<?php

/**
 * utiworkf.php : Utility de Clases del Modulo Logistica - workflow
 *
 * Este script contiene la colecciones de clases para el Modulo Logistica - workflow de openComex
 * Valida y Guarda el Pedido
 * 
 * @author Cristian Perdomo <cristian.perdomo@openits.co>
 * @author Elian Amado <elian.amado@openits.co>
 * @package opencomex
 * @version 1.0
 */
include("utimifxx.php");

class cTickets
{

  function fnCabeceraTickets($cTicket)
  {
    global $cAlfa;
    global $xConexion01;
    global $vSysStr;

    $cAnioAnt = $vSysStr['logistica_ano_instalacion_modulo'];
    $cAnio = $cAnioAnt;

    $ticketData = array();

    for ($cAnio; $cAnio <= date('Y'); $cAnio++) {

      $qMiTicket  = "SELECT ";
      $qMiTicket .= "$cAlfa.ltic$cAnio.ticidxxx, ";   // Id Ticket
      $qMiTicket .= "$cAlfa.ltic$cAnio.ceridxxx, ";   // Id certificacion
      $qMiTicket .= "$cAlfa.ltic$cAnio.comidxxx, ";   // Id del Comprobante
      $qMiTicket .= "$cAlfa.ltic$cAnio.comcodxx, ";   // Codigo del Comprobante
      $qMiTicket .= "$cAlfa.ltic$cAnio.comprexx, ";   // Prefijo
      $qMiTicket .= "$cAlfa.ltic$cAnio.comcscxx, ";   // Consecutivo Uno
      $qMiTicket .= "$cAlfa.ltic$cAnio.comcsc2x, ";   // Consecutivo Dos
      $qMiTicket .= "$cAlfa.ltic$cAnio.comfecxx, ";   // Fecha Comprobante
      $qMiTicket .= "$cAlfa.ltic$cAnio.cliidxxx, ";    // Id cliente
      $qMiTicket .= "$cAlfa.ltic$cAnio.tticodxx, ";   // Codigo Tipo Ticket
      $qMiTicket .= "$cAlfa.ltic$cAnio.pticodxx, ";   // Codigo Prioridad Ticket
      $qMiTicket .= "$cAlfa.ltic$cAnio.sticodxx, ";   // Codigo Status Ticket
      $qMiTicket .= "$cAlfa.ltic$cAnio.ticasuxx, ";   // Asunto
      $qMiTicket .= "$cAlfa.ltic$cAnio.ticcierx, ";   // Fecha de cierre
      $qMiTicket .= "$cAlfa.ltic$cAnio.regusrxx, ";   // Usuario que creo el registro
      $qMiTicket .= "$cAlfa.ltic$cAnio.regfcrex, ";   // Fecha de creación
      $qMiTicket .= "$cAlfa.ltic$cAnio.reghcrex, ";   // Hora de creación
      $qMiTicket .= "$cAlfa.ltic$cAnio.regfmodx, ";   // Fecha de modificación
      $qMiTicket .= "$cAlfa.ltic$cAnio.reghmodx, ";   // Hora de modificación
      $qMiTicket .= "$cAlfa.ltic$cAnio.regestxx, ";   // Estado
      $qMiTicket .= "$cAlfa.lpar0150.clinomxx, ";   // Razon social
      $qMiTicket .= "$cAlfa.lpar0158.ttidesxx, ";   // Descripcion Ticket
      $qMiTicket .= "$cAlfa.lpar0156.pticolxx, ";   // Color
      $qMiTicket .= "$cAlfa.lpar0156.ptidesxx, ";   // Proiridad descripcion
      $qMiTicket .= "$cAlfa.lpar0157.stidesxx, ";   // Status
      $qMiTicket .= "$cAlfa.SIAI0003.USRNOMXX AS usrnomxx, ";   // Creado por
      $qMiTicket .= "GROUP_CONCAT(SIAI0003_2.USRNOMXX SEPARATOR ', ') AS responsables, ";   // Responsables
      $qMiTicket .= "GROUP_CONCAT(SIAI0003_3.USREMAXX SEPARATOR ', ') AS emails ";   // Emails
      $qMiTicket .= "FROM $cAlfa.ltic$cAnio ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.ltic$cAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0158 ON $cAlfa.ltic$cAnio.tticodxx = $cAlfa.lpar0158.tticodxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0156 ON $cAlfa.ltic$cAnio.pticodxx = $cAlfa.lpar0156.pticodxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0157 ON $cAlfa.ltic$cAnio.sticodxx = $cAlfa.lpar0157.sticodxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.ltic$cAnio.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0159 ON $cAlfa.ltic$cAnio.tticodxx = $cAlfa.lpar0159.tticodxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.SIAI0003 AS SIAI0003_2 ON $cAlfa.lpar0159.ttiusrxx = SIAI0003_2.USRIDXXX ";
      $qMiTicket .= "LEFT JOIN $cAlfa.SIAI0003 AS SIAI0003_3 ON $cAlfa.lpar0159.ttiusrxx = SIAI0003_3.USRIDXXX ";
      $qMiTicket .= "WHERE ticidxxx = \"$cTicket\"";
      $xTickets  = f_MySql("SELECT", "", $qMiTicket, $xConexion01, "");

      if (mysql_num_rows($xTickets) > 0) {
        $ticketData = mysql_fetch_array($xTickets);
        break; 
      }
    }
    return $ticketData;
  }

  function fnDetalleTickets($cTicket)
  {
    global $cAlfa;
    global $xConexion01;
    global $vSysStr;

    $cAnioAnt = $vSysStr['logistica_ano_instalacion_modulo'];
    $cAnio = $cAnioAnt;

    for ($cAnio = $cAnioAnt; $cAnio <= date('Y'); $cAnio++) {
      $mMatrizTickets = array();
      $qTickets  = "SELECT * ";
      $qTickets .= "FROM $cAlfa.ltid$cAnio ";
      $qTickets .= "WHERE ticidxxx = \"$cTicket\" ";
      $xTickets = f_MySql("SELECT", "", $qTickets, $xConexion01, "");
      if (mysql_num_rows($xTickets) > 0) {
        while ($vTickets = mysql_fetch_array($xTickets)) {
          $nInd_mMatrizTickets = count($mMatrizTickets);
          $mMatrizTickets[$nInd_mMatrizTickets]['repidxxx'] = $vTickets['repidxxx'];
          $mMatrizTickets[$nInd_mMatrizTickets]['ticidxxx'] = $vTickets['ticidxxx'];
          $mMatrizTickets[$nInd_mMatrizTickets]['repcscxx'] = $vTickets['repcscxx'];
          $mMatrizTickets[$nInd_mMatrizTickets]['tticodxx'] = $vTickets['tticodxx'];
          $mMatrizTickets[$nInd_mMatrizTickets]['pticodxx'] = $vTickets['pticodxx'];
          $mMatrizTickets[$nInd_mMatrizTickets]['sticodxx'] = $vTickets['sticodxx'];
          $mMatrizTickets[$nInd_mMatrizTickets]['ticccopx'] = $vTickets['ticccopx'];
          $mMatrizTickets[$nInd_mMatrizTickets]['repreply'] = $vTickets['repreply'];
          $mMatrizTickets[$nInd_mMatrizTickets]['reprepor'] = $vTickets['reprepor'];
          $mMatrizTickets[$nInd_mMatrizTickets]['regusrxx'] = $vTickets['regusrxx'];
          $mMatrizTickets[$nInd_mMatrizTickets]['regusrem'] = $vTickets['regusrem'];
          $mMatrizTickets[$nInd_mMatrizTickets]['regfcrex'] = $vTickets['regfcrex'];
          $mMatrizTickets[$nInd_mMatrizTickets]['reghcrex'] = $vTickets['reghcrex'];
          $mMatrizTickets[$nInd_mMatrizTickets]['regfmodx'] = $vTickets['regfmodx'];
          $mMatrizTickets[$nInd_mMatrizTickets]['reghmodx'] = $vTickets['reghmodx'];
          $mMatrizTickets[$nInd_mMatrizTickets]['regestxx'] = $vTickets['regestxx'];
          $mMatrizTickets[$nInd_mMatrizTickets]['regstamp'] = $vTickets['regstamp'];
        }
        break;
      }
    }
    return $mMatrizTickets;
  }
} #FIN class cTickets

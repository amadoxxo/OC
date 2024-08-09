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
include('../../../libs/php/uticemax.php');

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
      $qMiTicket .= "$cAlfa.ltic$cAnio.ticidxxx, ";  // Id Ticket
      $qMiTicket .= "$cAlfa.ltic$cAnio.ceridxxx, ";  // Id certificacion
      $qMiTicket .= "$cAlfa.ltic$cAnio.comidxxx, ";  // Id del Comprobante
      $qMiTicket .= "$cAlfa.ltic$cAnio.comcodxx, ";  // Codigo del Comprobante
      $qMiTicket .= "$cAlfa.ltic$cAnio.comprexx, ";  // Prefijo
      $qMiTicket .= "$cAlfa.ltic$cAnio.comcscxx, ";  // Consecutivo Uno
      $qMiTicket .= "$cAlfa.ltic$cAnio.comcsc2x, ";  // Consecutivo Dos
      $qMiTicket .= "$cAlfa.ltic$cAnio.comfecxx, ";  // Fecha Comprobante
      $qMiTicket .= "$cAlfa.ltic$cAnio.cliidxxx, ";  // Id cliente
      $qMiTicket .= "$cAlfa.ltic$cAnio.tticodxx, ";  // Codigo Tipo Ticket
      $qMiTicket .= "$cAlfa.ltic$cAnio.pticodxx, ";  // Codigo Prioridad Ticket
      $qMiTicket .= "$cAlfa.ltic$cAnio.sticodxx, ";  // Codigo Status Ticket
      $qMiTicket .= "$cAlfa.ltic$cAnio.ticasuxx, ";  // Asunto
      $qMiTicket .= "$cAlfa.ltic$cAnio.ticcierx, ";  // Fecha de cierre
      $qMiTicket .= "$cAlfa.ltic$cAnio.regusrxx, ";  // Usuario que creo el registro
      $qMiTicket .= "$cAlfa.ltic$cAnio.regfcrex, ";  // Fecha de creación
      $qMiTicket .= "$cAlfa.ltic$cAnio.reghcrex, ";  // Hora de creación
      $qMiTicket .= "$cAlfa.ltic$cAnio.regfmodx, ";  // Fecha de modificación
      $qMiTicket .= "$cAlfa.ltic$cAnio.reghmodx, ";  // Hora de modificación
      $qMiTicket .= "$cAlfa.ltic$cAnio.regestxx, ";  // Estado
      $qMiTicket .= "$cAlfa.lpar0150.clinomxx, ";  // Razon social
      $qMiTicket .= "$cAlfa.lpar0158.ttidesxx, ";  // Descripcion Ticket
      $qMiTicket .= "$cAlfa.lpar0156.pticolxx, ";  // Color
      $qMiTicket .= "$cAlfa.lpar0156.ptidesxx, ";  // Prioridad descripcion
      $qMiTicket .= "$cAlfa.lpar0157.stidesxx, ";  // Status
      $qMiTicket .= "$cAlfa.SIAI0003.USRNOMXX AS usrnomxx, ";  // Creado por
      $qMiTicket .= "$cAlfa.SIAI0003.USREMAXX AS emailcre, ";  // Creado por
      $qMiTicket .= "GROUP_CONCAT(SIAI0003_2.USRNOMXX SEPARATOR ', ') AS responsables, ";  // Responsables
      $qMiTicket .= "GROUP_CONCAT(SIAI0003_3.USREMAXX SEPARATOR ', ') AS emails ";  // Emails
      $qMiTicket .= "FROM $cAlfa.ltic$cAnio ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.ltic$cAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0158 ON $cAlfa.ltic$cAnio.tticodxx = $cAlfa.lpar0158.tticodxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0156 ON $cAlfa.ltic$cAnio.pticodxx = $cAlfa.lpar0156.pticodxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0157 ON $cAlfa.ltic$cAnio.sticodxx = $cAlfa.lpar0157.sticodxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.ltic$cAnio.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0159 ON $cAlfa.ltic$cAnio.tticodxx = $cAlfa.lpar0159.tticodxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.SIAI0003 AS SIAI0003_2 ON $cAlfa.lpar0159.ttiusrxx = SIAI0003_2.USRIDXXX ";
      $qMiTicket .= "LEFT JOIN $cAlfa.SIAI0003 AS SIAI0003_3 ON $cAlfa.lpar0159.ttiusrxx = SIAI0003_3.USRIDXXX ";
      $qMiTicket .= "WHERE ticidxxx = \"$cTicket\" OR ";
      $qMiTicket .= "ceridxxx = \"$cTicket\"; ";
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

    $mMatrizTickets = array();

    for ($cAnio; $cAnio <= date('Y'); $cAnio++) {
      $qTickets = "SELECT ";
      $qTickets .= "$cAlfa.ltid$cAnio.repidxxx, ";
      $qTickets .= "$cAlfa.ltid$cAnio.ticidxxx, ";
      $qTickets .= "$cAlfa.ltid$cAnio.repcscxx, ";
      $qTickets .= "$cAlfa.ltid$cAnio.tticodxx, ";
      $qTickets .= "$cAlfa.ltid$cAnio.pticodxx, ";
      $qTickets .= "$cAlfa.ltid$cAnio.sticodxx, ";
      $qTickets .= "$cAlfa.ltid$cAnio.ticccopx, ";
      $qTickets .= "$cAlfa.ltid$cAnio.repreply, ";
      $qTickets .= "$cAlfa.ltid$cAnio.reprepor, ";
      $qTickets .= "$cAlfa.ltid$cAnio.regusrxx, ";
      $qTickets .= "$cAlfa.ltid$cAnio.regusrem, ";
      $qTickets .= "$cAlfa.ltid$cAnio.regfcrex, ";
      $qTickets .= "$cAlfa.ltid$cAnio.reghcrex, ";
      $qTickets .= "$cAlfa.ltid$cAnio.regfmodx, ";
      $qTickets .= "$cAlfa.ltid$cAnio.reghmodx, ";
      $qTickets .= "$cAlfa.ltid$cAnio.regestxx, ";
      $qTickets .= "$cAlfa.ltid$cAnio.regstamp, ";
      $qTickets .= "$cAlfa.SIAI0003.USRNOMXX AS usrnomxx ";
      $qTickets .= "FROM $cAlfa.ltid$cAnio ";
      $qTickets .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.ltid$cAnio.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
      $qTickets .= "WHERE $cAlfa.ltid$cAnio.ticidxxx = $cTicket ";
      $qTickets .= "ORDER BY $cAlfa.ltid$cAnio.repidxxx ASC; ";
      $xTickets = f_MySql("SELECT", "", $qTickets, $xConexion01, "");

      if (mysql_num_rows($xTickets) > 0) {
        while ($vTickets = mysql_fetch_assoc($xTickets)) {
          $mMatrizTickets[] = $vTickets;
        }
      }
    }

    return $mMatrizTickets;
  }

  function fnEnvioEmail($nSwitch, $cMsj, $cId)
  {
    global $cAlfa;
    $datosCabecera = $this->fnCabeceraTickets($cId);

    $cTiCcErx = ($datosCabecera['ticcierx'] != "0000-00-00") ? $datosCabecera['ticcierx'] : "";
    // Obtener valores dinámicos de cEmaUsr
    $i = 0;
    $ticketEnviado = [];
    while (isset($_POST["cEmaUsr$i"])) {
      $ticketEnviado[] = $_POST["cEmaUsr$i"];
      $i++;
    }
    
    if (count($ticketEnviado) > 0) {
      $cSubject = "Solicitud: 1 / {$datosCabecera['ttidesxx']} / {$datosCabecera['clinomxx']} / {$datosCabecera['stidesxx']} / {$datosCabecera['comprexx']}{$datosCabecera['comcscxx']} ";
      
      $cMessage  = "<body style='margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 14px; color: #333;'>";
      $cMessage .= "<table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color: #f9f9f9;'>";
      $cMessage .= "<tr>";
      $cMessage .= "<td align='center'>";
      $cMessage .= "<table width='600' border='0' cellspacing='0' cellpadding='10' style='margin-top: 20px; margin-bottom: 20px; background-color: #ffffff;'>";
      $cMessage .= "<tr style='background-color: #e6e6e6;'>";
      $cMessage .= "<td style='text-align: left; font-size: 16px; padding: 10px;'><strong>Ticket: </strong>{$_POST['cTicket']}</td>";
      $cMessage .= "</tr>";
      $cMessage .= "<tr>";
      $cMessage .= "<td style='font-weight: bold;'>Asunto: {$_POST['cAsuTck']}</td>";
      $cMessage .= "</tr>";
      $cMessage .= "<tr>";
      $cMessage .= "<td>";
      $cMessage .= "<table width='100%' border='0' cellspacing='0' cellpadding='5' style='font-size: 14px;'>";
      $cMessage .= "<tr>";
      $cMessage .= "<td style='font-weight: bold;'>POST ID:</td>";
      $cMessage .= "<td>{$datosCabecera[0]['repcscxx']}</td>";
      $cMessage .= "<td style='font-weight: bold;'>Cliente:</td>";
      $cMessage .= "<td>{$_POST['cCliNom']}</td>";
      $cMessage .= "</tr>";
      $cMessage .= "<tr>";
      $cMessage .= "<td style='font-weight: bold;'>Prioridad:</td>";
      $cMessage .= "<td>{$datosCabecera['ptidesxx']}</td>";
      $cMessage .= "<td style='font-weight: bold;'>Estado:</td>";
      $cMessage .= "<td>{$datosCabecera['stidesxx']}</td>";
      $cMessage .= "</tr>";
      $cMessage .= "<tr>";
      $cMessage .= "<td style='font-weight: bold;'>Apertura Ticket:</td>";
      $cMessage .= "<td>{$datosCabecera['regfcrex']}</td>";
      $cMessage .= "<td style='font-weight: bold;'>Cierre Ticket:</td>";
      $cMessage .= "<td>{$cTiCcErx}</td>";
      $cMessage .= "</tr>";
      $cMessage .= "<tr>";
      $cMessage .= "<td style='font-weight: bold;'>Tipo de Ticket:</td>";
      $cMessage .= "<td>{$datosCabecera['ttidesxx']}</td>";
      $cMessage .= "<td style='font-weight: bold;'>Ticket enviado a:</td>";
      $cMessage .= "<td>".implode(', ', $ticketEnviado)."</td>";
      $cMessage .= "</tr>";
      $cMessage .= "<tr>";
      $cMessage .= "<td style='font-weight: bold;'>Ticket CC a:</td>";
      $cMessage .= "<td>{$_POST['cCliPCECn']}</td>";
      $cMessage .= "<td style='font-weight: bold;'>Certificacion:</td>";
      $cMessage .= "<td>{$datosCabecera['comprexx']}{$datosCabecera['comcscxx']}</td>";
      $cMessage .= "</tr>";
      $cMessage .= "</table>";
      $cMessage .= "</td>";
      $cMessage .= "</tr>";
      $cMessage .= "<tr>";
      $cMessage .= "<td style='text-align: left; font-size: 14px; padding: 20px; background-color: #ffffff;'>Buen dia,<br><br>{$_POST['cConten']}</td>";
      $cMessage .= "</tr>";
      $cMessage .= "</table>";
      $cMessage .= "</td>";
      $cMessage .= "</tr>";
      $cMessage .= "</table>";
      $cMessage .= "</body>";

      if ($nSwitch == 0) {
        // Send
        $vDatos['basedato'] = $cAlfa;
        $vDatos['asuntoxx'] = $cSubject;
        $vDatos['mensajex'] = $cMessage;
        $vDatos['adjuntos'] = [];
        $vDatos['replytox'] = [$_POST['cUsrEma']]; // un array con el correo del usuario que creó el ticket
        
        $ObjEnvioEmail = new cEnvioEmail();
        
        $cCorreos = "";
        for ($nC=0;$nC<count($ticketEnviado);$nC++) { 
          if ($ticketEnviado[$nC] != "") {
            $vDatos['destinos'] = [$ticketEnviado[$nC]]; // Array con los correos de destino
            // Enviando correos a los contactos que se notifica
            $vReturn = $ObjEnvioEmail->fnEviarEmailSMTP($vDatos);
            if ($vReturn[0] == "false") {
              $cMsjError = "";
              for ($nR=1;$nR<count($vReturn);$nR++) { 
                $cMsjError .= $vReturn[$nR]."\n"; 
              }
              $nSwitch = 1;
              return $cMsj .= "\nError al Enviar Correo al destinatario [{$ticketEnviado[$nC]}].\n".$cMsjError."\n";
            }
            $cCorreos .= "{$ticketEnviado[$nC]}, ";
          }
        }
        $cCorreos = substr($cCorreos, 0, strlen($cCorreos)-2);
      }
      
      if ($nSwitch == 0) {
        return $cMsj .= "Se Envio el ticket con Exito a los Siguientes Correos:\n$cCorreos.\n";
      }
    }
  }
} #FIN class cTickets

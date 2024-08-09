<?php

/**
 * Graba Orgainzacion de Ventas.
 * --- Descripcion: Permite Guardar una Nuvo Status Ticket.
 * @author cristian.perdomo@openits.co
 * @package openComex
 * @version 001
 */
include("../../../../../financiero/libs/php/utility.php");
include("../../../../../logistica/libs/php/utiworkf.php");
include("../../../../../libs/php/uticemax.php");

//cargo data del ticket en el utiworkf.php
$ticket = new cTickets();

$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
$cMsj = "";
global $cAlfa;
global $xConexion01;
global $vSysStr;

switch ($_COOKIE['kModo']) {
  case "NUEVO":
  case "EDITAR":
    // Validando que el Prefijo no sea vacio.
    if ($_POST['cComPre'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "Prefijo no puede ser vacio.\n";
    } else {
      // Validando que el prefijo exista.
      if ($_COOKIE['kModo'] == "NUEVOTICKET") {
        $qComprobante  = "SELECT ";
        $qComprobante .= "comidxxx ";
        $qComprobante .= "FROM $cAlfa.lpar0117 ";
        $qComprobante .= "WHERE ";
        $qComprobante .= "$cAlfa.lpar0117.comidxxx = \"{$_POST['cComId']}\" AND ";
        $qComprobante .= "$cAlfa.lpar0117.comprexx = \"{$_POST['cComPre']}\" AND ";
        $qComprobante .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
        $xComprobante  = f_MySql("SELECT", "", $qComprobante, $xConexion01, "");
        if (mysql_num_rows($xComprobante) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
          $cMsj .= "El Prefijo [{$_POST['cComPre']}] no existe.\n";
        }
      }
    }

    // Validando el Consecutivo
    if ($_COOKIE['kModo'] == "NUEVOTICKET") {
      if ($_POST['cComCsc'] != "") {
        if (!preg_match('/^[0-9]+$/', $_POST['cComCsc'])) {
          $nSwitch = 1;
          $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
          $cMsj .= "El Consecutivo [{$_POST['cComCsc']}] debe ser numerico.\n";
        }
      } elseif ($_POST['cComCsc'] == "" && $_POST['cComTCo'] == "MANUAL") {
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "El Consecutivo no puede ser vacio.\n";
      }
    }

    // Validando que el Nit no sea vacio.
    if ($_POST['cCliId'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El Nit no puede ser vacio.\n";
    } else {
      // Validando que el Nit exista.
      if ($_COOKIE['kModo'] == "NUEVOTICKET") {
        $qCliDat  = "SELECT * ";
        $qCliDat .= "FROM $cAlfa.lpar0150 ";
        $qCliDat .= "WHERE ";
        $qCliDat .= "$cAlfa.lpar0150.cliidxxx = \"{$_POST['cCliId']}\" AND ";
        $qCliDat .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
        $xCliDat  = f_MySql("SELECT", "", $qCliDat, $xConexion01, "");
        if (mysql_num_rows($xCliDat) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
          $cMsj .= "El Nit del Cliente [{$_POST['cCliId']}] no existe.\n";
        }
      }
    }

    // Validando Fechas
    // Validando que la Fecha Desde no sea vacia.
    if ($_POST['cComFec'] == "" || $_POST['cComFec'] == "0000-00-00") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "Se debe seleccionar una Fecha.\n";
    }

    // Valida que la observacion no sea vacia
    if ($_POST['cAsuTck'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El asunto no puede ser vacio.\n";
    }

    // Valida que el Tipo Ticket no sea vacio
    if ($_POST['cTtiCod'] == "" || $_POST['cTtiDes'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El tipo no puede ser vacio.\n";
    } else {
      // Validando que el Tipo Ticket exista
      if ($_COOKIE['kModo'] == "NUEVOTICKET") {
        $qCliDat  = "SELECT tticodxx, ttidesxx ";
        $qCliDat .= "FROM $cAlfa.lpar0158 ";
        $qCliDat .= "WHERE ";
        $qCliDat .= "$cAlfa.lpar0158.tticodxx = \"{$_POST['cTtiCod']}\" AND ";
        $qCliDat .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
        $xCliDat  = f_MySql("SELECT", "", $qCliDat, $xConexion01, "");
        if (mysql_num_rows($xCliDat) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
          $cMsj .= "El Tipo Ticket [{$_POST['cTtiCod']}] no existe.\n";
        }
      }
    }

    // Valida que la prioridad no sea vacia
    if ($_POST['cPriori'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "La prioridad no puede ser vacia.\n";
    } else {
      // Validando que el Tipo Ticket exista
      if ($_COOKIE['kModo'] == "NUEVOTICKET") {
        $qCliDat  = "SELECT pticodxx, ptidesxx ";
        $qCliDat .= "FROM $cAlfa.lpar0156 ";
        $qCliDat .= "WHERE ";
        $qCliDat .= "$cAlfa.lpar0156.pticodxx = \"{$_POST['cPriori']}\" AND ";
        $qCliDat .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
        $xCliDat  = f_MySql("SELECT", "", $qCliDat, $xConexion01, "");
        if (mysql_num_rows($xCliDat) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
          $cMsj .= "El Tipo Ticket {$_POST['cPriori']} no existe.\n";
        }
      }
    }

    // Valida que el estado no sea vacio
    if ($_POST['cEstado'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El estado no puede ser vacio.\n";
    } else {
      // Validando que el Tipo Ticket exista
      if ($_COOKIE['kModo'] == "NUEVOTICKET") {
        $qCliDat  = "SELECT sticodxx, stidesxx ";
        $qCliDat .= "FROM $cAlfa.lpar0157 ";
        $qCliDat .= "WHERE ";
        $qCliDat .= "$cAlfa.lpar0157.sticodxx = \"{$_POST['cEstado']}\" AND ";
        $qCliDat .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
        $xCliDat  = f_MySql("SELECT", "", $qCliDat, $xConexion01, "");
        if (mysql_num_rows($xCliDat) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
          $cMsj .= "El Tipo Ticket [{$_POST['cEstado']}] no existe.\n";
        }
      }
    }

    // Valida que el email no sea vacio
    if (trim($_POST['cCliPCECn']) != "") {
      $vCorreos = explode(",", $_POST['cCliPCECn']);
      for ($i = 0; $i < count($vCorreos); $i++) {
        $vCorreos[$i] = trim($vCorreos[$i]);
        if ($vCorreos[$i] != "") {
          if (!filter_var($vCorreos[$i], FILTER_VALIDATE_EMAIL)) {
            $nSwitch = 1;
            $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
            $cMsj .= " El Correo [" . $vCorreos[$i] . "], No es Valido.\n";
          }
        }
      }
    }

    // Valida que el contenido no sea vacio
    if ($_POST['cConten'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El contenido no puede ser vacio.\n";
    }

    // Obtener valores dinámicos de cEmaUsr
    /*  $i = 0;
    $ticketEnviado = [];
    while (isset($_POST["cEmaUsr$i"])) {
      $ticketEnviado[] = $_POST["cEmaUsr$i"];
      $i++;
    } */

    /* correos */
    $cTtiCod = $_POST['cTtiCod'];
    $reprepor = "TERCERO"; // Valor predeterminado

    // Recupera el valor de la cookie
    $cookieUsrId = isset($_COOKIE['kUsrId']) ? $_COOKIE['kUsrId'] : "";
    $mMatrizRes = array();

    $qResTi  = "SELECT ";
    $qResTi .= "tticodxx, ";
    $qResTi .= "ttiusrxx ";
    $qResTi .= "FROM $cAlfa.lpar0159 ";
    $qResTi .= "WHERE ";
    $qResTi .= "tticodxx = \"$cTtiCod\" AND ";
    $qResTi .= "regestxx = \"ACTIVO\" ";
    $xResTi = f_MySql("SELECT", "", $qResTi, $xConexion01, "");
    if (mysql_num_rows($xResTi) > 0) {
      while ($vResTi = mysql_fetch_array($xResTi)) {
        $nInd_mMatrizRes = count($mMatrizRes);
        $mMatrizRes[$nInd_mMatrizRes]['tticodxx'] = $vResTi['tticodxx'];
        $mMatrizRes[$nInd_mMatrizRes]['ttiusrxx'] = $vResTi['ttiusrxx'];

        if ($vResTi['ttiusrxx'] === $cookieUsrId) {
          $reprepor = "RESPONSABLE";
        }
      }
    }
/* 
    $ticketEnviado = [];
    for ($i = 0; $i < count($mMatrizRes); $i++) {
      $mMatrizUsr = array();

      $qUser  = "SELECT USRNOMXX, USREMAXX, USRIDXXX ";
      $qUser .= "FROM $cAlfa.SIAI0003 ";
      $qUser .= "WHERE ";
      $qUser .= "USRIDXXX = \"" . $mMatrizRes[$i]['ttiusrxx'] . "\" AND ";
      $qUser .= "REGESTXX = \"ACTIVO\"; ";
      $xUser  = f_MySql("SELECT", "", $qUser, $xConexion01, "");
      if (mysql_num_rows($xUser) > 0) {
        $mMatrizUsr = mysql_fetch_assoc($xUser);
      }

      $ticketEnviado[] = $mMatrizUsr['USREMAXX'];
    } */
    /* end */


    /* $ticket = new cTickets();
    $datosCabecera = $ticket->fnCabeceraTickets($_POST['cTicket']);

    if (count($ticketEnviado) > 0) {
      $cSubject = "Solicitud: / {$datosCabecera['ttidesxx']} / {$datosCabecera['clinomxx']} / {$datosCabecera['stidesxx']} / {$datosCabecera['comprexx']}{$datosCabecera['comcscxx']}";

      $cMessage = "<body style='font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4;'>";
      $cMessage .= "<div style='background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>";
      $cMessage .= "<h2 style='background-color: #f2f2f2; padding: 15px; border-radius: 5px; text-align: center; margin-top: 0;'>Ticket: 1</h2>";
      
      $sections = [
          "Asunto" => $_POST['cAsuTck'],
          "Prioridad" => $datosCabecera['ptidesxx'],
          "Estado" => $datosCabecera['stidesxx'],
          "Apertura Ticket" => $datosCabecera['regfcrex'],
          "Cierre Ticket" => $cTiCcErx,
          "Tipo de Ticket" => $datosCabecera['ttidesxx'],
          "Ticket enviado a" => implode(', ', $ticketEnviado),
          "Ticket CC a" => $_POST['cCliPCECn'],
          "Certificacion" => "{$datosCabecera['comprexx']}{$datosCabecera['comcscxx']}",
          "Cliente" => $datosCabecera['clinomxx'],
          "Contenido" => $_POST['cConten']
      ];
      
      foreach ($sections as $title => $value) {
          if ($title === "Contenido") {
              $cMessage .= "<div style='margin-bottom: 20px;'>";
              $cMessage .= "<strong style='display: block; padding: 8px; background-color: #f9f9f9;'>{$title}:</strong>";
              $cMessage .= "<p style='margin: 0; padding: 8px;'>{$value}</p>";
              $cMessage .= "</div>";
          } else {
              $cMessage .= "<div style='margin-bottom: 10px;'>";
              $cMessage .= "<div style='display: flex; align-items: center;'>";
              $cMessage .= "<strong style='display: block; width: 200px; padding: 8px; background-color: #f9f9f9;'>{$title}:</strong>";
              $cMessage .= "<p style='margin: 0; padding: 8px;'>{$value}</p>";
              $cMessage .= "</div>";
              $cMessage .= "</div>";
          }
      }
      
      $cMessage .= "</div>";
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
        for ($nC = 0; $nC < count($ticketEnviado); $nC++) {
          if ($ticketEnviado[$nC] != "") {
            $vDatos['destinos'] = [$ticketEnviado[$nC]]; // Array con los correos de destino
            // Enviando correos a los contactos que se notifica
            $vReturn = $ObjEnvioEmail->fnEviarEmailSMTP($vDatos);
            if ($vReturn[0] == "false") {
              $cMsjError = "";
              for ($nR = 1; $nR < count($vReturn); $nR++) {
                $cMsjError .= $vReturn[$nR] . "\n";
              }
              $nSwitch = 1;
              $cMsj .= "\nError al Enviar Correo al destinatario [{$ticketEnviado[$nC]}].\n" . $cMsjError . "\n";
            }
            $cCorreos .= "{$ticketEnviado[$nC]}, ";
          }
        }
        $cCorreos = substr($cCorreos, 0, strlen($cCorreos) - 2);
      }

      if ($nSwitch == 0) {
        $cMsj .= "Se Envio el ticket con Exito a los Siguientes Correos:\n$cCorreos.\n";
      }
    } */
    break;
}
/***** Fin de la Validacion *****/

/***** Ahora Empiezo a Grabar *****/
/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "EDITAR":
      /***** Fin de Validaciones Particulares *****/
      // Actualiza la observacion de cabecera
      $cAnioAnt = $vSysStr['logistica_ano_instalacion_modulo'];
      $cAnio = $cAnioAnt;

      for ($cAnio; $cAnio <= date('Y'); $cAnio++) {
        $qTickets = "SELECT * ";
        $qTickets .= "FROM $cAlfa.ltic$cAnio ";
        $qTickets .= "WHERE $cAlfa.ltic$cAnio.ticidxxx = " . $_POST['cTicket'];

        $xTickets = f_MySql("SELECT", "", $qTickets, $xConexion01, "");

        if (mysql_num_rows($xTickets) > 0) {
          break;
        }
      }

      $qUpdate = array(
        array('NAME' => 'tticodxx', 'VALUE' => trim($_POST['cTtiCod']), 'CHECK' => 'NO'),
        array('NAME' => 'pticodxx', 'VALUE' => trim($_POST['cPriori']), 'CHECK' => 'NO'),
        array('NAME' => 'sticodxx', 'VALUE' => trim($_POST['cEstado']), 'CHECK' => 'NO'),
        array('NAME' => 'ticidxxx', 'VALUE' => trim($_POST['cTicket']), 'CHECK' => 'WH')
      );

      if (!f_MySql("UPDATE", "ltic$cAnio", $qUpdate, $xConexion01, $cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "Error actualizando la cabecera del Ticket.\n";
      }

      $qReply  = "SELECT ";
      $qReply .= "$cAlfa.ltid$cAnio.* ";
      $qReply .= "FROM $cAlfa.ltid$cAnio ";
      $qReply .= "WHERE ";
      $qReply .= "$cAlfa.ltid$cAnio.ticidxxx = \"{$_POST['cTicket']}\" ";
      $xReply  = f_MySql("SELECT", "", $qReply, $xConexion01, "");
      if (mysql_num_rows($xReply) > 0) {
        $qInsert = array(
          array('NAME' => 'tticodxx', 'VALUE' => trim($_POST['cTtiCod']), 'CHECK' => 'SI'),
          array('NAME' => 'repcscxx', 'VALUE' => trim(mysql_num_rows($xReply) + 1), 'CHECK' => 'SI'),
          array('NAME' => 'pticodxx', 'VALUE' => trim($_POST['cPriori']), 'CHECK' => 'SI'),
          array('NAME' => 'sticodxx', 'VALUE' => trim($_POST['cEstado']), 'CHECK' => 'SI'),
          array('NAME' => 'ticccopx', 'VALUE' => trim($_POST['cCliPCECn']), 'CHECK' => 'SI'),
          array('NAME' => 'repreply', 'VALUE' => trim($_POST['cConten']), 'CHECK' => 'SI'),
          array('NAME' => 'reprepor', 'VALUE' => trim($reprepor), 'CHECK' => 'SI'),
          array('NAME' => 'regusrxx', 'VALUE' => trim(strtoupper($_COOKIE['kUsrId'])), 'CHECK' => 'SI'),  //Usuario que creo el registro
          array('NAME' => 'regusrem', 'VALUE' => trim($_POST['cUsrEma']), 'CHECK' => 'SI'),  //Usuario que creo el registro
          array('NAME' => 'regfcrex', 'VALUE' => date('Y-m-d'), 'CHECK' => 'SI'),  //Fecha de creacion
          array('NAME' => 'reghcrex', 'VALUE' => date('H:i:s'), 'CHECK' => 'SI'),  //Hora de creacion 
          array('NAME' => 'regfmodx', 'VALUE' => date('Y-m-d'), 'CHECK' => 'SI'),  //Fecha de modificacion
          array('NAME' => 'reghmodx', 'VALUE' => date('H:i:s'), 'CHECK' => 'SI'),  //Hora de modificacion
          array('NAME' => 'regestxx', 'VALUE' => trim('ACTIVO'), 'CHECK' => 'SI'),  //Hora de modificacion
          array('NAME' => 'regstamp', 'VALUE' => date('Y-m-d H:m:s'), 'CHECK' => 'SI'),  //Hora de modificacion
          array('NAME' => 'ticidxxx', 'VALUE' => trim($_POST['cTicket']), 'CHECK' => 'WH')
        );

        if (!f_MySql("INSERT", "ltid$cAnio", $qInsert, $xConexion01, $cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
          $cMsj .= "Error al crear el reply.\n";
        }

        $ticket->fnEnvioEmail($nSwitch, $cMsj);
      }
      break;
  }
}

if ($nSwitch == 0) {
  if ($_COOKIE['kModo'] == "EDITAR") {
    f_Mensaje(__FILE__, __LINE__, "El Registro se Cargo Con Exito");
  }
?>
  <form name="frgrm" action="<?php echo $_COOKIE['kIniAnt'] ?>" method="post" target="fmwork"></form>
  <script language="javascript">
    window.close();
  </script>
<?php }

if ($nSwitch == 1) {
  f_Mensaje(__FILE__, __LINE__, $cMsj . "Verifique.\n");
}
?>
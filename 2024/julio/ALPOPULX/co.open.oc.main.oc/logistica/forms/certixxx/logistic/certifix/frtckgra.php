<?php
  /**
   * Graba Certificacion.
   * --- Descripcion: Permite Guardar en la tabla Tickets.
   * @author Elian Amado. <elian.amado@openits.co>
   * @package opencomex
   * @version 001
   */
  include('../../../../../financiero/libs/php/utility.php');
  include('../../../../../logistica/libs/php/utiworkf.php');
  include('../../../../../libs/php/uticemax.php');

  /**
   * Switch para Vericar la Validacion de Datos.
   * 
   * @var int
   */
  $nSwitch = 0;

  /**
   * Almacena los errores generados en el proceso.
   * 
   * @var string
   */
  $cMsj = "\n";
  /**
   * Año actual del sistema.
   * 
   * @var string
   */
  $cPerAno = date('Y');
  

  // Creación de tabla anualizada en caso de que no exista
  $qTabExis = "SHOW TABLES FROM $cAlfa LIKE \"ltic$cPerAno\"";
  $xTabExis = f_MySql("SELECT","",$qTabExis,$xConexion01,"");
  if(mysql_num_rows($xTabExis) == 0){
    $cAnoCrea = $vSysStr['logistica_ano_instalacion_modulo'];

    $qCreate  = "CREATE TABLE IF NOT EXISTS $cAlfa.ltic$cPerAno LIKE $cAlfa.ltic$cAnoCrea ";
    $xCreate = mysql_query($qCreate,$xConexion01);
    if(!$xCreate) {
      $nSwitch   = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Error al crear Tabla Anualizada [ltic$cPerAno].\n".mysql_error($xConexion01);
    }else {
      $qTabExis = "SHOW TABLES FROM $cAlfa LIKE \"ltid$cPerAno\"";
      $xTabExis  = f_MySql("SELECT","",$qTabExis,$xConexion01,"");
      if( mysql_num_rows($xTabExis) == 0 ){
        $qCreate  = "CREATE TABLE IF NOT EXISTS $cAlfa.ltid$cPerAno LIKE $cAlfa.ltid$cAnoCrea ";
        $xCreate = mysql_query($qCreate,$xConexion01);
        //f_Mensaje(__FILE__,__LINE__,$qTabExis);    
        if(!$xCreate) {
          $nSwitch   = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al crear Tabla Anualizada [ltid$cPerAno].\n";
        }
      }
    }
  }

  $cPerAno = ($_COOKIE['kModo'] == "NUEVOTICKET") ? $cPerAno : $_POST['cAnio'];
  // Inicio de la Validacion
  switch ($_COOKIE['kModo']) {
    case "NUEVOTICKET":
    case "EDITAR":
      // Validando que el Prefijo no sea vacio.
      if ($_POST['cComPre'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
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
          $xComprobante  = f_MySql("SELECT","",$qComprobante,$xConexion01,"");
          if(mysql_num_rows($xComprobante) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Prefijo [{$_POST['cComPre']}] no existe.\n";
          }
        }
      }

      // Validando el Consecutivo
      if ($_COOKIE['kModo'] == "NUEVOTICKET") {
        if ($_POST['cComCsc'] != "") {
          if (!preg_match('/^[0-9]+$/', $_POST['cComCsc'])) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Consecutivo [{$_POST['cComCsc']}] debe ser numerico.\n";
          }
        } elseif ($_POST['cComCsc'] == "" && $_POST['cComTCo'] == "MANUAL") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Consecutivo no puede ser vacio.\n";
        }
      }

      // Validando que el Nit no sea vacio.
      if ($_POST['cCliId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Nit no puede ser vacio.\n";
      } else {
        // Validando que el Nit exista.
        if ($_COOKIE['kModo'] == "NUEVOTICKET") {
          $qCliDat  = "SELECT * ";
          $qCliDat .= "FROM $cAlfa.lpar0150 ";
          $qCliDat .= "WHERE ";
          $qCliDat .= "$cAlfa.lpar0150.cliidxxx = \"{$_POST['cCliId']}\" AND ";
          $qCliDat .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
          $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
          if(mysql_num_rows($xCliDat) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Nit del Cliente [{$_POST['cCliId']}] no existe.\n";
          }
        }
      }

        // Validando Fechas
        // Validando que la Fecha Desde no sea vacia.
        if ($_POST['cComFec'] == "" || $_POST['cComFec'] == "0000-00-00") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Se debe seleccionar una Fecha.\n";
        }

      // Valida que la observacion no sea vacia
      if ($_POST['cAsuTck'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El asunto no puede ser vacio.\n";
      }

      // Valida que el Tipo Ticket no sea vacio
      if ($_POST['cTtiCod'] == "" || $_POST['cTtiDes'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El tipo no puede ser vacio.\n";
      } else {
        // Validando que el Tipo Ticket exista
        if ($_COOKIE['kModo'] == "NUEVOTICKET") {
          $qCliDat  = "SELECT tticodxx, ttidesxx ";
          $qCliDat .= "FROM $cAlfa.lpar0158 ";
          $qCliDat .= "WHERE ";
          $qCliDat .= "$cAlfa.lpar0158.tticodxx = \"{$_POST['cTtiCod']}\" AND ";
          $qCliDat .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
          $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
          if(mysql_num_rows($xCliDat) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Tipo Ticket [{$_POST['cTtiCod']}] no existe.\n";
          }
        }
      }

      // Valida que la prioridad no sea vacia
      if ($_POST['cPriori'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La prioridad no puede ser vacia.\n";
      } else {
        // Validando que el Tipo Ticket exista
        if ($_COOKIE['kModo'] == "NUEVOTICKET") {
          $qCliDat  = "SELECT pticodxx, ptidesxx ";
          $qCliDat .= "FROM $cAlfa.lpar0156 ";
          $qCliDat .= "WHERE ";
          $qCliDat .= "$cAlfa.lpar0156.pticodxx = \"{$_POST['cPriori']}\" AND ";
          $qCliDat .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
          $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
          if(mysql_num_rows($xCliDat) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Tipo Ticket {$_POST['cPriori']} no existe.\n";
          }
        }
      }
    
      // Valida que el estado no sea vacio
      if ($_POST['cEstado'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El estado no puede ser vacio.\n";
      } else {
        // Validando que el Tipo Ticket exista
        if ($_COOKIE['kModo'] == "NUEVOTICKET") {
          $qCliDat  = "SELECT sticodxx, stidesxx ";
          $qCliDat .= "FROM $cAlfa.lpar0157 ";
          $qCliDat .= "WHERE ";
          $qCliDat .= "$cAlfa.lpar0157.sticodxx = \"{$_POST['cEstado']}\" AND ";
          $qCliDat .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
          $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
          if(mysql_num_rows($xCliDat) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Tipo Ticket [{$_POST['cEstado']}] no existe.\n";
          }
        }
      }

      // Valida que el email no sea vacio
      if(trim($_POST['cCliPCECn']) != "") {
        $vCorreos = explode(",", $_POST['cCliPCECn']);
        for ($i=0; $i < count($vCorreos); $i++) { 
          $vCorreos[$i] = trim($vCorreos[$i]);
          if($vCorreos[$i] != ""){
            if (!filter_var($vCorreos[$i], FILTER_VALIDATE_EMAIL)) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= " El Correo [".$vCorreos[$i]."], No es Valido.\n";
            }
          }
        }
      }

      $ticket = new cTickets();
      $datosCabecera = $ticket->fnCabeceraTickets($_POST['cCerId']);

      $cTiCcErx = ($datosCabecera['ticcierx'] != "0000-00-00") ? $datosCabecera['ticcierx'] : "";

      // Obtener valores dinámicos de cEmaUsr
      $i = 0;
      $ticketEnviado = [];
      while (isset($_POST["cEmaUsr$i"])) {
        $ticketEnviado[] = $_POST["cEmaUsr$i"];
        $i++;
      }

      if (count($ticketEnviado) > 0) {
        $cSubject = "Solicitud: \"1\" / \"".$datosCabecera['ttidesxx']."\" / \"".$datosCabecera['clinomxx']."\" / \"".$datosCabecera['stidesxx']."\" / \"".$datosCabecera['comprexx'].$datosCabecera['comcscxx']."\" ";
        
        $cMessage  = "<b>Ticket:</b> 1<br>";
        $cMessage .= "<b>Post ID:</b> 1<br>";
        $cMessage .= "<b>Asunto:</b> {$_POST['cAsuTck']}<br>";
        $cMessage .= "<b>Prioridad:</b> {$datosCabecera['ptidesxx']}<br>";
        $cMessage .= "<b>Status:</b> {$datosCabecera['stidesxx']}<br>";
        $cMessage .= "<b>Apertura Ticket:</b> {$datosCabecera['regfcrex']}<br>";
        $cMessage .= "<b>Cierre Ticket:</b> {$cTiCcErx}<br>";
        $cMessage .= "<b>Tipo de Ticket:</b> {$datosCabecera['ttidesxx']}<br>";
        $cMessage .= "<b>Ticket enviado a:</b> ".implode(', ', $ticketEnviado)."<br>";
        $cMessage .= "<b>Ticket CC a:</b> {$_POST['cCliPCECn']}<br>";
        $cMessage .= "<b>Certificaci&oacute;n:</b> {$datosCabecera['comprexx']}{$datosCabecera['comcscxx']}<br>";
        $cMessage .= "<b>Cliente:</b> {$datosCabecera['cliidxxx']}<br><br>";
        $cMessage .= "Contenido:<br>{$_POST['cConten']}";

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
                $cMsj .= "\nError al Enviar Correo al destinatario [{$ticketEnviado[$nC]}].\n".$cMsjError."\n";
              }
              $cCorreos .= "{$ticketEnviado[$nC]}, ";
            }
          }
          $cCorreos = substr($cCorreos, 0, strlen($cCorreos)-2);
        }
        
        if ($nSwitch == 0) {
          $cMsj .= "Se Envio el ticket con Exito a los Siguientes Correos:\n$cCorreos.\n";
        }
      }

      // Valida que el contenido no sea vacio
      if ($_POST['cConten'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El contenido no puede ser vacio.\n";
      }

    break;
  }
  // Fin de la Validacion

  // echo "<br>";
  // echo $nSwitch;
  // echo "<br>";
  // echo $cMsj;
  // echo "<br>";

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVOTICKET":
        $cAnioMif = $_POST['cPerAno'];
        // Insertando en la Tabla lccaYYYY (Cabecera)
        $qInsert  = array(array('NAME' => 'ceridxxx','VALUE' => trim($_POST['cCerId'])               ,'CHECK' => 'SI'),  //Id del comprobante
                          array('NAME' => 'comidxxx','VALUE' => trim($_POST['cComId'])               ,'CHECK' => 'SI'),  //Codigo del comprobante
                          array('NAME' => 'comcodxx','VALUE' => trim($_POST['cComCod'])              ,'CHECK' => 'SI'),  //Codigo del comprobante
                          array('NAME' => 'comprexx','VALUE' => trim(strtoupper($_POST['cComPre']))  ,'CHECK' => 'SI'),  //Prefijo
                          array('NAME' => 'comcscxx','VALUE' => trim($_POST['cComCsc'])              ,'CHECK' => 'SI'),  //Consecutivo uno
                          array('NAME' => 'comcsc2x','VALUE' => trim($_POST['cComCsc2'])             ,'CHECK' => 'SI'),  //Consecutivo dos
                          array('NAME' => 'comfecxx','VALUE' => trim($_POST['cComFec'])              ,'CHECK' => 'SI'),  //Fecha del comprobante
                          array('NAME' => 'cliidxxx','VALUE' => trim($_POST['cCliId'])               ,'CHECK' => 'SI'),  //Id Cliente
                          array('NAME' => 'tticodxx','VALUE' => trim($_POST['cTtiCod'])              ,'CHECK' => 'SI'),  //Codigo Tipo de Ticket
                          array('NAME' => 'pticodxx','VALUE' => trim($_POST['cPriori'])              ,'CHECK' => 'SI'),  //Codigo Prioridad Ticket
                          array('NAME' => 'sticodxx','VALUE' => trim($_POST['cEstado'])              ,'CHECK' => 'SI'),  //Codigo Status Ticket
                          array('NAME' => 'ticasuxx','VALUE' => trim($_POST['cAsuTck'])              ,'CHECK' => 'SI'),  //Asunto
                          array('NAME' => 'ticcierx','VALUE' => trim('')                             ,'CHECK' => 'NO'),  //Fecha de cierre
                          array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_POST['cUsrId']))   ,'CHECK' => 'SI'),  //Usuario que creo el registro
                          array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                        ,'CHECK' => 'SI'),  //Fecha de creacion
                          array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                        ,'CHECK' => 'SI'),  //Hora de creacion
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                        ,'CHECK' => 'SI'),  //Fecha de modificacion
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                        ,'CHECK' => 'SI'),  //Hora de modificacion
                          array('NAME' => 'regestxx','VALUE' => trim('ACTIVO')                       ,'CHECK' => 'SI'),  //Estado
                          array('NAME' => 'regstamp','VALUE' => date('Y-m-d H:m:s')                  ,'CHECK' => 'SI')); //Fecha de modificacion

        if (!f_MySql("INSERT","ltic$cPerAno",$qInsert,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error guardando datos de la Tickets - Cabecera \n";
        } else {
          // Insertando en la tabla ltidYYYY (Detalle)
          $qCertifiCab  = "SELECT ";
          $qCertifiCab .= "MAX(ticidxxx) AS ultimoId ";
          $qCertifiCab .= "FROM $cAlfa.ltic$cPerAno";
          $xCertifiCab  = f_MySql("SELECT","",$qCertifiCab,$xConexion01,"");
          $vCertifiCab  = mysql_fetch_array($xCertifiCab);

          $nErrorDetalle = 0;
          $qInsert = array(array('NAME' => 'ticidxxx','VALUE' => $vCertifiCab['ultimoId']           ,'CHECK' => 'SI'),  //Id Tickets Cabecera
                          array('NAME' => 'repcscxx','VALUE' => trim('1')                           ,'CHECK' => 'SI'),  //Consecutivo Reply
                          array('NAME' => 'tticodxx','VALUE' => trim($_POST['cTtiCod'])             ,'CHECK' => 'SI'),  //Codigo Tipo de Ticket
                          array('NAME' => 'pticodxx','VALUE' => trim($_POST['cPriori'])             ,'CHECK' => 'SI'),  //Codigo Prioridad Ticket
                          array('NAME' => 'sticodxx','VALUE' => trim($_POST['cEstado'])             ,'CHECK' => 'SI'),  //Codigo Status Ticket
                          array('NAME' => 'ticccopx','VALUE' => trim($_POST['cCliPCECn'])           ,'CHECK' => 'NO'),  //Correos en copia
                          array('NAME' => 'repreply','VALUE' => trim($_POST['cConten'])             ,'CHECK' => 'SI'),  //Reply
                          array('NAME' => 'reprepor','VALUE' => trim($_POST['cRePre'])              ,'CHECK' => 'SI'),  //Realizado por (RESPONSABLE/TERCERO)
                          array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_POST['cUsrId']))  ,'CHECK' => 'SI'),  //Usuario que creo el Registro
                          array('NAME' => 'regusrem','VALUE' => trim($_POST['cUsrEma'])             ,'CHECK' => 'SI'),  //Correo Usuario que Creo el Registro
                          array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                       ,'CHECK' => 'SI'),  //Fecha de Creacion del Registro
                          array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                       ,'CHECK' => 'SI'),  //Hora de Creacion del Registro
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                       ,'CHECK' => 'SI'),  //Fecha de Modificacion del Registro
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                       ,'CHECK' => 'SI'),  //Hora de Modificacion del Registro
                          array('NAME' => 'regestxx','VALUE' => trim('ACTIVO')                      ,'CHECK' => 'SI'),  //Estado del Registro
                          array('NAME' => 'regstamp','VALUE' => date('Y-m-d H:m:s')                 ,'CHECK' => 'SI')); //Modificado

          if (!f_MySql("INSERT","ltid$cPerAno",$qInsert,$xConexion01,$cAlfa)) {
            $nErrorDetalle = 1;
          }

          if ($nErrorDetalle == 1) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error guardando datos de la Tickets - Replies en el detalle.\n";
          }
        }
      break;
      case "EDITAR":
        $cAnioMif = $_POST['cPerAno'];

        // Actualiza la observacion de cabecera
        $qUpdate = array(array('NAME' => 'cerobsxx', 'VALUE' => $_POST['cCerObs']    ,'CHECK'=>'NO'),
                        array('NAME' => 'ceridxxx', 'VALUE' => $_POST['cCerId']     ,'CHECK'=>'WH'));
                        
        if (!f_MySql("UPDATE","lcca$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error actualizando la observacion de la Certificacion.\n";
        }

        // Válida si los subservicios guardados en el sistema son los mismos que se están editando, sino se eliminan de la Base de Datos
        $qCertifiDet  = "SELECT ";
        $qCertifiDet .= "$cAlfa.lcde$cPerAno.* ";
        $qCertifiDet .= "FROM $cAlfa.lcde$cPerAno ";
        $qCertifiDet .= "WHERE ";
        $qCertifiDet .= "$cAlfa.lcde$cPerAno.ceridxxx = \"{$_POST['cCerId']}\" ";
        $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
        if (mysql_num_rows($xCertifiDet) > 0) {
          while ($xRCD = mysql_fetch_array($xCertifiDet)) {
            $nExiste = 0;
            // Recorre la grilla de certificacion
            for ($i=1; $i <= $_POST['nSecuencia_Certificacion']; $i++) { 
              if ($xRCD['cerdidxx'] == $_POST['cCerdId' . $i ]) {
                $nExiste = 1;
              }
            }

            // Si no existe el subservicio se elimina de la Base de Datos
            if ($nExiste == 0) {
              $qDelete = array(array('NAME' => 'cerdidxx','VALUE' => trim(strtoupper($xRCD['cerdidxx']))   ,'CHECK'=>'WH'));
              if (f_MySql("DELETE","lcde$cPerAno",$qDelete,$xConexion01,$cAlfa) && $_POST['cMifId'] != "") {
                // Se libera el consecutivo de la certificacion en la MIF
                $qMifSubservi  = "SELECT ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.mifdidxx ";
                $qMifSubservi .= "FROM $cAlfa.lmsu$cAnioMif ";
                $qMifSubservi .= "WHERE ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.mifidxxx = \"{$_POST['cMifId']}\" AND ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.sersapxx = \"{$xRCD['sersapxx']}\" AND ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.subidxxx = \"{$xRCD['subidxxx']}\"";
                $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
                $nError = 0;
                if (mysql_num_rows($xMifSubservi) > 0) {
                  while ($xRMS = mysql_fetch_array($xMifSubservi)) {
                    $qUpdateMif = array(array('NAME'=>'cercscxx','VALUE' => ""                 ,'CHECK'=>'NO'),
                                        array('NAME'=>'regestxx','VALUE' => "ACTIVO"           ,'CHECK'=>'SI'),
                                        array('NAME'=>'mifdidxx','VALUE' => $xRMS['mifdidxx']  ,'CHECK'=>'WH'));
                    
                    if (!f_MySql("UPDATE","lmsu$cAnioMif",$qUpdateMif,$xConexion01,$cAlfa)) {
                      $nError = 1;
                    }
                  }

                  if ($nError == 1) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "No se pudo actualizar el consecutivo de Certificacion en el detalle de la MIF.\n";
                  }
                }
              }
            }
          }
        }

        $nErrorDetalle = 0;
        // Actualiza o crea los nuevos subservicios de la grilla de certificacion
        for ($i=1; $i <= $_POST['nSecuencia_Certificacion']; $i++) {
          $qCertifiDet  = "SELECT ";
          $qCertifiDet .= "$cAlfa.lcde$cPerAno.* ";
          $qCertifiDet .= "FROM $cAlfa.lcde$cPerAno ";
          $qCertifiDet .= "WHERE ";
          $qCertifiDet .= "$cAlfa.lcde$cPerAno.cerdidxx = \"{$_POST['cCerdId' . $i]}\" ";
          $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
          if (mysql_num_rows($xCertifiDet) > 0) {
            $qUpdate = array(array('NAME' => 'sersapxx','VALUE' => trim($_POST['cCodSapSer' . $i])       ,'CHECK' => 'NO'),  //Codigo SAP Servicio
                            array('NAME' => 'subidxxx','VALUE' => trim($_POST['cSubId_Certi' . $i])     ,'CHECK' => 'NO'),  //Id Subservicio
                            array('NAME' => 'subdesxx','VALUE' => trim($_POST['cDesMaterial' . $i])     ,'CHECK' => 'SI'),  //Descripcion Subservicio
                            array('NAME' => 'obfidxxx','VALUE' => trim($_POST['cObfId_Certi' . $i])     ,'CHECK' => 'SI'),  //Id Objeto Facturable
                            array('NAME' => 'ufaidxxx','VALUE' => trim($_POST['cUfaId_Certi' . $i])     ,'CHECK' => 'SI'),  //Id Unidad Facturable
                            array('NAME' => 'cebidxxx','VALUE' => trim($_POST['cCebId' . $i])           ,'CHECK' => 'SI'),  //Id Codigo Cebe
                            array('NAME' => 'basexxxx','VALUE' => trim($_POST['cBase' . $i])            ,'CHECK' => 'SI'),  //Base
                            array('NAME' => 'cerdconx','VALUE' => trim($_POST['cCondicion' . $i])       ,'CHECK' => 'SI'),  //Condicion
                            array('NAME' => 'cerdestx','VALUE' => trim($_POST['cStatus' . $i])          ,'CHECK' => 'SI'),  //Status
                            array('NAME' => 'cerdorix','VALUE' => trim($_POST['cTipoCerti' . $i])       ,'CHECK' => 'SI'),  //Origen
                            array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK' => 'SI'),  //Usuario que creo el registro
                            array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),  //Fecha de creacion
                            array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),  //Hora de creacion 
                            array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),  //Fecha de modificacion
                            array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),  //Hora de modificacion
                            array('NAME' => 'cerdidxx','VALUE' => $_POST['cCerdId' . $i]                ,'CHECK' => 'WH'));

            if (!f_MySql("UPDATE","lcde$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
              $nErrorDetalle = 1;
            }
          } else {
            $qInsert = array(array('NAME' => 'ceridxxx','VALUE' => trim($_POST['cCerId'])                ,'CHECK' => 'SI'),  //Id Certificacion Cabecera
                            array('NAME' => 'sersapxx','VALUE' => trim($_POST['cCodSapSer' . $i])       ,'CHECK' => 'NO'),  //Codigo SAP Servicio
                            array('NAME' => 'subidxxx','VALUE' => trim($_POST['cSubId_Certi' . $i])     ,'CHECK' => 'NO'),  //Id Subservicio
                            array('NAME' => 'subdesxx','VALUE' => trim($_POST['cDesMaterial' . $i])     ,'CHECK' => 'SI'),  //Descripcion Subservicio
                            array('NAME' => 'obfidxxx','VALUE' => trim($_POST['cObfId_Certi' . $i])     ,'CHECK' => 'SI'),  //Id Objeto Facturable
                            array('NAME' => 'ufaidxxx','VALUE' => trim($_POST['cUfaId_Certi' . $i])     ,'CHECK' => 'SI'),  //Id Unidad Facturable
                            array('NAME' => 'cebidxxx','VALUE' => trim($_POST['cCebId' . $i])           ,'CHECK' => 'SI'),  //Id Codigo Cebe
                            array('NAME' => 'basexxxx','VALUE' => trim($_POST['cBase' . $i])            ,'CHECK' => 'SI'),  //Base
                            array('NAME' => 'cerdconx','VALUE' => trim($_POST['cCondicion' . $i])       ,'CHECK' => 'SI'),  //Condicion
                            array('NAME' => 'cerdestx','VALUE' => trim($_POST['cStatus' . $i])          ,'CHECK' => 'SI'),  //Status
                            array('NAME' => 'cerdorix','VALUE' => trim($_POST['cTipoCerti' . $i])       ,'CHECK' => 'SI'),  //Origen
                            array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK' => 'SI'),  //Usuario que creo el registro
                            array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),  //Fecha de creacion
                            array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),  //Hora de creacion 
                            array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),  //Fecha de modificacion
                            array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),  //Hora de modificacion
                            array('NAME' => 'regestxx','VALUE' => "ENPROCESO"                           ,'CHECK' => 'SI')); //Estado

            if (!f_MySql("INSERT","lcde$cPerAno",$qInsert,$xConexion01,$cAlfa)) {
              $nErrorDetalle = 1;
            } else {
              // Actualiza el Subservicio de la MIF con el Consecutivo de la Certificacion
              if ($_POST['cMifComCsc'] != "" && $_POST['cMifId'] != "") {
                $cAnioMif = $_POST['cPerAno'];
                $qMifSubservi  = "SELECT ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.mifdidxx ";
                $qMifSubservi .= "FROM $cAlfa.lmsu$cAnioMif ";
                $qMifSubservi .= "WHERE ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.mifidxxx = \"{$_POST['cMifId']}\" AND ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.sersapxx = \"{$_POST['cCodSapSer' . $i]}\" AND ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.subidxxx = \"{$_POST['cSubId_Certi' . $i]}\"";
                $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
                $nError = 0;
                if (mysql_num_rows($xMifSubservi) > 0) {
                  while ($xRMS = mysql_fetch_array($xMifSubservi)) {
                    $cConsecutivo = trim(strtoupper($_POST['cComId']))."-".trim(strtoupper($_POST['cComPre']))."-".trim(strtoupper($_POST['cComCsc']));
                    $qUpdateMif   = array(array('NAME'=>'cercscxx','VALUE' => $cConsecutivo      ,'CHECK'=>'SI'),
                                          array('NAME'=>'regestxx','VALUE' => "CERTIFICADO"      ,'CHECK'=>'SI'),
                                          array('NAME'=>'mifdidxx','VALUE' => $xRMS['mifdidxx']  ,'CHECK'=>'WH'));
                    
                    if (!f_MySql("UPDATE","lmsu$cAnioMif",$qUpdateMif,$xConexion01,$cAlfa)) {
                      $nError = 1;
                    }
                  }

                  if ($nError == 1) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "No se pudo actualizar el consecutivo de Certificacion en el detalle de la MIF.\n";
                  }
                }
              }
            }
          }
        }

        if ($nErrorDetalle == 1) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error actualizando datos en el detalle de la Certificacion.\n";
        }

      break;
      case "ANULAR":
        // Actualiza el registro de cabecera
        $qUpdate = array(array('NAME' => 'cerusuan','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))   ,'CHECK' => 'SI'),
                        array('NAME' => 'cerfecan','VALUE' => date('Y-m-d H:i:s')                    ,'CHECK' => 'SI'),
                        array('NAME' => 'cerobsan','VALUE' => $_POST['gObservacion']                 ,'CHECK' => 'SI'),
                        array('NAME' => 'regestxx','VALUE' => "ANULADO"                              ,'CHECK' => 'SI'),
                        array('NAME' => 'comidxxx','VALUE' => $_POST['gComId']                       ,'CHECK' => 'WH'),
                        array('NAME' => 'comcodxx','VALUE' => $_POST['gComCod']                      ,'CHECK' => 'WH'),
                        array('NAME' => 'comcscxx','VALUE' => $_POST['gComCsc']                      ,'CHECK' => 'WH'),
                        array('NAME' => 'comcsc2x','VALUE' => $_POST['gComCsc2']                     ,'CHECK' => 'WH'));

        if (!f_MySql("UPDATE","lcca$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al actualizar el estado de Anulacion en la cabecera de la Certificacion.\n";
        } else {
          // Actualiza los registros de detalle
          $qCertifiDet  = "SELECT ";
          $qCertifiDet .= "lcde$cPerAno.cerdidxx, ";
          $qCertifiDet .= "lcde$cPerAno.sersapxx, ";
          $qCertifiDet .= "lcde$cPerAno.subidxxx, ";
          $qCertifiDet .= "lcde$cPerAno.cerdorix ";
          $qCertifiDet .= "FROM $cAlfa.lcde$cPerAno ";
          $qCertifiDet .= "WHERE ";
          $qCertifiDet .= "$cAlfa.lcde$cPerAno.ceridxxx = \"{$vCertificacion['ceridxxx']}\" ";
          $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
          if (mysql_num_rows($xCertifiDet) > 0) {
            while ($xRCD = mysql_fetch_array($xCertifiDet)) {
              $qUpdate = array(array('NAME' => 'regestxx','VALUE' => "ANULADO"            ,'CHECK' => 'SI'),
                              array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')        ,'CHECK' => 'SI'),
                              array('NAME' => 'reghmodx','VALUE' => date('H:i:s')        ,'CHECK' => 'SI'),
                              array('NAME' => 'cerdidxx','VALUE' => $xRCD['cerdidxx']    ,'CHECK' => 'WH'));

              if (!f_MySql("UPDATE","lcde$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Error al actualizar el estado de Anulacion en el detalle de la Certificacion.\n";
              } else {
                if ($xRCD['sersapxx'] != "" && $xRCD['subidxxx'] != "" && $xRCD['cerdorix'] == "MIF") {
                  fnActualizaEstadoSubserviciosMif($vCertificacion['mifidano'], $vCertificacion['mifidxxx'], $xRCD['sersapxx'], $xRCD['subidxxx'], "ACTIVO");
                }
              }
            }
          }
        }
      
      break;
    }
  }

  // echo "<br>";
  // echo $nSwitch;
  // echo "<br>";
  // echo $cMsj;
  // echo "<br>";
  // die();

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVOTICKET":
        f_Mensaje(__FILE__,__LINE__,"El Ticket se creo con exito.");
      break;
      case "EDITAR":
        f_Mensaje(__FILE__,__LINE__,"Se actualizo el Ticket con exito.");
      break;
      case "ANULAR":
        f_Mensaje(__FILE__,__LINE__,"Se Anulo la Certificacion con exito.");
      break;
      default:
        // no hace nada
      break;
    }

    if ($_COOKIE['kModo'] == "NUEVOTICKET" || $_COOKIE['kModo'] == "EDITAR") {
      ?>
      <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
        document.forms['frgrm'].submit();
      </script> 
      <?php
    } else {
      ?>
        <script type="text/javascript">
          parent.window.fmwork.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        </script>
      <?php
    }
  } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
  }
?>

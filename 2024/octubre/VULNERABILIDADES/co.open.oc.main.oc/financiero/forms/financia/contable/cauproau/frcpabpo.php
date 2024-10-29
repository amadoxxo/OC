<?php
  namespace openComex;
  /**
   * Procesar Documentos PCCA de BPO
   *
   * @author Johana Arboleda Ramos <johana.arboleda@open-eb.co>
   * @package openComex
   * @version 001
   */

  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");

  date_default_timezone_set('America/Bogota');

  /**
   * Variables de error
   */
  $nSwitch = 0; 
  $cMensaje = "";

  /**
   * Cuando se ejecuta desde el cron debe armarse la cookie para incluir los utilitys
   */
  if ($_SERVER["SERVER_PORT"] == "") {
    if ($nSwitch == 0) {

      //Incluyendo Ruta del Config
      include ($OPENINIT['pathdr']."/opencomex/config/config.php");

      //Haciendo Conexion
      $xConexion01 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con MYSQL, Verifique.\n");

      //Consultando tabla de agenamiento
      $qAge  = "SELECT * ";
      $qAge .= "FROM LOCK.sysbpoag ";
      $qAge .= "WHERE ";
      $qAge .= "regestxx = \"ACTIVO\" LIMIT 0,1";
      $xAge  = mysql_query($qAge,$xConexion01);
      
      if (mysql_num_rows($xAge) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "No se Encontro Agendamiento [sysbpoag][{$vArg[0]}].|";
      } else {
        $vAge = mysql_fetch_assoc($xAge);
        
        //Actualizando Agendamiento a finalizado
        $qUpdate = "UPDATE LOCK.sysbpoag SET regestxx = \"ENPROCESO\" WHERE ageidxxx = \"{$$vAge['ageidxxx']}\"";
        if (!mysql_query($qUpdate,$xConexion01)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al actualizar el estado del registro[sysbpoag].|";
        }

        switch (OC_ENVIRONMENT) {
          case "produccion":  $cPrefijo = "";   break;
          case "pruebas":     $cPrefijo = "TE"; break;
          case "desarrollo":  $cPrefijo = "DE"; break;
          default:
            //No hace
          break;
        }//switch(OC_ENVIRONMENT){

        $cUserDB     = strtolower($cPrefijo.$vAge['agedbxxx']);
        $cUserDBPass = "opencomex";
        $cDataBase   = strtoupper($cPrefijo.$vAge['agedbxxx']);
        $cDataBaseC  = strtoupper($cPrefijo.$vAge['agedbxxx']);
        $_COOKIE["kDatosFijos"] = OC_SERVER."~$cUserDB~$cUserDBPass~$cDataBase~$cDataBaseC";

        include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utility.php");
        include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/uticonta.php");
        include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utitrans.php");
        include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utiajuxx.php");
        include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/uticupro.php");
        include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/uticpaxx.php");
        include("{$OPENINIT['pathdr']}/opencomex/libs/php/uticidwf.php");

        #Datos requeridos para procesar el documento
        $qDocBPO  = "SELECT * ";
        $qDocBPO .= "FROM $cDataBase.bfpccxxx ";
        $qDocBPO .= "WHERE ";
        $qDocBPO .= "fpcidxxx = \"{$vAge['ageidpro']}\" AND ";
        $qDocBPO .= "fpcestpr = \"\" LIMIT 0,1 ";
        $xDocBPO  = mysql_query($qDocBPO,$xConexion01);
      
        if (mysql_num_rows($xDocBPO) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "No se Encontro Registro [bfpccxxx][{$vAge['ageidpro']}].|";
        } else {
          $vDocBPO = mysql_fetch_assoc($xDocBPO);

          $qUpdate = "UPDATE $cDataBase.bfpccxxx SET fpcestpr = \"ENPROCESO\" WHERE fpcidxxx = \"{$vAge['ageidpro']}\"";
          if (!mysql_query($qUpdate,$xConexion01)) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al actualizar el estado del registro[bfpccxxx].|";
          }
        }
      }
    }

    if ($nSwitch == 0) {

      //Informacion de cabecera fpccabxx
      $oDocCab = json_decode($vDocBPO['fpccabxx']);

      //Informacion de detalle fpcdetxx
      $oDocDet = json_decode(utf8_encode($vDocBPO['fpcdetxx']));

      //Usuario que se le asigna la causacion
      $_COOKIE['kUsrId'] = $oDocCab->cac_usuario;

      $vDatos['fpcidxxx'] = $vDocBPO['fpcidxxx'];
      $vDatos['terid2xx'] = $vDocBPO['terid2xx'];
      $vDatos['comcscxx'] = $vDocBPO['comcscxx'];
      $vDatos['fpccabxx'] = $oDocCab;
      $vDatos['fpcdetxx'] = $oDocDet;
      $vDatos['fpctitem'] = $vDocBPO['fpctitem'];
      $vDatos['regusrxx'] = $vDocBPO['regusrxx'];

      #Creando Causacion pagos a terceros automatica
      #Creando la instancia para la creacion de Causacion pagos a terceros automatica
      $ObjCau = new cCausacionPagosTercerosAutomatica();
      $vRetorna = $ObjCau->fnProcesarCausacionTercerosBPO($vDatos);

      //Estado del proceso
      $cEstPr = ($vRetorna[0] == "true") ? "EXITOSO" : "FALLIDO";
      //Mensajes de error del proceso
      $cMsjRet = "";
      for ($nR=1; $nR<count($vRetorna);$nR++) {
        $cMsjRet .= $vRetorna[$nR]."|";
      }
      $cMsjRet = substr($cMsjRet,0,-1);

      //Actualizando registro 
      $qUpdate  = "UPDATE $cDataBase.bfpccxxx SET ";
      $qUpdate .= "fpcerror = \"$cMsjRet\",";
      $qUpdate .= "fpcestpr = \"$cEstPr\" ";
      $qUpdate .= "WHERE fpcidxxx = \"{$vAge['ageidpro']}\"";
      if (!mysql_query($qUpdate,$xConexion01)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error al actualizar el estado del registro[bfpccxxx].|";
      }

      //Actualizando registro en la base de datos de BPO
      //Instanciando Objetos para Crear Conexion con DWF
      $ObjConexion  = new cEstructurasDWF();
      $mReturnCon  = $ObjConexion->fnConectarDwfDB();
      if($mReturnCon[0] == "false") {
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnCon);$nR++){
          $cMsj .= $mReturnCon[$nR]."|";
        }
      } else {
        $xEnlaces = $mReturnCon[1];

        //Concectando con DWF
        $vArchivo = explode("~",OC_DWF);
        $cBase   = $vArchivo[2];

        $qUpdate  = "UPDATE $cBase.dwf_factura_pcc SET ";
        $qUpdate .= "fpc_mensaje_error_erp = \"$cMsjRet\",";
        $qUpdate .= "fpc_estado_proceso_erp = \"$cEstPr\" ";
        $qUpdate .= "WHERE fpc_id = \"{$vDocBPO['fpcidbpo']}\"";
        if (!mysql_query($qUpdate,$xEnlaces)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al actualizar el estado del registro[dwf_factura_pcc].|";
        }        
      }
    }

    //Actualizando Agendamiento a finalizado
    $qUpdate = "UPDATE LOCK.sysbpoag SET regestxx = \"FINALIZADO\" WHERE ageidxxx = \"{$vAge['ageidxxx']}\"";
    if (!mysql_query($qUpdate,$xConexion01)) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Error al actualizar el estado del registro[sysbpoag].|";
    }

    // $cMsj = substr($cMsj, 0, -1);
    // echo $cMsj;

  }
?>

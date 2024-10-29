<?php
  /**
   * Transmitir Documento a OpenPCE
   *
   * @author Shamaru Primera C. <shamaru001@gmail.com>
   * @package openComex
   * @version 001
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  include("../../../../../config/config.php");
  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utimovdo.php");
  include("../../../../libs/php/utiliqdo.php");
  include("../../../../libs/php/uticerma.php");
  include("../../../../libs/php/uticietl.php");

  /**
   *  Cookie fija
   */
  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb   = $kDf[3];
  $kUser      = $kDf[4];
  $kLicencia  = $kDf[5];
  $swidth     = $kDf[6];

  // Armo las variables para consultar y actualizar .
  $cPerAno = substr($_POST['dRegFCre'],0,4);
  $cPerMes = substr($_POST['dRegFCre'],5,2);
  $cPerDia = substr($_POST['dRegFCre'],8,2);

  /**
   * Variable para controlar si hay errores de validacion
   * @var integer
   */
  $nSwitch = 0;

  /**
   * Variable para concatenar los mensajes de validacion
   * @var string
   */
  $cMsj = "";

  /**
   * Validando Licencia
   */
  $nLic = f_Licencia();
  if ($nLic == 0){
    $nSwitch = 1;
    $cMsj .= "Error grave de Seguridad otro usuario ingreso con su clave\n";
  }

  /**
   * Inicio de validaciones
   */
  switch ($_COOKIE['kModo']) {
    case "TRANSMITIROPENPCE":
      //Transmision openETL
      if ($vSysStr['system_activar_openetl'] == "SI") {
        
        $cEnvETL  = "SI"; // Variable que indica si el comprobante debe ser enviado a openETL
        
        // Trayendo valor pagos a terceros
        $qDocCon  = "SELECT comexpcc, comipxxx ";
        $qDocCon .= "FROM $cAlfa.fcoc$cPerAno ";
        $qDocCon .= "WHERE ";
        $qDocCon .= "fcoc$cPerAno.comidxxx = \"{$_POST['cComId']}\"  AND ";
        $qDocCon .= "fcoc$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";
        $qDocCon .= "fcoc$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";
        $qDocCon .= "fcoc$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\" ";
        $xDocCon  = f_MySql("SELECT","",$qDocCon,$xConexion01,"");
        $vDocCon  = mysql_fetch_array($xDocCon);

        // Si se excluyo y los ingresos propios son iguales a cero no se envia a openETL
        if ($vDocCon['comexpcc'] == "SI" && ($vDocCon['comipxxx']+0) == 0) {
          $cEnvETL  = "NO";
        }

        if ($cEnvETL == "SI") {
          $vParametros['database'] = $cAlfa;              //Base de datos.
          $vParametros['sysstrxx'] = $vSysStr;            //Variables del sistema.
          $vParametros['comidxxx'] = $_POST['cComId'];    //Id del documento
          $vParametros['comcodxx'] = $_POST['cComCod'];   //Codigo del documento
          $vParametros['comcscxx'] = $_POST['cComCsc'];   //Consecutivo del documento
          $vParametros['comcsc2x'] = $_POST['cComCsc2'];  //Consecutivo 2 del documento
          $vParametros['comfecxx'] = $_POST['dRegFCre'];  //Fecha del documento
          $vParametros['conexion'] = $xConexion01;        //Conexion BD
          $vParametros['origenxx'] = "TRACKING";          //Origen
  
          $objFeVp = new cIntegracionopenETL();
          $mReturnData = $objFeVp->fnRegistrarDocumentosOpenETL($vParametros);
  
          if($mReturnData[0] == "false") {
            $cMsj = "Se Presentaron los siguientes errores al transmitir el documento a openETL:\n\n";
          }
  
          for ($nRT=2; $nRT<count($mReturnData); $nRT++) {
            $cMsj .= $mReturnData[$nRT]."\n";
          }
        } else {
          $nSwitch = 1;
          $cMsj .= "No Es Posible Transmitir El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}], Es un Documento de Cobro\n";
        }
      } elseif ($vSysStr['system_activar_openpce'] == "SI") {
        //Transision openPCE
        /* Realizo la validacion de los campos compcees y compcesn */
        //Buscando los datos del comprobante en cabecera
        $qDocCon  = "SELECT fcoc$cPerAno.compcees, fcoc$cPerAno.compcesn, fcoc$cPerAno.compceen, fcoc$cPerAno.compcere ";
        $qDocCon .= "FROM $cAlfa.fcoc$cPerAno ";
        $qDocCon .= "WHERE ";
        $qDocCon .= "fcoc$cPerAno.comidxxx = \"{$_POST['cComId']}\"  AND ";
        $qDocCon .= "fcoc$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";
        $qDocCon .= "fcoc$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";
        $qDocCon .= "fcoc$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\" ";
        $xDocCon  = f_MySql("SELECT","",$qDocCon,$xConexion01,"");
        $vDocCon  = mysql_fetch_array($xDocCon);
        //f_Mensaje(__FILE__,__LINE__,$qDocCon."~".mysql_num_rows($xDocCon));

        switch ($vDocCon["compcesn"]) {
          case "NOENVIAR":
            if (!($vDocCon["compcees"] != "0000-00-00 00:00:00" && $vDocCon["compceen"] != "0000-00-00 00:00:00")) {
              $nSwitch = 1;
              $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] no Puede ser Reenviado a openPCE.\n";
            } else {
              $cMsj = "Se Desmarco el Comprobante [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] con Exito, para ser Transmitido a openPCE.\n";
            }
          break;
          
          default:
            if (! (($vDocCon["compceen"] != "0000-00-00 00:00:00" && $vDocCon["compcesn"] != "EXITOSO") || ($vDocCon["compceen"] != "0000-00-00 00:00:00" && $vDocCon["compcees"] != "0000-00-00 00:00:00" && $vDocCon["compcere"] != "EXITOSO"))  ) {
              $nSwitch = 1;
              $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] no Puede ser Reenviado a openPCE.\n";
            } else {
              $cMsj = "Se Desmarco el Comprobante [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] con Exito, para ser Reenviado a openPCE.\n";
            }
          break;
        }
      }
    break;
    case "NOENVIAROPENPCE":
      /* Realizo la validacion de los campos compcees y compcesn */
      //Buscando los datos del comprobante en cabecera
      $qDocCon = "SELECT fcoc$cPerAno.compcees, fcoc$cPerAno.compcesn, fcoc$cPerAno.compceen, fcoc$cPerAno.compcere ";
      $qDocCon .= "FROM $cAlfa.fcoc$cPerAno ";
      $qDocCon .= "WHERE ";
      $qDocCon .= "fcoc$cPerAno.comidxxx = \"{$_POST['cComId']}\"  AND ";
      $qDocCon .= "fcoc$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";
      $qDocCon .= "fcoc$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";
      $qDocCon .= "fcoc$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\" ";
      $xDocCon = f_MySql("SELECT", "", $qDocCon, $xConexion01, "");
      $vDocCon = mysql_fetch_array($xDocCon);
      //f_Mensaje(__FILE__,__LINE__,$qDocCon."~".mysql_num_rows($xDocCon));

      if(!($vDocCon["compcees"] == "0000-00-00 00:00:00" && $vDocCon["compceen"] == "0000-00-00 00:00:00" && $vDocCon["compcesn"] == "" && $vDocCon["compcere"] == "")){
        $nSwitch = 1;
        $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] no Puede ser Inhabilitado para su envio a openPCE.\n";
      }
    break;
    case "CONSULTAROPENETL":
      /* Realizo la validacion de que el documento ya haya sido enviado a openETL */
      //Buscando los datos del comprobante en cabecera
      $qDocCon = "SELECT fcoc$cPerAno.compcesn, fcoc$cPerAno.compcevx, fcoc$cPerAno.resprexx, fcoc$cPerAno.comobs2x, fcoc$cPerAno.regestxx ";
      $qDocCon .= "FROM $cAlfa.fcoc$cPerAno ";
      $qDocCon .= "WHERE ";
      $qDocCon .= "fcoc$cPerAno.comidxxx = \"{$_POST['cComId']}\"  AND ";
      $qDocCon .= "fcoc$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";
      $qDocCon .= "fcoc$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";
      $qDocCon .= "fcoc$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\"";
      $xDocCon = f_MySql("SELECT", "", $qDocCon, $xConexion01, "");
      $vDocCon = mysql_fetch_array($xDocCon);
      //f_Mensaje(__FILE__,__LINE__,$qDocCon."~".mysql_num_rows($xDocCon));

      if(!($vDocCon["compcesn"] != "" && $vDocCon["compcevx"] == "VP")) {
        $nSwitch = 1;
        $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] no Puede ser Consultado, este no ha sido enviado a openETL.\n";
      }
    break;
    case "DESCARGARPDFETL":
    case "REENVIARCORREO":

      $cMensaje = ($_COOKIE['kModo'] == "DESCARGARPDFETL") ? "Descargado" : "Reenviado";

      /* Realizo la validacion de que el documento ya haya sido enviado a openETL */
      //Buscando los datos del comprobante en cabecera
      $qDocCon  = "SELECT ";
      $qDocCon .= "fcoc$cPerAno.compcesn, ";
      $qDocCon .= "fcoc$cPerAno.compcevx, ";
      $qDocCon .= "fcoc$cPerAno.compcere, ";
      $qDocCon .= "fcoc$cPerAno.resprexx ";
      $qDocCon .= "FROM $cAlfa.fcoc$cPerAno ";
      $qDocCon .= "WHERE ";
      $qDocCon .= "fcoc$cPerAno.comidxxx = \"{$_POST['cComId']}\"  AND ";
      $qDocCon .= "fcoc$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";
      $qDocCon .= "fcoc$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";
      $qDocCon .= "fcoc$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\" ";
      $xDocCon  = f_MySql("SELECT", "", $qDocCon, $xConexion01, "");
      $vDocCon  = mysql_fetch_array($xDocCon);
      //f_Mensaje(__FILE__,__LINE__,$qDocCon."~".mysql_num_rows($xDocCon));

      if(!($vDocCon["compcesn"] != "" && $vDocCon["compcevx"] == "VP")) {
        $nSwitch = 1;
        $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] no Puede ser {$cMensaje}, este no ha sido enviado a openETL.\n";
      }

      if($vDocCon["compcere"] == "FALLIDO"){
        $nSwitch = 1;
        $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] no Puede ser {$cMensaje}, tiene estado FALLIDO en openETL.\n";
      }
    break;
    default:
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El Modo de Grabado No Es Correcto.\n";
    break;
  }
  /*** Fin de Validaciones ***/

  if ($nSwitch == 0){
    switch ($_COOKIE['kModo']) {
      case "TRANSMITIROPENPCE":
        if ($vSysStr['system_activar_openpce'] == "SI" && $vSysStr['system_activar_openetl'] != "SI") {
          $qUpdate  = "UPDATE $cAlfa.fcoc$cPerAno SET ";
          $qUpdate .= "fcoc$cPerAno.compceen = \"0000-00-00 00:00:00\", ";
          $qUpdate .= "fcoc$cPerAno.compcees = \"0000-00-00 00:00:00\", ";
          $qUpdate .= "fcoc$cPerAno.compcesn = \"\", ";
          $qUpdate .= "fcoc$cPerAno.compcere = \"\" ";
          $qUpdate .= "WHERE ";
          $qUpdate .= "fcoc$cPerAno.comidxxx = \"{$_POST['cComId']}\"  AND ";
          $qUpdate .= "fcoc$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";
          $qUpdate .= "fcoc$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";
          $qUpdate .= "fcoc$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\" ";
          $xUpdate = mysql_query($qUpdate);

          if (!$xUpdate){
            $nSwitch = 1;
            $cMsj .= "Error al Desmarcar el Comprobante [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] Para ser Reenviado a OpenPCE.\n";
          }
        } 
      break;
      case "NOENVIAROPENPCE":
        $qUpdate = "UPDATE $cAlfa.fcoc$cPerAno SET ";
        $qUpdate .= "fcoc$cPerAno.compcees = NOW(), ";
        $qUpdate .= "fcoc$cPerAno.compceen = NOW(), ";
        $qUpdate .= "fcoc$cPerAno.compcesn = \"NOENVIAR\" ";
        $qUpdate .= "WHERE ";
        $qUpdate .= "fcoc$cPerAno.comidxxx = \"{$_POST['cComId']}\"  AND ";
        $qUpdate .= "fcoc$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";
        $qUpdate .= "fcoc$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";
        $qUpdate .= "fcoc$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\" ";
        $xUpdate = mysql_query($qUpdate);
        //f_Mensaje(__FILE__,__LINE__,$qUpdate);

        if (!$xUpdate) {
          $nSwitch = 1;
          $cMsj .= "Error al Inhabilitar el Comprobante [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] Para ser Transmitido a openPCE.\n";
        } else {
          $cMsj = "Se Inhabilito el Comprobante [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] con Exito, no podra ser Transmitido a openPCE.\n";
        }
      break;
      case "CONSULTAROPENETL":
        $vParametros['database'] = $cAlfa;               //Base de datos.
        $vParametros['sysstrxx'] = $vSysStr;             //Variables del sistema.
        $vParametros['comidxxx'] = $_POST['cComId'];     //Id del documento
        $vParametros['comcodxx'] = $_POST['cComCod'];    //Codigo del documento
        $vParametros['comcscxx'] = $_POST['cComCsc'];    //Consecutivo del documento
        $vParametros['comcsc2x'] = $_POST['cComCsc2'];   //Consecutivo 2 del documento
        $vParametros['comfecxx'] = $_POST['dRegFCre'];   //Fecha del documento
        $vParametros['resprexx'] = $vDocCon['resprexx']; //Prefijo Comprobante
        $vParametros['comobs2x'] = $vDocCon['comobs2x']; //Observacion dos comprobante
        $vParametros['origenxx'] = "TRACKING";           //Origen

        $objFeVp = new cIntegracionopenETL();
        $mReturnData = $objFeVp->fnConsumirWSConsultaDocumentos($vParametros);

        if($mReturnData[0] == "false") {
          $cMsj = "Se Presentaron los siguientes errores al consultar el documento en openETL:\n\n";
        }

        for ($nE=0; $nE<count($mReturnData[1]); $nE++) {
          $cMsj .=  $mReturnData[1][$nE]."\n";
        }

        for ($nE=0; $nE<count($mReturnData[3]); $nE++) {
          $cMsj .=  $mReturnData[3][$nE]."\n";
        }
      break;
      case "DESCARGARPDFETL":
        $vParametros['database'] = $cAlfa;              //Base de datos.
        $vParametros['sysstrxx'] = $vSysStr;            //Variables del sistema.
        $vParametros['resultxx'] = "base64";            //Resultado de la peticion.
        $vParametros['comidxxx'] = $_POST['cComId'];    //Id del documento
        $vParametros['comcodxx'] = $_POST['cComCod'];   //Codigo del documento
        $vParametros['comcscxx'] = $_POST['cComCsc'];   //Consecutivo del documento
        $vParametros['comcsc2x'] = $_POST['cComCsc2'];  //Consecutivo 2 del documento
        $vParametros['comfecxx'] = $_POST['dRegFCre'];  //Fecha del documento
        $vParametros['conexion'] = $xConexion01;        //Conexion BD

        $objFeVp = new cIntegracionopenETL();
        $mReturnData = $objFeVp->fnDescargarPdfETL($vParametros);

        ## Valido si llega la respuesta en "true" para generar el archivo ##
        if($mReturnData[0] == "true") {
          
          //Defino el nombre del archivo .pdf
          $cNomFile = "pdf_" . $kUser . "_" . date("YmdHis") . ".pdf";
          $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;

          if (file_exists($cFile)) {
            unlink($cFile);
          }

          //Decodifico la respuesta que llega en base64
          $vData = json_decode($mReturnData[2], true);

          foreach ($vData as $_data) {
            //Decodifico la repuesta base64 en un PDF
            $cArcvhivo =  base64_decode($_data['pdf_notificacion']);
          }

          $fOp = fopen($cFile, 'a');
          fwrite($fOp, $cArcvhivo);

          chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));

          // Obtener la ruta absoluta del archivo
          $cAbsolutePath = realpath($cFile);
          $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

          if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
            $cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFile);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $cDownLoadFilename);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($cFile));

            ob_clean();
            flush();
            readfile($cFile);

            //Descargo el archivo PDF
            echo "<html><script>document.location=frfacdow??cRuta=".$cFile."</script></html>";
          }

        } else {
          $nSwitch = 1;
          $cMsj = "Se Presentaron los siguientes errores al descargar el documento de openETL:\n\n";

          ## Recorro los errores generados ##
          for ($nRT=3; $nRT<count($mReturnData); $nRT++) {
            $cMsj .= $mReturnData[$nRT]."\n";
          }
        }
      break;
      case "REENVIARCORREO":
        $vParametros['database'] = $cAlfa;               //Base de datos.
        $vParametros['sysstrxx'] = $vSysStr;             //Variables del sistema.
        $vParametros['comidxxx'] = $_POST['cComId'];     //Id del documento
        $vParametros['comcodxx'] = $_POST['cComCod'];    //Codigo del documento
        $vParametros['comcscxx'] = $_POST['cComCsc'];    //Consecutivo del documento
        $vParametros['comcsc2x'] = $_POST['cComCsc2'];   //Consecutivo 2 del documento
        $vParametros['comfecxx'] = $_POST['dRegFCre'];   //Fecha del documento
        $vParametros['resprexx'] = $vDocCon['resprexx']; //Prefijo Comprobante

        /*** array para el envío de datos al método de Movimiento ***/
        $vDatos = array();
        $vDatos['cTipo']    = "1";                // Tipo de impresión, por pdf o excel
        $vDatos['cGenerar'] = "FACTURADO";        // opción para impresión: facturado y/o no facturado
        $vDatos['cIntPag']  = "NO";               // Intermediación de Pagos
        $vDatos['cTerId']   = "";                 // Tercero
        $vDatos['dFecDes']  = $_POST['dRegFCre']; // Fecha desde
        $vDatos['dFecHas']  = $_POST['dRegFCre']; // Fecha Hasta
        $vDatos['cComId']   = $_POST['cComId'];   // Id del comprobante
        $vDatos['cComCod']  = $_POST['cComCod'];  // Código del comprobante
        $vDatos['cComCsc']  = $_POST['cComCsc'];  // Consecutivo Uno del Comprobante
        $vDatos['cComCsc2'] = $_POST['cComCsc2']; // Consecutivo Dos del Comprobante

        /*** Se instancia la clase cMovimientoDo del utility utimovdo.php ***/
        $ObjMovimiento = new cMovimientoDo();

        /*** se envían todos los datos necesarios al método fnPagosaTerceros ***/
        $mDatos  = array();
        $mReturnPagTer = $ObjMovimiento->fnPagosaTerceros($vDatos);
      
        if(count($mReturnPagTer[1]) > 0) {
          $vParamsCertificado = array();
          $vParamsCertificado['DATOSXXX'] = $mReturnPagTer[1];
          $vParamsCertificado['RESDATXX'] = $mReturnPagTer[2];
          $vParamsCertificado['RESIDXXX'] = $mReturnPagTer[3];
          $vParamsCertificado['COCDATXX'] = $mReturnPagTer[4];
          $vParamsCertificado['TIPOXXXX'] = "CERTIFICADOPCC";
      
          $objCerMan    = new cCertificadoMandato();
          $mCertificado = $objCerMan->fnGenerarCertificadoMandato($vParamsCertificado);

          if($mCertificado[0] == "true") {
            $cRutCerMan = $mCertificado[1];
            $vParametros['certific'] = base64_encode(file_get_contents($cRutCerMan));
          }else if($mCertificado[0] == "false") {
            $nSwitch = 1;
            for($nCPCC = 1; $nCPCC < count($mCertificado); $nCPCC++) {
              $cMsj .= "Documento [{$xRMC['comidxxx']}-{$xRMC['comcodxx']}-{$xRMC['comcscxx']}-{$xRMC['comcsc2x']}]: ".$mCertificado[$nCPCC]."\n";
            }
          }
        }//if(count($mDatos) > 0) {

        $cIntegracion = new cIntegracionopenETL();
        $mReturnData  = $cIntegracion->fnReenviarCorreo($vParametros);

        // Valida si se presentaron errores
        if($mReturnData[2] != "") {
          $respuesta = json_decode($mReturnData[2], true);

          if (array_key_exists("errors", $respuesta) && count($respuesta['errors']) > 0) {
            $nSwitch = 1;
            $cMsj = "Se Presentaron los siguientes errores al reenviar la notificacion del documento en openETL:\n\n";
          
            ## Recorro los errores generados ##
            for ($nRT=0; $nRT<count($respuesta['errors']); $nRT++) {
              $cMsj .= utf8_decode($respuesta['errors'][$nRT])."\n";
            }
          }
        }

        if($mReturnData[3] != "") {
          $nSwitch = 1;
          $cMsj .= $mReturnData[3]."\n";
        }

        if ($nSwitch == 0) {
          $cMsj .= "El reenvio de la notificacion del documento en openETL se realizo con exito";
        }
      break;
    }
  }

  if ($nSwitch == 0){
    f_Mensaje(__FILE__,__LINE__, $cMsj);
  ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      document.forms['frgrm'].submit();
    </script>
  <?php
  }else{
    f_Mensaje(__FILE__,__LINE__,$cMsj);
  }
?>

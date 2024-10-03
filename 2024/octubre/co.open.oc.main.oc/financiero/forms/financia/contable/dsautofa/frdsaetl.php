<?php
  namespace openComex;
  /**
   * Transmitir Documento a OpenETL
   * @author Johana Arboleda Ramos <johana.arboleda@openits.co>
   * @package openComex
   * @version 001
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  include("../../../../../config/config.php");
  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/uticidsa.php");

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
    case "TRANSMITIROPENETL":
      //Transmision openETL
      if ($vSysStr['system_activar_openetl_ds'] == "SI") {

        $vParametros['database'] = $cAlfa;              //Base de datos.
        $vParametros['sysstrxx'] = $vSysStr;            //Variables del sistema.
        $vParametros['comidxxx'] = $_POST['cComId'];    //Id del documento
        $vParametros['comcodxx'] = $_POST['cComCod'];   //Codigo del documento
        $vParametros['comcscxx'] = $_POST['cComCsc'];   //Consecutivo del documento
        $vParametros['comcsc2x'] = $_POST['cComCsc2'];  //Consecutivo 2 del documento
        $vParametros['comfecxx'] = $_POST['dRegFCre'];  //Fecha del documento
        $vParametros['conexion'] = $xConexion01;        //Conexion BD
        $vParametros['origenxx'] = "TRACKING";          //Origen

        $objFeVp = new cIntegracionDsopenETL();
        $mReturnData = $objFeVp->fnRegistrarDocumentosOpenETL($vParametros);

        if($mReturnData[0] == "false") {
          $cMsj = "Se Presentaron los siguientes errores al transmitir el documento a openETL:\n\n";
        }

        for ($nRT=2; $nRT<count($mReturnData); $nRT++) {
          $cMsj .= $mReturnData[$nRT]."\n";
        }

      }
    break;
    case "CONSULTAROPENETL":
      /* Realizo la validacion de que el documento ya haya sido enviado a openETL */
      //Buscando los datos del comprobante en cabecera
      $qDocCon = "SELECT fdsc$cPerAno.comptesn, fdsc$cPerAno.comptevx, fdsc$cPerAno.resprexx, fdsc$cPerAno.comobs2x, fdsc$cPerAno.regestxx ";
      $qDocCon .= "FROM $cAlfa.fdsc$cPerAno ";
      $qDocCon .= "WHERE ";
      $qDocCon .= "fdsc$cPerAno.comidxxx = \"{$_POST['cComId']}\"  AND ";
      $qDocCon .= "fdsc$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";
      $qDocCon .= "fdsc$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";
      $qDocCon .= "fdsc$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\"";
      $xDocCon = f_MySql("SELECT", "", $qDocCon, $xConexion01, "");
      $vDocCon = mysql_fetch_array($xDocCon);
      //f_Mensaje(__FILE__,__LINE__,$qDocCon."~".mysql_num_rows($xDocCon));

      if(!($vDocCon["comptesn"] != "" && $vDocCon["comptevx"] == "VP")) {
        $nSwitch = 1;
        $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] no Puede ser Consultado, este no ha sido enviado a openETL.\n";
      }
    break;
    case "DESCARGARPDFOPENETL":
      /* Realizo la validacion de que el documento ya haya sido enviado a openETL */
      //Buscando los datos del comprobante en cabecera
      $qDocCon  = "SELECT ";
      $qDocCon .= "fdsc$cPerAno.comptesn, ";
      $qDocCon .= "fdsc$cPerAno.comptevx, ";
      $qDocCon .= "fdsc$cPerAno.comptere ";
      $qDocCon .= "FROM $cAlfa.fdsc$cPerAno ";
      $qDocCon .= "WHERE ";
      $qDocCon .= "fdsc$cPerAno.comidxxx = \"{$_POST['cComId']}\"  AND ";
      $qDocCon .= "fdsc$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";
      $qDocCon .= "fdsc$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";
      $qDocCon .= "fdsc$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\" ";
      $xDocCon  = f_MySql("SELECT", "", $qDocCon, $xConexion01, "");
      $vDocCon  = mysql_fetch_array($xDocCon);
      //f_Mensaje(__FILE__,__LINE__,$qDocCon."~".mysql_num_rows($xDocCon));

      if(!($vDocCon["comptesn"] != "" && $vDocCon["comptevx"] == "VP")) {
        $nSwitch = 1;
        $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] no Puede ser Descargado, este no ha sido enviado a openETL.\n";
      }

      if($vDocCon["comptere"] == "FALLIDO"){
        $nSwitch = 1;
        $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}] no Puede ser Descargado, tiene estado FALLIDO en openETL.\n";
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

        $objFeVp = new cIntegracionDsopenETL();
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
      case "DESCARGARPDFOPENETL":
        $vParametros['database'] = $cAlfa;              //Base de datos.
        $vParametros['sysstrxx'] = $vSysStr;            //Variables del sistema.
        $vParametros['resultxx'] = "base64";            //Resultado de la peticion.
        $vParametros['comidxxx'] = $_POST['cComId'];    //Id del documento
        $vParametros['comcodxx'] = $_POST['cComCod'];   //Codigo del documento
        $vParametros['comcscxx'] = $_POST['cComCsc'];   //Consecutivo del documento
        $vParametros['comcsc2x'] = $_POST['cComCsc2'];  //Consecutivo 2 del documento
        $vParametros['comfecxx'] = $_POST['dRegFCre'];  //Fecha del documento
        $vParametros['conexion'] = $xConexion01;        //Conexion BD

        $objFeVp = new cIntegracionDsopenETL();
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
            $cArcvhivo =  base64_decode($_data['pdf']);
          }

          $fOp = fopen($cFile, 'a');
          fwrite($fOp, $cArcvhivo);

           // Obtener la ruta absoluta del archivo
          $cAbsolutePath = realpath($cFile);
          $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

          if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
            chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
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

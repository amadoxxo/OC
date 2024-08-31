<?php
/**
  * Cargar Anexos.
  * --- Descripcion: Envio de Archivos y Creacion de Carpetas al Cargar Anexos de un registro M.I.F
  * @author Elian Amado. elian.amado@openits.co
  * @package opencomex
  * @version 001
  */
  include("../../../../../financiero/libs/php/utility.php");
  include("../../../../libs/php/utigesdo.php");
  include('../../../../../libs/php/uticecmx.php');

  $nSwitch = 0; // Switch para Vericar la Validacion de Datos
  $cMsj = "";

  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");

  switch ($_COOKIE['kModo']) {
    // Validaciones
    case "CARGARANEXOS":
      $mTipDocId = array();
      for ($i=1;$i<=$_POST['nSecuencia'];$i++) {
        if ($_POST['cTdoIdEcm'.$i] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Tipo Documental es requerido. \n";
          break;
        }
        $mTipDocId[] = $_POST['cTdoIdEcm'.$i];
      }

      $dFechaCag = explode('-', $_POST['dFechaCag']);
      $nAnio = $dFechaCag[0];
      $nMes  = $dFechaCag[1];
      $nDia  = $dFechaCag[2];

      $dHoraCag = explode(':', $_POST['cRegHCre']);
      $nHora     = $dHoraCag[0];
      $nMinutos  = $dHoraCag[1];
      $nSegundos = $dHoraCag[2];
    
      $cRuta = "{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa/$nAnio/$nMes/$nDia/$nHora/$nMinutos/$nSegundos/{$_POST['cOrigen']}/{$_POST['nCagId']}";
    
      if (!is_dir($cRuta)) {
        if (mkdir($cRuta, 0777, true)) {
          chmod($cRuta, intval($vSysStr['system_permisos_directorios'], 8));
        }
      }

      // Validar que todos los archivos tengan un nombre no vacío antes de procesarlos
      foreach ($_FILES as $file) {
        $cNombreArchivo = basename($file['name']);
        if ($cNombreArchivo == "") {
            $nSwitch = 1;
            $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
            $cMsj .= "Todas las filas deben cargar un documento. \n";
            break;
        }
      }

      $mDocumentos = array();
      // Si no hubo errores en la validación, proceder a mover los archivos
      if ($nSwitch == 0) {
        $nCount = 0; // Contador para $mTipDocId
        foreach($_FILES as $file) {
          // Obtener el nombre del archivo
          $cNombreArchivo = basename($file['name']);
          // Establecer la ruta de destino para el archivo
          $cDestinoArchivo = $cRuta . "/" . $cNombreArchivo;

          // Mover el archivo subido a la carpeta dinámica
          if (move_uploaded_file($file['tmp_name'], $cDestinoArchivo)) {
            /** SE SUBIO EL ARCHIVO CORRECTAMENTE */
            $mDocumentos[count($mDocumentos)] = [
              'tdoidecm' => $mTipDocId[$nCount], //Id Tipos Documentales
              'rutaxxxx' => $cRuta,              //Ruta del Archivo
              'nomfilex' => $cNombreArchivo,     //Nombre del archivo
            ];
          } else {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Hubo un error al subir el archivo. \n";
            break;
          }
          $nCount++;
        }
      }

      /** 
       * Almacena los parametros para enviar a la API.
       */
      $vParametros = array();
      $vParametros['idcompro'] = $_POST['nCagId']; //Id del registro
      $vParametros['anioxxxx'] = $nAnio;           //Año del registro
      $vParametros['procesox'] = $_POST['cOrigen'];//Origen del registro a la que esta consultando
      $vParametros['datos']    = $mDocumentos;     //Archivos Anexados

      echo '<pre>'; 
      print_r($vParametros);

      /**
       * Envio de parametros al metodo del utility donde se hace la conexion a openECM.
       */
      $objIntegracion = new cIntegracionGestorDocumentalopenECM();
      $mReturnRadicarDocumentos = $objIntegracion->fnRadicarDocumentosAnexos($vParametros);
      echo '<pre>'; 
      print_r($mReturnRadicarDocumentos);

      if ($mReturnRadicarDocumentos[0] == 'false') {
        $nSwitch = 1;
        for ($n=2; $n < count($mReturnRadicarDocumentos); $n++) {
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= $mReturnRadicarDocumentos[$n] . "\n";
        }
      }

    break;
  }


  if ($nSwitch == 1) {
    f_Mensaje(__FILE__,__LINE__,"$cMsj Verifique.");
  }

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "CARGARANEXOS":
        f_Mensaje(__FILE__,__LINE__,"Los Archivos se han Subido con Exito.\n");
      ?>
        <form name="frgrm" method="post" target="fmwork"></form>
        <script language="javascript">
          var cOrigen = "<?php echo $_POST['cOrigen'] ?>";

          if (cOrigen == "CERTIFICACION") {
            document.forms['frgrm'].action = "../certifix/frcerini.php";
          } else if (cOrigen == "PEDIDO") {
            document.forms['frgrm'].action = "../pedidoxx/frpedini.php";
          } else {
            document.forms['frgrm'].action = "<?php echo $_COOKIE['kIniAnt'] ?>";
          }

          parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
          document.forms['frgrm'].submit();
        </script>
      <?php
      break;
    }
  }
?>
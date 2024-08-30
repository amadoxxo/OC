<?php
/**
  * Cargar Anexos.
  * --- Descripcion: Envio de Archivos y Creacion de Carpetas al Cargar Anexos de un registro M.I.F
  * @author Elian Amado. elian.amado@openits.co
  * @package opencomex
  * @version 001
  */
  include("../../../../../financiero/libs/php/utility.php");

  $nSwitch = 0; // Switch para Vericar la Validacion de Datos
  $cMsj = "";

  switch ($_COOKIE['kModo']) {
    // Validaciones
    case "CARGARANEXOS":

      $mTipDocId   = array();
      $mTipDocDesc = array();

      for ($i=1;$i<=$_POST['nSecuencia'];$i++) { 
        if ($_POST['sTipDocu'.$i] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Tipo Documental es requerido. \n";
          break;
        }
        $mTipDocId[] = $_POST['sTipDocu'.$i];

        // Consulta para obtener la descripción
        $qTipDoc  = "SELECT tdodesxx ";
        $qTipDoc .= "FROM $cAlfa.lpar0162 ";
        $qTipDoc .= "WHERE tdoidxxx = \"{$_POST['sTipDocu'.$i]}\" AND ";
        $qTipDoc .= "tdogruxx = \"$cOrigen\" AND ";
        $qTipDoc .= "regestxx = \"ACTIVO\";";
        $xTipDoc  = f_MySql("SELECT", "", $qTipDoc, $xConexion01, "");
        // f_Mensaje(__FILE__,__LINE__,$qTipDoc."~".mysql_num_rows($xTipDoc));
        // echo $qTipDoc."~".mysql_num_rows($xTipDoc);
        if (mysql_num_rows($xTipDoc) > 0) {
          $vTipDoc  = mysql_fetch_array($xTipDoc);
          $mTipDocDesc[] = $vTipDoc['tdodesxx'];
        }
      }

      var_dump($mTipDocId);
      var_dump($mTipDocDesc);

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
    
      foreach($_FILES as $file) {
        // Obtener el nombre del archivo
        $cNombreArchivo = basename($file['name']);
    
        // Establecer la ruta de destino para el archivo
        $cDestinoArchivo = $cRuta . "/" . $cNombreArchivo;
        
        // Mover el archivo subido a la carpeta dinámica
        if (move_uploaded_file($file['tmp_name'], $cDestinoArchivo)) {
          /** SE SUBIO EL ARCHIVO CORRECTAMENTE */
        } elseif ($cNombreArchivo == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Todas las filas deben cargar un documento. \n";
          break;
        } else {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Hubo un error al subir el archivo. \n";
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
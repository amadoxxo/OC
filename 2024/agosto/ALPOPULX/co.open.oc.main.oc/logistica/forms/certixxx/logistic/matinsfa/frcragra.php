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
    case "CARGARANEXOS":

      $dFechaMif = explode('-', $_POST['dFechaMif']);
      $nAnio = $dFechaMif[0];
      $nMes  = $dFechaMif[1];
      $nDia  = $dFechaMif[2];
    
      // Consulta para obtener la hora de creacion del registro
      $qMif  = "SELECT reghcrex ";
      $qMif .= "FROM $cAlfa.lmca$nAnio ";
      $qMif .= "WHERE mifidxxx = \"{$_POST['nMifId']}\" ";
      $xMif  = f_MySql("SELECT", "", $qMif, $xConexion01, "");
      // f_Mensaje(__FILE__,__LINE__,$qMif."~".mysql_num_rows($xMif));
      // echo $qMif."~".mysql_num_rows($xMif);
      if (mysql_num_rows($xMif) > 0) {
        $vMif     = mysql_fetch_array($xMif);
        $dHoraMif = explode(':', $vMif['reghcrex']);
        
        $nHora     = $dHoraMif[0];
        $nMinutos  = $dHoraMif[1];
        $nSegundos = $dHoraMif[2];
      }
    
      $cRuta = "{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa/$nAnio/$nMes/$nDia/$nHora/$nMinutos/$nSegundos";
    
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
        <form name="frgrm" action="<?php echo $_COOKIE['kIniAnt'] ?>" method="post" target="fmwork"></form>
        <script languaje="javascript">
          parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
          document.forms['frgrm'].submit();
        </script>
      <?php
      break;
    }
  }
?>
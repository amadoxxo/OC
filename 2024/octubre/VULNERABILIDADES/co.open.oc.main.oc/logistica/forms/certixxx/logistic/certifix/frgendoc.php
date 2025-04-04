<?php
  namespace openComex;
  /**
   * Descargar Archivo Certificacion.
   * --- Descripcion: Descargar Archivo Excel. 
   * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @version 001
   * @package opencomex
   */

  include("../../../../../financiero/libs/php/utility.php");

  $cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cRuta;
  if (file_exists($cRuta)) {
    // Obtener la ruta absoluta del archivo
    $cAbsolutePath = realpath($cRuta);
    $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

    if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
      $cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cRuta);
  
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename=' . $cDownLoadFilename);
      header('Content-Transfer-Encoding: binary');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
      header('Content-Length: ' . filesize($cRuta));
  
      ob_clean();
      flush();
      readfile($cRuta);
      exit;
    }
  }
?>

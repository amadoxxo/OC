<?php
  namespace openComex;
  /**
   * Descargar Archivo Reporte Certificaciones.
   * --- Descripcion: Descargar Archivo Excel. 
   * @author Elian Amado. <elian.amado@openits.co>
   * @version 001
   * @package opencomex
   */

  include("../../../../../financiero/libs/php/utility.php");

  $cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cRuta;
  if (file_exists($cRuta)) {
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
?>

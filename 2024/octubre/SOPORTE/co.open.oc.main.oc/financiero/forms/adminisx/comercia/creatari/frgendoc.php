<?php
/**
 * Descargar Archivo.
 * --- Descripcion: Descargar Reporte
 * @author Johana Arboleda Ramos <johana.arboleda@openits>
 * @version 001
 * @package opencomex
 */
	include("../../../../libs/php/utility.php");

	$cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cRuta;
  if (file_exists($cRuta)) {
  	$cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cRuta);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' .'REPORTE_TARIFAS_CONSOLIDADO_'. $cDownLoadFilename);
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

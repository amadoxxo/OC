<?php
  /**
   * Descargar Archivo.
   * --- Descripcion: Descargar Reporte Excel
   * @author Cesar Cadena <cesar.cadena@open-eb.co>
   * @version 001
   * @package opencomex
   */

  include("../../../../libs/php/utility.php");
  
  if($gBg == "SI"){
    $cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . "propios/" . $cAlfa . "/estado_cuenta" . "/" . $cRuta;
  }else{
    $cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cRuta;
  }

  if (file_exists($cRuta)) {
    // Obtener la ruta absoluta del archivo
    $cAbsolutePath = realpath($cRuta);
    $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));
    
    /* 
      Esta lógica valida si la ruta absoluta comienza con alguna de las rutas autorizadas en el array,
      permitiendo que cualquier subdirectorio dentro de una ruta base permitida (Ej: /var/www/html/desarrollo/opencomex/propios/GRUMALCO/estado_cuenta) para que
      sea considerado valido para descargar un archivo.
     */
    $nEncontro = 0;
    foreach ($vSystem_Path_Authorized as $cAuthorizedPath) {
      if (strpos(realpath($cAbsolutePath), $cAuthorizedPath) === 0) {
        $nEncontro = 1;
        break;
      }
    }

    if ($nEncontro == 1) {
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
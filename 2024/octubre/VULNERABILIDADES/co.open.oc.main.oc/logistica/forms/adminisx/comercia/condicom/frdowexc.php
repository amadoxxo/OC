<?php
  namespace openComex;
 /**
   * Interface Condiciones Comerciales.
   * --- Descripcion: Interface para cargar por las Condiciones Comerciales.
   * @author Diego Fernando Cortes Rojas <diego.cortes@openits.co>
   * @version 001
   * @package opencomex
   */

  include("../../../../../financiero/libs/php/utility.php");

  $cNomFile = "CargueCondicionesComerciales_".$_COOKIE['kUsrId']."_".date("YmdHis").".xls";
  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;
  if (file_exists($cFile)){
    unlink($cFile);
  }

  $fOp = fopen($cFile,'a');

  $cCad01  = "No. OFERTA COMERCIAL\t";
  $cCad01 .= "NIT\t";
  $cCad01 .= "TIPO OFERTA COMERCIAL\t";
  $cCad01 .= "DIA CIERRE DE FACTURACION\t";
  $cCad01 .= "FECHA VIGENCIA DESDE\t";
  $cCad01 .= "FECHA VIGENCIA HASTA\t";
  $cCad01 .= "TIPO DE INCREMENTO\t";
  $cCad01 .= "ESPECIFIQUE TIPO INCREMENTO OTRO\t";
  $cCad01 .= "OBSERVACIONES";
  $cCad01 .= "\n";

  fwrite($fOp,$cCad01);
  fclose($fOp);

  if (file_exists($cFile)){
    // Obtener la ruta absoluta del archivo
    $cAbsolutePath = realpath($cFile);
    $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

    if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
      chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
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
      exit;
    }
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }
?>
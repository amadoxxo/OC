<?php
  namespace openComex;
include("../../../../../financiero/libs/php/utility.php");

$cNomFile = "CargueClientes_".$_COOKIE['kUsrId']."_".date("YmdHis").".xls";
$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;
if (file_exists($cFile)){
  unlink($cFile);
}

$fOp = fopen($cFile,'a');

$cCad01  = "NIT\t";
$cCad01 .= "CODIGO TIPO DE DOCUMENTO\t";
$cCad01 .= "TIPO DE PERSONA (PUBLICA - JURIDICA - NATURAL)\t";
$cCad01 .= "RAZON SOCIAL\t";
$cCad01 .= "NOMBRE COMERCIAL\t";
$cCad01 .= "PRIMER APELLIDO\t";
$cCad01 .= "SEGUNDO APELLIDO\t";
$cCad01 .= "PRIMER NOMBRE\t";
$cCad01 .= "OTROS NOMBRES\t";
$cCad01 .= "REQUIERE PREFACTURA (SI - NO)\t";
$cCad01 .= "CODIGO PAIS DOMICILIO FISCAL\t";
$cCad01 .= "CODIGO DEPARTAMENTO DOMICILIO FISCAL\t";
$cCad01 .= "CODIGO CIUDAD DOMICILIO FISCAL\t";
$cCad01 .= "DIRECCION DOMICILIO FISCAL\t";
$cCad01 .= "CODIGO POSTAL\t";
$cCad01 .= "TELEFONO\t";
$cCad01 .= "TELEFONO MOVIL\t";
$cCad01 .= "CORREO FACTURACION ELECTRONICA\t";
$cCad01 .= "APARTADO AEREO\t";
$cCad01 .= "CLIENTE (SI - NO)\t";
$cCad01 .= "USUARIO (SI - NO)\t";
$cCad01 .= "USUARIO DIAN (SI - NO)\t";
$cCad01 .= "EMPLEADO (SI - NO)\t";
$cCad01 .= "CONTACTO (SI - NO)\t";
$cCad01 .= "OTRO (SI - NO)\t";
$cCad01 .= "CODIGO SAP\t";
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

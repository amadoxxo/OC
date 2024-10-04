<?php
/**
 * Genera el archivo Excel para cargas de CODIGO CEBE
 * @package opencomex
 * @author oscar.perez@openits.co
 * 
 * Variables:
 * @var string  $cNomFile     Nombre del documento.
 * @var file    $cFile     Ruta del archivo.
 * @var file    $fOp      Objeto con archivo.
 * @var string  $cCeb01   Cadena con tabla para excel.
 * @var string  $qCeb     Consulta a fpar0115 para obtener valores del PUC.
 * @var mixed   $xCeb     Cursor respuesta de la consulta $qCeb.
 * @var array   $xRC      Arreglo con información del cursor $xCeb.
 */

# Limite de tiempo
//set_time_limit(0);

# Librerias
include("../../../../../financiero/libs/php/utility.php");

$cNomFile = "CargueCebe_".$_COOKIE['kUsrId']."_".date("YmdHis").".xls";
$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;
if (file_exists($cFile)){
	unlink($cFile);
}

$fOp = fopen($cFile,'a');

$cCeb01  = "<table border=\"1\">";
  $cCeb01 .= "<tr>";
    $cCeb01 .= "<td style=\"mso-number-format:\@\">ID</td>";
    $cCeb01 .= "<td style=\"mso-number-format:\@\">PLATAFORMA</td>";
    $cCeb01 .= "<td style=\"mso-number-format:\@\">COD SAP</td>";
    $cCeb01 .= "<td style=\"mso-number-format:\@\">SECTOR</td>";
    $cCeb01 .= "<td style=\"mso-number-format:\@\">COD CEBE</td>";
    $cCeb01 .= "<td style=\"mso-number-format:\@\">DESCRIPCION</td>";
    $cCeb01 .= "<td style=\"mso-number-format:\@\">MUNICIPIO</td>";
  $cCeb01 .= "</tr>";

fwrite($fOp,$cCeb01);   

switch ($cTipo) {
	case "1": //cebe
		$qCeb  = "SELECT * ";
		$qCeb .= "FROM $cAlfa.lpar0010 ";
		$qCeb .= "WHERE  ";
		$qCeb .= "regestxx = \"ACTIVO\" ";
		$xCeb = f_MySql("SELECT","",$qCeb,$xConexion01,"");
		if (mysql_num_rows($xCeb) > 0) {
			while ($xRC = mysql_fetch_array($xCeb)) {

        $qSec  = "SELECT secdesxx ";
		    $qSec .= "FROM $cAlfa.lpar0009 ";
		    $qSec .= "WHERE  ";
        $qSec .= "secsapxx = \"{$xRC['secsapxx']}\" ";
        $xSec = f_MySql("SELECT","",$qSec,$xConexion01,"");
        $vSec = mysql_fetch_array($xSec);

			  $cCeb01 = "<tr>";
          $cCeb01 .= "<td style=\"mso-number-format:\@\">{$xRC['cebidxxx']}</td>"; //Id Cod Cebe
          $cCeb01 .= "<td style=\"mso-number-format:\@\">{$xRC['cebplaxx']}</td>"; //Plataforma
          $cCeb01 .= "<td style=\"mso-number-format:\@\">{$xRC['secsapxx']}</td>"; //COD SAP
          $cCeb01 .= "<td style=\"mso-number-format:\@\">{$vSec['secdesxx']}</td>"; //Sector
          $cCeb01 .= "<td style=\"mso-number-format:\0\">{$xRC['cebcodxx']}</td>"; //COD CEBE
          $cCeb01 .= "<td style=\"mso-number-format:\@\">{$xRC['cebdesxx']}</td>"; //Descripcion del Cod Cebe
          $cCeb01 .= "<td style=\"mso-number-format:\@\">{$xRC['cebmunxx']}</td>"; //Municipio
        $cCeb01 .= "</tr>";
        fwrite($fOp,$cCeb01);
			}
		}
	break;
	default:
		//no hace nada	
	break;
}
$cCeb01 = "</table>";
fwrite($fOp,$cCeb01);
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

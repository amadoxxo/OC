<?php
/**
 * Genera el archivo Excel para cargas de PUC
 * @package opencomex
 * @todo NA
 * 
 * Variables:
 * @var string  $cNomFile     Nombre del documento.
 * @var file    $cFile     Ruta del archivo.
 * @var file    $fOp      Objeto con archivo.
 * @var string  $cCad01   Cadena con tabla para excel.
 * @var string  $qPuc     Consulta a fpar0115 para obtener valores del PUC.
 * @var mixed   $xPuc     Cursor respuesta de la consulta $qPuc.
 * @var array   $xRP      Arreglo con información del cursor $xPuc.
 */

# Limite de tiempo
//set_time_limit(0);

# Librerias
include("../../../../libs/php/utility.php");

$cNomFile = "CargueTerceros_".$_COOKIE['kUsrId']."_".date("YmdHis").".xls";
$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;
if (file_exists($cFile)){
	unlink($cFile);
}

$fOp = fopen($cFile,'a');

$cCad01  = "<table border=\"1\">";
  $cCad01 .= "<tr>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">GRUPO DE LA CUENTA</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">ID DE LA CUENTA</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">SUB-CUENTA</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">AUXILIAR</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">SUB-AUXILIAR</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">DESCRIPCION DE LA CUENTA</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">ES UNA CUENTA DE ACTIVOS? (N,S)</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">ES UNA CUENTA DEL DISPONIBLE? (N,S)</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">TIPO DE DETALLE DE LA CUENTA (P,N,C,D)</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">ES UNA CUENTA DE TERCEROS? (N,S,R)</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">BASE DE RETENCION</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">PORCENTAJE DE RETENCON DE LA CUENTA</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">ES UNA CUENTA DE CENTRO DE COSTOS? (N,S)</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">ES UNA CUENTA AJUSTABLE? (N,A,D)</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">LA CUENTA TIENE MANEJO EN MONEDA EXTRANJERA? (N,S)</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">LA CUENTA SE AJUSTA EN MONEDA EXTRANJERA? (N,S)</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">TIPO CUENTA? (P,A,T,I,E,O,C)</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">NATURALEZA DE LA CUENTA? (D,C)</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">CARACTERISTICA DE LA CUENTA (N,G,E,I,M,X,T)</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">CUAL ES LA CUENTA ALTERNA</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">OBLIGA DO EN EL SUBCENTRO DE COSTO (N,S)</td>";
    $cCad01 .= "<td style=\"mso-number-format:\@\">TIPO DE EJECUCION (L-LOCAL, N-NIIF, VACIO-AMBAS)</td>";
  $cCad01 .= "</tr>";

fwrite($fOp,$cCad01);   

switch ($cTipo) {
	case "1": //terceros
		$qPuc  = "SELECT * ";
		$qPuc .= "FROM $cAlfa.fpar0115 ";
		$qPuc .= "WHERE  ";
		$qPuc .= "regestxx = \"ACTIVO\" ";
		$xPuc = f_MySql("SELECT","",$qPuc,$xConexion01,"");
		if (mysql_num_rows($xPuc) > 0) {
			while ($xRP = mysql_fetch_array($xPuc)) {
			  $cCad01 = "<tr>";
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucgruxx']}</td>"; //Grupo de la Cuenta
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucctaxx']}</td>"; //Id de la Cuenta
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucsctax']}</td>"; //Sub-Cuenta
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucauxxx']}</td>"; //Auxiliar
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucsauxx']}</td>"; //Sub-Auxiliar
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucdesxx']}</td>"; //Descripcion de la Cuenta
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucactxx']}</td>"; //Es una Cuenta de Activos?
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucdisxx']}</td>"; //Es una Cuenta del Disponible?
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucdetxx']}</td>"; //Tipo de Detalle de la Cuenta
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucterxx']}</td>"; //Es una Cuenta de Terceros?
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucbaret']}</td>"; //Base de Retención
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucretxx']}</td>"; //Porcentaje Retencion de la Cuenta
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['puccccxx']}</td>"; //Es una Cuenta de Centro Costos?
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucajuxx']}</td>"; //Es una Cuenta Ajustable?
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucmexxx']}</td>"; //La Cuenta Tiene Manejo en Moneda Extranjera?
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucajuex']}</td>"; //La Cuenta se Ajusta en Moneda Extranjera?
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['puctcuxx']}</td>"; //Tipo Cuenta?
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucnatxx']}</td>"; //Naturaleza de la Cuenta?
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['puccctxx']}</td>"; //Caracteristica de la Cuenta
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucctaal']}</td>"; //Cual es la Cuenta Alterna
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['pucdoscc']}</td>"; //Indica si la Cuenta Obliga DO en el Subcentro de Costo
          $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRP['puctipej']}</td>"; //Tipo de Ejecucion(L-Local,N-Niif,vacio-Ambas)
        $cCad01 .= "</tr>";
        fwrite($fOp,$cCad01);
			}
		}
	break;
	default:
		//no hace nada	
	break;
}
$cCad01 = "</table>";
fwrite($fOp,$cCad01);
fclose($fOp);

if (file_exists($cFile)){
  chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));

  // Obtener la ruta absoluta del archivo
  $cAbsolutePath = realpath($cFile);
  $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

  if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
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

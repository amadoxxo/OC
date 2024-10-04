<?php
  //set_time_limit(0);
	include("../../../../../financiero/libs/php/utility.php");

	$cNomFile = "ReporteClientes_".$_COOKIE['kUsrId']."_".date("YmdHis").".xls";
  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;
  if (file_exists($cFile)){
    unlink($cFile);
  }

  $fOp = fopen($cFile,'a');

  $gBuscar = ($gBuscar == "") ? "OR" : $gBuscar;

	$cCad01  = "<table border=\"1\">";
  $cCad01  .= "<tr>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">NIT</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">CODIGO TIPO DE DOCUMENTO</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">TIPO DE PERSONA (PUBLICA - JURIDICA - NATURAL)</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">RAZON SOCIAL</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">NOMBRE COMERCIAL</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">PRIMER APELLIDO</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">SEGUNDO APELLIDO</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">PRIMER NOMBRE</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">OTROS NOMBRES</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">REQUIERE PREFACTURA (SI - NO)</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">CODIGO PAIS DOMICILIO FISCAL</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">CODIGO DEPARTAMENTO DOMICILIO FISCAL</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">CODIGO CIUDAD DOMICILIO FISCAL</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">DIRECCION DOMICILIO FISCAL</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">CODIGO POSTAL</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">TELEFONO</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">TELEFONO MOVIL</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">CORREO FACTURACION ELECTRONICA</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">APARTADO AEREO</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">CLIENTE (SI - NO)</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">USUARIO (SI - NO)</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">USUARIO DIAN (SI - NO)</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">EMPLEADO (SI - NO)</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">CONTACTO (SI - NO)</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">OTRO (SI - NO)</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">CODIGO SAP</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:#2ce404;text-align: center\">OBSERVACIONES</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:red;text-align: center\">FECHA DE CREACION</td>";
  $cCad01 .= "<td style=\"font-weight:bold;background-color:red\">ESTADO</td>";
  $cCad01  .= "</tr>";

	fwrite($fOp,$cCad01);

  /**
   * Armando condiciones del query
   * la unica condicion obligatoria es la clasificacion y el estado
   */
  $qSqlTip = "";

  if( $gChCli == "SI" ){
    $qSqlTip .= "cliclixx = \"SI\" $gBuscar ";
  }

  if( $gChUsu == "SI" ){
    $qSqlTip .= "cliusuxx = \"SI\" $gBuscar ";
  }

  if( $gChCont == "SI" ){
    $qSqlTip .= "cliconxx = \"SI\" $gBuscar ";
  }

  if( $gChDian == "SI" ){
    $qSqlTip .= "clidianx = \"SI\" $gBuscar ";
  }

  if( $gChEmp == "SI" ){
    $qSqlTip .= "cliempxx = \"SI\" $gBuscar ";
  }

  if( $gChOtro == "SI" ){
    $qSqlTip .= "cliotrxx = \"SI\" $gBuscar ";
  }

  $qSqlAux = "";

  if ($gTpeId != ""){
    $qSqlAux .= "clitperx = \"$gTpeId\" $gBuscar ";
  }

  if ($gTdiId != ""){
    $qSqlAux .= "AND tdiidxxx = \"$gTdiId\" $gBuscar ";
  }

  if ($gCliId != ""){
    if ($gExcCliId == "SI"){
      $qSqlAux .="cliidxxx = \"$gCliId\" $gBuscar ";
    } else{
      $qSqlAux .="cliidxxx LIKE \"%$gCliId%\" $gBuscar ";
    }
  }

  if ($gCliNom != ""){
    if ($gExcTerNom == "SI"){
      $qSqlAux .="clinomxx = \"$gCliNom\" $gBuscar ";
    } else{
      $qSqlAux .="clinomxx LIKE \"%$gCliNom%\" $gBuscar ";
    }
  }

  if ($gCliNomC != ""){
    if ($gxTerNomC == "SI"){
      $qSqlAux .="clinomcx = \"$gCliNomC\" $gBuscar ";
    } else{
      $qSqlAux .="clinomcx LIKE \"%$gCliNomC%\" $gBuscar ";
    }
  }

  if ($gPaiId != ""){
    $qSqlAux .= "paiidxxx = \"$gPaiId\" $gBuscar ";
  }

  if ($gDepId != ""){
    $qSqlAux .= "depidxxx = \"$gDepId\" $gBuscar ";
  }

  if ($gCiuId != ""){
    $qSqlAux .= "ciuidxxx = \"$gCiuId\" $gBuscar ";
  }

  $qSqlAux = substr($qSqlAux, 0, (($gBuscar == "OR") ? -4 : -5));
  $qSqlTip = substr($qSqlTip, 0, (($gBuscar == "OR") ? -4 : -5));

  $qClientes  = "SELECT * ";
  $qClientes .= "FROM $cAlfa.lpar0150 ";
  $qClientes .= "WHERE  ";
  if ($qSqlTip != "") {
    $qClientes .= "(".$qSqlTip.") AND ";
  }

  if ($qSqlAux != "") {
    $qClientes .= "(".$qSqlAux.") AND ";
  }
  switch($gEstado) {
    case "ACTIVO":
      $qClientes .= "regestxx = \"ACTIVO\" ";
    break;
    case "INACTIVO":
      $qClientes .= "regestxx = \"INACTIVO\" ";
    break;
    default:
      $qClientes .= "regestxx IN (\"ACTIVO\",\"INACTIVO\") ";
    break;
  }

  $xClientes = f_MySql("SELECT","",$qClientes,$xConexion01,"");
  // f_mensaje(__FILE__,__LINE__,$qClientes."~".mysql_num_rows($xClientes));

  if (mysql_num_rows($xClientes) > 0) {
    while ($xRT = mysql_fetch_array($xClientes)) {

      if($xRT['clitperx'] == "NATURAL"){
        $xRT['clinomxx'] = "";
        $xRT['clinomcx'] = "";
      } else {
        $xRT['clinom1x'] = "";
        $xRT['clinom2x'] = "";
        $xRT['cliape1x'] = "";
        $xRT['cliape2x'] = "";
      }

      $cCad01  = "<tr>";
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['cliidxxx']}</td>"; //NIT
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['tdiidxxx']}</td>"; //CODIGO TIPO DE DOCUMENTO
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['clitperx']}</td>"; //TIPO DE PERSONA (PUBLICA - JURIDICA - NATURAL)
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['clinomxx']}</td>"; //RAZON SOCIAL
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['clinomcx']}</td>"; //NOMBRE COMERCIAL
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['cliape1x']}</td>"; //PRIMER APELLIDO
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['cliape2x']}</td>"; //SEGUNDO APELLIDO
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['clinom1x']}</td>"; //PRIMER NOMBRE
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['clinom2x']}</td>"; //OTROS NOMBRES
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['cliprefa']}</td>"; //REQUIERE PREFACTURA (SI - NO)
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['paiidxxx']}</td>"; //CODIGO PAIS DOMICILIO FISCAL
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['depidxxx']}</td>"; //CODIGO DEPARTAMENTO DOMICILIO FISCAL
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['ciuidxxx']}</td>"; //CODIGO CIUDAD DOMICILIO FISCAL
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['clidirxx']}</td>"; //DIRECCION DOMICILIO FISCAL
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['clicposx']}</td>"; //CODIGO POSTAL 
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['clitelxx']}</td>"; //TELEFONO
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['climovxx']}</td>"; //TELÉFONO MOVIL
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['cliemaxx']}</td>"; //CORREO FACTURACIÓN ELECTRÓNICA
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['cliapaxx']}</td>"; //APARTADO AEREO
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['cliclixx']}</td>"; //CLIENTE (SI - NO)
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['cliusuxx']}</td>"; //USUARIO (SI - NO)
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['clidianx']}</td>"; //USUARIO DIAN (SI - NO)
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['cliempxx']}</td>"; //EMPLEADO (SI - NO)
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['cliconxx']}</td>"; //CONTACTO (SI - NO)
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['cliotrxx']}</td>"; //OTRO (SI - NO)
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['clisapxx']}</td>"; //CODIGO SAP
  			$cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['cliobsxx']}</td>"; //OBSERVACIONES
				$cCad01 .= "<td style=\"mso-number-format:yyyy-mm-dd;text-align: center\">{$xRT['regfcrex']}</td>"; //Fecha de Creacion				
        $cCad01 .= "<td style=\"mso-number-format:\@\">{$xRT['regestxx']}</td>"; //ESTADO
			$cCad01 .= "</tr>";
      fwrite($fOp,$cCad01);
		}
	}
  $cCad01 = "</table>";
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

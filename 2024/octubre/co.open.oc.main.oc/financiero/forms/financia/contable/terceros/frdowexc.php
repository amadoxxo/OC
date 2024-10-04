<?php
  //set_time_limit(0);
	include("../../../../libs/php/utility.php");

	$cNomFile = "CargueTerceros_".$_COOKIE['kUsrId']."_".date("YmdHis").".xls";
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
  $cCad01 .= "CODIGO PAIS DOMICILIO FISCAL\t";
  $cCad01 .= "CODIGO DEPARTAMENTO DOMICILIO FISCAL\t";
  $cCad01 .= "CODIGO CIUDAD DOMICILIO FISCAL\t";
  $cCad01 .= "TELEFONO DOMICILIO FISCAL\t";
  $cCad01 .= "DIRECCION DOMICILIO FISCAL\t";
  $cCad01 .= "CORREO ELECTRONICO\t";
  $cCad01 .= "CODIGO PAIS CORRESPONDENCIA\t";
  $cCad01 .= "CODIGO DEPARTAMENTO CORRESPONDENCIA\t";
  $cCad01 .= "CODIGO CIUDAD CORRESPONDENCIA\t";
  $cCad01 .= "DIRECCION CORRESPONDENCIA\t";
  $cCad01 .= "PROVEEDOR-CLIENTE (SI - NO)\t";
  $cCad01 .= "PROVEEDOR-EMPRESA (SI - NO)\t";
  $cCad01 .= "PROVEEDOR-SOCIO (SI - NO)\t";
  $cCad01 .= "ENTIDAD FINANCIERA (SI - NO)\t";
  $cCad01 .= "PROVEEDOR-OTROS (SI - NO)\t";
  $cCad01 .= "EMPLEADO (SI - NO)\t";
  $cCad01 .= "VENDEDOR (SI - NO)\t";
  $cCad01 .= "VENDEDOR ASIGNADO\t";
  $cCad01 .= "RESPONSABLE IVA REGIMEN COMUN (SI - NO)\t";
  $cCad01 .= "RESPONSABLE IVA REGIMEN SIMPLIFICADO (SI - NO)\t";
  $cCad01 .= "GRAN CONTRIBUYENTE (SI - NO)\t";
  $cCad01 .= "REGIMEN SIMPLE TRIBUTARIO (SI - NO)\t";
  $cCad01 .= "NO RESIDENTE EN EL PAIS  (SI - NO)\t";
  $cCad01 .= "NO RESIDENTE EN EL PAIS - APLICA IVA (SI - NO)\t";
  $cCad01 .= "NO RESIDENTE EN EL PAIS - APLICA GMF (SI - NO)\t";
  $cCad01 .= "NO RESIDENTE EN EL PAIS - NO SUJETO RETEFTE POR RENTA (SI - NO)\t";
  if ((f_InList($cAlfa,"ROLDANLO","TEROLDANLO","DEROLDANLO"))) {
    $cCad01 .= "NO RESIDENTE EN EL PAIS - AUTORRETENEDOR EN RENTA (SI - NO)\t";
    $cCad01 .= "NO RESIDENTE EN EL PAIS - AUTORRETENEDOR EN CREE (SI - NO)\t";
    $cCad01 .= "NO RESIDENTE EN EL PAIS - AGENTE RETENEDOR EN RENTA (SI - NO)\t";
    $cCad01 .= "NO RESIDENTE EN EL PAIS - AGENTE RETENEDOR EN CREE (SI - NO)\t";
  }
  $cCad01 .= "AUTORETENEDOR EN RENTA (SI - NO)\t";
  $cCad01 .= "AUTORETENEDOR DE IVA (SI - NO)\t";
  $cCad01 .= "AUTORETENEDOR DE ICA (SI - NO)\t";
  $cCad01 .= "CODIGO SUCURSALES AUTORETENEDOR DE ICA (SEPARADAS POR COMA)\t";
  $cCad01 .= "AUTORETENEDOR DE CREE (SI - NO)\t";
  $cCad01 .= "NO SUJETO RETEFTE POR RENTA (SI - NO)\t";
  $cCad01 .= "NO SUJETO RETEFTE POR IVA (SI - NO)\t";
  $cCad01 .= "NO SUJETO RETENCION CREE (SI - NO)\t";
  $cCad01 .= "NO SUJETO A RETENCION ICA (SI - NO)\t";
  $cCad01 .= "AGENTE RETENEDOR EN RENTA (SI - NO)\t";
  $cCad01 .= "AGENTE RETENEDOR EN IVA (SI - NO)\t";
  $cCad01 .= "AGENTE RETENEDOR CREE (SI - NO)\t";
  $cCad01 .= "AGENTE RETENEDOR ICA (SI - NO)\t";
  $cCad01 .= "CODIGO SUCURSALES AGENTE RETENEDOR ICA (SEPARADAS POR COMA)\t";
  $cCad01 .= "PROVEEDOR COMERCIALIZADORA INTERNACIONAL (SI - NO)\t";
  $cCad01 .= "NO SUJETO A EXPEDIR FACTURA DE VENTA O DOCUMENTO EQUIVALENTE (SI - NO)\t";
  $cCad01 .= "CODIGO POSTAL DIRECCION FISCAL\t";
  $cCad01 .= "CODIGO POSTAL DIRECCION CORRESPONDENCIA\t";
  $cCad01 .= "RESPONSABILIDAD FISCAL (SEPARADAS POR COMA)\t";
  $cCad01 .= "RESPONSABILIDAD TRIBUTO (SEPARADAS POR COMA)";
  if($vSysStr['system_activar_openetl'] == "SI") {
    $cCad01 .= "\tCORREOS NOTIFICACION (SEPARADOS POR COMA Y SIN ESPACIOS)\t";
    $cCad01 .= "MATRICULA MERCANTIL";
  }

  if ($cAlfa == "DHLEXPRE" || $cAlfa == "DEDHLEXPRE" || $cAlfa == "TEDHLEXPRE") {
    $cCad01 .= "\tCUENTA IMP CASH\t";
    $cCad01 .= "ESTADO CUENTA IMP CASH\t";
    $cCad01 .= "CUENTA IMP CREDITO\t";
    $cCad01 .= "ESTADO CUENTA IMP CREDITO\t";
    $cCad01 .= "ID BANCO\t";
    $cCad01 .= "NOMBRE BANCO\t";
    $cCad01 .= "TIPO CUENTA\t";
    $cCad01 .= "NUMERO DE CUENTA\t";
    $cCad01 .= "ESTADO CUENTA";
  }

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

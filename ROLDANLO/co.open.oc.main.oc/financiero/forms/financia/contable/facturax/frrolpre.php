<?php
	/**
	 * Imprime Vista Previa Factura de Venta Roldan.
	 * @author Hair Zabala <hair.zabala@opentecnologia.com.co>
	 */
	include("../../../../libs/php/utility.php");
	include("../../../../libs/php/utiliqdo.php");

	// ini_set('error_reporting', E_ERROR);
	// ini_set("display_errors","1");

  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb   = $kDf[3];
  $kUser      = $kDf[4];
  $kLicencia  = $kDf[5];
  $swidth     = $kDf[6];

	$switch=0;
	$cEstiloLetra = 'arial';
	$cEstiloLetrab = 'arialb';

	$mPrn = explode("|",$prints);
	for ($nn=0;$nn<count($mPrn);$nn++) {
		if (strlen($mPrn[$nn]) > 0) {
			$vComp = explode("~",$mPrn[$nn]);
			$cComId   = $vComp[0];
			$cComCod  = $vComp[1];
			$cComCsc  = $vComp[3];
			$cComCsc2 = $vComp[3];
			$cRegFCre = $vComp[4];
			$cNewYear = substr($cRegFCre,0,4);
			$cAno     = substr($cRegFCre,0,4);
		}
	}

	//Busco los comprobantes donde El tipo de Comprobante sea RCM
	$qFpar117  = "SELECT comidxxx, comcodxx ";
	$qFpar117 .= "FROM $cAlfa.fpar0117 ";
	$qFpar117 .= "WHERE ";
	$qFpar117 .= "comtipxx  = \"RCM\" ";
	$xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
	$mRCM = array();
	while ($xRF117 = mysql_fetch_array($xFpar117)) {
		$mRCM[count($mRCM)] = "{$xRF117['comidxxx']}~{$xRF117['comcodxx']}";
	}

	# Reconstruyendo POST de DOS, pagos a terceros e ingresos propios
	$qDatDo  = "SELECT * ";
	$qDatDo .= "FROM $cAlfa.$cTabla_DOS ";
	$qDatDo .= "WHERE ";
	$qDatDo .= "cUsrId_DOS = \"{$_COOKIE['kUsrId']}\" AND ";
	$qDatDo .= "cFacId_DOS = \"{$_POST['cFacId']}\" ";
	$qDatDo .= "ORDER BY ABS(cSeq_DOS) ";
	$xDatDo  = f_MySql("SELECT","",$qDatDo,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qDatDo."~".mysql_num_rows($xDatDo));
	$vCampos = array();
	while($xRC = mysql_fetch_field($xDatDo)){
		$vCampos[] = $xRC->name;
	}

	# Armando Matriz de DOs
	$nSecuencia = 1;
	while ($xRDD = mysql_fetch_assoc($xDatDo)) {
		for($n=0; $n<count($vCampos); $n++) {
			$_POST[$vCampos[$n].$nSecuencia] = $xRDD[$vCampos[$n]];
		}
		$nSecuencia++;
	}

	$qPCCA  = "SELECT * ";
	$qPCCA .= "FROM $cAlfa.$cTabla_PCCA ";
	$qPCCA .= "WHERE ";
	$qPCCA .= "cUsrId_PCCA = \"{$_COOKIE['kUsrId']}\" AND ";
	$qPCCA .= "cFacId_PCCA = \"{$_POST['cFacId']}\" ";
	$qPCCA .= "ORDER BY ABS(cComSeq_PCCA) ";
	$xPCCA  = f_MySql("SELECT","",$qPCCA,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qPCCA."~".mysql_num_rows($xPCCA));
	$vCampos = array();
	while($xRC = mysql_fetch_field($xPCCA)){
		$vCampos[] = $xRC->name;
	}
	# Armando Matriz de DOs
	$nSecuencia = 1;
	while ($xRP = mysql_fetch_array($xPCCA)) {
		for($n=0; $n<count($vCampos); $n++) {
			$_POST[$vCampos[$n].$nSecuencia] = $xRP[$vCampos[$n]];
		}
		$nSecuencia++;
	}

	$qIPA  = "SELECT * ";
	$qIPA .= "FROM $cAlfa.$cTabla_IPA ";
	$qIPA .= "WHERE ";
	$qIPA .= "cUsrId_IPA = \"{$_COOKIE['kUsrId']}\" AND ";
	$qIPA .= "cFacId_IPA = \"{$_POST['cFacId']}\" ";
	$qIPA .= "ORDER BY ABS(cComSeq_IPA) ";
	$xIPA  = f_MySql("SELECT","",$qIPA,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qIPA."~".mysql_num_rows($xIPA));
	$vCampos = array();
	while($xRC = mysql_fetch_field($xIPA)){
		$vCampos[] = $xRC->name;
	}
	# Armando Matriz de DOs
	$nSecuencia = 1;
	while ($xRI = mysql_fetch_array($xIPA)) {
		for($n=0; $n<count($vCampos); $n++) {
			$_POST[$vCampos[$n].$nSecuencia] = $xRI[$vCampos[$n]];
		}
		$nSecuencia++;
	}

	## Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Dietrich Para encabezado de factura ##
	$qAgeDat  = "SELECT ";
	$qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
	$qAgeDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
	$qAgeDat .= "$cAlfa.SIAI0150.CLIDIRXX, ";
	$qAgeDat .= "$cAlfa.SIAI0150.CLITELXX, ";
	$qAgeDat .= "$cAlfa.SIAI0150.CLIFAXXX ";
	$qAgeDat .= "FROM $cAlfa.SIAI0150 ";
	$qAgeDat .= "WHERE ";
	$qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vSysStr['financiero_nit_agencia_aduanas']}\" AND ";
	$qAgeDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
	$xAgeDat  = f_MySql("SELECT","",$qAgeDat,$xConexion01,"");
	$vAgeDat  = mysql_fetch_array($xAgeDat);
	## Fin Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Dietrich Para encabezado de factura ##

	##Traigo Datos de la Resolucion ##
	$qResDat  = "SELECT * ";
	$qResDat .= "FROM $cAlfa.fpar0138 ";
	$qResDat .= "WHERE ";
	$qResDat .= "rescomxx LIKE \"%{$cComId}~{$cComCod}%\" AND ";
	$qResDat .= "regestxx = \"ACTIVO\" LIMIT 0,1";
	$xResDat  = f_MySql("SELECT","",$qResDat,$xConexion01,"");
	$nFilRes  = mysql_num_rows($xResDat);
	// f_Mensaje(__FILE__,__LINE__,$qResDat."~".$nFilRes);
	if ($nFilRes > 0) {
		$vResDat = mysql_fetch_array($xResDat);
	}
	##Fin Traigo Datos de la Resolucion ##

	##Consulto en la SIAI0150 Datos del Facturado A: ##
	$qCliDat  = "SELECT ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIFAXXX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.CLICONTX != \"\",$cAlfa.SIAI0150.CLICONTX,\"SIN RESPONSABLE\") AS CLICONTX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.CLINRPXX != \"\",$cAlfa.SIAI0150.CLINRPXX,\"\") AS CLINRPXX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.CLIPLAXX != \"\",$cAlfa.SIAI0150.CLIPLAXX,\"\") AS CLIPLAXX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.TDIIDXXX != \"\",$cAlfa.SIAI0150.TDIIDXXX,\"\") AS TDIIDXXX ";
	$qCliDat .= "FROM $cAlfa.SIAI0150 ";
	$qCliDat .= "WHERE ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$_POST['cTerIdInt']}\" AND ";
	$qCliDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
	$xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qCliDat."~".mysql_num_rows($xCliDat));
	if (mysql_num_rows($xCliDat) > 0) {
		$vCliDat = mysql_fetch_array($xCliDat);
	}
	##Consulto en la SIAI0150 Datos del Facturado A: ##

	/**
   * Nombre del cliente dueño del DO - teridxxx
   */
  $qNomTer  = "SELECT ";
  $qNomTer .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS CLINOMXX ";
  $qNomTer .= "FROM $cAlfa.SIAI0150 ";
  $qNomTer .= "WHERE ";
  $qNomTer .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$_POST['cTerId']}\" ";
  $xNomTer  = f_MySql("SELECT","",$qNomTer,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qNomTer."~".mysql_num_rows($xNomTer));
  $vNomTer  = mysql_fetch_array($xNomTer);

	##Traigo Ciudad del Facturado A ##
	$qCiuDat  = "SELECT * ";
	$qCiuDat .= "FROM $cAlfa.SIAI0055 ";
	$qCiuDat .= "WHERE ";
	$qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCliDat['PAIIDXXX']}\" AND ";
	$qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCliDat['DEPIDXXX']}\" AND ";
	$qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCliDat['CIUIDXXX']}\" AND ";
	$qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
	$xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qCiuDat."~".mysql_num_rows($xCiuDat));
	if (mysql_num_rows($xCiuDat) > 0) {
		$vCiuDat = mysql_fetch_array($xCiuDat);
	}
	##Fin Traigo Ciudad del Facturado A ##

	##Traigo Datos de Contacto del Facturado a ##
	// f_Mensaje(__FILE__,__LINE__,$vCliDat['CLICONTX']);
	if($vCliDat['CLICONTX'] != ""){
		$vContactos = explode("~",$vCliDat['CLICONTX']);
		//f_Mensaje(__FILE__,__LINE__,count($vContactos));
		if(count($vContactos) > 1){
			$vIdContacto = $vContactos[1];
		}else{
			$vIdContacto = $vCliDat['CLICONTX'];
		}
	}

	$qConDat  = "SELECT ";
	$qConDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS NOMBRE ";
	$qConDat .= "FROM $cAlfa.SIAI0150 ";
	$qConDat .= "WHERE ";
	$qConDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"$vIdContacto\" AND ";
	$qConDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ";
	$xConDat  = f_MySql("SELECT","",$qConDat,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qConDat."~".mysql_num_rows($xConDat));
	if (mysql_num_rows($xConDat) > 0) {
		$vConDat = mysql_fetch_array($xConDat);
	}
	##Fin Traigo Datos de Contacto del Facturado a ##

	##Traigo Primer Do para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##
	$cSucId = ""; $cDocId  = ""; $cDocSuf = "";
	$nCantDo = 0;
	$dFecMay = date("Y"); //Fecha
	for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
		if($i == 0) {
			$cDocId   = $_POST['cDosNro_DOS'.($i+1)];
			$cDocSuf  = $_POST['cDosSuf_DOS'.($i+1)];
			$cSucId   = $_POST['cSucId_DOS' .($i+1)];
		}
		$dFecMay = ($dFecMay > substr($_POST['cDosFec_DOS'.($i+1)],0,4)) ? substr($_POST['cDosFec_DOS'.($i+1)],0,4) : $dFecMay;
		$nCantDo += 1;
	}//for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
	$nAnoIniDo = (($dFecMay-1) <  $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($dFecMay-1);
	##Fin Traigo Primer Do para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##

	##Busco Ciudad por donde se esta facturando ##
	$qCcoDat  = "SELECT * ";
	$qCcoDat .= "FROM $cAlfa.fpar0008 ";
	$qCcoDat .= "WHERE ";
	$qCcoDat .= "$cAlfa.fpar0008.sucidxxx = \"{$cSucId}\" AND ";
	$qCcoDat .= "$cAlfa.fpar0008.regestxx = \"ACTIVO\" LIMIT 0,1 ";
	$xCcoDat  = f_MySql("SELECT","",$qCcoDat,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qCcoDat."~".mysql_num_rows($xCcoDat));
	if (mysql_num_rows($xCcoDat) > 0) {
		$vSucDesc = mysql_fetch_array($xCcoDat);
	}

	##Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##
	$qDceDat  = "SELECT * ";
	$qDceDat .= "FROM $cAlfa.sys00121 ";
	$qDceDat .= "WHERE ";
	$qDceDat .= "$cAlfa.sys00121.sucidxxx = \"$cSucId\" AND ";
	$qDceDat .= "$cAlfa.sys00121.docidxxx = \"$cDocId\" AND ";
	$qDceDat .= "$cAlfa.sys00121.docsufxx = \"$cDocSuf\" ";
	$xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qDceDat."~".mysql_num_rows($xDceDat));
	if (mysql_num_rows($xDceDat) > 0) {
		$vDceDat = mysql_fetch_array($xDceDat);
	}
	##Fin Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##

	##Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##
	switch ($vDceDat['doctipxx']){
		case "IMPORTACION":
			$cTitulo = "Importador";

			##Traigo Datos de la SIAI0200 DATOS DEL DO ##
			$qDoiDat  = "SELECT * ";
			$qDoiDat .= "FROM $cAlfa.SIAI0200 ";
			$qDoiDat .= "WHERE ";
			$qDoiDat .= "$cAlfa.SIAI0200.DOIIDXXX = \"$cDocId\" AND ";
			$qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"$cDocSuf\" AND ";
			$qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"$cSucId\" ";
			$xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qDoiDat." ~ ".mysql_num_rows($xDoiDat));
			if (mysql_num_rows($xDoiDat) > 0) {
				$vDoiDat = mysql_fetch_array($xDoiDat);
			}
			##Fin Traigo Datos de la SIAI0200 DATOS DEL DO ##

			##Traigo Datos de Do SIAI0206 ##
			$qDecDat  = "SELECT ";
			$qDecDat .= "SUBID2XX, ";
			$qDecDat .= "ADMIDXXX, ";
			$qDecDat .= "SUM($cAlfa.SIAI0206.LIMBULXX) AS LIMBULXX, ";
			$qDecDat .= "SUM($cAlfa.SIAI0206.LIMNETXX) AS LIMNETXX, ";//Cif
			$qDecDat .= "SUM($cAlfa.SIAI0206.LIMCIFXX) AS LIMCIFXX, ";
			$qDecDat .= "SUM($cAlfa.SIAI0206.LIMPBRXX) AS LIMPBRXX, ";
			$qDecDat .= "SUM($cAlfa.SIAI0206.LIMPNEXX) AS LIMPNEXX, ";
			$qDecDat .= "SUM($cAlfa.SIAI0206.LIMVLRXX) AS LIMVLRXX, ";//Fob
			$qDecDat .= "SUM($cAlfa.SIAI0206.LIMGRAXX) AS LIMGRA2X, ";
			$qDecDat .= "SUM($cAlfa.SIAI0206.LIMSUBT2) AS LIMSUBT2, ";
			$qDecDat .= "SUM($cAlfa.SIAI0206.LIMFLEXX) AS LIMFLEXX, ";
			$qDecDat .= "SUM($cAlfa.SIAI0206.LIMSEGXX) AS LIMSEGXX, ";
			$qDecDat .= "SUM($cAlfa.SIAI0206.DGETRMXX) AS DGETRMXX ";//Trm
			$qDecDat .= "FROM $cAlfa.SIAI0206 ";
			$qDecDat .= "WHERE ";
			$qDecDat .= "$cAlfa.SIAI0206.DOIIDXXX = \"$cDocId\"  AND ";
			$qDecDat .= "$cAlfa.SIAI0206.DOISFIDX = \"$cDocSuf\" AND ";
			$qDecDat .= "$cAlfa.SIAI0206.ADMIDXXX = \"$cSucId\" ";
			$qDecDat .= "GROUP BY $cAlfa.SIAI0206.DOIIDXXX ";
			$xDecDat  = f_MySql("SELECT","",$qDecDat,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qDecDat."~".mysql_num_rows($xDecDat));
			if (mysql_num_rows($xDecDat) > 0) {
				$vDecDat  = mysql_fetch_array($xDecDat);
			}
			##Fin Traigo Datos de Do SIAI0206 ##

			##Administracion de ingreso##
			$vAdmIng = array();
			if ($vDoiDat['ODIIDXXX'] != "") {
				$qAdmIng  = "SELECT ODIDESXX ";
				$qAdmIng .= "FROM $cAlfa.SIAI0103 ";
				$qAdmIng .= "WHERE ";
				$qAdmIng .= "ODIIDXXX = \"{$vDoiDat['ODIIDXXX']}\" ";
				$qAdmIng .= "LIMIT 0,1 ";
				$xAdmIng  = f_MySql("SELECT","",$qAdmIng,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qAdmIng."~".mysql_num_rows($xAdmIng));
				$vAdmIng  = mysql_fetch_array($xAdmIng);
			}
			##Fin Administracion de ingreso##

			//Busco nombre comercial
			$qDceDat  = "SELECT $cAlfa.SIAI0205.ITENOCXX ";
			$qDceDat .= "FROM $cAlfa.SIAI0205 ";
			$qDceDat .= "WHERE ";
			$qDceDat .= "$cAlfa.SIAI0205.ADMIDXXX = \"$cSucId\" AND ";
			$qDceDat .= "$cAlfa.SIAI0205.DOIIDXXX = \"$cDocId\" AND ";
			$qDceDat .= "$cAlfa.SIAI0205.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
			$xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qDceDat."~".mysql_num_rows($xDceDat));
			if (mysql_num_rows($xDceDat) > 0) {
				$vNomComercial = mysql_fetch_array($xDceDat);
			}

			//Busco Proveedor Primer Registro
			$qProDat  = "SELECT $cAlfa.SIAI0202.PIEIDXXX ";
			$qProDat .= "FROM $cAlfa.SIAI0202 ";
			$qProDat .= "WHERE ";
			$qProDat .= "$cAlfa.SIAI0202.ADMIDXXX = \"$cSucId\" AND ";
			$qProDat .= "$cAlfa.SIAI0202.DOIIDXXX = \"$cDocId\" AND ";
			$qProDat .= "$cAlfa.SIAI0202.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
			$xProDat  = f_MySql("SELECT","",$qProDat,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qProDat."~".mysql_num_rows($xProDat));
			if (mysql_num_rows($xProDat) > 0) {
				$vProveedor = mysql_fetch_array($xProDat);
				// Busco Nombre del Proveedor
				$qProNom  = "SELECT $cAlfa.SIAI0125.PIENOMXX ";
				$qProNom .= "FROM $cAlfa.SIAI0125 ";
				$qProNom .= "WHERE ";
				$qProNom .= "$cAlfa.SIAI0125.PIEIDXXX = \"{$vProveedor['PIEIDXXX']}\" LIMIT 0,1";
				$xProNom  = f_MySql("SELECT","",$qProNom,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qProNom."~".mysql_num_rows($xProNom));
				if (mysql_num_rows($xProNom) > 0) {
					$vProNom = mysql_fetch_array($xProNom);
				}
			}

			## Busco medio de Transporte
			$qMedTra  = "SELECT $cAlfa.SIAI0120.MTRDESXX ";
			$qMedTra .= "FROM $cAlfa.SIAI0120 ";
			$qMedTra .= "WHERE ";
			$qMedTra .= "$cAlfa.SIAI0120.MTRIDXXX = \"{$vDoiDat['MTRIDXXX']}\" LIMIT 0,1 ";
			$xMedTra  = f_MySql("SELECT","",$qMedTra,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qMedTra."~".mysql_num_rows($xMedTra));
			if (mysql_num_rows($xMedTra) > 0) {
				$vMedTra = mysql_fetch_array($xMedTra);
			}

			## Busco Pais de Origen
			$qPaiOrg  = "SELECT $cAlfa.SIAI0052.PAIDESXX ";
			$qPaiOrg .= "FROM $cAlfa.SIAI0052 ";
			$qPaiOrg .= "WHERE ";
			$qPaiOrg .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$vDoiDat['PAIIDXXX']}\" LIMIT 0,1 ";
			$xPaiOrg  = f_MySql("SELECT","",$qPaiOrg,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qPaiOrg."~".mysql_num_rows($xPaiOrg));
			if (mysql_num_rows($xPaiOrg) > 0) {
				$vPaiOrg = mysql_fetch_array($xPaiOrg);
			}

			## Busco Lugar de Ingreso
			$qLugIng  = "SELECT $cAlfa.SIAI0119.LINDESXX ";
			$qLugIng .= "FROM $cAlfa.SIAI0119 ";
			$qLugIng .= "WHERE ";
			$qLugIng .= "$cAlfa.SIAI0119.LINIDXXX = \"{$vDoiDat['LINIDXXX']}\" LIMIT 0,1 ";
			$xLugIng  = f_MySql("SELECT","",$qLugIng,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qLugIng."~".mysql_num_rows($xLugIng));
			if (mysql_num_rows($xLugIng) > 0) {
				$vLugIng = mysql_fetch_array($xLugIng);
			}

      ## Busco Datos del Vendedor (Nombres y Apellidos)
      if ($vDoiDat['DOCVENXX'] != "") {
        $qVenDat  = "SELECT ";
        $qVenDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX ";
        $qVenDat .= "FROM $cAlfa.SIAI0150 ";
        $qVenDat .= "WHERE ";
        $qVenDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vDoiDat['DOCVENXX']}\" AND ";
        $qVenDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ";
        $xVenDat  = f_MySql("SELECT","",$qVenDat,$xConexion01,"");
        $nVenDat  = mysql_num_rows($xVenDat);
        if ($nVenDat > 0) {
          $vVenDat = mysql_fetch_array($xVenDat);
        }
      }

			##Cargo Variables para Impresion de Datos de Do ##
			$cTasCam          = $vDoiDat['TCATASAX']; //Tasa de Cambio
			$cDocTra          = $vDoiDat['DGEDTXXX']; //Documento de Transporte
			$cBultos          = $vDoiDat['DGEBULXX']; //Bultos
			$cPesBru          = $vDoiDat['DGEPBRXX']; //Peso Bruto
			$nValAdu          = $vDecDat['LIMCIFXX'];
			$cPaisOrigen      = $vDoiDat['PAIIDXXX'];
			$cOpera           = "CIF:";
			$cPedido          = $vDoiDat['DOIPEDXX'];
			$cAduana          = $vAdmIng['ODIDESXX'];
			$cVendedor        = $nVenDat['CLINOMXX'];
			$cProveedor       = $vProNom['PIENOMXX'];
			$cMedioTransporte = $vMedTra['MTRDESXX'];
			$cPaisOrigen      = $vPaiOrg['PAIDESXX'];
			$cLugarIngreso    = $vLugIng['LINDESXX'];
      $cContenedor      = $vDoiDat['DOICONNU'];
      $cCifUsd          = $vDecDat['LIMNETXX']; //Cif USD
			$cValTrm          = $vDecDat['DGETRMXX']; //TRM
			##Fin Cargo Variables para Impresion de Datos de Do ##
		break;
		case "EXPORTACION":
			$cTitulo = "Exportador";

			## Consulto Datos de Do en Exportaciones tabla siae0199 ##
			$qDexDat  = "SELECT * ";
			$qDexDat .= "FROM $cAlfa.siae0199 ";
			$qDexDat .= "WHERE ";
			$qDexDat .= "$cAlfa.siae0199.dexidxxx = \"$cDocId\" AND ";
			$qDexDat .= "$cAlfa.siae0199.admidxxx = \"$cSucId\" ";
			$xDexDat  = f_MySql("SELECT","",$qDexDat,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qDexDat."~".mysql_num_rows($xDexDat));
			if (mysql_num_rows($xDexDat) > 0) {
				$vDexDat = mysql_fetch_array($xDexDat);
			}
			## Fin Consulto Datos de Do en Exportaciones tabla siae0199 ##

			##Trayendo aduana de salida ##
			$qAduSal  = "SELECT odiid2xx ";
			$qAduSal .= "FROM $cAlfa.siae0200 ";
			$qAduSal .= "WHERE ";
			$qAduSal .= "$cAlfa.siae0200.dexidxxx = \"$cDocId\" AND ";
			$qAduSal .= "$cAlfa.siae0200.admidxxx = \"$cSucId\" AND ";
			$qAduSal .= "$cAlfa.siae0200.odiid2xx != \"\" LIMIT 0,1 ";
			$xAduSal  = f_MySql("SELECT","",$qAduSal,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qAduSal."~".mysql_num_rows($xAduSal));
			$vAduSal  = mysql_fetch_array($xAduSal);
			if ($vAduSal['odiid2xx'] != "") {
				##Tayendo descripcion Aduana de salida
				$qDesAdu  = "SELECT ODIDESXX ";
				$qDesAdu .= "FROM $cAlfa.SIAI0103 ";
				$qDesAdu .= "WHERE ";
				$qDesAdu .= "ODIIDXXX = \"{$vAduSal['odiid2xx']}\" ";
				$qDesAdu .= "LIMIT 0,1 ";
				$xDesAdu  = f_MySql("SELECT","",$qDesAdu,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qDesAdu."~".mysql_num_rows($xDesAdu));
				$vDesAdu  = mysql_fetch_array($xDesAdu);
			}

			##Trayendo el proveedor##
			$qProDat  = "SELECT pieidxxx,laiidxxx ";
			$qProDat .= "FROM $cAlfa.siae0200 ";
			$qProDat .= "WHERE ";
			$qProDat .= "$cAlfa.siae0200.dexidxxx = \"$cDocId\" AND ";
			$qProDat .= "$cAlfa.siae0200.admidxxx = \"$cSucId\" LIMIT 0,1 ";
			$xProDat  = f_MySql("SELECT","",$qProDat,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qProDat."~".mysql_num_rows($xProDat));
			$vProDat  = mysql_fetch_array($xProDat);
			if ($vProDat['pieidxxx'] != "") {
				$qProNom  = "SELECT $cAlfa.SIAI0125.PIENOMXX ";
				$qProNom .= "FROM $cAlfa.SIAI0125 ";
				$qProNom .= "WHERE ";
				$qProNom .= "$cAlfa.SIAI0125.PIEIDXXX = \"{$vProDat['pieidxxx']}\" LIMIT 0,1";
				$qProNom .= "LIMIT 0,1 ";
				$xProNom  = f_MySql("SELECT","",$qProNom,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qProNom."~".mysql_num_rows($xProNom));
				$vProNom  = mysql_fetch_array($xProNom);
			}

			##Traigo Valor fob, peso neto, peso bruto, bultos de la tabla de Items de Exportaciones siae0201 ##
			$qIteDat  = "SELECT ";
			$qIteDat .= "SUM($cAlfa.siae0201.itefobxx) AS itefobxx, ";
			$qIteDat .= "SUM($cAlfa.siae0201.itepbrxx) AS itepbrxx, ";
			$qIteDat .= "SUM($cAlfa.siae0201.itepnexx) AS itepnexx, ";
			$qIteDat .= "SUM($cAlfa.siae0201.itebulxx) AS itebulxx ";
			$qIteDat .= "FROM $cAlfa.siae0201 ";
			$qIteDat .= "WHERE ";
			$qIteDat .= "$cAlfa.siae0201.dexidxxx =\"$cDocId\" AND ";
			$qIteDat .= "$cAlfa.siae0201.admidxxx = \"$cSucId\" ";
			$xIteDat  = f_MySql("SELECT","",$qIteDat,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qIteDat."~".mysql_num_rows($xIteDat));
			if (mysql_num_rows($xIteDat) > 0) {
				$vIteDat = mysql_fetch_array($xIteDat);
			}

			## Busco medio de Transporte
			$qMedTra  = "SELECT $cAlfa.SIAI0120.MTRDESXX ";
			$qMedTra .= "FROM $cAlfa.SIAI0120 ";
			$qMedTra .= "WHERE ";
			$qMedTra .= "$cAlfa.SIAI0120.MTRIDXXX = \"{$vDexDat['mtridxxx']}\" LIMIT 0,1 ";
			$xMedTra  = f_MySql("SELECT","",$qMedTra,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qMedTra."~".mysql_num_rows($xMedTra));
			if (mysql_num_rows($xMedTra) > 0) {
				$vMedTra = mysql_fetch_array($xMedTra);
			}

			## Busco Lugar Destino Final
			$qLugIng  = "SELECT $cAlfa.siae0074.laidesxx ";
			$qLugIng .= "FROM $cAlfa.siae0074 ";
			$qLugIng .= "WHERE ";
			$qLugIng .= "$cAlfa.siae0074.laiidxxx = \"{$vProDat['laiidxxx']}\" LIMIT 0,1 ";
			$xLugIng  = f_MySql("SELECT","",$qLugIng,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qLugIng."~".mysql_num_rows($xLugIng));
			if (mysql_num_rows($xLugIng) > 0) {
				$vLugIng = mysql_fetch_array($xLugIng);
			}
      ## Busco Datos del Vendedor (Nombres y Apellidos)
      if ($vDexDat['docvenxx'] != "") {
        $qVenDat  = "SELECT ";
        $qVenDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX ";
        $qVenDat .= "FROM $cAlfa.SIAI0150 ";
        $qVenDat .= "WHERE ";
        $qVenDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vDexDat['docvenxx']}\" AND ";
        $qVenDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ";
        $xVenDat  = f_MySql("SELECT","",$qVenDat,$xConexion01,"");
        $nVenDat  = mysql_num_rows($xVenDat);
        if ($nVenDat > 0) {
          $vVenDat = mysql_fetch_array($xVenDat);
        }
      }

			##Cargo Variables para Impresion de Datos de Do ##
			$cTasCam          = $vDoiDat['TCATASAX']; //Tasa de Cambio
			$cDocTra          = $vDexDat['dexdtrxx']; //Documento de Transporte
			$cBultos          = $vIteDat['itebulxx']; //Bultos
			$cPesBru          = $vIteDat['itepbrxx']; //Peso Bruto
			$nValAdu          = ($vIteDat['itefobxx']*$vDceDat['doctrmxx']);
			$cOpera           = "FOB"; // FOB
			$cPedido          = $vDexDat['dexpedxx'];
			$cAduana          = $vDesAdu['ODIDESXX'];
			$cVendedor        = $vVenDat['CLINOMXX'];
			$cProveedor       = $vProNom['PIENOMXX'];
			$cMedioTransporte = $vMedTra['MTRDESXX'];
			$cPaisOrigen      = 'COLOMBIA';
			$cLugarIngreso    = $vLugIng['laidesxx'];
			##Fin Cargo Variables para Impresion de Datos de Do ##

		break;
		case "TRANSITO":
			$cTitulo = "Importador";

			## Traigo Datos de la SIAI0200 ##
			$qDoiDat  = "SELECT * ";
			$qDoiDat .= "FROM $cAlfa.SIAI0200 ";
			$qDoiDat .= "WHERE ";
			$qDoiDat .= "DOIIDXXX = \"$cDocId\" AND ";
			$qDoiDat .= "DOISFIDX = \"$cDocSuf\" AND ";
			$qDoiDat .= "ADMIDXXX = \"$cSucId\" ";
			$xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qDoiDat."~".mysql_num_rows($xDoiDat));
			if (mysql_num_rows($xDoiDat) > 0) {
				$vDoiDat = mysql_fetch_array($xDoiDat);
			}
			## Fin Consulta a la tabla de Do's ##

			## Consulto en la Tabla de Control DTA ##
			$qDtaDat  = "SELECT * ";
			$qDtaDat .= "FROM $cAlfa.dta00200 ";
			$qDtaDat .= "WHERE ";
			$qDtaDat .= "$cAlfa.dta00200.doiidxxx = \"$cDocId\" AND ";
			$qDtaDat .= "$cAlfa.dta00200.admidxxx = \"$cSucId\" ";
			$xDtaDat  = f_MySql("SELECT","",$qDtaDat,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qDtaDat."~".mysql_num_rows($xDtaDat));
			if (mysql_num_rows($xDtaDat) > 0) {
				$vDtaDat = mysql_fetch_array($xDtaDat);
			}
			## Fin consulto en la tabla de Control DTA ##

			## Consulto en la tabla de Items DTA ##
			$qIteDat  = "SELECT  ";
			$qIteDat .= "SUM($cAlfa.dta00201.itepbrxx) AS itepbrxx, ";
			$qIteDat .= "SUM($cAlfa.dta00201.itebulxx) AS itebulxx ";
			$qIteDat .= "FROM $cAlfa.dta00201 ";
			$qIteDat .= "WHERE ";
			$qIteDat .= "$cAlfa.dta00201.doiidxxx = \"$cDocId\" AND ";
			$qIteDat .= "$cAlfa.dta00201.admidxxx = \"$cSucId\" ";
			$xIteDat  = f_MySql("SELECT","",$qIteDat,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qIteDat."~".mysql_num_rows($xIteDat));
			if (mysql_num_rows($xIteDat) > 0) {
				$vIteDat = mysql_fetch_array($xIteDat);
			}

			##Administracion de ingreso##
			$vAdmIng = array();
			if ($vDoiDat['ODIIDXXX'] != "") {
				$qAdmIng  = "SELECT ODIDESXX ";
				$qAdmIng .= "FROM $cAlfa.SIAI0103 ";
				$qAdmIng .= "WHERE ";
				$qAdmIng .= "ODIIDXXX = \"{$vDoiDat['ODIIDXXX']}\" ";
				$qAdmIng .= "LIMIT 0,1 ";
				$xAdmIng  = f_MySql("SELECT","",$qAdmIng,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qAdmIng."~".mysql_num_rows($xAdmIng));
				$vAdmIng  = mysql_fetch_array($xAdmIng);
			}

			//Busco nombre comercial
			$qDceDat  = "SELECT $cAlfa.SIAI0205.ITENOCXX ";
			$qDceDat .= "FROM $cAlfa.SIAI0205 ";
			$qDceDat .= "WHERE ";
			$qDceDat .= "$cAlfa.SIAI0205.ADMIDXXX = \"$cSucId\" AND ";
			$qDceDat .= "$cAlfa.SIAI0205.DOIIDXXX = \"$cDocId\" AND ";
			$qDceDat .= "$cAlfa.SIAI0205.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
			$xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qDceDat."~".mysql_num_rows($xDceDat));
			if (mysql_num_rows($xDceDat) > 0) {
				$vNomComercial = mysql_fetch_array($xDceDat);
			}

			//Busco Proveedor Primer Registro
			$qProDat  = "SELECT $cAlfa.SIAI0202.PIEIDXXX ";
			$qProDat .= "FROM $cAlfa.SIAI0202 ";
			$qProDat .= "WHERE ";
			$qProDat .= "$cAlfa.SIAI0202.ADMIDXXX = \"$cSucId\" AND ";
			$qProDat .= "$cAlfa.SIAI0202.DOIIDXXX = \"$cDocId\" AND ";
			$qProDat .= "$cAlfa.SIAI0202.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
			$xProDat  = f_MySql("SELECT","",$qProDat,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qProDat."~".mysql_num_rows($xProDat));
			if (mysql_num_rows($xProDat) > 0) {
				$vProveedor = mysql_fetch_array($xProDat);
				// Busco Nombre del Proveedor
				$qProNom  = "SELECT $cAlfa.SIAI0125.PIENOMXX ";
				$qProNom .= "FROM $cAlfa.SIAI0125 ";
				$qProNom .= "WHERE ";
				$qProNom .= "$cAlfa.SIAI0125.PIEIDXXX = \"{$vProveedor['PIEIDXXX']}\" LIMIT 0,1";
				$xProNom  = f_MySql("SELECT","",$qProNom,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qProNom."~".mysql_num_rows($xProNom));
				if (mysql_num_rows($xProNom) > 0) {
					$vProNom = mysql_fetch_array($xProNom);
				}
			}

			## Busco medio de Transporte
			$qMedTra  = "SELECT $cAlfa.SIAI0120.MTRDESXX ";
			$qMedTra .= "FROM $cAlfa.SIAI0120 ";
			$qMedTra .= "WHERE ";
			$qMedTra .= "$cAlfa.SIAI0120.MTRIDXXX = \"{$vDoiDat['MTRIDXXX']}\" LIMIT 0,1 ";
			$xMedTra  = f_MySql("SELECT","",$qMedTra,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qMedTra."~".mysql_num_rows($xMedTra));
			if (mysql_num_rows($xMedTra) > 0) {
				$vMedTra = mysql_fetch_array($xMedTra);
			}

			## Busco Pais de Origen
			$qPaiOrg  = "SELECT $cAlfa.SIAI0052.PAIDESXX ";
			$qPaiOrg .= "FROM $cAlfa.SIAI0052 ";
			$qPaiOrg .= "WHERE ";
			$qPaiOrg .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$vDoiDat['PAIIDXXX']}\" LIMIT 0,1 ";
			$xPaiOrg  = f_MySql("SELECT","",$qPaiOrg,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qPaiOrg."~".mysql_num_rows($xPaiOrg));
			if (mysql_num_rows($xPaiOrg) > 0) {
				$vPaiOrg = mysql_fetch_array($xPaiOrg);
			}

			## Busco Lugar de Ingreso
			$qLugIng  = "SELECT $cAlfa.SIAI0119.LINDESXX ";
			$qLugIng .= "FROM $cAlfa.SIAI0119 ";
			$qLugIng .= "WHERE ";
			$qLugIng .= "$cAlfa.SIAI0119.LINIDXXX = \"{$vDoiDat['LINIDXXX']}\" LIMIT 0,1 ";
			$xLugIng  = f_MySql("SELECT","",$qLugIng,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qLugIng."~".mysql_num_rows($xLugIng));
			if (mysql_num_rows($xLugIng) > 0) {
				$vLugIng = mysql_fetch_array($xLugIng);
			}

      ## Busco Datos del Vendedor (Nombres y Apellidos)
      if ($vDoiDat['DOCVENXX'] != "") {
        $qVenDat  = "SELECT ";
        $qVenDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX ";
        $qVenDat .= "FROM $cAlfa.SIAI0150 ";
        $qVenDat .= "WHERE ";
        $qVenDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vDoiDat['DOCVENXX']}\" AND ";
        $qVenDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ";
        $xVenDat  = f_MySql("SELECT","",$qVenDat,$xConexion01,"");
        $nVenDat  = mysql_num_rows($xVenDat);
        if ($nVenDat > 0) {
          $vVenDat = mysql_fetch_array($xVenDat);
        }
      }

			##Cargo Variables para Impresion de Datos de Do ##
			$cTasCam          = $vDoiDat['TCATASAX']; //Tasa de Cambio
			$cDocTra          = $vDoiDat['DGEDTXXX']; //Documento de Transporte
			$cBultos          = $vIteDat['itebulxx']; //Bultos
			$cPesBru          = $vIteDat['itepbrxx']; //Peso Bruto
			$nValAdu          = $vDtaDat['dtafobxx'];
			$cOpera           = "CIF"; 							 // CIF
			$cPedido          = $vDoiDat['DOIPEDXX'];
			$cAduana          = $vAdmIng['ODIDESXX'];
			$cVendedor        = $vVenDat['CLINOMXX'];
			$cProveedor       = $vProNom['PIENOMXX'];
			$cMedioTransporte = $vMedTra['MTRDESXX'];
			$cPaisOrigen      = $vPaiOrg['PAIDESXX'];
			$cLugarIngreso    = $vLugIng['LINDESXX'];
			$cContenedor      = $vDoiDat['DOICONNU'];
			##Fin Cargo Variables para Impresion de Datos de Do ##

		break;
		case "OTROS":
			$cMedioTransporte  = "NA";
			$cPaisOrigen       = "NA";
			$cLugarIngreso     = "NA";
			$cTitulo           = "";
		break;
	}//switch (){
	##Fin Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##

  /**
   * Si la Variable cVendedor viene vacia cargo el Primer vendedor de la Tabla SIAI0150
   */
  if($cVendedor == ""){
    $qTerceros  = "SELECT CLIVENXX ";
    $qTerceros .= "FROM $cAlfa.SIAI0150 ";
    $qTerceros .= "WHERE ";
    $qTerceros .= "CLIIDXXX = \"{$_POST['cTerId']}\" LIMIT 0,1 ";
    $xTerceros  = f_MySql("SELECT","",$qTerceros,$xConexion01,"");
    // f_Mensaje(__FILE__, __LINE__, $qTerceros."~".mysql_num_rows($xTerceros));
    $vTerceros = mysql_fetch_array($xTerceros);
    $mVendedores = explode("~", $vTerceros['CLIVENXX']);
    if(count($mVendedores) > 0){
      $qVenDat  = "SELECT ";
      $qVenDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX ";
      $qVenDat .= "FROM $cAlfa.SIAI0150 ";
      $qVenDat .= "WHERE ";
      $qVenDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"$mVendedores[0]\" AND ";
      $qVenDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
      $xVenDat  = f_MySql("SELECT","",$qVenDat,$xConexion01,"");
      $nVenDat  = mysql_num_rows($xVenDat);
      if ($nVenDat > 0) {
        $vVenDat   = mysql_fetch_array($xVenDat);
        $cVendedor = $vVenDat['CLINOMXX'];
      }
    }
  }

  #Agrupo Ingresos Propios
  $mIP = array();
  for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
    $vDatosIp = array();
    $cObs = "|".$_POST['cSerId_IPA'.($i+1)]."~".$_POST['cFcoId_IPA'.($i+1)]."~".$_POST['cComObs_IPA'.($i+1)]."|";;
    $vDatosIp = f_Cantidad_Ingreso_Propio($cObs,'',$_POST['cSucId_IPA'.($i+1)],$_POST['cDosNro_IPA'.($i+1)],$_POST['cDosSuf_IPA'.($i+1)]);

    $mIP[$_POST['cComId_IPA'.($i+1)]]['ctoidxxx']  = $_POST['cComId_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comobsxx']  = $vDatosIp[0];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvlrxx'] += $_POST['nComVlr_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['compivax']  = $_POST['nComPIva_IPA'.($i+1)]; // Porcentaje IVA
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvlr01'] += $_POST['nComVIva_IPA'.($i+1)]; // Valor Iva
    //Cantidad FE
    $mIP[$_POST['cComId_IPA'.($i+1)]]['unidadfe']  = $vDatosIp[2];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['canfexxx'] += $vDatosIp[1];

    //Cantidad por condicion especial
    for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
      $mIP[$_POST['cComId_IPA'.($i+1)]]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] += $vDatosIp[3][$nP]['valpdfxx'];
    }
  } ## for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) { ##

	$mIngPro = array();
	foreach ($mIP as $cKey => $mValores) {
		$mIngPro[count($mIngPro)] = $mValores;
  }

	##Traigo Condiciones Comerciales del Facturar A: ##
	$qCccDat  = "SELECT * ";
	$qCccDat .= "FROM $cAlfa.fpar0151 ";
	$qCccDat .= "WHERE ";
	$qCccDat .= "$cAlfa.fpar0151.cliidxxx = \"{$_POST['cTerIdInt']}\" AND ";
	$qCccDat .= "$cAlfa.fpar0151.regestxx = \"ACTIVO\" ";
	$xCccDat  = f_MySql("SELECT","",$qCccDat,$xConexion01,"");
	$nFilCcc  = mysql_num_rows($xCccDat);
	if ($nFilCcc > 0) {
		$vCccDat = mysql_fetch_array($xCccDat);
	}
	##Traigo Condiciones Comerciales del Facturar A:##
	// f_Mensaje(__FILE__,__LINE__,$vCccDat['cccimpro']);

	##Traigo el codigo de la Unidad de medida por Concepto
	$qCtoCon  = "SELECT ";
	$qCtoCon .= "ctoidxxx, ";
	$qCtoCon .= "ctoclapr, ";
	$qCtoCon .= "cceidxxx, ";
	$qCtoCon .= "umeidxxx, ";
	$qCtoCon .= "ctochald ";
	$qCtoCon .= "FROM $cAlfa.fpar0121 ";
	$xCtoCon  = mysql_query($qCtoCon, $xConexion01);
	//f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
	while ($xRC = mysql_fetch_assoc($xCtoCon)) {
		$vCtoCon["{$xRC['ctoidxxx']}"] = $xRC;
	}
	##Fin Traigo el codigo de la Unidad de medida por Concepto

	##Codigo para imprimir los ingresos para terceros ##
	$mIngTer = array();

	if($vCccDat['cccimpro'] == "CONSOLIDADO") {
		// f_Mensaje(__FILE__,__LINE__,"entre consolidado");
		for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {

			$vTercero = explode("^",$_POST['cComObs_PCCA'.($i+1)]);

      //Busco por Cuenta PUC y Concepto ID --> en los conceptos contables (fpar0119 y fpar0121)
      $qConCon  = "SELECT cacidxxx ";
      $qConCon .= "FROM $cAlfa.fpar0119 ";
      $qConCon .= "WHERE ";
      $qConCon .= "$cAlfa.fpar0119.ctoidxxx = \"{$_POST['cComId_PCCA'.($i+1)]}\" AND ";
      $qConCon .= "$cAlfa.fpar0119.pucidxxx = \"{$_POST['cPucId_PCCA'.($i+1)]}\" LIMIT 0,1 ";
      $xConCon  = f_MySql("SELECT","",$qConCon,$xConexion01,"");
      $vConCon  = mysql_fetch_array($xConCon);
      // f_Mensaje(__FILE__,__LINE__,$qConCon."~".mysql_num_rows($xConCon));

      if (mysql_num_rows($xConCon) == 0) {
        $qConCon  = "SELECT cacidxxx ";
        $qConCon .= "FROM $cAlfa.fpar0121 ";
        $qConCon .= "WHERE ";
        $qConCon .= "$cAlfa.fpar0121.ctoidxxx = \"{$_POST['cComId_PCCA'.($i+1)]}\" AND ";
        $qConCon .= "$cAlfa.fpar0121.pucidxxx = \"{$_POST['cPucId_PCCA'.($i+1)]}\" LIMIT 0,1 ";
        $xConCon  = f_MySql("SELECT","",$qConCon,$xConexion01,"");
        $vConCon  = mysql_fetch_array($xConCon);
        // f_Mensaje(__FILE__,__LINE__,$qConCon."~".mysql_num_rows($xConCon));
      }

			if($vConCon['cacidxxx'] != ""){

				$nSwitch_Find = 0;
				for ($j=0;$j<count($mIngTer);$j++) {
					//f_Mensaje(__FILE__,__LINE__,$_POST['cComId_PCCA'.($i+1)]. " - ".$mIngTer[$j]['cComId']);
          //agrupando por categoria
					if ($mIngTer[$j]['cCacId'] == $vConCon['cacidxxx']) {
						$nSwitch_Find = 1;
						$cComCsc3 = ($_POST['cComDocIn_PCCA'.($i+1)] != "") ?  $_POST['cComDocIn_PCCA'.($i+1)] : $_POST['cComCsc3_PCCA'.($i+1)];
						$mIngTer[$j]['cComCsc3'] .= ((strlen($mIngTer[$nInd_mIngTer]['cComCsc3'])+strlen("/".$_POST['cComCsc3_PCCA'.($i+1)])) <= 40) ? "/".$cComCsc3 : "";
						$mIngTer[$j]['nComVlr']  += $_POST['nComVlr_PCCA'.($i+1)];
						$mIngTer[$j]['nBaseIva'] += $_POST['nBaseIva_PCCA'.($i+1)];
						$mIngTer[$j]['nVlrIva']  += $_POST['nVlrIva_PCCA'.($i+1)];
					}
				}

				if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura

				  /*** Busco la categoria concepto a la que pertenece ***/
          $qCatCon  = "SELECT cacidxxx,cacdesxx ";
          $qCatCon .= "FROM $cAlfa.fpar0144 ";
          $qCatCon .= "WHERE ";
          $qCatCon .= "$cAlfa.fpar0144.cacidxxx = \"{$vConCon['cacidxxx']}\" LIMIT 0,1 ";
          $xCatCon  = f_MySql("SELECT","",$qCatCon,$xConexion01,"");
          $vCatCon  = mysql_fetch_array($xCatCon);

					$nInd_mIngTer = count($mIngTer);

					$mIngTer[$nInd_mIngTer]['cComObs']  = $vCatCon['cacdesxx'];
					$mIngTer[$nInd_mIngTer]['cTerNom']  = trim($vTercero[1]);
					$mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId_PCCA'  .($i+1)];
					$mIngTer[$nInd_mIngTer]['cTerId']   = trim($vTercero[0]);
					$mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA' .($i+1)];
					$mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nBaseIva_PCCA'.($i+1)];
					$mIngTer[$nInd_mIngTer]['nVlrIva']  = $_POST['nVlrIva_PCCA' .($i+1)];
					$mIngTer[$nInd_mIngTer]['cTipo']    = $_POST['cTipo_PCCA'   .($i+1)];
					$mIngTer[$nInd_mIngTer]['cCacId']   = $vCatCon['cacidxxx']; // $mIngTer[$j]['cCacId']: Codigo de la categoria concepto
					$mIngTer[$nInd_mIngTer]['cUniMed']  = ($vCtoCon["{$_POST['cComId_PCCA'.($i+1)]}"]['umeidxxx'] != '') ? $vCtoCon["{$_POST['cComId_PCCA'.($i+1)]}"]['umeidxxx'] : "A9"; // Unidad de medida
				}
			}else{
				// f_Mensaje(__FILE__,__LINE__,"Sin Categoria");
				$_POST['cCacId_PCCA' .($i+1)] = "NO"; //Bandera para indicar los conceptos que NO estan clasificados por categorias, para agregarlos al final de la matriz de terceros
			}
		}//for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
	}

  //Agrupando los conceptos no consolidados
  for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {

    if($vCccDat['cccimpro'] != "CONSOLIDADO") {
      $_POST['cCacId_PCCA' .($i+1)] = "NO";
    }

    if($_POST['cCacId_PCCA' .($i+1)] == "NO") {

      //Si el concepto es de pago de tributos se agrupa por concepto y descripcion
      $cSwitch_Comprobante_Pago_Impuestos = "NO";
      $qComAju  = "SELECT * ";
      $qComAju .= "FROM $cAlfa.fpar0117 ";
      $qComAju .= "WHERE ";
      $qComAju .= "comidxxx = \"{$_POST['cComId3_PCCA'.($i+1)]}\" AND ";
      $qComAju .= "comcodxx = \"{$_POST['cComCod3_PCCA'.($i+1)]}\" AND ";
      $qComAju .= "comtipxx = \"PAGOIMPUESTOS\" AND ";
      $qComAju .= "regestxx = \"ACTIVO\" LIMIT 0,1";
      $xComAju  = f_MySql("SELECT","",$qComAju,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qComAju." ~ ".mysql_num_rows($xComAju));

      if (mysql_num_rows($xComAju) == 1) {

        $qCtoCba  = "SELECT * ";
        $qCtoCba .= "FROM $cAlfa.fpar0119 "; // Aqui no aplica la busqueda contra la fpar0121
        $qCtoCba .= "WHERE ";
        $qCtoCba .= "pucidxxx = \"{$_POST['cPucId_PCCA'.($i+1)]}\" AND ";
        $qCtoCba .= "ctocomxx LIKE \"%{$_POST['cComId3_PCCA'.($i+1)]}~{$_POST['cComCod3_PCCA'.($i+1)]}%\" AND ";
        $qCtoCba .= "ctoidxxx = \"{$_POST['cComId_PCCA'.($i+1)]}\" AND ";
        $qCtoCba .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xCtoCba  = f_MySql("SELECT","",$qCtoCba,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qCtoCba." ~ ".mysql_num_rows($xCtoCba));

        if (mysql_num_rows($xCtoCba) == 1) {
          $vCtoCba = mysql_fetch_array($xCtoCba);
          if ($vCtoCba['ctoptaxg'] == "SI" || $vCtoCba['ctoptaxl'] == "SI") {
            $cSwitch_Comprobante_Pago_Impuestos = "SI";
          } else {
            $cSwitch_Comprobante_Pago_Impuestos = "NO";
          }
        }
      }

      // f_Mensaje(__FILE__,__LINE__,"Sin Categoria ". ($i+1));
      $vTercero = explode("^",$_POST['cComObs_PCCA'.($i+1)]);
      $mComObs_IP = stripos($_POST['cComObs_PCCA'.($i+1)], "[");

      $nSwitch_Find = 0;
      for ($j=0;$j<count($mIngTer);$j++) {
        //f_Mensaje(__FILE__,__LINE__,$_POST['cComId_PCCA'.($i+1)]. " - ".$mIngTer[$j]['cComId']);

        $nAgrupar = 0;
        if ($cSwitch_Comprobante_Pago_Impuestos == "NO") {
          //Agrupando por concepto
          if ($_POST['cComId_PCCA'.($i+1)] == $mIngTer[$j]['cComId'] /*&& trim($vTercero[0]) == $mIngTer[$j]['cTerId']*/) {
            $nAgrupar = 1;
          }
        } else {
          //Agrupando por concepto y descripcion
          if ($_POST['cComId_PCCA'.($i+1)] == $mIngTer[$j]['cComId'] && trim($vTercero[0]) == trim($mIngTer[$j]['cComObs'])) {
            $nAgrupar = 1;
          }
        }

        if ($nAgrupar == 1) {
          $nSwitch_Find = 1;
          $cComCsc3 = ($_POST['cComDocIn_PCCA'.($i+1)] != "") ?  $_POST['cComDocIn_PCCA'.($i+1)] : $_POST['cComCsc3_PCCA'.($i+1)];
          $mIngTer[$j]['cComCsc3'] .= ((strlen($mIngTer[$nInd_mIngTer]['cComCsc3'])+strlen("/".$_POST['cComCsc3_PCCA'.($i+1)])) <= 40) ? "/".$cComCsc3 : "";
          $mIngTer[$j]['nComVlr']   += $_POST['nComVlr_PCCA'.($i+1)];
          $mIngTer[$j]['nBaseIva']  += $_POST['nBaseIva_PCCA'.($i+1)];
          $mIngTer[$j]['nVlrIva']   += $_POST['nVlrIva_PCCA'.($i+1)];
        }
      }

      if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
        $nInd_mIngTer = count($mIngTer);
        $mIngTer[$nInd_mIngTer]['cComObs']  = trim($vTercero[0]);
        $mIngTer[$nInd_mIngTer]['cTerNom']  = trim($vTercero[1]);
        $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId_PCCA'  .($i+1)];
        $mIngTer[$nInd_mIngTer]['cTerId']   = trim($vTercero[0]);
        $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA' .($i+1)];
        $mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nBaseIva_PCCA'.($i+1)];
        $mIngTer[$nInd_mIngTer]['nVlrIva']  = $_POST['nVlrIva_PCCA'.($i+1)];
				$mIngTer[$nInd_mIngTer]['cTipo']    = $_POST['cTipo_PCCA'.($i+1)];
				$mIngTer[$nInd_mIngTer]['cUniMed']  = ($vCtoCon["{$_POST['cComId_PCCA'.($i+1)]}"]['umeidxxx'] != '') ? $vCtoCon["{$_POST['cComId_PCCA'.($i+1)]}"]['umeidxxx'] : "A9"; // Unidad de medida
      }
    }
  }//for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {


	##Fin Codigo para imprimir los ingresos para terceros ##
	// echo '<pre>';
	// print_r($mIngTer);
	// echo '</pre>';
	// die();
	#Fin Agrupo Ingresos Propios
	//////////////////////////////////////////////////////////////////////////

	## Creo Matriz para Guardar Dirección, Telefono y Fax de la Sucursal
	switch ($cSucId) {
		case 'BOG':
			$vSucursal[$vCocDat['sucidxxx']]['direccion'] = "Cr 100 25B 40";
			$vSucursal[$vCocDat['sucidxxx']]['telefono'] = "4042904";
			$vSucursal[$vCocDat['sucidxxx']]['fax'] = "";
		break;
		case 'BAQ':
			$vSucursal[$vCocDat['sucidxxx']]['direccion'] = "Cr 30 Av. Hamburgo Ed. Administrativo Zona Franca Ps 2 ";
			$vSucursal[$vCocDat['sucidxxx']]['telefono'] = "3447648 - 3447649";
			$vSucursal[$vCocDat['sucidxxx']]['fax'] = "";
		break;
		case 'BUN':
			$vSucursal[$vCocDat['sucidxxx']]['direccion'] = "Cl 8 3 50 Of 302/303 Ed Roldan ";
			$vSucursal[$vCocDat['sucidxxx']]['telefono'] = "2433624 - 2408077";
			$vSucursal[$vCocDat['sucidxxx']]['fax'] = "";
		break;
		case 'CLO':
			$vSucursal[$vCocDat['sucidxxx']]['direccion'] = "Cl 10 4 47 Of 503";
			$vSucursal[$vCocDat['sucidxxx']]['telefono'] = "8822860 - 8822171";
			$vSucursal[$vCocDat['sucidxxx']]['fax'] = "";
		break;
		case 'CTG':
			$vSucursal[$vCocDat['sucidxxx']]['direccion'] = "Manga Cr 27 29 43 Unidad 4 Zona Franca ";
			$vSucursal[$vCocDat['sucidxxx']]['telefono'] = "6609298";
			$vSucursal[$vCocDat['sucidxxx']]['fax'] = "";
		break;
		case 'MZL':
			$vSucursal[$vCocDat['sucidxxx']]['direccion'] = "Cr 23 63 15 Of 405 Ed. El Castillo";
			$vSucursal[$vCocDat['sucidxxx']]['telefono'] = "8862450";
			$vSucursal[$vCocDat['sucidxxx']]['fax'] = "";
		break;
		case 'MDE':
		case 'PUU':
			$vSucursal[$vCocDat['sucidxxx']]['direccion'] = "Cr 43A 1A Sur 69 Of 703 Ed. Tempo";
			$vSucursal[$vCocDat['sucidxxx']]['telefono'] = "3520687";
			$vSucursal[$vCocDat['sucidxxx']]['fax'] = "";
		break;
		case 'PEI':
			$vSucursal[$vCocDat['sucidxxx']]['direccion'] = "Cr 8 20 67 Of 403 Ed. Banco Unión Colombiano";
			$vSucursal[$vCocDat['sucidxxx']]['telefono'] = "3240923";
			$vSucursal[$vCocDat['sucidxxx']]['fax'] = "";
		break;
		case 'SMR':
			$vSucursal[$vCocDat['sucidxxx']]['direccion'] = "Cl 10C 1C 51";
			$vSucursal[$vCocDat['sucidxxx']]['telefono'] = "4214299";
			$vSucursal[$vCocDat['sucidxxx']]['fax'] = "";
		break;
		case 'IPI':
			$vSucursal[$vCocDat['sucidxxx']]['direccion'] = "Cr 6 14 33 Of 402 Ed. Bastidas";
			$vSucursal[$vCocDat['sucidxxx']]['telefono'] = "7732715";
			$vSucursal[$vCocDat['sucidxxx']]['fax'] = "";
		break;
		default:
			$vSucursal[$vCocDat['sucidxxx']]['direccion'] = "Cr 100 25B 40";
			$vSucursal[$vCocDat['sucidxxx']]['telefono'] = "4042904";
			$vSucursal[$vCocDat['sucidxxx']]['fax'] = "";
		break;
	}
	## Fin Matriz para Guardar Dirección, Telefono y Fax de la Sucursal

	##Traigo la Forma de Pago##
  $cFormaPag = "";
  if (isset($_POST['cComFpag'])) {
    //Buscando descripcion
    $cFormaPag = ($_POST['cComFpag'] == 1) ? "Contado" : "Crédito";
  }

	$cRoot = $_SERVER['DOCUMENT_ROOT'];
	define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
	require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

	class PDF extends FPDF {
		function Header() {
			global $cAlfa;   global $cRoot;   global $cPlesk_Skin_Directory; global $posy; global $posx;
			global $gCcoId;  global $gSccId;  global $gMesDes; global $gAnoDes; global $gMesHas; global $gAnoHas;
			global $cUsrNom; global $cCcoDes; global $cScoDes; global $nPag;    global $vAgeDat; global $vCocDat;
			global $vResDat; global $cDocTra; global $cTasCam; global $cBultos; global $cPesBru; global $vCliDat;
			global $cDocId;  global $vCiuDat; global $vDceDat; global $cOpera;  global $nValAdu;  global $vCccDat;
			global $cCscFac; global $vConDat; global $cPaisOrigen; global $cPedido; global $vNomComercial;  global $vSucursal;
			global $cProveedor; global $cVendedor; global $cMedioTransporte; global $cPaisOrigen; global $cLugarIngreso; global $cContenerdor;
			global $vIdContacto; global $cEstiloLetra; global $cAduana;  global $_COOKIE; global $vSucDesc; global $vNomTer; global $cContenedor;
			global $cTitulo; global $cDocSuf; global $cFormaPag; global $bImpFin;

			##Impresion Datos Generales Factura ##
			$posy = 40;
			$posx = 10;
			$bImpFin = false;

			//Membrete de la factura
			$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/membrete_roldan.jpg',0,0,215,283);

			$this->Rect($posx, $posy+5, 97, 20);
			$this->Rect($posx+100, $posy+5, 95, 20);

			//Recuadro lado izquierdo
			$this->setXY($posx,$posy+5);
			$this->SetFont($cEstiloLetra,'B',8);
			$this->Cell(23,4,"Ciudad:",0,0,'L');
			$this->Cell(65,4,$vSucDesc['sucdesxx']." - COLOMBIA",0,0,'L');

			$this->setXY($posx,$posy+9);
			$this->SetFont($cEstiloLetra,'B',8);
			$this->Cell(23,4,utf8_decode("Dirección:"),0,0,'L');
			$this->SetFont($cEstiloLetra,'',8);
			$this->Cell(65,4,utf8_decode($vSucursal[$vCocDat['sucidxxx']]['direccion']),0,0,'L');

			$this->setXY($posx,$posy+13);
			$this->SetFont($cEstiloLetra,'B',8);
			$this->Cell(23,4,utf8_decode("Teléfono:"),0,0,'L');
			$this->SetFont($cEstiloLetra,'',8);
			$this->Cell(65,4,utf8_decode($vSucursal[$vCocDat['sucidxxx']]['telefono']),0,0,'L');

			$this->setXY($posx,$posy+17);
			$this->SetFont($cEstiloLetra,'B',8);
			$this->Cell(23,4,"Forma de Pago:",0,0,'L');
			$this->SetFont($cEstiloLetra,'',8);
			$this->Cell(65,4,utf8_decode($cFormaPag),0,0,'L');

			$this->setXY($posx,$posy+21);
			$this->SetFont($cEstiloLetra,'B',8);
			$this->Cell(23,4,"Medio de Pago:",0,0,'L');
			$this->SetFont($cEstiloLetra,'',8);
			$this->Cell(65,4,utf8_decode(ucwords(strtolower(substr($_POST['cMePagDes'], 0, 45)))),0,0,'L');

			//Recuadro lado derecho
			$this->setXY($posx+100,$posy+5);
			$this->SetFont($cEstiloLetra,'B',8);
			$this->Cell(40,4,"Factura de Venta No.:",0,0,'L');
			$this->Cell(55,4,strtoupper($vResDat['resprexx']."-XXXXXX"),0,0,'L');

			$this->setXY($posx+100,$posy+9);
			$this->SetFont($cEstiloLetra,'B',8);
			$this->Cell(40,4,utf8_decode("Fecha de Emisión:"),0,0,'L');
			$this->SetFont($cEstiloLetra,'',8);
			$this->Cell(55,4,ucfirst(f_Fecha_Letras(date('Y-m-d'))),0,0,'L');

			$this->setXY($posx+100,$posy+13);
			$this->SetFont($cEstiloLetra,'B',8);
			$this->Cell(40,4,utf8_decode("Hora de Emisión:"),0,0,'L');
			$this->SetFont($cEstiloLetra,'',8);
			$this->Cell(55,4,date('H:m:s'),0,0,'L');

			$this->setXY($posx+100,$posy+17);
			$this->SetFont($cEstiloLetra,'B',8);
			$this->Cell(40,4,utf8_decode("Fecha de Vencimiento:"),0,0,'L');
			$this->SetFont($cEstiloLetra,'',8);
			$this->Cell(55,4,ucfirst(f_Fecha_Letras(date('Y-m-d', strtotime("+{$_POST['cTerPla']} day") ))),0,0,'L');

			$this->setXY($posx+100,$posy+21);
			$this->SetFont($cEstiloLetra,'B',8);
			$this->Cell(40,4,utf8_decode("Tasa de Cambio:"),0,0,'L');
			$this->SetFont($cEstiloLetra,'',8);
			$this->Cell(55,4,number_format($_POST['nTasaCambio'],2,",","."),0,0,'L');

			//Columna 1
			$posy += 27;
			$this->setXY($posx, $posy);
			$this->SetFont($cEstiloLetra, 'B', 6);
			$this->Cell(15, 4, utf8_decode("Cliente"), 0, 0, 'L');
			$this->SetFont($cEstiloLetra, '', 6);
			$this->MultiCell(60, 4, substr($vCliDat['CLINOMXX'], 0, 65), 0, 'L');				
			$this->setX($posx);
			$this->SetFont($cEstiloLetra, 'B', 6);
			$this->Cell(15, 4, utf8_decode("NIT"), 0, 0, 'L');
			$this->SetFont($cEstiloLetra, '', 6);
			if($vCliDat['TDIIDXXX'] == "21" ||
				 $vCliDat['TDIIDXXX'] == "22" ||
				 $vCliDat['TDIIDXXX'] == "41" ||
				 $vCliDat['TDIIDXXX'] == "42"){
				$this->MultiCell(60, 4, $vCliDat['CLIIDXXX'], 0, 'L');
			}else{
				$this->MultiCell(60, 4, $vCliDat['CLIIDXXX']."-".f_Digito_Verificacion($vCliDat['CLIIDXXX']), 0, 'L');
			}

			$this->setX($posx);
			$this->SetFont($cEstiloLetra, 'B', 6);
			$this->Cell(15, 4, utf8_decode("Dirección"), 0, 0, 'L');
			$this->SetFont($cEstiloLetra, '', 6);
			$this->MultiCell(60, 4, substr($vCliDat['CLIDIRXX'], 0, 65), 0, 'L');	
			$this->setX($posx);
			$this->SetFont($cEstiloLetra, 'B', 6);
			$this->Cell(15, 4, utf8_decode("Teléfono"), 0, 0, 'L');
			$this->SetFont($cEstiloLetra, '', 6);
			$this->MultiCell(60, 4, $vCliDat['CLITELXX'], 0, 'L');
			$this->setX($posx);
			$this->SetFont($cEstiloLetra, 'B', 6);
			$this->Cell(15, 4, utf8_decode("Ciudad"), 0, 0, 'L');
			$this->SetFont($cEstiloLetra, '', 6);
			$this->MultiCell(30, 4, $vCiuDat['CIUDESXX'], 0, 'L');
			$this->setX($posx);
			$this->SetFont($cEstiloLetra, 'B', 6);
			$this->Cell(15, 4, utf8_decode("Comercial"), 0, 0, 'L');
			$this->SetFont($cEstiloLetra, '', 6);
			$this->MultiCell(60, 4, substr(utf8_encode($cVendedor), 0, 65), 0, 'L');
			$this->Ln(2);
			$posyy = $this->GetY();

			//Columna 2
			$this->setXY($posx + 75, $posy);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(20, 4, utf8_decode("$cTitulo"), 0, 0, 'L');
			$this->SetFont('Arial', '', 6);
			if ($cTitulo != "") {
				$this->MultiCell(45, 4, substr($vNomTer['CLINOMXX'], 0, 60), 0, 'L');
			} else {
				$this->MultiCell(45, 4, "", 0, 'L');
			}
			$this->setX($posx + 75);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(20, 4, utf8_decode("Vía"), 0, 0, 'L');
			$this->SetFont('Arial', '', 6);
			$this->MultiCell(45, 4, $cMedioTransporte, 0, 'L');
			$this->setX($posx + 75);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(20, 4, utf8_decode("D.O."), 0, 0, 'L');
			$this->SetFont('Arial', '', 6);
			$this->MultiCell(45, 4, "$cDocId-$cDocSuf", 0, 'L');
			$this->setX($posx + 75);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(20, 4, utf8_decode("Dto. Transporte"), 0, 0, 'L');
			$this->SetFont('Arial', '', 6);
			$this->MultiCell(45, 4, $cDocTra, 0, 'L');
			$this->setX($posx + 75);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(20, 4, utf8_decode("Pedido"), 0, 0, 'L');
			$this->SetFont('Arial', '', 6);
			$this->MultiCell(45, 4, $cPedido, 0, 'L');
			$this->setX($posx + 75);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(20, 4, utf8_decode("Proveedor"), 0, 0, 'L');
			$this->SetFont('Arial', '', 6);
			$this->MultiCell(45, 4, $cProveedor, 0, 'L');
			$this->Ln(2);
			$posyy = $this->GetY() > $posyy ? $this->GetY() : $posyy;

			//Columna 3
			$this->setXY($posx + 140, $posy);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(15, 4, utf8_decode("Procesos"), 0, 0, 'L');
			$this->SetFont('Arial', '', 6);
			switch ($vDceDat['doctipxx']) {
				case 'IMPORTACION':
					$strProcesos = "IMPORTACIÓN";
					break;
				case 'EXPORTACION':
					$strProcesos = "EXPORTACIÓN";
					break;
				default:
					$strProcesos = $vDceDat['doctipxx'];
					break;
			}
			$this->MultiCell(40, 4, utf8_decode($vDceDat['doctipxx'] == "TRANSITO" ? "DTA" : $strProcesos), 0, 'L');
			$this->setX($posx + 140);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(15, 4, utf8_decode("Desde"), 0, 0, 'L');
			$this->SetFont('Arial', '', 6);
			$this->MultiCell(40, 4, $cPaisOrigen, 0, 'L');
			$this->setX($posx + 140);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(15, 4, utf8_decode("Hasta"), 0, 0, 'L');
			$this->SetFont('Arial', '', 6);
			$this->MultiCell(40, 4, $cLugarIngreso, 0, 'L');
			$this->setX($posx + 140);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(15, 4, utf8_decode("Contenedor"), 0, 0, 'L');
			$this->SetFont('Arial', '', 6);
			$vContenedor = explode(",",trim($cContenedor,","));
			$this->MultiCell(40, 4, $vContenedor[0], 0, 'L');
			$this->setX($posx + 140);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(15, 4, utf8_decode("Bultos"), 0, 0, 'L');
			$this->SetFont('Arial', '', 6);
			$this->MultiCell(40, 4, number_format($cBultos,0,",","."), 0, 'L');
			$this->setX($posx + 140);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(15, 4, utf8_decode("Kilos"), 0, 0, 'L');
			$this->SetFont('Arial', '', 6);
			$this->MultiCell(40, 4, number_format($cPesBru,0,",","."), 0, 'L');
			$this->Ln(2);
			$posyy = $this->GetY() > $posyy ? $this->GetY() : $posyy;

			$this->setXY($posx, $posyy);
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(10, 5, utf8_decode("ITEM"), 0, 0, 'C');
			$this->Cell(20, 5, utf8_decode("CÓDIGO"), 0, 0, 'C');
			$this->Cell(15, 5, utf8_decode("CANTIDAD"), 0, 0, 'C');
			$this->Cell(75, 5, utf8_decode("DETALLE"), 0, 0, 'C');
			$this->Cell(20, 5, utf8_decode("UNIDAD"), 0, 0, 'C');
			$this->Cell(27, 5, utf8_decode("USD"), 0, 0, 'C');
			$this->Cell(28, 5, utf8_decode("COP"), 0, 0, 'C');

			$intTamRect = 200 - $posyy;
			$intTamLine = $posyy + $intTamRect;
			$this->Rect($posx, $posyy, 195, $intTamRect);
			$this->Line($posx + 10, $posyy, $posx + 10, $intTamLine);
			$this->Line($posx + 30, $posyy, $posx + 30, $intTamLine);
			$this->Line($posx + 45, $posyy, $posx + 45, $intTamLine);
			$this->Line($posx + 120, $posyy, $posx + 120, $intTamLine);
			$this->Line($posx + 140, $posyy, $posx + 140, $intTamLine);
			$this->Line($posx + 167, $posyy, $posx + 167, $intTamLine);

			$this->Line($posx, $this->GetY()+5, $posx+195, $this->GetY()+5);
			$this->posy = $this->GetY();

		}//Function Header

		function Footer() {
		  global $cRoot;   global $cPlesk_Skin_Directory;   global $vCocDat; global $bImpFin;
			global $cEstiloLetra; global $posy; global $posx; global $vCliDat; global $vResDat;

			$posy = 202;
			$posx = 10;

			$this->SetFont($cEstiloLetra,'B',7);
			$this->setXY($posx+3,$posy);
			$this->Cell(115,4,utf8_decode("AUTORETENEDORES DE RENTA, Resolución DIAN No 005745 de Julio 26 de 2012"),0,0,'C');

			$posy += 3;
			$this->SetFont($cEstiloLetra,'',6);
			$this->setXY($posx+3,$posy);
			$this->Cell(115,4,utf8_decode("IVA RÉGIMEN COMÚN                  ACTIVIDAD ECONÓMICA 5229"),0,0,'C');

			$posy += 3;
			$this->SetFont($cEstiloLetra,'',6);
			$this->setXY($posx+3,$posy);
			$this->MultiCell(115,2.7,utf8_decode("Agentes Retenedores de IVA Conforme Numeral 7 Art 437-2 E.T.N\nExentos de RETENCIÓN en pagos a terceros Art. 8 Decreto 2775 de 1983\nAUTORETENEDORES DE ICA en las siguientes Ciudades: Barranquilla, Santa Marta, Pereira y Cartagena."),0,'C');
			$posy = $this->getY()+5;

			$this->SetFont($cEstiloLetra,'',6);
			$this->setXY($posx,$posy);
			$this->MultiCell(195,2.7,utf8_decode("AUTORIZO CONSULTA Y REPORTE A CENTRALES DE RIESGO\nLA PRESENTE FACTURA DE VENTA SE ASIMILA EN TODOS LOS EFECTOS A UNA LETRA DE CAMBIO ARTÍCULO 774 DEL CÓDIGO DE COMERCIO\nFAVOR APLICAR LA RETENCIÓN DE ICA, CORRESPONDIENTE A LA CIUDAD DE FACTURACIÓN, SI USTED ES CONTRIBUYENTE EN ESTA CIUDAD\nAPLICA DECRETO 1154 DE AGOSTO 2020 ACEPTACIÓN EXPRESA DE LA FACTURA ELECTRÓNICA, DENTRO DE LOS TRES (3) DÍAS HÁBILES SIGUIENTES A SU RECEPCIÓN"),0,'C');

			$posy = 202;
			$this->setXY($posx,$posy);
			$this->SetFont($cEstiloLetra,'B',7);
			$this->Cell(115,4,"",0,0,'R');
			$this->Cell(36,4,"TOTAL",0,0,'L');
			$this->Cell(30,4, ($bImpFin == false) ? "0,00": "",0,0,'R');
			$posy += 4;

			$this->setXY($posx,$posy);
			$this->SetFont($cEstiloLetra,'B',7);
			$this->Cell(115,4,"",0,0,'R');
			$this->Cell(36,4,"ANTICIPO",0,0,'L');
			$this->Cell(30,4, ($bImpFin == false) ? "0,00": "",0,0,'R');
			$posy += 4;

			$this->setXY($posx,$posy);
			$this->SetFont($cEstiloLetra,'B',7);
			$this->Cell(115,4,"",0,0,'R');
			$this->Cell(36,4,"TOTAL A PAGAR",0,0,'L');
			$this->Cell(30,4, ($bImpFin == false) ? "0,00": "",0,0,'R');
			$posy += 4;

			$this->setXY($posx,$posy);
			$this->SetFont($cEstiloLetra,'B',7);
			$this->Cell(115,4,"",0,0,'R');
			$this->Cell(36,4,"SALDO A FAVOR DEL CLIENTE",0,0,'L');
			$this->Cell(30,4, ($bImpFin == false) ? "0,00": "",0,0,'R');
			$posy += 4;

			//Traigo numero de Meses entre Desde y Hasta
			$dFechaInicial = date_create($vResDat['resfdexx']);
			$dFechaFinal = date_create($vResDat['resfhaxx']);
			$nDiferencia = date_diff($dFechaInicial, $dFechaFinal);
			$nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m;

			$posy = 233;
			$this->SetFont($cEstiloLetra,'B',7);
			$this->setXY($posx,$posy);
			$this->Cell(195,4,utf8_decode("Resolución de Facturación Electrónica No. {$vResDat['residxxx']} desde ".$vResDat['resfdexx']." al ".$vResDat['resfhaxx']."  desde {$vResDat['resprexx']}-{$vResDat['resdesxx']} hasta {$vResDat['resprexx']}-{$vResDat['reshasxx']} Vigencia: ".$nMesesVigencia." meses"),0,0,'C');

			// logo pse
			$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_pse.jpg', 150, 245, 15);

			$posy = 263;
			$this->SetFont('Arial', 'B', 6);
			$this->setXY($posx, $posy+11);
			$this->Cell(195, 4, utf8_decode('página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'C');
		}

		function Setwidths($w) {
			//Set the array of column widths
			$this->widths=$w;
		}

		// rota la celda
		function RotatedText($x,$y,$txt,$angle){
			//Text rotated around its origin
			$this->Rotate($angle,$x,$y);
			$this->Text($x,$y,$txt);
			$this->Rotate(0);
		}

		// rota la celda
		var $angle=0;
		function Rotate($angle,$x=-1,$y=-1){
			if($x==-1)
				$x=$this->x;
			if($y==-1)
				$y=$this->y;
			if($this->angle!=0)
				$this->_out('Q');
			$this->angle=$angle;
			if($angle!=0) {
				$angle*=M_PI/180;
				$c=cos($angle);
				$s=sin($angle);
				$cx=$x*$this->k;
				$cy=($this->h-$y)*$this->k;
				$this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
			}
		}

		function SetAligns($a){
			//Set the array of column alignments
			$this->aligns=$a;
		}

		function Row($data){
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data);$i++)
				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
			$h=3.35*$nb;
			//Issue a page break first if needed
			$this->CheckPageBreak($h);
			//Draw the cells of the row
			for($i=0;$i<count($data);$i++) {
				$w=$this->widths[$i];
				$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
				//Save the current position
				$x=$this->GetX();
				$y=$this->GetY();
				//Draw the border
				//$this->Rect($x,$y,$w,$h);
				//Print the text
				$this->MultiCell($w,3.35,$data[$i],0,$a);
				//Put the position to the right of the cell
				$this->SetXY($x+$w,$y);
			}
			//Go to the next line
			$this->Ln($h);
		}

		function CheckPageBreak($h){
			//If the height h would cause an overflow, add a new page immediately
			if($this->GetY()+$h>$this->PageBreakTrigger)
			$this->AddPage($this->CurOrientation);
		}

		function NbLines($w,$txt){
			//Computes the number of lines a MultiCell of width w will take
			$cw=&$this->CurrentFont['cw'];
			if($w==0)
				$w=$this->w-$this->rMargin-$this->x;
			$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			$s=str_replace("\r",'',$txt);
			$nb=strlen($s);
			if($nb>0 and $s[$nb-1]=="\n")
			    $nb--;
			$sep=-1;
			$i=0;
			$j=0;
			$l=0;
			$nl=1;
			while($i<$nb){
				$c=$s[$i];
				if($c=="\n"){
					$i++;
					$sep=-1;
					$j=$i;
					$l=0;
					$nl++;
					continue;
				}
				if($c==' ')
					$sep=$i;
				$l+=$cw[$c];
				if($l>$wmax){
					if($sep==-1){
						if($i==$j)
							$i++;
					}
					else
						$i=$sep+1;
					$sep=-1;
					$j=$i;
					$l=0;
					$nl++;
				}
				else
					$i++;
				}
				return $nl;
		}

	}//class PDF extends FPDF {

	$pdf = new PDF('P','mm','Letter');  //Error al invocar la clase
	$pdf->AddFont($cEstiloLetra,'','arial.php');
	$pdf->AliasNbPages();
	$pdf->SetMargins(0,0,0);
	$pdf->SetAutoPageBreak(0,22);

	$pdf->AddPage();

	##Inicializando variables por copia##
	$nTotPag1  = "";	$cSaldo       = "";	$cNeg = "";
	$nIva      = 0;   $nSubToFacIva = 0;  $nSubToFac  = 0;
	$nSubToIP  = 0; 	$nSubToIPIva  = 0;	$nTotPcc    = 0;
	$nSubToPcc = 0;		$nSubToPcc    = 0;	$nSubTotPcc = 0;

	##Busco valor de IVA ##
	if ($vCocDat['CLINRPXX'] == "SI") {
		$nIva = 0;
		for ($k=0;$k<count($mIngPro);$k++) {
			if($mIngPro[$k]['comctocx'] == 'IVAIP'){
				$nIva += $mIngPro[$k]['comvlrxx'];
			}
		}
		$nSubToIPIva = $nIva;
	}

	$posy   = $pdf->posy+5;
	$posx   = 10;
	$posFin = 195;
	$pyy    = $posy;
	// Contador de items
	$nConItem = 0;
	$pdf->setXY($posx,$posy);

	##Imprimo Pagos a Terceros ##
	if(count($mIngTer) > 0){
		$nSubTotPcc = 0;
		$pdf->setXY($posx,$pyy);
		$pdf->SetWidths(array(10,20,15,75,20,27,28));
		$pdf->SetAligns(array("C","C","C","L","C","R","R"));

		// Imprimo Titulo Pagos a Terceros
		$pdf->SetFont($cEstiloLetra,'B',6);
		$pdf->setX($posx+45);
		$pdf->Cell(50,4,"PAGOS A TERCEROS EFECTUADOS POR SU CUENTA",0,0,'L');
		$pdf->Ln(4);
		for($i=0;$i<count($mIngTer);$i++){
			$nConItem++;
			$pyy = $pdf->GetY();
			if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
				$pdf->AddPage();
				$posy = $pdf->posy+5;
				$posx = 10;
				$pyy  = $posy;
				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->setXY($posx,$posy);
			}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion
			$nSubTotPcc += $mIngTer[$i]['nComVlr'];

			//Consulto la descripcion de la Unidad de medida
			$cUniMedi  = "";
			$qUniMedi  = "SELECT umedesxx ";
			$qUniMedi .= "FROM $cAlfa.fpar0157 ";
			$qUniMedi .= "WHERE ";
			$qUniMedi .= "umeidxxx = \"{$mIngTer[$i]['cUniMed']}\" LIMIT 0,1";
			$xUniMedi  = mysql_query($qUniMedi, $xConexion01);
			//f_Mensaje(__FILE__,__LINE__,$qUniMedi." ~ ".mysql_num_rows($xUniMedi));
			while ($xRUM = mysql_fetch_assoc($xUniMedi)) {
				$cUniMedi = strtolower($xRUM['umedesxx']);
			}

			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->setX($posx);
			$mIngTer[$i]['cComObs'] = (trim($mIngTer[$i]['cComObs']) == "GRAVAMEN ARANCELARIO")  ? "ARANCEL DECLARACION DE IMPORTACION" : $mIngTer[$i]['cComObs'];
			$mIngTer[$i]['cComObs'] = (trim($mIngTer[$i]['cComObs']) == "IMPUESTO A LAS VENTAS") ? "IVA DECLARACION DE IMPORTACION"     : $mIngTer[$i]['cComObs'];
			$pdf->Row(array($nConItem,
											"",
											"1",
											$mIngTer[$i]['cComObs'],
											utf8_decode(ucwords($cUniMedi)),
											$vCliDat['CLINRPXX'] == "SI" ? number_format($mIngTer[$i]['nComVlr'],2,',','.') : "",
											$vCliDat['CLINRPXX'] != "SI" ? number_format($mIngTer[$i]['nComVlr'],2,',','.') : ""));

		}//for($i=0;$i<count($mIngTer);$i++){

		if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$posy = $pdf->posy+5;
			$posx = 10;
			$pyy = $posy;
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->setXY($posx,$posy);
		}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		$pyy = $pdf->GetY();
		if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$posy = $pdf->posy+5;
			$posx = 10;
			$pyy = $posy;
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->setXY($posx,$posy);
		}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		##Imprimo Subtotal de Pagos a Terceros ##
		$nTotPcc = $nSubTotPcc + $nSubToPcc;
		$pdf->SetFont($cEstiloLetra,'B',6);
		$pdf->setXY($posx+45,$pyy);
		$pdf->Cell(92,4,"TOTAL PAGOS A TERCEROS",0,0,'L');
		$pdf->Cell(30,4,$vCliDat['CLINRPXX'] == "SI" ? number_format($nTotPcc,2,',','.') : "",0,0,'R');
		$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? number_format($nTotPcc,2,',','.') : "",0,0,'R');
		$pyy += 8;
		##Fin Imprimo Subtotal de Pagos a Terceros ##
	}//if(count($mIngTer) > 0){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
	##Fin Imprimo Pagos a Terceros ##

	if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
		$pdf->AddPage();
		$posy = $pdf->posy+5;
		$posx = 10;
		$pyy = $posy;
		$pdf->SetFont($cEstiloLetra,'',6);
		$pdf->setXY($posx,$posy);
	}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

	// f_Mensaje(__FILE__,__LINE__,count($mIngPro));
	$nSubToIP = 0;
	$nSubToIPIva = 0;
	if(count($mIngPro) > 0){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
		if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$posy = $pdf->posy+5;
			$posx = 10;
			$pyy = $posy;
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->setXY($posx,$posy);
		}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		##Imprimo Ingresos Propios##
		$pdf->setXY($posx,$pyy);
		$pdf->SetWidths(array(10,20,15,75,20,27,28));
			$pdf->SetAligns(array("C","C","C","L","C","R","R"));

		// Imprimo Titulo Pagos por Cuenta Propia
		$pdf->SetFont($cEstiloLetra,'B',6);
		$pdf->setX($posx+45);
		$pdf->Cell(50,4,utf8_decode("SERVICIOS POR INTERMEDIACIÓN"),0,0,'L');
		$pdf->Ln(4);
		// hago dos veces el mismo for para separar los ingresos GRAVADOS y luego los NO GRAVADOS
		for($k=0;$k<(count($mIngPro));$k++) {
			$pyy = $pdf->GetY();
			if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
				$pdf->AddPage();
				$posy = $pdf->posy+5;
				$posx = 10;
				$pyy = $posy;
				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->setXY($posx,$posy);
			}

			if($mIngPro[$k]['comvlr01'] != 0 ) {
				//Contar Items
				$nConItem++;
				$nSubToIP += $mIngPro[$k]['comvlrxx'];
				$nSubToIPIva += $mIngPro[$k]['comvlr01'];
        $pdf->SetFont($cEstiloLetra,'',6);

				$cValor = ""; $cValCon = ""; $cValCif = "";
        //Mostrando cantidades por tipo de cantidad
        foreach ($mIngPro[$k]['itemcanx'] as $cKey => $cValue) {
          // Personalizacion de la descripcion por base de datos e informacion adicional
          if($cKey == "FOB") {
            if (($cValue+0) > 0) {
              $cValor  = " FOB: ($".number_format($cValue,2,'.',',');
              $cValor .= ($mIngPro[$k]['itemcanx']['TRM'] > 0) ? " TRM: $".number_format($mIngPro[$k]['itemcanx']['TRM'],2,'.',',') : "";
              $cValor .= ")";
            }
          } elseif ($cKey == "CIF") {
            if ($cCifUsd > 0) {
              $cValCif  = ($cCifUsd > 0) ? " CIF: (USD ".number_format($cCifUsd,2,'.',',').")" : "";
              $cValCif .= ($cValTrm > 0) ? " TRM: ".number_format($cValTrm,2,'.',',') : "";
            }
          } elseif ($cKey == "CONTENEDORES_DE_20") {
            $cValCon .= " CONTENEDORES DE 20: (".$cValue.")";
          } elseif ($cKey == "CONTENEDORES_DE_40") {
            $cValCon .= " CONTENEDORES DE 40: (".$cValue.")";
          }elseif ($cKey == "UNIDADES_DE_CARGA_SUELTA") {
            $cValCon .= " UNIDADES DE CARGA SUELTA: (".$cValue.")";
          }elseif($cKey == "TRM") {
            //No hace nada, porque se incluyo con el key FOB
          }else {
            if ($cKey == "DIM"    || $cKey == "DAV"    || $cKey == "VUCE" || 
                $cKey == "HORAS"  || $cKey == "PIEZAS" || $cKey == "DEX"  || 
                $cKey == "SERIAL" || $cKey == "CANTIDAD") {
              $cValor = " $cKey: (".$cValue.")";
            } else {
              $cValor = " CANTIDAD: (".$cValue.")";
            }
          }
        }

        $cValor = ($cValCif != "" && $nCantDo == 1) ? $cValCif.$cValor : $cValor;
        $cValor = ($cValCon != "") ? $cValCon.$cValor : $cValor;

				## Logica para obtener base de gravados y no gravados
				$nBaseGravados   +=  (($mIngPro[$k]['comvlr01'] + 0) > 0 ) ? $mIngPro[$k]['comvlrxx'] : 0;
				$nBaseNoGravados +=  (($mIngPro[$k]['comvlr01'] + 0) == 0 ) ? $mIngPro[$k]['comvlrxx'] : 0;

				//Consulto la descripcion de la Unidad de medida
				$cUniMedi  = "";
				$qUniMedi  = "SELECT umedesxx ";
				$qUniMedi .= "FROM $cAlfa.fpar0157 ";
				$qUniMedi .= "WHERE ";
				$qUniMedi .= "umeidxxx = \"{$mIngPro[$k]['unidadfe']}\" LIMIT 0,1";
				$xUniMedi  = mysql_query($qUniMedi, $xConexion01);
				//f_Mensaje(__FILE__,__LINE__,$qUniMedi." ~ ".mysql_num_rows($xUniMedi));
				while ($xRUM = mysql_fetch_assoc($xUniMedi)) {
					$cUniMedi = strtolower($xRUM['umedesxx']);
				}

				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->setX($posx);
				$pdf->Row(array($nConItem,
												$mIngPro[$k]['ctoidxxx'],
												$mIngPro[$k]['canfexxx'],
												trim($mIngPro[$k]['comobsxx'].$cValor),
												utf8_decode(ucwords($cUniMedi)),
												$vCliDat['CLINRPXX'] == "SI" ? number_format($mIngPro[$k]['comvlrxx'],2,',','.') : "",
												$vCliDat['CLINRPXX'] != "SI" ? number_format($mIngPro[$k]['comvlrxx'],2,',','.') : ""));

			}//if($mIngPro[$k]['comctocx'] == 'IP'){
		}## for($k=$nPosIP;$k<(count($mIngPro));$k++) { ##

		for($k=0;$k<(count($mIngPro));$k++) {
		  $pyy = $pdf->GetY();
			if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
				$pdf->AddPage();
				$posy = $pdf->posy+5;
				$posx = 10;
				$pyy = $posy;
				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->setXY($posx,$posy);
			}

			// if($mIngPro[$k]['comctocx'] == "IP" && $mIngPro[$k]['comvlr01'] == 0 ) {
			if($mIngPro[$k]['comvlr01'] == 0 ) {
				$nConItem++;
				$nSubToIP += $mIngPro[$k]['comvlrxx'];
				$nSubToIPIva += $mIngPro[$k]['comvlr01'];
				$pdf->SetFont($cEstiloLetra,'',6);

				$cValor = ""; $cValCon = ""; $cValCif = "";
        //Mostrando cantidades por tipo de cantidad
        foreach ($mIngPro[$k]['itemcanx'] as $cKey => $cValue) {
          // Personalizacion de la descripcion por base de datos e informacion adicional
          if($cKey == "FOB") {
            if (($cValue+0) > 0) {
              $cValor  = " FOB: ($".number_format($cValue,2,'.',',');
              $cValor .= ($mIngPro[$k]['itemcanx']['TRM'] > 0) ? " TRM: $".number_format($mIngPro[$k]['itemcanx']['TRM'],2,'.',',') : "";
              $cValor .= ")";
            }
          } elseif ($cKey == "CIF") {
            if ($cCifUsd > 0) {
              $cValCif  = ($cCifUsd > 0) ? " CIF: (USD ".number_format($cCifUsd,2,'.',',').")" : "";
              $cValCif .= ($cValTrm > 0) ? " TRM: ".number_format($cValTrm,2,'.',',') : "";
            }
          } elseif ($cKey == "CONTENEDORES_DE_20") {
            $cValCon .= " CONTENEDORES DE 20: (".$cValue.")";
          } elseif ($cKey == "CONTENEDORES_DE_40") {
            $cValCon .= " CONTENEDORES DE 40: (".$cValue.")";
          }elseif ($cKey == "UNIDADES_DE_CARGA_SUELTA") {
            $cValCon .= " UNIDADES DE CARGA SUELTA: (".$cValue.")";
          }elseif($cKey == "TRM") {
            //No hace nada, porque se incluyo con el key FOB
          }else {
            if ($cKey == "DIM"    || $cKey == "DAV"    || $cKey == "VUCE" || 
                $cKey == "HORAS"  || $cKey == "PIEZAS" || $cKey == "DEX"  || 
                $cKey == "SERIAL" || $cKey == "CANTIDAD") {
              $cValor = " $cKey: (".$cValue.")";
            } else {
              $cValor = " CANTIDAD: (".$cValue.")";
            }
          }
        }

        $cValor = ($cValCif != "" && $nCantDo == 1) ? $cValCif.$cValor : $cValor;
        $cValor = ($cValCon != "") ? $cValCon.$cValor : $cValor;

				## Logica para obtener base de gravados y no gravados
				$nBaseGravados   +=  (($mIngPro[$k]['comvlr01'] + 0) > 0) ? $mIngPro[$k]['comvlrxx'] : 0;
				$nBaseNoGravados +=  (($mIngPro[$k]['comvlr01'] + 0) == 0) ? $mIngPro[$k]['comvlrxx'] : 0;

				//Consulto la descripcion de la Unidad de medida
				$cUniMedi  = "";
				$qUniMedi  = "SELECT umedesxx ";
				$qUniMedi .= "FROM $cAlfa.fpar0157 ";
				$qUniMedi .= "WHERE ";
				$qUniMedi .= "umeidxxx = \"{$mIngPro[$k]['unidadfe']}\" LIMIT 0,1";
				$xUniMedi  = mysql_query($qUniMedi, $xConexion01);
				//f_Mensaje(__FILE__,__LINE__,$qUniMedi." ~ ".mysql_num_rows($xUniMedi));
				while ($xRUM = mysql_fetch_assoc($xUniMedi)) {
					$cUniMedi = strtolower($xRUM['umedesxx']);
				}

				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->setX($posx);
				$pdf->Row(array($nConItem,
												$mIngPro[$k]['ctoidxxx'],
												$mIngPro[$k]['canfexxx'],
												trim($mIngPro[$k]['comobsxx'].$cValor),
												utf8_decode(ucwords($cUniMedi)),
												$vCliDat['CLINRPXX'] == "SI" ? number_format($mIngPro[$k]['comvlrxx'],2,',','.') : "",
												$vCliDat['CLINRPXX'] != "SI" ? number_format($mIngPro[$k]['comvlrxx'],2,',','.') : ""));

			}//if($mIngPro[$k]['comctocx'] == 'IP'){
		}## for($k=$nPosIP;$k<(count($mIngPro));$k++) { ##
    ##Fin Imprimo Ingresos Propios##
		$pyy = $pdf->GetY();
		if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$posy = $pdf->posy+5;
			$posx = 10;
			$pyy = $posy;
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->setXY($posx,$posy);
		}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		##Imprimo Subtotal de Ingresos Propios ##
		$pdf->SetFont($cEstiloLetra,'B',6);
		$pdf->setXY($posx,$pyy);
		$pdf->Cell(10,5,$nConItem,"T",0,'C');
		$pdf->Cell(35,4,"",0,0,'C');
		$pdf->Cell(75,4,"TOTAL SERVICIOS",0,0,'L');
		$pdf->Cell(20,4,"",0,0,'L');
		$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? number_format($nSubToIP,2,',','.') : "",0,0,'R');
		$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? number_format($nSubToIP,2,',','.') : "",0,0,'R');
		$pyy += 4;
		##Imprimo Subtotal de Ingresos Propios ##
	}//if(count($mPccIng) > 0){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS

	##Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##*/

	##Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##
	$nSubToFac = 0;
	$nSubToFac = $nTotPcc + $nSubToIP;
	$nSubToFacIva += $nSubToIPIva;
	##Fin Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##

	// ##Busco valor de IVA ##
	// $nIva = 0;
	// for ($k=0;$k<count($mIngPro);$k++) {
		// if($mIngPro[$k]['comctocx'] == 'IVAIP'){
			// $nIva += $mIngPro[$k]['comvlrxx'];
		// }
	// }
	// ##Fin Busco Valor de IVA ##


	##Busco valor de Anticipo ##
	$cNegativo = "";
	$cNeg = "";

	##Bloque que acumula retenciones por valor de porcentaje##
  $mRetFte      = array();
  $mRetIca      = array();
  $mAutoRetIca  = array();
  $mRetIva      = array();
  $mReteCre     = array();
  for ($i=0;$i<$_POST['nSecuencia_IPA'];$i++) {
    if ($_POST['nPorFte_IPA'.($i+1)] > 0) {
      $nSwitch_Encontre_RetFte = 0;
      for ($j=0;$j<count($mRetFte);$j++) {
        if ($_POST['nPorFte_IPA'.($i+1)] == $mRetFte[$j]['pucretxx']) {
          $nSwitch_Encontre_RetFte = 1;
          $mRetFte[$j]['comvlrxx']  += ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrFte_IPA'.($i+1)],2) : round($_POST['nVlrFte_IPA'.($i+1)],0);
          $mRetFte[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
        }
      }

      if ($nSwitch_Encontre_RetFte == 0) { // No lo encontro en la matriz para pintar en la factura
        $nInd_mRetFte = count($mRetFte);
        $mRetFte[$nInd_mRetFte]['tipretxx'] = "FUENTE";
        $mRetFte[$nInd_mRetFte]['pucretxx'] = $_POST['nPorFte_IPA' .($i+1)];
        $mRetFte[$nInd_mRetFte]['comvlrxx'] = ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrFte_IPA' .($i+1)],2) : round($_POST['nVlrFte_IPA'.($i+1)],0);
        $mRetFte[$nInd_mRetFte]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
      }
    }

    if ($_POST['nPorIca_IPA'.($i+1)] > 0) {
      $nSwitch_Encontre_RetIca = 0;
      for ($j=0;$j<count($mRetIca);$j++) {
        if ($_POST['nPorIca_IPA'.($i+1)] == $mRetIca[$j]['pucretxx']) {
          $nSwitch_Encontre_RetIca = 1;
          $mRetIca[$j]['comvlrxx']  += ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrIca_IPA'.($i+1)],2) : round($_POST['nVlrIca_IPA'.($i+1)],0);
          $mRetIca[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
        }
      }

      if ($nSwitch_Encontre_RetIca == 0) { // No lo encontro en la matriz para pintar en la factura
        $nInd_mRetIca = count($mRetIca);
        $mRetIca[$nInd_mRetIca]['tipretxx'] = "ICA";
        $mRetIca[$nInd_mRetIca]['pucretxx'] = $_POST['nPorIca_IPA' .($i+1)];
        $mRetIca[$nInd_mRetIca]['comvlrxx'] = ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrIca_IPA' .($i+1)],2) : round($_POST['nVlrIca_IPA'.($i+1)],0);
        $mRetIca[$nInd_mRetIca]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
      }
    }

    // Auto Retencion de ICA
    if ($_POST['nPorAIca_IPA'.($i+1)] > 0) {
      $nSwitch_Encontre_ARetIca = 0;
      for ($j=0;$j<count($mAutoRetIca);$j++) {
        if ($_POST['nPorAIca_IPA'.($i+1)] == $mAutoRetIca[$j]['pucretxx']) {
          $nSwitch_Encontre_ARetIca = 1;
          $mAutoRetIca[$j]['comvlrxx']  += ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrAIca_IPA'.($i+1)],2) : round($_POST['nVlrAIca_IPA'.($i+1)],0);
          $mAutoRetIca[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
        }
      }

      if ($nSwitch_Encontre_ARetIca == 0) { // No lo encontro en la matriz para pintar en la factura
        $nInd_mAutoRetIca = count($mAutoRetIca);
        $mAutoRetIca[$nInd_mAutoRetIca]['tipretxx'] = "AICA";
        $mAutoRetIca[$nInd_mAutoRetIca]['pucretxx'] = $_POST['nPorAIca_IPA' .($i+1)];
        $mAutoRetIca[$nInd_mAutoRetIca]['comvlrxx'] = ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrAIca_IPA' .($i+1)],2) : round($_POST['nVlrAIca_IPA'.($i+1)],0);
        $mAutoRetIca[$nInd_mAutoRetIca]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
      }
    }

    if ($_POST['nPorIva_IPA'.($i+1)] > 0) {
      $nSwitch_Encontre_RetIva = 0;
      for ($j=0;$j<count($mRetIva);$j++) {
        if ($_POST['nPorIva_IPA'.($i+1)] == $mRetIva[$j]['pucretxx']) {
          $nSwitch_Encontre_RetIva = 1;
          $mRetIva[$j]['comvlrxx']  += ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrIva_IPA'.($i+1)],2) : round($_POST['nVlrIva_IPA'.($i+1)],0);
          $mRetIva[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
        }
      }

      if ($nSwitch_Encontre_RetIva == 0) { // No lo encontro en la matriz para pintar en la factura
        $nInd_mRetIva = count($mRetIva);
        $mRetIva[$nInd_mRetIva]['tipretxx'] = "IVA";
        $mRetIva[$nInd_mRetIva]['pucretxx'] = $_POST['nPorIva_IPA' .($i+1)];
        $mRetIva[$nInd_mRetIva]['comvlrxx'] = ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrIva_IPA' .($i+1)],2) : round($_POST['nVlrIva_IPA'.($i+1)],0);
        $mRetIva[$nInd_mRetIva]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
      }
    }

    if ($_POST['nPorCre_IPA'.($i+1)] > 0) {
      $nSwitch_Encontre_RetCree = 0;
      for ($j=0;$j<count($mRetCree);$j++) {
        if ($_POST['nPorCre_IPA'.($i+1)] == $mRetCree[$j]['pucretxx']) {
          $nSwitch_Encontre_RetCree = 1;
          $mRetCree[$j]['comvlrxx']  += ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrCre_IPA'.($i+1)],2) : round($_POST['nVlrCre_IPA'.($i+1)],0);
          $mRetCree[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
        }
      }

      if ($nSwitch_Encontre_RetCree == 0) { // No lo encontro en la matriz para pintar en la factura
        $nInd_mRetCree = count($mRetCree);
        $mRetCree[$nInd_mRetCree]['tipretxx'] = "CREE";
        $mRetCree[$nInd_mRetCree]['pucretxx'] = $_POST['nPorCre_IPA' .($i+1)];
        $mRetCree[$nInd_mRetCree]['comvlrxx'] = ($vCliDat['CLINRPXX'] == "SI") ? round($_POST['nVlrCre_IPA' .($i+1)],2) : round($_POST['nVlrCre_IPA'.($i+1)],0);
        $mRetCree[$nInd_mRetCree]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
      }
    }
  }
  ##Fin Bloque que acumula retenciones por valor de porcentaje##

	$posy = $pdf->GetY()+4;
	if($posy > $posFin) {//Validacion para siguiente pagina si se excede espacio de impresion
		$pdf->AddPage();
		$posy = $pdf->posy+5;
		$posx = 10;
		$pdf->SetFont($cEstiloLetra,'',6);
		$pdf->setXY($posx,$posy);
	}

	## Imprimo IVA
	$pdf->SetFont($cEstiloLetra,'',6);
	$pdf->setXY($posx+45,$posy);
	$pdf->Cell(75,4,"IVA (Base ".number_format($nBaseGravados+$nBaseNoGravados,2,',','.').")",0,0,'L');
	$pdf->Cell(20,4,"",0,0,'C');
	$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? number_format($nSubToFacIva,2,',','.') : "",0,0,'R');
	$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? number_format($nSubToFacIva,2,',','.') : "",0,0,'R');
	$posy += 4;

	if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
		$pdf->AddPage();
		$posy = $pdf->posy+5;
		$posx = 10;
		$pdf->SetFont($cEstiloLetra,'',6);
		$pdf->setXY($posx,$posy);
	}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

	$pdf->setXY($posx+45,$posy);
	$pdf->SetFont($cEstiloLetra,'B',6);
	$pdf->Cell(75,4,"SUBTOTAL",0,0,'L');
	$pdf->Cell(20,4,"",0,0,'C');
	$pdf->SetFont($cEstiloLetra,'B',6);
	$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? number_format(abs($nSubToFac+$nSubToFacIva),2,',','.') : "",0,0,'R');
	$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? number_format(abs($nSubToFac+$nSubToFacIva),2,',','.') : "",0,0,'R');
	$posy += 4;

	if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
		$pdf->AddPage();
		$posy = $pdf->posy+5;
		$posx = 10;
		$pdf->SetFont($cEstiloLetra,'',6);
		$pdf->setXY($posx,$posy);
	}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

	// Validar si todas las retenciones de RETEICA se anulan con las AUTORETEICA.
  $nAuxRetIca = 0;
  for($nRI=0;$nRI<count($mRetIca);$nRI++){
    for($nARI = 0; $nARI < count($mAutoRetIca); $nARI++){
      if($mRetIca[$nRI]['pucretxx'] == $mAutoRetIca[$nARI]['pucretxx'] && $mRetIca[$nRI]['comvlrxx'] == $mAutoRetIca[$nARI]['comvlrxx']){
        $nAuxRetIca++;
        $nARI = count($mAutoRetIca);
      }
    }
  }

	if((count($mRetIca) > 0 && $nAuxRetIca != count($mRetIca)) || count($mRetIva) > 0 ){
	  $pdf->setXY($posx+45,$posy);
    $pdf->SetFont($cEstiloLetra,'B',6);
		$pdf->Cell(75,4,"RETENCIONES",0,0,'L');
    $pdf->Cell(20,4,"",0,0,'R');
    $pdf->Cell(27,4,"",0,0,'R');
    $pdf->Cell(28,4,"",0,0,'R');
    $posy += 4;
	}

	##RETENCIONES##
	/*for($i=0;$i<count($mRetFte);$i++){
		if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$posy = $pdf->posy+5;
			$posx = 10;
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->setXY($posx,$posy);
		}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		$pdf->setXY($posx+45,$posy);
		$pdf->SetFont($cEstiloLetra,'',6);
		$pdf->Cell(95,5,"Retencion ".$mRetFte[$i]['tipretxx']." del ".($mRetFte[$i]['pucretxx']+0)."%",0,0,'L');
		$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? number_format(abs($mRetFte[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
		$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? number_format(abs($mRetFte[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
		$posy +=4;
	}*/

	for($i=0;$i<count($mRetIca);$i++){
		if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$posy = $pdf->posy+5;
			$posx = 10;
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->setXY($posx,$posy);
		}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		// Si el valor de AICA y ICA son iguales, no se imprime en la factura.
    $nRetIca = 0;
    for($nARI = 0; $nARI < count($mAutoRetIca); $nARI++){
      if($mRetIca[$i]['pucretxx'] == $mAutoRetIca[$nARI]['pucretxx'] && $mRetIca[$i]['comvlrxx'] == $mAutoRetIca[$nARI]['comvlrxx']){
        $nRetIca = 1;
        $nARI = count($mAutoRetIca);
      }
    }

    if($nRetIca == 0){
			if($mRetIca[$i]['comvlrxx'] > 0){
				$pdf->setXY($posx+45,$posy);
				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->Cell(75,5,"Retencion ".$mRetIca[$i]['tipretxx']." del ".($mRetIca[$i]['pucretxx']+0)."%",0,0,'L');
				$pdf->Cell(20,5,"",0,0,'L');
				$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? number_format(abs($mRetIca[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
				$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? number_format(abs($mRetIca[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
				$posy +=4;
			}
    }
	}

	for($i=0;$i<count($mRetIva);$i++){
		if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$posy = $pdf->posy+5;
			$posx = 10;
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->setXY($posx,$posy);
		}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		if($mRetIva[$i]['comvlrxx'] > 0){
			$pdf->setXY($posx+45,$posy);
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->Cell(75,5,"Retencion ".$mRetIva[$i]['tipretxx']." del ".($mRetIva[$i]['pucretxx']+0)."%",0,0,'L');
			$pdf->Cell(20,5,"",0,0,'L');
			$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? number_format(abs($mRetIva[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
			$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? number_format(abs($mRetIva[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
			$posy +=4;
		}
	}

	/*for($i=0;$i<count($mRetCree);$i++){
		if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$posy = $pdf->posy+5;
			$posx = 10;
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->setXY($posx,$posy);
		}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		$pdf->setXY($posx+45,$posy);
		$pdf->SetFont($cEstiloLetra,'',6);
		$pdf->Cell(95,5,"Retencion ".$mRetCree[$i]['tipretxx']." del ".($mRetCree[$i]['pucretxx']+0)."%",0,0,'L');
		$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? number_format(abs($mRetCree[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
		$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? number_format(abs($mRetCree[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
		$posy +=4;
	}*/
	##FIN RETENCIONES##

  $nTotLet = round($_POST['nPCCVNe'] + $_POST['nIPASub'] + $_POST['nIPAIva'] - ($_POST['nIPARFte'] + $_POST['nIPARCre'] + $_POST['nIPARIva'] + $_POST['nIPARIca']) + ($_POST['nIPAARFte'] + $_POST['nIPAARCre'] + $_POST['nIPAARIca']),2)+0;

	##Busco valor de Anticipo DIAN ##
	$nAnticipoDian = (abs($nTotLet) > abs($_POST['nIPAAnt'])) ? abs($_POST['nIPAAnt']) : abs($nTotLet);
	## Fin Busco valor de Anticipo DIAN ##

	$posy += 2;
	if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
		$pdf->AddPage();
		$posy = $pdf->posy+5;
		$posx = 10;
		$pdf->SetFont($cEstiloLetra,'',6);
		$pdf->setXY($posx,$posy);
	}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

	$pdf->setXY($posx+45,$posy);
	$pdf->SetFont($cEstiloLetra,'B',6);
	$pdf->Cell(75,4,"TOTAL",0,0,'L');
	$pdf->Cell(20,4,"",0,0,'C');
	$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? number_format(abs($nTotLet),2,',','.') : "",0,0,'R');
	$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? number_format(abs($nTotLet),2,',','.') : "",0,0,'R');
	$posy += 4;

	if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
		$pdf->AddPage();
		$posy = $pdf->posy+5;
		$posx = 10;
		$pdf->SetFont($cEstiloLetra,'',6);
		$pdf->setXY($posx,$posy);
	}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

	##Imprimo Valor en Letras##
	$cMoneda  = ($vCliDat['CLINRPXX'] != "SI") ? "PESO" : "DOLAR" ;
	$nTotPag1 = f_Cifra_Php(str_replace("-","",abs($nTotLet)), $cMoneda);
	$pdf->setXY($posx+45,$posy);
	$pdf->SetFont($cEstiloLetra,'',6);
	$pdf->MultiCell(75,3,utf8_decode(str_replace("DOLARES", "DOLÁRES", $nTotPag1)),0,'L');
	$posy = $pdf->getY()+1;

	##Imprimo Observacion##
	if($_POST['cComObs'] != ""){
		if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$posy = $pdf->posy+5;
			$posx = 10;
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->setXY($posx,$posy);
		}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion	

		$nObs = explode("~",f_Words($_POST['cComObs'],60));
		for ($n=0;$n<count($nObs) - 1;$n++) {
			if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
				$pdf->AddPage();
				$posy = $pdf->posy+5;
				$posx = 10;
				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->setXY($posx,$posy);
			}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->setXY($posx+45,$posy);
			if($n == 0){
				// f_Mensaje(__FILE__,__LINE__,$pdf->getY());
				$pdf->SetFont($cEstiloLetra,'B',6);
				$pdf->Cell(18,3,utf8_decode("DESCRIPCIÓN:"),0,0,'L');
				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->Cell(60,3,$nObs[$n],0,0,'L');
				$pdf->setXY($posx+104,$posy);
				$pdf->Cell(20,4,"",0,0,'R');
				$pdf->Cell(22,4,"",0,0,'R');
				$pdf->Cell(27,4,"",0,0,'R');
			}else{
				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->Cell(75,3,$nObs[$n],0,0,'L');
				$pdf->setXY($posx+104,$posy);
				$pdf->Cell(20,4,"",0,0,'R');
				$pdf->Cell(22,4,"",0,0,'R');
				$pdf->Cell(27,4,"",0,0,'R');
			}
			$posy+=4;
		}
	}

	if($_POST['nIPASal'] > 0){
		$cSaldo = "CARGO";
	}else{
		$cSaldo = "SU FAVOR";
	}

	$bImpFin = true;
	$posy = 202;
	$pdf->setXY($posx,$posy); // TOTAL
	$pdf->SetFont($cEstiloLetra,'B',7);
	$pdf->Cell(140,4,"",0,0,'R');
	$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? number_format(abs($nTotLet),2,',','.') : "",0,0,'R');
	$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? number_format(abs($nTotLet),2,',','.') : "",0,0,'R');
	$posy += 4;

	$pdf->setXY($posx,$posy); // ANTICIPO
	$pdf->SetFont($cEstiloLetra,'B',7);
	$pdf->Cell(140,4,"",0,0,'R');
	$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? number_format(abs($_POST['nIPAAnt']),2,',','.') : "",0,0,'R');
	$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? number_format(abs($_POST['nIPAAnt']),2,',','.') : "",0,0,'R');
	$posy += 4;

	$pdf->setXY($posx,$posy); // TOTAL A PAGAR
	$pdf->SetFont($cEstiloLetra,'B',7);
	$pdf->Cell(140,4,"",0,0,'R');
	if ($cSaldo == "CARGO") {
		$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? number_format(abs($_POST['nIPASal']),2,',','.') : "",0,0,'R');
		$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? number_format(abs($_POST['nIPASal']),2,',','.') : "",0,0,'R');
	} else {
		$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? "0,00" : "",0,0,'R');
		$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? "0,00" : "",0,0,'R');
	}
	$posy += 4;

	$pdf->setXY($posx,$posy);// SALDO A FAVOR DEL CLIENTE
	$pdf->SetFont($cEstiloLetra,'B',7);
	$pdf->Cell(140,4,"",0,0,'R');
	if ($cSaldo == "SU FAVOR") {
		$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? number_format(abs($_POST['nIPASal']),2,',','.') : "",0,0,'R');
		$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? number_format(abs($_POST['nIPASal']),2,',','.') : "",0,0,'R');
	} else {
		$pdf->Cell(27,4,$vCliDat['CLINRPXX'] == "SI" ? "0,00" : "",0,0,'R');
		$pdf->Cell(28,4,$vCliDat['CLINRPXX'] != "SI" ? "0,00" : "",0,0,'R');
	}

	$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	$pdf->Output($cFile);

	if (file_exists($cFile)){
		chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
	} else {
		f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
	}
	echo "<html><script>document.location='$cFile';</script></html>";

	function f_Fecha_Letras($xFecha){
		if ($xFecha==''){
			$xFecfor='';
		}else{
			$fano = substr ($xFecha, 0, 4);
			$fdia = substr ($xFecha, 8, 2);
			$fmes_antes = substr ($xFecha, 5, 2);
			if($fmes_antes=='01')
				$fmes="Enero";
			if($fmes_antes=='02')
				$fmes="Febrero";
			if($fmes_antes=='03')
				$fmes="Marzo";
			if($fmes_antes=='04')
				$fmes="Abril";
			if($fmes_antes=='05')
				$fmes="Mayo";
			if($fmes_antes=='06')
				$fmes="Junio";
			if($fmes_antes=='07')
				$fmes="Julio";
			if($fmes_antes=='08')
				$fmes="Agosto";
			if($fmes_antes=='09')
				$fmes="Septiembre";
			if($fmes_antes=='10')
				$fmes="Octubre";
			if($fmes_antes=='11')
				$fmes="Noviembre";
			if($fmes_antes=='12')
				$fmes="Diciembre";
			$xFecFor= $fmes." ".$fdia." de ".$fano;
		}
		return ($xFecFor);
	}
?>

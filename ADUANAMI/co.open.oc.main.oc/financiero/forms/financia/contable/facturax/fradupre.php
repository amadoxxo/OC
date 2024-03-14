
<?php
  /**
	 * Imprime Vista Previa Factura de Venta Aduanamientos.
	 * --- Descripcion: Permite Imprimir Vista Previa de la Factura de Venta.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 */
  include("../../../../libs/php/utility.php");

  $cRoot = $_SERVER['DOCUMENT_ROOT'];

  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

  $mPrn = explode("|",$prints);
  for ($nn=0;$nn<count($mPrn);$nn++) {
    if (strlen($mPrn[$nn]) > 0) {
      $vComp = explode("~",$mPrn[$nn]);
  		$cComId   = $vComp[0];
  		$cComCod  = $vComp[1];
  		$cComCsc  = $vComp[3];
  		$cComCsc2 = $vComp[3];
  		$cRegFCre = $vComp[4];
  		$cNewYear = date("Y");
	    //$cAno     = substr($cRegFCre,0,4);
    }
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

	## Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Aduanamientos Para encabezado de factura ##
	$qAgeDat  = "SELECT ";
	$qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
	$qAgeDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
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
  if ($nFilRes > 0) {
    $vResDat = mysql_fetch_array($xResDat);
  }
	##Fin Traigo Datos de la Resolucion ##

  ##Busco Ciudad por donde se esta facturando ##
  /*$qCcoDat  = "SELECT * ";
  $qCcoDat .= "FROM $cAlfa.fpar0008 ";
  $qCcoDat .= "WHERE ";
  $qCcoDat .= "$cAlfa.fpar0008.sucidxxx = \"{$_POST['cSucId']}\" AND ";
  $qCcoDat .= "$cAlfa.fpar0008.regestxx = \"ACTIVO\" LIMIT 0,1 ";
 	$xCcoDat  = f_MySql("SELECT","",$qCcoDat,$xConexion01,"");
  $nFilCco  = mysql_num_rows($xResDat);
  if ($nFilCco > 0) {
    $vCcoDat = mysql_fetch_array($xCcoDat);
  }*/
  ##Fin Busco Ciudad por donde se esta facturando ##

  ##Consulto en la SIAI0150 Datos del Facturado A: ##
  $qCliDat  = "SELECT ";
  $qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIDIRXX, ";
  $qCliDat .= "$cAlfa.SIAI0150.PAIIDXXX, ";
  $qCliDat .= "$cAlfa.SIAI0150.DEPIDXXX, ";
  $qCliDat .= "$cAlfa.SIAI0150.CIUIDXXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLITELXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIFAXXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLICONTX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLINRPXX  ";
	$qCliDat .= "FROM $cAlfa.SIAI0150 ";
	$qCliDat .= "WHERE ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$_POST['cTerIdInt']}\" AND ";
	$qCliDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
	$xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,$qCliDat);
  if (mysql_num_rows($xCliDat) > 0) {
    $vCliDat = mysql_fetch_array($xCliDat);
  }

  // Consulta para traer la descripcion del pais y mostrarla en el archivo PDF.
  $qDesCiu  = "SELECT CIUDESXX ";
  $qDesCiu .= "FROM $cAlfa.SIAI0055 ";
  $qDesCiu .= "WHERE ";
  $qDesCiu .= "PAIIDXXX = \"{$vCliDat['PAIIDXXX']}\" AND ";
  $qDesCiu .= "DEPIDXXX = \"{$vCliDat['DEPIDXXX']}\" AND ";
  $qDesCiu .= "CIUIDXXX = \"{$vCliDat['CIUIDXXX']}\"";
  $xDesCiu  = f_MySql("SELECT","",$qDesCiu, $xConexion01,"");
  $nDesCiu  = mysql_num_rows($xDesCiu);
  if ($nDesCiu > 0){
    $vDesCiu = mysql_fetch_array($xDesCiu);
  }
  // f_Mensaje(__FILE__,__LINE__,$qDesCiu."~".mysql_num_rows($xDesCiu)."~".$vDesCiu['CIUDESXX']);
  // Fin de consulta para traer la descripcion del pais y mostrarla en el archivo PDF.

  ##Consulto en la SIAI0150 Datos del Facturado A: ##

  ##Traigo Datos de Contacto del Facturado a ##
  if($vCliDat['CLICONTX'] <> ""){
  	$vContactos = explode("~",$vCliDat['CLICONTX']);
  	//f_Mensaje(__FILE__,__LINE__,count($vContactos));
  	if(count($vContactos) > 1){
  		$vIdContacto = $vContactos[1];
  	}else{
  		$vIdContacto = $vCliDat['CLICONTX'];
  	}

  }//if($vCocDat['CLICONTX'] <> ""){

  $qConDat  = "SELECT ";
  $qConDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS NOMBRE ";
  $qConDat .= "FROM $cAlfa.SIAI0150 ";
  $qConDat .= "WHERE ";
  $qConDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"$vIdContacto\" AND ";
  $qConDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ";
  //f_Mensaje(__FILE__,__LINE__,$qConDat);
 	$xConDat  = f_MySql("SELECT","",$qConDat,$xConexion01,"");
  $nFilCon  = mysql_num_rows($xConDat);
  if ($nFilCon > 0) {
    $vConDat = mysql_fetch_array($xConDat);
  }
  ##Fin Traigo Datos de Contacto del Facturado a ##

	##Traigo Primer Do para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##
	$cSucId  = "";
	$cDocId  = "";
	$cDocSuf = "";

	for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
    $cSucId   = $_POST['cSucId_DOS' .($i+1)];
    $cDocId   = $_POST['cDosNro_DOS'.($i+1)];
    $cDocSuf  = $_POST['cDosSuf_DOS'.($i+1)];
    $i = $_POST['nSecuencia_Dos'];
  }//for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {

  ##Fin Traigo Primer Do para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##

  ##Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##
  $qDceDat  = "SELECT $cAlfa.sys00121.*, ";
  $qDceDat .= "$cAlfa.fpar0008.sucdesxx, ";
  $qDceDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS IMPORTADOR ";
  $qDceDat .= "FROM $cAlfa.sys00121 ";
  $qDceDat .= "LEFT JOIN  $cAlfa.fpar0008 ON $cAlfa.sys00121.sucidxxx = $cAlfa.fpar0008.sucidxxx ";
  $qDceDat .= "LEFT JOIN  $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
  $qDceDat .= "WHERE ";
  $qDceDat .= "$cAlfa.sys00121.sucidxxx = \"$cSucId\" AND ";
  $qDceDat .= "$cAlfa.sys00121.docidxxx = \"$cDocId\" AND ";
  $qDceDat .= "$cAlfa.sys00121.docsufxx = \"$cDocSuf\" ";
  //f_Mensaje(__FILE__,__LINE__,$qDceDat);
 	$xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
  $nFilDce  = mysql_num_rows($xDceDat);
  if ($nFilDce > 0) {
    $vDceDat = mysql_fetch_array($xDceDat);
  }
  ##Fin Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##

  ##Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##
  $nValCif =0;
  switch ($vDceDat['doctipxx']){
  	case "IMPORTACION":
  		##Traigo Datos de la SIAI0200 DATOS DEL DO ##
  		$qDoiDat  = "SELECT ";
			$qDoiDat .= "$cAlfa.SIAI0200.DGEDTXXX, ";
			$qDoiDat .= "$cAlfa.SIAI0200.DGEBULXX, ";
			$qDoiDat .= "$cAlfa.SIAI0200.TCATASAX, ";
			$qDoiDat .= "$cAlfa.SIAI0200.DGEPBRXX, ";
			$qDoiDat .= "$cAlfa.SIAI0200.DOICON20, ";
			$qDoiDat .= "$cAlfa.SIAI0200.DOICON40, ";
			$qDoiDat .= "$cAlfa.SIAI0052.PAIDESXX, ";
			$qDoiDat .= "$cAlfa.SIAI0054.DEPDESXX  ";
			$qDoiDat .= "FROM $cAlfa.SIAI0200 ";
			$qDoiDat .= "LEFT JOIN $cAlfa.SIAI0052 ON $cAlfa.SIAI0200.PAIIDXXX = $cAlfa.SIAI0052.PAIIDXXX ";
			$qDoiDat .= "LEFT JOIN $cAlfa.SIAI0054 ON $cAlfa.SIAI0054.PAIIDXXX = \"CO\" AND ";
			$qDoiDat .= "$cAlfa.SIAI0200.DEPID2XX = $cAlfa.SIAI0054.DEPIDXXX ";
			$qDoiDat .= "WHERE $cAlfa.SIAI0200.DOIIDXXX = \"$cDocId\" AND ";
			$qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"$cDocSuf\" AND ";
			$qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"$cSucId\" ";
		  //f_Mensaje(__FILE__,__LINE__,$qDoiDat);
			$xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
			$nFilDoi  = mysql_num_rows($xDoiDat);
			if ($nFilDoi > 0) {
				$vDoiDat  = mysql_fetch_array($xDoiDat);
			}
			##Fin Traigo Datos de la SIAI0200 DATOS DEL DO ##

			##Cargo Variables para Impresion de Datos de Do ##
			$cCarOri = $vDoiDat['DEPDESXX']; //Carga Origen
			$cProced = $vDoiDat['PAIDESXX']; //Pais Procedencia
			$cTasCam = $vDoiDat['TCATASAX']; //Tasa de Cambio
			$cDocTra = $vDoiDat['DGEDTXXX']; //Documento de Transporte
			$cBultos = $vDoiDat['DGEBULXX']; //Bultos
			$cPesBru = $vDoiDat['DGEPBRXX']; //Peso Bruto
			$cCont20 = $vDoiDat['DOICON20']; //Contenedor de 20
			$cCont40 = $vDoiDat['DOICON40']; //Contenedor de 40
			##Fin Cargo Variables para Impresion de Datos de Do ##

			for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
				$cDocId   = $_POST['cDosNro_DOS'.($i+1)]; // DO
				$cDocSuf  = $_POST['cDosSuf_DOS'.($i+1)]; // Sufijo
        $cSucId   = $_POST['cSucId_DOS' .($i+1)]; // Sucursal

				##Traigo Datos de la SIAI0200 DATOS DEL DO ##
				$qDoiDat  = "SELECT ";
				$qDoiDat .= "$cAlfa.SIAI0200.TCATASAX "; // Tasa
				$qDoiDat .= "FROM $cAlfa.SIAI0200 ";
				$qDoiDat .= "WHERE ";
				$qDoiDat .= "$cAlfa.SIAI0200.DOIIDXXX = \"$cDocId\" AND ";
				$qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"$cDocSuf\" AND ";
				$qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"$cSucId\" ";
				$xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
				$nFilDoi  = mysql_num_rows($xDoiDat);
				if ($nFilDoi > 0) {
					$vDoiDat  = mysql_fetch_array($xDoiDat);
				}
				##Fin Traigo Datos de la SIAI0200 DATOS DEL DO ##

				##Traigo Datos de la SIAI0206 DATOS DEL DO ##
				$qDecDat  = "SELECT ";
				$qDecDat .= "SUBID2XX, ";
				$qDecDat .= "ADMIDXXX, ";
				$qDecDat .= "SUM($cAlfa.SIAI0206.LIMBULXX) AS LIMBULXX, ";
				$qDecDat .= "SUM($cAlfa.SIAI0206.LIMNETXX) AS LIMNETXX, ";
				$qDecDat .= "SUM($cAlfa.SIAI0206.LIMCIFXX) AS LIMCIFXX, ";
				$qDecDat .= "SUM($cAlfa.SIAI0206.LIMPBRXX) AS LIMPBRXX, ";
				$qDecDat .= "SUM($cAlfa.SIAI0206.LIMPNEXX) AS LIMPNEXX, ";
				$qDecDat .= "SUM($cAlfa.SIAI0206.LIMVLRXX) AS LIMVLRXX, ";//Fob
				$qDecDat .= "SUM($cAlfa.SIAI0206.LIMGRAXX) AS LIMGRA2X, ";
				$qDecDat .= "SUM($cAlfa.SIAI0206.LIMSUBT2) AS LIMSUBT2, ";
				$qDecDat .= "SUM($cAlfa.SIAI0206.LIMFLEXX) AS LIMFLEXX, ";
				$qDecDat .= "SUM($cAlfa.SIAI0206.LIMSEGXX) AS LIMSEGXX ";
				$qDecDat .= "FROM $cAlfa.SIAI0206 ";
				$qDecDat .= "WHERE $cAlfa.SIAI0206.DOIIDXXX = \"$cDocId\" AND ";
				$qDecDat .= "$cAlfa.SIAI0206.DOISFIDX = \"$cDocSuf\" AND ";
				$qDecDat .= "$cAlfa.SIAI0206.ADMIDXXX = \"$cSucId\" ";
				$qDecDat .= "GROUP BY $cAlfa.SIAI0206.DOIIDXXX,$cAlfa.SIAI0206.DOISFIDX,$cAlfa.SIAI0206.ADMIDXXX ";
				$xDecDat  = f_MySql("SELECT","",$qDecDat,$xConexion01,"");
						$nFilDec  = mysql_num_rows($xDecDat);
				if ($nFilDec > 0) {
					$vDecDat  = mysql_fetch_array($xDecDat);
				}
				##Fin Traigo Datos de la SIAI0206 DATOS DEL DO ##

				$nValCif += number_format($vDecDat['LIMNETXX'] * $vDoiDat['TCATASAX'],0,',','');// Valor Aduana * Tasa
			}
  	break;
  	case "EXPORTACION":
  		## Consulto Datos de Do en Exportaciones tabla siae0199 ##
			$qDexDat  = "SELECT * ";
			$qDexDat .= "FROM $cAlfa.siae0199 ";
			$qDexDat .= "WHERE ";
			$qDexDat .= "$cAlfa.siae0199.dexidxxx = \"$cDocId\" AND ";
			$qDexDat .= "$cAlfa.siae0199.admidxxx = \"$cSucId\" ";
			$xDexDat  = f_MySql("SELECT","",$qDexDat,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qDexDat);
			$nFilDex  = mysql_num_rows($xDexDat);
			if ($nFilDex > 0) {
				$vDexDat = mysql_fetch_array($xDexDat);
			}
			## Fin Consulto Datos de Do en Exportaciones tabla siae0199 ##
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
			$nFilIte  = mysql_num_rows($xIteDat);
			if ($nFilIte > 0) {
				$vIteDat = mysql_fetch_array($xIteDat);
			}
			##Cargo Variables para Impresion de Datos de Do ##
			$cCarOri = ""; //Carga Origen
			$cProced = ""; //Pais Procedencia
			$cTasCam = $vDceDat['doctrmxx']; //Tasa de Cambio
			$cDocTra = $vDexDat['dexdtrxx']; //Documento de Transporte
			$cBultos = $vIteDat['itebulxx']; //Bultos
			$cPesBru = $vIteDat['itepbrxx']; //Peso Bruto
			$cCont20 = ""; //Contenedor de 20
			$cCont40 = ""; //Contenedor de 40
			##Fin Cargo Variables para Impresion de Datos de Do ##

			for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
				$cDocId   = $_POST['cDosNro_DOS'.($i+1)]; // DO
				$cDocSuf  = $_POST['cDosSuf_DOS'.($i+1)]; // Sufijo
        $cSucId   = $_POST['cSucId_DOS' .($i+1)]; // Sucursal

				$qDatCom  = "SELECT ";
				$qDatCom .= "$cAlfa.sys00121.docfobxx, ";
				$qDatCom .= "$cAlfa.sys00121.doctrmxx ";
				$qDatCom .= "FROM $cAlfa.sys00121 ";
				$qDatCom .= "WHERE ";
				$qDatCom .= "$cAlfa.sys00121.sucidxxx = \"$cSucId\" AND ";
				$qDatCom .= "$cAlfa.sys00121.docidxxx = \"$cDocId\"  AND ";
				$qDatCom .= "$cAlfa.sys00121.docsufxx = \"$cDocSuf\" ";
				$xDatCom  = f_MySql("SELECT","",$qDatCom,$xConexion01,"");
				$nFilDce  = mysql_num_rows($xDatCom);
				if ($nFilDce > 0) {
					$vDatCom = mysql_fetch_array($xDatCom);
				}
				$nValCif += number_format(($vDatCom['docfobxx'] * $vDatCom['doctrmxx']),0,',',''); // Valor FOB * TRM
			}
  	break;
  	case "TRANSITO":
  		## Traigo Datos de la SIAI0200 ##
			$qDoiDat  = "SELECT * ";
			$qDoiDat .= "FROM $cAlfa.SIAI0200 ";
			$qDoiDat .= "WHERE ";
			$qDoiDat .= "DOIIDXXX = \"$cDocId\" AND ";
			$qDoiDat .= "DOISFIDX = \"$cDocSuf\" AND ";
			$qDoiDat .= "ADMIDXXX = \"$cSucId\" ";
			$xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
			$nFilDoi  = mysql_num_rows($xDoiDat);
			if ($nFilDoi > 0) {
				$vDoiDat = mysql_fetch_array($xDoiDat);
			}
			## Fin Consulta a la tabla de Do's ##
			//f_Mensaje(__FILE__,__LINE__,$qDoiDat);

			## Consulto en la tabla de Items DTA ##
			$qIteDat  = "SELECT  ";
			$qIteDat .= "SUM($cAlfa.dta00201.itepbrxx) AS itepbrxx, ";
			$qIteDat .= "SUM($cAlfa.dta00201.itebulxx) AS itebulxx ";
			$qIteDat .= "FROM $cAlfa.dta00201 ";
			$qIteDat .= "WHERE ";
			$qIteDat .= "$cAlfa.dta00201.doiidxxx = \"$cDocId\" AND ";
			$qIteDat .= "$cAlfa.dta00201.admidxxx = \"$cSucId\" ";
			$xIteDat  = f_MySql("SELECT","",$qIteDat,$xConexion01,"");
			$nFilIte  = mysql_num_rows($xIteDat);
			if ($nFilIte > 0) {
				$vIteDat = mysql_fetch_array($xIteDat);
			}
			##Cargo Variables para Impresion de Datos de Do ##
			$cCarOri = $vDoiDat['DEPDESXX']; //Carga Origen
			$cProced = $vDoiDat['PAIDESXX']; //Pais Procedencia
			$cTasCam = $vDoiDat['TCATASAX']; //Tasa de Cambio
			$cDocTra = $vDoiDat['DGEDTXXX']; //Documento de Transporte
			$cBultos = $vIteDat['itebulxx']; //Bultos
			$cPesBru = $vIteDat['itepbrxx']; //Peso Bruto
			$cCont20 = ""; //Contenedor de 20
			$cCont40 = ""; //Contenedor de 40
			##Fin Cargo Variables para Impresion de Datos de Do ##

			for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
				$cDocId   = $_POST['cDosNro_DOS'.($i+1)]; // DO
				$cDocSuf  = $_POST['cDosSuf_DOS'.($i+1)]; // Sufijo
        $cSucId   = $_POST['cSucId_DOS' .($i+1)]; // Sucursal

				// Datos por DO
				$vDoiId  = explode("~",$mDoiId[$i]);

				## Consulto en la Tabla de Control DTA ##
				$qDtaDat  = "SELECT * ";
				$qDtaDat .= "FROM $cAlfa.dta00200 ";
				$qDtaDat .= "WHERE ";
				$qDtaDat .= "$cAlfa.dta00200.doiidxxx = \"$cDocId\" AND ";
				$qDtaDat .= "$cAlfa.dta00200.admidxxx = \"$cSucId\" ";
				$xDtaDat  = f_MySql("SELECT","",$qDtaDat,$xConexion01,"");
				$nFilDta  = mysql_num_rows($xDtaDat);
				if ($nFilDta > 0) {
				$vDtaDat = mysql_fetch_array($xDtaDat);
				}

				## Traigo Tasa de la SIAI0200 ##
				$qDoiDat  = "SELECT TCATASAX ";
				$qDoiDat .= "FROM $cAlfa.SIAI0200 ";
				$qDoiDat .= "WHERE ";
				$qDoiDat .= "DOIIDXXX = \"$cDocId\" AND ";
				$qDoiDat .= "DOISFIDX = \"$cDocSuf\" AND ";
				$qDoiDat .= "ADMIDXXX = \"$cSucId\" ";
				$xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
				$nFilDoi  = mysql_num_rows($xDoiDat);
				if ($nFilDoi > 0) {
					$vDoiDat = mysql_fetch_array($xDoiDat);
				}
				## Fin Consulta a la tabla de Do's ##

				$nValCif += number_format(($vDtaDat['dtafobxx']*$vDoiDat['TCATASAX']),0,',','');
			}
  	break;
  	case "OTROS":
  	break;
  }//switch (){
  ##Fin Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##

  ##Busco los comprobantes que estan marcados como Reembolso de Caja Menor para luego traer el numero del Vale de Caja Menor con el que inicialmente se hizo el pago##
  $qComRee  = "SELECT ";
  $qComRee .= "$cAlfa.fpar0117.comidxxx, ";
  $qComRee .= "$cAlfa.fpar0117.comcodxx ";
  $qComRee .= "FROM $cAlfa.fpar0117 ";
  $qComRee .= "WHERE ";
  $qComRee .= "$cAlfa.fpar0117.comtipxx = \"RCM\" AND ";
  $qComRee .= "$cAlfa.fpar0117.regestxx = \"ACTIVO\" ";
  $xComRee  = f_MySql("SELECT","",$qComRee,$xConexion01,"");
 	$mComRee  = array();
  while($xRCR = mysql_fetch_array($xComRee)){
  	$mComRee[count($mComRee)] = $xRCR;
  }
  ##fin Busco los comprobantes que estan marcados como Reembolso de Caja Menor para luego traer el numero del Vale de Caja Menor con el que inicialmente se hizo el pago##


	##Codigo para imprimir los ingresos para terceros ##
	$mIngTer = array();
  for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
    if (substr_count($_POST['cComObs_PCCA' .($i+1)],"") > 0 ) { // Encontre la palabra DIAN de pago de impuestos.
       $nInd_mIngTer = count($mIngTer);
       $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId_PCCA'  .($i+1)];
       $vDesc = explode("^",$_POST['cComObs_PCCA' .($i+1)]);
       $mIngTer[$nInd_mIngTer]['cComObs']  = $vDesc[1].$vDesc[0];
       $mIngTer[$nInd_mIngTer]['cTerId']   = $_POST['cTerId_PCCA'.($i+1)];
       $mIngTer[$nInd_mIngTer]['cComCsc3'] = $_POST['cComCsc3_PCCA'.($i+1)];
       $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA' .($i+1)];
       $mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nBaseIva_PCCA'.($i+1)];
       $mIngTer[$nInd_mIngTer]['nVlrIva']  = $_POST['nVlrIva_PCCA'.($i+1)];
      }else{
          $nSwitch_Find = 0;
          for ($j=0;$j<count($mIngTer);$j++) {
            if ($_POST['cComId_PCCA'.($i+1)] == $mIngTer[$j]['cComId']) {//Agrupar por Concepto
            	if ($_POST['cTerId_PCCA'.($i+1)] == $mIngTer[$j]['cTerId']) {//Agrupar por Tercero
	              $nSwitch_Find = 1;
	              $mIngTer[$j]['nComVlr']   += $_POST['nComVlr_PCCA'.($i+1)];
	              $mIngTer[$j]['nBaseIva']  += $_POST['nBaseIva_PCCA'.($i+1)];
	              $mIngTer[$j]['nVlrIva']   += $_POST['nVlrIva_PCCA'.($i+1)];
	              ##Si es un comprobante de Reembolsos de Caja Menor debo buscar el numero del consecutivo del vale de caja Menor para mostrarlo en el pago a tercero##
		            $nAnio = date("Y");
		            for($ii=0;$ii<count($mComRee);$ii++){
		            	if($mComRee[$ii]['comidxxx'] == $_POST['cComId3_PCCA'.($i+1)] && $mComRee[$ii]['comcodxx'] == $_POST['cComCod3_PCCA'.($i+1)]){
		            		$nAnio = substr($_POST['cComCsc3_PCCA'.($i+1)],0,4);
		            		$qNumRec  = "SELECT ";
		            		$qNumRec .= "$cAlfa.fcod$nAnio.comidc2x, ";
		            		$qNumRec .= "$cAlfa.fcod$nAnio.comcodc2, ";
		            		$qNumRec .= "$cAlfa.fcod$nAnio.comcscc2 ";
		            		$qNumRec .= "FROM $cAlfa.fcod$nAnio ";
		            		$qNumRec .= "WHERE ";
		            		$qNumRec .= "$cAlfa.fcod$nAnio.comidxxx = \"{$_POST['cComId3_PCCA'.($i+1)]}\" AND ";
		            		$qNumRec .= "$cAlfa.fcod$nAnio.comcodxx = \"{$_POST['cComCod3_PCCA'.($i+1)]}\" AND ";
		            		$qNumRec .= "$cAlfa.fcod$nAnio.comcscxx = \"{$_POST['cComCsc3_PCCA'.($i+1)]}\" AND ";
		            		$qNumRec .= "$cAlfa.fcod$nAnio.comseqxx = \"{$_POST['cComSeq3_PCCA'.($i+1)]}\" AND ";
		            		$qNumRec .= "$cAlfa.fcod$nAnio.regestxx = \"ACTIVO\"  ";
		            		$xNumRec  = f_MySql("SELECT","",$qNumRec,$xConexion01,"");
		            		$vNumRec = mysql_fetch_array($xNumRec);
		            		$_POST['cComCsc3_PCCA'.($i+1)] = $vNumRec['comcscc2'];
		            		$ii = count($mComRee);
		            	}//if($mComRee[$ii]['comidxxx'] == $mIT[$i][3] && $mComRee[$ii]['comcodxx'] == $mIT[$i][4]){
		            }//for($ii=0;$ii<count($mComRee);$ii++){
		            $mIngTer[$j]['cComCsc3'] .= ((strlen($mIngTer[$nInd_mIngTer]['cComCsc3'])+strlen("/".$_POST['cComCsc3_PCCA'.($i+1)])) <= 40) ? "/".$_POST['cComCsc3_PCCA'.($i+1)] : "";
	            	##Si es un comprobante de Reembolsos de Caja Menor debo buscar el numero del consecutivo del vale de caja Menor para mostrarlo en el pago a tercero##
            	}//if ($_POST['cTerId_PCCA'.($i+1)] == $mIngTer[$j]['cTerId']) {//Agrupar por Tercero
            }//if ($_POST['cComId_PCCA'.($i+1)] == $mIngTer[$j]['cComId']) {//Agrupar por Concepto
          }//for ($j=0;$j<count($mIngTer);$j++) {

          if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
            $nInd_mIngTer = count($mIngTer);
            $mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId_PCCA'  .($i+1)];
            $vDesc = explode("^",$_POST['cComObs_PCCA' .($i+1)]);
            $mIngTer[$nInd_mIngTer]['cComObs']  = $vDesc[1].$vDesc[0];
            $mIngTer[$nInd_mIngTer]['cTerId']   = $_POST['cTerId_PCCA'.($i+1)];
            $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA' .($i+1)];
            $mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nBaseIva_PCCA'.($i+1)];
            $mIngTer[$nInd_mIngTer]['nVlrIva']  = $_POST['nVlrIva_PCCA'.($i+1)];
            ##Si es un comprobante de Reembolsos de Caja Menor debo buscar el numero del consecutivo del vale de caja Menor para mostrarlo en el pago a tercero##
	        	$nAnio = date("Y");
	        	for($mm=0;$mm<count($mComRee);$mm++){
	        		if($mComRee[$mm]['comidxxx'] == $_POST['cComId3_PCCA'.($i+1)] && $mComRee[$mm]['comcodxx'] == $_POST['cComCod3_PCCA'.($i+1)]){
	          		$nAnio = substr($_POST['cComCsc3_PCCA'.($i+1)],0,4);
	            	$qNumRec  = "SELECT ";
	            	$qNumRec .= "$cAlfa.fcod$nAnio.comidc2x, ";
		            $qNumRec .= "$cAlfa.fcod$nAnio.comcodc2, ";
		           	$qNumRec .= "$cAlfa.fcod$nAnio.comcscc2 ";
		            $qNumRec .= "FROM $cAlfa.fcod$nAnio ";
		            $qNumRec .= "WHERE ";
		            $qNumRec .= "$cAlfa.fcod$nAnio.comidxxx = \"{$_POST['cComId3_PCCA'.($i+1)]}\" AND ";
		            $qNumRec .= "$cAlfa.fcod$nAnio.comcodxx = \"{$_POST['cComCod3_PCCA'.($i+1)]}\" AND ";
		            $qNumRec .= "$cAlfa.fcod$nAnio.comcscxx = \"{$_POST['cComCsc3_PCCA'.($i+1)]}\" AND ";
		            $qNumRec .= "$cAlfa.fcod$nAnio.comseqxx = \"{$_POST['cComSeq3_PCCA'.($i+1)]}\" AND ";
		            $qNumRec .= "$cAlfa.fcod$nAnio.regestxx = \"ACTIVO\"  ";
		            //f_Mensaje(__FILE__,__LINE__,$qNumRec);
		            $xNumRec  = f_MySql("SELECT","",$qNumRec,$xConexion01,"");
		            $vNumRec = mysql_fetch_array($xNumRec);
		            $_POST['cComCsc3_PCCA'.($i+1)] = $vNumRec['comcscc2'];
		            $mm = count($mComRee);
	          	}//if($mComRee[$ii]['comidxxx'] == $mIT[$i][3] && $mcomRee[$ii]['comcodxx'] == $mIT[$i][4]){
	        	}//for($ii=0;$ii<count($mComRee);$ii++){
	        	$mIngTer[$nInd_mIngTer]['ccomcsc3'] = $_POST['cComCsc3_PCCA'.($i+1)];
	        	##Si es un comprobante de Reembolsos de Caja Menor debo buscar el numero del consecutivo del vale de caja Menor para mostrarlo en el pago a tercero##
          }
      }//}else{
  }//for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
  ##Fin Codigo para imprimir los ingresos para terceros ##
  ##Recorro Grilla de IP para saber si se habilita la impresion del bloque de Ingresos Propios##
  #Agrupo Ingresos Propios
  $mIP = array();
  for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
    $cObs    = "";
    $cCif    = "";
    $nCif    = 0;
    $cAplCif = "NO";

    $cDim    = "";
    $nDim    = 0;
    $cAplDim = "NO";

    $cHor    = "";
    $nHor    = 0;
    $cAplHor = "NO";

    $cPie    = "";
    $nPie    = 0;
    $cAplPie = "NO";

    $cDav    = "";
    $nDav    = 0;
    $cAplDav = "NO";

    $cVuce    = "";
    $nVuce    = 0;
    $cAplVuce = "NO";

    $cCertificados    = "";
    $nCertificados    = 0;
    $cAplCertificados = "NO";

    $cDex    = "";
    $nDex    = 0;
    $cAplDex = "NO";

    $cSerial    = "";
    $nSerial    = 0;
    $cAplSerial = "NO";

    $cArancelaria    = "";
    $nArancelaria    = 0;
    $cAplArancelaria = "NO";

    $cDta      = "";
    $nDta      = 0;
    $cAplDta   = "NO";

    $cItems    = "";
    $nItems    = 0;
    $cAplItems = "NO";

    $cCan      = "";
    $nCan      = 0;
    $cAplCan   = "NO";

    $cFob      = "";
    $nFob      = 0;
    $cAplFob   = "NO";

    $cCon20    = "";
    $nCon20    = 0;
    $cAplCon20 = "NO";

    $cCon40    = "";
    $nCon40    = 0;
    $cAplCon40 = "NO";

    $cCarSue   = "";
    $nCarSue   = 0;
    $cAplCarSue= "NO";

    $cFobAgen = "NO";    // Valor Fob de Agenciamiento para Expo
    $xRDD     = array(); //Inicializando el cursor de Valor Fob
    switch ($_POST['cSerId_IPA'.($i+1)]) {
      // case "109":
      // 	$nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
  		// 	if($nComObs_IP > 0){
  		// 		$mAuxArancelaria = explode("CLASIFICACIONES ARANCELARIAS:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  		// 		$cArancelaria = "";
  		// 		if(count($mAuxArancelaria) > 1) {
  		// 			$cArancelaria    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxArancelaria[1]);
  		// 			$nArancelaria    = $cArancelaria;
  		// 			$cAplArancelaria = "SI";
  		// 		}
  		// 		$cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
  		// 	}else{
  		// 		$cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
  		// 	}
  		// break;
  		// case "111":
  		// 	$nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
  		// 	if($nComObs_IP > 0){
  		// 		$mAuxPie = explode("PIEZAS:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  		// 		$cPie = "";
  		// 		if(count($mAuxPie) > 1) {
  		// 			$cPie    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxPie[1]);
  		// 			$nPie    = $cPie;
  		// 			$cAplPie = "SI";
  		// 		}
  		// 		$cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
  		// 	}else{
  		// 		$cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
  		// 	}
  		// break;
  		// case "101":
  		// case "103":
  		// case "119":
  		// case "201":
  		// case "309":
  		// 	$nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
  		// 	if($nComObs_IP > 0){
  		// 		$mAuxHor = explode("HORAS:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  		// 		$mAuxSerial = explode("CANT SERIALES:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  		// 		$mAuxItems = explode("ITEMS:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));

  		// 		$cDim    = "";
  		// 		$cSerial = "";
  		// 		$cItems  = "";
  		// 		if(count($mAuxHor) > 1) {
  		// 			$cHor    =str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxHor[1]);
  		// 			$nHor    = $cHor;
  		// 			$cAplHor = "SI";
  		// 		}

  		// 		if(count($mAuxSerial) > 1) {
  		// 			$cSerial    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxSerial[1]);
  		// 			$nSerial    = $cSerial;
  		// 			$cAplSerial = "SI";
  		// 		}

  		// 		if(count($mAuxItems) > 1) {
  		// 			$cItems    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxItems[1]);
  		// 			$nItems    = $cItems;
  		// 			$cAplItems = "SI";
  		// 		}

  		// 		$cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
  		// 	}else{
  		// 		$cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
  		// 	}
  		// break;
  		case "102":
  		//case "110":
  			$nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
  			if($nComObs_IP > 0){
  				$mAuxDim = explode("DIM:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  				$cDim = "";
  				if(count($mAuxDim) > 1) {
  				$cDim    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxDim[1]);
  				$nDim    = $cDim;
  				$cAplDim = "SI";
  				}
  				$cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
  			}else{
  				$cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
  			}
  		break;
  		case "103":
  		case "148":
  		//case "156":
  			$nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
  			if($nComObs_IP > 0){
  				$mAuxDav = explode("DAV:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  							$mAuxDavMag = explode("DAV MAGNETICAS:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  				$cDav = "";
  				if(count($mAuxDav) > 1 || count($mAuxDavMag) > 1) {
  				$cDav    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "",  (count($mAuxDav) > 1) ? $mAuxDav[1]  : $mAuxDavMag[1] );
  				$nDav    = $cDav;
  				$cAplDav = "SI";
  				}
  				$cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
  			}else{
  				$cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
  			}
  		break;
  		// case "104":
  		// case "504":
  		// 	$nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
  		// 	if($nComObs_IP > 0){
  		// 		$mAuxVuce = explode("VUCE:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  		// 		$cVuce = "";
  		// 		if(count($mAuxVuce) > 1) {
  		// 			$cVuce    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxVuce[1]);
  		// 			$nVuce    = $cVuce;
  		// 			$cAplVuce = "SI";
  		// 		}
  		// 		$cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
  		// 	}else{
  		// 		$cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
  		// 	}//if($mComObs_IP[$i][2] != ""){
  		// break;
  		// case "200":
  		// 	$nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
  		// 		if($nComObs_IP > 0){
  		// 			$mAuxFob    = explode("FOB:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  		// 			$mAuxFob[0] = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxFob[1]);
  		// 			$cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
  		// 		}else{
  		// 			$cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
  		// 		}
  		// break;
        	// case "203":
  		// 	$nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
  		// 	if($nComObs_IP > 0){
  		// 		$mAuxCertificados = explode("ORIGEN:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  		// 		$cCertificados = "";
  		// 		if(count($mAuxCertificados) > 1) {
  		// 			$cCertificados    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCertificados[1]);
  		// 			$nCertificados    = $cCertificados;
  		// 			$cAplCertificados = "SI";
  		// 		}
  		// 		$cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
  		// 	}else{
  		// 		$cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
  		// 	}//if($mComObs_IP[$i][2] != ""){
  		// break;
  		// case '201':
  		// case '204':
  		// case "202":
  		// 	$nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
  		// 		if($nComObs_IP > 0){
  		// 			$mAuxDex = explode("DEX:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  		// 			$cDex = "";
  		// 			if(count($mAuxDex) > 1) {
  		// 			$cDex    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxDex[1]);
  		// 			$nDex    = $cDex;
  		// 			$cAplDex = "SI";
  		// 			}
  		// 			$cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
  		// 		}else{
  		// 			$cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
  		// 		}
  		// break;
  		// case "301":
        	// case "308":
  		// 	$nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
          	// 	if($nComObs_IP > 0){
  		// 		$mAuxDta = explode("DTA:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  		// 		$cDta = "";
  		// 		if(count($mAuxDta) > 1) {
  		// 		$cDta    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxDta[1]);
  		// 		$nDta    = $cDta;
  		// 		$cAplDta = "SI";
  		// 		}
  		// 		$cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
  		// 	}else{
  		// 		$cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
  		// 	}
  		// break;
        	// case "305":
  		// 	$nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
  		// 		if($nComObs_IP > 0){
  		// 			$mAuxCan = explode("Cantidad:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  		// 			$cCan = "";
  		// 			if(count($mAuxCan) > 1) {
  		// 			$cCan    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCan[1]);
  		// 			$nCan    = $cCan;
  		// 			$cAplCan = "SI";
  		// 			}
  		// 			$cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
  		// 		}else{
  		// 			$cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
  		// 		}
  		// break;
  		// case "300":
        	// case "307":
  		// 	$nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
  		// 	if($nComObs_IP > 0){
  		// 		//Valor FOB - Buscando Posicion TRM en la observacion
  		// 		$nPosTrm   = stripos($_POST['cComObs_IPA'.($i+1)], "TRM");
  		// 		$mAuxFob   = explode("FOB:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,($nPosTrm-$nComObs_IP)));
  		// 		//Contenedores de 20 - Buscando Posicion Contenedores de 40
  		// 		$nPosCon40 = stripos($_POST['cComObs_IPA'.($i+1)], "CONTENEDORES DE 40:");
  		// 		$nPosCon40 = ($nPosCon40 === false) ? strlen($_POST['cComObs_IPA'.($i+1)]) : $nPosCon40 ;
  		// 		$mAuxCon20 = explode("CONTENEDORES DE 20:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,($nPosCon40-$nComObs_IP)));
  		// 		//Contenedores de 40
  		// 		$mAuxCon40 = explode("CONTENEDORES DE 40:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  		// 		//Carga Suelta
  		// 		$mAuxCarSue = explode("UNIDADES DE CARGA SUELTA:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));

  		// 		$cFob    = "";
  		// 		$cCon20  = "";
  		// 		$cCon40  = "";
  		// 		$cCarSue = "";
  		// 		if(count($mAuxFob) > 1) {
  		// 		$cFob    = str_replace(array(".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxFob[1]);
  		// 		$nFob    = $cFob;
  		// 		$cAplFob = "SI";
  		// 		}
  		// 		if(count($mAuxCon20) > 1) {
  		// 		$cCon20 = str_replace(array("(",")",".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCon20[1]);
  		// 		$nCon20 = $cCon20;
  		// 		$cAplCon20 = "SI";
  		// 		}
  		// 		if(count($mAuxCon40) > 1) {
  		// 		$cCon40 = str_replace(array("(",")",".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCon40[1]);
  		// 		$nCon40 = $cCon40;
  		// 		$cAplCon40 = "SI";
  		// 		}
  		// 		if(count($mAuxCarSue) > 1) {
  		// 		$cCarSue = str_replace(array("(",")",".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCarSue[1]);
  		// 		$nCarSue = $cCarSue;
  		// 		$cAplCarSue = "SI";
  		// 		// f_Mensaje(__FILE__,__LINE__,$nCarSue);
  		// 		}
  		// 		$cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
  		// 	}else{
  		// 		$cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
  		// 	}
  		// break;
  		default:
  			$nComObs_IP = stripos($_POST['cComObs_IPA'.($i+1)], "[");
  			if($nComObs_IP > 0){
  				$mAuxCif = explode("CIF:",substr($_POST['cComObs_IPA'.($i+1)],$nComObs_IP,strlen($_POST['cComObs_IPA'.($i+1)])));
  				$cCif = "";

  				if(count($mAuxCif) > 1) {
  				$cCif    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCif[1]);
  				$nCif    = $cCif;
  				$cAplCif = "SI";
  				}
  				$cObs = substr(substr($_POST['cComObs_IPA'.($i+1)],0,$nComObs_IP),0,70);
  			}else{
  				$cObs = substr($_POST['cComObs_IPA'.($i+1)],0,70);
  			}
      break;
    }

    //   switch ($_POST['cSerId_IPA'.($i+1)]) {
    //     case "200":
  	// 	case "203":
  	// 		$qDocDat  = "SELECT docfobxx,doctrmxx ";
  	// 		$qDocDat .= "FROM $cAlfa.sys00121 ";
  	// 		$qDocDat .= "WHERE ";
  	// 		$qDocDat .= "docidxxx = \"{$cDocId}\" AND ";
  	// 		$qDocDat .= "sucidxxx = \"{$cSucId}\" AND ";
  	// 		$qDocDat .= "docsufxx = \"{$cDocSuf}\" LIMIT 0,1 ";
  	// 		$xDocDat  = f_MySql("SELECT","",$qDocDat,$xConexion01,"");
  	// 		$xRDD = mysql_fetch_array($xDocDat);
  	// 		$cFobAgen = "SI";
  	// 	break;
    //   }

    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvlr01'] += $_POST['nComVIva_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvlrxx'] += $_POST['nComVlr_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['ctoidxxx']  = $_POST['cComId_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comobsxx']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comobsxx'] == "") ? trim($cObs) : $mIP[$_POST['cComId_IPA'.($i+1)]]['comobsxx'];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcifxx'] += $nCif;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcifap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comcifap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comcifap']:$cAplCif;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdimxx'] += $nDim;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdimap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comdimap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comdimap']:$cAplDim;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdavxx'] += $nDav;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdavap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comdavap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comdavap']:$cAplDav;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvucxx'] += $nVuce;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comvucap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comvucap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comvucap']:$cAplVuce;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcerxx'] += $nCertificados;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcerap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comcerap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comcerap']:$cAplCertificados;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comfobxx']  = $cFobAgen;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['docfobxx'] += $xRDD['docfobxx'];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['doctrmxx']  = $xRDD['doctrmxx'];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comhorxx'] += $nHor;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comhorap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comhorap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comhorap']:$cAplHor;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['compiexx'] += $nPie;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['compieap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['compieap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['compieap']:$cAplPie;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdexxx'] += $nDex;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdexap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comdexap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comdexap']:$cAplDex;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comserxx'] += $nSerial;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comserap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comserap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comserap']:$cAplSerial;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comaraxx'] += $nArancelaria;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comaraap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comaraap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comaraap']:$cAplArancelaria;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdtaxx'] += $nDta;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comdtaap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comdtaap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comdtaap']:$cAplDta;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comitexx'] += $nItems;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comiteap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comiteap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comiteap']:$cAplItems;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcanxx'] += $nCan;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcanap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comcanap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comcanap']:$cAplCan;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comfob2x'] += $nFob;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comfobap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comfobap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comfobap']:$cAplFob;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comc20xx'] += $nCon20;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comc20ap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comc20ap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comc20ap']:$cAplCon20;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comc40xx'] += $nCon40;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comc40ap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comc40ap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comc40ap']:$cAplCon40;
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcsuxx'] += $nCarSue;
    // f_Mensaje(__FILE__,__LINE__,"Antes: ".$mIP[$_POST['cComId_IPA'.($i+1)]]['comcsuap']);
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comcsuap']  = ($mIP[$_POST['cComId_IPA'.($i+1)]]['comcsuap'] == "SI")?$mIP[$_POST['cComId_IPA'.($i+1)]]['comcsuap']:$cAplCarSue;
    // f_Mensaje(__FILE__,__LINE__,"Despues: ".$mIP[$_POST['cComId_IPA'.($i+1)]]['comcsuap']);
  }

	$mIngPro = array();
	foreach ($mIP as $cKey => $mValores) {
		$mIngPro[count($mIngPro)] = $mValores;
	}

  ##Fin de Recorro Grilla de IP para saber si se habilita la impresion del bloque de Ingresos Propios##
	## Comienzo a pintar Vista Previa de Factura##
  //////////////////////////////////////////////////////////////////////////

  class PDF extends FPDF {
		function Header() {
		  global $cAlfa;   global $cRoot;   global $cPlesk_Skin_Directory;
			global $gCcoId;  global $gSccId;  global $gMesDes; global $gAnoDes; global $gMesHas; global $gAnoHas;
			global $cUsrNom; global $cCcoDes; global $cScoDes; global $nPag;    global $vAgeDat; global $vCocDat;
			global $vResDat; global $cDocTra; global $cTasCam; global $cDocTra; global $cBultos; global $cPesBru;
			global $cDocId;  global $vCliDat; global $vConDat; global $vDceDat; global $nValCif; global $cCarOri;
			global $cProced; global $cCont20; global $cCont40; global $vDesCiu;

		  $posy	= 20;  /// PRIMERA POSICION DE Y ///
      $posx	= 10;
      ##Impresion Datos Generales Factura ##
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamientos.jpg',10,7,85,30);
      $this->SetFont('verdanab','',6);
      $this->setXY($posx+30,$posy+6);
      $this->Cell(30,7,"NIT ".$vAgeDat['CLIIDXXX']."-".f_Digito_Verificacion($vAgeDat['CLIIDXXX']),0,0,'C');
      $this->SetFont('verdanab','',10);
      $this->setXY(130,$posy);
      $this->Cell(50,10,"FACTURA DE VENTA No. XXXXXX",0,0,'L');
      $posy += 5;
      $this->SetFont('verdanab','',8);
      $this->setXY(130,$posy);
      $this->Cell(30,10,"FECHA DE EXPEDICION: ",0,0,'L');
      $this->setXY(175,$posy);
      $this->SetFont('verdana','',8);
      $cFechaE = explode("-",f_Fecha_Letras($_POST['dRegFCre']));
      $this->Cell(30,10,$cFechaE[0]."-".strtoupper(substr($cFechaE[1],0,3))."-".$cFechaE[2],0,0,'L');
      $posy += 5;
      $this->setXY(130,$posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(30,10,"FECHA DE VENCIMIENTO: ",0,0,'L');
      $this->setXY(175,$posy);
      $this->SetFont('verdana','',8);
      $dFecVen = date("Y-m-d",mktime(0,0,0,substr($_POST['dRegFCre'],5,2),substr($_POST['dRegFCre'],8,2)+$_POST['cTerPla'],substr($_POST['dRegFCre'],0,4)));
      $cFechaV = explode("-",f_Fecha_Letras($dFecVen));
      $this->Cell(30,10,$cFechaV[0]."-".strtoupper(substr($cFechaV[1],0,3))."-".$cFechaV[2],0,0,'L');
      $posy = 35;

      $this->SetFont('verdanab','',8);
      $this->setXY($posx,$posy);
      $this->Cell(30,10,utf8_decode("SEÑORES"),0,0,'L');
      $posy += 6;
      $this->SetFont('verdana','',8);
      $this->setXY($posx,$posy);
			$this->Cell(110,6,$vCliDat['CLINOMXX'],0,0,'L');
	    $posy += 4;
	    $this->setXY($posx,$posy);
	    $this->Cell(110,6,"ATN :  ".($vConDat['CLINOMXX'] == "" ? $vConDat['NOMBRE'] : $vConDat['CLINOMXX']) ,0,0,'L');
	    $posy += 4;
	    $this->setXY($posx,$posy);
	    $this->Cell(110,6,"NIT :  ".$vCliDat['CLIIDXXX']."-".f_Digito_Verificacion($vCliDat['CLIIDXXX']),0,0,'L');
	    $posy += 4;
	    $this->setXY($posx,$posy);
      $this->Cell(110,6,"DIRECCION: ",0,0,'L');
      $this->SetFont('verdana','',8);
      $this->setXY(29,$posy);
			$this->Cell(84,6,$vCliDat["CLIDIRXX"],0,0,'L');
      $posy += 4;
      $this->setXY(29,$posy);
      $this->Cell(84,6,$vDesCiu["CIUDESXX"],0,0,'L');
      $this->Rect($posx,37,115,25);

      $posy1 = 35;
      $this->setXY(130,$posy1);
      $this->SetFont('verdana','',5);
      $this->Cell(50,10,"NO DESCUENTE RETENCION EN LA FUENTE POR RENTA",0,0,'L');
      $posy1 += 2;
      $this->setXY(130,$posy1);
      $this->Cell(50,10,"SOMOS AUTORRETENEDORES SEGUN",0,0,'L');
      $posy1 += 2;
      $this->setXY(130,$posy1);
      $this->Cell(50,10,"RESOLUCION No. 5538 DEL 14 DE JUNIO DE 2002",0,0,'L');
      $posy1 += 2;
	    $this->setXY(130,$posy1);
      $this->Cell(50,10,"ACTIVIDAD ECONOMICA: 5229",0,0,'L');
      $posy1 += 2;
      $this->setXY(130,$posy1);
      $this->Cell(50,10,"INDUSTRIA Y COMERCIO: ACTIVIDAD 304 TARIFA 9,66  0/00",0,0,'L');
      $posy1 += 2;
      $this->setXY(130,$posy1);
      $this->Cell(50,10,"IVA REGIMEN COMUN",0,0,'L');
      $posy1 += 3;
      $this->setXY(130,$posy1);
      $this->Cell(50,10,"Autorizacion de facturacion por Computador Resolucion DIAN",0,0,'L');
      $posy1 += 2;
      $this->setXY(130,$posy1);
      $cFecha = explode("-",f_Fecha_Letras($vResDat['resfdexx']));
      $this->Cell(50,10,"No {$vResDat['residxxx']} del $cFecha[2] de $cFecha[1] de $cFecha[0] del No. {$vResDat['resdesxx']}",0,0,'L');
      $posy1 += 2;
      $this->setXY(130,$posy1);
      $this->Cell(50,10,"Al {$vResDat['reshasxx']} Impreso por Agencia de Aduanas Aduanamientos Ltda Nivel 1.",0,0,'L');

      $posy += 3;
      $this->SetFont('verdana','',6);
      $this->setXY($posx,$posy);
      //$this->Cell(200,10,"El aceptante se obliga a pagar irrevocablemente a {$vAgeDat['CLINOMXX']} las sumas por los conceptos que a continuacion se indican:",0,0,'C');
      $this->Cell(200,10,"El aceptante se obliga a pagar irrevocablemente a Agencia de Aduanas Aduanamientos Ltda Nivel 1 las sumas por los conceptos que a continuacion se indican:",0,0,'C');
      $posy += 5;
      $this->SetFont('verdanab','',8);
      $this->setXY($posx,$posy);
      $this->Cell(7,10,"REF: ",0,0,'L');
      $this->setXY(18,$posy);
      $this->SetFont('verdana','',8);
      $this->Cell(30,10,$cDocId,0,0,'L');
      $this->setXY(60,$posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(7,10,"CLIENTE: ",0,0,'L');
      $this->setXY(75,$posy);
      $this->SetFont('verdana','',8);
      $this->Cell(30,10,$vDceDat['IMPORTADOR'],0,0,'L');

      ##Fin Valido si el nombre del Importador excede el espacion de impresion para calcular doble renglon e imprimir en letra mas peque�a ##
      //$this->Cell(30,10,$vDceDat['IMPORTADOR'],0,0,'L');
      $this->setXY(150,$posy);
      $this->SetFont('verdanab','',8);
      /*$this->Cell(7,10,"PEDIDO: ",0,0,'L');
      $this->setXY(165,$posy);
      $this->SetFont('verdana','',8);
      $this->Cell(30,10,$vDceDat['docpedxx'],0,0,'L');*/
      $poyy = $posy+5;
      $this->setXY($posx,$poyy);
      $this->SetFont('verdanab','',8);
      $this->Cell(7,7,"PUERTO: ",0,0,'L');
      $this->SetFont('verdana','',8);
      $this->setXY(25,$poyy);
      $this->Cell(30,7,$vDceDat['sucdesxx'],0,0,'L');
      $this->setXY(60,$poyy);
      $this->SetFont('verdanab','',8);
      $this->Cell(7,7,"PEDIDO: ",0,0,'L');
      $this->setXY(75,$poyy);
      $this->SetFont('verdana','',8);
      $this->Cell(30,7,$vDceDat['docpedxx'],0,0,'L');
      $this->setXY(150,$poyy);
      $this->SetFont('verdanab','',8);
      $this->Cell(7,7,"PROCEDENCIA: ",0,0,'L');
      $this->setXY(175,$poyy);
      $this->SetFont('verdana','',8);
      $this->Cell(30,7,$cProced,0,0,'L');
      $poyy += 4;
      $this->setXY($posx,$poyy);
      $this->SetFont('verdanab','',8);
      $this->Cell(7,7,"OPERACION: ",0,0,'L');
      $this->setXY(31,$poyy);
      $this->SetFont('verdana','',8);
     	$this->Cell(30,7,$vDceDat['doctipxx'],0,0,'L');

      $this->setXY(60,$poyy);
      $this->SetFont('verdanab','',8);
      $this->Cell(7,7,"DOC. TRANSPORTE: ",0,0,'L');
      $this->setXY(93,$poyy);
      $this->SetFont('verdana','',8);
      $this->Cell(30,7,$cDocTra,0,0,'L');
      $this->SetFont('verdanab','',8);
      $this->setXY(150,$poyy);
      $this->Cell(7,7,"VALOR ADUANA: ",0,0,'L');
      $this->setXY(178,$poyy);
      $this->SetFont('verdana','',8);
      if($nValCif <> 0){
      	$this->Cell(30,7,number_format($nValCif,0,',','.'),0,0,'L');
      }else{
      	$this->Cell(30,7,"",0,0,'L');
      }

      $poyy += 4;

			$this->setXY($posx,$poyy);
      $this->SetFont('verdanab','',8);
      $this->setXY($posx,$poyy);
      $this->SetFont('verdanab','',8);
      if($cBultos <> 0){
      	$this->Cell(30,7,"CON ".number_format($cBultos,0,',','.')." PIEZAS",0,0,'L');
      }else{
      	$this->Cell(30,7,"CON  PIEZAS",0,0,'L');
      }
      $this->setXY(60,$poyy);
      $this->SetFont('verdanab','',8);
      if(($cCont20 == "" || $cCont20 == 0) && ($cCont40 == "" || $cCont40 == 0)){
      	$this->Cell(7,7,"CARGA SUELTA",0,0,'L');
      }else{
      	$this->Cell(7,7,"CONTENEDOR: ",0,0,'L');
      }

      $this->setXY(85,$poyy);
      $this->SetFont('verdana','',8);
      if($cCont20 <> "" && $cCont20 <> 0){
      	$Contenedores20 = $cCont20."/20"."'";
      }
			if($cCont40 <> "" && $cCont40 <> 0){
      	$Contenedores40 = $cCont40."/40"."'";
      }
      if($Contenedores20 > 0 && $Contenedores40 > 0){
      	$this->Cell(30,7,$Contenedores20." y ".$Contenedores40,0,0,'L');
      }elseif($Contenedores20 > 0 && ($Contenedores40 == "" || $Contenedores40 == 0)){
      	$this->Cell(30,7,$Contenedores20,0,0,'L');
      }elseif($Contenedores40 > 0 && ($Contenedores20 == "" || $Contenedores20 == 0)){
      	$this->Cell(30,7,$Contenedores40,0,0,'L');
      }
      $poyy += 4;
      if(strlen($_POST['cComObs']) > 0 ){
      	$this->setXY($posx,$poyy);
	      $this->SetFont('verdanab','',8);
	      $this->Cell(10,7,"OBSERVACIONES: ",0,0,'L');
	      $alinea = explode("~",f_Words($_POST['cComObs'],158));
	    	for ($n=0;$n<count($alinea);$n++) {
	    		$this->setXY($posx+30,$poyy);
	      	$this->SetFont('verdana','',8);
	      	$this->Cell(110,7,$alinea[$n],0,0,'L');
	      	$poyy+=3;
				}//for ($n=0;$n<count($alinea);$n++) {
      }//if(strlen($_POST['cComObs']) > 0 ){

      $this->Rect($posx,$posy+3,200,23);
      $this->Rect($posx,87,166,128);
      $this->Rect(176,87,34,123);
      $posy = 86;
      $this->setXY($posx,$posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(166,7,"DESCRIPCION",0,0,'C');
		}//function Header() {

		function Footer() {
		  global $cRoot;   global $cPlesk_Skin_Directory;   global $cNomCopia;   global $nCopia;    global $nb;   global $n;
		  global $vAgeDat;
			$posy = 175;
			$posx = 10;
			$py = $posy;
			$this->SetFont('verdana','',7);
			$this->setXY($posx,$posy);
			$this->MultiCell(150,3,utf8_decode("Efectué sus pagos en: BANCO DE BOGOTA en la cuenta corriente 043046523 o BANCO BANCOLOMBIA en la cuenta corriente número 211-000009-99 o GNB SUDAMERIS cuenta corriente 01010164. Favor enviar su consignación o registro de transferencia a las siguientes direcciones electrónicas: m.baquero@aduanamientos.com; servicioalcliente@aduanamientos.com; cartera@aduanamientos.com."),0);

      $posy = 190;
			$this->setXY($posx,$posy);
		  //$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/firma_aduanamientos.jpg',17,$posy-5,35,15);
		  $this->SetFont('verdanab','',7);
      $this->setXY(10,$posy);
      $this->Cell(40,10,"__________________________",0,0,'L');
      $this->setXY(10,$posy+3);
      $this->Cell(40,10,"FIRMA DEL RESPONSABLE",0,0,'C');
		  $posy = 198;
		  $posx = 10;
		  $this->SetFont('verdanab','',7);
      $this->setXY($posx,$posy);
      $this->Cell(35,10,"AUTORRETEFUENTE",0,0,'C');

      $this->setXY(40,$posy);
      $this->Cell(40,10,"AUTORENTA 0.8%",0,0,'C');

      $this->setXY(70,$posy);
      $this->Cell(40,10,"RETENCION ICA",0,0,'C');

      $this->setXY(100,$posy);
      $this->Cell(40,10,"RETENCION IVA",0,0,'C');

      $this->setXY(135,$posy);
      $posy1 = $posy;
      $this->Cell(31,10,"INGRESOS POR TERCEROS",0,0,'L');
      $this->setXY(172,$posy1);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy1+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy1);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy1+2,34,5);
      $posy1 += 5;
      $this->setXY(135,$posy1);
      $this->Cell(31,10,"INGRESOS PROPIOS",0,0,'L');
      $this->setXY(172,$posy1);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy1+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy1);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy1+2,34,5);
		  $posy += 10;
      $py = $posy;
      $this->SetFont('verdanab','',5.5);
      $this->setXY($posx,$posy+4);
      $this->Cell(135,10,"ESTE   DOCUMENTO   POR    DISPOSICION   DE   LA    LEY    1231",0,0,'L');
      $py += 6;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"DE  17  DE  JULIO  DE  2008  CONSTITUYE   UN  TITULO   VALOR.",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"NO   SE   ACEPTAN   RECLAMOS  O    DEVOLUCIONES     DESPUES",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"DE   10   DIAS   CALENDARIO   CONTADOS  A   PARTIR    DE    LA",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"FECHA  DE RECEPCION  DE  LA PRESENTE  FACTURA DE   VENTA.",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"EL   ACEPTANTE    HACE     CONSTAR    QUE   RECIBIO   REAL    Y ",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"MATERIALMENTE LOS SERVICIOS A SU  ENTERA  SATISFACCION",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"Y QUE ACEPTA LA PRESENTE FACTURA DE  VENTA.",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"LA   CANCELACION   DE   ESTA   FACTURA  DE  VENTA    DESPUES",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"DE SU FECHA DE VENCIMIENTO CAUSARA INTERESES  DE  MORA",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"A  LA TASA  MAXIMA  AUTORIZADA POR LA SUPERINTENDENCIA",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"FINANCIERA   A   LA    FECHA    DE    PAGO   DE   LA  MISMA;   SE",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"LIQUIDARAN  INTERESES   DESDE   EL  DIA   DE    VENCIMIENTO",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"HASTA LA FECHA DE PAGO",0,0,'L');
      $py += 20;
      $this->Rect($posx,$posy+7,72,30);
      $this->Rect(82,$posy+7,53,30);
      $this->SetFont('verdanab','',8);
      $this->setXY(82,$posy+4);
      $this->Cell(50,10,"RECIBIDA",0,0,'C');
      $pyy1 = $posy+11;
      $this->SetFont('verdanab','',7);
      $this->setXY(81,$pyy1);
      $this->Cell(50,10,"______________________________",0,0,'L');
      $pyy1 += 3;
      $this->setXY(81,$pyy1);
      $this->Cell(50,10,"FIRMA DE ACEPTACION",0,0,'L');
      $pyy1 += 4;
      $this->SetFont('verdanab','',5);
      $this->setXY(81,$pyy1);
      $this->Cell(50,10," Nombre___________________________________",0,0,'L');
      $pyy1 += 4;
      $this->SetFont('verdanab','',5);
      $this->setXY(81,$pyy1);
      $this->Cell(50,10," Doc. Identificacion__________________________",0,0,'L');
      $pyy1 += 4;
      $this->SetFont('verdanab','',5);
      $this->setXY(81,$pyy1);
      $this->Cell(50,10," Fecha Recibo Factura________________________",0,0,'L');
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"VALOR DE LA OPERACION",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $posy += 5;
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"IVA",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $posy += 5;
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"RETENCIONES",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $posy += 5;
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"TOTAL",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $posy += 5;
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"ANTICIPOS RECIBIDOS",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $posy += 5;
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"TOTAL A PAGAR",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $posy += 5;
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"SALDO A FAVOR",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $this->setXY(175,259);
      if($nCopia == 0){
  	  	$this->Cell(30,3,"VISTA PREVIA",0,0,'R');
      }else {
      	$this->Cell(30,3,"COPIA ".$nCopia,0,0,'R');
      }
      $posy = 245;
      $this->setXY($posx,$posy);
      $this->SetFont('verdanab','',5);
      $cPiePag  = "LAS MERCANCIAS SE TRANSPORTAN POR CUENTA Y ";
      $cPiePag .= "RIESGO DE NUESTROS CLIENTES Y NO ASEGURAMOS LAS MISMAS DE NO MEDIAR ";
      $cPiePag .= "ORDEN EXPRESA -POR ESCRITO- POR PARTE DE UN FUNCIONARIO ";
      $this->Cell(200,3,$cPiePag,0,0,'L');
      $cPiePag  = "AUTORIZADO POR PARTE DEL CLIENTE PARA HACERLO LA RESPONSABILIDAD ";
      $cPiePag .= "EN LOS SERVICIOS CONEXOS EN LA OPERACION DEPENDEN IGUALMENTE DE LA DECISION ";
      $cPiePag .= "DEL CLIENTE PARA ASEGURAR";
      $posy += 2;
      $this->setXY($posx,$posy);
      $this->Cell(200,3,$cPiePag,0,0,'L');
      $cPiePag = "LA MERCANCIA";
      $posy += 2;
      $this->setXY($posx,$posy);
      $this->Cell(200,3,$cPiePag,0,0,'L');
      $posy += 3;
      $this->SetFont('verdanab','',8);
      $this->setXY($posx,$posy);
      $this->Cell(200,3,"AVENIDA CALLE 24 N 95 12 PORTOS PARQUE INDUSTRIAL PBX ".$vAgeDat['CLITELXX']." FAX ".$vAgeDat['CLIFAXXX'],0,0,'C');
      $posy += 3;
      $this->SetFont('verdanab','',9);
      $this->setXY($posx,$posy);
      $this->Cell(200,3,"Oficinas en: BARRANQUILLA - CARTAGENA - SANTA MARTA - BUENAVENTURA - CUCUTA - IPIALES Y RIOHACHA",0,0,'C');
      $this->SetFont('verdanab','',7);
		  $this->setXY(20,259);
      $this->Cell(40,3,"PAGINA: ".$this->PageNo()." DE {nb}",0,0,'R');
      //$this->Cell(40,3,"PAGINA: ".$nb." DE {nb}",0,0,'R');
		}
  }//class PDF extends FPDF {


  $pdf = new PDF('P','mm','Letter');  //Error al invocar la clase
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->SetFont('verdana','',8);
  $pdf->AliasNbPages();
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);
  $pdf->AddPage();

	$cMoneda = "";
  if($vCliDat['CLINRPXX'] != "SI"){
		$cMoneda = "PESO";

	  $posy = 90;
	  $posx = 10;
	  $posFin = 170;
	  $nb = 1;

	  $pyy = $posy;
	  ##Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##
	  $pdf->SetFont('verdanab','',9);
	  $pdf->setXY($posx,$posy);
	  ##Imprimo Pagos a Terceros ##
	  if(count($mIngTer) > 0 ){//Si la matriz de Pcc tiene registros
	  	$nSubTotPcc = 0;
	  	$pdf->Cell(135,10,"INGRESOS PARA TERCEROS",0,0,'L');
	  	$pyy += 3;
	  	for($i=0;$i<count($mIngTer);$i++){
	  	  if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
	  	    $pdf->AddPage();
	  	    $nb++;
	  	    $posy = 90;
	        $posx = 10;
	        $pyy = $posy;
	        $pdf->SetFont('verdana','',9);
	        $pdf->setXY($posx,$posy);
	  	  }//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion
	  		$nSubTotPcc += $mIngTer[$i]['nComVlr'];
	  		$pdf->SetFont('verdana','',8);
	  		$pdf->setXY($posx,$pyy);
	  		$pdf->setXY(145,$pyy);
	  		$pdf->Cell(31,10,"",0,0,'R');
	  		$pdf->setXY(176,$pyy);
	  		$pdf->Cell(34,10,number_format($mIngTer[$i]['nComVlr'],0,',','.'),0,0,'R');
	  		$cComObs  = explode("^",$mIngTer[$i]['cComObs']);
	  		if($mIngTer[$i]['ccomcsc3'] <> ""){
	        $cComObsv = str_replace("CANTIDAD", "CANT",($cComObs[0].". ".$mIngTer[$i]['ccomcsc3']));
	        $aIngTer = explode("~",f_Words($cComObsv,170));
	  			for ($n=0;$n<count($aIngTer);$n++) {
	      		$pdf->setXY($posx,$pyy);
	        	$pdf->Cell(135,10,$aIngTer[$n],0,0,'L');
	        	$pyy+=3;
	      	}
	  		  //$pdf->Cell(135,10,substr(str_replace("CANTIDAD", "CANT", $cComObsv),0,85),0,0,'L');
	  		}else {
	  		  $cComObsv = $cComObs[0];
	  		  $pdf->setXY($posx,$pyy);
	  		  $pdf->Cell(135,10,substr(str_replace("CANTIDAD", "CANT", $cComObsv),0,170),0,0,'L');
	  		  $pyy +=3;
	  		}
	  		$pyy -= 3;
	  	}//for($i=0;$i<count($mIngTer);$i++){
	  	if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
	  	   $pdf->AddPage();
	  	   $nb++;
	  	   $posy = 90;
	       $posx = 10;
	       $pyy = $posy;
	       $pdf->SetFont('verdana','',9);
	       $pdf->setXY($posx,$posy);
	  	}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

	  	##Imprimo Subtotal de Pagos a Terceros ##
	  	/*$pdf->SetFont('verdanab','',8);
	  	$pdf->setXY($posx,$pyy);
	  	$pdf->Cell(135,10,"SUBTOTAL PAGOS A TERCEROS",0,0,'L');
	  	$pdf->setXY(145,$pyy);
	  	$pdf->Cell(31,10,"",0,0,'R');
	  	$pdf->setXY(176,$pyy);
	  	$pdf->Cell(30,10,number_format($nSubTotPcc,0,',','.'),0,0,'R');*/
	  	##Fin Imprimo Subtotal de Pagos a Terceros ##
	  }//if(count($mIngTer) > 0 || $nBandPcc == 1){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
	  ##Fin Imprimo Pagos a Terceros ##

	  if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
  		$pdf->AddPage();
  	 	$nb++;
  	 	$posy = 90;
     	$posx = 10;
     	$pyy = $posy;
     	$pdf->SetFont('verdana','',9);
     	$pdf->setXY($posx,$posy);
	  }//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion
	  $pyy += 5;
	  $nSubToIP = 0;
	  if($_POST['nSecuencia_IPA'] > 0 || count($mIngPro) > 0){//Valido si la Secuencia de la Grilla de Ip viene mayor a cero para imprimir bloque de INGRESOS PROPIOS
	  	$pdf->setXY($posx,$pyy);
	  	$pdf->SetFont('verdanab','',9);
	  	$pdf->Cell(135,10,"INGRESOS PROPIOS",0,0,'L');
		  ##Imprimo Ingresos Propios##
		  $pyy += 3;
	    for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
	      if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
	  	    $pdf->AddPage();
	  	    $nb++;
	  	    $posy = 90;
	        $posx = 10;
	        $pyy = $posy;
	        $pdf->SetFont('verdana','',9);
	        $pdf->setXY($posx,$posy);
	  	  }//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion
	   		$nSubToIP += $_POST['nComVlr_IPA'.($i+1)];
			if($mIngPro[$i]['comvlrxx'] != 0){
				$pdf->SetFont('verdana','',8);
				$pdf->setXY($posx,$pyy);

				$cValor = "";
				// if($mIngPro[$i]['comfobxx'] == "SI" && $mIngPro[$i]['docfobxx'] > 0) {
				// 	$cValor  = " FOB: ($".number_format($mIngPro[$i]['docfobxx'],2,'.',',');
				// 	$cValor .= ($mIngPro[$i]['doctrmxx'] > 0) ? " TRM: $".number_format($mIngPro[$i]['doctrmxx'],2,'.',',') : "";
				// 	$cValor .= ")]";
				// }
				// if ($mIngPro[$i]['comcifap'] == "SI"){
				// 	$cValor = " CIF: $(".number_format($mIngPro[$i]['comcifxx'],0,'.',',').')';
				// }
				if ($mIngPro[$i]['comdimap'] == "SI"){
					$cValor = " DIM: (".number_format($mIngPro[$i]['comdimxx'],0,'.',',').')';
				}
				if ($mIngPro[$i]['comdavap'] == "SI"){
					$cValor = " DAV: (".number_format($mIngPro[$i]['comdavxx'],0,'.',',').')';
				}
				// if ($mIngPro[$i]['comvucap'] == "SI"){
				// 	$cValor = " VUCE: (".number_format($mIngPro[$i]['comvucxx'],0,'.',',').")";
				// }
				// if ($mIngPro[$i]['comcerap'] == "SI"){
				// 	$cValor = " CERTIFICADOS DE ORIGEN: (".number_format($mIngPro[$i]['comcerxx'],0,'.',',').")";
				// }
				// if ($mIngPro[$i]['comhorap'] == "SI"){
				// 		$cValor = " HORAS: (".number_format($mIngPro[$i]['comhorxx'],0,'.',',').")";
				// }
				// if ($mIngPro[$i]['compieap'] == "SI"){
				// 	$cValor = " PIEZAS: (".number_format($mIngPro[$i]['compiexx'],0,'.',',').")";
				// }
				// if ($mIngPro[$i]['comdexap'] == "SI"){
				// $cValor = " DEX: (".number_format($mIngPro[$i]['comdexxx'],0,'.',',').')';
				// }
				// if ($mIngPro[$i]['comserap'] == "SI"){
				// $cValor = " SERIAL: (".number_format($mIngPro[$i]['comserxx'],0,'.',',').')';
				// }
				// if ($mIngPro[$i]['comaraap'] == "SI"){
				// $cValor = " CANT.: (".number_format($mIngPro[$i]['comaraxx'],0,'.',',').')';
				// }
				// if ($mIngPro[$i]['comdtaap'] == "SI"){
				// $cValor = " DTA: (".number_format($mIngPro[$i]['comdtaxx'],0,'.',',').')';
				// }
				// if ($mIngPro[$i]['comiteap'] == "SI"){
				// $cValor = " ITEMS: (".number_format($mIngPro[$i]['comitexx'],0,'.',',').')';
				// }
				// if ($mIngPro[$i]['comcanap'] == "SI"){
				// $cValor = " CANTIDAD: (".number_format($mIngPro[$i]['comcanxx'],0,'.',',').')';
				// }
				// // if ($mIngPro[$i]['comfobap'] == "SI"){
				// // 	$cValor = " FOB: ($".number_format($mIngPro[$i]['comfob2x'],0,'.',',').')';
				// // }
				// if ($mIngPro[$i]['comc20ap'] == "SI" || $mIngPro[$i]['comc40ap'] == "SI" || $mIngPro[$i]['comcsuap'] == "SI"){
				// $cValor = "";
				// 	if($mIngPro[$i]['comc20ap'] == "SI"){
				// 	$cValor .= " CONTENEDORES DE 20: (".number_format($mIngPro[$i]['comc20xx'],0,'.',',').')';
				// 	}
				// 	if($mIngPro[$i]['comc40ap'] == "SI"){
				// 	$cValor .= " CONTENEDORES DE 40: (".number_format($mIngPro[$i]['comc40xx'],0,'.',',').')';
				// 	}
				// 	if($mIngPro[$i]['comcsuap'] == "SI"){
				// 	$cValor .= " UNIDADES DE CARGA SUELTA: (".number_format($mIngPro[$i]['comcsuxx'],0,'.',',').')';
				// 	}
				// }
				$pdf->Cell(135,10,$mIngPro[$i]['comobsxx'].$cValor,0,0,'L');
				//$pdf->Cell(135,10,substr($_POST['cComObs_IPA'.($i+1)],0,100),0,0,'L');
				$pdf->setXY(145,$pyy);
				$pdf->Cell(31,10,"",0,0,'R');
				$pdf->setXY(176,$pyy);
				$pdf->Cell(34,10,number_format($mIngPro[$i]['comvlrxx'],0,',','.'),0,0,'R');
				$pyy +=3;
			}
		  }
		  ##Fin Imprimo Ingresos Propios##

		  if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
	  	  $pdf->AddPage();
	  	  $nb++;
	  	  $posy = 90;
	      $posx = 10;
	      $pyy = $posy;
	      $pdf->SetFont('verdana','',9);
	      $pdf->setXY($posx,$posy);
	  	}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		  ##Imprimo Subtotal de Ingresos Propios ##
	  	/*$pdf->SetFont('verdanab','',8);
	  	$pdf->setXY($posx,$pyy);
	  	$pdf->Cell(135,10,"SUBTOTAL INGRESOS PROPIOS",0,0,'L');
	  	$pdf->setXY(145,$pyy);
	  	$pdf->Cell(31,10,"",0,0,'R');
	  	$pdf->setXY(176,$pyy);
	  	$pdf->Cell(30,10,number_format($nSubToIP,0,',','.'),0,0,'R');*/
		  ##Imprimo Subtotal de Ingresos Propios ##
	  }//if($nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
	  /*$pdf->Rect($posx,$posy+2,135,130);
	  $pdf->Rect(145,$posy+2,31,130);
	  $pdf->Rect(176,$posy+2,30,130);*/
	  ##Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##

	  ##Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##
	  $nSubToFac = $nSubTotPcc + $nSubToIP;
	  ##Fin Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##


	 	##Busco Valor de RET.IVA ##
		$nTotIva = 0;
	  for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
	    $nTotIva +=$_POST['nVlrIva_IPA'.($i+1)];
	  }
	 	##Fin Busco Valor de RET.IVA ##

	  ##Busco Valor de RET.ICA ##
		$nTotIca = 0;
	  for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
	    $nTotIca +=$_POST['nVlrIca_IPA'.($i+1)];
	  }
	 	##Fin Busco Valor de RET.ICA ##

	  ##Busco Valor de RET.FTE ##
		$nTotRfte = 0;
	  for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
	    $nTotRfte +=$_POST['nVlrFte_IPA'.($i+1)];
	  }
	 	##Fin Busco Valor de RET.FTE ##


	  ##Busco Valor de RET.CRE ##
	  $nTotCre = 0;
	  for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
	  	$nTotCre +=$_POST['nVlrCre_IPA'.($i+1)];
	  }
	  ##Fin Busco Valor de RET.CRE ##



  	$posy = 202;
	  $pdf->SetFont('verdanab','',8);

	  if($nTotRfte <> 0){
	  	$pdf->setXY($posx,$posy);
	  	$pdf->Cell(35,10,number_format($nTotRfte,0,',','.'),0,0,'C');
	  }

	  if($nTotCre <> 0){
	  	$pdf->setXY(40,$posy);
	  	$pdf->Cell(40,10,number_format($nTotCre,0,',','.'),0,0,'C');
	  }
	  if($nTotIca <> 0){
	  	$pdf->setXY(70,$posy);
	  	$pdf->Cell(40,10,number_format($nTotIca,0,',','.'),0,0,'C');
	  }
	  if($nTotIva <> 0){
	  	$pdf->setXY(100,$posy);
	  	$pdf->Cell(40,10,number_format($nTotIva,0,',','.'),0,0,'C');
	  }

	  $posy1 = 198;
	  $pdf->setXY(176,$posy1);
	  $pdf->SetFont('verdanab','',8);
	  if($nSubTotPcc != 0){
	  	$pdf->Cell(34,10,number_format($nSubTotPcc,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  if($nSubToIP != 0){
	  	$pdf->Cell(34,10,number_format($nSubToIP,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  if($nSubToFac != 0){
	  	$pdf->Cell(34,10,number_format($nSubToFac,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }


	  //$posy = 208;
	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  if($_POST['nIPAIva'] != 0){
	  	$pdf->Cell(34,10,number_format($_POST['nIPAIva'],0,',','.'),0,0,'R');
	  }else {
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  $nTotRet = $nTotIva+$nTotIca;
	  if($nTotRet != 0){
	  	$pdf->Cell(34,10,number_format($nTotRet,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $nTotal = ($nSubToFac+$_POST['nIPAIva'])-($nTotIva+$nTotIca);
	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  if($nTotal != 0){
	  	$pdf->Cell(34,10,number_format($nTotal,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  if($_POST['nIPAAnt'] != 0){
	  	$pdf->Cell(34,10,number_format($_POST['nIPAAnt'],0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy1 += 5;
	 	$pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
  	$nTotPag = str_replace("-","",$_POST['nIPASal']);
	  $cNegativo = "";
	  if(substr($_POST['nIPASal'],0,1) == "-"){
	  	$cNegativo = "-";
	  }else{
	  	$cNegativo = "";
	  }
	  if($_POST['nIPASal'] != 0){
	   if($cNegativo == "-"){
	   	$pdf->setXY(176,$posy1+5);
	   	$pdf->Cell(34,10,number_format($_POST['nIPASal'],0,',','.'),0,0,'R');
	   }else{
	   	$pdf->Cell(34,10,number_format($_POST['nIPASal'],0,',','.'),0,0,'R');
	   }
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

		$pdf->setXY(10,$posy+7.7);
		$pdf->SetFont('verdanab','',6);
		$cVlrLetra = f_Cifra_Php(abs($nTotPag),$cMoneda);
		$pdf->MultiCell(120, 2.5, "SON:" . utf8_decode($cVlrLetra),0,'L');

  }else{
	  ##Si calidad de Tercero es NORESIDENTE Imprimo factura en Dolares##
		$cMoneda = "DOLAR";

	  $posy = 90;
	  $posx = 10;
	  $posFin = 170;
	  $nb = 1;

	  $pyy = $posy;
	  ##Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##
	  $pdf->SetFont('verdanab','',9);
	  $pdf->setXY($posx,$posy);
	  ##Imprimo Pagos a Terceros ##
	  if(count($mIngTer) > 0 ){//Si la matriz de Pcc tiene registros
	  	$nSubTotPcc = 0;
	  	$pdf->Cell(135,10,"INGRESOS PARA TERCEROS",0,0,'L');
	  	$pyy += 3;
	  	for($i=0;$i<count($mIngTer);$i++){
	  	  if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
	  	    $pdf->AddPage();
	  	    $nb++;
	  	    $posy = 90;
	        $posx = 10;
	        $pyy = $posy;
	        $pdf->SetFont('verdana','',9);
	        $pdf->setXY($posx,$posy);
	  	  }//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion
	  		$nSubTotPcc += $mIngTer[$i]['nComVlr'];
	  		$pdf->SetFont('verdana','',8);
	  		$pdf->setXY(176,$pyy);
	  		$pdf->Cell(34,10,number_format($mIngTer[$i]['nComVlr'],2,',','.'),0,0,'R');
	  		$pdf->setXY($posx,$pyy);
	  		$cComObs  = explode("^",$mIngTer[$i]['cComObs']);
	  		if($mIngTer[$i]['ccomcsc3'] <> ""){
	  			$cComObsv = str_replace("CANTIDAD", "CANT",($cComObs[0].". ".$mIngTer[$i]['ccomcsc3']));
	        $aIngTer = explode("~",f_Words($cComObsv,170));
	  			for ($n=0;$n<count($aIngTer);$n++) {
	      		$pdf->setXY($posx,$pyy);
	        	$pdf->Cell(135,10,$aIngTer[$n],0,0,'L');
	        	$pyy+=3;
	      	}
	  		  //$pdf->Cell(135,10,substr(str_replace("CANTIDAD", "CANT", $cComObsv),0,85),0,0,'L');
	  		}else {
	  		  $cComObsv = $cComObs[0];
	  		  $pdf->setXY($posx,$pyy);
	  		  $pdf->Cell(135,10,substr(str_replace("CANTIDAD", "CANT", $cComObsv),0,170),0,0,'L');
	  		  $pyy +=3;
	  		}
	  		$pyy -= 3;
	  	}//for($i=0;$i<count($mIngTer);$i++){
	  	if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
	  	   $pdf->AddPage();
	  	   $nb++;
	  	   $posy = 90;
	       $posx = 10;
	       $pyy = $posy;
	       $pdf->SetFont('verdana','',9);
	       $pdf->setXY($posx,$posy);
	  	}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

	  	##Imprimo Subtotal de Pagos a Terceros ##
	  	/*$pdf->SetFont('verdanab','',8);
	  	$pdf->setXY($posx,$pyy);
	  	$pdf->Cell(135,10,"SUBTOTAL PAGOS A TERCEROS",0,0,'L');
	  	$pdf->setXY(145,$pyy);
	  	$pdf->Cell(31,10,number_format($nSubTotPcc,2,',','.'),0,0,'R');
	  	$pdf->setXY(176,$pyy);
	  	$pdf->Cell(30,10,"",0,0,'R');*/
	  	##Fin Imprimo Subtotal de Pagos a Terceros ##
	  }//if(count($mIngTer) > 0 || $nBandPcc == 1){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
	  ##Fin Imprimo Pagos a Terceros ##

	  if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
	  	   $pdf->AddPage();
	  	   $nb++;
	  	   $posy = 90;
	       $posx = 10;
	       $pyy = $posy;
	       $pdf->SetFont('verdana','',9);
	       $pdf->setXY($posx,$posy);
	  }//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion
	  $pyy += 5;
	  $nSubToIP = 0;
	  //f_Mensaje(__FILE__,__LINE__,$_POST['nSecuencia_IPA']);
	  if($_POST['nSecuencia_IPA'] > 0){//Valido si la Secuencia de la Grilla de Ip viene mayor a cero para imprimir bloque de INGRESOS PROPIOS
	  	$pdf->setXY($posx,$pyy);
	  	$pdf->SetFont('verdanab','',9);
	  	$pdf->Cell(135,10,"INGRESOS PROPIOS",0,0,'L');
		  ##Imprimo Ingresos Propios##
		  $pyy += 3;
	    for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
	      if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
	  	    $pdf->AddPage();
	  	    $nb++;
	  	    $posy = 90;
	        $posx = 10;
	        $pyy = $posy;
	        $pdf->SetFont('verdana','',9);
	        $pdf->setXY($posx,$posy);
	  	  }//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion
	   		$nSubToIP += $_POST['nComVlr_IPA'.($i+1)];
		   	$pdf->SetFont('verdana','',8);
	  	 	$pdf->setXY($posx,$pyy);
				$cValor = "";
				// if($mIngPro[$i]['comfobxx'] == "SI" && $mIngPro[$i]['docfobxx'] > 0) {
				// 	$cValor  = " FOB: ($".number_format($mIngPro[$i]['docfobxx'],2,'.',',');
				// 	$cValor .= ($mIngPro[$i]['doctrmxx'] > 0) ? " TRM: $".number_format($mIngPro[$i]['doctrmxx'],2,'.',',') : "";
				// 	$cValor .= ")]";
				// }
				// if ($mIngPro[$i]['comcifap'] == "SI"){
				// 	$cValor = " CIF: $(".number_format($mIngPro[$i]['comcifxx'],0,'.',',').')';
				// }
				// if ($mIngPro[$i]['comdimap'] == "SI"){
				// 	$cValor = " DIM: (".number_format($mIngPro[$i]['comdimxx'],0,'.',',').')';
				// }
				// if ($mIngPro[$i]['comdavap'] == "SI"){
				// 	$cValor = " DAV: (".number_format($mIngPro[$i]['comdavxx'],0,'.',',').')';
				// }
				// if ($mIngPro[$i]['comvucap'] == "SI"){
				// 	$cValor = " VUCE: (".number_format($mIngPro[$i]['comvucxx'],0,'.',',').")";
				// }
        			// if ($mIngPro[$i]['comcerap'] == "SI"){
				// 	$cValor = " CERTIFICADOS DE ORIGEN: (".number_format($mIngPro[$i]['comcerxx'],0,'.',',').")";
				// }
				// if ($mIngPro[$i]['comhorap'] == "SI"){
				// 		$cValor = " HORAS: (".number_format($mIngPro[$i]['comhorxx'],0,'.',',').")";
				// }
				// if ($mIngPro[$i]['compieap'] == "SI"){
				// 	$cValor = " PIEZAS: (".number_format($mIngPro[$i]['compiexx'],0,'.',',').")";
				// }
				// if ($mIngPro[$i]['comdexap'] == "SI"){
  				// $cValor = " DEX: (".number_format($mIngPro[$i]['comdexxx'],0,'.',',').')';
  				// }
				// if ($mIngPro[$i]['comserap'] == "SI"){
  				// $cValor = " SERIAL: (".number_format($mIngPro[$i]['comserxx'],0,'.',',').')';
  				// }
				// if ($mIngPro[$i]['comaraap'] == "SI"){
  				// $cValor = " CANT.: (".number_format($mIngPro[$i]['comaraxx'],0,'.',',').')';
  				// }
				// if ($mIngPro[$i]['comdtaap'] == "SI"){
  				// $cValor = " DTA: (".number_format($mIngPro[$i]['comdtaxx'],0,'.',',').')';
  				// }
				// if ($mIngPro[$i]['comiteap'] == "SI"){
  				// $cValor = " ITEMS: (".number_format($mIngPro[$i]['comitexx'],0,'.',',').')';
  				// }
				// if ($mIngPro[$i]['comcanap'] == "SI"){
  				// $cValor = " CANTIDAD: (".number_format($mIngPro[$i]['comcanxx'],0,'.',',').')';
  				// }
				// // if ($mIngPro[$i]['comfobap'] == "SI"){
  				// // 	$cValor = " FOB: ($".number_format($mIngPro[$i]['comfob2x'],0,'.',',').')';
  				// // }
				// if ($mIngPro[$i]['comc20ap'] == "SI" || $mIngPro[$i]['comc40ap'] == "SI" || $mIngPro[$i]['comcsuap'] == "SI"){
				// $cValor = "";
				// 	if($mIngPro[$i]['comc20ap'] == "SI"){
				// 	$cValor .= " CONTENEDORES DE 20: (".number_format($mIngPro[$i]['comc20xx'],0,'.',',').')';
				// 	}
				// 	if($mIngPro[$i]['comc40ap'] == "SI"){
				// 	$cValor .= " CONTENEDORES DE 40: (".number_format($mIngPro[$i]['comc40xx'],0,'.',',').')';
				// 	}
				// 	if($mIngPro[$i]['comcsuap'] == "SI"){
				// 	$cValor .= " UNIDADES DE CARGA SUELTA: (".number_format($mIngPro[$i]['comcsuxx'],0,'.',',').')';
				// 	}
				// }
				$pdf->Cell(135,10,$mIngPro[$i]['comobsxx'].$cValor,0,0,'L');
	  		$pdf->setXY(176,$pyy);
	  		$pdf->Cell(34,10,number_format($mIngPro[$i]['comvlrxx'],2,',','.'),0,0,'R');
	  		$pyy +=3;
		  }

		  //for ($k=0;$k<count($mCodDat);$k++) {
		  ##Fin Imprimo Ingresos Propios##

		  if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
	  	  $pdf->AddPage();
	  	  $nb++;
	  	  $posy = 90;
	      $posx = 10;
	      $pyy = $posy;
	      $pdf->SetFont('verdana','',9);
	      $pdf->setXY($posx,$posy);
	  	}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		  ##Imprimo Subtotal de Ingresos Propios ##
	  	/*$pdf->SetFont('verdanab','',8);
	  	$pdf->setXY($posx,$pyy);
	  	$pdf->Cell(135,10,"SUBTOTAL INGRESOS PROPIOS",0,0,'L');
	  	$pdf->setXY(145,$pyy);
	  	$pdf->Cell(31,10,number_format($nSubToIP,2,',','.'),0,0,'R');
	  	$pdf->setXY(176,$pyy);
	  	$pdf->Cell(30,10,"",0,0,'R');*/
		  ##Imprimo Subtotal de Ingresos Propios ##
	  }//if($nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
	  /*$pdf->Rect($posx,$posy+2,135,130);
	  $pdf->Rect(145,$posy+2,31,130);
	  $pdf->Rect(176,$posy+2,30,130);*/
	  ##Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##

	  ##Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##
	  $nSubToFac = $nSubTotPcc + $nSubToIP;
	  ##Fin Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##


	 	##Busco Valor de RET.IVA ##
		$nTotIva = 0;
	  for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
	    $nTotIva +=$_POST['nVlrIva_IPA'.($i+1)];
	  }
	 	##Fin Busco Valor de RET.IVA ##

	  ##Busco Valor de RET.ICA ##
		$nTotIca = 0;
	  for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
	    $nTotIca +=$_POST['nVlrIca_IPA'.($i+1)];
	  }
	 	##Fin Busco Valor de RET.ICA ##

	  ##Busco Valor de RET.FTE ##
		$nTotRfte = 0;
	  for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
	    $nTotRfte +=$_POST['nVlrFte_IPA'.($i+1)];
	  }
	 	##Fin Busco Valor de RET.FTE ##

  	$posy = 202;
	  $pdf->SetFont('verdanab','',8);
	  if($nTotRfte <> 0){
	  	$pdf->setXY($posx,$posy);
	  	$pdf->Cell(40,10,number_format($nTotRfte,2,',','.'),0,0,'C');
	  }
	  if($nTotIca <> 0){
	  	$pdf->setXY(50,$posy);
	  	$pdf->Cell(40,10,number_format($nTotIca,2,',','.'),0,0,'C');
	  }
	  if($nTotIva <> 0){
	  	$pdf->setXY(90,$posy);
	  	$pdf->Cell(40,10,number_format($nTotIva,2,',','.'),0,0,'C');
	  }
	  $posy1 = 198;
	  $pdf->setXY(176,$posy1);
	  $pdf->SetFont('verdanab','',8);
	  $pdf->Cell(34,10,number_format($nSubTotPcc,2,',','.'),0,0,'R');
	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  $pdf->Cell(34,10,number_format($nSubToIP,2,',','.'),0,0,'R');
	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  $pdf->Cell(34,10,number_format($nSubToFac,2,',','.'),0,0,'R');

	  //$posy = 208;
	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  $pdf->Cell(34,10,number_format($_POST['nIPAIva'],2,',','.'),0,0,'R');
	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  $pdf->Cell(34,10,number_format($nTotIva+$nTotIca,2,',','.'),0,0,'R');
	  $nTotal = ($nSubToFac+$_POST['nIPAIva'])-($nTotIva+$nTotIca);
	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  $pdf->Cell(34,10,number_format($nTotal,0,',','.'),2,0,'R');
	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  $pdf->Cell(34,10,number_format($_POST['nIPAAnt'],2,',','.'),0,0,'R');
	  $posy1 += 5;
	 	$pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  $pdf->Cell(34,10,number_format($_POST['nIPASal'],2,',','.'),0,0,'R');
	  $nTotPag = str_replace("-","",$_POST['nIPASal']);
	  $cNegativo = "";
	  if(substr($_POST['nIPASal'],0,1) == "-"){
	  	$cNegativo = "MENOS ";
	  }else{
	  	$cNegativo = "";
	  }
	  $posy += 7;

		$pdf->setXY(10,$posy+1);
		$pdf->SetFont('verdanab','',6);
		$cVlrLetra = f_Cifra_Php(abs($nTotPag),$cMoneda);
		$pdf->MultiCell(120, 2.5, "SON:" . utf8_decode($cVlrLetra),0,'L');
  } ##Fin Si calidad de Terceros es NORESIDENTE Imprimo factura en Dolares ##

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
     $xFecFor= $fano."-".$fmes."-".$fdia;
   }
   return ($xFecFor);
 }
?>

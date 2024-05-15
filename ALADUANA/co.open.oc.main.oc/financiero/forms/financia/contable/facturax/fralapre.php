<?php
  /**
	 * Imprime Vista Previa Factura de Venta ALADUANA.
	 * --- Descripcion: Permite Imprimir Vista Previa de la Factura de Venta.
	 * @author Camilo Dulce <camilo.dulce@opentecnologia.com.co>
	 */

	// ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");

  $cRoot = $_SERVER['DOCUMENT_ROOT'];
  $cEstiloLetra = 'arial';
  $cEstiloLetraOfton = 'ofton1';
	$cEstiloLetrab = 'arialb';

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
  		$cNewYear = substr($cRegFCre,0,4);
	    //$cAno     = substr($cRegFCre,0,4);
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

  ##Traigo Dias de Plazo ##
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
  ##Fin Traigo Dias de Plazo ##

  /*** Consulto en que bloque se deben imprimir los anticipos PCC o IP ***/
  if( $vCccDat['ccccdant'] == "PCC" ){
    $nAnticipo = 1; // anticipos en pagos a terceros
  }else if($vCccDat['ccccdant'] == "AMBOS" || $vCccDat['ccccdant'] == ""){
    $nAnticipo = 2; // anticipos en ingresos propios
  }

  // $mAnticipos = array();
  // $qANT  = "SELECT * ";
  // $qANT .= "FROM $cAlfa.$cTabla_ANT ";
  // $qANT .= "WHERE ";
  // $qANT .= "cUsrId_ANT = \"{$_COOKIE['kUsrId']}\" AND ";
  // $qANT .= "cFacId_ANT = \"{$_POST['cFacId']}\" ";
  // $qANT .= "ORDER BY cComFec_ANT ASC ";
  // $xANT  = f_MySql("SELECT","",$qANT,$xConexion01,"");
  // // f_Mensaje(__FILE__,__LINE__,$qANT."~".mysql_num_rows($xANT));
  // # Armando Matriz de Anticipos
  // while ($xRANT = mysql_fetch_array($xANT)) {
  //   $nInd_mAnticipos = count($mAnticipos);
  //   $mAnticipos[$nInd_mAnticipos]['comfecxx'] = $xRANT['cComFec_ANT'];
  //   $mAnticipos[$nInd_mAnticipos]['puctipej'] = $xRANT['cPucTipEj_ANT'];
  //   $mAnticipos[$nInd_mAnticipos]['comvlrxx'] = $xRANT['nComVlr_ANT'];
  //   $mAnticipos[$nInd_mAnticipos]['comvlrnf'] = $xRANT['nComVlrNF_ANT'];
  //   $mAnticipos[$nInd_mAnticipos]['comcscxx'] = $xRANT['cComCsc_ANT'];
  // }
  // f_Mensaje(__FILE__,__LINE__,$_POST['cForImp']." Anticipo: ".$nAnticipo);

	## Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Dietrich Para encabezado de factura ##
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

  /*** Nombre del usuario logueado. ***/
  $qUsrNom  = "SELECT USRNOMXX ";
  $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
  $qUsrNom .= "WHERE ";
  $qUsrNom .= "USRIDXXX = \"$kUser\" LIMIT 0,1 ";
  $xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qUsrNom."~".mysql_num_rows($xUsrNom));
  $vUsrNom  = mysql_fetch_array($xUsrNom);

  // Busco la Sucursal por la cual se hizo la Factura en la Parametrica de Comprobantes
  $qSucFac  = "SELECT * ";
  $qSucFac .= "FROM $cAlfa.fpar0117 ";
  $qSucFac .= "WHERE $cAlfa.fpar0117.comidxxx = \"$cComId\" AND ";
  $qSucFac .= "$cAlfa.fpar0117.comcodxx = \"$cComCod\" LIMIT 0,1";
  $xSucFac  = f_MySql("SELECT","",$qSucFac,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qSucFac."~".mysql_num_rows($xSucFac));
  $vSucFac  = mysql_fetch_array($xSucFac);

  ##Consulto en la SIAI0150 Datos del Facturado A: ##
  $qCliDat  = "SELECT ";
  $qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIDIRXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIDIR3X, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLITELXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIFAXXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLINRPXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.PAIIDXXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.DEPIDXXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CIUIDXXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLICONTX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIPLAXX  ";
	$qCliDat .= "FROM $cAlfa.SIAI0150 ";
	$qCliDat .= "WHERE ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$_POST['cTerIdInt']}\" AND ";
	$qCliDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
	$xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,$qCliDat);
  $nFilCli  = mysql_num_rows($xCliDat);
  if ($nFilCli > 0) {
    $vCliDat = mysql_fetch_array($xCliDat);
  }
  ##Consulto en la SIAI0150 Datos del Facturado A: ##

  ##Traigo Pais del Facturado A ##
  $qPaiDat  = "SELECT PAIDESXX ";
  $qPaiDat .= "FROM $cAlfa.SIAI0052 ";
  $qPaiDat .= "WHERE ";
  $qPaiDat .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$vCliDat['PAIIDXXX']}\" AND ";
  $qPaiDat .= "$cAlfa.SIAI0052.REGESTXX = \"ACTIVO\" ";
  //f_Mensaje(__FILE__,__LINE__,$qPaiDat);
  $xPaiDat  = f_MySql("SELECT","",$qPaiDat,$xConexion01,"");
  $nFilCiu  = mysql_num_rows($xPaiDat);
  if ($nFilCiu > 0) {
    $vPaiDat = mysql_fetch_array($xPaiDat);
  }
  ##Fin Traigo Pais del Facturado A ##

  ##Traigo Ciudad del Facturado A ##
  $qCiuDat  = "SELECT * ";
  $qCiuDat .= "FROM $cAlfa.SIAI0055 ";
  $qCiuDat .= "WHERE ";
  $qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCliDat['PAIIDXXX']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCliDat['DEPIDXXX']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCliDat['CIUIDXXX']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
  //f_Mensaje(__FILE__,__LINE__,$qCiuDat);
  $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
  $nFilCiu  = mysql_num_rows($xCiuDat);
  if ($nFilCiu > 0) {
    $vCiuDat = mysql_fetch_array($xCiuDat);
  }
  ##Fin Traigo Ciudad del Facturado A ##

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
  //f_Mensaje(__FILE__,__LINE__,$vCliDat['CLICONTX']);
 	$xConDat  = f_MySql("SELECT","",$qConDat,$xConexion01,"");
  $nFilCon  = mysql_num_rows($xConDat);
  if ($nFilCon > 0) {
    $vConDat = mysql_fetch_array($xConDat);
  }
  ##Fin Traigo Datos de Contacto del Facturado a ##

	##Traigo Primer Do para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##
	$cSucId = ""; $cDocId  = ""; $cDocSuf = "";
  $dFecMay = date("Y"); //Fecha
	for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
	  if($i == 0) {
      $cDocId   = $_POST['cDosNro_DOS'.($i+1)];
      $cDocSuf  = $_POST['cDosSuf_DOS'.($i+1)];
      $cSucId   = $_POST['cSucId_DOS' .($i+1)];
    }
    $dFecMay = ($dFecMay > substr($_POST['cDosFec_DOS'.($i+1)],0,4)) ? substr($_POST['cDosFec_DOS'.($i+1)],0,4) : $dFecMay;
  }//for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
  $nAnoIniDo = (($dFecMay-1) <  $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($dFecMay-1);
  ##Fin Traigo Primer Do para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##

  ##Trayendo Datos de Do Dependiendo del Tipo de Operacion ##
  $vDatDo = f_Datos_Operativos_Do($cSucId, $cDocId, $cDocSuf);
  $vDceDat    = $vDatDo['decdatxx'];
  $cTasCam    = $vDatDo['tascamxx']; //Tasa de Cambio
  $cDocTra    = $vDatDo['doctraxx']; //Documento de Transporte
  $cBultos    = $vDatDo['bultosxx']; //Bultos
  $cPesBru    = $vDatDo['pesbruxx']; //Peso Bruto
  $nValAdu    = $vDatDo['valaduxx']; //Valor en aduana
  $nValAduCop = $vDatDo['valaduco']; //Valor en aduana
  $nValAduUsd = $vDatDo['valaduus']; //Valor en aduana
  $cOpera     = $vDatDo['operaxxx']; //texto valor CIF: o FOB: segun el tipo operacion del tramite
  $cPedido    = $vDatDo['pedidoxx']; //Pedido
  $cAduana    = $vDatDo['aduanaxx']; //Descripcion Aduana
  $cNomVen    = $vDatDo['nomvenxx']; //Nombre Vendedor
  $cOrdCom    = $vDatDo['ordcomxx']; //Orden de Compra
  $cPaiOri    = $vDatDo['paiorixx']; //Pais de Origen
  $cDepOri    = $vDatDo['deporide']; //Departamento Origen
  $cPaiOri    = $vDatDo['paioride']; //Pais Origen
  $cDesMer    = $vDatDo['desmerxx']; //Descripcion Mercancia
  $cNumVap    = $vDatDo['numvapxx']; //Numero vapor
  $cLimStk    = $vDatDo['limstkxx']; //Autoadhesivo de la primera declaracion
  $cLugIngDes = $vDatDo['lindesxx']; //Lugar de Ingreso Descripcion
  $cSucDes    = $vDatDo['sucdesxx']; //Sucursal del DO
  $cObsCom    = $vDatDo['doiobsal']; //Observacion ALADUANA
  ###Fin Trayendo Datos de Do Dependiendo del Tipo de Operacion ##

	#Agrupo Ingresos Propios
  $mIP = array();
  for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
      
    //Traigo las cantidades y el detalle de los IP del utiliqdo.php
		$vDatosIp = array();
		$cObs = "|".$_POST['cSerId_IPA'.($i+1)]."~".$_POST['cFcoId_IPA'.($i+1)]."~".$_POST['cComObs_IPA'.($i+1)]."|";
    $vDatosIp = f_Cantidad_Ingreso_Propio($cObs,'',$_POST['cSucId_IPA'.($i+1)],$_POST['cDosNro_IPA'.($i+1)],$_POST['cDosSuf_IPA'.($i+1)]);

    $mIP[$_POST['cComId_IPA'.($i+1)]]['ctoidxxx']  = $_POST['cComId_IPA'.($i+1)];
		$mIP[$_POST['cComId_IPA'.($i+1)]]['seridxxx']  = $_POST['cSerId_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comobsxx']  = $vDatosIp[0];
		$mIP[$_POST['cComId_IPA'.($i+1)]]['comvlrxx'] += $_POST['nComVlr_IPA'.($i+1)];
		$mIP[$_POST['cComId_IPA'.($i+1)]]['compivax']  = $_POST['nComPIva_IPA'.($i+1)]; // Porcentaje IVA
		$mIP[$_POST['cComId_IPA'.($i+1)]]['comvlr01'] += $_POST['nComVIva_IPA'.($i+1)]; // Valor Iva
		//Cantidad FE
		$mIP[$_POST['cComId_IPA'.($i+1)]]['unidadfe'] = $vDatosIp[2];
		$mIP[$_POST['cComId_IPA'.($i+1)]]['canfexxx'] += $vDatosIp[1];

		//Cantidad por condicion especial
		for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
			$mIP[$_POST['cComId_IPA'.($i+1)]]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] += $vDatosIp[3][$nP]['valpdfxx'];
		}
  }//for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {

  $mIngPro = array();
  foreach ($mIP as $cKey => $mValores) {
    $mIngPro[count($mIngPro)] = $mValores;
  }

  ## Busco Codigos de homologacion ##
  //Conceptos de causaciones automaticas
  $qPCC121  = "SELECT ctoidxxx, ctochald ";
  $qPCC121 .= "FROM $cAlfa.fpar0121";
  $xPCC121 = f_MySql("SELECT","",$qPCC121,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qPCC121."~".mysql_num_rows($xPCC121));
  $mCtoDes = array();
  while($xRCP121 = mysql_fetch_array($xPCC121)) {
    $mCtoDes["{$xRCP121['ctoidxxx']}"] = $xRCP121['ctochald'];
  }

  //Buscando conceptos
  $qCtoPCC  = "SELECT ctoidxxx, ctochald ";
  $qCtoPCC .= "FROM $cAlfa.fpar0119 ";
  $qCtoPCC .= "WHERE ctopccxx = \"SI\"";
  $xCtoPCC  = f_MySql("SELECT","",$qCtoPCC,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qCtoPCC."~".mysql_num_rows($xCtoPCC));
  while($xRCAP = mysql_fetch_array($xCtoPCC)) {
    $mCtoDes["{$xRCAP['ctoidxxx']}"] = $xRCAP['ctochald'];
  }
  ## Busco Codigos de homologacion ##

  ## Conceptos de cobro
  $qCtoCob  = "SELECT ctoidxxx, serclapr, cceidxxx, umeidxxx  ";
  $qCtoCob .= "FROM $cAlfa.fpar0129 ";
  $xCtoCob  = mysql_query($qCtoCob, $xConexion01);
  $vCtoSer = array();
  while ($xRS = mysql_fetch_assoc($xCtoCob)) {
    $vCtoSer["{$xRS['ctoidxxx']}"] = $xRS;
  }
  ## Fin Conceptos de cobro

  // echo '<pre>';
  // print_r($_POST);
  // die();
  ##Codigo para imprimir los ingresos para terceros ##
	$mIngTer = array();
  for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
    $vDesc = explode("^",$_POST['cComObs_PCCA' .($i+1)]);
    if ($_POST['cTipo_PCCA'.($i+1)] == "IMPUESTO_FINANCIERO") { //si es GMF debe mostrarse en GMF
      $nInd_mIngTer = count($mIngTer);
      $mIngTer[$nInd_mIngTer]['ctochald'] = "5106";
      $mIngTer[$nInd_mIngTer]['cComObs']  = $_POST['cComObs_PCCA'.($i+1)];
      $mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nComVlr_PCCA'.($i+1)];
      $mIngTer[$nInd_mIngTer]['nVlrIva']  = 0;
      $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA'.($i+1)];
      $mIngTer[$nInd_mIngTer]['cCtoId']   = $_POST['cComId_PCCA1'];
    } elseif (trim($_POST['cComObs_PCCA' .($i+1)]) == "IMPUESTO A LAS VENTAS" && trim($vDesc[1]) == "DIAN") {
      $nInd_mIngTer = count($mIngTer);
      $mIngTer[$nInd_mIngTer]['ctochald'] = $mCtoDes["{$_POST['cComId_PCCA'.($i+1)]}"];
      $mIngTer[$nInd_mIngTer]['cComObs']  = $vDesc[0];
      $mIngTer[$nInd_mIngTer]['nBaseIva'] = 0;
      $mIngTer[$nInd_mIngTer]['nVlrIva']  = $_POST['nComVlr_PCCA'.($i+1)];
      $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA'.($i+1)];
      $mIngTer[$nInd_mIngTer]['cCtoId']   = $_POST['cComId_PCCA1'];
    } else {
      //Si tiene IVA
      //en el item se envia el valor de la base, costo 
      //y se crea un registro nuevo con el valor del IVA
      $nInd_mIngTer = count($mIngTer);
      $mIngTer[$nInd_mIngTer]['ctochald'] = $mCtoDes["{$_POST['cComId_PCCA'.($i+1)]}"];
      $mIngTer[$nInd_mIngTer]['cComObs']  = $vDesc[0];

      if (($_POST['nVlrIva_PCCA'.($i+1)]+0) > 0) {
        $mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nComVlr_PCCA'.($i+1)] - $_POST['nVlrIva_PCCA'.($i+1)];
        $mIngTer[$nInd_mIngTer]['nVlrIva']  = 0;
        $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA'.($i+1)] - $_POST['nVlrIva_PCCA'.($i+1)];

        //Creo una nueva linea para iva
        $nInd_mIngTer = count($mIngTer);
        $mIngTer[$nInd_mIngTer]['ctochald'] = str_replace(substr($mCtoDes["{$_POST['cComId_PCCA'.($i+1)]}"], 0, 1), "6", $mCtoDes["{$_POST['cComId_PCCA'.($i+1)]}"]);//Cod
        $mIngTer[$nInd_mIngTer]['cComObs']  = trim("IVA ".$vDesc[0]);
        $mIngTer[$nInd_mIngTer]['nBaseIva'] = 0;
        $mIngTer[$nInd_mIngTer]['nVlrIva']  = $_POST['nVlrIva_PCCA'.($i+1)];
        $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nVlrIva_PCCA'.($i+1)];
      } else {
        $mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nComVlr_PCCA'.($i+1)];
        $mIngTer[$nInd_mIngTer]['nVlrIva']  = 0;
        $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA'.($i+1)];
      }
    }
  }//for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
  ##Fin Codigo para imprimir los ingresos para terceros ##
  //////////////////////////////////////////////////////////////////////////

  // echo "<pre>";
  // print_r($mIngTer);
  // die();

  ##Traigo la Forma de Pago##
  $cFormaPag = "";
  if (isset($_POST['cComFpag'])) {
    if ($_POST['cComFpag'] == "1") {
      $cFormaPag = "CONTADO";

    } elseif($_POST['cComFpag'] == "2") {
      $cFormaPag = "CREDITO";
    }
  }
  class PDF extends FPDF {
		function Header() {
			global $cAlfa;   global $cRoot;   global $cPlesk_Skin_Directory;
			global $gCcoId;  global $gSccId;  global $gMesDes; global $gAnoDes; global $gMesHas; global $gAnoHas;
			global $cUsrNom; global $cCcoDes; global $cScoDes; global $nPag;    global $vAgeDat; global $vCocDat;
			global $vResDat; global $cDocTra; global $cBultos; global $cPesBru; global $nValAdu;  global $vCccDat;
			global $cDocId;  global $cDocSuf; global $vCiuDat; global $vDceDat; global $cOpera;  global $_COOKIE;
			global $cCscFac; global $vConDat; global $cPedido; global $vSysStr; global $vPaiDat; global $cSucId;
      global $vIdContacto; global $cEstiloLetra; global $cEstiloLetraOfton; global $vCliDat; global $cSucDes; 
      global $cLugIngDes; global $cDesMer; global $cLimStk; global $cNumVap; global $cFormaPag;

      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,190,190);

      /*** Impresion Datos Generales Factura ***/
      $nPosX = 5;
      $nPosY = 6;

      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',$nPosX,$nPosY+2,55);
      //
      $this->SetFont($cEstiloLetra,'B',14);
      $this->setXY($nPosX+52,$nPosY);
      $this->Cell(60,4,utf8_decode("AGENCIA DE ADUANAS"),0,0,'');
      $this->setXY($nPosX+50,$nPosY+5);
      $this->Cell(62,4,utf8_decode("ALADUANA S.A.S  NIVEL 1"),0,0,'');
      $this->SetFont($cEstiloLetra,'B',6);
      $this->setXY($nPosX+47,$nPosY+10);
      $this->Cell(70,3,"Nit: ".number_format($vSysStr['financiero_nit_agencia_aduanas'],0,'','.')."-".f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']),0,0,'C');
      $this->setXY($nPosX+47,$nPosY+13);
      $this->Cell(70,3,utf8_decode("http://wwww.aladuana.com"),0,0,'C');
      $this->setXY($nPosX+47,$nPosY+16);
      $this->Cell(70,3,utf8_decode("facturacion@aladuana.com"),0,0,'C');

      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/aladuanaiso2.jpeg',$nPosX+120,$nPosY+3,19,20);

      $this->SetFont($cEstiloLetra,'B',5);
      $this->setXY($nPosX+47,$nPosY+21.5);
      $cObservaciones  = "OBSERVACIONES: Esta Factura de venta se asimila en sus efectos legales a una letra de cambio ARTICULO 774 del Código ";
      $cObservaciones .= "de Comercio. La Cancelación de esta factura después de la fecha de vencimiento, causará intereses de mora a la tasa ";
      $cObservaciones .= "autorizada por la Superintendencia Bancaria";
      $this->Multicell(70,2,utf8_decode($cObservaciones),0,'J');

      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/aladuanabasc2_factura.jpg',$nPosX+141,$nPosY,24,25);

      /** Factura de venta **/
      $this->setXY($nPosX+166,$nPosY+3);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(38,7,utf8_decode("FACTURA DE VENTA"),0,0,'C', true);
      $this->Rect($nPosX+166, $nPosY+3, 38, 17);
      $this->setXY($nPosX+167,$nPosX+13);
      $this->SetFont($cEstiloLetra,'',13);
      $this->Cell(37,4,utf8_decode("No. XXXXX"),0,0,'C');
      $this->setXY($nPosX+168,$nPosX+22);
      $this->SetFont($cEstiloLetra,'B',5);
      $this->MultiCell(28,2,utf8_decode("IVA E ICA REGIMEN COMUN NO SOMOS GRANDES CONTRIBUYENTES\nSOMOS RETENEDORES DE IVA"),0,'C');

      $nPosX = 6;
      $nPosY += 17;

      /*** Ciudad y Fecha***/
      $this->SetFont($cEstiloLetra,'B',8);
      $this->setXY($nPosX,$nPosY+27);
      $this->Cell(26,5,utf8_decode("CIUDAD Y FECHA:"),0,0,'');
      $this->SetFont($cEstiloLetraOfton,'',8);
      /*** Busco Saldo para colocar fecha de vencimiento ***/
      if ($_POST['nIPASal'] > 0) {
        $cSaldo = "CARGO";
      } else {
        $cSaldo = "FAVOR";
      }
      if($cSaldo == "FAVOR"){
        $cFecVen = date('Y-m-d');
      }else{
        $cFecVen = date('Y-m-d', strtotime("+{$_POST['cTerPla']} day"));
      }

      $cSucursal = $cSucDes;
      if($cSucId == "RCH" || $cSucId == "SMR" || $cSucDes == "TOLU"){
        $cSucursal = "CARTAGENA";
      }elseif($cSucId == "IPI"){
        $cSucursal = "BOGOTA";
      }

      $this->Cell(33,5,$cSucursal.", ".$_POST['dRegFCre'],0,0,'');

      $this->setXY($nPosX+100,$nPosY+27);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(27,5,"VENCIMIENTO: ",0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(27,5,$cFecVen,0,0,'L');

      /*** Fin Busco Saldo para colocar fecha de vencimiento ***/

      /*** Pedido ***/
      $this->setXY($nPosX+160,$nPosY+27);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(12,5,utf8_decode("PEDIDO:"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(40,5,$cPedido,0,0,'L');

      /*** DO ***/
      $this->setXY($nPosX,$nPosY+32);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(19,5,utf8_decode("DO:"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(50,5,$cDocId."-".str_pad($cDocSuf,3,"0",STR_PAD_LEFT),0,0,'L');

      /*** FORMA PAGO ***/
      $this->setXY($nPosX+100,$nPosY+32);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(27,5,utf8_decode("FORMA DE PAGO"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(40,5,$cFormaPag,0,0,'L');

      /*** Telefono ***/
      $this->setXY($nPosX+160,$nPosY+32);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(22,5,utf8_decode("TEL:"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(50,5,$vCliDat['CLITELXX'],0,0,'L');

      /*** SEÑOR(ES) ***/
      $this->setXY($nPosX,$nPosY+37);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(19,5,utf8_decode("SEÑOR:(ES)"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',7);
      $this->Cell(90,5,utf8_decode(substr($vCliDat['CLINOMXX'], 0, 73)),0,0,'L');

      /*** MEDIO DE PAGO ***/
      $this->setXY($nPosX+100,$nPosY+37);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(27,5,utf8_decode("MEDIO DE PAGO"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(40,5,substr(utf8_decode($_POST['cMePagDes']), 0, 25),0,0,'L');

      /*** V.R. CIF ***/
      $this->setXY($nPosX+160,$nPosY+37);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(22,5,utf8_decode("V.R. CIF:"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(30,5,$nValAdu,0,0,'L');

      /*** Direccion ***/
      $this->setXY($nPosX,$nPosY+42);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(19,5,utf8_decode("DIRECCION:"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',7);
      $this->Cell(90,5,utf8_decode(substr($vCliDat['CLIDIR3X'], 0, 73)),0,0,'L');

      /*** Mercancia ***/
      $this->setXY($nPosX+100,$nPosY+42);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(27,5,utf8_decode("MERCANCIA:"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(56,5,$cDesMer,0,0,'L');

      /*** VAPOR ***/
      $this->setXY($nPosX+160,$nPosY+42);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(22,5,utf8_decode("VAPOR:"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(50,5,$cNumVap,0,0,'L');

      /*** Nit ***/
      $this->setXY($nPosX,$nPosY+47);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(19,5,utf8_decode("NIT:"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(33,5,$_POST['cTerIdInt']."-".f_Digito_Verificacion($_POST['cTerIdInt']),0,0,'L');

      /*** DECLARACION ***/
      $this->setXY($nPosX+100,$nPosY+47);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(27,5,utf8_decode("DECLARACION:"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(23,5,$cLimStk,0,0,'L');
 
      /*** Peso ***/
      $this->setXY($nPosX+160,$nPosY+47);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(22,5,utf8_decode("PESO:"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(25,5,number_format($cPesBru,2,'.',','),0,0,'L');

      /*** Guia/Bl ***/
      $this->setXY($nPosX,$nPosY+52);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(19,5,utf8_decode("GUIA/BL:"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(56,5,$cDocTra,0,0,'L');

      /*** PUERTO/TRANS ***/
      $this->setXY($nPosX+100,$nPosY+52);
      $this->SetFont($cEstiloLetra,'B',8);
      $this->Cell(27,5,utf8_decode("PUERTO/TRANS:"),0,0,'L');
      $this->SetFont($cEstiloLetraOfton,'',8);
      $this->Cell(48,5,$cLugIngDes,0,0,'L');

      $this->setXY($nPosX+10,$nPosY+65);
      $this->Cell(100,5,"CONCEPTO",0,0,'');
      $this->Cell(30,5,"CANTIDAD",0,0,'C');
      $this->Cell(30,5,"BASE/VALOR",0,0,'C');
      $this->Cell(30,5,"TOTAL",0,0,'C');

		}//Function Header

		function Footer() {
      global $cRoot; global $cPlesk_Skin_Directory; global $cNomCopia; global $nCopia;
      global $nContPage; global $vCocDat; global $cSaldo; global $vResDat;
      global $cEstiloLetra; global $gCorreo; global $nb; global $vUsrNom;

      $nPosY = 215;
      $nPosX = 6;

      $this->setXY($nPosX,$nPosY);
      $this->SetFont($cEstiloLetra,'B',6);
      $this->Cell(9,3,utf8_decode("Bogota: "),0,0,'');
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(75,3,utf8_decode("Cra. 103 No. 25B-86 P3. PBX: (1) 4151556 * Fax: Ext. 102 * E-mail: bogota@aladuana.com"),0,0,'');

      $this->setXY($nPosX,$nPosY+3.5);
      $this->SetFont($cEstiloLetra,'B',6);
      $this->Cell(16,3,utf8_decode("Buenaventura: "),0,0,'');
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(80,3,utf8_decode("Calle 7 No. 3-11, Ed. Pacific Trade Center Oficina 1802 * Tel.: (2) 241 3884 - 241 3885 - 241 2425"),0,0,'');

      $this->setXY($nPosX,$nPosY+6.5);
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(30,3,utf8_decode("Email: buenaventura@aladuana.com"),0,0,'');

      $this->setXY($nPosX,$nPosY+9.5);
      $this->SetFont($cEstiloLetra,'B',6);
      $this->Cell(12,3,utf8_decode("Cartagena: "),0,0,'');
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(63,3,utf8_decode("Manga Avenida Miramar No. 23-87 * Tel.: (5) 660 9397 - 660 9448"),0,0,'');

      $this->setXY($nPosX,$nPosY+12.5);
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(44,3,utf8_decode("Fax: (5) 660 9448 * E-mail: cartagena@aladuana.com"),0,0,'');

      $this->setXY($nPosX,$nPosY+15.5);
      $this->SetFont($cEstiloLetra,'B',6);
      $this->Cell(14,3,utf8_decode("Barranquilla: "),0,0,'');
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(95,3,utf8_decode("Cll. 70 No. 52 - 29 Ofc. 102 Edif. Centro Comercial Miracentro * Tel.: (5) 332 3390 * E-mail: barranquilla@aladuana.com"),0,0,'');

      $this->setXY($nPosX,$nPosY+18.5);
      $this->SetFont($cEstiloLetra,'B',6);
      $this->Cell(10,3,utf8_decode("Medellín: "),0,0,'');
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(82,3,utf8_decode("Cra. 43b No. 14-51 Of. 705 Ed. Alcala * Tel.: (4) 311 8074 - 311 8357 * Email: medellin@aladuana.com"),0,0,'');

      $this->setXY($nPosX,$nPosY+21.5);
      $this->SetFont($cEstiloLetra,'B',6);
      $this->Cell(9,3,utf8_decode("Cucuta: "),0,0,'');
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(104,3,utf8_decode("Avenida Camilo Daza No. 21 - 99 Of. 303 Edificio Emycar * Tel.: (7) 587 6156 * Cel.: 317 512 0318* E-mail: cucuta@aladuana.com"),0,0,'');

      $this->setXY($nPosX,$nPosY+24.5);
      $this->SetFont($cEstiloLetra,'B',6);
      $this->Cell(5,3,utf8_decode("Cali: "),0,0,'');
      $this->SetFont($cEstiloLetra,'',6);
      $this->Cell(105,3,utf8_decode("Av. 3FN No. 59 -120 Casa 24 Unidad Recodo de la Flora * Cel.: 315 512 3442 * Email: cali@aladuana.com"),0,0,'');

      $this->Line($nPosX+136,$nPosY+5,$nPosX+136,$nPosY+27);
      $this->setXY($nPosX+141,$nPosY+8);
      $this->SetFont($cEstiloLetra,'B',5);
      $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA BOGOTA, D.C. 5229; ICA TARIFA 9.66X1000"),0,0,'');
      $this->setXY($nPosX+141,$nPosY+10);
      $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA BUENAVENTURA: 5229; ICA TARIFA 10X1000"),0,0,'');
      $this->setXY($nPosX+141,$nPosY+12);
      $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA CARTAGENA: 5229; ICA TARIFA 8X1000"),0,0,'');
      $this->setXY($nPosX+141,$nPosY+14);
      $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA BARRANQUILLA: 5229; ICA TARIFA 10X1000"),0,0,'');
      $this->setXY($nPosX+141,$nPosY+16);
      $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA MEDELLIN: 5229: ICA TARIFA 6X1000"),0,0,'');
      $this->setXY($nPosX+141,$nPosY+18);
      $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA CUCUTA: 5229; ICA TARIFA 7x1000"),0,0,'');
      $this->setXY($nPosX+141,$nPosY+20);
      $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA CALI: 5229; ICA TARIFA 10x1000"),0,0,'');
      $this->setXY($nPosX+141,$nPosY+22);
      $this->Cell(63,2,utf8_decode("CF0301 V02"),0,0,'');

      $this->setXY($nPosX,$nPosY+28);
      $this->SetTextColor(30, 30, 70);
      $this->SetFillColor(255,255,255);
      $this->SetFont($cEstiloLetra,'B',6);
      $this->MultiCell(200,3.5,utf8_decode("Nota: Apreciado Cliente: favor consignar en las siguientes cuentas corrientes Bancolombia No. 237955408-95, Cód.recaudo 06993, ref:8300109054 o Banco Itaú No. 011362845 a nombre de AGENCIA DE ADUANAS ALADUANA S.A.S"),0,'',true);
      $this->SetTextColor(0,0,0);

      $this->setXY($nPosX,$nPosY+33);
      $this->SetFont($cEstiloLetra,'B',6);
      $this->Cell(206,5,utf8_decode($cNomCopia),0,0,'C');

      $this->SetFont($cEstiloLetra,'',6);
      $dFechaInicial = date_create($vResDat['resfdexx']);
      $dFechaFinal   = date_create($vResDat['resfhaxx']);
      $nDiferencia   = date_diff($dFechaInicial, $dFechaFinal);
      $nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m + (($nDiferencia->d > 0) ? 1 : 0);
      $this->RotatedText($nPosX-1.5,215,utf8_decode("AUTORIZACIÓN DIAN No. {$vResDat['residxxx']} DEL ".$vResDat['resfdexx']." DEL ".$vResDat['resprexx'].$vResDat['resdesxx']." AL ".$vResDat['resprexx'].$vResDat['reshasxx']." AUTORIZACIÓN VIGENCIA {$nMesesVigencia} MESES"),90);
      $this->RotatedText($nPosX+207.5,260,utf8_decode("Impreso por openTecnologia S.A Nit 830.135.010-5."),90);

      $nPosY = 247;
			$this->setXY($nPosX,$nPosY+3);
      $this->SetFont($cEstiloLetra,'',7);
      $this->Cell(40,4, utf8_decode("FECHA Y HORA DE VALIDACIÓN:"),0,0,'L');
      $this->Ln(5);
      $this->setX($nPosX);
      $this->SetFont($cEstiloLetra,'B',7);
      $this->Cell(40,4, utf8_decode("REPRESENTACIÓN IMPRESA DE LAFACTURA ELECTRÓNICA"),0,0,'L');
      $this->Ln(4);
      $this->setX($nPosX);
      $this->Cell(40,4, utf8_decode("Firma Electrónica:"),0,0,'L');

      $this->setXY($nPosX+140,$nPosY);
      $this->SetFont($cEstiloLetra,'B',6);
      $this->Cell(40,4, utf8_decode("CUFE:"),0,0,'L');

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

		function Setwidths($w) {
			//Set the array of column widths
			$this->widths=$w;
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
			$h=4*$nb;
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
				$this->MultiCell($w,4,$data[$i],0,$a);
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
  $pdf->AddFont($cEstiloLetra, '', 'arial.php');
  $pdf->AddFont($cEstiloLetraOfton, '', 'otfon1.php');
  $pdf->AliasNbPages();
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,22);
  $pdf->SetFillColor(229,229,229);
  $pdf->AddPage();
  
  $pdf->SetWidths(array(100,28,28,28));
  $pdf->SetAligns(array("L","R","R","R"));

  $nPosY = $pdf->GetY()+6;
  $nPosX = 16;
  $nPosFin = 195;
  $nb = 1;
  $pyy = $nPosY;

  $nTotBasePcc = 0;
  $nTotalPcc   = 0;

  // $mIngTer = array_merge($mIngTer,$mIngTer);
  // $mIngTer = array_merge($mIngTer,$mIngTer);
  // $mIngTer = array_merge($mIngTer,$mIngTer);

  /*** Imprimo Pagos a Terceros ***/
  if(count($mIngTer) > 0 ){//Si la matriz de Pcc tiene registros

    $pdf->setXY($nPosX,$pyy);
    $pdf->SetFont($cEstiloLetraOfton,'',8);
    
    for($i=0;$i<count($mIngTer);$i++){
      $pyy = $pdf->GetY();

      if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
        $pdf->AddPage();
        $nb++;
        $nPosY = $pdf->GetY()+10;
        $nPosX = 16;
        $pyy = $nPosY;
        $pdf->SetFont($cEstiloLetraOfton,'',8);
        $pdf->setXY($nPosX,$pyy);
      }

      $nCantidad     = '1';
      $intVlrBase    = ($mIngTer[$i]['nBaseIva'] > 0) ? $mIngTer[$i]['nBaseIva'] : $mIngTer[$i]['nComVlr'];
      $nTotBasePcc  += $mIngTer[$i]['nBaseIva'];
      $nTotalPcc    += $mIngTer[$i]['nComVlr'];


      if ($vCccDat['cccvlcto'] != "") {
        $mVlrCto = f_Explode_Array($vCccDat['cccvlcto'], "|","~");
        for ($nVC=0; $nVC<count($mVlrCto); $nVC++) {
          // Obtiene la cantidad y valor unitario del concepto parametrizado en la condiciones comerciales
          if ($mIngTer[$i]['cCtoId'] == $mVlrCto[$nVC][0]) {
            $nCantidad  = $mIngTer[$i]['comvlrxx'] / $mVlrCto[$nVC][2];
            $intVlrBase = $mVlrCto[$nVC][2];
          }
        }
      }

      $pdf->setX($nPosX);
      $pdf->Row(array(
        $mIngTer[$i]['ctochald']." ".utf8_decode($mIngTer[$i]['cComObs']),
        $nCantidad,
        number_format($intVlrBase,2,'.',','),
        number_format($mIngTer[$i]['nComVlr'],2,'.',',')
      ));
    }//for($i=0;$i<count($mIngTer);$i++){
    $pyy += 6;

  }//if(count($mIngTer) > 0){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
  /*** Fin Imprimo Pagos a Terceros ***/

   /***  Total pagos a Terceros ***/
   if(count($mIngTer) > 0){

    $pyy = ($pdf->GetY() > $pyy) ? $pdf->GetY(): $pyy;
    $pdf->setXY($nPosX,$pyy);

    if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
      $pdf->AddPage();
      $nb++;
      $nPosY = $pdf->GetY()+10;
      $nPosX = 16;
      $pyy = $nPosY;
      $pdf->SetFont($cEstiloLetraOfton,'',8);
      $pdf->setXY($nPosX,$pyy);
    }

    $pdf->SetWidths(array(100,28,28,28));
    $pdf->SetAligns(array("L","C","R","R"));
    $pdf->SetFont($cEstiloLetraOfton,'',8);

    $pdf->setX($nPosX);
    $pdf->Row(array("TOTAL PAGOS A TERCEROS",
                    "",
                    number_format($nTotBasePcc,2,'.',','),
                    number_format($nTotalPcc,2,'.',',')));

    $pyy += 6;
  }
  /*** Fin Total pagos a Terceros ***/

  /*** Imprimo Ingresos Propios ***/
  $nSubToIP = 0;
	$nSubToIPIva = 0;
  $nSubtotalIPGra   = 0; // Total Ingresos Gravados (Subtotal pagos propios)
  $nSubTotalIPNoGra = 0; // Total Ingresos No Gravados (Subtotal pagos propios)
  $nTotalIPGra      = 0; // Total Ingresos Gravados
  $nTotalIPNoGra    = 0; // Total Ingresos No Gravados

  if($_POST['nSecuencia_IPA'] > 0 || count($mIngPro) > 0) { //Valido si la Secuencia de la Grilla de Ip viene mayor a cero para imprimir bloque de INGRESOS PROPIOS

    $pdf->setXY($nPosX,$pyy);
    $pdf->SetFont($cEstiloLetraOfton,'',8);
    $pdf->Cell(67,6,utf8_decode("INGRESOS PROPIOS"),0,0,'L');

    $pyy += 6;
    $pdf->setXY($nPosX+5,$pyy);
    $pdf->SetWidths(array(95,28,28,28));
    $pdf->SetAligns(array("L","R","R","R"));
    $pdf->SetFont($cEstiloLetraOfton,'',8);

		/***  OJO: hago dos veces el mismo for para separar los ingresos GRAVADOS y luego los NO GRAVADOS ***/
		for($k=0;$k<(count($mIngPro));$k++) {
      $pyy = $pdf->GetY();

      if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
        $pdf->AddPage();
        $nb++;
        $nPosY = $pdf->GetY()+10;
        $nPosX = 16;
        $pyy = $nPosY;
        $pdf->SetFont($cEstiloLetraOfton,'',8);
        $pdf->setXY($nPosX,$pyy);
      }

			if( $mIngPro[$k]['comvlr01'] != 0 ) {

        $cValor = "";
        foreach ($mIngPro[$k]['itemcanx'] as $cKey => $cValue) {
          if ($cKey == "CONTENEDORES_DE_20") {
            $cValor .= " CONTENEDORES DE 20: (".number_format($cValue,0,'.',',').')';
          } elseif ($cKey == "CONTENEDORES_DE_40") {
            $cValor .= " CONTENEDORES DE 40: (".number_format($cValue,0,'.',',').')';
          } elseif ($cKey == "UNIDADES_DE_CARGA_SUELTA") {
            $cValor .= " UNIDADES DE CARGA SUELTA: (".number_format($cValue,0,'.',',').')';
          }
        }

        $nValorUnitario  = ($mIngPro[$k]['unidadfe'] != "A9" && $mIngPro[$k]['canfexxx'] > 0) ? ($mIngPro[$k]['comvlrxx']/$mIngPro[$k]['canfexxx']) : $mIngPro[$k]['comvlrxx'];
        $cCtoCodigo      = ($vCtoSer["{$mIngPro[$k]['ctoidxxx']}"]['serclapr'] == "001") ? $vCtoSer["{$mIngPro[$k]['ctoidxxx']}"]['cceidxxx'] : ltrim($mIngPro[$k]['ctoidxxx'], "0");
        $nSubtotalIPGra += $nValorUnitario;
        $nTotalIPGra    += $mIngPro[$k]['comvlrxx'];

        $pdf->setX($nPosX+5);
        $pdf->Row(array(
          trim($cCtoCodigo." ".$mIngPro[$k]['comobsxx']).$cValor,
          number_format($mIngPro[$k]['canfexxx'],0,'.',','),
          number_format($nValorUnitario,2,'.',','),
          number_format(($mIngPro[$k]['comvlrxx']),2,'.',',')
        ));
			}
		}

		for($k=0;$k<(count($mIngPro));$k++) {
      $pyy = $pdf->GetY();
      if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
        $pdf->AddPage();
        $nb++;
        $nPosY = $pdf->GetY()+10;
        $nPosX = 16;
        $pyy = $nPosY;
        $pdf->SetFont($cEstiloLetraOfton,'',8);
        $pdf->setXY($nPosX,$nPosY);
      }

			if( $mIngPro[$k]['comvlr01'] == 0 ) {

        $cValor = "";
        foreach ($mIngPro[$k]['itemcanx'] as $cKey => $cValue) {
          if ($cKey == "CONTENEDORES_DE_20") {
            $cValor .= " CONTENEDORES DE 20: (".number_format($cValue,0,'.',',').')';
          } elseif ($cKey == "CONTENEDORES_DE_40") {
            $cValor .= " CONTENEDORES DE 40: (".number_format($cValue,0,'.',',').')';
          } elseif ($cKey == "UNIDADES_DE_CARGA_SUELTA") {
            $cValor .= " UNIDADES DE CARGA SUELTA: (".number_format($cValue,0,'.',',').')';
          }
        }

        $nValorUnitario    = ($mIngPro[$k]['unidadfe'] != "A9" && $mIngPro[$k]['canfexxx'] > 0) ? ($mIngPro[$k]['comvlrxx']/$mIngPro[$k]['canfexxx']) : $mIngPro[$k]['comvlrxx'];
        $cCtoCodigo        = ($vCtoSer["{$mIngPro[$k]['ctoidxxx']}"]['serclapr'] == "001") ? $vCtoSer["{$mIngPro[$k]['ctoidxxx']}"]['cceidxxx'] : ltrim($mIngPro[$k]['ctoidxxx'], "0");
        $nSubTotalIPNoGra += $nValorUnitario;
        $nTotalIPNoGra    += $mIngPro[$k]['comvlrxx'];

        $pdf->setX($nPosX+5);
        $pdf->Row(array(
          trim($cCtoCodigo." ".$mIngPro[$k]['comobsxx']).$cValor,
          number_format($mIngPro[$k]['canfexxx'],0,'.',','),
          number_format($nValorUnitario,2,'.',','),
          number_format(($mIngPro[$k]['comvlrxx']),2,'.',',')
        ));
			}
		}

  }//if($nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
  /*** Fin Imprimo Ingresos Propios ***/
  /*** Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios ***/

  /*** Impresion GMF ***/
  // $nSubTotGmf = 0;
  // if ( count($mDatGmf) > 0 ){
  //
  //   $pyy = ($pdf->GetY() > $pyy) ? $pdf->GetY(): $pyy;
  //
  //   if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
  //     $pdf->AddPage();
  //     $nb++;
  //     $nPosY = $pdf->GetY()+6;
  //     $nPosX = 115;
  //     $pyy = $nPosY;
  //     $pdf->SetFont($cEstiloLetra,'',8);
  //     $pdf->setXY($nPosX,$pyy);
  //   }
  //
  //   $pdf->setXY($nPosX,$pyy);
  //   $pdf->SetFont($cEstiloLetra,'',8);
  //   $pdf->Cell(67,6,utf8_decode("RECUPERACIÓN GASTOS BANCARIOS (GMF)"),0,0,'L');
  //
  //   for ($i=0;$i<count($mDatGmf);$i++) {
  //     $nSubTotGmf += $mDatGmf[$i]['comvlrxx'];
  //   }//for ($i=0;$i<count($mDatGmf);$i++) {
  //
  //   /*** Imprimo Total GMF ***/
  //   $pdf->Cell(27,6,number_format($nSubTotGmf,0,',','.'),0,0,'R');
  //   $pyy += 6;
  // }
  /*** Fin Impresion GMF ***/

  /*** Total Ingresos Propios ***/
  if($_POST['nSecuencia_IPA'] > 0 || count($mIngPro) > 0) {

    $pyy = ($pdf->GetY() > $pyy) ? $pdf->GetY(): $pyy;
    if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
      $pdf->AddPage();
      $nb++;
      $nPosY = $pdf->GetY()+10;
      $nPosX = 16;
      $pyy = $nPosY;
      $pdf->SetFont($cEstiloLetraOfton,'',8);
      $pdf->setXY($nPosX,$pyy);
    }

    $pdf->setXY($nPosX,$pyy);
    $pdf->SetWidths(array(100,28,28,28));
    $pdf->SetAligns(array("L","R","R","R"));
    $pdf->SetFont($cEstiloLetraOfton,'',8);

    $pdf->setX($nPosX);
    $pdf->Row(array("TOTAL INGRESOS PROPIOS",
                    "",
                    number_format(($nSubtotalIPGra+$nSubTotalIPNoGra),2,'.',','),
                    number_format(($nTotalIPGra+$nTotalIPNoGra),2,'.',',')));

    $pyy += 6;
  }
  /*** Fin Total Ingresos Propios ***/

  //Subtotal Factura
  $nSubTotal = $nTotalIPGra + $nTotalIPNoGra + $nTotalPcc;

  // echo "<pre>";
  // print_r($_POST);
  // die();

  /*** Calculo e impresión de Iva y Retenciones. ***/
 	##Busco Valor de IVA ##
	$nIva      = $_POST['nIPAIva'];
 	##Busco Valor de RET.IVA ##
	$nTotIva   = $_POST['nIPARIva'];
  ##Busco Valor de RET.ICA ##
  $nTotIca   = $_POST['nIPARIca'];
  ##Busco Valor de AUTO RET.ICA ##
  $nTotAIca  = $_POST['nIPAARIca'];
  ##Busco Valor de RET.FTE ##
  $nTotRfte  = $_POST['nIPARFte'];
  ##Busco Valor de AUTO RET.FTE ##
  $nTotARfte = $_POST['nIPAARFte'];
  ##Busco Valor de RET.CREE ##
  $nTotCree  = $_POST['nIPARCre'];
  ##Busco Valor de AUTO RET.CREE ##
  $nTotACree = $_POST['nIPAARCre'];

  ##Bloque que acumula retenciones por valor de porcentaje##
  $mRetFte = array();
  $mRetIva = array();
  // echo "<pre>";
  // print_r($_POST);die();
  for ($i=0;$i<$_POST['nSecuencia_IPA'];$i++) {
    // f_Mensaje(__FILE__,__LINE__,$_POST['nPorFte_IPA'.($i+1)]."~".$_POST['nPorIva_IPA'.($i+1)]);
    if ($_POST['nPorFte_IPA'.($i+1)] > 0) {
      $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx,";
      $qPucDat .= "pucdesxx ";
      $qPucDat .= "FROM $cAlfa.fpar0115 ";
      $qPucDat .= "WHERE ";
      $qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$_POST['cCtaFte_IPA'.($i+1)]}\" LIMIT 0,1 ";
      $xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qPucDat."~".mysql_num_rows($xPucDat));
      $nFilPuc  = mysql_num_rows($xPucDat);
      if($nFilPuc > 0){
        $xRPD = mysql_fetch_array($xPucDat);
        $nSwitch_Encontre_Porcentaje = 0;
        for ($j=0;$j<count($mRetFte);$j++) {
          if ($_POST['nPorFte_IPA'.($i+1)] == $mRetFte[$j]['pucretxx']) {
            $nSwitch_Encontre_Porcentaje = 1;
            $mRetFte[$j]['comvlrxx']  += $_POST['nVlrFte_IPA'.($i+1)];
            $mRetFte[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
          }
        }
        if ($nSwitch_Encontre_Porcentaje == 0) { // No lo encontro en la matriz para pintar en la factura
          $nInd_mRetFte = count($mRetFte);
          $mRetFte[$nInd_mRetFte]['tipretxx'] = "FUENTE";
          $mRetFte[$nInd_mRetFte]['pucretxx'] = $_POST['nPorFte_IPA'.($i+1)];
          $mRetFte[$nInd_mRetFte]['comvlrxx'] = $_POST['nVlrFte_IPA'.($i+1)];
          $mRetFte[$nInd_mRetFte]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
          $mRetFte[$nInd_mRetFte]['pucdesxx'] = $xRPD['pucdesxx'];
        }
      }
    }

    if ($_POST['nPorIva_IPA'.($i+1)] > 0) {
      $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx,";
      $qPucDat .= "pucdesxx ";
      $qPucDat .= "FROM $cAlfa.fpar0115 ";
      $qPucDat .= "WHERE ";
      $qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$_POST['cCtaIva_IPA'.($i+1)]}\" LIMIT 0,1 ";
      $xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qPucDat."~".mysql_num_rows($xPucDat));
      $nFilPuc  = mysql_num_rows($xPucDat);
      if($nFilPuc > 0){
        $xRPD = mysql_fetch_array($xPucDat);
        $nSwitch_Encontre_Porcentaje = 0;
        for ($j=0;$j<count($mRetIva);$j++) {
          if ($_POST['nPorIva_IPA'.($i+1)] == $mRetIva[$j]['pucretxx']) {
            $nSwitch_Encontre_Porcentaje = 1;
            $mRetIva[$j]['comvlrxx']  += $_POST['nVlrIva_IPA'.($i+1)];
            $mRetIva[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
          }
        }
        if ($nSwitch_Encontre_Porcentaje == 0) { // No lo encontro en la matriz para pintar en la factura
          $nInd_mRetIva = count($mRetIva);
          $mRetIva[$nInd_mRetIva]['tipretxx'] = "IVA";
          $mRetIva[$nInd_mRetIva]['pucretxx'] = $_POST['nPorIva_IPA'.($i+1)];
          $mRetIva[$nInd_mRetIva]['comvlrxx'] = $_POST['nVlrIva_IPA'.($i+1)];
          $mRetIva[$nInd_mRetIva]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
          $mRetIva[$nInd_mRetIva]['pucdesxx'] = $xRPD['pucdesxx'];
        }
      }
    }

    if ($_POST['nPorIca_IPA'.($i+1)] > 0) {
      $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx,";
      $qPucDat .= "pucdesxx ";
      $qPucDat .= "FROM $cAlfa.fpar0115 ";
      $qPucDat .= "WHERE ";
      $qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$_POST['cCtaIca_IPA'.($i+1)]}\" LIMIT 0,1 ";
      $xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qPucDat."~".mysql_num_rows($xPucDat));
      $nFilPuc  = mysql_num_rows($xPucDat);
      if($nFilPuc > 0){
        $xRPD = mysql_fetch_array($xPucDat);
        $nSwitch_Encontre_Porcentaje = 0;
        for ($j=0;$j<count($mRetIca);$j++) {
          if ($_POST['nPorIca_IPA'.($i+1)] == $mRetIca[$j]['pucretxx']) {
            $nSwitch_Encontre_Porcentaje = 1;
            $mRetIca[$j]['comvlrxx']  += $_POST['nVlrIca_IPA'.($i+1)];
            $mRetIca[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
          }
        }
        if ($nSwitch_Encontre_Porcentaje == 0) { // No lo encontro en la matriz para pintar en la factura
          $nInd_mRetIca = count($mRetIca);
          $mRetIca[$nInd_mRetIca]['tipretxx'] = "ICA";
          $mRetIca[$nInd_mRetIca]['pucretxx'] = $_POST['nPorIca_IPA'.($i+1)];
          $mRetIca[$nInd_mRetIca]['comvlrxx'] = $_POST['nVlrIca_IPA'.($i+1)];
          $mRetIca[$nInd_mRetIca]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
          $mRetIca[$nInd_mRetIva]['pucdesxx'] = $xRPD['pucdesxx'];
        }
      }
    }
  }

  /*** Calculo Valores Totales ***/
  $nTotal        = ($nSubTotal + $nIva) - ($nTotRfte + $nTotIva + $nTotIca);

  $nTotalCargo = 0;
  $nTotalFavor = 0;
  /** Imprimo Saldo **/
  if ($_POST['nIPASal'] > 0) {
    $nTotalCargo = $_POST['nIPASal'];
  } else {
    $nTotalFavor = $_POST['nIPASal'];
  }
  /*** Fin Calculo Valores Totales ***/

  /*** Bloque de IVA ***/
  $pyy = ($pdf->GetY() > $pyy) ? $pdf->GetY(): $pyy;
  if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
    $pdf->AddPage();
    $nb++;
    $nPosY = $pdf->GetY()+10;
    $nPosX = 16;
    $pyy = $nPosY;
    $pdf->SetFont($cEstiloLetraOfton,'',8);
    $pdf->setXY($nPosX,$pyy);
  }

  $pdf->setXY($nPosX,$pyy);
  $pdf->Cell(156, 4, "IVA 19.00%", 0, 0, 'L');
  $pdf->Cell(28, 4, number_format($nIva, 2, '.', ',' ), 0, 0, 'R');
  $pyy += 5;
  /*** Fin Bloque de IVA ***/

  /** Imprimo Retenciones **/
  if(count($mRetIva) > 0 || count($mRetFte) > 0 || count($mRetIca) > 0){
    $pyy = ($pdf->GetY() > $pyy) ? $pdf->GetY(): $pyy;
    if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
      $pdf->AddPage();
      $nb++;
      $nPosY = $pdf->GetY()+10;
      $nPosX = 16;
      $pyy = $nPosY;
      $pdf->SetFont($cEstiloLetraOfton,'',8);
      $pdf->setXY($nPosX,$pyy);
    }

    $pdf->setXY($nPosX,$pyy);
    $pdf->SetFont($cEstiloLetraOfton,'',8);
    $pdf->Cell(67,6,utf8_decode("RETENCIONES"),0,0,'L');

    $pyy += 6;
    $pdf->setXY($nPosX+5,$pyy);
    $pdf->SetWidths(array(123,28,28));
    $pdf->SetAligns(array("L","R","R"));
    $pdf->SetFont($cEstiloLetraOfton,'',8);

    for($i=0;$i<count($mRetIva);$i++){
      $pyy = $pdf->GetY();
      if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
        $pdf->AddPage();
        $nb++;
        $nPosY = $pdf->GetY()+10;
        $nPosX = 16;
        $pyy = $nPosY;
        $pdf->SetFont($cEstiloLetraOfton,'',8);
        $pdf->setXY($nPosX,$nPosY);
      }

      $pdf->setX($nPosX+5);
      $pdf->Row(array("IVA RETENIDO ".number_format($mRetIva[$i]['pucretxx'],2,'.',',')."%",
                      "",
                      number_format($mRetIva[$i]['comvlrxx'],2,'.',',')));
    }

    for($i=0;$i<count($mRetFte);$i++){
      $pyy = $pdf->GetY();
      if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
        $pdf->AddPage();
        $nb++;
        $nPosY = $pdf->GetY()+10;
        $nPosX = 16;
        $pyy = $nPosY;
        $pdf->SetFont($cEstiloLetraOfton,'',8);
        $pdf->setXY($nPosX,$nPosY);
      }

      $pdf->setX($nPosX+5);
      $pdf->Row(array("RTE FTE HONORARIOS Y COMISIONES ".number_format($mRetFte[$i]['pucretxx'],2,'.',',')."%",
                      "",
                      number_format($mRetFte[$i]['comvlrxx'],2,'.',',')));
    }

    for($i=0;$i<count($mRetIca);$i++){
      $pyy = $pdf->GetY();
      if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
        $pdf->AddPage();
        $nb++;
        $nPosY = $pdf->GetY()+10;
        $nPosX = 16;
        $pyy = $nPosY;
        $pdf->SetFont($cEstiloLetraOfton,'',8);
        $pdf->setXY($nPosX,$nPosY);
      }

      $pdf->setX($nPosX+5);
      $pdf->Row(array("RTE ICA ".number_format($mRetIca[$i]['pucretxx'],2,'.',',')."%",
                      "",
                      number_format($mRetIca[$i]['comvlrxx'],2,'.',',')));
    }

    $pyy = ($pdf->GetY() > $pyy) ? $pdf->GetY(): $pyy;
    if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
      $pdf->AddPage();
      $nb++;
      $nPosY = $pdf->GetY()+10;
      $nPosX = 16;
      $pyy = $nPosY;
      $pdf->SetFont($cEstiloLetraOfton,'',8);
      $pdf->setXY($nPosX,$pyy);
    }

    $pdf->setXY($nPosX,$pyy);
    $pdf->SetWidths(array(100,28,28,28));
    $pdf->SetAligns(array("L","R","R","R"));
    $pdf->SetFont($cEstiloLetraOfton,'',8);

    $pdf->setX($nPosX);
    $pdf->Row(array("TOTAL RETENCIONES",
                    "",
                    "",
                    number_format(($nTotRfte+$nTotIva+$nTotIca),2,'.',',')));

    $pyy += 6;
  }
  /*** Fin Retenciones ***/
  
  /*** Bloque de anticipos ***/
  $pyy = ($pdf->GetY() > $pyy) ? $pdf->GetY(): $pyy;
  if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
    $pdf->AddPage();
    $nb++;
    $nPosY = $pdf->GetY()+10;
    $nPosX = 16;
    $pyy = $nPosY;
    $pdf->SetFont($cEstiloLetraOfton,'',8);
    $pdf->setXY($nPosX,$pyy);
  }

  $pdf->setXY($nPosX,$pyy);
  $pdf->SetWidths(array(100,28,28,28));
  $pdf->SetAligns(array("L","R","R","R"));
  $pdf->SetFont($cEstiloLetraOfton,'',8);

  $pdf->setX($nPosX);
  $pdf->Row(array("ANTICIPOS",
                  "",
                  "",
									number_format(abs($_POST['nIPAAnt']),2,'.',',')));

  $pyy += 6;
  /*** Fin Bloque de anticipos ***/

  /** Imprimo Observaciones de la factura **/
  $pyy = ($pdf->GetY() > $pyy) ? $pdf->GetY(): $pyy;
  if($pyy > 205){//Validacion para siguiente pagina si se excede espacio de impresion
    $pdf->AddPage();
    $nb++;
    $nPosY = $pdf->GetY()+10;
    $nPosX = 16;
    $pyy = $nPosY;
    $pdf->SetFont($cEstiloLetraOfton,'',8);
    $pdf->setXY($nPosX,$pyy);
  }
  if($pyy < 190){
    $pyy = 190;
  }
  $pdf->setXY($nPosX,$pyy);
  $pdf->SetWidths(array(184));
  $pdf->SetAligns(array("J"));
  $pdf->SetFont($cEstiloLetraOfton,'',8);

  $pdf->setX($nPosX);
  switch($vDceDat){
    case "IMPORTACION":
    case "TRANSITO":
      $pdf->Row(array($cObsCom));
    break;
    case "EXPORTACION":
      $pdf->Row(array($cObsCom));
    break;
    default:
      $pdf->Row(array($_POST['cComObs']));
    break;
  }

  $pyy += 6;

  $pdf->setXY($nPosX,$pyy);
  $pdf->SetFont($cEstiloLetraOfton,'',8);
  $pdf->Cell(78,6,"SALDO A CARGO",0,0,'L');
  $pdf->Cell(106,6,number_format($nTotalCargo,2,'.',',') ,0,0,'R');

	$pdf->setXY($nPosX,$pyy+5);
  $pdf->SetFont($cEstiloLetraOfton,'',8);
  $pdf->Cell(78,6,"SALDO A FAVOR",0,0,'L');
  $pdf->Cell(106,6,number_format($nTotalFavor,2,'.',',') ,0,0,'R');

  $pyy += 11;
  /** Fin Imprimo Saldo **/

  ##Imprimo Valor en Letras##
  $pdf->SetFont($cEstiloLetraOfton,'',8);
  $nTotletra = f_Cifra_Php(str_replace("-","",abs($_POST['nIPASal'])),'PESO');
  $pdf->setXY($nPosX,$pyy);
  $pdf->MultiCell(100,4,utf8_decode($nTotletra),0,'L');

  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	$pdf->Output($cFile);

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }
	echo "<html><script>document.location='$cFile';</script></html>";

  //$pdf->Output();
?>

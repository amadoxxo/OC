<?php
  /**
	 * Imprime Vista Previa Factura de Venta UPS.
	 * --- Descripcion: Permite Imprimir la Vista Previa de la Factura de Venta.
	 * @author Johana Arboleda <jarboleda@opentecnologia.com.co>
	 */
  include("../../../../libs/php/utility.php");
	include("../../../../libs/php/utiliqdo.php");

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
  		$cNewYear = substr($cRegFCre,0,4);
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

	## Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Para encabezado de factura ##
	$qAgeDat  = "SELECT ";
	$qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
	$qAgeDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
	$qAgeDat .= "$cAlfa.SIAI0150.CLIDIRXX, ";
	$qAgeDat .= "$cAlfa.SIAI0150.CLITELXX, ";
	$qAgeDat .= "$cAlfa.SIAI0150.CLIFAXXX ";
	$qAgeDat .= "FROM $cAlfa.SIAI0150 ";
	$qAgeDat .= "WHERE ";
	$qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vSysStr['financiero_nit_agencia_aduanas']}\" AND ";
	$qAgeDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
	$xAgeDat  = f_MySql("SELECT","",$qAgeDat,$xConexion01,"");
	$vAgeDat  = mysql_fetch_array($xAgeDat);
	## Fin Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Para encabezado de factura ##

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

  ##Consulto en la SIAI0150 Datos del Facturado A: ##
  $qCliDat  = "SELECT ";
  $qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
	$qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIDIRXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLITELXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIFAXXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.PAIIDXXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.DEPIDXXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CIUIDXXX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLICONTX, ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIPLAXX, ";
	$qCliDat .= "$cAlfa.fpar0151.cccplaxx ";
	$qCliDat .= "FROM $cAlfa.SIAI0150 ";
	$qCliDat .= "LEFT JOIN $cAlfa.fpar0151 ON $cAlfa.SIAI0150.CLIIDXXX = $cAlfa.fpar0151.cliidxxx ";
	$qCliDat .= "WHERE ";
	$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$_POST['cTerIdInt']}\" AND ";
	$qCliDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
	$xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,"La consulta es:  ".$qCliDat);
  $nFilCli  = mysql_num_rows($xCliDat);
  if ($nFilCli > 0) {
    $vCliDat = mysql_fetch_array($xCliDat);
  }
  ##Consulto en la SIAI0150 Datos del Facturado A: ##

  ##Traigo Ciudad del Facturado A ##
  $qCiuDat  = "SELECT * ";
  $qCiuDat .= "FROM $cAlfa.SIAI0055 ";
  $qCiuDat .= "WHERE ";
  $qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCliDat['PAIIDXXX']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCliDat['DEPIDXXX']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCliDat['CIUIDXXX']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
  $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
  $nFilCiu  = mysql_num_rows($xCiuDat);
  if ($nFilCiu > 0) {
    $vCiuDat = mysql_fetch_array($xCiuDat);
  }
  ##Fin Traigo Ciudad del Facturado A ##

  ##Traigo Pais del Cliente ##
  $qPaiDat  = "SELECT * ";
  $qPaiDat .= "FROM $cAlfa.SIAI0052 ";
  $qPaiDat .= "WHERE ";
  $qPaiDat .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$vCliDat['PAIIDXXX']}\" AND ";
  $qPaiDat .= "$cAlfa.SIAI0052.REGESTXX = \"ACTIVO\" ";
  $xPaiDat  = f_MySql("SELECT","",$qPaiDat,$xConexion01,"");
  $nFilPai  = mysql_num_rows($xPaiDat);
  if ($nFilPai > 0) {
    $vPaiDat = mysql_fetch_array($xPaiDat);
  }
  ##Fin Traigo Ciudad del Cliente ##


  ##Traigo Datos de Contacto del Facturado a ##
  if($vCliDat['CLICONTX'] != ""){
  	$vContactos = explode("~",$vCliDat['CLICONTX']);
  	//f_Mensaje(__FILE__,__LINE__,count($vContactos));
  	if(count($vContactos) > 1){
  		$vIdContacto = $vContactos[1];
  	}else{
  		$vIdContacto = $vCliDat['CLICONTX'];

  	}
  }//if($vCocDat['CLICONTX'] != ""){

  $qConDat  = "SELECT ";
  $qConDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS NOMBRE ";
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
	$cDocId  = "";
	$cDocSuf = "";
	for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
		$cDocId   = $_POST['cDosNro_DOS'.($i+1)];
    $cDocSuf  = $_POST['cDosSuf_DOS'.($i+1)];
    $cSucId   = $_POST['cSucId_DOS'.($i+1)];
    $i = $_POST['nSecuencia_Dos'];
  }//for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
  ##Fin Traigo Primer Do para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##

	##Trayendo Datos de Do Dependiendo del Tipo de Operacion ##
	$vDatDo = f_Datos_Operativos_Do($cSucId, $cDocId, $cDocSuf);
	$vDceDat = $vDatDo['decdatxx'];
	$cTasCam = $vDatDo['tascamxx']; //Tasa de Cambio
	$cDocTra = $vDatDo['doctraxx']; //Documento de Transporte
	$cBultos = $vDatDo['bultosxx']; //Bultos
	$cPesBru = $vDatDo['pesbruxx']; //Peso Bruto
	$cPesNet = $vDatDo['pesnetxx']; //Peso Neto
	$nValAdu = $vDatDo['valaduxx']; //Valor en aduana
	$cOpera  = $vDatDo['operaxxx']; //texto valor CIF: o FOB: segun el tipo operacion del tramite
	$cPedido = $vDatDo['pedidoxx']; //Pedido
	$cAduana = $vDatDo['aduanaxx']; //Descripcion Aduana
	$cNomVen = $vDatDo['nomvenxx']; //Nombre Vendedor
	$cOrdCom = $vDatDo['ordcomxx']; //Orden de Compra
	$cPaiOri = $vDatDo['paiorixx']; //Pais de Origen
	$dReaArr = $vDatDo['fecrearr']; //Fecha real de arribo
	$cCiuOri = $vDatDo['ciuorixx']; //Ciudad Origen
	$cCiuDes = $vDatDo['ciudesxx']; //Ciudad Destino
	###Fin Trayendo Datos de Do Dependiendo del Tipo de Operacion ##

	##Codigo para imprimir los ingresos para terceros ##
	$mIngTer = array();
	##Traigo los Documentos que estan marcados como PAGOIMPUESTOS##
	$qDatCom  = "SELECT ";
	$qDatCom .= "ctoidxxx, ";
	$qDatCom .= "pucidxxx ";
	$qDatCom .= "FROM $cAlfa.fpar0119 ";
	$qDatCom .= "WHERE ";
	$qDatCom .= "(ctoptaxg = \"SI\" OR ctoptaxl = \"SI\") AND ";
	$qDatCom .= "regestxx = \"ACTIVO\" ";
	$xDatCom  = f_MySql("SELECT","",$qDatCom,$xConexion01,"");
	$vComImp = array();
	while($xRDC = mysql_fetch_array($xDatCom)){
		$nInd_mComImp = count($vComImp);
		$vComImp[] = $xRDC['ctoidxxx']."~".$xRDC['pucidxxx'];
	}
	##Fin Traigo los Documentos que estan marcados como PAGOIMPUESTOS##
  for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
		$nSwitch_Find = 0;
		//f_Mensaje(__FILE__,__LINE__,"{$_POST['cComId_PCCA'.($i+1)]}~{$_POST['cPucId_PCCA'.($i+1)]}~".in_array("{$_POST['cComId_PCCA'.($i+1)]}~{$_POST['cPucId_PCCA'.($i+1)]}", $vComImp));
		if (in_array("{$_POST['cComId_PCCA'.($i+1)]}~{$_POST['cPucId_PCCA'.($i+1)]}", $vComImp) == false) {
			for ($j=0;$j<count($mIngTer);$j++) {
				if ($_POST['cComId_PCCA'.($i+1)] == $mIngTer[$j]['cComId']) {
					$nSwitch_Find = 1;
					$mIngTer[$j]['cComCsc3'] .= ((strlen($mIngTer[$nInd_mIngTer]['cComCsc3'])+strlen("/".$_POST['cComCsc3_PCCA'.($i+1)])) <= 40) ? "/".$_POST['cComCsc3_PCCA'.($i+1)] : "";
					$mIngTer[$j]['nComVlr']   += $_POST['nComVlr_PCCA'.($i+1)];
					$mIngTer[$j]['nBaseIva']  += $_POST['nBaseIva_PCCA'.($i+1)];
					$mIngTer[$j]['nVlrIva']   += $_POST['nVlrIva_PCCA'.($i+1)];
				}
			}
		}
		if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
			$nInd_mIngTer = count($mIngTer);
			$mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId_PCCA'  .($i+1)];
			$vDesc = explode("^",$_POST['cComObs_PCCA' .($i+1)]);

			$mIngTer[$nInd_mIngTer]['cComObs']  = $_POST['cComObs_PCCA' .($i+1)];
			$mIngTer[$nInd_mIngTer]['ccomcsc3'] = $_POST['cComCsc3_PCCA'.($i+1)];
			$mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA' .($i+1)];
			$mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nBaseIva_PCCA'.($i+1)];
			$mIngTer[$nInd_mIngTer]['nVlrIva']  = $_POST['nVlrIva_PCCA'.($i+1)];
		}
  }//for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
  ##Fin Codigo para imprimir los ingresos para terceros ##
  ##Recorro Grilla de IP para saber si se habilita la impresion del bloque de Ingresos Propios##

  ##Fin de Recorro Grilla de IP para saber si se habilita la impresion del bloque de Ingresos Propios##
	## Comienzo a pintar Vista Previa de Factura##

  #Agrupo Ingresos Propios
  for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
		$vDatosIp = array();
    $cObs = "|".$_POST['cSerId_IPA'.($i+1)]."~".$_POST['cFcoId_IPA'.($i+1)]."~".$_POST['cComObs_IPA'.($i+1)]."|";
    $vDatosIp = f_Cantidad_Ingreso_Propio($cObs,'',$_POST['cSucId_IPA'.($i+1)],$_POST['cDosNro_IPA'.($i+1)],$_POST['cDosSuf_IPA'.($i+1)]);

    $mIP[$_POST['cComId_IPA'.($i+1)]]['ctoidxxx']  = $_POST['cComId_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comobsxx'] = $vDatosIp[0];
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
  }//for ($k=0;$k<count($mCodDat);$k++) {
	foreach ($mIP as $cKey => $mValores) {
		$mDatIP[] = $mValores;
	}
  #Fin Agrupo Ingresos Propios

	$cObsFac = $_POST['cComObs'];

	//Forma de pago
  $cFormaPago = "";
  if ($_POST['cComFpag'] != "") {
    //Buscando descripcion
    $cFormaPago = ($_POST['cComFpag'] == 1) ? "CONTADO" : "CREDITO";
	}
	$cMedioPago = "";
	if ($_POST['cMePagId'] != "") {
		//Buscando descripcion
		$qMedPag  = "SELECT ";
		$qMedPag .= "mpaidxxx, ";
		$qMedPag .= "mpadesxx, ";
		$qMedPag .= "regestxx ";
		$qMedPag .= "FROM $cAlfa.fpar0155 ";
		$qMedPag .= "WHERE ";
		$qMedPag .= "mpaidxxx = \"{$_POST['cMePagId']}\" LIMIT 0,1";
		$xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
		// f_Mensaje(__FILE__,__LINE__,$qMedPag."~ ".mysql_num_rows($xMedPag));
		if (mysql_num_rows($xMedPag) == 0) {
			$vMedPag = mysql_fetch_array($xMedPag);
			$cMedioPago = $vMedPag['mpadesxx'];
		}
	}
  //////////////////////////////////////////////////////////////////////////

  class PDF extends FPDF {
		function Header() {
		  global $cAlfa;   global $cRoot;   global $cPlesk_Skin_Directory;
			global $gCcoId;  global $gSccId;  global $gMesDes; global $gAnoDes; global $gMesHas; global $gAnoHas;
			global $cUsrNom; global $cCcoDes; global $cScoDes; global $nPag;    global $vAgeDat; global $vCocDat;
			global $vResDat; global $cTasCam; global $cDocTra; global $cBultos; global $cPesBru; global $cDocId;
			global $vCliDat; global $vCiuDat; global $vDceDat; global $cAduana; global $cOpera;  global $vConDat;
			global $cPesNet; global $cObsFac; global $cPesNet; global $cCiuOri; global $cCiuDes; global $dReaArr;
			global $vPaiDat; global $cFormaPago; global $cMedioPago;

			$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma.jpg',165,15,40,12);
			$posy	= 5;  /// PRIMERA POSICION DE Y ///
      $posx	= 5;
      ##Impresion Datos Generales Factura ##

      #consecutivo
      $this->setXY($posx,$posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(60,5,"FACTURA ELECTRONICA DE VENTA   ",0,0,'L');
      $this->SetFont('verdana','',8);
      $this->Cell(33,5,$vResDat['resprexx']." No. XXXXX",1,0,'L');
      #consecutivo

      #Descripcion
      $this->setXY($posx + 102,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(60,5,$vAgeDat['CLINOMXX'],0,0,'L');

			$posy += 3;
			$this->setXY($posx + 102,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(60,5,"CR 106  15 A 25 MZ 8 BG 55 B P 3",0,0,'L');

			$posy += 3;
			$this->setXY($posx,$posy);
      $this->SetFont('verdana','',7);
			$this->Cell(93,5,"NIT ".number_format($vAgeDat['CLIIDXXX'],0,',','.')."-".f_Digito_Verificacion($vAgeDat['CLIIDXXX']),0,0,'C');

			$this->setXY($posx + 102,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(60,5,utf8_decode("Bogotá, Colombia"),0,0,'L');

			$posy += 3;
			$this->setXY($posx,$posy);
      $this->SetFont('verdana','',7);
			$this->Cell(93,5,"FACTURACION ELECTRONICA SEGUN",0,0,'C');

			$this->setXY($posx + 102,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(60,5,utf8_decode("Teléfono: (+571) 7485005"),0,0,'L');

			$posy += 3;
			$this->setXY($posx,$posy);
      $this->SetFont('verdana','',7);
      $dFechaDe = str_replace('-', '/',  $vResDat['resfdexx']);
      $dFechaHa = str_replace('-', '/',  $vResDat['resfhaxx']);
			$this->Cell(93,5,"RESOLUCION DIAN ".$vResDat['residxxx']." de ".$dFechaDe." a ".$dFechaHa,0,0,'C');

			$this->setXY($posx + 102,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(60,5,utf8_decode("E-mail: gerencia@bma.com.co"),0,0,'L');

			$posy += 3;
			$this->setXY($posx,$posy);
      $this->SetFont('verdana','',7);
      // Traigo numero de Meses entre Desde y Hasta
			$dFechaInicial = date_create($vResDat['resfdexx']);
			$dFechaFinal = date_create($vResDat['resfhaxx']);
			$nDiferencia = date_diff($dFechaInicial, $dFechaFinal);
			$nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m + (($nDiferencia->d > 0) ? 1 : 0);
			$this->Cell(93,5,"DESDE: {$vResDat['resprexx']}-{$vResDat['resdesxx']} HASTA: {$vResDat['resprexx']}-{$vResDat['reshasxx']}. VIGENCIA ".$nMesesVigencia." MESES",0,0,'C');

			$this->setXY($posx + 102,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(60,5,utf8_decode(""),0,0,'L');

			$posy += 3;
			$this->setXY($posx,$posy);
      $this->SetFont('verdana','',7);
			$dFecha = str_replace('-', '/',  $vResDat['resfdexx']);
			$this->Cell(93,5,"IVA REGIMEN COMUN - NO SOMOS GRANDE CONTRIBUYENTES ",0,0,'C');

			$this->setXY($posx + 102,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(60,5,utf8_decode(""),0,0,'L');

			$posy += 3;
			$this->setXY($posx,$posy);
      $this->SetFont('verdana','',7);
			$dFecha = str_replace('-', '/',  $vResDat['resfdexx']);
			$this->Cell(93,5,"CODIGO DE AGENCIA DE ADUANA 547",0,0,'C');

			$this->setXY($posx + 102,$posy);
      $this->SetFont('verdana','',7);
			$this->Cell(60,5,utf8_decode(""),0,0,'L');
			
			$posy += 3;
			$this->setXY($posx + 102,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(30,5,utf8_decode("FECHA DE EMISION "),0,0,'L');
			$this->setXY($posx + 135,$posy);
			$this->Cell(30,5,f_Fecha_Letras($_POST['dRegFCre']),0,0,'L');

			$posy += 3;
			$this->setXY($posx + 102,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(30,5,utf8_decode("HORA DE EMISION "),0,0,'L');
			$this->setXY($posx + 135,$posy);
			$this->Cell(30,5,date("H:i:s"),0,0,'L');

			$this->SetFillColor(109,102,102);
      $this->Rect(5,37,200,5, 'FD');

      $posy	= 45;
      #Cliente ##
      $this->setXY($posx+1,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(104,4,$vCliDat['CLINOMXX'],0,0,'L');
			#Cliente ##

			##Direccion ##
      $this->Ln(4);
			$this->setX($posx+1);
      $this->MultiCell(104,4,$vCliDat['CLIDIRXX'],0,'L',0);
			##Fin Direccion ##
			
			##Ciudad ##
      $this->setX($posx+1);
      $this->Cell(104,4,$vCiuDat['CIUDESXX']." (".$vPaiDat['PAIDESXX'].")",0,0,'L');
			##Fin Ciudad ##
			
			##Telefono ##
      $this->Ln(4);
      $this->setX($posx+1);
      $this->Cell(104,4,"Tel: ".$vCliDat['CLITELXX'],0,0,'L');
			##Fin Telefono ##
			
			##Atten##
      $this->Ln(4);
      $this->setX($posx+1);
      $this->Cell(104,4,"Attn: ".$vConDat['NOMBRE'],0,0,'L');
			##Fin Atten##

			#Nit Cliente ##
      $this->Ln(4);
      $this->setX($posx+1);
      $this->Cell(104,4,"NIT.".$vAgeDat['CLIIDXXX']."-".f_Digito_Verificacion($vAgeDat['CLIIDXXX']),0,0,'L');
      #Nit Cliente ##

			##Forma de Pago ##
			$this->setXY($posx+102,$posy);
			$this->Cell(20,4,"Forma de Pago: ",0,0,'L');
			$this->Cell(78,4,$cFormaPago,0,0,'L');
			##Fin Forma de Pago ##

			##Medio de Pago ##
      $this->Ln(4);
      $this->setX($posx+102);
      $this->Cell(20,4,"Medio de Pago: ",0,0,'L');
      $this->Cell(78,4,$cMedioPago,0,0,'L');
			##Fin Medio de Pago ##

			##Fecha Vencimiento ##
			$dFecVen = date("Y-m-d",mktime(0,0,0,substr($_POST['dRegFCre'],5,2),substr($_POST['dRegFCre'],8,2)+$_POST['cTerPla'],substr($_POST['dRegFCre'],0,4)));
      $this->Ln(4);
      $this->setX($posx+102);
      $this->Cell(25,4,"Fecha Vencimiento: ",0,0,'L');
      $this->Cell(73,4,f_Fecha_Letras($dFecVen),0,0,'L');
			##Fin Fecha Vencimiento ##

      ##ref ##
      $this->Ln(4);
      $this->setX($posx+102);
      $this->Cell(20,4,"Ref: ",0,0,'L');
      $this->Cell(78,4,$vDceDat['docpedxx'],0,0,'L');
      ##Fin ref ##
      
      ##Pos / ope ##
      $this->Ln(4);
      $this->setX($posx+102);
      $this->Cell(20,4,"POS / OPE: ",0,0,'L');
      $this->Cell(78,4,$cDocId,0,0,'L');
			##Fin Pos / ope ##

      ##Nro bulto ##
      $this->Ln(4);
      $this->setX($posx+102);
      $this->Cell(20,4,"Nro. Bultos: ",0,0,'L');
      $this->Cell(78,4,number_format($cBultos,2,',','.'),0,0,'L');
			##Fin Nro bulto ##
      
			##Peso/Vol ##
      $this->Ln(4);
      $this->setX($posx+102);
      $this->Cell(20,4,"Peso/Vol: ",0,0,'L');
      $this->Cell(78,4,number_format($cPesBru,2,',','.'),0,0,'L');
			##Fin Peso/Vol ##

      ##Arribo ##
      $this->Ln(4);
      $this->setX($posx+102);
      $this->Cell(20,4,"Arribo: ",0,0,'L');
			$cfecha = ($dReaArr != '0000-00-00' ) ? str_replace('-', "/", $dReaArr) : ' / / ';
			$this->Cell(78,4,$cfecha,0,0,'L');
			##Fin Arribo ##

			$this->Rect($posx,$posy-8,200,40);
			$posy = $this->GetY()+4;
			$this->Rect($posx,$posy,200,120);

			$posy += 130;
			$this->Rect($posx,$posy,145,18);
			$this->Rect($posx+145,$posy-10,55,28);
			$this->Line($posx+145,$posy+10,$posx+200,$posy+10);

			$this->SetFont('verdanab','',6.5);
			$this->setXY($posx+2 ,$posy+9);
			$this->MultiCell(140,3,utf8_decode("FAVOR REALIZAR TRANFERENCIA A LA CUENTA CORRIENTE BANCOLOMBIA No: 679-285559-19 O A LA CUENTA CORRIENTE DAVIVIENDA No. 485-1699979-99 Y ENVIAR COPIA DE LA CONSIGNACIÓN AL CORREO director.administrativo@bma.com.co"),0,'C',0);

			$posy += 10;
			$this->SetFont('verdana','',8);
			$this->setXY($posx+145 ,$posy);
			$this->Cell(53,4,utf8_decode("ACEPTA: (FIRMA SELLO-No DOC):"),0,0,'L');

			$posy += 4;
			$this->setXY($posx+145 ,$posy);
			$this->Cell(53,4,utf8_decode("FECHA DE RECIBIDO: .................."),0,0,'L');

			$posy += 4;
			$this->setXY($posx,$posy);
			$this->SetFont('verdana','',6);
			$this->Cell(200,8,utf8_decode("Despues de 10 días calendario de recibida esta factura se asume aceptación. La mora en la cancelación de esta factura causará intereses a la Tasa máxima permitida por la Ley"),1,0,'C');

			$posy += 10;
			$this->setXY($posx,$posy);
			$this->SetFont('verdanab','',7);
			$this->Cell(200,4,utf8_decode("REPRESENTACIÓN IMPRESA DE LA FACTURA ELECTRÓNICA"),0,0,'L');
		}//function Header() {

		function Footer() {
		  global $cRoot;   global $cPlesk_Skin_Directory;   global $cNomCopia;   global $nCopia;
		  global $nb;      global $n;	                      global $cObsFac;

		 	$posx	= 5;

		  $this->SetFont('verdana','',6);
      $this->setXY($posx,275);
    	$this->Cell(200,3,$cNomCopia,0,0,'C');
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
	$pdf->AddFont('verdana','','verdana.php');
	$pdf->AddFont('verdanab','','verdanab.php');
	$pdf->AddFont('otfon1','','otfon1.php');
	$pdf->AliasNbPages();
	$pdf->SetMargins(0,0,0);
	$pdf->SetAutoPageBreak(0,0);
	$pdf->AddPage();

	$posy = 78;
	$posx = 10;
	$posFin = 193;
	$nb = 1;
	$pyy = $posy;
	$pdf->setXY($posx,$pyy);

	if(count($mIngTer) > 0 ){//Si la matriz de Pcc tiene registros

		$pdf->SetFont('verdanab','',8);
		$pdf->Cell(140,4,"INGRESOS PARA TERCEROS",0,0,'L');
		$pyy +=5;
		
		## Impresion de Titulos
		$pdf->SetFont('verdanab','',7);
		$pdf->setXY($posx,$pyy);
		$pdf->Cell(20,5,utf8_decode("CÓDIGO"),0,0,'C');
		$pdf->Cell(100,5,"CONCEPTO",0,0,'C');
		$pdf->Cell(20,5,"CANTIDAD",0,0,'C');
		$pdf->Cell(25,5,"USD",0,0,'C');
		$pdf->Cell(25,5,"COL$",0,0,'C');
		$pyy +=4;
		$pdf->setXY($posx,$pyy);

		$pdf->SetWidths(array(20,100,20,25,25));
		$pdf->SetAligns(array("C","L","C","R","R"));

		$nTotPcc = 0;
		for($i=0;$i<count($mIngTer);$i++){
			$pyy = $pdf->GetY();
			if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
				$pdf->AddPage();
				$nb++;
				$posy = 78;
				$posx = 10;
				$pyy = $posy;
				$pdf->SetFont('verdana','',7);
				$pdf->setXY($posx,$posy);
			}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion
			$nTotPcc += $mIngTer[$i]['nComVlr'];
			
			$vComObs  = explode("^",$mIngTer[$i]['cComObs']);
			$cComObs = $vComObs[0];
			
			$pdf->SetFont('verdana','',7);
			$pdf->setX($posx);
			$pdf->Row(array(trim(substr($mIngTer['cComId'],-3)),
											trim(str_replace("CANTIDAD", "CANT", $cComObs)), 
											"1", 
											"",
											number_format($mIngTer[$i]['nComVlr'],0,',','.')));
		}//for($i=0;$i<count($mIngTer);$i++){
			
		$pyy = $pdf->GetY();
		if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$nb++;
			$posy = 78;
			$posx = 10;
			$pyy = $posy;
			$pdf->SetFont('verdana','',7);
			$pdf->setXY($posx,$posy);
		}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		##Imprimo Subtotal de Pagos a Terceros ##
		$pdf->SetFont('verdanab','',8);
		$pdf->setXY($posx,$pyy);
		$pdf->Cell(120,10,"TOTAL INGRESOS DE TERCEROS",0,0,'L');
		$pdf->Cell(20,10,"",0,0,'R');
		$pdf->Cell(25,10,"",0,0,'R');
		$pdf->Cell(25,10,number_format($nTotPcc,2,'.',','),0,0,'R');
		##Fin Imprimo Subtotal de Pagos a Terceros ##

	}//if(count($mIngTer) > 0){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
	##Fin Imprimo Pagos a Terceros ##

	$pyy = $pdf->GetY();
	if(count($mIngTer) > 0){
		$pyy+=6;
	}

	if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
		$pdf->AddPage();
		$nb++;
		$posy = 78;
		$posx = 10;
		$pyy = $posy;
		$pdf->SetFont('verdana','',8);
		$pdf->setXY($posx,$posy);
	}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

	$nSubToIP = 0;
	if($_POST['nSecuencia_IPA'] > 0){//Valido si la Secuencia de la Grilla de Ip viene mayor a cero para imprimir bloque de INGRESOS PROPIOS

		$pdf->SetFont('verdanab','',8);
		$pdf->setXY($posx,$pyy);
		$pdf->Cell(140,10,"INGRESOS PROPIOS",0,0,'L');
		$pyy +=8;
		
		## Impresion de Titulos
		$pdf->SetFont('verdanab','',7);
		$pdf->setXY($posx,$pyy);
		$pdf->Cell(20,5,utf8_decode("CÓDIGO"),0,0,'C');
		$pdf->Cell(100,5,"CONCEPTO",0,0,'C');
		$pdf->Cell(20,5,"CANTIDAD",0,0,'C');
		$pdf->Cell(25,5,"USD",0,0,'C');
		$pdf->Cell(25,5,"COL$",0,0,'C');
		$pyy +=5;
		$pdf->setXY($posx,$pyy);

		$pdf->SetWidths(array(20,100,20,25,25));
		$pdf->SetAligns(array("C","L","C","R","R"));

	  ##Imprimo Ingresos Propios##
	  for ($k=0;$k<count($mDatIP);$k++) {
			$pyy = $pdf->GetY();
			if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
				$pdf->AddPage();
				$nb++;
				$posy = 78;
				$posx = 10;
				$pyy = $posy;
				$pdf->SetFont('verdana','',7);
				$pdf->setXY($posx,$posy);
			}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

			$nSubToIP += $mDatIP[$k]['comvlrxx'];

			$pdf->SetFont('verdana','',7);
			$pdf->setX($posx);
			$pdf->Row(array(trim($mDatIP[$k]['seridxxx']),
											$mDatIP[$k]['comobsxx'], 
											$mDatIP[$k]['canfexxx'], 
											"",
											number_format($mDatIP[$k]['comvlrxx'],0,',','.')));
		  }//for ($k=0;$k<count($mCodDat);$k++) {
		  ##Fin Imprimo Ingresos Propios##

		  $pyy = $pdf->GetY();
		  if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
	  	  $pdf->AddPage();
	  	  $nb++;
	  	  $posy = 78;
	      $posx = 10;
	      $pyy = $posy;
	      $pdf->SetFont('verdana','',7);
	      $pdf->setXY($posx,$posy);
	    }//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		  ##Imprimo Subtotal de Ingresos Propios ##$pyy += 1;
	  	$pdf->SetFont('verdanab','',8);
	  	$pdf->setXY($posx,$pyy);
			$pdf->Cell(120,10,"TOTAL INGRESOS PROPIOS",0,0,'L');
			$pdf->Cell(20,10,"",0,0,'R');
	  	$pdf->Cell(25,10,"",0,0,'R');
			$pdf->Cell(25,10,number_format($nSubToIP,2,'.',','),0,0,'R');
		  ##Imprimo Subtotal de Ingresos Propios ##
	  }//if($nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
	  ##Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##

		$pyy = $pdf->GetY();
		$pyy +=6;
		if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$nb++;
			$posy = 78;
			$posx = 10;
			$pyy = $posy;
			$pdf->SetFont('verdana','',7);
			$pdf->setXY($posx,$posy);
		}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

	  ##Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##
	  $nSubToFac = $nTotPcc + $nSubToIP;

	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY($posx,$pyy);
		$pdf->Cell(120,10,"SUBTOTAL INGRESOS",0,0,'L');
		$pdf->Cell(20,10,"",0,0,'R');
	  $pdf->Cell(25,10,"",0,0,'R');
		$pdf->Cell(25,10,number_format($nSubToFac,2,'.',','),0,0,'R');
		$pyy += 4;
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

		##Bloque que acumula retenciones por valor de porcentaje##
    $mRetFte = array();
    for ($i=0;$i<$_POST['nSecuencia_IPA'];$i++) {
      if ($_POST['nPorFte_IPA'.($i+1)] > 0) {
        $nSwitch_Encontre_RetFte = 0;
        for ($j=0;$j<count($mRetFte);$j++) {
          if ($_POST['nPorFte_IPA'.($i+1)] == $mRetFte[$j]['pucretxx']) {
            $nSwitch_Encontre_RetFte = 1;
            $mRetFte[$j]['comvlrxx']  += $_POST['nVlrFte_IPA'.($i+1)];
            $mRetFte[$j]['basexxxx']  += ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
          }
        }

        if ($nSwitch_Encontre_RetFte == 0) { // No lo encontro en la matriz para pintar en la factura
          $nInd_mRetFte = count($mRetFte);
          $mRetFte[$nInd_mRetFte]['tipretxx'] = "RET.FUENTE";
          $mRetFte[$nInd_mRetFte]['pucretxx'] = $_POST['nPorFte_IPA' .($i+1)];
          $mRetFte[$nInd_mRetFte]['comvlrxx'] = $_POST['nVlrFte_IPA' .($i+1)];
          $mRetFte[$nInd_mRetFte]['basexxxx'] = ($_POST['nComVlr_IPA'.($i+1)] > 0) ? $_POST['nComVlr_IPA'.($i+1)] : $_POST['nComVlrNF_IPA'.($i+1)];
        }
      }
    }
	  ##Busco Valor de RET.FTE ##
		$nTotRfte = 0;
	  for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
	    $nTotRfte +=$_POST['nVlrFte_IPA'.($i+1)];
	  }
	 	##Fin Busco Valor de RET.FTE ##

		if ($pdf->GetY()+6 > 160) {
			$pdf->AddPage();
			$nb++;
			$posy = 78;
			$posx = 10;
			$pyy = $posy;
			$pdf->SetFont('verdana','',7);
			$pdf->setXY($posx,$posy);
		}

	  $posy = 157;
	  $posx = 150;
	  $pdf->SetFont('verdana','',8);

		$pdf->Line($posx-10,$posy,$posx+55,$posy);

		##RETENCIONES##
		$nReteFuenteOnce = 0;
		$nReteFuenteCuatro = 0;
		foreach ($mRetFte as $key => $mFte) {
			if ( ($mFte['pucretxx']+0) == 11 ) {
				$nReteFuenteOnce = 1;
			}
			if ( ($mFte['pucretxx']+0) == 4 ) {
				$nReteFuenteCuatro = 1;
			}
			$pdf->setXY($posx,$posy);
			$pdf->Cell(22,5,"RETEFUENTE ".($mFte['pucretxx']+0)."%",0,0,'R');
			$pdf->Cell(30,5,number_format($mFte['comvlrxx'],2,'.',','),0,0,'R');
			$posy += 4;
		}
		if ($nReteFuenteOnce == 0) {
			$pdf->setXY($posx,$posy);
			$pdf->Cell(22,5,"RETEFUENTE 11%",0,0,'R');
			$pdf->Cell(30,5,number_format(0,2,'.',','),0,0,'R');
			$posy += 4;
		}
		if ($nReteFuenteCuatro == 0) {
			$pdf->setXY($posx,$posy);
			$pdf->Cell(22,5,"RETEFUENTE 4%",0,0,'R');
			$pdf->Cell(30,5,number_format(0,2,'.',','),0,0,'R');
			$posy += 4;
		}

		#Saldo en letras
		$nTotPag  = $nSubToFac + $_POST['nIPAIva'];
    $nSaldo   = (($nSubToFac + $_POST['nIPAIva']) - $nTotIca - $nTotIva - $nTotRfte + $_POST['nIPAARFte']) + $_POST['nIPAAnt'];
		$nSalFavo = 0;
		if($nSaldo < 0) {
			$nSalFavo = $nSaldo;
			$nSaldo   = 0;
		}

		$pdf->setXY($posx,$posy);
		$pdf->Cell(22,5,"RETEICA",0,0,'R');
		$pdf->Cell(30,5,number_format($nTotIca,2,'.',','),0,0,'R');
		$posy += 4;
		$pdf->setXY($posx,$posy);
		$pdf->Cell(22,5,"RETEIVA",0,0,'R');
		$pdf->Cell(30,5,number_format($nTotIva,2,'.',','),0,0,'R');
		$posy += 4;
		$pdf->setXY($posx,$posy);
		$pdf->SetFont('verdanab','',8);
		$pdf->Cell(22,5,"SUBTOTAL",0,0,'R');
		$pdf->Cell(30,5,number_format($nSubToFac - $nTotIca - $nTotIva - $nTotRfte + $_POST['nIPAARFte'],2,'.',','),0,0,'R');
		$posy += 4;
		$pdf->SetFont('verdana','',8);
		$pdf->setXY($posx,$posy);
		$pdf->Cell(22,5,"IVA 19.00%",0,0,'R');
		$pdf->Cell(30,5,number_format($_POST['nIPAIva'],2,'.',','),0,0,'R');
		$posy += 4;
		$pdf->setXY($posx,$posy);
		$pdf->SetFont('verdanab','',8);
		$pdf->Cell(22,5,"TOTAL FACTURA",0,0,'R');
		$pdf->Cell(30,5,number_format(($nSubToFac - $nTotIca - $nTotIva - $nTotRfte + $_POST['nIPAARFte']) + $_POST['nIPAIva'],2,'.',','),0,0,'R');
		$posy += 4;
		$pdf->setXY($posx,$posy);
		$pdf->SetFont('verdana','',8);
		$pdf->Cell(22,5,"ANTICIPOS",0,0,'R');
		$pdf->Cell(30,5,number_format(abs($_POST['nIPAAnt']),2,'.',','),0,0,'R');
		$posy += 4;
		$pdf->setXY($posx,$posy);
		$pdf->SetFont('verdana','',8);
		$pdf->Cell(22,5,"TOTAL A PAGAR",0,0,'R');
		$pdf->Cell(30,5,number_format($nSaldo,2,'.',','),0,0,'R');
		$posy += 4;
		$pdf->setXY($posx,$posy);
		$pdf->SetFont('verdana','',8);
		$pdf->Cell(22,5,"SALDO A FAVOR",0,0,'R');
		$pdf->Cell(30,5,number_format(abs($nSalFavo),2,'.',','),0,0,'R');

		$posy = 185;
		$posx = 5;
		$pdf->setXY($posx,$posy);
		$pdf->SetFont('verdana','',7);
		$cTextoFac = "Esta factura se asimila en todos sus efectos a la letra de cambio (Art 774 Num. 6 del Código de comercio), cumple con todos los requisitos de la Ley 1231 de Julio 17 de 2008; articulos 519 y 621 del codigo de comercio, 617 del Estatuto Tributario Nacional y Articulo 773 del Decreto 410 de 1971.";
		$pdf->MultiCell(110,3,utf8_decode($cTextoFac),1,'C',0);

		$nTotPag1 = f_Cifra_Php(abs($nSaldo),'PESO');

		$posy = 200;
		$pdf->Rect($posx,$posy-3,$posx+140,10);
		$pdf->setXY($posx,$posy);
		$pdf->SetFont('verdana','',7);
		$pdf->MultiCell(145,3,"Son: ".trim($nTotPag1)." MCTE",0,'C',0);

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

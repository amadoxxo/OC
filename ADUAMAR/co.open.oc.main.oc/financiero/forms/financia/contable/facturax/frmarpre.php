<?php
  /**
	 * Imprime Factura de Venta ADUAMARX.
	 * --- Descripcion: Permite Imprimir Factura de Venta ADUAMARX por Viste Previa.
	 * @author Juan Jose Trujillo <juan.trujillo@open-eb.co>
	 * @version 001
	 */
  include("../../../../libs/php/utility.php");  
  include("../../../../libs/php/utiliqdo.php");

  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

  $pdf = new FPDF('P','mm','Letter');
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->SetFont('verdana','',8);
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);

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

  for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
	  if($i == 0) {
      $cDocId   = $_POST['cDosNro_DOS'.($i+1)];
      $cDocSuf  = $_POST['cDosSuf_DOS'.($i+1)];
      $cSucId   = $_POST['cSucId_DOS' .($i+1)];
    }
  }//for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {

  //Fecha Creacion
  $dFecEmi = $_POST['dRegFCre'];
  //Hora Creacion
  $dHora_IPA = explode(" ", $_POST['cRegStamp_DOS1']);
  $dHorEmi   = $dHora_IPA[1];
  //Fecha Vencimiento
  $dFecVen = date("Y-m-d",mktime(0,0,0,substr($_POST['dRegFCre'],5,2),substr($_POST['dRegFCre'],8,2)+$_POST['cTerPla'],substr($_POST['dRegFCre'],0,4)));

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
  $cFecTra = $vDatDo['dgefdtxx']; //Fecha Doc. Transporte
	$cDoitra = $vDatDo['tradesxx']; //Transportadora
	$cCodEmb = $vDatDo['temidxxx']; //Codigo Embalaje
	###Fin Trayendo Datos de Do Dependiendo del Tipo de Operacion ##

  ##Consulto en la SIAI0150 Datos del Facturado A: ##
	$qCliFac  = "SELECT ";
	$qCliFac .= "$cAlfa.SIAI0150.CLIIDXXX,";
  $qCliFac .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\" SIN NOMBRE\") AS CLINOMXX, ";
	$qCliFac .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX,";
	$qCliFac .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX,";
	$qCliFac .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX,";
	$qCliFac .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX,";
	$qCliFac .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX ";
	$qCliFac .= "FROM $cAlfa.SIAI0150 ";
	$qCliFac .= "WHERE ";
	$qCliFac .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$_POST['cTerIdInt']}\" AND ";
	$qCliFac .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
	$xCliFac  = f_MySql("SELECT","",$qCliFac,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qCliDat."~".mysql_num_rows($xCliFac));
	if (mysql_num_rows($xCliFac) > 0) {
		$vCliFac = mysql_fetch_array($xCliFac);
	}
  ##Consulto en la SIAI0150 Datos del Facturado A: ##

  ##Traigo Ciudad del Facturado A ##
	$qCiuFac  = "SELECT * ";
	$qCiuFac .= "FROM $cAlfa.SIAI0055 ";
	$qCiuFac .= "WHERE ";
	$qCiuFac .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCliFac['PAIIDXXX']}\" AND ";
	$qCiuFac .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCliFac['DEPIDXXX']}\" AND ";
	$qCiuFac .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCliFac['CIUIDXXX']}\" AND ";
	$qCiuFac .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" LIMIT 0,1";
	$xCiuFac  = f_MySql("SELECT","",$qCiuFac,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qCiuFac."~".mysql_num_rows($xCiuFac));
	if (mysql_num_rows($xCiuFac) > 0) {
		$vCiuFac = mysql_fetch_array($xCiuFac);
  }
	##Fin Traigo Ciudad del Facturado A ##

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

  ##TRAIGO DATOS DEL CLIENTE POR CUENTA DE
  $qCliPcd  = "SELECT ";
	$qCliPcd .= "$cAlfa.SIAI0150.CLIIDXXX,";
  $qCliPcd .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\" SIN NOMBRE\") AS CLINOMXX, ";
	$qCliPcd .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX,";
	$qCliPcd .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX,";
	$qCliPcd .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX,";
	$qCliPcd .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX,";
	$qCliPcd .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX ";
	$qCliPcd .= "FROM $cAlfa.SIAI0150 ";
	$qCliPcd .= "WHERE ";
	$qCliPcd .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$_POST['cTerId']}\" AND ";
	$qCliPcd .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
	$xCliPcd  = f_MySql("SELECT","",$qCliPcd,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qCliDat."~".mysql_num_rows($xCliPcd));
	if (mysql_num_rows($xCliPcd) > 0) {
		$vCliPcd = mysql_fetch_array($xCliPcd);
  }

	$qUsrDat  = "SELECT ";
	$qUsrDat .= "USRNOMXX  ";
	$qUsrDat .= "FROM $cAlfa.SIAI0003 ";
	$qUsrDat .= "WHERE ";
	$qUsrDat .= "USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1 ";
	$xUsrDat  = f_MySql("SELECT","",$qUsrDat,$xConexion01,"");
	$nFilUsr  = mysql_num_rows($xUsrDat);
	if ($nFilUsr > 0) {
	  $vUsrDat = mysql_fetch_array($xUsrDat);
	}
	//f_Mensaje(__FILE__,__LINE__,$qUsrDat);

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

  ##Traigo la Forma de Pago##
  $cFormaPag = "";
  if (isset($_POST['cComFpag'])) {
    if ($_POST['cComFpag'] == "1") {
      $cFormaPag = "CREDITO";

    } elseif($_POST['cComFpag'] == "2") {
      $cFormaPag = "CONTADO";
    }
  }
  ##FIN Traigo la Forma de Pago##

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

  ##Codigo para imprimir los ingresos para terceros ##
  $mAgrupaxConcepto = array();
  for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
    $mComObs_IP = stripos($_POST['cComObs_PCCA'.($i+1)], "[");
  
    $nSwitch_Find = 0;

    if (in_array("{$_POST['cComId_PCCA'.($i+1)]}~{$_POST['cPucId_PCCA'.($i+1)]}", $vComImp) == false) {
      for ($j=0;$j<count($mAgrupaxConcepto);$j++) {
        if ($_POST['cComId_PCCA'.($i+1)] == $mAgrupaxConcepto[$j]['cComId'] && $_POST['cComCsc3_PCCA'.($i+1)] == $mAgrupaxConcepto[$j]['ccomcsc3'] && $_POST['cTerId2_PCCA'.($i+1)] == $mAgrupaxConcepto[$j]['cterid2']) {
          $nSwitch_Find = 1;
          // F_Mensaje(__FILE__,__LINE__,"Nuevo Concepto~".$mAgrupaxConcepto[$j]['cComId']);
          $mAgrupaxConcepto[$j]['cComCsc3'] .= ((strlen($mAgrupaxConcepto[$nInd_mAgrupaxConcepto]['cComCsc3'])+strlen("/".$_POST['cComCsc3_PCCA'.($i+1)])) <= 40) ? "/".$_POST['cComCsc3_PCCA'.($i+1)] : "";
          $mAgrupaxConcepto[$j]['nComVlr']   += $_POST['nComVlr_PCCA'.($i+1)];
          $mAgrupaxConcepto[$j]['nBaseIva']  += $_POST['nBaseIva_PCCA'.($i+1)];
          $mAgrupaxConcepto[$j]['nVlrIva']   += $_POST['nVlrIva_PCCA'.($i+1)];
        }
      }
    }

    if ($nSwitch_Find == 0) { // No lo encontro en la matriz para pintar en la factura
      // F_Mensaje(__FILE__,__LINE__,"Concepto Repetido~".$_POST['cComId_PCCA'  .($i+1)]);
      $nInd_mAgrupaxConcepto = count($mAgrupaxConcepto);
      $mAgrupaxConcepto[$nInd_mAgrupaxConcepto]['cComId']   = $_POST['cComId_PCCA'  .($i+1)];
      $mAgrupaxConcepto[$nInd_mAgrupaxConcepto]['cComObs']  = $_POST['cComObs_PCCA' .($i+1)];
      $mAgrupaxConcepto[$nInd_mAgrupaxConcepto]['ccomcsc3'] = $_POST['cComCsc3_PCCA'.($i+1)];
      $mAgrupaxConcepto[$nInd_mAgrupaxConcepto]['cterid2']  = $_POST['cTerId2_PCCA'.($i+1)];
      $mAgrupaxConcepto[$nInd_mAgrupaxConcepto]['nComVlr']  = $_POST['nComVlr_PCCA' .($i+1)];
      $mAgrupaxConcepto[$nInd_mAgrupaxConcepto]['nBaseIva'] = $_POST['nBaseIva_PCCA'.($i+1)];
      $mAgrupaxConcepto[$nInd_mAgrupaxConcepto]['nVlrIva']  = $_POST['nVlrIva_PCCA'.($i+1)];
    }
  }//for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
  ##Fin Codigo para imprimir los ingresos para terceros ##

  #Agrupo Ingresos Propios
  for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
    $vDatosIp = array();
    $cObs = "|".$_POST['cSerId_IPA'.($i+1)]."~".$_POST['cFcoId_IPA'.($i+1)]."~".$_POST['cComObs_IPA'.($i+1)]."|";;
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
  $mDatIP = array();
  foreach ($mIP as $cKey => $mValores) {
    $mDatIP[] = $mValores;
  }
  #Fin Agrupo Ingresos Propios

  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
  class PDF extends FPDF {

    function Header() {        
      global $cAlfa;   global $cRoot;  global $cPlesk_Skin_Directory; global $vSysStr; global $_COOKIE;
      global $vResDat; global $cDocId; global $cDocSuf; global $vCliFac; global $vCliPcd; global $cComCsc;
      global $dFecEmi; global $dHorEmi; global $dFecVen; global $vDceDat; global $vCiuFac; global $cDoitra;
      global $cFecTra; global $cBultos; global $cCodEmb; global $cPesBru; global $cDo; global $cDocTra;
      global $cFormaPag; global $cPedido; global $vAgeDat;

      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,180,180);
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,180,180);

      $posy	= 8;
      $posx	= 8;
      //logo
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg', 15, 10, 25);
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobereauveritas.png', 170, 13, 38, 16);

      //Nombre de la agencia
      $this->SetFont('arial','B',8);
      $this->setXY($posx+45,$posy);
      $this->Cell(110,4,utf8_decode("AGENCIA DE ADUANAS ADUAMAR DE COLOMBIA CIA. S.A.S NIVEL 1"),0,0,'C');
      $this->Ln(5);
      $this->setX($posx+45);
      $this->Cell(110,4,"IMPORTACIONES Y EXPORTACIONES",0,0,'C');

      //Direccion
      $posy += 10;
      $this->SetFont('arial','',8);
      $this->setXY($posx+45,$posy);
      $this->Cell(110,4,$vAgeDat["CLIDIRXX"] . utf8_decode(", Teléfono: ") . $vAgeDat["CLITELXX"],0,0,'C');

      //Resolucion DIAN 
      //Traigo numero de Meses entre Desde y Hasta
			$dFechaInicial = date_create($vResDat['resfdexx']);
			$dFechaFinal = date_create($vResDat['resfhaxx']);
			$nDiferencia = date_diff($dFechaInicial, $dFechaFinal);
      $nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m;
      
      $posy += 6;
      $this->setXY($posx, $posy);
      $cResolucion  = utf8_decode("Autorización de Facturación Electrónica N° ").$vResDat['residxxx']." ";
      $cResolucion .= " - Fecha: ".substr($vResDat['resfdexx'], 0, 4)."/".substr($vResDat['resfdexx'], 5, 2)."/".substr($vResDat['resfdexx'], 8, 2)." ";
      $this->Cell(197, 3, $cResolucion, 0, 0, 'C');
      $this->Ln(4);
      $this->setX($posx);
      $this->Cell(197, 3,  utf8_decode("Vigencia: ". $nMesesVigencia ." Meses - Numeradas del ".$vResDat['resprexx'].$vResDat['resdesxx']." al ".$vResDat['resprexx'].$vResDat['reshasxx']), 0, 0, 'C');

      //NIT
      $posy += 10;
      $this->setXY($posx+45, $posy);
      $cNitAduana  = number_format($vSysStr['financiero_nit_agencia_aduanas'], 0, '', '.')."-";
      $cNitAduana .= f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']);
      $this->Cell(110, 3, utf8_decode("NIT: ".$cNitAduana),0,0,'C');
      $this->Ln(4);
      $this->setX($posx+45);
      $this->Cell(110, 3, "I.V.A REGIMEN COMUN",0,0,'C');
      $this->Ln(4);
      $this->setX($posx+45);
      $this->Cell(110, 3, utf8_decode("Actividad Económica ICA 5229 - Tarifa 9.66 X 1.000"),0,0,'C');

      $posy += 8;
      //Fecha Elaboracion
      $this->setXY($posx, $posy);
      $this->SetFont('arial', 'B', 7);
      $this->Cell(30, 3, utf8_decode("FECHA ELABORACIÓN"),0,0,'L');
      $this->Ln(5);
      $this->setX($posx);
      $this->SetFont('arial', '', 7);
      $this->Cell(9, 4, substr($dFecEmi, 8, 2), 0, 0, 'C');
      $this->Cell(9, 4, substr($dFecEmi, 5, 2), 0, 0, 'C');
      $this->Cell(11, 4, substr($dFecEmi, 0, 4), 0, 0, 'C');

      //Hora Generacion
      $this->setXY($posx+31, $posy);
      $this->SetFont('arial', 'B', 7);
      $this->Cell(30, 3, utf8_decode("HORA GENERACIÓN"),0,0,'L');
      $this->Ln(5);
      $this->setX($posx+30);
      $this->SetFont('arial', '', 7);
      $this->Cell(9, 4, substr($dHorEmi, 0, 2), 0, 0, 'C');
      $this->Cell(9, 4, substr($dHorEmi, 3, 2), 0, 0, 'C');
      $this->Cell(10, 4, substr($dHorEmi, 6, 2), 0, 0, 'C');

      $this->Line($posx, $posy+4, $posx+59, $posy+4);
      $this->Line($posx+9, $posy+4, $posx+9, $posy+11);
      $this->Line($posx+18, $posy+4, $posx+18, $posy+11);
      $this->Line($posx+30, $posy-1, $posx+30, $posy+11);
      $this->Line($posx+39, $posy+4, $posx+39, $posy+11);
      $this->Line($posx+48, $posy+4, $posx+48, $posy+11);
      $this->Rect($posx, $posy-1, 59, 12);

      //NUMERO DE FACTURA
      $this->setXY($posx+168, $posy-2);
      $this->SetFont('arial', 'B', 7);
      $this->MultiCell(30, 3, "FACTURA DE VENTA ELECTRONICA",0,'C');
      $this->Ln(1);
      $this->setX($posx+166);
      $this->setTextColor(255,0,0);
      $this->SetFont('arial', '', 8);
      $this->Cell(38, 5, $vResDat['resprexx'].$cComCsc,0,0,'C');
      $this->setTextColor(0);
      $this->Line($posx+166, $posy+4, $posx+200, $posy+4);
      $this->Rect($posx+166, $posy-3, 34, 14);

      //Datos Facturar A
      $posy += 12;
      $posyIni = $posy;
      $this->setXY($posx, $posy);
      $this->SetFont('arial', 'B', 7);
      $this->Cell(30, 3, "CLIENTE",0,0,'L');
      $this->setXY($posx+19, $posy);
      $this->SetFont('arial', '', 7);
      $this->MultiCell(80, 3.5, utf8_decode($vCliFac['CLINOMXX']),0,'L');
      $posyfin = $this->getY()+0.5;
      
      $this->setXY($posx+100, $posy);
      $this->SetFont('arial', 'B', 7);
      $this->Cell(25, 3, "FORMA DE PAGO",0,0,'C');
      $this->SetFont('arial', '', 7);
      $this->Cell(20, 3, $cFormaPag,0,0,'L');
      $this->setXY($posx+147, $posy);
      $this->SetFont('arial', 'B', 7);
      $this->Cell(36, 3, "FECHA DE VENCIMIENTO",0,0,'C');
      $this->SetFont('arial', '', 7);
      $this->Cell(20, 3, $dFecVen,0,0,'L');

      $this->Line($posx+18, $posy-1, $posx+18, $posyfin);
      $this->Line($posx+100, $posy-1, $posx+100, $posyfin);
      $this->Line($posx+125, $posy-1, $posx+125, $posyfin);
      $this->Line($posx+147, $posy-1, $posx+147, $posyfin);
      $this->Line($posx+182, $posy-1, $posx+182, $posyfin);

      $this->Line($posx, $posyfin, $posx+200, $posyfin);
      $posy = $posyfin;
      $this->setXY($posx,$posy);
      $this->SetFont('arial', 'B', 7);
      $this->Cell(30, 4, "TELEFONO",0,0,'L');
      $this->setX($posx+23);
      $this->Cell(30, 4, utf8_decode("DIRECCIÓN"),0,0,'L');
      $this->setX($posx+45);
      $this->SetFont('arial', '', 7);
      $this->Cell(30, 4, substr($vCliFac['CLIDIRXX'], 0, 57),0,0,'L');
      $this->setX($posx+149);
      $this->SetFont('arial', 'B', 7);
      $this->Cell(30, 4, "NIT",0,0,'L');
      $this->Ln(5);
      $this->setX($posx);
      $this->SetFont('arial', '', 7);
      $this->Cell(30, 4, $vCliFac['CLITELXX'],0,0,'L');
      $this->setX($posx+23);
      $this->SetFont('arial', 'B', 7);
      $this->Cell(30, 4, "CIUDAD",0,0,'L');
      $this->setX($posx+45);
      $this->SetFont('arial', '', 7);
      $this->Cell(30, 4, substr($vCiuFac['CIUDESXX'], 0, 58),0,0,'L');
      $this->setX($posx+149);
      $this->Cell(30, 4, number_format($vCliFac['CLIIDXXX'], 0, '', '.'). "-" .f_Digito_Verificacion($vCliFac['CLIIDXXX']), 0, 0, 'L');
      $this->Ln(4.5);
      
      $this->Line($posx+23, $posyfin, $posx+23, $this->getY());
      $this->Line($posx+45, $posyfin, $posx+45, $this->getY());
      $this->Line($posx+147, $posyfin, $posx+147, $this->getY());
      $this->Line($posx, $posyfin+5, $posx+200, $posyfin+5);
      $this->Rect($posx, $posyIni-1, 200, $this->getY() - ($posyIni-1));

      $posy = $this->getY()+1;
      $this->setXY($posx, $posy);
      $this->SetFont('arial', '', 6);  
      $this->Cell(190, 3, utf8_decode("NOS PERMITIMOS DETALLAR LA LIQUIDACIÓN DE LOS TRIBUTOS ADUANEROS Y DEMAS GASTOS SOBRE LAS MERCANCIAS LLEGADAS A NUESTRA CONSIGNACIÓN"),0,0,'C');

      //Datos Generales del DO
      $posy += 7;
      $posyIni = $posy;
      $this->setXY($posx+7, $posy);
      $this->SetFont('arial', 'B', 7);
      $this->Cell(30, 3, "VAPOR Y/O EMPRESA AREA",0,0,'C');
      $this->setX($posx+50);
      $this->Cell(20, 3, "FECHA",0,0,'C');
      $this->setX($posx+75);
      $this->Cell(20, 3, "BULTOS",0,0,'C');
      $this->setX($posx+95);
      $this->Cell(20, 3, "DE",0,0,'C');
      $this->setX($posx+120);
      $this->Cell(20, 3, "KILOS BRUTOS",0,0,'C');
      $this->setX($posx+149);
      $this->Cell(20, 3, "LICENCIA No.",0,0,'C');
      $this->setX($posx+175);
      $this->Cell(20, 3, "FECHA",0,0,'C');

      $posy += 4;
      $this->setXY($posx, $posy);
      $this->SetFont('arial', '', 7);
      $this->MultiCell(45, 3, substr(utf8_decode($cDoitra), 0, 52),0,'L');
      $this->setXY($posx+50, $posy);
      $this->Cell(20, 3, $cFecTra,0,0,'C');
      $this->setXY($posx+75, $posy);
      $this->Cell(20, 3, number_format($cBultos, 0),0,0,'C');
      $this->setXY($posx+95, $posy);
      $this->Cell(20, 3, $cCodEmb,0,0,'C');
      $this->setXY($posx+120, $posy);
      $this->Cell(20, 3, number_format($cPesBru, 0),0,0,'C');
      $this->setXY($posx+149, $posy);
      $this->Cell(20, 3, "",0,0,'C');
      $this->setXY($posx+175, $posy);
      $this->Cell(20, 3, "",0,0,'C'); 

      $posy += 7;
      $this->setXY($posx+12, $posy);
      $this->SetFont('arial', 'B', 7);
      $this->Cell(20, 3, "D.O No.",0,0,'C');
      $this->setXY($posx+85, $posy);
      $this->Cell(20, 3, "PEDIDO No.",0,0,'C');
      $this->setXY($posx+163, $posy);
      $this->Cell(20, 3, "B/L - AWB No.",0,0,'C');

      $posy += 4;
      $this->setXY($posx, $posy);
      $this->SetFont('arial', '', 7);
      $this->Cell(45, 3, $cDocId,0,0,'C');
      $this->setXY($posx+50, $posy);
      $this->Cell(95, 3, $cPedido,0,0,'C');
      $this->setXY($posx+147, $posy);
      $this->Cell(50, 3, $cDocTra,0,0,'C');
      $this->setXY($posx+50, $posy);
      $this->MultiCell(93, 3, utf8_decode($vCocDat['comobsxx']),0,'L');
      $posyFin = $this->getY();

      $this->Line($posx, $posyIni+3, $posx+200, $posyIni+3);
      $this->Line($posx, $posyIni+10, $posx+200, $posyIni+10);
      $this->Line($posx, $posyIni+14, $posx+200, $posyIni+14);

      $this->Line($posx+48, $posyIni-1, $posx+48, $posyFin+1);
      $this->Line($posx+73, $posyIni-1, $posx+73, $posyIni+10);
      $this->Line($posx+95, $posyIni-1, $posx+95, $posyIni+10);
      $this->Line($posx+115, $posyIni-1, $posx+115, $posyIni+10);
      $this->Line($posx+146, $posyIni-1, $posx+146, $posyFin+1);
      $this->Line($posx+172, $posyIni-1, $posx+172, $posyIni+10);
      $this->Rect($posx, $posyIni-1, 200, $posyFin - ($posyIni-2));

      $this->Ln(4);
    }//Function Header
    
    function Footer(){
      $posx	= 8;
      $posy = 215;

      $textFooter_1  = "Esta factura se considera irrevocablemente aceptada por el comprador o beneficiario del servicio si no manifiesta expresamente rechazo de la factura ";
      $textFooter_1 .= "mediante reclamo escrito dirigido al emisor dentro de los tres (3) días hábiles siguientes a su recepción";      
      $this->setXY($posx,$posy+1);
      $this->SetFont('arial','',8);
      $this->MultiCell(195,3.5,utf8_decode($textFooter_1),0,'L');
      $this->Rect($posx, $posy, 200, 10);

      //Recuadro de las Tarifas
      $posy += 11;
      $this->Rect($posx, $posy-1, 105, 14);

      ## TOTALES ##
      $this->setXY($posx+148,$posy+3);
      $this->SetFont('arial','B',8);
      $this->Cell(20,4,"ANTICIPO",0,0,'R');
      $this->Ln(5);
      $this->setX($posx+148);
      $this->setTextColor(100);
      $this->Cell(20,4,"SUB TOTAL",0,0,'R');
      $this->Ln(5);
      $this->setX($posx+148);
      $this->Cell(20,4,"I.V.A.",0,0,'R');
      $this->Ln(5);
      $this->setX($posx+148);
      $this->Cell(20,4,"RETEIVA",0,0,'R');
      $this->Ln(5);
      $this->setX($posx+148);
      $this->Cell(20,4,"RETEICA",0,0,'R');
      $this->Ln(5);
      $this->setX($posx+148);
      $this->Cell(20,4,"RETEFUENTE",0,0,'R');
      $this->Ln(5);
      $this->setX($posx+148);
      $this->Cell(20,4,"TOTAL",0,0,'R');

      $textFooter_2  = "ESTA FACTURA DE VENTA SE ASIMILIA EN SUS DEFECTOS A LA LETRA DE CAMBIO, ART. 774 NUMERAL 6 DEL CODIGO DE COMERCIO, Si la Factura no es cancelada en la fecha de vencimiento, ";
      $textFooter_2 .= "se cobrará intereses de mora a la tasa máxima legal permitida por la Superintendencia Bancaria. Los firmantes en aceptada declaran haber recibido los servicios de arriba mencionados y de manera ";
      $textFooter_2 .= "satisfactoria, ser el representante legal de la empresa o intictución o estar legalmente autorizado para recibir y firmar este documento, por lo tanto se da por aceptada por parte del comprador. ";
      $textFooter_2 .= "La aceptación de esta Factura de Venta da por aceptada las condiciones de la cotización presentada por ADUAMAR DE COLOMBIA, al Cliente.";
      $posy += 14;      
      $this->setXY($posx,$posy);
      $this->setTextColor(100);
      $this->SetFont('arial','',6);
      $this->MultiCell(100,3,utf8_decode($textFooter_2),0,'L');
      $this->setTextColor(0);
      $this->Rect($posx, $posy-1, 105, 25);

      $posy += 25;
      $this->setXY($posx,$posy);
      $this->SetFont('arial','',7);
      $this->Cell(14,5,"CUFE: ",1,0,'L');
      $this->setX($posx+15);
      $this->Cell(130,5,"",1,0,'L');

      $posy += 5;
      $this->setXY($posx,$posy);
      $this->SetFont('arial','',5);
      $this->Cell(30,3,"FECHA Y HORA VALIDACION DIAN: ",0,0,'L');

      $posy += 2;
      $this->setXY($posx+100,$posy);
      $this->SetFont('arial','B',8);
      $this->Cell(30,5,$cNomCopia,0,0,'C');
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
  }

  $pdf=new PDF('P','mm','Letter');
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->SetFont('verdana','',8);
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);

  $pdf->AddPage();
  $nPagina = 1;

  $posy	= $pdf->GetY()+3; 
  $posx	= 8;
  $posfin = 205;
  $posRect = $posy-2;
  //Se imprimen los Ingresos por Terceros

  ## Impresion de Titulos
  $pdf->SetFont('arial','B',9);
  $pdf->setXY($posx,$posy);
  $pdf->Cell(20,5,utf8_decode("CÓDIGO"),0,0,'C');
  $pdf->Cell(128,5,utf8_decode("DESCRIPCIÓN"),0,0,'C');
  $pdf->Cell(20,5,"CANTIDAD",0,0,'C');
  $pdf->Cell(30,5,"VALOR",0,0,'C');
  $py = $posy+8;

  if(count($mAgrupaxConcepto) > 0){
    //Se imprimen los Ingresos por Terceros
    $pdf->SetFont('arial','',9);
    $pdf->setXY($posx,$py);
    $pdf->Cell(140,3,"** PAGOS A TERCEROS **  (NO GRAVABLES)",0,0,'L');
    $TotPcc = 0; $ToPcc  = 0; $TvVlr = 0;
    $py += 5;

    $pdf->SetWidths(array(20,128,20,30));
    $pdf->SetAligns(array("C","L","C","R"));
    $pdf->setXY($posx,$py);

    for ($i=0;$i<count($mAgrupaxConcepto);$i++) {
      if($py > $posfin){
        $pdf->Rect($posx,$posRect,200,215-$posRect);
        $pdf->AddPage();
        $nPagina++; 
        $py = $posy;
      }

      $TotPcc += $mAgrupaxConcepto[$i]['nComVlr'];

      $cComObs_PCCA = explode("^", $mAgrupaxConcepto[$i]['cComObs']);
      if (substr_count($cComObs_PCCA[0],"DIAN") > 0 ) { // Encontre la palabra DIAN de pago de impuestos.
        $cObs = "TRIBUTOS ADUANEROS";
      }else{
        // $cCodFactura = (trim($mAgrupaxConcepto[$i]['ccomcsc3']) != "") ? "FRA. ".$mAgrupaxConcepto[$i]['ccomcsc3'] : "";
        // $cObs = $cComObs_PCCA[0]." ".$cCodFactura." ".$cComObs_PCCA[1];
        $cObs = $cComObs_PCCA[0];
      }

      $pdf->SetFont('arial','',9);
      $pdf->setX($posx);
      $pdf->Row(array(trim($mAgrupaxConcepto[$i]['cComId']),
                      trim($cObs), 
                      "1", 
                      number_format($mAgrupaxConcepto[$i]['nComVlr'],0,',','.')));
      $py += 4;
    }

    if($py > $posfin){
      if($py > $posfin){
        $pdf->Rect($posx,$posRect,200,215-$posRect);
        $pdf->AddPage();
        $nPagina++; 
        $py = $posy;
      }
    }
    $TotPcc = $TotPcc + $To4xMil;
  }else{
    $py = $pdf->GetY()+6;
  }

  $TotalIP = 0;
  if (count($mDatIP) > 0) {
    //Ingresos Propios
    $py += 5;
    $pdf->SetFont('arial','',9);
    $pdf->setXY($posx,$py);
    $pdf->Cell(30,3,"** SERVICIOS ADUAMAR DE COLOMBIA **  (GRAVABLES)",0,0,'L');
    $py += 5;

    $pdf->SetWidths(array(20,128,20,30));
    $pdf->SetAligns(array("C","L","C","R"));
    $pdf->setXY($posx,$py);

    ##Imprimo Ingresos Propios##
    for ($k=0;$k<count($mDatIP);$k++) {
      if($py > $posfin){
        $pdf->Rect($posx,$posRect,200,215-$posRect);
        $pdf->AddPage();
        $nPagina++; 
        $py = $posy;
      }

      $TotalIP += $mDatIP[$k]['comvlrxx'];

      $pdf->SetFont('arial','',9);
      $pdf->setX($posx);
      $pdf->Row(array(trim($mDatIP[$k]['ctoidxxx']),
                      trim($mDatIP[$k]['comobsxx']), 
                      $mDatIP[$k]['canfexxx'], 
                      number_format($mDatIP[$k]['comvlrxx'],0,',','.')));
      $py += 4;
    }//for ($k=0;$k<count($mCodDat);$k++) {
    ##Fin Imprimo Ingresos Propios##
  }
  
  if($py > $posfin){
    if($py > $posfin){
      $pdf->Rect($posx,$posRect,200,215-$posRect);
      $pdf->AddPage();
      $nPagina++; 
      $py = $posy;
    }
  }
  
  ##Bloque que acumula retenciones por valor de porcentaje##
  ## RRETEFUENTE ##
  $mRetFte = array();
  $nTotRfte = 0;
  for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
    $nTotRfte += $_POST['nVlrFte_IPA'.($i+1)];
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
  }

  ## RETEICA ##
  $mRetIca = array();
  $nTotIca = 0;
  for ($i=0;$i<$_POST['nSecuencia_IPA'];$i++) {
    $nTotIca += $_POST['nVlrIca_IPA'.($i+1)];
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
  }

  ## RETEIVA ##
  $mRetIva = array();
  $nTotIva = 0;
  for ($i=0;$i<$_POST['nSecuencia_IPA'];$i++) {
    $nTotIva += $_POST['nVlrIva_IPA'.($i+1)];
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
  }
  ##Fin Bloque que acumula retenciones por valor de porcentaje##

  $py += 7;
  if($py > $posfin){
    $pdf->Rect($posx,$posRect,200,215-$posRect);
    $pdf->AddPage();
    $nPagina++; 
    $py = $posy;
  }

  $py = $pdf->GetY()+5;
  $pdf->Rect($posx,$posRect,200,215-$posRect);

  ### Imprimo Valores Retenidos ##
  $posx	= 8;
  $posy = 225;

  $pdf->SetFont('arial','',6);
  for ($k=0;$k<count($mRetIva);$k++) {
    if($mRetIva[$k]['tipretxx'] == 'IVA'){
      $pdf->setXY($posx,$posy);
      $pdf->Cell(35,2.5,"Tarifa: ".$mRetIva[$k]['pucretxx']."% - Valor Base: ".$mRetIva[$k]['basexxxx']." - Valor Retenido: ".$mRetIva[$k]['comvlrxx'],0,0,'L');
      $posy += 2.5;
    }
  }

  for ($k=0;$k<count($mRetIca);$k++) {
    if($mRetIca[$k]['tipretxx'] == 'ICA'){
      $pdf->setXY($posx,$posy);
      $pdf->Cell(35,2.5,"Tarifa: ".$mRetIca[$k]['pucretxx']."% - Valor Base: ".$mRetIca[$k]['basexxxx']." - Valor Retenido: ".$mRetIca[$k]['comvlrxx'],0,0,'L');
      $posy += 2.5;
    }
  }

  for ($k=0;$k<count($mRetFte);$k++) {
    if($mRetFte[$k]['tipretxx'] == 'FUENTE'){
      $pdf->setXY($posx,$posy);
      $pdf->Cell(35,2.5,"Tarifa: ".$mRetFte[$k]['pucretxx']."% - Valor Base: ".$mRetFte[$k]['basexxxx']." - Valor Retenido: ".$mRetFte[$k]['comvlrxx'],0,0,'L');
      $posy += 2.5;
      $nTotFte += $mRetFte[$k]['comvlrxx'];
    }
  }

  #### SubTotales ####
  $nSubTot = $TotPcc + $TotalIP;

  /** Imprimo Saldo **/
  $cNegativo = "";
  if ($_POST['nIPASal'] > 0) {
    $cNegativo = "";
  } else {
    $cNegativo = "-";
  }
  $nTotPag = abs($_POST['nIPASal']);

  if($cNegativo == "-") {
    $nAnticipoRecibido = abs($_POST['nIPAAnt']);
    $nSaldoFavor = $nTotPag;
    $_POST['nIPAAnt'] = abs($_POST['nIPAAnt']) + ($nTotPag*-1);
    $nTotPag = 0;

    $pdf->SetFont('arial','B',6);
    $posy += 1;
    $pdf->setXY($posx,$posy);
    $pdf->Cell(35,2.5,"Anticipo Total Recibido: $ ".number_format($nAnticipoRecibido, 0, ',', '.')."   Saldo a favor del cliente: $ ".number_format($nSaldoFavor, 0, ',', '.'),0,0,'L');
    $posy += 2.5;
  }

  $posy = 229;
  //Anticipo
  $pdf->SetFont('arial','',8);
  $pdf->setXY($posx+158,$posy);
  $pdf->Cell(40,4,number_format($_POST['nIPAAnt'], 2, ',', '.'),0,0,'R'); 
  //SubTotal
  $pdf->setXY($posx+158,$posy+5);
  $pdf->Cell(40,4,($nSubTot < 0 ? "-": "").number_format($nSubTot, 2, ',', '.'),0,0,'R');
  //I.V.A
  $pdf->setXY($posx+158,$posy+10);
  $pdf->Cell(40,4,number_format(($_POST['nIPAIva']), 2, ',', '.'),0,0,'R');
  //ReteIva
  $pdf->setXY($posx+158,$posy+15);
  $pdf->Cell(40,4,number_format($nTotIva, 2, ',', '.'),0,0,'R');
  //ReteIca
  $pdf->setXY($posx+158,$posy+20);
  $pdf->Cell(40,4,number_format($nTotIca, 2, ',', '.'),0,0,'R');
  //ReteFuente
  $pdf->setXY($posx+158,$posy+25);
  $pdf->Cell(40,4,number_format($nTotFte, 2, ',', '.'),0,0,'R');
  //Total
  $pdf->SetFont('arial','B',8);
  $pdf->setXY($posx+158,$posy+30);
  $pdf->Cell(40,4,number_format($nTotPag, 2, ',', '.'),0,0,'R');

  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	$pdf->Output($cFile);

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }

	echo "<html><script>document.location='$cFile';</script></html>";
?>

<?php
  /**
	 * Imprime Factura de Venta SOLUCIONES ADUANERAS.
	 * --- Descripcion: Permite Imprimir Factura de Venta SOLUCIONES ADUANERAS por Vista Previa.
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
        if ($_POST['cComId_PCCA'.($i+1)] == $mAgrupaxConcepto[$j]['cComId']) {
          $nSwitch_Find = 1;
          // F_Mensaje(__FILE__,__LINE__,"Nuevo Concepto~".$mAgrupaxConcepto[$j]['cComId']);

          // Si el PCC es generado desde un Egreso (G) se envia el Doc. Inf. digitado en el comprobante.
          if ($_POST['cComId3_PCCA'.($i+1)] == "G") {
            $mAgrupaxConcepto[$j]['cComCsc3'] .= ((strlen($mAgrupaxConcepto[$j]['cComCsc3'])+strlen("/".$_POST['cComDocIn_PCCA'.($i+1)])) <= 40) ? "/".$_POST['cComDocIn_PCCA'.($i+1)] : "";
          } else {
            $mAgrupaxConcepto[$j]['cComCsc3'] .= ((strlen($mAgrupaxConcepto[$j]['cComCsc3'])+strlen("/".$_POST['cComCsc3_PCCA'.($i+1)])) <= 40) ? "/".$_POST['cComCsc3_PCCA'.($i+1)] : "";
          }

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

      // Si el PCC es generado desde un Egreso (G) se envia el Doc. Inf. digitado en el comprobante.
      if ($_POST['cComId3_PCCA'.($i+1)] == "G") {
        $mAgrupaxConcepto[$nInd_mAgrupaxConcepto]['cComCsc3'] = $_POST['cComDocIn_PCCA'.($i+1)];
      } else {
        $mAgrupaxConcepto[$nInd_mAgrupaxConcepto]['cComCsc3'] = $_POST['cComCsc3_PCCA'.($i+1)];
      }

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

    $vFactGasto = explode("MONEDA",trim($_POST['cMisc_IPA'.($i+1)]));

    $mIP[$_POST['cComId_IPA'.($i+1)]]['ctoidxxx']  = $_POST['cComId_IPA'.($i+1)];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['comobsxx']  = $vDatosIp[0];
    $mIP[$_POST['cComId_IPA'.($i+1)]]['cfactgas']  = $vFactGasto[0];
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
  }//for ($k=0;$k<count($_POST['nSecuencia_IPA']);$k++) {

  $mDatIP = array();
  foreach ($mIP as $cKey => $mValores) {
    $mDatIP[] = $mValores;
  }
  #Fin Agrupo Ingresos Propios

  ##Traigo la Forma de Pago##
  $cFormaPag = "";
  if ($_POST['cComFpag'] != "") {
    //Buscando descripcion
    $cFormaPag = ($_POST['cComFpag'] == 1) ? "CONTADO" : "CREDITO";
  }

  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
  class PDF extends FPDF {

    function Header() {        
      global $cAlfa;   global $cRoot;   global $cPlesk_Skin_Directory; global $vSysStr; global $_COOKIE;
      global $vResDat; global $cDocId;  global $vCliFac;  global $cComCsc; global $dFecEmi; global $cDocTra;
      global $dHorEmi; global $dFecVen; global $vCiuFac;  global $cBultos; global $cPesBru; global $cFormaPag; 

      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,180,180);
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,180,180);

      $posx = 10;
      $posy = 10;
      //logo
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg', $posx+1, $posy, 50);

      //Nombre de la agencia
      $this->setXY($posx, $posy);
      $this->SetFont('Arial', 'B', 9);
      $this->Cell(183, 3, utf8_decode("AGENCIA DE ADUANAS SOLUCIONES ADUANERAS S.A.S NIVEL 2"), 0, 0, 'C');
      $this->ln(4);
      $this->setX($posx);
      $this->SetFont('Arial', '', 7);
      $cNitAduana  = number_format($vSysStr['financiero_nit_agencia_aduanas'], 0, '', '.')."-";
      $cNitAduana .= f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']);
      $this->Cell(195, 3, "NIT. " . $cNitAduana, 0, 0, 'C');
      $this->ln(4);
      $this->setX($posx);
      $this->Cell(195, 3, utf8_decode("CL 43 39 39BRR EL ROSARIO"), 0, 0, 'C');
      $this->ln(4);
      $this->setX($posx);
      $this->Cell(195, 3, "3133900", 0, 0, 'C');
      $this->ln(4);
      $this->setX($posx);
      $this->Cell(195, 3, "Barranquilla, Colombia", 0, 0, 'C');

      //Numero de la factura
      $this->setXY($posx + 145, $posy);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(50, 6, utf8_decode("FACTURA DE VENTA ELECTRÓNICA"), 1, 0, 'C');
      $this->ln(6);
      $this->setX($posx + 145);
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(50, 9, $vResDat['resprexx']." ".$cComCsc, 1, 0, 'C');

      //Resolucion DIAN 
      //Traigo numero de Meses entre Desde y Hasta
			$dFechaInicial = date_create($vResDat['resfdexx']);
			$dFechaFinal = date_create($vResDat['resfhaxx']);
			$nDiferencia = date_diff($dFechaInicial, $dFechaFinal);
      $nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m;

      $cResolucion  = "Resolución de Facturación Electrónica No. ".$vResDat['residxxx'];
      $cResolucion .= " del ".substr($vResDat['resfdexx'], 0, 4)."/".substr($vResDat['resfdexx'], 5, 2)."/".substr($vResDat['resfdexx'], 8, 2);
      $cResolucion .= " del ".$vResDat['resprexx'].$vResDat['resdesxx']." al ".$vResDat['resprexx'].$vResDat['reshasxx']." Vigencia: ". $nMesesVigencia ." Meses";

      //Resolucion
      $this->setXY($posx, $posy + 10);
      $this->SetFont('Arial', '', 7);
      $this->TextWithDirection(6, 207, utf8_decode($cResolucion), 'U');

      //Recuadro de la derecha - Datos del adquiriente
      $this->ln(15);
      $posy = $this->GetY();
      $this->setX($posx);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(15, 4, utf8_decode("CLIENTE:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(45, 4, utf8_decode($vCliFac['CLINOMXX']), 0, 'L');
      $this->setX($posx);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(15, 4, utf8_decode("NIT:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(45, 4, number_format($vCliFac['CLIIDXXX'], 0, '', '.'). "-" .f_Digito_Verificacion($vCliFac['CLIIDXXX']), 0, 'L');
      $this->setX($posx);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(15, 4, utf8_decode("DIRECCIÓN:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(45, 4, $vCliFac['CLIDIRXX'], 0, 'L');
      $this->setX($posx);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(15, 4, utf8_decode("TELÉFONO:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(45, 4, $vCliFac['CLITELXX'], 0, 'L');
      $this->setX($posx);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(15, 4, utf8_decode("CIUDAD:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(45, 4, $vCiuFac['CIUDESXX'], 0, 'L');
      $posyy = $this->GetY();

      //Recuadro del centro - Informacion adicional
      $this->setXY($posx + 65, $posy);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(15, 5, utf8_decode("GUIA/BL:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(45, 5, $cDocTra, 0, 'L');
      $this->setX($posx + 65);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(15, 5, utf8_decode("PESO(KG):"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(45, 5, number_format($cPesBru, 2, ',', '.'), 0, 'L');
      $this->setX($posx + 65);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(15, 5, utf8_decode("D.O. No:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(45, 5, $cDocId, 0, 'L');
      $this->setX($posx + 65);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(15, 5, utf8_decode("PIEZAS:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(45, 5, number_format($cBultos, 0), 0, 'L');
      $posyy = $this->GetY() > $posyy ? $this->GetY() : $posyy;

      //Cuadro izquierdo
      $this->setXY($posx + 130, $posy);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(35, 5, utf8_decode("FECHA EMISIÓN:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(30, 5, $dFecEmi, 0, 'R');
      $this->setX($posx + 130);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(35, 5, utf8_decode("HORA GENERACIÓN:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(30, 5, $dHorEmi, 0, 'R');
      $this->setX($posx + 130);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(35, 5, utf8_decode("FECHA DE VENCIMIENTO:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(30, 5, $dFecVen, 0, 'R');
      $this->setX($posx + 130);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(35, 5, utf8_decode("FORMA DE PAGO:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(30, 5, utf8_decode($cFormaPag), 0, 'R');
      $this->setX($posx + 130);
      $this->SetFont('Arial', 'B', 7);
      $this->Cell(35, 5, utf8_decode("MEDIO DE PAGO:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(30, 5, $_POST['cMePagDes'], 0, 'R');
      $posyy = $this->GetY() > $posyy ? $this->GetY() : $posyy;
      //Pinto los rectangulos
      $this->Rect($posx, $posy, 60, $posyy - $posy);
      $this->Rect($posx + 65, $posy, 60, $posyy - $posy);
      $this->Rect($posx + 130, $posy, 65, $posyy - $posy);
      $this->setXY($posx, $posyy);
      
      $this->Ln(4);
    }//Function Header
    
    function Footer(){
      $posx	= 10;
      $posy = 242;

      $this->setXY($posx+143,$posy);
      $this->SetFont('Arial','B',6);
      $this->Cell(30,3, utf8_decode("Validación DIAN: "),0,0,'L');

      $this->setXY($posx, $posy);
      $this->Cell(155, 4, utf8_decode("REPRESENTACIÓN IMPRESA DE LA FACTURA ELECTRÓNICA"), 0, 0, 'L');

      $this->setXY($posx + 180, $posy + 2);
      $this->Cell(15, 5, "CUFE:", 0, 0, 'L');
   
      $posy += 2;
      $this->setXY($posx+80,$posy);
      $this->SetFont('arial','B',8);
      $this->Cell(30,5,"",0,0,'C');
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

    function TextWithDirection($x, $y, $txt, $direction='U') {
      if ($direction=='R')
          $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
      elseif ($direction=='L')
          $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
      elseif ($direction=='U')
          $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
      elseif ($direction=='D')
          $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
      else
          $s=sprintf('BT %.2F %.2F Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
      if ($this->ColorFlag)
          $s='q '.$this->TextColor.' '.$s.' Q';
      $this->_out($s);
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

  $posy	    = $pdf->GetY()+3; 
  $posx	    = 10;
  $posfin   = 230;
  $posRect  = $posy-2;

  ## Impresion de Titulo pagos a terceros
  $pdf->SetFont('arial','B',8);
  $pdf->setXY($posx,$posy);
  $pdf->Cell(140,5,"PAGOS A TERCEROS",0,0,'L');

  ## Impresion de Titulos
  $pdf->SetFont('arial','B',7);
  $pdf->setXY($posx,$posy+5);
  $pdf->Cell(25,4,utf8_decode("CÓDIGO"),0,0,'C');
  $pdf->Cell(70,4,utf8_decode("DESCRIPCIÓN"),0,0,'C');
  $pdf->Cell(20,4,"CANT",0,0,'C');
  $pdf->MultiCell(30,4,"FACTURA\nGASTOS",0,'C');
  $pdf->setXY($posx+145,$posy+5);
  $pdf->MultiCell(25,4,"VLR\nUNITARIO",0,'C');
  $pdf->setXY($posx+170,$posy+5);
  $pdf->Cell(25,4,"VLR TOTAL",0,0,'C');
  $py = $posy+13;

  // $mAgrupaxConcepto = array_merge($mAgrupaxConcepto, $mAgrupaxConcepto, $mAgrupaxConcepto);
  // $mAgrupaxConcepto = array_merge($mAgrupaxConcepto, $mAgrupaxConcepto, $mAgrupaxConcepto);
  // $mAgrupaxConcepto = array_merge($mAgrupaxConcepto, $mAgrupaxConcepto, $mAgrupaxConcepto);

  if(count($mAgrupaxConcepto) > 0){

    //Se imprimen los Ingresos por Terceros
    $pdf->SetWidths(array(25,70,20,30,25,25));
    $pdf->SetAligns(array("C","L","C","C","R","R"));
    $pdf->setXY($posx,$py);

    for ($i=0;$i<count($mAgrupaxConcepto);$i++) {
      if($py > $posfin){
        $pdf->Rect($posx,$posy,195,($posfin-$posRect));
        $pdf->AddPage();
        $nPagina++; 
        $py = $posy;
        $pdf->setXY($posx,$py);
      }

      $TotPcc += $mAgrupaxConcepto[$i]['nComVlr'];
      $cComObs_PCCA = explode("^", $mAgrupaxConcepto[$i]['cComObs']);
      // if (substr_count($cComObs_PCCA[0],"DIAN") > 0 ) { // Encontre la palabra DIAN de pago de impuestos.
      //   $cObs = "TRIBUTOS ADUANEROS";
      // }else{
        $cObs = $cComObs_PCCA[0];
      // }

      $pdf->SetFont('arial','',7);
      $pdf->setX($posx);
      $pdf->Row(array("",//trim($mAgrupaxConcepto[$i]['cComId'])
                      trim($cObs), 
                      "1", 
                      $mAgrupaxConcepto[$i]['cComCsc3'], 
                      number_format($mAgrupaxConcepto[$i]['nComVlr'],0,'.',','),
                      number_format($mAgrupaxConcepto[$i]['nComVlr'],0,'.',',')));
      $py += 4;
    }

    if($py > $posfin){
      $pdf->Rect($posx,$posy,195,($posfin-$posRect));
      $pdf->AddPage();
      $nPagina++; 
      $py = $posy;
      $pdf->setXY($posx,$py);
    }
    $TotPcc = $TotPcc + $To4xMil;
  }

  if($py > $posfin){
    $pdf->Rect($posx,$posy,195,($posfin-$posRect));
    $pdf->AddPage();
    $nPagina++; 
    $py = $posy;
    $pdf->setXY($posx,$py);
  }

  // Imprimo el subtotal de los Pagos a Terceros
  $pdf->setXY($posx+120,$py+5);
  $pdf->SetFont('arial','B',8);
  $pdf->Cell(50,3, "TOTAL PAGOS A TERCEROS",0,0,'L');
  $pdf->Cell(25,3, number_format($TotPcc, 0,'.',','),0,0,'R');
  $py += 10;

  $TotalIP = 0;
  if (count($mDatIP) > 0) {
    //Ingresos Propios
    $pdf->SetFont('arial','B',8);
    $pdf->setXY($posx,$py);
    $pdf->Cell(140,3,"SERVICIOS GRAVADOS CON IVA",0,0,'L');
    $py += 5;

    $pdf->SetWidths(array(25,70,20,30,25,25));
    $pdf->SetAligns(array("C","L","C","C","R","R"));
    $pdf->setXY($posx,$py);

    ##Imprimo Ingresos Propios##
    for ($k=0;$k<count($mDatIP);$k++) {
      if($py > $posfin){
        $pdf->Rect($posx,$posy,195,($posfin-$posRect));
        $pdf->AddPage();
        $nPagina++; 
        $py = $posy;
        $pdf->setXY($posx,$py);
      }

      $TotalIP  += $mDatIP[$k]['comvlrxx'];
      $nValorUni = ($mDatIP[$k]['unidadfe'] != "A9" && $mDatIP[$k]['canfexxx'] > 0) ? $mDatIP[$k]['comvlrxx']/$mDatIP[$k]['canfexxx'] : $mDatIP[$k]['comvlrxx'];

      $pdf->SetFont('arial','',7);
      $pdf->setX($posx);
      $pdf->Row(array(trim($mDatIP[$k]['ctoidxxx']),
                      trim($mDatIP[$k]['comobsxx']), 
                      $mDatIP[$k]['canfexxx'],
                      $mDatIP[$k]['cfactgas'], 
                      number_format($nValorUni,0,'.',','),
                      number_format($mDatIP[$k]['comvlrxx'],0,'.',',')));
      $py += 4;
    }//for ($k=0;$k<count($mDatIP);$k++) {
    ##Fin Imprimo Ingresos Propios##
  }
  
  if($py > $posfin){
    $pdf->Rect($posx,$posy,195,($posfin-$posRect));
    $pdf->AddPage();
    $nPagina++; 
    $py = $posy;
    $pdf->setXY($posx,$py);
  }

  // Imprimo el subtotal de los Ingresos Propios
  $pdf->setXY($posx+5,$py+5);
  $pdf->SetFont('arial','B',8);
  $pdf->Cell(115,5, "SUBTOTAL",0,0,'L');
  $pdf->Cell(50,5, "TOTAL INGRESOS PROPIOS",0,0,'L');
  $pdf->Cell(25,5, number_format($TotalIP, 0,'.',','),0,0,'R');
  $py += 5;

  if($py > $posfin){
    $pdf->Rect($posx,$posy,195,181-$posRect);
    $pdf->AddPage();
    $nPagina++; 
    $py = $posy;
  }

  $pdf->Rect($posx,$posy,195,181-$posRect);

  
  ##Bloque que acumula retenciones por valor de porcentaje##
  ## RETEFUENTE ##
  $mRetFte = array();
  $nTotRfte = 0;
  for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {

    $nTotRfte += $_POST['nVlrFte_IPA'.($i+1)];
    $nPorRfte  = $_POST['nPorFte_IPA' .($i+1)];

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

  #### SubTotales ####
  $cNegativo = "";
  if ($_POST['nIPASal'] > 0) {
    $cNegativo = "";
  } else {
    $cNegativo = "-";
  }
  $nTotPag = abs($_POST['nIPASal']);

  $nAnticipoReal = abs($_POST['nIPAAnt']);
  if($cNegativo == "-") {
    $nSaldoFavor = $nTotPag;
    $_POST['nIPAAnt'] = abs($_POST['nIPAAnt']) + ($nTotPag*-1);
    $nTotPag = 0;
  }

  $nSubTotal = $TotPcc + $TotalIP;
  #### FIN SubTotales ####

  ### Imprimo los Subtotales ##
  $posx	= 10;
  $posy = 183;

  $pdf->SetFont('Arial', '', 7);
  $pdf->SetTextColor(100, 100, 100);
  $pdf->setXY($posx + 115, $posy);
  $pdf->Cell(40, 5, "Subtotal", 0, 0, 'L');
  $pdf->Cell(40, 5, number_format($nSubTotal, 0, '.', ','), 0, 0, 'R');
  $pdf->ln(5);
  $pdf->setX($posx + 115);
  $pdf->Cell(40, 5, "IVA", 0, 0, 'L');
  $pdf->Cell(40, 5, number_format($_POST['nIPAIva'], 0, '.', ','), 0, 0, 'R');
  $pdf->ln(5);
  $pdf->setX($posx + 115);
  $pdf->Cell(40, 5, "Retefuente ".number_format($nPorRfte, 2, '.', ',') ."%", 0, 0, 'L');
  $pdf->Cell(40, 5, number_format($nTotRfte, 0, '.', ','), 0, 0, 'R');
  $pdf->ln(5);
  $pdf->setX($posx + 115);
  $pdf->Cell(40, 5, "Anticipo", 0, 0, 'L');
  $pdf->Cell(40, 5, number_format(abs($_POST['nIPAAnt']), 0, '.', ','), 0, 0, 'R');
  $pdf->ln(5);
  $pdf->setX($posx + 115);
  $pdf->Cell(40, 5, "Total a Pagar", 0, 0, 'L');
  $pdf->Cell(40, 5, number_format($nTotPag, 0, '.', ','), 0, 0, 'R');
  $pdf->ln(5);

  $nToltalPagar = ($nSaldoFavor > 0) ? $nSaldoFavor : $nTotPag;
  $alinea       = explode("~",f_Words(f_Cifra_Php(abs($nToltalPagar),'PESO'),100));

  $cValorLetras = "";
  for ($n=0;$n<count($alinea);$n++) {
    $cValorLetras .= $alinea[$n];
  }

  $pdf->SetFillColor(217, 217, 217);
  $pdf->SetTextColor(0, 0, 0);
  $pdf->Rect($posx, $posy, 115, 15);
  $pdf->Rect($posx, $posy + 15, 115, 10, 'DF');
  $pdf->Rect($posx + 115, $posy, 80, $pdf->GetY() - $posy);
  $pdf->setXY($posx, $posy);
  $pdf->MultiCell(115, 3.3, utf8_decode("OBSERVACIONES: ".$_POST['cComObs']), 0, 'L');
  $pdf->SetFont('Arial', 'I', 8);
  $pdf->setXY($posx, $posy + 15);
  $pdf->SetTextColor(0, 0, 0);
  $pdf->Cell(35, 5, "ANTICIPO REAL", 'T', 0, 'L');
  $pdf->Cell(80, 5, number_format($nAnticipoReal, 0, '.', ','), 'T', 0, 'R');
  $pdf->ln(5);
  $pdf->setX($posx);
  $pdf->Cell(35, 5, "SALDO A FAVOR DEL CLIENTE", 0, 0, 'L');
  $pdf->Cell(80, 5, number_format($nSaldoFavor, 0, '.', ','), 0, 0, 'R');
  $pdf->setXY($posx, $posy + 25);
  $pdf->SetFont('Arial', '', 6);
  $pdf->SetFont('Arial', 'IB', 7);
  $pdf->MultiCell(195, 5, utf8_decode("SON: " . $cValorLetras), 1, 'L', true);
  $pdf->setX($posx);

  $pdf->SetFont('Arial', '', 6.5);
  $pdf->MultiCell(195, 4, utf8_decode("LA MORA EN EL PAGO OCASIONARÁ INTERESES SOBRE LOS SALDOS A LA TASA MÁS ALTA PERMITIDA SIN PERJUICIO DE LAS CONDICIONES EJECUTIVAS PERTINENTES"), 1, 'L');
  $pdf->setX($posx);
  $cNotaFinal_1  = "LAS MERCANCIAS VIAJAN POR CUENTA Y RIESGO DE NUESTROS CLIENTES Y NO ASEGURAMOS LAS MISMAS DE NO MEDIAR ORDEN EXPRESA POR ESCRITO EN TODAS LAS OPERACIONES DE TRANSPORTE NUESTRA RESPONSABILIDAD NO PODRÁ EXCEDER NINGÚN CASO A LA QUE ASUME ";
  $cNotaFinal_1 .= "FRENTE A NOSOTROS LAS COMPAÑÍAS DE NAVEGACIÓN, AEREA Y TRANSPORTE POR CARRETERA O CUALQUIER OTRO INERMEDIARIO QUE INTERVENGA EN EL TRANSCURSO DEL TRANSPORTE. LOS FLETES QUEDAN SUBORDINADOS A LAS FACTURACIONES DE LAS TARIFAS AÉREAS MARÍTIMAS Y TERERESTRES.";
  $pdf->MultiCell(195, 3.5, utf8_decode($cNotaFinal_1), 1, 'L');
  $cNotaFinal_2 = "Esta factura se asimila en todos sus efectos a una letra de cambio. Art. 774 y 776 del Código de Comercio Colombiano. Paguese mediante Transferencia a Cuenta Cte Banco de Occidente No. 808-08439-6 a nombre de AGENCIA DE ADUANAS SOLUCIONES ADUANERAS S.A.S NIVEL 2";
  $pdf->setX($posx);
  $pdf->MultiCell(195, 3.5, utf8_decode($cNotaFinal_2), 1, 'L');

  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	$pdf->Output($cFile);

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }

	echo "<html><script>document.location='$cFile';</script></html>";
?>

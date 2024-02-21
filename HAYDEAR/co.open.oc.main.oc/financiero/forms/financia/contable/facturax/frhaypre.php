<?php
  /**
	 * Imprime Factura de EXPORCOMEX.
	 * --- Descripcion: Permite Imprimir Factura de Venta Agencia de EXPORCOMEX por Vista Previa.
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package openComex
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

  // Reconstruyendo POST de DOS, pagos a terceros e ingresos propios
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

  // Armando Matriz de DOs
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
  // Armando Matriz de DOs
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

  // Armando Matriz de DOs
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

  // Fecha Creacion
  $dFecEmi = $_POST['dRegFCre'];
  // Hora Creacion
  $dHora_IPA = explode(" ", $_POST['cRegStamp_DOS1']);
  $dHorEmi   = $dHora_IPA[1];
  // Fecha Vencimiento
  $dFecVen = date("Y-m-d",mktime(0,0,0,substr($_POST['dRegFCre'],5,2),substr($_POST['dRegFCre'],8,2)+$_POST['cTerPla'],substr($_POST['dRegFCre'],0,4)));

	// Trayendo Datos de Do Dependiendo del Tipo de Operacion
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
	$cCodPos = $vDatDo['clicposx']; //Codigo Postal Vendedor
	$cOrdCom = $vDatDo['ordcomxx']; //Orden de Compra
	$cPaiOri = $vDatDo['paiorixx']; //Pais de Origen
	$dReaArr = $vDatDo['fecrearr']; //Fecha real de arribo
	$cCiuOri = $vDatDo['ciuorixx']; //Ciudad Origen
  $cCiuDes = $vDatDo['ciudesxx']; //Ciudad Destino
  $cFecTra = $vDatDo['dgefdtxx']; //Fecha Doc. Transporte
	$cDoitra = $vDatDo['tradesxx']; //Transportadora
	$cCodEmb = $vDatDo['temidxxx']; //Codigo Embalaje
  $cNomImp = $vDatDo['nomimpor']; //Nombre de Importador
	// Fin Trayendo Datos de Do Dependiendo del Tipo de Operacion

  // Consulto en la SIAI0150 Datos del Facturado A:
	$qCliFac  = "SELECT ";
	$qCliFac .= "$cAlfa.SIAI0150.CLIIDXXX,";
  $qCliFac .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\" SIN NOMBRE\") AS CLINOMXX, ";
	$qCliFac .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX,";
	$qCliFac .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX,";
	$qCliFac .= "IF($cAlfa.SIAI0150.CLIEMAXX != \"\",$cAlfa.SIAI0150.CLIEMAXX,\"SIN CORREO\") AS CLIEMAXX,";
	$qCliFac .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX,";
	$qCliFac .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX,";
	$qCliFac .= "IF($cAlfa.SIAI0150.CLICPOSX != \"\",$cAlfa.SIAI0150.CLICPOSX,\"\") AS CLICPOSX,";
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
  // Fin Consulto en la SIAI0150 Datos del Facturado A:

  // Traigo Ciudad del Facturado A
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
	// Fin Traigo Ciudad del Facturado A

  // Traigo el CLINOMXX o Razon Social de la Agencia
  $qAgenDat  = "SELECT ";
  $qAgenDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
  $qAgenDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
  $qAgenDat .= "$cAlfa.SIAI0150.CLIDIRXX, ";
  $qAgenDat .= "$cAlfa.SIAI0150.CLITELXX, ";
  $qAgenDat .= "$cAlfa.SIAI0150.CLIEMAXX, ";
  $qAgenDat .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
  $qAgenDat .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
  $qAgenDat .= "IF($cAlfa.SIAI0150.CLICPOSX != \"\",$cAlfa.SIAI0150.CLICPOSX,\"\") AS CLICPOSX, ";
  $qAgenDat .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX ";
  $qAgenDat .= "FROM $cAlfa.SIAI0150 ";
  $qAgenDat .= "WHERE ";
  $qAgenDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vSysStr['financiero_nit_agencia_aduanas']}\" AND ";
  $qAgenDat .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
  $xAgenDat  = f_MySql("SELECT","",$qAgenDat,$xConexion01,"");
  $vAgenDat  = array();
  if (mysql_num_rows($xAgenDat) > 0) {
    $vAgenDat = mysql_fetch_array($xAgenDat);
  }
  // Fin Traigo el CLINOMXX o Razon Social de la Agencia

  // Traigo la Descripcion de la Ciudad de la Agencia
  $qCiuAgen  = "SELECT CIUDESXX ";
  $qCiuAgen .= "FROM $cAlfa.SIAI0055 ";
  $qCiuAgen .= "WHERE ";
  $qCiuAgen .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vAgenDat['PAIIDXXX']}\" AND ";
  $qCiuAgen .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vAgenDat['DEPIDXXX']}\" AND ";
  $qCiuAgen .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vAgenDat['CIUIDXXX']}\" AND ";
  $qCiuAgen .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" LIMIT 0,1";
  $xCiuAgen  = f_MySql("SELECT","",$qCiuAgen,$xConexion01,"");
  $vCiuAgen  = array();
  if (mysql_num_rows($xCiuAgen) > 0) {
    $vCiuAgen = mysql_fetch_array($xCiuAgen);
  }
  // Fin Traigo la Descripcion de la Ciudad de la Agencia

  // Traigo la Descripcion del Pais de la Agencia
  $qPaisAgen  = "SELECT ";
  $qPaisAgen .= "PAIDESXX ";
  $qPaisAgen .= "FROM $cAlfa.SIAI0052 ";
  $qPaisAgen .= "WHERE ";
  $qPaisAgen .= "PAIIDXXX = \"{$vAgenDat['PAIIDXXX']}\"";
  $xPaisAgen  = f_MySql("SELECT","",$qPaisAgen,$xConexion01,"");
  $vPaisAgen  = array();
	if (mysql_num_rows($xPaisAgen) > 0) {
    $vPaisAgen = mysql_fetch_array($xPaisAgen);
  }
  // Fin Traigo la Descripcion del Pais de la Agencia

  // Traigo los datos de la resolucion
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
  // Fin Traigo los datos de la resolucion

  // Traigo el codigo de la Unidad de medida por Concepto
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
	// Fin Traigo el codigo de la Unidad de medida por Concepto

  // Codigo para imprimir los ingresos para terceros
	$mIngTer = array();
  for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
    $vDesc = explode("^",$_POST['cComObs_PCCA' .($i+1)]);
    if ($_POST['cTipo_PCCA'.($i+1)] == "IMPUESTO_FINANCIERO") { //si es GMF debe mostrarse en GMF
      $nInd_mIngTer = count($mIngTer);
			$mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId_PCCA' .($i+1)];
      $mIngTer[$nInd_mIngTer]['cComObs']  = $_POST['cComObs_PCCA'.($i+1)];
      $mIngTer[$nInd_mIngTer]['nVlrIva']  = 0;
      $mIngTer[$nInd_mIngTer]['nBaseIva'] = 0;
      $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA'.($i+1)];
      $mIngTer[$nInd_mIngTer]['cComCsc3'] = "";
      $mIngTer[$nInd_mIngTer]['cUniMed']  = ($vCtoCon["{$_POST['cComId_PCCA'.($i+1)]}"]['umeidxxx'] != '') ? $vCtoCon["{$_POST['cComId_PCCA'.($i+1)]}"]['umeidxxx'] : "A9"; // Unidad de medida
    } else {
      $nInd_mIngTer = count($mIngTer);
			$mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId_PCCA' .($i+1)];
      $mIngTer[$nInd_mIngTer]['cComObs']  = $vDesc[0] . " " . $vDesc[1]; // Se concatena el nombre del Proveedor
      $mIngTer[$nInd_mIngTer]['nVlrIva']  = ($_POST['nVlrIva_PCCA'.($i+1)]+0);
      $mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nBaseIva_PCCA'.($i+1)];
      $mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA'.($i+1)];
      $mIngTer[$nInd_mIngTer]['cComCsc3'] = ($_POST['cComDocIn_PCCA'.($i+1)] != "") ? $_POST['cComDocIn_PCCA'.($i+1)] : $_POST['cComCsc3_PCCA'.($i+1)];
      $mIngTer[$nInd_mIngTer]['cUniMed']  = ($vCtoCon["{$_POST['cComId_PCCA'.($i+1)]}"]['umeidxxx'] != '') ? $vCtoCon["{$_POST['cComId_PCCA'.($i+1)]}"]['umeidxxx'] : "A9"; // Unidad de medida
    }
  }//for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
  // Fin Codigo para imprimir los ingresos para terceros

  // se arma el array de ingresos propios
  $mIP = array();
  for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
    //Traigo las cantidades y el detalle de los IP del utiliqdo.php
		$vDatosIp = array();
		$cObs = "|".$_POST['cSerId_IPA'.($i+1)]."~".$_POST['cFcoId_IPA'.($i+1)]."~".$_POST['cComObs_IPA'.($i+1)]."|";
    $vDatosIp = f_Cantidad_Ingreso_Propio($cObs,'',$_POST['cSucId_IPA'.($i+1)],$_POST['cDosNro_IPA'.($i+1)],$_POST['cDosSuf_IPA'.($i+1)]);

    $nInd_mIP = count($mIP);
    $mIP[$nInd_mIP]['ctoidxxx'] = $_POST['cComId_IPA'.($i+1)];
		$mIP[$nInd_mIP]['seridxxx'] = $_POST['cSerId_IPA'.($i+1)];
    $mIP[$nInd_mIP]['comobsxx'] = $vDatosIp[0];
		$mIP[$nInd_mIP]['comvlrxx'] = $_POST['nComVlr_IPA'.($i+1)];
		$mIP[$nInd_mIP]['comvlr01'] = $_POST['nComVIva_IPA'.($i+1)]; // Valor Iva
		//Cantidad FE
		$mIP[$nInd_mIP]['unidadfe'] = $vDatosIp[2];
		$mIP[$nInd_mIP]['canfexxx'] += $vDatosIp[1];

		//Cantidad por condicion especial
		for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
			$mIP[$nInd_mIP]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] += $vDatosIp[3][$nP]['valpdfxx'];
		}
  }//for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {

  $mIngPro = array();
  foreach ($mIP as $cKey => $mValores) {
    $mIngPro[count($mIngPro)] = $mValores;
  }
  // Fin se arma el array de ingresos propios

  // Traigo la Forma de Pago
  $cFormaPag = "";
  if ($_POST['cComFpag'] != "") {
    $cFormaPag = ($_POST['cComFpag'] == 1) ? "CONTADO" : "CREDITO";
  }

  $cMedioPago = $_POST['cMePagDes'];

  class PDF extends FPDF {

    function Header() {
      global $cAlfa;    global $cRoot;    global $vSysStr; global $_COOKIE;   global $cPlesk_Skin_Directory;
      global $vAgenDat; global $vResDat;  global $vCliFac; global $cFormaPag; global $cMedioPago;
      global $cPedido;  global $cDocId;   global $cDocTra; global $cBultos;   global $cPesBru;
      global $cTasCam;  global $vComObs2; global $nValAdu; global $cDocSuf;   global $cSucId;
      global $vDceDat;  global $dFecEmi;  global $dFecVen; global $dHorEmi;

      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,180,180);

      $posx = 8;
      $posy = 10;

      // logo
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logohaydear.jpeg', $posx, $posy - 8, 70, 27);

      $this->SetFont('Arial', '', 6);
      $this->TextWithDirection(212,170,utf8_decode("Actividad Económica 6390 TARIFA 9.66 X 1.000- RÉGIMEN COMÚN"),'U');  
      $cResolucion  = "Autorización para factura electrónica según Resolución DIAN No.".$vResDat['residxxx']." Fecha ".$this->fechaCastellano($vResDat['resfdexx']). " Numeración Autorizada del No.";
      $cResponsable = "{$vResDat['resprexx']}"."{$vResDat['resdesxx']}". " hasta {$vResDat['resprexx']}"."{$vResDat['reshasxx']}";
      $this->TextWithDirection(3.5,210,utf8_decode($cResolucion),'U');
      $this->TextWithDirection(5.5,140,utf8_decode($cResponsable),'U');

      // Informacion Agencia
      $this->setXY($posx, $posy+15);
      $this->SetTextColor(50, 143, 206);
      $this->SetTextColor(0);
      $this->setX($posx+5);
      $this->SetFont('Arial', '', 7);
      $cNitAduana  = number_format($vSysStr['financiero_nit_agencia_aduanas'], 0, '', '.')."-";
      $cNitAduana .= f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']);
      $this->Cell(70, 2, utf8_decode("NIT. " . $cNitAduana), 0, 0, 'C');
      $this->Ln(4);
      $this->setX($posx+5);
      $this->Cell(70, 2, utf8_decode("COD 599"), 0, 0, 'C');
      $this->SetTextColor(0);

      $this->setXY($posx-3,$posy+22);
      $this->SetFont('Arial', 'I', 8);
      $this->Ln(2);

      // Informacion factura
      $this->SetFont('Arial', 'B', 9);
      $this->setXY($posx + 140, $posy+1);
      $this->MultiCell(70, 4, utf8_decode("FACTURA ELECTRÓNICA\n DE VENTA"), 0, 'C');
      $this->setX($posx + 148);
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(2, 6, "No.", 0, 0, 'L');
      $this->setX($posx + 156);
      $this->SetFont('Arial', '', 9);
      $this->MultiCell(40, 6, $vResDat['comidxxx']." ".$vCliFac['comcodxx'] ." ". $vCliFac['comcscxx'], 1, 'C');
      $this->setXY($posx + 138, $posy+16);
      $this->SetFont('Arial', '', 7);
      $this->MultiCell(60, 3, utf8_decode("NO SOMOS GRANDES CONTRIBUYENTES,\n Actividad Económica No. 5229 ICA 9.66\n Régimen Común IVA"), 0, 'R');
      $posy = $this->GetY();

      // Informacion facturar a
      $this->setXY($posx+3, $posy+4);
      $this->SetFont('Arial', '', 7.5);
      $this->Cell(22, 4, utf8_decode("FACTURADO A:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7.5);
      $this->MultiCell(83, 4, utf8_decode($vCliFac['CLINOMXX']), 0, 'J');
      $this->setX($posx+3);
      $this->SetFont('Arial', '', 7.5);
      $this->Cell(22, 4, utf8_decode("NIT:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7.5);
      $this->MultiCell(83, 4, number_format($vCliFac['CLIIDXXX'], 0, '', '.'). "-" .f_Digito_Verificacion($vCliFac['CLIIDXXX']), 0, 'L');
      $this->setX($posx+3);
      $this->SetFont('Arial', '', 7.5);
      $this->Cell(22, 4, utf8_decode("DIRECCION:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7.5);
      $this->MultiCell(83, 4, utf8_decode($vCliFac['CLIDIRXX']), 0, 'L');
      $this->setX($posx+3);
      $this->SetFont('Arial', '', 7.5);
      $this->Cell(22, 4, utf8_decode("TELÉFONO:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7.5);
      $this->MultiCell(83, 4, utf8_decode($vCliFac['CLITELXX']), 0, 'L');
      $this->setX($posx+3);
      $this->SetFont('Arial', '', 7.5);
      $this->Cell(22, 4, utf8_decode("DOC. TRANS:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7.5);
      $this->MultiCell(83, 4, utf8_decode($cDocTra), 0, 'L');
      $this->setX($posx+3);
      $this->SetFont('Arial', '', 7.5);
      $this->Cell(22, 4, utf8_decode("VALOR CIF:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7.5);
      $this->MultiCell(83, 4, utf8_decode($nValAdu), 0, 'L');
      $this->setX($posx+3);
      $this->SetFont('Arial', '', 7.5);
      $this->Cell(22, 4, utf8_decode("REFERENCIA:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7.5);
      $this->MultiCell(75, 4, utf8_decode($vDceDat), 0, 'L');
      $posyy = $this->GetY();

      $this->setXY($posx + 130, $posy+4);
      $this->SetFont('Arial', '', 7.5);
      $this->Cell(30, 4, utf8_decode("FECHA TRANSMISION:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7.5);
      $this->MultiCell(42, 4, utf8_decode($dFecEmi). " " .utf8_decode($dHorEmi), 0, 'L');
      $this->setX($posx + 130);
      $this->SetFont('Arial', '', 7.5);
      $this->Cell(30, 4, utf8_decode("FECHA EMISION:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7.5);
      $this->MultiCell(42, 4, utf8_decode($dFecEmi). " " .utf8_decode($dHorEmi), 0, 'L');
      $this->setX($posx + 130);
      $this->SetFont('Arial', '', 7.5);
      $this->Cell(30, 4, utf8_decode("FECHA VENCIMIENTO:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7.5);
      $this->MultiCell(42, 4, utf8_decode($dFecVen), 0, 'L');
      $this->setX($posx + 130);
      $this->SetFont('Arial', '', 7.5);
      $this->Cell(30, 4, utf8_decode("FORMA DE PAGO:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7.5);
      $this->MultiCell(42, 4, utf8_decode($cFormaPag), 0, 'L');
      $this->setX($posx + 130);
      $this->SetFont('Arial', '', 7.5);
      $this->Cell(30, 4, utf8_decode("NUMERO PEDIDO:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7.5);
      $this->MultiCell(42, 4, utf8_decode($cPedido), 0, 'L');
      $this->setX($posx + 130);
      $this->SetFont('Arial', '', 7.5);
      $this->Cell(30, 4, utf8_decode("NUMERO DO:"), 0, 0, 'L');
      $this->SetFont('Arial', '', 7.5);
      $this->MultiCell(43, 4, utf8_decode($cSucId)."-".utf8_decode($cDocId)."-".utf8_decode($cDocSuf), 0, 'L');

      $posyy = $this->GetY() > $posyy ? $this->GetY() : $posyy;
      $this->RoundedRect($posx, $posy+2.5, 200, 31, 5, 'D');
      $posy = $posyy;

      $this->setXY($posx, $posy+5);
    }//Function Header

    function Footer(){
      global $vSysStr; global $vAgenDat; global $vCiuAgen; global $vPaisAgen;

      $posx	= 8;
      $posy = 263;

      $this->setXY($posx, $posy);
      $this->SetFont('Arial', '', 6);
      $this->MultiCell(200, 2.5, utf8_decode("Esta factura se homologa a la ley 1231 del 2008, como título valor o mecanismo de financiación\n AGENCIA DE ADUANAS CORPORACION INTERNACIONAL DE COMERCIO EXTERIOR HAYDEAR S.A.S NIVEL 2 OFICINA\n PRINCIPAL BOGOTÁ: CALLE 104C No. 47 A 22 Tel: 7043555 - 7043559-7043562\n SUCURSALES: Cartagena, Barranquilla, Santa Marta, Buenaventura E-mail: gerencia@haydear.com\n Impreso por NOMBRE DEL FABRICANTE DEL SOFTWARE - PROVEEDOR TECNOLÓGICO: OPENTECNOLOGIA SA\n NIT: 830135010 NOMBRE DEL SOFTWARE: OPEN-V"), 0, 'C');

      // Paginacion
      // $this->setXY($posx,$posy+15);
      // $this->SetFont('Arial','B',8);
      // $this->Cell(200,4,utf8_decode('Página: ').$this->PageNo().' de {nb}',0,0,'C');
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
        // $this->Rect($x,$y,$w,$h);
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

    // Función para dibujar un rectángulo redondeado
    function RoundedRect($x, $y, $w, $h, $r, $style = '') {
      $k = $this->k;
      $hp = $this->h;
      if ($style == 'F') {
          $op = 'f';
      } elseif ($style == 'FD' || $style == 'DF') {
          $op = 'B';
      } else {
          $op = 'S';
      }
      $MyArc = 4 / 3 * (sqrt(2) - 1);
      $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
      $xc = $x + $w - $r;
      $yc = $y + $r;
      $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
      if ($r > 0) {
          $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
      }
      $xc = $x + $w - $r;
      $yc = $y + $h - $r;
      $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
      if ($r > 0) {
          $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
      }
      $xc = $x + $r;
      $yc = $y + $h - $r;
      $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
      if ($r > 0) {
          $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
      }
      $xc = $x + $r;
      $yc = $y + $r;
      $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
      if ($r > 0) {
          $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
      }
      $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
      $h = $this->h;
      $this->_out(sprintf(
          '%.2F %.2F %.2F %.2F %.2F %.2F c ',
          $x1 * $this->k,
          ($h - $y1) * $this->k,
          $x2 * $this->k,
          ($h - $y2) * $this->k,
          $x3 * $this->k,
          ($h - $y3) * $this->k
      ));
    }

    function fechaCastellano($dFecha) {
      $nombreCompletoMeses = [
        1 => 'ENERO',
        2 => 'FEBRERO',
        3 => 'MARZO',
        4 => 'ABRIL',
        5 => 'MAYO',
        6 => 'JUNIO',
        7 => 'JULIO',
        8 => 'AGOSTO',
        9 => 'SEPTIEMBRE',
        10 => 'OCTUBRE',
        11 => 'NOVIEMBRE',
        12 => 'DICIEMBRE'
      ];
  
      $dia = date("d", strtotime($dFecha));
      $anio = date("Y", strtotime($dFecha));
    
      $mes = $nombreCompletoMeses[date("n", strtotime($dFecha))];
      return $dia . " DE ". $mes . " DE " . $anio;
    }
  }

  $pdf = new PDF('P','mm','Letter');
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->SetFont('verdana','',8);
  $pdf->AliasNbPages();
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);

  $pdf->AddPage();
  $posy	   = $pdf->GetY()+3; 
  $posx	   = 8;
  $posfin  = 240;
  $nCount  = 0; 
  $pyy     = $posy;

  $nTotalPCC     = 0;
  $nTotalIPGra   = 0;
  $nTotalIPNoGra = 0;

  // $mIngPro = array_merge($mIngPro, $mIngPro, $mIngPro);
  // $mIngPro = array_merge($mIngPro, $mIngPro, $mIngPro);
  // $mIngPro = array_merge($mIngPro, $mIngPro, $mIngPro);

  // Imprimo Pagos a Terceros
  if (count($mIngTer) > 0) {
    $pdf->SetFont('arial','B',8);
    $pdf->setXY($posx,$pyy);
    $pdf->Cell(67,6,utf8_decode("DETALLE PAGOS A TERCEROS"),0,0,'L');
    $pdf->Line(93,72,93,205);
    $pdf->setXY($posx+105,$pyy);
    $pdf->Cell(10,6,utf8_decode("CONCEPTO"),0,0,'L');
    $pdf->Line(148,72,148,205);
    $pdf->setXY($posx+147,$pyy);
    $pdf->Cell(10,6,utf8_decode("FACTURA #"),0,0,'L');
    $pdf->Line(178,72,178,205);
    $pdf->setXY($posx+180,$pyy);
    $pdf->Cell(10,6,utf8_decode("VALOR"),0,0,'L');
    $pyy = $posy+6;

    //Se imprimen los Ingresos por Terceros
    $pdf->SetWidths(array(85, 55, 30, 30));
    $pdf->SetAligns(array("L", "L", "C", "R"));
    $pdf->setXY($posx,$pyy);

    for($i=0;$i<count($mIngTer);$i++){
      $nCount++;
      $pyy = $pdf->GetY();
      if($pyy > $posfin){
        $pdf->Rect($posx, $posy-3, 200, $pdf->getY()-($posy-3));
        $pdf->AddPage();
        $pyy = $posy+5;
        $pdf->setXY($posx,$pyy);
      }
      //Consulto la descripcion de la Unidad de medida
      $cUniMedi  = '';
      $qUniMedi  = "SELECT umedesxx ";
			$qUniMedi .= "FROM $cAlfa.fpar0157 ";
			$qUniMedi .= "WHERE ";
			$qUniMedi .= "umeidxxx = \"{$mIngTer[$i]['cUniMed']}\" LIMIT 0,1";
			$xUniMedi  = mysql_query($qUniMedi, $xConexion01);
      if (mysql_num_rows($xUniMedi) > 0) {
        $vUniMedi = mysql_fetch_array($xUniMedi);
        $cUniMedi = $vUniMedi['umedesxx'];
      }

      $nTotalPCC += $mIngTer[$i]['nComVlr'];
      $nVlrBase   = ($mIngTer[$i]['nBaseIva'] > 0) ? $mIngTer[$i]['nBaseIva'] : $mIngTer[$i]['nComVlr'];
      $cDescrip   = ($mIngTer[$i]['cComCsc3'] != "") ? " Base: " . number_format($mIngTer[$i]['nBaseIva'],2,'.',',') . " Iva: " . number_format($mIngTer[$i]['nVlrIva'],2,'.',',') . " FC. " . $mIngTer[$i]['cComCsc3'] : "";

      $pdf->SetFont('arial','',7);
      $pdf->setX($posx);
      $pdf->Row(array(
        $mIngTer[$i]['cComObs'] . " " . $cDescrip,
        $mIngTer[$i]['cComObs'],
        $mIngTer[$i]['facturax'],
        number_format($nVlrBase,2,'.',','),
      ));
    }//for($i=0;$i<count($mIngTer);$i++){

    if($pyy > $posfin){
      $pdf->Rect($posx, $posy-3, 200, $pdf->getY()-($posy-3));
      $pdf->AddPage();
      $pyy = $posy+5;
      $pdf->setXY($posx,$pyy);
    }
    $pyy += 10;
  }//if(count($mIngTer) > 0){
  // Fin Imprimo pagos a terceros

  // Imprimo Ingresos Propios
  if($_POST['nSecuencia_IPA'] > 0 || count($mIngPro) > 0){
    $pdf->SetFont('arial','B',8);
    $pdf->setXY($posx,$pyy);
    $pdf->Cell(67,6,utf8_decode("DETALLE INGRESOS PROPIOS"),0,0,'L');
    $pdf->Line(93,81,93,195);
    $pdf->setXY($posx+105,$pyy);
    $pdf->Cell(10,6,utf8_decode("CONCEPTO"),0,0,'L');
    $pdf->Line(148,81,148,195);
    $pdf->setXY($posx+147,$pyy);
    $pdf->Cell(10,6,utf8_decode("CANTIDAD"),0,0,'L');
    $pdf->Line(178,81,178,195);
    $pdf->setXY($posx+180,$pyy);
    $pdf->Cell(10,6,utf8_decode("VALOR"),0,0,'L');
    $pyy += 6;

    $pdf->SetWidths(array(85, 55, 30, 30));
    $pdf->SetAligns(array("L", "L", "C", "R"));
    $pdf->setXY($posx,$pyy);

    // Se hace dos veces el mismo for para separar los ingresos GRAVADOS y luego los NO GRAVADOS
    for($k=0;$k<(count($mIngPro));$k++) {
      $pyy = $pdf->GetY();
      if($pyy > $posfin){
        $pdf->Rect($posx, $posy-3, 200, $pdf->getY()-($posy-3));
        $pdf->AddPage();
        $pyy = $posy+5;
        $pdf->setXY($posx,$pyy);
      }

      if($mIngPro[$k]['comvlr01'] != 0) {
        $nCount++;
        // Consulto la descripcion de la Unidad de medida
        $cUniMedi  = '';
        $qUniMedi  = "SELECT umedesxx ";
        $qUniMedi .= "FROM $cAlfa.fpar0157 ";
        $qUniMedi .= "WHERE ";
        $qUniMedi .= "umeidxxx = \"{$mIngPro[$k]['unidadfe']}\" LIMIT 0,1";
        $xUniMedi  = mysql_query($qUniMedi, $xConexion01);
        if (mysql_num_rows($xUniMedi) > 0) {
          $vUniMedi = mysql_fetch_array($xUniMedi);
          $cUniMedi = $vUniMedi['umedesxx'];
        }

        $nTotalIPGra += $mIngPro[$k]['comvlrxx'];
        $nVlrUnit = ($mIngPro[$k]['unidadfe'] != "A9" && $mIngPro[$k]['canfexxx'] > 0) ? ($mIngPro[$k]['comvlrxx']/$mIngPro[$k]['canfexxx']) : $mIngPro[$k]['comvlrxx'];

        $pdf->SetFont('arial','',7);
        $pdf->setX($posx);
        $pdf->Row(array(
          $mIngPro[$k]['comobsxx'],
          "",
          $mIngPro[$k]['canfexxx'],
          number_format($nVlrUnit,2,'.',','),
        ));
      }
    }

    for($k=0;$k<(count($mIngPro));$k++) {
      $pyy = $pdf->GetY();
      if($pyy > $posfin){
        $pdf->Rect($posx, $posy-3, 200, $pdf->getY()-($posy-3));
        $pdf->AddPage();
        $pyy = $posy+5;
        $pdf->setXY($posx,$pyy);
      }

      if($mIngPro[$k]['comvlr01'] == 0) {
        $nCount++;
        //Consulto la descripcion de la Unidad de medida
        $cUniMedi  = '';
        $qUniMedi  = "SELECT umedesxx ";
        $qUniMedi .= "FROM $cAlfa.fpar0157 ";
        $qUniMedi .= "WHERE ";
        $qUniMedi .= "umeidxxx = \"{$mIngPro[$k]['unidadfe']}\" LIMIT 0,1";
        $xUniMedi  = mysql_query($qUniMedi, $xConexion01);
        if (mysql_num_rows($xUniMedi) > 0) {
          $vUniMedi = mysql_fetch_array($xUniMedi);
          $cUniMedi = $vUniMedi['umedesxx'];
        }

        $nTotalIPNoGra += $mIngPro[$k]['comvlrxx'];
        $nVlrUnit = ($mIngPro[$k]['unidadfe'] != "A9" && $mIngPro[$k]['canfexxx'] > 0) ? ($mIngPro[$k]['comvlrxx']/$mIngPro[$k]['canfexxx']) : $mIngPro[$k]['comvlrxx'];

        $pdf->SetFont('arial','',7);
        $pdf->setX($posx);
        $pdf->Row(array(
          $nCount,
          $mIngPro[$k]['ctoidxxx'],
          $mIngPro[$k]['comobsxx'],
          utf8_decode($cUniMedi),
          $mIngPro[$k]['canfexxx'],
          number_format($nVlrUnit,2,'.',','),
          number_format($mIngPro[$k]['comvlrxx'],2,'.',',')
        ));
      }
    }

    if($pyy > $posfin){
      $pdf->Rect($posx, $posy-3, 200, $pdf->getY()-($posy-3));
      $pdf->AddPage();
      $pyy = $posy+5;
      $pdf->setXY($posx,$pyy);
    }
    $pyy += 5;
  }//if($_POST['nSecuencia_IPA'] > 0 || count($mIngPro) > 0) {
  // Fin Imprimo Ingresos Propios

  // Se calculan los valores de los totales
  $nTotIva   = $_POST['nIPAIva'];
  $nSubTotal = $nTotalPCC + $nTotIva + $nTotalIPGra + $nTotalIPNoGra;

  $nTotRfte = 0;
  $nPorRfte = 0;
  $nTotRica = 0;
  $nPorRica = 0;
  $nTotRiva = 0;
  $nPorRiva = 0;

  for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
    // Busco Valor de RET.FUENTE
    $nTotRfte += $_POST['nVlrFte_IPA'.($i+1)];
    $nPorRfte  = $_POST['nPorFte_IPA' .($i+1)];

    // Busco Valor de RET.ICA
    $nTotRica += $_POST['nVlrIca_IPA'.($i+1)];
    $nPorRica  = $_POST['nPorIca_IPA' .($i+1)];

    // Busco Valor de RET.IVA
    $nTotRiva += $_POST['nVlrIva_IPA'.($i+1)];
    $nPorRiva  = $_POST['nPorIva_IPA' .($i+1)];
  }

  $cNegativo = "";
  if ($_POST['nIPASal'] > 0) {
    $cNegativo = "";
  } else {
    $cNegativo = "-";
  }
  $nTotPag = $nSubTotal - ($nTotRfte + $nTotRica + $nTotRiva);

  $nSaldoFavor = 0;
  $nAnticipoReal = abs($_POST['nIPAAnt']);
  if($cNegativo == "-") {
    $nSaldoFavor = abs($nTotPag - $nAnticipoReal);
  } else {
    $nTotPag = abs($nTotPag - $nAnticipoReal);
  }

  // Imprimo los Totales
  if($pyy > 195){
    $pdf->Rect($posx, $posy-3, 200, $pdf->getY()-($posy-3));
    $pdf->AddPage();
    $pyy = $posy;
  }

  // Recuadro de los Item
  $pdf->Rect($posx, $posy-3, 200, 205-($posy-3));

  $posx	= 8;
  $posy = 220;
  $pdf->SetFont('Arial', 'B', 7);
  $pdf->setXY($posx, $posy-47);
  $pdf->Cell(35, 4, "SUBTOTAL", 0, 0, 'L');
  $pdf->setX($posx + 175);
  $pdf->Cell(25, 4, number_format($nSubTotal, 2, '.', ','), 0, 0, 'R');
  $pdf->ln(4);
  $pdf->setX($posx);
  $pdf->SetFont('Arial', 'B', 7);
  $pdf->Cell(35, 4, "IVA GENERADO", 0, 0, 'L');
  $pdf->setX($posx + 175);
  $pdf->Cell(25, 4, number_format($nTotIva, 2, '.', ','), 0, 0, 'R');
  $pdf->ln(4);
  $pdf->setX($posx);
  $pdf->SetFont('Arial', 'B', 7);
  $pdf->Cell(35, 4, "RETE-ICA", 0, 0, 'L');
  $pdf->setX($posx + 175);
  $pdf->Cell(25, 4, "-".number_format($nTotRica, 2, '.', ','), 0, 0, 'R');
  $pdf->ln(4);
  $pdf->setX($posx);
  $pdf->SetFont('Arial', 'B', 7);
  $pdf->Cell(35, 4, "RETE-IVA", 0, 0, 'L');
  $pdf->setX($posx + 175);
  $pdf->Cell(25, 4, "-".number_format($nTotRiva, 2, '.', ','), 0, 0, 'R');
  $pdf->ln(4);
  $pdf->setX($posx);
  $pdf->SetFont('Arial', 'B', 7);
  $pdf->Cell(35, 4, "RETE-FUENTE" , 0, 0, 'L');
  $pdf->setX($posx + 175);
  $pdf->Cell(25, 4, "-".number_format($nTotRfte, 2, '.', ','), 0, 0, 'R');
  $pdf->ln(4);
  $pdf->setX($posx);
  $pdf->SetFont('Arial', 'B', 7);
  $pdf->Cell(35, 4, "ANTICIPOS RECIBIDOS", 0, 0, 'L');
  $pdf->setX($posx + 175);
  $pdf->Cell(25, 4, "-".number_format($nAnticipoReal, 2, '.', ','), 0, 0, 'R');
  $pdf->ln(4);
  $pdf->setX($posx);
  $pdf->SetFont('Arial', 'B', 7);
  $pdf->Cell(35, 5, "TOTAL", 0, 0, 'L');
  $pdf->setX($posx + 175);
  $pdf->Cell(25, 5, number_format($nSaldoFavor > 0 ? 0 : $nTotPag, 2, '.', ','), 0, 0, 'R');

  // Valor en letras
  $posy += 32;
  $nTotPag   = $nSaldoFavor > 0 ? $nSaldoFavor : $nTotPag;
  $cVlrLetra = f_Cifra_Php(str_replace("-","",abs($nTotPag)),"PESO");
  $pdf->setXY($posx, $posy-46);
  $pdf->SetFont('Arial', '', 6.5);
  $pdf->Cell(140, 5, "Valor en Pesos:" . utf8_decode($cVlrLetra), 0, 0, 'L');
  $pdf->Rect($posx, $posy-46, 200, 6);
  $pdf->SetFont('Arial', '', 6.5);
  $pdf->setXY($posx, $posy-39);
  $pdf->Cell(140, 5, 'Observaciones: ' . utf8_decode($vCliFac['comobsxx']), 0, 0, 'L');
  $pdf->Rect($posx, $posy-39, 200, 6);

  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
  $pdf->Output($cFile);

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }

  echo "<html><script>document.location='$cFile';</script></html>";
?>

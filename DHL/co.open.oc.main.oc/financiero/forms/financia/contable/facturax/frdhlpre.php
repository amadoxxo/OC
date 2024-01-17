<?php
  /**
	 * Imprime Factura de Venta AGENCIA ADUANAS DHL EXPRESS.
	 * --- Descripcion: Permite Imprimir Factura de Venta AGENCIA ADUANAS DHL EXPRESS por Vista Previa.
	 * @author Juan Jose Trujillo <juan.trujillo@open-eb.co>
	 * @version 001
	 */
  include("../../../../libs/php/utility.php");  
  include("../../../../libs/php/utiliqdo.php");

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

  $qTexCli = "SELECT ";
  $qTexCli .= "tfvtitxx, ";
  $qTexCli .= "tfvcontx ";
  $qTexCli .= "FROM $cAlfa.zdex0011";
	$xTexCli  = f_MySql("SELECT","",$qTexCli,$xConexion01,"");
  if (mysql_num_rows($xTexCli) > 0) {
    $vTexCli = mysql_fetch_array($xTexCli);
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

  $cDosInc = array();
  $vDocPed = array();
  for ($i=0;$i<$_POST['nSecuencia_Dos'];$i++) {
	  if($i == 0) {
      $cDocId   = $_POST['cDosNro_DOS'.($i+1)];
      $cDocSuf  = $_POST['cDosSuf_DOS'.($i+1)];
      $cSucId   = $_POST['cSucId_DOS' .($i+1)];
    }

    if (!in_array($_POST['cDosNro_DOS'.($i+1)], $cDosInc)) {
      $cDosInc[] = $_POST['cDosNro_DOS'.($i+1)];
    }

    ##Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##
    $vDceDat  = array();
    $qDceDat  = "SELECT doctipxx, docpedxx ";
    $qDceDat .= "FROM $cAlfa.sys00121 ";
    $qDceDat .= "WHERE ";
    $qDceDat .= "$cAlfa.sys00121.sucidxxx = \"{$_POST['cSucId_DOS' .($i+1)]}\" AND ";
    $qDceDat .= "$cAlfa.sys00121.docidxxx = \"{$_POST['cDosNro_DOS'.($i+1)]}\" AND ";
    $qDceDat .= "$cAlfa.sys00121.docsufxx = \"{$_POST['cDosSuf_DOS'.($i+1)]}\" ";
    $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
    if (mysql_num_rows($xDceDat) > 0) {
      $vDceDat = mysql_fetch_array($xDceDat);
    }
    ##Fin Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##

    switch ($vDceDat['doctipxx']) {
      case "IMPORTACION":
      case "TRANSITO":
        ##Traigo Datos de la SIAI0200 DATOS DEL DO ##
        $qDoiDat = "SELECT ";
        $qDoiDat .= "$cAlfa.SIAI0200.DOIPEDXX ";
        $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
        $qDoiDat .= "WHERE ";
        $qDoiDat .= "$cAlfa.SIAI0200.DOIIDXXX = \"{$_POST['cDosNro_DOS'.($i+1)]}\" AND ";
        $qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"{$_POST['cDosSuf_DOS'.($i+1)]}\" AND ";
        $qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$_POST['cSucId_DOS' .($i+1)]}\" LIMIT 0,1";
        $xDoiDat = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
        $vDoiDat = mysql_fetch_array($xDoiDat);

        if ($vDoiDat['DOIPEDXX'] != "") {
          $vDocPed[] = "P.O: ".$vDoiDat['DOIPEDXX'];
        }
        ##Fin Traigo Datos de la SIAI0200 DATOS DEL DO ##
        break;
      case "EXPORTACION":
        ## Consulto Datos de Do en Exportaciones tabla siae0199 ##
        $qDexDat = "SELECT dexpedxx ";
        $qDexDat .= "FROM $cAlfa.siae0199 ";
        $qDexDat .= "WHERE ";
        $qDexDat .= "$cAlfa.siae0199.dexidxxx = \"{$_POST['cDosNro_DOS'.($i+1)]}\" AND ";
        $qDexDat .= "$cAlfa.siae0199.admidxxx = \"{$_POST['cSucId_DOS' .($i+1)]}\" ";
        $xDexDat = f_MySql("SELECT","",$qDexDat,$xConexion01,"");
        $vDexDat = mysql_fetch_array($xDexDat);

        if ($vDexDat['dexpedxx'] != "") {
          $vDocPed[] = "P.O: ".$vDexDat['dexpedxx'];
        }
        ##Fin Cargo Variables para Impresion de Datos de Do ##
        break;
      case "OTROS":
        if ($vDceDat['docpedxx'] != "") {
          $vDocPed[] = "P.O: ".$vDceDat['docpedxx'];
        }
        break;
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
  $cDgeTrm = $vDatDo['dgetrmxx']; //TRM de la primera declaracion
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
  
  ##Traigo Pais del Facturado A ##
  $qPaisFac  = "SELECT $cAlfa.SIAI0052.PAIDESXX ";
  $qPaisFac .= "FROM $cAlfa.SIAI0052 ";
  $qPaisFac .= "WHERE ";
  $qPaisFac .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$vCliFac['PAIIDXXX']}\" LIMIT 0,1 ";
  $xPaisFac  = f_MySql("SELECT","",$qPaisFac,$xConexion01,"");
  if (mysql_num_rows($xPaisFac) > 0) {
    $vPaisFac = mysql_fetch_array($xPaisFac);
  }
  ##Fin Traigo Pais del Facturado A ##

  /*** Nombre del usuario de Creacion ***/
  $qUsrNom  = "SELECT USRNOMXX ";
  $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
  $qUsrNom .= "WHERE ";
  $qUsrNom .= "USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1 ";
  $xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
  $vUsrNom  = mysql_fetch_array($xUsrNom);
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
	$mIngTer = array();
  for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
    $nSwitch_Encontre_Concepto = 0;
    for ($j=0;$j<count($mIngTer);$j++) {
        if ($_POST['cComId_PCCA'.($i+1)] == $mIngTer[$j]['cComId']) {
            $nSwitch_Encontre_Concepto = 1;
            $mIngTer[$j]['cComCsc3'] .= ((strlen($mIngTer[$nInd_mIngTer]['cComCsc3'])+strlen("/".$_POST['cComCsc3_PCCA'.($i+1)])) <= 40) ? "/".$_POST['cComCsc3_PCCA'.($i+1)] : "";
            $mIngTer[$j]['nComVlr']  += $_POST['nComVlr_PCCA'.($i+1)];
            $mIngTer[$j]['nBaseIva'] += $_POST['nBaseIva_PCCA'.($i+1)];
            $mIngTer[$j]['nVlrIva']  += $_POST['nVlrIva_PCCA'.($i+1)];
            $mIngTer[$j]['cUniMed']   = ($vCtoCon["{$_POST['cComId_PCCA'.($i+1)]}"]['umeidxxx'] != '') ? $vCtoCon["{$_POST['cComId_PCCA'.($i+1)]}"]['umeidxxx'] : "A9"; // Unidad de medida
        }
    }

		if ($nSwitch_Encontre_Concepto == 0) { // No lo encontro en la matriz para pintar en la factura
			$nInd_mIngTer = count($mIngTer);
			$mIngTer[$nInd_mIngTer]['cComId']   = $_POST['cComId_PCCA'  .($i+1)];
      // Si es un pago de impuestos se cambia la descripcion
      if (in_array("{$_POST['cComId_PCCA'.($i+1)]}~{$_POST['cPucId_PCCA'.($i+1)]}", $vComImp)) {
        $mIngTer[$nInd_mIngTer]['cComObs'] = 'TRIBUTOS ADUANEROS';
      } else {
        $mIngTer[$nInd_mIngTer]['cComObs']  = $_POST['cComObs_PCCA' .($i+1)];
      }
			$mIngTer[$nInd_mIngTer]['ccomcsc3'] = $_POST['cComCsc3_PCCA'.($i+1)];
			$mIngTer[$nInd_mIngTer]['nComVlr']  = $_POST['nComVlr_PCCA' .($i+1)];
			$mIngTer[$nInd_mIngTer]['nBaseIva'] = $_POST['nBaseIva_PCCA'.($i+1)];
			$mIngTer[$nInd_mIngTer]['nVlrIva']  = $_POST['nVlrIva_PCCA'.($i+1)];
			$mIngTer[$nInd_mIngTer]['cUniMed']  = ($vCtoCon["{$_POST['cComId_PCCA'.($i+1)]}"]['umeidxxx'] != '') ? $vCtoCon["{$_POST['cComId_PCCA'.($i+1)]}"]['umeidxxx'] : "A9"; // Unidad de medida
		}
  }//for ($i=0;$i<$_POST['nSecuencia_PCCA'];$i++) {
  ##Fin Codigo para imprimir los ingresos para terceros ##

  #Agrupo Ingresos Propios
  for($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
		//Traigo las cantidades y el detalle de los IP del utiliqdo.php
		$vDatosIp = array();
		$cObs = "|".$_POST['cSerId_IPA'.($i+1)]."~".$_POST['cFcoId_IPA'.($i+1)]."~".$_POST['cComObs_IPA'.($i+1)]."|";
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
			$mIP[$_POST['cComId_IPA'.($i+1)]]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['valpdfxx'];
		}
  }//for ($k=0;$k<count($mCodDat);$k++) {

	foreach ($mIP as $cKey => $mValores) {
		$mDatIP[] = $mValores;
	}
  #Fin Agrupo Ingresos Propios

  ##Traigo la Forma de Pago##
  $cFormaPago = "";
  if ($_POST['cComFpag'] != "") {
    //Buscando descripcion
    $cFormaPago = ($_POST['cComFpag'] == 1) ? "CONTADO" : "CREDITO";
  }

  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
  class PDF extends FPDF {

    function Header() {
      global $cAlfa;   global $cRoot;   global $cPlesk_Skin_Directory;        global $vSysStr; global $_COOKIE;
      global $vResDat; global $cDocId;  global $vCliFac;  global $cComCsc;    global $dFecEmi; global $cDocTra;
      global $dHorEmi; global $dFecVen; global $vCiuFac;  global $cFormaPago; global $nValAdu; global $cTasCam;
      global $vPaisFac; 

      $posy = 5;
      $posx = 9;

      //Contenedor Principal 
      $this->SetFillColor(255, 204, 0);
      $this->Rect($posx,$posy,198,270,'DF');
      
      //Contenedor Datos Factura
      $this->SetFillColor(255, 255, 255);
      $this->Rect($posx+5,$posy+2,188,37,'DF');

      //logo
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg', $posx+8, $posy+5, 50, 15);

      //Contenedor Datos Ofe
      $cNitAduana  = number_format($vSysStr['financiero_nit_agencia_aduanas'], 0, '', '.')."-";
      $cNitAduana .= f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']);
      $this->setXY($posx+7, $posy+20);
      $this->SetFont('Arial','B',8);
      $this->Cell(50,4,"NIT ".$cNitAduana,0,0,'C');
      $this->Ln(4.5);
      $this->setX($posx+7);
      $this->SetFont('Arial','',8);
      $this->Cell(50,4,utf8_decode('Carrera 85D # 46A 38'),0,0,'C');
      $this->Ln(4.5);
      $this->setX($posx+7);
      $this->Cell(50,4,utf8_decode('Teléfono: 7477777'),0,0,'C');
      $this->Ln(4.5);
      $this->setX($posx+7);
      $this->Cell(50,4,utf8_decode('BOGOTA, D.C'),0,0,'C');

      //Resolucion DIAN
      //Traigo numero de Meses entre Desde y Hasta
      $dFechaInicial  = date_create($vResDat['resfdexx']);
      $dFechaFinal    = date_create($vResDat['resfhaxx']);
      $nDiferencia    = date_diff($dFechaInicial, $dFechaFinal);
      $nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m;

      $this->setXY($posx+75, $posy+5);
      $this->SetFont('Arial','B',7);
      $this->Cell(50,4,utf8_decode('AGENCIA DE ADUANAS DHL EXPRESSCOLOMBIA LTDA NIVEL 1'),0,0,'C');
      $this->Ln(4);
      $this->SetFont('Arial','',7);
      $this->setX($posx+75);
      $this->Cell(50,4,utf8_decode("Resolución DIAN No. ").$vResDat['residxxx'],0,0,'C');
      $this->Ln(4);
      $this->setX($posx+75);
      $this->Cell(50,4,utf8_decode("Del ". $vResDat['resfdexx'] ." DEL No. ". $vResDat['resprexx'].$vResDat['resdesxx'] ." AL No. ". $vResDat['resprexx'].$vResDat['reshasxx']), 0, 0, 'C');
      $this->Ln(4);
      $this->setX($posx+75);
      $this->Cell(50,4,utf8_decode("Vigencia ". $nMesesVigencia ." meses"), 0, 0, 'C');
      $this->Ln(4);
      $this->setX($posx+75);
      $this->SetFont('Arial','B',7);
      $this->Cell(50,4,utf8_decode("FACTURACIÓN ELECTRÓNICA"), 0, 0, 'C');

      $this->Ln(5);
      $this->setX($posx+75);
      $this->SetFont('Arial','B',7);
      $this->Cell(50,4,utf8_decode("REGIMEN COMÚN"), 0, 0, 'C');
      $this->Ln(4);
      $this->setX($posx+75);
      $this->Cell(50,4,utf8_decode("AUTORRETENEDORES SEGÚN RESOLUCIÓN"), 0, 0, 'C');
      $this->Ln(4);
      $this->setX($posx+75);
      $this->Cell(50,4,utf8_decode("No. 3654 DEL 28 DE ABRIL DEL 2008"), 0, 0, 'C');

      //Tabla de Datos Factura
      //Rectangulo Rojo
      $this->SetFillColor(128, 0, 0);
      $this->RoundedRect($posx+143, $posy+2, 50, 5, 1, '1234','F');
      $this->setXY($posx+143, $posy+3);
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(255, 255, 255);
      $this->Cell(50,4,utf8_decode("FACTURA ELECTRÓNICA DE VENTA No."),0,0,'C');

      //Rectangulo Gris
      //Numero de Documento
      $this->SetFillColor(206, 210, 225);
      $this->RoundedRect($posx+143, $posy+7, 50, 5, 1, '34','F');
      $this->setXY($posx+143,$posy+8);
      $this->SetFont('Arial','B',8);
      $this->SetTextColor(0, 0, 0);
      $this->Cell(50,4,"XXXX",0,0,'C');

      //Rectangulo Rojo FECHA
      $this->SetFillColor(128, 0, 0);
      $this->RoundedRect($posx+143, $posy+12, 50, 4, 1, '1234','F');
      $this->setXY($posx+143,$posy+12);
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(255, 255, 255);
      $this->Cell(50,4,"FECHA DOCUMENTO",0,0,'C');
      $this->SetTextColor(0, 0, 0);

      //Rectangulo Gris FECHA
      $this->SetFillColor(206, 210, 225);
      $this->RoundedRect($posx+143,$posy+16, 50, 9, 1, '34','F');

      //Separadores color Gris de los valores de la Fecha del Documento
      $this->SetDrawColor(255, 255, 255);
      $this->SetLineWidth(0.4);
      $this->Line($posx+143,$posy+20,$posx+192.8,$posy+20);
      $this->Line($posx+156.5,$posy+16.3,$posx+156.5,$posy+25);
      $this->Line($posx+169,$posy+16.3,$posx+169,$posy+25);
      $this->Line($posx+181.5,$posy+16.3,$posx+181.5,$posy+25);

      $this->setXY($posx+143,$posy+16);
      $this->SetFont('Arial','B',6);
      $this->SetTextColor(0, 0, 0);
      $this->Cell(15,4,utf8_decode("DÍA"),0,0,'C');
      $this->setX($posx+155);
      $this->Cell(15,4,utf8_decode("MES"),0,0,'C');
      $this->setX($posx+168);
      $this->Cell(15,4,utf8_decode("AÑO"),0,0,'C');
      $this->setX($posx+180);
      $this->Cell(15,4,utf8_decode("HORA"),0,0,'C');

      //Valores Fecha de Documento
      $this->setXY($posx+143, $posy+21);
      $this->SetFont('Arial','',8);
      $this->SetTextColor(0, 0, 0);
      $this->Cell(15,4,date('d', strtotime($dFecEmi)),0,0,'C');
      $this->setX($posx+155);
      $this->Cell(15,4,date('m', strtotime($dFecEmi)),0,0,'C');
      $this->setX($posx+168);
      $this->Cell(15,4,date('Y', strtotime($dFecEmi)),0,0,'C');
      $this->setX($posx+180);
      $this->Cell(15,4,date('H:i', strtotime($dHorEmi)),0,0,'C');

      //Rectangulo Rojo VENCIMIENTO
      $this->SetFillColor(128, 0, 0);
      $this->RoundedRect($posx+143, $posy+25, 50, 4, 1, '1234','F');
      $this->setXY($posx+143, $posy+25);
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(255, 255, 255);
      $this->Cell(50,4,"FECHA VENCIMIENTO",0,0,'C');
      $this->SetTextColor(0, 0, 0);

      //Rectangulo Gris VENCIMIENTO
      $this->SetFillColor(206, 210, 225);
      $this->RoundedRect($posx+143, $posy+29, 50, 9.5, 1, '34','F');

      //Separadores color Gris de los valores de la Fecha del Documento
      $this->SetDrawColor(255, 255, 255);
      $this->SetLineWidth(0.4);
      $this->Line($posx+143,$posy+33,$posx+192.8,$posy+33);
      $this->Line($posx+156.5,$posy+29.3,$posx+156.5,$posy+38);
      $this->Line($posx+169,$posy+29.3,$posx+169,$posy+38);
      $this->Line($posx+181.5,$posy+29.3,$posx+181.5,$posy+38);

      $this->setXY($posx+143,$posy+29);
      $this->SetFont('Arial','B',6);
      $this->SetTextColor(0, 0, 0);
      $this->Cell(15,4,utf8_decode("DÍA"),0,0,'C');
      $this->setX($posx+155);
      $this->Cell(15,4,utf8_decode("MES"),0,0,'C');
      $this->setX($posx+168);
      $this->Cell(15,4,utf8_decode("AÑO"),0,0,'C');
      $this->setX($posx+180);
      $this->Cell(15,4,utf8_decode("HORA"),0,0,'C');

      //Valores Fecha de Documento
      $this->setXY($posx+143, $posy+34);
      $this->SetFont('Arial','',8);
      $this->SetTextColor(0, 0, 0);
      $this->Cell(15,4,date('d', strtotime($dFecVen)),0,0,'C');
      $this->setX($posx+155);
      $this->Cell(15,4,date('m', strtotime($dFecVen)),0,0,'C');
      $this->setX($posx+168);
      $this->Cell(15,4,date('Y', strtotime($dFecVen)),0,0,'C');
      $this->setX($posx+180);
      $this->Cell(15,4,'00:00',0,0,'C');

      /*****  Datos Cliente FC *****/
      $posy = $this->GetY()+7;
      $this->SetFillColor(255, 255, 255);
      $this->RoundedRect($posx+4,$posy, 189, 30, 2, '1234','F');

      //Columna 1
      $this->setXY($posx+7,$posy+1);
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(0, 0, 0);
      $this->Cell(20, 4,utf8_decode("SEÑORES:"),0,0,'L');
      $this->SetFont('Arial','',7);
      $this->Cell(150,4,utf8_decode($vCliFac['CLINOMXX']),0,0,'L');
      $this->Ln(4);
      $this->setX($posx+7);
      $this->SetFont('Arial','B',7);
      $this->Cell(20,4,utf8_decode("DIRECCIÓN:"),0,0,'L');
      $this->SetFont('Arial','',7);
      $this->MultiCell(108,3,utf8_decode($vCliFac['CLIDIRXX']),0,'L');
      $this->Ln(1);
      $this->setX($posx+7);
      $this->SetFont('Arial','B',7);
      $this->Cell(20,4,"CIUDAD:",0,0,'L');
      $this->SetFont('Arial','',7);
      $this->Cell(55,4,utf8_decode($vCiuFac['CIUDESXX']),0,0,'L');
      $this->Ln(4);
      $this->setX($posx+7);
      $this->SetFont('Arial','B',7);
      $this->Cell(20,4,"NIT:",0,0,'L');
      $this->SetFont('Arial','',7);
      $this->Cell(45,4,number_format($vCliFac['CLIIDXXX'], 0, '', '.'). "-" .f_Digito_Verificacion($vCliFac['CLIIDXXX']), 0, 0, 'L');
      $this->Ln(4);
      $this->setX($posx+7);
      $this->SetFont('Arial','B',7);
      $this->Cell(20, 4,"TELEFONO:",0,0,'L');
      $this->SetFont('Arial','',7);
      $this->Cell(55,4,$vCliFac['CLITELXX'],0,0,'L');

      //Columna 2
      $posx += 135;
      $this->setXY($posx,$posy+1);
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(0, 0, 0);
      $this->Cell(27,4,"DO:",0,0,'L');
      $this->SetFont('Arial','',7);
      $this->Cell(150,4,$cDocId,0,0,'L');
      $this->Ln(4);
      $this->setX($posx);
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(0, 0, 0);
      $this->Cell(27,4,"GUIA AEREA:",0,0,'L');
      $this->SetFont('Arial','',7);
      $this->Cell(150,4,$cDocTra,0,0,'L');
      $this->Ln(4);
      $this->setX($posx);
      $this->SetFont('Arial','B',7);
      $this->Cell(27,4,"VALOR CIF:",0,0,'L');
      $this->Ln(0.5);
      $this->setX($posx+27);
      $this->SetFont('Arial','',7);
      $this->MultiCell(30,3,number_format($nValAdu, 0, '.', ','),0,'L');
      $this->Ln(1);
      $this->setX($posx);
      $this->SetFont('Arial','b',7);
      $this->Cell(27,4,"TASA DE CAMBIO:",0,0,'L');
      $this->SetFont('Arial','',7);
      $this->Cell(50,4,number_format($cTasCam, 0, '.', ','),0,0,'L');
      $this->Ln(4);
      $this->setX($posx);
      $this->SetFont('Arial','B',7);
      $this->Cell(27,4,"FORMA DE PAGO:",0,0,'L');
      $this->Ln(0.5);
      $this->setX($posx+27);
      $this->SetFont('Arial','',7);
      $this->MultiCell(30,3,$cFormaPago,0,'L');
      $this->Ln(1);
      $this->setX($posx);
      $this->SetFont('Arial','B',7);
      $this->Cell(27,4,"MEDIO DE PAGO:",0,0,'L');
      $this->SetFont('Arial','',7);
      $this->Cell(30,4,$_POST['cMePagDes'],0,0,'L');

      //Cabecera de los Conceptos
      $posy = $posy+33;
      $posx = 13;
      $this->SetFillColor(128, 0, 0);
      $this->RoundedRect($posx,$posy, 189, 5, 1, '1234', 'F');

      $this->setXY($posx,$posy+1);
      $this->SetFont('Arial','B',7);
      $this->SetTextColor(255, 255, 255);
      $this->Cell(9,4,"ITEM",0,0,'L');
      $this->Cell(25,4,"COD. PRODUCTO",0,0,'L');
      $this->Cell(78,4,utf8_decode("DESCRIPCIÓN"),0,0,'C');
      $this->Cell(15,4,"UNIDAD",0,0,'L');
      $this->Cell(12,4,"CANTIDAD",0,0,'C');
      $this->Cell(23,4,"VR. UNITARIO",0,0,'R');
      $this->Cell(25,4,"VALOR",0,0,'R');

      $this->nPosYIni = $posy+7;

      //Rectangulo que contiene los conceptos
      $posy = $this->GetY()+4;
      $this->SetFillColor(255, 255, 255);
      $this->Rect($posx,$posy,189,150,'F');
      $this->SetDrawColor(0,0,0);

      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',33,85,145,145);

      $this->Line($posx+165,$posy,$posx+165,$posy+121,'F');
      $this->Line($posx,$posy+90,$posx+189,$posy+90);

    }//Function Header
    
    function Footer(){
      global $vCocDat; global $dir; global $vSysStr; global $vUsrNom; global $cPlesk_Skin_Directory; global $vTexCli;

      $posy = 175;
      $posx = 13;

      $this->SetLineWidth(0.4);
      $this->Line($posx+123,$posy-1,$posx+123,$posy+30);
      $this->setXY($posx+1,$posy); 
      $this->SetFont('Arial','B',7);
      $this->Cell(25,4,"OBSERVACIONES: ",0,0,'L');
      $this->SetFont('Arial','',8);
      $this->MultiCell(100,4,"",0,'L');

      $this->setXY($posx+125,$posy); 
      $this->SetFont('Arial','B',7);
      $this->Cell(25,5,"TOTAL",0,0,'L');
      $this->SetFont('Arial','',8);
      $this->Cell(33,5,"",0,0,'R');
      $this->ln(4);
      $this->setX($posx+125); 
      $this->SetFont('Arial','B',7);
      $this->Cell(25,5,"IVA 19 %",0,0,'L');
      $this->SetFont('Arial','',8);
      $this->Cell(33,5,"",0,0,'R');
      $this->ln(4);
      $this->setX($posx+125); 
      $this->SetFont('Arial','B',7);
      $this->Cell(25,5,"RETEIVA",0,0,'L');
      $this->SetFont('Arial','',8);
      $this->Cell(33,5,"",0,0,'R');
      $this->ln(4);
      $this->setX($posx+125); 
      $this->SetFont('Arial','B',7);
      $this->Cell(25,5,"RETEICA",0,0,'L');
      $this->SetFont('Arial','',8);
      $this->Cell(33,5,"",0,0,'R');
      $this->ln(4);
      $this->setX($posx+125); 
      $this->SetFont('Arial','B',7);
      $this->Cell(25,5,"TOTAL",0,0,'L');
      $this->SetFont('Arial','',8);
      $this->Cell(33,5,"",0,0,'R');
      $this->ln(4);
      $this->setX($posx+125); 
      $this->SetFont('Arial','B',7);
      $this->Cell(25,5,"TOTAL A PAGAR",0,0,'L');
      $this->SetFont('Arial','',8);
      $this->Cell(33,5,"",0,0,'R');
      $this->ln(4);
      $this->setX($posx+125); 
      $this->SetFont('Arial','B',7);
      $this->Cell(25,5,"SALDO A SU FAVOR",0,0,'L');
      $this->SetFont('Arial','',8);
      $this->Cell(33,5,"",0,0,'R');

      $this->Line($posx,$posy+30,$posx+189,$posy+30);

      $posy += 30;
      $this->setXY($posx,$posy); 
      $this->SetTextColor(255, 51, 51);
      $this->SetFont('Arial','B',7);
      $this->Cell(25,5,$vTexCli['tfvtitxx'],0,0,'L');
      $this->SetTextColor(0, 0, 0);
      
      $posy += 35;
      $this->setXY($posx+55,$posy);
      $this->SetFont('Arial','B',7);
      $this->Cell(60,3,utf8_decode("REPRESENTACIÓN GRAFICA DE LA FACTURA"),0,0,'C');
      $this->Ln(4);

      //Contenedor Contáctenos
      $this->SetFillColor(255, 255, 255);            
      $this->Rect($posx+131, $posy, 58, 28, 'DF');
      $this->SetFont('Arial','B',8);
      $this->setXY($posx+133.5,$posy+3);
      $this->Cell(50,3,utf8_decode("¿Tiene dudas sobre este documento?"),0,0,'L');
      $this->Ln(7);
      $this->setX($posx+131);
      $this->SetFont('Arial','B',7);
      $this->Cell(50,3,utf8_decode("Línea Nacional 018000183345 Opc 3"),0,0,'L');
      $this->Ln(4);
      $this->setX($posx+131);
      $this->SetFont('Arial','B',7);
      $this->Cell(50,3,utf8_decode("Línea de atención en Bogotá 6017477777 Opc 3"),0,0,'L');
      $this->Ln(4);
      $this->setX($posx+131);
      $this->SetFont('Arial','B',7);
      $this->Cell(50,3,utf8_decode("Correo Electrónico: DHLcobranzasCO@dhl.com"),0,0,'L');
      $this->Ln(4);
      $this->setX($posx+131);
      $this->SetFont('Arial','U',7);
      $this->Cell(50,3,utf8_decode("https://aduanasdhlexpress.dhl.com"),0,0,'L');
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

    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '') {
      $k = $this->k;
      $hp = $this->h;
      if($style=='F')
          $op='f';
      elseif($style=='FD' || $style=='DF')
          $op='B';
      else
          $op='S';
      $MyArc = 4/3 * (sqrt(2) - 1);
      $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

      $xc = $x+$w-$r;
      $yc = $y+$r;
      $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
      if (strpos($corners, '2')===false)
          $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
      else
          $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

      $xc = $x+$w-$r;
      $yc = $y+$h-$r;
      $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
      if (strpos($corners, '3')===false)
          $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
      else
          $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

      $xc = $x+$r;
      $yc = $y+$h-$r;
      $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
      if (strpos($corners, '4')===false)
          $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
      else
          $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

      $xc = $x+$r ;
      $yc = $y+$r;
      $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
      if (strpos($corners, '1')===false)
      {
          $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
          $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
      }
      else
          $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
      $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
      $h = $this->h;
      $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
          $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }
  }

  $pdf=new PDF('P','mm','Letter');
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->SetFont('verdana','',8);
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);

  $pdf->AddPage();
  $posy	     = $pdf->nPosYIni;
  $posx	     = 13;
  $posfin    = 160;
	$nCounItem = 0;
  $nTotalPcc = 0;
  $nTotalIP  = 0;

  // $mIngTer = array_merge($mIngTer, $mIngTer, $mIngTer);
  // $mIngTer = array_merge($mIngTer, $mIngTer, $mIngTer);

  ## Imprimo los pagos a terceros ##
  if(count($mIngTer) > 0){
    $pdf->setXY($posx+8,$posy);
    $pdf->SetFont('arial','B',7);
    $pdf->Cell(140,5,"PAGOS A TERCEROS",0,0,'L');
    $pdf->SetFont('arial','',7);
    $posy+=5;

    $pdf->SetWidths(array(8,23,80,15,12,25,25));
    $pdf->SetAligns(array("C","C","L","C","C","R","R"));
    $pdf->setXY($posx,$posy);

    for($i=0;$i<count($mIngTer);$i++){
      if($posy > $posfin){
        $pdf->AddPage();
        $posy = $pdf->nPosYIni;
        $posx	= 13;
        $pdf->setXY($posx,$posy);
      }

      //Consulto la descripcion de la Unidad de medida
			$qUniMedi  = "SELECT umedesxx ";
			$qUniMedi .= "FROM $cAlfa.fpar0157 ";
			$qUniMedi .= "WHERE ";
			$qUniMedi .= "umeidxxx = \"{$mIngTer[$i]['cUniMed']}\" LIMIT 0,1";
			$xUniMedi  = mysql_query($qUniMedi, $xConexion01);
			//f_Mensaje(__FILE__,__LINE__,$qUniMedi." ~ ".mysql_num_rows($xUniMedi));
			while ($xRUM = mysql_fetch_assoc($xUniMedi)) {
				$cUniMedi = $xRUM['umedesxx'];
			}

      $cComObs     = explode("^",$mIngTer[$i]['cComObs']);
      $cComObs_PCC = str_replace("CANTIDAD", "CANT", $cComObs[0]);

			$nCounItem++;
      $nTotalPcc   += $mIngTer[$i]['nComVlr'];

      $pdf->SetFont('arial','',7);
      $pdf->setX($posx);
      $pdf->Row(array(
        $nCounItem,
        $mIngTer[$i]['cComId'],
        $cComObs_PCC,
        $cUniMedi,
        "1",
        number_format($mIngTer[$i]['nComVlr'],0,',','.'),
        number_format($mIngTer[$i]['nComVlr'],0,',','.')
      ));
      $posy += 4;
    }
    $posy += 4;
  }
  ## Fin Imprimo los pagos a terceros ##

  if($posy > $posfin){
    $pdf->AddPage();
    $posx	= 13;
    $posy = $pdf->nPosYIni;
    $pdf->setXY($posx,$posy);
  }

  ##Imprimo Ingresos Propios##
	if($_POST['nSecuencia_IPA'] > 0){
		$pdf->setXY($posx+12,$posy);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(40,5,utf8_decode("INGRESOS PROPIOS"),0,0,'L');
    $pdf->SetFont('arial','',7);
    $posy+=5;

    $pdf->SetWidths(array(8,23,80,15,12,25,25));
    $pdf->SetAligns(array("C","C","L","C","C","R","R"));
    $pdf->setXY($posx,$posy);
		
		for($k=0;$k<count($mDatIP);$k++){
			if($posy > $posfin){
        $pdf->AddPage();
        $posx	= 13;
        $posy = $pdf->nPosYIni;
        $pdf->setXY($posx,$posy);
      }

			//Consulto la descripcion de la Unidad de medida
			$qUniMedi  = "SELECT umedesxx ";
			$qUniMedi .= "FROM $cAlfa.fpar0157 ";
			$qUniMedi .= "WHERE ";
			$qUniMedi .= "umeidxxx = \"{$mDatIP[$k]['unidadfe']}\" LIMIT 0,1";
			$xUniMedi  = mysql_query($qUniMedi, $xConexion01);
			//f_Mensaje(__FILE__,__LINE__,$qUniMedi." ~ ".mysql_num_rows($xUniMedi));
			while ($xRUM = mysql_fetch_assoc($xUniMedi)) {
				$cUniMedi = $xRUM['umedesxx'];
			}

			$nTotalIP  += $mDatIP[$k]['comvlrxx'];
			$nValorUni = ($mDatIP[$k]['unidadfe'] != "A9" && $mDatIP[$k]['canfexxx'] > 0) ? $mDatIP[$k]['comvlrxx']/$mDatIP[$k]['canfexxx'] : $mDatIP[$k]['comvlrxx'];
			$nCounItem++;

			$pdf->SetFont('Arial','',7);
			$pdf->setX($posx);
			$pdf->Row(array(
				$nCounItem,
				$mDatIP[$k]['ctoidxxx'],
				utf8_decode(trim($mDatIP[$k]['comobsxx'])),
				utf8_decode($cUniMedi),
				number_format($mDatIP[$k]['canfexxx'],0,'.',','),
				number_format($nValorUni,0,',','.'),
				number_format($mDatIP[$k]['comvlrxx'],0,',','.'))
			);
      $posy += 4;
		}//for($k=0;$k<count($mDatIP);$k++){
	}//($_POST['nSecuencia_IPA'] > 0){
	##Fin Imprimo Ingresos Propios ##

  $pdf->SetFont('Arial', '', 7);
  $pdf->setXY($posx,$posy);
  $pdf->Cell(8,4,$nCounItem,'T',0,'C');

  if($posy > $posfin){
    $pdf->AddPage();
    $posx	= 13;
    $posy = $pdf->nPosYIni;
    $pdf->setXY($posx,$posy);
  }

  if (count($vDocPed) > 0) {
    $posy += 10;
    $pdf->SetFont('Arial', '', 7);
    $pdf->setXY($posx+10,$posy);
    for ($i=0; $i <count($vDocPed); $i++) {
      if($posy > ($posfin+5)){
        $pdf->AddPage();
        $posx	= 13;
        $posy = $pdf->nPosYIni;
        $pdf->setXY($posx+10,$posy);
      }
      $pdf->setXY($posx+10,$posy);
      $pdf->Cell(150, 4,utf8_decode($vDocPed[$i]), 0, 0, 'L');
      $posy += 4;
    }
    $posy -= 4;
  }

  if(count($cDosInc) > 1) {
    $posy += 10;
    if($posy > $posfin){
      $pdf->AddPage();
      $posx	= 13;
      $posy = $pdf->nPosYIni;
      $pdf->setXY($posx,$posy);
    }

    $pdf->SetFont('Arial', 'B', 7);
    $pdf->setXY($posx+10,$posy);
    $pdf->Cell(40, 4,"Numeros DO incluidos en esta factura", 0, 0, 'L');
    $posy += 3;
    $pdf->SetFont('Arial', '', 7);
    $pdf->setXY($posx+10,$posy);
    $pdf->MultiCell(155, 4, implode(", ", $cDosInc), 0, 'L');
  }

  if($posy > $posfin){
    $pdf->AddPage();
    $posx	= 13;
    $posy = $pdf->nPosYIni;
    $pdf->setXY($posx,$posy);
  }

  ### Calculo los subtotales ###
  $nSubTotal = $nTotalPcc + $nTotalIP;

  ##Busco Valor de RET.IVA ##
	$nTotRteIva = 0;
	for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
		$nTotRteIva +=$_POST['nVlrIva_IPA'.($i+1)];
	}
	##Fin Busco Valor de RET.IVA ##

	##Busco Valor de RET.ICA ##
	$nTotRteIca = 0;
	for ($i=0;$i<($_POST['nSecuencia_IPA']);$i++) {
		$nTotRteIca +=$_POST['nVlrIca_IPA'.($i+1)];
	}
	##Fin Busco Valor de RET.ICA ##

  if ($_POST['nIPASal'] > 0) {
    $nTotalPagar = $_POST['nIPASal'];
  } else {
    $nSaldoFavor = abs($_POST['nIPASal']);
  }

  $nTotAnt = 0;
  for($k=0;$k<count($mDatIP);$k++){
    if($mDatIP[$k]['comctocx'] == 'CD' && strpos($mDatIP[$k]['comobsxx'],'ANTICIPOS') > 0) {
      $nTotAnt += $mDatIP[$k]['comvlrxx'];
    }
  }

  // Nota para las observaciones
  $_POST['cComObs'] .= utf8_decode(" La firma de terceros en representación del cliente, implica la aceptación de este documento. Código de actividad económica CIIU 5229. GEN - BASE PARA CALCULAR IVA Y RETEICA SOBRE EL VALOR DE INGRESOS PROPIOS: "). number_format($nTotalIP, 0, '.', ',');

  ##Imprimo los valores Totales
  $posx	= 13;
  $posy = 175;

  $nTotalFactura = ($nSubTotal + $_POST['nIPAIva']) - ($nTotRteIva + $nTotRteIca);

  $pdf->SetFont('Arial', '', 7);
  $pdf->setXY($posx+163, $posy);
  $pdf->Cell(25,4, number_format($nSubTotal, 0, '.', ','), 0, 0, 'R');
  $pdf->ln(4);
  $pdf->setX($posx+163);
  $pdf->Cell(25,4, number_format($_POST['nIPAIva'], 0, '.', ','), 0, 0, 'R');
  $pdf->ln(4);
  $pdf->setX($posx+163);
  $pdf->Cell(25,4, number_format($nTotRteIva, 0, '.', ','), 0, 0, 'R');
  $pdf->ln(4);
  $pdf->setX($posx+163);
  $pdf->Cell(25,4, number_format($nTotRteIca, 0, '.', ','), 0, 0, 'R');
  $pdf->ln(4);
  $pdf->setX($posx+163);
  $pdf->Cell(25,4, number_format($nTotalFactura, 0, '.', ','), 0, 0, 'R');
  $pdf->ln(4);
  $pdf->setX($posx+163);
  $pdf->Cell(25,4, number_format($nTotalPagar, 0, '.', ','), 0, 0, 'R');
  $pdf->ln(4);
  $pdf->setX($posx+163);
  $pdf->Cell(25,4, number_format($nSaldoFavor, 0, '.', ','), 0, 0, 'R');

  // Observaciones
  $pdf->setXY($posx+1,$posy+5);
  $pdf->SetFont('Arial', '', 8);
  $pdf->MultiCell(120,4, $_POST['cComObs'],0,'L');
  $pdf->SetFont('Arial', '', 7);
  if(abs($_POST['nIPAAnt']) > 0) {
    $pdf->ln(1);
    $pdf->setX($posx+1);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(25,3, "ANTICIPOS:",0,'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(25,3, number_format(abs($_POST['nIPAAnt']), 0, '.', ','),0,'L');
  }
  if($cDgeTrm || $cDgeTrm != '') {
    $pdf->ln(3);
    $pdf->setX($posx+1);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(25,3, "TASA DE CAMBIO:",0,'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(25,3, number_format($cDgeTrm, 0, '.', ','),0,'L');
  }

  // Texto estimado cliente
  $posx	= 13;
  $posy = 210;
  $pdf->SetFont('Arial', '', 8);
  $pdf->setXY($posx, $posy);
  $cContenido = str_replace(array("// ", " //", " // "), "\n", $vTexCli['tfvcontx']);
  $pdf->MultiCell(187,4,$cContenido,0,'J');
  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	$pdf->Output($cFile);

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }

  echo "<html><script>document.location='$cFile';</script></html>";

?>

<?php
  /**
	 * Imprime Factura de Venta ADUAMARX.
	 * --- Descripcion: Permite Imprimir Factura de Venta ADUAMARX.
	 * @author Juan Jose Trujillo <juan.trujillo@open-eb.co>
	 * @version 001
	 */
  
  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");
  // date_default_timezone_set('America/Bogota');
  
  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");
  
  //Generacion del codigo QR
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/phpqrcode/qrlib.php');
  
  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
	require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
  
  $nSwitch=0;

  // Validacion de Comprobante Repetido
  $mPrints = f_Explode_Array($prints,"|","~");
  $cAno =  substr($mPrints[0][4],0,4);
  // Busco la resolucion en la tabla GRM00138.
  $qResFac  = "SELECT rescomxx ";
  $qResFac .= "FROM $cAlfa.fpar0138 ";
  $qResFac .= "WHERE ";
  $qResFac .= "rescomxx LIKE \"%{$mPrints[0][0]}~{$mPrints[0][1]}%\" AND ";
  $qResFac .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  $xResFac  = f_MySql("SELECT","",$qResFac,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,$qResFac." ~ ".mysql_num_rows($xResFac));
  $mResFac = mysql_fetch_array($xResFac);
  // Fin de Busco la resolucion en la tabla GRM00138.

	// Armo la variable con los codigos de los comprobantes de facturacion para la actualizacion del consecutivo despues de grabar la factura.
  $mCodCom = f_Explode_Array($mResFac['rescomxx'],"|","~");
  $cCodigos_Comprobantes = "";
  for ($i=0;$i<count($mCodCom);$i++) {
		$cCodigos_Comprobantes .= "\"";
		$cCodigos_Comprobantes .= "{$mCodCom[$i][1]}";
		$cCodigos_Comprobantes .= "\"";
		if ($i < (count($mCodCom) -1)) { 
			$cCodigos_Comprobantes .= ","; 
		}
  }
  // Fin de Armo la variable con los codigos de los comprobantes de facturacion para la actualizacion del consecutivo despues de grabar la factura.

  $qValCsc  = "SELECT comidxxx,comcodxx,comcscxx,comcsc2x ";
	$qValCsc .= "FROM $cAlfa.fcoc$cAno ";
	$qValCsc .= "WHERE ";
	$qValCsc .= "comidxxx = \"{$mPrints[0][0]}\"  AND ";
	$qValCsc .= "comcodxx IN ($cCodigos_Comprobantes) AND ";
	$qValCsc .= "comcscxx = \"{$mPrints[0][2]}\"";
	$xValCsc  = f_MySql("SELECT","",$qValCsc,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,$qValCsc." ~ ".mysql_num_rows($xValCsc));
	if (mysql_num_rows($xValCsc) > 1) {
		$nSwitch = 1;
		f_Mensaje(__FILE__,__LINE__,"El Documento [{$mPrints[0][0]}-{$mPrints[0][1]}-{$mPrints[0][2]}] No se puede Imprimir porque su Consecutivo se Encuentra Repetido en el Modulo de Contabilidad, Verifique");
	}
  // Fin de Validacion de Comprobante Repetido

  $vMemo=explode("|",$prints);
  $permisos=0;
  $zCadPer="|";
  $resolucion=0;
  $zCadRes="|";
  ///////////////////////
  $fomularios=0;
  $zCadFor="";
  ///////////////////////
  $diferencia=0;

  $paso=0;
  for($u=0; $u<count($vMemo); $u++) {
		if ($vMemo[$u]!=""){
			$zMatriz=explode("~",$vMemo[$u]);

      ////// CABECERA 1001 /////
			$qCocDat  = "SELECT * ";
			$qCocDat .= "FROM $cAlfa.fcoc$cAno ";
			$qCocDat .= "WHERE $cAlfa.fcoc$cAno.comidxxx = \"{$zMatriz[0]}\" AND ";
			$qCocDat .= "$cAlfa.fcoc$cAno.comcodxx = \"{$zMatriz[1]}\" AND ";
			$qCocDat .= "$cAlfa.fcoc$cAno.comcscxx = \"{$zMatriz[2]}\" AND ";
			$qCocDat .= "$cAlfa.fcoc$cAno.comcsc2x = \"{$zMatriz[3]}\" LIMIT 0,1";
			//f_Mensaje(__FILE__,__LINE__,$qCocDat);
			$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
			$nFilCoc  = mysql_num_rows($xCocDat);
			if ($nFilCoc > 0) {
				$vCocDat  = mysql_fetch_array($xCocDat);
				if($vCocDat['comprnxx']=="IMPRESO" && $vCocDat['regestxx'] != "INACTIVO" && $_COOKIE['kModo'] != "VERFACTURA"){
					$zFac=$zMatriz[0].$zMatriz[1]."-".$zMatriz[2]."|";
					$zCadPer .=$zFac;
					$permisos=1;
				}
			}
		}
  }

  if($permisos==1){
	  $nSwitch=1;
		f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas No tienen Permiso de Impresion [$zCadPer], Verifique.");?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
		<script languaje = "javascript">
			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			document.forms['frgrm'].submit();
		</script>
		<?
  }
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($fomularios==1){
		$nSwitch=1;
		f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas Presentan Inconsistencias con Formularios: \n $zCadFor --- Verifique --- ");?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
		<script languaje = "javascript">
			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			document.forms['frgrm'].submit();
		</script>
		<?
  }
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($resolucion==1){
		$nSwitch=1;
		f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas no Tienen Resolucion Activa [$zCadRes], Verifique."); ?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
		<script languaje = "javascript">
			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			document.forms['frgrm'].submit();
		</script>
		<?
  }

  $mPrn = explode("|",$prints);
  for ($nn=0;$nn<count($mPrn);$nn++) {
    if (strlen($mPrn[$nn]) > 0) {
      $vComp = explode("~",$mPrn[$nn]);
      $cComId   = $vComp[0];
      $cComCod  = $vComp[1];
      $cComCsc  = $vComp[3];
      $cComCsc2 = $vComp[3];
      $cRegFCre = $vComp[4];
      $cAno     =  substr($cRegFCre,0,4);
    }
  }
  
  // Codigo para actualizar campo de impresion
  if($vCocDat['regestxx'] != "INACTIVO" && $_COOKIE['kModo'] != "VERFACTURA") {
    $mUpdPrn = array(array('NAME'=>'comprnxx','VALUE'=>'IMPRESO'  ,'CHECK'=>'SI'),
                      array('NAME'=>'comidxxx','VALUE'=>$cComId    ,'CHECK'=>'WH'),
                      array('NAME'=>'comcodxx','VALUE'=>$cComCod   ,'CHECK'=>'WH'),
                      array('NAME'=>'comcscxx','VALUE'=>$cComCsc   ,'CHECK'=>'WH'),
                      array('NAME'=>'comcsc2x','VALUE'=>$cComCsc2  ,'CHECK'=>'WH'));
    if (f_MySql("UPDATE","fcoc$cAno",$mUpdPrn,$xConexion01,$cAlfa)){
    }else{
      $nSwitch = 1;
    }
  }
  // Codigo para actualizar campo de impresion

  ////// CABECERA 1001 /////
  $qCocDat  = "SELECT ";
  $qCocDat .= "$cAlfa.fcoc$cAno.*, ";
  $qCocDat .= "IF($cAlfa.fpar0008.sucidxxx != \"\",$cAlfa.fpar0008.sucidxxx,\"SUCURSAL SIN ID\") AS sucidxxx, ";
  $qCocDat .= "IF($cAlfa.fpar0008.sucdesxx != \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS CLINOMXX, ";
  $qCocDat .= "$cAlfa.A.CLINRPXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CLICONTX != \"\",$cAlfa.SIAI0150.CLICONTX,\"SIN RESPONSABLE\") AS CLICONTX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX, ";
  $qCocDat .= "IF($cAlfa.A.CLINOMXX != \"\",$cAlfa.A.CLINOMXX,\" SIN NOMBRE\") AS CLIINTXX, ";
  $qCocDat .= "IF($cAlfa.A.CLIDIRXX != \"\",$cAlfa.A.CLIDIRXX,\" SIN DIRECCION\") AS CLIDIRIX, ";
  $qCocDat .= "IF($cAlfa.A.CLITELXX != \"\",$cAlfa.A.CLITELXX,\" SIN TELEFONO\") AS CLITELIX, ";
  $qCocDat .= "IF($cAlfa.A.PAIIDXXX != \"\",$cAlfa.A.PAIIDXXX,\"\") AS PAIIDIXX, ";
  $qCocDat .= "IF($cAlfa.A.DEPIDXXX != \"\",$cAlfa.A.DEPIDXXX,\"\") AS DEPIDIXX, ";
  $qCocDat .= "IF($cAlfa.A.CIUIDXXX != \"\",$cAlfa.A.CIUIDXXX,\"\") AS CIUIDIXX ";
  $qCocDat .= "FROM $cAlfa.fcoc$cAno ";
  $qCocDat .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcoc$cAno.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
  $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
  $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.fcoc$cAno.terid2xx = $cAlfa.A.CLIIDXXX ";
  $qCocDat .= "WHERE $cAlfa.fcoc$cAno.comidxxx = \"$cComId\" AND ";
  $qCocDat .= "$cAlfa.fcoc$cAno.comcodxx = \"$cComCod\" AND ";
  $qCocDat .= "$cAlfa.fcoc$cAno.comcscxx = \"$cComCsc\" AND ";
  $qCocDat .= "$cAlfa.fcoc$cAno.comcsc2x = \"$cComCsc2\" LIMIT 0,1";

  $xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
  $nFilCoc  = mysql_num_rows($xCocDat);
  if ($nFilCoc > 0) {
    $vCocDat  = mysql_fetch_array($xCocDat);
    //$result = array_merge($vCocDat,$vCocDat);
    
  }

  ##Traigo Ciudad del Facturado A ##
	$qCiuFac  = "SELECT CIUDESXX ";
	$qCiuFac .= "FROM $cAlfa.SIAI0055 ";
	$qCiuFac .= "WHERE ";
	$qCiuFac .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
	$qCiuFac .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
	$qCiuFac .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCocDat['CIUIDXXX']}\" AND ";
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

  ////// DETALLE 1002 /////
  $qCodDat  = "SELECT DISTINCT ";
  $qCodDat .= "$cAlfa.fcod$cAno.*, ";
  $qCodDat .= "$cAlfa.sys00121.docmtrxx AS docmtrxx ";
  $qCodDat .= "FROM $cAlfa.fcod$cAno ";
  $qCodDat .= "LEFT JOIN $cAlfa.sys00121 ON $cAlfa.fcod$cAno.comcsccx = $cAlfa.sys00121.docidxxx AND $cAlfa.fcod$cAno.comseqcx = $cAlfa.sys00121.docsufxx  ";
  $qCodDat .= "WHERE $cAlfa.fcod$cAno.comidxxx = \"$cComId\" AND ";
  $qCodDat .= "$cAlfa.fcod$cAno.comcodxx = \"$cComCod\" AND ";
  $qCodDat .= "$cAlfa.fcod$cAno.comcscxx = \"$cComCsc\" AND ";
  $qCodDat .= "$cAlfa.fcod$cAno.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cAno.comseqxx) ASC ";
  $xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
  $nFilCod  = mysql_num_rows($xCodDat);
  if($nFilCod > 0) {
    // Cargo la Matriz con los ROWS del Cursor
    $iA=0;
    while ($xRCD = mysql_fetch_array($xCodDat)) {
      $mCodDat[$iA] = $xRCD;
      $iA++;
    }
  }

  $cDocId  = "";
  $cDocSuf = "";
  $mDoiId = explode("|",$vCocDat['comfpxxx']);
  for ($i=0;$i<count($mDoiId);$i++) {
    if($mDoiId[$i] != ""){
      $vDoiId  = explode("~",$mDoiId[$i]);
      $cDocId  = $vDoiId[2];
      $cDocSuf = $vDoiId[3];
      $cSucId  = $vDoiId[15];
      $i = count($mDoiId);
    }
  }

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

  $qSucDat  = "SELECT ";
  $qSucDat .= "sucdesxx ";
  $qSucDat .= "FROM $cAlfa.fpar0008 ";
  $qSucDat .= "WHERE ";
  $qSucDat .= "sucidxxx = \"{$vDceDat['sucidxxx']}\" AND ";
  $qSucDat .= "regestxx = \"ACTIVO\" ";
  $xSucDat  = f_MySql("SELECT","",$qSucDat,$xConexion01,"");
  $nFilSuc  = mysql_num_rows($xSucDat);
  if ($nFilSuc > 0) {
    $vSucDat = mysql_fetch_array($xSucDat);
  }

  $qUsrDat  = "SELECT ";
  $qUsrDat .= "IF(USRNOMXX != \"\",USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
  $qUsrDat .= "FROM $cAlfa.SIAI0003 ";
  $qUsrDat .= "WHERE ";
  $qUsrDat .= "USRIDXXX = \"{$vCocDat['regusrxx']}\" LIMIT 0,1 ";
  $xUsrDat  = f_MySql("SELECT","",$qUsrDat,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qUsrDat."~".mysql_num_rows($xUsrDat));
  $nFilUsr  = mysql_num_rows($xUsrDat);
  if ($nFilUsr > 0) {
    $vUsrDat = mysql_fetch_array($xUsrDat);
  }
  
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

  ##Agrupo los Pagos a Terceros, por Concepto, Nro. Factura y Proveedor##
  $mCto = array();
  $mDec = f_Explode_Array($vCocDat['commemod'],"|","~");

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
	//f_Mensaje(__FILE__,__LINE__,"$qDatCom~".mysql_num_rows($xDatCom));
	while($xRDC = mysql_fetch_array($xDatCom)){
		$nInd_mComImp = count($vComImp);
		$vComImp[] = $xRDC['ctoidxxx']."~".$xRDC['pucidxxx'];
	}
  ##Fin Traigo los Documentos que estan marcados como PAGOIMPUESTOS##
  
  for ($i=0;$i<count($mDec);$i++) {
    if ($mDec[$i][1] != "") {
      $mComObs_PCC = stripos($mDec[$i][2],"[");
  
      $nSwitch_Encontre_Concepto = 0;
      if (in_array("{$mDec[$i][1]}~{$mDec[$i][9]}", $vComImp) == false) {
        for ($j=0;$j<count($mCto);$j++) {
          if ($mCto[$j][1] == $mDec[$i][1] && $mCto[$j][5] == $mDec[$i][5] && $mCto[$j][12] == $mDec[$i][12]) {
            $nSwitch_Encontre_Concepto = 1;
            $mCto[$j][7] += $mDec[$i][7]; // Acumulo el valor de ingreso para tercero.
            $mCto[$j][15] += $mDec[$i][15]; // Acumulo base de iva.
            $mCto[$j][16] += $mDec[$i][16]; // Acumulo valor del iva.
            $mCto[$j][20] += $mDec[$i][20]; // Acumulo el valor de ingreso para tercero en Dolares.
            $mCto[$j][100] = ((strlen($mCto[$j][100]) + strlen($mDec[$i][5]) + 1) < 50) ? $mCto[$j][100]."/".$mDec[$i][5] : $mCto[$j][100];
            $mCto[$j][100] = (substr($mCto[$j][100],0,1) == "/") ? substr($mCto[$j][100],1,strlen($mCto[$j][100])) : $mCto[$j][100];
            $j = count($mCto); // Me salgo del FOR cuando encuentro el concepto.
          }
        }
      }

      if ($nSwitch_Encontre_Concepto == 0) { // No encontre el ingreso para tercero en la matrix $mCto
        $nInd_mCto = count($mCto);
        $mCto[$nInd_mCto] = $mDec[$i]; // Ingreso el registro como nuevo.
        $mCto[$nInd_mCto][100] = ((strlen($mCto[$nInd_mCto][100]) + strlen($mDec[$i][5]) + 1) < 50) ? $mCto[$nInd_mCto][100]."/".$mDec[$i][5] : $mCto[$nInd_mCto][100];
        $mCto[$nInd_mCto][100] = (substr($mCto[$nInd_mCto][100],0,1) == "/") ? substr($mCto[$nInd_mCto][100],1,strlen($mCto[$nInd_mCto][100])) : $mCto[$nInd_mCto][100];
      }
    }
  }
  ##FIN Agrupo los Pagos a Terceros, por Concepto, Nro. Factura y Proveedor##

  /*****Agrupo Pagos a Terceros por Concepto de Facturacion *****/
  $mMatriz = array();
  $mDatIP  = array();
  $mPCC    = array();
  for($i=0;$i<count($mCodDat);$i++){
    if($mCodDat[$i]['comctocx'] == "PCC") {
      $cObs    = "";
      $cCan    = "";
      $nCan    = 0;
      $cAplCan = "";
      $nComObs_PCC = stripos($mCodDat[$i]['comobsxx'], "[");
      if($nComObs_PCC > 0){
        $mAuxCan = explode("CANTIDAD:",substr($mCodDat[$i]['comobsxx'],$nComObs_PCC,strlen($mCodDat[$i]['comobsxx'])));
        $cCan = "";
        if(count($mAuxCan) > 1) {
          $cCan    = str_replace(array(",","]"," "), "", $mAuxCan[1]);
          $nCan    = $cCan;
          $cAplCan = "SI";
        }
        $cObs = substr(substr($mCodDat[$i]['comobsxx'],0,$nComObs_PCC),0,70);
      }else{
        $cObs = substr($mCodDat[$i]['comobsxx'],0,70);
      }

      $mPCC[$mCodDat[$i]['ctoidxxx']]['comctocx']  = $mCodDat[$i]['comctocx'];
      $mPCC[$mCodDat[$i]['ctoidxxx']]['pucidxxx']  = $mCodDat[$i]['pucidxxx'];
      $mPCC[$mCodDat[$i]['ctoidxxx']]['comvlrxx'] += $mCodDat[$i]['comvlrxx'];
      $mPCC[$mCodDat[$i]['ctoidxxx']]['ctoidxxx']  = $mCodDat[$i]['ctoidxxx'];
      $mPCC[$mCodDat[$i]['ctoidxxx']]['comobsxx']  = ($mPCC[$mCodDat[$i]['ctoidxxx']]['comobsxx'] == "")?$cObs:$mPCC[$mCodDat[$i]['ctoidxxx']]['comobsxx'];
      $mPCC[$mCodDat[$i]['ctoidxxx']]['comcanxx'] += $nCan;
      $mPCC[$mCodDat[$i]['ctoidxxx']]['comcanap']  = ($mPCC[$mCodDat[$i]['ctoidxxx']]['comcanap'] == "SI")?$mPCC[$mCodDat[$i]['ctoidxxx']]['comcanap']:$cAplCan;
    }

    if($mCodDat[$i]['comctocx'] == "IP") {

      $nSwitch_Encontre_Concepto = 0;

      //Agrupando por Concepto
      //Trayendo descripcion concepto, cantidad y unidad
      $mComObs_IP = f_Explode_Array($mCodDat[$i]['comobsxx'],"|","~");

      $vDatosIp = array();
      $vDatosIp = f_Cantidad_Ingreso_Propio($mCodDat[$i]['comobsxx'],'',$mCodDat[$i]['sucidxxx'],$mCodDat[$i]['docidxxx'],$mCodDat[$i]['docsufxx']);

      //Los IP se agrupan por Sevicio
      for($j=0;$j<count($mDatIP);$j++){
        if($mDatIP[$j]['ctoidxxx'] == $mCodDat[$i]['ctoidxxx'] && $mDatIP[$j]['seridxxx'] == $mCodDat[$i]['seridxxx']){
          $nSwitch_Encontre_Concepto = 1;

          $mDatIP[$j]['comvlrxx'] += $mCodDat[$i]['comvlrxx'];
          $mDatIP[$j]['comvlrme'] += $mCodDat[$i]['comvlrme'];
          
          $mDatIP[$j]['compivax']  = $mCodDat[$i]['compivax']; // Porcentaje IVA
          $mDatIP[$j]['comvlr01'] += $mCodDat[$i]['comvlr01']; // Valor Iva
          
          //Cantidad FE
          $mDatIP[$j]['canfexxx'] += $vDatosIp[1];

          //Cantidad por condicion especial
          for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
            $mDatIP[$j]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] += $vDatosIp[3][$nP]['valpdfxx'];
          }
        }
      }

      if ($nSwitch_Encontre_Concepto == 0) {
        $nInd_mConData = count($mDatIP);
        $mDatIP[$nInd_mConData] = $mCodDat[$i];
        $mDatIP[$nInd_mConData]['comobsxx'] = $vDatosIp[0];
        $mDatIP[$nInd_mConData]['canfexxx'] = $vDatosIp[1];
        $mDatIP[$nInd_mConData]['unidadfe'] = $vDatosIp[2];

        for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
          $mDatIP[$nInd_mConData]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['valpdfxx'];
        }
      }
    }
  }
  foreach ($mPCC as $cKey => $mValores) {
    $mMatriz[] = $mValores;
  }
  /*****Fin Agrupo Pagos a Terceros por concepto de Faaturacion *****/
  
  ##Traigo la Forma de Pago##
  $cFormaPag = "";
  $vCodFormPago = explode("~", $vCocDat['comobs2x']);

  if ($vCodFormPago[14] == "1") {
    $cFormaPag = "CREDITO";
  } elseif($vCodFormPago[14] == "2") {
    $cFormaPag = "CONTADO";
  }
  ##FIN Traigo la Forma de Pago##

  ##Busco los conceptos que son de 4xMil y que en el campo ctoclaxf sean igual a IMPUESTOFINANCIERO para imprimirlo en el bloque de ingresos propios##
  if($nSwitch == 0){
		
    class PDF extends FPDF {

      function Header() {        
        global $cAlfa;   global $cRoot;   global $cPlesk_Skin_Directory; global $vSysStr; global $_COOKIE;
        global $vCocDat; global $vResDat; global $cDocId; global $cDocSuf; global $cDocTra; global $vDceDat;
        global $cDocId; global $vCiuFac; global $cDoitra; global $cFecTra; global $cBultos; global $cPesBru;
        global $cCodEmb; global $cFormaPag; global $cPedido; global $vAgeDat;

        if ($vCocDat['regestxx'] == "INACTIVO") {
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,180,180);
        }
  
        if ($_COOKIE['kModo'] == "VERFACTURA"){
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,180,180);
        }

        $posy	= 10;
        $posx	= 8;
        //logo
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg', 15, 10, 25);
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobereauveritas.png', 170, 13, 38, 16);
        //Nombre de la agencia
        $this->SetFont('arial','B',8);
    		$this->setXY($posx+40,$posy);
        $this->Cell(110,4,utf8_decode("AGENCIA DE ADUANAS ADUAMAR DE COLOMBIA CIA. S.A.S NIVEL 1"),0,0,'C');
        $this->Ln(5);
        $this->setX($posx+45);
        $this->Cell(110,4,"IMPORTACIONES Y EXPORTACIONES",0,0,'C');

        //Direccion
        $posy += 10;
        $this->SetFont('arial','',8);
        $this->setXY($posx+45,$posy);
        $this->Cell(110,4, $vAgeDat['CLIDIRXX'] . utf8_decode(", Teléfono: ") . $vAgeDat['CLITELXX'],0,0,'C');
        
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
        $this->Cell(9, 4, substr($vCocDat['comfecxx'], 8, 2), 0, 0, 'C');
        $this->Cell(9, 4, substr($vCocDat['comfecxx'], 5, 2), 0, 0, 'C');
        $this->Cell(11, 4, substr($vCocDat['comfecxx'], 0, 4), 0, 0, 'C');

        //Hora Generacion
        $this->setXY($posx+30, $posy);
        $this->SetFont('arial', 'B', 7);
        $this->Cell(30, 3, utf8_decode("HORA GENERACIÓN"),0,0,'L');
        $this->Ln(5);
        $this->setX($posx+30);
        $this->SetFont('arial', '', 7);
        $this->Cell(9, 4, substr($vCocDat['reghcrex'], 0, 2), 0, 0, 'C');
        $this->Cell(9, 4, substr($vCocDat['reghcrex'], 3, 2), 0, 0, 'C');
        $this->Cell(10, 4, substr($vCocDat['reghcrex'], 6, 2), 0, 0, 'C');

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
        $this->MultiCell(30, 3, utf8_decode("FACTURA DE VENTA ELECTRÓNICA"),0,'C');
        $this->Ln(1);
        $this->setX($posx+163);
        $this->setTextColor(255,0,0);
        $this->SetFont('arial', '', 8);
        $this->Cell(38, 5, $vResDat['resprexx'].$vCocDat['comcscxx'],0,0,'C');
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
        $this->MultiCell(80, 3.5, utf8_decode($vCocDat['CLIINTXX']),0,'L');
        $posyfin = $this->getY()+0.5;

        $this->setXY($posx+100, $posy);
        $this->SetFont('arial', 'B', 7);
        $this->Cell(25, 3, "FORMA DE PAGO",0,0,'C');
        $this->SetFont('arial', '', 7);
        $this->Cell(20, 3, utf8_decode($cFormaPag),0,0,'L');
        $this->setXY($posx+147, $posy);
        $this->SetFont('arial', 'B', 7);
        $this->Cell(36, 3, "FECHA DE VENCIMIENTO",0,0,'C');
        $this->SetFont('arial', '', 7);
        $this->Cell(20, 3, $vCocDat['comfecve'],0,0,'L');

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
        $this->Cell(30, 4, substr($vCocDat['CLIDIRIX'], 0, 57),0,0,'L');
        $this->setX($posx+149);
        $this->SetFont('arial', 'B', 7);
        $this->Cell(30, 4, "NIT",0,0,'L');
        $this->Ln(5);
        $this->setX($posx);
        $this->SetFont('arial', '', 7);
        $this->Cell(30, 4, $vCocDat['CLITELXX'],0,0,'L');
        $this->setX($posx+23);
        $this->SetFont('arial', 'B', 7);
        $this->Cell(30, 4, "CIUDAD",0,0,'L');
        $this->setX($posx+45);
        $this->SetFont('arial', '', 7);
        $this->Cell(30, 4, substr($vCiuFac['CIUDESXX'], 0, 58),0,0,'L');
        $this->setX($posx+149);
        $this->Cell(30, 4, number_format($vCocDat['terid2xx'], 0, '', '.'). "-" .f_Digito_Verificacion($vCocDat['terid2xx']), 0, 0, 'L');
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
        $this->Line($posx+115, $posyIni-1, $posx+115, $posyIni+10);
        $this->Line($posx+95, $posyIni-1, $posx+95, $posyIni+10);
        $this->Line($posx+146, $posyIni-1, $posx+146, $posyFin+1);
        $this->Line($posx+172, $posyIni-1, $posx+172, $posyIni+10);
        $this->Rect($posx, $posyIni-1, 200, $posyFin - ($posyIni-2));

        $this->Ln(4);
  		}//Function Header

			function Footer(){
        global $cNomCopia; global $vCocDat; global $nPagina; global $vUsrDat; global $dir; global $vSysStr; 

        $posx	= 8;
        $posy = 215;

        $this->setXY($posx+173,33);
        $this->Cell(20,4,utf8_decode("PAGINA: ").$nPagina,0,0,'L');

        $textFooter_1  = "Esta factura se considera irrevocablemente aceptada por el comprador o beneficiario del servicio si no manifiesta expresamente rechazo de la factura ";
        $textFooter_1 .= "mediante reclamo escrito dirigido al emisor dentro de los tres (3) días hábiles siguientes a su recepción";      
        $this->setXY($posx,$posy+1);
        $this->SetFont('arial','',8);
        $this->MultiCell(195,3.5,utf8_decode($textFooter_1),0,'L');
        $this->Rect($posx, $posy, 200, 10);

        //Recuadro de las Tarifas
        $posy += 10;
        $this->Rect($posx, $posy, 105, 14);

        if ($vCocDat['compceqr'] != "") {
          $cFileQR = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/QR_".$_COOKIE['kUsrId']."_".date("YmdHis").".png";
          QRcode::png($vCocDat['compceqr'], $cFileQR, "H", 10, 1);
          $this->Image($cFileQR,$posx+106,$posy+1,38,38);
        }

        ## TOTALES ##
        $this->setXY($posx+148,$posy+4);
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
        $posy += 15;      
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
        $this->Cell(130,5,$vCocDat['compcecu'],1,0,'L');

        $posy += 5;
        $this->setXY($posx,$posy);
        $this->SetFont('arial','',5);
        $this->Cell(30,3,"FECHA Y HORA VALIDACION DIAN: ".substr($vCocDat['compcevd'],0,16),0,0,'L');

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

    for($y=1; $y<=2; $y++){
      $pdf->AddPage();
      $cNomCopia = "";
      $nPagina = 1;

      ##Codigo Para impresion de Copias de Factura ##
			switch($y){
        case 1:
          $cNomCopia = "ORIGINAL";
        break;
        case 2:
          $cNomCopia = "COPIA";
        break;
      }
      ##Codigo Para impresion de Copias de Factura ##

      $posy	= $pdf->GetY()+3; 
      $posx	= 8;
      $posfin = 207;
      $posRect = $posy-2;

      ## Impresion de Titulos
      $pdf->SetFont('arial','B',9);
      $pdf->setXY($posx,$posy);
      $pdf->Cell(20,5,utf8_decode("CÓDIGO"),0,0,'C');
      $pdf->Cell(132,5,utf8_decode("DESCRIPCIÓN"),0,0,'C');
      $pdf->Cell(18,5,"CANTIDAD",0,0,'C');
      $pdf->Cell(28,5,"VALOR",0,0,'C');
      $py = $posy+8;

      $TotPcc = 0;
      if(count($mCto) > 0 || count($mMatriz) > 0){

        //Se imprimen los Ingresos por Terceros
        $pdf->SetFont('arial','B',9);
        $pdf->setXY($posx,$py);
        $pdf->Cell(140,3,"** PAGOS A TERCEROS ** (NO GRAVABLES)",0,0,'L');
        
        $py += 5;
        $pdf->SetFont('arial','',9);
        $pdf->SetWidths(array(20,132,18,28));
        $pdf->SetAligns(array("C","L","C","R"));
        $pdf->setXY($posx,$py);

        for ($i=0;$i<count($mCto);$i++) {
          if($py > $posfin){
            $pdf->Rect($posx,$posRect,200,215-$posRect);
            $pdf->AddPage();
            $nPagina++; 
            $py = $posy;
          }

          $TotPcc += $mCto[$i][7];

          $cComObs_PCCA = explode("^",$mCto[$i][2]);
          if (substr_count($cComObs_PCCA[0],"DIAN") > 0 ) { // Encontre la palabra DIAN de pago de impuestos.
            $cObs = "TRIBUTOS ADUANEROS";
          }else{
            // $cCodFactura = (trim($mCto[$i][5]) != "") ? "FRA. ".$mCto[$i][5] : "";
            // $cObs = $cComObs_PCCA[0]." ".$cCodFactura." ".$cComObs_PCCA[1];
            $cObs = $cComObs_PCCA[0];
          }
          $pdf->SetFont('arial','',9);
          $pdf->setX($posx);
          $pdf->Row(array(trim($mCto[$i][1]),
                          trim($cObs), 
                          "1", 
                          number_format($mCto[$i][7],0,',','.')));
          $py += 4;
        }

        for ($k=0;$k<count($mMatriz);$k++) {
            $cComObs_PCCA = explode("^",$mMatriz[$k]['comobsxx']);
            $TotPcc += $mMatriz[$k]['comvlrxx'];

            $pdf->SetFont('arial','',9);
            $pdf->setX($posx);
            $pdf->Row(array(trim($mMatriz[$k]['ctoidxxx']),
                            trim($cComObs_PCCA[0]), 
                            ($mMatriz[$k]['comcanxx'] != 0) ? $mMatriz[$k]['comcanxx'] : "1", 
                            number_format($mMatriz[$k]['comvlrxx'],0,',','.')));
            $py += 4;
        }
        $py = $pdf->GetY();

        if($py > $posfin){
          if($py > $posfin){
            $pdf->Rect($posx,$posRect,200,215-$posRect);
            $pdf->AddPage();
            $nPagina++; 
            $py = $posy;
          }
        }

        if($py > $posfin){
          if($py > $posfin){
            $pdf->Rect($posx,$posRect,200,215-$posRect);
            $pdf->AddPage();
            $nPagina++; 
            $py = $posy;
          }
        }
      }else{
        $py = $pdf->GetY()+6;        
      }

      $TotalIP = 0;
      if (count($mDatIP) > 0) {
        /*****Imprimir Ingresos Propios diferentes al concepto de MENOS DEPOSITO DE CONTENEDORES *****/
        $py += 5;
        $pdf->SetFont('arial','B',9);
        $pdf->setXY($posx,$py);
        $pdf->Cell(30,3,"** SERVICIOS ADUAMAR DE COLOMBIA ** (GRAVABLES)",0,0,'L');
        $py += 5;

        $pdf->SetFont('arial','',9);
        $pdf->SetWidths(array(20,132,18,28));
        $pdf->SetAligns(array("C","L","C","R"));
        $pdf->setXY($posx,$py);

        for ($k=0;$k<count($mDatIP);$k++) {
          if($mDatIP[$k]['comctocx'] == 'IP'){
            if($py > $posfin){
              $pdf->Rect($posx,$posRect,200,215-$posRect);
              $pdf->AddPage();
              $nPagina++; 
              $py = $posy;
            }

            $TotalIP += $mDatIP[$k]['comvlrxx'];

            $pdf->SetFont('arial','',9);
            $pdf->setX($posx);
            $pdf->Row(array($mDatIP[$k]['ctoidxxx'],
                            $mDatIP[$k]['comobsxx'], 
                            $mDatIP[$k]['canfexxx'], 
                            number_format($mDatIP[$k]['comvlrxx'],0,',','.')));
            $py += 4;
          }
        }
      }

      if($py > $posfin){
        if($py > $posfin){
          $pdf->Rect($posx,$posRect,200,215-$posRect);
          $pdf->AddPage();
          $nPagina++; 
          $py = $posy;
        }
      }
      
      $py = $pdf->GetY()+5;
      $pdf->Rect($posx,$posRect,200,215-$posRect);

      ##Bloque que acumula retenciones por valor de porcentaje##
      $mRetFte      = array();
      $mRetIca      = array();
      $mRetIva      = array();

      for ($k=0;$k<count($mCodDat);$k++) {
        if($mCodDat[$k]['comctocx'] == 'RETFTE'){
          $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx ";
          $qPucDat .= "FROM $cAlfa.fpar0115 ";
          $qPucDat .= "WHERE ";
          $qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$mCodDat[$k]['pucidxxx']}\" LIMIT 0,1 ";
          $xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
          $nFilPuc  = mysql_num_rows($xPucDat);
          if($nFilPuc > 0){
            //f_Mensaje(__FILE__,__LINE__,$qPucDat);
            while($xRPD = mysql_fetch_array($xPucDat)){
              $nSwitch_Encontre_Porcentaje = 0;
              for ($j=0;$j<count($mRetFte);$j++) {
                if($mRetFte[$j]['pucretxx'] == $xRPD['pucretxx']){
                  $nSwitch_Encontre_Porcentaje = 1;
                  $mRetFte[$j]['comvlrxx'] += $mCodDat[$k]['comvlrxx'];
                  $mRetFte[$j]['basexxxx'] += $mCodDat[$k]['comvlr01'];
                }
              }
              if ($nSwitch_Encontre_Porcentaje == 0) {
                $nInd_mRetFte = count($mRetFte);
                $mRetFte[$nInd_mRetFte]['tipretxx'] = "FUENTE";
                $mRetFte[$nInd_mRetFte]['pucretxx'] = $xRPD['pucretxx'];
                $mRetFte[$nInd_mRetFte]['pucretxx'] = $xRPD['pucretxx'];
                $mRetFte[$nInd_mRetFte]['basexxxx'] = $mCodDat[$k]['comvlr01'];
                $mRetFte[$nInd_mRetFte]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
              }
            }//while($xRPD = mysql_fetch_array($xPucDat)){
          }//if($nFilPuc > 0){
        }//if($mCodDat[$k]['comctocx'] == 'RETFTE'){

        if($mCodDat[$k]['comctocx'] == 'RETICA'){
          $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx ";
          $qPucDat .= "FROM $cAlfa.fpar0115 ";
          $qPucDat .= "WHERE ";
          $qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$mCodDat[$k]['pucidxxx']}\" LIMIT 0,1 ";
          $xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
          $nFilPuc  = mysql_num_rows($xPucDat);
          if($nFilPuc > 0){
            //f_Mensaje(__FILE__,__LINE__,$qPucDat);
            while($xRPD = mysql_fetch_array($xPucDat)){
              $nSwitch_Encontre_Porcentaje = 0;
              for ($j=0;$j<count($mRetIca);$j++) {
                if($mRetIca[$j]['pucretxx'] == $xRPD['pucretxx']){
                  $nSwitch_Encontre_Porcentaje = 1;
                  $mRetIca[$j]['comvlrxx'] += $mCodDat[$k]['comvlrxx'];
                  $mRetIca[$j]['basexxxx'] += $mCodDat[$k]['comvlr01'];
                }
              }
              if ($nSwitch_Encontre_Porcentaje == 0) {
                $nInd_mRetIca = count($mRetIca);
                $mRetIca[$nInd_mRetIca]['tipretxx'] = "ICA";
                $mRetIca[$nInd_mRetIca]['pucretxx'] = $xRPD['pucretxx'];
                $mRetIca[$nInd_mRetIca]['basexxxx'] = $mCodDat[$k]['comvlr01'];
                $mRetIca[$nInd_mRetIca]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
              }
            }//while($xRPD = mysql_fetch_array($xPucDat)){
          }//if($nFilPuc > 0){
        }//if($mCodDat[$k]['comctocx'] == 'RETICA'){

        if($mCodDat[$k]['comctocx'] == 'RETIVA'){
          $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx ";
          $qPucDat .= "FROM $cAlfa.fpar0115 ";
          $qPucDat .= "WHERE ";
          $qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$mCodDat[$k]['pucidxxx']}\" LIMIT 0,1 ";
          $xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
          $nFilPuc  = mysql_num_rows($xPucDat);
          if($nFilPuc > 0){
            //f_Mensaje(__FILE__,__LINE__,$qPucDat);
            while($xRPD = mysql_fetch_array($xPucDat)){
              $nSwitch_Encontre_Porcentaje = 0;
              for ($j=0;$j<count($mRetIva);$j++) {
                if($mRetIva[$j]['pucretxx'] == $xRPD['pucretxx']){
                  $nSwitch_Encontre_Porcentaje = 1;
                  $mRetIva[$j]['comvlrxx'] += $mCodDat[$k]['comvlrxx'];
                  $mRetIva[$j]['basexxxx'] += $mCodDat[$k]['comvlr01'];
                }
              }
              if ($nSwitch_Encontre_Porcentaje == 0) {
                $nInd_mRetIva = count($mRetIva);
                $mRetIva[$nInd_mRetIva]['tipretxx'] = "IVA";
                $mRetIva[$nInd_mRetIva]['pucretxx'] = $xRPD['pucretxx'];
                $mRetIva[$nInd_mRetIva]['basexxxx'] = $mCodDat[$k]['comvlr01'];
                $mRetIva[$nInd_mRetIva]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
              }
            }//while($xRPD = mysql_fetch_array($xPucDat)){
          }//if($nFilPuc > 0){
        }//if($mCodDat[$k]['comctocx'] == 'RETIVA'){
      }

      ### Imprimo Valores Retenidos ##
      $posx	= 8;
      $posy = 225;

      $nTotIva  = 0;
      $nTotIca  = 0;
      $nTotFte  = 0;
      $pdf->SetFont('arial','',6);
      for ($k=0;$k<count($mRetIva);$k++) {
        if($mRetIva[$k]['tipretxx'] == 'IVA'){
          $pdf->setXY($posx,$posy);
          $pdf->Cell(35,2.5,"Tarifa: ".$mRetIva[$k]['pucretxx']."% - Valor Base: ".$mRetIva[$k]['basexxxx']." - Valor Retenido: ".$mRetIva[$k]['comvlrxx'],0,0,'L');
          $posy += 2.5;
          $nTotIva += $mRetIva[$k]['comvlrxx'];
        }
      }

      for ($k=0;$k<count($mRetIca);$k++) {
        if($mRetIca[$k]['tipretxx'] == 'ICA'){
          $pdf->setXY($posx,$posy);
          $pdf->Cell(35,2.5,"Tarifa: ".$mRetIca[$k]['pucretxx']."% - Valor Base: ".$mRetIca[$k]['basexxxx']." - Valor Retenido: ".$mRetIca[$k]['comvlrxx'],0,0,'L');
          $posy += 2.5;
          $nTotIca += $mRetIca[$k]['comvlrxx'];
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

      $cNegativo = "";
      $nTotAnt = 0;
      for ($k=0;$k<count($mCodDat);$k++) {
        if($mCodDat[$k]['comctocx'] == 'CD' && strpos($mCodDat[$k]['comobsxx'],'ANTICIPOS') > 0){
          $nTotAnt += $mCodDat[$k]['comvlrxx'];
        }
      }

      $nTotPag = 0;
      for ($k=0;$k<count($mCodDat);$k++) {
        if($mCodDat[$k]['comctocx'] == 'SS' || $mCodDat[$k]['comctocx'] == 'SC'){
          if($mCodDat[$k]['comctocx'] == 'SC'){
            $cNegativo = "-";
          }
          $nTotPag += $mCodDat[$k]['comvlrxx'];
        }
      }

      $nSubTotal = $TotPcc + $TotalIP;
      
      if($cNegativo == "-") {
        $nAnticipoRecibido = $nTotAnt;
        $nSaldoFavor = $nTotPag;
        $nTotAnt = $nTotAnt + ($nTotPag*-1);
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
      $pdf->Cell(40,4,number_format($nTotAnt, 2, ',', '.'),0,0,'R'); 
      //SubTotal
      $pdf->setXY($posx+158,$posy+5);
      $pdf->Cell(40,4,number_format($nSubTotal, 2, ',', '.'),0,0,'R');
      //I.V.A
      $pdf->setXY($posx+158,$posy+10);
      $pdf->Cell(40,4,number_format($vCocDat['comivaxx'], 2, ',', '.'),0,0,'R');
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
    }
		$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
		$pdf->Output($cFile);

		if (file_exists($cFile)){
			chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
		} else {
			f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
		}

		echo "<html><script>document.location='$cFile';</script></html>";
  }

  /**
     *
     * @param $dataURI
     * @return array|bool
     */
    function getImage($dataURI){
      $img = explode(',',$dataURI,2);
      $pic = 'data://text/plain;base64,'.$img[1];
      $type = explode("/", explode(':', substr($dataURI, 0, strpos($dataURI, ';')))[1])[1]; // get the image type
      if ($type=="png"||$type=="jpeg"||$type=="gif") return array($pic, $type);
      return false;
  }
?>
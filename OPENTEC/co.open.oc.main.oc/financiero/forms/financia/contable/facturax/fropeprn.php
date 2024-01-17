<?php
  /**
	 * Imprime Factura de Venta OPENTECNOLOGIA.
	 * --- Descripcion: Permite Imprimir Factura de Venta de OPENTECNOLOGIA.
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
		<?php
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
		<?php
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
		<?php
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

    while ($xRCD = mysql_fetch_array($xCodDat)) {

      //Trayendo descripcion concepto, cantidad y unidad //Metodo del utiliqdo.php
      $vDatosIp = array();
      $vDatosIp = f_Cantidad_Ingreso_Propio($xRCD['comobsxx'], '', $xRCD['sucidxxx'], $xRCD['docidxxx'], $xRCD['docsufxx']);

      $nSwitch_Encontre_Concepto = 0;
      //Los IP se agrupan por Sevicio
      for($j=0;$j<count($mCodDat);$j++) {
        if ($mCodDat[$j]['ctoidxxx'] == $xRCD['ctoidxxx'] && $mCodDat[$j]['seridxxx'] == $xRCD['seridxxx']) {
          $nSwitch_Encontre_Concepto = 1;

          $mCodDat[$j]['comvlrxx'] += $xRCD['comvlrxx'];
          $mCodDat[$j]['comvlrme'] += $xRCD['comvlrme'];

          $mCodDat[$j]['compivax']  = $xRCD['compivax']; // Porcentaje IVA
          $mCodDat[$j]['comvlr01'] += $xRCD['comvlr01']; // Valor Iva

          //Cantidad FE
          $mCodDat[$j]['canfexxx'] += $vDatosIp[1];
        }
      }

      if ($nSwitch_Encontre_Concepto == 0) {
        $nInd_mConData = count($mCodDat);
        $mCodDat[$nInd_mConData] = $xRCD;
        $mCodDat[$nInd_mConData]['comobsxx'] = $vDatosIp[0];
        $mCodDat[$nInd_mConData]['canfexxx'] = $vDatosIp[1];
        $mCodDat[$nInd_mConData]['unidadfe'] = $vDatosIp[2];
      }
    }
  }

  $cDoiId  = "";
  $cComSeq = "";
  $mDoiId = explode("|",$vCocDat['comfpxxx']);
  for ($i=0;$i<count($mDoiId);$i++) {
    if($mDoiId[$i] != ""){
      $vDoiId  = explode("~",$mDoiId[$i]);
      $cDoiId  = $vDoiId[2];
      $cComSeq = $vDoiId[3];
      $cSucId  = $vDoiId[15];
      $i = count($mDoiId);
    }
  }
  
  $qDceDat  = "SELECT * ";
  $qDceDat .= "FROM $cAlfa.sys00121 ";
  $qDceDat .= "WHERE $cAlfa.sys00121.docidxxx = \"$cDoiId\" AND ";
  $qDceDat .= "$cAlfa.sys00121.docsufxx = \"$cComSeq\" AND ";
  $qDceDat .= "$cAlfa.sys00121.sucidxxx = \"$cSucId\" ";
  $qDceDat .= "GROUP BY $cAlfa.sys00121.docidxxx ";
  //f_Mensaje(__FILE__,__LINE__,$qDceDat);
  $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
  if (mysql_num_rows($xDceDat) > 0) {
    $vDceDat  = mysql_fetch_array($xDceDat);
  }
	
	##Traigo Pais del Cliente del Facturado ##
	$qPaiCfa  = "SELECT PAIDESXX ";
	$qPaiCfa .= "FROM $cAlfa.SIAI0052 ";
	$qPaiCfa .= "WHERE ";
	$qPaiCfa .= "PAIIDXXX = \"{$vCocDat['PAIIDIXX']}\" AND ";
	$qPaiCfa .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
	$xPaiCfa  = f_MySql("SELECT","",$qPaiCfa,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qPaiCfa."~".mysql_num_rows($xPaiCfa));
	if (mysql_num_rows($xPaiCfa) > 0) {
		$vPaiCfa = mysql_fetch_array($xPaiCfa);
	}

	##Traigo Departamento del Cliente del Facturado ##
	$qDepCfa  = "SELECT DEPDESXX ";
	$qDepCfa .= "FROM $cAlfa.SIAI0054 ";
	$qDepCfa .= "WHERE ";
	$qDepCfa .= "PAIIDXXX = \"{$vCocDat['PAIIDIXX']}\" AND ";
  $qDepCfa .= "DEPIDXXX = \"{$vCocDat['DEPIDIXX']}\" AND ";
	$qDepCfa .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
	$xDepCfa  = f_MySql("SELECT","",$qDepCfa,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qDepCfa."~".mysql_num_rows($xDepCfa));
	if (mysql_num_rows($xDepCfa) > 0) {
		$vDepCfa = mysql_fetch_array($xDepCfa);
	}

  ##Traigo Ciudad del Cliente del Facturado ##
  $qCiuCfa  = "SELECT CIUDESXX ";
  $qCiuCfa .= "FROM $cAlfa.SIAI0055 ";
  $qCiuCfa .= "WHERE ";
  $qCiuCfa .= "PAIIDXXX = \"{$vCocDat['PAIIDIXX']}\" AND ";
  $qCiuCfa .= "DEPIDXXX = \"{$vCocDat['DEPIDIXX']}\" AND ";
  $qCiuCfa .= "CIUIDXXX = \"{$vCocDat['CIUIDIXX']}\" AND ";
  $qCiuCfa .= "REGESTXX = \"ACTIVO\" ";
  $xCiuCfa  = f_MySql("SELECT","",$qCiuCfa,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qCiuCfa."~".mysql_num_rows($xCiuCfa));
  if (mysql_num_rows($xCiuCfa) > 0) {
    $vCiuCfa = mysql_fetch_array($xCiuCfa);
  }

  ##Traigo Ciudad del Cliente Do ##
  $qCiuCfd  = "SELECT CIUDESXX ";
  $qCiuCfd .= "FROM $cAlfa.SIAI0055 ";
  $qCiuCfd .= "WHERE ";
  $qCiuCfd .= "PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
  $qCiuCfd .= "DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
  $qCiuCfd .= "CIUIDXXX = \"{$vCocDat['CIUIDXXX']}\" AND ";
  $qCiuCfd .= "REGESTXX = \"ACTIVO\" ";
  $xCiuCfd  = f_MySql("SELECT","",$qCiuCfd,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qCiuCfd."~".mysql_num_rows($xCiuCfd));
  if (mysql_num_rows($xCiuCfd) > 0) {
    $vCiuCfd = mysql_fetch_array($xCiuCfd);
  }

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
  //f_Mensaje(__FILE__, __LINE__, "hola ");

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

  //Busco la Resolucion
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
  //FIN Busco la Resolucion

  ##Busco los conceptos que son de 4xMil y que en el campo ctoclaxf sean igual a IMPUESTOFINANCIERO para imprimirlo en el bloque de ingresos propios##
  $qCtoDat  = "SELECT * ";
  $qCtoDat .= "FROM $cAlfa.fpar0119 ";
  $qCtoDat .= "WHERE ";
  $qCtoDat .= "$cAlfa.fpar0119.ctoclaxf = \"IMPUESTOFINANCIERO\" AND ";
  $qCtoDat .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" LIMIT 0,1 ";
  $xCtoDat  = f_MySql("SELECT","",$qCtoDat,$xConexion01,"");
  $nFilCto  = mysql_num_rows($xCtoDat);
  if ($nFilCto > 0) {
    $vCtoDat = mysql_fetch_array($xCtoDat);
	}
	
	##Traigo la Forma de Pago##
  $cFormaPag = "";
  $vComObs2 = explode("~", $vCocDat['comobs2x']);

  if ($vComObs2[14] != "") {
    //Buscando descripcion
    $cFormaPag = ($vComObs2[14] == 1) ? "CONTADO" : "CREDITO";
  }
  ##FIN Traigo la Forma de Pago##
  
  $cMedioPago = "";
  ##Traigo el Medio de Pago##
  if ($vComObs2[15] != "") {
    $qMedPag  = "SELECT mpadesxx ";
    $qMedPag .= "FROM $cAlfa.fpar0155 ";
    $qMedPag .= "WHERE mpaidxxx = \"{$vComObs2[15]}\" AND ";
    $qMedPag .= "regestxx = \"ACTIVO\" LIMIT 0,1";
    $xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
    if (mysql_num_rows($xMedPag) > 0) {
      $vMedPag = mysql_fetch_array($xMedPag);
    }
  }
  ##FIN Traigo el Medio de Pago##

  ##Traigo sucursal de facturación##
  $cSucDesx = "";
  $qFpar117  = "SELECT sucidxxx, ccoidxxx ";
  $qFpar117 .= "FROM $cAlfa.fpar0117 ";
  $qFpar117 .= "WHERE ";
  $qFpar117 .= "comidxxx = \"{$vCocDat['comidxxx']}\" AND ";
  $qFpar117 .= "comcodxx = \"{$vCocDat['comcodxx']}\" LIMIT 0,1";
  $xFpar117 = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
  if (mysql_num_rows($xFpar117) > 0) {
    $vFpar117 = mysql_fetch_array($xFpar117);

    $qFpar008  = "SELECT sucdesxx ";
    $qFpar008 .= "FROM $cAlfa.fpar0008 ";
    $qFpar008 .= "WHERE ";
    $qFpar008 .= "sucidxxx = \"{$vFpar117['sucidxxx']}\" AND ";
    $qFpar008 .= "ccoidxxx = \"{$vFpar117['ccoidxxx']}\" LIMIT 0,1";
    $xFpar008 = f_MySql("SELECT","",$qFpar008,$xConexion01,"");
    $vFpar008 = mysql_fetch_array($xFpar008);
    $cSucDesx = $vFpar008['sucdesxx'];
  }
  ##Traigo sucursal##

  ##Busco los conceptos que son de 4xMil y que en el campo ctoclaxf sean igual a IMPUESTOFINANCIERO para imprimirlo en el bloque de ingresos propios##
  if($nSwitch == 0){
		define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
		require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf_js.php');
    class PDF_AutoPrint extends PDF_Javascript{
			function AutoPrint($dialog=false){
			  //Embed some JavaScript to show the print dialog or start printing immediately
				$param=($dialog ? 'true' : 'false');
				$script="print($param);";
				$this->IncludeJS($script);
      }

      function Header() {        
        global $cAlfa;   global $cRoot; global $cPlesk_Skin_Directory; global $vSysStr; global $_COOKIE;
				global $vCocDat; global $vResDat; global $vCiuCfa; global $vPaiCfa; global $vDepCfa;
				global $cFormaPag; global $vMedPag; global $cSucDesx; global $vComObs2;

        if ($vCocDat['regestxx'] == "INACTIVO") {
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',15,50,180,180);
        }
  
        if ($_COOKIE['kModo'] == "VERFACTURA"){
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',20,50,180,180);
        }

				//Inicializo Posicion X,Y
        $posx = 20;
        $posy = 10;
        //logo de Open
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_header_open.jpg', $posx, $posy-9, 181);

				## Datos Fecha Factura ##
        $this->setXY($posx,$posy+30);
        $this->SetFont('verdana','',7.5);
        $this->Cell(15,5, "Fecha de la Factura: " . substr($vCocDat['comfecxx'], 8, 2) . "-" . substr($vCocDat['comfecxx'], 5, 2) . "-" . substr($vCocDat['comfecxx'], 0, 4) ,0,0,'L');
        $this->Ln(4.5);
        $this->setX($posx);
        $this->Cell(15,5, "Fecha de vencimiento: " . substr($vCocDat['comfecve'], 8, 2) . "-" . substr($vCocDat['comfecve'], 5, 2) . "-" . substr($vCocDat['comfecve'], 0, 4),0,0,'L');
        $this->Ln(4.5);
				$this->setX($posx);
				$this->Cell(15,5, "Forma de pago: " . $cFormaPag,0,0,'L');
        $vNitsAdq = ['860030380', '860038063', '830002397', '830025224'];
        if (in_array($vCocDat['terid2xx'], $vNitsAdq)) {
          $this->Ln(4.5);
          $this->setX($posx);
          $this->Cell(15,5,utf8_decode("Ciudad Prestación Servicio: ") . $cSucDesx,0,0,'L');
        }
				$this->Ln(4.5);
				$this->setX($posx);
				$this->Cell(15,5, "Medio de pago: " . $vMedPag['mpadesxx'],0,0,'L');
				$this->Ln(4.5);

        $this->setX($posx);
        $this->Cell(15,5, "Orden de Compra: " . $vComObs2[27],0,0,'L');
        $this->Ln(4.5);

				## Datos Adquiriente ##
				$this->setX($posx);
				$this->Cell(15,5, "Facturado a",0,0,'L');
				//Cliente
        $this->setX($posx);
				$pyy = $this->getY() + 1.4;
				$alinea2 = explode("~",f_Words($vCocDat['CLIINTXX'],105));
				for ($n=0;$n<count($alinea2);$n++) {
					$this->SetFont('verdana','',7);
					$this->setXY(36,$pyy);
					$this->Cell(15,3, $alinea2[$n], 0,0,'L');
					$pyy+=3;
				}
				$this->Ln(0.5);
				//Nit Cliente
        $this->setX($posx);
				$this->Cell(15,5, "N.I.T: " . $vCocDat['terid2xx'] . "-" . f_Digito_Verificacion($vCocDat['terid2xx']),0,0,'L');				
				$this->Ln(4.5);
				//Telefono Cliente
        $this->setX($posx);
        $this->Cell(15,5, "Tel: " . $vCocDat['CLITELIX'],0,0,'L');
				$this->Ln(4.5);
				//Direccion Cliente
        $this->setX($posx);
        $this->MultiCell(100,4, utf8_decode("Dir: " . $vCocDat['CLIDIRIX']),0,'L');
        //Ciudad - Departamento
        $this->setX($posx);
        $this->Cell(15,5, utf8_decode($vCiuCfa['CIUDESXX'] . " - " . $vDepCfa['DEPDESXX']),0,0,'L');
        $this->Ln(4.5);
        $this->setX($posx);
        $this->Cell(15,5, "Pais: ".utf8_decode($vPaiCfa['PAIDESXX']),0,0,'L');

        ## Datos OFE ##
        $cTitulo = ($vCocDat['regestxx'] == "PROVISIONAL") ? "PRE-FACTURA" : "FACTURA";
        $this->setXY($posx+165,$posy+30);
        $this->SetFont('verdanab','',7.5);
				$this->Cell(15,5,utf8_decode($cTitulo . " ELECTRÓNICA DE VENTA No. ").$vResDat['resprexx'] . " " . $vCocDat['comcscxx'],0,0,'R');
				$this->Ln(4.5);

        $this->SetFont('verdana','',7.5);
        $this->setX($posx+165);
        $this->Cell(15,5,utf8_decode("OPENTECNOLOGIA S.A"),0,0,'R');
        $this->Ln(4.5);
        $this->setX($posx+165);
        $this->Cell(15,5,utf8_decode("N.I.T Nº: ") . number_format($vSysStr['financiero_nit_agencia_aduanas'], 0, '', '.'),0,0,'R');
        $this->Ln(4.5);
        $this->setX($posx+165);
        $this->Cell(15,5,utf8_decode("Regimen Común de IVA"),0,0,'R');
				$this->Ln(4.5);
				$this->setX($posx+165);
				$this->Cell(15,5,utf8_decode("Actividad Económica No. 5820 ICA 9,66"),0,0,'R');
				$this->Ln(4.5);
  
        $this->setX($posx+165);
        $this->Cell(15,5,"Dir.: ".utf8_decode("Carrera 70C No. 49-68"),0,0,'R');
        $this->Ln(4.5);
        $this->setX($posx+165);
        $this->Cell(15,5,"Tel.: "."2950100",0,0,'R');
        $this->Ln(4.5);
        $this->setX($posx+165);
        $this->Cell(15,5,utf8_decode("BOGOTA - BOGOTA D.C"),0,0,'R');
        $this->Ln(4.5);

				//Resolucion DIAN 
        //Calculo numero de Meses entre Desde y Hasta
        $dFechaInicial = date_create($vResDat['resfdexx']);
        $dFechaFinal = date_create($vResDat['resfhaxx']);
        $nDiferencia = date_diff($dFechaInicial, $dFechaFinal);
				$nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m;
				
				$this->setX($posx+165);
				$this->Cell(15,5,utf8_decode("Resolución DIAN: ".$vResDat['residxxx']),0,0,'R');
				$this->Ln(4.5);
				$this->setX($posx+165);
				$this->Cell(15,5,utf8_decode(" Fecha de Expedición ").$vResDat['resfdexx'],0,0,'R');
				$this->Ln(4.5);
				$this->setX($posx+165);
				$this->Cell(15,5,"Vigencia: ". $nMesesVigencia ." Meses",0,0,'R');
				$this->Ln(4.5);
				$this->setX($posx+165);
				$this->Cell(15,5,utf8_decode("Numeracion Autorizada ").$vResDat['resprexx']." desde el No. ".$vResDat['resdesxx']." hasta el No. ".$vResDat['reshasxx'],0,0,'R');
				$this->Ln(4.5);

				$this->setX($posx+165);
				$this->Cell(15,5,utf8_decode("CUENTA CORRIENTE NUMERO 01137626-6 DEL BANCO ITAÚ"),0,0,'R');
				$this->Ln(7);

				/***** Cabecera de detalle de los IP *****/
				$this->SetFillColor(150);
				$this->SetTextColor(255);
				$this->SetFont('verdanab','',7);
				$this->setX($posx+1);
				$this->Cell(15,5,"Cantidad",0,0,'L', true);
				$this->Cell(108,5,utf8_decode("Descripción"),0,0,'C', true);
				$this->Cell(27,5,"Valor Unitario.",0,0,'C',true);
				$this->Cell(27,5,"Valor Total",0,0,'C',true);
				$this->SetTextColor(0);
				$this->Ln(5);

  		}//Function Header
     
			function Footer(){
				global $cPlesk_Skin_Directory; global $cNomCopia; global $vCocDat; global $vSysStr; global $_COOKIE;

				$posx	= 20;
        $posy = 181;

        if ($vCocDat['compceqr'] != "") {
          $cFileQR = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/QR_".$_COOKIE['kUsrId']."_".date("YmdHis").".png";
          QRcode::png($vCocDat['compceqr'], $cFileQR, "H", 10, 1);
					$this->Image($cFileQR,$posx+150,$posy+21,30,30);
        }
        
        $this->setXY($posx,$posy);
        $this->SetFont('arial','',6);
        $this->Cell(15,3,"FECHA Y HORA VALIDACION DIAN: ".substr($vCocDat['compcevd'],0,16),0,0,'L');
        $posy += 4;
				$this->setXY($posx,$posy);
				$this->SetFont('verdanab','',7.5);
        $this->MultiCell(130,4, utf8_decode("ESTA FACTURA DE VENTA SE ASIMILA EN SUS EFECTOS LEGALES A LA LETRA DE CAMBIO, Art. 774, 775, 776 Y SIGUIENTES DE C.C. LA NO CANCELACION A SU VENCIMIENTO, CAUSARA EL MAXIMO INTERES PERMITIDO LEGALMENTE."),0,'L');

        $this->setXY($posx+145,$posy-5);
        $this->SetFont('verdanab','',7);
				$this->Cell(15,5,"CUFE: ",0,0,'L');
				$this->Ln(5);
				$this->setX($posx+145);
        $this->SetFont('verdana','',6);
        $this->MultiCell(35, 3,$vCocDat['compcecu'],0,'L');

        $this->setXY($posx,$posy+21);
        $this->SetFont('verdana','',7);
        $this->Cell(15,5, utf8_decode("Representación Impresa de la Factura electrónica"),0,0,'L');
        $this->Ln(5);
        $this->setX($posx);
        $this->SetFont('verdana','',6);
        $this->MultiCell(130, 3,$vCocDat['compcesv'],0,'L');

        //Logos footer
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_footer_open.jpg', $posx-2, $posy+47, 181);
        
        $this->setXY($posx, $posy+90);
        $this->SetFont('verdanab','',8);
        $this->Cell(170,5,"ORIGINAL",0,0,'C');
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
          $angle*=M_PI/170;
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
    }
    
    $pdf=new PDF_AutoPrint('P','mm','Letter');
		$pdf->AddFont('verdana','','verdana.php');
		$pdf->AddFont('verdanab','','verdanab.php');
		$pdf->SetFont('verdana','',8);
		$pdf->SetMargins(0,0,0);
    $pdf->SetAutoPageBreak(0,0);
    $pdf->SetWidths(array(15,95,35,27));
	  $pdf->SetAligns(array("C","L","R","R"));
			
    /*** Inicializando Posdicones X,Y ***/
    $pdf->AddPage();
    $posx	   = 21;
    $posy	   = $pdf->GetY()+3; 
    $posfin  = 176;
    $posRect = $posy-8;
    if($vCocDat['CLINRPXX'] != "SI"){

      $py = $posy;
      $ToIP  = 0;
      $nComVlr_IPTotal = 0;

      // $mCodDat = array_merge($mCodDat,$mCodDat,$mCodDat,$mCodDat);
      $pdf->setXY($posy,$py);

      // echo "<pre>";
      // print_r($mCodDat);
      // die();

      for ($k=0;$k<count($mCodDat);$k++) {
        if($mCodDat[$k]['comctocx'] == 'IP'){
          if($py > $posfin){
            $pdf->Rect($posx, $posRect, 177, ($posfin-$posRect+2));
            $pdf->Line($posx + 15, $posRect, $posx + 15, $posfin+2);
            $pdf->Line($posx + 123, $posRect, $posx + 123, $posfin+2);
            $pdf->Line($posx + 150, $posRect, $posx + 150, $posfin+2);

            $pdf->AddPage();
            $nPagina++; 
            $py = $posy;
          }

          //Acumulo total de los IP
          $nComVlr_IPTotal += $mCodDat[$k]['comvlrxx'];

          $nValorUni = ($mCodDat[$k]['unidadfe'] != "A9" && $mCodDat[$k]['canfexxx'] > 0) ? $mCodDat[$k]['comvlrxx']/$mCodDat[$k]['canfexxx'] : $mCodDat[$k]['comvlrxx'];

          /*** Detalle de Ingresos Propios***/
          $pdf->setX($posx);
          $pdf->Row(array(number_format($mCodDat[$k]['canfexxx'],0,'.',','),
                          utf8_decode($mCodDat[$k]['comobsxx']),
                          number_format($nValorUni,2,'.',','),
                          number_format($mCodDat[$k]['comvlrxx'],2,'.',',')
                        ));
          $py = $pdf->getY();
        }
      }
      
      $posyFin = $py;

      if($py > $posfin + 5){
        $pdf->Rect($posx, $posRect, 177, ($posfin-$posRect+2));
        $pdf->Line($posx + 15, $posRect, $posx + 14, $posfin+2);
        $pdf->Line($posx + 123, $posRect, $posx + 123, $posfin+2);
        $pdf->Line($posx + 150, $posRect, $posx + 150, $posfin+2);
        $pdf->AddPage();
        $nPagina++; 
        $py = $posy;
      }

      $pdf->Rect($posx, $posRect, 177, ($posyFin-$posRect));
      $pdf->Line($posx + 15, $posRect, $posx + 15, $posyFin);
      $pdf->Line($posx + 123, $posRect, $posx + 123, $posyFin);
      $pdf->Line($posx + 150, $posRect, $posx + 150, $posyFin);

      //Subtotales
      $posy = $pdf->getY();
      $posyIni = $posy;
      
      //Traigo Valor del Iva
      $nTotIvaIP = 0;
      for ($k=0;$k<count($mCodDat);$k++) {
        if($mCodDat[$k]['comctocx'] == 'IVAIP'){
          $nTotIvaIP += $mCodDat[$k]['comvlrxx'];
        }
      }

      //Calculo subtotal y total
      $nSubTotal = $nComVlr_IPTotal;
      $nTotPag   = $nSubTotal + $nTotIvaIP;

      $pdf->SetFont('verdanab', '', 8);
      $pdf->setXY($posx + 117, $posyIni + 1);
      $pdf->Cell(30, 5, "SUBTOTAL", 0, 0, 'R');
      $pdf->Cell(30, 5, number_format($nSubTotal, 2,',','.'), 0, 0, 'R');
      $pdf->Ln(6);
  
      $pdf->setX($posx + 117);
      $pdf->Cell(30, 5, "IVA", 0, 0, 'R');
      $pdf->Cell(30, 5, number_format($nTotIvaIP, 2,',','.'), 0, 0, 'R');
      $pdf->Ln(6);

      $pdf->setX($posx + 117);
      $pdf->Cell(30, 5, "TOTAL", 0, 0, 'R');
      $pdf->Cell(30, 5, number_format($nTotPag, 2,',','.'), 0, 0, 'R');
      $pdf->Ln(6);

      //Recuadro de los totales
      $pdf->Line($posx + 150, $posy, $posx + 150, $posy + 18);
      $pdf->Line($posx + 123, $posy + 6, $posx + 177, $posy + 6);
      $pdf->Line($posx + 123, $posy + 12, $posx + 177, $posy + 12);
      $pdf->Rect($posx + 123, $posy, 54, 18);

      //Observaciones
      $pdf->setXY($posx, $posyIni + 1);
      $pdf->SetFont('verdanab', '', 8);
      $pdf->Cell(30, 5, "OBSERVACIONES:", 0, 0, 'L');
      $pdf->Ln(5);
      $pdf->setX($posx);
      $pdf->SetFont('verdana', '', 7);
      $pdf->MultiCell(128, 3.3, utf8_decode($vCocDat['comobsxx']), 0, 'L');

      ### Inicializo posicion Y
      $posy = 199;
      $nTotPag1 = f_Cifra_Php($nTotPag,'PESO');
      $pdf->setXY($posx, $posy);
      $pdf->SetFont('verdanab', '', 8);
      $pdf->MultiCell(130, 4, "SON: " . $nTotPag1 , 0, 'L');
      $pdf->SetFont('verdana', '', 8);
    }

		$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
		// $pdf->AutoPrint(false);
		$pdf->Output($cFile);

		if (file_exists($cFile)){
			chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
		} else {
			f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
		}

		echo "<html><script>document.location='$cFile';</script></html>";
  }
?>
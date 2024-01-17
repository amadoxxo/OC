<?php
	/**
	 * Imprime Factura de Venta Roldan
	 * --- Descripcion: Permite Imprimir Factura de Venta.
	 * @author Victor Vivenzio <victor.vivenzio@opentecnologia.com.co>
	 */
  include("../../../../libs/php/utility.php");  
  include("../../../../libs/php/utiliqdo.php");

	$switch=0;
	$vMemo=explode("|",$prints);

	// Validacion de Comprobante Repetido
	$mPrints = f_Explode_Array($prints,"|","~");
	$cAno    = substr($mPrints[0][4],0,4);
	$cEstiloLetra = 'arial';
	$cEstiloLetrab = 'arialb';

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
		if ($i < (count($mCodCom) -1)) { $cCodigos_Comprobantes .= ","; }
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
		$swich = 1;
		f_Mensaje(__FILE__,__LINE__,"El Documento [{$mPrints[0][0]}-{$mPrints[0][1]}-{$mPrints[0][2]}] No se puede Imprimir porque su Consecutivo se Encuentra Repetido en el Modulo de Contabilidad, Verifique");
	}
	// Fin de Validacion de Comprobante Repetido

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
	##Codigo para verificar si la factura ya fue impresa al menos una vez, sino se debe hacer una autorizacion ##
	for($u=0; $u<count($vMemo); $u++) {
		if ($vMemo[$u]!=""){
			$zMatriz=explode("~",$vMemo[$u]);
			## Select a la 1001 para traer el campo que se marca cuando se ha impreso factura##
			$qCocDat  = "SELECT * ";
			$qCocDat .= "FROM $cAlfa.fcoc$cAno ";
			$qCocDat .= "WHERE $cAlfa.fcoc$cAno.comidxxx = \"{$zMatriz[0]}\" AND ";
			$qCocDat .= "$cAlfa.fcoc$cAno.comcodxx = \"{$zMatriz[1]}\" AND ";
			$qCocDat .= "$cAlfa.fcoc$cAno.comcscxx = \"{$zMatriz[2]}\" AND ";
			$qCocDat .= "$cAlfa.fcoc$cAno.comcsc2x = \"{$zMatriz[3]}\" LIMIT 0,1";
      // f_Mensaje(__FILE__,__LINE__,$qCocDat);
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
			##Fin Select a la 1001 para traer el campo que se marca cuando se ha impreso factura##
		}//if ($vMemo[$u]!=""){
	}//for($u=0; $u<count($vMemo); $u++) {

	##Codigo que valida si hay errores para permitir o NO la Impresion de la Factura ##
	if($permisos==1){
		$switch=1;
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
		$switch=1;
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
		$switch=1;
		f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas no Tienen Resolucion Activa [$zCadRes], Verifique."); ?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
		<script languaje = "javascript">
			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			document.forms['frgrm'].submit();
		</script>
		<?php
	}

	if($switch == 0) {

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

    if($vCocDat['regestxx'] != "INACTIVO" && $_COOKIE['kModo'] != "VERFACTURA") {

      ##Codigo para Actualizar Campo de Impresion en la 1001 ##
      $mUpdPrn = array(array('NAME'=>'comprnxx','VALUE'=>'IMPRESO'  ,'CHECK'=>'SI'),
                       array('NAME'=>'comidxxx','VALUE'=>$cComId    ,'CHECK'=>'WH'),
                       array('NAME'=>'comcodxx','VALUE'=>$cComCod   ,'CHECK'=>'WH'),
                       array('NAME'=>'comcscxx','VALUE'=>$cComCsc   ,'CHECK'=>'WH'),
                       array('NAME'=>'comcsc2x','VALUE'=>$cComCsc2  ,'CHECK'=>'WH'));

      if (!f_MySql("UPDATE","fcoc$cAno",$mUpdPrn,$xConexion01,$cAlfa)) {
      $nSwitch = 1;
      }
      ##Fin Codigo para Actualizar Campo de Impresion en la 1001 ##
    }

		////// CABECERA 1001 /////
		$qCocDat  = "SELECT ";
		$qCocDat .= "$cAlfa.fcoc$cNewYear.*, ";
		$qCocDat .= "IF($cAlfa.fpar0008.sucidxxx != \"\",$cAlfa.fpar0008.sucidxxx,\"SUCURSAL SIN ID\") AS sucidxxx, ";
		$qCocDat .= "IF($cAlfa.fpar0008.sucdesxx != \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS CLINOMXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CLICONTX != \"\",$cAlfa.SIAI0150.CLICONTX,\"SIN RESPONSABLE\") AS CLICONTX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CLINRPXX != \"\",$cAlfa.SIAI0150.CLINRPXX,\"\") AS CLINRPXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CLIPLAXX != \"\",$cAlfa.SIAI0150.CLIPLAXX,\"\") AS CLIPLAXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.TDIIDXXX != \"\",$cAlfa.SIAI0150.TDIIDXXX,\"\") AS TDIIDXXX ";
		$qCocDat .= "FROM $cAlfa.fcoc$cNewYear ";
		$qCocDat .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcoc$cNewYear.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
		$qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cNewYear.terid2xx = $cAlfa.SIAI0150.CLIIDXXX ";
		$qCocDat .= "WHERE ";
		$qCocDat .= "$cAlfa.fcoc$cNewYear.comidxxx = \"$cComId\"  AND ";
		$qCocDat .= "$cAlfa.fcoc$cNewYear.comcodxx = \"$cComCod\" AND ";
		$qCocDat .= "$cAlfa.fcoc$cNewYear.comcscxx = \"$cComCsc\" AND ";
		$qCocDat .= "$cAlfa.fcoc$cNewYear.comcsc2x = \"$cComCsc2\" LIMIT 0,1";

		// f_Mensaje(__FILE__,__LINE__,$qCocDat);
		$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
		$nFilCoc  = mysql_num_rows($xCocDat);
		if ($nFilCoc > 0) {
			$vCocDat  = mysql_fetch_array($xCocDat);
		}
		//////////////////////////////////////////////////////////////////////////////////////

		/**
     * Nombre del cliente dueño del DO - teridxxx
     */
    $qNomTer  = "SELECT ";
    $qNomTer .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS CLINOMXX ";
    $qNomTer .= "FROM $cAlfa.SIAI0150 ";
    $qNomTer .= "WHERE ";
    $qNomTer .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vCocDat['teridxxx']}\" ";
    $xNomTer  = f_MySql("SELECT","",$qNomTer,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qNomTer."~".mysql_num_rows($xNomTer));
    $vNomTer  = mysql_fetch_array($xNomTer);

    //Extrayendo datos de comobs2xx
    $vComObs2 = explode("~", $vCocDat['comobs2x']);

    //La moneda de impresion depende de la moneda con la que se guardo la factura
    $vCocDat['CLINRPXX']= ($vComObs2[16] == "USD") ? "SI" : "NO";


		$cCamVlr = ($vCocDat['CLINRPXX'] == "SI") ? "comvlrme" : "comvlrxx";
		////// DETALLE 1002 /////
		$qCodDat  = "SELECT DISTINCT ";
		$qCodDat .= "$cAlfa.fcod$cNewYear.*, ";
		$qCodDat .= "$cAlfa.sys00121.docmtrxx AS docmtrxx ";
		$qCodDat .= "FROM $cAlfa.fcod$cNewYear ";
		$qCodDat .= "LEFT JOIN $cAlfa.sys00121 ON $cAlfa.fcod$cNewYear.comcsccx = $cAlfa.sys00121.docidxxx AND $cAlfa.fcod$cNewYear.comseqcx = $cAlfa.sys00121.docsufxx  ";
		$qCodDat .= "WHERE $cAlfa.fcod$cNewYear.comidxxx = \"$cComId\" AND ";
		$qCodDat .= "$cAlfa.fcod$cNewYear.comcodxx = \"$cComCod\" AND ";
		$qCodDat .= "$cAlfa.fcod$cNewYear.comcscxx = \"$cComCsc\" AND ";
		$qCodDat .= "$cAlfa.fcod$cNewYear.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cNewYear.comseqxx) ASC ";
		$xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
		//f_Mensaje(__FILE__,__LINE__,$qCodDat);
		$nFilCod  = mysql_num_rows($xCodDat);

		if ($nFilCod > 0) {
			// Cargo la Matriz con los ROWS del Cursor
			$iA=0;
			while ($xRCD = mysql_fetch_array($xCodDat)) {

				if($xRCD['comctocx'] == 'PCC'){
					$nInd_mValores = count($mValores);
					$mValores[$nInd_mValores]['comobsxx'] = $xRCD['comobsxx'];
					$mValores[$nInd_mValores]['comvlrxx'] = $xRCD[$cCamVlr];
					$mValores[$nInd_mValores]['puctipej'] = $xRCD['puctipej'];
					$mValores[$nInd_mValores]['ctoidxxx'] = $xRCD['ctoidxxx'];
					$mValores[$nInd_mValores]['comvlr01'] = $xRCD['comvlr01'];

				} else {

          $nSwitch_Encontre_Concepto = 0;
          
          if ($xRCD['comctocx'] == "IP") {
            //Agrupando por Concepto
            //Trayendo descripcion concepto, cantidad y unidad
            $mComObs_IP = f_Explode_Array($xRCD['comobsxx'], "|", "~");
  
            $vDatosIp = array();
            $vDatosIp = f_Cantidad_Ingreso_Propio($xRCD['comobsxx'], '', $xRCD['sucidxxx'], $xRCD['docidxxx'], $xRCD['docsufxx']);
  
            //Los IP se agrupan por Sevicio
            for ($j = 0; $j < count($mCodDat); $j++) {
              if ($mCodDat[$j]['ctoidxxx'] == $xRCD['ctoidxxx'] && $mCodDat[$j]['seridxxx'] == $xRCD['seridxxx']) {
                $nSwitch_Encontre_Concepto = 1;
  
                $mCodDat[$j]['comvlrxx'] += $xRCD['comvlrxx'];
                $mCodDat[$j]['comvlrme'] += $xRCD['comvlrme'];
  
                $mCodDat[$j]['compivax']  = $xRCD['compivax']; // Porcentaje IVA
                $mCodDat[$j]['comvlr01'] += $xRCD['comvlr01']; // Valor Iva
  
                //Cantidad FE
                $mCodDat[$j]['canfexxx'] += $vDatosIp[1];
  
                //Cantidad por condicion especial
                for ($nP = 0; $nP < count($vDatosIp[3]); $nP++) {
                  $mCodDat[$j]['itemcanx'][str_replace(" ", "_", "{$vDatosIp[3][$nP]['despdfxx']}")] += $vDatosIp[3][$nP]['valpdfxx'];
                }
              }
            }
          } else {
            $vDatosIp[0] = $xRCD['comobsxx'];
          }

          if ($nSwitch_Encontre_Concepto == 0) {
            $nInd_mConData = count($mCodDat);
            $mCodDat[$nInd_mConData] = $xRCD;
            $mCodDat[$nInd_mConData]['comobsxx'] = $vDatosIp[0];
            $mCodDat[$nInd_mConData]['canfexxx'] = $vDatosIp[1];
            $mCodDat[$nInd_mConData]['unidadfe'] = $vDatosIp[2];
  
            for ($nP = 0; $nP < count($vDatosIp[3]); $nP++) {
              $mCodDat[$nInd_mConData]['itemcanx'][str_replace(" ", "_", "{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['valpdfxx'];
            }
          }
				}
			}
			// Fin de Cargo la Matriz con los ROWS del Cursor
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
		// f_Mensaje(__FILE__,__LINE__,$qResDat."~".mysql_num_rows($xResDat));
		$nFilRes  = mysql_num_rows($xResDat);
		if ($nFilRes > 0) {
			$vResDat = mysql_fetch_array($xResDat);
		}
		##Fin Traigo Datos de la Resolucion ##

		##Traigo Ciudad del Cliente ##
		$qCiuDat  = "SELECT * ";
		$qCiuDat .= "FROM $cAlfa.SIAI0055 ";
		$qCiuDat .= "WHERE ";
		$qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
		$qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
		$qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCocDat['CIUIDXXX']}\" AND ";
		$qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
		//f_Mensaje(__FILE__,__LINE__,$qCiuDat);
		$xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
		$nFilCiu  = mysql_num_rows($xCiuDat);
		if ($nFilCiu > 0) {
			$vCiuDat = mysql_fetch_array($xCiuDat);
		}
		##Fin Traigo Ciudad del Cliente ##

		##Traigo Datos de Contacto del Facturado a ##
		if($vCocDat['CLICONTX'] != ""){
				$vContactos = explode("~",$vCocDat['CLICONTX']);
				//f_Mensaje(__FILE__,__LINE__,count($vContactos));
			if(count($vContactos) > 1){
				$vIdContacto = $vContactos[1];
			}else{
				$vIdContacto = $vCocDat['CLICONTX'];
			}
		}//if($vCocDat['CLICONTX'] != ""){

		$qConDat  = "SELECT ";
		$qConDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)))) AS NOMBRE ";
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

		##Traigo Dias de Plazo ##
		$qCccDat  = "SELECT * ";
		$qCccDat .= "FROM $cAlfa.fpar0151 ";
		$qCccDat .= "WHERE ";
		$qCccDat .= "$cAlfa.fpar0151.cliidxxx = \"{$vCocDat['terid2xx']}\" AND ";
		$qCccDat .= "$cAlfa.fpar0151.regestxx = \"ACTIVO\" ";
		$xCccDat  = f_MySql("SELECT","",$qCccDat,$xConexion01,"");
		$nFilCcc  = mysql_num_rows($xCccDat);
		if ($nFilCcc > 0) {
			$vCccDat = mysql_fetch_array($xCccDat);
		}
		##Fin Traigo Dias de Plazo ##

		##Exploto campo Matriz para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##
		$cDocId  = ""; $cDocSuc = ""; $cDocSuf = "";
		$nCantDo = 0;
		$dFecMay = date("Y"); //Fecha
		$mDoiId = explode("|",$vCocDat['comfpxxx']);
		for ($i=0;$i<count($mDoiId);$i++) {
			if($mDoiId[$i] != "") {
				$vDoiId  = explode("~",$mDoiId[$i]);
				if($cDocId == "") {
					$cDocId  = $vDoiId[2];
					$cDocSuf = $vDoiId[3];
					$cSucId  = $vDoiId[15];
				}
				$dFecMay = ($dFecMay > substr($vDoiId[6],0,4)) ? substr($vDoiId[6],0,4) : $dFecMay;
				$nCantDo += 1;	
			}//if($mDoiId[$i] != ""){
		}//for ($i=0;$i<count($mDoiId);$i++) {
		$nAnoIniDo = (($dFecMay-1) <  $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($dFecMay-1);
		##Fin Exploto campo Matriz para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##

		##Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##
		$qDceDat  = "SELECT * ";
		$qDceDat .= "FROM $cAlfa.sys00121 ";
		$qDceDat .= "WHERE ";
		$qDceDat .= "$cAlfa.sys00121.sucidxxx = \"$cSucId\" AND ";
		$qDceDat .= "$cAlfa.sys00121.docidxxx = \"$cDocId\" AND ";
		$qDceDat .= "$cAlfa.sys00121.docsufxx = \"$cDocSuf\" ";
		$xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
		$nFilDce  = mysql_num_rows($xDceDat);
		if ($nFilDce > 0) {
			$vDceDat = mysql_fetch_array($xDceDat);
		}
		##Fin Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##

		## Creo Matriz para Guardar Dirección, Telefono y Fax de la Sucursal
		switch ($vCocDat['sucidxxx']) {
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

		##Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##
		switch ($vDceDat['doctipxx']){
			case "IMPORTACION":
				##Traigo Datos de la SIAI0200 DATOS DEL DO ##
				$cTitulo = "Importador";

				$qDoiDat  = "SELECT * ";
				$qDoiDat .= "FROM $cAlfa.SIAI0200 ";
				$qDoiDat .= "WHERE $cAlfa.SIAI0200.DOIIDXXX = \"$cDocId\" AND ";
				$qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"$cDocSuf\" AND ";
				$qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"$cSucId\" ";
				$xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qDoiDat." ~ ".mysql_num_rows($xDoiDat));
				$nFilDoi  = mysql_num_rows($xDoiDat);
				if ($nFilDoi > 0) {
					$vDoiDat = mysql_fetch_array($xDoiDat);
				}
				##Fin Traigo Datos de la SIAI0200 DATOS DEL DO ##

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
				$qDecDat .= "$cAlfa.SIAI0206.DGETRMXX AS DGETRMXX       ";//Trm
				$qDecDat .= "FROM $cAlfa.SIAI0206 ";
				$qDecDat .= "WHERE ";
				$qDecDat .= "$cAlfa.SIAI0206.DOIIDXXX = \"$cDocId\"  AND ";
				$qDecDat .= "$cAlfa.SIAI0206.DOISFIDX = \"$cDocSuf\" AND ";
				$qDecDat .= "$cAlfa.SIAI0206.ADMIDXXX = \"$cSucId\" ";
				$qDecDat .= "GROUP BY $cAlfa.SIAI0206.DOIIDXXX ";

        // f_Mensaje(__FILE__,__LINE__,$qDecDat);
				$xDecDat  = f_MySql("SELECT","",$qDecDat,$xConexion01,"");
				$nFilDec  = mysql_num_rows($xDecDat);
				if ($nFilDec > 0) {
					$vDecDat  = mysql_fetch_array($xDecDat);
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
				$nFilDce  = mysql_num_rows($xDceDat);
				if ($nFilDce > 0) {
					$vNomComercial = mysql_fetch_array($xDceDat);
				}

				//Busco Proveedor Primer Registro
				$qProDat  = "SELECT $cAlfa.SIAI0202.PIEIDXXX ";
				$qProDat .= "FROM $cAlfa.SIAI0202 ";
				$qProDat .= "WHERE ";
				$qProDat .= "$cAlfa.SIAI0202.ADMIDXXX = \"$cSucId\" AND ";
				$qProDat .= "$cAlfa.SIAI0202.DOIIDXXX = \"$cDocId\" AND ";
				$qProDat .= "$cAlfa.SIAI0202.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
				// f_Mensaje(__FILE__,__LINE__,$qProDat);
				$xProDat  = f_MySql("SELECT","",$qProDat,$xConexion01,"");
				$nProDat  = mysql_num_rows($xProDat);
				if ($nProDat > 0) {
					$vProveedor = mysql_fetch_array($xProDat);
					// Busco Nombre del Proveedor
					$qProNom  = "SELECT $cAlfa.SIAI0125.PIENOMXX ";
					$qProNom .= "FROM $cAlfa.SIAI0125 ";
					$qProNom .= "WHERE ";
					$qProNom .= "$cAlfa.SIAI0125.PIEIDXXX = \"{$vProveedor['PIEIDXXX']}\" LIMIT 0,1";
					// f_Mensaje(__FILE__,__LINE__,$qProNom);
					$xProNom  = f_MySql("SELECT","",$qProNom,$xConexion01,"");
					$nProNom  = mysql_num_rows($xProNom);
					if ($nProNom > 0) {
						$vProNom = mysql_fetch_array($xProNom);
					}
				}

				## Busco medio de Transporte
				$qMedTra  = "SELECT $cAlfa.SIAI0120.MTRDESXX ";
				$qMedTra .= "FROM $cAlfa.SIAI0120 ";
				$qMedTra .= "WHERE ";
				$qMedTra .= "$cAlfa.SIAI0120.MTRIDXXX = \"{$vDoiDat['MTRIDXXX']}\" LIMIT 0,1 ";
				$xMedTra  = f_MySql("SELECT","",$qMedTra,$xConexion01,"");
				$nMedTra  = mysql_num_rows($xMedTra);
				if ($nMedTra > 0) {
					$vMedTra = mysql_fetch_array($xMedTra);
				}

				## Busco Pais de Origen
				$qPaiOrg  = "SELECT $cAlfa.SIAI0052.PAIDESXX ";
				$qPaiOrg .= "FROM $cAlfa.SIAI0052 ";
				$qPaiOrg .= "WHERE ";
				$qPaiOrg .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$vDoiDat['PAIIDXXX']}\" LIMIT 0,1 ";
				$xPaiOrg  = f_MySql("SELECT","",$qPaiOrg,$xConexion01,"");
				$nPaiOrg  = mysql_num_rows($xPaiOrg);
				if ($nPaiOrg > 0) {
					$vPaiOrg = mysql_fetch_array($xPaiOrg);
				}

				## Busco Lugar de Ingreso
				$qLugIng  = "SELECT $cAlfa.SIAI0119.LINDESXX ";
				$qLugIng .= "FROM $cAlfa.SIAI0119 ";
				$qLugIng .= "WHERE ";
				$qLugIng .= "$cAlfa.SIAI0119.LINIDXXX = \"{$vDoiDat['LINIDXXX']}\" LIMIT 0,1 ";
				$xLugIng  = f_MySql("SELECT","",$qLugIng,$xConexion01,"");
				$nLugIng  = mysql_num_rows($xLugIng);
				if ($nLugIng > 0) {
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
				$cVendedor        = $vVenDat['CLINOMXX'];
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
				## Consulto Datos de Do en Exportaciones tabla siae0199 ##
				$cTitulo = "Exportador";

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

				##Trayendo aduana de salida ##
				$qAduSal  = "SELECT odiid2xx ";
				$qAduSal .= "FROM $cAlfa.siae0200 ";
				$qAduSal .= "WHERE ";
				$qAduSal .= "$cAlfa.siae0200.dexidxxx = \"$cDocId\" AND ";
				$qAduSal .= "$cAlfa.siae0200.admidxxx = \"$cSucId\" AND ";
				$qAduSal .= "$cAlfa.siae0200.odiid2xx != \"\" LIMIT 0,1 ";
				$xAduSal  = f_MySql("SELECT","",$qAduSal,$xConexion01,"");
				$vAduSal  = mysql_fetch_array($xAduSal);
				if ($vAduSal['odiid2xx'] != "") {
					##Tayendo descripcion Aduana de salida
					$qDesAdu  = "SELECT ODIDESXX ";
					$qDesAdu .= "FROM $cAlfa.SIAI0103 ";
					$qDesAdu .= "WHERE ";
					$qDesAdu .= "ODIIDXXX = \"{$vAduSal['odiid2xx']}\" ";
					$qDesAdu .= "LIMIT 0,1 ";
					$xDesAdu  = f_MySql("SELECT","",$qDesAdu,$xConexion01,"");
					$vDesAdu  = mysql_fetch_array($xDesAdu);
				}

				##Trayendo el proveedor##
				$qProDat  = "SELECT pieidxxx,laiidxxx ";
				$qProDat .= "FROM $cAlfa.siae0200 ";
				$qProDat .= "WHERE ";
				$qProDat .= "$cAlfa.siae0200.dexidxxx = \"$cDocId\" AND ";
				$qProDat .= "$cAlfa.siae0200.admidxxx = \"$cSucId\" LIMIT 0,1 ";
				$xProDat  = f_MySql("SELECT","",$qProDat,$xConexion01,"");
				$vProDat  = mysql_fetch_array($xProDat);
				if ($vProDat['pieidxxx'] != "") {
					$qProNom  = "SELECT $cAlfa.SIAI0125.PIENOMXX ";
					$qProNom .= "FROM $cAlfa.SIAI0125 ";
					$qProNom .= "WHERE ";
					$qProNom .= "$cAlfa.SIAI0125.PIEIDXXX = \"{$vProDat['pieidxxx']}\" LIMIT 0,1";
					$qProNom .= "LIMIT 0,1 ";
					$xProNom  = f_MySql("SELECT","",$qProNom,$xConexion01,"");
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
				$nFilIte  = mysql_num_rows($xIteDat);
				if ($nFilIte > 0) {
					$vIteDat = mysql_fetch_array($xIteDat);
				}

				## Busco medio de Transporte
				$qMedTra  = "SELECT $cAlfa.SIAI0120.MTRDESXX ";
				$qMedTra .= "FROM $cAlfa.SIAI0120 ";
				$qMedTra .= "WHERE ";
				$qMedTra .= "$cAlfa.SIAI0120.MTRIDXXX = \"{$vDexDat['mtridxxx']}\" LIMIT 0,1 ";
				$xMedTra  = f_MySql("SELECT","",$qMedTra,$xConexion01,"");
				$nMedTra  = mysql_num_rows($xMedTra);
				if ($nMedTra > 0) {
					$vMedTra = mysql_fetch_array($xMedTra);
				}

				## Busco Lugar Destino Final
				$qLugIng  = "SELECT $cAlfa.siae0074.laidesxx ";
				$qLugIng .= "FROM $cAlfa.siae0074 ";
				$qLugIng .= "WHERE ";
				$qLugIng .= "$cAlfa.siae0074.laiidxxx = \"{$vProDat['laiidxxx']}\" LIMIT 0,1 ";
				$xLugIng  = f_MySql("SELECT","",$qLugIng,$xConexion01,"");
				$nLugIng  = mysql_num_rows($xLugIng);
				if ($nMedTra > 0) {
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
				## Traigo Datos de la SIAI0200 ##
				$cTitulo = "Importador";

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
				$nFilIte  = mysql_num_rows($xIteDat);
				if ($nFilIte > 0) {
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
				$nFilDce  = mysql_num_rows($xDceDat);
				if ($nFilDce > 0) {
				  $vNomComercial = mysql_fetch_array($xDceDat);
				}

				//Busco Proveedor Primer Registro
				$qProDat  = "SELECT $cAlfa.SIAI0202.PIEIDXXX ";
				$qProDat .= "FROM $cAlfa.SIAI0202 ";
				$qProDat .= "WHERE ";
				$qProDat .= "$cAlfa.SIAI0202.ADMIDXXX = \"$cSucId\" AND ";
				$qProDat .= "$cAlfa.SIAI0202.DOIIDXXX = \"$cDocId\" AND ";
				$qProDat .= "$cAlfa.SIAI0202.DOISFIDX = \"$cDocSuf\" LIMIT 0,1";
				// f_Mensaje(__FILE__,__LINE__,$qProDat);
				$xProDat  = f_MySql("SELECT","",$qProDat,$xConexion01,"");
				$nProDat  = mysql_num_rows($xProDat);
				if ($nProDat > 0) {
					$vProveedor = mysql_fetch_array($xProDat);
					// Busco Nombre del Proveedor
					$qProNom  = "SELECT $cAlfa.SIAI0125.PIENOMXX ";
					$qProNom .= "FROM $cAlfa.SIAI0125 ";
					$qProNom .= "WHERE ";
					$qProNom .= "$cAlfa.SIAI0125.PIEIDXXX = \"{$vProveedor['PIEIDXXX']}\" LIMIT 0,1";
					// f_Mensaje(__FILE__,__LINE__,$qProNom);
					$xProNom  = f_MySql("SELECT","",$qProNom,$xConexion01,"");
					$nProNom  = mysql_num_rows($xProNom);
					if ($nProNom > 0) {
						$vProNom = mysql_fetch_array($xProNom);
					}
				}

				## Busco medio de Transporte
				$qMedTra  = "SELECT $cAlfa.SIAI0120.MTRDESXX ";
				$qMedTra .= "FROM $cAlfa.SIAI0120 ";
				$qMedTra .= "WHERE ";
				$qMedTra .= "$cAlfa.SIAI0120.MTRIDXXX = \"{$vDoiDat['MTRIDXXX']}\" LIMIT 0,1 ";
				$xMedTra  = f_MySql("SELECT","",$qMedTra,$xConexion01,"");
				$nMedTra  = mysql_num_rows($xMedTra);
				if ($nMedTra > 0) {
					$vMedTra = mysql_fetch_array($xMedTra);
				}

				## Busco Pais de Origen
				$qPaiOrg  = "SELECT $cAlfa.SIAI0052.PAIDESXX ";
				$qPaiOrg .= "FROM $cAlfa.SIAI0052 ";
				$qPaiOrg .= "WHERE ";
				$qPaiOrg .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$vDoiDat['PAIIDXXX']}\" LIMIT 0,1 ";
				$xPaiOrg  = f_MySql("SELECT","",$qPaiOrg,$xConexion01,"");
				$nPaiOrg  = mysql_num_rows($xPaiOrg);
				if ($nPaiOrg > 0) {
					$vPaiOrg = mysql_fetch_array($xPaiOrg);
				}

				## Busco Lugar de Ingreso
				$qLugIng  = "SELECT $cAlfa.SIAI0119.LINDESXX ";
				$qLugIng .= "FROM $cAlfa.SIAI0119 ";
				$qLugIng .= "WHERE ";
				$qLugIng .= "$cAlfa.SIAI0119.LINIDXXX = \"{$vDoiDat['LINIDXXX']}\" LIMIT 0,1 ";
				$xLugIng  = f_MySql("SELECT","",$qLugIng,$xConexion01,"");
				$nLugIng  = mysql_num_rows($xLugIng);
				if ($nLugIng > 0) {
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
				$cTasCam = $vDoiDat['TCATASAX']; //Tasa de Cambio
				$cDocTra = $vDoiDat['DGEDTXXX']; //Documento de Transporte
				$cBultos = $vIteDat['itebulxx']; //Bultos
				$cPesBru = $vIteDat['itepbrxx']; //Peso Bruto
				$nValAdu = $vDtaDat['dtafobxx'];
				$cOpera  = "CIF"; // CIF
				$cPedido = $vDoiDat['DOIPEDXX'];
				$cAduana = $vAdmIng['ODIDESXX'];
				$cVendedor        = $vVenDat['CLINOMXX'];
				$cProveedor       = $vProNom['PIENOMXX'];
				$cMedioTransporte = $vMedTra['MTRDESXX'];
				$cPaisOrigen      = $vPaiOrg['PAIDESXX'];
				$cLugarIngreso    = $vLugIng['LINDESXX'];
				$cContenedor      = $vDoiDat['DOICONNU'];
				##Fin Cargo Variables para Impresion de Datos de Do ##
			break;
			case "OTROS":
				$cTitulo = "";
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
      $qTerceros .= "CLIIDXXX = \"{$vCocDat['teridxxx']}\" LIMIT 0,1 ";
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
        // f_Mensaje(__FILE__, __LINE__, $qVenDat."~".mysql_num_rows($xVenDat));
        $nVenDat  = mysql_num_rows($xVenDat);
        if ($nVenDat > 0) {
          $vVenDat   = mysql_fetch_array($xVenDat);
          $cVendedor = $vVenDat['CLINOMXX'];
        }
      }
    }


		##Recorrido para saber si hay registros PCC e IP y poder controlar la impresion de bloques de Pagos a Terceros e Ingresos Propios ##
		$nBandPcc = 0;  $nBandIP = 0; // Banderas que se ponen en 1 si encontro registros para impresion bloques PCC e IP.
		for ($k=0;$k<count($mCodDat);$k++) {
			if($mCodDat[$k]['comctocx'] == 'PCC' && substr($mCodDat[$k]['pucidxxx'], 0,1) == "4"){
				$nBandIP = 1;
			} elseif($mCodDat[$k]['comctocx'] == 'PCC'){
				$nBandPcc = 1;
			}//if($mCodDat[$k]['comctocx'] == 'PCC'){
			if($mCodDat[$k]['comctocx'] == 'IP'){
				$nBandIP = 1;
			}//if($mCodDat[$k]['comctocx'] == 'IP'){
		}//for ($k=0;$k<count($mCodDat);$k++) {
		##Fin Recorrido para saber si hay registros PCC e IP y poder controlar la impresion de bloques de Pagos a Terceros e Ingresos Propios ##

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

		// Codigo para imprimir los ingresos para terceros
		$mIT = f_Explode_Array($vCocDat['commemod'],"|","~");
		$mIngTer = array();
		// f_Mensaje(__FILE__,__LINE__,count($mIT));
		if($vCccDat['cccimpro'] == "CONSOLIDADO"){
			// f_Mensaje(__FILE__,__LINE__,"entre consolidado");
			for ($i=0;$i<count($mIT);$i++) {
				if ($mIT[$i][1] != "") {

					$vTercero = explode("^",$mIT[$i][2]);

					//Busco por Cuenta PUC y Concepto ID --> en los conceptos contables (fpar0119 y fpar0121)
					$qConCon  = "SELECT cacidxxx ";
					$qConCon .= "FROM $cAlfa.fpar0119 ";
					$qConCon .= "WHERE ";
					$qConCon .= "$cAlfa.fpar0119.ctoidxxx = \"{$mIT[$i][1]}\" AND ";
					$qConCon .= "$cAlfa.fpar0119.pucidxxx = \"{$mIT[$i][9]}\" LIMIT 0,1 ";
					$xConCon  = f_MySql("SELECT","",$qConCon,$xConexion01,"");
					$vConCon  = mysql_fetch_array($xConCon);
					// f_Mensaje(__FILE__,__LINE__,$qConCon."~".mysql_num_rows($xConCon));

					if (mysql_num_rows($xConCon) == 0) {
					  $qConCon  = "SELECT cacidxxx ";
            $qConCon .= "FROM $cAlfa.fpar0121 ";
            $qConCon .= "WHERE ";
            $qConCon .= "$cAlfa.fpar0121.ctoidxxx = \"{$mIT[$i][1]}\" AND ";
            $qConCon .= "$cAlfa.fpar0121.pucidxxx = \"{$mIT[$i][9]}\" LIMIT 0,1 ";
            $xConCon  = f_MySql("SELECT","",$qConCon,$xConexion01,"");
            $vConCon  = mysql_fetch_array($xConCon);
            // f_Mensaje(__FILE__,__LINE__,$qConCon."~".mysql_num_rows($xConCon));
					}

					if($vConCon['cacidxxx'] != ""){

						/*** Agrego el pago a tercero a la matriz segun la clasificacion***/
						$nSwitch_Encontre_Concepto = 0;
						for ($j=0;$j<count($mIngTer);$j++) {
						  //agrupando por categoria
							if ($mIngTer[$j][200] == $vConCon['cacidxxx']) { // $mIngTer[$j][200]: Codigo de la categoria concepto
								$nSwitch_Encontre_Concepto = 1;
								$mIngTer[$j][7]  += ($vCocDat['CLINRPXX'] == "SI") ? $mIT[$i][20] : $mIT[$i][7]; // Acumulo el valor de ingreso para tercero.
								$mIngTer[$j][15] += $mIT[$i][15]; // Acumulo base de iva.
								$mIngTer[$j][16] += $mIT[$i][16]; // Acumulo valor del iva.
								$mIngTer[$j][20] += $mIT[$i][20]; // Acumulo el valor de ingreso para tercero en Dolares.
								$mIngTer[$j][100] = ((strlen($mIngTer[$j][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$j][100]."/".$mIT[$i][5] : $mIngTer[$j][100];
								$mIngTer[$j][100] = (substr($mIngTer[$j][100],0,1) == "/") ? substr($mIngTer[$j][100],1,strlen($mIngTer[$j][100])) : $mIngTer[$j][100];
								$mIngTer[$j][101] = ($vCtoCon["{$mIngTer[$j][1]}"]['umeidxxx'] != '') ? $vCtoCon["{$mIngTer[$j][1]}"]['umeidxxx'] : "A9"; // Unidad de medida
								$j = count($mIngTer); // Me salgo del FOR cuando encuentro el concepto.
							}
						}

						if ($nSwitch_Encontre_Concepto == 0) { // No encontre el ingreso para tercero en la matrix $mIngTer

  						/*** Busco la categoria concepto a la que pertenece ***/
              $qCatCon  = "SELECT cacidxxx,cacdesxx ";
              $qCatCon .= "FROM $cAlfa.fpar0144 ";
              $qCatCon .= "WHERE ";
              $qCatCon .= "$cAlfa.fpar0144.cacidxxx = \"{$vConCon['cacidxxx']}\" LIMIT 0,1 ";
              $xCatCon  = f_MySql("SELECT","",$qCatCon,$xConexion01,"");
              $vCatCon = mysql_fetch_array($xCatCon);

							$mIT[$i][7] = ($vCocDat['CLINRPXX'] == "SI") ? $mIT[$i][20] : $mIT[$i][7];
							$nInd_mIngTer = count($mIngTer);

							$mIngTer[$nInd_mIngTer]      = $mIT[$i]; // Ingreso el registro como nuevo.
							$mIngTer[$nInd_mIngTer][2]   = $vCatCon['cacdesxx'];
							$mIngTer[$nInd_mIngTer][99]  = $vTercero[1];
							$mIngTer[$nInd_mIngTer][98]  = $vTercero[2];
							$mIngTer[$nInd_mIngTer][200] = $vCatCon['cacidxxx']; // $mIngTer[$j][200]: Codigo de la categoria concepto

							$mIngTer[$nInd_mIngTer][100] = ((strlen($mIngTer[$nInd_mIngTer][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$nInd_mIngTer][100]."/".$mIT[$i][5] : $mIngTer[$nInd_mIngTer][100];
							$mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$nInd_mIngTer][100],0,1) == "/") ? substr($mIngTer[$nInd_mIngTer][100],1,strlen($mIngTer[$nInd_mIngTer][100])) : $mIngTer[$nInd_mIngTer][100];
							$mIngTer[$nInd_mIngTer][101] = ($vCtoCon["{$mIngTer[$nInd_mIngTer][1]}"]['umeidxxx'] != '') ? $vCtoCon["{$mIngTer[$nInd_mIngTer][1]}"]['umeidxxx'] : "A9"; // Unidad de medida
						}
					}else{
				    // f_Mensaje(__FILE__,__LINE__,"Sin Categoria");
						$mIT[$i][201] = "NO"; //Bandera para indicar los conceptos que NO estan clasificados por categorias, para agregarlos al final de la matriz de terceros
					}
				}
			}
		}

    //Agrupando los conceptos no consolidados
    for ($i=0;$i<count($mIT);$i++) {

      if($vCccDat['cccimpro'] != "CONSOLIDADO"){
        $mIT[$i][201] = "NO";
      }

      if ($mIT[$i][1] != "" && $mIT[$i][201] == "NO") {

        //Si el concepto es de pago de tributos se agrupa por concepto y descripcion
        $cSwitch_Comprobante_Pago_Impuestos = "NO";
        $qComAju  = "SELECT * ";
        $qComAju .= "FROM $cAlfa.fpar0117 ";
        $qComAju .= "WHERE ";
        $qComAju .= "comidxxx = \"{$mIT[$i][3]}\" AND ";
        $qComAju .= "comcodxx = \"{$mIT[$i][4]}\" AND ";
        $qComAju .= "comtipxx = \"PAGOIMPUESTOS\" AND ";
        $qComAju .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xComAju  = f_MySql("SELECT","",$qComAju,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qComAju." ~ ".mysql_num_rows($xComAju));

        if (mysql_num_rows($xComAju) == 1) {

          $qCtoCba  = "SELECT * ";
          $qCtoCba .= "FROM $cAlfa.fpar0119 "; // Aqui no aplica la busqueda contra la fpar0121
          $qCtoCba .= "WHERE ";
          $qCtoCba .= "pucidxxx = \"{$mIT[$i][9]}\" AND ";
          $qCtoCba .= "ctocomxx LIKE \"%{$mIT[$i][3]}~{$mIT[$i][4]}%\" AND ";
          $qCtoCba .= "ctoidxxx = \"{$mIT[$i][1]}\" AND ";
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
        $vTercero = explode("^",$mIT[$i][2]);
        $mComObs_PCC = stripos($mIT[$i][2],"[");

        $nSwitch_Encontre_Concepto = 0;
        for ($j=0;$j<count($mIngTer);$j++) {
         $nAgrupar = 0;
          if ($cSwitch_Comprobante_Pago_Impuestos == "NO") {
             //Agrupando por concepto
             if ($mIngTer[$j][1] == $mIT[$i][1] /*&& $vTercero[2] == $mIngTer[$j][98]*/) {
               $nAgrupar = 1;
             }
          } else {
            //Agrupando por concepto y descripcion
            if ($mIngTer[$j][1] == $mIT[$i][1] && trim($vTercero[0]) == trim($mIngTer[$j][2])) {
               $nAgrupar = 1;
             }
          }

          if ($nAgrupar == 1) {
            $nSwitch_Encontre_Concepto = 1;
            $mIngTer[$j][7]  += ($vCocDat['CLINRPXX'] == "SI") ? $mIT[$i][20] : $mIT[$i][7]; // Acumulo el valor de ingreso para tercero.
            $mIngTer[$j][15] += $mIT[$i][15]; // Acumulo base de iva.
            $mIngTer[$j][16] += $mIT[$i][16]; // Acumulo valor del iva.
            $mIngTer[$j][20] += $mIT[$i][20]; // Acumulo el valor de ingreso para tercero en Dolares.
            $mIngTer[$j][100] = ((strlen($mIngTer[$j][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$j][100]."/".$mIT[$i][5] : $mIngTer[$j][100];
						$mIngTer[$j][100] = (substr($mIngTer[$j][100],0,1) == "/") ? substr($mIngTer[$j][100],1,strlen($mIngTer[$j][100])) : $mIngTer[$j][100];
						$mIngTer[$j][101] = ($vCtoCon["{$mIngTer[$j][1]}"]['umeidxxx'] != "") ? $vCtoCon["{$mIngTer[$j][1]}"]['umeidxxx'] : "A9"; // Unidad de medida
            $j = count($mIngTer); // Me salgo del FOR cuando encuentro el concepto.
          }
        }

        if ($nSwitch_Encontre_Concepto == 0) { // No encontre el ingreso para tercero en la matrix $mIngTer
          $mIT[$i][7] = ($vCocDat['CLINRPXX'] == "SI") ? $mIT[$i][20] : $mIT[$i][7];
          $nInd_mIngTer = count($mIngTer);

          $mIngTer[$nInd_mIngTer]      = $mIT[$i]; // Ingreso el registro como nuevo.
          $mIngTer[$nInd_mIngTer][2]   = $vTercero[0];
          $mIngTer[$nInd_mIngTer][99]  = $vTercero[1];
          $mIngTer[$nInd_mIngTer][98]  = $vTercero[2];

          $mIngTer[$nInd_mIngTer][100] = ((strlen($mIngTer[$nInd_mIngTer][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$nInd_mIngTer][100]."/".$mIT[$i][5] : $mIngTer[$nInd_mIngTer][100];
					$mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$nInd_mIngTer][100],0,1) == "/") ? substr($mIngTer[$nInd_mIngTer][100],1,strlen($mIngTer[$nInd_mIngTer][100])) : $mIngTer[$nInd_mIngTer][100];
					$mIngTer[$nInd_mIngTer][101] = ($vCtoCon["{$mIngTer[$nInd_mIngTer][1]}"]['umeidxxx'] != "") ? $vCtoCon["{$mIngTer[$nInd_mIngTer][1]}"]['umeidxxx'] : "A9"; // Unidad de medida
        }
      }
    }

		$cCscFac = ($vCocDat['regestxx'] == "PROVISIONAL") ?  "XXXXX" : $vCocDat['comcscxx'];
		// Fin de Codigo para imprimir los ingresos para terceros

		##Traigo la Forma de Pago##
		$cFormaPag = "";
		if ($vComObs2[14] != "") {
			//Buscando descripcion
			$cFormaPag = ($vComObs2[14] == 1) ? "Contado" : "Crédito";
		}
		##FIN Traigo la Forma de Pago##
		
		##Traigo el Medio de Pago##
		$cMedioPago = "";
		if ($vComObs2[15] != "") {
			$qMedPag  = "SELECT mpadesxx ";
			$qMedPag .= "FROM $cAlfa.fpar0155 ";
			$qMedPag .= "WHERE mpaidxxx = \"{$vComObs2[15]}\" AND ";
			$qMedPag .= "regestxx = \"ACTIVO\" LIMIT 0,1";
			$xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
			if (mysql_num_rows($xMedPag) > 0) {
				$vMedPag = mysql_fetch_array($xMedPag);
				$cMedioPago = $vMedPag['mpadesxx'];
			}
		}
		##FIN Traigo el Medio de Pago##

		## Codigo Para Imprimir Original y numero de Copias ##
		$cRoot = $_SERVER['DOCUMENT_ROOT'];

		define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
		require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

		//Generacion del codigo QR
		require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/phpqrcode/qrlib.php');

		##Fin Switch para incluir fuente y clase pdf segun base de datos ##
		class PDF extends FPDF {
			function Header() {
				global $cAlfa;   global $cRoot;   global $cPlesk_Skin_Directory;
				global $gCcoId;  global $gSccId;  global $gMesDes; global $gAnoDes; global $gMesHas; global $gAnoHas;
				global $cUsrNom; global $cCcoDes; global $cScoDes; global $nPag;    global $vAgeDat; global $vCocDat;
				global $vResDat; global $cDocTra; global $cTasCam; global $cDocTra; global $cBultos; global $cPesBru;
				global $cDocId;  global $vCiuDat; global $vDceDat; global $cOpera;  global $nValAdu;  global $vCccDat;
				global $cCscFac; global $vConDat; global $cPaisOrigen; global $cPedido; global $vNomComercial;  global $vSucursal;
				global $cProveedor; global $cVendedor; global $cMedioTransporte; global $cPaisOrigen; global $cLugarIngreso; global $cContenerdor;
				global $vIdContacto; global $cEstiloLetra; global $cAduana;  global $_COOKIE; global $vNomTer; global $cContenedor;
				global $cTitulo; global $cDocSuf; global $cFormaPag; global $cMedioPago;

				//Membrete de la factura
				$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/membrete_roldan.jpg',0,0,212,282);

        if ($vCocDat['regestxx'] == "INACTIVO") {
					$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,180,180);
				}

				if ($_COOKIE['kModo'] == "VERFACTURA"){
      		$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,180,180);
				}

				##Impresion Datos Generales Factura ##
				$posy = 40;
				$posx = 10;
				$bImpFin = false;

				$this->Rect($posx, $posy+5, 97, 20);
				$this->Rect($posx+100, $posy+5, 95, 20);

				//Recuadro lado izquierdo
				$this->setXY($posx,$posy+5);
				$this->SetFont($cEstiloLetra,'B',8);
				$this->Cell(23,4,"Ciudad:",0,0,'L');
				$this->Cell(65,4,$vCocDat['sucdesxx']." - COLOMBIA",0,0,'L');

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
				$this->Cell(65,4,utf8_decode(ucwords(strtolower(substr($cMedioPago, 0, 45)))),0,0,'L');

				//Recuadro lado derecho
				$this->setXY($posx+100,$posy+5);
				$this->SetFont($cEstiloLetra,'B',8);
				$this->Cell(40,4,"Factura de Venta No.:",0,0,'L');
				$this->Cell(55,4,strtoupper($vResDat['resprexx']."-".str_pad($vCocDat['comcscxx'],strlen($vResDat['resdesxx']),"0",STR_PAD_LEFT)),0,0,'L');

				$this->setXY($posx+100,$posy+9);
				$this->SetFont($cEstiloLetra,'B',8);
				$this->Cell(40,4,utf8_decode("Fecha de Emisión:"),0,0,'L');
				$this->SetFont($cEstiloLetra,'',8);
				$this->Cell(55,4,ucfirst(f_Fecha_Letras($vCocDat['comfecxx'])),0,0,'L');

				$this->setXY($posx+100,$posy+13);
				$this->SetFont($cEstiloLetra,'B',8);
				$this->Cell(40,4,utf8_decode("Hora de Emisión:"),0,0,'L');
				$this->SetFont($cEstiloLetra,'',8);
				$this->Cell(55,4,$vCocDat['reghcrex'],0,0,'L');

				$this->setXY($posx+100,$posy+17);
				$this->SetFont($cEstiloLetra,'B',8);
				$this->Cell(40,4,utf8_decode("Fecha de Vencimiento:"),0,0,'L');
				$this->SetFont($cEstiloLetra,'',8);
				$this->Cell(55,4,ucfirst(f_Fecha_Letras($vCocDat['comfecve'])),0,0,'L');

				$this->setXY($posx+100,$posy+21);
				$this->SetFont($cEstiloLetra,'B',8);
				$this->Cell(40,4,utf8_decode("Tasa de Cambio:"),0,0,'L');
				$this->SetFont($cEstiloLetra,'',8);
				$this->Cell(55,4,number_format($vCocDat['tcatasax'],2,",","."),0,0,'L');

				//Columna 1
				$posy += 27;
				$this->setXY($posx, $posy);
        $this->SetFont($cEstiloLetra, 'B', 6);
        $this->Cell(15, 4, utf8_decode("Cliente"), 0, 0, 'L');
        $this->SetFont($cEstiloLetra, '', 6);
				$this->MultiCell(60, 4, substr($vCocDat['CLINOMXX'], 0, 65), 0, 'L');				
        $this->setX($posx);
        $this->SetFont($cEstiloLetra, 'B', 6);
        $this->Cell(15, 4, utf8_decode("NIT"), 0, 0, 'L');
				$this->SetFont($cEstiloLetra, '', 6);
				if($vCocDat['TDIIDXXX'] == "21" ||
					 $vCocDat['TDIIDXXX'] == "22" ||
					 $vCocDat['TDIIDXXX'] == "41" ||
					 $vCocDat['TDIIDXXX'] == "42"){
					$this->MultiCell(60, 4, $vCocDat['terid2xx'], 0, 'L');
				}else{
					$this->MultiCell(60, 4, $vCocDat['terid2xx']."-".f_Digito_Verificacion($vCocDat['terid2xx']), 0, 'L');
				}

        $this->setX($posx);
        $this->SetFont($cEstiloLetra, 'B', 6);
        $this->Cell(15, 4, utf8_decode("Dirección"), 0, 0, 'L');
        $this->SetFont($cEstiloLetra, '', 6);
				$this->MultiCell(60, 4, substr($vCocDat['CLIDIRXX'], 0, 65), 0, 'L');				
        $this->setX($posx);
        $this->SetFont($cEstiloLetra, 'B', 6);
        $this->Cell(15, 4, utf8_decode("Teléfono"), 0, 0, 'L');
        $this->SetFont($cEstiloLetra, '', 6);
        $this->MultiCell(60, 4, $vCocDat['CLITELXX'], 0, 'L');
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
				global $cRoot;  global $cPlesk_Skin_Directory;  global $vCocDat; global $mCodDat;
				global $cEstiloLetra; global $vResDat; global $bImpFin;

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

				$posy = 250;
				$this->SetFont($cEstiloLetra,'B',6);
				$this->setXY($posx,$posy);
				$this->Cell(195,3,"CLIENTE",0,0,'C');

				$posy = 202;
				$this->setXY($posx,$posy);
				$this->SetFont($cEstiloLetra,'B',7);
				$this->Cell(115,4,"",0,0,'R');
				$this->Cell(38,4,"TOTAL",0,0,'L');
				$this->Cell(40,4,($bImpFin == false) ? "0,00": "",0,0,'R');
				$posy += 4;

				$this->setXY($posx,$posy);
				$this->SetFont($cEstiloLetra,'B',7);
				$this->Cell(115,4,"",0,0,'R');
				$this->Cell(38,4,"ANTICIPO",0,0,'L');
				$this->Cell(40,4,($bImpFin == false) ? "0,00": "",0,0,'R');
				$posy += 4;

				$this->setXY($posx,$posy);
				$this->SetFont($cEstiloLetra,'B',7);
				$this->Cell(115,4,"",0,0,'R');
				$this->Cell(38,4,"TOTAL A PAGAR",0,0,'L');
				$this->Cell(40,4,($bImpFin == false) ? "0,00": "",0,0,'R');
				$posy += 4;

				$this->setXY($posx,$posy);
				$this->SetFont($cEstiloLetra,'B',7);
				$this->Cell(115,4,"",0,0,'R');
				$this->Cell(38,4,"SALDO A FAVOR DE CLIENTE",0,0,'L');
				$this->Cell(40,4,($bImpFin == false) ? "0,00": "",0,0,'R');
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

		// $mCodDat = array_merge($mCodDat,$mCodDat,$mCodDat,$mCodDat);
		// $mCodDat = array_merge($mCodDat,$mCodDat,$mCodDat,$mCodDat);
		// $mCodDat = array_merge($mCodDat,$mCodDat,$mCodDat,$mCodDat);

		$pdf->AddPage();

		$mRetFte      = array();
		$mRetIca      = array();
		$mAutoRetIca  = array();
		$mRetIva      = array();
		$mReteCre     = array();

		##Inicializando variables por copia##
		$nTotPag1  = "";	$cSaldo       = "";	$cNeg = "";
		$nTotPag   = 0;		$nTotAnt      = 0;	$nSubToFacIva = 0;	$nSubToFac = 0;
		$nIva      = 0;		$nSubToIP  		= 0; 	$nSubToIPIva  = 0;	$nTotPcc 	 = 0;
		$nSubToPcc = 0;		$nSubToPcc    = 0;	$nSubTotPcc   = 0;
		$nBaseGravados   = 0; 
		$nBaseNoGravados = 0;

		$posy   = $pdf->posy+5;
		$posx   = 10;
		$posFin = 195;
		$pyy    = $posy;
		// Contador de items
		$nConItem = 0;
		$pdf->setXY($posx,$posy);
		##Imprimo Pagos a Terceros ##
		if(count($mIngTer) > 0 || count($mValores) > 0){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
			$nSubTotPcc = 0;
			$pdf->setXY($posx,$pyy);
			$pdf->SetWidths(array(10,20,15,75,20,27,28));
			$pdf->SetAligns(array("C","C","C","L","C","R","R"));

			// Imprimo Titulo Pagos a Terceros
			$pdf->SetFont($cEstiloLetra,'B',6);
			$pdf->setX($posx+45);
			$pdf->Cell(50,4,"PAGOS A TERCEROS EFECTUADOS POR SU CUENTA", 0, 0, 'L');
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
				$nSubTotPcc += $mIngTer[$i][7];

				//Consulto la descripcion de la Unidad de medida
				$cUniMedi  = "";
				$qUniMedi  = "SELECT umedesxx ";
				$qUniMedi .= "FROM $cAlfa.fpar0157 ";
				$qUniMedi .= "WHERE ";
				$qUniMedi .= "umeidxxx = \"{$mIngTer[$i][101]}\" LIMIT 0,1";
				$xUniMedi  = mysql_query($qUniMedi, $xConexion01);
				//f_Mensaje(__FILE__,__LINE__,$qUniMedi." ~ ".mysql_num_rows($xUniMedi));
				while ($xRUM = mysql_fetch_assoc($xUniMedi)) {
					$cUniMedi = strtolower($xRUM['umedesxx']);
				}

				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->setX($posx);
				$mIngTer[$i][2] = (trim($mIngTer[$i][2]) == "GRAVAMEN ARANCELARIO")  ? "ARANCEL DECLARACION DE IMPORTACION" : $mIngTer[$i][2];
				$mIngTer[$i][2] = (trim($mIngTer[$i][2]) == "IMPUESTO A LAS VENTAS") ? "IVA DECLARACION DE IMPORTACION" : $mIngTer[$i][2];
				$pdf->Row(array($nConItem,
												"",
												"1",
												$mIngTer[$i][2],
												utf8_decode(ucwords($cUniMedi)),
												$vCocDat['CLINRPXX'] == "SI" ? number_format($mIngTer[$i][7],2,',','.') : "",
												$vCocDat['CLINRPXX'] != "SI" ? number_format($mIngTer[$i][7],2,',','.') : ""));
			}//for($i=0;$i<count($mIngTer);$i++){

			if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
				$pdf->AddPage();
				$posy = $pdf->posy+5;
				$posx = 10;
				$pyy = $posy;
				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->setXY($posx,$posy);
			}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion
			## Recorro la matriz de la 1002 para imprimir Registros de PCC ##

			for ($i=0;$i<count($mValores);$i++) {
				$nConItem++;
				$pyy = $pdf->GetY();
				if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
					$pdf->AddPage();
					$posy = $pdf->posy+5;
					$posx = 10;
					$pyy = $posy;
					$pdf->SetFont($cEstiloLetra,'',6);
					$pdf->setXY($posx,$posy);
				}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

				//Consulto la descripcion de la Unidad de medida
				$cUniMedi  = "";
				$qUniMedi  = "SELECT umedesxx ";
				$qUniMedi .= "FROM $cAlfa.fpar0157 ";
				$qUniMedi .= "WHERE ";
				$qUniMedi .= "umeidxxx = \"{$mValores[$i][101]}\" LIMIT 0,1";
				$xUniMedi  = mysql_query($qUniMedi, $xConexion01);
				//f_Mensaje(__FILE__,__LINE__,$qUniMedi." ~ ".mysql_num_rows($xUniMedi));
				while ($xRUM = mysql_fetch_assoc($xUniMedi)) {
					$cUniMedi = strtolower($xRUM['umedesxx']);
				}

				$nSubToPcc += $mValores[$i]['comvlrxx'];
				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->setX($posx);
				$pdf->Row(array($nConItem,
												"",
												"1",
												substr(str_replace("CANTIDAD","CANT",$mValores[$i]['comobsxx']),0,100),
												utf8_decode(ucwords($cUniMedi)),
												$vCocDat['CLINRPXX'] == "SI" ? number_format($mValores[$i]['comvlrxx'],2,',','.') : "",
												$vCocDat['CLINRPXX'] != "SI" ? number_format($mValores[$i]['comvlrxx'],2,',','.') : ""));
			}//for ($i=0;$i<count($mValores);$i++) {

			$pyy = $pdf->GetY();

			## Fin Recorro la matriz de la 1002 para imprimir Registros de PCC ##
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
			$pdf->Cell(30,4,$vCocDat['CLINRPXX'] == "SI" ? number_format($nTotPcc,2,',','.') : "",0,0,'R');
			$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? number_format($nTotPcc,2,',','.') : "",0,0,'R');
			$pyy += 8;
			##Fin Imprimo Subtotal de Pagos a Terceros ##
		}//if(count($mIngTer) > 0 || $nBandPcc == 1){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
		##Fin Imprimo Pagos a Terceros ##

		if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$posy = $pdf->posy+5;
			$posx = 10;
			$pyy  = $posy;
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->setXY($posx,$posy);
		}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		if(count($mCodDat) > 0 && $nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
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
			$pdf->Cell(50,4,utf8_decode("SERVICIOS POR INTERMEDIACIÓN"), 0, 0, 'L');
			$pdf->Ln(4);
			// hago dos veces el mismo for para separar los ingresos GRAVADOS y luego los NO GRAVADOS
			for($k=0;$k<(count($mCodDat));$k++) {
				$pyy = $pdf->GetY();
				if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
					$pdf->AddPage();
					$posy = $pdf->posy+5;
					$posx = 10;
					$pyy = $posy;
					$pdf->SetFont($cEstiloLetra,'',6);
					$pdf->setXY($posx,$posy);
				}

				if($mCodDat[$k]['comctocx'] == "IP" && $mCodDat[$k]['comvlr01'] != 0 ) {
					//Contar Items
					$nConItem++;
					
					$cValor = ""; $cValCon = ""; $cValCif = "";
					//Mostrando cantidades por tipo de cantidad
					foreach ($mCodDat[$k]['itemcanx'] as $cKey => $cValue) {
						// Personalizacion de la descripcion por base de datos e informacion adicional
						if($cKey == "FOB") {
							if (($cValue+0) > 0) {
								$cValor  = " FOB: ($".number_format($cValue,2,',','.');
								$cValor .= ($mCodDat[$k]['itemcanx']['TRM'] > 0) ? " TRM: $".number_format($mCodDat[$k]['itemcanx']['TRM'],2,',','.') : "";
								$cValor .= ")";
							}
						} elseif ($cKey == "CIF") {
							if ($cCifUsd > 0) {
								$cValCif  = ($cCifUsd > 0) ? " CIF: (USD ".number_format($cCifUsd,2,',','.').")" : "";
								$cValCif .= ($cValTrm > 0) ? " TRM: ".number_format($cValTrm,2,',','.') : "";
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

					##Busco si el valor es Moneda Extranjera##
					if ($vCocDat['CLINRPXX'] == "SI") {
						$nVlrUnitarioIP = $mCodDat[$k]['comvlrme'];
					} else {
						$nVlrUnitarioIP = $mCodDat[$k]['comvlrxx'];
					}

					## Logica para obtener base de gravados y no gravados
					$nBaseGravados   += ($mCodDat[$k]['comvlr01'] != 0 && $mCodDat[$k]['comvlr01'] != "" ) ? $nVlrUnitarioIP : 0;
					$nBaseNoGravados += ($mCodDat[$k]['comvlr01'] == 0 || $mCodDat[$k]['comvlr01'] == "" ) ? $nVlrUnitarioIP : 0;
					$nSubToIP 			 += $nVlrUnitarioIP;
					$nSubToIPIva 		 += $mCodDat[$k]['comvlr01'];

					//Consulto la descripcion de la Unidad de medida
					$cUniMedi  = "";
					$qUniMedi  = "SELECT umedesxx ";
					$qUniMedi .= "FROM $cAlfa.fpar0157 ";
					$qUniMedi .= "WHERE ";
					$qUniMedi .= "umeidxxx = \"{$mCodDat[$k]['unidadfe']}\" LIMIT 0,1";
					$xUniMedi  = mysql_query($qUniMedi, $xConexion01);
					//f_Mensaje(__FILE__,__LINE__,$qUniMedi." ~ ".mysql_num_rows($xUniMedi));
					while ($xRUM = mysql_fetch_assoc($xUniMedi)) {
						$cUniMedi = strtolower($xRUM['umedesxx']);
					}

					$pdf->SetFont($cEstiloLetra,'',6);
					$pdf->setX($posx);
					$pdf->Row(array($nConItem,
													$mCodDat[$k]['ctoidxxx'],
													number_format($mCodDat[$k]['canfexxx'],0),
													trim($mCodDat[$k]['comobsxx'].$cValor),
													utf8_decode(ucwords($cUniMedi)),
													$vCocDat['CLINRPXX'] == "SI" ? number_format($nVlrUnitarioIP,2,',','.') : "",
													$vCocDat['CLINRPXX'] != "SI" ? number_format($nVlrUnitarioIP,2,',','.') : ""));

				}//if($mCodDat[$k]['comctocx'] == 'IP'){
			}## for($k=$nPosIP;$k<(count($mCodDat));$k++) { ##

			for($k=0;$k<(count($mCodDat));$k++) {
				$pyy = $pdf->GetY();
				if($pyy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
					$pdf->AddPage();
					$posy = $pdf->posy+5;
					$posx = 10;
					$pyy = $posy;
					$pdf->SetFont($cEstiloLetra,'',6);
					$pdf->setXY($posx,$posy);
				}

				if($mCodDat[$k]['comctocx'] == "IP" && $mCodDat[$k]['comvlr01'] == 0 ) {
					//Contar Items
					$nConItem++;

					$cValor = ""; $cValCon = ""; $cValCif = "";
					//Mostrando cantidades por tipo de cantidad
					foreach ($mCodDat[$k]['itemcanx'] as $cKey => $cValue) {

						// Personalizacion de la descripcion por base de datos e informacion adicional
						if($cKey == "FOB") {
							if (($cValue+0) > 0) {
								$cValor  = " FOB: ($".number_format($cValue,2,',','.');
								$cValor .= ($mCodDat[$k]['itemcanx']['TRM'] > 0) ? " TRM: $".number_format($mCodDat[$k]['itemcanx']['TRM'],2,',','.') : "";
								$cValor .= ")";
							}
						} elseif ($cKey == "CIF") {
							if ($cCifUsd > 0) {
								$cValCif  = ($cCifUsd > 0) ? " CIF: (USD ".number_format($cCifUsd,2,',','.').")" : "";
								$cValCif .= ($cValTrm > 0) ? " TRM: ".number_format($cValTrm,2,',','.') : "";
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

					##Busco si el valor es Moneda Extranjera##
					if ($vCocDat['CLINRPXX'] == "SI") {
						$nVlrUnitarioIP = $mCodDat[$k]['comvlrme'];
					} else {
						$nVlrUnitarioIP = $mCodDat[$k]['comvlrxx'];
					}

					## Logica para obtener base de gravados y no gravados
					$nBaseGravados   +=  ($mCodDat[$k]['comvlr01'] != 0 && $mCodDat[$k]['comvlr01'] != "" ) ? $nVlrUnitarioIP : 0;
					$nBaseNoGravados +=  ($mCodDat[$k]['comvlr01'] == 0 || $mCodDat[$k]['comvlr01'] == "" ) ? $nVlrUnitarioIP : 0;
					$nSubToIP        += $nVlrUnitarioIP;
					$nSubToIPIva     += $mCodDat[$k]['comvlr01'];

					//Consulto la descripcion de la Unidad de medida
					$cUniMedi  = "";
					$qUniMedi  = "SELECT umedesxx ";
					$qUniMedi .= "FROM $cAlfa.fpar0157 ";
					$qUniMedi .= "WHERE ";
					$qUniMedi .= "umeidxxx = \"{$mCodDat[$k]['unidadfe']}\" LIMIT 0,1";
					$xUniMedi  = mysql_query($qUniMedi, $xConexion01);
					//f_Mensaje(__FILE__,__LINE__,$qUniMedi." ~ ".mysql_num_rows($xUniMedi));
					while ($xRUM = mysql_fetch_assoc($xUniMedi)) {
						$cUniMedi = strtolower(ucwords($xRUM['umedesxx']));
					}

					$pdf->SetFont($cEstiloLetra,'',6);
					$pdf->setX($posx);
					$pdf->Row(array($nConItem,
													$mCodDat[$k]['ctoidxxx'],
													number_format($mCodDat[$k]['canfexxx'],0),
													trim($mCodDat[$k]['comobsxx'].$cValor),
													utf8_decode(ucwords($cUniMedi)),
													$vCocDat['CLINRPXX'] == "SI" ? number_format($nVlrUnitarioIP,2,',','.') : "",
													$vCocDat['CLINRPXX'] != "SI" ? number_format($nVlrUnitarioIP,2,',','.') : ""));

				}//if($mCodDat[$k]['comctocx'] == 'IP'){
			}## for($k=$nPosIP;$k<(count($mCodDat));$k++) { ##

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
			$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? number_format($nSubToIP,2,',','.') : "",0,0,'R');
			$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? number_format($nSubToIP,2,',','.') : "",0,0,'R');
			$pyy += 4;
			##Imprimo Subtotal de Ingresos Propios ##
		}//if(count($mCodDat) > 0 || $nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS

		##Busco valor de IVA ##
		$nIva = 0;
		for ($k=0;$k<count($mCodDat);$k++) {
			if($mCodDat[$k]['comctocx'] == 'IVAIP'){
				$nIva += ($vCocDat['CLINRPXX'] == "SI") ? $mCodDat[$k]['comvlrme'] : $mCodDat[$k]['comvlrxx'];
			}
		}
		##Fin Busco Valor de IVA ##

		##Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##
		$nSubToFac = $nTotPcc + $nSubToIP;
		$nSubToFacIva += $nIva;
		##Fin Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##

		##Busco valor de Anticipo ##
		$cNegativo = "";
		$cNeg = "";
		$nTotAnt = 0;
		/*El codigo se comenta porque para los SS no esta guardando correctamente el valor del anticipo
		for ($k=0;$k<count($mCodDat);$k++) {
			if($mCodDat[$k]['comctocx'] == "SS" || $mCodDat[$k]['comctocx'] == "SC"){
				if($mCodDat[$k]['comctocx'] == "SC"){
					$cNegativo = "MENOS ";
				}
				$nTotAnt += $mCodDat[$k]['comvlr01'];
				f_Mensaje(__FILE__, __LINE__, $mCodDat[$k]['comctocx']."~".$mCodDat[$k]['comvlr01']."~".$nTotAnt);
			}
		}*/
		/*
		* En caso de que el valor a pagar de la Factura sea cero, en detalle no se guarda registro SS o SC,
		* Razon por la cual no se muestra el valor del anticipo que fue aplicado.
		* Para imprimir este valor se debe tomar el campo comfpxx de cabecera, posicion 13 donde se guarda el valor del anticipo
		*/
		if ($vCocDat['CLINRPXX'] == "SI") {
			for ($k=0;$k<count($mCodDat);$k++) {
				if($mCodDat[$k]['comctocx'] == 'CD' && strpos($mCodDat[$k]['comobsxx'],'ANTICIPOS') > 0){
					$nTotAnt += ($vCocDat['CLINRPXX'] == "SI") ? $mCodDat[$k]['comvlrme'] : $mCodDat[$k]['comvlrxx'];
				}
			}
		} else {
			$mComFp = f_Explode_Array($vCocDat['comfpxxx'],"|","~");
			for ($k=0;$k<count($mComFp);$k++) {
				if($mComFp[$k][13] != "" && $mComFp[$k][13] != 0){
					$nTotAnt += $mComFp[$k][13];
				}
			}
		}
		/*
		* Fin de Recorrido al campo comfpxxx para imprimir valor de anticipo.
		*/
		##Fin Busco valor de Anticipo ##
		##Busco Valor a Pagar ##
		$nTotPag = 0;
		for ($k=0;$k<count($mCodDat);$k++) {
			if($mCodDat[$k]['comctocx'] == "SS" || $mCodDat[$k]['comctocx'] == "SC"){
				if($mCodDat[$k]['comctocx'] == "SC"){
					$cSaldo = "SU FAVOR";
				} else {
					$cSaldo = "CARGO";
				}
				$nTotPag += ($vCocDat['CLINRPXX'] == "SI") ? $mCodDat[$k]['comvlrme'] : $mCodDat[$k]['comvlrxx'];
			}
		}
		##Fin Busco Valor a Pagar ##

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
								$mRetFte[$j]['comvlrme'] += $mCodDat[$k]['comvlrme'];
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
							$mRetFte[$nInd_mRetFte]['comvlrme'] = $mCodDat[$k]['comvlrme'];
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
								$mRetIca[$j]['comvlrme'] += $mCodDat[$k]['comvlrme'];
								$mRetIca[$j]['basexxxx'] += $mCodDat[$k]['comvlr01'];
							}
						}
						if ($nSwitch_Encontre_Porcentaje == 0) {
							$nInd_mRetIca = count($mRetIca);
							$mRetIca[$nInd_mRetIca]['tipretxx'] = "ICA";
							$mRetIca[$nInd_mRetIca]['pucretxx'] = $xRPD['pucretxx'];
							$mRetIca[$nInd_mRetIca]['basexxxx'] = $mCodDat[$k]['comvlr01'];
							$mRetIca[$nInd_mRetIca]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
							$mRetIca[$nInd_mRetIca]['comvlrme'] = $mCodDat[$k]['comvlrme'];
						}
					}//while($xRPD = mysql_fetch_array($xPucDat)){
				}//if($nFilPuc > 0){
			}//if($mCodDat[$k]['comctocx'] == 'RETICA'){

			// Auto Retencion de ICA
			if($mCodDat[$k]['comctocx'] == 'ARETICA'){
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
						for ($j=0;$j<count($mAutoRetIca);$j++) {
							if($mAutoRetIca[$j]['pucretxx'] == $xRPD['pucretxx']){
								$nSwitch_Encontre_Porcentaje = 1;
								$mAutoRetIca[$j]['comvlrxx'] += $mCodDat[$k]['comvlrxx'];
								$mAutoRetIca[$j]['comvlrme'] += $mCodDat[$k]['comvlrme'];
								$mAutoRetIca[$j]['basexxxx'] += $mCodDat[$k]['comvlr01'];
							}
						}
						if ($nSwitch_Encontre_Porcentaje == 0) {
							$nInd_mAutoRetIca = count($mAutoRetIca);
							$mAutoRetIca[$nInd_mAutoRetIca]['tipretxx'] = "AICA";
							$mAutoRetIca[$nInd_mAutoRetIca]['pucretxx'] = $xRPD['pucretxx'];
							$mAutoRetIca[$nInd_mAutoRetIca]['basexxxx'] = $mCodDat[$k]['comvlr01'];
							$mAutoRetIca[$nInd_mAutoRetIca]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
							$mAutoRetIca[$nInd_mAutoRetIca]['comvlrme'] = $mCodDat[$k]['comvlrme'];
						}
					}//while($xRPD = mysql_fetch_array($xPucDat)){
				}//if($nFilPuc > 0){
			}//if($mCodDat[$k]['comctocx'] == 'ARETICA'){

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
								$mRetIva[$j]['comvlrme'] += $mCodDat[$k]['comvlrme'];
								$mRetIva[$j]['basexxxx'] += $mCodDat[$k]['comvlr01'];
							}
						}
						if ($nSwitch_Encontre_Porcentaje == 0) {
							$nInd_mRetIva = count($mRetIva);
							$mRetIva[$nInd_mRetIva]['tipretxx'] = "IVA";
							$mRetIva[$nInd_mRetIva]['pucretxx'] = $xRPD['pucretxx'];
							$mRetIva[$nInd_mRetIva]['basexxxx'] = $mCodDat[$k]['comvlr01'];
							$mRetIva[$nInd_mRetIva]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
							$mRetIva[$nInd_mRetIva]['comvlrme'] = $mCodDat[$k]['comvlrme'];
						}
					}//while($xRPD = mysql_fetch_array($xPucDat)){
				}//if($nFilPuc > 0){
			}//if($mCodDat[$k]['comctocx'] == 'RETIVA'){

			if($mCodDat[$k]['comctocx'] == 'RETCRE'){
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
						for ($j=0;$j<count($mReteCre);$j++) {
							if($mReteCre[$j]['pucretxx'] == $xRPD['pucretxx']){
								$nSwitch_Encontre_Porcentaje = 1;
								$mReteCre[$j]['comvlrxx'] += $mCodDat[$k]['comvlrxx'];
								$mReteCre[$j]['comvlrme'] += $mCodDat[$k]['comvlrme'];
								$mReteCre[$j]['basexxxx'] += $mCodDat[$k]['comvlr01'];
							}
						}
						if ($nSwitch_Encontre_Porcentaje == 0) {
							$nInd_mReteCre = count($mReteCre);
							$mReteCre[$nInd_mReteCre]['tipretxx'] = "CREE";
							$mReteCre[$nInd_mReteCre]['pucretxx'] = $xRPD['pucretxx'];
							$mReteCre[$nInd_mReteCre]['basexxxx'] = $mCodDat[$k]['comvlr01'];
							$mReteCre[$nInd_mReteCre]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
							$mReteCre[$nInd_mReteCre]['comvlrme'] = $mCodDat[$k]['comvlrme'];
						}
					}//while($xRPD = mysql_fetch_array($xPucDat)){
				}//if($nFilPuc > 0){
			}
		}

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
		$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? number_format($nSubToFacIva,2,',','.') : "",0,0,'R');
		$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? number_format($nSubToFacIva,2,',','.') : "",0,0,'R');
		$posy += 4;

		if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$posy = $pdf->posy+5;
			$posx = 10;
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->setXY($posx,$posy);
		}//if($posy < 190){//Validacion para siguiente pagina si se excede espacio de impresion

		$pdf->setXY($posx+45,$posy);
		$pdf->SetFont($cEstiloLetra,'B',6);
		$pdf->Cell(75,4,"SUBTOTAL",0,0,'L');
		$pdf->Cell(20,4,"",0,0,'C');
		$pdf->SetFont($cEstiloLetra,'B',6);
		$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? number_format(abs($nSubToFac+$nSubToFacIva),2,',','.') : "",0,0,'R');
		$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? number_format(abs($nSubToFac+$nSubToFacIva),2,',','.') : "",0,0,'R');
		$posy += 4;

		if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$posy = $pdf->posy+5;
			$posx = 10;
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
			$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? number_format(abs($mRetFte[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
			$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? number_format(abs($mRetFte[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
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
					$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? number_format(abs($mRetIca[$i]['comvlrme']),2,',','.') : "",0,0,'R');
					$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? number_format(abs($mRetIca[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
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
				$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? number_format(abs($mRetIva[$i]['comvlrme']),2,',','.') : "",0,0,'R');
				$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? number_format(abs($mRetIva[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
				$posy +=4;
			}
		}

		/*for($i=0;$i<count($mReteCre);$i++){
			if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
				$pdf->AddPage();
				$posy = $pdf->posy+5;
				$posx = 10;
				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->setXY($posx,$posy);
			}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

			$pdf->setXY($posx+45,$posy);
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->Cell(95,5,"Retencion ".$mReteCre[$i]['tipretxx']." del ".($mReteCre[$i]['pucretxx']+0)."%",0,0,'L');
			$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? number_format(abs($mReteCre[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
			$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? number_format(abs($mReteCre[$i]['comvlrxx']),2,',','.') : "",0,0,'R');
			$posy +=4;
		}*/
		##FIN RETENCIONES##

		if( $vCocDat['CLINRPXX'] == "SI" ){
			$nTotLet = round($nSubToFac + $nSubToFacIva,2)+0;
		}else if( $vCocDat['CLINRPXX'] != "SI" ) {
			$nTotLet = round($vCocDat['comvlr02'] + $vCocDat['comvlr03'] + $vCocDat['comifxxx'] + $vCocDat['comipxxx'] + $vCocDat['comivaxx'] - ($vCocDat['comrftex'] + $vCocDat['comrcrex'] + $vCocDat['comrivax'] + $vCocDat['comricax']) + ($vCocDat['comarfte'] + $vCocDat['comarcre'] + $vCocDat['comarica']),2)+0;
		}

		##Busco valor de Anticipo DIAN ##
		$nAnticipoDian = (abs($nTotLet) > abs($nTotAnt)) ? abs($nTotAnt) : abs($nTotLet);
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
		$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? number_format(abs($nTotLet),2,',','.') : "",0,0,'R');
		$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? number_format(abs($nTotLet),2,',','.') : "",0,0,'R');
		$posy += 4;

		if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
			$pdf->AddPage();
			$posy = $pdf->posy+5;
			$posx = 10;
			$pdf->SetFont($cEstiloLetra,'',6);
			$pdf->setXY($posx,$posy);
		}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

		##Imprimo Valor en Letras##
		$cMoneda  = $vCocDat['CLINRPXX'] != "SI" ? "PESO" : "DOLAR" ;
		$nTotPag1 = trim(f_Cifra_Php(str_replace("-","",abs($nTotLet)),$cMoneda));
		$pdf->setXY($posx+45,$posy);
		$pdf->SetFont($cEstiloLetra,'',6);
		$pdf->MultiCell(75,3,utf8_decode(str_replace("DOLARES", "DOLÁRES", $nTotPag1)),0,'L');
		$posy = $pdf->getY()+1;

		##Imprimo Observacion##
		if($vCocDat['comobsxx'] != ""){
			if($posy > $posFin){//Validacion para siguiente pagina si se excede espacio de impresion
				$pdf->AddPage();
				$posy = $pdf->posy+5;
				$posx = 10;
				$pdf->SetFont($cEstiloLetra,'',6);
				$pdf->setXY($posx,$posy);
			}//if($posy < 130){//Validacion para siguiente pagina si se excede espacio de impresion

			$nObs = explode("~",f_Words($vCocDat['comobsxx'],60));
			for ($n=0;$n<count($nObs) -1 ;$n++) {
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

		$posy = 202;
		$bImpFin = true;
		$pdf->setXY($posx,$posy);
		$pdf->SetFont($cEstiloLetra,'B',7);
		$pdf->Cell(140,4,"",0,0,'R');
		$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? number_format(abs($nTotLet),2,',','.') : "",0,0,'R');
		$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? number_format(abs($nTotLet),2,',','.') : "",0,0,'R');
		$posy += 4;

		$pdf->setXY($posx,$posy);
		$pdf->SetFont($cEstiloLetra,'B',7);
		$pdf->Cell(140,4,"",0,0,'R');
		$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? number_format(abs($nTotAnt),2,',','.') : "",0,0,'R');
		$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? number_format(abs($nTotAnt),2,',','.') : "",0,0,'R');
		$posy += 4;

		$pdf->setXY($posx,$posy);
		$pdf->SetFont($cEstiloLetra,'B',7);
		$pdf->Cell(140,4,"",0,0,'R');
		if ($cSaldo == "CARGO") {
			$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? number_format(abs($nTotPag),2,',','.') : "",0,0,'R');
			$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? number_format(abs($nTotPag),2,',','.') : "",0,0,'R');
		} else {
			$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? "0,00" : "",0,0,'R');
			$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? "0,00" : "",0,0,'R');
		}
		$posy += 4;

		$pdf->setXY($posx,$posy);
		$pdf->SetFont($cEstiloLetra,'B',7);
		$pdf->Cell(140,4,"",0,0,'R');
		if ($cSaldo == "SU FAVOR") {
			$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? number_format(abs($nTotPag),2,',','.') : "",0,0,'R');
			$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? number_format(abs($nTotPag),2,',','.') : "",0,0,'R');
		} else {
			$pdf->Cell(27,4,$vCocDat['CLINRPXX'] == "SI" ? "0,00" : "",0,0,'R');
			$pdf->Cell(28,4,$vCocDat['CLINRPXX'] != "SI" ? "0,00" : "",0,0,'R');
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

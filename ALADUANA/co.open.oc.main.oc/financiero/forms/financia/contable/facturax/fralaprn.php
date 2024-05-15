<?php
  /**
	 * Imprime Factura de Venta ALADUANA.
	 * --- Descripcion: Permite Imprimir Factura de Venta.
	 * @author Camilo Dulce <camilo.dulce@opentecnologia.com.co>
	 */
  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");

  $nSwitch=0;
  $vMemo=explode("|",$prints);

  // Validacion de Comprobante Repetido
  $mPrints = f_Explode_Array($prints,"|","~");
  $cAno    = substr($mPrints[0][4],0,4);
  $cEstiloLetra = 'arial';
  $cEstiloLetraOfton = 'ofton1';
  $cEstiloLetrab = 'arialb';

  $gRetenciones = isset($_REQUEST['gRetenciones']) && $gRetenciones == "NO" ? 0 : 1;

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
    	##Fin Select a la 1001 para traer el campo que se marca cuando se ha impreso factura##
    }//if ($vMemo[$u]!=""){
  }//for($u=0; $u<count($vMemo); $u++) {

  ##Codigo que valida si hay errores para permitir o NO la Impresion de la Factura ##
  ##El Codigo se comenta a solicitud del cliente, no debe hacerse control de re-impresion de la factura##
  /*if($permisos==1){
    $nSwitch=1;
    f_Mensaje(__FILE__,__LINE__,"Las Siguientes Facturas No tienen Permiso de Impresion [$zCadPer], Verifique.");?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
			<script languaje = "javascript">
		  	parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
		  	document.forms['frgrm'].submit();
			</script>
    <?php
  }*/
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
  if($nSwitch == 0) {

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

    /*** Nombre del usuario logueado. ***/
    $qUsrNom  = "SELECT USRNOMXX ";
    $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
    $qUsrNom .= "WHERE ";
    $qUsrNom .= "USRIDXXX = \"$kUser\" LIMIT 0,1 ";
    $xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
  	// f_Mensaje(__FILE__,__LINE__,$qUsrNom."~".mysql_num_rows($xUsrNom));
	  $vUsrNom  = mysql_fetch_array($xUsrNom);

  	////// CABECERA 1001 /////
  	$qCocDat  = "SELECT ";
  	$qCocDat .= "$cAlfa.fcoc$cNewYear.*, ";
  	$qCocDat .= "IF($cAlfa.fpar0008.sucidxxx != \"\",$cAlfa.fpar0008.sucidxxx,\"SUCURSAL SIN ID\") AS sucidxxx, ";
  	$qCocDat .= "IF($cAlfa.fpar0008.sucdesxx != \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS CLINOMXX, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.CLIDIR3X != \"\",$cAlfa.SIAI0150.CLIDIR3X,\"SIN DIRECCION\") AS CLIDIR3X, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.CLICONTX != \"\",$cAlfa.SIAI0150.CLICONTX,\"SIN RESPONSABLE\") AS CLICONTX, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.CLINRPXX != \"\",$cAlfa.SIAI0150.CLINRPXX,\"\") AS CLINRPXX, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.CLIPLAXX != \"\",$cAlfa.SIAI0150.CLIPLAXX,\"\") AS CLIPLAXX ";
  	$qCocDat .= "FROM $cAlfa.fcoc$cNewYear ";
  	$qCocDat .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcoc$cNewYear.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
    $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cNewYear.terid2xx = $cAlfa.SIAI0150.CLIIDXXX ";
  	$qCocDat .= "WHERE ";
  	$qCocDat .= "$cAlfa.fcoc$cNewYear.comidxxx = \"$cComId\"  AND ";
  	$qCocDat .= "$cAlfa.fcoc$cNewYear.comcodxx = \"$cComCod\" AND ";
  	$qCocDat .= "$cAlfa.fcoc$cNewYear.comcscxx = \"$cComCsc\" AND ";
  	$qCocDat .= "$cAlfa.fcoc$cNewYear.comcsc2x = \"$cComCsc2\" LIMIT 0,1";

  	//f_Mensaje(__FILE__,__LINE__,$qCocDat);
  	$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
  	$nFilCoc  = mysql_num_rows($xCocDat);
  	if ($nFilCoc > 0) {
  	  $vCocDat  = mysql_fetch_array($xCocDat);
  	}
  	//////////////////////////////////////////////////////////////////////////////////////

    // $cCamVlr = ($vCocDat['CLINRPXX'] == "SI") ? "comvlrme" : "comvlrxx";
    $cCamVlr = "comvlrxx";
  	////// DETALLE 1002 /////
  	$qCodDat  = "SELECT DISTINCT ";
    $qCodDat .= "$cAlfa.fcod$cNewYear.* ";
    $qCodDat .= "FROM $cAlfa.fcod$cNewYear ";
  	$qCodDat .= "WHERE $cAlfa.fcod$cNewYear.comidxxx = \"$cComId\" AND ";
  	$qCodDat .= "$cAlfa.fcod$cNewYear.comcodxx = \"$cComCod\" AND ";
  	$qCodDat .= "$cAlfa.fcod$cNewYear.comcscxx = \"$cComCsc\" AND ";
  	$qCodDat .= "$cAlfa.fcod$cNewYear.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cNewYear.comseqxx) ASC ";
  	$xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
  	// f_Mensaje(__FILE__,__LINE__,$qCodDat);
  	$nFilCod  = mysql_num_rows($xCodDat);

  	/*** Matriz para pagos de 4xmil GMF ***/
  	$mDatGmf = array();

  	if ($nFilCod > 0) {
      //Cargo la Matriz con los ROWS del Cursor
      $mCodDat = array();
  		while ($xRCD = mysql_fetch_array($xCodDat)) {

        if($xRCD['comctocx'] == "PCC" && $xRCD['comidc2x'] != "X"){
          //donde el campo pucidxxx like '4%' y el campo cmoctocx = 'PCC'
          $nInd_mDatGmf = count($mDatGmf);
          $mDatGmf[$nInd_mDatGmf]['ctoidxxx'] = $xRCD['ctoidxxx'];
          $mDatGmf[$nInd_mDatGmf]['comobsxx'] = $xRCD['comobsxx'];
          $mDatGmf[$nInd_mDatGmf]['comvlrxx'] = $xRCD[$cCamVlr];
          $mDatGmf[$nInd_mDatGmf]['puctipej'] = $xRCD['puctipej'];
          $mDatGmf[$nInd_mDatGmf]['comvlr01'] = $xRCD['comvlr01'];
        }else if ($xRCD['comctocx'] == "PCC") {
  				//donde el campo pucidxxx like '4%' y el campo cmoctocx = 'PCC'
          $nInd_mValores = count($mValores);
          $mValores[$nInd_mValores]['ctoidxxx'] = $xRCD['ctoidxxx'];
  				$mValores[$nInd_mValores]['comobsxx'] = $xRCD['comobsxx'];
  				$mValores[$nInd_mValores]['comvlrxx'] = $xRCD[$cCamVlr];
  				$mValores[$nInd_mValores]['puctipej'] = $xRCD['puctipej'];
  				$mValores[$nInd_mValores]['comvlr01'] = $xRCD['comvlr01'];

  			} else if ($xRCD['comctocx'] == "IP") {

          $nSwitch_Encontre_Concepto = 0;
					//Trayendo descripcion concepto, cantidad y unidad
					$mComObs_IP = f_Explode_Array($xRCD['comobsxx'],"|","~");

					//Traigo las cantidades y el detalle de los IP del utiliqdo.php
					$vDatosIp = array();
          $vDatosIp = f_Cantidad_Ingreso_Propio($xRCD['comobsxx'],'',$xRCD['sucidxxx'],$xRCD['docidxxx'],$xRCD['docsufxx']);

					//Los IP se agrupan por Sevicio
					for($j=0;$j<count($mCodDat);$j++){
						if($mCodDat[$j]['ctoidxxx'] == $xRCD['ctoidxxx'] && $mCodDat[$j]['seridxxx'] == $xRCD['seridxxx']){
							$nSwitch_Encontre_Concepto = 1;

							$mCodDat[$j]['comvlrxx'] += $xRCD['comvlrxx'];
							$mCodDat[$j]['comvlrme'] += $xRCD['comvlrme'];
							$mCodDat[$j]['compivax']  = $xRCD['compivax']; // Porcentaje IVA
							$mCodDat[$j]['comvlr01'] += $xRCD['comvlr01']; // Valor Iva
							//Cantidad FE
							$mCodDat[$j]['canfexxx'] += $vDatosIp[1];
							//Cantidad por condicion especial
							for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
								$mCodDat[$j]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] += $vDatosIp[3][$nP]['valpdfxx'];
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

					for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
						$mCodDat[$nInd_mConData]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['valpdfxx'];
					}
        }
  		}
  		// Fin de Cargo la Matriz con los ROWS del Cursor
    }

    /*** Consulto los anticipos en el campo memo de cabecera commempa y se llena la matriz de anticipos ***/
    $mAnticipos = array();
    $mComMemPa = explode("|", $vCocDat['commempa']);
    for( $nCMP = 0; $nCMP < count($mComMemPa); $nCMP++ ) {
      $mAntAux = explode("~", $mComMemPa[$nCMP]);
      if($mAntAux[0] == "ANTICIPO"){
        $nInd_mAnticipos = count($mAnticipos);
        $mAnticipos[$nInd_mAnticipos]['comfecxx'] = $mAntAux[8];
        $mAnticipos[$nInd_mAnticipos]['puctipej'] = $mAntAux[13];
        $mAnticipos[$nInd_mAnticipos]['comvlrxx'] = $mAntAux[14];
        $mAnticipos[$nInd_mAnticipos]['comvlrnf'] = $mAntAux[15];
        $mAnticipos[$nInd_mAnticipos]['comcscxx'] = $mAntAux[3];
      }
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
    $nFilRes  = mysql_num_rows($xResDat);
    if ($nFilRes > 0) {
      $vResDat = mysql_fetch_array($xResDat);
    }
  	##Fin Traigo Datos de la Resolucion ##

  	##Traigo Pais del Cliente ##
    $qPaiDat  = "SELECT PAIDESXX ";
    $qPaiDat .= "FROM $cAlfa.SIAI0052 ";
    $qPaiDat .= "WHERE ";
    $qPaiDat .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
    $qPaiDat .= "$cAlfa.SIAI0052.REGESTXX = \"ACTIVO\" ";
    //f_Mensaje(__FILE__,__LINE__,$qPaiDat);
    $xPaiDat  = f_MySql("SELECT","",$qPaiDat,$xConexion01,"");
    $nFilCiu  = mysql_num_rows($xPaiDat);
    if ($nFilCiu > 0) {
      $vPaiDat = mysql_fetch_array($xPaiDat);
    }
    ##Fin Traigo Pais del Cliente ##

  	##Traigo Ciudad del Cliente ##
    $qCiuDat  = "SELECT * ";
    $qCiuDat .= "FROM $cAlfa.SIAI0055 ";
    $qCiuDat .= "WHERE ";
    $qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
    $qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
    $qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCocDat['CIUIDXXX']}\" AND ";
    $qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
    $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qCiuDat."~".mysql_num_rows($xCiuDat));
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
    $cDocId  = ""; $cDocSuc = ""; $cDocSuf = ""; $cDocIdImp = "";
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
      }//if($mDoiId[$i] != ""){
    }//for ($i=0;$i<count($mDoiId);$i++) {
    $nAnoIniDo = (($dFecMay-1) <  $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($dFecMay-1);
    ##Fin Exploto campo Matriz para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##

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

    ## Busco codigos de homologacion ##
    //Buscano conceptos de causaciones automaticas
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
    ## Fin Busco codigos de homologacion ##

    ## Conceptos de cobro
    $qCtoCob  = "SELECT ctoidxxx, serclapr, cceidxxx, umeidxxx  ";
    $qCtoCob .= "FROM $cAlfa.fpar0129 ";
    $xCtoCob  = mysql_query($qCtoCob, $xConexion01);
    $vCtoSer = array();
    while ($xRS = mysql_fetch_assoc($xCtoCob)) {
      $vCtoSer["{$xRS['ctoidxxx']}"] = $xRS;
    }
    ## Fin Conceptos de cobro

    ## Codigo para imprimir los ingresos para terceros ##
    $mIT = f_Explode_Array($vCocDat['commemod'],"|","~");
    $mIngTer = array();

    for ($i=0;$i<count($mIT);$i++) {
      //Traer descripcion concepto
      $cObs = explode("^",$mIT[$i][2]);

      if(trim($cObs[0]) == ""){
        $qConCon  = "SELECT ";
        $qConCon .= "$cAlfa.fpar0119.ctodesxp, ";
        $qConCon .= "$cAlfa.fpar0119.ctodesxl, ";
        $qConCon .= "$cAlfa.fpar0119.ctodesxr, ";
        $qConCon .= "$cAlfa.fpar0119.ctodesxx ";
        $qConCon .= "FROM $cAlfa.fpar0119 ";
        $qConCon .= "WHERE ";
        $qConCon .= "$cAlfa.fpar0119.ctoidxxx = \"{$mIT[$i][2]}\" LIMIT 0,1 ";
        $xConCon  = f_MySql("SELECT","",$qConCon,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qConCon."~".mysql_num_rows($xConCon)."~".$mComMemPa[$nCMP][3]."~".$mComMemPa[$nCMP][5]);

        if (mysql_num_rows($xConCon) > 0) {
          $vConCon = mysql_fetch_array($xConCon);
          if($vConCon['ctodesxp'] != ""){
            $cObs[0]	= $vConCon['ctodesxp'];
          }elseif($vConCon['ctodesxl']!= ""){
            $cObs[0]	= $vConCon['ctodesxl'];
          }elseif($vConCon['ctodesxr']!= ""){
            $cObs[0]	= $vConCon['ctodesxr'];
          }else{
            $cObs[0] = $vConCon["ctodesxx"];
          }
        }else{
          $qConCca  = "SELECT ";
          $qConCca .= "$cAlfa.fpar0121.ctodesxx ";
          $qConCca .= "FROM $cAlfa.fpar0121 ";
          $qConCca .= "WHERE ";
          $qConCca .= "$cAlfa.fpar0121.ctoidxxx = \"{$mIT[$i][2]}\" LIMIT 0,1";
          $xConCca  = f_MySql("SELECT","",$qConCca,$xConexion01,"");
          if (mysql_num_rows($xConCca) > 0) {
            $vConCca = mysql_fetch_array($xConCca);
            $cObs[0] = $vConCca["ctodesxx"];
          }else{
            $vPucId = mysql_fetch_array($xPucId);
            $qPucDat  = "SELECT ";
            $qPucDat .= "$cAlfa.fpar0115.pucdesxx ";
            $qPucDat .= "FROM $cAlfa.fpar0115 ";
            $qPucDat .= "WHERE ";
            $qPucDat .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$mIT[$i][9]}\" ";
            $qPucDat .= "LIMIT 0,1";
            $xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
            if (mysql_num_rows($xPucDat) > 0) {
              $vPucDat = mysql_fetch_array($xPucDat);
              $cObs[0] = $vPucDat["pucdesxx"];
            }else{
              $cObs[0] = "";
            }
          }#if (mysql_num_rows($xConCca) > 0) {
        }#if (mysql_num_rows($xConCon) > 0) {
      }

      $nInd_mIngTer = count($mIngTer);
      $mIngTer[$nInd_mIngTer]['ctochald'] = $mCtoDes["{$mIT[$i][1]}"];//Cod
      $mIngTer[$nInd_mIngTer]['ctodesxx'] = trim($cObs[0]);//Descripcion
      $mIngTer[$nInd_mIngTer]['ctoidxxx'] = $mIT[$i][1];//CtoId

  
      if (trim($cObs[0]) == "IMPUESTO A LAS VENTAS" && trim($cObs[1]) == "DIAN") {
        $mIngTer[$nInd_mIngTer]['costoxxx'] = 0;//Costo
        $mIngTer[$nInd_mIngTer]['ivaxxxxx'] = $mIT[$i][7]+0;//Iva
        $mIngTer[$nInd_mIngTer]['totalxxx'] = $mIT[$i][7]+0;//Total
      } else {
        //Si tiene IVA
        //en el item se envia el valor de la base, costo 
        //y se crea un registro nuevo con el valor del IVA
        if(($mIT[$i][16]+0) > 0){
          $mIngTer[$nInd_mIngTer]['costoxxx'] = $mIT[$i][7] - $mIT[$i][16];//Costo
          $mIngTer[$nInd_mIngTer]['ivaxxxxx'] = 0;//Iva
          $mIngTer[$nInd_mIngTer]['totalxxx'] = $mIT[$i][7] - $mIT[$i][16];//Total

          $nCosto += $mIngTer[$nInd_mIngTer]['costoxxx'];
          $nIva   += $mIngTer[$nInd_mIngTer]['ivaxxxxx'];
          $nTotal += $mIngTer[$nInd_mIngTer]['totalxxx'];

          //Creo una nueva linea para iva
          $nInd_mIngTer = count($mIngTer);
          $mIngTer[$nInd_mIngTer]['ctochald'] = str_replace(substr($mCtoDes["{$mIT[$i][1]}"], 0, 1), "6", $mCtoDes["{$mIT[$i][1]}"]);//Cod
          $mIngTer[$nInd_mIngTer]['ctodesxx'] = trim("IVA ".$cObs[0]);//Descripcion
          $mIngTer[$nInd_mIngTer]['ctoidxxx'] = '';//CtoId
          $mIngTer[$nInd_mIngTer]['costoxxx'] = 0;//Costo
          $mIngTer[$nInd_mIngTer]['ivaxxxxx'] = $mIT[$i][16]+0;//Iva
          $mIngTer[$nInd_mIngTer]['totalxxx'] = $mIT[$i][16]+0;//Total
        }else{
          $mIngTer[$nInd_mIngTer]['costoxxx'] = $mIT[$i][7];//Costo
          $mIngTer[$nInd_mIngTer]['ivaxxxxx'] = 0;//Iva
          $mIngTer[$nInd_mIngTer]['totalxxx'] = $mIT[$i][7];//Total
        }
      }
    }

    //Incluyendo GMF y Formularios
    for ($i=0;$i<count($mValores);$i++) {
      $nInd_mIngTer = count($mIngTer);
      $mIngTer[$nInd_mIngTer]['ctochald'] = str_replace(substr($mCtoDes["{$mValores[$i]['ctoidxxx']}"], 0, 1), "6", $mCtoDes["{$mValores[$i]['ctoidxxx']}"]);//Cod
      $mIngTer[$nInd_mIngTer]['ctodesxx'] = $mValores[$i]['comobsxx'];//Descripcion
      $mIngTer[$nInd_mIngTer]['costoxxx'] = $mValores[$i]['comvlrxx'];//Costo
      $mIngTer[$nInd_mIngTer]['ivaxxxxx'] = 0;//Iva
      $mIngTer[$nInd_mIngTer]['totalxxx'] = $mValores[$i]['comvlrxx'];//Total
    }

    for ($i=0;$i<count($mDatGmf);$i++) {
      $nInd_mIngTer = count($mIngTer);
      $mIngTer[$nInd_mIngTer]['ctochald'] = "5106";//Cod
      $mIngTer[$nInd_mIngTer]['ctodesxx'] = $mDatGmf[$i]['comobsxx'];//Descripcion
      $mIngTer[$nInd_mIngTer]['costoxxx'] = $mDatGmf[$i]['comvlrxx'];//Costo
      $mIngTer[$nInd_mIngTer]['ivaxxxxx'] = 0;//Iva
      $mIngTer[$nInd_mIngTer]['totalxxx'] = $mDatGmf[$i]['comvlrxx'];//Total
    }

  	$cCscFac = ($vCocDat['regestxx'] == "PROVISIONAL") ?  "XXXXX" : $vCocDat['comcscxx'];
    // Fin de Codigo para imprimir los ingresos para terceros

    ##Traigo la Forma de Pago##
		$vCodFormPago = explode("~", $vCocDat['comobs2x']);

    $cFormaPag = "";
		if ($vCodFormPago[14] == "1") {
			$cFormaPag = "CONTADO";
		} elseif($vCodFormPago[14] == "2") {
			$cFormaPag = "CREDITO";
		}
		##FIN Traigo la Forma de Pago##
		
		$VMedioPago = "";
		##Traigo el Medio de Pago##
		if ($vCodFormPago[15] != "") {
			$qMedPag  = "SELECT mpadesxx ";
			$qMedPag .= "FROM $cAlfa.fpar0155 ";
			$qMedPag .= "WHERE mpaidxxx = \"{$vCodFormPago[15]}\" AND ";
			$qMedPag .= "regestxx = \"ACTIVO\" LIMIT 0,1";
			$xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
			if (mysql_num_rows($xMedPag) > 0) {
				$VMedioPago = mysql_fetch_array($xMedPag);
			}
		}
    ##FIN Traigo el Medio de Pago##

    // echo "<pre>";
    // print_r($mCodDat);
    // die();

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
  			global $gCcoId;  global $gSccId;  global $gMesDes; global $cDocId;  global $cDocSuf; global $vCiuDat;
  			global $cUsrNom; global $vAgeDat; global $vCocDat; global $vConDat; global $cPedido; global $cSucDes;
  			global $vResDat; global $cDocTra; global $cPesBru; global $_COOKIE; global $vPaiDat; global $vDceDat;
        global $nValAdu; global $vCccDat; global $cCscFac; global $cEstiloLetra; global $cEstiloLetraOfton;
        global $vSysStr; global $cDesMer; global $cLimStk; global $cNumVap; global $mCodDat; global $cLugIngDes;
        global $cSucId;  global $VMedioPago; global $cFormaPag;

        if ($vCocDat['regestxx'] == "INACTIVO") {
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,190,190);
        }

        if ($_COOKIE['kModo'] == "VERFACTURA"){
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,190,190);
        }

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
        $this->Cell(37,4,utf8_decode("No. ".$cCscFac),0,0,'C');
        $this->setXY($nPosX+168,$nPosX+22);
        $this->SetFont($cEstiloLetra,'B',5);
  		  $this->MultiCell(28,2,utf8_decode("IVA E ICA REGIMEN COMUN NO SOMOS GRANDES CONTRIBUYENTES\nSOMOS RETENEDORES DE IVA"),0,'C');

        $nPosX = 6;
        $nPosY += 17;

        /*** Ciudad y Fecha***/
        $this->SetFont($cEstiloLetra,'B',8);
        $this->setXY($nPosX,$nPosY+27);
        $this->Cell(26,5,utf8_decode("CIUDAD Y FECHA:"),0,0,'L');
        $this->SetFont($cEstiloLetraOfton,'',8);
        /*** Busco Saldo para colocar fecha de vencimiento ***/
        for ($k=0;$k<count($mCodDat);$k++) {
    			if($mCodDat[$k]['comctocx'] == "SS" || $mCodDat[$k]['comctocx'] == "SC"){
    				if($mCodDat[$k]['comctocx'] == "SC"){
    					$cSaldo = "FAVOR";
    				} else {
    					$cSaldo = "CARGO";
    				}
    			}
    		}
        if($cSaldo == "FAVOR"){
          $cFecVen = date('Y-m-d');
        }else{
          $cFecVen = $vCocDat['comfecve'];
        }

        $cSucursal = $cSucDes;
        if($cSucId == "RCH" || $cSucId == "SMR" || $cSucDes == "TOLU"){
          $cSucursal = "CARTAGENA";
        }elseif($cSucId == "IPI"){
          $cSucursal = "BOGOTA";
        }
        $this->Cell(33,5,$cSucursal.", ".$vCocDat['comfecxx'],0,0,'');
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
        $this->Cell(50,5,$vCocDat['CLITELXX'],0,0,'L');

        /*** SEÑOR(ES) ***/
        $this->setXY($nPosX,$nPosY+37);
        $this->SetFont($cEstiloLetra,'B',8);
        $this->Cell(19,5,utf8_decode("SEÑOR:(ES)"),0,0,'L');
        $this->SetFont($cEstiloLetraOfton,'',7);
        $this->Cell(90,5,utf8_decode(substr($vCocDat['CLINOMXX'], 0, 73)),0,0,'L');

        /*** MEDIO DE PAGO ***/
        $this->setXY($nPosX+100,$nPosY+37);
        $this->SetFont($cEstiloLetra,'B',8);
        $this->Cell(27,5,utf8_decode("MEDIO DE PAGO"),0,0,'L');
        $this->SetFont($cEstiloLetraOfton,'',8);
        $this->Cell(40,5,substr(utf8_decode($VMedioPago['mpadesxx']), 0, 25),0,0,'L');

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
        $this->Cell(90,5,utf8_decode(substr($vCocDat['CLIDIR3X'], 0, 73)),0,0,'L');

        /*** Mercancia ***/
        $this->setXY($nPosX+100,$nPosY+42);
        $this->SetFont($cEstiloLetra,'B',8);
        $this->Cell(27,5,utf8_decode("MERCANCIA:"),0,0,'L');
        $this->SetFont($cEstiloLetraOfton,'',8);
        $this->Cell(56,5,substr($cDesMer, 0, 25),0,0,'L');

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
        $this->Cell(33,5,$vCocDat['terid2xx']."-".f_Digito_Verificacion($vCocDat['terid2xx']),0,0,'L');

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

        $this->setXY($nPosX+10,$nPosY+60);
        $this->Cell(100,5,"CONCEPTO",0,0,'');
        $this->Cell(28,5,"CANTIDAD",0,0,'C');
        $this->Cell(28,5,"BASE/VALOR",0,0,'C');
        $this->Cell(28,5,"TOTAL",0,0,'C');

  		}//Function Header

  		function Footer() {
  		  global $cRoot; global $cPlesk_Skin_Directory; global $cNomCopia; global $vResDat;
        global $nContPage; global $vCocDat; global $mCodDat; global $cSaldo; global $vSysStr;
        global $cEstiloLetra; global $gCorreo; global $nb; global $vUsrNom;

        $nPosY = 216;
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
        $this->setXY($nPosX+138,$nPosY+8);
        $this->SetFont($cEstiloLetra,'B',5);
        $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA BOGOTA, D.C. 5229; ICA TARIFA 9.66X1000"),0,0,'');
        $this->setXY($nPosX+138,$nPosY+10);
        $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA BUENAVENTURA: 5229; ICA TARIFA 10X1000"),0,0,'');
        $this->setXY($nPosX+138,$nPosY+12);
        $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA CARTAGENA: 5229; ICA TARIFA 8X1000"),0,0,'');
        $this->setXY($nPosX+138,$nPosY+14);
        $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA BARRANQUILLA: 5229; ICA TARIFA 10X1000"),0,0,'');
        $this->setXY($nPosX+138,$nPosY+16);
        $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA MEDELLIN: 5229: ICA TARIFA 6X1000"),0,0,'');
        $this->setXY($nPosX+138,$nPosY+18);
        $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA CUCUTA: 5229; ICA TARIFA 7x1000"),0,0,'');
        $this->setXY($nPosX+138,$nPosY+20);
        $this->Cell(63,2,utf8_decode("ACTIVIDAD ECONOMICA CALI: 5229; ICA TARIFA 10x1000"),0,0,'');
        $this->setXY($nPosX+138,$nPosY+22);
        $this->Cell(63,2,utf8_decode("CF0301 V02"),0,0,'');

        $this->setXY($nPosX,$nPosY+28);
        $this->SetTextColor(30, 30, 70);
        $this->SetFillColor(255,255,255);
        $this->SetFont($cEstiloLetra,'B',6);
        $this->MultiCell(200,3.5,utf8_decode("Nota: Apreciado Cliente: favor consignar en las siguientes cuentas corrientes Bancolombia No. 237955408-95, Cód.recaudo 06993, ref:8300109054 o Banco Itaú No. 011362845 a nombre de AGENCIA DE ADUANAS ALADUANA S.A.S"),0,'',true);
        $this->SetTextColor(0,0,0);

        // $this->setXY($nPosX,$nPosY+33);
        // $this->SetFont($cEstiloLetra,'B',6);
        // $this->Cell(206,5,utf8_decode($cNomCopia),0,0,'C');

        $this->SetFont($cEstiloLetra,'',6);
        $dFechaInicial = date_create($vResDat['resfdexx']);
        $dFechaFinal   = date_create($vResDat['resfhaxx']);
        $nDiferencia   = date_diff($dFechaInicial, $dFechaFinal);
        $nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m + (($nDiferencia->d > 0) ? 1 : 0);
        $this->RotatedText($nPosX-1.5,215,utf8_decode("AUTORIZACIÓN DIAN No. {$vResDat['residxxx']} DEL ".$vResDat['resfdexx']." DEL ".$vResDat['resprexx'].$vResDat['resdesxx']." AL ".$vResDat['resprexx'].$vResDat['reshasxx']." AUTORIZACIÓN VIGENCIA {$nMesesVigencia} MESES"),90);
    		$this->RotatedText($nPosX+207.5,260,utf8_decode("Impreso por openTecnologia S.A Nit 830.135.010-5."),90);

        $nPosY = 248;
        $this->setXY($nPosX,$nPosY+3);
        $this->SetFont($cEstiloLetra,'',7);
        $this->Cell(40,4, utf8_decode("FECHA Y HORA DE VALIDACIÓN: ").substr($vCocDat['compcevd'],0,16),0,0,'L');
        $this->Ln(5);
        $this->setX($nPosX);
        $this->SetFont($cEstiloLetra,'B',7);
        $this->Cell(40,4, utf8_decode("REPRESENTACIÓN IMPRESA DE LAFACTURA ELECTRÓNICA"),0,0,'L');
        $this->Ln(4);
        $this->setX($nPosX);
        $this->Cell(40,4, utf8_decode("Firma Electrónica:"),0,0,'L');
        $this->Ln(4);
				$this->setX($nPosX);
				$this->SetFont($cEstiloLetra,'',5);
        $this->MultiCell(125, 2,$vCocDat['compcesv'],0,'L');

        $this->setXY($nPosX+140,$nPosY);
        $this->SetFont($cEstiloLetra,'B',6);
        $this->Cell(40,4, utf8_decode("CUFE:"),0,0,'L');
        $this->Ln(1.5);
				$this->setX($nPosX+148);
				$this->SetFont($cEstiloLetra,'',5);
				$this->MultiCell(50, 2,$vCocDat['compcecu'],0,'L');

        //Codigo QR
				if ($vCocDat['compceqr'] != "") {
					$cFileQR = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/QR_".$_COOKIE['kUsrId']."_".date("YmdHis").".png";
					QRcode::png($vCocDat['compceqr'], $cFileQR, "H", 10, 1);
					$this->Image($cFileQR,$nPosX+150,$nPosY+8,22,22);
        }
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

    $pdf->SetWidths(array(100,28,28,28));
    $pdf->SetAligns(array("L","C","R","R"));

    // $mIngTer = array_merge($mIngTer, $mIngTer);
    // $mIngTer = array_merge($mIngTer, $mIngTer);
    // $mIngTer = array_merge($mIngTer, $mIngTer);

    for($y=1; $y<=1; $y++){
      $pdf->AddPage();
      $cNomCopia = "";

  		##Codigo Para impresion de Copias de Factura ##
  		// switch($y){
    	// 	case 1:
    	// 		$cNomCopia = "ORIGINAL";
    	// 	break;
    	// 	case 2:
      //     $cNomCopia = "COPIA";
      //   break;
    	// 	case 3:
    	// 		$cNomCopia = "COPIA2";
    	// 	break;
  		// }
  		##Codigo Para impresion de Copias de Factura ##

  		/*** Inicializando variables por copia ***/
  		$nTotletra = "";	$cSaldo       = "";
  		$nTotPag   = 0;		$nTotAnt      = 0;
  		$nTotRfte  = 0; 	$nTotIca      = 0;
      $nTotPcc = 0;
      $nTotBasePcc = 0;

  	  $nPosY = $pdf->GetY()+5;
  	  $nPosX = 16;
  	  $nPosFin = 195;
  	  $nb = 1;
  	  $pyy = $nPosY;

  	  /*** Imprimo Detalle de Pagos a Terceros e Ingresos Propios ***/
      /*** Imprimo Pagos a Terceros ***/
  	  if (count($mIngTer) > 0 || $nBandPcc == 1) {//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
        $pdf->setXY($nPosX,$pyy);

        for($i=0;$i<count($mIngTer);$i++){
          $pyy = $pdf->GetY();

          if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
            $pdf->AddPage();
            $nb++;
            $nPosY = $pdf->GetY()+10;
            $nPosX = 16;
            $pyy = $nPosY;
            $pdf->setXY($nPosX,$pyy);
          }

          $nCantidad    = '1';
          $intVlrBase   = ($mIngTer[$i]['costoxxx'] > 0) ? $mIngTer[$i]['costoxxx'] : $mIngTer[$i]['totalxxx'];
          $nTotBasePcc += $intVlrBase;
          $nTotPcc     += $mIngTer[$i]['totalxxx'];

          if ($vCccDat['cccvlcto'] != "") {
            $mVlrCto = f_Explode_Array($vCccDat['cccvlcto'], "|","~");

            for ($nVC=0; $nVC<count($mVlrCto); $nVC++) {
              // Obtiene la cantidad y valor unitario del concepto parametrizado en la condiciones comerciales
              if ($mIngTer[$i]['ctoidxxx'] == $mVlrCto[$nVC][0]) {
                $nCantidad  = $mIngTer[$i]['comvlrxx'] / $mVlrCto[$nVC][2];
                $intVlrBase = $mVlrCto[$nVC][2];
              }
            }
          }

          $pdf->SetFont($cEstiloLetraOfton,'',8);
          $pdf->setX($nPosX);
          $pdf->Row(array(
            $mIngTer[$i]['ctochald']." ".utf8_decode($mIngTer[$i]['ctodesxx']),
            $nCantidad,
            number_format($intVlrBase,2,'.',','),
            number_format($mIngTer[$i]['totalxxx'],2,'.',',')
          ));
  	  	}//for($i=0;$i<count($mIngTer);$i++){

  	  }//if(count($mIngTer) > 0 || $nBandPcc == 1){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
      /*** Fin Imprimo Pagos a Terceros ***/

      /*** Total Total pagos a Terceros ***/
      if(count($mIngTer) > 0 || $nBandPcc == 1){

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
        $pdf->SetAligns(array("L","C","R","R"));
        $pdf->SetFont($cEstiloLetraOfton,'',8);

        $pdf->setX($nPosX);
				$pdf->Row(array("TOTAL PAGOS A TERCEROS",
												"",
                        number_format(($nTotBasePcc),2,'.',','),
                        number_format(($nTotPcc),2,'.',',')));

        $pyy += 6;
      }
      /*** Fin Total Total pagos a Terceros ***/

      /*** Imprimo Ingresos Propios ***/
      $nSubToIP = 0;    // Subtotal pagos propios
      $nSubToIPIva = 0; // Iva 19%
      $nSubtotalIPGra   = 0; // Total Ingresos Gravados (Subtotal pagos propios)
      $nSubTotalIPNoGra = 0; // Total Ingresos No Gravados (Subtotal pagos propios)
      $nTotalIPGra      = 0; // Total Ingresos Gravados
      $nTotalIPNoGra    = 0; // Total Ingresos No Gravados

      if($nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS

        $pdf->setXY($nPosX,$pyy);
        $pdf->SetFont($cEstiloLetraOfton,'',8);
        $pdf->Cell(67,6,utf8_decode("INGRESOS PROPIOS"),0,0,'L');

        $pyy += 6;
        $pdf->setXY($nPosX+5,$pyy);
        $pdf->SetWidths(array(95,28,28,28));
        $pdf->SetAligns(array("L","C","R","R"));
        $pdf->SetFont($cEstiloLetraOfton,'',8);

        /*** hago dos veces el mismo for para separar los ingresos GRAVADOS y luego los NO GRAVADOS ***/
        for($k=0;$k<(count($mCodDat));$k++) {
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

  				if($mCodDat[$k]['comctocx'] == "IP" && $mCodDat[$k]['comvlr01'] != 0 ) {

            $cValor = "";
						foreach ($mCodDat[$k]['itemcanx'] as $cKey => $cValue) {
							if ($cKey == "CONTENEDORES_DE_20") {
								$cValor .= " CONTENEDORES DE 20: (".number_format($cValue,0,'.',',').')';
							} elseif ($cKey == "CONTENEDORES_DE_40") {
								$cValor .= " CONTENEDORES DE 40: (".number_format($cValue,0,'.',',').')';
							} elseif ($cKey == "UNIDADES_DE_CARGA_SUELTA") {
								$cValor .= " UNIDADES DE CARGA SUELTA: (".number_format($cValue,0,'.',',').')';
							}
            }

            $nValorUnitario  = ($mCodDat[$k]['unidadfe'] != "A9" && $mCodDat[$k]['canfexxx'] > 0) ? ($mCodDat[$k]['comvlrxx']/$mCodDat[$k]['canfexxx']) : $mCodDat[$k]['comvlrxx'];
            $cCtoCodigo      = ($vCtoSer["{$mCodDat[$k]['ctoidxxx']}"]['serclapr'] == "001") ? $vCtoSer["{$mCodDat[$k]['ctoidxxx']}"]['cceidxxx'] : ltrim($mCodDat[$k]['ctoidxxx'], "0");
            $nSubtotalIPGra += $nValorUnitario;
            $nTotalIPGra    += $mCodDat[$k]['comvlrxx'];

  					$pdf->setX($nPosX+5);
						$pdf->Row(array(
              trim($cCtoCodigo." ".$mCodDat[$k]['comobsxx'].$cValor),
              number_format($mCodDat[$k]['canfexxx'],0,'.',','),
              number_format($nValorUnitario,2,'.',','),
              number_format(($mCodDat[$k]['comvlrxx']),2,'.',',')
            ));
  				}//if($mCodDat[$k]['comctocx'] == 'IP'){
  			}## for($k=$nPosIP;$k<(count($mCodDat));$k++) { ##

  			for($k=0;$k<(count($mCodDat));$k++) {
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

  				if($mCodDat[$k]['comctocx'] == "IP" && $mCodDat[$k]['comvlr01'] == 0 ) {

  					$cValor = "";
						foreach ($mCodDat[$k]['itemcanx'] as $cKey => $cValue) {
							if ($cKey == "CONTENEDORES_DE_20") {
								$cValor .= " CONTENEDORES DE 20: (".number_format($cValue,0,'.',',').')';
							} elseif ($cKey == "CONTENEDORES_DE_40") {
								$cValor .= " CONTENEDORES DE 40: (".number_format($cValue,0,'.',',').')';
							} elseif ($cKey == "UNIDADES_DE_CARGA_SUELTA") {
								$cValor .= " UNIDADES DE CARGA SUELTA: (".number_format($cValue,0,'.',',').')';
							}
						}

            $nValorUnitario    = ($mCodDat[$k]['unidadfe'] != "A9" && $mCodDat[$k]['canfexxx'] > 0) ? ($mCodDat[$k]['comvlrxx']/$mCodDat[$k]['canfexxx']) : $mCodDat[$k]['comvlrxx'];
            $cCtoCodigo        = ($vCtoSer["{$mCodDat[$k]['ctoidxxx']}"]['serclapr'] == "001") ? $vCtoSer["{$mCodDat[$k]['ctoidxxx']}"]['cceidxxx'] : ltrim($mCodDat[$k]['ctoidxxx'], "0");
            $nSubtotalIPNoGra += $nValorUnitario;
            $nTotalIPNoGra    += $mCodDat[$k]['comvlrxx'];

  					$pdf->setX($nPosX+5);
						$pdf->Row(array(
              trim($cCtoCodigo." ".$mCodDat[$k]['comobsxx'].$cValor),
              number_format($mCodDat[$k]['canfexxx'],0,'.',','),
              number_format($nValorUnitario,2,'.',','),
              number_format(($mCodDat[$k]['comvlrxx']),2,'.',',')
            ));
  				}//if($mCodDat[$k]['comctocx'] == 'IP'){
  			}## for($k=$nPosIP;$k<(count($mCodDat));$k++) { ##

		  }//if($nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
      /*** Fin Imprimo Ingresos Propios ***/
      /*** Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios ***/

      /*** Total Ingresos Propios ***/
      if($nBandIP == 1){

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
        $pdf->SetAligns(array("L","C","R","R"));
        $pdf->SetFont($cEstiloLetraOfton,'',8);

        $pdf->setX($nPosX);
				$pdf->Row(array("TOTAL INGRESOS PROPIOS",
												"",
                        number_format(($nSubtotalIPGra+$nSubtotalIPNoGra),2,'.',','),
                        number_format(($nTotalIPGra+$nTotalIPNoGra),2,'.',',')));

        $pyy += 7;
      }
      /*** Fin Total Ingresos Propios ***/

      //Subtotal Factura
      $nSubTotal = $nTotalIPGra + $nTotalIPNoGra + $nTotPcc;

      /*** Calculo e impresión de Iva y Retenciones. ***/
      $nIva      = 0;
      $nPorIva   = 0;
      $nTotRfte  = 0;
      $nTotARfte = 0;
      $nTotCree  = 0;
      $nTotACree = 0;
      $nTotIva   = 0;
      $nTotIca   = 0;
      $nTotAIca  = 0;

      for ($k=0;$k<count($mCodDat);$k++) {

        ##Busco valor de IVA ##
        if($mCodDat[$k]['comctocx'] == 'IVAIP'){
          $nIva   += $mCodDat[$k]['comvlrxx'];
          $nPorIva = $mCodDat[$k]['compivax'];
        }
        ##Fin Busco Valor de IVA ##

        ##Busco Valor de RET.FTE ##
        if($mCodDat[$k]['comctocx'] == 'RETFTE'){
          $nTotRfte += $mCodDat[$k]['comvlrxx'];
        }
        ##Fin Busco Valor de RET.FTE ##

        ##Busco Valor de AUTO RET.FTE ##
        if($mCodDat[$k]['comctocx'] == 'ARETFTE'){
          $nTotARfte += $mCodDat[$k]['comvlrxx'];
        }
        ##Fin Busco Valor de AUTO RET.FTE ##

        ##Busco Valor de RET.CREE ##
        if($mCodDat[$k]['comctocx'] == 'RETCRE'){
          $nTotCree += $mCodDat[$k]['comvlrxx'];
        }
        ##Fin Busco Valor de RET.CREE ##

        ##Busco Valor de AUTO RET.CREE ##
        if($mCodDat[$k]['comctocx'] == 'ARETCRE'){
          $nTotACree += $mCodDat[$k]['comvlrxx'];
        }
        ##Fin Busco Valor de AUTO RET.CREE ##

        ##Busco Valor de RET.IVA ##
        if($mCodDat[$k]['comctocx'] == 'RETIVA'){
          $nTotIva += $mCodDat[$k]['comvlrxx'];
        }
        ##Fin Busco Valor de RET.IVA ##

        ##Busco Valor de RET.ICA ##
        if($mCodDat[$k]['comctocx'] == 'RETICA'){
          $nTotIca += $mCodDat[$k]['comvlrxx'];
        }
        ##Fin Busco Valor de RET.ICA ##

        ##Busco Valor de AUTO RET.ICA ##
        if($mCodDat[$k]['comctocx'] == 'ARETICA'){
          $nTotAIca += $mCodDat[$k]['comvlrxx'];
        }
        ##Fin Busco Valor de AUTO RET.ICA ##
      }

      ##Busco valor de Anticipo ##
  	  $cNegativo = "";
  	  $nTotAnt   = 0;
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
      // if ($vCocDat['CLINRPXX'] == "SI") {
        // for ($k=0;$k<count($mCodDat);$k++) {
        // 	if($mCodDat[$k]['comctocx'] == 'CD' && strpos($mCodDat[$k]['comobsxx'],'ANTICIPOS') > 0){
        // 		$nTotAnt += $mCodDat[$k]['comvlrxx'];
        // 	}
        // }
      // } else {
        $mComFp = f_Explode_Array($vCocDat['comfpxxx'],"|","~");
        for ($k=0;$k<count($mComFp);$k++) {
          if($mComFp[$k][13] != "" && $mComFp[$k][13] != 0){
            $nTotAnt += $mComFp[$k][13];
          }
        }
      // }
      /*
       * Fin de Recorrido al campo comfpxxx para imprimir valor de anticipo.
       */
      ##Fin Busco valor de Anticipo ##

      for ($k=0;$k<count($mCodDat);$k++) {
        if($mCodDat[$k]['comctocx'] == 'RETFTE'){
          $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx,";
          $qPucDat .= "pucdesxx ";
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
                $mRetFte[$nInd_mRetFte]['basexxxx'] = $mCodDat[$k]['comvlr01'];
                $mRetFte[$nInd_mRetFte]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
                $mRetFte[$nInd_mRetFte]['pucdesxx'] = $xRPD['pucdesxx'];
              }
            }//while($xRPD = mysql_fetch_array($xPucDat)){
          }//if($nFilPuc > 0){
        }//if($mCodDat[$k]['comctocx'] == 'RETFTE'){

        if($mCodDat[$k]['comctocx'] == 'RETICA'){
          $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx,";
          $qPucDat .= "pucdesxx ";
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
                $mRetIca[$nInd_mRetIca]['pucdesxx'] = $xRPD['pucdesxx'];
              }
            }//while($xRPD = mysql_fetch_array($xPucDat)){
          }//if($nFilPuc > 0){
        }//if($mCodDat[$k]['comctocx'] == 'RETICA'){

        if($mCodDat[$k]['comctocx'] == 'RETIVA'){
          $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx,";
          $qPucDat .= "pucdesxx ";
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
                $mRetIva[$nInd_mRetIva]['pucdesxx'] = $xRPD['pucdesxx'];
              }
            }//while($xRPD = mysql_fetch_array($xPucDat)){
          }//if($nFilPuc > 0){
        }//if($mCodDat[$k]['comctocx'] == 'RETIVA'){

        if($mCodDat[$k]['comctocx'] == 'RETCRE'){
          $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx,";
          $qPucDat .= "pucdesxx ";
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
                  $mReteCre[$j]['basexxxx'] += $mCodDat[$k]['comvlr01'];
                }
              }
              if ($nSwitch_Encontre_Porcentaje == 0) {
                $nInd_mReteCre = count($mReteCre);
                $mReteCre[$nInd_mReteCre]['tipretxx'] = "CREE";
                $mReteCre[$nInd_mReteCre]['pucretxx'] = $xRPD['pucretxx'];
                $mReteCre[$nInd_mReteCre]['basexxxx'] = $mCodDat[$k]['comvlr01'];
                $mReteCre[$nInd_mReteCre]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
                $mReteCre[$nInd_mReteCre]['pucdesxx'] = $xRPD['pucdesxx'];
              }
            }//while($xRPD = mysql_fetch_array($xPucDat)){
          }//if($nFilPuc > 0){
        }//if($mCodDat[$k]['comctocx'] == 'RETCRE'){

  			// Auto Retencion de ICA
  			if($mCodDat[$k]['comctocx'] == 'ARETICA'){
          $qPucDat  = "SELECT $cAlfa.fpar0115.pucretxx,";
          $qPucDat .= "pucdesxx ";
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
                  $mAutoRetIca[$j]['basexxxx'] += $mCodDat[$k]['comvlr01'];
                }
              }
              if ($nSwitch_Encontre_Porcentaje == 0) {
                $nInd_mAutoRetIca = count($mAutoRetIca);
                $mAutoRetIca[$nInd_mAutoRetIca]['tipretxx'] = "AICA";
                $mAutoRetIca[$nInd_mAutoRetIca]['pucretxx'] = $xRPD['pucretxx'];
                $mAutoRetIca[$nInd_mAutoRetIca]['basexxxx'] = $mCodDat[$k]['comvlr01'];
                $mAutoRetIca[$nInd_mAutoRetIca]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
                $mAutoRetIca[$nInd_mAutoRetIca]['pucdesxx'] = $xRPD['pucdesxx'];
              }
            }//while($xRPD = mysql_fetch_array($xPucDat)){
          }//if($nFilPuc > 0){
        }//if($mCodDat[$k]['comctocx'] == 'ARETICA'){
      }

      /*** Calculo Valores Totales ***/
      $nTotal        = ($nSubTotal + $nIva) - ($nTotRfte + $nTotIva + $nTotIca);
      $nTotalFactura = $nTotal - abs($nTotAnt);

      $nTotalCargo = 0;
      $nTotalFavor = 0;
      if ($nTotalFactura > 0) {
        $nTotalCargo = $nTotalFactura;
      } else {
        $nTotalFavor = abs($nTotalFactura);
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
      $pdf->Cell(156, 4, "IVA ".number_format($nPorIva, 2, '.', ',')."%", 0, 0, 'L');
      $pdf->Cell(28, 4, number_format($nIva, 2, '.', ','), 0, 0, 'R');
      $pyy += 5;
      /*** Fin Bloque de IVA ***/

      if($gRetenciones == 1) {
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

          /** Imprimo Total Retenciones **/
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
          /** Fin Imprimi Total Retenciones **/
        }
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
                      number_format(abs($nTotAnt),2,'.',',')));

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
          $pdf->Row(array($vCocDat['comobsxx']));
        break;
      }
      $pyy += 6;

      /** Imprimo Saldos Factura y Valor Letras **/
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

      // $cMoneda = $vCocDat['CLINRPXX'] != "SI" ? "COP" : "USD" ;
      $pdf->setXY($nPosX,$pyy);
      $pdf->SetFont($cEstiloLetraOfton,'',8);
  		$pdf->Cell(78,6,"SALDO A CARGO",0,0,'L');
      $pdf->Cell(106,6,number_format($nTotalCargo,2,'.',','),0,0,'R');

			$pdf->setXY($nPosX,$pyy+5);
      $pdf->SetFont($cEstiloLetraOfton,'',8);
  		$pdf->Cell(78,6,"SALDO A FAVOR",0,0,'L');
      $pdf->Cell(106,6,number_format($nTotalFavor,2,'.',','),0,0,'R');

      $pyy += 11;
      ##Imprimo Valor en Letras##
  		$pdf->SetFont($cEstiloLetraOfton,'',8);
    	$nTotletra = f_Cifra_Php(str_replace("-","",abs($nTotalFactura)),'PESO');
      $pdf->setXY($nPosX,$pyy);
      $pdf->MultiCell(100,4,utf8_decode($nTotletra),0,'L');
      ##Fin Imprimo Valor en Letras##
    }//for($y=1; $y<=2; $y++){

   	$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
    $pdf->Output($cFile);

    if (file_exists($cFile)){
      chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
    } else {
      f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
    }
  	echo "<html><script>document.location='$cFile';</script></html>";
  }

?>

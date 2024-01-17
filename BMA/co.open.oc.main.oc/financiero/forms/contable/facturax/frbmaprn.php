<?php
  /**
	 * Imprime Factura de Venta BMA.
	 * --- Descripcion: Permite Imprimir la Factura de Venta.
	 * @author Marcio Vilalta <marcio.vilalta@opentecnologia.com.co>
	 */

	// ini_set('error_reporting', E_ERROR);
	// ini_set("display_errors","1");
	
	include("../../../../libs/php/utility.php");
	include("../../../../libs/php/utiliqdo.php");
	
	//Generacion del codigo QR
	require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/phpqrcode/qrlib.php');

	##Switch para incluir fuente y clase pdf segun base de datos ##
	define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
	require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

  $switch=0;
  $vMemo=explode("|",$prints);

  // Validacion de Comprobante Repetido
  $mPrints = f_Explode_Array($prints,"|","~");
  $cAno    = substr($mPrints[0][4],0,4);

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
			$qCocDat .= "WHERE ";
			$qCocDat .= "$cAlfa.fcoc$cAno.comidxxx = \"{$zMatriz[0]}\" AND ";
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

	if($switch == 0 ){
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

    if($vCocDat['regestxx'] != "INACTIVO" && $_COOKIE['kModo'] != "VERFACTURA"){
  	  ##Codigo para Actualizar Campo de Impresion en la 1001 ##
  	  $mUpdPrn = array(array('NAME'=>'comprnxx','VALUE'=>'IMPRESO'  ,'CHECK'=>'SI'),
  		        				 array('NAME'=>'comidxxx','VALUE'=>$cComId    ,'CHECK'=>'WH'),
  		        				 array('NAME'=>'comcodxx','VALUE'=>$cComCod   ,'CHECK'=>'WH'),
  		        				 array('NAME'=>'comcscxx','VALUE'=>$cComCsc   ,'CHECK'=>'WH'),
  		        				 array('NAME'=>'comcsc2x','VALUE'=>$cComCsc2  ,'CHECK'=>'WH'));

  		if (f_MySql("UPDATE","fcoc$cAno",$mUpdPrn,$xConexion01,$cAlfa)) {
  	  } else {
  			$nSwitch = 1;
  		}
    }
		##Fin Codigo para Actualizar Campo de Impresion en la 1001 ##

		////// CABECERA 1001 /////
		$qCocDat  = "SELECT ";
		$qCocDat .= "$cAlfa.fcoc$cNewYear.*, ";
		$qCocDat .= "IF($cAlfa.fpar0008.sucidxxx != \"\",$cAlfa.fpar0008.sucidxxx,\"SUCURSAL SIN ID\") AS sucidxxx, ";
		$qCocDat .= "IF($cAlfa.fpar0008.sucdesxx != \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS CLINOMXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CLICONTX != \"\",$cAlfa.SIAI0150.CLICONTX,\"SIN RESPONSABLE\") AS CLICONTX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CLIFORPX != \"\",$cAlfa.SIAI0150.CLIFORPX,\"\") AS CLIFORPX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX, ";
		$qCocDat .= "$cAlfa.fpar0151.cccplaxx ";
		$qCocDat .= "FROM $cAlfa.fcoc$cNewYear ";
		$qCocDat .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcoc$cNewYear.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
	  $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cNewYear.terid2xx = $cAlfa.SIAI0150.CLIIDXXX ";
	  $qCocDat .= "LEFT JOIN $cAlfa.fpar0151 ON $cAlfa.fcoc$cNewYear.terid2xx = $cAlfa.fpar0151.cliidxxx ";
		$qCocDat .= "WHERE $cAlfa.fcoc$cNewYear.comidxxx = \"$cComId\" AND ";
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
		$nFilCod  = mysql_num_rows($xCodDat);

		if ($nFilCod > 0) {
			// Cargo la Matriz con los ROWS del Cursor
			$iA=0;
			$mPCC = array(); $mIP = array();
			while ($xRCD = mysql_fetch_array($xCodDat)) {
				$mCodDat[$iA] = $xRCD;
				$iA++;
				if($xRCD['comctocx'] == "PCC") {
					$cObs    = "";
					$cCan    = "";
					$nCan    = 0;
					$cAplCan = "";
					$nComObs_PCC = stripos($xRCD['comobsxx'], "[");
		      if($nComObs_PCC > 0){
		      	$mAuxCan = explode("CANTIDAD:",substr($xRCD['comobsxx'],$nComObs_PCC,strlen($xRCD['comobsxx'])));
		      	$cCan = "";
		      	if(count($mAuxCan) > 1) {
		      		$cCan    = str_replace(array(",","]"," "), "", $mAuxCan[1]);
		      		$nCan    = $cCan;
		      		$cAplCan = "SI";
		      	}
		      	$cObs = substr(substr($xRCD['comobsxx'],0,$nComObs_PCC),0,70);
		      }else{
		     		$cObs = substr($xRCD['comobsxx'],0,70);
		     	}

					$mPCC[$xRCD['ctoidxxx']]['comctocx']  = $xRCD['comctocx'];
					$mPCC[$xRCD['ctoidxxx']]['pucidxxx']  = $xRCD['pucidxxx'];
					$mPCC[$xRCD['ctoidxxx']]['comvlrxx'] += $xRCD['comvlrxx'];
					$mPCC[$xRCD['ctoidxxx']]['ctoidxxx']  = $xRCD['ctoidxxx'];
					$mPCC[$xRCD['ctoidxxx']]['comobsxx']  = ($mPCC[$xRCD['ctoidxxx']]['comobsxx'] == "")?$cObs:$mPCC[$xRCD['ctoidxxx']]['comobsxx'];
					$mPCC[$xRCD['ctoidxxx']]['comcanxx'] += $nCan;
					$mPCC[$xRCD['ctoidxxx']]['comcanap']  = ($mPCC[$xRCD['ctoidxxx']]['comcanap'] == "SI")?$mPCC[$xRCD['ctoidxxx']]['comcanap']:$cAplCan;
				}

				if($xRCD['comctocx'] == "IP") {

					$nSwitch_Encontre_Concepto = 0;

					//Agrupando por Concepto
					//Trayendo descripcion concepto, cantidad y unidad
					$mComObs_IP = f_Explode_Array($xRCD['comobsxx'],"|","~");

					$vDatosIp = array();
					$vDatosIp = f_Cantidad_Ingreso_Propio($xRCD['comobsxx'],'',$xRCD['sucidxxx'],$xRCD['docidxxx'],$xRCD['docsufxx']);

					//Los IP se agrupan por Sevicio
					for($j=0;$j<count($mDatIP);$j++){
						if($mDatIP[$j]['ctoidxxx'] == $xRCD['ctoidxxx'] && $mDatIP[$j]['seridxxx'] == $xRCD['seridxxx']){
							$nSwitch_Encontre_Concepto = 1;

							$mDatIP[$j]['comvlrxx'] += $xRCD['comvlrxx'];
							$mDatIP[$j]['comvlrme'] += $xRCD['comvlrme'];
							
							$mDatIP[$j]['compivax']  = $xRCD['compivax']; // Porcentaje IVA
							$mDatIP[$j]['comvlr01'] += $xRCD['comvlr01']; // Valor Iva
							
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
						$mDatIP[$nInd_mConData] = $xRCD;
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
				$mDatPCC[] = $mValores;
			}
			// Fin de Cargo la Matriz con los ROWS del Cursor
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

    ##Traigo Pais del Cliente ##
    $qPaiDat  = "SELECT * ";
    $qPaiDat .= "FROM $cAlfa.SIAI0052 ";
    $qPaiDat .= "WHERE ";
    $qPaiDat .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
    $qPaiDat .= "$cAlfa.SIAI0052.REGESTXX = \"ACTIVO\" ";
    $xPaiDat  = f_MySql("SELECT","",$qPaiDat,$xConexion01,"");
    $nFilPai  = mysql_num_rows($xPaiDat);
    if ($nFilPai > 0) {
      $vPaiDat = mysql_fetch_array($xPaiDat);
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

	  ##Traigo Datos de Contacto del Facturado a ##
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
	  $cDocId  = "";
	  $cDocSuc = "";
	  $cDocSuf = "";
	  $mDoiId = explode("|",$vCocDat['comfpxxx']);
	  for ($i=0;$i<count($mDoiId);$i++) {
	    if($mDoiId[$i] != ""){
	    	$vDoiId  = explode("~",$mDoiId[$i]);
	      $cDocId  = $vDoiId[2];
	      $cDocSuf = $vDoiId[3];
	      $cSucId = $vDoiId[15];
	      $i = count($mDoiId);
	    }//if($mDoiId[$i] != ""){
	  }//for ($i=0;$i<count($mDoiId);$i++) {
		##Fin Exploto campo Matriz para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##
		
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

		##Recorrido para saber si hay registros PCC e IP y poder controlar la impresion de bloques de Pagos a Terceros e Ingresos Propios ##
		$nBandPcc = 0;  $nBandIP = 0; // Banderas que se ponen en 1 si encontro registros para impresion bloques PCC e IP.
		for ($k=0;$k<count($mCodDat);$k++) {
			if($mCodDat[$k]['comctocx'] == 'PCC'){
				$nBandPcc = 1;
			}//if($mCodDat[$k]['comctocx'] == 'PCC'){
			if($mCodDat[$k]['comctocx'] == 'IP'){
				$nBandIP = 1;
			}//if($mCodDat[$k]['comctocx'] == 'IP'){
		}//for ($k=0;$k<count($mCodDat);$k++) {
		##Fin Recorrido para saber si hay registros PCC e IP y poder controlar la impresion de bloques de Pagos a Terceros e Ingresos Propios ##

  // Codigo para imprimir los ingresos para terceros
  $mIT = f_Explode_Array($vCocDat['commemod'],"|","~");
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
	//f_Mensaje(__FILE__,__LINE__,"$qDatCom~".mysql_num_rows($xDatCom));
	while($xRDC = mysql_fetch_array($xDatCom)){
		$nInd_mComImp = count($vComImp);
		$vComImp[] = $xRDC['ctoidxxx']."~".$xRDC['pucidxxx'];
	}
	##Fin Traigo los Documentos que estan marcados como PAGOIMPUESTOS##
  //echo "<pre>";  print_r($vComImp); echo "</pre>";
  for ($i=0;$i<count($mIT);$i++) {
    if ($mIT[$i][1] != "") {
    	$mComObs_PCC = stripos($mIT[$i][2],"[");
			$nSwitch_Encontre_Concepto = 0;
			if (in_array("{$mIT[$i][1]}~{$mIT[$i][9]}", $vComImp) == false) {
				for ($j=0;$j<count($mIngTer);$j++) {
					if ($mIngTer[$j][1] == $mIT[$i][1]) {
						$nSwitch_Encontre_Concepto = 1;
						$mIngTer[$j][7] += $mIT[$i][7]; // Acumulo el valor de ingreso para tercero.
						$mIngTer[$j][15] += $mIT[$i][15]; // Acumulo base de iva.
						$mIngTer[$j][16] += $mIT[$i][16]; // Acumulo valor del iva.
						$mIngTer[$j][20] += $mIT[$i][20]; // Acumulo el valor de ingreso para tercero en Dolares.
						$mIngTer[$j][100] = ((strlen($mIngTer[$j][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$j][100]."/".$mIT[$i][5] : $mIngTer[$j][100];
						$mIngTer[$j][100] = (substr($mIngTer[$j][100],0,1) == "/") ? substr($mIngTer[$j][100],1,strlen($mIngTer[$j][100])) : $mIngTer[$j][100];
						$j = count($mIngTer); // Me salgo del FOR cuando encuentro el concepto.
					}
				}
			}
			if ($nSwitch_Encontre_Concepto == 0) { // No encontre el ingreso para tercero en la matrix $mIngTer
				$nInd_mIngTer = count($mIngTer);
				$mIngTer[$nInd_mIngTer] = $mIT[$i]; // Ingreso el registro como nuevo.
				$mIngTer[$nInd_mIngTer][100] = ((strlen($mIngTer[$nInd_mIngTer][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$nInd_mIngTer][100]."/".$mIT[$i][5] : $mIngTer[$nInd_mIngTer][100];
				$mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$nInd_mIngTer][100],0,1) == "/") ? substr($mIngTer[$nInd_mIngTer][100],1,strlen($mIngTer[$nInd_mIngTer][100])) : $mIngTer[$nInd_mIngTer][100];
			}
		}
	}
	
	/*** Consulto en que bloque se deben imprimir los anticipos PCC o IP ***/
	$vComObs2 = explode("~", $vCocDat['comobs2x']);
	$cFormaPago = "";
	if ($vComObs2[14] != "") {
		//Buscando descripcion
		$cFormaPago = ($vComObs[14] == 1) ? "CONTADO" : "CREDITO";
	}
	$cMedioPago = "";
	if ($vComObs2[15] != "") {
		//Buscando descripcion
		$qMedPag  = "SELECT ";
		$qMedPag .= "mpaidxxx, ";
		$qMedPag .= "mpadesxx, ";
		$qMedPag .= "regestxx ";
		$qMedPag .= "FROM $cAlfa.fpar0155 ";
		$qMedPag .= "WHERE ";
		$qMedPag .= "mpaidxxx = \"{$vComObs2[15]}\" LIMIT 0,1";
		$xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
		// f_Mensaje(__FILE__,__LINE__,$qMedPag."~ ".mysql_num_rows($xMedPag));
		if (mysql_num_rows($xMedPag) == 0) {
			$vMedPag = mysql_fetch_array($xMedPag);
			$cMedioPago = $vMedPag['mpadesxx'];
		}
	}

  // Fin de Codigo para imprimir los ingresos para terceros

  ##Fin Switch para incluir fuente y clase pdf segun base de datos ##
  class PDF extends FPDF {
		function Header() {
			global $cAlfa;   global $cPlesk_Skin_Directory;
			global $gCcoId;  global $gSccId;  global $gMesDes; global $gAnoDes; global $gMesHas; global $gAnoHas;
			global $cUsrNom; global $cCcoDes; global $cScoDes; global $nPag;    global $vAgeDat; global $vCocDat;
			global $vResDat; global $cDocTra; global $cTasCam; global $cDocTra; global $cBultos; global $cPesBru;
			global $cDocId;  global $vCiuDat; global $vDceDat; global $cOpera;  global $nValAdu;  global $vCccDat;
			global $vConDat; global $cPesNet; global $cCiuOri; global $cCiuDes; global $_COOKIE; global $dReaArr;
			global $vPaiDat; global $cFormaPago; global $cMedioPago; global $vSysStr;

      if ($vCocDat['regestxx'] == "INACTIVO") {
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,180,180);
      }

      if ($_COOKIE['kModo'] == "VERFACTURA"){
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,180,180);
      }
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma.jpg',165,15,40,12);
			$posy	= 5;  /// PRIMERA POSICION DE Y ///
      $posx	= 5;
      ##Impresion Datos Generales Factura ##

      #consecutivo
      $this->setXY($posx,$posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(60,5,"FACTURA ELECTRONICA DE VENTA  ",0,0,'L');
      $this->SetFont('verdana','',8);
      $this->Cell(33,5, $vResDat['resprexx']." No. ".$vCocDat['comcscxx'],1,0,'L');
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
			$this->Cell(93,5,"NIT ".number_format($vAgeDat['CLIIDXXX'],0,",",".")."-".f_Digito_Verificacion($vAgeDat['CLIIDXXX']),0,0,'C');

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
			$nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m + (($nDiferencia->d > 0) ? 1 : 0) ;
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
			$this->Cell(30,5,f_Fecha_Letras($vCocDat['comfecxx']),0,0,'L');

			$posy += 3;
			$this->setXY($posx + 102,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(30,5,utf8_decode("HORA DE EMISION "),0,0,'L');
			$this->setXY($posx + 135,$posy);
			$this->Cell(30,5,$vCocDat['reghcrex'],0,0,'L');

			$this->SetFillColor(109,102,102);
      $this->Rect(5,37,200,5, 'FD');

      $posy	= 45;
      #Cliente ##
      $this->setXY($posx+1,$posy);
      $this->SetFont('verdana','',7);
      $this->Cell(104,4,$vCocDat['CLINOMXX'],0,0,'L');
			#Cliente ##

			##Direccion ##
      $this->Ln(4);
			$this->setX($posx+1);
      $this->MultiCell(104,4,$vCocDat['CLIDIRXX'],0,'L',0);
			##Fin Direccion ##
			
			##Ciudad ##
      $this->setX($posx+1);
      $this->Cell(104,4,$vCiuDat['CIUDESXX']." (".$vPaiDat['PAIDESXX'].")" ,0,0,'L');
			##Fin Ciudad ##
			
			##Telefono ##
      $this->Ln(4);
      $this->setX($posx+1);
      $this->Cell(104,4,"Tel: ".$vCocDat['CLITELXX'],0,0,'L');
			##Fin Telefono ##
			
			##Atten##
      $this->Ln(4);
      $this->setX($posx+1);
      $this->Cell(104,4,"Attn: ".$vConDat['NOMBRE'],0,0,'L');
			##Fin Atten##

			#Nit Cliente ##
      $this->Ln(4);
      $this->setX($posx+1);
      $this->Cell(104,4,"NIT.".number_format($vCocDat['terid2xx'],0,",",".")."-".f_Digito_Verificacion($vCocDat['terid2xx']),0,0,'L');
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
      $this->Ln(4);
      $this->setX($posx+102);
      $this->Cell(25,4,"Fecha Vencimiento: ",0,0,'L');
      $this->Cell(73,4,f_Fecha_Letras($vCocDat['comfecve']),0,0,'L');
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
			$this->setXY($posx ,$posy+9);
			$this->MultiCell(140,3,utf8_decode("FAVOR REALIZAR TRANFERENCIA A LA CUENTA CORRIENTE BANCOLOMBIA No: 679-5559-19 O A LA CUENTA CORRIENTE DAVIVIENDA No. 485-1699979-99 Y ENVIAR COPIA DE LA CONSIGNACIÓN AL CORREO director.administrativo@bma.com.co"),0,'C',0);

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

			$posy += 8;
			$this->setXY($posx,$posy);
			$this->SetFont('verdana','',6);
			$this->Cell(200,4,"FECHA DE VALIDACION ".substr($vCocDat['compcevd'],0,16),0,0,'L');

			$this->setXY($posx+145,$posy);
			if ($vCocDat['compcecu'] != "") {
				$this->SetFont('verdanab','',7);
				$this->Cell(200,4,utf8_decode("CUFE:"),0,0,'L');
				$this->Ln(4);
				$this->setX($posx+145);
				$this->SetFont('verdana','',6);
				$this->MultiCell(55,3,$vCocDat['compcecu'],0,'L',0);
			}

			//Codigo QR
			if ($vCocDat['compceqr'] != "") {
				$cFileQR = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/QR_".$_COOKIE['kUsrId']."_".date("YmdHis").".png";
				QRcode::png($vCocDat['compceqr'], $cFileQR, "H", 10, 1);
				$this->Image($cFileQR,$posx+160,$posy+14,25,25);
			}

			$posy += 10;
			$this->setXY($posx,$posy);
			$this->SetFont('verdanab','',7);
			$this->Cell(200,4,utf8_decode("REPRESENTACIÓN IMPRESA DE LA FACTURA ELECTRÓNICA"),0,0,'L');

			if ($vCocDat['compcesv'] != "") {
				$posy += 4;
				$this->setXY($posx,$posy);
				$this->SetFont('verdanab','',6);
				$this->Cell(200,3,utf8_decode("Firma Electrónica"),0,0,'L');

				$posy += 3;
				$this->setXY($posx,$posy);
				$this->SetFont('verdana','',6);
				$this->MultiCell(145,3,$vCocDat['compcesv'],0,'L',0);
			}

    }//Function Header

		function Footer() {
		  global $cPlesk_Skin_Directory;   global $cNomCopia;
		  global $nb;      global $nContPage;               global $vCocDat;

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

  ## ##
  #Solo se imprime una vez
	for($y=1; $y<=4; $y++){
    $pdf->AddPage();
    ##Codigo Para impresion de Copias de Factura ##
	  switch($y){
      case "1":
			case "2":
			  $cNomCopia = "ORIGINAL";
		  break;
		  case "3":
      case "4":
			  $cNomCopia = "COPIA";
		  break;
	 	}

		$posy = 78;
	  $posx = 10;
	  $posFin = 193;
	  $nb = 1;
		$pyy = $posy;
		$pdf->setXY($posx,$pyy);

	  ##Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##
	  ##Imprimo Pagos a Terceros ##
	  if(count($mIngTer) > 0 || $nBandPcc == 1){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1

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
	  	  $nTotPcc += $mIngTer[$i][7];
				
				$vComObs  = explode("^",$mIngTer[$i][2]);
				$cComObs = $vComObs[0];
				
				$pdf->SetFont('verdana','',7);
				$pdf->setX($posx);
				$pdf->Row(array(trim(substr($mIngTer[$i][1],-3)),
												trim($vComObs[0]), 
												"1", 
												"",
												number_format($mIngTer[$i][7],0,',','.')));
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

	  	## Recorro la matriz de la 1002 para imprimir Registros de PCC ##
			$nSubToPcc = 0;
			for ($i=0;$i<count($mDatPCC);$i++) {
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
        if($mDatPCC[$i]['comctocx'] == 'PCC'){
					$nTotPcc += $mDatPCC[$i]['comvlrxx'];

					$cCan = "";
	  			if ($mDatPCC[$i]['comcanap'] == "SI"){
	  				$cCan = "[CANT: ".number_format($mDatPCC[$i]['comcanxx'],0,'.',',')."]";
	  			}
					
					$pdf->SetFont('verdana','',7);
					$pdf->setX($posx);
					$pdf->Row(array(trim(substr($mDatPCC[$i]['ctoidxxx'],-3)),
													trim(str_replace("CANTIDAD","CANT",$mDatPCC[$i]['comobsxx']).$cCan), 
													$cCan, 
													"",
													number_format($mDatPCC[$i]['comvlrxx'],0,',','.')));
	    	}//if($mCodDat[$i]['comctocx'] == 'PCC'){
	    }//for ($i=0;$i<count($mCodDat);$i++) {
	  	## Fin Recorro la matriz de la 1002 para imprimir Registros de PCC ##
			
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
	  }//if(count($mIngTer) > 0 || $nBandPcc == 1){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
	  ##Fin Imprimo Pagos a Terceros ##

		$pyy = $pdf->GetY();
		if(count($mIngTer) > 0 || $nBandPcc == 1){
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
	  if($nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS

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

	  ##Busco valor de IVA ##
		$nIva = 0;
	  for ($k=0;$k<count($mCodDat);$k++) {
	  	if($mCodDat[$k]['comctocx'] == 'IVAIP'){
	    	$nIva += $mCodDat[$k]['comvlrxx'];
	   	}
	 	}
	  ##Fin Busco Valor de IVA ##

	 	##Busco Valor de RET.IVA ##
		$nTotIva = 0;
	  for ($k=0;$k<count($mCodDat);$k++) {
	  	if($mCodDat[$k]['comctocx'] == 'RETIVA'){
	    	$nTotIva += $mCodDat[$k]['comvlrxx'];
	    }
	  }
	 	##Fin Busco Valor de RET.IVA ##

	  ##Busco Valor de RET.ICA ##
		$nTotIca = 0;
	  for ($k=0;$k<count($mCodDat);$k++) {
	  	if($mCodDat[$k]['comctocx'] == 'RETICA'){
	    	$nTotIca += $mCodDat[$k]['comvlrxx'];
	    }
	  }
	 	##Fin Busco Valor de RET.ICA ##


		$mRetFte = array();
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
              $mRetFte[$nInd_mRetFte]['tipretxx'] = "Retefuente Servicios";
              $mRetFte[$nInd_mRetFte]['pucretxx'] = $xRPD['pucretxx'];
              $mRetFte[$nInd_mRetFte]['pucretxx'] = $xRPD['pucretxx'];
              $mRetFte[$nInd_mRetFte]['basexxxx'] = $mCodDat[$k]['comvlr01'];
              $mRetFte[$nInd_mRetFte]['comvlrxx'] = $mCodDat[$k]['comvlrxx'];
            }
          }//while($xRPD = mysql_fetch_array($xPucDat)){
        }//if($nFilPuc > 0){
      }
    }

	  ##Busco Valor de RET.FTE ##
		$nTotRfte = 0;
	  for ($k=0;$k<count($mCodDat);$k++) {
	  	if($mCodDat[$k]['comctocx'] == 'RETFTE'){
	    	$nTotRfte += $mCodDat[$k]['comvlrxx'];
	    }
	  }
	 	##Fin Busco Valor de RET.FTE ##

	  ##Busco Valor de AUTORET.FTE ##
    $nTotArfte = 0;
    for ($k=0;$k<count($mCodDat);$k++) {
      if($mCodDat[$k]['comctocx'] == 'ARETFTE'){
        $nTotArfte += $mCodDat[$k]['comvlrxx'];
      }
    }
    ##Fin Busco Valor de RET.FTE ##

	  ##Busco valor de Anticipo ##
	  $cNegativo = "";
	  $cNeg = "";
	  $nTotAnt = 0;
		for ($k=0;$k<count($mCodDat);$k++) {
			if($mCodDat[$k]['comctocx'] == 'SS' || $mCodDat[$k]['comctocx'] == 'SC'){
				if($mCodDat[$k]['comctocx'] == 'SC'){
					$cNegativo = "MENOS ";
					$cNeg = "-";
				}
				$nTotAnt += $mCodDat[$k]['comvlr01'];
			}
		}

	   /*
	   * En caso de que el valor a pagar de la Factura sea cero, en detalle no se guarda registro SS o SC,
	   * Razon por la cual no se muestra el valor del anticipo que fue aplicado.
	   * Para imprimir este valor se debe tomar el campo comfpxx de cabecera, posicion 13 donde se guarda el valor del anticipo
	   */
		if($nTotAnt == 0){
			$mComFp = f_Explode_Array($vCocDat['comfpxxx'],"|","~");
			for ($k=0;$k<count($mComFp);$k++) {
				if($mComFp[$k][13] != "" && $mComFp[$k][13] != 0){
					$nTotAnt += abs($mComFp[$k][13]);
				}
			}
		}

	  /* Fin de Recorrido al campo comfpxxx para imprimir valor de anticipo.*/
	 	##Fin Busco valor de Anticipo ##

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
    $nTotPag  = $nSubToFac + $nIva;
		$nSaldo   = (($nSubToFac + $nIva) - $nTotIca - $nTotIva - $nTotRfte + $nTotArfte) - $nTotAnt;
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
	  $pdf->Cell(30,5,number_format($nSubToFac - $nTotIca - $nTotIva - $nTotRfte + $nTotArfte,2,'.',','),0,0,'R');
	  $posy += 4;
		$pdf->SetFont('verdana','',8);
	  $pdf->setXY($posx,$posy);
    $pdf->Cell(22,5,"IVA",0,0,'R');
	  $pdf->Cell(30,5,number_format($nIva,2,'.',','),0,0,'R');
		$posy += 4;
    $pdf->setXY($posx,$posy);
		$pdf->SetFont('verdanab','',8);
    $pdf->Cell(22,5,"TOTAL FACTURA",0,0,'R');
    $pdf->Cell(30,5,number_format(($nSubToFac - $nTotIca - $nTotIva - $nTotRfte + $nTotArfte ) + $nIva,2,'.',','),0,0,'R');
		$posy += 4;
		$pdf->SetFont('verdana','',8);
    $pdf->setXY($posx,$posy);
    $pdf->Cell(22,5,"ANTICIPOS",0,0,'R');
    $pdf->Cell(30,5,number_format($nTotAnt,2,'.',','),0,0,'R');
    $posy += 4;
    $pdf->setXY($posx,$posy);
    $pdf->Cell(22,5,"TOTAL A PAGAR",0,0,'R');
		$pdf->Cell(30,5,number_format($nSaldo,2,'.',','),0,0,'R');
		$posy += 4;
    $pdf->setXY($posx,$posy);
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

  }//for($y=1; $y<=5; $y++){

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

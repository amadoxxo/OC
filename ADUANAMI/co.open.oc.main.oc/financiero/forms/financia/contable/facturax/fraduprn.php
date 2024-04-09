<?php
  /**
	 * Imprime Factura de Venta Aduanamientos [ADUANAMI].
	 * --- Descripcion: Permite Imprimir Factura de Venta.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 */
  include("../../../../libs/php/utility.php");

  $switch=0;
  $vMemo=explode("|",$prints);

  # Validacion de Comprobante Repetido
  $mPrints = f_Explode_Array($prints,"|","~");
  $cAno    = substr($mPrints[0][4],0,4);

  # Busco la resolucion en la tabla GRM00138.
	$qResFac  = "SELECT rescomxx ";
  $qResFac .= "FROM $cAlfa.fpar0138 ";
  $qResFac .= "WHERE ";
  $qResFac .= "rescomxx LIKE \"%{$mPrints[0][0]}~{$mPrints[0][1]}%\" AND ";
  $qResFac .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  $xResFac  = f_MySql("SELECT","",$qResFac,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qResFac." ~ ".mysql_num_rows($xResFac));
  $mResFac = mysql_fetch_array($xResFac);
  # Fin de Busco la resolucion en la tabla GRM00138.

	# Armo la variable con los codigos de los comprobantes de facturacion para la actualizacion del consecutivo despues de grabar la factura.
  $mCodCom = f_Explode_Array($mResFac['rescomxx'],"|","~");
  $cCodigos_Comprobantes = "";
  for ($i=0;$i<count($mCodCom);$i++) {
    $cCodigos_Comprobantes .= "\"";
    $cCodigos_Comprobantes .= "{$mCodCom[$i][1]}";
    $cCodigos_Comprobantes .= "\"";
    if ($i < (count($mCodCom) -1)) { $cCodigos_Comprobantes .= ","; }
  }
  # Fin de Armo la variable con los codigos de los comprobantes de facturacion para la actualizacion del consecutivo despues de grabar la factura.

  $qValCsc  = "SELECT comidxxx,comcodxx,comcscxx,comcsc2x ";
	$qValCsc .= "FROM $cAlfa.fcoc$cAno ";
	$qValCsc .= "WHERE ";
	$qValCsc .= "comidxxx = \"{$mPrints[0][0]}\"  AND ";
	$qValCsc .= "comcodxx IN ($cCodigos_Comprobantes) AND ";
	$qValCsc .= "comcscxx = \"{$mPrints[0][2]}\"";
	$xValCsc  = f_MySql("SELECT","",$qValCsc,$xConexion01,"");
	// f_Mensaje(__FILE__,__LINE__,$qValCsc." ~ ".mysql_num_rows($xValCsc));
	if (mysql_num_rows($xValCsc) > 1) {
		$swich = 1;
		f_Mensaje(__FILE__,__LINE__,"El Documento [{$mPrints[0][0]}-{$mPrints[0][1]}-{$mPrints[0][2]}] No se puede Imprimir porque su Consecutivo se Encuentra Repetido en el Modulo de Contabilidad, Verifique");
	}
  # Fin de Validacion de Comprobante Repetido

  $permisos=0;
  $zCadPer="|";
  $resolucion=0;
  $zCadRes="|";

  $fomularios=0;
  $zCadFor="";

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
    }#if ($vMemo[$u]!=""){
  }#for($u=0; $u<count($vMemo); $u++) {
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

 if($switch == 0){

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
	    // $cAno     = substr($cRegFCre,0,4);
    }
  }

  if($vCocDat['regestxx'] != "INACTIVO" && $_COOKIE['kModo'] != "VERFACTURA") {
    ##Codigo para Actualizar Campo de Impresion en la 1001 ##
    $mUpdPrn = array(array('NAME'=>'comprnxx','VALUE'=>'IMPRESO'  ,'CHECK'=>'SI'),
  	        				 array('NAME'=>'comidxxx','VALUE'=>$cComId    ,'CHECK'=>'WH'),
  	        				 array('NAME'=>'comcodxx','VALUE'=>$cComCod   ,'CHECK'=>'WH'),
  	        				 array('NAME'=>'comcscxx','VALUE'=>$cComCsc   ,'CHECK'=>'WH'),
  	        				 array('NAME'=>'comcsc2x','VALUE'=>$cComCsc2  ,'CHECK'=>'WH'));

    /**
     * Tener en cuenta que para ADUANAMIENTOS no hay restriccion de impresion,
     * pueden imprimir tantas veces quieran.
     */
  	if (f_MySql("UPDATE","fcoc$cAno",$mUpdPrn,$xConexion01,$cAlfa)) {
    } else {
  		$nSwitch = 1;
  	}
  }
	##Fin Codigo para Actualizar Campo de Impresion en la 1001 ##

	# CABECERA 1001 #
	$qCocDat  = "SELECT ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.*, ";

	$qCocDat .= "IF($cAlfa.fpar0008.sucidxxx <> \"\",$cAlfa.fpar0008.sucidxxx,\"SUCURSAL SIN ID\") AS sucidxxx, ";
	$qCocDat .= "IF($cAlfa.fpar0008.sucdesxx <> \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS CLINOMXX, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX <> \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX <> \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.CLICONTX <> \"\",$cAlfa.SIAI0150.CLICONTX,\"SIN RESPONSABLE\") AS CLICONTX, ";
  $qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX <> \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX <> \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX <> \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.CLINRPXX <> \"\",$cAlfa.SIAI0150.CLINRPXX,\"\") AS CLINRPXX, ";
	$qCocDat .= "IF($cAlfa.A.CLINOMXX <> \"\",$cAlfa.A.CLINOMXX,(TRIM(CONCAT($cAlfa.A.CLINOMXX,\" \",$cAlfa.A.CLINOM1X,\" \",$cAlfa.A.CLINOM2X,\" \",$cAlfa.A.CLIAPE1X,\" \",$cAlfa.A.CLIAPE2X)))) AS IMPORTADOR ";
	$qCocDat .= "FROM $cAlfa.fcoc$cNewYear ";
	$qCocDat .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcoc$cNewYear.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
  $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cNewYear.terid2xx = $cAlfa.SIAI0150.CLIIDXXX ";
  $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150  AS A ON $cAlfa.fcoc$cNewYear.teridxxx = $cAlfa.A.CLIIDXXX ";
	$qCocDat .= "WHERE $cAlfa.fcoc$cNewYear.comidxxx = \"$cComId\" AND ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.comcodxx = \"$cComCod\" AND ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.comcscxx = \"$cComCsc\" AND ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.comcsc2x = \"$cComCsc2\" LIMIT 0,1";

	// f_Mensaje(__FILE__,__LINE__,$qCocDat);

	$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
	$nFilCoc  = mysql_num_rows($xCocDat);
	if ($nFilCoc > 0) {
	  $vCocDat  = mysql_fetch_array($xCocDat);
	}

  # Consulta para traer la descripcion del pais y mostrarla en el archivo PDF.
  $qDesCiu  = "SELECT CIUDESXX ";
  $qDesCiu .= "FROM $cAlfa.SIAI0055 ";
  $qDesCiu .= "WHERE ";
  $qDesCiu .= "PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
  $qDesCiu .= "DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
  $qDesCiu .= "CIUIDXXX = \"{$vCocDat['CIUIDXXX']}\"";
  $xDesCiu  = f_MySql("SELECT","",$qDesCiu, $xConexion01,"");
  if (mysql_num_rows($xDesCiu) > 0){
    $vDesCiu = mysql_fetch_array($xDesCiu);
  }
  // f_Mensaje(__FILE__,__LINE__,$qDesCiu."~".mysql_num_rows($xDesCiu)."~".$vDesCiu['CIUDESXX']);
  # Fin de consulta para traer la descripcion del pais y mostrarla en el archivo PDF.

	# DETALLE 1002 #
	$qCodDat  = "SELECT DISTINCT ";
  $qCodDat .= "$cAlfa.fcod$cNewYear.* ";
  $qCodDat .= "FROM $cAlfa.fcod$cNewYear ";
  $qCodDat .= "WHERE $cAlfa.fcod$cNewYear.comidxxx = \"$cComId\" AND ";
	$qCodDat .= "$cAlfa.fcod$cNewYear.comcodxx = \"$cComCod\" AND ";
	$qCodDat .= "$cAlfa.fcod$cNewYear.comcscxx = \"$cComCsc\" AND ";
	$qCodDat .= "$cAlfa.fcod$cNewYear.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cNewYear.comseqxx) ASC ";
	$xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
	$nFilCod  = mysql_num_rows($xCodDat);
	if ($nFilCod > 0) {
		# Cargo la Matriz con los ROWS del Cursor
		$iA=0;
		while ($xRCD = mysql_fetch_array($xCodDat)) {
		#	$mCodDat[$iA] = $xRCD;
			if($xRCD['comctocx'] == 'IP'){
				$nSwitch_Encontre_Concepto = 0;

  				#Armando Observacion
  				$cObs    = "";
  				$cCif    = "";
  				$nCif    = 0;
  				$cAplCif = "NO";

  				$cDim    = "";
  				$nDim    = 0;
  				$cAplDim = "NO";

  				$cHor    = "";
  				$nHor    = 0;
  				$cAplHor = "NO";

  				$cPie    = "";
  				$nPie    = 0;
  				$cAplPie = "NO";

  				$cDav    = "";
  				$nDav    = 0;
  				$cAplDav = "NO";

  				$cVuce    = "";
  				$nVuce    = 0;
  				$cAplVuce = "NO";

          $cCertificados    = "";
  				$nCertificados    = 0;
  				$cAplCertificados = "NO";

  				$cDex    = "";
  				$nDex    = 0;
  				$cAplDex = "NO";

  				$cSerial    = "";
  				$nSerial    = 0;
  				$cAplSerial = "NO";

  				$cArancelaria    = "";
  				$nArancelaria    = 0;
  				$cAplArancelaria = "NO";

  				$cDta      = "";
  				$nDta      = 0;
  				$cAplDta   = "NO";

  				$cItems    = "";
  				$nItems    = 0;
  				$cAplItems = "NO";

  				$cCan      = "";
  				$nCan      = 0;
  				$cAplCan   = "NO";

  				$cFob      = "";
  				$nFob      = 0;
  				$cAplFob   = "NO";

  				$cCon20    = "";
  				$nCon20    = 0;
  				$cAplCon20 = "NO";

  				$cCon40    = "";
  				$nCon40    = 0;
  				$cAplCon40 = "NO";

  				$cCarSue   = "";
  				$nCarSue   = 0;
  				$cAplCarSue= "NO";

  				$cFobAgen = "NO";    // Valor Fob de Agenciamiento para Expo
  				$xRDD     = array(); //Inicializando el cursor de Valor Fob

  				$mComObs_IP = f_Explode_Array($xRCD['comobsxx'],"|","~");
  				if(count($mComObs_IP) > 0){
  					for($nC=0;$nC<count($mComObs_IP);$nC++){
  						switch ($mComObs_IP[$nC][0]) {
                /*
  							case "109":
  								if($mComObs_IP[$nC][2] != ""){
  									$nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
  									if($nComObs_IP > 0){
  										$mAuxArancelaria = explode("CLASIFICACIONES ARANCELARIAS:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$cArancelaria = "";
  										if(count($mAuxArancelaria) > 1) {
  											$cArancelaria = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxArancelaria[1]);
  											$nArancelaria = $cArancelaria;
  											$cAplArancelaria = "SI";
  										}
  										$cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)//.$mAuxPie[0];
  									}else{
  										$cObs = $mComObs_IP[$nC][2];
  									}
  								}#if($mComObs_IP[$i][2] != ""){#
  							break;
  							case "111":
  								if($mComObs_IP[$nC][2] != ""){
  									$nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
  									if($nComObs_IP > 0){
  										$mAuxPie = explode("PIEZAS:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$cPie = "";
  										if(count($mAuxPie) > 1) {
  											$cPie    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxPie[1]);
  											$nPie    = $cPie;
  											$cAplPie = "SI";
  										}
  										$cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)//.$mAuxPie[0];
  									}else{
  										$cObs = $mComObs_IP[$nC][2];
  									}
  								}#if($mComObs_IP[$i][2] != ""){
  							break;
  							case "101":
  							case "103":
  							case "119":
								case "201":
								case "309":
  								if($mComObs_IP[$nC][2] != ""){
  									$nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
  									if($nComObs_IP > 0){
  										$mAuxHor    = explode("HORAS:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$mAuxSerial = explode("CANT SERIALES:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$mAuxItems  = explode("ITEMS:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$cHor = "";
  										$cSerial = "";
  										$cItems = "";
  										if(count($mAuxHor) > 1) {
  											$cHor    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxHor[1]);
  											$nHor    = $cHor;
  											$cAplHor = "SI";
  										}
  										if(count($mAuxSerial) > 1) {
  											$cSerial    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxSerial[1]);
  											$nSerial    = $cSerial;
  											$cAplSerial = "SI";
  										}
  										if(count($mAuxItems) > 1) {
  											$cItems    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxItems[1]);
  											$nItems    = $cItems;
  											$cAplItems = "SI";
  										}
  										$cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)//.$mAuxHor[0];
  									}else{
  										$cObs = $mComObs_IP[$nC][2];
  									}
  								}#if($mComObs_IP[$i][2] != ""){
								break;
								*/
  							case "102":
								// case "110":
  								if($mComObs_IP[$nC][2] != ""){
  									$nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
  									if($nComObs_IP > 0){
  										$mAuxDim = explode("DIM:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$cDim = "";
  										if(count($mAuxDim) > 1) {
  											$cDim    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxDim[1]);
  											$nDim    = $cDim;
  											$cAplDim = "SI";
  										}
  										$cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxDim[0]*/;
  									}else{
  										$cObs = $mComObs_IP[$nC][2];
  									}
  								}##if($mComObs_IP[$i][2] != ""){
  							break;
  							case "103":
  							case "148":
								// case "156":
  								if($mComObs_IP[$nC][2] != ""){
  									$nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
  									if($nComObs_IP > 0){
  										$mAuxDav = explode("DAV:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$mAuxDavMag = explode("DAV MAGNETICAS:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$cDav = "";
  										if(count($mAuxDav) > 1 || count($mAuxDavMag) > 1) {
  											$cDav    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", (count($mAuxDav) > 1) ? $mAuxDav[1]  : $mAuxDavMag[1] );
  											$nDav    = $cDav;
  											$cAplDav = "SI";
  										}
  										$cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxDav[0]*/;
  									}else{
  										$cObs = $mComObs_IP[$nC][2];
  									}
  								}#if($mComObs_IP[$i][2] != ""){
  							break;
                /*
  							case "104":
  							case "504":
  								if($mComObs_IP[$nC][2] != ""){
  									$nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
  									if($nComObs_IP > 0){
  										$mAuxVuce = explode("VUCE:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$cVuce = "";
  										if(count($mAuxVuce) > 1) {
  											$cVuce    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxVuce[1]);
  											$nVuce    = $cVuce;
  											$cAplVuce = "SI";
  										}
  										$cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)//.$mAuxVuce[0];
  									}else{
  										$cObs = $mComObs_IP[$nC][2];
  									}
  								}#if($mComObs_IP[$i][2] != ""){
  							break;
  							case "200":
                  if($mComObs_IP[$nC][2] != ""){
                    $nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
  									if($nComObs_IP > 0){
                      $mAuxFob    = explode("FOB:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$mAuxFob[0] = str_replace(array(",","$","]","MONEDA:COP","MONEDA:USD"), "", $mAuxFob[0]);
  										$cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)//.$mAuxFob[0];
  									}else{
                      $cObs = $mComObs_IP[$nC][2];
  									}
  								}#if($mComObs_IP[$i][2] != ""){
  							break;
                case "203":
  								if($mComObs_IP[$nC][2] != ""){
  									$nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
  									if($nComObs_IP > 0){
                      $mAuxCertificados = explode("ORIGEN:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$cCertificados = "";
  										if(count($mAuxCertificados) > 1) {
                        $cCertificados    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD","MONEDA",":","COP","USD"), "", $mAuxCertificados[1]);
                        $nCertificados    = $cCertificados;
  											$cAplCertificados = "SI";
  										}
  										$cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)//.$mAuxVuce[0];
  									}else{
                      //f_Mensaje(__FILE__,__LINE__,"Entro al else del segundo if case 203");
  										$cObs = $mComObs_IP[$nC][2];
  									}
  								}#if($mComObs_IP[$i][2] != ""){
  							break;
  							case '201':
  							case '204':
  							case "202":
  								if($mComObs_IP[$nC][2] != ""){
  									$nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
  									if($nComObs_IP > 0){
  										$mAuxDex = explode("DEX:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$cDex = "";
  										if(count($mAuxDex) > 1) {
  											$cDex    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxDex[1]);
  											$nDex    = $cDex;
  											$cAplDex = "SI";
  										}
  										$cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)//.$mAuxDav[0];
  									}else{
  										$cObs = $mComObs_IP[$nC][2];
  									}
  								}#if($mComObs_IP[$i][2] != ""){
  							break;
                case "301":
                case "308":
  								if($mComObs_IP[$nC][2] != ""){
  									$nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
  									if($nComObs_IP > 0){
  										$mAuxDta = explode("DTA:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$cDta = "";
  										if(count($mAuxDta) > 1) {
  											$cDta = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxDta[1]);
  											$nDta = $cDta;
  											$cAplDta = "SI";
  										}
  										$cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/.$mAuxPie[0];
  									}else{
  										$cObs = $mComObs_IP[$nC][2];
  									}
  								}#if($mComObs_IP[$i][2] != ""){
  							break;
                case "305":
  								if($mComObs_IP[$nC][2] != ""){
  									$nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
  									if($nComObs_IP > 0){
  										$mAuxCan = explode("Cantidad:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$cCan = "";
  										if(count($mAuxCan) > 1) {
  											$cCan = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCan[1]);
  											$nCan = $cCan;
  											$cAplCan = "SI";
  										}
  										$cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)//.$mAuxPie[0];
  									}else{
  										$cObs = $mComObs_IP[$nC][2];
  									}
  								}#if($mComObs_IP[$i][2] != ""){
  							break;
                case "300":
                case "307":
  								if($mComObs_IP[$nC][2] != ""){
  									$nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
  									if($nComObs_IP > 0){
                      #Valor FOB - Buscando Posicion TRM en la observacion
                      $nPosTrm   = stripos($mComObs_IP[$nC][2], "TRM");
  										$mAuxFob   = explode("FOB:",substr($mComObs_IP[$nC][2],$nComObs_IP,($nPosTrm-$nComObs_IP)));
                      #Contenedores de 20 - Buscando Posicion Contenedores de 40
                      $nPosCon40 = stripos($mComObs_IP[$nC][2], "CONTENEDORES DE 40:");
                      $nPosCon40 = ($nPosCon40 === false) ? strlen($mComObs_IP[$nC][2]) : $nPosCon40 ;
  										$mAuxCon20 = explode("CONTENEDORES DE 20:",substr($mComObs_IP[$nC][2],$nComObs_IP,($nPosCon40-$nComObs_IP)));
                      #Contenedores de 40
  										$mAuxCon40 = explode("CONTENEDORES DE 40:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
                      #Carga Suelta
  										$mAuxCarSue = explode("UNIDADES DE CARGA SUELTA:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$cFob    = "";
                      $cCon20  = "";
                      $cCon40  = "";
                      $cCarSue = "";
  										if(count($mAuxFob) > 1) {
  											$cFob = str_replace(array(".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxFob[1]);
  											$nFob = $cFob;
  											$cAplFob = "SI";
  										}
  										if(count($mAuxCon20) > 1) {
  											$cCon20 = str_replace(array("(",")",".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCon20[1]);
  											$nCon20 = $cCon20;
  											$cAplCon20 = "SI";
  										}
  										if(count($mAuxCon40) > 1) {
  											$cCon40 = str_replace(array("(",")",".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCon40[1]);
  											$nCon40 = $cCon40;
  											$cAplCon40 = "SI";
  										}
  										if(count($mAuxCarSue) > 1) {
  											$cCarSue = str_replace(array("(",")",".","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCarSue[1]);
  											$nCarSue = $cCarSue;
  											$cAplCarSue = "SI";
  										}
  										$cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)//.$mAuxPie[0];
  									}else{
  										$cObs = $mComObs_IP[$nC][2];
  									}
  								}##if($mComObs_IP[$i][2] != ""){
  							break;
  							*/
  							default:
  								if($mComObs_IP[$nC][2] != ""){
  									$nComObs_IP = stripos($mComObs_IP[$nC][2], "[");
  									if($nComObs_IP > 0){
  										$mAuxCif = explode("CIF:",substr($mComObs_IP[$nC][2],$nComObs_IP,strlen($mComObs_IP[$nC][2])));
  										$cCif = "";
  										if(count($mAuxCif) > 1) {
  											$cCif    = str_replace(array(",","$","]"," ","MONEDA:COP","MONEDA:USD"), "", $mAuxCif[1]);
  											$nCif    = $cCif;
  											$cAplCif = "SI";
  										}
  										$cObs = substr($mComObs_IP[$nC][2],0,$nComObs_IP)/*.$mAuxCif[0]*/;
  									}else{
  										$cObs = $mComObs_IP[$nC][2];
  									}
  								} else {
  									$cObs = $mComObs_IP[$nC][1];
  								}##if($mComObs_IP[$nC][2] != ""){
  							break;
  						}

              /*switch ($mComObs_IP[$nC][0]) {
                case "200":
  							case "203":
                  $qDocDat  = "SELECT docfobxx,doctrmxx ";
  								$qDocDat .= "FROM $cAlfa.sys00121 ";
  								$qDocDat .= "WHERE ";
  								$qDocDat .= "docidxxx = \"{$xRCD['docidxxx']}\" AND ";
  								$qDocDat .= "sucidxxx = \"{$xRCD['sucidxxx']}\" AND ";
  								$qDocDat .= "docsufxx = \"{$xRCD['docsufxx']}\" LIMIT 0,1 ";
  								$xDocDat  = f_MySql("SELECT","",$qDocDat,$xConexion01,"");
  								$xRDD = mysql_fetch_array($xDocDat);
                  $cFobAgen = "SI";
                break;
              }*/
  					}##for($nC=0;$nC<count($mComObs_IP);$nC++){
  				}##if(count($mComObs_IP) > 0){

  				##Agrupando por Concepto

  				/*if ($xRCD['comctocx'] == "IP") {
					//	f_Mensaje(__FILE__,__LINE__,count($mCodDat));
    				for($j=0;$j<count($mCodDat);$j++){
    					if($mCodDat[$j]['ctoidxxx'] == $xRCD['ctoidxxx'] && $mCodDat[$j]['seridxxx'] == $xRCD['seridxxx']){
    						$nSwitch_Encontre_Concepto = 1;

    						$mCodDat[$j]['comctocx'] =  $xRCD['comctocx'];
    						$mCodDat[$j]['comvlrxx'] += $xRCD['comvlrxx'];
    						$mCodDat[$j]['comvlr01'] += $xRCD['comvlr01'];
    						$mCodDat[$j]['ctoidxxx']  = $xRCD['ctoidxxx'];
    						$mCodDat[$j]['comobsxx']  = str_replace(array("MONEDA:COP","MONEDA : COP","MONEDA : USD"), "", $cObs);
    						$mCodDat[$j]['comcifxx'] += $nCif;
    						$mCodDat[$j]['comcifap']  = ($mCodDat[$j]['comcifap'] == "SI")?$mCodDat[$j]['comcifap']:$cAplCif;
    						$mCodDat[$j]['comdimxx'] += $nDim;
    						$mCodDat[$j]['comdimap']  = ($mCodDat[$j]['comdimap'] == "SI")?$mCodDat[$j]['comdimap']:$cAplDim;
    						$mCodDat[$j]['comdavxx'] += $nDav;
    						$mCodDat[$j]['comdavap']  = ($mCodDat[$j]['comdavap'] == "SI")?$mCodDat[$j]['comdavap']:$cAplDav;
    						$mCodDat[$j]['comvucxx'] += $nVuce;
    						$mCodDat[$j]['comvucap']  = ($mCodDat[$nC]['comvucap']  == "SI")?$mCodDat[$nC]['comvucap'] :$cAplVuce;
                $mCodDat[$j]['comcerxx'] += $nCertificados;
                $mCodDat[$j]['comcerap']  = ($mCodDat[$nC]['comcerap']  == "SI")?$mCodDat[$nC]['comcerap'] :$cAplCertificados;
    						$mCodDat[$j]['comfobxx']  = $cFobAgen;
    						$mCodDat[$j]['docfobxx'] += $xRDD['docfobxx'];
    						$mCodDat[$j]['comhorxx'] += $nHor;
    						$mCodDat[$j]['comhorap']  = ($mCodDat[$j]['comhorap'] == "SI")?$mCodDat[$j]['comhorap']:$cAplHor;
    						$mCodDat[$j]['compiexx'] += $nPie;
    						$mCodDat[$j]['compieap']  = ($mCodDat[$j]['compieap'] == "SI")?$mCodDat[$j]['compieap']:$cAplPie;
    						$mCodDat[$j]['comdexxx'] += $nDex;
    						$mCodDat[$j]['comdexap']  = ($mCodDat[$j]['comdexap'] == "SI")?$mCodDat[$j]['comdexap']:$cAplDex;
    						$mCodDat[$j]['comserxx'] += $nSerial;
    						$mCodDat[$j]['comserap']  = ($mCodDat[$j]['comserap'] == "SI")?$mCodDat[$j]['comserap']:$cAplSerial;
    						$mCodDat[$j]['comaraxx'] += $nArancelaria;
    						$mCodDat[$j]['comaraap']  = ($mCodDat[$j]['comaraap'] == "SI")?$mCodDat[$j]['comaraap']:$cAplArancelaria;
    						$mCodDat[$j]['comdtaxx'] += $nDta;
    						$mCodDat[$j]['comdtaap']  = ($mCodDat[$j]['comdtaap'] == "SI")?$mCodDat[$j]['comdtaap']:$cAplDta;
    						$mCodDat[$j]['comitexx'] += $nItems;
    						$mCodDat[$j]['comiteap']  = ($mCodDat[$j]['comiteap'] == "SI")?$mCodDat[$j]['comiteap']:$cAplItems;
    						$mCodDat[$j]['comcanxx'] += $nCan;
    						$mCodDat[$j]['comcanap']  = ($mCodDat[$j]['comcanap'] == "SI")?$mCodDat[$j]['comcanap']:$cAplCan;
    						$mCodDat[$j]['comfob2x'] += $nFob;
    						$mCodDat[$j]['comfobap']  = ($mCodDat[$j]['comfobap'] == "SI")?$mCodDat[$j]['comfobap']:$cAplFob;
    						$mCodDat[$j]['comc20xx'] += $nCon20;
    						$mCodDat[$j]['comc20ap']  = ($mCodDat[$j]['comc20ap'] == "SI")?$mCodDat[$j]['comc20ap']:$cAplCon20;
    						$mCodDat[$j]['comc40xx'] += $nCon40;
    						$mCodDat[$j]['comc40ap']  = ($mCodDat[$j]['comc40ap'] == "SI")?$mCodDat[$j]['comc40ap']:$cAplCon40;
    						$mCodDat[$j]['comcsuxx'] += $nCarSue;
    						$mCodDat[$j]['comcsuap']  = ($mCodDat[$j]['comcsuap'] == "SI")?$mCodDat[$j]['comcsuap']:$cAplCarSue;
    					}
    				}
          } */

   				if ($nSwitch_Encontre_Concepto == 0) {
  					$nInd_mConData = count($mCodDat);
   					$mCodDat[$nInd_mConData] = $xRCD;
   					$mCodDat[$nInd_mConData]['comcifxx'] = $nCif;
   					$mCodDat[$nInd_mConData]['comobsxx'] = str_replace(array("MONEDA:COP","MONEDA : COP","MONEDA : USD"), "", $cObs);
						$mCodDat[$nInd_mConData]['comcifap'] = $cAplCif;
   					$mCodDat[$nInd_mConData]['comdimxx'] = $nDim;
   					$mCodDat[$nInd_mConData]['comdimap'] = $cAplDim;
   					$mCodDat[$nInd_mConData]['comdavxx'] = $nDav;
   					$mCodDat[$nInd_mConData]['comdavap'] = $cAplDav;
   					$mCodDat[$nInd_mConData]['comvucxx'] = $nVuce;
   					$mCodDat[$nInd_mConData]['comvucap'] = $cAplVuce;
            $mCodDat[$nInd_mConData]['comcerxx'] = $nCertificados;
            $mCodDat[$nInd_mConData]['comcerap'] = $cAplCertificados;
   					$mCodDat[$nInd_mConData]['comfobxx'] = $cFobAgen;
   					$mCodDat[$nInd_mConData]['docfobxx'] = $xRDD['docfobxx'];
   					$mCodDat[$nInd_mConData]['doctrmxx'] = $xRDD['doctrmxx'];
   					$mCodDat[$nInd_mConData]['comhorxx'] = $nHor;
   					$mCodDat[$nInd_mConData]['comhorap'] = $cAplHor;
   					$mCodDat[$nInd_mConData]['compiexx'] = $nPie;
   					$mCodDat[$nInd_mConData]['compieap'] = $cAplPie;
  					$mCodDat[$nInd_mConData]['comdexxx'] = $nDex;
   					$mCodDat[$nInd_mConData]['comdexap'] = $cAplDex;
  					$mCodDat[$nInd_mConData]['comserxx'] = $nSerial;
   					$mCodDat[$nInd_mConData]['comserap'] = $cAplSerial;
  					$mCodDat[$nInd_mConData]['comaraxx'] = $nArancelaria;
   					$mCodDat[$nInd_mConData]['comaraap'] = $cAplArancelaria;
  					$mCodDat[$nInd_mConData]['comdtaxx'] = $nDta;
   					$mCodDat[$nInd_mConData]['comdtaap'] = $cAplDta;
  					$mCodDat[$nInd_mConData]['comitexx'] = $nItems;
   					$mCodDat[$nInd_mConData]['comiteap'] = $cAplItems;
  					$mCodDat[$nInd_mConData]['comcanxx'] = $nCan;
   					$mCodDat[$nInd_mConData]['comcanap'] = $cAplCan;
  					$mCodDat[$nInd_mConData]['comfob2x'] = $nFob;
   					$mCodDat[$nInd_mConData]['comfobap'] = $cAplFob;
  					$mCodDat[$nInd_mConData]['comc20xx'] = $nCon20;
   					$mCodDat[$nInd_mConData]['comc20ap'] = $cAplCon20;
  					$mCodDat[$nInd_mConData]['comc40xx'] = $nCon40;
   					$mCodDat[$nInd_mConData]['comc40ap'] = $cAplCon40;
  					$mCodDat[$nInd_mConData]['comcsuxx'] = $nCarSue;
   					$mCodDat[$nInd_mConData]['comcsuap'] = $cAplCarSue;
   				}
			}else{
				$mCodDat[$iA] = $xRCD;
			}
			$iA++;
		}
		## Fin de Cargo la Matriz con los ROWS del Cursor
	}

	## Traigo el CLINOMXX o Razon Social de la Agencia de Aduana Dietrich Para encabezado de factura ##
	$qAgeDat  = "SELECT ";
	$qAgeDat .= "$cAlfa.SIAI0150.CLIIDXXX, ";
	$qAgeDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS CLINOMXX, ";
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

  ##Traigo Datos de Contacto del Facturado a ##
  if($vCocDat['CLICONTX'] <> ""){
  	$vContactos = explode("~",$vCocDat['CLICONTX']);
  	//f_Mensaje(__FILE__,__LINE__,count($vContactos));
  	if(count($vContactos) > 1){
  		$vIdContacto = $vContactos[1];
  	}else{
  		$vIdContacto = $vCocDat['CLICONTX'];
  	}

  }##if($vCocDat['CLICONTX'] <> ""){

  $qConDat  = "SELECT ";
  $qConDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))) AS NOMBRE ";
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
    }##if($mDoiId[$i] != ""){
  }##for ($i=0;$i<count($mDoiId);$i++) {
  ##Fin Exploto campo Matriz para traer primer Do y traer Datos de Tasa de Cambio, Documento de Transporte, Bultos, Peso ##

  ##Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##
  $qDceDat  = "SELECT $cAlfa.sys00121.*, ";
  $qDceDat .= "$cAlfa.fpar0008.sucdesxx ";
  $qDceDat .= "FROM $cAlfa.sys00121 ";
  $qDceDat .= "LEFT JOIN  $cAlfa.fpar0008 ON $cAlfa.sys00121.sucidxxx = $cAlfa.fpar0008.sucidxxx ";
  $qDceDat .= "WHERE ";
  $qDceDat .= "$cAlfa.sys00121.sucidxxx = \"$cSucId\" AND ";
  $qDceDat .= "$cAlfa.sys00121.docidxxx = \"$cDocId\" AND ";
  $qDceDat .= "$cAlfa.sys00121.docsufxx = \"$cDocSuf\" ";
 	$xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
  $nFilDce  = mysql_num_rows($xDceDat);
  if ($nFilDce > 0) {
    $vDceDat = mysql_fetch_array($xDceDat);
  }
  //f_Mensaje(__FILE__,__LINE__,$qDceDat);
  ##Fin Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##

  ##Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##
  switch ($vDceDat['doctipxx']){
  	case "IMPORTACION":

  		##Traigo Datos de la SIAI0200 DATOS DEL PRIMER DO ##
  		$qDoiDat  = "SELECT ";
  		$qDoiDat .= "$cAlfa.SIAI0200.DGEDTXXX, ";
  		$qDoiDat .= "$cAlfa.SIAI0200.DGEBULXX, ";
  		$qDoiDat .= "$cAlfa.SIAI0200.TCATASAX, ";
  		$qDoiDat .= "$cAlfa.SIAI0200.DGEPBRXX, ";
  		$qDoiDat .= "$cAlfa.SIAI0200.DOICON20, ";
  		$qDoiDat .= "$cAlfa.SIAI0200.DOICON40, ";
  		$qDoiDat .= "$cAlfa.SIAI0052.PAIDESXX, ";
  		$qDoiDat .= "$cAlfa.SIAI0054.DEPDESXX  ";
  		$qDoiDat .= "FROM $cAlfa.SIAI0200 ";
  		$qDoiDat .= "LEFT JOIN $cAlfa.SIAI0052 ON $cAlfa.SIAI0200.PAIIDXXX = $cAlfa.SIAI0052.PAIIDXXX ";
  		$qDoiDat .= "LEFT JOIN $cAlfa.SIAI0054 ON $cAlfa.SIAI0054.PAIIDXXX = \"CO\" AND ";
  		$qDoiDat .= "$cAlfa.SIAI0200.DEPID2XX = $cAlfa.SIAI0054.DEPIDXXX ";
  		$qDoiDat .= "WHERE ";
  		$qDoiDat .= "$cAlfa.SIAI0200.DOIIDXXX = \"$cDocId\" AND ";
  		$qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"$cDocSuf\" AND ";
  		$qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"$cSucId\" ";
  		//f_Mensaje(__FILE__,__LINE__,$qDoiDat);
  		$xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
  		$nFilDoi  = mysql_num_rows($xDoiDat);
  		if ($nFilDoi > 0) {
  			$vDoiDat  = mysql_fetch_array($xDoiDat);
  		}
  		##Fin Traigo Datos de la SIAI0200 DATOS DEL PRIMER DO ##

  		##Cargo Variables para Impresion de Datos de Do ##
  		$cCarOri  = $vDoiDat['DEPDESXX']; //Carga Origen
  		$cProced  = $vDoiDat['PAIDESXX']; //Pais Procedencia
  		$cTasCam  = $vDoiDat['TCATASAX']; //Tasa de Cambio
  		$cDocTra  = $vDoiDat['DGEDTXXX']; //Documento de Transporte
  		$cBultos  = $vDoiDat['DGEBULXX']; //Bultos
  		$cPesBru  = $vDoiDat['DGEPBRXX']; //Peso Bruto
  		$cCont20  = $vDoiDat['DOICON20']; //Contenedor de 20
  		$cCont40  = $vDoiDat['DOICON40']; //Contenedor de 40
  		##Fin Cargo Variables para Impresion de Datos de Do ##


  		## Calculo Valor Aduana de todos los DO
  		$mDoiId = explode("|",$vCocDat['comfpxxx']);
  		for ($i=0;$i<count($mDoiId);$i++) {
  			if($mDoiId[$i] != ""){
  				$vDoiId  = explode("~",$mDoiId[$i]);

		  		##Traigo Datos de la SIAI0200 DATOS DEL DO ##
		  		$qDoiDat  = "SELECT ";
					$qDoiDat .= "$cAlfa.SIAI0200.TCATASAX "; // Tasa
					$qDoiDat .= "FROM $cAlfa.SIAI0200 ";
					$qDoiDat .= "WHERE ";
					$qDoiDat .= "$cAlfa.SIAI0200.DOIIDXXX = \"{$vDoiId[2]}\" AND ";
					$qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"{$vDoiId[3]}\" AND ";
					$qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$vDoiId[15]}\" ";
					$xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
					$nFilDoi  = mysql_num_rows($xDoiDat);
					if ($nFilDoi > 0) {
						$vDoiDat  = mysql_fetch_array($xDoiDat);
					}
					##Fin Traigo Datos de la SIAI0200 DATOS DEL DO ##

					##Traigo Datos de la SIAI0206 DATOS DEL DO ##
		  		$qDecDat  = "SELECT ";
					$qDecDat .= "SUBID2XX, ";
					$qDecDat .= "ADMIDXXX, ";
					$qDecDat .= "SUM($cAlfa.SIAI0206.LIMBULXX) AS LIMBULXX, ";
					$qDecDat .= "SUM($cAlfa.SIAI0206.LIMNETXX) AS LIMNETXX, ";
					$qDecDat .= "SUM($cAlfa.SIAI0206.LIMCIFXX) AS LIMCIFXX, ";
					$qDecDat .= "SUM($cAlfa.SIAI0206.LIMPBRXX) AS LIMPBRXX, ";
					$qDecDat .= "SUM($cAlfa.SIAI0206.LIMPNEXX) AS LIMPNEXX, ";
					$qDecDat .= "SUM($cAlfa.SIAI0206.LIMVLRXX) AS LIMVLRXX, ";//Fob
					$qDecDat .= "SUM($cAlfa.SIAI0206.LIMGRAXX) AS LIMGRA2X, ";
					$qDecDat .= "SUM($cAlfa.SIAI0206.LIMSUBT2) AS LIMSUBT2, ";
					$qDecDat .= "SUM($cAlfa.SIAI0206.LIMFLEXX) AS LIMFLEXX, ";
					$qDecDat .= "SUM($cAlfa.SIAI0206.LIMSEGXX) AS LIMSEGXX ";
					$qDecDat .= "FROM $cAlfa.SIAI0206 ";
					$qDecDat .= "WHERE $cAlfa.SIAI0206.DOIIDXXX = \"{$vDoiId[2]}\" AND ";
					$qDecDat .= "$cAlfa.SIAI0206.DOISFIDX = \"{$vDoiId[3]}\" AND ";
					$qDecDat .= "$cAlfa.SIAI0206.ADMIDXXX = \"{$vDoiId[15]}\" ";
				  $qDecDat .= "GROUP BY $cAlfa.SIAI0206.DOIIDXXX,$cAlfa.SIAI0206.DOISFIDX,$cAlfa.SIAI0206.ADMIDXXX ";
				  $xDecDat  = f_MySql("SELECT","",$qDecDat,$xConexion01,"");
					$nFilDec  = mysql_num_rows($xDecDat);
					if ($nFilDec > 0) {
						$vDecDat  = mysql_fetch_array($xDecDat);
					}
					##Fin Traigo Datos de la SIAI0206 DATOS DEL DO ##

					$nValCif += number_format($vDecDat['LIMNETXX'] * $vDoiDat['TCATASAX'],0,',',''); // Valor Aduana * Tasa
					//f_Mensaje(__FILE__,__LINE__,$vDecDat['LIMNETXX']." * ".$vDoiDat['TCATASAX']."= ".($vDecDat['LIMNETXX'] * $vDoiDat['TCATASAX']));
				}
			}
  	break;
  	case "EXPORTACION":
  		## Consulto Datos de Do en Exportaciones tabla siae0199 ##
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
			##Cargo Variables para Impresion de Datos de Do ##
			$cCarOri = ""; //Carga Origen
			$cProced = ""; //Pais Procedencia
			$cTasCam = $vDceDat['doctrmxx']; //Tasa de Cambio
			$cDocTra = $vDexDat['dexdtrxx']; //Documento de Transporte
			$cBultos = $vIteDat['itebulxx']; //Bultos
			$cPesBru = $vIteDat['itepbrxx']; //Peso Bruto
			$cCont20 = $vDceDat['docc20xx']; //Contenedor de 20
			$cCont40 = $vDceDat['docc40xx']; //Contenedor de 40
			##Fin Cargo Variables para Impresion de Datos de Do ##

			// Calculo Valor Aduana de los Do en exportaciones
			$mDoiId = explode("|",$vCocDat['comfpxxx']);
			for ($i=0;$i<count($mDoiId);$i++) {
				if($mDoiId[$i] != ""){
					$vDoiId  = explode("~",$mDoiId[$i]);

					$qDatCom  = "SELECT ";
					$qDatCom .= "$cAlfa.sys00121.docfobxx, ";
					$qDatCom .= "$cAlfa.sys00121.doctrmxx ";
					$qDatCom .= "FROM $cAlfa.sys00121 ";
					$qDatCom .= "WHERE ";
					$qDatCom .= "$cAlfa.sys00121.sucidxxx = \"{$vDoiId[15]}\" AND ";
					$qDatCom .= "$cAlfa.sys00121.docidxxx = \"{$vDoiId[2]}\"  AND ";
					$qDatCom .= "$cAlfa.sys00121.docsufxx = \"{$vDoiId[3]}\" ";
					$xDatCom  = f_MySql("SELECT","",$qDatCom,$xConexion01,"");
					$nFilDce  = mysql_num_rows($xDatCom);
					if ($nFilDce > 0) {
						$vDatCom = mysql_fetch_array($xDatCom);
					}
					$nValCif += number_format(($vDatCom['docfobxx'] * $vDatCom['doctrmxx']),0,',',''); // Valor FOB * TRM
					//f_Mensaje(__FILE__,__LINE__,$vDtaDat['dtafobxx']." * ".$vDoiDat['TCATASAX']."= ".($vDtaDat['dtafobxx']*$vDoiDat['TCATASAX']));
				}
			}
  	break;
  	case "TRANSITO":
  		## Traigo Datos de la SIAI0200 ##
			$qDoiDat  = "SELECT * ";
			$qDoiDat .= "FROM $cAlfa.SIAI0200 ";
			$qDoiDat .= "WHERE ";
			$qDoiDat .= "DOIIDXXX = \"$cDocId\"  AND ";
			$qDoiDat .= "DOISFIDX = \"$cDocSuf\" AND ";
			$qDoiDat .= "ADMIDXXX = \"$cSucId\" ";
			$xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
			$nFilDoi  = mysql_num_rows($xDoiDat);
			if ($nFilDoi > 0) {
				$vDoiDat = mysql_fetch_array($xDoiDat);
			}
			## Fin Consulta a la tabla de Do's ##
			//f_Mensaje(__FILE__,__LINE__,$qDoiDat);

			##Cargo Variables para Impresion de Datos de Do ##
			$cCarOri = $vDoiDat['DEPDESXX']; //Carga Origen
			$cProced = $vDoiDat['PAIDESXX']; //Pais Procedencia
			$cTasCam = $vDoiDat['TCATASAX']; //Tasa de Cambio
			$cDocTra = $vDoiDat['DGEDTXXX']; //Documento de Transporte
			$cBultos = $vIteDat['itebulxx']; //Bultos
			$cPesBru = $vIteDat['itepbrxx']; //Peso Bruto
			$cCont20 = ""; //Contenedor de 20
			$cCont40 = ""; //Contenedor de 40
			##Fin Cargo Variables para Impresion de Datos de Do ##

			// Calculo Valor Aduana de los Do en exportaciones
			$mDoiId = explode("|",$vCocDat['comfpxxx']);
			for ($i=0;$i<count($mDoiId);$i++) {
				if($mDoiId[$i] != ""){

					// Datos por DO
					$vDoiId  = explode("~",$mDoiId[$i]);

					## Consulto en la Tabla de Control DTA ##
					$qDtaDat  = "SELECT * ";
					$qDtaDat .= "FROM $cAlfa.dta00200 ";
					$qDtaDat .= "WHERE ";
					$qDtaDat .= "$cAlfa.dta00200.doiidxxx = \"{$vDoiId[2]}\" AND ";
					$qDtaDat .= "$cAlfa.dta00200.admidxxx = \"{$vDoiId[15]}\" ";
					$xDtaDat  = f_MySql("SELECT","",$qDtaDat,$xConexion01,"");
					$nFilDta  = mysql_num_rows($xDtaDat);
					if ($nFilDta > 0) {
						$vDtaDat = mysql_fetch_array($xDtaDat);
					}

					## Traigo Tasa de la SIAI0200 ##
					$qDoiDat  = "SELECT TCATASAX ";
					$qDoiDat .= "FROM $cAlfa.SIAI0200 ";
					$qDoiDat .= "WHERE ";
					$qDoiDat .= "DOIIDXXX = \"{$vDoiId[2]}\" AND ";
					$qDoiDat .= "DOISFIDX = \"{$vDoiId[3]}\" AND ";
					$qDoiDat .= "ADMIDXXX = \"{$vDoiId[15]}\" ";
					$xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
					$nFilDoi  = mysql_num_rows($xDoiDat);
					if ($nFilDoi > 0) {
						$vDoiDat = mysql_fetch_array($xDoiDat);
					}
					## Fin Consulta a la tabla de Do's ##

					$nValCif += number_format(($vDtaDat['dtafobxx']*$vDoiDat['TCATASAX']),0,',','');
					//f_Mensaje(__FILE__,__LINE__,$vDtaDat['dtafobxx']." * ".$vDoiDat['TCATASAX']."= ".($vDtaDat['dtafobxx']*$vDoiDat['TCATASAX']));
				}
			}
  	break;
  	case "OTROS":
  	break;
  }//switch (){
  ##Fin Switch para traer Datos de Do Dependiendo del Tipo de Operacion ##

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

  ##Busco los comprobantes que estan marcados como Reembolso de Caja Menor para luego traer el numero del Vale de Caja Menor con el que inicialmente se hizo el pago##
  $qComRee  = "SELECT ";
  $qComRee .= "$cAlfa.fpar0117.comidxxx, ";
  $qComRee .= "$cAlfa.fpar0117.comcodxx ";
  $qComRee .= "FROM $cAlfa.fpar0117 ";
  $qComRee .= "WHERE ";
  $qComRee .= "$cAlfa.fpar0117.comtipxx = \"RCM\" AND ";
  $qComRee .= "$cAlfa.fpar0117.regestxx = \"ACTIVO\" ";
  $xComRee  = f_MySql("SELECT","",$qComRee,$xConexion01,"");
 	$mComRee  = array();
  while($xRCR = mysql_fetch_array($xComRee)){
  	$mComRee[count($mComRee)] = $xRCR;
  }

  ##fin Busco los comprobantes que estan marcados como Reembolso de Caja Menor para luego traer el numero del Vale de Caja Menor con el que inicialmente se hizo el pago##

  # Codigo para imprimir los ingresos para terceros
  $mIT = f_Explode_Array($vCocDat['commemod'],"|","~");
  $mIngTer = array();
  for ($i=0;$i<count($mIT);$i++) {
    if ($mIT[$i][1] != "") {
      if (substr_count($mIT[$i][2],"") > 0) { # Encontre la palabra DIAN de pago de impuestos.
        $nInd_mIngTer = count($mIngTer);
        $mIngTer[$nInd_mIngTer] = $mIT[$i]; # Ingreso el registro como nuevo.
      } else {
        $nSwitch_Encontre_Concepto = 0;
        for ($j=0;$j<count($mIngTer);$j++) {
          if ($mIngTer[$j][1] == $mIT[$i][1]) {#Agrupar por concepto
          	if ($mIngTer[$j][12] == $mIT[$i][12]) {#Agrupar por Tercero
	            $nSwitch_Encontre_Concepto = 1;
	            $mIngTer[$j][7] += $mIT[$i][7]; # Acumulo el valor de ingreso para tercero.
	            $mIngTer[$j][15] += $mIT[$i][15]; # Acumulo base de iva.
	            $mIngTer[$j][16] += $mIT[$i][16]; # Acumulo valor del iva.
	            $mIngTer[$j][20] += $mIT[$i][20]; # Acumulo el valor de ingreso para tercero en Dolares.
	            ##Si es un comprobante de Reembolsos de Caja Menor debo buscar el numero del consecutivo del vale de caja Menor para mostrarlo en el pago a tercero##
	            $nAnio = date("Y");
	            for($ii=0;$ii<count($mComRee);$ii++){
	            	if($mComRee[$ii]['comidxxx'] == $mIT[$i][3] && $mComRee[$ii]['comcodxx'] == $mIT[$i][4]){
	            		$nAnio = substr($mIT[$i][5],0,4);
	            		$qNumRec  = "SELECT ";
	            		$qNumRec .= "$cAlfa.fcod$nAnio.comidc2x, ";
	            		$qNumRec .= "$cAlfa.fcod$nAnio.comcodc2, ";
	            		$qNumRec .= "$cAlfa.fcod$nAnio.comcscc2 ";
	            		$qNumRec .= "FROM $cAlfa.fcod$nAnio ";
	            		$qNumRec .= "WHERE ";
	            		$qNumRec .= "$cAlfa.fcod$nAnio.comidxxx = \"{$mIT[$i][3]}\" AND ";
	            		$qNumRec .= "$cAlfa.fcod$nAnio.comcodxx = \"{$mIT[$i][4]}\" AND ";
	            		$qNumRec .= "$cAlfa.fcod$nAnio.comcscxx = \"{$mIT[$i][5]}\" AND ";
	            		$qNumRec .= "$cAlfa.fcod$nAnio.comseqxx = \"{$mIT[$i][6]}\" AND ";
	            		$qNumRec .= "$cAlfa.fcod$nAnio.regestxx = \"ACTIVO\"  ";
	            		$xNumRec  = f_MySql("SELECT","",$qNumRec,$xConexion01,"");
	            		$vNumRec = mysql_fetch_array($xNumRec);
	            		$mIT[$i][5] = $vNumRec['comcscc2'];
	            		$ii = count($mComRee);
	            	}
	            }
	            ##Si es un comprobante de Reembolsos de Caja Menor debo buscar el numero del consecutivo del vale de caja Menor para mostrarlo en el pago a tercero##
	            $mIngTer[$j][100] = ((strlen($mIngTer[$j][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$j][100]."/".$mIT[$i][5] : $mIngTer[$j][100];
	            $mIngTer[$j][100] = (substr($mIngTer[$j][100],0,1) == "/") ? substr($mIngTer[$j][100],1,strlen($mIngTer[$j][100])) : $mIngTer[$j][100];
	            $j = count($mIngTer); # Me salgo del FOR cuando encuentro el concepto.
          	}#if ($mIngTer[$j][1] == $mIT[$i][1]) {
          }#if ($mIngTer[$j][1] == $mIT[$i][1]) {#Si el concepto es igual
        }#for ($j=0;$j<count($mIngTer);$j++) {
        if ($nSwitch_Encontre_Concepto == 0) { # No encontre el ingreso para tercero en la matrix $mIngTer
          $nInd_mIngTer = count($mIngTer);
          $mIngTer[$nInd_mIngTer] = $mIT[$i]; # Ingreso el registro como nuevo.
          ##Si es un comprobante de Reembolsos de Caja Menor debo buscar el numero del consecutivo del vale de caja Menor para mostrarlo en el pago a tercero##
	        $nAnio = date("Y");
	        for($mm=0;$mm<count($mComRee);$mm++){
	        	if($mComRee[$mm]['comidxxx'] == $mIT[$i][3] && $mComRee[$mm]['comcodxx'] == $mIT[$i][4]){
	          	$nAnio = substr($mIT[$i][5],0,4);
	            $qNumRec  = "SELECT ";
	            $qNumRec .= "$cAlfa.fcod$nAnio.comidc2x, ";
	            $qNumRec .= "$cAlfa.fcod$nAnio.comcodc2, ";
	           	$qNumRec .= "$cAlfa.fcod$nAnio.comcscc2 ";
	            $qNumRec .= "FROM $cAlfa.fcod$nAnio ";
	            $qNumRec .= "WHERE ";
	            $qNumRec .= "$cAlfa.fcod$nAnio.comidxxx = \"{$mIT[$i][3]}\" AND ";
	            $qNumRec .= "$cAlfa.fcod$nAnio.comcodxx = \"{$mIT[$i][4]}\" AND ";
	            $qNumRec .= "$cAlfa.fcod$nAnio.comcscxx = \"{$mIT[$i][5]}\" AND ";
	            $qNumRec .= "$cAlfa.fcod$nAnio.comseqxx = \"{$mIT[$i][6]}\" AND ";
	            $qNumRec .= "$cAlfa.fcod$nAnio.regestxx = \"ACTIVO\"  ";
	            //f_Mensaje(__FILE__,__LINE__,$qNumRec);
	            $xNumRec  = f_MySql("SELECT","",$qNumRec,$xConexion01,"");
	            $vNumRec = mysql_fetch_array($xNumRec);
	            $mIT[$i][5] = $vNumRec['comcscc2'];
	            $mm = count($mComRee);
	          }#if($mComRee[$ii]['comidxxx'] == $mIT[$i][3] && $mcomRee[$ii]['comcodxx'] == $mIT[$i][4]){
	        }#for($ii=0;$ii<count($mComRee);$ii++){
	        ##Si es un comprobante de Reembolsos de Caja Menor debo buscar el numero del consecutivo del vale de caja Menor para mostrarlo en el pago a tercero##
          $mIngTer[$nInd_mIngTer][100] = ((strlen($mIngTer[$nInd_mIngTer][100]) + strlen($mIT[$i][5]) + 1) < 50) ? $mIngTer[$nInd_mIngTer][100]."/".$mIT[$i][5] : $mIngTer[$nInd_mIngTer][100];
          $mIngTer[$nInd_mIngTer][100] = (substr($mIngTer[$nInd_mIngTer][100],0,1) == "/") ? substr($mIngTer[$nInd_mIngTer][100],1,strlen($mIngTer[$nInd_mIngTer][100])) : $mIngTer[$nInd_mIngTer][100];
        }
      }
    }
  }
  # Fin de Codigo para imprimir los ingresos para terceros

  ## Codigo Para Imprimir Original y numero de Copias ##
  $cRoot = $_SERVER['DOCUMENT_ROOT'];

  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

  class PDF extends FPDF {
		function Header() {
			global $cAlfa;   global $cRoot;   global $cPlesk_Skin_Directory;
			global $gCcoId;  global $gSccId;  global $gMesDes; 	global $gAnoDes; global $gMesHas; global $gAnoHas;
			global $cUsrNom; global $cCcoDes; global $cScoDes; 	global $nPag;    global $vAgeDat; global $vCocDat;
			global $vResDat; global $cDocTra; global $cTasCam; 	global $cDocTra; global $cBultos; global $cPesBru;
			global $cDocId;  global $vConDat; global $vDceDat;	global $cCarOri; global $cProced;	global $nValCif;
			global $cCont20; global $cCont40; global $_COOKIE;  global $vDesCiu;

      if ($vCocDat['regestxx'] == "INACTIVO") {
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/facturaanulada.jpg',10,50,180,180);
      }

      if ($_COOKIE['kModo'] == "VERFACTURA"){
        $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/copianovalida.jpg',10,50,180,180);
      }

			$posy	= 20;  ## PRIMERA POSICION DE Y ##
      $posx	= 10;
      ##Impresion Datos Generales Factura ##
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamientos.jpg',10,7,85,30);
      $this->SetFont('verdanab','',6);
      $this->setXY($posx+30,$posy+6);
      $this->Cell(30,7,"NIT ".$vAgeDat['CLIIDXXX']."-".f_Digito_Verificacion($vAgeDat['CLIIDXXX']),0,0,'C');
      $this->SetFont('verdanab','',10);
      $this->setXY(130,$posy);
      $this->Cell(50,10,"FACTURA DE VENTA No. ".$vCocDat['comcscxx'],0,0,'L');
      $posy += 5;
      $this->SetFont('verdanab','',8);
      $this->setXY(130,$posy);
      $this->Cell(30,10,"FECHA DE EXPEDICION: ",0,0,'L');
      $this->setXY(175,$posy);
      $this->SetFont('verdana','',8);
      $cFechaE = explode("-",f_Fecha_Letras($vCocDat['comfecxx']));
      $this->Cell(30,10,$cFechaE[0]."-".strtoupper(substr($cFechaE[1],0,3))."-".$cFechaE[2],0,0,'L');
      $posy += 5;
      $this->setXY(130,$posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(30,10,"FECHA DE VENCIMIENTO: ",0,0,'L');
      $this->setXY(175,$posy);
      $this->SetFont('verdana','',8);
      $cFechaV = explode("-",f_Fecha_Letras($vCocDat['comfecve']));
      $this->Cell(30,10,$cFechaV[0]."-".strtoupper(substr($cFechaV[1],0,3))."-".$cFechaV[2],0,0,'L');
      $posy = 35;

      $this->SetFont('verdanab','',8);
      $this->setXY($posx,$posy);
      $this->Cell(30,10,utf8_decode("SEÑORES"),0,0,'L');
      $posy += 6;
      $this->SetFont('verdana','',8);
      $this->setXY($posx,$posy);
			$this->Cell(110,6,$vCocDat['CLINOMXX'],0,0,'L');
	    $posy += 4;
	    $this->setXY($posx,$posy);
	    $this->Cell(110,6,"ATN SR(A):  ".($vConDat['CLINOMXX'] == "" ? $vConDat['NOMBRE'] : $vConDat['CLINOMXX']) ,0,0,'L');
	    $posy += 4;
	    $this->setXY($posx,$posy);
	    $this->Cell(110,6,"NIT :  ".$vCocDat['terid2xx']."-".f_Digito_Verificacion($vCocDat['terid2xx']),0,0,'L');
	    $posy += 4;
	    $this->setXY($posx,$posy);
      $this->Cell(110,6,"DIRECCION:",0,0,'L');
      $this->SetFont('verdana','',8);
      $this->setXY(29,$posy);
      $this->Cell(84,6,$vCocDat["CLIDIRXX"],0,0,'L');
      $posy += 4;
      $this->setXY(29,$posy);
      $this->Cell(84,6,$vDesCiu["CIUDESXX"],0,0,'L');
      $this->Rect($posx,37,115,25);

      $posy1 = 35;
      $this->setXY(130,$posy1);
      $this->SetFont('verdana','',5);
      $this->Cell(50,10,"NO DESCUENTE RETENCION EN LA FUENTE POR RENTA",0,0,'L');
      $posy1 += 2;
      $this->setXY(130,$posy1);
      $this->Cell(50,10,"SOMOS AUTORRETENEDORES SEGUN",0,0,'L');
      $posy1 += 2;
      $this->setXY(130,$posy1);
      $this->Cell(50,10,"RESOLUCION No. 5538 DEL 14 DE JUNIO DE 2002",0,0,'L');
      $posy1 += 2;
			$this->setXY(130,$posy1);
      $this->Cell(50,10,"ACTIVIDAD ECONOMICA: 5229",0,0,'L');
      $posy1 += 2;
      $this->setXY(130,$posy1);
      $this->Cell(50,10,"INDUSTRIA Y COMERCIO: ACTIVIDAD 304 TARIFA 9,66  0/00",0,0,'L');
      $posy1 += 2;
      $this->setXY(130,$posy1);
      $this->Cell(50,10,"IVA REGIMEN COMUN",0,0,'L');
      $posy1 += 3;
      $this->setXY(130,$posy1);
      $this->Cell(50,10,"Autorizacion de facturacion por Computador Resolucion DIAN",0,0,'L');
      $posy1 += 2;
      $this->setXY(130,$posy1);
      $cFecha = explode("-",f_Fecha_Letras($vResDat['resfdexx']));
      $this->Cell(50,10,"No {$vResDat['residxxx']} del $cFecha[2] de $cFecha[1] de $cFecha[0] del No. {$vResDat['resdesxx']}",0,0,'L');
      #$this->Cell(50,10,"No {$vResDat['residxxx']} del $cFecha[0] de $cFecha[1] de $cFecha[2] del No. {$vResDat['resdesxx']}",0,0,'L');
      $posy1 += 2;
      $this->setXY(130,$posy1);
      $this->Cell(50,10,"Al {$vResDat['reshasxx']} Impreso por Agencia de Aduanas Aduanamientos Ltda Nivel 1.",0,0,'L');

      $posy += 3;
      $this->SetFont('verdana','',6);
      $this->setXY($posx,$posy);
      #$this->Cell(200,10,"El aceptante se obliga a pagar irrevocablemente a {$vAgeDat['CLINOMXX']} las sumas por los conceptos que a continuacion se indican:",0,0,'C');
      $this->Cell(200,10,"El aceptante se obliga a pagar irrevocablemente a Agencia de Aduanas Aduanamientos Ltda Nivel 1 las sumas por los conceptos que a continuacion se indican:",0,0,'C');
      $posy += 5;
      $this->setXY($posx,$posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(7,10,"REF: ",0,0,'L');
      $this->setXY(18,$posy);
      $this->SetFont('verdana','',8);
      $this->Cell(30,10,$cDocId,0,0,'L');
      $this->setXY(60,$posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(7,10,"CLIENTE: ",0,0,'L');
      $this->setXY(75,$posy);
      $this->SetFont('verdana','',8);
      $this->Cell(30,10,$vCocDat['IMPORTADOR'],0,0,'L');

      ##Fin Valido si el nombre del Importador excede el espacion de impresion para calcular doble renglon e imprimir en letra mas pequeña ##
      #$this->Cell(30,10,$vCocDat['IMPORTADOR'],0,0,'L');
      $this->setXY(150,$posy);
      $this->SetFont('verdanab','',8);
      /*$this->Cell(7,10,"PEDIDO: ",0,0,'L');
      $this->setXY(165,$posy);
      $this->SetFont('verdana','',8);
      $this->Cell(30,10,$vDceDat['docpedxx'],0,0,'L');*/
      $poyy = $posy+5;
      $this->setXY($posx,$poyy);
      $this->SetFont('verdanab','',8);
      $this->Cell(7,7,"PUERTO: ",0,0,'L');
      $this->SetFont('verdana','',8);
      $this->setXY(25,$poyy);
      $this->Cell(30,7,$vDceDat['sucdesxx'],0,0,'L');
      $this->setXY(60,$poyy);
      $this->SetFont('verdanab','',8);
      $this->Cell(7,7,"PEDIDO: ",0,0,'L');
      $this->setXY(75,$poyy);
      $this->SetFont('verdana','',8);
      $this->Cell(30,7,$vDceDat['docpedxx'],0,0,'L');
      $this->setXY(150,$poyy);
      $this->SetFont('verdanab','',8);
      $this->Cell(7,7,"PROCEDENCIA: ",0,0,'L');
      $this->setXY(175,$poyy);
      $this->SetFont('verdana','',8);
      $this->Cell(30,7,$cProced,0,0,'L');
      $poyy += 4;
      $this->setXY($posx,$poyy);
      $this->SetFont('verdanab','',8);
      $this->Cell(7,7,"OPERACION: ",0,0,'L');
      $this->setXY(31,$poyy);
      $this->SetFont('verdana','',8);
     	$this->Cell(30,7,$vDceDat['doctipxx'],0,0,'L');
      $this->setXY(60,$poyy);
      $this->SetFont('verdanab','',8);
      $this->Cell(7,7,"DOC. TRANSPORTE: ",0,0,'L');
      $this->setXY(93,$poyy);
      $this->SetFont('verdana','',8);
      $this->Cell(30,7,$cDocTra,0,0,'L');
      $this->SetFont('verdanab','',8);
      $this->setXY(150,$poyy);
      $this->Cell(7,7,"VALOR ADUANA: ",0,0,'L');
      $this->setXY(178,$poyy);
      $this->SetFont('verdana','',8);
      if($nValCif <> 0){
      	$this->Cell(30,7,number_format($nValCif,0,',','.'),0,0,'L');
      }else{
      	$this->Cell(30,7,"",0,0,'L');
      }
      $poyy += 4;

			$this->setXY($posx,$poyy);
      $this->SetFont('verdanab','',8);
      $this->setXY($posx,$poyy);
      $this->SetFont('verdanab','',8);
      if($cBultos <> 0){
      	$this->Cell(30,7,"CON ".number_format($cBultos,0,',','.')." PIEZAS",0,0,'L');
      }else{
      	$this->Cell(30,7,"CON  PIEZAS",0,0,'L');
      }
      $this->setXY(60,$poyy);
      $this->SetFont('verdanab','',8);
			if(($cCont20 == "" || $cCont20 == 0) && ($cCont40 == "" || $cCont40 == 0)){
      	$this->Cell(7,7,"CARGA SUELTA: ",0,0,'L');
      }else{
      	$this->Cell(7,7,"CONTENEDOR: ",0,0,'L');
      }
      $this->setXY(85,$poyy);
      $this->SetFont('verdana','',8);

      if($cCont20 <> "" && $cCont20 <> 0){
      	$Contenedores20 = $cCont20."/20"."'";
      }
			if($cCont40 <> "" && $cCont40 <> 0){
      	$Contenedores40 = $cCont40."/40"."'";
      }
      if($Contenedores20 > 0 && $Contenedores40 > 0){
      	$this->Cell(30,7,$Contenedores20." y ".$Contenedores40,0,0,'L');
      }elseif($Contenedores20 > 0 && ($Contenedores40 == "" || $Contenedores40 == 0)){
      	$this->Cell(30,7,$Contenedores20,0,0,'L');
      }elseif($Contenedores40 > 0 && ($Contenedores20 == "" || $Contenedores20 == 0)){
      	$this->Cell(30,7,$Contenedores40,0,0,'L');
      }

      $poyy += 4;
      if(strlen($vCocDat['comobsxx']) > 0 ){
      	$this->setXY($posx,$poyy);
	      $this->SetFont('verdanab','',8);
	      $this->Cell(10,7,"OBSERVACIONES: ",0,0,'L');
	      $alinea = explode("~",f_Words($vCocDat['comobsxx'],158));
	    	for ($n=0;$n<count($alinea);$n++) {
	    		$this->setXY($posx+30,$poyy);
	      	$this->SetFont('verdana','',8);
	      	$this->Cell(110,7,$alinea[$n],0,0,'L');
	      	$poyy+=3;
				}#for ($n=0;$n<count($alinea);$n++) {
      }#if(strlen($vCocDat['comobsxx']) > 0 ){

      $this->Rect($posx,$posy+3,200,23);
      //$this->Rect($posx,$posy+2,200,18);
      $this->Rect($posx,87,166,128);
      $this->Rect(176,87,34,123);
      $posy = 86;
      $this->setXY($posx,$posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(166,7,"DESCRIPCION",0,0,'C');
		}#Function Header


		function Footer() {
		  global $cRoot;   global $cPlesk_Skin_Directory;   global $cNomCopia;   global $nCopia;    global $nb;    global $nContPage;
		  global $vAgeDat;
		  $posy = 175;
			$posx = 10;
			$py = $posy;
			$this->SetFont('verdana','',7);
			$this->setXY($posx,$posy);
			$this->MultiCell(150,3,utf8_decode("Efectué sus pagos en: BANCO DE BOGOTA en la cuenta corriente 043046523 o BANCO BANCOLOMBIA en la cuenta corriente número 211-000009-99 o GNB SUDAMERIS cuenta corriente 01010164. Favor enviar su consignación o registro de transferencia a las siguientes direcciones electrónicas: m.baquero@aduanamientos.com; servicioalcliente@aduanamientos.com; cartera@aduanamientos.com."),0);

      $posy = 190;
			$this->setXY($posx,$posy);
		  //$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/firma_aduanamientos.jpg',17,$posy-5,35,15);
		  $this->SetFont('verdanab','',7);
      $this->setXY(10,$posy);
      $this->Cell(40,10,"__________________________",0,0,'L');
      $this->setXY(10,$posy+3);
      $this->Cell(40,10,"FIRMA DEL RESPONSABLE",0,0,'C');
		  $posy = 198;
		  $posx = 10;
		  $this->SetFont('verdanab','',7);
		  $this->setXY($posx,$posy);
		  $this->Cell(35,10,"AUTORRETEFUENTE",0,0,'C');
		  $this->setXY(40,$posy);
      $this->Cell(40,10,"AUTORENTA 0.8%",0,0,'C');
		  $this->setXY(70,$posy);
		  $this->Cell(40,10,"RETENCION ICA",0,0,'C');
		  $this->setXY(100,$posy);
		  $this->Cell(40,10,"RETENCION IVA",0,0,'C');
      $this->setXY(135,$posy);
      $posy1 = $posy;
      $this->Cell(31,10,"INGRESOS POR TERCEROS",0,0,'L');
      $this->setXY(172,$posy1);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy1+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy1);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy1+2,34,5);
      $posy1 += 5;
      $this->setXY(135,$posy1);
      $this->Cell(31,10,"INGRESOS PROPIOS",0,0,'L');
      $this->setXY(172,$posy1);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy1+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy1);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy1+2,34,5);
		  $posy += 10;
      $py = $posy;
      $this->SetFont('verdanab','',5.5);
      $this->setXY($posx,$posy+4);
      $this->Cell(135,10,"ESTE   DOCUMENTO   POR    DISPOSICION   DE   LA    LEY    1231",0,0,'L');
      $py += 6;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"DE  17  DE  JULIO  DE  2008  CONSTITUYE   UN  TITULO   VALOR.",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"NO   SE   ACEPTAN   RECLAMOS  O    DEVOLUCIONES     DESPUES",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"DE   10   DIAS   CALENDARIO   CONTADOS  A   PARTIR    DE    LA",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"FECHA  DE RECEPCION  DE  LA PRESENTE  FACTURA DE   VENTA.",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"EL   ACEPTANTE    HACE     CONSTAR    QUE   RECIBIO   REAL    Y ",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"MATERIALMENTE LOS SERVICIOS A SU  ENTERA  SATISFACCION",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"Y QUE ACEPTA LA PRESENTE FACTURA DE  VENTA.",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"LA   CANCELACION   DE   ESTA   FACTURA  DE  VENTA    DESPUES",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"DE SU FECHA DE VENCIMIENTO CAUSARA INTERESES  DE  MORA",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"A  LA TASA  MAXIMA  AUTORIZADA POR LA SUPERINTENDENCIA",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"FINANCIERA   A   LA    FECHA    DE    PAGO   DE   LA  MISMA;   SE",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"LIQUIDARAN  INTERESES   DESDE   EL  DIA   DE    VENCIMIENTO",0,0,'L');
      $py += 2;
      $this->setXY($posx,$py);
      $this->Cell(135,10,"HASTA LA FECHA DE PAGO",0,0,'L');
      $py += 20;
      $this->Rect($posx,$posy+7,72,30);
      $this->Rect(82,$posy+7,53,30);
      $this->SetFont('verdanab','',8);
      $this->setXY(82,$posy+4);
      $this->Cell(50,10,"RECIBIDA",0,0,'C');
      $pyy1 = $posy+11;
      $this->SetFont('verdanab','',7);
      $this->setXY(81,$pyy1);
      $this->Cell(50,10,"______________________________",0,0,'L');
      $pyy1 += 3;
      $this->setXY(81,$pyy1);
      $this->Cell(50,10,"FIRMA DE ACEPTACION",0,0,'L');
      $pyy1 += 4;
      $this->SetFont('verdanab','',5);
      $this->setXY(81,$pyy1);
      $this->Cell(50,10," Nombre___________________________________",0,0,'L');
      $pyy1 += 4;
      $this->SetFont('verdanab','',5);
      $this->setXY(81,$pyy1);
      $this->Cell(50,10," Doc. Identificacion__________________________",0,0,'L');
      $pyy1 += 4;
      $this->SetFont('verdanab','',5);
      $this->setXY(81,$pyy1);
      $this->Cell(50,10," Fecha Recibo Factura________________________",0,0,'L');
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"VALOR DE LA OPERACION",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $posy += 5;
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"IVA",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $posy += 5;
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"RETENCIONES",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $posy += 5;
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"TOTAL",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $posy += 5;
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"ANTICIPOS RECIBIDOS",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $posy += 5;
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"TOTAL A PAGAR",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $posy += 5;
      $this->SetFont('verdanab','',7);
      $this->setXY(135,$posy);
      $this->Cell(31,10,"SALDO A FAVOR",0,0,'L');
      $this->setXY(172,$posy);
      $this->Cell(2,10," $",0,0,'L');
      $this->Rect(135,$posy+2,41,5);
      $this->SetFont('verdanab','',7);
      $this->setXY(176,$posy);
      $this->Cell(30,10,"",0,0,'R');
      $this->Rect(176,$posy+2,34,5);
      $this->setXY(175,259);
      if($nCopia == 0){
  	  	$this->Cell(30,3,"ORIGINAL",0,0,'R');
      }else {
      	$this->Cell(30,3,"COPIA ".$nCopia,0,0,'R');
      }
      $posy = 245;
      $this->setXY($posx,$posy);
      $this->SetFont('verdanab','',5);
      $cPiePag  = "LAS MERCANCIAS SE TRANSPORTAN POR CUENTA Y ";
      $cPiePag .= "RIESGO DE NUESTROS CLIENTES Y NO ASEGURAMOS LAS MISMAS DE NO MEDIAR ";
      $cPiePag .= "ORDEN EXPRESA -POR ESCRITO- POR PARTE DE UN FUNCIONARIO ";
      $this->Cell(200,3,$cPiePag,0,0,'L');
      $cPiePag  = "AUTORIZADO POR PARTE DEL CLIENTE PARA HACERLO LA RESPONSABILIDAD ";
      $cPiePag .= "EN LOS SERVICIOS CONEXOS EN LA OPERACION DEPENDEN IGUALMENTE DE LA DECISION ";
      $cPiePag .= "DEL CLIENTE PARA ASEGURAR";
      $posy += 2;
      $this->setXY($posx,$posy);
      $this->Cell(200,3,$cPiePag,0,0,'L');
      $cPiePag = "LA MERCANCIA";
      $posy += 2;
      $this->setXY($posx,$posy);
      $this->Cell(200,3,$cPiePag,0,0,'L');
      $posy += 3;
      $this->SetFont('verdanab','',8);
      $this->setXY($posx,$posy);
      $this->Cell(200,3,"AVENIDA CALLE 24 N 95 12 PORTOS PARQUE INDUSTRIAL PBX ".$vAgeDat['CLITELXX']." FAX ".$vAgeDat['CLIFAXXX'],0,0,'C');
      $posy += 3;
      $this->SetFont('verdanab','',9);
      $this->setXY($posx,$posy);
      $this->Cell(200,3,"Oficinas en: BARRANQUILLA - CARTAGENA - SANTA MARTA - BUENAVENTURA - CUCUTA - IPIALES Y RIOHACHA",0,0,'C');
      $this->SetFont('verdanab','',7);
      $this->setXY(20,259);
      $this->Cell(40,3,"PAGINA: ".$nb." DE ".$nContPage,0,0,'R');

		}
  }//class PDF extends FPDF {

  $pdf = new PDF('P','mm','Letter');  //Error al invocar la clase
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->SetFont('verdana','',8);
  $pdf->AliasNbPages();

  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);



  global $xConexion01; global $cAlfa;
  ## ##
  //$pdf->AddPage();
  $posy = 90;
  $posx = 10;
  $posFin = 170;
  $nContPage = 1;
  //$posy +=5;
  $pyy = $posy;
  ##Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##
  $pdf->SetFont('verdanab','',9);
  $pdf->setXY($posx,$posy);
  ##Imprimo Pagos a Terceros ##
  if(count($mIngTer) > 0 || $nBandPcc == 1){#Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
  	$pyy += 3;
  	for($i=0;$i<count($mIngTer);$i++){
  	 if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
  	   $nContPage++;
  	   $posy = 90;
       $posx = 10;
       $pyy = $posy;
  	 }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
  		$pyy +=3;
  	}#for($i=0;$i<count($mIngTer);$i++){
  	if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
  	   $nContPage++;
  	   $posy = 90;
       $posx = 10;
       $pyy = $posy;
  	 }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
  	## Recorro la matriz de la 1002 para imprimir Registros de PCC ##
  	$nSubToPcc = 0;
  	for ($i=0;$i<count($mCodDat);$i++) {
  	 if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
  	   $nContPage++;
  	   $posy = 90;
       $posx = 10;
       $pyy = $posy;
  	 }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
    	if($mCodDat[$i]['comctocx'] == 'PCC'){
  			$pyy +=3;
    	}#if($mCodDat[$i]['comctocx'] == 'PCC'){
    }#for ($i=0;$i<count($mCodDat);$i++) {
  	## Fin Recorro la matriz de la 1002 para imprimir Registros de PCC ##
  	if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
  	  $nContPage++;
  	  $posy = 90;
      $posx = 10;
      $pyy = $posy;
    }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion


  }#if(count($mIngTer) > 0 || $nBandPcc == 1){#Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
  ##Fin Imprimo Pagos a Terceros ##

  if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
    $nContPage++;
  	$posy = 90;
    $posx = 10;
    $pyy = $posy;
  }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion

  $pyy += 5;
  if($nBandIP == 1){#Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
    if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
  	  $nContPage++;
  	  $posy = 90;
      $posx = 10;
      $pyy = $posy;
  	}#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
  	$pdf->setXY($posx,$pyy);
	  ##Imprimo Ingresos Propios##
	  $pyy += 3;
	  for ($k=0;$k<count($mCodDat);$k++) {
	    if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
  	     $nContPage++;
  	     $posy = 90;
         $posx = 10;
         $pyy = $posy;
  	  }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
	    if($mCodDat[$k]['comctocx'] == 'IP'){
  			$pyy +=3;
	    }#if($mCodDat[$k]['comctocx'] == 'IP'){
	  }#for ($k=0;$k<count($mCodDat);$k++) {
	  ##Fin Imprimo Ingresos Propios##
	  if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
  	  $nContPage++;
  	  $posy = 90;
      $posx = 10;
      $pyy = $posy;
    }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
  }#if($nBandIP == 1){#Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS

  ## ##




	for($y=1; $y<=4; $y++){
    $pdf->AddPage();
    if($y==1){
      $pdf->SetFont('verdana','',7);
  	  $cNomCopia = "ORIGINAL:";

    } else {
    	  switch($y){
    		  case "2":
    			  //$cNomCopia = "CLIENTE";
    		  break;
    		  case "3":
    			 // $cNomCopia = "CONTABILIDAD";
    		  break;
    		  case "4":
    			  //$cNomCopia = "CARTERA";
    		  break;
    	 }
    	 $nCopia = $y-1;
    }

  //$pdf->AddPage();
  if($vCocDat['CLINRPXX'] != "SI"){
	  $posy = 90;
	  $posx = 10;
	  $posFin = 170;
	  $nb = 1;
	  //$posy +=5;
	  $pyy = $posy;
	  ##Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##
	  $pdf->SetFont('verdanab','',9);
	  $pdf->setXY($posx,$posy);
	  ##Imprimo Pagos a Terceros ##
	  if(count($mIngTer) > 0 || $nBandPcc == 1){#Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
	  	$nSubTotPcc = 0;
	  	$pdf->Cell(135,10,"INGRESOS PARA TERCEROS",0,0,'L');
	  	$pyy += 3;
	  	for($i=0;$i<count($mIngTer);$i++){
	  	 if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	   $pdf->AddPage();
	  	   $nb++;
	  	   $posy = 90;
	       $posx = 10;
	       $pyy = $posy;
	       $pdf->SetFont('verdana','',9);
	       $pdf->setXY($posx,$posy);
	  	 }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
	  		$nSubTotPcc += $mIngTer[$i][7];
	  		$pdf->SetFont('verdana','',8);
	  		$pdf->setXY(176,$pyy);
	  		$pdf->Cell(34,10,number_format($mIngTer[$i][7],0,',','.'),0,0,'R');
	  		$pdf->setXY($posx,$pyy);
	  		$cComObs  = explode("^",$mIngTer[$i][2]);
	  		if($mIngTer[$i][100] <> ""){
	        $cComObsv = str_replace("CANTIDAD", "CANT",($cComObs[1].". ".$cComObs[0]." ".$mIngTer[$i][100]));
	  		}else{
	  		  $cComObsv = $cComObs[1]." ".$cComObs[0];
	  		}
	  		$aIngTer = explode("~",f_Words($cComObsv,160));
	  		for ($n=0;$n<count($aIngTer);$n++) {
	     		$pdf->setXY($posx,$pyy);
	        $pdf->Cell(135,10,$aIngTer[$n],0,0,'L');
	        $pyy+=3;
	  		}
	  		//$pdf->Cell(135,10,substr(str_replace("CANTIDAD", "CANT", $cComObsv),0,85),0,0,'L');
	  		$pyy -=3;
	  	}#for($i=0;$i<count($mIngTer);$i++){
	  	if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	   $pdf->AddPage();
	  	   $nb++;
	  	   $posy = 90;
	       $posx = 10;
	       $pyy = $posy;
	       $pdf->SetFont('verdana','',9);
	       $pdf->setXY($posx,$posy);
	  	 }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
	  	## Recorro la matriz de la 1002 para imprimir Registros de PCC ##
	  	$nSubToPcc = 0;
	  	for ($i=0;$i<count($mCodDat);$i++) {
	  	 if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	   $pdf->AddPage();
	  	   $nb++;
	  	   $posy = 90;
	       $posx = 10;
	       $pyy = $posy;
	       $pdf->SetFont('verdana','',9);
	       $pdf->setXY($posx,$posy);
	  	 }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
	    	if($mCodDat[$i]['comctocx'] == 'PCC'){
	    		$nSubToPcc += $mCodDat[$i]['comvlrxx'];
	  			$pdf->SetFont('verdana','',8);
	  			$pdf->setXY($posx,$pyy);
	  			$pdf->Cell(135,10,substr(str_replace("CANTIDAD","CANT",$mCodDat[$i]['comobsxx']),0,85),0,0,'L');
	  			$pdf->setXY(145,$pyy);
	  			$pdf->Cell(31,10,"",0,0,'R');
	  			$pdf->setXY(176,$pyy);
	  			$pdf->Cell(34,10,number_format($mCodDat[$i]['comvlrxx'],0,',','.'),0,0,'R');
	  			$pyy +=3;
	    	}#if($mCodDat[$i]['comctocx'] == 'PCC'){
	    }#for ($i=0;$i<count($mCodDat);$i++) {
	  	## Fin Recorro la matriz de la 1002 para imprimir Registros de PCC ##
	  	if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	  $pdf->AddPage();
	  	  $nb++;
	  	  $posy = 90;
	      $posx = 10;
	      $pyy = $posy;
	      $pdf->SetFont('verdana','',9);
	      $pdf->setXY($posx,$posy);
	    }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion

	  	##Imprimo Subtotal de Pagos a Terceros ##
	  	$nTotPcc = $nSubTotPcc + $nSubToPcc;
	  	/*$pdf->SetFont('verdanab','',8);
	  	$pdf->setXY($posx,$pyy);
	  	$pdf->Cell(135,10,"SUBTOTAL PAGOS A TERCEROS",0,0,'L');
	  	$pdf->setXY(145,$pyy);
	  	$pdf->Cell(31,10,"",0,0,'R');
	  	$pdf->setXY(176,$pyy);
	  	$pdf->Cell(30,10,number_format($nTotPcc,0,',','.'),0,0,'R');*/
	  	##Fin Imprimo Subtotal de Pagos a Terceros ##
	  }#if(count($mIngTer) > 0 || $nBandPcc == 1){#Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
	  ##Fin Imprimo Pagos a Terceros ##

	  if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	    $pdf->AddPage();
	    $nb++;
	  	$posy = 90;
	    $posx = 10;
	    $pyy = $posy;
	    $pdf->SetFont('verdana','',9);
	    $pdf->setXY($posx,$posy);
	  }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion

	  $pyy += 5;
	  $nSubToIP = 0;
	  if($nBandIP == 1){#Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
	    if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	  $pdf->AddPage();
	  	  $nb++;
	  	  $posy = 90;
	      $posx = 10;
	      $pyy = $posy;
	      $pdf->SetFont('verdana','',9);
	      $pdf->setXY($posx,$posy);
	  	}#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
	  	$pdf->SetFont('verdanab','',9);
	  	$pdf->setXY($posx,$pyy);
	  	$pdf->Cell(135,10,"INGRESOS PROPIOS",0,0,'L');
		  ##Imprimo Ingresos Propios##
		  $pyy += 3;
		  for ($k=0;$k<count($mCodDat);$k++) {
		    if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	     $pdf->AddPage();
	  	     $nb++;
	  	     $posy = 90;
	         $posx = 10;
	         $pyy = $posy;
	         $pdf->SetFont('verdana','',9);
	         $pdf->setXY($posx,$posy);
	  	  }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
		    if($mCodDat[$k]['comctocx'] == 'IP'){
		    	$nSubToIP += $mCodDat[$k]['comvlrxx'];
		    	$pdf->SetFont('verdana','',8);
	  			$pdf->setXY($posx,$pyy);
          $cValor = "";
          /*
					if($mCodDat[$k]['comfobxx'] == "SI" && $mCodDat[$k]['docfobxx'] > 0) {
						//f_Mensaje(__FILE__,__LINE__,"Entro a impresion Fob 2");
						$cValor  = " FOB: ($".number_format($mCodDat[$k]['docfobxx'],2,'.',',');
						$cValor .= ($mCodDat[$k]['doctrmxx'] > 0) ? " TRM: $".number_format($mCodDat[$k]['doctrmxx'],2,'.',',') : "";
						$cValor .= ")";

					}
					if ($mCodDat[$k]['comcifap'] == "SI"){
						$cValor = "CIF: ($".number_format($mCodDat[$k]['comcifxx'],0,'.',',').")";
					}
					*/
					if ($mCodDat[$k]['comdimap'] == "SI"){
						$cValor = "DIM: (".number_format($mCodDat[$k]['comdimxx'],0,'.',',').")";
					}
					if ($mCodDat[$k]['comdavap'] == "SI"){
						$cValor = "DAV: (".number_format($mCodDat[$k]['comdavxx'],0,'.',',').")";
					}
          /*
					if ($mCodDat[$k]['comvucap'] == "SI"){
						$cValor = " VUCE: (".number_format($mCodDat[$k]['comvucxx'],0,'.',',').")";
					}
					if ($mCodDat[$k]['comcerap'] == "SI"){
					  $cValor = " CERTIFICADOS DE ORIGEN: (".number_format($mCodDat[$k]['comcerxx'],0,'.',',').")";
					}
					if ($mCodDat[$k]['comhorap'] == "SI"){
						$cValor = "HORAS: (".number_format($mCodDat[$k]['comhorxx'],0,'.',',').")";
					}
					if ($mCodDat[$k]['compieap'] == "SI"){
						$cValor = "PIEZAS: (".number_format($mCodDat[$k]['compiexx'],0,'.',',').")";
					}
					if ($mCodDat[$k]['comdexap'] == "SI"){
						$cValor = " DEX: (".number_format($mCodDat[$k]['comdexxx'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comserap'] == "SI"){
						$cValor = " SERIAL: (".number_format($mCodDat[$k]['comserxx'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comaraap'] == "SI"){
						$cValor = " CANT.: (".number_format($mCodDat[$k]['comaraxx'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comdtaap'] == "SI"){
						$cValor = " DTA: (".number_format($mCodDat[$k]['comdtaxx'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comiteap'] == "SI"){
						$cValor = " ITEMS: (".number_format($mCodDat[$k]['comitexx'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comcanap'] == "SI"){
						$cValor = " CANTIDAD: (".number_format($mCodDat[$k]['comcanxx'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comfobap'] == "SI"){
						$cValor = " FOB: ($".number_format($mCodDat[$k]['comfob2x'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comc20ap'] == "SI" || $mCodDat[$k]['comc40ap'] == "SI" || $mCodDat[$k]['comcsuap'] == "SI"){
					  $cValor = "";
					  if($mCodDat[$k]['comc20ap'] == "SI"){
					    $cValor .= " CONTENEDORES DE 20: (".number_format($mCodDat[$k]['comc20xx'],0,'.',',').')';
					  }
					  if($mCodDat[$k]['comc40ap'] == "SI"){
					    $cValor .= " CONTENEDORES DE 40: (".number_format($mCodDat[$k]['comc40xx'],0,'.',',').')';
					  }
					  if ($mCodDat[$k]['comcsuap'] == "SI"){
							$cValor .= " UNIDADES DE CARGA SUELTA: (".number_format($mCodDat[$k]['comcsuxx'],0,'.',',').')';
						}
					}
          */
	        $pdf->Cell(135,10,trim($mCodDat[$k]['comobsxx']." ".$cValor),0,0,'L');
	  			$pdf->setXY(145,$pyy);
	  			$pdf->Cell(31,10,"",0,0,'R');
	  			$pdf->setXY(176,$pyy);
	  			$pdf->Cell(34,10,number_format($mCodDat[$k]['comvlrxx'],0,',','.'),0,0,'R');
	  			$pyy +=3;
		    }#if($mCodDat[$k]['comctocx'] == 'IP'){
		  }#for ($k=0;$k<count($mCodDat);$k++) {
		  ##Fin Imprimo Ingresos Propios##
		  if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	  $pdf->AddPage();
	  	  $nb++;
	  	  $posy = 90;
	      $posx = 10;
	      $pyy = $posy;
	      $pdf->SetFont('verdana','',9);
	      $pdf->setXY($posx,$posy);
	    }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion

		  ##Imprimo Subtotal de Ingresos Propios ##
	  	/*$pdf->SetFont('verdanab','',8);
	  	$pdf->setXY($posx,$pyy);
	  	$pdf->Cell(135,10,"SUBTOTAL INGRESOS PROPIOS",0,0,'L');
	  	$pdf->setXY(145,$pyy);
	  	$pdf->Cell(31,10,"",0,0,'R');
	  	$pdf->setXY(176,$pyy);
	  	$pdf->Cell(30,10,number_format($nSubToIP,0,',','.'),0,0,'R');*/
		  ##Imprimo Subtotal de Ingresos Propios ##
	  }#if($nBandIP == 1){#Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS

	  ##Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##*/

	  ##Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##
	  $nSubToFac = $nTotPcc + $nSubToIP;
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

	  ##Busco Valor de RET.FTE ##
		$nTotRfte = 0;
	  for ($k=0;$k<count($mCodDat);$k++) {
	  	if($mCodDat[$k]['comctocx'] == 'RETFTE'){
	    	$nTotRfte += $mCodDat[$k]['comvlrxx'];
	    }
	  }
	 	##Fin Busco Valor de RET.FTE ##



	  ##Busco Valor de RET.CREE ##
	  $nTotCre = 0;
	  for ($k=0;$k<count($mCodDat);$k++) {
	  	if($mCodDat[$k]['comctocx'] == 'ARETCRE'){
	  		$nTotCre += $mCodDat[$k]['comvlrxx'];
	  	}
	  }
	  ##Fin Busco Valor de RET.CREE ##

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
	     if($mCodDat[$k]['comctocx'] == 'SS' || $mCodDat[$k]['comctocx'] == 'SC'){
	      $nTotPag += $mCodDat[$k]['comvlrxx'];
	     }
	   }
	  ##Fin Busco Valor a Pagar ##

	  $posy = 202;
	  $pdf->SetFont('verdana','',8);
	  if($nTotRfte <> 0){
	  	$pdf->setXY($posx,$posy);
	  	$pdf->Cell(40,10,number_format($nTotRfte,0,',','.'),0,0,'C');
	  }

	  if($nTotCre <> 0){
	  	$pdf->setXY(40,$posy);
	  	$pdf->Cell(40,10,number_format($nTotCre,0,',','.'),0,0,'C');
	  }

	  if($nTotIca <> 0){
	  	$pdf->setXY(70,$posy);
	  	$pdf->Cell(40,10,number_format($nTotIca,0,',','.'),0,0,'C');
	  }
	  if($nTotIva <> 0){
	  	$pdf->setXY(100,$posy);
	  	$pdf->Cell(40,10,number_format($nTotIva,0,',','.'),0,0,'C');
	  }
	  $posy = 198;
	  $posy1 = $posy;

	  //$posy = 208;
	  $pdf->setXY(176,$posy1);
	  $pdf->SetFont('verdanab','',8);
	  if($nTotPcc <> "" || $nTotPcc != 0){
	  	$pdf->Cell(34,10,number_format($nTotPcc,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }
	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  if($nSubToIP <> "" || $nSubToIP != 0){
	  	$pdf->Cell(34,10,number_format($nSubToIP,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }
	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  if($nSubToFac <> "" || $nSubToFac != 0){
	  	$pdf->Cell(34,10,number_format($nSubToFac,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  if($nIva <> "" || $nIva != 0){
	  	$pdf->Cell(34,10,number_format($nIva,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  $nTotRet = ($nTotIva+$nTotIca);
	  if($nTotRet <> "" || $nTotRet != 0){
	  	$pdf->Cell(34,10,number_format($nTotRet,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }
	  $nTotal = ($nSubToFac+$nIva)-($nTotIva+$nTotIca);
	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  if($nTotal <> "" || $nTotal != 0){
	  	$pdf->Cell(34,10,number_format($nTotal,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  if($nTotAnt <> "" || $nTotAnt != 0){
	  	$pdf->Cell(34,10,number_format($nTotAnt,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy1 += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy1);
	  if($nTotPag <> "" || $nTotPag != 0){
	  	if($cNeg == '-'){
	  		$pdf->setXY(176,$posy1+5);
	  		$pdf->Cell(34,10,$cNeg.number_format($nTotPag,0,',','.'),0,0,'R');
	  	}else{
	  		$pdf->Cell(34,10,$cNeg.number_format($nTotPag,0,',','.'),0,0,'R');
	  	}
	  	$pdf->Cell(34,10,$cNeg.number_format($nTotPag,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy += 5;

		$pdf->setXY(10,$posy+7);
	  $pdf->SetFont('verdanab','',6);
    $cVlrLetra = f_Cifra_Php(abs($nTotPag), "PESO");
    $pdf->MultiCell(120, 2.5, "SON:" . utf8_decode($cVlrLetra),0,'L');
	}else{#Si es igual a NORESIDENTE
		$posy = 90;
	  $posx = 10;
	  $posFin = 170;
	  $nb = 1;
	  //$posy +=5;
	  $pyy = $posy;
	  ##Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##
	  $pdf->SetFont('verdanab','',9);
	  $pdf->setXY($posx,$posy);
	  ##Imprimo Pagos a Terceros ##
	  if(count($mIngTer) > 0 || $nBandPcc == 1){#Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
	  	$nSubTotPcc = 0;
	  	$pdf->Cell(135,10,"INGRESOS PARA TERCEROS",0,0,'L');
	  	$pyy += 3;
	  	for($i=0;$i<count($mIngTer);$i++){
	  	 if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	   $pdf->AddPage();
	  	   $nb++;
	  	   $posy = 90;
	       $posx = 10;
	       $pyy = $posy;
	       $pdf->SetFont('verdana','',9);
	       $pdf->setXY($posx,$posy);
	  	 }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
	  		$nSubTotPcc += $mIngTer[$i][20];
	  		$pdf->SetFont('verdana','',8);
	  		$pdf->setXY(176,$pyy);
	  		$pdf->Cell(34,10,number_format($mIngTer[$i][20],2,',','.'),0,0,'R');
	  		$pdf->setXY($posx,$pyy);
	  		$cComObs  = explode("^",$mIngTer[$i][2]);
	  		if($mIngTer[$i][100] <> ""){
	        $cComObsv = str_replace("CANTIDAD", "CANT",($cComObs[0].". ".$mIngTer[$i][100]));
	  		}else{
	  		  $cComObsv = str_replace("CANTIDAD", "CANT",$cComObs[0]);
	  		}
	  	  $aIngTer = explode("~",f_Words($cComObsv,170));
	  		for ($n=0;$n<count($aIngTer);$n++) {
	     		$pdf->setXY($posx,$pyy);
	        $pdf->Cell(135,10,$aIngTer[$n],0,0,'L');
	        $pyy+=3;
	  		}
	  		#$pdf->Cell(135,10,substr(str_replace("CANTIDAD", "CANT", $cComObsv),0,85),0,0,'L');
	  		$pyy -=3;
	  	}#for($i=0;$i<count($mIngTer);$i++){
	  	if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	   $pdf->AddPage();
	  	   $nb++;
	  	   $posy = 90;
	       $posx = 10;
	       $pyy = $posy;
	       $pdf->SetFont('verdana','',9);
	       $pdf->setXY($posx,$posy);
	  	 }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
	  	## Recorro la matriz de la 1002 para imprimir Registros de PCC ##
	  	$nSubToPcc = 0;
	  	for ($i=0;$i<count($mCodDat);$i++) {
	  	 if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	   $pdf->AddPage();
	  	   $nb++;
	  	   $posy = 90;
	       $posx = 10;
	       $pyy = $posy;
	       $pdf->SetFont('verdana','',9);
	       $pdf->setXY($posx,$posy);
	  	 }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
	    	if($mCodDat[$i]['comctocx'] == 'PCC'){
	    		$nSubToPcc += $mCodDat[$i]['comvlrme'];
	  			$pdf->SetFont('verdana','',8);
	  			$pdf->setXY($posx,$pyy);
	  			$pdf->Cell(135,10,substr(str_replace("CANTIDAD","CANT",$mCodDat[$i]['comobsxx']),0,85),0,0,'L');
	  			$pdf->setXY(176,$pyy);
	  			$pdf->Cell(34,10,number_format($mCodDat[$i]['comvlrme'],2,',','.'),0,0,'R');
	  		  $pyy +=3;
	    	}#if($mCodDat[$i]['comctocx'] == 'PCC'){
	    }#for ($i=0;$i<count($mCodDat);$i++) {
	  	## Fin Recorro la matriz de la 1002 para imprimir Registros de PCC ##
	  	if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	  $pdf->AddPage();
	  	  $nb++;
	  	  $posy = 90;
	      $posx = 10;
	      $pyy = $posy;
	      $pdf->SetFont('verdana','',9);
	      $pdf->setXY($posx,$posy);
	    }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion

	  	##Imprimo Subtotal de Pagos a Terceros ##
	  	$nTotPcc = $nSubTotPcc + $nSubToPcc;
	  	/*$pdf->SetFont('verdanab','',8);
	  	$pdf->setXY($posx,$pyy);
	  	$pdf->Cell(135,10,"SUBTOTAL PAGOS A TERCEROS",0,0,'L');
	  	$pdf->setXY(145,$pyy);
	  	$pdf->Cell(31,10,number_format($nTotPcc,2,',','.'),0,0,'R');
	  	$pdf->setXY(176,$pyy);
	  	$pdf->Cell(30,10,"",0,0,'R');*/
	  	##Fin Imprimo Subtotal de Pagos a Terceros ##
	  }#if(count($mIngTer) > 0 || $nBandPcc == 1){#Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
	  ##Fin Imprimo Pagos a Terceros ##

	  if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	    $pdf->AddPage();
	    $nb++;
	  	$posy = 90;
	    $posx = 10;
	    $pyy = $posy;
	    $pdf->SetFont('verdana','',9);
	    $pdf->setXY($posx,$posy);
	  }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion

	  $pyy += 5;
	  $nSubToIP = 0;
	  if($nBandIP == 1){#Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
	    if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	  $pdf->AddPage();
	  	  $nb++;
	  	  $posy = 90;
	      $posx = 10;
	      $pyy = $posy;
	      $pdf->SetFont('verdana','',9);
	      $pdf->setXY($posx,$posy);
	  	}#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
	  	$pdf->SetFont('verdanab','',9);
	  	$pdf->setXY($posx,$pyy);
	  	$pdf->Cell(135,10,"INGRESOS PROPIOS",0,0,'L');
		  ##Imprimo Ingresos Propios##
		  $pyy += 3;
		  for ($k=0;$k<count($mCodDat);$k++) {
		    if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	     $pdf->AddPage();
	  	     $nb++;
	  	     $posy = 90;
	         $posx = 10;
	         $pyy = $posy;
	         $pdf->SetFont('verdana','',9);
	         $pdf->setXY($posx,$posy);
	  	  }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion
		    if($mCodDat[$k]['comctocx'] == 'IP'){
		    	$nSubToIP += $mCodDat[$k]['comvlrme'];
		    	$pdf->SetFont('verdana','',8);
	  			$pdf->setXY($posx,$pyy);

					$cValor = "";
          /*
					if($mCodDat[$k]['comfobxx'] == "SI" && $mCodDat[$k]['docfobxx'] > 0) {
						//f_Mensaje(__FILE__,__LINE__,"Entro a impresion Fob 2");
						$cValor  = " FOB: ($".number_format($mCodDat[$k]['docfobxx'],2,'.',',');
						$cValor .= ($mCodDat[$k]['doctrmxx'] > 0) ? " TRM: $".number_format($mCodDat[$k]['doctrmxx'],2,'.',',') : "";
						$cValor .= ")";

					}
					if ($mCodDat[$k]['comcifap'] == "SI"){
						$cValor = "CIF: ($".number_format($mCodDat[$k]['comcifxx'],0,'.',',').")";
					}
					*/
					if ($mCodDat[$k]['comdimap'] == "SI"){
						$cValor = "DIM: (".number_format($mCodDat[$k]['comdimxx'],0,'.',',').")";
					}
					if ($mCodDat[$k]['comdavap'] == "SI"){
						$cValor = "DAV: (".number_format($mCodDat[$k]['comdavxx'],0,'.',',').")";
					}
          /*
					if ($mCodDat[$k]['comvucap'] == "SI"){
						$cValor = " VUCE: (".number_format($mCodDat[$k]['comvucxx'],0,'.',',').")";
					}
					if ($mCodDat[$k]['comcerap'] == "SI"){
					  $cValor = " CERTIFICADOS DE ORIGEN: (".number_format($mCodDat[$k]['comcerxx'],0,'.',',').")";
					}
					if ($mCodDat[$k]['comhorap'] == "SI"){
						$cValor = "HORAS: (".number_format($mCodDat[$k]['comhorxx'],0,'.',',').")";
					}
					if ($mCodDat[$k]['compieap'] == "SI"){
						$cValor = "PIEZAS: (".number_format($mCodDat[$k]['compiexx'],0,'.',',').")";
					}
					if ($mCodDat[$k]['comdexap'] == "SI"){
						$cValor = " DEX: (".number_format($mCodDat[$k]['comdexxx'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comserap'] == "SI"){
						$cValor = " SERIAL: (".number_format($mCodDat[$k]['comserxx'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comaraap'] == "SI"){
						$cValor = " CANT.: (".number_format($mCodDat[$k]['comaraxx'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comdtaap'] == "SI"){
						$cValor = " DTA: (".number_format($mCodDat[$k]['comdtaxx'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comiteap'] == "SI"){
						$cValor = " ITEMS: (".number_format($mCodDat[$k]['comitexx'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comcanap'] == "SI"){
						$cValor = " CANTIDAD: (".number_format($mCodDat[$k]['comcanxx'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comfobap'] == "SI"){
						$cValor = " FOB: ($".number_format($mCodDat[$k]['comfob2x'],0,'.',',').')';
					}
					if ($mCodDat[$k]['comc20ap'] == "SI" || $mCodDat[$k]['comc40ap'] == "SI" || $mCodDat[$k]['comcsuap'] == "SI"){
					  $cValor = "";
					  if($mCodDat[$k]['comc20ap'] == "SI"){
					    $cValor .= " CONTENEDORES DE 20: (".number_format($mCodDat[$k]['comc20xx'],0,'.',',').')';
					  }
					  if($mCodDat[$k]['comc40ap'] == "SI"){
					    $cValor .= " CONTENEDORES DE 40: (".number_format($mCodDat[$k]['comc40xx'],0,'.',',').')';
					  }
					  if ($mCodDat[$k]['comcsuap'] == "SI"){
							$cValor .= " UNIDADES DE CARGA SUELTA: (".number_format($mCodDat[$k]['comcsuxx'],0,'.',',').')';
						}
					}
          */
	        $pdf->Cell(135,10,trim($mCodDat[$k]['comobsxx']." ".$cValor),0,0,'L');
	  			$pdf->setXY(176,$pyy);
	  			$pdf->Cell(34,10,number_format($mCodDat[$k]['comvlrme'],2,',','.'),0,0,'R');
	  			$pyy +=3;
		    }#if($mCodDat[$k]['comctocx'] == 'IP'){
		  }#for ($k=0;$k<count($mCodDat);$k++) {
		  ##Fin Imprimo Ingresos Propios##
		  if($pyy > $posFin){#Validacion para siguiente pagina si se excede espacio de impresion
	  	  $pdf->AddPage();
	  	  $nb++;
	  	  $posy = 90;
	      $posx = 10;
	      $pyy = $posy;
	      $pdf->SetFont('verdana','',9);
	      $pdf->setXY($posx,$posy);
	    }#if($posy < 130){#Validacion para siguiente pagina si se excede espacio de impresion

		  ##Imprimo Subtotal de Ingresos Propios ##
	  	/*$pdf->SetFont('verdanab','',8);
	  	$pdf->setXY($posx,$pyy);
	  	$pdf->Cell(135,10,"SUBTOTAL INGRESOS PROPIOS",0,0,'L');
	  	$pdf->setXY(145,$pyy);
	  	$pdf->Cell(31,10,number_format($nSubToIP,2,',','.'),0,0,'R');
	  	$pdf->setXY(176,$pyy);
	  	$pdf->Cell(30,10,"",0,0,'R');*/
		  ##Imprimo Subtotal de Ingresos Propios ##
	  }#if($nBandIP == 1){#Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS

	  ##Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios ##*/

	  ##Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##
	  $nSubToFac = $nTotPcc + $nSubToIP;
	  ##Fin Sumo Totales de Pagos a Terceros e Ingresos Propios para impresion de subtotal de factura ##

	  ##Busco valor de IVA ##
		$nIva = 0;
	  for ($k=0;$k<count($mCodDat);$k++) {
	  	if($mCodDat[$k]['comctocx'] == 'IVAIP'){
	    	$nIva += $mCodDat[$k]['comvlrme'];
	   	}
	 	}
	  ##Fin Busco Valor de IVA ##

	 	##Busco Valor de RET.IVA ##
		$nTotIva = 0;
	  for ($k=0;$k<count($mCodDat);$k++) {
	  	if($mCodDat[$k]['comctocx'] == 'RETIVA'){
	    	$nTotIva += $mCodDat[$k]['comvlrme'];
	    }
	  }
	 	##Fin Busco Valor de RET.IVA ##

	  ##Busco Valor de RET.ICA ##
		$nTotIca = 0;
	  for ($k=0;$k<count($mCodDat);$k++) {
	  	if($mCodDat[$k]['comctocx'] == 'RETICA'){
	    	$nTotIca += $mCodDat[$k]['comvlrme'];
	    }
	  }
	 	##Fin Busco Valor de RET.ICA ##

	  ##Busco Valor de RET.FTE ##
		$nTotRfte = 0;
	  for ($k=0;$k<count($mCodDat);$k++) {
	  	if($mCodDat[$k]['comctocx'] == 'RETFTE'){
	    	$nTotRfte += $mCodDat[$k]['comvlrme'];
	    }
	  }
	 	##Fin Busco Valor de RET.FTE ##

	  ##Busco valor de Anticipo ##
	  $cNegativo = "";
	  $cNeg = "";
	  $nTotAnt = 0;
	  /*for ($k=0;$k<count($mCodDat);$k++) {
	  	if($mCodDat[$k]['comctocx'] == 'SS' || $mCodDat[$k]['comctocx'] == 'SC'){
	      if($mCodDat[$k]['comctocx'] == 'SC'){
	        $cNegativo = "MENOS ";
	        $cNeg = "-";
	      }
	       $nTotAnt += $mCodDat[$k]['comvlr01'];
	     }
	   }*/
	   /*
	    * En caso de que el valor a pagar de la Factura sea cero, en detalle no se guarda registro SS o SC,
	    * Razon por la cual no se muestra el valor del anticipo que fue aplicado.
	    * Para imprimir este valor se debe tomar el campo comfpxx de cabecera, posicion 13 donde se guarda el valor del anticipo
	    */
	   	if($nTotAnt == 0){
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

	  ##Busco si el valor a Pagar es Saldo a Favor del cliente, se debe mostrar el menos
		for ($k=0;$k<count($mCodDat);$k++) {
	     if($mCodDat[$k]['comctocx'] == 'SS' || $mCodDat[$k]['comctocx'] == 'SC'){
	     	if($mCodDat[$k]['comctocx'] == 'SC'){
	     		$cNegativo = "MENOS ";
	        $cNeg = "-";
	     }
	   }
		}
	  ##Fin Busco Valor a Pagar ##


	   ##Busco Valor a Pagar ##
	  $nTotPag = 0;
		for ($k=0;$k<count($mCodDat);$k++) {
	     if($mCodDat[$k]['comctocx'] == 'SS' || $mCodDat[$k]['comctocx'] == 'SC'){
	      $nTotPag += $mCodDat[$k]['comvlrme'];
	     }
	   }
	  ##Fin Busco Valor a Pagar ##
		$posy = 198;
	  $pdf->setXY(176,$posy);
	  $pdf->SetFont('verdanab','',8);
	  if($nTotPcc <> "" || $nTotPcc != 0){
	  	$pdf->Cell(34,10,number_format($nTotPcc,2,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }
	  $posy += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy);
	  if($nSubToIP <> "" || $nSubToIP != 0){
	  	$pdf->Cell(34,10,number_format($nSubToIP,0,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $pdf->SetFont('verdanab','',8);
		if($nTotRfte <> 0){
			$pdf->setXY($posx,$posy);
		  $pdf->Cell(40,10,number_format($nTotRfte,0,',','.'),0,0,'C');
		}
		if($nTotIva <> 0){
			$pdf->setXY(50,$posy);
		  $pdf->Cell(40,10,number_format($nTotIca,0,',','.'),0,0,'C');
		}
		if($nTotICa <> 0){
			$pdf->setXY(90,$posy);
		  $pdf->Cell(40,10,number_format($nTotIva,0,',','.'),0,0,'C');
		}
	  $posy = 208;
	  $pdf->setXY(176,$posy);
	  if($nSubToFac <> "" || $nSubToFac != 0){
	  	$pdf->Cell(34,10,number_format($nSubToFac,2,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy);
	  if($nIva <> "" || $nIva != 0){
	  	$pdf->Cell(34,10,number_format($nIva,2,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy += 5;
	  $pdf->SetFont('verdanab','',8);
	  /*$pdf->setXY(145,$posy);
	  $pdf->Cell(31,10,"RET.FTE",0,0,'L');
	  $pdf->Rect(145,$posy+2,31,7);*/
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy);
	  if($nTotRfte <> "" || $nTotRfte != 0){
	  	$pdf->Cell(34,10,number_format($nTotRfte,2,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy += 5;
		$nTotal = ($nSubToFac+$nIva)-($nTotIva+$nTotIca);
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy);
	  if($nTotal <> "" || $nTotal != 0){
	  	$pdf->Cell(34,10,number_format($nTotal,2,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy += 5;
	  $pdf->SetFont('verdanab','',8);
	  /*$pdf->setXY(145,$posy);
	  $pdf->Cell(31,10,"ANTICIPO RECIBIDOS",0,0,'L');
	  $pdf->Rect(145,$posy+2,31,7);*/
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy);
	  if($nTotAnt <> "" || $nTotAnt != 0){
	  	$pdf->Cell(34,10,number_format($nTotAnt,2,',','.'),0,0,'R');
	  }else{
	  	$pdf->Cell(34,10,"",0,0,'R');
	  }

	  $posy += 5;
	  $pdf->SetFont('verdanab','',8);
	  $pdf->setXY(176,$posy);
	  if($nTotPag <> "" || $nTotPag != 0){
	  	if($cNeg == '-'){
	  		$pdf->setXY(176,$posy+5);
	  		$pdf->Cell(34,10,$cNeg.number_format($nTotPag,2,',','.'),0,0,'R');
	  	}else{
        $pdf->Cell(34,10,$cNeg.number_format($nTotPag,2,',','.'),0,0,'R');
	  	}
	  }else{
      $pdf->Cell(34,10,"",0,0,'R');
	  }
	  $posy += 5;

		$pdf->setXY(10,$posy-28);
	  $pdf->SetFont('verdanab','',6);
    $cVlrLetra = f_Cifra_Php(abs($nTotPag), "DOLAR");
    $pdf->MultiCell(120, 2.5, "SON:" . utf8_decode($cVlrLetra),0,'L');
	}

  ##Si la Calidad del Tercero es NORESIDENTE pinto factura en dolares##
  ##Fin Si la Calidad del Tercero es NORESIDENTE pinto factura en dolares##


  }#for($y=1; $y<=5; $y++){
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
     $xFecFor= $fano."-".$fmes."-".$fdia;
   }
   return ($xFecFor);
 }
?>

<?php
  namespace openComex;
	ini_set("memory_limit","512M");
	set_time_limit(0);

	include("../../../../libs/php/utility.php");

	$cCuenta   = $_POST['cCuentas'];
	$cTitCue   = $_POST['cTitulos'];
	$gTerId    = $_POST['cTerId'];
	$gTerId2   = $_POST['cTerId2'];
	$dDesde   = $_POST['dDesde'];
	$dHasta   = $_POST['dHasta'];
	$nAno     = substr($_POST['dDesde'], 0, 4);
  $gFormato = $_POST['cFormato'];
  
  $vCueRetFue = explode(",",$vSysStr['financiero_cuentas_retefuente']);
  $cCueRetFue = "\"".implode("\",\"", $vCueRetFue)."\"";
  $vCueImpAsu = explode(",",$vSysStr['financiero_cuentas_impuestos_asumidos']);
  $cCueImpAsu = "\"".implode("\",\"", $vCueImpAsu)."\"";
  $vCueRetIva = explode(",",$vSysStr['financiero_cuentas_reteiva']);
  $cCueRetIva = "\"".implode("\",\"", $vCueRetIva)."\"";

?>
	<html>
		<head>
			<title>Informe de Medios Magneticos</title>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		</head>
		<script type="text/javascript">
			function f_Datos_Comprobante(xComId,xComCod,xComCsc,xComCsc2,xComFec,xRegEst,xRegFCre,xTipCom){
				document.cookie="kModo=VER;path="+"/";
				document.cookie="kIniAnt=frdetprn.php;path="+"/";
				switch(xComId){
					case "R":
						var zRuta   = '../../../../forms/financia/contable/recibosc/frrecnue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
					break;
					case 'L':
						if(xTipCom == 'PAGOIMPUESTOS'){
						var zRuta   = '../../../../forms/financia/contable/carbcoxx/frcbanue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
						} else {
							var zRuta   = '../../../../forms/financia/contable/ajustesx/frajunue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;           	  
						}
					break;
					case 'P':
						if(xTipCom == 'CPE'){
							var zRuta   = '../../../../forms/financia/contable/comprase/frcpenue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;              
						} else if(xTipCom == 'RCM'){
							var zRuta   = '../../../../forms/financia/contable/cajameno/frcamcau.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gRegFCre='+xComFec;
						} else if(xTipCom == 'CPC_MAN'){
							var zRuta   = '../../../../forms/financia/contable/comprast/frcomnue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
						}else if(xTipCom == 'CPC_AUT'){
							var zRuta   = '../../../../forms/financia/contable/cauproau/frcpanue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
						}           
					break;
					case 'C':
							if(xTipCom != 'AJUSTE'){
								var zRuta   = '../../../../forms/financia/contable/notacrex/frnocnue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;              
							} else {
								var zRuta   = '../../../../forms/financia/contable/ajustesx/frajunue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
							}             
					break;
					case 'G':
						var zRuta   = '../../../../forms/financia/contable/egresosx/fregrnue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
					break;
					case 'N':
					case 'D':
						var zRuta   = '../../../../forms/financia/contable/ajustesx/frajunue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
					break;
					case 'F':
						var zRuta   = '../../../../forms/financia/contable/facturax/frfacnue.php?gComId='+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gRegFCre='+xRegFCre;
					break;
				}
				
				var zX    = screen.width;
				var zY    = screen.height;
				var zNx     = (zX-1100)/2;
				var zNy     = (zY-700)/2;
				var zWinPro = 'width=1100,scrollbars=1,height=700,left='+zNx+',top='+zNy;
				var cNomVen = 'zWindow'+Math.ceil(Math.random()*1000);
				zWindow = window.open(zRuta,cNomVen,zWinPro);
				zWindow.focus();
			}
		</script>
		<body>
			<div id="loading" style="background: white;position: absolute;left: 45%;top: 45%;padding: 2px;height: auto;border: 1px solid #ccc;">
				<div style="background: white;color: #444;font: bold 13px tahoma, arial, helvetica;padding: 10px;margin: 0;height: auto;">
						<img src="<?php echo $cPlesk_Skin_Directory ?>/loading.gif" width="32" height="32" style="margin-right:8px;float:left;vertical-align:top;"/>
						openComex<br>
						<span style="font: normal 10px arial, tahoma, sans-serif;">Cargando...</span>
				</div>
			</div>	
	
	<?php //Cargando datos
	
	//Busco los comprobantes tipo P para saber si es CPE o CPC
	$qDatExt  = "SELECT comidxxx,comcodxx,comtipxx ";
	$qDatExt .= "FROM $cAlfa.fpar0117 ";
	$qDatExt .= "WHERE ";
	$qDatExt .= "(comidxxx = \"P\" OR comidxxx = \"L\" OR comidxxx = \"C\") AND ";
	$qDatExt .= "regestxx = \"ACTIVO\" ";
	$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	$mComP = array();
	while ($xRDE = mysql_fetch_array($xDatExt)){
	$mComP[$xRDE['comidxxx']][$xRDE['comcodxx']] = $xRDE['comtipxx'];
	}
		
	//Buscando nombre del tercero
	$qDatExt  = "SELECT CLINOMXX,CLIAPE1X,CLIAPE2X,CLINOM1X,CLINOM2X ";
	$qDatExt .= "FROM $cAlfa.SIAI0150 ";
	$qDatExt .= "WHERE ";
	$qDatExt .= "CLIIDXXX = \"$gTerId\" LIMIT 0,1 ";
	$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	$xRNC = mysql_fetch_array($xDatExt);
		
	switch ($gFormato) {
		case "1001": // 5295050100
			
			/**
			 * Ticket 8823-Informacion Exogena
			 * Johana Arboleda Ramos 2014-04-05  10:23
			 * se debe incluir la columna Concepto (vacia), la columna Iva mayor valor del costo o gasto no deducible (valor cero),
			 * la columna Retencion en la fuente practicadas CREE (retencion CREE registrada en el sistema) y
			 * la columna Retencion en la fuente asumidas CREE (valor cero).
			 */
			
			#Creando tabla temporal de cuentas 2365, 531520 y 2367
			$cFcoc = "fcod".$nAno;
			$cTabFac = fnCadenaAleatoria();
			$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";   
			$xNewTab = mysql_query($qNewTab,$xConexion01); 

			//Buscando las cuentas de retencion CREE
			$qRetCree  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
			$qRetCree .= "FROM $cAlfa.fpar0115 ";
			$qRetCree .= "WHERE ";
			$qRetCree .= "pucgruxx LIKE \"23\" AND ";
			$qRetCree .= "pucterxx LIKE \"R\"  AND ";
			$qRetCree .= "pucdesxx LIKE \"%CREE%\" AND ";
			$qRetCree .= "pucdesxx NOT LIKE \"%AUTO%\" AND ";
			$qRetCree .= "regestxx = \"ACTIVO\" ";
			$xRetCree  = f_MySql("SELECT","",$qRetCree,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qRetCree."~".mysql_num_rows($xRetCree));
			$mRetCree = array();
			while ($xRRC = mysql_fetch_array($xRetCree)){
				$mRetCree[count($mRetCree)] = $xRRC['pucidxxx'];
			}
			
			$cReteCree = "";
			for($nRC=0; $nRC<count($mRetCree); $nRC++) {
				$cReteCree .= "$cAlfa.fcod$nAno.pucidxxx LIKE \"{$mRetCree[$nRC]}\" OR ";
			}
			$cReteCree = substr($cReteCree, 0, strlen($cReteCree)-4);
			
			$qFcod  = "SELECT * ";
			$qFcod .= "FROM $cAlfa.fcod$nAno ";
			$qFcod .= "WHERE ";
			$qFcod .= "($cAlfa.fcod$nAno.pucidxxx LIKE \"2408%\" OR ";
			$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,4) IN ($cCueRetFue) OR ";
			$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,6) IN ($cCueImpAsu) OR ";
			$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR $cReteCree": "").") AND ";
			$qFcod .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
		
			$qInsert = "INSERT INTO $cAlfa.$cTabFac $qFcod";
			$xInsert = mysql_query($qInsert,$xConexion01);
			#Fin Creando tabla temporal de facturas cabecera
				
			#Trayendo datos del teridxxx
			$qDatExt = "SELECT * ";
			$qDatExt .= "FROM $cAlfa.SIAI0150 ";
			$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"$gTerId\" LIMIT 0,1 ";
			$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
			
			$mCalTer = array();
			if(mysql_num_rows($xDatExt) > 0) {
				$xRDE = mysql_fetch_array($xDatExt);
				if ($xRDE['CLIRECOM']=="SI") {
					$mCalTer[$gTerId] = "COMUN";
				}
				if ($xRDE['CLIRESIM']=="SI") {
					$mCalTer[$gTerId] = "SIMPLIFICADO";
				}
				if ($xRDE['CLIGCXXX']=="SI") {
					$mCalTer[$gTerId] = "CONTRIBUYENTE";
				}
				if ($xRDE['CLINRPXX']=="SI") {
					$mCalTer[$gTerId] = "NORESIDENTE";
				}
			}
			
			$qData  = "SELECT ";
			$qData .= "$cAlfa.fcod$nAno.comidxxx,";
			$qData .= "$cAlfa.fcod$nAno.comcodxx,";
			$qData .= "$cAlfa.fcod$nAno.comcscxx,";
			$qData .= "$cAlfa.fcod$nAno.comcsc2x,";
			$qData .= "$cAlfa.fcod$nAno.comseqxx,";
			$qData .= "$cAlfa.fcod$nAno.comfecxx,";
			$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ccoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.sccidxxx,";
			$qData .= "$cAlfa.fcod$nAno.regestxx,";
			$qData .= "$cAlfa.fcod$nAno.regfcrex,";
			$qData .= "$cAlfa.fcod$nAno.commovxx,";
			$qData .= "$cAlfa.fcod$nAno.teridxxx,";
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
			$qData .= "FROM $cAlfa.fcod$nAno "; 
			$qData .= "WHERE ";
			$qData .= "$cAlfa.fcod$nAno.teridxxx = \"$gTerId\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.pucidxxx  IN ($cCuenta) AND "; 
			$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
			$qData .= "GROUP BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$qData .= "ORDER BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));
			
			$mData = array(); $mComAgru = array();
			while ($xDATA = mysql_fetch_array($xData)) {
				$nIva = 0; $nPrac = 0; $nAsum = 0; $nComun = 0; $nSimpl = 0; $nDom = 0; $nCreePrac = 0; $nCreeAsum = 0;
				#Calculando valores:
				#Iva mayor del costo o gasto deducible
				#Iva mayor valor del costo o gasto no deducible
				#Retencion en la fuente practicada renta
				#Retencion en la fuente asumida renta
				#Retencion en la fuente practicada iva regimen comun
				#Retencion en la fuente asumida iva regimen simp.
				#Retencion en la fuente practicada iva no domiciliados
				#Retencion en la fuente practicadas CREE
				#Retencion en la fuente asumidas CREE
				#Traigo las cuantas que empiezan por 2365,531520,2367 para le comprobante
				$qFcod  = "SELECT ";
				$qFcod .= "$cAlfa.$cTabFac.pucidxxx,";
				$qFcod .= "$cAlfa.$cTabFac.tertipxx,";
				$qFcod .= "$cAlfa.$cTabFac.teridxxx,";
				$qFcod .= "$cAlfa.$cTabFac.tertip2x,";
				$qFcod .= "$cAlfa.$cTabFac.terid2xx,";
				#para las cuentas 2365 y 2367 los creditos suman, los debitos restan
				$qFcod .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx,$cAlfa.$cTabFac.comvlrxx*-1) AS comvlrsu,";
				#para la cuenta 531520 los creditos restan, los debitos suman
				$qFcod .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx*-1,$cAlfa.$cTabFac.comvlrxx) AS comvlrre ";
				$qFcod .= "FROM $cAlfa.$cTabFac ";
				$qFcod .= "WHERE ";
				$qFcod .= "$cAlfa.$cTabFac.comidxxx = \"{$xDATA['comidxxx']}\" AND ";
				$qFcod .= "$cAlfa.$cTabFac.comcodxx = \"{$xDATA['comcodxx']}\" AND ";
				$qFcod .= "$cAlfa.$cTabFac.comcscxx = \"{$xDATA['comcscxx']}\" AND ";
				$qFcod .= "$cAlfa.$cTabFac.teridxxx = \"{$xDATA['teridxxx']}\" AND ";
				$qFcod .= "($cAlfa.$cTabFac.pucidxxx LIKE \"2408%\" OR ";
				$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetFue) OR ";
				$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,6) IN ($cCueImpAsu) OR ";
				$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR ".str_replace("fcod$nAno", $cTabFac, $cReteCree) : "").") AND ";
				$qFcod .= "$cAlfa.$cTabFac.regestxx = \"ACTIVO\" ";
        $xFcod = mysql_query($qFcod,$xConexion01); 
				
				if (mysql_num_rows($xFcod) > 0) {
					while($xRF = mysql_fetch_array($xFcod)) {
						//Verifico si no es una cuenta de retencion Cree
						if (in_array($xRF['pucidxxx'], $mRetCree) == true) {
							$nCreePrac += $xRF['comvlrsu'];
						} else {
							if(substr($xRF['pucidxxx'],0,4) == '2408') {
								$nIva += $xRF['comvlrre'];
              }
              if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue)) {
								$nPrac += $xRF['comvlrsu'];
							}
							if(in_array(substr($xRF['pucidxxx'],0,6), $vCueImpAsu)) {
								$nAsum += $xRF['comvlrre'];
							}
							if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva)) {
								switch ($mCalTer[$xRF['teridxxx']]) {
									case "CONTRIBUYENTE":
									case "COMUN":
										$nComun += $xRF['comvlrsu'];
									break;
									case "SIMPLIFICADO":
										$nSimpl += $xRF['comvlrsu'];
									break;
									case "NORESIDENTE":
										$nDom += $xRF['comvlrsu'];
									break;
								}
							}
						}
					}
				}
				
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx'] = $xDATA['comidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx'] = $xDATA['comcodxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx'] = $xDATA['comcscxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x'] = $xDATA['comcsc2x'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx'] = $xDATA['comseqxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx'] = $xDATA['comfecxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx'] = $xDATA['regestxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex'] = $xDATA['regfcrex'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx'] = $xDATA['ccoidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx'] = $xDATA['sccidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx'] = $xDATA['commovxx'];
				
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlrxx'] += $xDATA['comvlrxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlrno']  = 0;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comivaxx'] +=  ($cAlfa == 'MIRCANAX' || $cAlfa == 'TEMIRCANAX' || $cAlfa == 'DEMIRCANAX')? 0 : $nIva;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comivano']  = 0;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['pracxxxx'] += $nPrac;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['asumxxxx'] += $nAsum;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comunxxx'] += $nComun;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['simplxxx'] += $nSimpl;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ndomxxxx'] += $nDom;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['praccree'] += $nCreePrac;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['asumcree'] += $nCreeAsum;
			}
			
			foreach ($mComAgru as $i => $cValue) {
				$nInd_Data = count($mData);
				$mData[$nInd_Data] = $mComAgru[$i];
			}
			
		break;
		case "1003": 
			
			/**
			 * Ticket 8823-Informacion Exogena
			 * Johana Arboleda Ramos 2014-04-05 10:24
			 * se debe incluir la columna Concepto (vacia).
			 */
			
			$qData  = "SELECT ";
			$qData .= "$cAlfa.fcod$nAno.comidxxx,";
			$qData .= "$cAlfa.fcod$nAno.comcodxx,";
			$qData .= "$cAlfa.fcod$nAno.comcscxx,";
			$qData .= "$cAlfa.fcod$nAno.comcsc2x,";
			$qData .= "$cAlfa.fcod$nAno.comseqxx,";
			$qData .= "$cAlfa.fcod$nAno.comfecxx,";
			$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ccoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.sccidxxx,";
			$qData .= "$cAlfa.fcod$nAno.regestxx,";
			$qData .= "$cAlfa.fcod$nAno.regfcrex,";
			$qData .= "$cAlfa.fcod$nAno.commovxx,";
			$qData .= "$cAlfa.fcod$nAno.teridxxx,";
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx, ";
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"D\",$cAlfa.fcod$nAno.comvlr01,$cAlfa.fcod$nAno.comvlr01*-1)) AS comvlr01  ";
			$qData .= "FROM $cAlfa.fcod$nAno "; 
			$qData .= "WHERE ";
			$qData .= "$cAlfa.fcod$nAno.teridxxx = \"$gTerId\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.pucidxxx  IN ($cCuenta) AND "; 
			$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
			$qData .= "GROUP BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$qData .= "ORDER BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));
			
			$mData = array(); $mComAgru = array();
			while ($xDATA = mysql_fetch_array($xData)) {
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx'] = $xDATA['comidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx'] = $xDATA['comcodxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx'] = $xDATA['comcscxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x'] = $xDATA['comcsc2x'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx'] = $xDATA['comseqxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx'] = $xDATA['comfecxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx'] = $xDATA['regestxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex'] = $xDATA['regfcrex'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx'] = $xDATA['ccoidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx'] = $xDATA['sccidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx'] = $xDATA['commovxx'];
				
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlr01'] += $xDATA['comvlr01'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlrxx'] += $xDATA['comvlrxx'];
			}
			
			foreach ($mComAgru as $i => $cValue) {
				$nInd_Data = count($mData);
				$mData[$nInd_Data] = $mComAgru[$i];
			}			
		break;
		case "1005":
				
			/**
				* Ticket 8823-Informacion Exogena
				* Johana Arboleda Ramos 2014-04-07  08:00
				* Reporte 1005-Impuesto a las ventas (descontable)
				*/
				
			$qData  = "SELECT ";
			$qData .= "$cAlfa.fcod$nAno.comidxxx,";
			$qData .= "$cAlfa.fcod$nAno.comcodxx,";
			$qData .= "$cAlfa.fcod$nAno.comcscxx,";
			$qData .= "$cAlfa.fcod$nAno.comcsc2x,";
			$qData .= "$cAlfa.fcod$nAno.comseqxx,";
			$qData .= "$cAlfa.fcod$nAno.comfecxx,";
			$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ccoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.sccidxxx,";
			$qData .= "$cAlfa.fcod$nAno.regestxx,";
			$qData .= "$cAlfa.fcod$nAno.regfcrex,";
			$qData .= "$cAlfa.fcod$nAno.commovxx,";
			$qData .= "$cAlfa.fcod$nAno.teridxxx,";
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"C\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx, ";
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"C\",$cAlfa.fcod$nAno.comvlr01,$cAlfa.fcod$nAno.comvlr01*-1)) AS comvlr01  ";
			$qData .= "FROM $cAlfa.fcod$nAno ";
			$qData .= "WHERE ";
			$qData .= "$cAlfa.fcod$nAno.teridxxx = \"$gTerId\" AND ";
			$qData .= "$cAlfa.fcod$nAno.pucidxxx  IN ($cCuenta) AND ";
			$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
			$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
			$qData .= "GROUP BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$qData .= "ORDER BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));
				
			$mData = array(); $mComAgru = array();
			while ($xDATA = mysql_fetch_array($xData)) {
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx'] = $xDATA['comidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx'] = $xDATA['comcodxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx'] = $xDATA['comcscxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x'] = $xDATA['comcsc2x'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx'] = $xDATA['comseqxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx'] = $xDATA['comfecxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx'] = $xDATA['regestxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex'] = $xDATA['regfcrex'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx'] = $xDATA['ccoidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx'] = $xDATA['sccidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx'] = $xDATA['commovxx'];
		
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlr01'] += $xDATA['comvlr01'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlrxx'] += $xDATA['comvlrxx'];
			}
				
			foreach ($mComAgru as $i => $cValue) {
				$nInd_Data = count($mData);
				$mData[$nInd_Data] = $mComAgru[$i];
			}
				
		break;
		case "1006": // 2408050100
			
			/**
				* Ticket 8823-Informacion Exogena
				* Johana Arboleda Ramos 2014-04-05  09:50
				* se debe incluir la columna IVA recuperado por operaciones en devoluciones en compras anuladas,
				* rescindidas o resueltas (con valor cero) y la columna Impuesto al consumo (con valor cero).
				*/
			
			$qData  = "SELECT ";
			$qData .= "$cAlfa.fcod$nAno.teridxxx,";
			$qData .= "$cAlfa.fcod$nAno.comidxxx,";
			$qData .= "$cAlfa.fcod$nAno.comcodxx,";
			$qData .= "$cAlfa.fcod$nAno.comcscxx,";
			$qData .= "$cAlfa.fcod$nAno.comcsc2x,";
			$qData .= "$cAlfa.fcod$nAno.comseqxx,";
			$qData .= "$cAlfa.fcod$nAno.comfecxx,";
			$qData .= "$cAlfa.fcod$nAno.ccoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.sccidxxx,";
			$qData .= "$cAlfa.fcod$nAno.regestxx,";
			$qData .= "$cAlfa.fcod$nAno.regfcrex,";
			$qData .= "$cAlfa.fcod$nAno.commovxx,";
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
			$qData .= "FROM $cAlfa.fcod$nAno "; 
			$qData .= "WHERE ";
			$qData .= "$cAlfa.fcod$nAno.teridxxx = \"$gTerId\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.pucidxxx  IN ($cCuenta) AND "; 
			$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND "; 
			//$qData .= "$cAlfa.fcod$nAno.comperxx = \"201005\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
			$qData .= "ORDER BY $cAlfa.fcod$nAno.comfecxx";
			$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));
			
			$mData = array();
			while ($xDATA = mysql_fetch_array($xData)) {
				$nInd_Data = count($mData);
				$mData[$nInd_Data] = $xDATA;
			}
			
		break;
		case "1007": // 4145950100
			
			/**
				* Ticket 8823-Informacion Exogena
				* Johana Arboleda Ramos 2014-04-05  10:06
				* se debe incluir la columna Concepto (vacia).
				*/
			
			#Creando tabla temporal de cuentas 4
			$cFcoc = "fcod".$nAno;
			$cTabFac = fnCadenaAleatoria();
			$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";   
			$xNewTab = mysql_query($qNewTab,$xConexion01); 
				
			$qFcod  = "SELECT * ";
			$qFcod .= "FROM $cAlfa.fcod$nAno ";
			$qFcod .= "WHERE ";
			$qFcod .= "$cAlfa.fcod$nAno.pucidxxx LIKE \"4%\" AND ";
			$qFcod .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
		
			$qInsert = "INSERT INTO $cAlfa.$cTabFac $qFcod";
			$xInsert = mysql_query($qInsert,$xConexion01);
			#Fin Creando tabla temporal de facturas cabecera
			
			$qData  = "SELECT ";			
			$qData .= "$cAlfa.fcod$nAno.comidxxx,";
			$qData .= "$cAlfa.fcod$nAno.comcodxx,";
			$qData .= "$cAlfa.fcod$nAno.comcscxx,";
			$qData .= "$cAlfa.fcod$nAno.comcsc2x,";
			$qData .= "$cAlfa.fcod$nAno.comseqxx,";
			$qData .= "$cAlfa.fcod$nAno.comfecxx,";
			$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ccoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.sccidxxx,";
			$qData .= "$cAlfa.fcod$nAno.regestxx,";
			$qData .= "$cAlfa.fcod$nAno.regfcrex,";
			$qData .= "$cAlfa.fcod$nAno.commovxx,";
			$qData .= "$cAlfa.fcod$nAno.teridxxx,";
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx = \"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
			$qData .= "FROM $cAlfa.fcod$nAno ";
			$qData .= "WHERE ";
			$qData .= "$cAlfa.fcod$nAno.teridxxx = \"$gTerId\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta) AND ";
			$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
			$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
			$qData .= "GROUP BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$qData .= "ORDER BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));
			
			$mData = array();
			$mLisCli = array(); $mDatCli = array();
			while ($xDATA = mysql_fetch_array($xData)) {
				
				$nIpro = 0; $nIcon = 0; $nIfid = 0;
				#Calculando valores:
				#Iva mayor del costo o gasto deducible
				#Retencion en la fuente practicada renta
				#Retencion en la fuente asumida renta
				#Retencion en la fuente practicada iva regimen comun
				#Retencion en la fuente asumida iva regimen simp.
				#Retencion en la fuente practicada iva no domiciliados
				#Traigo las cuantas que empiezan por 4 para le comprobante
				$qFcod  = "SELECT ";
				$qFcod .= "$cAlfa.$cTabFac.pucidxxx, ";
				$qFcod .= "$cAlfa.$cTabFac.tertipxx,";
				$qFcod .= "$cAlfa.$cTabFac.teridxxx, ";
				$qFcod .= "$cAlfa.$cTabFac.tertip2x,";
				$qFcod .= "$cAlfa.$cTabFac.terid2xx, ";
				$qFcod .= "$cAlfa.$cTabFac.comvlrxx ";
				$qFcod .= "FROM $cAlfa.$cTabFac ";
				$qFcod .= "WHERE ";
				$qFcod .= "$cAlfa.$cTabFac.comidxxx = \"{$xDATA['comidxxx']}\" AND ";
				$qFcod .= "$cAlfa.$cTabFac.comcodxx = \"{$xDATA['comcodxx']}\" AND ";
				$qFcod .= "$cAlfa.$cTabFac.comcscxx = \"{$xDATA['comcscxx']}\" AND ";
				$qFcod .= "$cAlfa.$cTabFac.teridxxx = \"{$xDATA['teridxxx']}\" AND ";
				$qFcod .= "$cAlfa.$cTabFac.pucidxxx LIKE \"4%\" AND ";
				$qFcod .= "$cAlfa.$cTabFac.regestxx = \"ACTIVO\" ";
				$xFcod = mysql_query($qFcod,$xConexion01); 
				
				if (mysql_num_rows($xFcod) > 0) {
					while($xRF = mysql_fetch_array($xFcod)) {
						if(substr($xRF['pucidxxx'],0,4) == '4175' || substr($xRF['pucidxxx'],0,4) == '4275') {
							$nIcon += $xRF['comvlrxx'];
						}else {
							$nIpro += $xRF['comvlrxx'];
						}	
						if(substr($xRF['pucidxxx'],0,6) == '424045') {
							$nIfid += $xRF['comvlrxx'];
						}          
					}
				}
				
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx'] = $xDATA['comidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx'] = $xDATA['comcodxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx'] = $xDATA['comcscxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x'] = $xDATA['comcsc2x'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx'] = $xDATA['comseqxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx'] = $xDATA['comfecxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx'] = $xDATA['regestxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex'] = $xDATA['regfcrex'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx'] = $xDATA['ccoidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx'] = $xDATA['sccidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx'] = $xDATA['commovxx'];
				
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['iproxxxx'] += $nIpro;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['iconxxxx'] += $nIcon;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['imanxxxx']  = 0;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['iexpxxxx']  = 0;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ifidxxxx'] += $nIfid;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['iterxxxx']  = 0;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['devxxxxx']  = 0;
			}
			
			foreach ($mComAgru as $i => $cValue) {
				$nInd_Data = count($mData);
				$mData[$nInd_Data] = $mComAgru[$i];
			}
			
		break;
		case "1008":
		
			/**
				* Ticket 8823-Informacion Exogena
				* Johana Arboleda Ramos 2014-04-07  07:50
				* Reporte 1008-Saldo de cuentas por cobrar al 31 de Diciembre- V7
				*/
				
			$AnoIni = (($nAno-3) <= $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($nAno-3);
		
			##Creacion de la tabla detalle del dia
			$mData = array(); $mAux = array();
			for ($nAnio=$AnoIni;$nAnio<=$nAno;$nAnio++) {
				
				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAnio.comidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.comcodxx,";
				$qData .= "$cAlfa.fcod$nAnio.comcscxx,";
				$qData .= "$cAlfa.fcod$nAnio.comcsc2x,";
				$qData .= "$cAlfa.fcod$nAnio.comseqxx,";
				$qData .= "$cAlfa.fcod$nAnio.comfecxx,";
				$qData .= "$cAlfa.fcod$nAnio.pucidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.ctoidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.ccoidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.sccidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.regestxx,";
				$qData .= "$cAlfa.fcod$nAnio.regfcrex,";
				$qData .= "$cAlfa.fcod$nAnio.commovxx,";
				$qData .= "$cAlfa.fcod$nAnio.teridxxx,";
				$qData .= "SUM(if ($cAlfa.fcod$nAnio.commovxx = \"D\", $cAlfa.fcod$nAnio.comvlrxx, $cAlfa.fcod$nAnio.comvlrxx*-1)) AS saldoxxx ";
				$qData .= "FROM $cAlfa.fcod$nAnio, $cAlfa.fpar0115 ";
				$qData .= "WHERE $cAlfa.fcod$nAnio.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
				$qData .= "$cAlfa.fcod$nAnio.teridxxx = \"$gTerId\" AND ";
				$qData .= "$cAlfa.fcod$nAnio.pucidxxx IN ($cCuenta) AND ";
				$qData .= "$cAlfa.fcod$nAnio.comfecxx <= \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAnio.regestxx = \"ACTIVO\" AND ";
				$qData .= "$cAlfa.fpar0115.pucdetxx = \"C\"";
				$qData .= "GROUP BY $cAlfa.fcod$nAnio.comidxxx,$cAlfa.fcod$nAnio.comcodxx,$cAlfa.fcod$nAnio.comcscxx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAnio.comfecxx ";
				$xData = mysql_query($qData,$xConexion01);
				//f_Mensaje(__FILE__,__LINE__,$qData."~".mysql_num_rows($xData));
		
				while ($xDATA = mysql_fetch_array($xData)) {
					if ($xDATA['saldoxxx'] != 0) {				
						
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx']  = $xDATA['comidxxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx']  = $xDATA['comcodxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx']  = $xDATA['comcscxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x']  = $xDATA['comcsc2x'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx']  = $xDATA['comseqxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx']  = $xDATA['comfecxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx']  = $xDATA['regestxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex']  = $xDATA['regfcrex'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx']  = $xDATA['ccoidxxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx']  = $xDATA['sccidxxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx']  = $xDATA['commovxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlrxx'] += $xDATA['saldoxxx'];
					}
				}
			}
												
			foreach ($mComAgru as $xKey => $cValue) {
				if ($mComAgru[$xKey]['comvlrxx'] != 0) {
					$nInd_Data = count($mData);
					$mData[$nInd_Data] = $mComAgru[$xKey];
				}
			}
		break;
		case "1009":
		
			/**
				* Ticket 8823-Informacion Exogena
				* Johana Arboleda Ramos 2014-04-07  07:50
				* Reporte 1009-Saldo de cuentas por pagar al 31 de Diciembre- V7
				*/
		
			$AnoIni = (($nAno-3) <= $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($nAno-3);
		
			##Creacion de la tabla detalle del dia
			$mData = array(); $mAux = array();
			for ($nAnio=$AnoIni;$nAnio<=$nAno;$nAnio++) {		
				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAnio.comidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.comcodxx,";
				$qData .= "$cAlfa.fcod$nAnio.comcscxx,";
				$qData .= "$cAlfa.fcod$nAnio.comcsc2x,";
				$qData .= "$cAlfa.fcod$nAnio.comseqxx,";
				$qData .= "$cAlfa.fcod$nAnio.comfecxx,";
				$qData .= "$cAlfa.fcod$nAnio.pucidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.ctoidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.ccoidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.sccidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.regestxx,";
				$qData .= "$cAlfa.fcod$nAnio.regfcrex,";
				$qData .= "$cAlfa.fcod$nAnio.commovxx,";
				$qData .= "$cAlfa.fcod$nAnio.teridxxx,";
				$qData .= "SUM(if ($cAlfa.fcod$nAnio.commovxx = \"D\", $cAlfa.fcod$nAnio.comvlrxx, $cAlfa.fcod$nAnio.comvlrxx*-1)) AS saldoxxx ";
				$qData .= "FROM $cAlfa.fcod$nAnio, $cAlfa.fpar0115 ";
				$qData .= "WHERE $cAlfa.fcod$nAnio.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
				$qData .= "$cAlfa.fcod$nAnio.teridxxx = \"$gTerId\" AND ";
				$qData .= "$cAlfa.fcod$nAnio.pucidxxx IN ($cCuenta) AND ";
				$qData .= "$cAlfa.fcod$nAnio.comfecxx <= \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAnio.regestxx = \"ACTIVO\" AND ";
				$qData .= "$cAlfa.fpar0115.pucdetxx = \"P\"";
				$qData .= "GROUP BY $cAlfa.fcod$nAnio.comidxxx,$cAlfa.fcod$nAnio.comcodxx,$cAlfa.fcod$nAnio.comcscxx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAnio.comfecxx ";
				$xData = mysql_query($qData,$xConexion01);
				//f_Mensaje(__FILE__,__LINE__,$qData."~".mysql_num_rows($xData));
			
				while ($xDATA = mysql_fetch_array($xData)) {
					if ($xDATA['saldoxxx'] != 0) {
			
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx']  = $xDATA['comidxxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx']  = $xDATA['comcodxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx']  = $xDATA['comcscxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x']  = $xDATA['comcsc2x'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx']  = $xDATA['comseqxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx']  = $xDATA['comfecxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx']  = $xDATA['regestxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex']  = $xDATA['regfcrex'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx']  = $xDATA['ccoidxxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx']  = $xDATA['sccidxxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx']  = $xDATA['commovxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlrxx'] += $xDATA['saldoxxx'];
					}
				}
			}
		
			foreach ($mComAgru as $xKey => $cValue) {
				if ($mComAgru[$xKey]['comvlrxx'] != 0) {
					$nInd_Data = count($mData);
					$mData[$nInd_Data] = $mComAgru[$xKey];
				}
			}
		break;
		case "1012":
		
			/**
				* Ticket 8823-Informacion Exogena
				* Johana Arboleda Ramos 2014-04-08  08:43
				Reporte 1012- Informacion de declaraciones tributarias, acciones, inversiones en bonos titulos valores y cuentas de ahorro y cuentas corrientes  V7
			*/
		
			$AnoIni = (($nAno-3) <= $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($nAno-3);
		
			$cTitCue = "$gTerId";
			##Creacion de la tabla detalle del dia
			$mData = array(); $mAux = array();
			for ($nAnio=$AnoIni;$nAnio<=$nAno;$nAnio++) {
				$qData  = "SELECT ";
				$qData .= "$cAlfa.fcod$nAnio.comidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.comcodxx,";
				$qData .= "$cAlfa.fcod$nAnio.comcscxx,";
				$qData .= "$cAlfa.fcod$nAnio.comcsc2x,";
				$qData .= "$cAlfa.fcod$nAnio.comseqxx,";
				$qData .= "$cAlfa.fcod$nAnio.comfecxx,";
				$qData .= "$cAlfa.fcod$nAnio.pucidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.ctoidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.ccoidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.sccidxxx,";
				$qData .= "$cAlfa.fcod$nAnio.regestxx,";
				$qData .= "$cAlfa.fcod$nAnio.regfcrex,";
				$qData .= "$cAlfa.fcod$nAnio.commovxx,";
				$qData .= "$cAlfa.fcod$nAnio.teridxxx,";
				$qData .= "SUM(if ($cAlfa.fcod$nAnio.commovxx = \"D\", $cAlfa.fcod$nAnio.comvlrxx, $cAlfa.fcod$nAnio.comvlrxx*-1)) AS saldoxxx ";
				$qData .= "FROM $cAlfa.fcod$nAnio, $cAlfa.fpar0115 ";
				$qData .= "WHERE $cAlfa.fcod$nAnio.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
				$qData .= "$cAlfa.fcod$nAnio.pucidxxx = \"$gTerId\" AND ";
				$qData .= "$cAlfa.fcod$nAnio.comfecxx <= \"$dHasta\" AND ";
				$qData .= "$cAlfa.fcod$nAnio.regestxx = \"ACTIVO\" ";
				$qData .= "GROUP BY $cAlfa.fcod$nAnio.comidxxx,$cAlfa.fcod$nAnio.comcodxx,$cAlfa.fcod$nAnio.comcscxx ";
				$qData .= "ORDER BY $cAlfa.fcod$nAnio.comfecxx ";
				$xData = mysql_query($qData,$xConexion01);
				//f_Mensaje(__FILE__,__LINE__,$qData."~".mysql_num_rows($xData));
				
				while ($xDATA = mysql_fetch_array($xData)) {
					if ($xDATA['saldoxxx'] != 0) {					
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx']  = $xDATA['comidxxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx']  = $xDATA['comcodxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx']  = $xDATA['comcscxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x']  = $xDATA['comcsc2x'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx']  = $xDATA['comseqxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx']  = $xDATA['comfecxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx']  = $xDATA['regestxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex']  = $xDATA['regfcrex'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx']  = $xDATA['ccoidxxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx']  = $xDATA['sccidxxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx']  = $xDATA['commovxx'];
						$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlrxx'] += $xDATA['saldoxxx'];
					}
				}
			}
		
			foreach ($mComAgru as $xKey => $cValue) {
				if ($mComAgru[$xKey]['comvlrxx'] != 0) {
					$nInd_Data = count($mData);
					$mData[$nInd_Data] = $mComAgru[$xKey];
				}
			}
		break;
		case "1016": // 1380250100
		
			#Creando tabla temporal de cuentas 2365, 531520 y 2367
			$cFcoc = "fcod".$nAno;
			$cTabFac = fnCadenaAleatoria();
			$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";   
			$xNewTab = mysql_query($qNewTab,$xConexion01); 
					
			//Buscando las cuentas de retencion CREE
			$qRetCree  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
			$qRetCree .= "FROM $cAlfa.fpar0115 ";
			$qRetCree .= "WHERE ";
			$qRetCree .= "pucgruxx LIKE \"23\" AND ";
			$qRetCree .= "pucterxx LIKE \"R\"  AND ";
			$qRetCree .= "pucdesxx LIKE \"%CREE%\" AND ";
			$qRetCree .= "pucdesxx NOT LIKE \"%AUTO%\" AND ";
			$qRetCree .= "regestxx = \"ACTIVO\" ";
			$xRetCree  = f_MySql("SELECT","",$qRetCree,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qRetCree."~".mysql_num_rows($xRetCree));
			$mRetCree = array();
			while ($xRRC = mysql_fetch_array($xRetCree)){
				$mRetCree[count($mRetCree)] = $xRRC['pucidxxx'];
			}
			
			$cReteCree = "";
			for($nRC=0; $nRC<count($mRetCree); $nRC++) {
				$cReteCree .= "$cAlfa.fcod$nAno.pucidxxx LIKE \"{$mRetCree[$nRC]}\" OR ";
			}
			$cReteCree = substr($cReteCree, 0, strlen($cReteCree)-4);
			
			$qFcod  = "SELECT * ";
			$qFcod .= "FROM $cAlfa.fcod$nAno ";
			$qFcod .= "WHERE ";
			$qFcod .= "($cAlfa.fcod$nAno.pucidxxx LIKE \"2408%\" OR ";
			$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,4) IN ($cCueRetFue) OR ";
			$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,6) IN ($cCueImpAsu) OR ";
			$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR $cReteCree": "").") AND ";
			$qFcod .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
		
			$qInsert = "INSERT INTO $cAlfa.$cTabFac $qFcod";
			$xInsert = mysql_query($qInsert,$xConexion01);
			#Fin Creando tabla temporal de facturas cabecera
			
			#Trayendo datos del teridxxx
			$qDatExt = "SELECT * ";
			$qDatExt .= "FROM $cAlfa.SIAI0150 ";
			$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"$gTerId\" LIMIT 0,1 ";
			$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
			
			$mCalTer = array();
			if(mysql_num_rows($xDatExt) > 0) {
				$xRDE = mysql_fetch_array($xDatExt);
				if ($xRDE['CLIRECOM']=="SI") {
					$mCalTer[$gTerId] = "COMUN";
				}
				if ($xRDE['CLIRESIM']=="SI") {
					$mCalTer[$gTerId] = "SIMPLIFICADO";
				}
				if ($xRDE['CLIGCXXX']=="SI") {
					$mCalTer[$gTerId] = "CONTRIBUYENTE";
				}
				if ($xRDE['CLINRPXX']=="SI") {
					$mCalTer[$gTerId] = "NORESIDENTE";
				}
			}

			#Trayendo datos del terid2xx
			$qDatExt = "SELECT * ";
			$qDatExt .= "FROM $cAlfa.SIAI0150 ";
			$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"$gTerId2\" LIMIT 0,1 ";
			$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
			
			if(mysql_num_rows($xDatExt) > 0) {
				$xRDE = mysql_fetch_array($xDatExt);
				if ($xRDE['CLIRECOM']=="SI") {
					$mCalTer[$gTerId2] = "COMUN";
				}
				if ($xRDE['CLIRESIM']=="SI") {
					$mCalTer[$gTerId2] = "SIMPLIFICADO";
				}
				if ($xRDE['CLIGCXXX']=="SI") {
					$mCalTer[$gTerId2] = "CONTRIBUYENTE";
				}
				if ($xRDE['CLINRPXX']=="SI") {
					$mCalTer[$gTerId2] = "NORESIDENTE";
				}
			}
			
			#Busco si el concepto contable es de anticipo
			$qCtoAnt  = "SELECT ";
			$qCtoAnt .= "ctoidxxx ";
			$qCtoAnt .= "FROM $cAlfa.fpar0119 ";
			$qCtoAnt .= "WHERE  ";
			$qCtoAnt .= "ctoantxx = \"SI\" AND ";
			$qCtoAnt .= "regestxx = \"ACTIVO\" ";
			$xCtoAnt  = f_MySql("SELECT","",$qCtoAnt,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qCtoAnt." ~ ".mysql_num_rows($xCtoAnt));
			$cCtoAnt = "";
			while ($xRPT = mysql_fetch_array($xCtoAnt)) {
				$cCtoAnt .= "{$xRPT['ctoidxxx']},";
			}
			$cCtoAnt = substr($cCtoAnt, 0, strlen($cCtoAnt)-1);
			
			if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
				#Busco si el concepto contable es de pago de tributo
				$qPagTri  = "SELECT ";
				$qPagTri .= "ctoidxxx ";
				$qPagTri .= "FROM $cAlfa.fpar0119 ";
				$qPagTri .= "WHERE  ";
				$qPagTri .= "(ctoptaxg = \"SI\" OR ctoptaxl = \"SI\") AND ";
				$qPagTri .= "regestxx = \"ACTIVO\" ";
				$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qPagTri." ~ ".mysql_num_rows($xPagTri));
				$cPagTri = "";
				while ($xRPT = mysql_fetch_array($xPagTri)) {
					$cPagTri .= "{$xRPT['ctoidxxx']},";
				}
				$cPagTri = substr($cPagTri, 0, strlen($cPagTri)-1);
				
				//Buscando las L que no son ajustes
				$qCarBa  = "SELECT ";
				$qCarBa .= "CONCAT(comidxxx,\"~\",comcodxx) AS comidxxx ";
				$qCarBa .= "FROM $cAlfa.fpar0117 ";
				$qCarBa .= "WHERE ";
				$qCarBa .= "comidxxx = \"L\" AND ";
				$qCarBa .= "comtipxx != \"AJUSTES\" ";
				$xCarBa = f_MySql("SELECT","",$qCarBa,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qCarBa." ~ ".mysql_num_rows($xCarBa));
				$vCarBa = "";
				while ($xRCB = mysql_fetch_array($xCarBa)) {
					$vCarBa[] = $xRCB['comidxxx'];
				}
			}
			
			$qFpar117  = "SELECT comidxxx, comcodxx ";
			$qFpar117 .= "FROM $cAlfa.fpar0117 ";
			$qFpar117 .= "WHERE ";
			$qFpar117 .= "comtipxx  = \"RCM\"";
			$xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
			$mRCM = array();
			while ($xRF117 = mysql_fetch_array($xFpar117)) {
				$mRCM[count($mRCM)] = "{$xRF117['comidxxx']}~{$xRF117['comcodxx']}";
			}
			
			/**
				* Ticket 22351: Buscando los comprobantes de nota credito para COLMAS, estos comprobantes deben excluirse
				* Ajuste: 2016-03-10 10:14 se incluye cambio para excluir las notas credito para todas las agencias de aduana  
				*/
			$cNotCre  = "";
			switch ($cAlfa) {
				case "COLMASXX":
				case "DECOLMASXX":
				case "TECOLMASXX":
					$cNotCre .= "\"L~044\",";
					$cNotCre .= "\"L~024\",";
					$cNotCre .= "\"L~020\",";
					$cNotCre .= "\"L~016\",";
					$cNotCre .= "\"C~001\",";
					$cNotCre .= "\"C~002\",";
					$cNotCre .= "\"C~003\",";
					$cNotCre .= "\"C~004\",";
				break;          
				default:
					//No hace nada
				break;
			}
			$qNotCre  = "SELECT ";
			$qNotCre .= "CONCAT(comidxxx,\"~\",comcodxx) AS comidxxx ";
			$qNotCre .= "FROM $cAlfa.fpar0117 ";
			$qNotCre .= "WHERE ";
			$qNotCre .= "comidxxx = \"C\" AND ";
			$qNotCre .= "comtipxx != \"AJUSTES\" ";
			$xNotCre = f_MySql("SELECT","",$qNotCre,$xConexion01,"");
			while ($xRDB = mysql_fetch_array($xNotCre)) {
				$cNotCre .= "\"{$xRDB['comidxxx']}\",";
			}
			$cNotCre = substr($cNotCre,0,strlen($cNotCre)-1); 
			
			$qData  = "SELECT ";
			$qData .= "$cAlfa.fcod$nAno.comidxxx,";
			$qData .= "$cAlfa.fcod$nAno.comcodxx,";
			$qData .= "$cAlfa.fcod$nAno.comcscxx,";
			$qData .= "$cAlfa.fcod$nAno.comcsc2x,";
			$qData .= "$cAlfa.fcod$nAno.comseqxx,";
			$qData .= "$cAlfa.fcod$nAno.comfecxx,";
			$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ccoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.sccidxxx,";
			$qData .= "$cAlfa.fcod$nAno.teridxxx,";
			$qData .= "$cAlfa.fcod$nAno.terid2xx,";
			$qData .= "$cAlfa.fcod$nAno.regestxx,";
			$qData .= "$cAlfa.fcod$nAno.regfcrex,";
			$qData .= "$cAlfa.fcod$nAno.commovxx,";
			//Comprobante cruce dos
			$qData .= "$cAlfa.fcod$nAno.comidc2x,";
			$qData .= "$cAlfa.fcod$nAno.comcodc2,";
			$qData .= "$cAlfa.fcod$nAno.comcscc2,";
			$qData .= "$cAlfa.fcod$nAno.comseqc2,";
			//Concatenando consecutivo Dos, para el caso de los comprobantes de caja menor
			$qData .= "GROUP_CONCAT(CONCAT(comidc2x,\"-\",comcodc2,\"-\",comcscc2,\"-\",comseqc2) SEPARATOR \"~\") AS cajameno,";
			/**
				* Sumatoria valores
				* Para COLMAS, GLA y ADUACARGA se mantiene igual, ya que ellos tienen su propia logica, 
				* para las demas agencias 
				* si la base es cero y el iva es cero, la base debe ser igual al valor del comprobante
				*/
			switch ($cAlfa) {
				case "COLMASXX": case "DECOLMASXX": case "TECOLMASXX":
				case "GRUPOGLA": case "DEGRUPOGLA": case "TEGRUPOGLA":
				case "ADUACARX": case "DEADUACARX": case "TEADUACARX":
					$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr02,$cAlfa.fcod$nAno.comvlr02*-1)) AS comvlr02,";
					$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr01,$cAlfa.fcod$nAno.comvlr01*-1)) AS comvlr01,";
					$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
				break;          
				default:
					$nAsiBas = "IF($cAlfa.fcod$nAno.comvlr01 = 0 AND $cAlfa.fcod$nAno.comvlr02 = 0, $cAlfa.fcod$nAno.comvlrxx, $cAlfa.fcod$nAno.comvlr01)";
					$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr02,$cAlfa.fcod$nAno.comvlr02*-1)) AS comvlr02,";
					$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$nAsiBas,$nAsiBas*-1)) AS comvlr01,";
					$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
				break;
			}
			$qData .= "FROM $cAlfa.fcod$nAno "; 
			$qData .= "LEFT JOIN $cAlfa.fcoc$nAno ON $cAlfa.fcod$nAno.comidxxx = $cAlfa.fcoc$nAno.comidxxx AND $cAlfa.fcod$nAno.comcodxx = $cAlfa.fcoc$nAno.comcodxx AND $cAlfa.fcod$nAno.comcscxx = $cAlfa.fcoc$nAno.comcscxx AND $cAlfa.fcod$nAno.comcsc2x = $cAlfa.fcoc$nAno.comcsc2x ";
			$qData .= "WHERE ";
			$qData .= "$cAlfa.fcod$nAno.comidxxx != \"F\"          AND ";
			$qData .= "$cAlfa.fcod$nAno.terid2xx = \"$gTerId2\"    AND "; 
			$qData .= "$cAlfa.fcod$nAno.teridxxx = \"$gTerId\"     AND "; 
			$qData .= "$cAlfa.fcoc$nAno.comintpa != \"SI\"         AND ";
			$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta)     AND ";
			$qData .= "$cAlfa.fcod$nAno.ctoidxxx NOT IN ($cCtoAnt) AND ";
			if (($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") && $cPagTri <> "") {
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx NOT IN ($cPagTri) AND ";
			}
			/**
				* Ticket 22351: Buscando los comprobantes de nota credito para COLMAS, estos comprobantes deben excluirse
				* Ajuste: 2016-03-10 10:14 se incluye cambio para excluir las notas credito para todas las agencias de aduana 
				*/
			if ($cNotCre != "") {
				$qData .= "CONCAT($cAlfa.fcod$nAno.comidxxx,\"~\",$cAlfa.fcod$nAno.comcodxx) NOT IN ($cNotCre) AND ";
			} 
			$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND "; 
			/*$qData .= "$cAlfa.fcod$nAno.comidxxx = \"N\" AND ";
			$qData .= "$cAlfa.fcod$nAno.comcodxx = \"013\" AND ";
			$qData .= "$cAlfa.fcod$nAno.comcscxx = \"2013110039\" AND ";*/
			$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
			$qData .= "GROUP BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$qData .= "ORDER BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));
			
			$mData = array(); $mComAgru = array();
			while ($xDATA = mysql_fetch_array($xData)) {
				$nPrac = 0; $nAsum = 0; $nComun = 0; $nSimpl = 0; $nDom = 0; $nCreeAsum = 0; $nCreePrac = 0;
				
				#Calculando valores:
				#Retencion en la fuente practicada renta
				#Retencion en la fuente asumida renta
				#Retencion en la fuente practicada iva regimen comun
				#Retencion en la fuente asumida iva regimen simp.
				#Retencion en la fuente practicada iva no domiciliados
				#Traigo las cuantas que empiezan por 2365,531520,2367 para le comprobante
				$qFcod  = "SELECT ";
				$qFcod .= "$cAlfa.$cTabFac.*,";
				#para las cuentas 2365 y 2367 los creditos suman, los debitos restan
				$qFcod .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx,$cAlfa.$cTabFac.comvlrxx*-1) AS comvlrsu,";
				#para la cuenta 531520 los creditos restan, los debitos suman
				$qFcod .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx*-1,$cAlfa.$cTabFac.comvlrxx) AS comvlrre ";
				$qFcod .= "FROM $cAlfa.$cTabFac ";
				$qFcod .= "WHERE ";
				$qFcod .= "$cAlfa.$cTabFac.comidxxx = \"{$xDATA['comidxxx']}\" AND ";
				$qFcod .= "$cAlfa.$cTabFac.comcodxx = \"{$xDATA['comcodxx']}\" AND ";
				$qFcod .= "$cAlfa.$cTabFac.comcscxx = \"{$xDATA['comcscxx']}\" AND ";
				switch ($cAlfa) {
					case "ADUACARX":
					case "TEADUACARX":
					case "DEADUACARX":
						//Las validaciones de los terceros se hacen en el while
						$qFcod .= "(SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetFue) OR SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,6) IN ($cCueImpAsu) OR SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR ".str_replace("fcod$nAno", $cTabFac, $cReteCree) : "").") AND ";
					break;
					default:
						$qFcod .= "(($cAlfa.$cTabFac.teridxxx = \"$gTerId2\" AND $cAlfa.$cTabFac.terid2xx = \"$gTerId\") OR ($cAlfa.$cTabFac.teridxxx = \"$gTerId\" AND $cAlfa.$cTabFac.terid2xx =  \"$gTerId2\")) AND ";
						$qFcod .= "(SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetFue) OR ";
						$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,6) IN ($cCueImpAsu) OR ";
						$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR ".str_replace("fcod$nAno", $cTabFac, $cReteCree) : "").") AND ";	
					break;
				}
				$qFcod .= "$cAlfa.$cTabFac.regestxx = \"ACTIVO\" ";
				$xFcod = mysql_query($qFcod,$xConexion01);
				
				if (mysql_num_rows($xFcod) > 0) {
					while($xRF = mysql_fetch_array($xFcod)) {
						$nIncRect = 0;
						//Validaciones de los terceros para ADUACARGA
						if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
						$nIncRect = 1;	       	 	
						//Primera condicion: que los terceros del reistro analizado esten contenidos en los terceros de la retencion
						if (($xRF['teridxxx'] == $xDATA['terid2xx'] && $xRF['terid2xx'] == $xDATA['teridxxx']) ||
								($xRF['teridxxx'] == $xDATA['teridxxx'] && $xRF['terid2xx'] == $xDATA['terid2xx'])) {
							$nIncRect = 0;
						} else { 
							//si el comprobante es una G y el terid2xx del reistro analizado este en el teridxxx de la retencion,
							//y el teridxxx del registro analizado este en el subcentro de costo de la retencion
							//y la cuenta de retencion empieza por 2365 o 2367
							if (($xRF['comidxxx'] == "G" || ($xRF['comidxxx'] == "L" && in_array($xRF['comidxxx']."~".$xRF['comcodxx'], $vCarBa) == true)) && 
                  $xRF['teridxxx'] == $xDATA['terid2xx'] && $xRF['sccidxxx'] == $xDATA['teridxxx'] &&	
									(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue) || in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva))) {
								$nIncRect = 0;		
							} else {
								//Se busca si el subcentro de costo de la retencion es un DO, se trae el importador 
								$qDatDo = "SELECT cliidxxx FROM $cAlfa.sys00121 WHERE docidxxx = \"{$xRF['sccidxxx'] }\" LIMIT 0,1";
								$xDatDo  = f_MySql("SELECT","",$qDatDo,$xConexion01,"");
								//f_Mensaje(__FILE__,__LINE__,$qDatDo." ~ ".mysql_num_rows($xDatDo));

								//si el comprobante es una G y el terid2xx del reistro analizado este en el teridxxx de la retencion,
								//y el teridxxx del registro analizado es el importador del DO
								//y la cuenta empiece por 2365 o 2367
								if (mysql_num_rows($xDatDo) > 0) {
									$xRDD = mysql_fetch_array($xDatDo);
									if (($xRF['comidxxx'] == "G" || ($xRF['comidxxx'] == "L" && in_array($xRF['comidxxx']."~".$xRF['comcodxx'], $vCarBa) == true)) && 
											$xRF['teridxxx'] == $xDATA['terid2xx'] && $xDATA['teridxxx'] == $xRDD['cliidxxx'] &&		
											(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue) || in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva))) {
										$nIncRect = 0;		
									}
								}
							}
						}
					} 

						if ($nIncRect == 0) {
							
						//Verifico si no es una cuenta de retencion Cree
						if (in_array($xRF['pucidxxx'], $mRetCree) == true) {
							$nCreePrac += $xRF['comvlrsu'];
						}else{
              //Verifico si es un iva practicado
							if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue)) {
								$nPrac += $xRF['comvlrsu'];
							}
							//Verifico si es un iva asumido
							if(in_array(substr($xRF['pucidxxx'],0,6), $vCueImpAsu)) {
								$nAsum += $xRF['comvlrre'];
							}
							//Verifico el tipo de rentencion
							if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva)) {
								if($xRF['tertipxx'] == "CLIPROCX"){
									$cCliId = $xRF['teridxxx'];
								} else {
									$cCliId = $xRF['terid2xx'];
								}
								switch ($mCalTer[$cCliId]) {
									case "CONTRIBUYENTE":
									case "COMUN":
									$nComun += $xRF['comvlrsu'];
									break;
									case "SIMPLIFICADO":
									$nSimpl += $xRF['comvlrsu'];
									break;
									case "NORESIDENTE":
									$nDom += $xRF['comvlrsu'];
									break;
								}
							}
							}
						}
					}
				}
				
				//Para los comprobantes de Reembolso de caja Menor trae el iva del Recibo de Caja Menor
				//Busco el comprobante cruce dos en recibos de caja menor
				if ($xDATA['cajameno'] != "" && $xDATA['comvlr02'] == 0 && in_array("{$xDATA['comidxxx']}~{$xDATA['comcodxx']}", $mRCM) == true) {
					$vRecCaja  = explode("~",$xDATA['cajameno']);
					$cRecCaja  = "";
					for ($nRC=0; $nRC<count($vRecCaja); $nRC++) {
						$cRecCaja .= ($vRecCaja[$nRC] != "") ? "\"{$vRecCaja[$nRC]}\"," : "";        		
					}
					$cRecCaja = substr($cRecCaja, 0, strlen($cRecCaja)-1);
					
					if ($cRecCaja != "") {
						$qRecCaja  = "SELECT SUM(IF(commovxx=\"D\",comvlr02,comvlr02*-1)) AS comvlr02 ";
						$qRecCaja .= "FROM $cAlfa.fcme$nAno ";
						$qRecCaja .= "WHERE ";
						$qRecCaja .= "CONCAT(comidxxx,\"-\",comcodxx,\"-\",comcscxx,\"-\",comseqxx) IN ($cRecCaja)";
						$xRecCaja  = f_MySql("SELECT","",$qRecCaja,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qRecCaja." ~ ".mysql_num_rows($xRecCaja));
						if (mysql_num_rows($xRecCaja) > 0) {
							$xRRC = mysql_fetch_array($xRecCaja);
							$xDATA['comvlr02'] = $xRRC['comvlr02'];
						}
					}
				}
				
			if ($xDATA['comvlr01'] == 0) {    
				switch ($cAlfa) {
					case "COLMASXX":
					case "DECOLMASXX":
					case "TECOLMASXX":
					$xDATA['comvlr01'] = $xDATA['comvlrxx'];
					$xDATA['comvlr02'] = 0;
					break;          
					default:
						$nCal = 0;
						if ($xDATA['comidxxx'] == "G" || $xDATA['comidxxx'] == "L") {
								#Busco si el concepto contable es de pago de tributo
								$qPagTri  = "SELECT ";
								$qPagTri .= "ctoptaxg, ";
								$qPagTri .= "ctoptaxl  ";
								$qPagTri .= "FROM $cAlfa.fpar0119 ";
								$qPagTri .= "WHERE  ";
								$qPagTri .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND  ";
								$qPagTri .= "ctoidxxx = \"{$xDATA['ctoidxxx']}\" LIMIT 0,1  ";
								$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
								//f_Mensaje(__FILE__,__LINE__,$qPagTri." ~ ".mysql_num_rows($xPagTri));
								if (mysql_num_rows($xPagTri) > 0) {
									$xRPT = mysql_fetch_array($xPagTri);
									if($xDATA['comidxxx'] == "G" && $xRPT['ctoptaxg'] == "SI") {
										$xDATA['comvlr01'] = $xDATA['comvlrxx'];
										$xDATA['comvlr02'] = 0;
										$nCal = 1;
									}
									
								if($xDATA['comidxxx'] == "L" && $xRPT['ctoptaxl'] == "SI") {
										$xDATA['comvlr01'] = $xDATA['comvlrxx'];
										$xDATA['comvlr02'] = 0;
										$nCal = 1;
									}
								}
						}
						if ($nCal == 0) {
							switch ($cAlfa) {
								case "GRUPOGLA": case "DEGRUPOGLA": case "TEGRUPOGLA":
								case "ADUACARX": case "DEADUACARX": case "TEADUACARX":
									#Si el comvrl01x es cero calculo la base
									$xDATA['comvlr01'] = ($xDATA['comvlrxx']/1.16);
									$xDATA['comvlr02'] = ($xDATA['comvlr01']*0.16);  
								break;
								default:
									#Si el valor de la base e iva es cero, en la base se envia el valor del comprobante
									$xDATA['comvlr01'] = ($xDATA['comvlr01'] == 0 && $xDATA['comvlr02'] == 0) ? $xDATA['comvlrxx'] : $xDATA['comvlr01'];
								break;
							}
						}
					break;
				}
				}
				
				#Se incluye para ADUACARGA que en la columna IVA mayor valor del costo o gasto sea siempre de valor cero
				if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
					$xDATA['comvlr02'] = 0;
				}
				
				#Validacion para ADUACARGA, se excluye el registro si este tienen valor cero en las siguientes columnas:
				#Pago o abono en cta 	
				#IVA mayor valor del costo o gasto 	
				#Retencion en la fuente practicada renta 	
				#Retencion en la fuente asumida renta 	
				#Retencion en la fuente practicada iva regimen comun 	
				#Retencion en la fuente asumida iva regimen simp. 	
				#Retencion en la fuente practicada iva no domiciliados
				$nIncluir = 0;
				if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
					$nIncluir = ($xDATA['comvlr01'] == 0 && $xDATA['comvlr02'] == 0 && $nPrac == 0  && $nAsum == 0 && $nComun == 0 && $nSimpl == 0 && $nDom == 0) ? 1 : 0;	
				}
				
				if ($nIncluir == 0) {	        
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx'] = $xDATA['comidxxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx'] = $xDATA['comcodxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx'] = $xDATA['comcscxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x'] = $xDATA['comcsc2x'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx'] = $xDATA['comseqxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx'] = $xDATA['comfecxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx'] = $xDATA['regestxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex'] = $xDATA['regfcrex'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx'] = $xDATA['ccoidxxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx'] = $xDATA['sccidxxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx'] = $xDATA['commovxx'];
					
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlrxx'] += $xDATA['comvlrxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlr01'] += $xDATA['comvlr01'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlr02'] += $xDATA['comvlr02'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['pracxxxx'] += $nPrac;
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['asumxxxx'] += $nAsum;
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comunxxx'] += $nComun;
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['simplxxx'] += $nSimpl;
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ndomxxxx'] += $nDom;
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['praccree'] += $nCreePrac;
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['asumcree'] += $nCreeAsum;
				}
			}
			
			foreach ($mComAgru as $i => $cValue) {
				$nInd_Data = count($mData);
				$mData[$nInd_Data] = $mComAgru[$i];
			}
			
		break;
		case "1018":
		
			//Dentro de las cuentas seleccionadas busco solo aquellas que sean por cobrar
			$cCuenta = str_replace("\"", "", $cCuenta);
			$vCuenta = explode(",", $cCuenta);
			$cCuenta = "";
			for($nC=0; $nC<count($vCuenta); $nC++) {
				if ($vCuenta[$nC] != "") {
					$qCxC  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
					$qCxC .= "FROM $cAlfa.fpar0115 ";
					$qCxC .= "WHERE ";
					$qCxC .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$vCuenta[$nC]}\" AND ";
					$qCxC .= "pucdetxx = \"C\" LIMIT 0,1";
					$xCxC  = mysql_query($qCxC,$xConexion01);
					//f_Mensaje(__FILE__,__LINE__,$qCxC."~".mysql_num_rows($xCxC));
					if (mysql_num_rows($xCxC) > 0) {
						$xRCxC = mysql_fetch_array($xCxC);
						$cCuenta .= "\"{$xRCxC['pucidxxx']}\",";
					}
				}
			}
			$cCuenta = substr($cCuenta, 0, -1);
			
			$vTablas = array(); $mData = array(); $mComAgru = array();
			if ($cCuenta != "") { 
				for ($nAnio=$vSysStr['financiero_ano_instalacion_modulo'];$nAnio<=$nAno;$nAnio++) {
					$qDatMov  = "SELECT ";
					$qDatMov .= "comidxxx,";
					$qDatMov .= "comcodxx,";
					$qDatMov .= "comcscxx,";
					$qDatMov .= "comcsc2x,";
					$qDatMov .= "comseqxx,";
					$qDatMov .= "comidcxx,";
					$qDatMov .= "comcodcx,";
					$qDatMov .= "comcsccx,";
					$qDatMov .= "pucidxxx,";
					$qDatMov .= "teridxxx,";
					$qDatMov .= "terid2xx,";
					$qDatMov .= "comfecxx,";
					$qDatMov .= "regestxx,";
					$qDatMov .= "regfcrex,";
					$qDatMov .= "ccoidxxx,";
					$qDatMov .= "sccidxxx,";
					$qDatMov .= "commovxx,";
					$qDatMov .= "IF(commovxx = \"D\", comvlrxx, comvlrxx*-1) AS saldoxxx ";
					$qDatMov .= "FROM $cAlfa.fcod$nAnio ";
					$qDatMov .= "WHERE  ";
					$qDatMov .= "pucidxxx IN ($cCuenta)  AND ";
					$qDatMov .= "comfecxx <= \"$dHasta\" AND ";
					$qDatMov .= "teridxxx = \"$gTerId\"  AND ";
					$qDatMov .= "regestxx = \"ACTIVO\" ";
					$qDatMov .= "ORDER BY comidcxx,comcodcx,comcsccx,teridxxx,comfecxx,pucidxxx ";
					$xDatMov = mysql_query($qDatMov,$xConexion01);
					//echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";
					
					while ($xDATA = mysql_fetch_array($xDatMov)) {
						
						if ($xDATA['comidxxx'] != $xDATA['comidcxx'] || 
								$xDATA['comcodxx'] != $xDATA['comcodcx'] || 
								$xDATA['comcscxx'] != $xDATA['comcsccx']) {
							//Se debe buscar el cliente del combropante que se esta cancelando
							for ($nAnioDC=$nAnio;$nAnioDC>=$vSysStr['financiero_ano_instalacion_modulo'];$nAnioDC--) {
								$qDocCru  = "SELECT ";
								$qDocCru .= "terid2xx  ";
								$qDocCru .= "FROM $cAlfa.fcod$nAnioDC ";
								$qDocCru .= "WHERE  ";
								$qDocCru .= "comidxxx = \"{$xDATA['comidcxx']}\" AND ";
								$qDocCru .= "comcodxx = \"{$xDATA['comcodcx']}\" AND ";
								$qDocCru .= "comcscxx = \"{$xDATA['comcsccx']}\" AND ";
								$qDocCru .= "teridxxx = \"{$xDATA['teridxxx']}\" AND ";
								$qDocCru .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND ";
								$qDocCru .= "regestxx = \"ACTIVO\" LIMIT 0,1";
								$xDocCru = mysql_query($qDocCru,$xConexion01);
								//echo $qDocCru."~".mysql_num_rows($xDocCru)."<br><br>";
								if (mysql_num_rows($xDocCru) > 0) {
									$xRDC = mysql_fetch_array($xDocCru);
									$xDATA['terid2xx'] = $xRDC['terid2xx'];
									
									$nAnioDC = $vSysStr['financiero_ano_instalacion_modulo']-1;
								}
							}
						}

						if ($xDATA['terid2xx'] == $gTerId2) {
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx']  = $xDATA['comidxxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx']  = $xDATA['comcodxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx']  = $xDATA['comcscxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x']  = $xDATA['comcsc2x'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx']  = $xDATA['comseqxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx']  = $xDATA['comfecxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx']  = $xDATA['regestxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex']  = $xDATA['regfcrex'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx']  = $xDATA['ccoidxxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx']  = $xDATA['sccidxxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx']  = $xDATA['commovxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlrxx'] += $xDATA['saldoxxx'];
						}
					}
				}
			}
												
			foreach ($mComAgru as $xKey => $cValue) {
				if ($mComAgru[$xKey]['comvlrxx'] != 0) {
					$nInd_Data = count($mData);
					$mData[$nInd_Data] = $mComAgru[$xKey];
				}
			}
		break;
		case "1027":
		
			//Dentro de las cuentas seleccionadas busco solo aquellas que sean por cobrar
			$cCuenta = str_replace("\"", "", $cCuenta);
			$vCuenta = explode(",", $cCuenta);
			$cCuenta = "";
			for($nC=0; $nC<count($vCuenta); $nC++) {
				if ($vCuenta[$nC] != "") {
					$qCxP  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
					$qCxP .= "FROM $cAlfa.fpar0115 ";
					$qCxP .= "WHERE ";
					$qCxP .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$vCuenta[$nC]}\" AND ";
					$qCxP .= "pucdetxx = \"P\" LIMIT 0,1";
					$xCxP  = mysql_query($qCxP,$xConexion01);
					//f_Mensaje(__FILE__,__LINE__,$qCxP."~".mysql_num_rows($xCxP));
					if (mysql_num_rows($xCxP) > 0) {
						$xRCxP = mysql_fetch_array($xCxP);
						$cCuenta .= "\"{$xRCxP['pucidxxx']}\",";
					}
				}
			}
			$cCuenta = substr($cCuenta, 0, -1);
			
			$vTablas = array(); $mData = array(); $mComAgru = array();
			if ($cCuenta != "") { 
				for ($nAnio=$vSysStr['financiero_ano_instalacion_modulo'];$nAnio<=$nAno;$nAnio++) {
					$qDatMov  = "SELECT ";
					$qDatMov .= "comidxxx,";
					$qDatMov .= "comcodxx,";
					$qDatMov .= "comcscxx,";
					$qDatMov .= "comcsc2x,";
					$qDatMov .= "comseqxx,";
					$qDatMov .= "comidcxx, ";
					$qDatMov .= "comcodcx, ";
					$qDatMov .= "comcsccx, ";
					$qDatMov .= "teridxxx, ";
					$qDatMov .= "terid2xx, ";
					$qDatMov .= "pucidxxx,";
					$qDatMov .= "comfecxx,";
					$qDatMov .= "regestxx,";
					$qDatMov .= "regfcrex,";
					$qDatMov .= "ccoidxxx,";
					$qDatMov .= "sccidxxx,";
					$qDatMov .= "commovxx,";
					$qDatMov .= "IF(commovxx = \"D\", comvlrxx, comvlrxx*-1) AS saldoxxx ";
					$qDatMov .= "FROM $cAlfa.fcod$nAnio ";
					$qDatMov .= "WHERE  ";
					$qDatMov .= "pucidxxx IN ($cCuenta)  AND ";
					$qDatMov .= "comfecxx <= \"$dHasta\" AND ";
					$qDatMov .= "teridxxx = \"$gTerId\"  AND ";
					$qDatMov .= "regestxx = \"ACTIVO\" ";
					$qDatMov .= "ORDER BY comidcxx,comcodcx,comcsccx,teridxxx,comfecxx,pucidxxx ";
					$xDatMov = mysql_query($qDatMov,$xConexion01);
					//echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";
					
					while ($xDATA = mysql_fetch_array($xDatMov)) {
						
						if ($xDATA['comidxxx'] != $xDATA['comidcxx'] || 
								$xDATA['comcodxx'] != $xDATA['comcodcx'] || 
								$xDATA['comcscxx'] != $xDATA['comcsccx']) {
							//Se debe buscar el cliente del combropante que se esta cancelando
							for ($nAnioDC=$nAnio;$nAnioDC>=$vSysStr['financiero_ano_instalacion_modulo'];$nAnioDC--) {
								$qDocCru  = "SELECT ";
								$qDocCru .= "terid2xx  ";
								$qDocCru .= "FROM $cAlfa.fcod$nAnioDC ";
								$qDocCru .= "WHERE  ";
								$qDocCru .= "comidxxx = \"{$xDATA['comidcxx']}\" AND ";
								$qDocCru .= "comcodxx = \"{$xDATA['comcodcx']}\" AND ";
								$qDocCru .= "comcscxx = \"{$xDATA['comcsccx']}\" AND ";
								$qDocCru .= "teridxxx = \"{$xDATA['teridxxx']}\" AND ";
								$qDocCru .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND ";
								$qDocCru .= "regestxx = \"ACTIVO\" LIMIT 0,1";
								$xDocCru = mysql_query($qDocCru,$xConexion01);
								//echo "{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"."~".$qDocCru."~".mysql_num_rows($xDocCru)."<br><br>";
								if (mysql_num_rows($xDocCru) > 0) {
									$xRDC = mysql_fetch_array($xDocCru);
									$xDATA['terid2xx'] = $xRDC['terid2xx'];
									
									$nAnioDC = $vSysStr['financiero_ano_instalacion_modulo']-1;
								}
							}
						}

						if ($xDATA['terid2xx'] == $gTerId2) {
							//echo "$gTerId2~{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}~{$xDATA['terid2xx']}~{$xDATA['saldoxxx']}<br>";
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx']  = $xDATA['comidxxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx']  = $xDATA['comcodxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx']  = $xDATA['comcscxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x']  = $xDATA['comcsc2x'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx']  = $xDATA['comseqxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx']  = $xDATA['comfecxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx']  = $xDATA['regestxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex']  = $xDATA['regfcrex'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx']  = $xDATA['ccoidxxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx']  = $xDATA['sccidxxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx']  = $xDATA['commovxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlrxx'] += $xDATA['saldoxxx'];
						}
					}
				}
			}
												
			foreach ($mComAgru as $xKey => $cValue) {
				if ($mComAgru[$xKey]['comvlrxx'] != 0) {
					$nInd_Data = count($mData);
					$mData[$nInd_Data] = $mComAgru[$xKey];
				}
			}
		break;
		case "1054": // 1380250100
			
			if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
				#Busco si el concepto contable es de pago de tributo
				$qPagTri  = "SELECT ";
				$qPagTri .= "ctoidxxx ";
				$qPagTri .= "FROM $cAlfa.fpar0119 ";
				$qPagTri .= "WHERE  ";
				$qPagTri .= "(ctoptaxg = \"SI\" OR ctoptaxl = \"SI\") AND ";
				$qPagTri .= "regestxx = \"ACTIVO\" ";
				$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qPagTri." ~ ".mysql_num_rows($xPagTri));
				$cPagTri = "";
				while ($xRPT = mysql_fetch_array($xPagTri)) {
					$cPagTri .= "{$xRPT['ctoidxxx']},";
				}
				$cPagTri = substr($cPagTri, 0, strlen($cPagTri)-1);
			}
			
			$qFpar117  = "SELECT comidxxx, comcodxx ";
			$qFpar117 .= "FROM $cAlfa.fpar0117 ";
			$qFpar117 .= "WHERE ";
			$qFpar117 .= "comtipxx  = \"RCM\"";
			$xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
			$mRCM = array();
			while ($xRF117 = mysql_fetch_array($xFpar117)) {
				$mRCM[count($mRCM)] = "{$xRF117['comidxxx']}~{$xRF117['comcodxx']}";
			}
			
			$qData  = "SELECT ";
			$qData .= "$cAlfa.fcod$nAno.comidxxx,";
			$qData .= "$cAlfa.fcod$nAno.comcodxx,";
			$qData .= "$cAlfa.fcod$nAno.comcscxx,";
			$qData .= "$cAlfa.fcod$nAno.comcsc2x,";
			$qData .= "$cAlfa.fcod$nAno.comseqxx,";
			$qData .= "$cAlfa.fcod$nAno.comfecxx,";
			$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ccoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.sccidxxx,";
			$qData .= "$cAlfa.fcod$nAno.regestxx,";
			$qData .= "$cAlfa.fcod$nAno.regfcrex,";
			$qData .= "$cAlfa.fcod$nAno.commovxx,";
			//Comprobante cruce dos
			$qData .= "$cAlfa.fcod$nAno.comidc2x,";
			$qData .= "$cAlfa.fcod$nAno.comcodc2,";
			$qData .= "$cAlfa.fcod$nAno.comcscc2,";
			$qData .= "$cAlfa.fcod$nAno.comseqc2,";
			//$qData .= "SUM($cAlfa.fcod$nAno.comvlr02) AS comvlr02,";
			//Concatenando consecutivo Dos, para el caso de los comprobantes de caja menor
			$qData .= "GROUP_CONCAT(CONCAT(comidc2x,\"-\",comcodc2,\"-\",comcscc2,\"-\",comseqc2) SEPARATOR \"~\") AS cajameno,";
			//Sumatoria valores
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr02,$cAlfa.fcod$nAno.comvlr02*-1)) AS comvlr02,";
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr01,$cAlfa.fcod$nAno.comvlr01*-1)) AS comvlr01,";
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
			$qData .= "FROM $cAlfa.fcod$nAno "; 
			$qData .= "WHERE ";
			$qData .= "$cAlfa.fcod$nAno.comidxxx NOT IN (\"F\") AND ";
			$qData .= "$cAlfa.fcod$nAno.terid2xx = \"$gTerId2\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.teridxxx = \"$gTerId\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.pucidxxx  IN ($cCuenta) AND "; 
			if (($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") && $cPagTri <> "") {
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx NOT IN ($cPagTri) AND ";
			}
			$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
			$qData .= "GROUP BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$qData .= "ORDER BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));
			
			$mData = array(); $mComAgru = array();
			while ($xDATA = mysql_fetch_array($xData)) {
				
				//Para los comprobantes de Reembolso de caja Menor trae el iva del Recibo de Caja Menor
				//Busco el comprobante cruce dos en recibos de caja menor
				if ($xDATA['cajameno'] != "" && $xDATA['comvlr02'] == 0 && in_array("{$xDATA['comidxxx']}~{$xDATA['comcodxx']}", $mRCM) == true) {
					$vRecCaja  = explode("~",$xDATA['cajameno']);
					$cRecCaja  = "";
					for ($nRC=0; $nRC<count($vRecCaja); $nRC++) {
						$cRecCaja .= ($vRecCaja[$nRC] != "") ? "\"{$vRecCaja[$nRC]}\"," : "";        		
					}
					$cRecCaja = substr($cRecCaja, 0, strlen($cRecCaja)-1);
					
					if ($cRecCaja != "") {
						$qRecCaja  = "SELECT SUM(IF(commovxx=\"D\",comvlr02,comvlr02*-1)) AS comvlr02 ";
						$qRecCaja .= "FROM $cAlfa.fcme$nAno ";
						$qRecCaja .= "WHERE ";
						$qRecCaja .= "CONCAT(comidxxx,\"-\",comcodxx,\"-\",comcscxx,\"-\",comseqxx) IN ($cRecCaja)";
						$xRecCaja  = f_MySql("SELECT","",$qRecCaja,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qRecCaja." ~ ".mysql_num_rows($xRecCaja));
						if (mysql_num_rows($xRecCaja) > 0) {
							$xRRC = mysql_fetch_array($xRecCaja);
							$xDATA['comvlr02'] = $xRRC['comvlr02'];
						}
					}
				}
				
				if ($xDATA['comvlr01'] == 0) {    
				switch ($cAlfa) {
					case "COLMASXX":
					case "DECOLMASXX":
					case "TECOLMASXX":
					$xDATA['comvlr01'] = $xDATA['comvlrxx'];
					$xDATA['comvlr02'] = 0;
					break;          
					default:
						$nCal = 0;
						if ($xDATA['comidxxx'] == "G" || $xDATA['comidxxx'] == "L") {
								#Busco si el concepto contable es de pago de tributo
								$qPagTri  = "SELECT ";
								$qPagTri .= "ctoptaxg, ";
								$qPagTri .= "ctoptaxl  ";
								$qPagTri .= "FROM $cAlfa.fpar0119 ";
								$qPagTri .= "WHERE  ";
								$qPagTri .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND  ";
								$qPagTri .= "ctoidxxx = \"{$xDATA['ctoidxxx']}\" LIMIT 0,1  ";
								$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
								if (mysql_num_rows($xPagTri) > 0) {
									$xRPT = mysql_fetch_array($xPagTri);
									if($xDATA['comidxxx'] == "G" && $xRPT['ctoptaxg'] == "SI") {
										$xDATA['comvlr01'] = $xDATA['comvlrxx'];
										$xDATA['comvlr02'] = 0;
										$nCal = 1;
									}
									
								if($xDATA['comidxxx'] == "L" && $xRPT['ctoptaxl'] == "SI") {
										$xDATA['comvlr01'] = $xDATA['comvlrxx'];
										$xDATA['comvlr02'] = 0;
										$nCal = 1;
									}
								}
						}
						if ($nCal == 0) {
							switch ($cAlfa) {
								case "GRUPOGLA": case "DEGRUPOGLA": case "TEGRUPOGLA":
								case "ADUACARX": case "DEADUACARX": case "TEADUACARX":
									#Si el comvrl01x es cero calculo la base
									$xDATA['comvlr01'] = ($xDATA['comvlrxx']/1.16);
									$xDATA['comvlr02'] = ($xDATA['comvlr01']*0.16);  
								break;
								default:
									#No se hace nada Se envia lo digitado en la grilla
								break;
							}
						}
					break;
				}
				}
				
				#Validacion para ADUACARGA, se excluye el registro si este tienen valor cero en las siguientes columnas:
				#Impuesto descontable 	
				#IVA resultante por devoluciones en ventas anuladas rescindidas o resueltas (esta siempre tiene valor cero)
				$nIncluir = 0;
				if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
					$nIncluir = ($xDATA['comvlr02'] == 0) ? 1 : 0;	
				}
				
				if ($nIncluir == 0) {	        
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx'] = $xDATA['comidxxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx'] = $xDATA['comcodxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx'] = $xDATA['comcscxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x'] = $xDATA['comcsc2x'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx'] = $xDATA['comseqxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx'] = $xDATA['comfecxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx'] = $xDATA['regestxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex'] = $xDATA['regfcrex'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx'] = $xDATA['ccoidxxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx'] = $xDATA['sccidxxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx'] = $xDATA['commovxx'];
					
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['impdxxxx'] += $xDATA['comvlr02'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ivarxxxx']  = 0;
				}
			}
			
			foreach ($mComAgru as $i => $cValue) {
				$nInd_Data = count($mData);
				$mData[$nInd_Data] = $mComAgru[$i];
			}
		break;
		
		case "5247": // Nuevo 
			
			#Creando tabla temporal de cuentas 2365, 531520 y 2367
			$cFcoc = "fcod".$nAno;
			$cTabFac = fnCadenaAleatoria();
			$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";   
			$xNewTab = mysql_query($qNewTab,$xConexion01); 
					
			//Buscando las cuentas de retencion CREE
			$qRetCree  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
			$qRetCree .= "FROM $cAlfa.fpar0115 ";
			$qRetCree .= "WHERE ";
			$qRetCree .= "pucgruxx LIKE \"23\" AND ";
			$qRetCree .= "pucterxx LIKE \"R\"  AND ";
			$qRetCree .= "pucdesxx LIKE \"%CREE%\" AND ";
			$qRetCree .= "pucdesxx NOT LIKE \"%AUTO%\" AND ";
			$qRetCree .= "regestxx = \"ACTIVO\" ";
			$xRetCree  = f_MySql("SELECT","",$qRetCree,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qRetCree."~".mysql_num_rows($xRetCree));
			$mRetCree = array();
			while ($xRRC = mysql_fetch_array($xRetCree)){
				$mRetCree[count($mRetCree)] = $xRRC['pucidxxx'];
			}
			
			$cReteCree = "";
			for($nRC=0; $nRC<count($mRetCree); $nRC++) {
				$cReteCree .= "$cAlfa.fcod$nAno.pucidxxx LIKE \"{$mRetCree[$nRC]}\" OR ";
			}
			$cReteCree = substr($cReteCree, 0, strlen($cReteCree)-4);
			
			$qFcod  = "SELECT * ";
			$qFcod .= "FROM $cAlfa.fcod$nAno ";
			$qFcod .= "WHERE ";
			$qFcod .= "($cAlfa.fcod$nAno.pucidxxx LIKE \"2408%\" OR ";
			$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,4) IN ($cCueRetFue) OR ";
			$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,6) IN ($cCueImpAsu) OR ";
			$qFcod .= "SUBSTRING($cAlfa.fcod$nAno.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR $cReteCree": "").") AND ";
			$qFcod .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
		
			$qInsert = "INSERT INTO $cAlfa.$cTabFac $qFcod";
			$xInsert = mysql_query($qInsert,$xConexion01);
			#Fin Creando tabla temporal de facturas cabecera
			
			#Trayendo datos del teridxxx
			$qDatExt = "SELECT * ";
			$qDatExt .= "FROM $cAlfa.SIAI0150 ";
			$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"$gTerId\" LIMIT 0,1 ";
			$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
			
			$mCalTer = array();
			if(mysql_num_rows($xDatExt) > 0) {
				$xRDE = mysql_fetch_array($xDatExt);
				if ($xRDE['CLIRECOM']=="SI") {
					$mCalTer[$gTerId] = "COMUN";
				}
				if ($xRDE['CLIRESIM']=="SI") {
					$mCalTer[$gTerId] = "SIMPLIFICADO";
				}
				if ($xRDE['CLIGCXXX']=="SI") {
					$mCalTer[$gTerId] = "CONTRIBUYENTE";
				}
				if ($xRDE['CLINRPXX']=="SI") {
					$mCalTer[$gTerId] = "NORESIDENTE";
				}
			}

			#Trayendo datos del terid2xx
			$qDatExt = "SELECT * ";
			$qDatExt .= "FROM $cAlfa.SIAI0150 ";
			$qDatExt .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"$gTerId2\" LIMIT 0,1 ";
			$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
			
			if(mysql_num_rows($xDatExt) > 0) {
				$xRDE = mysql_fetch_array($xDatExt);
				if ($xRDE['CLIRECOM']=="SI") {
					$mCalTer[$gTerId2] = "COMUN";
				}
				if ($xRDE['CLIRESIM']=="SI") {
					$mCalTer[$gTerId2] = "SIMPLIFICADO";
				}
				if ($xRDE['CLIGCXXX']=="SI") {
					$mCalTer[$gTerId2] = "CONTRIBUYENTE";
				}
				if ($xRDE['CLINRPXX']=="SI") {
					$mCalTer[$gTerId2] = "NORESIDENTE";
				}
			}
			
			#Busco si el concepto contable es de anticipo
			$qCtoAnt  = "SELECT ";
			$qCtoAnt .= "ctoidxxx ";
			$qCtoAnt .= "FROM $cAlfa.fpar0119 ";
			$qCtoAnt .= "WHERE  ";
			$qCtoAnt .= "ctoantxx = \"SI\" AND ";
			$qCtoAnt .= "regestxx = \"ACTIVO\" ";
			$xCtoAnt  = f_MySql("SELECT","",$qCtoAnt,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qCtoAnt." ~ ".mysql_num_rows($xCtoAnt));
			$cCtoAnt = "";
			while ($xRPT = mysql_fetch_array($xCtoAnt)) {
				$cCtoAnt .= "{$xRPT['ctoidxxx']},";
			}
			$cCtoAnt = substr($cCtoAnt, 0, strlen($cCtoAnt)-1);
			
			if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
				#Busco si el concepto contable es de pago de tributo
				$qPagTri  = "SELECT ";
				$qPagTri .= "ctoidxxx ";
				$qPagTri .= "FROM $cAlfa.fpar0119 ";
				$qPagTri .= "WHERE  ";
				$qPagTri .= "(ctoptaxg = \"SI\" OR ctoptaxl = \"SI\") AND ";
				$qPagTri .= "regestxx = \"ACTIVO\" ";
				$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qPagTri." ~ ".mysql_num_rows($xPagTri));
				$cPagTri = "";
				while ($xRPT = mysql_fetch_array($xPagTri)) {
					$cPagTri .= "{$xRPT['ctoidxxx']},";
				}
				$cPagTri = substr($cPagTri, 0, strlen($cPagTri)-1);
				
				//Buscando las L que no son ajustes
				$qCarBa  = "SELECT ";
				$qCarBa .= "CONCAT(comidxxx,\"~\",comcodxx) AS comidxxx ";
				$qCarBa .= "FROM $cAlfa.fpar0117 ";
				$qCarBa .= "WHERE ";
				$qCarBa .= "comidxxx = \"L\" AND ";
				$qCarBa .= "comtipxx != \"AJUSTES\" ";
				$xCarBa = f_MySql("SELECT","",$qCarBa,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qCarBa." ~ ".mysql_num_rows($xCarBa));
				$vCarBa = "";
				while ($xRCB = mysql_fetch_array($xCarBa)) {
					$vCarBa[] = $xRCB['comidxxx'];
				}
			}
			
			$qFpar117  = "SELECT comidxxx, comcodxx ";
			$qFpar117 .= "FROM $cAlfa.fpar0117 ";
			$qFpar117 .= "WHERE ";
			$qFpar117 .= "comtipxx  = \"RCM\"";
			$xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
			$mRCM = array();
			while ($xRF117 = mysql_fetch_array($xFpar117)) {
				$mRCM[count($mRCM)] = "{$xRF117['comidxxx']}~{$xRF117['comcodxx']}";
			}
			
			/**
			* Ticket 22351: Buscando los comprobantes de nota credito para COLMAS, estos comprobantes deben excluirse
			* Ajuste: 2016-03-10 10:14 se incluye cambio para excluir las notas credito para todas las agencias de aduana  
			*/
			$cNotCre  = "";
			switch ($cAlfa) {
				case "COLMASXX":
				case "DECOLMASXX":
				case "TECOLMASXX":
					$cNotCre .= "\"L~044\",";
					$cNotCre .= "\"L~024\",";
					$cNotCre .= "\"L~020\",";
					$cNotCre .= "\"L~016\",";
					$cNotCre .= "\"C~001\",";
					$cNotCre .= "\"C~002\",";
					$cNotCre .= "\"C~003\",";
					$cNotCre .= "\"C~004\",";
				break;          
				default:
					//No hace nada
				break;
			}
			$qNotCre  = "SELECT ";
			$qNotCre .= "CONCAT(comidxxx,\"~\",comcodxx) AS comidxxx ";
			$qNotCre .= "FROM $cAlfa.fpar0117 ";
			$qNotCre .= "WHERE ";
			$qNotCre .= "comidxxx = \"C\" AND ";
			$qNotCre .= "comtipxx != \"AJUSTES\" ";
			$xNotCre = f_MySql("SELECT","",$qNotCre,$xConexion01,"");
			while ($xRDB = mysql_fetch_array($xNotCre)) {
				$cNotCre .= "\"{$xRDB['comidxxx']}\",";
			}
			$cNotCre = substr($cNotCre,0,strlen($cNotCre)-1); 
			
			$qData  = "SELECT ";
			$qData .= "$cAlfa.fcod$nAno.comidxxx,";
			$qData .= "$cAlfa.fcod$nAno.comcodxx,";
			$qData .= "$cAlfa.fcod$nAno.comcscxx,";
			$qData .= "$cAlfa.fcod$nAno.comcsc2x,";
			$qData .= "$cAlfa.fcod$nAno.comseqxx,";
			$qData .= "$cAlfa.fcod$nAno.comfecxx,";
			$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ccoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.sccidxxx,";
			$qData .= "$cAlfa.fcod$nAno.teridxxx,";
			$qData .= "$cAlfa.fcod$nAno.terid2xx,";
			$qData .= "$cAlfa.fcod$nAno.regestxx,";
			$qData .= "$cAlfa.fcod$nAno.regfcrex,";
			$qData .= "$cAlfa.fcod$nAno.commovxx,";
			//Comprobante cruce dos
			$qData .= "$cAlfa.fcod$nAno.comidc2x,";
			$qData .= "$cAlfa.fcod$nAno.comcodc2,";
			$qData .= "$cAlfa.fcod$nAno.comcscc2,";
			$qData .= "$cAlfa.fcod$nAno.comseqc2,";
			//Concatenando consecutivo Dos, para el caso de los comprobantes de caja menor
			$qData .= "GROUP_CONCAT(CONCAT(comidc2x,\"-\",comcodc2,\"-\",comcscc2,\"-\",comseqc2) SEPARATOR \"~\") AS cajameno,";
			/**
			* Sumatoria valores
			* Para COLMAS, GLA y ADUACARGA se mantiene igual, ya que ellos tienen su propia logica, 
			* para las demas agencias 
			* si la base es cero y el iva es cero, la base debe ser igual al valor del comprobante
			*/
			switch ($cAlfa) {
				case "COLMASXX": case "DECOLMASXX": case "TECOLMASXX":
				case "GRUPOGLA": case "DEGRUPOGLA": case "TEGRUPOGLA":
				case "ADUACARX": case "DEADUACARX": case "TEADUACARX":
					$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr02,$cAlfa.fcod$nAno.comvlr02*-1)) AS comvlr02,";
					$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr01,$cAlfa.fcod$nAno.comvlr01*-1)) AS comvlr01,";
					$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
				break;          
				default:
					$nAsiBas = "IF($cAlfa.fcod$nAno.comvlr01 = 0 AND $cAlfa.fcod$nAno.comvlr02 = 0, $cAlfa.fcod$nAno.comvlrxx, $cAlfa.fcod$nAno.comvlr01)";
					$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr02,$cAlfa.fcod$nAno.comvlr02*-1)) AS comvlr02,";
					$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$nAsiBas,$nAsiBas*-1)) AS comvlr01,";
					$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
				break;
			}
			$qData .= "FROM $cAlfa.fcod$nAno "; 
			$qData .= "LEFT JOIN $cAlfa.fcoc$nAno ON $cAlfa.fcod$nAno.comidxxx = $cAlfa.fcoc$nAno.comidxxx AND $cAlfa.fcod$nAno.comcodxx = $cAlfa.fcoc$nAno.comcodxx AND $cAlfa.fcod$nAno.comcscxx = $cAlfa.fcoc$nAno.comcscxx AND $cAlfa.fcod$nAno.comcsc2x = $cAlfa.fcoc$nAno.comcsc2x ";
			$qData .= "WHERE ";
			$qData .= "$cAlfa.fcod$nAno.comidxxx != \"F\"          AND ";
			$qData .= "$cAlfa.fcod$nAno.terid2xx = \"$gTerId2\"    AND "; 
			$qData .= "$cAlfa.fcod$nAno.teridxxx = \"$gTerId\"     AND "; 
			$qData .= "$cAlfa.fcoc$nAno.comintpa != \"SI\"         AND ";
			$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta)     AND ";
			$qData .= "$cAlfa.fcod$nAno.ctoidxxx NOT IN ($cCtoAnt) AND ";
			if (($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") && $cPagTri <> "") {
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx NOT IN ($cPagTri) AND ";
			}
			/**
			* Ticket 22351: Buscando los comprobantes de nota credito para COLMAS, estos comprobantes deben excluirse
			* Ajuste: 2016-03-10 10:14 se incluye cambio para excluir las notas credito para todas las agencias de aduana 
			*/
			if ($cNotCre != "") {
				$qData .= "CONCAT($cAlfa.fcod$nAno.comidxxx,\"~\",$cAlfa.fcod$nAno.comcodxx) NOT IN ($cNotCre) AND ";
			} 
			$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND "; 
			/*$qData .= "$cAlfa.fcod$nAno.comidxxx = \"N\" AND ";
			$qData .= "$cAlfa.fcod$nAno.comcodxx = \"013\" AND ";
			$qData .= "$cAlfa.fcod$nAno.comcscxx = \"2013110039\" AND ";*/
			$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
			$qData .= "GROUP BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$qData .= "ORDER BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));
			
			$mData = array(); $mComAgru = array();
			while ($xDATA = mysql_fetch_array($xData)) {
				$nPrac = 0; $nAsum = 0; $nComun = 0; $nSimpl = 0; $nDom = 0; $nCreeAsum = 0; $nCreePrac = 0;
				
				#Calculando valores:
				#Retencion en la fuente practicada renta
				#Retencion en la fuente asumida renta
				#Retencion en la fuente practicada iva regimen comun
				#Retencion en la fuente asumida iva regimen simp.
				#Retencion en la fuente practicada iva no domiciliados
				#Traigo las cuantas que empiezan por 2365,531520,2367 para le comprobante
				$qFcod  = "SELECT ";
				$qFcod .= "$cAlfa.$cTabFac.*,";
				#para las cuentas 2365 y 2367 los creditos suman, los debitos restan
				$qFcod .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx,$cAlfa.$cTabFac.comvlrxx*-1) AS comvlrsu,";
				#para la cuenta 531520 los creditos restan, los debitos suman
				$qFcod .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx*-1,$cAlfa.$cTabFac.comvlrxx) AS comvlrre ";
				$qFcod .= "FROM $cAlfa.$cTabFac ";
				$qFcod .= "WHERE ";
				$qFcod .= "$cAlfa.$cTabFac.comidxxx = \"{$xDATA['comidxxx']}\" AND ";
				$qFcod .= "$cAlfa.$cTabFac.comcodxx = \"{$xDATA['comcodxx']}\" AND ";
				$qFcod .= "$cAlfa.$cTabFac.comcscxx = \"{$xDATA['comcscxx']}\" AND ";
				switch ($cAlfa) {
					case "ADUACARX":
					case "TEADUACARX":
					case "DEADUACARX":
						//Las validaciones de los terceros se hacen en el while
						$qFcod .= "(SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetFue) OR SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,6) IN ($cCueImpAsu) OR SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR ".str_replace("fcod$nAno", $cTabFac, $cReteCree) : "").") AND ";
					break;
					default:
						$qFcod .= "(($cAlfa.$cTabFac.teridxxx = \"$gTerId2\" AND $cAlfa.$cTabFac.terid2xx = \"$gTerId\") OR ($cAlfa.$cTabFac.teridxxx = \"$gTerId\" AND $cAlfa.$cTabFac.terid2xx =  \"$gTerId2\")) AND ";
						$qFcod .= "(SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetFue) OR ";
						$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,6) IN ($cCueImpAsu) OR ";
						$qFcod .= "SUBSTRING($cAlfa.$cTabFac.pucidxxx,1,4) IN ($cCueRetIva)".(($cReteCree != "") ? " OR ".str_replace("fcod$nAno", $cTabFac, $cReteCree) : "").") AND ";	
					break;
				}
				$qFcod .= "$cAlfa.$cTabFac.regestxx = \"ACTIVO\" ";
				$xFcod = mysql_query($qFcod,$xConexion01);
				
				if (mysql_num_rows($xFcod) > 0) {
					while($xRF = mysql_fetch_array($xFcod)) {
						$nIncRect = 0;
						//Validaciones de los terceros para ADUACARGA
						if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
						$nIncRect = 1;	       	 	
						//Primera condicion: que los terceros del reistro analizado esten contenidos en los terceros de la retencion
						if (($xRF['teridxxx'] == $xDATA['terid2xx'] && $xRF['terid2xx'] == $xDATA['teridxxx']) ||
								($xRF['teridxxx'] == $xDATA['teridxxx'] && $xRF['terid2xx'] == $xDATA['terid2xx'])) {
							$nIncRect = 0;
						} else { 
							//si el comprobante es una G y el terid2xx del reistro analizado este en el teridxxx de la retencion,
							//y el teridxxx del registro analizado este en el subcentro de costo de la retencion
							//y la cuenta de retencion empieza por 2365 o 2367
							if (($xRF['comidxxx'] == "G" || ($xRF['comidxxx'] == "L" && in_array($xRF['comidxxx']."~".$xRF['comcodxx'], $vCarBa) == true)) && 
									$xRF['teridxxx'] == $xDATA['terid2xx'] && $xRF['sccidxxx'] == $xDATA['teridxxx'] &&		
									(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue) || in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva))) {
								$nIncRect = 0;		
							} else {
								//Se busca si el subcentro de costo de la retencion es un DO, se trae el importador 
								$qDatDo = "SELECT cliidxxx FROM $cAlfa.sys00121 WHERE docidxxx = \"{$xRF['sccidxxx'] }\" LIMIT 0,1";
								$xDatDo  = f_MySql("SELECT","",$qDatDo,$xConexion01,"");
								//f_Mensaje(__FILE__,__LINE__,$qDatDo." ~ ".mysql_num_rows($xDatDo));

								//si el comprobante es una G y el terid2xx del reistro analizado este en el teridxxx de la retencion,
								//y el teridxxx del registro analizado es el importador del DO
								//y la cuenta empiece por 2365 o 2367
								if (mysql_num_rows($xDatDo) > 0) {
									$xRDD = mysql_fetch_array($xDatDo);
									if (($xRF['comidxxx'] == "G" || ($xRF['comidxxx'] == "L" && in_array($xRF['comidxxx']."~".$xRF['comcodxx'], $vCarBa) == true)) && 
											$xRF['teridxxx'] == $xDATA['terid2xx'] && $xDATA['teridxxx'] == $xRDD['cliidxxx'] &&		
											(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue) || in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva))) {
										$nIncRect = 0;		
									}
								}
							}
						}
					} 

						if ($nIncRect == 0) {
							
						//Verifico si no es una cuenta de retencion Cree
						if (in_array($xRF['pucidxxx'], $mRetCree) == true) {
							$nCreePrac += $xRF['comvlrsu'];
						}else{
							//Verifico si es un iva practicado
							if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetFue)) {
								$nPrac += $xRF['comvlrsu'];
							}
							//Verifico si es un iva asumido
							if(in_array(substr($xRF['pucidxxx'],0,6), $vCueImpAsu)) {
								$nAsum += $xRF['comvlrre'];
							}
							//Verifico el tipo de rentencion
							if(in_array(substr($xRF['pucidxxx'],0,4), $vCueRetIva)) {
								if($xRF['tertipxx'] == "CLIPROCX"){
									$cCliId = $xRF['teridxxx'];
								} else {
									$cCliId = $xRF['terid2xx'];
								}
								switch ($mCalTer[$cCliId]) {
									case "CONTRIBUYENTE":
									case "COMUN":
									$nComun += $xRF['comvlrsu'];
									break;
									case "SIMPLIFICADO":
									$nSimpl += $xRF['comvlrsu'];
									break;
									case "NORESIDENTE":
									$nDom += $xRF['comvlrsu'];
									break;
								}
							}
							}
						}
					}
				}
				
				//Para los comprobantes de Reembolso de caja Menor trae el iva del Recibo de Caja Menor
				//Busco el comprobante cruce dos en recibos de caja menor
				if ($xDATA['cajameno'] != "" && $xDATA['comvlr02'] == 0 && in_array("{$xDATA['comidxxx']}~{$xDATA['comcodxx']}", $mRCM) == true) {
					$vRecCaja  = explode("~",$xDATA['cajameno']);
					$cRecCaja  = "";
					for ($nRC=0; $nRC<count($vRecCaja); $nRC++) {
						$cRecCaja .= ($vRecCaja[$nRC] != "") ? "\"{$vRecCaja[$nRC]}\"," : "";        		
					}
					$cRecCaja = substr($cRecCaja, 0, strlen($cRecCaja)-1);
					
					if ($cRecCaja != "") {
						$qRecCaja  = "SELECT SUM(IF(commovxx=\"D\",comvlr02,comvlr02*-1)) AS comvlr02 ";
						$qRecCaja .= "FROM $cAlfa.fcme$nAno ";
						$qRecCaja .= "WHERE ";
						$qRecCaja .= "CONCAT(comidxxx,\"-\",comcodxx,\"-\",comcscxx,\"-\",comseqxx) IN ($cRecCaja)";
						$xRecCaja  = f_MySql("SELECT","",$qRecCaja,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qRecCaja." ~ ".mysql_num_rows($xRecCaja));
						if (mysql_num_rows($xRecCaja) > 0) {
							$xRRC = mysql_fetch_array($xRecCaja);
							$xDATA['comvlr02'] = $xRRC['comvlr02'];
						}
					}
				}
				
			if ($xDATA['comvlr01'] == 0) {    
				switch ($cAlfa) {
					case "COLMASXX":
					case "DECOLMASXX":
					case "TECOLMASXX":
					$xDATA['comvlr01'] = $xDATA['comvlrxx'];
					$xDATA['comvlr02'] = 0;
					break;          
					default:
						$nCal = 0;
						if ($xDATA['comidxxx'] == "G" || $xDATA['comidxxx'] == "L") {
								#Busco si el concepto contable es de pago de tributo
								$qPagTri  = "SELECT ";
								$qPagTri .= "ctoptaxg, ";
								$qPagTri .= "ctoptaxl  ";
								$qPagTri .= "FROM $cAlfa.fpar0119 ";
								$qPagTri .= "WHERE  ";
								$qPagTri .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND  ";
								$qPagTri .= "ctoidxxx = \"{$xDATA['ctoidxxx']}\" LIMIT 0,1  ";
								$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
								//f_Mensaje(__FILE__,__LINE__,$qPagTri." ~ ".mysql_num_rows($xPagTri));
								if (mysql_num_rows($xPagTri) > 0) {
									$xRPT = mysql_fetch_array($xPagTri);
									if($xDATA['comidxxx'] == "G" && $xRPT['ctoptaxg'] == "SI") {
										$xDATA['comvlr01'] = $xDATA['comvlrxx'];
										$xDATA['comvlr02'] = 0;
										$nCal = 1;
									}
									
								if($xDATA['comidxxx'] == "L" && $xRPT['ctoptaxl'] == "SI") {
										$xDATA['comvlr01'] = $xDATA['comvlrxx'];
										$xDATA['comvlr02'] = 0;
										$nCal = 1;
									}
								}
						}
						if ($nCal == 0) {
							switch ($cAlfa) {
								case "GRUPOGLA": case "DEGRUPOGLA": case "TEGRUPOGLA":
								case "ADUACARX": case "DEADUACARX": case "TEADUACARX":
									#Si el comvrl01x es cero calculo la base
									$xDATA['comvlr01'] = ($xDATA['comvlrxx']/1.16);
									$xDATA['comvlr02'] = ($xDATA['comvlr01']*0.16);  
								break;
								default:
									#Si el valor de la base e iva es cero, en la base se envia el valor del comprobante
									$xDATA['comvlr01'] = ($xDATA['comvlr01'] == 0 && $xDATA['comvlr02'] == 0) ? $xDATA['comvlrxx'] : $xDATA['comvlr01'];
								break;
							}
						}
					break;
				}
				}
				
				#Se incluye para ADUACARGA que en la columna IVA mayor valor del costo o gasto sea siempre de valor cero
				if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
					$xDATA['comvlr02'] = 0;
				}
				
				#Validacion para ADUACARGA, se excluye el registro si este tienen valor cero en las siguientes columnas:
				#Pago o abono en cta 	
				#IVA mayor valor del costo o gasto 	
				#Retencion en la fuente practicada renta 	
				#Retencion en la fuente asumida renta 	
				#Retencion en la fuente practicada iva regimen comun 	
				#Retencion en la fuente asumida iva regimen simp. 	
				#Retencion en la fuente practicada iva no domiciliados
				$nIncluir = 0;
				if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
					$nIncluir = ($xDATA['comvlr01'] == 0 && $xDATA['comvlr02'] == 0 && $nPrac == 0  && $nAsum == 0 && $nComun == 0 && $nSimpl == 0 && $nDom == 0) ? 1 : 0;	
				}
				
				if ($nIncluir == 0) {	        
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx'] = $xDATA['comidxxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx'] = $xDATA['comcodxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx'] = $xDATA['comcscxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x'] = $xDATA['comcsc2x'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx'] = $xDATA['comseqxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx'] = $xDATA['comfecxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx'] = $xDATA['regestxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex'] = $xDATA['regfcrex'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx'] = $xDATA['ccoidxxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx'] = $xDATA['sccidxxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx'] = $xDATA['commovxx'];
					
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlr01'] += $xDATA['comvlr01'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlr02'] += $xDATA['comvlr02'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['pracxxxx'] += $nPrac;
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['asumxxxx'] += $nAsum;
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comunxxx'] += $nComun;
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['simplxxx'] += $nSimpl;
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ndomxxxx'] += $nDom;
				}
			}
			
			foreach ($mComAgru as $i => $cValue) {
				$nInd_Data = count($mData);
				$mData[$nInd_Data] = $mComAgru[$i];
			}
			
		break;

		case "5248": // Nuevo
			
			## Busco detalle del comprobante
			$qData  = "SELECT ";			
			$qData .= "$cAlfa.fcod$nAno.comidxxx,";
			$qData .= "$cAlfa.fcod$nAno.comcodxx,";
			$qData .= "$cAlfa.fcod$nAno.comcscxx,";
			$qData .= "$cAlfa.fcod$nAno.comcsc2x,";
			$qData .= "$cAlfa.fcod$nAno.comseqxx,";
			$qData .= "$cAlfa.fcod$nAno.comfecxx,";
			$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ccoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.sccidxxx,";
			$qData .= "$cAlfa.fcod$nAno.regestxx,";
			$qData .= "$cAlfa.fcod$nAno.regfcrex,";
			$qData .= "$cAlfa.fcod$nAno.commovxx,";
			$qData .= "$cAlfa.fcod$nAno.teridxxx,";
			$qData .= "$cAlfa.fcod$nAno.comvlrxx ";
			$qData .= "FROM $cAlfa.fcod$nAno ";
			$qData .= "WHERE ";
			$qData .= "$cAlfa.fcod$nAno.terid2xx = \"$gTerId\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta) AND ";
			$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
			$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
			$qData .= "GROUP BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$qData .= "ORDER BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));
		
			$mData = array(); $mComAgru = array();
			while ($xDATA = mysql_fetch_array($xData)) {
				
				$nIpro = 0; $nDev = 0; 
			
				if($xDATA['commovxx'] == "D"){
					$nIpro += $xDATA['comvlrxx'];
				}elseif($xDATA['commovxx'] == "C"){
					$nDev += $xDATA['comvlrxx'];
				}
				
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx'] = $xDATA['comidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx'] = $xDATA['comcodxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx'] = $xDATA['comcscxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x'] = $xDATA['comcsc2x'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx'] = $xDATA['comseqxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx'] = $xDATA['comfecxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx'] = $xDATA['regestxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex'] = $xDATA['regfcrex'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx'] = $xDATA['ccoidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx'] = $xDATA['sccidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx'] = $xDATA['commovxx'];
				
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['iproxxxx'] += $nIpro;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['devxxxxx'] += $nDev;
				
			}
			
			foreach ($mComAgru as $i => $cValue) {
				$nInd_Data = count($mData);
				$mData[$nInd_Data] = $mComAgru[$i];
			}
			
		break;

		case "5249": // Nuevo
			
			if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
				#Busco si el concepto contable es de pago de tributo
				$qPagTri  = "SELECT ";
				$qPagTri .= "ctoidxxx ";
				$qPagTri .= "FROM $cAlfa.fpar0119 ";
				$qPagTri .= "WHERE  ";
				$qPagTri .= "(ctoptaxg = \"SI\" OR ctoptaxl = \"SI\") AND ";
				$qPagTri .= "regestxx = \"ACTIVO\" ";
				$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qPagTri." ~ ".mysql_num_rows($xPagTri));
				$cPagTri = "";
				while ($xRPT = mysql_fetch_array($xPagTri)) {
					$cPagTri .= "{$xRPT['ctoidxxx']},";
				}
				$cPagTri = substr($cPagTri, 0, strlen($cPagTri)-1);
			}
			
			$qFpar117  = "SELECT comidxxx, comcodxx ";
			$qFpar117 .= "FROM $cAlfa.fpar0117 ";
			$qFpar117 .= "WHERE ";
			$qFpar117 .= "comtipxx  = \"RCM\"";
			$xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
			$mRCM = array();
			while ($xRF117 = mysql_fetch_array($xFpar117)) {
				$mRCM[count($mRCM)] = "{$xRF117['comidxxx']}~{$xRF117['comcodxx']}";
			}
			
			$qData  = "SELECT ";
			$qData .= "$cAlfa.fcod$nAno.comidxxx,";
			$qData .= "$cAlfa.fcod$nAno.comcodxx,";
			$qData .= "$cAlfa.fcod$nAno.comcscxx,";
			$qData .= "$cAlfa.fcod$nAno.comcsc2x,";
			$qData .= "$cAlfa.fcod$nAno.comseqxx,";
			$qData .= "$cAlfa.fcod$nAno.comfecxx,";
			$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ccoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.sccidxxx,";
			$qData .= "$cAlfa.fcod$nAno.regestxx,";
			$qData .= "$cAlfa.fcod$nAno.regfcrex,";
			$qData .= "$cAlfa.fcod$nAno.commovxx,";
			//Comprobante cruce dos
			$qData .= "$cAlfa.fcod$nAno.comidc2x,";
			$qData .= "$cAlfa.fcod$nAno.comcodc2,";
			$qData .= "$cAlfa.fcod$nAno.comcscc2,";
			$qData .= "$cAlfa.fcod$nAno.comseqc2,";
			//$qData .= "SUM($cAlfa.fcod$nAno.comvlr02) AS comvlr02,";
			//Concatenando consecutivo Dos, para el caso de los comprobantes de caja menor
			$qData .= "GROUP_CONCAT(CONCAT(comidc2x,\"-\",comcodc2,\"-\",comcscc2,\"-\",comseqc2) SEPARATOR \"~\") AS cajameno,";
			//Sumatoria valores
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr02,$cAlfa.fcod$nAno.comvlr02*-1)) AS comvlr02,";
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlr01,$cAlfa.fcod$nAno.comvlr01*-1)) AS comvlr01,";
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
			$qData .= "FROM $cAlfa.fcod$nAno "; 
			$qData .= "WHERE ";
			$qData .= "$cAlfa.fcod$nAno.comidxxx NOT IN (\"F\") AND ";
			$qData .= "$cAlfa.fcod$nAno.terid2xx = \"$gTerId2\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.teridxxx = \"$gTerId\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.pucidxxx  IN ($cCuenta) AND "; 
			if (($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") && $cPagTri <> "") {
				$qData .= "$cAlfa.fcod$nAno.ctoidxxx NOT IN ($cPagTri) AND ";
			}
			$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
			$qData .= "GROUP BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$qData .= "ORDER BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));
			
			$mData = array(); $mComAgru = array();
			while ($xDATA = mysql_fetch_array($xData)) {
				
				//Para los comprobantes de Reembolso de caja Menor trae el iva del Recibo de Caja Menor
				//Busco el comprobante cruce dos en recibos de caja menor
				if ($xDATA['cajameno'] != "" && $xDATA['comvlr02'] == 0 && in_array("{$xDATA['comidxxx']}~{$xDATA['comcodxx']}", $mRCM) == true) {
					$vRecCaja  = explode("~",$xDATA['cajameno']);
					$cRecCaja  = "";
					for ($nRC=0; $nRC<count($vRecCaja); $nRC++) {
						$cRecCaja .= ($vRecCaja[$nRC] != "") ? "\"{$vRecCaja[$nRC]}\"," : "";        		
					}
					$cRecCaja = substr($cRecCaja, 0, strlen($cRecCaja)-1);
					
					if ($cRecCaja != "") {
						$qRecCaja  = "SELECT SUM(IF(commovxx=\"D\",comvlr02,comvlr02*-1)) AS comvlr02 ";
						$qRecCaja .= "FROM $cAlfa.fcme$nAno ";
						$qRecCaja .= "WHERE ";
						$qRecCaja .= "CONCAT(comidxxx,\"-\",comcodxx,\"-\",comcscxx,\"-\",comseqxx) IN ($cRecCaja)";
						$xRecCaja  = f_MySql("SELECT","",$qRecCaja,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qRecCaja." ~ ".mysql_num_rows($xRecCaja));
						if (mysql_num_rows($xRecCaja) > 0) {
							$xRRC = mysql_fetch_array($xRecCaja);
							$xDATA['comvlr02'] = $xRRC['comvlr02'];
						}
					}
				}
				
				if ($xDATA['comvlr01'] == 0) {    
				switch ($cAlfa) {
					case "COLMASXX":
					case "DECOLMASXX":
					case "TECOLMASXX":
					$xDATA['comvlr01'] = $xDATA['comvlrxx'];
					$xDATA['comvlr02'] = 0;
					break;          
					default:
						$nCal = 0;
						if ($xDATA['comidxxx'] == "G" || $xDATA['comidxxx'] == "L") {
								#Busco si el concepto contable es de pago de tributo
								$qPagTri  = "SELECT ";
								$qPagTri .= "ctoptaxg, ";
								$qPagTri .= "ctoptaxl  ";
								$qPagTri .= "FROM $cAlfa.fpar0119 ";
								$qPagTri .= "WHERE  ";
								$qPagTri .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND  ";
								$qPagTri .= "ctoidxxx = \"{$xDATA['ctoidxxx']}\" LIMIT 0,1  ";
								$xPagTri  = f_MySql("SELECT","",$qPagTri,$xConexion01,"");
								if (mysql_num_rows($xPagTri) > 0) {
									$xRPT = mysql_fetch_array($xPagTri);
									if($xDATA['comidxxx'] == "G" && $xRPT['ctoptaxg'] == "SI") {
										$xDATA['comvlr01'] = $xDATA['comvlrxx'];
										$xDATA['comvlr02'] = 0;
										$nCal = 1;
									}
									
								if($xDATA['comidxxx'] == "L" && $xRPT['ctoptaxl'] == "SI") {
										$xDATA['comvlr01'] = $xDATA['comvlrxx'];
										$xDATA['comvlr02'] = 0;
										$nCal = 1;
									}
								}
						}
						if ($nCal == 0) {
							switch ($cAlfa) {
								case "GRUPOGLA": case "DEGRUPOGLA": case "TEGRUPOGLA":
								case "ADUACARX": case "DEADUACARX": case "TEADUACARX":
									#Si el comvrl01x es cero calculo la base
									$xDATA['comvlr01'] = ($xDATA['comvlrxx']/1.16);
									$xDATA['comvlr02'] = ($xDATA['comvlr01']*0.16);  
								break;
								default:
									#No se hace nada Se envia lo digitado en la grilla
								break;
							}
						}
					break;
				}
				}
				
				#Validacion para ADUACARGA, se excluye el registro si este tienen valor cero en las siguientes columnas:
				#Impuesto descontable 	
				#IVA resultante por devoluciones en ventas anuladas rescindidas o resueltas (esta siempre tiene valor cero)
				$nIncluir = 0;
				if ($cAlfa == "ADUACARX" || $cAlfa == "TEADUACARX" || $cAlfa == "DEADUACARX") {
					$nIncluir = ($xDATA['comvlr02'] == 0) ? 1 : 0;	
				}
				
				if ($nIncluir == 0) {	        
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx'] = $xDATA['comidxxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx'] = $xDATA['comcodxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx'] = $xDATA['comcscxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x'] = $xDATA['comcsc2x'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx'] = $xDATA['comseqxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx'] = $xDATA['comfecxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx'] = $xDATA['regestxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex'] = $xDATA['regfcrex'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx'] = $xDATA['ccoidxxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx'] = $xDATA['sccidxxx'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx'] = $xDATA['commovxx'];
					
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['impdxxxx'] += $xDATA['comvlr02'];
					$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ivarxxxx']  = 0;
				}
			}
			
			foreach ($mComAgru as $i => $cValue) {
				$nInd_Data = count($mData);
				$mData[$nInd_Data] = $mComAgru[$i];
			}
		break;

		case "5250": // Nuevo

			$mCtoPCC = array(); // Arreglo cuenta concepto
			## Busco detalle del comprobante
			$qData  = "SELECT ";			
			$qData .= "$cAlfa.fcod$nAno.comidxxx,";
			$qData .= "$cAlfa.fcod$nAno.comcodxx,";
			$qData .= "$cAlfa.fcod$nAno.comcscxx,";
			$qData .= "$cAlfa.fcod$nAno.comcsc2x,";
			$qData .= "$cAlfa.fcod$nAno.comseqxx,";
			$qData .= "$cAlfa.fcod$nAno.comfecxx,";
			$qData .= "$cAlfa.fcod$nAno.pucidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ctoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.ccoidxxx,";
			$qData .= "$cAlfa.fcod$nAno.sccidxxx,";
			$qData .= "$cAlfa.fcod$nAno.regestxx,";
			$qData .= "$cAlfa.fcod$nAno.regfcrex,";
			$qData .= "$cAlfa.fcod$nAno.commovxx,";
			$qData .= "$cAlfa.fcod$nAno.teridxxx,";
			$qData .= "SUM(IF($cAlfa.fcod$nAno.commovxx=\"D\",$cAlfa.fcod$nAno.comvlrxx,$cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
			// $qData .= "$cAlfa.fcod$nAno.comvlrxx ";
			$qData .= "FROM $cAlfa.fcod$nAno ";
			$qData .= "WHERE ";
			$qData .= "$cAlfa.fcod$nAno.terid2xx = \"$gTerId\" AND "; 
			$qData .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuenta) AND ";
			$qData .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
			$qData .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
			$qData .= "GROUP BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$qData .= "ORDER BY $cAlfa.fcod$nAno.comidxxx,$cAlfa.fcod$nAno.comcodxx,$cAlfa.fcod$nAno.comcscxx ";
			$xData  = f_MySql("SELECT","",$qData,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qData." ~ ".mysql_num_rows($xData));

			## Busco los PCC en la Tabla fpar0121
			$qCtoP121  = "SELECT ";
			$qCtoP121 .= "$cAlfa.fpar0121.pucidxxx, $cAlfa.fpar0121.ctoidxxx ";
			$qCtoP121 .= "FROM $cAlfa.fpar0121 ";
			$qCtoP121 .= "WHERE $cAlfa.fpar0121.regestxx = \"ACTIVO\"";
			$xCtoP121  = f_MySql("SELECT","",$qCtoP121,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qCtoP121." ~ ".mysql_num_rows($xCtoP121));

			while($xRCP121 =  mysql_fetch_array($xCtoP121)){
				$mCtoPCC[count($mCtoPCC)] = "{$xRCP121['pucidxxx']}~{$xRCP121['ctoidxxx']}";
			}

			## Busco los PCC en la Tabla fpar0119
			$qCtoP119  = "SELECT ";
			$qCtoP119 .= "$cAlfa.fpar0119.pucidxxx, $cAlfa.fpar0119.ctoidxxx, $cAlfa.fpar0119.ctopccxx ";
			$qCtoP119 .= "FROM $cAlfa.fpar0119 ";
			$qCtoP119 .= "WHERE $cAlfa.fpar0119.ctopccxx = \"SI\" AND ";
			$qCtoP119 .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\"";
			$xCtoP119  = f_MySql("SELECT","",$qCtoP119,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qCtoP119." ~ ".mysql_num_rows($xCtoP119));
			
			while($xRCP119 =  mysql_fetch_array($xCtoP119)){
				$mCtoPCC[count($mCtoPCC)] = "{$xRCP119['pucidxxx']}~{$xRCP119['ctoidxxx']}";
			}

		
			$mData = array(); $mComAgru = array();
			while ($xDATA = mysql_fetch_array($xData)) {
				
				// $nImpGen = 0; $nIvaDev = 0; $nImpCon  = 0;
			
				if(in_array("{$xDATA['pucidxxx']}~{$xDATA['ctoidxxx']}",$mCtoPCC)){
					
					if($xDATA['commovxx'] == "C"){
						$nImpGen += $xDATA['comvlr02']; 
					}elseif($xDATA['commovxx'] == "D"){
						$nIvaDev += $xDATA['comvlr02'];
					}
				
					if(substr($xDATA['pucidxxx'],0,4) == "2464"){
						$nImpCon = $xDATA['comvlrxx'];
					}
				
				}
				
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx'] = $xDATA['comidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx'] = $xDATA['comcodxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx'] = $xDATA['comcscxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x'] = $xDATA['comcsc2x'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx'] = $xDATA['comseqxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx'] = $xDATA['comfecxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx'] = $xDATA['regestxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex'] = $xDATA['regfcrex'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx'] = $xDATA['ccoidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx'] = $xDATA['sccidxxx'];
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx'] = $xDATA['commovxx'];
				
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['impgenxx'] += $nImpGen;
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ivadexxx'] += $nIvaDev; 
				$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['impconxx'] += $nImpCon; 
				
			}
			
			foreach ($mComAgru as $i => $cValue) {
				$nInd_Data = count($mData);
				$mData[$nInd_Data] = $mComAgru[$i];
			}
			
		break;

		case "5251": // Nuevo
		
			//Dentro de las cuentas seleccionadas busco solo aquellas que sean por cobrar
			$cCuenta = str_replace("\"", "", $cCuenta);
			$vCuenta = explode(",", $cCuenta);
			$cCuenta = "";
			for($nC=0; $nC<count($vCuenta); $nC++) {
				if ($vCuenta[$nC] != "") {
					$qCxC  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
					$qCxC .= "FROM $cAlfa.fpar0115 ";
					$qCxC .= "WHERE ";
					$qCxC .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$vCuenta[$nC]}\" AND ";
					$qCxC .= "pucdetxx = \"C\" LIMIT 0,1";
					$xCxC  = mysql_query($qCxC,$xConexion01);
					//f_Mensaje(__FILE__,__LINE__,$qCxC."~".mysql_num_rows($xCxC));
					if (mysql_num_rows($xCxC) > 0) {
						$xRCxC = mysql_fetch_array($xCxC);
						$cCuenta .= "\"{$xRCxC['pucidxxx']}\",";
					}
				}
			}
			$cCuenta = substr($cCuenta, 0, -1);
			
			$vTablas = array(); $mData = array(); $mComAgru = array();
			if ($cCuenta != "") { 
				for ($nAnio=$vSysStr['financiero_ano_instalacion_modulo'];$nAnio<=$nAno;$nAnio++) {
					$qDatMov  = "SELECT ";
					$qDatMov .= "comidxxx,";
					$qDatMov .= "comcodxx,";
					$qDatMov .= "comcscxx,";
					$qDatMov .= "comcsc2x,";
					$qDatMov .= "comseqxx,";
					$qDatMov .= "comidcxx,";
					$qDatMov .= "comcodcx,";
					$qDatMov .= "comcsccx,";
					$qDatMov .= "pucidxxx,";
					$qDatMov .= "teridxxx,";
					$qDatMov .= "terid2xx,";
					$qDatMov .= "comfecxx,";
					$qDatMov .= "regestxx,";
					$qDatMov .= "regfcrex,";
					$qDatMov .= "ccoidxxx,";
					$qDatMov .= "sccidxxx,";
					$qDatMov .= "commovxx,";
					$qDatMov .= "IF(commovxx = \"D\", comvlrxx, comvlrxx*-1) AS saldoxxx ";
					$qDatMov .= "FROM $cAlfa.fcod$nAnio ";
					$qDatMov .= "WHERE  ";
					$qDatMov .= "pucidxxx IN ($cCuenta)  AND ";
					$qDatMov .= "comfecxx <= \"$dHasta\" AND ";
					$qDatMov .= "teridxxx = \"$gTerId\"  AND ";
					$qDatMov .= "regestxx = \"ACTIVO\" ";
					$qDatMov .= "ORDER BY comidcxx,comcodcx,comcsccx,teridxxx,comfecxx,pucidxxx ";
					$xDatMov = mysql_query($qDatMov,$xConexion01);
					//echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";
					
					while ($xDATA = mysql_fetch_array($xDatMov)) {
						
						if ($xDATA['comidxxx'] != $xDATA['comidcxx'] || 
								$xDATA['comcodxx'] != $xDATA['comcodcx'] || 
								$xDATA['comcscxx'] != $xDATA['comcsccx']) {
							//Se debe buscar el cliente del combropante que se esta cancelando
							for ($nAnioDC=$nAnio;$nAnioDC>=$vSysStr['financiero_ano_instalacion_modulo'];$nAnioDC--) {
								$qDocCru  = "SELECT ";
								$qDocCru .= "terid2xx  ";
								$qDocCru .= "FROM $cAlfa.fcod$nAnioDC ";
								$qDocCru .= "WHERE  ";
								$qDocCru .= "comidxxx = \"{$xDATA['comidcxx']}\" AND ";
								$qDocCru .= "comcodxx = \"{$xDATA['comcodcx']}\" AND ";
								$qDocCru .= "comcscxx = \"{$xDATA['comcsccx']}\" AND ";
								$qDocCru .= "teridxxx = \"{$xDATA['teridxxx']}\" AND ";
								$qDocCru .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND ";
								$qDocCru .= "regestxx = \"ACTIVO\" LIMIT 0,1";
								$xDocCru = mysql_query($qDocCru,$xConexion01);
								//echo $qDocCru."~".mysql_num_rows($xDocCru)."<br><br>";
								if (mysql_num_rows($xDocCru) > 0) {
									$xRDC = mysql_fetch_array($xDocCru);
									$xDATA['terid2xx'] = $xRDC['terid2xx'];
									
									$nAnioDC = $vSysStr['financiero_ano_instalacion_modulo']-1;
								}
							}
						}

						if ($xDATA['terid2xx'] == $gTerId2) {
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx']  = $xDATA['comidxxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx']  = $xDATA['comcodxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx']  = $xDATA['comcscxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x']  = $xDATA['comcsc2x'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx']  = $xDATA['comseqxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx']  = $xDATA['comfecxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx']  = $xDATA['regestxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex']  = $xDATA['regfcrex'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx']  = $xDATA['ccoidxxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx']  = $xDATA['sccidxxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx']  = $xDATA['commovxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlrxx'] += $xDATA['saldoxxx'];
						}
					}
				}
			}
												
			foreach ($mComAgru as $xKey => $cValue) {
				if ($mComAgru[$xKey]['comvlrxx'] != 0) {
					$nInd_Data = count($mData);
					$mData[$nInd_Data] = $mComAgru[$xKey];
				}
			}
		break;

		case "5252": // Nuevo
		
			//Dentro de las cuentas seleccionadas busco solo aquellas que sean por cobrar
			$cCuenta = str_replace("\"", "", $cCuenta);
			$vCuenta = explode(",", $cCuenta);
			$cCuenta = "";
			for($nC=0; $nC<count($vCuenta); $nC++) {
				if ($vCuenta[$nC] != "") {
					$qCxP  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
					$qCxP .= "FROM $cAlfa.fpar0115 ";
					$qCxP .= "WHERE ";
					$qCxP .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$vCuenta[$nC]}\" LIMIT 0,1";
					// $qCxP .= "pucdetxx = \"P\" LIMIT 0,1";
					$xCxP  = mysql_query($qCxP,$xConexion01);
					//f_Mensaje(__FILE__,__LINE__,$qCxP."~".mysql_num_rows($xCxP));
					if (mysql_num_rows($xCxP) > 0) {
						$xRCxP = mysql_fetch_array($xCxP);
						$cCuenta .= "\"{$xRCxP['pucidxxx']}\",";
					}
				}
			}
			$cCuenta = substr($cCuenta, 0, -1);
			
			$vTablas = array(); $mData = array(); $mComAgru = array();
			if ($cCuenta != "") { 
				for ($nAnio=$vSysStr['financiero_ano_instalacion_modulo'];$nAnio<=$nAno;$nAnio++) {
					$qDatMov  = "SELECT ";
					$qDatMov .= "comidxxx,";
					$qDatMov .= "comcodxx,";
					$qDatMov .= "comcscxx,";
					$qDatMov .= "comcsc2x,";
					$qDatMov .= "comseqxx,";
					$qDatMov .= "comidcxx, ";
					$qDatMov .= "comcodcx, ";
					$qDatMov .= "comcsccx, ";
					$qDatMov .= "teridxxx, ";
					$qDatMov .= "terid2xx, ";
					$qDatMov .= "pucidxxx,";
					$qDatMov .= "comfecxx,";
					$qDatMov .= "regestxx,";
					$qDatMov .= "regfcrex,";
					$qDatMov .= "ccoidxxx,";
					$qDatMov .= "sccidxxx,";
					$qDatMov .= "commovxx,";
					$qDatMov .= "IF(commovxx = \"D\", comvlrxx, comvlrxx*-1) AS saldoxxx ";
					$qDatMov .= "FROM $cAlfa.fcod$nAnio ";
					$qDatMov .= "WHERE  ";
					$qDatMov .= "pucidxxx IN ($cCuenta)  AND ";
					$qDatMov .= "comfecxx <= \"$dHasta\" AND ";
					$qDatMov .= "teridxxx = \"$gTerId\"  AND ";
					$qDatMov .= "regestxx = \"ACTIVO\" ";
					$qDatMov .= "ORDER BY comidcxx,comcodcx,comcsccx,teridxxx,comfecxx,pucidxxx ";
					$xDatMov = mysql_query($qDatMov,$xConexion01);
					//echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";
					
					while ($xDATA = mysql_fetch_array($xDatMov)) {
						
						if ($xDATA['comidxxx'] != $xDATA['comidcxx'] || 
								$xDATA['comcodxx'] != $xDATA['comcodcx'] || 
								$xDATA['comcscxx'] != $xDATA['comcsccx']) {
							//Se debe buscar el cliente del combropante que se esta cancelando
							for ($nAnioDC=$nAnio;$nAnioDC>=$vSysStr['financiero_ano_instalacion_modulo'];$nAnioDC--) {
								$qDocCru  = "SELECT ";
								$qDocCru .= "terid2xx  ";
								$qDocCru .= "FROM $cAlfa.fcod$nAnioDC ";
								$qDocCru .= "WHERE  ";
								$qDocCru .= "comidxxx = \"{$xDATA['comidcxx']}\" AND ";
								$qDocCru .= "comcodxx = \"{$xDATA['comcodcx']}\" AND ";
								$qDocCru .= "comcscxx = \"{$xDATA['comcsccx']}\" AND ";
								$qDocCru .= "teridxxx = \"{$xDATA['teridxxx']}\" AND ";
								$qDocCru .= "pucidxxx = \"{$xDATA['pucidxxx']}\" AND ";
								$qDocCru .= "regestxx = \"ACTIVO\" LIMIT 0,1";
								$xDocCru = mysql_query($qDocCru,$xConexion01);
								//echo "{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"."~".$qDocCru."~".mysql_num_rows($xDocCru)."<br><br>";
								if (mysql_num_rows($xDocCru) > 0) {
									$xRDC = mysql_fetch_array($xDocCru);
									$xDATA['terid2xx'] = $xRDC['terid2xx'];
									
									$nAnioDC = $vSysStr['financiero_ano_instalacion_modulo']-1;
								}
							}
						}

						if ($xDATA['terid2xx'] == $gTerId2) {
							//echo "$gTerId2~{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}~{$xDATA['terid2xx']}~{$xDATA['saldoxxx']}<br>";
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comidxxx']  = $xDATA['comidxxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcodxx']  = $xDATA['comcodxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcscxx']  = $xDATA['comcscxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comcsc2x']  = $xDATA['comcsc2x'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comseqxx']  = $xDATA['comseqxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comfecxx']  = $xDATA['comfecxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regestxx']  = $xDATA['regestxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['regfcrex']  = $xDATA['regfcrex'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['ccoidxxx']  = $xDATA['ccoidxxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['sccidxxx']  = $xDATA['sccidxxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['commovxx']  = $xDATA['commovxx'];
							$mComAgru["{$xDATA['comidxxx']}-{$xDATA['comcodxx']}-{$xDATA['comcscxx']}"]['comvlrxx'] += $xDATA['saldoxxx'];
						}
					}
				}
			}
												
			foreach ($mComAgru as $xKey => $cValue) {
				if ($mComAgru[$xKey]['comvlrxx'] != 0) {
					$nInd_Data = count($mData);
					$mData[$nInd_Data] = $mComAgru[$xKey];
				}
			}
		break;
	} 
	
	#Numero de columnas
	$nColumnas = 0;
	switch ($gFormato) {
		case "1001":
			$nColumnas = 16;
		break;
		case "1003":
		case "1005":
		case "1054":
		case "5248":
		case "5249":
			$nColumnas = 7;
		break;
		case "1006":
		case "5250":
			$nColumnas = 8; 
		break;
		case "1007":
		case "1016":
			$nColumnas = 14;
		break;
		case "1008":
		case "1009":
		case "1012":
		case "1018":
		case "1027":
		case "5251":
		case "5252":
			$nColumnas = 6;
		break;
		case "5247":
			$nColumnas = 12;
		break;
	} ?>
	
	<form name = 'frgrm' action='frmedprn.php' method="post">
		<center>
			<table width="99%"  cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td>
						<table border="1 cellspacing="0" cellpadding="0" width="99%" align=center>
							<tr bgcolor = 'white' height="30">
								<td class="name" align="left" colspan="<?php echo $nColumnas?>"><font size="4"><b>Informe de Medios Magneticos - Formato <?php echo $gFormato ?></b>
								</td>
							</tr>
							<tr bgcolor = 'white' height="30">
								<td class="name" align="left" colspan="<?php echo $nColumnas?>"><font size="3">Tercero : <?php echo ($xRNC['CLINOMXX'] != "") ? utf8_encode($xRNC['CLINOMXX']) : (utf8_encode(trim($xRNC['CLIAPE1X']." ".$xRNC['CLIAPE2X']." ".$xRNC['CLINOM1X']." ".$xRNC['CLINOM2X']))); ?></td>
							</tr>	
							<tr bgcolor = 'white' height="30">
								<td class="name" align="left" colspan="<?php echo $nColumnas?>"><font size="3">Cuenta (PUC): <?php echo $cTitCue; ?></td>
							</tr>
							<tr bgcolor = 'white' height="30">
								<td class="name" align="left" colspan="<?php echo $nColumnas?>"><font size="2">Registros Analizados : <?php echo number_format(count($mData)) ?></td>
							</tr>
							<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
								<td class="name" align="center">Comprobante</td>
								<td class="name" width="110" align="center">Fecha</td>
								<td class="name" width="80" align="center">CC</td>
								<td class="name" width="80" align="center">SC</td>
								<td class="name" width="80" align="center">Movimiento</td>
								<?php
								switch ($gFormato) {
									case "1001": ?>
										<td class="name" width="150" align="center">Pago o abono en cuenta deducible</td>
										<td class="name" width="150" align="center">Pago o abono en cuenta no deducible</td>
										<td class="name" width="150" align="center">IVA mayor valor del costo o gasto deducible</td>    				                
										<td class="name" width="150" align="center">Iva mayor valor del costo o gasto no deducible</td>    				                
										<td class="name" width="150" align="center">Retencion en la fuente practicada renta</td>    				                
										<td class="name" width="150" align="center">Retencion en la fuente asumida renta</td>
										<td class="name" width="150" align="center">Retencion en la fuente practicada IVA regimen comun</td>
										<td class="name" width="150" align="center">Retencion en la fuente asumida IVA regimen simplificado</td>
										<td class="name" width="150" align="center">Retencion en la fuente practicada IVA no domiciliados</td>
										<td class="name" width="150" align="center">Retencion en la fuente practicadas CREE</td>
										<td class="name" width="150" align="center">Retencion en la fuente asumidas CREE</td>
									<?php break;
									case "1003": ?>
									<td class="name" width="150" align="center">Valor acum. del pago o abono sujeto a retencion en la fuente</td>
									<td class="name" width="150" align="center">Retencion en la fuente que le practicaron</td>
									<?php break;
									case "1005": ?>
										<td class="name" width="150" align="center">Impuesto descontable</td>
										<td class="name" width="150" align="center">IVA resultante por devoluciones en ventas anuladas, rescindidas o resueltas</td>
									<?php break;
									case "1006": ?>
										<td class="name" width="150" align="center">Impuesto generado</td>
										<td class="name" width="150" align="center">IVA recuperado por operaciones en devoluciones en compras anuladas, rescindidas o resueltas</td>
										<td class="name" width="150" align="center">Impuesto al consumo</td>
									<?php break;
									case "1007": ?>
										<td class="name" width="150" align="center">Ingresos brutos recibidos por operaciones propias</td>
										<td class="name" width="150" align="center">Ingresos a traves de consorcios o uniones temporales</td>
										<td class="name" width="150" align="center">Ingresos a traves de contratos de mandato o administracion delegada</td>
										<td class="name" width="150" align="center">Ingresos a traves de exploracion y explotacion de minerales</td>
										<td class="name" width="150" align="center">Ingresos a traves de fiducias</td>
										<td class="name" width="150" align="center">Ingresos recibidos a traves de terceros</td>
										<td class="name" width="150" align="center">Devoluciones, rebajas y descuentos</td>
									<?php break;	
									case "1008": ?>
										<td class="name" width="150" align="center">Saldo cuentas por cobrar al 31-12</td>
									<?php break;
									case "1009": ?>
										<td class="name" width="150" align="center">Saldo cuentas por pagar al 31-12</td>
									<?php break;
									case "1012": ?>
										<td class="name" width="150" align="center">Valor al 31-12 </td>
									<?php break;
									case "1016": ?>
										<td class="name" width="150" align="center">Pago o abono en cta</td>
										<td class="name" width="150" align="center">IVA mayor valor del costo o gasto</td>    				                
										<td class="name" width="150" align="center">Retencion en la fuente practicada renta</td>    				                
										<td class="name" width="150" align="center">Retencion en la fuente asumida renta</td>
										<td class="name" width="150" align="center">Retencion en la fuente practicada iva regimen comun</td>
										<td class="name" width="150" align="center">Retencion en la fuente asumida iva regimen simp.</td>
										<td class="name" width="150" align="center">Retencion en la fuente practicada iva no domiciliados</td> 
										<td class="name" width="150" align="center">Retencion en la fuente practicada CREE</td>
										<td class="name" width="150" align="center">Retencion en la fuente asumida CREE</td>
									<?php break;
									case "1018": ?>
										<td class="name" width="150" align="center">Saldo al 31-12</td>
									<?php break;
									case "1027": ?>
										<td class="name" width="150" align="center">Saldo al 31-12</td>
									<?php break;
									case "1054": ?>
										<td class="name" width="150" align="center">Impuesto descontable</td>
										<td class="name" width="150" align="center">IVA resultante por devoluciones en ventas anuladas rescindidas o resueltas</td>    				                
									<?php break;
									case "5247": ?>
										<td class="name" width="150" align="center">Pago o abono en cta</td>
										<td class="name" width="150" align="center">IVA mayor valor del costo o gasto</td>    				                
										<td class="name" width="150" align="center">Retencion en la fuente practicada renta</td>    				                
										<td class="name" width="150" align="center">Retencion en la fuente asumida renta</td>
										<td class="name" width="150" align="center">Retencion en la fuente practicada iva regimen comun</td>
										<td class="name" width="150" align="center">Retencion en la fuente asumida iva regimen simp.</td>
										<td class="name" width="150" align="center">Retencion en la fuente practicada iva no domiciliados</td> 
									<?php break;
									case "5248": ?>
										<td class="name" width="150" align="center">Ingreso bruto recibido</td>
										<td class="name" width="150" align="center">Devoluciones, rebajas y descuentos</td>    				                
									<?php break;
									case "5249": ?>
										<td class="name" width="150" align="center">Impuesto descontable</td>
										<td class="name" width="150" align="center">IVA descontable por devoluciones en ventas</td>    				                
									<?php break;
									case "5250": ?>
										<td class="name" width="150" align="center">Impuesto generdado</td>
										<td class="name" width="150" align="center">IVA generado por devoluciones en ventas</td>   
										<td class="name" width="150" align="center">Impuesto del consumo</td>    				                
									<?php break;
									case "5251": ?>
										<td class="name" width="150" align="center">Saldo de la CXC a diciembre 31</td>
									<?php break;
									case "5252": ?>
										<td class="name" width="150" align="center">Saldo de la CXP a diciembre 31</td>
									<?php break;
								} ?>
							</tr>
							<?php for ($i=0;$i<count($mData);$i++) { 
								if($mData[$i]['comidxxx'] == 'P' || $mData[$i]['comidxxx'] == 'L' || $mData[$i]['comidxxx'] == 'C'){
									$cTipCom = $mComP[$mData[$i]['comidxxx']][$mData[$i]['comcodxx']];
									
									if (in_array("{$mData[$i]['comidxxx']}~{$mData[$i]['comcodxx']}", $mRCM) == true) {
											$cTipCom = "RCM";
									}
								}else{
									$cTipCom = "";
								}
								
								if($mData[$i]['comidxxx'] == 'P' && $cTipCom = "CPC"){
									$qTipCau  = "SELECT comobs2x ";
									$qTipCau .= "FROM $cAlfa.fcoc$nAno ";
									$qTipCau .= "WHERE ";
									$qTipCau .= "comidxxx = \"{$mData[$i]['comidxxx']}\" AND ";
									$qTipCau .= "comcodxx = \"{$mData[$i]['comcodxx']}\" AND ";
									$qTipCau .= "comcscxx = \"{$mData[$i]['comcscxx']}\" AND ";
									$qTipCau .= "comcsc2x = \"{$mData[$i]['comcsc2x']}\" LIMIT 0,1 ";
									$xTipCau  = f_MySql("SELECT","",$qTipCau,$xConexion01,"");
									$vTipCau  = mysql_fetch_array($xTipCau);

									if(trim($vTipCau['comobs2x']) == "MANUAL"){
										$cTipCom = "CPC_MAN";
									}else{
										$cTipCom = "CPC_AUT";
									}
								}
								
								?>
								<tr bgcolor = 'white' height="30" style="padding-left:5px;padding-rigth:5px">
									<?php switch ($gFormato) {
										case "1006": ?>
											<td class="letra7" align="left"><?php echo ($mData[$i]['comidxxx'] <> "")?"<a href=\"javascript:f_Datos_Comprobante('{$mData[$i]['comidxxx']}','{$mData[$i]['comcodxx']}','{$mData[$i]['comcscxx']}','{$mData[$i]['comcsc2x']}','{$mData[$i]['comfecxx']}','{$mData[$i]['regestxx']}','{$mData[$i]['regfcrex']}','$cTipCom')\">{$mData[$i]['comidxxx']}-{$mData[$i]['comcodxx']}-{$mData[$i]['comcscxx']}-{$mData[$i]['comseqxx']}</a>": "&nbsp;"; ?></td>
										<?php break;
										case "1001":
										case "1003":
										case "1005":
										case "1007":
										case "1008":
										case "1009":
										case "1012":
										case "1018":
										case "1027":
										case "1016": 
										case "1054":
										case "5247":
										case "5248":
										case "5249":
										case "5250":
										case "5251": 
										case "5252": ?>
											<td class="letra7" align="left"><?php echo ($mData[$i]['comidxxx'] <> "")?"<a href=\"javascript:f_Datos_Comprobante('{$mData[$i]['comidxxx']}','{$mData[$i]['comcodxx']}','{$mData[$i]['comcscxx']}','{$mData[$i]['comcsc2x']}','{$mData[$i]['comfecxx']}','{$mData[$i]['regestxx']}','{$mData[$i]['regfcrex']}','$cTipCom')\">{$mData[$i]['comidxxx']}-{$mData[$i]['comcodxx']}-{$mData[$i]['comcscxx']}</a>": "&nbsp;"; ?></td>
										<?php break;
									} ?>
									<td class="letra7" align="center"><?php echo ($mData[$i]['comfecxx']<> "")?$mData[$i]['comfecxx']: "&nbsp;"; ?></td>
									<td class="letra7" align="center"><?php echo ($mData[$i]['ccoidxxx'] <> "") ?  $mData[$i]['ccoidxxx'] : "&nbsp;"; ?></td>
									<td class="letra7" align="center"><?php echo ($mData[$i]['sccidxxx']<> "")?$mData[$i]['sccidxxx']: "&nbsp;"; ?></td>
									<td class="letra7" align="center"><?php echo ($mData[$i]['commovxx']<> "")?$mData[$i]['commovxx']: "&nbsp;"; ?></td>
									<?php
									switch ($gFormato) {
										case "1001": ?>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['comvlrxx'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['comvlrno'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['comivaxx'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['comivano'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['pracxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['asumxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['comunxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['simplxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['ndomxxxx'],0,",",".") ?></td> 
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['praccree'],0,",",".") ?></td>    					                
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['asumcree'],0,",",".") ?></td>                     
										<?php break;
										case "1003": ?>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['comvlr01'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['comvlrxx'],0,",",".") ?></td>
										<?php break;
										case "1005": ?>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['comvlrxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format(0,0,",",".") ?></td>
										<?php break;
										case "1006": ?>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['comvlrxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format(0,0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format(0,0,",",".") ?></td>
										<?php break;
										case "1007": ?>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['iproxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['iconxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['imanxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['iexpxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['ifidxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['iterxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['devxxxxx'],0,",",".") ?></td>
										<?php break;
										case "1008": 
										case "1009":
										case "1018": 
										case "1012":
										case "1027":
										case "5251":
										case "5252": ?>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['comvlrxx'],0,",",".") ?></td>
										<?php break;
										case "1016": ?>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['comvlr01'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['comvlr02'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['pracxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['asumxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['comunxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['simplxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['ndomxxxx'],0,",",".") ?></td>  
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['praccree'],0,",",".") ?></td>    					                
											<td class="letra7" align="right"> <?php echo number_format($mData[$i]['asumcree'],0,",",".") ?></td>                     
										<?php break;
										case "1054": ?>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['impdxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['ivarxxxx'],0,",",".") ?></td>
										<?php break; 
										case "5247": ?>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['comvlr01'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['comvlr02'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['pracxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['asumxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['comunxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['simplxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['ndomxxxx'],0,",",".") ?></td>
										<?php break;
										case "5248": ?>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['iproxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['devxxxxx'],0,",",".") ?></td>
										<?php break;  
										case "5249": ?>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['impdxxxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['ivarxxxx'],0,",",".") ?></td>
										<?php break;  
										case "5250": ?>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['impgenxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['ivadexxx'],0,",",".") ?></td>
											<td class="letra7" align="right"><?php echo number_format($mData[$i]['impconxx'],0,",",".") ?></td>
										<?php break;  
									} ?>	                
								</tr>
							<?php } //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
						</table>
					</td>
				</tr>
			</table>
		</center>
	</form>
	<script type="text/javascript">document.getElementById('loading').style.display="none";</script>
	</body>
	</html>

<?php 
	function fnCadenaAleatoria($pLength = 8) {
		$cCaracteres = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ";
		$nCaracteres = strlen($cCaracteres);
		$cResult = "";
		for ($x=0;$x< $pLength;$x++) {
			$nIndex = mt_rand(0,$nCaracteres - 1);
			$cResult .= $cCaracteres[$nIndex];
		}
		return $cResult;
	}
?>

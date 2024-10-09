<?php
  namespace openComex;
	/**
 		* Tracking Documentos con saldo descuadrado. pinta  por Pantalla o Por Excel
 		* Este programa permite mostrar los documentos con saldo descuadrado que se Encuentran en la Base de Datos.
 		* @author Oscar hernandez <oscar.hernandez@opentenologia.com.co>
 		* @package openComex
 		*/
 		
 		// ini_set('error_reporting', E_ERROR);
		// ini_set("display_errors","1");
	
 		ini_set("memory_limit","1024M");
		set_time_limit(0);

		define(_NUMREG_,1000);
		
		include("../../../../libs/php/utility.php"); 
		include("../../../../../config/config.php");

		/****INICIO SQL ****/
  	$AnoIni= $vSysStr['financiero_ano_instalacion_modulo'];
  	$AnoFin= date("Y");

		#Buscando cuentas por cobrar o por pagar
  	$qCuentas  = "SELECT *, CONCAT(pucgruxx, pucctaxx, pucsctax, pucauxxx, pucsauxx) AS pucidxxx ";
  	$qCuentas .= "FROM $cAlfa.fpar0115 ";
  	$qCuentas .= "WHERE ";
  	$qCuentas .= "pucdetxx IN (\"C\",\"P\") ";
  	$xCuentas  = f_MySql("SELECT","",$qCuentas,$xConexion01,"");
		$cCuentas = ""; $vCuentas = array();
		while ($xRDS = mysql_fetch_array($xCuentas)){
  		$cCuentas .= "\"{$xRDS['pucidxxx']}\",";
			$vCuentas["{$xRDS['pucidxxx']}"] = $xRDS;
  	}
		mysql_free_result($xCuentas);
  	$cCuentas = substr($cCuentas, 0, -1);
		#Fin Buscando cuentas por cobrar o por pagar
  	
  	if ($cCuentas != "") { 	

			//Se debe crear una tabla temporal mem para pasarla como parametro al nuevo de ajuste y enviar los datos
        
			//Llamando Metodo que hace conexion
			$mReturnConexionTM = fnConectarDB();
			if($mReturnConexionTM[0] == "true"){
				$xConexionTM = $mReturnConexionTM[1];
			}else{
				$cMsj = "";
				for($nR=1;$nR<count($mReturnConexionTM);$nR++){
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= $mReturnConexionTM[$nR];
				}
				if ($cMsj != "") {
					echo $cMsj;
				}
			}
			
			$cTabTem = "memsalde".mt_rand(1000000000, 9999999999); 
        
      $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabTem (";
			$qNewTab .= "comidcxx varchar(1) NOT NULL COMMENT 'Id del Comprobante Cruce',";
			$qNewTab .= "comcodcx varchar(4) DEFAULT NULL COMMENT 'Codigo del Comprobante Cruce',";
			$qNewTab .= "comcsccx varchar(20) DEFAULT NULL COMMENT 'Consecutivo del Comprobante Cruce',";
			$qNewTab .= "comseqcx varchar(5) NOT NULL COMMENT 'Secuencia del Comprobante Cruce',";
			$qNewTab .= "teridxxx varchar(12) NOT NULL COMMENT 'Id del Tercero',";
			$qNewTab .= "pucidxxx varchar(10) NOT NULL COMMENT 'Cuenta Contable PUC',";
			$qNewTab .= "pucdetxx varchar(1) NOT NULL COMMENT 'Tipo de Detalle de la Cuenta',";
			$qNewTab .= "comfecve date NOT NULL COMMENT 'Fecha de Vencimiento del Comprobante',";
			$qNewTab .= "comperxx varchar(6) NOT NULL COMMENT 'Periodo del Comprobante',";
			$qNewTab .= "comidxxx varchar(1) NOT NULL COMMENT 'Id del Comprobante',";
			$qNewTab .= "comcodxx varchar(4) NOT NULL COMMENT 'Codigo del Comprobante',";
			$qNewTab .= "comfecxx date NOT NULL COMMENT 'Fecha del Comprobante',";
			$qNewTab .= "saldoxxx decimal(15,2) NOT NULL COMMENT 'Valor del Comprobante',";
			$qNewTab .= "regestxx varchar(12) NOT NULL COMMENT 'Estado del Registro',";
			$qNewTab .= "PRIMARY KEY (comidcxx,comcodcx,comcsccx,teridxxx,pucidxxx))";
			$xNewTab = mysql_query($qNewTab,$xConexionTM);

			##Fin Acciones sobre la DB en el paso Dos		
			$mDatMov = array();
			$mDatMov = array();
			$nCanReg = 0;
			for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {
				
				$qDatMov  = "SELECT ";
				$qDatMov .= "$cAlfa.fcod$nAno.comidcxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comcodcx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comcsccx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comseqcx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.teridxxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.pucidxxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comperxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comidxxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comcodxx, ";			
				$qDatMov .= "$cAlfa.fcod$nAno.comfecxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comfecve, ";
				$qDatMov .= "$cAlfa.fcod$nAno.commovxx, ";			
				$qDatMov .= "IF($cAlfa.fcod$nAno.commovxx = \"D\", SUM($cAlfa.fcod$nAno.comvlrxx), SUM($cAlfa.fcod$nAno.comvlrxx)*-1) AS comvlrxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.regestxx ";			
				$qDatMov .= "FROM $cAlfa.fcod$nAno ";
				$qDatMov .= "WHERE ";
				// $qDatMov .= "$cAlfa.fcod$nAno.comidcxx = \"F-025617\" AND ";
				// $qDatMov .= "$cAlfa.fcod$nAno.comidcxx = \"P\" AND ";
				// $qDatMov .= "$cAlfa.fcod$nAno.comcodcx = \"014\" AND ";
				// $qDatMov .= "$cAlfa.fcod$nAno.comcsccx = \"2012040001\" AND ";
				$qDatMov .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" AND ";			
				$qDatMov .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuentas) ";
				$qDatMov .= "GROUP BY $cAlfa.fcod$nAno.comidcxx,$cAlfa.fcod$nAno.comcodcx,$cAlfa.fcod$nAno.comcsccx,$cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.pucidxxx,$cAlfa.fcod$nAno.commovxx ";			
				$qDatMov .= "ORDER BY $cAlfa.fcod$nAno.comfecxx ";				
				$xDatMov = mysql_query($qDatMov,$xConexion01);
				//f_Mensaje(__FILE__,__LINE__,$qDatMov);
				// echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";
				$nCanReg = 0;
				while ($xCre = mysql_fetch_array($xDatMov)) {

					$nCanReg++;
					if (($nCanReg % _NUMREG_) == 0) {
						$xConexion01 = fnReiniciarConexion();
					}

					$qDocCru  = "SELECT saldoxxx ";
					$qDocCru .= "FROM $cAlfa.$cTabTem ";
					$qDocCru .= "WHERE ";
					$qDocCru .= "comidcxx = \"{$xCre['comidcxx']}\" AND ";
					$qDocCru .= "comcodcx = \"{$xCre['comcodcx']}\" AND ";
					$qDocCru .= "comcsccx = \"{$xCre['comcsccx']}\" AND ";
					$qDocCru .= "teridxxx = \"{$xCre['teridxxx']}\" AND ";
					$qDocCru .= "pucidxxx = \"{$xCre['pucidxxx']}\" LIMIT 0,1 ";
					$xDocCru = mysql_query($qDocCru,$xConexion01);
					// echo $qDocCru."~".mysql_num_rows($xDocCru)."<br>";

					if (mysql_num_rows($xDocCru) == 0) {
						$qInsert  = "INSERT INTO $cAlfa.$cTabTem VALUES (";
						$qInsert .= "\"{$xCre['comidcxx']}\",";
						$qInsert .= "\"{$xCre['comcodcx']}\",";
						$qInsert .= "\"{$xCre['comcsccx']}\",";
						$qInsert .= "\"{$xCre['comseqcx']}\",";
						$qInsert .= "\"{$xCre['teridxxx']}\",";
						$qInsert .= "\"{$xCre['pucidxxx']}\",";
						$qInsert .= "\"".$vCuentas["{$xCre['pucidxxx']}"]['pucdetxx']."\",";
						$qInsert .= "\"{$xCre['comfecve']}\",";
						$qInsert .= "\"{$xCre['comperxx']}\",";
						$qInsert .= "\"{$xCre['comidxxx']}\",";
						$qInsert .= "\"{$xCre['comcodxx']}\",";
						$qInsert .= "\"{$xCre['comfecxx']}\",";
						$qInsert .= "\"{$xCre['comvlrxx']}\",";
						$qInsert .= "\"{$xCre['regestxx']}\")";
						$xInsDet = mysql_query($qInsert,$xConexion01);
						// echo $qInsert."<br><br>";
						if (!$xInsDet) {
							echo mysql_error($xInsDet);
						}
					} else {
						$vDocCru = mysql_fetch_array($xDocCru);
						$nSaldo = $vDocCru['saldoxxx'] + $xCre['comvlrxx'];	

						// echo "$nSaldo = {$vDocCru['saldoxxx']} + {$xCre['comvlrxx']}<br>";

						$qUpdate  = "UPDATE $cAlfa.$cTabTem ";
						$qUpdate .= "SET saldoxxx = \"$nSaldo\" ";
						$qUpdate .= "WHERE ";
						$qUpdate .= "comidcxx = \"{$xCre['comidcxx']}\" AND ";
						$qUpdate .= "comcodcx = \"{$xCre['comcodcx']}\" AND ";
						$qUpdate .= "comcsccx = \"{$xCre['comcsccx']}\" AND ";
						$qUpdate .= "teridxxx = \"{$xCre['teridxxx']}\" AND ";
						$qUpdate .= "pucidxxx = \"{$xCre['pucidxxx']}\"";
						// echo $qUpdate."<br><br>";
						$xUpdate  = mysql_query($qUpdate,$xConexion01);
						if (!$xUpdate) {
							echo mysql_error($xUpdate);
						}
					}				
				}	  	
			}
			
			// $qDocCru  = "SELECT * ";
			// $qDocCru .= "FROM $cAlfa.$cTabTem";
			// $xDocCru = mysql_query($qDocCru,$xConexion01);
			// echo $qDocCru."~".mysql_num_rows($xDocCru)."<br><br>";
			// die();
			
		  //// Empiezo a Recorrer la Matriz de Creditos Vs Debitos para Dejar las Cuentas que a la fecha de Corte tienen Saldo ////		
			$mSaldos = array();
			if ($gVerificar == 1) {
				$qDocCru  = "SELECT * ";
				$qDocCru .= "FROM $cAlfa.$cTabTem";
				$xDocCru = mysql_query($qDocCru,$xConexion01);

				$nCanReg = 0;
				while ($xRDC = mysql_fetch_array($xDocCru)) {

					$nCanReg++;
					if (($nCanReg % _NUMREG_) == 0) {
						$xConexion01 = fnReiniciarConexion();
					}

					if ($xRDC['pucdetxx'] == "P") { $cTable = "$cAlfa.fcxp0000"; } else {$cTable = "$cAlfa.fcxc0000"; }
					$qSaldo  = "SELECT ";
					$qSaldo .= "$cTable.comsaldo ";
					$qSaldo .= "FROM $cTable ";
					$qSaldo .= "WHERE ";
					$qSaldo .= "$cTable.comidxxx = \"{$xRDC['comidcxx']}\" AND ";
					$qSaldo .= "$cTable.comcodxx = \"{$xRDC['comcodcx']}\" AND ";
					$qSaldo .= "$cTable.comcscxx = \"{$xRDC['comcsccx']}\" AND ";
					$qSaldo .= "$cTable.teridxxx = \"{$xRDC['teridxxx']}\" AND ";
					$qSaldo .= "$cTable.pucidxxx = \"{$xRDC['pucidxxx']}\" AND ";
					$qSaldo .= "$cTable.regestxx = \"ACTIVO\" LIMIT 0,1";
					$xSaldo  = mysql_query($qSaldo,$xConexion01);
					// echo $qSaldo." ~ ".mysql_num_rows($xSaldo)."<br><br>";
					//f_Mensaje(__FILE__,__LINE__,$qSaldo." ~ ".mysql_num_rows($xSaldo));
					$vSaldo = array();
					$vSaldo  = mysql_fetch_array($xSaldo);
									
					if (round($xRDC['saldoxxx'],2) != round($vSaldo['comsaldo'],2)) {
						//Traigo Nombre del Tercero
						$qCliDat  = "SELECT ";
						$qCliDat .= "if($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF($cAlfa.SIAI0150.CLIAPE1X  != \"\",CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X),\"TERCERO SIN NOMBRE\")) AS CLINOMXX ";
						$qCliDat .= "FROM $cAlfa.SIAI0150 ";
						$qCliDat .= "WHERE ";
						$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$xRDC['teridxxx']}\" LIMIT 0,1 ";
						$xCliDat  = mysql_query($qCliDat,$xConexion01);
						$vCliDat = mysql_fetch_array($xCliDat);
						//f_Mensaje(__FILE__,__LINE__,$qCliDat." ~ ".mysql_num_rows($xCliDat));
									
						$nInd_mSaldos = count($mSaldos);
						$mSaldos[$nInd_mSaldos] = $xRDC; 
						$mSaldos[$nInd_mSaldos]['doccruxx'] = $xRDC['comidcxx']."-".$xRDC['comcodcx']."-".$xRDC['comcsccx']."-".$xRDC['comseqcx']; 
						$mSaldos[$nInd_mSaldos]['comsaldo'] = $vSaldo['comsaldo']; 
						$mSaldos[$nInd_mSaldos]['clinomxx'] = $vCliDat['CLINOMXX'];  
					}	  		 		
				}
			}	

			if ($gVerificar == 2) {

				$qCxP  = "SELECT * ";
				$qCxP .= "FROM $cAlfa.fcxp0000 ";
				$xCxP = mysql_query($qCxP,$xConexion01);
				$nCanReg = 0;
				while ($xRCxP = mysql_fetch_array($xCxP)) {

					$nCanReg++;
					if (($nCanReg % _NUMREG_) == 0) {
						$xConexion01 = fnReiniciarConexion();
					}

					$qSaldo  = "SELECT ";
					$qSaldo .= "saldoxxx ";
					$qSaldo .= "FROM $cAlfa.$cTabTem ";
					$qSaldo .= "WHERE ";
					$qSaldo .= "comidcxx = \"{$xRCxP['comidxxx']}\" AND ";
					$qSaldo .= "comcodcx = \"{$xRCxP['comcodxx']}\" AND ";
					$qSaldo .= "comcsccx = \"{$xRCxP['comcscxx']}\" AND ";
					$qSaldo .= "teridxxx = \"{$xRCxP['teridxxx']}\" AND ";
					$qSaldo .= "pucidxxx = \"{$xRCxP['pucidxxx']}\" AND ";
					$qSaldo .= "regestxx = \"ACTIVO\" LIMIT 0,1";
					$xSaldo  = mysql_query($qSaldo,$xConexion01);
					// echo $qSaldo." ~ ".mysql_num_rows($xSaldo)."<br><br>";
					//f_Mensaje(__FILE__,__LINE__,$qSaldo." ~ ".mysql_num_rows($xSaldo));
					
					if (mysql_num_rows($xSaldo) == 0) {
						//Traigo Nombre del Tercero
						$qCliDat  = "SELECT ";
						$qCliDat .= "if($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF($cAlfa.SIAI0150.CLIAPE1X  != \"\",CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X),\"TERCERO SIN NOMBRE\")) AS CLINOMXX ";
						$qCliDat .= "FROM $cAlfa.SIAI0150 ";
						$qCliDat .= "WHERE ";
						$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$xRCxP['teridxxx']}\" LIMIT 0,1 ";
						$xCliDat  = mysql_query($qCliDat,$xConexion01);
						$vCliDat = mysql_fetch_array($xCliDat);
						//f_Mensaje(__FILE__,__LINE__,$qCliDat." ~ ".mysql_num_rows($xCliDat));
									
						$nInd_mSaldos = count($mSaldos);
						$mSaldos[$nInd_mSaldos]['comidcxx'] = $xRCxP['comidxxx'];
						$mSaldos[$nInd_mSaldos]['comcodcx'] = $xRCxP['comcodxx'];
						$mSaldos[$nInd_mSaldos]['comcsccx'] = $xRCxP['comcscxx'];
						$mSaldos[$nInd_mSaldos]['comseqcx'] = $xRCxP['comseqxx'];
						$mSaldos[$nInd_mSaldos]['teridxxx'] = $xRCxP['teridxxx'];
						$mSaldos[$nInd_mSaldos]['pucidxxx'] = $xRCxP['pucidxxx'];
						$mSaldos[$nInd_mSaldos]['pucdetxx'] = $vCuentas["{$xRCxP['pucidxxx']}"]['pucdetxx'];
						$mSaldos[$nInd_mSaldos]['comfecve'] = $xRCxP['comfecve'];
						$mSaldos[$nInd_mSaldos]['comperxx'] = str_replace(-"","",substr($xRCxP['comfecve'],0,7));
						$mSaldos[$nInd_mSaldos]['comidxxx'] = $xRCxP['comidxxx'];
						$mSaldos[$nInd_mSaldos]['comcodxx'] = $xRCxP['comcodxx'];
						$mSaldos[$nInd_mSaldos]['comfecxx'] = $xRCxP['comfecve'];
						$mSaldos[$nInd_mSaldos]['saldoxxx'] = "0";
						$mSaldos[$nInd_mSaldos]['regestxx'] = $xRCxP['regestxx'];

						$mSaldos[$nInd_mSaldos]['doccruxx'] = $xRCxP['comidxxx']."-".$xRCxP['comcodxx']."-".$xRCxP['comcscxx']."-".$xRCxP['comseqxx']; 
						$mSaldos[$nInd_mSaldos]['comsaldo'] = $xRCxP['comsaldo']; 
						$mSaldos[$nInd_mSaldos]['clinomxx'] = $vCliDat['CLINOMXX'];  
					}	  		 		
				}
			}

			if ($gVerificar == 3) {
				$qCxC  = "SELECT * ";
				$qCxC .= "FROM $cAlfa.fcxc0000 ";
				$xCxC = mysql_query($qCxC,$xConexion01);
				$nCanReg = 0;
				while ($xRCxC = mysql_fetch_array($xCxC)) {

					$nCanReg++;
					if (($nCanReg % _NUMREG_) == 0) {
						$xConexion01 = fnReiniciarConexion();
					}

					$qSaldo  = "SELECT ";
					$qSaldo .= "saldoxxx ";
					$qSaldo .= "FROM $cAlfa.$cTabTem ";
					$qSaldo .= "WHERE ";
					$qSaldo .= "comidcxx = \"{$xRCxC['comidxxx']}\" AND ";
					$qSaldo .= "comcodcx = \"{$xRCxC['comcodxx']}\" AND ";
					$qSaldo .= "comcsccx = \"{$xRCxC['comcscxx']}\" AND ";
					$qSaldo .= "teridxxx = \"{$xRCxC['teridxxx']}\" AND ";
					$qSaldo .= "pucidxxx = \"{$xRCxC['pucidxxx']}\" AND ";
					$qSaldo .= "regestxx = \"ACTIVO\" LIMIT 0,1";
					$xSaldo  = mysql_query($qSaldo,$xConexion01);
					// echo $qSaldo." ~ ".mysql_num_rows($xSaldo)."<br><br>";
					// f_Mensaje(__FILE__,__LINE__,$qSaldo." ~ ".mysql_num_rows($xSaldo));
					
					if (mysql_num_rows($xSaldo) == 0) {
						//Traigo Nombre del Tercero
						$qCliDat  = "SELECT ";
						$qCliDat .= "if($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF($cAlfa.SIAI0150.CLIAPE1X  != \"\",CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X),\"TERCERO SIN NOMBRE\")) AS CLINOMXX ";
						$qCliDat .= "FROM $cAlfa.SIAI0150 ";
						$qCliDat .= "WHERE ";
						$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$xRCxC['teridxxx']}\" LIMIT 0,1 ";
						$xCliDat  = mysql_query($qCliDat,$xConexion01);
						$vCliDat = mysql_fetch_array($xCliDat);
						//f_Mensaje(__FILE__,__LINE__,$qCliDat." ~ ".mysql_num_rows($xCliDat));
									
						$nInd_mSaldos = count($mSaldos);
						$mSaldos[$nInd_mSaldos]['comidcxx'] = $xRCxC['comidxxx'];
						$mSaldos[$nInd_mSaldos]['comcodcx'] = $xRCxC['comcodxx'];
						$mSaldos[$nInd_mSaldos]['comcsccx'] = $xRCxC['comcscxx'];
						$mSaldos[$nInd_mSaldos]['comseqcx'] = $xRCxC['comseqxx'];
						$mSaldos[$nInd_mSaldos]['teridxxx'] = $xRCxC['teridxxx'];
						$mSaldos[$nInd_mSaldos]['pucidxxx'] = $xRCxC['pucidxxx'];
						$mSaldos[$nInd_mSaldos]['pucdetxx'] = $vCuentas["{$xRCxC['pucidxxx']}"]['pucdetxx'];
						$mSaldos[$nInd_mSaldos]['comfecve'] = $xRCxC['comfecve'];
						$mSaldos[$nInd_mSaldos]['comperxx'] = str_replace(-"","",substr($xRCxC['comfecve'],0,7));
						$mSaldos[$nInd_mSaldos]['comidxxx'] = $xRCxC['comidxxx'];
						$mSaldos[$nInd_mSaldos]['comcodxx'] = $xRCxC['comcodxx'];
						$mSaldos[$nInd_mSaldos]['comfecxx'] = $xRCxC['comfecve'];
						$mSaldos[$nInd_mSaldos]['saldoxxx'] = "0";
						$mSaldos[$nInd_mSaldos]['regestxx'] = $xRCxC['regestxx'];

						$mSaldos[$nInd_mSaldos]['doccruxx'] = $xRCxC['comidxxx']."-".$xRCxC['comcodxx']."-".$xRCxC['comcscxx']."-".$xRCxC['comseqxx']; 
						$mSaldos[$nInd_mSaldos]['comsaldo'] = $xRCxC['comsaldo']; 
						$mSaldos[$nInd_mSaldos]['clinomxx'] = $vCliDat['CLINOMXX'];  
					}	  		 		
				}
			}

	 	}

	 	// Armo Cadena  del Tipo De busqueda seleccionada  previamente
	 	$cCadena = "Documentos con Saldo Descuadrado Periodo  ";
	 	
	 	if($gPeriodo != "" && $gComId == "" && $gComCod == ""){
	 		$cCadena.= " ".substr($gPeriodo, 0,4)."-".substr($gPeriodo, 4,2);
	 		
	 	}else if ($gPeriodo != "" && $gComId != "" && $gComCod != ""){
	 		$cCadena.= " ".substr($gPeriodo, 0,4)."-".substr($gPeriodo, 4,2)." ";
	 		$cCadena.= " con el Comprobante ".$gComId."-".$gComCod;
	 		
	 		
	 	}else if ($gPeriodo != "" && $gComId != "" && $gComCod == ""){
	 		$cCadena.= " ".substr($gPeriodo, 0,4)."-".substr($gPeriodo, 4,2)." ";
	 		$cCadena.= "del Documento ".$gComId;
	 	
	 	}else if ($gPeriodo == "" && $gComId == "" && $gComCod == ""){
	 		$cCadena = "";
	 		
	 	}else if ($gPeriodo == "" && $gComId != "" && $gComCod != ""){
	 		$cCadena = "Documentos con Saldo descuadrado con ";
	 		$cCadena.= "Comprobante ".$gComId."-".$gComCod;
	 	}
	 	
	 	
		switch ($cTipo){
			case 1:
				// Pinta Por Pantalla
		?>
			<html>
    		<head>
      		<title>Reporte de Documentos con Saldo Descuadrado</title>
      		<link rel="stylesheet" type="text/css" href="<?php echo $cSystem_Libs_JS_Directory_New ?>/gwtext/resources/css/ext-all.css" />
		  		<script type="text/javascript" src="<?php echo $cSystem_Libs_JS_Directory_New ?>/gwtext/adapter/ext/ext-base.js"></script>
		  		<script type="text/javascript" src="<?php echo $cSystem_Libs_JS_Directory_New ?>/gwtext/ext-all.js"></script>
		  		<script language="JavaScript" src="<?php echo $cSystem_Libs_JS_Directory_New ?>/gwtext/conexijs/loading/loading.js"></script>
      		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
      		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
      		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
      		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    		</head>
    		<body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0" onLoad="init();">
  				<script>
						uLoad();
			  		var ld=(document.all);
			  		var ns4=document.layers;
			  		var ns6=document.getElementById&&!document.all;
			  		var ie4=document.all;
			  		
			  		function init() {
			  			if(ns4){ld.visibility="hidden";}
			  			else if (ns6||ie4){
			  					Ext.MessageBox.updateProgress(1,'100% completed');
			  					Ext.MessageBox.hide();
			  			}
			  		}
		  		</script>
		  		<?php
						ob_flush();
						flush();
						if(count($mSaldos)>0){
					?>
		      		<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="98%">
		        		<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
		          		<td class="name" colspan="13" align="left">
		          			<center><font size="3"><br><b>REPORTE DE DOCUMENTOS CON SALDO DESCUADRADO
		          			<br>
		          			<br>Total Comprobantes Descuadrados en el sistema  : <?php echo number_format(count($mSaldos),0,',','.') ?> de <?php echo number_format($nCanReg,0,',','.') ?><br><br>
		              		</font>
		            		</center>
		            		<center><font size="3"><?php echo $cCadena?></center></font>
		            		<br>
		          		</td>
		        		</tr>              
		        		<tr height="20">
		        			<td style="background-color:#0B610B" class="letra8" align="center" width="050px"><b><font color=white>ID</font></b></td>
		        			<td style="background-color:#0B610B" class="letra8" align="center" width="150px"><b><font color=white>Documento Cruce</font></b></td>
		        			<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Fecha</font></b></td>
		        			<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Tercero</font></b></td>
		        			<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Nombre</font></b></td>
		          		<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>PUC</font></b></td>
		          		<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Detalle</font></b></td>
		          		<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Saldo Contabilidad</font></b></td>
		           		<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Saldo Modulo<br>(CxC / CxP)</font></b></td>
		        		</tr>
       					<?php 
       					for ($i=0;$i<count($mSaldos);$i++) { 
       						$nBand = 0;
       						if($gPeriodo == $mSaldos[$i]['comperxx'] && $gComId == $mSaldos[$i]['comidxxx'] && $gComCod == $mSaldos[$i]['comcodxx']){
       							$nBand = 1;
       						}else if ($gPeriodo == $mSaldos[$i]['comperxx'] && $gComId == "" && $gComCod == ""){
       								$nBand = 1;
       						}else if($gPeriodo == $mSaldos[$i]['comperxx'] && $gComId == $mSaldos[$i]['comidxxx'] && $gComCod == ""){
       							$nBand = 1;
       						}else if ($gPeriodo == "" && $gComId == "" && $gComCod == ""){
       							$nBand = 1;
       						}else if ($gPeriodo == "" && $gComId == $mSaldos[$i]['comidxxx'] && $gComCod == $mSaldos[$i]['comcodxx']){
       						  $nBand =1;
       						}       							
       						
       						if($nBand == 1){
       							$n++;
       							
										if ( $vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' ) {
											// si el ups busco el comcsc3x
											for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {
												$qDatMov  = "SELECT ";
												$qDatMov .= "$cAlfa.fcoc$nAno.comcsc3x, ";
												$qDatMov .= "$cAlfa.fcoc$nAno.comcsc2x ";
												$qDatMov .= "FROM $cAlfa.fcoc$nAno ";
												$qDatMov .= "WHERE ";
												$qDatMov .= "($cAlfa.fcoc$nAno.comidxxx = \"{$mDatMov[$i]['comidcxx']}\" OR $cAlfa.fcoc$nAno.comidxxx =\"S\" ) AND ";
												$qDatMov .= "$cAlfa.fcoc$nAno.comidcxx = \"{$mDatMov[$i]['comidcxx']}\" AND ";
												$qDatMov .= "$cAlfa.fcoc$nAno.comcodcx = \"{$mDatMov[$i]['comcodcx']}\" AND ";
												$qDatMov .= "$cAlfa.fcoc$nAno.comcsccx = \"{$mDatMov[$i]['comcsccx']}\" AND ";
												$qDatMov .= "$cAlfa.fcoc$nAno.teridxxx = \"{$mDatMov[$i]['teridxxx']}\" AND ";
												$qDatMov .= "$cAlfa.fcoc$nAno.pucidxxx = \"{$mDatMov[$i]['pucidxxx']}\" LIMIT 0,1";
												$xDatMov  = mysql_query($qDatMov,$xConexion01);
												// echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";
												
												if (mysql_num_rows($xDatMov) > 0 ) {
													$vCre = mysql_fetch_array($xDatMov);
													$vCre['comcsc3x'] = ($vCre['comcsc3x'] != '') ? $vCre['comcsc3x'] : $vCre['comcsc2x'];
													$mSaldos[$i]['doccruxx'] = $mSaldos[$i]['comidcxx']."-".$mSaldos[$i]['comcodcx']."-".$mSaldos[$i]['comcsccx']."-".$vCre['comcsc3x']."-".$mSaldos[$i]['comseqcx'];
													$nAno = $AnoFin + 1;
												}
											}
       							}
       						?>
									<tr style="padding-left:5px;padding-right:5px">
            				<td class="letra7" align="right"><?php  echo ($n) ?></td>
       							<td class="letra7" align="left"><?php   echo ($mSaldos[$i]['doccruxx'] != "") ? $mSaldos[$i]['doccruxx'] : "&nbsp" ?></td>
  	   							<td class="letra7" align="right"><?php  echo $mSaldos[$i]['comfecxx'] ?></td>
  	   							<td class="letra7" align="center"><?php echo ($mSaldos[$i]['teridxxx'] != "") ? $mSaldos[$i]['teridxxx'] : "&nbsp" ?></td>
  	   							<td class="letra7" align="left"><?php   echo ($mSaldos[$i]['clinomxx'] != "") ? $mSaldos[$i]['clinomxx'] : "&nbsp" ?></td>
  	   							<td class="letra7" align="center"><?php echo ($mSaldos[$i]['pucidxxx'] != "") ? $mSaldos[$i]['pucidxxx'] : "&nbsp" ?></td>
  	   							<td class="letra7" align="center"><?php echo ($mSaldos[$i]['pucdetxx'] != "") ? $mSaldos[$i]['pucdetxx'] : "&nbsp" ?></td>
  	   							<td class="letra7" align="right"><?php  echo number_format($mSaldos[$i]['saldoxxx']) ?></td>
  	   							<td class="letra7" align="right"><?php  echo number_format($mSaldos[$i]['comsaldo']) ?></td>
  	   							
	        				</tr>
       						<?php 
       						}
       					}  ?>
      				</table>  
      				<?php 
						}else{
							echo "No se Generaron registros.";
						}	?>            
    	</body>
  	</html>
 <?php 
 			break;
			case 2:
				// Pinta Por Excel
				if(count($mSaldos)>0){
					$header .= 'REPORTE DE DOCUMENTOS CON SALDOS DESCUADRADOS'."\n";
	  			$header .= "\n";
					$data = '';
					$title = "REPORTE DE DOCUMENTOS CON SALDOS DESCUADRADOS_".$_COOKIE['kUsrId']."_".date("YmdHis").".xls";
					
					//Inicio Carga de Datos
					$data .= '<table width="600" border="1">';
						
					 	$data .= '<tr>';
							$data .= '<td class="name" colspan="17" style="font-size:18px;font-weight:bold"><center>REPORTE DE DOCUMENTOS CON SALDOS DESCUADRADOS</center></td>';
						$data .= '</tr>';
						
						
						$data .= '<tr>';
							$data .= '<td style="border-bottom: hidden" class="name" colspan="17" style="font-size:14px;font-weight:bold">
							<center>TOTAL COMPROBANTES DESCUADRADOS EN EL SISTEMA: '.number_format(count($mSaldos),0,',','.').' de '.number_format($nCanReg,0,',','.').'</center></td>';
						$data .= '</tr>';
						
						$data .= '<tr>';
							$data .= '<td class="name" colspan="17" style="font-size:12px;">FECHA Y HORA DE CONSULTA: '.date('Y-m-d')."-".date('H:i:s').'</td>';
						$data .= '</tr>';
						
						$data .= '<tr>';
							$data .= '<td class="name" colspan="17" style="font-size:13px;">'.$cCadena.'</td>';
						$data .= '</tr>';
					
						$data .= '<tr>';
							$data .= '<td class="name" colspan="17"></td>';
						$data .= '</tr>';
						
						$data .= '<tr>';
							$data .= '<td style="background-color:#0B610B" class="letra8"  align="center"><b><font color=white>ID</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" colspan="2" align="center"><b><font color=white>Documento Cruce</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" colspan="2" align="center"><b><font color=white>Fecha</b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" colspan="2" align="center"><b><font color=white>Tercero</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" colspan="2" align="center"><b><font color=white>Nombre</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" colspan="2" align="center"><b><font color=white>PUC</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" colspan="2" align="center"><b><font color=white>Detalle</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" colspan="2" align="center"><b><font color=white>Saldo Contabilidad</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" colspan="2" align="center"><b><font color=white>Saldo Modulo<br>(CxC / CxP)</font></b></td>';
							
						$data .= '</tr>';
						
						for ($i=0;$i<count($mSaldos);$i++) {
							$nBand = 0;
       				if($gPeriodo == $mSaldos[$i]['comperxx'] && $gComId == $mSaldos[$i]['comidxxx'] && $gComCod == $mSaldos[$i]['comcodxx']){
       					$nBand = 1;
       				}else if ($gPeriodo == $mSaldos[$i]['comperxx'] && $gComId == "" && $gComCod == ""){
       					$nBand = 1;
       				}else if($gPeriodo == $mSaldos[$i]['comperxx'] && $gComId == $mSaldos[$i]['comidxxx'] && $gComCod == ""){
       					$nBand = 1;
       				}else if ($gPeriodo == "" && $gComId == "" && $gComCod == ""){
       					$nBand = 1;
       				}else if ($gPeriodo == "" && $gComId == $mSaldos[$i]['comidxxx'] && $gComCod == $mSaldos[$i]['comcodxx']){
       					$nBand =1;
       				} 
       				
							if($nBand == 1){
								$n++;
								
								if ( $vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' ) {
									// si el ups busco el comcsc3x
									
									for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {
										$mTabMovCsc3[$nAno] = 'fcod'.$nAno;
										$qDatMov  = "SELECT ";
										$qDatMov .= "$cAlfa.{$mTabMovCsc3[$nAno]}.comcsc3x, ";
										$qDatMov .= "$cAlfa.{$mTabMovCsc3[$nAno]}.comcsc2x ";
										$qDatMov .= "FROM $cAlfa.{$mTabMovCsc3[$nAno]} ";
										$qDatMov .= "WHERE ";
										$qDatMov .= "($cAlfa.{$mTabMovCsc3[$nAno]}.comidxxx = \"{$mSaldos[$i]['comidcxx']}\" OR $cAlfa.{$mTabMovCsc3[$nAno]}.comidxxx =\"S\" ) AND ";
										$qDatMov .= "$cAlfa.{$mTabMovCsc3[$nAno]}.comidcxx = \"{$mSaldos[$i]['comidcxx']}\" AND ";
										$qDatMov .= "$cAlfa.{$mTabMovCsc3[$nAno]}.comcodcx = \"{$mSaldos[$i]['comcodcx']}\" AND ";
										$qDatMov .= "$cAlfa.{$mTabMovCsc3[$nAno]}.comcsccx = \"{$mSaldos[$i]['comcsccx']}\" AND ";
										$qDatMov .= "$cAlfa.{$mTabMovCsc3[$nAno]}.teridxxx = \"{$mSaldos[$i]['teridxxx']}\" AND ";
										$qDatMov .= "$cAlfa.{$mTabMovCsc3[$nAno]}.pucidxxx = \"{$mSaldos[$i]['pucidxxx']}\" ";
										$qDatMov .= "LIMIT 0,1";
										
										$xDatMov  = mysql_query($qDatMov,$xConexion01);
										/*echo '<br>';
											echo $qDatMov.' - '.mysql_num_rows($xDatMov);
											echo '<br>';*/
										if (mysql_num_rows($xDatMov) > 0 ) {
											
											$vCre = mysql_fetch_array($xDatMov);
											//print_r($vCre);
											//agrego el comcsc3x en el comprobante 
											$vCre['comcsc3x'] = ($vCre['comcsc3x'] != '') ? $vCre['comcsc3x'] : $vCre['comcsc2x'];
											$mSaldos[$i]['doccruxx'] = $mSaldos[$i]['comidcxx']."-".$mSaldos[$i]['comcodcx']."-".$mSaldos[$i]['comcsccx']."-".$vCre['comcsc3x']."-".$mSaldos[$i]['comseqcx'];
											$nAno = $AnoFin + 1;
										}
									}
   							}

								$data.= '<tr>';
									$data.='<td class="letra7" align="center"><b>'.($n).'</center></b></td>';
									$data.='<td class="letra7" colspan="2" align="left"><b>'.(($mSaldos[$i]['doccruxx'] != "") ? $mSaldos[$i]['doccruxx'] : "&nbsp;").'</b></td>';
									$data.='<td class="letra7" colspan="2" align="right" style="mso-number-format:yyyy-mm-dd" ><b>'.($mSaldos[$i]['comfecxx']).'</center></b></td>';
									$data.='<td class="letra7" colspan="2" align="center"><b>'.(($mSaldos[$i]['teridxxx'] != "") ? $mSaldos[$i]['teridxxx'] : "&nbsp;").'</b></td>';
									$data.='<td class="letra7" colspan="2" align="left"><b>'.(($mSaldos[$i]['clinomxx'] != "") ? $mSaldos[$i]['clinomxx'] : "&nbsp;").'</b></td>';
									$data.='<td class="letra7" colspan="2" align="center"><b>'.(($mSaldos[$i]['pucidxxx'] != "") ? $mSaldos[$i]['pucidxxx'] : "&nbsp;").'</b></td>';
									$data.='<td class="letra7" colspan="2" align="center"><b>'.(($mSaldos[$i]['pucdetxx'] != "") ? $mSaldos[$i]['pucdetxx'] : "&nbsp;").'</b></td>';
									$data.='<td class="letra7" colspan="2" align="right"; style="mso-number-format:@" ><b>'.((strpos(($mSaldos[$i]['saldoxxx']+0),'.') > 0) ? number_format(($mSaldos[$i]['saldoxxx']),2,',','.') : number_format(($mSaldos[$i]['saldoxxx']),0,',','.')).'</b></td>';
									$data.='<td class="letra7" colspan="2" align="right"; style="mso-number-format:@" ><b>'.((strpos(($mSaldos[$i]['comsaldo']+0),'.') > 0) ? number_format(($mSaldos[$i]['comsaldo']),2,',','.') : number_format(($mSaldos[$i]['comsaldo']),0,',','.')).'</b></td>';
								$data.= '</tr>';
							}
						}
						
						
					$data .= '</table>';
					// Fin de Cargar Los datos
					header("Pragma: public");
      		header("Expires: 0");
      		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      		header("Cache-Control: private",false); // required for certain browsers
      		header("Content-type: application/octet-stream");
      		header("Content-Disposition: attachment; filename=\"".basename($title)."\";");
      		print $data;
				}else{
					f_Mensaje(__FILE__,__LINE__,"No se Generaron registros");
				}
			break;  
		}
 	?>
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

		/**
		 * Metodo que realiza la conexion
		 */
		function fnConectarDB(){
			global $cAlfa;

			/**
			 * Variable para saber si hay o no errores de validacion.
			 *
			 * @var number
			 */
			$nSwitch = 0;

			/**
			 * Matriz para Retornar Valores
			 */
			$mReturn = array();
			
			/**
			 * Reservo Primera Posicion para retorna true o false
			 */
			$mReturn[0] = "";
			
			$xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);
			if($xConexion99){
				$nSwitch = 0;
			}else{
				$nSwitch = 1;
				$mReturn[count($mReturn)] = "El Sistema no Logro Conexion con ".OC_SERVER;
			}
			
			if($nSwitch == 0){
				$mReturn[0] = "true"; $mReturn[1] = $xConexion99; 
			}else{
				$mReturn[0] = "false";    
			}
			return $mReturn;
		}##function fnConectarDB(){##
	?> 
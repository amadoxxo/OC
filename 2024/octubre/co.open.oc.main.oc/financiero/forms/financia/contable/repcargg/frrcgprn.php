<?php
  namespace openComex;
  use FPDF;

  /**
	 * Imprime Analisis de Cuentas.
	 * --- Descripcion: Permite Imprimir Reporte Estado de Cartera.
	 * @author Johana Arboleda <johana.arboleda@opentecnologia.com.co>
	 */

	include("../../../../libs/php/utility.php");

	ini_set("memory_limit","512M");
	set_time_limit(0);
	
	date_default_timezone_set("America/Bogota");
  
  $nSwitch = 0; 	// Variable para la Validacion de los Datos
  $cMsj = "\n"; 	// Variable para Guardar los Errores de las Validaciones  
  $mArcCre = array();
	
	$cMes = "";	
	 			
  switch (substr($dHasta,5,2)){
    case "01": $cMes="ENERO";      break;
    case "02": $cMes="FEBRERO";    break;
    case "03": $cMes="MARZO";      break;
    case "04": $cMes="ABRIL";      break;
    case "05": $cMes="MAYO";       break;
    case "06": $cMes="JUNIO";      break;
    case "07": $cMes="JULIO";      break;
    case "08": $cMes="AGOSTO";     break;
    case "09": $cMes="SEPTIEMBRE"; break;
    case "10": $cMes="OCTUBRE";    break;
    case "11": $cMes="NOVIEMBRE";  break;
    case "12": $cMes="DICIEMBRE";  break;
  }
	$cFecha = substr($dHasta,8,2)." de $cMes de ".substr($dHasta,0,4);
  /////INICO DE VALIDACIONES /////  
  ///Inicio Validaciones para condiciones del Reporte ///

  $cTerId = trim($cTerId);
  
  // Inicio Fecha de Corte // 
  if ($dHasta == "") {
  	$nSwitch = 1;
  	$cMsj .= "La Fecha de Corte no puede ser vacio.\n";    	    	
  } else {  	
  	if (substr($dHasta,0,4) < $vSysStr['financiero_ano_instalacion_modulo']) {
	  	$nSwitch = 1;
	  	$cMsj .= "El Ano de la Fecha de Corte no puede ser menor al Ano en que se instalo el Modulo Financiero Contable.\n";    	    	  		
  	} 
  }
  		
  // Fin Fecha de Corte //    
  if ($cTerId == "") {
	  $qDatCli  ="SELECT ";
	  $qDatCli .="SIAI0150.CLIIDXXX, ";
	  $qDatCli .="if($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF($cAlfa.SIAI0150.CLIAPE1X  != \"\",CONCAT($cAlfa.SIAI0150.CLINOM1X, \" \",$cAlfa.SIAI0150.CLINOM2X, \" \",$cAlfa.SIAI0150.CLIAPE1X, \" \",$cAlfa.SIAI0150.CLIAPE2X),\"TERCERO SIN NOMBRE\")) AS clinomxx, ";
	  $qDatCli .="fpar0151.cccggesx ";
	  $qDatCli .="FROM $cAlfa.SIAI0150 ";
	  $qDatCli .="LEFT JOIN $cAlfa.fpar0151 ON $cAlfa.SIAI0150.CLIIDXXX = $cAlfa.fpar0151.cliidxxx ";
	  $qDatCli .="WHERE ";
	  $qDatCli .="fpar0151.cccggesx LIKE  \"%$cGruSer%\" ";
	  $xDatCli = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
	  //f_mensaje(__FILE__,__LINE__,$qDatCli." ~ ".mysql_num_rows($xDatCli));
	  $vCliId = array(); $cCliId = "";
	  while ($xRC = mysql_fetch_array($xDatCli)) {
	  	$vCccGges = array();
	  	$vCccGges = explode("~",$xRC['cccggesx']);
	  	for ($i=0;$i<count($vCccGges);$i++) {
	  		if ($vCccGges[$i] != "") {
	  			if ($vCccGges[$i] == $cGruSer) {
	  				if (in_array("{$xRC['CLIIDXXX']}", $vCliId) == false)
	  				$vCliId[] = "{$xRC['CLIIDXXX']}";
	  				$cCliId  .= "\"{$xRC['CLIIDXXX']}\",";
	  			}
	  		}
	  	}
	  }	
		  
	  $cCliId = substr($cCliId, 0, -1);
	  if ($cCliId == "") {
	  	$nSwitch = 1;
	  	$cMsj .= "No se encontro ningun cliente asociado al Grupo de Gestion [$cGruSer].\n";
	  }
  }
  //Fin de Validaciones para condiciones del Reporte
  /////FIN DE VALIDACIONES /////
  
 if ($nSwitch == 0) {
  	$AnoFin=substr($dHasta,0,4);		
  	$AnoIni= $vSysStr['financiero_ano_instalacion_modulo']; 
  
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
  	
  	if ($cTerId != "") {
	  	//Traigo el Nombre del Cliente
	  	$qNomCli  = "SELECT ";
	  	$qNomCli .= "if($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF($cAlfa.SIAI0150.CLIAPE1X  != \"\",CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X),\"TERCERO SIN NOMBRE\")) AS clinomxx, ";
	  	$qNomCli .= "$cAlfa.SIAI0150.CLITELXX ";
	  	$qNomCli .= "FROM $cAlfa.SIAI0150 ";
	  	$qNomCli .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"$cTerId\" LIMIT 0,1";
	  	$xNomCli = f_MySql("SELECT","",$qNomCli,$xConexion01,"");
	  	$vNomCli = mysql_fetch_array($xNomCli);
			//f_Mensaje(__FILE__,__LINE__,$qNomCli."~".mysql_num_rows($xNomCli));
  	}
		
  	#Buscando solo los saldos de cartera
  	$qSaldos  = "SELECT pucidxxx ";
  	$qSaldos .= "FROM $cAlfa.fpar0119 ";
  	$qSaldos .= "WHERE ";
  	$qSaldos .= "ctoclaxf IN (\"SCLIENTE\",\"SCLIENTEUSD\",\"SAGENCIA\",\"SAGENCIAIP\",\"SAGENCIAPCC\",\"SAGENCIAUSD\",\"SAGENCIAUSDIP\",\"SAGENCIAUSDPCC\") AND ";
  	$qSaldos .= "regestxx = \"ACTIVO\" ";
  	$xSaldos  = f_MySql("SELECT","",$qSaldos,$xConexion01,"");
  	$cPucSal  = "";
  	while ($xRDS = mysql_fetch_array($xSaldos)){
  		$cPucSal .= "\"{$xRDS['pucidxxx']}\",";
  	}
  	$cPucSal = substr($cPucSal, 0, strlen($cPucSal)-1);
  	#Fin Buscando solo los saldos de cartera
  	 
		##Creacion de la tabla detalle del dia
		$mTabMov = array(); //Nombre de las tablas temporales para el movimiento
		$mFacCli = array(); //Datos de Cabecera de las facturas del cleinte
		if ($cPucSal != "") {
			for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {
				
				$cTabFac = fnCadenaAleatoria();
				$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac (";
				$qNewTab .= "comidcxx varchar(1), ";
				$qNewTab .= "comcodcx varchar(4), ";
				$qNewTab .= "comcsccx varchar(20), ";
				$qNewTab .= "teridxxx varchar(12), ";
				$qNewTab .= "pucidxxx varchar(10), ";
				$qNewTab .= "fechasxx text, ";
				$qNewTab .= "commovxx varchar(1), ";
				$qNewTab .= "comvlrxx decimal(15,2), ";
				$qNewTab .= "pucdetxx varchar(2), ";
				$qNewTab .= "pucdesxx varchar(50), ";
				$qNewTab .= "regestxx varchar(12),";
				$qNewTab .= "comidxxx varchar(1),";
				$qNewTab .= "comcodxx varchar(4),";
				$qNewTab .= "comcscxx varchar(20),";
				$qNewTab .= "comfefac date, ";
				$qNewTab .= "comobs2x text, ";
				$qNewTab .= "comfpxxx text, ";
				if ($cTerId == "") {
					$qNewTab .= "clinomxx varchar(60),";
				}
				$qNewTab .= "PRIMARY KEY (comidcxx,comcodcx,comcsccx,teridxxx,pucidxxx,commovxx))";
				$xNewTab = mysql_query($qNewTab,$xConexion01);
				 
				$qDatMov  = "SELECT ";
				$qDatMov .= "$cAlfa.fcod$nAno.comidcxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comcodcx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comcsccx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.teridxxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.pucidxxx, ";			
				$qDatMov .= "GROUP_CONCAT(CONCAT($cAlfa.fcod$nAno.comidxxx,\"-\",$cAlfa.fcod$nAno.comcodxx,\"-\",$cAlfa.fcod$nAno.comcscxx,\"~\",$cAlfa.fcod$nAno.comfecxx,\"~\",$cAlfa.fcod$nAno.comfecve)) AS fechasxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.commovxx, ";						
				$qDatMov .= "SUM(if ($cAlfa.fcod$nAno.commovxx = \"D\", $cAlfa.fcod$nAno.comvlrxx, $cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx, ";
				$qDatMov .= "$cAlfa.fpar0115.pucdetxx, ";
				$qDatMov .= "if($cAlfa.fpar0115.pucdesxx != \"\",$cAlfa.fpar0115.pucdesxx,\"SIN DESCRIPCION\") AS pucdesxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.regestxx,";
				$qDatMov .= "$cAlfa.fcoc$nAno.comidxxx,";			
				$qDatMov .= "$cAlfa.fcoc$nAno.comcodxx,";			
				$qDatMov .= "$cAlfa.fcoc$nAno.comcscxx,";		
				$qDatMov .= "MAX($cAlfa.fcoc$nAno.comfefac),";			
				$qDatMov .= "$cAlfa.fcoc$nAno.comobs2x, ";
				$qDatMov .= "$cAlfa.fcoc$nAno.comfpxxx ";
				if ($cTerId == "") {
					$qDatMov .= ", if($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF($cAlfa.SIAI0150.CLIAPE1X  != \"\",CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X),\"TERCERO SIN NOMBRE\")) AS clinomxx ";
				}
				$qDatMov .= "FROM $cAlfa.fcod$nAno ";
				$qDatMov .= "LEFT JOIN  $cAlfa.fcoc$nAno ON ";
				$qDatMov .= "$cAlfa.fcod$nAno.comidxxx = $cAlfa.fcoc$nAno.comidxxx AND ";
				$qDatMov .= "$cAlfa.fcod$nAno.comcodxx = $cAlfa.fcoc$nAno.comcodxx  AND ";
				$qDatMov .= "$cAlfa.fcod$nAno.comcscxx = $cAlfa.fcoc$nAno.comcscxx  AND ";
				$qDatMov .= "$cAlfa.fcod$nAno.comcsc2x = $cAlfa.fcoc$nAno.comcsc2x ";				
				$qDatMov .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fcod$nAno.pucidxxx ";
				if ($cTerId == "") {
					$qDatMov .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$nAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
				}
				$qDatMov .= "WHERE $cAlfa.fcod$nAno.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
				
				//$qDatMov .= "$cAlfa.fcod$nAno.comidcxx = \"F\" AND ";
				//$qDatMov .= "$cAlfa.fcod$nAno.comcodcx = \"031\" AND ";
				//$qDatMov .= "$cAlfa.fcod$nAno.comcsccx = \"117923\" AND ";
			  if ($cTerId == "") {
			  	$qDatMov .= "$cAlfa.fcod$nAno.teridxxx IN ($cCliId) AND ";
			  } else {
			  	$qDatMov .= "$cAlfa.fcod$nAno.teridxxx = \"$cTerId\" AND ";
			  }
			  
				$qDatMov .= "$cAlfa.fcod$nAno.comfecxx <= \"$dHasta\" AND ";	
				$qDatMov .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" AND ";			
				$qDatMov .= "$cAlfa.fcod$nAno.pucidxxx IN ($cPucSal) AND ";
				$qDatMov .= "$cAlfa.fpar0115.pucdetxx IN (\"C\",\"P\") ";
				$qDatMov .= "GROUP BY $cAlfa.fcod$nAno.comidcxx,$cAlfa.fcod$nAno.comcodcx,$cAlfa.fcod$nAno.comcsccx,$cAlfa.fcod$nAno.pucidxxx ";			
				$qDatMov .= "ORDER BY $cAlfa.fcod$nAno.comidcxx,$cAlfa.fcod$nAno.comcodcx,$cAlfa.fcod$nAno.comcsccx,$cAlfa.fcod$nAno.pucidxxx ";
				//f_mensaje(__FILE__,__LINE__,$qDatMov);
				$qInsert = "INSERT INTO $cAlfa.$cTabFac $qDatMov";
			  $xInsert = mysql_query($qInsert,$xConexion01);
			  $mTabMov[$nAno] = $cTabFac;
				##Fin Creacion de la tabla detalle del dia
	  	}
	  	
			##Fin Acciones sobre la DB en el paso Dos		
			$mDatMov = array();
			$mDatMov = array();
			for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) { 
				$qDatMov  = "SELECT ";
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.comidcxx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.comcodcx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.comcsccx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.teridxxx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.pucidxxx, ";			
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.pucdetxx, ";		
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.pucdesxx, ";		
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.fechasxx, ";				
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.commovxx, ";						
				$qDatMov .= "SUM($cAlfa.{$mTabMov[$nAno]}.comvlrxx) as saldoxxx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.regestxx,";				
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.comidxxx,";
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.comcodxx,";
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.comcscxx,";
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.comfefac,";				
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.comobs2x, ";	
				$qDatMov .= "$cAlfa.{$mTabMov[$nAno]}.comfpxxx ";
				if ($cTerId == "") {
					$qDatMov .= ", $cAlfa.{$mTabMov[$nAno]}.clinomxx ";
				}
				$qDatMov .= "FROM $cAlfa.{$mTabMov[$nAno]} ";
				$qDatMov .= "GROUP BY $cAlfa.{$mTabMov[$nAno]}.comidcxx,$cAlfa.{$mTabMov[$nAno]}.comcodcx,$cAlfa.{$mTabMov[$nAno]}.comcsccx,$cAlfa.{$mTabMov[$nAno]}.teridxxx,$cAlfa.{$mTabMov[$nAno]}.pucidxxx ";
				$qDatMov .= "ORDER BY $cAlfa.{$mTabMov[$nAno]}.comfefac ";
		  	$xDatMov = mysql_query($qDatMov,$xConexion01);
				
		  	while ($xCre = mysql_fetch_array($xDatMov)) {
		  		//Buscando la fecha del comprobante 
					$mAuxFec = explode(",", $xCre['fechasxx']);
					$dFecCre    = ""; $dFecVen    = ""; 					
					for ($nF=0; $nF<count($mAuxFec); $nF++) {
						if ($mAuxFec[$nF] != "") {
							$mAuxCom = array();
							$mAuxCom = explode("~", $mAuxFec[$nF]);
							$dFecCre = $mAuxCom[1]; 
							$dFecVen = $mAuxCom[2];
							if ($mAuxCom[0] == $xCre['comidcxx']."-".$xCre['comcodcx']."-".$xCre['comcsccx']) {
								//Encontro fecha comprobante
								$xCre['comfecxx'] = $mAuxCom[1]; 
								$xCre['comfecve'] = $mAuxCom[2];
								$nF = count($mAuxFec);
							}
						}
					}
					
				  $nDias = 0;
					$cKey = $xCre['comidcxx']."-".$xCre['comcodcx']."-".$xCre['comcsccx']."-".$xCre['teridxxx']."-".$xCre['pucidxxx'];
					if($mDatMov[$cKey]['comidcxx'] == '') {
						$mDatMov[$cKey]['comidcxx']  = $xCre['comidcxx'];
						$mDatMov[$cKey]['comcodcx']  = $xCre['comcodcx'];
						$mDatMov[$cKey]['comcsccx']  = $xCre['comcsccx'];
						$mDatMov[$cKey]['teridxxx']  = $xCre['teridxxx'];
						$mDatMov[$cKey]['pucidxxx']  = $xCre['pucidxxx'];
						$mDatMov[$cKey]['pucdetxx']  = $xCre['pucdetxx'];
						$mDatMov[$cKey]['pucdesxx']  = $xCre['pucdesxx'];
						$mDatMov[$cKey]['commovxx']  = $xCre['commovxx'];
						$mDatMov[$cKey]['comobs2x']  = $xCre['comobs2x'];
						if ($cTerId == "") {
							$mDatMov[$cKey]['clinomxx']  = $xCre['clinomxx'];
						}
						$mDatMov[$cKey]['comfpxxx']  = $xCre['comfpxxx'];
						$mDatMov[$cKey]['comfefac']  = $xCre['comfefac'];
						$mDatMov[$cKey]['regestxx']  = $xCre['regestxx'];						
						$mDatMov[$cKey]['fechasxx']  = $xCre['fechasxx'];						
					}

					$mDatMov[$cKey]['comfecxx']  = ($xCre['comfecxx'] != "") ? $xCre['comfecxx'] : $dFecCre;
					$mDatMov[$cKey]['comfecve']  = ($xCre['comfecve'] != "") ? $xCre['comfecve'] : $dFecVen;								
					$mDatMov[$cKey]['saldoxxx'] += $xCre['saldoxxx'];				
				}  	
	  	}

		
	  	//// Empiezo a Recorrer la Matriz de Creditos Vs Debitos para Dejar las Cuentas que a la fecha de Corte tienen Saldo ////		  	
	  	$mCarteraVencida   = array(); //Cartera que tiene uno o mas dias de vencimiento
	  	$mCarteraSinVencer = array(); //Carter que no se ha vencido
	  	$mSaldosaFavor     = array(); //Saldos a Favor del Cliente, valores negativos
	  	foreach ($mDatMov as $i => $cValue) {	  		
	  		if ($mDatMov[$i]['saldoxxx'] != 0) {
	  			
					//Fechas de vencimeinto de SIACO, se calcula con la fecha de entrega de factura al cliente
					//Buscando Pedido
					if ($mDatMov[$i]['comobs2x'] != "") {
						$vAuxPed = explode("~",$mDatMov[$i]['comobs2x']);
						if($vAuxPed[8] != ""){
							$mDatMov[$i]['pedidoxx'] = $vAuxPed[8];
						} else {
							$vFrmPag = explode("~",$mDatMov[$i]['comfpxxx']);
							$mDatMov[$i]['pedidoxx'] = $vFrmPag[7];
						}
				 	}
					 
					//Calculando cuantos dias son para el vencimiento				  			
		  		$dComFec  = str_replace("-","",$mDatMov[$i]['comfecxx']);
		  		$dConFeVe = str_replace("-","",$mDatMov[$i]['comfecve']);
		  		$nDias    = round((mktime(0,0,0,substr($dConFeVe,4,2),substr($dConFeVe,6,2), substr($dConFeVe,0,4))  - mktime(0,0,0,substr($dComFec,4,2), substr($dComFec,6,2),  substr($dComFec,0,4))) / (60 * 60 * 24));
				
					if ($mDatMov[$i]['comfefac'] == "0000-00-00") {
						$mDatMov[$i]['comfecnx'] = "0000-00-00";
						$mDatMov[$i]['comfecvn'] = "0000-00-00";
					} else {
						$mDatMov[$i]['comfecnx'] = $mDatMov[$i]['comfefac'];
						$dConFeVe = str_replace("-","",$mDatMov[$i]['comfefac']);
						$mDatMov[$i]['comfecvn'] = date("Y-m-d",mktime(0,0,0,substr($dConFeVe,4,2),substr($dConFeVe,6,2)+$nDias, substr($dConFeVe,0,4)));
					}
					
					$dComFecVe = $mDatMov[$i]['comfecnx'];
					$dComFec   = $mDatMov[$i]['comfecvn'];			
					    			
	  		  $valorVen = 0;					
	  			if ($dComFecVe != "0000-00-00" && $dComFec != "0000-00-00") {
		  			$dFecCor = str_replace("-","",$dComFecVe);
		  			$dFecCar = str_replace("-","",$dComFec);
		  			
		  			$dateCor = mktime(0,0,0,substr($dFecCor,4,2), substr($dFecCor,6,2), substr($dFecCor,0,4));
						$dateCar = mktime(0,0,0,substr($dFecCar,4,2), substr($dFecCar,6,2), substr($dFecCar,0,4));
						$valorCar= round(($dateCor  - $dateCar) / (60 * 60 * 24));
		  			
						$dFecCor = str_replace("-","",$dHasta);
						$dFecVen = str_replace("-","",$dComFecVe);
						
						$dateCor = mktime(0,0,0,substr($dFecCor,4,2), substr($dFecCor,6,2), substr($dFecCor,0,4));
						$dateVen = mktime(0,0,0,substr($dFecVen,4,2), substr($dFecVen,6,2), substr($dFecVen,0,4));
						$valorVen= round(($dateCor  - $dateVen) / (60 * 60 * 24));
	  			} 
	  			
	  			$vFrmPag = explode("|",$mDatMov[$i]['comfpxxx']);
	  			$vFrmPa1 = explode("~",$vFrmPag[1]); 
	  			$mDatMov[$i]['doidxxxx'] = $vFrmPa1[2];
	  			//echo $mDatMov[$i]['comcsccx']."~".$mDatMov[$i]['comfecve']."~".$mDatMov[$i]['comfecxx']."<br>";
									
	  			$mDatMov[$i]['commovxx'] = ($mDatMov[$i]['saldoxxx'] > 0) ? "D" : "C";
	  			 
					if ($mDatMov[$i]['saldoxxx'] < 0 || $mDatMov[$i]['pucdetxx'] == "P") { //es un saldo a favor del cliente
						$nInd_mSaldosaFavor = count($mSaldosaFavor);
						$mSaldosaFavor[$nInd_mSaldosaFavor]['comidxxx']=$mDatMov[$i]['comidcxx'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['comcodxx']=$mDatMov[$i]['comcodcx'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['comcscxx']=$mDatMov[$i]['comcsccx'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['document']=$mDatMov[$i]['comidcxx']."-".$mDatMov[$i]['comcodcx']."-".$mDatMov[$i]['comcsccx'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['comfecxx']=$mDatMov[$i]['comfecxx'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['comfecve']=$mDatMov[$i]['comfecve'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['comfecnx']=$mDatMov[$i]['comfecnx'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['comfecvn']=$mDatMov[$i]['comfecvn'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['diascart']=$valorCar;
						$mSaldosaFavor[$nInd_mSaldosaFavor]['diasvenc']=0;
						$mSaldosaFavor[$nInd_mSaldosaFavor]['teridxxx']=$mDatMov[$i]['teridxxx'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['pucidxxx']=$mDatMov[$i]['pucidxxx'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['pucdesxx']=$mDatMov[$i]['pucdesxx'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['commovxx']=$mDatMov[$i]['commovxx'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['saldoxxx']=abs($mDatMov[$i]['saldoxxx']);
						$mSaldosaFavor[$nInd_mSaldosaFavor]['regestxx']=$mDatMov[$i]['regestxx'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['pedidoxx']=$mDatMov[$i]['pedidoxx'];
						$mSaldosaFavor[$nInd_mSaldosaFavor]['doidxxxx']=$mDatMov[$i]['doidxxxx'];
						if ($cTerId == "") {
							$mSaldosaFavor[$nInd_mSaldosaFavor]['clinomxx']=$mDatMov[$i]['clinomxx'];
						}
					} else if ($valorVen > 0) { //Cartera vencida
						$nInd_mCarteraVencida = count($mCarteraVencida);
						$mCarteraVencida[$nInd_mCarteraVencida]['comidxxx']=$mDatMov[$i]['comidcxx'];
						$mCarteraVencida[$nInd_mCarteraVencida]['comcodxx']=$mDatMov[$i]['comcodcx'];
						$mCarteraVencida[$nInd_mCarteraVencida]['comcscxx']=$mDatMov[$i]['comcsccx'];
						$mCarteraVencida[$nInd_mCarteraVencida]['document']=$mDatMov[$i]['comidcxx']."-".$mDatMov[$i]['comcodcx']."-".$mDatMov[$i]['comcsccx'];
						$mCarteraVencida[$nInd_mCarteraVencida]['comfecxx']=$mDatMov[$i]['comfecxx'];
						$mCarteraVencida[$nInd_mCarteraVencida]['comfecve']=$mDatMov[$i]['comfecve'];
						$mCarteraVencida[$nInd_mCarteraVencida]['comfecnx']=$mDatMov[$i]['comfecnx'];
						$mCarteraVencida[$nInd_mCarteraVencida]['comfecvn']=$mDatMov[$i]['comfecvn'];
						$mCarteraVencida[$nInd_mCarteraVencida]['diascart']=$valorCar;
						$mCarteraVencida[$nInd_mCarteraVencida]['diasvenc']=$valorVen;
						$mCarteraVencida[$nInd_mCarteraVencida]['teridxxx']=$mDatMov[$i]['teridxxx'];
						$mCarteraVencida[$nInd_mCarteraVencida]['pucidxxx']=$mDatMov[$i]['pucidxxx'];
						$mCarteraVencida[$nInd_mCarteraVencida]['pucdesxx']=$mDatMov[$i]['pucdesxx'];
						$mCarteraVencida[$nInd_mCarteraVencida]['commovxx']=$mDatMov[$i]['commovxx'];
						$mCarteraVencida[$nInd_mCarteraVencida]['saldoxxx']=abs($mDatMov[$i]['saldoxxx']);
						$mCarteraVencida[$nInd_mCarteraVencida]['regestxx']=$mDatMov[$i]['regestxx'];
						$mCarteraVencida[$nInd_mCarteraVencida]['pedidoxx']=$mDatMov[$i]['pedidoxx'];
						$mCarteraVencida[$nInd_mCarteraVencida]['doidxxxx']=$mDatMov[$i]['doidxxxx'];//$mSaldosaFavor[$nInd_mSaldosaFavor]['doidxxxx']=$mDatMov[$i]['doidxxxx']
						if ($cTerId == "") {
							$mCarteraVencida[$nInd_mCarteraVencida]['clinomxx']=$mDatMov[$i]['clinomxx'];
						}
					} else { //Cartera no vencida
						$nInd_mCarteraSinVencer = count($mCarteraSinVencer);
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['comidxxx']=$mDatMov[$i]['comidcxx'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['comcodxx']=$mDatMov[$i]['comcodcx'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['comcscxx']=$mDatMov[$i]['comcsccx'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['document']=$mDatMov[$i]['comidcxx']."-".$mDatMov[$i]['comcodcx']."-".$mDatMov[$i]['comcsccx'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['comfecxx']=$mDatMov[$i]['comfecxx'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['comfecve']=$mDatMov[$i]['comfecve'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['comfecnx']=$mDatMov[$i]['comfecnx'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['comfecvn']=$mDatMov[$i]['comfecvn'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['diascart']=$valorCar;
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['diasvenc']=$valorVen;
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['teridxxx']=$mDatMov[$i]['teridxxx'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['pucidxxx']=$mDatMov[$i]['pucidxxx'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['pucdesxx']=$mDatMov[$i]['pucdesxx'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['commovxx']=$mDatMov[$i]['commovxx'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['saldoxxx']=abs($mDatMov[$i]['saldoxxx']);
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['regestxx']=$mDatMov[$i]['regestxx'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['pedidoxx']=$mDatMov[$i]['pedidoxx'];
						$mCarteraSinVencer[$nInd_mCarteraSinVencer]['doidxxxx']=$mDatMov[$i]['doidxxxx'];
						if ($cTerId == "") {
							$mCarteraSinVencer[$nInd_mCarteraSinVencer]['clinomxx']=$mDatMov[$i]['clinomxx'];
						}
					}
	  		}  		
	 		}  
	 		//// Fin Recorrer la Matriz de Creditos-Debitos para Dejar las Cuentas que a la fecha de Corte tienen Saldo ////  		  		 	  	  	
	  	/////FIN DE CALCULOS PARA ARMAR EL ARCHIVO /////
		}
		
 		///Recibos Provisionales a la fecha de corte
 		$mRecProv = array();
 		for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {
 			$qProvCab  = "SELECT ";
 			$qProvCab .= "$cAlfa.fcoc$nAno.comidxxx, ";
 			$qProvCab .= "$cAlfa.fcoc$nAno.comcodxx, ";
 			$qProvCab .= "$cAlfa.fcoc$nAno.comcscxx, ";
 			$qProvCab .= "$cAlfa.fcoc$nAno.comcsc2x, ";
 			$qProvCab .= "$cAlfa.fcoc$nAno.teridxxx, ";
 			$qProvCab .= "$cAlfa.fcoc$nAno.comfecxx, ";
 			$qProvCab .= "$cAlfa.fcoc$nAno.comfecve, ";
 			$qProvCab .= "$cAlfa.fcoc$nAno.comvlr01  ";
 			$qProvCab .= "FROM $cAlfa.fcoc$nAno ";
 			$qProvCab .= "WHERE ";
 			$qProvCab .= "$cAlfa.fcoc$nAno.comidxxx != \"F\" AND ";
 			if ($cTerId == "") {
 				$qProvCab .= "$cAlfa.fcoc$nAno.teridxxx IN ($cCliId) AND ";
 			} else {
 				$qProvCab .= "$cAlfa.fcoc$nAno.teridxxx = \"$cTerId\" AND ";
 			}
 			$qProvCab .= "$cAlfa.fcoc$nAno.regestxx = \"PROVISIONAL\" ";
 			$xProvCab = f_MySql("SELECT","",$qProvCab,$xConexion01,"");
 			
 			while ($xRDM = mysql_fetch_array($xProvCab)) {
 				if ($xRDM['comvlr01'] != 0) {
 					$nInd_mRecProv = count($mRecProv);
 					$mRecProv[$nInd_mRecProv]['comidxxx']=$xRDM['comidxxx'];
 					$mRecProv[$nInd_mRecProv]['comcodxx']=$xRDM['comcodxx'];
 					$mRecProv[$nInd_mRecProv]['comcscxx']=$xRDM['comcscxx'];
 					$mRecProv[$nInd_mRecProv]['comfecxx']=$xRDM['comfecxx'];
 					$mRecProv[$nInd_mRecProv]['document']=$xRDM['comidxxx']."-".$xRDM['comcodxx']."-".$xRDM['comcscxx'];
 					$mRecProv[$nInd_mRecProv]['comfecve']=$xRDM['comfecve'];
 					$mRecProv[$nInd_mRecProv]['diascart']="";
 					$mRecProv[$nInd_mRecProv]['diasvenc']="";
 					$mRecProv[$nInd_mRecProv]['teridxxx']=$xRDM['teridxxx'];
 					$mRecProv[$nInd_mRecProv]['commovxx']=($xRDM['comvlr01'] > 0) ? "D" : "C";
 					$mRecProv[$nInd_mRecProv]['saldoxxx']=abs($xRDM['comvlr01']);
 				}
 			}
 		}
 		///Recibos Provisionales a la fecha de corte
 		
 		
 		if (count($mSaldosaFavor) > 0) {
   		$mSaldosaFavor     = f_ordenar_array_bidimensional($mSaldosaFavor,'pucidxxx',SORT_ASC,'diasvenc',SORT_DESC,'comfecxx',SORT_ASC,'document',SORT_ASC);
		}	
		if (count($mCarteraVencida) > 0) {
   		$mCarteraVencida   = f_ordenar_array_bidimensional($mCarteraVencida,'pucidxxx',SORT_ASC,'diasvenc',SORT_DESC,'comfecxx',SORT_ASC,'document',SORT_ASC);
		}	
		if (count($mCarteraSinVencer) > 0) {
   		$mCarteraSinVencer = f_ordenar_array_bidimensional($mCarteraSinVencer,'pucidxxx',SORT_ASC,'diasvenc',SORT_DESC,'comfecxx',SORT_ASC,'document',SORT_ASC);
   	}
		if (count($mRecProv) > 0) {
   		$mRecProv          = f_ordenar_array_bidimensional($mRecProv,'comfecxx',SORT_ASC,'document',SORT_ASC);
		}
   	
		switch ($_POST['rTipo']) {
		  case 1:
			  // PINTA POR PANTALLA//  ?>
			  <script language="javascript">
	  			function f_Ver(xComId,xComCod,xComCsc,xComFec,xRegEst,xTipCom) {
					  
		  			var xComId = xComId;
		  			var xComCod = xComCod;
		  			var xComCsc = xComCsc;
		  			var xComFec = xComFec;
		  			var xRegEst = xRegEst;
		  			var xTipCom = xTipCom;
		  			
						var ruta  = "frvercom.php?xComId="+xComId+"&xComCod="+xComCod+"&xComCsc="+xComCsc+"&xComFec="+xComFec+"&xRegEst="+xRegEst+"&xTipCom="+xTipCom;
						
		  	    //document.location = ruta; // Invoco el menu.
						//
			  	  var zX    = screen.width;
						var zY    = screen.height;
						var zNx     = (zX-550)/2;
					  var zNy     = (zY-350)/2;
						var zWinPro = 'width=550,scrollbars=1,height=350,left='+zNx+',top='+zNy;
						//var cNomVen = 'zWindowcom';
						var cNomVen = 'zWindow'+Math.ceil(Math.random()*1000);
						zWindow = window.open(ruta,cNomVen,zWinPro);
						zWindow.focus();
					}
  			</script>
				<html>
					<title>Reporte Estado de Cartera</title>
					<head>
						<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
						<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
						<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
				  </head>
				  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
						<?php  
						$nCol     = ($cTerId == "") ? 13 : 11; 											
						$nColProv = ($cTerId == "") ? 1 : 4; 
						?>											
						<center>
							<table border = "1" cellpadding = "0" cellspacing = "0" width = "98%">
								<tr>
									<td class="name" style="font-size:14px" colspan="<?php echo $nCol ?>">
										<center><br><span style="font-size:18px"><?php echo "REPORTE DE ESTADO DE CARTERA AL ". $cFecha ?></span></center><br>
									</td>
								</tr>
							<?php if ($cTerId != "") { ?>							
								<tr>
									<td align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>CONSULTA POR CLIENTE</b></td>
								</tr>
								<tr>
									<td align="center" bgcolor = "#D6DFF7"><b>Cliente</b></td>
									<td align="center" style="width=200px" colspan = "4"><b><?php echo $vNomCli['clinomxx']?></b></td>
									<td align="center" bgcolor = "#D6DFF7"><b>Tel&eacute;fono</b></td>
									<td align="center" style="width=200px" colspan = "2"><b><?php echo $vNomCli['CLITELXX']?></b></td>
									<td align="center" bgcolor = "#D6DFF7"><b>Fecha y Hora de Consulta</b></td>
									<td align="center" style="width=200px" colspan = "2"><b><?php echo date("Y-m-d H:i:s");?></b></td>
								</tr>	
							<?php } ?>
								<tr><td colspan = "<?php echo $nCol ?>">&nbsp;</td></tr>
								<tr>
									<td align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>CARTERA VENCIDA</b></td>
								</tr>
								<tr>
									<?php if ($cTerId == "")  { ?>
										<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Nit</b></td>
										<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cliente</b></td>
									<?php } ?>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Do</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Pedido</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Comprobante</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cuenta</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Fecha</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Entrega Cliente</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Vencimiento</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>D&iacute;as Cartera</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>D&iacute;as Vencidos</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Movimiento</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Saldo</b></td>
								</tr>
								<?php for($i=0;$i<count($mCarteraVencida);$i++){ 
	
									if($mCarteraVencida[$i]['comidxxx'] == 'P' || $mCarteraVencida[$i]['comidxxx'] == 'L' || $mCarteraVencida[$i]['comidxxx'] == 'C'){
										$cTipCom = $mComP[$mCarteraVencida[$i]['comidxxx']][$mCarteraVencida[$i]['comcodxx']];
									
										if (in_array("{$mCarteraVencida[$i]['comidxxx']}~{$mCarteraVencida[$i]['comcodxx']}", $mRCM) == true) {
											$cTipCom = "RCM";
										}
									}else{
										$cTipCom = "";
									} ?>	
									<tr>
										<?php if ($cTerId == "")  { ?>
											<td align="left"><?php echo $mCarteraVencida[$i]['teridxxx'] ?></td>
											<td align="left"><?php echo $mCarteraVencida[$i]['clinomxx'] ?></td>
										<?php } ?>
										<td align="center"><?php echo ($mCarteraVencida[$i]['doidxxxx'] != "") ? $mCarteraVencida[$i]['doidxxxx'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mCarteraVencida[$i]['pedidoxx'] != "") ? $mCarteraVencida[$i]['pedidoxx'] : "&nbsp;" ?></td>
										<td align="left"><?php echo ($mCarteraVencida[$i]['document'] != "")?"<a href=\"javascript:f_Ver('{$mCarteraVencida[$i]['comidxxx']}','{$mCarteraVencida[$i]['comcodxx']}','{$mCarteraVencida[$i]['comcscxx']}','{$mCarteraVencida[$i]['comfecxx']}','{$mCarteraVencida[$i]['regestxx']}','$cTipCom');\">{$mCarteraVencida[$i]['document']}</a>": "&nbsp;"; ?></td>
										<td align="center"><?php echo ($mCarteraVencida[$i]['pucidxxx'] != "") ? $mCarteraVencida[$i]['pucidxxx'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mCarteraVencida[$i]['comfecxx'] != "") ? $mCarteraVencida[$i]['comfecxx'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mCarteraVencida[$i]['comfecnx'] != "") ? $mCarteraVencida[$i]['comfecnx'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mCarteraVencida[$i]['comfecvn'] != "") ? $mCarteraVencida[$i]['comfecvn'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mCarteraVencida[$i]['diascart'] != "") ? $mCarteraVencida[$i]['diascart'] : "&nbsp;" ?></td>
										<td align="center"><font color="red"><?php echo ($mCarteraVencida[$i]['diasvenc'] != "") ? $mCarteraVencida[$i]['diasvenc'] : "&nbsp;" ?></font></td>
										<td align="center"><?php echo ($mCarteraVencida[$i]['commovxx'] != "") ? $mCarteraVencida[$i]['commovxx'] : "&nbsp;" ?></td>
										<td align="right"><?php echo number_format($mCarteraVencida[$i]['saldoxxx'],2,",",".")?></td>
									</tr>
									<?php $nTotCarVencida += ($mCarteraVencida[$i]['commovxx'] == "D") ? $mCarteraVencida[$i]['saldoxxx'] : ($mCarteraVencida[$i]['saldoxxx'] * -1);
								} ?>
								<tr>
									<td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA VENCIDA: </b></td>
									<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotCarVencida,2,",",".")?></b></td>
								</tr>
								<tr><td colspan = "<?php echo $nCol ?>">&nbsp;</td></tr>
								<tr>
									<td align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>CARTERA SIN VENCER</b></td>
								</tr>
								<tr>
									<?php if ($cTerId == "")  { ?>
										<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Nit</b></td>
										<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cliente</b></td>
									<?php } ?>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Do</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Pedido</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Comprobante</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cuenta</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Fecha</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Entrega Cliente</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Vencimiento</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>D&iacute;as Cartera</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>D&iacute;as Vencidos</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Movimiento</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Saldo</b></td>
								</tr>
								<?php for($i=0;$i<count($mCarteraSinVencer);$i++){
									if($mCarteraSinVencer[$i]['comidxxx'] == 'P' || $mCarteraSinVencer[$i]['comidxxx'] == 'L' || $mCarteraSinVencer[$i]['comidxxx'] == 'C'){
										$cTipCom = $mComP[$mCarteraSinVencer[$i]['comidxxx']][$mCarteraSinVencer[$i]['comcodxx']];
									
										if (in_array("{$mCarteraSinVencer[$i]['comidxxx']}~{$mCarteraSinVencer[$i]['comcodxx']}", $mRCM) == true) {
											$cTipCom = "RCM";
										}
									}else{
										$cTipCom = "";
									} ?>
									<tr>
										<?php if ($cTerId == "")  { ?>
											<td align="left"><?php echo $mCarteraSinVencer[$i]['teridxxx'] ?></td>
											<td align="left"><?php echo $mCarteraSinVencer[$i]['clinomxx'] ?></td>
										<?php } ?>
										<td align="center"><?php echo ($mCarteraSinVencer[$i]['doidxxxx'] != "") ? $mCarteraSinVencer[$i]['doidxxxx'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mCarteraSinVencer[$i]['pedidoxx'] != "") ? $mCarteraSinVencer[$i]['pedidoxx'] : "&nbsp;" ?></td>
										<td align="left"><?php echo ($mCarteraSinVencer[$i]['document'] != "")?"<a href=\"javascript:f_Ver('{$mCarteraSinVencer[$i]['comidxxx']}','{$mCarteraSinVencer[$i]['comcodxx']}','{$mCarteraSinVencer[$i]['comcscxx']}','{$mCarteraSinVencer[$i]['comfecxx']}','{$mCarteraSinVencer[$i]['regestxx']}','$cTipCom')\">{$mCarteraSinVencer[$i]['document']}</a>": "&nbsp;"; ?></td>
										<td align="center"><?php echo ($mCarteraSinVencer[$i]['pucidxxx'] != "") ? $mCarteraSinVencer[$i]['pucidxxx'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mCarteraSinVencer[$i]['comfecxx'] != "") ? $mCarteraSinVencer[$i]['comfecxx'] : "&nbsp;"; ?></td>
										<td align="center"><?php echo ($mCarteraSinVencer[$i]['comfecnx'] != "") ? $mCarteraSinVencer[$i]['comfecnx'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mCarteraSinVencer[$i]['comfecvn'] != "") ? $mCarteraSinVencer[$i]['comfecvn'] : "&nbsp;"; ?></td>
										<td align="center"><?php echo ($mCarteraSinVencer[$i]['diascart'] != "") ? $mCarteraSinVencer[$i]['diascart'] : "&nbsp;"; ?></td>
										<td align="center"><?php echo ($mCarteraSinVencer[$i]['diasvenc'] != "") ? $mCarteraSinVencer[$i]['diasvenc'] : "&nbsp;"; ?></td>
										<td align="center"><?php echo ($mCarteraSinVencer[$i]['commovxx'] != "") ? $mCarteraSinVencer[$i]['commovxx'] : "&nbsp;"; ?></td>
										<td align="right"><?php echo number_format($mCarteraSinVencer[$i]['saldoxxx'],2,",",".")?></td>
									</tr>
									<?php $nTotCartera += ($mCarteraSinVencer[$i]['commovxx'] == "D") ? $mCarteraSinVencer[$i]['saldoxxx'] : ($mCarteraSinVencer[$i]['saldoxxx'] * -1);
								} ?>
								<tr>
									<td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA SIN VENCER: </b></td>
									<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotCartera,2,",",".")?></b></td>
								</tr>
								<tr><td colspan = "<?php echo $nCol ?>">&nbsp;</td></tr>
								<tr>
									<td align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>SALDOS A FAVOR</b></td>
								</tr>
								<tr>
									<?php if ($cTerId == "")  { ?>
										<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Nit</b></td>
										<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cliente</b></td>
									<?php } ?>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Do</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Pedido</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Comprobante</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cuenta</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Fecha</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Entrega Cliente</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Vencimiento</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>D&iacute;as Cartera</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>D&iacute;as Vencidos</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Movimiento</b></td>
									<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Saldo</b></td>
								</tr>
								<?php for($i=0;$i<count($mSaldosaFavor);$i++){
									if($mSaldosaFavor[$i]['comidxxx'] == 'P' || $mSaldosaFavor[$i]['comidxxx'] == 'L' || $mSaldosaFavor[$i]['comidxxx'] == 'C'){
										$cTipCom = $mComP[$mSaldosaFavor[$i]['comidxxx']][$mSaldosaFavor[$i]['comcodxx']];
									
										if (in_array("{$mSaldosaFavor[$i]['comidxxx']}~{$mSaldosaFavor[$i]['comcodxx']}", $mRCM) == true) {
											$cTipCom = "RCM";
										}
									}else{
										$cTipCom = "";
									} ?>
									<tr>
										<?php if ($cTerId == "")  { ?>
											<td align="left"><?php echo $mSaldosaFavor[$i]['teridxxx'] ?></td>
											<td align="left"><?php echo $mSaldosaFavor[$i]['clinomxx'] ?></td>
										<?php } ?>
										<td align="center"><?php echo ($mSaldosaFavor[$i]['doidxxxx'] != "") ? $mSaldosaFavor[$i]['doidxxxx'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mSaldosaFavor[$i]['pedidoxx'] != "") ? $mSaldosaFavor[$i]['pedidoxx'] : "&nbsp;" ?></td>
										<td align="left"><?php echo ($mSaldosaFavor[$i]['document'] != "")?"<a href=\"javascript:f_Ver('{$mSaldosaFavor[$i]['comidxxx']}','{$mSaldosaFavor[$i]['comcodxx']}','{$mSaldosaFavor[$i]['comcscxx']}','{$mSaldosaFavor[$i]['comfecxx']}','{$mSaldosaFavor[$i]['regestxx']}','$cTipCom')\">{$mSaldosaFavor[$i]['document']}</a>": "&nbsp;"; ?></td>
										<td align="center"><?php echo $mSaldosaFavor[$i]['pucidxxx']?></td>
										<td align="center"><?php echo ($mSaldosaFavor[$i]['comfecxx'] != "") ? $mSaldosaFavor[$i]['comfecxx'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mSaldosaFavor[$i]['comfecnx'] != "") ? $mSaldosaFavor[$i]['comfecnx'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mSaldosaFavor[$i]['comfecvn'] != "") ? $mSaldosaFavor[$i]['comfecvn'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mSaldosaFavor[$i]['diascart'] != "") ? $mSaldosaFavor[$i]['diascart'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mSaldosaFavor[$i]['diasvenc'] != "") ? $mSaldosaFavor[$i]['diasvenc'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mSaldosaFavor[$i]['commovxx'] != "") ? $mSaldosaFavor[$i]['commovxx'] : "&nbsp;" ?></td>
										<td align="right"><?php echo number_format($mSaldosaFavor[$i]['saldoxxx'],2,",",".")?></td>
									</tr>
									<?php $nTotSaldos += ($mSaldosaFavor[$i]['commovxx'] == "D") ? $mSaldosaFavor[$i]['saldoxxx'] : ($mSaldosaFavor[$i]['saldoxxx'] * -1);
								} ?>
								<tr>
									<td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS A FAVOR: </b></td>
									<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotSaldos,2,",",".")?></b></td>
								</tr>
								<tr><td colspan = "<?php echo $nCol ?>">&nbsp;</td></tr>
								<tr>
									<td align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>RECIBOS PROVISIONALES</b></td>
								</tr>
								<tr>
									<?php if ($cTerId == "")  { ?>
										<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Nit</b></td>
										<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cliente</b></td>
									<?php } ?>
									<td align="center" bgcolor = "#D6DFF7" colspan="2"><b>Comprobante</b></td>
									<td align="center" bgcolor = "#D6DFF7"><b>Cuenta</b></td>
									<td align="center" bgcolor = "#D6DFF7"><b>Fecha</b></td>
									<td align="center" bgcolor = "#D6DFF7"><b>Vencimiento</b></td>
									<td align="center" bgcolor = "#D6DFF7"><b>D&iacute;as Cartera</b></td>
									<td align="center" bgcolor = "#D6DFF7"><b>D&iacute;as Vencidos</b></td>
									<td align="center" bgcolor = "#D6DFF7" colspan="2"><b>Movimiento</b></td>
									<td align="center" bgcolor = "#D6DFF7" colspan="2"><b>Saldo</b></td>
								</tr>
								<?php for($i=0;$i<count($mRecProv);$i++){
									if($mRecProv[$i]['comidxxx'] == 'P' || $mRecProv[$i]['comidxxx'] == 'L' || $mRecProv[$i]['comidxxx'] == 'C'){
										$cTipCom = $mComP[$mRecProv[$i]['comidxxx']][$mRecProv[$i]['comcodxx']];
									
										if (in_array("{$mRecProv[$i]['comidxxx']}~{$mRecProv[$i]['comcodxx']}", $mRCM) == true) {
											$cTipCom = "RCM";
										}
									}else{
										$cTipCom = "";
									}?>
									<tr>
										<?php if ($cTerId == "")  { ?>
											<td align="left"><?php echo $mCarteraSinVencer[$i]['teridxxx'] ?></td>
											<td align="left"><?php echo $mCarteraSinVencer[$i]['clinomxx'] ?></td>
										<?php } ?>
										<td align="center" colspan="2"><?php echo ($mRecProv[$i]['document'] != "")?"<a href=\"javascript:f_Ver('{$mRecProv[$i]['comidxxx']}','{$mRecProv[$i]['comcodxx']}','{$mRecProv[$i]['comcscxx']}','{$mRecProv[$i]['comfecxx']}','PROVISIONAL','$cTipCom')\">{$mRecProv[$i]['document']}</a>": "&nbsp;"; ?></td>
										<td align="center"><?php echo ($mRecProv[$i]['pucidxxx'] != "") ? $mRecProv[$i]['pucidxxx'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mRecProv[$i]['comfecxx'] != "") ? $mRecProv[$i]['comfecxx'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mRecProv[$i]['comfecvn'] != "") ? $mRecProv[$i]['comfecvn'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mRecProv[$i]['diascart'] != "") ? $mRecProv[$i]['diascart'] : "&nbsp;" ?></td>
										<td align="center"><?php echo ($mRecProv[$i]['diasvenc'] != "") ? $mRecProv[$i]['diasvenc'] : "&nbsp;" ?></td>
										<td align="center" colspan="2"><?php echo ($mRecProv[$i]['commovxx'] != "") ? $mRecProv[$i]['commovxx'] : "&nbsp;" ?></td>
										<td align="right"  colspan="2"><?php echo number_format($mRecProv[$i]['saldoxxx'],2,",",".")?></td>
									</tr>
									<?php $nTotProvicionales += ($mRecProv[$i]['commovxx'] == "D") ? $mRecProv[$i]['saldoxxx'] : ($mRecProv[$i]['saldoxxx'] * -1); 
								} ?>
								<tr>
									<td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS PROVISIONALES: </b></td>
									<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotProvicionales,2,",",".")?></b></td>
								</tr>	
								<tr><td colspan = "<?php echo $nCol ?>">&nbsp;</td></tr>
								<tr>
									<td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS A FAVOR: </b></td>
									<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotSaldos,2,",",".")?></b></td>
								</tr>	
								<tr>
									<td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA: </b></td>
									<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format(($nTotCarVencida+$nTotCartera),2,",",".")?></b></td>
								</tr>
	              <?php $mNomTotales = array();
	              (abs($nTotCarVencida+$nTotCartera)>0) ? $mNomTotales[count($mNomTotales)] ="TOTAL CARTERA" :""; 
	              (abs($nTotSaldos)>0) ? $mNomTotales[count($mNomTotales)] ="SALDOS A FAVOR" :"";
	              (abs($nTotProvicionales)>0) ? $mNomTotales[count($mNomTotales)]="RECIBOS PROVISIONALES" :""; 
	                              
	              $mTitulo="";
	                               
	              for($j=0;$j <= (count($mNomTotales)-1);$j++){  
		            	$mTitulo .= $mNomTotales[$j];
		              ($j==(count($mNomTotales)-1)) ? "" : $mTitulo .=" - ";
	              } ?>
								<tr>	
									<td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b><?php echo $mTitulo.":" ?></b></td>
									<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format((($nTotCarVencida+$nTotCartera) + ($nTotProvicionales) + ($nTotSaldos)),2,",",".")?></b></td>
								</tr>
							</table>
						</center>	
					</body>	
				</html> 
			<?php break;
		  case 2:		  	
				// PINTA POR EXCEL//
					 	
				/**
				 * Variable para armar la cadena de texto que se envia al excel
				 * @var Text
				 */
        $header .= 'Reporte Estado de Cartera'."\n";
        $header .= "\n";
        $cData = '';
        $title = "ESTADO_DE_CUENTA_".$kUser."_".date('YmdHis').".xls";
      
      	$nCol     = ($cTerId == "") ? 13 : 11; 											
				$nColProv = ($cTerId == "") ? 1 : 4; 
				
				$cData .= '<table border = "1" cellpadding = "0" cellspacing = "0" width = "98%">';
					$cData .= '<tr>';
						$cData .= '<td class="name" style="font-size:14px" colspan="'.($nCol ).'">';
							$cData .= '<span style="font-size:18px">REPORTE DE ESTADO DE CARTERA AL '.$cFecha.'</span></center><br>';
						$cData .= '</td>';
					$cData .= '</tr>';
					if ($cTerId != "") {							
						$cData .= '<tr>';
							$cData .= '<td align="left" colspan = "'.($nCol ).'" bgcolor = "#96ADEB" style="font-size:14px"><b>CONSULTA POR CLIENTE</b></td>';
						$cData .= '</tr>';
						$cData .= '<tr>';
							$cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Cliente</b></td>';
							$cData .= '<td align="center" style="width=200px" colspan = "4"><b>'.($vNomCli['clinomxx']).'</b></td>';
							$cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Tel&eacute;fono</b></td>';
							$cData .= '<td align="center" style="width=200px" colspan = "2"><b>'.($vNomCli['CLITELXX']).'</b></td>';
							$cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Fecha y Hora de Consulta</b></td>';
							$cData .= '<td align="center" style="width=200px" colspan = "2"><b>'.(date("Y-m-d H:i:s")).'</b></td>';
						$cData .= '</tr>';
					}
					$cData .= '<tr><td colspan = "'.($nCol ).'">&nbsp;</td></tr>';
					$cData .= '<tr>';
						$cData .= '<td align="left" colspan = "'.($nCol ).'" bgcolor = "#96ADEB" style="font-size:14px"><b>CARTERA VENCIDA</b></td>';
					$cData .= '</tr>';
					$cData .= '<tr>';
						if ($cTerId == "")  { 
							$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Nit</b></td>';
							$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cliente</b></td>';
						}
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Do</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Pedido</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Comprobante</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cuenta</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Fecha</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Entrega Cliente</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Vencimiento</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>D&iacute;as Cartera</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>D&iacute;as Vencidos</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Movimiento</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Saldo</b></td>';
					$cData .= '</tr>';
					for ($i=0;$i<count($mCarteraVencida);$i++) { 
						$cData .= '<tr>';
							if ($cTerId == "")  {
								$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.($mCarteraVencida[$i]['teridxxx'] ).'</td>';
								$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.($mCarteraVencida[$i]['clinomxx'] ).'</td>';
							}
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['doidxxxx'] != "") ? $mCarteraVencida[$i]['doidxxxx'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['pedidoxx'] != "") ? $mCarteraVencida[$i]['pedidoxx'] : "" ).'</td>';
							$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['document'] != "")   ? $mCarteraVencida[$i]['document'] : "" ).'</td>';
							$cData .= '<td align="center">'.(($mCarteraVencida[$i]['pucidxxx'] != "") ? $mCarteraVencida[$i]['pucidxxx'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraVencida[$i]['comfecxx'] != "") ? $mCarteraVencida[$i]['comfecxx'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraVencida[$i]['comfecnx'] != "") ? $mCarteraVencida[$i]['comfecnx'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraVencida[$i]['comfecvn'] != "") ? $mCarteraVencida[$i]['comfecvn'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['diascart'] != "") ? $mCarteraVencida[$i]['diascart'] : "" ).'</td>';
							$cData .= '<td align="center"><font color="red">'.(($mCarteraVencida[$i]['diasvenc'] != "") ? $mCarteraVencida[$i]['diasvenc'] : "" ).'</font></td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['commovxx'] != "") ? $mCarteraVencida[$i]['commovxx'] : "" ).'</td>';
							$cData .= '<td align="right">'.(number_format($mCarteraVencida[$i]['saldoxxx'],2,",",".")).'</td>';
						$cData .= '</tr>';
						$nTotCarVencida += ($mCarteraVencida[$i]['commovxx'] == "D") ? $mCarteraVencida[$i]['saldoxxx'] : ($mCarteraVencida[$i]['saldoxxx'] * -1);
					}
					$cData .= '<tr>';
						$cData .= '<td align="right" colspan = "'.($nCol-1 ).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA VENCIDA: </b></td>';
						$cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotCarVencida,2,",",".")).'</b></td>';
					$cData .= '</tr>';
					$cData .= '<tr><td colspan = "'.($nCol ).'">&nbsp;</td></tr>';
					$cData .= '<tr>';
						$cData .= '<td align="left" colspan = "'.($nCol ).'" bgcolor = "#96ADEB" style="font-size:14px"><b>CARTERA SIN VENCER</b></td>';
					$cData .= '</tr>';
					$cData .= '<tr>';
						if ($cTerId == "")  { 
							$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Nit</b></td>';
							$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cliente</b></td>';
						}
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Do</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Pedido</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Comprobante</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cuenta</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Fecha</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Entrega Cliente</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Vencimiento</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>D&iacute;as Cartera</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>D&iacute;as Vencidos</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Movimiento</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Saldo</b></td>';
					$cData .= '</tr>';
					for($i=0;$i<count($mCarteraSinVencer);$i++) {
						$cData .= '<tr>';
							if ($cTerId == "")  { 
								$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.($mCarteraSinVencer[$i]['teridxxx'] ).'</td>';
								$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.($mCarteraSinVencer[$i]['clinomxx'] ).'</td>';
							}
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['doidxxxx'] != "") ? $mCarteraSinVencer[$i]['doidxxxx'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['pedidoxx'] != "") ? $mCarteraSinVencer[$i]['pedidoxx'] : "" ).'</td>';
							$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['document'] != "")   ? $mCarteraSinVencer[$i]['document'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['pucidxxx'] != "") ? $mCarteraSinVencer[$i]['pucidxxx'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraSinVencer[$i]['comfecxx'] != "") ? $mCarteraSinVencer[$i]['comfecxx'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraSinVencer[$i]['comfecnx'] != "") ? $mCarteraSinVencer[$i]['comfecnx'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraSinVencer[$i]['comfecvn'] != "") ? $mCarteraSinVencer[$i]['comfecvn'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['diascart'] != "") ? $mCarteraSinVencer[$i]['diascart'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['diasvenc'] != "") ? $mCarteraSinVencer[$i]['diasvenc'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['commovxx'] != "") ? $mCarteraSinVencer[$i]['commovxx'] : "" ).'</td>';
							$cData .= '<td align="right">'.(number_format($mCarteraSinVencer[$i]['saldoxxx'],2,",",".")).'</td>';
						$cData .= '</tr>';
						$nTotCartera += ($mCarteraSinVencer[$i]['commovxx'] == "D") ? $mCarteraSinVencer[$i]['saldoxxx'] : ($mCarteraSinVencer[$i]['saldoxxx'] * -1);
					}
					$cData .= '<tr>';
						$cData .= '<td align="right" colspan = "'.($nCol-1 ).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA SIN VENCER: </b></td>';
						$cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotCartera,2,",",".")).'</b></td>';
					$cData .= '</tr>';
					$cData .= '<tr><td colspan = "'.($nCol ).'">&nbsp;</td></tr>';
					$cData .= '<tr>';
						$cData .= '<td align="left" colspan = "'.($nCol ).'" bgcolor = "#96ADEB" style="font-size:14px"><b>SALDOS A FAVOR</b></td>';
					$cData .= '</tr>';
					$cData .= '<tr>';
						if ($cTerId == "")  { 
							$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Nit</b></td>';
							$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cliente</b></td>';
						}
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Do</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Pedido</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Comprobante</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cuenta</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Fecha</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Entrega Cliente</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Vencimiento</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>D&iacute;as Cartera</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>D&iacute;as Vencidos</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Movimiento</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Saldo</b></td>';
					$cData .= '</tr>';
					for($i=0;$i<count($mSaldosaFavor);$i++) {
						$cData .= '<tr>';
							if ($cTerId == "")  {
								$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.($mSaldosaFavor[$i]['teridxxx'] ).'</td>';
								$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.($mSaldosaFavor[$i]['clinomxx'] ).'</td>';
							}
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['doidxxxx'] != "") ? $mSaldosaFavor[$i]['doidxxxx'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['pedidoxx'] != "") ? $mSaldosaFavor[$i]['pedidoxx'] : "" ).'</td>';
							$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['document'] != "")   ? $mSaldosaFavor[$i]['document'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.($mSaldosaFavor[$i]['pucidxxx']).'</td>';
							$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mSaldosaFavor[$i]['comfecxx'] != "") ? $mSaldosaFavor[$i]['comfecxx'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mSaldosaFavor[$i]['comfecnx'] != "") ? $mSaldosaFavor[$i]['comfecnx'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mSaldosaFavor[$i]['comfecvn'] != "") ? $mSaldosaFavor[$i]['comfecvn'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['diascart'] != "") ? $mSaldosaFavor[$i]['diascart'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['diasvenc'] != "") ? $mSaldosaFavor[$i]['diasvenc'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['commovxx'] != "") ? $mSaldosaFavor[$i]['commovxx'] : "" ).'</td>';
							$cData .= '<td align="right">'.(number_format($mSaldosaFavor[$i]['saldoxxx'],2,",",".")).'</td>';
						$cData .= '</tr>';
						$nTotSaldos += ($mSaldosaFavor[$i]['commovxx'] == "D") ? $mSaldosaFavor[$i]['saldoxxx'] : ($mSaldosaFavor[$i]['saldoxxx'] * -1);
					}
					$cData .= '<tr>';
						$cData .= '<td align="right" colspan = "'.($nCol-1 ).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS A FAVOR: </b></td>';
						$cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotSaldos,2,",",".")).'</b></td>';
					$cData .= '</tr>';
					$cData .= '<tr><td colspan = "'.($nCol ).'">&nbsp;</td></tr>';
					$cData .= '<tr>';
						$cData .= '<td align="left" colspan = "'.($nCol ).'" bgcolor = "#96ADEB" style="font-size:14px"><b>RECIBOS PROVISIONALES</b></td>';
					$cData .= '</tr>';
					$cData .= '<tr>';
						if ($cTerId == "")  { 
							$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Nit</b></td>';
							$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width=100px"><b>Cliente</b></td>';
						}
						$cData .= '<td align="center" bgcolor = "#D6DFF7" colspan="2"><b>Comprobante</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Cuenta</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Fecha</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Vencimiento</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7"><b>D&iacute;as Cartera</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7"><b>D&iacute;as Vencidos</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" colspan="2"><b>Movimiento</b></td>';
						$cData .= '<td align="center" bgcolor = "#D6DFF7" colspan="2"><b>Saldo</b></td>';
					$cData .= '</tr>';
					for($i=0;$i<count($mRecProv);$i++) {
						$cData .= '<tr>';
							if ($cTerId == "")  {
								$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.($mCarteraSinVencer[$i]['teridxxx'] ).'</td>';
								$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.($mCarteraSinVencer[$i]['clinomxx'] ).'</td>';
							}
							$cData .= '<td align="center" colspan="2">'.(($mRecProv[$i]['document'] != "") ? $mRecProv[$i]['document'] : "").'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mRecProv[$i]['pucidxxx'] != "") ? $mRecProv[$i]['pucidxxx'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mRecProv[$i]['comfecxx'] != "") ? $mRecProv[$i]['comfecxx'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mRecProv[$i]['comfecvn'] != "") ? $mRecProv[$i]['comfecvn'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mRecProv[$i]['diascart'] != "") ? $mRecProv[$i]['diascart'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mRecProv[$i]['diasvenc'] != "") ? $mRecProv[$i]['diasvenc'] : "" ).'</td>';
							$cData .= '<td align="center" style="mso-number-format:\'\@\'" colspan="2">'.(($mRecProv[$i]['commovxx'] != "") ? $mRecProv[$i]['commovxx'] : "" ).'</td>';
							$cData .= '<td align="right"  colspan="2">'.(number_format($mRecProv[$i]['saldoxxx'],2,",",".")).'</td>';
						$cData .= '</tr>';
						$nTotProvicionales += ($mRecProv[$i]['commovxx'] == "D") ? $mRecProv[$i]['saldoxxx'] : ($mRecProv[$i]['saldoxxx'] * -1); 
					}
					$cData .= '<tr>';
						$cData .= '<td align="right" colspan = "'.($nCol-1 ).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS PROVISIONALES: </b></td>';
						$cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotProvicionales,2,",",".")).'</b></td>';
					$cData .= '</tr>';
					$cData .= '<tr><td colspan = "'.($nCol ).'">&nbsp;</td></tr>';
					$cData .= '<tr>';
						$cData .= '<td align="right" colspan = "'.($nCol-1 ).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS A FAVOR: </b></td>';
						$cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotSaldos,2,",",".")).'</b></td>';
					$cData .= '</tr>';
					$cData .= '<tr>';
						$cData .= '<td align="right" colspan = "'.($nCol-1 ).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA: </b></td>';
						$cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format(($nTotCarVencida+$nTotCartera),2,",",".")).'</b></td>';
					$cData .= '</tr>';
					
			    $mNomTotales = array();
			    (abs($nTotCarVencida+$nTotCartera)>0) ? $mNomTotales[count($mNomTotales)] ="TOTAL CARTERA" :""; 
			    (abs($nTotSaldos)>0) ? $mNomTotales[count($mNomTotales)] ="SALDOS A FAVOR" :"";
			    (abs($nTotProvicionales)>0) ? $mNomTotales[count($mNomTotales)]="RECIBOS PROVISIONALES" :""; 
			                              
			    $mTitulo="";
			                               
			    for($j=0;$j <= (count($mNomTotales)-1);$j++){  
				  	$mTitulo .= $mNomTotales[$j];
				    ($j==(count($mNomTotales)-1)) ? "" : $mTitulo .=" - ";
			    }
					$cData .= '<tr>';
						$cData .= '<td align="right" colspan = "'.($nCol-1 ).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>'.($mTitulo.":" ).'</b></td>';
						$cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format((($nTotCarVencida+$nTotCartera) + ($nTotProvicionales) + ($nTotSaldos)),2,",",".")).'</b></td>';
					$cData .= '</tr>';
				$cData .= '</table>';
	                
      	if ($cData == "") {
        	f_Mensaje(__FILE__,__LINE__,"Error al Generar el Archivo Excel."); ?>
        	<script languaje = "javascript">
          	window.close();
        	</script> 
        <? } else {
        	header("Pragma: public");
          header("Expires: 0");
          header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
          header("Cache-Control: private",false); // required for certain browsers
          header("Content-type: application/octet-stream");
          header("Content-Disposition: attachment; filename=\"".basename($title)."\";");
          print $cData;
        }													  			  
		  break;
			default:
				define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
				require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
				
				/**
				 * Standarizar colores.
				 * @var matrix $mBlack: define el color negro
				 * @var matrix $mMBlue: define el color azul normal
				 * @var matrix $mLBlue: define el color azul claro
				 * @var matrix $mRed: define el color rojo
				 * @var matrix $mGrey: define el color azul gris
				 * @var matrix $mWhite: define el color azul blanco
				 */

				$mBlack = array(0,0,0);
				$mMBlue = array(214,223,247);
				$mLBlue = array(166,222,238);
				$mRed = array(255,0,0);
				$mGrey = array(249,248,248);
				$mWhite = array(255,255,255);
				
				/**
				 * @var boolean $bDrawSubHeader: define si hay que pintar el subheader o no en cada salto de página.
				 * @var string $cSubHeaderText: se utiliza para definir el texto del subheader dinámicamente.
				 * @var boolean $bHeaderProvisionales: define si hay que mostrar los headers de columnas específicos de Provisionales.
				 * @var boolean $bShowColumns: define si hay que pintar los headers de las columnas en el salto de página.
				 * @var boolean $bHidePageHeader: define si hay que pintar los headers del salto de página cuando llamamos a la función Header() sola.
				 */
				$bDrawSubHeader = true;
				$cSubHeaderText = '';
				$bHeaderProvisionales = false;
				$bShowColumns = true;
				$bHidePageHeader = false;
				
				/**
				 * @var number $nTotCarVencida: para guardar el total de Cartera Vencida.
				 * @var number $nTotCartera: para guardar el total de Cartera Sin Vencer.
				 * @var number $nTotSaldos: para guardar el total de Saldos.
				 * @var number $nTotProvicionales: para guardar el total de Provisionales.
				 */
				
				$nTotCarVencida = 0;
				$nTotCartera = 0;
				$nTotSaldos = 0;
				$nTotProvicionales = 0;
				
				class PDF extends FPDF {
					function Header() {
						global $cRoot; global $cPlesk_Skin_Directory;
						global $cAlfa; global $cTipoCta; global $cMes; global $fec; global $cTerId; global $cTpTer; global $dDesde; global $dHasta; global $cFecha; global $vNomCli;
						
						global $mBlack; global $mMBlue; global $mRed; global $mWhite; global $bDrawSubHeader; global $cSubHeaderText; global $bHeaderProvisionales; global $bShowColumns; global $bHidePageHeader;
						
						// Guardo los valores seteados, en caso de que el header se esté llamando a causa de una línea nueva. De modo que no se pierden los valores para los siguientes rows.
						$mAuxBackgroundColors = $this->backgroundColors;
						$mAuxColors = $this->colors;
						$mAuxAligns = $this->aligns;
						
						if ( !$bHidePageHeader ) {
							$this->SetFont('verdana','',16);
							$this->SetXY(13,7);
							$this->SetFillColor(249,248,248);
							$this->SetTextColor(85,85,85);
							$this->Cell(255,15,"REPORTE DE ESTADO DE CARTERA AL $cFecha",1,0,'C',true);
							$this->Ln(18);
							
							// Agrego los datos del cliente.
							if ( $cTerId != "" ) {
								$this->SetX(13);
								$this->SetFont('verdana','B',8);
								$this->SetFillColor(150,173,235);
								$this->SetTextColor(0,0,0);
								$this->Cell(255,4,'CONSULTA POR CLIENTE',1,0,'L',true);
								$this->Ln(4);
								
								$this->SetX(13);
								$this->SetFont('verdana','B',6);
								$this->SetWidths(array('25','100','25','30','45','30'));
								$this->SetAligns(array('C','C','C','C','C','C'));
								$this->SetColors(array($mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack));
								$this->SetBackgroundColors(array($mMBlue,$mWhite,$mMBlue,$mWhite,$mMBlue,$mWhite));
								$this->Row(array("Cliente",
																 $vNomCli['clinomxx'],
																 iconv('UTF-8', 'windows-1252', "Teléfono"),
																 $vNomCli['CLITELXX'],
																 "Fecha y Hora de Consulta",
																 date("Y-m-d H:i:s")));
								
								$this->Ln(4);
							}
						}
						
						if( $bDrawSubHeader ) {
							$this->SetX(13);
							$this->SetFont('verdana','B',8);
							$this->SetFillColor(150,173,235);
							$this->SetTextColor(0,0,0);
							$this->Cell(255,4,$cSubHeaderText,1,0,'L',true);
							$this->Ln(4);
							
							$bDrawSubHeader = false;
						}
						
						$this->SetFont('verdana','B',6);
						$this->SetFillColor(255,255,255);
						$this->SetDrawColor(0,0,0);
						
						if ( $bShowColumns ) {
							
							// Si es la tabla de Provisionales, cambio los headers de las columnas.
							if ( $bHeaderProvisionales ) {
								if ( $cTerId == "" ) {
									$this->SetX(13);
									$this->SetWidths(array('20','89','28','20','17','17','14','14','8','28'));
									$this->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
									$this->SetColors(array($mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack));
									$this->SetBackgroundColors(array($mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue));
									$this->Row(array("Nit",
																	 "Cliente",
																	 "Comprobante",
																	 "Cuenta",
																	 "Fecha",
																	 "Vencimiento",
																	 iconv('UTF-8', 'windows-1252', "Días Cartera"),
																	 iconv('UTF-8', 'windows-1252', "Días Vencidos"),
																	 "Mov",
																	 "Saldo"));
								} else {
									$this->SetX(13);
									$this->SetWidths(array('29','50','35','35','30','30','18','28'));
									$this->SetAligns(array('C','C','C','C','C','C','C','C'));
									$this->SetColors(array($mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack));
									$this->SetBackgroundColors(array($mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue));
									$this->Row(array("Comprobante",
																	 "Cuenta",
																	 "Fecha",
																	 "Vencimiento",
																	 iconv('UTF-8', 'windows-1252', "Días Cartera"),
																	 iconv('UTF-8', 'windows-1252', "Días Vencidos"),
																	 "Mov",
																	 "Saldo"));
								}
							} else {
								if ( $cTerId == "" ) {
									$this->SetX(13);
									$this->SetWidths(array('20','37','15','20','28','20','17','17','17','14','14','8','28'));
									$this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C'));
									$this->SetColors(array($mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack));
									$this->SetBackgroundColors(array($mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue));
									$this->Row(array("Nit",
																	 "Cliente",
																	 "Do",
																	 "Pedido",
																	 "Comprobante",
																	 "Cuenta",
																	 "Fecha",
																	 "Entrega Cliente",
																	 "Vencimiento",
																	 iconv('UTF-8', 'windows-1252', "Días Cartera"),
																	 iconv('UTF-8', 'windows-1252', "Días Vencidos"),
																	 "Mov",
																	 "Saldo"));
								} else {
									$this->SetX(13);
									$this->SetWidths(array('23','40','38','30','20','20','20','14','14','8','28'));
									$this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C'));
									$this->SetColors(array($mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack));
									$this->SetBackgroundColors(array($mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue,$mMBlue));
									$this->Row(array("Do",
																	 "Pedido",
																	 "Comprobante",
																	 "Cuenta",
																	 "Fecha",
																	 "Entrega Cliente",
																	 "Vencimiento",
																	 iconv('UTF-8', 'windows-1252', "Días Cartera"),
																	 iconv('UTF-8', 'windows-1252', "Días Vencidos"),
																	 "Mov",
																	 "Saldo"));
								}
							}
						}
						
						// Recupero los parámetros que tenía al ingresar al método.
						$this->SetBackgroundColors($mAuxBackgroundColors);
						$this->SetColors($mAuxColors);
						$this->SetAligns($mAuxAligns);
						$this->SetFont('verdana','',6);
						
						$this->SetX(13);
					}
					
					function CustomHeader() {
						// Checkea si hay espacio para iniciar una nueva tabla. Si hay espacio, llama Header() sin que se impriman las cabeceras de página; si no hay espacio, agrego una página normalmente.
						
						global $bHidePageHeader;
						
						if($this->GetY()+20>$this->PageBreakTrigger) {
							$bHidePageHeader = false;
							$this->AddPage($pdf->CurOrientation);
						} else {
							$this->Ln(8);
							$bHidePageHeader = true;
							$this->Header();
						}
						
						$bHidePageHeader = false;
					}
					
					function Footer() {
	    			$this->SetY(-10);
	    			$this->SetFont('verdana','',6);
	    			$this->Cell(0,5,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	  			}
	
					function SetWidths($w) {
						//Set the array of column widths
						$this->widths=$w;
					}
	
					function SetAligns($a){
					  //Set the array of column alignments
					  $this->aligns=$a;
					}
					
					function SetColors($a){
						//Set the array of text colors.
						$this->colors = $a;
					}
					
					function SetBackgroundColors($a){
						//Set the array of background colors.
						$this->backgroundColors = $a;
					}
					
					function SetFontSizes($a){
						//Set the array of font sizes.
						$this->fontSizes = $a;
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
				    for($i=0;$i<count($data);$i++)
				    {
				        $w=$this->widths[$i];
				        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
				        //Save the current position
				        $x=$this->GetX();
				        $y=$this->GetY();
				        
								isset($this->colors[$i]) ? $this->SetTextColor($this->colors[$i][0],$this->colors[$i][1],$this->colors[$i][2]) : $this->SetTextColor(0,0,0);
								isset($this->backgroundColors[$i]) ? $this->SetFillColor($this->backgroundColors[$i][0],$this->backgroundColors[$i][1],$this->backgroundColors[$i][2]) : $this->SetFillColor(255,255,255);
								
								// Draw the border and add the background color.
								$this->Rect($x,$y,$w,$h, 'FD');
								
								//Print the text
								$this->MultiCell($w,4,$data[$i],0,$a);
								
								// Put the position to the right of the cell
								$this->SetXY($x+$w,$y);
								
								$this->SetTextColor(0,0,0);
								$this->SetFillColor(255,255,255);
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
				
				$pdf = new PDF('L','mm','Letter');
				$pdf->AddFont('verdana','','');
				$pdf->AddFont('verdana','B','');
				$pdf->AliasNbPages();
				$pdf->SetMargins(0,0,0);
	
				$bDrawSubHeader = true;
				$cSubHeaderText = "CARTERA VENCIDA";
				$bHidePageHeader = false;
				$pdf->AddPage();
				
				// Cartera vencida.
				for ($i=0;$i<count($mCarteraVencida);$i++) {
					$mRows = array();
					
					if ( $cTerId == "" ) {
						$pdf->SetAligns(array('L','L','C','C','L','C','C','C','C','C','C','C','R'));
						$pdf->SetColors(array($mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mRed,$mBlack,$mBlack));
						$pdf->SetBackgroundColors(array($mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite));
						
						array_push($mRows	, $mCarteraVencida[$i]['teridxxx'],
																$mCarteraVencida[$i]['clinomxx']);
					} else {
						$pdf->SetAligns(array('C','C','L','C','C','C','C','C','C','C','R'));
						$pdf->SetColors(array($mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mRed,$mBlack,$mBlack));
						$pdf->SetBackgroundColors(array($mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite));
					}
					
					array_push($mRows	, $mCarteraVencida[$i]['doidxxxx'] != "" ? $mCarteraVencida[$i]['doidxxxx'] : "",
															$mCarteraVencida[$i]['pedidoxx'] != "" ? $mCarteraVencida[$i]['pedidoxx'] : "",
															$mCarteraVencida[$i]['document'] != "" ? $mCarteraVencida[$i]['document'] : "",
															$mCarteraVencida[$i]['pucidxxx'] != "" ? $mCarteraVencida[$i]['pucidxxx'] : "",
															$mCarteraVencida[$i]['comfecxx'] != "" ? $mCarteraVencida[$i]['comfecxx'] : "",
															$mCarteraVencida[$i]['comfecnx'] != "" ? $mCarteraVencida[$i]['comfecnx'] : "",
															$mCarteraVencida[$i]['comfecvn'] != "" ? $mCarteraVencida[$i]['comfecvn'] : "",
															$mCarteraVencida[$i]['diascart'] != "" ? $mCarteraVencida[$i]['diascart'] : "",
															$mCarteraVencida[$i]['diasvenc'] != "" ? $mCarteraVencida[$i]['diasvenc'] : "",
															$mCarteraVencida[$i]['commovxx'] != "" ? $mCarteraVencida[$i]['commovxx'] : "",
															number_format($mCarteraVencida[$i]['saldoxxx'],2,",","."));
					
					$pdf->SetX(13);
					$pdf->SetFont('verdana','',6);
					$pdf->Row($mRows);
					
					$nTotCarVencida += ($mCarteraVencida[$i]['commovxx'] == "D") ? $mCarteraVencida[$i]['saldoxxx'] : ($mCarteraVencida[$i]['saldoxxx'] * -1);
				}
				
				// Pinto el row de totales.
				$pdf->SetX(13);
				$pdf->SetFont('verdana','B',7);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFillColor(166,222,238);
				$pdf->Cell(227,4,'TOTAL CARTERA VENCIDA:',1,0,'R',true);
				$pdf->SetFont('verdana','B',6);
				$pdf->Cell(28,4,number_format($nTotCarVencida,2,",","."),1,0,'R',true);
				
				// Cartera sin vencer.
				$nPag = 0;
				$bDrawSubHeader = true;
				$cSubHeaderText = "CARTERA SIN VENCER";
				
				$pdf->CustomHeader();
				
				for ($i=0;$i<count($mCarteraSinVencer);$i++) {
					$mRows = array();
					
					if ( $cTerId == "" ) {
						$pdf->SetAligns(array('L','L','C','C','L','C','C','C','C','C','C','C','R'));
						$pdf->SetColors(array($mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mRed,$mBlack,$mBlack));
						$pdf->SetBackgroundColors(array($mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite));
						
						array_push($mRows	, $mCarteraSinVencer[$i]['teridxxx'],
																$mCarteraSinVencer[$i]['clinomxx']);
					} else {
						$pdf->SetAligns(array('C','C','L','C','C','C','C','C','C','C','R'));
						$pdf->SetColors(array($mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mRed,$mBlack,$mBlack));
						$pdf->SetBackgroundColors(array($mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite));
					}
					
					array_push($mRows	, $mCarteraSinVencer[$i]['doidxxxx'] != "" ? $mCarteraSinVencer[$i]['doidxxxx'] : "",
															$mCarteraSinVencer[$i]['pedidoxx'] != "" ? $mCarteraSinVencer[$i]['pedidoxx'] : "",
															$mCarteraSinVencer[$i]['document'] != "" ? $mCarteraSinVencer[$i]['document'] : "",
															$mCarteraSinVencer[$i]['pucidxxx'] != "" ? $mCarteraSinVencer[$i]['pucidxxx'] : "",
															$mCarteraSinVencer[$i]['comfecxx'] != "" ? $mCarteraSinVencer[$i]['comfecxx'] : "",
															$mCarteraSinVencer[$i]['comfecnx'] != "" ? $mCarteraSinVencer[$i]['comfecnx'] : "",
															$mCarteraSinVencer[$i]['comfecvn'] != "" ? $mCarteraSinVencer[$i]['comfecvn'] : "",
															$mCarteraSinVencer[$i]['diascart'] != "" ? $mCarteraSinVencer[$i]['diascart'] : "",
															$mCarteraSinVencer[$i]['diasvenc'] != "" ? $mCarteraSinVencer[$i]['diasvenc'] : "",
															$mCarteraSinVencer[$i]['commovxx'] != "" ? $mCarteraSinVencer[$i]['commovxx'] : "",
															number_format($mCarteraSinVencer[$i]['saldoxxx'],2,",","."));
					
					$pdf->SetX(13);
					$pdf->SetFont('verdana','',6);
					$pdf->Row($mRows);
					
					$nTotCartera += ($mCarteraSinVencer[$i]['commovxx'] == "D") ? $mCarteraSinVencer[$i]['saldoxxx'] : ($mCarteraSinVencer[$i]['saldoxxx'] * -1);
				}
				
				// Pinto el row de totales.
				$pdf->SetX(13);
				$pdf->SetFont('verdana','B',7);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFillColor(166,222,238);
				$pdf->Cell(227,4,'TOTAL CARTERA SIN VENCER:',1,0,'R',true);
				$pdf->SetFont('verdana','B',6);
				$pdf->Cell(28,4,number_format($nTotCartera,2,",","."),1,0,'R',true);
				
				// Saldo a favor.
				$nPag = 0;
				$bDrawSubHeader = true;
				$cSubHeaderText = "SALDO A FAVOR";
				
				$pdf->CustomHeader();
				
				for ($i=0;$i<count($mSaldosaFavor);$i++) {
					$mRows = array();
					
					if ( $cTerId == "" ) {
						$pdf->SetAligns(array('L','L','C','C','L','C','C','C','C','C','C','C','R'));
						$pdf->SetColors(array($mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mRed,$mBlack,$mBlack));
						$pdf->SetBackgroundColors(array($mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite));
						
						array_push($mRows	, $mCarteraSinVencer[$i]['teridxxx'],
																$mCarteraSinVencer[$i]['clinomxx']);
					} else {
						$pdf->SetAligns(array('C','C','L','C','C','C','C','C','C','C','R'));
						$pdf->SetColors(array($mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mRed,$mBlack,$mBlack));
						$pdf->SetBackgroundColors(array($mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite));
					}
					
					array_push($mRows	, $mSaldosaFavor[$i]['doidxxxx'] != "" ? $mSaldosaFavor[$i]['doidxxxx'] : "",
															$mSaldosaFavor[$i]['pedidoxx'] != "" ? $mSaldosaFavor[$i]['pedidoxx'] : "",
															$mSaldosaFavor[$i]['document'] != "" ? $mSaldosaFavor[$i]['document'] : "",
															$mSaldosaFavor[$i]['pucidxxx'] != "" ? $mSaldosaFavor[$i]['pucidxxx'] : "",
															$mSaldosaFavor[$i]['comfecxx'] != "" ? $mSaldosaFavor[$i]['comfecxx'] : "",
															$mSaldosaFavor[$i]['comfecnx'] != "" ? $mSaldosaFavor[$i]['comfecnx'] : "",
															$mSaldosaFavor[$i]['comfecvn'] != "" ? $mSaldosaFavor[$i]['comfecvn'] : "",
															$mSaldosaFavor[$i]['diascart'] != "" ? $mSaldosaFavor[$i]['diascart'] : "",
															$mSaldosaFavor[$i]['diasvenc'] != "" ? $mSaldosaFavor[$i]['diasvenc'] : "",
															$mSaldosaFavor[$i]['commovxx'] != "" ? $mSaldosaFavor[$i]['commovxx'] : "",
															number_format($mSaldosaFavor[$i]['saldoxxx'],2,",","."));
					
					$pdf->SetX(13);
					$pdf->SetFont('verdana','',6);
					$pdf->Row($mRows);
					
					$nTotSaldos += ($mSaldosaFavor[$i]['commovxx'] == "D") ? $mSaldosaFavor[$i]['saldoxxx'] : ($mSaldosaFavor[$i]['saldoxxx'] * -1);
				}
				
				// Pinto el row de totales.
				$pdf->SetX(13);
				$pdf->SetFont('verdana','B',7);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFillColor(166,222,238);
				$pdf->Cell(227,4,'TOTAL SALDOS A FAVOR:',1,0,'R',true);
				$pdf->SetFont('verdana','B',6);
				$pdf->Cell(28,4,number_format($nTotSaldos,2,",","."),1,0,'R',true);
				
				// Recibos provicionales.
				$nPag = 0;
				$bDrawSubHeader = true;
				$bHeaderProvisionales = true;
				$cSubHeaderText = "RECIBOS PROVISIONALES";
				
				$pdf->CustomHeader();
				
				for ($i=0;$i<count($mRecProv);$i++) {
					$mRows = array();
					
					if ( $cTerId == "" ) {
						$pdf->SetAligns(array('L','L','L','C','C','C','C','C','C','R'));
						$pdf->SetColors(array($mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mRed,$mBlack,$mBlack));
						$pdf->SetBackgroundColors(array($mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite));
						
						array_push($mRows	, $mCarteraSinVencer[$i]['teridxxx'],
																$mCarteraSinVencer[$i]['clinomxx']);
					} else {
						$pdf->SetAligns(array('L','C','C','C','C','C','C','R'));
						$pdf->SetColors(array($mBlack,$mBlack,$mBlack,$mBlack,$mBlack,$mRed,$mBlack,$mBlack));
						$pdf->SetBackgroundColors(array($mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite,$mWhite));
					}
					
					array_push($mRows	, $mRecProv[$i]['document'] != "" ? $mRecProv[$i]['document'] : "",
															$mRecProv[$i]['pucidxxx'] != "" ? $mRecProv[$i]['pucidxxx'] : "",
															$mRecProv[$i]['comfecxx'] != "" ? $mRecProv[$i]['comfecxx'] : "",
															$mRecProv[$i]['comfecvn'] != "" ? $mRecProv[$i]['comfecvn'] : "",
															$mRecProv[$i]['diascart'] != "" ? $mRecProv[$i]['diascart'] : "",
															$mRecProv[$i]['diasvenc'] != "" ? $mRecProv[$i]['diasvenc'] : "",
															$mRecProv[$i]['commovxx'] != "" ? $mRecProv[$i]['commovxx'] : "",
															number_format($mRecProv[$i]['saldoxxx'],2,",","."));
					
					$pdf->SetX(13);
					$pdf->SetFont('verdana','',6);
					$pdf->Row($mRows);
					
					$nTotProvicionales += ($mRecProv[$i]['commovxx'] == "D") ? $mRecProv[$i]['saldoxxx'] : ($mRecProv[$i]['saldoxxx'] * -1);
				}
				
				// Pinto el row de totales.
				$pdf->SetX(13);
				$pdf->SetFont('verdana','B',7);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFillColor(166,222,238);
				$pdf->Cell(227,4,'TOTAL SALDOS PROVISIONALES:',1,0,'R',true);
				$pdf->SetFont('verdana','B',6);
				$pdf->Cell(28,4,number_format($nTotProvicionales,2,",","."),1,0,'R',true);
				
				// Pinto las columnas de totales generales.
				$bShowColumns = false;
				$pdf->Ln(8);
				$pdf->SetX(13);
				$pdf->SetFont('verdana','B',7);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFillColor(166,222,238);
				$pdf->Cell(227,4,'TOTAL SALDOS A FAVOR:',1,0,'R',true);
				$pdf->SetFont('verdana','B',6);
				$pdf->Cell(28,4,number_format($nTotSaldos,2,",","."),1,0,'R',true);
				
				$pdf->Ln(4);
				$pdf->SetX(13);
				$pdf->SetFont('verdana','B',7);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFillColor(166,222,238);
				$pdf->Cell(227,4,'TOTAL CARTERA:',1,0,'R',true);
				$pdf->SetFont('verdana','B',6);
				$pdf->Cell(28,4,number_format($nTotCarVencida+$nTotCartera,2,",","."),1,0,'R',true);
				
				/**
				 * @var matrix $mNomTotales: matriz auxiliar para armar el título del total final.
				 * @var string $cTitulo: título del total final.
				 */
				
				$mNomTotales = array();
				$cTitulo = "";
				
				abs($nTotCarVencida+$nTotCartera)>0 ? $mNomTotales[count($mNomTotales)] ="TOTAL CARTERA" :""; 
				abs($nTotSaldos)>0 ? $mNomTotales[count($mNomTotales)] ="SALDOS A FAVOR" :"";
				abs($nTotProvicionales)>0 ? $mNomTotales[count($mNomTotales)]="RECIBOS PROVISIONALES" :""; 
				
				for($j=0;$j <= (count($mNomTotales)-1);$j++){  
					$cTitulo .= $mNomTotales[$j];
					($j==(count($mNomTotales)-1)) ? "" : $cTitulo .=" - ";
				}
				
				$pdf->Ln(4);
				$pdf->SetX(13);
				$pdf->SetFont('verdana','B',7);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFillColor(166,222,238);
				$pdf->Cell(227,4,$cTitulo,1,0,'R',true);
				$pdf->SetFont('verdana','B',6);
				$pdf->Cell(28,4,number_format((($nTotCarVencida+$nTotCartera) + ($nTotProvicionales) + ($nTotSaldos)),2,",","."),1,0,'R',true);
				
				$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	
				$pdf->Output($cFile);
	
				if (file_exists($cFile)){
					chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
				} else {
					f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
				}
	
				echo "<html><script>document.location='$cFile';</script></html>";
				break;
		}
	}
	
	if ($nSwitch == 0) {
	} else {
	  f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.\n");  ?>
        
        <script languaje = "javascript">
          window.close();
        </script> 
      <?php 
	} 
 	
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
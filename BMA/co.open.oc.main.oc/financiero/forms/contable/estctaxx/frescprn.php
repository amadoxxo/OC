<?php
  /**
	 * Imprime Estado de cuentas.
	 * --- Descripcion: Permite Imprimir Estado de cuentas(por Cobrar / por Pagar).
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 */

	set_time_limit(0);
	ini_set("memory_limit","512M");

	date_default_timezone_set("America/Bogota");

	/**
	 * Variables de control de errores
	 * @var number
	 */
	$nSwitch = 0;

	/**
	 * Variable para almacenar los mensajes de error
	 * @var string
	 */
	$cMsj = "\n";

	/**
	 * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales
	 * @var Number
	 */
	$cEjePro = 0;

	/**
	 * Nombre(s) de los archivos en excel generados
	 */
	$cNomArc = "";	

	/**
	 * Cuando se ejecuta desde el cron debe armarse la cookie para incluir los utilitys
	 */
	if ($_SERVER["SERVER_PORT"] == "") {
		$vArg = explode(",", $argv[1]);

		if ($vArg[0] == "") {
			$nSwitch = 1;
			$cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
			$cMsj .= "El parametro Id del Proceso no puede ser vacio.\n";
		}

		if ($vArg[1] == "") {
			$nSwitch = 1;
			$cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
			$cMsj .= "El parametro de la Cookie no puede ser vacio.\n";
		}

		if ($nSwitch == 0) {
			$_COOKIE["kDatosFijos"] = $vArg[1];

			# Librerias
			include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
			include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utility.php");
			include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php");

			/**
			 * Buscando el ID del proceso
			 */
			$qProBg = "SELECT * ";
			$qProBg .= "FROM $cBeta.sysprobg ";
			$qProBg .= "WHERE ";
			$qProBg .= "pbaidxxx= \"{$vArg[0]}\" AND ";
			$qProBg .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
			$xProBg = f_MySql("SELECT", "", $qProBg, $xConexion01, "");
			if (mysql_num_rows($xProBg) == 0) {
				$xRPB = mysql_fetch_array($xProBg);
				$nSwitch = 1;
				$cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
				$cMsj .= "El Proceso en Background [{$vArg[0]}] No Existe o ya fue Procesado.\n";
			} else {
				$xRB = mysql_fetch_array($xProBg);

				/**
				 * Reconstruyendo Post
				 */
				$mPost = f_Explode_Array($xRB['pbapostx'], "|", "~");
				for ($nP = 0; $nP < count($mPost); $nP++) {
					if ($mPost[$nP][0] != "") {
						$_POST[$mPost[$nP][0]] = $mPost[$nP][1];
					}
				}
			}
		}
	}

	/**
	 * Subiendo el archivo al sistema
	 */
	if ($_SERVER["SERVER_PORT"] != "") {
		# Librerias
		include("../../../../config/config.php");
		include("../../../../libs/php/utility.php");
		include("../../../../../libs/php/utiprobg.php");
	}

	/**
	 *  Cookie fija
	 */
	$kDf = explode("~", $_COOKIE["kDatosFijos"]);
	$kUser = $kDf[4];

	$cSystemPath = OC_DOCUMENTROOT;

	if ($_SERVER["SERVER_PORT"] != "") {
		/*** Ejecutar proceso en Background ***/
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;

		if($cTerId != ""){
			#Busco el nombre del cliente
			$qCliNom  = "SELECT ";
			$qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
			$qCliNom .= "FROM $cAlfa.SIAI0150 ";
			$qCliNom .= "WHERE ";
			$qCliNom .= "CLIIDXXX = \"{$cTerId}\" LIMIT 0,1";
			$xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");

			if (mysql_num_rows($xCliNom) > 0) {
				$xDDE = mysql_fetch_array($xCliNom);
			} else {
				$xDDE['clinomxx'] = "CLIENTE SIN NOMBRE";
			}
		}

	} // fin if ($_SERVER["SERVER_PORT"] != "")

	if ($_SERVER["SERVER_PORT"] == "") {
		$cTerId 	= $_POST['cTerId'];
		$cTipoCta = $_POST['cTipoCta'];
		$cTipo  	= $_POST['cTipo'];
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;
	
		$strPost = "cTipoCta~" . $cTipoCta . "|cTerId~" . $cTerId . "|cTipo~" . $cTipo ;

		$cTabla = "";
		if($cTipoCta == 'PAGAR'){
			$cTabla = "fcxp0000";
			$cPuc   = "P";
		}elseif($cTipoCta == 'COBRAR'){
			$cTabla = "fcxc0000";
			$cPuc   = "C";
		}
		
		$qRegistros  = "SELECT SQL_CALC_FOUND_ROWS teridxxx ";
		$qRegistros .= "FROM $cAlfa.$cTabla ";
		$qRegistros .= "WHERE ";
		if($cTerId != ""){
			$qRegistros .= "$cAlfa.$cTabla.teridxxx = \"$cTerId\" AND ";
		}
		$qRegistros .= "$cAlfa.$cTabla.regestxx = \"ACTIVO\" ";
		$xRegistros  = f_MySql("SELECT","",$qRegistros,$xConexion01,"");
		mysql_free_result($xRegistros);

		$xNumRows = mysql_query("SELECT FOUND_ROWS();");
		$xRNR = mysql_fetch_array($xNumRows);
		$nRegistros = $xRNR['FOUND_ROWS()'];
		mysql_free_result($xNumRows);
	
		$vParBg['pbadbxxx'] = $cAlfa;                                         	//Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                                  	//Modulo
		$vParBg['pbatinxx'] = "ESTADOCUENTA";                             	  	//Tipo Interface
		$vParBg['pbatinde'] = "ESTADO DE CUENTA";                               //Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                                             	//Sucursal
		$vParBg['doiidxxx'] = "";                                             	//Do
		$vParBg['doisfidx'] = "";                                             	//Sufijo
		$vParBg['cliidxxx'] = $cTerId;                                          //Nit
		$vParBg['clinomxx'] = $xDDE['clinomxx'];                                //Nombre Importador
		$vParBg['pbapostx'] = $strPost;																					//Parametros para reconstruir Post
		$vParBg['pbatabxx'] = "";                                             	//Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];                    	//Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];                        	//cookie
		$vParBg['pbacrexx'] = $nRegistros;                                    	//Cantidad Registros
		$vParBg['pbatxixx'] = 1;                                              	//Tiempo Ejecucion x Item en Segundos
		$vParBg['pbaopcxx'] = "";                                             	//Opciones
		$vParBg['regusrxx'] = $kUser;                                         	//Usuario que Creo Registro
	
		#Incluyendo la clase de procesos en background
		$ObjProBg = new cProcesosBackground();
		$mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);
	
		#Imprimiendo resumen de todo ok.
		if ($mReturnProBg[0] == "true") {
			f_Mensaje(__FILE__, __LINE__, "Proceso en Background Agendado con Exito."); ?>
			<script languaje = "javascript">
				parent.fmwork.fnRecargar();
			</script>
		<?php } else {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
				$cMsj .= $mReturnProBg[$nR] . "\n";
			}
			f_Mensaje(__FILE__, __LINE__, $cMsj."Verifique.");
		}
	} // fin del if ($_SERVER["SERVER_PORT"] != "" && $_POST['cEjProBg'] == "SI" && $nSwitch == 0)

	if ($cEjePro == 0) {
		if ($nSwitch == 0) {

			$cTabla = "";
			if($cTipoCta == 'PAGAR'){
				$cTabla = "fcxp0000";
				$cPuc   = "P";
			}elseif($cTipoCta == 'COBRAR'){
				$cTabla = "fcxc0000";
				$cPuc   = "C";
			}

			$fec  = date('Y-m-d');
			$cMes = "";
			switch (substr($fec,5,2)){
				case "01": $cMes="ENERO";       break;
				case "02": $cMes="FEBRERO";     break;
				case "03": $cMes="MARZO";       break;
				case "04": $cMes="ABRIL";       break;
				case "05": $cMes="MAYO";        break;
				case "06": $cMes="JUNIO";       break;
				case "07": $cMes="JULIO";       break;
				case "08": $cMes="AGOSTO";      break;
				case "09": $cMes="SEPTIEMBRE";  break;
				case "10": $cMes="OCTUBRE";     break;
				case "11": $cMes="NOVIEMBRE";   break;
				case "12": $cMes="DICIEMBRE";   break;
			}
		
			if($cTerId <> ""){
				$qCliDat  = "SELECT $cAlfa.SIAI0150.*, ";
				$qCliDat .= "$cAlfa.SIAI0055.CIUDESXX ";
				$qCliDat .= "FROM $cAlfa.SIAI0150 ";
				$qCliDat .= "LEFT JOIN $cAlfa.SIAI0055 ON $cAlfa.SIAI0150.PAIIDXXX = $cAlfa.SIAI0055.PAIIDXXX AND ";
				$qCliDat .= "$cAlfa.SIAI0150.DEPIDXXX = $cAlfa.SIAI0055.DEPIDXXX AND ";
				$qCliDat .= "$cAlfa.SIAI0150.CIUIDXXX = $cAlfa.SIAI0055.CIUIDXXX ";
				$qCliDat .= "WHERE ";
				$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"$cTerId\" ";
				//f_Mensaje(__FILE__,__LINE__,$qCliDat);
				$xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
				$nFilCli  = mysql_num_rows($xCliDat);
				if ($nFilCli > 0) {
					$vCliDat  = mysql_fetch_array($xCliDat);
				}
			}
		
			$nAno = $vSysStr['financiero_ano_instalacion_modulo'];
		
			$mCliVen = explode("~",$vCliDat['CLIVENXX']);
      $cCliVen = "";
      for($nC=0;$nC<=count($mCliVen);$nC++) {
        if($mCliVen[$nC] != "") {
          //Buscando el nombre del vendedor
          $qCliVen  = "SELECT ";
          $qCliVen .= "IF(CLINOMXX != \"\",CLINOMXX,IF(CLIAPE1X  != \"\",CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X),\"\")) AS CLINOMXX ";
          $qCliVen .= "FROM $cAlfa.SIAI0150 ";
          $qCliVen .= "WHERE ";
          $qCliVen .= "CLIIDXXX = \"{$mCliVen[$nC]}\" LIMIT 0,1 ";
          $xCliVen  = f_MySql("SELECT","",$qCliVen,$xConexion01,"");
          // echo $qCliVen."~".mysql_num_rows($xCliVen)."<br>";
          $vCliVen = mysql_fetch_array($xCliVen);
          $cCliVen = $mCliVen[$nC]." - ".$vCliVen['CLINOMXX'];
        }
      }			
			$qCtaDat  = "SELECT DISTINCT ";
			$qCtaDat .= "$cAlfa.$cTabla.* ";
			$qCtaDat .= "FROM $cAlfa.$cTabla ";
			$qCtaDat .= "WHERE ";
			if($cTerId <> ""){
				$qCtaDat .= "$cAlfa.$cTabla.teridxxx = \"$cTerId\" AND ";
			}
			$qCtaDat .= "$cAlfa.$cTabla.regestxx = \"ACTIVO\" ";
			$qCtaDat .= "ORDER BY $cAlfa.$cTabla.teridxxx, $cAlfa.$cTabla.comidxxx, ABS($cAlfa.$cTabla.comcscxx) ASC, ABS($cAlfa.$cTabla.comseqxx) ASC ";
			$xCtaDat  = f_MySql("SELECT","",$qCtaDat,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qCtaDat);
			$nFilCta  = mysql_num_rows($xCtaDat);
			//f_Mensaje(__FILE__,__LINE__,$nFilCta);
			// Cargo la Matriz con los ROWS del Cursor
			$nA=0;
			while ($xRCD = mysql_fetch_array($xCtaDat)) {
				//Busco el nombre del cliente
				$qCliente  = "SELECT ";
				$qCliente .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,IF($cAlfa.SIAI0150.CLIAPE1X  <> \"\",CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X),\"TERCERO SIN NOMBRE\")) AS clinomxx ";
				$qCliente .= "FROM $cAlfa.SIAI0150 ";
				$qCliente .= "WHERE ";
				$qCliente .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$xRCD['teridxxx']}\" LIMIT 0,1";
				$xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
				if (mysql_num_rows($xCliente) > 0) {
					$xRC = mysql_fetch_array($xCliente);
					$xRCD['clinomxx'] = $xRC['clinomxx'];
				} else {
					$xRCD['clinomxx'] = "TERCERO SIN NOMBRE";
				}
		
				/**
				 * Consultando consecutivo 3
				 */
				if ($vSysStr['financiero_editar_consecutivo_tres_factura'] == "SI") {
					for ($nAnio = date('Y'); $nAnio >= $nAno; $nAnio--) {
						$qCsc2  = "SELECT comcsc2x, comcsc3x "; // consecutivo 3 para ups
						$qCsc2 .= "FROM $cAlfa.fcoc$nAnio ";
						$qCsc2 .= "WHERE ";
						$qCsc2 .= "comidxxx = \"{$xRCD['comidxxx']}\" AND ";
						$qCsc2 .= "comcodxx = \"{$xRCD['comcodxx']}\" AND ";
						$qCsc2 .= "comcscxx = \"{$xRCD['comcscxx']}\" AND ";
						$qCsc2 .= "regestxx = \"ACTIVO\" LIMIT 0,1";
						$xCsc2  = f_MySql("SELECT","",$qCsc2,$xConexion01,"");
						$vCsc2  = mysql_fetch_array($xCsc2);
		
						$xRCD['comcsc2x'] = $vCsc2['comcsc2x'];
						// Consecutivo 3 para ups.
						$xRCD['comcsc3x'] = $vCsc2['comcsc3x'];
					}
				}
		
				$mCtaDat[$nA] = $xRCD;
		
				if($mCtaDat[$nA]['commovxx'] == 'D'){
					$nTDebitos += $mCtaDat[$nA]['comsaldo'];
				}else{
					$nTCreditos += $mCtaDat[$nA]['comsaldo'];
				}
				$nA++;
			}
		
			//Cristian Cardona--Ajuste Reporte Estado de cuenta--Ticket 9120
			$qPucDet  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx, pucdetxx ";
			$qPucDet .= "FROM $cAlfa.fpar0115 ";
			$qPucDet .= "WHERE ";
			$qPucDet .= "pucdetxx = \"$cPuc\" AND ";
			$qPucDet .= "regestxx = \"ACTIVO\" ";
			$xPucDet  = f_MySql("SELECT","",$qPucDet,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qRetCree."~".mysql_num_rows($xRetCree));
			$mPucId = array();
			while ($xRPD = mysql_fetch_array($xPucDet)){
				$mPucId[count($mPucId)] = $xRPD['pucidxxx'];
			}
		
			$nTotalSinAsignar = 0;
			$mTotPro1 = array();
		
			for ($i = $nAno; $i <= date('Y'); $i++) {
				$qTotPro  = "SELECT fcoc$i.* ";
				$qTotPro .= "FROM $cAlfa.fcoc$i ";
				$qTotPro .= "WHERE ";
				$qTotPro .= "comidxxx != \"F\" AND ";
				$qTotPro .= "teridxxx = \"$cTerId\" AND ";
				$qTotPro .= "regestxx = \"PROVISIONAL\" ";
				$xTotPro = f_MySql("SELECT","",$qTotPro,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qTotPro."~".mysql_num_rows($xTotPro));
				while ($xRTP = mysql_fetch_array($xTotPro)) {
					//Busqueda en la tabla fcod
					$qTotPro1 = "SELECT fcod$i.* ";
					$qTotPro1 .= "FROM $cAlfa.fcod$i ";
					$qTotPro1 .= "WHERE ";
					$qTotPro1 .= "fcod$i.comidxxx= \"{$xRTP['comidxxx']}\" AND ";
					$qTotPro1 .= "fcod$i.comcodxx= \"{$xRTP['comcodxx']}\" AND ";
					$qTotPro1 .= "fcod$i.comcscxx= \"{$xRTP['comcscxx']}\" AND ";
					$qTotPro1 .= "fcod$i.comcsc2x= \"{$xRTP['comcsc2x']}\" ";
					$xTotPro1 = f_MySql("SELECT","",$qTotPro1,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qTotPro1."~".mysql_num_rows($xTotPro1));
		
					$nTotalAsignado = 0;
					while ($xRTP1 = mysql_fetch_array($xTotPro1)) {
						$nTotalAsignado += ($xRTP1['commovxx'] == 'D') ? $xRTP1['comvlrxx'] : ($xRTP1['comvlrxx']*-1);
		
						if (in_array($xRTP1['pucidxxx'],$mPucId) == true) {
							if($xRTP1['commovxx'] == 'D'){
								$nTDebitos2 += $xRTP1['comvlrxx'];
							}else{
								$nTCreditos2+= $xRTP1['comvlrxx'];
							}
							$mTotPro1[count($mTotPro1)] = $xRTP1;
						}
					}
					//Sumatoria del campo comvlrxx (Valor Comprobante) en la fcoc
					$nTotalSinAsignar += ($xRTP['comvlrxx'] + $nTotalAsignado);
				}
			}
			//Fin

			switch ($cTipo) {
				case 1:
					// PINTA POR PANTALLA// ?>
					<html>
						<head><title>Reporte de Estado de Cuentas </title>
						<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
						<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
						<script languaje = 'javascript' src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
						</head>
						<body>
						<?php if ($nFilCta > 0 OR mysql_num_rows($xEstPro) > 0) {

							?>
							<center><br>
								<table width="100%">
									<tr><td class="name"><center><h4 style="border-bottom:#6699CC 1px solid;margin-bottom:0px">REPORTE DE ESTADO DE CUENTAS</h4></center></td></tr>
								</table>
							</center>
							<center>
								<table width="100%">
									<tr>
										<td>
											<fieldset>
											<legend><h4> Resultado consulta</h4></legend>
											<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid black;">
												<tr>
													<?php switch ($cAlfa) {
														case 'ADUANAMO':
														case 'DEADUANAMO':
														case 'TEADUANAMO': ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="156" height="41" style="left: 15px;margin-top: 8px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logo_aduanamo.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4) ?>
															</center><br>
															</td>
														<?php break;
														case "LOGINCAR":
														case "DELOGINCAR":
														case "TELOGINCAR": ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="156" height="41" style="left: 20px;margin-top: 8px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/Logo_Login_Cargo_Ltda_2.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4) ?>
															</center><br>
															</td>
														<?php break;
														case "ROLDANLO"://ROLDAN
														case "TEROLDANLO"://ROLDAN
														case "DEROLDANLO"://ROLDAN ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="160" height="45" style="left: 20px;margin-top: 8px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoroldan.png">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4) ?>
															</center><br>
															</td>
														<?php break;
														case "CASTANOX":
														case "DECASTANOX":
														case "TECASTANOX": ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="90" height="45" style="left: 20px;margin-top: 8px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomartcam.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4) ?>
															</center><br>
															</td>
														<?php break;
														case "ALMACAFE": //ALMACAFE
														case "TEALMACAFE": //ALMACAFE
														case "DEALMACAFE": //ALMACAFE ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="100" height="45" style="left: 18px;margin-top: 5px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoalmacafe.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4) ?>
															</center><br>
															</td>
														<?php break;
														case "ADIMPEXX": // ADIMPEX
														case "TEADIMPEXX": // ADIMPEX
														case "DEADIMPEXX": // ADIMPEX ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="158" height="35" style="left: 18px;margin-top: 9px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoadimpex4.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4) ?>
															</center><br>
															</td>
														<?php break;
														case "GRUMALCO"://GRUMALCO
														case "TEGRUMALCO"://GRUMALCO
														case "DEGRUMALCO"://GRUMALCO ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="100" height="45" style="left: 18px;margin-top: 5px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomalco.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4) ?>
															</center><br>
															</td>
														<?php break;
														case "ALADUANA": //ALADUANA
														case "TEALADUANA": //ALADUANA
														case "DEALADUANA": //ALADUANA ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="100" height="50" style="left: 18px;margin-top: 5px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaladuana.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4) ?>
															</center><br>
															</td>
														<?php break;
														case "ANDINOSX": //ANDINOSX
														case "TEANDINOSX": //ANDINOSX
														case "DEANDINOSX": //ANDINOSX ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="120" height="50" style="left: 18px;margin-top: 5px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoandinos.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
															</center><br>
															</td>
														<?php break;
														case "GRUPOALC": //GRUPOALC
														case "TEGRUPOALC": //GRUPOALC
														case "DEGRUPOALC": //GRUPOALC ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="100" height="50" style="left: 18px;margin-top: 5px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoalc.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
															</center><br>
															</td>
														<?php break;
														case "AAINTERX": //AAINTERX
														case "TEAAINTERX": //AAINTERX
														case "DEAAINTERX": //AAINTERX ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="100" height="50" style="left: 18px;margin-top: 5px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointernacional.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4) ?>
															</center><br>
															</td>
														<?php break;
														case "AALOPEZX": 
														case "TEAALOPEZX":
														case "DEAALOPEZX": ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="100" style="left: 18px;margin-top: 5px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaalopez.png">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
															</center><br>
															</td>
														<?php break;
														case "ADUAMARX": //ADUAMARX
														case "TEADUAMARX": //ADUAMARX
														case "DEADUAMARX": //ADUAMARX ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="50" height="50" style="left: 18px;margin-top: 4px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaduamar.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
															</center><br>
															</td>
														<?php break;
														case "SOLUCION": //SOLUCION
														case "TESOLUCION": //SOLUCION
														case "DESOLUCION": //SOLUCION ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="100" style="left: 18px;margin-top: 4px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logosoluciones.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
															</center><br>
															</td>
														<?php break;
														case "FENIXSAS": //FENIXSAS
														case "TEFENIXSAS": //FENIXSAS
														case "DEFENIXSAS": //FENIXSAS ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="130" height="50" style="left: 18px;margin-top: 4px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logofenix.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
															</center><br>
															</td>
														<?php break;
														case "COLVANXX": //COLVANXX
														case "TECOLVANXX": //COLVANXX
														case "DECOLVANXX": //COLVANXX ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="140" height="50" style="left: 18px;margin-top: 2px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logocolvan.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
															</center><br>
															</td>
														<?php break;
														case "INTERLAC": //INTERLAC
														case "TEINTERLAC": //INTERLAC
														case "DEINTERLAC": //INTERLAC ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="135" height="50" style="left: 18px;margin-top: 2px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointerlace.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
															</center><br>
															</td>
														<?php break;
														default: ?>
														<td>
															<center>
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4) ?>
															</center><br>
														</td>
														<?php break;
														case "DHLEXPRE": //DHLEXPRE
														case "TEDHLEXPRE": //DHLEXPRE
														case "DEDHLEXPRE": //DHLEXPRE ?>
															<td rowspan="2" class="name"  width="100%">
															<center>
																<img width="100" height="45" style="left: 18px;margin-top: 5px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logo_dhl_express.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4) ?>
															</center><br>
															</td>
														<?php break;
														case "KARGORUX": //KARGORUX
														case "TEKARGORUX": //KARGORUX
														case "DEKARGORUX": //KARGORUX ?>
															<td rowspan="2" class="name" width="100%">
															<center>
																<img width="100" height="45" style="left: 18px;margin-top: 6px;margin-left:10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logokargoru.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
															</center><br>
															</td>
														<?php break;
														case "ALOGISAS": //LOGISTICA
														case "TEALOGISAS": //LOGISTICA
														case "DEALOGISAS": //LOGISTICA ?>
															<td rowspan="2" class="name" width="100%">
															<center>
																<img width="115" style="left: 18px;margin-top: 4px;margin-left:10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logologisticasas.jpg">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
															</center><br>
															</td>
														<?php break;
														case "PROSERCO":
														case "TEPROSERCO":
														case "DEPROSERCO": ?>
															<td rowspan="2" class="name" width="100%">
															<center>
																<img width="90" style="left: 18px;margin-top: 2px;margin-left:10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoproserco.png">
																<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																<?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
															</center><br>
															</td>
														<?php break;
                            case "MANATIAL":
                            case "TEMANATIAL":
                            case "DEMANATIAL": ?>
                              <td rowspan="2" class="name" width="100%">
                                <center>
                                  <img width="140" height="45" style="left: 18px;margin-top: 4px;margin-left:10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logomanantial.jpg">
                                  <br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
                                  <?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
                                </center><br>
                              </td>
                            <?php break;
                            case "DSVSASXX":
                            case "DEDSVSASXX":
                            case "TEDSVSASXX": ?>
                              <td rowspan="2" class="name" width="100%">
                                <center>
                                  <img width="140" height="45" style="left: 18px;margin-top: 4px;margin-left:10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logodsv.jpg">
                                  <br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
                                  <?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
                                </center><br>
                              </td>
                            <?php break;
                            case "MELYAKXX":    //MELYAK
                            case "DEMELYAKXX":  //MELYAK
                            case "TEMELYAKXX":  //MELYAK ?>
                              <td rowspan="2" class="name" width="100%">
                                <center>
                                  <img width="140" height="45" style="left: 18px;margin-top: 4px;margin-left:10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logomelyak.jpg">
                                  <br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
                                  <?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
                                </center><br>
                              </td>
                            <?php break;
                            case "FEDEXEXP":    //FEDEX
                            case "DEFEDEXEXP":  //FEDEX
                            case "TEFEDEXEXP":  //FEDEX ?>
                              <td rowspan="2" class="name" width="100%">
                                <center>
                                  <img width="100" height="45" style="left: 18px;margin-top: 4px;margin-left:10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logofedexexp.jpg">
                                  <br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
                                  <?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
                                </center><br>
                              </td>
                            <?php break;
														case "EXPORCOM":    //EXPORCOMEX
														case "DEEXPORCOM":  //EXPORCOMEX
														case "TEEXPORCOM":  //EXPORCOMEX ?>
															<td rowspan="2" class="name" width="100%">
																<center>
																	<img width="100" height="45" style="left: 18px;margin-top: 4px;margin-left:10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoexporcomex.jpg">
																	<br><span style="font-size:14px;font-weight:bold">DE CUENTA POR <?php echo $cTipoCta ?></span><br>
																	<?php echo $cMes . " " . substr($fec, 8, 2) . " DE " . substr($fec, 0, 4) ?>
																</center><br>
															</td>
														<?php break;
													} ?>
												</tr>
											</table>
											<?php if($cTerId <> ""){ ?>
												<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
													<tr>
														<td class="name" width="10%"><br>&nbsp;TERCERO</td>
														<td class="name" width="35%"><br><?php echo $vCliDat['CLINOMXX'] ?></td>
														<td class="name" width="10%"><br>NIT</td>
														<td class="name" width="45%"><br><?php echo $vCliDat['CLIIDXXX'] ?></td>
													</tr>
													<tr>
														<td class="name" width="10%"><br>&nbsp;DIRECCION</td>
														<td class="name" width="35%"><br><?php echo $vCliDat['CLIDIRXX'] ?></td>
														<td class="name" width="10%"><br>TELEFONO</td>
														<td class="name" width="10%"><br><?php echo $vCliDat['CLITELXX'] ?></td>
													</tr>
													<tr>
														<td class="name" width="10%"><br>&nbsp;CIUDAD</td>
														<td class="name" width="35%"><br><?php echo $vCliDat['CIUDESXX'] ?></td>
														<td class="name" width="25%"><br>VENDEDOR</td>
														<td class="name" width="25%"><br><?php echo $cCliVen ?></td>
													</tr>
												</table>
											<?php } ?>
											<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
												<tr>
													<td width="16%" style="font-size:14px;font-weight:bold"><br><center>DETALLE MOVIMIENTOS</center><br></td>
												</tr>
											</table>
											<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
												<tr>
													<td class="name" width="16%"><br>&nbsp;DEBITOS</td>
													<td class="name" width="17%"><br><?php echo number_format($nTDebitos,2,',','.') ?></td>
													<td class="name" width="17%"><br>CREDITOS</td>
													<td class="name" width="17%"><br><?php echo number_format($nTCreditos,2,',','.') ?></td>
													<td class="name" width="17%"><br>SALDO FINAL</td>
													<td class="name" width="16%"><br><?php echo number_format(($nTDebitos + $nTCreditos),2,',','.') ?><br></td>
												</tr>
											</table>
											<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
												<tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">
													<?php if($cTerId == ""){ ?>
														<td class="name" width="09%"><center>Nit</center></td>
														<td class="name" width="09%"><center>Tercero</center></td>
														<td class="name" width="12%"><center>Documento</center></td>
														<td class="name" width="10%"><center><?php  echo ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' ) ? 'Csc Tres' : 'Csc Dos' ?></center></td>
														<td class="name" width="09%"><center>Cuenta</center></td>
														<td class="name" width="09%"><center>Vencimiento</center></td>
														<td class="name" width="09%"><center>Fecha Documento</center></td>
														<td class="name" width="09%"><center>Fecha Vencimiento</center></td>
														<td class="name" width="10%"><center>Nro. de D&iacute;as</center></td>
														<td class="name" width="08%"><center>Valor Mora</center></td>
														<td class="name" width="08%"><center>Valor</center></td>
														<td class="name" width="08%"><center>Movimiento</center></td>
													<?php }else{ ?>
														<td class="name" width="15%"><center>Documento</center></td>
														<td class="name" width="10%"><center><?php  echo ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' ) ? 'Csc Tres' : 'Csc Dos' ?></center></td>
														<td class="name" width="10%"><center>Cuenta</center></td>
														<td class="name" width="10%"><center>Vencimiento</center></td>
														<td class="name" width="10%"><center>Fecha Documento</center></td>
														<td class="name" width="10%"><center>Fecha Vencimiento</center></td>
														<td class="name" width="15%"><center>Nro. de D&iacute;as</center></td>
														<td class="name" width="10%"><center>Valor Mora</center></td>
														<td class="name" width="10%"><center>Valor</center></td>
														<td class="name" width="10%"><center>Movimiento</center></td>
													<?php } ?>
												</tr>
												<?php
												$color = '#D5D5D5';
												$nTotSal = 0;
												$nTotSad = 0;
												for($j=0;$j<count($mCtaDat);$j++){
													if($mCtaDat[$j]['commovxx'] == 'D'){
														$mCtaDat[$j]['commovfx'] = 'DEBITO';
													}else{
														$mCtaDat[$j]['commovfx'] = 'CREDITO';
													}
													$dHoy = date('Y-m-d');
													$dFecHoy = str_replace("-","",$dHoy);
													$dFecVen = str_replace("-","",$mCtaDat[$j]['comfecve']);

													$dateHoy = mktime(0,0,0,substr($dFecHoy,4,2), substr($dFecHoy,6,2), substr($dFecHoy,0,4));
													$dateVen = mktime(0,0,0,substr($dFecVen,4,2), substr($dFecVen,6,2), substr($dFecVen,0,4));
													$valor= round(($dateHoy  - $dateVen) / (60 * 60 * 24));
													if($mCtaDat[$j]['comfecve'] < $dHoy){
														$mCtaDat[$j]['valordxx'] =  $valor." VENCIDOS";
														$nTotSal += $mCtaDat[$j]['comsaldo'];
													}else{
														$mCtaDat[$j]['valordxx'] = $valor." POR VENCER";
														$nTotSad += $mCtaDat[$j]['comsaldo'];
													} ?>
													<tr bgcolor="<?php echo $color ?>">
														<?php if($cTerId == ""){ ?>
															<td class="letra7" align="center"><?php echo $mCtaDat[$j]['teridxxx'] ?></td>
															<td class="letra7" align="left"><?php echo $mCtaDat[$j]['clinomxx'] ?></td>
														<?php } ?>
														<td class="letra7" align="left"><?php echo $mCtaDat[$j]['comidxxx']."-".$mCtaDat[$j]['comcodxx']."-".$mCtaDat[$j]['comcscxx'] ?></td>
														<td class="letra7" align="center"><?php echo ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mCtaDat[$j]['comcsc3x'] != '' ) ? $mCtaDat[$j]['comcsc3x'] : $mCtaDat[$j]['comcsc2x'] ?></td>
														<td class="letra7" align="center"><?php echo $mCtaDat[$j]['pucidxxx'] ?></td>
														<td class="letra7" align="center"><?php echo $mCtaDat[$j]['comseqxx'] ?></td>
														<td class="letra7" align="center"><?php echo $mCtaDat[$j]['regfcrex'] ?></td>
														<td class="letra7" align="center"><?php echo $mCtaDat[$j]['comfecve'] ?></td>
														<td class="letra7" align="center"><?php echo $mCtaDat[$j]['valordxx'] ?></td>
														<td class="letra7" align="center"><?php echo "0,00" ?></td>
														<td class="letra7" align="right"><?php echo number_format($mCtaDat[$j]['comsaldo'],2,',','.') ?></td>
														<td class="letra7" align="center"><?php echo $mCtaDat[$j]['commovfx'] ?></td>
													</tr>
												<?php } ?>
												</table>
												<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border-top:1px solid black; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
													<tr>
														<td class="name" width="77%" align="right"><br>VENCIDA</td>
														<td class="letra7" width="20%" align="left"><br>&nbsp;&nbsp;<?php echo number_format($nTotSal,2,',','.') ?><br></td>
													</tr>
													<tr>
														<td class="name" width="77%" align="right"><br>POR VENCER</td>
														<td class="letra7" width="20%" align="left"><br>&nbsp;&nbsp;<?php echo number_format($nTotSad,2,',','.') ?><br></td>
													</tr>
													<tr>
														<td class="name" width="77%" align="left"><br>FECHA Y HORA DE CONSULTA:&nbsp;&nbsp;&nbsp;<?php echo date('Y-m-d')." - ".date('H:i:s')?></td>
													</tr>
												</table>
												<?php if (count($mTotPro1) > 0 || $nTotalSinAsignar > 0) { ?>
													<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
														<tr>
															<td width="16%" style="font-size:14px;font-weight:bold"><br><center>RECIBOS EN ESTADO PROVISIONAL</center><br></td>
														</tr>
													</table>
													<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
														<tr>
															<td class="name" width="16%"><br>&nbsp;DEBITOS</td>
															<td class="name" width="17%"><br><?php echo number_format($nTDebitos2,2,',','.') ?></td>
															<td class="name" width="17%"><br>CREDITOS</td>
															<td class="name" width="17%"><br><?php echo number_format($nTCreditos2,2,',','.') ?></td>
															<td class="name" width="17%"><br>SALDO FINAL</td>
															<td class="name" width="16%"><br><?php echo number_format(($nTDebitos2 + $nTCreditos2),2,',','.') ?><br></td>
														</tr>
													</table>
													<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
														<tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">
															<?php if($cTerId == ""){ ?>
																<td class="name" width="09%"><center>Nit</center></td>
																<td class="name" width="09%"><center>Tercero</center></td>
																<td class="name" width="12%"><center>Documento</center></td>
																<td class="name" width="10%"><center><?php  echo ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' ) ? 'Csc Tres' : 'Csc Dos' ?></center></td>
																<td class="name" width="09%"><center>Cuenta</center></td>
																<td class="name" width="09%"><center>Vencimiento</center></td>
																<td class="name" width="09%"><center>Fecha Documento</center></td>
																<td class="name" width="09%"><center>Fecha Vencimiento</center></td>
																<td class="name" width="10%"><center>Nro. de D&iacute;as</center></td>
																<td class="name" width="08%"><center>Valor Mora</center></td>
																<td class="name" width="08%"><center>Valor</center></td>
																<td class="name" width="08%"><center>Movimiento</center></td>
															<?php }else{ ?>
																<td class="name" width="15%"><center>Documento</center></td>
																<td class="name" width="10%"><center><?php  echo ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') ? 'Csc Tres' : 'Csc Dos' ?></center></td>
																<td class="name" width="10%"><center>Cuenta</center></td>
																<td class="name" width="10%"><center>Vencimiento</center></td>
																<td class="name" width="10%"><center>Fecha Documento</center></td>
																<td class="name" width="10%"><center>Fecha Vencimiento</center></td>
																<td class="name" width="15%"><center>Nro. de D&iacute;as</center></td>
																<td class="name" width="10%"><center>Valor Mora</center></td>
																<td class="name" width="10%"><center>Valor</center></td>
																<td class="name" width="10%"><center>Movimiento</center></td>
															<?php } ?>
														</tr>
														<?php
														$color = '#D5D5D5';
														$nTotPro = 0;
														for($j=0;$j<count($mTotPro1);$j++){

															if($mTotPro1[$j]['commovxx'] == 'D'){
																$mTotPro1[$j]['commovfx'] = 'DEBITO';
															}else{
																$mTotPro1[$j]['commovfx'] = 'CREDITO';
															}
															$dHoy = date('Y-m-d');
															$dFecHoy = str_replace("-","",$dHoy);
															$dFecVen = str_replace("-","",$mTotPro1[$j]['comfecve']);

															$dateHoy = mktime(0,0,0,substr($dFecHoy,4,2), substr($dFecHoy,6,2), substr($dFecHoy,0,4));
															$dateVen = mktime(0,0,0,substr($dFecVen,4,2), substr($dFecVen,6,2), substr($dFecVen,0,4));
															$valor= round(($dateHoy  - $dateVen) / (60 * 60 * 24));

															if($mTotPro1[$j]['comfecve'] < $dHoy){
																$mTotPro1[$j]['valordxx'] =  $valor." VENCIDOS";
																//$nTotSal += $mEstPro2[$j]['comsaldo'];
															}else{
																$mTotPro1[$j]['valordxx'] = $valor." POR VENCER";
																//$nTotSad += $mEstPro2[$j]['comsaldo'];
															}
															$nTotPro +=($mTotPro1[$j]['commovxx']=="D") ? number_format($mTotPro1[$j]['comvlrxx'],2,',','.'):number_format(($mTotPro1[$j]['comvlrxx']*-1),2,',','.'); ?>
															<tr bgcolor="<?php echo $color ?>">
																<?php if($cTerId == ""){ ?>
																	<td class="letra7" align="center"><?php echo $mTotPro1[$j]['teridxxx'] ?></td>
																	<td class="letra7" align="left"><?php echo $mTotPro1[$j]['clinomxx'] ?></td>
																<?php } ?>
																<td class="letra7" align="left"><?php echo $mTotPro1[$j]['comidxxx']."-".$mTotPro1[$j]['comcodxx']."-".$mTotPro1[$j]['comcscxx'] ?></td>
																<td class="letra7" align="center"><?php echo ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mTotPro1[$j]['comcsc3x'] != '' ) ? $mTotPro1[$j]['comcsc3x'] : $mTotPro1[$j]['comcsc2x'] ?></td>
																<td class="letra7" align="center"><?php echo $mTotPro1[$j]['pucidxxx'] ?></td>
																<td class="letra7" align="center"><?php echo $mTotPro1[$j]['comseqxx'] ?></td> <!-- secuanencia comprobante -->
																<td class="letra7" align="center"><?php echo $mTotPro1[$j]['regfcrex'] ?></td>
																<td class="letra7" align="center"><?php echo $mTotPro1[$j]['comfecve'] ?></td>
																<td class="letra7" align="center"><?php echo $mTotPro1[$j]['valordxx'] ?></td>
																<td class="letra7" align="center"><?php echo "0,00" ?></td>
																<td class="letra7" align="right"><?php echo ($mTotPro1[$j]['commovxx']=="D") ? number_format($mEstPro2[$j]['comvlrxx'],2,',','.'):number_format(($mTotPro1[$j]['comvlrxx']*-1),2,',','.'); ?></td>
																<td class="letra7" align="center"><?php echo $mTotPro1[$j]['commovfx'] ?></td>
															</tr>
														<?php } ?>
													</table>
													<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border-top:1px solid black; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
														<tr>
															<td class="name" width="77%" align="right"><br>TOTAL RECIBOS PROVISIONALES SIN ASIGNAR</td>
															<td class="letra7" width="20%" align="left"><br>&nbsp;&nbsp;<?php echo number_format($nTotalSinAsignar,2,',','.') ?><br></td>
														</tr>
														<tr>
															<td class="name" width="77%" align="right"><br>TOTAL RECIBOS PROVISIONALES</td>
															<td class="letra7" width="20%" align="left"><br>&nbsp;&nbsp;<?php echo number_format(abs($nTotPro),2,',','.') ?><br></td>
														</tr>
														<tr>
															<td class="name" width="77%" align="right"><br>TOTAL CARTERA</td>
															<td class="letra7" width="20%" align="left"><br>&nbsp;&nbsp;<?php echo number_format(($nTDebitos + $nTCreditos+ $nTotPro),2,',','.') ?><br></td>
														</tr>
														<tr>
															<td class="name" width="77%" align="right"><br>TOTAL CARTERA - RECIBOS PROVISIONALES</td>
															<td class="letra7" width="20%" align="left"><br>&nbsp;&nbsp;<?php echo number_format(($nTDebitos + $nTCreditos),2,',','.') ?><br></td>
														</tr>
														<tr>
															<td class="name" width="77%" align="left"><br>FECHA Y HORA DE CONSULTA:&nbsp;&nbsp;&nbsp;<?php echo date('Y-m-d')." - ".date('H:i:s')?></td>
														</tr>
													</table>
												<?php } else{
													echo "No tiene comprobantes en Estado Provisional.";
												} ?>
											</fieldset>
										</td>
									</tr>
								</table>
							</center>
						<?php }else{
							echo "El Cliente No tiene registros.";
						} ?>
					</body>
				</html>
				<?php break;

				case 2:
					if ($nFilCta > 0 OR mysql_num_rows($xEstPro) > 0) {
						// PINTA POR EXCEL //
						$header .= 'REPORTE DE ESTADO DE CUENTAS'."\n";
						$header .= "\n";
						$data = '';
						$cNomFile = "ESTADO_DE_CUENTA_POR_". $cTipoCta .".xls";

						if ($_SERVER["SERVER_PORT"] != "") {
							$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
						} else {
							$cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
						}

						if (file_exists($cFile)) {
							unlink($cFile);
						}

						$fOp = fopen($cFile, 'a');

						/**
						* Cargando Datos en la variable
						*/

						if($cTerId <> ""){
							$nColSpan = 9;
							$nTamCol = 1;
						}else{
							$nColSpan = 11;
							$nTamCol = 2;
						}

						$data .= '<table width="1024px" cellpadding="1" cellspacing="1" border="1" style="font-family:arial;font-size:12px;border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">';
							$data .= '<tr>';
								$data .= '<td colspan="'.$nColSpan.'" style="font-size:14px;font-weight:bold"><center>ESTADO DE CUENTA POR '.$cTipoCta.'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td colspan="'.$nColSpan.'"><B><center>'.$cMes.' '.substr($fec,8,2).' DE '.substr($fec,0,4).'</center></B></td>';
							$data .= '</tr>';

							if($cTerId <> ""){
								$data .= '<tr>';
								$data .= '<td><B>TERCERO</B></td>';
								$data .= '<td colspan="'.($nColSpan-3).'">'.$vCliDat['CLINOMXX'].'</td>';
								$data .= '<td><B>NIT</B></td>';
								$data .= '<td>'.$vCliDat['CLIIDXXX'].'</td>';
								$data .= '</tr>';
								$data .= '<tr>';
								$data .= '<td><B>DIRECCION</B></td>';
								$data .= '<td colspan="'.($nColSpan-3).'">'.$vCliDat['CLIDIRXX'].'</td>';
								$data .= '<td><B>TELEFONO</B></td>';
								$data .= '<td>'.$vCliDat['CLITELXX'].'</td>';
								$data .= '</tr>';
								$data .= '<tr>';
								$data .= '<td><B>CIUDAD</B></td>';
								$data .= '<td colspan="2" >'.$vCliDat['CIUDESXX'].'</td>';
								$data .= '<td><B>VENDEDOR</B></td>';
								$data .= '<td colspan="'.($nColSpan-4).'">'.$cCliVen.'</td>';
								$data .= '</tr>';
							}

							$nNumCol = ceil(($nColSpan-($nTamCol*4))/3);
							$data .= '<tr><td colspan="'.$nColSpan.'"></td></tr>';
							$data .= '<tr>';
								$data .= '<td colspan="'.$nTamCol.'"></td>';
								$data .= '<td colspan="'.($nColSpan-(2*$nNumCol)-($nTamCol*4)).'" align="right"><B>DEBITOS</B></td>';
								$data .= '<td colspan="'.$nTamCol.'">'.number_format($nTDebitos,2,',','.').'</td>';
								$data .= '<td colspan="'.$nNumCol.'" align="right"><B>CREDITOS</B></td>';
								$data .= '<td colspan="'.$nTamCol.'">'.number_format($nTCreditos,2,',','.').'</td>';
								$data .= '<td colspan="'.$nNumCol.'" align="right"><B>SALDO FINAL</B></td>';
								$data .= '<td colspan="	'.$nTamCol.'">'.number_format(($nTDebitos + $nTCreditos),2,',','.').'</td>';
							$data .= '</tr>';
							$data .= '<tr><td colspan="'.$nColSpan.'"></td></tr>';
							$data .= '<tr>';
								$data .= '<td colspan="'.$nColSpan.'" style="font-size:14px;font-weight:bold"><B><center>DETALLE MOVIMIENTOS</center></B></td>';
							$data .= '</tr>';

							$data .= '<tr style="font-weight:bold">';
							if($cTerId == ""){
								$data .= '<td width="09%"><center>Nit</center></td>';
								$data .= '<td width="09%"><center>Tercero</center></td>';
								$data .= '<td width="12%"><center>Documento</center></td>';
								$data .= '<td width="09%"><center>Cuenta</center></td>';
								$data .= '<td width="09%"><center>Vencimiento</center></td>';
								$data .= '<td width="09%"><center>Fecha Documento</center></td>';
								$data .= '<td width="09%"><center>Fecha Vencimiento</center></td>';
								$data .= '<td width="10%"><center>Nro. de D&iacute;as</center></td>';
								$data .= '<td width="08%"><center>Valor Mora</center></td>';
								$data .= '<td width="08%"><center>Valor</center></td>';
								$data .= '<td width="08%"><center>Movimiento</center></td>';
							}else{
								$data .= '<td width="15%"><center>Documento</center></td>';
								$data .= '<td width="10%"><center>Cuenta</center></td>';
								$data .= '<td width="10%"><center>Vencimiento</center></td>';
								$data .= '<td width="10%"><center>Fecha Documento</center></td>';
								$data .= '<td width="10%"><center>Fecha Vencimiento</center></td>';
								$data .= '<td width="15%"><center>Nro. de D&iacute;as</center></td>';
								$data .= '<td width="10%"><center>Valor Mora</center></td>';
								$data .= '<td width="10%"><center>Valor</center></td>';
								$data .= '<td width="10%"><center>Movimiento</center></td>';
							}
							$data .= '</tr>';

							$nTotSal = 0;
							$nTotSad = 0;
							for($j=0;$j<count($mCtaDat);$j++){
								if($mCtaDat[$j]['commovxx'] == 'D'){
									$mCtaDat[$j]['commovfx'] = 'DEBITO';
								}else{
									$mCtaDat[$j]['commovfx'] = 'CREDITO';
								}
								$dHoy = date('Y-m-d');
								$dFecHoy = str_replace("-","",$dHoy);
								$dFecVen = str_replace("-","",$mCtaDat[$j]['comfecve']);

								$dateHoy = mktime(0,0,0,substr($dFecHoy,4,2), substr($dFecHoy,6,2), substr($dFecHoy,0,4));
								$dateVen = mktime(0,0,0,substr($dFecVen,4,2), substr($dFecVen,6,2), substr($dFecVen,0,4));
								$valor= round(($dateHoy  - $dateVen) / (60 * 60 * 24));
								if($mCtaDat[$j]['comfecve'] < $dHoy){
									$mCtaDat[$j]['valordxx'] =  $valor." VENCIDOS";
									$nTotSal += $mCtaDat[$j]['comsaldo'];
								}else{
									$mCtaDat[$j]['valordxx'] = $valor." POR VENCER";
									$nTotSad += $mCtaDat[$j]['comsaldo'];
								}
								$data .= '<tr>';
									if($cTerId == ""){
										$data .= '<td class="letra7" align="center">'.$mCtaDat[$j]['teridxxx'].'</td>';
										$data .= '<td class="letra7" align="left">'.$mCtaDat[$j]['clinomxx'].'</td>';
									}
									$data .= '<td class="letra7" align="left">'.$mCtaDat[$j]['comidxxx']."-".$mCtaDat[$j]['comcodxx']."-".$mCtaDat[$j]['comcscxx'].'</td>';
									$data .= '<td class="letra7" align="center">'.$mCtaDat[$j]['pucidxxx'].'</td>';
									$data .= '<td class="letra7" align="center">'.$mCtaDat[$j]['comseqxx'].'</td>';
									$data .= '<td class="letra7" align="center">'.$mCtaDat[$j]['regfcrex'].'</td>';
									$data .= '<td class="letra7" align="center">'.$mCtaDat[$j]['comfecve'].'</td>';
									$data .= '<td class="letra7" align="center">'.$mCtaDat[$j]['valordxx'].'</td>';
									$data .= '<td class="letra7" align="center">'."0,00".'</td>';
									$data .= '<td class="letra7" align="right">'.number_format($mCtaDat[$j]['comsaldo'],2,',','.').'</td>';
									$data .= '<td class="letra7" align="center">'.$mCtaDat[$j]['commovfx'].'</td>';
								$data .= '</tr>';
							}

							$data .= '<tr><td colspan="'.$nColSpan.'"></td></tr>';

							$data .= '<tr>';
								$data .= '<td colspan="'.($nColSpan-1).'" align="right"><B>VENCIDA</B></td>';
								$data .= '<td colspan="1" class="letra7" align="left">&nbsp;&nbsp;'. number_format($nTotSal,2,',','.').'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td colspan="'.($nColSpan-1).'" align="right"><B>POR VENCER</B></td>';
								$data .= '<td colspan="1" class="letra7" align="left">&nbsp;&nbsp;'. number_format($nTotSad,2,',','.').'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td colspan="'.$nColSpan.'" align="left"><B>FECHA Y HORA DE CONSULTA:</B>&nbsp;&nbsp;&nbsp;'. date('Y-m-d').' - '.date('H:i:s').'</td>';
							$data .= '</tr>';

							//Resibos Provisionales
							if (count($mTotPro1) > 0 || $nTotalSinAsignar > 0) {
								$data .= '<tr>';
								$data .= '<td colspan="'.$nColSpan.'" style="font-size:14px;font-weight:bold"><B><center>.</center></B></td>';
								$data .= '</tr>';
								$data .= '<tr>';
								$data .= '<td colspan="'.$nColSpan.'" style="font-size:14px;font-weight:bold"><B><center>RECIBOS EN ESTADO PROVISIONAL</center></B></td>';
								$data .= '</tr>';
								$data .= '<tr style="font-weight:bold">';
								if($cTerId == ""){
									$data .= '<td width="09%"><center>Nit</center></td>';
									$data .= '<td width="09%"><center>Tercero</center></td>';
									$data .= '<td width="12%"><center>Documento</center></td>';
									$data .= '<td width="09%"><center>Cuenta</center></td>';
									$data .= '<td width="09%"><center>Vencimiento</center></td>';
									$data .= '<td width="09%"><center>Fecha Documento</center></td>';
									$data .= '<td width="09%"><center>Fecha Vencimiento</center></td>';
									$data .= '<td width="10%"><center>Nro. de D&iacute;as</center></td>';
									$data .= '<td width="08%"><center>Valor Mora</center></td>';
									$data .= '<td width="08%"><center>Valor</center></td>';
									$data .= '<td width="08%"><center>Movimiento</center></td>';
								}else{
									$data .= '<td width="15%"><center>Documento</center></td>';
									$data .= '<td width="10%"><center>Cuenta</center></td>';
									$data .= '<td width="10%"><center>Vencimiento</center></td>';
									$data .= '<td width="10%"><center>Fecha Documento</center></td>';
									$data .= '<td width="10%"><center>Fecha Vencimiento</center></td>';
									$data .= '<td width="15%"><center>Nro. de D&iacute;as</center></td>';
									$data .= '<td width="10%"><center>Valor Mora</center></td>';
									$data .= '<td width="10%"><center>Valor</center></td>';
									$data .= '<td width="10%"><center>Movimiento</center></td>';
								}

								$data .= '</tr>';

								$nTotSal = 0;
								$nTotSad = 0;
								for($j=0;$j<count($mTotPro1);$j++){
									if($mTotPro1[$j]['commovxx'] == 'D'){
										$mTotPro1[$j]['commovfx'] = 'DEBITO';
									}else{
										$mTotPro1[$j]['commovfx'] = 'CREDITO';
									}
									$dHoy = date('Y-m-d');
									$dFecHoy = str_replace("-","",$dHoy);
									$dFecVen = str_replace("-","",$mTotPro1[$j]['comfecve']);

									$dateHoy = mktime(0,0,0,substr($dFecHoy,4,2), substr($dFecHoy,6,2), substr($dFecHoy,0,4));
									$dateVen = mktime(0,0,0,substr($dFecVen,4,2), substr($dFecVen,6,2), substr($dFecVen,0,4));
									$valor= round(($dateHoy  - $dateVen) / (60 * 60 * 24));
									if($mTotPro1[$j]['comfecve'] < $dHoy){
										$mTotPro1[$j]['valordxx'] =  $valor." VENCIDOS";
										//$nTotSal += $mCtaDat[$j]['comsaldo'];
									}else{
										$mTotPro1[$j]['valordxx'] = $valor." POR VENCER";
										//$nTotSad += $mCtaDat[$j]['comsaldo'];
									}

									$nTotPro +=($mTotPro1[$j]['commovxx']=="D") ? number_format($mTotPro1[$j]['comvlrxx'],2,',','.'):number_format(($mTotPro1[$j]['comvlrxx']*-1),2,',','.');

									$data .= '<tr>';
									$vCommo=($mTotPro1[$j]['commovxx']=="D") ? number_format($mTotPro1[$j]['comvlrxx'],2,',','.'):number_format(($mTotPro1[$j]['comvlrxx']*-1),2,',','.');
									if($cTerId == ""){
										$data .= '<td class="letra7" align="center">'.$mTotPro1[$j]['teridxxx'].'</td>';
										$data .= '<td class="letra7" align="left">'.$mTotPro1[$j]['clinomxx'].'</td>';
									}
									$data .= '<td class="letra7" align="left">'.$mTotPro1[$j]['comidxxx']."-".$mTotPro1[$j]['comcodxx']."-".$mTotPro1[$j]['comcscxx'].'</td>';
									$data .= '<td class="letra7" align="center">'.$mTotPro1[$j]['pucidxxx'].'</td>';
									$data .= '<td class="letra7" align="center">'.$mTotPro1[$j]['comseqxx'].'</td>';
									$data .= '<td class="letra7" align="center">'.$mTotPro1[$j]['regfcrex'].'</td>';
									$data .= '<td class="letra7" align="center">'.$mTotPro1[$j]['comfecve'].'</td>';
									$data .= '<td class="letra7" align="center">'.$mTotPro1[$j]['valordxx'].'</td>';
									$data .= '<td class="letra7" align="center">'."0,00".'</td>';
									$data .= '<td class="letra7" align="right">'.number_format($vCommo,2,',','.').'</td>';
									$data .= '<td class="letra7" align="center">'.$mTotPro1[$j]['commovfx'].'</td>';
									$data .= '</tr>';
								}

								$data .= '<tr><td colspan="'.$nColSpan.'"></td></tr>';

								$data .= '<tr>';
								$data .= '<td colspan="'.($nColSpan-1).'" align="right"><B>TOTAL RECIBOS PROVISIONALES SIN ASIGNAR</B></td>';
								$data .= '<td colspan="1" class="letra7" align="left">&nbsp;&nbsp;'. number_format($nTotalSinAsignar,2,',','.').'</td>';
								$data .= '</tr>';
								$data .= '<tr>';
								$data .= '<td colspan="'.($nColSpan-1).'" align="right"><B>TOTAL RECIBOS PROVISIONALES</B></td>';
								$data .= '<td colspan="1" class="letra7" align="left">&nbsp;&nbsp;'. number_format(abs($nTotPro),2,',','.').'</td>';
								$data .= '</tr>';
								$data .= '<tr>';
								$data .= '<td colspan="'.($nColSpan-1).'" align="right"><B>TOTAL CARTERA</B></td>';
								$data .= '<td colspan="1" class="letra7" align="left">&nbsp;&nbsp;'. number_format(($nTDebitos + $nTCreditos+ $nTotPro),2,',','.').'</td>';
								$data .= '</tr>';
								$data .= '<tr>';
								$data .= '<td colspan="'.($nColSpan-1).'" align="right"><B>TOTAL CARTERA - RECIBOS PROVISIONALES</B></td>';
								$data .= '<td colspan="1" class="letra7" align="left">&nbsp;&nbsp;'. number_format(($nTDebitos + $nTCreditos),2,',','.').'</td>';
								$data .= '</tr>';

							}else{
								$data .= '<tr>';
								$data .= '<td colspan="'.($nColSpan-1).'" align="right"><B>No tiene comprobantes en Estado Provisional.</B></td>';
								$data .= '</tr>';
							}
						$data .= '</table>';
						
						fwrite($fOp, $data);
						fclose($fOp);

						if (file_exists($cFile)) {

							chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
							$cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFile);
		
							if ($_SERVER["SERVER_PORT"] != "") {
								header('Content-Description: File Transfer');
								header('Content-Type: application/octet-stream');
								header('Content-Disposition: attachment; filename=' . $cDownLoadFilename);
								header('Content-Transfer-Encoding: binary');
								header('Expires: 0');
								header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
								header('Pragma: public');
								header('Content-Length: ' . filesize($cFile));
		
								ob_clean();
								flush();
								readfile($cFile);
							} else {
								$cNomArc = $cNomFile;
							}
						} else {
							$nSwitch = 1;
							if ($_SERVER["SERVER_PORT"] != "") {
								f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
							} else {
								$cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
							}
						}
					} else {
						if ($_SERVER["SERVER_PORT"] != "") {
              f_Mensaje(__FILE__,__LINE__,"No se encontraron registros.");
            } else {
              $cMsj .= "No se encontraron registros.";
            }
					}
				break;

				case 3 :
					/* PINTA POR PDF */

					if ($nFilCta > 0 OR mysql_num_rows($xEstPro) > 0) {
						$cRoot = $_SERVER['DOCUMENT_ROOT'];

						define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
						require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

						class PDF extends FPDF {
							function Header() {
								global $cRoot; global $cPlesk_Skin_Directory;
								global $cAlfa; global $cTipoCta; global $cMes; global $fec; global $cTerId; global $nPag;

								if($cAlfa == "INTERLOG" || $cAlfa == "DESARROL" || $cAlfa == "PRUEBASX"){

									$this->SetXY(13,7);
									$this->Cell(42,28,'',1,0,'C');
									$this->Cell(213,28,'',1,0,'C');

									// Dibujo //
									$this->Image($cRoot.$cPlesk_Skin_Directory.'/MaryAire.jpg',14,8,40,25);

									$this->SetFont('verdana','',16);
									$this->SetXY(55,15);
									$this->Cell(213,8,"ESTADO DE CUENTA POR $cTipoCta",0,0,'C');
									$this->Ln(8);
									$this->SetFont('verdana','',12);
									$this->SetX(55);
									$this->Cell(213,6,"$cMes ".substr($fec,8,2)." DE ".substr($fec,0,4),0,0,'C');
									$this->Ln(15);
									$this->SetX(13);
								}else{

									##Impresión de Logo de ADIMPEX en la parte superior derecha##
									switch($cAlfa){
										case "TEADIMPEXX": // ADIMPEX
										case "DEADIMPEXX": // ADIMPEX
										case "ADIMPEXX": // ADIMPEX
											$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex5.jpg',255,00,25,20);
										break;
										default:
											// No hace nada
										break;
									}
									##Fin Impresión de Logo de ADIMPEX en la parte superior derecha##

									$this->SetXY(13,7);
									$this->Cell(255,15,'',1,0,'C');

									$this->SetFont('verdana','',16);
									$this->SetXY(13,8);
									$this->Cell(255,8,"ESTADO DE CUENTA POR $cTipoCta",0,0,'C');
									$this->Ln(8);
									$this->SetFont('verdana','',12);
									$this->SetX(13);
									$this->Cell(255,6,"$cMes ".substr($fec,8,2)." DE ".substr($fec,0,4),0,0,'C');
									$this->Ln(10);
									$this->SetX(13);
								}

								switch ($cAlfa) {
									case 'ADUANAMO':
									case 'DEADUANAMO':
									case 'TEADUANAMO':
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logo_aduanamo.jpg',15,8,30,'');
									break;
									case "LOGINCAR":
									case "DELOGINCAR":
									case "TELOGINCAR":
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/Logo_Login_Cargo_Ltda_2.jpg',15,8,38,'');
									break;
									case "TRLXXXXX":
									case "DETRLXXXXX":
									case "TETRLXXXXX":
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logobma.jpg',15,8,40,13);
									break;
									case "TEADIMPEXX": // ADIMPEX
									case "DEADIMPEXX": // ADIMPEX
									case "ADIMPEXX": // ADIMPEX
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoadimpex4.jpg',17,10,36,8);
									break;
									case "ROLDANLO"://ROLDAN
									case "TEROLDANLO"://ROLDAN
									case "DEROLDANLO"://ROLDAN
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',15,8,40,13);
									break;
									case "CASTANOX":
									case "TECASTANOX":
									case "DECASTANOX":
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomartcam.jpg',15,8,28,12);
									break;
									case "ALMACAFE": //ALMACAFE
									case "TEALMACAFE": //ALMACAFE
									case "DEALMACAFE": //ALMACAFE
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalmacafe.jpg',15,8,28,12);
									break;
									case "GRUMALCO": //GRUMALCO
									case "TEGRUMALCO": //GRUMALCO
									case "DEGRUMALCO": //GRUMALCO
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',15,8,28,13);
									break;
									case "ALADUANA": //ALADUANA
									case "TEALADUANA": //ALADUANA
									case "DEALADUANA": //ALADUANA
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',15,8,22,13);
									break;
									case "ANDINOSX": //ANDINOSX
									case "TEANDINOSX": //ANDINOSX
									case "DEANDINOSX": //ANDINOSX
										$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoandinos.jpg', 14, $py + 8, 36, 12);
									break;
									case "GRUPOALC": //GRUPOALC
									case "TEGRUPOALC": //GRUPOALC
									case "DEGRUPOALC": //GRUPOALC
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalc.jpg',15,8,28,13);
									break;
									case "AAINTERX": //AAINTERX
									case "TEAAINTERX": //AAINTERX
									case "DEAAINTERX": //AAINTERX
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointernacional.jpg',15,8,22,13);
									break;
									case "AALOPEZX":
									case "TEAALOPEZX":
									case "DEAALOPEZX":
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaalopez.png',15,$py+8,25);
									break;
									case "ADUAMARX": //ADUAMARX
									case "TEADUAMARX": //ADUAMARX
									case "DEADUAMARX": //ADUAMARX
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg',15,7.5,14);
									break;
									case "SOLUCION": //SOLUCION
									case "TESOLUCION": //SOLUCION
									case "DESOLUCION": //SOLUCION
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',15,$py+8,30);
									break;
									case "FENIXSAS": //FENIXSAS
									case "TEFENIXSAS": //FENIXSAS
									case "DEFENIXSAS": //FENIXSAS
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg',15,10,33);
									break;
									case "COLVANXX": //COLVANXX
									case "TECOLVANXX": //COLVANXX
									case "DECOLVANXX": //COLVANXX
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logocolvan.jpg', 15, 8, 32);
									break;
									case "INTERLAC": //INTERLAC
									case "TEINTERLAC": //INTERLAC
									case "DEINTERLAC": //INTERLAC
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointerlace.jpg', 15, 8, 29);
									break;
									case "DHLEXPRE": //DHLEXPRE
									case "TEDHLEXPRE": //DHLEXPRE
									case "DEDHLEXPRE": //DHLEXPRE
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',15,8,28,13);
									break;
									case "KARGORUX": //KARGORUX
									case "TEKARGORUX": //KARGORUX
									case "DEKARGORUX": //KARGORUX
										$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logokargoru.jpg', 15, 8, 28, 13);
									break;
									case "ALOGISAS": //LOGISTICA
									case "TEALOGISAS": //LOGISTICA
									case "DEALOGISAS": //LOGISTICA
										$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 15, 8, 32);
									break;
									case "PROSERCO":
									case "TEPROSERCO":
									case "DEPROSERCO":
										$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoproserco.png', 14, 8, 24);
									break;
                  case "MANATIAL":
                  case "TEMANATIAL":
                  case "DEMANATIAL":
                    $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomanantial.jpg', 14, 8, 36, 13);
                  break;
                  case "DSVSASXX":
                  case "DEDSVSASXX":
                  case "TEDSVSASXX":
                    $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logodsv.jpg', 14, 8, 36, 13);
                  break;
                  case "MELYAKXX":    //MELYAK
                  case "DEMELYAKXX":  //MELYAK
                  case "TEMELYAKXX":  //MELYAK
                    $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomelyak.jpg', 14, 8, 36, 13);
                  break;
                  case "FEDEXEXP":
                  case "DEFEDEXEXP":
                  case "TEFEDEXEXP":
                    $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logofedexexp.jpg',14,8,30,13);
                  break;
									case "EXPORCOM":
									case "DEEXPORCOM":
									case "TEEXPORCOM":
										$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoexporcomex.jpg',14,8,30,13);
									break;
								}

								if($this->PageNo() > 1 && $nPag ==1){
									if($cTerId == ""){
										$this->SetFont('verdana','B',7);
										$this->SetWidths(array('20','45','28','18','20','20','20','24','10','30','20'));
										$this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C'));
										$this->SetX(13);
										$this->Row(array("Nit",
																		"Tercero",
																		"Documento",
																		"Cuenta",
																		"Vencimiento",
																		"Fecha Documento",
																		"Fecha Vencimiento",
																		"Nro. de Dias",
																		"Valor Mora",
																		"Valor",
																		"Movimiento"));
										$this->SetFont('verdana','',7);
										$this->SetAligns(array('C','L','L','C','C','C','C','L','C','R','C'));
									}else{
										$this->SetWidths(array('40','25','20','30','30','30','30','30','20'));
										$this->SetAligns(array('C','C','C','C','C','C','C','C','C'));
										$this->SetX(13);
										$this->Row(array("Documento",
																		"Cuenta",
																		"Vencimiento",
																		"Fecha Documento",
																		"Fecha Vencimiento",
																		"Nro. de Dias",
																		"Valor Mora",
																		"Valor",
																		"Movimiento"));
										$this->SetFont('verdana','',7);
										$this->SetAligns(array('L','C','C','C','C','L','C','R','C'));
									}
									$this->SetX(13);
								}

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
										//Draw the border
										$this->Rect($x,$y,$w,$h);
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


						$pdf = new PDF('L','mm','Letter');
						$pdf->AddFont('verdana','','');
						$pdf->AddFont('verdana','B','');
						$pdf->AliasNbPages();
						$pdf->SetMargins(0,0,0);

						$pdf->AddPage();

						if($cTerId <> ""){
							$pdf->SetX(13);
							$pdf->SetFont('verdana','B',8);
							$pdf->Cell(22,5,"TERCERO:",0,0,'L');
							$pdf->SetFont('verdana','',8);
							$pdf->Cell(163,5,$vCliDat['CLINOMXX'],0,0,'L');

							$pdf->SetFont('verdana','B',8);
							$pdf->Cell(10,5,"NIT:",0,0,'L');
							$pdf->SetFont('verdana','',8);
							$pdf->Cell(60,5,$vCliDat['CLIIDXXX'],0,0,'L');

							$pdf->Ln(5);
							$pdf->SetX(13);
							$pdf->SetFont('verdana','B',8);
							$pdf->Cell(22,5,"DIRECCION:",0,0,'L');
							$pdf->SetFont('verdana','',8);
							$pdf->Cell(153,5,$vCliDat['CLIDIRXX'],0,0,'L');

							$pdf->SetFont('verdana','B',8);
							$pdf->Cell(20,5,"TELEFONO:",0,0,'L');
							$pdf->SetFont('verdana','',8);
							$pdf->Cell(60,5,$vCliDat['CLITELXX'],0,0,'L');
							$pdf->Ln(10);
						}

						$pdf->SetX(13);
						$pdf->SetFont('verdana','B',8);
						$pdf->Cell(25,5,"DEBITOS:",0,0,'L');
						$pdf->SetFont('verdana','',8);
						$pdf->Cell(60,5,number_format($nTDebitos,2,',','.'),0,0,'L');

						$pdf->SetFont('verdana','B',8);
						$pdf->Cell(25,5,"CREDITOS:",0,0,'L');
						$pdf->SetFont('verdana','',8);
						$pdf->Cell(60,5,number_format($nTCreditos,2,',','.'),0,0,'L');

						$pdf->SetFont('verdana','B',8);
						$pdf->Cell(25,5,"SALDO FINAL:",0,0,'L');
						$pdf->SetFont('verdana','',8);
						$pdf->Cell(60,5,number_format(($nTDebitos + $nTCreditos),2,',','.'),0,0,'L');

						$pdf->Ln(7);
						$pdf->SetFont('verdana','B',7);
						if($cTerId == ""){
							$pdf->SetWidths(array('20','45','28','18','20','20','20','24','10','30','20'));
							$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C'));
							$pdf->SetX(13);
							$pdf->Row(array("Nit",
															"Tercero",
															"Documento",
															"Cuenta",
															"Vencimiento",
															"Fecha Documento",
															"Fecha Vencimiento",
															"Nro. de Dias",
															"Valor Mora",
															"Valor",
															"Movimiento"));
							$pdf->SetFont('verdana','',7);
							$pdf->SetAligns(array('C','L','L','C','C','C','C','L','C','R','C'));
						}else{
							$pdf->SetWidths(array('40','25','20','30','30','30','30','30','20'));
							$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
							$pdf->SetX(13);
							$pdf->Row(array("Documento",
															"Cuenta",
															"Vencimiento",
															"Fecha Documento",
															"Fecha Vencimiento",
															"Nro. de Dias",
															"Valor Mora",
															"Valor",
															"Movimiento"));
							$pdf->SetFont('verdana','',7);
							$pdf->SetAligns(array('L','C','C','C','C','L','C','R','C'));
						}

						$nTotSal = 0;
						$nTotSad = 0;
						$nPag = 0;
						for($j=0;$j<count($mCtaDat);$j++){
							$nPag = 1;
							if($mCtaDat[$j]['commovxx'] == 'D'){
								$mCtaDat[$j]['commovfx'] = 'DEBITO';
							}else{
								$mCtaDat[$j]['commovfx'] = 'CREDITO';
							}
							$dHoy = date('Y-m-d');
							$dFecHoy = str_replace("-","",$dHoy);
							$dFecVen = str_replace("-","",$mCtaDat[$j]['comfecve']);

							$dateHoy = mktime(0,0,0,substr($dFecHoy,4,2), substr($dFecHoy,6,2), substr($dFecHoy,0,4));
							$dateVen = mktime(0,0,0,substr($dFecVen,4,2), substr($dFecVen,6,2), substr($dFecVen,0,4));
							$valor= round(($dateHoy  - $dateVen) / (60 * 60 * 24));
							if($mCtaDat[$j]['comfecve'] < $dHoy){
								$mCtaDat[$j]['valordxx'] =  $valor." VENCIDOS";
								$nTotSal += $mCtaDat[$j]['comsaldo'];
							}else{
								$mCtaDat[$j]['valordxx'] = $valor." POR VENCER";
								$nTotSad += $mCtaDat[$j]['comsaldo'];
							}

							$pdf->SetX(13);
							if($cTerId == ""){
								$pdf->Row(array($mCtaDat[$j]['teridxxx'],
																$mCtaDat[$j]['clinomxx'],
																$mCtaDat[$j]['comidxxx']."-".$mCtaDat[$j]['comcodxx']."-".$mCtaDat[$j]['comcscxx'],
																$mCtaDat[$j]['pucidxxx'],
																$mCtaDat[$j]['comseqxx'],
																$mCtaDat[$j]['regfcrex'],
																$mCtaDat[$j]['comfecve'],
																$mCtaDat[$j]['valordxx'],
																"0,00",
																number_format($mCtaDat[$j]['comsaldo'],2,',','.'),
																$mCtaDat[$j]['commovfx']));
							}else{
								$pdf->Row(array($mCtaDat[$j]['comidxxx']."-".$mCtaDat[$j]['comcodxx']."-".$mCtaDat[$j]['comcscxx'],
																$mCtaDat[$j]['pucidxxx'],
																$mCtaDat[$j]['comseqxx'],
																$mCtaDat[$j]['regfcrex'],
																$mCtaDat[$j]['comfecve'],
																$mCtaDat[$j]['valordxx'],
																"0,00",
																number_format($mCtaDat[$j]['comsaldo'],2,',','.'),
																$mCtaDat[$j]['commovfx']));
							}
						}
						$nPag = 0;
						$pdf->Ln(5);
						$pdf->SetX(13);
						$pdf->SetFont('verdana','B',8);
						$pdf->Cell(215,5,"VENCIDA:",0,0,'R');
						$pdf->SetFont('verdana','',8);
						$pdf->Cell(40,5,number_format($nTotSal,2,',','.'),0,0,'R');

						$pdf->Ln(5);
						$pdf->SetX(13);
						$pdf->SetFont('verdana','B',8);
						$pdf->Cell(215,5,"POR VENCER:",0,0,'R');
						$pdf->SetFont('verdana','',8);
						$pdf->Cell(40,5,number_format($nTotSad,2,',','.'),0,0,'R');

						$pdf->Ln(5);
						$pdf->SetX(13);
						$pdf->SetFont('verdana','B',8);
						$pdf->Cell(50,5,"FECHA Y HORA DE CONSULTA:",0,0,'L');
						$pdf->SetFont('verdana','',8);
						$pdf->Cell(205,5,date('Y-m-d').' - '.date('H:i:s'),0,0,'L');

						$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";

						//Resibos Provisionales
						if (mysql_num_rows($xTotPro1) > 0) {
						$pdf->Ln(20);
						$pdf->SetX(94);
						$pdf->SetFont('verdana','B',13);
						$pdf->Cell(100,8,"RECIBOS EN ESTADO PROVISIONAL",1,0,'C');
						$pdf->Ln(15);
						$pdf->SetX(13);
						$pdf->SetFont('verdana','B',8);
						$pdf->Cell(25,5,"DEBITOS:",0,0,'L');
						$pdf->SetFont('verdana','',8);
						$pdf->Cell(60,5,number_format($nTDebitos2,2,',','.'),0,0,'L');
						$pdf->SetFont('verdana','B',8);
						$pdf->Cell(25,5,"CREDITOS:",0,0,'L');
						$pdf->SetFont('verdana','',8);
						$pdf->Cell(60,5,number_format($nTCreditos2,2,',','.'),0,0,'L');
						$pdf->SetFont('verdana','B',8);
						$pdf->Cell(25,5,"SALDO FINAL:",0,0,'L');
						$pdf->SetFont('verdana','',8);
						$pdf->Cell(60,5,number_format(($nTDebitos2 + $nTCreditos2),2,',','.'),0,0,'L');

						$pdf->Ln(7);
						$pdf->SetFont('verdana','B',7);
						if($cTerId == ""){
							$pdf->SetWidths(array('20','45','28','18','20','20','20','24','10','30','20'));
							$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C'));
							$pdf->SetX(13);
							$pdf->Row(array("Nit",
									"Tercero",
									"Documento",
									"Cuenta",
									"Vencimiento",
									"Fecha Documento",
									"Fecha Vencimiento",
									"Nro. de Dias",
									"Valor Mora",
									"Valor",
									"Movimiento"));
										$pdf->SetFont('verdana','',7);
										$pdf->SetAligns(array('C','L','L','C','C','C','C','L','C','R','C'));
									}else{
										$pdf->SetWidths(array('40','25','20','30','30','30','30','30','20'));
										$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
										$pdf->SetX(13);
										$pdf->Row(array("Documento",
																		"Cuenta",
																		"Vencimiento",
																		"Fecha Documento",
																		"Fecha Vencimiento",
																		"Nro. de Dias",
																		"Valor Mora",
																		"Valor",
																		"Movimiento"));
										$pdf->SetFont('verdana','',7);
										$pdf->SetAligns(array('L','C','C','C','C','L','C','R','C'));
									}

									$nTotSal = 0;
									$nTotSad = 0;
									$nPag = 0;

									for($j=0;$j<count($mTotPro1);$j++){
										$nPag = 1;
										if($mTotPro1[$j]['commovxx'] == 'D'){
											$mTotPro1[$j]['commovfx'] = 'DEBITO';
										}else{
											$mTotPro1[$j]['commovfx'] = 'CREDITO';
										}
										$dHoy = date('Y-m-d');
										$dFecHoy = str_replace("-","",$dHoy);
										$dFecVen = str_replace("-","",$mTotPro1[$j]['comfecve']);

										$dateHoy = mktime(0,0,0,substr($dFecHoy,4,2), substr($dFecHoy,6,2), substr($dFecHoy,0,4));
										$dateVen = mktime(0,0,0,substr($dFecVen,4,2), substr($dFecVen,6,2), substr($dFecVen,0,4));
										$valor= round(($dateHoy  - $dateVen) / (60 * 60 * 24));
										if($mTotPro1[$j]['comfecve'] < $dHoy){
											$mTotPro1[$j]['valordxx'] =  $valor." VENCIDOS";
											//$nTotSal += $mCtaDat[$j]['comsaldo'];
										}else{
											$mTotPro1[$j]['valordxx'] = $valor." POR VENCER";
											//$nTotSad += $mCtaDat[$j]['comsaldo'];
										}

									$nTotPro +=($mTotPro1[$j]['commovxx']=="D") ? number_format($mTotPro1[$j]['comvlrxx'],2,',','.'):number_format(($mTotPro1[$j]['comvlrxx']*-1),2,',','.');

										$pdf->SetX(13);
										$vCommo=($mTotPro1[$j]['commovxx']=="D") ? number_format($mTotPro1[$j]['comvlrxx'],2,',','.'):number_format(($mTotPro1[$j]['comvlrxx']*-1),2,',','.');
										if($cTerId == ""){
											$pdf->Row(array($mTotPro1[$j]['teridxxx'],
																			$mTotPro1[$j]['clinomxx'],
																			$mTotPro1[$j]['comidxxx']."-".$mTotPro1[$j]['comcodxx']."-".$mTotPro1[$j]['comcscxx'],
																			$mTotPro1[$j]['pucidxxx'],
																			$mTotPro1[$j]['comseqxx'],
																			$mTotPro1[$j]['regfcrex'],
																			$mTotPro1[$j]['comfecve'],
																			$mTotPro1[$j]['valordxx'],
																			"0,00",
																			$vCommo,
																			$mTotPro1[$j]['commovfx']));
										}else{
											$pdf->Row(array($mTotPro1[$j]['comidxxx']."-".$mTotPro1[$j]['comcodxx']."-".$mTotPro1[$j]['comcscxx'],
																			$mTotPro1[$j]['pucidxxx'],
																			$mTotPro1[$j]['comseqxx'],
																			$mTotPro1[$j]['regfcrex'],
																			$mTotPro1[$j]['comfecve'],
																			$mTotPro1[$j]['valordxx'],
																			"0,00",
																			$vCommo,
																			$mTotPro1[$j]['commovfx']));
										}
									}

									$nPag = 0;
									$pdf->Ln(5);
									$pdf->SetX(13);
									$pdf->SetFont('verdana','B',8);
									$pdf->Cell(215,5,"TOTAL RECIBOS PROVISIONALES SIN ASIGNAR:",0,0,'R');
									$pdf->SetFont('verdana','',8);
									$pdf->Cell(40,5,number_format($nTotalSinAsignar,2,',','.'),0,0,'R');

									$pdf->Ln(5);
									$pdf->SetX(13);
									$pdf->SetFont('verdana','B',8);
									$pdf->Cell(215,5,"TOTAL RECIBOS PROVISIONALES:",0,0,'R');
									$pdf->SetFont('verdana','',8);
									$pdf->Cell(40,5,number_format(abs($nTotPro),2,',','.'),0,0,'R');

									$pdf->Ln(5);
									$pdf->SetX(13);
									$pdf->SetFont('verdana','B',8);
									$pdf->Cell(215,5,"TOTAL CARTERA:",0,0,'R');
									$pdf->SetFont('verdana','',8);
									$pdf->Cell(40,5,number_format(($nTDebitos + $nTCreditos+ $nTotPro),2,',','.'),0,0,'R');

									$pdf->Ln(5);
									$pdf->SetX(13);
									$pdf->SetFont('verdana','B',8);
									$pdf->Cell(215,5,"TOTAL CARTERA - RECIBOS PROVISIONALES:",0,0,'R');
									$pdf->SetFont('verdana','',8);
									$pdf->Cell(40,5,number_format(($nTDebitos + $nTCreditos),2,',','.'),0,0,'R');

									$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
									}else{
										$pdf->Ln(15);
										$pdf->SetX(13);
										$pdf->SetFont('verdana','B',8);
										$pdf->Cell(25,5,"No tiene comprobantes en Estado Provisional.",0,0,'L');
									}
						//Fin
						$pdf->Output($cFile);

						if (file_exists($cFile)){
							chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
						} else {
							f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
						}

						echo "<html><script>document.location='$cFile';</script></html>";

					}else{
						f_Mensaje(__FILE__,__LINE__,"No se Generaron registros");
					}

				break;
			}
		}
	}

	if ($_SERVER["SERVER_PORT"] == "") {
		/**
		 * Se ejecuto por el proceso en background
		 * Actualizo el campo de resultado y nombre del archivo
		 */
		$vParBg['pbarespr'] = ($nSwitch == 0) ? "EXITOSO" : "FALLIDO";  //Resultado Proceso
		$vParBg['pbaexcxx'] = $cNomArc;                                 //Nombre Archivos Excel
		$vParBg['pbaerrxx'] = $cMsj;                                    //Errores al ejecutar el Proceso
		$vParBg['regdfinx'] = date('Y-m-d H:i:s');                      //Fecha y Hora Fin Ejecucion Proceso
		$vParBg['pbaidxxx'] = $vArg[0];                                 //id Proceso
	
		#Incluyendo la clase de procesos en background
		$ObjProBg = new cProcesosBackground();
		$mReturnProBg = $ObjProBg->fnFinalizarProcesoBackground($vParBg);
	
		#Imprimiendo resumen de todo ok.
		if ($mReturnProBg[0] == "false") {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
				$cMsj .= $mReturnProBg[$nR] . "\n";
			}
		}
	} // fin del if ($_SERVER["SERVER_PORT"] == "")
?>

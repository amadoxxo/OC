<?php

	/**
	 * Imprime Consecutivos de Comprobantes.
	 * --- Descripcion: Permite Imprimir Consecutivos de Comprobantes.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 */

	ini_set('error_reporting', E_ERROR);
	ini_set("display_errors", "1");

	set_time_limit(0);
	ini_set("memory_limit", "512M");

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
	 * Variables para reemplazar caracteres especiales
	 * @var array
	 */
	$cBuscar = array('"', "'", chr(13), chr(10), chr(27), chr(9));
	$cReempl = array('\"', "\'", " ", " ", " ", " ");

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
			$cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
			$cMsj .= "El parametro Id del Proceso no puede ser vacio.\n";
		}

		if ($vArg[1] == "") {
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
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
			$xProBg = f_MySql("SELECT","",$qProBg,$xConexion01,"");
			if (mysql_num_rows($xProBg) == 0) {
				$xRPB = mysql_fetch_array($xProBg);
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
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
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb = $kDf[3];
	$kUser = $kDf[4];
	$kLicencia = $kDf[5];
	$swidth = $kDf[6];

	$cSystemPath = OC_DOCUMENTROOT;

	if ($_SERVER["SERVER_PORT"] != "") {
		/*** Ejecutar proceso en Background ***/
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
	} // fin if ($_SERVER["SERVER_PORT"] != "")

	if ($_SERVER["SERVER_PORT"] == "") {
		$cTipo     = $_POST['cTipo'];
		$cComId    = $_POST['cComId'];
		$dDesde    = $_POST['dDesde'];
		$dHasta    = $_POST['dHasta'];
		$cComIdc   = $_POST['cComIdc'];
		$cComCodc  = $_POST['cComCodc'];
		$cComCscc  = $_POST['cComCscc'];
		$cOrd      = $_POST['cOrd'];
		$cEjProBg  = $_POST['cEjProBg'];
	} 

	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;

		$strPost = "cTipo~" 		. $cTipo.
							"|cComId~" 		. $cComId.
							"|dDesde~" 		. $dDesde.
							"|dHasta~" 		. $dHasta.
							"|cComIdc~" 	. $cComIdc.
							"|cComCodc~" 	. $cComCodc.
							"|cComCscc~" 	. $cComCscc .
							"|cOrd~" 			. $cOrd .
							"|cEjProBg~" 	. $cEjProBg;

		$vParBg['pbadbxxx'] = $cAlfa;                                         	//Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                                  	//Modulo
		$vParBg['pbatinxx'] = "CSCCOMPROBANTES";                                //Tipo Interface
		$vParBg['pbatinde'] = "REPORTE CONSECUTIVO DE COMPROBANTES";            //Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                                             	//Sucursal
		$vParBg['doiidxxx'] = "";                                             	//Do
		$vParBg['doisfidx'] = "";                                             	//Sufijo
		$vParBg['cliidxxx'] = "";                                             	//Nit
		$vParBg['clinomxx'] = "";                                             	//Nombre Importador
		$vParBg['pbapostx'] = $strPost;																					//Parametros para reconstruir Post
		$vParBg['pbatabxx'] = "";                                               //Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];                    	//Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];                        	//cookie
		$vParBg['pbacrexx'] = 0;                                              	//Cantidad Registros
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
				$cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}
			f_Mensaje(__FILE__, __LINE__, $cMsj."Verifique.");
		}
	}

	if ($cEjePro == 0) {
		if ($nSwitch == 0) {
			//Proceso
			$cADesde  = substr($dDesde,0,4);
			$cAHasta  = substr($dHasta,0,4);

			$cComIdc  = trim($cComIdc);
			$cComCodc = trim($cComCodc);
			$cComCscc = trim($cComCscc);
			$vComprobantes = explode("|", $cComprobantes);

			$cNomCom = "";
			$cComSel = "";
			for ($j=0;$j<count($vComprobantes);$j++) {
				if ($vComprobantes[$j] != "") {
					$cComSel .= "\"{$vComprobantes[$j]}\",";
					$cNomCom .= str_replace("~", "-", $vComprobantes[$j]).", ";
				}
			}
			$cComSel = substr($cComSel,0,-1);
			$cNomCom = substr($cNomCom,0,-2);

			//f_Mensaje(__FILE__,__LINE__," tipo $cTipo Cta $cTipoCta Nit $cTerId");
			//f_Mensaje(__FILE__,__LINE__,  $cComIdc."-".$cComCodc."-".$cComCscc);
			if ($cAlfa == 'SIACOSIA' || $cAlfa == 'DESIACOSIP' || $cAlfa == 'TESIACOSIP') {
				//BUSCO LOS COMPROBANTES DE REEMBOLSO DE CAJA MENOR
				$qFpar117  = "SELECT comidxxx, comcodxx ";
				$qFpar117 .= "FROM $cAlfa.fpar0117 ";
				$qFpar117 .= "WHERE ";
				$qFpar117 .= "comtipxx  = \"RCM\" AND ";
				$qFpar117 .= "regestxx = \"ACTIVO\"";
				$xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qFpar117." ~ ".mysql_num_rows($xFpar117));
				$mRCM = array();
				while ($xRF117 = mysql_fetch_array($xFpar117)) {
					$mRCM[count($mRCM)] = "{$xRF117['comidxxx']}~{$xRF117['comcodxx']}";
				}
			}

			$strTempTables = "";

			#Tabla temporal para el detalle de las facturas de ese periodo
			if ($cComId == "" || $cComId == "F") {
				$mDetFac = array(); //Array con nombre de las tablas temporales para Facturas
				for ($i=$cADesde;$i<=$cAHasta;$i++) {
					$cFcoc = "fcod".$i;
					$cTabFac = fnCadenaAleatoria();
					$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";
					$xNewTab = mysql_query($qNewTab,$xConexion01);

					$qFcod  = "SELECT * ";
					$qFcod .= "FROM $cAlfa.fcod$i ";
					$qFcod .= "WHERE ";
					$qFcod .= "$cAlfa.fcod$i.comidxxx = \"F\" AND ";
					$qFcod .= "$cAlfa.fcod$i.regfcrex BETWEEN \"$dDesde\" AND \"$dHasta\" ";

					$qInsert = "INSERT INTO $cAlfa.$cTabFac $qFcod";
					$xInsert = mysql_query($qInsert,$xConexion01);
					$mDetFac[$i] = $cTabFac;
					$strTempTables = $strTempTables == "" ? $cTabFac : "~".$cTabFac;
				}
			}
			#Fin Tabla temporal para el detalle de las facturas de ese periodo

			#Tabla temporal para documentos cruce de ese periodo
			if ($cComIdc != "" && $cComCodc != "" && $cComCscc != "") {
				$cDocCru = ""; //Tablas temporales para Facturas
				$j = 0;
				for ($i=$cADesde;$i<=$cAHasta;$i++) {
					if ($j == 0) {
						$cFcoc = "fcod".$i;
						$cTabFac = fnCadenaAleatoria();
						$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";
						$xNewTab = mysql_query($qNewTab,$xConexion01);
						$j++;
					}

					$qFcod  = "SELECT * ";
					$qFcod .= "FROM $cAlfa.fcod$i ";
					$qFcod .= "WHERE ";
					$qFcod .= "$cAlfa.fcod$i.comidcxx = \"$cComIdc\" AND ";
					$qFcod .= "$cAlfa.fcod$i.comcodcx = \"$cComCodc\" AND ";
					$qFcod .= "$cAlfa.fcod$i.comcsccx = \"$cComCscc\" AND ";
					$qFcod .= "$cAlfa.fcod$i.regfcrex BETWEEN \"$dDesde\" AND \"$dHasta\" ";

					$qInsert = "INSERT INTO $cAlfa.$cTabFac $qFcod";
					$xInsert = mysql_query($qInsert,$xConexion01);
					$cDocCru = $cTabFac;
					$strTempTables = $strTempTables == "" ? $cTabFac : "~".$cTabFac;
				}
			}
			#Tabla temporal para documentos cruce de ese periodo

			$mMeses = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
			$cMes = $mMeses[date('m') - 1];

			$iA = 0;
			$mComId  = array();
			$mCocDat = array();

			for ($i=$cADesde;$i<=$cAHasta;$i++) {

				$qCocDat  = "SELECT ";
				$qCocDat .= "$cAlfa.fcoc$i.comidxxx, ";
				$qCocDat .= "$cAlfa.fcoc$i.comcodxx, ";
				$qCocDat .= "$cAlfa.fcoc$i.comcscxx, ";
				$qCocDat .= "$cAlfa.fcoc$i.comcsc2x, ";
				$qCocDat .= "$cAlfa.fcoc$i.comcsc3x, ";
				$qCocDat .= "$cAlfa.fcoc$i.comfecxx, ";
				$qCocDat .= "$cAlfa.fcoc$i.regfcrex, ";
				$qCocDat .= "$cAlfa.fcoc$i.regfmodx, ";
				$qCocDat .= "$cAlfa.fcoc$i.comvlrxx, ";
				$qCocDat .= "$cAlfa.fcoc$i.regestxx, ";
				$qCocDat .= "$cAlfa.fcoc$i.comipxxx, ";
				$qCocDat .= "$cAlfa.fcoc$i.comvlr01, ";
				$qCocDat .= "$cAlfa.fcoc$i.comvlr02, ";
				$qCocDat .= "$cAlfa.fcoc$i.comifxxx, ";
				$qCocDat .= "$cAlfa.fcoc$i.comivaxx, ";
				$qCocDat .= "$cAlfa.fcoc$i.comrftex, ";
				$qCocDat .= "$cAlfa.fcoc$i.comrivax, ";
				$qCocDat .= "$cAlfa.fcoc$i.comricax, ";
				$qCocDat .= "$cAlfa.fcoc$i.teridxxx, ";
				$qCocDat .= "$cAlfa.fcoc$i.comobs2x, ";
				$qCocDat .= "$cAlfa.fcoc$i.regusrxx  ";
				$qCocDat .= "FROM $cAlfa.fcoc$i ";
				$qCocDat .= "WHERE ";

				if ($cComId == "F" || $cComId == "") {
					$qCocDat .= "$cAlfa.fcoc$i.regestxx NOT IN (\"PROVISIONAL\") AND  ";
				}
				if ($cComId != ""  &&  $cComSel == "") {
					$qCocDat .= "$cAlfa.fcoc$i.comidxxx = \"$cComId\" AND ";
				}
				if ($cComSel != "") {
					$qCocDat .= "CONCAT(comidxxx,\"~\",comcodxx) IN ($cComSel) AND ";
				}
				if ($cComIdc != "" && $cComCodc != "" && $cComCscc != "") {
					$qCocDat .= "CONCAT($cAlfa.fcoc$i.comidxxx,\"-\",$cAlfa.fcoc$i.comcodxx,\"-\",$cAlfa.fcoc$i.comcscxx) IN (SELECT CONCAT($cAlfa.$cDocCru.comidxxx,\"-\",$cAlfa.$cDocCru.comcodxx,\"-\",$cAlfa.$cDocCru.comcscxx) FROM $cAlfa.$cDocCru) AND ";
				}
				$qCocDat .= "$cAlfa.fcoc$i.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" ";

				if ($cOrd == 'comidxxx') {
					$qCocDat .= "ORDER BY $cAlfa.fcoc$i.comidxxx ASC, ABS($cAlfa.fcoc$i.comcodxx) ASC, ABS($cAlfa.fcoc$i.comcscxx) ASC ";
				} elseif ($cOrd == 'regestxx') {
					$qCocDat .= "ORDER BY $cAlfa.fcoc$i.comidxxx ASC, ABS($cAlfa.fcoc$i.comcodxx), $cAlfa.fcoc$i.$cOrd ASC ";
				} else {
					$qCocDat .= "ORDER BY $cAlfa.fcoc$i.comidxxx ASC, ABS($cAlfa.fcoc$i.comcodxx), ABS($cAlfa.fcoc$i.$cOrd) ASC ";
				}

				$xCocDat  = mysql_query($qCocDat,$xConexion01);
				// f_Mensaje(__FILE__,__LINE__,$qCocDat."~".mysql_num_rows($xCocDat));
				while ($xRCD = mysql_fetch_array($xCocDat)) {
					if ($xRCD['comidxxx'] == "F" && substr($xRCD['comcscxx'],0,1) == "P" && $xRCD['regestxx'] == "INACTIVO") {
					} else {
						#Busco el nombre del cliente
							$qCliNom  = "SELECT ";
							$qCliNom .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
							$qCliNom .= "FROM $cAlfa.SIAI0150 ";
							$qCliNom .= "WHERE ";
							$qCliNom .= "CLIIDXXX = \"{$xRCD['teridxxx']}\" LIMIT 0,1";
							$xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
							if (mysql_num_rows($xCliNom) > 0) {
								$xRCN = mysql_fetch_array($xCliNom);
								$xRCD['CLINOMXX'] = $xRCN['CLINOMXX'];
							} else {
								$xRCD['CLINOMXX'] = "CLIENTE SIN NOMBRE";
							}
						#Fin Busco el nombre del cliente

						#Busco el nombre del usuraio
							$qUsrNom  = "SELECT ";
							$qUsrNom .= "$cAlfa.SIAI0003.USRNOMXX AS USRNOMXX ";
							$qUsrNom .= "FROM $cAlfa.SIAI0003 ";
							$qUsrNom .= "WHERE ";
							$qUsrNom .= "USRIDXXX = \"{$xRCD['regusrxx']}\" LIMIT 0,1";
							$xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
							if (mysql_num_rows($xUsrNom) > 0) {
								$xRCN = mysql_fetch_array($xUsrNom);
								$xRCD['USRNOMXX'] = $xRCN['USRNOMXX'];
							} else {
								$xRCD['USRNOMXX'] = "USUARIO SIN NOMBRE";
							}
						#Fin Busco el nombre del cliente

						$xRCD['colorxxx'] = "#000000";
						if ($xRCD['comidxxx'] == 'F') {
							$cPerAno = substr($xRCD['regfcrex'],0,4);
							$cTabFac = $mDetFac[$cPerAno];

							$qCodDet  = "SELECT ";
							$qCodDet .= "$cAlfa.$cTabFac.commovxx, ";
							$qCodDet .= "SUM(IF($cAlfa.$cTabFac.commovxx = 'D',$cAlfa.$cTabFac.comvlrxx,0)) AS comvrlde, ";
							$qCodDet .= "SUM(IF($cAlfa.$cTabFac.commovxx = 'C',$cAlfa.$cTabFac.comvlrxx,0)) AS comvrldc  ";
							$qCodDet .= "FROM $cAlfa.$cTabFac ";
							$qCodDet .= "WHERE ";
							$qCodDet .= "$cAlfa.$cTabFac.comidxxx = \"{$xRCD['comidxxx']}\" AND ";
							$qCodDet .= "$cAlfa.$cTabFac.comcodxx = \"{$xRCD['comcodxx']}\" AND ";
							$qCodDet .= "$cAlfa.$cTabFac.comcscxx = \"{$xRCD['comcscxx']}\" ";
							$qCodDet .= "GROUP BY $cAlfa.$cTabFac.comidxxx, $cAlfa.$cTabFac.comcodxx, $cAlfa.$cTabFac.comcscxx";
							$xCodDet  = mysql_query($qCodDet,$xConexion01);

							if (mysql_num_rows($xCodDet) > 0) {
								$xCDT = mysql_fetch_array($xCodDet);
								$xRCD['comvlrxx'] = $xCDT['comvrlde'];
								if ($xCDT['comvrlde'] != $xCDT['comvrldc']) {
									$xRCD['colorxxx'] = "#FF0000";
								}
							}
						}

						if ($cAlfa == 'SIACOSIA' || $cAlfa == 'DESIACOSIP' || $cAlfa == 'TESIACOSIP') {

							if (in_array("{$xRCD['comidxxx']}~{$xRCD['comcodxx']}", $mRCM) == true) {
								$cPerAno = substr($xRCD['regfcrex'],0,4);
								$cTabFac = $mDetFac[$cPerAno];

								$qCodDet  = "SELECT ";
								$qCodDet .= "$cAlfa.fcod$i.commovxx, ";
								$qCodDet .= "SUM(IF($cAlfa.fcod$i.commovxx = \"D\",$cAlfa.fcod$i.comvlrxx,0)) AS comvrlde ";
								$qCodDet .= "FROM $cAlfa.fcod$i ";
								$qCodDet .= "WHERE ";
								$qCodDet .= "$cAlfa.fcod$i.comidxxx = \"{$xRCD['comidxxx']}\" AND ";
								$qCodDet .= "$cAlfa.fcod$i.comcodxx = \"{$xRCD['comcodxx']}\" AND ";
								$qCodDet .= "$cAlfa.fcod$i.comcscxx = \"{$xRCD['comcscxx']}\" ";
								$qCodDet .= "GROUP BY $cAlfa.fcod$i.comidxxx, $cAlfa.fcod$i.comcodxx, $cAlfa.fcod$i.comcscxx";
								$xCodDet  = mysql_query($qCodDet,$xConexion01);

								if (mysql_num_rows($xCodDet) > 0) {
									$xCDT = mysql_fetch_array($xCodDet);
									$xRCD['comvlrxx'] = $xCDT['comvrlde'];
								}
							}
						}
						//Buscando Tipo Consecutivo (MANUAL - AUTOMATICO)
						$xRCD['tipocscx'] = "";
						if ($xRCD['comidxxx'] == "F") {
							if (substr_count($xRCD['comobs2x'],"AUTOMATICA~") > 0) {
								$xRCD['tipocscx'] = "AUTOMATICA";
							}
							if (substr_count($xRCD['comobs2x'],"MANUAL~") > 0) {
								$xRCD['tipocscx'] = "MANUAL";
							}
						}
						$nInd_mCocDat = count($mCodDat);
						$mCodDat[$nInd_mCocDat] = $xRCD;
					}
				}
			}
			$nNumReg = count($mCodDat);
		//Fin Proceso
		}// if ($nSwitch == 0)
	}//if ($cEjePro == 0)

	if ($cEjePro == 0) {
		if ($nSwitch == 0) {
			switch ($cTipo) {
				case 1:
					/***** PINTA POR PANTALLA *****/
					?>
					<html>

					<head>
						<title>Consecutivos de Comprobantes </title>
						<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
						<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
						<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
					</head>
					<body>
						<?php if (mysql_num_rows($xCocDat) > 0) { ?>
							<center><br>
								<table width="100%">
									<td class="name">
										<center>
											<h4 style="border-bottom:#6699CC 1px solid;margin-bottom:0px">CONSECUTIVOS DE COMPROBANTES</h4>
										</center>
									</td>
								</table>
							</center>
							<center>
								<fieldset>
									<legend>
										<h4> Resultado consulta</h4>
									</legend>
									<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid black; border-bottom: none;">
										<?php switch ($cAlfa) {
											case "LOGINCAR":
											case "DELOGINCAR":
											case "TELOGINCAR": ?>
												<tr>
													<td class="name">
														<center><img width="156" height="41" style="left: 15px;margin-top: 8px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/Logo_Login_Cargo_Ltda_2.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "ROLDANLO": //ROLDAN
											case "TEROLDANLO": //ROLDAN
											case "DEROLDANLO": //ROLDAN 
											?>
												<tr>
													<td class="name">
														<center><img width="156" height="41" style="left: 15px;margin-top: 6px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoroldan.png"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "ADUANAMO": //ADUANAMO
											case "DEADUANAMO": //ADUANAMO
											case "TEADUANAMO": //ADUANAMO 
											?>
												<tr>
													<td class="name">
														<center><img width="156" height="41" style="left: 15px;margin-top: 6px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logo_aduanamo.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "CASTANOX":
											case "TECASTANOX":
											case "DECASTANOX": ?>
												<tr>
													<td class="name">
														<center><img width="78" height="43" style="left: 15px;margin-top: 4px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logomartcam.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "ALMACAFE": //ALMACAFE
											case "TEALMACAFE": //ALMACAFE
											case "DEALMACAFE": //ALMACAFE 
											?>
												<tr>
													<td class="name">
														<center><img width="90" height="43" style="left: 13px;margin-top: 4px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoalmacafe.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "TEADIMPEXX": // ADIMPEX
											case "DEADIMPEXX": // ADIMPEX
											case "ADIMPEXX": // ADIMPEX 
											?>
												<tr>
													<td class="name">
														<center><img width="158" height="35" style="left: 18px;margin-top: 9px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoadimpex4.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "GRUMALCO": //GRUMALCO
											case "TEGRUMALCO": //GRUMALCO
											case "DEGRUMALCO": //GRUMALCO
											?>
												<tr>
													<td class="name">
														<center><img width="100" height="45" style="left: 15px;margin-top: 6px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logomalco.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "ALADUANA": //ALADUANA
											case "TEALADUANA": //ALADUANA
											case "DEALADUANA": //ALADUANA
											case "DEDESARROL": ?>
												<tr>
													<td class="name">
														<center><img width="100" height="47" style="left: 15px;margin-top: 6px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoaladuana.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "ANDINOSX": //ANDINOSX
											case "TEANDINOSX": //ANDINOSX
											case "DEANDINOSX": //ANDINOSX
											?>
												<tr>
													<td class="name">
														<center><img width="100" height="40" style="left: 15px;margin-top: 2px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoandinos.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php
												} ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "GRUPOALC": //GRUPOALC
											case "TEGRUPOALC": //GRUPOALC
											case "DEGRUPOALC": //GRUPOALC
											?>
												<tr>
													<td class="name">
														<center><img width="100" height="53" style="left: 18px;margin-top: 2px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoalc.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php
												} ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "AAINTERX": //AAINTERX
											case "TEAAINTERX": //AAINTERX
											case "DEAAINTERX": //AAINTERX
											?>
												<tr>
													<td class="name">
														<center><img width="100" height="47" style="left: 15px;margin-top: 4px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logointernacional.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "AALOPEZX":
											case "TEAALOPEZX":
											case "DEAALOPEZX": ?>
												<tr>
													<td class="name">
														<center><img width="100" height="47" style="left: 15px;margin-top: 2px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoaalopez.png"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php
												} ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "ADUAMARX": //ADUAMARX
											case "TEADUAMARX": //ADUAMARX
											case "DEADUAMARX": //ADUAMARX
											?>
												<tr>
													<td class="name">
														<center><img width="49" height="49" style="left: 18px;margin-top: 2px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoaduamar.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php
												} ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "SOLUCION": //SOLUCION
											case "TESOLUCION": //SOLUCION
											case "DESOLUCION": //SOLUCION
											?>
												<tr>
													<td class="name">
														<center><img width="100" height="50" style="left: 18px;margin-top: 2px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logosoluciones.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php
												} ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "FENIXSAS": //FENIXSAS
											case "TEFENIXSAS": //FENIXSAS
											case "DEFENIXSAS": //FENIXSAS
											?>
												<tr>
													<td class="name">
														<center><img width="130" height="50" style="left: 18px;margin-top: 2px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logofenix.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php
												} ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "COLVANXX": //COLVANXX
											case "TECOLVANXX": //COLVANXX
											case "DECOLVANXX": //COLVANXX
											?>
												<tr>
													<td class="name">
														<center><img width="130" height="50" style="left: 18px;margin-top: 2px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logocolvan.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php
												} ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "INTERLAC": //INTERLAC
											case "TEINTERLAC": //INTERLAC
											case "DEINTERLAC": //INTERLAC
											?>
												<tr>
													<td class="name">
														<center><img width="130" height="50" style="left: 18px;margin-top: 2px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logointerlace.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php
												} ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "DHLEXPRE": //DHLEXPRE
											case "TEDHLEXPRE": //DHLEXPRE
											case "DEDHLEXPRE": //DHLEXPRE
											?>
												<tr>
													<td class="name">
														<center><img width="100" height="45" style="left: 15px;margin-top: 6px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logo_dhl_express.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "KARGORUX": //KARGORUX
											case "TEKARGORUX": //KARGORUX
											case "DEKARGORUX": //KARGORUX
											?>
												<tr>
													<td class="name">
														<center><img width="100" height="45" style="left: 15px;margin-top: 12px;margin-left: 10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logokargoru.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: " . " " . $dDesde . " " . "A: " . " " . $dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "ALOGISAS": //LOGISTICA
											case "TEALOGISAS": //LOGISTICA
											case "DEALOGISAS": //LOGISTICA
											?>
												<tr>
													<td class="name">
														<center><img width="140" style="left: 15px;margin-top: 3px;margin-left: 10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logologisticasas.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: " . " " . $dDesde . " " . "A: " . " " . $dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "PROSERCO":
											case "TEPROSERCO":
											case "DEPROSERCO":
												?>
												<tr>
													<td class="name">
														<center><img width="85" style="left: 15px;margin-top: 3px;margin-left: 10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoproserco.png"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: " . " " . $dDesde . " " . "A: " . " " . $dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
                      case "MANATIAL":
                      case "TEMANATIAL":
                      case "DEMANATIAL":
                        ?>
                        <tr>
                          <td class="name">
                            <center><img width="135" height="40" style="left: 15px;margin-top: 8px;margin-left: 10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logomanantial.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
                          </td>
                        </tr>
                        <?php if ($cNomCom != "") { ?>
                          <tr>
                            <td class="name">
                              <center>COMPROBANTE: <?php echo $cNomCom ?>
                            </td>
                          </tr>
                        <?php } ?>
                        <tr>
                          <td style="border-bottom: hidden" class="name">
                            <center><?php echo "DE: " . " " . $dDesde . " " . "A: " . " " . $dHasta ?></center>
                          </td>
                        </tr>
                        <tr>
                          <td style="border-bottom: hidden" class="name">
                            <center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
                          </td>
                        </tr>
                      <?php break;
                      case "DSVSASXX":
                      case "DEDSVSASXX":
                      case "TEDSVSASXX":
                        ?>
                        <tr>
                          <td class="name">
                            <center><img width="135" height="40" style="left: 15px;margin-top: 8px;margin-left: 10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logodsv.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
                          </td>
                        </tr>
                        <?php if ($cNomCom != "") { ?>
                          <tr>
                            <td class="name">
                              <center>COMPROBANTE: <?php echo $cNomCom ?>
                            </td>
                          </tr>
                        <?php } ?>
                        <tr>
                          <td style="border-bottom: hidden" class="name">
                            <center><?php echo "DE: " . " " . $dDesde . " " . "A: " . " " . $dHasta ?></center>
                          </td>
                        </tr>
                        <tr>
                          <td style="border-bottom: hidden" class="name">
                            <center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
                          </td>
                        </tr>
                      <?php break;
											case "MELYAKXX":    //MELYAK
											case "DEMELYAKXX":  //MELYAK
											case "TEMELYAKXX":  //MELYAK
												?>
                        <tr>
                          <td class="name">
                            <center><img width="135" height="40" style="left: 15px;margin-top: 8px;margin-left: 10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logomelyak.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
                          </td>
                        </tr>
                        <?php if ($cNomCom != "") { ?>
                          <tr>
                            <td class="name">
                              <center>COMPROBANTE: <?php echo $cNomCom ?>
                            </td>
                          </tr>
                        <?php } ?>
                        <tr>
                          <td style="border-bottom: hidden" class="name">
                            <center><?php echo "DE: " . " " . $dDesde . " " . "A: " . " " . $dHasta ?></center>
                          </td>
                        </tr>
                        <tr>
                          <td style="border-bottom: hidden" class="name">
                            <center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
                          </td>
                        </tr>
                      <?php break;
                      case "FEDEXEXP":    //FEDEX
                      case "DEFEDEXEXP":  //FEDEX
                      case "TEFEDEXEXP":  //FEDEX
                        ?>
                        <tr>
                          <td class="name">
                            <center><img width="110" height="40" style="left: 13px;margin-top: 8px;margin-left: 10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logofedexexp.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
                          </td>
                        </tr>
                        <?php if ($cNomCom != "") { ?>
                          <tr>
                            <td class="name">
                              <center>COMPROBANTE: <?php echo $cNomCom ?>
                            </td>
                          </tr>
                        <?php } ?>
                        <tr>
                          <td style="border-bottom: hidden" class="name">
                            <center><?php echo "DE: " . " " . $dDesde . " " . "A: " . " " . $dHasta ?></center>
                          </td>
                        </tr>
                        <tr>
                          <td style="border-bottom: hidden" class="name">
                            <center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
                          </td>
                        </tr>
                      <?php break;
											case "EXPORCOM":    //EXPORCOMEX
											case "DEEXPORCOM":  //EXPORCOMEX
											case "TEEXPORCOM":  //EXPORCOMEX
												?>
												<tr>
													<td class="name">
														<center><img width="90" height="40" style="left: 13px;margin-top: 8px;margin-left: 10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoexporcomex.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: " . " " . $dDesde . " " . "A: " . " " . $dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "HAYDEARX":    //HAYDEARX
											case "DEHAYDEARX":  //HAYDEARX
											case "TEHAYDEARX":  //HAYDEARX
												?>
												<tr>
													<td class="name">
														<center><img width="120" height="40" style="left: 13px;margin-top: 8px;margin-left: 10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logohaydear.jpeg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: " . " " . $dDesde . " " . "A: " . " " . $dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "CONNECTA":   //CONNECTA
											case "DECONNECTA": //CONNECTA
											case "TECONNECTA": //CONNECTA
												?>
												<tr>
													<td class="name">
														<center><img width="80" height="40" style="left: 13px;margin-top: 8px;margin-left: 10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoconnecta.jpg"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: " . " " . $dDesde . " " . "A: " . " " . $dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											case "OPENEBCO":   //OPENEBCO
											case "DEOPENEBCO": //OPENEBCO
											case "TEOPENEBCO": //OPENEBCO
												?>
												<tr>
													<td class="name">
														<center><img width="80" height="40" style="left: 13px;margin-top: 8px;margin-left: 10px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/opentecnologia.JPG"><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: " . " " . $dDesde . " " . "A: " . " " . $dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
											<?php break;
											default: ?>
												<tr>
													<td class="name">
														<center><br>REPORTE DE CONSECUTIVOS DE COMPROBANTES
													</td>
												</tr>
												<?php if ($cNomCom != "") { ?>
													<tr>
														<td class="name">
															<center>COMPROBANTE: <?php echo $cNomCom ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center><?php echo "DE: "." ".$dDesde." "."A: "." ".$dHasta ?></center>
													</td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name">
														<center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo $nNumReg ?></center>
													</td>
												</tr>
										<?php break;
										} ?>

									</table>

									<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid black;">
										<tr>
											<td class="name" align="left">FECHA Y HORA DE CONSULTA:<?php echo " ".$cMes." ".date('m')." "."DE ".date('Y')." "."- ".date('H:i:s') ?></td>
										</tr>
									</table>
									<table width="100%" cellpadding="1" cellspacing="1" border="1">
										<tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">
											<td class="name"><center>ID</center></td>
											<td class="name"><center>DOCUMENTO</center></td>
											<td class="name"><center>CONSECUTIVO</center></td>
											<td class="name"><center>TIPO</center></td>
											<td class="name"><center>FECHA COMPROBANTE</center></td>
											<td class="name"><center>FECHA DE CREACION</center></td>
											<td class="name"><center>FECHA DE MODIFICACION</center></td>
											<td class="name"><center>CREADO POR</center></td>
											<td class="name"><center>TERCERO</center></td>
											<td class="name"><center>VALOR</center></td>
											<td class="name"><center>ESTADO</center></td>
										</tr>

										<?php
										//Pinta cxc o cxp si el Movimiento es Debito
										$color = '#D5D5D5';
										$nTotAct = 0;
										$nTotIna = 0;
										$nTotPro = 0;
										$nNumAct = 0;
										$nNumIna = 0;
										$nNumPro = 0;
										$cont = 1;

										for ($j=0;$j<count($mCodDat);$j++) {
											switch ($mCodDat[$j]['regestxx']) {
												case "ACTIVO":
													$nTotAct += $mCodDat[$j]['comvlrxx'];
													$nNumAct++;
													break;
												case "INACTIVO":
													$mCodDat[$j]['comvlrxx'] = 0;
													$nTotIna += $mCodDat[$j]['comvlrxx'];
													$nNumIna++;
													break;
												case "PROVISIONAL":
													$nTotPro += $mCodDat[$j]['comvlrxx'];
													$nNumPro++;
													break;
											}
											$nComVlr = $mCodDat[$j]['comvlrxx'];
										?>
											<tr bgcolor="<?php echo $color ?>" style="color:<?php echo $mCodDat[$j]['colorxxx']; ?>">
												<td class="letra7" align="center"><?php echo $cont ?></td>
												<td class="letra7" align="left"><?php echo ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mCodDat[$j]['comcsc3x'] != '') ? $mCodDat[$j]['comidxxx']."-".$mCodDat[$j]['comcodxx']."-".$mCodDat[$j]['comcscxx']."-".$mCodDat[$j]['comcsc3x'] : $mCodDat[$j]['comidxxx']."-".$mCodDat[$j]['comcodxx']."-".$mCodDat[$j]['comcscxx'] ?></td>
												<td class="letra7" align="center"><?php echo ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mCodDat[$j]['comcsc3x'] != '') ? $mCodDat[$j]['comcsc3x'] : $mCodDat[$j]['comcsc2x'] ?></td>
												<td class="letra7" align="center"><?php echo ($mCodDat[$j]['tipocscx'] != "") ? $mCodDat[$j]['tipocscx'] : "&nbsp;" ?></td>
												<td class="letra7" align="center"><?php echo $mCodDat[$j]['comfecxx'] ?></td>
												<td class="letra7" align="center"><?php echo $mCodDat[$j]['regfcrex'] ?></td>
												<td class="letra7" align="center"><?php echo $mCodDat[$j]['regfmodx'] ?></td>
												<td class="letra7" align="left"><?php echo $mCodDat[$j]['USRNOMXX'] ?></td>
												<td class="letra7" align="left"><?php echo $mCodDat[$j]['CLINOMXX'] ?></td>
												<td class="letra7" align="right"><?php echo number_format($nComVlr,0,',', '.') ?></td>
												<td class="letra7" align="center"><?php echo $mCodDat[$j]['regestxx'] ?></td>
											</tr>
										<?php
											$cont++;
										}
										?>
									</table>
									<table width="100%" cellpadding="1" cellspacing="1" border="1">
										<tr>
											<td class="name" align="left" bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">TOTAL ACTIVO</td>
											<td class="name" align="right"><?php echo number_format($nTotAct,0,',', '.') ?>&nbsp;&nbsp;</td>
											<td class="name" align="left" bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">TOTAL No. ACTIVOS</td>
											<td class="name" align="right"><?php echo $nNumAct ?></td>
										</tr>
										<tr>
											<td class="name" align="left" bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">TOTAL INACTIVO</td>
											<td class="name" align="right"><?php echo number_format($nTotIna,0,',', '.') ?>&nbsp;&nbsp;</td>
											<td class="name" align="left" bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">TOTAL No. INACTIVOS</td>
											<td class="name" align="right"><?php echo $nNumIna ?></td>
										</tr>
										<tr>
											<td class="name" align="left" bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">TOTAL PROVISIONALES</td>
											<td class="name" align="right"><?php echo number_format($nTotPro,0,',', '.') ?>&nbsp;&nbsp;</td>
											<td class="name" align="left" bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">TOTAL No. PROVISIONALES</td>
											<td class="name" align="right"><?php echo $nNumPro ?></td>
										</tr>
									</table>
								</fieldset>
							</center>
						<?php  } else {
							echo "No se Generaron Registros.";
						} ?>
					</body>

					</html>
					<?php
					//Fin pintar por pantalla
					break;
				case 2:
					if (mysql_num_rows($xCocDat) > 0) {

						// PINTA POR EXCEL //
						$cNomFile = "REPORTE_CONSECUTIVOS_COMPROBANTES_".$_COOKIE['kUsrId'].date("YmdHis").".xls";

						if ($_SERVER["SERVER_PORT"] != "") {
							$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;
						} else {
							$cFile = "{$OPENINIT['pathdr']}/opencomex/".$vSysStr['system_download_directory']."/".$cNomFile;
						}
			
						if (file_exists($cFile)) {
							unlink($cFile);
						}
			
						$fOp = fopen($cFile, 'a');
			
						$data = '';

						$data .= '<table width="1104px" cellpadding="1" cellspacing="1" border="1" style="font-family:arial;font-size:12px;border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">';
						$data .= '<tr>';
						$data .= '<td class="name" colspan="10" style="font-size:14px;font-weight:bold"><center>REPORTE DE CONSECUTIVOS DE COMPROBANTES</center></td>';
						$data .= '</tr>';
						if ($cComId != "") {
							$data .= '<tr>';
							$data .= '<td class="name" colspan="10" style="font-size:12px;font-weight:bold"><center>COMPROBANTE: '.$cNomCom.'</center></td>';
							$data .= '</tr>';
						}
						$data .= '<tr>';
						$data .= '<td style="border-bottom: hidden" class="name" colspan="10" style="font-size:12px;font-weight:bold"><center>DE: '.$dDesde.'  A: '.$dHasta.'</center></td>';
						$data .= '</tr>';
						$data .= '<tr>';
						$data .= '<td style="border-bottom: hidden" class="name" colspan="10" style="font-size:12px;font-weight:bold"><center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: '.$nNumReg.'</center></td>';
						$data .= '</tr>';
						$data .= '<tr>';
						$data .= '<td class="name" align="left" colspan="10" style="font-size:12px;font-weight:bold">FECHA Y HORA DE CONSULTA: '.$cMes.' '.date('m').'  DE '.date('Y').'   - '.date('H:i:s').'</td>';
						$data .= '</tr>';
						$data .= '<tr style="font-weight:bold">';
						$data .= '<td class="name" width="70"><center>ID</center></td>';
						$data .= '<td class="name" width="120"><center>DOCUMENTO</center></td>';
						$data .= '<td class="name" width="120"><center>CONSECUTIVO</center></td>';
						$data .= '<td class="name" width="100"><center>TIPO</center></td>';
						$data .= '<td class="name" width="120"><center>FECHA COMPROBANTE</center></td>';
						$data .= '<td class="name" width="120"><center>FECHA DE CREACION</center></td>';
						$data .= '<td class="name" width="120"><center>FECHA DE MODIFICACION</center></td>';
						$data .= '<td class="name" width="300"><center>CREADO POR</center></td>';
						$data .= '<td class="name" width="300"><center>TERCERO</center></td>';
						$data .= '<td class="name" width="120"><center>VALOR</center></td>';
						$data .= '<td class="name" width="120"><center>ESTADO</center></td>';
						$data .= '</tr>';

						// //Pinta cxc o cxp si el Movimiento es Debito
						$nTotAct = 0;
						$nTotIna = 0;
						$nTotPro = 0;
						$nNumAct = 0;
						$nNumIna = 0;
						$nNumPro = 0;
						$cont = 1;
						for ($j=0;$j<count($mCodDat);$j++) {
							switch ($mCodDat[$j]['regestxx']) {
								case "ACTIVO":
									$nTotAct += $mCodDat[$j]['comvlrxx'];
									$nNumAct++;
									break;
								case "INACTIVO":
									$mCodDat[$j]['comvlrxx'] = 0;
									$nTotIna += $mCodDat[$j]['comvlrxx'];
									$nNumIna++;
									break;
								case "PROVISIONAL":
									$nTotPro += $mCodDat[$j]['comvlrxx'];
									$nNumPro++;
									break;
							}
							$nComVlr = $mCodDat[$j]['comvlrxx'];

							$cDocumento = ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mCodDat[$j]['comcsc3x'] != '') ? $mCodDat[$j]['comidxxx']."-".$mCodDat[$j]['comcodxx']."-".$mCodDat[$j]['comcscxx']."-".$mCodDat[$j]['comcsc3x'] : $mCodDat[$j]['comidxxx']."-".$mCodDat[$j]['comcodxx']."-".$mCodDat[$j]['comcscxx'];
							$cConsecutivo = ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mCodDat[$j]['comcsc3x'] != '') ? $mCodDat[$j]['comcsc3x'] : $mCodDat[$j]['comcsc2x'];
							$data .= '<tr>';
							$data .= '<td class="letra7" align="center">'.$cont.'</td>';
							$data .= '<td class="letra7" align="left">'.$cDocumento.'</td>';
							$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$cConsecutivo.'</td>';
							$data .= '<td class="letra7" align="center">'.$mCodDat[$j]['tipocscx'].'</td>';
							$data .= '<td class="letra7" align="center">'.$mCodDat[$j]['comfecxx'].'</td>';
							$data .= '<td class="letra7" align="center">'.$mCodDat[$j]['regfcrex'].'</td>';
							$data .= '<td class="letra7" align="center">'.$mCodDat[$j]['regfmodx'].'</td>';
							$data .= '<td class="letra7" align="left">'.$mCodDat[$j]['USRNOMXX'].'</td>';
							$data .= '<td class="letra7" align="left">'.$mCodDat[$j]['CLINOMXX'].'</td>';
							$data .= '<td class="letra7" align="right">'.number_format($nComVlr,0,',', '').'</td>';
							$data .= '<td class="letra7" align="center">'.$mCodDat[$j]['regestxx'].'</td>';
							$data .= '</tr>';
							$cont++;
						}
						$data .= '<tr>';
						$data .= '<td class="name" align="left" colspan="10"></td>';
						$data .= '</tr>';
						$data .= '<tr>';
						$data .= '<td class="name" align="left" colspan="2" style="font-weight:bold">TOTAL ACTIVO</td>';
						$data .= '<td class="name" align="right" colspan="2">'.number_format($nTotAct,0,',', '').'</td>';
						$data .= '<td class="name" align="left" style="font-weight:bold" colspan="2">TOTAL No. ACTIVOS</td>';
						$data .= '<td class="name" align="right">'.$nNumAct.'</td>';
						$data .= '<td class="name" align="right" colspan="3"></td>';
						$data .= '</tr>';
						$data .= '<tr>';
						$data .= '<td class="name" align="left" colspan="2" style="font-weight:bold">TOTAL INACTIVO</td>';
						$data .= '<td class="name" align="right" colspan="2">'.number_format($nTotIna,0,',', '').'</td>';
						$data .= '<td class="name" align="left" style="font-weight:bold" colspan="2">TOTAL No. INACTIVOS</td>';
						$data .= '<td class="name" align="right">'.$nNumIna.'</td>';
						$data .= '<td class="name" align="right" colspan="3"></td>';
						$data .= '</tr>';
						$data .= '<tr>';
						$data .= '<td class="name" align="left" colspan="2" style="font-weight:bold">TOTAL PROVISIONALES</td>';
						$data .= '<td class="name" align="right" colspan="2">'.number_format($nTotPro,0,',', '').'</td>';
						$data .= '<td class="name" align="left" style="font-weight:bold" colspan="2">TOTAL No. PROVISIONALES</td>';
						$data .= '<td class="name" align="right">'.$nNumPro.'</td>';
						$data .= '<td class="name" align="right" colspan="3"></td>';
						$data .= '</tr>';
						$data .= '</table>';

						fwrite($fOp, $data);
						fclose($fOp);

						if (file_exists($cFile)) {
							chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
							$cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFile);

							if ($_SERVER["SERVER_PORT"] != "") {
								header('Content-Description: File Transfer');
								header('Content-Type: application/octet-stream');
								header('Content-Disposition: attachment; filename='.$cDownLoadFilename);
								header('Content-Transfer-Encoding: binary');
								header('Expires: 0');
								header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
								header('Pragma: public');
								header('Content-Length: '.filesize($cFile));

								ob_clean();
								flush();
								readfile($cFile);
								exit;
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
				case 3:
					/* PINTA POR PDF */
					if (mysql_num_rows($xCocDat) > 0) {

						$cAddr = "";
						if ($cAlfa == "DESARROL" || $cAlfa == "PRUEBASX") {
							$cAddr = "../";
						}

						$cRoot = $_SERVER['DOCUMENT_ROOT'];

						##Switch para incluir fuente y clase pdf segun base de datos ##
						switch ($cAlfa) {
							case "COLMASXX":
								define('FPDF_FONTPATH', "../../../../../fonts/");
								require("../../../../../forms/fpdf.php");
								break;
							default:
								define('FPDF_FONTPATH', $_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
								require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
								break;
						}
						##Fin Switch para incluir fuente y clase pdf segun base de datos ##

						class PDF extends FPDF
						{
							function Header()
							{
								global $cRoot;
								global $cPlesk_Skin_Directory;
								global $cAlfa;
								global $cComId;
								global $cComCod;
								global $dHasta;
								global $nNumReg;
								global $nPag;
								global $cNomCom;
								switch ($cAlfa) {
									case "INTERLOG":
									case "DEINTERLOG":
									case "TEINTERLOG":
										$this->SetXY(6,7);
										$this->Cell(42, 28, '', 1,0,'C');
										$this->Cell(160, 28, '', 1,0,'C');

										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/MaryAire.jpg', 7, 8, 40, 25);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 9);
										$this->Cell(245, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cNomCom != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(6);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(245, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(245, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "LOGINCAR":
									case "DELOGINCAR":
									case "TELOGINCAR":

										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');

										$this->Image($cRoot.$cPlesk_Skin_Directory.'/Logo_Login_Cargo_Ltda_2.jpg', 7, 10, 40, 12);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cNomCom != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "TRLXXXXX":
									case "DETRLXXXXX":
									case "TETRLXXXXX":
										$this->SetXY(6, 7);
										$this->Cell(204.5, 25, '', 1,0,'C');

										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logobma1.jpg', 10, 10, 40, 13);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cNomCom != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "TEADIMPEXX": // ADIMPEX
									case "DEADIMPEXX": // ADIMPEX
									case "ADIMPEXX": // ADIMPEX
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoadimpex5.jpg', 192, 00, 25, 20);
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');

										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoadimpex4.jpg', 10, 14, 36, 8);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "ROLDANLO": //ROLDAN
									case "TEROLDANLO": //ROLDAN
									case "DEROLDANLO": //ROLDAN
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');

										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoroldan.png', 10, 10, 37, 18);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "ADUANAMO": //ADUANAMO
									case "DEADUANAMO": //ADUANAMO
									case "TEADUANAMO": //ADUANAMO
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');

										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logo_aduanamo.jpg', 10, 9, 30, 19);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "CASTANOX":
									case "TECASTANOX":
									case "DECASTANOX":
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');

										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logomartcam.jpg', 7, 8, 37, 21);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "ALMACAFE": //ALMACAFE
									case "TEALMACAFE": //ALMACAFE
									case "DEALMACAFE": //ALMACAFE
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');

										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoalmacafe.jpg', 8, 11, 35, 15);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "GRUMALCO": //GRUMALCO
									case "TEGRUMALCO": //GRUMALCO
									case "DEGRUMALCO": //GRUMALCO
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');

										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logomalco.jpg', 10, 10, 37, 18);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "ALADUANA": //ALADUANA
									case "TEALADUANA": //ALADUANA
									case "DEALADUANA": //ALADUANA
									case "DEDESARROL":
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');

										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoaladuana.jpg', 10, 9, 32, 21);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "ANDINOSX": //ANDINOSX
									case "TEANDINOSX": //ANDINOSX
									case "DEANDINOSX": //ANDINOSX
									case "DEDESARROL":
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');

										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoandinos.jpg', 9, 9, 36, 21);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "GRUPOALC": //GRUPOALC
									case "TEGRUPOALC": //GRUPOALC
									case "DEGRUPOALC": //GRUPOALC
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoalc.jpg', 10, 11, 33, 16);
										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "AAINTERX": //AAINTERX
									case "TEAAINTERX": //AAINTERX
									case "DEAAINTERX": //AAINTERX
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');

										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logointernacional.jpg', 10, 9, 32, 21);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;

									case "AALOPEZX":
									case "TEAALOPEZX":
									case "DEAALOPEZX":
										$this->SetXY(6, 7);
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoaalopez.png', 10, 9, 30);
										$this->Cell(204.5, 24, '', 1,0,'C');

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "ADUAMARX": //ADUAMARX
									case "TEADUAMARX": //ADUAMARX
									case "DEADUAMARX": //ADUAMARX
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoaduamar.jpg', 10, 9.5, 20);
										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "SOLUCION": //SOLUCION
									case "TESOLUCION": //SOLUCION
									case "DESOLUCION": //SOLUCION
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logosoluciones.jpg', 10, 11, 35);
										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "FENIXSAS": //FENIXSAS
									case "TEFENIXSAS": //FENIXSAS
									case "DEFENIXSAS": //FENIXSAS
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logofenix.jpg', 10, 14, 37);
										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "COLVANXX": //COLVANXX
									case "TECOLVANXX": //COLVANXX
									case "DECOLVANXX": //COLVANXX
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logocolvan.jpg', 10, 11, 37);
										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "INTERLAC": //INTERLAC
									case "TEINTERLAC": //INTERLAC
									case "DEINTERLAC": //INTERLAC
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logointerlace.jpg', 10, 10, 37);
										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "DHLEXPRE": //DHLEXPRE
									case "TEDHLEXPRE": //DHLEXPRE
									case "DEDHLEXPRE": //DHLEXPRE
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');

										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logo_dhl_express.jpg', 10, 10, 37, 18);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
									case "KARGORUX": //KARGORUX
									case "TEKARGORUX": //KARGORUX
									case "DEKARGORUX": //KARGORUX
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1, 0, 'C');

										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logokargoru.jpg', 10, 10, 37, 18);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES", 0, 0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: " . $cNomCom, 0, 0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: " . $dDesde . "  A:   " . $dHasta, 0, 0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: " . $nNumReg, 0, 0, 'C');
										$this->Ln(10);
										$this->SetX(6);
                  break;
                  case "ALOGISAS": //LOGISTICA
                  case "TEALOGISAS": //LOGISTICA
                  case "DEALOGISAS": //LOGISTICA
                    $this->SetXY(6, 7);
                    $this->Cell(204.5, 24, '', 1, 0, 'C');

                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 8, 10, 43);

                    $this->SetFont('verdana', '', 12);
                    $this->SetXY(6, 8);
                    $this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES", 0, 0, 'C');
                    $this->Ln(7);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    if ($cComId != "") {
                      $this->Cell(204.5, 6, "COMPROBANTE: " . $cNomCom, 0, 0, 'C');
                    }
                    $this->Ln(5);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(204.5, 6, "DE: " . $dDesde . "  A:   " . $dHasta, 0, 0, 'C');
                    $this->Ln(5);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: " . $nNumReg, 0, 0, 'C');
                    $this->Ln(10);
                    $this->SetX(6);
                  break;
                  case "PROSERCO":
                  case "TEPROSERCO":
                  case "DEPROSERCO":
                    $this->SetXY(6, 7);
                    $this->Cell(204.5, 24, '', 1, 0, 'C');

                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logoproserco.png', 8, 7.5, 40);

                    $this->SetFont('verdana', '', 12);
                    $this->SetXY(6, 8);
                    $this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES", 0, 0, 'C');
                    $this->Ln(7);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    if ($cComId != "") {
                      $this->Cell(204.5, 6, "COMPROBANTE: " . $cNomCom, 0, 0, 'C');
                    }
                    $this->Ln(5);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(204.5, 6, "DE: " . $dDesde . "  A:   " . $dHasta, 0, 0, 'C');
                    $this->Ln(5);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: " . $nNumReg, 0, 0, 'C');
                    $this->Ln(10);
                    $this->SetX(6);
                  break;
                  case "MANATIAL":
                  case "TEMANATIAL":
                  case "DEMANATIAL":
                    $this->SetXY(6, 7);
                    $this->Cell(204.5, 24, '', 1, 0, 'C');

                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logomanantial.jpg', 8, 12, 37, 11);

                    $this->SetFont('verdana', '', 12);
                    $this->SetXY(6, 8);
                    $this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES", 0, 0, 'C');
                    $this->Ln(7);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    if ($cComId != "") {
                      $this->Cell(204.5, 6, "COMPROBANTE: " . $cNomCom, 0, 0, 'C');
                    }
                    $this->Ln(5);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(204.5, 6, "DE: " . $dDesde . "  A:   " . $dHasta, 0, 0, 'C');
                    $this->Ln(5);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: " . $nNumReg, 0, 0, 'C');
                    $this->Ln(10);
                    $this->SetX(6);
                    break;
                  case "DSVSASXX":
                  case "DEDSVSASXX":
                  case "TEDSVSASXX":
                    $this->SetXY(6, 7);
                    $this->Cell(204.5, 24, '', 1, 0, 'C');

                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logodsv.jpg', 8, 14.5, 37, 11);

                    $this->SetFont('verdana', '', 12);
                    $this->SetXY(6, 8);
                    $this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES", 0, 0, 'C');
                    $this->Ln(7);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    if ($cComId != "") {
                      $this->Cell(204.5, 6, "COMPROBANTE: " . $cNomCom, 0, 0, 'C');
                    }
                    $this->Ln(5);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(204.5, 6, "DE: " . $dDesde . "  A:   " . $dHasta, 0, 0, 'C');
                    $this->Ln(5);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: " . $nNumReg, 0, 0, 'C');
                    $this->Ln(10);
                    $this->SetX(6);
                  break;
                  case "MELYAKXX":    //MELYAK
                  case "DEMELYAKXX":  //MELYAK
                  case "TEMELYAKXX":  //MELYAK
                    $this->SetXY(6, 7);
                    $this->Cell(204.5, 24, '', 1, 0, 'C');

                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logomelyak.jpg', 8, 14.5, 37, 11);

                    $this->SetFont('verdana', '', 12);
                    $this->SetXY(6, 8);
                    $this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES", 0, 0, 'C');
                    $this->Ln(7);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    if ($cComId != "") {
                      $this->Cell(204.5, 6, "COMPROBANTE: " . $cNomCom, 0, 0, 'C');
                    }
                    $this->Ln(5);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(204.5, 6, "DE: " . $dDesde . "  A:   " . $dHasta, 0, 0, 'C');
                    $this->Ln(5);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: " . $nNumReg, 0, 0, 'C');
                    $this->Ln(10);
                    $this->SetX(6);
                  break;
                  case "FEDEXEXP":    //FEDEX
                  case "DEFEDEXEXP":  //FEDEX
                  case "TEFEDEXEXP":  //FEDEX
                    $this->SetXY(6, 7);
                    $this->Cell(204.5, 24, '', 1, 0, 'C');
  
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logofedexexp.jpg', 8, 13, 37, 15);
  
                    $this->SetFont('verdana', '', 12);
                    $this->SetXY(6, 8);
                    $this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES", 0, 0, 'C');
                    $this->Ln(7);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    if ($cComId != "") {
                      $this->Cell(204.5, 6, "COMPROBANTE: " . $cNomCom, 0, 0, 'C');
                    }
                    $this->Ln(5);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(204.5, 6, "DE: " . $dDesde . "  A:   " . $dHasta, 0, 0, 'C');
                    $this->Ln(5);
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: " . $nNumReg, 0, 0, 'C');
                    $this->Ln(10);
                    $this->SetX(6);
                  break;
									case "EXPORCOM":    //EXPORCOMEX
									case "DEEXPORCOM":  //EXPORCOMEX
									case "TEEXPORCOM":  //EXPORCOMEX
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1, 0, 'C');
	
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoexporcomex.jpg', 8, 11, 33, 15);
	
										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES", 0, 0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: " . $cNomCom, 0, 0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: " . $dDesde . "  A:   " . $dHasta, 0, 0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: " . $nNumReg, 0, 0, 'C');
										$this->Ln(10);
										$this->SetX(6);
									case "HAYDEARX":    //HAYDEARX
									case "DEHAYDEARX":  //HAYDEARX
									case "TEHAYDEARX":  //HAYDEARX
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1, 0, 'C');
	
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logohaydear.jpeg', 8, 11, 37, 15);
	
										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES", 0, 0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: " . $cNomCom, 0, 0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: " . $dDesde . "  A:   " . $dHasta, 0, 0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: " . $nNumReg, 0, 0, 'C');
										$this->Ln(10);
										$this->SetX(6);
									break;
									case "CONNECTA":    //CONNECTA
									case "DECONNECTA":  //CONNECTA
									case "TECONNECTA":  //CONNECTA
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1, 0, 'C');
	
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoconnecta.jpg', 8, 11, 28, 17);
	
										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES", 0, 0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: " . $cNomCom, 0, 0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: " . $dDesde . "  A:   " . $dHasta, 0, 0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: " . $nNumReg, 0, 0, 'C');
										$this->Ln(10);
										$this->SetX(6);
									break;
									case "OPENEBCO":    //OPENEBCO
									case "DEOPENEBCO":  //OPENEBCO
									case "TEOPENEBCO":  //OPENEBCO
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1, 0, 'C');
	
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/opentecnologia.JPG', 8, 11, 35, 17);
	
										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES", 0, 0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: " . $cNomCom, 0, 0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: " . $dDesde . "  A:   " . $dHasta, 0, 0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: " . $nNumReg, 0, 0, 'C');
										$this->Ln(10);
										$this->SetX(6);
									break;
									default:
										$this->SetXY(6, 7);
										$this->Cell(204.5, 24, '', 1,0,'C');

										$this->SetFont('verdana', '', 12);
										$this->SetXY(6, 8);
										$this->Cell(204.5, 8, "REPORTE DE CONSECUTIVOS DE COMPROBANTES",0,0, 'C');
										$this->Ln(7);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										if ($cComId != "") {
											$this->Cell(204.5, 6, "COMPROBANTE: ".$cNomCom,0,0, 'C');
										}
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204.5, 6, "DE: ".$dDesde."  A:   ".$dHasta,0,0, 'C');
										$this->Ln(5);
										$this->SetFont('verdana', '', 8);
										$this->SetX(6);
										$this->Cell(204, 6, "CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ".$nNumReg,0,0, 'C');
										$this->Ln(10);
										$this->SetX(6);
										break;
								}

								if ($this->PageNo() > 1 && $nPag == 1) {
									$this->SetFont('verdana', 'B', 6);
									$this->SetWidths(array('7', '26', '18', '17', '15', '15', '15', '29', '29', '16', '15'));
									$this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
									$this->SetX(6);
									$this->Row(array(
										"ID",
										"DOCUMENTO",
										"CONSEC.",
										"TIPO",
										"FECHA COMPROB.",
										"FECHA DE CREACION",
										"FECHA DE MODIF.",
										"CREADO POR",
										"TERCERO",
										"VALOR",
										"ESTADO"
									));
									$this->SetFont('verdana', '', 6);
									$this->SetAligns(array('C', 'L', 'C', 'C', 'C', 'C', 'C', 'L', 'L', 'R', 'C'));
									$this->SetX(6);
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

						$pdf = new PDF('P', 'mm', 'Letter');
						$pdf->AddFont('verdana', '', '');
						$pdf->AddFont('verdana', 'B', '');
						$pdf->AliasNbPages();
						$pdf->SetMargins(0,0,0);

						$pdf->AddPage();

						$pdf->SetX(6);
						$pdf->SetFont('verdana', 'B', 7);
						$pdf->Cell(50, 5, "FECHA Y HORA DE CONSULTA:",0,0, 'L');
						$pdf->SetFont('verdana', '', 7);
						$pdf->Cell(205, 5, $cMes." ".substr($fec, 8, 2)." DE ".substr($fec,0,4)." - ".date('H:i:s'),0,0, 'L');

						$pdf->Ln(7);
						$pdf->SetFont('verdana', 'B', 6);
						$pdf->SetWidths(array('7', '26', '18', '17', '15', '15', '15', '29', '29', '16', '15'));
						$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
						$pdf->SetX(6);
						$pdf->Row(array(
							"ID",
							"DOCUMENTO",
							"CONSEC.",
							"TIPO",
							"FECHA COMPROB.",
							"FECHA DE CREACION",
							"FECHA DE MODIF.",
							"CREADO POR",
							"TERCERO",
							"VALOR",
							"ESTADO"
						));
						$pdf->SetFont('verdana', '', 6);
						$pdf->SetAligns(array('C', 'L', 'C', 'C', 'C', 'C', 'C', 'L', 'L', 'R', 'C'));

						$nPag = 0;

						$nTotAct = 0;
						$nTotIna = 0;
						$nTotPro = 0;
						$nNumAct = 0;
						$nNumIna = 0;
						$nNumPro = 0;
						$cont = 1;
						for ($j=0;$j<count($mCodDat);$j++) {
							$nPag = 1;
							switch ($mCodDat[$j]['regestxx']) {
								case "ACTIVO":
									$nTotAct += $mCodDat[$j]['comvlrxx'];
									$nNumAct++;
									break;
								case "INACTIVO":
									$mCodDat[$j]['comvlrxx'] = 0;
									$nTotIna += $mCodDat[$j]['comvlrxx'];
									$nNumIna++;
									break;
								case "PROVISIONAL":
									$nTotPro += $mCodDat[$j]['comvlrxx'];
									$nNumPro++;
									break;
							}
							$nComVlr = $mCodDat[$j]['comvlrxx'];

							$pdf->SetX(6);
							$pdf->Row(array(
								$cont,
								($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mCodDat[$j]['comcsc3x'] != '') ? $mCodDat[$j]['comidxxx']."-".$mCodDat[$j]['comcodxx']."-".$mCodDat[$j]['comcscxx']."-".$mCodDat[$j]['comcsc3x'] : $mCodDat[$j]['comidxxx']."-".$mCodDat[$j]['comcodxx']."-".$mCodDat[$j]['comcscxx'],
								($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mCodDat[$j]['comcsc3x'] != '') ? $mCodDat[$j]['comcsc3x']  : $mCodDat[$j]['comcsc2x'],
								$mCodDat[$j]['tipocscx'],
								$mCodDat[$j]['comfecxx'],
								$mCodDat[$j]['regfcrex'],
								$mCodDat[$j]['regfmodx'],
								$mCodDat[$j]['USRNOMXX'],
								$mCodDat[$j]['CLINOMXX'],
								number_format($nComVlr,0,',', '.'),
								$mCodDat[$j]['regestxx']
							));
							$cont++;
						}
						$nPag = 0;
						$pdf->Ln(5);
						$pdf->SetX(6);
						$pdf->SetFont('verdana', 'B', 7);
						$pdf->Cell(15, 5, "TOTAL ACTIVO:",0,0, 'L');
						$pdf->SetFont('verdana', '', 7);
						$pdf->Cell(40, 5, number_format($nTotAct,0,',', '.'),0,0, 'R');
						$pdf->Cell(91, 5, "",0,0, 'L');
						$pdf->SetFont('verdana', 'B', 7);
						$pdf->Cell(15, 5, "TOTAL No. ACTIVOS:",0,0, 'L');
						$pdf->SetFont('verdana', '', 7);
						$pdf->Cell(43, 5, number_format($nNumAct),0,0, 'R');

						$pdf->Ln(5);
						$pdf->SetX(6);
						$pdf->SetFont('verdana', 'B', 7);
						$pdf->Cell(15, 5, "TOTAL INACTIVO:",0,0, 'L');
						$pdf->SetFont('verdana', '', 7);
						$pdf->Cell(40, 5, number_format($nTotIna,0,',', '.'),0,0, 'R');
						$pdf->Cell(91, 5, "",0,0, 'L');
						$pdf->SetFont('verdana', 'B', 7);
						$pdf->Cell(15, 5, "TOTAL No. INACTIVO:",0,0, 'L');
						$pdf->SetFont('verdana', '', 7);
						$pdf->Cell(43, 5, number_format($nNumIna),0,0, 'R');

						$pdf->Ln(5);
						$pdf->SetX(6);
						$pdf->SetFont('verdana', 'B', 7);
						$pdf->Cell(15, 5, "TOTAL PROVISIONALES:",0,0, 'L');
						$pdf->SetFont('verdana', '', 7);
						$pdf->Cell(40, 5, number_format($nTotPro,0,',', '.'),0,0, 'R');
						$pdf->Cell(91, 5, "",0,0, 'L');
						$pdf->SetFont('verdana', 'B', 7);
						$pdf->Cell(15, 5, "TOTAL No. PROVISIONALES:",0,0, 'L');
						$pdf->SetFont('verdana', '', 7);
						$pdf->Cell(43, 5, number_format($nNumPro),0,0, 'R');

						$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";

						$pdf->Output($cFile);

						if (file_exists($cFile)) {
							chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
						} else {
							f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
						}

						echo "<html><script>document.location='$cFile';</script></html>";
					} else {
						f_Mensaje(__FILE__, __LINE__, "No se Generaron registros"); ?>
						<script languaje="javascript">
							window.close();
						</script>
						<?php }
					break;
			}
		}//if ($nSwitch == 0)
	}//if ($cEjePro == 0)

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
				$cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}
		}
	} 

	function fnCadenaAleatoria($pLength = 8)
	{
		$cCaracteres = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ";
		$nCaracteres = strlen($cCaracteres);
		$cResult = "";
		for ($x = 0; $x < $pLength; $x++) {
			$nIndex = mt_rand(0, $nCaracteres - 1);
			$cResult .= $cCaracteres[$nIndex];
		}
		return $cResult;
	}
?>
<?php
	// ini_set('error_reporting', E_ERROR);
	// ini_set("display_errors", "1");

	set_time_limit(0);
	ini_set("memory_limit", "1024M");
	date_default_timezone_set('America/Bogota');

	/**
	 * Cantidad de Registros para reiniciar conexion.
	 */
	define("_NUMREG_",100);

	include("../../../../../config/config.php");
	include("../../../../../libs/php/utiprobg.php");
	include("../../../../libs/php/utility.php");
	require_once($OPENINIT['pathdr'].'/opencomex/class/spout-2.7.3/src/Spout/Autoloader/autoload.php');

	use Box\Spout\Writer\WriterFactory;
	use Box\Spout\Common\Type;
	use Box\Spout\Writer\Style\Color;
	use Box\Spout\Writer\Style\Border;
	use Box\Spout\Writer\Style\StyleBuilder;
	use Box\Spout\Writer\Style\BorderBuilder;

	/**
	 * Variables de control de errores
	 * @var number
	 */
	$nSwitch = 0;

	/**
	 * Variable para almacenar los mensajes de error
	 * @var string
	 */
	$cMsj = "";

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
			$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "El parametro Id del Proceso no puede ser vacio.\n";
		}

		if ($vArg[1] == "") {
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "El parametro de la Cookie no puede ser vacio.\n";
		}

		if ($nSwitch == 0) {
			$_COOKIE["kDatosFijos"] = $vArg[1];

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
			$xProBg = f_MySql("SELECT","",$qProBg,$xConexion01, "");
			if (mysql_num_rows($xProBg) == 0) {
				$xRPB = mysql_fetch_array($xProBg);
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Proceso en Background [{$vArg[0]}] No Existe o ya fue Procesado.\n".$qProBg;
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
		// include("../../../../../config/config.php");
		// include("../../../../libs/php/utility.php");
		// include("../../../../../libs/php/utiprobg.php");
	}

	/**
	 *  Cookie fija
	 */
	$kDf = explode("~", $_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb   = $kDf[3];
	$kUser      = $kDf[4];
	$kLicencia  = $kDf[5];
	$swidth     = $kDf[6];

	$cSystemPath = OC_DOCUMENTROOT;

	if ($_SERVER["SERVER_PORT"] != "") {
		/*** Ejecutar proceso en Background ***/
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
	} // fin if ($_SERVER["SERVER_PORT"] != "")

	if ($_SERVER["SERVER_PORT"] == "") {
		$dDesde    = $_POST['dDesde'];
		$dHasta    = $_POST['dHasta'];
		$gInterfaz = $_POST['gInterfaz'];
		$gComId    = $_POST['gComId'];
		$gComCod   = $_POST['gComCod'];
		$gUsrId    = $_POST['gUsrId'];
		$cEjProBg  = $_POST['cEjProBg'];
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

	echo "\nprueba";
	echo "\n";
	echo $nSwitch;
	echo "\n";
	echo $_SERVER["SERVER_PORT"];
	echo "\n";
	echo $cEjProBg;
	echo "\n";


	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		echo "\n";
		echo "entra";

		$cEjePro = 1;	
		$strPost  = "dDesde~".   $dDesde."|";
		$strPost .= "dHasta~".   $dHasta."|";
		$strPost .= "gInterfaz~".$gInterfaz."|";
		$strPost .= "gComId~".   $gComId."|";
		$strPost .= "gComCod~".  $gComCod."|";
		$strPost .= "gUsrId~".   $gUsrId."|";
		$strPost .= "cEjProBg~". $cEjProBg;

		$vParBg['pbadbxxx'] = $cAlfa;                        //Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                 //Modulo
		$vParBg['pbatinxx'] = "TRANSMISIESA";                //Tipo Interface
		$vParBg['pbatinde'] = "TRANSMISION A SIESA";         //Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                            //Sucursal
		$vParBg['doiidxxx'] = "";                            //Do
		$vParBg['doisfidx'] = "";                            //Sufijo
		$vParBg['cliidxxx'] = "";                            //Nit
		$vParBg['clinomxx'] = "";                            //Nombre Importador
		$vParBg['pbapostx'] = $strPost;										   //Parametros para reconstruir Post
		$vParBg['pbatabxx'] = "";                            //Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];   //Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];       //cookie
		$vParBg['pbacrexx'] = 0;                             //Cantidad Registros
		$vParBg['pbatxixx'] = 1;                             //Tiempo Ejecucion x Item en Segundos
		$vParBg['pbaopcxx'] = "";                            //Opciones
		$vParBg['regusrxx'] = $kUser;                        //Usuario que Creo Registro

		#Incluyendo la clase de procesos en background
		$ObjProBg = new cProcesosBackground();
		$mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);

		#Imprimiendo resumen de todo ok.
		if ($mReturnProBg[0] == "true") {
			f_Mensaje(__FILE__, __LINE__, "Proceso en Background Agendado con Exito.");
			?>
			<script language = "javascript">
				parent.fmwork.fnRecargar();
			</script>
			<?php

		} else {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}
		}
	} // fin del if ($_SERVER["SERVER_PORT"] != "" && $_POST['cEjProBg'] == "SI" && $nSwitch == 0)

	echo "\n";
	echo $cEjePro;

	/**
	 * Ejecucion del proceso
	 */
	if ($cEjePro == 1) {
		if ($nSwitch == 0) { // cuando lo hace el navegador y por consola
			/**
				* Buscando la informacion de la tabla fpar0115 (Cuentas Contables).
				*
				* @var array
				*/
			$mCuenta  = array();
			$qPucIds  = "SELECT $cAlfa.fpar0115.pucdetxx, $cAlfa.fpar0115.pucterxx , CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
			$qPucIds .= "FROM $cAlfa.fpar0115 ";
			$xPucIds  = mysql_query($qPucIds,$xConexion01);
			// echo "<br>".$qPucIds."~".mysql_num_rows($xPucIds);
			while($xRPU = mysql_fetch_array($xPucIds)) {
				$mCuenta["{$xRPU['pucidxxx']}"]['pucdetxx'] = $xRPU['pucdetxx'];
				$mCuenta["{$xRPU['pucidxxx']}"]['pucterxx'] = $xRPU['pucterxx'];
			}

			/**
				* Buscando las causaciones proveedor empresa.
				*
				* @var array
				*/
			$mCpe  = array();
			$qCpe  = "SELECT ";
			$qCpe .= "CONCAT(comidxxx,\"-\",comcodxx) AS comidxxx ";
			$qCpe .= "FROM $cAlfa.fpar0117 ";
			$qCpe .= "WHERE ";
			$qCpe .= "comidxxx = \"P\" AND ";
			$qCpe .= "comtipxx = \"CPE\" ";
			$xCpe = f_MySql("SELECT","",$qCpe,$xConexion01,"");
			while ($xRDB = mysql_fetch_array($xCpe)) {
				$mCpe[count($mCpe)] = $xRDB['comidxxx'];
			}

			$gAnoIni = date('Y');
			$gAnofin = date('Y');


			$mDataHoja2 = array();
			$mDataHoja3 = array();
			for ($nAno=$gAnoIni; $nAno<=$gAnofin; $nAno++) {
				// Consulta principal
				$qDatMov  = "SELECT ";
				$qDatMov .= "$cAlfa.fcod$nAno.comidxxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comcodxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comcscxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comcsc2x, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comseqxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.teridxxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.terid2xx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comfecxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.pucidxxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comvlrxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.regestxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comvlr01, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comfecve, ";
				$qDatMov .= "$cAlfa.fcod$nAno.commovxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comidcxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comcodcx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comcsccx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.ccoidxxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.sccidxxx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comctocx, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comidc2x, ";
				$qDatMov .= "$cAlfa.fcod$nAno.comobsxx, ";
				$qDatMov .= "$cAlfa.fcoc$nAno.comcscxx AS comcsc1c, ";
				$qDatMov .= "$cAlfa.fcoc$nAno.comcsc2x AS comcsc2c, ";
				$qDatMov .= "$cAlfa.fcoc$nAno.teridxxx AS teridcxx, ";
				$qDatMov .= "$cAlfa.fcoc$nAno.terid2xx AS terid2cx, ";
				$qDatMov .= "$cAlfa.fcoc$nAno.comfecxx AS comfeccx, ";
				$qDatMov .= "$cAlfa.fcoc$nAno.comvlrxx AS comvlrca, ";
				$qDatMov .= "$cAlfa.fcoc$nAno.ccoidxxx AS ccoidcab, ";
				$qDatMov .= "$cAlfa.fcoc$nAno.sccidxxx AS sccidcab, ";
				$qDatMov .= "$cAlfa.fcoc$nAno.comobsxx AS comobsca, ";
				$qDatMov .= "$cAlfa.fcoc$nAno.comtcbxx, ";
				$qDatMov .= "$cAlfa.fcoc$nAno.comncbxx ";
				$qDatMov .= "FROM $cAlfa.fcod$nAno ";
				$qDatMov .= "LEFT JOIN $cAlfa.fcoc$nAno ON $cAlfa.fcod$nAno.comidxxx = $cAlfa.fcoc$nAno.comidxxx AND $cAlfa.fcod$nAno.comcodxx = $cAlfa.fcoc$nAno.comcodxx AND $cAlfa.fcod$nAno.comcscxx = $cAlfa.fcoc$nAno.comcscxx AND $cAlfa.fcod$nAno.comcsc2x = $cAlfa.fcoc$nAno.comcsc2x ";
				$qDatMov .= "WHERE ";
				if($gComId != ""){
					$qDatMov .= "fcod$nAno.comidxxx = \"$gComId\" AND ";
				}
				if($gComCod != ""){
					$qDatMov .= "fcod$nAno.comcodxx = \"$gComCod\" AND ";
				}
				if($gUsrId != ""){
					$qDatMov .= "fcod$nAno.regusrxx = \"$gUsrId\" AND ";
				}
				$qDatMov .= "fcod$nAno.regestxx = \"ACTIVO\" AND ";
				$qDatMov .= "fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" ";
				$qDatMov .= "ORDER BY fcod$nAno.comidxxx,fcod$nAno.comcodxx,fcod$nAno.comcscxx,ABS(fcod$nAno.comcsc2x),ABS(fcod$nAno.comseqxx) ";
				$xDatMov  = f_MySql("SELECT","",$qDatMov,$xConexion01,"");
				// echo $qDatMov."~".mysql_num_rows($xDatMov);


				$cComId   = "";
				$cComCod  = "";
				$cComCsc  = "";
				$cComCsc2 = "";
				while ($xRDM = mysql_fetch_array($xDatMov)) {
					
					// Detecta el cambio de comprobante para asignar la información de cabecera
					if ($cComId != $xRDM['comidxxx'] || $cComCod != $xRDM['comcodxx'] || $cComCsc != $xRDM['comcscxx'] || $cComCsc2 != $xRDM['comcsc2x']) {
						$nInd_mDataHoja2 = count($mDataHoja2);
						$cComId   = $xRDM['comidxxx'];
						$cComCod  = $xRDM['comcodxx'];
						$cComCsc  = $xRDM['comcscxx'];
						$cComCsc2 = $xRDM['comcsc2x'];


						// Array de la hoja 2 - Cabecera del comprobante
						$mDataHoja2[$nInd_mDataHoja2]['fciaxxxx'] = '001';
						$mDataHoja2[$nInd_mDataHoja2]['ccoidcab'] = substr($xRDM['ccoidcab'], 1);
						$mDataHoja2[$nInd_mDataHoja2]['tipodocu'] = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1);
						$mDataHoja2[$nInd_mDataHoja2]['comcscxx'] = $xRDM['comidxxx'] == "F" ? $xRDM['comcsc1c'] : substr($xRDM['comcsc2c'], 2);
						$mDataHoja2[$nInd_mDataHoja2]['comfecxx'] = str_replace("-", "", $xRDM['comfeccx']);
						$mDataHoja2[$nInd_mDataHoja2]['teridxxx'] = $xRDM['comidxxx'] == "R" ? $xRDM['teridcxx'] : $xRDM['terid2cx'];


						// Se obtiene el primer DO del comprobante
						$cDocId  = ""; $cDocSuf = ""; $cSucId = "";
						$mDoiId = explode("|",$vCocDat['comfpxxx']);
						for ($i=0;$i<count($mDoiId);$i++) {
							if($mDoiId[$i] != "") {
								$vDoiId  = explode("~",$mDoiId[$i]);
								if($cDocId == "") {
									$cDocId  = $vDoiId[2];
									$cDocSuf = $vDoiId[3];
									$cSucId  = $vDoiId[15];
								}
							}//if($mDoiId[$i] != ""){
						}//for ($i=0;$i<count($mDoiId);$i++) {
								
						$cObservacion = "";
						if ($xRDM['comidxxx'] == 'F') {
							$cObservacion = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1). " " . $xRDM['comcsc1c'] . " " . $cSucId . $cDocId . $cDocSuf;
						} else {
							if ($xRDM['comobsca'] != "") {
								$cObservacion = $xRDM['comobsca'];
							} else {
								$cObservacion = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1). " " . $xRDM['comcsc1c'] . " " . $cSucId . $cDocId . $cDocSuf;
							}
						}


						$mDataHoja2[$nInd_mDataHoja2]['notasxxx'] = $cObservacion;
					}


					// Array de la hoja 3 = Detalle del movimiento contable
					if ($mCuenta[$xRDM['pucidxxx']]['pucdetxx'] != "P" && $mCuenta[$xRDM['pucidxxx']]['pucdetxx'] != "C" && $xRDM['comctocx'] != "SC") {
						$nInd_mDataHoja3 = count($mDataHoja3);
						$mDataHoja3[$nInd_mDataHoja3]['fciaxxxx'] = '001';
						$mDataHoja3[$nInd_mDataHoja3]['ccoidcab'] = substr($xRDM['ccoidcab'], 1);
						$mDataHoja3[$nInd_mDataHoja3]['tipodocu'] = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1);
						$mDataHoja3[$nInd_mDataHoja3]['comcscxx'] = $xRDM['comidxxx'] == "F" ? $xRDM['comcsc1c'] : substr($xRDM['comcsc2c'], 2);


						if (substr($xRDM['pucidxxx'], 4) == "0000") {
							$mDataHoja3[$nInd_mDataHoja3]['cuentaxx'] = rtrim($xRDM['pucidxxx'], '0000');
						} elseif (substr($xRDM['pucidxxx'], 4) == "00") {
							$mDataHoja3[$nInd_mDataHoja3]['cuentaxx'] = rtrim($xRDM['pucidxxx'], '00');
						} else {
							$mDataHoja3[$nInd_mDataHoja3]['cuentaxx'] = $xRDM['pucidxxx'];
						}


						// Pendiente tercero
						switch ($xRDM['comidxxx']) {
							case 'R':
									if (substr($xRDM['pucidxxx'], 0, 2) == "28") {
										$mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['teridcxx'];
									} elseif(substr($xRDM['pucidxxx'], 0, 4) == "1110") {
										$mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = '';
									} else {
										$mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['terid2cx'];
									}
							break;
							case 'L':
							case 'G':
								if (substr($xRDM['pucidxxx'], 0, 2) == "28") {
									$mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['teridcxx'];
								} elseif(substr($xRDM['pucidxxx'], 0, 4) == "1110") {
									$mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = '';
								} else {
									$mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['teridxxx'];
								}
							break;
							case 'P':
								if (in_array($xRDM['comidxxx'] . "-" . $xRDM['comcodxx'], $mCpe)) {
									$mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['teridcxx'];
								} else {
									if (substr($xRDM['pucidxxx'], 0, 2) == "28") {
										$mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['teridcxx'];
									} else {
										$mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['terid2cx'];
									}
								}
							break;
							case 'F':
								if ($xRDM['comctocx'] == "IP" || $xRDM['comctocx'] == "PCC" ) {
									$mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['teridcxx'];
								} else {
									$mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['terid2cx'];
								}
							break;
							case 'C':
							case 'D':
								$mDataHoja3[$nInd_mDataHoja3]['teridxxx'] = $xRDM['terid2cx'];
							break;
							default:
								// No hace nada
								break;
						}


						$mDataHoja3[$nInd_mDataHoja3]['ccoidxxx'] = substr($xRDM['ccoidxxx'], 1);
						$mDataHoja3[$nInd_mDataHoja3]['idunxxxx'] = '01';


						$mDataHoja3[$nInd_mDataHoja3]['sccidxxx'] = '';
						if (in_array(substr($xRDM['pucidxxx'], 0, 2), array("51","52","53","61"))) {
							$mDataHoja3[$nInd_mDataHoja3]['sccidxxx'] = $xRDM['sccidxxx'];
						}


						$mDataHoja3[$nInd_mDataHoja3]['codfexxx'] = '';
						if (substr($xRDM['pucidxxx'], 0, 4) == "1110" || substr($xRDM['pucidxxx'], 0, 4) == "1245") {
							if ($xRDM['commovxx'] == "C") {
								$mDataHoja3[$nInd_mDataHoja3]['codfexxx'] = "1201";
							} else {
								$mDataHoja3[$nInd_mDataHoja3]['codfexxx'] = "1101";
							}
							
							
						}
						
						if ($xRDM['commovxx'] == "C") {
							$mDataHoja3[$nInd_mDataHoja3]['vlrcrxxx'] = $xRDM['comvlrxx'];
							$mDataHoja3[$nInd_mDataHoja3]['vlrdbxxx'] = '+000000000000000.0000';
						} else {
							$mDataHoja3[$nInd_mDataHoja3]['vlrdbxxx'] = $xRDM['comvlrxx'];
							$mDataHoja3[$nInd_mDataHoja3]['vlrcrxxx'] = '+000000000000000.0000';
						}
						
						if ($mCuenta[$xRDM['pucidxxx']]['pucterxx'] == "R") {
							$mDataHoja3[$nInd_mDataHoja3]['basegrav'] = ($xRCD['comvlr01'] > 0) ? $xRCD['comvlr01'] : '+000000000000000.0000';
						} else {
							$mDataHoja3[$nInd_mDataHoja3]['basegrav'] = '+000000000000000.0000';
						}
						
						$mDataHoja3[$nInd_mDataHoja3]['comtcbxx'] = '';
						$mDataHoja3[$nInd_mDataHoja3]['comncbxx'] = '';
						if ($xRDM['comidxxx'] == 'R' || $xRDM['comidxxx'] == 'L' ||  $xRDM['comidxxx'] == 'G') {
							$mDataHoja3[$nInd_mDataHoja3]['comtcbxx'] = $xRDM['comtcbxx'];
							$mDataHoja3[$nInd_mDataHoja3]['comncbxx'] = $xRDM['comncbxx'];
						}

						$mDataHoja3[$nInd_mDataHoja3]['notasxxx'] = $cObservacion;
					}


					// Array de la hoja 4 - Cuentas por cobrar y saldo a favor de los comprobantes
					if ($mCuenta[$xRDM['pucidxxx']]['pucdetxx'] == "C" || $xRDM['comctocx'] == "SC") {
							$nInd_mDataHoja4 = count($mDataHoja4);
							$mDataHoja4[$nInd_mDataHoja4]['fciaxxxx'] = '001';
							$mDataHoja4[$nInd_mDataHoja4]['ccoidcab'] = substr($xRDM['ccoidcab'], 1);
							$mDataHoja4[$nInd_mDataHoja4]['tipodocu'] = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1);
							$mDataHoja4[$nInd_mDataHoja4]['comcscxx'] = $xRDM['comidxxx'] == "F" ? $xRDM['comcsc1c'] : substr($xRDM['comcsc2c'], 2);

							if (substr($xRDM['pucidxxx'], 4) == "0000") {
								$mDataHoja4[$nInd_mDataHoja4]['cuentaxx'] = rtrim($xRDM['pucidxxx'], '0000');
							} elseif (substr($xRDM['pucidxxx'], 4) == "00") {
								$mDataHoja4[$nInd_mDataHoja4]['cuentaxx'] = rtrim($xRDM['pucidxxx'], '00');
							} else {
								$mDataHoja4[$nInd_mDataHoja4]['cuentaxx'] = $xRDM['pucidxxx'];
							}

							// Nit del tercero
							$mDataHoja4[$nInd_mDataHoja4]['teridxxx'] = "";

							$mDataHoja4[$nInd_mDataHoja4]['ccoidxxx'] = substr($xRDM['ccoidxxx'], 1);
							$mDataHoja4[$nInd_mDataHoja4]['idunxxxx'] = '01';

							$mDataHoja4[$nInd_mDataHoja4]['sccidxxx'] = "";

							if ($xRDM['commovxx'] == "C") {
								$mDataHoja4[$nInd_mDataHoja4]['vlrcrxxx'] = $xRDM['comvlrxx'];
								$mDataHoja4[$nInd_mDataHoja4]['vlrdbxxx'] = '+000000000000000.0000';
							} else {
								$mDataHoja4[$nInd_mDataHoja4]['vlrdbxxx'] = $xRDM['comvlrxx'];
								$mDataHoja4[$nInd_mDataHoja4]['vlrcrxxx'] = '+000000000000000.0000';
							}

							$mDataHoja4[$nInd_mDataHoja4]['sucidxxx'] = '001';
							$mDataHoja4[$nInd_mDataHoja4]['tipdoccr'] = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1);
							$mDataHoja4[$nInd_mDataHoja4]['comcscc2'] = ($xRDM['comidxxx'] == "F") ? $xRDM['comcsc1c'] : substr($xRDM['comcscc2'], 2);
							$mDataHoja4[$nInd_mDataHoja4]['comfecve'] = ($xRDM['comidxxx'] == "R" || $xRDM['comidxxx'] == "N") ? str_replace("-", "", $xRDM['comfecxx']) : str_replace("-", "", $xRDM['comfecve']);
							$mDataHoja4[$nInd_mDataHoja4]['comfecve2'] = ($xRDM['comidxxx'] == "R" || $xRDM['comidxxx'] == "N") ? str_replace("-", "", $xRDM['comfecxx']) : str_replace("-", "", $xRDM['comfecve']);
							
							$mDataHoja4[$nInd_mDataHoja4]['vendedor'] = "";
							
							$mDataHoja4[$nInd_mDataHoja4]['notasxxx'] = $cObservacion;
						}

					// Array de la hoja 5 - Cuentas por pagar
					if ($mCuenta[$xRDM['pucidxxx']]['pucdetxx'] == "C" || $xRDM['comctocx'] == "SC") {
						$nInd_mDataHoja5 = count($mDataHoja5);
						$mDataHoja5[$nInd_mDataHoja5]['fciaxxxx'] = '001';
						$mDataHoja5[$nInd_mDataHoja5]['ccoidcab'] = substr($xRDM['ccoidcab'], 1);
						$mDataHoja5[$nInd_mDataHoja5]['tipodocu'] = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1);
						$mDataHoja5[$nInd_mDataHoja5]['comcscxx'] = $xRDM['comidxxx'] == "F" ? $xRDM['comcsc1c'] : substr($xRDM['comcsc2c'], 2);

						if (substr($xRDM['pucidxxx'], 4) == "0000") {
							$mDataHoja5[$nInd_mDataHoja5]['cuentaxx'] = rtrim($xRDM['pucidxxx'], '0000');
						} elseif (substr($xRDM['pucidxxx'], 4) == "00") {
							$mDataHoja5[$nInd_mDataHoja5]['cuentaxx'] = rtrim($xRDM['pucidxxx'], '00');
						} else {
							$mDataHoja5[$nInd_mDataHoja5]['cuentaxx'] = $xRDM['pucidxxx'];
						}

						// Nit del tercero
						$mDataHoja5[$nInd_mDataHoja5]['teridxxx'] = "";

						$mDataHoja5[$nInd_mDataHoja5]['ccoidxxx'] = substr($xRDM['ccoidxxx'], 1);
						$mDataHoja5[$nInd_mDataHoja5]['idunxxxx'] = '01';

						if ($xRDM['commovxx'] == "C") {
							$mDataHoja5[$nInd_mDataHoja5]['vlrcrxxx'] = $xRDM['comvlrxx'];
							$mDataHoja5[$nInd_mDataHoja5]['vlrdbxxx'] = '+000000000000000.0000';
						} else {
							$mDataHoja5[$nInd_mDataHoja5]['vlrdbxxx'] = $xRDM['comvlrxx'];
							$mDataHoja5[$nInd_mDataHoja5]['vlrcrxxx'] = '+000000000000000.0000';
						}

						$mDataHoja5[$nInd_mDataHoja5]['sucidxxx'] = '001';
						$mDataHoja5[$nInd_mDataHoja5]['tipdoccr'] = $xRDM['comidxxx'] . substr($xRDM['comcodxx'], 1);
						$mDataHoja5[$nInd_mDataHoja5]['comcscc2'] = ($xRDM['comidxxx'] == "F") ? $xRDM['comcsc1c'] : substr($xRDM['comcscc2'], 2);
						$mDataHoja5[$nInd_mDataHoja5]['comfecve'] = ($xRDM['comidxxx'] == "R" || $xRDM['comidxxx'] == "N") ? str_replace("-", "", $xRDM['comfecxx']) : str_replace("-", "", $xRDM['comfecve']);
						$mDataHoja5[$nInd_mDataHoja5]['comfecve2'] = ($xRDM['comidxxx'] == "R" || $xRDM['comidxxx'] == "N") ? str_replace("-", "", $xRDM['comfecxx']) : str_replace("-", "", $xRDM['comfecve']);
						$mDataHoja5[$nInd_mDataHoja5]['comfecve3'] = ($xRDM['comidxxx'] == "R" || $xRDM['comidxxx'] == "N") ? str_replace("-", "", $xRDM['comfecxx']) : str_replace("-", "", $xRDM['comfecve']);
						
						$mDataHoja5[$nInd_mDataHoja5]['notasxxx'] = $cObservacion;
					}
					}
				}


			$writer = WriterFactory::create(Type::XLSX); // for XLSX files
			
			$cRuta = "TRANSMISION_SIESA_".date("YmdHis").".xls";
			$excelFilePath = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cRuta;

			$writer->openToFile($excelFilePath); // write data to a file or to a PHP stream
			$border = (new BorderBuilder())
					->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
					->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
					->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
					->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
					->build();

			$style = (new StyleBuilder())
							->setFontBold()
							->setFontSize(11)
							->setFontColor(Color::BLACK)
							->setShouldWrapText(false)
							->setBorder($border)
							// ->setBackgroundColor(Color::rgb(11,113,193))
							->build();

			$valor_fijo = ['001'];

			// Hoja 1
			$writer->getCurrentSheet()->setName('Inicial');

			$mColumnasInicial  = [
				'F_CIA'
			];

			$writer->addRowWithStyle($mColumnasInicial, $style);
			$writer->addRowWithStyle($valor_fijo, $style);

			// Crear una nueva hoja
			$writer->addNewSheetAndMakeItCurrent();
			// Hoja 2
			$writer->getCurrentSheet()->setName('Documentocontable');

			$mColumnasDocumentocontable = [
				'F_CIA',
				'F350_ID_CO',
				'F350_ID_TIPO_DOCTO',
				'F350_CONSEC_DOCTO',
				'F350_FECHA',
				'F350_ID_TERCERO',
				'F350_NOTAS'
			];

			$writer->addRowWithStyle($mColumnasDocumentocontable, $style);

			for ($i=0; $i<count($mDataHoja2); $i++) {
				$writer->addRowWithStyle($mDataHoja2[$i], $style);
			}

			// Crear una nueva hoja
			$writer->addNewSheetAndMakeItCurrent();
			// Hoja 3
			$writer->getCurrentSheet()->setName('Movimientocontable');

			$mColumnasMovimientocontable = [
				'F_CIA',
				'F350_ID_CO',
				'F350_ID_TIPO_DOCTO',
				'F350_CONSEC_DOCTO',
				'F351_ID_AUXILIAR',
				'F351_ID_TERCERO',
				'F351_ID_CO_MOV',
				'F351_ID_UN',
				'F351_ID_CCOSTO',
				'F351_ID_FE',
				'F351_VALOR_DB',
				'F351_VALOR_CR',
				'F351_BASE_GRAVABLE',
				'F351_DOCTO_BANCO',
				'F351_NRO_DOCTO_BANCO',
				'F351_NOTAS',
			];

			$writer->addRowWithStyle($mColumnasMovimientocontable, $style);
			for ($i=0; $i <count($mDataHoja3); $i++) { 
				$writer->addRowWithStyle($mDataHoja3[$i], $style);
			}


			// Crear una nueva hoja
			$writer->addNewSheetAndMakeItCurrent();
			// Hoja 4
			$writer->getCurrentSheet()->setName('MovimientoCxC');

			$mColumnasMovimientoCxC = [
					'F_CIA',
					'F350_ID_CO',
					'F350_ID_TIPO_DOCTO',
					'F350_CONSEC_DOCTO',
					'F351_ID_AUXILIAR',
					'F351_ID_TERCERO',
					'F351_ID_CO_MOV',
					'F351_ID_UN',
					'F351_ID_CCOSTO',
					'F351_VALOR_DB',
					'F351_VALOR_CR',
					'F353_ID_SUCURSAL',
					'F353_ID_TIPO_DOCTO_CRUCE',
					'F353_CONSEC_DOCTO_CRUCE',
					'F353_FECHA_VCTO',
					'F353_FECHA_DSCTO_PP',
					'F354_TERCERO_VEND',
					'F354_NOTAS'
			];

			$writer->addRowWithStyle($mColumnasMovimientoCxC, $style);
			for ($i=0; $i<count($mDataHoja4) ; $i++) { 
				$writer->addRowWithStyle($mDataHoja4[$i], $style);
			}

			// Crear una nueva hoja
			$writer->addNewSheetAndMakeItCurrent();
			// Hoja 5
			$writer->getCurrentSheet()->setName('MovimientoCxP');

			$mColumnasMovimientoCxP = [
					'F_CIA',
					'F350_ID_CO',
					'F350_ID_TIPO_DOCTO',
					'F350_CONSEC_DOCTO',
					'F351_ID_AUXILIAR',
					'F351_ID_TERCERO',
					'F351_ID_CO_MOV',
					'F351_ID_UN',
					'F351_VALOR_DB',
					'F351_VALOR_CR',
					'F353_ID_SUCURSAL',
					'F353_PREFIJO_CRUCE',
					'F353_CONSEC_DOCTO_CRUCE',
					'F353_FECHA_VCTO',
					'F353_FECHA_DSCTO_PP',
					'F353_FECHA_DOCTO_CRUCE',
					'F354_NOTAS'
			];

			$writer->addRowWithStyle($mColumnasMovimientoCxP, $style);
			for ($i=0; $i<count($mDataHoja5) ; $i++) { 
				$writer->addRowWithStyle($mDataHoja5[$i], $style);
			}

			// Crear una nueva hoja
			$writer->addNewSheetAndMakeItCurrent();
			// Hoja 6
			$writer->getCurrentSheet()->setName('Final');

			$mColumnasFinal = [
					'F_CIA'
			];

			$writer->addRowWithStyle($mColumnasFinal, $style);
			$writer->addRowWithStyle($valor_fijo, $style);
			
			$writer->close();

			$cNomArc = $cRuta;
		}
	}

	if ($_SERVER["SERVER_PORT"] == "") {
		/**
		 * Se ejecuto por el proceso en background
		 * Actualizo el campo de resultado y nombre del archivo
		 */
		$vParBg['pbarespr'] = ($nSwitch == 0) ? "EXITOSO" : "FALLIDO";  //Resultado Proceso
		$vParBg['pbaexcxx'] = $cNomArc;                                 //Nombre Archivos Excel
		$vParBg['pbaerrxx'] = str_replace(" | ", "\n", $cMsj);          //Errores al ejecutar el Proceso
		$vParBg['regdfinx'] = date('Y-m-d H:i:s');                      //Fecha y Hora Fin Ejecucion Proceso
		$vParBg['pbaidxxx'] = $vArg[0];                                 //id Proceso

		#Incluyendo la clase de procesos en background
		$ObjProBg = new cProcesosBackground();
		$mReturnProBg = $ObjProBg->fnFinalizarProcesoBackground($vParBg);

		#Imprimiendo resumen de todo ok.
		if ($mReturnProBg[0] == "false") {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}
		}
	} // fin del if ($_SERVER["SERVER_PORT"] == "")
?>

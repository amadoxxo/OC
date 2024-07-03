<?php
  /**
	 * Imprime Movimiento Por Documento.
	 * --- Descripcion: Permite Imprimir Movimiento Por Documento.
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

  //f_Mensaje(__FILE__,__LINE__," tipo $cTipo Cta $cTipoCta Nit $cTerId");
	//f_Mensaje(__FILE__,__LINE__,"  tipo documento $cTipoDoc");
	
	/**
	 *  Cookie fija
	 */
	$kDf = explode("~", $_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb 	= $kDf[3];
	$kUser 			= $kDf[4];
	$kLicencia 	= $kDf[5];
	$swidth 		= $kDf[6];

	$cSystemPath = OC_DOCUMENTROOT;

	if ($_SERVER["SERVER_PORT"] != "") {
		/*** Ejecutar proceso en Background ***/
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
	} // fin if ($_SERVER["SERVER_PORT"] != "")

	if ($_SERVER["SERVER_PORT"] == "") {
		$cComId  	= $_POST['cComId'];
		$cComCod  = $_POST['cComCod'];
		$cComCsc  = $_POST['cComCsc'];
		$cCtoCod  = $_POST['cCtoCod'];
		$dDesde  	= $_POST['dDesde'];
		$dHasta  	= $_POST['dHasta'];
		$cTipoDoc	= $_POST['cTipoDoc'];
		$cTipo 		= $_POST['cTipo'];
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

  $cDate = date('Y-m-d');
  $fec   = date('Y-m-d');

	if ($dDesde == "" || $dHasta == "") {
		$dDesde = $vSysStr['financiero_ano_instalacion_modulo']."-01-01";
		$dHasta = date('Y-m-d');
	}

  $nAnoIni = substr($dDesde,0,4);
	$nAnoFin = substr($dHasta,0,4);
	
	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;
	
		$strPost = "cComId~" 		. $cComId. 
							"|cComCod~" 	. $cComCod. 
							"|cComCsc~" 	. $cComCsc. 
							"|cCtoCod~" 	. $cCtoCod. 
							"|dDesde~" 		. $dDesde.
							"|dHasta~" 		. $dHasta.
							"|cTipoDoc~" 	. $cTipoDoc.
							"|cTipo~" 		. $cTipo;

		$vParBg['pbadbxxx'] = $cAlfa;                           // Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                    // Modulo
		$vParBg['pbatinxx'] = "MOVIMIENTODOCUMENTOS";      			// Tipo Interface
		$vParBg['pbatinde'] = "REPORTE MOVIMIENTO POR DOCUMENTO";      // Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                               // Sucursal
		$vParBg['doiidxxx'] = "";                               // Do
		$vParBg['doisfidx'] = "";                               // Sufijo
		$vParBg['cliidxxx'] = "";                               // Nit
		$vParBg['clinomxx'] = $xDDE['clinomxx'];                // Nombre Importador
		$vParBg['pbapostx'] = $strPost;													// Parametros para reconstruir Post
		$vParBg['pbatabxx'] = "";                               // Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];      // Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];          // cookie
		$vParBg['pbacrexx'] = 0;                                // Cantidad Registros
		$vParBg['pbatxixx'] = 1;                                // Tiempo Ejecucion x Item en Segundos
		$vParBg['pbaopcxx'] = "";                               // Opciones
		$vParBg['regusrxx'] = $kUser;                           // Usuario que Creo Registro
	
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
	} // fin del if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0)

	if ($cEjePro == 0) {
		if ($nSwitch == 0) {
			
			$mCodDat = array();
			switch ($cTipoDoc) {
				case "FUENTE":
					for($nAno=$nAnoIni;$nAno<=$nAnoFin;$nAno++){
						$qCodDat  = "SELECT DISTINCT ";
						$qCodDat .= "$cAlfa.fcod$nAno.*,";
						$qCodDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX, ";
						$qCodDat .= "IF($cAlfa.A.CLINOMXX != \"\",$cAlfa.A.CLINOMXX,(TRIM(CONCAT($cAlfa.A.CLINOM1X,\" \",$cAlfa.A.CLINOM2X,\" \",$cAlfa.A.CLIAPE1X,\" \",$cAlfa.A.CLIAPE2X)))) AS PRONOMXX ";
						$qCodDat .= "FROM $cAlfa.fcod$nAno ";
						$qCodDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$nAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
						$qCodDat .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.fcod$nAno.terid2xx = $cAlfa.A.CLIIDXXX ";
						$qCodDat .= "WHERE ";
						if($cComId != ""){
							$qCodDat .= "$cAlfa.fcod$nAno.comidxxx = \"$cComId\" AND ";
						}
						if($cComCod != ""){
							$qCodDat .= "$cAlfa.fcod$nAno.comcodxx = \"$cComCod\" AND ";
						}
						if($cComCsc != ""){
							$qCodDat .= "$cAlfa.fcod$nAno.comcscxx = \"$cComCsc\" AND ";
						}
						if($cCtoCod != ""){
							$qCodDat .= "$cAlfa.fcod$nAno.ctoidxxx = \"$cCtoCod\" AND ";
						}
						if($dDesde != "" && $dHasta != ""){
							$qCodDat .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
						}
						$qCodDat .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
						$qCodDat .= "ORDER BY $cAlfa.fcod$nAno.comidxxx,ABS($cAlfa.fcod$nAno.comcodxx),ABS($cAlfa.fcod$nAno.comcscxx),ABS($cAlfa.fcod$nAno.comseqxx) ASC,$cAlfa.fcod$nAno.pucidxxx,$cAlfa.fcod$nAno.regfcrex,$cAlfa.fcod$nAno.reghcrex";

						$xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
						while ($xRCD = mysql_fetch_array($xCodDat)) {
							$mCodDat[count($mCodDat)] = $xRCD;
						}
					}
				break;
				case "CRUCE":
					for($nAno=$nAnoIni;$nAno<=$nAnoFin;$nAno++){
						$qCodDat  = "SELECT DISTINCT ";
						$qCodDat .= "$cAlfa.fcod$nAno.*,";
						$qCodDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX, ";
						$qCodDat .= "IF($cAlfa.A.CLINOMXX != \"\",$cAlfa.A.CLINOMXX,(TRIM(CONCAT($cAlfa.A.CLINOM1X,\" \",$cAlfa.A.CLINOM2X,\" \",$cAlfa.A.CLIAPE1X,\" \",$cAlfa.A.CLIAPE2X)))) AS PRONOMXX ";
						$qCodDat .= "FROM $cAlfa.fcod$nAno ";
						$qCodDat .= "JOIN $cAlfa.fpar0115 ON $cAlfa.fcod$nAno.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND $cAlfa.fpar0115.pucdetxx IN (\"P\",\"C\",\"D\") ";
						$qCodDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$nAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
						$qCodDat .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.fcod$nAno.terid2xx = $cAlfa.A.CLIIDXXX ";
						$qCodDat .= "WHERE ";
						if($cComId != ""){
							$qCodDat .= "($cAlfa.fcod$nAno.comidcxx = \"$cComId\") AND ";
						}
						if($cComCod != ""){
							$qCodDat .= "($cAlfa.fcod$nAno.comcodcx = \"$cComCod\" ) AND ";
						}
						if($cComCsc != ""){
							$qCodDat .= "($cAlfa.fcod$nAno.comcsccx = \"$cComCsc\") AND ";
						}
						if($cCtoCod != ""){
							$qCodDat .= "$cAlfa.fcod$nAno.ctoidxxx = \"$cCtoCod\" AND ";
						}
						$qCodDat .= "$cAlfa.fcod$nAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
						$qCodDat .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
						$qCodDat .= "ORDER BY $cAlfa.fcod$nAno.comidcxx,ABS($cAlfa.fcod$nAno.comcodcx),ABS($cAlfa.fcod$nAno.comcsccx),ABS($cAlfa.fcod$nAno.comseqcx) ASC,$cAlfa.fcod$nAno.pucidxxx,$cAlfa.fcod$nAno.regfcrex,$cAlfa.fcod$nAno.reghcrex";

						$xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qCodDat." ~ ".mysql_num_rows($xCodDat));
						while ($xRCD = mysql_fetch_array($xCodDat)) {
							$mCodDat[count($mCodDat)] = $xRCD;
						}
					}
				break;
				case "DO":

					$qCodDat  = "SELECT ";
					$qCodDat .= "$cAlfa.sys00121.regfcrex ";
					$qCodDat .= "FROM $cAlfa.sys00121 ";
					$qCodDat .= "WHERE ";
					$qCodDat .= "$cAlfa.sys00121.sucidxxx = \"$cSucId\" AND ";
					$qCodDat .= "$cAlfa.sys00121.docidxxx = \"$cDocNro\" AND ";
					$qCodDat .= "$cAlfa.sys00121.docsufxx = \"$cDocSuf\"";
					$xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
					$xRCD = mysql_fetch_array($xCodDat);

					$nAnoAnt = ((substr($xRCD['regfcrex'],0,4) - 1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (substr($xRCD['regfcrex'],0,4) - 1);

					for($nAno=$nAnoAnt;$nAno<=date('Y');$nAno++){
						$qCodDat  = "SELECT DISTINCT ";
						$qCodDat .= "$cAlfa.fcod$nAno.*,";
						$qCodDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX ";
						$qCodDat .= "FROM $cAlfa.fcod$nAno ";
						$qCodDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$nAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
						$qCodDat .= "WHERE ";
						$qCodDat .= "($cAlfa.fcod$nAno.comcsccx = \"$cDocNro\" OR ";
						$qCodDat .= "$cAlfa.fcod$nAno.comcscc2 = \"$cDocNro\" OR ";
						$qCodDat .= "$cAlfa.fcod$nAno.docidxxx = \"$cDocNro\") AND ";
						$qCodDat .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
						$qCodDat .= "ORDER BY $cAlfa.fcod$nAno.comfecxx";
						$xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
						while ($xRCD = mysql_fetch_array($xCodDat)) {
							$qPucIds  = "SELECT ";
							$qPucIds .= "pucdoscc,";
							$qPucIds .= "pucdetxx ";
							$qPucIds .= "FROM $cAlfa.fpar0115 ";
							$qPucIds .= "WHERE ";
							$qPucIds .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$xRCD['pucidxxx']}\" ";
							$xPucIds  = mysql_query($qPucIds,$xConexion01);
							$vPucIds = mysql_fetch_array($xPucIds);

							$qParConC  = "SELECT ";
							$qParConC .= "ctodocxg, ";
							$qParConC .= "ctodocxl ";
							$qParConC .= "FROM $cAlfa.fpar0119 ";
							$qParConC .= "WHERE ";
              $qParConC .= "pucidxxx = \"{$xRCD['pucidxxx']}\" AND ";
							$qParConC .= "ccoidxxx = \"{$xRCD['ctoidxxx']}\" LIMIT 0,1 ";
							$xParConC  = mysql_query($qParConC,$xConexion01);
							$vParConC = mysql_fetch_array($xParConC);

							$nAplica = 0;
							if($xRCD['docidxxx'] != "" &&  $xRCD['sccidxxx'] == $cDocNro){
								$nAplica = 1;
							}else{
								if($xRCD['comidxxx'] == "F"){
									if (($vPucIds['pucdoscc'] == "S" || $vPucIds['pucdetxx'] == "D") && $xRCD['comcsccx'] == $cDocNro && $xRCD['comseqcx'] == $cDocSuf){
										$nAplica = 1;
									}
								}else{
									if(($vParConC['ctodocxg'] = "SI" || $vParConC['ctodocxl'] = "SI") && ($xRCD['comcscc2'] == $cDocNro || $xRCD['docidxxx'] == $cDocNro) ){
										$nAplica = 1;
									}else{
										if(($vPucIds['pucdoscc'] == "S" || $vPucIds['pucdetxx'] == "D") && ($xRCD['comcsccx'] == $cDocNro || $xRCD['comcscc2'] == $cDocNro || $xRCD['docidxxx'] == $cDocNro)){
											$nAplica = 1;
										}
									}
								}
							}
							if ($nAplica == 1) {
								$mCodDat[count($mCodDat)] = $xRCD;
							}
						}
					}
				break;
			}

			switch ($cTipo) {
				// PINTA POR PANTALLA//
				case 1: ?>
					<html>
						<head><title>Reporte de Movimiento de Documentos </title>
						<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
						<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
						<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
						<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
						</head>
						<body>
							<?php // PINTA POR PANTALLA//
							if (count($mCodDat) > 0) { ?>
								<table width="100%">
									<tr>
										<td>
											<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid black;">
												<tr>
													<?php
													switch ($cAlfa) {
														case "ROLDANLO"://ROLDAN
														case "TEROLDANLO"://ROLDAN
														case "DEROLDANLO"://ROLDAN ?>
																<td class="name"><center><img width="156" height="41" style="left: 15px;margin-top: 6px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoroldan.png"></td><?php
														break;
														case "GRUMALCO"://GRUMALCO
														case "TEGRUMALCO"://GRUMALCO
														case "DEGRUMALCO"://GRUMALCO?>
																<td class="name"><center><img width="100" height="41" style="left: 15px;margin-top: 6px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomalco.jpg"></td><?php
														break;
														case "ALADUANA": //ALADUANA
														case "TEALADUANA": //ALADUANA
														case "DEALADUANA": //ALADUANA
														case "DEDESARROL":?>
																<td class="name"><center><img width="100" height="46" style="left: 15px;margin-top: 6px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaladuana.jpg"></td><?php
														break;
														case "ANDINOSX": //ANDINOSX
														case "TEANDINOSX": //ANDINOSX
														case "DEANDINOSX": //ANDINOSX?>
															<td class="name"><center><img width="100" height="46" style="left: 15px;margin-top: 6px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoandinos.jpg"></td><?php
														break;
														case "GRUPOALC": //GRUPOALC
														case "TEGRUPOALC": //GRUPOALC
														case "DEGRUPOALC": //ANDINOSX?>
															<td class="name"><center><img width="100" height="46" style="left: 14px;margin-top: 6px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoalc.jpg"></td><?php
														break;
														case "AAINTERX": //AAINTERX
														case "TEAAINTERX": //AAINTERX
														case "DEAAINTERX": //AAINTERX ?>
															<td class="name"><center><img width="100" height="46" style="left: 15px;margin-top: 6px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointernacional.jpg"></td><?php
														break;
														case "AALOPEZX": //ANDINOSX
														case "TEAALOPEZX": //ANDINOSX
														case "DEAALOPEZX": //ANDINOSX?>
															<td class="name"><center><img width="100" height="46" style="left: 15px;margin-top: 6px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaalopez.png"></td><?php
														break;
														case "ADUAMARX": //ADUAMARX
														case "TEADUAMARX": //ADUAMARX
														case "DEADUAMARX": //ADUAMARX?>
															<td class="name"><center><img width="50" height="50" style="left: 14px;margin-top: 4px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaduamar.jpg"></td><?php
														break;
														case "SOLUCION": //SOLUCION
														case "TESOLUCION": //SOLUCION
														case "DESOLUCION": //SOLUCION?>
															<td class="name"><center><img width="100" height="50" style="left: 14px;margin-top: 4px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logosoluciones.jpg"></td><?php
														break;
														case "FENIXSAS": //FENIXSAS
														case "TEFENIXSAS": //FENIXSAS
														case "DEFENIXSAS": //FENIXSAS?>
															<td class="name"><center><img width="130" height="50" style="left: 14px;margin-top: 4px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logofenix.jpg"></td><?php
														break;
														case "COLVANXX": //COLVANXX
														case "TECOLVANXX": //COLVANXX
														case "DECOLVANXX": //COLVANXX?>
															<td class="name"><center><img width="130" height="50" style="left: 14px;margin-top: 2px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logocolvan.jpg"></td><?php
														break;
														case "INTERLAC": //INTERLAC
														case "TEINTERLAC": //INTERLAC
														case "DEINTERLAC": //INTERLAC?>
															<td class="name"><center><img width="130" height="50" style="left: 14px;margin-top: 2px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointerlace.jpg"></td><?php
														break;
														case "DHLEXPRE": //DHLEXPRE
														case "TEDHLEXPRE": //DHLEXPRE
														case "DEDHLEXPRE": //DHLEXPRE?>
															<td class="name"><center><img width="100" height="41" style="left: 15px;margin-top: 6px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logo_dhl_express.jpg"></td><?php
														break;
														case "KARGORUX": //KARGORUX
														case "TEKARGORUX": //KARGORUX
														case "DEKARGORUX": //KARGORUX?>
															<td class="name"><center><img width="100" height="41" style="left: 15px;margin-top: 7px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logokargoru.jpg"></td><?php
														break;
														case "ALOGISAS": //LOGISTICA
														case "TEALOGISAS": //LOGISTICA
														case "DEALOGISAS": //LOGISTICA?>
															<td class="name"><center><img width="100" height="41" style="left: 15px;margin-top: 7px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logologisticasas.jpg"></td><?php
														break;
														case "PROSERCO": //PROSERCO
														case "TEPROSERCO": //PROSERCO
														case "DEPROSERCO": //PROSERCO?>
															<td class="name"><center><img width="100" height="48" style="left: 15px;margin-top: 4px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoproserco.png"></td><?php
														break;
                            case "MANATIAL": //MANATIAL
                            case "TEMANATIAL": //MANATIAL
                            case "DEMANATIAL": //MANATIAL?>
                              <td class="name"><center><img width="130" height="45" style="left: 15px;margin-top: 4px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logomanantial.jpg"></td><?php
                            break;
                            case "DSVSASXX":   //DSVSAS
                            case "DEDSVSASXX": //DSVSAS
                            case "TEDSVSASXX": //DSVSAS?>
                              <td class="name"><center><img width="130" height="45" style="left: 15px;margin-top: 4px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logodsv.jpg"></td><?php
                            break;
                            case "MELYAKXX":    //MELYAK
                            case "DEMELYAKXX":  //MELYAK
                            case "TEMELYAKXX":  //MELYAK ?>
                              <td class="name"><center><img width="130" height="45" style="left: 15px;margin-top: 4px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logomelyak.jpg"></td><?php	
                            break;
                            case "FEDEXEXP":    //FEDEX
                            case "DEFEDEXEXP":  //FEDEX
                            case "TEFEDEXEXP":  //FEDEX ?>
                              <td class="name"><center><img width="100" height="45" style="left: 15px;margin-top: 4px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logofedexexp.jpg"></td><?php	
                            break;
														case "EXPORCOM":    //EXPORCOMEX
														case "DEEXPORCOM":  //EXPORCOMEX
														case "TEEXPORCOM":  //EXPORCOMEX ?>
															<td class="name"><center><img width="100" height="45" style="left: 15px;margin-top: 4px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoexporcomex.jpg"></td><?php	
														break;
														case "HAYDEARX":    //HAYDEARX
														case "DEHAYDEARX":  //HAYDEARX
														case "TEHAYDEARX":  //HAYDEARX ?>
															<td class="name"><center><img width="120" height="45" style="left: 15px;margin-top: 4px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logohaydear.jpeg"></td><?php	
														break;
														case "CONNECTA":    //HAYDEARX
														case "DECONNECTA":  //HAYDEARX
														case "TECONNECTA":  //HAYDEARX ?>
															<td class="name"><center><img width="85" height="45" style="left: 15px;margin-top: 4px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/logoconnecta.jpg"></td><?php	
														break;
														case "OPENEBCO":    //OPENEBCO
														case "DEOPENEBCO":  //OPENEBCO
														case "TEOPENEBCO":  //OPENEBCO ?>
															<td class="name"><center><img width="85" height="45" style="left: 15px;margin-top: 4px;position: absolute;" src="<?php echo $cPlesk_Skin_Directory ?>/opentecnologia.JPG"></td><?php	
														break;
													}?>
												</tr>
												<tr>
													<td class="name" width="20%"><center><br>REPORTE DE MOVIMIENTO DE DOCUMENTOS</td>
												</tr>
												<tr>
													<td class="name" width="20%"><center>DOCUMENTO <?php echo " ".$cTipoDoc; if($cComCsc != "") { echo " (".$cComCsc.")"; } ?></td>
												</tr>
												<tr>
													<td style="border-bottom: hidden" class="name" width="20%"><center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: <?php echo count($mCodDat) ?></center></td>
												</tr>
											</table>
											<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
												<tr>
													<td class="name" width="16%">FECHA Y HORA DE CONSULTA: <?php echo date('Y-m-d')."-".date('H:i:s')?></td>
												</tr>
											</table>
											<table width="100%" cellpadding="1" cellspacing="1" border="0">
												<tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">
													<?php if ($cTipoDoc == "DO") { ?>
														<td class="name" width="14%"><center>DOC.FUENTE</center></td>
														<td class="name" width="7%"><center>FECHA COMPROBANTE</center></td>
														<td class="name" width="7%"><center>CC</center></td>
														<td class="name" width="7%"><center>SC</center></td>
														<td class="name" width="4%"><center>CUENTA</center></td>
														<td class="name" width="4%"><center>CONCEPTO</center></td>
														<td class="name" width="5%"><center>MOV</center></td>
														<td class="name" width="4%"><center>CLIENTE</center></td>
														<td class="name" width="9%"><center>NOMBRE DEL CLIENTE</center></td>
														<td class="name" width="5%"><center>PROVEEDOR</center></td>
														<td class="name" width="7%"><center>NOMBRE PROVEEDOR</center></td>
														<td class="name" width="18%"><center>DOCUMENTO CRUCE 1</center></td>
														<td class="name" width="18%"><center>DOCUMENTO CRUCE 2</center></td>
														<td class="name" width="18%"><center>SUCURSAL/DO/SUFIJO</center></td>
														<td class="name" width="7%"><center>TIPO DE EJECUCION</center></td>
														<td class="name" width="9%"><center>VALOR DEL COMPROBANTE</center></td>
														<td class="name" width="9%"><center>BASE DE RETENCION</center></td>
														<td class="name" width="9%"><center>BASE DE IVA</center></td>
														<td class="name" width="9%"><center>VALOR EN NIIF</center></td>
													<?php } else { ?>
														<td class="name" width="10%"><center>DOC.FUENTE</center></td>
														<td class="name" width="09%"><center>DOC.CRUCE</center></td>
														<td class="name" width="7%"><center><? echo ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') ? 'CSC TRES' : 'CSC DOS' ?></center></td>
														<td class="name" width="7%"><center>CONCEPTO</center></td>
														<td class="name" width="7%"><center>CUENTA</center></td>
														<td class="name" width="7%"><center>FECHA CRE.</center></td>
														<td class="name" width="7%"><center>FECHA DOC.</center></td>
														<td class="name" width="7%"><center>FECHA VTO</center></td>
														<td class="name" width="7%"><center>NIT</center></td>
														<td class="name" width="<?php echo ($cTipoDoc == "CRUCE") ? "9%" : "18%" ?>"><center>TERCERO1</center></td>
														<td class="name" width="7%"><center>NIT</center></td>
														<td class="name" width="<?php echo ($cTipoDoc == "CRUCE") ? "9%" : "18%" ?>"><center>TERCERO2</center></td>
														<td class="name" width="06%"><center>SUCURSAL</center></td>
														<td class="name" width="10%"><center>NO DO</center></td>
														<td class="name" width="06%"><center>SUFIJO</center></td>
														<td class="name" width="10%"><center>DESCRIPCION</center></td>
														<td class="name" width="9%"><center>DEBITO</center></td>
														<td class="name" width="9%"><center>CREDITO</center></td>
													<?php } ?>
													<?php if ($cTipoDoc == "CRUCE") { ?>
														<td class="name" width="9%"><center>SALDO</center></td>
													<?php } ?>
												</tr>
												<?php
													$color = '#D5D5D5';
													$nTComVlrD = 0;
													$nTComVlrD = 0;
													for($j=0;$j<count($mCodDat);$j++) {
														if($mCodDat[$j]['commovxx'] == 'D') {
															$cComVlrD  = 0;
															$cComVlrC  = 0;
															$cComVlrD  = $mCodDat[$j]['comvlrxx'];
															$nTComVlrD += $mCodDat[$j]['comvlrxx'];
														}else{
															$cComVlrD  = 0;
															$cComVlrC  = 0;
															$cComVlrC  = $mCodDat[$j]['comvlrxx'];
															$nTComVlrC += $mCodDat[$j]['comvlrxx'];
														}
														$nSaldo += $cComVlrD-$cComVlrC; ?>
													<tr bgcolor="<?php echo $color ?>">
														<?php if ($cTipoDoc == "DO") { ?>
															<td class="letra7" align="center"><?php echo ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mCodDat[$j]['comcsc3x'] != '') ? $mCodDat[$j]['comidxxx']."-".$mCodDat[$j]['comcodxx']."-".$mCodDat[$j]['comcscxx']."-".$mCodDat[$j]['comcsc3x']."-".$mCodDat[$j]['comseqxx'] : $mCodDat[$j]['comidxxx']."-".$mCodDat[$j]['comcodxx']."-".$mCodDat[$j]['comcscxx']."-".$mCodDat[$j]['comcsc2x']."-".$mCodDat[$j]['comseqxx'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['comfecxx'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['ccoidxxx'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['sccidxxx'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['pucidxxx'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['ctoidxxx'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['commovxx'] ?></td>

															<td class="letra7" align="center"><?php echo $mCodDat[$j]['teridxxx'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['CLINOMXX'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['terid2xx'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['CLINOMXX'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['comidcxx']."-".$mCodDat[$j]['comcodcx']."-".$mCodDat[$j]['comcsccx']."-".$mCodDat[$j]['comseqcx']?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['comidc2x']."-".$mCodDat[$j]['comcodc2']."-".$mCodDat[$j]['comcscc2']."-".$mCodDat[$j]['comseqc2']?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['sucidxxx']."-".$mCodDat[$j]['docidxxx']."-".$mCodDat[$j]['docsufxx']?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['puctipej'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['comvlrxx'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['comvlr01'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['comvlr02'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['comvlrnf'] ?></td>
														<?php } else { ?>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['comidxxx']."-".$mCodDat[$j]['comcodxx']."-".$mCodDat[$j]['comcscxx']."-".str_pad($mCodDat[$j]['comseqxx'],3,"0",STR_PAD_LEFT) ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['comidcxx']."-".$mCodDat[$j]['comcodcx']."-".$mCodDat[$j]['comcsccx']."-".str_pad($mCodDat[$j]['comseqcx'],3,"0",STR_PAD_LEFT) ?></td>
															<td class="letra7" align="center"><?php echo ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mCodDat[$j]['comcsc3x'] != '') ? $mCodDat[$j]['comcsc3x'] : $mCodDat[$j]['comcsc2x'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['ctoidxxx'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['pucidxxx'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['regfcrex'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['comfecxx'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['comfecve'] ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['terid2xx'] ?></td>
															<td class="letra7" align="left"><?php echo utf8_encode($mCodDat[$j]['PRONOMXX']) ?></td>
															<td class="letra7" align="center"><?php echo $mCodDat[$j]['teridxxx'] ?></td>
															<td class="letra7" align="left"><?php echo utf8_encode($mCodDat[$j]['CLINOMXX']) ?></td>
															<td class="letra7" align="center"><?php echo utf8_encode($mCodDat[$j]['sucidxxx']) ?></td>
															<td class="letra7" align="center"><?php echo utf8_encode($mCodDat[$j]['docidxxx']) ?></td>
															<td class="letra7" align="center"><?php echo utf8_encode($mCodDat[$j]['docsufxx']) ?></td>
															<td class="letra7" align="left"><?php echo utf8_encode($mCodDat[$j]['comobsxx']) ?></td>
															<td class="letra7" align="right"><?php echo number_format($cComVlrD,2,',','.') ?></td>
															<td class="letra7" align="right"><?php echo number_format($cComVlrC,2,',','.') ?></td>
														<?php } ?>
														<?php if ($cTipoDoc == "CRUCE") { ?>
															<td class="letra7" align="right"><?php echo number_format(($nSaldo),2,',','.')?></td>
														<?php } ?>
													</tr>
													<?php
												}
												?>
													<?php if ($cTipoDoc != "DO") { ?>
														<tr>
															<td colspan="16" class="name" width="70%" align="right" style="border:1px solid <?php echo $color ?>">TOTAL DOCUMENTO</td>
															<td class="letra7" width="9%" align="right" style="border:1px solid <?php echo $color ?>"><?php echo number_format($nTComVlrD,2,',','.') ?></td>
															<td class="letra7" width="9%" align="right" style="border:1px solid <?php echo $color ?>"><?php echo number_format($nTComVlrC,2,',','.') ?></td>
															<?php if ($cTipoDoc == "CRUCE") { ?>
																<td class="letra7" width="9%" align="right" style="border:1px solid <?php echo $color ?>"><?php echo number_format(($nTComVlrD - $nTComVlrC),2,',','.') ?></td>
															<?php } ?>
														</tr>
													<?php } ?>
												</table>
										</td>
									</tr>
								</table>
								</center>
							<?php } else {  //echo "No se Generaron registros"; }
								echo "No se Generaron registros, problemas con el filtro.";
							} ?>
						</body>
					</html>
				<?php break;

				// PINTA POR EXCEL //
				case 2:
					if (count($mCodDat) > 0) {

						$header .= 'REPORTE DE MOVIMIENTO DE DOCUMENTOS'."\n";
						$header .= "\n";
						$data = '';
						$cNomFile = "REPORTE_DE_MOVIMIENTO_DE_DOCUMENTOS_".$_COOKIE['kUsrId'].date("YmdHis").".xls";

						if ($_SERVER["SERVER_PORT"] != "") {
							$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
						} else {
							$cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
						}

						if (file_exists($cFile)) {
							unlink($cFile);
						}

						$fOp = fopen($cFile, 'a');

						// Inicio de Caga de Datos
						$data .= '<table width="1024" border="1">';
							$data .= '<tr>';
								$data .= '<td class="name" colspan="19" style="font-size:18px;font-weight:bold"><center>REPORTE DE MOVIMIENTO DE DOCUMENTOS</center></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td class="name" colspan="19" style="font-size:16px;font-weight:bold"><center>DOCUMENTO '." ".$cTipoDoc;
								if($cComCsc != "") { $data .= ' ('.$cComCsc.')'; }
								$data .= '</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td style="border-bottom: hidden" class="name" colspan="19" style="font-size:14px;font-weight:bold"><center>CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: '.count($mCodDat).'</center></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td class="name" colspan="19" style="font-size:12px;">FECHA Y HORA DE CONSULTA: '.date('Y-m-d')."-".date('H:i:s').'</td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td class="name" colspan="19"></td>';
							$data .= '</tr>';
							$data .= '<tr>';
								$data .= '<td class="name" width="7%"><b><center>DOC.FUENTE</center></b></td>';
								$data .= '<td class="name" width="12%"><b><center>DOC.CRUCE</center></b></td>';

								$cTituloCsc = ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') ? "CSC TRES" : "CSC DOS";

								$data .= '<td class="name" width="10%"><b><center>'.$cTituloCsc.'</center></b></td>';
								$data .= '<td class="name" width="7%"><b><center>CONCEPTO</center></b></td>';
								$data .= '<td class="name" width="7%"><b><center>CUENTA</center></b></td>';
								$data .= '<td class="name" width="7%"><b><center>FECHA CRE.</center></b></td>';
								$data .= '<td class="name" width="7%"><b><center>FECHA DOC.</center></b></td>';
								$data .= '<td class="name" width="7%"><b><center>FECHA VTO</center></b></td>';
								$data .= '<td class="name" width="7%"><b><center>NIT</center></b></td>';
								$data .= '<td class="name" width="9%"><b><center>TERCERO1</center></b></td>';
								$data .= '<td class="name" width="7%"><b><center>NIT</center></b></td>';
								$data .= '<td class="name" width="9%"><b><center>TERCERO2</center></b></td>';
								$data .= '<td class="name" width="10%"><b><center>SUCURSAL</center></b></td>';
								$data .= '<td class="name" width="10%"><b><center>NO DO</center></b></td>';
								$data .= '<td class="name" width="10%"><b><center>SUFIJO</center></b></td>';
								$data .= '<td class="name" width="10%"><b><center>DESCRIPCION</center></b></td>';
								$data .= '<td class="name" width="9%"><b><center>DEBITO</center></b></td>';
								$data .= '<td class="name" width="9%"><b><center>CREDITO</center></b></td>';
								$data .= '<td class="name" width="9%"><b><center>SALDO</center></b></td>';
							$data .= '</tr>';

							$nTComVlrD = 0;
							$nTComVlrD = 0;
							for($j=0;$j<count($mCodDat);$j++){
								if($mCodDat[$j]['commovxx'] == 'D'){
									$cComVlrD  = 0;
									$cComVlrC   = 0;
									$cComVlrD   = $mCodDat[$j]['comvlrxx'];
									$nTComVlrD += $mCodDat[$j]['comvlrxx'];
								}else{
									$cComVlrD  = 0;
									$cComVlrC   = 0;
									$cComVlrC   = $mCodDat[$j]['comvlrxx'];
									$nTComVlrC += $mCodDat[$j]['comvlrxx'];
								}
								$nSaldo += $cComVlrD-$cComVlrC;

								$cFuente = ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mCodDat[$j]['comcsc3x'] != '') ? $mCodDat[$j]['comidxxx'].'-'.$mCodDat[$j]['comcodxx'].'-'.$mCodDat[$j]['comcscxx'].'-'.$mCodDat[$j]['comcsc3x'] : $mCodDat[$j]['comidxxx'].'-'.$mCodDat[$j]['comcodxx'].'-'.$mCodDat[$j]['comcscxx'];
								$cConsec = ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mCodDat[$j]['comcsc3x'] != '') ? $mCodDat[$j]['comcsc3x'] : $mCodDat[$j]['comcsc2x'];
								$data .= '<tr>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$cFuente.'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$mCodDat[$j]['comidcxx'].'-'.$mCodDat[$j]['comcodcx'].'-'.$mCodDat[$j]['comcsccx'].'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$cConsec.'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$mCodDat[$j]['ctoidxxx'].'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$mCodDat[$j]['pucidxxx'].'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$mCodDat[$j]['regfcrex'].'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$mCodDat[$j]['comfecxx'].'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$mCodDat[$j]['comfecve'].'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$mCodDat[$j]['terid2xx'].'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="left">'.$mCodDat[$j]['PRONOMXX'].'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$mCodDat[$j]['teridxxx'].'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="left">'.$mCodDat[$j]['CLINOMXX'].'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$mCodDat[$j]['sucidxxx'].'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$mCodDat[$j]['docidxxx'].'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="center">'.$mCodDat[$j]['docsufxx'].'</td>';
									$data .= '<td class="letra7" style="mso-number-format:\'\@\'" align="left">'.$mCodDat[$j]['comobsxx'].'</td>';
									$data .= '<td class="letra7" align="right">'.number_format($cComVlrD,2,',','.').'</td>';
									$data .= '<td class="letra7" align="right">'.number_format($cComVlrC,2,',','.').'</td>';
									$data .= '<td class="letra7" align="right">'.number_format(($nSaldo),2,',','.').'</td>';
								$data .= '</tr>';
							}
							$data .= '<tr style="font-size:14px;font-weight:bold">';
								$data .= '<td class="name" colspan="16" align="right">TOTAL DOCUMENTO</td>';
								$data .= '<td class="letra7" align="right">'.number_format($nTComVlrD,2,',','.').'</td>';
								$data .= '<td class="letra7" align="right">'.number_format($nTComVlrC,2,',','.').'</td>';
								$data .= '<td class="letra7" align="right">'.number_format(($nTComVlrD - $nTComVlrC),2,',','.').'</td>';
							$data .= '</tr>';
						$data .= '</table>';
						// Fin carga de datos

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
								exit;
							}else{
								$cNomArc = $cNomFile;
							}
						}else {
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

				// PINTA POR PDF //
				case 3:
					if (count($mCodDat) > 0) {

						$cAddr = "";
						if ($cAlfa == "DESARROL" || $cAlfa == "PRUEBASX"){
							$cAddr = "../";
						}

						$cRoot = $_SERVER['DOCUMENT_ROOT'];

						if($cComCsc != "") {
							$cTitulo2 = 'DOCUMENTO '.$cTipoDoc.' ('.$cComCsc.')';
						}else{
							$cTitulo2 = 'DOCUMENTO '.$cTipoDoc;
						}

						$nRegCon = count($mCodDat);

						##Switch para incluir fuente y clase pdf segun base de datos ##
						switch($cAlfa){
							case "COLMASXX":
								define('FPDF_FONTPATH',"../../../../../fonts/");
								require("../../../../../forms/fpdf.php");
							break;
							default:
								define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
								require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
							break;
						}
						##Fin Switch para incluir fuente y clase pdf segun base de datos ##


						class PDF extends FPDF {
							function Header() {
								global $cRoot; global $cPlesk_Skin_Directory;
								global $cAlfa; global $cTitulo2; global $nRegCon; global $nPag;
								switch ($cAlfa) {
									case "INTERLOG":
										$this->SetXY(4,7);
										$this->Cell(51,28,'',1,0,'C');
										$this->Cell(221,28,'',1,0,'C');
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/MaryAire.jpg',10,8,40,25);
										$this->SetFont('verdana','',16);
										$this->SetXY(55,13);
										$this->Cell(221,8,"REPORTE DE MOVIMIENTO DE DOCUMENTOS",0,0,'C');
										$this->Ln(8);
										$this->SetFont('verdana','',12);
										$this->SetX(59);
										$this->Cell(213,6,$cTitulo2,0,0,'C');
										$this->Ln(6);
										$this->SetFont('verdana','',10);
										$this->SetX(59);
										$this->Cell(213,6,'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: '.$nRegCon,0,0,'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "ROLDANLO"://ROLDAN
									case "TEROLDANLO"://ROLDAN
									case "DEROLDANLO"://ROLDAN
										$this->SetXY(4,7);
										$this->Cell(51,28,'',1,0,'C');
										$this->Cell(221,28,'',1,0,'C');
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoroldan.png',10,8,40,25);
										$this->SetFont('verdana','',16);
										$this->SetXY(55,13);
										$this->Cell(221,8,"REPORTE DE MOVIMIENTO DE DOCUMENTOS",0,0,'C');
										$this->Ln(8);
										$this->SetFont('verdana','',12);
										$this->SetX(59);
										$this->Cell(213,6,$cTitulo2,0,0,'C');
										$this->Ln(6);
										$this->SetFont('verdana','',10);
										$this->SetX(59);
										$this->Cell(213,6,'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: '.$nRegCon,0,0,'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "GRUMALCO"://GRUMALCO
									case "TEGRUMALCO"://GRUMALCO
									case "DEGRUMALCO"://GRUMALCO
										$this->SetXY(4,7);
										$this->Cell(51,28,'',1,0,'C');
										$this->Cell(221,28,'',1,0,'C');
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logomalco.jpg',10,8,40,25);
										$this->SetFont('verdana','',16);
										$this->SetXY(55,13);
										$this->Cell(221,8,"REPORTE DE MOVIMIENTO DE DOCUMENTOS",0,0,'C');
										$this->Ln(8);
										$this->SetFont('verdana','',12);
										$this->SetX(59);
										$this->Cell(213,6,$cTitulo2,0,0,'C');
										$this->Ln(6);
										$this->SetFont('verdana','',10);
										$this->SetX(59);
										$this->Cell(213,6,'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: '.$nRegCon,0,0,'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "ALADUANA": //ALADUANA
									case "TEALADUANA": //ALADUANA
									case "DEALADUANA": //ALADUANA
									case "DEDESARROL":
										$this->SetXY(4,7);
										$this->Cell(51,28,'',1,0,'C');
										$this->Cell(221,28,'',1,0,'C');
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoaladuana.jpg',12,9,36,24);
										$this->SetFont('verdana','',16);
										$this->SetXY(55,13);
										$this->Cell(221,8,"REPORTE DE MOVIMIENTO DE DOCUMENTOS",0,0,'C');
										$this->Ln(8);
										$this->SetFont('verdana','',12);
										$this->SetX(59);
										$this->Cell(213,6,$cTitulo2,0,0,'C');
										$this->Ln(6);
										$this->SetFont('verdana','',10);
										$this->SetX(59);
										$this->Cell(213,6,'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: '.$nRegCon,0,0,'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "ANDINOSX": //ANDINOSX
									case "TEANDINOSX": //ANDINOSX
									case "DEANDINOSX": //ANDINOSX
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
											// Dibujo //
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoandinos.jpg', 10, 9, 40, 24);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "GRUPOALC": //GRUPOALC
									case "TEGRUPOALC": //GRUPOALC
									case "DEGRUPOALC": //GRUPOALC
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
											// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoalc.jpg',9,12,40,18);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "AAINTERX": //AAINTERX
									case "TEAAINTERX": //AAINTERX
									case "DEAAINTERX": //AAINTERX
										$this->SetXY(4,7);
										$this->Cell(51,28,'',1,0,'C');
										$this->Cell(221,28,'',1,0,'C');
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logointernacional.jpg',12,9,36,24);
										$this->SetFont('verdana','',16);
										$this->SetXY(55,13);
										$this->Cell(221,8,"REPORTE DE MOVIMIENTO DE DOCUMENTOS",0,0,'C');
										$this->Ln(8);
										$this->SetFont('verdana','',12);
										$this->SetX(59);
										$this->Cell(213,6,$cTitulo2,0,0,'C');
										$this->Ln(6);
										$this->SetFont('verdana','',10);
										$this->SetX(59);
										$this->Cell(213,6,'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: '.$nRegCon,0,0,'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "AALOPEZX":
									case "TEAALOPEZX":
									case "DEAALOPEZX":
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoaalopez.png', 12, 9, 30);
										$this->SetXY(4,7);
										$this->Cell(272,22,'',1,0,'C');
										$this->SetFont('verdana','',16);
										$this->SetXY(4,8);
										$this->Cell(268,8,"REPORTE DE MOVIMIENTO DE DOCUMENTOS",0,0,'C');
										$this->Ln(8);
										$this->SetFont('verdana','',12);
										$this->SetX(4);
										$this->Cell(268,6,$cTitulo2,0,0,'C');
										$this->Ln(6);
										$this->SetFont('verdana','',10);
										$this->SetX(4);
										$this->Cell(268,6,'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: '.$nRegCon,0,0,'C');
										$this->Ln(10);
										$this->SetX(4);
									break;
									case "ADUAMARX": //ADUAMARX
									case "TEADUAMARX": //ADUAMARX
									case "DEADUAMARX": //ADUAMARX
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
											// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoaduamar.jpg',17,9,25);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "SOLUCION": //SOLUCION
									case "TESOLUCION": //SOLUCION
									case "DESOLUCION": //SOLUCION
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
											// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logosoluciones.jpg',7,11,45);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "FENIXSAS": //FENIXSAS
									case "TEFENIXSAS": //FENIXSAS
									case "DEFENIXSAS": //FENIXSAS
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
											// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logofenix.jpg',7,15,45);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "COLVANXX": //COLVANXX
									case "TECOLVANXX": //COLVANXX
									case "DECOLVANXX": //COLVANXX
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
											// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logocolvan.jpg',7,11,45);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "INTERLAC": //INTERLAC
									case "TEINTERLAC": //INTERLAC
									case "DEINTERLAC": //INTERLAC
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
											// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logointerlace.jpg',7,10,45);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "DHLEXPRE": //DHLEXPRE
									case "TEDHLEXPRE": //DHLEXPRE
									case "DEDHLEXPRE": //DHLEXPRE
										$this->SetXY(4,7);
										$this->Cell(51,28,'',1,0,'C');
										$this->Cell(221,28,'',1,0,'C');
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',10,8,40,25);
										$this->SetFont('verdana','',16);
										$this->SetXY(55,13);
										$this->Cell(221,8,"REPORTE DE MOVIMIENTO DE DOCUMENTOS",0,0,'C');
										$this->Ln(8);
										$this->SetFont('verdana','',12);
										$this->SetX(59);
										$this->Cell(213,6,$cTitulo2,0,0,'C');
										$this->Ln(6);
										$this->SetFont('verdana','',10);
										$this->SetX(59);
										$this->Cell(213,6,'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: '.$nRegCon,0,0,'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "KARGORUX": //KARGORUX
									case "TEKARGORUX": //KARGORUX
									case "DEKARGORUX": //KARGORUX
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
										// Dibujo //
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logokargoru.jpg', 11, 11, 35, 20);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "ALOGISAS": //LOGISTICA
									case "TEALOGISAS": //LOGISTICA
									case "DEALOGISAS": //LOGISTICA
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
										// Dibujo //
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 7, 11, 45);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
									case "PROSERCO":
									case "TEPROSERCO":
									case "DEPROSERCO":
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
										// Dibujo //
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoproserco.png', 7, 8.7, 45);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->Ln(15);
										$this->SetX(4);
									break;
                  case "MANATIAL":
                  case "TEMANATIAL":
                  case "DEMANATIAL":
                    $this->SetXY(4, 7);
                    $this->Cell(51, 28, '', 1, 0, 'C');
                    $this->Cell(221, 28, '', 1, 0, 'C');
                    // Dibujo //
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logomanantial.jpg', 7, 13, 44, 12);
                    $this->SetFont('verdana', '', 16);
                    $this->SetXY(55, 13);
                    $this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
                    $this->Ln(8);
                    $this->SetFont('verdana', '', 12);
                    $this->SetX(59);
                    $this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
                    $this->Ln(6);
                    $this->SetFont('verdana', '', 10);
                    $this->SetX(59);
                    $this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
                    $this->Ln(15);
                    $this->SetX(4);
                  break;
                  case "DSVSASXX":
                  case "DEDSVSASXX":
                  case "TEDSVSASXX":
                    $this->SetXY(4, 7);
                    $this->Cell(51, 28, '', 1, 0, 'C');
                    $this->Cell(221, 28, '', 1, 0, 'C');
                    // Dibujo //
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logodsv.jpg', 7, 15, 44, 12);
                    $this->SetFont('verdana', '', 16);
                    $this->SetXY(55, 13);
                    $this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
                    $this->Ln(8);
                    $this->SetFont('verdana', '', 12);
                    $this->SetX(59);
                    $this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
                    $this->Ln(6);
                    $this->SetFont('verdana', '', 10);
                    $this->SetX(59);
                    $this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
                    $this->SetX(4);
                    $this->Ln(15);
                  break;
                  case "MELYAKXX":    //MELYAK
                  case "DEMELYAKXX":  //MELYAK
                  case "TEMELYAKXX":  //MELYAK
                    $this->SetXY(4, 7);
                    $this->Cell(51, 28, '', 1, 0, 'C');
                    $this->Cell(221, 28, '', 1, 0, 'C');
                    // Dibujo //
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logomelyak.jpg', 7, 15, 44, 12);
                    $this->SetFont('verdana', '', 16);
                    $this->SetXY(55, 13);
                    $this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
                    $this->Ln(8);
                    $this->SetFont('verdana', '', 12);
                    $this->SetX(59);
                    $this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
                    $this->Ln(6);
                    $this->SetFont('verdana', '', 10);
                    $this->SetX(59);
                    $this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
                    $this->SetX(4);
                    $this->Ln(15);
                  break;
                  case "FEDEXEXP":    //FEDEX
                  case "DEFEDEXEXP":  //FEDEX
                  case "TEFEDEXEXP":  //FEDEX
                    $this->SetXY(4, 7);
                    $this->Cell(51, 28, '', 1, 0, 'C');
                    $this->Cell(221, 28, '', 1, 0, 'C');
                    // Dibujo //
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logofedexexp.jpg', 7, 13, 40, 20);
                    $this->SetFont('verdana', '', 16);
                    $this->SetXY(55, 13);
                    $this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
                    $this->Ln(8);
                    $this->SetFont('verdana', '', 12);
                    $this->SetX(59);
                    $this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
                    $this->Ln(6);
                    $this->SetFont('verdana', '', 10);
                    $this->SetX(59);
                    $this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
                    $this->SetX(4);
                    $this->Ln(15);
                  break;
									case "EXPORCOM":    //EXPORCOMEX
									case "DEEXPORCOM":  //EXPORCOMEX
									case "TEEXPORCOM":  //EXPORCOMEX
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
										// Dibujo //
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoexporcomex.jpg', 9, 11, 40, 20);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->SetX(4);
										$this->Ln(15);
									break;
									case "HAYDEARX":   //HAYDEARX
									case "DEHAYDEARX": //HAYDEARX
									case "TEHAYDEARX": //HAYDEARX
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
										// Dibujo //
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logohaydear.jpeg', 9, 11, 45, 20);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->SetX(4);
										$this->Ln(15);
									break;
									case "CONNECTA":   //CONNECTA
									case "DECONNECTA": //CONNECTA
									case "TECONNECTA": //CONNECTA
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
										// Dibujo //
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoconnecta.jpg', 9, 11, 35, 20);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->SetX(4);
										$this->Ln(15);
									break;
									case "OPENEBCO":   //OPENEBCO
									case "DEOPENEBCO": //OPENEBCO
									case "TEOPENEBCO": //OPENEBCO
										$this->SetXY(4, 7);
										$this->Cell(51, 28, '', 1, 0, 'C');
										$this->Cell(221, 28, '', 1, 0, 'C');
										// Dibujo //
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/opentecnologia.JPG', 7, 11, 45, 20);
										$this->SetFont('verdana', '', 16);
										$this->SetXY(55, 13);
										$this->Cell(221, 8, "REPORTE DE MOVIMIENTO DE DOCUMENTOS", 0, 0, 'C');
										$this->Ln(8);
										$this->SetFont('verdana', '', 12);
										$this->SetX(59);
										$this->Cell(213, 6, $cTitulo2, 0, 0, 'C');
										$this->Ln(6);
										$this->SetFont('verdana', '', 10);
										$this->SetX(59);
										$this->Cell(213, 6, 'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: ' . $nRegCon, 0, 0, 'C');
										$this->SetX(4);
										$this->Ln(15);
									break;
									default:
										$this->SetXY(4,7);
										$this->Cell(272,22,'',1,0,'C');
										$this->SetFont('verdana','',16);
										$this->SetXY(4,8);
										$this->Cell(268,8,"REPORTE DE MOVIMIENTO DE DOCUMENTOS",0,0,'C');
										$this->Ln(8);
										$this->SetFont('verdana','',12);
										$this->SetX(4);
										$this->Cell(268,6,$cTitulo2,0,0,'C');
										$this->Ln(6);
										$this->SetFont('verdana','',10);
										$this->SetX(4);
										$this->Cell(268,6,'CANTIDAD DE MOVIMIENTOS ENCONTRADOS EN LA CONSULTA: '.$nRegCon,0,0,'C');
										$this->Ln(10);
										$this->SetX(4);
									break;
								}

								if($this->PageNo() > 1 && $nPag ==1){
									$this->SetFont('verdana','B',6);
									$this->SetWidths(array('18','10','14','16','16','16','16','16','22','22','08','15','08','24','17','17','17'));
									$this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
									$this->SetX(4);
									$this->Row(array("DOC.FUENTE",
																	"DOC.CRUCE",
																	($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') ? "CSC TRES" : "CSC DOS",
																	"CONCEPTO",
																	"CUENTA",
																	"FECHA CRE.",
																	"FECHA DOC.",
																	"FECHA VTO",
																	"TERCERO1",
																	"TERCERO2",
																	"SUC",
																	"DO",
																	"SUF",
																	"DESCRIPCION",
																	"DEBITO",
																	"CREDITO",
																	"SALDO"));
									$this->SetFont('verdana','',6);
									$this->SetAligns(array('L','L','L','C','C','C','C','C','L','L','C','C','C','L','R','R','R'));
									$this->SetX(4);
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

						$pdf->SetX(4);
						$pdf->SetFont('verdana','B',8);
						$pdf->Cell(50,5,'FECHA Y HORA DE CONSULTA: ',0,0,'L');
						$pdf->SetFont('verdana','',8);
						$pdf->Cell(205,5,date('Y-m-d').'-'.date('H:i:s'),0,0,'L');

						$pdf->Ln(7);
						$pdf->SetFont('verdana','B',6);
						$pdf->SetWidths(array('18','10','14','16','16','16','16','16','22','22','08','15','08','24','17','17','17'));
						$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
						$pdf->SetX(4);
						$pdf->Row(array("DOC.FUENTE",
														"DOC.CRUCE",
														($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') ? "CSC TRES" : "CSC DOS",
														"CONCEPTO",
														"CUENTA",
														"FECHA CRE.",
														"FECHA DOC.",
														"FECHA VTO",
														"TERCERO1",
														"TERCERO2",
														"SUC",
														"DO",
														"SUF",
														"DESCRIPCION",
														"DEBITO",
														"CREDITO",
														"SALDO"));
						$pdf->SetFont('verdana','',6);
						$pdf->SetAligns(array('L','L','L','C','C','C','C','C','L','L','C','C','C','L','R','R','R'));

						$nTComVlrD = 0;
						$nTComVlrD = 0;
						$nPag = 0;
						for($j=0;$j<count($mCodDat);$j++){
							$nPag = 1;
							if($mCodDat[$j]['commovxx'] == 'D'){
								$cComVlrD  = 0;
								$cComVlrC   = 0;
								$cComVlrD   = $mCodDat[$j]['comvlrxx'];
								$nTComVlrD += $mCodDat[$j]['comvlrxx'];
							}else{
								$cComVlrD  = 0;
								$cComVlrC   = 0;
								$cComVlrC   = $mCodDat[$j]['comvlrxx'];
								$nTComVlrC += $mCodDat[$j]['comvlrxx'];
							}
							$nSaldo += $cComVlrD-$cComVlrC;

							$pdf->SetX(4);
							$pdf->Row(array(($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mCodDat[$j]['comcsc3x'] != '') ? $mCodDat[$j]['comidxxx'].'-'.$mCodDat[$j]['comcodxx'].'-'.$mCodDat[$j]['comcscxx'].'-'.$mCodDat[$j]['comcsc3x'] : $mCodDat[$j]['comidxxx'].'-'.$mCodDat[$j]['comcodxx'].'-'.$mCodDat[$j]['comcscxx'],
															$mCodDat[$j]['comidcxx'].'-'.$mCodDat[$j]['comcodcx'].'-'.$mCodDat[$j]['comcsccx'],
															($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mCodDat[$j]['comcsc3x'] != '') ? $mCodDat[$j]['comcsc3x'] : $mCodDat[$j]['comcsc2x'],
															$mCodDat[$j]['ctoidxxx'],
															$mCodDat[$j]['pucidxxx'],
															$mCodDat[$j]['regfcrex'],
															$mCodDat[$j]['comfecxx'],
															$mCodDat[$j]['comfecve'],
															$mCodDat[$j]['teridxxx']." - ".$mCodDat[$j]['CLINOMXX'],
															$mCodDat[$j]['terid2xx']." - ".$mCodDat[$j]['PRONOMXX'],
															$mCodDat[$j]['sucidxxx'],
															$mCodDat[$j]['docidxxx'],
															$mCodDat[$j]['docsufxx'],
															$mCodDat[$j]['comobsxx'],
															number_format($cComVlrD,2,',','.'),
															number_format($cComVlrC,2,',','.'),
															number_format(($nSaldo),2,',','.')));

						}
						$nPag = 0;
						$pdf->Ln(5);
						$pdf->SetX(4);
						$pdf->SetFont('verdana','B',8);
						$pdf->Cell(152,5,"",0,0,'C');
						$pdf->Cell(40,5,"DEBITO",1,0,'C');
						$pdf->Cell(40,5,"CREDITO",1,0,'C');
						$pdf->Cell(40,5,"SALDO",1,0,'C');
						$pdf->Ln(5);
						$pdf->SetX(4);
						$pdf->SetFont('verdana','B',8);
						$pdf->Cell(117,5,"",0,0,'R');
						$pdf->Cell(35,5,"TOTAL DOCUMENTO",1,0,'C');
						$pdf->SetFont('verdana','',8);
						$pdf->Cell(40,5,number_format($nTComVlrD,2,',','.'),1,0,'R');
						$pdf->Cell(40,5,number_format($nTComVlrC,2,',','.'),1,0,'R');
						$pdf->Cell(40,5,number_format(($nTComVlrD - $nTComVlrC)),1,0,'R');

						$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";

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
		} //if ($nSwitch == 0)
	} //if ($cEjePro == 0)

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

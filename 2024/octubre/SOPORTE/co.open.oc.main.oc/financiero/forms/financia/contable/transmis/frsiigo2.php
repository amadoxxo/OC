<?php

	##Estableciendo que el tiempo de ejecucion no se limite

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
		$dDesde  = $_POST['dDesde'];
		$dHasta  = $_POST['dHasta'];
		$gUsrId  = $_POST['gUsrId'];
		$gComCod = $_POST['gComCod'];
		$gComId  = $_POST['gComId'];
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

	$cAno = substr($dDesde,0,4);

	$nNumReg = 100;

	$qLoad = "SELECT SQL_CALC_FOUND_ROWS comidxxx ";
	$qLoad .= "FROM $cAlfa.fcod$cAno ";
	$qLoad .= "WHERE ";
	if ($gComId != "") {
		$qLoad .= "comidxxx = \"". $gComId ."\" AND ";
	}
	if ($gComCod != "") {
		$qLoad .= "comcodxx = \"".$gComCod."\" AND ";
	}
	if ($gUsrId != "") {
		$qLoad .= "regusrxx = \"".$gUsrId."\" AND ";
	}
	$qLoad .= "regestxx IN (\"ACTIVO\",\"INACTIVO\") AND ";
	$qLoad .= "comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" LIMIT 0,1";
	$xLoad = f_Mysql("SELECT","",$qLoad,$xConexion01, "");

	mysql_free_result($xLoad);

	$xNumRows = mysql_query("SELECT FOUND_ROWS();");
	$xRNR = mysql_fetch_array($xNumRows);
	$nRegistros =$xRNR['FOUND_ROWS()'];
	mysql_free_result($xNumRows);

	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;	

		$strPost  = "dDesde~".$dDesde."|";
		$strPost .= "dHasta~".$dHasta."|";
		$strPost .= "gComId~".$gComId."|";
		$strPost .= "gComCod~".$gComCod."|";
		$strPost .= "gUsrId~".$gUsrId;

		$vParBg['pbadbxxx'] = $cAlfa;                                         	//Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                                  	//Modulo
		$vParBg['pbatinxx'] = "TRANSSIIGO";                                   	//Tipo Interface
		$vParBg['pbatinde'] = "TRANSMISION SIIGO";                            	//Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                                             	//Sucursal
		$vParBg['doiidxxx'] = "";                                             	//Do
		$vParBg['doisfidx'] = "";                                             	//Sufijo
		$vParBg['cliidxxx'] = "";                                             	//Nit
		$vParBg['clinomxx'] = "";                                             	//Nombre Importador
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

	/**
	 * Ejecucion proceso
	 */
	if ($cEjePro == 0) {
		if ($nSwitch == 0) { // cuando lo hace el navegador y por consola
			if ($_SERVER["SERVER_PORT"] != "") {
				$cBrowser = "<html>";
				$cBrowser .= "<head>";
				$cBrowser .= "<title>Archivo Plano para Sistema SIIGO</title>";
				$cBrowser .= "<LINK rel = 'stylesheet' href = '".$cSystem_Libs_JS_Directory."/estilo.css'>";
				$cBrowser .= "<LINK rel = 'stylesheet' href = '".$cSystem_Libs_JS_Directory."/general.css'>";
				$cBrowser .= "<LINK rel = 'stylesheet' href = '".$cSystem_Libs_JS_Directory."/layout.css'>";
				$cBrowser .= "<LINK rel = 'stylesheet' href = '".$cSystem_Libs_JS_Directory."/custom.css'>";
				$cBrowser .= "</head>";
				$cBrowser .= "<body>";
			}

			$dos = "SIIGO_".$kUser."_".date("YmdHis").".TXT";
			if ($_SERVER["SERVER_PORT"] != "") {
				$fedi = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$dos;
			} else {
				$fedi = "{$OPENINIT['pathdr']}/opencomex/".$vSysStr['system_download_directory']."/".$dos;
			}

			$fp = fopen($fedi, 'a+');

			/**
			 * Trayendo Comprobantes de Nota Credito
			 */
			$qNotCre = "SELECT ";
			$qNotCre .= "CONCAT(comidxxx,\"~\",comcodxx) AS comidxxx ";
			$qNotCre .= "FROM $cAlfa.fpar0117 ";
			$qNotCre .= "WHERE ";
			$qNotCre .= "comidxxx = \"C\" AND ";
			$qNotCre .= "comtipxx != \"AJUSTES\" ";
			$xNotCre = f_MySql("SELECT","",$qNotCre,$xConexion01, "");
			$vNotCre = array();
			while ($xRDB = mysql_fetch_array($xNotCre)) {
				$vNotCre[count($vNotCre)] =$xRDB['comidxxx'];
			}

			/**
			 * Variable para manejar el total de registros
			 * @var number
			 */
			$j = 0; 

			/**
			 * Variable para controlar el reinicio de secuencia de cada comprobante 
			 * @var string
			 */
			$cComId = "";

			// echo "\nCantidad de Registros: ".$nRegistros;
			for ($i=0;$i<=$nRegistros;$i+=$nNumReg) {

				/*** Reinicio de conexion. ***/
				$xConexion01 = fnReiniciarConexion();

				$cAno = substr($dDesde,0,4);

				$qCodDat = "SELECT $cAlfa.fcod$cAno.*, ";
				$qCodDat .= "$cAlfa.fpar0115.pucdetxx, ";
				$qCodDat .= "$cAlfa.fpar0117.comtipxx, ";
				$qCodDat .= "$cAlfa.fpar0119.ctoantxx ";
				$qCodDat .= "FROM $cAlfa.fcod$cAno ";
				$qCodDat .= "LEFT JOIN $cAlfa.fpar0115 ON $cAlfa.fcod$cAno.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
				$qCodDat .= "$cAlfa.fpar0115.regestxx = \"ACTIVO\" ";
				$qCodDat .= "LEFT JOIN $cAlfa.fpar0117 ON $cAlfa.fcod$cAno.comidxxx = $cAlfa.fpar0117.comidxxx AND ";
				$qCodDat .= "$cAlfa.fcod$cAno.comcodxx = $cAlfa.fpar0117.comcodxx AND ";
				$qCodDat .= "$cAlfa.fpar0117.regestxx = \"ACTIVO\" ";
				$qCodDat .= "LEFT JOIN $cAlfa.fpar0119 ON $cAlfa.fcod$cAno.ctoidxxx = $cAlfa.fpar0119.ctoidxxx ";
				//LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcoc$cNewYear.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
				$qCodDat .= "WHERE ";
				if($gComId != ""){
					$qCodDat .= "$cAlfa.fcod$cAno.comidxxx = \"$gComId\" AND ";
				}
				if($gComCod != ""){
					$qCodDat .= "$cAlfa.fcod$cAno.comcodxx = \"$gComCod\" AND ";
				}
				if($gUsrId != ""){
					$qCodDat .= "$cAlfa.fcod$cAno.regusrxx = \"$gUsrId\" AND ";
				}
				/*$qCodDat .= "$cAlfa.fcod$cAno.comidxxx = \"R\" AND ";
				$qCodDat .= "$cAlfa.fcod$cAno.comcodxx = \"001\" AND ";
				$qCodDat .= "$cAlfa.fcod$cAno.comcscxx = \"2011090249\" AND ";*/
				$qCodDat .= "$cAlfa.fcod$cAno.regestxx IN (\"ACTIVO\",\"INACTIVO\") AND ";
				$qCodDat .= "$cAlfa.fcod$cAno.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" ";
				$qCodDat .= "ORDER BY $cAlfa.fcod$cAno.comidxxx,$cAlfa.fcod$cAno.comcodxx,$cAlfa.fcod$cAno.comcscxx,ABS($cAlfa.fcod$cAno.comcsc2x),ABS($cAlfa.fcod$cAno.comseqxx) ";
				$qCodDat .= "LIMIT $i,$nNumReg";
				$xCodDat = f_Mysql("SELECT","",$qCodDat,$xConexion01, "");
				// echo "\n".$qCodDat."~".mysql_num_rows($xCodDat);

				$nSwitchFile = 0; $cMsj = "";$xRCD_Ant = array();

				while ($xRCD = mysql_fetch_array($xCodDat)) {

					/**
					 * Cuando cambia el comprobante se reinicia la secuencia
					 */
					if ($cComId != "{$xRCD['comidxxx']}~{$xRCD['comcodxx']}~{$xRCD['comcscxx']}~{$xRCD['comcsc2x']}") {
						$cComId = "{$xRCD['comidxxx']}~{$xRCD['comcodxx']}~{$xRCD['comcscxx']}~{$xRCD['comcsc2x']}";
						$nComSeq = 0;
					}
					$nComSeq++;

					/**
					 * Limpiando Cadenas
					 */
					$cCad01 = "";                   // TIPO DE COMPROBANTE
					$cCad02 = "";                   // CODIGO COMPROBANTE
					$cCad03 = "";                   // NUMERO DE DOCUMENTO
					$cCad04 = "";                   // SECUENCIA  (HASTA 250)
					$cCad05 = "";                   // NIT
					$cCad06 = "000";                // SUCURSAL
					$cCad07 = "";                   // CUENTA CONTABLE
					$cCad08 = "0000000000000";      // CODIGO DE PRODUCTO
					$cCad09 = "";                   // FECHA DEL DOCUMENTO (AAAAMMDD)
					$cCad10 = "";                   // CENTRO DE COSTO
					$cCad11 = "";                   // SUBCENTRO DE COSTO
					$cCad12 = "";                   // DESCRIPCION DEL MOVIMIENTO
					$cCad13 = "";                   // DEBITO O CREDITO
					$cCad14 = "";                   // VALOR DEL MOVIMIENTO
					$cCad15 = "";                   // BASE DE RETENCION
					$cCad16 = "0001";               // CODIGO DEL VENDEDOR
					$cCad17 = "0000";               // CODIGO DE LA CIUDAD
					$cCad18 = "000";                // CODIGO DE LA ZONA
					$cCad19 = "0000";               // CODIGO DE LA BODEGA
					$cCad20 = "000";                // CODIGO DE LA UBICACION
					$cCad21 = "000000000000000";    // CANTIDAD
					$cCad22 = "";                   // TIPO DE DOCUMENTO CRUCE
					$cCad23 = "";                   // CODIGO COMPROBANTE CRUCE
					$cCad24 = "";                   // NUMERO DE DOCUMENTO CRUCE
					$cCad25 = "";                   // SECUENCIA DEL DOCUMENTO CRUCE
					$cCad26 = "";                   // FECHA VENCIMIENTO DOC CRUCE
					$cCad27 = "0000";               // CODIGO FORMA DE PAGO
					$cCad28 = "00";                 // CODIGO DEL BANCO
					$cCad29 = " ";                  // TIPO DOCUMENTO DE PEDIDO
					$cCad30 = "000";                // CODIGO COMPROBANTE DE PEDIDO
					$cCad31 = "00000000000";        // NUMERO DE COMPROBANTE DE PEDIDO
					$cCad32 = "000";                // SECUENCIA DE PEDIDO
					$cCad33 = "00";                 // CODIGO DE LA MONEDA
					$cCad34 = "000000000000000";    // TASA DE CAMBIO
					$cCad35 = "000000000000000";    // VALOR DEL MOVIMIENTO EN EXTRANJERA
					$cCad36 = "000";                // CONCEPTO DE NOMINA
					$cCad37 = "00000000000";        // CANTIDAD DE PAGO
					$cCad38 = "0000";               // PORCENTAJE DEL DESCUENTO DE MOVIMIENTO
					$cCad39 = "0000000000000";      // VALOR DE DESCUENTO DEL MOVIMIENTO
					$cCad40 = "0000";               // PORCENTAJE DE CARGO DEL MOVIMIENTO
					$cCad41 = "0000000000000";      // VALOR DE CARGO DEL MOVIMIENTO
					$cCad42 = "0000";               // PORCENTAJE DEL IVA DE MOVIMIENTO
					$cCad43 = "0000000000000";      // VALOR DE IVA DEL MOVIMIENTO
					$cCad44 = "N";                  // INDICADOR DE NOMINA
					$cCad45 ="0";                  // NUMERO DE PAGO
					$cCad46 = "00000000000";        // NUMERO DE CHEQUE
					$cCad47 = "N";                  // INDICADOR TIPO MOVIMIENTO
					$cCad48 = "OPEN";               // NOMBRE DEL COMPUTADOR
					$cCad49 = "";                   // ESTADO DEL COMPROBANTE
					$cCad50 = "  ";                 // ECUADOR
					$cCad51 = "00";                 // ECUADOR
					$cCad52 = "    ";               // PERU NUMERO DE COMPROBANTE DEL PROVEEDOR
					$cCad53 = "00000000000";        // NUMERO DEL DOCUMENTO DEL PROVEEDOR
					$cCad54 = "          ";         // PREFIJO DEL DOCUMENTO DEL PROVEEDOR
					$cCad55 = "00000000";           // FECHA DE DOCUMENTO DE PROVEEDOR
					$cCad56 = "000000000000000000"; // PRECIO UNITARIO EN MONEDA LOCAL
					$cCad57 = "000000000000000000"; // PRECIO UNITARIO EN MONEDA EXTRANJERA
					$cCad58 = " ";                  // INDICAR TIPO DE MOVIMIENTO
					$cCad59 = "000";                // VECES A DEPRECIAR EL ACTIVO
					$cCad60 = "00";                 // ECUADOR SECUENCIA DE TRANSACCION
					$cCad61 = "0000000000";         // ECUADOR AUTORIZACION IMPRENTA
					$cCad62 = "A";                  // ECUADOR SECUENCIA MARCADA COMO IVA PARA EL COA
					$cCad63 = "000";                // NUMERO DE CAJA
					$cCad64 = "000000000000000";    // -- SIGNO -- NUMERO DE PUNTOS OBTENIDOS
					$cCad65 = "000000000000000";    // CANTIDAD DOS
					$cCad66 = "000000000000000";    // CANTIDAD ALTERNA DOS
					$cCad67 = "L";                  // METODO DE DEPRECIACION
					$cCad68 = "000000000000000000"; // CANTIDAD DE FACTOR DE CONVERSION
					$cCad69 = "1";                  // OPERADOR DE FACTOR DE CONVERSION
					$cCad70 = "0000000000";         // FACTOR DE CONVERSION
					$cCad71 = "00000000";           // FECHA DE CADUCIDAD
					$cCad72 = "00";                 // CODIGO ICE
					$cCad73 = "     ";              // CODIGO RETENCION
					$cCad74 = " ";                  // CLASE RETENCION
					$cCad75 = "0000";               // Codigo del motivo de devolucion
					$cCad76 = "                                           "; // DATOS M/CIA CONSIGNACION
					$cCad77 = "                   "; // NUMERO COMPROBANTE FISCAL PROPIO (REP.DOM)
					$cCad78 = "                   "; // NUMERO COMPROBANTE FISCAL PROVEEDOR (REP.DOM)
					$cCad79 = " ";                   // INDICADOR TIPO DE LETRA:  1
					$cCad80 = " ";                   // ESTADO DE LA LETRA:  1

					//Eliminando caracteres de tabulacion, interlineado de los campos
					foreach ($xRCD as $ckey => $cValue) {
						$xRCD[$ckey] = str_replace($vBuscar, $vReempl,$xRCD[$ckey]);
					}
					// Busco los datos de cabecera.
					$qCabFac = "SELECT * ";
					$qCabFac .= "FROM $cAlfa.fcoc$cAno ";
					$qCabFac .= "WHERE ";
					$qCabFac .= "comidxxx = \"{$xRCD['comidxxx']}\" AND ";
					$qCabFac .= "comcodxx = \"{$xRCD['comcodxx']}\" AND ";
					$qCabFac .= "comcscxx = \"{$xRCD['comcscxx']}\" AND ";
					$qCabFac .= "comcsc2x = \"{$xRCD['comcsc2x']}\" AND ";
					$qCabFac .= "regestxx = \"{$xRCD['regestxx']}\" LIMIT 0,1";
					$xCabFac = f_MySql("SELECT","",$qCabFac,$xConexion01, "");
					//f_Mensaje(__FILE__,__LINE__,$qCabFac." ~ ".mysql_num_rows($xCabFac));
					$vCabFac = mysql_fetch_array($xCabFac);
					// Fin Busco los datos de cabecera.

					// Busco las caracteristicas de la cuenta.
					/*$qPucId  = "SELECT * ";
					$qPucId .= "FROM $cAlfa.fpar0115 ";
					$qPucId .= "WHERE ";
					$qPucId .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$xRCD['pucidxxx']}\" AND ";
					$qPucId .= "regestxx = \"ACTIVO\" LIMIT 0,1";
					$xPucId  = f_MySql("SELECT","",$qPucId,$xConexion01,"");
					$vPucId  = mysql_fetch_array($xPucId);*/
					// Fin Busco las caracteristicas de la cuenta.

					##Busco Tipo de Comprobante para hacer validacion en los comprobantes de Tipo P y enviar el nit2 ##
					/*$qComTip  = "SELECT * ";
					$qComTip .= "FROM $cAlfa.fpar0117 ";
					$qComTip .= "WHERE ";
					$qComTip .= "$cAlfa.fpar0117.comidxxx = \"{$xRCD['comidxxx']}\" AND ";
					$qComTip .= "$cAlfa.fpar0117.comcodxx = \"{$xRCD['comcodxx']}\" AND ";
					$qComTip .= "$cAlfa.fpar0117.regestxx = \"ACTIVO\" LIMIT 0,1 ";
					$xComTip = f_MySql("SELECT","",$qComTip,$xConexion01,"");
					$vComTip  = mysql_fetch_array($xComTip);*/
					##Fin Busco Tipo de Comprobante para hacer validacion en los comprobantes de Tipo P y enviar el nit2 ##

					##Busco si el concepto es de anticipos##
					/*$qCtoDat  = "SELECT ";
					$qCtoDat .= "$cAlfa.fpar0119.ctoantxx ";
					$qCtoDat .= "FROM $cAlfa.fpar0119 ";
					$qCtoDat .= "WHERE ";
					$qCtoDat .= "$cAlfa.fpar0119.ctoidxxx = \"{$xRCD['ctoidxxx']}\"  ";
					$xCtoDat  = f_MySql("SELECT","",$qCtoDat,$xConexion01,"");
					$vCtoDat  = mysql_fetch_array($xCtoDat);
					//f_Mensaje(__FILE__,__LINE__,$qCtoDat);*/
					##Fin Busco si el concepto es de anticipos##

					$j++;
					$supercadena = '';

					$cCad01 = str_pad($xRCD['comidxxx'],1,chr(32),STR_PAD_RIGHT);								 /* Tipo de Comprobante */
					switch ($xRCD['comidxxx']) { // Trampa para Alcomex para enviar todos los comprobantes de facturacion como F-001.
						case "F":
							switch ($xRCD['comcodxx']) {
								case "051":
								case "052":
								case "053":
								case "054":
								case "055":
								case "057":
									$cCad02 = str_pad("051",3,"0",STR_PAD_LEFT);
								break;
								default:
									$cCad02 = str_pad($xRCD['comcodxx'],3,"0",STR_PAD_LEFT);
								break;/* Codigo de Comprobante */
							}
						break;
						default:
							$cCad02 = str_pad($xRCD['comcodxx'],3,"0",STR_PAD_LEFT);
						break;	 /* Codigo de Comprobante */
					}
					$cCad03 = str_pad($xRCD['comcsc2x'],11,"0",STR_PAD_LEFT); 									 /* Numero del Documento */
					$cCad04 = str_pad($nComSeq,5,"0",STR_PAD_LEFT); 										 /* Secuencia */
					//if ($xRCD['comidxxx'] == "F" && ($xRCD['comctocx'] == "SS" || $xRCD['comctocx'] == "SC") && f_InList($vCabFac['terid2xx'],$vSysStr['alcomex_nits_companias_vinculadas'])) {
					if($xRCD['ctoantxx'] == "SI"){//Si el concepto contable esta marcado como Anticipo se debe enviar el nit del teri2xx
						$cCad05 = str_pad($xRCD['terid2xx'],13,"0",STR_PAD_LEFT);
					}elseif ($xRCD['comidxxx'] == "F") {
						//$cCad05 = str_pad($xRCD['terid2xx'],13,"0",STR_PAD_LEFT); // Para todas las cuentas del Comprobante Tipo F se debe enviar el Nit del FACTURAR A
						if(substr($xRCD['pucidxxx'],0,6) == "280505"){
							$cCad05 = str_pad($xRCD['terid2xx'],13,"0",STR_PAD_LEFT); // Para las cuentas de Anticipo y cuyo Comprobante sea Tipo F se debe enviar el Nit del Importador
						}else{
							$cCad05 = str_pad($vCabFac['terid2xx'],13,"0",STR_PAD_LEFT); // Para todas las cuentas del Comprobante Tipo F se debe enviar el Nit del FACTURAR A
						}
					}elseif(($xRCD['comidxxx'] == "L" || $xRCD['comidxxx'] == "G") && $xRCD['pucdetxx'] == 'D'){
						$cCad05 = str_pad($xRCD['terid2xx'],13,"0",STR_PAD_LEFT); // Para todas las cuentas del Comprobante Tipo L o G y detalle D se debe enviar el Nit dos
					}elseif($xRCD['comidxxx'] == "P" && ($xRCD['comtipxx'] == "CPC" || $xRCD['comtipxx'] == "RCM") && $xRCD['pucdetxx'] == "D"){//Si es Comprobante tipo P y detalle de cuenta es de Do's, se debe enviar el nit2
						$cCad05 = str_pad($xRCD['terid2xx'],13,"0",STR_PAD_LEFT);
					}else{ 
						/**
						 * Si es una nota credito debe comportarse igual que la factura
						 */
						if (in_array("{$xRCD['comidxxx']}~{$xRCD['comcodxx']}", $vNotCre) == true) {
							if(substr($xRCD['pucidxxx'],0,6) == "280505"){
								$cCad05 = str_pad($xRCD['terid2xx'],13,"0",STR_PAD_LEFT); // Para las cuentas de Anticipo y cuyo Comprobante sea Tipo F se debe enviar el Nit del Importador
							}else{
								$cCad05  = str_pad($vCabFac['terid2xx'],13,"0",STR_PAD_LEFT); // Para todas las cuentas del Comprobante Tipo F se debe enviar el Nit del FACTURAR A
							}
						} else {
							$cCad05 = str_pad($xRCD['teridxxx'],13,"0",STR_PAD_LEFT);                  /* Nit */
						}
					}

					$cCad07 = str_pad($xRCD['pucidxxx'],10,"0",STR_PAD_LEFT); 								 	 /* Cuenta */
					/*
					if ($xRCD['comidxxx'] == "F" && $xRCD['comctocx'] == "SS" && f_InList($vCabFac['terid2xx'],"800188557","830506117","800006786","830063139","900148614")) {
					$cCad07 = "1310100500"; // Trampa para Alcomex - Compañias Vinculadas.
					} else {
					$cCad07 = str_pad($xRCD['pucidxxx'],10,"0",STR_PAD_LEFT); 								 // Cuenta
					}
					*/
					$cCad09 = str_pad(str_replace('-','',$xRCD['comfecxx']),8,"0",STR_PAD_LEFT); /* Fecha del Comprobante */
					$xRCD['sccidxxx'] = 5;
					switch (strlen($xRCD['sccidxxx'])) {
						case "7": // Cuando la longitud del sub centro de costo sea de 7 digitos.
							$cCad10 = str_pad(substr($xRCD['ccoidxxx'],0,4),4,"0",STR_PAD_LEFT); 		 // Centro de Costos
							$cCad11 = str_pad(substr($xRCD['sccidxxx'],-3),3,"0",STR_PAD_LEFT); 		 // Sub Centro de Costos
						break;
						case "6": // Cuando la longitud del sub centro de costo sea de 6 digitos (porque el DO no permite cero a la izquierda).
							$cCad10 = str_pad(substr($xRCD['ccoidxxx'],0,3),4,"0",STR_PAD_LEFT); 		 // Centro de Costos
							$cCad11 = str_pad(substr($xRCD['sccidxxx'],-3),3,"0",STR_PAD_LEFT); 		 // Sub Centro de Costos
						break;
						case "0": // Cuando la longitud del sub centro de costo sea de 0 digitos (viene vacio, en este caso uso el subcentro del registro anterior).
							$xRCD['sccidxxx'] = str_pad("0",7,"0",STR_PAD_LEFT);
							$cCad10 = str_pad(substr($xRCD['ccoidxxx'],0,4),4,"0",STR_PAD_LEFT); 		 // Centro de Costos
							$cCad11 = str_pad(substr($xRCD['sccidxxx'],-3),3,"0",STR_PAD_LEFT); 		 // Sub Centro de Costos
						break;
						default: // Cuando la longitud del sub centro de costo sea diferente 7,6,0 digitos.
							$xRCD['sccidxxx'] = str_pad($xRCD['sccidxxx'],7,"0",STR_PAD_LEFT);
							$cCad10 = str_pad(substr($xRCD['ccoidxxx'],0,4),4,"0",STR_PAD_LEFT); 		 // Centro de Costos
							$cCad11 = str_pad(substr($xRCD['sccidxxx'],-3),3,"0",STR_PAD_LEFT); 		 // Sub Centro de Costos
						break;
					}
					// $cCad10 = str_pad(substr($xRCD['ccoidxxx'],0,4),4,"0",STR_PAD_LEFT); 		 // Centro de Costos

					// $cCad11 = str_pad(substr($xRCD['sccidxxx'],-3),3,"0",STR_PAD_LEFT);

					$cCad12 = str_pad(substr($xRCD['comobsxx'],0,50),50,chr(32),STR_PAD_RIGHT);  /* Observacion del Item */
					$cCad13 = str_pad($xRCD['commovxx'],1,chr(32),STR_PAD_RIGHT); 							 /* Tipo de Movimiento Debito o Credito */
					/* Valor del Movimiento */
					$decex = explode('.',$xRCD['comvlrxx']);
					$nint = $decex[0];
					$ndec = $decex[1];
					$cCad1 = str_pad($nint,13,"0",STR_PAD_LEFT);
					$cCad2 = str_pad($ndec,2,"0",STR_PAD_RIGHT);
					$cCad14 = $cCad1.$cCad2;																											 /* Valor del Movimiento */
					/* Fin Valor del Movimiento */
					/* Base Retencion */
					$decex = explode('.',$xRCD['comvlr01']); //??????
					$nint = $decex[0];
					$ndec = $decex[1];
					$cCad1 = str_pad($nint,13,"0",STR_PAD_LEFT);
					$cCad2 = str_pad($ndec,2,"0",STR_PAD_RIGHT);
					$cCad15 = $cCad1.$cCad2;																											 /* Base de la Retencion */
					/* Fin Base Retencion */
					$cCad22 = str_pad($xRCD['comidcxx'],1,"0",STR_PAD_LEFT);										 /* Tipo Documento Cruce */
					switch ($xRCD['comidcxx']) {
						case "F":
							switch ($xRCD['comcodcx']) {
							case "051":
							case "052":
							case "053":
							case "054":
							case "055":
							case "057":
								$cCad23 = str_pad("051",3,"0",STR_PAD_LEFT);
							break;																					/* Codigo Documento Cruce */
							default:
								$cCad23 = str_pad($xRCD['comcodcx'],3,"0",STR_PAD_LEFT);
							break;
						}
						break;
						default:
							$cCad23 = str_pad($xRCD['comcodcx'],3,"0",STR_PAD_LEFT);
						break;	 /* Codigo Documento Cruce */
					}

					if($xRCD['pucdetxx'] == "D"){
						$xRCD['comcsccx'] = 0;
					}
					$cCad24 = str_pad($xRCD['comcsccx'],11,"0",STR_PAD_LEFT); 									 /* Numero Documento Cruce */
					$cCad25 = str_pad($xRCD['comseqcx'],3,"0",STR_PAD_LEFT);   									 /* Secuencia Documento Cruce */
					$cCad26 = str_pad(str_replace('-','',$xRCD['comfecve']),8,"0",STR_PAD_LEFT); /* Fecha Vencimiento Doc. Cruce */
					/* Estado del Comprobante */
					if ($xRCD['regestxx'] == "ACTIVO") {
						$cCad49 = " ";																														 /* Estado del Comprobante */
					} else {
						$cCad49 = "A";
					}																																					 	 /* Estado del Comprobante */
					/* Fin Estado del Comprobante */

					$supercadena  = $cCad01.$cCad02.$cCad03.$cCad04.$cCad05.$cCad06.$cCad07.$cCad08.$cCad09.$cCad10;
					$supercadena .= $cCad11.$cCad12.$cCad13.$cCad14.$cCad15.$cCad16.$cCad17.$cCad18.$cCad19.$cCad20;
					$supercadena .= $cCad21.$cCad22.$cCad23.$cCad24.$cCad25.$cCad26.$cCad27.$cCad28.$cCad29.$cCad30;
					$supercadena .= $cCad31.$cCad32.$cCad33.$cCad34.$cCad35.$cCad36.$cCad37.$cCad38.$cCad39.$cCad40;
					$supercadena .= $cCad41.$cCad42.$cCad43.$cCad44.$cCad45.$cCad46.$cCad47.$cCad48.$cCad49.$cCad50;
					$supercadena .= $cCad51.$cCad52.$cCad53.$cCad54.$cCad55.$cCad56.$cCad57.$cCad58.$cCad59.$cCad60;
					$supercadena .= $cCad61.$cCad62.$cCad63.$cCad64.$cCad65.$cCad66.$cCad67.$cCad68.$cCad69.$cCad70;
					$supercadena .= $cCad71.$cCad72.$cCad73.$cCad74.$cCad75.$cCad76.$cCad77.$cCad78.$cCad79.$cCad80;

					//if (strlen($supercadena) == 220) {
					if (strlen($supercadena) == 625) {
						fwrite($fp,trim($supercadena));
						if ($j < $nRegistros){
							fwrite($fp,chr(13).chr(10));
						}
					} else {
						$nSwitchFile = strlen($supercadena);
						$cMsj .= "$cCad01-$cCad02-$cCad03-$cCad04, Error la Longitud del Registro es Diferente a [$nSwitchFile], Verifique.<br>";
						//$cMsj .= "$cCad24-$cCad11, Error la Longitud del Registro es Diferente a [$nSwitchFile], Verifique.<br>";
					}

					$xRCD_Ant = $xRCD; // Guardo un backup del registro que lei.
				}
			}

			fclose($fp);

			if (file_exists($fedi)) {
				chmod($fedi, intval($vSysStr['system_permisos_archivos'], 8));
			} else {
				if ($_SERVER["SERVER_PORT"] != "") {
					f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $fedi, Favor Comunicar este Error a openTecnologia S.A.");
				} else {
					$cMsj .= "No se encontro el archivo $fedi, Favor Comunicar este Error a openTecnologia S.A.";
				}
			}

			if ($j == 0) { $nSwitchFile = 1; $cMsj .= "No se Generaron Registros, Verifique.<br>"; }

			if ($nSwitchFile == 0) {
				if ($_SERVER["SERVER_PORT"] != "") {
					$cBrowser .= "<br><br><center><a href ='$fedi'>SIIGO.TXT</a></center>";
					$cBrowser .= "</body>";
					$cBrowser .= "</html>";

					echo $cBrowser;
				} else {
					$cNomArc = $dos;
				}
			} else {
        echo $cMsj;
			}
			/*** Fin Logica del proceso  ***/
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
				$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}
		}
	} // fin del if ($_SERVER["SERVER_PORT"] == "")
?>

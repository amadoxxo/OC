<?php

	/**
	 * Imprime Reporte de Fondo Rotativo
	 * --- Descripcion: Permite Imprimir Reporte de Fondo Rotativo
	 * @author Juan Jose Trujillo <juan.trujillo@open-eb.co>
	 * @author Hair Zabala Cuervo <hair.zabala@open-eb.co>
	 * @version 001
	 */

  // ini_set('error_reporting', E_ALL);
  // ini_set("display_errors","1");

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
		include("../../../../../config/config.php");
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
	} // fin if ($_SERVER["SERVER_PORT"] != "")

	if ($_SERVER["SERVER_PORT"] == "") {
		$gTerId  = $_POST['gTerId'];		
		$gDesde  = $_POST['gDesde'];		
		$gHasta  = $_POST['gHasta'];
		$gSucId  = $_POST['gSucId'];
		$gDocNro = $_POST['gDocNro'];
		$gDocSuf = $_POST['gDocSuf'];
		$gEstado = $_POST['gEstado'];
		$cTipo   = $_POST['cTipo'];
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;

		$strPost = "gTerId~" . $gTerId . "|gDesde~" . $gDesde . "|gHasta~" . $gHasta .  "|gSucId~" . $gSucId . "|gDocNro~" . $gDocNro . "|gDocSuf~" . $gDocSuf . "|cTipo~" . $cTipo . "|gEstado~" . $gEstado;

		# Numero de registros
		$qRegistros  = "SELECT SQL_CALC_FOUND_ROWS ";
		$qRegistros .= "$cAlfa.sys00121.sucidxxx, ";
		$qRegistros .= "$cAlfa.sys00121.docidxxx, ";
		$qRegistros .= "$cAlfa.sys00121.docsufxx, ";
		$qRegistros .= "$cAlfa.sys00121.cliidxxx, ";
		$qRegistros .= "$cAlfa.sys00121.regfcrex, ";
		$qRegistros .= "$cAlfa.sys00121.regestxx ";
		$qRegistros .= "FROM $cAlfa.sys00121 ";
		$qRegistros .= "WHERE ";
		if($gDocNro!=""){
		$qRegistros .= "$cAlfa.sys00121.sucidxxx = \"$gSucId\" AND ";
		$qRegistros .= "$cAlfa.sys00121.docidxxx = \"$gDocNro\" AND ";
		$qRegistros .= "$cAlfa.sys00121.docsufxx = \"$gDocSuf\" AND ";
		}
		if($gTerId!=""){
			$qRegistros .= "$cAlfa.sys00121.cliidxxx = \"$gTerId\" AND ";
		}
		$qRegistros .= "$cAlfa.sys00121.regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
		switch ($gEstado){
			case "TODOS":
				$qRegistros .= "$cAlfa.sys00121.regestxx IN (\"ACTIVO\",\"FACTURADO\") ";
			break;
			case "FACTURADO":
				$qRegistros .= "$cAlfa.sys00121.regestxx = \"FACTURADO\" ";
			break;
			case "ACTIVO":
				$qRegistros .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" ";
			break;
		}

		$xRegistros  = f_MySql("SELECT","",$qRegistros,$xConexion01,"");
		// f_Mensaje(__FILE__,__LINE__,$qRegistros."~".mysql_num_rows($xRegistros));
		mysql_free_result($xRegistros);

		$xNumRows = mysql_query("SELECT FOUND_ROWS();");
		$xRNR = mysql_fetch_array($xNumRows);
		$nRegistros =$xRNR['FOUND_ROWS()'];
		mysql_free_result($xNumRows);

		$vParBg['pbadbxxx'] = $cAlfa;                           // Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                    // Modulo
		$vParBg['pbatinxx'] = "FONDOROTATIVO";           				// Tipo Interface
		$vParBg['pbatinde'] = "REPORTE FONDO ROTATIVO";      		// Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = trim($gSucId);                    // Sucursal
		$vParBg['doiidxxx'] = trim($gDocNro);                   // Do
		$vParBg['doisfidx'] = trim($gDocSuf);                   // Sufijo
		$vParBg['cliidxxx'] = $gTerId;                          // Nit
		$vParBg['clinomxx'] = $xDDE['clinomxx'];                // Nombre Importador
		$vParBg['pbapostx'] = $strPost;													// Parametros para reconstruir Post
		$vParBg['pbatabxx'] = "";                               // Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];      // Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];          // cookie
		$vParBg['pbacrexx'] = $nRegistros;                      // Cantidad Registros
		$vParBg['pbatxixx'] = 0.4;                              // Tiempo Ejecucion x Item en Segundos
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

			if($gTerId != ""){
				#Busco el nombre del cliente
				$qCliNom  = "SELECT ";
				$qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
				$qCliNom .= "FROM $cAlfa.SIAI0150 ";
				$qCliNom .= "WHERE ";
				$qCliNom .= "CLIIDXXX = \"{$gTerId}\" LIMIT 0,1";
				$xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qCliNom." ~ ".mysql_num_rows($xCliNom));
		
				if (mysql_num_rows($xCliNom) > 0) {
					$xDDE = mysql_fetch_array($xCliNom);
				} else {
					$xDDE['clinomxx'] = "CLIENTE SIN NOMBRE";
				}
			}

			$cTitulo = "ESTADO: ";
			switch ($gEstado) {
				case "ACTIVO":
					$cTitulo .= "NO FACTURADO ";
				break;
				case "FACTURADO":
					$cTitulo .= "FACTURADO ";
				break;
				default:
					$cTitulo .= "";
				break;
			}

			$nColspan = 21;

			switch ($cTipo) {
				case 1: // PINTA POR PANTALLA //
					if ($_SERVER["SERVER_PORT"] != "") {
						?>
						<html>
							<head>
								<title>Reporte Fondo Rotativo</title>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
							
							</head>
							<body>
							<form name = 'frgrm' action='frinpgr.php' method="POST">
								<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">
									<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
										<td class="name" colspan="<?php echo $nColspan; ?>" align="left">
									
											<font size="3"><b>
												FONDO ROTATIVO <br>
												<?php if($gDocNro!=""){ ?>
													DO: <?php echo $gSucId." - ".$gDocNro." - ".$gDocSuf ?><br>
												<?php }
												if($gTerId!=""){ ?>
													CLIENTE: <?php echo "[".$gTerId."] ".$xDDE['clinomxx'] ?><br>
												<?php } 
													echo "DESDE ".$gDesde." HASTA ".$gHasta ?><br>
												<?php 
												if($cTitulo != ""){
													echo $cTitulo ?><br>
												<?php 
												} ?>
											</b></font>	
										</td>
									</tr>
									<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
										<td class="name" colspan="<?php echo $nColspan; ?>" align="left">
								
											<font size="3">
												<b>TOTAL DE REGISTROS <input type="text" name="nCanReg" style="width:80px" readonly><br>
											</font>
										
										</td>
									</tr>
									<tr height="20">
										<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Nit del Cliente</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nombre Cliente</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>DO</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Anticipo</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Documento del Anticipo</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Tipo Operaci&oacute;n</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>No. Pedido</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nit Proveedor</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nombre Proveedor</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Detalle Gasto</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>No. de Factura Proveedor</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha de Causaci&oacute;n de Factura Proveedor</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>No. Causaci&oacute;n</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha de Pago al Proveedor</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Doc. que Cancela Factura del Proveedor</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>No. Factura de Venta de Mario Londo&ntilde;o</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha Factura de Venta Mario Londo&ntilde;o</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Documento que Cancela Factura de Venta</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nit del Facturar A</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nombre del Facturar A</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Valor del Gasto de la Factura del Proveedor</font></b></td>

									</tr>
						<?php 
					}
				break;
				case 2: // PINTA POR EXCEL //
					$header  = 'REPORTE FONDO ROTATIVO'."\n";
					$header .= "\n";
					$data = '';
					$cNomFile = "REPORTE_FONDO_ROTATIVO_" . $kUser . "_" . date("YmdHis") . ".xls";

					if ($_SERVER["SERVER_PORT"] != "") {
						$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
					} else {
						$cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
					}

					if (file_exists($cFile)) {
						unlink($cFile);
					}

					$fOp = fopen($cFile, 'a');

					$data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">';
						$data .= '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
							$data .= '<td class="name" colspan="'.$nColspan.'" align="left">';
						
								$data .= '<font size="3">';
								$data .= 'FONDO ROTATIVO <br>';
								if($gDocNro!=""){
									$data .= 'DO: '.$gSucId." - ".$gDocNro." - ".$gDocSuf.'<br>';
								}
								if($gTerId!=""){
									$data .= 'CLIENTE: '."[".$gTerId."] ".$xDDE['clinomxx'].'<br>';
								}
								$data .= 'DESDE '.$gDesde.' HASTA '.$gHasta.'<br>';
								if($cTitulo != ""){
									$data .= '<b>'.$cTitulo.'<br>';
								}
								$data .= '</b>';
								$data .= '</font>';
							$data .= '</td>';
						$data .= '</tr>';
						$data .= '<tr height="20">';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Nit del Cliente</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nombre Cliente</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>DO</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Anticipo</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Documento del Anticipo</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Tipo Operaci&oacute;n</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>No. Pedido</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nit Proveedor</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Nombre Proveedor</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Detalle Gasto</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>No. de Factura Proveedor</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Fecha de Causaci&oacute;n de Factura Proveedor</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>No. Causaci&oacute;n</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Fecha de Pago al Proveedor</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Documento que Cancela Factura del Proveedor</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>No. Factura de Venta de Mario Londo&ntilde;o</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Fecha Factura de Venta de Mario Londo&ntilde;o</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Documento que Cancela Factura de Venta</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Nit del Facturar A</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Nombre del Facturar A</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Valor del Gasto de la Factura del Proveedor</font></b></td>';
						$data .= '</tr>';

						fwrite($fOp, $data);
				break;
			}

			#Trayendo comprobantes
			/*** Migrando logica de cargue de datos al reporte rotativo. ***/
			/*** Rango de años en los que debo buscar el reporte. ***/
			$nAnoI = ((substr($gDesde,0,4)-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (substr($gDesde,0,4)-1);
			$nAnoF = date('Y');

			$mTabDet = array(); //Array con nombre de las tablas temporales para los ajustes, anticipos y pagos a terceros en la tabla de detalle
			$mTabCue = array(); //Array con nombre de las tablas temporales para las cuentas por cobrar o por pagar de los comprobantes
			$mTabFac = array(); //Array con nombre de las tablas temporales para el encabezado de las facturas de venta

			$mCtoAnt  = array(); //Array con la marca de anticipos por cuenta-concepto
			$mCtoPCC  = array(); //Array con la marca de pcc por cuenta-concepto
			$mCuentas = array(); //Array con la marca de Cuentas por Cobrar o por Pagar

			/*** Matriz para almacenar las descripciones de los conceptos. ***/
			$mConCon = array();

			/*** Buscando las cuentas que sean por pagar o por cobrar ***/
			$qCuentas  = "SELECT *, ";
			$qCuentas .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) as pucidxxx ";
			$qCuentas .= "FROM $cAlfa.fpar0115 ";
			$qCuentas .= "WHERE ";
			$qCuentas .= "(pucdetxx = \"P\" OR pucdetxx = \"C\") AND ";
			$qCuentas .= "regestxx = \"ACTIVO\"";
			$xCuentas  = f_MySql("SELECT","",$qCuentas,$xConexion01,"");
			$cCuentas  = "";
			while($xRC = mysql_fetch_array($xCuentas)) {
				$cCuentas .= "\"{$xRC['pucidxxx']}\",";
				$mCuentas[count($mCuentas)] = $xRC['pucidxxx'];
			}

			$cCuentas = $cCauAut.substr($cCuentas,0,strlen($cCuentas)-1);

			/*** Buscando conceptos de causaciones automaticas PCC ***/
			$qCauAut  = "SELECT * ";
			$qCauAut .= "FROM $cAlfa.fpar0121 ";
			$qCauAut .= "WHERE ";
			$qCauAut .= "regestxx = \"ACTIVO\"";
			$xCauAut  = f_MySql("SELECT","",$qCauAut,$xConexion01,"");
			// echo "\n".$qCauAut."~".mysql_num_rows($xCauAut);

			$cCauAut = "";
			while($xRCA = mysql_fetch_array($xCauAut)) {
				$cCauAut .= "\"{$xRCA['pucidxxx']}~{$xRCA['ctoidxxx']}\",";
				$mCtoPCC[count($mCtoPCC)] = "{$xRCA['pucidxxx']}~{$xRCA['ctoidxxx']}";

				/*** Almaceno la informacion de la cuenta/concepto ***/
				$xRCA['ctodesxp'] = $xRCA['ctodesxx'];
				$mConCon[$xRCA['pucidxxx']."~".$xRCA['ctoidxxx']] = $xRCA;
			}

			/*** Buscando conceptos PCC y Anticipos ***/ 
			$qCauAnt  = "SELECT * ";
			$qCauAnt .= "FROM $cAlfa.fpar0119 ";
			$qCauAnt .= "WHERE ";
			$qCauAnt .= "(ctoantxx = \"SI\" OR ctopccxx = \"SI\") AND ";
			$qCauAnt .= "regestxx = \"ACTIVO\"";
			$xCauAnt  = f_MySql("SELECT","",$qCauAnt,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qCauAnt."~".mysql_num_rows($xCauAnt));
			$cCauAnt = "";
			while($xRCA = mysql_fetch_array($xCauAnt)) {

				$cCauAnt .= "\"{$xRCA['pucidxxx']}~{$xRCA['ctoidxxx']}\",";

				if ($xRCA['ctoantxx'] == "SI") {
					$mCtoAnt[count($mCtoAnt)] = "{$xRCA['pucidxxx']}~{$xRCA['ctoidxxx']}";
				}
				if ($xRCA['ctopccxx'] == "SI") {
					$mCtoPCC[count($mCtoPCC)] = "{$xRCA['pucidxxx']}~{$xRCA['ctoidxxx']}";
				}

				/*** Almaceno la informacion de la cuenta/concepto ***/
				$mConCon[$xRCA['pucidxxx']."~".$xRCA['ctoidxxx']] = $xRCA;
      }
      
      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionTM = fnConectarDBFondoRotativo();
      if($mReturnConexionTM[0] == "true"){
        $xConexionTM = $mReturnConexionTM[1];
      }else{
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnConexionTM);$nR++){
          $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
        }
      }

			$cCauAnt = $cCauAut.substr($cCauAnt,0,strlen($cCauAnt)-1);
			
			for($nPerAno=$nAnoI; $nPerAno<=$nAnoF; $nPerAno++) {
				/*** Creando y cargando tablas temporales de PCC y Anticipos. ***/
				/*** Creando y cargando tablas temporales de PCC y Anticipos. ***/
				$cFcod = "fcod".$nPerAno;

				/*** Random para Nombre de la Tabla ***/
				$cTabCar = mt_rand(100000, 999999);
				$cTabFac = "memffcod".$nPerAno.$cTabCar;

				$qNewTab = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcod";
				$xNewTab = mysql_query($qNewTab,$xConexionTM);

				$qMovDO  = "SELECT * ";
				$qMovDO .= "FROM $cAlfa.fcod$nPerAno ";
				$qMovDO .= "WHERE ";
				if($gDocNro != "") {
					$qMovDO .= "comcsccx = \"$gDocNro\" AND ";
					$qMovDO .= "comseqcx = \"$gDocSuf\" AND ";
				}
				$qMovDO .= "CONCAT(pucidxxx,\"~\",ctoidxxx) IN ($cCauAnt) AND "; //PCC Y ANTICPOS
				$qMovDO .= "comidxxx != \"F\"  AND ";
				// $qMovDO .= "( comfacxx = \"\" OR ( comfacxx != \"\" AND comfacxx LIKE '%-P%' )  ) AND ";
				$qMovDO .= "regestxx =  \"ACTIVO\" ";
				// echo "<br>".$qMovDO."~".mysql_num_rows($xMovDO)."<br><br>";
				
				$qInsert = "INSERT INTO $cAlfa.$cTabFac $qMovDO";
				// echo "\n".$qInsert."\n";
				$xInsert = mysql_query($qInsert,$xConexion01);

				$mTabDet[$nPerAno] = $cTabFac;
				/*** Fin Creando y cargando tablas temporales de PCC y Anticipos. ***/
				/*** Fin Creando y cargando tablas temporales de PCC y Anticipos. ***/

				/*** Creando y cargando tablas temporales de Cuentas por Pagar o Por Cobrar. ***/
				/*** Creando y cargando tablas temporales de Cuentas por Pagar o Por Cobrar. ***/
				$cFcod = "fcod".$nPerAno;

				/*** Random para Nombre de la Tabla ***/
				$cTabCar = mt_rand(100000, 999999);
				$cTabFac = "memfcxcp".$nPerAno.$cTabCar;

				$qNewTab = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcod";
				$xNewTab = mysql_query($qNewTab,$xConexionTM);

				$qMovDO  = "SELECT * ";
				$qMovDO .= "FROM $cAlfa.fcod$nPerAno ";
				$qMovDO .= "WHERE ";
				$qMovDO .= "pucidxxx IN ($cCuentas) AND "; //Cuentas por Cobrar o por Pagar.
				$qMovDO .= "regestxx =  \"ACTIVO\" ";
				// echo "<br>".$qMovDO."~".mysql_num_rows($xMovDO)."<br><br>";
				
				$qInsert = "INSERT INTO $cAlfa.$cTabFac $qMovDO";
				// echo "\n".$qInsert."\n";
				$xInsert = mysql_query($qInsert,$xConexion01);

				$mTabCue[$nPerAno] = $cTabFac;
				/*** Fin Creando y cargando tablas temporales de Cuentas por Pagar o Por Cobrar. ***/
				/*** Fin Creando y cargando tablas temporales de Cuentas por Pagar o Por Cobrar. ***/

				/*** Creando y cargando tablas temporales de encabezado de las facturas de venta. ***/
				/*** Creando y cargando tablas temporales de encabezado de las facturas de venta. ***/
				$cFcoc = "fcoc".$nPerAno;

				/*** Random para Nombre de la Tabla ***/
				$cTabCar = mt_rand(100000, 999999);
				$cTabFac = "memfcocc".$nPerAno.$cTabCar;

				$qNewTab = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";
				$xNewTab = mysql_query($qNewTab,$xConexionTM);

				$qMovDO  = "SELECT * ";
				$qMovDO .= "FROM $cAlfa.fcoc$nPerAno ";
				$qMovDO .= "WHERE ";
				$qMovDO .= "comidxxx = \"F\" AND "; //Facturas de Venta
				$qMovDO .= "regestxx = \"ACTIVO\" ";
				// echo "<br>".$qMovDO."~".mysql_num_rows($xMovDO)."<br><br>";
				
				$qInsert = "INSERT INTO $cAlfa.$cTabFac $qMovDO";
				// echo "\n".$qInsert."\n";
				$xInsert = mysql_query($qInsert,$xConexion01);

				$mTabFac[$nPerAno] = $cTabFac;
				/*** Fin Creando y cargando tablas temporales de Cuentas por Pagar o Por Cobrar. ***/
				/*** Fin Creando y cargando tablas temporales de Cuentas por Pagar o Por Cobrar. ***/

			}
			#Fin Trayendo comprobantes

			$qDatDoi  = "SELECT ";
			$qDatDoi .= "$cAlfa.sys00121.sucidxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.docidxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.docsufxx, ";
			$qDatDoi .= "$cAlfa.sys00121.comidxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.comcodxx, ";
			$qDatDoi .= "$cAlfa.sys00121.ccoidxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.pucidxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.succomxx, ";
			$qDatDoi .= "$cAlfa.sys00121.doctipxx, ";
			$qDatDoi .= "$cAlfa.sys00121.docpedxx, ";
			$qDatDoi .= "$cAlfa.sys00121.cliidxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.diridxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.docfacxx, ";
			$qDatDoi .= "$cAlfa.sys00121.docffecx, ";
			$qDatDoi .= "$cAlfa.sys00121.regfcrex, ";
			$qDatDoi .= "$cAlfa.sys00121.docusrce, ";
			$qDatDoi .= "$cAlfa.sys00121.docfecce, ";
			$qDatDoi .= "$cAlfa.sys00121.regestxx ";
			$qDatDoi .= "FROM $cAlfa.sys00121 ";
			$qDatDoi .= "WHERE ";
			if($gDocNro!=""){
				$qDatDoi .= "$cAlfa.sys00121.sucidxxx = \"$gSucId\" AND ";
				$qDatDoi .= "$cAlfa.sys00121.docidxxx = \"$gDocNro\" AND ";
				$qDatDoi .= "$cAlfa.sys00121.docsufxx = \"$gDocSuf\" AND ";
			}
			if($gTerId!=""){
				$qDatDoi .= "$cAlfa.sys00121.cliidxxx = \"$gTerId\" AND ";
			}
			$qDatDoi .= "$cAlfa.sys00121.regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
		
			switch ($gEstado){
				case "TODOS":
					$qDatDoi .= "$cAlfa.sys00121.regestxx IN (\"ACTIVO\",\"FACTURADO\") ";
				break;
				case "FACTURADO":
					$qDatDoi .= "$cAlfa.sys00121.regestxx = \"FACTURADO\" ";
				break;
				case "ACTIVO":
					$qDatDoi .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" ";
				break;
			}

			$qDatDoi .= "ORDER BY $cAlfa.sys00121.regfcrex";
			$xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qDatDoi."~".mysql_num_rows($xDatDoi));
			// echo "\n".$qDatDoi."~".mysql_num_rows($xDatDoi);
			// die();

			/**
			 * Matriz para acumular el nombre de los proveedores y clientes y no consultarlo siempre por cada pago o DO.
			 * @var array 
			 */
			$mTerceros = array();

			## Recorro los Do's
			$nCanReg = 0; ## Contador de registros
			
			while ($xRDD = mysql_fetch_array($xDatDoi)) {

				/*** Busco el nombre del cliente. ***/
				if(!isset($mTerceros[$xRDD['cliidxxx']])){
					$qCliNom  = "SELECT ";
					$qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,' ',CLINOM2X,' ',CLIAPE1X,' ',CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,' ',CLINOM2X,' ',CLIAPE1X,' ',CLIAPE2X)), CLINOMXX) AS clinomxx ";
					$qCliNom .= "FROM $cAlfa.SIAI0150 ";
					$qCliNom .= "WHERE ";
					$qCliNom .= "CLIIDXXX = \"{$xRDD['cliidxxx']}\" LIMIT 0,1";
					$xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
					// echo "<br>".$qCliNom."~".mysql_num_rows($xCliNom);
					if (mysql_num_rows($xCliNom) > 0) {
						$vCliNom = mysql_fetch_array($xCliNom);
						$mTerceros[$xRDD['cliidxxx']] = $vCliNom['clinomxx'];
					} else {
						$mTerceros[$xRDD['cliidxxx']] = "CLIENTE SIN NOMBRE";
					}
				}
				
				$xRDD['clinomxx'] = $mTerceros[$xRDD['cliidxxx']];
				/*** Fin Busco el nombre del cliente. ***/

				$mPCC = array();
				$mAnticipos = array();
				$nAnticipos = 0;
				$cAnticipos = "";

				$nAno01I = ((substr($xRDD['regfcrex'],0,4)-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (substr($xRDD['regfcrex'],0,4)-1);
				$nAno01F = date('Y');

				/*** Buscando Anticipos y PCC ***/
				for($nAnoBus = $nAno01I; $nAnoBus <= $nAno01F; $nAnoBus++){
					$cTabFac = $mTabDet[$nAnoBus];

					$qMovDO  = "SELECT ";
					$qMovDO .= "$cAlfa.$cTabFac.comidxxx, ";
					$qMovDO .= "$cAlfa.$cTabFac.comcodxx, ";
					$qMovDO .= "$cAlfa.$cTabFac.comcscxx, ";
					$qMovDO .= "$cAlfa.$cTabFac.comcsc2x, ";
					$qMovDO .= "$cAlfa.$cTabFac.comseqxx, ";
					$qMovDO .= "$cAlfa.$cTabFac.comcsc3x, ";
					$qMovDO .= "$cAlfa.$cTabFac.comfecxx, ";
					$qMovDO .= "$cAlfa.$cTabFac.ccoidxxx, ";
					$qMovDO .= "$cAlfa.$cTabFac.pucidxxx, ";
					$qMovDO .= "$cAlfa.$cTabFac.comfacxx, ";
					$qMovDO .= "$cAlfa.$cTabFac.ctoidxxx, ";
					$qMovDO .= "$cAlfa.$cTabFac.teridxxx, ";
					$qMovDO .= "$cAlfa.$cTabFac.terid2xx, ";
					$qMovDO .= "$cAlfa.$cTabFac.sucidxxx, ";
					$qMovDO .= "$cAlfa.$cTabFac.docidxxx, ";
					$qMovDO .= "$cAlfa.$cTabFac.docsufxx, ";
					$qMovDO .= "$cAlfa.$cTabFac.commovxx, ";
					$qMovDO .= "IF($cAlfa.$cTabFac.commovxx = \"C\",($cAlfa.$cTabFac.comvlrxx * -1), $cAlfa.$cTabFac.comvlrxx) AS comvlrxx  ";
					$qMovDO .= "FROM $cAlfa.$cTabFac ";
					$qMovDO .= "WHERE ";
					$qMovDO .= "$cAlfa.$cTabFac.comcsccx =  \"{$xRDD['docidxxx']}\" AND ";
					$qMovDO .= "$cAlfa.$cTabFac.comseqcx =  \"{$xRDD['docsufxx']}\" AND ";
					$qMovDO .= "$cAlfa.$cTabFac.regestxx =  \"ACTIVO\" ";
					$xMovDO  = mysql_query($qMovDO,$xConexion01);
					//echo "<br>fcod".$nAnoBus."~".$qMovDO."~".mysql_num_rows($xMovDO)."<br><br>";
					while ($xRMD = mysql_fetch_array($xMovDO)) {

						/*** Acumulando los Anticipos por Do. ***/
						if (in_array("{$xRMD['pucidxxx']}~{$xRMD['ctoidxxx']}",$mCtoAnt)) {
							//echo "ANT ~~> ".$xRMD['comidxxx']."~".$xRMD['comcodxx']."~".$xRMD['comcscxx']."~".$xRMD['comseqxx']." ~~ ".$xRMD['commovxx']." ~~ ".$xRMD['anticipo']."<br>";
							
							$nSw_Incluir = 0;
							if ($xRMD['sucidxxx'] != "" && $xRMD['docidxxx'] != "" && $xRMD['docsufxx'] != "") {
								//si tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
								if ($xRMD['sucidxxx'] == $xRDD['sucidxxx'] && $xRMD['docidxxx'] == $xRDD['docidxxx'] && $xRMD['docsufxx'] == $xRDD['docsufxx']) {
									$nSw_Incluir = 1;
								}
							} else {
								//Comparando por el centro de costo
								if ($xRMD['ccoidxxx'] == $xRDD['ccoidxxx']) {
									$nSw_Incluir = 1;
								}
							}

							if ($nSw_Incluir == 1) {
								$nAnticipos += ($xRMD['comvlrxx']*-1);
								$cAnticipos .= $xRMD['comcscxx'].", ";
							}
						}
						/*** Fin Acumulando los Anticipos por Do. ***/

						/*** Buscando los PCC por DO. ***/
						if (in_array("{$xRMD['pucidxxx']}~{$xRMD['ctoidxxx']}",$mCtoPCC)) {
							//echo "PCC ~~> ".$xRMD['comidxxx']."~".$xRMD['comcodxx']."~".$xRMD['comcscxx']."~".$xRMD['comseqxx']." ~~ ".$xRMD['commovxx']." ~~ ".$xRMD['pagoster']."<br>";
							
							$nSw_Incluir = 0;
							if ($xRMD['sucidxxx'] != "" && $xRMD['docidxxx'] != "" && $xRMD['docsufxx'] != "") {
								//si tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
								if ($xRMD['sucidxxx'] == $xRDD['sucidxxx'] && $xRMD['docidxxx'] == $xRDD['docidxxx'] && $xRMD['docsufxx'] == $xRDD['docsufxx']) {
									$nSw_Incluir = 1;
								}
							} else {
								//Comparando por el centro de costo
								if ($xRMD['ccoidxxx'] == $xRDD['ccoidxxx']) {
									$nSw_Incluir = 1;
								}
							}

							if ($nSw_Incluir == 1) {

								/*** Busco el nombre del proveedor. ***/
								if(!isset($mTerceros[$xRMD['terid2xx']])){
									$qProNom  = "SELECT ";
									$qProNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
									$qProNom .= "FROM $cAlfa.SIAI0150 ";
									$qProNom .= "WHERE ";
									$qProNom .= "CLIIDXXX = \"{$xRMD['terid2xx']}\" LIMIT 0,1";
									$xProNom = f_MySql("SELECT","",$qProNom,$xConexion01,"");
									// echo "<br>".$qProNom."~".mysql_num_rows($xProNom);
									if (mysql_num_rows($xProNom) > 0) {
										$vProNom = mysql_fetch_array($xProNom);
										$mTerceros[$xRMD['terid2xx']] = $vProNom['clinomxx'];
									} else {
										$mTerceros[$xRMD['terid2xx']] = "PROVEEDOR SIN NOMBRE";
									}
								}

								$nAno01I = ((substr($xDD['regfcrex'],0,4)-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (substr($xDD['regfcrex'],0,4)-1);
								$nAno01F = date('Y');
								
								/*** Busco datos del pago a proveedor ***/
								for($cAnoP = $nAno01F; $cAnoP >= $nAno01I; $cAnoP--){

									$cTabFac = $mTabCue[$cAnoP];

									$qCauAut = "SELECT ";
									$qCauAut .= "comidxxx, ";
									$qCauAut .= "comcodxx, ";
									$qCauAut .= "comcscxx, ";
									$qCauAut .= "comcsc2x, ";
									$qCauAut .= "comfecxx, ";
									$qCauAut .= "regfcrex ";
									$qCauAut .= "FROM $cAlfa.$cTabFac ";
									$qCauAut .= "WHERE ";
									$qCauAut .= "comidcxx = \"{$xRMD['comidxxx']}\" AND ";
									$qCauAut .= "comcodcx = \"{$xRMD['comcodxx']}\" AND ";
									$qCauAut .= "comcsccx = \"{$xRMD['comcscxx']}\" AND "; 
									$qCauAut .= "terid2xx = \"{$xRMD['terid2xx']}\" LIMIT 0,1 "; 
									$xCauAut = mysql_query($qCauAut, $xConexion01);
									// echo "<br>".$qCauAut."~".mysql_num_rows($xCauAut);
									if(mysql_num_rows($xCauAut) > 0){
										
										$vCauAut = mysql_fetch_array($xCauAut);
										$xRMD['comfecdc'] = $vCauAut['comfecxx']; // Fecha documento cruce
										$xRMD['comcscdc'] = $vCauAut['comidxxx']."-".$vCauAut['comcodxx']."-".$vCauAut['comcscxx']."-".$vCauAut['comcsc2x']; // No. doc. cruce
										break;
									}
								}

								/*** Busco datos de la factura del pago ***/
								if(trim($xRMD['comfacxx']) != ""){
									$vComFac = explode("-", $xRMD['comfacxx']);
									$xRMD['comfacxx'] = $vComFac[2]; // No. Factura de venta

									for($cAnoP = $nAno01F; $cAnoP >= $nAno01I; $cAnoP--){

										$cTabFac = $mTabFac[$cAnoP];

										$qComFec  = "SELECT ";
										$qComFec .= "comfecxx, ";
										$qComFec .= "regfcrex, ";
										$qComFec .= "terid2xx ";
										$qComFec .= "FROM $cAlfa.$cTabFac ";
										$qComFec .= "WHERE ";
										$qComFec .= "comidxxx = \"{$vComFac[0]}\" AND ";
										$qComFec .= "comcodxx = \"{$vComFac[1]}\" AND ";
										$qComFec .= "comcscxx = \"{$vComFac[2]}\" AND "; 
										$qComFec .= "comcsc2x = \"{$vComFac[3]}\" LIMIT 0,1 "; 
										$xComFec = mysql_query($qComFec, $xConexion01);
										// echo "<br>".$qComFec."~".mysql_num_rows($xComFec);

										if(mysql_num_rows($xComFec) > 0){
											$vComFec = mysql_fetch_array($xComFec);

											$xRMD['fecfacxx'] = $vComFec['comfecxx']; // Fecha de factura
											$xRMD['nitfacax'] = $vComFec['terid2xx']; // Nit facturar a:

											/*** Busco el nombre del facturar a. ***/
											if(!isset($mTerceros[$xRMD['nitfacax']])){
												$qCliNom  = "SELECT ";
												$qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
												$qCliNom .= "FROM $cAlfa.SIAI0150 ";
												$qCliNom .= "WHERE ";
												$qCliNom .= "CLIIDXXX = \"{$xRMD['nitfacax']}\" LIMIT 0,1";
												$xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
												// echo "<br>".$qCliNom."~".mysql_num_rows($xCliNom);
												if (mysql_num_rows($xCliNom) > 0) {
													$vCliNom = mysql_fetch_array($xCliNom);
													$mTerceros[$xRMD['nitfacax']] = $vCliNom['clinomxx'];
												} else {
													$mTerceros[$xRMD['nitfacax']] = "CLIENTE SIN NOMBRE";
												}
											}

											$xRMD['nomfacax'] = $mTerceros[$xRMD['nitfacax']];

											/*** Busco si la facutra ya fue cruzada con un ajuste, un recibo de caja o una nota credito. ***/
											for($cAnoPAux = $nAno01F; $cAnoPAux >= $cAnoP; $cAnoPAux--){
												
												$cTabFac = $mTabCue[$cAnoPAux];

												$qDocFac = "SELECT ";
												$qDocFac .= "comidxxx, ";
												$qDocFac .= "comcodxx, ";
												$qDocFac .= "comcscxx, ";
												$qDocFac .= "comcsc2x ";
												$qDocFac .= "FROM $cAlfa.$cTabFac ";
												$qDocFac .= "WHERE ";
												$qDocFac .= "comidxxx != \"F\" AND ";
												$qDocFac .= "comidcxx  = \"{$vComFac[0]}\" AND ";
												$qDocFac .= "comcodcx  = \"{$vComFac[1]}\" AND ";
												$qDocFac .= "comcsccx  = \"{$vComFac[2]}\" AND "; 
												$qDocFac .= "terid2xx  = \"{$vComFec['terid2xx']}\" LIMIT 0,1 "; 
												$xDocFac = mysql_query($qDocFac, $xConexion01);
												// echo "<br>".$qDocFac."~".mysql_num_rows($xDocFac);
	
												if(mysql_num_rows($xDocFac) > 0){
													while($xRDF = mysql_fetch_array($xDocFac)){
														if($xRDF['comidxxx'] == "R" || $xRDF['comidxxx'] == "C" || $xRDF['comidxxx'] == "L"){
															$xRMD['docfacxx'] = $xRDF['comidxxx']."-".$xRDF['comcodxx']."-".$xRDF['comcscxx']."-".$xRDF['comcsc2x']; # Doc. que cancela Factura
														}
													}
													break;
												}
											}
											break;
										}
									}
								}
								
								$xRMD['PRONOMXX'] = $mTerceros[$xRMD['terid2xx']];
								$xRMD['comdesxx'] = $mConCon[$xRMD['pucidxxx']."~".$xRMD['ctoidxxx']]['ctodesx'.strtolower($xRMD['comidxxx'])]; // Detalle gasto
								$xRMD['comcsc2x'] = ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $xRMD['comcsc3x'] != '') ? $xRMD['comcsc3x'] : $xRMD['comcsc2x'];
								$mPCC[count($mPCC)] = $xRMD;
							}
						}
						/*** Fin Buscando los PCC por DO. ***/

					} ## while ($xRMD = mysql_fetch_array($xMovDO)) {##
				}
				/*** Fin Buscando Anticipos y PCC ***/

				## No. documento de pedido del DO
				$cPedido = "";
				switch ($xRDD['doctipxx']){
					case "TRANSITO":
					case "IMPORTACION":
						##Traigo Datos de la SIAI0200 DATOS DEL DO ##
						$qDoiDat  = "SELECT DOIPEDXX ";
						$qDoiDat .= "FROM $cAlfa.SIAI0200 ";
						$qDoiDat .= "WHERE ";
						$qDoiDat .= "DOIIDXXX = \"{$xRDD['docidxxx']}\" AND ";
						$qDoiDat .= "DOISFIDX = \"{$xRDD['docsufxx']}\" AND ";
						$qDoiDat .= "ADMIDXXX = \"{$xRDD['sucidxxx']}\" ";
						$xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
						$nFilDoi  = mysql_num_rows($xDoiDat);
						if ($nFilDoi > 0) {
							$vDoiDat  = mysql_fetch_array($xDoiDat);
						}
						## Cargo Variables de Pedido ##
						$cPedido = $vDoiDat['DOIPEDXX'];
					break;
					case "EXPORTACION":
						## Consulto Datos de Do en Exportaciones tabla siae0199 ##
						$qDexDat  = "SELECT dexpedxx ";
						$qDexDat .= "FROM $cAlfa.siae0199 ";
						$qDexDat .= "WHERE ";
						$qDexDat .= "$cAlfa.siae0199.dexidxxx = \"{$xRDD['docidxxx']}\" AND ";
						$qDexDat .= "$cAlfa.siae0199.admidxxx = \"{$xRDD['sucidxxx']}\" ";
						$xDexDat  = f_MySql("SELECT","",$qDexDat,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qDexDat);
						$nFilDex  = mysql_num_rows($xDexDat);
						if ($nFilDex > 0) {
							$vDexDat = mysql_fetch_array($xDexDat);
						}
						## Fin Consulto Datos de Do en Exportaciones tabla siae0199 ##
						## Cargo Variable Pedido ##
						$cPedido = $vDexDat['dexpedxx'];
					break;
					case "OTROS":
					break;
				}//switch (){

				$nCanReg++;

				switch ($cTipo) {
					case 1:  // PINTA POR PANTALLA //
						if ($_SERVER["SERVER_PORT"] != "") {
							$zColorPro = "#000000"; ?>
							<tr bgcolor = "white" height="20"   style="padding-left:4px;padding-right:4px">
								<td class="letra7" align="left" 	style = "color:<?php echo $zColorPro ?>"><?php echo $xRDD['cliidxxx'] ?></td>
								<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xRDD['clinomxx'] ?></td>
								<td class="letra7" align="left" 	style = "color:<?php echo $zColorPro ?>"><?php echo $xRDD['docidxxx'] ?></td>
								<td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo number_format($nAnticipos,0,',','.')  ?></td>
								<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo substr($cAnticipos,0,-2); ?></td>
								<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $xRDD['doctipxx']  ?></td>
								<td class="letra7" align="left" 	style = "color:<?php echo $zColorPro ?>"><?php echo $cPedido ?></td>
								
								<!-- ROMPIMIENTO -->
								<?php
								for($i=0; $i<count($mPCC); $i++){  
									if($i == 0){ ?>
										<td class="letra7" align="left" 	style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['terid2xx'] ?></td>
										<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['PRONOMXX'] ?></td>
										<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comdesxx'] ?></td> 
										<td class="letra7" align="left" 	style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comcscxx'] ?></td>  
										<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comfecxx'] ?></td>  
										<td class="letra7" align="left" 	style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comcsc2x'] ?></td>
										<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comfecdc'] ?></td>   
										<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comcscdc'] ?></td>  
										<td class="letra7" align="left" 	style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comfacxx'] ?></td>
										<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['fecfacxx'] ?></td>
										<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['docfacxx'] ?></td>									
										<td class="letra7" align="left" 	style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['nitfacax'] ?></td>
										<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['nomfacax'] ?></td>
										<td class="letra7" align="right" 	style = "color:<?php echo $zColorPro ?>"><?php echo number_format($mPCC[$i]['comvlrxx'],0,',','.') ?></td>

									<?php }else{  
										?>
										<tr>
											<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>">&nbsp;</td>
											<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>">&nbsp;</td>
											<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>">&nbsp;</td>
											<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>">&nbsp;</td>
											<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>">&nbsp;</td>
											<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>">&nbsp;</td>
											<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>">&nbsp;</td> 
											<td class="letra7" align="left" 	style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['terid2xx'] ?></td>
											<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['PRONOMXX'] ?></td>
											<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comdesxx'] ?></td>
											<td class="letra7" align="left" 	style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comcscxx'] ?></td> 
											<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comfecxx'] ?></td>
											<td class="letra7" align="left" 	style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comcsc2x'] ?></td>
											<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comfecdc'] ?></td>  
											<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comcscdc'] ?></td> 
											<td class="letra7" align="left" 	style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['comfacxx'] ?></td>  
											<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['fecfacxx'] ?></td>
											<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['docfacxx'] ?></td>
											<td class="letra7" align="left" 	style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['nitfacax'] ?></td>  
											<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mPCC[$i]['nomfacax'] ?></td>  
											<td class="letra7" align="right" 	style = "color:<?php echo $zColorPro ?>"><?php echo number_format($mPCC[$i]['comvlrxx'],0,',','.') ?></td> 
										<tr>
									<?php }
								} ?>
							</tr>
							<?php 
						}
					break;
					case 2: // PINTA POR EXCEL //
							$zColorPro = "#000000";
							$nValor01 = ($nAnticipos != "") ? number_format($nAnticipos,0,',','.') : ""; // Valor Anticipos
							$nValor02 = ($cAnticipos != "") ? substr($cAnticipos,0,-2) : "";  // Numero de anticipos
							
							$data  = '<tr bgcolor = "white" height="20"   style="padding-left:4px;padding-right:4px">';
								$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$xRDD['cliidxxx'].'</td>';
								$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$xRDD['clinomxx'].'</td>';
								$data .= '<td class="letra7" align="left" 	style = "color:'.$zColorPro.'">'.$xRDD['docidxxx'].'</td>';
								$data .= '<td class="letra7" align="rigth" 	style = "color:'.$zColorPro.'">'.$nValor01.'</td>';
								$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$nValor02.'</td>';
								$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$xRDD['doctipxx'].'</td>'; 
								$data .= '<td class="letra7" align="left" 	style = "color:'.$zColorPro.'">'.$cPedido.'</td>';

								// ROMPIMIENTO
								for($i=0; $i<count($mPCC); $i++){  
									if($i == 0){
										$data .= '<td class="letra7" align="left" 	style = "color:'.$zColorPro.'">'.$mPCC[$i]['terid2xx'].'</td>';
										$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mPCC[$i]['PRONOMXX'].'</td>';
										$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mPCC[$i]['comdesxx'].'</td>';
										$data .= '<td class="letra7" align="left" 	style = "color:'.$zColorPro.'">'.$mPCC[$i]['comcscxx'].'</td>';
										$data .= '<td class="letra7" align="center" style = "mso-number-format:\'yyyy-mm-dd\';color:'.$zColorPro.'">'.$mPCC[$i]['comfecxx'].'</td>';
										$data .= '<td class="letra7" align="left" 	style = "color:'.$zColorPro.'">'.$mPCC[$i]['comcsc2x'].'</td>';
										$data .= '<td class="letra7" align="center" style = "mso-number-format:\'yyyy-mm-dd\';color:'.$zColorPro.'">'.$mPCC[$i]['comfecdc'].'</td>';
										$data .= '<td class="letra7" align="left" 	style = "color:'.$zColorPro.'">'.$mPCC[$i]['comcscdc'].'</td>';
										$data .= '<td class="letra7" align="left" 	style = "color:'.$zColorPro.'">'.$mPCC[$i]['comfacxx'].'</td>';
										$data .= '<td class="letra7" align="center" style = "mso-number-format:\'yyyy-mm-dd\';color:'.$zColorPro.'">'.$mPCC[$i]['fecfacxx'].'</td>'; 
										$data .= '<td class="letra7" align="left" 	style = "color:'.$zColorPro.'">'.$mPCC[$i]['docfacxx'].'</td>';
										$data .= '<td class="letra7" align="left" 	style = "color:'.$zColorPro.'">'.$mPCC[$i]['nitfacax'].'</td>';
										$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mPCC[$i]['nomfacax'].'</td>';
										$data .= '<td class="letra7" align="right" 	style = "color:'.$zColorPro.'">'.number_format($mPCC[$i]['comvlrxx'],0,',','.').'</td>';

									}else{
										$data .= '<tr>';
											$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">&nbsp;</td>';
											$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">&nbsp;</td>';
											$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">&nbsp;</td>';
											$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">&nbsp;</td>';
											$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">&nbsp;</td>';
											$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">&nbsp;</td>';
											$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">&nbsp;</td>';
											$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mPCC[$i]['terid2xx'].'</td>';
											$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mPCC[$i]['PRONOMXX'] .'</td>';
											$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mPCC[$i]['comdesxx'].'</td>';
											$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mPCC[$i]['comcscxx'].'</td>';
											$data .= '<td class="letra7" align="center" style = "mso-number-format:\'yyyy-mm-dd\';color:'.$zColorPro.'">'.$mPCC[$i]['comfecxx'].'</td>';
											$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mPCC[$i]['comcsc2x'].'</td>';
											$data .= '<td class="letra7" align="center" style = "mso-number-format:\'yyyy-mm-dd\';color:'.$zColorPro.'">'.$mPCC[$i]['comfecdc'].'</td>';
											$data .= '<td class="letra7" align="left" 	style = "color:'.$zColorPro.'">'.$mPCC[$i]['comcscdc'].'</td>';
											$data .= '<td class="letra7" align="left" 	style = "color:'.$zColorPro.'">'.$mPCC[$i]['comfacxx'].'</td>';
											$data .= '<td class="letra7" align="center" style = "mso-number-format:\'yyyy-mm-dd\';color:'.$zColorPro.'">'.$mPCC[$i]['fecfacxx'].'</td>';
											$data .= '<td class="letra7" align="left" 	style = "color:'.$zColorPro.'">'.$mPCC[$i]['docfacxx'].'</td>';
											$data .= '<td class="letra7" align="left" 	style = "color:'.$zColorPro.'">'.$mPCC[$i]['nitfacax'].'</td>';
											$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mPCC[$i]['nomfacax'].'</td>';
											$data .= '<td class="letra7" align="right" 	style = "color:'.$zColorPro.'">'.number_format($mPCC[$i]['comvlrxx'],0,',','.').'</td>';

										$data .= '</tr>';	
									}
								}
							$data .= '</tr>';

							fwrite($fOp, $data);
					break;
				}//Fin Switch
			} ## while ($xDD = mysql_fetch_array($xDatDoi)) { ## Recorro Do's

			switch ($cTipo) {
				case 1:
						// PINTA POR PANTALLA// ?>
									</table>
								</form>
							</body>
						</html>
						<script type="text/javascript">
							document.forms['frgrm']['nCanReg'].value = "<?php echo $nCanReg ?>";
						</script>
					<?php 
				break;
				case 2:
						/*** Colspan una ultima fila ***/
						$nExcCsp += 21;
						$data  = '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
							$data .= '<td class="name" colspan="'.$nExcCsp.'" align="left">';
									$data .= '<font size="3">';
										$data   .= '<b>TOTAL DOs EN ESTA CONSULTA ['.$nCanReg.']<br>';
									$data .= '</font>';
							$data .= '</td>';
						$data .= '</tr>';
					$data .= '</table>';
					
					fwrite($fOp, $data);
					fclose($fOp);
						
					if (file_exists($cFile)) {

						if ($data == "") {
							$data = "\n(0) REGISTROS!\n";
						}
            // Obtener la ruta absoluta del archivo
            $cAbsolutePath = realpath($cFile);
            $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

						chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
						$cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFile);

            if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
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
              }else{
                $cNomArc = $cNomFile;
              }
            }
					}else {
						$nSwitch = 1;
						if ($_SERVER["SERVER_PORT"] != "") {
							f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
						} else {
							$cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
						}
					}
				break;
			}//Fin Switch
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
  
  /**
   * Metodo que realiza la conexion
   */
  function fnConectarDBFondoRotativo(){

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
      $mReturn[0] = "true";
      $mReturn[1] = $xConexion99;
    }else{
      $mReturn[0] = "false";
    }
    return $mReturn;
  }##function fnConectarDBFondoRotativo(){##
	
?>

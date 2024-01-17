<?php
	/**
	 * GRABA Transmision para Sistema SIIGO NUBE - MIRCANA.
	 * 
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package opencomex
	 */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

	set_time_limit(0);
  ini_set("memory_limit","1024M");
	date_default_timezone_set('America/Bogota');

	/**
	 * Cantidad de Registros para reiniciar conexion.
	 */
	define("_NUMREG_",100);

	include("../../../../libs/php/utility.php");
	require_once($OPENINIT['pathdr'].'/opencomex/class/spout-2.7.3/src/Spout/Autoloader/autoload.php');
  
  use Box\Spout\Writer\WriterFactory;
  use Box\Spout\Common\Type;
  use Box\Spout\Writer\Style\Color;
  use Box\Spout\Writer\Style\Border;
  use Box\Spout\Writer\Style\StyleBuilder;
  use Box\Spout\Writer\Style\BorderBuilder;
?>
<html>
	<head>
		<title>Archivo Excel para Transmision a Sistema SIIGO</title>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
	</head>
	<body>
		<?php
			/**
			 * Array para buscar y reemplazar los caracteres especiales.
			 */
			$vBuscar = array(chr(13),chr(10),chr(27),chr(9),chr(126));
			$vReempl = array(" "," "," "," ","");

			/**
			 * Variable para el control de errores.
			 * 
			 * @var integer
			 */
			$nSwitch = 0;

			/**
			 * Variable para controlar el reinicio de conexion.
			 * 
			 * @var integer
			 */
			$nCanReg = 0;

			/**
			 * Variable para controlar la cantidad de registros a pintar en un excel.
			 * 
			 * @var integer
			 */
			$nRegExcel = 500;

			/**
			 * Variable para guardar la cantidad de registros por comprobante.
			 * 
			 * @var integer
			 */
			$nCantRegComp = 0;

			/**
			 * Buscando la informacion de la tabla fpar0117 COMPORBANTES.
			 */
			$qFpar117  = "SELECT ";
			$qFpar117 .= "$cAlfa.fpar0117.comidxxx, ";
			$qFpar117 .= "$cAlfa.fpar0117.comcodxx, ";
			$qFpar117 .= "$cAlfa.fpar0117.comids1x, ";
			$qFpar117 .= "$cAlfa.fpar0117.comcods1 ";
			$qFpar117 .= "FROM $cAlfa.fpar0117";
			$xFpar117  = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
			// echo $qFpar117."~".mysql_num_rows($xFpar117)."<br><br>";
			$mComprobantes = array();
			while($xR117 = mysql_fetch_array($xFpar117)) {
				$mComprobantes["{$xR117['comidxxx']}~{$xR117['comcodxx']}"]['comids1x'] = $xR117['comids1x'];
				$mComprobantes["{$xR117['comidxxx']}~{$xR117['comcodxx']}"]['comcods1'] = $xR117['comcods1'];
			}

			/**
			 * Buscando la informacion de la tabla fpar0115  DETALLES DE LA CTA PUC.
			 */
			$qFpar115  = "SELECT ";
			$qFpar115 .= "CONCAT(pucgruxx, pucctaxx, pucsctax, pucauxxx, pucsauxx) AS pucid36x, ";
			$qFpar115 .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,IF($cAlfa.fpar0115.pucauxxx = \"00\" AND $cAlfa.fpar0115.pucsauxx = \"00\",\"\",$cAlfa.fpar0115.pucauxxx),IF($cAlfa.fpar0115.pucsauxx != \"00\",$cAlfa.fpar0115.pucsauxx,\"\")) AS pucidxxx, ";
			$qFpar115 .= "$cAlfa.fpar0115.pucdetxx, ";
			$qFpar115 .= "$cAlfa.fpar0115.puc10dig ";
			$qFpar115 .= "FROM $cAlfa.fpar0115 ";
			$xFpar115 = f_MySql("SELECT","",$qFpar115,$xConexion01,"");
			// echo $qFpar115."~".mysql_num_rows($xFpar115)."<br><br>";
			$mCuenta = array();
			while($xR115 = mysql_fetch_array($xFpar115)) {
				$mCuenta["{$xR115['pucid36x']}"]['pucdetxx'] = $xR115['pucdetxx'];
				$mCuenta["{$xR115['pucid36x']}"]['puc10dig'] = $xR115['puc10dig'];
			}

			// verifica que los años de incio y fin sean iguales.
			if (substr($dDesde,0,4) == substr($dHasta,0,4)) { 
				$cAno = substr($dDesde,0,4);
				$dFecIni = $dDesde;
				$dFecHas = $dHasta;
			} else {
				$nSwitch = 1;
				f_Mensaje(__FILE__,__LINE__,"Error al Generar Archivo de Transmision SIIGO NUBE, Verifique que el Anio de las Fechas Desde y Hasta sea el Mismo");
			}

			if ($nSwitch == 0) {
				// Consulta principal para generar el archivo de transmision
				$cAno = substr($dDesde,0,4);
				$qCocDat  = "SELECT ";
				$qCocDat .= "$cAlfa.fcod$cAno.comidxxx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comcodxx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comcscxx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comcsc2x, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comseqxx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.teridxxx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.terid2xx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comfecxx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.pucidxxx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comvlrxx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.regestxx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comvlr01, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comfecve, ";
				$qCocDat .= "$cAlfa.fcod$cAno.commovxx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comidcxx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comcodcx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comcsccx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.ccoidxxx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.sccidxxx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comctocx, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comidc2x, ";
				$qCocDat .= "$cAlfa.fcod$cAno.comobsxx, ";
				$qCocDat .= "$cAlfa.fcoc$cAno.teridxxx AS teridcxx, ";
				$qCocDat .= "$cAlfa.fcoc$cAno.terid2xx AS terid2cx, ";
				$qCocDat .= "$cAlfa.fcoc$cAno.comfecxx AS comfeccx, ";
				$qCocDat .= "$cAlfa.fcoc$cAno.comvlrxx AS comvlrca, ";
				$qCocDat .= "$cAlfa.fcoc$cAno.ccoidxxx AS ccoidcab, ";
				$qCocDat .= "$cAlfa.fcoc$cAno.sccidxxx AS sccidcab, ";
				$qCocDat .= "$cAlfa.fcoc$cAno.comobsxx AS comobsca, ";
				$qCocDat .= "$cAlfa.fcoc$cAno.resprexx AS resprexx ";
				$qCocDat .= "FROM $cAlfa.fcod$cAno ";
				$qCocDat .= "LEFT JOIN $cAlfa.fcoc$cAno ON $cAlfa.fcod$cAno.comidxxx = $cAlfa.fcoc$cAno.comidxxx AND $cAlfa.fcod$cAno.comcodxx = $cAlfa.fcoc$cAno.comcodxx AND $cAlfa.fcod$cAno.comcscxx = $cAlfa.fcoc$cAno.comcscxx AND $cAlfa.fcod$cAno.comcsc2x = $cAlfa.fcoc$cAno.comcsc2x ";
				$qCocDat .= "WHERE ";
				if($gComId != ""){
					$qCocDat .= "fcod$cAno.comidxxx = \"$gComId\" AND ";
				}
				if($gComCod != ""){
					$qCocDat .= "fcod$cAno.comcodxx = \"$gComCod\" AND ";
				}
				if($gUsrId != ""){
					$qCocDat .= "fcod$cAno.regusrxx = \"$gUsrId\" AND ";
				}
				$qCocDat .= "fcod$cAno.regestxx = \"ACTIVO\" AND ";
				$qCocDat .= "fcod$cAno.comfecxx BETWEEN \"$dDesde\" AND \"$dFecHas\" ";
				$qCocDat .= "ORDER BY fcod$cAno.comidxxx,fcod$cAno.comcodxx,fcod$cAno.comcscxx,ABS(fcod$cAno.comcsc2x),ABS(fcod$cAno.comseqxx) ";
				$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
				// echo $qCocDat."~".mysql_num_rows($xCocDat);

				$cComId    = "";
				$cComCod   = "";
				$cComCsc   = "";
				$cComCsc2  = "";
				$nCountReg = 0;
				$nIndice   = 0;
				$mDatos    = array();
				while ($xRCD = mysql_fetch_array($xCocDat)) {
					$nCanReg++;
					if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexion(); }

					// Se detecta el cambio de comprobante para consultar la cantidad de registros a nivel de detalle
					if ($cComId != $xRCD['comidxxx'] || $cComCod != $xRCD['comcodxx'] || $cComCsc != $xRCD['comcscxx'] || $cComCsc2 != $xRCD['comcsc2x']) {
						$cComId   = $xRCD['comidxxx'];
						$cComCod  = $xRCD['comcodxx'];
						$cComCsc  = $xRCD['comcscxx'];
						$cComCsc2 = $xRCD['comcsc2x'];

						$qFcodxxx  = "SELECT COUNT($cAlfa.fcod$cAno.comidxxx) AS cantidad ";
						$qFcodxxx .= "FROM $cAlfa.fcod$cAno ";
						$qFcodxxx .= "WHERE ";
						$qFcodxxx .= "$cAlfa.fcod$cAno.comidxxx = \"$cComId\" AND ";
						$qFcodxxx .= "$cAlfa.fcod$cAno.comcodxx = \"$cComCod\" AND ";
						$qFcodxxx .= "$cAlfa.fcod$cAno.comcscxx = \"$cComCsc\" AND ";
						$qFcodxxx .= "$cAlfa.fcod$cAno.comcsc2x = \"$cComCsc2\" LIMIT 0,1 ";
						$xFcodxxx = f_MySql("SELECT","",$qFcodxxx,$xConexion01,"");
						$vFcodxxx = mysql_fetch_array($xFcodxxx);
						$nCantRegComp = $vFcodxxx['cantidad'];
					}

					// Si la cantidad de registros del comprobante supera el limite por excel se crea un nuevo indice para el array
					if (($nCantRegComp+$nCountReg) > $nRegExcel) {
						$nIndice++;
						$nCountReg = 0;
					}

					$cCuenta = substr($xRCD['pucidxxx'], 0, 8);
					if ($mCuenta["{$xRCD['pucidxxx']}"]['puc10dig'] == "SI") {
						$cCuenta = $xRCD['pucidxxx'];
					}

					$cTerId				= $xRCD['teridxxx'];
					$cPrefijo			= "";
					$cConsecutivo = "";
					$cNroCuota    = "";
					$dFecVenci    = "";
					if ($xRCD['comidxxx'] == "F") {
          	$cTerId = $xRCD['terid2cx'];			
					}

					$mCuentas = array("13300501", "23670501", "23689501", "28059501", "22050501", "24120501", 
														"2345100100", "2345100200", "2345100300", "2345050100");

					if (substr($xRCD['pucidxxx'],0,6) == "130505" || substr($xRCD['pucidxxx'],0,4) == "2205" || substr($xRCD['pucidxxx'],0,4) == "2335" ||
							in_array(substr($xRCD['pucidxxx'],0,8), $mCuentas) || in_array($xRCD['pucidxxx'], $mCuentas)
					) {
						$cPrefijo 		= ($xRCD['comidxxx'] == "F") ? $xRCD['resprexx'] : $xRCD['comidxxx'] . "-" . $xRCD['comcodxx'];
						$cConsecutivo = ($xRCD['comidxxx'] == "F") ? $xRCD['comcsc2x'] : $xRCD['comcscxx'];
						$cNroCuota    = "1";
						$dFecVenci    = ($xRCD['comfecve'] != "") ? date("d/m/Y", strtotime($xRCD['comfecve'])) : date("d/m/Y", strtotime($xRCD['comfeccx']));
					}

					// Se crea la matriz con la información de la transmision
					$mDatos[$nIndice][$nCountReg]['tipocomp'] = $mComprobantes["{$xRCD['comidxxx']}~{$xRCD['comcodxx']}"]['comids1x']; //Tipo de Comprobante
					$mDatos[$nIndice][$nCountReg]['conscomp'] = ($xRCD['comidxxx'] == "F") ? $xRCD['comcscxx'] : $xRCD['comcsc2x']; //Consecutivo Comprobante
					$mDatos[$nIndice][$nCountReg]['fechelab'] = date("d/m/Y", strtotime($xRCD['comfeccx'])); //Fecha de Elaboracion
					$mDatos[$nIndice][$nCountReg]['siglamon'] = ''; //Sigla Moneda
					$mDatos[$nIndice][$nCountReg]['tasacamb'] = ''; //Tasa de cambio
					$mDatos[$nIndice][$nCountReg]['ctaconta'] = $cCuenta; //Codigo cuenta contable
					$mDatos[$nIndice][$nCountReg]['identerc'] = strval($cTerId); //Identicacion tercero
					$mDatos[$nIndice][$nCountReg]['sucursal'] = ''; //Sucursal
					$mDatos[$nIndice][$nCountReg]['codprodu'] = ''; //Codigo producto
					$mDatos[$nIndice][$nCountReg]['codbodeg'] = ''; //Codigo de bodega
					$mDatos[$nIndice][$nCountReg]['accionxx'] = ''; //Accion
					$mDatos[$nIndice][$nCountReg]['cantprod'] = ''; //Cantidad producto
          $mDatos[$nIndice][$nCountReg]['prefijox'] = $cPrefijo; //Prefijo
					$mDatos[$nIndice][$nCountReg]['consecut'] = $cConsecutivo; //Consecutivo
					$mDatos[$nIndice][$nCountReg]['nrocuota'] = $cNroCuota; //Nro Cuota
					$mDatos[$nIndice][$nCountReg]['fechvenc'] = $dFecVenci; //Fecha de Vencimiento
					$mDatos[$nIndice][$nCountReg]['codimpue'] = ''; //Codigo Impuesto
					$mDatos[$nIndice][$nCountReg]['codgrufi'] = ''; //Codigo grupo activo fijo
					$mDatos[$nIndice][$nCountReg]['codactfi'] = ''; //Codigo activo fijo
					$mDatos[$nIndice][$nCountReg]['descripc'] = ''; //Descripcion
					$mDatos[$nIndice][$nCountReg]['ccsubccx'] = $xRCD['ccoidcab'] .'-'. $xRCD['sccidcab']; //Codigo centro/subcentro de costos
					$mDatos[$nIndice][$nCountReg]['debitoxx'] = ''; //Debito
					$mDatos[$nIndice][$nCountReg]['creditox'] = ''; //Credito
					$mDatos[$nIndice][$nCountReg]['observac'] = str_replace($vBuscar, $vReempl, $xRCD['comobsca']); //Observaciones
					$mDatos[$nIndice][$nCountReg]['basegrav'] = ''; //Base gravable libro compras/ventas
					$mDatos[$nIndice][$nCountReg]['baseexen'] = ''; //Base exenta libro compras/ventas
					$mDatos[$nIndice][$nCountReg]['mescierr'] = ''; //Mes de cierre

					if ($xRCD['commovxx'] == "C") {
						$mDatos[$nIndice][$nCountReg]['creditox'] = floatval(number_format($xRCD['comvlrxx'],2,'.','')); //Credito
					} else {
						$mDatos[$nIndice][$nCountReg]['debitoxx'] = floatval(number_format($xRCD['comvlrxx'],2,'.','')); //Debito
					}

					if (($xRCD['comidxxx'] == "F" && $xRCD['comctocx'] == "IP") || $xRCD['comidxxx'] == "P") {
						$mDatos[$nIndice][$nCountReg]['basegrav'] = ($xRCD['comvlr01'] > 0) ? floatval(number_format($xRCD['comvlr01'],2,'.','')) : ''; //Base gravable libro compras/ventas
					}

					$nCantRegComp = 0;
					$nCountReg++;
				}

				// echo "<pre>";
				// print_r($mDatos);

				$cMsjXlsx = '';
				for ($i=0; $i<count($mDatos); $i++) {
					/*** GENERACION DE ARCHIVO xlsx ***/
					$cFile01 = "SIIGO_NUBE_MIRCANAX_".date("YmdHis")."_".($i+1).".xlsx";
					$cFileDownload = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cFile01;

					// Borrando archivo si ya existe
					if (file_exists($cFileDownload)) {
						unlink($cFileDownload); 
					}

					$cF01 = fopen($cFileDownload,"a");
					if (!$cF01) {
						$nSwitch = 1;
						f_Mensaje(__FILE__,__LINE__,"No se Pudo crear el Archivo[{$cFileDownload}].");
					}

					if ($nSwitch == 0) {
						$mColumnas  = [
							'Tipo de comprobante',
							'Consecutivo comprobante',
							'Fecha de elaboración ',
							'Sigla moneda',
							'Tasa de cambio',
							'Código cuenta contable',
							'Identificación tercero',
							'Sucursal',
							'Código producto',
							'Código de bodega',
							'Acción',
							'Cantidad producto',
							'Prefijo',
							'Consecutivo',
							'No. cuota',
							'Fecha vencimiento',
							'Código impuesto',
							'Código grupo activo fijo',
							'Código activo fijo',
							'Descripción',
							'Código centro/subcentro de costos',
							'Débito',
							'Crédito',
							'Observaciones',
							'Base gravable libro compras/ventas  ',
							'Base exenta libro compras/ventas',
							'Mes de cierre'
						];

						$writerImpo = WriterFactory::create(Type::XLSX); // for XLSX files
						$writerImpo->openToFile($cFileDownload); // write data to a file or to a PHP stream

						$border = (new BorderBuilder())
												->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
												->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
												->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
												->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
												->build();

						$style = (new StyleBuilder())
											->setFontSize(11)
											->setFontColor(Color::WHITE)
											->setShouldWrapText(false)
											->setBorder($border)
											->setBackgroundColor(Color::rgb(11,113,193)) 
											->build();

						$multipleRows = array();
						$nInd_MultipleRows = 0;
						$writerImpo->addRowWithStyle($mColumnas,$style);

						// Se recorre el array interno que contiene los campos a pintar en el excel
						for ($j=0; $j<count($mDatos[$i]); $j++) {
							$multipleRows[$nInd_MultipleRows] = [
								strval($mDatos[$i][$j]['tipocomp']), //Tipo de Comprobante
								strval($mDatos[$i][$j]['conscomp']), //Consecutivo Comprobante
								$mDatos[$i][$j]['fechelab'], //Fecha de Elaboracion
								$mDatos[$i][$j]['siglamon'], //Sigla Moneda
								$mDatos[$i][$j]['tasacamb'], //Tasa de cambio
								strval($mDatos[$i][$j]['ctaconta']), //Codigo cuenta contable
								strval($mDatos[$i][$j]['identerc']), //Identicacion tercero
								$mDatos[$i][$j]['sucursal'], //Sucursal
								$mDatos[$i][$j]['codprodu'], //Codigo producto																																
								$mDatos[$i][$j]['codbodeg'], //Codigo de bodega
								$mDatos[$i][$j]['accionxx'], //Accion
								$mDatos[$i][$j]['cantprod'], //Cantidad producto
								$mDatos[$i][$j]['prefijox'], //Prefijo
								$mDatos[$i][$j]['consecut'], //Consecutivo
								$mDatos[$i][$j]['nrocuota'], //Nro Cuota
								$mDatos[$i][$j]['fechvenc'], //Fecha de Vencimiento
								$mDatos[$i][$j]['codimpue'], //Codigo Impuesto
								$mDatos[$i][$j]['codgrufi'], //Codigo grupo activo fijo
								$mDatos[$i][$j]['codactfi'], //Codigo activo fijo
								$mDatos[$i][$j]['descripc'], //Descripcion
								$mDatos[$i][$j]['ccsubccx'], //Codigo centro/subcentro de costos
								$mDatos[$i][$j]['debitoxx'], //Debito
								$mDatos[$i][$j]['creditox'], //Credito,
								$mDatos[$i][$j]['observac'], //Observaciones,
								$mDatos[$i][$j]['basegrav'], //Base gravable libro compras/ventas
								$mDatos[$i][$j]['baseexen'], //Base exenta libro compras/ventas
								$mDatos[$i][$j]['mescierr']  //Mes de cierre
							];
							$nInd_MultipleRows++;
						}

						$styleRows = (new StyleBuilder())
										->setFontColor(Color::BLACK)
										->setBorder($border)
										->build();

						$writerImpo->addRowsWithStyle($multipleRows,$styleRows);
						$writerImpo->close();

						$cMsjXlsx .= "<br><br><center><a href ='$cFileDownload' download='$cFile01'>$cFile01</a></center>";
					}
				}

				if(mysql_num_rows($xCocDat) == 0 ){
					echo "No se Generaron Registros.";
				}else{
					echo $cMsjXlsx;
				}
			}
		?>
	</body>
</html>

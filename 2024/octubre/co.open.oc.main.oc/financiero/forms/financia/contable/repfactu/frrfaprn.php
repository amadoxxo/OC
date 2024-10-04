<?php
	/**
	 * Imprime Reporte Facturacion.
	 * --- Descripcion: Permite Imprimir Reporte Estado Facturacion.
	 * @author Sebastian Cardenas Suarez <sebastian.cardenas@open-eb.co>
	 */
	header('Content-Type:text/html; charset=UTF-8');

	set_time_limit(0);
	ini_set("memory_limit", "512M");

	// ini_set('error_reporting', E_ERROR);
	// ini_set("display_errors","1");

	date_default_timezone_set("America/Bogota");

	/**
	 * Cantidad de Registros para reiniciar conexion
	 */
	define("_NUMREG_", 50);

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
	 * Nombre del archivo excel
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

			// Librerias
			include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
			include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utility.php");
			include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php");
			include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/uticfdhl.php");
			include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utiliqdo.php");

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
		## Librerias
		include("../../../../../config/config.php");
		include("../../../../libs/php/utility.php");
		include("../../../../libs/php/uticfdhl.php");
		include("../../../../libs/php/utiliqdo.php");
		include("../../../../../libs/php/utiprobg.php");
	}

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
	$swidth			= $kDf[6];

	if ($_SERVER["SERVER_PORT"] != "") {
		/*** Ejecutar proceso en Background ***/
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
	} // fin if ($_SERVER["SERVER_PORT"] != "")

	if ($_SERVER["SERVER_PORT"] == "") {
		$rTipo     = $_POST['rTipo'];
		$gDesde    = $_POST['gDesde'];
		$gHasta    = $_POST['gHasta'];
		$gTerId    = $_POST['gTerId'];
		$gTerNom   = $_POST['gTerNom'];
		$gSucId    = $_POST['gSucId'];
		$gDocNro   = $_POST['gDocNro'];
		$gDocSuf   = $_POST['gDocSuf'];
		$cEjProBg  = $_POST['cEjProBg'];
	}

	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;

		$strPost = "rTipo~" 	. $rTipo.
							"|gDesde~" 	. $gDesde.
							"|gHasta~" 	. $gHasta.
							"|gTerId~" 	. $gTerId.
							"|gTerNom~" . $gTerNom.
							"|gSucId~" 	. $gSucId.
							"|gDocNro~" . $gDocNro.
							"|gDocSuf~" . $gDocSuf.
							"|cEjProBg~" 	. $cEjProBg;

		$vParBg['pbadbxxx'] = $cAlfa;                       // Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                // Modulo
		$vParBg['pbatinxx'] = "REPORTEDEFACTURACION";       // Tipo Interface
		$vParBg['pbatinde'] = "REPORTE DE FACTURACION";     // Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                           // Sucursal
		$vParBg['doiidxxx'] = "";                           // Do
		$vParBg['doisfidx'] = "";                           // Sufijo
		$vParBg['cliidxxx'] = $gTerId;                      // Nit
		$vParBg['clinomxx'] = $gTerNom;                     // Nombre Importador
		$vParBg['pbapostx'] = $strPost;											// Parametros para reconstruir Post
		$vParBg['pbatabxx'] = "";                           // Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];	// Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];      // cookie
		$vParBg['pbacrexx'] = 0;                            // Cantidad Registros
		$vParBg['pbatxixx'] = 1;                            // Tiempo Ejecucion x Item en Segundos
		$vParBg['pbaopcxx'] = "";                           // Opciones
		$vParBg['regusrxx'] = $kUser;                       // Usuario que Creo Registro

		## Incluyendo la clase de procesos en background
		$ObjProBg = new cProcesosBackground();
		$mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);

		## Imprimiendo resumen de todo ok.
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
			$objFacturacionDhlExpress = new cFacturacionDhlExpress(); // se instancia la clase cTOE

			$vDatos['DDESDEXX'] = $gDesde;           //FECHA DESDE
			$vDatos['DHASTAXX'] = $gHasta;           //FECHA HASTA
			$vDatos['TERIDXXX'] = $gTerId;           //ID CLIENTE
			$vDatos['TERNOMXX'] = $gTerNom;          //NOMBRE DEL CLIENTE
			$vDatos['SUCIDXXX'] = $gSucId;           //ID DE LA SUCURSAL
			$vDatos['DOCIDXXX'] = $gDocNro;          //ID DEL DO
			$vDatos['DOCSUFXX'] = $gDocSuf;          //SUFIJO DEL DO
			$vDatos['TIPREPXX'] = $rTipo;            //TIPO DE REPORTE

			$mReturnReporte = $objFacturacionDhlExpress->fnReporteFacturacionDhlExpress($vDatos);

			$cFecha = $vDatos['DDESDEXX']." HASTA:  ".$vDatos['DHASTAXX'];

      //Trayendo los titulos
      $qTitulos  = "SELECT comserti ";
			$qTitulos .= "FROM $cAlfa.$mReturnReporte[1] LIMIT 0,1";
			$xTitulos = f_MySql("SELECT","",$qTitulos,$xConexion01,"");
      $vTitulos = mysql_fetch_array($xTitulos);
      $mTitulos = json_decode($vTitulos['comserti'], true);

			$qTabTem  = "SELECT * ";
			$qTabTem .= "FROM $cAlfa.$mReturnReporte[1]";
			$xTabTem = f_MySql("SELECT","",$qTabTem,$xConexion01,"");
			if (mysql_num_rows($xTabTem) > 0) {
				switch ($vDatos['TIPREPXX']) {
					case 1: // PINTA POR PANTALLA//
						?>
						<html>
							<title>Reporte de Facturacion</title>
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
								$nCol = 59 + count($mTitulos);
								?>
								<center>
									<table border = "1" cellpadding = "0" cellspacing = "0" width = "8785px">
										<tr>
											<td class="name" style="font-size:14px;width:196.66px">
												<center><img style="justify-content: center;" src = "<?php echo $cPlesk_Skin_Directory ?>/logo_dhl_express.jpg" width="170px"><center>
											</td>
											<td style="padding-left: 10px;" class="name" style="font-size:14px">
												<span style="font-size:18px"> <?php echo "REPORTE DE FACTURACIÓN" ?></span><br>
												<?php if ($gDesde != '' || $gHasta != '') { ?>
													<span style="font-size:18px"> <?php echo "FECHA - DESDE:  ". $cFecha ?></span><br>
												<?php } ?>
												<br>
												<?php if ($gTerId != '') { ?>
													<b>NIT: </b><b><?php echo $vDatos['TERIDXXX']."-".f_Digito_Verificacion($vDatos['TERIDXXX'])?></b><br>
												<?php }
															if ($gTerNom != '') { ?>
													<b>CLIENTE: </b><b><?php echo $vDatos['TERNOMXX']?></b><br>
												<?php }
															if ($gSucId != '' || $gDocNro != '' || $gDocSuf != '') { ?>
													<b>DO: </b><b><?php echo $vDatos['SUCIDXXX'].' - '.$vDatos['DOCIDXXX'].' - '.$vDatos['DOCSUFXX']?></b><br>
												<?php } ?>
												<b>FECHA Y HORA DE CONSULTA: </b><b><?php echo date("Y-m-d H:i:s");?></b>
										</tr>
									</table>
									<br>
									<table border = "1" cellpadding = "0" cellspacing = "0" width = "8785px">
										<tr>
											<td style="padding-left: 10px;" align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>REPORTE DE FACTURACI&Oacute;N</b></td>
										</tr>
										<tr>
											<td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>CLIENTE</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>NIT</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>DIGITO DE VERIFICACI&Oacute;N</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>FACTURAR A</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:310px"><b>DIRECCI&Oacute;N</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>TEL&Eacute;FONO</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>CIUDAD</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>NIT FACTURAR A</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>
                      <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>SUFIJO</b></td>
                      <td align="center" bgcolor = "#D6DFF7" style="width:160px"><b>OBSERVACI&Oacute;N</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>GU&Iacute;A</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DEPOSITO</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>FOBUSD</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>CIF USD</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>CIF COP</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>ARANCEL</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>IVA</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>LICENCIA DE IMPORTACI&Oacute;N</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>RESCATES</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:130px"><b>SALVAGUARDIAS</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>SANCIONES</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>TRANSPORTE</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>ICA</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>INVIMA</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>FITO</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>DERECHOS ANTIDUMPING</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>OTROS PAGOS</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>LIBERACIONES</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>BODEGAJES</b></td>
                      <?php for ($nM=0; $nM<count($mTitulos); $nM++) { ?>
                        <td align="center" bgcolor = "#D6DFF7" style="width:135px"><b><?php echo $mTitulos[$nM] ?></b></td>
                      <?php } ?>
                      <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>G.M.F.</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>BASE IVA</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>VALOR IVA</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:130px"><b>BASE RETE FUENTE</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:140px"><b>VALOR RETE FUENTE</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>BASE RETE IVA</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>VALOR RETE IVA</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>BASE RETE ICA</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>VALOR RETE ICA</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>TOTAL</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>ANTICIPOS</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>SALDO A CARGO</b></td>
                      <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>SALDO A FAVOR</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>FACTURA</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>FECHA FACTURA</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>AGENTE</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>TARIFA</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>CUENTA FACTURACI&Oacute;N</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:180px"><b>FECHA DE LEVANTE</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>PPP</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>RCF</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>NOTA CR&Eacute;DITO</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:180px"><b>FECHA NOTA CR&Eacute;DITO</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:180px"><b>VALOR NOTA CR&Eacute;DITO</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:320px"><b>FECHA APROBACI&Oacute;N REGISTRO IMPORTACI&Oacute;N</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:130px"><b>RECIBOS DE CAJA</b></td>
											<td align="center" bgcolor = "#D6DFF7" style="width:160px"><b>VALOR RECIBO DE CAJA</b></td>
										</tr>
										<?php
										while ($xRTT = mysql_fetch_array($xTabTem)) {
                      $mServicios = array();
                      $mServicios = json_decode($xRTT['comserxx'], true);
										?>
										<tr>
											<td align="left" style="padding:2px"><?php echo $xRTT['ternomxx'] ?></td>
											<td align="center" style="padding:2px"><?php echo $xRTT['teridxxx'] ?></td>
											<td align="center" style="padding:2px"><?php echo f_Digito_Verificacion($xRTT['teridxxx']) ?></td>
											<td align="left" style="padding:2px"><?php echo $xRTT['ternom2x'] ?></td>
											<td align="left" style="padding:2px"><?php echo $xRTT['terdirxx'] ?></td>
											<td align="center" style="padding:2px"><?php echo $xRTT['tertelxx'] ?></td>
											<td align="left" style="padding:2px"><?php echo $xRTT['terciuxx'] ?></td>
											<td align="center" style="padding:2px"><?php echo $xRTT['terid2xx'] ?></td>
											<td align="left" style="padding:2px"><?php echo $xRTT['docidxxx'] ?></td>
                      <td align="center" style="padding:2px"><?php echo $xRTT['docsufxx'] ?></td>
                      <td align="center" style="padding:2px"><?php echo $xRTT['comobsxx'] ?></td>
											<td align="center" style="padding:2px"><?php echo $xRTT['dgedtxxx'] ?></td>
											<td align="left" style="padding:2px"><?php echo $xRTT['daaidxxx'] ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['limvlrxx']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['limnetxx']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['limcifxx']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['limgraxx']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['limsubtx']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['vlrlicim']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['subrestl']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['subsaltl']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['subsantl']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['comtrans']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['comicaxx']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['cominvim']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['comfitox']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['subanttl']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['comotrpg']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['comliber']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['combodeg']+0) ?></td>
                      <?php 
                      for ($nM=0; $nM<count($mTitulos); $nM++) { ?>
                          <td align="right"><?php echo ($mServicios[$nM]['comvlrxx']+0) ?></td>
                      <?php
                      } ?>
                      <td align="right" style="padding:2px"><?php echo ($xRTT['comifxxx']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['combivax']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['comivaxx']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['combrfte']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['comrftex']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['combriva']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['comrivax']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['combrica']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['comricax']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['comvlrxx']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['comantxx']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['comsalfa']+0) ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['comsalca']+0) ?></td>
											<td align="center" style="padding:2px"><?php echo $xRTT['resprexx'].$xRTT['comcscxx'] ?></td>
											<td align="center" style="padding:2px"><?php echo (($xRTT['comfecxx'] != "0000-00-00") ? $xRTT['comfecxx'] : "") ?></td>
											<td align="center" style="padding:2px"><?php echo $xRTT['agentexx'] ?></td>
											<td align="left" style="padding:2px"><?php echo $xRTT['tarifaxx'] ?></td>
											<td align="center" style="padding:2px"><?php echo $xRTT['cliimpxx'] ?></td>
											<td align="center" style="padding:2px"><?php echo (($xRTT['doimylev'] != "0000-00-00") ? $xRTT['doimylev'] : "") ?></td>
											<td align="center" style="padding:2px"><?php echo (($xRTT['doifenfa'] != "0000-00-00") ? $xRTT['doifenfa'] : "") ?></td>
											<td align="center" style="padding:2px"><?php echo (($xRTT['doifenca'] != "0000-00-00") ? $xRTT['doifenca'] : "") ?></td>
											<td align="center" style="padding:2px"><?php echo $xRTT['notcrexx'] ?></td>
											<td align="center" style="padding:2px"><?php echo (($xRTT['notfecre'] != "0000-00-00") ? $xRTT['notfecre'] : "") ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['notvrlxx']+0) ?></td>
											<td align="center" style="padding:2px"><?php echo (($xRTT['fecregim'] != "0000-00-00") ? $xRTT['fecregim'] : "") ?></td>
											<td align="center" style="padding:2px"><?php echo $xRTT['reccajxx'] ?></td>
											<td align="right" style="padding:2px"><?php echo ($xRTT['vlrrecca']+0) ?></td>
										</tr>
										<?php
										}
										?>
									</table>
								</center>
							</body>
						</html>
					<?php break;
					case 2: // PINTA POR EXCEL//
						/**
						 * Variable para armar la cadena de texto que se envia al excel
						 * @var Text
						 */
						$header  .= 'Reporte de Facturacion'."\n";
						$header  .= "\n";
						$cData    = '';
						$cNomFile = "REPORTE_DE_FACTURACION_".$kUser."_".date('YmdHis').".xls";

						if ($_SERVER["SERVER_PORT"] != "") {
							$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
						} else {
							$cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
						}
				
						if (file_exists($cFile)) {
							unlink($cFile);
						}

						$fOp = fopen($cFile, 'a');

						$nCol = 59 + count($mTitulos);
						$cData .= '<table border = "1" cellpadding = "0" cellspacing = "0">';
							$cData .= '<tr>';
								$cData .= '<td style="padding-left: 10px;" class="name" colspan = "'.$nCol.'" style="font-size:14px">';
									$cData .= '<span style="font-size:18px">REPORTE DE FACTURACI&Oacute;N</span><br>';
									if ($gDesde != '' || $gHasta != '') {
										$cData .= '<span style="font-size:18px">FECHA - DESDE:  '.$cFecha.'</span><br>';
									}
								$cData .= '</td>';
							$cData .= '</tr>';
							if ($gTerId != '') {
								$cData .= '<tr>';
									$cData .= '<td style="padding-left: 10px;" class="name" colspan = "'.$nCol.'" style="font-size:14px">';
										$cData .= '<b>NIT: </b><b>'.$vDatos['TERIDXXX']."-".f_Digito_Verificacion($vDatos['TERIDXXX']).'</b><br>';
									$cData .= '</td>';
								$cData .= '</tr>';
							}
							if ($gTerNom != '') {
								$cData .= '<tr>';
									$cData .= '<td style="padding-left: 10px;" class="name" colspan = "'.$nCol.'" style="font-size:14px">';
										$cData .= '<b>CLIENTE: </b><b>'.$vDatos['TERNOMXX'].'</b><br>';
									$cData .= '</td>';
								$cData .= '</tr>';
							}
							if ($gSucId != '' || $gDocNro != '' || $gDocSuf != '') {
								$cData .= '<tr>';
									$cData .= '<td style="padding-left: 10px;" class="name" colspan = "'.$nCol.'" style="font-size:14px">';
										$cData .= '<b>DO: </b><b>'.$vDatos['SUCIDXXX'].' - '.$vDatos['DOCIDXXX'].' - '.$vDatos['DOCSUFXX'].'</b><br>';
									$cData .= '</td>';
								$cData .= '</tr>';
							}
							$cData .= '<tr>';
								$cData .= '<td style="padding-left: 10px;" class="name" colspan = "'.$nCol.'" style="font-size:14px">';
									$cData .= '<b>FECHA Y HORA DE CONSULTA: </b><b>'.date("Y-m-d H:i:s").'</b>';
								$cData .= '</td>';
							$cData .= '</tr>';
							$cData .= '<tr><td colspan = "'.$nCol.'"></td></tr>';
							$cData .= '<tr>';
								$cData .= '<td style="padding-left: 10px;" align="left" colspan = "'.$nCol.'" bgcolor = "#96ADEB" style="font-size:14px"><b>REPORTE DE FACTURACI&Oacute;N</b></td>';
							$cData .= '</tr>';
							$cData .= '<tr>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>CLIENTE</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>NIT</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>DIGITO DE VERIFICACI&Oacute;N</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>FACTURAR A</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:310px"><b>DIRECCI&Oacute;N</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>TEL&Eacute;FONO</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>CIUDAD</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>NIT FACTURAR A</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>';
                $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>SUFIJO</b></td>';
                $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:160px"><b>OBSERVACI&Oacute;N</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>GU&Iacute;A</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DEPOSITO</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>FOBUSD</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>CIF USD</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>CIF COP</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>ARANCEL</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>IVA</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>LICENCIA DE IMPORTACI&Oacute;N</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>RESCATES</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:130px"><b>SALVAGUARDIAS</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>SANCIONES</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>TRANSPORTE</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>ICA</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>INVIMA</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>FITO</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>DERECHOS ANTIDUMPING</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>OTROS PAGOS</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>LIBERACIONES</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>BODEGAJES</b></td>';
                for ($nM=0; $nM<count($mTitulos); $nM++) {
                  $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:135px"><b>'.$mTitulos[$nM].'</b></td>';
                }
                $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>G.M.F.</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>BASE IVA</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>VALOR IVA</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:130px"><b>BASE RETE FUENTE</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:140px"><b>VALOR RETE FUENTE</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>BASE RETE IVA</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>VALOR RETE IVA</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>BASE RETE ICA</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>VALOR RETE ICA</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>TOTAL</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>ANTICIPOS</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>SALDO A CARGO</b></td>';
                $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>SALDO A FAVOR</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>FACTURA</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>FECHA FACTURA</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>AGENTE</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>TARIFA</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>CUENTA FACTURACI&Oacute;N</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:180px"><b>FECHA DE LEVANTE</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>PPP</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>RCF</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>NOTA CR&Eacute;DITO</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:180px"><b>FECHA NOTA CR&Eacute;DITO</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:180px"><b>VALOR NOTA CR&Eacute;DITO</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:320px"><b>FECHA APROBACI&Oacute;N REGISTRO IMPORTACI&Oacute;N</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:130px"><b>RECIBOS DE CAJA</b></td>';
								$cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:160px"><b>VALOR RECIBO DE CAJA</b></td>';
							$cData .= '</tr>';
							while ($xRTT = mysql_fetch_array($xTabTem)) {
                $mServicios = array();
                $mServicios = json_decode($xRTT['comserxx'], true);

								$cData .= '<tr>';
									$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['ternomxx'].'</td>';
									$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.$xRTT['teridxxx'].'</td>';
									$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.f_Digito_Verificacion($xRTT['teridxxx']).'</td>';
									$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['ternom2x'].'</td>';
									$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['terdirxx'].'</td>';
									$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.$xRTT['tertelxx'].'</td>';
									$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['terciuxx'].'</td>';
									$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.$xRTT['terid2xx'].'</td>';
									$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['docidxxx'].'</td>';
                  $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.$xRTT['docsufxx'].'</td>';
                  $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['comobsxx'].'</td>';
									$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.$xRTT['dgedtxxx'].'</td>';
									$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['daaidxxx'].'</td>';
									$cData .= '<td align="right">'.($xRTT['limvlrxx']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['limnetxx']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['limcifxx']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['limgraxx']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['limsubtx']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['vlrlicim']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['subrestl']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['subsaltl']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['subsantl']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['comtrans']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['comicaxx']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['cominvim']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['comfitox']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['subanttl']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['comotrpg']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['comliber']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['combodeg']+0).'</td>';
                  for ($nM=0; $nM<count($mTitulos); $nM++) { 
                    $cData .= '<td align="right">'.($mServicios[$nM]['comvlrxx']+0).'</td>';
                  }
									$cData .= '<td align="right">'.($xRTT['comifxxx']+0).'</td>';
                  $cData .= '<td align="right">'.($xRTT['combivax']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['comivaxx']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['combrfte']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['comrftex']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['combriva']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['comrivax']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['combrica']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['comricax']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['comvlrxx']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['comantxx']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['comsalfa']+0).'</td>';
									$cData .= '<td align="right">'.($xRTT['comsalca']+0).'</td>';
									$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.$xRTT['resprexx'].$xRTT['comcscxx'].'</td>';
									$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($xRTT['comfecxx'] != "0000-00-00") ? $xRTT['comfecxx'] : "").'</td>';
									$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.$xRTT['agentexx'].'</td>';
									$cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['tarifaxx'].'</td>';
									$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.$xRTT['cliimpxx'].'</td>';
									$cData .= '<td align="center"style="mso-number-format:yyyy-mm-dd">'.(($xRTT['doimylev'] != "0000-00-00") ? $xRTT['doimylev'] : "").'</td>';
									$cData .= '<td align="center"style="mso-number-format:yyyy-mm-dd">'.(($xRTT['doifenfa'] != "0000-00-00") ? $xRTT['doifenfa'] : "").'</td>';
									$cData .= '<td align="center"style="mso-number-format:yyyy-mm-dd">'.(($xRTT['doifenca'] != "0000-00-00") ? $xRTT['doifenca'] : "").'</td>';
									$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.$xRTT['notcrexx'].'</td>';
									$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($xRTT['notfecre'] != "0000-00-00") ? $xRTT['notfecre'] : "").'</td>';
									$cData .= '<td align="right" style="mso-number-format:\'\@\'">'.($xRTT['notvrlxx']+0).'</td>';
									$cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($xRTT['fecregim'] != "0000-00-00") ? $xRTT['fecregim'] : "").'</td>';
									$cData .= '<td align="center" style="mso-number-format:\'\@\'">'.$xRTT['reccajxx'].'</td>';
									$cData .= '<td align="right">'.($xRTT['vlrrecca']+0).'</td>';
								$cData .= '</tr>';
							}
						$cData .= '</table>';

						fwrite($fOp, $cData);
						fclose($fOp);

						if (file_exists($cFile)) {
              // Obtener la ruta absoluta del archivo
              $cAbsolutePath = realpath($cFile);
              $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

							if ($cData == "") {
								$cData = "\n(0) REGISTROS!\n";
							}
              if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
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
                }else{
                  $cNomArc = $cNomFile;
                  echo "\n".$cNomArc;
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
					default:
						/*** PINTA POR PDF ***/
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
								global $vDatos;
								global $cFecha;
								global $gDesde;
								global $gHasta;
								global $gTerId;
								global $gTerNom;
								global $gSucId;
								global $gDocNro;
								global $gDocSuf;

								$this->SetXY(6, 7);
								$this->Cell(205, 31, '', 1,0,'C');

								$this->Image($cRoot.$cPlesk_Skin_Directory.'/logo_dhl_express.jpg', 10, 13, 39, 19);

								$this->SetFont('verdana', 'B', 12);
								$this->SetXY(6, 8);
								$this->Cell(205, 8, "REPORTE DE FACTURACION",0,0, 'C');
								$this->Ln(7);
								if ($gDesde != '' || $gHasta != '') {
									$this->SetFont('verdana', '', 8);
									$this->SetX(6);
									$this->Cell(205, 6, "FECHA - DESDE: ".$cFecha,0,0, 'C');
									$this->Ln(4);
							  }
								if ($gTerId != '') {
									$this->SetFont('verdana', '', 8);
									$this->SetX(6);
									$this->Cell(205,6,'NIT: '.$vDatos['TERIDXXX']."-".f_Digito_Verificacion($vDatos['TERIDXXX']),0,0,'C');
									$this->Ln(4);
							  }
								if ($gTerNom != '') {
									$this->SetFont('verdana', '', 8);
									$this->SetX(6);
									$this->Cell(205,6,'CLIENTE: '.$vDatos['TERNOMXX'],0,0,'C');
									$this->Ln(4);
							  }
								if ($gSucId != '' || $gDocNro != '' || $gDocSuf != '') {
									$this->SetFont('verdana', '', 8);
									$this->SetX(6);
									$this->Cell(205,6,'DO: '.$vDatos['SUCIDXXX'].' - '.$vDatos['DOCIDXXX'].' - '.$vDatos['DOCSUFXX'],0,0,'C');
									$this->Ln(4);
							  }
								$this->SetFont('verdana', '', 8);
								$this->SetX(6);
								$this->Cell(205,6,'FECHA Y HORA DE CONSULTA: '.date("Y-m-d H:i:s"),0,0,'C');
								$this->Ln(15);
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
								for($i=0;$i<count($data);$i++){
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
									$this->setX(6);
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

						//Linea Inicial

						$pdf->SetFont('verdana', 'B', 6);
						$pdf->SetY(41);
						$pdf->SetX(6);
						$pdf->Cell(205,0,'----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------',0,0,'C');
						$pdf->Ln(3);

						global $xRTT;
						global $xTabTem;

						while ($xRTT = mysql_fetch_array($xTabTem)) {

              $mServicios = array();
              $mServicios = json_decode($xRTT['comserxx'], true);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(20,5,"CLIENTE",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(142,5,$xRTT['ternomxx'],1,0,'L');

              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(13,5,"NIT",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(30,5,$xRTT['teridxxx']."-".f_Digito_Verificacion($xRTT['teridxxx']),1,0,'L');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(20,5,"FACTURAR A",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(142,5,$xRTT['ternom2x'],1,0,'L');

              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(13,5,"NIT",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(30,5,$xRTT['terid2xx']."-".f_Digito_Verificacion($xRTT['terid2xx']),1,0,'L');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(20,5,"DIRECCION",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(107,5,$xRTT['terdirxx'],1,0,'L');

              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(15,5,"TELEFONO",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(20,5,$xRTT['tertelxx'],1,0,'L');

              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(13,5,"CIUDAD",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(30,5,$xRTT['terciuxx'],1,0,'L');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(20,5,"DO",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(30,5,$xRTT['docidxxx'],1,0,'L');

              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(13,5,"SUFIJO",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(8,5,$xRTT['docsufxx'],1,0,'L');

              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(10,5,"GUIA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(30,5,$xRTT['dgedtxxx'],1,0,'L');

              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(15,5,"DEPOSITO",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(79,5,$xRTT['daaidxxx'],1,0,'L');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(20,5,"OBSERVACION",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(185,5,$xRTT['comobsxx'],1,0,'L');
              $pdf->Ln(10);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"FOB USD",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['limvlrxx']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"CIF USD",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['limnetxx']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"CIF COP",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['limcifxx']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"ARANCEL",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['limgraxx']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"IVA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['limsubtx']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"LICENCIA DE IMPORTACION",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['vlrlicim']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"RESCATES",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['subrestl']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"SALVAGUARDIAS",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['subsaltl']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"SANCIONES",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['subsantl']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"TRANSPORTE",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['comtrans']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"ICA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['comicaxx']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"INVIMA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['cominvim']+0),1,0,'R');
              $pdf->Ln(10);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"FITO",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['comfitox']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"DERECHOS ANTIDUMPING",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['subanttl']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"OTROS PAGOS",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['comotrpg']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"LIBERACIONES",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['comliber']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"BODEGAJES",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['combodeg']+0),1,0,'R');
              $pdf->Ln(10);


              for ($nM=0; $nM<count($mTitulos); $nM++) { 
                $pdf->SetX(6);
                $pdf->SetFont('verdana', 'B', 6);
                $pdf->Cell(160,5,$mTitulos[$nM],1,0,'L');
                $pdf->SetFont('verdana', '', 6);
                $pdf->Cell(45,5,($mServicios[$nM]['comvlrxx']+0),1,0,'R');
                $pdf->Ln(5);
              }
							$pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"BASE IVA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['combivax']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"VALOR IVA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['comivaxx']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"BASE RETEFUENTE",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['combrfte']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"VALOR RETEFUENTE",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['comrftex']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"BASE RETEIVA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['combriva']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"VALOR RETEIVA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['comrivax']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"BASE RETEICA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['combrica']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"VALOR RETEICA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['comricax']+0),1,0,'R');
              $pdf->Ln(10);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"TOTAL",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['comvlrxx']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"ANTICIPOS",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['comantxx']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"SALDO A CARGO",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['comsalca']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"SALDO A FAVOR",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['comsalfa']+0),1,0,'R');
              $pdf->Ln(10);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"FACTURA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,$xRTT['resprexx'].$xRTT['comcscxx'],1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"FECHA FACTURA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,(($xRTT['comfecxx'] != "0000-00-00") ? $xRTT['comfecxx'] : ""),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"AGENTE",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,$xRTT['agentexx'],1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"TARIFA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,$xRTT['tarifaxx'],1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"CUENTA FACTURACION",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,$xRTT['cliimpxx'],1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"FECHA DE LEVANTE",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,(($xRTT['doimylev'] != "0000-00-00") ? $xRTT['doimylev'] : ""),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"PPP",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,(($xRTT['doifenfa'] != "0000-00-00") ? $xRTT['doifenfa'] : ""),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"RCF",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,(($xRTT['doifenca'] != "0000-00-00") ? $xRTT['doifenca'] : ""),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"NOTA CREDITO",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,$xRTT['notcrexx'],1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"FECHA NOTA CREDITO",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,(($xRTT['notfecre'] != "0000-00-00") ? $xRTT['notfecre'] : ""),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"VALOR NOTA CREDITO",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['notvrlxx']+0),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"FECHA APROBACION REGISTRO IMPORTACION",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,(($xRTT['fecregim'] != "0000-00-00") ? $xRTT['fecregim'] : ""),1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"RECIBOS DE CAJA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,$xRTT['reccajxx'],1,0,'R');
              $pdf->Ln(5);

              $pdf->SetX(6);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->Cell(160,5,"VALOR RECIBO DE CAJA",1,0,'L');
              $pdf->SetFont('verdana', '', 6);
              $pdf->Cell(45,5,($xRTT['vlrrecca']+0),1,0,'R');
              $pdf->Ln(10);

							$pdf->SetFont('verdana', 'B', 6);
							$pdf->SetX(6);
							$pdf->Cell(205,0,'----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------',0,0,'C');
							$pdf->Ln(3);
						}

						$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";

						$pdf->Output($cFile);

						if (file_exists($cFile)) {
							chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
						} else {
							f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
						}

						echo "<html><script>document.location='$cFile';</script></html>";
					break;
				}
			} else {
				echo 'No se encontraron registros.';
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
				$cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}
		}
	}

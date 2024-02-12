<?php

	/**
	 * Nuevo Conceptos Reporte Facturacion DHL
	 * --- Descripcion: Permite Crear Nuevo Concepto Reporte Facturacion DHL
	 * @author Elian Amado Ramirez <elian.amado@openits.co>
	 * @package openComex
	 */

	include("../../../libs/php/utility.php");
	/**
	 *  Cookie fija
	 */
	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb   = $kDf[3];
	$kUser      = $kDf[4];
	$kLicencia  = $kDf[5];
	$swidth     = $kDf[6];

?>
<html>
	<head>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>

		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/utility.js'></script>
		<script language="javascript">
			function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				var rutaact = "frcrfini.php";
				parent.fmwork.location= rutaact;
				parent.fmnav.location='<?php echo $cPlesk_Forms_Directory_New ?>/nivel3.php';
			}

			function fnLinks(xLink,xSwitch,xIteration) {
				var zX    = screen.width;
				var zY    = screen.height;
				switch (xLink) {
					case "cSerId":
						if (xSwitch == "VALID") {
							var cRuta  = "frdes129.php?gWhat=VALID&gFunction=cSerId&gSerId="+document.forms['frnav']['cSerId'].value.toUpperCase();
							parent.fmpro.location = cRuta;
						} else {
							var zNx     = (zX-750)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = "width=750,scrollbars=1,height=250,left="+zNx+",top="+zNy;
							var cRuta   = "frdes129.php?gWhat=WINDOW&gFunction=cSerId&gSerId="+document.forms['frnav']['cSerId'].value.toUpperCase();
							var zWindow = window.open(cRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cFcoId":
						if (xSwitch == "VALID") {
							var cRuta  = "frdes130.php?gWhat=VALID&gFunction=cFcoId"+
																				"&gFcoId="+document.forms['frnav']['cFcoId'].value.toUpperCase()+
																				"&gFcoIds="+document.forms['frnav']['cFcoIds'].value.toUpperCase();
							parent.fmpro.location = cRuta;
						} else {
							var zNx     = (zX-500)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = "width=500,scrollbars=1,height=250,left="+zNx+",top="+zNy;
							var cRuta   = "frdes130.php?gWhat=WINDOW&gFunction=cFcoId"+
																				"&gFcoId="+document.forms['frnav']['cFcoId'].value.toUpperCase()+
																				"&gFcoIds="+document.forms['frnav']['cFcoIds'].value.toUpperCase();
							var zWindow = window.open(cRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cConceptoCobro":
						var zTerId  =  document.forms['frnav']['cDesId'].value.toUpperCase();
						var zNx     = (zX-580)/2;
						var zNy     = (zY-500)/2;
						var zWinPro = 'width=580,scrollbars=1,height=500,left='+zNx+',top='+zNy;
						var zRuta   = 'frcrftrn.php?cDesId='+zTerId+'&gColCtoId='+document.forms['frnav']['cColCtoId'].value;
						zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
						zWindow2.focus();
					break;
					default:
						// no hace nada
					break;
				}
			}

			function fnCargarGrillas() {
					var cRuta = "frcrfgri.php?gTipo=1&gDesId=<?php echo $cDesId ?>&gColCtoId="+document.forms['frnav']['cColCtoId'].value;
					parent.fmpro.location = cRuta;
			}

		</script>
	</head>
	<body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="400">
				<tr>
					<td>
						<fieldset>
							<legend><?php echo ucfirst(strtolower($_COOKIE['kModo']))." ".$_COOKIE['kProDes'] ?></legend>
							<form name = "frnav" action = "frcrfgra.php" method = "post" target = "fmpro">
								<!-- <input type = "text" name = "cDesId" readonly> -->
								<center>
									<table border = "0" cellpadding = "0" cellspacing = "0" width="400">
										<?php echo f_Columnas(20,20); ?>
										<tr>
											<td Class = "name" colspan = "2">Id<br>
												<input type = "text" Class = "letra" style = "width:40;text-align:center" name = "cDesId" id="cDesId" readonly>
											</td>
											<td Class = "name" colspan = "16">Descripci&oacute;n Columna<br>
												<input type = "text" Class = "letra" style = "width:320" name = "cDesCod">
											</td>
											<td Class = "name" colspan = "2">Orden<br>
												<input type = "text" Class = "letra" style = "width:40" name = "cSerId">
											</td>
										</tr>
										<tr>
											<td colspan = "20">
											<fieldset>
												<input type = "hidden" name = "cColCtoId">
												<!-- <input type = "hidden" name = "cDesPorc"> -->
												<legend>Conceptos de Cobro</legend>
												<div id = "overDivVen"></div>
											</fieldset>
											</td>
										</tr>
										<tr>
											<td Class = "name" colspan = "4">Fecha<br>
												<input type = "text" Class = "letra"  style = "width:80;text-align:center"  name = "dRegFCre"
													value = "<?php echo date('Y-m-d') ?>" readonly>
											</td>
											<td Class = "name" colspan = "4">Hora<br>
												<input type = "text" Class = "letra" style = "width:80;text-align:center" name = "tRegHCre"
													value = "<?php echo date('H:i:s')  ?>" readonly>
											</td>
											<td Class = "name" colspan = "4">Modificado<br>
												<input type = "text" Class = "letra"  style = "width:80;text-align:center"  name = "dRegFMod"
													value = "<?php echo date('Y-m-d') ?>" readonly>
											</td>
											<td Class = "name" colspan = "4">Hora<br>
												<input type = "text" Class = "letra" style = "width:80;text-align:center" name = "tRegHMod"
													value = "<?php echo date('H:i:s')  ?>" readonly>
											</td>
											<td Class = "name" colspan = "4">Estado<br>
												<input type = "text" Class = "letra" style = "width:80;text-align:center" name = "cRegEst"
													value = "ACTIVO" readonly>
											</td>
										</tr>
									</table>
								</center>
							</form>
						</fieldset>
					</td>
				</tr>
			</table>
		</center>
		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="400">
				<tr height="21">
					<?php
					switch ($_COOKIE['kModo']) {
						case "VER": ?>
							<td width="309" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_cancel_bg.gif" style="cursor:hand"
								onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
							</td>
							<?php
						break;
						default: ?>
							<td width="218" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_ok_bg.gif" style="cursor:hand"
								onClick = "javascript:document.forms['frnav'].submit()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar
							</td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_cancel_bg.gif" style="cursor:hand"
								onClick = "javascript:fnRetorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
							</td>
						<?php
						break;
					} ?>
				</tr>
			</table>
		</center>
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<?php
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
				$nMaxId = 0;
				$qMaximo  = "SELECT MAX(colidxxx) AS colidxxx ";
				$qMaximo .= "FROM $cAlfa.fpar0166 ";
				$xMaximo = f_MySql("SELECT","",$qMaximo,$xConexion01,"");
				if (mysql_num_rows($xMaximo) > 0){
					$vMaximo = mysql_fetch_array($xMaximo);
					$nMaxId = $vMaximo['colidxxx'] + 1;
				} else {
					$nMaxId = 1;
				}
			?>
				<script languaje = "javascript">
					document.forms['frnav']['cDesId'].value = "<?php echo str_pad($nMaxId, 3, "00", STR_PAD_LEFT); ?>";
					fnCargarGrillas();
				</script>
				<?php
			break;
			case "EDITAR":
				fnCargaData($gDesId); ?>
				<script languaje = "javascript">
					document.forms['frnav']['dRegFCre'].readOnly = true;
					document.forms['frnav']['tRegHCre'].readOnly = true;
					document.forms['frnav']['cRegEst'].readOnly  = true;
					document.forms['frnav']['dRegFMod'].value    = "<?php echo date('Y-m-d'); ?>";
					document.forms['frnav']['tRegHMod'].value    = "<?php echo date('H:i:s') ; ?>";
					document.forms['frnav']['cDesCod'].onfocus   = "";
					document.forms['frnav']['cDesCod'].onblur    = "";
					fnCargarGrillas();
				</script>
				<?php
			break;
			case "VER":
				fnCargaData($gDesId);
				?>
				<script languaje = "javascript">
					document.forms['frnav']['cDesId'].disabled  = true;
					document.forms['frnav']['cDesCod'].disabled = true;
					document.forms['frnav']['cSerId'].disabled  = true;
					fnCargarGrillas();
					// document.getElementById('lSerId').href = "javascript:alert('Opcion No Permitida')";
					// document.getElementById('lFcoId').href = "javascript:alert('Opcion No Permitida')";
				</script>
				<?php
			break;
		} ?>
		<?php
		function fnCargaData($xDesId) {
			global $xConexion01; global $cAlfa;
			$qDescuento  = "SELECT ";
			$qDescuento .= "$cAlfa.fpar0166.colidxxx, ";
			$qDescuento .= "$cAlfa.fpar0166.coldesxx, ";
			$qDescuento .= "$cAlfa.fpar0166.colorden, ";
			$qDescuento .= "$cAlfa.fpar0166.colctoid, ";
			$qDescuento .= "$cAlfa.fpar0166.colctode, ";
			$qDescuento .= "$cAlfa.fpar0166.regfcrex, ";
			$qDescuento .= "$cAlfa.fpar0166.reghcrex, ";
			$qDescuento .= "$cAlfa.fpar0166.regfmodx, ";
			$qDescuento .= "$cAlfa.fpar0166.reghmodx, ";
			$qDescuento .= "$cAlfa.fpar0166.regestxx ";
			// $qDescuento .= "IF($cAlfa.fpar0129.serdespx != \"\",$cAlfa.fpar0129.serdespx,$cAlfa.fpar0129.serdesxx) AS serdesxx, ";
			// $qDescuento .= "$cAlfa.fpar0129.fcoidxxx AS serfcoid, ";
			// $qDescuento .= "$cAlfa.fpar0130.fcodesxx ";
			$qDescuento .= "FROM $cAlfa.fpar0166 ";
			// $qDescuento .= "LEFT JOIN $cAlfa.fpar0129 ON $cAlfa.fpar0166.colorden = $cAlfa.fpar0129.colorden ";
			// $qDescuento .= "LEFT JOIN $cAlfa.fpar0130 ON $cAlfa.fpar0166.colctoid = $cAlfa.fpar0130.fcoidxxx ";
			$qDescuento .= "WHERE ";
			$qDescuento .= "colidxxx = \"$xDesId\" LIMIT 0,1";
			// echo $qDescuento;
			$xDescuento  = f_MySql("SELECT","",$qDescuento,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qDescuento."~".mysql_num_rows($xDescuento));
			while ($xRDE = mysql_fetch_array($xDescuento)) {
				?>
				<script language = "javascript">
					document.forms['frnav']['cDesId'].value    = "<?php echo $xRDE['colidxxx'] ?>";
					document.forms['frnav']['cDesCod'].value   = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$xRDE['coldesxx']) ?>";
					document.forms['frnav']['cSerId'].value    = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$xRDE['colorden']) ?>";
					document.forms['frnav']['cColCtoId'].value = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$xRDE['colctoid']) ?>";
					document.forms['frnav']['cDesPorc'].value = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$xRDE['colctode']) ?>";
					document.forms['frnav']['cSerDes'].value   = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$xRDE['serdesxx']) ?>";
					document.forms['frnav']['cFcoDes'].value  = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$xRDE['fcodesxx']) ?>";
					document.forms['frnav']['cFcoIds'].value  = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$xRDE['serfcoid']) ?>";
					document.forms['frnav']['dRegFCre'].value = "<?php echo $xRDE['regfcrex'] ?>";
					document.forms['frnav']['dRegFMod'].value = "<?php echo $xRDE['regfmodx'] ?>";
					document.forms['frnav']['tRegHCre'].value = "<?php echo $xRDE['reghcrex'] ?>";
					document.forms['frnav']['tRegHMod'].value = "<?php echo $xRDE['reghmodx'] ?>";
					document.forms['frnav']['cRegEst'].value  = "<?php echo $xRDE['regestxx'] ?>";
				</script>
				<?php
			}
		}
		?>
	</body>
</html>

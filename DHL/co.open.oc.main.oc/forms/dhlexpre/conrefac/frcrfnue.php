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
					case "cConceptoCobro":
						var zTerId  =  document.forms['frnav']['cColId'].value.toUpperCase();
						var zNx     = (zX-580)/2;
						var zNy     = (zY-500)/2;
						var zWinPro = 'width=580,scrollbars=1,height=500,left='+zNx+',top='+zNy;
						var zRuta   = 'frcrfcon.php?cColId='+zTerId+'&gColCtoId='+document.forms['frnav']['cColCtoId'].value;
						zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
						zWindow2.focus();
					break;
					default:
						// no hace nada
					break;
				}
			}

			function uDelCol(valor,fecha)	{
				if (confirm('ELIMINAR CONCEPTO REPORTE FACTURACION '+valor+'?'))	{
					var ruta = "frcrfcog.php?cColId=<?php echo $cColId ?>&tipsave=4&cIntId="+valor+"&cColCtoId="+document.forms['frnav']['cColCtoId'].value;
					parent.fmpro.location = ruta;
				}
			}

			function fnCargarGrillas() {
					var cRuta = "frcrfgri.php?gTipo=1&gColId=<?php echo $cColId ?>&gColCtoId="+document.forms['frnav']['cColCtoId'].value;
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
								<center>
									<table border = "0" cellpadding = "0" cellspacing = "0" width="400">
										<?php echo f_Columnas(20,20); ?>
										<tr>
											<td Class = "name" colspan = "2">Id<br>
												<input type = "text" Class = "letra" style = "width:40;text-align:center" name = "cColId" id="cColId" readonly>
											</td>
											<td Class = "name" colspan = "16">Descripci&oacute;n Columna<br>
												<input type = "text" Class = "letra" style = "width:320" name = "cColDes">
											</td>
											<td Class = "name" colspan = "2">Orden<br>
												<input type = "text" Class = "letra" style = "width:40" name = "cColOrden">
											</td>
										</tr>
										<tr>
											<td colspan = "20">
											<fieldset>
												<input type = "hidden" name = "cColCtoId">
												<legend>Conceptos de Cobro</legend>
												<div id = "overDivCto"></div>
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
					document.forms['frnav']['cColId'].value = "<?php echo str_pad($nMaxId, 3, "00", STR_PAD_LEFT); ?>";
					fnCargarGrillas();
				</script>
				<?php
			break;
			case "EDITAR":
				fnCargaData($gColId); ?>
				<script languaje = "javascript">
					document.forms['frnav']['dRegFCre'].readOnly = true;
					document.forms['frnav']['tRegHCre'].readOnly = true;
					document.forms['frnav']['cRegEst'].readOnly  = true;
					document.forms['frnav']['dRegFMod'].value    = "<?php echo date('Y-m-d'); ?>";
					document.forms['frnav']['tRegHMod'].value    = "<?php echo date('H:i:s') ; ?>";
					document.forms['frnav']['cColDes'].onfocus   = "";
					document.forms['frnav']['cColDes'].onblur    = "";
					fnCargarGrillas();
				</script>
				<?php
			break;
			case "VER":
				fnCargaData($gColId);
				?>
				<script languaje = "javascript">
					document.forms['frnav']['cColId'].disabled  = true;
					document.forms['frnav']['cColDes'].disabled = true;
					document.forms['frnav']['cColOrden'].disabled  = true;
					fnCargarGrillas();
				</script>
				<?php
			break;
		} ?>
		<?php
		function fnCargaData($xColId) {
			global $xConexion01; global $cAlfa;

			$qColumna  = "SELECT ";
			$qColumna .= "$cAlfa.fpar0166.colidxxx, ";
			$qColumna .= "$cAlfa.fpar0166.coldesxx, ";
			$qColumna .= "$cAlfa.fpar0166.colorden, ";
			$qColumna .= "$cAlfa.fpar0166.colctoid, ";
			$qColumna .= "$cAlfa.fpar0166.colctode, ";
			$qColumna .= "$cAlfa.fpar0166.regfcrex, ";
			$qColumna .= "$cAlfa.fpar0166.reghcrex, ";
			$qColumna .= "$cAlfa.fpar0166.regfmodx, ";
			$qColumna .= "$cAlfa.fpar0166.reghmodx, ";
			$qColumna .= "$cAlfa.fpar0166.regestxx ";
			$qColumna .= "FROM $cAlfa.fpar0166 ";
			$qColumna .= "WHERE ";
			$qColumna .= "colidxxx = \"$xColId\" LIMIT 0,1";
			$xColumna  = f_MySql("SELECT","",$qColumna,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qColumna."~".mysql_num_rows($xColumna));
			while ($xRCO = mysql_fetch_array($xColumna)) {
				?>
				<script language = "javascript">
					document.forms['frnav']['cColId'].value    = "<?php echo $xRCO['colidxxx'] ?>";
					document.forms['frnav']['cColDes'].value   = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$xRCO['coldesxx']) ?>";
					document.forms['frnav']['cColOrden'].value = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$xRCO['colorden']) ?>";
					document.forms['frnav']['cColCtoId'].value = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$xRCO['colctoid']) ?>";
					document.forms['frnav']['dRegFCre'].value  = "<?php echo $xRCO['regfcrex'] ?>";
					document.forms['frnav']['dRegFMod'].value  = "<?php echo $xRCO['regfmodx'] ?>";
					document.forms['frnav']['tRegHCre'].value  = "<?php echo $xRCO['reghcrex'] ?>";
					document.forms['frnav']['tRegHMod'].value  = "<?php echo $xRCO['reghmodx'] ?>";
					document.forms['frnav']['cRegEst'].value   = "<?php echo $xRCO['regestxx'] ?>";
				</script>
				<?php
			}
		}
		?>
	</body>
</html>

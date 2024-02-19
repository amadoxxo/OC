<?php
	/**
	 * Carga los Conceptos de Cobro en la Parametrica de Conceptos Reporte Facturacion DHL.
	 * @author Elian Amado Ramirez <elian.amado@openits.co>
	 */
  include("../../../libs/php/utility.php");
?>
<html>
	<head>
		<title>Conceptos de Cobro</title>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script languaje = 'javascript'>
		function fnGuardar(){
			var cadeni  = document.forms['frcotoplo']['cCadena'].value;
			if (cadeni.length > 1)	{
				var cRuta = "frcrfcog.php?cColId=<?php echo $cColId ?>&cCadena="+cadeni+"&cColCtoId="+window.opener.document.forms['frnav']['cColCtoId'].value+"&tipsave=5";
				var Msj  = fnMakeRequest(cRuta);
			}	else	{
				alert('Debe Seleccionar un Concepto de Cobro.\nVerifique.');
			}
		}

		//Funcion para cargar y validar los documentos de requisitos legales de un cliente
		function fnMakeRequest(xRuta){
			http_request = false;
			if (window.XMLHttpRequest) { // Mozilla, Safari,...
				http_request = new XMLHttpRequest();
				if (http_request.overrideMimeType) {
					http_request.overrideMimeType('text/xml');
					// Ver nota sobre esta linea al final
				}
			}else if (window.ActiveXObject) { // IE
				try {
					http_request = new ActiveXObject("Msxml2.XMLHTTP");
				} catch (e) {
					try {
						http_request = new ActiveXObject("Microsoft.XMLHTTP");
					}  catch (e) {}
				}
			}        
			if (!http_request) {
				alert('Falla :( No es posible crear una instancia XMLHTTP');
				return false;
			}
			
			http_request.onreadystatechange = fnAlertContents;
			http_request.open('GET', xRuta, true);
			http_request.send(null);
		}
			
		function fnAlertContents() {
			if(http_request.readyState==1){          
			}else if(http_request.readyState == 4) {
				if (http_request.status == 200) {
					if(http_request.responseText!=""){
						var cRetorno = http_request.responseText.replace(/^\s+|\s+$/g,"");
						var mRetorno = cRetorno.split("|");
						if (mRetorno[0] == "true") {
							window.opener.document.forms['frnav']['cColCtoId'].value = mRetorno[1];
							window.opener.fnCargarGrillas();                
							window.close();
						} else {
							alert(mRetorno[1]);
						}
					}else{
						//No Hace Nada
					}
				} else {
					alert('Hubo problemas con la peticion.');
				}
			}
		}

		function fnCo(fld){
			var cade = document.forms['frcotoplo']['cCadena'].value;
			var name = 'OFF';
			if (fld.checked == true)	{
				name = 'ON';
			}
			var otra = fld.name+',';
			if (name == 'ON')	{
				if (cade.indexOf(otra) < 0) {
					cade = cade + otra;
					document.forms['frcotoplo']['cCadena'].value = cade;
				}
			}
			if (name == 'OFF')	{
				cade = cade.replace(otra,'');
				document.forms['frcotoplo']['cCadena'].value = cade;
			}
		}
		</script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<?php
		$mCadena = explode(",",$gColCtoId);
		$mCtoCobro = array();
		for($i=0;$i<count($mCadena);$i++){
			if ($mCadena[$i] != "") {
				$mCtoCobro[count($mCtoCobro)] = $mCadena[$i];
			}
		}     
		?>
		<form name = 'frcotoplo' action = '' method = 'post' target = 'fmpro'>
			<input type = 'hidden' name = 'cCadena' value = '<?php echo $cCadena ?>' style='width:500px' readonly>
		</form>

		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="450">
				<tr>
					<td>
						<fieldset>
							<legend>Conceptos de Cobro</legend>
							<form name = "frnav" action = "" method = "post" target = "fmpro">
							<?php
								$qCtoCobro  = "SELECT ";
								$qCtoCobro .= "seridxxx,";
								$qCtoCobro .= "serdespx,";
								$qCtoCobro .= "sertopxx,";
								$qCtoCobro .= "regestxx ";																								
								$qCtoCobro .= "FROM $cAlfa.fpar0129 ";
								$qCtoCobro .= "WHERE ";
								$qCtoCobro .= "regestxx = \"ACTIVO\" ORDER BY seridxxx";
								$xCtoCobro  = f_MySql("SELECT","",$qCtoCobro,$xConexion01,"");
								if (mysql_num_rows($xCtoCobro) > 0) {
							?>
								<center>
									<table cellspacing = "0" cellpadding = "1" border = "1" width = "450">
										<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
											<td widht = "020" Class = "name"><center>ID</center></td>
											<td widht = "240" Class = "name"><center>Descripci&oacute;n Personalizada</center></td>
											<td widht = "030" Class = "name"><center>Operaci&oacute;n</center></td>
											<td widht = "020" Class = "name"><center>Estado</center></td>
											<td></td>
										</tr>
									<?php
										$y = 0;
										$cont = 0;
										while ($xRCC = mysql_fetch_array($xCtoCobro)) {
											$cvb  = 0;
											if (in_array($xRCC['seridxxx'],$mCtoCobro) == true) {
												$cvb = 1;
											}
											if ($cvb == 0)	{
												$y ++;
												$cont++;
												$zColor = "{$vSysStr['system_row_impar_color_ini']}";
												if($y % 2 == 0) {
													$zColor = "{$vSysStr['system_row_par_color_ini']}";
												}
												?>
													<tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
														<td style='width: 060px' Class = 'letra7'><?php echo $xRCC['seridxxx'] ?></td>
														<td style='width: 240px' Class = 'letra7'><?php echo substr($xRCC['serdespx'],0,45) ?></td>
														<td style='width: 080px' Class = 'letra7'><?php echo $xRCC['sertopxx'] ?></td>
														<td style='width: 050px' Class = 'letra7'><?php echo $xRCC['regestxx'] ?></td>
														<td style='width: 020px' Class = 'letra7'><center><input type = 'checkbox' style = 'width:20' name = '<?php echo $xRCC['seridxxx'] ?>' onClick ='javascript:fnCo(this)'></center></td>
													</tr>
												<?php
											}
										}
									?>
									</table>
								</center>
								<?php
									if ($cont == 0)	{
										?>
										<script languaje='javascript'>
											alert('Ya tiene asignados todos los Conceptos de Cobro Existentes.');
											window.close();
										</script>
										<?php
									}
								?>
								<center>
									<table border="0" cellpadding="0" cellspacing="0" width="450">
										<tr height="21">
											<td width="268" height="21"></td>
											<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javascript:fnGuardar()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
											<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:window.close()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
										</tr>
									</table>
								</center>
								<?php
									}	else {
										f_Mensaje(__FILE__,__LINE__,"No Se Encontraron Registros");
									}
								?>
							</form>
						</fieldset>
					</td>
				</tr>
			</table>
		</center>
	</body>
</html>
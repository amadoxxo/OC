<?php
  namespace openComex;
	/**
	 * Carga las Responsabilidades Fiscales en la ficha de Terceros.
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@open-eb.co>
	 */
  include("../../../../libs/php/utility.php");
?>
<html>
	<head>
		<title>Responsabilidades Fiscales</title>
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
				var cRuta = "frterrfg.php?cTerId=<?php echo $cTerId ?>&cCadena="+cadeni+"&cCliResFi="+window.opener.document.forms['frgrm']['cCliResFi'].value+"&tipsave=5";
				var Msj  = f_makeRequest(cRuta);
			}	else	{
				alert('Debe Seleccionar una Responsabilidad Fiscal.\nVerifique.');
			}
		}

		//Funcion para cargar y validar los documentos de requisitos legales de un cliente
			function f_makeRequest(xRuta){
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
				
				http_request.onreadystatechange = f_alertContents;
				http_request.open('GET', xRuta, true);
				http_request.send(null);
			}
			
			function f_alertContents() {
				if(http_request.readyState==1){          
				}else if(http_request.readyState == 4) {
					if (http_request.status == 200) {
						if(http_request.responseText!=""){
							var cRetorno = http_request.responseText.replace(/^\s+|\s+$/g,"");
							var mRetorno = cRetorno.split("|");
							if (mRetorno[0] == "true") {
								window.opener.document.forms['frgrm']['cCliResFi'].value = mRetorno[1];
								window.opener.fnCargarResponsabilidadFiscal();                
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

		function f_Co(fld){
			var cade = document.forms['frcotoplo']['cCadena'].value;
			var name = 'OFF';
			if (fld.checked == true)	{
				name = 'ON';
			}
			var otra = fld.name+'~';
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
		$mCadena = explode("~",$gCliResFi);
		$mResFis = array();
		for($i=0;$i<count($mCadena);$i++){
			if ($mCadena[$i] != "") {
				$mResFis[count($mResFis)] = $mCadena[$i];
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
			   			<legend>Responsabilidades Fiscales</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
								$qResFis  = "SELECT ";
	  						$qResFis .= "rfiidxxx,";
	  						$qResFis .= "rfidesxx,";
                $qResFis .= "rfiaplxx,";
	  						$qResFis .= "regestxx ";
								$qResFis .= "FROM $cAlfa.fpar0152 ";
								$qResFis .= "WHERE ";
								$qResFis .= "regestxx = \"ACTIVO\" ORDER BY rfiidxxx";
								$xResFis  = f_MySql("SELECT","",$qResFis,$xConexion01,"");

								if (mysql_num_rows($xResFis) > 0) {
									?>
									<center>
					    			<table cellspacing = "0" cellpadding = "1" border = "1" width = "450">
											<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
												<td widht = "020" Class = "name"><center></center></td>
												<td widht = "060" Class = "name"><center>C&oacute;digo</center></td>
												<td widht = "240" Class = "name"><center>Descripci&oacute;n</center></td>
                        <td widht = "080" Class = "name"><center>Aplica para</center></td>
												<td widht = "050" Class = "name"><center>Estado</center></td>
											</tr>
											<?php
											$y = 0;
											$cont = 0;
											while ($xRRF = mysql_fetch_array($xResFis)) {
											  $cvb  = 0;
                        if (in_array($xRRF['rfiidxxx'],$mResFis) == true) {
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
														<td style='width: 020px' Class = 'letra7'><center><input type = 'checkbox' style = 'width:20' name = '<?php echo $xRRF['rfiidxxx'] ?>' onClick ='javascript:f_Co(this)'></center></td>
														<td style='width: 060px' Class = 'letra7'><?php echo $xRRF['rfiidxxx'] ?></td>
														<td style='width: 240px' Class = 'letra7'><?php echo substr($xRRF['rfidesxx'],0,45) ?></td>
                            <td style='width: 080px' Class = 'letra7'><?php echo $xRRF['rfiaplxx'] ?></td>
														<td style='width: 050px' Class = 'letra7'><?php echo $xRRF['regestxx'] ?></td>
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
											alert('Ya tiene asignadas todas las Responsabilidades Fiscales Existentes.');
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
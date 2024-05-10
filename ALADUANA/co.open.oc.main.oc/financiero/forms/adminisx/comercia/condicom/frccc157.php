<?php
	/**
	 * Parametrica de Unidades de Medida
	 * @author Elian Amado <elian.amado@openits.co>
	 * @package opentecnologia
	 */
	include("../../../../libs/php/utility.php");
	// f_Mensaje(__FILE__,__LINE__,$gWhat."~".$gFunction);

?>
<?php if ($gWhat != "" && $gFunction != "") { 
	?>
	<html>
		<head>
			<title>Parametrica de Unidades de Medida </title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
			<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
			</script>
	  </head>
	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
			<center>
				<table border ="0" cellpadding="0" cellspacing="0" width="300">
					<tr>
						<td>
							<fieldset>
								<legend>Param&eacute;trica de Unidades de Medida </legend>
								<form name = "frgrm" action = "" method = "post" target = "fmpro">
									<?php
									
										switch ($gWhat) {
											case "WINDOW":
												$qUniMed  = "SELECT ";
												$qUniMed .= "umeidxxx, ";
												$qUniMed .= "umedesxx ";
												$qUniMed .= "FROM $cAlfa.fpar0157 ";
												$qUniMed .= "WHERE ";
												$qUniMed .= "umeidxxx LIKE \"%$gUmeId%\" AND ";
												$qUniMed .= "regestxx = \"ACTIVO\" ";
												$qUniMed .= "ORDER BY abs(umeidxxx) ASC";
												$xUniMed  = f_MySql("SELECT","",$qUniMed,$xConexion01,"");
												// f_Mensaje(__FILE__,__LINE__,$qUniMed."~".mysql_num_rows($xUniMed));
												
												if (mysql_num_rows($xUniMed) > 0) { ?>
													<center>
														<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
															<tr bgcolor = '#D6DFF7'>
																<td widht = "080" Class = "name"><center>C&oacute;digo</center></td>
																<td widht = "420" Class = "name"><center>Descripci&oacute;n</center></td>
															</tr>
															<?php 
															while ($xRMP = mysql_fetch_array($xUniMed)){?>
																<tr>
																	<?php
																	switch($gFunction){
																		case "cUmeId": ?>
																			<td width = "050" class= "name" style = "text-align:center">
																				<a href = "javascript:window.opener.document.forms['frgrm']['cUmeId'].value = '<?php echo $xRMP['umeidxxx']?>';
																					window.opener.f_Links('cUmeId','EXACT');
																					window.close();"><?php echo $xRMP['umeidxxx'] ?></a>
																			</td>
																			<?php
																		break;
																	}?>
																	<td width = "400" class= "name"><?php echo $xRMP['umedesxx'] ?></td>
																</tr>
																<?php 
															}?>
														</table>
													</center>
													<?php
												}else{
													f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
												}
											break;
											case "VALID":
												$qUniMed  = "SELECT ";
												$qUniMed .= "umeidxxx, ";
												$qUniMed .= "umedesxx ";
												$qUniMed .= "FROM $cAlfa.fpar0157 ";
												$qUniMed .= "WHERE ";
												$qUniMed .= "umeidxxx LIKE \"%$gUmeId%\" AND ";
												$qUniMed .= "regestxx = \"ACTIVO\" ";
												$qUniMed .= "ORDER BY abs(umeidxxx) ASC";
												$xUniMed  = f_MySql("SELECT","",$qUniMed,$xConexion01,"");
												// f_Mensaje(__FILE__,__LINE__,$qUniMed."~".mysql_num_rows($xUniMed));
												
												if (mysql_num_rows($xUniMed) > 0){
													if (mysql_num_rows($xUniMed) == 1){
														while ($xRMP = mysql_fetch_array($xUniMed)) {
															switch ($gFunction){
																case "cUmeId": ?>
																	<script language = "javascript">
																		parent.fmwork.document.forms['frgrm']['cUmeId'].value  = "<?php echo $xRMP['umeidxxx'] ?>";
																		parent.fmwork.f_Links('<?php echo $gFunction ?>','EXACT');
																	</script>
																	<?php
																break;
															}
														}
													}else{
														?>
														<script language = "javascript">
															parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
														</script>
														<?php
													}
												}else{
													switch ($gFunction){
														case "cUmeId": ?>
															<script language = "javascript">
																alert('No hay registros coincidentes');
																parent.fmwork.document.forms['frgrm']['cUmeId'].value   = "";
															</script>
															<?php
														break;
													}
												}
											break;
											case "EXACT":
												$qUniMed  = "SELECT ";
												$qUniMed .= "umeidxxx, ";
												$qUniMed .= "umedesxx ";
												$qUniMed .= "FROM $cAlfa.fpar0157 ";
												$qUniMed .= "WHERE ";
												$qUniMed .= "umeidxxx = \"$gUmeId\" AND ";
												$qUniMed .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
												$xUniMed  = f_MySql("SELECT","",$qUniMed,$xConexion01,"");
												// f_Mensaje(__FILE__,__LINE__,$qUniMed."~ ".mysql_num_rows($xUniMed));
												$vUniMed = mysql_fetch_array($xUniMed);
												switch ($gFunction){
													case "cUmeId":
														?>
														<script language = "javascript">
															parent.fmwork.document.forms['frgrm']['cUmeId'].value    = "<?php echo $vUniMed['umeidxxx'] ?>";
														</script>
														<?php
													break;
												}
											break;				
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
<?php } else {
	f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
} ?>
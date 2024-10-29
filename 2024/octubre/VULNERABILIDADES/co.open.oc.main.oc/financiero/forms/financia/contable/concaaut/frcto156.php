<?php
  namespace openComex;
	/**
	 * Parametrica de Colombia Compra Eficiente
	 * @author Juan Jose Trujillo <juan.trujillo@open-eb.co>
	 * @package opentecnologia
	 */
	include("../../../../libs/php/utility.php");
	// f_Mensaje(__FILE__,__LINE__,$gWhat."~".$gFunction);

?>
<?php if ($gWhat != "" && $gFunction != "") { 
	?>
	<html>
		<head>
			<title>Parametrica de Colombia Compra Eficiente </title>
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
								<legend>Param&eacute;trica de Colombia Compra Eficiente </legend>
								<form name = "frgrm" action = "" method = "post" target = "fmpro">
									<?php
									
										switch ($gWhat) {
											case "WINDOW":
												$qComEfi  = "SELECT ";
												$qComEfi .= "cceidxxx, ";
												$qComEfi .= "ccedesxx ";
												$qComEfi .= "FROM $cAlfa.fpar0156 ";
												$qComEfi .= "WHERE ";
												$qComEfi .= "cceidxxx LIKE \"%$gCceId%\" AND ";
												$qComEfi .= "regestxx = \"ACTIVO\" ";
												$qComEfi .= "ORDER BY abs(cceidxxx) ASC";
												$xComEfi  = f_MySql("SELECT","",$qComEfi,$xConexion01,"");
												// f_Mensaje(__FILE__,__LINE__,$qComEfi."~".mysql_num_rows($xComEfi));
												
												if (mysql_num_rows($xComEfi) > 0) { ?>
													<center>
														<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
															<tr bgcolor = '#D6DFF7'>
																<td widht = "100" Class = "name"><center>C&oacute;digo</center></td>
																<td widht = "420" Class = "name"><center>Descripci&oacute;n</center></td>
															</tr>
															<?php 
															while ($xRMP = mysql_fetch_array($xComEfi)){?>
																<tr>
																	<?php
																	switch($gFunction){
																		case "cCceId": ?>
																			<td width = "100" class= "name" style = "text-align:center">
																				<a href = "javascript:window.opener.document.forms['frgrm']['cCceId'].value = '<?php echo $xRMP['cceidxxx']?>';
																					window.opener.f_Links('cCceId','EXACT');
																					window.close();"><?php echo $xRMP['cceidxxx'] ?></a>
																			</td>
																			<?php
																		break;
																	}?>
																	<td width = "400" class= "name"><?php echo $xRMP['ccedesxx'] ?></td>
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
												$qComEfi  = "SELECT ";
												$qComEfi .= "cceidxxx, ";
												$qComEfi .= "ccedesxx ";
												$qComEfi .= "FROM $cAlfa.fpar0156 ";
												$qComEfi .= "WHERE ";
												$qComEfi .= "cceidxxx LIKE \"%$gCceId%\" AND ";
												$qComEfi .= "regestxx = \"ACTIVO\" ";
												$qComEfi .= "ORDER BY abs(cceidxxx) ASC";
												$xComEfi  = f_MySql("SELECT","",$qComEfi,$xConexion01,"");
												// f_Mensaje(__FILE__,__LINE__,$qComEfi."~".mysql_num_rows($xComEfi));
												
												if (mysql_num_rows($xComEfi) > 0){
													if (mysql_num_rows($xComEfi) == 1){
														while ($xRMP = mysql_fetch_array($xComEfi)) {
															switch ($gFunction){
																case "cCceId": ?>
																	<script language = "javascript">
																		parent.fmwork.document.forms['frgrm']['cCceId'].value  = "<?php echo $xRMP['cceidxxx'] ?>";
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
														case "cCceId": ?>
															<script language = "javascript">
																alert('No hay registros coincidentes');
																parent.fmwork.document.forms['frgrm']['cCceId'].value   = "";
															</script>
															<?php
														break;
													}
												}
											break;
											case "EXACT":
												$qComEfi  = "SELECT ";
												$qComEfi .= "cceidxxx, ";
												$qComEfi .= "ccedesxx ";
												$qComEfi .= "FROM $cAlfa.fpar0156 ";
												$qComEfi .= "WHERE ";
												$qComEfi .= "cceidxxx = \"$gCceId\" AND ";
												$qComEfi .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
												$xComEfi  = f_MySql("SELECT","",$qComEfi,$xConexion01,"");
												// f_Mensaje(__FILE__,__LINE__,$qComEfi."~ ".mysql_num_rows($xComEfi));
												$vComEfi = mysql_fetch_array($xComEfi);
												switch ($gFunction){
													case "cCceId":
														?>
														<script language = "javascript">
															parent.fmwork.document.forms['frgrm']['cCceId'].value  = "<?php echo $vComEfi['cceidxxx'] ?>";
															parent.fmwork.document.forms['frgrm']['cCceDes'].value = "<?php echo str_replace('"','\"',$vComEfi['ccedesxx']) ?>";
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
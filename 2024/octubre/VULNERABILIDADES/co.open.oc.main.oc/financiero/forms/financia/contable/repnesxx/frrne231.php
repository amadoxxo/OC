<?php 
  namespace openComex;
	include("../../../../libs/php/utility.php");
	if ($gModo != "" && $gFunction != ""){ 
	?>
	<html>
		<head>
			<title>Tipo de Bien</title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
			<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
	  </head>
	  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
	  <center>
			<table border = "0" cellpadding = "0" cellspacing = "0" width = "550">
				<tr>
					<td>
						<fieldset>
			   			<legend>Tipo de Bien</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gModo) {
	  								case "VALID":
	  									$qTipBien  = "SELECT * ";
											$qTipBien .= "FROM $cAlfa.SIAI0231 ";
	  									switch ($gFunction) {
	  										case "cTdbId":
	  											$qTipBien .= "WHERE ";
													$qTipBien .= "TDBIDXXX = \"$gTdbId\" AND ";
													$qTipBien .= "CLIIDXXX = \"$gCliId\" ";
	  										break;
	  										case "cTdbDes":
	  											$qTipBien .= "WHERE ";
													$qTipBien .= "TDBDESXX = \"$gTdbDes\" AND ";
													$qTipBien .= "CLIIDXXX = \"$gCliId\" ";
													$qTipBien .= "ORDER BY TDBDESXX";
	  										break;
	  									}
			  							$xTipBien  = f_MySql("SELECT","",$qTipBien,$xConexion01,"");

	  									if (mysql_num_rows($xTipBien) == 1) {
	  										$vTipBien = mysql_fetch_array($xTipBien); ?>
												<script languaje = "javascript">
                        	parent.fmwork.document.forms['frgrm']['cTdbId'].value  = "<?php echo $vTipBien['TDBIDXXX'] ?>";
                          parent.fmwork.document.forms['frgrm']['cTdbDes'].value = "<?php echo $vTipBien['TDBDESXX'] ?>";
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
		  								$qTipBien  = "SELECT * ";
											$qTipBien .= "FROM $cAlfa.SIAI0231 ";
											switch ($gFunction) {
	  										case "cTdbId":
	  											$qTipBien .= "WHERE ";
													$qTipBien .= "TDBIDXXX LIKE \"%$gTdbId%\" AND ";
													$qTipBien .= "CLIIDXXX = \"$gCliId\" ";
													$qTipBien .= "ORDER BY TDBIDXXX";
	  										break;
	  										case "cTdbDes":
	  											$qTipBien .= "WHERE ";
													$qTipBien .= "TDBDESXX LIKE \"%$gTdbDes%\" AND ";
													$qTipBien .= "CLIIDXXX = \"$gCliId\" ";
													$qTipBien .= "ORDER BY TDBDESXX";
	  										break;
	  									}
	  									$xTipBien  = f_MySql("SELECT","",$qTipBien,$xConexion01,"");

 											if (mysql_num_rows($xTipBien) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "550">
														<tr>
															<td width = "50" Class = "name"><center>Codigo</center></td>
															<td width = "400" Class = "name"><center>Tipo Bien</center></td>
															<td width = "100" Class = "name"><center>Estado</center></td>
														</tr>
														<?php while ($xRTB = mysql_fetch_array($xTipBien)) {
															if (mysql_num_rows($xTipBien) > 0) { ?>
																<tr>
																	<td Class = "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cTdbId'].value  = '<?php echo $xRTB['TDBIDXXX'] ?>';
																										 			window.opener.document.forms['frgrm']['cTdbDes'].value = '<?php echo $xRTB['TDBDESXX'] ?>';
																							 						window.close()"><?php echo $xRTB['TDBIDXXX'] ?>
																		</a>
																	</td>
																	<td Class = "name"><?php echo $xRTB['TDBDESXX'] ?></td>
																	<td Class = "name" align="center"><?php echo $xRTB['REGESTXX'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
    															window.opener.document.forms['frgrm']['cTdbId'].value  = "<?php echo $xRTB['TDBIDXXX'] ?>";
    															window.opener.document.forms['frgrm']['cTdbDes'].value = "<?php echo $xRTB['TDBDESXX'] ?>";
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else {
		 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique."); ?>
												<script languaje="javascript">
													window.opener.document.forms['frgrm']['cTdbId'].value  = "";
													window.opener.document.forms['frgrm']['cTdbDes'].value = "";
													window.close();
												</script>
											<?php
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
	f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos, Verifique.");
} ?>
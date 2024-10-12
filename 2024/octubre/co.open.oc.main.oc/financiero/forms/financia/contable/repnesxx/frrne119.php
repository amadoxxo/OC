<?php 
  namespace openComex;
	include("../../../../libs/php/utility.php");
	if ($gModo != "" && $gFunction != ""){ 
	?>
	<html>
		<head>
			<title>Sucursal</title>
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
			   			<legend>Sucursal</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gModo) {
	  								case "VALID":
	  									$qDatSuc  = "SELECT * ";
											$qDatSuc .= "FROM $cAlfa.SIAI0119 ";
	  									switch ($gFunction) {
	  										case "cSucId":
	  											$qDatSuc .= "WHERE ";
													$qDatSuc .= "LINIDXXX like \"%$gSucId%\" ";
	  										break;
	  										case "cSucDes":
	  											$qDatSuc .= "WHERE ";
													$qDatSuc .= "LINDESXX LIKE \"%$gSucDes%\" ";
													$qDatSuc .= "ORDER BY LINDESXX";
	  										break;
	  									}
			  							$xDatSuc  = f_MySql("SELECT","",$qDatSuc,$xConexion01,"");

	  									if (mysql_num_rows($xDatSuc) == 1) {
	  										$vDatSuc = mysql_fetch_array($xDatSuc); ?>
												<script languaje = "javascript">
										    	parent.fmwork.document.forms['frgrm']['cSucId'].value  = "<?php echo $vDatSuc['LINIDXXX'] ?>";
                          parent.fmwork.document.forms['frgrm']['cSucDes'].value = "<?php echo $vDatSuc['LINDESXX'] ?>";
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
		  								$qDatSuc  = "SELECT * ";
											$qDatSuc .= "FROM $cAlfa.SIAI0119 ";
											switch ($gFunction) {
	  										case "cSucId":
	  											$qDatSuc .= "WHERE ";
													$qDatSuc .= "LINIDXXX LIKE \"%$gSucId%\" ";
													$qDatSuc .= "ORDER BY LINIDXXX";
	  										break;
	  										case "cSucDes":
	  											$qDatSuc .= "WHERE ";
													$qDatSuc .= "LINDESXX LIKE \"%$gSucDes%\" ";
													$qDatSuc .= "ORDER BY LINDESXX";
	  										break;
	  									}
	  									$xDatSuc  = f_MySql("SELECT","",$qDatSuc,$xConexion01,"");

 											if (mysql_num_rows($xDatSuc) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "550">
														<tr>
															<td width = "50" Class = "name"><center>Codigo</center></td>
															<td width = "400" Class = "name"><center>Sucursal</center></td>
															<td width = "100" Class = "name"><center>Estado</center></td>
														</tr>
														<?php while ($xRDS = mysql_fetch_array($xDatSuc)) {
															if (mysql_num_rows($xDatSuc) > 0) { ?>
																<tr>
																	<td Class = "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cSucId'].value  = '<?php echo $xRDS['LINIDXXX'] ?>';
																										 			window.opener.document.forms['frgrm']['cSucDes'].value = '<?php echo $xRDS['LINDESXX'] ?>';
																							 						window.close()"><?php echo $xRDS['LINIDXXX'] ?>
																		</a>
																	</td>
																	<td Class = "name"><?php echo $xRDS['LINDESXX'] ?></td>
																	<td Class = "name" align="center"><?php echo $xRDS['REGESTXX'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
    															window.opener.document.forms['frgrm']['cSucId'].value  = "<?php echo $xRDS['LINIDXXX'] ?>";
    															window.opener.document.forms['frgrm']['cSucDes'].value = "<?php echo $xRDS['LINDESXX'] ?>";
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else {
		 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique."); ?>
												<script languaje="javascript">
													window.opener.document.forms['frgrm']['cSucId'].value  = "";
													window.opener.document.forms['frgrm']['cSucDes'].value = "";
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
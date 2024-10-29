<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  $qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_NAME = \"SIAI0119\" LIMIT 0,1";
  $xTblCom = f_MySql("SELECT","",$qTblCom,$xConexion01,"");
  $vTblCom = mysql_fetch_array($xTblCom);
//f_Mensaje(__FILE__,__LINE__,$gModo." ~ ".$gFunction." ~ ".$gLinId);
if ($gModo != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title><?php echo $vTblCom['TABLE_COMMENT'] ?></title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
	  </head>
	  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
	  <center>
			<table border = "0" cellpadding = "0" cellspacing = "0" width = "550">
				<tr>
					<td>
						<fieldset>
			   			<legend><?php echo $vTblCom['TABLE_COMMENT'] ?></legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gModo) {
	  								case "VALID":
	  									$qDatExt  = "SELECT ";
	  									$qDatExt .= "SIAI0119.LINIDXXX,";
                	    $qDatExt .= "SIAI0119.LINDESXX ";
											$qDatExt .= "FROM $cAlfa.SIAI0119 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "SUCCOMXX = \"SI\" AND ";
											$qDatExt .= "LINIDXXX = \"$gLinId\" AND ";
											$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY LINIDXXX LIMIT 0,1";
			  							$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
			  							//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

	  									if (mysql_num_rows($xDatExt) == 1) {
	  										$vDatExt = mysql_fetch_array($xDatExt); ?>
												<script languaje = "javascript">
                          parent.fmwork.document.forms['frgrm']['cLinId'].value  = "<?php echo $vDatExt['LINIDXXX'] ?>";
  												parent.fmwork.document.forms['frgrm']['cLinDes'].value = "<?php echo $vDatExt['LINDESXX'] ?>";
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
		  								$qDatExt  = "SELECT  ";
											$qDatExt .= "SIAI0119.LINIDXXX,";
                	    $qDatExt .= "SIAI0119.LINDESXX ";
											$qDatExt .= "FROM $cAlfa.SIAI0119 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "SUCCOMXX = \"SI\" AND ";
											$qDatExt .= "LINIDXXX LIKE \"%$gLinId%\" AND ";
  										$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY LINIDXXX";
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

 											if (mysql_num_rows($xDatExt) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "550">
														<tr>
															<td widht = "050" Class = "name"><center>Id</center></td>
															<td widht = "500" Class = "name"><center>Sucursal</center></td>
														</tr>
														<?php while ($xRDE = mysql_fetch_array($xDatExt)) {
															if (mysql_num_rows($xDatExt) > 0) { ?>
																<tr>
																	<td width = "030" Class = "name">
																		<a href = "javascript:
                															 window.opener.document.forms['frgrm']['cLinId'].value  = '<?php echo $xRDE['LINIDXXX'] ?>';
																							 window.opener.document.forms['frgrm']['cLinDes'].value = '<?php echo $cNombre = ($xRDE['LINDESXX'] != "") ? $xRDE['LINDESXX'] : trim($xRDE['CLIAPE1X']." ".$xRDE['CLIAPE2X']." ".$xRDE['CLINOM1X']." ".$xRDE['CLINOM2X']); ?>';
																							 window.close()"><?php echo $xRDE['LINIDXXX'] ?>
																		</a>
																	</td>
																	<td width = "390" Class = "name"><?php echo $xRDE['LINDESXX'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cLinId'].value  = "<?php echo $xRDE['LINIDXXX'] ?>";
																	window.opener.document.forms['frgrm']['cLinDes'].value = "<?php echo $cNombre = ($xRDE['LINDESXX'] != "") ? $xRDE['LINDESXX'] : trim($xRDE['CLIAPE1X']." ".$xRDE['CLIAPE2X']." ".$xRDE['CLINOM1X']." ".$xRDE['CLINOM2X']); ?>";
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else {
		 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique."); ?>
		 										<script languaje="javascript">
													window.opener.document.forms['frgrm']['cLinId'].value  = "";
													window.opener.document.forms['frgrm']['cLinDes'].value = "";
													window.close();
												</script>
		 									<?php }
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
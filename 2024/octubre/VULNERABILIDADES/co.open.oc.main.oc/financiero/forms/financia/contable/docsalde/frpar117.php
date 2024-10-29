<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  $qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_NAME = \"fpar0117\" LIMIT 0,1";
  $xTblCom = f_MySql("SELECT","",$qTblCom,$xConexion01,"");
  $vTblCom = mysql_fetch_array($xTblCom);
  //f_Mensaje(__FILE__,__LINE__,$vTblCom['TABLE_COMMENT']);
if ($gModo != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title><?php echo $vTblCom['TABLE_COMMENT'] ?></title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
	  </head>
	  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
	  <center>
			<table border = "0" cellpadding = "0" cellspacing = "0" width = "450">
				<tr>
					<td>
						<fieldset>
			   			<legend><?php echo $vTblCom['TABLE_COMMENT'] ?></legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gModo) {
	  								case "VALID":
	  									$qDatExt  = "SELECT * ";
											$qDatExt .= "FROM $cAlfa.fpar0117 ";
											$qDatExt .= "WHERE comidxxx = \"$gComId\" AND ";
											$qDatExt .= "comcodxx = \"$gComCod\" AND ";
											$qDatExt .= "regestxx = \"ACTIVO\" ORDER BY comdesxx LIMIT 0,1";
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
	  									if (mysql_num_rows($xDatExt) == 1) {
	  										$vDatExt = mysql_fetch_array($xDatExt); ?>
												<script languaje = "javascript">
													parent.fmwork.document.forms['frgrm']['cComId'].value  = "<?php echo $vDatExt['comidxxx'] ?>";
													parent.fmwork.document.forms['frgrm']['cComCod'].value = "<?php echo $vDatExt['comcodxx'] ?>";
													parent.fmwork.document.forms['frgrm']['cComDes'].value = "<?php echo $vDatExt['comdesxx'] ?>";
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
		  								$qDatExt  = "SELECT * ";
											$qDatExt .= "FROM $cAlfa.fpar0117 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "comidxxx LIKE \"%$gComId%\" AND ";
											$qDatExt .= "comcodxx LIKE \"%$gComCod%\" AND ";
											$qDatExt .= "regestxx = \"ACTIVO\" ORDER BY comdesxx";
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
 											if (mysql_num_rows($xDatExt) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "450">
														<tr>
															<td widht = "030" Class = "name"><center>Com</center></td>
															<td widht = "030" Class = "name"><center>Cod</center></td>
															<td widht = "390" Class = "name"><center>Descripcion</center></td>
														</tr>
														<?php while ($xRDE = mysql_fetch_array($xDatExt)) {
															if (mysql_num_rows($xDatExt) > 1) { ?>
																<tr>
																	<td width = "030" Class = "name"><?php echo $xRDE['comidxxx'] ?></td>
																	<td width = "030" Class = "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cComId'].value  = '<?php echo $xRDE['comidxxx'] ?>';
																													window.opener.document.forms['frgrm']['cComCod'].value = '<?php echo $xRDE['comcodxx'] ?>';
																													window.opener.document.forms['frgrm']['cComDes'].value = '<?php echo $xRDE['comdesxx'] ?>';
																													window.close()"><?php echo $xRDE['comcodxx'] ?>
																		</a>
																	</td>
																	<td width = "390" Class = "name"><?php echo $xRDE['comdesxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cComId'].value  = '<?php echo $xRDE['comidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cComCod'].value = '<?php echo $xRDE['comcodxx'] ?>';
																	window.opener.document.forms['frgrm']['cComDes'].value = '<?php echo $xRDE['comdesxx'] ?>';
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else {
		 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique.");
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
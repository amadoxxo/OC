<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  $qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_NAME = \"fpar0119\" LIMIT 0,1";
  $xTblCom = f_MySql("SELECT","",$qTblCom,$xConexion01,"");
  $vTblCom = mysql_fetch_array($xTblCom);
  
  //f_Mensaje(__FILE__,__LINE__,"gWhat->".$gWhat." ~ gFunction->".$gFunction." ~ gCliTpCto->".$gCliTpCto);

  if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title><?php echo $vTblCom['TABLE_COMMENT'] ?></title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
			<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
	  </head>
	  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
	  <center>
			<table border = "0" cellpadding = "0" cellspacing = "0" width = "400">
				<tr>
					<td>
						<fieldset>
			   			<legend><?php echo $vTblCom['TABLE_COMMENT'] ?></legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "VALID":
	  									$qDatExt  = "SELECT pucidxxx, ctoidxxx, IF(ctodesxp != \"\",ctodesxp,ctodesxx) AS ctodesxp ";
			  							$qDatExt .= "FROM $cAlfa.fpar0119 ";
			  							$qDatExt .= "WHERE ";
			  							$qDatExt .= "$cAlfa.fpar0119.ctocomxx LIKE \"%|P~%\" AND ";
			  							$qDatExt .= "$cAlfa.fpar0119.ctoidxxx LIKE \"%$gCliTpCto%\" AND ";
			  							$qDatExt .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
			  							$qDatExt .= "ORDER BY $cAlfa.fpar0119.pucidxxx,$cAlfa.fpar0119.ctoidxxx ";
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
											if (mysql_num_rows($xDatExt) == 1) {
												$xRDE = mysql_fetch_array($xDatExt); ?>
												<script languaje = "javascript">
													parent.fmwork.document.forms['frgrm']['cCliTpCto'].value = "<?php echo $xRDE['ctoidxxx']?>";
													parent.fmwork.document.forms['frgrm']['cCliTpDes'].value = "<?php echo $xRDE['ctodesxp']?>";
		                      parent.fmwork.document.forms['frgrm']['cCliTpPuc'].value = "<?php echo $xRDE['pucidxxx']?>";
												</script>
											<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
											$qDatExt  = "SELECT pucidxxx, ctoidxxx, IF(ctodesxp != \"\",ctodesxp,ctodesxx) AS ctodesxp ";
			  							$qDatExt .= "FROM $cAlfa.fpar0119 ";
			  							$qDatExt .= "WHERE ";
			  							$qDatExt .= "$cAlfa.fpar0119.ctocomxx LIKE \"%|P~%\" AND ";
			  							$qDatExt .= "$cAlfa.fpar0119.ctoidxxx LIKE \"%$gCliTpCto%\" AND ";
			  							$qDatExt .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
			  							$qDatExt .= "ORDER BY $cAlfa.fpar0119.pucidxxx,$cAlfa.fpar0119.ctoidxxx ";
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
 											if (mysql_num_rows($xDatExt) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "400">
														<tr>
															<td widht = "050" Class = "name"><center>Cto</center></td>
															<td widht = "300" Class = "name"><center>Descripcion</center></td>
															<td widht = "050" Class = "name"><center>Cuenta</center></td>
														</tr>
														<?php while ($xRDE = mysql_fetch_array($xDatExt)) {
															if (mysql_num_rows($xDatExt) > 1) { ?>
																<tr>
																	<td Class = "name"><?php echo $xRDE['ctoidxxx'] ?></td>
																	<td Class = "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cCliTpCto'].value = '<?php echo $xRDE['ctoidxxx']?>';
																	 												window.opener.document.forms['frgrm']['cCliTpDes'].value = '<?php echo $xRDE['ctodesxp']?>';
																	 												window.opener.document.forms['frgrm']['cCliTpPuc'].value = '<?php echo $xRDE['pucidxxx']?>';
																	                        window.close()"><?php echo $xRDE['ctodesxp'] ?></a>
																	</td>
																	<td class= "name"><?php echo $xRDE['pucidxxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cCliTpCto'].value = "<?php echo $xRDE['ctoidxxx']?>";
					 												window.opener.document.forms['frgrm']['cCliTpDes'].value = "<?php echo $xRDE['ctodesxp']?>";
					 												window.opener.document.forms['frgrm']['cCliTpPuc'].value = "<?php echo $xRDE['pucidxxx']?>";
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else {
		 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique."); ?>
		 										<script languaje = "javascript">
		 											window.opener.document.forms['frgrm']['cCliTpCto'].value = "";
		 											window.opener.document.forms['frgrm']['cCliTpDes'].value = "";
		 											window.opener.document.forms['frgrm']['cCliTpPuc'].value = "";
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
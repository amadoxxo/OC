<?php 
  namespace openComex;
	include("../../../../libs/php/utility.php");
  if ($gModo != "" && $gFunction != "") { 
?>
	<html>
		<head>
			<title>Importador</title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<!--<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">-->
			<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
	  </head>
	  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
	  <center>
			<table border = "0" cellpadding = "0" cellspacing = "0" width = "550">
				<tr>
					<td>
						<fieldset>
			   			<legend>Importador</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gModo) {
	  								case "VALID":
	  									$qDatImp  = "SELECT CLIIDXXX, ";
	  									$qDatImp .= "IF(CLINOMCX <> \"\",CLINOMCX,IF(CLINOMXX <> \"\",CLINOMXX,CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))) AS CLINOMXX ";
											$qDatImp .= "FROM $cAlfa.SIAI0150 ";
											$qDatImp .= "WHERE ";
											$qDatImp .= "$gCliTip = \"SI\" AND ";
	  									switch ($gFunction) {
	  										case "cCliId":
													$qDatImp .= "CLIIDXXX = \"$gCliId\" AND ";
													$qDatImp .= "CLIIDXXX IN (\"830050346\",\"860002130\") AND ";
													$qDatImp .= "REGESTXX = \"ACTIVO\" ORDER BY CLIIDXXX LIMIT 0,1";
	  										break;
	  										case "cCliNom":
													$qDatImp .= "CLINOMXX LIKE \"%$gCliNom%\" AND ";
													$qDatImp .= "CLIIDXXX IN (\"830050346\",\"860002130\") AND ";
													$qDatImp .= "REGESTXX = \"ACTIVO\" ORDER BY CLINOMXX";
	  										break;
	  									}
			  							$xDatImp  = f_MySql("SELECT","",$qDatImp,$xConexion01,"");
			  							//f_Mensaje(__FILE__,__LINE__,$qDatImp." ~ ".mysql_num_rows($xDatImp));

	  									if (mysql_num_rows($xDatImp) == 1) {
	  										$vDatImp = mysql_fetch_array($xDatImp); ?>
												<script languaje = "javascript">
                        	parent.fmwork.document.forms['frgrm']['cCliId'].value  = "<?php echo $vDatImp['CLIIDXXX'] ?>";
    											parent.fmwork.document.forms['frgrm']['cCliDV'].value  = "<?php echo f_Digito_Verificacion($vDatImp['CLIIDXXX']) ?>";
                          parent.fmwork.document.forms['frgrm']['cCliNom'].value = "<?php echo $vDatImp['CLINOMXX'] ?>";
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
		  								$qDatImp  = "SELECT CLIIDXXX, ";
	  									$qDatImp .= "IF(CLINOMCX <> \"\",CLINOMCX,IF(CLINOMXX <> \"\",CLINOMXX,CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))) AS CLINOMXX ";
											$qDatImp .= "FROM $cAlfa.SIAI0150 ";
											$qDatImp .= "WHERE ";
											$qDatImp .= "$gCliTip = \"SI\" AND ";
											switch ($gFunction) {
	  										case "cCliId":
													$qDatImp .= "CLIIDXXX LIKE \"%$gCliId%\" AND ";
													$qDatImp .= "CLIIDXXX IN (\"830050346\",\"860002130\") AND ";
													$qDatImp .= "REGESTXX = \"ACTIVO\" ORDER BY CLIIDXXX";
	  										break;
	  										case "cCliNom":
													$qDatImp .= "CLINOMXX LIKE \"%$gCliNom%\" AND ";
													$qDatImp .= "CLIIDXXX IN (\"830050346\",\"860002130\") AND ";
													$qDatImp .= "REGESTXX = \"ACTIVO\" ORDER BY CLINOMXX";
	  										break;
	  									}
	  									$xDatImp  = f_MySql("SELECT","",$qDatImp,$xConexion01,"");
											//f_Mensaje(__FILE__,__LINE__,$qDatImp." ~ ".mysql_num_rows($xDatImp));
 											if (mysql_num_rows($xDatImp) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "550">
														<tr>
															<td width = "100" Class = "name"><center>Nit</center></td>
															<td widht = "350" Class = "name"><center>Nombre</center></td>
															<td widht = "100" Class = "name"><center>Estado</center></td>
														</tr>
														<?php while ($xRDE = mysql_fetch_array($xDatImp)) {
															if (mysql_num_rows($xDatImp) > 0) { ?>
																<tr>
																	<td Class = "name">
																		<a href = "javascript:
															                   switch ('<?php echo $gFunction ?>') {
															                     case 'cCliId':
												                           case 'cCliNom':
                      															 window.opener.document.forms['frgrm']['cCliId'].value  = '<?php echo $xRDE['CLIIDXXX'] ?>';
                      															 window.opener.document.forms['frgrm']['cCliDV'].value  = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
																										 window.opener.document.forms['frgrm']['cCliNom'].value = '<?php echo $xRDE['CLINOMXX'] ?>';
																									 break;
															                   }
																							 window.close()"><?php echo $xRDE['CLIIDXXX'] ?>
																		</a>
																	</td>
																	<td Class = "name"><?php echo $xRDE['CLINOMXX'] ?></td>
																	<td Class = "name"><?php echo $xRDE['REGESTXX'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
    															window.opener.document.forms['frgrm']['cCliId'].value  = "<?php echo $xRDE['CLIIDXXX'] ?>";
    															window.opener.document.forms['frgrm']['cCliDV'].value  = "<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>";
    															window.opener.document.forms['frgrm']['cCliNom'].value = "<?php echo $xRDE['CLINOMXX'] ?>";
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else {
		 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique."); ?>
												<script languaje="javascript">
													window.opener.document.forms['frgrm']['cCliId'].value  = "";
													window.opener.document.forms['frgrm']['cCliDV'].value  = "";
													window.opener.document.forms['frgrm']['cCliNom'].value = "";
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
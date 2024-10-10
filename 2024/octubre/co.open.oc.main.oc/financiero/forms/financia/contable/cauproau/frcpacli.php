<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  $qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_NAME = \"SIAI0150\" LIMIT 0,1";
  $xTblCom = f_MySql("SELECT","",$qTblCom,$xConexion01,"");
  $vTblCom = mysql_fetch_array($xTblCom);
//f_Mensaje(__FILE__,__LINE__,$gModo." ~ ".$gFunction." ~ ".$gComCod);
if ($gModo != "" && $gFunction != "") { ?>
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
			<table border = "0" cellpadding = "0" cellspacing = "0" width = "550">
				<tr>
					<td>
						<fieldset>
			   			<legend><?php echo $vTblCom['TABLE_COMMENT'] ?></legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gModo) {
	  								case "VALID":
	  									$qDatExt  = "SELECT *, ";
											$qDatExt .= "IF(CLINOMXX <> \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
											$qDatExt .= "FROM $cAlfa.SIAI0150 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "$gTerTip = \"SI\" AND ";
	  									switch ($gFunction) {
	  										case "cTerId":
	  										case "cTerIdB":
													$qDatExt .= "CLIIDXXX = \"$gTerId\" AND ";
													$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY CLIIDXXX LIMIT 0,1";
	  										break;
	  										case "cTerNom":
	  										case "cTerNomB":
													$qDatExt .= "CLINOMXX LIKE \"%$gTerNom%\" AND ";
													$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY CLINOMXX";
	  										break;
	  									}
			  							$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
			  							//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

	  									if (mysql_num_rows($xDatExt) == 1) {
	  										$vDatExt = mysql_fetch_array($xDatExt); 
	  										?>
												<script languaje = "javascript">
												  switch ("<?php echo $gFunction ?>") {
												    case "cTerId":
												    case "cTerNom":
                              parent.framework.document.forms['frgrm']['cTerId'].value  = "<?php echo $vDatExt['CLIIDXXX'] ?>";
    													parent.framework.document.forms['frgrm']['cTerDV'].value  = "<?php echo f_Digito_Verificacion($vDatExt['CLIIDXXX']) ?>";
    													parent.framework.document.forms['frgrm']['cTerNom'].value = "<?php echo $vDatExt['CLINOMXX'] ?>";   													
												    break;
												    case "cTerIdB":
												    case "cTerNomB":
  												    parent.framework.document.forms['frgrm']['cTerIdB'].value  = "<?php echo $vDatExt['CLIIDXXX'] ?>";
    													parent.framework.document.forms['frgrm']['cTerDVB'].value  = "<?php echo f_Digito_Verificacion($vDatExt['CLIIDXXX']) ?>";
    													parent.framework.document.forms['frgrm']['cTerNomB'].value = "<?php echo $vDatExt['CLINOMXX'] ?>";
												    break;
												  }
												  parent.framework.f_Enabled_Combos();
												  parent.framework.document.forms['frgrm'].submit();
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.framework.f_Links("<?php echo $gFunction ?>","WINDOW");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
		  								$qDatExt  = "SELECT *, ";
											$qDatExt .= "IF(CLINOMXX <> \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
											$qDatExt .= "FROM $cAlfa.SIAI0150 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "$gTerTip = \"SI\" AND ";
											switch ($gFunction) {
	  										case "cTerId":
	  										case "cTerIdB":
													$qDatExt .= "CLIIDXXX LIKE \"%$gTerId%\" AND ";
													$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY CLIIDXXX";
	  										break;
	  										case "cTerNom":
	  										case "cTerNomB":
													$qDatExt .= "CLINOMXX LIKE \"%$gTerNom%\" AND ";
													$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY CLINOMXX";
	  										break;
	  									}
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

 											if (mysql_num_rows($xDatExt) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "550">
														<tr>
															<td widht = "050" Class = "name"><center>Nit</center></td>
															<td widht = "500" Class = "name"><center>Nombre</center></td>
														</tr>
														<?php while ($xRDE = mysql_fetch_array($xDatExt)) {
															if (mysql_num_rows($xDatExt) > 1) { ?>
																<tr>
																	<td width = "030" Class = "name">
																		<a href = "javascript:
															                   switch ('<?php echo $gFunction ?>') {
															                     case 'cTerId':
												                           case 'cTerNom':
                      															 window.opener.document.forms['frgrm']['cTerId'].value  = '<?php echo $xRDE['CLIIDXXX'] ?>';
																										 window.opener.document.forms['frgrm']['cTerDV'].value  = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
																										 window.opener.document.forms['frgrm']['cTerNom'].value = '<?php echo $xRDE['CLINOMXX'] ?>';                              
																									 break;
																									 case 'cTerIdB':
												                           case 'cTerNomB':
                                                     window.opener.document.forms['frgrm']['cTerIdB'].value   = '<?php echo $xRDE['CLIIDXXX'] ?>';
																										 window.opener.document.forms['frgrm']['cTerDVB'].value   = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
																										 window.opener.document.forms['frgrm']['cTerNomB'].value  = '<?php echo $xRDE['CLINOMXX'] ?>';
												                           break;
															                   }
															                  window.opener.f_Enabled_Combos();
															                  window.opener.document.forms['frgrm'].submit();
																							 window.close()"><?php echo $xRDE['CLIIDXXX'] ?>
																		</a>
																	</td>
																	<td width = "390" Class = "name"><?php echo $xRDE['CLINOMXX'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
  																switch ("<?php echo $gFunction ?>") {
        												    case "cTerId":
        												    case "cTerNom":
    																	window.opener.document.forms['frgrm']['cTerId'].value  = "<?php echo $xRDE['CLIIDXXX'] ?>";
    																	window.opener.document.forms['frgrm']['cTerDV'].value  = "<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>";
    																	window.opener.document.forms['frgrm']['cTerNom'].value = "<?php echo $xRDE['CLINOMXX'] ?>";    																
    																break;
    																case "cTerIdB":
        												    case "cTerNomB":
        												      window.opener.document.forms['frgrm']['cTerIdB'].value  = "<?php echo $xRDE['CLIIDXXX'] ?>";
    																	window.opener.document.forms['frgrm']['cTerDVB'].value  = "<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>";
    																	window.opener.document.forms['frgrm']['cTerNomB'].value = "<?php echo $xRDE['CLINOMXX'] ?>";
        												    break;
  																}
  																window.opener.f_Enabled_Combos();
  																window.opener.document.forms['frgrm'].submit();
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
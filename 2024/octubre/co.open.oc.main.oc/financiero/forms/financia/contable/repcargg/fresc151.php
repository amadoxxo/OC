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
	  					<form name = "frnav" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gModo) {
	  								case "VALID":
	  									$qDatCli  ="SELECT "; 
	  									$qDatCli .="SIAI0150.*, ";  
	  									$qDatCli .="fpar0151.cccggesx ";
	  									$qDatCli .="FROM $cAlfa.SIAI0150 ";
	  									$qDatCli .="LEFT JOIN $cAlfa.fpar0151 ON $cAlfa.SIAI0150.CLIIDXXX = $cAlfa.fpar0151.cliidxxx ";
	  									$qDatCli .="WHERE ";
	  									$qDatCli .="fpar0151.cccggesx LIKE  \"%$gGruSer%\" ";
	  									
	  									switch ($gFunction) {
	  										case "cTerId":
	  											$qDatCli .= "AND ";
	  											$qDatCli .= "SIAI0150.CLIIDXXX LIKE \"%$gTerId%\" AND ";
	  											$qDatCli .= "SIAI0150.REGESTXX = \"ACTIVO\" ORDER BY CLIIDXXX";
	  											break;
	  										case "cTerNom":
	  											$qDatCli .= "AND ";
	  											$qDatCli .= "SIAI0150.CLINOMXX LIKE \"%$gTerNom%\" AND ";
	  											$qDatCli .= "SIAI0150.REGESTXX = \"ACTIVO\" ORDER BY CLINOMXX";
	  											break;
	  									}
	  									
	  									$xDatCli = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
											//f_mensaje(__FILE__,__LINE__,$qDatCli." ~ ".mysql_num_rows($xDatCli));
											$cCadena = array();
											
											while ($xRC = mysql_fetch_array($xDatCli)) {
												$vCccGges = array();
												$vCccGges = explode("~",$xRC['cccggesx']);
												for ($i=0;$i<count($vCccGges);$i++) {
													if ($vCccGges[$i] != "") {
														if ($vCccGges[$i] == $gGruSer) {
															$nClientes = count($cCadena);
															$cCadena[$nClientes] = $xRC;
														}
													}
												}
											}
	  									if (count($cCadena) == 1) { ?>
												<script languaje = "javascript">
												  switch ("<?php echo $gFunction ?>") {
												    case "cTerId":
												    case "cTerNom":
                              parent.fmwork.document.forms['frnav']['cTerId'].value  = "<?php echo $cCadena[0]['CLIIDXXX'] ?>";
    													parent.fmwork.document.forms['frnav']['cTerDV'].value  = "<?php echo f_Digito_Verificacion($cCadena[0]['CLIIDXXX']) ?>";
    													parent.fmwork.document.forms['frnav']['cTerNom'].value = "<?php echo $cNombre = ($cCadena[0]['CLINOMXX'] != "") ? $cCadena[0]['CLINOMXX'] : trim($cCadena[0]['CLIAPE1X']." ".$cCadena[0]['CLIAPE2X']." ".$cCadena[0]['CLINOM1X']." ".$cCadena[0]['CLINOM2X']); ?>";
												    break;
												  }
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
	  									$qDatCli  ="SELECT ";
	  									$qDatCli .="SIAI0150.*, ";
	  									$qDatCli .="fpar0151.cccggesx ";
	  									$qDatCli .="FROM $cAlfa.SIAI0150 ";
	  									$qDatCli .="LEFT JOIN $cAlfa.fpar0151 ON $cAlfa.SIAI0150.CLIIDXXX = $cAlfa.fpar0151.cliidxxx ";
	  									$qDatCli .="WHERE ";
	  									$qDatCli .="fpar0151.cccggesx LIKE  \"%$gGruSer%\" ";
	  									
	  									switch ($gFunction) {
	  										case "cTerId":
	  											$qDatCli .= "AND ";
	  											$qDatCli .= "SIAI0150.CLIIDXXX LIKE \"%$gTerId%\" AND ";
	  											$qDatCli .= "SIAI0150.REGESTXX = \"ACTIVO\" ORDER BY CLIIDXXX";
	  											break;
	  										case "cTerNom":
	  											$qDatCli .= "AND ";
	  											$qDatCli .= "SIAI0150.CLINOMXX LIKE \"%$gTerNom%\" AND ";
	  											$qDatCli .= "SIAI0150.REGESTXX = \"ACTIVO\" ORDER BY CLINOMXX";
	  											break;
	  									}
	  									
	  									$xDatCli = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
	  									//f_mensaje(__FILE__,__LINE__,$qDatCli." ~ ".mysql_num_rows($xDatCli));
											$cCadena = array();
											
											while ($xRC = mysql_fetch_array($xDatCli)) {
												$vCccGges = array();
												$vCccGges = explode("~",$xRC['cccggesx']);
												for ($i=0;$i<count($vCccGges);$i++) {
													if ($vCccGges[$i] != "") {
														if ($vCccGges[$i] == $gGruSer) {
															$nClientes = count($cCadena);
															$cCadena[$nClientes] = $xRC;
														}
													}
												}
											}									

 											if (count($cCadena) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "550">
														<tr>
															<td widht = "050" Class = "name"><center>Nit</center></td>
															<td widht = "500" Class = "name"><center>Nombre</center></td>
														</tr>
														<?php for ($i=0;$i<count($cCadena);$i++) {
															if (count($cCadena) > 1) { ?>
																<tr>
																	<td width = "030" Class = "name">
																		<a href = "javascript:
															                   switch ('<?php echo $gFunction ?>') {
															                     case 'cTerId':
												                           case 'cTerNom':
                      															 window.opener.document.forms['frnav']['cTerId'].value  = '<?php echo $cCadena[$i]['CLIIDXXX'] ?>';
																										 window.opener.document.forms['frnav']['cTerDV'].value  = '<?php echo f_Digito_Verificacion($cCadena[$i]['CLIIDXXX']) ?>';
																										 window.opener.document.forms['frnav']['cTerNom'].value = '<?php echo $cNombre = ($cCadena[$i]['CLINOMXX'] != "") ? $cCadena[$i]['CLINOMXX'] : trim($cCadena[$i]['CLIAPE1X']." ".$cCadena[$i]['CLIAPE2X']." ".$cCadena[$i]['CLINOM1X']." ".$cCadena[$i]['CLINOM2X']); ?>';
																									 break;
															                   }
																							 window.close()"><?php echo $cCadena[$i]['CLIIDXXX'] ?>
																		</a>
																	</td>
																	<td width = "390" Class = "name"><?php echo $cNombre = ($cCadena[$i]['CLINOMXX'] != "") ? $cCadena[$i]['CLINOMXX'] : trim($cCadena[$i]['CLIAPE1X']." ".$cCadena[$i]['CLIAPE2X']." ".$cCadena[$i]['CLINOM1X']." ".$cCadena[$i]['CLINOM2X']); ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
  																switch ("<?php echo $gFunction ?>") {
        												    case "cTerId":
        												    case "cTerNom":
    																	window.opener.document.forms['frnav']['cTerId'].value  = "<?php echo $cCadena[$i]['CLIIDXXX'] ?>";
    																	window.opener.document.forms['frnav']['cTerDV'].value  = "<?php echo f_Digito_Verificacion($cCadena[$i]['CLIIDXXX']) ?>";
    																	window.opener.document.forms['frnav']['cTerDes'].value = "<?php echo $cNombre = ($cCadena[$i]['CLINOMXX'] != "") ? $cCadena[$i]['CLINOMXX'] : trim($cCadena[$i]['CLIAPE1X']." ".$cCadena[$i]['CLIAPE2X']." ".$cCadena[$i]['CLINOM1X']." ".$cCadena[$i]['CLINOM2X']); ?>";
    																break;
  																}
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
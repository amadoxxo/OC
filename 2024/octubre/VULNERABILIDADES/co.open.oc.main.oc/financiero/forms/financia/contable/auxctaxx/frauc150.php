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
	  									$qDatExt  = "SELECT * ";
											$qDatExt .= "FROM $cAlfa.SIAI0150 ";
											$qDatExt .= "WHERE ";
	  									switch ($gFunction) {
												case "cTerId":
													$cOrden = "CLIIDXXX";
													$qDatExt .= "CLIIDXXX = \"$gTerId\" ";
	  										break;
												case "cTerNom":
													$cOrden = "CLINOMXX";
													$qDatExt .= "CLINOMXX LIKE \"%$gTerNom%\" ";
	  										break;
											}
											if ($vSysStr['financiero_ver_terceros_inactivos_reportes'] != "SI") {
												$qDatExt .= "AND REGESTXX = \"ACTIVO\" ";
											}
											$qDatExt .= "ORDER BY $cOrden";
			  							$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
			  							//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

	  									if (mysql_num_rows($xDatExt) == 1) {
	  										$vDatExt = mysql_fetch_array($xDatExt); ?>
												<script languaje = "javascript">
												  switch ("<?php echo $gFunction ?>") {
												    case "cTerId":
												    case "cTerNom":
                              parent.fmwork.document.forms['frgrm']['cTerId'].value  = "<?php echo $vDatExt['CLIIDXXX'] ?>";
    													parent.fmwork.document.forms['frgrm']['cTerDV'].value  = "<?php echo f_Digito_Verificacion($vDatExt['CLIIDXXX']) ?>";
    													parent.fmwork.document.forms['frgrm']['cTerNom'].value = "<?php echo $cNombre = ($vDatExt['CLINOMXX'] != "") ? $vDatExt['CLINOMXX'] : trim($vDatExt['CLIAPE1X']." ".$vDatExt['CLIAPE2X']." ".$vDatExt['CLINOM1X']." ".$vDatExt['CLINOM2X']); ?>";
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
		  								$qDatExt  = "SELECT * ";
											$qDatExt .= "FROM $cAlfa.SIAI0150 ";
											$qDatExt .= "WHERE ";
											switch ($gFunction) {
												case "cTerId":
													$cOrden = "CLIIDXXX";
													$qDatExt .= "CLIIDXXX LIKE \"%$gTerId%\" ";
	  										break;
												case "cTerNom":
													$cOrden = "CLINOMXX";
													$qDatExt .= "CLINOMXX LIKE \"%$gTerNom%\" ";
	  										break;
											}
											if ($vSysStr['financiero_ver_terceros_inactivos_reportes'] != "SI") {
												$qDatExt .= "AND REGESTXX = \"ACTIVO\" ";
											}
											$qDatExt .= "ORDER BY $cOrden";
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

 											if (mysql_num_rows($xDatExt) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "550">
														<tr>
															<td widht = "050" Class = "name"><center>Nit</center></td>
															<td widht = "500" Class = "name"><center>Nombre</center></td>
															<td widht = "050" Class = "name"><center>Estado</center></td>
														</tr>
														<?php while ($xRDE = mysql_fetch_array($xDatExt)) {
															if (mysql_num_rows($xDatExt) > 0) { ?>
																<tr>
																	<td width = "030" Class = "name">
																		<a href = "javascript:
															                   switch ('<?php echo $gFunction ?>') {
															                     case 'cTerId':
												                           case 'cTerNom':
                      															 window.opener.document.forms['frgrm']['cTerId'].value  = '<?php echo $xRDE['CLIIDXXX'] ?>';
																										 window.opener.document.forms['frgrm']['cTerDV'].value  = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
																										 window.opener.document.forms['frgrm']['cTerNom'].value = '<?php echo $cNombre = ($xRDE['CLINOMXX'] != "") ? $xRDE['CLINOMXX'] : trim($xRDE['CLIAPE1X']." ".$xRDE['CLIAPE2X']." ".$xRDE['CLINOM1X']." ".$xRDE['CLINOM2X']); ?>';
																									 break;
															                   }
																							 window.close()"><?php echo $xRDE['CLIIDXXX'] ?>
																		</a>
																	</td>
																	<td width = "390" Class = "name"><?php echo $cNombre = ($xRDE['CLINOMXX'] != "") ? $xRDE['CLINOMXX'] : trim($xRDE['CLIAPE1X']." ".$xRDE['CLIAPE2X']." ".$xRDE['CLINOM1X']." ".$xRDE['CLINOM2X']); ?></td>
                                  <td widht = "050" Class = "name"><center><?php echo $xRDE['REGESTXX'] ?></center></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
  																switch ("<?php echo $gFunction ?>") {
        												    case "cTerId":
        												    case "cTerNom":
    																	window.opener.document.forms['frgrm']['cTerId'].value  = "<?php echo $xRDE['CLIIDXXX'] ?>";
    																	window.opener.document.forms['frgrm']['cTerDV'].value  = "<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>";
    																	window.opener.document.forms['frgrm']['cTerDes'].value = "<?php echo $cNombre = ($xRDE['CLINOMXX'] != "") ? $xRDE['CLINOMXX'] : trim($xRDE['CLIAPE1X']." ".$xRDE['CLIAPE2X']." ".$xRDE['CLINOM1X']." ".$xRDE['CLINOM2X']); ?>";
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
<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  $qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_NAME = \"fpar0116\" LIMIT 0,1";
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
			<table border = "0" cellpadding = "0" cellspacing = "0" width = "250">
				<tr>
					<td>
						<fieldset>
			   			<legend><?php echo $vTblCom['TABLE_COMMENT'] ?></legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
                  // variabe para centros de costo asociados
                  $cCcoIdAso = "";

                  ## if para llenar la variable de centros de costo asociados si está parametrizada ##
                  if ($gComId != "" && $gComCod != "") {
                    $qComCoId  = "SELECT comccoid ";
                    $qComCoId .= "FROM $cAlfa.fpar0117 ";
                    $qComCoId .= "WHERE ";
                    $qComCoId .= "comidxxx = \"$gComId\" AND ";
                    $qComCoId .= "comcodxx = \"$gComCod\" AND ";
                    $qComCoId .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                    $xComCoId  = f_MySql("SELECT","",$qComCoId,$xConexion01,"");
                    // f_Mensaje(__FILE__,__LINE__,$qComCoId." ~ ".mysql_num_rows($xComCoId));
                    if (mysql_num_rows($xComCoId) == 1) {
                      $vComCoId = mysql_fetch_array($xComCoId);
                      if ($vComCoId['comccoid'] != "") {
                        $cCcoIdAso = "\"".str_replace('~', '","',$vComCoId['comccoid'])."\"";
                      }
                    }
                  }
                  ## FIN if para llenar la variable de centros de costo asociados si está parametrizada ##

	  							switch ($gModo) {
	  								case "VALID":
	  									$qDatExt  = "SELECT * ";
											$qDatExt .= "FROM $cAlfa.fpar0116 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "ccoidxxx = \"$gCcoId\" AND ";
                      switch ($gType) {
                        case "GRID":
                          // no hace nada
                        break;
                        default:
                          if ($cCcoIdAso != "") {
                            $qDatExt .= "ccoidxxx IN ($cCcoIdAso) AND ";
                          }
                        break;
                      }
											switch ($cAlfa) {
												case "SIACOSIA":
                        case "DESIACOSIP":
												case "TESIACOSIP":
													$qDatExt .= "regestxx = \"ACTIVO\" ORDER BY ABS(ccoidxxx) LIMIT 0,1";
												break;
												default:
													$qDatExt .= "regestxx = \"ACTIVO\" ORDER BY ccodesxx LIMIT 0,1";
												break;
											}
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
	  									if (mysql_num_rows($xDatExt) == 1) {
	  										$vDatExt = mysql_fetch_array($xDatExt); ?>
												<script languaje = "javascript">
													switch ("<?php echo $gType ?>") {
														case "GRID":
															parent.fmwork.document.forms['frgrm']['cCcoId'+<?php echo $gSecuencia ?>].value  = "<?php echo $vDatExt['ccoidxxx'] ?>";
														break;
														default:
															parent.fmwork.document.forms['frgrm']['cCcoId'].value  = "<?php echo $vDatExt['ccoidxxx'] ?>";
														break;
													}
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW","<?php echo $gSecuencia ?>","<?php echo $gType ?>");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
		  								$qDatExt  = "SELECT * ";
											$qDatExt .= "FROM $cAlfa.fpar0116 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "ccoidxxx LIKE \"%$gCcoId%\" AND ";
                      switch ($gType) {
                        case "GRID":
                          // no hace nada
                        break;
                        default:
                          if ($cCcoIdAso != "") {
                            $qDatExt .= "ccoidxxx IN ($cCcoIdAso) AND ";
                          }
                        break;
                      }
											switch ($cAlfa) {
												case "SIACOSIA":
                        case "DESIACOSIP":
												case "TESIACOSIP":
													$qDatExt .= "regestxx = \"ACTIVO\" ORDER BY ABS(ccoidxxx)";
												break;
												default:
													$qDatExt .= "regestxx = \"ACTIVO\" ORDER BY ccodesxx";
												break;
											}
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
 											if (mysql_num_rows($xDatExt) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "250">
														<tr>
															<td widht = "030" Class = "name"><center>Id</center></td>
															<td widht = "220" Class = "name"><center>Descripcion</center></td>
														</tr>
														<?php while ($xRDE = mysql_fetch_array($xDatExt)) {
															if (mysql_num_rows($xDatExt) > 1) { ?>
																<tr>
																	<td width = "030" Class = "name">
																		<a href = "javascript:
																								switch ('<?php echo $gType ?>') {
																									case 'GRID':
																										window.opener.document.forms['frgrm']['cCcoId'+<?php echo $gSecuencia ?>].value  = '<?php echo $xRDE['ccoidxxx'] ?>';
																									break;
																									default:
																										window.opener.document.forms['frgrm']['cCcoId'].value  = '<?php echo $xRDE['ccoidxxx'] ?>';
																									break;
																								};
																								window.close()"><?php echo $xRDE['ccoidxxx'] ?>
																		</a>
																	</td>
																	<td width = "390" Class = "name"><?php echo $xRDE['ccodesxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	switch ("<?php echo $gType ?>") {
																		case "GRID":
																			window.opener.document.forms['frgrm']['cCcoId'+<?php echo $gSecuencia ?>].value  = "<?php echo $xRDE['ccoidxxx'] ?>";
																		break;
																		default:
																			window.opener.document.forms['frgrm']['cCcoId'].value  = "<?php echo $xRDE['ccoidxxx'] ?>";
																		break;
																	}
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else {
		 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique.");?>
                        <script languaje="javascript">
	                        switch ("<?php echo $gType ?>") {
		                        case "GRID":
		                      	  window.opener.document.forms['frgrm']['cCcoId'+<?php echo $gSecuencia ?>].value  = "";
		                      	break;
		                        default:
		                      	  window.opener.document.forms['frgrm']['cCcoId'].value  = "";
		                        break;
	                        }
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

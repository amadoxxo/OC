<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  switch ($gPucDet) {
    case "N": $cTabla = "fcxp0000"; break;
    case "P": $cTabla = "fcxp0000"; break;
    case "C": $cTabla = "fcxc0000"; break;
    default:  $cTabla = "fcxp0000"; break;
  }
  $qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_NAME = \"$cTabla\" LIMIT 0,1";
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
			<script languaje = "javascript">
			</script>
	  </head>
	  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
	  <center>
			<table border = "0" cellpadding = "0" cellspacing = "0" width = "500">
				<tr>
					<td>
						<fieldset>
			   			<legend><?php echo $vTblCom['TABLE_COMMENT'] ?></legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  					  <input type = "hidden" name = "mChkCom" value = "">
	  						<?php
	  							switch ($gModo) {
	  								case "VALID":
	  									// Pregunto el Detalle de la Cuenta para Saber Contra que Documento Cruzo
	  									switch ($gPucDet) {
	  										case "N":
	  										break;
	  										case "P":
	  											$qDatExt  = "SELECT *, ";
		                      $qDatExt .= "\"\" AS sucidxxx, ";
		                      $qDatExt .= "\"\" AS docidxxx, ";
		                      $qDatExt .= "\"\" AS docsufxx  ";
			  									$qDatExt .= "FROM $cAlfa.fcxp0000 ";
			  									$qDatExt .= "WHERE ";
			  									$qDatExt .= "teridxxx = \"$gTerId\" AND ";
			  									$qDatExt .= "comcscxx = \"$gComCscC\" AND ";
			  									$qDatExt .= "regestxx = \"ACTIVO\"  AND ";
			  									$qDatExt .= "(comsaldo <> 0 OR comsalnf <> 0) ";
	  										break;
	  										case "C":
                          $qDatExt  = "SELECT *, ";
		                      $qDatExt .= "\"\" AS sucidxxx, ";
		                      $qDatExt .= "\"\" AS docidxxx, ";
		                      $qDatExt .= "\"\" AS docsufxx  ";
			  									$qDatExt .= "FROM $cAlfa.fcxc0000 ";
			  									$qDatExt .= "WHERE ";
			  									$qDatExt .= "teridxxx = \"$gTerId\" AND ";
			  									$qDatExt .= "comcscxx = \"$gComCscC\" AND ";
			  									$qDatExt .= "regestxx = \"ACTIVO\"  AND ";
			  									$qDatExt .= "(comsaldo <> 0 OR comsalnf <> 0) ";
	  										break;
                        case "D":
	  											$qDatExt  = "SELECT *,";
			  									$qDatExt .= "docidxxx AS comcscxx,";
			  									$qDatExt .= "docsufxx AS comseqxx,";
			  									$qDatExt .= "0 AS comsaldo,";
			  									$qDatExt .= "0 AS comsalnf ";
			  									$qDatExt .= "FROM $cAlfa.sys00121 ";
			  									$qDatExt .= "WHERE ";
			  									$qDatExt .= "cliidxxx = \"$gTerId\"  AND ";
			  									$qDatExt .= "docidxxx = \"$gComCscC\" AND ";
			  									$qDatExt .= "regestxx IN (\"ACTIVO\") ";
	  										break;
	  										default:
	  											f_Mensaje(__FILE__,__LINE__,"No se Pudo Hacer la Consulta Porque la Cuenta PUC del Concepto no Tiene Detalle, Verifique.");
	  										break;
	  									}
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
	  									if (mysql_num_rows($xDatExt) == 1) {
	  										$vDatExt = mysql_fetch_array($xDatExt);
	  										
	  										if ($gPucDet == "P" || $gPucDet == "C") {
		  										$qPucId  = "SELECT *,";
		  										$qPucId .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AS pucidxxx ";
		  										$qPucId .= "FROM $cAlfa.fpar0115 ";
		  										$qPucId .= "WHERE ";
		  										$qPucId .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$vDatExt['pucidxxx']}\" AND ";
		  										$qPucId .= "regestxx = \"ACTIVO\" LIMIT 0,1";
		  										$xPucId  = f_MySql("SELECT","",$qPucId,$xConexion01,"");
		  										$vPucId  = mysql_fetch_array($xPucId);
		  										//f_Mensaje(__FILE__,__LINE__,$qPucId." ~ ".mysql_num_rows($xPucId));
	  										} elseif ($gPucDet == "D") {  // Trampa para dejar ver los DO's, porque estos ya no se crean con una cuenta.
	  											$vPucId['pucidxxx'] = $gPucId; $vPucId['pucdetxx'] = $gPucDet; $vDatExt['puctipej'] = $gPucTipEj;
	  										}
	  										
                        if (($vPucId['pucidxxx'] == $gPucId) && ($vPucId['pucdetxx'] == $gPucDet)) { ?>
  												<script languaje = "javascript">
	  												var nSwicht = 0;
	  												if (("<?php echo $vDatExt['puctipej'] ?>" == "L" || "<?php echo $vDatExt['puctipej'] ?>" == "") && "<?php echo $gPucTipEj ?>" == "N") {
	  													nSwicht = 1;
	  													alert('No Puede Seleccionar el Comprobante [<?php echo $vDatExt['comidxxx'] ?>-<?php echo $vDatExt['comcodxx'] ?>-<?php echo $vDatExt['comcscxx'] ?>], este se guardo con Tipo de ejecucion <?php echo (($vDatExt['puctipej'] == "") ? "AMBAS" : ($vDatExt['puctipej'] == "L") ? "LOCAL" : "NIIF") ?> y el tipo de Ejecucion Actual del Concepto es <?php echo ($gPucTipEj == "") ? "AMBAS" : ($gPucTipEj == "L") ? "LOCAL" : "NIIF" ?>.');
	  												}
	
	  												if (("<?php echo $gPucTipEj ?>" == "L" || "<?php echo $gPucTipEj ?>" == "") && "<?php echo $vDatExt['puctipej'] ?>" == "N") {
	  													nSwicht = 1;
	  													alert('No Puede Seleccionar el Comprobante [<?php echo $vDatExt['comidxxx'] ?>-<?php echo $vDatExt['comcodxx'] ?>-<?php echo $vDatExt['comcscxx'] ?>], este se guardo con Tipo de ejecucion <?php echo (($vDatExt['puctipej'] == "") ? "AMBAS" : ($vDatExt['puctipej'] == "L") ? "LOCAL" : "NIIF") ?> y el tipo de Ejecucion Actual del Concepto es <?php echo ($gPucTipEj == "") ? "AMBAS" : ($gPucTipEj == "L") ? "LOCAL" : "NIIF" ?>.');
	  												}
	
	  	  										if (nSwicht == 0) {
	                            parent.framework.document.forms['frgrm']['cComIdC'].value  = "<?php echo $vDatExt['comidxxx'] ?>";
	              				    	parent.framework.document.forms['frgrm']['cComCodC'].value = "<?php echo $vDatExt['comcodxx'] ?>";
	              				    	parent.framework.document.forms['frgrm']['cComCscC'].value = "<?php echo $vDatExt['comcscxx'] ?>";
	              							parent.framework.document.forms['frgrm']['cComSeqC'].value = "<?php echo $vDatExt['comseqxx'] ?>";
	              							parent.framework.document.forms['frgrm']['cCcoId'].value   = "<?php echo $vDatExt['ccoidxxx'] ?>";
	              							if ("<?php echo $gPucDet ?>" == "D") {
	              								parent.framework.document.forms['frgrm']['cSccId'].value = "<?php echo $vDatExt['comcscxx'] ?>";
	              							}
	              							parent.framework.document.forms['frgrm']['cSucId'].value   = '<?php echo $vDatExt['sucidxxx'] ?>';
	              							parent.framework.document.forms['frgrm']['cDocId'].value   = '<?php echo $vDatExt['docidxxx'] ?>';
	              							parent.framework.document.forms['frgrm']['cDocSuf'].value  = '<?php echo $vDatExt['docsufxx'] ?>';		              							
	              							parent.framework.document.forms['frgrm']['cPucTipEj'].value= '<?php echo $vDatExt['puctipej'] ?>';		              							
	              							switch ("<?php echo $vDatExt['puctipej'] ?>") {
								  							case "L":
								  								parent.framework.document.forms['frgrm']['nSaldo'].value   = "<?php echo abs($vDatExt['comsaldo']) ?>";
								  								parent.framework.document.forms['frgrm']['nSaldoNF'].value = "";
								  	  					break;
								  							case "N":
								  								parent.framework.document.forms['frgrm']['nSaldo'].value   = "";
								  								parent.framework.document.forms['frgrm']['nSaldoNF'].value = "<?php echo abs($vDatExt['comsalnf']) ?>";
								  	  					break;
								  							default:
								  								parent.framework.document.forms['frgrm']['nSaldo'].value   = "<?php echo abs($vDatExt['comsaldo']) ?>";
								  								parent.framework.document.forms['frgrm']['nSaldoNF'].value = "<?php echo abs($vDatExt['comsalnf']) ?>";
								  	  					break;
							  							}
	  	  										}
  												</script>
  										  <?php } else { ?>
  												<script languaje = "javascript">
  	      	    						parent.framework.f_Links("<?php echo $gFunction ?>","WINDOW","<?php echo $gSecuencia ?>");
  												</script>
  											<?php }
	  									} else { ?>
												<script languaje = "javascript">
	      	    						parent.framework.f_Links("<?php echo $gFunction ?>","WINDOW","<?php echo $gSecuencia ?>");
												</script>
  										<?php }
	      	      		break;
	  								case "WINDOW":
	  									// Pregunto el Detalle de la Cuenta para Saber Contra que Documento Cruzo
	  									switch ($gPucDet) {
	  										case "N":
	  										break;
	  										case "P":
	  											$qDatExt  = "SELECT *, ";
	  											$qDatExt .= "\"\" AS sucidxxx, ";
	  											$qDatExt .= "\"\" AS docidxxx, ";
	  											$qDatExt .= "\"\" AS docsufxx  ";
			  									$qDatExt .= "FROM $cAlfa.fcxp0000 ";
			  									$qDatExt .= "WHERE ";
			  									$qDatExt .= "teridxxx = \"$gTerId\" AND ";
			  									$qDatExt .= "regestxx = \"ACTIVO\"  AND ";
			  									$qDatExt .= "(comsaldo <> 0 OR comsalnf <> 0)";
	  										break;
	  										case "C":
                          $qDatExt  = "SELECT *, ";
                          $qDatExt .= "\"\" AS sucidxxx, ";
                          $qDatExt .= "\"\" AS docidxxx, ";
                          $qDatExt .= "\"\" AS docsufxx  ";
			  									$qDatExt .= "FROM $cAlfa.fcxc0000 ";
			  									$qDatExt .= "WHERE ";
			  									$qDatExt .= "teridxxx = \"$gTerId\" AND ";
			  									$qDatExt .= "regestxx = \"ACTIVO\"  AND ";
			  									$qDatExt .= "(comsaldo <> 0 OR comsalnf <> 0)";
	  										break;
	  										case "D":
	  											$qDatExt  = "SELECT *,";
	  											$qDatExt .= "docidxxx AS comcscxx,";
	  											$qDatExt .= "docsufxx AS comseqxx,";
	  											$qDatExt .= "0 AS comsaldo,";
	  											$qDatExt .= "0 AS comsalnf ";
			  									$qDatExt .= "FROM $cAlfa.sys00121 ";
			  									$qDatExt .= "WHERE ";
			  									$qDatExt .= "cliidxxx = \"$gTerId\"  AND ";
			  									$qDatExt .= "regestxx IN (\"ACTIVO\")";
	  										break;
	  										default:
	  											f_Mensaje(__FILE__,__LINE__,"No se Pudo Hacer la Consulta Porque la Cuenta PUC del Concepto no Tiene Detalle, Verifique.");
	  										break;
	  									}
	  									
	  									//echo $qDatExt;
	  									//f_Mensaje(__FILE__,__LINE__,"$qDatExt");
	  									
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
 											if (mysql_num_rows($xDatExt) > 0) { ?>
		 										<center>
                          <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "220" Class = "name"><center>Comprobante</center></td>
															<td widht = "060" Class = "name"><center>Fecha</center></td>
															<td widht = "060" Class = "name"><center>Cuenta</center></td>
															<td widht = "020" Class = "name"><center>C.C.</center></td>
															<td widht = "100" Class = "name"><center>Saldo</center></td>
															<td widht = "040" Class = "name"><center>Estado</center></td>
														</tr>
														
														<?php $i=0; while ($xRDE = mysql_fetch_array($xDatExt)) {
															if ($gPucDet == "P" || $gPucDet == "C") {
	                              $qPucId  = "SELECT *,";
	                              $qPucId .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AS pucidxxx ";
	      	  										$qPucId .= "FROM $cAlfa.fpar0115 ";
	      	  										$qPucId .= "WHERE ";
	      	  										$qPucId .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$xRDE['pucidxxx']}\" AND ";
	      	  										$qPucId .= "regestxx = \"ACTIVO\" LIMIT 0,1";
	      	  										$xPucId  = f_MySql("SELECT","",$qPucId,$xConexion01,"");
	      	  										$vPucId  = mysql_fetch_array($xPucId);
	      	  										//f_Mensaje(__FILE__,__LINE__,$qPucId." ~ ".mysql_num_rows($xPucId));																
															} elseif ($gPucDet == "D") { // Trampa para dejar ver los DO's, porque estos ya no se crean con una cuenta.
																$vPucId['pucidxxx'] = $gPucId; $vPucId['pucdetxx'] = $gPucDet; $xRDE['puctipej'] = $gPucTipEj;
															} ?>
 
															<tr>
															 <?php if (($vPucId['pucidxxx'] == $gPucId) && ($vPucId['pucdetxx'] == $gPucDet)) { ?>
  																<td Class = "name" style = "text-align:left">
                                    <a href = "#" onclick = "javascript:var nSwicht = 0;
																  												if (('<?php echo $xRDE['puctipej'] ?>' == 'L' || '<?php echo $xRDE['puctipej'] ?>' == '') && '<?php echo $gPucTipEj ?>' == 'N') {
																  													nSwicht = 1;
																  													alert('No Puede Seleccionar el Comprobante [<?php echo $xRDE['comidxxx'] ?>-<?php echo $xRDE['comcodxx'] ?>-<?php echo $xRDE['comcscxx'] ?>], este se guardo con Tipo de ejecucion <?php echo (($xRDE['puctipej'] == "") ? "AMBAS" : ($xRDE['puctipej'] == "L") ? "LOCAL" : "NIIF") ?> y el tipo de Ejecucion Actual del Concepto es <?php echo ($gPucTipEj == "") ? "AMBAS" : ($gPucTipEj == "L") ? "LOCAL" : "NIIF" ?>.');
																  												}
																
																  												if (('<?php echo $gPucTipEj ?>' == 'L' || '<?php echo $gPucTipEj ?>' == '') && '<?php echo $xRDE['puctipej'] ?>' == 'N') {
																  													nSwicht = 1;
																  													alert('No Puede Seleccionar el Comprobante [<?php echo $xRDE['comidxxx'] ?>-<?php echo $xRDE['comcodxx'] ?>-<?php echo $xRDE['comcscxx'] ?>], este se guardo con Tipo de ejecucion <?php echo (($xRDE['puctipej'] == "") ? "AMBAS" : ($xRDE['puctipej'] == "L") ? "LOCAL" : "NIIF") ?> y el tipo de Ejecucion Actual del Concepto es <?php echo ($gPucTipEj == "") ? "AMBAS" : ($gPucTipEj == "L") ? "LOCAL" : "NIIF" ?>.');
																  												}
                                    											
                                    											if (nSwicht == 0) {
	                                    											window.opener.document.forms['frgrm']['cComIdC'].value  = '<?php echo $xRDE['comidxxx'] ?>';
	                                            				    	window.opener.document.forms['frgrm']['cComCodC'].value = '<?php echo $xRDE['comcodxx'] ?>';
	                                            				    	window.opener.document.forms['frgrm']['cComCscC'].value = '<?php echo $xRDE['comcscxx'] ?>';
	                                            							window.opener.document.forms['frgrm']['cComSeqC'].value = '<?php echo $xRDE['comseqxx'] ?>';
	                                            							window.opener.document.forms['frgrm']['cCcoId'].value   = '<?php echo $xRDE['ccoidxxx'] ?>';
	                                            							if ('<?php echo $gPucDet ?>' == 'D') {
																              								window.opener.document.forms['frgrm']['cSccId'].value = '<?php echo $xRDE['comcscxx'] ?>';
																              							}
																              							window.opener.document.forms['frgrm']['cSucId'].value   = '<?php echo $xRDE['sucidxxx'] ?>';
																              							window.opener.document.forms['frgrm']['cDocId'].value   = '<?php echo $xRDE['docidxxx'] ?>';
																              							window.opener.document.forms['frgrm']['cDocSuf'].value  = '<?php echo $xRDE['docsufxx'] ?>';
																              							window.opener.document.forms['frgrm']['cPucTipEj'].value= '<?php echo $xRDE['puctipej'] ?>';
																              							switch ('<?php echo $xRDE['puctipej'] ?>') {
																							  							case 'L':
																							  								window.opener.document.forms['frgrm']['nSaldo'].value   = '<?php echo abs($xRDE['comsaldo']) ?>';
																							  								window.opener.document.forms['frgrm']['nSaldoNF'].value = '';
																							  	  					break;
																							  							case 'N':
																							  								window.opener.document.forms['frgrm']['nSaldo'].value   = '';
																							  								window.opener.document.forms['frgrm']['nSaldoNF'].value = '<?php echo abs($xRDE['comsalnf']) ?>';
																							  	  					break;
																							  							default:
																							  								window.opener.document.forms['frgrm']['nSaldo'].value   = '<?php echo abs($xRDE['comsaldo']) ?>';
																							  								window.opener.document.forms['frgrm']['nSaldoNF'].value = '<?php echo abs($xRDE['comsalnf']) ?>';
																							  	  					break;
																						  							}
	                                            							window.close();
	                                            						}"><?php echo $xRDE['comidxxx']."-".$xRDE['comcodxx']."-".$xRDE['comcscxx']."-".$xRDE['comseqxx'] ?></a>
                                  </td>
                                <?php } else { ?>
                                  <td Class = "name" style = "text-align:left">
                                    <?php echo $xRDE['comidxxx']."-".$xRDE['comcodxx']."-".$xRDE['comcscxx']."-".$xRDE['comseqxx']; ?>
                                  </td>
                                <?php } ?>
																</td>
																<td Class = "name" style = "text-align:center"><?php echo $xRDE['regfcrex'] ?></td>
																<td Class = "name" style = "text-align:center"><?php echo $xRDE['pucidxxx'] ?></td>
																<td Class = "name" style = "text-align:center"><?php echo $xRDE['ccoidxxx'] ?></td>
                                <td Class = "name" style = "text-align:right"><?php echo (abs($xRDE['comsaldo']) > 0) ? number_format($xRDE['comsaldo']) : number_format($xRDE['comsalnf']) ?></td>
                                <td Class = "name" style = "text-align:center"><?php echo $xRDE['regestxx'] ?></td>
															</tr>
														<?php $i++; } ?>
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
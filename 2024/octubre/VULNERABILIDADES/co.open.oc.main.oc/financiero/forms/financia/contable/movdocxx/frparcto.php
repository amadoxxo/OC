<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");

  if (!empty($gModo) && !empty($gFunction)) {
		?>
		<html>
			<head>
				<title>Parametrica de Conceptos Contables</title>
				<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
				<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
				<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
				<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
				<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
				<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
			</head>
			<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
				<center>
					<table border ="0" cellpadding="0" cellspacing="0" width="300">
						<tr>
							<td>
								<fieldset>
									<legend>Parametrica de Conceptos Contables</legend>
									<form name = "frgrm" action = "" method = "post" target = "fmpro">
										<?php
											// Conceptos Contables
											$qCto0119  = "SELECT ";
											$qCto0119 .= " $cAlfa.fpar0119.pucidxxx, ";
											$qCto0119 .= " $cAlfa.fpar0119.ctoidxxx, ";
											$qCto0119 .= " $cAlfa.fpar0119.ctodesxp, ";
											$qCto0119 .= " $cAlfa.fpar0119.ctodesxg, ";
											$qCto0119 .= " $cAlfa.fpar0119.ctodesxr, ";
											$qCto0119 .= " $cAlfa.fpar0119.ctodesxl, ";
											$qCto0119 .= " $cAlfa.fpar0119.ctodesxf, ";
											$qCto0119 .= " $cAlfa.fpar0119.ctodesxm, ";
											$qCto0119 .= " $cAlfa.fpar0119.ctodesxn, ";
											$qCto0119 .= " $cAlfa.fpar0119.ctodesxc, ";
											$qCto0119 .= " $cAlfa.fpar0119.ctodesxd, ";
											$qCto0119 .= " $cAlfa.fpar0119.ctodesxx, ";
											$qCto0119 .= " $cAlfa.fpar0119.regestxx ";
											$qCto0119 .= "FROM $cAlfa.fpar0119 ";
											$qCto0119 .= "WHERE ";
											if ($gModo == 'VALID') {
												$qCto0119 .= "$cAlfa.fpar0119.ctoidxxx = \"$gCtoCto\" AND ";
											} else {
												$qCto0119 .= "$cAlfa.fpar0119.ctoidxxx LIKE \"%$gCtoCto%\" AND ";
											}
											$qCto0119 .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
											$qCto0119 .= "ORDER BY ABS($cAlfa.fpar0119.ctoidxxx) ";
											$xCto0119  = f_MySql("SELECT","",$qCto0119,$xConexion01,"");
											// Se asignan los conceptos contables
											if ($xCto0119 && mysql_num_rows($xCto0119) > 0) {
												while ($xRCT = mysql_fetch_array($xCto0119)) {
													$nInd_mConceptos = count($mConceptos);
													$mConceptos[$nInd_mConceptos] = $xRCT;
												}
											}

											// Conceptos Contables Causaciones Automaticas
											$qCto0121  = "SELECT ";
											$qCto0121 .= " $cAlfa.fpar0121.pucidxxx, ";
											$qCto0121 .= " $cAlfa.fpar0121.ctoidxxx, ";
											$qCto0121 .= " $cAlfa.fpar0121.ctodesxx ";
											$qCto0121 .= "FROM $cAlfa.fpar0121 ";
											$qCto0121 .= "WHERE ";
											if ($gModo == 'VALID') {
												$qCto0121 .= "$cAlfa.fpar0121.ctoidxxx = \"$gCtoCto\" AND ";
											} else {
												$qCto0121 .= "$cAlfa.fpar0121.ctoidxxx LIKE \"%$gCtoCto%\" AND ";
											}
											$qCto0121 .= "$cAlfa.fpar0121.regestxx = \"ACTIVO\" ";
											$qCto0121 .= "ORDER BY ABS($cAlfa.fpar0121.ctoidxxx) ";
											$xCto0121  = f_MySql("SELECT","",$qCto0121,$xConexion01,"");
											// Se asignan los conceptos contables causaciones automaticas
											if ($xCto0121 && mysql_num_rows($xCto0121) > 0) {
												while ($xRCT = mysql_fetch_array($xCto0121)) {
													$nInd_mConceptos = count($mConceptos);
													$mConceptos[$nInd_mConceptos] = $xRCT;
												}
											}

											// Conceptos de Cobro
											$qCto0129  = "SELECT ";
											$qCto0129 .= " $cAlfa.fpar0129.pucidxxx, ";
											$qCto0129 .= " $cAlfa.fpar0129.ctoidxxx, ";
											$qCto0129 .= " $cAlfa.fpar0129.serdesxx AS ctodesxp, ";
											$qCto0129 .= " $cAlfa.fpar0129.serdespx AS ctodesxx ";
											$qCto0129 .= "FROM $cAlfa.fpar0129 ";
											$qCto0129 .= "WHERE ";
											$qCto0129 .= "$cAlfa.fpar0129.ctoidxxx != \"\" AND ";
											if ($gModo == 'VALID') {
												$qCto0129 .= "$cAlfa.fpar0129.ctoidxxx = \"$gCtoCto\" AND ";
											} else {
												$qCto0129 .= "$cAlfa.fpar0129.ctoidxxx LIKE \"%$gCtoCto%\" AND ";
											}
											$qCto0129 .= "$cAlfa.fpar0129.regestxx = \"ACTIVO\" ";
											$qCto0129 .= "ORDER BY ABS($cAlfa.fpar0129.ctoidxxx) ";
											$xCto0129  = f_MySql("SELECT","",$qCto0129,$xConexion01,"");
											// Se asignan los conceptos de cobro
											if ($xCto0129 && mysql_num_rows($xCto0129) > 0) {
												while ($xRCT = mysql_fetch_array($xCto0129)) {
													$nInd_mConceptos = count($mConceptos);
													$mConceptos[$nInd_mConceptos] = $xRCT;
												}
											}

											switch ($gModo) {
												case "WINDOW":
													if (count($mConceptos) > 0) {
														?>
														<center>
															<table cellspacing = "0" cellpadding = "1" border = "1" width ="500">
																<tr>
																	<td widht ="080" bgcolor="#D6DFF7" Class = "name"><center>CONCEPTO</center></td>
																	<td bgcolor="#D6DFF7" Class = "name"><center>NOMBRE</center></td>
																	<td widht ="050" bgcolor="#D6DFF7" Class = "name"><center>CUENTA</center></td>
																</tr>
																<?php for ($i=0;$i<count($mConceptos);$i++) {
																	// Se asigna la descripcion del concepto correspondiente
																	$cCtoDesx = '';
																	if ($mConceptos[$i]['ctodesxp'] != "") {
																		$cCtoDesx = $mConceptos[$i]['ctodesxp'];
																	}elseif ($mConceptos[$i]['ctodesxg'] != "") {
																		$cCtoDesx = $mConceptos[$i]['ctodesxg'];
																	} elseif ($mConceptos[$i]['ctodesxr'] != "") {
																		$cCtoDesx = $mConceptos[$i]['ctodesxr'];
																	} elseif ($mConceptos[$i]['ctodesxl'] != "") {
																		$cCtoDesx = $mConceptos[$i]['ctodesxl'];
																	} elseif ($mConceptos[$i]['ctodesxf'] != "") {
																		$cCtoDesx = $mConceptos[$i]['ctodesxf'];
																	} elseif ($mConceptos[$i]['ctodesxm'] != "") {
																		$cCtoDesx = $mConceptos[$i]['ctodesxm'];
																	} elseif ($mConceptos[$i]['ctodesxn'] != "") {
																		$cCtoDesx = $mConceptos[$i]['ctodesxn'];
																	} elseif ($mConceptos[$i]['ctodesxc'] != "") {
																		$cCtoDesx = $mConceptos[$i]['ctodesxc'];
																	} elseif ($mConceptos[$i]['ctodesxd'] != "") {
																		$cCtoDesx = $mConceptos[$i]['ctodesxd'];
																	} elseif ($mConceptos[$i]['ctodesxx'] != "") {
																		$cCtoDesx = $mConceptos[$i]['ctodesxx'];
																	} else {
																		$cCtoDesx = "CONCEPTO SIN DESCRIPCION";
																	}
																	if (count($mConceptos) > 1) {
																		?>
																		<tr>
																			<td class= "name">
																				<a href = "javascript:window.opener.document.forms['frgrm']['cCtoCod'].value ='<?php echo $mConceptos[$i]['ctoidxxx']?>';
																															window.opener.document.forms['frgrm']['cCtoDes'].value='<?php echo $cCtoDesx?>';
																															window.close()"><?php echo $mConceptos[$i]['ctoidxxx'] ?></a></td>
																			<td class= "name"><?php echo $cCtoDesx ?></td>
																			<td class= "name"><?php echo $mConceptos[$i]['pucidxxx'] ?></td>
																		</tr>
																	<?php	} else { ?>
																		<script languaje="javascript">
																			window.opener.document.forms['frgrm']['cCtoCod'].value ='<?php echo $mConceptos[$i]['ctoidxxx'] ?>';
																			window.opener.document.forms['frgrm']['cCtoDes'].value='<?php echo $cCtoDesx ?>';
																			window.close()
																		</script>
																	<?php }
																} ?>
															</table>
														</center>
													<?php	} else {
														f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
														?>
														<script languaje="javascript">
															window.opener.document.forms['frgrm']['cCtoCod'].value ='';
															window.opener.document.forms['frgrm']['cCtoDes'].value='';
															window.close();
														</script>
														<?php
													}
												break;
												case "VALID":
													if (count($mConceptos) > 0) {
														for ($i=0;$i<count($mConceptos);$i++) {
															// Se asigna la descripcion del concepto correspondiente
															$cCtoDesx = '';
															if ($mConceptos[$i]['ctodesxp'] != "") {
																$cCtoDesx = $mConceptos[$i]['ctodesxp'];
															}elseif ($mConceptos[$i]['ctodesxg'] != "") {
																$cCtoDesx = $mConceptos[$i]['ctodesxg'];
															} elseif ($mConceptos[$i]['ctodesxr'] != "") {
																$cCtoDesx = $mConceptos[$i]['ctodesxr'];
															} elseif ($mConceptos[$i]['ctodesxl'] != "") {
																$cCtoDesx = $mConceptos[$i]['ctodesxl'];
															} elseif ($mConceptos[$i]['ctodesxf'] != "") {
																$cCtoDesx = $mConceptos[$i]['ctodesxf'];
															} elseif ($mConceptos[$i]['ctodesxm'] != "") {
																$cCtoDesx = $mConceptos[$i]['ctodesxm'];
															} elseif ($mConceptos[$i]['ctodesxn'] != "") {
																$cCtoDesx = $mConceptos[$i]['ctodesxn'];
															} elseif ($mConceptos[$i]['ctodesxc'] != "") {
																$cCtoDesx = $mConceptos[$i]['ctodesxc'];
															} elseif ($mConceptos[$i]['ctodesxd'] != "") {
																$cCtoDesx = $mConceptos[$i]['ctodesxd'];
															} elseif ($mConceptos[$i]['ctodesxx'] != "") {
																$cCtoDesx = $mConceptos[$i]['ctodesxx'];
															} else {
																$cCtoDesx = "CONCEPTO SIN DESCRIPCION";
															}
															?>
															<script languaje = "javascript">
																parent.fmwork.document.forms['frgrm']['cCtoCod'].value ='<?php echo $mConceptos[$i]['ctoidxxx']?>';
																parent.fmwork.document.forms['frgrm']['cCtoDes'].value='<?php echo $cCtoDesx?>';
															</script>
														<?php break;
														}
													} else {
														?>
														<script languaje = "javascript">
															parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
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
		f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
	} ?>
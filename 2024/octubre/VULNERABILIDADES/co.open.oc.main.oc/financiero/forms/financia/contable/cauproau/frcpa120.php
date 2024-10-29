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
	  							switch ($gModo) {
	  								case "VALID":
	  									$qDatExt  = "SELECT * ";
											$qDatExt .= "FROM $cAlfa.fpar0120 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "ccoidxxx = \"$gCcoId\" AND ";
											$qDatExt .= "sccidxxx = \"$gSccId\" AND ";
                      if ($vSysStr['financiero_comprobantes_mostrar_cabecera_subcentro_costo_do'] == "NO" && $gType == ""){
                        $qDatExt .= "sccestdo =\"\" AND ";
                      }
											switch ($cAlfa) {
												case "SIACOSIA":
												case "TESIACOSIP":
													$qDatExt .= "regestxx = \"ACTIVO\" ORDER BY ABS(sccidxxx) LIMIT 0,1";
												break;
												default:
													$qDatExt .= "regestxx = \"ACTIVO\" ORDER BY sccdesxx LIMIT 0,1";
												break;
											}
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
	  									if (mysql_num_rows($xDatExt) == 1) {
	  										$vDatExt = mysql_fetch_array($xDatExt);

	  										//Buscando si el subcentro de costo es un DO
	  										$qTramite  = "SELECT sucidxxx, docidxxx, docsufxx ";
	  										$qTramite .= "FROM $cAlfa.sys00121 ";
	  										$qTramite .= "WHERE ";
	  										$qTramite .= "ccoidxxx = \"{$vDatExt['ccoidxxx']}\" AND ";
	  										$qTramite .= "docidxxx = \"{$vDatExt['sccidxxx']}\"";
	  										$xTramite  = f_MySql("SELECT","",$qTramite,$xConexion01,"");	  	
	  										//f_Mensaje(__FILE__,__LINE__,$qTramite." ~ ".mysql_num_rows($xTramite));
	  										$vTramite = mysql_fetch_array($xTramite);
	  										$nBan = (mysql_num_rows($xTramite) > 1) ? 1 : 0; ?>
												<script languaje = "javascript">
													switch ("<?php echo $gType ?>") {
														case "GRID":
															parent.fmwork.document.forms['frgrm']['cSccId'+<?php echo $gSecuencia ?>].value  = "<?php echo $vDatExt['sccidxxx'] ?>";
															if ("<?php echo $nBan ?>" == "1") {
																parent.fmwork.f_Links("cSccId_DocId","WINDOW","<?php echo $gSecuencia ?>","<?php echo $gType ?>");
															} else {
																parent.fmwork.document.forms['frgrm']['cSucId'+<?php echo $gSecuencia ?>].value  = "<?php echo $vTramite['sucidxxx'] ?>";
																parent.fmwork.document.forms['frgrm']['cDocId'+<?php echo $gSecuencia ?>].value  = "<?php echo $vTramite['docidxxx'] ?>";
																parent.fmwork.document.forms['frgrm']['cDocSuf'+<?php echo $gSecuencia ?>].value = "<?php echo $vTramite['docsufxx'] ?>";
															}															
														break;
														default:
															parent.fmwork.document.forms['frgrm']['cSccId'].value  = "<?php echo $vDatExt['sccidxxx'] ?>";
															if ("<?php echo $nBan ?>" == "1") {
																parent.fmwork.f_Links("cSccId_DocId","WINDOW","","");
															} else {
																parent.fmwork.document.forms['frgrm']['cSccId_SucId'].value  = "<?php echo $vTramite['sucidxxx'] ?>";
																parent.fmwork.document.forms['frgrm']['cSccId_DocId'].value  = "<?php echo $vTramite['docidxxx'] ?>";
																parent.fmwork.document.forms['frgrm']['cSccId_DocSuf'].value = "<?php echo $vTramite['docsufxx'] ?>";
															}
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
											$qDatExt .= "FROM $cAlfa.fpar0120 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "ccoidxxx = \"$gCcoId\" AND ";
											$qDatExt .= "sccidxxx LIKE \"%$gSccId%\" AND ";
                      if ($vSysStr['financiero_comprobantes_mostrar_cabecera_subcentro_costo_do'] == "NO" && $gType == ""){
                        $qDatExt .= "sccestdo =\"\" AND ";
                      }
	  									switch ($cAlfa) {
												case "SIACOSIA":
												case "TESIACOSIP":
													$qDatExt .= "regestxx = \"ACTIVO\" ORDER BY ABS(sccidxxx)";
												break;
												default:
													$qDatExt .= "regestxx = \"ACTIVO\" ORDER BY sccdesxx";
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
															//Buscando si el subcentro de costo es un DO
															$qTramite  = "SELECT sucidxxx, docidxxx, docsufxx ";
															$qTramite .= "FROM $cAlfa.sys00121 ";
															$qTramite .= "WHERE ";
															$qTramite .= "ccoidxxx = \"{$xRDE['ccoidxxx']}\" AND ";
															$qTramite .= "docidxxx = \"{$xRDE['sccidxxx']}\"";
															$xTramite  = f_MySql("SELECT","",$qTramite,$xConexion01,"");
															$vTramite = mysql_fetch_array($xTramite);
															//f_Mensaje(__FILE__,__LINE__,$qTramite." ~ ".mysql_num_rows($xTramite));
															$nBan = (mysql_num_rows($xTramite) > 1) ? 1 : 0;
															
															if (mysql_num_rows($xDatExt) > 1) { ?>
																<tr>
																	<td width = "030" Class = "name">
																		<a href = "javascript:
																								switch ('<?php echo $gType ?>') {
																									case 'GRID':
																										window.opener.document.forms['frgrm']['cSccId'+<?php echo $gSecuencia ?>].value  = '<?php echo $xRDE['sccidxxx'] ?>';
																										if ('<?php echo $nBan ?>' == '1') {
																											window.opener.f_Links('cSccId_DocId','WINDOW','<?php echo $gSecuencia ?>','<?php echo $gType ?>');
																										} else {
																											window.opener.document.forms['frgrm']['cSucId'+<?php echo $gSecuencia ?>].value  = '<?php echo $vTramite['sucidxxx'] ?>';
																											window.opener.document.forms['frgrm']['cDocId'+<?php echo $gSecuencia ?>].value  = '<?php echo $vTramite['docidxxx'] ?>';
																											window.opener.document.forms['frgrm']['cDocSuf'+<?php echo $gSecuencia ?>].value = '<?php echo $vTramite['docsufxx'] ?>';
																										}
																									break;
																									default:
																										window.opener.document.forms['frgrm']['cSccId'].value  = '<?php echo $xRDE['sccidxxx'] ?>';
																										if ('<?php echo $nBan ?>' == '1') {
																											window.opener.f_Links('cSccId_DocId','WINDOW','','');
																										} else {
																											window.opener.document.forms['frgrm']['cSccId_SucId'].value  = '<?php echo $vTramite['sucidxxx'] ?>';
																											window.opener.document.forms['frgrm']['cSccId_DocId'].value  = '<?php echo $vTramite['docidxxx'] ?>';
																											window.opener.document.forms['frgrm']['cSccId_DocSuf'].value = '<?php echo $vTramite['docsufxx'] ?>';
																										}
																									break;
																								};
																								window.close()"><?php echo $xRDE['sccidxxx'] ?>
																		</a>
																	</td>
																	<td width = "390" Class = "name"><?php echo $xRDE['sccdesxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	switch ("<?php echo $gType ?>") {
																		case "GRID":
																			window.opener.document.forms['frgrm']['cSccId'+<?php echo $gSecuencia ?>].value  = "<?php echo $xRDE['sccidxxx'] ?>";
																			if ("<?php echo $nBan ?>" == "1") {
																				window.opener.f_Links("cSccId_DocId","WINDOW","<?php echo $gSecuencia ?>","<?php echo $gType ?>");
																			} else {
																				window.opener.document.forms['frgrm']['cSucId'+<?php echo $gSecuencia ?>].value  = "<?php echo $vTramite['sucidxxx'] ?>";
																				window.opener.document.forms['frgrm']['cDocId'+<?php echo $gSecuencia ?>].value  = "<?php echo $vTramite['docidxxx'] ?>";
																				window.opener.document.forms['frgrm']['cDocSuf'+<?php echo $gSecuencia ?>].value = "<?php echo $vTramite['docsufxx'] ?>";
																			}
																		break;
																		default:
																			window.opener.document.forms['frgrm']['cSccId'].value  = "<?php echo $xRDE['sccidxxx'] ?>";
																			if ("<?php echo $nBan ?>" == "1") {
																				window.opener.f_Links("cSccId_DocId","WINDOW","","");
																			} else {
																				window.opener.document.forms['frgrm']['cSccId_SucId'].value  = "<?php echo $vTramite['sucidxxx'] ?>";
																				window.opener.document.forms['frgrm']['cSccId_DocId'].value  = "<?php echo $vTramite['docidxxx'] ?>";
																				window.opener.document.forms['frgrm']['cSccId_DocSuf'].value = "<?php echo $vTramite['docsufxx'] ?>";
																			}
																		break;
																	}
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else { ?>
		 										<script languaje="javascript">
		 											switch ("<?php echo $gType ?>") {
		 												case "GRID":
		 													window.opener.f_Links("cSccId_DocId","VALID","<?php echo $gSecuencia ?>","<?php echo $gType ?>");
		 												break;
		 												default:
		 													alert("No se Encontraron Registros, Verifique.");
		 													window.opener.document.forms['frgrm']['cSccId'].value        = "";
		 													window.opener.document.forms['frgrm']['cSccId_SucId'].value  = "";
															window.opener.document.forms['frgrm']['cSccId_DocId'].value  = "";
															window.opener.document.forms['frgrm']['cSccId_DocSuf'].value = "";
		 												break;
		 											}
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
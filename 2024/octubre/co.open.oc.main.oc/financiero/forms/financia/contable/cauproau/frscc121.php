<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  $qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_NAME = \"sys00121\" LIMIT 0,1";
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
				function f_Carga(xSucId,xDocId,xDocSuf,xSecuencia,xTipo) {
					switch (xTipo) {
						case "GRID":
							window.opener.document.forms['frgrm']['cSccId'+xSecuencia].value  = xDocId;
							window.opener.document.forms['frgrm']['cSucId'+xSecuencia].value  = xSucId;
							window.opener.document.forms['frgrm']['cDocId'+xSecuencia].value  = xDocId;
							window.opener.document.forms['frgrm']['cDocSuf'+xSecuencia].value = xDocSuf;
						break;
						default:
							window.opener.document.forms['frgrm']['cSccId'].value        = xDocId;
							window.opener.document.forms['frgrm']['cSccId_SucId'].value  = xSucId;
							window.opener.document.forms['frgrm']['cSccId_DocId'].value  = xDocId;
							window.opener.document.forms['frgrm']['cSccId_DocSuf'].value = xDocSuf;
						break;
					};
				}

				function f_CargaValid(xSucId,xDocId,xDocSuf,xSecuencia,xTipo) {
					switch (xTipo) {
						case "GRID":
							parent.fmwork.document.forms['frgrm']['cSccId'+xSecuencia].value  = xDocId;
							parent.fmwork.document.forms['frgrm']['cSucId'+xSecuencia].value  = xSucId;
							parent.fmwork.document.forms['frgrm']['cDocId'+xSecuencia].value  = xDocId;
							parent.fmwork.document.forms['frgrm']['cDocSuf'+xSecuencia].value = xDocSuf;
						break;
						default:
							parent.fmwork.document.forms['frgrm']['cSccId'].value        = xDocId;
							parent.fmwork.document.forms['frgrm']['cSccId_SucId'].value  = xSucId;
							parent.fmwork.document.forms['frgrm']['cSccId_DocId'].value  = xDocId;
							parent.fmwork.document.forms['frgrm']['cSccId_DocSuf'].value = xDocSuf;
						break;
					};
				}
			</script>
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
	  									//Buscando si el subcentro de costo es un DO
  										$qTramite  = "SELECT sucidxxx, docidxxx, docsufxx, regestxx ";
  										$qTramite .= "FROM $cAlfa.sys00121 ";
  										$qTramite .= "WHERE ";
  										$qTramite .= "ccoidxxx = \"$gCcoId\" AND ";
  										$qTramite .= "docidxxx = \"$gSccId\" ";
  										$xTramite  = f_MySql("SELECT","",$qTramite,$xConexion01,"");	  	
  										//f_Mensaje(__FILE__,__LINE__,$qTramite." ~ ".mysql_num_rows($xTramite)."~".$gType);
	  									if (mysql_num_rows($xTramite) == 1) {
	  										$vTramite = mysql_fetch_array($xTramite); ?>
												<script languaje = "javascript">
													f_CargaValid('<?php echo $vTramite['sucidxxx'] ?>','<?php echo $vTramite['docidxxx'] ?>','<?php echo $vTramite['docsufxx'] ?>','<?php echo $gSecuencia ?>','<?php echo $gType ?>');
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW","<?php echo $gSecuencia ?>","<?php echo $gType ?>");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
		  								//Buscando si el subcentro de costo es un DO
  										$qTramite  = "SELECT sucidxxx, docidxxx, docsufxx, regestxx ";
  										$qTramite .= "FROM $cAlfa.sys00121 ";
  										$qTramite .= "WHERE ";
  										$qTramite .= "ccoidxxx = \"$gCcoId\" AND ";
  										$qTramite .= "docidxxx = \"$gSccId\" ";
  										$qTramite .= "ORDER BY sucidxxx,docidxxx,docsufxx ";
  										$xTramite  = f_MySql("SELECT","",$qTramite,$xConexion01,"");	  	
	  									//f_Mensaje(__FILE__,__LINE__,$qTramite." ~ ".mysql_num_rows($xTramite));
 											if (mysql_num_rows($xTramite) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "250">
														<tr>
															<td widht = "040" Class = "name"><center>Suc</center></td>
															<td widht = "140" Class = "name"><center>Do</center></td>
															<td widht = "030" Class = "name"><center>Suf</center></td>
															<td widht = "040" Class = "name"><center>Estado</center></td>
														</tr>
														<?php while ($xRT = mysql_fetch_array($xTramite)) {
															if (mysql_num_rows($xTramite) > 1) { ?>
																<tr>
																	<td width = "040" Class = "name"><?php echo $xRT['sucidxxx'] ?></td>
																	<td width = "140" Class = "name">
																		<a href = "javascript: 
																								f_Carga('<?php echo $xRT['sucidxxx'] ?>','<?php echo $xRT['docidxxx'] ?>','<?php echo $xRT['docsufxx'] ?>','<?php echo $gSecuencia ?>','<?php echo $gType ?>');
																								window.close()"><?php echo $xRT['docidxxx'] ?>
																		</a>
																	</td>
																	<td width = "030" Class = "name"><?php echo $xRT['docsufxx'] ?></td>
																	<td width = "040" Class = "name"><?php echo $xRT['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	f_Carga('<?php echo $xRT['sucidxxx'] ?>','<?php echo $xRT['docidxxx'] ?>','<?php echo $xRT['docsufxx'] ?>','<?php echo $gSecuencia ?>','<?php echo $gType ?>');
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else {
		 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Debe digitar el Numero del DO Exacto, Verifique."); ?>
												<script languaje="javascript">
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
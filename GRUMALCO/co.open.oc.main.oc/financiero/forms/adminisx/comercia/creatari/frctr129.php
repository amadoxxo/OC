<!--
	Los Parametros que Recibo son :
	$gSearch = Un dato para filtrar la consulta con el WHERE
	$gFields = A que campos del formulario voy a devolver los datos
	$gWhat   = Que voy a hacer, una ventana o una validacion
-->
<?php
  include("../../../../libs/php/utility.php");
?>

<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica de Conceptos de Cobro</title>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
	   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
	  </head>
	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>

	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="700">
				<tr>
					<td>
						<fieldset>
			   			<legend>Parametrica de Conceptos de Cobro</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":
		  								$qSqlCom  = "SELECT *, ";
								    	$qSqlCom .= "IF(serdespx <> \"\",serdespx,serdesxx) AS serdesxx, ";
                      $qSqlCom .= "SUBSTRING(seridxxx,1,1) AS serid ";
								    	$qSqlCom .= "FROM $cAlfa.fpar0129 ";
								    	$qSqlCom .= "WHERE ";
								    	if ($gSerId != "") {
											$qSqlCom .= "seridxxx LIKE \"%$gSerId%\" AND ";
											}
											if ($gSerDes != "") {
												$qSqlCom .= "serdesxx LIKE \"%$gSerDes%\" AND ";
											}
								    	$qSqlCom .= "regestxx = \"ACTIVO\" ORDER BY serid,abs(seridxxx),sertopxx";
	  									$xSqlCom  = f_MySql("SELECT","",$qSqlCom,$xConexion01,"");
                      // f_Mensaje(__FILE__,__LINE__,$qSqlCom."~".mysql_num_rows($xSqlCom));
											
 											if ($xSqlCom && mysql_num_rows($xSqlCom) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "700">
														<tr>
															<td widht = "050" Class = "name"><center>Codigo</center></td>
															<td widht = "050" Class = "name"><center>Operacion</center></td>
															<td widht = "550" Class = "name"><center>Descripcion</center></td>
															<td widht = "050" Class = "name"><center>Estado</center></td>
														</tr>
														<?php while ($zRow = mysql_fetch_array($xSqlCom)) {
															
															$cPucRfteId  = "";	
															$cPucARfteId = "";	
															if ($zRow['pucrftex'] != "") {
																/* Traigo Descripcion de la Cuenta cPucRfte en la fpar0115 */
																$qSqlCta1 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRow['pucrftex']}\" LIMIT 0,1";
																$xSqlCta1 = f_MySql("SELECT","",$qSqlCta1,$xConexion01,"");
																while ($zRCta1 = mysql_fetch_array($xSqlCta1)) {
																	$cPucRfteId = $zRow['pucrftex'];
																}
															}
															
															if ($zRow['pucaftex'] != "") {
																/* Traigo Descripcion de la Cuenta cPucRfte en la fpar0115 */
																$qSqlCta1 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRow['pucaftex']}\" LIMIT 0,1";
																$xSqlCta1 = f_MySql("SELECT","",$qSqlCta1,$xConexion01,"");
																while ($zRCta1 = mysql_fetch_array($xSqlCta1)) {
																	$cPucARfteId = $zRow['pucaftex'];
																}
															}
															if (mysql_num_rows($xSqlCom) > 1) { ?>
																<tr>
																	<td width = "050" Class = "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cSerId'].value      ='<?php echo $zRow['seridxxx'] ?>';
																													window.opener.document.forms['frgrm']['cSerDes'].value     ='<?php echo $zRow['serdesxx'] ?>';
																													window.opener.document.forms['frgrm']['cFcoIds'].value     ='<?php echo $zRow['fcoidxxx'] ?>';
																													if('<?php echo $gTipo ?>' != 'REPORTE') {
																														window.opener.document.forms['frgrm']['cSerDesPc'].value   ='<?php echo ($zRow['serdespx'] <> "")?$zRow['serdespx']:$zRow['serdesxx']; ?>';
																														window.opener.document.forms['frgrm']['cSerTop'].value     ='<?php echo $zRow['sertopxx'] ?>';
																														window.opener.document.forms['frgrm']['cSercones'].value   ='<?php echo $zRow['sercones'] ?>';	
																														window.opener.document.forms['frgrm']['cPucRfte'].value    ='';
																														window.opener.document.forms['frgrm']['cPucRfteCto'].value ='';
																														window.opener.document.forms['frgrm']['cPucRfteD'].value   ='';
																														window.opener.document.forms['frgrm']['cRetRfte'].value    ='';
																														window.opener.document.forms['frgrm']['cPucARfte'].value   ='';
																														window.opener.document.forms['frgrm']['cPucARfteCto'].value='';
																														window.opener.document.forms['frgrm']['cPucARfteD'].value  ='';
																														window.opener.document.forms['frgrm']['cARetRfte'].value   ='';																														
																														if('<?php echo $cPucRfteId ?>' != '' || '<?php echo $cPucARfteId ?>' != '') {
																															window.opener.document.forms['frgrm']['cPucRfteCto'].value    ='<?php echo $cPucRfteId ?>';
																															window.opener.document.forms['frgrm']['cPucARfteCto'].value   ='<?php echo $cPucARfteId ?>';
																															window.opener.document.getElementById('tblDatTri').style.display ='block';
																														} else {
																															window.opener.document.getElementById('tblDatTri').style.display ='none';
																														}
																													}
																													window.close()">&nbsp;<?php echo $zRow['seridxxx'] ?></a></td>
																	<td width = "050" Class = "name"><center><?php echo $zRow['sertopxx'] ?></center></td>
																	<td width = "550" Class = "name">&nbsp;<?php echo $zRow['serdesxx'] ?></td>
																	<td width = "050" Class = "name"><center><?php echo $zRow['regestxx'] ?></center></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cSerId'].value      = "<?php echo $zRow['seridxxx'] ?>";
																	window.opener.document.forms['frgrm']['cSerDes'].value     = "<?php echo $zRow['serdesxx'] ?>";
																	window.opener.document.forms['frgrm']['cFcoIds'].value     = "<?php echo $zRow['fcoidxxx'] ?>";
																	if('<?php echo $gTipo ?>' != 'REPORTE') {
																		window.opener.document.forms['frgrm']['cSerDesPc'].value   = "<?php echo ($zRow['serdespx'] <> "")?$zRow['serdespx']:$zRow['serdesxx']; ?>";
																		window.opener.document.forms['frgrm']['cSerTop'].value     = "<?php echo $zRow['sertopxx'] ?>";
																		window.opener.document.forms['frgrm']['cSercones'].value   = "<?php echo $zRow['sercones'] ?>";
																		window.opener.document.forms['frgrm']['cPucRfte'].value    = "";
																		window.opener.document.forms['frgrm']['cPucRfteCto'].value = "";
																		window.opener.document.forms['frgrm']['cPucRfteD'].value   = "";
																		window.opener.document.forms['frgrm']['cRetRfte'].value    = "";
																		window.opener.document.forms['frgrm']['cPucARfte'].value   = "";
																		window.opener.document.forms['frgrm']['cPucARfteCto'].value= "";
																		window.opener.document.forms['frgrm']['cPucARfteD'].value  = "";
																		window.opener.document.forms['frgrm']['cARetRfte'].value   = "";
																		if("<?php echo $cPucRfteId ?>" != "" || "<?php echo $cPucARfteId ?>" != "") {
																			window.opener.document.forms['frgrm']['cPucRfteCto'].value    ='<?php echo $cPucRfteId ?>';
																			window.opener.document.forms['frgrm']['cPucARfteCto'].value   ='<?php echo $cPucARfteId ?>';
																			window.opener.document.getElementById('tblDatTri').style.display ="block";
																		} else {
																			window.opener.document.getElementById('tblDatTri').style.display ="none";
																		}
																	}
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else {
		 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); ?>
												<script languaje="javascript">
													window.opener.document.forms['frgrm']['cSerId'].value       = "";
													window.opener.document.forms['frgrm']['cSerDes'].value      = "";
													window.opener.document.forms['frgrm']['cFcoIds'].value      = "";
													if('<?php echo $gTipo ?>' != 'REPORTE') {
														window.opener.document.forms['frgrm']['cSerDesPc'].value    = "";
														window.opener.document.forms['frgrm']['cSerTop'].value      = "";
														window.opener.document.forms['frgrm']['cSercones'].value    = "";
														window.opener.document.forms['frgrm']['cPucRfte'].value     ="";
														window.opener.document.forms['frgrm']['cPucRfteCto'].value  ="";
														window.opener.document.forms['frgrm']['cPucRfteD'].value    ="";
														window.opener.document.forms['frgrm']['cRetRfte'].value     ="";
														window.opener.document.forms['frgrm']['cPucARfte'].value    ="";
														window.opener.document.forms['frgrm']['cPucARfteCto'].value ="";
														window.opener.document.forms['frgrm']['cPucARfteD'].value   ="";
														window.opener.document.forms['frgrm']['cARetRfte'].value    ="";	
														window.opener.document.getElementById('tblDatTri').style.display ="none";
													}
                          window.close();
												</script>
											<?php }
		  							break;
	  								case "VALID":
	  									$qSqlCom  = "SELECT *, ";
								    	$qSqlCom .= "IF(serdespx <> \"\",serdespx,serdesxx) AS serdesxx, ";
                      $qSqlCom .= "SUBSTRING(seridxxx,1,1) AS serid ";
											$qSqlCom .= "FROM $cAlfa.fpar0129 ";
											$qSqlCom .= "WHERE ";
											if ($gSerId != "") {
												$qSqlCom .= "seridxxx = \"$gSerId\" AND ";
											}
											if ($gSerDes != "") {
												$qSqlCom .= "serdesxx = \"$gSerDes\" AND ";
											}
											$qSqlCom .= "regestxx = \"ACTIVO\" ORDER BY serid,abs(seridxxx),sertopxx";
	  									$xSqlCom  = f_MySql("SELECT","",$qSqlCom,$xConexion01,"");

	  									if (mysql_num_rows($xSqlCom) > 0) {
	  										while ($zRow = mysql_fetch_array($xSqlCom)) { 
													$cPucRfteId  = "";	
													$cPucARfteId = "";	
													
													if ($zRow['pucrftex'] != "") {
														/* Traigo Descripcion de la Cuenta cPucRfte en la fpar0115 */
														$qSqlCta1 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRow['pucrftex']}\" LIMIT 0,1";
														$xSqlCta1 = f_MySql("SELECT","",$qSqlCta1,$xConexion01,"");
														//f_Mensaje(__FILE__,__LINE__,$qSqlCta1."~".mysql_num_rows($xSqlCta1));
														while ($zRCta1 = mysql_fetch_array($xSqlCta1)) {
															$cPucRfteId = $zRow['pucrftex'];
														}
													}

													if ($zRow['pucaftex'] != "") {
														/* Traigo Descripcion de la Cuenta cPucRfte en la fpar0115 */
														$qSqlCta1 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRow['pucaftex']}\" LIMIT 0,1";
														$xSqlCta1 = f_MySql("SELECT","",$qSqlCta1,$xConexion01,"");
														while ($zRCta1 = mysql_fetch_array($xSqlCta1)) {
															$cPucARfteId = $zRow['pucaftex'];
														}
													} ?>
													<script languaje = "javascript">
														parent.fmwork.document.forms['frgrm']['cSerId'].value      = '<?php echo $zRow['seridxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cSerDes'].value     = '<?php echo $zRow['serdesxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cFcoIds'].value     = '<?php echo $zRow['fcoidxxx'] ?>';
														if('<?php echo $gTipo ?>' != 'REPORTE') {
															parent.fmwork.document.forms['frgrm']['cSerDesPc'].value   = '<?php echo ($zRow['serdespx'] <> "")?$zRow['serdespx']:$zRow['serdesxx']; ?>';
															parent.fmwork.document.forms['frgrm']['cSerTop'].value     = '<?php echo $zRow['sertopxx'] ?>';
															parent.fmwork.document.forms['frgrm']['cSercones'].value   = "<?php echo $zRow['sercones'] ?>";
															parent.fmwork.document.forms['frgrm']['cPucRfte'].value    ='';
															parent.fmwork.document.forms['frgrm']['cPucRfteCto'].value ='';
															parent.fmwork.document.forms['frgrm']['cPucRfteD'].value   ='';
															parent.fmwork.document.forms['frgrm']['cRetRfte'].value    ='';
															parent.fmwork.document.forms['frgrm']['cPucARfte'].value   ='';
															parent.fmwork.document.forms['frgrm']['cPucARfteCto'].value='';
															parent.fmwork.document.forms['frgrm']['cPucARfteD'].value  ='';
															parent.fmwork.document.forms['frgrm']['cARetRfte'].value   ='';
															if("<?php echo $cPucRfteId ?>" != "" || "<?php echo $cPucARfteId ?>" != "") {
																parent.fmwork.document.forms['frgrm']['cPucRfteCto'].value    ='<?php echo $cPucRfteId ?>';
																parent.fmwork.document.forms['frgrm']['cPucARfteCto'].value   ='<?php echo $cPucARfteId ?>';
																parent.fmwork.document.getElementById('tblDatTri').style.display ='block';
															}	else {
																parent.fmwork.document.getElementById('tblDatTri').style.display ='none'; 
															}
														}
													</script>
	  										<?php } ?>
	  									<?php } else { ?>
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
	?><script>close();</script>
	<?php
} ?>
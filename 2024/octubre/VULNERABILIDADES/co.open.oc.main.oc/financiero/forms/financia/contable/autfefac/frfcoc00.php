<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");
?>

<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Param&eacute;trica de Facturas</title>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
	  </head>
	  <body topmargin = 0 leftmargin = 0 marginwidth = 0 marginheight = 0 style = 'margin-right:0'>

	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="250">
				<tr>
					<td>
						<fieldset>
			   			<legend>Param&eacute;trica de Facturas</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":
	  									##Traigo Datos de la factura ## 						  		
						 					$qFacDat  = "SELECT ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comidxxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcodxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcscxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcsc2x, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.teridxxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comfecxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx  ";
											$qFacDat .= "FROM $cAlfa.fcoc$cPerAno ";
											$qFacDat .= "WHERE ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comidxxx = 'F' AND ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcscxx LIKE \"%$cComCsc%\" AND ";											
											$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx = \"ACTIVO\" ";
											$qFacDat .= "ORDER BY $cAlfa.fcoc$cPerAno.comfecxx ASC ";
											$xFacDat = f_MySql("SELECT","",$qFacDat,$xConexion01,"");	
																	
										
											## FIN Traigo Factura ##						
											
	  									if ($xFacDat && mysql_num_rows($xFacDat) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "250">
														<tr>
														<td width = "050" Class = "name"><center>ID</center></td>
														<td width = "050" Class = "name"><center>COD</center></td>
														<td width = "100" Class = "name"><center>FACTURA</center></td>
														<td width = "050" Class = "name"><center>ESTADO</center></td>
														
														</tr>
														<?php 
														while ($mFacDat = mysql_fetch_array($xFacDat)) {
															
															##Traigo Nombre del Importador ##
															$qCliDat  = "SELECT ";
															$qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\", $cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX ";
															$qCliDat .= "FROM $cAlfa.SIAI0150 ";
															$qCliDat .= "WHERE ";
															$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mFacDat['teridxxx']}\" LIMIT 0,1 ";
															$xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");																
															$vCliDat = mysql_fetch_array($xCliDat);
															##Fin Traigo Nombre del Importador ##
															
															
															if (mysql_num_rows($xFacDat) > 1) { ?>
																<tr>
																	<td width = "050" class= "name"> <?php echo $mFacDat['comidxxx'] ?></td>
																	<td width = "050" class= "name"> <?php echo $mFacDat['comcodxx'] ?></td>
																	<td width = "100" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cComId'].value  ='<?php echo $mFacDat['comidxxx']?>';
																													window.opener.document.forms['frgrm']['cComCod'].value ='<?php echo $mFacDat['comcodxx']?>';
																													window.opener.document.forms['frgrm']['cComCsc'].value ='<?php echo $mFacDat['comcscxx']?>';
																													window.opener.document.forms['frgrm']['cCliId'].value  ='<?php echo $mFacDat['teridxxx']?>';
																													window.opener.document.forms['frgrm']['cCliDV'].value  ='<?php echo f_Digito_Verificacion($mFacDat['teridxxx']) ?>';
																													window.opener.document.forms['frgrm']['cCliNom'].value ='<?php echo $vCliDat['CLINOMXX']?>';
																													window.opener.document.forms['frgrm']['cComCsc2'].value='<?php echo $mFacDat['comcsc2x']?>';
																													window.opener.document.forms['frgrm']['dComFec'].value ='<?php echo $mFacDat['comfecxx']?>';
																													close()"><?php echo $mFacDat['comcscxx'] ?></a></td>
																	<td width = "050" class= "name"> <?php echo $mFacDat['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>														
																<script language="javascript">
																	window.opener.document.forms['frgrm']['cComId'].value  = '<?php echo $mFacDat['comidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cComCod'].value = '<?php echo $mFacDat['comcodxx'] ?>';
																	window.opener.document.forms['frgrm']['cComCsc'].value = '<?php echo $mFacDat['comcscxx'] ?>';
																	window.opener.document.forms['frgrm']['cCliId'].value  = '<?php echo $mFacDat['teridxxx'] ?>';
																	window.opener.document.forms['frgrm']['cCliDV'].value  = '<?php echo f_Digito_Verificacion($mFacDat['teridxxx']) ?>';																
																	window.opener.document.forms['frgrm']['cCliNom'].value = '<?php echo $vCliDat['CLINOMXX'] ?>';
																	window.opener.document.forms['frgrm']['cComCsc2'].value= '<?php echo $mFacDat['comcsc2x']?>';
																	window.opener.document.forms['frgrm']['dComFec'].value = '<?php echo $mFacDat['comfecxx']?>';																																	
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
	  									<?php	
	  									
	  									} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");?>														
													<script language="javascript">
														window.opener.document.forms['frgrm']['cComId'].value  = '';
														window.opener.document.forms['frgrm']['cComCod'].value = '';
														window.opener.document.forms['frgrm']['cComCsc'].value = '';
														window.opener.document.forms['frgrm']['cCliId'].value  = '';
														window.opener.document.forms['frgrm']['cCliDV'].value  = '';																
														window.opener.document.forms['frgrm']['cCliNom'].value = '';
														window.opener.document.forms['frgrm']['cComCsc2'].value= '';
														window.opener.document.forms['frgrm']['dComFec'].value = '';																																		
														window.close();
													</script>
											<?php }
										break;

	  								case "VALID":
	  									##Traigo Datos de la factura ## 						  		
						 
											$qFacDat  = "SELECT ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comidxxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcodxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcscxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcsc2x, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.teridxxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comfecxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx  ";
											$qFacDat .= "FROM $cAlfa.fcoc$cPerAno ";
											$qFacDat .= "WHERE ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comidxxx = 'F' AND ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcscxx = \"$cComCsc\" AND ";											
											$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx = \"ACTIVO\" ";
											$qFacDat .= "ORDER BY $cAlfa.fcoc$cPerAno.comfecxx ASC ";
											$xFacDat = f_MySql("SELECT","",$qFacDat,$xConexion01,"");								
										
											## FIN Traigo Factura ##
																						
	  									if ($xFacDat && mysql_num_rows($xFacDat) == 1) {
	  										while ($mFacDat = mysql_fetch_array($xFacDat)) { 
	  										
	  											##Traigo Nombre del Importador ##
													$qCliDat  = "SELECT ";
													$qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\", $cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX ";
													$qCliDat .= "FROM $cAlfa.SIAI0150 ";
													$qCliDat .= "WHERE ";
													$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mFacDat['teridxxx']}\" LIMIT 0,1 ";
													//f_Mensaje (__FILE__, __LINE__,$qCliDat);
													$xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");																
													$vCliDat = mysql_fetch_array($xCliDat);
													##Fin Traigo Nombre del Importador ##	  											
	  											
	  											?>
													<script language = "javascript">
														parent.fmwork.document.forms['frgrm']['cComId'].value  = '<?php echo $mFacDat['comidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cComCod'].value = '<?php echo $mFacDat['comcodxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cComCsc'].value = '<?php echo $mFacDat['comcscxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cCliId'].value  = '<?php echo $mFacDat['teridxxx'] ?>';														
														parent.fmwork.document.forms['frgrm']['cCliDV'].value  = '<?php echo f_Digito_Verificacion($mFacDat['teridxxx']) ?>';
														parent.fmwork.document.forms['frgrm']['cCliNom'].value = '<?php echo $vCliDat['CLINOMXX'] ?>';												
														parent.fmwork.document.forms['frgrm']['cComCsc2'].value= '<?php echo $mFacDat['comcsc2x']?>';
														parent.fmwork.document.forms['frgrm']['dComFec'].value = '<?php echo $mFacDat['comfecxx']?>';
													</script>
	      	      				<?php break;
	  										}
	  									} else { ?>
												<script language = "javascript">
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
} 
?>
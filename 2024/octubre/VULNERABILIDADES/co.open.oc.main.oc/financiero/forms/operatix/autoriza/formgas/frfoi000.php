<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Param&eacute;trica de Seriales</title>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
	  </head>
	  <body topmargin = 0 leftmargin = 0 marginwidth = 0 marginheight = 0 style = 'margin-right:0'>

	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
						<fieldset>
			   			<legend>Param&eacute;trica de Seriales</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":

	  									$qForDat  = "SELECT $cAlfa.ffoi0000.*, ";
	  									$qForDat .= "$cAlfa.sys00121.cliidxxx, ";
	  									$qForDat .= "$cAlfa.sys00121.doctipxx ";
	  									$qForDat .= "FROM $cAlfa.ffoi0000 ";
	  									$qForDat .= "LEFT JOIN $cAlfa.sys00121 ON $cAlfa.ffoi0000.doccomex = $cAlfa.sys00121.docidxxx AND ";
	  									$qForDat .= "$cAlfa.ffoi0000.sucidxxx = $cAlfa.sys00121.sucidxxx AND ";
	  									$qForDat .= "$cAlfa.ffoi0000.docsufxx = $cAlfa.sys00121.docsufxx ";
	  									$qForDat .= "WHERE ";
	  									$qForDat .= "$cAlfa.ffoi0000.seridxxx LIKE \"%$cSerId%\" AND ";
	  									$qForDat .= "$cAlfa.ffoi0000.regestxx = \"CONDO\" ";
	  									//f_Mensaje(__FILE__, __LINE__, $qForDat);
	  									$xForDat = f_MySql("SELECT","",$qForDat,$xConexion01,"");


	  									if ($xForDat && mysql_num_rows($xForDat) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td width = "050" Class = "name"><center>SERIAL</center></td>
															<td width = "400" Class = "name"><center>DO</center></td>
															<td width = "400" Class = "name"><center>SUF</center></td>
															<td width = "400" Class = "name"><center>SUC</center></td>
															<td width = "400" Class = "name"><center>DIR.CUENTA</center></td>
															<td width = "050" Class = "name"><center>ESTADO</center></td>
														</tr>
														<?php while ($mForDat = mysql_fetch_array($xForDat)) {
															
															##Traigo Nombre del Importador ##
															$qCliDat  = "SELECT ";
															$qCliDat .= "$cAlfa.SIAI0150.CLINOMXX ";
															//$qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\", $cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX ";
															$qCliDat .= "FROM $cAlfa.SIAI0150 ";
															$qCliDat .= "WHERE ";
															$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mForDat['cliidxxx']}\" LIMIT 0,1 ";
															//echo ($qCliDat);
															$xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");																
															$vCliDat = mysql_fetch_array($xCliDat);
																			
															##Fin Traigo Nombre del Importador ##
															
															if (mysql_num_rows($xForDat ) > 1) { ?>
																<tr>
																	<td width = "050" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cSerId'].value  ='<?php echo $mForDat['seridxxx']?>';
																													window.opener.document.forms['frgrm']['cDocComex'].value ='<?php echo $mForDat['doccomex']?>';
																													window.opener.document.forms['frgrm']['cDocSuf'].value ='<?php echo $mForDat['docsufxx']?>';
																													window.opener.document.forms['frgrm']['cSucId'].value ='<?php echo $mForDat['sucidxxx']?>';																													
																													window.opener.document.forms['frgrm']['cDocTip'].value ='<?php echo $mForDat['doctipxx']?>';
																													window.opener.document.forms['frgrm']['cCliid'].value ='<?php echo $mForDat['cliidxxx']?>';
																													window.opener.document.forms['frgrm']['cCliDV'].value ='<?php echo f_Digito_Verificacion($mForDat['cliidxxx']) ?>';
																													window.opener.document.forms['frgrm']['cCliNom'].value = '<?php echo $vCliDat['CLINOMXX'] ?>';
																													close()"><?php echo $mForDat['seridxxx'] ?></a></td>
																	<td width = "100" class= "name"> <?php echo $mForDat['doccomex'] ?></td>
																	<td width = "050" class= "name"> <?php echo $mForDat['docsufxx'] ?></td>
																	<td width = "050" class= "name"> <?php echo $mForDat['sucidxxx'] ?></td>
																	<td width = "050" class= "name"> <?php echo $mForDat['diridxxx'] ?></td>
																	<td width = "050" class= "name"> <?php echo $mForDat['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script language="javascript">
																	window.opener.document.forms['frgrm']['cSerId'].value  = '<?php echo $mForDat['seridxxx'] ?>';
																	window.opener.document.forms['frgrm']['cSucId'].value = '<?php echo $mForDat['sucidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cDocComex'].value = '<?php echo $mForDat['doccomex'] ?>';
																	window.opener.document.forms['frgrm']['cDocSuf'].value = '<?php echo $mForDat['docsufxx'] ?>';
																	window.opener.document.forms['frgrm']['cDocTip'].value = '<?php echo $mForDat['doctipxx'] ?>';																	
																	window.opener.document.forms['frgrm']['cCliid'].value = '<?php echo $mForDat['cliidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cCliDV'].value = "<?php echo f_Digito_Verificacion($mForDat['cliidxxx']) ?>";
																	window.opener.document.forms['frgrm']['cCliNom'].value = '<?php echo $vCliDat['CLINOMXX'] ?>';
																	close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
	  									}
	  								break;

	  								case "VALID":

											$qForDat  = "SELECT $cAlfa.ffoi0000.*, ";
	  									$qForDat .= "$cAlfa.sys00121.cliidxxx, ";
	  									$qForDat .= "$cAlfa.sys00121.doctipxx ";
	  									$qForDat .= "FROM $cAlfa.ffoi0000 ";
	  									$qForDat .= "LEFT JOIN $cAlfa.sys00121 ON $cAlfa.ffoi0000.doccomex = $cAlfa.sys00121.docidxxx AND ";
	  									$qForDat .= "$cAlfa.ffoi0000.sucidxxx = $cAlfa.sys00121.sucidxxx AND ";
	  									$qForDat .= "$cAlfa.ffoi0000.docsufxx = $cAlfa.sys00121.docsufxx ";
	  									$qForDat .= "WHERE ";
	  									$qForDat .= "$cAlfa.ffoi0000.seridxxx LIKE \"%$cSerId%\" AND ";
	  									$qForDat .= "$cAlfa.ffoi0000.regestxx = \"CONDO\" ";
	  									//f_Mensaje(__FILE__, __LINE__, $qForDat);
	  									$xForDat = f_MySql("SELECT","",$qForDat,$xConexion01,"");
	  									if ($xForDat && mysql_num_rows($xForDat) == 1) {
	  										while ($mForDat = mysql_fetch_array($xForDat)) { 
	  											##Traigo Nombre del Importador ##
													$qCliDat  = "SELECT ";
													$qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\", $cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX ";
													$qCliDat .= "FROM $cAlfa.SIAI0150 ";
													$qCliDat .= "WHERE ";
													$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mForDat['cliidxxx']}\" LIMIT 0,1 ";
													//f_Mensaje (__FILE__, __LINE__,$qCliDat);
													$xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");																
													$vCliDat = mysql_fetch_array($xCliDat);
													##Fin Traigo Nombre del Importador ##
	  											
	  											?>
													<script language = "javascript">
														parent.fmwork.document.forms['frgrm']['cSerId'].value  = '<?php echo $mForDat['seridxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cSucId'].value = '<?php echo $mForDat['sucidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cDocComex'].value = '<?php echo $mForDat['doccomex'] ?>';
														parent.fmwork.document.forms['frgrm']['cDocSuf'].value = '<?php echo $mForDat['docsufxx'] ?>';														
														parent.fmwork.document.forms['frgrm']['cDocTip'].value = '<?php echo $mForDat['doctipxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cCliDV'].value = '<?php echo f_Digito_Verificacion($mForDat['cliidxxx']) ?>';														  
														parent.fmwork.document.forms['frgrm']['cCliid'].value = '<?php echo $mForDat['cliidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cCliNom'].value = '<?php echo $vCliDat['CLINOMXX'] ?>';
														close();
													</script>
	      	      				<?php break;
	  										}
	  									} else { ?>
												<script language = "javascript">
												  parent.fmwork.document.forms['frgrm']['cSerId'].value  = "";
													parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
													close();
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

<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");

?>
<html>
		<head>
			<title>Param&eacute;trica de Tipos de Resolucion </title>
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
			   			<legend>Param&eacute;trica de Tipos de Resolucion</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  					<?php 
	  							switch ($gWhat) {
	  								case "WINDOW":
	  									  									
	  									##formularios legalizados##
	  											$zSqlCab  = "SELECT ";
	  											$zSqlCab .= "$cAlfa.sys00121.sucidxxx, ";
													$zSqlCab .= "$cAlfa.sys00121.docidxxx, ";
													$zSqlCab .= "$cAlfa.sys00121.docsufxx, ";													
													$zSqlCab .= "$cAlfa.sys00121.docfrsxx, ";
													$zSqlCab .= "$cAlfa.sys00121.cliidxxx, ";
													$zSqlCab .= "$cAlfa.sys00121.regestxx ";
												  $zSqlCab .= "FROM $cAlfa.sys00121 ";
													$zSqlCab .= "WHERE ";
													$zSqlCab .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" AND ";
													$zSqlCab .= "$cAlfa.sys00121.docidxxx LIKE \"%$cDocId%\" AND ";
													$zSqlCab .= "$cAlfa.sys00121.docfrsxx = \"PRINCIPAL\" ";
													$zSqlCab .= "ORDER BY CONVERT($cAlfa.sys00121.docidxxx,signed) ASC ";
													$xSqlCab = f_MySql("SELECT","",$zSqlCab,$xConexion01,"");
												
													
											## fin formularios legalizados ##		
	  										if ($xSqlCab && mysql_num_rows($xSqlCab) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "250">
														<tr>
														<td width = "050" Class = "name"><center>SUC</center></td>
														<td width = "050" Class = "name"><center>DO</center></td>
														<td width = "050" Class = "name"><center>SUF</center></td>
														<td width = "100" Class = "name"><center>RESOLUCION</center></td>
														
														
														</tr>
														<?php 
														while ($mSqlCab = mysql_fetch_array($xSqlCab)) {
															
															##Traigo Nombre del Importador 
																$qCliDat  = "SELECT ";
																$qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\", $cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX ";
																$qCliDat .= "FROM $cAlfa.SIAI0150 ";
																$qCliDat .= "WHERE ";
																$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mSqlCab['cliidxxx']}\" LIMIT 0,1 ";
																$xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");																
																$vCliDat = mysql_fetch_array($xCliDat);
															##Fin Traigo Nombre del Importador ##	
															
																
															if (mysql_num_rows($xSqlCab) > 1) { ?>
																<tr>
																	<td width = "050" class= "name"> <?php echo $mSqlCab['sucidxxx'] ?></td>																	
																	<td width = "100" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cSucId'].value  ='<?php echo $mSqlCab['sucidxxx']?>';
																													window.opener.document.forms['frgrm']['cDocId'].value ='<?php echo $mSqlCab['docidxxx']?>';
																													window.opener.document.forms['frgrm']['cDocSuf'].value ='<?php echo $mSqlCab['docsufxx']?>';
																													window.opener.document.forms['frgrm']['cDocFsr'].value  ='<?php echo $mSqlCab['docfrsxx']?>';
																													window.opener.document.forms['frgrm']['cCliId'].value  ='<?php echo $mSqlCab['cliidxxx']?>';
																													window.opener.document.forms['frgrm']['cCliDV'].value   ='<?php echo f_Digito_Verificacion($mSqlCab['cliidxxx']) ?>';
																													window.opener.document.forms['frgrm']['cCliNom'].value ='<?php echo $vCliDat['CLINOMXX']?>';
																													close()"><?php echo $mSqlCab['docidxxx'] ?></a></td>
																	<td width = "050" class= "name"> <?php echo $mSqlCab['docsufxx'] ?></td>
																	<td width = "050" class= "name"> <?php echo $mSqlCab['docfrsxx'] ?></td>
																</tr>
															<?php	} else { ?>
														
																<script language="javascript">
																	window.opener.document.forms['frgrm']['cSucId'].value  = '<?php echo $mSqlCab['sucidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cDocId'].value = '<?php echo $mSqlCab['docidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cDocSuf'].value = '<?php echo $mSqlCab['docsufxx'] ?>';
																	window.opener.document.forms['frgrm']['cDocFsr'].value = '<?php echo $mSqlCab['docfrsxx'] ?>';
																	window.opener.document.forms['frgrm']['cCliId'].value  = '<?php echo $mSqlCab['cliidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cCliDV'].value  = '<?php echo f_Digito_Verificacion($mSqlCab['cliidxxx']) ?>';																
																	window.opener.document.forms['frgrm']['cCliNom'].value = '<?php echo $vCliDat['CLINOMXX'] ?>';																																
																	window.close();
																</script>
															<?php }
															
															
															
														} ?>
													</table>
												</center>
	  									<?php	
	  									
	  									} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
	  											  										
	  									}			  		
						 
										break;

	  								case "VALID":
	  									
	  									##formularios legalizados##
	  											$zSqlCab  = "SELECT ";
	  											$zSqlCab .= "$cAlfa.sys00121.sucidxxx, ";
													$zSqlCab .= "$cAlfa.sys00121.docidxxx, ";
													$zSqlCab .= "$cAlfa.sys00121.docsufxx, ";													
													$zSqlCab .= "$cAlfa.sys00121.docfrsxx, ";
													$zSqlCab .= "$cAlfa.sys00121.cliidxxx, ";
													$zSqlCab .= "$cAlfa.sys00121.regestxx ";
												  $zSqlCab .= "FROM $cAlfa.sys00121 ";
													$zSqlCab .= "WHERE ";
													$zSqlCab .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" AND ";
													$zSqlCab .= "$cAlfa.sys00121.docidxxx LIKE \"%$cDocId%\" AND ";
													$zSqlCab .= "$cAlfa.sys00121.docfrsxx = \"PRINCIPAL\" ";
													$zSqlCab .= "ORDER BY CONVERT($cAlfa.sys00121.docidxxx,signed) ASC ";
													$xSqlCab = f_MySql("SELECT","",$zSqlCab,$xConexion01,"");
												
													
											## fin formularios legalizados ##	
											
													
	  								  if ($xSqlCab && mysql_num_rows($xSqlCab) == 1) {
	  										while ($mSqlCab = mysql_fetch_array($xSqlCab)) { 
	  										
	  													##Traigo Nombre del Importador 
																$qCliDat  = "SELECT ";
																$qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\", $cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX ";
																$qCliDat .= "FROM $cAlfa.SIAI0150 ";
																$qCliDat .= "WHERE ";
																$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mSqlCab['cliidxxx']}\" LIMIT 0,1 ";
																$xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");																
																$vCliDat = mysql_fetch_array($xCliDat);
															##Fin Traigo Nombre del Importador ##										
	  										
	  										
	  													?>
													<script language = "javascript">
														parent.fmwork.document.forms['frgrm']['cSucId'].value  = '<?php echo $mSqlCab['sucidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cDocId'].value = '<?php echo $mSqlCab['docidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cDocSuf'].value = '<?php echo $mSqlCab['docsufxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cDocFsr'].value = '<?php echo $mSqlCab['docfrsxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cCliId'].value  = '<?php echo $mSqlCab['cliidxxx'] ?>';														
														parent.fmwork.document.forms['frgrm']['cCliDV'].value  = '<?php echo f_Digito_Verificacion($mSqlCab['cliidxxx']) ?>';
														parent.fmwork.document.forms['frgrm']['cCliNom'].value = '<?php echo $vCliDat['CLINOMXX'] ?>';												
														close();
													</script>
	      	      				<?php break;
	  										}
	  									} else { ?>
												<script language = "javascript">
													parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
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


	  									


	  						
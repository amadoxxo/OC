<?php 
  namespace openComex;
	include("../../../../libs/php/utility.php");
	$qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_SCHEMA = \"$cAlfa\" AND TABLE_NAME = \"par00104\" LIMIT 0,1";
  $xTblCom = f_MySql("SELECT","",$qTblCom,$xConexion01,"");
  $vTblCom = mysql_fetch_array($xTblCom);
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>	
	<html>
		<head>
			<title><?php echo $vTblCom['TABLE_COMMENT'] ?></title>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>	   
		</script> 	
	  </head>
	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>

	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
						<fieldset>
			   			<legend><?php echo $vTblCom['TABLE_COMMENT'] ?></legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) { 
	  								case "WINDOW":
	  									
	  									$qPaiDes  = "SELECT * FROM $cAlfa.par00104 WHERE paidesxx LIKE \"%$cPaiDes%\" AND regestxx = \"ACTIVO\" ";
	  									$xPaiDes = f_MySql("SELECT","",$qPaiDes,$xConexion01,"");
	  									
	  									if ($xPaiDes && mysql_num_rows($xPaiDes) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "050" Class = "name"><center>ID</center></td>
															<td widht = "400" Class = "name"><center>DESCRIPCION</center></td>
															<td widht = "050" Class = "name"><center>ESTADO</center></td>
														</tr>
														<?php while ($mPaiDes = mysql_fetch_array($xPaiDes)) {
															if (mysql_num_rows($xPaiDes) > 1) { ?>
																<tr>
																	<td width = "050" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cPaiIdDf'].value  ='<?php echo $mPaiDes['paiidxxx']?>';
																													window.opener.document.forms['frgrm']['cPaiDesDf'].value ='<?php echo $mPaiDes['paidesxx']?>';
																													close()"><?php echo $mPaiDes['paiidxxx'] ?></a></td>
																	<td width = "400" class= "name"> <?php echo $mPaiDes['paidesxx'] ?></td>
																	<td width = "050" class= "name"> <?php echo $mPaiDes['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cPaiIdDf'].value  = '<?php echo $mPaiDes['paiidxxx'] ?>';																
																	window.opener.document.forms['frgrm']['cPaiDesDf'].value = '<?php echo $mPaiDes['paidesxx'] ?>';
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
	  									
											$qPaiDes  = "SELECT * FROM $cAlfa.par00104 WHERE paiidxxx = \"$cPaiId\" AND regestxx = \"ACTIVO\" ";
	  									$xPaiDes  = f_MySql("SELECT","",$qPaiDes,$xConexion01,"");
	  									
	  									if ($xPaiDes && mysql_num_rows($xPaiDes) > 0) {
	  										while ($mPaiDes = mysql_fetch_array($xPaiDes)) { ?>
													<script languaje = "javascript">
														parent.fmwork.document.forms['frgrm']['cPaiIdDf'].value  = '<?php echo $mPaiDes['paiidxxx'] ?>';													
														parent.fmwork.document.forms['frgrm']['cPaiDesDf'].value = '<?php echo $mPaiDes['paidesxx'] ?>';
														close();
													</script>
	      	      				<?php break;
	  										}
	  									} else { ?>
												<script languaje = "javascript">
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
<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { 
	//f_Mensaje(__FILE__,__LINE__,$cTerId);
?>
	<html>
		<head>
			<title>Parametrica Tipo de Documento</title>
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
			   			<legend>Param&eacute;trica Tipo de Documento</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":

	  									$qDatTdi  = "SELECT * FROM $cAlfa.fpar0109 WHERE tdiidxxx  LIKE \"%$cTdiId%\" AND regestxx = \"ACTIVO\" ";
	  									$xDatTdi = f_MySql("SELECT","",$qDatTdi,$xConexion01,"");


	  									if ($xDatTdi && mysql_num_rows($xDatTdi) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "050" Class = "name"><center>ID</center></td>
															<td widht = "400" Class = "name"><center>TIPO DE DOCUMENTO</center></td>
															<td widht = "050" Class = "name"><center>ESTADO</center></td>
														</tr>
														<?php while ($xDT = mysql_fetch_array($xDatTdi)) {
															if (mysql_num_rows($xDatTdi) > 1) { ?>
																<tr>
																	<td width = "050" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cTdiId'].value  ='<?php echo $xDT['tdiidxxx']?>';
																													window.opener.document.forms['frgrm']['cTdiDes'].value ='<?php echo $xDT['tdidesxx']?>';
																													<?php if($cTerId <> ""){?>
																														window.opener.f_GenDv('<?php echo $cTerId ?>');
																													<?php }?>
																													close()"><?php echo $xDT['tdiidxxx'] ?></a></td>
																	<td width = "400" class= "name"> <?php echo $xDT['tdidesxx'] ?></td>
																	<td width = "050" class= "name"> <?php echo $xDT['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cTdiId'].value  = '<?php echo $xDT['tdiidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cTdiDes'].value = '<?php echo $xDT['tdidesxx'] ?>';
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

											$qDatTdi  = "SELECT * FROM $cAlfa.fpar0109 WHERE tdiidxxx = \"$cTdiId\" AND regestxx = \"ACTIVO\" ";
	  									$xDatTdi = f_MySql("SELECT","",$qDatTdi,$xConexion01,"");

	  									if ($xDatTdi && mysql_num_rows($xDatTdi) > 0) {
	  										while ($xDT = mysql_fetch_array($xDatTdi)) { ?>
													<script languaje = "javascript">
														parent.fmwork.document.forms['frgrm']['cTdiId'].value  = '<?php echo $xDT['tdiidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cTdiDes'].value = '<?php echo $xDT['tdidesxx'] ?>';
														<?php if($cTerId <> ""){?>
															parent.fmwork.f_GenDv('<?php echo $cTerId ?>');
														<?php }?>
														close();
													</script>
	      	      				<?php break;
	  										}
	  									} else { ?>
												<script languaje = "javascript">
												  parent.fmwork.document.forms['frgrm']['cTdiId'].value  = "";
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
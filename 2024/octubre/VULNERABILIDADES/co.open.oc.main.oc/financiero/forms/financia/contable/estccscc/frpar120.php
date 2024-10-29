<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica Centro de Costos</title>
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
			   			<legend>Param&eacute;trica Centro de Costos</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":

	  									$qBanDes  = "SELECT * FROM $cAlfa.fpar0120 WHERE ccoidxxx = \"$gCcoId\" AND sccidxxx LIKE \"%$gSccId%\" AND regestxx = \"ACTIVO\" ";
	  									$xBanDes = f_MySql("SELECT","",$qBanDes,$xConexion01,"");
											//f_Mensaje(__FILE__,__LINE__,$qBanDes."~".mysql_num_rows($xBanDes));

	  									if ($xBanDes && mysql_num_rows($xBanDes) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "050" Class = "name"><center>ID</center></td>
															<td widht = "400" Class = "name"><center>SUBCENTRO COSTO</center></td>
															<td widht = "050" Class = "name"><center>ESTADO</center></td>
														</tr>
														<?php while ($mBanDes = mysql_fetch_array($xBanDes)) {
															if (mysql_num_rows($xBanDes) > 1) { ?>
																<tr>
																	<td width = "050" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cSccId'].value  ='<?php echo $mBanDes['sccidxxx']?>';
																													window.opener.document.forms['frgrm']['cSccDes'].value ='<?php echo $mBanDes['sccdesxx']?>';
																													close()"><?php echo $mBanDes['sccidxxx'] ?></a></td>
																	<td width = "400" class= "name"> <?php echo $mBanDes['sccdesxx'] ?></td>
																	<td width = "050" class= "name"> <?php echo $mBanDes['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cSccId'].value  = '<?php echo $mBanDes['sccidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cSccDes'].value = '<?php echo $mBanDes['sccdesxx'] ?>';
																	close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); ?>
												<script languaje="javascript">
													window.opener.document.forms['frgrm']['cSccId'].value  = '';
													window.opener.document.forms['frgrm']['cSccDes'].value = '';
													close();
												</script>
											<?php }
	  								break;

	  								case "VALID":

											$qBanDes  = "SELECT * FROM $cAlfa.fpar0120 WHERE ccoidxxx = \"$gCcoId\" AND sccidxxx = \"$gSccId\" AND regestxx = \"ACTIVO\" ";
	  									$xBanDes = f_MySql("SELECT","",$qBanDes,$xConexion01,"");

	  									if ($xBanDes && mysql_num_rows($xBanDes) > 0) {
	  										while ($mBanDes = mysql_fetch_array($xBanDes)) { ?>
													<script languaje = "javascript">
														parent.fmwork.document.forms['frgrm']['cSccId'].value  = '<?php echo $mBanDes['sccidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cSccDes'].value = '<?php echo $mBanDes['sccdesxx'] ?>';
														close();
													</script>
	      	      				<?php break;
	  										}
	  									} else { ?>
												<script languaje = "javascript">
												  parent.fmwork.document.forms['frgrm']['cSccId'].value  = "";
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
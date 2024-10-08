<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica Actividad Economica</title>
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
			   			<legend>Param&eacute;trica Actividad Economica</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":

	  									$qAecDes  = "SELECT * FROM $cAlfa.SIAI0101 WHERE AECIDXXX LIKE \"%$cAecId%\" AND REGESTXX  = \"ACTIVO\" ";
	  									$xAecDes = f_MySql("SELECT","",$qAecDes,$xConexion01,"");


	  									if ($xAecDes && mysql_num_rows($xAecDes) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "050" Class = "name"><center>ID</center></td>
															<td widht = "350" Class = "name"><center>ACTIVIDAD ECONOMICA</center></td>
															<td widht = "050" Class = "name"><center>% RET. CREE</center></td>
															<td widht = "050" Class = "name"><center>ESTADO</center></td>
														</tr>
														<?php while ($xAD = mysql_fetch_array($xAecDes)) {
															if (mysql_num_rows($xAecDes) > 1) { ?>
																<tr>
																	<td width = "050" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cAecId'].value  ='<?php echo $xAD['AECIDXXX']?>';
																													window.opener.document.forms['frgrm']['cAecDes'].value ='<?php echo $xAD['AECDESXX']?>';
																													window.opener.document.forms['frgrm']['cAecRet'].value ='<?php echo (($xAD['AECRETXX'] > 0) ? $xAD['AECRETXX']+0 : "") ?>';
																													close()"><?php echo $xAD['AECIDXXX'] ?></a></td>
																	<td width = "350" class= "name"> <?php echo $xAD['AECDESXX'] ?></td>
																	<td width = "050" class= "name" align="right"> <?php echo (($xAD['AECRETXX'] > 0) ? $xAD['AECRETXX']+0 : "&nbsp;") ?></td>
																	<td width = "050" class= "name"> <?php echo $xAD['REGESTXX'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cAecId'].value  = '<?php echo $xAD['AECIDXXX'] ?>';
																	window.opener.document.forms['frgrm']['cAecDes'].value = '<?php echo $xAD['AECDESXX'] ?>';
																	window.opener.document.forms['frgrm']['cAecRet'].value = '<?php echo (($xAD['AECRETXX'] > 0) ? $xAD['AECRETXX']+0 : "") ?>';
																	window.close();
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

											$qAecDes  = "SELECT * FROM $cAlfa.SIAI0101 WHERE AECIDXXX = \"$cAecId\" AND REGESTXX = \"ACTIVO\" ";
	  									$xAecDes = f_MySql("SELECT","",$qAecDes,$xConexion01,"");

	  									if ($xAecDes && mysql_num_rows($xAecDes) > 0) {
	  										while ($xAD = mysql_fetch_array($xAecDes)) { ?>
													<script languaje = "javascript">
														parent.fmwork.document.forms['frgrm']['cAecId'].value  = '<?php echo $xAD['AECIDXXX'] ?>';
														parent.fmwork.document.forms['frgrm']['cAecDes'].value = '<?php echo $xAD['AECDESXX'] ?>';
														parent.fmwork.document.forms['frgrm']['cAecRet'].value = '<?php echo (($xAD['AECRETXX'] > 0) ? $xAD['AECRETXX']+0 : "") ?>';
													</script>
	      	      				<?php break;
	  										}
	  									} else { ?>
												<script languaje = "javascript">
												  parent.fmwork.document.forms['frgrm']['cAecId'].value  = "";
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
<?php } else {
	f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
} ?>
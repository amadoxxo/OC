<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica Grupo de Clientes</title>
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
			   			<legend>Param&eacute;trica Grupo de Clientes</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":

	  									$qGruDes  = "SELECT * FROM $cAlfa.fpar0139 WHERE gruidxxx LIKE \"%$cGruId%\" AND regestxx = \"ACTIVO\" ";
	  									$xGruDes = f_MySql("SELECT","",$qGruDes,$xConexion01,"");


	  									if ($xGruDes && mysql_num_rows($xGruDes) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "050" Class = "name"><center>ID</center></td>
															<td widht = "400" Class = "name"><center>GRUPO DE CLIENTES</center></td>
															<td widht = "050" Class = "name"><center>ESTADO</center></td>
														</tr>
														<?php while ($xGD = mysql_fetch_array($xGruDes)) {
															if (mysql_num_rows($xGruDes) > 1) { ?>
																<tr>
																	<td width = "050" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cGruId'].value  ='<?php echo $xGD['gruidxxx']?>';
																													window.opener.document.forms['frgrm']['cGruDes'].value ='<?php echo $xGD['grudesxx']?>';
																													close()"><?php echo $xGD['gruidxxx'] ?></a></td>
																	<td width = "400" class= "name"> <?php echo $xGD['grudesxx'] ?></td>
																	<td width = "050" class= "name"> <?php echo $xGD['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cGruId'].value  = '<?php echo $xGD['gruidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cGruDes'].value = '<?php echo $xGD['grudesxx'] ?>';
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

											$qGruDes  = "SELECT * FROM $cAlfa.fpar0139 WHERE gruidxxx = \"$cGruId\" AND regestxx = \"ACTIVO\" ";
	  									$xGruDes = f_MySql("SELECT","",$qGruDes,$xConexion01,"");

	  									if ($xGruDes && mysql_num_rows($xGruDes) > 0) {
	  										while ($xGD = mysql_fetch_array($xGruDes)) { ?>
													<script languaje = "javascript">
														parent.fmwork.document.forms['frgrm']['cGruId'].value  = '<?php echo $xGD['gruidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cGruDes'].value = '<?php echo $xGD['grudesxx'] ?>';
														close();
													</script>
	      	      				<?php break;
	  										}
	  									} else { ?>
												<script languaje = "javascript">
												  parent.fmwork.document.forms['frgrm']['cGruId'].value  = "";
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
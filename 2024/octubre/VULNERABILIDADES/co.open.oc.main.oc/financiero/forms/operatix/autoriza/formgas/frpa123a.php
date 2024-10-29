<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica Grupo de Observaciones para Formularios</title>
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
			<table border ="0" cellpadding="0" cellspacing="0" width="350">
				<tr>
					<td>
						<fieldset>
			   			<legend>Param&eacute;trica Grupo de Observaciones para Formularios</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":

	  									$qGruObs  = "SELECT * FROM $cAlfa.fpar0123 WHERE goftipxx = \"FORMULARIOS\" AND gofidxxx LIKE \"%$gGofId%\" AND regestxx = \"ACTIVO\" ";
	  									$xGruObs = f_MySql("SELECT","",$qGruObs,$xConexion01,"");


	  									if ($xGruObs && mysql_num_rows($xGruObs) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "350">
														<tr>
															<td widht = "050" Class = "name"><center>ID</center></td>
															<td widht = "250" Class = "name"><center>DESCRIPCI&Oacute;N</center></td>
															<td widht = "050" Class = "name"><center>ESTADO</center></td>
														</tr>
														<?php while ($xRGO = mysql_fetch_array($xGruObs)) {
															if (mysql_num_rows($xGruObs) > 1) { ?>
																<tr>
																	<td width = "050" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cGofId'].value  ='<?php echo $xRGO['gofidxxx']?>';
																													window.opener.document.forms['frgrm']['cGofDes'].value ='<?php echo $xRGO['gofdesxx']?>';
																													close()"><?php echo $xRGO['gofidxxx'] ?></a></td>
																	<td width = "250" class= "name"> <?php echo $xRGO['gofdesxx'] ?></td>
																	<td width = "050" class= "name"> <?php echo $xRGO['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cGofId'].value  = '<?php echo $xRGO['gofidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cGofDes'].value = '<?php echo $xRGO['gofdesxx'] ?>';
																	window.opener.focus();
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); ?>
												<script languaje="javascript">
													window.opener.document.forms['frgrm']['cGofId'].value  = '';
													window.opener.document.forms['frgrm']['cGofDes'].value = '';
													window.opener.focus();
													window.close();
												</script>
											<?php
	  									}
	  								break;
	  								case "VALID":
											$qGruObs  = "SELECT * FROM $cAlfa.fpar0123 WHERE goftipxx = \"FORMULARIOS\" AND gofidxxx = \"$gGofId\" AND regestxx = \"ACTIVO\" ";
	  									$xGruObs = f_MySql("SELECT","",$qGruObs,$xConexion01,"");
											
	  									if ($xGruObs && mysql_num_rows($xGruObs) > 0) {
	  										while ($xRGO = mysql_fetch_array($xGruObs)) { ?>
													<script languaje = "javascript">
														parent.fmwork.document.forms['frgrm']['cGofId'].value  = '<?php echo $xRGO['gofidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cGofDes'].value = '<?php echo $xRGO['gofdesxx'] ?>';
													</script>
	      	      				<?php break;
	  										}
	  									} else { ?>
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
} ?>
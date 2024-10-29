<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica Tipo Producto Formularios</title>
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
			   			<legend>Param&eacute;trica Tipo Producto Formularios</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":

	  									$qPtoDes  = "SELECT * FROM $cAlfa.fpar0132 WHERE $Calfa.fpar0132.ptoidxxx LIKE \"%$cPtoId%\" AND $cAlfa.fpar0132.regestxx = \"ACTIVO\" ";
	  									$xPtoDes = f_MySql("SELECT","",$qPtoDes,$xConexion01,"");


	  									if ($xPtoDes && mysql_num_rows($xPtoDes) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "050" Class = "name"><center><b>ID</b></center></td>
															<td widht = "400" Class = "name"><center><b>TIPO PRODUCTO</b></center></td>
															<td widht = "050" Class = "name"><center><b>ESTADO</b></center></td>
														</tr>
														<?php while ($mPtoDes = mysql_fetch_array($xPtoDes)) {
															if (mysql_num_rows($xPtoDes) > 1) { ?>
																<tr>
																	<td width = "050" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cPtoId'].value  ='<?php echo $mPtoDes['ptoidxxx']?>';
																													window.opener.document.forms['frgrm']['cPtoDes'].value ='<?php echo $mPtoDes['ptodesxx']?>';
																													close()"><?php echo $mPtoDes['ptoidxxx'] ?></a></td>
																	<td width = "400" class= "name"> <?php echo $mPtoDes['ptodesxx'] ?></td>
																	<td width = "050" class= "name"> <?php echo $mPtoDes['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cPtoId'].value  = '<?php echo $mPtoDes['ptoidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cPtoDes'].value = '<?php echo $mPtoDes['ptodesxx'] ?>';
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

											$qPtoDes  = "SELECT * FROM $cAlfa.fpar0132 WHERE $cAlfa.fpar0132.ptoidxxx = \"$cPtoId\" AND $cAlfa.fpar0132.regestxx = \"ACTIVO\" ";
	  									$xPtoDes = f_MySql("SELECT","",$qPtoDes,$xConexion01,"");

	  									if ($xPtoDes && mysql_num_rows($xPtoDes) > 0) {
	  										while ($mPtoDes = mysql_fetch_array($xPtoDes)) { ?>
													<script languaje = "javascript">
														parent.fmwork.document.forms['frgrm']['cPtoId'].value  = '<?php echo $mPtoDes['ptoidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cPtoDes'].value = '<?php echo $mPtoDes['ptodesxx'] ?>';
														close();
													</script>
	      	      				<?php break;
	  										}
	  									} else { ?>
												<script languaje = "javascript">
												  parent.fmwork.document.forms['frgrm']['cPtoId'].value  = "";
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
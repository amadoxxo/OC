<!--
	Los Parametros que Recibo son :
	$gSearch = Un dato para filtrar la consulta con el WHERE
	$gFields = A que campos del formulario voy a devolver los datos
	$gWhat   = Que voy a hacer, una ventana o una validacion
-->
<?php
  include("../../../../libs/php/utility.php");
?>

<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica de Grupo de Tarifas</title>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
	   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
	  </head>
	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>

	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="550">
				<tr>
					<td>
						<fieldset>
			   			<legend>Parametrica de Grupo de Tarifas</legend>
	  					<form name = "frnav" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":
		  								$qGruTar  = "SELECT gtaidxxx, ";
								    	$qGruTar .= "IF(gtadesxx <> \"\",gtadesxx,\"SIN DESCRIPCION\") AS gtadesxx ";
								    	$qGruTar .= "FROM $cAlfa.fpar0111 ";
		                  $qGruTar .= "WHERE gtaidxxx LIKE \"%$gGtaId%\" AND ";
                      $qGruTar .= "regestxx = \"ACTIVO\" ORDER BY gtaidxxx";
	  									$xGruTar  = f_MySql("SELECT","",$qGruTar,$xConexion01,"");

 											if ($xGruTar && mysql_num_rows($xGruTar) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "550">
														<tr>
															<td widht = "050" Class = "name"><center>Id</center></td>
															<td widht = "350" Class = "name"><center>Descripci&oacute;n</center></td>
															<td widht = "050" Class = "name"><center>Estado</center></td>
														</tr>
														<?php while ($xRGT = mysql_fetch_array($xGruTar)) {
															if (mysql_num_rows($xGruTar) > 1) { ?>
																<tr>
																	<td Class = "name">
																		<a href = "javascript:window.opener.document.forms['frnav']['cGtaId'].value ='<?php echo $xRGT['gtaidxxx'] ?>';
																													window.opener.document.forms['frnav']['cGtaDes'].value='<?php echo $xRGT['gtadesxx'] ?>';
																													window.close()">&nbsp;<?php echo $xRGT['gtaidxxx'] ?></a></td>
																	<td Class = "name">&nbsp;<?php echo $xRGT['gtadesxx'] ?></td>
																	<td Class = "name"><center><?php echo $xRGT['regestxx'] ?></center></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frnav']['cGtaId'].value  = "<?php echo $xRGT['gtaidxxx'] ?>";
																	window.opener.document.forms['frnav']['cGtaDes'].value = "<?php echo $xRGT['gtadesxx'] ?>";
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else {
		 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
		 										?><script>window.close();</script>
		 										<?php
		 									}
		  							break;
	  								case "VALID":
	  									$qGruTar  = "SELECT gtaidxxx, ";
								    	$qGruTar .= "IF(gtadesxx <> \"\",gtadesxx,\"SIN DESCRIPCION\") AS gtadesxx ";
											$qGruTar .= "FROM $cAlfa.fpar0111 ";
                      $qGruTar .= "WHERE gtaidxxx = \"$gGtaId\" AND ";
                      $qGruTar .= "regestxx = \"ACTIVO\" ORDER BY gtaidxxx";
	  									$xGruTar  = f_MySql("SELECT","",$qGruTar,$xConexion01,"");

	  									if (mysql_num_rows($xGruTar) == 1) {
	  										while ($xRGT = mysql_fetch_array($xGruTar)) { ?>
													<script languaje = "javascript">
														parent.fmwork.document.forms['frnav']['cGtaId'].value  = '<?php echo $xRGT['gtaidxxx'] ?>';
														parent.fmwork.document.forms['frnav']['cGtaDes'].value = '<?php echo $xRGT['gtadesxx'] ?>';
													</script>
	  										<?php } ?>
	  									<?php } else { ?>
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
	?><script>close();</script>
	<?php
} ?>

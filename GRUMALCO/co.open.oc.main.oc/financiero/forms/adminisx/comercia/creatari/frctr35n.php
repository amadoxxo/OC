<!--
	Los Parametros que Recibo son :
	$gSearch = Un dato para filtrar la consulta con el WHERE
	$gFields = A que campos del formulario voy a devolver los datos
	$gWhat   = Que voy a hacer, una ventana o una validacion
-->

<?php
  include("../../../../libs/php/utility.php");

  if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica de Clientes por Nombre</title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
	   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">

	  </head>
	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>

	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
						<fieldset>
			   			<legend>Parametrica de Clientes</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":
	  									$qSqlCli  = "SELECT ";
	  									$qSqlCli .= "$cAlfa.SIAI0150.CLIIDXXX, ";
	  									$qSqlCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) AS CLINOMXX, ";
	  									$qSqlCli .= "$cAlfa.SIAI0150.REGESTXX ";
	  									$qSqlCli .= "FROM $cAlfa.SIAI0150 ";
	  									$qSqlCli .= "WHERE ";
											switch ($gFunction) {
												case 'cCliNom':
													$qSqlCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) LIKE \"%$gCliNom%\" ORDER BY CLINOMXX";
												break;
											}
	  									$xSqlCli  = f_MySql("SELECT","",$qSqlCli,$xConexion01,"");

	  									if ($xSqlCli && mysql_num_rows($xSqlCli) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "050" Class = "name"><center>NIT</center></td>
															<td widht = "400" Class = "name"><center>NOMBRE</center></td>
															<td widht = "050" Class = "name"><center>ESTADO</center></td>
														</tr>
														<?php while ($zRow = mysql_fetch_array($xSqlCli)) {
															if (mysql_num_rows($xSqlCli) > 1) { ?>
																<tr>
																	<?php switch ($gFunction) {
																		case "cCliNom": ?>
																			<td width = "050" class = "name">
																				<a href = "javascript:window.opener.document.forms['frgrm']['cCliId'].value  ='<?php echo $zRow['CLIIDXXX']?>';
																	    	                      window.opener.document.forms['frgrm']['cCliNom'].value ='<?php echo $zRow['CLINOMXX']?>';
																	      	                    window.opener.document.forms['frgrm']['cCliDV'].value  ='<?php echo f_Digito_Verificacion($zRow['CLIIDXXX'])?>';
																															if ('<?php echo $gOrigen ?>' == 'REPORTE') {
																																window.opener.fnHabilitaDeshabilitaFechas();
																															}
																															window.close();"
																															><?php echo $zRow['CLIIDXXX'] ?></a>
																			</td>
																		<?php break;
																	} ?>
																	<td width = "400" class= "name"><?php echo $zRow['CLINOMXX'] ?></td>
																	<td width = "050" class= "name"><?php echo $zRow['REGESTXX'] ?></td>
																</tr>
															<?php	} else {
																switch ($gFunction) {
																	case "cCliNom": ?>
																		<script languaje="javascript">
																			window.opener.document.forms['frgrm']['cCliId'].value  = '<?php echo $zRow['CLIIDXXX'] ?>';
																			window.opener.document.forms['frgrm']['cCliNom'].value = '<?php echo $zRow['CLINOMXX'] ?>';
																			window.opener.document.forms['frgrm']['cCliDV'].value  = '<?php echo f_Digito_Verificacion($zRow['CLIIDXXX'])?>';
																			if ('<?php echo $gOrigen ?>' == 'REPORTE') {
																				window.opener.fnHabilitaDeshabilitaFechas();
																			}
																			window.close();
																		</script>
																	<?php break;
																} ?>
															<?php }
														} ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); ?>
	  										<script>
		  										window.opener.document.forms['frgrm']['cCliId'].value  = '';
													window.opener.document.forms['frgrm']['cCliNom'].value = '';
													window.opener.document.forms['frgrm']['cCliDV'].value  = '';
													if ('<?php echo $gOrigen ?>' == 'REPORTE') {
														window.opener.fnHabilitaDeshabilitaFechas();
													}
		  										window.close();
		  									</script>
	  									<?php }
	  								break;
	  								case "VALID":
	  									$qSqlCli  = "SELECT ";
	  									$qSqlCli .= "$cAlfa.SIAI0150.CLIIDXXX, ";
	  									$qSqlCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X))) AS CLINOMXX, ";
	  									$qSqlCli .= "$cAlfa.SIAI0150.REGESTXX ";
											$qSqlCli .= "FROM $cAlfa.SIAI0150 ";
											$qSqlCli .= "WHERE ";
											switch ($gFunction) {
												case 'cCliNom':
													$qSqlCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) = \"$gCliNom\" ORDER BY CLINOMXX";
												break;
											}
	  									$xSqlCli  = f_MySql("SELECT","",$qSqlCli,$xConexion01,"");

	  									if ($xSqlCli && mysql_num_rows($xSqlCli) == 1) {
	  										while ($zRow = mysql_fetch_array($xSqlCli)) {
	  											switch ($gFunction) {
	  												case "cCliNom": ?>
	  													<script languaje = "javascript">
	      	    									parent.fmwork.document.forms['frgrm']['cCliId'].value  = '<?php echo $zRow['CLIIDXXX'] ?>';
																parent.fmwork.document.forms['frgrm']['cCliNom'].value = '<?php echo $zRow['CLINOMXX'] ?>';
																parent.fmwork.document.forms['frgrm']['cCliDV'].value  = '<?php echo f_Digito_Verificacion($zRow['CLIIDXXX'])?>';
																if ('<?php echo $gOrigen ?>' == 'REPORTE') {
																	parent.fmwork.fnHabilitaDeshabilitaFechas();
																}
															</script>
	  												<?php break;
	  											}
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
	?>
	<script>close();</script>
	<?php
} ?>
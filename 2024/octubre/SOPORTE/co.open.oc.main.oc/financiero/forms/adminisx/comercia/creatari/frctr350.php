<!--
	Los Parametros que Recibo son :
	$gSearch = Un dato para filtrar la consulta con el WHERE
	$gFields = A que campos del formulario voy a devolver los datos
	$gWhat   = Que voy a hacer, una ventana o una validacion
-->
<?php
  include("../../../../libs/php/utility.php");
?>
<?php if ($gWhat!= "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica de Clientes por Nit</title>
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
	  									$qSqlCli .= "$cAlfa.SIAI0150.CLIIDXXX,";
                      $qSqlCli .= "$cAlfa.SIAI0150.CLISAPXX,";
	  									$qSqlCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX, ";
	  									$qSqlCli .= "$cAlfa.SIAI0150.REGESTXX ";
	  									$qSqlCli .= "FROM $cAlfa.SIAI0150 ";
	  									$qSqlCli .= "WHERE ";
	  									$qSqlCli .= "$cAlfa.SIAI0150.CLIIDXXX LIKE \"%$gCliId%\" ORDER BY CLINOMXX";
	  									$xSqlCli  = f_MySql("SELECT","",$qSqlCli,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qSqlCli."~".mysql_num_rows($xSqlCli));

	  									if ($xSqlCli && mysql_num_rows($xSqlCli) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "550">
														<tr>
															<td widht = "050" Class = "name"><center>NIT</center></td>
															<td widht = "350" Class = "name"><center>NOMBRE</center></td>
                              <!-- Se inserta columna llamada Código SAP, solo aplica para ALMACAFE -->
															<?php
															switch($cAlfa) {
																case "TEALMACAFE":
																case "DEALMACAFE":
																case "ALMACAFE":
																?>
																	<td widht = "100" Class = "name"><center>C&Oacute;DIGO SAP</center></td>
																<?php
																break;
																default:
																	// no hace nada
																break;
															}
															?>
															<!-- FIN de inserción columna llamada Código SAP, solo aplica para ALMACAFE -->
															<td widht = "050" Class = "name"><center>ESTADO</center></td>
														</tr>
														<?php while ($zRow = mysql_fetch_array($xSqlCli)) {
															if (mysql_num_rows($xSqlCli) > 1) { ?>
																<tr>
																	<td width = "050" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cCliId'].value  ='<?php echo $zRow['CLIIDXXX']?>';
																                          window.opener.document.forms['frgrm']['cCliNom'].value ='<?php echo $zRow['CLINOMXX']?>';
																                          window.opener.document.forms['frgrm']['cCliDV'].value  ='<?php echo f_Digito_Verificacion($zRow['CLIIDXXX'])?>';
																                          if ('<?php echo $gOrigen ?>' == 'REPORTE') {
																														window.opener.fnHabilitaDeshabilitaFechas();
																													}
																													window.close()"
																													><?php echo $zRow['CLIIDXXX'] ?></a></td>
																	<td width = "350" class= "name"><?php echo $zRow['CLINOMXX'] ?></td>
                                  <!-- Se inserta columna llamada Código SAP, solo aplica para ALMACAFE -->
																	<?php
																	switch($cAlfa) {
																		case "TEALMACAFE":
																		case "DEALMACAFE":
																		case "ALMACAFE":
																		?>
																			<td width = "100" Class = "name"><?php echo $zRow['CLISAPXX'] ?></td>
																		<?php
																		break;
																		default:
																			// no hace nada
																		break;
																	}
																	?>
																	<!-- FIN de inserción columna llamada Código SAP, solo aplica para ALMACAFE -->
																	<td width = "050" class= "name"><?php echo $zRow['REGESTXX'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cCliId'].value  = '<?php echo $zRow['CLIIDXXX'] ?>';
																	window.opener.document.forms['frgrm']['cCliNom'].value = '<?php echo $zRow['CLINOMXX'] ?>';
																	window.opener.document.forms['frgrm']['cCliDV'].value  = '<?php echo f_Digito_Verificacion($zRow['CLIIDXXX'])?>';
																	if ('<?php echo $gOrigen ?>' == 'REPORTE') {
																		window.opener.fnHabilitaDeshabilitaFechas();
																	}
																	window.close();
																</script>
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
											$qSqlCli .= "$cAlfa.SIAI0150.CLIIDXXX,";
											$qSqlCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX, ";
											$qSqlCli .= "$cAlfa.SIAI0150.REGESTXX ";
											$qSqlCli .= "FROM $cAlfa.SIAI0150 ";
											$qSqlCli .= "WHERE ";
											$qSqlCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"$gCliId\" ORDER BY CLINOMXX";
	  									$xSqlCli  = f_MySql("SELECT","",$qSqlCli,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qSqlCli."~".mysql_num_rows($xSqlCli));

	  									if ($xSqlCli && mysql_num_rows($xSqlCli) > 0) {
	  										while ($zRow = mysql_fetch_array($xSqlCli)) { ?>
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
	?><script>close();</script>
	<?php
} ?>

<!--
	Los Parametros que Recibo son :
	$gSearch = Un dato para filtrar la consulta con el WHERE
	$gFields = A que campos del formulario voy a devolver los datos
	$gWhat   = Que voy a hacer, una ventana o una validacion
-->
<?php
  include("../../../../libs/php/utility.php");
?>

<?php if ($gWhat != "" && $gFunction != "") {?>
	<html>
		<head>
			<title>Parametrica de Formas de Cobro</title>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
			<!--<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>-->
	   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
	  </head>
	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>

	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
						<fieldset>
			   			<legend>Parametrica de Formas de Cobro</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":

			  							$zMtzTmp = explode("~",$gFcoIds);
			  							$zWhere = "(";
			  							for ($i=0;$i<count($zMtzTmp);$i++) {
			  								if ($zMtzTmp[$i] != "") {
		  										$zWhere .= "fcoidxxx = \"{$zMtzTmp[$i]}\" OR ";
		  										//$zWhere .= "fcoidxxx LIKE \"%{$zMtzTmp[$i]}%\" ORDER BY ABS({$zMtzTmp[$i]}) OR  ";
			  								}
			  							}
			  							$zWhere = substr($zWhere,0,(strlen($zWhere)-4));
			  							$zWhere .= ")";
			  							//f_Mensaje(__FILE__,__LINE__,$zWhere);

		  								$qSqlFco  = "SELECT * ";
								    	$qSqlFco .= "FROM $cAlfa.fpar0130 ";
								    	$qSqlFco .= "WHERE ";
											switch ($gFunction) {
												case 'cFcoId':
													$qSqlFco .= "$zWhere AND ";
												break;
												case 'cFcoDes':
													$qSqlFco .= "fcodesxx LIKE \"%$gFcoDes%\" AND ";
												break;
											}
								    	$qSqlFco .= "regestxx = \"ACTIVO\" ORDER BY fcoidxxx";
								    	//f_Mensaje(__FILE__,__LINE__,"Windows: ".$qSqlFco);
	  									$xSqlFco  = f_MySql("SELECT","",$qSqlFco,$xConexion01,"");


 											if ($xSqlFco && mysql_num_rows($xSqlFco) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "400">
														<tr>
															<td widht = "050" Class = "name"><center>Tarifa</center></td>
															<td widht = "300" Class = "name"><center>Descripcion</center></td>
															<td widht = "050" Class = "name"><center>Estado</center></td>
														</tr>
														<?php while ($zRow = mysql_fetch_array($xSqlFco)) {
															if (mysql_num_rows($xSqlFco) > 1) { ?>
																<tr>
																	<td width = "050" Class = "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cFcoId'].value ='<?php echo $zRow['fcoidxxx'] ?>';
																													window.opener.document.forms['frgrm']['cFcoDes'].value='<?php echo $zRow['fcodesxx'] ?>';
																													if('<?php echo $gTipo ?>' != 'REPORTE') {
																														window.opener.uAjaxHideObjetcs('<?php echo $zRow['fcoidxxx'] ?>');
																													}
																													window.close()">&nbsp;<?php echo $zRow['fcoidxxx'] ?></a></td>
																	<td width = "300" Class = "name"><?php echo $zRow['fcodesxx'] ?></td>
																	<td width = "050" Class = "name"><?php echo $zRow['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cFcoId'].value  = '<?php echo $zRow['fcoidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cFcoDes'].value = '<?php echo $zRow['fcodesxx'] ?>';
																	window.close();
																	if('<?php echo $gTipo ?>' != 'REPORTE') {
																		window.opener.uAjaxHideObjetcs('<?php echo $zRow['fcoidxxx'] ?>');
																	}
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else {
		 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
		 										?>
                        <script>
                          window.opener.document.forms['frgrm']['cFcoTpi'].value  = "";
                          window.opener.document.forms['frgrm']['cFcoTpd'].value  = "";
                          close();
                        </script>
		 										<?php
		 									}
		  							break;
	  								case "VALID":
	  									$zSw = 0; if (substr_count($gFcoIds,$gFcoId) > 0) { $zSw = 1; }
			  							//f_Mensaje(__FILE__,__LINE__,$zWhere);
			  							if ($zSw == 1) {
		  									$qSqlFco  = "SELECT * ";
												$qSqlFco .= "FROM $cAlfa.fpar0130 ";
												$qSqlFco .= "WHERE ";
												switch ($gFunction) {
													case 'cFcoId':
														$qSqlFco .= "fcoidxxx = \"$gFcoId\" AND ";
													break;
													case 'cFcoDes':
														$qSqlFco .= "fcodesxx = \"$gFcoDes\" AND ";
													break;
												}
												$qSqlFco .= "regestxx = \"ACTIVO\" ORDER BY fcoidxxx LIMIT 0,1";
		  									$xSqlFco  = f_MySql("SELECT","",$qSqlFco,$xConexion01,"");
		  									//f_Mensaje(__FILE__,__LINE__,"VALID: ".$qSqlFco." ~ ".mysql_num_rows($xSqlFco));

		  									if (mysql_num_rows($xSqlFco) == 1) {
		  										while ($zRow = mysql_fetch_array($xSqlFco)) { ?>
														<script languaje = "javascript">
															parent.fmwork.document.forms['frgrm']['cFcoId'].value  = '<?php echo $zRow['fcoidxxx'] ?>';
															parent.fmwork.document.forms['frgrm']['cFcoDes'].value = '<?php echo $zRow['fcodesxx'] ?>';
															if('<?php echo $gTipo ?>' != 'REPORTE') {
																parent.fmwork.uAjaxHideObjetcs('<?php echo $zRow['fcoidxxx'] ?>');
															}
															close();
														</script>
		  										<?php } ?>
		  									<?php } else { ?>
													<script languaje = "javascript">
		      	    						parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
														close();
													</script>
												<?php }
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
	?><script>close();</script>
	<?php
} ?>

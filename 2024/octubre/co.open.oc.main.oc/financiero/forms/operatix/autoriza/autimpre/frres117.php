<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
?>
<!-- 
	Los Parametros que Recibo son :
	$gSearch = Un dato para filtrar la consulta con el WHERE
	$gFields = A que campos del formulario voy a devolver los datos
	$gWhat   = Que voy a hacer, una ventana o una validacion
-->
<?php if ($gWhat != "" && $gFunction != "") { 
	//wMenssage(__FILE__,__LINE__,str_replace("~","%",$gQuery));	
	//wMenssage(__FILE__,__LINE__,"$gQuery - $gFields - $gWhat");
	//wMenssage(__FILE__,__LINE__,"Como Llega --> $gQuery");
?>
<html>
	<head>
		<title>Parametrica de Comprobantes</title>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
						<fieldset>
			   			<legend>Parametrica de Comprobantes</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<input type = "hidden" name = "cComId"  value = "">
								<input type = "hidden" name = "cComCod" value = "">
	  						<?php
	  							switch ($gWhat) { 
	  								/**case "VALID":
	  									$zSqlXXX  = "SELECT * ";
											$zSqlXXX .= "FROM $cAlfa.GRM00117 ";
											$zSqlXXX .= "COMIDXXX LIKE \"$gComId\" AND REGESTXX = \"ACTIVO\" ORDER BY COMIDXXX";
	  									$zCrsCom = mysql_query($zSqlXXX,$xConexion01);
	  									if ($zCrsCom && mysql_num_rows($zCrsCom) > 0) {
	  										while ($zRow = mysql_fetch_array($zCrsCom)) {
	  											$zComId  = $zRow['COMIDXXX'];
	  											$zComCod = $zRow['COMCODXX']; ?>
													<script languaje = "javascript">
														parent.fmwork.document.forms['frgrm']['cComId'].value  = '<?php echo $zRow['COMIDXXX'] ?>';
														parent.fmwork.document.forms['frgrm']['cComCod'].value = '<?php echo $zRow['COMCODXX'] ?>';
														document.forms['frgrm']['cComId'].value  = '<?php echo $zRow['COMIDXXX'] ?>';
														document.forms['frgrm']['cComCod'].value = '<?php echo $zRow['COMCODXX'] ?>';
													</script>
	  										<?php }
	  									} else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
												</script>
											<?php }
	      	      		break;*/	  
	  								case "WINDOW":
		  								$zSqlXXX  = "SELECT * ";
								    	$zSqlXXX .= "FROM $cAlfa.GRM00117 ";
								    	$zSqlXXX .= "WHERE COMIDXXX = \"F\" AND ";
								      $zSqlXXX .= "REGESTXX = \"ACTIVO\" ";
								    	$zSqlXXX .= "ORDER BY COMIDXXX ASC,COMCODXX ASC";
								    	$zCrsCom = mysql_query($zSqlXXX,$xConexion01);
								    	// wMenssage(__FILE__,__LINE__,mysql_num_rows($zCrsCom));	
 											if (mysql_num_rows($zCrsCom) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "300">
														<tr>
															<td widht = "030" Class = "name"><center>Com</center></td>
															<td widht = "030" Class = "name"><center>Cod</center></td>
															<td widht = "240" Class = "name"><center>Descripcion</center></td>
														</tr>
											<?php while ($zRow = mysql_fetch_array($zCrsCom)) {
															if (mysql_num_rows($zCrsCom) > 1) { ?>
																<tr>
																	<td width = "030" Class = "name"><?php echo $zRow['COMIDXXX'] ?></td>
																	<td width = "030" Class = "name"><?php echo $zRow['COMCODXX'] ?></td>
																	<td width = "240" Class = "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cComId'].value ='<?php echo $zRow['COMIDXXX'] ?>';
																													window.opener.document.forms['frgrm']['cComCod'].value='<?php echo $zRow['COMCODXX'] ?>';
																													window.close()"><?php echo $zRow['COMDESXX'] ?></a></td>
																</tr>
												<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cComId'].value  = '<?php echo $zRow['COMIDXXX'] ?>';
																	window.opener.document.forms['frgrm']['cComCod'].value = '<?php echo $zRow['COMCODXX'] ?>';
																	window.opener.document.forms['frgrm']['cComDes'].value = '<?php echo $zRow['COMDESXX'] ?>';
																	document.forms['frgrm']['cComId'].value  = '<?php echo $zRow['COMIDXXX'] ?>';
																	document.forms['frgrm']['cComCod'].value = '<?php echo $zRow['COMCODXX'] ?>';
																	window.close();
																</script>
													<?php }
														} ?>
													</table>
												</center>
		 						 <?php	} else {
		 										    wMenssage(__FILE__,__LINE__,"No se Encontraron Registros");	
		 									    }
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
	wMenssage(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
} ?>
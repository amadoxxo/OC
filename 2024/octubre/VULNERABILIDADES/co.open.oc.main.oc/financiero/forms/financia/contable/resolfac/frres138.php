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
	//f_Mensaje(__FILE__,__LINE__,str_replace("~","%",$gQuery));
	//f_Mensaje(__FILE__,__LINE__,"$gQuery - $gFields - $gWhat");
	//f_Mensaje(__FILE__,__LINE__,"Como Llega --> $gQuery");
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
   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		</script>
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
	  								case "WINDOW":
		  								$zSqlXXX  = "SELECT * ";
								    	$zSqlXXX .= "FROM $cAlfa.fpar0138 ";
								    	$zSqlXXX .= "WHERE ";
								    	$zSqlXXX .= "resclaxx = \"AUTORIZACION\" AND ";
								      $zSqlXXX .= "regestxx = \"ACTIVO\" ";
								    	$zSqlXXX .= "ORDER BY ABS(residxxx) ";
								    	$zCrsCom = f_MySql("SELECT","",$zSqlXXX,$xConexion01,"");

 											if (mysql_num_rows($zCrsCom) > 0) {
 											  ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "300">
														<tr>
															<td widht = "120" Class = "name"><center>Resolusion</center></td>
															<td widht = "120" Class = "name"><center>Clase</center></td>
															<td widht = "060" Class = "name"><center>Estado</center></td>
														</tr>
											<?php while ($zRow = mysql_fetch_array($zCrsCom)) {
      											    /*
											          $zSqlDes  = "SELECT * ";
      											    $zSqlDes .= "FROM $cAlfa.fpar0117 ";
      											    $zSqlDes .= "WHERE ";
      											    $zSqlDes .= "comidxxx = \"{$zRow['comidxxx']}\" AND ";
      											    $zSqlDes .= "comcodxx = \"{$zRow['comcodxx']}\" ";
      	  									    $zCrsDes = f_MySql("SELECT","",$zSqlDes,$xConexion01,"");

											          $zRowd=mysql_fetch_array($zCrsDes);

											          //f_Mensaje(__FILE__,__LINE__,mysql_num_rows($zCrsDes));*/
															if (mysql_num_rows($zCrsCom) > 1) { ?>
																<tr>
																	<td width = "120" Class = "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cResIdH'].value  = '<?php echo $zRow['residxxx'] ?>';
																													window.opener.document.forms['frgrm']['cResClaH'].value = '<?php echo $zRow['resclaxx'] ?>';
																	                        window.opener.document.forms['frgrm']['cResTipH'].value = '<?php echo $zRow['restipxx'] ?>';
																	                        window.opener.document.forms['frgrm']['dFecFinH'].value = '<?php echo $zRow['resfhaxx'] ?>';
																	                        window.opener.document.forms['frgrm']['cFacIniH'].value = '<?php echo $zRow['resdesxx'] ?>';
																	                        window.opener.document.forms['frgrm']['cFacFinH'].value = '<?php echo $zRow['reshasxx'] ?>';
																													window.close()"><?php echo $zRow['residxxx'] ?></a></td>
																	<td width = "120" Class = "name"><?php echo $zRow['resclaxx'] ?></td>
																	<td width = "030" Class = "name"><?php echo $zRow['regestxx'] ?></td>
																</tr>
												<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cResIdH'].value  = '<?php echo $zRow['residxxx'] ?>';
																	window.opener.document.forms['frgrm']['cResTipH'].value = '<?php echo $zRow['restipxx'] ?>';
																	window.opener.document.forms['frgrm']['cResClaH'].value = '<?php echo $zRow['resclaxx'] ?>';
																	window.opener.document.forms['frgrm']['dFecFinH'].value = '<?php echo $zRow['resfhaxx'] ?>';
																	window.opener.document.forms['frgrm']['cFacIniH'].value = '<?php echo $zRow['resdesxx'] ?>';
																	window.opener.document.forms['frgrm']['cFacFinH'].value = '<?php echo $zRow['reshasxx'] ?>';
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
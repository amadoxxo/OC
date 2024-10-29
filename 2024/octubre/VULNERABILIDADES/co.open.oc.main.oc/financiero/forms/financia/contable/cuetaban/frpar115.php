<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");

  if (!empty($gWhat) && !empty($gFunction)) {

?>
	<html>
		<head>
			<title>Param&eacute;trica de Cuentas</title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
	   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/programs/estilo.css">

	  </head>
	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>

	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
						<fieldset>
			   			<legend>Param&eacute;trica de Cuentas PUC</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":
	  								  //f_Mensaje(__FILE__,__LINE__,"Entre");

	  								  $qPucDes  = "SELECT * FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) LIKE \"%$cPucId%\" AND regestxx = \"ACTIVO\" ";
	  									$xPucDes  = f_MySql("SELECT","",$qPucDes,$xConexion01,"");

	  									/*
	  									//$zSqlCta  = "SELECT  CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx,pucdesxx,pucretxx,regestxx ";
	  									$zSqlCta  = "SELECT  * ";
	  									$zSqlCta .= "FROM $cAlfa.fpar0115 ";
	  									//$zSqlCta .= "WHERE pucidxxx LIKE \"%$cPucId%\" AND ";
	  									//$zSqlCta .= "regestxx = \"ACTIVO\"";
	  									$zCrsCta  = f_MySql("SELECT","",$zSqlCta,$xConexion01,"");
	  									*/


	  									if ($xPucDes && mysql_num_rows($xPucDes) > 0) {
	  									  //f_Mensaje(__FILE__,__LINE__,"Entre");
	  									  ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width ="500">
														<tr>
															<td widht ="050" bgcolor="#D6DFF7" Class = "name"><center>CUENTA</center></td>
															<td widht ="400" bgcolor="#D6DFF7" Class = "name"><center>NOMBRE</center></td>
															<td widht ="050" bgcolor="#D6DFF7" Class = "name"><center>ESTADO</center></td>

														</tr>
														<?php while ($zRow = mysql_fetch_array($xPucDes)) {
															if (mysql_num_rows($xPucDes) > 1) { ?>
																<tr>
																	<td style="width:050" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cPucId'].value ='<?php echo $zRow['pucgruxx'].$zRow['pucctaxx'].$zRow['pucsctax'].$zRow['pucauxxx'].$zRow['pucsauxx']?>';
																                          window.opener.document.forms['frgrm']['cPucDes'].value='<?php echo $zRow['pucdesxx']?>';
																                          window.close()"><?php echo $zRow['pucgruxx'].$zRow['pucctaxx'].$zRow['pucsctax'].$zRow['pucauxxx'].$zRow['pucsauxx'] ?></a></td>
																	<td width ="400" class= "name"><?php echo $zRow['pucdesxx'] ?></td>
																	<td width ="050" class= "name"><?php echo $zRow['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cPucId'].value  = '<?php echo $zRow['pucgruxx'].$zRow['pucctaxx'].$zRow['pucsctax'].$zRow['pucauxxx'].$zRow['pucsauxx'] ?>';
																	window.opener.document.forms['frgrm']['cPucDes'].value = '<?php echo $zRow['pucdesxx'] ?>';
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
	  								  //f_Mensaje(__FILE__,__LINE__,"Entre");

	  								  $qPucDes  = "SELECT * FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"$cPucId\" AND regestxx = \"ACTIVO\" ";
	  									$xPucDes  = f_MySql("SELECT","",$qPucDes,$xConexion01,"");

	  									if ($xPucDes && mysql_num_rows($xPucDes) > 0) {
	  										while ($zRow = mysql_fetch_array($xPucDes)) { ?>
													<script languaje = "javascript">
	      	    							parent.fmwork.document.forms['frgrm']['cPucId'].value  = '<?php echo $zRow['pucgruxx'].$zRow['pucctaxx'].$zRow['pucsctax'].$zRow['pucauxxx'].$zRow['pucsauxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cPucDes'].value = '<?php echo $zRow['pucdesxx'] ?>';
														window.close();
													</script>
	      	      				<?php break;
	  										}
	  									} else {
	  									  //f_Mensaje(__FILE__,__LINE__,"Entre");
	  									  ?>
												<script languaje = "javascript">
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
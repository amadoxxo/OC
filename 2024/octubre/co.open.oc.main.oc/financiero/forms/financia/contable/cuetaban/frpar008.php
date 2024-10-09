<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");

  if (!empty($gWhat) && !empty($gFunction)) {

?>
	<html>
		<head>
			<title>Param&eacute;trica de Sucursales</title>
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
			   			<legend>Param&eacute;trica de Sucursales</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":
	  								  //f_Mensaje(__FILE__,__LINE__,"Entre");

	  								  $qSucDes  = "SELECT * FROM $cAlfa.fpar0008 WHERE sucidxxx LIKE \"%$cBanSuc%\" AND regestxx = \"ACTIVO\" ";
	  									$xSucDes  = f_MySql("SELECT","",$qSucDes,$xConexion01,"");

	  									/*
	  									//$zSqlCta  = "SELECT  CONCAT(sucidxxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx,sucdesxx,pucretxx,regestxx ";
	  									$zSqlCta  = "SELECT  * ";
	  									$zSqlCta .= "FROM $cAlfa.fpar0008 ";
	  									//$zSqlCta .= "WHERE pucidxxx LIKE \"%$cBanSuc%\" AND ";
	  									//$zSqlCta .= "regestxx = \"ACTIVO\"";
	  									$zCrsCta  = f_MySql("SELECT","",$zSqlCta,$xConexion01,"");

	  									*/


	  									if ($xSucDes && mysql_num_rows($xSucDes) > 0) {
	  									  //f_Mensaje(__FILE__,__LINE__,"Entre");
	  									  ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width ="500">
														<tr>
															<td widht ="050" bgcolor="#D6DFF7" Class = "name"><center>SUCURSAL</center></td>
															<td widht ="400" bgcolor="#D6DFF7" Class = "name"><center>DESCRIPCION</center></td>
															<td widht ="050" bgcolor="#D6DFF7" Class = "name"><center>ESTADO</center></td>

														</tr>
														<?php while ($zRow = mysql_fetch_array($xSucDes)) {
															if (mysql_num_rows($xSucDes) > 1) { ?>
																<tr>
																	<td style="width:050" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cBanSuc'].value ='<?php echo $zRow['sucidxxx']?>';
																                          window.opener.document.forms['frgrm']['cSucDes'].value='<?php echo $zRow['sucdesxx']?>';
																                          window.close()"><?php echo $zRow['sucidxxx'] ?></a></td>
																	<td width ="400" class= "name"><?php echo $zRow['sucdesxx'] ?></td>
																	<td width ="050" class= "name"><?php echo $zRow['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cBanSuc'].value  = '<?php echo $zRow['sucidxxx'] ?>';
																	window.opener.document.forms['frgrm']['cSucDes'].value = '<?php echo $zRow['sucdesxx'] ?>';
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

	  								  $qSucDes  = "SELECT * FROM $cAlfa.fpar0008 WHERE sucidxxx = \"$cBanSuc\" AND regestxx = \"ACTIVO\" ";
	  									$xSucDes  = f_MySql("SELECT","",$qSucDes,$xConexion01,"");

	  									if ($xSucDes && mysql_num_rows($xSucDes) > 0) {
	  										while ($zRow = mysql_fetch_array($xSucDes)) { ?>
													<script languaje = "javascript">
	      	    							parent.fmwork.document.forms['frgrm']['cBanSuc'].value  = '<?php echo $zRow['sucidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cSucDes'].value = '<?php echo $zRow['sucdesxx'] ?>';
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
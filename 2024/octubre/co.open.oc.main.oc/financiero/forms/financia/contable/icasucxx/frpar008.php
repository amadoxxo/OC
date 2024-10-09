<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica de Sucursales </title>
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
			   			<legend>Param&eacute;trica de Sucursales</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":

	  									$qDesSuc  = "SELECT * ";
                			$qDesSuc .= "FROM $cAlfa.fpar0008 ";
                			$qDesSuc .= "WHERE ";
                			switch ($gFunction) {
	  								   case "cSucDes":
	  								     $qDesSuc .= "sucdesxx LIKE \"%$cSucId%\" AND ";
	  								   break;
	  								   default:
	  								     $qDesSuc .= "sucidxxx LIKE \"%$cSucId%\" AND ";
	  								   break;
                			}
                			$qDesSuc .= "regestxx = \"ACTIVO\" ";
                			$qDesSuc .= "ORDER BY sucdesxx ";
                			$xDesSuc  = f_MySql("SELECT","",$qDesSuc,$xConexion01,"");
                			//f_Mensaje(__FILE__,__LINE__,$qDesSuc."~".mysql_num_rows($xDesSuc));


	  									if ($xDesSuc && mysql_num_rows($xDesSuc) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "050" Class = "name"><center>ID</center></td>
															<td widht = "350" Class = "name"><center>SUCURSAL</center></td>
															<td widht = "100" Class = "name"><center>CENTRO DE COSTO</center></td>
														</tr>
														<?php while ($xDS = mysql_fetch_array($xDesSuc)) {
															if (mysql_num_rows($xDesSuc) > 1) { ?>
																<tr>
																	<td width = "050" class= "name" style="padding-left:5px">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cSucId'].value  ='<?php echo $xDS['sucidxxx']?>';
																													window.opener.document.forms['frgrm']['cSucDes'].value ='<?php echo $xDS['sucdesxx']?>';
																													window.opener.document.forms['frgrm']['cCcoId'].value ='<?php echo $xDS['ccoidxxx']?>';
																													window.close()"><?php echo $xDS['sucidxxx'] ?></a></td>
																	<td width = "350" class= "name"> <?php echo $xDS['sucdesxx'] ?></td>
																	<td width = "100" class= "name" align="center"> <?php echo $xDS['ccoidxxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cSucId'].value  ='<?php echo $xDS['sucidxxx']?>';
																	window.opener.document.forms['frgrm']['cSucDes'].value ='<?php echo $xDS['sucdesxx']?>';
																	window.opener.document.forms['frgrm']['cCcoId'].value ='<?php echo $xDS['ccoidxxx']?>';
																	window.close()
																</script>
															<?php }
														} ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); ?>
												<script languaje="javascript">
													window.opener.document.forms['frgrm']['cSucId'].value  ='';
													window.opener.document.forms['frgrm']['cSucDes'].value ='';
													window.opener.document.forms['frgrm']['cCcoId'].value ='';
													window.close()
												</script>
											<?php
	  									}
	  								break;

	  								case "VALID":

											$qDesSuc  = "SELECT * ";
                			$qDesSuc .= "FROM $cAlfa.fpar0008 ";
                			$qDesSuc .= "WHERE ";
                			switch ($gFunction) {
	  								   case "cSucDes":
	  								     $qDesSuc .= "sucdesxx LIKE \"%$cSucId%\" AND ";
	  								   break;
	  								   default:
	  								     $qDesSuc .= "sucidxxx = \"$cSucId\" AND ";
	  								   break;
                			}
                			$qDesSuc .= "regestxx = \"ACTIVO\" ";
                			$qDesSuc .= "ORDER BY sucdesxx ";
                			$xDesSuc  = f_MySql("SELECT","",$qDesSuc,$xConexion01,"");
                			//f_Mensaje(__FILE__,__LINE__,$qDesSuc."~".mysql_num_rows($xDesSuc));

	  									if (mysql_num_rows($xDesSuc) == 1) {
	  										while ($xDS = mysql_fetch_array($xDesSuc)) {
	  										?>
													<script languaje = "javascript">
										        parent.fmwork.document.forms['frgrm']['cSucId'].value  ='<?php echo $xDS['sucidxxx']?>';
														parent.fmwork.document.forms['frgrm']['cSucDes'].value ='<?php echo $xDS['sucdesxx']?>';
														parent.fmwork.document.forms['frgrm']['cCcoId'].value ='<?php echo $xDS['ccoidxxx']?>';
													</script>
	      	      				<?php break;
	  										}
	  									} else {
	  									?>
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
<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");

  //f_Mensaje(__FILE__,__LINE__,$gFunction);
  if ($gWhat != "" && $gFunction != "") { ?>
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
			   			<legend>Param&eacute;trica de Ciudades</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":

	  									$qDesSuc  = "SELECT $cAlfa.SIAI0055.*, ";
                			$qDesSuc .= "IF($cAlfa.SIAI0054.DEPDESXX <> \"\",$cAlfa.SIAI0054.DEPDESXX,\"DEPARTAMENTO SIN DESCRIPCION\") AS DEPDESXX, ";
                			$qDesSuc .= "IF($cAlfa.SIAI0052.PAIDESXX <> \"\",$cAlfa.SIAI0052.PAIDESXX,\"PAIS SIN DESCRIPCION\") AS PAIDESXX ";
                			$qDesSuc .= "FROM $cAlfa.SIAI0055 ";
                			$qDesSuc .= "LEFT JOIN $cAlfa.SIAI0054 ON $cAlfa.SIAI0055.PAIIDXXX =  $cAlfa.SIAI0054.PAIIDXXX AND $cAlfa.SIAI0055.DEPIDXXX =  $cAlfa.SIAI0054.DEPIDXXX ";
                			$qDesSuc .= "LEFT JOIN $cAlfa.SIAI0052 ON $cAlfa.SIAI0055.PAIIDXXX =  $cAlfa.SIAI0052.PAIIDXXX ";
                			$qDesSuc .= "WHERE ";
                			switch ($gFunction) {
	  								   case "cCiuDes":
	  								     $qDesSuc .= "$cAlfa.SIAI0055.CIUDESXX LIKE \"%$cCiuId%\" AND ";
	  								   break;
	  								   default:
	  								     $qDesSuc .= "$cAlfa.SIAI0055.CIUIDXXX LIKE \"%$cCiuId%\" AND ";
	  								   break;
                			}
                			$qDesSuc .= "$cAlfa.SIAI0055.PAIIDXXX  =  \"CO\" AND ";
                			// $qDesSuc .= "$cAlfa.SIAI0055.SUCIDXXX  = \"\" AND ";
                			$qDesSuc .= "$cAlfa.SIAI0055.REGESTXX  =  \"ACTIVO\" ";
                			$qDesSuc .= "ORDER BY DEPDESXX, $cAlfa.SIAI0055.CIUDESXX ";
                			$xDesSuc  = f_MySql("SELECT","",$qDesSuc,$xConexion01,"");
                			//f_Mensaje(__FILE__,__LINE__,$qDesSuc."~".mysql_num_rows($xDesSuc));


	  									if ($xDesSuc && mysql_num_rows($xDesSuc) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "050" Class = "name"><center>CODIGO</center></td>
															<td widht = "250" Class = "name"><center>CIUDAD</center></td>
															<td widht = "200" Class = "name"><center>DEPARTAMENTO</center></td>
														</tr>
														<?php while ($xDS = mysql_fetch_array($xDesSuc)) {
															if (mysql_num_rows($xDesSuc) > 1) { ?>
																<tr>
																	<td width = "050" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cPaiId'].value  ='<?php echo $xDS['PAIIDXXX']?>';
																													window.opener.document.forms['frgrm']['cPaiDes'].value ='<?php echo $xDS['PAIDESXX']?>';
																													window.opener.document.forms['frgrm']['cDepId'].value  ='<?php echo $xDS['DEPIDXXX']?>';
																													window.opener.document.forms['frgrm']['cDepDes'].value ='<?php echo $xDS['DEPDESXX']?>';
																													window.opener.document.forms['frgrm']['cCiuId'].value  ='<?php echo $xDS['CIUIDXXX']?>';
																													window.opener.document.forms['frgrm']['cCiuDes'].value ='<?php echo $xDS['CIUDESXX']?>';
																													window.close()"><?php echo $xDS['CIUIDXXX'] ?></a></td>
																	<td width = "250" class= "name"> <?php echo $xDS['CIUDESXX'] ?></td>
																	<td width = "200" class= "name"> <?php echo $xDS['DEPDESXX'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['cPaiId'].value  ='<?php echo $xDS['PAIIDXXX']?>';
																	window.opener.document.forms['frgrm']['cPaiDes'].value ='<?php echo $xDS['PAIDESXX']?>';
																	window.opener.document.forms['frgrm']['cDepId'].value  ='<?php echo $xDS['DEPIDXXX']?>';
																	window.opener.document.forms['frgrm']['cDepDes'].value ='<?php echo $xDS['DEPDESXX']?>';
																	window.opener.document.forms['frgrm']['cCiuId'].value  ='<?php echo $xDS['CIUIDXXX']?>';
																	window.opener.document.forms['frgrm']['cCiuDes'].value ='<?php echo $xDS['CIUDESXX']?>';
																	window.close()
																</script>
															<?php }
														} ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");?>
												<script languaje="javascript">
													window.opener.document.forms['frgrm']['cPaiId'].value  ='';
													window.opener.document.forms['frgrm']['cPaiDes'].value ='';
													window.opener.document.forms['frgrm']['cDepId'].value  ='';
													window.opener.document.forms['frgrm']['cDepDes'].value ='';
													window.opener.document.forms['frgrm']['cCiuId'].value  ='';
													window.opener.document.forms['frgrm']['cCiuDes'].value ='';
													window.close()
												</script>
											<?php
	  									}
	  								break;

	  								case "VALID":

										  $qDesSuc  = "SELECT $cAlfa.SIAI0055.*, ";
                			$qDesSuc .= "IF($cAlfa.SIAI0054.DEPDESXX <> \"\",$cAlfa.SIAI0054.DEPDESXX,\"DEPARTAMENTO SIN DESCRIPCION\") AS DEPDESXX, ";
                			$qDesSuc .= "IF($cAlfa.SIAI0052.PAIDESXX <> \"\",$cAlfa.SIAI0052.PAIDESXX,\"PAIS SIN DESCRIPCION\") AS PAIDESXX ";
                			$qDesSuc .= "FROM $cAlfa.SIAI0055 ";
                			$qDesSuc .= "LEFT JOIN $cAlfa.SIAI0054 ON $cAlfa.SIAI0055.PAIIDXXX =  $cAlfa.SIAI0054.PAIIDXXX AND $cAlfa.SIAI0055.DEPIDXXX =  $cAlfa.SIAI0054.DEPIDXXX ";
                			$qDesSuc .= "LEFT JOIN $cAlfa.SIAI0052 ON $cAlfa.SIAI0055.PAIIDXXX =  $cAlfa.SIAI0052.PAIIDXXX ";
                			$qDesSuc .= "WHERE ";
                			switch ($gFunction) {
	  								   case "cCiuDes":
	  								     $qDesSuc .= "$cAlfa.SIAI0055.CIUDESXX = \"$cCiuId\" AND ";
	  								   break;
	  								   default:
	  								     $qDesSuc .= "$cAlfa.SIAI0055.CIUIDXXX  LIKE \"%$cCiuId%\" AND ";
	  								   break;
                			}
                			$qDesSuc .= "$cAlfa.SIAI0055.PAIIDXXX  =  \"CO\" AND ";
                			// $qDesSuc .= "$cAlfa.SIAI0055.SUCIDXXX  = \"\" AND ";
                			$qDesSuc .= "$cAlfa.SIAI0055.REGESTXX  =  \"ACTIVO\"";
                			$xDesSuc  = f_MySql("SELECT","",$qDesSuc,$xConexion01,"");
                			//f_Mensaje(__FILE__,__LINE__,$qDesSuc."~".mysql_num_rows($xDesSuc));
                			
	  									if (mysql_num_rows($xDesSuc) == 1) {
	  										while ($xDS = mysql_fetch_array($xDesSuc)) {
	  										?>
													<script languaje = "javascript">
													  parent.fmwork.document.forms['frgrm']['cPaiId'].value  ='<?php echo $xDS['PAIIDXXX']?>';
  													parent.fmwork.document.forms['frgrm']['cPaiDes'].value ='<?php echo $xDS['PAIDESXX']?>';
  													parent.fmwork.document.forms['frgrm']['cDepId'].value  ='<?php echo $xDS['DEPIDXXX']?>';
  													parent.fmwork.document.forms['frgrm']['cDepDes'].value ='<?php echo $xDS['DEPDESXX']?>';
  													parent.fmwork.document.forms['frgrm']['cCiuId'].value  ='<?php echo $xDS['CIUIDXXX']?>';
  													parent.fmwork.document.forms['frgrm']['cCiuDes'].value ='<?php echo $xDS['CIUDESXX']?>';
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
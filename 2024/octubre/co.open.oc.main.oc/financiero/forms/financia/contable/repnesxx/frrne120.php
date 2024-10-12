<?php 
  namespace openComex;
	include("../../../../libs/php/utility.php");
	if ($gModo != "" && $gFunction != ""){ 
		if($gModo == 'ALL'){
			$gModo = 'WINDOW';
		}
	?>
	<html>
		<head>
			<title>Modo de Transporte</title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
			<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
	  </head>
	  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
	  <center>
			<table border = "0" cellpadding = "0" cellspacing = "0" width = "550">
				<tr>
					<td>
						<fieldset>
			   			<legend>Modo de Transporte</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gModo) {
	  								case "VALID":
	  									$qModTra  = "SELECT * ";
											$qModTra .= "FROM $cAlfa.SIAI0120 ";
	  									switch ($gFunction) {
	  										case "cMtrId":
	  											$qModTra .= "WHERE ";
													$qModTra .= "MTRIDXXX = \"$gMtrId\" ";
	  										break;
	  										case "cMtrDes":
	  											$qModTra .= "WHERE ";
													$qModTra .= "MTRDESXX = \"$gMtrDes\" ";
													$qModTra .= "ORDER BY MTRDESXX";
	  										break;
	  									}
			  							$xModTra  = f_MySql("SELECT","",$qModTra,$xConexion01,"");

	  									if (mysql_num_rows($xModTra) == 1) {
	  										$vModTra = mysql_fetch_array($xModTra); ?>
												<script languaje = "javascript">
                        	parent.fmwork.document.forms['frgrm']['cMtrId'].value  = "<?php echo $vModTra['MTRIDXXX'] ?>";
                          parent.fmwork.document.forms['frgrm']['cMtrDes'].value = "<?php echo $vModTra['MTRDESXX'] ?>";
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
		  								$qModTra  = "SELECT * ";
											$qModTra .= "FROM $cAlfa.SIAI0120 ";
											switch ($gFunction) {
	  										case "cMtrId":
	  											$qModTra .= "WHERE ";
													$qModTra .= "MTRIDXXX LIKE \"%$gMtrId%\" ";
													$qModTra .= "ORDER BY MTRIDXXX";
	  										break;
	  										case "cMtrDes":
	  											$qModTra .= "WHERE ";
													$qModTra .= "MTRDESXX LIKE \"%$gMtrDes%\" ";
													$qModTra .= "ORDER BY MTRDESXX";
	  										break;
	  									}
	  									$xModTra  = f_MySql("SELECT","",$qModTra,$xConexion01,"");

 											if (mysql_num_rows($xModTra) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "550">
														<tr>
															<td width = "50" Class = "name"><center>Codigo</center></td>
															<td width = "400" Class = "name"><center>Modo de Transporte</center></td>
															<td width = "100" Class = "name" align="center"><center>Estado</center></td>
														</tr>
														<?php while ($xRMD = mysql_fetch_array($xModTra)) {
															if (mysql_num_rows($xModTra) > 0) { ?>
																<tr>
																	<td Class = "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cMtrId'].value  = '<?php echo $xRMD['MTRIDXXX'] ?>';
																										 			window.opener.document.forms['frgrm']['cMtrDes'].value = '<?php echo $xRMD['MTRDESXX'] ?>';
																												 	window.close()"><?php echo $xRMD['MTRIDXXX'] ?>
																		</a>
																	</td>
																	<td Class = "name"><?php echo $xRMD['MTRDESXX'] ?></td>
																	<td Class = "name" align="center"><?php echo $xRMD['REGESTXX'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
  																switch ("<?php echo $gFunction ?>") {
        												    case "cMtrId":
        												    case "cMtrDes":
    																	window.opener.document.forms['frgrm']['cMtrId'].value  = "<?php echo $xRMD['MTRIDXXX'] ?>";
    																	window.opener.document.forms['frgrm']['cMtrDes'].value = "<?php echo $xRMD['MTRDESXX'] ?>";
    																break;
  																}
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else {
		 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique."); ?>
												<script languaje="javascript">
													window.opener.document.forms['frgrm']['cMtrId'].value  = "";
													window.opener.document.forms['frgrm']['cMtrDes'].value = "";
													window.close();
												</script>
											<?php
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
	f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos, Verifique.");
} ?>
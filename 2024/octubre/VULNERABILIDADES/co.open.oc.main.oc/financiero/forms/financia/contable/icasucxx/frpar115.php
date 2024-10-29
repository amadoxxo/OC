<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica Descripci&oacute;n Cuenta PUC </title>
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
			   			<legend>Param&eacute;trica Descripcion Cuenta PUC</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":

	  									$qDesPuc  = "SELECT * ";
                			$qDesPuc .= "FROM $cAlfa.fpar0115 ";
                			$qDesPuc .= "WHERE ";
                			$qDesPuc .= "pucterxx = \"R\" AND "; //cuenta de retencion
                			$qDesPuc .= "(LENGTH(CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx)) = 10) AND ";
                			$qDesPuc .= "(CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) LIKE \"%$cPucId%\") ";
                			$qDesPuc .= "ORDER BY CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx)";
                			$xDesPuc  = f_MySql("SELECT","",$qDesPuc,$xConexion01,"");
                			//f_Mensaje(__FILE__,__LINE__,$qDesPuc);


	  									if ($xDesPuc && mysql_num_rows($xDesPuc) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "050" Class = "name"><center>ID</center></td>
															<td widht = "350" Class = "name"><center>CUENTA</center></td>
															<td widht = "050" Class = "name"><center>RETENCI&Oacute;N</center></td>
															<td widht = "050" Class = "name"><center>ESTADO</center></td>
														</tr>
														<?php while ($xDP = mysql_fetch_array($xDesPuc)) {
															if (mysql_num_rows($xDesPuc) > 1) {
															  switch ($gFunction) {
																	case "cPucId": ?>
                                    <tr>
                                      <td width = "050" class= "name">
                                        <a href = "javascript:window.opener.document.forms['frgrm']['cPucId'].value  ='<?php echo $xDP['pucgruxx'].$xDP['pucctaxx'].$xDP['pucsctax'].$xDP['pucauxxx'].$xDP['pucsauxx']?>';
                                                              window.opener.document.forms['frgrm']['cPucDes'].value ='<?php echo $xDP['pucdesxx'] ?>';
                                                              window.opener.document.forms['frgrm']['cCiuIca'].value ='<?php echo ($xDP['pucretxx']+0) ?>';
                                                              window.close()"><?php echo $xDP['pucgruxx'].$xDP['pucctaxx'].$xDP['pucsctax'].$xDP['pucauxxx'].$xDP['pucsauxx'] ?></a></td>
                                      <td width = "350" class= "name"> <?php echo $xDP['pucdesxx'] ?></td>
                                      <td width = "050" class= "name" align="right"> <?php echo ($xDP['pucretxx']+0) ?></td>
                                      <td width = "050" class= "name"> <?php echo $xDP['regestxx'] ?></td>
                                    </tr>
                                  <?php break;																	
																	default: ?>
                                    <tr>
                                      <td width = "050" class= "name">
                                        <a href = "javascript:window.opener.document.forms['frgrm']['cPucId2'].value  ='<?php echo $xDP['pucgruxx'].$xDP['pucctaxx'].$xDP['pucsctax'].$xDP['pucauxxx'].$xDP['pucsauxx']?>';
                                                              window.opener.document.forms['frgrm']['cPucDes2'].value ='<?php echo $xDP['pucdesxx'] ?>';
                                                              window.opener.document.forms['frgrm']['cCiuIca2'].value ='<?php echo ($xDP['pucretxx']+0) ?>';
                                                              window.close()"><?php echo $xDP['pucgruxx'].$xDP['pucctaxx'].$xDP['pucsctax'].$xDP['pucauxxx'].$xDP['pucsauxx'] ?></a></td>
                                      <td width = "350" class= "name"> <?php echo $xDP['pucdesxx'] ?></td>
                                      <td width = "050" class= "name" align="right"> <?php echo ($xDP['pucretxx']+0) ?></td>
                                      <td width = "050" class= "name"> <?php echo $xDP['regestxx'] ?></td>
                                    </tr>
                                  <?php break;
																}
															} else { 
															  switch ($gFunction) {
                                  case "cPucId": ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm']['cPucId'].value  = '<?php echo $xDP['pucgruxx'].$xDP['pucctaxx'].$xDP['pucsctax'].$xDP['pucauxxx'].$xDP['pucsauxx'] ?>';
                                      window.opener.document.forms['frgrm']['cPucDes'].value = '<?php echo $xDP['pucdesxx'] ?>';
                                      window.opener.document.forms['frgrm']['cCiuIca'].value = '<?php echo ($xDP['pucretxx']+0) ?>';
                                      window.close();
                                    </script>
                                  <?php break;                                  
                                  default: ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm']['cPucId2'].value  = '<?php echo $xDP['pucgruxx'].$xDP['pucctaxx'].$xDP['pucsctax'].$xDP['pucauxxx'].$xDP['pucsauxx'] ?>';
                                      window.opener.document.forms['frgrm']['cPucDes2'].value = '<?php echo $xDP['pucdesxx'] ?>';
                                      window.opener.document.forms['frgrm']['cCiuIca2'].value = '<?php echo ($xDP['pucretxx']+0) ?>';
                                      window.close();
                                    </script>
                                  <?php break;
                                }
															}
														}
	  									?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); 
	  										switch ($gFunction) {
                          case "cPucId": ?>
                            <script languaje="javascript">
                              window.opener.document.forms['frgrm']['cPucId'].value  = '';
                              window.opener.document.forms['frgrm']['cPucDes'].value = '';
                              window.opener.document.forms['frgrm']['cCiuIca'].value = '';
                              window.close();
                            </script>
                          <?php break;                                  
                          default: ?>
                            <script languaje="javascript">
                              window.opener.document.forms['frgrm']['cPucId2'].value  = '';
                              window.opener.document.forms['frgrm']['cPucDes2'].value = '';
                              window.opener.document.forms['frgrm']['cCiuIca2'].value = '';
                              window.close();
                            </script>
                          <?php break;
                        }
	  									}
	  								break;
	  								case "VALID":

											$qDesPuc = "SELECT * ";
                			$qDesPuc .= "FROM $cAlfa.fpar0115 ";
                			$qDesPuc .= "WHERE ";
                      $qDesPuc .= "pucterxx = \"R\" AND "; //cuenta de retencion
                			$qDesPuc .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx)= \"$cPucId\" ";
                			$xDesPuc  = f_MySql("SELECT","",$qDesPuc,$xConexion01,"");

	  									if (mysql_num_rows($xDesPuc) == 1){
	  										while ($xDP = mysql_fetch_array($xDesPuc)) {
	  										  switch ($gFunction) {
                            case "cPucId": ?>
                              <script languaje="javascript">
                                parent.fmwork.document.forms['frgrm']['cPucId'].value  = '<?php echo $xDP['pucgruxx'].$xDP['pucctaxx'].$xDP['pucsctax'].$xDP['pucauxxx'].$xDP['pucsauxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cPucDes'].value = '<?php echo $xDP['pucdesxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cCiuIca'].value = '<?php echo ($xDP['pucretxx']+0) ?>';
                              </script>
                            <?php break;                                  
                            default: ?>
                              <script languaje="javascript">
                                parent.fmwork.document.forms['frgrm']['cPucId2'].value  = '<?php echo $xDP['pucgruxx'].$xDP['pucctaxx'].$xDP['pucsctax'].$xDP['pucauxxx'].$xDP['pucsauxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cPucDes2'].value = '<?php echo $xDP['pucdesxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cCiuIca2'].value = '<?php echo ($xDP['pucretxx']+0) ?>';
                              </script>
                            <?php break;
                          }
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
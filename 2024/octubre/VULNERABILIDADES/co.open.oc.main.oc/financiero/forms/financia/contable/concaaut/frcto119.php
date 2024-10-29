<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");

  if (!empty($gWhat) && !empty($gFunction)) {
  
  $mComMemo = array();
  $mComMemo = explode("|",$cComMemo);
  $cSqlCom = "";
  for ($i=0; $i<count($mComMemo);$i++) {
    if ($mComMemo[$i] <> "") {
      $mAux = array();
      $mAux = explode("~",$mComMemo[$i]);
      $cSqlCom .= "ctocomxx LIKE \"%|{$mAux[0]}~{$mAux[1]}~%\" OR ";
    }
  }
  $cSqlCom = substr($cSqlCom,0,strlen($cSqlCom)-4);
?>
	<html>
		<head>
			<title>Parametrica de Conceptos Contables</title>
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
			   			<legend>Parametrica de Conceptos Contables</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							#Campos 
	  							switch ($gFunction) {
  									case "cCtoCtoRf":
  										$cId = "cCtoCtoRf";  $cDes = "cCtoDesRf";  $cRet = "cCtoRetRf";
  									break;
  									case "cCtoCtrFs":
  										$cId = "cCtoCtrFs";  $cDes = "cCtoDesFs";  $cRet = "cCtoRetFs";
  										break;
  									case "cCtoCtoRv":
  										$cId = "cCtoCtoRv";  $cDes = "cCtoDesRv";  $cRet = "cCtoRetRv";
  									break;
  									case "cCtoCtoRc":
                      $cId = "cCtoCtoRc";  $cDes = "cCtoDesRc";  $cRet = "cCtoRetRc";
                    break;
  									default:
  										$cId = "cCtoCtoCp";  $cDes = "cCtoDesCp";  $cRet = "";
  									break;
								  }
	  							switch ($gWhat) {
	  								case "WINDOW":
	  								  //f_Mensaje(__FILE__,__LINE__,"Entre");
	  								  $qCtoId  = "SELECT $cAlfa.fpar0119.pucidxxx,$cAlfa.fpar0119.ctoidxxx,$cAlfa.fpar0119.regestxx, ";
										  $qCtoId .= "IF($cAlfa.fpar0119.ctodesxp <> \"\",$cAlfa.fpar0119.ctodesxp,IF($cAlfa.fpar0119.ctodesxx <> \"\",$cAlfa.fpar0119.ctodesxx,\"CONCEPTO SIN DESCRIPCION\")) AS ctodesxp, ";
										  $qCtoId .= "$cAlfa.fpar0115.pucretxx ";
										  $qCtoId .= "FROM $cAlfa.fpar0119 ";
										  $qCtoId .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
										  $qCtoId .= "WHERE ";
										  $qCtoId .= "$cAlfa.fpar0119.ctoidxxx LIKE \"%$gCtoCto%\" AND ";
										  switch ($gFunction) {
	  										case "cCtoCtoRf":
	  										case "cCtoCtrFs":
	  										case "cCtoCtoRv":
	  										case "cCtoCtoRc":
	  											$qCtoId .= "$cAlfa.fpar0115.pucterxx = \"R\" AND ";
	  										break;
	  										default:
	  											$qCtoId .= "$cAlfa.fpar0115.pucdetxx = \"P\" AND ";
	  										break;
										  }
										  $qCtoId .= "($cSqlCom) AND ";
										  $qCtoId .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
										  $qCtoId .= "ORDER BY ABS($cAlfa.fpar0119.ctoidxxx) ";
										  $xCtoId  = f_MySql("SELECT","",$qCtoId,$xConexion01,"");
										  //f_Mensaje(__FILE__,__LINE__,$qCtoId."~".mysql_num_rows($xCtoId));

											if ($xCtoId && mysql_num_rows($xCtoId) > 0) {
	  									  //f_Mensaje(__FILE__,__LINE__,"Entre");
	  									  ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width ="500">
														<tr>
															<td widht ="080" bgcolor="#D6DFF7" Class = "name"><center>CONCEPTO</center></td>
															<td bgcolor="#D6DFF7" Class = "name"><center>NOMBRE</center></td>
															<?php 
															switch ($gFunction) {
					  										case "cCtoCtoRf":
					  										case "cCtoCtrFs":
					  										case "cCtoCtoRv":
					  										case "cCtoCtoRc": ?>
					  											<td widht ="050" bgcolor="#D6DFF7" Class = "name"><center>RETENCION</center></td>
					  										<?php break;
														  }?>
															<td widht ="050" bgcolor="#D6DFF7" Class = "name"><center>CUENTA</center></td>

														</tr>
														<?php while ($xRCI = mysql_fetch_array($xCtoId)) {
															if (mysql_num_rows($xCtoId) > 1) {
																?>
																<tr>
																	<td class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['<?php echo $cId ?>'].value ='<?php echo $xRCI['ctoidxxx']?>';
																                          window.opener.document.forms['frgrm']['<?php echo $cDes ?>'].value='<?php echo $xRCI['ctodesxp']?>';
																                          if ('<?php echo $cRet ?>' != '') {
																                          	window.opener.document.forms['frgrm']['<?php echo $cRet ?>'].value='<?php echo $xRCI['pucretxx']?>';
																                          }
																                          window.close()"><?php echo $xRCI['ctoidxxx'] ?></a></td>
																	<td class= "name"><?php echo $xRCI['ctodesxp'] ?></td>
																	<?php 
																	switch ($gFunction) {
							  										case "cCtoCtoRf":
							  										case "cCtoCtrFs":
							  										case "cCtoCtoRv": 
							  										case "cCtoCtoRc":?>
							  											<td class= "name"><?php echo $xRCI['pucretxx'] ?></td>
							  										<?php break;
																  }?>
																	<td class= "name"><?php echo $xRCI['pucidxxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	window.opener.document.forms['frgrm']['<?php echo $cId ?>'].value ='<?php echo $xRCI['ctoidxxx']?>';
				                          window.opener.document.forms['frgrm']['<?php echo $cDes ?>'].value='<?php echo $xRCI['ctodesxp']?>';
				                          if ('<?php echo $cRet ?>' != '') {
				                          	window.opener.document.forms['frgrm']['<?php echo $cRet ?>'].value='<?php echo $xRCI['pucretxx']?>';
				                          }
				                          window.close()
																</script>
															<?php }
														} ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
	  										?>
												<script languaje="javascript">
													window.opener.document.forms['frgrm']['<?php echo $cId ?>'].value ='';
													window.opener.document.forms['frgrm']['<?php echo $cDes ?>'].value='';
	                      	if ('<?php echo $cRet ?>' != '') {
	                      		window.opener.document.forms['frgrm']['<?php echo $cRet ?>'].value='';
	                      	}
													window.close();
												</script>
												<?php
	  									}
	  								break;
	  								case "VALID":
	  								  //f_Mensaje(__FILE__,__LINE__,"Entre");

	  								  $qCtoId  = "SELECT $cAlfa.fpar0119.ctoidxxx,$cAlfa.fpar0119.regestxx, ";
										  $qCtoId .= "IF($cAlfa.fpar0119.ctodesxp <> \"\",$cAlfa.fpar0119.ctodesxp,IF($cAlfa.fpar0119.ctodesxx <> \"\",$cAlfa.fpar0119.ctodesxx,\"CONCEPTO SIN DESCRIPCION\")) AS ctodesxp, ";
										  $qCtoId .= "$cAlfa.fpar0115.pucretxx ";
										  $qCtoId .= "FROM $cAlfa.fpar0119 ";
										  $qCtoId .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
										  $qCtoId .= "WHERE ";
										  $qCtoId .= "$cAlfa.fpar0119.ctoidxxx = \"$gCtoCto\" AND ";
										  switch ($gFunction) {
	  										case "cCtoCtoRf":
	  										case "cCtoCtrFs":
	  										case "cCtoCtoRv":
	  										case "cCtoCtoRc":
	  											$qCtoId .= "$cAlfa.fpar0115.pucterxx = \"R\" AND ";
	  										break;
	  										default:
	  											$qCtoId .= "$cAlfa.fpar0115.pucdetxx = \"P\" AND ";
	  										break;
										  }
										  $qCtoId .= "($cSqlCom) AND ";
										  $qCtoId .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
										  $qCtoId .= "ORDER BY ABS($cAlfa.fpar0119.ctoidxxx) ";
										  $xCtoId  = f_MySql("SELECT","",$qCtoId,$xConexion01,"");
										  //f_Mensaje(__FILE__,__LINE__,$qCtoId."~".mysql_num_rows($xCtoId));

	  									if ($xCtoId && mysql_num_rows($xCtoId) > 0) {
	  										while ($xRCI = mysql_fetch_array($xCtoId)) { ?>
													<script languaje = "javascript">
														parent.fmwork.document.forms['frgrm']['<?php echo $cId ?>'].value ='<?php echo $xRCI['ctoidxxx']?>';
														parent.fmwork.document.forms['frgrm']['<?php echo $cDes ?>'].value='<?php echo $xRCI['ctodesxp']?>';
	                          if ('<?php echo $cRet ?>' != '') {
	                        	  parent.fmwork.document.forms['frgrm']['<?php echo $cRet ?>'].value='<?php echo $xRCI['pucretxx']?>';
	                          }
													</script>
	      	      				<?php break;
	  										}
	  									} else {
	  									  //f_Mensaje(__FILE__,__LINE__,"Entre");
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
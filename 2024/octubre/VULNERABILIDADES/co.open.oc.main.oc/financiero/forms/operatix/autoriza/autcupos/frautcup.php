<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");
	include("../../../../libs/php/uticonta.php");

?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Param&eacute;trica de Cupos</title>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
	  </head>
	  <body topmargin = 0 leftmargin = 0 marginwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="250">
				<tr>
					<td>
						<fieldset>
			   			<legend>Param&eacute;trica de Cupos</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":

											## formularios NO LEGALIZADOS ##
	  									$qDatDo  = "SELECT * ";
										  $qDatDo .= "FROM $cAlfa.sys00121 ";
											$qDatDo .= "WHERE ";
											$qDatDo .= "$cAlfa.sys00121.docidxxx LIKE \"%$cDocId%\" AND ";
											$qDatDo .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" AND ";
											$qDatDo .= "$cAlfa.sys00121.doccupxx = \"\" ";
											$qDatDo .= "ORDER BY CONVERT(docidxxx,signed) ASC ";
											$xDatDo = mysql_query($qDatDo,$xConexion01);
											## FIN formularios NO LEGALIZADOS ##

	  									if ($xDatDo && mysql_num_rows($xDatDo) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "250">
														<tr>
															<td width = "50" Class = "name"><center>SUC</center></td>
															<td width = "100" Class = "name"><center>DO</center></td>
															<td width = "50" Class = "name"><center>SUF</center></td>


															<?php while ($mDatDo = mysql_fetch_array($xDatDo)) {

																##Traigo Nombre del Importador
																$qCliDat  = "SELECT $cAlfa.SIAI0150.*, ";
																$qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\", $cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX ";
																$qCliDat .= "FROM $cAlfa.SIAI0150 ";
																$qCliDat .= "WHERE ";
																$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mDatDo['cliidxxx']}\" LIMIT 0,1 ";
																$xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
																$vCliDat = mysql_fetch_array($xCliDat);
																##Fin Traigo Nombre del Importador ##

																$cCupNom = $vCliDat['CLICUPTI'];
																switch ($vCliDat['CLICUPTI']){
																	 case "LIMITADO":
																	    $vCliDat['CLICUPCL'] = number_format($vCliDat['CLICUPCL'],0,',','');
																      $vCliDat['CLICUPOP'] = number_format($vCliDat['CLICUPOP'],0,',','');
																	 break;
																	 case "ILIMITADO":
																	   $vCliDat['CLICUPCL'] = "";
																     $vCliDat['CLICUPOP'] = "";
																	 break;
																	 case "LIMITADO/ILIMITADO":
																	   $vCliDat['CLICUPCL'] = number_format($vCliDat['CLICUPCL'],0,',','');
																     $vCliDat['CLICUPOP'] = "";
																	 break;
																	 case "ILIMITADO/LIMITADO":
																	   $vCliDat['CLICUPCL'] = "";
																     $vCliDat['CLICUPOP'] = number_format($vCliDat['CLICUPOP'],0,',','');
																	 break;
													         case "SINCUPO":
													         default:
													           $vCliDat['CLICUPTI'] = "SINCUPO";
													           $vCliDat['CLICUPCL'] = number_format(0,0,',','.');
													           $vCliDat['CLICUPOP'] = number_format(0,0,',','.');
													           $cCupNom = "SIN CUPO";
													         break;
															  }

																$cSaldo = f_Traer_Cupos_Financieros($mDatDo['cliidxxx'],$mDatDo['docidxxx'],$mDatDo['docsufxx']);

																if (mysql_num_rows($xDatDo) > 1) { ?>
																	<tr>
																		<td width = "050" class= "name"> <?php echo $mDatDo['sucidxxx'] ?></td>
																		<td width = "100" class= "name">
																			<a href = "javascript:window.opener.document.forms['frgrm']['cDocId'].value       ='<?php echo $mDatDo['docidxxx']?>';
																														window.opener.document.forms['frgrm']['cDocSuf'].value      ='<?php echo $mDatDo['docsufxx']?>';
																														window.opener.document.forms['frgrm']['cSucId'].value       ='<?php echo $mDatDo['sucidxxx']?>';
																														window.opener.document.forms['frgrm']['cDocPed'].value      ='<?php echo $mDatDo['docpedxx']?>';
																														window.opener.document.forms['frgrm']['cDocTip'].value      ='<?php echo $mDatDo['doctipxx']?>';
																														window.opener.document.forms['frgrm']['cCliId'].value       ='<?php echo $mDatDo['cliidxxx']?>';
																														window.opener.document.forms['frgrm']['cCliDV'].value       ='<?php echo f_Digito_Verificacion($mDatDo['cliidxxx'])?>';
																														window.opener.document.forms['frgrm']['cCliNom'].value      ='<?php echo $vCliDat['CLINOMXX']?>';
																														window.opener.document.forms['frgrm']['cCliCupTiNom'].value ='<?php echo $vCliDat['CLICUPTI']?>';
																														window.opener.document.forms['frgrm']['cCliCupCl'].value    ='<?php echo $vCliDat['CLICUPCL']?>';
																														window.opener.document.forms['frgrm']['cCliCupOp'].value    ='<?php echo $vCliDat['CLICUPOP'] ?>';
																														window.opener.document.forms['frgrm']['cComId'].value       ='<?php echo $mDatDo['comidxxx'] ?>';
																														window.opener.document.forms['frgrm']['cComCod'].value      ='<?php echo $mDatDo['comcodxx'] ?>';
																														window.opener.document.forms['frgrm']['cPucId'].value       ='<?php echo $mDatDo['pucidxxx'] ?>';
																														window.opener.document.forms['frgrm']['cCcoId'].value       ='<?php echo $mDatDo['ccoidxxx'] ?>';
																														window.opener.document.forms['frgrm']['dRegFCre'].value     ='<?php echo $mDatDo['regfcrex'] ?>';
																														window.opener.document.forms['frgrm']['cSalDo'].value       ='<?php echo $cSaldo ?>';
																														window.close()"><?php echo $mDatDo['docidxxx'] ?></a></td>
																		<td width = "050" class= "name"> <?php echo $mDatDo['docsufxx'] ?></td>
																	</tr>
																<?php	} else { ?>
																	<script language="javascript">
																		window.opener.document.forms['frgrm']['cDocId'].value       = '<?php echo $mDatDo['seridxxx'] ?>';
																		window.opener.document.forms['frgrm']['cDocSuf'].value      = '<?php echo $mDatDo['docsufxx'] ?>';
																		window.opener.document.forms['frgrm']['cSucId'].value       = '<?php echo $mDatDo['sucidxxx'] ?>';
																		window.opener.document.forms['frgrm']['cDocPed'].value      = '<?php echo $mDatDo['docpedxx'] ?>';
																		window.opener.document.forms['frgrm']['cDocTip'].value      = '<?php echo $mDatDo['doctipxx'] ?>';
																		window.opener.document.forms['frgrm']['cCliId'].value       = '<?php echo $mDatDo['cliidxxx'] ?>';
																		window.opener.document.forms['frgrm']['cCliDV'].value       = "<?php echo f_Digito_Verificacion($mDatDo['cliidxxx']) ?>";
																		window.opener.document.forms['frgrm']['cCliNom'].value      = '<?php echo $vCliDat['CLINOMXX'] ?>';
																		window.opener.document.forms['frgrm']['cCliCupTiNom'].value = '<?php echo $vCliDat['CLICUPTI'] ?>';
																		window.opener.document.forms['frgrm']['cCliCupCl'].value    = '<?php echo $vCliDat['CLICUPCL'] ?>';
																		window.opener.document.forms['frgrm']['cCliCupOp'].value    = '<?php echo $vCliDat['CLICUPOP'] ?>';
																		window.opener.document.forms['frgrm']['cComId'].value       ='<?php echo $mDatDo['comidxxx'] ?>';
																		window.opener.document.forms['frgrm']['cComCod'].value      ='<?php echo $mDatDo['comcodxx'] ?>';
																		window.opener.document.forms['frgrm']['cPucId'].value       ='<?php echo $mDatDo['pucidxxx'] ?>';
																		window.opener.document.forms['frgrm']['cCcoId'].value       ='<?php echo $mDatDo['ccoidxxx'] ?>';
																		window.opener.document.forms['frgrm']['dRegFCre'].value     ='<?php echo $mDatDo['regfcrex'] ?>';
																		window.opener.document.forms['frgrm']['cSalDo'].value       ='<?php echo $cSaldo ?>';
																		window.close();
																	</script>
																<?php }
															} ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); ?>
												<script language="javascript">
													window.opener.document.forms['frgrm']['cDocId'].value       = '';
													window.opener.document.forms['frgrm']['cDocSuf'].value      = '';
													window.opener.document.forms['frgrm']['cSucId'].value       = '';
													window.opener.document.forms['frgrm']['cDocPed'].value      = '';
													window.opener.document.forms['frgrm']['cDocTip'].value      = '';
													window.opener.document.forms['frgrm']['cCliId'].value       = '';
													window.opener.document.forms['frgrm']['cCliDV'].value       = '';
													window.opener.document.forms['frgrm']['cCliNom'].value      = '';
													window.opener.document.forms['frgrm']['cCliCupTiNom'].value = '';
													window.opener.document.forms['frgrm']['cCliCupCl'].value    = '';
													window.opener.document.forms['frgrm']['cCliCupOp'].value    = '';
													window.opener.document.forms['frgrm']['cComId'].value       ='';
													window.opener.document.forms['frgrm']['cComCod'].value      ='';
													window.opener.document.forms['frgrm']['cPucId'].value       ='';
													window.opener.document.forms['frgrm']['cCcoId'].value       ='';
													window.opener.document.forms['frgrm']['dRegFCre'].value     ='';
													window.opener.document.forms['frgrm']['cSalDo'].value       ='';
												</script>
											<?php }
	  								break;
	  								case "VALID":
											$qDatDo  = "SELECT * ";
										  $qDatDo .= "FROM $cAlfa.sys00121 ";
											$qDatDo .= "WHERE ";
											$qDatDo .= "$cAlfa.sys00121.docidxxx = \"$cDocId\" AND ";
											$qDatDo .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" AND ";
											$qDatDo .= "$cAlfa.sys00121.doccupxx = \"\" ";
											$xDatDo = mysql_query($qDatDo,$xConexion01);
	  									if ($xDatDo && mysql_num_rows($xDatDo) == 1) {
	  										while ($mDatDo = mysql_fetch_array($xDatDo)) {
	  											##Traigo Nombre del Importador
													$qCliDat  = "SELECT $cAlfa.SIAI0150.*, ";
													$qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\", $cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX ";
													$qCliDat .= "FROM $cAlfa.SIAI0150 ";
													$qCliDat .= "WHERE ";
													$qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mDatDo['cliidxxx']}\" LIMIT 0,1 ";
													$xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
													$vCliDat = mysql_fetch_array($xCliDat);
													##Fin Traigo Nombre del Importador ##

													$cCupNom = $vCliDat['CLICUPTI'];
													switch ($vCliDat['CLICUPTI']){
														 case "LIMITADO":
																$vCliDat['CLICUPCL'] = number_format($vCliDat['CLICUPCL'],0,',','');
																$vCliDat['CLICUPOP'] = number_format($vCliDat['CLICUPOP'],0,',','');
														 break;
														 case "ILIMITADO":
															 $vCliDat['CLICUPCL'] = "";
															 $vCliDat['CLICUPOP'] = "";
														 break;
														 case "LIMITADO/ILIMITADO":
															 $vCliDat['CLICUPCL'] = number_format($vCliDat['CLICUPCL'],0,',','');
															 $vCliDat['CLICUPOP'] = "";
														 break;
														 case "ILIMITADO/LIMITADO":
															 $vCliDat['CLICUPCL'] = "";
															 $vCliDat['CLICUPOP'] = number_format($vCliDat['CLICUPOP'],0,',','');
														 break;
														 case "SINCUPO":
														 default:
															 $vCliDat['CLICUPTI'] = "SINCUPO";
															 $vCliDat['CLICUPCL'] = number_format(0,0,',','.');
															 $vCliDat['CLICUPOP'] = number_format(0,0,',','.');
															 $cCupNom = "SIN CUPO";
														 break;
													}

													$cSaldo = f_Traer_Cupos_Financieros($mDatDo['cliidxxx'],$mDatDo['docidxxx'],$mDatDo['docsufxx']);
	  											?>
													<script language = "javascript">
														parent.fmwork.document.forms['frgrm']['cDocId'].value       = '<?php echo $mDatDo['docidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cDocSuf'].value      = '<?php echo $mDatDo['docsufxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cSucId'].value       = '<?php echo $mDatDo['sucidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cDocPed'].value      = '<?php echo $mDatDo['docpedxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cDocTip'].value      = '<?php echo $mDatDo['doctipxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cCliId'].value       = '<?php echo $mDatDo['cliidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cCliDV'].value       = '<?php echo f_Digito_Verificacion($mDatDo['cliidxxx']) ?>';
														parent.fmwork.document.forms['frgrm']['cCliNom'].value      = '<?php echo $vCliDat['CLINOMXX'] ?>';
														parent.fmwork.document.forms['frgrm']['cCliCupTiNom'].value = '<?php echo $vCliDat['CLICUPTI'] ?>';
														parent.fmwork.document.forms['frgrm']['cCliCupCl'].value    = '<?php echo $vCliDat['CLICUPCL'] ?>';
														parent.fmwork.document.forms['frgrm']['cCliCupOp'].value    = '<?php echo $vCliDat['CLICUPOP'] ?>';
														parent.fmwork.document.forms['frgrm']['cComId'].value       ='<?php echo $mDatDo['comidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cComCod'].value      ='<?php echo $mDatDo['comcodxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cPucId'].value       ='<?php echo $mDatDo['pucidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['cCcoId'].value       ='<?php echo $mDatDo['ccoidxxx'] ?>';
														parent.fmwork.document.forms['frgrm']['dRegFCre'].value     ='<?php echo $mDatDo['regfcrex'] ?>';
														parent.fmwork.document.forms['frgrm']['cSalDo'].value       ='<?php echo $cSaldo ?>';
													</script>
	      	      				<?php break;
	  										}
	  									} else { ?>
												<script language = "javascript">
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

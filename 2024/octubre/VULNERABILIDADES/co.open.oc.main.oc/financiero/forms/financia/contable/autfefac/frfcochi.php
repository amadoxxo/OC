<?php
  namespace openComex;
	/**
   * Valid/Window de Parametrica de Facturas.
   * --- Descripcion: Permite Visualizar las Facturas con las que Hizo Match el Consecutivo Ingresado.
   * @author Juan Jose Trujillo Ch <juan.trujillo@open-eb.co>
   * @package Opencomex
   */

	include("../../../../libs/php/utility.php");
?>

<?php if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Param&eacute;trica de Facturas</title>
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
			   			<legend>Param&eacute;trica de Facturas</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":
	  									##Traigo Datos de la factura ## 						  		
						 					$qFacDat  = "SELECT ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comidxxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcodxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcscxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcsc2x, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comfacpr, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx  ";
											$qFacDat .= "FROM $cAlfa.fcoc$cPerAno ";
											$qFacDat .= "WHERE ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comidxxx = \"F\" AND ";
											//Se valida el tipo de factura (Provisional o Definitiva) para realizar la consulta
											if($cTipoFac == "PROVISIONAL"){
												$qFacDat .= "(($cAlfa.fcoc$cPerAno.comcscxx LIKE \"%$cComCsc%\" AND ";
												$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx = \"PROVISIONAL\") OR ";
												$qFacDat .= "(SUBSTRING_INDEX(SUBSTRING_INDEX(comfacpr, \"-\", -2) , \"-\" ,1) LIKE \"%$cComCsc%\" AND ";
												$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx = \"ACTIVO\")) ";
											}else{
												$qFacDat .= "$cAlfa.fcoc$cPerAno.comcscxx LIKE \"%$cComCsc%\" AND ";
												$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx = \"ACTIVO\" ";
											}
											$qFacDat .= "ORDER BY $cAlfa.fcoc$cPerAno.comfecxx ASC ";
											$xFacDat = f_MySql("SELECT","",$qFacDat,$xConexion01,"");
											//f_Mensaje(__FILE__,__LINE__,$qFacDat."~".mysql_num_rows($xFacDat));
											## FIN Traigo Factura ##						
											
	  									if ($xFacDat && mysql_num_rows($xFacDat) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "250">
														<tr>
															<td width = "050" Class = "name"><center>ID</center></td>
															<td width = "050" Class = "name"><center>COD</center></td>
															<td width = "100" Class = "name"><center>FACTURA</center></td>
															<td width = "050" Class = "name"><center>ESTADO</center></td>
														</tr>
														<?php 
														while ($mFacDat = mysql_fetch_array($xFacDat)) {
															// Se asigna el consecutivo de la factura segun el tipo (Provisional o Definita)
															if($mFacDat['regestxx'] == "PROVISIONAL" && $cTipoFac == "PROVISIONAL"){
																$cComIdxx = $mFacDat['comidxxx'];
																$cComCodx = $mFacDat['comcodxx'];
																$cComCscx = $mFacDat['comcscxx'];
																$cComCsc2 = $mFacDat['comcsc2x'];
																$cRegEstx = $mFacDat['regestxx'];
															}elseif($mFacDat['regestxx'] == "ACTIVO" && $cTipoFac == "PROVISIONAL"){
																$vComFacpr = explode('-', $mFacDat['comfacpr']);
																$cComIdxx = $vComFacpr[0];
																$cComCodx = $vComFacpr[1];
																$cComCscx = $vComFacpr[2];
																$cComCsc2 = $vComFacpr[3];
																$cRegEstx = $mFacDat['regestxx'];
															}else{
																$cComIdxx = $mFacDat['comidxxx'];
																$cComCodx = $mFacDat['comcodxx'];
																$cComCscx = $mFacDat['comcscxx'];
																$cComCsc2 = $mFacDat['comcsc2x'];
																$cRegEstx = $mFacDat['regestxx'];
															}

															if (mysql_num_rows($xFacDat) > 1) { ?>
																<tr>
																	<td width = "050" class= "name"> <?php echo $cComIdxx ?></td>
																	<td width = "050" class= "name"> <?php echo $cComCodx ?></td>
																	<td width = "100" class= "name">
																		<a href = "javascript:window.opener.document.forms['frgrm']['cComId'].value  ='<?php echo $cComIdxx ?>';
																													window.opener.document.forms['frgrm']['cComCod'].value ='<?php echo $cComCodx ?>';
																													window.opener.document.forms['frgrm']['cComCsc'].value ='<?php echo $cComCscx ?>';
																													window.opener.document.forms['frgrm']['cComCsc2'].value='<?php echo $cComCsc2 ?>';
																													close()"><?php echo $cComCscx ?></a></td>
																	<td width = "050" class= "name"> <?php echo $cRegEstx ?></td>
																</tr>
															<?php	} else { ?>														
																<script language="javascript">
																	window.opener.document.forms['frgrm']['cComId'].value  = '<?php echo $cComIdxx ?>';
																	window.opener.document.forms['frgrm']['cComCod'].value = '<?php echo $cComCodx ?>';
																	window.opener.document.forms['frgrm']['cComCsc'].value = '<?php echo $cComCscx ?>';
																	window.opener.document.forms['frgrm']['cComCsc2'].value= '<?php echo $cComCsc2 ?>';
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
	  									<?php	
	  									
	  									} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");?>														
													<script language="javascript">
														window.opener.document.forms['frgrm']['cComId'].value  = '';
														window.opener.document.forms['frgrm']['cComCod'].value = '';
														window.opener.document.forms['frgrm']['cComCsc'].value = '';
														window.opener.document.forms['frgrm']['cComCsc2'].value= '';
														window.close();
													</script>
											<?php }
										break;

	  								case "VALID":
	  									##Traigo Datos de la factura ## 						  		
						 
											$qFacDat  = "SELECT ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comidxxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcodxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcscxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comcsc2x, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.teridxxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comfecxx, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comfacpr, ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx  ";
											$qFacDat .= "FROM $cAlfa.fcoc$cPerAno ";
											$qFacDat .= "WHERE ";
											$qFacDat .= "$cAlfa.fcoc$cPerAno.comidxxx = \"F\" AND ";
											//Se valida el tipo de factura (Provisional o Definitiva) para realizar la consulta
											if($cTipoFac == "PROVISIONAL"){
												$qFacDat .= "(($cAlfa.fcoc$cPerAno.comcscxx = \"$cComCsc\" AND ";
												$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx = \"PROVISIONAL\") OR ";
												$qFacDat .= "(\"$cComCsc\" = SUBSTRING_INDEX(SUBSTRING_INDEX(comfacpr, \"-\", -2) , \"-\" ,1) AND ";
												$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx = \"ACTIVO\")) ";
											}else{
												$qFacDat .= "$cAlfa.fcoc$cPerAno.comcscxx = \"$cComCsc\" AND ";
												$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx = \"ACTIVO\" ";
											}
											$qFacDat .= "ORDER BY $cAlfa.fcoc$cPerAno.comfecxx ASC ";
											$xFacDat = f_MySql("SELECT","",$qFacDat,$xConexion01,"");
											//f_Mensaje(__FILE__,__LINE__,$qFacDat."~".mysql_num_rows($xFacDat));
											## FIN Traigo Factura ##
																						
	  									if ($xFacDat && mysql_num_rows($xFacDat) == 1) {
	  										while ($mFacDat = mysql_fetch_array($xFacDat)) {
													if($mFacDat['regestxx'] == "PROVISIONAL" && $cTipoFac == "PROVISIONAL"){
														$cComIdxx = $mFacDat['comidxxx'];
														$cComCodx = $mFacDat['comcodxx'];
														$cComCscx = $mFacDat['comcscxx'];
														$cComCsc2 = $mFacDat['comcsc2x'];
														$cRegEstx = $mFacDat['regestxx'];
													}elseif($mFacDat['regestxx'] == "ACTIVO" && $cTipoFac == "PROVISIONAL"){
														$vComFacpr = explode('-', $mFacDat['comfacpr']);
														$cComIdxx = $vComFacpr[0];
														$cComCodx = $vComFacpr[1];
														$cComCscx = $vComFacpr[2];
														$cComCsc2 = $vComFacpr[3];
														$cRegEstx = $mFacDat['regestxx'];
													}else{
														$cComIdxx = $mFacDat['comidxxx'];
														$cComCodx = $mFacDat['comcodxx'];
														$cComCscx = $mFacDat['comcscxx'];
														$cComCsc2 = $mFacDat['comcsc2x'];
														$cRegEstx = $mFacDat['regestxx'];
													}

	  											?>
													<script language = "javascript">
														parent.fmwork.document.forms['frgrm']['cComId'].value  = '<?php echo $cComIdxx ?>';
														parent.fmwork.document.forms['frgrm']['cComCod'].value = '<?php echo $cComCodx ?>';
														parent.fmwork.document.forms['frgrm']['cComCsc'].value = '<?php echo $cComCscx ?>';
														parent.fmwork.document.forms['frgrm']['cComCsc2'].value= '<?php echo $cComCsc2 ?>';
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
} 
?>
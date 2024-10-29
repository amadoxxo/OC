<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  $qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_NAME = \"fpar0117\" LIMIT 0,1";
  $xTblCom = f_MySql("SELECT","",$qTblCom,$xConexion01,"");
  $vTblCom = mysql_fetch_array($xTblCom);
  //f_Mensaje(__FILE__,__LINE__,$vTblCom['TABLE_COMMENT']);
if ($gModo != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title><?php echo $vTblCom['TABLE_COMMENT'] ?></title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
			<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
			<script languaje = "javascript">
				function f_Periodo_Anterior(xTipo) {
					switch(xTipo) {
						case "VALID":
							parent.fmwork.document.frgrm.dComFec.options[0] = new Option('-- SELECCIONE --','');
							
							var xRuta = "frcpaant.php?gComId="+document.forms['frgrm']['cComId'].value+
																			"&gComCod="+document.forms['frgrm']['cComCod'].value;
							parent.parent.fmpro.location = xRuta;
						break;
						case "WINDOW":
							window.opener.document.frgrm.dComFec.options[0] = new Option('-- SELECCIONE --','');
							
							var xRuta = "frcpaant.php?gComId="+document.forms['frgrm']['cComId'].value+
																			"&gComCod="+document.forms['frgrm']['cComCod'].value;
							window.opener.parent.fmpro.location = xRuta;
						break;
					}
		  	}
		  </script>
	  </head>
	  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
	  <center>
			<table border = "0" cellpadding = "0" cellspacing = "0" width = "450">
				<tr>
					<td>
						<fieldset>
			   			<legend><?php echo $vTblCom['TABLE_COMMENT'] ?></legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  					  <input type = "hidden" name = "cComId"  value = "">
								<input type = "hidden" name = "cComCod" value = "">
	  						<?php
	  							/**
	  							 * Busco los documentos contables que el usuario tiene autorizados
	  							 */
	  								$qUsrDoc  = "SELECT USRDOCXX ";
	  								$qUsrDoc .= "FROM $cAlfa.SIAI0003 ";
	  								$qUsrDoc .= "WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" AND ";
	  								$qUsrDoc .= "USRDOCXX LIKE \"%|P~%\" AND ";
	  								$qUsrDoc .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
	  								$xUsrDoc  = f_MySql("SELECT","",$qUsrDoc,$xConexion01,"");
	  								$cUsrDoc = "";
	  								if(mysql_num_rows($xUsrDoc)>0){
	  									$xRUD = mysql_fetch_array($xUsrDoc);
	  									$mUsrDoc = f_explode_array($xRUD['USRDOCXX'],"|","~");
	  									for($i=0;$i<count($mUsrDoc);$i++){
	  										if($mUsrDoc[$i][0]=="P"){
	  											$cUsrDoc .= "\"{$mUsrDoc[$i][1]}\",";			
	  										}
	  									}
	  									$cUsrDoc = substr($cUsrDoc,0,strlen($cUsrDoc)-1);
	  								}
	  							/**
	  							 * Fin de busqueda de docuemntos
	  							 */
	  							if($cUsrDoc <> ""){
		  							switch ($gModo) {
		  								case "VALID":
		  									$qDatExt  = "SELECT * ";
												$qDatExt .= "FROM $cAlfa.fpar0117 ";
												$qDatExt .= "WHERE ";
												$qDatExt .= "comidxxx = \"P\" AND ";
												$qDatExt .= "comtipxx = \"CPC\" AND ";
												if($gComCod <> ""){
													$qDatExt .= "comcodxx = \"$gComCod\" AND ";
												}
												$qDatExt .= "comcodxx IN ($cUsrDoc) AND ";
												$qDatExt .= "regestxx = \"ACTIVO\" ORDER BY comdesxx ";
		  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
		  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
		  									if (mysql_num_rows($xDatExt) == 1) {
		  										$vDatExt = mysql_fetch_array($xDatExt); ?>
													<script languaje = "javascript">
														parent.fmwork.document.forms['frgrm']['cComId'].value  = "<?php echo $vDatExt['comidxxx'] ?>";
														parent.fmwork.document.forms['frgrm']['cComCod'].value = "<?php echo $vDatExt['comcodxx'] ?>";
														parent.fmwork.document.forms['frgrm']['cComDes'].value = "<?php echo $vDatExt['comdesxx'] ?>";
														parent.fmwork.document.forms['frgrm']['cComTco'].value = "<?php echo $vDatExt['comtcoxx'] ?>";
														parent.fmwork.document.forms['frgrm']['cComCco'].value = "<?php echo $vDatExt['comccoxx'] ?>";
														document.forms['frgrm']['cComId'].value  = '<?php echo $vDatExt['comidxxx'] ?>';
														document.forms['frgrm']['cComCod'].value = '<?php echo $vDatExt['comcodxx'] ?>';
														if ('<?php echo $_COOKIE['kModo'] ?>' == 'ANTERIOR' && '<?php echo $vSysStr['financiero_permitir_digitar_fecha_periodo_anterior']?>' == 'NO') {
															f_Periodo_Anterior('VALID');
													  }
														parent.fmwork.f_Activa_Csc();
													</script>
	  										<?php } else { ?>
													<script languaje = "javascript">
		      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW");
													</script>
												<?php }
		      	      		break;
		  								case "WINDOW":
			  								$qDatExt  = "SELECT * ";
												$qDatExt .= "FROM $cAlfa.fpar0117 ";
												$qDatExt .= "WHERE ";
												$qDatExt .= "comidxxx = \"P\" AND ";
												$qDatExt .= "comtipxx = \"CPC\" AND ";
												$qDatExt .= "comcodxx LIKE \"%$gComCod%\" AND ";
												$qDatExt .= "comcodxx IN ($cUsrDoc) AND ";
												$qDatExt .= "regestxx = \"ACTIVO\" ORDER BY comdesxx";
		  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
		  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
	 											if (mysql_num_rows($xDatExt) > 0) { ?>
			 										<center>
						    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "450">
															<tr>
																<td widht = "030" Class = "name"><center>Com</center></td>
																<td widht = "030" Class = "name"><center>Cod</center></td>
																<td widht = "390" Class = "name"><center>Descripcion</center></td>
															</tr>
															<?php while ($xRDE = mysql_fetch_array($xDatExt)) {
																if (mysql_num_rows($xDatExt) > 1) { ?>
																	<tr>
																		<td width = "030" Class = "name"><?php echo $xRDE['comidxxx'] ?></td>
																		<td width = "030" Class = "name">
																			<a href = "javascript:window.opener.document.forms['frgrm']['cComId'].value  = '<?php echo $xRDE['comidxxx'] ?>';
																														window.opener.document.forms['frgrm']['cComCod'].value = '<?php echo $xRDE['comcodxx'] ?>';
																														window.opener.document.forms['frgrm']['cComDes'].value = '<?php echo $xRDE['comdesxx'] ?>';
																														window.opener.document.forms['frgrm']['cComTco'].value = '<?php echo $xRDE['comtcoxx'] ?>';
																														window.opener.document.forms['frgrm']['cComCco'].value = '<?php echo $xRDE['comccoxx'] ?>';
																														document.forms['frgrm']['cComId'].value  = '<?php echo $xRDE['comidxxx'] ?>';
	                        																	document.forms['frgrm']['cComCod'].value = '<?php echo $xRDE['comcodxx'] ?>';
	                        																	if ('<?php echo $_COOKIE['kModo'] ?>' == 'ANTERIOR' && '<?php echo $vSysStr['financiero_permitir_digitar_fecha_periodo_anterior']?>' == 'NO') {
	                        																		 f_Periodo_Anterior('WINDOW');
	                        																	}
	                        																	window.opener.f_Activa_Csc();
																														window.close()"><?php echo $xRDE['comcodxx'] ?>
																			</a>
																		</td>
																		<td width = "390" Class = "name"><?php echo $xRDE['comdesxx'] ?></td>
																	</tr>
																<?php	} else { ?>
																	<script languaje="javascript">
																		window.opener.document.forms['frgrm']['cComId'].value  = '<?php echo $xRDE['comidxxx'] ?>';
																		window.opener.document.forms['frgrm']['cComCod'].value = '<?php echo $xRDE['comcodxx'] ?>';
																		window.opener.document.forms['frgrm']['cComDes'].value = '<?php echo $xRDE['comdesxx'] ?>';
																		window.opener.document.forms['frgrm']['cComTco'].value = '<?php echo $xRDE['comtcoxx'] ?>';
																		window.opener.document.forms['frgrm']['cComCco'].value = '<?php echo $xRDE['comccoxx'] ?>';
																		document.forms['frgrm']['cComId'].value  = '<?php echo $xRDE['comidxxx'] ?>';
																		document.forms['frgrm']['cComCod'].value = '<?php echo $xRDE['comcodxx'] ?>';
																		if ('<?php echo $_COOKIE['kModo'] ?>' == 'ANTERIOR' && '<?php echo $vSysStr['financiero_permitir_digitar_fecha_periodo_anterior']?>' == 'NO') {
																			f_Periodo_Anterior('WINDOW');
																		}
																		window.opener.f_Activa_Csc();
																		window.close();
																	</script>
																<?php }
															} ?>
														</table>
													</center>
			 									<?php	} else {
			 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique.");
			 									}
			  							break;
		  							}
	  							}else{
										f_Mensaje(__FILE__,__LINE__,"El Usuario no tiene Documentos Autorizados para Realizar Causacion Automatica a Terceros, Verifique.");
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
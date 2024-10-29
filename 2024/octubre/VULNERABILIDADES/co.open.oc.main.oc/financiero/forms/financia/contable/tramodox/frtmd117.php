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
							var xRuta = "frtmdant.php?gComId="+document.forms['frnav']['cComId'].value+
																			"&gComCod="+document.forms['frnav']['cComCod'].value;
							parent.parent.fmpro.location = xRuta;
						break;
						case "WINDOW":
							var xRuta = "frtmdant.php?gComId="+document.forms['frnav']['cComId'].value+
																			"&gComCod="+document.forms['frnav']['cComCod'].value;
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
	  					<form name = "frnav" action = "" method = "post" target = "fmpro">
	  					  <input type = "hidden" name = "cComId"  value = "">
								<input type = "hidden" name = "cComCod" value = "">
	  						<?php
	  						/*
	  						 * Busco los Tipos de Comprobantes que son de de tipo AJUSTES
	  						 */
	  							$qDatCom  = "SELECT comidxxx,comcodxx ";
	  							$qDatCom .= "FROM $cAlfa.fpar0117 ";
	  							$qDatCom .= "WHERE ";
                  $qDatCom .= "comtipxx = \"AJUSTES\" AND ";
                  $qDatCom .= "comtcoxx = \"MANUAL\"  AND ";
	  							$qDatCom .= "regestxx = \"ACTIVO\"  ";
                  $xDatCom  = f_MySql("SELECT","",$qDatCom,$xConexion01,"");
                  // f_Mensaje(__FILE__,__LINE__,$qDatCom." ~ ".mysql_num_rows($xDatCom));
	  							$cUsrDoc = "";
	  							if(mysql_num_rows($xDatCom)>0){
	  								while ($xRDC = mysql_fetch_array($xDatCom)) {
	  								/**
	  							 * Busco los documentos contables que el usuario tiene autorizados
	  							 */
	  									$qUsrDoc  = "SELECT USRDOCXX ";
	  									$qUsrDoc .= "FROM $cAlfa.SIAI0003 ";
	  									$qUsrDoc .= "WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" AND ";
	  									$qUsrDoc .= "USRDOCXX LIKE \"%|{$xRDC['comidxxx']}~%\" AND ";
	  									$qUsrDoc .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
                      $xUsrDoc  = f_MySql("SELECT","",$qUsrDoc,$xConexion01,"");
                      // f_Mensaje(__FILE__,__LINE__,$qUsrDoc." ~ ".mysql_num_rows($xUsrDoc));
	  									if(mysql_num_rows($xUsrDoc)>0){
	  										$xRUD = mysql_fetch_array($xUsrDoc);
	  										$mUsrDoc = f_explode_array($xRUD['USRDOCXX'],"|","~");
	  										for($i=0;$i<count($mUsrDoc);$i++){
	  											if($mUsrDoc[$i][0] == $xRDC['comidxxx'] && $mUsrDoc[$i][1] == $xRDC['comcodxx']){
		  											$cUsrDoc .= "(comidxxx = \"{$mUsrDoc[$i][0]}\" AND comcodxx = \"{$mUsrDoc[$i][1]}\")";
		  											if ($i < (count($mUsrDoc) - 1)) { $cUsrDoc .= " OR "; }
	  											}
	  										}
	  									}
	  								}
	  							}
	  							
	  							if(substr($cUsrDoc,-3) == 'OR '){
	  								$cUsrDoc = substr($cUsrDoc,0,strlen($cUsrDoc)-3);
	  							}
	  							
	  							
	  						//f_Mensaje(__FILE__,__LINE__,$cUsrDoc);
	  						/*
	  						 * Fin de busqueda de documentos
	  						 */
	  					
	  							if($cUsrDoc <> ""){
		  							switch ($gModo) {
		  								case "VALID":
		  								
			  								for($i=0;$i<count($mUsrDoc);$i++){
			  									if($mUsrDoc[$i][0] == $gComId){
			  										$cUsrDocV .= "\"{$mUsrDoc[$i][1]}\",";	
			  									}
			  								}
		  								
		  									$cUsrDocV = substr($cUsrDocV,0,strlen($cUsrDocV)-1);
		  									$qDatExt  = "SELECT * ";
												$qDatExt .= "FROM $cAlfa.fpar0117 ";
												$qDatExt .= "WHERE ";
												//$qDatExt .= "$cUsrDoc AND ";
												$qDatExt .= "comidxxx = \"$gComId\" AND ";
		  									if($gComCod <> ""){
													$qDatExt .= "comcodxx = \"$gComCod\" AND ";
												}
												$qDatExt .= "comcodxx IN ($cUsrDocV) AND ";
												$qDatExt .= "comtipxx = \"AJUSTES\" AND ";
												$qDatExt .= "regestxx = \"ACTIVO\" ORDER BY comdesxx";
		  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
		  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
		  									if (mysql_num_rows($xDatExt) == 1) {
		  										$vDatExt = mysql_fetch_array($xDatExt); ?>
													<script languaje = "javascript">
														parent.fmwork.document.forms['frnav']['cComId'].value  = "<?php echo $vDatExt['comidxxx'] ?>";
														parent.fmwork.document.forms['frnav']['cComCod'].value = "<?php echo $vDatExt['comcodxx'] ?>";
														parent.fmwork.document.forms['frnav']['cComDes'].value = "<?php echo $vDatExt['comdesxx'] ?>";
														parent.fmwork.document.forms['frnav']['cComTco'].value = "<?php echo $vDatExt['comtcoxx'] ?>";
														parent.fmwork.document.forms['frnav']['cComCco'].value = "<?php echo $vDatExt['comccoxx'] ?>";
														document.forms['frnav']['cComId'].value  = '<?php echo $vDatExt['comidxxx'] ?>';
														document.forms['frnav']['cComCod'].value = '<?php echo $vDatExt['comcodxx'] ?>';
														if ('<?php echo $_COOKIE['kModo'] ?>' == 'ANTERIOR' && '<?php echo $vSysStr['financiero_permitir_digitar_fecha_periodo_anterior']?>' == 'NO') {
															f_Periodo_Anterior('VALID');
													  }
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
														 $qDatExt .= "$cUsrDoc AND ";
														 $qDatExt .= "regestxx = \"ACTIVO\" ORDER BY comidxxx,comcodxx,comdesxx";
					  								 $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
					  								 //f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
		  											
		  											 while ($xRDE = mysql_fetch_array($xDatExt)) {
		  											 	$mComId[count($mComId)] = $xRDE;
		  											 }
		  									
	 											if (mysql_num_rows($xDatExt) > 0) { ?>
			 										<center>
						    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "450">
															<tr>
																<td widht = "030" Class = "name"><center>Com</center></td>
																<td widht = "030" Class = "name"><center>Cod</center></td>
																<td widht = "390" Class = "name"><center>Descripcion</center></td>
															</tr>
															<?php //while ($xRDE = mysql_fetch_array($xDatExt)) { 
															for($y=0;$y<count($mComId);$y++) {
																if (count($mComId) > 1) { ?>
																	<tr>
																		<td width = "030" Class = "name"><?php echo $mComId[$y]['comidxxx'] ?></td>
																		<td width = "030" Class = "name">
																			<a href = "javascript:window.opener.document.forms['frnav']['cComId'].value  = '<?php echo $mComId[$y]['comidxxx'] ?>';
																														window.opener.document.forms['frnav']['cComCod'].value = '<?php echo $mComId[$y]['comcodxx'] ?>';
																														window.opener.document.forms['frnav']['cComDes'].value = '<?php echo $mComId[$y]['comdesxx'] ?>';
																														window.opener.document.forms['frnav']['cComTco'].value = '<?php echo $mComId[$y]['comtcoxx'] ?>';
																														window.opener.document.forms['frnav']['cComCco'].value = '<?php echo $mComId[$y]['comccoxx'] ?>';
																														document.forms['frnav']['cComId'].value  = '<?php echo $mComId[$y]['comidxxx'] ?>';
	                        																	document.forms['frnav']['cComCod'].value = '<?php echo $mComId[$y]['comcodxx'] ?>';
	                        																	if ('<?php echo $_COOKIE['kModo'] ?>' == 'ANTERIOR' && '<?php echo $vSysStr['financiero_permitir_digitar_fecha_periodo_anterior']?>' == 'NO') {
	                        																		 f_Periodo_Anterior('WINDOW');
	                        																	}
																														window.close()"><?php echo $mComId[$y]['comcodxx'] ?>
																			</a>
																		</td>
																		<td width = "390" Class = "name"><?php echo $mComId[$y]['comdesxx'] ?></td>
																	</tr>
																<?php	} else { ?>
																	<script languaje="javascript">
																		window.opener.document.forms['frnav']['cComId'].value  = '<?php echo $mComId[$y]['comidxxx'] ?>';
																		window.opener.document.forms['frnav']['cComCod'].value = '<?php echo $mComId[$y]['comcodxx'] ?>';
																		window.opener.document.forms['frnav']['cComDes'].value = '<?php echo $mComId[$y]['comdesxx'] ?>';
																		window.opener.document.forms['frnav']['cComTco'].value = '<?php echo $mComId[$y]['comtcoxx'] ?>';
																		window.opener.document.forms['frnav']['cComCco'].value = '<?php echo $mComId[$y]['comccoxx'] ?>';
																		document.forms['frnav']['cComId'].value  = '<?php echo $mComId[$y]['comidxxx'] ?>';
																		document.forms['frnav']['cComCod'].value = '<?php echo $mComId[$y]['comcodxx'] ?>';
																		if ('<?php echo $_COOKIE['kModo'] ?>' == 'ANTERIOR' && '<?php echo $vSysStr['financiero_permitir_digitar_fecha_periodo_anterior']?>' == 'NO') {
																			f_Periodo_Anterior('WINDOW');
																		}
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
										f_Mensaje(__FILE__,__LINE__,"El Usuario no tiene Documentos Autorizados para Causacion de Ajustes, Verifique.");
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
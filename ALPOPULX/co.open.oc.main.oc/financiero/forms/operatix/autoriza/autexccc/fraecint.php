<?php
	include("../../../../libs/php/utility.php");
  $qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_NAME = \"SIAI0150\" LIMIT 0,1";
  $xTblCom = f_MySql("SELECT","",$qTblCom,$xConexion01,"");
  $vTblCom = mysql_fetch_array($xTblCom);
	//f_Mensaje(__FILE__,__LINE__,$gModo." ~ ".$gFunction." ~ ".$gSecuencia);

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
	  </head>
	  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
	  <center>
			<table border = "0" cellpadding = "0" cellspacing = "0" width = "550">
				<tr>
					<td>
						<fieldset>
			   			<legend><?php echo $vTblCom['TABLE_COMMENT'] ?></legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  						
	  						  $qCliCoc  = "SELECT * ";
                  $qCliCoc .= "FROM $cAlfa.fpar0151 ";
                  $qCliCoc .= "WHERE ";
                  $qCliCoc .= "cliidxxx = \"$gTerId\" AND ";
                  $qCliCoc .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xCliCoc  = f_MySql("SELECT","",$qCliCoc,$xConexion01,"");
                  $vCliCoc  = mysql_fetch_array($xCliCoc);
                  
                  $cCliInt = explode("~",$vCliCoc['cccintxx']);
                  $cIntermediarios = "";
                  $nEncontro = 0;
                  for ($i=0;$i<count($cCliInt);$i++) {
                    if ($cCliInt[$i] != "") {
                      $cIntermediarios .= "\"";
                      $cIntermediarios .= $cCliInt[$i];
                      $cIntermediarios .= "\"";
                      $cIntermediarios .= ",";
                      
                      if ($gTerId == $cCliInt[$i]) {
                        $nEncontro = 1;
                      }
                    }
                  }
                  if ($nEncontro == 0) {
                    $cIntermediarios .= $gTerId.",";  
                  }
                  $cIntermediarios = substr($cIntermediarios,0,strlen($cIntermediarios)-1);
                  
	  							switch ($gModo) {
	  								case "VALID":
											$qDatExt  = "SELECT *, ";
											$qDatExt .= "IF(CLINOMXX <> \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
											$qDatExt .= "FROM $cAlfa.SIAI0150 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "CLIIDXXX IN ($cIntermediarios) AND ";
											$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY CLINOMXX";
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
			  							// f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

	  									if (mysql_num_rows($xDatExt) == 1) {
	  									  $vDatExt = mysql_fetch_array($xDatExt);
                        ?>
												<script languaje = "javascript">
												  switch ("<?php echo $gFunction ?>") {
												    case "cTerIdInt":
												    case "cTerNomInt":
												      if ("<?php  echo $gSecuencia ?>" == ""){
												        parent.fmwork.document.forms['frgrm']['cTerIdInt'].value   = "<?php echo $vDatExt['CLIIDXXX'] ?>";
                                parent.fmwork.document.forms['frgrm']['cTerDVInt'].value   = "<?php echo f_Digito_Verificacion($vDatExt['CLIIDXXX']) ?>";
                                parent.fmwork.document.forms['frgrm']['cTerNomInt'].value  = "<?php echo $vDatExt['CLINOMXX'] ?>";
                                parent.fmwork.f_CargarTarifasFacturaA();
												      } else {
                                parent.fmwork.document.forms['frgrm']['cTerIdInt<?php  echo $gSecuencia ?>'].value   = "<?php echo $vDatExt['CLIIDXXX'] ?>";
                                parent.fmwork.document.forms['frgrm']['cTerDVInt<?php  echo $gSecuencia ?>'].value   = "<?php echo f_Digito_Verificacion($vDatExt['CLIIDXXX']) ?>";
                                parent.fmwork.document.forms['frgrm']['cTerNomInt<?php echo $gSecuencia ?>'].value  = "<?php echo $vDatExt['CLINOMXX'] ?>";												        
												      }
												    break;
												  }
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
												  if ("<?php  echo $gSecuencia ?>" == ""){
	      	    						  parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW");
	      	    				    } else {
	      	    				      parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW","<?php echo $gSecuencia ?>");
	      	    				    }
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
											$qDatExt  = "SELECT *, ";
											$qDatExt .= "IF(CLINOMXX <> \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
											$qDatExt .= "FROM $cAlfa.SIAI0150 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "CLIIDXXX IN ($cIntermediarios) AND ";
											$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY CLINOMXX";
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
			  							//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

 											if (mysql_num_rows($xDatExt) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "550">
														<tr>
															<td widht = "050" Class = "name"><center>Nit</center></td>
															<td widht = "500" Class = "name"><center>Nombre</center></td>
														</tr>
														<?php while ($xRDE = mysql_fetch_array($xDatExt)) {
															if (mysql_num_rows($xDatExt) > 1) { ?>
																<tr>
																	<td width = "030" Class = "name">
																		<a href = "javascript:
															                   switch ('<?php echo $gFunction ?>') {
															                     case 'cTerIdInt':
												                           case 'cTerNomInt':
												                             if ('<?php  echo $gSecuencia ?>' == ''){
												                               window.opener.document.forms['frgrm']['cTerIdInt'].value   = '<?php echo $xRDE['CLIIDXXX'] ?>';
                                                       window.opener.document.forms['frgrm']['cTerDVInt'].value   = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
                                                       window.opener.document.forms['frgrm']['cTerNomInt'].value  = '<?php echo $xRDE['CLINOMXX'] ?>';
                                                       window.opener.f_CargarTarifasFacturaA();
												                             } else {
																										   window.opener.document.forms['frgrm']['cTerIdInt<?php  echo $gSecuencia ?>'].value   = '<?php echo $xRDE['CLIIDXXX'] ?>';
																										   window.opener.document.forms['frgrm']['cTerDVInt<?php  echo $gSecuencia ?>'].value   = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
																										   window.opener.document.forms['frgrm']['cTerNomInt<?php echo $gSecuencia ?>'].value  = '<?php echo $xRDE['CLINOMXX'] ?>';
																										 }
																									 break;
															                   }
																							 window.close()"><?php echo $xRDE['CLIIDXXX'] ?>
																		</a>
																	</td>
																	<td width = "390" Class = "name"><?php echo $cNombre = ($xRDE['CLINOMXX'] != "") ? $xRDE['CLINOMXX'] : trim($xRDE['CLIAPE1X']." ".$xRDE['CLIAPE2X']." ".$xRDE['CLINOM1X']." ".$xRDE['CLINOM2X']); ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
  																switch ("<?php echo $gFunction ?>") {
        												    case "cTerIdInt":
        												    case "cTerNomInt":
        												      if ('<?php  echo $gSecuencia ?>' == ''){
        												        window.opener.document.forms['frgrm']['cTerIdInt'].value  = '<?php echo $xRDE['CLIIDXXX'] ?>';
                                        window.opener.document.forms['frgrm']['cTerDVInt'].value  = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
                                        window.opener.document.forms['frgrm']['cTerNomInt'].value  = '<?php echo $xRDE['CLINOMXX'] ?>';
                                        window.opener.f_CargarTarifasFacturaA();
        												      } else {
    																	  window.opener.document.forms['frgrm']['cTerIdInt<?php  echo $gSecuencia ?>'].value  = '<?php echo $xRDE['CLIIDXXX'] ?>';
                                        window.opener.document.forms['frgrm']['cTerDVInt<?php  echo $gSecuencia ?>'].value  = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
                                        window.opener.document.forms['frgrm']['cTerNomInt<?php echo $gSecuencia ?>'].value  = '<?php echo $xRDE['CLINOMXX'] ?>';
                                      }
    																break;
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
<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  $qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_NAME = \"SIAI0150\" LIMIT 0,1";
  $xTblCom = f_MySql("SELECT","",$qTblCom,$xConexion01,"");
  $vTblCom = mysql_fetch_array($xTblCom);
//f_Mensaje(__FILE__,__LINE__,$gModo." ~ ".$gFunction." ~ ".$gComCod);

$cBuscar = array("'",chr(13),chr(10),chr(27),chr(9));
$cReempl = array("\'"," "," "," "," ");

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
	  							switch ($gModo) {
	  								case "VALID":
	  									$qDatExt  = "SELECT *, ";
											$qDatExt .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
											$qDatExt .= "FROM $cAlfa.SIAI0150 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "$gTerTip = \"SI\" AND ";
	  									switch ($gFunction) {
	  										case "cTerId":
	  										case "cTerIdB":
													$qDatExt .= "CLIIDXXX = \"$gTerId\" AND ";
													$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY CLIIDXXX LIMIT 0,1";
	  										break;
	  										case "cTerNom":
	  										case "cTerNomB":
													$qDatExt .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) LIKE \"%$gTerNom%\" AND ";
													$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY CLINOMXX";
	  										break;
	  									}
			  							$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
			  							//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

	  									if (mysql_num_rows($xDatExt) == 1) {
												$xRDE = mysql_fetch_array($xDatExt);
												
	  										//Regimen
                        $cTerReg = "";
                        if($xRDE['CLIGCXXX'] == 'SI') {
                          $cTerReg = "G. CONTRIBUYENTE";
                        } elseif($xRDE['CLINRPXX'] == 'SI') {
                          $cTerReg = "NO RESIDENTE";
                        } elseif($xRDE['CLIRECOM'] == 'SI') {
                          $cTerReg = "COMUN";
                        } elseif($xRDE['CLIRESIM'] == 'SI') {
                          $cTerReg = "SIMPLIFICADO";
                        }

                        //Actividad economica
                        $qDatAec = "SELECT * ";
                        $qDatAec.= "FROM $cAlfa.SIAI0101 ";
                        $qDatAec.= "WHERE AECIDXXX =\"{$xRDE['AECIDXXX']}\" LIMIT 0,1";
                        $xDatAec = f_MySql("SELECT","",$qDatAec,$xConexion01,"");
                        $cAecoDes = ""; $cAecoRet="";
                        while($xDA = mysql_fetch_array($xDatAec)){
                        	$cAecoDes = $xDA['AECDESXX'];
                        	$cAecoRet = $xDA['AECRETXX']+0;
                        }

                        switch ($gFunction) {
	                        case "cTerIdB":
	                        case "cTerNomB":
	                          //Buscando palzo del cliente para calcular la fecha de vencimiento
	                          $nPlazo = 0;
												    if ($xRDE['CLIFORPX'] == "CONTADO" || $xRDE['CLIFORPX'] == "") {
												      $nPlazo = 0;
												    } elseif ($xRDE['CLIFORPX'] == "CREDITO") {
												      $nPlazo = ($xRDE['CLIPLAXX'] > 0) ? $xRDE['CLIPLAXX'] : 0;
												    }
												    //Sumando los dias calendario a la fecha actual del sistema
												    $dComVen = date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$nPlazo,date('Y')));
	                        break;
                        }

                        $nExiste = 0; $cComId = "";
                        switch ($gFunction) {
                          case "cTerId":
                          case "cTerNom":
                            //No hace Nada
                          break;
                          case "cTerIdB":
                          case "cTerNomB":
                            //si el comprobante es de ejecucion MANUAL
                            if ($gComTco != "AUTOMATICA") {
                        			// Valido que el comprobante no este duplicado en el sistema.
															 // Valido que el comprobante no este duplicado en el sistema.
															$nAnioActual = date('Y');
															$nAnioAnterior = (($nAnioActual - 2) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($nAnioActual - 2);

															for ($iAnoCsc=$nAnioActual;$iAnoCsc>=$nAnioAnterior;$iAnoCsc--) {
																$qValCsc  = "SELECT comidxxx,comcodxx,comcscxx,comcsc2x ";
																$qValCsc .= "FROM $cAlfa.fcoc$iAnoCsc ";
																$qValCsc .= "WHERE ";
																$qValCsc .= "comidxxx = \"$gComId\"   AND ";
																if($vSysStr['financiero_control_consecutivo_factura_proveedor'] == "SI"){
																	$cTexto = "para este Proveedor ";
																	$qValCsc .= "comcscxx = \"$gComCsc\"  AND ";
																	$qValCsc .= "terid2xx = \"{$xRDE['CLIIDXXX']}\" AND ";
																}else{
																	$qValCsc .= "comcodxx = \"$gComCod\"  AND ";
																	$qValCsc .= "comcscxx = \"$gComCsc\"  AND ";
																	if($vSysStr['financiero_consecutivo_por_proveedor_causaciones_a_terceros'] == "SI"){
																		$cTexto = "para este Proveedor ";
																		$qValCsc .= "terid2xx = \"{$xRDE['CLIIDXXX']}\" AND ";
																	}
																}
																$qValCsc .= "regestxx = \"ACTIVO\" LIMIT 0,1";
																$xValCsc  = f_MySql("SELECT","",$qValCsc,$xConexion01,"");
																//f_Mensaje(__FILE__,__LINE__,$qValCsc." ~ ".mysql_num_rows($xValCsc));
																if (mysql_num_rows($xValCsc) > 0) {
																	$xRVC = mysql_fetch_array($xValCsc);
																	$nExiste = 1;
																	$cComId = "{$xRVC['comidxxx']}-{$xRVC['comcodxx']}-{$xRVC['comcscxx']}-{$xRVC['comcsc2x']}";
																	break;
																}
															}
                            }
                          break;
                        }

                        ?>
												<script languaje = "javascript">
												  switch ("<?php echo $gFunction ?>") {
												    case "cTerId":
												    case "cTerNom":
                              parent.fmwork.document.forms['frgrm']['cTerId'].value   = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['CLIIDXXX']) ?>';
    													parent.fmwork.document.forms['frgrm']['cTerDV'].value   = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
    													parent.fmwork.document.forms['frgrm']['cTerNom'].value  = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['CLINOMXX']) ?>';

    													parent.fmwork.document.forms['frgrm']['cTerReg'].value  = '<?php echo $cTerReg ?>';
    													parent.fmwork.document.forms['frgrm']['cTerArr'].value  = '<?php echo ($xRDE['CLIARRXX'] == 'SI')?"SI":"NO" ?>';
    													parent.fmwork.document.forms['frgrm']['cTerAcr'].value  = '<?php echo ($xRDE['CLIARCRX'] == 'SI')?"SI":"NO" ?>';
    													parent.fmwork.document.forms['frgrm']['cTerArrI'].value = '<?php echo ($xRDE['CLIARRIX'] == 'SI')?"SI":"NO" ?>';
    													parent.fmwork.document.forms['frgrm']['cTerPci'].value  = '<?php echo ($xRDE['CLIPCIXX'] == 'SI')?"SI":"NO" ?>';

    													parent.fmwork.document.forms['frgrm']['cCliAecId'].value  = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['AECIDXXX']) ?>';
    													parent.fmwork.document.forms['frgrm']['cCliAecDes'].value = '<?php echo str_replace($cBuscar, $cReempl, $cAecoDes) ?>';
    													parent.fmwork.document.forms['frgrm']['cCliAecRet'].value = '<?php echo str_replace($cBuscar, $cReempl, $cAecoRet) ?>';

    													//tasa Pactada
    													parent.fmwork.document.forms['frgrm']['cCliTp'].value = '<?php echo (($xRDE['CLITPXXX']+0) > 0) ? ($xRDE['CLITPXXX']+0) : "" ?>';
    													if (parseFloat("<?php echo ($xRDE['CLITPXXX']+0) ?>") > 0) {
    														parent.fmwork.document.forms['frgrm']['cCliTpApl'].value    = 'SI';
    														parent.fmwork.document.forms['frgrm']['cCliTpApl'].checked  = true;
    														parent.fmwork.document.forms['frgrm']['cCliTpApl'].disabled = false;
    													} else {
    														parent.fmwork.document.forms['frgrm']['cCliTpApl'].value    = 'NO';
    														parent.fmwork.document.forms['frgrm']['cCliTpApl'].checked = false;
    														parent.fmwork.document.forms['frgrm']['cCliTpApl'].disabled = true;
    													}
    													if ("<?php echo $cTerReg ?>" == "") {
        													alert("El Cliente No Tiene Parametrizadas las Condicones Tributarias.");
    													}
    													parent.fmwork.f_Cargar_Grillas('<?php echo $gFunction ?>');
												    break;
												    case "cTerIdB":
												    case "cTerNomB":
															parent.fmwork.document.forms['frgrm']['cTerIdB'].value   = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['CLIIDXXX']) ?>';
															parent.fmwork.document.forms['frgrm']['cTerDVB'].value   = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
															parent.fmwork.document.forms['frgrm']['cTerNomB'].value  = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['CLINOMXX']) ?>';

															parent.fmwork.document.forms['frgrm']['cProReg'].value   = '<?php echo $cTerReg ?>';
															parent.fmwork.document.forms['frgrm']['cProRegSt'].value = '<?php echo ($xRDE['CLIREGST'] == 'SI')?"SI":"NO" ?>';
															parent.fmwork.document.forms['frgrm']['cProArAre'].value = '<?php echo ($xRDE['CLIARARE'] == 'SI')?'SI':'NO' ?>';
															parent.fmwork.document.forms['frgrm']['cProArAcr'].value = '<?php echo ($xRDE['CLIARACR'] == 'SI')?'SI':'NO' ?>';
															parent.fmwork.document.forms['frgrm']['cProArIva'].value = '<?php echo ($xRDE['CLIARAIV'] == 'SI')?'SI':'NO' ?>';
															parent.fmwork.document.forms['frgrm']['cProArIca'].value = '<?php echo ($xRDE['CLIARAIC'] == 'SI')?'SI':'NO' ?>';
															parent.fmwork.document.forms['frgrm']['dComVen'].value   = '<?php echo ($dComVen != '')?$dComVen:date('Y-m-d') ?>';

															parent.fmwork.document.forms['frgrm']['cProAecId'].value  = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['AECIDXXX']) ?>';
															parent.fmwork.document.forms['frgrm']['cProAecDes'].value = '<?php echo str_replace($cBuscar, $cReempl, $cAecoDes) ?>';
															parent.fmwork.document.forms['frgrm']['cProAecRet'].value = '<?php echo str_replace($cBuscar, $cReempl, $cAecoRet) ?>';
    													if ("<?php echo $nExiste ?>" == "1") {
    														alert("El Comprobante [<?php echo "$gComId-$gComCod-$gComCsc" ?>] ya existe <?php echo $cTexto ?>en el documento [<?php echo $cComId ?>], Verifique.\n");
    													}
    													if ("<?php echo $cTerReg ?>" == "") {
        												alert("El Proveedor No Tiene Parametrizadas las Condicones Tributarias.");
    													}
    													parent.fmwork.f_Cargar_Grillas('<?php echo $gFunction ?>');
												    break;
												  }
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
		  								$qDatExt  = "SELECT *, ";
											$qDatExt .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
											$qDatExt .= "FROM $cAlfa.SIAI0150 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "$gTerTip = \"SI\" AND ";
											switch ($gFunction) {
	  										case "cTerId":
	  										case "cTerIdB":
													$qDatExt .= "CLIIDXXX LIKE \"%$gTerId%\" AND ";
													$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY CLIIDXXX";
	  										break;
	  										case "cTerNom":
	  										case "cTerNomB":
													$qDatExt .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) LIKE \"%$gTerNom%\" AND ";
													$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY CLINOMXX";
	  										break;
	  									}
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

														  //Regimen
			                        $cTerReg = "";
			                        if($xRDE['CLIGCXXX'] == 'SI') {
			                          $cTerReg = "G. CONTRIBUYENTE";
			                        } elseif($xRDE['CLINRPXX'] == 'SI') {
			                          $cTerReg = "NO RESIDENTE";
			                        } elseif($xRDE['CLIRECOM'] == 'SI') {
			                          $cTerReg = "COMUN";
			                        } elseif($xRDE['CLIRESIM'] == 'SI') {
			                          $cTerReg = "SIMPLIFICADO";
			                        }

			                        //Actividad economica
			                        $qDatAec = "SELECT * ";
			                        $qDatAec.= "FROM $cAlfa.SIAI0101 ";
			                        $qDatAec.= "WHERE AECIDXXX =\"{$xRDE['AECIDXXX']}\" LIMIT 0,1";
			                        $xDatAec = f_MySql("SELECT","",$qDatAec,$xConexion01,"");
			                        $cAecoDes = ""; $cAecoRet="";
			                        while($xDA = mysql_fetch_array($xDatAec)){
			                        	$cAecoDes = $xDA['AECDESXX'];
			                        	$cAecoRet = $xDA['AECRETXX']+0;
			                        }

														 switch ($gFunction) {
			                          case "cTerIdB":
			                          case "cTerNomB":
			                            //Buscando palzo del cliente para calcular la fecha de vencimiento
			                            $nPlazo = 0;
			                            if ($xRDE['CLIFORPX'] == "CONTADO" || $xRDE['CLIFORPX'] == "") {
			                              $nPlazo = 0;
			                            } elseif ($xRDE['CLIFORPX'] == "CREDITO") {
			                              $nPlazo = ($xRDE['CLIPLAXX'] > 0) ? $xRDE['CLIPLAXX'] : 0;
			                            }
			                            //Sumando los dias calendario a la fecha actual del sistema
			                            $dComVen = date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$nPlazo,date('Y')));
			                          break;
			                        }

			                        $nExiste = 0; $cComId = "";
                              switch ($gFunction) {
                                case "cTerId":
                                case "cTerNom":
                                  //No hace Nada
                                break;
                                case "cTerIdB":
                                case "cTerNomB":
                                  //si el comprobante es de ejecucion MANUAL
                                  if ($gComTco != "AUTOMATICA") {
                                    // Valido que el comprobante no este duplicado en el sistema.
                                    $nAnioActual = date('Y');
                                    $nAnioAnterior = (($nAnioActual - 2) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($nAnioActual - 2);

                                    for ($iAnoCsc=$nAnioActual;$iAnoCsc>=$nAnioAnterior;$iAnoCsc--) {
                                      $qValCsc  = "SELECT * ";
                                      $qValCsc .= "FROM $cAlfa.fcoc$iAnoCsc ";
                                      $qValCsc .= "WHERE ";
                                      $qValCsc .= "comidxxx = \"$gComId\"   AND ";
                                      if($vSysStr['financiero_control_consecutivo_factura_proveedor'] == "SI"){
                                        $cTexto = "para este Proveedor ";
                                        $qValCsc .= "comcscxx = \"$gComCsc\"  AND ";
                                        $qValCsc .= "terid2xx = \"{$xRDE['CLIIDXXX']}\" AND ";
                                      }else{
                                        $qValCsc .= "comcodxx = \"$gComCod\"  AND ";
                                        $qValCsc .= "comcscxx = \"$gComCsc\"  AND ";
                                        if($vSysStr['financiero_consecutivo_por_proveedor_causaciones_a_terceros'] == "SI"){
                                          $cTexto = "para este Proveedor ";
                                          $qValCsc .= "terid2xx = \"{$xRDE['CLIIDXXX']}\" AND ";
                                        }
                                      }
                                      $qValCsc .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                                      $xValCsc  = f_MySql("SELECT","",$qValCsc,$xConexion01,"");
                                      //f_Mensaje(__FILE__,__LINE__,$qValCsc." ~ ".mysql_num_rows($xValCsc));
                                      if (mysql_num_rows($xValCsc) > 0) {
                                        $xRVC = mysql_fetch_array($xValCsc);
                                        $nExiste = 1;
                                        $cComId = "{$xRVC['comidxxx']}-{$xRVC['comcodxx']}-{$xRVC['comcscxx']}-{$xRVC['comcsc2x']}";
                                        break;
                                      }
                                    }	
                                  }
                                break;
			                        }
															if (mysql_num_rows($xDatExt) > 1) { ?>
																<tr>
																	<td width = "030" Class = "name">
																	  <a href = "#" onclick = "javascript:switch ('<?php echo $gFunction ?>') {
																																				  case 'cTerId':
																				                                  case 'cTerNom':
																					                                  window.opener.document.forms['frgrm']['cTerId'].value  = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['CLIIDXXX']) ?>';
																					                                  window.opener.document.forms['frgrm']['cTerDV'].value  = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
																					                                  window.opener.document.forms['frgrm']['cTerNom'].value = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['CLINOMXX']) ?>';

																					                                  window.opener.document.forms['frgrm']['cTerReg'].value  = '<?php echo $cTerReg ?>';
																					                                  window.opener.document.forms['frgrm']['cTerArr'].value  = '<?php echo (($xRDE['CLIARRXX'] == 'SI')?'SI':'NO') ?>';
																					                                  window.opener.document.forms['frgrm']['cTerAcr'].value  = '<?php echo (($xRDE['CLIARCRX'] == 'SI')?'SI':'NO') ?>';
																					                                  window.opener.document.forms['frgrm']['cTerArrI'].value = '<?php echo (($xRDE['CLIARRIX'] == 'SI')?'SI':'NO') ?>';
																					                                  window.opener.document.forms['frgrm']['cTerPci'].value  = '<?php echo (($xRDE['CLIPCIXX'] == 'SI')?'SI':'NO') ?>';

																					                                  window.opener.document.forms['frgrm']['cCliAecId'].value  = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['AECIDXXX']) ?>';
																																						window.opener.document.forms['frgrm']['cCliAecDes'].value = '<?php echo str_replace($cBuscar, $cReempl, $cAecoDes) ?>';
																																						window.opener.document.forms['frgrm']['cCliAecRet'].value = '<?php echo str_replace($cBuscar, $cReempl, $cAecoRet) ?>';

																																						//tasa Pactada
																							    													window.opener.document.forms['frgrm']['cCliTp'].value = '<?php echo (($xRDE['CLITPXXX']+0) > 0) ? ($xRDE['CLITPXXX']+0) : "" ?>';
																							    													if (parseFloat('<?php echo ($xRDE['CLITPXXX']+0) ?>') > 0) {
																							    														window.opener.document.forms['frgrm']['cCliTpApl'].value    = 'SI';
																							    														window.opener.document.forms['frgrm']['cCliTpApl'].checked  = true;
																							    														window.opener.document.forms['frgrm']['cCliTpApl'].disabled = false;
																							    													} else {
																							    														window.opener.document.forms['frgrm']['cCliTpApl'].value    = 'NO';
																							    														window.opener.document.forms['frgrm']['cCliTpApl'].checked = false;
																							    														window.opener.document.forms['frgrm']['cCliTpApl'].disabled = true;
																							    													}
																							    													if ('<?php echo $cTerReg ?>' == '') {
																							        													alert('El Cliente No Tiene Parametrizadas las Condicones Tributarias.');
																							    													}
																					                                  window.opener.f_Cargar_Grillas('<?php echo $gFunction ?>');
																				                                  break;
																				                                  case 'cTerIdB':
																				                                  case 'cTerNomB':
																					                                  window.opener.document.forms['frgrm']['cTerIdB'].value   = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['CLIIDXXX']) ?>';
																					                                  window.opener.document.forms['frgrm']['cTerDVB'].value   = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
																					                                  window.opener.document.forms['frgrm']['cTerNomB'].value  = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['CLINOMXX']) ?>';

																					                                  window.opener.document.forms['frgrm']['cProReg'].value   = '<?php echo $cTerReg ?>';
																																						window.opener.document.forms['frgrm']['cProRegSt'].value  = '<?php echo ($xRDE['CLIREGST'] == 'SI')?"SI":"NO" ?>';
																					                                  window.opener.document.forms['frgrm']['cProArAre'].value = '<?php echo ($xRDE['CLIARARE'] == 'SI')?'SI':'NO' ?>';
																					                                  window.opener.document.forms['frgrm']['cProArAcr'].value = '<?php echo ($xRDE['CLIARACR'] == 'SI')?'SI':'NO' ?>';
																					                                  window.opener.document.forms['frgrm']['cProArIva'].value = '<?php echo ($xRDE['CLIARAIV'] == 'SI')?'SI':'NO' ?>';
																					                                  window.opener.document.forms['frgrm']['cProArIca'].value = '<?php echo ($xRDE['CLIARAIC'] == 'SI')?'SI':'NO' ?>';
																					                                  window.opener.document.forms['frgrm']['dComVen'].value  = '<?php echo ($dComVen != '')?$dComVen:date('Y-m-d') ?>';

																					                                  window.opener.document.forms['frgrm']['cProAecId'].value  = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['AECIDXXX']) ?>';
																																						window.opener.document.forms['frgrm']['cProAecDes'].value = '<?php echo str_replace($cBuscar, $cReempl, $cAecoDes) ?>';
																																						window.opener.document.forms['frgrm']['cProAecRet'].value = '<?php echo str_replace($cBuscar, $cReempl, $cAecoRet) ?>';
																																						if ('<?php echo $nExiste ?>' == '1') {
																							    														alert('El Comprobante [<?php echo "$gComId-$gComCod-$gComCsc" ?>] ya existe <?php echo $cTexto ?>en el documento [<?php echo $cComId ?>], Verifique.\n');
																							    													}
																																						if ('<?php echo $cTerReg ?>' == '') {
																							        													alert('El Proveedor No Tiene Parametrizadas las Condicones Tributarias.');
																							    													}
																					                                  window.opener.f_Cargar_Grillas('<?php echo $gFunction ?>');
																				                                  break;
																			                                  }
																			                                  window.close();"><?php echo $xRDE['CLIIDXXX'] ?>
																		</a>
																	</td>
																	<td width = "390" Class = "name"><?php echo $xRDE['CLINOMXX'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																  switch ('<?php echo $gFunction ?>') {
																	  case 'cTerId':
	                                  case 'cTerNom':
		                                  window.opener.document.forms['frgrm']['cTerId'].value  = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['CLIIDXXX']) ?>';
		                                  window.opener.document.forms['frgrm']['cTerDV'].value  = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
		                                  window.opener.document.forms['frgrm']['cTerNom'].value = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['CLINOMXX']) ?>';

		                                  window.opener.document.forms['frgrm']['cTerReg'].value  = '<?php echo $cTerReg ?>';
		                                  window.opener.document.forms['frgrm']['cTerArr'].value  = '<?php echo (($xRDE['CLIARRXX'] == 'SI')?'SI':'NO') ?>';
		                                  window.opener.document.forms['frgrm']['cTerAcr'].value  = '<?php echo (($xRDE['CLIARCRX'] == 'SI')?'SI':'NO') ?>';
		                                  window.opener.document.forms['frgrm']['cTerArrI'].value = '<?php echo (($xRDE['CLIARRIX'] == 'SI')?'SI':'NO') ?>';
		                                  window.opener.document.forms['frgrm']['cTerPci'].value  = '<?php echo (($xRDE['CLIPCIXX'] == 'SI')?'SI':'NO') ?>';

		                                  window.opener.document.forms['frgrm']['cCliAecId'].value  = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['AECIDXXX']) ?>';
																			window.opener.document.forms['frgrm']['cCliAecDes'].value = '<?php echo str_replace($cBuscar, $cReempl, $cAecoDes) ?>';
																			window.opener.document.forms['frgrm']['cCliAecRet'].value = '<?php echo str_replace($cBuscar, $cReempl, $cAecoRet) ?>';

																			//tasa Pactada
				    													window.opener.document.forms['frgrm']['cCliTp'].value = '<?php echo (($xRDE['CLITPXXX']+0) > 0) ? ($xRDE['CLITPXXX']+0) : "" ?>';
				    													if (parseFloat('<?php echo ($xRDE['CLITPXXX']+0) ?>') > 0) {
				    														window.opener.document.forms['frgrm']['cCliTpApl'].value    = 'SI';
				    														window.opener.document.forms['frgrm']['cCliTpApl'].checked  = true;
				    														window.opener.document.forms['frgrm']['cCliTpApl'].disabled = false;
				    													} else {
				    														window.opener.document.forms['frgrm']['cCliTpApl'].value    = 'NO';
				    														window.opener.document.forms['frgrm']['cCliTpApl'].checked = false;
				    														window.opener.document.forms['frgrm']['cCliTpApl'].disabled = true;
				    													}
				    													if ("<?php echo $cTerReg ?>" == "") {
				        													alert("El Cliente No Tiene Parametrizadas las Condicones Tributarias.");
				    													}
		                                  window.opener.f_Cargar_Grillas('<?php echo $gFunction ?>');
	                                  break;
	                                  case 'cTerIdB':
	                                  case 'cTerNomB':
																		window.opener.document.forms['frgrm']['cTerIdB'].value   = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['CLIIDXXX']) ?>';
																		window.opener.document.forms['frgrm']['cTerDVB'].value   = '<?php echo f_Digito_Verificacion($xRDE['CLIIDXXX']) ?>';
																		window.opener.document.forms['frgrm']['cTerNomB'].value  = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['CLINOMXX']) ?>';

																		window.opener.document.forms['frgrm']['cProReg'].value   = '<?php echo $cTerReg ?>';
																		window.opener.document.forms['frgrm']['cProRegSt'].value  = '<?php echo ($xRDE['CLIREGST'] == 'SI')?"SI":"NO" ?>';
																		window.opener.document.forms['frgrm']['cProArAre'].value = '<?php echo ($xRDE['CLIARARE'] == 'SI')?'SI':'NO' ?>';
																		window.opener.document.forms['frgrm']['cProArAcr'].value = '<?php echo ($xRDE['CLIARACR'] == 'SI')?'SI':'NO' ?>';
																		window.opener.document.forms['frgrm']['cProArIva'].value = '<?php echo ($xRDE['CLIARAIV'] == 'SI')?'SI':'NO' ?>';
																		window.opener.document.forms['frgrm']['cProArIca'].value = '<?php echo ($xRDE['CLIARAIC'] == 'SI')?'SI':'NO' ?>';
																		window.opener.document.forms['frgrm']['dComVen'].value  = '<?php echo ($dComVen != '')?$dComVen:date('Y-m-d') ?>';

																		window.opener.document.forms['frgrm']['cProAecId'].value  = '<?php echo str_replace($cBuscar, $cReempl, $xRDE['AECIDXXX']) ?>';
																		window.opener.document.forms['frgrm']['cProAecDes'].value = '<?php echo str_replace($cBuscar, $cReempl, $cAecoDes) ?>';
																		window.opener.document.forms['frgrm']['cProAecRet'].value = '<?php echo str_replace($cBuscar, $cReempl, $cAecoRet) ?>';

																			if ("<?php echo $nExiste ?>" == "1") {
				    														alert("El Comprobante [<?php echo "$gComId-$gComCod-$gComCsc" ?>] ya existe <?php echo $cTexto ?>en el documento [<?php echo $cComId ?>], Verifique.\n");
				    													}

																			if ("<?php echo $cTerReg ?>" == "") {
					        											alert("El Proveedor No Tiene Parametrizadas las Condicones Tributarias.");
					    												}
		                                  window.opener.f_Cargar_Grillas('<?php echo $gFunction ?>');
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
                          switch ("<?php echo $gFunction ?>") {
                            case "cTerId":
                            case "cTerNom":
                          	  window.opener.document.forms['frgrm']['cTerId'].value  = '';
                              window.opener.document.forms['frgrm']['cTerDV'].value  = '';
                              window.opener.document.forms['frgrm']['cTerNom'].value = '';

                              window.opener.document.forms['frgrm']['cTerReg'].value  = '';
                              window.opener.document.forms['frgrm']['cTerArr'].value  = '';
                              window.opener.document.forms['frgrm']['cTerAcr'].value  = '';
                              window.opener.document.forms['frgrm']['cTerArrI'].value = '';
                              window.opener.document.forms['frgrm']['cTerPci'].value  = '';

                              window.opener.document.forms['frgrm']['cCliAecId'].value  = '';
															window.opener.document.forms['frgrm']['cCliAecDes'].value = '';
															window.opener.document.forms['frgrm']['cCliAecRet'].value = '';

															//tasa Pactada
	    												window.opener.document.forms['frgrm']['cCliTp'].value       = '';
	    												window.opener.document.forms['frgrm']['cCliTpApl'].value    = 'NO';
	    												window.opener.document.forms['frgrm']['cCliTpApl'].checked  = false;
	    												window.opener.document.forms['frgrm']['cCliTpApl'].disabled = true;

                              window.opener.document.forms['frgrm']['dComVen'].value  = '<?php echo date('Y-m-d') ?>';
                            break;
                            case "cTerIdB":
                            case "cTerNomB":
                          	  window.opener.document.forms['frgrm']['cTerIdB'].value   = '';
                              window.opener.document.forms['frgrm']['cTerDVB'].value   = '';
                              window.opener.document.forms['frgrm']['cTerNomB'].value  = '';

                              window.opener.document.forms['frgrm']['cProReg'].value   = '';
															window.opener.document.forms['frgrm']['cProRegSt'].value   = '';
                              window.opener.document.forms['frgrm']['cProArAre'].value = '';
                              window.opener.document.forms['frgrm']['cProArAcr'].value = '';
                              window.opener.document.forms['frgrm']['cProArIva'].value = '';
                              window.opener.document.forms['frgrm']['cProArIca'].value = '';

                              window.opener.document.forms['frgrm']['cProAecId'].value  = '';
															window.opener.document.forms['frgrm']['cProAecDes'].value = '';
															window.opener.document.forms['frgrm']['cProAecRet'].value = '';
                            break;
                          }
                          window.close();
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
	f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos, Verifique.");
} ?>
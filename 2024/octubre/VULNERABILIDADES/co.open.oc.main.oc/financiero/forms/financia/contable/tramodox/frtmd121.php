<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
?>
<!--
	Los Parametros que Recibo son :
	$gSearch = Un dato para filtrar la consulta con el WHERE
	$gFields = A que campos del formulario voy a devolver los datos
	$gWhat   = Que voy a hacer, una ventana o una validacion -->
<?php
  if ($gWhat != "" && $gFunction != "") { ?>
  	<html>
  		<head>
  			<title>Documentos de Comercio Exterior</title>
  			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
  			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
  			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
  			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
  			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
  	   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
  	  </head>
  	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
  	  <center>
  			<table border ="0" cellpadding="0" cellspacing="0" width="300">
  				<tr>
  					<td>
  						<fieldset>
  			   			<legend>Documentos de Comercio Exterior</legend>
  	  					<form name = "frnav" action = "" method = "post" target = "fmpro">
  	  						<?php switch ($gWhat) {
  	  							case "VALID":
  	  								/* Primero lo Busco en OPENCOMEX */
                      $qDatDoi  = "SELECT $cAlfa.sys00121.sucidxxx, $cAlfa.sys00121.doctipxx, $cAlfa.sys00121.docidxxx, $cAlfa.sys00121.docsufxx, ";
                      $qDatDoi .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) != \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) AS clinomxx ";
                      $qDatDoi .= "FROM $cAlfa.sys00121 ";
                      $qDatDoi .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
			  							$qDatDoi .= "WHERE ";
                      $qDatDoi .= "$cAlfa.sys00121.docidxxx = \"$gDocNro\"";
                      switch ($gFunction) {
                        case 'cDocNroDes':
                        case 'cDocNroOri':
                          $qDatDoi .= " AND $cAlfa.sys00121.regestxx = \"ACTIVO\" ";
                        default:
                          //No hace nada
                        break;
                      }
                      $xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
	  									// f_Mensaje(__FILE__,__LINE__,$qDatDoi."~".mysql_num_rows($xDatDoi));

  	  								if (mysql_num_rows($xDatDoi) == 1) {
  											$xMtzDo = mysql_fetch_array($xDatDoi); ?>
  	  									<script languaje = "javascript">
                          switch ('<?php echo $gFunction ?>') {
                            case 'cDocNroDes':
                              parent.fmwork.document.forms['frnav']['cSucIdDes'].value   = "<?php echo $xMtzDo['sucidxxx']?>";
                              parent.fmwork.document.forms['frnav']['cDocTipDes'].value  = "<?php echo $xMtzDo['doctipxx']?>";
                              parent.fmwork.document.forms['frnav']['cDocNroDes'].value  = "<?php echo $xMtzDo['docidxxx']?>";
                              parent.fmwork.document.forms['frnav']['cDocSufDes'].value  = "<?php echo $xMtzDo['docsufxx']?>";
                              parent.fmwork.document.forms['frnav']['cCliNomDes'].value  = "<?php echo $xMtzDo['clinomxx']?>";
                            break;
                            case 'cDocNroOri':
                              parent.fmwork.document.forms['frnav']['cSucIdOri'].value   = "<?php echo $xMtzDo['sucidxxx']?>";
                              parent.fmwork.document.forms['frnav']['cDocTipOri'].value  = "<?php echo $xMtzDo['doctipxx']?>";
                              parent.fmwork.document.forms['frnav']['cDocNroOri'].value  = "<?php echo $xMtzDo['docidxxx']?>";
                              parent.fmwork.document.forms['frnav']['cDocSufOri'].value  = "<?php echo $xMtzDo['docsufxx']?>";
                              parent.fmwork.document.forms['frnav']['cCliNomOri'].value  = "<?php echo $xMtzDo['clinomxx']?>";
                              parent.fmwork.fnCargarPagos();
                            break;
                            case 'cDocIdDes':
                              parent.fmwork.document.forms['frnav']['cSucIdDes'].value   = "<?php echo $xMtzDo['sucidxxx']?>";
                              parent.fmwork.document.forms['frnav']['cDocTipDes'].value  = "<?php echo $xMtzDo['doctipxx']?>";
                              parent.fmwork.document.forms['frnav']['cDocIdDes'].value  = "<?php echo $xMtzDo['docidxxx']?>";
                              parent.fmwork.document.forms['frnav']['cDocSufDes'].value  = "<?php echo $xMtzDo['docsufxx']?>";
                            break;
                            case 'cDocIdOri':
                              parent.fmwork.document.forms['frnav']['cSucIdOri'].value   = "<?php echo $xMtzDo['sucidxxx']?>";
                              parent.fmwork.document.forms['frnav']['cDocTipOri'].value  = "<?php echo $xMtzDo['doctipxx']?>";
                              parent.fmwork.document.forms['frnav']['cDocIdOri'].value  = "<?php echo $xMtzDo['docidxxx']?>";
                              parent.fmwork.document.forms['frnav']['cDocSufOri'].value  = "<?php echo $xMtzDo['docsufxx']?>";
                            break;
                          }
  	  									</script>
  	  								<?php } else { ?>
  	  								  <script languaje = "javascript">
  	     	    					  parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
  												window.close();
  											</script>
  	  								<?php }
  	  							break;
  	  							case "WINDOW":
                      $qDatDoi  = "SELECT $cAlfa.sys00121.sucidxxx, $cAlfa.sys00121.doctipxx, $cAlfa.sys00121.docidxxx, $cAlfa.sys00121.docsufxx, ";
                      $qDatDoi .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) != \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) AS clinomxx ";
                      $qDatDoi .= "FROM $cAlfa.sys00121 ";
                      $qDatDoi .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
                      $qDatDoi .= "WHERE ";
                      $qDatDoi .= "$cAlfa.sys00121.docidxxx LIKE \"%$gDocNro%\"";
                      switch ($gFunction) {
                        case 'cDocNroDes':
                        case 'cDocNroOri':
                          $qDatDoi .= " AND $cAlfa.sys00121.regestxx = \"ACTIVO\" ";
                        default:
                          //No hace nada
                        break;
                      }
	  									$xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
	  									// f_Mensaje(__FILE__,__LINE__,$qDatDoi."~".mysql_num_rows($xDatDoi));

	  									if (mysql_num_rows($xDatDoi) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "300">
														<tr>
															<td widht = "050" Class = "name"><center>Suc.</center></td>
															<td widht = "050" Class = "name"><center>Tipo</center></td>
															<td widht = "150" Class = "name"><center>Documento</center></td>
															<td widht = "050" Class = "name"><center>Sufijo</center></td>
														</tr>
														<?php while ($xRDoc = mysql_fetch_array($xDatDoi)) { ?>
															<tr>
																<td width = "050" class= "name"><?php echo $xRDoc['sucidxxx'] ?></td>
																<td width = "050" class= "name"><?php echo $xRDoc['doctipxx'] ?></td>
																<td width = "150" class= "name">
																	<a href = "javascript:switch ('<?php echo $gFunction ?>') {
                                                          case 'cDocNroDes':
                                                            window.opener.document.forms['frnav']['cSucIdDes'].value   ='<?php echo $xRDoc['sucidxxx'] ?>';
                                                            window.opener.document.forms['frnav']['cDocTipDes'].value  ='<?php echo $xRDoc['doctipxx'] ?>';
                                                            window.opener.document.forms['frnav']['cDocNroDes'].value  ='<?php echo $xRDoc['docidxxx'] ?>';
                                                            window.opener.document.forms['frnav']['cDocSufDes'].value  ='<?php echo $xRDoc['docsufxx'] ?>';
                                                            window.opener.document.forms['frnav']['cCliNomDes'].value  ='<?php echo $xRDoc['clinomxx'] ?>';
                                                          break;
                                                          case 'cDocNroOri':
                                                            window.opener.document.forms['frnav']['cSucIdOri'].value   ='<?php echo $xRDoc['sucidxxx'] ?>';
                                                            window.opener.document.forms['frnav']['cDocTipOri'].value  ='<?php echo $xRDoc['doctipxx'] ?>';
                                                            window.opener.document.forms['frnav']['cDocNroOri'].value  ='<?php echo $xRDoc['docidxxx'] ?>';
                                                            window.opener.document.forms['frnav']['cDocSufOri'].value  ='<?php echo $xRDoc['docsufxx'] ?>';
                                                            window.opener.document.forms['frnav']['cCliNomOri'].value  ='<?php echo $xRDoc['clinomxx'] ?>';
                                                            window.opener.fnCargarPagos();
                                                          break;
                                                          case 'cDocIdDes':
                                                            window.opener.document.forms['frnav']['cSucIdDes'].value   ='<?php echo $xRDoc['sucidxxx'] ?>';
                                                            window.opener.document.forms['frnav']['cDocTipDes'].value  ='<?php echo $xRDoc['doctipxx'] ?>';
                                                            window.opener.document.forms['frnav']['cDocIdDes'].value  ='<?php echo $xRDoc['docidxxx'] ?>';
                                                            window.opener.document.forms['frnav']['cDocSufDes'].value  ='<?php echo $xRDoc['docsufxx'] ?>';
                                                          break;
                                                          case 'cDocIdOri':
                                                            window.opener.document.forms['frnav']['cSucIdOri'].value   ='<?php echo $xRDoc['sucidxxx'] ?>';
                                                            window.opener.document.forms['frnav']['cDocTipOri'].value  ='<?php echo $xRDoc['doctipxx'] ?>';
                                                            window.opener.document.forms['frnav']['cDocIdOri'].value  ='<?php echo $xRDoc['docidxxx'] ?>';
                                                            window.opener.document.forms['frnav']['cDocSufOri'].value  ='<?php echo $xRDoc['docsufxx'] ?>';
                                                          break;
                                                        }
																                        window.close()"><?php echo $xRDoc['docidxxx'] ?></a></td>
																<td width = "050" class= "name"><?php echo $xRDoc['docsufxx'] ?></td>
															</tr>
														<?php } ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");?>
  	  								  <script languaje = "javascript">
                          switch ('<?php echo $gFunction ?>') {
                            case 'cDocNroDes':
                              window.opener.document.forms['frnav']['cSucIdDes'].value   ='';
                              window.opener.document.forms['frnav']['cDocTipDes'].value  ='';
                              window.opener.document.forms['frnav']['cDocNroDes'].value  ='';
                              window.opener.document.forms['frnav']['cDocSufDes'].value  ='';
                              window.opener.document.forms['frnav']['cCliNomDes'].value  ='';
                            break;
                            case 'cDocNroOri':
                              window.opener.document.forms['frnav']['cSucIdOri'].value   ='';
                              window.opener.document.forms['frnav']['cDocTipOri'].value  ='';
                              window.opener.document.forms['frnav']['cDocNroOri'].value  ='';
                              window.opener.document.forms['frnav']['cDocSufOri'].value  ='';
                              window.opener.document.forms['frnav']['cCliNomOri'].value  ='';
                              window.opener.fnCargarPagos();
                            break;
                            case 'cDocIdDes':
                              window.opener.document.forms['frnav']['cSucIdDes'].value   ='';
                              window.opener.document.forms['frnav']['cDocTipDes'].value  ='';
                              window.opener.document.forms['frnav']['cDocIdDes'].value  ='';
                              window.opener.document.forms['frnav']['cDocSufDes'].value  ='';
                            break;
                            case 'cDocIdOri':
                              window.opener.document.forms['frnav']['cSucIdOri'].value   ='';
                              window.opener.document.forms['frnav']['cDocTipOri'].value  ='';
                              window.opener.document.forms['frnav']['cDocIdOri'].value  ='';
                              window.opener.document.forms['frnav']['cDocSufOri'].value  ='';
                            break;
													window.close();
  											</script>
  	  								<?php
	  									}
  	  							break;
  	  						}?>
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
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
  	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
  	  						<input type = "hidden" name = "vMatriz"    value = "">
  	  						<?php switch ($gWhat) {
  	  							case "VALID":
  	  								/* Primero lo Busco en OPENCOMEX */
  	  								$qDatDoi  = "SELECT $cAlfa.sys00121.*, ";
  	  								$qDatDoi .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS clinomxx ";
			  							$qDatDoi .= "FROM $cAlfa.sys00121 ";
			  							$qDatDoi .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
			  							$qDatDoi .= "WHERE "; 
			  							switch ($gFunction) {
												case "cDocId":
													$qDatDoi .= "$cAlfa.sys00121.docidxxx = \"$gDocId\" AND ";
													$qDatDoi .= "$cAlfa.sys00121.doctexpt = \"\" AND ";
													$qDatDoi .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" ";
												break;
												default:
													$qDatDoi .= "$cAlfa.sys00121.docidxxx = \"$gDocNro\" AND ";
													$qDatDoi .= "$cAlfa.sys00121.docobept <> \"\" AND ";
													$qDatDoi .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" ";
												break;	
											}			  							
	  									$xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatDoi);

  	  								if (mysql_num_rows($xDatDoi) == 1) {
  											$xMtzDo = mysql_fetch_array($xDatDoi);  											
  											switch ($gFunction) {
  												case "cDocId": ?>
  													<script languaje = "javascript">
  														parent.fmwork.document.forms['frgrm']['cSucId'].value     = "<?php echo $xMtzDo['sucidxxx']?>";
  													  parent.fmwork.document.forms['frgrm']['cDocId'].value     = "<?php echo $xMtzDo['docidxxx']?>";
  													  parent.fmwork.document.forms['frgrm']['cDocSuf'].value    = "<?php echo $xMtzDo['docsufxx']?>";
  													  parent.fmwork.document.forms['frgrm']['cDocTip'].value    = "<?php echo $xMtzDo['doctipxx']?>";
  													  parent.fmwork.document.forms['frgrm']['cCliId'].value     = "<?php echo $xMtzDo['cliidxxx']?>";
  													  parent.fmwork.document.forms['frgrm']['cCliDv'].value     = "<?php echo f_Digito_Verificacion($xMtzDo['cliidxxx'])?>";
  													  parent.fmwork.document.forms['frgrm']['cCliNom'].value    = "<?php echo $xMtzDo['clinomxx']?>";
  													  parent.fmwork.f_Carga_Pagos("<?php echo $xMtzDo['sucidxxx']?>", "<?php echo $xMtzDo['docidxxx']?>", "<?php echo $xMtzDo['docsufxx']?>")
  													  </script>
  												<?php break;
  												default: ?>
  													<script languaje = "javascript">
  														parent.fmwork.document.forms['frgrm']['cSucId'].value     = "<?php echo $xMtzDo['sucidxxx']?>";
  													  parent.fmwork.document.forms['frgrm']['cDocNro'].value    = "<?php echo $xMtzDo['docidxxx']?>";
  													  parent.fmwork.document.forms['frgrm']['cDocSuf'].value    = "<?php echo $xMtzDo['docsufxx']?>";
  													</script>
  												<?php break;
  											}
  										} else { ?>
  	  								  <script languaje = "javascript">
  	     	    					  parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
  												window.close();
  											</script>
  	  								<?php }
  	  							break;
  	  							case "WINDOW":
  	  									/* Traigo DO's de Importacion de OPENCOMEX */
											$qDatDoi  = "SELECT $cAlfa.sys00121.*, ";
											$qDatDoi .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS clinomxx ";
			  							$qDatDoi .= "FROM $cAlfa.sys00121 ";
			  							$qDatDoi .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
			  							$qDatDoi .= "WHERE ";
			  							switch ($gFunction) {
			  								case "cDocId":
			  									break;
			  								default:
			  									break;
			  							}
			  							
			  							switch ($gFunction) {
			  								case "cDocId":
			  									$qDatDoi .= "$cAlfa.sys00121.docidxxx LIKE \"%$gDocId%\" AND ";
					  							$qDatDoi .= "$cAlfa.sys00121.docsufxx LIKE \"%$gDocSuf%\" AND ";
					  							$qDatDoi .= "$cAlfa.sys00121.doctexpt = \"\" AND ";
					  							$qDatDoi .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" ";
			  								break;
			  								default:
			  									$qDatDoi .= "$cAlfa.sys00121.docidxxx LIKE \"%$gDocNro%\" AND ";
			  									$qDatDoi .= "$cAlfa.sys00121.docobept <> \"\" AND ";
			  									$qDatDoi .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" ";
			  								break;
			  							}
			  							$xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");

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
																
																	<?php switch ($gFunction) {
					  										 		case "cDocId": ?>
					  										 			<a href = "javascript:window.opener.document.forms['frgrm']['cSucId'].value     ='<?php echo $xRDoc['sucidxxx'] ?>';
																  										 			window.opener.document.forms['frgrm']['cDocId'].value     ='<?php echo $xRDoc['docidxxx'] ?>';
																  										 			window.opener.document.forms['frgrm']['cDocSuf'].value    ='<?php echo $xRDoc['docsufxx'] ?>';
																  										 			window.opener.document.forms['frgrm']['cDocTip'].value    ='<?php echo $xRDoc['doctipxx'] ?>';
																  										 			window.opener.document.forms['frgrm']['cCliId'].value     ='<?php echo $xRDoc['cliidxxx'] ?>';
																  										 			window.opener.document.forms['frgrm']['cCliDv'].value     ='<?php echo f_Digito_Verificacion($xRDoc['cliidxxx']) ?>';
																  										 			window.opener.document.forms['frgrm']['cCliNom'].value    ='<?php echo $xRDoc['clinomxx'] ?>';
																  										 			window.opener.f_Carga_Pagos('<?php echo $xRDoc['sucidxxx'] ?>', '<?php echo $xRDoc['docidxxx'] ?>', '<?php echo $xRDoc['docsufxx'] ?>');
																  										 			window.close()">
					  										 		<?php break; 
					  										  	default: ?>
					  										  		<a href = "javascript:window.opener.document.forms['frgrm']['cSucId'].value     ='<?php echo $xRDoc['sucidxxx'] ?>';
																  										 			window.opener.document.forms['frgrm']['cDocNro'].value    ='<?php echo $xRDoc['docidxxx'] ?>';
																  										 			window.opener.document.forms['frgrm']['cDocSuf'].value    ='<?php echo $xRDoc['docsufxx'] ?>';
																  										 			window.close()">
					  										  	<?php break;
					  										  } ?>
																	<?php echo $xRDoc['docidxxx'] ?></a></td>
																<td width = "050" class= "name"><?php echo $xRDoc['docsufxx'] ?></td>
															</tr>
														<?php } ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
	  										switch ($gFunction) {
  										 		case "cDocId": ?>
  										  		<script languaje = "javascript">
  										  			window.opener.document.forms['frgrm']['cSucId'].value     = "";
  										  			window.opener.document.forms['frgrm']['cDocId'].value     = "";
  										  			window.opener.document.forms['frgrm']['cDocSuf'].value    = "";
  										  			window.opener.document.forms['frgrm']['cDocTip'].value    = "";
  										  			window.opener.document.forms['frgrm']['cCliId'].value     = "";
  										  			window.opener.document.forms['frgrm']['cCliDv'].value     = "";
  										  			window.opener.document.forms['frgrm']['cCliNom'].value    = "";
  										  		</script>
  										  	<?php break;
  										  	default: ?>
  										  		<script languaje = "javascript">
  										  			window.opener.document.forms['frgrm']['cSucId'].value     = "";
  										  			window.opener.document.forms['frgrm']['cDocNro'].value    = "";
  										  			window.opener.document.forms['frgrm']['cDocSuf'].value    = "";
  										  		</script>
  										  	<?php break;
  										  }
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
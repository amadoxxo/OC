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
  	  						<input type = "hidden" name = "cIteration" value = "<?php echo $gIteration ?>">
  	  						<input type = "hidden" name = "vMatriz"    value = "">
  	  						<?php switch ($gWhat) {
  	  							case "VALID":
  	  								/* Primero lo Busco en OPENCOMEX */
  	  								$qDatDoi  = "SELECT * ";
			  							$qDatDoi .= "FROM $cAlfa.sys00121 ";
			  							$qDatDoi .= "WHERE docidxxx = \"$gDocNro\" ";
			  							//$qDatDoi .= "WHERE docidxxx = \"$gDocNro\" AND docsufxx = \"$gDocSuf\" ";
			  							//$qDatDoi .= "WHERE docidxxx = \"$gDocNro\" ";
	  									$xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatDoi);

  	  								if (mysql_num_rows($xDatDoi) == 1) {
  											$xMtzDo = mysql_fetch_array($xDatDoi); ?>
  	  									<script languaje = "javascript">
  	  										parent.fmwork.document.forms['frgrm']['cComIdDo'].value = "<?php echo $xMtzDo['comidxxx']?>";
  	  										parent.fmwork.document.forms['frgrm']['cComCodDo'].value= "<?php echo $xMtzDo['comcodxx']?>";
  	  										parent.fmwork.document.forms['frgrm']['cSucId'].value   = "<?php echo $xMtzDo['sucidxxx']?>";
  	  										parent.fmwork.document.forms['frgrm']['cDocTip'].value  = "<?php echo $xMtzDo['doctipxx']?>";
  	  										parent.fmwork.document.forms['frgrm']['cDocNro'].value  = "<?php echo $xMtzDo['docidxxx']?>";
  	  										parent.fmwork.document.forms['frgrm']['cDocSuf'].value  = "<?php echo $xMtzDo['docsufxx']?>";
  	  										parent.fmwork.document.forms['frgrm']['cPucId'].value   = "<?php echo $xMtzDo['pucidxxx']?>";
  	  										parent.fmwork.document.forms['frgrm']['cCcoId'].value   = "<?php echo $xMtzDo['ccoidxxx']?>";
  	  										parent.fmwork.document.forms['frgrm']['cCliId'].value   = "<?php echo $xMtzDo['cliidxxx']?>";
  	  										parent.fmwork.document.forms['frgrm']['dRegFCre'].value = "<?php echo $xMtzDo['regfcrex']?>";
  	  									</script>
  	  								<?php } else { ?>
  	  								  <script languaje = "javascript">
  	     	    					  parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
  												window.close();
  											</script>
  	  								<?php }
  	  							break;
  	  							case "WINDOW":
  									/* Traigo DO's de Importacion de OPENCOMEX */
									$qDatDoi  = "SELECT * ";
		  							$qDatDoi .= "FROM $cAlfa.sys00121 ";
		  							$qDatDoi .= "WHERE docidxxx LIKE \"$gDocNro\" AND docsufxx LIKE \"%$gDocSuf%\" ";
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
															<a href = "javascript:window.opener.document.forms['frgrm']['cComIdDo'].value ='<?php echo $xRDoc['comidxxx'] ?>';
																										window.opener.document.forms['frgrm']['cComCodDo'].value='<?php echo $xRDoc['comcodxx'] ?>';
																										window.opener.document.forms['frgrm']['cSucId'].value   ='<?php echo $xRDoc['sucidxxx'] ?>';
																										window.opener.document.forms['frgrm']['cDocTip'].value  ='<?php echo $xRDoc['doctipxx'] ?>';
																										window.opener.document.forms['frgrm']['cDocNro'].value  ='<?php echo $xRDoc['docidxxx'] ?>';
																										window.opener.document.forms['frgrm']['cDocSuf'].value  ='<?php echo $xRDoc['docsufxx'] ?>';
																										window.opener.document.forms['frgrm']['cPucId'].value   ='<?php echo $xRDoc['pucidxxx'] ?>';
																										window.opener.document.forms['frgrm']['cCcoId'].value   ='<?php echo $xRDoc['ccoidxxx'] ?>';
																										window.opener.document.forms['frgrm']['cCliId'].value   ='<?php echo $xRDoc['cliidxxx'] ?>';
																										window.opener.document.forms['frgrm']['dRegFCre'].value ='<?php echo $xRDoc['regfcrex'] ?>';
																			window.close()"><?php echo $xRDoc['docidxxx'] ?></a></td>
														<td width = "050" class= "name"><?php echo $xRDoc['docsufxx'] ?></td>
													</tr>
												<?php } ?>
											</table>
										</center>
  									<?php	} else {
  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
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
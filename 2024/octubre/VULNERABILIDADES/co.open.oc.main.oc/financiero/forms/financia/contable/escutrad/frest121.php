<?php
  namespace openComex;
?>
<!--
	Los Parametros que Recibo son :
	$gSearch = Un dato para filtrar la consulta con el WHERE
	$gFields = A que campos del formulario voy a devolver los datos
	$gWhat   = Que voy a hacer, una ventana o una validacion -->

<?php
  include("../../../../libs/php/utility.php");

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
			  							$qDatDoi .= "WHERE ";
			  							$qDatDoi .= "docidxxx = \"$gDocNro\" ";
	  									$xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatDoi."~".mysql_num_rows($xDatDoi));

  	  								if (mysql_num_rows($xDatDoi) == 1) {
  											$xMtzDo = mysql_fetch_array($xDatDoi); ?>
  	  									<script languaje = "javascript">
  	  										parent.fmwork.document.forms['frgrm']['cSucId'].value   = "<?php echo $xMtzDo['sucidxxx']?>";
  	  										parent.fmwork.document.forms['frgrm']['cDocTip'].value  = "<?php echo $xMtzDo['doctipxx']?>";
  	  										parent.fmwork.document.forms['frgrm']['cDocNro'].value  = "<?php echo $xMtzDo['docidxxx']?>";
  	  										parent.fmwork.document.forms['frgrm']['cDocSuf'].value  = "<?php echo $xMtzDo['docsufxx']?>";
  	  										parent.fmwork.document.forms['frgrm']['dDesde'].value   = "<?php echo $xMtzDo['regfcrex']?>";
  	  										parent.fmwork.document.forms['frgrm']['dHasta'].value   = "<?php echo $xMtzDo['regfcrex']?>";
  	  										parent.fmwork.document.forms['frgrm']['cEstado'].value  = "<?php echo $xMtzDo['regestxx']?>";
  	  									</script>
  	  								<?php } else { ?>
  	  								  <script languaje = "javascript">
  	     	    					  parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
  												window.close();
  											</script>
  	  								<?php }
  	  							break;
  	  							case "WINDOW":
  	  								$qDatDoi  = "SELECT * ";
			  							$qDatDoi .= "FROM $cAlfa.sys00121 ";
			  							$qDatDoi .= "WHERE docidxxx LIKE \"%$gDocNro%\" ";
	  									$xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatDoi."~".mysql_num_rows($xDatDoi));

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
																	<a href = "javascript:window.opener.document.forms['frgrm']['cSucId'].value   ='<?php echo $xRDoc['sucidxxx'] ?>';
	  																										window.opener.document.forms['frgrm']['cDocTip'].value  ='<?php echo $xRDoc['doctipxx'] ?>';
	  																										window.opener.document.forms['frgrm']['cDocNro'].value  ='<?php echo $xRDoc['docidxxx'] ?>';
	  																										window.opener.document.forms['frgrm']['cDocSuf'].value  ='<?php echo $xRDoc['docsufxx'] ?>';
	  																										window.opener.document.forms['frgrm']['dDesde'].value   ='<?php echo $xRDoc['regfcrex'] ?>';
                                                        window.opener.document.forms['frgrm']['dHasta'].value   ='<?php echo $xRDoc['regfcrex'] ?>';
																                        window.opener.document.forms['frgrm']['cEstado'].value  ='<?php echo $xRDoc['regestxx'] ?>';
																                        window.close()"><?php echo $xRDoc['docidxxx'] ?></a></td>
																<td width = "050" class= "name"><?php echo $xRDoc['docsufxx'] ?></td>
															</tr>
														<?php } ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");?>
  	  								  <script languaje = "javascript">
                          window.opener.document.forms['frgrm']['cSucId'].value   ='';
													window.opener.document.forms['frgrm']['cDocTip'].value  ='';
													window.opener.document.forms['frgrm']['cDocNro'].value  ='';
													window.opener.document.forms['frgrm']['cDocSuf'].value  ='';
													window.opener.document.forms['frgrm']['cEstado'].value  ='';
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
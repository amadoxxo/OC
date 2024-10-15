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
  if ($gWhat != "" && $gFunction != "") { 
    $cTitVen = "Documentos de Comercio Exterior";
  ?>
  	<html>
  		<head>
  			<title><?php echo $cTitVen ?></title>
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
  			   			<legend><?php echo $cTitVen ?></legend>
  	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
  	  						<?php switch ($gWhat) {
  	  							case "VALID":
	  	  						  $qObsDo  = "SELECT * ";
							        $qObsDo .= "FROM $cAlfa.sys00121 ";
							        $qObsDo .= "WHERE " ;
							        $qObsDo .= "docidxxx = \"$gDocNro\" LIMIT 0,1 ";
							        $xObsDo  = f_MySql("SELECT","",$qObsDo,$xConexion01,"");
  	  								//f_Mensaje(__FILE__,__LINE__,$qObsDo."~".mysql_num_rows($xObsDo));
  	  								if (mysql_num_rows($xObsDo) == 1) {
  											$xMtzDo = mysql_fetch_array($xObsDo); ?>
	                          <script languaje = "javascript">
		                          parent.fmwork.document.forms['frgrm']['cSucId'].value   = "<?php echo $xMtzDo['sucidxxx']?>";
		                          parent.fmwork.document.forms['frgrm']['cDocNro'].value  = "<?php echo $xMtzDo['docidxxx']?>";
		                          parent.fmwork.document.forms['frgrm']['cDocSuf'].value  = "<?php echo $xMtzDo['docsufxx']?>";
		                        </script>							
  	  								<?php } else { ?>
  	  								  <script languaje = "javascript">
  	     	    					  parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
  												window.close();
  											</script>
  	  								<?php }
  	  							break;
  	  							case "WINDOW":
  	  								$qObsDo  = "SELECT * ";
                      $qObsDo .= "FROM $cAlfa.sys00121 ";
                      $qObsDo .= "WHERE " ;
                      $qObsDo .= "docidxxx LIKE \"%$gDocNro%\"";
                      $xObsDo  = f_MySql("SELECT","",$qObsDo,$xConexion01,"");
                      //f_Mensaje(__FILE__,__LINE__,$qObsDo."~".mysql_num_rows($xObsDo));
	  									if (mysql_num_rows($xObsDo) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "300">
					    					    <tr>
		                          <td widht = "050" Class = "name"><center>Suc.</center></td>
		                          <td widht = "200" Class = "name"><center>Documento</center></td>
		                          <td widht = "050" Class = "name"><center>Sufijo</center></td>
		                        </tr>
			                      <?php while ($xRDoc = mysql_fetch_array($xObsDo)) { ?>
                                <tr>
	                                <td width = "050" class= "name"><?php echo $xRDoc['sucidxxx'] ?></td>
	                                <td width = "200" class= "name">
	                                  <a href = "javascript:window.opener.document.forms['frgrm']['cSucId'].value   ='<?php echo $xRDoc['sucidxxx'] ?>';
	                                                        window.opener.document.forms['frgrm']['cDocNro'].value  ='<?php echo $xRDoc['docidxxx'] ?>';
	                                                        window.opener.document.forms['frgrm']['cDocSuf'].value  ='<?php echo $xRDoc['docsufxx'] ?>';
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
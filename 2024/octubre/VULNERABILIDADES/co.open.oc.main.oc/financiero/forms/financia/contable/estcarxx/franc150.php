<?php 
  namespace openComex;
  /**
  * Parametrica de Clientes
  * Este programa permite listar los Clientes que se encuentran en la Base de Datos 
  * @author  openTecnologia - Desarrollo
  * @package openComex
  * @version 3.0.0
  **/

	include("../../../../libs/php/utility.php");
	
  if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica de Clientes</title>			
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
			   			<legend>Parametrica de Clientes</legend>			   			
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "VALID":
	  									$qDatExt  = "SELECT * ";
											$qDatExt .= "FROM $cAlfa.SIAI0150 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "CLIIDXXX = \"$gTerId\" AND ";
											$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY CLIIDXXX LIMIT 0,1";											
			  							$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
			  							//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

	  									if (mysql_num_rows($xDatExt) == 1) {
	  										$vDatExt = mysql_fetch_array($xDatExt); ?>
												<script languaje = "javascript">
                              parent.fmwork.document.forms['frgrm']['<?php echo $gFunction ?>'].value  = "<?php echo $vDatExt['CLIIDXXX'] ?>";
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
		  								$qDatExt  = "SELECT * ";
											$qDatExt .= "FROM $cAlfa.SIAI0150 ";
											$qDatExt .= "WHERE ";
											$qDatExt .= "CLIIDXXX LIKE \"%$gTerId%\" AND ";
											$qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY CLIIDXXX";
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
															if (mysql_num_rows($xDatExt) > 0) { ?>
																<tr>
																	<td width = "030" Class = "name">
																		<a href = "javascript:
                      															 window.opener.document.forms['frgrm']['<?php echo $gFunction ?>'].value  = '<?php echo $xRDE['CLIIDXXX'] ?>';
																							 window.close()"><?php echo $xRDE['CLIIDXXX'] ?>
																		</a>
																	</td>
																	<td width = "390" Class = "name"><?php echo $cNombre = ($xRDE['CLINOMXX'] != "") ? $xRDE['CLINOMXX'] : trim($xRDE['CLIAPE1X']." ".$xRDE['CLIAPE2X']." ".$xRDE['CLINOM1X']." ".$xRDE['CLINOM2X']); ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
    																	window.opener.document.forms['frgrm']['<?php echo $gFunction ?>'].value  = "<?php echo $xRDE['CLIIDXXX'] ?>";
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
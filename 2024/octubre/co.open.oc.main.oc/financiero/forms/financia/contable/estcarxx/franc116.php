<?php 
  namespace openComex;
  /**
  * Parametrica de Centros de Costo
  * Este programa permite listar los Centros de costo que se encuentran en la Base de Datos 
  * @author  openTecnologia - Desarrollo
  * @package openComex
  * @version 3.0.0
  **/

	include("../../../../libs/php/utility.php");
	
  if ($gWhat != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Parametrica de Centros de Costo</title>			
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
			   			<legend>Parametrica de Centros de Costo</legend>			   			
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "VALID":
	  									$qDatCco  = "SELECT * ";
											$qDatCco .= "FROM $cAlfa.fpar0116 ";
											$qDatCco .= "WHERE ";
											$qDatCco .= "ccoidxxx = \"$gCcoId\" AND ";
											$qDatCco .= "regestxx = \"ACTIVO\" ORDER BY ccoidxxx LIMIT 0,1";											
			  							$xDatCco  = f_MySql("SELECT","",$qDatCco,$xConexion01,"");
			  							//f_Mensaje(__FILE__,__LINE__,$qDatCco." ~ ".mysql_num_rows($xDatCco));

	  									if (mysql_num_rows($xDatCco) == 1) {
	  										$vDatCco = mysql_fetch_array($xDatCco); ?>
												<script languaje = "javascript">
                              parent.fmwork.document.forms['frgrm']['<?php echo $gFunction ?>'].value  = "<?php echo $vDatCco['ccoidxxx'] ?>";
												</script>
  										<?php } else { ?>
												<script languaje = "javascript">
	      	    						parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
		  								$qDatCco  = "SELECT * ";
											$qDatCco .= "FROM $cAlfa.fpar0116 ";
											$qDatCco .= "WHERE ";
											$qDatCco .= "ccoidxxx LIKE \"%$gCcoId%\" AND ";
											$qDatCco .= "regestxx = \"ACTIVO\" ORDER BY ccoidxxx";
	  									$xDatCco  = f_MySql("SELECT","",$qDatCco,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatCco." ~ ".mysql_num_rows($xDatCco));

 											if (mysql_num_rows($xDatCco) > 0) { ?>
		 										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "550">
														<tr>
															<td widht = "050" Class = "name"><center>Id</center></td>
															<td widht = "500" Class = "name"><center>Descripci&oacute;on</center></td>
														</tr>
														<?php while ($xRDE = mysql_fetch_array($xDatCco)) {
															if (mysql_num_rows($xDatCco) > 0) { ?>
																<tr>
																	<td width = "030" Class = "name">
																		<a href = "javascript:
                      												 window.opener.document.forms['frgrm']['<?php echo $gFunction ?>'].value  = '<?php echo $xRDE['ccoidxxx'] ?>';
																							 window.close()"><?php echo $xRDE['ccoidxxx'] ?>
																		</a>
																	</td>
																	<td width = "390" Class = "name"><?php echo $xRDE['ccodesxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
    															window.opener.document.forms['frgrm']['<?php echo $gFunction ?>'].value  = "<?php echo $xRDE['ccoidxxx'] ?>";
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
<?php
namespace openComex;
include("../../../../libs/php/utility.php");
?>
<!--
	Los Parametros que Recibo son :
	$gSearch = Un dato para filtrar la consulta con el WHERE
	$gFields = A que campos del formulario voy a devolver los datos
	$gWhat   = Que voy a hacer, una ventana o una validacion
-->
<?php if (!empty($gWhat) && !empty($gFunction)) {
	//f_Mensaje(__FILE__,__LINE__,str_replace("~","%",$gQuery));
	?>

	<html>
		<head>
			<title>Param&eacute;trica de Director de Cuenta</title>
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
			   			<legend>Param&eacute;trica de Director de Cuenta</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gWhat) {
	  								case "WINDOW":
	  									$qConCob  = "SELECT * ";
	  									$qConCob .= "FROM $cAlfa.fpar0129 ";
	  									$qConCob .= "WHERE ";
	  									switch ($gFunction) {
	  										case "cSerId":
    	  									$qConCob .= "seridxxx LIKE \"%$gSerId%\" AND ";
    	  									$qConCob .= "regestxx = \"ACTIVO\" ORDER BY serdesxx";
    	  								break;
    	  								case "cSerNom":
    	  									$qConCob .= "serdesxx LIKE \"%$gSerNom%\" AND ";
    	  									$qConCob .= "regestxx = \"ACTIVO\" ORDER BY serdesxx";
    	  								break;
	  									}
    	  							$xConCob = f_MySql("SELECT","",$qConCob,$xConexion01,"");

	  									if (mysql_num_rows($xConCob) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "060" Class = "name"><center>Id</center></td>
															<td widht = "350" Class = "name"><center>Concepto</center></td>
															<td widht = "050" Class = "name"><center>Estado</center></td>
														</tr>
														<?php while ($zRow = mysql_fetch_array($xConCob)) {
															if (mysql_num_rows($xConCob) > 1) { ?>
																<tr>
																	<td width = "060" class= "name">
																		<a href = "javascript:
															        switch ('<?php echo $gFunction ?>') {
									                     case 'cSerId':
						                           case 'cSerNom':
    																		window.opener.document.forms['frgrm']['cSerId'].value  = '<?php echo $zRow['seridxxx'] ?>';
    																		window.opener.document.forms['frgrm']['cSerNom'].value = '<?php echo $zRow['serdesxx'] ?>';
    																	 break;
															        }
															        window.close()"><?php echo $zRow['seridxxx'] ?></a>
																	</td>
																	<td width = "350" class= "name"><?php echo $zRow['serdesxx'] ?>&nbsp;</td>
																	<td width = "050" class= "name"><?php echo $zRow['regestxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje = "javascript">
																switch ("<?php echo $gFunction ?>") {
        												    case "cSerId":
        												    case "cSerNom":
																	window.opener.document.forms['frgrm']['cSerId'].value   = "<?php echo $zRow['seridxxx'] ?>";
																	window.opener.document.forms['frgrm']['cSerNom'].value  = "<?php echo $zRow['serdesxx'] ?>";
																}
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");?>
												<script languaje = "javascript">
												switch ("<?php echo $gFunction ?>") {
												    case "cSerId":
												    case "cSerNom":
													window.opener.document.forms['frgrm']['cSerId'].value   = "";
													window.opener.document.forms['frgrm']['cSerNom'].value  = "";
												}
													window.close();
												</script>
											<?php
	  									}
	  								break;
	  								case "VALID":
	  									$qConCob  = "SELECT * ";
	  									$qConCob .= "FROM $cAlfa.fpar0129 ";
	  									$qConCob .= "WHERE ";
	  									switch ($gFunction) {
	  										case "cSerId":
    	  									$qConCob .= "seridxxx = \"$gSerId\" AND ";
    	  									$qConCob .= "regestxx = \"ACTIVO\" ORDER BY serdesxx";
    	  								break;
    	  								case "cSerNom":
    	  									$qConCob .= "seridxxx = \"$gSerNom\" AND ";
    	  									$qConCob .= "regestxx = \"ACTIVO\" ORDER BY serdesxx";
    	  								break;
	  									}
	  									$xConCob = f_MySql("SELECT","",$qConCob,$xConexion01,"");

	  									if (mysql_num_rows($xConCob) > 0) {
	  										while ($zRow = mysql_fetch_array($xConCob)) { ?>
  												<script languaje = "javascript">
  												 switch ("<?php echo $gFunction ?>") {
												    case "cSerId":
												    case "cSerNom":
  														parent.fmwork.document.forms['frgrm']['cSerId'].value   = "<?php echo $zRow['seridxxx'] ?>";
  														parent.fmwork.document.forms['frgrm']['cSerNom'].value  = "<?php echo $zRow['serdesxx'] ?>";
  													break;
  												 }
													</script>
												<?php }
	  									} else { ?>
												<script languaje = "javascript">
													parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
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
	f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
} ?>
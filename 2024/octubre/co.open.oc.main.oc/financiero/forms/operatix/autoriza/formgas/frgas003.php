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
	  									$qSqlUsr  = "SELECT * ";
	  									$qSqlUsr .= "FROM $cAlfa.SIAI0003 ";
	  									$qSqlUsr .= "WHERE ";
	  									$qSqlUsr .= "(USRIDXXX = USRID2XX OR ";
	  									$qSqlUsr .= "USRPROXX LIKE \"%100~100%\") AND ";
	  									$qSqlUsr .= "USRIDXXX != \"ADMIN\" AND ";   //Cambio para pruebas Alpopular 2009-08-17 11:40 AM
                      $qSqlUsr .= "USRINTXX != \"SI\" AND ";
	  									switch ($gFunction) {
	  										case "cDirId":
    	  									$qSqlUsr .= "USRIDXXX LIKE \"%$gDirId%\" AND ";
    	  									$qSqlUsr .= "REGESTXX = \"ACTIVO\" ORDER BY USRNOMXX";
    	  								break;
    	  								case "cDirNom":
    	  									$qSqlUsr .= "USRNOMXX LIKE \"%$gDirNom%\" AND ";
    	  									$qSqlUsr .= "REGESTXX = \"ACTIVO\" ORDER BY USRNOMXX";
    	  								break;
	  									}
	  									//f_Mensaje(__FILE__,__LINE__,$qSqlUsr);
    	  							$xSqlUsr = f_MySql("SELECT","",$qSqlUsr,$xConexion01,"");

	  									if (mysql_num_rows($xSqlUsr) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
														<tr>
															<td widht = "060" Class = "name"><center>Nit</center></td>
															<td widht = "350" Class = "name"><center>Nombre</center></td>
															<td widht = "050" Class = "name"><center>Estado</center></td>
														</tr>
														<?php while ($zRow = mysql_fetch_array($xSqlUsr)) {
															if (mysql_num_rows($xSqlUsr) > 1) { ?>
																<tr>
																	<td width = "060" class= "name">
																		<a href = "javascript:
															        switch ('<?php echo $gFunction ?>') {
									                     case 'cDirId':
						                           case 'cDirNom':
    																		window.opener.document.forms['frgrm']['cDirId'].value  = '<?php echo $zRow['USRIDXXX'] ?>';
    																		window.opener.document.forms['frgrm']['cDirNom'].value = '<?php echo $zRow['USRNOMXX'] ?>';
    																	 break;
															        }
															        window.close()"><?php echo $zRow['USRIDXXX'] ?></a>
																	</td>
																	<!--<td width = "060" class= "name"><?php echo $zRow['SUCIDXXX'] ?></td>-->
																	<td width = "350" class= "name"><?php echo $zRow['USRNOMXX'] ?></td>
																	<td width = "050" class= "name"><?php echo $zRow['REGESTXX'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje = "javascript">
																switch ("<?php echo $gFunction ?>") {
        												    case "cDirId":
        												    case "cDirNom":
																	window.opener.document.forms['frgrm']['cDirId'].value   = "<?php echo $zRow['USRIDXXX'] ?>";
																	window.opener.document.forms['frgrm']['cDirNom'].value  = "<?php echo $zRow['USRNOMXX'] ?>";
																}
																	window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
	  									}
	  								break;
	  								case "VALID":
	  									$qSqlUsr  = "SELECT * ";
	  									$qSqlUsr .= "FROM $cAlfa.SIAI0003 ";
	  									$qSqlUsr .= "WHERE ";
	  									$qSqlUsr .= "(USRIDXXX = USRID2XX OR ";
	  									$qSqlUsr .= "USRPROXX LIKE \"%100~100%\") AND ";
	  									$qSqlUsr .= "USRIDXXX != \"ADMIN\" AND ";
                      $qSqlUsr .= "USRINTXX != \"SI\" AND ";
	  									switch ($gFunction) {
	  										case "cDirId":
    	  									$qSqlUsr .= "USRIDXXX = \"$gDirId\" AND ";
    	  									$qSqlUsr .= "REGESTXX = \"ACTIVO\" ORDER BY USRNOMXX";
    	  								break;
    	  								case "cDirNom":
    	  									$qSqlUsr .= "USRNOMXX = \"$gDirNom\" AND ";
    	  									$qSqlUsr .= "REGESTXX = \"ACTIVO\" ORDER BY USRNOMXX";
    	  								break;
	  									}
	  									//f_Mensaje(__FILE__,__LINE__,$qSqlUsr);
	  									$xSqlUsr = f_MySql("SELECT","",$qSqlUsr,$xConexion01,"");

	  									if (mysql_num_rows($xSqlUsr) > 0) {
	  										while ($zRow = mysql_fetch_array($xSqlUsr)) { ?>
  												<script languaje = "javascript">
  												 switch ("<?php echo $gFunction ?>") {
												    case "cDirId":
												    case "cDirNom":
  														parent.fmwork.document.forms['frgrm']['cDirId'].value   = "<?php echo $zRow['USRIDXXX'] ?>";
  														parent.fmwork.document.forms['frgrm']['cDirNom'].value  = "<?php echo $zRow['USRNOMXX'] ?>";
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
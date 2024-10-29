<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");

	/* TRAIGO EL NOMBRE DEL USUARIO SYS00001 */
	$zSqlUsu = "SELECT USRNOMXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
	$zCrsUsu = mysql_query($zSqlUsu,$xConexion01);
	$zUsrNom = "USUARIO SIN NOMBRE";
	while ($zRUsu = mysql_fetch_array($zCrsUsu)) {
		$zUsrNom = trim($zRUsu['USRNOMXX']);
	}
?>
<html>
	<title>OBSERVACIONES DEL CLIENTE</title>
 	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
   	<script languaje = 'javascript'>
			function wRegresa() { // Devuelvo al Formulario que Me Llama los Datos del Cliente
	  		var	zTerObs = document.forms['frgrm']['cTerObs'].value;
	  		if (zTerObs.length > 5) {
					xTerObs = ' << <?php echo $zUsrNom.' '.date('Y-m-d').' '.date('H:i:s') ?> >> '
					xTerObs = xTerObs.toUpperCase() + zTerObs;
	  			xTerObs = xTerObs.toUpperCase();
 					window.opener.document.forms['frgrm']['cTerObs'].value = window.opener.document.forms['frgrm']['cTerObs'].value + xTerObs;
		  		window.close();
	   		} else	{
	   			alert('Debe digitar una Observacion');
	   		}
		  }
		</script>
 	</head>
 	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
 		<div id = 'overDiv' style = 'position:absolute; visibility:hide; z-index:1'></div>
 		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="360">
				<tr>
					<td>
				  	<fieldset>
					  	<legend>Observaciones del Cliente</legend>
					 		<form name = 'frgrm' action = 'frterobs.php' method = 'post'>
								<center>
									<table border = '0' cellpadding = '0' cellspacing = '0' style = 'width:360' bgcolor = '#C0C0C0'>
										<?php
										$zCol = f_Format_Cols(18);
										echo $zCol;
										?>
										<tr>
											<td Class = 'letra7' colspan = '18'>
											<textarea Class = 'letrata' style = 'width:360;height:96' name = 'cTerObs' onBlur = 'javascript:this.value=this.value.toUpperCase()'></textarea></td>
										</tr>
							   	</table>
							  </center>
							</form>
						</fieldset>
					</td>
				</tr>
		 	</table>
		</center>
		<center>
		 	<table border="0" cellpadding="0" cellspacing="0" width="360">
				<tr height="21">
					<td width="269" height="21"></td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javascript:wRegresa()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
				</tr>
			</table>
		</center>
	</body>
</html>
<?php
  namespace openComex;
/**
	 * Proceso Grupo de Observaciones para Formularios.
	 * --- Descripcion: Permite Crear un Nuevo Grupo de Observaciones para Formularios.
	 * @author Johana Arboleda Ramos <dp1@opentecnologia.com.co>
 	 * @package opencomex
	 * @version 001
	 */
	include("../../../../libs/php/utility.php");
?>
<html>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script language="javascript">
  		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
  				document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
  				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
  	  }
	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="400">
				<tr>
					<td>
				  	<fieldset>
					  	<legend>Nuevo <?php echo $_COOKIE['kProDes'] ?></legend>
						 	<form name = 'frgrm' action = 'frgofgra.php' method = 'post' target='fmpro'>
						 		<input type ="hidden" name ="cGofTip">
							 	<center>
      							<table border = '0' cellpadding = '0' cellspacing = '0' width='400'>
  							 			<?php echo f_Format_Cols(20); ?>
  										<tr>
  											<td Class = "clase08" colspan = "04">Id</a><br>
  												<input type = 'text' Class = 'letra' style = 'width:80' name = 'cGofId' readonly>
  											</td>
  											<td Class = 'clase08' colspan = '16'>Descripci&oacute;n<br>
  												<input type = 'text' Class = 'letra' style = 'width:320' name = 'cGofDes' onBlur = "javascript:this.value=this.value.toUpperCase()">
  											</td>
  										</tr>
  										<tr>
  								   		<td Class = "name" colspan = "4">Fecha Cre<br>
  									   		<input type = "text" Class = "letra"  style = "width:80;text-align:center"  name = "dFecCre" value = "<?php echo date('Y-m-d') ?>" readonly>
  								    	</td>
  								    	<td Class = 'name' colspan = "4">Hora Cre<br>
  										 		<input type = 'text' Class = 'letra' style = "width:80;text-align:center" name = "cHorCre" value = "<?php echo date('H:i:s') ?>" readonly>
  											</td>
  								   		<td Class = "name" colspan = "4">Fecha Mod<br>
  									   		<input type = "text" Class = "letra"  style = "width:80;text-align:center"  name = "dFecMod" value = "<?php echo date('Y-m-d') ?>" readonly>
  								    	</td>
  								    	<td Class = 'name' colspan = "4">Hora Mod<br>
  										 		<input type = 'text' Class = 'letra' style = "width:80;text-align:center" name = "cHorMod" value = "<?php echo date('H:i:s') ?>" readonly>
  											</td>
  											<td Class = "name" colspan = "4">Estado<br>
  										 		<input type = "text" Class = "letra" style = "width:80;text-align:center" name = "cEstado"  value = "ACTIVO" readonly>
  											</td>
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
			<table border="0" cellpadding="0" cellspacing="0" width="380">
				<tr height="21">
					<?php switch ($_COOKIE['kModo']) {
						case "VER": ?>
							<td width="289" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
						default: ?>
							<td width="198" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:document.forms['frgrm'].submit()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
			  	} ?>
				</tr>
			</table>
		</center>
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<?php
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
			break;
			case "EDITAR":
				f_CargaData($gGofTip,$gGofId);
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cGofId'].readOnly	 = true;
				</script>
			<?php break;
			case "VER":
				f_CargaData($gGofTip,$gGofId); ?>
				<script languaje = "javascript">
					for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
            document.forms['frgrm'].elements[x].style.fontWeight = "bold";
          }
				</script>
			<?php break;
		} ?>

		<?php function f_CargaData($xGofTip,$xGofId) {
		  global $cAlfa; global $xConexion01;

		  /* TRAIGO DATOS DE CABECERA*/
      $qDatObs  = "SELECT * ";
			$qDatObs .= "FROM $cAlfa.fpar0123 ";
	    $qDatObs .= "WHERE goftipxx = \"$xGofTip\" AND ";
	    $qDatObs .= "gofidxxx = \"$xGofId\" LIMIT 0,1";
			$xDatObs  = f_MySql("SELECT","",$qDatObs,$xConexion01,"");

		 /* EMPIEZO A RECORRER EL CURSOR DE CABECERA */
			while ($vDatObs = mysql_fetch_array($xDatObs)) {
		?>
				<script language = "javascript">
					document.forms['frgrm']['cGofTip'].value	 = "<?php echo $vDatObs['goftipxx'] ?>";
					document.forms['frgrm']['cGofId'].value		 = "<?php echo $vDatObs['gofidxxx'] ?>";
					document.forms['frgrm']['cGofDes'].value   = "<?php echo $vDatObs['gofdesxx'] ?>";
		      document.forms['frgrm']['dFecCre'].value   = "<?php echo $vDatObs['regfcrex'] ?>";
				 	document.forms['frgrm']['cHorCre'].value   = "<?php echo $vDatObs['reghcrex'] ?>";
				 	document.forms['frgrm']['dFecMod'].value   = "<?php echo $vDatObs['regfmodx'] ?>";
				 	document.forms['frgrm']['cHorMod'].value   = "<?php echo $vDatObs['reghmodx'] ?>";
				 	document.forms['frgrm']['cEstado'].value   = "<?php echo $vDatObs['regestxx'] ?>";
				</script>
			<?php }
		} ?>
	</body>
</html>
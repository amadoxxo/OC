<?php
  namespace openComex;
/**
	 * Proceso tipo de Documento.
	 * --- Descripcion: Permite Crear un Nuevo Tipo de Documento.
	 * @author
	 * @package emisioncero
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

		  function f_EnabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				//document.forms['frgrm']['cTdiId'].disabled =false;
		  }

		  function f_DisabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				//document.forms['frgrm']['cTdiId'].disabled =true;
		  }

			function f_Valida_Estado(){
       	var zEstado = document.forms['frgrm']['cEstado'].value.toUpperCase();
       	if(zEstado == 'A' || zEstado == 'AC' || zEstado == 'ACT' || zEstado == 'ACTI' || zEstado == 'ACTIV' || zEstado == 'ACTIVO'){
       		zEstado = 'ACTIVO';
       	} else {
       		if(zEstado == 'I' || zEstado == 'IN' || zEstado == 'INA' || zEstado == 'INAC' || zEstado == 'INACT' || zEstado == 'INACTI' || zEstado == 'INACTIV' || zEstado == 'INACTIVO') {
       			zEstado = 'INACTIVO';
       		} else {
       			zEstado = '';
       		}
       	}
       	document.forms['frgrm']['cEstado'].value = zEstado;
    	}
	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
				  	<fieldset>
					  	<legend>Nuevo <?php echo $_COOKIE['kProDes'] ?></legend>
						 	<form name = 'frgrm' action = 'frtdigra.php' method = 'post' target='fmpro'>
							 	<center>
      							<table border = '0' cellpadding = '0' cellspacing = '0' width='300'>
  							 			<?php $nCol = f_Format_Cols(20);
  							 			echo $nCol;?>
  										<tr>
  											<td Class = "clase08" colspan = "4">Id</a><br>
  												<input type = 'text' Class = 'letra' style = 'width:80' name = 'cTdiId' maxlength="10"
  							 			        onBlur = "javascript:this.value=this.value.toUpperCase()">
  											</td>
  											<td Class = 'clase08' colspan = '14'>Tipo de Documento<br>
  												<input type = 'text' Class = 'letra' style = 'width:280' name = 'cTdiDes' onBlur = "javascript:this.value=this.value.toUpperCase()">
  											</td>
  										</tr>
  										<!--
  										<tr>
  								   		<td Class = "clase08" colspan = "7">Fecha<br>
  									   		<input type = "text" Class = "letra"  style = "width:140;text-align:center"  name = "dFecCre"  value = "<?php echo date('Y-m-d') ?>" readonly>
  								    	</td>
  								    	<td Class = "clase08" colspan = "5">Hora<br>
  										 		<input type = 'text' Class = 'letra' style = "width:100;text-align:center" name = "dHorCre"  value = "<?php echo date('H:i:s') ?>" readonly>
  											</td>
  								   		<td Class = "clase08" colspan = "6">Estado<br>
  										 		<input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cEstado"  value = "ACTIVO"
  										         onblur = "javascript:this.value=this.value.toUpperCase();f_Valida_Estado();
  																		             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  														 onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td>
  										</tr>
  										-->
  										<tr>
  								   		<td Class = "name" colspan = "4">Fecha Cre<br>
  									   		<input type = "text" Class = "letra"  style = "width:80;text-align:center"  name = "dFecCre" value = "<?php echo date('Y-m-d') ?>" readonly>
  								    	</td>
  								    	<td Class = 'name' colspan = "3">Hora Cre<br>
  										 		<input type = 'text' Class = 'letra' style = "width:60;text-align:center" name = "cHorCre" value = "<?php echo date('H:i:s') ?>" readonly>
  											</td>
  								   		<td Class = "name" colspan = "4">Fecha Mod<br>
  									   		<input type = "text" Class = "letra"  style = "width:80;text-align:center"  name = "dFecMod" value = "<?php echo date('Y-m-d') ?>" readonly>
  								    	</td>
  								    	<td Class = 'name' colspan = "3">Hora Mod<br>
  										 		<input type = 'text' Class = 'letra' style = "width:60;text-align:center" name = "cHorMod" value = "<?php echo date('H:i:s') ?>" readonly>
  											</td>
  											<td Class = "name" colspan = "4">Estado<br>
  										 		<input type = "text" Class = "letra" style = "width:80;text-align:center" name = "cEstado"  value = "ACTIVO"
    										    onblur = "javascript:this.value=this.value.toUpperCase();f_Valida_Estado();
    																             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
    												onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
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
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:f_EnabledCombos();document.forms['frgrm'].submit();f_DisabledCombos()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
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
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cEstado'].readOnly  = true;
				</script>
				<?php
			break;
			case "EDITAR":
				f_CargaData($cTdiId);
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cTdiId'].readOnly	 = true;
				</script>
			<?php break;
			case "VER":
				f_CargaData($cTdiId); ?>
				<script languaje = "javascript">
					//document.forms['frgrm']['cTdiId'].readOnly	 = true;
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

		<?php function f_CargaData($xTdiId) {
		  global $cAlfa; global $xConexion01;

		  /* TRAIGO DATOS DE CABECERA*/
      $qDatTdi  = "SELECT * ";
			$qDatTdi .= "FROM $cAlfa.fpar0109 ";
	    $qDatTdi .= "WHERE tdiidxxx = \"$xTdiId\" LIMIT 0,1";
			$xDatTdi  = f_MySql("SELECT","",$qDatTdi,$xConexion01,"");

		 /* EMPIEZO A RECORRER EL CURSOR DE CABECERA */
			while ($vDatTdi = mysql_fetch_array($xDatTdi)) {
		?>
				<script language = "javascript">
					document.forms['frgrm']['cTdiId'].value		 = "<?php echo $vDatTdi['tdiidxxx'] ?>";
					document.forms['frgrm']['cTdiDes'].value   = "<?php echo $vDatTdi['tdidesxx'] ?>";
		      document.forms['frgrm']['dFecCre'].value   = "<?php echo $vDatTdi['regfcrex'] ?>";
				 	document.forms['frgrm']['cHorCre'].value   = "<?php echo $vDatTdi['reghcrex'] ?>";
				 	document.forms['frgrm']['dFecMod'].value   = "<?php echo $vDatTdi['regfmodx'] ?>";
				 	document.forms['frgrm']['cHorMod'].value   = "<?php echo $vDatTdi['reghmodx'] ?>";
				 	document.forms['frgrm']['cEstado'].value   = "<?php echo $vDatTdi['regestxx'] ?>";
				</script>
			<?php }
		} ?>
	</body>
</html>
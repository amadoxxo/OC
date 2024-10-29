<?php
  namespace openComex;
/**
	 * Proceso Bancos.
	 * --- Descripcion: Permite Crear un Nuevo Banco.
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
				document.forms['frgrm']['cConId'].disabled =false;
		  }

		  function f_DisabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.forms['frgrm']['cConId'].disabled =true;
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
					  	<legend><?php echo $_COOKIE['kModo']." ".$_COOKIE['kProDes'] ?></legend>
						 	<form name = 'frgrm' action = 'frcongra.php' method = 'post' target='fmpro'>
							 	<center>
									<table border = '0' cellpadding = '0' cellspacing = '0' width='520'>
							 			<?php $nCol = f_Format_Cols(26);
  							 		echo $nCol;?>
							    	<tr>
											<td Class = "name" colspan = "3">Codigo<br>
										 		<input type = "text" Class = "letra" name = "cConId" style = "width:60" maxlength="3" readonly>
											</td>
								    	<td Class = "name" colspan = "16">Descripcion<br>
										 		<input type = "text" Class = "letra" name = "cConDes" style = "width:320"
										 		  onblur = "javascript:this.value=this.value.toUpperCase();
																	             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
									    	  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
										 	</td>
										 	<td Class = "name" colspan = "7">Tipo operacion<br>
										 		<input type = "text" Class = "letra" name = "cConTip" style = "width:140"
										 		  onblur = "javascript:this.value=this.value.toUpperCase();
																	             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
									    	  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
										 	</td>
										</tr>
										<!--
										<tr>
								   		<td Class = "name" colspan = "7">Fecha<br>
									   		<input type = "text" Class = "letra"  style = "width:140;text-align:center"  name = "dFecCre" value = "<?php echo date('Y-m-d') ?>" readonly>
								    	</td>
								    	<td Class = 'name' colspan = "6">Hora<br>
										 		<input type = 'text' Class = 'letra' style = "width:120;text-align:center" name = "cHorCre" value = "<?php echo date('H:i:s') ?>" readonly>
											</td>
								   		<td Class = "name" colspan = "6">Mod<br>
									   		<input type = "text" Class = "letra"  style = "width:120;text-align:center"  name = "dFecMod" value = "<?php echo date('Y-m-d') ?>" readonly>
								    	</td>
											<td Class = "name" colspan = "7">Estado<br>
										 		<input type = "text" Class = "letra" style = "width:140;text-align:center" name = "cEstado"  value = "ACTIVO"
  										    onblur = "javascript:this.value=this.value.toUpperCase();f_Valida_Estado();
  																             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  												onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
											</td>
										</tr>
										-->
										<tr>
								   		<td Class = "name" colspan = "5">Fecha Cre<br>
									   		<input type = "text" Class = "letra"  style = "width:100;text-align:center"  name = "dFecCre" value = "<?php echo date('Y-m-d') ?>" readonly>
								    	</td>
								    	<td Class = 'name' colspan = "5">Hora Cre<br>
										 		<input type = 'text' Class = 'letra' style = "width:100;text-align:center" name = "cHorCre" value = "<?php echo date('H:i:s') ?>" readonly>
											</td>
								   		<td Class = "name" colspan = "5">Fecha Mod<br>
									   		<input type = "text" Class = "letra"  style = "width:100;text-align:center"  name = "dFecMod" value = "<?php echo date('Y-m-d') ?>" readonly>
								    	</td>
								    	<td Class = 'name' colspan = "5">Hora Mod<br>
										 		<input type = 'text' Class = 'letra' style = "width:100;text-align:center" name = "cHorMod" value = "<?php echo date('H:i:s') ?>" readonly>
											</td>
											<td Class = "name" colspan = "6">Estado<br>
										 		<input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cEstado"  value = "ACTIVO"
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
			<table border="0" cellpadding="0" cellspacing="0" width="520">
				<tr height="21">
				 <?php switch ($_COOKIE['kModo']) {
						case "VER": ?>
						<td width="429" height="21"></td>
						<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
					<?php break;
						default: ?>
						<td width="338" height="21"></td>
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
			  f_Mensaje(__FILE__,__LINE__,"Para [INSERTAR] [ACTUALIZAR] [ELIMINAR] Una Condicion Especial, por favor Comunicarse con openTecnologia Ltda.");
			  ?>
				<script languaje = "javascript">
					document.forms['frgrm']['cEstado'].readOnly  = true;
					f_Retorna();
				</script>
			<?php break;
			case "EDITAR":
				f_CargaData($gConId);
				f_Mensaje(__FILE__,__LINE__,"Para [INSERTAR] [ACTUALIZAR] [ELIMINAR] Una Condicion Especial, por favor Comunicarse con openTecnologia Ltda.");
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cConId'].disabled	 = true;
					f_Retorna();
				</script>
			<?php break;
			case "VER":
				f_CargaData($gConId); ?>
				<script languaje = "javascript">
					for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
          }
				</script>
			<?php break;
		} ?>

		<?php
    function f_CargaData($gConId) {
  	  global $xConexion01; global $cAlfa;
    	/* TRAIGO DATOS DE CABECERA */
			$qSqlCab  = "SELECT * ";
			$qSqlCab .= "FROM $cAlfa.fpar0134 ";
			$qSqlCab .= "WHERE ";
			$qSqlCab .= "conidxxx = \"$gConId\"  LIMIT 0,1";
			$xSqlCab  = f_MySql("SELECT","",$qSqlCab,$xConexion01,"");

			while ($zRCab = mysql_fetch_array($xSqlCab)) {
				?>
				<script language = "javascript">
				 	document.forms['frgrm']['cConId'].value    = "<?php echo $zRCab['conidxxx'] ?>";
				 	document.forms['frgrm']['cConDes'].value   = "<?php echo $zRCab['condesxx'] ?>";
          document.forms['frgrm']['cConTip'].value   = "<?php echo $zRCab['contipxx'] ?>";
				 	document.forms['frgrm']['dFecCre'].value   = "<?php echo $zRCab['regfcrex'] ?>";
				 	document.forms['frgrm']['cHorCre'].value   = "<?php echo $zRCab['reghcrex'] ?>";
				 	document.forms['frgrm']['dFecMod'].value   = "<?php echo $zRCab['regfmodx'] ?>";
				 	document.forms['frgrm']['cHorMod'].value   = "<?php echo $zRCab['reghmodx'] ?>";

				 	document.forms['frgrm']['cEstado'].value   = "<?php echo $zRCab['regestxx'] ?>";
				</script>
		    <?php
			}
		}
		?>
	</body>
</html>
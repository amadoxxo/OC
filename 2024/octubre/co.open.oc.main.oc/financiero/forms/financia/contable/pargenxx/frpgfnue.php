<?php
  namespace openComex;
/**
	 * Proceso Bancos.
	 * --- Descripcion: Permite Crear un Nuevo.
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
				document.forms['frgrm']['cStrId'].disabled =false;
		  }

		  function f_DisabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.forms['frgrm']['cStrId'].disabled =true;
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
						 	<form name = 'frgrm' action = 'frpgfgra.php' method = 'post' target='fmpro'>
							 	<center>
									<table border = '0' cellpadding = '0' cellspacing = '0' width='520'>
							 			<?php $nCol = f_Format_Cols(26);
  							 		echo $nCol;?>
							    	<tr>
											<td Class = "name" colspan = "13">Id<br>
										 		<input type = "text" Class = "letra" name = "cStrId" style = "width:260" readonly>
											</td>
								    	<td Class = "name" colspan = "13">Valor<br>
										 		<input type = "text" Class = "letra" name = "cStrVlr" style = "width:260" maxlength="100"
										 		  onblur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
									    	  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
										 	</td>
										</tr>
										<tr>
											<td Class = "name" colspan = "26">Descripci&oacute;n<br>
												<textarea style = 'width:520;height:40' name = "cStrDes" readonly></textarea>
									  	</td>
									  </tr>
									  <tr>	
									  	<td Class = "name" colspan = "26">Ejemplo<br>
												<textarea style = 'width:520;height:40' name = "cStrEjm" readonly></textarea>
									  	</td>
										</tr>
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
			case "NUEVO": ?>
				<script languaje = "javascript">
					document.forms['frgrm']['cEstado'].readOnly  = true;
				</script>
			<?php break;
			case "EDITAR":
				f_CargaData($gStrId); ?>
				<script languaje = "javascript">
					document.forms['frgrm']['cStrId'].disabled	 = true;
				</script>
			<?php break;
			case "VER":
				f_CargaData($gStrId); ?>
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
    function f_CargaData($gStrId) {
  	  global $xConexion01; global $cAlfa;
    	/* TRAIGO DATOS DE CABECERA */
			$qSqlCab  = "SELECT * ";
			$qSqlCab .= "FROM $cAlfa.sys00002 ";
			$qSqlCab .= "WHERE ";
			$qSqlCab .= "stridxxx = \"$gStrId\"  LIMIT 0,1";
			$xSqlCab  = f_MySql("SELECT","",$qSqlCab,$xConexion01,"");
			while ($zRCab = mysql_fetch_array($xSqlCab)) {
				?>
				<script language = "javascript">
				 	document.forms['frgrm']['cStrId'].value    = "<?php echo $zRCab['stridxxx'] ?>";
				 	document.forms['frgrm']['cStrVlr'].value   = "<?php echo $zRCab['strvlrxx'] ?>";
				 	document.forms['frgrm']['cStrDes'].value   = "<?php echo $zRCab['strdesxx'] ?>";
				 	document.forms['frgrm']['cStrEjm'].value   = "<?php echo $zRCab['strejmxx'] ?>";
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
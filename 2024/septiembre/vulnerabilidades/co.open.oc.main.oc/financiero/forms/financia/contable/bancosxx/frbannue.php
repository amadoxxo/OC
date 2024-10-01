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
				//document.forms['frgrm']['cBanId'].disabled =false;
		  }

		  function f_DisabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				//document.forms['frgrm']['cBanId'].disabled =true;
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
						 	<form name = 'frgrm' action = 'frbangra.php' method = 'post' target='fmpro'>
							 	<center>
      							<table border = '0' cellpadding = '0' cellspacing = '0' width='300'>
  							 			<?php $nCol = f_Format_Cols(15);
  							 			echo $nCol;?>
  										<tr>
  											<td Class = "clase08" colspan = "4">Id</a><br>
  												<input type = 'text' Class = 'letra' style = 'width:80' name = 'cBanId' maxlength="3"
  							 			        onBlur = "javascript:this.value=this.value.toUpperCase()">
  											</td>
  											<td Class = 'clase08' colspan = '14'>Banco<br>
  												<input type = 'text' Class = 'letra' style = 'width:280' name = 'cBanDes' onBlur = "javascript:this.value=this.value.toUpperCase()">
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
                      <?php if ($cAlfa == "TEDHLEXPRE" || $cAlfa == "DEDHLEXPRE" || $cAlfa == "DHLEXPRE") { ?>
                        <tr>
                          <td Class = "name" colspan = "15">
                            <fieldset>
                              <legend>Otros</legend>
                                <table border = '0' cellpadding = '0' cellspacing = '0' width='280'>
                                  <?php $nCol = f_Format_Cols(14);
                                  echo $nCol;?>
                                  <tr>
                                    <td Class = "clase08" colspan = "14">Cuenta Bancaria Recibo de Caja<br>
                                      <input type = 'text' Class = 'letra' style = 'width:200' name = 'cBanCue'
                                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                            onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                                    </td>
                                  </tr>
                                </table>
                            </fieldset><br>
                          </td>
                        </tr>
                      <?php } ?>
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
				f_CargaData($cBanId);
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cBanId'].readOnly	 = true;
				</script>
			<?php break;
			case "VER":
				f_CargaData($cBanId); ?>
				<script languaje = "javascript">
					//document.forms['frgrm']['cBanId'].readOnly	 = true;
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

		<?php function f_CargaData($xBanId) {
		  global $cAlfa; global $xConexion01;

		  /* TRAIGO DATOS DE CABECERA*/
      $qDatBan  = "SELECT * ";
			$qDatBan .= "FROM $cAlfa.fpar0124 ";
	    $qDatBan .= "WHERE banidxxx = \"$xBanId\" LIMIT 0,1";
			$xDatBan  = f_MySql("SELECT","",$qDatBan,$xConexion01,"");

		 /* EMPIEZO A RECORRER EL CURSOR DE CABECERA */
			while ($vDatBan = mysql_fetch_array($xDatBan)) {
		?>
				<script language = "javascript">
					document.forms['frgrm']['cBanId'].value		 = "<?php echo $vDatBan['banidxxx'] ?>";
					document.forms['frgrm']['cBanDes'].value   = "<?php echo $vDatBan['bandesxx'] ?>";
		      document.forms['frgrm']['dFecCre'].value   = "<?php echo $vDatBan['regfcrex'] ?>";
				 	document.forms['frgrm']['cHorCre'].value   = "<?php echo $vDatBan['reghcrex'] ?>";
				 	document.forms['frgrm']['dFecMod'].value   = "<?php echo $vDatBan['regfmodx'] ?>";
				 	document.forms['frgrm']['cHorMod'].value   = "<?php echo $vDatBan['reghmodx'] ?>";
				 	document.forms['frgrm']['cEstado'].value   = "<?php echo $vDatBan['regestxx'] ?>";
          if ("<?php echo $cAlfa ?>" == "TEDHLEXPRE" || "<?php echo $cAlfa ?>" == "DEDHLEXPRE" || "<?php echo $cAlfa ?>" == "DHLEXPRE") {
            document.forms['frgrm']['cBanCue'].value = "<?php echo $vDatBan['bancuexx'] ?>";
          }
				</script>
			<?php }
		} ?>
	</body>
</html>
<?php
  namespace openComex;
/**
	 * Proceso Cuentas Corrientes.
	 * --- Descripcion: Permite Crear una Cuenta Corriente.
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

    	function f_Links(xLink,xSwitch,xIteration) {
				var zX    = screen.width;
				var zY    = screen.height;
				switch (xLink){
					case "cBanId":
						if (xSwitch == "VALID") {
							var zRuta  = "frpar124.php?gWhat=VALID&gFunction=cBanId&cBanId="+document.frgrm.cBanId.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
		  				var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpar124.php?gWhat=WINDOW&gFunction=cBanId&cBanId="+document.frgrm.cBanId.value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
				  case "cBanSuc":
						if (xSwitch == "VALID") {
							var zRuta  = "frpar008.php?gWhat=VALID&gFunction=cBanSuc&cBanSuc="+document.frgrm.cBanSuc.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
		  				var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpar008.php?gWhat=WINDOW&gFunction=cBanSuc&cBanSuc="+document.frgrm.cBanSuc.value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
  				case "cPucId":
						if (xSwitch == "VALID") {
							var zRuta  = "frpar115.php?gWhat=VALID&gFunction=cPucId&cPucId="+document.frgrm.cPucId.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
		  				var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpar115.php?gWhat=WINDOW&gFunction=cPucId&cPucId="+document.frgrm.cPucId.value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
		    }
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
						 	<form name = 'frgrm' action = 'frctegra.php' method = 'post' target='fmpro'>
							 	<center>
      							<table border = '0' cellpadding = '0' cellspacing = '0' width='300'>
  							 			<?php $nCol = f_Format_Cols(20);
  							 			echo $nCol;?>
  										<tr>
  											<td Class = "clase08" colspan = "2">
  												<a href = "javascript:document.frgrm.cBanId.value  = '';
  																			  		  document.frgrm.cBanDes.value = '';
  																							f_Links('cBanId','VALID')" id="IdBan">Id</a><br>
  												<input type = 'text' Class = 'letra' style = 'width:40' name = 'cBanId' maxlength="3"
  							 						onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         f_Links('cBanId','VALID');
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:document.frgrm.cBanId.value  = '';
  																			  		  document.frgrm.cBanDes.value = '';
  														                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td>
  											<td Class = 'clase08' colspan = '16'>Banco<br>
  												<input type = 'text' Class = 'letra' style = 'width:320' name = 'cBanDes' readonly>
  											</td>
  										</tr>
  										<tr>
  											<td Class = 'clase08' colspan = '9'>Numero de Cuenta<br>
  												<input type = 'text' Class = 'letra' style = 'width:180' name = 'cBanCta' maxlength="20"
  													onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td>
  											<td Class = "clase08" colspan = "9">Tipo de Cuenta<br>
													<select Class = "letrase" name = "cTipCta" style = "width:180">
													  <option value = ''>[SELECCIONE]</option>
														<option value = 'CTAAHO'>AHORROS</option>
														<option value = 'CTACTE'>CORRIENTE</option>
														<?php if ($cAlfa == "ADUANERA" || $cAlfa == "TEADUANERA" || $cAlfa == "DEADUANERA" || $cAlfa == "DEDESARROL") { ?>
															<option value = 'CREROT'>CREDITO ROTATIVO</option>
														<?php } ?>
													</select>
												</td>
  										</tr>
  											<td Class = "clase08" colspan = "2">
  												<a href = "javascript:document.frgrm.cBanSuc.value  = '';
  																			  		  document.frgrm.cSucDes.value = '';
  																							f_Links('cBanSuc','VALID')" id="IdSuc">Id</a><br>
  												<input type = 'text' Class = 'letra' style = 'width:40' name = 'cBanSuc' maxlength="3"
  										    	onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         f_Links('cBanSuc','VALID');
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:document.frgrm.cBanSuc.value  = '';
  																			  		  document.frgrm.cSucDes.value = '';
  														                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td>
  											<td Class = 'clase08' colspan = '16'>Sucursal<br>
  												<input type = 'text' Class = 'letra' style = 'width:320' name = 'cSucDes' readonly>
  											</td>
  										</tr>
  										<tr>
  										<td Class = 'clase08' colspan = '18'>Direcci&oacute;n Sucursal del Banco<br>
  												<input type = 'text' Class = 'letra' style = 'width:360' name = 'cBanDir'>
  											</td>
  										</tr>
  										<tr>
  											<td Class = "clase08" colspan = "4">
  												<a href = "javascript:document.frgrm.cPucId.value  = '';
  																			  		  document.frgrm.cPucDes.value = '';
  																							f_Links('cPucId','VALID')" id="IdPuc">Id</a><br>
  												<input type = 'text' Class = 'letra' style = 'width:80' name = 'cPucId' maxlength="10"
  										    	onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         f_Links('cPucId','VALID');
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:document.frgrm.cPucId.value  = '';
  																			  		  document.frgrm.cPucDes.value = '';
  														                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td>
  											<td Class = 'clase08' colspan = '14'>Cuenta PUC<br>
  												<input type = 'text' Class = 'letra' style = 'width:280' name = 'cPucDes' readonly>
  											</td>
  										</tr>
  										<tr>
  											  <td colspan="20">
                        	<fieldset>
                          <legend>Datos para Integraci&oacute;n con Sistema Uno </legend>
            								<table border = '0' cellpadding = '0' cellspacing = '0' width='300'>
        							 				<?php $nCol = f_Format_Cols(15);
        							 					echo $nCol;?>
        							 					<td Class = "clase08" colspan = "8">C&oacute;digo Cuenta Bancaria<br>
      									   				<input type="text" name = "cBanCodc" maxlength="3" style = 'width:160'>
      									   			</td>
        							 	   	</table>
        							   	</fieldset>
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
				f_CargaData($cBanId,$cBanCta);
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cBanId'].readOnly	= true;
					document.forms['frgrm']['cBanCta'].readOnly	= true;

					document.forms['frgrm']['cBanId'].onfocus   = "";
					document.forms['frgrm']['cBanCta'].onfocus  = "";

				 	document.forms['frgrm']['cBanId'].onblur    = "";
				 	document.forms['frgrm']['cBanCta'].onblur   = "";

					document.getElementById('IdBan').disabled=true;
				 	document.getElementById('IdBan').href="#";

				</script>
			<?php break;
			case "VER":
				f_CargaData($cBanId,$cBanCta); ?>
				<script languaje = "javascript">
					//document.forms['frgrm']['cBanId'].readOnly	 = true;
					for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
            document.forms['frgrm'].elements[x].style.fontWeight = "bold";
          }

          document.getElementById('IdBan').disabled=true;
				 	document.getElementById('IdBan').href="#";
				 	document.getElementById('IdSuc').disabled=true;
				 	document.getElementById('IdSuc').href="#";
				 	document.getElementById('IdPuc').disabled=true;
				 	document.getElementById('IdPuc').href="#";

				</script>
			<?php break;
		} ?>

		<?php function f_CargaData($xBanId,$xBanCta) {
		  global $cAlfa; global $xConexion01;

		  /* TRAIGO DATOS DE CABECERA*/
      $qDatCte  = "SELECT * ";
			$qDatCte .= "FROM $cAlfa.fpar0128 ";
	    $qDatCte .= "WHERE ";
	    $qDatCte .= "$cAlfa.fpar0128.banidxxx = \"$xBanId\" AND ";
	    $qDatCte .= "$cAlfa.fpar0128.banctaxx = \"$xBanCta\" LIMIT 0,1";
			$xDatCte  = f_MySql("SELECT","",$qDatCte,$xConexion01,"");


		 	/* EMPIEZO A RECORRER EL CURSOR DE CABECERA */
			while ($vDatCte = mysql_fetch_array($xDatCte)) {
				/* Descripcion del Banco */
				$qDesBan  = "SELECT * ";
				$qDesBan .= "FROM $cAlfa.fpar0124 ";
				$qDesBan .= "WHERE ";
				$qDesBan .= "$cAlfa.fpar0124.banidxxx = \"{$vDatCte['banidxxx']}\" AND ";
				$qDesBan .= "$cAlfa.fpar0124.regestxx = \"ACTIVO\" LIMIT 0,1 ";
				$xDesBan  = f_MySql("SELECT","",$qDesBan,$xConexion01,"");

			  $vRDBan = mysql_fetch_array($xDesBan);
				/* Descripcion del Banco */

				/* Descripcion de la Sucursal */
				$qDesSuc  = "SELECT * ";
				$qDesSuc .= "FROM $cAlfa.fpar0008 ";
				$qDesSuc .= "WHERE ";
				$qDesSuc .= "$cAlfa.fpar0008.sucidxxx = \"{$vDatCte['bansucxx']}\" AND ";
				$qDesSuc .= "$cAlfa.fpar0008.regestxx = \"ACTIVO\" LIMIT 0,1 ";
				$xDesSuc  = f_MySql("SELECT","",$qDesSuc,$xConexion01,"");

			  $vRDSuc = mysql_fetch_array($xDesSuc);
				/* Descripcion de la Sucursal */

				/* Descripcion de la Cuenta PUC */
				$qDesPuc  = "SELECT * ";
				$qDesPuc .= "FROM $cAlfa.fpar0115 ";
				$qDesPuc .= "WHERE ";
				$qDesPuc .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$vDatCte['pucidxxx']}\" AND ";
				$qDesPuc .= "$cAlfa.fpar0115.regestxx = \"ACTIVO\" LIMIT 0,1 ";
				$xDesPuc  = f_MySql("SELECT","",$qDesPuc,$xConexion01,"");

			  $vRDPuc = mysql_fetch_array($xDesPuc);
				/* Descripcion de la Cuenta PUC */
			  
			  /* Tipo de Cuenta */
			  $vDatCte['banticta'] = ($vDatCte['banticta'] == "") ? "CTACTE" : $vDatCte['banticta'];
			?>
				<script language = "javascript">
					document.forms['frgrm']['cBanId'].value		 = "<?php echo $vDatCte['banidxxx'] ?>";
					document.forms['frgrm']['cBanDes'].value   = "<?php echo $vRDBan['bandesxx']  ?>";
					document.forms['frgrm']['cBanCta'].value	 = "<?php echo $vDatCte['banctaxx'] ?>";
					document.forms['frgrm']['cTipCta'].value	 = "<?php echo $vDatCte['banticta'] ?>";
					document.forms['frgrm']['cBanSuc'].value	 = "<?php echo $vDatCte['bansucxx'] ?>";
					document.forms['frgrm']['cBanDir'].value	 = "<?php echo $vDatCte['bandirxx'] ?>";
					document.forms['frgrm']['cBanCodc'].value	 = "<?php echo $vDatCte['bancodcx'] ?>";
					document.forms['frgrm']['cSucDes'].value   = "<?php echo $vRDSuc['sucdesxx']  ?>";
					document.forms['frgrm']['cPucId'].value		 = "<?php echo $vDatCte['pucidxxx'] ?>";
					document.forms['frgrm']['cPucDes'].value   = "<?php echo $vRDPuc['pucdesxx']  ?>";
		      document.forms['frgrm']['dFecCre'].value   = "<?php echo $vDatCte['regfcrex'] ?>";
				 	document.forms['frgrm']['cHorCre'].value   = "<?php echo $vDatCte['reghcrex'] ?>";
				 	document.forms['frgrm']['dFecMod'].value   = "<?php echo $vDatCte['regfmodx'] ?>";
				 	document.forms['frgrm']['cHorMod'].value   = "<?php echo $vDatCte['reghmodx'] ?>";
				 	document.forms['frgrm']['cEstado'].value   = "<?php echo $vDatCte['regestxx'] ?>";
				</script>
			<?php }
		} ?>
	</body>
</html>
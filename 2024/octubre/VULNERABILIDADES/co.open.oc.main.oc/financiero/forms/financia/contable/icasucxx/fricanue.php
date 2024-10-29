<?php
  namespace openComex;
/**
	 * Proceso Ica x Sucursales.
	 * --- Descripcion: Permite Editar Ica x Sucursales.
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

    	function f_Links(xLink,xSwitch) {
				var zX    = screen.width;
				var zY    = screen.height;
				
				switch (xLink) {
					case "cPucId":
					case "cPucId2":
						if (xSwitch == "VALID") {
							var zRuta  = "frpar115.php?gWhat=VALID&gFunction="+xLink+"&cPucId="+document.forms['frgrm'][xLink].value;
							parent.fmpro.location = zRuta;
						} else {
  		  			var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpar115.php?gWhat=WINDOW&gFunction="+xLink+"&cPucId="+document.forms['frgrm'][xLink].value;
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
					case "cCiuId":
					case "cCiuDes":
						if (xSwitch == "VALID") {
							var zRuta  = "frica055.php?gWhat=VALID&gFunction="+xLink+"&cCiuId="+document.forms['frgrm'][xLink].value;
							parent.fmpro.location = zRuta;
						} else {
  		  			var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frica055.php?gWhat=WINDOW&gFunction="+xLink+"&cCiuId="+document.forms['frgrm'][xLink].value
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
				  case "cSucId":
					case "cSucDes":
						if (xSwitch == "VALID") {
							var zRuta  = "frpar008.php?gWhat=VALID&gFunction="+xLink+"&cSucId="+document.forms['frgrm'][xLink].value;
							parent.fmpro.location = zRuta;
						} else {
  		  			var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpar008.php?gWhat=WINDOW&gFunction="+xLink+"&cSucId="+document.forms['frgrm'][xLink].value
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
			<table border ="0" cellpadding="0" cellspacing="0" width="40">
				<tr>
					<td>
				  	<fieldset>
					  	<legend><?php echo $_COOKIE['kMenDes'] ?></legend>
						 	<form name = 'frgrm' action = 'fricagra.php' method = 'post' target='fmpro'>
						 	  <input type = 'hidden' name = 'cCadena' style='width:700px' readonly>
							 	<center>
							 	 <table border = '0' cellpadding = '0' cellspacing = '0' width='400'>
  							 			<?php $nCol = f_Format_Cols(20);
  							 			echo $nCol;?>
  							 			<tr>
  											<td Class = "clase08" colspan = "4">Id<br>
  													<input type = 'text' Class = 'letra' style = 'width:80;text-align:center' name = 'cPaiId' readonly>
  											</td>
  											<td Class = 'clase08' colspan = '16'>Pais<br>
  												<input type = 'text' Class = 'letra' style = 'width:320' name = 'cPaiDes' readonly>
  											</td>
  										</tr>
  										<tr>
  											<td Class = "clase08" colspan = "4">Id<br>
  													<input type = 'text' Class = 'letra' style = 'width:80;text-align:center' name = 'cDepId' readonly>
  											</td>
  											<td Class = 'clase08' colspan = '16'>Departamento<br>
  												<input type = 'text' Class = 'letra' style = 'width:320' name = 'cDepDes' readonly>
  											</td>
  										</tr>
  										<tr>
  											<td Class = "clase08" colspan = "4">
  											<a href = "javascript:document.frgrm.cPaiId.value  = '';
																			  		  document.frgrm.cPaiDes.value = '';
																			  		  document.frgrm.cDepId.value  = '';
																			  		  document.frgrm.cDepDes.value = '';
																			  		  document.frgrm.cCiuId.value  = '';
																			  		  document.frgrm.cCiuDes.value = '';
																							f_Links('cCiuId','VALID');" id="IdCui">Id</a><br>
  													<input type = 'text' Class = 'letra' style = 'width:80;text-align:center' name = 'cCiuId' maxlength="10"
  							 						onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         f_Links('cCiuId','VALID');
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:document.frgrm.cPaiId.value  = '';
  																			  		  document.frgrm.cPaiDes.value = '';
  																			  		  document.frgrm.cDepId.value  = '';
  																			  		  document.frgrm.cDepDes.value = '';
  																			  		  document.frgrm.cCiuId.value  = '';
  																			  		  document.frgrm.cCiuDes.value = '';
  														                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td>
  											<td Class = 'clase08' colspan = '1'><br>
                          <input type = 'text' Class = 'letra' style = 'width:20;text-align:center' readonly>
                        </td>
  											<td Class = 'clase08' colspan = '15'>Ciudad<br>
  												<input type = 'text' Class = 'letra' style = 'width:300' name = 'cCiuDes' maxlength="100"
  										    	onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         f_Links('cCiuDes','VALID');
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td>
  										</tr>
  										<tr>
  											<td Class = "clase08" colspan = "4">
  											 <a href = "javascript:document.frgrm.cSucId.value  = '';
  																			  		 document.frgrm.cSucDes.value = '';
  																			  		 document.frgrm.cCcoId.value = '';
																							 f_Links('cSucId','VALID');" id="IdSuc">Id</a><br>
  													<input type = 'text' Class = 'letra' style = 'width:80;text-align:center' name = 'cSucId' maxlength="10"
  							 						onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         f_Links('cSucId','VALID');
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:document.frgrm.cSucId.value  = '';
  																			  		  document.frgrm.cSucDes.value = '';
  																			  		  document.frgrm.cCcoId.value = '';
  														                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td>
  											<td Class = 'clase08' colspan = '1'><br>
  											  <input type = 'text' Class = 'letra' style = 'width:20;text-align:center' readonly>
                        </td>
  											<td Class = 'clase08' colspan = '11'>Sucursal<br>
  												<input type = 'text' Class = 'letra' style = 'width:220' name = 'cSucDes'
  										    	onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         f_Links('cSucDes','VALID');
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td>
  											<td Class = 'clase08' colspan = '4'>Centro Costo<br>
  												<input type = 'text' Class = 'letra' style = 'width:80;text-align:center' name = 'cCcoId' maxlength="10" readonly>
  											</td>
  										</tr>
  										<tr>
  								   		<td Class = "name" colspan = "20">
  								   		 <fieldset>
  								   		   <legend>Retenci&oacute;n ICA</legend>
  								   		      <table border = '0' cellpadding = '0' cellspacing = '0' width='380'>
            							 			<?php $nCol = f_Format_Cols(19);
            							 			echo $nCol;?>
            							 			<tr>
                                  <td Class = "clase08" colspan = "5">
                                    <a href = "javascript:document.frgrm.cPucId.value  = '';
                                                          document.frgrm.cPucDes.value = '';
                                                          document.frgrm.cCiuIca.value = '';
																							            f_Links('cPucId','VALID');" id="IdPuc">Cuenta PUC</a><br>
                                    <input type = 'text' Class = 'letra' style = 'width:100' name = 'cPucId'  maxlength="10"
                                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                                    f_Links('cPucId','VALID');
                                                    this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                          onFocus="javascript:document.frgrm.cPucId.value  = '';
                                                              document.frgrm.cPucDes.value = '';
                                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                                  </td>
                                  <td Class = 'clase08' colspan = '9'>Descripci&oacute;n<br>
            												<input type = 'text' Class = 'letra' style = 'width:180' name = 'cPucDes' readonly>
            											</td>
            											<td Class = 'clase08' colspan = '5'>Tarifa ICA<br>
            												<input type = 'text' Class = 'letra' style = 'width:100;text-align:right' name = 'cCiuIca'
                  												onBlur = "javascript:this.value=this.value.toUpperCase();
                                                               f_FixFloat(this);
        																			                 this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
        										    	        onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
            											</td>
            							 			</tr>
            							 		</table>
  								   		 </fieldset>
  								   		</td>
  								   	</tr>
  								   	<tr>
  								   		<td Class = "name" colspan = "20">
  								   		 <fieldset>
  								   		   <legend>Autoretenci&oacute;n ICA</legend>
  								   		      <table border = '0' cellpadding = '0' cellspacing = '0' width='380'>
            							 			<?php $nCol = f_Format_Cols(19);
            							 			echo $nCol;?>
            							 			<tr>
                                  <td Class = "clase08" colspan = "5">
                                    <a href = "javascript:document.frgrm.cPucId2.value  = '';
                                                          document.frgrm.cPucDes2.value = '';
                                                          document.frgrm.cCiuIca2.value = '';
																							            f_Links('cPucId2','VALID');" id="IdPuc2">Cuenta PUC</a><br>
                                    <input type = 'text' Class = 'letra' style = 'width:100' name = 'cPucId2'  maxlength="10"
                                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                                    f_Links('cPucId2','VALID');
                                                    this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                          onFocus="javascript:document.frgrm.cPucId2.value  = '';
                                                   document.frgrm.cPucDes2.value = '';
                                                   this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                                  </td>
                                  <td Class = 'clase08' colspan = '9'>Descripci&oacute;n<br>
            												<input type = 'text' Class = 'letra' style = 'width:180' name = 'cPucDes2' readonly>
            											</td>
            											<td Class = 'clase08' colspan = '5'>Tarifa ICA<br>
            												<input type = 'text' Class = 'letra' style = 'width:100;text-align:right' name = 'cCiuIca2'
                  												onBlur = "javascript:this.value=this.value.toUpperCase();
                                                               f_FixFloat(this);
        																			                 this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
        										    	        onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
            											</td>
            							 			</tr>
            							 		</table>
  								   		 </fieldset><br>
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
			<table border="0" cellpadding="0" cellspacing="0" width="400">
				<tr height="21">
					<?php switch ($_COOKIE['kModo']) {
						case "VER": ?>
							<td width="309" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
						default: ?>
							<td width="218" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:f_EnabledCombos();document.forms['frgrm'].submit();f_DisabledCombos();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
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
				f_CargaData($cPaiId,$cDepId,$cCiuId);
				?>
				<script languaje = "javascript">
				  document.getElementById('IdCui').href  = "javascript:alert('Opcion No Permitida')";
					document.forms['frgrm']['cCiuId'].readOnly = true;
          document.forms['frgrm']['cCiuId'].onfocus  = "";
          document.forms['frgrm']['cCiuId'].onblur   = "";
          document.forms['frgrm']['cCiuDes'].readOnly = true;
          document.forms['frgrm']['cCiuDes'].onfocus  = "";
          document.forms['frgrm']['cCiuDes'].onblur   = "";
				</script>
			<?php break;
			case "VER":
				f_CargaData($cPaiId,$cDepId,$cCiuId); ?>
				<script languaje = "javascript">
					for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
          }
          document.getElementById('IdCui').href  = "javascript:alert('Opcion No Permitida')";
          document.getElementById('IdSuc').href  = "javascript:alert('Opcion No Permitida')";
          document.getElementById('IdPuc').href  = "javascript:alert('Opcion No Permitida')";
          document.getElementById('IdPuc2').href = "javascript:alert('Opcion No Permitida')";
				</script>
			<?php break;
		} ?>

		<?php function f_CargaData($xPaiId,$xDepId,$xCiuId) {
		  global $cAlfa; global $xConexion01;

		  /* TRAIGO DATOS DE CABECERA*/
      $qDatTer  = "SELECT * ";
		  $qDatTer .= "FROM $cAlfa.SIAI0055 ";
		  $qDatTer .= "WHERE ";
		  $qDatTer .= "PAIIDXXX = \"$xPaiId\" AND ";
		  $qDatTer .= "DEPIDXXX = \"$xDepId\" AND ";
		  $qDatTer .= "CIUIDXXX = \"$xCiuId\" AND ";
      $qDatTer .= "REGESTXX = \"ACTIVO\" ";
		  $qDatTer .= "ORDER BY ABS(CIUIDXXX) ";
		  $xDatTer = f_MySql("SELECT","",$qDatTer,$xConexion01,"");


		 	/* EMPIEZO A RECORRER EL CURSOR DE CABECERA */
			while ($xRDT = mysql_fetch_array($xDatTer)) {
			 /**  Buscando Pais */
			  $qPaiDes = "SELECT PAIDESXX FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"$xPaiId\" LIMIT 0,1";
			  $xPaiDes  = f_MySql("SELECT","",$qPaiDes,$xConexion01,"");
        $xRPD  = mysql_fetch_array($xPaiDes);
        if($xRPD['PAIDESXX'] <> ''){
          $xRDT['PAIDESXX'] = $xRPD['PAIDESXX'];
        }else {
          $xRDT['PAIDESXX'] = "PAIS SIN DESCRIPCION";
        }
        
        /**  Buscando Departamento */
			  $qDepDes = "SELECT DEPDESXX FROM $cAlfa.SIAI0054 WHERE PAIIDXXX = \"$xPaiId\" AND DEPIDXXX = \"$xDepId\" LIMIT 0,1";
			  $xDepDes  = f_MySql("SELECT","",$qDepDes,$xConexion01,"");
			  $xRDD  = mysql_fetch_array($xDepDes);
        if($xRDD['DEPDESXX'] <> ''){
          $xRDT['DEPDESXX'] = $xRDD['DEPDESXX'];
        }else {
          $xRDT['DEPDESXX'] = "DEPARTAMENTO SIN DESCRIPCION";
        }
        
        
        /**  Buscando Sucursal */
			  $qSucDes = "SELECT sucdesxx FROM $cAlfa.fpar0008 WHERE sucidxxx = \"{$xRDT['SUCIDXXX']}\" AND ccoidxxx = \"{$xRDT['CCOIDXXX']}\" LIMIT 0,1";
			  $xSucDes  = f_MySql("SELECT","",$qSucDes,$xConexion01,"");
			  $xRSD  = mysql_fetch_array($xSucDes);
        if($xRSD['sucdesxx'] <> ''){
          $xRDT['SUCDESXX'] = $xRSD['sucdesxx'];
        }else {
          $xRDT['SUCDESXX'] = "SUCURSAL SIN DESCRIPCION";
        }
			 
			 /*Traigo la Descripcion de la cuenta PUC de Retencion*/
        $qDesPuc  = "SELECT pucdesxx ";
        $qDesPuc .= "FROM $cAlfa.fpar0115 ";
        $qDesPuc .= "WHERE ";
        $qDesPuc .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx)= \"{$xRDT['PUCIDXXX']}\" LIMIT 0,1";
        $xDesPuc  = f_MySql("SELECT","",$qDesPuc,$xConexion01,"");
        $xRDP  = mysql_fetch_array($xDesPuc);
        if($xRDT['PUCIDXXX'] <> ""){
          $xRDT['PUCDESXX'] = ($xRDP['pucdesxx'] != "") ? trim($xRDP['pucdesxx']) : "CUENTA SIN DESCRIPCION";
        }
        
        /*Traigo la Descripcion de la cuenta PUC de AutoRetencion*/
        $qDesPuc  = "SELECT pucdesxx ";
        $qDesPuc .= "FROM $cAlfa.fpar0115 ";
        $qDesPuc .= "WHERE ";
        $qDesPuc .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx)= \"{$xRDT['PUCID2XX']}\" LIMIT 0,1";
        $xDesPuc  = f_MySql("SELECT","",$qDesPuc,$xConexion01,"");
        $xRDP  = mysql_fetch_array($xDesPuc);
        if($xRDT['PUCID2XX'] <> ""){
          $xRDT['PUCDES2X'] = ($xRDP['pucdesxx'] != "") ? trim($xRDP['pucdesxx']) : "CUENTA SIN DESCRIPCION";
        }
        
        if($xRDT['CIUICA2X']==0){
          $xRDT['CIUICA2X'] = '';
        }
				?>
				<script language = "javascript">
				  document.forms['frgrm']['cPaiId'].value		 = "<?php echo $xRDT['PAIIDXXX'] ?>";
					document.forms['frgrm']['cPaiDes'].value   = "<?php echo $xRDT['PAIDESXX'] ?>";
					document.forms['frgrm']['cDepId'].value		 = "<?php echo $xRDT['DEPIDXXX'] ?>";
					document.forms['frgrm']['cDepDes'].value   = "<?php echo $xRDT['DEPDESXX'] ?>";
					document.forms['frgrm']['cCiuId'].value    = "<?php echo $xRDT['CIUIDXXX'] ?>";
					document.forms['frgrm']['cCiuDes'].value   = "<?php echo $xRDT['CIUDESXX'] ?>";
					document.forms['frgrm']['cSucId'].value    = "<?php echo $xRDT['SUCIDXXX'] ?>";
					document.forms['frgrm']['cSucDes'].value   = "<?php echo $xRDT['SUCDESXX'] ?>";
					document.forms['frgrm']['cCcoId'].value    = "<?php echo $xRDT['CCOIDXXX'] ?>";
					document.forms['frgrm']['cPucId'].value    = "<?php echo $xRDT['PUCIDXXX'] ?>";
					document.forms['frgrm']['cPucDes'].value   = "<?php echo $xRDT['PUCDESXX'] ?>";
					document.forms['frgrm']['cCiuIca'].value   = "<?php echo $xRDT['CIUICAXX'] ?>";
					document.forms['frgrm']['cPucId2'].value   = "<?php echo $xRDT['PUCID2XX'] ?>";
					document.forms['frgrm']['cPucDes2'].value  = "<?php echo $xRDT['PUCDES2X'] ?>";
					document.forms['frgrm']['cCiuIca2'].value  = "<?php echo $xRDT['CIUICA2X'] ?>";
		      document.forms['frgrm']['dFecCre'].value   = "<?php echo $xRDT['REGFECXX'] ?>";
				 	document.forms['frgrm']['cHorCre'].value   = "<?php echo $xRDT['REGHORXX'] ?>";
				 	document.forms['frgrm']['dFecMod'].value   = "<?php echo $xRDT['REGMODXX'] ?>";
				 	document.forms['frgrm']['cHorMod'].value   = "<?php echo $xRDT['REGHORXX'] ?>";
				 	document.forms['frgrm']['cEstado'].value   = "<?php echo $xRDT['REGESTXX'] ?>";
				</script>
			<?php }
		} ?>
	</body>
</html>
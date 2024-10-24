<?php
  namespace openComex;

/**
	 * Proceso Sucursales.
	 * --- Descripcion: Permite Crear una Sucursal.
	 * @author
	 * @package emisioncero
	 * @version 001
	 */
	include("../../../../libs/php/utility.php");
	include("../../../../libs/php/utiliqdo.php");
	include("../../../../libs/php/uticonta.php");
?>
<html>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script language="javascript">
	  	function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
  	  }

	  	function f_Links(xLink,xSwitch,xIteration) {
			var zX    = screen.width;
			var zY    = screen.height;
			switch (xLink){
				case "cDocId":
					if (xSwitch == "VALID") {
						var zRuta  = "frautcup.php?gWhat=VALID&gFunction=cDocId&cDocId="+document.frgrm.cDocId.value.toUpperCase()+"";
						parent.fmpro.location = zRuta;
					} else {
	  				var zNx     = (zX-400)/2;
						var zNy     = (zY-250)/2;
						var zWinPro = 'width=400,scrollbars=1,height=250,left='+zNx+',top='+zNy;
						var zRuta   = "frautcup.php?gWhat=WINDOW&gFunction=cDocId&cDocId="+document.frgrm.cDocId.value.toUpperCase()+"";
						zWindow = window.open(zRuta,"zWindow",zWinPro);
				  	zWindow.focus();
					}
			  break;
			   }
		}

			function f_ValidacEstado(){
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

    	function f_Imprimir() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
    		if(document.forms['frgrm']['cSucId'].value.length   > 0 &&
    			 document.forms['frgrm']['cDocTip'].value.length  > 0 &&
    			 document.forms['frgrm']['cDocId'].value.length   > 0 &&
    			 document.forms['frgrm']['cDocSuf'].value.length  > 0 &&
    			 document.forms['frgrm']['cCliId'].value.length   > 0 &&
    			 document.forms['frgrm']['cCcoId'].value.length   > 0 &&
    			 document.forms['frgrm']['dRegFCre'].value.length > 0 ){

  				var cRuta = "../../../financia/contable/movimido/frmdoprn.php?"+
					  					"gComId="+document.forms['frgrm']['cComId'].value+
							        "&gComCod="+document.forms['frgrm']['cComCod'].value+
							        "&gSucId="+document.forms['frgrm']['cSucId'].value+
							        "&gDocTip="+document.forms['frgrm']['cDocTip'].value+
							        "&gDocId="+document.forms['frgrm']['cDocId'].value+
							        "&gDocSuf="+document.forms['frgrm']['cDocSuf'].value+
							        "&gPucId="+document.forms['frgrm']['cPucId'].value+
							        "&gCcoId="+document.forms['frgrm']['cCcoId'].value+
							        "&gCliId="+document.forms['frgrm']['cCliId'].value+
							        "&gRegFCre="+document.forms['frgrm']['dRegFCre'].value+
							        "&gMov=CONCEPTO"+
							        "&gPyG=1";

	  			var zX    = screen.width;
					var zY    = screen.height;
					var zNx     = (zX-1100)/2;
					var zNy     = (zY-700)/2;
		  		var zWinPro = 'width=1100,scrollbars=1,height=700,left='+zNx+',top='+zNy;
		  		var cNomVen = 'zWindow'+Math.ceil(Math.random()*1000);
          console.log(cRuta);
    			// cWindow = window.open(cRuta,cNomVen,zWinPro);
    			// cWindow.focus();

  			} else {
  				alert("El Numero del DO esta Vacio, Verifique");
  			}
	  	}
    	</script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="600">
				<tr>
					<td>
				  	<fieldset>
					  	<legend><?php echo $_COOKIE['kModo']." ".$_COOKIE['kProDes'] ?></legend>
						 	<form name = 'frgrm' action = 'fraccgra.php' method = 'post' target='fmpro'>
						 	  <input type = "hidden" name = "cComId"   readonly>
						 	  <input type = "hidden" name = "cComCod"  readonly>
						 	  <input type = "hidden" name = "cPucId"   readonly>
						 	  <input type = "hidden" name = "cCcoId"   readonly>
						 	  <input type = "hidden" name = "dRegFCre" readonly>
							 	<center>
      							<table border = '0' cellpadding = '0' cellspacing = '0' width='600'>
  							 			<?php $nCol = f_Format_Cols(30);
  							 			echo $nCol;?>
  							 			<tr>
  										  <td Class = "clase08" colspan = "5">
  												<a href = "javascript:document.frgrm.cDocId.value  = '';
  																							document.frgrm.cDocSuf.value = '';
  																							document.frgrm.cSucId.value = '';
  																							document.frgrm.cDocPed.value = '';
  																							document.frgrm.cDocTip.value = '';
  																							document.frgrm.cCliId.value = '';
  																							document.frgrm.cCliDV.value = '';
  																			  		  document.frgrm.cCliNom.value = '';
  																							document.frgrm.cCliCupTiNom.value = '';
  																							document.frgrm.cCliCupCl.value = '';
  																							document.frgrm.cCliCupOp.value = '';
  																							document.frgrm.cSalDo.value = '';
  																							document.frgrm.cCupAut.value = '';
  																							document.frgrm.cAutFac.value = '';
  																							document.forms['frgrm']['cComId'].value ='';
																								document.forms['frgrm']['cComCod'].value ='';
																								document.forms['frgrm']['cPucId'].value ='';
																								document.forms['frgrm']['cCcoId'].value ='';
																								document.forms['frgrm']['dRegFCre'].value ='';
  																			  		  if(document.forms['frgrm']['cDocId'].value != ''){
  							 																	f_Links('cDocId','VALID');
  																			          this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'
  							 																}else{
  							 																	alert('Debe Digitar al Menos Dos Digitos del DO');
  							 																}";
  																							 id="DocId"> DO </a><br>
  												<input type = 'text' Class = 'letra' style = 'width:100' name = 'cDocId' maxlength="20"
  							 						onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         if(document.forms['frgrm']['cDocId'].value != ''){
  							 																	f_Links('cDocId','VALID');
  																			          this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'
  							 																}else{
  							 																	alert('Debe Digitar al Menos Dos Digitos del DO');
  							 																}"
  										    	onFocus="javascript:document.frgrm.cDocId.value  = '';
  										    											document.frgrm.cDocSuf.value = '';
  																			  		  document.frgrm.cSucId.value = '';
  																			  		  document.frgrm.cDocPed.value = '';
  																			  		  document.frgrm.cDocTip.value = '';
  																			  		  document.frgrm.cCliId.value = '';
  																			  		  document.frgrm.cCliDV.value = '';
  																			  		  document.frgrm.cCliNom.value = '';
  																			  		  document.frgrm.cCliCupTiNom.value = '';
  																			  		  document.frgrm.cCliCupCl.value = '';
  																			  		  document.frgrm.cCliCupOp.value = '';
  																			  		  document.frgrm.cSalDo.value = '';
  																			  		  document.frgrm.cCupAut.value = '';
  																			  		  document.frgrm.cAutFac.value = '';
  																			  		  document.forms['frgrm']['cComId'].value ='';
																								document.forms['frgrm']['cComCod'].value ='';
																								document.forms['frgrm']['cPucId'].value ='';
																								document.forms['frgrm']['cCcoId'].value ='';
																								document.forms['frgrm']['dRegFCre'].value ='';
  														                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  											</td>
  											<td Class = "clase08" colspan = "04">Sufijo<br>
  												<input type = "text" Class = "letra" style = "width:080" name = "cDocSuf" readonly>
  											</td>
  											<td Class = "clase08" colspan = "06">Sucursal<br>
  												<input type = "text" Class = "letra" style = "width:120" name = "cSucId" readonly>
  											</td>
  											<td Class = "clase08" colspan = "06">Pedido<br>
  												<input type = "text" Class = "letra" style = "width:120" name = "cDocPed" readonly>
  											</td>
  											<td Class = "clase08" colspan = "09">Operaci&oacute;n<br>
  												<input type = "text" Class = "letra" style = "width:180" name = "cDocTip" readonly>
  											</td>
                      </tr>
  							 			<tr>
  										  <td Class = "clase08" colspan = "09">Nit<br>
  											<input type = "text" Class = "letra" style = "width:180" name = "cCliId" readonly>

  											</td>
  											<td Class = "clase08" colspan = "01">Dv<br>
  												<input type = "text" Class = "letra" style = "width:20" name = "cCliDV" readonly>
  											</td>
  											<td Class = "clase08" colspan = "20">Cliente<br>
  												<input type = "text" Class = "letra" style = "width:400" name = "cCliNom" readonly>
  											</td>
                      </tr>
                      <tr>
  											<td Class = "clase08" colspan = "8">Tipo Cupo<br>
  											 <input type = "text" Class = "letra" style = "width:160" name = "cCliCupTiNom" readonly>
  											 <input type = "hidden" name = "cCliCupTi" readonly>
                        </td>
                        <td Class = "clase08" colspan = "8">Cupo por Cliente<br>
  										 		<input type = "text" Class = "letra" style = "width:160;text-align:right" name = "cCliCupCl" readonly>
                        </td>
                        <td Class = "clase08" colspan = "7">Cupo por Operaci&oacute;n<br>
  										 		<input type = "text" Class = "letra" style = "width:140;text-align:right" name = "cCliCupOp" readonly>
                        </td>
                        <td Class = "clase08" colspan = "7">
  												<a href='javascript:f_Imprimir()'>Saldo por Operaci&oacute;n</a><br>
  												<input type = "text" Class = "letra" style = "width:140;text-align:right" name = "cSalDo" readonly>
  											</td>
                      </tr>
                      <tr>
  											<td Class = "clase08" colspan = "8">Cupo Autorizado<br>
  											 <input type = "text" Class = "letra" style = "width:140;text-align:right" name = "cCupAut"
  													onBlur = "javascript:f_FixInt(this);
  																	             this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  									    	  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                        <td Class = "clase08" colspan = "22"><br>Se Autoriza Facturar&nbsp;&nbsp;
  										 		<select class="letrase" name = "cAutFac" style = "width:050">
                          	<option value = "SI">SI</option>
    			                	<option value = "NO" selected>NO</option>
  			                  </select>
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
			<table border="0" cellpadding="0" cellspacing="0" width="600">
				<tr height="21">
					<?php switch ($_COOKIE['kModo']) {
						case "VER": ?>
							<td width="509" height="21"></td>
							<td width="91" height="21" Class="clase08" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
						default: ?>
							<td width="418" height="21"></td>
							<td width="91" height="21" Class="clase08" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:document.forms['frgrm'].submit();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Autorizar</td>
							<td width="91" height="21" Class="clase08" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
			  	} ?>
				</tr>
			</table>
		</center>
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<?php
		// f_Mensaje(__FILE__,__LINE__,$_COOKIE['kModo']);
		switch ($_COOKIE['kModo']) {
			case "EDITAR":
				f_CargaData($cSucId,$cDocId,$cDocSuf);
				?>
				<script language = "javascript">
					document.forms['frgrm']['cDocId'].readOnly	= true;
					document.forms['frgrm']['cDocId'].onfocus   = "";
				 	document.forms['frgrm']['cDocId'].onblur    = "";
					document.getElementById('DocId').disabled=true;
				 	document.getElementById('DocId').href="#";
				</script>
				<?php
			break;
		} ?>

		<?php function f_CargaData($xSucId,$xDocId,$xDocSuf) {
		  global $cAlfa; global $xConexion01;

		  $qDatDo  = "SELECT * ";
		  $qDatDo .= "FROM $cAlfa.sys00121 ";
			$qDatDo .= "WHERE ";
			$qDatDo .= "sucidxxx = \"$xSucId\"  AND ";
			$qDatDo .= "docidxxx = \"$xDocId\"  AND ";
			$qDatDo .= "docsufxx = \"$xDocSuf\" AND ";
			$qDatDo .= "regestxx = \"ACTIVO\" ";
			//wMenssage(__FILE__,__LINE__,$qDatDo);
			$xDatDo = mysql_query($qDatDo,$xConexion01);
      $xRDD = mysql_fetch_array($xDatDo);

		  $qCliDat  = "SELECT *, ";
			$qCliDat .= "IF(TRIM(CONCAT(CLINOMXX,' ',CLIAPE1X,' ',CLIAPE2X,' ',CLINOM1X,' ',CLINOM2X)) <> \"\",TRIM(CONCAT(CLINOMXX,' ',CLIAPE1X,' ',CLIAPE2X,' ',CLINOM1X,' ',CLINOM2X)),\"CLIENTE SIN NOMBRE\") AS NOMBREXX ";
			$qCliDat .= "FROM $cAlfa.SIAI0150 ";
      $qCliDat .= "WHERE ";
      $qCliDat .= "CLIIDXXX = \"{$xRDD['cliidxxx']}\" AND ";
			$qCliDat .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
			$xCliDat = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
			$xRCD = mysql_fetch_array($xCliDat);
			$cCupNom = $xRCD['CLICUPTI'];
			switch ($xRCD['CLICUPTI']){
				 case "LIMITADO":
				    $xRCD['CLICUPCL'] = number_format($xRCD['CLICUPCL'],0,',','');
			      $xRCD['CLICUPOP'] = number_format($xRCD['CLICUPOP'],0,',','');
				 break;
				 case "ILIMITADO":
				   $xRCD['CLICUPCL'] = "";
			     $xRCD['CLICUPOP'] = "";
				 break;
				 case "LIMITADO/ILIMITADO":
				   $xRCD['CLICUPCL'] = number_format($xRCD['CLICUPCL'],0,',','');
			     $xRCD['CLICUPOP'] = "";
				 break;
				 case "ILIMITADO/LIMITADO":
				   $xRCD['CLICUPCL'] = "";
			     $xRCD['CLICUPOP'] = number_format($xRCD['CLICUPOP'],0,',','');
				 break;
         case "SINCUPO":
         default:
           $xRCD['CLICUPTI'] = "SINCUPO";
           $xRCD['CLICUPCL'] = number_format(0,0,',','.');
           $xRCD['CLICUPOP'] = number_format(0,0,',','.');
           $cCupNom = "SIN CUPO";
         break;
		  }

			$cSaldo = f_Traer_Cupos_Financieros($xRCD['CLIIDXXX'],$xRDD['docidxxx'],$xRDD['docsufxx']);

			?>
			<script language = "javascript">
			  document.forms['frgrm']['cDocId'].value		 = "<?php echo $xRDD['docidxxx'] ?>";
			  document.forms['frgrm']['cDocSuf'].value	 = "<?php echo $xRDD['docsufxx'] ?>";
			  document.forms['frgrm']['cSucId'].value	   = "<?php echo $xRDD['sucidxxx'] ?>";
			  document.forms['frgrm']['cDocPed'].value	 = "<?php echo $xRDD['docpedxx'] ?>";
			  document.forms['frgrm']['cDocTip'].value	 = "<?php echo $xRDD['doctipxx'] ?>";
			  document.forms['frgrm']['cComId'].value    = "<?php echo $xRDD['comidxxx'] ?>";
				document.forms['frgrm']['cComCod'].value   = "<?php echo $xRDD['comcodxx'] ?>";
				document.forms['frgrm']['cPucId'].value    = "<?php echo $xRDD['pucidxxx'] ?>";
				document.forms['frgrm']['cCcoId'].value    = "<?php echo $xRDD['ccoidxxx'] ?>";
				document.forms['frgrm']['dRegFCre'].value  = "<?php echo $xRDD['regfcrex'] ?>";
				document.forms['frgrm']['cCupAut'].value	 = "<?php echo number_format($xRDD['doccupxx'],0,',','') ?>";
			  if("<?php echo $xRDD['doccupaf'] ?>" != ""){
			   document.forms['frgrm']['cAutFac'].value	 = "<?php echo $xRDD['doccupaf'] ?>";
			  }
			  document.forms['frgrm']['cCliId'].value		   = "<?php echo $xRCD['CLIIDXXX'] ?>";
			  document.forms['frgrm']['cCliDV'].value		   = "<?php echo f_Digito_Verificacion($xRCD['CLIIDXXX']) ?>";
			  document.forms['frgrm']['cCliNom'].value	   = "<?php echo $xRCD['NOMBREXX'] ?>";
	      document.forms['frgrm']['cCliCupTi'].value   = "<?php echo $xRCD['CLICUPTI'] ?>";
	      document.forms['frgrm']['cCliCupTiNom'].value= "<?php echo $cCupNom ?>";
			 	document.forms['frgrm']['cCliCupCl'].value   = "<?php echo $xRCD['CLICUPCL'] ?>";
			 	document.forms['frgrm']['cCliCupOp'].value   = "<?php echo $xRCD['CLICUPOP'] ?>";
			 	document.forms['frgrm']['cSalDo'].value      = "<?php echo $cSaldo ?>";
			</script>
	 <?php } ?>
	</body>
</html>

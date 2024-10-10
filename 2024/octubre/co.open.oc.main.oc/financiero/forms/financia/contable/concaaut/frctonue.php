<?php
  namespace openComex;
 /**
	 * Proceso Conceptos Contables Causaciones Automaticas
	 * --- Descripcion: Permite Crear un Nuevo Concepto Contables.
	 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
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

			function f_Valida_Check() {
			  switch (document.frgrm.gIteration.value) {
  			  case "1":
  			    if(document.frgrm.oCheck.checked == true && (document.frgrm.oCheckD.checked != true && document.frgrm.oCheckC.checked != true)){
  					  alert('No Puede Existir un Comprobante sin parametrizacion de Debitos y Creditos, Verifique');
  					}else{
  					  if (document.frgrm.oCheck.checked != true){
  					    alert('Debe Seleccionar al menos un Comprobante, Verifique');
  					  }else{
  					    document.forms['frgrm'].submit();
  					  }
            }
          break;
  				default:
  				  var band=0;
  				  for (i=0;i<document.frgrm.oCheck.length;i++){
  						if(document.frgrm.oCheck[i].checked == true && (document.frgrm.oCheckD[i].checked != true && document.frgrm.oCheckC[i].checked != true)){
    					  band=1;
   					  }
  				  }
  				  if(band==0){
  					  document.frgrm.cComMemoApl.value="";
  						document.forms['frgrm'].submit();
  					}else{
  					  alert('No Puede Existir un Comprobante sin parametrizacion de Debitos y Creditos, Verifique');
  					}
  			  break;
  			}
			}

			function f_Activar_Check() {
        switch (document.frgrm.gIteration.value) {
  			  case "1":
  			    if(document.frgrm.oCheck.checked == true){
    				  document.frgrm.oCheckD.disabled = false;
    				  document.frgrm.oCheckD.checked  = true;

    				  document.frgrm.oCheckC.disabled = true;
    				  document.frgrm.oCheckC.checked  = false;
    				}else{
    					document.frgrm.oCheckD.disabled = true;
              document.frgrm.oCheckD.checked  = false;

              document.frgrm.oCheckC.disabled = true;
              document.frgrm.oCheckC.checked  = false;
  				  }
			    break;
  				default:
  					for (i=0;i<document.frgrm.oCheck.length;i++) {
    				  if(document.frgrm.oCheck[i].checked == true){
    				    document.frgrm.oCheckD[i].disabled = false;
    				    document.frgrm.oCheckD[i].checked  = true;

    				    document.frgrm.oCheckC[i].disabled = true;
    				    document.frgrm.oCheckC[i].checked  = false;
    				  }else{
    					  document.frgrm.oCheckD[i].disabled = true;
                document.frgrm.oCheckD[i].checked  = false;

                document.frgrm.oCheckC[i].disabled = true;
                document.frgrm.oCheckC[i].checked  = false;
  				    }
  				  }
			   break;
  			}
			}

			function f_Carga_Data() {
			  var band=0;
	  	  document.frgrm.cComMemo.value="|";

		  	switch (document.frgrm.gIteration.value) {
  			  case "1":
  				  if (document.frgrm.oCheck.checked == true) {
  					  document.frgrm.cComMemo.value += document.frgrm.oCheck.value;
  					  if(document.frgrm.oCheckD.checked!=true && document.frgrm.oCheckC.checked!=true){
  						  band=1;
  					  }else{
  					    if(document.frgrm.oCheckD.checked==true && document.frgrm.oCheckC.checked!= true){
  					      document.frgrm.cComMemo.value +="~"+"D";
  					    }
  					    if(document.frgrm.oCheckC.checked==true && document.frgrm.oCheckD.checked!=true){
  					      document.frgrm.cComMemo.value +="~"+"C";
  					    }
  					    if(document.frgrm.oCheckC.checked==true && document.frgrm.oCheckD.checked==true){
    					    document.frgrm.cComMemo.value +="~";
    					  }
  					    document.frgrm.cComMemo.value+="|";
  					  }
  					}
  				break;
  				default:
  					var zSw_Prv = 0;
  					for (i=0;i<document.frgrm.oCheck.length;i++) {
  						if (document.frgrm.oCheck[i].checked == true && band==0) {
  							document.frgrm.cComMemo.value += document.frgrm.oCheck[i].value;
  							if(document.frgrm.oCheckD[i].checked!=true && document.frgrm.oCheckC[i].checked!=true){
    						  band=1;
    					  }else{
    					    if(document.frgrm.oCheckD[i].checked==true && document.frgrm.oCheckC[i].checked!=true){
    					      document.frgrm.cComMemo.value +="~"+"D";
    					    }
    					    if(document.frgrm.oCheckC[i].checked==true && document.frgrm.oCheckD[i].checked!=true){
    					      document.frgrm.cComMemo.value +="~"+"C";
    					    }
    					    if(document.frgrm.oCheckC[i].checked==true && document.frgrm.oCheckD[i].checked==true){
    					      document.frgrm.cComMemo.value +="~";
    					    }
                  document.frgrm.cComMemo.value+="|";
                }
  						}
  					}
  				break;
  			}
			  if (document.frgrm.cComMemo.value =="|") {
				  document.frgrm.cComMemo.value = "";
			  }
	  	}

      function f_Links(xLink,xSwitch,xIteration) {
				var zX    = screen.width;
				var zY    = screen.height;
				switch (xLink){
					case "cCacId":
						if (xSwitch == "VALID") {
							var zRuta  = "frcac144.php?gWhat=VALID&gFunction=cCacId&gCacId="+document.frgrm.cCacId.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else{
							if(xSwitch == "WINDOW"){
			  				var zNx     = (zX-560)/2;
								var zNy     = (zY-300)/2;
								var zWinPro = 'width=560,scrollbars=1,height=300,left='+zNx+',top='+zNy;
								var zRuta   = "frcac144.php?gWhat=WINDOW&gFunction=cCacId&gCacId="+document.frgrm.cCacId.value.toUpperCase()+"";
								zWindow = window.open(zRuta,"zWindow",zWinPro);
						  	zWindow.focus();
							}else{
	              if (xSwitch == "EXACT"){
	                var zRuta  = "frcac144.php?gWhat=EXACT&gFunction=cCacId&gCacId="+document.frgrm.cCacId.value.toUpperCase()+"";
	                parent.fmpro.location = zRuta;
	              }
	            }
            }
				  break;
				  case "cCacDes":
						if (xSwitch == "VALID") {
							var zRuta  = "frcac144.php?gWhat=VALID&gFunction=cCacDes&gCacDes="+document.frgrm.cCacDes.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else{
							if(xSwitch == "WINDOW"){
			  				var zNx     = (zX-560)/2;
								var zNy     = (zY-300)/2;
								var zWinPro = 'width=560,scrollbars=1,height=300,left='+zNx+',top='+zNy;
								var zRuta   = "frcac144.php?gWhat=WINDOW&gFunction=cCacDes&gCacDes="+document.frgrm.cCacDes.value.toUpperCase()+"";
								zWindow = window.open(zRuta,"zWindow",zWinPro);
						  	zWindow.focus();
							}else{
	              if (xSwitch == "EXACT"){
	                var zRuta  = "frcac144.php?gWhat=EXACT&gFunction=cCacDes&gCacDes="+document.frgrm.cCacDes.value.toUpperCase()+"";
	                parent.fmpro.location = zRuta;
	              }
	            }
            }
				  break;
  				case "cPucId":
  					if (xSwitch == "VALID") {
							var zRuta  = "frcto115.php?gWhat=VALID&gFunction="+xLink+"&cPucId="+document.forms['frgrm'][xLink].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
		  				var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frcto115.php?gWhat=WINDOW&gFunction="+xLink+"&cPucId="+document.forms['frgrm'][xLink].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
				  case "cCtoCtori":
						  var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpar119.php?gCtoCtori="+document.frgrm.cCtoCtori.value.toUpperCase()+"&gCampo=cCtoCtori";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
					break;
  				case "cCtoCtoRf":
  				case "cCtoCtrFs":
  				case "cCtoCtoRv":
  				case "cCtoCtoRc":
  				case "cCtoCtoCp":
  	  			if(document.forms['frgrm']['cComMemo'].value != "") {
							if (xSwitch == "VALID") {
								var zRuta  = "frcto119.php";
								document.forms['frnav']['gWhat'].value     = 'VALID';
	              document.forms['frnav']['gFunction'].value = xLink;
	              document.forms['frnav']['gCtoCto'].value   = document.forms['frgrm'][xLink].value.toUpperCase();
	              document.forms['frnav']['cComMemo'].value  = document.forms['frgrm']['cComMemo'].value;

	              document.forms['frnav'].target='fmpro';
	              document.forms['frnav'].action=zRuta;
	              document.forms['frnav'].submit();
							} else {
			  				var zNx     = (zX-600)/2;
								var zNy     = (zY-250)/2;
								var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;

								var zRuta   = "frcto119.php";
								document.forms['frnav']['gWhat'].value     = 'WINDOW';
						  	document.forms['frnav']['gFunction'].value = xLink;
						  	document.forms['frnav']['gCtoCto'].value   = document.forms['frgrm'][xLink].value.toUpperCase();
						  	document.forms['frnav']['cComMemo'].value  = document.forms['frgrm']['cComMemo'].value.toUpperCase();

						  	var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
			          zWindow = window.open('',cNomVen,zWinPro);

			          document.forms['frnav'].target=cNomVen;
			          document.forms['frnav'].action=zRuta;
			          document.forms['frnav'].submit();
							}
  	  			} else {
  	  	  			alert("Debe Seleccionar al menos un Comprobante.");
  	  			}
				  break;
					case "cCceId":
						if (xSwitch == "VALID") {
							var zRuta  = "frcto156.php?gWhat=VALID&gFunction="+xLink+"&gCceId="+document.forms['frgrm']['cCceId'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							if (xSwitch == "WINDOW") {
								var zNx     = (zX-600)/2;
								var zNy     = (zY-250)/2;

								var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
								var zRuta   = "frcto156.php?gWhat=WINDOW&gFunction="+xLink+"&gCceId="+document.forms['frgrm']['cCceId'].value.toUpperCase()+"";
								zWindow = window.open(zRuta,"zWindow",zWinPro);
								zWindow.focus();
							} 
							else {
								if(xSwitch == "EXACT"){
									var zRuta  = "frcto156.php?gWhat=EXACT&gFunction="+xLink+"&gCceId="+document.forms['frgrm']['cCceId'].value.toUpperCase()+"";
									parent.fmpro.location = zRuta;
								}
							}
						}
					break;
					case "cUmeId":
						if (xSwitch == "VALID") {
							var zRuta  = "frcto157.php?gWhat=VALID&gFunction="+xLink+"&gUmeId="+document.forms['frgrm']['cUmeId'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							if (xSwitch == "WINDOW") {
								var zNx     = (zX-600)/2;
								var zNy     = (zY-250)/2;

								var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
								var zRuta   = "frcto157.php?gWhat=WINDOW&gFunction="+xLink+"&gUmeId="+document.forms['frgrm']['cUmeId'].value.toUpperCase()+"";
								zWindow = window.open(zRuta,"zWindow",zWinPro);
								zWindow.focus();
							} else {
								if(xSwitch == "EXACT"){
									var zRuta  = "frcto157.php?gWhat=EXACT&gFunction="+xLink+"&gUmeId="+document.forms['frgrm']['cUmeId'].value.toUpperCase()+"";
									parent.fmpro.location = zRuta;
								}
							}
						}
					break;
		    }
			}

      function f_Cargar_Comprobantes(xTipo) {
    	  switch (xTipo) {
	    		case "PROPIOS":
	    			document.getElementById('ad_TipPropios').style.display = "block";
	    			document.getElementById('ad_TipTerceros').style.display= "none";
	    		break;
	    		default:
	    			document.forms['frgrm']['cCtoNit'].value = "CLIENTE";
	    			document.getElementById('ad_TipPropios').style.display = "none";
    				document.getElementById('ad_TipTerceros').style.display= "block";
	    		break;
	    	}
    	  var cRuta  = "frpar117.php?gTipo="+xTipo;
				parent.fmpro.location = cRuta;
      }

      function f_Mostrar_SucRetIca(){
    	  var cRuta  = "frsucica.php?gCtoCtori="+document.frgrm.cCtoCtori.value;
				parent.fmpro2.location = cRuta;
      }

      function f_Borrar_SucRetIca(xCtoId) {
          var cCtoCtori = "";
          var mCto = document.forms['frgrm']['cCtoCtori'].value.split("|");
          for (i=0;i<mCto.length;i++) {
             if(mCto[i] != xCtoId && mCto[i] != ''){
            	 cCtoCtori += mCto[i]+"|";
             }
          }
          document.forms['frgrm']['cCtoCtori'].value = cCtoCtori;
          f_Mostrar_SucRetIca();
      }

      function f_EnabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.forms['frgrm']['cPucId'].disabled =false;
				document.forms['frgrm']['cCtoTip'].disabled =false;
		  }

		  function f_DisabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.forms['frgrm']['cPucId'].disabled =true;
				document.forms['frgrm']['cCtoTip'].disabled =true;
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

			function fnHabilitarCodCompraEficiente() {
				if (document.forms['frgrm']['cCtoClapr'].value == 001) {
					document.forms['frgrm']['cCceId'].disabled = false;
					document.forms['frgrm']['cCceDes'].disabled = false;
					document.getElementById('idComEfi').href	 = "javascript:document.frgrm.cCceId.value  = ''; document.frgrm.cCceDes.value  = ''; f_Links('cCceId','VALID')";
				} else {
					document.forms['frgrm']['cCceId'].value    = "";
					document.forms['frgrm']['cCceId'].disabled = true;
					document.forms['frgrm']['cCceDes'].disabled = true;
					document.getElementById('idComEfi').href   = "javascript:alert('Opcion No Permitida')";
				}
			}
	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
	<form name = 'frnav' action = 'frcto119.php"' method = 'post' target='fmpro'>
    <input type="hidden" name="gWhat" value="" >
    <input type="hidden" name="gFunction" value="" >
    <input type="hidden" name="gCtoCto" value="" >
    <input type="hidden" name="cComMemo" value="" >
  </form>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="740">
				<tr>
					<td>
						<fieldset>
							<legend><?php echo ucfirst(strtolower($_COOKIE['kModo']))." ".$_COOKIE['kProDes'] ?></legend>
							<center>
								<form name = 'frgrm' action = 'frctogra.php' method = 'post' target='fmpro'>
									<input type="hidden" name="gIteration" value="0" >
									<input type="hidden" name="cComMemo" value="" >
									<input type="hidden" name="cComMemoApl" value="" >

									<center>
										<fieldset>
											<legend><b>Datos Generales</b></legend>
											<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
												<?php $nCol = f_Format_Cols(35);
												echo $nCol;?>
												<tr>
													<td Class = "name" colspan = "8">
														<a href = "javascript:document.frgrm.cPucId.value  = '';
																									document.frgrm.cPucDes.value = '';
																									f_Links('cPucId','VALID')" id="IdCta">Cuenta</a><br>
														<input type = "text" Class = "letra" style = "width:160" name = "cPucId"
																	 onBlur = "javascript:this.value=this.value.toUpperCase();
																												f_Links('cPucId','VALID');
																												this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																	 onFocus="javascript:document.frgrm.cPucId.value  ='';
																											 document.frgrm.cPucDes.value = '';
																											 this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
													<td Class = "name" colspan = "27">Descripci&oacute;n<br>
														<input type = "text" Class = "letra" style = "width:540" name = "cPucDes" readonly>
													</td>
												</tr>
												 <tr>
													<td Class = "name" colspan = "8">Concepto<br>
														<input type = "text" Class = "letra" style = "width:160" name = "cCtoId" onBlur = "javascript:this.value=this.value.toUpperCase();" readonly>
													</td>
													<td Class = "name" colspan = "27">Descripci&oacute;n<br>
														<input type = "text" Class = "letra" style = "width:540" name = "cCtoDes"
																	 onBlur = "javascript:this.value=this.value.toUpperCase();
																												this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																	 onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
												</tr>
												<tr>
													<td Class = "name" colspan = "10">Nit Busqueda Documento Cruce<br>
														<select name="cCtoNit" style="width:200">
															<option value="">[SELECCIONE]</option>
															<option value="TERCERO">TERCERO</option>
															<option value="CLIENTE">CLIENTE</option>
														</select>
													</td>
													<td Class = "name" colspan = "8">Tipo Concepto<br>
														<select name="cCtoTip" style="width:160" onchange="javascript:f_Cargar_Comprobantes(this.value)">
															<option value="PROPIOS">PROPIOS</option>
															<option value="TERCEROS" selected>TERCEROS</option>
														</select>
													</td>
													<td Class = "name" colspan = "9">Calculo Automatico Base<br>
														<select name="cCtoVlr01" style="width:180">
															<option value="">[SELECCIONE]</option>
															<option value="SI">SI</option>
															<option value="NO">NO</option>
														</select>
													</td>
													<td Class = "name" colspan = "8">Calculo Automatico Iva<br>
														<select name="cCtoVlr02" style="width:160">
															<option value="">[SELECCIONE]</option>
															<option value="SI">SI</option>
															<option value="NO">NO</option>
														</select>
													</td>
												</tr>
											</table>
										</fieldset>
										<?php
										/*** Si la variable Categoriacion Conceptos Facturacion esta encendida se habilita el menu de Categoria Conceptos***/
										if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){
											?>
											<fieldset style="width:720px">
		                    <legend><b>Categoria Concepto</b></legend>
		      							<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
		  							 			<?php $nCol = f_Format_Cols(35);
		  							 			echo $nCol;?>
											    <tr>
		  								      <td Class = "name" colspan = "5">
		  												<a href = "javascript:document.frgrm.cCacId.value  = '';
		  																			  		  document.frgrm.cCacDes.value = '';
		  																							f_Links('cCacId','VALID')" id="IdCac">Codigo</a><br>
		  												<input type = "text" Class = "letra" style = "width:100" name = "cCacId"
		  										    	onBlur = "javascript:this.value=this.value.toUpperCase();
		  																			         f_Links('cCacId','VALID');
		  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
		  										    	onFocus="javascript:document.frgrm.cCacId.value  ='';
		  	            						  									document.frgrm.cCacDes.value = '';
		  														                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
		  										  </td>
		  										  <td Class = "name" colspan = "30">Descripcion<br>
		  												 <input type = "text" Class = "letra" style = "width:600" name = "cCacDes"
		  												 onBlur = "javascript:this.value=this.value.toUpperCase();
	  																			         f_Links('cCacDes','VALID');
	  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'">
		  										  </td>
		  									  </tr>
											  </table>
											</fieldset>
											<?php
										}
										?>

										<fieldset style="width:720px">
											<legend><b>Comprobantes</b></legend>
											<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
												<tr><td id="tblCom"></td></tr>
											</table>
										</fieldset>
										<br>
										<fieldset id="ad_TipTerceros" style="border:4px ridge; padding:3">
											<legend><b>Datos Adicionales Para Comprobantes Tipo P</b></legend>
											<fieldset style="width:700px">
												<legend>Conceptos de Retenci&oacute;n ICA</legend>
												<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
													<tr>
														<td id="tblConIca"></td>
													</tr>
												</table>
												<input type = "hidden" name="cCtoCtori">
												<br>
											</fieldset>
											<br>
											<fieldset style="width:700px">
												<legend>Concepto de Retenci&oacute;n en la Fuente - R&eacute;gimen Com&uacute;n</legend>
												<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
													<?php $nCol = f_Format_Cols(34);
													echo $nCol;?>
													<tr>
														<td Class = "name" colspan = "5">
															<a href = "javascript:document.frgrm.cCtoCtoRf.value  = '';
																										document.frgrm.cCtoRetRf.value = '';
																										document.frgrm.cCtoRetRf.value  = '';
																										f_Links('cCtoCtoRf','VALID')" id="IdCto">Concepto</a><br>
															<input type = "text" Class = "letra" style = "width:100" name = "cCtoCtoRf"
																		 onBlur = "javascript:this.value=this.value.toUpperCase();
																		                      if(document.frgrm.cCtoCtoRf.value  != '') {
																													 f_Links('cCtoCtoRf','VALID');
																													}
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		 onFocus="javascript:document.frgrm.cCtoCtoRf.value  = '';
		                                                     document.frgrm.cCtoDesRf.value = '';
		                                                     document.frgrm.cCtoRetRf.value  = '';
																												 this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
														</td>
														<td Class = "name" colspan = "26">Descripcion<br>
															<input type = "text" Class = "letra" style = "width:520" name = "cCtoDesRf" readonly>
														</td>
														<td Class = "name" colspan = "3">Ret (%)<br>
															<input type = "text" Class = "letra" style = "width:60" name = "cCtoRetRf" readonly>
														</td>
													</tr>
												</table>
												<br>
											</fieldset>

											<fieldset style="width:700px">
												<legend>Concepto de Retenci&oacute;n en la Fuente - R&eacute;gimen Simplificado</legend>
												<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
													<?php $nCol = f_Format_Cols(34);
													echo $nCol;?>
													<tr>
														<td Class = "name" colspan = "5">
															<a href = "javascript:document.frgrm.cCtoCtrFs.value  = '';
																										document.frgrm.cCtoDesFs.value  = '';
																										document.frgrm.cCtoRetFs.value  = '';
																										f_Links('cCtoCtrFs','VALID')" id="IdCtoFs">Concepto</a><br>
															<input type = "text" Class = "letra" style = "width:100" name = "cCtoCtrFs"
																		 onBlur = "javascript:this.value=this.value.toUpperCase();
																		                      if(document.frgrm.cCtoCtrFs.value  != '') {
																													 f_Links('cCtoCtrFs','VALID');
																													}
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		 onFocus="javascript:document.frgrm.cCtoCtrFs.value  = '';
		                                                     document.frgrm.cCtoDesFs.value  = '';
		                                                     document.frgrm.cCtoRetFs.value  = '';
																												 this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
														</td>
														<td Class = "name" colspan = "26">Descripcion<br>
															<input type = "text" Class = "letra" style = "width:520" name = "cCtoDesFs" readonly>
														</td>
														<td Class = "name" colspan = "3">Ret (%)<br>
															<input type = "text" Class = "letra" style = "width:60" name = "cCtoRetFs" readonly>
														</td>
													</tr>
												</table>
												<br>
											</fieldset>
											<br>
											<fieldset style="width:700px">
												<legend>Concepto de Retenci&oacute;n IVA - Gran Contribuyente</legend>
												<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
													<?php $nCol = f_Format_Cols(34);
													echo $nCol;?>
													<tr>
														<td Class = "name" colspan = "5">
															<a href = "javascript:document.frgrm.cCtoCtoRv.value  = '';
																										document.frgrm.cCtoDesRv.value = '';
																										document.frgrm.cCtoRetRv.value  = '';
																										f_Links('cCtoCtoRv','VALID')" id="IdCtoRv">Concepto</a><br>
															<input type = "text" Class = "letra" style = "width:100" name = "cCtoCtoRv"
																		 onBlur = "javascript:this.value=this.value.toUpperCase();
																		                      if (document.frgrm.cCtoCtoRv.value  != '') {
																													 f_Links('cCtoCtoRv','VALID');
																													}
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		 onFocus="javascript:document.frgrm.cCtoCtoRv.value  = '';
                                                         document.frgrm.cCtoDesRv.value = '';
                                                         document.frgrm.cCtoRetRv.value  = '';
																												 this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
														</td>
														<td Class = "name" colspan = "26">Descripcion<br>
															<input type = "text" Class = "letra" style = "width:520" name = "cCtoDesRv" readonly>
														</td>
														<td Class = "name" colspan = "3">Ret (%)<br>
															<input type = "text" Class = "letra" style = "width:60" name = "cCtoRetRv" readonly>
														</td>
													</tr>
												</table>
												<br>
											</fieldset>
											<br>
											<fieldset style="width:700px">
                        <legend>Concepto de Retenci&oacute;n IVA - R&eacute;gimen Com&uacute;n</legend>
                        <table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
                          <?php $nCol = f_Format_Cols(34);
                          echo $nCol;?>
                          <tr>
                            <td Class = "name" colspan = "5">
                              <a href = "javascript:document.frgrm.cCtoCtoRc.value  = '';
                                                    document.frgrm.cCtoDesRc.value = '';
                                                    document.frgrm.cCtoRetRc.value  = '';
                                                    f_Links('cCtoCtoRc','VALID')" id="IdCtoRc">Concepto</a><br>
                              <input type = "text" Class = "letra" style = "width:100" name = "cCtoCtoRc"
                                     onBlur = "javascript:this.value=this.value.toUpperCase();
                                                          if (document.frgrm.cCtoCtoRc.value  != '') {
                                                           f_Links('cCtoCtoRc','VALID');
                                                          }
                                                          this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                     onFocus="javascript:document.frgrm.cCtoCtoRc.value  = '';
                                                         document.frgrm.cCtoDesRc.value = '';
                                                         document.frgrm.cCtoRetRc.value  = '';
                                                         this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                            </td>
                            <td Class = "name" colspan = "26">Descripcion<br>
                              <input type = "text" Class = "letra" style = "width:520" name = "cCtoDesRc" readonly>
                            </td>
                            <td Class = "name" colspan = "3">Ret (%)<br>
                              <input type = "text" Class = "letra" style = "width:60" name = "cCtoRetRc" readonly>
                            </td>
                          </tr>
                        </table>
                        <br>
                      </fieldset>
                      <br>
											<fieldset style="width:700px">
												<legend>Concepto Cuenta por Pagar</legend>
												<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
													<?php $nCol = f_Format_Cols(34);
													echo $nCol;?>
													<tr>
														<td Class = "name" colspan = "5">
															<a href = "javascript:document.frgrm.cCtoCtoCp.value  = '';
																										document.frgrm.cCtoDesCp.value = '';
																										f_Links('cCtoCtoCp','VALID')" id="IdCtoCp">Concepto</a><br>
															<input type = "text" Class = "letra" style = "width:100" name = "cCtoCtoCp"
																		 onBlur = "javascript:this.value=this.value.toUpperCase();
																		                      if (document.frgrm.cCtoCtoCp.value  != '') {
																													 f_Links('cCtoCtoCp','VALID');
																													}
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		 onFocus="javascript:document.frgrm.cCtoCtoCp.value  = '';
                                                         document.frgrm.cCtoDesCp.value = '';
																												 this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
														</td>
														<td Class = "name" colspan = "29">Descripcion<br>
															<input type = "text" Class = "letra" style = "width:580" name = "cCtoDesCp" readonly>
														</td>
													</tr>
												</table>
											</fieldset>
											<br>
											<fieldset style="width:700px">
	                    <legend>Datos Adicionales Orden de Nit's para Integraci&oacute;n con Saphiens </legend>
	                    	<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
	      							 		<?php $nCol = f_Format_Cols(34);
	      							 		echo $nCol;?>
	    									  <tr>
										     		<td Class = "name" colspan = "17">N C&oacute;digo<br>
	  													<select name="cCtoNit1p" style="width:340">
	  												  	<option value="">[SELECCIONE]</option>
	  												  	<option value="CLIENTE">CLIENTE</option>
	  												  	<option value="TERCERO">TERCERO</option>
	  													</select>
	  										 		</td>
	  										 		<td Class = "name" colspan = "17">NP C&oacute;digo<br>
	  													<select name="cCtoNit2p" style="width:340">
	  												  	<option value="">[SELECCIONE]</option>
	  												  	<option value="CLIENTE">CLIENTE</option>
	  												  	<option value="TERCERO">TERCERO</option>
	  													</select>
	  										  	</td>
										   		</tr>
	    									</table>
	    									<br>
	  									</fieldset>
										</fieldset>
										<fieldset id="ad_TipPropios" style="border:4px ridge; padding:3;width:720px">
											<legend><b>Datos Adicionales Para Comprobantes Tipo P</b></legend>
										</fieldset>
										<!-- Codigo de integracion con E2K UPS -->
	                  <?php if ($cAlfa == "UPSXXXXX" || $cAlfa == "TEUPSXXXXX" || $cAlfa == "TEUPSXXXXP" || $cAlfa == "DEUPSXXXXX") { ?>
	                    <fieldset>
	                      <legend>Integraci&oacute;n con E2K</legend>
	                      <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
	                        <?php $nCol = f_Format_Cols(35);
	                        echo $nCol;?>
	                        <tr>
	                          <td Class = "name" colspan = "35">COD E2K<br>
	                            <input type = "text" Class = "letra"  style = "width:180"  name = "cCtoE2k" value = "" maxlength="3"
	                                   onblur = "javascript:this.value=this.value.toUpperCase();">
	                          </td>
	                        </tr>
	                      </table>
	                    </fieldset>
	                  <?php } else { ?>
	                    <input type = "hidden" name = "cCtoE2k" value = "">
	                  <?php } ?>
	                  <!-- Fin Codigo de integracion con E2K UPS -->
                    <!-- Codigo Homologacion Aladuanas -->
    					  	  <?php if ($cAlfa == "TEALADUANA" || $cAlfa == "DEALADUANA" || $cAlfa == "ALADUANA") { ?>
    					  	    <fieldset>
                        <legend>C&oacute;digo Homologaci&oacute;n Reporte Facturaci&oacute;n</legend>
                        <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
                          <?php $nCol = f_Format_Cols(35);
                          echo $nCol;?>
                          <tr>
                            <td Class = "name" colspan = "35">C&oacute;digo<br>
                              <input type = "text" Class = "letra"  style = "width:100"  name = "cCtoChAld" value = "" maxlength="10"
                                     onblur = "javascript:this.value=this.value.toUpperCase();">
                            </td>
                          </tr>
                        </table>
                      </fieldset>
                    <?php } else { ?>
                      <input type = "hidden" name = "cCtoChAld" value = "">
                    <?php } ?>
    					  	  <!-- Fin Codigo Homologacion Aladuanas -->
	                   <!-- Inicio Codigo de Integracion con Belcorp -->
                    <?php if ($cAlfa == "ADUANERA" || $cAlfa == "DEADUANERA" || $cAlfa == "TEADUANERA" || $cAlfa == "DEDESARROL" ||  $cAlfa == "DEADUANERP"){?>
										<fieldset>
											<legend>Datos para Integraci&oacute;n con Belcorp </legend>
											<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
											<?php $nCol = f_Format_Cols(35);
  							 			echo $nCol;?>
									    <tr>
  								      <td Class = "name" colspan = "18">
  												Tipo de Documento para Registros de Importados en SAP
  												<input type = "text" Class = "letra" style = "width:360" name = "cPucBel" maxlength="2"
  										    	onblur = "javascript:this.value=this.value.toUpperCase();
				    																	      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
				    								onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  										  </td>
  										  <td Class = "name" colspan = "17">N&uacute;mero de Asignaci&oacute;n<br>
  												<input type = "text" Class = "letra" style = "width:340" name = "cNuAsBel" maxlength="2" value=""
  												 	onblur = "javascript:this.value=this.value.toUpperCase();
				    																	      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
				    								onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
  										  </td>
  									  </tr>
										</table>
									</fieldset>
									<?php }?>
									<!-- Fin Codigo de Integracion con Belcorp -->
									<!-- Codigo Global ID SAP -->
									<?php if ($cAlfa == "TEDHLEXPRE" || $cAlfa == "DEDHLEXPRE" || $cAlfa == "DHLEXPRE") { ?>
	                    <fieldset>
	                      <legend>Integraci&oacute;n con SAP</legend>
	                      <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
	                        <?php $nCol = f_Format_Cols(35);
	                        echo $nCol;?>
	                        <tr>
	                          <td Class = "name" colspan = "35">Global ID SAP<br>
	                            <input type = "text" Class = "letra"  style = "width:180"  name = "cCtoSapId" value = "" maxlength="3"
	                                   onblur = "javascript:this.value=this.value.toUpperCase();">
	                          </td>
	                        </tr>
	                      </table>
	                    </fieldset>
	                  <?php } else { ?>
	                    <input type = "hidden" name = "cCtoSapId" value = "">
	                  <?php } ?>
	                <!-- Codigo Global ID SAP -->
                  <!-- Inicio COdigo de Integracion SAP -->
									<?php 
									$vBDIntegracionColmasSap = explode("~",$vSysStr['system_integracion_colmas_sap']);
									if (in_array($cAlfa, $vBDIntegracionColmasSap) == true){ ?>
				            <fieldset>
  										<legend>Integraci&oacute;n SAP </legend>
  										<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
    										<?php $nCol = f_Format_Cols(35);
    							 			echo $nCol;?>
                        <tr>
                          <td Class = "name" colspan = "07">Cuenta Costo<br>
                            <input type = "text" Class = "letra"  style = "width:140"  name = "cCtoSapC" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
                          <td Class = "name" colspan = "07">Cuenta Ingreso<br>
                            <input type = "text" Class = "letra"  style = "width:140"  name = "cCtoSapI" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
                          <td Class = "name" colspan = "11">C&oacute;digo Impuesto de Compra<br>
                            <input type = "text" Class = "letra"  style = "width:220"  name = "cCtoSapIc" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
                          <td Class = "name" colspan = "10">C&oacute;digo Impuesto de Venta<br>
                            <input type = "text" Class = "letra"  style = "width:200"  name = "cCtoSapIv" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
                        </tr>
                        <tr>
                          <td Class = "name" colspan = "05">C&oacute;digo &Aacute;rea<br>
                            <input type = "text" Class = "letra"  style = "width:100"  name = "cCtoSapCA" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
                          <td Class = "name" colspan = "10">C&oacute;digo L&iacute;nea Importaciones<br>
                            <input type = "text" Class = "letra"  style = "width:200"  name = "cCtoSapLI" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
													<td Class = "name" colspan = "10">C&oacute;digo L&iacute;nea Exportaciones<br>
                            <input type = "text" Class = "letra"  style = "width:200"  name = "cCtoSapLE" maxlength="8"
                              onblur = "javascript:this.value=this.value.toUpperCase();
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
													<td Class = "name" colspan = "10">&nbsp;</td>
                        </tr>
                      </table>
				            </fieldset>
  								<?php
                  }?>
				          <!-- Fin Codigo de  Integracion SAP -->

                  <!-- Inicio Integracion DSV -->
                  <?php if ($cAlfa == "DSVSASXX" || $cAlfa == "TEDSVSASXX" || $cAlfa == "DEDSVSASXX") { ?>
                    <fieldset>
                      <legend>Integraci&oacute;n DSV</legend>
                      <table border='0' cellpadding='0' cellspacing='0' width='700'>
                        <?php $nCol = f_Format_Cols(35);
                        echo $nCol; ?>
                        <tr>
                          <td Class="name" colspan="100">Charge Codes Cargowise<br>
                            <input type="text" Class="letra" style="width:200" maxlength="20" name="cCtocWccX" onblur="javascript:this.value=this.value.toUpperCase(); this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'" onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
                        </tr>
                      </table>
                    </fieldset>
                  <?php } else { ?>
                    <input type="hidden" name="cCtocWccX" value="">
                  <?php } ?>
                  <!-- Fin Integracion DVS -->

									<!-- Inicio Datos Adicionales openETL -->
									<fieldset>
										<legend>Datos Adicionales openETL</legend>
										<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
											<?php $nCol = f_Format_Cols(35);
  							 			echo $nCol;?>
									    <tr>
  								      <td Class = "name" colspan = "14">
  												Clasificaci&oacute;n Producto<br>
  												<select name="cCtoClapr" style="width:280" onChange = "javascript:fnHabilitarCodCompraEficiente();">
  												  <option value="">[SELECCIONE]</option>
  												  <option value="001">001 - UNSPSC (COLOMBIA COMPRA EFICIENTE)</option>
  												  <option value="999">999 - ESTANDAR DE ADOPCION DEL CONTRIBUYENTE</option>
  												</select>
  										  </td>
												<td Class = "name" colspan = "05">
													<a href = "javascript:document.frgrm.cCceId.value  = '';
																								document.frgrm.cCceDes.value = '';
  																							f_Links('cCceId','VALID')" id = "idComEfi">C&oacute;digo UNSPSC</a><br>
  												<input type = "text" Class = "letra" style = "width:100" name = "cCceId" 
  										    	onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         f_Links('cCceId','VALID');
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:document.frgrm.cCceId.value  = '';
																								document.frgrm.cCceDes.value = '';
																								this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
												<td Class = "name" colspan = "16">Descripci&oacute;n Colombia Compra Eficiente<br>
  												<input type = "text" Class = "letra" style = "width:320" name = "cCceDes" readonly>
												</td>
  									  </tr>
											<tr>
												<td Class = "name" colspan = "08">
													<a href = "javascript:document.frgrm.cUmeId.value  = '';
																								document.frgrm.cUmeDes.value  = '';
  																							f_Links('cUmeId','VALID')" id = "cCodUmed">Unidad Medida</a><br>
  												<input type = "text" Class = "letra" style = "width:160" name = "cUmeId"
  										    	onBlur = "javascript:this.value=this.value.toUpperCase();
  																			         f_Links('cUmeId','VALID');
  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
  										    	onFocus="javascript:document.frgrm.cUmeId.value  = '';
																								document.frgrm.cUmeDes.value  = '';
																								this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
												<td Class = "name" colspan = "27">Descripci&oacute;n<br>
  												<input type = "text" Class = "letra" style = "width:540" name = "cUmeDes" readonly>
												</td>
											</tr>
										</table>
									</fieldset>
									<!-- Fin Datos Adicionales openETL -->

									<!-- Inicio Datos Adicionales Transmision 8.5 -->
									<?php if($cAlfa == "COLVANXX" || $cAlfa == "TECOLVANXX" || $cAlfa == "DECOLVANXX") { ?>
									<fieldset>
										<legend><b>Infomaci&oacute;n Adicional Transmisi&oacute;n 8.5</b></legend>
										<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
											<?php $nCol = f_Format_Cols(35);
                      echo $nCol;?>
											<tr>
												<td Class = "name" colspan = "100">Cuenta Iva Transmisi&oacute;n 8.5<br>
                          <input type = "text" Class = "letra" style = "width:200" name = "cCtoPuc85">
												</td>
											</tr>
										</table>
									</fieldset>
									<?php } else { ?>
                    <input type = "hidden" name = "cCtoPuc85" value = "">
                  <?php } ?>
									<!-- Fin Datos Adicionales Transmision 8.5 -->

									<br>
									<fieldset style="width:720px">
											<legend><b>Datos del Registro</b></legend>
											<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
												<?php $nCol = f_Format_Cols(35);
												echo $nCol;?>
												<tr>
													<td Class = "clase08" colspan = "7">Fecha Creado<br>
														<input type = "text" Class = "letra"  style = "width:140;text-align:center"  name = "dFecCre"  value = "<?php echo date('Y-m-d') ?>" readonly>
													</td>
													<td Class = "clase08" colspan = "7">Hora Creado<br>
														<input type = 'text' Class = 'letra' style = "width:140;text-align:center" name = "cHorCre"  value = "<?php echo date('H:i:s') ?>" readonly>
													</td>
													<td Class = "clase08" colspan = "7">Fecha Modificado<br>
														<input type = "text" Class = "letra"  style = "width:140;text-align:center"  name = "dFecMod"  value = "<?php echo date('Y-m-d') ?>" readonly>
													</td>
													<td Class = "clase08" colspan = "7">Hora Modificado<br>
														<input type = 'text' Class = 'letra' style = "width:140;text-align:center" name = "cHorMod"  value = "<?php echo date('H:i:s') ?>" readonly>
													</td>
													<td Class = "clase08" colspan = "7">Estado<br>
														<input type = "text" Class = "letra" style = "width:140;text-align:center" name = "cEstado"  value = "ACTIVO"
																	 onblur = "javascript:this.value=this.value.toUpperCase();f_ValidacEstado();
																												this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																	 onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
												</tr>
											</table>
										</fieldset>
									</center>
								</form>
							</center>
						</fieldset>
					</td>
				</tr>
			</table>
		</center>
		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="720">
				<tr height="21">
					<?php switch ($_COOKIE['kModo']) {
						case "VER": ?>
							<td width="629" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
						default: ?>
							<td width="538" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:f_EnabledCombos();f_Valida_Check();f_DisabledCombos()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
					  } ?>
				</tr>
			</table>
		</center>
    <br>
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<?php
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
				?>
				<script languaje = "javascript">

				  f_Cargar_Comprobantes(document.forms['frgrm']['cCtoTip'].value);
				  f_Mostrar_SucRetIca();

				  document.forms['frgrm']['cCtoTip'].value     = "TERCEROS";
				  document.forms['frgrm']['cCtoTip'].disabled  = true;
				  document.forms['frgrm']['cEstado'].readOnly  = true;

					//Inhabilitar Campo codigo Colombia Compra Eficiente
					document.forms['frgrm']['cCceId'].disabled = true;
					document.getElementById('idComEfi').href	 = "javascript:alert('Opcion No Permitida')";
				</script>
				<?php
			break;
			case "EDITAR":
				?>
				<script languaje = "javascript">
				  document.forms['frgrm']['cEstado'].readOnly  = true;

				  document.forms['frgrm']['cCtoTip'].value     = "TERCEROS";
				  document.forms['frgrm']['cCtoTip'].disabled  = true;
				  document.forms['frgrm']['cEstado'].readOnly  = true;
				</script>
        <?php
        f_CargaData($cCtoId,$cPucId);
      break;
			case "VER":
				?>
				<script languaje = "javascript">
				  for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
          }
				  document.forms['frgrm']['cCtoTip'].value     = "TERCEROS";
				  document.forms['frgrm']['cCtoTip'].disabled  = true;
				  document.forms['frgrm']['cEstado'].readOnly  = true;

				  document.getElementById('IdCto').disabled=true;
		      document.getElementById('IdCto').href="#";
		  	  document.getElementById('IdCtoFs').disabled=true;
		      document.getElementById('IdCtoFs').href="#";
				  document.getElementById('IdCtoRv').disabled=true;
		      document.getElementById('IdCtoRv').href="#";
				  document.getElementById('IdCtoRc').disabled=true;
		      document.getElementById('IdCtoRc').href="#";
				  document.getElementById('IdCtoCp').disabled=true;
		      document.getElementById('IdCtoCp').href="#";
		      //Link Categoria Conceptos
		      if("<?php echo $vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] ?>" == 'SI'){
				    document.getElementById('IdCac').disabled=true;
					  document.getElementById('IdCac').href="#";
          }
				</script>
			  <?php
			  f_CargaData($cCtoId,$cPucId);
			break;
		} ?>

		<?php
		function f_CargaData($xCtoId,$xPucId) {
		  global $xConexion01; global $cAlfa;  global $vSysStr;

			$qSqlCab  = "SELECT * ";
			$qSqlCab .= "FROM $cAlfa.fpar0121 ";
			$qSqlCab .= "WHERE ";
			$qSqlCab .= "pucidxxx = \"$xPucId\" AND ctoidxxx = \"$xCtoId\" AND ";
			$qSqlCab .= "regestxx = \"ACTIVO\" LIMIT 0,1";
			$xSqlCab  = f_MySql("SELECT","",$qSqlCab,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qSqlCab." ~ ".mysql_num_rows($xSqlCab));

			$cLisCom = "";
      $nSec = 0;

			while ($zRCab = mysql_fetch_array($xSqlCab)) {

				//Buscando descripcio del codigo de colombia compra eficiente
				$vComEfi = array();
				if ($zRCab['cceidxxx'] != "") {
					$qComEfi  = "SELECT ";
					$qComEfi .= "cceidxxx, ";
					$qComEfi .= "ccedesxx ";
					$qComEfi .= "FROM $cAlfa.fpar0156 ";
					$qComEfi .= "WHERE ";
					$qComEfi .= "cceidxxx = \"{$zRCab['cceidxxx']}\"";
					$xComEfi  = f_MySql("SELECT","",$qComEfi,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qComEfi."~".mysql_num_rows($xComEfi));
					$vComEfi = mysql_fetch_array($xComEfi);
				}
				
				//Buscando descripcion unidad de medida
				$vUniMed = array();
				if ($zRCab['umeidxxx'] != "") {
					$qUniMed  = "SELECT ";
					$qUniMed .= "umeidxxx, ";
					$qUniMed .= "umedesxx ";
					$qUniMed .= "FROM $cAlfa.fpar0157 ";
					$qUniMed .= "WHERE ";
					$qUniMed .= "umeidxxx = \"{$zRCab['umeidxxx']}\"";
					$xUniMed  = f_MySql("SELECT","",$qUniMed,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qUniMed."~".mysql_num_rows($xUniMed));
					$vUniMed = mysql_fetch_array($xUniMed);
				}

				/*** Si la variable Categoriacion Conceptos Facturacion esta encendida se habilita el menu de Categoria Conceptos***/
				if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){
					/*** Categoria Conceptos***/
					$qCatCon  = "SELECT * ";
					$qCatCon .= "FROM $cAlfa.fpar0144 ";
					$qCatCon .= "WHERE ";
					$qCatCon .= "cacidxxx = \"{$zRCab['cacidxxx']}\" LIMIT 0,1 ";
					$xCatCon  = f_MySql("SELECT","",$qCatCon,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qCatCon."~".mysql_num_rows($xCatCon));
					$vCatCon = mysql_fetch_array($xCatCon);
				}

				#Descripcion de la cuenta PUC
				$qSqlCta  = "SELECT * ";
				$qSqlCta .= "FROM $cAlfa.fpar0115 ";
				$qSqlCta .= "WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucidxxx']}\" AND ";
				$qSqlCta .= "regestxx = \"ACTIVO\"";
				$xSqlCta  = f_MySql("SELECT","",$qSqlCta,$xConexion01,"");
			  $zRCta = mysql_fetch_array($xSqlCta);

			  #Matriz con los comprobantes seleccionados
			  $mAux  = explode("|",$zRCab['ctocomxx']);
			  $mComp = array();
			  for ($i=0; $i<count($mAux); $i++) {
			  	if ($mAux[$i] <> "") {
			  		$mAux02 = array();
			  		$mAux02 = explode("~",$mAux[$i]);
			  		$mComp[$mAux02[0]][$mAux02[1]] = $mAux02[2];
			  	}
			  }

			  #Comprobantes segun el tipo de comprobante
			  $qComId  = "SELECT * ";
				$qComId .= "FROM $cAlfa.fpar0117 ";
				$qComId .= "WHERE ";
				$qComId .= "comidxxx = \"P\"   AND ";
				switch ($zRCab['ctotipxx']) {
					case "PROPIOS":
						$qComId .= "comtipxx = \"CPE\" AND ";
					break;
					default:
						$qComId .= "comtipxx = \"CPC\" AND ";
					break;
				}
				$qComId .= "regestxx = \"ACTIVO\" ";
				$qComId .= "ORDER BY comidxxx,comcodxx ";
				$xComId  = f_MySql("SELECT","",$qComId,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qComId." ~ ".mysql_num_rows($xComId));

				while ($xRCI = mysql_fetch_array($xComId)) {
				  $nSec++;

					$cLisCom .= "<tr>";
						$cLisCom .= "<td Class = \"name\" align=\"center\">".$xRCI['comidxxx']."</td>";
						$cLisCom .= "<td Class = \"name\" align=\"center\">".str_pad($xRCI['comcodxx'],3,"0",STR_PAD_LEFT)."</td>";
						$cLisCom .= "<td Class = \"name\" align=\"left\" style=\"padding-left:5px\">".utf8_encode(substr($xRCI['comdesxx'],0,42))."</td>";
						if($mComp[$xRCI['comidxxx']][$xRCI['comcodxx']] <> "") {
						  $cLisCom .= "<td Class = \"name\" align=\"center\"><input type=\"checkbox\" name=\"oCheck\" value=\"".$xRCI['comidxxx'].'~'.$xRCI['comcodxx']."\" onclick=\"javascript:f_Carga_Data();f_Activar_Check();\" checked></td>";
							if ($mComp[$xRCI['comidxxx']][$xRCI['comcodxx']] == 'D') {
								$cCheckD = " checked";
								$cCheckC = "";
							} else {
								$cCheckD = "";
								$cCheckC = " checked";
							}
							$cLisCom .= "<td Class = \"name\" align=\"center\"><input type=\"checkbox\" name=\"oCheckD\" value=\"".$xRCI['comidxxx'].'~'.$xRCI['comcodxx']."\" onclick=\"javascript:f_Activar_Check();f_Carga_Data();\"".$cCheckD."></td>";
							$cLisCom .= "<td Class = \"name\" align=\"center\"><input type=\"checkbox\" name=\"oCheckC\" value=\"".$xRCI['comidxxx'].'~'.$xRCI['comcodxx']."\" onclick=\"javascript:f_Activar_Check();f_Carga_Data();\"".$cCheckC." disabled></td>";
						} else {
							$cLisCom .= "<td Class = \"name\" align=\"center\"><input type=\"checkbox\" name=\"oCheck\" value=\"".$xRCI['comidxxx'].'~'.$xRCI['comcodxx']."\" onclick=\"javascript:f_Activar_Check();f_Carga_Data();\" ></td>";
							$cLisCom .= "<td Class = \"name\" align=\"center\"><input type=\"checkbox\" name=\"oCheckD\" value=\"".$xRCI['comidxxx'].'~'.$xRCI['comcodxx']."\" onclick=\"javascript:f_Activar_Check();f_Carga_Data();\" disabled></td>";
							$cLisCom .= "<td Class = \"name\" align=\"center\"><input type=\"checkbox\" name=\"oCheckC\" value=\"".$xRCI['comidxxx'].'~'.$xRCI['comcodxx']."\" onclick=\"javascript:f_Activar_Check();f_Carga_Data();\" disabled></td>";
						}
					$cLisCom .= "</tr>";
				}

				if($cLisCom <> "") {
					$cTexto  = "<table border = \"1\" cellpadding = \"0\" cellspacing = \"0\" width=\"700\">";
						$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
							$cTexto .= "<td Class = \"name\" width = \"100\" align=\"center\">Comprobante</td>";
							$cTexto .= "<td Class = \"name\" width = \"080\" align=\"center\">Codigo</td>";
							$cTexto .= "<td Class = \"name\" width = \"320\" align=\"center\">Descripci&oacute;n</td>";
							$cTexto .= "<td Class = \"name\" width = \"080\" align=\"center\">Seleccione</td>";
							$cTexto .= "<td Class = \"name\" width = \"060\" align=\"center\">Debito</td>";
							$cTexto .= "<td Class = \"name\" width = \"060\" align=\"center\">Credito</td>";
					$cTexto .= "</tr>";
					$cTexto .= $cLisCom;
					$cTexto .= "</table>";
				} else {
					$cTexto = "No Se Encontraron Comprobantes para el Tipo Concepto {$zRCab['ctotipxx']}";
				}

			  #Buscando Datos propios de cada tipo de comprobante
				switch ($zRCab['ctotipxx']) {
					case "PROPIOS":
					break;
					default:
						#Datos de los conceptos de cobro
					  $qCtoId  = "SELECT $cAlfa.fpar0119.ctoidxxx, ";
					  $qCtoId .= "IF($cAlfa.fpar0119.ctodesxp <> \"\",$cAlfa.fpar0119.ctodesxp,IF($cAlfa.fpar0119.ctodesxx <> \"\",$cAlfa.fpar0119.ctodesxx,\"CONCEPTO SIN DESCRIPCION\")) AS ctodesxp, ";
					  $qCtoId .= "$cAlfa.fpar0115.pucretxx ";
					  $qCtoId .= "FROM $cAlfa.fpar0119 ";
					  $qCtoId .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
					  $qCtoId .= "WHERE ";
					  $qCtoId .= "$cAlfa.fpar0119.ctoidxxx IN (\"{$zRCab['ctoctorf']}\",\"{$zRCab['ctoctrfs']}\",\"{$zRCab['ctoctorv']}\",\"{$zRCab['ctoctorc']}\",\"{$zRCab['ctoctocp']}\") AND ";
					  $qCtoId .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
					  $qCtoId .= "ORDER BY ABS($cAlfa.fpar0119.ctoidxxx) ";
					  $xCtoId  = f_MySql("SELECT","",$qCtoId,$xConexion01,"");
					  $mCtoId = array();
					  while ($xRCI = mysql_fetch_array($xCtoId)) {
					  	$mCtoId[$xRCI['ctoidxxx']] = $xRCI;
					  }
					break;
				}
  			?>
				<script language = "javascript">
				    document.forms['frgrm']['cPucId'].value    = "<?php echo $zRCab['pucidxxx'] ?>";
				 	document.forms['frgrm']['cPucDes'].value   = "<?php echo $zRCta['pucdesxx'] ?>";
				 	document.forms['frgrm']['cCtoId'].value    = "<?php echo $zRCab['ctoidxxx'] ?>";
				 	document.forms['frgrm']['cCtoDes'].value   = "<?php echo $zRCab['ctodesxx'] ?>";
				 	document.forms['frgrm']['cCtoNit'].value   = "<?php echo $zRCab['ctonitxx'] ?>";
				 	document.forms['frgrm']['cCtoVlr01'].value = "<?php echo $zRCab['ctovlr01'] ?>";
				 	document.forms['frgrm']['cCtoVlr02'].value = "<?php echo $zRCab['ctovlr02'] ?>";
				 	document.forms['frgrm']['cCtoTip'].value   = "<?php echo $zRCab['ctotipxx'] ?>";
				 	document.forms['frgrm']['cComMemo'].value  = "<?php echo $zRCab['ctocomxx'] ?>";

				 	switch (document.forms['frgrm']['cCtoTip'].value) {
		    		case "PROPIOS":
		    			document.getElementById('ad_TipPropios').style.display = "block";
		    			document.getElementById('ad_TipTerceros').style.display= "none";
		    		break;
		    		default:
			    		document.forms['frgrm']['gIteration'].value = '<?php echo $nSec ?>';
		    			document.getElementById('tblCom').innerHTML = '<?php echo $cTexto ?>';
		    			f_Activar_Check();
		    			f_Carga_Data();

		    			document.forms['frgrm']['cCtoCtori'].value   = "<?php echo $zRCab['ctoctori'] ?>";
						 	f_Mostrar_SucRetIca();

						 	document.forms['frgrm']['cCtoCtoRf'].value   = "<?php echo $zRCab['ctoctorf'] ?>";
						 	document.forms['frgrm']['cCtoDesRf'].value   = "<?php echo $mCtoId[$zRCab['ctoctorf']]['ctodesxp'] ?>";
						 	document.forms['frgrm']['cCtoRetRf'].value   = "<?php echo $mCtoId[$zRCab['ctoctorf']]['pucretxx'] ?>";

							//Nuevos para regimen simplificado
						 	document.forms['frgrm']['cCtoCtrFs'].value   = "<?php echo $zRCab['ctoctrfs'] ?>";
						 	document.forms['frgrm']['cCtoDesFs'].value   = "<?php echo $mCtoId[$zRCab['ctoctrfs']]['ctodesxp'] ?>";
						 	document.forms['frgrm']['cCtoRetFs'].value   = "<?php echo $mCtoId[$zRCab['ctoctrfs']]['pucretxx'] ?>";
							//Fin
						 	document.forms['frgrm']['cCtoCtoRv'].value   = "<?php echo $zRCab['ctoctorv'] ?>";
						 	document.forms['frgrm']['cCtoDesRv'].value   = "<?php echo $mCtoId[$zRCab['ctoctorv']]['ctodesxp'] ?>";
						 	document.forms['frgrm']['cCtoRetRv'].value   = "<?php echo $mCtoId[$zRCab['ctoctorv']]['pucretxx'] ?>";

						 	document.forms['frgrm']['cCtoCtoRc'].value   = "<?php echo $zRCab['ctoctorc'] ?>";
              				document.forms['frgrm']['cCtoDesRc'].value   = "<?php echo $mCtoId[$zRCab['ctoctorc']]['ctodesxp'] ?>";
              				document.forms['frgrm']['cCtoRetRc'].value   = "<?php echo $mCtoId[$zRCab['ctoctorc']]['pucretxx'] ?>";

						 	document.forms['frgrm']['cCtoCtoCp'].value   = "<?php echo $zRCab['ctoctocp'] ?>";
						 	document.forms['frgrm']['cCtoDesCp'].value   = "<?php echo $mCtoId[$zRCab['ctoctocp']]['ctodesxp'] ?>";

						 	document.forms['frgrm']['cCtoNit1p'].value   = "<?php echo $zRCab['ctonit1p'] ?>";
					 		document.forms['frgrm']['cCtoNit2p'].value   = "<?php echo $zRCab['ctonit2p'] ?>";

					 		document.getElementById('ad_TipPropios').style.display = "none";
	    				document.getElementById('ad_TipTerceros').style.display= "block";
		    		break;
		    	}

				 	document.forms['frgrm']['cPucId'].readOnly  = true;
				 	document.forms['frgrm']['cPucId'].onfocus   = "";
				 	document.forms['frgrm']['cPucId'].onblur    = "";
				 	document.forms['frgrm']['cCtoId'].readOnly  = true;
				 	document.getElementById('IdCta').disabled=true;
				 	document.getElementById('IdCta').href="#";

				 	document.forms['frgrm']['cCtoE2k'].value     = "<?php echo $zRCab['ctoe2kxx'] ?>";
					document.forms['frgrm']['cCtoSapId'].value   = "<?php echo $zRCab['ctosapid'] ?>";
          document.forms['frgrm']['cCtoChAld'].value   = "<?php echo $zRCab['ctochald'] ?>";

          // Campo Integración DSV - Charge Codes Cargowise
          <?php
          if ($cAlfa == "DSVSASXX" || $cAlfa == "TEDSVSASXX" || $cAlfa == "DEDSVSASXX") { ?>
            document.forms['frgrm']['cCtocWccX'].value = "<?php echo $zRCab['ctocwccx'] ?>"; //Charge Codes Cargowis
          <?php } ?>

				 	<?php
				 	  if ($cAlfa == "ADUANERA" || $cAlfa == "DEADUANERA" || $cAlfa == "TEADUANERA" || $cAlfa == "DEDESARROL" ||  $cAlfa == "DEADUANERP"){?>
						 	document.forms['frgrm']['cPucBel'].value     = "<?php echo $zRCab['pucadbel'] ?>"; //C�digo Integraci�n Belcorp
						 	document.forms['frgrm']['cNuAsBel'].value    = "<?php echo $zRCab['pucadnas'] ?>"; //N�mero Asignaci�n Belcorp
							if(document.forms['frgrm']['cNuAsBel'].value == "0"){
						 		document.forms['frgrm']['cNuAsBel'].value  = "";
							}
          <?php } ?>

          <?php
				 	$vBDIntegracionColmasSap = explode("~",$vSysStr['system_integracion_colmas_sap']);
					 if (in_array($cAlfa, $vBDIntegracionColmasSap) == true){ ?>
					 	document.forms['frgrm']['cCtoSapC'].value  = "<?php echo $zRCab['ctosapcx'] ?>"; //Cuenta Costo
				  	document.forms['frgrm']['cCtoSapI'].value  = "<?php echo $zRCab['ctosapix'] ?>"; //Cuenta Ingreso
				  	document.forms['frgrm']['cCtoSapIc'].value = "<?php echo $zRCab['ctosapic'] ?>"; //Cuenta del Impuesto de Compra
				  	document.forms['frgrm']['cCtoSapIv'].value = "<?php echo $zRCab['ctosapiv'] ?>"; //Cuenta del Impuesto de Venta
				  	document.forms['frgrm']['cCtoSapCA'].value = "<?php echo $zRCab['ctosapca'] ?>"; //Codigo del area
				  	document.forms['frgrm']['cCtoSapLI'].value = "<?php echo $zRCab['ctosapli'] ?>"; //Codigo de la linea
				  	document.forms['frgrm']['cCtoSapLE'].value = "<?php echo $zRCab['ctosaple'] ?>"; //Codigo de la linea
					<?php
          }
          ?>
					//// Campos Nuevos ////
					document.forms['frgrm']['cCtoClapr'].value		= "<?php echo $zRCab['ctoclapr'] ?>";
					<?php
					if ($zRCab['ctoclapr'] == "001"){ ?>
						document.forms['frgrm']['cCceId'].value  	 	= "<?php echo $zRCab['cceidxxx'] ?>";
						document.forms['frgrm']['cCceDes'].value  	= "<?php echo str_replace('"','\"',$vComEfi['ccedesxx']) ?>";
						document.forms['frgrm']['cCceId'].disabled 	= false;
						document.forms['frgrm']['cCceDes'].disabled = false;
						document.getElementById('idComEfi').href		=	"javascript:document.frgrm.cCceId.value  = ''; document.frgrm.cCceDes.value  = ''; f_Links('cCceId','VALID')";
					<?php } else { ?>
						document.forms['frgrm']['cCceId'].value    	= "";
						document.forms['frgrm']['cCceId'].disabled 	= true;
						document.forms['frgrm']['cCceDes'].disabled	= true;
						document.getElementById('idComEfi').href		= "javascript:alert('Opcion No Permitida')";
					<?php } 
					/*** Opcion VER ***/ 
					if ($_COOKIE['kModo'] == "VER"){ ?>
						//Link Codigo Colombia Compra Eficiente
						document.getElementById('idComEfi').href		=	"javascript:alert('Opcion No Permitida')";
						//Link Unidad de Medida
						document.getElementById('cCodUmed').href		= "javascript:alert('Opcion No Permitida')";
					<?php } ?>
					document.forms['frgrm']['cUmeId'].value       = "<?php echo $zRCab['umeidxxx'] ?>";
					document.forms['frgrm']['cUmeDes'].value     	= "<?php echo str_replace('"','\"',$vUniMed['umedesxx']) ?>";
					document.forms['frgrm']['cCtoPuc85'].value    = "<?php echo $zRCab['ctopuc85'] ?>";

				 	document.forms['frgrm']['dFecCre'].value     	= "<?php echo $zRCab['regfcrex'] ?>";
				 	document.forms['frgrm']['cHorCre'].value     	= "<?php echo $zRCab['reghcrex'] ?>";
				 	document.forms['frgrm']['dFecMod'].value     	= "<?php echo $zRCab['regfmodx'] ?>";
				 	document.forms['frgrm']['cHorMod'].value     	= "<?php echo $zRCab['reghmodx'] ?>";
				 	document.forms['frgrm']['cEstado'].value     	= "<?php echo $zRCab['regestxx'] ?>";

					//Categoria Conceptos
					/*** Si la variable Categoriacion Conceptos Facturacion esta encendida se habilita el menu de Categoria Conceptos***/
					if('<?php echo $vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI' ?>'){
						document.forms['frgrm']['cCacId'].value           = "<?php echo $vCatCon['cacidxxx'] ?>";
						document.forms['frgrm']['cCacDes'].value          = "<?php echo $vCatCon['cacdesxx'] ?>";
					}
				</script>
		<?php }
		} ?>
	</body>
</html>

<?php
namespace openComex;
include("../../../../libs/php/utility.php");
?>
<html>
	<head>
		<title>Cambio de Nit de Cliente y Proveedor</title>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
	  <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>

		<script languaje = 'javascript'>
			function f_Links(xLink,xSwitch,xSecuencia,xType) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
					case "cTerId":
					case "cTerIdB":
					case "cTerNom":
					case "cTerNomB":
					  if (xLink == "cTerId" || xLink == "cTerNom") {
					    var cTerTip = document.forms['frgrm']['cTerTip'].value.toUpperCase();
					    var cTerId  = document.forms['frgrm']['cTerId'].value.toUpperCase();
					    var cTerNom = document.forms['frgrm']['cTerNom'].value.toUpperCase();
					  } else if (xLink == "cTerIdB" || xLink == "cTerNomB") {
              var cTerTip = document.forms['frgrm']['cTerTipB'].value.toUpperCase();
					    var cTerId  = document.forms['frgrm']['cTerIdB'].value.toUpperCase();
					    var cTerNom = document.forms['frgrm']['cTerNomB'].value.toUpperCase();
					  }

						if (xSwitch == "VALID") {
							var cPathUrl = "frcpacnv.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gTerTip="+cTerTip+
																				"&gTerId="+cTerId+
																				"&gTerNom="+cTerNom;
							//alert(cPathUrl);
							parent.framepro.location = cPathUrl;
						} else {
							var nNx      = (nX-600)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "frcpacnv.php?gModo="+xSwitch+"&gFunction="+xLink+
																				 "&gTerTip="+cTerTip+
																				 "&gTerId="+cTerId+
																				 "&gTerNom="+cTerNom;
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
				  		cWindow.focus();
						}
					break;
					case "cComCscC":
						switch ("<?php echo $gPucDet ?>") {
  				    case "N": // Cuenta no Detalla
  				    break;
  				    case "C": // Cuentas por Cobrar
  				    case "P": // Cuentas por Pagar
  				    case "D": // Cuenta de DO's

    						if (xSwitch == "VALID") {
    							var cPathUrl = "frcpacom.php?gModo="+xSwitch+"&gFunction="+xLink+
    																				 "&gTerId="+document.forms['frgrm']['cTerId'].value+
    																				 "&gComCscC="+document.forms['frgrm']['cComCscC'].value.toUpperCase()+
    																				 "&gCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase()+
    																				 "&gPucId=<?php echo $gPucId ?>"+
    																				 "&gPucDet=<?php echo $gPucDet ?>"+
    																				 "&gPucTipEj=<?php echo $gPucTipEj ?>"+
    																				 "&gSecuencia=<?php echo $gSecuencia ?>";
    							//alert(cPathUrl);
    							parent.framepro.location = cPathUrl;
    						} else {
    							var nNx      = (nX-550)/2;
    							var nNy      = (nY-250)/2;
    							var cWinOpt  = "width=550,scrollbars=1,height=250,left="+nNx+",top="+nNy;
    							var cPathUrl = "frcpacom.php?gModo="+xSwitch+"&gFunction="+xLink+
    																				 "&gTerId="+document.forms['frgrm']['cTerId'].value+
    																				 "&gComCscC="+document.forms['frgrm']['cComCscC'].value.toUpperCase()+
    																				 "&gCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase()+
    																				 "&gPucId=<?php echo $gPucId ?>"+
    																				 "&gPucDet=<?php echo $gPucDet ?>"+
    																				 "&gPucTipEj=<?php echo $gPucTipEj ?>"+
    																				 "&gSecuencia=<?php echo $gSecuencia ?>";
    							//alert(cPathUrl);
    							cWindow = window.open(cPathUrl,xLink+"A",cWinOpt);
    				  		cWindow.focus();
    						}
    					break;
						}

					break;
				}
			}

		  function f_Enabled_Combos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.forms['frgrm']['cTerTip'].disabled  = false;
				document.forms['frgrm']['cTerTipB'].disabled = false;
		  }

		  function f_Disabled_Combos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.forms['frgrm']['cTerTip'].disabled  = true;
				document.forms['frgrm']['cTerTipB'].disabled = true;
		  }

		  function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
		  	parent.window.opener.document.forms['frgrm']['cComIdC'  +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cComIdC'].value;
		  	parent.window.opener.document.forms['frgrm']['cComCodC' +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cComCodC'].value;
		  	parent.window.opener.document.forms['frgrm']['cComCscC' +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cComCscC'].value;
		  	parent.window.opener.document.forms['frgrm']['cComSeqC' +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cComSeqC'].value;

				parent.window.opener.document.forms['frgrm']['cComCscC' +<?php echo $gSecuencia ?>].id  = document.forms['frgrm']['cComIdC'].value  + "~";
				parent.window.opener.document.forms['frgrm']['cComCscC' +<?php echo $gSecuencia ?>].id += document.forms['frgrm']['cComCodC'].value + "~";
				parent.window.opener.document.forms['frgrm']['cComCscC' +<?php echo $gSecuencia ?>].id += document.forms['frgrm']['cComCscC'].value + "~";
				parent.window.opener.document.forms['frgrm']['cComCscC' +<?php echo $gSecuencia ?>].id += document.forms['frgrm']['cComSeqC'].value;

		  	parent.window.opener.document.forms['frgrm']['cCcoId'   +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cCcoId'].value;
		  	parent.window.opener.document.forms['frgrm']['cSccId'   +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cSccId'].value;
		  	parent.window.opener.document.forms['frgrm']['cSucId'   +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cSucId'].value;
		  	parent.window.opener.document.forms['frgrm']['cDocId'   +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cDocId'].value;
		  	parent.window.opener.document.forms['frgrm']['cDocSuf'  +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cDocSuf'].value;
		  	parent.window.opener.document.forms['frgrm']['nComVlr'  +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['nSaldo'].value;
		  	parent.window.opener.document.forms['frgrm']['nComVlrNF'+<?php echo $gSecuencia ?>].value=document.forms['frgrm']['nSaldoNF'].value;
				parent.window.opener.document.forms['frgrm']['cTerTip'  +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cTerTip'].value;
				parent.window.opener.document.forms['frgrm']['cTerId'   +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cTerId'].value;
				parent.window.opener.document.forms['frgrm']['cTerTipB' +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cTerTipB'].value;
				parent.window.opener.document.forms['frgrm']['cTerIdB'  +<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cTerIdB'].value;
				document.forms['frgrm']['cPucTipEj'].value = (document.forms['frgrm']['cPucTipEj'].value != "") ? document.forms['frgrm']['cPucTipEj'].value : "<?php echo $gPucTipEj ?>";
				parent.window.opener.document.forms['frgrm']['cPucTipEj'+<?php echo $gSecuencia ?>].value=document.forms['frgrm']['cPucTipEj'].value;
				switch (document.forms['frgrm']['cPucTipEj'].value) {
					case 'L':
						parent.window.opener.document.forms['frgrm']['nComVlr'  +<?php echo $gSecuencia ?>].disabled = false;
						parent.window.opener.document.forms['frgrm']['nComVlrNF'+<?php echo $gSecuencia ?>].disabled = true;
					break;
					case 'N':
						parent.window.opener.document.forms['frgrm']['nComVlr'  +<?php echo $gSecuencia ?>].disabled = true;
						parent.window.opener.document.forms['frgrm']['nComVlrNF'+<?php echo $gSecuencia ?>].disabled = false;
					break;
					default:
						parent.window.opener.document.forms['frgrm']['nComVlr'  +<?php echo $gSecuencia ?>].disabled = false;
						parent.window.opener.document.forms['frgrm']['nComVlrNF'+<?php echo $gSecuencia ?>].disabled = false;
					break;
				}

				parent.window.opener.f_Valores_Automaticos("<?php echo $gSecuencia ?>");
        parent.window.opener.f_Cuadre_Debitos_Creditos();
		  }
		</script>
	</head>
	<body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
		<!-- PRIMERO PINTO EL FORMULARIO -->
		<center>
			<table border = "0" cellpadding = "0" cellspacing= "0" width = "460">
				<tr>
					<td>
						<fieldset>
			   			<legend>Cambio de Nit de Cliente y Proveedor</legend>
							<form name = "frgrm" action = "frcpacnt.php" method = "post" target="framepro">
								<input type = "hidden" Class = "letra" name = "cSucId" readonly>
								<input type = "hidden" Class = "letra" name = "cDocId" readonly>
								<input type = "hidden" Class = "letra" name = "cDocSuf" readonly>
								<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:460">
									<?php $cCols = f_Format_Cols(23); echo $cCols; ?>
									<tr>
										<td Class = "name" colspan = "5">Tipo Tercero<br>
											<select Class = "letrase" name = "cTerTip" style = "width:100" disabled>
												<option value = 'CLICLIXX' selected>CLIENTE</option>
												<option value = 'CLIPROCX'>PROVEEDORC</option>
												<option value = 'CLIPROEX'>PROVEEDORE</option>
												<option value = 'CLIEFIXX'>E. FINANCIERA</option>
												<option value = 'CLISOCXX'>SOCIO</option>
												<option value = 'CLIEMPXX'>EMPLEADO</option>
												<option value = 'CLIOTRXX'>OTROS</option>
											</select>
										</td>
										<td Class = "name" colspan = "4">
											<a href = "javascript:document.forms['frgrm']['cTerId'].value   = '';
																		  		  document.forms['frgrm']['cTerNom'].value  = '';
																						document.forms['frgrm']['cTerDV'].value   = '';
																						document.forms['frgrm']['cComIdC'].value  = '';
																						document.forms['frgrm']['cComCodC'].value = '';
																						document.forms['frgrm']['cComCscC'].value = '';
																						document.forms['frgrm']['cComSeqC'].value = '';
																						document.forms['frgrm']['cCcoId'].value   = '';
																						document.forms['frgrm']['cSccId'].value   = '';
																						document.forms['frgrm']['cSucId'].value   = '';
																						document.forms['frgrm']['cDocId'].value   = '';
																						document.forms['frgrm']['cDocSuf'].value  = '';
																						document.forms['frgrm']['nSaldo'].value   = '';
																						document.forms['frgrm']['nSaldoNF'].value = '';
																						document.forms['frgrm']['cPucTipEj'].value= '';
																						f_Links('cTerId','WINDOW')" id="id_href_cTerId">Nit</a><br>
											<input type = "text" Class = "letra" style = "width:80;text-align:center" name = "cTerId"
												onfocus="javascript:document.forms['frgrm']['cTerId'].value   = '';
            						  									document.forms['frgrm']['cTerNom'].value  = '';
																				    document.forms['frgrm']['cTerDV'].value   = '';
																				    document.forms['frgrm']['cComIdC'].value  = '';
																						document.forms['frgrm']['cComCodC'].value = '';
																						document.forms['frgrm']['cComCscC'].value = '';
																						document.forms['frgrm']['cComSeqC'].value = '';
																						document.forms['frgrm']['cCcoId'].value   = '';
																						document.forms['frgrm']['cSccId'].value   = '';
																						document.forms['frgrm']['cSucId'].value   = '';
																						document.forms['frgrm']['cDocId'].value   = '';
																						document.forms['frgrm']['cDocSuf'].value  = '';
																						document.forms['frgrm']['nSaldo'].value   = '';
																						document.forms['frgrm']['nSaldoNF'].value = '';
																						document.forms['frgrm']['cPucTipEj'].value= '';
													                  this.style.background='#00FFFF'"
									    	onBlur = "javascript:this.value=this.value.toUpperCase();
																		         f_Links('cTerId','VALID');
																		         this.style.background='#FFFFFF'">
										</td>
										<td Class = "name" colspan = "1">Dv<br>
											<input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDV" readonly>
										</td>
										<td Class = "name" colspan = "12">Cliente<br>
											<input type = "text" Class = "letra" style = "width:240" name = "cTerNom"
									    	onfocus="javascript:document.forms['frgrm']['cTerId'].value   = '';
            						  									document.forms['frgrm']['cTerNom'].value  = '';
																				    document.forms['frgrm']['cTerDV'].value   = '';
																				    document.forms['frgrm']['cComIdC'].value  = '';
																						document.forms['frgrm']['cComCodC'].value = '';
																						document.forms['frgrm']['cComCscC'].value = '';
																						document.forms['frgrm']['cComSeqC'].value = '';
																						document.forms['frgrm']['cCcoId'].value   = '';
																						document.forms['frgrm']['cSccId'].value   = '';
																						document.forms['frgrm']['cSucId'].value   = '';
																						document.forms['frgrm']['cDocId'].value   = '';
																						document.forms['frgrm']['cDocSuf'].value  = '';
																						document.forms['frgrm']['nSaldo'].value   = '';
																						document.forms['frgrm']['nSaldoNF'].value = '';
																						document.forms['frgrm']['cPucTipEj'].value= '';
													                  this.style.background='#00FFFF'"
									    	onBlur = "javascript:this.value=this.value.toUpperCase();
													                   f_Links('cTerNom','VALID');
													                   this.style.background='#FFFFFF'">
										</td>
										<td Class = "name" colspan = "1"><br>
											<input type = "text" Class = "letra" style = "width:20;text-align:center" readonly>
										</td>
									</tr>

									<tr>
										<td Class = "name" colspan = "5">Tipo Tercero<br>
											<select Class = "letrase" name = "cTerTipB" style = "width:100" disabled>
												<option value = 'CLICLIXX'>CLIENTE</option>
												<option value = 'CLIPROCX' selected>PROVEEDORC</option>
												<option value = 'CLIPROEX'>PROVEEDORE</option>
												<option value = 'CLIEFIXX'>E. FINANCIERA</option>
												<option value = 'CLISOCXX'>SOCIO</option>
												<option value = 'CLIEMPXX'>EMPLEADO</option>
												<option value = 'CLIOTRXX'>OTROS</option>
											</select>
										</td>
										<td Class = "name" colspan = "4">
											<a href = "javascript:document.forms['frgrm']['cTerIdB'].value  = '';
																		  		  document.forms['frgrm']['cTerNomB'].value = '';
																						document.forms['frgrm']['cTerDVB'].value  = '';
																						document.forms['frgrm']['cComIdC'].value  = '';
																						document.forms['frgrm']['cComCodC'].value = '';
																						document.forms['frgrm']['cComCscC'].value = '';
																						document.forms['frgrm']['cComSeqC'].value = '';
																						document.forms['frgrm']['cCcoId'].value   = '';
																						document.forms['frgrm']['cSccId'].value   = '';
																						document.forms['frgrm']['cSucId'].value   = '';
																						document.forms['frgrm']['cDocId'].value   = '';
																						document.forms['frgrm']['cDocSuf'].value  = '';
																						document.forms['frgrm']['nSaldo'].value   = '';
																						document.forms['frgrm']['nSaldoNF'].value = '';
																						document.forms['frgrm']['cPucTipEj'].value= '';
																						f_Links('cTerIdB','VALID')" id="id_href_cTerIdB">Nit</a><br>
											<input type = "text" Class = "letra" style = "width:80;text-align:center" name = "cTerIdB"
												onfocus="javascript:document.forms['frgrm']['cTerIdB'].value  = '';
            						  									document.forms['frgrm']['cTerNomB'].value = '';
																				    document.forms['frgrm']['cTerDVB'].value  = '';
																				    document.forms['frgrm']['cComIdC'].value  = '';
																						document.forms['frgrm']['cComCodC'].value = '';
																						document.forms['frgrm']['cComCscC'].value = '';
																						document.forms['frgrm']['cComSeqC'].value = '';
																						document.forms['frgrm']['cCcoId'].value   = '';
																						document.forms['frgrm']['cSccId'].value   = '';
																						document.forms['frgrm']['cSucId'].value   = '';
																						document.forms['frgrm']['cDocId'].value   = '';
																						document.forms['frgrm']['cDocSuf'].value  = '';
																						document.forms['frgrm']['nSaldo'].value   = '';
																						document.forms['frgrm']['nSaldoNF'].value = '';
																						document.forms['frgrm']['cPucTipEj'].value= '';
													                  this.style.background='#00FFFF'"
									    	onBlur = "javascript:this.value=this.value.toUpperCase();
																		         f_Links('cTerIdB','VALID');
																		         this.style.background='#FFFFFF'">
										</td>
										<td Class = "name" colspan = "1">Dv<br>
											<input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDVB" readonly>
										</td>
										<td Class = "name" colspan = "12">Tercero<br>
											<input type = "text" Class = "letra" style = "width:240" name = "cTerNomB"
									    	onfocus="javascript:document.forms['frgrm']['cTerIdB'].value  = '';
            						  									document.forms['frgrm']['cTerNomB'].value = '';
																				    document.forms['frgrm']['cTerDVB'].value  = '';
																				    document.forms['frgrm']['cComIdC'].value  = '';
																						document.forms['frgrm']['cComCodC'].value = '';
																						document.forms['frgrm']['cComCscC'].value = '';
																						document.forms['frgrm']['cComSeqC'].value = '';
																						document.forms['frgrm']['cCcoId'].value   = '';
																						document.forms['frgrm']['cSccId'].value   = '';
																						document.forms['frgrm']['cSucId'].value   = '';
																						document.forms['frgrm']['cDocId'].value   = '';
																						document.forms['frgrm']['cDocSuf'].value  = '';
																						document.forms['frgrm']['nSaldo'].value   = '';
																						document.forms['frgrm']['nSaldoNF'].value = '';
																						document.forms['frgrm']['cPucTipEj'].value= '';
													                  this.style.background='#00FFFF'"
									    	onBlur = "javascript:this.value=this.value.toUpperCase();
													                   f_Links('cTerNomB','VALID');
													                   this.style.background='#FFFFFF'">
										</td>
										<td Class = "name" colspan = "1"><br>
											<input type = "text" Class = "letra" style = "width:20;text-align:center" readonly>
										</td>
									</tr>

									<tr id="Documento_Cruce">
										<?php $cCols = f_Format_Cols(23); echo $cCols; ?>
										<td Class = "name" colspan = "1">Cp<br>
											<input type = "text" Class = "letra" style = "width:020;text-align:center" name = "cComIdC" readonly>
										</td>
										<td Class = "name" colspan = "2">Cd<br>
											<input type = "text" Class = "letra" style = "width:040;text-align:center" name = "cComCodC" readonly>
										</td>
										<td Class = "name" colspan = "4">
											<a href = "javascript:document.forms['frgrm']['cComIdC'].value  = '';
																						document.forms['frgrm']['cComCodC'].value = '';
																						document.forms['frgrm']['cComCscC'].value = '';
																						document.forms['frgrm']['cComSeqC'].value = '';
																						document.forms['frgrm']['cCcoId'].value   = '';
																						document.forms['frgrm']['cSccId'].value   = '';
																						document.forms['frgrm']['cSucId'].value   = '';
																						document.forms['frgrm']['cDocId'].value   = '';
																						document.forms['frgrm']['cDocSuf'].value  = '';
																						document.forms['frgrm']['nSaldo'].value   = '';
																						document.forms['frgrm']['nSaldoNF'].value = '';
																						document.forms['frgrm']['cPucTipEj'].value= '';
																						f_Links('cComCscC','VALID')">Doc. Cruce</a><br>
											<input type = "text" Class = "letra" style = "width:080;text-align:center" name = "cComCscC" id = ""
												onFocus="javascript:document.forms['frgrm']['cComIdC'].value  = '';
																						document.forms['frgrm']['cComCodC'].value = '';
																						document.forms['frgrm']['cComCscC'].value = '';
																						document.forms['frgrm']['cComSeqC'].value = '';
																						document.forms['frgrm']['cCcoId'].value   = '';
																						document.forms['frgrm']['cSccId'].value   = '';
																						document.forms['frgrm']['cSucId'].value   = '';
																						document.forms['frgrm']['cDocId'].value   = '';
																						document.forms['frgrm']['cDocSuf'].value  = '';
																						document.forms['frgrm']['nSaldo'].value   = '';
																						document.forms['frgrm']['nSaldoNF'].value = '';
																						document.forms['frgrm']['cPucTipEj'].value= '';
																						this.style.background='#00FFFF'"
									    	onBlur = "javascript:this.value=this.value.toUpperCase();
																						 f_Links('cComCscC','VALID');
																						 this.style.background='#FFFFFF'" readonly>
										</td>
										<td Class = "name" colspan = "2"><br>
											<input type = "text"   Class = "letra" style = "width:040;text-align:center" name = "cComSeqC" readonly>
										</td>
										<td Class = "name" colspan = "2">Cc<br>
											<input type = "text" Class = "letra" style = "width:040;text-align:center" name = "cCcoId" readonly>
										</td>
										<td Class = "name" colspan = "4">Sc<br>
											<input type = "text" Class = "letra" style = "width:040;text-align:center" name = "cSccId" readonly>
										</td>
										<td Class = "name" colspan = "4">Saldo<br>
											<input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nSaldo" readonly>
										</td>
										<td Class = "name" colspan = "4">Saldo NIIF<br>
											<input type = "text" Class = "letra" style = "width:080;text-align:right" name = "nSaldoNF" readonly>
											<input type = "hidden" name = "cPucTipEj" readonly>
										</td>
									</tr>
								</table>
					   	</form>
					  </fieldset>
					</td>
				</tr>
			</table>
		</center>

		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="460">
				<tr height="21">
					<td width="369" height="21"></td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer"
						onClick = "javascript:f_Enabled_Combos();f_Retorna();f_Disabled_Combos();parent.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aceptar
					</td>
				</tr>
			</table>
		</center>

		<?php
			$qTerId  = "SELECT ";
			$qTerId .= "$cAlfa.SIAI0150.*, ";
			$qTerId .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
			$qTerId .= "FROM $cAlfa.SIAI0150 ";
			$qTerId .= "WHERE ";
			$qTerId .= "$cAlfa.SIAI0150.$gTerTip = \"SI\" AND ";
			$qTerId .= "$cAlfa.SIAI0150.CLIIDXXX = \"$gTerId\" AND ";
			$qTerId .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ORDER BY $cAlfa.SIAI0150.CLIIDXXX LIMIT 0,1";
			$xTerId  = f_MySql("SELECT","",$qTerId,$xConexion01,"");
			$vTerId  = mysql_fetch_array($xTerId);
			//f_Mensaje(__FILE__,__LINE__,$qTerId." ~ ".mysql_num_rows($xTerId));

			$qTerIdB  = "SELECT ";
			$qTerIdB .= "$cAlfa.SIAI0150.*, ";
			$qTerIdB .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
			$qTerIdB .= "FROM $cAlfa.SIAI0150 ";
			$qTerIdB .= "WHERE ";
			$qTerIdB .= "$cAlfa.SIAI0150.$gTerTipB = \"SI\" AND ";
			$qTerIdB .= "$cAlfa.SIAI0150.CLIIDXXX = \"$gTerIdB\" AND ";
			$qTerIdB .= "$cAlfa.SIAI0150.REGESTXX = \"ACTIVO\" ORDER BY $cAlfa.SIAI0150.CLIIDXXX LIMIT 0,1";
			$xTerIdB  = f_MySql("SELECT","",$qTerIdB,$xConexion01,"");
			$vTerIdB  = mysql_fetch_array($xTerIdB);
			//f_Mensaje(__FILE__,__LINE__,$qTerIdB." ~ ".mysql_num_rows($xTerIdB));
		?>

		<script languaje = "javascript">
			document.forms['frgrm']['cTerTip'].value  = parent.window.opener.document.forms['frgrm']['cTerTip'  +<?php echo $gSecuencia ?>].value;
			document.forms['frgrm']['cTerId'].value   = parent.window.opener.document.forms['frgrm']['cTerId'   +<?php echo $gSecuencia ?>].value;
			document.forms['frgrm']['cTerDV'].value   = "<?php echo f_Digito_Verificacion($gTerId) ?>";
			document.forms['frgrm']['cTerNom'].value  = "<?php echo $vTerId['CLINOMXX'] ?>";

			document.forms['frgrm']['cTerTipB'].value = parent.window.opener.document.forms['frgrm']['cTerTipB' +<?php echo $gSecuencia ?>].value;
			document.forms['frgrm']['cTerIdB'].value  = parent.window.opener.document.forms['frgrm']['cTerIdB'  +<?php echo $gSecuencia ?>].value;
			document.forms['frgrm']['cTerDVB'].value  = "<?php echo f_Digito_Verificacion($gTerIdB) ?>";
			document.forms['frgrm']['cTerNomB'].value = "<?php echo $vTerIdB['CLINOMXX'] ?>";

		  document.forms['frgrm']['cComIdC'].value  = parent.window.opener.document.forms['frgrm']['cComIdC'  +<?php echo $gSecuencia ?>].value;
		  document.forms['frgrm']['cComCodC'].value = parent.window.opener.document.forms['frgrm']['cComCodC' +<?php echo $gSecuencia ?>].value;
		  document.forms['frgrm']['cComCscC'].value = parent.window.opener.document.forms['frgrm']['cComCscC' +<?php echo $gSecuencia ?>].value;
		  document.forms['frgrm']['cComSeqC'].value = parent.window.opener.document.forms['frgrm']['cComSeqC' +<?php echo $gSecuencia ?>].value;

			document.forms['frgrm']['cComCscC'].id    = document.forms['frgrm']['cComIdC'].value  + "~";
			document.forms['frgrm']['cComCscC'].id   += document.forms['frgrm']['cComCodC'].value + "~";
			document.forms['frgrm']['cComCscC'].id   += document.forms['frgrm']['cComCscC'].value + "~";
			document.forms['frgrm']['cComCscC'].id   += document.forms['frgrm']['cComSeqC'].value;

		  document.forms['frgrm']['cCcoId'].value   = parent.window.opener.document.forms['frgrm']['cCcoId'   +<?php echo $gSecuencia ?>].value;
		  document.forms['frgrm']['cSccId'].value   = parent.window.opener.document.forms['frgrm']['cSccId'   +<?php echo $gSecuencia ?>].value;
		  document.forms['frgrm']['cSucId'].value   = parent.window.opener.document.forms['frgrm']['cSucId'   +<?php echo $gSecuencia ?>].value;
		  document.forms['frgrm']['cDocId'].value   = parent.window.opener.document.forms['frgrm']['cDocId'   +<?php echo $gSecuencia ?>].value;
		  document.forms['frgrm']['cDocSuf'].value  = parent.window.opener.document.forms['frgrm']['cDocSuf'  +<?php echo $gSecuencia ?>].value;
		  document.forms['frgrm']['nSaldo'].value   = parent.window.opener.document.forms['frgrm']['nComVlr'  +<?php echo $gSecuencia ?>].value;
		  document.forms['frgrm']['nSaldoNF'].value = parent.window.opener.document.forms['frgrm']['nComVlrNF'+<?php echo $gSecuencia ?>].value;
		  document.forms['frgrm']['cPucTipEj'].value= parent.window.opener.document.forms['frgrm']['cPucTipEj'+<?php echo $gSecuencia ?>].value;

			document.forms['frgrm']['cComCscC'].readOnly = false;

			switch ("<?php echo $gPucDet ?>") {
				case "N":
					document.getElementById("Documento_Cruce").style.display="none";
				break;
				case "P":
				case "C":
				case "D":
					document.getElementById("Documento_Cruce").style.display="block";
				break;
				default:
					document.getElementById("Documento_Cruce").style.display="none";
				break;
			}

		</script>
	</body>
</html>

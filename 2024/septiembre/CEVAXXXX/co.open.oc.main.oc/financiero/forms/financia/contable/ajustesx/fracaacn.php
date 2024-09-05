<?php
	/**
	* Comprobante(Ajuste Contables).
	* --- Descripcion: Permite Subir y Guardar Automaticamente Ajuste Contables.
	* @version 001
	*/
	include("../../../../libs/php/utility.php");
	include("../../../../libs/php/uticonta.php");
?>
<html>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>

		<script languaje = 'javascript'>
  		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
  			document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
  	  }

			function f_Links(xLink,xSwitch,xSecuencia,xType) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
					case "cCcoId":
						var cCcoId = "";
					  switch (xType) {
					 	  case "GRID": cCcoId = document.forms['frgrm']['cCcoId'+xSecuencia].value.toUpperCase(); break;
					 	  default: 		 cCcoId = document.forms['frgrm']['cCcoId'].value.toUpperCase(); 					  break;
					  }
						if (xSwitch == "VALID") {
							var cPathUrl = "fraju116.php?gModo="+xSwitch+"&gFunction="+xLink+
																			 	 "&gCcoId="+cCcoId+
							                           "&gType="+xType+
							                           "&gSecuencia="+xSecuencia;
							//alert(cPathUrl);
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-300)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=300,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "fraju116.php?gModo="+xSwitch+"&gFunction="+xLink+
																					"&gCcoId="+cCcoId+
							                            "&gType="+xType+
							                            "&gSecuencia="+xSecuencia;
							//alert(cPathUrl);
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
				  		cWindow.focus();
						}
					break;
					case "cSccId":
						var cCcoId = ""; var cSccId = "";  var cPucDet = "";
						switch (xType) {
					 	  case "GRID":
					 	  	cCcoId = document.forms['frgrm']['cCcoId'+xSecuencia].value.toUpperCase();
					 	  	cSccId = document.forms['frgrm']['cSccId'+xSecuencia].value.toUpperCase();
					 	  	cPucDet = document.forms['frgrm']['cPucDet'+xSecuencia].value.toUpperCase();
					 	  break;
					 	  default:
					 	  	cCcoId = document.forms['frgrm']['cCcoId'].value.toUpperCase();
					 	  	cSccId = document.forms['frgrm']['cSccId'].value.toUpperCase();
					 	  	var xType = "";
					 	  break;
					  }
						if ((xType == "GRID" && cPucDet != "D") || (xType == "")) {
							if (xSwitch == "VALID") {
								var cPathUrl = "fraju120.php?gModo="+xSwitch+"&gFunction="+xLink+
																					 "&gCcoId="+cCcoId+
								                           "&gSccId="+cSccId+
								                           "&gType="+xType+
								                           "&gSecuencia="+xSecuencia;
								//alert(cPathUrl);
								parent.fmpro.location = cPathUrl;
							} else {
								var nNx      = (nX-300)/2;
								var nNy      = (nY-250)/2;
								var cWinOpt  = "width=300,scrollbars=1,height=250,left="+nNx+",top="+nNy;
								var cPathUrl = "fraju120.php?gModo="+xSwitch+"&gFunction="+xLink+
								                            "&gCcoId="+cCcoId+
								                            "&gSccId="+cSccId+
								                            "&gType="+xType+
								                            "&gSecuencia="+xSecuencia;
								//alert(cPathUrl);
								cWindow = window.open(cPathUrl,xLink,cWinOpt);
					  		cWindow.focus();
							}
						} else if (xType == "GRID" && cPucDet == "D") {
							document.forms['frgrm']['cSccId'+xSecuencia].value = document.forms['frgrm']['cComCscC'+xSecuencia].value.toUpperCase();
						}
					break;
					case "cSccId_DocId":
						var cCcoId = ""; var cSccId = ""; var cPucDet = "";
						switch (xType) {
					 	  case "GRID":
						 		cCcoId  = document.forms['frgrm']['cCcoId' +xSecuencia].value.toUpperCase();
					 	  	cSccId  = document.forms['frgrm']['cSccId' +xSecuencia].value.toUpperCase();
					 	  	cPucDet = document.forms['frgrm']['cPucDet'+xSecuencia].value.toUpperCase();
					 	  break;
					 	  default:
						 		cCcoId   = document.forms['frgrm']['cCcoId'].value.toUpperCase();
					 	  	cSccId   = document.forms['frgrm']['cSccId'].value.toUpperCase();
					 	  	var xType = "";
					 	  	document.forms['frgrm']['cSccId'].value = "";
					 	  break;
					  }
						
						if ((xType == "GRID" && cPucDet != "D") || (xType == "")) {
							if (xSwitch == "VALID") {
								var cPathUrl = "frscc121.php?gModo="+xSwitch+"&gFunction="+xLink+
																					 "&gCcoId="+cCcoId+
																					 "&gSccId="+cSccId+
								                           "&gType="+xType+
								                           "&gSecuencia="+xSecuencia;
								//alert(cPathUrl);
								parent.fmpro.location = cPathUrl;
							} else {
								var nNx      = (nX-300)/2;
								var nNy      = (nY-250)/2;
								var cWinOpt  = "width=300,scrollbars=1,height=250,left="+nNx+",top="+nNy;
								var cPathUrl = "frscc121.php?gModo="+xSwitch+"&gFunction="+xLink+
																						"&gCcoId="+cCcoId+
								                            "&gSccId="+cSccId+
								                            "&gType="+xType+
								                            "&gSecuencia="+xSecuencia;
								cWindow = window.open(cPathUrl,xLink,cWinOpt);
					  		cWindow.focus();
							}
						} else {
							if (xType == "GRID" && cPucDet == "D") {
								document.forms['frgrm']['cSccId'+xSecuencia].value = document.forms['frgrm']['cComCscC'+xSecuencia].value.toUpperCase();
							}
						}
					break;
				}
			}

		function f_Guardar() {
			if (document.forms['frgrm']['cPaso'].value == "SUBIR") { //Cargar el archivo
			  document.cookie="kModo=SUBIR;path="+"/";
			} else { //Guardar carta bancaria
				document.cookie="kModo=NUEVO;path="+"/";
			}
			
			var bSubCentro = false;
			<?php if($vSysStr['financiero_obligar_subcentro_de_costo'] == "SI") { ?>
				if (document.forms['frgrm']['cSccId'].value == '') {
					alert("El Sub Centro de Costo no puede ser Vacio, Verifique.");
					bSubCentro = true;
				}
			<?php } ?>
			
			if ( document.forms['frgrm']['cCcoId'].value == '' ) {
				alert("El Centro de Costo no Puede ser Vacio, Verifique.");
			} else if ( document.forms['frgrm']['cArcPla'].value == '' ) {
				alert("Debe Seleccionar un Archivo, Verifique.");
			} else {
				if ( bSubCentro != true ) {
					document.forms['frgrm'].submit();
				}
			}
		}
		</script>
	</head>
	<body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
		<!-- PRIMERO PINTO EL FORMULARIO -->
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="360">
				<tr>
					<td>
						<form name = "frgrm" enctype='multipart/form-data' action = "frajunue.php" method = "post">
							<fieldset>
			   				<legend>Cargue Autom&aacute;tico Ajuste Contable</legend>
								<input type = "hidden" name = "nSecuencia"  value = "0">
								<input type = "hidden" name = "cPaso"       value = "SUBIR">
							<fieldset>
							<legend>Datos Generales</legend>
							<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:360">
									<?php $cCols = f_Format_Cols(54); echo $cCols; ?>
									<tr>
										<td Class = "name" colspan = "6">
											<a href = "javascript:document.forms['frgrm']['cCcoId'].value='';
																						document.forms['frgrm']['cSccId'].value='';
																						document.forms['frgrm']['cSccId_SucId'].value='';
																						document.forms['frgrm']['cSccId_DocId'].value='';
																						document.forms['frgrm']['cSccId_DocSuf'].value='';
																						f_Links('cCcoId','VALID');" id="id_href_cCcoId">Centro Costo</a><br>
											<input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cCcoId" maxlength = "10"
												onfocus="javascript:this.value='';
																						document.forms['frgrm']['cSccId'].value='';
																						document.forms['frgrm']['cSccId_SucId'].value='';
																						document.forms['frgrm']['cSccId_DocId'].value='';
																						document.forms['frgrm']['cSccId_DocSuf'].value='';
																						this.style.background='#00FFFF'"
												onblur = "javascript:f_Links('cCcoId','VALID');
																						 this.style.background='#FFFFFF';">
										</td>
										<td Class = "name" colspan = "6">
											<a href = "javascript:document.forms['frgrm']['cSccId'].value='';
																					  document.forms['frgrm']['cSccId_SucId'].value='';
																						document.forms['frgrm']['cSccId_DocId'].value='';
																						document.forms['frgrm']['cSccId_DocSuf'].value='';
																						f_Links('cSccId','VALID');" id="id_href_cSccId">Sub Centro</a><br>
											<input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cSccId" maxlength = "20"
												onfocus="javascript:this.value='';
																						document.forms['frgrm']['cSccId_SucId'].value='';
																						document.forms['frgrm']['cSccId_DocId'].value='';
																						document.forms['frgrm']['cSccId_DocSuf'].value='';
																						this.style.background='#00FFFF'"
												onblur = "javascript:f_Links('cSccId','VALID');
																						 this.style.background='#FFFFFF';">
											<input type = "hidden" Class = "letra" style = "width:80" name = "cSccId_SucId"  readonly>
											<input type = "hidden" Class = "letra" style = "width:80" name = "cSccId_DocId"  readonly>
											<input type = "hidden" Class = "letra" style = "width:80" name = "cSccId_DocSuf" readonly>
										</td>
										<td Class = "name" colspan = "6">Periodo
											<select name="cPeriodo" style = "width:120;text-align:center">
											  <option value="ACTUAL">ACTUAL</option>
											  <option value="ANTERIOR">ANTERIOR</option>
											  
											</select>
										</td>
									</tr>
								</table>
								</fieldset>
								<center>
									<table border ="0" cellpadding="0" cellspacing="0" id ="tblArchivo">
									<tr>
										<td>
											<fieldset>
							   				<legend>Seleccione el Archivo Plano</legend>
												<table border = "0" cellpadding = "0" cellspacing = "0" width="370">
													<?php
														$cExtPer .= "application/vnd.ms-excel,";
													?>
													<tr>
														<td Class="name">
															<input type = "file" Class = "letra" style = "width:360px;height:22px" name = "cArcPla" accept="<?php echo $cExtPer ?>">
														</td>
													</tr>
													<tr>
														<td colspan = "30">
															<span style="color:#0046D5">Extensiones permitidas: .xls, .xlsx</span><br>
														</td>
													</tr> 
													<tr>
														<td Class="name"><br>
															<a href="frdowexc.php">Descargar Formato</a>
														</td>
													</tr>
												</table>
											</fieldset>
										</td>
									</tr>
								</table>
							<center>
							</fieldset>
							<center>
								<table border = "0" cellpadding = "0" cellspacing = "0" width="400">
									<tr height="21">
										<td width="218" height="21">&nbsp;</td>
										<td width="91" height="21" Class="name" >
											<input type="button" name="Btn_" id="Btn_Subir" value="Subir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif) no-repeat;border:0px"
												onclick = "javascript:f_Guardar()">
										</td>
								  	<td width="91" height="21" Class="name" >
								  		<input type="button" value="Salir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif) no-repeat;border:0px"
												onClick = "javascript:f_Retorna()">
								  	</td>
								  </tr>
								</table>
							</center>
						</form>
					</td>
				</tr>
			</table>
		</center>
	</body>
</html>
<?php
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
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script language="javascript">
			function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			}

			function f_EnabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.forms['frgrm']['cSerId'].disabled =false;
			}

			function f_DisabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.forms['frgrm']['cSerId'].disabled =true;
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

			function uLinks(xLink,xSwitch,nSecuencia) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
					case "cPucId":                           // CASO PARA cuenta //
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cPucId&gPucId="+document.forms['frgrm']['cPucId'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cPucId&gPucId="+document.forms['frgrm']['cPucId'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cPucIdExt":                           // CASO PARA cuenta //
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cPucIdExt&gPucId="+document.forms['frgrm']['cPucIdExt'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cPucIdExt&gPucId="+document.forms['frgrm']['cPucIdExt'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cPucIva":                           // CASO PARA cuenta //
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cPucIva&gPucIva="+document.forms['frgrm']['cPucIva'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cPucIva&gPucIva="+document.forms['frgrm']['cPucIva'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cPucRfte":                           // CASO PARA cuenta //
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cPucRfte&gPucId="+document.forms['frgrm']['cPucRfte'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cPucRfte&gPucId="+document.forms['frgrm']['cPucRfte'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cPucRcr":                           // CASO PARA cuenta //
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cPucRcr&gPucId="+document.forms['frgrm']['cPucRcr'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cPucRcr&gPucId="+document.forms['frgrm']['cPucRcr'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cPucARfte":                           // CASO PARA cuenta //
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cPucARfte&gPucId="+document.forms['frgrm']['cPucARfte'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cPucARfte&gPucId="+document.forms['frgrm']['cPucARfte'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cPucARcr":                           // CASO PARA cuenta //
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cPucARcr&gPucId="+document.forms['frgrm']['cPucARcr'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cPucARcr&gPucId="+document.forms['frgrm']['cPucARcr'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cPucRica":                           // CASO PARA cuenta //
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cPucRica&gPucId="+document.forms['frgrm']['cPucRica'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cPucRica&gPucId="+document.forms['frgrm']['cPucRica'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cPucARica":                           // CASO PARA cuenta //
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cPucARica&gPucId="+document.forms['frgrm']['cPucARica'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cPucARica&gPucId="+document.forms['frgrm']['cPucARica'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cPucRiva":                           // CASO PARA cuenta //
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cPucRiva&gPucId="+document.forms['frgrm']['cPucRiva'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cPucRiva&gPucId="+document.forms['frgrm']['cPucRiva'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cPucRiva01":                           // CASO PARA cuenta //
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cPucRiva01&gPucId="+document.forms['frgrm']['cPucRiva01'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cPucRiva01&gPucId="+document.forms['frgrm']['cPucRiva01'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cSerCxcIp": // CASO PARA cuenta x cobrar ingreso propio
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cSerCxcIp&gPucId="+document.forms['frgrm']['cSerCxcIp'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
		  				var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cSerCxcIp&gPucId="+document.forms['frgrm']['cSerCxcIp'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
					case "cPucRfteT":                           // CASO PARA cuenta //
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cPucRfteT&gPucId="+document.forms['frgrm']['cPucRfteT'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cPucRfteT&gPucId="+document.forms['frgrm']['cPucRfteT'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cPucARfteT":                           // CASO PARA cuenta //
						if (xSwitch == "VALID") {
							var zRuta  = "frser115.php?gWhat=VALID&gTipSav=EDITAR&gFunction=cPucARfteT&gPucId="+document.forms['frgrm']['cPucARfteT'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser115.php?gWhat=WINDOW&gTipSav=EDITAR&gFunction=cPucARfteT&gPucId="+document.forms['frgrm']['cPucARfteT'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cFcoId": // Busca Tarifas
						document.forms['frgrm']['cFcoDes'+nSecuencia].value = "";
						document.forms['frgrm']['cFcoEst'+nSecuencia].value = "";
						if (xSwitch == "VALID") {
							var zRuta  = "frser130.php?gWhat=VALID&gFunction=cFcoId"+
																			 "&gTarId="+document.forms['frgrm']['cFcoId'+nSecuencia].value.toUpperCase()+
																			 "&gIteration="+nSecuencia;
							parent.fmpro.location = zRuta;
						} else {
		  				var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser130.php?gWhat=WINDOW&gFunction=cFcoId"+
																				"&gTarId="+document.forms['frgrm']['cFcoId'+nSecuencia].value.toUpperCase()+
																				"&gIteration="+nSecuencia;
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
					case "cCceId": //Codigo Colombia Compra Eficiente
						if (xSwitch == "VALID") {
							var zRuta  = "frser156.php?gWhat=VALID&gFunction="+xLink+"&gCceId="+document.forms['frgrm']['cCceId'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							if (xSwitch == "WINDOW") {
								var zNx     = (nX-600)/2;
								var zNy     = (nY-250)/2;

								var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
								var zRuta   = "frser156.php?gWhat=WINDOW&gFunction="+xLink+"&gCceId="+document.forms['frgrm']['cCceId'].value.toUpperCase()+"";
								zWindow = window.open(zRuta,"zWindow",zWinPro);
								zWindow.focus();
							} 
							else {
								if(xSwitch == "EXACT"){
									var zRuta  = "frser156.php?gWhat=EXACT&gFunction="+xLink+"&gCceId="+document.forms['frgrm']['cCceId'].value.toUpperCase()+"";
									parent.fmpro.location = zRuta;
								}
							}
						}
					break;
					case "cUmeId": //Unidad de Medida
						if (xSwitch == "VALID") {
							var zRuta  = "frser157.php?gWhat=VALID&gFunction="+xLink+"&gUmeId="+document.forms['frgrm']['cUmeId'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							if (xSwitch == "WINDOW") {
								var zNx     = (nX-600)/2;
								var zNy     = (nY-250)/2;

								var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
								var zRuta   = "frser157.php?gWhat=WINDOW&gFunction="+xLink+"&gUmeId="+document.forms['frgrm']['cUmeId'].value.toUpperCase()+"";
								zWindow = window.open(zRuta,"zWindow",zWinPro);
								zWindow.focus();
							} else {
								if(xSwitch == "EXACT"){
									var zRuta  = "frser157.php?gWhat=EXACT&gFunction="+xLink+"&gUmeId="+document.forms['frgrm']['cUmeId'].value.toUpperCase()+"";
									parent.fmpro.location = zRuta;
								}
							}
						}
					break;
          case "cCcoId":
						if (xSwitch == "VALID") {
							var zRuta  = "frser116.php?gWhat=VALID&gFunction=cCcoId&cCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
		  				var zNx     = (nX-600)/2;
							var zNy     = (nX-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frser116.php?gWhat=WINDOW&gFunction=cCcoId&cCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
          case "cSccId":
            if (document.forms['frgrm']['cCcoId'].value != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frser120.php?gWhat=VALID&gFunction=cSccId&cCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase()+"&cSccId="+document.forms['frgrm']['cSccId'].value.toUpperCase()+"";
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (nX-600)/2;
                var zNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frser120.php?gWhat=WINDOW&gFunction=cSccId&cCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase()+"&cSccId="+document.forms['frgrm']['cSccId'].value.toUpperCase()+"";
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert('Debe Seleccionar un Centro de Costo, Verifique.');
            }
					break;
					case "cCodLineaNeg":
						if (xSwitch == "VALID") {
							var zRuta  = "frserz03.php?gWhat=VALID&gFunction="+xLink+"&gCodLineaNeg="+document.forms['frgrm']['cCodLineaNeg'+nSecuencia].value.toUpperCase()+"&gSecuencia="+nSecuencia;
							parent.fmpro.location = zRuta;
						} else {
							var zNx = (nX-800)/2;
							var zNy = (nY-500)/2;

							var zWinPro = 'width=800,scrollbars=1,height=500,left='+zNx+',top='+zNy;
							var zRuta   = "frserz03.php?gWhat=WINDOW&gFunction="+xLink+
																				"&gCodLineaNeg="+document.forms['frgrm']['cCodLineaNeg'+nSecuencia].value.toUpperCase()+
																				"&gSecuencia="+nSecuencia;
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
				}
			}

			function f_LastButton() {
				var tbl = document.getElementById('Grid_Comprobante');
				var lastRow = tbl.rows.length;
				for (i=1;i<=lastRow;i++) {

					if (i < lastRow) {

						document.forms['frgrm']['oBtnDel'+i+''].value = '';
					} else {
						//document.forms['frgrm']['oBtnDel'+i+''].value = 'X';
						document.forms['frgrm']['oBtnDel'+i+''].value = '';

					}
				}
			}

			function f_Enter(e, xGrid, nSecuencia){
				var code;

				if (!e) var e = window.event;
				if (e.keyCode) code = e.keyCode;
					else if (e.which) code = e.which;{
						if(code==13) {
							if (xGrid == 'Grid_LineaNegocio') {
								fnAddNewRowLineaNegocio('Grid_LineaNegocio')
							} else if (xGrid == 'cCodLineaNeg') {
								uLinks('cCodLineaNeg', 'VALID', nSecuencia);
							} else {
								f_Add_New_Row_Comprobante();
							}
						}
					}
			}

			function f_Delete_Row(xNumRow) {
				var tbl = document.getElementById('Grid_Comprobante');
				var lastRow = tbl.rows.length;
				if (lastRow > 1 && xNumRow == 'X'){
					if (confirm('Realmente Desea Eliminar la Secuencia')){
						tbl.deleteRow(lastRow - 1);
						document.forms['frgrm']['nSecuencia'].value = lastRow - 1;
						f_LastButton();
					}
				} else {
					alert('Operacion no Permitida');
				}
			}

			function f_Add_New_Row_Comprobante() {

				var tbl       = document.getElementById('Grid_Comprobante');
				var lastRow   = tbl.rows.length;
				var nSecuencia = lastRow+1;
				var TR        = tbl.insertRow(lastRow);
				var lRow      = nSecuencia-1;

				var cFcoId   = 'cFcoId'  + nSecuencia;
				var cFcoDes  = 'cFcoDes' + nSecuencia;
				var cFcoEst  = 'cFcoEst' + nSecuencia;
				var oBtnDel  = 'oBtnDel'  + nSecuencia;

				var TD_cFcoId = TR.insertCell(0);
				TD_cFcoId.innerHTML = "<input type = 'text'   Class = 'letra' style = 'width:040' name = "+cFcoId+"  readonly>";

				var TD_cFcoDes = TR.insertCell(1);
				TD_cFcoDes.innerHTML = "<input type = 'text'   Class = 'letra' style = 'width:380' name = "+cFcoDes+" readonly>";

				var TD_cFcoEst = TR.insertCell(2);
				TD_cFcoEst.innerHTML = "<input type = 'text'   Class = 'letra' style = 'width:060' name = "+cFcoEst+" readonly>";

				var TD_oBtnDel = TR.insertCell(3);
				TD_oBtnDel.innerHTML = "<input type = 'button' Class = 'letra' style = 'width:020' name = "+oBtnDel+"  value = 'X'>";

				document.forms['frgrm']['nSecuencia'].value = nSecuencia;
				f_LastButton();
			}

			function fnHabilitarCodCompraEficiente() {
				if (document.forms['frgrm']['cSerClapr'].value == 001) {
					document.forms['frgrm']['cCceId'].disabled = false;
					document.forms['frgrm']['cCceDes'].disabled = false;
					document.getElementById('idComEfi').href	 = "javascript:document.frgrm.cCceId.value  = ''; document.frgrm.cCceDes.value  = ''; uLinks('cCceId','VALID')";
				} else {
					document.forms['frgrm']['cCceId'].value    = "";
					document.forms['frgrm']['cCceId'].disabled = true;
					document.forms['frgrm']['cCceDes'].disabled = true;
					document.getElementById('idComEfi').href   = "javascript:alert('Opcion No Permitida')";
				}
			}

			/**
			 * Permite agregar una nueva grilla en la sección de linea de negocio.
			 */
			function fnAddNewRowLineaNegocio(xTabla) {
				var cGrid        = document.getElementById(xTabla);
				var nLastRow     = cGrid.rows.length;
				var nSecuencia   = nLastRow+1;
				var cTableRow    = cGrid.insertRow(nLastRow);
				var cCodLineaNeg = 'cCodLineaNeg' + nSecuencia; // Codigo Linea de Negocio
				var cDesLineaNeg = 'cDesLineaNeg' + nSecuencia; // Descripcion Linea de Negocio
				var cCtaIngreso  = 'cCtaIngreso'  + nSecuencia; // Cuenta de Ingreso
				var cCtaCosto    = 'cCtaCosto'    + nSecuencia; // Cuenta de Costo
				var oBtnDelLinea = 'oBtnDelLinea' + nSecuencia; // Boton de Borrar Row

				TD_xAll = cTableRow.insertCell(0);
				TD_xAll.style.width  = "120px";
				TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:120;text-align:left' name = '"+cCodLineaNeg+"' id = '"+cCodLineaNeg+"'' onkeyup='javascript:f_Enter(event, \"cCodLineaNeg\", \""+nSecuencia+"\")'>";
																		
				TD_xAll = cTableRow.insertCell(1);
				TD_xAll.style.width  = "120px";
				TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:120;text-align:left' name = '"+cDesLineaNeg+"' id = '"+cDesLineaNeg+"'' readonly>";
							
				TD_xAll = cTableRow.insertCell(2);
				TD_xAll.style.width  = "100px";
				TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:100;text-align:left' name = '"+cCtaIngreso+"' id = '"+cCtaIngreso+"''>";

				TD_xAll = cTableRow.insertCell(3);
				TD_xAll.style.width  = "100px";
				TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:100;text-align:left' name = '"+cCtaCosto+"' id = '"+cCtaCosto+"' onKeyUp='javascript:f_Enter(event,\"Grid_LineaNegocio\");'>";

				TD_xAll = cTableRow.insertCell(4);
				TD_xAll.style.width  = "20px";
				TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020;text-align:center' name = "+oBtnDelLinea+" id = "+oBtnDelLinea+" value = 'X' "+
																"onClick = 'javascript:fnDeleteRowLineaNegocio(this.value,\""+nSecuencia+"\",\""+xTabla+"\");'>";
																
				document.forms['frgrm']['nSecuencia_' + xTabla].value = nSecuencia;
			}

			/**
			 * Permite eliminar una grilla de la sección de linea de negocio.
			 */
			function fnDeleteRowLineaNegocio(xNumRow,xSecuencia,xTabla) {
        var cGrid = document.getElementById(xTabla);
        var nLastRow = cGrid.rows.length;
        if (nLastRow > 1 && xNumRow == "X") {
          if (confirm("Realmente Desea Eliminar La Linea de Negocio ["+document.forms['frgrm']['cCodLineaNeg' + xSecuencia].value+"]?")){ 
            if(xSecuencia < nLastRow){
              var j=0;
              for(var i=xSecuencia;i<nLastRow;i++){
                j = parseFloat(i)+1;
                document.forms['frgrm']['cCodLineaNeg' + i].value = document.forms['frgrm']['cCodLineaNeg' + j].value;
                document.forms['frgrm']['cDesLineaNeg' + i].value = document.forms['frgrm']['cDesLineaNeg' + j].value;
                document.forms['frgrm']['cCtaIngreso'  + i].value = document.forms['frgrm']['cCtaIngreso' + j].value;
                document.forms['frgrm']['cCtaCosto'    + i].value = document.forms['frgrm']['cCtaCosto' + j].value;
              }
            }
            cGrid.deleteRow(nLastRow - 1);
            document.forms['frgrm']['nSecuencia_' + xTabla].value = nLastRow - 1;
          }
        } else {
          alert("No se Pueden Eliminar Todas las Secuencias, Verifique.");
        }
      }

			/**
			 * Elimina todas la grillas de la sección de linea de negocio.
			 */
			function fnBorrarLineaNegocio(xTabla){
        document.getElementById(xTabla).innerHTML = "";
        fnAddNewRowLineaNegocio(xTabla);
      }
		</script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
						<fieldset>
							<legend><?php echo ucfirst(strtolower($_COOKIE['kModo']))." ".$_COOKIE['kProDes'] ?></legend>
							<form name = 'frgrm' action = 'frsergra.php' method = 'post' target='fmpro'>
								<input type = "hidden" name = "nSecuencia"    value = "0">
                <input type = "hidden" name = "nSecuencia_Grid_LineaNegocio">

								<center>
									<table border = '0' cellpadding = '0' cellspacing = '0' width='520'>
										<?php $zCol = f_Format_Cols(26);
										echo $zCol;?>
										<tr>
											<td Class = "name" colspan = "2">Codigo<br>
												<input type = "text" Class = "letra" style = "width:40" name = "cSerId" maxlength="3" readonly>
											</td>
											<td Class = "name" colspan = "18">Descripcion<br>
												<input type = "text" Class = "letra" style = "width:360" name = "cSerDes" readonly>
											</td>
											<td Class = "name" colspan = "4">T.Operacion<br>
												<input type = "text" Class = "letra" style = "width:80" name = "cSerTop" readonly>
											</td>
											<td Class = "name" colspan = "2">Orden<br>
												<input type = "text" Class = "letra" style = "width:40" name = "cSerOrd" maxlength="3"
													onblur = "javascript:f_FixInt(this);
																							this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
													onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
											</td>
										</tr>
										<tr>
											<td Class = "name" colspan = "26">Descripci&oacute;n Personalizada del Concepto de Cobro<br>
												<input type = "text" Class = "letra" style = "width:520" name = "cSerDesP"
													onBlur = "javascript:this.value=this.value.toUpperCase();
																							this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
													onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
											</td>
										</tr>
										<tr>
											<td Class = "name" colspan = "4">
												<a href = "javascript:document.forms['frgrm']['cPucId'].value  = '';
																							document.forms['frgrm']['cPucDes'].value = '';
																							uLinks('cPucId','VALID')" id="iPucId">Cta PUC</a><br>
												<input type = "text" Class = "letra" style = "width:80" name = "cPucId" maxlength="10"
													onBlur = "javascript:this.value=this.value.toUpperCase();
																							uLinks('cPucId','VALID');
																							this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
													onFocus="javascript:document.forms['frgrm']['cPucId'].value  ='';
																							document.forms['frgrm']['cPucDes'].value = '';
																							this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
											</td>
											<td Class = "name" colspan = "14">Clientes Nacionales<br>
												<input type = "text" Class = "letra" style = "width:280" name = "cPucDes" readonly>
											</td>
											<td Class = "name" colspan = "4">Movimiento<br>
												<select Class = "letrase" name = "cPucMov" style = "width:80">
													<option value = "" selected>[ SELECCIONE UNO ]</option>
													<option value = "D">DEBITO</option>
													<option value = "C">CREDITO</option>
												</select>
											</td>
											<td Class = "name" colspan = "4">Concepto<br>
												<input type = "text" Class = "letra" style = "width:80" name = "cCtoId" maxlength="10"
													onblur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
													onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
											</td>
										</tr>
										<!-- Johana Arboleda 2012-06-14 12:20
												Parametrizaciono de las cuentas para el cliente en el exterior -->
										<tr>
											<td Class = "name" colspan = "4">
												<a href = "javascript:document.forms['frgrm']['cPucIdExt'].value  = '';
																							document.forms['frgrm']['cPucDesExt'].value = '';
																							uLinks('cPucIdExt','VALID')" id="iPucIdExt">Cta PUC</a><br>
												<input type = "text" Class = "letra" style = "width:80" name = "cPucIdExt" maxlength="10"
													onBlur = "javascript:this.value=this.value.toUpperCase();
																							uLinks('cPucIdExt','VALID');
																							this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
													onFocus="javascript:document.forms['frgrm']['cPucIdExt'].value  ='';
																							document.forms['frgrm']['cPucDesExt'].value = '';
																							this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
											</td>
											<td Class = "name" colspan = "14">Clientes Exterior<br>
												<input type = "text" Class = "letra" style = "width:280" name = "cPucDesExt" readonly>
											</td>
											<td Class = "name" colspan = "4">Movimiento<br>
												<select Class = "letrase" name = "cPucMovExt" style = "width:80">
													<option value = "" selected>[ SELECCIONE UNO ]</option>
													<option value = "D">DEBITO</option>
													<option value = "C">CREDITO</option>
												</select>
											</td>
											<td Class = "name" colspan = "4">Concepto<br>
												<input type = "text" Class = "letra" style = "width:80" name = "cCtoIdExt" maxlength="10"
													onblur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
													onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
											</td>
										</tr>
									</table>

									<fieldset>
										<legend><b>Datos Tributarios</b></legend>
										<center>
											<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:500">
												<?php $zCol = f_Format_Cols(25); echo $zCol; ?>
												<tr>
													<td Class = "name" colspan = "8"><br>Iva Generado:
													</td>
													<td Class = "name" colspan = "4">
														<a href = "javascript:document.forms['frgrm']['cPucIva'].value  = '';
																									document.forms['frgrm']['cPucIvaD'].value = '';
																									document.forms['frgrm']['cRetIva'].value  = '';
																									uLinks('cPucIva','VALID')" id="iPucIva">Cta PUC</a><br>
														<input type = "text" Class = "letra" style = "width:80" name = "cPucIva" maxlength="10"
															onBlur = "javascript:this.value=this.value.toUpperCase();
																									uLinks('cPucIva','VALID');
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus="javascript:document.forms['frgrm']['cPucIva'].value  = '';
																									document.forms['frgrm']['cPucIvaD'].value = '';
																									document.forms['frgrm']['cRetIva'].value  = '';
																									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
													<td Class = "name" colspan = "11">Descripcion<br>
														<input type = "text" Class = "letra" style = "width:220" name = "cPucIvaD" readonly>
													</td>
													<td Class = "name" colspan = "3">Retencion<br>
														<input type = "text" Class = "letra" style = "width:60;text-align:right" name = "cRetIva" readonly>
													</td>
												</tr>
												<tr>
													<td Class = "name" colspan = "8"><br>Retencion en la Fuente:
													</td>
													<td Class = "name" colspan = "4">
														<a href = "javascript:document.forms['frgrm']['cPucRfte'].value  = '';
																									document.forms['frgrm']['cPucRfteD'].value = '';
																									document.forms['frgrm']['cRetRfte'].value  = '';
																									uLinks('cPucRfte','VALID')" id="iPucRfte">Cta PUC</a><br>
														<input type = "text" Class = "letra" style = "width:80" name = "cPucRfte" maxlength="10"
															onBlur = "javascript:this.value=this.value.toUpperCase();
																									uLinks('cPucRfte','VALID');
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus="javascript:document.forms['frgrm']['cPucRfte'].value  = '';
																									document.forms['frgrm']['cPucRfteD'].value = '';
																									document.forms['frgrm']['cRetRfte'].value  = '';
																									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
													<td Class = "name" colspan = "11">Descripcion<br>
														<input type = "text" Class = "letra" style = "width:220" name = "cPucRfteD" readonly>
													</td>
													<td Class = "name" colspan = "3">Retencion<br>
														<input type = "text" Class = "letra" style = "width:60;text-align:right" name = "cRetRfte" readonly>
													</td>
												</tr>
												<tr>
													<td Class = "name" colspan = "8"><br>Autoretencion en la Fuente:
													</td>
													<td Class = "name" colspan = "4">
														<a href = "javascript:document.forms['frgrm']['cPucARfte'].value  = '';
																									document.forms['frgrm']['cPucARfteD'].value = '';
																									document.forms['frgrm']['cARetRfte'].value  = '';
																									uLinks('cPucARfte','VALID')" id="iPucARfte">Cta PUC</a><br>
														<input type = "text" Class = "letra" style = "width:80" name = "cPucARfte" maxlength="10"
															onBlur = "javascript:this.value=this.value.toUpperCase();
																									uLinks('cPucARfte','VALID');
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus="javascript:document.forms['frgrm']['cPucARfte'].value  = '';
																									document.forms['frgrm']['cPucARfteD'].value = '';
																									document.forms['frgrm']['cARetRfte'].value  = '';
																									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
													<td Class = "name" colspan = "11">Descripcion<br>
														<input type = "text" Class = "letra" style = "width:220" name = "cPucARfteD" readonly>
													</td>
													<td Class = "name" colspan = "3">Retencion<br>
														<input type = "text" Class = "letra" style = "width:60;text-align:right" name = "cARetRfte" readonly>
													</td>
												</tr>
												<tr>
													<td Class = "name" colspan = "8"><br>Retencion en la Fuente: <span style="font-size:8px">(R&eacute;gimen Simple Tributaci&oacute;n)</span>
													</td>
													<td Class = "name" colspan = "4">
														<a href = "javascript:document.forms['frgrm']['cPucRfteT'].value  = '';
																									document.forms['frgrm']['cPucRfteTD'].value = '';
																									document.forms['frgrm']['cRetRfteT'].value  = '';
																									uLinks('cPucRfteT','VALID')" id="iPucRfteT">Cta PUC</a><br>
														<input type = "text" Class = "letra" style = "width:80" name = "cPucRfteT" maxlength="10"
															onBlur = "javascript:this.value=this.value.toUpperCase();
																									uLinks('cPucRfteT','VALID');
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus="javascript:document.forms['frgrm']['cPucRfteT'].value  = '';
																									document.forms['frgrm']['cPucRfteTD'].value = '';
																									document.forms['frgrm']['cRetRfteT'].value  = '';
																									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
													<td Class = "name" colspan = "11">Descripcion<br>
														<input type = "text" Class = "letra" style = "width:220" name = "cPucRfteTD" readonly>
													</td>
													<td Class = "name" colspan = "3">Retencion<br>
														<input type = "text" Class = "letra" style = "width:60;text-align:right" name = "cRetRfteT" readonly>
													</td>
												</tr>
												<tr>
													<td Class = "name" colspan = "8"><br>Autoretencion en la Fuente:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-size:8px">(R&eacute;gimen Simple Tributaci&oacute;n)</span>
													</td>
													<td Class = "name" colspan = "4">
														<a href = "javascript:document.forms['frgrm']['cPucARfteT'].value  = '';
																									document.forms['frgrm']['cPucARfteTD'].value = '';
																									document.forms['frgrm']['cARetRfteT'].value  = '';
																									uLinks('cPucARfteT','VALID')" id="iPucARfteT">Cta PUC</a><br>
														<input type = "text" Class = "letra" style = "width:80" name = "cPucARfteT" maxlength="10"
															onBlur = "javascript:this.value=this.value.toUpperCase();
																									uLinks('cPucARfteT','VALID');
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus="javascript:document.forms['frgrm']['cPucARfteT'].value  = '';
																									document.forms['frgrm']['cPucARfteTD'].value = '';
																									document.forms['frgrm']['cARetRfteT'].value  = '';
																									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
													<td Class = "name" colspan = "11">Descripcion<br>
														<input type = "text" Class = "letra" style = "width:220" name = "cPucARfteTD" readonly>
													</td>
													<td Class = "name" colspan = "3">Retencion<br>
														<input type = "text" Class = "letra" style = "width:60;text-align:right" name = "cARetRfteT" readonly>
													</td>
												</tr>
												<!-- Retencion CREE -->
												<tr>
													<td Class = "name" colspan = "8"><br>Retencion CREE:
													</td>
													<td Class = "name" colspan = "4">
														<a href = "javascript:document.forms['frgrm']['cPucRcr'].value  = '';
																									document.forms['frgrm']['cPucRcrD'].value = '';
																									document.forms['frgrm']['cRetRcr'].value  = '';
																									uLinks('cPucRcr','VALID')" id="iPucRcr">Cta PUC</a><br>
														<input type = "text" Class = "letra" style = "width:80" name = "cPucRcr" maxlength="10"
															onBlur = "javascript:this.value=this.value.toUpperCase();
																									uLinks('cPucRcr','VALID');
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus="javascript:document.forms['frgrm']['cPucRcr'].value  = '';
																									document.forms['frgrm']['cPucRcrD'].value = '';
																									document.forms['frgrm']['cRetRcr'].value  = '';
																									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
													<td Class = "name" colspan = "11">Descripcion<br>
														<input type = "text" Class = "letra" style = "width:220" name = "cPucRcrD" readonly>
													</td>
													<td Class = "name" colspan = "3">Retencion<br>
														<input type = "text" Class = "letra" style = "width:60;text-align:right" name = "cRetRcr" readonly>
													</td>
												</tr>
												<tr>
													<td Class = "name" colspan = "8"><br>Autoretencion CREE:
													</td>
													<td Class = "name" colspan = "4">
														<a href = "javascript:document.forms['frgrm']['cPucARcr'].value  = '';
																									document.forms['frgrm']['cPucARcrD'].value = '';
																									document.forms['frgrm']['cARetRcr'].value  = '';
																									uLinks('cPucARcr','VALID')" id="iPucARcr">Cta PUC</a><br>
														<input type = "text" Class = "letra" style = "width:80" name = "cPucARcr" maxlength="10"
															onBlur = "javascript:this.value=this.value.toUpperCase();
																									uLinks('cPucARcr','VALID');
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus="javascript:document.forms['frgrm']['cPucARcr'].value  = '';
																									document.forms['frgrm']['cPucARcrD'].value = '';
																									document.forms['frgrm']['cARetRcr'].value  = '';
																									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
													<td Class = "name" colspan = "11">Descripcion<br>
														<input type = "text" Class = "letra" style = "width:220" name = "cPucARcrD" readonly>
													</td>
													<td Class = "name" colspan = "3">Retencion<br>
														<input type = "text" Class = "letra" style = "width:60;text-align:right" name = "cARetRcr" readonly>
													</td>
												</tr>
												<!-- Fin Retencion CREE -->
												<tr>
													<td Class = "name" colspan = "8"><br>Retencion de ICA:
													</td>
													<td Class = "name" colspan = "4">
														<a href = "javascript:document.forms['frgrm']['cPucRica'].value  = '';
																									document.forms['frgrm']['cPucRicaD'].value = '';
																									document.forms['frgrm']['cRetRica'].value  = '';
																									uLinks('cPucRica','VALID')" id="iPucRica">Cta PUC</a><br>
														<input type = "text" Class = "letra" style = "width:80" name = "cPucRica" maxlength="10"
															onBlur = "javascript:this.value=this.value.toUpperCase();
																									uLinks('cPucRica','VALID');
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus="javascript:document.forms['frgrm']['cPucRica'].value  = '';
																									document.forms['frgrm']['cPucRicaD'].value = '';
																									document.forms['frgrm']['cRetRica'].value  = '';
																									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
													<td Class = "name" colspan = "11">Descripcion<br>
														<input type = "text" Class = "letra" style = "width:220" name = "cPucRicaD" readonly>
													</td>
													<td Class = "name" colspan = "3">Retencion<br>
														<input type = "text" Class = "letra" style = "width:60;text-align:right" name = "cRetRica" readonly>
													</td>
												</tr>
												<tr>
												<tr>
													<td Class = "name" colspan = "8"><br>Autoretencion de ICA:
													</td>
													<td Class = "name" colspan = "4">
														<a href = "javascript:document.forms['frgrm']['cPucARica'].value  = '';
																									document.forms['frgrm']['cPucARicaD'].value = '';
																									document.forms['frgrm']['cARetRica'].value  = '';
																									uLinks('cPucARica','VALID')" id="iPucARica">Cta PUC</a><br>
														<input type = "text" Class = "letra" style = "width:80" name = "cPucARica" maxlength="10"
															onBlur = "javascript:this.value=this.value.toUpperCase();
																									uLinks('cPucARica','VALID');
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus="javascript:document.forms['frgrm']['cPucARica'].value  = '';
																									document.forms['frgrm']['cPucARicaD'].value = '';
																									document.forms['frgrm']['cARetRica'].value  = '';
																									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
													<td Class = "name" colspan = "11">Descripcion<br>
														<input type = "text" Class = "letra" style = "width:220" name = "cPucARicaD" readonly>
													</td>
													<td Class = "name" colspan = "3">Retencion<br>
														<input type = "text" Class = "letra" style = "width:60;text-align:right" name = "cARetRica" readonly>
													</td>
												</tr>
												<tr>
													<td Class = "name" colspan = "8"><br>Retencion de IVA Gran Contribuyente:
													</td>
													<td Class = "name" colspan = "4">
														<a href = "javascript:document.forms['frgrm']['cPucRiva'].value  = '';
																									document.forms['frgrm']['cPucRivaD'].value = '';
																									document.forms['frgrm']['cRetRiva'].value  = '';
																									uLinks('cPucRiva','VALID')" id="iPucRiva">Cta PUC</a><br>
														<input type = "text" Class = "letra" style = "width:80" name = "cPucRiva" maxlength="10"
															onBlur = "javascript:this.value=this.value.toUpperCase();
																									uLinks('cPucRiva','VALID');
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus="javascript:document.forms['frgrm']['cPucRiva'].value  = '';
																									document.forms['frgrm']['cPucRivaD'].value = '';
																									document.forms['frgrm']['cRetRiva'].value  = '';
																									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
													<td Class = "name" colspan = "11">Descripcion<br>
														<input type = "text" Class = "letra" style = "width:220" name = "cPucRivaD" readonly>
													</td>
													<td Class = "name" colspan = "3">Retencion<br>
														<input type = "text" Class = "letra" style = "width:60;text-align:right" name = "cRetRiva" readonly>
													</td>
												</tr>
												<tr>
													<td Class = "name" colspan = "8"><br>Retencion de IVA R&eacute;gimen Com&uacute;n:
													</td>
													<td Class = "name" colspan = "4">
														<a href = "javascript:document.forms['frgrm']['cPucRiva01'].value  = '';
																									document.forms['frgrm']['cPucRivaD01'].value = '';
																									document.forms['frgrm']['cRetRiva01'].value  = '';
																									uLinks('cPucRiva01','VALID')" id="iPucRiva01">Cta PUC</a><br>
														<input type = "text" Class = "letra" style = "width:80" name = "cPucRiva01" maxlength="10"
															onBlur = "javascript:this.value=this.value.toUpperCase();
																									uLinks('cPucRiva01','VALID');
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus="javascript:document.forms['frgrm']['cPucRiva01'].value  = '';
																									document.forms['frgrm']['cPucRivaD01'].value = '';
																									document.forms['frgrm']['cRetRiva01'].value  = '';
																									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
													</td>
													<td Class = "name" colspan = "11">Descripcion<br>
														<input type = "text" Class = "letra" style = "width:220" name = "cPucRivaD01" readonly>
													</td>
													<td Class = "name" colspan = "3">Retencion<br>
														<input type = "text" Class = "letra" style = "width:60;text-align:right" name = "cRetRiva01" readonly>
													</td>
												</tr>
											</table>
										</center>
									</fieldset>

                  <?php 
                  /**
                   * Para siaco y ups no se habilita esta opción, ya que ellos manejan su propia logica de centro y subcentro
                   * de costo en la factura de venta
                   */
                  if (!f_InList($cAlfa,"SIACOSIA","TESIACOSIP","DESIACOSIP","UPSXXXXX","TEUPSXXXXX","DEUPSXXXXX")) { ?>
                    <fieldset>
                      <legend><b>Datos Adiconales</b></legend>
                      <table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
                        <?php $nCol = f_Format_Cols(25);
                        echo $nCol;?>
                        <tr>
                          <td Class = "clase08" colspan = "05">
                            <a href = "javascript:document.frgrm.cCcoId.value  = '';
                                                  document.frgrm.cCcoDes.value = '';
                                                  document.frgrm.cSccId.value  = '';
                                                  document.frgrm.cSccDes.value = '';
                                                  uLinks('cCcoId','VALID')" id="IdCco">Id</a><br>
                            <input type = 'text' Class = 'letra' style = 'width:100' name = 'cCcoId' maxlength="10"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                  uLinks('cCcoId','VALID');
                                                  this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus="javascript:document.frgrm.cCcoId.value  = '';
                                                  document.frgrm.cCcoDes.value = '';
                                                  document.frgrm.cSccId.value  = '';
                                                  document.frgrm.cSccDes.value = '';
                                                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
                          <td Class = 'clase08' colspan = '20'>Centro de Costo<br>
                            <input type = 'text' Class = 'letra' style = 'width:400' name = 'cCcoDes' readonly>
                          </td>
                        </tr>
                        <tr>
                          <td Class = "clase08" colspan = "05">
                            <a href = "javascript:document.frgrm.cSccId.value  = '';
                                                  document.frgrm.cSccDes.value = '';
                                                  uLinks('cSccId','VALID')" id="IdScc">Id</a><br>
                            <input type = 'text' Class = 'letra' style = 'width:100' name = 'cSccId' maxlength="10"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                  uLinks('cSccId','VALID');
                                                  this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus="javascript:document.frgrm.cSccId.value  = '';
                                                  document.frgrm.cSccDes.value = '';
                                                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
                          <td Class = 'clase08' colspan = '20'>Sub Centro de Costo<br>
                            <input type = 'text' Class = 'letra' style = 'width:400' name = 'cSccDes' readonly>
                          </td>
                        </tr>
                      </table>
                    </fieldset>
                  <?php } ?>

									<?php if($cAlfa == "ALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "DEALMAVIVA") { ?>
										<fieldset>
					   					<legend><b>Datos adicionales</b></legend>
											<center>
												<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:500">
													<?php $zCol = f_Format_Cols(25); echo $zCol; ?>
													<tr>
													  <td Class = "name" colspan = "8"><br>Cuenta por Cobrar:
	    											</td>
	    											<td Class = "name" colspan = "4">
	    												<a href = "javascript:document.forms['frgrm']['cSerCxcIp'].value  = '';
	    																			  		  document.forms['frgrm']['cSerCxcIpD'].value = '';
	    																							uLinks('cSerCxcIp','VALID')" id="iCtoCxcIp">Cta PUC</a><br>
	    												<input type = "text" Class = "letra" style = "width:80" name = "cSerCxcIp" maxlength="10"
	    										    	onBlur = "javascript:this.value=this.value.toUpperCase();
	    																			         uLinks('cSerCxcIp','VALID');
	    																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
	    										    	onFocus="javascript:document.forms['frgrm']['cSerCxcIp'].value  = '';
	    	            						  									document.forms['frgrm']['cSerCxcIpD'].value = '';
	    														                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
	    										  </td>
	    										  <td Class = "name" colspan = "14">Descripcion<br>
	    												 <input type = "text" Class = "letra" style = "width:280" name = "cSerCxcIpD" readonly>
	    										  </td>
	    										</tr>
												</table>
											</center>
										</fieldset>
									<?php } ?>

                  <table border = '0' cellpadding = '0' cellspacing = '0' width='520'>
										<?php $zCol = f_Format_Cols(26);
										echo $zCol;?>
										<tr>
											<td colspan="26">
												<?php if($cAlfa == "ALPOPULX" || $cAlfa == "TEALPOPULP" || $cAlfa == "TEALPOPULX" || $cAlfa == "DEALPOPULX") { ?>
													<fieldset>
														<legend><b>Integraci&oacute;n con Sistema Seven</b></legend>
														<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
															<?php $nCol = f_Format_Cols(25);
															echo $nCol;?>
															<tr>
																<td Class = "name" colspan = "7">Centro de Costo<br>
																	<input type = "text" Class = "letra"  style = "width:140"  name = "cSerSg" value = "" maxlength="6"
																		onblur = "javascript:this.value=this.value.toUpperCase();">
																</td>
																<td Class = "name" colspan = "9">C&oacute;digo Servicio ALPOPULAR<br>
																	<input type = "text" Class = "letra"  style = "width:180"  name = "cSerChsa" value = "" maxlength="10"
																		onblur = "javascript:this.value=this.value.toUpperCase();">
																	<input type = "hidden" name = "cSerCodSe" value = "">
																</td>
															</tr>
														</table>
													</fieldset>
												<?php } elseif($cAlfa == "ALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "DEALMAVIVA") { ?>
													<fieldset>
														<legend><b>Integraci&oacute;n con Sistema Seven</b></legend>
														<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
															<?php $nCol = f_Format_Cols(25);
															echo $nCol;?>
															<tr>
																<td Class = "name" colspan = "7">Centro de Costo<br>
																	<input type = "text" Class = "letra"  style = "width:140"  name = "cSerSg" value = "" maxlength="6"
																		onblur = "javascript:this.value=this.value.toUpperCase();">
																</td>
																<td Class = "name" colspan = "9">C&oacute;digo Servicio<br>
																	<input type = "text" Class = "letra"  style = "width:180"  name = "cSerChsa" value = "" maxlength="10"
																		onblur = "javascript:this.value=this.value.toUpperCase();">
																</td>
															</tr>
														</table>
													</fieldset>
													<!-- Inicio Codigo de Integracion SAP -->
													<fieldset>
														<legend><b>Integraci&oacute;n con Otros Sistemas</b></legend>
														<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
															<?php $nCol = f_Format_Cols(25);
															echo $nCol;?>
															<tr>
																<td Class = "name" colspan = "25">C&oacute;digo de Servicio Belstar/Finart<br>
																	<input type = "text" Class = "letra"  style = "width:200"  name = "cSerCodSe" value = ""
																		onblur = "javascript:this.value=this.value.toUpperCase();">
																</td>
															</tr>
														</table>
													</fieldset>
													<!-- Fin Inicio Codigo de Integracion SAP -->
												<?php } else { ?>
													<input type = "hidden" name = "cSerSg"    value = "">
													<input type = "hidden" name = "cSerChsa"  value = "">
													<input type = "hidden" name = "cSerCodSe" value = "">
												<?php } ?>
											</td>
										</tr>
										<!-- Codigo de Integracion con E2K -->
										<tr>
											<td colspan="26">
												<?php if ($cAlfa == "UPSXXXXX" || $cAlfa == "TEUPSXXXXX" || $cAlfa == "TEUPSXXXXP" || $cAlfa == "DEUPSXXXXX") { ?>
													<fieldset>
														<legend><b>Integraci&oacute;n con E2K</b></legend>
														<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
															<?php $nCol = f_Format_Cols(25);
															echo $nCol;?>
															<tr>
																<td Class = "name" colspan = "25">COD E2K<br>
																	<input type = "text" Class = "letra"  style = "width:180"  name = "cCtoE2k" value = "" maxlength="3"
																		onblur = "javascript:this.value=this.value.toUpperCase();">
																</td>
															</tr>
														</table>
													</fieldset>
												<?php } else { ?>
													<input type = "hidden" name = "cCtoE2k" value = "">
												<?php } ?>
											</td>
										</tr>
										<!-- Fin Codigo de Integracion con E2K -->
										<tr>
											<td colspan="26">
												<!-- Inicio Codigo de Integracion con Belcorp -->
												<?php if ($cAlfa == "ADUANERA" || $cAlfa == "DEADUANERA" || $cAlfa == "TEADUANERA" || $cAlfa == "DEDESARROL" ||  $cAlfa == "DEADUANERP"){?>
												<fieldset>
													<legend><b>Datos para Integraci&oacute;n con Belcorp</b></legend>
													<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
															<?php $nCol = f_Format_Cols(25);
															echo $nCol;?>
															<tr>
																<td Class = "name" colspan = "17">Tipo de Documento para Registros de Importados en SAP<br>
																	<input type = "text" Class = "letra"  style = "width:340"  name = "cPucBel" value = "" maxlength="2"
																		onblur = "javascript:this.value=this.value.toUpperCase();">
																</td>
																<td Class = "name" colspan = "8">N&uacute;mero de Asignaci&oacute;n<br>
																	<input type = "text" Class = "letra"  style = "width:160"  name = "cNuAsBel" value = "" maxlength="2"
																		onblur = "javascript:this.value=this.value.toUpperCase();">
																</td>
															</tr>
														</table>
												</fieldset>
												<?php }?>
												<!-- Fin Codigo de Integracion con Belcorp -->
											</td>
										</tr>
										<!-- Codigo Global ID SAP -->
										<tr>
											<td colspan="26">
												<?php if ($cAlfa == "TEDHLEXPRE" || $cAlfa == "DEDHLEXPRE" || $cAlfa == "DHLEXPRE") { ?>
													<fieldset>
														<legend><b>Integraci&oacute;n con SAP</b></legend>
														<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
															<?php $nCol = f_Format_Cols(25);
															echo $nCol;?>
															<tr>
																<td Class = "name" colspan = "25">Global ID SAP<br>
																	<input type = "text" Class = "letra"  style = "width:180"  name = "cSerSapId" value = "" maxlength="3"
																		onblur = "javascript:this.value=this.value.toUpperCase();">
																</td>
															</tr>
														</table>
													</fieldset>
												<?php } else { ?>
													<input type = "hidden" name = "cSerSapId" value = "">
												<?php } ?>
											</td>
										</tr>
										<!-- Fin Codigo Global ID SAP -->
										<tr>
											<td colspan="26">
											<!-- Inicio Codigo de Integracion SAP -->
											<?php 
											$vBDIntegracionColmasSap = explode("~",$vSysStr['system_integracion_colmas_sap']);
											if (in_array($cAlfa, $vBDIntegracionColmasSap) == true) {?>
											<fieldset>
												<legend><b>Integraci&oacute;n SAP</b></legend>
												<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
														<?php $nCol = f_Format_Cols(25);
														echo $nCol;?>
														<tr>
															<td Class = "name" colspan = "05">Cuenta Costo<br>
																<input type = "text" Class = "letra"  style = "width:100"  name = "cSerSapC" maxlength="8"
																	onblur = "javascript:this.value=this.value.toUpperCase();">
															</td>
															<td Class = "name" colspan = "05">Cuenta Ingreso<br>
																<input type = "text" Class = "letra"  style = "width:100"  name = "cSerSapI" maxlength="8"
																	onblur = "javascript:this.value=this.value.toUpperCase();">
															</td>
															<td Class = "name" colspan = "08">C&oacute;digo Impuesto Compra<br>
																<input type = "text" Class = "letra"  style = "width:160"  name = "cSerSapIc" maxlength="8"
																	onblur = "javascript:this.value=this.value.toUpperCase();">
															</td>
															<td Class = "name" colspan = "07">C&oacute;digo Impuesto Venta<br>
																<input type = "text" Class = "letra"  style = "width:140"  name = "cSerSapIv" maxlength="8"
																	onblur = "javascript:this.value=this.value.toUpperCase();">
															</td>
														</tr>
														<tr>
															<td Class = "name" colspan = "05">C&oacute;digo &Aacute;rea<br>
																<input type = "text" Class = "letra"  style = "width:100"  name = "cSerSapCA" maxlength="8"
																	onblur = "javascript:this.value=this.value.toUpperCase();">
															</td>
															<td Class = "name" colspan = "05">C&oacute;digo L&iacute;nea<br>
																<input type = "text" Class = "letra"  style = "width:100"  name = "cSerSapCL" maxlength="8"
																	onblur = "javascript:this.value=this.value.toUpperCase();">
															</td>
														</tr>
														<tr>
															<td colspan="25">
																<fieldset>
																	<legend>L&iacute;nea de Negocio</legend>
																	<table border = '0' cellpadding = '0' cellspacing = '0' width='460'>
																		<?php $nCol = f_Format_Cols(23); echo $nCol;?>
																		<tr>
																			<td colspan="23" class= "clase08" align="right">
																				<?php if ($_COOKIE['kModo'] != "VER") { ?>
																					<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" onClick = "javascript:fnAddNewRowLineaNegocio('Grid_LineaNegocio')" style = "cursor:pointer" title="Adicionar">
																					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:fnBorrarLineaNegocio('Grid_LineaNegocio')" style = "cursor:pointer" title="Eliminar Todos">
																				<?php } ?>
																			</td>                       
																		</tr>
																		<tr>
																			<td class = "clase08" colspan="06" align="left">L&iacute;nea de Negocio</td>
																			<td class = "clase08" colspan="06" align="left">Descripci&oacute;n Linea</td>
																			<td class = "clase08" colspan="06" align="left">Cuenta de Ingreso</td>
																			<td class = "clase08" colspan="05" align="left">Cuenta de Costo</td>
																			<td class = "clase08" colspan="01" align="right">&nbsp;</td>                       
																		</tr>
																	</table>
																	<table border = "0" cellpadding = "0" cellspacing = "0" width = "460" id = "Grid_LineaNegocio"></table>
																</fieldset>
															</td>
														</tr>
													</table>
											</fieldset>
											<?php }?>
											<!-- Fin Codigo de Integracion SAP -->
										</td>
										</tr>
										<!-- Inserción Material SAP -->
										<?php
										switch($cAlfa) {
											case "TEALMACAFE":
											case "DEALMACAFE":
                      case "ALMACAFE":
                      case "DEALPOPULX":
                      case "TEALPOPULP":
                      case "ALPOPULX":
                      case "DEALMAVIVA":
                      case "TEALMAVIVA":
                      case "ALMAVIVA": ?>
												<tr>
													<td colspan="26">
														<fieldset>
																<legend><b>Integraci&oacute;n SAP</b></legend>
																	<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
																		<tr>
																			<td Class = "name" colspan = "10">Material SAP<br>
																				<input type = "text" Class = "letra" name = "cSerMaSap" style = "width:100" maxlength="10"
																					onBlur = "javascript:f_FixInt(this); this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																					onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																			</td>
																		</tr>
																	</table>
														</fieldset>
													</td>
												</tr>
											<?php
											break;
											case "TEALADUANA":
											case "DEALADUANA":
											case "ALADUANA": ?>
												<tr>
													<td colspan="26">
														<fieldset>
																<legend><b>C&oacute;digo Homologaci&oacute;n Reporte Facturaci&oacute;n</b></legend>
																	<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
																		<tr>
																			<td Class = "name" colspan = "10">C&oacute;digo<br>
																				<input type = "text" Class = "letra" name = "cSerChAld" style = "width:100" maxlength="10"
																					onBlur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																					onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																			</td>
																		</tr>
																	</table>
														</fieldset>
													</td>
												</tr>
												<tr>
													<td colspan="26">
														<fieldset>
																<legend><b>Datos Adicionales Aladuana</b></legend>
																	<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
																		<tr>
																			<td Class = 'name' colspan = '19'>C&oacute;digo IP<br>
																				<input type = 'text' Class = 'letra' style = 'width:380' name = 'cSerCipAl'
																					onBlur = "javascript:this.value=this.value.toUpperCase();">
																			</td>
																			<td Class = 'name' colspan = '6'>Aplica C&oacute;digo Unico<br>
																				<input type = 'checkbox' name = 'cSerAplCu' style = 'width:23'>
																			</td>
																		</tr>
																	</table>
														</fieldset>
													</td>
												</tr>
											<?php
											break;
											default:
												// no hace nada
											break;
										}
										?>
										<!-- FIN Inserción Material SAP -->

										<!-- Inicio sección “Integración DSV” -->
										<tr>
											<td colspan="26">
											<?php if($cAlfa == "DSVSASXX" || $cAlfa == "TEDSVSASXX" || $cAlfa == "DEDSVSASXX") { ?>
												<fieldset>
													<legend><b>Integraci&oacute;n DSV</b></legend>
													<table border='0' cellpadding='0' cellspacing='0' width='500'>
														<tr>
															<td Class='name' colspan='19'>Charge Codes Cargowise<br>
															<input type = "text" Class = "letra" style = "width:200" maxlength="20" name = "cSercWccX"
																onblur = "javascript:this.value=this.value.toUpperCase(); this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																onFocus= "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
															</td>
														</tr>
													</table>
												</fieldset>
											</td>
										</tr>
										<?php } else { ?>
											<input type = "hidden" name = "cSercWccX" value = "">
										<?php } ?>
										<!-- Fin sección “Integración DSV” -->

										<!-- Inicio Datos Adicionales openETL -->
										<!-- Para alpopular solo se muestra la unidad de medida -->
										<tr>
											<td colspan="26">
												<fieldset>
													<?php if($cAlfa == "ALPOPULX" || $cAlfa == "TEALPOPULP" || $cAlfa == "TEALPOPULX" || $cAlfa == "DEALPOPULX") { ?>
														<legend><b>Infomaci&oacute;n Adicional</b></legend>
													<?php } else { ?>
														<legend><b>Datos Adicionales openETL</b></legend>
													<?php } ?>
													<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
														<?php $nCol = f_Format_Cols(25);
														echo $nCol;?>
														<tr id="tblClasificacionProducto">
															<td Class = "name" colspan = "25">
																Clasificaci&oacute;n Producto<br>
																<select name="cSerClapr" style="width:500" onChange = "javascript:fnHabilitarCodCompraEficiente();">
																	<option value="">[SELECCIONE]</option>
																	<option value="001">001 - UNSPSC (COLOMBIA COMPRA EFICIENTE)</option>
																	<option value="999">999 - ESTANDAR DE ADOPCION DEL CONTRIBUYENTE</option>
																</select>
															</td>
														</tr>
														<tr id="tblCodigoProducto">
															<td Class = "name" colspan = "06">
																<a href = "javascript:document.frgrm.cCceId.value  = '';
																											document.frgrm.cCceDes.value = '';
																											uLinks('cCceId','VALID')" id = "idComEfi">C&oacute;digo UNSPSC</a><br>
																<input type = "text" Class = "letra" style = "width:120" name = "cCceId" 
																	onBlur = "javascript:this.value=this.value.toUpperCase();
																											uLinks('cCceId','VALID');
																											this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																	onFocus="javascript:document.frgrm.cCceId.value  = '';
																											document.frgrm.cCceDes.value = '';
																											this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
															</td>
															<td Class = "name" colspan = "19">Descripci&oacute;n Colombia Compra Eficiente<br>
																<input type = "text" Class = "letra" style = "width:380" name = "cCceDes" readonly>
															</td>
														</tr>
														<tr>
															<td Class = "name" colspan = "06">
																<a href = "javascript:document.frgrm.cUmeId.value  = '';
																											document.frgrm.cUmeDes.value  = '';
																											uLinks('cUmeId','VALID')" id = "cCodUmed">Unidad Medida</a><br>
																<input type = "text" Class = "letra" style = "width:120" name = "cUmeId"
																	onBlur = "javascript:this.value=this.value.toUpperCase();
																											uLinks('cUmeId','VALID');
																											this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																	onFocus="javascript:document.frgrm.cUmeId.value  = '';
																											document.frgrm.cUmeDes.value  = '';
																											this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
															</td>
															<td Class = "name" colspan = "19">Descripci&oacute;n<br>
																<input type = "text" Class = "letra" style = "width:380" name = "cUmeDes" readonly>
															</td>
														</tr>
														<tr id="tblNota">
															<td Class = "letra07" colspan = "25">
																<br><b>Nota: </b> La Unidad de Medida seleccionada aplica para las formas de cobro donde el c&aacute;lculo aplicado 
																es Valor Fijo * Cantidad y no aplique M&iacute;nima. Para las dem&aacute;s formas de cobro se enviar&aacute; cantidad 1 y 
																Unidad de Medida A9-TARIFA. <br><br>Si no selecciona Unidad de Medida, sin importar la forma de cobro 
																se enviara cantidad 1 y Unidad de Medida A9-TARIFA.
															</td>
														</tr>
													</table>
												</fieldset>
											</td>
										</tr>
										<?php if($cAlfa == "ALPOPULX" || $cAlfa == "TEALPOPULP" || $cAlfa == "TEALPOPULX" || $cAlfa == "DEALPOPULX") { ?>
											<script languaje = "javascript">
												document.getElementById('tblClasificacionProducto').style.display = "none";
												document.getElementById('tblCodigoProducto').style.display  			= "none";
												document.getElementById('tblNota').style.display            			= "none";
											</script>
										<?php } ?>
										<!-- Fin Datos Adicionales openETL -->
									</table>

                  <?php if ($_COOKIE['kModo'] != "NUEVO") { ?>
										<fieldset id="iFielSet_Formas">
											<legend><b>Formas de Cobrar Este Concepto</b></legend>
											<center>
												<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:500">
													<?php $zCol = f_Format_Cols(25); echo $zCol; ?>
													<tr>
														<td colspan = "2"  class = "name"><center>Forma</center></td>
														<td colspan = "19" class = "name"><center>Descripcion</center></td>
														<td colspan = "3"  class = "name"><center>Estado</center></td>
														<td colspan = "1"  class = "name" align = "right"></td>
													</tr>
												</table>
												<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:460" id = "Grid_Comprobante"></table>
											</center>
										</fieldset>
									<?php } ?>
                  
                  <table border = '0' cellpadding = '0' cellspacing = '0' width='520'>
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
												<input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cEstado"  value = "ACTIVO" readonly
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
		<br>
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<?php
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
				f_Mensaje(__FILE__,__LINE__,"Para [INSERTAR] Un Concepto de Cobro, por favor Comunicarse con openTecnologia Ltda.");
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cEstado'].readOnly  = true;

					//Inhabilitar Campo codigo Colombia Compra Eficiente
					document.forms['frgrm']['cCceId'].disabled 	= true;
					document.getElementById('idComEfi').href		=	"javascript:alert('Opcion No Permitida')";
					f_Retorna();

					<?php 
					$vBDIntegracionColmasSap = explode("~",$vSysStr['system_integracion_colmas_sap']);
					if (in_array($cAlfa, $vBDIntegracionColmasSap) == true) {?>
						fnAddNewRowLineaNegocio('Grid_LineaNegocio');
					<?php } ?>
				</script>
			<?php break;
			case "EDITAR":
				f_CargaData($gSerId); ?>
				<script languaje = "javascript">
					document.forms['frgrm']['cSerId'].disabled	 = true;
				</script>
			<?php break;
			case "VER":
				f_CargaData($gSerId); ?>
				<script languaje = "javascript">
					document.getElementById('iPucId').href     = "javascript:alert('Opcion No Permitida')";
					document.getElementById('iPucRfte').href   = "javascript:alert('Opcion No Permitida')";
					document.getElementById('iPucRfteT').href  = "javascript:alert('Opcion No Permitida')";
					document.getElementById('iPucARfteT').href = "javascript:alert('Opcion No Permitida')";
					document.getElementById('iPucRcr').href    = "javascript:alert('Opcion No Permitida')";
					document.getElementById('iPucRica').href   = "javascript:alert('Opcion No Permitida')";
					document.getElementById('iPucRiva').href   = "javascript:alert('Opcion No Permitida')";
					document.getElementById('iPucRiva01').href = "javascript:alert('Opcion No Permitida')";
          <?php if (!f_InList($cAlfa,"SIACOSIA","TESIACOSIP","DESIACOSIP","UPSXXXXX","TEUPSXXXXX","DEUPSXXXXX")) { ?>
            document.getElementById('IdCco').href      = "javascript:alert('Opcion No Permitida')";
            document.getElementById('IdScc').href      = "javascript:alert('Opcion No Permitida')";
          <?php } ?>
					if("<?php echo $cAlfa ?>" == "ALMAVIVA" || "<?php echo $cAlfa ?>" == "TEALMAVIVA" || "<?php echo $cAlfa ?>" == "DEALMAVIVA") {
				  	document.getElementById('iCtoCxcIp').href  = "javascript:alert('Opcion No Permitida')";
					}
					document.getElementById('idComEfi').href	=	"javascript:alert('Opcion No Permitida')";

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
		function f_CargaData($gSerId) {
			global $xConexion01; global $cAlfa; global $vSysStr;
			/* TRAIGO DATOS DE CABECERA */
			$qSqlCab  = "SELECT * ";
			$qSqlCab .= "FROM $cAlfa.fpar0129 ";
			$qSqlCab .= "WHERE ";
			$qSqlCab .= "seridxxx = \"$gSerId\"  LIMIT 0,1";
			$xSqlCab  = f_MySql("SELECT","",$qSqlCab,$xConexion01,"");

			while ($zRCab = mysql_fetch_array($xSqlCab)) {
				$zMtzTar = explode("~",$zRCab['fcoidxxx']);
				/* Traigo Descripcion de la Cuenta PucId en la fpar0115 */
				$qSqlCta = "SELECT pucdesxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucidxxx']}\" LIMIT 0,1";
				$xSqlCta  = f_MySql("SELECT","",$qSqlCta,$xConexion01,"");
				$zCta = "CUENTA SIN DESCRIPCION";
				while ($zRCta = mysql_fetch_array($xSqlCta)) {
					$zCta = trim($zRCta['pucdesxx']);
				}

				/* Traigo Descripcion de la Cuenta PucId Clientes Exteror en la fpar0115 */
				$qSqlCtaExt = "SELECT pucdesxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucidexx']}\" LIMIT 0,1";
				$xSqlCtaExt  = f_MySql("SELECT","",$qSqlCtaExt,$xConexion01,"");
				$zCtaExt = "CUENTA SIN DESCRIPCION";
				while ($zRCta = mysql_fetch_array($xSqlCtaExt)) {
					$zCtaExt = trim($zRCta['pucdesxx']);
				}

				/* Traigo Descripcion de la Cuenta cPucRfte en la fpar0115 */
				$qSqlCta1 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucrftex']}\" LIMIT 0,1";
				$xSqlCta1 = f_MySql("SELECT","",$qSqlCta1,$xConexion01,"");
				$zCta1 = "CUENTA SIN DESCRIPCION";
				while ($zRCta1 = mysql_fetch_array($xSqlCta1)) {
					$cPucRfteD = trim($zRCta1['pucdesxx']);
					$cRetRfte  = trim($zRCta1['pucretxx']);
				}

				/* Traigo Descripcion de la Cuenta cPucRcr en la fpar0115 */
				$qSqlCta9 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucrcrxx']}\" LIMIT 0,1";
				$xSqlCta9 = f_MySql("SELECT","",$qSqlCta9,$xConexion01,"");
				$zCta9 = "CUENTA SIN DESCRIPCION";
				while ($zRCta9 = mysql_fetch_array($xSqlCta9)) {
					$cPucRcrD  = trim($zRCta9['pucdesxx']);
					$cRetRcr  = trim($zRCta9['pucretxx']);
				}

				/* Traigo Descripcion de la Cuenta cPucRica en la fpar0115 */
				$qSqlCta2 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucricax']}\" LIMIT 0,1";
				$xSqlCta2 = f_MySql("SELECT","",$qSqlCta2,$xConexion01,"");
				$zCta2 = "CUENTA SIN DESCRIPCION";
				while ($zRCta2 = mysql_fetch_array($xSqlCta2)) {
					$cPucRicaD = trim($zRCta2['pucdesxx']);
					$cRetRica  = trim($zRCta2['pucretxx']);
				}
				/* Traigo Descripcion de la Cuenta cPucRiva en la fpar0115 */
				$qSqlCta3 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucrivax']}\" LIMIT 0,1";
				$xSqlCta3 = f_MySql("SELECT","",$qSqlCta3,$xConexion01,"");
				$zCta3 = "CUENTA SIN DESCRIPCION";
				while ($zRCta3 = mysql_fetch_array($xSqlCta3)) {
					$cPucRivaD = trim($zRCta3['pucdesxx']);
					$cRetRiva  = trim($zRCta3['pucretxx']);
				}
				/* Traigo Descripcion de la Cuenta cPucIva en la fpar0115 */
				$qSqlCta4 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucivaxx']}\" LIMIT 0,1";
				$xSqlCta4 = f_MySql("SELECT","",$qSqlCta4,$xConexion01,"");
				$zCta4 = "CUENTA SIN DESCRIPCION";
				while ($zRCta4 = mysql_fetch_array($xSqlCta4)) {
					$cPucIvaD = trim($zRCta4['pucdesxx']);
					$cRetIva  = trim($zRCta4['pucretxx']);
				}
				/* Traigo Descripcion de la Cuenta cPucARfte en la fpar0115 */
				$qSqlCta5 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucaftex']}\" LIMIT 0,1";
				$xSqlCta5 = f_MySql("SELECT","",$qSqlCta5,$xConexion01,"");
				$zCta5 = "CUENTA SIN DESCRIPCION";
				while ($zRCta5 = mysql_fetch_array($xSqlCta5)) {
					$cPucARfteD = trim($zRCta5['pucdesxx']);
					$cARetRfte  = trim($zRCta5['pucretxx']);
				}
				
				/* Traigo Descripcion de la Cuenta cPucRfteT Retefuente Regimen Simple Tributario en la fpar0115 */
				$qSqlCta1 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucrftet']}\" LIMIT 0,1";
				$xSqlCta1 = f_MySql("SELECT","",$qSqlCta1,$xConexion01,"");
				while ($zRCta1 = mysql_fetch_array($xSqlCta1)) {
					$cPucRfteTD = trim($zRCta1['pucdesxx']);
					$cRetRfteT  = trim($zRCta1['pucretxx']);
				}
				
				/* Traigo Descripcion de la Cuenta cPucARfteT Autoretefuente Regimen Simple Tributacion en la fpar0115 */
				$qSqlCta5 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucaftet']}\" LIMIT 0,1";
				$xSqlCta5 = f_MySql("SELECT","",$qSqlCta5,$xConexion01,"");
				while ($zRCta5 = mysql_fetch_array($xSqlCta5)) {
					$cPucARfteTD = trim($zRCta5['pucdesxx']);
					$cARetRfteT  = trim($zRCta5['pucretxx']);
				}

				/* Traigo Descripcion de la Cuenta cPucARcr en la fpar0115 */
				$qSqlCta8 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucacrxx']}\" LIMIT 0,1";
				$xSqlCta8 = f_MySql("SELECT","",$qSqlCta8,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qSqlCta8."~".mysql_num_rows($xSqlCta8));
				$zCta8 = "CUENTA SIN DESCRIPCION";
				while ($zRCta8 = mysql_fetch_array($xSqlCta8)) {
					$cPucARcrD = trim($zRCta8['pucdesxx']);
					$cARetRcr  = trim($zRCta8['pucretxx']);
				}

				/* Traigo Descripcion de la Cuenta cPucARica en la fpar0115 */
				$qSqlCta6 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucaicax']}\" LIMIT 0,1";
				$xSqlCta6 = f_MySql("SELECT","",$qSqlCta6,$xConexion01,"");
				$zCta6 = "CUENTA SIN DESCRIPCION";
				while ($zRCta6 = mysql_fetch_array($xSqlCta6)) {
					$cPucARicaD = trim($zRCta6['pucdesxx']);
					$cARetRica  = trim($zRCta6['pucretxx']);
				}
				/* Traigo Descripcion de la Cuenta cPucRiva01 en la fpar0115 */
				$qSqlCta7 = "SELECT pucdesxx,pucretxx FROM $cAlfa.fpar0115 WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['pucriva1']}\" LIMIT 0,1";
				$xSqlCta7 = f_MySql("SELECT","",$qSqlCta7,$xConexion01,"");
				$zCta7 = "CUENTA SIN DESCRIPCION";
				while ($zRCta7 = mysql_fetch_array($xSqlCta7)) {
					$cPucRivaD01 = trim($zRCta7['pucdesxx']);
					$cRetRiva01  = trim($zRCta7['pucretxx']);
				}
				/* Traigo Descripcion de la Cuenta x Cobrar Ingresos propios */
				if ($zRCab['sercxcip'] != "") {
					$qCxCIP  = "SELECT pucdesxx,pucretxx ";
					$qCxCIP .= "FROM $cAlfa.fpar0115 ";
					$qCxCIP .= "WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$zRCab['sercxcip']}\" LIMIT 0,1";
					$xCxCIP = f_MySql("SELECT","",$qCxCIP,$xConexion01,"");
					while ($xRCI = mysql_fetch_array($xCxCIP)) {
						$cSerCxcIpD = trim($xRCI['pucdesxx']);
					}
				}

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
        
        /* Descripcion del Centro de Costo */
				$qDesCco  = "SELECT ccodesxx ";
				$qDesCco .= "FROM $cAlfa.fpar0116 ";
				$qDesCco .= "WHERE ccoidxxx = \"{$zRCab['ccoidxxx']}\" AND ";
				$qDesCco .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
				$xDesCco  = f_MySql("SELECT","",$qDesCco,$xConexion01,"");

			  $vRNCco = mysql_fetch_array($xDesCco);
			  $vRNCco['ccodesxx'] = ($vRNCco['ccodesxx'] != "") ? trim($vRNCco['ccodesxx']) : "";
				/* Descripcion del Centro de Costo */

				/* Descripcion del Sub Centro de Costo */
				$qDesScc  = "SELECT sccdesxx ";
				$qDesScc .= "FROM $cAlfa.fpar0120 ";
				$qDesScc .= "WHERE ccoidxxx = \"{$zRCab['ccoidxxx']}\" AND ";
				$qDesScc .= "sccidxxx = \"{$zRCab['sccidxxx']}\" AND ";
				$qDesScc .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
				$xDesScc  = f_MySql("SELECT","",$qDesScc,$xConexion01,"");

			  $vRNScc = mysql_fetch_array($xDesScc);
			  $vRNScc['ccodesxx'] = ($vRNScc['ccodesxx'] != "") ? trim($vRNScc['ccodesxx']) : "";
				/* Descripcion del Sub Centro de Costo */
				?>
				<script language = "javascript">
					document.forms['frgrm']['cSerId'].value     = "<?php echo $zRCab['seridxxx'] ?>";
					document.forms['frgrm']['cSerDes'].value    = "<?php echo $zRCab['serdesxx'] ?>";
					document.forms['frgrm']['cSerDesP'].value   = "<?php echo $zRCab['serdespx'] ?>";
					document.forms['frgrm']['cSerTop'].value    = "<?php echo $zRCab['sertopxx'] ?>";
					document.forms['frgrm']['cSerOrd'].value    = "<?php echo $zRCab['serordxx'] ?>";

					document.forms['frgrm']['cPucId'].value     = "<?php echo $zRCab['pucidxxx'] ?>";
					document.forms['frgrm']['cPucDes'].value    = "<?php echo $zCta ?>";
					document.forms['frgrm']['cPucMov'].value    = "<?php echo $zRCab['pucmovxx'] ?>";
					document.forms['frgrm']['cCtoId'].value     = "<?php echo $zRCab['ctoidxxx'] ?>";

					document.forms['frgrm']['cPucIdExt'].value  = "<?php echo $zRCab['pucidexx'] ?>";
					document.forms['frgrm']['cPucDesExt'].value = "<?php echo $zCtaExt ?>";
					document.forms['frgrm']['cPucMovExt'].value = "<?php echo $zRCab['pucmovex'] ?>";
					document.forms['frgrm']['cCtoIdExt'].value  = "<?php echo $zRCab['ctoidexx'] ?>";

					document.forms['frgrm']['cPucIva'].value    = "<?php echo $zRCab['pucivaxx'] ?>";
					document.forms['frgrm']['cPucIvaD'].value   = "<?php echo $cPucIvaD ?>";
					document.forms['frgrm']['cRetIva'].value    = "<?php echo $cRetIva ?>";

					document.forms['frgrm']['cPucRfte'].value   = "<?php echo $zRCab['pucrftex'] ?>";
					document.forms['frgrm']['cPucRfteD'].value  = "<?php echo $cPucRfteD ?>";
					document.forms['frgrm']['cRetRfte'].value   = "<?php echo $cRetRfte ?>";

					document.forms['frgrm']['cPucARfte'].value  = "<?php echo $zRCab['pucaftex'] ?>";
					document.forms['frgrm']['cPucARfteD'].value = "<?php echo $cPucARfteD ?>";
					document.forms['frgrm']['cARetRfte'].value  = "<?php echo $cARetRfte ?>";
				
					document.forms['frgrm']['cPucRfteT'].value   = "<?php echo $zRCab['pucrftet'] ?>";
					document.forms['frgrm']['cPucRfteTD'].value  = "<?php echo $cPucRfteTD ?>";
					document.forms['frgrm']['cRetRfteT'].value   = "<?php echo $cRetRfteT ?>";
					
					document.forms['frgrm']['cPucARfteT'].value  = "<?php echo $zRCab['pucaftet'] ?>";
					document.forms['frgrm']['cPucARfteTD'].value = "<?php echo $cPucARfteTD ?>";
					document.forms['frgrm']['cARetRfteT'].value  = "<?php echo $cARetRfteT ?>";

					document.forms['frgrm']['cPucRcr'].value    = "<?php echo $zRCab['pucrcrxx'] ?>";
					document.forms['frgrm']['cPucRcrD'].value   = "<?php echo $cPucRcrD ?>";
					document.forms['frgrm']['cRetRcr'].value    = "<?php echo $cRetRcr ?>";

					document.forms['frgrm']['cPucARcr'].value   = "<?php echo $zRCab['pucacrxx'] ?>";
					document.forms['frgrm']['cPucARcrD'].value  = "<?php echo $cPucARcrD ?>";
					document.forms['frgrm']['cARetRcr'].value   = "<?php echo $cARetRcr ?>";

					document.forms['frgrm']['cPucRica'].value   = "<?php echo $zRCab['pucricax'] ?>";
					document.forms['frgrm']['cPucRicaD'].value  = "<?php echo $cPucRicaD ?>";
					document.forms['frgrm']['cRetRica'].value   = "<?php echo $cRetRica ?>";

					document.forms['frgrm']['cPucARica'].value  = "<?php echo $zRCab['pucaicax'] ?>";
					document.forms['frgrm']['cPucARicaD'].value = "<?php echo $cPucARicaD ?>";
					document.forms['frgrm']['cARetRica'].value  = "<?php echo $cARetRica ?>";

					document.forms['frgrm']['cPucRiva'].value   = "<?php echo $zRCab['pucrivax'] ?>";
					document.forms['frgrm']['cPucRivaD'].value  = "<?php echo $cPucRivaD ?>";
					document.forms['frgrm']['cRetRiva'].value   = "<?php echo $cRetRiva ?>";

					document.forms['frgrm']['cPucRiva01'].value = "<?php echo $zRCab['pucriva1'] ?>";
					document.forms['frgrm']['cPucRivaD01'].value= "<?php echo $cPucRivaD01 ?>";
					document.forms['frgrm']['cRetRiva01'].value = "<?php echo $cRetRiva01 ?>";

          <?php if (!f_InList($cAlfa,"SIACOSIA","TESIACOSIP","DESIACOSIP","UPSXXXXX","TEUPSXXXXX","DEUPSXXXXX")) { ?>
            document.forms['frgrm']['cCcoId'].value     = "<?php echo $zRCab['ccoidxxx'] ?>";
            document.forms['frgrm']['cCcoDes'].value    = "<?php echo $vRNCco['ccodesxx'] ?>";
            document.forms['frgrm']['cSccId'].value     = "<?php echo $zRCab['sccidxxx'] ?>";
            document.forms['frgrm']['cSccDes'].value    = "<?php echo $vRNScc['sccdesxx'] ?>";
          <?php } ?>

					<?php if ($cAlfa == "ALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "DEALMAVIVA"){?>
						document.forms['frgrm']['cSerCxcIp'].value  = "<?php echo $zRCab['sercxcip'] ?>";
						document.forms['frgrm']['cSerCxcIpD'].value = "<?php echo $cSerCxcIpD ?>";
					<?php } ?>

					document.forms['frgrm']['cSerSg'].value     = "<?php echo $zRCab['sersgxxx'] ?>";
					document.forms['frgrm']['cSerChsa'].value   = "<?php echo $zRCab['serchsax'] ?>";
					document.forms['frgrm']['cSerCodSe'].value  = "<?php echo $zRCab['sercodse'] ?>";

					document.forms['frgrm']['cCtoE2k'].value    = "<?php echo $zRCab['ctoe2kxx'] ?>";
					document.forms['frgrm']['cSerSapId'].value  = "<?php echo $zRCab['sersapid'] ?>";

					<?php
					if ($cAlfa == "DSVSASXX" || $cAlfa == "TEDSVSASXX" || $cAlfa == "DEDSVSASXX") { ?>
						document.forms['frgrm']['cSercWccX'].value = "<?php echo $zRCab['sercwccx'] ?>"; //Charge Codes Cargowis
					<?php } ?>

					<?php 
					if ($cAlfa == "ADUANERA" || $cAlfa == "DEADUANERA" || $cAlfa == "TEADUANERA" || $cAlfa == "DEDESARROL" ||  $cAlfa == "DEADUANERP"){?>
						document.forms['frgrm']['cPucBel'].value    = "<?php echo $zRCab['pucadbel'] ?>"; //C�digo Integraci�n Belcorp
						document.forms['frgrm']['cNuAsBel'].value   = "<?php echo $zRCab['pucadnas'] ?>"; //N�mero Asignaci�n Belcorp
						if(document.forms['frgrm']['cNuAsBel'].value == "0"){
							document.forms['frgrm']['cNuAsBel'].value  = "";
						}
					<?php 
					} ?>

					<?php 
					$vBDIntegracionColmasSap = explode("~",$vSysStr['system_integracion_colmas_sap']);
					if (in_array($cAlfa, $vBDIntegracionColmasSap) == true) { ?>
						document.forms['frgrm']['cSerSapC'].value  = "<?php echo $zRCab['sersapcx'] ?>"; //Cuenta Costo
						document.forms['frgrm']['cSerSapI'].value  = "<?php echo $zRCab['sersapix'] ?>"; //Cuenta Ingreso
						document.forms['frgrm']['cSerSapIc'].value = "<?php echo $zRCab['sersapic'] ?>"; //Cuenta del impuesto de compra
						document.forms['frgrm']['cSerSapIv'].value = "<?php echo $zRCab['sersapiv'] ?>"; //Cuenta del impuesto de venta
						document.forms['frgrm']['cSerSapCA'].value = "<?php echo $zRCab['sersapca'] ?>"; //Codigo del area
						document.forms['frgrm']['cSerSapCL'].value = "<?php echo $zRCab['sersapcl'] ?>"; //Codigo de la linea

						<?php
						$mLineasNegocio = f_explode_array($zRCab['serlineg'],"|","~");
						$nCanCueCs = 0;
						for ($i=0;$i<count($mLineasNegocio);$i++) {
              if ($mLineasNegocio[$i][0] != "") { 
								$nCanCueCs++;
								$qDesLinNeg  = "SELECT lnedesxx ";
								$qDesLinNeg .= "FROM $cAlfa.zcol0003 ";
								$qDesLinNeg .= "WHERE lnecodxx = \"{$mLineasNegocio[$i][0]}\" AND ";
								$qDesLinNeg .= "regestxx = \"ACTIVO\";";
								$xDesLinNeg = f_MySql("SELECT","",$qDesLinNeg,$xConexion01,"");
								if (mysql_num_rows($xDesLinNeg) > 0) {
									$vDesLinNeg = mysql_fetch_array($xDesLinNeg);
									$vDesLinNeg = $vDesLinNeg['lnedesxx'];
								}
							?>
								fnAddNewRowLineaNegocio('Grid_LineaNegocio');
								document.forms['frgrm']['cCodLineaNeg' + document.forms['frgrm']['nSecuencia_Grid_LineaNegocio'].value].value = "<?php echo $mLineasNegocio[$i][0] ?>"
								document.forms['frgrm']['cDesLineaNeg' + document.forms['frgrm']['nSecuencia_Grid_LineaNegocio'].value].value = "<?php echo $vDesLinNeg ?>";
								document.forms['frgrm']['cCtaIngreso'  + document.forms['frgrm']['nSecuencia_Grid_LineaNegocio'].value].value = "<?php echo $mLineasNegocio[$i][1] ?>";
								document.forms['frgrm']['cCtaCosto'    + document.forms['frgrm']['nSecuencia_Grid_LineaNegocio'].value].value = "<?php echo $mLineasNegocio[$i][2] ?>";
								if ("<?php echo $_COOKIE['kModo'] ?>" == "VER") {
									document.forms['frgrm']['oBtnDelLinea' + document.forms['frgrm']['nSecuencia_Grid_LineaNegocio'].value].disabled = true;
								}
              	<?php
              }
            }
						if ($nCanCueCs == 0) { ?>
							fnAddNewRowLineaNegocio('Grid_LineaNegocio');
            <?php }
					} ?>

					<?php if ($cAlfa == "ALADUANA" || $cAlfa == "DEALADUANA" || $cAlfa == "TEALADUANA"){?>
						document.forms['frgrm']['cSerCipAl'].value   = "<?php echo $zRCab['sercipal'] ?>";
						document.forms['frgrm']['cSerAplCu'].checked = ("<?php echo $zRCab['seraplcu'] ?>" == "SI") ? true : false;
					<?php } ?>

					//// Campos Nuevos ////
					document.forms['frgrm']['cSerClapr'].value		= "<?php echo $zRCab['serclapr'] ?>";
					<?php
					if ($zRCab['serclapr'] == "001"){ ?>
						document.forms['frgrm']['cCceId'].value  	 	= "<?php echo $zRCab['cceidxxx'] ?>";
						document.forms['frgrm']['cCceDes'].value  	= "<?php echo str_replace('"','\"',$vComEfi['ccedesxx']) ?>";
						document.forms['frgrm']['cCceId'].disabled 	= false;
						document.forms['frgrm']['cCceDes'].disabled = false;
						document.getElementById('idComEfi').href		=	"javascript:document.frgrm.cCceId.value  = ''; document.frgrm.cCceDes.value  = ''; uLinks('cCceId','VALID')";
					<?php } else { ?>
						document.forms['frgrm']['cCceId'].value    	= "";
						document.forms['frgrm']['cCceId'].disabled 	= true;
						document.forms['frgrm']['cCceDes'].disabled	= true;
						document.getElementById('idComEfi').href		= "javascript:alert('Opcion No Permitida')";
					<?php } 
					if ($_COOKIE['kModo'] == "VER"){ ?>
						document.forms['frgrm']['cCceId'].disabled 	= false;
						//Link Codigo Colombia Compra Eficiente
					<?php } ?>
					//Link Unidad de Medida
					document.forms['frgrm']['cUmeId'].value       = "<?php echo $zRCab['umeidxxx'] ?>";
					document.forms['frgrm']['cUmeDes'].value     	= "<?php echo str_replace('"','\"',$vUniMed['umedesxx']) ?>";
					
					//Campos magicos
					document.forms['frgrm']['dFecCre'].value    = "<?php echo $zRCab['regfcrex'] ?>";
					document.forms['frgrm']['cHorCre'].value    = "<?php echo $zRCab['reghcrex'] ?>";
					document.forms['frgrm']['dFecMod'].value    = "<?php echo $zRCab['regfmodx'] ?>";
					document.forms['frgrm']['cHorMod'].value    = "<?php echo $zRCab['reghmodx'] ?>";
					document.forms['frgrm']['cEstado'].value    = "<?php echo $zRCab['regestxx'] ?>";

				</script>
				<?php
				## Carga de Material SAP ##
				switch ($cAlfa) {
					case "TEALMACAFE":
					case "DEALMACAFE":
          case "ALMACAFE":
          case "DEALPOPULX":
          case "TEALPOPULP":
          case "ALPOPULX":
          case "DEALMAVIVA":
          case "TEALMAVIVA":
          case "ALMAVIVA":
					?>
						<script language = "javascript">
							document.forms['frgrm']['cSerMaSap'].value = "<?php echo $zRCab['sermasap'] ?>";
						</script>
					<?php
					break;
					case "TEALADUANA":
					case "DEALADUANA":
					case "ALADUANA":
					?>
						<script language = "javascript">
							document.forms['frgrm']['cSerChAld'].value = "<?php echo $zRCab['serchald'] ?>";
						</script>
					<?php
					break;
					default:
						// no hace nada
					break;
				}
				## FIN Carga de Material SAP
			}
			if ($_COOKIE['kModo'] != "NUEVO") {
				for ($i=0;$i<count($zMtzTar);$i++) {
					if ($zMtzTar[$i] != "") {
						$qSqlTar = "SELECT * FROM $cAlfa.fpar0130 WHERE fcoidxxx = \"{$zMtzTar[$i]}\" AND regestxx = \"ACTIVO\" LIMIT 0,1";
						$xSqlTar  = f_MySql("SELECT","",$qSqlTar,$xConexion01,"");

						if (mysql_num_fields($xSqlTar) > 0) {
							$zMtz130 = mysql_fetch_array($xSqlTar); ?>
							<script language="javascript">
								f_Add_New_Row_Comprobante();
								document.forms['frgrm']['cFcoId' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $zMtz130['fcoidxxx'] ?>";
								document.forms['frgrm']['cFcoDes'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $zMtz130['fcodesxx'] ?>";
								document.forms['frgrm']['cFcoEst'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $zMtz130['regestxx'] ?>";
							</script>
						<?php }
					}
				}
			}
		}
		?>
	</body>
</html>

<?php
	/**
	 * Comprobante P-28 (Causaciones Proveedor Clientes).
	 * --- Descripcion: Permite Crear Nueva Causacion .
	 * @author Alexander Gordillo <alexanderg@repremundo.com.co>
	 * @version 001
	 */
	include("../../../../libs/php/utility.php");
	include("../../../../libs/php/uticonta.php");
	include("../../../../../config/config.php");
	include("../../../../libs/php/uticarbx.php");

?>
<html>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>

		<script languaje = 'javascript'>
  		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        if ("<?php echo $_COOKIE['kModo'] ?>" == "CONTABILIZAR") {
          parent.window.close();
        } else {
          document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
          parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
        }
  	  }

			function f_Links(xLink,xSwitch,xSecuencia,xType) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
					case "cComCod":
						if (xSwitch == "VALID") {
							var cPathUrl = "fraju117.php?gModo="+xSwitch+"&gFunction="+xLink+
							                           "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase()+"&gComId="+document.forms['frgrm']['cComId'].value.toUpperCase();
							//alert(cPathUrl);
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-500)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=500,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "fraju117.php?gModo="+xSwitch+"&gFunction="+xLink+
							                           "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase()+"&gComId="+document.forms['frgrm']['cComId'].value.toUpperCase();
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
				  		cWindow.focus();
						}
					break;
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
							                           "&gSecuencia="+xSecuencia+
																				 "&gComId="+document.forms['frgrm']['cComId'].value.toUpperCase()+
																				 "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase();
							//alert(cPathUrl);
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-300)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=300,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "fraju116.php?gModo="+xSwitch+"&gFunction="+xLink+
																					"&gCcoId="+cCcoId+
							                            "&gType="+xType+
							                            "&gSecuencia="+xSecuencia+
 																				 	"&gComId="+document.forms['frgrm']['cComId'].value.toUpperCase()+
 																				 	"&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase();
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

					/* Funciones de la Grilla */

					case "cCtoId":
				    if (document.forms['frgrm']['cComId'].value.length  > 0 &&
    				    document.forms['frgrm']['cComCod'].value.length > 0 &&
    				    document.forms['frgrm']['cCcoId'].value.length  > 0) {

              //Si el Concepto ha Cambiado en el Item, Limpio Todo el Row
  		    		if (document.forms['frgrm']['cCtoId'+xSecuencia].id != document.forms['frgrm']['cCtoId'+xSecuencia].value) {
  		    		  document.forms['frgrm']['cInvLin'  +xSecuencia].value = "";
  							document.forms['frgrm']['cInvGru'  +xSecuencia].value = "";
  							document.forms['frgrm']['cInvPro'  +xSecuencia].value = "";
  							document.forms['frgrm']['nInvCos'  +xSecuencia].value = "";
  							document.forms['frgrm']['nInvCan'  +xSecuencia].value = "";
  							document.forms['frgrm']['cInvBod'  +xSecuencia].value = "";
  							document.forms['frgrm']['cInvUbi'  +xSecuencia].value = "";
  				    	document.forms['frgrm']['cCtoDes'  +xSecuencia].value = "";
  				    	document.forms['frgrm']['cCtoAnt'  +xSecuencia].value = "";
  				    	document.forms['frgrm']['cComObs'  +xSecuencia].value = "";
  				    	document.forms['frgrm']['cComIdC'  +xSecuencia].value = "";
  				    	document.forms['frgrm']['cComCodC' +xSecuencia].value = "";
  				    	document.forms['frgrm']['cComCscC' +xSecuencia].value = "";
  							document.forms['frgrm']['cComSeqC' +xSecuencia].value = "";
  							document.forms['frgrm']['cCcoId'   +xSecuencia].value = "";
  							document.forms['frgrm']['cSccId'   +xSecuencia].value = "";
  							document.forms['frgrm']['cComCtoC' +xSecuencia].value = "";
  							document.forms['frgrm']['cComIdCB' +xSecuencia].value = "";
  				    	document.forms['frgrm']['cComCodCB'+xSecuencia].value = "";
  				    	document.forms['frgrm']['cComCscCB'+xSecuencia].value = "";
  							document.forms['frgrm']['cComSeqCB'+xSecuencia].value = "";
  							document.forms['frgrm']['cComFecCB'+xSecuencia].value = "";
  							document.forms['frgrm']['cDocInf'  +xSecuencia].value = "";
  							document.forms['frgrm']['cDocFac'  +xSecuencia].value = "";
  							document.forms['frgrm']['cDocFacA' +xSecuencia].value = "";
  							document.forms['frgrm']['nComBRet' +xSecuencia].value = "";
								document.forms['frgrm']['nComBIva' +xSecuencia].value = "";
								document.forms['frgrm']['nComIva'  +xSecuencia].value = "";
  							document.forms['frgrm']['nComVlr'  +xSecuencia].value = "";
  							document.forms['frgrm']['nComVlrNF'+xSecuencia].value = "";
  							document.forms['frgrm']['cComMov'  +xSecuencia].value = "";
  							document.forms['frgrm']['cComNit'  +xSecuencia].value = "";
  							document.forms['frgrm']['cTerTip'  +xSecuencia].value = "";
  							document.forms['frgrm']['cTerId'   +xSecuencia].value = "";
  							document.forms['frgrm']['cTerNom'  +xSecuencia].value = "";
  							document.forms['frgrm']['cTerTipB' +xSecuencia].value = "";
  							document.forms['frgrm']['cTerIdB'  +xSecuencia].value = "";
  							document.forms['frgrm']['cTerNomB' +xSecuencia].value = "";
  							document.forms['frgrm']['cPucId'   +xSecuencia].value = "";
  							document.forms['frgrm']['cPucDet'  +xSecuencia].value = "";
  							document.forms['frgrm']['cPucTer'  +xSecuencia].value = "";
  							document.forms['frgrm']['nPucBRet' +xSecuencia].value = "";
  							document.forms['frgrm']['nPucRet'  +xSecuencia].value = "";
  							document.forms['frgrm']['cPucNat'  +xSecuencia].value = "";
  							document.forms['frgrm']['cPucInv'  +xSecuencia].value = "";
  							document.forms['frgrm']['cPucCco'  +xSecuencia].value = "";
  							document.forms['frgrm']['cPucDoSc' +xSecuencia].value = "";
  							document.forms['frgrm']['cPucTipEj'+xSecuencia].value = "";
  							document.forms['frgrm']['cComVlr1' +xSecuencia].value = "";
  							document.forms['frgrm']['cComVlr2' +xSecuencia].value = "";
  							document.forms['frgrm']['cComFac'  +xSecuencia].value = "";
  							document.forms['frgrm']['cComComLi'+xSecuencia].value = "";
  							document.forms['frgrm']['cSucId'   +xSecuencia].value = "";
  							document.forms['frgrm']['cDocId'   +xSecuencia].value = "";
  							document.forms['frgrm']['cDocSuf'  +xSecuencia].value = "";
  							document.forms['frgrm']['cComEst'  +xSecuencia].value = "";

  							document.forms['frgrm']['nComBRet'+xSecuencia].disabled = true;
								document.forms['frgrm']['nComBIva'+xSecuencia].disabled = true;
								document.forms['frgrm']['nComIva' +xSecuencia].disabled = true;
  		    		}

  						if (xSwitch == "VALID") {
  							var cPathUrl = "fraju119.php?gModo="+xSwitch+"&gFunction="+xLink+
  																				 "&gComId="+document.forms['frgrm']['cComId'].value.toUpperCase()+
  																				 "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase()+
  																				 "&gCtoId="+document.forms['frgrm']['cCtoId'+xSecuencia].value.toUpperCase()+
  																				 "&gCtoIdZ="+document.forms['frgrm']['cCtoIdZ'].value.toUpperCase()+
  																				 "&gSecuencia="+xSecuencia;
  							//alert(cPathUrl);
  							parent.fmpro.location = cPathUrl;
  						} else {
  							var nNx      = (nX-600)/2;
  							var nNy      = (nY-300)/2;
  							var cWinOpt  = "width=600,scrollbars=1,height=300,left="+nNx+",top="+nNy;
  							var cPathUrl = "fraju119.php?gModo="+xSwitch+"&gFunction="+xLink+
  																				 "&gComId="+document.forms['frgrm']['cComId'].value.toUpperCase()+
  																				 "&gComCod="+document.forms['frgrm']['cComCod'].value.toUpperCase()+
  																			   "&gCtoId="+document.forms['frgrm']['cCtoId'+xSecuencia].value.toUpperCase()+
  																			   "&gCtoIdZ="+document.forms['frgrm']['cCtoIdZ'].value.toUpperCase()+
  																				 "&gSecuencia="+xSecuencia;
  							//alert(cPathUrl);
  							cWindow = window.open(cPathUrl,xLink+document.forms['frgrm']['nRandom'].value,cWinOpt);
  				  		cWindow.focus();
  						}

        		} else {
              alert("No Hay Datos de Cabecera del Comprobante Digitados, Verifique.");
        		}
					break;
					case "cTerId":
					case "cTerIdB":
					case "cTerNom":
					case "cTerNomB":
					  if (xLink == "cTerId" || xLink == "cTerNom") {
					    var cTerId  = document.forms['frgrm']['cTerNom'+xSecuencia].value.toUpperCase();
					    document.forms['frgrm']['cTerTip'+xSecuencia].value = "";
					    document.forms['frgrm']['cTerId'+xSecuencia].value  = "";
					    document.forms['frgrm']['cTerNom'+xSecuencia].value = "";

							document.forms['frgrm']['cDocFac'+xSecuencia].value = "";
							document.forms['frgrm']['cDocFacA'+xSecuencia].value = "";
							
					  } else if (xLink == "cTerIdB" || xLink == "cTerNomB") {
					    var cTerId  = document.forms['frgrm']['cTerNomB'+xSecuencia].value.toUpperCase();
					    document.forms['frgrm']['cTerTipB'+xSecuencia].value = "";
					    document.forms['frgrm']['cTerIdB'+xSecuencia].value  = "";
					    document.forms['frgrm']['cTerNomB'+xSecuencia].value = "";
					  }

						if (xSwitch == "VALID") {
							var cPathUrl = "fraju150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gTerId="+cTerId+
																				"&gSecuencia="+xSecuencia;
							//alert(cPathUrl);
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-600)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "fraju150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				 "&gTerId="+cTerId+
																				 "&gSecuencia="+xSecuencia;
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
				  		cWindow.focus();
						}
					break;
					case "cComCscC":

						//Si la cuenta detalla por DO:
						//se debe digitar obligatoriamente el cliente
						//Si se digita el cliente y no se selecciona proveedor, se asume que el cliente es el mismo proveedor

						//Si la cuenta es por cobrar o por pagar
						//Se debe digitar el cliente o el proveedor o ambos
						//Si se digita el cliente, se buscan las CxC o CxP de ese cliente (en el teridxxx)
						//y cuando se seleccione el comprobante se completa la informacion del tercero
						//Si se digita solo el tercero, se buscan las CxC o CxP de ese tercero (en el terid2xx)
						//y cuando se seleccione el comprobante se completa la informacion del cliente

						//Si la cuenta no detalla se debe digitar el cliente
						var nSwicht = 0; var cMsj = "";
						switch (document.forms['frgrm']['cPucDet'+xSecuencia].value) {
							case "C":
							case "P":
								if ((document.forms['frgrm']['cTerTip' +xSecuencia].value.length > 0 &&
										 document.forms['frgrm']['cTerId'  +xSecuencia].value.length > 0 &&
										 document.forms['frgrm']['cTerNom' +xSecuencia].value.length > 0) ||
					    			(document.forms['frgrm']['cTerTipB'+xSecuencia].value.length > 0 &&
							    	 document.forms['frgrm']['cTerIdB' +xSecuencia].value.length > 0 &&
					    			 document.forms['frgrm']['cTerNomB'+xSecuencia].value.length > 0)){
					    			//Permite desplegar la ventana
									} else {
										nSwicht = 1;
										cMsj = "El Tipo, Nit o Nombre del Documento Cruce del Cliente o Tercero no Pueden ser Vacios, Verifique.";
									}
							break;
							default: //Es decir la que detallan por DO y las que no detallan
								if (document.forms['frgrm']['cTerTip' +xSecuencia].value.length > 0 &&
				    				document.forms['frgrm']['cTerId'  +xSecuencia].value.length > 0 &&
				    				document.forms['frgrm']['cTerNom' +xSecuencia].value.length > 0) {
				    			//Permite desplegar la ventana
								} else {
									nSwicht = 1;
									cMsj = "El Tipo, Nit o Nombre del Documento Cruce del Cliente no Pueden ser Vacios, Verifique.";
								}
							break;
						}

						if (nSwicht == 0) {

    					switch (xType) {
    						case "CRUCE_UNO":
    							document.forms['frgrm']['cComIdC'  +xSecuencia].value = "";
						    	document.forms['frgrm']['cComCodC' +xSecuencia].value = "";
									document.forms['frgrm']['cComSeqC' +xSecuencia].value = "";
									document.forms['frgrm']['cCcoId'   +xSecuencia].value = "";
									document.forms['frgrm']['cSccId'   +xSecuencia].value = "";
									document.forms['frgrm']['cComIdCB' +xSecuencia].value = "";
						    	document.forms['frgrm']['cComCodCB'+xSecuencia].value = "";
						    	document.forms['frgrm']['cComCscCB'+xSecuencia].value = "";
									document.forms['frgrm']['cComSeqCB'+xSecuencia].value = "";
									document.forms['frgrm']['cComFecCB'+xSecuencia].value = "";
									document.forms['frgrm']['cDocInf'  +xSecuencia].value = "";
									document.forms['frgrm']['cSucId'   +xSecuencia].value = "";
									document.forms['frgrm']['cDocId'   +xSecuencia].value = "";
									document.forms['frgrm']['cDocSuf'  +xSecuencia].value = "";
									document.forms['frgrm']['cComEst'  +xSecuencia].value = "";
									if (document.forms['frgrm']['cPucDet'+xSecuencia].value != "D") { // Difernete a detalle "D" de DO's.
										document.forms['frgrm']['nComBRet' +xSecuencia].value = "";
										document.forms['frgrm']['nComBIva' +xSecuencia].value = "";
										document.forms['frgrm']['nComIva'  +xSecuencia].value = "";
										document.forms['frgrm']['nComVlr'  +xSecuencia].value = "";
										document.forms['frgrm']['nComVlrNF'+xSecuencia].value = "";
									}

									var cTerIdAux  = document.forms['frgrm']['cTerId' +xSecuencia].value;
									var cTerIdBAux = document.forms['frgrm']['cTerIdB'+xSecuencia].value;

    						break;
    						case "CRUCE_DOS":
									document.forms['frgrm']['cComIdCB' +xSecuencia].value = "";
						    	document.forms['frgrm']['cComCodCB'+xSecuencia].value = "";
						    	document.forms['frgrm']['cComCscCB'+xSecuencia].value = "";
									document.forms['frgrm']['cComSeqCB'+xSecuencia].value = "";
									document.forms['frgrm']['cComFecCB'+xSecuencia].value = "";
									document.forms['frgrm']['cDocInf'  +xSecuencia].value = "";

									if (document.forms['frgrm']['cTerId' +xSecuencia].value != "" && document.forms['frgrm']['cTerIdB'+xSecuencia].value != "") {
										var cTerIdAux  = document.forms['frgrm']['cTerId' +xSecuencia].value;
										var cTerIdBAux = document.forms['frgrm']['cTerIdB'+xSecuencia].value;
									} else if (document.forms['frgrm']['cTerId' +xSecuencia].value != "" && document.forms['frgrm']['cTerIdB'+xSecuencia].value == "") {
										var cTerIdAux  = document.forms['frgrm']['cTerId' +xSecuencia].value;
										var cTerIdBAux = document.forms['frgrm']['cTerId' +xSecuencia].value;
									}else {
										var cTerIdAux  = document.forms['frgrm']['cTerIdB'+xSecuencia].value;
										var cTerIdBAux = document.forms['frgrm']['cTerIdB'+xSecuencia].value;
									}
    						break;
    					}

    					var cTerId = ""; var cTerId2 = "";
							switch (document.forms['frgrm']['cComNit'+xSecuencia].value) {
								case "CLIENTE":  // Cliente
									/**
  								 * Alexander Gordillo 30-04-2007 a las 10:30
  								 * Hay casos en los que se elije un concepto en la L-38 que tiene parametrizado en la fpar0119 que el NIT que lleva a SIIGO
  								 * es el del CLIENTE, pero para el caso del AJUSTE CONTABLE necesitan afectarlo con el NIT del PROVEEDOR, por eso pregunto
  								 * que si el NIT que llevo a SIIGO es diferente de vacio y llevo este a la busqueda de los comprobantes, de lo contrario
  								 * llevo el otro NIT, es decir, si no es el CLIENTE es el PROVEEDOR o viseversa.
  								 */
									if (cTerIdAux.length > 0) {
										cTerId  = cTerIdAux;
										cTerId2 = cTerIdBAux;
									} else {
										cTerId  = cTerIdBAux;
										cTerId2 = cTerIdAux;
									}
								break;
								case "TERCERO":  // Proveedor
									/**
  								 * Alexander Gordillo 30-04-2007 a las 10:30
  								 * Hay casos en los que se elije un concepto en la L-38 que tiene parametrizado en la fpar0119 que el NIT que lleva a SIIGO
  								 * es el del CLIENTE, pero para el caso del AJUSTE CONTABLE necesitan afectarlo con el NIT del PROVEEDOR, por eso pregunto
  								 * que si el NIT que llevo a SIIGO es diferente de vacio y llevo este a la busqueda de los comprobantes, de lo contrario
  								 * llevo el otro NIT, es decir, si no es el CLIENTE es el PROVEEDOR o viseversa.
  								 */
									if (cTerIdBAux.length > 0) {
										cTerId  = cTerIdBAux;
										cTerId2 = cTerIdAux;
									} else {
										cTerId  = cTerIdAux;
										cTerId2 = cTerIdBAux;
									}
								break;
							}

							switch (document.forms['frgrm']['cPucDet'+xSecuencia].value) {
    				    case "N": // Cuenta no Detalla
  								if(document.forms['frgrm']['cPucId'+xSecuencia].value == "1710201000" && xType == "CRUCE_DOS"){
  								  var mMemoCru="|";
  								  for(i=0;i<document.forms['frgrm']['nSecuencia'].value;i++){
                      if(document.frgrm['cPucId'   +(i+1)].value == "2805050000" && // Cuenta de DO's
                         document.frgrm['cComCscC' +(i+1)].value != ""           && // Que el documento cruce sea difernete de vacio
                         document.frgrm['cComIdCB' +(i+1)].value == "F"          && // Que el COMIDC2 sea igual a F
                         document.frgrm['cCscCruCB'+(i+1)].value != "") {           // Que el COMCSCC2 sea diferente de vacio
  								    	mMemoCru += "F"+"~"+document.forms['frgrm']['cComCodCB'+(i+1)].value+"~"+document.forms['frgrm']['cComCscCB'+(i+1)].value+"|";
  								  	}
  								  }

  								  if(mMemoCru != ""){
  								  	var nNx      = ((nX-750)/2);
											var nNy      = (nY-300)/2;
											var cWinOpt  = 'width=750,scrollbars=1,height=300,left='+nNx+',top='+nNy;
										  var cPathUrl = "frajucom.php?gModo="+xSwitch+"&gFunction="+xLink+
										  													 "&gTerId="+cTerId+
											  												 "&gTerId2="+cTerId2+
										  													 "&gMemoCru="+mMemoCru+
										  													 "&gComNit="+document.forms['frgrm']['cComNit'+xSecuencia].value.toUpperCase()+
										  													 "&gPucId="+document.forms['frgrm']['cPucId'+xSecuencia].value.toUpperCase()+
										  													 "&gPucDet="+document.forms['frgrm']['cPucDet'+xSecuencia].value.toUpperCase()+
											  												 "&gPucTipEj="+document.forms['frgrm']['cPucTipEj'+xSecuencia].value.toUpperCase()+
										  													 "&gTipCru="+xType+
											  												 "&gCtoAnt="+document.forms['frgrm']['cCtoAnt'+xSecuencia].value.toUpperCase()+
										  													 "&gComIdC="+document.forms['frgrm']['cComIdC'+xSecuencia].value.toUpperCase()+
																						     "&gComCodC="+document.forms['frgrm']['cComCodC'+xSecuencia].value.toUpperCase()+
																						     "&gComCscC="+document.forms['frgrm']['cComCscC'+xSecuencia].value.toUpperCase()+
																								 "&gComSeqC="+document.forms['frgrm']['cComSeqC'+xSecuencia].value.toUpperCase()+
																								 "&gModoLiq="+document.forms['frgrm']['cModo'].value.toUpperCase()+
										  													 "&gSecuencia="+xSecuencia;
										  //alert(cPathUrl);
										  cWindow = window.open(cPathUrl,xLink,cWinOpt);
		      				  	cWindow.focus();
  								  }else{
  								    alert("No se Encontraron DOs en la Grilla Para Mostrar Las Facturas Correspondientes, Verifique.");
  								  }
  								}
    				    break;
    				    case "C": // Cuentas por Cobrar
    				    case "P": // Cuentas por Pagar
    				    case "D": // Cuenta de DO's
									var nNx      = ((nX-750)/2);
									var nNy      = (nY-300)/2;
									var cWinOpt  = 'width=750,scrollbars=1,height=300,left='+nNx+',top='+nNy;
								  var cPathUrl = "frajucom.php?gModo="+xSwitch+"&gFunction="+xLink+
								  													 "&gTerId="+cTerId+
									  												 "&gTerId2="+cTerId2+
										  											 "&gComNit="+document.forms['frgrm']['cComNit'+xSecuencia].value.toUpperCase()+
								  													 "&gPucId="+document.forms['frgrm']['cPucId'+xSecuencia].value.toUpperCase()+
								  													 "&gPucDet="+document.forms['frgrm']['cPucDet'+xSecuencia].value.toUpperCase()+
									  												 "&gPucTipEj="+document.forms['frgrm']['cPucTipEj'+xSecuencia].value.toUpperCase()+
								  													 "&gTipCru="+xType+
									  												 "&gCtoAnt="+document.forms['frgrm']['cCtoAnt'+xSecuencia].value.toUpperCase()+
								  													 "&gCtoId=" +document.forms['frgrm']['cCtoId'+xSecuencia].value.toUpperCase()+
								  													 "&gComIdC="+document.forms['frgrm']['cComIdC'+xSecuencia].value.toUpperCase()+
																						 "&gComCodC="+document.forms['frgrm']['cComCodC'+xSecuencia].value.toUpperCase()+
																						 "&gComCscC="+document.forms['frgrm']['cComCscC'+xSecuencia].value.toUpperCase()+
																						 "&gComSeqC="+document.forms['frgrm']['cComSeqC'+xSecuencia].value.toUpperCase()+
																						 "&gModoLiq="+document.forms['frgrm']['cModo'].value.toUpperCase()+
								  													 "&gSecuencia="+xSecuencia;
								  //alert(cPathUrl);
								  cWindow = window.open(cPathUrl,xLink,cWinOpt);
      				  	cWindow.focus();
    				  	break;
    				  }
    				} else {
    					alert(cMsj);
    				}
					break;
					case "cDocFac":
						if(document.forms['frgrm']['cTerId'+xSecuencia].value != ""){
							if(document.forms['frgrm']['cDocFac'+xSecuencia].value != ""){
								if (xSwitch == "VALID") {
									var cPathUrl = "frajufac.php?gModo="+xSwitch+"&gFunction="+xLink+
																						"&gDocFac="+document.forms['frgrm']['cDocFac'+xSecuencia].value+
																						"&gTerId="+document.forms['frgrm']['cTerId'+xSecuencia].value+
																						"&gSecuencia="+xSecuencia;
									parent.fmpro.location = cPathUrl;
								}
							}else{
								document.forms['frgrm']['cDocFacA'+xSecuencia].value = "";
								alert("Indique Consecutivo de la factura.");
							}
						}else{
							alert("Debe indicar el Cliente.");
						}
					break;
				}
			}

			function f_Last_Button() {
				var cGrid = document.getElementById("Grid_Comprobante");
				var nLastRow = cGrid.rows.length;
				for (i=1;i<=nLastRow;i++) {
					var cRow = document.getElementById("oBtnDel" + i);
					if (i < nLastRow) {
						cRow.value = "";
					} else {
						cRow.value = "X";
					}
				}
			}

			function f_KeyUp(e){
				var code;
				if (!e) {
					var e = window.event;
				}
				if (e.keyCode) {
					code = e.keyCode;
				} else {
					if (e.which) {
						code = e.which;
					}
				}
				return code;
			}

	    function f_Enter(e,xName) {
				var code;
				if (!e) {
					var e = window.event;
				}
				if (e.keyCode) {
					code = e.keyCode;
				} else {
					if (e.which) {
						code = e.which;
					}
				}
				if (code == 13){
					if (xName == "nComVlr"+parseFloat(document.forms['frgrm']['nSecuencia'].value) || xName == "nComVlrNF"+parseFloat(document.forms['frgrm']['nSecuencia'].value)) {
						f_Add_New_Row_Comprobante();
					}
				}
			}

	    function f_Delete_Row(xNumRow,xSecuencia,xTabla) {
				switch (xTabla) {
					case "Grid_Comprobante":
						var cGrid = document.getElementById(xTabla);
						var nLastRow = cGrid.rows.length;
						if (nLastRow > 1 && xNumRow == "X") {
							if (confirm("Realmente Desea Eliminar la Secuencia?")){
					  		if(xSecuencia < nLastRow){
		            	var j=0;
		             	for(var i=xSecuencia;i<nLastRow;i++){
		           	  	j = parseFloat(i)+1;
				            document.forms['frgrm']['cComSeq'   + i].value = f_Str_Pad(i,3,"0","STR_PAD_LEFT"); // Secuencia
				            document.forms['frgrm']['cCtoId'    + i].value = document.forms['frgrm']['cCtoId'   + j].value; // Id del Concepto
				            document.forms['frgrm']['cCtoDes'   + i].value = document.forms['frgrm']['cCtoDes'  + j].value; // Descripcion del Concepto
				            document.forms['frgrm']['cCtoAnt'   + i].value = document.forms['frgrm']['cCtoAnt'  + j].value; // Hidden (Concepto de Anticipos?)
				            document.forms['frgrm']['cInvLin'   + i].value = document.forms['frgrm']['cInvLin'  + j].value; // Hidden (Inventario - Linea)
				            document.forms['frgrm']['cInvGru'   + i].value = document.forms['frgrm']['cInvGru'  + j].value; // Hidden (Inventario - Grupo)
				            document.forms['frgrm']['cInvPro'   + i].value = document.forms['frgrm']['cInvPro'  + j].value; // Hidden (Inventario - Producto)
				            document.forms['frgrm']['nInvCan'   + i].value = document.forms['frgrm']['nInvCan'  + j].value; // Hidden (Inventario - Cantidad)
				            document.forms['frgrm']['nInvCos'   + i].value = document.forms['frgrm']['nInvCos'  + j].value; // Hidden (Inventario - Costo Unitario)
				            document.forms['frgrm']['cInvBod'   + i].value = document.forms['frgrm']['cInvBod'  + j].value; // Hidden (Inventario - Bodega)
				            document.forms['frgrm']['cInvUbi'   + i].value = document.forms['frgrm']['cInvUbi'  + j].value; // Hidden (Inventario - Ubicacion)
				            document.forms['frgrm']['cComObs'   + i].value = document.forms['frgrm']['cComObs'  + j].value; // Observacion del Comprobante
				            document.forms['frgrm']['cComIdC'   + i].value = document.forms['frgrm']['cComIdC'  + j].value; // Id Comprobante Cruce
				            document.forms['frgrm']['cComCodC'  + i].value = document.forms['frgrm']['cComCodC' + j].value; // Codigo Comprobante Cruce
				            document.forms['frgrm']['cComCscC'  + i].value = document.forms['frgrm']['cComCscC' + j].value; // Consecutivo Comprobante Cruce
				            document.forms['frgrm']['cComSeqC'  + i].value = document.forms['frgrm']['cComSeqC' + j].value; // Secuencia Comprobante Cruce
				            document.forms['frgrm']['cCcoId'    + i].value = document.forms['frgrm']['cCcoId'   + j].value; // Centro de Costos
				            document.forms['frgrm']['cSccId'    + i].value = document.forms['frgrm']['cSccId'   + j].value; // Sub Centro de Costos
				            document.forms['frgrm']['cComCtoC'  + i].value = document.forms['frgrm']['cComCtoC' + j].value; // Concepto Comprobante Cruce
				            document.forms['frgrm']['cComIdCB'  + i].value = document.forms['frgrm']['cComIdCB' + j].value; // Id Comprobante Cruce Dos
				            document.forms['frgrm']['cComCodCB' + i].value = document.forms['frgrm']['cComCodCB'+ j].value; // Codigo Comprobante Cruce Dos
				            document.forms['frgrm']['cComCscCB' + i].value = document.forms['frgrm']['cComCscCB'+ j].value; // Consecutivo Comprobante Cruce Dos
				            document.forms['frgrm']['cComSeqCB' + i].value = document.forms['frgrm']['cComSeqCB'+ j].value; // Secuencia Comprobante Cruce Dos
				            document.forms['frgrm']['cComFecCB' + i].value = document.forms['frgrm']['cComFecCB'+ j].value; // Fecha de Creacion del Comprobante Cruce Dos
				            document.forms['frgrm']['cDocInf'   + i].value = document.forms['frgrm']['cDocInf'  + j].value; // Fecha de Creacion del Comprobante Cruce Dos
				            document.forms['frgrm']['cDocFac'   + i].value = document.forms['frgrm']['cDocFac'  + j].value; // Consecutivo Informativo de Factura
				            document.forms['frgrm']['cDocFacA'  + i].value = document.forms['frgrm']['cDocFacA' + j].value; // Año Consecutivo Informativo de Factura
				            document.forms['frgrm']['nComBRet' 	+ i].value = document.forms['frgrm']['nComBRet' + j].value; // Base de Retencion
				            document.forms['frgrm']['nComBIva' 	+ i].value = document.forms['frgrm']['nComBIva' + j].value; // Base de Iva
				            document.forms['frgrm']['nComIva'  	+ i].value = document.forms['frgrm']['nComIva'  + j].value; // Valor del Iva
				            document.forms['frgrm']['nComVlr'   + i].value = document.forms['frgrm']['nComVlr'  + j].value; // Valor del Comprobante
				            document.forms['frgrm']['nComVlrNF' + i].value = document.forms['frgrm']['nComVlrNF'+ j].value; // Valor NIIF del Comprobante
				            document.forms['frgrm']['cComMov'   + i].value = document.forms['frgrm']['cComMov'  + j].value; // Movimiento Debito o Credito
				            document.forms['frgrm']['cComNit'   + i].value = document.forms['frgrm']['cComNit'  + j].value; // Hidden (Nit que va para SIIGO)
				            document.forms['frgrm']['cTerTip'   + i].value = document.forms['frgrm']['cTerTip'  + j].value; // Hidden (Tipo de Tercero)
				            document.forms['frgrm']['cTerId'    + i].value = document.forms['frgrm']['cTerId'   + j].value; // Hidden (Id del Tercero)
				            document.forms['frgrm']['cTerNom'   + i].value = document.forms['frgrm']['cTerNom'  + j].value; // Nombre del Tercero
				            document.forms['frgrm']['cTerTipB'  + i].value = document.forms['frgrm']['cTerTipB' + j].value; // Hidden (Tipo de Tercero Dos)
				            document.forms['frgrm']['cTerIdB'   + i].value = document.forms['frgrm']['cTerIdB'  + j].value; // Hidden (Id del Tercero Dos)
				            document.forms['frgrm']['cTerNomB'  + i].value = document.forms['frgrm']['cTerNomB' + j].value; // Nombre del Tercero B
				            document.forms['frgrm']['cPucId'    + i].value = document.forms['frgrm']['cPucId'   + j].value; // Hidden (La Cuenta Contable)
				            document.forms['frgrm']['cPucDet'   + i].value = document.forms['frgrm']['cPucDet'  + j].value; // Hidden (Detalle de la Cuenta)
				            document.forms['frgrm']['cPucTer'   + i].value = document.forms['frgrm']['cPucTer'  + j].value; // Hidden (Cuenta de Terceros?)
				            document.forms['frgrm']['nPucBRet'  + i].value = document.forms['frgrm']['nPucBRet' + j].value; // Hidden (Base de Retencion de la Cuenta)
				            document.forms['frgrm']['nPucRet'   + i].value = document.forms['frgrm']['nPucRet'  + j].value; // Hidden (Porcentaje de Retencion de la Cuenta)
				            document.forms['frgrm']['cPucNat'   + i].value = document.forms['frgrm']['cPucNat'  + j].value; // Hidden (Naturaleza de la Cuenta)
				            document.forms['frgrm']['cPucInv'   + i].value = document.forms['frgrm']['cPucInv'  + j].value; // Hidden (Cuenta de Inventarios?)
				            document.forms['frgrm']['cPucCco'   + i].value = document.forms['frgrm']['cPucCco'  + j].value; // Hidden (Para esta Cuenta Aplica Centro de Costos?)
				            document.forms['frgrm']['cPucDoSc'  + i].value = document.forms['frgrm']['cPucDoSc' + j].value; // Hidden (Aplica DO para Subcentro de Costo?)
				            document.forms['frgrm']['cPucTipEj' + i].value = document.forms['frgrm']['cPucTipEj'+ j].value; // Hidden (Tipo de Ejecucion(L-Local,N-Niif,vacio-Ambas))
				            document.forms['frgrm']['cComVlr1'  + i].value = document.forms['frgrm']['cComVlr1' + j].value; // Hidden (Valor Uno)
				            document.forms['frgrm']['cComVlr2'  + i].value = document.forms['frgrm']['cComVlr2' + j].value; // Hidden (Valor Dos)
				            document.forms['frgrm']['cComFac'   + i].value = document.forms['frgrm']['cComFac'  + j].value; // Hidden (Comfac)
				            document.forms['frgrm']['cComComLi' + i].value = document.forms['frgrm']['cComComLi'+ j].value; // Hidden (comliq)
				            document.forms['frgrm']['cSucId'    + i].value = document.forms['frgrm']['cSucId'   + j].value; // Hidden (Sucursal)
				            document.forms['frgrm']['cDocId'    + i].value = document.forms['frgrm']['cDocId'   + j].value; // Hidden (Do)
				            document.forms['frgrm']['cDocSuf'   + i].value = document.forms['frgrm']['cDocSuf'  + j].value; // Hidden (Sufijo)
				            document.forms['frgrm']['cComEst'   + i].value = document.forms['frgrm']['cComEst'  + j].value; // Campo que indica si esta cambiando el Saldo (SI-> se cambia el saldo de por cobrar a por pagar, NO o Vacio -> es una cuenta por pagar normal)

				            document.forms['frgrm']['nComVlr'  + i].disabled = document.forms['frgrm']['nComVlr'  + j].disabled;
				            document.forms['frgrm']['nComVlrNF'+ i].disabled = document.forms['frgrm']['nComVlrNF'+ j].disabled;
				            document.forms['frgrm']['nComBRet' + i].disabled = document.forms['frgrm']['nComBRet' + j].disabled;
				            document.forms['frgrm']['nComBIva' + i].disabled = document.forms['frgrm']['nComBIva' + j].disabled;
				            document.forms['frgrm']['nComIva'  + i].disabled = document.forms['frgrm']['nComIva'  + j].disabled;
		             	}
		           	}
		           	cGrid.deleteRow(nLastRow - 1);
		           	document.forms['frgrm']['nSecuencia'].value = nLastRow - 1;

		           	f_Cuadre_Debitos_Creditos();
					  	}
						} else {
							alert("No se Pueden Eliminar Todas las Secuencias, Verifique.");
						}
					break;
					default: //No hace nada
					break;
				}
			}

			function f_Valores_Automaticos(xSecuencia) {
			  if (document.forms['frgrm']['cPucInv'+xSecuencia].value != "I") { // Pregunto si la cuenta es diferente a inventarios
				  var nSumValor = 0;
  				if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI") { // Para evitar el error en la sumatoria
  					if (document.forms['frgrm']['nComVlr01'].value == "") { // Por si el usuario no digito nada en valor
  						document.forms['frgrm']['nComVlr01'].value = 0;
  					}
  					nSumValor += parseFloat(document.forms['frgrm']['nComVlr01'].value);
  				}
  				if (document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") { // Para evitar el error en la sumatoria
  					if (document.forms['frgrm']['nComVlr02'].value == "") { // Por si el usuario no digito nada en valor
  						document.forms['frgrm']['nComVlr02'].value = 0;
  					}
  					nSumValor += parseFloat(document.forms['frgrm']['nComVlr02'].value);
  				}
  				// Sigo Sumando

  	  		//Las Retenciones y el IVA se calculan automaticamente si la ejecucion de la cuenta es LOCAL o AMBAS
  	  	  if  (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "L" || document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") {
	  				if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI" || document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") {
	  					if (document.forms['frgrm']['nPucRet'+xSecuencia].value > 0) { // Es una retencion
	  						document.forms['frgrm']['nComBIva'+xSecuencia].disabled = true; document.forms['frgrm']['nComBIva'+xSecuencia].value = "";
	  						document.forms['frgrm']['nComIva'+xSecuencia].disabled  = true; document.forms['frgrm']['nComIva'+xSecuencia].value  = "";
	  						document.forms['frgrm']['nComBRet'+xSecuencia].value = parseFloat(nSumValor);
	  						document.forms['frgrm']['nComBRet'+xSecuencia].disabled = false;
	  						f_Calcula_Retencion(xSecuencia);
	  					} else { // Es un IVA.
	  						document.forms['frgrm']['nComBRet'+xSecuencia].disabled = true; document.forms['frgrm']['nComBRet'+xSecuencia].value = "";
	  						document.forms['frgrm']['nComVlr'+xSecuencia].value = parseFloat(nSumValor);
	  						//Si es el tipo de ejecucion es AMBAS se asigna tambien el valor aL valor NIIF
	  						document.forms['frgrm']['nComVlrNF'+xSecuencia].value = (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") ? parseFloat(nSumValor) : "";
	  						// Nuevos valores para base de iva y valor del iva.
	  						document.forms['frgrm']['nComBIva'+xSecuencia].disabled = false;
	  						document.forms['frgrm']['nComIva'+xSecuencia].disabled  = false;
	  						f_Cacula_BaseIva_e_Iva("Base_mas_Iva",xSecuencia);
	  					}
	  				}
				  } else if (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "N") {
					  //Para la ejecucion NIIF no aplican retenciones, ni IVA
					  document.forms['frgrm']['nComBRet'+xSecuencia].disabled = true; document.forms['frgrm']['nComBRet'+xSecuencia].value = "";
					  document.forms['frgrm']['nComBIva'+xSecuencia].disabled = true; document.forms['frgrm']['nComBIva'+xSecuencia].value = "";
					  document.forms['frgrm']['nComIva' +xSecuencia].disabled = true; document.forms['frgrm']['nComIva' +xSecuencia].value = "";
					  document.forms['frgrm']['nComVlr'  +xSecuencia].value = "";
					  if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI" || document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") {
					  	document.forms['frgrm']['nComVlrNF'+xSecuencia].value = parseFloat(nSumValor);
					  	if (document.forms['frgrm']['nPucRet'+xSecuencia].value > 0) { // Es una retencion
	  						//No Hace Nada
	  					} else { // Es un IVA, se debe digitar base Iva, no se calcula Iva
	  						document.forms['frgrm']['nComBIva'+xSecuencia].disabled = false;
	  						document.forms['frgrm']['nComBIva'+xSecuencia].value    = document.forms['frgrm']['nComVlrNF'+xSecuencia].value;
	  					}
					  }
				  }
			  }
	    }

	    function f_Calcula_Retencion(xSecuencia) {
	    	if  (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "L" || document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") {
		    	document.forms['frgrm']['nComBIva'+xSecuencia].disabled = true;
					document.forms['frgrm']['nComIva'+xSecuencia].disabled  = true;
					var nRetencion = 0;
		    	if (document.forms['frgrm']['nPucRet'+xSecuencia].value > 0) {
		    		var nRound = (document.forms['frgrm']['nComBRet'+xSecuencia].value.indexOf(".") > 0) ? 2 : 0;

		    		nRetencion = parseFloat(document.forms['frgrm']['nPucRet'+xSecuencia].value/100);
		    		document.forms['frgrm']['nComVlr'  +xSecuencia].value = f_RoundValor((parseFloat(document.forms['frgrm']['nComBRet'+xSecuencia].value) * parseFloat(nRetencion)),nRound);
		    		//Si es el tipo de ejecucion es AMBAS se asigna tambien el valor aL valor NIIF
		    		document.forms['frgrm']['nComVlrNF'+xSecuencia].value = (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") ? document.forms['frgrm']['nComVlr'+xSecuencia].value : "";
		    	}
	    	}
	    }

	    function f_Valida_Base_Retencion(xSecuencia) {
	    	//Las Retenciones y el IVA se calculan si la ejecucion de la cuenta es LOCAL o AMBAS
			  if  (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "L" || document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") {
					if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI" || document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") {
						if (document.forms['frgrm']['nPucRet'+xSecuencia].value > 0) { // Es una retencion
							if (f_RoundValor(parseFloat(document.forms['frgrm']['nComBRet'+xSecuencia].value)) < f_RoundValor(parseFloat(document.forms['frgrm']['nPucBRet'+xSecuencia].value))) {
								alert("La Base de Retencion ["+f_RoundValor(parseFloat(document.forms['frgrm']['nComBRet'+xSecuencia].value))+"] es Menor a la Base de Retencion ["+f_RoundValor(parseFloat(document.forms['frgrm']['nPucBRet'+xSecuencia].value))+"] Parametrizada en la Cuenta PUC ["+document.forms['frgrm']['cPucId'+xSecuencia].value+"].");
		    			}
						} else { // Es un IVA.
							if (f_RoundValor(parseFloat(document.forms['frgrm']['nComBIva'+xSecuencia].value)) < f_RoundValor(parseFloat(document.forms['frgrm']['nPucBRet'+xSecuencia].value))) {
								alert("La Base de Retencion ["+f_RoundValor(parseFloat(document.forms['frgrm']['nComBIva'+xSecuencia].value))+"] es Menor a la Base de Retencion ["+f_RoundValor(parseFloat(document.forms['frgrm']['nPucBRet'+xSecuencia].value))+"] Parametrizada en la Cuenta PUC ["+document.forms['frgrm']['cPucId'+xSecuencia].value+"].");
		    			}
						}
					}
			  }
	    }

	    function f_Cacula_BaseIva_e_Iva(xTipo,xSecuencia) {
	    	if  (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "L" || document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") {
		    	document.forms['frgrm']['nComBRet'+xSecuencia].disabled = true;
		    	switch (xTipo) {
		    		case "BaseIva":
		    			var nRound = (document.forms['frgrm']['nComBIva'+xSecuencia].value.indexOf(".") > 0) ? 2 : 0;
							if ("<?php echo $vSysStr['system_financiero_calcular_iva_segun_concepto'] == 'SI' ?>") {
								if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI" && document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") {
									document.forms['frgrm']['nComIva'  +xSecuencia].value = f_RoundValor((parseFloat(document.forms['frgrm']['nComBIva'+xSecuencia].value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100))),nRound);
								}else{
									document.forms['frgrm']['nComIva'  +xSecuencia].value = 0;
								}
							}else{
								document.forms['frgrm']['nComIva'  +xSecuencia].value = f_RoundValor((parseFloat(document.forms['frgrm']['nComBIva'+xSecuencia].value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100))),nRound);
							}

							document.forms['frgrm']['nComVlr'  +xSecuencia].value = f_RoundValor((parseFloat(document.forms['frgrm']['nComBIva'+xSecuencia].value) + parseFloat(document.forms['frgrm']['nComIva'+xSecuencia].value)),nRound);
							//Si es el tipo de ejecucion es AMBAS se asigna tambien el valor aL valor NIIF
							document.forms['frgrm']['nComVlrNF'+xSecuencia].value = (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") ? document.forms['frgrm']['nComVlr'+xSecuencia].value : "";
		    		break;
		    		case "VlrIva":
		    			var nRound = ((document.forms['frgrm']['nComBIva'+xSecuencia].value.indexOf(".") > 0) || (document.forms['frgrm']['nComIva'+xSecuencia].value.indexOf(".") > 0)) ? 2 : 0;

		    			document.forms['frgrm']['nComVlr'  +xSecuencia].value = f_RoundValor((parseFloat(document.forms['frgrm']['nComBIva'+xSecuencia].value) + parseFloat(document.forms['frgrm']['nComIva'+xSecuencia].value)),nRound);
		    			//Si es el tipo de ejecucion es AMBAS se asigna tambien el valor aL valor NIIF
		    			document.forms['frgrm']['nComVlrNF'+xSecuencia].value = (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "") ? document.forms['frgrm']['nComVlr'+xSecuencia].value : "";
		    		break;
		    		case "Base_mas_Iva":
		    			var nRound = (document.forms['frgrm']['nComVlr01'].value.indexOf(".") > 0) ? 2 : 0;

		    			document.forms['frgrm']['nComBIva'+xSecuencia].value = document.forms['frgrm']['nComVlr01'].value;
							if ("<?php echo $vSysStr['system_financiero_calcular_iva_segun_concepto'] == 'SI' ?>") {
								if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI" && document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") {
									document.forms['frgrm']['nComIva'+xSecuencia].value  = f_RoundValor((parseFloat(document.forms['frgrm']['nComVlr01'].value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100))),nRound);
								}else{
									document.forms['frgrm']['nComIva'+xSecuencia].value  = 0;
								}
							}else{
								document.forms['frgrm']['nComIva'+xSecuencia].value  = f_RoundValor((parseFloat(document.forms['frgrm']['nComVlr01'].value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100))),nRound);
							}

		    		break;
		    	}
	    	} else if (document.forms['frgrm']['cPucTipEj'+xSecuencia].value == "N") {
	    		switch (xTipo) {
		    		case "BaseIva":
							document.forms['frgrm']['nComVlrNF'+xSecuencia].value = document.forms['frgrm']['nComBIva'+xSecuencia].value
		    		break;
		    		case "BaseNiif":
		    			if (document.forms['frgrm']['cComVlr1'+xSecuencia].value == "SI" || document.forms['frgrm']['cComVlr2'+xSecuencia].value == "SI") {
						  	if (document.forms['frgrm']['nPucRet'+xSecuencia].value > 0) { // Es una retencion
		  						//No Hace Nada
		  					} else { // Es un IVA, se debe digitar base Iva, no se calcula Iva
		  						document.forms['frgrm']['nComBIva'+xSecuencia].value    = document.forms['frgrm']['nComVlrNF'+xSecuencia].value;
		  					}
						  }
		    		break;
		    	}
	    	}
	    }

	    function f_Cuadre_Debitos_Creditos() {
	    	document.forms['frgrm']['nDebitos'].value    = 0;
	    	document.forms['frgrm']['nCreditos'].value   = 0;
	    	document.forms['frgrm']['nDiferencia'].value = 0;

	    	//Recorro la grilla para determinar el tipo de ejecucion del comprobante
				//Si hay tipos de ejecucion LOCAL o AMBAS deben sumarse para los Debitos y Creditos el nComVlr
				//Si solo hay tipos de ejecucion NIIF deben sumarse para los Debitos y Creditos el nComVlrNF
				var nCanEjeLoc = 0;
				for (i=0;i<document.forms['frgrm']['nSecuencia'].value;i++) {
					if (document.forms['frgrm']['cPucTipEj'+(i+1)].value == "L" || document.forms['frgrm']['cPucTipEj'+(i+1)].value == "") {
						nCanEjeLoc++;
					}
				}
				var cCamEje = (nCanEjeLoc > 0) ? "nComVlr" : "nComVlrNF";
				for (i=0;i<document.forms['frgrm']['nSecuencia'].value;i++) {
					if (document.forms['frgrm']['nComVlr'+(i+1)].value == "") { // Para evitar el error en la sumatoria
						document.forms['frgrm']['nComVlr'+(i+1)].value = 0;
					}
					if (document.forms['frgrm']['nComVlrNF'+(i+1)].value == "") { // Para evitar el error en la sumatoria
						document.forms['frgrm']['nComVlrNF'+(i+1)].value = 0;
					}
	    		switch(document.forms['frgrm']['cComMov'+(i+1)].value) {
	    			case "D":
	    				document.forms['frgrm']['nDebitos'].value  = f_RoundValor(parseFloat(document.forms['frgrm']['nDebitos'].value) + parseFloat(document.forms['frgrm'][cCamEje+(i+1)].value));
	    			break;
	    			case "C":
	    				document.forms['frgrm']['nCreditos'].value = f_RoundValor(parseFloat(document.forms['frgrm']['nCreditos'].value) + parseFloat(document.forms['frgrm'][cCamEje+(i+1)].value));
	    			break;
	    		}
	    	}
	    	document.forms['frgrm']['nDiferencia'].value = f_RoundValor(parseFloat(document.forms['frgrm']['nDebitos'].value) - parseFloat(document.forms['frgrm']['nCreditos'].value));
	    }

	    function f_Enabled_Combos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.forms['frgrm']['cComCsc2'].disabled = false;
				document.forms['frgrm']['cComCsc3'].disabled = false;
		  }

		  function f_Disabled_Combos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.forms['frgrm']['cComCsc2'].disabled = true;
				document.forms['frgrm']['cComCsc3'].disabled = true;
		  }

			function f_Add_New_Row_Comprobante() {
				var cGrid      = document.getElementById("Grid_Comprobante");
				var nLastRow   = cGrid.rows.length;
				var nSecuencia = nLastRow+1;
				var cTableRow  = cGrid.insertRow(nLastRow);

				var cComSeq   = 'cComSeq'   + nSecuencia; // Secuencia
			  var cCtoId    = 'cCtoId'    + nSecuencia; // Id del Concepto
			  var cCtoDes   = 'cCtoDes'   + nSecuencia; // Descripcion del Concepto
			  var cCtoAnt   = 'cCtoAnt'   + nSecuencia; // Hidden (Concepto de Anticipos?)
			  var cInvLin   = 'cInvLin'   + nSecuencia; // Hidden (Inventario - Linea)
			  var cInvGru   = 'cInvGru'   + nSecuencia; // Hidden (Inventario - Grupo)
			  var cInvPro   = 'cInvPro'   + nSecuencia; // Hidden (Inventario - Producto)
			  var nInvCan   = 'nInvCan'   + nSecuencia; // Hidden (Inventario - Cantidad)
			  var nInvCos   = 'nInvCos'   + nSecuencia; // Hidden (Inventario - Costo Unitario)
			  var cInvBod   = 'cInvBod'   + nSecuencia; // Hidden (Inventario - Bodega)
			  var cInvUbi   = 'cInvUbi'   + nSecuencia; // Hidden (Inventario - Ubicacion)
				var cComObs   = 'cComObs'   + nSecuencia; // Observacion del Comprobante
			  var cComIdC   = 'cComIdC'   + nSecuencia; // Id Comprobante Cruce
			  var cComCodC  = 'cComCodC'  + nSecuencia; // Codigo Comprobante Cruce
			  var cComCscC  = 'cComCscC'  + nSecuencia; // Consecutivo Comprobante Cruce
			  var cComSeqC  = 'cComSeqC'  + nSecuencia; // Secuencia Comprobante Cruce
			  var cCcoId    = 'cCcoId'    + nSecuencia; // Centro de Costos
			  var cSccId    = 'cSccId'    + nSecuencia; // Sub Centro de Costos
			  var cComCtoC  = 'cComCtoC'  + nSecuencia; // Concepto Comprobante Cruce
			  var cComIdCB  = 'cComIdCB'  + nSecuencia; // Id Comprobante Cruce Dos
			  var cComCodCB = 'cComCodCB' + nSecuencia; // Codigo Comprobante Cruce Dos
			  var cComCscCB = 'cComCscCB' + nSecuencia; // Consecutivo Comprobante Cruce Dos
			  var cComSeqCB = 'cComSeqCB' + nSecuencia; // Secuencia Comprobante Cruce Dos
			  var cComFecCB = 'cComFecCB' + nSecuencia; // Fecha de Creacion del Comprobante Cruce Dos
			  var cDocInf   = 'cDocInf'   + nSecuencia; // Documento Informativo
				var cDocFac   = 'cDocFac'   + nSecuencia; // Consecutivo Informativo de Factura
			  var cDocFacA  = 'cDocFacA'  + nSecuencia; // Consecutivo Informativo de Factura
			  var nComBRet  = 'nComBRet' 	+ nSecuencia; // Base de Retencion
			  var nComBIva  = 'nComBIva' 	+ nSecuencia; // Base de Iva
			  var nComIva   = 'nComIva'  	+ nSecuencia; // Valor del Iva
			  var nComVlr   = 'nComVlr'   + nSecuencia; // Valor del Comprobante
			  var nComVlrNF = 'nComVlrNF' + nSecuencia; // Valor NIIF del Comprobante
			  var cComMov   = 'cComMov'   + nSecuencia; // Movimiento Debito o Credito
			  var oBtnDel   = 'oBtnDel'   + nSecuencia; // Boton de Borrar Row
			  var cComNit   = 'cComNit'   + nSecuencia; // Hidden (Nit que va para SIIGO)
			  var cTerTip   = 'cTerTip'   + nSecuencia; // Hidden (Tipo de Tercero)
			  var cTerId    = 'cTerId'    + nSecuencia; // Hidden (Id del Tercero)
			  var cTerNom   = 'cTerNom'   + nSecuencia; // Nombre del Tercero
			  var cTerTipB  = 'cTerTipB'  + nSecuencia; // Hidden (Tipo de Tercero Dos)
			  var cTerIdB   = 'cTerIdB'   + nSecuencia; // Hidden (Id del Tercero Dos)
			  var cTerNomB  = 'cTerNomB'  + nSecuencia; // Nombre del Tercero B
			  var cPucId    = 'cPucId'    + nSecuencia; // Hidden (La Cuenta Contable)
			  var cPucDet   = 'cPucDet'   + nSecuencia; // Hidden (Detalle de la Cuenta)
			  var cPucTer   = 'cPucTer'   + nSecuencia; // Hidden (Cuenta de Terceros?)
			  var nPucBRet  = 'nPucBRet'  + nSecuencia; // Hidden (Base de Retencion de la Cuenta)
			  var nPucRet   = 'nPucRet'   + nSecuencia; // Hidden (Porcentaje de Retencion de la Cuenta)
			  var cPucNat   = 'cPucNat'   + nSecuencia; // Hidden (Naturaleza de la Cuenta)
			  var cPucInv   = 'cPucInv'   + nSecuencia; // Hidden (Cuenta de Inventarios?)
			  var cPucCco   = 'cPucCco'   + nSecuencia; // Hidden (Para esta Cuenta Aplica Centro de Costos?)
			  var cPucDoSc  = 'cPucDoSc'  + nSecuencia; // Hidden (Aplica DO para Subcentro de Costo?)
			  var cPucTipEj = 'cPucTipEj' + nSecuencia; // Hidden (Tipo de Ejecucion(L-Local,N-Niif,vacio-Ambas))
			  var cComVlr1  = 'cComVlr1'  + nSecuencia; // Hidden (Valor Uno)
			  var cComVlr2  = 'cComVlr2'  + nSecuencia; // Hidden (Valor Dos)
			  var cComFac   = 'cComFac'   + nSecuencia; // Hidden (Comfac)
			  var cComComLi = 'cComComLi' + nSecuencia; // Hidden (comliq)
			  var cSucId    = 'cSucId'    + nSecuencia; // Hidden (Sucursal)
			  var cDocId    = 'cDocId'    + nSecuencia; // Hidden (Do)
			  var cDocSuf   = 'cDocSuf'   + nSecuencia; // Hidden (Sufijo)
			  var cComEst   = 'cComEst'   + nSecuencia; // Campo que indica si esta cambiando el Saldo (SI-> se cambia el saldo de por cobrar a por pagar, NO o Vacio -> es una cuenta por pagar normal)

		    var TD_xAll = cTableRow.insertCell(0);

				TD_xAll.innerHTML = "<input type = 'text'   Class = 'letra' style = 'width:040;text-align:center' name = "+cComSeq+"  value = "+f_Str_Pad(nSecuencia,3,"0","STR_PAD_LEFT")+"  readonly>"+
				                    "<input type = 'text'   Class = 'letra' style = 'width:060;text-align:center' name = "+cCtoId+"  id = '' maxlength='10' "+
				                    	"onKeyUp = 'javascript:if(f_KeyUp(event)==13){"+
				                    		"this.value=this.value.toUpperCase();"+
				                    		"f_Links(\"cCtoId\",\"VALID\",\""+nSecuencia+"\");f_Cuadre_Debitos_Creditos();}'>"+
				                    "<input type = 'text'   Class = 'letra' style = 'width:060' name = "+cCtoDes+" readonly>"+
				                    "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cCtoAnt+" readonly>"+
				                    "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cInvLin+" readonly>"+
				                    "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cInvGru+" readonly>"+
				                    "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cInvPro+" readonly>"+
				                    "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+nInvCan+" readonly>"+
				                    "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+nInvCos+" readonly>"+
				                    "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cInvBod+" readonly>"+
				                    "<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cInvUbi+" readonly>"+
														"<input type = 'text'   Class = 'letra' style = 'width:020' name = "+cComObs+" maxlength='<?php echo ($vSysStr['financiero_longitud_observaciones_grilla'] > 255 ) ? 255 : (($vSysStr['financiero_longitud_observaciones_grilla'] < 1) ? 1 : $vSysStr['financiero_longitud_observaciones_grilla']);?>' "+
															"onBlur = 'javascript:this.value=this.value.toUpperCase();'>"+

														"<input type = 'hidden' Class = 'letra' style = 'width:0'   name = "+cTerTip+" readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0'   name = "+cTerId+"  readonly>"+
														"<input type = 'text'   Class = 'letra' style = 'width:060' name = "+cTerNom+" "+
															"onKeyUp = 'javascript:if(f_KeyUp(event)==13){"+
				                    	"this.value=this.value.toUpperCase();"+
				                    	"f_Links(\"cTerId\",\"VALID\",\""+nSecuencia+"\");}'>"+

														"<input type = 'hidden' Class = 'letra' style = 'width:0'   name = "+cTerTipB+" readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0'   name = "+cTerIdB+"  readonly>"+
														"<input type = 'text'   Class = 'letra' style = 'width:060' name = "+cTerNomB+" "+
															"onKeyUp = 'javascript:if(f_KeyUp(event)==13){"+
				                    	"this.value=this.value.toUpperCase();"+
				                    	"f_Links(\"cTerIdB\",\"VALID\",\""+nSecuencia+"\");}'>"+

														"<input type = 'text'   Class = 'letra' style = 'width:020;color:#FF0000;font-weight:bold' name = "+cComIdC+" readonly>"+
														"<input type = 'text'   Class = 'letra' style = 'width:030;color:#FF0000;font-weight:bold;text-align:center' name = "+cComCodC+" readonly>"+
				                    "<input type = 'text'   Class = 'letra' style = 'width:050;color:#FF0000;font-weight:bold;text-align:right'  name = "+cComCscC+" maxlength='10' "+
				                    	"onKeyUp = 'javascript:if(f_KeyUp(event)==13){"+
				                    	"this.value=this.value.toUpperCase();"+
				                    	"f_Links(\"cComCscC\",\"WINDOW\",\""+nSecuencia+"\",\"CRUCE_UNO\");f_Cuadre_Debitos_Creditos();}'>"+
				                    "<input type = 'text'   Class = 'letra' style = 'width:030;color:#FF0000;font-weight:bold;text-align:center' name = "+cComSeqC+" readonly>"+

				                    "<input type = 'text'   Class = 'letra' style = 'width:040;color:#FF0000;font-weight:bold;text-align:center' name = "+cCcoId+" maxlength='10' "+
				                    	"onKeyUp = 'javascript:if(f_KeyUp(event)==13){"+
				                    	"this.value=this.value.toUpperCase();"+
				                    	"f_Links(\"cCcoId\",\"VALID\",\""+nSecuencia+"\",\"GRID\");}'>"+
				                    "<input type = 'text'   Class = 'letra' style = 'width:040;color:#FF0000;font-weight:bold;text-align:center' name = "+cSccId+" maxlength='20' "+
				                    	"onKeyUp = 'javascript:if(f_KeyUp(event)==13){"+
				                    	"this.value=this.value.toUpperCase();"+
				                    	"f_Links(\"cSccId\",\"VALID\",\""+nSecuencia+"\",\"GRID\");}'>"+

				                    "<input type = 'hidden' Class = 'letra' style = 'width:020;color:#FF0000' name = "+cComCtoC+" readonly>"+
				                    "<input type = 'text'   Class = 'letra' style = 'width:020;color:#FF0000;font-weight:bold' name = "+cComIdCB+" readonly>"+
				                    "<input type = 'text'   Class = 'letra' style = 'width:030;color:#FF0000;font-weight:bold;text-align:center' name = "+cComCodCB+" readonly>"+
				                    "<input type = 'text'   Class = 'letra' style = 'width:050;color:#FF0000;font-weight:bold;text-align:right'  name = "+cComCscCB+" maxlength='10' "+
				                    	"onKeyUp = 'javascript:if(f_KeyUp(event)==13){"+
				                    	"this.value=this.value.toUpperCase();"+
				                    	"f_Links(\"cComCscC\",\"WINDOW\",\""+nSecuencia+"\",\"CRUCE_DOS\");}'>"+
				                    "<input type = 'text'   Class = 'letra' style = 'width:030;color:#FF0000;font-weight:bold;text-align:center' name = "+cComSeqCB+" readonly>"+
				                    "<input type = 'hidden' Class = 'letra' style = 'width:020;color:#FF0000' name = "+cComFecCB+" readonly>"+

														"<input type = 'text'   Class = 'letra' style = 'width:060;text-align:right' name = "+cDocInf+" maxlength='10'>"+
														"<input type = 'text'   Class = 'letra' style = 'width:060;text-align:right' name = "+cDocFac+" maxlength='25' "+
															"onKeyUp = 'javascript:if(f_KeyUp(event)==13){"+
																"this.value=this.value.toUpperCase();"+
																"f_Links(\"cDocFac\",\"VALID\",\""+nSecuencia+"\",\"GRID\");}'>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:020;color:#FF0000' name = "+cDocFacA+" readonly>"+

				                    "<input type = 'text'   Class = 'letra' style = 'width:60;text-align:right' name = "+nComBRet+" maxlength = '10' disabled "+
													  	"onKeyUp = 'javascript:this.value=f_ValDec(this.value);if (this.value.substr(-1) != \".\") { f_Calcula_Retencion(\""+nSecuencia+"\");f_Cuadre_Debitos_Creditos(); }' "+
															"onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value); f_Calcula_Retencion(\""+nSecuencia+"\");f_Cuadre_Debitos_Creditos();f_Valida_Base_Retencion(\""+nSecuencia+"\")'>"+
														"<input type = 'text'   Class = 'letra' style = 'width:60;text-align:right' name = "+nComBIva+" maxlength = '10' disabled "+
													  	"onKeyUp = 'javascript:this.value=f_ValDec(this.value);'"+
															"onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value); f_Cacula_BaseIva_e_Iva(\"BaseIva\",\""+nSecuencia+"\");f_Cuadre_Debitos_Creditos();f_Valida_Base_Retencion(\""+nSecuencia+"\")'>"+
														"<input type = 'text'   Class = 'letra' style = 'width:60;text-align:right' name = "+nComIva+" maxlength = '10' disabled "+
													  	"onKeyUp = 'javascript:this.value=f_ValDec(this.value);'"+
															"onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value); f_Cacula_BaseIva_e_Iva(\"VlrIva\",\""+nSecuencia+"\");f_Cuadre_Debitos_Creditos();f_Valida_Base_Retencion(\""+nSecuencia+"\")'>"+
														"<input type = 'text'   Class = 'letra' style = 'width:80;text-align:right' name = "+nComVlr+" maxlength = '10' "+
															"onKeyUp = 'javascript:this.value=f_ValDec(this.value);if (this.value.substr(-1) != \".\") { f_Cuadre_Debitos_Creditos(); } f_Enter(event,this.name);'"+
															"onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value); f_Cuadre_Debitos_Creditos();f_Valida_Base_Retencion(\""+nSecuencia+"\")'>"+
														"<input type = 'text'   Class = 'letra' style = 'width:80;text-align:right' name = "+nComVlrNF+" maxlength = '10' "+
															"onKeyUp = 'javascript:this.value=f_ValDec(this.value);if (this.value.substr(-1) != \".\") { f_Cacula_BaseIva_e_Iva(\"BaseNiif\",\""+nSecuencia+"\");f_Cuadre_Debitos_Creditos(); } f_Enter(event,this.name);'"+
															"onBlur = 'javascript:if (this.value.substr(-1) == \".\") { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value); f_Cuadre_Debitos_Creditos()'>"+
														"<input type = 'text'   Class = 'letra' style = 'width:020' name = "+cComMov+" maxlength='1' "+
															"onKeyUp = 'javascript:this.value=this.value.toUpperCase();"+
															"if(this.value != \"D\" && this.value != \"C\"){this.value=\"\";alert(\"El Movimiento Debe Ser Debito o Credito, Verifique\")};f_Cuadre_Debitos_Creditos()'>"+
														"<input type = 'button' Class = 'letra' style = 'width:020' id = "+oBtnDel+" value = 'X' "+
															"onClick = 'javascript:f_Delete_Row(this.value,\""+nSecuencia+"\",\"Grid_Comprobante\")'>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cComNit+"  readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cPucId+"   readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cPucDet+"  readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cPucTer+"  readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+nPucBRet+" readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+nPucRet+"  readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cPucNat+"  readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cPucInv+"  readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cPucCco+"  readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cPucDoSc+" readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cPucTipEj+" readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cComVlr1+" readonly>"+
														"<input type = 'hidden' Class = 'letra' style = 'width:0' name = "+cComVlr2+" readonly>"+
														"<input type = 'hidden' name = "+cComFac+"   value = ''  readonly>"+
														"<input type = 'hidden' name = "+cComComLi+" value = ''  readonly>"+
														"<input type = 'hidden' name = "+cSucId+"    value = ''  readonly>"+
														"<input type = 'hidden' name = "+cDocId+"    value = ''  readonly>"+
														"<input type = 'hidden' name = "+cDocSuf+"   value = ''  readonly>"+
														"<input type = 'hidden' name = "+cComEst+"   value = '' readonly>";

				document.forms['frgrm']['nSecuencia'].value = nSecuencia;
			}

		  function f_Activa_Csc() { // Activa el campo Factura dependiendo de la parametrizacion del comprobante
				switch (document.forms['frgrm']['cComTco'].value) {
	    		case "MANUAL":
	    			document.forms['frgrm']['cComCsc'].readOnly = false;
	    		break;
	    		default:
	    			document.forms['frgrm']['cComCsc'].readOnly = true;
	    		break;
	    	}
		  }
		</script>
	</head>
	<body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
		<!-- PRIMERO PINTO EL FORMULARIO -->
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="1140">
				<tr>
					<td>
						<form name = "frgrm" action = "frajugra.php" method = "post" target="fmpro">
							<fieldset>
			   				<legend>Nuevo <?php echo $_COOKIE['kProDes'] ?></legend>
			   				<input type = "hidden" name = "cCtoIdZ"     value = ""> <!-- Para filtar la consulta de conceptos en el fraju119 -->
								<input type = "hidden" name = "nRandom"     value = ""> <!-- Para filtar la consulta de conceptos en el fraju119 -->
								<input type = "hidden" name = "nSecuencia"  value = "0">
								<input type = "hidden" name = "cComTco"     value = ""> <!-- Tipo de Consecutivo para el comprobante (MANUAL/AUTOMATICO) -->
								<input type = "hidden" name = "cComCco"     value = ""> <!-- Control Consecutivo para el comprobante (MENSUAL/ANUAL/INDEFINIDO) -->
								<input type = "hidden" name = "nTimesSave"  value = "0">
								<input type = "hidden" name = "dComFec_Ant" value = "">
								<input type = "hidden" name = "cFileName"   value = "">
								<input type = "hidden" name = "cTabLiqDo"   value = "">
								<input type = "hidden" name = "cAjuAut"   	value = ""> <!-- Indica el comprobante de cancelacion automatico, se envia desde el proceso automatico, desde el proceso de ajustes se envia vacio (INTEGRACION COLMAS) -->
								<input type = "hidden" name = "cModo"       value = "<?php echo $_COOKIE['kModo'] ?>">
                <!---Campos Relacionados con el documento soporte autofactura-->
                <input type = "hidden" name = "cComIdDs"   value = "">
                <input type = "hidden" name = "cComCodDs"  value = "">
                <input type = "hidden" name = "cComCscDs"  value = "">
                <input type = "hidden" name = "cComCsc2Ds" value = "">
                <input type = "hidden" name = "dComFecDs"  value = "">
                <input type = "hidden" name = "cOrigen"    value = "">

								<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:1140">
									<?php $cCols = f_Format_Cols(57); echo $cCols; ?>
									<tr>
										<td Class = "name" colspan = "1">Id<br>
											<input type = "text" Class = "letra" style = "width:20" name = "cComId" value = "" readonly>
										</td>
										<td Class = "name" colspan = "2">
											<a href = "javascript:document.forms['frgrm']['cComId'].value='';
																						document.forms['frgrm']['cComCod'].value='';
																						document.forms['frgrm']['cComDes'].value='';
																						document.forms['frgrm']['cEisId'].value='';
																						f_Links('cComCod','VALID')" id="id_href_cComCod">Cod</a><br>
											<input type = "text" Class = "letra" style = "width:40;text-align:center" name = "cComCod" value = ""
												onfocus="javascript:this.value='';
																						document.forms['frgrm']['cComDes'].value='';
																						document.forms['frgrm']['cEisId'].value='';
																						this.style.background='#00FFFF'"
												onblur = "javascript:f_Links('cComCod','VALID');
																						 this.style.background='#FFFFFF';
																						 document.forms['frgrm']['cComDes'].focus();">
										</td>
										<td Class = "name" colspan = "21">Descripcion<br>
											<input type = "text" Class = "letra" style = "width:420" name = "cComDes" readonly>
										</td>

										<td Class = "name" colspan = "4">
											<a href = "javascript:document.forms['frgrm']['cCcoId'].value='';
																						document.forms['frgrm']['cSccId'].value='';
																						document.forms['frgrm']['cSccId_SucId'].value='';
																						document.forms['frgrm']['cSccId_DocId'].value='';
																						document.forms['frgrm']['cSccId_DocSuf'].value='';
																						f_Links('cCcoId','VALID');" id="id_href_cCcoId">Centro Costo</a><br>
											<input type = "text" Class = "letra" style = "width:80;text-align:center" name = "cCcoId" maxlength = "10"
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
										<?php if (($_COOKIE['kModo'] == "ANTERIOR" || $_POST['cPeriodo'] == 'ANTERIOR') && $vSysStr['financiero_permitir_digitar_fecha_periodo_anterior'] == 'NO' ) { ?>
											<td Class = "name" colspan = "4">Fecha<br>
												<select Class = "letrase" name = "dComFec" style = "width:80">
												</select>
											</td>
										<?php } else { ?>
											<td Class = "name" colspan = "4">
											  <a href='javascript:show_calendar("frgrm.dComFec")' id="id_href_dComFec">Fecha</a><br>
												<input type = "text" Class = "letra" style = "width:80;text-align:center"
												  name = "dComFec" value = "<?php echo date('Y-m-d') ?>" onBlur = "javascript:f_Date(this)">
											</td>
										<?php } ?>
										<td Class = "name" colspan = "3">Hora<br>
											<input type = "text" Class = "letra" style = "width:60;text-align:center"
										    name = "tRegHCre" value = "<?php echo date('H:i:s') ?>" readonly>
										</td>
										<td Class = "name" colspan = "6">Csc. Uno<br>
											<input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cComCsc" <?php echo (($vSysStr['financiero_permitir_caracteres_alfanumericos_consecutivo_manual']) == 'NO') ? "onblur = \"javascript:f_FixFloat(this);\"" : '' ?> maxlength = "<?php echo (($vSysStr['financiero_digitos_consecutivo_manual']+0) > 0) ? $vSysStr['financiero_digitos_consecutivo_manual'] : 10 ?>" readonly>
										</td>
										<td Class = "name" colspan = "6">
											<?php if (f_InList($cAlfa,"UPSXXXXX","DEUPSXXXXX","TEUPSXXXXX")) { ?>
												Csc. Dos<br>
												<input type = "hidden" name = "cComCsc2" readonly>
												<input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cComCsc3" readonly>
											<?php } else { ?>
												Csc. Tres<br>
												<input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cComCsc2" readonly>
												<input type = "hidden" name = "cComCsc3" readonly>
											<?php } ?>
										</td>
										<td Class = "name" colspan = "4">
											<a href='javascript:show_calendar("frgrm.dComVen")' id="id_href_dComVen">Vencimiento</a><br>
											<input type = "text" Class = "letra" style = "width:80;text-align:center"
												name = "dComVen" value = "<?php echo date('Y-m-d') ?>" onBlur = "javascript:f_Date(this)">
										</td>
									</tr>
                  <?php if (f_InList($cAlfa,"GRUMALCO","TEGRUMALCO","DEGRUMALCO")) { ?>
                    <tr>
                      <td Class = "name" colspan = "41">Tipo Documento<br>
                        <select Class = "letrase" style = "width:820" name = "cDsoId">
                          <option value = "">[SELECCIONE]</option>
                          <?php
                          $qTipOpe  = "SELECT ";
                          $qTipOpe .= "dsoidxxx, ";
                          $qTipOpe .= "dsodesxx ";
                          $qTipOpe .= "FROM $cAlfa.fpar0163 ";
                          $qTipOpe .= "WHERE ";
                          $qTipOpe .= "regestxx = \"ACTIVO\"";
                          $xTipOpe  = f_MySql("SELECT","",$qTipOpe,$xConexion01,"");
                          while($xRC = mysql_fetch_array($xTipOpe)){ ?>
                            <option value="<?php echo $xRC['dsoidxxx'] ?>"><?php echo "(".$xRC['dsoidxxx'].") ".mb_strtoupper($xRC['dsodesxx']) ?></option>
                          <?php } ?>
                        </select>
                        <script language="javascript">
                          document.forms['frgrm']['cDsoId'].value="<?php echo $_POST['cDsoId'] ?>";
                        </script>
                      </td>
                      <td Class = "name" colspan = "6">Prefijo Factura<br>
                        <input type = "text" Class = "letra" style = "width:120" name = "cDsoPre" value = "">
                      </td>
                      <td Class = "name" colspan = "6">No. Factura<br>
                        <input type = "text" Class = "letra" style = "width:120" name = "cDsoNumF" value = "">
                      </td>
                      <td Class = "name" colspan = "4">
                        <a href='javascript:show_calendar("frgrm.dDsoFec")' id="id_href_dComFec">Fecha Factura</a><br>
                        <input type = "text" Class = "letra" style = "width:80;text-align:center"
                          name = "dDsoFec" value = "" onBlur = "javascript:f_Date(this)">
                      </td>
                    </tr>
                  <?php } ?>
									<tr>
										<?php
										if ($cAlfa == "COLMASXX" || $cAlfa == "TECOLMASXX" || $cAlfa == "DECOLMASXX") {
											$qEscInt  = "SELECT * ";
											$qEscInt .= "FROM $cAlfa.fpar0146 ";
											$qEscInt .= "WHERE ";
											$qEscInt .= "eisesaju = \"SI\" AND ";											
											$qEscInt .= "regestxx = \"ACTIVO\" ";
											$qEscInt .= "ORDER BY eisescpc DESC, eisdesxx ";
											$xEscInt  = f_MySql("SELECT","",$qEscInt,$xConexion01,"");
											?>
											<td Class = "name" colspan = "22">Observaciones Generales<br>
												<input type = "text" Class = "letra" style = "width:440" name = "cComObs" maxlength="200" value = ""
										    	onBlur = "javascript:this.value=this.value.toUpperCase()">
											</td>
											<td Class = "name" colspan = "15">Escenario SAP<br>
												<select Class = "letrase" name = "cEisId" style = "width:300">
												  <option value = ''>[SELECCIONE]</option>
													<?php
													while ($xREI = mysql_fetch_array($xEscInt)) {
														echo "<option value = '{$xREI['eisidxxx']}'>{$xREI['eisdesxx']}</option>";
													} ?>
												</select>
											</td>
											<td Class = "name" colspan = "05"><br>
												<select Class = "letrase" name = "cEisTip" style = "width:100">
												  <option value = ''>NO APLICA</option>
												  <option value = 'PROVEEDOR'>PROVEEDOR</option>
												  <option value = 'CLIENTE'>CLIENTE</option>
												</select>
											</td><?php
										} elseif ($cAlfa == "DEINTERLO2" || $cAlfa == "TEINTERLO2" || $cAlfa == "INTERLO2") {  ?>
                      <td Class = "name" colspan = "34">Observaciones Generales<br>
                        <input type = "text" Class = "letra" style = "width:680" name = "cComObs" maxlength="200" value = ""
                          onBlur = "javascript:this.value=this.value.toUpperCase()">
                          <input type="hidden" name = "cEisId">
                          <input type="hidden" name = "cEisTip">
                      </td>
                      <td Class = "name" colspan = "4">Cte Bancario<br>
                        <select Class = "letrase" name = "cComTCB" style = "width:80">
                          <option value = ''></option>
                          <option value = 'CG'>CG</option>
                          <option value = 'CH'>CH</option>
                          <option value = 'ND'>ND</option>
                          <option value = 'NC'>NC</option>
                        </select>
                      </td>
                      <td Class = "name" colspan = "4"><br>
                        <?php
                        if($vSysStr['financiero_longitud_numero_comprobante_bancario'] == ""){
                          $cLongitudComNCB = 6;
                        }else if($vSysStr['financiero_longitud_numero_comprobante_bancario'] > 20){
                          $cLongitudComNCB = 20;
                        }else{
                          $cLongitudComNCB = $vSysStr['financiero_longitud_numero_comprobante_bancario'];
                        }
                        ?>
                        <input type = "text" Class = "letra" style = "width:80" name = "cComNCB" maxlength="<?php echo $cLongitudComNCB ?>"
                          onBlur = "javascript:this.value=this.value.toUpperCase()">
                      </td>
                    <?php } else { ?>
											<td Class = "name" colspan = "42">Observaciones Generales<br>
												<input type = "text" Class = "letra" style = "width:840" name = "cComObs" maxlength="200" value = ""
										    	onBlur = "javascript:this.value=this.value.toUpperCase()">
												<input type="hidden" name = "cEisId">
												<input type="hidden" name = "cEisTip">
											</td>
										<?php } ?>
										<td Class = "name" colspan = "5">Tasa Cambio<br>
											<input type = "text" Class = "letra" style = "width:100;text-align:right" name = "nTasaCambio" value="<?php echo f_Buscar_Tasa_Cambio(date('Y-m-d'),"USD"); ?>"
												onKeyUp = "javascript:this.value=f_ValDec(this.value);"
												onFocus = "javascript:this.style.background='#00FFFF';"
						    	      onBlur  = "javascript:if (this.value.substr(-1) == '.') { this.value = this.value.substring(0, this.value.length-1); } this.value=f_ValDec(this.value);this.style.background='#FFFFFF';">
  									</td>
										<td Class = "name" colspan = "5"
									    onmouseover="javascript:status='Valor Uno'"
									    onmouseout ="javascript:status=''">Base Total<br>
											<input type = "text" Class = "letra" style = "width:100;text-align:right" name = "nComVlr01" maxlength = "10"
									    	onfocus = "javascript:this.style.background='#00FFFF'"
									    	onblur  = "javascript:if (this.value.substr(-1) == '.') { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value); var nRound = (this.value.indexOf('.') > 0) ? 2 : 0; document.forms['frgrm']['nComVlr02'].value=f_RoundValor(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)),nRound);
																						 this.style.background='#FFFFFF'"
												onkeyup = "javascript:this.value=f_ValDec(this.value); if (this.value.substr(-1) != '.') { var nRound = (this.value.indexOf('.') > 0) ? 2 : 0; document.forms['frgrm']['nComVlr02'].value=f_RoundValor(parseFloat(this.value*(<?php echo $vSysStr['financiero_porcentaje_iva_compras'] ?>/100)),nRound); }">
										</td>
										<td Class = "name" colspan = "5"
											onmouseover="javascript:status='Valor Dos'"
									    onmouseout ="javascript:status=''">Iva<br>
											<input type = "text" Class = "letra" style = "width:100;text-align:right" name = "nComVlr02" maxlength = "10"
									    	onKeyUp = "javascript:this.value=f_ValDec(this.value);"
									    	onfocus="javascript:this.style.background='#00FFFF'"
									    	onblur = "javascript:if (this.value.substr(-1) == '.') { this.value = this.value.substring(0, this.value.length-1); } f_ValDec(this.value);
																						 this.style.background='#FFFFFF'">
										</td>
									</tr>

									<tr><td colspan = "57"><hr></td></tr>
 	          	    <tr>
										<td colspan = "2"  class = "name"><center>Sq</font></center></td>
										<td colspan = "3"  class = "name"><center><font color="#ff0000">Id</font></center></td>
										<td colspan = "3"  class = "name"><center>Cto.</center></td>
										<td colspan = "1"  class = "name"><center>Ob.</center></td>

										<td colspan = "3"  class = "name"><center><font color="#ff0000">Cliente</font></center></td>
										<td colspan = "3"  class = "name"><center><font color="#ff0000">Tercero</font></center></td>

							 			<td colspan = "7"  class = "name"><center><font color="#ff0000">Doc. Cruce Uno</font></center></td>
							 			<td colspan = "2"  class = "name"><center><font color="#ff0000">CC</font></center></td>
							 			<td colspan = "2"  class = "name"><center><font color="#ff0000">SC</font></center></td>

										<td colspan = "6.5"  class = "name"><center><font color="#ff0000">Doc. Cruce Dos</font></center></td>

										<td colspan = "3"  class = "name"><center>Doc. Inf</center></td>
										<td colspan = "3"  class = "name"><center><font color="#ff0000">Fac. Inf</font></center></td>
										<td colspan = "3"  class = "name"><center>Base Ret.</center></td>
										<td colspan = "3"  class = "name"><center>Base Iva</center></td>
										<td colspan = "3"  class = "name"><center>Vlr. Iva</center></td>
										<td colspan = "4"  class = "name"><center>Valor Local</center></td>
										<td colspan = "4"  class = "name"><center>Valor NIIF</center></td>
										<td colspan = "1"  class = "name"><center>M</center></td>
										<td colspan = "1"  class = "name" align = "right"></td>
									</tr>
								</table>

								<center>
									<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:1140" id = "Grid_Comprobante">
									</table>
								</center>

								<center>
									<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:1140">
										<?php $cCols = f_Format_Cols(54); echo $cCols; ?>
									  <tr>
									  	<td Class = "name" colspan = "5">Estado<br>
										  	<input type = "text" Class = "letra" style = "width:100;text-align:center"
									    	  name = "cRegEst" value = "ACTIVO" readonly>
										  </td>
											<td Class = "name" colspan = "5">Modificado<br>
												<input type = "text" Class = "letra" style = "width:100;text-align:center"
									    	  name = "dRegFMod" value = "<?php echo date('Y-m-d') ?>" readonly>
								  		</td>
									  	<td colspan="37"></td>
										  <td Class = "name" colspan = "5">Debitos<br>
										  	<input type = "text" Class = "letra" style = "width:100;text-align:right"
									    	  name = "nDebitos" value = "0" readonly>
										  </td>
											<td Class = "name" colspan = "5">Creditos<br>
												<input type = "text" Class = "letra" style = "width:100;text-align:right"
									    	  name = "nCreditos" value = "0" readonly>
											</td>
										</tr>
										<tr>
										 	<td colspan="47"></td>
										 	<td Class = "name" colspan = "5">Diferencia D-C</td>
											<td Class = "name" colspan = "5">
												<input type = "text" Class = "letra" style = "width:100;text-align:right;color:#FF0000;font-weight:bold"
									    	  name = "nDiferencia" value = "0" readonly>
											</td>
								  	</tr>
								 	</table>
							  </center>
							</fieldset>
							<center>
								<table border="0" cellpadding="0" cellspacing="0" width="1140">
									<tr height="21">
										<?php switch ($_COOKIE['kModo']) {
											case "VER": ?>
												<td width="1049" height="21"></td>
										  	<td width="91" height="21" Class="name">
										  		<input type="button" name="Btn_Salir" value="Salir" style = "width:95;height:21;" onClick = "javascript:f_Retorna()">
										  	</td>
								  		<?php break;
											default: ?>
												<td width="958" height="21"></td>
												<td width="91" height="21" Class="name" >
													<input type="button" name="Btn_Guardar" value="Guardar" style = "width:95;height:21;"
														onclick = "javascript:f_Enabled_Combos();
											                            document.forms['frgrm']['Btn_Guardar'].disabled=true;
																									document.forms['frgrm']['nTimesSave'].value++;
																									document.forms['frgrm'].submit();
																									f_Disabled_Combos();"></td>
										  	<td width="91" height="21" Class="name" >
										  		<input type="button" name="Btn_Salir" value="Salir" style = "width:95;height:21;"
														onClick = "javascript:f_Retorna()">
										  	</td>
								  		<?php break;
								  	} ?>
									</tr>
								</table>
							</center>
						</form>
					</td>
				</tr>
			</table>
		</center>

		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<?php switch ($_COOKIE['kModo']) {
			case "NUEVO": ?>
				<script languaje = "javascript">
					f_Add_New_Row_Comprobante();
					f_Links('cComCod','VALID');
				</script>
			<?php break;
			case "ANTERIOR": ?>
				<script languaje = "javascript">
					f_Add_New_Row_Comprobante();
					f_Links('cComCod','VALID');
				</script>
			<?php break;
			case "BORRAR":
				f_Carga_Data($gComId,$gComCod,$gComCsc,$gComCsc2,$gComFec); ?>

				<script languaje = "javascript">
					document.forms['frgrm']['dComFec_Ant'].value = document.forms['frgrm']['dComFec'].value;

					//Si es Manual se habilita el Consecutivo
					if (document.forms['frgrm']['cComTco'].value == "MANUAL") {
		      	document.forms['frgrm']['cComCsc'].readOnly = false;
		     	} else {
		      	document.forms['frgrm']['cComCsc'].readOnly = true;
			     	document.forms['frgrm']['cComCsc'].onfocus  = "";
			     	document.forms['frgrm']['cComCsc'].onblur   = "";
		     	}

					document.forms['frgrm'].target="fmpro";
					document.forms['frgrm'].action="frajudel.php";
					document.forms['frgrm'].submit();
					document.forms['frgrm'].action="frajugra.php";
				</script>
			<?php break;
			case "VER":
				f_Carga_Data($gComId,$gComCod,$gComCsc,$gComCsc2,$gComFec); ?>
				<script languaje = "javascript">
					for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
          }
          document.forms['frgrm']['Btn_Salir'].disabled = false;
          document.getElementById('id_href_cComCod').href  = "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
          document.getElementById('id_href_cCcoId').href   = "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
          document.getElementById('id_href_cSccId').href   = "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
          document.getElementById('id_href_dComFec').href = "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
          document.getElementById('id_href_dComVen').href  = "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
				</script>
			<?php break;
			case "SUBIR":
				if($vSysStr['financiero_obligar_subcentro_de_costo'] == "SI") {
					if ($_POST['cSccId'] == "") {
						$nSwitch = 1;
						$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMensaje .= "El Sub Centro de Costo no puede ser Vacio.\n";
					}
				}

        /**
         * Validando extension permitida del archivo
         */
        if($_FILES['cArcPla']['name'] != ""){
          $vExtPer = ["application/vnd.ms-excel"];
          $finfo = finfo_open(FILEINFO_MIME_TYPE);
          $mime = finfo_file($finfo, $_FILES['cArcPla']['tmp_name']);
					echo "<pre>";
          print_r($mime);
          echo "<pre>";
          print_r($_FILES['cArcPla']['tmp_name']);
          die();
          if (!in_array($mime, $vExtPer)) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
            $cMsj .= "Archivo No Permitido.\n";
          }
          finfo_close($finfo);
        }

				## Validando que haya seleccionado un archivo
				if ($_FILES['cArcPla']['name'] == "") {
					$nSwitch = 1;
					$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMensaje .= "Debe Seleccionar un Archivo.\n";
				} else {
					#Copiando el archivo a la carpeta de downloads
					//$cNomFile = "/carbcoaut_".$kUser."_".date("YmdHis").".txt";
					$cNomFile = "/ajucoaut_".$kUser."_".date("YmdHis").".txt";
					switch (PHP_OS) {
						case "Linux" :
							$cFile = OC_DOCUMENTROOT."/opencomex/".$vSysStr['system_download_directory'].$cNomFile;
							break;
						case "WINNT":
							$cFile = OC_DOCUMENTROOT."/opencomex/".$vSysStr['system_download_directory'].$cNomFile;
							break;
					}

					if(!copy($_FILES['cArcPla']['tmp_name'],$cFile)){
						$nSwitch = 1;
						$cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMensaje .= "Error al Copiar Archivo.\n";
					}
				}

				#Creando tabla temporal
				if ($nSwitch == 0) {
					if ( $_POST['cPeriodo'] == 'ANTERIOR' ) {
				 ?>
				 		<script languaje = "javascript">
							document.cookie="kModo=ANTERIOR;path="+"/";
						</script>
					<?php
					} else {
					?>
						<script languaje = "javascript">
							document.cookie="kModo=NUEVO;path="+"/";
						</script>
				<?php
					} ?>
					<script languaje = "javascript">
						document.forms['frgrm']['cCcoId'].value      = "<?php echo $_POST['cCcoId'] ?>";
				 		document.forms['frgrm']['cSccId'].value      = "<?php echo $_POST['cSccId'] ?>";
				 		document.forms['frgrm']['cFileName'].value   = "<?php echo $cFile ?>";

						document.forms['frgrm'].target="fmpro";
						document.forms['frgrm'].action="fracaacg.php";
						document.forms['frgrm'].submit();
						document.forms['frgrm'].action="frajugra.php";
					</script>
				<?php
				}
			break;
      case "LIQUIDARDO":

        if($vSysStr['financiero_obligar_subcentro_de_costo'] == "SI") {
          if ($_POST['cSccId'] == "") {
            $nSwitch = 1;
            $cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMensaje .= "El Sub Centro de Costo no puede ser Vacio.\n";
          }
        }

        if ($_POST['cTabLiqDo'] == "") {
          $nSwitch = 1;
          $cMensaje .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMensaje .= "Error al Cargar los Datos, Tabla Temporal No Puede Ser Vacia.\n";
        }

        #Creando tabla temporal
        if ($nSwitch == 0) {
          if ( $_POST['cPeriodo'] == 'ANTERIOR' ) {
         ?>
            <script languaje = "javascript">
              document.cookie="kModo=ANTERIOR;path="+"/";
            </script>
          <?php
          } else {
          ?>
            <script languaje = "javascript">
              document.cookie="kModo=NUEVO;path="+"/";
            </script>
        <?php
          } ?>
          <script languaje = "javascript">
            document.forms['frgrm']['cCcoId'].value    = "<?php echo $_POST['cCcoId'] ?>";
            document.forms['frgrm']['cSccId'].value    = "<?php echo $_POST['cSccId'] ?>";
            document.forms['frgrm']['cTabLiqDo'].value = "<?php echo $_POST['cTabLiqDo'] ?>";

            document.forms['frgrm'].target="fmpro";
            document.forms['frgrm'].action="fracaacg.php";
            document.forms['frgrm'].submit();
            document.forms['frgrm'].action="frajugra.php";
          </script>
        <?php
        }
      break;
      case "CONTABILIZAR":
        //Opcion utilizada desde Documentos Soporte Autofactura
        //Para contabilizar un documento soporte como ajuste contable
        ?>
        <script languaje = "javascript">
          document.cookie="kModo=NUEVO;path="+"/";
        </script>
        <script languaje = "javascript">
          document.forms['frgrm']['cComIdDs'].value   = "<?php echo $gComIdDs ?>";
          document.forms['frgrm']['cComCodDs'].value  = "<?php echo $gComCodDs ?>";
          document.forms['frgrm']['cComCscDs'].value  = "<?php echo $gComCscDs ?>";
          document.forms['frgrm']['cComCsc2Ds'].value = "<?php echo $gComCsc2Ds ?>";
          document.forms['frgrm']['dComFecDs'].value  = "<?php echo $gComFecDs ?>";
          document.forms['frgrm']['cOrigen'].value    = "CONTABILIZAR";

          document.forms['frgrm'].target="fmpro";
          document.forms['frgrm'].action="fracdsag.php";
          document.forms['frgrm'].submit();
          document.forms['frgrm'].action="frajugra.php";
        </script>
        <?php
      break;
		} ?>

		<?php function f_Carga_Data($xComId,$xComCod,$xComCsc,$xComCsc2,$xComFec) {
		  global $xConexion01; global $cAlfa;

		  $xAno = substr($xComFec,0,4);

		  /**
		   * Trayendo los comprobantes de Causaciones Proveedores Empresa
		   * En estos no se completa dato en el cliente
		   */
		  $vComCpe = array();
		  $qCpe  = "SELECT ";
		  $qCpe .= "CONCAT(comidxxx,\"-\",comcodxx) AS comidxxx ";
		  $qCpe .= "FROM $cAlfa.fpar0117 ";
		  $qCpe .= "WHERE ";
		  $qCpe .= "comidxxx = \"P\" AND ";
		  $qCpe .= "comtipxx = \"CPE\" ";
		  $xCpe = f_MySql("SELECT","",$qCpe,$xConexion01,"");
		  $cCpe = "";
		  while ($xRDB = mysql_fetch_array($xCpe)) {
		  	$vComCpe[count($vComCpe)] = $xRDB['comidxxx'];
		  }

		  // Traigo los datos de la cabecera.
			$qConCab  = "SELECT * ";
			$qConCab .= "FROM $cAlfa.fcoc$xAno ";
			$qConCab .= "WHERE ";
			$qConCab .= "comidxxx = \"$xComId\"  AND ";
			$qConCab .= "comcodxx = \"$xComCod\" AND ";
			$qConCab .= "comcscxx = \"$xComCsc\" AND ";
			$qConCab .= "comcsc2x = \"$xComCsc2\" LIMIT 0,1";
			$xConCab  = f_MySql("SELECT","",$qConCab,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qConCab." ~ ".mysql_num_rows($xConCab));
			$vConCab  = mysql_fetch_array($xConCab);

			// Traigo los datos del detalle.
			$qConDet  = "SELECT * ";
			$qConDet .= "FROM $cAlfa.fcod$xAno ";
			$qConDet .= "WHERE ";
			$qConDet .= "comidxxx = \"$xComId\"  AND ";
			$qConDet .= "comcodxx = \"$xComCod\" AND ";
			$qConDet .= "comcscxx = \"$xComCsc\" AND ";
			$qConDet .= "comcsc2x = \"$xComCsc2\" ORDER BY ABS(comseqxx)";
	  	$xConDet = f_MySql("SELECT","",$qConDet,$xConexion01,"");
	  	//f_Mensaje(__FILE__,__LINE__,$qConDet." ~ ".mysql_num_rows($xConDet));

			// Busco la descripcion del comprobante.
			$qComDes  = "SELECT comdesxx,comtcoxx ";
		  $qComDes .= "FROM $cAlfa.fpar0117 ";
		  $qComDes .= "WHERE ";
		  $qComDes .= "comidxxx = \"$xComId\"  AND ";
		  $qComDes .= "comcodxx = \"$xComCod\" LIMIT 0,1";
			$xComDes  = f_MySql("SELECT","",$qComDes,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qComDes." ~ ".mysql_num_rows($xComDes));
			$vComDes  = mysql_fetch_array($xComDes);
			$vConCab['comdesxx'] = ($vComDes['comdesxx'] == "") ? "COMPROBANTE SIN DESCRIPCION" : $vComDes['comdesxx'];
			$vConCab['comtcoxx'] = $vComDes['comtcoxx'];

			// Busco la descripcion del centro de costos.
			$qCcoDes  = "SELECT ccodesxx ";
		  $qCcoDes .= "FROM $cAlfa.fpar0116 ";
		  $qCcoDes .= "WHERE ";
		  $qCcoDes .= "ccoidxxx = \"{$vConCab['ccoidxxx']}\" LIMIT 0,1";
			$xCcoDes  = f_MySql("SELECT","",$qCcoDes,$xConexion01,"");
			$vCcoDes  = mysql_fetch_array($xCcoDes);
			$vConCab['ccodesxx'] = ($vCcoDes['ccodesxx'] == "") ? "CENTRO DE COSTO SIN DESCRIPCION" : $vCcoDes['ccodesxx'];

			/*** Escenario de integracion para COLMAS ***/
			if ($cAlfa == "COLMASXX" || $cAlfa == "TECOLMASXX" || $cAlfa == "DECOLMASXX") {
				$vEscInt = explode( "~",$vConCab['comobs2x'] );
				$cEscInt = $vEscInt[0];
				$cEisTip = $vEscInt[2];
			}
			?>
			<script language = "javascript">
				document.forms['frgrm']['cComTco'].value     = "<?php echo $vConCab['comtcoxx'] ?>";
				document.forms['frgrm']['cComId'].value      = "<?php echo $vConCab['comidxxx'] ?>";
				document.forms['frgrm']['cComCod'].value     = "<?php echo $vConCab['comcodxx'] ?>";
				document.forms['frgrm']['cComDes'].value     = "<?php echo $vConCab['comdesxx'] ?>";
			 	document.forms['frgrm']['cCcoId'].value      = "<?php echo $vConCab['ccoidxxx'] ?>";
			 	document.forms['frgrm']['cSccId'].value      = "<?php echo $vConCab['sccidxxx'] ?>";
			 	document.forms['frgrm']['dComFec'].value     = "<?php echo $vConCab['comfecxx'] ?>";
			 	document.forms['frgrm']['cComCsc'].value     = "<?php echo $vConCab['comcscxx'] ?>";
			 	document.forms['frgrm']['cComCsc2'].value    = "<?php echo $vConCab['comcsc2x'] ?>";
			 	document.forms['frgrm']['cComCsc3'].value    = "<?php echo $vConCab['comcsc3x'] ?>";
			 	document.forms['frgrm']['dComVen'].value     = "<?php echo $vConCab['comfecve'] ?>";
			 	document.forms['frgrm']['cComObs'].value     = "<?php echo $vConCab['comobsxx'] ?>";
         if ("<?php echo $cAlfa ?>" == "DEINTERLO2" || "<?php echo $cAlfa ?>" == "TEINTERLO2" || "<?php echo $cAlfa ?>" == "INTERLO2") {
          document.forms['frgrm']['cComTCB'].value     = "<?php echo $vConCab['comtcbxx'] ?>";
          document.forms['frgrm']['cComNCB'].value     = "<?php echo $vConCab['comncbxx'] ?>";
         }
				document.forms['frgrm']['cEisId'].value      = "<?php echo $cEscInt ?>";
				document.forms['frgrm']['cEisTip'].value     = "<?php echo $cEisTip ?>";
				document.forms['frgrm']['nTasaCambio'].value = "<?php echo $vConCab['tcatasax']+0 ?>";
			 	document.forms['frgrm']['nComVlr01'].value   = "<?php echo $vConCab['comvlr01']+0 ?>";
			 	document.forms['frgrm']['nComVlr02'].value   = "<?php echo $vConCab['comvlr02']+0 ?>";
			 	document.forms['frgrm']['cRegEst'].value     = "<?php echo $vConCab['regestxx'] ?>";
			 	document.forms['frgrm']['dRegFMod'].value    = "<?php echo $vConCab['regfmodx'] ?>";

        if ("<?php echo $cAlfa ?>" == "GRUMALCO" || "<?php echo $cAlfa ?>" == "TEGRUMALCO" || "<?php echo $cAlfa ?>" == "DEGRUMALCO") {
          document.forms['frgrm']['cDsoId'].value   = "<?php echo $vConCab['dsoidxxx'] ?>";
          document.forms['frgrm']['cDsoPre'].value  = "<?php echo $vConCab['dsoprexx'] ?>";
          document.forms['frgrm']['cDsoNumF'].value = "<?php echo $vConCab['dsonumfa'] ?>";
          document.forms['frgrm']['dDsoFec'].value  = "<?php echo $vConCab['dsofecxx'] ?>";
        }
			</script>

			<?php // Empienzo a Pintar Grilla
			if (mysql_num_rows($xConDet) > 0) { // Pregunto si hay registros en detalle GRM01002 para pintar
				while ($xRCD = mysql_fetch_array($xConDet)) {
					// Busco la descripcion del concepto
				  $qCtoCon  = "SELECT $cAlfa.fpar0119.*,$cAlfa.fpar0115.* ";
		  		$qCtoCon .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
		  		$qCtoCon .= "WHERE ";
		  		$qCtoCon .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
		  		$qCtoCon .= "$cAlfa.fpar0119.ctoidxxx = \"{$xRCD['ctoidxxx']}\" AND ";
		  		$qCtoCon .= "$cAlfa.fpar0119.pucidxxx = \"{$xRCD['pucidxxx']}\" LIMIT 0,1";
					$xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
					if (mysql_num_rows($xCtoCon) > 0) {
						$vCtoCon = mysql_fetch_array($xCtoCon);
					} else {
					  //Busco en la parametrica de Conceptos Contables Causaciones Automaticas
					  $qCtoCon  = "SELECT $cAlfa.fpar0121.*,$cAlfa.fpar0115.* ";
	          $qCtoCon .= "FROM $cAlfa.fpar0121,$cAlfa.fpar0115 ";
	          $qCtoCon .= "WHERE ";
	          $qCtoCon .= "$cAlfa.fpar0121.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
	          $qCtoCon .= "$cAlfa.fpar0121.ctoidxxx = \"{$xRCD['ctoidxxx']}\" AND ";
	          $qCtoCon .= "$cAlfa.fpar0121.pucidxxx = \"{$xRCD['pucidxxx']}\" LIMIT 0,1";
	          $xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
	          //f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
	          if (mysql_num_rows($xCtoCon) > 0) {
	            $vCtoCon = mysql_fetch_array($xCtoCon);
	          } else {
	            //Busco por la cuenta, si es una cuenta de ingresos busco la descripcion del concepto de cobro
	            if (substr($xRCD['pucidxxx'],0,1) == "4") {
		            $qCtoCon  = "SELECT $cAlfa.fpar0129.*,$cAlfa.fpar0115.* ";
		            $qCtoCon .= "FROM $cAlfa.fpar0129,$cAlfa.fpar0115 ";
		            $qCtoCon .= "WHERE ";
		            $qCtoCon .= "$cAlfa.fpar0129.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
		            $qCtoCon .= "$cAlfa.fpar0129.ctoidxxx = \"{$xRCD['ctoidxxx']}\" AND ";
		            $qCtoCon .= "$cAlfa.fpar0129.pucidxxx = \"{$xRCD['pucidxxx']}\" LIMIT 0,1";
		            $xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
		            //f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
		            if (mysql_num_rows($xCtoCon) > 0) {
		              $vCtoCon = mysql_fetch_array($xCtoCon);
		              $vCtoCon['ctodesx'.strtolower($xRCD['comctoc2'])] = $vCtoCon['serdespx'];
		              $vCtoCon['ctonitxx'] = "CLIENTE";
		            }
	            }
            }
					}

					$vCtoCon['ctodesxx'] = ($vCtoCon['ctodesx'.strtolower($xRCD['comctoc2'])] <> "") ? $vCtoCon['ctodesx'.strtolower($xRCD['comctoc2'])] : $vCtoCon['ctodesxx'];
					$vCtoCon['ctodesxx'] = ($vCtoCon['ctodesxx'] <> "") ? $vCtoCon['ctodesxx'] : "CONCEPTO SIN DESCRIPCION";

					// Busco el nombre del tercero cliente.
					$qCliNom  = "SELECT ";
					$qCliNom .= "$cAlfa.SIAI0150.*, ";
					$qCliNom .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS TERNOMXX ";
					$qCliNom .= "FROM $cAlfa.SIAI0150 ";
					$qCliNom .= "WHERE ";
					$qCliNom .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$xRCD['teridxxx']}\" LIMIT 0,1";
					$xCliNom  = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
					$vCliNom  = mysql_fetch_array($xCliNom);
					//f_Mensaje(__FILE__,__LINE__,$qCliNom." ~ ".mysql_num_rows($xCliNom));
					$vConCab['ternomxx'] = "";
					$vConCab['ternomxx'] = ($vCliNom['TERNOMXX'] == "") ? "CLIENTE SIN NOMBRE" : trim($vCliNom['TERNOMXX']);
					$cTerTip = "CLICLIXX"; $cTerId = $xRCD['teridxxx']; $cTerNom = $vConCab['ternomxx'];

					// Busco el nombre del tercero proveedor.
					$qProNom  = "SELECT ";
					$qProNom .= "$cAlfa.SIAI0150.*, ";
					$qProNom .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS TERNOMXX ";
					$qProNom .= "FROM $cAlfa.SIAI0150 ";
					$qProNom .= "WHERE ";
					$qProNom .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$xRCD['terid2xx']}\" LIMIT 0,1";
					$xProNom  = f_MySql("SELECT","",$qProNom,$xConexion01,"");
					$vProNom  = mysql_fetch_array($xProNom);
					$vConCab['ternom2x'] = ($vProNom['TERNOMXX'] == "") ? "PROVEEDOR SIN NOMBRE" : trim($vProNom['TERNOMXX']);
					$cTerTipB = "CLIPROCX"; $cTerIdB = $xRCD['terid2xx']; $cTerNomB = $vConCab['ternom2x'];

					/**
					 * Si el documento cruece es una causacion propia empresa el cliente es el mismo proveedor
					 */
					if (in_array("{$xRCD['comidcxx']}-{$xRCD['comcodcx']}", $vComCpe) == true && $vCtoCon['pucdetxx'] != "D") {
						$xRCD['tertipxx']    = "";
						$xRCD['teridxxx']    = "";
						$vConCab['ternomxx'] = "";

            //Actualizacion 2016-04-12 Johana Arboleda Ramos
            //En los ajuste contables, cuando se realiza un ajuste a un concepto que esta marcado como de Anticipo
            //se debe guardar en el movimiento contable en el Cliente (terixxx) quien recibe el pago
            //(el tercero seleccionado en la grilla) y en el Tercero (terid2xxx) el dueño del DO
            //(el cliente seleccionado en la grilla), entonces se deben cargar cambiados
					}

          if ($vCtoCon['ctoantxx'] == "SI" && $xRCD['comidc2x'] == "R") {
            $cTerTipAux       = $xRCD['tertipxx']; $cTerIdAux        = $xRCD['teridxxx']; $cTerNomAux          = $vConCab['ternomxx'];
            $xRCD['tertipxx'] = $xRCD['tertip2x']; $xRCD['teridxxx'] = $xRCD['terid2xx']; $vConCab['ternomxx'] = $vConCab['ternom2x'];
            $xRCD['tertip2x'] = $cTerTipAux;       $xRCD['terid2xx'] = $cTerIdAux;        $vConCab['ternom2x'] = $cTerNomAux;
          }
         ?>
				 	<script languaje = "javascript">
						f_Add_New_Row_Comprobante();
						document.forms['frgrm']['cComSeq'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comseqxx'] ?>";
						document.forms['frgrm']['cCtoId'   +document.forms['frgrm']['nSecuencia'].value].id    = "<?php echo $xRCD['ctoidxxx'] ?>";
						document.forms['frgrm']['cCtoId'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['ctoidxxx'] ?>";
						document.forms['frgrm']['cCtoDes'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['ctodesxx'] ?>";
						document.forms['frgrm']['cCtoAnt'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['ctoantxx'] ?>";
						if ("<?php echo $vCtoCon['pucinvxx'] ?>" == "I") {
							document.forms['frgrm']['cInvLin'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo substr($xRCD['proidxxx'],0,3) ?>";
							document.forms['frgrm']['cInvGru'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo substr($xRCD['proidxxx'],3,4) ?>";
							document.forms['frgrm']['cInvPro'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo substr($xRCD['proidxxx'],7,6) ?>";
							document.forms['frgrm']['nInvCan'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comcanxx'] ?>";
							document.forms['frgrm']['nInvCos'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo ($xRCD['comvlrxx']/$xRCD['comcanxx']) ?>";
							document.forms['frgrm']['cInvBod'+document.forms['frgrm']['nSecuencia'].value].value = "";
							document.forms['frgrm']['cInvUbi'+document.forms['frgrm']['nSecuencia'].value].value = "";
						}
						document.forms['frgrm']['cComObs'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comobsxx'] ?>";
						document.forms['frgrm']['cComIdC'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comidcxx'] ?>";
						document.forms['frgrm']['cComCodC' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comcodcx'] ?>";
						document.forms['frgrm']['cComCscC' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comcsccx'] ?>";
						document.forms['frgrm']['cComSeqC' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comseqcx'] ?>";
						document.forms['frgrm']['cCcoId'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['ccoidxxx'] ?>";
						document.forms['frgrm']['cSccId'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['sccidxxx'] ?>";
						document.forms['frgrm']['cComCtoC' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comctoc2'] ?>";
						document.forms['frgrm']['cComIdCB' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comidc2x'] ?>";
						document.forms['frgrm']['cComCodCB'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comcodc2'] ?>";
						document.forms['frgrm']['cComCscCB'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comcscc2'] ?>";
						document.forms['frgrm']['cComSeqCB'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comseqc2'] ?>";
						document.forms['frgrm']['cComFecCB'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['regfcrex'] ?>";
						document.forms['frgrm']['cDocInf'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comdocin'] ?>";

						<?php 
						$vComDocFa = explode('~',$xRCD['comdocfa']); ?>
						
						document.forms['frgrm']['cDocFacA' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vComDocFa[0] ?>";
						document.forms['frgrm']['cDocFac'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vComDocFa[1] ?>";

						if ("<?php echo $vCtoCon['ctovlr01'] ?>" == "SI" || "<?php echo $vCtoCon['ctovlr02'] ?>" == "SI") {
						  if ("<?php echo $vCtoCon['pucretxx'] ?>" > 0) {
                document.forms['frgrm']['nComBRet'+document.forms['frgrm']['nSecuencia'].value].value = "<?php if($xRCD['comvlr01'] > 0){echo $xRCD['comvlr01']+0;}else{echo "";} ?>";
								document.forms['frgrm']['nComBRet'+document.forms['frgrm']['nSecuencia'].value].disabled = false;
						  } else {
						    document.forms['frgrm']['nComBIva'+document.forms['frgrm']['nSecuencia'].value].value = "<?php if($xRCD['comvlr01'] > 0){echo $xRCD['comvlr01']+0;}else{echo "";} ?>";
								document.forms['frgrm']['nComIva' +document.forms['frgrm']['nSecuencia'].value].value = "<?php if($xRCD['comvlr02'] > 0){echo $xRCD['comvlr02']+0;}else{echo "";} ?>";
								document.forms['frgrm']['nComBIva'+document.forms['frgrm']['nSecuencia'].value].disabled = false;
								document.forms['frgrm']['nComIva' +document.forms['frgrm']['nSecuencia'].value].disabled = false;
						  }
						}
						document.forms['frgrm']['nComVlr'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php if($xRCD['comvlrxx'] > 0){echo $xRCD['comvlrxx']+0;}else{echo "";} ?>";
						document.forms['frgrm']['nComVlrNF'+document.forms['frgrm']['nSecuencia'].value].value = "<?php if($xRCD['comvlrnf'] > 0){echo $xRCD['comvlrnf']+0;}else{echo "";} ?>";
						document.forms['frgrm']['cComMov'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['commovxx'] ?>";
						document.forms['frgrm']['cComNit'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['ctonitxx'] ?>";
						switch ("<?php echo $vCtoCon['ctonitxx'] ?>") {
							case "CLIENTE":
								document.forms['frgrm']['cTerTip'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['tertipxx'] ?>";
								document.forms['frgrm']['cTerId'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['teridxxx'] ?>";
								document.forms['frgrm']['cTerNom'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vConCab['ternomxx'] ?>";
								document.forms['frgrm']['cTerTipB' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['tertip2x'] ?>";
								document.forms['frgrm']['cTerIdB'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['terid2xx'] ?>";
								document.forms['frgrm']['cTerNomB' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vConCab['ternom2x'] ?>";
							break;
							case "TERCERO":
								document.forms['frgrm']['cTerTip'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['tertip2x'] ?>";
								document.forms['frgrm']['cTerId'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['terid2xx'] ?>";
								document.forms['frgrm']['cTerNom'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vConCab['ternom2x'] ?>";
								document.forms['frgrm']['cTerTipB' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['tertipxx'] ?>";
								document.forms['frgrm']['cTerIdB'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['teridxxx'] ?>";
								document.forms['frgrm']['cTerNomB' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vConCab['ternomxx'] ?>";
							break;
						}
						document.forms['frgrm']['cPucId'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['pucidxxx'] ?>";
						document.forms['frgrm']['cPucDet'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucdetxx'] ?>";
						document.forms['frgrm']['cPucTer'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucterxx'] ?>";
						document.forms['frgrm']['nPucBRet' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucbaret'] ?>";
						document.forms['frgrm']['nPucRet'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucretxx'] ?>";
						document.forms['frgrm']['cPucNat'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucnatxx'] ?>";
						document.forms['frgrm']['cPucInv'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucinvxx'] ?>";
						document.forms['frgrm']['cPucCco'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['puccccxx'] ?>";
						document.forms['frgrm']['cPucDoSc' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['pucdoscc'] ?>";
						document.forms['frgrm']['cPucTipEj'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['puctipej'] ?>";
						document.forms['frgrm']['cComVlr1' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['ctovlr01'] ?>";
						document.forms['frgrm']['cComVlr2' +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $vCtoCon['ctovlr02'] ?>";
						document.forms['frgrm']['cComFac'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comfacxx'] ?>";
						document.forms['frgrm']['cComComLi'+document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comcomli'] ?>";
						document.forms['frgrm']['cSucId'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['sucidxxx'] ?>";
						document.forms['frgrm']['cDocId'   +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['docidxxx'] ?>";
						document.forms['frgrm']['cDocSuf'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['docsufxx'] ?>";
						document.forms['frgrm']['cComEst'  +document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $xRCD['comestxx'] ?>";

						if (document.forms['frgrm']['cSccId_SucId'].value == "" && document.forms['frgrm']['cSccId'].value == "<?php echo $xRCD['sccidxxx'] ?>") {
							document.forms['frgrm']['cSccId_SucId'].value  = "<?php echo $xRCD['sucidxxx'] ?>";
							document.forms['frgrm']['cSccId_DocId'].value  = "<?php echo $xRCD['docidxxx'] ?>";
							document.forms['frgrm']['cSccId_DocSuf'].value = "<?php echo $xRCD['docsufxx'] ?>";
						}

						//Habilitando Grilla segun tipo de ejecucion
						switch ("<?php echo $xRCD['puctipej'] ?>") {
					    case "L": //Tipo ejecucion Local
					    	document.forms['frgrm']['nComVlr'  +document.forms['frgrm']['nSecuencia'].value].disabled = false;
					    	document.forms['frgrm']['nComVlrNF'+document.forms['frgrm']['nSecuencia'].value].disabled = true;
						  break;
						  case "N": //Ejecucion NIIF
						   	document.forms['frgrm']['nComVlr'  +document.forms['frgrm']['nSecuencia'].value].disabled = true;
						   	document.forms['frgrm']['nComVlrNF'+document.forms['frgrm']['nSecuencia'].value].disabled = false;
							break;
							default: //Ambas
					    	document.forms['frgrm']['nComVlr'  +document.forms['frgrm']['nSecuencia'].value].disabled = false;
					    	document.forms['frgrm']['nComVlrNF'+document.forms['frgrm']['nSecuencia'].value].disabled = false;
							break;
				    }

						if  ("<?php echo $vCtoCon['puctipej'] ?>" == "L" || "<?php echo $vCtoCon['puctipej'] ?>" == "") {
					 		if ("<?php echo $vCtoCon['ctovlr01'] ?>" == "SI" || "<?php echo $vCtoCon['ctovlr02'] ?>" == "SI") {
						  	if ("<?php echo $vCtoCon['pucretxx'] ?>" > 0) { // Es una retencion
						  		document.forms['frgrm']['nComBIva' +document.forms['frgrm']['nSecuencia'].value].disabled = true;
						  		document.forms['frgrm']['nComIva'  +document.forms['frgrm']['nSecuencia'].value].disabled = true;
						  		document.forms['frgrm']['nComBRet' +document.forms['frgrm']['nSecuencia'].value].disabled = false;
								} else { // Es un IVA.
									document.forms['frgrm']['nComBRet' +document.forms['frgrm']['nSecuencia'].value].disabled = true;
									document.forms['frgrm']['nComBIva' +document.forms['frgrm']['nSecuencia'].value].disabled = false;
									document.forms['frgrm']['nComIva'  +document.forms['frgrm']['nSecuencia'].value].disabled = false;
								}
					  	}
				  	} else if ("<?php echo $vCtoCon['puctipej'] ?>" == "N") {
							//Para la ejecucion NIIF no aplican retenciones, ni IVA
							document.forms['frgrm']['nComBRet'+document.forms['frgrm']['nSecuencia'].value].disabled = true;
							document.forms['frgrm']['nComBIva'+document.forms['frgrm']['nSecuencia'].value].disabled = true;
							document.forms['frgrm']['nComIva' +document.forms['frgrm']['nSecuencia'].value].disabled = true;
							if ("<?php echo $vCtoCon['ctovlr01'] ?>" == "SI" || "<?php echo $vCtoCon['ctovlr02'] ?>" == "SI") {
								if (document.forms['frgrm']['nPucRet'+document.forms['frgrm']['nSecuencia'].value].value > 0) { // Es una retencion
									//No Hace Nada
								} else { // Es un IVA, se debe digitar base Iva, no se calcula Iva
									document.forms['frgrm']['nComBIva'+document.forms['frgrm']['nSecuencia'].value].disabled = false;
								}
							}
						}
						//Habilitando Grilla segun tipo de ejecucion
					</script>
				<?php } ?>
				<script languaje = "javascript">
					f_Cuadre_Debitos_Creditos();
				</script>
			<?php }
		}
 ?>
	</body>
</html>
<?php // Hola Mundo ...

	ini_set("memory_limit","512M");
  set_time_limit(0);

	//ini_set('error_reporting', E_ERROR);
 	//ini_set("display_errors","1");

 	/**
	 * Nuevo Modelo de Facturacion - Utilizando Tablas temporales (2015-05)
	 * Johana Arboleda Ramos <johana.arboleda@opentecnologia.com.co>
	 *
	 * NOTA: si se adiciona algun campo en la grilla DOS, Pagos a terceros o Ingresos propios,
	 * el nuevo campo debe adicionarse en las tablas temporales que estan en el uticones.php
	 * Adicional a esto se debe hacer un act borrando estas tablas temporales del sistema,
	 * para que el proceso de facturacion cree automaticamente las nuevas tablas con los cambios realizados.
	 */

	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
	$kSystemCookie = explode("~",$_COOKIE["kDatosFijos"]);
	include("../../../../libs/php/utility.php");
	include("../../../../libs/php/uticonta.php");
	switch ($kSystemCookie[3]) {
    case "TEALPOPULP": 
      include("../../../../../ws/alpopulp/utiwsout.php"); 
      include("../../../../../ws/alpopula/utiwssap.php"); 
    break;
    case "ALPOPULX":   
      include("../../../../../ws/alpopula/utiwsout.php"); 
      include("../../../../../ws/alpopula/utiwssap.php"); 
    break;
		case "ALMAVIVA": case "TEALMAVIVA":
      include("../../../../../ws/almaviva/utiwssou.php");
      include("../../../../../ws/almaviva/utiwssap.php"); 
		break;
	}
  include("../../../../../config/config.php");
  include("../../../../libs/php/utiliqdo.php");
 	include("../../../../libs/php/uticones.php");

	$kMysqlDb = $kDf[3];

	/**
	 * Ajuste  Nueva Parametrizacion Perfiles Especiales
	 * Johana Arboleda Ramos 2012-10-18 10:28
	 */
	$qPerEsp  = "SELECT proidxxx, spridxxx, sucidxxx ";
  $qPerEsp .= "FROM $cAlfa.sys00123 ";
  $qPerEsp .= "WHERE usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
  $qPerEsp .= "proidxxx = \"103\" AND ";
  $qPerEsp .= "spridxxx = \"100\" AND ";
  $qPerEsp .= "regestxx = \"ACTIVO\" ";
  $xPerEsp  = f_MySql("SELECT","",$qPerEsp,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qPerEsp."~".mysql_num_rows($xPerEsp));
  $cUsrSuc = "";
  while($xRPE = mysql_fetch_array($xPerEsp)) {
    if(in_array($xRPE['sucidxxx'],$mUsrSuc) == false) {
      $cUsrSuc .= "\"{$xRPE['sucidxxx']}\",";
      $mUsrSuc[] = $xRPE['sucidxxx'];
    }
  }
	$cUsrSuc = substr($cUsrSuc,0,strlen($cUsrSuc)-1);


	$qCcoId = "SELECT ccoidxxx FROM $cAlfa.fpar0008 WHERE sucidxxx IN ($cUsrSuc) AND regestxx = \"ACTIVO\"";
  $xCcoId = f_MySql("SELECT","",$qCcoId,$xConexion01,"");
  $cUsrCco = "";
  $mUsrCco = array();
  while ($xCI = mysql_fetch_array($xCcoId)){
  	if (in_array($xCI['ccoidxxx'],$mUsrCco) == false) {
  		$cUsrCco .= "\"{$xCI['ccoidxxx']}\",";
  		$mUsrCco = $xCI['ccoidxxx'];
  	}
  }
  $cUsrCco = substr($cUsrCco,0,strlen($cUsrCco)-1);
  #Fin Busco el centro de costo del usuario.
?>
<html>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>

		<style type="text/css">
			.bntGuardar {
				width:91px;
				height:21px;
				border:0px;
				cursor:pointer;
				text-align:center;
				background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif)
			}
		</style>

		<script languaje = 'javascript'>
  		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
  			document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
  	  }

  	  function f_Salir() {
  	    if(!parent.fmnav) {
          window.close();
  	    } else {
          document.location='<?php echo $_COOKIE['kIniAnt'] ?>';
          parent.fmnav.location='<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php';
        }
  	  }

			function f_Links(xLink,xSwitch,xSecuencia,xGrid) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
					case "cTerId":
					case "cTerNom":
					  var nSwitch = 0; var cMsj = "";
					  if (document.forms['frgrm']['cComTCo'].value == "") {
					    nSwitch = 0;
					    cMsj += "Debe Seleccionar el Tipo de Cobro.\n";
					  }

					  if (nSwitch == 0) {
  						if (xSwitch == "VALID") {
  							var cPathUrl = "frfac150.php?gModo="+xSwitch+"&gFunction="+xLink+
  																				"&gTerTip="  +document.forms['frgrm']['cTerTip'].value.toUpperCase()+
  																				"&gTerId="   +document.forms['frgrm']['cTerId'].value.toUpperCase()+
  																				"&gTerNom="  +document.forms['frgrm']['cTerNom'].value.toUpperCase()+
  																				"&gComTCo="  +document.forms['frgrm']['cComTCo'].value+
  																				"&gRegFCre=" +document.forms['frgrm']['dRegFCre'].value;
  							// alert(cPathUrl);
  							parent.fmpro.location = cPathUrl;
  						} else {
  							var nNx      = (nX-600)/2;
  							var nNy      = (nY-250)/2;
  							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
  							var cPathUrl = "frfac150.php?gModo="+xSwitch+"&gFunction="+xLink+
  																				 "&gTerTip="  +document.forms['frgrm']['cTerTip'].value.toUpperCase()+
  																				 "&gTerId="   +document.forms['frgrm']['cTerId'].value.toUpperCase()+
  																				 "&gTerNom="  +document.forms['frgrm']['cTerNom'].value.toUpperCase()+
  																				 "&gComTCo="  +document.forms['frgrm']['cComTCo'].value+
  																				 "&gRegFCre=" +document.forms['frgrm']['dRegFCre'].value;
  							cWindow = window.open(cPathUrl,xLink,cWinOpt);
  				  		cWindow.focus();
  						}
						} else {
						  alert(cMsj+"Verifique.");
						}
					break;
					case "cTerIdInt":
					case "cTerNomInt":
            var nSwitch = 0; var cMsj = "";
            if (document.forms['frgrm']['cComTCo'].value == "") {
              nSwitch = 0;
              cMsj += "Debe Seleccionar el Tipo de Cobro.\n";
            }

            if (nSwitch == 0) {
  						if (xSwitch == "VALID") {
  							var cPathUrl = "frfacint.php?gModo="+xSwitch+"&gFunction="+xLink+
  																				"&gTerId="   +document.forms['frgrm']['cTerId'].value+
  																				"&gComTCo="  +document.forms['frgrm']['cComTCo'].value+
  																				"&gRegFCre=" +document.forms['frgrm']['dRegFCre'].value;
  							// alert(cPathUrl);
  							parent.fmpro.location = cPathUrl;
  						} else {
  							var nNx      = (nX-600)/2;
  							var nNy      = (nY-250)/2;
  							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
  							var cPathUrl = "frfacint.php?gModo="    +xSwitch+"&gFunction="+xLink+
  																				 "&gTerId="   +document.forms['frgrm']['cTerId'].value+
  																				 "&gComTCo="  +document.forms['frgrm']['cComTCo'].value+
  																				 "&gRegFCre=" +document.forms['frgrm']['dRegFCre'].value;
  							cWindow = window.open(cPathUrl,xLink,cWinOpt);
  				  		cWindow.focus();
  						}
  					} else {
              alert(cMsj+"Verifique.");
            }
					break;

					///// Inicio LLamado a la Grilla de Tramites /////
					case "cDosNro_DOS":
						if (xSwitch == "VALID") {
							var cPathUrl = "frfac200.php?gModo=VALID&gFunction="+xLink+
	  												             "&gDosNro="   +document.forms['frgrm']['cDosNro_DOS'+xSecuencia].value.toUpperCase()+
		  												           "&gDosSuc="   +document.forms['frgrm']['cDosSuf_DOS'+xSecuencia].value.toUpperCase()+
		  												           "&gTabla_GEN="+document.forms['frgrm']['cTabla_GEN'].value+
		  												           "&gTabla_DOS="+document.forms['frgrm']['cTabla_DOS'].value+
		  												           "&gFacId="    +document.forms['frgrm']['cFacId'].value+
		  												           "&gSecuencia="+xSecuencia;

							//alert(xSwitch + " -> " + cPathUrl);
							parent.fmpro.location = cPathUrl;
						} else {
							if (document.forms['frgrm']['cComCon'].value == "SI") {
								var nAncho = 800;
				        var nAlto  = 300;
							} else {
								var nAncho = 550;
								var nAlto  = 300;
							}
							var nNx      = (nX-nAncho)/2;
							var nNy      = (nY-nAlto)/2;
							var cWinOpt  = "width="+nAncho+",scrollbars=1,height="+nAlto+",left="+nNx+",top="+nNy;
							var cPathUrl = "frfacfrm.php?gModo=WINDOW&gFunction="+xLink+
  												             "&gArchivo=frfac200.php"+
  												             "&gDosNro="   +document.forms['frgrm']['cDosNro_DOS'+xSecuencia].value.toUpperCase()+
	  												           "&gDosSuc="   +document.forms['frgrm']['cDosSuf_DOS'+xSecuencia].value.toUpperCase()+
	  												           "&gTabla_GEN="+document.forms['frgrm']['cTabla_GEN'].value+
	  												           "&gTabla_DOS="+document.forms['frgrm']['cTabla_DOS'].value+
	  												           "&gFacId="    +document.forms['frgrm']['cFacId'].value+
	  												           "&gSecuencia="+xSecuencia;
  						//alert(xSwitch + " -> " + cPathUrl);
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
				  		cWindow.focus();
						}
					break;
					///// Inicio LLamado a la Grilla de Ingresos Propios /////
					case "cComId_IPA":

						if (document.forms['frgrm']['cComTFa'].value == "MANUAL") {
							var nBorrar = 0;

			    		// Si el Concepto ha Cambiado en el Item, Limpio Todo el Row
			    		if (document.forms['frgrm']['cComId_IPA'+xSecuencia].id != document.forms['frgrm']['cComId_IPA'+xSecuencia].value) {
			    			nBorrar = 1;
			    		}

				    	if (xSwitch == "VALID") {
								var cPathUrl = "frfac129.php?gModo=VALID&gFunction="+xLink+
														   "&gComId="+document.forms['frgrm']['cComId_IPA'+xSecuencia].value.toUpperCase()+
														   "&gSecuencia="+xSecuencia+
														   "&gTabla_GEN="+document.forms['frgrm']['cTabla_GEN'].value+
														   "&gTabla_DOS="+document.forms['frgrm']['cTabla_DOS'].value+
														   "&gTabla_IPA="+document.forms['frgrm']['cTabla_IPA'].value+
														   "&gTabla_PCCA="+document.forms['frgrm']['cTabla_PCCA'].value+
		  						             "&gFacId="+document.forms['frgrm']['cFacId'].value+
		  						             "&gPCCVNe="+document.forms['frgrm']['nPCCVNe'].value+
					          					 "&gIPAAnt="+document.forms['frgrm']['nIPAAnt'].value+
		  						             "&gBorrar="+nBorrar;
							  //alert(cPathUrl);
								parent.fmpro.location = cPathUrl;
				    	} else {
								var nNx      = (nX-820)/2;
								var nNy      = (nY-300)/2;
								var cWinOpt  = "width=820,scrollbars=1,height=300,left="+nNx+",top="+nNy;
								var cPathUrl = "frfacfrm.php?gModo=WINDOW&gFunction="+xLink+
	  												   "&gArchivo=frfac129.php"+
														   "&gComId="+document.forms['frgrm']['cComId_IPA'+xSecuencia].value.toUpperCase()+
														   "&gSecuencia="+xSecuencia+
														   "&gTabla_GEN="+document.forms['frgrm']['cTabla_GEN'].value+
														   "&gTabla_DOS="+document.forms['frgrm']['cTabla_DOS'].value+
														   "&gTabla_IPA="+document.forms['frgrm']['cTabla_IPA'].value+
														   "&gTabla_PCCA="+document.forms['frgrm']['cTabla_PCCA'].value+
		  						             "&gFacId="+document.forms['frgrm']['cFacId'].value+
		  						             "&gPCCVNe="+document.forms['frgrm']['nPCCVNe'].value+
					          					 "&gIPAAnt="+document.forms['frgrm']['nIPAAnt'].value+
		  						             "&gBorrar="+nBorrar;
								//alert(cPathUrl);
								cWindow = window.open(cPathUrl,xLink,cWinOpt);
					  		cWindow.focus();
				    	}
				    }
					break;

					case "cComTra_IPA":
						if (document.forms['frgrm']['cComTFa'].value == "MANUAL" && (eval(document.forms['frgrm']['nSecuencia_Dos'].value) > 1 || document.forms['frgrm']['cPucDet_IPA'+xSecuencia].value == "P")) {
							if (document.forms['frgrm']['cComId_IPA'+xSecuencia].value != "") {
								if (xSwitch == "VALID") {
									// No se hace nada.
								} else {
									var nNx      = (nX-550)/2;
									var nNy      = (nY-250)/2;
									var cWinOpt  = "width=550,scrollbars=1,height=250,left="+nNx+",top="+nNy;
									var cPathUrl = "frfacfrm.php?gModo=WINDOW&gFunction="+xLink+
																 "&gArchivo=frfacddc.php"+
		    						             "&gSecuencia="+xSecuencia+
																 "&gTabla_GEN="+document.forms['frgrm']['cTabla_GEN'].value+
																 "&gTabla_DOS="+document.forms['frgrm']['cTabla_DOS'].value+
																 "&gTabla_IPA="+document.forms['frgrm']['cTabla_IPA'].value+
																 "&gTabla_PCCA="+document.forms['frgrm']['cTabla_PCCA'].value+
								  						   "&gFacId="+document.forms['frgrm']['cFacId'].value+
								  						   "&gPCCVNe="+document.forms['frgrm']['nPCCVNe'].value+
					          					 	 "&gIPAAnt="+document.forms['frgrm']['nIPAAnt'].value;
		  						//alert(cPathUrl);
									cWindow = window.open(cPathUrl,xLink,cWinOpt);
						  		cWindow.focus();
								}
							}
						}
					break;
					case "cMePagId":
						if (xSwitch == "VALID") {
							var cPathUrl  = "frfac155.php?gModo="+xSwitch+"&gFunction="+xLink+"&gMePagId="+document.forms['frgrm']['cMePagId'].value;
							parent.fmpro.location = cPathUrl;
						} else {
							if (xSwitch == "WINDOW") {
								var zNx     = (nX-600)/2;
								var zNy     = (nY-250)/2;
								var cWinOpt = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
								var cPathUrl   = "frfac155.php?gModo="+xSwitch+"&gFunction="+xLink+"&gMePagId="+document.forms['frgrm']['cMePagId'].value;
								zWindow = window.open(cPathUrl,"zWindow",cWinOpt);
								zWindow.focus();
							} else {
								if (xSwitch == "EXACT") {
									var cPathUrl  = "frfac155.php?gModo="+xSwitch+"&gFunction="+xLink+"&gMePagId="+document.forms['frgrm']['cMePagId'].value;
									parent.fmpro.location = cPathUrl;
                }
							}
						}
					break;
				}
			}

			function f_Tasa(){
    		var x = screen.width;
  			var y = screen.height;
  		  var nx = (x-450)/2;
  			var ny = (y-450)/2;
  			var str = 'width=450,scrollbars=1,height=450,left='+nx+',top='+ny;
  			var rut = "frdoitas.php";
  			msg = window.open(rut,'myw',str);
  			msg.focus();
    	}

			function f_Cambiar_Tipo_Cobro(xValue, xBusPla){
        switch (xValue) {
          case 'TODO':
            document.forms['frgrm']['cComTCD'].value = 'PAGOS POR CUENTA DEL CLIENTE E INGRESOS PROPIOS';
            document.forms['frgrm']['cComTCD'].style['color'] = '#ff0000';
          break;
          case 'PCC':
            document.forms['frgrm']['cComTCD'].value = 'PAGOS POR CUENTA DEL CLIENTE';
            document.forms['frgrm']['cComTCD'].style['color'] = '#00ff00';
          break;
          case 'IP':
            document.forms['frgrm']['cComTCD'].value = 'INGRESOS PROPIOS';
            document.forms['frgrm']['cComTCD'].style['color'] = '#0000ff';
          break;
        }

        if (xBusPla == "SI") {
          if (document.forms['frgrm']['cTerIdInt'].value != '') {
            var cPathUrl = "frfacpla.php?&gTerId="+document.forms['frgrm']['cTerId'].value
  												 +"&gTerIdInt="+document.forms['frgrm']['cTerIdInt'].value
                           +"&gComTCo="+document.forms['frgrm']['cComTCo'].value;
            // alert(cPathUrl);
            parent.fmpro.location = cPathUrl;
          }
        }
      }

			function f_Ver_Condiciones_Especiales_x_DO(xSucId,xDocId,xDocSuf) {
				if (xSucId != "" && xDocId != "" && xDocSuf != "") {
				  var cRuta = "frfacces.php?gSucId="+xSucId+"&gDoiId="+xDocId+"&gDocSuf="+xDocSuf;
          parent.fmpro.location = cRuta; // Invoco el menu.
				} else {
					alert("No Hay DO Seleccionado en la Grilla, Verifique.");
				}
			}

		  function f_Mostrar_u_Ocultar_Objetos(xStep)	{
		  	// Oculto campos de valor FOB y cantidad de formularios que solo aplican para EXPORTACIONES y DTA's.
		  	switch (xStep) {
		  		case "1":
		  			document.getElementById("Datos_del_Comprobante").style.display="block";
		  			document.getElementById("Datos_del_Importador").style.display="block";
						<?php if($vSysStr['system_activar_openetl'] == "SI") { ?>
							document.getElementById("Datos_adicionales_openetl").style.display="block";
						<?php } else { ?>
							document.getElementById("Datos_adicionales_openetl").style.display="none";
						<?php } ?>
            if ('<?php echo $cAlfa ?>' == "EXPORCOM" || '<?php echo $cAlfa ?>' == "TEEXPORCOM" || '<?php echo $cAlfa ?>' == "DEEXPORCOM") {
		  			  document.getElementById("Datos_adicionales").style.display="block";
            }
		  			document.getElementById("Grid_de_Tramites").style.display="none";
		  			document.getElementById("Pagos_del_Cliente_Automaticos").style.display="none";
		  			document.getElementById("Pagos_del_Cliente_Totales").style.display="none";
		  			document.getElementById("Ingresos_Propios_Automaticos").style.display="none";
		  			document.getElementById("Ingresos_Propios_Tototales").style.display="none";
		  		break;
		  		case "2":
		  			document.getElementById("Datos_del_Comprobante").style.display="none";
		  			document.getElementById("Datos_del_Importador").style.display="none";
            if ('<?php echo $cAlfa ?>' == "EXPORCOM" || '<?php echo $cAlfa ?>' == "TEEXPORCOM" || '<?php echo $cAlfa ?>' == "DEEXPORCOM") {
		  			  document.getElementById("Datos_adicionales").style.display="none";
            }
						document.getElementById("Datos_adicionales_openetl").style.display="none"; 
		  			document.getElementById("Grid_de_Tramites").style.display="block";
		  			document.getElementById("Pagos_del_Cliente_Automaticos").style.display="none";
		  			document.getElementById("Pagos_del_Cliente_Totales").style.display="none";
		  			document.getElementById("Ingresos_Propios_Automaticos").style.display="none";
		  			document.getElementById("Ingresos_Propios_Tototales").style.display="none";
		  		break;
		  		case "3":
		  			document.getElementById("Datos_del_Comprobante").style.display="none";
		  			document.getElementById("Datos_del_Importador").style.display="none";
            if ('<?php echo $cAlfa ?>' == "EXPORCOM" || '<?php echo $cAlfa ?>' == "TEEXPORCOM" || '<?php echo $cAlfa ?>' == "DEEXPORCOM") {
		  			  document.getElementById("Datos_adicionales").style.display="none";
            }
						document.getElementById("Datos_adicionales_openetl").style.display="none";	
		  			document.getElementById("Grid_de_Tramites").style.display="none";
		  			document.getElementById("Pagos_del_Cliente_Automaticos").style.display="block";
		  			document.getElementById("Pagos_del_Cliente_Totales").style.display="block";
		  			document.getElementById("Ingresos_Propios_Automaticos").style.display="none";
		  			document.getElementById("Ingresos_Propios_Tototales").style.display="none";
		  		break;
		  		case "4":
		  			document.getElementById("Datos_del_Comprobante").style.display="none";
		  			document.getElementById("Datos_del_Importador").style.display="none";
            if ('<?php echo $cAlfa ?>' == "EXPORCOM" || '<?php echo $cAlfa ?>' == "TEEXPORCOM" || '<?php echo $cAlfa ?>' == "DEEXPORCOM") {
		  			  document.getElementById("Datos_adicionales").style.display="none";
            }
						document.getElementById("Datos_adicionales_openetl").style.display="none";
		  			document.getElementById("Grid_de_Tramites").style.display="none";
		  			document.getElementById("Pagos_del_Cliente_Automaticos").style.display="none";
		  			document.getElementById("Pagos_del_Cliente_Totales").style.display="none";
		  			document.getElementById("Ingresos_Propios_Automaticos").style.display="block";
		  			document.getElementById("Ingresos_Propios_Tototales").style.display="block";
		  			document.getElementById("imgLoad").style.display="none";
		  		break;
		  		case "SHOW":
		  			document.getElementById("TblTar1").style.display="block";
		  			document.getElementById("TblTar2").style.display="block";
		  		break;
		  	}
      }

      function f_Cuadre_Debitos_Creditos_IPA(xAccion,xSecuencia,xCampo='') {
      	f_makeRequest(xAccion,xSecuencia,xCampo);
     	}

     	function fnBorrarDos() {
     		if (confirm("Esta Seguro de Borrar todos los DOS de la Grilla?")) {
      		f_makeRequest("BORRARDOS",'');
      	}
     	}

     	function fnBorrarIPA() {
     		if (confirm("Esta Seguro de Borrar todos los Ingresos Propios de la Grilla?")) {
      		f_makeRequest("BORRARIPA",'');
      	}
     	}

	    /**
	     * Funciones Ajax para actualizar tabla de saldos marcados
	     * Recibe como parametros:
	     * xAccion    -> Accion que debe realizarse
	     * xSecuencia -> Puede Ser vacia, y es la secuencia sobre la que realiza la accion
	     */
	    function f_makeRequest(xAccion,xSecuencia,xCampo=''){

	    	http_request = false;
	      if (window.XMLHttpRequest) { // Mozilla, Safari,...
	      	http_request = new XMLHttpRequest();
	        if (http_request.overrideMimeType) {
	        	http_request.overrideMimeType('text/xml');
	        }
	      }else if (window.ActiveXObject) { // IE
        	try {
          	http_request = new ActiveXObject("Msxml2.XMLHTTP");
          } catch (e) {
            try {
              http_request = new ActiveXObject("Microsoft.XMLHTTP");
            }  catch (e) {}
          }
        }
        if (!http_request) {
          alert('Falla :( No es posible crear una instancia XMLHTTP');
          return false;
        }

        switch(xAccion) {
        	case "CUADREIPA":
        		var cRuta  = "frfacipg.php?"+
							           "cModo="         +xAccion+
							           "&cParent=VALID" +
							           "&cFacId="       +document.forms['frgrm']['cFacId'].value+
							           "&cTabla_GEN="   +document.forms['frgrm']['cTabla_GEN'].value+
							           "&cTabla_DOS="   +document.forms['frgrm']['cTabla_DOS'].value+
							           "&cTabla_IPA="   +document.forms['frgrm']['cTabla_IPA'].value+
							           "&cTabla_PCCA="  +document.forms['frgrm']['cTabla_PCCA'].value+
							           "&nSecuencia="   +xSecuencia+
                         "&nComCan_IPA="  +document.forms['frgrm']['nComCan_IPA'  +xSecuencia].value+
                         "&nComVlrU_IPA=" +document.forms['frgrm']['nComVlrU_IPA' +xSecuencia].value+
							           "&nComVlr_IPA="  +document.forms['frgrm']['nComVlr_IPA'  +xSecuencia].value+
							           "&nComVlrNF_IPA="+document.forms['frgrm']['nComVlrNF_IPA'+xSecuencia].value+
							           "&nPCCVNe="      +document.forms['frgrm']['nPCCVNe'].value+
							           "&nIPAAnt="      +document.forms['frgrm']['nIPAAnt'].value+
                         "&cCampo="       +xCampo;
						http_request.onreadystatechange = f_Acciones_IPA;
		        http_request.open('GET', cRuta, true);
		        http_request.send(null);
        	break;
        	case "CSC3PCC":
        		var cRuta  = "frfacipg.php?"  +
							           "cModo="         +xAccion+
							           "&cFacId="       +document.forms['frgrm']['cFacId'].value+
							           "&cTabla_GEN="   +document.forms['frgrm']['cTabla_GEN'].value+
							           "&cTabla_DOS="   +document.forms['frgrm']['cTabla_DOS'].value+
							           "&cTabla_IPA="   +document.forms['frgrm']['cTabla_IPA'].value+
							           "&cTabla_PCCA="  +document.forms['frgrm']['cTabla_PCCA'].value+
							           "&nSecuencia="   +xSecuencia+
							           "&cComCsc3_PCCA="+document.forms['frgrm']['cComCsc3_PCCA'+xSecuencia].value;
        		http_request.onreadystatechange = f_Acciones_PCCA;
		        http_request.open('GET', cRuta, true);
		        http_request.send(null);
        	break;
        	case "OBSIPA":
        		if (document.forms['frgrm']['cComObs_IPA'+xSecuencia].value != document.forms['frgrm']['cComObs_IPA'+xSecuencia].id) {
	        		var cRuta  = "frfacipg.php?"  +
								           "cModo="         +xAccion+
								           "&cFacId="       +document.forms['frgrm']['cFacId'].value+
								           "&cTabla_GEN="   +document.forms['frgrm']['cTabla_GEN'].value+
								           "&cTabla_DOS="   +document.forms['frgrm']['cTabla_DOS'].value+
								           "&cTabla_IPA="   +document.forms['frgrm']['cTabla_IPA'].value+
								           "&cTabla_PCCA="  +document.forms['frgrm']['cTabla_PCCA'].value+
								           "&nSecuencia="   +xSecuencia+
								           "&cComObs_IPA="  +document.forms['frgrm']['cComObs_IPA'+xSecuencia].value+
								           "&nPCCVNe="      +document.forms['frgrm']['nPCCVNe'].value+
							           	 "&nIPAAnt="      +document.forms['frgrm']['nIPAAnt'].value;
	        		http_request.onreadystatechange = f_Acciones_ObsIPA;
			        http_request.open('GET', cRuta, true);
		        	http_request.send(null);
		        }
        	break;
        	case "BORRARDOS":
        		var cRuta  = "frfac20g.php?"  +
							           "cModo="         +xAccion+
							           "&cFacId="       +document.forms['frgrm']['cFacId'].value+
							           "&cTabla_GEN="   +document.forms['frgrm']['cTabla_GEN'].value+
							           "&cTabla_DOS="   +document.forms['frgrm']['cTabla_DOS'].value+
							           "&cTabla_IPA="   +document.forms['frgrm']['cTabla_IPA'].value+
							           "&cTabla_PCCA="  +document.forms['frgrm']['cTabla_PCCA'].value+
							           "&nSecuencia="   +xSecuencia;
        		http_request.onreadystatechange = f_Acciones_borrarDos;
		        http_request.open('GET', cRuta, true);
	        	http_request.send(null);
		      break;
        	case "BORRARIPA":
        		var cRuta  = "frfacipg.php?"  +
							           "cModo="         +xAccion+
							           "&cFacId="       +document.forms['frgrm']['cFacId'].value+
							           "&cTabla_GEN="   +document.forms['frgrm']['cTabla_GEN'].value+
							           "&cTabla_DOS="   +document.forms['frgrm']['cTabla_DOS'].value+
							           "&cTabla_IPA="   +document.forms['frgrm']['cTabla_IPA'].value+
							           "&cTabla_PCCA="  +document.forms['frgrm']['cTabla_PCCA'].value+
							           "&nSecuencia="   +xSecuencia;
        		http_request.onreadystatechange = f_Acciones_borrarIPA;
		        http_request.open('GET', cRuta, true);
	        	http_request.send(null);
		      break;
        	default: //No hace nada
        	break;
        }
      }

      function f_Acciones_IPA() {
        if(http_request.readyState==1){
        }else if(http_request.readyState == 4) {
          if (http_request.status == 200) {
            if(http_request.responseText!=""){
              var cRetorno = http_request.responseText.replace(/^\s+|\s+$/g,"");
              var mRetorno = cRetorno.split("~");
              if (mRetorno[0] == "true") {
                //Hizo bien la actualizacion del saldo, actualizo campos
                document.forms['frgrm']['cComSeq_IPA'  + (eval(mRetorno[2])+0)].value = mRetorno[3];
	  						document.forms['frgrm']['cComId_IPA'   + (eval(mRetorno[2])+0)].value = mRetorno[4];
	  						document.forms['frgrm']['cComObs_IPA'  + (eval(mRetorno[2])+0)].value = mRetorno[5];
	  						document.forms['frgrm']['cComTra_IPA'  + (eval(mRetorno[2])+0)].value = mRetorno[6];
                document.forms['frgrm']['nComCan_IPA'  + (eval(mRetorno[2])+0)].value = ((eval(mRetorno[25])+0) > 0) ? (eval(mRetorno[25])+0) : "";
                document.forms['frgrm']['nComVlrU_IPA' + (eval(mRetorno[2])+0)].value = ((eval(mRetorno[26])+0) > 0) ? (eval(mRetorno[26])+0) : "";
	  						document.forms['frgrm']['nComVlr_IPA'  + (eval(mRetorno[2])+0)].value = ((eval(mRetorno[7])+0) > 0) ? (eval(mRetorno[7])+0) : "";
	  						document.forms['frgrm']['nComVlrNF_IPA'+ (eval(mRetorno[2])+0)].value = ((eval(mRetorno[8])+0) > 0) ? (eval(mRetorno[8])+0) : "";
	  						document.forms['frgrm']['cComMov_IPA'  + (eval(mRetorno[2])+0)].value = mRetorno[9];
	  						document.forms['frgrm']['cComMov_IPA'  + (eval(mRetorno[2])+0)].title = mRetorno[10];
	  						document.forms['frgrm']['cPucDet_IPA'  + (eval(mRetorno[2])+0)].value = mRetorno[24];
	  						document.forms['frgrm']['nIPAIva'].value   = mRetorno[11];
	  						document.forms['frgrm']['nIPATot'].value   = mRetorno[12];
	  						document.forms['frgrm']['nIPASub'].value   = mRetorno[13];
	  						document.forms['frgrm']['nIPARFte'].value  = mRetorno[14];
	  						document.forms['frgrm']['nIPAARFte'].value = mRetorno[15];
	  						document.forms['frgrm']['nIPARCre'].value  = mRetorno[16];
	  						document.forms['frgrm']['nIPAARCre'].value = mRetorno[17];
	  						document.forms['frgrm']['nIPARIva'].value  = mRetorno[18];
	  						document.forms['frgrm']['nIPARIca'].value  = mRetorno[19];
	  						document.forms['frgrm']['nIPAARIca'].value = mRetorno[20];
	  						document.forms['frgrm']['nIPAAnt'].value   = mRetorno[21];
	  						document.forms['frgrm']['nIPASal'].value   = mRetorno[22];
	  						document.forms['frgrm']['cComSal'].value   = mRetorno[23];
	  						if (mRetorno[3] != "") {
	  							document.forms['frgrm']['cComMov_IPA'+(eval(mRetorno[2])+0)].focus();
	  						} else {
	  							document.forms['frgrm']['cComId_IPA' +(eval(mRetorno[2])+0)].focus();
	  						}
              } else {
              	alert("Error al Actualizar la tabla temporal [IP].\n"+mRetorno[2]);
              }
            }else{
              //No Hace Nada
            }
          } else {
            alert('Hubo problemas con la peticion [IP].');
          }
        }
      }

      function f_Acciones_PCCA() {
        if(http_request.readyState==1){
        }else if(http_request.readyState == 4) {
          if (http_request.status == 200) {
            if(http_request.responseText!=""){
              var cRetorno = http_request.responseText.replace(/^\s+|\s+$/g,"");
              var mRetorno = cRetorno.split("~");
              if (mRetorno[0] == "true") {
                //Hizo bien la actualizacion del consecutivo 3
                document.forms['frgrm']['cComCsc3_PCCA'+(eval(mRetorno[2])+0)].value = mRetorno[3];
              } else {
              	alert(mRetorno[2]);
              	document.forms['frgrm']['cComCsc3_PCCA'+(eval(mRetorno[2])+0)].value = "";
              	document.forms['frgrm']['cComCsc3_PCCA'+(eval(mRetorno[2])+0)].focus();
              }
            }else{
              //No Hace Nada
            }
          } else {
            alert('Hubo problemas con la peticion [PCCA].');
          }
        }
      }

      function f_Acciones_ObsIPA() {
        if(http_request.readyState==1){
        }else if(http_request.readyState == 4) {
          if (http_request.status == 200) {
            if(http_request.responseText!=""){
              var cRetorno = http_request.responseText.replace(/^\s+|\s+$/g,"");
              var mRetorno = cRetorno.split("~");
              if (mRetorno[0] == "true") {
                //Hizo bien la actualizacion de la observacion del IPA
                document.forms['frgrm']['cComObs_IPA'+(eval(mRetorno[2])+0)].value = mRetorno[3];
                document.forms['frgrm']['cComTra_IPA'+(eval(mRetorno[2])+0)].focus();
              } else {
              	alert(mRetorno[2]);
              	document.forms['frgrm']['cComObs_IPA'+(eval(mRetorno[2])+0)].value = "";
              	document.forms['frgrm']['cComObs_IPA'+(eval(mRetorno[2])+0)].focus();
              }
            }else{
              //No Hace Nada
            }
          } else {
            alert('Hubo problemas con la peticion [OBS IP].');
          }
        }
      }

      function f_Acciones_borrarDos() {
        if(http_request.readyState==1){
        }else if(http_request.readyState == 4) {
          if (http_request.status == 200) {
            if(http_request.responseText!=""){
              var cRetorno = http_request.responseText.replace(/^\s+|\s+$/g,"");
              var mRetorno = cRetorno.split("~");
              if (mRetorno[0] == "true") {
              	//Borro todos los Dos
                fnBorrarGrilla('Grid_Tramites');
                // Si la BD es del cliente DHL Express se borran las observaciones del DO
								if ("<?php echo $cAlfa ?>" == 'DHLEXPRE' || "<?php echo $cAlfa ?>" == 'DEDHLEXPRE' || "<?php echo $cAlfa ?>" == 'TEDHLEXPRE') {
                  fnBorrarGrilla('Grid_Tramites_Observaciones');
                }
              } else {
              	alert(mRetorno[2]);
              }
            }else{
              //No Hace Nada
            }
          } else {
            alert('Hubo problemas con la peticion [BORRAR DOS].');
          }
        }
      }

      function f_Acciones_borrarIPA() {
        if(http_request.readyState==1){
        }else if(http_request.readyState == 4) {
          if (http_request.status == 200) {
            if(http_request.responseText!=""){
              var cRetorno = http_request.responseText.replace(/^\s+|\s+$/g,"");
              var mRetorno = cRetorno.split("~");
              if (mRetorno[0] == "true") {
              	//Borro todos los IPA
                fnBorrarGrilla('Grid_IPA');
              } else {
              	alert(mRetorno[2]);
              }
            }else{
              //No Hace Nada
            }
          } else {
            alert('Hubo problemas con la peticion [BORRAR IPA].');
          }
        }
      }

			function f_Enter(e,xName,xGrilla) {
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

				if (code == 13) {
					switch (xGrilla) {
						case "Grid_Tramites":
							if (xName == 'cDosCE_DOS'+eval(document.forms['frgrm']['nSecuencia_Dos'].value)) {
								f_Add_New_Row_Tramites();
							}
						break;
						case "Grid_IPA":
							if (xName == 'nComVlr_IPA'  +eval(document.forms['frgrm']['nSecuencia_IPA'].value) ||
							    xName == 'nComVlrNF_IPA'+eval(document.forms['frgrm']['nSecuencia_IPA'].value) ||
							    xName == 'cComMov_IPA'  +eval(document.forms['frgrm']['nSecuencia_IPA'].value)) {
								f_Add_New_Row_IPA();
							}
						break;
					}
				}
			}

	    function f_Delete_Row(xNumRow,xSecuencia,xTabla) {
				switch (xTabla) {
					case "Grid_Tramites":
						var cGrid = document.getElementById(xTabla);
						var nLastRow = cGrid.rows.length;
						if (nLastRow > 1 && xNumRow == "X") {
							var nBorrar = 0;

							if (!confirm("Realmente Desea Eliminar la Secuencia ["+document.forms['frgrm']['cSeq_DOS'+xSecuencia].value+"], del DO ["+document.forms['frgrm']['cSucId_DOS'+xSecuencia].value+"-"+document.forms['frgrm']['cDosNro_DOS'+xSecuencia].value+"-"+document.forms['frgrm']['cDosSuf_DOS'+xSecuencia].value+"]?")){
								nBorrar = 1;
							}

							if (nBorrar == 0){
								fnAsignarValores();
								document.forms['frestado']['nSecuencia'].value = xSecuencia;
	  						document.forms['frestado']['cModo'].value      = "BORRAR";
	  						document.forms['frestado'].action = "frfac20g.php";
								document.forms['frestado'].target = "fmpro";
								document.forms['frestado'].submit();
					  	}
						} else {
							alert("No se Pueden Eliminar Todas las Secuencias, Verifique.");
						}
					break;
					case "Grid_IPA":
						var cGrid = document.getElementById(xTabla);
						var nLastRow = cGrid.rows.length;
						if (nLastRow > 1 && xNumRow == "X") {
							var nBorrar = 0;

							var cTexto = (document.forms['frgrm']['cComTra_IPA'+xSecuencia].value != "") ? " del Tramite ["+document.forms['frgrm']['cComTra_IPA'+xSecuencia].value+"]": "";
							if (!confirm("Realmente Desea Eliminar el Concepto ["+document.forms['frgrm']['cComId_IPA'+xSecuencia].value+"]"+cTexto+"?")){
								nBorrar = 1;
							}

							if (nBorrar == 0){
								fnAsignarValores();
								document.forms['frestado']['nSecuencia'].value = xSecuencia;
	  						document.forms['frestado']['cModo'].value      = "BORRAR";
	  						document.forms['frestado'].action = "frfacipg.php";
								document.forms['frestado'].target = "fmpro";
								document.forms['frestado'].submit();
					  	}
						} else {
							alert("No se Pueden Eliminar Todas las Secuencias, Verifique.");
						}
					break;
					default: //No hace nada
					break;
				}
			}

			function f_Add_New_Row_Tramites() {
				var cGrid      = document.getElementById("Grid_Tramites");
				var nLastRow   = cGrid.rows.length;
				var nSecuencia = nLastRow+1;
				var cTableRow  = cGrid.insertRow(nLastRow);

				var cSeq_DOS      = 'cSeq_DOS'      + nSecuencia; // Secuencia del DO
				var cSucId_DOS    = 'cSucId_DOS'    + nSecuencia; // Hidden: Sucursal del DO
			  var cDosNro_DOS   = 'cDosNro_DOS'   + nSecuencia; // Numero del DO
				var cDosSuf_DOS   = 'cDosSuf_DOS'   + nSecuencia; // Hidden: Sufijo del DO
				var cDosTip_DOS   = 'cDosTip_DOS'   + nSecuencia; // Tipo de Operacion del Tramite
				var cDosMtr_DOS   = 'cDosMtr_DOS'   + nSecuencia; // Modo de Transporte del Tramite
			  var cDosFec_DOS   = 'cDosFec_DOS'   + nSecuencia; // Fecha de Apertura del Tramite
			  var cDosPed_DOS   = 'cDosPed_DOS'   + nSecuencia; // Pedido del Cliente
			  var nDosVlr_DOS   = 'nDosVlr_DOS'   + nSecuencia; // Valor de los Tributos para el caso de IMPORTACION
				var cDosFor_DOS   = 'cDosFor_DOS'   + nSecuencia; // Switch para Saber si Aplican Formularios
			  var cDosRec_DOS   = 'cDosRec_DOS'   + nSecuencia; // Switch para Saber si Aplican Horas de Reconocimiento
			  var cDosCE_DOS    = 'cDosCE_DOS'    + nSecuencia; // Switch para Saber si Aplican Condiciones Especiales, link y enter
				var oBtnDos_DOS   = 'oBtnDos_DOS'   + nSecuencia; // Boton de Borrar Row

				var TD_xAll = cTableRow.insertCell(0);
				TD_xAll.style.width  = "40px";
				TD_xAll.style.border = "1px solid #E6E6E6";
				TD_xAll.innerHTML    = "<input type = 'text' Class = 'letra' style = 'width:40;border:0;text-align:center' name = "+cSeq_DOS+" id = "+cSeq_DOS+" value = '"+f_Str_Pad(nSecuencia,3,"0","STR_PAD_LEFT")+"' readonly>";

				TD_xAll = cTableRow.insertCell(1);
				TD_xAll.style.width  = "160px";
				TD_xAll.style.border = "1px solid #E6E6E6";
				TD_xAll.innerHTML    = "<input type = 'hidden' name = "+cSucId_DOS+" id = "+cSucId_DOS+" readonly>"+
														   "<input type = 'text'   Class = 'letra' style = 'width:160;border:0;text-align:center' name = "+cDosNro_DOS+" id = "+cDosNro_DOS+" maxlength='20' "+
					                    	"onBlur  = 'javascript:this.value=this.value.toUpperCase();"+
						                   "f_Links(\"cDosNro_DOS\",\"VALID\",\""+nSecuencia+"\")'>";

				TD_xAll = cTableRow.insertCell(2);
				TD_xAll.style.width = "40px";
				TD_xAll.style.border= "1px solid #E6E6E6";
				TD_xAll.innerHTML   = "<input type = 'text' Class = 'letra' style = 'width:40;border:0;text-align:center' name = "+cDosSuf_DOS+" id = "+cDosSuf_DOS+" readonly>";

				TD_xAll = cTableRow.insertCell(3);
				TD_xAll.style.width           = "120px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.className       = "letra";
				TD_xAll.style.textAlign       = "center";
				TD_xAll.style.fontWeight      = "bold";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = cDosTip_DOS;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(4);
				TD_xAll.style.width           = "120px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.className       = "letra";
				TD_xAll.style.textAlign       = "center";
				TD_xAll.style.fontWeight      = "bold";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = cDosMtr_DOS;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(5);
				TD_xAll.style.width           = "80px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.className       = "letra";
				TD_xAll.style.textAlign       = "center";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = cDosFec_DOS;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(6);
				TD_xAll.style.width  = "100px";
				TD_xAll.style.border = "1px solid #E6E6E6";
				TD_xAll.innerHTML    = "<input type = 'text' Class = 'letra' style = 'width:100;border:0;text-align:center' name = "+cDosPed_DOS+" id = "+cDosPed_DOS+" readonly>";

				TD_xAll = cTableRow.insertCell(7);
				TD_xAll.style.width           = "100px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.className       = "letra";
				TD_xAll.style.textAlign       = "right";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = nDosVlr_DOS;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(8);
				TD_xAll.style.width           = "60px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.className       = "letra";
				TD_xAll.style.textAlign       = "center";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = cDosFor_DOS;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(9);
				TD_xAll.style.width           = "60px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.className       = "letra";
				TD_xAll.style.textAlign       = "center";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = cDosRec_DOS;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(10);
				TD_xAll.style.width  = "40px";
				TD_xAll.style.border = "1px solid #E6E6E6";
				TD_xAll.innerHTML    = "<input type = 'text'   Class = 'letra' style = 'width:40;border:0;text-align:center'  name = "+cDosCE_DOS+"  id = "+cDosCE_DOS+" "+
														   "onKeyUp = 'javascript:f_Enter(event,this.name,\"Grid_Tramites\");'"+
														   "onDblClick = 'javascript:f_Ver_Condiciones_Especiales_x_DO(document.forms[\"frgrm\"][\"cSucId_DOS\"+"+nSecuencia+"].value,document.forms[\"frgrm\"][\"cDosNro_DOS\"+"+nSecuencia+"].value,document.forms[\"frgrm\"][\"cDosSuf_DOS\"+"+nSecuencia+"].value);' readonly>";

				TD_xAll = cTableRow.insertCell(11);
				TD_xAll.innerHTML = "<input type = 'button' Class = 'letra' style = 'width:20;text-align:center' id = "+oBtnDos_DOS+" value = 'X' "+
														  "onClick = 'javascript:f_Delete_Row(this.value,\""+nSecuencia+"\",\"Grid_Tramites\");'>";

				document.forms['frgrm']['nSecuencia_Dos'].value = nSecuencia;
			}

			function f_Add_New_Row_PCCA() {
				var cGrid      = document.getElementById("Grid_PCCA");
				var nLastRow   = cGrid.rows.length;
				var nSecuencia = nLastRow+1;
				var cTableRow  = cGrid.insertRow(nLastRow);

			  var cComSeq_PCCA    = 'cComSeq_PCCA'   + nSecuencia; // Hidden: Secuencia del Comprobante
			  var cComId_PCCA     = 'cComId_PCCA'    + nSecuencia; // Id del Comprobante
			  var cComObs_PCCA    = 'cComObs_PCCA'   + nSecuencia; // Descripcion del Pago a Tercero
			  var cComTra_PCCA    = 'cComTra_PCCA'   + nSecuencia; // Tramite
			  var cComId3_PCCA    = 'cComId3_PCCA'   + nSecuencia; // Id Comprobante
			  var cComCod3_PCCA   = 'cComCod3_PCCA'  + nSecuencia; // Codigo Comprobante
			  var cComCsc3_PCCA   = 'cComCsc3_PCCA'  + nSecuencia; // Consecutivo Comprobante
			  var cComSeq3_PCCA   = 'cComSeq3_PCCA'  + nSecuencia; // Secuencia Comprobante
			  var cComDocIn_PCCA  = 'cComDocIn_PCCA' + nSecuencia; // Documento Informativo
			  var nComVlr_PCCA    = 'nComVlr_PCCA'   + nSecuencia; // Valor del Comprobante
			  var nComVlrNF_PCCA  = 'nComVlrNF_PCCA' + nSecuencia; // Valor del Comprobante en Ejecucion NIFF
			  var cComMov_PCCA    = 'cComMov_PCCA'   + nSecuencia; // Movimiento del Comprobante

			  TD_xAll = cTableRow.insertCell(0);
				TD_xAll.style.width           = "80px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.textAlign       = "center";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = cComId_PCCA;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(1);
				TD_xAll.style.width  = "300px";
				TD_xAll.style.border = "1px solid #E6E6E6";
				TD_xAll.innerHTML    = "<input type = 'hidden' name = "+cComSeq_PCCA+" id = "+cComSeq_PCCA+" value = "+f_Str_Pad(nSecuencia,3,"0","STR_PAD_LEFT")+" readonly>";
				TD_xAll.innerHTML   += "<input type = 'text' Class = 'letra' style = 'width:300;border:0' name = "+cComObs_PCCA+" id = "+cComObs_PCCA+" readonly>";

				TD_xAll = cTableRow.insertCell(2);
				TD_xAll.style.width           = "140px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.textAlign       = "center";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = cComTra_PCCA;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(3);
				TD_xAll.style.width           = "20px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.color           = "#FF0000";
				TD_xAll.style.fontWeight      = "bold";
				TD_xAll.style.textAlign       = "center";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = cComId3_PCCA;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(4);
				TD_xAll.style.width           = "40px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.color           = "#FF0000";
				TD_xAll.style.fontWeight      = "bold";
				TD_xAll.style.textAlign       = "center";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = cComCod3_PCCA;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(5);
				TD_xAll.style.width  = "80px";
				TD_xAll.style.border = "1px solid #E6E6E6";
				TD_xAll.innerHTML    = "<input type = 'text' Class = 'letra' style = 'width:080;border:0;color:#FF0000;font-weight:bold;text-align:right' name = "+cComCsc3_PCCA+" id = "+cComCsc3_PCCA+" maxlength='10' readonly>";

				TD_xAll = cTableRow.insertCell(6);
				TD_xAll.style.width           = "40px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.color           = "#FF0000";
				TD_xAll.style.fontWeight      = "bold";
				TD_xAll.style.textAlign       = "center";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = cComSeq3_PCCA;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(7);
				TD_xAll.style.width           = "60px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.color           = "#FF0000";
				TD_xAll.style.textAlign       = "center";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = cComDocIn_PCCA;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(8);
				TD_xAll.style.width           = "80px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.textAlign       = "right";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = nComVlr_PCCA;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(9);
				TD_xAll.style.width           = "80px";
				TD_xAll.style.border          = "1px solid #E6E6E6";
				TD_xAll.style.backgroundColor = "#FFFFFF";
				TD_xAll.style.fontFamily      = "arial";
				TD_xAll.style.fontSize        = "8pt";
				TD_xAll.style.textAlign       = "right";
				TD_xAll.style.padding         = "2px 2px 2px 2px";
				TD_xAll.id                    = nComVlrNF_PCCA;
				TD_xAll.innerHTML             = "&nbsp;";

				TD_xAll = cTableRow.insertCell(10);
				TD_xAll.style.width  = "20px";
				TD_xAll.style.border = "1px solid #E6E6E6";
				TD_xAll.innerHTML    = "<input type = 'text' Class = 'letra' style = 'width:020;border:0;text-align:center' name = "+cComMov_PCCA+" id = "+cComMov_PCCA+" readonly>";

				document.forms['frgrm']['nSecuencia_PCCA'].value = nSecuencia;

				if ("<?php echo $vSysStr['financiero_editar_consecutivo_tres_factura'] ?>" == "SI") {
					document.forms['frgrm']['cComCsc3_PCCA'+nSecuencia].readOnly = false;
					document.forms['frgrm']['cComCsc3_PCCA'+nSecuencia].onblur  = function () { f_makeRequest("CSC3PCC",nSecuencia); }
				} else {
					document.forms['frgrm']['cComCsc3_PCCA'+nSecuencia].readOnly = true;
					document.forms['frgrm']['cComCsc3_PCCA'+nSecuencia].onblur   = "";
				}
			}

			function f_Add_New_Row_IPA() {
				var cGrid       = document.getElementById("Grid_IPA");
				var nLastRow    = cGrid.rows.length;
				var nSecuencia  = nLastRow+1;
				var cTableRow   = cGrid.insertRow(nLastRow);

				var cComSeq_IPA   = 'cComSeq_IPA'   + nSecuencia; // Hidden: Secuencia del Comprobante
			  var cComId_IPA    = 'cComId_IPA'    + nSecuencia; // Id del Comprobante
			  var cComObs_IPA   = 'cComObs_IPA'   + nSecuencia; // Descripcion del Concepto de Ingresos Propios
			  var cComTra_IPA   = 'cComTra_IPA'   + nSecuencia; // Tramite
        var nComCan_IPA   = 'nComCan_IPA'   + nSecuencia; // Cantidad del Ingreso
			  var nComVlrU_IPA  = 'nComVlrU_IPA'  + nSecuencia; // Valor Unitario del Ingreso
        var nComVlr_IPA   = 'nComVlr_IPA'   + nSecuencia; // Valor del Ingreso
			  var nComVlrNF_IPA = 'nComVlrNF_IPA' + nSecuencia; // Valor del Ingreso en ejecucion NIFF
			  var cComMov_IPA   = 'cComMov_IPA'   + nSecuencia; // Movimiento del Ingreso
			  var cPucDet_IPA   = 'cPucDet_IPA'   + nSecuencia; // Detalle de la cuenta
			  var oBtnDos_IPA   = 'oBtnDos_IPA'   + nSecuencia; // Boton de Borrar Row

		    var TD_xAll = cTableRow.insertCell(0);
				TD_xAll.style.width  = "80px";
				TD_xAll.style.border = "1px solid #E6E6E6";
				TD_xAll.innerHTML    = "<input type = 'hidden' Class = 'letra' style = 'width:080;border:0;text-align:center' name = "+cComSeq_IPA+" id = "+cComSeq_IPA+" value = "+f_Str_Pad(nSecuencia,3,"0","STR_PAD_LEFT")+" readonly>";
				TD_xAll.innerHTML   += "<input type = 'text'   Class = 'letra' style = 'width:080;border:0;text-align:center' name = "+cComId_IPA+" id = "+cComId_IPA+" maxlength = '10' readonly>";

        if ("<?php echo $cAlfa == "GRUMALCO" || $cAlfa == "TEGRUMALCO" ?>") {
				  var cWidth = (document.forms['frgrm']['cComTFa'].value == "MANUAL") ? "380" : "400";
        } else {
          var cWidth = (document.forms['frgrm']['cComTFa'].value == "MANUAL") ? "520" : "540";
        }
				TD_xAll = cTableRow.insertCell(1);
				TD_xAll.style.width  = cWidth+"px";
				TD_xAll.style.border = "1px solid #E6E6E6";
				TD_xAll.innerHTML    = "<input type = 'text'   Class = 'letra' style = 'width:"+cWidth+";border:0' name = "+cComObs_IPA+" id = "+cComObs_IPA+" readonly>";

				TD_xAll = cTableRow.insertCell(2);
				TD_xAll.style.width  = "140px";
				TD_xAll.style.border = "1px solid #E6E6E6";
				TD_xAll.innerHTML    = "<input type = 'text'   Class = 'letra' style = 'width:140;border:0;color:#FF0000;font-weight:bold;text-align:center' name = "+cComTra_IPA+"  id = "+cComTra_IPA+" maxlength = '21' readonly>";

        $nCol = 3;
        if ("<?php echo $cAlfa == "GRUMALCO" || $cAlfa == "TEGRUMALCO" ?>") {
          TD_xAll = cTableRow.insertCell($nCol);
          TD_xAll.style.width  = "40px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text'   Class = 'letra' style = 'width:080;border:0;text-align:right' name = "+nComCan_IPA+" id = "+nComCan_IPA+" readonly>";
          $nCol++;

          TD_xAll = cTableRow.insertCell($nCol);
          TD_xAll.style.width  = "40px";
          TD_xAll.style.border = "1px solid #E6E6E6";
          TD_xAll.innerHTML    = "<input type = 'text'   Class = 'letra' style = 'width:080;border:0;text-align:right' name = "+nComVlrU_IPA+" id = "+nComVlrU_IPA+" readonly>";
          $nCol++;
        } else {
          TD_xAll.innerHTML   += "<input type = 'hidden' Class = 'letra' style = 'width:080;border:0;text-align:center' name = "+nComCan_IPA+" id = "+nComCan_IPA+" readonly>";
          TD_xAll.innerHTML   += "<input type = 'hidden' Class = 'letra' style = 'width:080;border:0;text-align:center' name = "+nComVlrU_IPA+" id = "+nComVlrU_IPA+" readonly>";
        }

				TD_xAll = cTableRow.insertCell($nCol);
				TD_xAll.style.width  = "80px";
				TD_xAll.style.border = "1px solid #E6E6E6";
				TD_xAll.innerHTML    = "<input type = 'text'   Class = 'letra' style = 'width:080;border:0;text-align:right' name = "+nComVlr_IPA+" id = "+nComVlr_IPA+" readonly>";
        $nCol++;

				TD_xAll = cTableRow.insertCell($nCol);
				TD_xAll.style.width  = "80px";
				TD_xAll.style.border = "1px solid #E6E6E6";
				TD_xAll.innerHTML    = "<input type = 'text'   Class = 'letra' style = 'width:080;border:0;text-align:right' name = "+nComVlrNF_IPA+" id = "+nComVlrNF_IPA+" readonly>";
        $nCol++;

				TD_xAll = cTableRow.insertCell($nCol);
				TD_xAll.style.width  = "20px";
				TD_xAll.style.border = "1px solid #E6E6E6";
				TD_xAll.innerHTML    = "<input type = 'text'   Class = 'letra' style = 'width:020;border:0;text-align:center' name = "+cComMov_IPA+" id = "+cComMov_IPA+ " "+
														   "onKeyUp = 'javascript:"+
																				"if (document.forms[\"frgrm\"][\"cComTFa\"].value == \"MANUAL\") {"+
																					"f_Enter(event,this.name,\"Grid_IPA\");"+
																				"}' readonly>";
				TD_xAll.innerHTML   += "<input type = 'hidden' name = "+cPucDet_IPA+"  id = "+cPucDet_IPA+" readonly>";
        $nCol++;

				if (document.forms['frgrm']['cComTFa'].value == "MANUAL") {
					TD_xAll = cTableRow.insertCell($nCol);
					TD_xAll.innerHTML = "<input type = 'button' Class = 'letra' style = 'width:20;text-align:center' id = "+oBtnDos_IPA+" value = 'X' "+
																  "onClick = 'javascript:f_Delete_Row(this.value,\""+nSecuencia+"\",\"Grid_IPA\");'>";
          $nCol++;
				}

				switch(document.forms['frgrm']['cComTFa'].value) {
					case "AUTOMATICA":
						document.forms['frgrm']['cComId_IPA'   +nSecuencia].readOnly = true;
				    document.forms['frgrm']['cComObs_IPA'  +nSecuencia].readOnly = true;
            document.forms['frgrm']['nComCan_IPA'  +nSecuencia].readOnly = true;
            document.forms['frgrm']['nComVlrU_IPA' +nSecuencia].readOnly = true;
				    document.forms['frgrm']['nComVlr_IPA'  +nSecuencia].readOnly = true;
				    document.forms['frgrm']['nComVlrNF_IPA'+nSecuencia].readOnly = true;
				    document.forms['frgrm']['cComObs_IPA'  +nSecuencia].onblur   = "";
					break;
					case "MANUAL":
						document.forms['frgrm']['cComId_IPA'   +nSecuencia].readOnly = false;
				    document.forms['frgrm']['cComObs_IPA'  +nSecuencia].readOnly = false;
            document.forms['frgrm']['nComCan_IPA'  +nSecuencia].readOnly = false;
            document.forms['frgrm']['nComVlrU_IPA' +nSecuencia].readOnly = false;
				    document.forms['frgrm']['nComVlr_IPA'  +nSecuencia].readOnly = false;
				    document.forms['frgrm']['nComVlrNF_IPA'+nSecuencia].readOnly = false;
				    document.forms['frgrm']['cComObs_IPA'  +nSecuencia].onBlur   = function () { f_makeRequest("OBSIPA",nSecuencia); }

				    //funciones para el cComId_IPA
				    document.forms['frgrm']['cComId_IPA'  +nSecuencia].onfocus  = function() { document.forms['frgrm']['nTxtFocus'].value = nSecuencia; }
						document.forms['frgrm']['cComId_IPA'  +nSecuencia].onblur   = function() { this.value=this.value.toUpperCase(); f_Links('cComId_IPA','VALID',nSecuencia); }

				   	//funciones para el cComObs_IPA
					 	document.forms['frgrm']['cComObs_IPA'  +nSecuencia].onfocus  = function() {	document.forms['frgrm']['nTxtFocus'].value = nSecuencia; }
						document.forms['frgrm']['cComObs_IPA'  +nSecuencia].onblur   = function() {	this.value=this.value.toUpperCase(); f_makeRequest("OBSIPA",nSecuencia); }

					  //funciones para el cComTra_IPA
				    document.forms['frgrm']['cComTra_IPA'  +nSecuencia].onfocus  = function() {	document.forms['frgrm']['nTxtFocus'].value = nSecuencia;
				    																																						if (document.forms['frgrm']['cComId_IPA'  +nSecuencia].value == "") {
					    																																						this.value="";
					    																																						alert("Debe Seleccionar un Concepto.");
					    																																						document.forms['frgrm']['cComId_IPA'+nSecuencia].focus();
				    																																						}
				    																																					}
						document.forms['frgrm']['cComTra_IPA'  +nSecuencia].onblur   = function() { this.value=this.value.toUpperCase();
																																												if (document.forms['frgrm']['cComId_IPA'  +nSecuencia].value != "") {
																																													f_Links('cComTra_IPA','WINDOW',nSecuencia);
																																												}
																																											}

						//Verificar la calidad del tercero para permitir decimales
            switch (document.forms['frgrm']['cTerCalInt'].value) {
              case "NORESIDENTE":
                var cTerCal  = document.forms['frgrm']['cTerCalInt'].value;
              break;
              default:
                if ("<?php echo $vSysStr['financiero_facturacion_aplica_impuestos_facturar_a'] ?>" == "SI") {
                  var cTerCal  = document.forms['frgrm']['cTerCalInt'].value;
                } else {
                  var cTerCal  = document.forms['frgrm']['cTerCal'].value;
                }
              break;
            }
				    //Funciones para la Cantidad y valor unitario
            if ("<?php echo $cAlfa == "GRUMALCO" || $cAlfa == "TEGRUMALCO" ?>") {
              document.forms['frgrm']['nComCan_IPA'  +nSecuencia].onKeyUp  = function() { if (document.forms['frgrm']['cComTFa'].value == 'MANUAL') {
                                                                                          this.value = (cTerCal == 'NORESIDENTE') ? f_ValDec(this.value) : Math.round(this.value);
                                                                                        }
                                                                                      }
              document.forms['frgrm']['nComCan_IPA'  +nSecuencia].onfocus  = function() { document.forms['frgrm']['nTxtFocus'].value = nSecuencia; }
              document.forms['frgrm']['nComCan_IPA'  +nSecuencia].onblur   = function() {	this.value = f_ValDec(this.value);
                                                                                          if (document.forms['frgrm']['cComTra_IPA'  +nSecuencia].value != "" && document.forms['frgrm']['cComId_IPA'  +nSecuencia].value != "") {
                                                                                            //Si el valor unitario o el valor total es diferente de vacio se calculan nuevamente los valores
                                                                                            if (eval(document.forms['frgrm']['nComVlrU_IPA'+nSecuencia].value) > 0 || eval(document.forms['frgrm']['nComVlr_IPA'+nSecuencia].value) > 0) {
                                                                                              f_Cuadre_Debitos_Creditos_IPA("CUADREIPA",nSecuencia);
                                                                                            }
                                                                                          } else {
                                                                                            this.value="";
                                                                                          }
                                                                                        }

              document.forms['frgrm']['nComVlrU_IPA' +nSecuencia].onKeyUp  = function() { if (document.forms['frgrm']['cComTFa'].value == 'MANUAL') {
                                                                                          this.value = (cTerCal == 'NORESIDENTE') ? f_ValDec(this.value) : Math.round(this.value);
                                                                                        }
                                                                                      }
              document.forms['frgrm']['nComVlrU_IPA' +nSecuencia].onfocus  = function() { document.forms['frgrm']['nTxtFocus'].value = nSecuencia; }
              document.forms['frgrm']['nComVlrU_IPA' +nSecuencia].onblur   = function() {	this.value = (cTerCal == "NORESIDENTE") ? f_ValDec(this.value) : Math.round(this.value);
                                                                                          if (document.forms['frgrm']['cComTra_IPA'  +nSecuencia].value != "" && document.forms['frgrm']['cComId_IPA'  +nSecuencia].value != "") {
                                                                                            f_Cuadre_Debitos_Creditos_IPA("CUADREIPA",nSecuencia,'nComVlrU_IPA');
                                                                                          } else {
                                                                                            this.value="";
                                                                                          }
                                                                                        }
            }
            //funciones para el nComVlr_IPA
				    document.forms['frgrm']['nComVlr_IPA'  +nSecuencia].onKeyUp  = function() { if (document.forms['frgrm']['cComTFa'].value == 'MANUAL') {
                                                                                          this.value = (cTerCal == 'NORESIDENTE') ? f_ValDec(this.value) : Math.round(this.value);
                                                                                        }
                                                                                      }
						document.forms['frgrm']['nComVlr_IPA'  +nSecuencia].onfocus  = function() { document.forms['frgrm']['nTxtFocus'].value = nSecuencia; }
						document.forms['frgrm']['nComVlr_IPA'  +nSecuencia].onblur   = function() {	this.value = (cTerCal == "NORESIDENTE") ? f_ValDec(this.value) : Math.round(this.value);
																																												if (document.forms['frgrm']['cComTra_IPA'  +nSecuencia].value != "" && document.forms['frgrm']['cComId_IPA'  +nSecuencia].value != "") {
																																													f_Cuadre_Debitos_Creditos_IPA("CUADREIPA",nSecuencia,'nComVlr_IPA');
																																												} else {
																																													this.value="";
																																												}
																																											}

				    //funciones para el nComVlrNF_IPA
				    document.forms['frgrm']['nComVlrNF_IPA'  +nSecuencia].onKeyUp= function() { if (document.forms['frgrm']['cComTFa'].value == 'MANUAL') {
                                                                                          this.value = (cTerCal == 'NORESIDENTE') ? f_ValDec(this.value) : Math.round(this.value);
                                                                                        }
                                                                                      }
						document.forms['frgrm']['nComVlrNF_IPA'  +nSecuencia].onfocus= function() { document.forms['frgrm']['nTxtFocus'].value = nSecuencia; }
						document.forms['frgrm']['nComVlrNF_IPA'  +nSecuencia].onblur = function() {	this.value = (cTerCal == "NORESIDENTE") ? f_ValDec(this.value) : Math.round(this.value);
																																												if (document.forms['frgrm']['cComTra_IPA'  +nSecuencia].value != "" && document.forms['frgrm']['cComId_IPA'  +nSecuencia].value != "") {
																																													f_Cuadre_Debitos_Creditos_IPA("CUADREIPA",nSecuencia);
																																												} else {
																																													this.value="";
																																												}
																																											}

					break;
				}

				document.forms['frgrm']['nSecuencia_IPA'].value = nSecuencia;
			}

			function fnAsignarValores() {
				//Pasando valores al form que hace submit
				document.forms['frestado']['cTabla_GEN'].value      = document.forms['frgrm']['cTabla_GEN'].value;
				document.forms['frestado']['cTabla_DOS'].value      = document.forms['frgrm']['cTabla_DOS'].value;
				document.forms['frestado']['cTabla_PCCA'].value     = document.forms['frgrm']['cTabla_PCCA'].value;
				document.forms['frestado']['cTabla_IPA'].value      = document.forms['frgrm']['cTabla_IPA'].value;
				document.forms['frestado']['cTabla_ANT'].value      = document.forms['frgrm']['cTabla_ANT'].value;
				document.forms['frestado']['cTabla_FAC'].value      = document.forms['frgrm']['cTabla_FAC'].value;
				document.forms['frestado']['cFacId'].value          = document.forms['frgrm']['cFacId'].value;
				document.forms['frestado']['nGrid'].value           = document.forms['frgrm']['nGrid'].value;
				document.forms['frestado']['cStep'].value           = document.forms['frgrm']['cStep'].value;
				document.forms['frestado']['cStep_Ant'].value       = document.forms['frgrm']['cStep_Ant'].value;
				document.forms['frestado']['nSecuencia_Dos'].value  = document.forms['frgrm']['nSecuencia_Dos'].value;
				document.forms['frestado']['nSecuencia_PCCA'].value = document.forms['frgrm']['nSecuencia_PCCA'].value;
				document.forms['frestado']['nSecuencia_IPA'].value  = document.forms['frgrm']['nSecuencia_IPA'].value;
				document.forms['frestado']['cRetenciones'].value    = document.forms['frgrm']['cRetenciones'].value;
				document.forms['frestado']['ckMysqlDb'].value       = document.forms['frgrm']['ckMysqlDb'].value;
				document.forms['frestado']['nCscPro'].value         = document.forms['frgrm']['nCscPro'].value;
				document.forms['frestado']['nTimesSave'].value      = document.forms['frgrm']['nTimesSave'].value;
				document.forms['frestado']['nTxtFocus'].value       = document.forms['frgrm']['nTxtFocus'].value;
				document.forms['frestado']['cCccImp'].value         = document.forms['frgrm']['cCccImp'].value;
				document.forms['frestado']['cComId'].value          = document.forms['frgrm']['cComId'].value;
				document.forms['frestado']['cComCod'].value         = document.forms['frgrm']['cComCod'].value;
				document.forms['frestado']['cComCsc2'].value        = document.forms['frgrm']['cComCsc2'].value;
				document.forms['frestado']['cSucId'].value          = document.forms['frgrm']['cSucId'].value;
				document.forms['frestado']['cCcoId'].value          = document.forms['frgrm']['cCcoId'].value;
				document.forms['frestado']['cResId'].value          = document.forms['frgrm']['cResId'].value;
				document.forms['frestado']['cResPre'].value         = document.forms['frgrm']['cResPre'].value;
				document.forms['frestado']['cResTip'].value         = document.forms['frgrm']['cResTip'].value;
				document.forms['frestado']['cSucFId'].value         = document.forms['frgrm']['cSucFId'].value;
				document.forms['frestado']['nPorIca'].value         = document.forms['frgrm']['nPorIca'].value;
				document.forms['frestado']['cPucIca'].value         = document.forms['frgrm']['cPucIca'].value;
				document.forms['frestado']['nPorAIca'].value        = document.forms['frgrm']['nPorAIca'].value;
				document.forms['frestado']['cPucAIca'].value        = document.forms['frgrm']['cPucAIca'].value;
				document.forms['frestado']['cRegEst'].value         = document.forms['frgrm']['cRegEst'].value;
				document.forms['frestado']['cComDes'].value         = document.forms['frgrm']['cComDes'].value;
				document.forms['frestado']['cComTFa'].value         = document.forms['frgrm']['cComTFa'].value;
				document.forms['frestado']['cComTipCsc'].value      = document.forms['frgrm']['cComTipCsc'].value;
				document.forms['frestado']['dRegFCre'].value        = document.forms['frgrm']['dRegFCre'].value;
				document.forms['frestado']['cComCsc'].value         = document.forms['frgrm']['cComCsc'].value;
				document.forms['frestado']['cComTCo'].value         = document.forms['frgrm']['cComTCo'].value;
				document.forms['frestado']['cComFCA'].value         = document.forms['frgrm']['cComFCA'].value;
				document.forms['frestado']['cComTCD'].value         = document.forms['frgrm']['cComTCD'].value;
        document.forms['frestado']['cMonId'].value          = document.forms['frgrm']['cMonId'].value;
        document.forms['frestado']['cForImp'].value         = document.forms['frgrm']['cForImp'].value;
				document.forms['frestado']['cComObs'].value         = document.forms['frgrm']['cComObs'].value;
				document.forms['frestado']['cOrdenCompra'].value    = document.forms['frgrm']['cOrdenCompra'].value;
				//Datos adicionales openEtl
				document.forms['frestado']['cComTdoc'].value        = document.forms['frgrm']['cComTdoc'].value;
				document.forms['frestado']['cTopId'].value        	= document.forms['frgrm']['cTopId'].value;
				document.forms['frestado']['cComFpag'].value        = document.forms['frgrm']['cComFpag'].value;
				document.forms['frestado']['cMePagId'].value        = document.forms['frgrm']['cMePagId'].value;
				document.forms['frestado']['cMePagDes'].value       = document.forms['frgrm']['cMePagDes'].value;
				//Datos adicionales openEtl
				document.forms['frestado']['cTarEst'].value         = document.forms['frgrm']['cTarEst'].value;
				document.forms['frestado']['cTerTip'].value         = document.forms['frgrm']['cTerTip'].value;
				document.forms['frestado']['cTerId'].value          = document.forms['frgrm']['cTerId'].value;
				document.forms['frestado']['cTerId_Ant'].value      = document.forms['frgrm']['cTerId_Ant'].value;
				document.forms['frestado']['cTerCal'].value         = document.forms['frgrm']['cTerCal'].value;
				document.forms['frestado']['cTerRSt'].value         = document.forms['frgrm']['cTerRSt'].value;
				document.forms['frestado']['cTerRFte'].value        = document.forms['frgrm']['cTerRFte'].value;
				document.forms['frestado']['cTerRCre'].value        = document.forms['frgrm']['cTerRCre'].value;
				document.forms['frestado']['cTerCInt'].value        = document.forms['frgrm']['cTerCInt'].value;
				document.forms['frestado']['cTerRIca'].value        = document.forms['frgrm']['cTerRIca'].value;
				document.forms['frestado']['cTerAIva'].value        = document.forms['frgrm']['cTerAIva'].value;
				document.forms['frestado']['cTerAIf'].value         = document.forms['frgrm']['cTerAIf'].value;
				document.forms['frestado']['cTerSIca'].value        = document.forms['frgrm']['cTerSIca'].value;
				document.forms['frestado']['cTerDV'].value          = document.forms['frgrm']['cTerDV'].value;
				document.forms['frestado']['cTerNom'].value         = document.forms['frgrm']['cTerNom'].value;
				document.forms['frestado']['cBlur'].value           = document.forms['frgrm']['cBlur'].value;
				document.forms['frestado']['nTasaCambio'].value     = document.forms['frgrm']['nTasaCambio'].value;
				document.forms['frestado']['dFechaProm'].value      = document.forms['frgrm']['dFechaProm'].value; //Fecha Promulgacion
				document.forms['frestado']['cTerIdInt'].value       = document.forms['frgrm']['cTerIdInt'].value;
				document.forms['frestado']['cTerCalInt'].value      = document.forms['frgrm']['cTerCalInt'].value;
				document.forms['frestado']['cTerRStInt'].value      = document.forms['frgrm']['cTerRStInt'].value;
				document.forms['frestado']['cTerRFteInt'].value     = document.forms['frgrm']['cTerRFteInt'].value;
				document.forms['frestado']['cTerRCreInt'].value     = document.forms['frgrm']['cTerRCreInt'].value;
				document.forms['frestado']['cTerCIntInt'].value     = document.forms['frgrm']['cTerCIntInt'].value;
				document.forms['frestado']['cTerRIcaInt'].value     = document.forms['frgrm']['cTerRIcaInt'].value;
				document.forms['frestado']['cTerAIvaInt'].value     = document.forms['frgrm']['cTerAIvaInt'].value;
				document.forms['frestado']['cTerAIfInt'].value      = document.forms['frgrm']['cTerAIfInt'].value;
				document.forms['frestado']['cTerSIcaInt'].value     = document.forms['frgrm']['cTerSIcaInt'].value;
				document.forms['frestado']['cTerDVInt'].value       = document.forms['frgrm']['cTerDVInt'].value;
				document.forms['frestado']['cTerNomInt'].value      = document.forms['frgrm']['cTerNomInt'].value;
				document.forms['frestado']['cBlur2'].value          = document.forms['frgrm']['cBlur2'].value;
				document.forms['frestado']['cComCon'].value         = document.forms['frgrm']['cComCon'].value;
				document.forms['frestado']['cTerDir'].value         = document.forms['frgrm']['cTerDir'].value;
				document.forms['frestado']['cTerTel'].value         = document.forms['frgrm']['cTerTel'].value;
				document.forms['frestado']['cTerFax'].value         = document.forms['frgrm']['cTerFax'].value;
				document.forms['frestado']['cTerPla'].value         = document.forms['frgrm']['cTerPla'].value;
				document.forms['frestado']['cTerEma'].value         = document.forms['frgrm']['cTerEma'].value;
				document.forms['frestado']['cTerAnt'].value         = document.forms['frgrm']['cTerAnt'].value;
				document.forms['frestado']['cTerGru'].value         = document.forms['frgrm']['cTerGru'].value;
				document.forms['frestado']['cCccAIF'].value         = document.forms['frgrm']['cCccAIF'].value;
				document.forms['frestado']['cCccIFA'].value         = document.forms['frgrm']['cCccIFA'].value;
				document.forms['frestado']['cFactura'].value        = document.forms['frgrm']['cFactura'].value;
				document.forms['frestado']['cProtocolo'].value      = document.forms['frgrm']['cProtocolo'].value;
				document.forms['frestado']['cCodCobro'].value       = document.forms['frgrm']['cCodCobro'].value;
				document.forms['frestado']['cJobs'].value           = document.forms['frgrm']['cJobs'].value;
				document.forms['frestado']['cRegistro'].value       = document.forms['frgrm']['cRegistro'].value;
				document.forms['frestado']['cDeclaracion'].value    = document.forms['frgrm']['cDeclaracion'].value;
				document.forms['frestado']['cContenido'].value      = document.forms['frgrm']['cContenido'].value;
				document.forms['frestado']['cTasaCamnbio'].value    = document.forms['frgrm']['cTasaCamnbio'].value;
				document.forms['frestado']['nPCCAnt'].value         = document.forms['frgrm']['nPCCAnt'].value;
				document.forms['frestado']['nPCCAntTC'].value       = document.forms['frgrm']['nPCCAntTC'].value;
				document.forms['frestado']['nPCCDeb'].value         = document.forms['frgrm']['nPCCDeb'].value;
				document.forms['frestado']['nPCCCre'].value         = document.forms['frgrm']['nPCCCre'].value;
				document.forms['frestado']['nPCCVNe'].value         = document.forms['frgrm']['nPCCVNe'].value;
				document.forms['frestado']['nIPASub'].value         = document.forms['frgrm']['nIPASub'].value;
				document.forms['frestado']['nIPAIva'].value         = document.forms['frgrm']['nIPAIva'].value;
				document.forms['frestado']['nIPATot'].value         = document.forms['frgrm']['nIPATot'].value;
				document.forms['frestado']['nIPARFte'].value        = document.forms['frgrm']['nIPARFte'].value;
				document.forms['frestado']['nIPARCre'].value        = document.forms['frgrm']['nIPARCre'].value;
				document.forms['frestado']['nIPARIva'].value        = document.forms['frgrm']['nIPARIva'].value;
				document.forms['frestado']['nIPARIca'].value        = document.forms['frgrm']['nIPARIca'].value;
				document.forms['frestado']['nIPAARFte'].value       = document.forms['frgrm']['nIPAARFte'].value;
				document.forms['frestado']['nIPAARCre'].value       = document.forms['frgrm']['nIPAARCre'].value;
				document.forms['frestado']['nIPAARIca'].value       = document.forms['frgrm']['nIPAARIca'].value;
				document.forms['frestado']['nIPAAnt'].value         = document.forms['frgrm']['nIPAAnt'].value;
				document.forms['frestado']['cComSal'].value         = document.forms['frgrm']['cComSal'].value;
				document.forms['frestado']['nIPASal'].value         = document.forms['frgrm']['nIPASal'].value;
			}

			function fnPegarDo() {
				var nX    = screen.width;
				var nY    = screen.height;
				var nAncho = 550;
				var nAlto  = 250;
				var nNx      = (nX-nAncho)/2;
				var nNy      = (nY-nAlto)/2;
				var cWinOpt  = "width="+nAncho+",scrollbars=1,height="+nAlto+",left="+nNx+",top="+nNy;
				var cPathUrl = "frfacfrm.php?gFunction=PegarDo"+
																 "&gArchivo=frfaccpd.php"+
				                         "&gTabla_GEN="+document.forms['frgrm']['cTabla_GEN'].value+
				                         "&gTabla_DOS="+document.forms['frgrm']['cTabla_DOS'].value+
				                         "&gFacId="+document.forms['frgrm']['cFacId'].value+
				                         "&gSecuencia="+document.forms['frgrm']['nSecuencia_Dos'].value;
				//alert(xSwitch + " -> " + cPathUrl);
				cWindow = window.open(cPathUrl,"PegarDo",cWinOpt);
	  		cWindow.focus();
			}

			function fnBorrarGrilla(xTabla) {

				//Borrando tabla de DOs
				var cTable = document.getElementById(xTabla);
			  var nLastRow = cTable.rows.length;
			  for (i=0;i<nLastRow;i++) {
			  	cTable.deleteRow(cTable.rows.length - 1);
			  }

				switch(xTabla) {
					case "Grid_Tramites":
						document.forms['frgrm']['nSecuencia_Dos'].value = 0;
				  	f_Add_New_Row_Tramites();
					break;
					case "Grid_IPA":
						document.forms['frgrm']['nSecuencia_IPA'].value = 0;
				  	f_Add_New_Row_IPA();
					break;
					case "Grid_Tramites_Observaciones":
						document.getElementById("Tramites_Observaciones").style.display="none";
					break;
				}
			}

			function fnGuardar() {
				//Funcion para guardar, antes debe realizar cuadre de debitos y creditos,
				//se envia la secuencia de la ultima fila modificada
				document.getElementById("imgLoad").style.display="block";
				document.getElementById('Btn_Guardar').disabled=true;
				document.forms['frgrm']['cTerTip'].disabled=false;
				document.forms['frgrm']['cStep'].value = '4';
				document.forms['frgrm']['cStep_Ant'].value = '3';
				document.forms['frgrm']['nTimesSave'].value++;

				if (document.forms['frgrm']['cComTFa'].value == "MANUAL") {
					var nSecuencia = document.forms['frgrm']['nTxtFocus'].value;

					var cRuta = "frfacipg.php?"+
											"cModo=GUARDAR"  +
											"&cParent=VALID" +
											"&cForm=frfacgra.php" +
						          "&cFacId="       +document.forms['frgrm']['cFacId'].value+
						          "&cTabla_GEN="   +document.forms['frgrm']['cTabla_GEN'].value+
						          "&cTabla_DOS="   +document.forms['frgrm']['cTabla_DOS'].value+
						          "&cTabla_IPA="   +document.forms['frgrm']['cTabla_IPA'].value+
						          "&cTabla_PCCA="  +document.forms['frgrm']['cTabla_PCCA'].value+
						          "&nPCCVNe="      +document.forms['frgrm']['nPCCVNe'].value+
						          "&nIPAAnt="      +document.forms['frgrm']['nIPAAnt'].value+
						          "&nSecuencia="   +nSecuencia+
						          "&nComVlr_IPA="  +document.forms['frgrm']['nComVlr_IPA'+nSecuencia].value+
						          "&nComVlrNF_IPA="+document.forms['frgrm']['nComVlrNF_IPA'+nSecuencia].value;

					//alert(cRuta);
					parent.fmpro2.location = cRuta;
				} else {
					fnAsignarValores();
					document.forms['frestado']['cModo'].value = "NUEVO";
					document.forms['frestado'].action='frfacgra.php';
					document.forms['frestado'].target='fmpro';
					document.forms['frestado'].submit();
					document.forms['frgrm']['cTerTip'].disabled=true;
				}
			}

			function fnVistaPrevia() {

			  var cRuta = '';

				switch(document.forms['frgrm']['ckMysqlDb'].value){
					case 'UPSXXXXX': case 'TEUPSXXXXX': case 'DEUPSXXXXX': cRuta='frupspre.php'; break;
					case 'GRUPOGLA': case 'TEGRUPOGLA': case 'DEGRUPOGLA': cRuta='frglapre.php'; break;
					case 'ADUACARX': case 'TEADUACARX': case 'DEADUACARX': cRuta='frfacpre.php'; break;
					case 'INTERLOG': case 'DEINTERLOG': case 'TEINTERLOG': cRuta='frfaipre.php'; break;
					case 'ALPOPULX': case 'TEALPOPULP': case 'TEALPOPULX': case 'DEALPOPULX':
						if( document.forms['frgrm']['cCccImp'].value == 'NORMAL'){
							cRuta='frfalpre.php';
						}else{
							cRuta='frdptpre.php';
						}
					break;
					case 'ETRANSPT': case 'TEETRANSPT': case 'DEETRANSPT': cRuta='frdlipre.php'; break;
					case 'COLMASXX': case 'TECOLMASXX': case 'DECOLMASXX': cRuta='frcolpre.php'; break;
					case 'ADUANAMI': case 'TEADUANAMI': case 'DEADUANAMI': cRuta='fradupre.php'; break;
					case 'INTERLO2': case 'TEINTERLO2': case 'DEINTERLO2': cRuta='frintpre.php'; break;
					case 'SIACOSIA': case 'TESIACOSIP': case 'DESIACOSIP': cRuta='frsiapre.php'; break;
					case 'MIRCANAX': case 'TEMIRCANAX': case 'DEMIRCANAX': cRuta='frmirpre.php'; break;
					case 'ADUANAMO': case 'TEADUANAMO': case 'DEADUANAMO': cRuta='framopre.php'; break;
					case 'SUPPORTX': case 'TESUPPORTX': case 'DESUPPORTX': cRuta='frsuppre.php'; break;
					case 'LIDERESX': case 'TELIDERESX': case 'DELIDERESX': cRuta='frlidpre.php'; break;
					case 'ACODEXXX': case 'TEACODEXXX': case 'DEACODEXXX': cRuta='fracnpre.php'; break;
					case 'LOGISTSA': case 'TELOGISTSA': case 'DELOGISTSA': cRuta='frloipre.php'; break;
					case 'LOGINCAR': case 'TELOGINCAR': case 'DELOGINCAR': cRuta='frlogpre.php'; break;
					case 'TRLXXXXX': case 'TETRLXXXXX': case 'DETRLXXXXX': cRuta='frbmapre.php'; break;
					case 'ADIMPEXX': case 'TEADIMPEXX': case 'DEADIMPEXX': cRuta='fradipre.php'; break;
					case 'ROLDANLO': case 'TEROLDANLO': case 'DEROLDANLO': cRuta='frrolpre.php'; break;
					case 'ALMAVIVA': case 'TEALMAVIVA': case 'DEALMAVIVA': cRuta='fralmpre.php'; break;
					case 'CASTANOX': case 'TECASTANOX': case 'DECASTANOX': cRuta='frcaspre.php'; break;
					case 'ALMACAFE': case 'TEALMACAFE': case 'DEALMACAFE': cRuta='fralcpre.php'; break;
					case 'CARGOADU': case 'TECARGOADU': case 'DECARGOADU': cRuta='frcadpre.php'; break;
					case 'GRUMALCO': case 'TEGRUMALCO': case 'DEGRUMALCO': cRuta='frmalpre.php'; break;
					case 'ANDINOSX': case 'TEANDINOSX': case 'DEANDINOSX': cRuta='frandpre.php'; break;
					case 'ALADUANA': case 'TEALADUANA': case 'DEALADUANA': cRuta='fralapre.php'; break;
					case 'GRUPOALC': case 'TEGRUPOALC': case 'DEGRUPOALC': cRuta='frgalpre.php'; break;
					case 'AAINTERX': case 'TEAAINTERX': case 'DEAAINTERX': cRuta='frainpre.php'; break;
					case 'AALOPEZX': case 'TEAALOPEZX': case 'DEAALOPEZX': cRuta='frloppre.php'; break;
					case 'ADUAMARX': case 'TEADUAMARX': case 'DEADUAMARX': cRuta='frmarpre.php'; break;
					case 'OPENEBCO': case 'TEOPENEBCO': case 'DEOPENEBCO': cRuta='fropepre.php'; break;
					case 'SOLUCION': case 'TESOLUCION': case 'DESOLUCION': cRuta='frsolpre.php'; break;
					case 'FENIXSAS': case 'TEFENIXSAS': case 'DEFENIXSAS': cRuta='frfenpre.php'; break;
					case 'INTERLAC': case 'TEINTERLAC': case 'DEINTERLAC': cRuta='frterpre.php'; break;
					case 'COLVANXX': case 'TECOLVANXX': case 'DECOLVANXX': cRuta='frcovpre.php'; break;
					case 'DHLEXPRE': case 'TEDHLEXPRE': case 'DEDHLEXPRE': cRuta='frdhlpre.php'; break;
					case 'KARGORUX': case 'TEKARGORUX': case 'DEKARGORUX': cRuta='frkarpre.php'; break;
					case 'PROSERCO': case 'TEPROSERCO': case 'DEPROSERCO': cRuta='frpropre.php'; break;
					case 'MANATIAL': case 'TEMANATIAL': case 'DEMANATIAL': cRuta='frmanpre.php'; break;
					case 'DSVSASXX': case 'TEDSVSASXX': case 'DEDSVSASXX': cRuta='frdsvpre.php'; break;
					case 'FEDEXEXP': case 'DEFEDEXEXP': case 'TEFEDEXEXP': cRuta='frfedpre.php'; break;
					case 'EXPORCOM': case 'DEEXPORCOM': case 'TEEXPORCOM': cRuta='frexppre.php'; break; 
					case 'HAYDEARX': case 'DEHAYDEARX': case 'TEHAYDEARX': cRuta='frhaypre.php'; break; 
					default: cRuta='frfacpre.php'; break;
				}
			  document.forms['frestado'].action = cRuta;
			  document.forms['frestado'].target = '_blank';

				if (document.forms['frgrm']['cComTFa'].value == "MANUAL") {
					var nSecuencia = document.forms['frgrm']['nTxtFocus'].value;

					var cRuta = "frfacipg.php?"+
											"cModo=VISTAPREVIA"  +
											"&cParent=VALID" +
						          "&cFacId="       +document.forms['frgrm']['cFacId'].value+
						          "&cTabla_GEN="   +document.forms['frgrm']['cTabla_GEN'].value+
						          "&cTabla_DOS="   +document.forms['frgrm']['cTabla_DOS'].value+
						          "&cTabla_IPA="   +document.forms['frgrm']['cTabla_IPA'].value+
						          "&cTabla_PCCA="  +document.forms['frgrm']['cTabla_PCCA'].value+
						          "&nPCCVNe="      +document.forms['frgrm']['nPCCVNe'].value+
						          "&nIPAAnt="      +document.forms['frgrm']['nIPAAnt'].value+
						          "&nSecuencia="   +nSecuencia+
						          "&nComVlr_IPA="  +document.forms['frgrm']['nComVlr_IPA'+nSecuencia].value+
						          "&nComVlrNF_IPA="+document.forms['frgrm']['nComVlrNF_IPA'+nSecuencia].value;

					//alert(cRuta);
					parent.fmpro2.location = cRuta;
				} else {
					fnAsignarValores();
					document.forms['frestado'].submit();
					document.forms['frgrm'].action='frfacnue.php';
					document.forms['frgrm'].target='fmwork';
				}
			}

      function fnCargarTipoFacturaFE(xComId, xComCod, xComTdoc) {
        var cRuta = "frfacfex.php?"+
                    "cModo=TIPOFACTURA"+
                    "&gComId=" +xComId+
                    "&gComCod="+xComCod+
                    "&gComTdoc="+xComTdoc;
        //alert(cRuta); 
        parent.fmpro3.location = cRuta;
      }

			function fnCargarObservaciones() {
				var cRuta = "frfac20g.php?"+
                    "cModo=OBSERVACIONESDO"+
										"&cFacId="       +document.forms['frgrm']['cFacId'].value+
										"&cTabla_GEN="   +document.forms['frgrm']['cTabla_GEN'].value+
										"&cTabla_DOS="   +document.forms['frgrm']['cTabla_DOS'].value;

        parent.fmpro3.location = cRuta;
			}

      function fnBuscarMedioPago() {
        var cRuta = "frfacfex.php?"+
                    "cModo=MEDIOPAGO"+
                    "&gComFpag="+document.forms['frgrm']['cComFpag'].value;
        // alert(cRuta); 
        parent.fmpro3.location = cRuta;
      }
		</script>
	</head>
	<body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
		<!-- PRIMERO PINTO EL FORMULARIO -->
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="980">
				<tr>
					<td>
						<!-- Estos campos son los que se envian al graba y vista previa -->
						<form name = "frestado" action = "frfacgra.php" method = "post" target="fmpro">
							<input type = "hidden" name = "cModo"       	  value = "">
							<input type = "hidden" name = "cOrigen"      	  value = "">
							<input type = "hidden" name = "nSecuencia"  	  value = "">
							<input type = "hidden" name = "nGrid"			  	  value = "">
							<input type = "hidden" name = "cTabla_GEN"      value = "">
							<input type = "hidden" name = "cTabla_DOS"      value = "">
							<input type = "hidden" name = "cTabla_PCCA"     value = "">
							<input type = "hidden" name = "cTabla_IPA"      value = "">
							<input type = "hidden" name = "cTabla_ANT"      value = "">
							<input type = "hidden" name = "cTabla_FAC"      value = "">
							<input type = "hidden" name = "cFacId"          value = "">
							<input type = "hidden" name = "cStep"           value = "">
							<input type = "hidden" name = "cStep_Ant"       value = "">
							<input type = "hidden" name = "nSecuencia_Dos"  value = "">
							<input type = "hidden" name = "nSecuencia_PCCA" value = "">
							<input type = "hidden" name = "nSecuencia_IPA"  value = "">
							<input type = "hidden" name = "cRetenciones"    value = "">
							<input type = "hidden" name = "ckMysqlDb"       value = "">
							<input type = "hidden" name = "nCscPro"         value = "">
							<input type = "hidden" name = "nTimesSave"      value = "">
							<input type = "hidden" name = "nTxtFocus"       value = "">
							<input type = "hidden" name = "cCccImp"         value = "">
							<input type = "hidden" name = "cComId"          value = "">
							<input type = "hidden" name = "cComCod"         value = "">
							<input type = "hidden" name = "cComCsc"         value = "">
							<input type = "hidden" name = "cComCsc2"        value = "">
							<input type = "hidden" name = "cSucId"          value = "">
							<input type = "hidden" name = "cCcoId"          value = "">
							<input type = "hidden" name = "cResId"          value = "">
							<input type = "hidden" name = "cResPre"         value = "">
							<input type = "hidden" name = "cResTip"         value = "">
							<input type = "hidden" name = "cSucFId"         value = "">
							<input type = "hidden" name = "nPorIca"         value = "">
							<input type = "hidden" name = "cPucIca"         value = "">
							<input type = "hidden" name = "nPorAIca"        value = "">
							<input type = "hidden" name = "cPucAIca"        value = "">
							<input type = "hidden" name = "cRegEst"         value = "">
							<input type = "hidden" name = "cComDes"         value = "">
							<input type = "hidden" name = "cComTFa"         value = "">
							<input type = "hidden" name = "cComTipCsc"      value = "">
							<input type = "hidden" name = "dRegFCre"        value = "">
							<input type = "hidden" name = "cComTCo"         value = "">
							<input type = "hidden" name = "cComFCA"         value = "">
							<input type = "hidden" name = "cComTCD"         value = "">
              <input type = "hidden" name = "cMonId"          value = "">
							<input type = "hidden" name = "cForImp"         value = "">
							<input type = "hidden" name = "cComObs"         value = "">
							<input type = "hidden" name = "cOrdenCompra"    value = "">
							<!-- Datos adiconales openEtl -->
							<input type = "hidden" name = "cComTdoc"        value = "">
							<input type = "hidden" name = "cTopId"        	value = "">
							<input type = "hidden" name = "cComFpag"        value = "">
							<input type = "hidden" name = "cMePagId"        value = "">
							<input type = "hidden" name = "cMePagDes"       value = "">
							<!--FIN Datos adiconales openEtl -->
							<input type = "hidden" name = "cTarEst"         value = "">
							<input type = "hidden" name = "cTerTip"         value = "">
							<input type = "hidden" name = "cTerId"          value = "">
							<input type = "hidden" name = "cTerId_Ant"      value = "">
							<input type = "hidden" name = "cTerCal"         value = "">
							<input type = "hidden" name = "cTerRSt"         value = "">
							<input type = "hidden" name = "cTerRFte"        value = "">
							<input type = "hidden" name = "cTerRCre"        value = "">
							<input type = "hidden" name = "cTerCInt"        value = "">
							<input type = "hidden" name = "cTerRIca"        value = "">
							<input type = "hidden" name = "cTerAIva"        value = "">
							<input type = "hidden" name = "cTerAIf"         value = "">
							<input type = "hidden" name = "cTerSIca"        value = "">
							<input type = "hidden" name = "cTerDV"          value = "">
							<input type = "hidden" name = "cTerNom"         value = "">
							<input type = "hidden" name = "cBlur"           value = "">
							<input type = "hidden" name = "nTasaCambio"     value = "">
							<input type = "hidden" name = "dFechaProm"      value = "">
							<input type = "hidden" name = "cTerIdInt"       value = "">
							<input type = "hidden" name = "cTerCalInt"      value = "">
							<input type = "hidden" name = "cTerRStInt"      value = "">
							<input type = "hidden" name = "cTerRFteInt"     value = "">
							<input type = "hidden" name = "cTerRCreInt"     value = "">
							<input type = "hidden" name = "cTerCIntInt"     value = "">
							<input type = "hidden" name = "cTerRIcaInt"     value = "">
							<input type = "hidden" name = "cTerAIvaInt"     value = "">
							<input type = "hidden" name = "cTerAIfInt"      value = "">
							<input type = "hidden" name = "cTerSIcaInt"     value = "">
							<input type = "hidden" name = "cTerDVInt"       value = "">
							<input type = "hidden" name = "cTerNomInt"      value = "">
							<input type = "hidden" name = "cBlur2"          value = "">
							<input type = "hidden" name = "cComCon"         value = "">
							<input type = "hidden" name = "cTerDir"         value = "">
							<input type = "hidden" name = "cTerTel"         value = "">
							<input type = "hidden" name = "cTerFax"         value = "">
							<input type = "hidden" name = "cTerPla"         value = "">
							<input type = "hidden" name = "cTerEma"         value = "">
							<input type = "hidden" name = "cTerAnt"         value = "">
							<input type = "hidden" name = "cTerGru"         value = "">
							<input type = "hidden" name = "cCccAIF"         value = "">
							<input type = "hidden" name = "cCccIFA"         value = "">
              <input type = "hidden" name = "cFactura"        value = "">
							<input type = "hidden" name = "cProtocolo"      value = "">
							<input type = "hidden" name = "cCodCobro"       value = "">
							<input type = "hidden" name = "cJobs"           value = "">
							<input type = "hidden" name = "cRegistro"       value = "">
							<input type = "hidden" name = "cDeclaracion"    value = "">
							<input type = "hidden" name = "cContenido"      value = "">
							<input type = "hidden" name = "cTasaCamnbio"    value = "">
							<input type = "hidden" name = "nPCCAnt"         value = "">
							<input type = "hidden" name = "nPCCAntTC"       value = "">
							<input type = "hidden" name = "nPCCDeb"         value = "">
							<input type = "hidden" name = "nPCCCre"         value = "">
							<input type = "hidden" name = "nPCCVNe"         value = "">
							<input type = "hidden" name = "nIPASub"         value = "">
							<input type = "hidden" name = "nIPAIva"         value = "">
							<input type = "hidden" name = "nIPATot"         value = "">
							<input type = "hidden" name = "nIPARFte"        value = "">
							<input type = "hidden" name = "nIPARCre"        value = "">
							<input type = "hidden" name = "nIPARIva"        value = "">
							<input type = "hidden" name = "nIPARIca"        value = "">
							<input type = "hidden" name = "nIPAARFte"       value = "">
							<input type = "hidden" name = "nIPAARCre"       value = "">
							<input type = "hidden" name = "nIPAARIca"       value = "">
							<input type = "hidden" name = "nIPAAnt"         value = "">
							<input type = "hidden" name = "cComSal"         value = "">
							<input type = "hidden" name = "nIPASal"         value = "">
						</form>

						<form name = "frgrm" action = "frfacgra.php" method = "post" target="fmpro">
							<input type = "hidden" name = "cTabla_GEN"      value = "<?php echo $_POST['cTabla_GEN'] ?>">
							<input type = "hidden" name = "cTabla_DOS"      value = "<?php echo $_POST['cTabla_DOS'] ?>">
							<input type = "hidden" name = "cTabla_PCCA"     value = "<?php echo $_POST['cTabla_PCCA'] ?>">
							<input type = "hidden" name = "cTabla_IPA"      value = "<?php echo $_POST['cTabla_IPA'] ?>">
							<input type = "hidden" name = "cTabla_ANT"      value = "<?php echo $_POST['cTabla_ANT'] ?>">
							<input type = "hidden" name = "cTabla_FAC"      value = "<?php echo $_POST['cTabla_FAC'] ?>">
							<input type = "hidden" name = "cFacId"          value = "<?php echo $_POST['cFacId'] ?>">
							<input type = "hidden" name = "nGrid"           value = "">
							<input type = "hidden" name = "cStep" 					value = "<?php echo $_POST['cStep'] ?>">
							<input type = "hidden" name = "cStep_Ant" 			value = "<?php echo $_POST['cStep_Ant'] ?>"> <!-- Paso anterior de la factura de donde vengo navegando -->
							<input type = "hidden" name = "nSecuencia_Dos"  value = "<?php echo $_POST['nSecuencia_Dos']  ?>">
							<input type = "hidden" name = "nSecuencia_PCCA" value = "<?php echo $_POST['nSecuencia_PCCA'] ?>">
							<input type = "hidden" name = "nSecuencia_IPA"  value = "<?php echo $_POST['nSecuencia_IPA']  ?>">
							<input type = "hidden" name = "cRetenciones"    value = "<?php echo $_POST['cRetenciones']    ?>">
							<input type = "hidden" name = "ckMysqlDb"       value = "<?php echo $kMysqlDb ?>">
							<input type = "hidden" name = "nCscPro"  				value = "<?php echo $_POST['nCscPro']  ?>" readonly> <!-- Consecutivo de Proceso para Metodo 8 de Web Services ALPOPULAR -->
							<input type = "hidden" name = "nTimesSave"      value = "0">
							<input type = "hidden" name = "nTxtFocus"       value = "">
							<input type = "hidden" name = "cCccImp"       	value = "">
							<?php 
              $nIncComTipCsc = 0; //Indica si el combo de tipo de consecutivo debe incluirse o no
              if ((f_InList($cAlfa,"ALMACAFE","TEALMACAFE","DEALMACAFE") || $vSysStr['system_activar_integracion_sap_almaviva'] == "SI") && $_COOKIE['kModo'] == "PEDIDOSAP") { ?>
								<input type = "hidden" name = "cComTipCsc"  value = "DEFINITIVO" readonly>
								<?php 
                $nIncComTipCsc = 1;
                $nCanColM = 31; $nWidColM = 620;
								$nCanColA = 35; $nWidColA = 700;
							} else {
								$nCanColM = 23; $nWidColM = 460;
								$nCanColA = 27; $nWidColA = 540;
							} ?>
							<fieldset>
			   				<legend style="color:#FF0000">Wizard de Facturacion - Paso <?php if($_POST['cStep'] == ""){ echo $_POST['cStep'] = "1";}else{ echo $_POST['cStep'];} ?></legend>

								<fieldset id="Datos_del_Comprobante">
			   					<legend><b>Ciudad y Tipo de Operacion</b></legend>
									<center>
										<table border = "0" cellpadding = "0" cellspacing = "0" width = "940">
											<?php $cCols = f_Format_Cols(47); echo $cCols; ?>
											<tr>
												<td Class = "name" colspan = "<?php echo ($vSysStr['financiero_tipo_consecutivo_facturacion'] == "MANUAL") ? $nCanColM : $nCanColA; ?>">Ciudad<br>
													<input type = "hidden" name = "cComId"   value = "F" readonly>
													<input type = "hidden" name = "cComCod"  value = "<?php echo $_POST['cComCod']  ?>"  readonly>
													<input type = "hidden" name = "cComCsc2" value = "<?php echo $_POST['cComCsc2'] ?>"  readonly>
													<input type = "hidden" name = "cSucId"   value = "<?php echo $_POST['cSucId']   ?>"  readonly>
													<input type = "hidden" name = "cCcoId"   value = "<?php echo $_POST['cCcoId']   ?>"  readonly>
													<input type = "hidden" name = "cResId"   value = "<?php echo $_POST['cResId']   ?>"  readonly>
													<input type = "hidden" name = "cResPre"  value = "<?php echo $_POST['cResPre']   ?>" readonly>
													<input type = "hidden" name = "cResTip"  value = "<?php echo $_POST['cResTip']  ?>"  readonly>
													<input type = "hidden" name = "cSucFId"  value = "<?php echo $_POST['cSucFId']  ?>"  readonly>
													<input type = "hidden" name = "nPorIca"  value = "<?php echo $_POST['nPorIca']  ?>"  readonly>
													<input type = "hidden" name = "cPucIca"  value = "<?php echo $_POST['cPucIca']  ?>"  readonly>
													<input type = "hidden" name = "nPorAIca" value = "<?php echo $_POST['nPorAIca'] ?>"  readonly>
													<input type = "hidden" name = "cPucAIca" value = "<?php echo $_POST['cPucAIca'] ?>"  readonly>
													<input type = "hidden" name = "cRegEst"  value = "ACTIVO" readonly>

													<select Class = "letrase" id = "cComDes" name = "cComDes" value = "<?php echo $_POST['cComDes'] ?>" style = "width:<?php echo ($vSysStr['financiero_tipo_consecutivo_facturacion'] == "MANUAL") ? $nWidColM : $nWidColA; ?>"
	       	       						onchange="javascript:var mComprobantes=this.value.split('~');
																								 document.forms['frgrm']['cComId'].value   = mComprobantes[0];
																								 document.forms['frgrm']['cComCod'].value  = mComprobantes[1];
																								 document.forms['frgrm']['cSucId'].value   = mComprobantes[2];
																								 document.forms['frgrm']['cCcoId'].value   = mComprobantes[3];
																								 document.forms['frgrm']['cResId'].value   = mComprobantes[4];
																								 document.forms['frgrm']['cResPre'].value  = mComprobantes[11];
																								 document.forms['frgrm']['cResTip'].value  = mComprobantes[5];
																								 document.forms['frgrm']['cSucFId'].value  = mComprobantes[6];
																								 document.forms['frgrm']['nPorIca'].value  = mComprobantes[7];
																								 document.forms['frgrm']['cPucIca'].value  = mComprobantes[8];
																								 document.forms['frgrm']['nPorAIca'].value = mComprobantes[9];
																								 document.forms['frgrm']['cPucAIca'].value = mComprobantes[10];
																								 if (document.forms['frgrm']['cResTip'].value == 'SECUNDARIA') {
                        												    document.getElementById('id_href_dRegFCre').href = 'javascript:show_calendar(\'frgrm.dRegFCre\');';
                        												    document.forms['frgrm']['dRegFCre'].value = '<?php echo date("Y-m-d"); ?>';
                        												  } else {
                        												    document.forms['frgrm']['dRegFCre'].value = '<?php echo date("Y-m-d"); ?>';
                        												    document.getElementById('id_href_dRegFCre').href = 'javascript:alert(\'Solo se Permite Cambiar la Fecha Cuando el Tipo de Resolucion es [SECUNDARIA], Verifique.\')';
                        												  }
                                                  fnCargarTipoFacturaFE(document.forms['frgrm']['cComId'].value, document.forms['frgrm']['cComCod'].value, '<?php echo $_POST['cComTdoc'] ?>');">
														<option value = "" selected>[SELECCIONE]</option>
														<?php

															/**
															 * Array auxiliar para almacenar las ciudades para ALMAVIVA
															 * @var array
															 */
															$mCiuFac = array();

															// Busco las resoluciones autorizadas o habilitadas, principales o secundarias que esten activas.
															$qResFac  = "SELECT residxxx,resprexx,restipxx,rescomxx ";
														  $qResFac .= "FROM $cAlfa.fpar0138 ";
														  $qResFac .= "WHERE ";
														  $qResFac .= "regestxx = \"ACTIVO\" ";
														  $qResFac .= "ORDER BY restipxx";
														  $xResTip  = f_MySql("SELECT","",$qResFac,$xConexion01,"");
														  //f_Mensaje(__FILE__,__LINE__,$qResFac." ~ ".mysql_num_rows($xResTip));
														  while ($xRRF = mysql_fetch_array($xResTip)) {
														  	$vComprobantes = f_Explode_Array($xRRF['rescomxx'],"|","~");
														  	for ($i=0;$i<count($vComprobantes);$i++) {
																	if($vComprobantes[$i][0] != "" && $vComprobantes[$i][1] != "") {
															  		// Por cada comprobante del campo vector busco los datos de las sucursal.
															  		$qCiudades  = "SELECT ";
																		$qCiudades .= "fpar0117.comidxxx,";
																		$qCiudades .= "fpar0117.comcodxx,";
																		$qCiudades .= "fpar0008.sucidxxx,";
																		$qCiudades .= "fpar0117.ccoidxxx,";
																		$qCiudades .= "fpar0117.comtdoxx,";
																		$qCiudades .= "fpar0117.comdesxx ";
																		$qCiudades .= "FROM $cAlfa.fpar0117,$cAlfa.fpar0008 ";
																		$qCiudades .= "WHERE ";
																		$qCiudades .= "fpar0117.sucidxxx = fpar0008.sucidxxx AND ";
																		$qCiudades .= "fpar0117.ccoidxxx = fpar0008.ccoidxxx AND ";
																		$qCiudades .= "fpar0117.comidxxx = \"{$vComprobantes[$i][0]}\" AND ";
																		$qCiudades .= "fpar0117.comcodxx = \"{$vComprobantes[$i][1]}\" AND ";
																		if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") {
			  															$qCiudades .= "fpar0117.sucidxxx IN ($cUsrSuc) AND ";
			  														} else {
																			$qCiudades .= "fpar0117.ccoidxxx IN ($cUsrCco) AND ";
																		}
			  														$qCiudades .= "fpar0117.regestxx = \"ACTIVO\" LIMIT 0,1";
																		$xCiudades  = f_MySql("SELECT","",$qCiudades,$xConexion01,"");
																		// f_Mensaje(__FILE__,__LINE__,$qCiudades." ~ ".mysql_num_rows($xCiudades));
																		if (mysql_num_rows($xCiudades) > 0) {
																			$vCiudades  = mysql_fetch_array($xCiudades);

																			// Busco la sucursal y el centro de costo en la tabla SIAI0055 para traer el PAIS, DEPARTAMENTO y CIUDAD.
																	    $qTarifaIca  = "SELECT * ";
																	    $qTarifaIca .= "FROM $cAlfa.SIAI0055 ";
																	    $qTarifaIca .= "WHERE ";
																	    $qTarifaIca .= "SUCIDXXX = \"{$vCiudades['sucidxxx']}\" AND ";
																	    $qTarifaIca .= "CCOIDXXX = \"{$vCiudades['ccoidxxx']}\" AND ";
																	    $qTarifaIca .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
																	    $xTarifaIca  = f_MySql("SELECT","",$qTarifaIca,$xConexion01,"");
																			//f_Mensaje(__FILE__,__LINE__,$qTarifaIca." ~ ".mysql_num_rows($xTarifaIca));
																			$vTarifaIca  = mysql_fetch_array($xTarifaIca);

																			// Si el usuario esta regresando al PASO 1 del wizzard desde el PASO 2, entonces le dejo la ciudad que habia elegido incialmente.
																			if (($vCiudades['comidxxx'] == $_POST['cComId']) && ($vCiudades['comcodxx'] == $_POST['cComCod'])) {
																				/*** para ALMAVIVA se guarda la data en una matriz para luego ser organizada en orden alfabetico las descripciones del combo ***/
																				if ($cAlfa == "ALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "DEALMAVIVA") {

																					$nInd_mCiuFac = count($mCiuFac);
																					$mCiuFac[$nInd_mCiuFac]['comidxxx'] = $vCiudades['comidxxx'];
																					$mCiuFac[$nInd_mCiuFac]['comcodxx'] = $vCiudades['comcodxx'];
																					$mCiuFac[$nInd_mCiuFac]['sucidxxx'] = $vCiudades['sucidxxx'];
																					$mCiuFac[$nInd_mCiuFac]['ccoidxxx'] = $vCiudades['ccoidxxx'];
																					$mCiuFac[$nInd_mCiuFac]['residxxx'] = $xRRF['residxxx'];
																					$mCiuFac[$nInd_mCiuFac]['restipxx'] = $xRRF['restipxx'];
																					$mCiuFac[$nInd_mCiuFac]['SUCIDXXX'] = $vTarifaIca['SUCIDXXX'];
																					$mCiuFac[$nInd_mCiuFac]['CIUICAXX'] = $vTarifaIca['CIUICAXX'];
																					$mCiuFac[$nInd_mCiuFac]['PUCIDXXX'] = $vTarifaIca['PUCIDXXX'];
																					$mCiuFac[$nInd_mCiuFac]['CIUICA2X'] = $vTarifaIca['CIUICA2X'];
																					$mCiuFac[$nInd_mCiuFac]['PUCID2XX'] = $vTarifaIca['PUCID2XX'];
																					$mCiuFac[$nInd_mCiuFac]['resprexx'] = $xRRF['resprexx'];
																					$mCiuFac[$nInd_mCiuFac]['comdesxx'] = $vCiudades['comdesxx']." ".trim($xRRF['restipxx']);
																					$mCiuFac[$nInd_mCiuFac]['selected'] = "SI";

																				} else {
																					?>
																					<option value = "<?php echo $vCiudades['comidxxx']."~".$vCiudades['comcodxx']."~".$vCiudades['sucidxxx']."~".$vCiudades['ccoidxxx']."~".$xRRF['residxxx']."~".$xRRF['restipxx']."~".$vTarifaIca['SUCIDXXX']."~".$vTarifaIca['CIUICAXX']."~".$vTarifaIca['PUCIDXXX']."~".$vTarifaIca['CIUICA2X']."~".$vTarifaIca['PUCID2XX']."~".$xRRF['resprexx'] ?>" selected><?php echo $vCiudades['comdesxx']." ".trim($xRRF['restipxx']) ?></option>
																				<?php }
																			} else {
																			  // Si el usuario esta entrando por primera vez al PASO 1 le dejo por default en el combo de ciudad
																			  // la resolucion [PRINCIPAL] del centro de costos al que pertenece dicho usuario.
																				if ($_POST['cComDes'] == "" && $xRRF['restipxx'] == "PRINCIPAL" && in_array($vCiudades['ccoidxxx'],$mUsrCco) == true) {

																					/*** para ALMAVIVA se guarda la data en una matriz para luego ser organizada en orden alfabetico las descripciones del combo ***/
																					if ($cAlfa == "ALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "DEALMAVIVA") {

																						$nInd_mCiuFac = count($mCiuFac);
																						$mCiuFac[$nInd_mCiuFac]['comidxxx'] = $vCiudades['comidxxx'];
																						$mCiuFac[$nInd_mCiuFac]['comcodxx'] = $vCiudades['comcodxx'];
																						$mCiuFac[$nInd_mCiuFac]['sucidxxx'] = $vCiudades['sucidxxx'];
																						$mCiuFac[$nInd_mCiuFac]['ccoidxxx'] = $vCiudades['ccoidxxx'];
																						$mCiuFac[$nInd_mCiuFac]['residxxx'] = $xRRF['residxxx'];
																						$mCiuFac[$nInd_mCiuFac]['restipxx'] = $xRRF['restipxx'];
																						$mCiuFac[$nInd_mCiuFac]['SUCIDXXX'] = $vTarifaIca['SUCIDXXX'];
																						$mCiuFac[$nInd_mCiuFac]['CIUICAXX'] = $vTarifaIca['CIUICAXX'];
																						$mCiuFac[$nInd_mCiuFac]['PUCIDXXX'] = $vTarifaIca['PUCIDXXX'];
																						$mCiuFac[$nInd_mCiuFac]['CIUICA2X'] = $vTarifaIca['CIUICA2X'];
																						$mCiuFac[$nInd_mCiuFac]['PUCID2XX'] = $vTarifaIca['PUCID2XX'];
																						$mCiuFac[$nInd_mCiuFac]['resprexx'] = $xRRF['resprexx'];
																						$mCiuFac[$nInd_mCiuFac]['comdesxx'] = $vCiudades['comdesxx']." ".trim($xRRF['restipxx']);
																						$mCiuFac[$nInd_mCiuFac]['selected'] = "SI";

																					} else {
																						$cId  = $vCiudades['comidxxx']."~";
																						$cId .= $vCiudades['comcodxx']."~";
																						$cId .= $vCiudades['sucidxxx']."~";
																						$cId .= $vCiudades['ccoidxxx']."~";
																						$cId .= $xRRF['residxxx']."~";
																						$cId .= $xRRF['restipxx']."~";
																						$cId .= $vTarifaIca['SUCIDXXX']."~";
																						$cId .= $vTarifaIca['CIUICAXX']."~";
																						$cId .= $vTarifaIca['PUCIDXXX']."~";
																						$cId .= $vTarifaIca['CIUICA2X']."~";
																						$cId .= $vTarifaIca['PUCID2XX']."~";
																						$cId .= $xRRF['resprexx'];
																						?>
																						<option value = "<?php echo $cId ?>" selected><?php echo $vCiudades['comdesxx']." ".trim($xRRF['restipxx']) ?></option>
																					<?php } ?>
																					<script language="javascript">
																						// Cargo las variables de comprobante 
																						document.forms['frgrm']['cComId'].value   = "<?php echo $vCiudades['comidxxx'] ?>";
																						document.forms['frgrm']['cComCod'].value  = "<?php echo $vCiudades['comcodxx'] ?>";
																						document.forms['frgrm']['cSucId'].value   = "<?php echo $vCiudades['sucidxxx'] ?>";
																						document.forms['frgrm']['cCcoId'].value   = "<?php echo $vCiudades['ccoidxxx'] ?>";
																						document.forms['frgrm']['cResId'].value   = "<?php echo $xRRF['residxxx'] ?>";
																						document.forms['frgrm']['cResPre'].value  = "<?php echo $xRRF['resprexx'] ?>";
																						document.forms['frgrm']['cResTip'].value  = "<?php echo $xRRF['restipxx'] ?>";
																						document.forms['frgrm']['cSucFId'].value  = "<?php echo $vTarifaIca['SUCIDXXX'] ?>";
																						document.forms['frgrm']['nPorIca'].value  = "<?php echo $vTarifaIca['CIUICAXX'] ?>";
																						document.forms['frgrm']['cPucIca'].value  = "<?php echo $vTarifaIca['PUCIDXXX'] ?>";
																						document.forms['frgrm']['nPorAIca'].value = "<?php echo $vTarifaIca['CIUICA2X'] ?>";
																						document.forms['frgrm']['cPucAIca'].value = "<?php echo $vTarifaIca['PUCID2XX'] ?>";
																					</script>

																			  <?php } else {

																					// Si el usuario esta entrando por primera vez al PASO 1 y no pertenece a ningun centro de costo, simplemente
																					// pinto en el combo las sucursales y dejo como default [SELECCIONE].

																					/*** para ALMAVIVA se guarda la data en una matriz para luego ser organizada en orden alfabetico las descripciones del combo ***/
																					if ($cAlfa == "ALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "DEALMAVIVA") {

																						$nInd_mCiuFac = count($mCiuFac);
																						$mCiuFac[$nInd_mCiuFac]['comidxxx'] = $vCiudades['comidxxx'];
																						$mCiuFac[$nInd_mCiuFac]['comcodxx'] = $vCiudades['comcodxx'];
																						$mCiuFac[$nInd_mCiuFac]['sucidxxx'] = $vCiudades['sucidxxx'];
																						$mCiuFac[$nInd_mCiuFac]['ccoidxxx'] = $vCiudades['ccoidxxx'];
																						$mCiuFac[$nInd_mCiuFac]['residxxx'] = $xRRF['residxxx'];
																						$mCiuFac[$nInd_mCiuFac]['restipxx'] = $xRRF['restipxx'];
																						$mCiuFac[$nInd_mCiuFac]['SUCIDXXX'] = $vTarifaIca['SUCIDXXX'];
																						$mCiuFac[$nInd_mCiuFac]['CIUICAXX'] = $vTarifaIca['CIUICAXX'];
																						$mCiuFac[$nInd_mCiuFac]['PUCIDXXX'] = $vTarifaIca['PUCIDXXX'];
																						$mCiuFac[$nInd_mCiuFac]['CIUICA2X'] = $vTarifaIca['CIUICA2X'];
																						$mCiuFac[$nInd_mCiuFac]['PUCID2XX'] = $vTarifaIca['PUCID2XX'];
																						$mCiuFac[$nInd_mCiuFac]['resprexx'] = $xRRF['resprexx'];
																						$mCiuFac[$nInd_mCiuFac]['comdesxx'] = $vCiudades['comdesxx']." ".trim($xRRF['restipxx']);

																					} else { ?>
																				    <option value = "<?php echo $vCiudades['comidxxx']."~".$vCiudades['comcodxx']."~".$vCiudades['sucidxxx']."~".$vCiudades['ccoidxxx']."~".$xRRF['residxxx']."~".$xRRF['restipxx']."~".$vTarifaIca['SUCIDXXX']."~".$vTarifaIca['CIUICAXX']."~".$vTarifaIca['PUCIDXXX']."~".$vTarifaIca['CIUICA2X']."~".$vTarifaIca['PUCID2XX']."~".$xRRF['resprexx'] ?>"><?php echo $vCiudades['comdesxx']." ".trim($xRRF['restipxx']) ?></option>
																			    <?php }
																			  }
																			}
																		}
															  	}
														  	}
														  }
														?>
													</select>
												</td>
												<!--  Para almaviva se carga la informacion de las sucursales en un matriz, se organiza alfabeticamente y se inserta en el comobo despues de procesada toda la data. -->
												<?php if ($cAlfa == "ALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "DEALMAVIVA") {
													if (count($mCiuFac) > 0) {

														$mCiuFac = f_Sort_Array_By_Field($mCiuFac,"comdesxx","ASC_AZ");

														for($nCF = 0; $nCF < count($mCiuFac); $nCF++) {?>
															<script language="javascript">
																var opt = new Option("<?php echo $mCiuFac[$nCF]['comdesxx'] ?>", "<?php echo $mCiuFac[$nCF]['comidxxx']."~".$mCiuFac[$nCF]['comcodxx']."~".$mCiuFac[$nCF]['sucidxxx']."~".$mCiuFac[$nCF]['ccoidxxx']."~".$mCiuFac[$nCF]['residxxx']."~".$mCiuFac[$nCF]['restipxx']."~".$mCiuFac[$nCF]['SUCIDXXX']."~".$mCiuFac[$nCF]['CIUICAXX']."~".$mCiuFac[$nCF]['PUCIDXXX']."~".$mCiuFac[$nCF]['CIUICA2X']."~".$mCiuFac[$nCF]['PUCID2XX']."~".$mCiuFac[$nCF]['resprexx'] ?>" );
																if ("<?php echo $mCiuFac[$nCF]['selected'] ?>" == "SI") {
																	opt.setAttribute("selected", "selected");
																}
																document.getElementById("cComDes").add(opt);
															</script><?php
														}
													}
												}
												?>

												<td Class = "name" colspan = "8">Tipo de Factura<br>
												  <select Class = "letrase" name = "cComTFa" value = "<?php if($_POST['cComTFa'] == ""){echo "AUTOMATICA";}else{echo $_POST['cComTFa'];} ?>" style = "width:160">
														<option value = "AUTOMATICA">AUTOMATICA</option>
														<option value = "MANUAL">MANUAL</option>
													</select>
													<script language="javascript">
														document.forms['frgrm']['cComTFa'].value="<?php if($_POST['cComTFa'] == ""){echo "AUTOMATICA";}else{echo $_POST['cComTFa'];} ?>";
													</script>
												</td>
												<?php if ($nIncComTipCsc == 0) { ?>
													<td Class = "name" colspan = "8">Tipo de Consecutivo<br>
													  <select Class = "letrase" name = "cComTipCsc" value = "<?php echo ($_POST['cComTipCsc'] == "") ? $vSysStr['financiero_tipo_de_factura'] : $_POST['cComTipCsc']; ?>" style = "width:160">
															<option value = "DEFINITIVO">DEFINITIVO</option>
															<option value = "PREFACTURA">PREFACTURA</option>
														</select>
														<script language="javascript">
															document.forms['frgrm']['cComTipCsc'].value="<?php echo ($_POST['cComTipCsc'] == "") ? $vSysStr['financiero_tipo_de_factura'] : $_POST['cComTipCsc']; ?>";
														</script>
													</td>
												<?php } ?>
												<td Class = "name" colspan = "4"><a href="#" id="id_href_dRegFCre">Fecha</a><br>
  												<input type = "text" style = "width:80;text-align:center" name = "dRegFCre" readonly
  												  onBlur = "javascript:uDate(this);">
  												<script language="javascript">
  												  if (document.forms['frgrm']['cResTip'].value == "SECUNDARIA") {
  												  	document.forms['frgrm']['dRegFCre'].value = "<?php echo $_POST['dRegFCre'] ?>";
  												    document.getElementById('id_href_dRegFCre').href = "javascript:show_calendar('frgrm.dRegFCre')";
  												  } else {
  												  	document.forms['frgrm']['dRegFCre'].value = "<?php echo date("Y-m-d"); ?>";
  												    document.getElementById('id_href_dRegFCre').href = "javascript:alert('Solo se Permite Cambiar la Fecha Cuando el Tipo de Resolucion es [SECUNDARIA], Verifique.')";
  												  }
  												</script>
												</td>

												<?php switch ($vSysStr['financiero_tipo_consecutivo_facturacion']) {
													case "MANUAL": ?>
														<td Class = "name" colspan = "4">Consecutivo<br>
															<input type = "text" Class = "letra" style = "width:80;text-align:right" name = "cComCsc" value = "<?php echo $_POST['cComCsc']  ?>"
																onFocus="javascript:this.value='';
																			 	 this.style.background='#00FFFF'"
																onKeyUp="javascript:f_FixFloat(this);"
																onBlur = "javascript:f_FixFloat(this);
																										 this.value=this.value.toUpperCase();
																				         		 this.style.background='#FFFFFF'">
														</td>
													<?php break;
													default: ?>
														<input type = "hidden" name = "cComCsc"  value = "<?php echo $_POST['cComCsc']  ?>" readonly>
													<?php break;
												} ?>
											</tr>
											<tr>
												<td Class = "name" colspan = "8">Tipo de Cobro<br>
												  <select Class = "letrase" style = "width:160" name = "cComTCo" value = "<?php if($_POST['cComTCo'] == ""){echo "TODO";}else{echo $_POST['cComTCo'];} ?>" onchange="f_Cambiar_Tipo_Cobro(this.value,'SI')">
														<option value = "TODO">TODO</option>
														<option value = "PCC">PAGOS A TERCEROS</option>
														<option value = "IP">INGRESOS PROPIOS</option>
                            <?php if (f_InList($cAlfa,"ALMAVIVA","TEALMAVIVA") && $_COOKIE['kModo'] == "PEDIDOSAP") { ?>
                              <option value = "NO_APLICA">NO APLICA</option>
                            <?php } ?>
													</select>
													<!-- Este campo es para guardar la forma de cobro anterior -->
													<input type = "hidden" name = "cComFCA" value = "" readonly>
													<script language="javascript">
														document.forms['frgrm']['cComTCo'].value = "<?php if($_POST['cComTCo'] == ""){echo "TODO";}else{echo $_POST['cComTCo'];} ?>";
														document.forms['frgrm']['cComFCA'].value = document.forms['frgrm']['cComTCo'].value;
													</script>
												</td>
												<?php 
                          switch ($cAlfa) {
                            case "SIACOSIA": case "TESIACOSIP": case "DESIACOSIP":
                              $nColspan = 27;
                              $nWidth   = 540;
                              // Valores para la columna Moneda
                              $nColMon = "6";
                              $nWidMon = "120";
                            break;
                            case "KARGORUX": case "TEKARGORUX": case "DEKARGORUX":
                            case "ROLDANLO": case "TEROLDANLO": case "DEROLDANLO":
                              $nColspan = 32;
                              $nWidth   = 640;
                              // Valores para la columna Moneda
                              $nColMon = "7";
                              $nWidMon = "140";
                            break;
														case "OPENEBCO": case "TEOPENEBCO": case "DEOPENEBCO":
															$nColspan = 27;
                              $nWidth   = 540;
														break;
                            default:
                              $nColspan = 39;
                              $nWidth   = 780;
                            break;
                          }
												?>
												<td Class = "name" colspan = "<?php echo $nColspan; ?>">Descripcion del Tipo de Cobro<br>
                          <b><input type = "text" Class = "letra" style = "font-weight:bold;width:<?php echo $nWidth; ?>" name = "cComTCD" value="" readonly></b>
                          <?php
                          // Campo Moneda
                          switch ($cAlfa) {
                            case "SIACOSIA": case "TESIACOSIP": case "DESIACOSIP":
                            case "KARGORUX": case "TEKARGORUX": case "DEKARGORUX": 
                              ?>
                              </td>
                              <td Class = "name" colspan = "<?php echo $nColMon ?>">Moneda<br>
                                <select Class = "letrase" style = "width:<?php echo $nWidMon ?>" name = "cMonId" value = "<?php if($_POST['cMonId'] == ""){echo "COP";}else{echo $_POST['cMonId'];} ?>">
                                  <option value = "COP">COP</option>
                                  <option value = "USD">USD</option>
                                </select>
                                <!-- Este campo es para guardar la forma de cobro anterior -->
                                <script language="javascript">
                                  document.forms['frgrm']['cMonId'].value = "<?php if($_POST['cMonId'] == ""){echo "COP";}else{echo $_POST['cMonId'];} ?>";
                                </script>
                              </td>
                              <?php if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") { ?>
                                <td Class = "name" colspan = "6">Formato Impresi&oacute;n<br>
                                  <select Class = "letrase" style = "width:120" name = "cForImp" value = "<?php if($_POST['cForImp'] == ""){echo "NORMAL";}else{echo $_POST['cForImp'];} ?>">
                                    <option value = "NORMAL">NORMAL</option>
                                    <option value = "REGALIAS">REGALIAS</option>
                                  </select>
                                  <!-- Este campo es para guardar la forma de cobro anterior -->
                                  <script language="javascript">
                                    document.forms['frgrm']['cForImp'].value = "<?php if($_POST['cForImp'] == ""){echo "NORMAL";}else{echo $_POST['cForImp'];} ?>";
                                  </script>
                                </td>
                              <?php } else { ?>
                                <input type = "hidden" name = "cForImp" value = "">
                              <?php } ?>
                              <input type = "hidden" name = "cOrdenCompra" value = "">
                              <?php
                            break;
                            case "ROLDANLO": case "TEROLDANLO": case "DEROLDANLO":
                              ?>
                              </td>
                              <td Class = "name" colspan = "<?php echo $nColMon ?>">Moneda<br>
                                <input type = "text" Class = "letra" style = "width:<?php echo $nWidMon ?>" name = "cMonId" value = "<?php if($_POST['cMonId'] == ""){echo "COP";}else{echo $_POST['cMonId'];} ?>" readonly>
                                <input type = "hidden" name = "cForImp" value = "">
                                <input type = "hidden" name = "cOrdenCompra" value = "">
                              </td>
                              <?php
                            break;
                            case "OPENEBCO": case "TEOPENEBCO": case "DEOPENEBCO":
                              ?>
                              <td class="name" colspan="12">Orden de Compra <br>
                                <input type="text" class="letra" style="width:240" name="cOrdenCompra" value="<?php if($_POST['cOrdenCompra'] == ""){echo "";}else{echo $_POST['cOrdenCompra'];} ?>"/>
                                <input type = "hidden" name = "cMonId"  value = "">
                                <input type = "hidden" name = "cForImp" value = "">
                              </td>
                              <?php
                            break;
                            default: ?>
                                <input type = "hidden" name = "cMonId"  value = "">
                                <input type = "hidden" name = "cForImp" value = "">
                                <input type = "hidden" name = "cOrdenCompra" value = "">
                              </td>
                              <?php
                            break;
                          } ?>
                        </tr>
                        <tr>
                        <td Class = "name" colspan = "47">Observaciones Generales de la Factura [Maximo 200 Caracteres]<br>
                          <textarea Class = "letrata" name="cComObs" style="width:940;height:35;overflow:auto"
                            onBlur = "javascript:this.value=this.value.toUpperCase();">
                          </textarea>
                          <script language="javascript">
  												  document.forms['frgrm']['cComObs'].value="<?php echo $_POST['cComObs'] ?>";
  												</script>
												</td>
											</tr>
										</table>
									</center>
								</fieldset>

								<fieldset id="Datos_del_Importador">
		   						<legend><b>Datos del Importador</b></legend>
		   						<center>
			   						<table border = "0" cellpadding = "0" cellspacing = "0" width = "940">
											<?php $cCols = f_Format_Cols(47); echo $cCols; ?>
											<tr>
												<td Class = "name" colspan = "6">Tipo Cliente<br>
													<!-- El Estado de la Tarifa, se Trae de la Tabla GRM00350 campo CLIATAXX -->
													<input type = "hidden" name = "cTarEst">
													<select Class = "letrase" name = "cTerTip" style = "width:120" value="<?php echo $_POST['cTerTip'] ?>" disabled>
														<option value = "CLICLIXX" selected>CLIENTE</option>
													</select>
												</td>
												<td Class = "name" colspan = "6">
													<a href = "javascript:document.forms['frgrm']['cTarEst'].value = '';
																								document.forms['frgrm']['cTerId'].value  = '';
																				  		  document.forms['frgrm']['cTerNom'].value = '';
																								document.forms['frgrm']['cTerDV'].value  = '';
																								document.forms['frgrm']['cTerCal'].value = '';
																								document.forms['frgrm']['cTerRSt'].value = '';
																								document.forms['frgrm']['cTerRFte'].value = '';
																								document.forms['frgrm']['cTerRCre'].value = '';
																								document.forms['frgrm']['cTerCInt'].value = '';
																								document.forms['frgrm']['cTerRIca'].value = '';
																								document.forms['frgrm']['cTerAIva'].value = '';
																								document.forms['frgrm']['cTerAIf'].value = '';
																								document.forms['frgrm']['cTerSIca'].value = '';
																								document.forms['frgrm']['cTerIdInt'].value = '';
																								document.forms['frgrm']['cTerDVInt'].value = '';
																								document.forms['frgrm']['cTerNomInt'].value = '';
															                  document.forms['frgrm']['cTerCalInt'].value  = '';
																								document.forms['frgrm']['cTerRStInt'].value  = '';
															                  document.forms['frgrm']['cTerRFteInt'].value = '';
															                  document.forms['frgrm']['cTerRCreInt'].value = '';
															                  document.forms['frgrm']['cTerCIntInt'].value = '';
															                  document.forms['frgrm']['cTerRIcaInt'].value = '';
															                  document.forms['frgrm']['cTerAIvaInt'].value = '';
															                  document.forms['frgrm']['cTerAIfInt'].value = '';
															                  document.forms['frgrm']['cTerSIcaInt'].value = '';
																								document.forms['frgrm']['cTerDir'].value = '';
														                    document.forms['frgrm']['cTerTel'].value = '';
														                    document.forms['frgrm']['cTerFax'].value = '';
														                    document.forms['frgrm']['cTerPla'].value = '';
																								document.forms['frgrm']['cTerEma'].value = '';
																								document.forms['frgrm']['cTerAnt'].value = '';
																								document.forms['frgrm']['cTerGru'].value = '';
																								document.forms['frgrm']['cCccAIF'].value = '';
																								document.forms['frgrm']['cCccIFA'].value = '';
																								document.forms['frgrm']['nTasaCambio'].value = '';
																								document.forms['frgrm']['dFechaProm'].value  = '';
																								f_Links('cTerId','VALID') " id="id_href_cTerId">Nit</a><br>
													<input type = "text" Class = "letra" style = "width:120" name = "cTerId" value="<?php echo $_POST['cTerId'] ?>"
											    	onFocus="javascript:document.forms['frgrm']['cTarEst'].value = '';
																								document.forms['frgrm']['cTerId'].value  = '';
																				  		  document.forms['frgrm']['cTerNom'].value = '';
																								document.forms['frgrm']['cTerDV'].value  = '';
																								document.forms['frgrm']['cTerCal'].value = '';
																								document.forms['frgrm']['cTerRSt'].value = '';
																								document.forms['frgrm']['cTerRFte'].value = '';
																								document.forms['frgrm']['cTerRCre'].value = '';
																								document.forms['frgrm']['cTerCInt'].value = '';
																								document.forms['frgrm']['cTerRIca'].value = '';
																								document.forms['frgrm']['cTerAIva'].value = '';
																								document.forms['frgrm']['cTerAIf'].value = '';
																								document.forms['frgrm']['cTerSIca'].value = '';
																								document.forms['frgrm']['cTerIdInt'].value = '';
																								document.forms['frgrm']['cTerDVInt'].value = '';
																								document.forms['frgrm']['cTerNomInt'].value = '';
															                  document.forms['frgrm']['cTerCalInt'].value  = '';
																								document.forms['frgrm']['cTerRStInt'].value  = '';
															                  document.forms['frgrm']['cTerRFteInt'].value = '';
															                  document.forms['frgrm']['cTerRCreInt'].value = '';
															                  document.forms['frgrm']['cTerCIntInt'].value = '';
															                  document.forms['frgrm']['cTerRIcaInt'].value = '';
															                  document.forms['frgrm']['cTerAIvaInt'].value = '';
															                  document.forms['frgrm']['cTerAIfInt'].value = '';
															                  document.forms['frgrm']['cTerSIcaInt'].value = '';
																								document.forms['frgrm']['cTerDir'].value = '';
														                    document.forms['frgrm']['cTerTel'].value = '';
														                    document.forms['frgrm']['cTerFax'].value = '';
														                    document.forms['frgrm']['cTerPla'].value = '';
																								document.forms['frgrm']['cTerEma'].value = '';
																								document.forms['frgrm']['cTerAnt'].value = '';
																								document.forms['frgrm']['cTerGru'].value = '';
																								document.forms['frgrm']['cCccAIF'].value = '';
																								document.forms['frgrm']['cCccIFA'].value = '';
																								document.forms['frgrm']['nTasaCambio'].value = '';
																								document.forms['frgrm']['dFechaProm'].value  = '';
															                  this.style.background='#00FFFF'"
														onBlur = "javascript:this.value=this.value.toUpperCase();
																				         f_Links('cTerId','VALID');
																				         this.style.background='#FFFFFF'">
													<input type = "hidden" name = "cTerId_Ant" value = "<?php echo $_POST['cTerId']   ?>" readonly>
													<input type = "hidden" name = "cTerCal"  value   = "<?php echo $_POST['cTerCal']  ?>"> <!-- Calidad del Tercero -->
													<input type = "hidden" name = "cTerRSt"  value   = "<?php echo $_POST['cTerRSt']  ?>"> <!-- Regimen Simple de Tributacion -->
													<input type = "hidden" name = "cTerRFte" value   = "<?php echo $_POST['cTerRFte'] ?>"> <!-- Retencion en la Fuente -->
													<input type = "hidden" name = "cTerRCre" value   = "<?php echo $_POST['cTerRCre'] ?>"> <!-- Retencion CREE -->
													<input type = "hidden" name = "cTerCInt" value   = "<?php echo $_POST['cTerCInt'] ?>"> <!-- Retencion de IVA -->
													<input type = "hidden" name = "cTerRIca" value   = "<?php echo $_POST['cTerRIca'] ?>"> <!-- Retencion de ICA -->
													<input type = "hidden" name = "cTerAIva" value   = "<?php echo $_POST['cTerAIva'] ?>"> <!-- Aplica Iva Generado-->
													<input type = "hidden" name = "cTerAIf"  value   = "<?php echo $_POST['cTerAIf']  ?>"> <!-- Aplica Gravamen financiero-->
													<input type = "hidden" name = "cTerSIca" value   = "<?php echo $_POST['cTerSIca'] ?>"> <!-- Sucursales para las que Aplica el ICA -->
												</td>
												<td Class = "name" colspan = "1">Dv<br>
													<input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDV" value="<?php echo $_POST['cTerDV'] ?>" readonly>
												</td>
												<?php
	 											if($vSysStr['system_activar_openetl'] == 'SI'){
													$nColspan = 22;
													$nWidth   = 440;
												}else{
													$nColspan = 28;
													$nWidth   = 560;
												}
												?>
												<td Class = "name" colspan = "<?php echo $nColspan ?>">Cliente<br>
												<input type = "text" Class = "letra" style = "width:<?php echo $nWidth ?>" name = "cTerNom" value="<?php echo $_POST['cTerNom'] ?>"
											    	onBlur = "javascript:this.value=this.value.toUpperCase();
															                   f_Links('cTerNom','VALID');
															                   this.style.background='#FFFFFF';"
											    	onFocus="javascript:document.forms['frgrm']['cTarEst'].value     = '';
																								document.forms['frgrm']['cTerId'].value      = '';
																				  		  document.forms['frgrm']['cTerNom'].value     = '';
																								document.forms['frgrm']['cTerDV'].value      = '';
																								document.forms['frgrm']['cTerCal'].value     = '';
																								document.forms['frgrm']['cTerRSt'].value     = '';
																								document.forms['frgrm']['cTerRFte'].value    = '';
																								document.forms['frgrm']['cTerRCre'].value    = '';
																								document.forms['frgrm']['cTerCInt'].value    = '';
																								document.forms['frgrm']['cTerRIca'].value    = '';
																								document.forms['frgrm']['cTerAIva'].value    = '';
																								document.forms['frgrm']['cTerAIf'].value     = '';
																								document.forms['frgrm']['cTerSIca'].value    = '';
																								document.forms['frgrm']['cTerIdInt'].value   = '';
																								document.forms['frgrm']['cTerDVInt'].value   = '';
																								document.forms['frgrm']['cTerNomInt'].value  = '';
															                  document.forms['frgrm']['cTerCalInt'].value  = '';
																								document.forms['frgrm']['cTerRStInt'].value  = '';
															                  document.forms['frgrm']['cTerRFteInt'].value = '';
															                  document.forms['frgrm']['cTerRCreInt'].value = '';
															                  document.forms['frgrm']['cTerCIntInt'].value = '';
															                  document.forms['frgrm']['cTerRIcaInt'].value = '';
															                  document.forms['frgrm']['cTerAIvaInt'].value = '';
															                  document.forms['frgrm']['cTerAIfInt'].value = '';
															                  document.forms['frgrm']['cTerSIcaInt'].value = '';
																								document.forms['frgrm']['cTerDir'].value     = '';
														                    document.forms['frgrm']['cTerTel'].value     = '';
														                    document.forms['frgrm']['cTerFax'].value     = '';
														                    document.forms['frgrm']['cTerPla'].value     = '';
																								document.forms['frgrm']['cTerEma'].value     = '';
																								document.forms['frgrm']['cTerAnt'].value     = '';
																								document.forms['frgrm']['cTerGru'].value     = '';
																								document.forms['frgrm']['cCccAIF'].value     = '';
																								document.forms['frgrm']['cCccIFA'].value     = '';
																								document.forms['frgrm']['nTasaCambio'].value = '';
																								document.forms['frgrm']['dFechaProm'].value  = '';
															                  this.style.background='#00FFFF'">
												</td>
												<td Class = "name" colspan = "1"><br>
													<input type = "text" Class = "letra" style = "width:20" name = "cBlur"  readonly>
													<?php if($vSysStr['system_activar_openetl'] != 'SI'){ ?>
														<input type = "hidden" Class = "letra" style = "width:20" name = "dFechaProm"  readonly>
													<?php } ?>
												</td>
												<?php if($vSysStr['system_activar_openetl'] == 'SI'){ ?>
													<td Class = "name" colspan = "6"><a href="javascript:f_Tasa()" id="idFTasa">Fecha Promulgaci&oacute;n</a><br>
														<input type="text" Class = "letra" style = "width:120;text-align:center" name="dFechaProm" value="<?php echo $_POST['dFechaProm'] ?>"
															onFocus="javascript:document.forms['frgrm']['dFechaProm'].value = '';
																				this.style.background='#00FFFF';"
															onBlur = "javascript:this.style.background='#FFFFFF';" readonly>
													</td>
												<?php } ?>
												<td Class = "name" colspan = "5">Tasa Cambio<br>
												<input type = "text" Class = "letra" style = "width:100;text-align:right" id = "idTasaCambio" name = "nTasaCambio" value="<?php echo $_POST['nTasaCambio'] ?>"
                            onFocus="javascript:
                                      if (document.forms['frgrm']['cTerCalInt'].value == 'NORESIDENTE') {
                                        document.forms['frgrm']['nTasaCambio'].value = '';
                                        this.style.background='#00FFFF';
                                      }"
                            onBlur = "javascript:
                                        if (document.forms['frgrm']['cTerCalInt'].value == 'NORESIDENTE') {
                                          this.style.background='#FFFFFF';
                                        }">
                          <script language="javascript">
                            <?php
														if ($vSysStr['system_activar_openetl'] == 'SI') {	?>
															document.forms['frgrm']['nTasaCambio'].readOnly = true;
														<?php
														} else { ?>
															document.forms['frgrm']['nTasaCambio'].readOnly = false;
														<?php
														}	?>
                          </script>
                        </td>
											</tr>
											<tr>
												<td Class = "name" colspan = "6">Facturar a<br>
													<input type = "text" Class = "letra" style = "width:120" name = "cTerIdInt" value="<?php echo $_POST['cTerIdInt'] ?>"
												    onFocus="javascript:document.forms['frgrm']['cTerIdInt'].value   = '';
																                document.forms['frgrm']['cTerDVInt'].value   = '';
														                    document.forms['frgrm']['cTerNomInt'].value  = '';
														                    document.forms['frgrm']['cTerDir'].value     = '';
															                  document.forms['frgrm']['cTerTel'].value     = '';
															                  document.forms['frgrm']['cTerFax'].value     = '';
															                  document.forms['frgrm']['cTerCalInt'].value  = '';
																								document.forms['frgrm']['cTerRStInt'].value  = '';
															                  document.forms['frgrm']['cTerRFteInt'].value = '';
															                  document.forms['frgrm']['cTerRCreInt'].value = '';
															                  document.forms['frgrm']['cTerCIntInt'].value = '';
															                  document.forms['frgrm']['cTerRIcaInt'].value = '';
															                  document.forms['frgrm']['cTerAIvaInt'].value = '';
															                  document.forms['frgrm']['cTerAIfInt'].value = '';
															                  document.forms['frgrm']['cTerSIcaInt'].value = '';
															                  document.forms['frgrm']['nTasaCambio'].value = '';
																								document.forms['frgrm']['dFechaProm'].value  = '';
															                  this.style.background='#00FFFF';"
														onBlur = "javascript:f_Links('cTerIdInt','VALID');this.style.background='#FFFFFF';">
													<input type = "hidden" name = "cTerCalInt" style = "width:050" value = "<?php echo $_POST['cTerCalInt']  ?>"> <!-- Calidad del Tercero Intermediario -->
													<input type = "hidden" name = "cTerRStInt"  value = "<?php echo $_POST['cTerRStInt']  ?>"> <!-- Regimen Simple de Tributacion -->
													<input type = "hidden" name = "cTerRFteInt" value = "<?php echo $_POST['cTerRFteInt'] ?>"> <!-- Retencion en la Fuente -->
													<input type = "hidden" name = "cTerRCreInt" value = "<?php echo $_POST['cTerRCreInt'] ?>"> <!-- Retencion CREE -->
													<input type = "hidden" name = "cTerCIntInt" value = "<?php echo $_POST['cTerCIntInt'] ?>"> <!-- Retencion de IVA -->
													<input type = "hidden" name = "cTerRIcaInt" value = "<?php echo $_POST['cTerRIcaInt'] ?>"> <!-- Retencion de ICA -->
													<input type = "hidden" name = "cTerAIvaInt" style = "width:050" value = "<?php echo $_POST['cTerAIvaInt'] ?>"> <!-- Aplica Iva Generado-->
													<input type = "hidden" name = "cTerAIfInt" style = "width:050" value = "<?php echo $_POST['cTerAIfInt'] ?>"> <!-- Aplica Gravamen Financiero-->
													<input type = "hidden" name = "cTerSIcaInt" value = "<?php echo $_POST['cTerSIcaInt'] ?>"> <!-- Sucursales para las que Aplica el ICA -->
												</td>
												<td Class = "name" colspan = "1">Dv<br>
													<input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDVInt" value="<?php echo $_POST['cTerDVInt'] ?>" readonly>
												</td>
												<td Class = "name" colspan = "34">Nombre<br>
													<input type = "text" Class = "letra" style = "width:680" name = "cTerNomInt" value="<?php echo $_POST['cTerNomInt'] ?>"
								    	      onFocus="javascript:document.forms['frgrm']['cTerIdInt'].value   = '';
															                  document.forms['frgrm']['cTerDVInt'].value   = '';
															                  document.forms['frgrm']['cTerNomInt'].value  = '';
															                  document.forms['frgrm']['cTerDir'].value     = '';
															                  document.forms['frgrm']['cTerTel'].value     = '';
															                  document.forms['frgrm']['cTerFax'].value     = '';
															                  document.forms['frgrm']['cTerCalInt'].value  = '';
																								document.forms['frgrm']['cTerRStInt'].value  = '';
																                document.forms['frgrm']['cTerRFteInt'].value = '';
																                document.forms['frgrm']['cTerRCreInt'].value = '';
																                document.forms['frgrm']['cTerCIntInt'].value = '';
																                document.forms['frgrm']['cTerRIcaInt'].value = '';
																                document.forms['frgrm']['cTerAIvaInt'].value = '';
																                document.forms['frgrm']['cTerAIfInt'].value = '';
																                document.forms['frgrm']['cTerSIcaInt'].value = '';
															                  document.forms['frgrm']['nTasaCambio'].value = '';
																								document.forms['frgrm']['dFechaProm'].value  = '';
												                  			this.style.background='#00FFFF';"
								    	      onBlur = "javascript:f_Links('cTerNomInt','VALID');this.style.background='#FFFFFF';">
												</td>
												<td Class = "name" colspan = "1"><br>
													<input type = "text" Class = "letra" style = "width:20" name = "cBlur2"  readonly>
												</td>
												<td Class = "name" colspan = "5">Consolidado<br>
													<select Class = "letrase" name = "cComCon" value = "<?php echo $_POST['cComCon'] ?>" style = "width:100">
													 <option value = "SI">SI</option>
													 <option value = "NO" selected>NO</option>
													</select>
													<?php if ($_POST['cComCon'] != "") {?>
														<script language="javascript">
	                            document.forms['frgrm']['cComCon'].value = "<?php echo $_POST['cComCon'] ?>";
	                          </script>
                          <?php } ?>
  											</td>
											</tr>
											<tr>
												<td Class = "name" colspan = "25">Direccion<br>
													<input type = "text" Class = "letra" style = "width:500" name = "cTerDir" value="<?php echo $_POST['cTerDir'] ?>" readonly>
												</td>
												<td Class = "name" colspan = "8">Telefono<br>
													<input type = "text" Class = "letra" style = "width:160" name = "cTerTel" value="<?php echo $_POST['cTerTel'] ?>" readonly>
												</td>
												<td Class = "name" colspan = "8">Fax<br>
													<input type = "text" Class = "letra" style = "width:160" name = "cTerFax" value="<?php echo $_POST['cTerFax'] ?>" readonly>
												</td>
												<td Class = "name" colspan = "6">Plazo<br>
													<?php if($vSysStr['financiero_facturacion_modificar_plazo'] != "SI") { ?>
														<input type = "text" Class = "letra" style = "width:120;text-align:right" name = "cTerPla" value="<?php echo $_POST['cTerPla'] ?>" readonly>
													<?php } else { ?>
														<input type = "text" Class = "letra" style = "width:120;text-align:right" name = "cTerPla" value="<?php echo $_POST['cTerPla'] ?>"
														onFocus="javascript:document.forms['frgrm']['cTerPla'].value = '';
																									this.style.background='#00FFFF';"
														onkeyup="javascript:f_FixFloat(this);"
														onBlur = "javascript:this.style.background='#FFFFFF';">
													<?php } ?>
												</td>
											</tr>
											<tr>
												<td Class = "name" colspan = "22">Correo Electronico<br>
													<input type = "text" Class = "letra" style = "width:440" name = "cTerEma" value="<?php echo $_POST['cTerEma'] ?>" readonly>
												</td>
												<td Class = "name" colspan = "10">Anticipo<br>
													<input type = "text" Class = "letra" style = "width:200" name = "cTerAnt" value="<?php echo $_POST['cTerAnt'] ?>" readonly>
												</td>
												<td Class = "name" colspan = "9">Grupo Clientes<br>
													<input type = "text" Class = "letra" style = "width:180" name = "cTerGru" value="<?php echo $_POST['cTerGru'] ?>" readonly>
												</td>
												<td Class = "name" colspan = "2">I.F.<br>
													<input type = "text" Class = "letra" style = "width:40" name = "cCccAIF" value="<?php echo $_POST['cCccAIF'] ?>" readonly>
												</td>
												<td Class = "name" colspan = "4">A Parir de:<br>
													<input type = "text" Class = "letra" style = "width:80;text-align:right" name = "cCccIFA"  value="<?php echo $_POST['cCccIFA'] ?>" readonly>
												</td>
											</tr>
										</table>
									</center>
								</fieldset>

                <!-- INICIO Datos Adicionales Exporcomex -->
                <?php if ($cAlfa == "EXPORCOM" || $cAlfa == "TEEXPORCOM" || $cAlfa == "DEEXPORCOM") { ?>
                  <fieldset id="Datos_adicionales">
                    <legend><b>Datos Adicionales</b></legend>
                    <center>
                      <table border = "0" cellpadding = "0" cellspacing = "0" width = "940">
                        <?php $cCols = f_Format_Cols(47); echo $cCols; ?>
                        <tr>
                          <td Class = "name" colspan = "12">Factura<br>
                            <input type = "text" Class = "letra" style = "width:240" name = "cFactura" value="<?php echo $_POST['cFactura'] ?>" maxlength="100">
                          </td>
                          <td Class = "name" colspan = "12">Protocolo<br>
                            <input type = "text" Class = "letra" style = "width:240" name = "cProtocolo" value="<?php echo $_POST['cProtocolo'] ?>" maxlength="100">
                          </td>
                          <td Class = "name" colspan = "12">C&oacute;digo de Cobro<br>
                            <input type = "text" Class = "letra" style = "width:240" name = "cCodCobro" value="<?php echo $_POST['cCodCobro'] ?>" maxlength="100">
                          </td>
                          <td Class = "name" colspan = "11">Jobs<br>
                            <input type = "text" Class = "letra" style = "width:220" name = "cJobs" value="<?php echo $_POST['cJobs'] ?>" maxlength="100">
                          </td>
                        </tr>
                        <tr>
                          <td Class = "name" colspan = "12">Registro<br>
                            <input type = "text" Class = "letra" style = "width:240" name = "cRegistro" value="<?php echo $_POST['cRegistro'] ?>" maxlength="100">
                          </td>
                          <td Class = "name" colspan = "12">Declaraci&oacute;n<br>
                            <input type = "text" Class = "letra" style = "width:240" name = "cDeclaracion" value="<?php echo $_POST['cDeclaracion'] ?>" maxlength="100">
                          </td>
                          <td Class = "name" colspan = "12">Contenido<br>
                            <input type = "text" Class = "letra" style = "width:240" name = "cContenido" value="<?php echo $_POST['cContenido'] ?>" maxlength="100">
                          </td>
                          <td Class = "name" colspan = "11">Tasa de Cambio<br>
                            <input type = "text" Class = "letra" style = "width:220" name = "cTasaCamnbio" value="<?php echo $_POST['cTasaCamnbio'] ?>" maxlength="100">
                          </td>
                        </tr>
                      </table>
                    </center>
                  </fieldset>
                <?php } else { ?>
                  <input type = "hidden" name = "cFactura"     value = "">
                  <input type = "hidden" name = "cProtocolo"   value = "">
                  <input type = "hidden" name = "cCodCobro"    value = "">
                  <input type = "hidden" name = "cJobs"        value = "">
                  <input type = "hidden" name = "cRegistro"    value = "">
                  <input type = "hidden" name = "cDeclaracion" value = "">
                  <input type = "hidden" name = "cContenido"   value = "">
                  <input type = "hidden" name = "cTasaCamnbio" value = "">
                <?php }?>
                <!-- FIN Datos Adicionales Exporcomex -->

								<!-- INICIO Datos Adicionales openEtl -->
								<fieldset id="Datos_adicionales_openetl">
									<legend><b>Datos Adicionales openETL</b></legend>
									<table border = "0" cellpadding = "0" cellspacing = "0" width = "940">
									<?php $cCols = f_Format_Cols(47); echo $cCols; ?>
										<tr>
											<td Class = "name" colspan = "12">Tipo de Factura Eletr&oacute;nica<br>
                        <?php if ($_POST['cStep'] != 1) { ?>
                          <input type = "hidden" name = "cComTdoc" value = "<?php echo $_POST['cComTdoc'] ?>">
                        <?php } else { ?>
                          <select Class = "letrase" style = "width:240" name = "cComTdoc" value = "<?php echo $_POST['cComTdoc'] ?>">
                            <option value = "">[SELECCIONE]</option>
                          </select>
                        <?php } ?>
											</td>
											<td Class = "name" colspan = "10">Tipo Operaci&oacute;n<br>
												<select Class = "letrase" style = "width:200" name = "cTopId">
													<option value = "">[SELECCIONE]</option>
													<?php
													$qTipOpe  = "SELECT ";
													$qTipOpe .= "topidxxx, ";
													$qTipOpe .= "topdesxx ";
													$qTipOpe .= "FROM $cAlfa.fpar0154 ";
													$qTipOpe .= "WHERE ";
													$qTipOpe .= "topdocxx = \"FC\" AND ";
													$qTipOpe .= "regestxx = \"ACTIVO\"";
													$xTipOpe  = f_MySql("SELECT","",$qTipOpe,$xConexion01,"");
													// f_Mensaje(__FILE__,__LINE__,$qTipOpe."~".mysql_num_rows($xTipOpe));

													while($xRC = mysql_fetch_array($xTipOpe)){ ?>
														<option value="<?php echo $xRC['topidxxx'] ?>"><?php echo "(".$xRC['topidxxx'].") ".strtoupper($xRC['topdesxx']) ?></option>
													<?php } ?>
												</select>
												<script language="javascript">
													document.forms['frgrm']['cTopId'].value="<?php echo $_POST['cTopId'] ?>";
												</script>
											</td>
											<td Class = "name" colspan = "08">Forma de Pago<br>
												<select Class = "letrase" style = "width:160" name = "cComFpag" onchange="javascript:fnBuscarMedioPago()">
													<option value = "">[SELECCIONE]</option>	
													<option value = "1">(1) CONTADO</option>
													<option value = "2">(2) CREDITO</option>
												</select> 
												<script language="javascript">
													document.forms['frgrm']['cComFpag'].value="<?php echo $_POST['cComFpag'] ?>";
												</script>
											</td>
										
											<td Class = "name" colspan = "05">
												<a href = "javascript:document.forms['frgrm']['cMePagId'].value  = '';
																							document.forms['frgrm']['cMePagDes'].value = '';
																							f_Links('cMePagId','VALID')" id="idMedPag">Medio de Pago</a><br>
												<input type = 'text' Class = 'letra' style = 'width:100' name = 'cMePagId' maxlength="12" value = "<?php echo $_POST['cMePagId'] ?>"
													onFocus="javascript:document.forms['frgrm']['cMePagId'].value  = '';
																							document.forms['frgrm']['cMePagDes'].value = '';
																							this.style.background='#00FFFF'"
																							
													onBlur = "javascript:this.value=this.value.toUpperCase();
																								f_Links('cMePagId','VALID');
																								this.style.background='#FFFFFF'">
											</td>
											<td Class = "name" colspan = "12"><br>
												<input type = 'text' Class = 'letra' style = 'width:240' name = "cMePagDes" value="<?php echo $_POST['cMePagDes'] ?>" readonly>
											</td>
										</tr>
									</table>
								</fieldset>
								<!-- FIN Datos Adicionales openEtl -->

								<fieldset id="Grid_de_Tramites">
			   					<legend><b>Relacion de DOs a Facturar</b></legend>
									<center>
										<table border = "0" cellpadding = "0" cellspacing = "0" width = "940">
											<tr>
												<?php $cCols = f_Format_Cols(47); echo $cCols; ?>
												<td colspan = "2"  class = "name"><center>Sec</center></td>
												<td colspan = "8"  class = "name"><center>DO</center></td>
												<td colspan = "2"  class = "name"><center>Suf</center></td>
												<td colspan = "6"  class = "name"><center>Tipo de Operacion</center></td>
												<td colspan = "6"  class = "name"><center>Modo Transporte</center></td>
												<td colspan = "4"  class = "name"><center>Fecha</center></td>
												<td colspan = "5"  class = "name"><center>Pedido</center></td>
												<td colspan = "5"  class = "name"><center>Valor Tramite</center></td>
												<td colspan = "3"  class = "name"><center>Forms</center></td>
												<td colspan = "3"  class = "name"><center>H.R.</center></td>
												<td colspan = "3"  class = "name"><center>C.E.
													<?php switch ($_COOKIE['kModo']) {
                            case "NUEVO":
                            case "PEDIDOSAP": ?>
															<img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:fnPegarDo()" style = "cursor:pointer" title="Pegar DOs">
															<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:fnBorrarDos()" style = "cursor:pointer" title="Eliminar Todos">
														<? break;
														default: //No hace nada
														break;
													} ?>
													</center>
												</td>
											</tr>
										</table>
										<table border = "0" cellpadding = "0" cellspacing = "0" width = "940" id = "Grid_Tramites"></table>
								  </center>
								</fieldset>

								<fieldset id="Pagos_del_Cliente_Automaticos">
			   					<legend><b>Pagos por Cuenta del Cliente Automaticos</b></legend>
									<center>
										<table border = "0" cellpadding = "0" cellspacing = "0" width = "940">
											<?php $cCols = f_Format_Cols(47); echo $cCols; ?>
			 	         	    <tr>
												<td colspan = "4"  class = "name"><center>Cto</center></td>
												<td colspan = "15" class = "name"><center>Servicio</center></td>
												<td colspan = "7"  class = "name"><center>Tramite</center></td>
												<td colspan = "9"  class = "name"><center>Documento Fuente</center></td>
												<td colspan = "3"  class = "name"><center>&nbsp;&nbsp;Doc. Inf.</center></td>
												<td colspan = "4"  class = "name"><center>Valor Local</center></td>
												<td colspan = "4"  class = "name"><center>Valor NIFF</center></td>
												<td colspan = "1"  class = "name"><center>M</center></td>
											</tr>
										</table>
										<table border = "0" cellpadding = "0" cellspacing = "0" width = "940" id = "Grid_PCCA"></table>
									</center>
								</fieldset>

								<table border = "0" cellpadding = "0" cellspacing = "0" width = "860" id="Pagos_del_Cliente_Totales">
									<?php $cCols = f_Format_Cols(43); echo $cCols; ?>
									<tr>
										<td Class = "name" colspan = "13"></td>
										<td Class = "name" colspan = "3">Anticipos</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nPCCAnt" value = "<?php if(abs($_POST['nPCCAnt']+0) > 0){echo $_POST['nPCCAnt'];}else{echo 0;} ?>" readonly>
											<input type = "hidden" name = "nPCCAntTC" value = "<?php if(abs($_POST['nPCCAntTC']+0) > 0){echo $_POST['nPCCAntTC'];}else{echo 0;} ?>" readonly>
										</td>
										<td Class = "name" colspan = "1"></td>
										<td Class = "name" colspan = "2">Debitos</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nPCCDeb" value = "<?php echo $_POST['nPCCDeb'] ?>" readonly>
										</td>
										<td Class = "name" colspan = "1"></td>
										<td Class = "name" colspan = "2">Creditos</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nPCCCre" value = "<?php echo $_POST['nPCCCre'] ?>" readonly>
										</td>
										<td Class = "name" colspan = "1"></td>
										<td Class = "name" colspan = "4">Valor Neto</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nPCCVNe" value = "<?php echo $_POST['nPCCVNe'] ?>" readonly>
										</td>
							  	</tr>
								</table>

								<fieldset id="Ingresos_Propios_Automaticos">
			   					<legend><b>Ingresos Propios Automaticos</b></legend>
									<center>
										<table border = "0" cellpadding = "0" cellspacing = "0" width = "940">
											<?php $cCols = f_Format_Cols(47); echo $cCols; ?>
			 	         	    <tr>
												<td colspan = "4"  class = "name"><center>Cto</center></td>
                        <?php if ($cAlfa == "GRUMALCO" || $cAlfa == "TEGRUMALCO") { ?>
                          <?php if($_POST['cComTFa'] == "MANUAL") { ?>
                            <td colspan = "19" class = "name"><center>Servicio</center></td>
                          <?php } else { ?>
                            <td colspan = "20" class = "name"><center>Servicio</center></td>
                          <?php } ?>
                        <?php } else { ?>
                          <?php if($_POST['cComTFa'] == "MANUAL") { ?>
                            <td colspan = "26" class = "name"><center>Servicio</center></td>
                          <?php } else { ?>
                            <td colspan = "27" class = "name"><center>Servicio</center></td>
                          <?php } ?>
                        <?php } ?>
												<td colspan = "7"  class = "name"><center>Tramite</center></td>
                        <?php if ($cAlfa == "GRUMALCO" || $cAlfa == "TEGRUMALCO") { ?>
                          <td colspan = "4"  class = "name"><center>Cantidad</center></td>
												  <td colspan = "4"  class = "name"><center>Valor Unitario</center></td>
                        <?php } ?>
												<td colspan = "4"  class = "name"><center>Valor Local</center></td>
												<td colspan = "4"  class = "name"><center>Valor NIFF</center></td>
												<td colspan = "1"  class = "name"><center>M</center></td>
												<?php if($_POST['cComTFa'] == "MANUAL") { ?>
													<td colspan = "1"  class = "name" align="right">
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:fnBorrarIPA()" style = "cursor:pointer" title="Eliminar Todos">
													</td>
												<?php } ?>
											</tr>
										</table>
										<table border = "0" cellpadding = "0" cellspacing = "0" width = "940" id = "Grid_IPA"></table>
									</center>
								</fieldset>

								<table border = "0" cellpadding = "0" cellspacing = "0" width = "940" id="Ingresos_Propios_Tototales">
									<tr><?php $cCols = f_Format_Cols(47); echo $cCols; ?></tr>
									<tr>
										<td colspan="21"></td>
										<td Class = "name" colspan = "4">SubTotal</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nIPASub" value = "<?php if($_POST['nIPASub'] != ""){echo $_POST['nIPASub'];}else{echo 0;} ?>" readonly>
										</td>
										<td colspan="1"></td>
										<td Class = "name" colspan = "4">Iva</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nIPAIva" value = "<?php if($_POST['nIPAIva'] != ""){echo $_POST['nIPAIva'];}else{echo 0;} ?>" readonly>
										</td>
										<td colspan="1"></td>
										<td Class = "name" colspan = "4">Total</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nIPATot" value = "<?php if($_POST['nIPATot'] != ""){echo $_POST['nIPATot'];}else{echo 0;} ?>" readonly>
										</td>
							  	</tr>
									<tr>
										<td colspan="12"></td>
										<td Class = "name" colspan = "4">Rete Fuente</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nIPARFte" value = "<?php if($_POST['nIPARFte'] != ""){echo $_POST['nIPARFte'];}else{echo 0;} ?>" readonly>
										</td>
										<td colspan="1"></td>
										<td Class = "name" colspan = "4">Rete CREE</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nIPARCre" value = "<?php if($_POST['nIPARCre'] != ""){echo $_POST['nIPARCre'];}else{echo 0;} ?>" readonly>
										</td>
										<td colspan="1"></td>
										<td Class = "name" colspan = "4">Rete Iva</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nIPARIva" value = "<?php if($_POST['nIPARIva'] != ""){echo $_POST['nIPARIva'];}else{echo 0;} ?>" readonly>
										</td>
										<td colspan="1"></td>
										<td Class = "name" colspan = "4">Rete ICA</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nIPARIca" value = "<?php if($_POST['nIPARIca'] != ""){echo $_POST['nIPARIca'];}else{echo 0;} ?>" readonly>
										</td>
							  	</tr>
							  	<tr>
										<td colspan="12"></td>
										<td Class = "name" colspan = "4">Auto RFte</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nIPAARFte" value = "<?php if($_POST['nIPAARFte'] != ""){echo $_POST['nIPAARFte'];}else{echo 0;} ?>" readonly>
										</td>
										<td colspan="1"></td>
										<td Class = "name" colspan = "4">Auto RCree</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nIPAARCre" value = "<?php if($_POST['nIPAARCre'] != ""){echo $_POST['nIPAARCre'];}else{echo 0;} ?>" readonly>
										</td>
										<td colspan="10"></td>
										<td Class = "name" colspan = "4">Auto RICA</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nIPAARIca" value = "<?php if($_POST['nIPAARIca'] != ""){echo $_POST['nIPAARIca'];}else{echo 0;} ?>" readonly>
										</td>
							  	</tr>
							  	<tr>
							  		<td colspan="30"></td>
							  		<td Class = "name" colspan = "4">Anticipo</td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nIPAAnt" value = "<?php if($_POST['nIPAAnt'] != ""){echo $_POST['nIPAAnt'];}else{if($_POST['nComTFa']=="MANUAL"){echo $_POST['nPCCAnt'];}else{echo 0;}} ?>" readonly>
										</td>
										<td colspan="1"></td>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:left;color:#000000;font-weight:bold" name = "cComSal" value="<?php if($_POST['nIPASal']>=0){echo "Saldo Agencia";}else{echo "Saldo Cliente";} ?>" readonly>
										<td Class = "name" colspan = "4">
											<input type = "text" Class = "letra" style = "width:080;text-align:right;color:#FF0000;font-weight:bold" name = "nIPASal" value = "<?php if($_POST['nIPASal'] != ""){echo $_POST['nIPASal'];}else{echo 0;} ?>" readonly>
										</td>
							  	</tr>
								</table>

							</fieldset>
						</form>
					</td>
				</tr>
			</table>
		</center>

		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="940">
				<tr>
					<?php switch ($_POST['cStep']) {
						case "1": ?>
							<?php if ($_COOKIE['kModo'] != "VER" && $_COOKIE['kModo'] != "BORRAR") { ?>
								<td width="758" height="21"></td>
						  	<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/siguiente.gif" style="cursor:pointer"
							    onClick = "javascript:
														   if ((document.forms['frgrm']['cComTCo'].value != document.forms['frgrm']['cComFCA'].value) ||
																	 (document.forms['frgrm']['cTerId'].value  != document.forms['frgrm']['cTerId_Ant'].value)) { // Tipo de Cobro vs. Forma de Cobro Anterior
																fnBorrarGrilla('Grid_Tramites');
														   }
														   document.forms['frgrm']['cStep'].value = '2';
															 document.forms['frgrm']['cStep_Ant'].value = '1';
															 fnAsignarValores();
															 document.forms['frestado'].target='fmpro';
		                           document.forms['frestado'].action='frfacvcl.php';
		                           document.forms['frestado'].submit();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Siguiente
						    </td>
								<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
									onClick ="javascript:f_Salir()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
								</td>
				    	<?php } else { ?>
					    	<td width="849" height="21"></td>
							  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
								  onClick ="javascript:f_Salir()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
							  </td>
				    	<?php } ?>
						<?php break;
						case "2": ?>
							<td width="758" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/anterior.gif" style="cursor:pointer"
								onClick = "javascript:document.forms['frgrm']['cStep'].value = '1';
																			document.forms['frgrm']['cStep_Ant'].value = '2';
																			fnAsignarValores();
														          document.forms['frestado'].target='fmwork';
																			document.forms['frestado'].action='frfacnue.php';
																			document.forms['frestado'].submit()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anterior</td>
					  	<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/siguiente.gif" style="cursor:pointer"
								onClick = "javascript:document.forms['frgrm']['cStep'].value = '3';
																			document.forms['frgrm']['cStep_Ant'].value = '2';
																			fnAsignarValores();
																			document.forms['frestado'].target='fmpro';
																			document.forms['frestado'].action='frfacvdo.php';
																			document.forms['frestado'].submit();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Siguiente</td>
						<?php break;
						case "3": ?>
							<td width="758" height="21"></td>
					  	<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/anterior.gif" style="cursor:pointer"
								onClick = "javascript:document.forms['frgrm']['cStep'].value = '2';
																			document.forms['frgrm']['cStep_Ant'].value = '3';
																			fnAsignarValores();
																			document.forms['frestado'].target='fmwork';
																			document.forms['frestado'].action='frfacnue.php';
																			document.forms['frestado'].submit();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anterior</td>
					  	<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/siguiente.gif" style="cursor:pointer"
								onClick = "javascript:document.forms['frgrm']['cStep'].value = '4';
																			document.forms['frgrm']['cStep_Ant'].value = '3';
																			fnAsignarValores();
																		  document.forms['frestado'].target='fmwork';
																		  document.forms['frestado'].action='frfacnue.php';
																		  document.forms['frestado'].submit();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Siguiente</td>
						<?php break;
						case "4":
							$qCccDat = "SELECT * FROM $cAlfa.fpar0151 WHERE cliidxxx = \"{$_POST['cTerIdInt']}\" LIMIT 0,1";
		  				$xCccDat = f_MySql("SELECT","",$qCccDat,$xConexion01,"");
		  				//f_Mensaje(__FILE__,__LINE__,$qCccDat." ~ ".mysql_num_rows($xCccDat));
		  				$vCccDat = mysql_fetch_array($xCccDat);
		  				$cCccImp = $vCccDat['cccimpxx']; ?>
							<script languaje = "javascript">
								document.forms['frgrm']['cCccImp'].value = "<?php echo $cCccImp ?>";
							</script>

							<td width="667" height="21"><img src = "<?php echo $cPlesk_Skin_Directory ?>/progress.gif" id="imgLoad"></td>
					  	<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/anterior.gif" style="cursor:pointer"
								onClick = "javascript:document.forms['frgrm']['cStep'].value = '3';
																			document.forms['frgrm']['cStep_Ant'].value = '4';
																			fnAsignarValores();
																			document.forms['frestado'].target='fmwork';
																			document.forms['frestado'].action='frfacnue.php';
																			document.forms['frestado'].submit()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anterior</td>
					  	<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_print_bg.gif" style="cursor:pointer"
								onClick = "javascript:fnVistaPrevia()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;V. Previa</td>
					  	<td width="91" height="21">
					  		 <input type="button" name="Btn_Guardar" id="Btn_Guardar" value="Grabar" Class="name bntGuardar"
														onclick = "javascript:fnGuardar();"<?php echo ($_POST['cComTFa'] != "MANUAL") ? " disabled" : "" ?>></td>
				  	<?php break;
				  	default: ?>
				  	<?php break;
			  	} ?>
			  </tr>
			</table>

			<?php
			// Historico de obsersaciones - Paso 2 Facturacion
			if ($cAlfa == 'DHLEXPRE' || $cAlfa == 'DEDHLEXPRE' || $cAlfa == 'TEDHLEXPRE') { ?>
				<center>
				<table border="0" cellpadding="0" cellspacing="0" width="995" id="Tramites_Observaciones" style="display:none; margin-left:10px;">
					<tr>
						<td>
							<fieldset id="Grid_de_Tramites">
								<legend><b>Historico Observaciones Do</b></legend>
								<center>
									<table border = "0" cellpadding = "0" cellspacing = "0" width = "960" id = "Grid_Tramites_Observaciones"></table>
								</center>
							</fieldset>
						<td> 
					</tr>
				</table>
				</center>
			<?php } ?>
		</center>

		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<?php switch ($_COOKIE['kModo']) {
      case "NUEVO":
      case "PEDIDOSAP": ?>
				<script languaje = "javascript">
					fnBorrarGrilla("Grid_Tramites");
				</script>
				<?php
        if ($_POST['cStep'] == 1) {
          //Si no se ha seleccionado Forma de Pago y Medio de Pago
          //Se asigna el parametrizado en las variables del sistema
          if ($vSysStr['system_activar_openetl'] == "SI" && $vSysStr['financiero_forma_pago_por_defecto'] != "" && $_POST['cComFpag'] == "") {
            ?>
            <script languaje = "javascript">
              document.forms['frgrm']['cComFpag'].value = "<?php echo $vSysStr['financiero_forma_pago_por_defecto'] ?>";
            </script>
            <?php
            //Cargando medio de pago
            $vMedPag = array();
            //Para la forma de pago contado (1)
            if ($vSysStr['financiero_forma_pago_por_defecto'] == "1" && $vSysStr['financiero_medio_pago_por_defecto_contado'] != "") {
              $qMedPag  = "SELECT ";
              $qMedPag .= "mpaidxxx, ";
              $qMedPag .= "mpadesxx, ";
              $qMedPag .= "regestxx ";
              $qMedPag .= "FROM $cAlfa.fpar0155 ";
              $qMedPag .= "WHERE ";
              $qMedPag .= "mpaidxxx = \"{$vSysStr['financiero_medio_pago_por_defecto_contado']}\" LIMIT 0,1";
              $xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qMedPag."~ ".mysql_num_rows($xMedPag));
              $vMedPag = mysql_fetch_array($xMedPag);
            }
            //Para la forma de pago credito (2)
            if ($vSysStr['financiero_forma_pago_por_defecto'] == "2" && $vSysStr['financiero_medio_pago_por_defecto_credito'] != "") {
              $qMedPag  = "SELECT ";
              $qMedPag .= "mpaidxxx, ";
              $qMedPag .= "mpadesxx, ";
              $qMedPag .= "regestxx ";
              $qMedPag .= "FROM $cAlfa.fpar0155 ";
              $qMedPag .= "WHERE ";
              $qMedPag .= "mpaidxxx = \"{$vSysStr['financiero_medio_pago_por_defecto_credito']}\" LIMIT 0,1";
              $xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qMedPag."~ ".mysql_num_rows($xMedPag));
              $vMedPag = mysql_fetch_array($xMedPag);
            }
            if (count($vMedPag) > 0) {
              ?>
              <script languaje = "javascript">
                document.forms['frgrm']['cMePagId'].value  = "<?php echo $vMedPag['mpaidxxx'] ?>";
                document.forms['frgrm']['cMePagDes'].value = "<?php echo utf8_decode($vMedPag['mpadesxx']) ?>";
              </script>
              <?php    
            }
          }
          ?>
          <script languaje = "javascript">
            f_Cambiar_Tipo_Cobro(document.forms['frgrm']['cComTCo'].value,'NO');
            fnCargarTipoFacturaFE(document.forms['frgrm']['cComId'].value, document.forms['frgrm']['cComCod'].value, '<?php echo $_POST['cComTdoc'] ?>');
          </script>
        <?php }

				//Limpio las variables de contenedores siempre que llegue al STEP 2.
				if ($_POST['cStep'] == 2) {
					if (trim($_POST['cFacId']) != "" && trim($_POST['cTabla_DOS']) != "") {
						//Mostrando DOs ya selecionados
						$qDatDo  = "SELECT * ";
						$qDatDo .= "FROM $cAlfa.{$_POST['cTabla_DOS']} ";
						$qDatDo .= "WHERE ";
						$qDatDo .= "cUsrId_DOS = \"{$_COOKIE['kUsrId']}\" AND ";
						$qDatDo .= "cFacId_DOS = \"{$_POST['cFacId']}\" ";
						$qDatDo .= "ORDER BY  ABS(cSeq_DOS) ";
						$xDatDo  = f_MySql("SELECT","",$qDatDo,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qDatDo."~".mysql_num_rows($xDatDo));
						$nSecuencia = 1;
						while ($xRDD = mysql_fetch_array($xDatDo)) { ?>
							<script languaje = "javascript">
								if ("<?php echo str_pad($nSecuencia,3,"0",STR_PAD_LEFT) ?>" != "001") {
									f_Add_New_Row_Tramites();
								}
								document.forms['frgrm']['cSeq_DOS'   +'<?php echo $nSecuencia ?>'].value     = '<?php echo str_pad($nSecuencia,3,"0",STR_PAD_LEFT) ?>';
								document.forms['frgrm']['cSucId_DOS' +'<?php echo $nSecuencia ?>'].value     = '<?php echo $xRDD['cSucId_DOS']  ?>';
								document.forms['frgrm']['cDosNro_DOS'+'<?php echo $nSecuencia ?>'].value     = '<?php echo $xRDD['cDosNro_DOS'] ?>';
				  			document.forms['frgrm']['cDosSuf_DOS'+'<?php echo $nSecuencia ?>'].value     = '<?php echo $xRDD['cDosSuf_DOS'] ?>';
				  			document.getElementById('cDosTip_DOS'+'<?php echo $nSecuencia ?>').innerHTML = '<?php echo $xRDD['cDosTip_DOS'] ?>';
				  			document.getElementById('cDosMtr_DOS'+'<?php echo $nSecuencia ?>').innerHTML = '<?php echo $xRDD['cDosMtr_DOS'] ?>';
				  			document.getElementById('cDosFec_DOS'+'<?php echo $nSecuencia ?>').innerHTML = '<?php echo $xRDD['cDosFec_DOS'] ?>';
				  			document.forms['frgrm']['cDosPed_DOS'+'<?php echo $nSecuencia ?>'].value     = '<?php echo $xRDD['cDosPed_DOS'] ?>';
				  			document.getElementById('nDosVlr_DOS'+'<?php echo $nSecuencia ?>').innerHTML = '<?php echo $xRDD['nDosVlr_DOS'] ?>';
				  			document.getElementById('cDosFor_DOS'+'<?php echo $nSecuencia ?>').innerHTML = '<?php echo $xRDD['cDosFor_DOS'] ?>';
				  			document.getElementById('cDosRec_DOS'+'<?php echo $nSecuencia ?>').innerHTML = '<?php echo $xRDD['cDosRec_DOS'] ?>';
				  			document.forms['frgrm']['cDosCE_DOS' +'<?php echo $nSecuencia ?>'].value     = '<?php echo $xRDD['cDosCE_DOS']  ?>';

				  			if ('<?php echo $xRDD['cColor_DOS'] ?>' == "red") {
									var cBgColor = "red";
									var cColor   = "#FFFFFF";
								} else {
									var cBgColor = "#FFFFFF";
									var cColor   = "black";
								}

								document.getElementById('cSeq_DOS'   +'<?php echo $nSecuencia ?>').style.color = cColor;
								document.getElementById('cSeq_DOS'   +'<?php echo $nSecuencia ?>').style.backgroundColor = cBgColor;
								document.getElementById('cSucId_DOS' +'<?php echo $nSecuencia ?>').style.color = cColor;
								document.getElementById('cSucId_DOS' +'<?php echo $nSecuencia ?>').style.backgroundColor = cBgColor;
								document.getElementById('cDosNro_DOS'+'<?php echo $nSecuencia ?>').style.color = cColor;
								document.getElementById('cDosNro_DOS'+'<?php echo $nSecuencia ?>').style.backgroundColor = cBgColor;
				  			document.getElementById('cDosSuf_DOS'+'<?php echo $nSecuencia ?>').style.color = cColor;
				  			document.getElementById('cDosSuf_DOS'+'<?php echo $nSecuencia ?>').style.backgroundColor = cBgColor;
				  			document.getElementById('cDosTip_DOS'+'<?php echo $nSecuencia ?>').style.color = cColor;
				  			document.getElementById('cDosTip_DOS'+'<?php echo $nSecuencia ?>').style.backgroundColor = cBgColor;
				  			document.getElementById('cDosMtr_DOS'+'<?php echo $nSecuencia ?>').style.color = cColor;
				  			document.getElementById('cDosMtr_DOS'+'<?php echo $nSecuencia ?>').style.backgroundColor = cBgColor;
				  			document.getElementById('cDosFec_DOS'+'<?php echo $nSecuencia ?>').style.color = cColor;
				  			document.getElementById('cDosFec_DOS'+'<?php echo $nSecuencia ?>').style.backgroundColor = cBgColor;
				  			document.getElementById('cDosPed_DOS'+'<?php echo $nSecuencia ?>').style.color = cColor;
				  			document.getElementById('cDosPed_DOS'+'<?php echo $nSecuencia ?>').style.backgroundColor = cBgColor;
				  			document.getElementById('nDosVlr_DOS'+'<?php echo $nSecuencia ?>').style.color = cColor;
				  			document.getElementById('nDosVlr_DOS'+'<?php echo $nSecuencia ?>').style.backgroundColor = cBgColor;
				  			document.getElementById('cDosFor_DOS'+'<?php echo $nSecuencia ?>').style.color = cColor;
				  			document.getElementById('cDosFor_DOS'+'<?php echo $nSecuencia ?>').style.backgroundColor = cBgColor;
				  			document.getElementById('cDosRec_DOS'+'<?php echo $nSecuencia ?>').style.color = cColor;
				  			document.getElementById('cDosRec_DOS'+'<?php echo $nSecuencia ?>').style.backgroundColor = cBgColor;
				  			document.getElementById('cDosCE_DOS' +'<?php echo $nSecuencia ?>').style.color = cColor;
				  			document.getElementById('cDosCE_DOS' +'<?php echo $nSecuencia ?>').style.backgroundColor = cBgColor;
							</script>
							<?php $nSecuencia++;
						}

						// Si la BD es del cliente DHL Express se cargan las observaciones del DO
						if ($cAlfa == 'DHLEXPRE' || $cAlfa == 'DEDHLEXPRE' || $cAlfa == 'TEDHLEXPRE') { ?>
							<script languaje = "javascript">
								fnCargarObservaciones();
							</script>
							<?php
						}
					} ?>

					<script languaje = "javascript">
						document.forms['frgrm']['nSecuencia_PCCA'].value = 0;
					</script>
					<?php // Si la variables es diferente de vacio es porque viene del paso tres y debo revertir el proceso.
					if ($_POST['cStep_Ant'] == "3" && $_POST['nCscPro'] != "" && f_InList($kSystemCookie[3],"ALPOPULX","TEALPOPULP","ALMAVIVA","TEALMAVIVA")) {
            if ($_COOKIE['kModo'] == "PEDIDOSAP") {
              //no se consultan pagos a terceros
            } else {
              f_Revertir_Recibir_Ingresos_Terceros_Seven($_POST['nCscPro'],$_POST['cComId'],$_POST['cComCod'],$_POST['cComCsc'],$_POST['cSucId']); ?>
              <script languaje = "javascript">
                document.forms['frgrm']['nCscPro'].value = ""; // Limpio el consecutivo de consumo porque se revirtieron los pagos a terceros.
              </script>
            <?php }
          }            
				}

				if ($_POST['cStep'] == 3) {
				  if ($_POST['cStep_Ant'] == "4") { // Vengo del paso cuatro.
				  	//Pinto los pagos a terceros de la tabla
				  	$qPCCA  = "SELECT * ";
					  $qPCCA .= "FROM $cAlfa.{$_POST['cTabla_PCCA']} ";
					  $qPCCA .= "WHERE ";
						$qPCCA .= "cUsrId_PCCA = \"{$_COOKIE['kUsrId']}\" AND ";
						$qPCCA .= "cFacId_PCCA = \"{$_POST['cFacId']}\" ";
						$qPCCA .= "ORDER BY ABS(cComSeq_PCCA) ";
					  $xPCCA  = f_MySql("SELECT","",$qPCCA,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qPCCA."~".mysql_num_rows($xPCCA));

						$nPCCVNe = 0; $nPCCDeb = 0; $nPCCCre = 0; $nValor  = 0;

					  while ($xRP = mysql_fetch_array($xPCCA)) {
					  	if ($xRP['nComVlr_PCCA']   == "") { $xRP['nComVlr_PCCA']   = 0; } // Para evitar el error en la sumatoria
							if ($xRP['nComVlrNF_PCCA'] == "") { $xRP['nComVlrNF_PCCA'] = 0; }// Para evitar el error en la sumatoria

					   	//Se toma el valor segun el tipo de ejecucion
							$nValor = ($xRP['cPucTipEj_PCCA'] == "L" || $xRP['cPucTipEj_PCCA'] == "") ?$xRP['nComVlr_PCCA'] : $xRP['nComVlrNF_PCCA'];

							$nPCCVNe += $nValor; // Valor Neto
				    	switch($xRP['cComMov_PCCA']) {
				    		case "D":
				    			$nPCCDeb += $nValor;
				    		break;
				    		case "C":
				    			$nPCCCre += $nValor;
				    		break;
				    	}

				    	if ($xRP['nVlrCre_PCCA'] > 0 || $xRP['nVlrACre_PCCA'] > 0) {
				    		$xRP['cComTit_PCCA'] = "|RteCree: ".$xRP['nVlrCre_PCCA']."|ARteCree: ".$xRP['nVlrACre_PCCA'];
				    	} ?>
						  <script languaje = "javascript">
								f_Add_New_Row_PCCA();
								document.forms['frgrm']['cComSeq_PCCA'  + '<?php echo ($xRP['cComSeq_PCCA']+0) ?>'].value     = "<?php echo $xRP['cComSeq_PCCA']       ?>";
						    document.getElementById('cComId_PCCA'   + '<?php echo ($xRP['cComSeq_PCCA']+0) ?>').innerHTML = "<?php echo $xRP['cComId_PCCA']        ?>";
								document.forms['frgrm']['cComObs_PCCA'  + '<?php echo ($xRP['cComSeq_PCCA']+0) ?>'].value     = "<?php echo $xRP['cComObs_PCCA']       ?>";
								document.getElementById('cComTra_PCCA'  + '<?php echo ($xRP['cComSeq_PCCA']+0) ?>').innerHTML = "<?php echo $xRP['cComTra_PCCA']       ?>";
								document.getElementById('cComId3_PCCA'  + '<?php echo ($xRP['cComSeq_PCCA']+0) ?>').innerHTML = "<?php echo $xRP['cComId3_PCCA']       ?>";
								document.getElementById('cComCod3_PCCA' + '<?php echo ($xRP['cComSeq_PCCA']+0) ?>').innerHTML = "<?php echo $xRP['cComCod3_PCCA']      ?>";
								document.forms['frgrm']['cComCsc3_PCCA' + '<?php echo ($xRP['cComSeq_PCCA']+0) ?>'].value     = "<?php echo $xRP['cComCsc3_PCCA']      ?>";
								document.getElementById('cComSeq3_PCCA' + '<?php echo ($xRP['cComSeq_PCCA']+0) ?>').innerHTML = "<?php echo $xRP['cComSeq3_PCCA']      ?>";
								document.getElementById('cComDocIn_PCCA'+ '<?php echo ($xRP['cComSeq_PCCA']+0) ?>').innerHTML = "<?php echo $xRP['cComDocIn_PCCA']     ?>";
								document.getElementById('nComVlr_PCCA'  + '<?php echo ($xRP['cComSeq_PCCA']+0) ?>').innerHTML = "<?php echo ($xRP['nComVlr_PCCA']+0)   ?>";
								document.getElementById('nComVlrNF_PCCA'+ '<?php echo ($xRP['cComSeq_PCCA']+0) ?>').innerHTML = "<?php echo ($xRP['nComVlrNF_PCCA']+0) ?>";
								document.forms['frgrm']['cComMov_PCCA'  + '<?php echo ($xRP['cComSeq_PCCA']+0) ?>'].value 		= "<?php echo $xRP['cComMov_PCCA']       ?>";
								document.forms['frgrm']['cComMov_PCCA'  + '<?php echo ($xRP['cComSeq_PCCA']+0) ?>'].title     = "<?php echo $xRP['cComTit_PCCA']       ?>";
							</script>
						<?php }

						//Aproximando a dos decimales, porque las causaciones pueden traer decimales
			    	$nPCCVNe = round($nPCCVNe*100)/100;
			    	$nPCCDeb = round($nPCCDeb*100)/100;
			    	$nPCCCre = round($nPCCCre*100)/100;
			    	?>

						<!-- // Pinto el Valor Total Neto -->
						<script languaje = "javascript">
						  document.forms['frgrm']['nSecuencia_Dos'].value = "<?php echo $_POST['nSecuencia_Dos'] ?>";
							document.forms['frgrm']['nPCCVNe'].value = eval("<?php echo ($nPCCVNe+0) ?>");
			    		document.forms['frgrm']['nPCCDeb'].value = eval("<?php echo ($nPCCDeb+0) ?>");
			    		document.forms['frgrm']['nPCCCre'].value = eval("<?php echo ($nPCCCre+0) ?>");
							if (document.forms['frgrm']['nPCCVNe'].value < 1) {
							  alert("El Sistema no Encontro Datos para Calcular Pagos Por Cuenta del Cliente, Verifique.");
							}
						</script>

						<?php
						// Desmarco los servicios adicionales DO a DO tomados por una factura
						if (f_InList($kSystemCookie[3],"TEALPOPULP","ALPOPULX","ALMAVIVA","TEALMAVIVA")) {
							$qDatDo  = "SELECT * ";
							$qDatDo .= "FROM $cAlfa.{$_POST['cTabla_DOS']} ";
							$qDatDo .= "WHERE ";
							$qDatDo .= "cUsrId_DOS = \"{$_COOKIE['kUsrId']}\" AND ";
							$qDatDo .= "cFacId_DOS = \"{$_POST['cFacId']}\" ";
							$qDatDo .= "ORDER BY ABS(cSeq_DOS) ";
							$xDatDo  = f_MySql("SELECT","",$qDatDo,$xConexion01,"");
							//f_Mensaje(__FILE__,__LINE__,$qDatDo."~".mysql_num_rows($xDatDo));

							while ($xRDD = mysql_fetch_array($xDatDo)) {

								$cFacSerAdi = $_POST['cComId']."-".$_POST['cComCod']."-".$_POST['cComCsc'];
					      $qUpdSerAdi = array(array('NAME'=>'seafacxx','VALUE'=>""               		,'CHECK'=>'NO'),
												            array('NAME'=>'cliidxxx','VALUE'=>$_POST['cTerId']		,'CHECK'=>'WH'),
												            array('NAME'=>'doiidxxx','VALUE'=>$xRDD['cDosNro_DOS'],'CHECK'=>'WH'),
												            array('NAME'=>'seafacxx','VALUE'=>$cFacSerAdi					,'CHECK'=>'WH'),
												            array('NAME'=>'regestxx','VALUE'=>"ACTIVO"						,'CHECK'=>'WH'));

					      if (!f_MySql("UPDATE","zalpo004",$qUpdSerAdi,$xConexion01,$cAlfa)) {
					        $nSwitch = 1;
									f_Mensaje(__FILE__,__LINE__,"El Sistema no Pudo Desmarcar los Servicios Adicionales de la Factura, Verifique.");
					      }
							}
						}
					} elseif ($_POST['cStep_Ant'] == "2") { // Viene del paso 2.

				  	// Limpio las variables [cDosTri], [nDosPCC_DOS], [nDosAnt_DOS], [nDosCla_DOS] por DO de la grilla de DO's,
				  	//si no lo hago cada vez que avance o retroceda del STEP 3, los valores de estas variables se van a duplicar, triplicar, cuatriplicar, etc.
					  $qUpdDo  = "UPDATE $cAlfa.{$_POST['cTabla_DOS']} ";
						$qUpdDo .= "SET ";
						$qUpdDo .= "nDosPCC_DOS   = 0, ";
						$qUpdDo .= "nDosPCCMN_DOS = 0, ";
						$qUpdDo .= "nDosAnt_DOS   = 0, ";
						$qUpdDo .= "nDosAntMN_DOS = 0, ";
						$qUpdDo .= "nDosIF_DOS    = 0, ";
						$qUpdDo .= "nDosIFMN_DOS  = 0, ";
						$qUpdDo .= "nDosIP_DOS    = 0, ";
						$qUpdDo .= "nDosIPMN_DOS  = 0, ";
						$qUpdDo .= "nDosIIP_DOS   = 0, ";
						$qUpdDo .= "nDosIIPMN_DOS = 0, ";
						$qUpdDo .= "nDosCla_DOS   = 0 ";
						$qUpdDo .= "WHERE ";
						$qUpdDo .= "cUsrId_DOS = \"{$_COOKIE['kUsrId']}\" AND ";
						$qUpdDo .= "cFacId_DOS = \"{$_POST['cFacId']}\" ";
						$xUpdDo  = mysql_query($qUpdDo,$xConexion01);
						//f_Mensaje(__FILE__,__LINE__,$qDatDo."~".mysql_num_rows($xDatDo));
						?>
						<script languaje = "javascript">
							document.forms['frgrm']['nPCCVNe'].value = 0;
			    		document.forms['frgrm']['nPCCDeb'].value = 0;
			    		document.forms['frgrm']['nPCCCre'].value = 0;
							document.forms['frgrm']['nPCCAnt'].value = 0;

							document.forms['frgrm']['nSecuencia_Dos'].value  = "<?php echo $_POST['nSecuencia_Dos'] ?>";
							document.forms['frgrm']['nSecuencia_PCCA'].value = 0;
							document.forms['frgrm']['nCscPro'].value = ""; // Siempre que vaya del paso 2 al 3 limpio el consecutivo de pagos a terceros.

							fnAsignarValores();

							document.forms['frestado'].target="fmpro";
							document.forms['frestado'].action="frfacpta.php";
							document.forms['frestado'].submit();
						</script>
					<?php }
				}

				if ($_POST['cStep'] == 4) {
				  switch ($_POST['cComTFa']) {
				    case "AUTOMATICA": ?>
    					<script languaje = "javascript">
    					  document.forms['frgrm']['nSecuencia_Dos'].value = "<?php echo $_POST['nSecuencia_Dos'] ?>";
    						document.forms['frgrm']['nSecuencia_IPA'].value = 0;
    						document.forms['frgrm']['nIPASub'].value   = 0;
    						document.forms['frgrm']['nIPAIva'].value   = 0;
    						document.forms['frgrm']['nIPATot'].value   = 0;
    						document.forms['frgrm']['nIPARFte'].value  = 0;
    						document.forms['frgrm']['nIPARCre'].value  = 0;
    						document.forms['frgrm']['nIPARIva'].value  = 0;
    						document.forms['frgrm']['nIPARIca'].value  = 0;
    						document.forms['frgrm']['nIPAARFte'].value = 0;
    						document.forms['frgrm']['nIPAARCre'].value = 0;
    						document.forms['frgrm']['nIPAARIca'].value = 0;
    						document.forms['frgrm']['nIPASal'].value   = 0;
    						document.forms['frgrm']['cComSal'].value   = "";
    						document.forms['frgrm']['nIPAAnt'].value = eval(document.forms['frgrm']['nPCCAnt'].value);

    						fnAsignarValores();
    						document.forms['frestado'].target="fmpro";
    						document.forms['frestado'].action="frfacipa.php";
    						document.forms['frestado'].submit();
    					</script>
				    <?php break;
				    case "MANUAL":
							//Trayendo el total de DO factuados para asignarlo a nSecuencia_Dos
							$qDatDo  = "SELECT * ";
							$qDatDo .= "FROM $cAlfa.{$_POST['cTabla_DOS']} ";
							$qDatDo .= "WHERE ";
							$qDatDo .= "cUsrId_DOS = \"{$_COOKIE['kUsrId']}\" AND ";
							$qDatDo .= "cFacId_DOS = \"{$_POST['cFacId']}\" ";
							$qDatDo .= "ORDER BY  ABS(cSeq_DOS) ";
							$xDatDo  = f_MySql("SELECT","",$qDatDo,$xConexion01,"");
							//f_Mensaje(__FILE__,__LINE__,$qDatDo."~".mysql_num_rows($xDatDo));
							?>
				    	<script languaje = "javascript">
								document.forms['frgrm']['nSecuencia_Dos'].value = "<?php echo mysql_num_rows($xDatDo) ?>";
							</script>
				    	<?php
							//Se sebe borrar todo lo de la tabla de IPA
							//Borrando IP de la tabla
							$qDelete  = "DELETE FROM $cAlfa.{$_POST['cTabla_IPA']} ";
							$qDelete .= "WHERE ";
							$qDelete .= "cUsrId_IPA = \"{$_COOKIE['kUsrId']}\" AND ";
							$qDelete .= "cFacId_IPA = \"{$_POST['cFacId']}\" ";
							$xDelete  = mysql_query($qDelete,$xConexion01);
							//f_Mensaje(__FILE__, __LINE__,$qDelete);
							if (!$xDelete) {
								$nSwitch = 1;
								f_Mensaje(__FILE__, __LINE__, "Error al Inicializar Tabla Temporal de IPA");
							} ?>
				    	<script languaje = "javascript">
				    		//Cargando los datos de la tabla temporal de Ingresos Propios
				    		f_Add_New_Row_IPA();
				    		document.forms['frgrm']['nIPASub'].value   = 0;
    						document.forms['frgrm']['nIPAIva'].value   = 0;
    						document.forms['frgrm']['nIPATot'].value   = 0;
    						document.forms['frgrm']['nIPARFte'].value  = 0;
    						document.forms['frgrm']['nIPARCre'].value  = 0;
    						document.forms['frgrm']['nIPARIva'].value  = 0;
    						document.forms['frgrm']['nIPARIca'].value  = 0;
    						document.forms['frgrm']['nIPAARFte'].value = 0;
    						document.forms['frgrm']['nIPAARCre'].value = 0;
    						document.forms['frgrm']['nIPAARIca'].value = 0;
    						document.forms['frgrm']['nIPASal'].value   = 0;
    						document.forms['frgrm']['cComSal'].value   = "";
				    		document.forms['frgrm']['nIPAAnt'].value = eval(document.forms['frgrm']['nPCCAnt'].value);
				    		f_Cuadre_Debitos_Creditos_IPA("CUADREIPA",1);
					    </script>
				    <?php break;
				  }
				} ?>

				<script languaje = "javascript">
				  f_Mostrar_u_Ocultar_Objetos("<?php echo $_POST['cStep'] ?>");
				</script>

				<?php if ($_POST['cFacId'] == "") {
				  $ObjTabla  = new cEstructuras();

					##Creando tabla temporal si existe
					$vParametros = array();
					$vParametros['TIPOESTU'] = "GENERAL";
					##Instanciando Objetos para Crear Estructuras
					$mReturnTabla  = $ObjTabla->fnCrearEstructuras($vParametros);
					if($mReturnTabla[0] == "false") {
						$nSwitch = 1;
						for($nR=1;$nR<count($mReturnTabla);$nR++){
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				    	$cMsj .= $mReturnTabla[$nR]."\n";
						}
					}

					//Generando Id de transaccion del la factura
					//Se crea la secuancia 000 en la tabla de DO's para identificar el id del usuario en la seccion y reservarlo
					if ($nSwitch == 0) {
						$nAsigno = 0; $nCan = 0;
						while ($nAsigno == 0 && $nCan < 5) {
							$nCan++;
							$nFacId = rand(1000000000,9999999999);
							$qSelect  = "SELECT * ";
							$qSelect .= "FROM $cAlfa.$mReturnTabla[1] ";
							$qSelect .= "WHERE ";
							$qSelect .= "cUsrId = \"{$_COOKIE['kUsrId']}\" AND ";
							$qSelect .= "cFacId = \"$nFacId\" LIMIT 0,1 ";
							$xSelect  = f_MySql("SELECT","",$qSelect,$xConexion01,"");
							//f_Mensaje(__FILE__, __LINE__, $qSelect."~".mysql_num_rows($xSelect));
							if (mysql_num_rows($xSelect) == 0) {
								$qInsert = array(array('NAME'=>'cUsrId','VALUE'=>$_COOKIE['kUsrId'],'CHECK'=>'SI'),
														     array('NAME'=>'cFacId','VALUE'=>$nFacId				   ,'CHECK'=>'SI'));

								if (f_MySql("INSERT",$mReturnTabla[1],$qInsert,$xConexion01,$cAlfa)) {
									$nAsigno = 1;	?>
									<script languaje = "javascript">
										document.forms['frgrm']['cFacId'].value     = "<?php echo $nFacId ?>";
										document.forms['frgrm']['cTabla_GEN'].value = "<?php echo $mReturnTabla[1] ?>";
									</script>
								<?php }
							}
						}
						if ($nCan > 5) {
							//Supero los 5 Intentos
							$nSwitch = 1;
						}
					}

					if ($nSwitch == 0) {
						##Creando tabla temporal si existe
						$vParametros = array();
						$vParametros['TIPOESTU'] = "DOS";
						##Instanciando Objetos para Crear Estructuras
						$mReturnTabla  = $ObjTabla->fnCrearEstructuras($vParametros);
						if($mReturnTabla[0] == "false") {
							$nSwitch = 1;
							for($nR=1;$nR<count($mReturnTabla);$nR++){
								$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					    	$cMsj .= $mReturnTabla[$nR]."\n";
							}
						} else { ?>
							<script languaje = "javascript">
								document.forms['frgrm']['cTabla_DOS'].value = "<?php echo $mReturnTabla[1] ?>";
							</script>
						<?php }
					}

					if ($nSwitch == 0) {
						##Creando tabla temporal si existe
						$vParametros = array();
						$vParametros['TIPOESTU'] = "PCCA";
						##Instanciando Objetos para Crear Estructuras
						$mReturnTabla  = $ObjTabla->fnCrearEstructuras($vParametros);
						if($mReturnTabla[0] == "false") {
							$nSwitch = 1;
							for($nR=1;$nR<count($mReturnTabla);$nR++){
								$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					    	$cMsj .= $mReturnTabla[$nR]."\n";
							}
						} else { ?>
							<script languaje = "javascript">
								document.forms['frgrm']['cTabla_PCCA'].value = "<?php echo $mReturnTabla[1] ?>";
							</script>
						<?php }
					}

					if ($nSwitch == 0) {
						##Creando tabla temporal si existe
						$vParametros = array();
						$vParametros['TIPOESTU'] = "IPA";
						##Instanciando Objetos para Crear Estructuras
						$mReturnTabla  = $ObjTabla->fnCrearEstructuras($vParametros);
						if($mReturnTabla[0] == "false") {
							$nSwitch = 1;
							for($nR=1;$nR<count($mReturnTabla);$nR++){
								$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					    	$cMsj .= $mReturnTabla[$nR]."\n";
							}
						} else { ?>
							<script languaje = "javascript">
								document.forms['frgrm']['cTabla_IPA'].value = "<?php echo $mReturnTabla[1] ?>";
							</script>
						<?php }
					}

					if ($nSwitch == 0) {
						##Creando tabla temporal si existe
						$vParametros = array();
						$vParametros['TIPOESTU'] = "FACTURA";
						##Instanciando Objetos para Crear Estructuras
						$mReturnTabla  = $ObjTabla->fnCrearEstructuras($vParametros);
						if($mReturnTabla[0] == "false") {
							$nSwitch = 1;
							for($nR=1;$nR<count($mReturnTabla);$nR++){
								$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					    	$cMsj .= $mReturnTabla[$nR]."\n";
							}
						} else { ?>
							<script languaje = "javascript">
								document.forms['frgrm']['cTabla_FAC'].value = "<?php echo $mReturnTabla[1] ?>";
							</script>
						<?php }
					}

					if ($nSwitch == 0) {
						##Creando tabla temporal si existe
						$vParametros = array();
						$vParametros['TIPOESTU'] = "ANTICIPOS";
						##Instanciando Objetos para Crear Estructuras
						$mReturnTabla  = $ObjTabla->fnCrearEstructuras($vParametros);
						if($mReturnTabla[0] == "false") {
							$nSwitch = 1;
							for($nR=1;$nR<count($mReturnTabla);$nR++){
								$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					    	$cMsj .= $mReturnTabla[$nR]."\n";
							}
						} else { ?>
							<script languaje = "javascript">
								document.forms['frgrm']['cTabla_ANT'].value = "<?php echo $mReturnTabla[1] ?>";
							</script>
						<?php }
					}

					if ($nSwitch == 1) {
						f_Mensaje(__FILE__, __LINE__, "Error al Crear las tablas temporales.\n".$cMsj); ?>
						<script language = "javascript">
							f_Retorna();
						</script>
					<?php }
					##Fin Creando tabla temporal si existe
				}
			break;
			case "ANTERIOR": ?>
				<script languaje = "javascript">
					f_Add_New_Row_Comprobante();
					f_Links('cComCod','VALID');
				</script>
			<?php break;
			case "BORRAR":
				f_Carga_Data($gComId,$gComCod,$gComCsc,$gComCsc2,$gRegFCre); ?>
				<script languaje = "javascript">
					<?php if($vSysStr['system_activar_openetl'] == "SI") { ?>
						document.getElementById("Datos_adicionales_openetl").style.display="block";
					<?php } else { ?>
						document.getElementById("Datos_adicionales_openetl").style.display="none";
					<?php } ?>
					document.forms['frgrm'].target="fmpro";
					document.forms['frgrm'].action="frfacdel.php";
					document.forms['frgrm'].submit();
				</script>
				<?php
			break;
			case "VER":
				f_Carga_Data($gComId,$gComCod,$gComCsc,$gComCsc2,$gRegFCre); ?>
				<script languaje = "javascript">
					for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
          }
					<?php if($vSysStr['system_activar_openetl'] == "SI") { ?>
						document.getElementById("Datos_adicionales_openetl").style.display="block";
					<?php } else { ?>
						document.getElementById("Datos_adicionales_openetl").style.display="none";
					<?php } ?>
          document.getElementById('id_href_dRegFCre').href  = "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
					document.getElementById('idMedPag').href 					= "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
					<?php if($vSysStr['system_activar_openetl'] == 'SI'){ ?>
						document.getElementById('idFTasa').href 					= "javascript:alert('Opcion no Permitida en este Modo, Verifique.')";
					<?php } ?>
				</script>
			<?php break;
		} ?>

		<?php function f_Carga_Data($xComId,$xComCod,$xComCsc,$xComCsc2,$xRegFCre) {
		  global $xConexion01; global $cAlfa; global $vSysStr;
		  $cPerAno = substr($xRegFCre,0,4);

		  // Traigo los datos de la cabecera.
			$qConCab  = "SELECT * ";
			$qConCab .= "FROM $cAlfa.fcoc$cPerAno ";
			$qConCab .= "WHERE ";
			$qConCab .= "comidxxx = \"$xComId\"  AND ";
			$qConCab .= "comcodxx = \"$xComCod\" AND ";
			$qConCab .= "comcscxx = \"$xComCsc\" AND ";
			$qConCab .= "comcsc2x = \"$xComCsc2\" LIMIT 0,1";
			$xConCab  = f_MySql("SELECT","",$qConCab,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qConCab." ~ ".mysql_num_rows($xConCab));
			$vConCab  = mysql_fetch_array($xConCab);

			// Traigo los datos del detalle.
			$qConDet  = "SELECT $cAlfa.fcod$cPerAno.*,fpar0115.pucdetxx ";
			$qConDet .= "FROM $cAlfa.fcod$cPerAno ";
      $qConDet .= "LEFT JOIN $cAlfa.fpar0115 ON $cAlfa.fcod$cPerAno.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) ";
			$qConDet .= "WHERE ";
			$qConDet .= "$cAlfa.fcod$cPerAno.comidxxx = \"$xComId\"  AND ";
			$qConDet .= "$cAlfa.fcod$cPerAno.comcodxx = \"$xComCod\" AND ";
			$qConDet .= "$cAlfa.fcod$cPerAno.comcscxx = \"$xComCsc\" AND ";
			$qConDet .= "$cAlfa.fcod$cPerAno.comcsc2x = \"$xComCsc2\" ORDER BY ABS(comseqxx)";
	  	$xConDet = f_MySql("SELECT","",$qConDet,$xConexion01,"");
	  	// f_Mensaje(__FILE__,__LINE__,$qConDet." ~ ".mysql_num_rows($xConDet));
	  	$mConDet = array();
	  	while ($xRDF = mysql_fetch_array($xConDet)) {

	  		$nInd_RDF = count($mConDet); $mConDet[$nInd_RDF] = $xRDF;
	  	}

	  	// Busco los datos de la cabecera de la factura.

	  	// Busco la ciudad de la factura
	  	$qCiudad  = "SELECT * ";
	  	$qCiudad .= "FROM $cAlfa.fpar0008 ";
	  	$qCiudad .= "WHERE ";
	  	$qCiudad .= "ccoidxxx = \"{$vConCab['ccoidxxx']}\" LIMIT 0,1";
	  	$xCiudad  = f_MySql("SELECT","",$qCiudad,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qCiudad." ~ ".mysql_num_rows($xCiudad));
			$vCiudad  = mysql_fetch_array($xCiudad);

	  	// Pinto los datos del comprobante
			$qCiudades  = "SELECT ";
			$qCiudades .= "fpar0117.comidxxx,";
			$qCiudades .= "fpar0117.comcodxx,";
			$qCiudades .= "fpar0008.sucidxxx,";
			$qCiudades .= "fpar0117.ccoidxxx,";
			$qCiudades .= "fpar0117.comdesxx ";
			$qCiudades .= "FROM $cAlfa.fpar0117,$cAlfa.fpar0008 ";
			$qCiudades .= "WHERE ";
			$qCiudades .= "fpar0117.ccoidxxx = fpar0008.ccoidxxx AND ";
			$qCiudades .= "fpar0117.comidxxx = \"F\" AND ";
			$qCiudades .= "fpar0117.comidxxx = \"$xComId\" AND ";
			$qCiudades .= "fpar0117.comcodxx = \"$xComCod\" AND ";
			$qCiudades .= "fpar0117.regestxx = \"ACTIVO\" ";
			$qCiudades .= "ORDER BY fpar0117.comcodxx  LIMIT 0,1";
			$xCiudades  = f_MySql("SELECT","",$qCiudades,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qCiudades." ~ ".mysql_num_rows($xCiudades));

			$i = 0;
			while ($xRC = mysql_fetch_array($xCiudades)) {
			  // Busco la resolucion en la fpar0138, si no hay resolucion no pinto el registro en el combo.
			  $qResFac  = "SELECT residxxx,restipxx ";
			  $qResFac .= "FROM $cAlfa.fpar0138 ";
			  $qResFac .= "WHERE ";
			  $qResFac .= "rescomxx LIKE \"%{$xRC['comidxxx']}~{$xRC['comcodxx']}%\" AND ";
			  $qResFac .= "regestxx = \"ACTIVO\" LIMIT 0,1";
			  $xResTip  = f_MySql("SELECT","",$qResFac,$xConexion01,"");
			  //f_Mensaje(__FILE__,__LINE__,$qResFac." ~ ".mysql_num_rows($xResTip));
			  if (mysql_num_rows($xResTip) == 1) {
			    $vResTip  = mysql_fetch_array($xResTip);
					// Busco la sucursal y el centro de costo en la tabla SIAI0055 para traer el PAIS, DEPARTAMENTO y CIUDAD.
			    $qTarifaIca = "SELECT * FROM $cAlfa.SIAI0055 WHERE SUCIDXXX = \"{$xRC['sucidxxx']}\" AND CCOIDXXX = \"{$xRC['ccoidxxx']}\" AND REGESTXX = \"ACTIVO\" LIMIT 0,1";
			    $xTarifaIca = f_MySql("SELECT","",$qTarifaIca,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qTarifaIca." ~ ".mysql_num_rows($xTarifaIca));
					$vTarifaIca = mysql_fetch_array($xTarifaIca); ?>
			    <script language="javascript">
			    	document.forms['frgrm']['cComDes'].options.length = 0;
			    	document.forms['frgrm']['cComDes'].options[<?php echo $i; ?>] = new Option("<?php echo $xRC['comdesxx'].' '.$vResTip['restipxx'] ?>","<?php echo $xRC['comidxxx'].'~'.$xRC['comcodxx'].'~'.$xRC['sucidxxx'].'~'.$xRC['ccoidxxx'].'~'.$vResTip['residxxx'].'~'.$vResTip['restipxx'].'~'.$vTarifaIca['SUCIDXXX'].'~'.$vTarifaIca['CIUICAXX'].'~'.$vTarifaIca['PUCIDXXX'].'~'.$vTarifaIca['CIUICA2X'].'~'.$vTarifaIca['PUCID2XX'] ?>");
			    </script>
			  <?php }
			}

			// Busco el datos de tipo de factura
			$vExtras = explode("~",$vConCab['comobs2x']);

			//Mostrando Datos de integracion con openETL
			if($vSysStr['system_activar_openetl'] == "SI") {
				//Buscando descripcion Medio de Pago
				if ($vExtras[15] != "") {
					$qMedPag  = "SELECT ";
					$qMedPag .= "mpaidxxx, ";
					$qMedPag .= "mpadesxx, ";
					$qMedPag .= "regestxx ";
					$qMedPag .= "FROM $cAlfa.fpar0155 ";
					$qMedPag .= "WHERE ";
					$qMedPag .= "mpaidxxx = \"{$vExtras[15]}\" LIMIT 0,1";
					$xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qMedPag."~ ".mysql_num_rows($xMedPag));
					$vMedPag = mysql_fetch_array($xMedPag);
				} ?>

				<script language="javascript">
          fnCargarTipoFacturaFE('<?php echo $vConCab['comidxxx'] ?>', '<?php echo $vConCab['comcodxx'] ?>', '<?php echo $vExtras[12] ?>');
					document.forms['frgrm']['cTopId'].value  	 = "<?php echo $vExtras[13] ?>";
					document.forms['frgrm']['cComFpag'].value  = "<?php echo $vExtras[14] ?>";
					document.forms['frgrm']['cMePagId'].value  = "<?php echo $vExtras[15] ?>";
					document.forms['frgrm']['cMePagDes'].value = "<?php echo $vMedPag['mpadesxx'] ?>";
				</script>

				<script language="javascript">
					document.forms['frgrm']['cOrdenCompra'].value = "<?php echo $vExtras[27] ?>";
				</script>
			<?php }

			$cTerCal = "";
			// Busco los datos del cliente
	  	$qCliente  = "SELECT ";
	  	$qCliente .= "$cAlfa.SIAI0150.*, ";
	  	$qCliente .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX ";
	  	$qCliente .= "FROM $cAlfa.SIAI0150 ";
	  	$qCliente .= "WHERE ";
	  	$qCliente .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vConCab['teridxxx']}\" LIMIT 0,1";
	  	$xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
	  	//f_Mensaje(__FILE__,__LINE__,$qCliente." ~ ".mysql_num_rows($xCliente));
	  	$vCliente  = mysql_fetch_array($xCliente);

			/*Inicio Nuevo Codigo para Asignar la Calidad del Tercero*/
			if ($vCliente['CLIRECOM']=="SI" && $vCliente['CLIGCXXX']!="SI") {
				$cTerCal = "COMUN";
			} else {
				$cTerCal = "CONTRIBUYENTE";
			}
			if ($vCliente['CLIRESIM']=="SI") {
				$cTerCal = "SIMPLIFICADO";
			}
			if ($vCliente['CLIGCXXX']=="SI") {
				$cTerCal = "CONTRIBUYENTE";
			}
			if ($vCliente['CLINRPXX']=="SI") {
				$cTerCal = "NORESIDENTE";
			}
			/*Fin Nuevo Codigo para Asignar la Calidad del Tercero*/

	  	$qConCom = "SELECT * FROM $cAlfa.fpar0151 WHERE cliidxxx = \"{$vCliente['CLIIDXXX']}\" LIMIT 0,1";
	  	$xConCom = f_MySql("SELECT","",$qConCom,$xConexion01,"");
	  	//f_Mensaje(__FILE__,__LINE__,$qConCom." ~ ".mysql_num_rows($xConCom));
	  	$vConCom = mysql_fetch_array($xConCom);

      /**
       * Ticket 21716 - Johana Arboleda Ramos 2016-04-17
       * - En el campo comobs2x se guarda en la posicion [10] el plazo utilizado para la factura, si no existe este valor,
       *   se aplica la siguiente logica:
       * - Si el tipo de cobro es TODO o PCC se trae el plazo del campo cccplaxx
       * - Si el tipo de cobro es IP, se trae el plazo del campo cccplaip, pero si este campo es vacio se trae el valor del campo cccplaxx
       */
      $cPlaTer = ($vExtras[10] != "") ? $vExtras[10] : (($vExtras[5] == "TODO" || $vExtras[5] == "PCC") ? $vConCom['cccplaxx'] : (($vConCom['cccplaip'] != "") ? $vConCom['cccplaip'] : $vConCom['cccplaxx']));

	  	if ($vConCab['teridxxx'] == $vConCab['terid2xx']) {
	  		$vCliente['CLIIDINT']  = $vCliente['CLIIDXXX'];
	  		$vCliente['CLINOMINT'] = $vCliente['CLINOMXX'];
	  	} else {
	  		// Busco los datos del intermediario
		  	$qIntermediario  = "SELECT ";
		  	$qIntermediario .= "$cAlfa.SIAI0150.*, ";
		  	$qIntermediario .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX ";
		  	$qIntermediario .= "FROM $cAlfa.SIAI0150 ";
		  	$qIntermediario .= "WHERE ";
		  	$qIntermediario .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vConCab['terid2xx']}\" LIMIT 0,1";
		  	$xIntermediario  = f_MySql("SELECT","",$qIntermediario,$xConexion01,"");
		  	//f_Mensaje(__FILE__,__LINE__,$qIntermediario." ~ ".mysql_num_rows($xIntermediario));
        $vIntermediario  = mysql_fetch_array($xIntermediario);
        
        /*Inicio Nuevo Codigo para Asignar la Calidad del Tercero*/
        if ($vIntermediario['CLIRECOM']=="SI" && $vIntermediario['CLIGCXXX']!="SI") {
          $cTerCalInt = "COMUN";
        } else {
          $cTerCalInt = "CONTRIBUYENTE";
        }
        if ($vIntermediario['CLIRESIM']=="SI") {
          $cTerCalInt = "SIMPLIFICADO";
        }
        if ($vIntermediario['CLIGCXXX']=="SI") {
          $cTerCalInt = "CONTRIBUYENTE";
        }
        if ($vIntermediario['CLINRPXX']=="SI") {
          $cTerCalInt = "NORESIDENTE";
        }
        /*Fin Nuevo Codigo para Asignar la Calidad del Tercero*/

        //Si la calidad del intermediario es NORESIDENTE o financiero_facturacion_aplica_impuestos_facturar_a es igual a SI, se aplica la calidad del intermediario
        if ($cTerCalInt == "NORESIDENTE" || $vSysStr['financiero_facturacion_aplica_impuestos_facturar_a'] == 'SI') {
          $cTerCal = $cTerCalInt;
        }
      }

      //Recalcular los valores en USD
      $cMonId = ($vExtras[16] != "") ? $vExtras[16] : "COP";
      if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP"|| 
          $cAlfa == "ROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "DEROLDANLO") {
        //Si la moneda es en dolares se debe calcular el valor en dolares
        $cFacUSD = ($cMonId == "USD") ? "SI" : "NO"; // Factura en Dolares
      } else {
        //Si es no residente en el pais se debe calcular el valor en dolares
        $cFacUSD = ($cTerCal == "NORESIDENTE") ? "SI" : "NO"; // Factura en Dolares
      }
      ?>

			<script language="javascript">
				document.forms['frgrm']['cComId'].value     = "<?php echo $vConCab['comidxxx'] ?>";
				document.forms['frgrm']['cComCod'].value    = "<?php echo $vConCab['comcodxx'] ?>";
				document.forms['frgrm']['cComCsc'].value    = "<?php echo $vConCab['comcscxx'] ?>";
				document.forms['frgrm']['cComCsc2'].value   = "<?php echo $vConCab['comcsc2x'] ?>";
				document.forms['frgrm']['cComTFa'].value    = "<?php echo $vExtras[0] ?>";
				document.forms['frgrm']['cComTipCsc'].value = "<?php echo ($vConCab['regestxx'] == "PROVISIONAL") ? "PREFACTURA" : "DEFINITIVO" ?>";
				document.forms['frgrm']['dRegFCre'].value   = "<?php echo $vConCab['comfecxx'] ?>";
        document.forms['frgrm']['cComTCo'].value    = "<?php echo $vExtras[5] ?>";
        document.forms['frgrm']['cMonId'].value     = "<?php echo ($vExtras[16] != "") ? $vExtras[16] : "COP" ?>";
        document.forms['frgrm']['cForImp'].value    = "<?php echo $vExtras[9] ?>";
				document.forms['frgrm']['cComObs'].value    = "<?php echo $vConCab['comobsxx'] ?>";
				document.forms['frgrm']['cTerId'].value     = "<?php echo $vCliente['CLIIDXXX'] ?>";
				document.forms['frgrm']['cTerDV'].value     = "<?php echo f_Digito_Verificacion($vCliente['CLIIDXXX']) ?>";
				document.forms['frgrm']['cTerNom'].value    = "<?php echo $vCliente['CLINOMXX'] ?>";
				document.forms['frgrm']['cTerIdInt'].value  = "<?php echo ($vConCab['teridxxx'] == $vConCab['terid2xx']) ? $vCliente['CLIIDXXX'] : $vIntermediario['CLIIDXXX']; ?>";
				document.forms['frgrm']['cTerCal'].value    = "<?php echo $cTerCal ?>";
				document.forms['frgrm']['cTerRSt'].value    = "<?php echo $vCliente['CLIREGST'] ?>";
				document.forms['frgrm']['cTerRFte'].value   = "<?php echo $vCliente['CLIARRXX'] ?>";
				document.forms['frgrm']['cTerRCre'].value   = "<?php echo $vCliente['CLIARCRX'] ?>";
				document.forms['frgrm']['cTerCInt'].value   = "<?php echo $vCliente['CLIPCIXX'] ?>";
				document.forms['frgrm']['cTerRIca'].value   = "<?php echo $vCliente['CLIARRIX'] ?>";
				document.forms['frgrm']['cTerAIva'].value   = "<?php echo $vCliente['CLINRPAI'] ?>";
				document.forms['frgrm']['cTerAIf'].value    = "<?php echo $vCliente['CLINRPIF'] ?>";
				document.forms['frgrm']['cTerSIca'].value   = "<?php echo $vCliente['CLIARRIS'] ?>";
				document.forms['frgrm']['cTerDVInt'].value  = "<?php echo f_Digito_Verificacion(($vConCab['teridxxx'] == $vConCab['terid2xx']) ? $vCliente['CLIIDXXX'] : $vIntermediario['CLIIDXXX']) ?>";
				document.forms['frgrm']['cTerNomInt'].value = "<?php echo ($vConCab['teridxxx'] == $vConCab['terid2xx']) ? $vCliente['CLINOMXX'] : $vIntermediario['CLINOMXX']; ?>";
				document.forms['frgrm']['cTerDir'].value    = "<?php echo ($vConCab['teridxxx'] == $vConCab['terid2xx']) ? $vCliente['CLIDIRXX'] : $vIntermediario['CLIDIRXX']; ?>";
				document.forms['frgrm']['cTerTel'].value    = "<?php echo ($vConCab['teridxxx'] == $vConCab['terid2xx']) ? $vCliente['CLITELXX'] : $vIntermediario['CLITELXX']; ?>";
				document.forms['frgrm']['cTerFax'].value    = "<?php echo ($vConCab['teridxxx'] == $vConCab['terid2xx']) ? $vCliente['CLIFAXXX'] : $vIntermediario['CLIFAXXX']; ?>";
				document.forms['frgrm']['cTerCalInt'].value = "<?php echo $cTerCal; ?>";
				document.forms['frgrm']['nTasaCambio'].value= "<?php echo round($vConCab['tcatasax'],2) ?>";
				document.forms['frgrm']['dFechaProm'].value = "<?php echo $vConCab['tcafecpx'] ?>";
				document.forms['frgrm']['cTerPla'].value    = "<?php echo $cPlaTer ?>";
				document.forms['frgrm']['cTerEma'].value    = "<?php echo $vCliente['CLIEMAXX'] ?>";
				document.forms['frgrm']['cTerAnt'].value    = "<?php echo $vConCom['cccantxx'] ?>";
				document.forms['frgrm']['cTerGru'].value    = "<?php echo ($vCliente['CLIGRUXX'] != "") ? $vCliente['CLIGRUXX'] : "SIN GRUPO"; ?>";
				document.forms['frgrm']['cCccAIF'].value    = "<?php echo $vConCom['cccifaxx'] ?>";
				document.forms['frgrm']['cCccIFA'].value    = "<?php echo $vConCom['cccifvxx'] ?>";
				document.forms['frgrm']['nCscPro'].value    = "<?php echo $vExtras[6] ?>";
			</script>

			<?php
			// Pinto los DO's facturados.

			$mTramites = f_Explode_Array($vConCab['comfpxxx'],"|","~");
			for ($i=0;$i<count($mTramites);$i++) {
				$qTramites  = "SELECT * ";
				$qTramites .= "FROM $cAlfa.sys00121 ";
				$qTramites .= "WHERE ";
			  $qTramites .= "sucidxxx = \"{$mTramites[$i][15]}\" AND ";
			  $qTramites .= "docidxxx = \"{$mTramites[$i][2]}\"  AND ";
			  $qTramites .= "docsufxx = \"{$mTramites[$i][3]}\"  LIMIT 0,1";
				$xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
				$vTramites  = mysql_fetch_array($xTramites);

			  //Busco la el nombre del cliente
        $qSqlCli  = "SELECT ";
        $qSqlCli .= "$cAlfa.SIAI0150.*, ";
        $qSqlCli .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
        $qSqlCli .= "FROM $cAlfa.SIAI0150 ";
        $qSqlCli .= "WHERE ";
        $qSqlCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vTramites['cliidxxx']}\" LIMIT 0,1";
        $xSqlCli  = f_MySql("SELECT","",$qSqlCli,$xConexion01,"");
        if(mysql_num_rows($xSqlCli) > 0) {
          $zRCli = mysql_fetch_array($xSqlCli);
          $vTramites['clinomxx'] = $zRCli['CLINOMXX'];
        } else {
          $vTramites['clinomxx'] = "CLIENTE SIN NOMBRE";
        }

				?>
				<script languaje = "javascript">
					f_Add_New_Row_Tramites();
					document.forms['frgrm']['cSeq_DOS'   +document.forms['frgrm']['nSecuencia_Dos'].value].value     = f_Str_Pad(document.forms['frgrm']['nSecuencia_Dos'].value,3,"0","STR_PAD_LEFT");
					document.forms['frgrm']['cSucId_DOS' +document.forms['frgrm']['nSecuencia_Dos'].value].value     = '<?php echo $mTramites[$i][15]  ?>';
					document.forms['frgrm']['cDosNro_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value].value     = '<?php echo $mTramites[$i][2] ?>';
	  			document.forms['frgrm']['cDosSuf_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value].value     = '<?php echo $mTramites[$i][3] ?>';
	  			document.getElementById('cDosTip_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).innerHTML = '<?php echo $mTramites[$i][4] ?>';
	  			document.getElementById('cDosMtr_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).innerHTML = '<?php echo $mTramites[$i][5] ?>';
	  			document.getElementById('cDosFec_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).innerHTML = '<?php echo $mTramites[$i][6] ?>';
	  			document.forms['frgrm']['cDosPed_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value].value     = '<?php echo $mTramites[$i][7] ?>';
	  			document.getElementById('nDosVlr_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).innerHTML = '<?php echo $mTramites[$i][8] ?>';
	  			document.getElementById('cDosFor_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).innerHTML = '<?php echo $mTramites[$i][9] ?>';
	  			document.getElementById('cDosRec_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).innerHTML = '<?php echo $mTramites[$i][10] ?>';
	  			document.forms['frgrm']['cDosCE_DOS' +document.forms['frgrm']['nSecuencia_Dos'].value].value     = '<?php echo $mTramites[$i][11]  ?>';

	  			if ('<?php echo $mTramites[$i][4] ?>' == "red") {
						var cBgColor = "red";
						var cColor   = "#FFFFFF";
					} else {
						var cBgColor = "#FFFFFF";
						var cColor   = "black";
					}

					document.getElementById('cSeq_DOS'   +document.forms['frgrm']['nSecuencia_Dos'].value).style.color = cColor;
					document.getElementById('cSeq_DOS'   +document.forms['frgrm']['nSecuencia_Dos'].value).style.backgroundColor = cBgColor;
					document.getElementById('cSucId_DOS' +document.forms['frgrm']['nSecuencia_Dos'].value).style.color = cColor;
					document.getElementById('cSucId_DOS' +document.forms['frgrm']['nSecuencia_Dos'].value).style.backgroundColor = cBgColor;
					document.getElementById('cDosNro_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.color = cColor;
					document.getElementById('cDosNro_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.backgroundColor = cBgColor;
	  			document.getElementById('cDosSuf_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.color = cColor;
	  			document.getElementById('cDosSuf_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.backgroundColor = cBgColor;
	  			document.getElementById('cDosTip_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.color = cColor;
	  			document.getElementById('cDosTip_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.backgroundColor = cBgColor;
	  			document.getElementById('cDosMtr_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.color = cColor;
	  			document.getElementById('cDosMtr_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.backgroundColor = cBgColor;
	  			document.getElementById('cDosFec_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.color = cColor;
	  			document.getElementById('cDosFec_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.backgroundColor = cBgColor;
	  			document.getElementById('cDosPed_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.color = cColor;
	  			document.getElementById('cDosPed_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.backgroundColor = cBgColor;
	  			document.getElementById('nDosVlr_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.color = cColor;
	  			document.getElementById('nDosVlr_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.backgroundColor = cBgColor;
	  			document.getElementById('cDosFor_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.color = cColor;
	  			document.getElementById('cDosFor_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.backgroundColor = cBgColor;
	  			document.getElementById('cDosRec_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.color = cColor;
	  			document.getElementById('cDosRec_DOS'+document.forms['frgrm']['nSecuencia_Dos'].value).style.backgroundColor = cBgColor;
	  			document.getElementById('cDosCE_DOS' +document.forms['frgrm']['nSecuencia_Dos'].value).style.color = cColor;
	  			document.getElementById('cDosCE_DOS' +document.forms['frgrm']['nSecuencia_Dos'].value).style.backgroundColor = cBgColor;

					<?php
					if ($cAlfa == 'DHLEXPRE' || $cAlfa == 'DEDHLEXPRE' || $cAlfa == 'TEDHLEXPRE') {
						$qObsDo  = "SELECT ";
						$qObsDo .= "$cAlfa.sys00017.sucidxxx, ";
						$qObsDo .= "$cAlfa.sys00017.docidxxx, ";
						$qObsDo .= "$cAlfa.sys00017.docsufxx, ";
						$qObsDo .= "$cAlfa.sys00017.obsobsxx, ";
						$qObsDo .= "$cAlfa.sys00017.obsobs2x, ";
						$qObsDo .= "$cAlfa.sys00017.regfcrex, ";
						$qObsDo .= "$cAlfa.sys00017.reghcrex, ";
	 					$qObsDo .= "IF($cAlfa.sys00015.obgdesxx != \"\",$cAlfa.sys00015.obgdesxx,\"GRUPO SIN DESCRIPCION\") AS obgdesxx, ";
	 					$qObsDo .= "IF($cAlfa.sys00016.obsdesxx != \"\",$cAlfa.sys00016.obsdesxx,\"GRUPO SIN DESCRIPCION\") AS obsdesxx, ";
	 					$qObsDo .= "$cAlfa.SIAI0003.USRNOMXX ";
            $qObsDo .= "FROM $cAlfa.sys00017 ";
	 					$qObsDo .= "LEFT JOIN $cAlfa.sys00015 ON $cAlfa.sys00015.obgidxxx = $cAlfa.sys00017.obgidxxx ";
						$qObsDo .= "LEFT JOIN $cAlfa.sys00016 ON $cAlfa.sys00016.obgidxxx = $cAlfa.sys00017.obgidxxx AND $cAlfa.sys00016.obsidxxx = $cAlfa.sys00017.obsidxxx ";
      			$qObsDo .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.SIAI0003.USRIDXXX = $cAlfa.sys00017.regusrxx ";
						$qObsDo .= "WHERE ";
						$qObsDo .= "$cAlfa.sys00017.docidxxx = \"{$mTramites[$i][2]}\" AND ";
						$qObsDo .= "$cAlfa.sys00017.sucidxxx = \"{$mTramites[$i][15]}\" AND ";
						$qObsDo .= "$cAlfa.sys00017.docsufxx = \"{$mTramites[$i][3]}\" AND ";
						$qObsDo .= "$cAlfa.sys00017.obgidxxx = \"{$vSysStr['dhlexpre_grupo_observacion_facturacion']}\" AND ";
						$qObsDo .= "$cAlfa.sys00017.regestxx = \"ACTIVO\" ";
						$qObsDo .= "ORDER BY $cAlfa.sys00017.regfcrex DESC ";
						$xObsDo  = f_MySql("SELECT","",$qObsDo,$xConexion01,"");
						// f_Mensaje(__FILE__,__LINE__,$qObsDo."~".mysql_num_rows($xObsDo));

						if (mysql_num_rows($xObsDo) > 0) { ?>
							document.getElementById("Tramites_Observaciones").style.display="block";
              <?php
              $cTable = '';
              $nSecuenciaObs = 0;
              while ($xROD = mysql_fetch_array($xObsDo)) {
                $nSecuenciaObs++; 
                $idSecObsDo = $xROD['sucidxxx'] . "-" . $xROD['docidxxx'] . "-" . $xROD['docsufxx'] . "-" . $nSecuenciaObs; ?>
                  // Se valida que no exista la misma secuencia de la observacion para el DO
                  var oObsDo = document.getElementById('<?php echo $idSecObsDo ?>');
                  if (oObsDo == null) {
                    var cGridObs  = document.getElementById("Grid_Tramites_Observaciones");
                    var nLastRow  = cGridObs.rows.length;
                    var cTableRow = cGridObs.insertRow(nLastRow);

                    var TD_xAll = cTableRow.insertCell(0);
                    TD_xAll.innerHTML = "<table border = \"1\" cellpadding = \"0\" cellspacing = \"0\" width=\"960\" id=\"<?php echo $idSecObsDo ?>\">"+
                      "<tr bgcolor=\"#CEE3F6\" height=\"15\">"+
                        "<td Class = \"name\" width=\"80\">&nbsp;<b>DO:</b></td>"+
                        "<td colspan=\"5\">&nbsp;<b><?php echo $xROD['sucidxxx'] . "-" . $xROD['docidxxx'] . "-" . $xROD['docsufxx'] ?></b></td>"+
                      "</tr>"+
                      "<tr height=\"15\">"+
                        "<td Class = \"name\" width=\"80\">&nbsp;<b>Usuario:</b></td>"+
                        "<td>&nbsp;<?php echo str_replace('"','\"',$xROD['USRNOMXX']) ?></td>"+
                        "<td Class = \"name\" width=\"80\">&nbsp;<b>Fecha:</b></td>"+
                        "<td width=\"80\">&nbsp;<?php echo $xROD['regfcrex'] ?></td>"+
                        "<td Class = \"name\" width=\"60\">&nbsp;<b>Hora:</b></td>"+
                        "<td width=\"80\">&nbsp;<?php echo $xROD['reghcrex'] ?></td>"+
                      "</tr>"+
                      "<tr height=\"15\">"+
                        "<td Class = \"name\">&nbsp;<b>Grupo:</b></td>"+
                        "<td colspan=\"5\">&nbsp;<?php echo str_replace('"','\"',$xROD['obgdesxx']) ?></td>"+
                      "</tr>"+
                      "<tr height=\"15\">"+
                        "<td Class = \"name\">&nbsp;<b>Subgrupo:</b></td>"+
                        "<td colspan=\"5\">&nbsp;<?php echo str_replace('"','\"',$xROD['obsdesxx']) ?></td>"+
                      "</tr>"+
                      "<tr height=\"15\">"+
                        "<td Class = \"name\">&nbsp;<b>Ver Cliente:</b></td>"+
                        "<td colspan=\"5\">&nbsp;<?php echo str_replace('"','\"',$xROD['obsobs2x']) ?></td>"+
                      "</tr>"+
                      "<tr height=\"15\">"+
                        "<td colspan=\"8\" Class = \"name\">&nbsp;<b>Observacion:</b></td>"+
                      "</tr>"+
                      "<tr>"+
                        "<td colspan=\"8\" style=\"padding-left:4px\">&nbsp;<?php echo str_replace('"','\"',$xROD['obsobsxx']) ?></td>"+
                      "</tr>"+
                    "</table><br>";
                  }
              <?php }
            }
					} ?>
				</script>
			<?php }

			// Pinto los pagos por cuenta del cliente.
			$nDebitos = 0; $nCreditos = 0;
			$mPCC = f_Explode_Array($vConCab['commemod'],"|","~");

			for ($i=0;$i<count($mPCC);$i++) { ?>
				<script languaje = "javascript">
					f_Add_New_Row_PCCA();
					document.forms['frgrm']['cComSeq_PCCA'  +document.forms['frgrm']['nSecuencia_PCCA'].value].value     = f_Str_Pad(document.forms['frgrm']['nSecuencia_PCCA'].value,3,"0","STR_PAD_LEFT");
			    document.getElementById('cComId_PCCA'   +document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo $mPCC[$i][1]      ?>";
					document.forms['frgrm']['cComObs_PCCA'  +document.forms['frgrm']['nSecuencia_PCCA'].value].value     = "<?php echo $mPCC[$i][2]      ?>";
					document.getElementById('cComTra_PCCA'  +document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo $mPCC[$i][14]     ?>";
					document.getElementById('cComId3_PCCA'  +document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo $mPCC[$i][3]      ?>";
					document.getElementById('cComCod3_PCCA' +document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo $mPCC[$i][4]      ?>";
					document.forms['frgrm']['cComCsc3_PCCA' +document.forms['frgrm']['nSecuencia_PCCA'].value].value     = "<?php echo $mPCC[$i][5]      ?>";
					document.getElementById('cComSeq3_PCCA' +document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo $mPCC[$i][6]      ?>";
					document.getElementById('cComDocIn_PCCA'+document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo $mPCC[$i][21]     ?>";
					document.getElementById('nComVlr_PCCA'  +document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo ($mPCC[$i][7]+0)  ?>";
					document.getElementById('nComVlrNF_PCCA'+document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo ($mPCC[$i][22]+0) ?>";
					document.forms['frgrm']['cComMov_PCCA'  +document.forms['frgrm']['nSecuencia_PCCA'].value].value 		 = "<?php echo $mPCC[$i][8]      ?>";
				</script>
				<?php
				$nComVlr_PCC = ($mPCC[$i][24] == "L" || $mPCC[$i][24] == "") ? $mPCC[$i][7] : $mPCC[$i][22];
				switch ($mPCC[$i][8]) {
				  case "D": $nDebitos_PCC  += $nComVlr_PCC; break;
				  case "C": $nCreditos_PCC += $nComVlr_PCC; break;
        }
			}

			for ($i=0;$i<count($mConDet);$i++) {
				if ($mConDet[$i]['comctocx'] == "PCC") { ?>
					<script languaje = "javascript">
						f_Add_New_Row_PCCA();
						document.forms['frgrm']['cComSeq_PCCA'  +document.forms['frgrm']['nSecuencia_PCCA'].value].value     = f_Str_Pad(document.forms['frgrm']['nSecuencia_PCCA'].value,3,"0","STR_PAD_LEFT");
				    document.getElementById('cComId_PCCA'   +document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo $mConDet[$i]['ctoidxxx']     ?>";
						document.forms['frgrm']['cComObs_PCCA'  +document.forms['frgrm']['nSecuencia_PCCA'].value].value     = "<?php echo $mConDet[$i]['comobsxx']     ?>";
						document.getElementById('cComTra_PCCA'  +document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo $mConDet[$i]['comtraxx']     ?>";
						document.getElementById('cComId3_PCCA'  +document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo $mConDet[$i]['comidc2x']     ?>";
						document.getElementById('cComCod3_PCCA' +document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo $mConDet[$i]['comcodc2']     ?>";
						document.forms['frgrm']['cComCsc3_PCCA' +document.forms['frgrm']['nSecuencia_PCCA'].value].value     = "<?php echo $mConDet[$i]['comcscc2']     ?>";
						document.getElementById('cComSeq3_PCCA' +document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo $mConDet[$i]['comseqc2']     ?>";
						document.getElementById('cComDocIn_PCCA'+document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "";
						document.getElementById('nComVlr_PCCA'  +document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo ($mConDet[$i]['comvlrxx']+0) ?>";
						document.getElementById('nComVlrNF_PCCA'+document.forms['frgrm']['nSecuencia_PCCA'].value).innerHTML = "<?php echo ($mConDet[$i]['comvlrnf']+0) ?>";
						document.forms['frgrm']['cComMov_PCCA'  +document.forms['frgrm']['nSecuencia_PCCA'].value].value 		 = "<?php echo  $mConDet[$i]['commovxx']    ?>";
					</script>
  				<?php
  				$nComVlr_PCC = ($mConDet[$i]['puctipej'] == "L" || $mConDet[$i]['puctipej'] == "") ? $mConDet[$i]['comvlrxx'] : $mConDet[$i]['comvlrnf'];
  				switch ($mConDet[$i]['commovxx']) {
  				  case "D": $nDebitos_PCC  += $nComVlr_PCC; break;
  				  case "C": $nCreditos_PCC += $nComVlr_PCC; break;
          }
        }
			} ?>

			<script languaje = "javascript">
				document.forms['frgrm']['nPCCAnt'].value = "<?php echo round($vConCab['comvlr01'],2)+0 ?>";
				document.forms['frgrm']['nPCCDeb'].value = "<?php echo round($nDebitos_PCC,2)+0 ?>";
				document.forms['frgrm']['nPCCCre'].value = "<?php echo round($nCreditos_PCC,2)+0 ?>";
				document.forms['frgrm']['nPCCVNe'].value = "<?php echo round($nDebitos_PCC+$nCreditos_PCC,2)+0 ?>";
			</script>

			<?php // Pinto los Ingresos Propios.
			$nSubTotal_IP = 0; $nIvaIP = 0; $nRetFte = 0; $nRetCre = 0; $nRetIva = 0; $nRetIca = 0; $nARetFte = 0; $nARetCre = 0; $nARetIca = 0;
			for ($i=0;$i<count($mConDet);$i++) {
				switch ($mConDet[$i]['comctocx']) {
					case "IVAIP":   $nIvaIP   += $mConDet[$i]['comvlrxx']; break;
					case "RETFTE":  $nRetFte  += $mConDet[$i]['comvlrxx']; break;
					case "ARETFTE": $nARetFte += $mConDet[$i]['comvlrxx']; break;
					case "RETCRE":  $nRetCre  += $mConDet[$i]['comvlrxx']; break;
					case "ARETCRE": $nARetCre += $mConDet[$i]['comvlrxx']; break;
					case "RETIVA":  $nRetIva  += $mConDet[$i]['comvlrxx']; break;
					case "RETICA":  $nRetIca  += $mConDet[$i]['comvlrxx']; break;
					case "ARETICA": $nARetIca += $mConDet[$i]['comvlrxx']; break;
          case "IP":
            
            $nComCan  = "";
            $nComVlrU = "";
            if ($cAlfa == "GRUMALCO" || $cAlfa == "TEGRUMALCO") {
              //Si la factura es manual, se trae la cantidad de la fcodYYYY
              //Si es automatica, se trae la cantidad de la descripcion
              if ($vExtras[0] == "MANUAL") {
                //Cantidad
                $nComCan  = (($mConDet[$i]['comcanxx']+0) > 0) ? ($mConDet[$i]['comcanxx']+0) : 1;
              } else {
                //Trayendo cantidad y unidad de medida desde la descripcion
                $vDatosIp = array();
                $vDatosIp = f_Cantidad_Ingreso_Propio($mConDet[$i]['comobsxx'],'', $mConDet[$i]['sucidxxx'],$mConDet[$i]['docidxxx'],$mConDet[$i]['docsufxx']);
                //Canitdad
                $nComCan  = ($vDatosIp[2] != "A9" && $vDatosIp[1] > 0) ? $vDatosIp[1] : 1;
              }
              //Calculando valor unitario
              $nValor = ($mConDet[$i]['puctipej'] == "L" || $vIPAUni['puctipej'] == "") ? ($mConDet[$i]['comvlrxx']+0) : ($mConDet[$i]['comvlrnf']+0); 
              if ($cFacUSD == "SI") {
                $nComVlrU = ($mConDet[$i]['puctipej'] == "L" || $mConDet[$i]['puctipej'] == "") ? round(($nValor/$nComCan)*100)/100 : 0;
              } else {
                $nComVlrU = ($mConDet[$i]['puctipej'] == "L" || $mConDet[$i]['puctipej'] == "") ? round($nValor/$nComCan) : 0;
              }
            } 
            ?>
						<script languaje = "javascript">
							f_Add_New_Row_IPA();
							document.forms['frgrm']['cComSeq_IPA'  +document.forms['frgrm']['nSecuencia_IPA'].value].value = f_Str_Pad(document.forms['frgrm']['nSecuencia_IPA'].value,3,"0","STR_PAD_LEFT");
  						document.forms['frgrm']['cComId_IPA'   +document.forms['frgrm']['nSecuencia_IPA'].value].value = "<?php echo $mConDet[$i]['ctoidxxx']     ?>";
  						document.forms['frgrm']['cComObs_IPA'  +document.forms['frgrm']['nSecuencia_IPA'].value].value = "<?php echo $mConDet[$i]['comobsxx']     ?>";
  						document.forms['frgrm']['cComTra_IPA'  +document.forms['frgrm']['nSecuencia_IPA'].value].value = "<?php echo $mConDet[$i]['comtraxx']     ?>";
              document.forms['frgrm']['nComCan_IPA'  +document.forms['frgrm']['nSecuencia_IPA'].value].value = "<?php echo ($nComCan+0)  ?>";
              document.forms['frgrm']['nComVlrU_IPA' +document.forms['frgrm']['nSecuencia_IPA'].value].value = "<?php echo ($nComVlrU+0) ?>";
  						document.forms['frgrm']['nComVlr_IPA'  +document.forms['frgrm']['nSecuencia_IPA'].value].value = "<?php echo ($mConDet[$i]['comvlrxx']+0) ?>";
  						document.forms['frgrm']['nComVlrNF_IPA'+document.forms['frgrm']['nSecuencia_IPA'].value].value = "<?php echo ($mConDet[$i]['comvlrnf']+0) ?>";
  						document.forms['frgrm']['cComMov_IPA'  +document.forms['frgrm']['nSecuencia_IPA'].value].value = "<?php echo  $mConDet[$i]['commovxx']    ?>";
  						document.forms['frgrm']['cPucDet_IPA'  +document.forms['frgrm']['nSecuencia_IPA'].value].value = "<?php echo  $mConDet[$i]['pucdetxx']    ?>";
						</script>
						<?php
						$nComVlr = ($mConDet[$i]['puctipej'] == "L" || $mConDet[$i]['puctipej'] == "") ? $mConDet[$i]['comvlrxx'] : $mConDet[$i]['comvlrnf'];

						$nSubTotal_IP += ($mConDet[$i]['pucdetxx'] == "P") ? (($mConDet[$i]['commovxx'] == "D") ? $nComVlr * -1 : $nComVlr) : $nComVlr;
						switch ($mConDet[$i]['commovxx']) {
						  case "D": $nDebitos_IP  += $nComVlr; break;
						  case "C": $nCreditos_IP += $nComVlr; break;
            }
					break;
				}
			} ?>

			<script languaje = "javascript">
				document.forms['frgrm']['nIPASub'].value   = "<?php echo round($nSubTotal_IP+$nDebitos_PCC+$nCreditos_PCC,2)+0; ?>";
				document.forms['frgrm']['nIPAIva'].value   = "<?php echo round($nIvaIP,2)+0; ?>";
      	document.forms['frgrm']['nIPAAnt'].value   = "<?php echo round($vConCab['comvlr01'],2)+0; ?>";
      	document.forms['frgrm']['nIPATot'].value   = (eval(document.forms['frgrm']['nIPASub'].value) + eval(document.forms['frgrm']['nIPAIva'].value));
      	document.forms['frgrm']['nIPARFte'].value  = "<?php echo round($nRetFte,2)+0; ?>";
      	document.forms['frgrm']['nIPAARFte'].value = "<?php echo round($nARetFte,2)+0; ?>";
      	document.forms['frgrm']['nIPARCre'].value  = "<?php echo round($nRetCre,2)+0; ?>";
      	document.forms['frgrm']['nIPAARCre'].value = "<?php echo round($nARetCre,2)+0; ?>";
      	document.forms['frgrm']['nIPARIva'].value  = "<?php echo round($nRetIva,2)+0; ?>";
      	document.forms['frgrm']['nIPARIca'].value  = "<?php echo round($nRetIca,2)+0; ?>";
      	document.forms['frgrm']['nIPAARIca'].value = "<?php echo round($nARetIca,2)+0; ?>";
    		document.forms['frgrm']['nIPASal'].value   = "<?php echo round(($vConCab['comvlr01']*-1) + ($nDebitos_PCC + $nCreditos_PCC) + $nSubTotal_IP + $nIvaIP - ($nRetFte + $nRetCre + $nRetIva + $nRetIca) + ($nARetFte + $nARetCre + $nARetIca),2)+0; ?>";

    		if(document.forms['frgrm']['nIPASal'].value < 0) {
    		  document.forms['frgrm']['cComSal'].value = "Saldo Cliente";
    		}

    		if(document.forms['frgrm']['nIPASal'].value > 0) {
    		  document.forms['frgrm']['cComSal'].value="Saldo Agencia";
    		}

    		if(document.forms['frgrm']['nIPASal'].value== 0){
    		  document.forms['frgrm']['cComSal'].value = "Saldo Cero";
    		}
			</script>
		<?php }	?>
	</body>
</html>
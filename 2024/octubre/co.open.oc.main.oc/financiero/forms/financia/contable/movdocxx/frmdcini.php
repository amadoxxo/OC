<?php
  namespace openComex;
	 /**
	 * Imprime Movimiento por Documento.
	 * --- Descripcion: Permite Imprimir Movimiento por Documento.
	 * @author yulieth Campos <ycampos@opentecnologia.com.co>
	 * @version 002
	 */

	include("../../../../libs/php/utility.php");

	$dHoy = date('Y-m-d');

  $qSysProbg = "SELECT * ";
  $qSysProbg .= "FROM $cBeta.sysprobg ";
  $qSysProbg .= "WHERE ";
  $qSysProbg .= "DATE(regdcrex) =\"$dHoy\" AND ";
  $qSysProbg .= "regusrxx = \"$kUser\" AND ";
  $qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
  $qSysProbg .= "pbamodxx = \"FACTURACION\" AND ";
  $qSysProbg .= "pbatinxx = \"MOVIMIENTODOCUMENTOS\" ";
  $qSysProbg .= "ORDER BY regdcrex DESC";
  $xSysProbg = f_MySql("SELECT", "", $qSysProbg, $xConexion01, "");
  // f_Mensaje(__FILE__,__LINE__,  $qSysProbg." ~ ".mysql_num_rows($xSysProbg));

	$mArcProBg = array();
	
	while ($xRB = mysql_fetch_array($xSysProbg)) {
    $vArchivos = explode("~", trim($xRB['pbaexcxx'], "~"));
    for ($nA = 0; $nA < count($vArchivos); $nA++) {
      $nInd_mArcProBg = count($mArcProBg);
      $cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $vArchivos[$nA];
      if ($vArchivos[$nA] != "" && file_exists($cRuta)) {
        $mArcProBg[$nInd_mArcProBg]['pbaexcxx'] = $vArchivos[$nA];
      } else {
        $mArcProBg[$nInd_mArcProBg]['pbaexcxx'] = "";
      }
  
      $mArcProBg[$nInd_mArcProBg]['pbaidxxx'] = $xRB['pbaidxxx'];
      $mArcProBg[$nInd_mArcProBg]['regunomx'] = $xRB['regunomx'];
  
      if ($xRB['regestxx'] != "INACTIVO") {
        $nTieEst = round(((strtotime(date('Y-m-d H:i:s')) - strtotime($xRB['regdinix'])) / ($xRB['pbatxixx'] * $xRB['pbacrexx'])), 2) . "&#37";
      } else {
        $nTieEst = "";
      }
  
      $mArcProBg[$nInd_mArcProBg]['pbarespr'] = $xRB['pbarespr'];
      $mArcProBg[$nInd_mArcProBg]['pbaerrxx'] = $xRB['pbaerrxx'];
      $mArcProBg[$nInd_mArcProBg]['regestxx'] = ($xRB['regdinix'] != "0000-00-00 00:00:00" && $xRB['regdfinx'] == "0000-00-00 00:00:00") ? "EN PROCESO" : $xRB['regestxx'];
  
      $mPost = f_Explode_Array($xRB['pbapostx'], "|", "~");
      for ($nP = 0; $nP < count($mPost); $nP++) {
        if ($mPost[$nP][0] != "") {
          $mArcProBg[$nInd_mArcProBg][$mPost[$nP][0]] = $mPost[$nP][1];
        }
      }
    }
  }
?>
<html>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script language="javascript">
  		function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				parent.fmwork.location='<?php echo $cPlesk_Forms_Directory ?>/frproces.php';
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			}

			function fnRecargar() {
				parent.fmwork.location="<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>";
			}
	
	    function f_Links(xLink,xSwitch,xSecuencia,xGrid,xType) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
					case "cComCod":
					case "cComDes":
						if(document.forms['frgrm']['cComId'].value != ""){
						  if (xLink == "cComCod" || xLink == "cComDes") {
						    //var cTerTip = document.forms['frgrm']['cTerTip'].value.toUpperCase();
						    var cComId  = document.forms['frgrm']['cComId'].value.toUpperCase();
						    var cComCod = document.forms['frgrm']['cComCod'].value.toUpperCase();
						    var cComDes = document.forms['frgrm']['cComDes'].value.toUpperCase();
						  }
							if (xSwitch == "VALID") {
								var cPathUrl = "frpar117.php?gModo="+xSwitch+"&gFunction="+xLink+
																					"&gComId="+cComId+
																					"&gComCod="+cComCod+
																					"&gComDes="+cComDes;
								//alert(cPathUrl);
								parent.fmpro.location = cPathUrl;
							} else {
								var nNx      = (nX-600)/2;
								var nNy      = (nY-250)/2;
								var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
								var cPathUrl = "frpar117.php?gModo="+xSwitch+"&gFunction="+xLink+
																					 "&gComId="+cComId+
																					 "&gComDes="+cComDes;
								cWindow = window.open(cPathUrl,xLink,cWinOpt);
					  		cWindow.focus();
							}
						}else{
							alert("Debe Seleccionar un Comprobante.");
						}
					break;
					case "cDocNro":
						if (xSwitch == "VALID") {
							var zRuta  = "frmdc121.php?gWhat=VALID&gFunction=cDocNro&gDocNro="+document.forms['frgrm']['cDocNro'].value.toUpperCase()+
																  "&gDocSuf="+document.forms['frgrm']['cDocSuf'].value;
							parent.fmpro.location = zRuta;
						} else {
							
							var nNx = (nX-400)/2;
							var nNy = (nY-250)/2;
							var zWinPro = 'width=400,scrollbars=1,height=250,left='+nNx+',top='+nNy;
							var zRuta  = "frmdc121.php?gWhat=WINDOW&gFunction=cDocNro&gDocNro="+document.forms['frgrm']['cDocNro'].value.toUpperCase();
							console.log('Verr', zRuta,"zWindow",zWinPro);
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  		zWindow.focus();
						}
					break;
					case "cCtoCod":
						var cCtoCod  = document.forms['frgrm']['cCtoCod'].value.toUpperCase();
						if (xSwitch == "VALID") {
							var cPathUrl = "frparcto.php?gModo="+xSwitch+"&gFunction="+xLink+
																					"&gCtoCto="+cCtoCod;
							//alert(cPathUrl);
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-600)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "frparcto.php?gModo="+xSwitch+"&gFunction="+xLink+
																					"&gCtoCto="+cCtoCod;
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
							cWindow.focus();
						}
				  break;
				}
	    }

			// FUNCION DE SELECT PARA CONSULTA //
			function fnGenSql()  {
				var nSwicht = 0;
				if((document.forms['frgrm']['cComId'].value == "" || document.forms['frgrm']['cComCod'].value == "" || document.forms['frgrm']['cComCsc'].value == "") && document.forms['frgrm']['rDoc'][2].checked != true ) {
				  if(document.forms['frgrm']['dDesde'].value == "" && document.forms['frgrm']['dHasta'].value == "") {
				  	alert("Debe Seleccionar un Rango de Fechas.");
				  	nSwicht = 1;
				  }
				}
				
				if ( document.forms['frgrm']['rDoc'][2].checked == true ) {
					if( document.forms['frgrm']['cDocNro'].value == "" || document.forms['frgrm']['cSucId'].value == "" || document.forms['frgrm']['cDocSuf'].value == "" ) {
						alert("Debe Seleccionar un Do.");
						nSwicht = 1;
					}
				}
				if(nSwicht == 0){
					var cTipo = 0;
				  var cTipDoc = "";
				  for (i=0;i<3;i++){
				    if (document.forms['frgrm']['rTipo'][i].checked == true){
				  	  cTipo = i+1;
				      break;
				    }
				  }
				  if(document.forms['frgrm']['rDocFte'].checked == true){
				   cTipDoc = "FUENTE";
				  } else if(document.forms['frgrm']['rDocCru'].checked == true){
				     cTipDoc = "CRUCE";
				  }else if(document.forms['frgrm']['rDocDo'].checked == true){
				     cTipDoc = "DO";
				  }

          if (cTipo != 2) {
            document.forms['frgrm']['cEjProBg'].checked = false;
            document.forms['frgrm']['cEjProBg'].value = "NO";
          }
	
				  if(cTipo == 2){
			  		var zRuta = 'frmdcprn.php?cTipo='+cTipo+
																		 '&cTipoDoc='	+cTipDoc+
																		 '&cComId='		+document.forms['frgrm']['cComId'].value+
																		 '&cComCod='	+document.forms['frgrm']['cComCod'].value+
																		 '&cComCsc='	+document.forms['frgrm']['cComCsc'].value+
																		 '&cCtoCod='	+document.forms['frgrm']['cCtoCod'].value+
																		 '&dDesde='		+document.forms['frgrm']['dDesde'].value+
																		 '&dHasta='		+document.forms['frgrm']['dHasta'].value+
																		 '&cEjProBg='	+document.forms['frgrm']['cEjProBg'].value;

						parent.fmpro.location = zRuta;
			  	}else{
		 				var zX      = screen.width;
	  				var zY      = screen.height;
	  				var zNx     = (zX-30)/2;
	  				var zNy     = (zY-100)/2;
	  				var zNy2    = (zY-100);
	  				var zWinPro = "width="+zX+",scrollbars=1,height="+zNy2+",left="+1+",top="+50;
						//var zRuta = 'rpsdiprn.php?cTipo='+document.forms['frgrm']['rTipo'][i].value+'&cMes='+document.forms['frgrm']['cMes'].value+'&cAnio='+document.forms['frgrm']['cAnio'].value+'&cEstado='+document.forms['frgrm']['cEstado'].value;
						if ( document.forms['frgrm']['rDoc'][2].checked != true ) {
							var zRuta = 'frmdcprn.php?cTipo='+document.forms['frgrm']['rTipo'][i].value+'&cTipoDoc='+cTipDoc+'&cComId='+document.forms['frgrm']['cComId'].value+'&cComCod='+document.forms['frgrm']['cComCod'].value+'&cComCsc='+document.forms['frgrm']['cComCsc'].value+'&cCtoCod='+document.forms['frgrm']['cCtoCod'].value+'&dDesde='+document.forms['frgrm']['dDesde'].value+'&dHasta='+document.forms['frgrm']['dHasta'].value;
						} else {
							var zRuta = 'frmdcprn.php?cTipo='+document.forms['frgrm']['rTipo'][i].value+'&cTipoDoc='+cTipDoc+'&cDocNro='+document.forms['frgrm']['cDocNro'].value+'&cSucId='+document.forms['frgrm']['cSucId'].value+'&cDocSuf='+document.forms['frgrm']['cDocSuf'].value;
						}
						var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
	  				zWindow = window.open(zRuta,cNomVen,zWinPro);
	  				zWindow.focus();
				  }
				}
		  }
		  
		  function chDate(fld){
				var val = fld.value;
				if (val.length > 0){
					var ok = 1;
					if (val.length < 10){
						 alert('Formato de Fecha debe ser aaaa-mm-dd');
						 fld.value = '';
						 fld.focus();
						 ok = 0;
					}
	        if(val.substr(4,1) == '-' && val.substr(7,1) == '-' && ok == 1){
						var anio = val.substr(0,4);
					  var mes  = val.substr(5,2);
					  var dia  = val.substr(8,2);
					  if (mes.substr(0,1) == '0'){
							mes = mes.substr(1,1);
						}
						if (dia.substr(0,1) == '0'){
						  dia = dia.substr(1,1);
    				}

    				if(mes > 12){
    					alert('El mes debe ser menor a 13');
    					fld.value = '';
    					fld.focus();
    				}
    				if (dia > 31){
    					alert('El dia debe ser menor a 32');
    					fld.value = '';
    					fld.focus();
    			  }
    				var aniobi = 28;
    				if(anio % 4 ==  0){
    					aniobi = 29;
    				}
    				if (mes == 4 || mes == 6 || mes == 9 || mes == 11){
    					 if (dia < 1 || dia > 30){
    							alert('El dia debe ser menor a 31, dia queda en 30');
    							fld.value = val.substr(0,8)+'30';
    						}
    				}
    				if (mes == 1 || mes == 3 || mes == 5 || mes == 7 || mes == 8 || mes == 10 || mes == 12){
    					 if (dia < 1 || dia > 32){
    							alert('El dia debe ser menor a 32');
    							fld.value = '';
    							fld.focus();
    				   }
    			  }
    				if(mes == 2 && aniobi == 28 && dia > 28 ){
    					alert('El dia debe ser menor a 29');
    					fld.value = '';
    					fld.focus();
    				}
    				if(mes == 2 && aniobi == 29 && dia > 29){
    					alert('El dia debe ser menor a 30');
    					fld.value = '';
    					fld.focus();
    				}
    		   }else{
    		         if(val.length > 0){
    		            alert('Fecha erronea, verifique');
    		          }
    		           fld.value = '';
    		           fld.focus();
    					  }
  				}
  	  }

			/* Esta funcion es llamada al chequear el tipo de reporte que queremos generar. */
			function habilitarDeshabilitaDo( form ) {
				/* Si el reporte es Do habilito la busqueda de DO, sino dejo el formulario de comprobantes. */
				if ( form.rDoc[2].checked == true ) {
					form.rTipo[0].checked = true;
					document.getElementsByName("comprobante")[0].style.display = 'none';
					document.getElementsByName("buscarDo")[0].style.display = 'block';
					//No mostramos el checked del Proceso en Bacground
					document.getElementById('EjProBg').style.display = 'none';
				} else {
					form.rDoc[2].checked = false;
					document.getElementsByName("comprobante")[0].style.display = 'block';
					document.getElementsByName("buscarDo")[0].style.display = 'none';
				}
				
			}
	
			/* Esta funcion es llamada al chequear la forma de visualizacion de un reporte */
			function DeshabilitaDo( form ) {
				form.rDoc[2].checked = false;
				document.getElementsByName("comprobante")[0].style.display = 'block';
				document.getElementsByName("buscarDo")[0].style.display = 'none';
				/* Por defecto activo el formato Doc. Fuente. */
				if (  form.rDoc[0].checked != true &&  form.rDoc[1].checked != true  ) {
					form.rDoc[0].checked = true;
				}
			}

			/* Metodo para habilitar y deshabilitar check de proceso en background */
			function fnHabilitarProBg(cTipo){
        if(cTipo == 2){
          document.getElementById('EjProBg').style.display = '';
        } else{
          document.forms['frgrm']['cEjProBg'].checked = false;
          document.forms['frgrm']['cEjProBg'].value = "NO";
          document.getElementById('EjProBg').style.display = 'none';
        }
      }

			function fnDescargar(xArchivo){
        parent.fmwork.location = "frgendoc.php?cRuta="+xArchivo;
      }
		</script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="500">
				<tr>
					<td>
            <form name='frgrm' action='frmdcprn.php' method="POST">
              <center>
          	    <fieldset>
          		    <legend>Consulta Movimiento Por Documento </legend>
          		    <table border="2" cellspacing="0" cellpadding="0" width="500">
          		     	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
          			     	<td class="name" width="30%"><center><h5><br>REPORTE DE MOVIMIENTO POR DOCUMENTO</h5></center></td>
          			    </tr>
          			  </table>
          			  <table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
  							 		<?php $nCol = f_Format_Cols(25);
  							 		echo $nCol;?>
          			    <tr>
          	          <td class="name" colspan = "5"><br>Desplegar en:
          			      </td>
          			      <td class="name" colspan = "7"><br>
          	            <input type="radio" name="rTipo" value="1" checked onclick="fnHabilitarProBg(this.value)">Pantalla
          	          </td>
          	          <td class="name" colspan = "7"><br>
          			         <input type="radio" name="rTipo" value="2" onClick="DeshabilitaDo(this.form); fnHabilitarProBg(this.value)">Excel
          			      </td>
          			      <td class="name" colspan = "6"><br>
          			        <input type="radio" name="rTipo" value="3" onClick="DeshabilitaDo(this.form); fnHabilitarProBg(this.value)">Pdf<br>
          			      </td>
          	        </tr>
										<tr>
											<td class="name" colspan = "12"><br>
													<input type="radio" name="rDoc" value="FUENTE" onClick="habilitarDeshabilitaDo(this.form)" id="rDocFte" checked>Doc.Fuente
												</td>
												<td class="name" colspan = "7"><br>
													<input type="radio" name="rDoc" value="CRUCE" onClick="habilitarDeshabilitaDo(this.form)" id="rDocCru">Doc.Cruce
												</td>
												<td class="name" colspan = "6"><br>
													<input type="radio" name="rDoc" value="Do" onClick="habilitarDeshabilitaDo(this.form)" id="rDocDo">Do
												</td>
										</tr>
          	      </table>
          	       <table border = '0' cellpadding = '0' cellspacing = '0' name="comprobante" style="display: block" width='500'>
  							 		<?php $nCol = f_Format_Cols(25);
  							 		echo $nCol;?>
										<tr>
											<td Class = "name" colspan = "25"><br>Comprobante
												<select class="letrase" size="1" name="cComId" style = "width:500" 
														onchange="javascript: document.forms['frgrm']['cComCod'].value = '';
																									document.forms['frgrm']['cComDes'].value  = '';" >
													<option value = "" selected>-- TODOS --</option>
													<option value = "A">A - AJUSTES X INF.</option>
													<option value = "B">B - ORDEN DE PRODUCCION</option>
													<option value = "C">C - NOTA CREDITO</option>
													<option value = "D">D - NOTA DEBITO</option>
													<option value = "E">E - NOTA ENTREGA</option>
													<option value = "F">F - FACTURAS</option>
													<option value = "G">G - EGRESOS</option>
													<option value = "H">H - NOTA SALIDA</option>
													<option value = "J">J - NOTA DEVOLUCION</option>
													<option value = "K">K - MINUTAS</option>
													<option value = "L">L - OTROS</option>
													<option value = "M">M - CAJA MENOR</option>
													<option value = "N">N - NOTA INTERNA</option>
													<option value = "O">O - NOTA PRODUCCION</option>
													<option value = "P">P - COMPRAS</option>
													<option value = "R">R - RECIBOS</option>
													<option value = "S">S - NOTA REMISION</option>
													<option value = "T">T - NOTA TRASLADO</option>
													<option value = "V">V - COTIZACION</option>
													<option value = "X">X - INVENTARIO DE FORMULARIOS</option>
													<option value = "Y">Y - ORDEN COMPRA</option>
													<option value = "Z">Z - ORDEN PEDIDO</option>
												</select>
											</td>
										</tr>
										<tr>
											<td Class = "name" colspan = "5">
												<a href = "javascript:document.forms['frgrm']['cComCod'].value = '';
																							document.forms['frgrm']['cComDes'].value  = '';
																							f_Links('cComCod','VALID')" id="id_href_cComCod">C&oacute;digo</a><br>
												<input type = "text" Class = "letra" style = "width:100;text-align:center" name = "cComCod"
													onfocus="javascript:document.forms['frgrm']['cComCod'].value = '';
																							document.forms['frgrm']['cComDes'].value  = '';
																							this.style.background='#00FFFF'"
													onBlur = "javascript:this.value=this.value.toUpperCase();
																							f_Links('cComCod','VALID');
																							this.style.background='#FFFFFF'">
											</td>
											<td Class = "name" colspan = "13">Descripci&oacute;n<br>
												<input type = "text" Class = "letra" style = "width:260" name = "cComDes" readonly>
											</td>
											<td Class = "name" colspan = "7">Documento<br>
												<input type = "text" Class = "letra" style = "width:140;text-align:center" name = "cComCsc"
													onfocus="javascript:document.forms['frgrm']['cComCsc'].value  = '';">
											</td>
										</tr>
										<tr>
											<td Class = "name" colspan = "5">
												<a href = "javascript:document.frgrm.cCtoCod.value  = '';
																							document.frgrm.cCtoDes.value  = '';
																							f_Links('cCtoCod','VALID')" id="IdCtoFs">Concepto</a><br>
												<input type = "text" Class = "letra" style = "width:100" name = "cCtoCod"
																onBlur = "javascript:this.value=this.value.toUpperCase();
																										if(document.frgrm.cCtoCod.value  != '') {
																											f_Links('cCtoCod','VALID');
																										}
																										this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																onFocus="javascript:document.frgrm.cCtoCod.value  = '';
																										document.frgrm.cCtoDes.value  = '';
																										this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
											</td>
											<td Class = "name" colspan = "20">Descripci&oacute;n<br>
												<input type = "text" Class = "letra" style = "width:400" name = "cCtoDes" readonly>
											</td>
										</tr>
										<tr>
											<td class="name" colspan = "5" width="100"><br>Rango De Fechas<br>(Fecha Doc.):</td>
											<td class="name" colspan = "3" width="60"><br><center><a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">De</a></center></td>
											<td class="name" colspan = "7"><br>
												<input type="text" name="dDesde" style = "width:140;text-align:center"
													onblur="javascript:chDate(this);">
											</td>
											<td class="name" colspan = "3" width="60"><br><center><a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">A</a></center></td>
											<td class="name" colspan = "7"><br>
												<input type="text" name="dHasta" style = "width:140;text-align:center"
													onblur="javascript:chDate(this);">
											</td>
										</tr>
          		    </table>
          		    
									<table border="0" cellspacing="0" cellpadding="0" name="buscarDo" style="display: none" width="100%">
										<tr>
											<td class="name"><br>Buscar Do :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="hidden" class="letra" name="cComIdDo"  style="width:30" id="cComIdDo"  readonly>
												<input type="hidden" class="letra" name="cComCodDo" style="width:30" id="cComCodDo" readonly>
												<input type="text"   class="letra" name="cSucId"    style="width:30" id="cSucId"    readonly>
												<input type="text"   class="letra" name="cDocTip"   style="width:80" readonly>
												<input type="text"   class="letra" name="cDocNro"   style="width:80"
														onblur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';
																			f_Links('cDocNro','VALID');"
														onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';
																		document.forms['frgrm']['cComId'].value='';
																		document.forms['frgrm']['cComCod'].value='';
																		document.forms['frgrm']['cSucId'].value='';
																		document.forms['frgrm']['cDocTip'].value='';
																		document.forms['frgrm']['cDocNro'].value='';
																		document.forms['frgrm']['cDocSuf'].value='';
																		document.forms['frgrm']['cPucId'].value='';
																		document.forms['frgrm']['cCcoId'].value='';
																		document.forms['frgrm']['cCliId'].value='';
																		document.forms['frgrm']['dRegFCre'].value='';" id="cDocNroDo">
												<input type="text" class="letra"   name="cDocSuf" style="width:30" id="cDocSuf" readonly>
												<input type="hidden" class="letra" name="cPucId"  style="width:30" readonly>
												<input type="hidden" class="letra" name="cCcoId"  style="width:30" readonly>
												<input type="hidden" class="letra" name="cCliId"  style="width:30" readonly>
												<input type="hidden" class="letra" name="dRegFCre" style="width:30" readonly>
											</td>
										</tr>
									</table>
									
									<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
										<tr id="EjProBg" style="display: none">
											<td Class = "name" colspan = "25"><br>
												<label><input type="checkbox" name="cEjProBg" value ="SI" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}" checked>Ejecutar Proceso en Background</label>
											</td>
										</tr>
									</table>
          		  </fieldset>
                <center>
          				<table border="0" cellpadding="0" cellspacing="0" width="500">
            				<tr height="21">
            					<td width="318" height="21"></td>
            					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:fnGenSql()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
            					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
          				  </tr>
          				</table>
          			</center>
          	  </form>
					</td>
				</tr>
		 	</table>
		</center>
		<?php if(count($mArcProBg) > 0){ ?>
			<center>
				<table border="0" cellpadding="0" cellspacing="0" width="500">
					<tr>
						<td Class = "name" colspan = "19"><br>
							<fieldset>
								<legend>Reportes Generados. Fecha [<?php echo date('Y-m-d'); ?>]</legend>
								<label>
									<table border="0" cellspacing="1" cellpadding="0" width="500">
										<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="height:20px">
											<td align="center"><strong>Usuario</strong></td>
											<td align="center"><strong>Comprobante</strong></td>
											<!-- <td align="center"><strong>C&oacute;digo</strong></td> -->
											<td align="center"><strong>Rango Fechas</strong></td>
											<td align="center"><strong>Resultado</strong></td>
											<td align="center"><strong>Estado</strong></td>
											<td align="center"><img src = "<?php echo $cPlesk_Skin_Directory ?>/b_cambest.gif" onClick = "javascript:fnRecargar()" style = "cursor:pointer" title="Recargar"></td>
										</tr>
										<?php for ($i = 0; $i < count($mArcProBg); $i++) {
											$cColor = "{$vSysStr['system_row_impar_color_ini']}";
											if($i % 2 == 0) {
												$cColor = "{$vSysStr['system_row_par_color_ini']}";
											}
											?>
										<tr bgcolor = "<?php echo $cColor ?>">
											<td style="padding:2px"><?php echo $mArcProBg[$i]['regunomx']; ?></td>

											<td style="padding:2px"><?php 
												if ($mArcProBg[$i]['cComId'] != "") { 
													echo $mArcProBg[$i]['cComId']; 
												} 

												if ($mArcProBg[$i]['cComCod'] != "") { 
													echo "-".$mArcProBg[$i]['cComCod']; 
												}
												?>
											</td>

											<!-- <td style="padding:2px"><?php 
												if ($mArcProBg[$i]['cComCod'] != "") { 
													echo $mArcProBg[$i]['cComCod']; 
												}?>
											</td> -->

											<td style="padding:2px"><?php 
												if ($mArcProBg[$i]['cComCod'] != "") { 
													echo "De " .$mArcProBg[$i]['dDesde'] . " A " . $mArcProBg[$i]['dHasta']; 
												}?>
											</td>

											<td style="padding:2px"><?php echo $mArcProBg[$i]['pbarespr']; ?></td>
											<td style="padding:2px"><?php echo $mArcProBg[$i]['regestxx']; ?></td>
											<td>
                        <?php if ($mArcProBg[$i]['pbaexcxx'] != "") { ?>
                          <a href = "javascript:fnDescargar('<?php echo $mArcProBg[$i]['pbaexcxx']; ?>')">
                            Descargar
                          </a>
                        <?php } ?>
                        <?php if ($mArcProBg[$i]['pbaerrxx'] != "") { ?>
                          <a href = "javascript:alert('<?php echo str_replace(array("<br>","'",'"'),array("\n"," "," "),$mArcProBg[$i]['pbaerrxx']) ?>')">
                            Ver
                          </a>
                        <?php } ?>
                      </td>
										</tr>
										<?php } ?>
									</table>
								</label>
							</fieldset>
						</td>
					</tr>
				</table>
			</center>
  	<?php } ?> 
	</body>
</html>
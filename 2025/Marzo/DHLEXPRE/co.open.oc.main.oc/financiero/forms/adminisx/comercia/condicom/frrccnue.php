<?php
/**
 * --- Descripcion: Reporte Condiciones Comerciales
 * @author Camilo Dulce <camilo.dulce@opentecnologia.com.co>
 * @version 001
 * @package opencomex
 */
	include("../../../../libs/php/utility.php");

	/**
	 *  Cookie fija
	 */
	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb   = $kDf[3];
	$kUser      = $kDf[4];
	$kLicencia  = $kDf[5];
	$swidth     = $kDf[6];
	$kModId  = $_COOKIE["kModId"];
	$kProId  = $_COOKIE["kProId"];

	?>
	<html>
		<title><?php echo $_COOKIE["kProDes"] ?></title>
		<head>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css'>
			<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css'>
			<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/utility.js'></script>
			<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/date_picker.js'></script>
	  	<script language = 'javascript'>
	  		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
					document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
  				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
				}

	  		function f_Links(xLink,xSwitch,xIteration) {
					var nX    = screen.width;
					var nY    = screen.height;
					switch (xLink){
					  case "cCliId":
						case "cCliIdFac":
						case "cCliIdCom":
						  if (xSwitch == "VALID") {
								var cRuta  = "frrcc150.php?gWhat=VALID&gFunction="+xLink+"&gCliId="+document.forms['frnav'][xLink].value.toUpperCase();
								parent.fmpro.location = cRuta;
							} else {
			  				var nNx     = (nX-550)/2;
								var nNy     = (nY-300)/2;
								var cWinPro = 'width=550,scrollbars=1,height=300,left='+nNx+',top='+nNy;
								var cRuta   = "frrcc150.php?gWhat=WINDOW&gFunction="+xLink+"&gCliId="+document.forms['frnav'][xLink].value.toUpperCase();
								cWindow = window.open(cRuta,"cWindow",cWinPro);
						  	cWindow.focus();
							}
					  break;
						case "cGtaId":
	  					if (xSwitch == "VALID") {
	  						var cRuta  = "frrcc111.php?gWhat=VALID&gFunction=cGtaId&gGtaId="+document.forms['frnav']['cGtaId'].value.toUpperCase();
	  						parent.fmpro.location = cRuta;
	  					} else {
	  		  			var nNx     = (nX-600)/2;
	  						var nNy     = (nY-250)/2;
	  						var cWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
	  						var cRuta   = "frrcc111.php?gWhat=WINDOW&gFunction=cGtaId&gGtaId="+document.forms['frnav']['cGtaId'].value.toUpperCase();
	  						cWindow = window.open(cRuta,"cWindow",cWinPro);
	  				  	cWindow.focus();
	  					}
	  			  break;
					}
		  	}

	  		function chDate(fld){
			  	var val = fld.value;
			    if (val.length > 0){
			    	var ok = 1;
			      if (val.length < 10){
			      	alert('Formato de Fecha debe ser aaaa-mm-dd');
			        fld.value = '0000-00-00';
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
			          fld.value = '0000-00-00';
			          fld.focus();
			        }
			        if (dia > 31){
			        	alert('El dia debe ser menor a 32');
			          fld.value = '0000-00-00';
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
			            fld.value = '0000-00-00';
			            fld.focus();
			          }
			        }
			        if(mes == 2 && aniobi == 28 && dia > 28 ){
			        	alert('El dia debe ser menor a 29');
			          fld.value = '0000-00-00';
			          fld.focus();
			        }
			        if(mes == 2 && aniobi == 29 && dia > 29){
			        	alert('El dia debe ser menor a 30');
			          fld.value = '0000-00-00';
			          fld.focus();
			        }
			      }else{
			      	if(val.length > 0){
			        	alert('Fecha erronea, Verifique');
			        }
			        fld.value = '0000-00-00';
			        fld.focus();
			      }
			    }else{
			    	alert("Debe Ingresar una Fecha");
			      fld.value = '0000-00-00';
			      fld.focus();
			    }
			  }

	    	function f_Generar(){
					var nSwitch = 0;
	      	var cMsj = "";
	        /**
	         * Validaciones
	         */
	        if(document.forms['frnav']['cCliId'].value == "" && document.forms['frnav']['cCliIdCom'].value == "" &&
						 document.forms['frnav']['cGtaId'].value == "" && document.forms['frnav']['cCliIdFac'].value == ""){
		        if(document.forms['frnav']['dFecVigDel'].value == "0000-00-00" && document.forms['frnav']['dFecVigAl'].value == "0000-00-00"){
		        	nSwitch = 1;
			        cMsj += 'Debe Aplicar un Rango de Fechas de Vigencia.\n';
		        }
	        }

	      	if(nSwitch == 0){
	    	  	var cTipo = 0;
				  	for (var i=0;i<2;i++){
				    	if (document.forms['frnav']['rTipo'][i].checked == true){
				  	  	cTipo = i+1;
				      	break;
				    	}
				  	}
				  	var cRuta = 'frrccprn.php';
				  	if(cTipo == 2){
					  	document.forms['frnav'].target='fmpro';
				    	document.forms['frnav'].action=cRuta;
				    	document.forms['frnav'].submit();
				  	}else{
		 					var zX      = screen.width;
	  					var zY      = screen.height;
	  					var zNx     = (zX-30)/2;
	  					var zNy     = (zY-100)/2;
	  					var zNy2    = (zY-100);
	  					var zWinPro = "width="+zX+",scrollbars=1,height="+zNy2+",left="+1+",top="+50;
							var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
				    	zWindow = window.open('',cNomVen,zWinPro);
				    	document.forms['frnav'].target=cNomVen;
				    	document.forms['frnav'].action=cRuta;
				    	document.forms['frnav'].submit();
				  	}
	  			} else {
	  				alert(cMsj + "Verifique.");
	  			}
	    	}
		  </script>
		</head>
		<body topmargin=0 leftmargin=0  marginwidth=0 marginheight=0 style = 'margin-right : 0'>
			<form name = 'frnav' action="frrccprn.php" method = "post" target="fmpro">
	    	<center>
	      	<table border = 0 cellpadding = 0 cellspacing = 0 width = '480'>
	          <tr>
	            <td>
	              <fieldset><legend>Reporte Condiciones Comerciales</legend>
	              	<table border="2" cellspacing="0" cellpadding="0" width="480">
	                  <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
	                    <td class="name"><center><h5><br>REPORTE CONDICIONES COMERCIALES</h5></center></td>
	                  </tr>
	                </table>
	                <table border = 0 cellpadding = 0 cellspacing = 0 width = '480'>
	                  <?php echo columnas(24,20); ?>
	                  <tr>
	                  	<td class="name" colspan = "06" style = "width:120"><br>Desplegar en<br></td>
	                    <td class="name" colspan = "09"><br><input type="radio" name="rTipo" value="1" checked>Pantalla<br></td>
	                    <td class="name" colspan = "09"><br><input type="radio" name="rTipo" value="2">Excel<br></td>
	                  </tr>
	                </table>
	                <table border = 0 cellpadding = 0 cellspacing = 0 width = '480'>
	                	<?php echo columnas(24,20); ?>
	          	      <tr>
											<td Class = 'name' colspan = '05'><br>
												<a href = "javascript:document.forms['frnav']['cCliId'].value  = '';
																							document.forms['frnav']['cCliDv'].value  = '';
																  						document.forms['frnav']['cCliNom'].value = '';
																							f_Links('cCliId','VALID')" id="idCli">Cliente
												</a><br>
											</td>
											<td Class = 'name' colspan = '06'><br>
												<input type = "text" Class = "letra" name = "cCliId" style = "width:120;"
													onfocus="javascript:document.forms['frnav']['cCliId'].value  = '';
            				  												document.forms['frnav']['cCliNom'].value = '';
																		  				document.forms['frnav']['cCliDv'].value  = '';
											                				this.style.background='#00FFFF'"
							    				onBlur = "javascript:this.value=this.value.toUpperCase();
																       				 f_Links('cCliId','VALID');
																       				 this.style.background='#FFFFFF'">
											</td>
											<td Class = 'name' colspan = '01'><br>
												<input type = "text" Class = "letra" name = "cCliDv" style = "width:20;text-align:center" readonly>
											</td>
											<td Class = 'name' colspan = '12'><br>
												<input type = "text" Class = "letra" style = "width:240;" name = "cCliNom" readonly>
											</td>
	          	      </tr>
										<tr>
											<td Class = 'name' colspan = '05'><br>
												<a href = "javascript:document.forms['frnav']['cCliIdCom'].value  = '';
																							document.forms['frnav']['cCliDvCom'].value  = '';
																  						document.forms['frnav']['cCliNomCom'].value = '';
																							f_Links('cCliIdCom','VALID')">Comercial
												</a><br>
											</td>
											<td Class = 'name' colspan = '06'><br>
												<input type = "text" Class = "letra" name = "cCliIdCom" style = "width:120;"
													onfocus="javascript:document.forms['frnav']['cCliIdCom'].value  = '';
            				  												document.forms['frnav']['cCliNomCom'].value = '';
																		  				document.forms['frnav']['cCliDvCom'].value  = '';
											                				this.style.background='#00FFFF'"
							    				onBlur = "javascript:this.value=this.value.toUpperCase();
																       				 f_Links('cCliIdCom','VALID');
																       				 this.style.background='#FFFFFF'">
											</td>
											<td Class = 'name' colspan = '01'><br>
												<input type = "text" Class = "letra" name = "cCliDvCom" style = "width:20;text-align:center" readonly>
											</td>
											<td Class = 'name' colspan = '12'><br>
												<input type = "text" Class = "letra" style = "width:240;" name = "cCliNomCom" readonly>
											</td>
	          	      </tr>
										<tr>
											<td Class = 'name' colspan = '05'><br>
												<a href = "javascript:document.forms['frnav']['cGtaId'].value  = '';
																						document.forms['frnav']['cGtaDes'].value = '';
																						f_Links('cGtaId','VALID')" id = "IdGta">Grupo de Tarifa
												</a><br>
											</td>
											<td Class = 'name' colspan = '06'><br>
												<input type = "text" Class = "letra" style = "width:120" name = "cGtaId"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                               f_Links('cGtaId','VALID');
                                               this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus="javascript:document.forms['frnav']['cGtaId'].value  ='';
                                              document.forms['frnav']['cGtaDes'].value = '';
                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
											</td>
											<td Class = 'name' colspan = '13'><br>
												<input type = "text" Class = "letra" style = "width:260" name = "cGtaDes" readonly>
											</td>
	          	      </tr>
										<tr>
											<td Class = 'name' colspan = '05'><br>
												<a href = "javascript:document.forms['frnav']['cCliIdFac'].value  = '';
																							document.forms['frnav']['cCliDvFac'].value  = '';
																  						document.forms['frnav']['cCliNomFac'].value = '';
																							f_Links('cCliIdFac','VALID')">Facturar A
												</a><br>
											</td>
											<td Class = 'name' colspan = '06'><br>
												<input type = "text" Class = "letra" name = "cCliIdFac" style = "width:120;"
													onfocus="javascript:document.forms['frnav']['cCliIdFac'].value  = '';
            				  												document.forms['frnav']['cCliNomFac'].value = '';
																		  				document.forms['frnav']['cCliDvFac'].value  = '';
											                				this.style.background='#00FFFF'"
							    				onBlur = "javascript:this.value=this.value.toUpperCase();
																       				 f_Links('cCliIdFac','VALID');
																       				 this.style.background='#FFFFFF'">
											</td>
											<td Class = 'name' colspan = '01'><br>
												<input type = "text" Class = "letra" name = "cCliDvFac" style = "width:20;text-align:center" readonly>
											</td>
											<td Class = 'name' colspan = '12'><br>
												<input type = "text" Class = "letra" style = "width:240;" name = "cCliNomFac" readonly>
											</td>
	          	      </tr>
	                  <tr>
	          	      	<td class="name" colspan = "06"><br>Fecha Vigencia:</td>
	          	        <td class="name" colspan = "02"><br><a href='javascript:show_calendar("frnav.dFecVigDel")' id="id_href_dFecVigDel">Del</a></td>
	          	        <td class="name" colspan = "06"><br>
	          	        	<input type="text" name="dFecVigDel" style = "width:120;text-align:center" onblur="javascript:chDate(this);" value="<?php echo "0000-00-00" ?>">
	          	        </td>
	          	        <td class="name" colspan = "04"><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:show_calendar("frnav.dFecVigAl")' id="id_href_dFecVigAl">Al</a></td>
	          	        <td class="name" colspan = "06" align="right"><br>
	          	        	<input type="text" name="dFecVigAl" style = "width:120;text-align:center" onblur="javascript:chDate(this);" value="<?php echo "0000-00-00" ?>">
	          	        </td>
	          	      </tr>
	                </table><br>
	              </fieldset>
	            </td>
	          </tr>
	        </table>
	      </center>
	    </form>
	    <center>
	    <table border="0" cellpadding="0" cellspacing="0" width="480">
		    <tr height="21">
			    <td width="298" height="21"></td>
			    <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_ok_bg.gif" style="cursor:hand"
			    		onClick = "javascript:f_Generar();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar
			    </td>
			    <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_cancel_bg.gif" style="cursor:hand"
			    		onClick = "javascript:f_Retorna();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
			    </td>
		    </tr>
	    </table>
	  </center>
	</body>
</html>

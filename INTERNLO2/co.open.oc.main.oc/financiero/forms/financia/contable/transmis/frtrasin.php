<?php
  /**
   * NUE Transmision para Sistema SIIGO NUBE - Interlogistica.
	 * 
   * @author Juan Jose Trujillo Chimbaco <juan.trujillo@open-eb.co>
   * @package opencomex
   */

  include("../../../../libs/php/utility.php");
?>
<html>
	<head>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
    <script language="javascript">
   	  function fnDate(fld){
				var val = fld.value;
				var ok = 1;
				if (val.length < 10) {
					alert('La Fecha Debe Ser AAAA-MM-DD.');
					fld.value = '';
					ok = 0;
				}

				if(val.substr(4,1) == '-' && val.substr(7,1) == '-' && ok == 1) {
					var anio = val.substr(0,4);
					var mes = val.substr(5,2);
					var dia = val.substr(8,2);
					if (mes.substr(0,1) == '0') {
						mes = mes.substr(1,1);
					}
					if (dia.substr(0,1) == '0') {
						dia = dia.substr(1,1);
					}
					if(mes > 12) {
						alert('El Mes Debe Ser Menor a 13.');
						fld.value = '';
					}
					if (dia > 31){
						alert('El Dia Debe Ser Menor a 32.');
						fld.value = '';
					}
					var aniobi = 28;
					if(anio % 4 ==  0){
						aniobi = 29;
					}
					if (mes == 4 || mes == 6 || mes == 9 || mes == 11){
						if (dia < 1 || dia > 30){
							alert('El Dia Debe Ser Menor a 31, Dia Queda en 30.');
							fld.value = val.substr(0,8)+'30';
						}
					}
					if (mes == 1 || mes == 3 || mes == 5 || mes == 7 || mes == 8 || mes == 10 || mes == 12){
						if (dia < 1 || dia > 32){
							alert('El Dia Debe Ser Menor a 32.');
							fld.value = '';
						}
					}
					if(mes == 2 && aniobi == 28 && dia > 28 ){
						alert('El Dia Debe Ser Menor a 29.');
						fld.value = '';
					}
					if(mes == 2 && aniobi == 29 && dia > 29){
						alert('El Dia Debe Ser Menor a 30');
						fld.value = '';
					}
				} else {
					if (val.length > 0){
						alert('Fecha Erronea, Verifique.');
					}
					fld.value = '';
				}
			}

     	function f_Links(xLink,xSwitch,xSecuencia,xGrid,xType) {
    		var nX    = screen.width;
    		var nY    = screen.height;
    		switch (xLink) {
    		  case "cComCod":
    			case "cComDes":
    				if(document.forms['frgrm']['cComId'].value != ""){
    				  if (xLink == "cComCod" || xLink == "cComDes") {
    				    var cComId  = document.forms['frgrm']['cComId'].value.toUpperCase();
    				    var cComCod  = document.forms['frgrm']['cComCod'].value.toUpperCase();
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
    		}
      }

      function fnEnvia(){
        var dDesde = document.forms['frgrm']['dDesde'].value;
				var dHasta = document.forms['frgrm']['dHasta'].value;
        var nSwicht = 0;
        var cMsj = "";

        if (dDesde.length != 10 || dHasta.length != 10) { // verifica que esten bien registradas las fechas
          nSwicht = 1;
          cMsj += "Verifique Fecha Desde y Fecha Hasta.\n";
        }

        if (nSwicht == 0) {
          var cIni = dDesde.replace('-','');  // pase de la forma AAAA-MM-DD --> AAAAMMDD
          var cFin = dHasta.replace('-',''); // pase de la forma AAAA-MM-DD --> AAAAMMDD
          var cIni2 = cIni.replace('-','');
          var cFin2 = cFin.replace('-','');
          nIni = 1 * cIni2;
          nFin = 1 * cFin2;
          if(cIni.substr(0,4) == cFin.substr(0,4)){ // pregunta si son iguales los años desde y hasta.
            if (nFin < nIni){ // compara la fecha final con la fecha inicial
              alert('Fecha Final es Menor a Inicial,verifique'); // Genera una alerta en caso de que la fecha final sea menor que la inicial
            } else {
              var zX      = screen.width;
  						var zY      = screen.height;
  						var zNx     = (zX-500)/2;
  						var zNy     = (zY-250)/2;
  						var zWinPro = 'width=500,scrollbars=1,height=250,left='+zNx+',top='+zNy;

							switch('<?php echo $kDf[3] ?>'){
	  						case 'MIRCANAX':
	  						case 'TEMIRCANAX':
	  						case 'DEMIRCANAX':
									var zRuta = 'frsignub.php?dDesde='+dDesde+'&dHasta='+dHasta+
																					'&gComId='+document.forms['frgrm']['cComId'].value+
																					'&gComCod='+document.forms['frgrm']['cComCod'].value+
																					'&gUsrId='+document.forms['frgrm']['cUsrId'].value;
								break;
								default:
									var zRuta = 'frsinube.php?dDesde='+dDesde+'&dHasta='+dHasta+
																					'&gComId='+document.forms['frgrm']['cComId'].value+
																					'&gComCod='+document.forms['frgrm']['cComCod'].value+
																					'&gUsrId='+document.forms['frgrm']['cUsrId'].value;
								break;
							}

              
  						zWindow = window.open(zRuta,'zWindow',zWinPro);
  						zWindow.focus();
            }
          }else{
              alert("No se Permiten A\u00f1os Diferentes.");
            }
        } else {
          alert(cMsj);
        }
      }

      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      }
		</script>
  </head>
  	<body>
  		<center>
				<table border ="0" cellpadding="0" cellspacing="0" width="520">
					<tr>
						<td>
			  			<fieldset>
						 	<legend>Archivo Excel para Transmision a Sistema SIIGO NUBE</legend>
				  			<form name = 'frgrm'>
				  				<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:520">
										<center>
		       	     			<table cellspacing="0" width="100%">
		         	         	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
		           	         	<td class="name" width="100%"><br><center><u>La Fecha de hoy es: <?php echo date('Y-m-d') ?></u><br><br></center>
		            	      </tr>
		                  </table>
		                </center>
		                <br>
		                <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:520">
		                  <?php $zCol = f_Format_Cols(26); echo $zCol; ?>
				  					  <tr>
				  						  <td Class = "name" colspan = "8"><a href='javascript:show_calendar("frgrm.dDesde")'>Fecha Desde</a><br>
												  <input type = 'text' name = 'dDesde' value = '<?php echo date('Y-m-d') ?>' maxlength=10 Class = 'letra' style = 'width:160;text-align:center' onBlur = 'javascript:fnDate(this)'>
											  </td>
				  						  <td Class = "name" colspan = "8"><a href='javascript:show_calendar("frgrm.dHasta")'>Fecha Hasta</a><br>
												  <input type = 'text' name = 'dHasta' value = '<?php echo date('Y-m-d') ?>' maxlength=10 Class = 'letra' style = 'width:160;text-align:center' onBlur = 'javascript:fnDate(this)'>
											  </td>
											  <td Class = "name" colspan = "10">Id del Comprobante<br>
													<select class="letrase" size="1" name="cComId" style = "width:200"
														onchange="javascript: document.forms['frgrm']['cComCod'].value = '';
																								  document.forms['frgrm']['cComDes'].value  = '';">
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
				  					  	<td Class = "name" colspan = "4">
													<a href = "javascript:document.forms['frgrm']['cComCod'].value = '';
																								document.forms['frgrm']['cComDes'].value  = '';
																								f_Links('cComCod','VALID')" id="id_href_cComCod">C&oacute;digo</a><br>
													<input type = "text" Class = "letra" style = "width:80;text-align:center" name = "cComCod"
														onfocus="javascript:document.forms['frgrm']['cComCod'].value = '';
																				    		document.forms['frgrm']['cComDes'].value  = '';
													                  		this.style.background='#00FFFF'"
									    			onBlur ="javascript:this.value=this.value.toUpperCase();
																		         		f_Links('cComCod','VALID');
																		         		this.style.background='#FFFFFF'">
											</td>
											<td Class = "name" colspan = "12">Descripci&oacute;n<br>
												<input type = "text" Class = "letra" style = "width:240" name = "cComDes" readonly>
											</td>
											<td class="name" width="10" align="left">Usuarios<br>
      									<select Class = "letrase" name = "cUsrId" value = "<?php echo $_COOKIE['kUsrId'] ?>" style = "width:200" >
      										<option value = "" selected>-- TODOS --</option>
      										<?php
      											$qUsrDat  = "SELECT * ";
      											$qUsrDat .= "FROM $cAlfa.SIAI0003 ";
      											$qUsrDat .= "WHERE ";
      											$qUsrDat .= "$cAlfa.SIAI0003.USRDOCXX != \"\" AND ";
      											$qUsrDat .= "$cAlfa.SIAI0003.REGESTXX = \"ACTIVO\" ";
      											$qUsrDat .= "ORDER BY $cAlfa.SIAI0003.USRNOMXX ";
      											$xUsrDat = f_MySql("SELECT","",$qUsrDat,$xConexion01,"");
      											//f_Mensaje(__FILE__,__LINE__,$qUsrDat." ~ ".mysql_num_rows($xUsrDat));
      											$mMatrizUsr = array();
      											if (mysql_num_rows($xUsrDat) > 0) {
      												while ($xRUD = mysql_fetch_array($xUsrDat)) {
      													$nInd_mMatrizUsr = count($mMatrizUsr);
      													$mMatrizUsr[$nInd_mMatrizUsr]['usridxxx'] = $xRUD['USRIDXXX'];
      													$mMatrizUsr[$nInd_mMatrizUsr]['usrnomxx'] = $xRUD['USRNOMXX'];
      												}
      											}
      											for($i=0;$i<count($mMatrizUsr);$i++){
      												if ($mMatrizUsr[$i]['usridxxx'] == $_COOKIE['kUsrId']) { ?>
      													<option value = "<?php echo $mMatrizUsr[$i]['usridxxx']?>" selected><?php echo $mMatrizUsr[$i]['usrnomxx'] ?></option>
      													<?php } else { ?>
      														<option value = "<?php echo $mMatrizUsr[$i]['usridxxx']?>"><?php echo $mMatrizUsr[$i]['usrnomxx'] ?></option>
      													<?php
      												}//if ($mMatrizUsr[$i]['usridxxx'] == $cUsrId) {
      											}//for($i=0;$i<count($mMatrizUsr);$i++){
      										?>
      									</select>
             	       	 </td>
				  					  <tr>
				  					  </tr>
					 		      </table>
				        </form>
				      </fieldset>
				    </td>
				  </tr>
				</table>
      </center>
      <center>
        <center>
          <table border = "0" cellpadding = "0" cellspacing = "0" width="520"><!-- Se modifica el ancho de 600 px a 500 px para una mejor vista-->
            <tr height="21">
              <td width="338" height="21">&nbsp;</td>
              <td width="91" height="21" Class="name" >
                <input type="button" name="Btn_" id="Btn_Subir" value="Generar" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif) no-repeat;border:0px"
                  onclick = "javascript:fnEnvia()">
              </td>
              <td width="91" height="21" Class="name" >
                <input type="button" value="Salir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif) no-repeat;border:0px"
                  onClick = "javascript:fnRetorna()">
              </td>
            </tr>
          </table>
        </center>
    </center>
  </body>
</html>

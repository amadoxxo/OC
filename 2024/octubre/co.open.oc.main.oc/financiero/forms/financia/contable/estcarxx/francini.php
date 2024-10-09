<?php
  namespace openComex;
  /**
  * Reporte Estado de Cuenta.
  * Este programa permite la definicion de filtros para la Generacion del Reporte de Estdo de Cuenta
  * @author  openTecnologia - Desarrollo
  * @package openComex
  * @version 3.0.0
  **/

	include("../../../../libs/php/utility.php");

  $dHoy = date('Y-m-d');

  $qSysProbg = "SELECT * ";
  $qSysProbg .= "FROM $cBeta.sysprobg ";
  $qSysProbg .= "WHERE ";
  $qSysProbg .= "DATE(regdcrex) =\"$dHoy\" AND ";
  $qSysProbg .= "regusrxx = \"$kUser\" AND ";
  $qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
  $qSysProbg .= "pbamodxx = \"FACTURACION\" AND ";
  $qSysProbg .= "pbatinxx = \"ANALISISDECARTERA\" ";
  $qSysProbg .= "ORDER BY regdcrex DESC";
  $xSysProbg = f_MySql("SELECT", "", $qSysProbg, $xConexion01, "");

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
  		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				parent.fmwork.location='<?php echo $cPlesk_Forms_Directory ?>/frproces.php';
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
	    }

  	  function f_HideShow(xId)	{
  	    if (xId == 'SI')	{
  	    	document.getElementById('edades').style.display='block';
				}	else	{
  	    	document.getElementById('edades').style.display='none';
        }
      }

			function f_Links(xLink,xSwitch,xSecuencia,xType) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
          case "cPucIni":
          case "cPucFin":
          	if (xSwitch == "VALID") {
          		var cRuta  = "franc115.php?gWhat=VALID&gFunction="+xLink+"&cPucId="+document['frgrm'][xLink].value.toUpperCase()+"";
          		parent.fmpro.location = cRuta;
          	} else {
          		var nNx     = (nX-600)/2;
          		var nNy     = (nY-250)/2;
          		var cWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
          		var cRuta   = "franc115.php?gWhat=WINDOW&gFunction="+xLink+"&cPucId="+document['frgrm'][xLink].value.toUpperCase()+"";
          		zWindow = window.open(cRuta,"zWindow",cWinPro);
            	zWindow.focus();
          	}
          break;
          case "nNitIni":
          case "nNitFin":
          	if (xSwitch == "VALID") {
          		var cRuta  = "franc150.php?gWhat=VALID&gFunction="+xLink+"&gTerId="+document['frgrm'][xLink].value.toUpperCase()+"";
          		parent.fmpro.location = cRuta;
          	} else {
          		var nNx     = (nX-600)/2;
          		var nNy     = (nY-250)/2;
          		var cWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
          		var cRuta   = "franc150.php?gWhat=WINDOW&gFunction="+xLink+"&gTerId="+document['frgrm'][xLink].value.toUpperCase()+"";          		
          		zWindow = window.open(cRuta,"zWindow",cWinPro);
            	zWindow.focus();
          	}
          break;
          case "nCcoIni":
          case "nCcoFin":
          	if (xSwitch == "VALID") {
          		var cRuta  = "franc116.php?gWhat=VALID&gFunction="+xLink+"&gCcoId="+document['frgrm'][xLink].value.toUpperCase()+"";
          		parent.fmpro.location = cRuta;
          	} else {
          		var nNx     = (nX-600)/2;
          		var nNy     = (nY-250)/2;
          		var cWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
          		var cRuta   = "franc116.php?gWhat=WINDOW&gFunction="+xLink+"&gCcoId="+document['frgrm'][xLink].value.toUpperCase()+"";          		
          		zWindow = window.open(cRuta,"zWindow",cWinPro);
            	zWindow.focus();
          	}
          break;          
				}
			}

			function chDate(fld) {
				var val = fld.value;
				if (val.length > 0) {
					var ok = 1;
					
					if (val.length < 10) {
						alert('Formato de Fecha debe ser aaaa-mm-dd');
						fld.value = '';
						fld.focus();
						ok = 0;
					}
					
					if(val.substr(4,1) == '-' && val.substr(7,1) == '-' && ok == 1) {
						var anio = val.substr(0,4);
						var mes  = val.substr(5,2);
						var dia  = val.substr(8,2);
						
						if (mes.substr(0,1) == '0') {
							mes = mes.substr(1,1);
						}
						
						if (dia.substr(0,1) == '0') {
							dia = dia.substr(1,1);
						}
				
						if(mes > 12) {
							alert('El mes debe ser menor a 13');
							fld.value = '';
							fld.focus();
						}
						
						if (dia > 31) {
							alert('El dia debe ser menor a 32');
							fld.value = '';
							fld.focus();
						}
						
						var aniobi = 28;
						if(anio % 4 ==  0) {
							aniobi = 29;
						}
						
						if (mes == 4 || mes == 6 || mes == 9 || mes == 11) {
							if (dia < 1 || dia > 30){
								alert('El dia debe ser menor a 31, dia queda en 30');
								fld.value = val.substr(0,8)+'30';
							}
						}
						
						if (mes == 1 || mes == 3 || mes == 5 || mes == 7 || mes == 8 || mes == 10 || mes == 12) {
							if (dia < 1 || dia > 32){
								alert('El dia debe ser menor a 32');
								fld.value = '';
								fld.focus();
							}
						}
						
						if(mes == 2 && aniobi == 28 && dia > 28 ) {
							alert('El dia debe ser menor a 29');
							fld.value = '';
							fld.focus();
						}
						
						if(mes == 2 && aniobi == 29 && dia > 29) {
							alert('El dia debe ser menor a 30');
							fld.value = '';
							fld.focus();
						}
					} else {
						if(val.length > 0) {
							alert('Fecha erronea, verifique');
						}
						fld.value = '';
						fld.focus();
					}
				}
			}			  			

			function f_Aceptar() {

				var nSwicht = 0;
				var cMsj = "\n";

				if (document.forms['frgrm']['dHasta'].value == '') {
					nSwicht = 1;
					cMsj += "La Fecha de Corte no Puede Ser Vacia.\n";
				}
 
        if (nSwicht == 0) {
          var cTipo = 0;
          for (i=0;i<2;i++){
            if (document.forms['frgrm']['rTipo'][i].checked == true){
              cTipo = i+1;
              break;
            }
          }

          if (cTipo != 2) {
            document.forms['frgrm']['cEjProBg'].checked = false;
            document.forms['frgrm']['cEjProBg'].value = "NO";
          }
                  
          if(cTipo == 2){                 
        	  document.forms['frgrm'].target='fmpro';
            document.forms['frgrm'].submit();
          }else{
        	  var zX      = screen.width;
            var zY      = screen.height;
            var zNx     = (zX-30)/2;
            var zNy     = (zY-100)/2;
            var zNy2    = (zY-100);
            var zWinPro = "width="+zX+",scrollbars=1,height="+zNy2+",left="+1+",top="+50;
            var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
            zWindow = window.open('',cNomVen,zWinPro);
                    
            document.forms['frgrm'].target=cNomVen;
            document.forms['frgrm'].submit();
            zWindow.focus();
          }
        } else {
           alert(cMsj + "Verifique.")
        }				 
			}

      function fnHabilitarProBg(cTipo){
        if(cTipo == 2 ){
          document.getElementById('EjProBg').style.display = 'table-row';
        } else{
          document.forms['frgrm']['cEjProBg'].checked = false;
          document.forms['frgrm']['cEjProBg'].value = "NO";
          document.getElementById('EjProBg').style.display = 'none';
        }
      }

      function fnDescargar(xArchivo){
        parent.fmwork.location = "frgendoc.php?cRuta="+xArchivo;
      }

      function fnRecargar() {
				parent.fmwork.location="<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>";
			}

      function fnVerFiltros(xCarEda, xPbaId){
				w=420;
     	  h=300;
     	  LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
        TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
        settings ='height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars=YES,resizable'
        var cRuta = 'francfil.php?gCarEda=' + xCarEda + '&gPbaId=' + xPbaId;
  		 	zWin = window.open(cRuta,'Ver Filtros',settings);
  		 	zWin.focus();
			}

	  </script>
	</head>
    <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
      <center>
        <table border ="0" cellpadding="0" cellspacing="0" width="500">
          <tr>
            <td>
              <form name='frgrm' action='francprn.php' method="post" target="fmpro">
                <center>
                  <fieldset>
                    <legend>Consulta An&aacute;lisis de Cuentas</legend>
                    <table border="2" cellspacing="0" cellpadding="0" width="500">
                      <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
                        <td class="name" width="30%"><center><h5><br>ANALISIS DE CUENTAS</h5></center></td>
                      </tr>
                    </table>

                    <fieldset>
                      <legend>Formato del Reporte </legend>
                      <table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
                        <?php $nCol = f_Format_Cols(24);
                        echo $nCol;?>
                        <tr>
                          <td class="name" colspan = "6"><br>Desplegar en:
                          </td>
                          <td class="name" colspan = "6"><br>
                            <input type="radio" name="rTipo" value="1" checked onclick="fnHabilitarProBg(this.value)">Pantalla
                          </td>
                          <td class="name" colspan = "6"><br>
                            <input type="radio" name="rTipo" value="2" onclick="fnHabilitarProBg(this.value)">Excel
                          </td>
                          <td class="name" colspan = "6"><br>
                            <input type="radio" name="rTipo" value="3" onclick="fnHabilitarProBg(this.value)">Pdf<br>
                          </td>
                        </tr>
                      </table>
                    </fieldset>

                    <fieldset>
                      <legend>Tipo de Reporte </legend>
                      <table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
                        <?php $nCol = f_Format_Cols(24);
                        echo $nCol;?>
                        <tr>
                          <td class="name" colspan = "12"><br>
                            <input type="radio" name="rTipCta" value="PAGAR" id="rTipCtaP" checked>Cuentas Por Pagar
                          </td>
                          <td class="name" colspan = "12"><br>
                            <input type="radio" name="rTipCta" value="COBRAR" id="rTipCtaC">Cuentas por Cobrar
                          </td>
                        </tr>
                      </table>
                    </fieldset>
                    
                    <fieldset>
                      <legend>Condiciones del Reporte </legend>
                      <table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
                        <?php $nCol = f_Format_Cols(24);
                        echo $nCol;?>
                        <tr>
                          <td class="name" colspan = "12"><br>Aplica Cartera por Edades:
                          </td>
                          <td class="name" colspan = "6"><br>
                            <input type="radio" name="rCarEda" value="SI" onclick="javascript:f_HideShow(this.value);" checked>SI
                          </td>
                          <td class="name" colspan = "6"><br>
                            <input type="radio" name="rCarEda" value="NO" onclick="javascript:f_HideShow(this.value);">NO
                          </td>
                        </tr>
                        <tr>
                          <td colspan="23">
                            <fieldset id='edades'>
                            <br>
                              <legend>Edades de Cartera</legend>                                                      
                          		<table border = '0' cellpadding = '0' cellspacing = '0' width='460'>
                           			<?php $zCol = f_Format_Cols(23);
                           			echo $zCol;?>
            										<td Class = "name" colspan = "6"
            									    onmouseover="javascript:status='Rango Uno'"
            									    onmouseout ="javascript:status=''">Rango Uno<br>
            											<input type = "text" Class = "letra" style = "width:120;text-align:right" name = "nComVlr01" value="90" maxlength = "4"
            									    	onfocus = "javascript:this.style.background='#00FFFF'"
            									    	onblur  = "javascript:f_FixFloat(this);
            																						 this.style.background='#FFFFFF'">
            										</td>
            										<td Class = "name" colspan = "6"
            									    onmouseover="javascript:status='Rango Dos'"
            									    onmouseout ="javascript:status=''">Rango Dos<br>
            											<input type = "text" Class = "letra" style = "width:120;text-align:right" name = "nComVlr02" value="180" maxlength = "4"
            									    	onfocus = "javascript:this.style.background='#00FFFF'"
            									    	onblur  = "javascript:f_FixFloat(this);
            																						 this.style.background='#FFFFFF'">
            										</td>
            										<td Class = "name" colspan = "6"
            									    onmouseover="javascript:status='Rango Tres'"
            									    onmouseout ="javascript:status=''">Rango Tres<br>
            											<input type = "text" Class = "letra" style = "width:120;text-align:right" name = "nComVlr03" value="270" maxlength = "4"
            									    	onfocus = "javascript:this.style.background='#00FFFF'"
            									    	onblur  = "javascript:f_FixFloat(this);
            																						 this.style.background='#FFFFFF'">
            										</td>
            										<td Class = "name" colspan = "5"
            									    onmouseover="javascript:status='Rango Cuatro'"
            									    onmouseout ="javascript:status=''">Rango Cuatro<br>
            											<input type = "text" Class = "letra" style = "width:100;text-align:right" name = "nComVlr04" value="360" maxlength = "4"
            									    	onfocus = "javascript:this.style.background='#00FFFF'"
            									    	onblur  = "javascript:f_FixFloat(this);
            																						 this.style.background='#FFFFFF'">
            										</td>
                              </table>
                              <br>
                            </fieldset>
                          </td>
                        </tr>
                      </table>
                      <table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
                        <?php $nCol = f_Format_Cols(24);
                        echo $nCol;?>
                        <tr>
                          <td class="name" colspan = "8"><br>Rango de Cuentas:
                          </td>
                          <td class="name" colspan = "7">
                            <input type = "text" Class = "letra" style = "width:140" name = "cPucIni"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                   f_Links('cPucIni','VALID');
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus="javascript:document.forms['frgrm']['cPucIni'].value  ='';
                                                   this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
      						        <td class="name" colspan = "2">
      					           <select name = "cOpe01" class="letrase" style="width:40">
                            <option value = "A" selected>A &nbsp;&nbsp;</option>
                            <option value = "Y">Y &nbsp;&nbsp;</option>
                           </select>
      			              </td>
                          <td class="name" colspan = "7">
                            <input type = "text" Class = "letra" style = "width:140" name = "cPucFin"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                   f_Links('cPucFin','VALID');
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus="javascript:document.forms['frgrm']['cPucFin'].value  ='';
                                                   this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>                          
                          <td class="name" colspan = "0">
                            <input type = "text" Class = "letra" style = "width:0" readonly>
                          </td>                          
                        </tr>                        
                        <tr>
                          <td class="name" colspan = "8"><br>Rango de Nits:
                          </td>
                          <td class="name" colspan = "7">
                            <input type = "text" Class = "letra" style = "width:140" name = "nNitIni"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                   f_Links('nNitIni','VALID');
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus="javascript:document.forms['frgrm']['nNitIni'].value  ='';
                                                   this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
      						        <td class="name" colspan = "2">
      					           <select name = "cOpe02" class="letrase" style="width:40">
                            <option value = "A" selected>A &nbsp;&nbsp;</option>
                            <option value = "Y">Y &nbsp;&nbsp;</option>
                           </select>
      			              </td>
                          <td class="name" colspan = "7">
                            <input type = "text" Class = "letra" style = "width:140" name = "nNitFin"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                   f_Links('nNitFin','VALID');
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus="javascript:document.forms['frgrm']['nNitFin'].value  ='';
                                                   this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>                          
                          <td class="name" colspan = "0">
                            <input type = "text" Class = "letra" style = "width:0" readonly>
                          </td>                          
                        </tr>                        
                        <tr>
                          <td class="name" colspan = "8"><br>Rango Centros Costo:
                          </td>
                          <td class="name" colspan = "7">
                            <input type = "text" Class = "letra" style = "width:140" name = "nCcoIni"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                   f_Links('nCcoIni','VALID');
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus="javascript:document.forms['frgrm']['nCcoIni'].value  ='';
                                                   this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>
      						        <td class="name" colspan = "2">
      					           <select name = "cOpe03" class="letrase" style="width:40">
                            <option value = "A" selected>A &nbsp;&nbsp;</option>
                            <option value = "Y">Y &nbsp;&nbsp;</option>
                           </select>
      			              </td>
                          <td class="name" colspan = "7">
                            <input type = "text" Class = "letra" style = "width:140" name = "nCcoFin"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                   f_Links('nCcoFin','VALID');
                                                   this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus="javascript:document.forms['frgrm']['nCcoFin'].value  ='';
                                                   this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                          </td>                          
                          <td class="name" colspan = "0">
                            <input type = "text" Class = "letra" style = "width:0" readonly>
                          </td>                          
                        </tr>                                                                                                                                                
												<tr>
													<td class="name" colspan = "8"><br><a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">Fecha de Corte:</a></td>
													<td class="name" colspan = "7"><br>
															<input type="text" name="dHasta" style = "width:140;text-align:center"
																		 onblur="javascript:chDate(this);">
													</td>
												</tr>
                      </table>
                    </fieldset>
                    <br>
                    <fieldset>
                      <legend>Ordenamiento del Reporte </legend>
                      <table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
                        <?php $nCol = f_Format_Cols(24);
                        echo $nCol;?>
                        <tr>
                          <td class="name" colspan = "6"><br>
                            <input type="radio" name="rOrdRep" value="NIT" id="rOrdRepN" checked>NIT
                          </td>
                          <td class="name" colspan = "6"><br>
                            <input type="radio" name="rOrdRep" value="CUENTA" id="rOrdRepC">Cuenta
                          </td>
                          <td class="name" colspan = "6"><br>
                            <input type="radio" name="rOrdRep" value="ALFABETICO" id="rOrdRepA">Alfab&eacute;tico
                          </td>
                          <td class="name" colspan = "6"><br>
                            <input type="radio" name="rOrdRep" value="MONTO" id="rOrdRepM">Monto
                          </td>
                        </tr>
                      </table>
                    </fieldset>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
                      <?php $nCol = f_Format_Cols(25); echo $nCol;?>
                      <tr id="EjProBg" style="display: none">
                        <td Class = "name" colspan = "25"><br>
                          <label><input type="checkbox" name="cEjProBg" value ="SI" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}" checked>Ejecutar Proceso en Background</label>
                        </td>
                      </tr>
                    </table>
                  </fieldset>
                  <table border="0" cellpadding="0" cellspacing="0" width="500">
                    <tr height="21">
                      <td width="318" height="21"></td>
                      <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = "javascript:f_Aceptar();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
                      <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
                    </tr>
                  </table>
                </center>
              </form>
            </td>
          </tr>
        </table>
      </center>
      <?php 
      if(count($mArcProBg) > 0) { ?>
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
                        <td align="center"><strong>Filtros</strong></td>
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
                        <?php

                        ?>
                        <td style="padding:2px"><?php echo $mArcProBg[$i]['regunomx']; ?></td>
                        <td style="padding:2px">
                          <?php
                          echo '<strong>Tipo de Reporte: </strong>'.($mArcProBg[$i]['rTipCta'] == "PAGAR" ? 'Cuentas Por Pagar' : 'Cuentas por Cobrar' ).'<br>';
                          echo "<strong>Condiciones del Reporte: </strong><a href=\"javascript:fnVerFiltros('{$mArcProBg[$i]['rCarEda']}','{$mArcProBg[$i]['pbaidxxx']}')\">Ver</a>";
                          echo "<br><strong>Ordenamiento del Reporte: </strong>{$mArcProBg[$i]['rOrdRep']}"; 
                          ?>
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
      <?php 
      } ?>
    </body>
</html>
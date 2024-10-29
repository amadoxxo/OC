<?php
  namespace openComex;
	 /**
	 * Imprime Reporte de Auxiliar por Cuenta.
	 * --- Descripcion: Permite Imprimir Reporte de Auxiliar por Cuenta.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 * @version 002
	 */

	include("../../../../libs/php/utility.php");
	$cAno = date('Y');
	//f_Mensaje(__FILE__,__LINE__,$cAno);

$dHoy = date('Y-m-d');

$qSysProbg = "SELECT * ";
$qSysProbg .= "FROM $cBeta.sysprobg ";
$qSysProbg .= "WHERE ";
$qSysProbg .= "DATE(regdcrex) =\"$dHoy\" AND ";
$qSysProbg .= "regusrxx = \"$kUser\" AND ";
$qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
$qSysProbg .= "pbamodxx = \"FACTURACION\" AND ";
$qSysProbg .= "pbatinxx = \"ESTADODECUENTA\" ";
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
  		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
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
				  case "cPucIdIni":
				  case "cPucIdFin":
						if (xSwitch == "VALID") {
							var cRuta  = "frauc115.php?gWhat=VALID&gFunction="+xLink+"&cPucId="+document['frgrm'][xLink].value.toUpperCase()+"";
							parent.fmpro.location = cRuta;
						} else {
		  				var nNx     = (nX-600)/2;
							var nNy     = (nY-250)/2;
							var cWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
							var cRuta   = "frauc115.php?gWhat=WINDOW&gFunction="+xLink+"&cPucId="+document['frgrm'][xLink].value.toUpperCase()+"";
							zWindow = window.open(cRuta,"zWindow",cWinPro);
					  	zWindow.focus();
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
					} else{
						if(val.length > 0){
							alert('Fecha erronea, verifique');
						}
						
						fld.value = '';
						fld.focus();
					}
				}
			}

			function f_GenSql(){
				//Validaciones
				var nSwicht = 0;
				var cMen = "\n";
				
				if(document.forms['frgrm']['cPucIdIni'].value == '' || document.forms['frgrm']['cPucIdFin'].value ==''){
					nSwicht = 1;
					cMen += "Debe Seleccionar un Rango de Cuentas.\n";
				} else {
					//la cuenta inicial debe ser menor o igual a la cuenta final
					if(parseInt(document.forms['frgrm']['cPucIdIni'].value)>parseInt(document.forms['frgrm']['cPucIdFin'].value)){
						nSwicht = 1;
						cMen += "La Cuenta Inical debe ser Menor o Igual a la Cuenta Final.\n";
					}
				}
				
				if(document.forms['frgrm']['dDesde'].value == '' || document.forms['frgrm']['dHasta'].value ==''){
					nSwicht = 1;
			 		cMen += "Debe Seleccionar un Rango de Fechas.\n";
				} else {
					//comparo que los anios seleccionados sean los mismos
  				var mFecIni = document.forms['frgrm']['dDesde'].value.split("-");
  				var mFecFin = document.forms['frgrm']['dHasta'].value.split("-");
					if(mFecIni[0] != mFecFin[0]){
						nSwicht = 1;
						cMen += "El Rango de Fechas debe ser del Mismo Anio.\n";
					}
				}
				
				if(nSwicht == 0){
  				var cTipo = 0;
  		  	for (i=0;i<3;i++){
						if (document.forms['frgrm']['rTipo'][i].checked == true){
							cTipo = i+1;
							break;
						}
					}

					if (cTipo != 2 && cTipo != 3) {
            document.forms['frgrm']['cEjProBg'].checked = false;
            document.forms['frgrm']['cEjProBg'].value = "NO";
          }
        			  
        	var cRuta = 'fraucprn.php?gPucIdIni='+document.forms['frgrm']['cPucIdIni'].value+
                      '&gPucIdFin='+document.forms.frgrm['cPucIdFin'].value+
											'&gDesde='+document.forms.frgrm['dDesde'].value+
                      '&gHasta='+document.forms.frgrm['dHasta'].value+
											'&cTipo='+document.forms['frgrm']['rTipo'][i].value+
                      '&cEjProBg='+document.forms['frgrm']['cEjProBg'].value;

			  	if(cTipo == 2 || cTipo == 3){
			  		parent.fmpro.location = cRuta;
			  	} else {
						var zX      = screen.width;
						var zY      = screen.height;
						var nNx     = 0;
						var nNy     = 0;
						var cWinPro = "width="+zX+",scrollbars=1,resizable=YES,height="+zY+",left="+nNx+",top="+nNy;
						var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
						zWindow = window.open(cRuta,cNomVen,cWinPro);
						zWindow.focus();
					}
				} else {
			 		alert(cMen);
				}
			}

			function fnHabilitarProBg(cTipo){
        if(cTipo == 2 || cTipo == 3){
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
          <form name='frgrm' action='frpclprn.php' method="POST">
            <fieldset>
              <legend>Consulta Auxiliar por Cuenta </legend>
              <center>
                <table border="2" cellspacing="0" cellpadding="0" width="500">
                  <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
                    <td class="name"><center><h5><br>REPORTE AUXILIAR POR CUENTA</h5></center></td>
                  </tr>
                </table>
                <table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
                  <?php $nCol = f_Format_Cols(25);
                  echo $nCol;?>
                  <tr>
        	          <td class="name" colspan = "7"><br>Desplegar en:
        			      </td>
        			      <td class="name" colspan = "4"><br>
        	            <input type="radio" name="rTipo" value="1" checked onclick="fnHabilitarProBg(this.value)">Pantalla
        	          </td>
        	          <td class="name" colspan = "4"><br>
        			         <input type="radio" name="rTipo" value="2" onclick="fnHabilitarProBg(this.value)">Excel
        			      </td>
        			      <td class="name" colspan = "7"><br>
                      <input type="radio" name="rTipo" value="3" onclick="fnHabilitarProBg(this.value)">Excel Sin Formato<br>
                    </td>
        			      <td class="name" colspan = "3"><br>
        			        <input type="radio" name="rTipo" value="4" onclick="fnHabilitarProBg(this.value)">Pdf<br>
        			      </td>
        	        </tr>
        	       </table>
								<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
                  <?php $nCol = f_Format_Cols(25);
                  echo $nCol;?>
                  <tr>
          	         <td class="name" colspan = "8" width="160"><br>Rango de Cuentas (PUC):</td>
          	         <td class="name" colspan = "9"><br>
          	         <a href = "javascript:document.frgrm.cPucIdIni.value  = '';
																						f_Links('cPucIdIni','VALID')" id="IdCtaIni">Cuenta</a><br>
											<input type = "text" Class = "letra" style = "width:180" name = "cPucIdIni"
									    	onBlur = "javascript:this.value=this.value.toUpperCase();
																		         f_Links('cPucIdIni','VALID');
																		         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
									    	onFocus="javascript:document.frgrm.cPucIdIni.value  ='';
            						  									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
          	         </td>
          	         <td class="name" colspan = "9"><br>
          	         <a href = "javascript:document.frgrm.cPucIdFin.value  = '';
																						f_Links('cPucIdFin','VALID')" id="IdCtaIni">Cuenta</a><br>
											<input type = "text" Class = "letra" style = "width:180" name = "cPucIdFin"
									    	onBlur = "javascript:this.value=this.value.toUpperCase();
																		         f_Links('cPucIdFin','VALID');
																		         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
									    	onFocus="javascript:document.frgrm.cPucIdFin.value  ='';
            						  									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
          	         </td>
          	       </tr>
								</table>
								<table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
                  <?php $nCol = f_Format_Cols(25);
                  echo $nCol;?>
                  <tr>
          	         <td class="name" colspan = "8" width="160"><br>Rango De Fechas<br>(Fecha Doc.):</td>
          	         <td class="name" colspan = "2" width="40"><br><center><a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">De</a></center></td>
          	         <td class="name" colspan = "7"><br>
          	            <input type="text" name="dDesde" style = "width:140;text-align:center"
          	               onblur="javascript:chDate(this);">
          	         </td>
          	         <td class="name" colspan = "2" width="40"><br><center><a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">A</a></center></td>
          	         <td class="name" colspan = "6"><br>
          	            <input type="text" name="dHasta" style = "width:140;text-align:center"
          	              onblur="javascript:chDate(this);">
          	         </td>
									</tr>
									
									<tr id="EjProBg" style="display: none">
										<td Class = "name" colspan = "25"><br>
											<label><input type="checkbox" name="cEjProBg" value ="SI" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}" checked>Ejecutar Proceso en Background</label>
										</td>
									</tr>
                 </table>
              </center>
            </fieldset>
            <center>
              <table border="0" cellpadding="0" cellspacing="0" width="500">
                <tr height="21">
                  <td width="318" height="21"></td>
                  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:f_GenSql()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
                  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
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
                    <td align="center"><strong>Rango Cuentas</strong></td>
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
                    <td style="padding:2px"><?php echo $mArcProBg[$i]['gPucIdIni'] . "  " . $mArcProBg[$i]['gPucIdFin']; ?></td>
										<td style="padding:2px"><?php echo $mArcProBg[$i]['gDesde'] . " " . $mArcProBg[$i]['gHasta']; ?></td>
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
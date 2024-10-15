<?php
  namespace openComex;
		/**
	 * Imprime Reporte de Log de Traslado DO a DO.
	 * --- Descripcion: Permite Imprimir Reporte de Log de Traslado DO a DO.
	 * @author Johana Arboleda Ramos <johana.arboleda@open-eb.co>
	 * @version 001
	 */

	include("../../../../libs/php/utility.php");
	$cAno = date('Y');

	$dHoy = date('Y-m-d');

	$qSysProbg = "SELECT * ";
	$qSysProbg .= "FROM $cBeta.sysprobg ";
	$qSysProbg .= "WHERE ";
	$qSysProbg .= "DATE(regdcrex) =\"$dHoy\" AND ";
	$qSysProbg .= "regusrxx = \"$kUser\" AND ";
	$qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
	$qSysProbg .= "pbamodxx = \"FACTURACION\" AND ";
	$qSysProbg .= "pbatinxx = \"TRASLADODO\" ";
	$qSysProbg .= "ORDER BY regdcrex DESC";
	$xSysProbg = f_MySql("SELECT", "", $qSysProbg, $xConexion01, "");

	$mArcProBg = array();

	while ($xRB = mysql_fetch_array($xSysProbg)) {
		$vArchivos = explode("~", trim($xRB['pbaexcxx'], "~"));
		for ($nA = 0; $nA < count($vArchivos); $nA++) {
			$nInd_mArcProBg = count($mArcProBg);
			$cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . "propios/" . $cAlfa . "/traslado_do" . "/" . $vArchivos[$nA];
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
			$mArcProBg[$nInd_mArcProBg]['progreso'] = ($xRB['regdinix'] == "" || $xRB['regdinix'] == "0000-00-00 00:00:00") ? "" : (($xRB['regdfinx'] != "0000-00-00 00:00:00") ? "100" : $nTieEst);

			$mArcProBg[$nInd_mArcProBg]['pbarespr'] = $xRB['pbarespr'];
			$mArcProBg[$nInd_mArcProBg]['regestxx'] = $xRB['regestxx'];

			$mPost = f_Explode_Array($xRB['pbapostx'], "|", "~");
			for ($nP = 0; $nP < count($mPost); $nP++) {
				if ($mPost[$nP][0] != "") {
					$mArcProBg[$nInd_mArcProBg][$mPost[$nP][0]] = $mPost[$nP][1];
				}
			}
		}
	}	
	//f_Mensaje(__FILE__,__LINE__,$cAno);
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
				document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
    		parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			}

			function fnRecargar() {
				parent.fmwork.location="<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>";
			}

			function f_Links(xLink,xSwitch) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
					case "cDocIdOri":
          case "cDocIdDes":
            if (xSwitch == "VALID") {
              var cPathUrl  = "frtmd121.php?gWhat=VALID&gFunction="+xLink+
                              "&gDocNro="+document.forms['frnav'][xLink].value.toUpperCase();
              parent.fmpro.location = cPathUrl;
            } else {
              var nNx     = (nX-400)/2;
              var nNy     = (nY-250)/2;
              var cWinOpt = 'width=400,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var cPathUrl  = "frtmd121.php?gWhat=WINDOW&gFunction="+xLink+
                              "&gDocNro="+document.forms['frnav'][xLink].value.toUpperCase();
              cWindow = window.open(cPathUrl,"cWindow",cWinOpt);
              cWindow.focus();
            }
          break;
					case "cTerId":
					case "cTerNom":
						if (xLink == "cTerId" || xLink == "cTerNom") {
							var cTerTip = 'CLICLIXX';
							var cTerId = document.forms['frnav']['cTerId'].value.toUpperCase();
							var cTerNom = document.forms['frnav']['cTerNom'].value.toUpperCase();
						}
						if (xSwitch == "VALID") {
							var cPathUrl = "frtmd150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gTerTip="+cTerTip+
																				"&gTerId="+cTerId+
																				"&gTerNom="+cTerNom;
							//alert(cPathUrl);
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-600)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "frtmd150.php?gModo="+xSwitch+"&gFunction="+xLink+
																					"&gTerTip="+cTerTip+
																					"&gTerId="+cTerId+
																					"&gTerNom="+cTerNom;
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
							cWindow.focus();
						}
					break;
				}
			}

			function f_GenSql(){
				var nSwitch = 0;
				var dAnioD = document.forms['frnav']['dDesde'].value;
				var dAnioH = document.forms['frnav']['dHasta'].value;
				
				if(document.forms['frnav']['dDesde'].value == '' || document.forms.frnav['dHasta'].value == ''){
					alert("Debe Seleccionar un Rango de Fechas, Verifique.");
					nSwitch = 1;
				}

				if(dAnioD.substr(0,4) != dAnioH.substr(0,4)){
					alert("Debe Seleccionar un Rango de Fechas del mismo Ano, Verifique.");
					nSwitch = 1;
				}

				if(nSwitch == 0){
					var cTipo = 0;
					for (i=0;i<2;i++){
						if (document.forms['frnav']['rTipo'][i].checked == true){
							cTipo = i+1;
							break;
						}
					}
					
					var cPathUrl = 'frtmdobs.php?gDesde='+document.forms['frnav']['dDesde'].value+
													'&gHasta='    +document.forms.frnav['dHasta'].value     +
													'&gSucIdOri=' +document.forms['frnav']['cSucIdOri'].value  +
													'&gDocIdOri='+document.forms['frnav']['cDocIdOri'].value +
													'&gDocSufOri='+document.forms['frnav']['cDocSufOri'].value +
                          '&gSucIdDes=' +document.forms['frnav']['cSucIdDes'].value  +
													'&gDocIdDes='+document.forms['frnav']['cDocIdDes'].value +
													'&gDocSufDes='+document.forms['frnav']['cDocSufDes'].value +
													'&gTerId='    +document.forms['frnav']['cTerId'].value  +
													'&gTipo='     +document.forms['frnav']['rTipo'][i].value+
													'&gEjProBg='  +document.forms['frnav']['cEjProBg'].value;
					if(cTipo == 2){        			  	
						parent.fmpro.location = cPathUrl;
					}else{
						var nX      = screen.width;
						var nY      = screen.height;
						var nNx     = 0;  				
						var nNy     = 0;
						var cWinOpt = "width="+nX+",scrollbars=1,resizable=YES,height="+nY+",left="+nNx+",top="+nNy;
						var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
						cWindow = window.open(cPathUrl,cNomVen,cWinOpt);
						cWindow.focus();
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

			function fnHabilitarProBg(cTipo){
        if(cTipo == 2){
          document.getElementById('EjProBg').style.display = '';
        } else{
          document.forms['frnav']['cEjProBg'].checked = false;
          document.forms['frnav']['cEjProBg'].value = "NO";
          document.getElementById('EjProBg').style.display = 'none';
        }
      }

			function fnDescargar(xArchivo){
        parent.fmpro.location = "frtmddoc.php?cRuta="+xArchivo+"&gBg=SI";
      }

		</script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
	<center>
		<table border ="0" cellpadding="0" cellspacing="0" width="500">
			<tr>
				<td>
					<form name='frnav' action='frtmdprn.php' method="POST">
						<fieldset>
							<legend>Reporte Observaciones Traslado DO a DO</legend>
							<center>
								<table border="2" cellspacing="0" cellpadding="0" width="500">
									<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
										<td class="name" width="30%"><center><h5><br>REPORTE OBSERVACIONES TRASLADO DO A DO</h5></center></td>
									</tr>
								</table>
								<table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
									<?php $nCol = f_Format_Cols(25);
									echo $nCol;?>
									<tr>
										<td class="name" colspan = "7"><br>Desplegar en:
										</td>
										<td class="name" colspan = "6"><br>
											<input type="radio" name="rTipo" value="1" checked onclick="fnHabilitarProBg(this.value)">Pantalla
										</td>
										<td class="name" colspan = "13"><br>
												<input type="radio" name="rTipo" value="2" onclick="fnHabilitarProBg(this.value)">Excel
										</td>
									</tr>
									</table>
								<table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
									<?php $nCol = f_Format_Cols(24);
									echo $nCol;?>
									<tr>
											<td class="name" colspan = "10"><br>Rango De Fechas (Traslado):</td>
											<td class="name" colspan = "2"><br><center><a href='javascript:show_calendar("frnav.dDesde")' id="id_href_dDesde">De</a></center></td>
											<td class="name" colspan = "5"><br>
												<input type="text" name="dDesde" style = "width:100;text-align:center"
														onblur="javascript:chDate(this);">
											</td>
											<td class="name" colspan = "2"><br><center><a href='javascript:show_calendar("frnav.dHasta")' id="id_href_dHasta">A</a></center></td>
											<td class="name" colspan = "5"><br>
												<input type="text" name="dHasta" style = "width:100;text-align:center"
													onblur="javascript:chDate(this);">
											</td>
										</tr>
										<tr>
										<tr>
											<td class="name" colspan = "05"><br>
												<a href = "javascript: document.forms['frnav']['cSucIdOri'].value='';
                                                document.forms['frnav']['cDocTipOri'].value='';
                                                document.forms['frnav']['cDocIdOri'].value='';
                                                document.forms['frnav']['cDocSufOri'].value='';
																								f_Links('cDocIdOri','VALID');" id="vDir">Do Origen:</a>
											</td>
											<td class="name" colspan = "21"><br>
                        <input type="text"   class="letra" name="cSucIdOri"  style="width:30" readonly>
                        <input type="text"   class="letra" name="cDocTipOri" style="width:80" readonly>
                        <input type="text"   class="letra" name="cDocIdOri" style="width:80"
                            onBlur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';
                                      f_Links('cDocIdOri','VALID');"
                            onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';
                                      document.forms['frnav']['cSucIdOri'].value='';
                                      document.forms['frnav']['cDocTipOri'].value='';
                                      document.forms['frnav']['cDocIdOri'].value='';
                                      document.forms['frnav']['cDocSufOri'].value='';">
                      <input type="text"   class="letra" name="cDocSufOri" style="width:30" readonly>
										</td>
									</tr>
                  <tr>
											<td class="name" colspan = "05"><br>
												<a href = "javascript: document.forms['frnav']['cSucIdDes'].value='';
                                                document.forms['frnav']['cDocTipDes'].value='';
                                                document.forms['frnav']['cDocIdDes'].value='';
                                                document.forms['frnav']['cDocSufDes'].value='';
																								f_Links('cDocIdDes','VALID');" id="vDir">Do Destino:</a>
											</td>
											<td class="name" colspan = "21"><br>
                        <input type="text"   class="letra" name="cSucIdDes"  style="width:30" readonly>
                        <input type="text"   class="letra" name="cDocTipDes" style="width:80" readonly>
                        <input type="text"   class="letra" name="cDocIdDes" style="width:80"
                            onBlur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';
                                      f_Links('cDocIdDes','VALID');"
                            onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';
                                      document.forms['frnav']['cSucIdDes'].value='';
                                      document.forms['frnav']['cDocTipDes'].value='';
                                      document.forms['frnav']['cDocIdDes'].value='';
                                      document.forms['frnav']['cDocSufDes'].value='';">
                      <input type="text"   class="letra" name="cDocSufDes" style="width:30" readonly>
										</td>
									</tr>
									<tr>
										<td class="name" colspan = "05"><br>
											<a href = "javascript:document.forms['frnav']['cTerId'].value  = '';
																						document.forms['frnav']['cTerDV'].value  = '';
																						document.forms['frnav']['cTerNom'].value = '';
																						f_Links('cTerId','VALID')" id="id_href_cTerId">Cliente:</a>
										</td>
										<td class="name" colspan = "05"><br>
											<input type="text" name="cTerId" style = "width:100"
														onfocus="javascript:document.forms['frnav']['cTerId'].value  = '';
																								document.forms['frnav']['cTerDV'].value  = '';
																								document.forms['frnav']['cTerNom'].value = '';
																								this.style.background='#00FFFF'"
														onBlur = "javascript:this.value=this.value.toUpperCase();
																								f_Links('cTerId','VALID');
																								this.style.background='#FFFFFF'">
										</td>
										<td Class = "name" colspan = "02"><br>
											<input type = "text" style = "width:40;text-align:center" name = "cTerDV" readonly>
										</td>
										<td class="name" colspan = "11"><br>
											<input type="text" name="cTerNom" style = "width:220"
														onfocus="javascript:document.forms['frnav']['cTerId'].value  = '';
																								document.forms['frnav']['cTerDV'].value  = '';
																								document.forms['frnav']['cTerNom'].value = '';
																								this.style.background='#00FFFF'"
														onBlur = "javascript:this.value=this.value.toUpperCase();
																								f_Links('cTerNom','VALID');
																								this.style.background='#FFFFFF'">
										</td>
									</tr>
								</table>
								<table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
									<?php $nCol = f_Format_Cols(24);
									echo $nCol;?>
									<tr id="EjProBg" style="display: none">
										<td Class = "name" colspan = "25"><br>
											<label><input type="checkbox" id="cEjProBg" name="cEjProBg" value ="NO" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}">Ejecutar Proceso en Background</label>
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
                    <td align="center"><strong>Par&aacute;metros</strong></td>
                    <td align="center"><strong>Progreso</strong></td>
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
										
										<td>
												
												<?php 
												if($mArcProBg[$i]['gDesde'] != ""){?>
														<?php echo "<strong>&raquo; Fechas:</strong> ". $mArcProBg[$i]['gDesde'] . " " . $mArcProBg[$i]['gHasta']."<br>"; ?>
													<?php
												}
												?>

												<?php 
												if($mArcProBg[$i]['gDocNroOri'] != ""){?>
														<?php echo "<strong>&raquo; DO Origen:</strong> ". $mArcProBg[$i]['gSucIdOri']."-".$mArcProBg[$i]['gDocNroOri']."-".$mArcProBg[$i]['gDocSufOri']."<br>"; ?>
													<?php
												}
												?>

                        <?php 
												if($mArcProBg[$i]['gDocNroDes'] != ""){?>
														<?php echo "<strong>&raquo; DO Destino:</strong> ". $mArcProBg[$i]['gSucIdDes']."-".$mArcProBg[$i]['gDocNroDes']."-".$mArcProBg[$i]['gDocSufDes']."<br>"; ?>
													<?php
												}
												?>

												<?php 
												if($mArcProBg[$i]['gTerId'] != ""){?>
														<?php echo "<strong>&raquo; Cliente:</strong> ". $mArcProBg[$i]['gTerId']."<br>"; ?>
													<?php
												}
												?>
										</td>
                    <td style="padding:2px"><?php echo $mArcProBg[$i]['progreso']; ?></td>
                    <td style="padding:2px"><?php echo $mArcProBg[$i]['pbarespr']; ?></td>
                    <td style="padding:2px"><?php echo $mArcProBg[$i]['regestxx']; ?></td>
                    <td>
                      <?php if ($mArcProBg[$i]['pbaexcxx'] != "") { ?>
                        
                      <?php } ?>
                      <a href = "javascript:fnDescargar('')">
                        Descargar
                      </a>
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
<?php
  namespace openComex;
	/**
	 * Imprime Reporte de Reporte Control Cupos - Siaco
	 * --- Descripcion: Permite Imprimir Reporte Control Cupos - Siaco
	 * @author Camilo Dulce <camilo.dulce@open-eb.co>
	 */
	include("../../../../libs/php/utility.php");

	$today = date('Y-m-d');
	$yesterday = date('Y-m-d', strtotime($today . ' -1 day'));

	$qSysProbg = "SELECT * ";
	$qSysProbg .= "FROM $cBeta.sysprobg ";
	$qSysProbg .= "WHERE ";
	$qSysProbg .= "DATE(regdcrex) BETWEEN \"$today\" AND \"$today\" AND ";
	$qSysProbg .= "regusrxx = \"$kUser\" AND ";
	$qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
	$qSysProbg .= "pbamodxx = \"FACTURACION\" AND ";
	$qSysProbg .= "pbatinxx = \"REPORTECONTROLCUPOSSIACO\" ";
	$qSysProbg .= "ORDER BY regdcrex DESC";
	$xSysProbg = f_MySql("SELECT", "", $qSysProbg, $xConexion01, "");
	// f_Mensaje(__FILE__, __LINE__,$qSysProbg."~".mysql_num_rows($xSysProbg));

	$mArcProBg = array();

	while ($xRB = mysql_fetch_array($xSysProbg)) {
		$vArchivos = explode("~", trim($xRB['pbaexcxx'], "~"));
		for ($nA = 0; $nA < count($vArchivos); $nA++) {
			$nInd_mArcProBg = count($mArcProBg);
			$cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . "propios/" . $cAlfa . "/control_cupos" . "/" . $vArchivos[$nA];
			if ($vArchivos[$nA] != "" && file_exists($cRuta)) {
				$mArcProBg[$nInd_mArcProBg]['pbaexcxx'] = $vArchivos[$nA];
			} else {
				$mArcProBg[$nInd_mArcProBg]['pbaexcxx'] = "";
			}

			$mArcProBg[$nInd_mArcProBg]['pbaerrxx'] = $xRB['pbaerrxx'];
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
			$mArcProBg[$nInd_mArcProBg]['regusrxx'] = $xRB['regusrxx'];

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

			function fnLinks(xLink,xSwitch,xIteration) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
					case "cCliId":
					case "cCliNom":
						if (xLink == "cCliId" || xLink == "cCliNom") {
							var cCliId  = document.forms['frgrm']['cCliId'].value.toUpperCase();
							var cCliNom = document.forms['frgrm']['cCliNom'].value.toUpperCase();
						}
						if (xSwitch == "VALID") {
							var cRuta = "frrep150.php?gModo="+xSwitch+"&gFunction="+xLink+
																			"&gCliId="+cCliId+"&gCliNom="+cCliNom;
							parent.fmpro.location = cRuta;
						} else {
							var nNx      = (nX-600)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cRuta = "frrep150.php?gModo="+xSwitch+"&gFunction="+xLink+
																			"&gCliId="+cCliId+"&gCliNom="+cCliNom;
							cWindow = window.open(cRuta,xLink,cWinOpt);
							cWindow.focus();
						}
					break;
				}
			}

			function fnGenerar()  {
			
				//Validaciones
				var nSwicht = 0;
				var cMen = "\n";
				
				if(nSwicht == 0){
					var cTipo = 0;
					for (i=0;i<2;i++){
						if (document.forms['frgrm']['rTipo'][i].checked == true){
							cTipo = i+1;
							break;
						}
					}
									
					var cRuta = 'frrepprn.php';
					if(cTipo == 2){        			  	
						document.forms['frgrm'].target='fmpro'; 
						document.forms['frgrm'].action=cRuta; 
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
						document.forms['frgrm'].action=cRuta;
						document.forms['frgrm'].submit();
					}
				}else{
					alert(cMen+"Verifique.");
				} 
			}
	
			function fnValidarBackground(myRadio) {
				switch (myRadio.value) {
					case "1":
						document.forms['frgrm']['cEjProBg'].checked = false;
						document.forms['frgrm']['cEjProBg'].value = "NO";
						document.getElementById("EjProBg").style.display="none";
					break;
					default:
						document.forms['frgrm']['cEjProBg'].checked = true;
						document.forms['frgrm']['cEjProBg'].value = "SI";
						document.getElementById("EjProBg").style.display="block";
					break;
				}
			}

			function fnDescargar(xArchivo){
				parent.fmpro.location = "frrepdoc.php?cRuta="+xArchivo+"&gBg=SI";
			}

			function fnRecargar() {
				parent.fmwork.location="<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>";
			}
		</script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="600">
				<tr>
					<td>
						<form name='frgrm' action='frrepprn.php' method="POST" target="fmwork">
							<input type = "hidden" name = "nSecuencia"  value = "0">
							<fieldset>
								<legend>Reporte Control Cupos - Siaco</legend>
								<center>
									<table border="2" cellspacing="0" cellpadding="0" width="600">
										<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
											<td class="name"><center><h3><br>Reporte Control Cupos - Siaco</h3></center></td>
										</tr>
									</table>
									<table border = '0' cellpadding = '0' cellspacing = '0' width='600'>
										<?php $nCol = f_Format_Cols(30);
										echo $nCol;?>
										<tr>
											<td class="name" colspan = "5"><br>Desplegar en:
											</td>
											<td class="name" colspan = "5"><br>
												<input type="radio" name="rTipo" onclick="fnValidarBackground(this);" value="1" checked>Pantalla
											</td>
											<td class="name" colspan = "20"><br>
												<input type="radio" name="rTipo" onclick="fnValidarBackground(this);" value="2">Excel
											</td>
										</tr>
									</table>
									<table border = '0' cellpadding = '0' cellspacing = '0' width='600'>
										<?php $nCol = f_Format_Cols(30);
										echo $nCol;?>
										<tr>
											<td class="name" colspan = "5"><br>
												<a href = "javascript:javascript:document.forms['frgrm']['cCliId'].value  = '';
																							document.forms['frgrm']['cCliNom'].value = '';
																							document.forms['frgrm']['cCliDv'].value  = '';
																							fnLinks('cCliId','WINDOW',-1)" id="idCli" title="Buscar Cliente">Cliente</a>
											</td>
											<td class="name" colspan = "4"><br>
												<input type = "text" Class = "letra" name = "cCliId" style = "width:80"
													onfocus="javascript:document.forms['frgrm']['cCliId'].value  = '';
																							document.forms['frgrm']['cCliNom'].value = '';
																							document.forms['frgrm']['cCliDv'].value  = '';
																							this.style.background='#00FFFF'"
													onBlur = "javascript:this.value=this.value.toUpperCase();
																							fnLinks('cCliId','VALID');
																							this.style.background='#FFFFFF'">
											</td>
											<td class="name" colspan = "1"><br>
												<input type = 'text' Class = 'letra' name = 'cCliDv' style = 'width:20' readonly>
											</td>
											<td class="name" colspan = "20"><br>
												<input type = "text" Class = "letra" name = "cCliNom" style = "width:400" 
													onfocus="javascript:document.forms['frgrm']['cCliId'].value  = '';
																							document.forms['frgrm']['cCliNom'].value = '';
																							document.forms['frgrm']['cCliDv'].value  = '';
																							this.style.background='#00FFFF'"
													onBlur = "javascript:this.value=this.value.toUpperCase();
																							fnLinks('cCliNom','VALID');
																							this.style.background='#FFFFFF'">
											</td>
										</tr>
										<tr>
											<td class="name" colspan = "5"><br>
												Tipo de Cupo
											</td>
											<td class="name" colspan = "25"><br>
												<select class="letrase" name = "cTipCup" style = "width:500">
													<option value = "">SELECCIONE</option>
													<option value = "SINCUPO">SIN CUPO</option>
													<option value = "LIMITADO">LIMITADO</option>
													<option value = "ILIMITADO">ILIMITADO</option>
													<option value = "LIMITADO/ILIMITADO">LIMITADO/ILIMITADO</option>
													<option value = "ILIMITADO/LIMITADO">ILIMITADO/LIMITADO</option>
													</select>
											</td>
										</tr>
									</table>
								</center>
								<center>
									<table border = '0' cellpadding = '0' cellspacing = '0' width='600' id="EjProBg" style="display:none">
										<?php $nCol = f_Format_Cols(30);
										echo $nCol;?>
										<tr>
											<td Class = "name" colspan = "30"><br>
												<label><input type="checkbox" name="cEjProBg" value ="SI" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}" >Ejecutar Proceso en Background</label>
											</td>
										</tr>
									</table>
								</center>

							</fieldset>
							<center>
								<table border="0" cellpadding="0" cellspacing="0" width="600">
									<tr height="21">
										<td width="418" height="21"></td>
										<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:fnGenerar()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
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
				<table border="0" cellpadding="0" cellspacing="0" width="600">
					<tr>
						<td Class = "name" colspan = "30"><br>
							<fieldset>
								<legend>Reportes Generados. Fecha [<?php echo date('Y-m-d'); ?>]</legend>
								<label>
									<table border="0" cellspacing="1" cellpadding="0" width="600">
										<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="height:20px">
											<td align="center" style="padding:2px"><strong>Generado por</strong></td>
											<td align="center" style="padding:2px"><strong>Par&aacute;metros</strong></td>
											<td align="center" style="padding:2px"><strong>Usuario</strong></td>
											<td align="center" style="padding:2px"><strong>Resultado</strong></td>
											<td align="center" style="padding:2px"><strong>Estado</strong></td>
											<td align="center" style="padding:2px"><img src = "<?php echo $cPlesk_Skin_Directory ?>/b_cambest.gif" onClick = "javascript:fnRecargar()" style = "cursor:pointer" title="Recargar"></td>
										</tr>
										<?php for ($i = 0; $i < count($mArcProBg); $i++) {
											$cColor = "{$vSysStr['system_row_impar_color_ini']}";
											if($i % 2 == 0) {
												$cColor = "{$vSysStr['system_row_par_color_ini']}";
											}
											
											$vUsrDat = array();
											if ($mArcProBg[$i]['regusrxx'] != "") {
												$qUsrDat  = "SELECT USRNOMXX ";
												$qUsrDat .= "FROM $cAlfa.SIAI0003 ";
												$qUsrDat .= "WHERE ";
												$qUsrDat .= "$cAlfa.SIAI0003.USRIDXXX = \"{$mArcProBg[$i]['regusrxx']}\" LIMIT 0,1 ";
												$xUsrDat = f_MySql("SELECT","",$qUsrDat,$xConexion01,"");
												$vUsrDat = mysql_fetch_array($xUsrDat);
											}

											?>
										<tr bgcolor = "<?php echo $cColor ?>">
											<td style="padding:2px"><?php echo $mArcProBg[$i]['regunomx']; ?></td>
											<td style="padding:2px">
												<?php 
													if($mArcProBg[$i]['cCliId'] != ""){?>
															<?php echo "<strong>&raquo; Cliente:</strong> ". $mArcProBg[$i]['cCliId']."<br>"; ?>
														<?php
													}
													?>

													<?php 
													if($mArcProBg[$i]['cTipCup'] != ""){?>
															<?php echo "<strong>&raquo; Tipo Cupo:</strong> ". $mArcProBg[$i]['cTipCup']."<br>"; ?>
														<?php
													}
													?>
											</td>
											<td style="padding:2px"><?php echo (($mArcProBg[$i]['regusrxx'] != "") ? "[".$mArcProBg[$i]['regusrxx']."] ".$vUsrDat['USRNOMXX'] : ""); ?></td>
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
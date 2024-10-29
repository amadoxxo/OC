<?php
  namespace openComex;
	 /**
	 * Imprime Reporte Mensual Certificado de Retenciones x Pagos a Terceros.
	 * --- Descripcion: Permite Imprimir reporte Mensual Certificado de Retenciones x Pagos a Terceros.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 * @version 002
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
  $qSysProbg .= "pbatinxx = \"CERTIFICADOXPAGOSATERCEROS\" ";
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

			function fnRecargar() {
				parent.fmwork.location="<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>";
			}

	    function f_Links(xLink,xSwitch,xSecuencia,xGrid,xType) {
				var nX    = screen.width;
				var nY    = screen.height;
				var cTerTip = 'CLICLIXX';
				switch (xLink) {
				 case "cTerId":
					case "cTerNom":
					  if (xLink == "cTerId" || xLink == "cTerNom") {
					    //var cTerTip = 'CLICLIXX';
					    var cTerId = document.forms['frgrm']['cTerId'].value.toUpperCase();
					    var cTerNom = document.forms['frgrm']['cTerNom'].value.toUpperCase();
					  }
						if (xSwitch == "VALID") {
							var cPathUrl = "frcer150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gTerTip="+cTerTip+
																				"&gTerId="+cTerId+
																				"&gTerNom="+cTerNom;
							//alert(cPathUrl);
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-600)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "frcer150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				 "&gTerTip="+cTerTip+
																				 "&gTerId="+cTerId+
																				 "&gTerNom="+cTerNom;
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
				  		cWindow.focus();
						}
					break;
				}
	    }
		
			function f_GenSql()  {
			  var nSwicht = 0;
  			var cMsj = "\n";
  			
  			var cTipo = 0;
        for (i=0;i<2;i++){
          if (document.forms['frgrm']['rTipo'][i].checked == true){
            cTipo = i+1;
            break;
          }
        }
  			
  			if(cTipo != 2){
  				if (document.forms['frgrm']['cTerId'].value == ""){
  				  cMsj += 'Debe Seleccionar un Tercero.\n';
    	 		  nSwicht = 1;
  				}
				} else {
				  if (document.forms['frgrm']['cTerId'].value == ""){
				    //Si no ha seleccionado cliente, debe ser del mismo mes
				    if (document.forms['frgrm']['cMesD'].value != document.forms['frgrm']['cMesH'].value){
              cMsj += 'Si No Selecciono Cliente, el Mes Inicial debe ser Igual al Mes Final.\n';
              nSwicht = 1;
            }
				  }
				}
				
				if (document.forms['frgrm']['cAnioD'].value != document.forms['frgrm']['cAnioH'].value){
				  cMsj += 'El ano Inicial debe ser Igual al ano Final.\n';
  	 		  nSwicht = 1;
				}				
				
				if(nSwicht == 0){

          if (cTipo != 2) {
            document.forms['frgrm']['cEjProBg'].checked = false;
            document.forms['frgrm']['cEjProBg'].value = "NO";
          }

			  	var cRuta = 'frcemprn.php?'+
			  	    				'gTerId='    +document.forms['frgrm']['cTerId'].value    +
											'&gTerNom='  +document.forms['frgrm']['cTerNom'].value   +
	            				'&gAnioD='   + document.forms['frgrm']['cAnioD'].value   +
	            				'&gMesD='    + document.forms['frgrm']['cMesD'].value    +
	            				'&gAnioH='   + document.forms['frgrm']['cAnioH'].value   +
	            				'&gMesH='    + document.forms['frgrm']['cMesH'].value    +
	            				'&cGenerar=' + document.forms['frgrm']['cGenerar'].value +
	            				'&cIntPag='  + document.forms['frgrm']['cIntPag'].value  +
											'&cEjProBg=' + document.forms['frgrm']['cEjProBg'].value +
	            				'&cTipo='    + cTipo;
	            
			  	if(cTipo == 2){  	
			  		parent.fmpro.location = cRuta;
				  }else{			  		  
	   				var zX      = screen.width;
	  				var zY      = screen.height;
	  				var zNx     = (zX-1100)/2;
	  				var zNy     = (zY-700)/2;
	  				var zWinPro = "width=1100,scrollbars=1,resizable=YES,height=700,left="+zNx+",top="+zNy;	  				
	  				zWindow = window.open(cRuta,'zWinTrp',zWinPro);
	  				zWindow.focus();
				  }
        } else {
        	alert(cMsj + "Verifique.");
	  		}
			}

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
			<table border ="0" cellpadding="0" cellspacing="0" width="520">
				<tr>
					<td>
            <form name='frgrm' action='frcemprn.php' method="POST">
							<fieldset>
								<legend>Reporte Mensual Certificado de Retenciones x Pagos a Terceros </legend>
								<center>
									<table border="2" cellspacing="0" cellpadding="0" width="">
										<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
											<td class="name" width="30%"><center><h5><br>REPORTE MENSUAL CERTIFICADO DE RETENCIONES X PAGOS A TERCEROS</h5></center></td>
										</tr>
									</table>
									<table border = '0' cellpadding = '0' cellspacing = '0' width='520'>
										<?php $nCol = f_Format_Cols(26);
										echo $nCol;?> 
										<tr>
											<td class="name" colspan = "5"><br>Desplegar en:
											</td>
											<td class="name" colspan = "5"><br>
												<input type="radio" name="rTipo" value="1" checked onclick="fnHabilitarProBg(this.value)">Pdf
											</td>
											<td class="name" colspan = "16"><br>
												<input type="radio" name="rTipo" value="2" onclick="fnHabilitarProBg(this.value)">Excel
											</td>
										</tr> 							 	
										<tr>
											<td class="name" colspan = "09"><br>Gerenerar Pagos a Terceros<br></td>
											<td class="name" colspan = "6" ><br>
												<select Class = "letrase" style = "width:120;text-align:left" name = "cGenerar">
													<option value="">TODOS</option>
													<option value="FACTURADO">FACTURADO</option>
													<option value="NOFACTURADO">NO FACTURADO</option>
												</select>
											</td>
											<td class="name" colspan = "10" align="right"><br>
												<label><input type="checkbox" name="cIntPag" value="NO" onclick="javascript: if(this.checked == true) { this.value = 'SI'; } else { this.value = 'NO'; }">&nbsp;&nbsp;Intermediaci&oacute;n de Pago</label>
											</td>
										</tr>
										<tr>
											<td Class = "name" colspan = "5">
												<a href = "javascript:document.forms['frgrm']['cTerId'].value  = '';
																						document.forms['frgrm']['cTerNom'].value = '';
																						document.forms['frgrm']['cTerDV'].value  = '';
																						f_Links('cTerId','VALID')" id="id_href_cTerId"><br>Nit</a><br>
												<input type = "text" Class = "letra" style = "width:100;text-align:center" name = "cTerId"
													onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
																						document.forms['frgrm']['cTerNom'].value = '';
																						document.forms['frgrm']['cTerDV'].value  = '';
																						this.style.background='#00FFFF'"
													onBlur = "javascript:this.value=this.value.toUpperCase();
																						f_Links('cTerId','VALID');
																						this.style.background='#FFFFFF'">
											</td>
											<td Class = "name" colspan = "1"><br>Dv<br>
												<input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDV" readonly>
											</td>
											<td Class = "name" colspan = "20"><br>Cliente<br>
												<input type = "text" Class = "letra" style = "width:400" name = "cTerNom"
													onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
																						document.forms['frgrm']['cTerNom'].value = '';
																						document.forms['frgrm']['cTerDV'].value  = '';
																						this.style.background='#00FFFF'"
													onBlur = "javascript:this.value=this.value.toUpperCase();
																						f_Links('cTerNom','VALID');
																						this.style.background='#FFFFFF'">
											</td>
										</tr>          	       
										<tr>
											<td class="name" colspan = "5"><br>Periodo De:</td>
											<td Class = "name" colspan = "3"><br>A&ntilde;o<br>
												<select Class = "letrase" name="cAnioD" style = "width:60">
													<?php for($i=2010; $i<=$cAno; $i++){
													if($i==$cAno){?>
														<option value = '<?php echo $i?>' selected><?php echo $i ?></option>
													<?php }else{?>
														<option value = '<?php echo $i?>'><?php echo $i ?></option>
													<?php }
													}?>
												</select>
											</td>
											<td class="name" colspan = "3"><br>Mes<br>
												<select Class = "letrase" name = "cMesD" style = "width:60">
													<option value = '01' selected>01</option>
													<option value = '02' selected>02</option>
													<option value = '03' selected>03</option>
													<option value = '04' selected>04</option>
													<option value = '05' selected>05</option>
													<option value = '06' selected>06</option>
													<option value = '07' selected>07</option>
													<option value = '08' selected>08</option>
													<option value = '09' selected>09</option>
													<option value = '10' selected>10</option>
													<option value = '11' selected>11</option>
													<option value = '12' selected>12</option>
												</select>
											</td>
											<td class="name" colspan = "9"><center><br>A:</center></td>
											<td Class = "name" colspan = "3"><br>A&ntilde;o<br>
												<select Class = "letrase" name="cAnioH" style = "width:60">
													<?php for($i=2010; $i<=$cAno; $i++){
													if($i==$cAno){?>
														<option value = '<?php echo $i?>' selected><?php echo $i ?></option>
													<?php }else{?>
														<option value = '<?php echo $i?>'><?php echo $i ?></option>
													<?php }
													}?>
												</select>
											</td>
											<td class="name" colspan = "3"><br>Mes<br>
												<select Class = "letrase" name = "cMesH" style = "width:60">
													<option value = '01' selected>01</option>
													<option value = '02' selected>02</option>
													<option value = '03' selected>03</option>
													<option value = '04' selected>04</option>
													<option value = '05' selected>05</option>
													<option value = '06' selected>06</option>
													<option value = '07' selected>07</option>
													<option value = '08' selected>08</option>
													<option value = '09' selected>09</option>
													<option value = '10' selected>10</option>
													<option value = '11' selected>11</option>
													<option value = '12' selected>12</option>
												</select>
											</td>
										</tr>
									</table>
								</center>
								<table>
									<tr id="EjProBg" style="display: none">
										<td Class = "name" colspan = "25"><br>
											<label><input type="checkbox" name="cEjProBg" value ="SI" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}" checked>Ejecutar Proceso en Background</label>
										</td>
									</tr> 
								</table>
							</fieldset>
							<center>
								<table border="0" cellpadding="0" cellspacing="0" width="520">
									<tr height="21">
										<td width="338" height="21"></td>
										<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:f_GenSql()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
										<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
									</tr>
								</table>
							</center>
							<script language="javascript">
								document.forms['frgrm']['cMesD'].value='<?php echo date('m') ?>';
								document.forms['frgrm']['cMesH'].value='<?php echo date('m') ?>';
							</script>
						</form>
					</td>
				</tr>
		 	</table>
		</center>
		<?php if(count($mArcProBg) > 0){ ?>
			<center>
				<table border="0" cellpadding="0" cellspacing="0" width="520">
					<tr>
						<td Class = "name" colspan = "19"><br>
							<fieldset>
								<legend>Reportes Generados. Fecha [<?php echo date('Y-m-d'); ?>]</legend>
								<label>
									<table border="0" cellspacing="1" cellpadding="0" width="520">
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
											<td style="padding:2px"><?php echo $mArcProBg[$i]['regunomx']; ?></td>
											<td style="padding:2px"><?php echo '<strong>Pagos a Terceros: </strong>'.$cGenerar = ($mArcProBg[$i]['cGenerar'] == "") ? "TODOS".'<br>' : $mArcProBg[$i]['cGenerar'].'<br>';
																										echo '<strong>Intermediaci&oacute;n de Pago: </strong>'.$mArcProBg[$i]['cIntPag'].'<br>';
																										echo $gTerId = ($mArcProBg[$i]['gTerId'] == "") ? "" : '<strong>Cliente: </strong>'.$mArcProBg[$i]['gTerId']." - ".$mArcProBg[$i]['gTerNom'].'<br>';
																										echo '<strong>Periodo: </strong>'.$mArcProBg[$i]['gAnioD']."-".$mArcProBg[$i]['gMesD']." A ".$mArcProBg[$i]['gAnioH']."-".$mArcProBg[$i]['gMesH'].'<br>'; ?></td>
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
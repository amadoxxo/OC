<?php
  namespace openComex;
	 /**
	 * Imprime Reporte Inventario de Formularios.
	 * --- Descripcion: Permite Imprimir Reporte por Pantalla,excel y Pdf del Inventario de Formularios.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
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
  $qSysProbg .= "pbatinxx = \"INVENTARIODEFORMULARIOS\" ";
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

	    function f_Links(xLink,xSwitch,xSecuencia,xGrid,xType) {
	    	var zX    = screen.width;
				var zY    = screen.height;
				switch (xLink) {
				case "cPtoId":
					if (xSwitch == "VALID") {
						var zRuta  = "frpar132.php?gWhat=VALID&gFunction=cPtoId&cPtoId="+document.frgrm.cPtoId.value.toUpperCase()+"";
						parent.fmpro.location = zRuta;
					} else {
	  				var zNx     = (zX-600)/2;
						var zNy     = (zY-250)/2;
						var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
						var zRuta   = "frpar132.php?gWhat=WINDOW&gFunction=cPtoId&cPtoId="+document.frgrm.cPtoId.value.toUpperCase()+"";
						zWindow = window.open(zRuta,"zWindow",zWinPro);
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

			// FUNCION DE SELECT PARA CONSULTA //
			function f_GenSql(){
  			var nSwitch = 0;
  			var cMen = "\n";
  			var dDesde = document.forms['frgrm']['dDesde'].value;
  			var dHasta = document.forms['frgrm']['dHasta'].value;
  			var ini = dDesde.replace('-','');
  			var fin = dHasta.replace('-','');
  			var fsi = '<?php echo date('Y-m-d') ?>';
  			var fsis = fsi.replace('-','');
  			var fsis1 = fsis.replace('-','');
  			var ini2 = ini.replace('-','');
  			var fin2 = fin.replace('-','');
  			var dAnioD = dDesde.substr(0,4);
  			var dAnioH = dHasta.substr(0,4);
  			inii = 1 * ini2;
  			fini = 1 * fin2;
  			fsi2 = 1 * fsis1;
  			if(fini > fsi2 ){
  				cMen += "Fecha Final no puede ser mayor a la Fecha de Hoy, Verifique.\n";
          document.forms['frgrm']['dDesde'].focus();
          nSwitch = 1;
        }

  	    if (fini < inii){
  	    	cMen += "Fecha Final es Menor a Inicial,verifique.\n";
    			document.forms['frgrm']['dHasta'].focus();
    			nSwitch = 1;
    		}

				if(dAnioD != dAnioH){
					cMen += "El Rango de Fechas Debe Estar Dentro del Mismo a\u00f1o, Verifique.\n";
					nSwitch = 1;
				}

      	if(document.forms['frgrm']['cPtoId'].value != '' && document.forms['frgrm']['cPtoDes'].value == ''){
      		nSwitch = 1;
      		cMen += "La Descripcion del Tipo de Formulario, No puede ser Vacia, Verifique.\n";
      	}

        if(document.forms['frgrm']['dDesde'].value.length == 0 && document.forms['frgrm']['dHasta'].value.length == 0){
       	 nSwitch = 1;
         cMen += "El Rango de Fechas No Puede Ser Vacio.\n";
        }
      	
        if(nSwitch == 0){
  				  var cTipo = 0;
    			  var cTipCta = "";
    			  for (i=0;i<3;i++){
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
    				  var zRuta = 'frrmfprn.php?cTipo='+cTipo
    				  						+'&cPtoId='+document.forms['frgrm']['cPtoId'].value
    				  						+'&cPtoDes='+document.forms['frgrm']['cPtoDes'].value
    				  						+'&dDesde='+document.forms['frgrm']['dDesde'].value
    				  						+'&dHasta='+document.forms['frgrm']['dHasta'].value
													+'&cEjProBg='+document.forms['frgrm']['cEjProBg'].value;
        			parent.fmpro.location = zRuta;
        		}else{
     					var zX      = screen.width;
    					var zY      = screen.height;
    					var zNx     = 0;
    					var zNy     = 0;
    					var zWinPro = "width="+zX+",scrollbars=1,resizable=YES,height="+zY+",left="+zNx+",top="+zNy;
    					var zRuta = 'frrmfprn.php?cTipo='+document.forms['frgrm']['rTipo'][i].value
    											+'&cPtoId='+document.forms['frgrm']['cPtoId'].value
    											+'&cPtoDes='+document.forms['frgrm']['cPtoDes'].value
    											+'&dDesde='+document.forms['frgrm']['dDesde'].value
    											+'&dHasta='+document.forms['frgrm']['dHasta'].value;
    					var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
    					zWindow = window.open(zRuta,cNomVen,zWinPro);
    					zWindow.focus();
           	}
        }else{
        	alert(cMen);
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

	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="500">
				<tr>
					<td>
            <form name='frgrm' action='frrmfprn.php' method="POST">
              <center>
          	    <fieldset>
          		    <legend>Consulta Inventario de Formularios </legend>
          		    <table border="2" cellspacing="0" cellpadding="0" width="500">
          		     	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
          			     	<td class="name" width="30%"><center><h5><br>INVENTARIO DE FORMULARIOS</h5></center></td>
          			    </tr>
          			  </table>
          			  <table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
  							 		<?php $nCol = f_Format_Cols(25);
  							 		echo $nCol;?>
          			    <tr>
          	          <td class="name" colspan = "6"><br>Desplegar en:
          			      </td>
          			      <td class="name" colspan = "6"><br>
          	            <input type="radio" name="rTipo" value="1" checked onclick="fnHabilitarProBg(this.value)">Pantalla
          	          </td>
          	          <td class="name" colspan = "7"><br>
          			         <input type="radio" name="rTipo" value="2" onclick="fnHabilitarProBg(this.value)">Excel
          			      </td>
          			      <td class="name" colspan = "6"><br>
          			        <input type="radio" name="rTipo" value="3" onclick="fnHabilitarProBg(this.value)">Pdf<br>
          			      </td>
          	        </tr>
          	       
          	        <tr>
          	         <td class="name" colspan = "6"><br>Tipo de Producto:</td>
            	       <td Class = "name" colspan = "4">
  											<a href = "javascript:document.forms['frgrm']['cPtoId'].value  = '';
  																		  		  document.forms['frgrm']['cPtoDes'].value = '';
  																						f_Links('cPtoId','VALID')" id="id_href_cPtoId"><br>C&oacute;digo</a><br>
  											<input type = "text" Class = "letra" style = "width:80;text-align:left" name = "cPtoId"
  												onfocus="javascript:document.forms['frgrm']['cPtoId'].value  = '';
              						  									document.forms['frgrm']['cPtoDes'].value = '';
  													                  this.style.background='#00FFFF'"
  									    	onBlur = "javascript:this.value=this.value.toUpperCase();
  																		         f_Links('cPtoId','VALID');
  																		         this.style.background='#FFFFFF'">
  										</td>
  										
  										<td Class = "name" colspan = "15"><br>Descripci&oacute;n<br>
  											<input type = "text" Class = "letra" style = "width:300" name = "cPtoDes" readonly
  									    	onfocus="javascript:this.style.background='#00FFFF'"
  									    	onBlur = "javascript:this.value=this.value.toUpperCase();
  													                   this.style.background='#FFFFFF'">
  									</td>
          	       </tr>
          	      
          	       <tr>
          	         <td class="name" colspan = "5"><br>Rango De Fechas:</td>
          	         <td class="name" colspan = "3"><br><center><a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">De</a></center></td>
          	         <td class="name" colspan = "7"><br>
          	            <input type="text" name="dDesde" style = "width:140;text-align:center"
          	               onblur="javascript:chDate(this);">
          	         </td>
          	         <td class="name" colspan = "3"><br><center><a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">A</a></center></td>
          	         <td class="name" colspan = "7"><br>
          	            <input type="text" name="dHasta" style = "width:140;text-align:center"
          	              onblur="javascript:chDate(this);">
          	         </td>
          	       </tr>
          		    </table>
									<table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
										<?php $nCol = f_Format_Cols(24); echo $nCol;?>
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
										<?php 
										for ($i = 0; $i < count($mArcProBg); $i++) {
											$cColor = "{$vSysStr['system_row_impar_color_ini']}";
											if($i % 2 == 0) {
												$cColor = "{$vSysStr['system_row_par_color_ini']}";
											}
											?>
											<tr bgcolor = "<?php echo $cColor ?>">
												<?php
												if($mArcProBg[$i]['cPtoId'] != "" ){
													$qPtoDesc  = "SELECT ";
													$qPtoDesc .= "ptodesxx ";
													$qPtoDesc .= "FROM $cAlfa.fpar0132 ";
													$qPtoDesc .= "WHERE ";
													$qPtoDesc .= "ptoidxxx = \"{$mArcProBg[$i]['cPtoId']}\" ";
													$xPtoDesc  = f_MySql("SELECT", "", $qPtoDesc, $xConexion01, "");
													$vPtoDesc  = mysql_fetch_array($xPtoDesc);
												}
												?>
												<td style="padding:2px"><?php echo $mArcProBg[$i]['regunomx']; ?></td>
												<td style="padding:2px">
													<?php
													echo "<strong>Tipo de Producto: </strong>".($mArcProBg[$i]['cPtoId'] != "" ? $mArcProBg[$i]['cPtoId']." - ".$vPtoDesc['ptodesxx'] : "");
													echo "<br><strong>Rango de Fechas: </strong>".($mArcProBg[$i]['dDesde'] != "" ? ($mArcProBg[$i]['dDesde']." / ".$mArcProBg[$i]['dHasta']) : "");
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
										<?php 
										} ?>
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
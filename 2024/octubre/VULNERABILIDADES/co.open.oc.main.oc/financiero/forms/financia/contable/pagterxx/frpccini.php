<?php
  namespace openComex;
	 /**
	 * Imprime Reporte de Productividad por Cliente.
	 * --- Descripcion: Permite Imprimir Reporte de Productividad por Cliente.
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
  $qSysProbg .= "pbatinxx = \"CONCEPTOSDEPAGOSATERCEROS\" ";
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
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
				  case "cTerId":
					case "cTerNom":
					  if (xLink == "cTerId" || xLink == "cTerNom") {
					    var cTerTip = 'CLICLIXX';
					    var cTerId = document.forms['frgrm']['cTerId'].value.toUpperCase();
					    var cTerNom = document.forms['frgrm']['cTerNom'].value.toUpperCase();
					  }
						if (xSwitch == "VALID") {
							var cPathUrl = "frpcc150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gTerTip="+cTerTip+
																				"&gTerId="+cTerId+
																				"&gTerNom="+cTerNom;
							//alert(cPathUrl);
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-600)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "frpcc150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				 "&gTerTip="+cTerTip+
																				 "&gTerId="+cTerId+
																				 "&gTerNom="+cTerNom;
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
				  		cWindow.focus();
						}
					break;
					case "cDirId":
					case "cDirNom":
						if (xSwitch == "VALID") {
							var zRuta  = "frpcc003.php?gWhat=VALID&gFunction="+xLink+
							                          "&gDirId="+document.frgrm['cDirId'].value.toUpperCase()+
							                          "&gDirNom="+document.frgrm['cDirNom'].value.toUpperCase();
							parent.fmpro.location = zRuta;
						} else {
	  					var nNx     = (nX-600)/2;
							var nNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
							var zRuta   = "frpcc003.php?gWhat=WINDOW&gFunction="+xLink+
							                           "&gDirId="+document.frgrm['cDirId'].value.toUpperCase()+
							                           "&gDirNom="+document.frgrm['cDirNom'].value.toUpperCase();
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
			    break;
			    case "cCotId":
					case "cCotDes":
						if (xSwitch == "VALID") {
							var zRuta  = "frpcc119.php?gWhat=VALID&gFunction="+xLink+
							                          "&gCotId="+document.frgrm['cCotId'].value.toUpperCase()+
							                          "&gCotDes="+document.frgrm['cCotDes'].value.toUpperCase();
							parent.fmpro.location = zRuta;
						} else {
	  					var nNx     = (nX-600)/2;
							var nNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
							var zRuta   = "frpcc119.php?gWhat=WINDOW&gFunction="+xLink+
							                           "&gCotId="+document.frgrm['cCotId'].value.toUpperCase()+
							                           "&gCotDes="+document.frgrm['cCotDes'].value.toUpperCase();
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
			    break;
				}
	    }


			function f_GenSql()  {
			 
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
        			  
		    var indice = document.frgrm.cMes.selectedIndex;
		    var indper = document.frgrm.cPeriodo.selectedIndex;
        var zRuta = 'frpccprn.php?gPeriodo='+document.forms['frgrm']['cPeriodo'].options[indper].value
                    +'&gMes='+document.forms.frgrm['cMes'].options[indice].value
                    +'&gDirId='+document.forms.frgrm['cDirId'].value
                    +'&gTerId='+document.forms.frgrm['cTerId'].value
                    +'&gCotId='+document.forms.frgrm['cCotId'].value
                    +'&gPucId='+document.forms.frgrm['cPucId'].value
                    +'&cTipo='+document.forms['frgrm']['rTipo'][i].value
                    +'&cEjProBg='+document.forms.frgrm['cEjProBg'].value;
			  if(cTipo == 2){        			  	
			  	parent.fmpro.location = zRuta;
			  }else{
   				var zX      = screen.width;
  				var zY      = screen.height;
  				var zNx     = 0;  				
  				var zNy     = 0;
  				var zWinPro = "width="+zX+",scrollbars=1,resizable=YES,height="+zY+",left="+zNx+",top="+zNy;
  				var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
  				zWindow = window.open(zRuta,cNomVen,zWinPro);
  				zWindow.focus();
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
            <form name='frgrm' action='frpccprn.php' method="POST">
              <fieldset>
                <legend>Consulta Concepto de Pagos a Terceros</legend>
                <center>
                  <table border="2" cellspacing="0" cellpadding="0" width="500">
                    <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
                      <td class="name" width="30%"><center><h5><br>REPORTE DE CONCEPTO DE PAGOS A TERCEROS</h5></center></td>
                    </tr>
                  </table>
                  <table border = '0' cellpadding = '0' cellspacing = '0' width='300'>
                    <?php $nCol = f_Format_Cols(15);
                    echo $nCol;?>
                    <tr>
                      <td class="name" colspan = "05"><br>Desplegar en:</td>
                      <td class="name" colspan = "05"><br><input type="radio" name="rTipo" value="1" checked onclick="fnHabilitarProBg(this.value)">Pantalla</td>
                      <td class="name" colspan = "05"><br><input type="radio" name="rTipo" value="2" onclick="fnHabilitarProBg(this.value)">Excel</td>
                    </tr>
                  </table>
                  <table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
                    <?php $nCol = f_Format_Cols(24);
                    echo $nCol;?>
                    <tr>
                      <td class="name" colspan = "03"><br>Peri&oacute;do:</td>
                      <td class="name" colspan = "05"><br>
                        <select Class = "letrase" name="cPeriodo" style = "width:100">
                          <?php for($i=2010; $i<=$cAno; $i++){
                            if($i==$cAno){?>
                              <option value = '<?php echo $i?>' selected><?php echo $i ?></option>
                            <?php }else{?>
                              <option value = '<?php echo $i?>'><?php echo $i ?></option>
                            <?php }
                          }?>
                        </select>
                      </td>
                      <td class="name" colspan = "02" style="text-align:right"><br>Mes:&nbsp;&nbsp;</td>
                      <td class="name" colspan = "14"><br>
                        <select Class = "letrase" name = "cMes" style = "width:80">
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
                    <tr>
                      <td class="name" colspan = "03"><br>
                        <a href = "javascript:document.frgrm.cCotId.value='';
                                              document.frgrm.cCotDes.value='';
                                              f_Links('cCotId','VALID')" id="vDir">Concepto:</a>
                      </td>
                      <td class="name" colspan = "06"><br>
                        <input type="text" name="cCotId" style = "width:120"
                              onfocus="javascript:document.forms['frgrm']['cCotId'].value  = '';
                                                  document.forms['frgrm']['cCotDes'].value = '';
                                                  this.style.background='#00FFFF'"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                  f_Links('cCotId','VALID');
                                                  this.style.background='#FFFFFF'">
                      <input type="hidden" name="cPucId" style = "width:120">
                      </td>
                      <td class="name" colspan = "15"><br>
                        <input type="text" name="cCotDes" style = "width:300"
                              onfocus="javascript:document.forms['frgrm']['cCotId'].value  = '';
                                                  document.forms['frgrm']['cCotDes'].value = '';
                                                  this.style.background='#00FFFF'"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                  f_Links('cCotDes','VALID');
                                                  this.style.background='#FFFFFF'">
                      </td>
                    </tr>
                    <tr>
                      <td class="name" colspan = "03"><br>
                        <a href = "javascript:document.frgrm.cDirId.value='';
                                              document.frgrm.cDirNom.value='';
                                              f_Links('cDirId','VALID')" id="vDir">Director:</a>
                      </td>
                      <td class="name" colspan = "06"><br>
                        <input type="text" name="cDirId" style = "width:120"
                              onfocus="javascript:document.forms['frgrm']['cDirId'].value  = '';
                                                  document.forms['frgrm']['cDirNom'].value = '';
                                                  this.style.background='#00FFFF'"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                  f_Links('cDirId','VALID');
                                                  this.style.background='#FFFFFF'">
                      </td>
                      <td class="name" colspan = "15"><br>
                        <input type="text" name="cDirNom" style = "width:300"
                              onfocus="javascript:document.forms['frgrm']['cDirId'].value  = '';
                                                  document.forms['frgrm']['cDirNom'].value = '';
                                                  this.style.background='#00FFFF'"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                  f_Links('cDirNom','VALID');
                                                  this.style.background='#FFFFFF'">
                      </td>
                    </tr>
                    <tr>
                      <td class="name" colspan = "03"><br>
                        <a href = "javascript:document.forms['frgrm']['cTerId'].value  = '';
                                              document.forms['frgrm']['cTerDV'].value  = '';
                                              document.forms['frgrm']['cTerNom'].value = '';
                                              f_Links('cTerId','VALID')" id="id_href_cTerId">Cliente:</a>
                      </td>
                      <td class="name" colspan = "05"><br>
                        <input type="text" name="cTerId" style = "width:100"
                              onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
                                                  document.forms['frgrm']['cTerDV'].value  = '';
                                                  document.forms['frgrm']['cTerNom'].value = '';
                                                  this.style.background='#00FFFF'"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                  f_Links('cTerId','VALID');
                                                  this.style.background='#FFFFFF'">
                      </td>
                      <td Class = "name" colspan = "02"><br>
                        <input type = "text" style = "width:40;text-align:center" name = "cTerDV" readonly>
                      </td>
                      <td class="name" colspan = "14"><br>
                        <input type="text" name="cTerNom" style = "width:280"
                              onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
                                                  document.forms['frgrm']['cTerDV'].value  = '';
                                                  document.forms['frgrm']['cTerNom'].value = '';
                                                  this.style.background='#00FFFF'"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                  f_Links('cTerNom','VALID');
                                                  this.style.background='#FFFFFF'">
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
              <script language="javascript">
              document.forms['frgrm']['cMes'].value='<?php echo date('m') ?>';
              </script>
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

                        if($mArcProBg[$i]['gCotId'] != ""){
                          $qConCob  = "SELECT ";
                          $qConCob .= "IF(ctodesxx <> \"\",ctodesxx,IF(ctodesxp <> \"\",ctodesxp,IF(ctodesxg <> \"\",ctodesxg,IF(ctodesxr <> \"\",ctodesxr,IF(ctodesxl <> \"\",ctodesxl,IF(ctodesxf <> \"\",ctodesxf,IF(ctodesxm <> \"\",ctodesxm,\"CONCEPTO SIN DESCRIPCION\"))))))) AS ctodesxx ";
                          $qConCob .= "FROM $cAlfa.fpar0119 ";
                          $qConCob .= "WHERE ";
                          $qConCob .= "ctoidxxx = \"{$mArcProBg[$i]['gCotId']}\" AND ";
                          $qConCob .= "ctopccxx = \"SI\" AND ";
                          $qConCob .= "regestxx = \"ACTIVO\" ORDER BY ctodesxx";
                          $xConCob = f_MySql("SELECT","",$qConCob,$xConexion01,"");
                          $vConCob = mysql_fetch_array($xConCob);
                        }

                        if($mArcProBg[$i]['gDirId']!=""){
                          //Busco en la base de datos el nombre del director
                          $qUsrNom  = "SELECT USRNOMXX ";
                          $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
                          $qUsrNom .= "WHERE ";
                          $qUsrNom .= "USRIDXXX = \"{$mArcProBg[$i]['gDirId']}\" AND ";
                          $qUsrNom .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
                          $xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
                          $vUsrNom = mysql_fetch_array($xUsrNom);
                        }

                        if($mArcProBg[$i]['gTerId'] != ""){
                          $qCliNom  = "SELECT ";
                          $qCliNom .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
                          $qCliNom .= "FROM $cAlfa.SIAI0150 ";
                          $qCliNom .= "WHERE ";
                          $qCliNom .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mArcProBg[$i]['gTerId']}\" ";
                          $xCliNom  = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
                          $vCliNom  = mysql_fetch_array($xCliNom);
                        }

												echo "<strong>Periodo: </strong>{$mArcProBg[$i]['gPeriodo']}-{$mArcProBg[$i]['gMes']}";
												echo "<br><strong>Concepto: </strong>".($mArcProBg[$i]['gCotId'] != "" ? $mArcProBg[$i]['gCotId']." - ".$vConCob['ctodesxx'] : "");
												echo "<br><strong>Director: </strong>". ($mArcProBg[$i]['gDirId'] != "" ? $mArcProBg[$i]['gDirId']." - ".$vUsrNom['USRNOMXX'] : "");
												echo "<br><strong>Cliente: </strong>". ($mArcProBg[$i]['gTerId'] != "" ? $mArcProBg[$i]['gTerId']." - ".$vCliNom['CLINOMXX'] : "");
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
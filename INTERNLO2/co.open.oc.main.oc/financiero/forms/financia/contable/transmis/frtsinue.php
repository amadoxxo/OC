<?php
  /**
   * Generacion Archivo Excel Integracion Transmisión a Sistema SIESA.
   * --- Descripcion: Permite la Generacion Archivo Excel Integracion Transmisión a Sistema SIESA.
   * @author Elian Amado Ramirez <elian.amado@openits.co>
   * @package openComex
   * @version 001
   */
  include("../../../../libs/php/utility.php");

  /**
   *  Cookie fija
   */
  $kDf = explode("~", $_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb = $kDf[3];
  $kUser = $kDf[4];
  $kLicencia = $kDf[5];
  $swidth = $kDf[6];
  $kModId = $_COOKIE["kModId"];
  $kProId = $_COOKIE["kProId"];

  $today = date('Y-m-d');
  $yesterday = date('Y-m-d', strtotime($today . ' -1 day'));

  $qSysProbg = "SELECT * ";
  $qSysProbg .= "FROM $cBeta.sysprobg ";
  $qSysProbg .= "WHERE ";
  $qSysProbg .= "DATE(regdcrex) BETWEEN \"$today\" AND \"$today\" AND ";
  $qSysProbg .= "regusrxx = \"$kUser\" AND ";
  $qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
  $qSysProbg .= "pbamodxx = \"FACTURACION\" AND ";
  $qSysProbg .= "pbatinxx = \"TRANSMISIESA\" ";
  $qSysProbg .= "ORDER BY regdcrex DESC";
  $xSysProbg = f_MySql("SELECT", "", $qSysProbg, $xConexion01, "");

  $mArcProBg = array();

  while ($xRB = mysql_fetch_array($xSysProbg)) {
    $nInd_mArcProBg = count($mArcProBg);

    $vArchivos = explode("~", trim($xRB['pbaexcxx'], "~"));
    for ($nA = 0; $nA < count($vArchivos); $nA++) {
      $cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $vArchivos[$nA];
      if ($vArchivos[$nA] != "" && file_exists($cRuta)) {
        $mArcProBg[$nInd_mArcProBg]['pbaexcxx'] = $vArchivos[$nA];
      } else {
        $mArcProBg[$nInd_mArcProBg]['pbaexcxx'] = "";
      }
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

    $mPost = f_Explode_Array($xRB['pbapostx'], "|", "~");
    for ($nP = 0; $nP < count($mPost); $nP++) {
      if ($mPost[$nP][0] != "") {
        $mArcProBg[$nInd_mArcProBg][$mPost[$nP][0]] = $mPost[$nP][1];
      }
    }
  }
?>
<html>
	<head>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
    <script language = "javascript">

			function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
	    }

      function fnDate(fld){
				var val = fld.value;
				var ok = 1;
				if (val.length < 10) {
					alert('LA FECHA DEBE SER AAAA-MM-DD');
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
						alert('EL MES DEBE SER MENOR A 13');
						fld.value = '';
					}
					if (dia > 31){
						alert('EL DIA DEBE SER MENOR A 32');
						fld.value = '';
					}
					var aniobi = 28;
					if(anio % 4 ==  0){
						aniobi = 29;
					}
					if (mes == 4 || mes == 6 || mes == 9 || mes == 11){
						if (dia < 1 || dia > 30){
							alert('EL DIA DEBE SER MENOR A 31,DIA QUEDA EN 30');
							fld.value = val.substr(0,8)+'30';
						}
					}
					if (mes == 1 || mes == 3 || mes == 5 || mes == 7 || mes == 8 || mes == 10 || mes == 12){
						if (dia < 1 || dia > 32){
							alert('EL DIA DEBE SER MENOR A 32');
							fld.value = '';
						}
					}
					if(mes == 2 && aniobi == 28 && dia > 28 ){
						alert('EL DIA DEBE SER MENOR A 29');
						fld.value = '';
					}
					if(mes == 2 && aniobi == 29 && dia > 29){
						alert('EL DIA DEBE SER MENOR A 30');
						fld.value = '';
					}
				} else {
					if (val.length > 0){
						alert('FECHA ERRONEA,VERIFIQUE');
					}
					fld.value = '';
				}
			}

      function f_Links(xLink,xSwitch,xSecuencia,xGrid,xType) {
    		var nX = screen.width;
    		var nY = screen.height;
    		switch (xLink) {
    		  case "cComCod":
    			case "cComDes":
    				if (document.forms['frgrm']['cComId'].value != ""){
    				  if (xLink == "cComCod" || xLink == "cComDes") {
    				    var cComId    = document.forms['frgrm']['cComId'].value.toUpperCase();
    				    var cComCod   = document.forms['frgrm']['cComCod'].value.toUpperCase();
    				    var cComDes   = document.forms['frgrm']['cComDes'].value.toUpperCase();
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
    				} else {
    					alert("Debe Seleccionar una Interfaz.");
    				}
    			break;
    		}
    	}

      function fnEnvia(){
				var dDesde = document.forms['frgrm']['dDesde'].value;
				var dHasta = document.forms['frgrm']['dHasta'].value;
				var indusr = document.frgrm.cUsrId.selectedIndex;

				if (dDesde.length == 10 && dHasta.length == 10){
					var ini   = dDesde.replace('-','');
					var fin   = dHasta.replace('-','');
					var fsi   = '<?php echo date('Y-m-d') ?>';
					var fsis  = fsi.replace('-','');
					var fsis1 = fsis.replace('-','');
					var ini2  = ini.replace('-','');
					var fin2  = fin.replace('-','');

					inii = 1 * ini2;
					fini = 1 * fin2;
					var fsi2 = 1 * fsis1;

					if(fini > fsi2){
            alert('Fecha Final no puede ser mayor a la Fecha de Hoy,verifique');
          }else{
  					if (fini < inii){
  						alert('Fecha Final es Menor a Inicial,verifique');
  					} else {
  						var zX      = screen.width;
  						var zY      = screen.height;
  						var zNx     = (zX-500)/2;
  						var zNy     = (zY-250)/2;
  						var zWinPro  = 'width=500,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var cRuta    = 'frtsigra.php?dDesde='    +dDesde+'&dHasta='+dHasta+
                                          '&gComId='   +document.forms['frgrm']['cComId'].value+
                                          '&gComCod='  +document.forms['frgrm']['cComCod'].value+
                                          '&gUsrId='   +document.forms['frgrm']['cUsrId'].options[indusr].value+
                                          '&cEjProBg=SI';

              parent.fmpro.location = cRuta;
  					}
				  }
				} else {
					alert('Verifique Fecha Desde, Fecha Hasta ');
				}
			}

      function fnDescargar(xArchivo){
        parent.fmpro.location = "frgendoc.php?cRuta="+xArchivo;
      }

			function fnRecargar() {
				parent.fmwork.location="<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>";
			}
    </script>
  </head>
  <body>
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="560">
        <tr>
          <td>
            <fieldset>
            <legend>Archivo Excel para Transmisi&oacute;n a Sistema SIESA</legend>
              <form name = "frgrm" action = "frtsigra.php" method = "post" target="fmwork">
                <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                  <center>
                    <table cellspacing="0" width="100%">
                      <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                        <td class="name" width="100%"><br><center><u>La Fecha de hoy es: <?php echo date('Y-m-d') ?></u><br><br></center>
                      </tr>
                    </table>
                  </center>
                  <br>
                  <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:560">
                    <?php $zCol = f_Format_Cols(28); echo $zCol; ?>
                    <tr>
                      <td Class = "name" colspan = "8"><a href='javascript:show_calendar("frgrm.dDesde")'>Fecha Desde</a><br>
                        <input type = 'text' name = 'dDesde' value = '<?php echo date('Y-m-d') ?>' maxlength=10 Class = 'letra' style = 'width:160;text-align:center' onBlur = 'javascript:fnDate(this)'>
                      </td>
                      <td Class = "name" colspan = "8"><a href='javascript:show_calendar("frgrm.dHasta")'>Fecha Hasta</a><br>
                        <input type = 'text' name = 'dHasta' value = '<?php echo date('Y-m-d') ?>' maxlength=10 Class = 'letra' style = 'width:160;text-align:center' onBlur = 'javascript:fnDate(this)'>
                      </td>
                      <td Class = "name" colspan = "12">Id del Comprobante<br>
                        <select class="letrase" size="1" name="cComId" style = "width:240"
                          onchange="javascript: document.forms['frgrm']['cComCod'].value = '';
                                                document.forms['frgrm']['cComDes'].value = '';">
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
                    <td class="name" width="12" align="left">Usuarios<br>
                      <select Class = "letrase" name = "cUsrId" value = "<?php echo $_COOKIE['kUsrId'] ?>" style = "width:240" >
                        <option value = "" selected>-- TODOS --</option>
                        <?php
                          $qUsrData  = "SELECT * ";
                          $qUsrData .= "FROM $cAlfa.SIAI0003 ";
                          $qUsrData .= "WHERE ";
                          $qUsrData .= "$cAlfa.SIAI0003.USRDOCXX != \"\" AND ";
                          $qUsrData .= "$cAlfa.SIAI0003.REGESTXX = \"ACTIVO\" ";
                          $qUsrData .= "ORDER BY $cAlfa.SIAI0003.USRNOMXX ";
                          $xUsrData = f_MySql("SELECT","",$qUsrData,$xConexion01,"");
                          //f_Mensaje(__FILE__,__LINE__,$qUsrData." ~ ".mysql_num_rows($xUsrData));
                          $mUsuario = array();
                          if (mysql_num_rows($xUsrData) > 0) {
                            while ($xRUD = mysql_fetch_array($xUsrData)) {
                              $nInd_mUsuario = count($mUsuario);
                              $mUsuario[$nInd_mUsuario]['usridxxx'] = $xRUD['USRIDXXX'];
                              $mUsuario[$nInd_mUsuario]['usrnomxx'] = $xRUD['USRNOMXX'];
                            }
                          }
                          for($i=0;$i<count($mUsuario);$i++){
                            if ($mUsuario[$i]['usridxxx'] == $_COOKIE['kUsrId']) { ?>
                              <option value = "<?php echo $mUsuario[$i]['usridxxx']?>" selected><?php echo $mUsuario[$i]['usrnomxx'] ?></option>
                              <?php } else { ?>
                                <option value = "<?php echo $mUsuario[$i]['usridxxx']?>"><?php echo $mUsuario[$i]['usrnomxx'] ?></option>
                              <?php
                            }
                          }
                        ?>
                      </select>
                      </td>
                  </tr>
                  <tr id="EjProBg">
                    <td Class = "name" colspan = "28"><br>
                      <label><input type="checkbox" name="cEjProBg" value ="SI" onclick="javascript:this.checked = true" checked>Ejecutar Proceso en Background</label>
                    </td>
                  </tr>
                  </table>
              </form>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
    <center>
			<table border="0" cellpadding="0" cellspacing="0" width="560">
				<tr height="21">
					<td width="378" height="21"></td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:fnEnvia()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
				</tr>
			</table>
		</center>
		<?php if(count($mArcProBg) > 0){ ?>
      <center>
        <table border="0" cellpadding="0" cellspacing="0" width="560">
          <tr>
            <td Class = "name" colspan = "19"><br>
              <fieldset>
                <legend>Reportes Generados. Fecha [<?php echo date('Y-m-d'); ?>]</legend>
                <label>
                  <table border="0" cellspacing="1" cellpadding="0" width="560">
                    <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="height:20px">
                      <td align="center" style="padding:2px"><strong>Usuario</strong></td>
                      <td align="center" style="padding:2px"><strong>Par&aacute;metros</strong></td>
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
                      if ($mArcProBg[$i]['gUsrId'] != "") {
                        $qUsrDat  = "SELECT USRNOMXX ";
                        $qUsrDat .= "FROM $cAlfa.SIAI0003 ";
                        $qUsrDat .= "WHERE ";
                        $qUsrDat .= "$cAlfa.SIAI0003.USRIDXXX = \"{$mArcProBg[$i]['gUsrId']}\" LIMIT 0,1 ";
                        $xUsrDat = f_MySql("SELECT","",$qUsrDat,$xConexion01,"");
                        $vUsrDat = mysql_fetch_array($xUsrDat);
                      }
                      ?>
                    <tr bgcolor = "<?php echo $cColor ?>">
                      <td style="padding:2px"><?php echo $mArcProBg[$i]['regunomx']; ?></td>
                      <td style="padding:2px">
                        <strong>Rango Fechas: </strong><?php echo $mArcProBg[$i]['dDesde']. " / ".$mArcProBg[$i]['dHasta']; ?><br>

                        <?php if ($mArcProBg[$i]['gComId'] != "") { ?>
                          <strong>Comprobante: </strong><?php echo $mArcProBg[$i]['gComId'] . (($mArcProBg[$i]['gComCod'] != "") ? "-".$mArcProBg[$i]['gComCod'] : "") ?>
                        <?php } ?>

                        <?php if ($mArcProBg[$i]['gUsrId'] != "") { ?>
                          <strong>Usuario: </strong><?php echo "[".$mArcProBg[$i]['gUsrId']."] ".$vUsrDat['USRNOMXX'] ?>
                        <?php } ?>
                      </td>
                      <td style="padding:2px"><?php echo $mArcProBg[$i]['pbarespr']; ?></td>
                      <td style="padding:2px"><?php echo $mArcProBg[$i]['regestxx']; ?></td>
                      <td style="padding:2px">
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
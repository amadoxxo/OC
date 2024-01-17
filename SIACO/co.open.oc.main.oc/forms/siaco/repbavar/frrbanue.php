<?php
  /**
   * Formulario para generar el Reporte Bavaria.
   * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package openComex
   * @version 001
   */
	include("../../../libs/php/utility.php");

	if($cPerAno == ""){
	  $cPerAno = date('Y');
	}
	
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
  $kModId     = $_COOKIE["kModId"];
  $kProId     = $_COOKIE["kProId"];

  $dHoy = date('Y-m-d');

  $qSysProbg = "SELECT * ";
  $qSysProbg .= "FROM $cBeta.sysprobg ";
  $qSysProbg .= "WHERE ";
  $qSysProbg .= "DATE(regdcrex) =\"$dHoy\" AND ";
  $qSysProbg .= "regusrxx = \"$kUser\" AND ";
  $qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
  $qSysProbg .= "pbamodxx = \"FACTURACION\" AND ";
  $qSysProbg .= "pbatinxx = \"REPORTEPROFORMASBAVARIA\" ";
  $qSysProbg .= "ORDER BY regdcrex DESC";
  $xSysProbg = f_MySql("SELECT", "", $qSysProbg, $xConexion01, "");
  // f_Mensaje(__FILE__,__LINE__,  $qSysProbg." ~ ".mysql_num_rows($xSysProbg));

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
    <title>Reporte Proformas Bavaria</title>
    <head>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css'>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css'>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css'>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css'>
      <script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/utility.js'></script>
      <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/date_picker3.js'></script>
      <script language = 'javascript'>
        function fnRetorna(){ // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
          parent.parent.fmwork.location='<?php echo $cPlesk_Forms_Directory_New ?>/frproces.php';
          parent.parent.fmnav.location='<?php echo $cPlesk_Forms_Directory_New ?>/nivel2.php';
        }

        function fnRecargar() {
          parent.fmwork.location="<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>";
        }

        function fnLinks(xLink,xSwitch,xIteration) {
          var nX = screen.width;
          var nY = screen.height;
          switch(xLink){
            case "cDexId":
              if (document.forms['frnav']['cDexId'].value == "") {
                return;
              }
             
              if (xSwitch == "VALID") {
                var zRuta = "frrba121.php?gWhat="+xSwitch+"&gFunction="+xLink+
                                        "&gDexId="+document.forms['frnav']['cDexId'].value.toUpperCase() +
                                        "&gTerId="+document.forms['frnav']['cTerId'].value.toUpperCase();
                parent.fmpro.location = zRuta;
              } else {
                if (xSwitch == "WINDOW"){
                  var nNx      = (nX-600)/2;
                  var nNy      = (nY-550)/2;
                  var zWinPro  = "width=600,scrollbars=1,height=550,left="+nNx+",top="+nNy;
                  var zRuta = "frrba121.php?gWhat="+xSwitch+"&gFunction="+xLink+
                                                  "&gDexId="+document.forms['frnav']['cDexId'].value.toUpperCase() +
                                                  "&gTerId="+document.forms['frnav']['cTerId'].value.toUpperCase();
                  zWindow = window.open(zRuta,xLink,zWinPro);
                  zWindow.focus();
                }else{
                  if (xSwitch == "EXACT"){
                    var zRuta  = "frrba121.php?gWhat=EXACT&gFunction=" + xLink +
                                            "&gDexId="+document.frnav[xLink].value.toUpperCase() +
                                            "&gSucId="+document.frnav['cSucId'].value.toUpperCase() +
                                            "&gTerId="+document.frnav['cTerId'].value.toUpperCase();
                    parent.fmpro.location = zRuta;
                  }
                }
              }
            break;
            case "cTerId":
              var cTerId = document.forms['frnav'][xLink].value.toUpperCase();

              if (xSwitch == "VALID") {
                if (cTerId == "") {
                  return;
                }
                if (cTerId.length < 2) {
                  alert('Debe Indicar al Menos Dos Caracteres para la Busqueda.');
                }else{
                  var zRuta = "frrba150.php?gWhat="+xSwitch+
                                          "&gFunction="+xLink+
                                          "&gTerId="+cTerId;
                  parent.fmpro.location = zRuta;
                }
              } else {
                if (xSwitch == "WINDOW"){
                  var nNx      = (nX-600)/2;
                  var nNy      = (nY-550)/2;
                  var zWinPro  = "width=600,scrollbars=1,height=550,left="+nNx+",top="+nNy;
                  var zRuta = "frrba150.php?gWhat="+xSwitch+
                                          "&gFunction="+xLink+
                                          "&gTerId="+cTerId;
                  zWindow = window.open(zRuta,xLink,zWinPro);
                  zWindow.focus();
                }else{
                  if (xSwitch == "EXACT"){
                    var zRuta  = "frrba150.php?gWhat=EXACT&gFunction="+xLink+
                                              "&gTerId="+cTerId;
                    parent.fmpro.location = zRuta;
                  }
                }
              }
            break;
						case "cComCsc":
							if (xSwitch == "VALID") {
								
								var zRuta   = "frfcoc00.php?gWhat=VALID&gFunction=cComCsc"+
                                          "&cComCsc="+document.forms['frnav']['cComCsc'].value+
                                          "&gTerId="+document.forms['frnav']['cTerId'].value+
                                          "&cPerAno="+document.forms['frnav']['cPerAno'].value+"";
								parent.fmpro.location = zRuta;

							} else {
								var zNx     = (nX-400)/2;
								var zNy     = (nY-250)/2;
								var zWinPro = "width=400,scrollbars=1,height=250,left="+zNx+",top="+zNy;
								var zRuta   = "frfcoc00.php?gWhat=WINDOW&gFunction=cComCsc"+
                                          "&cComCsc="+document.forms['frnav']['cComCsc'].value+
                                          "&gTerId="+document.forms['frnav']['cTerId'].value+
                                          "&cPerAno="+document.forms['frnav']['cPerAno'].value+"";
								zWindow = window.open(zRuta,"zWindow",zWinPro);
								zWindow.focus();
							}
						break;
          }
        }

        function chDate(fld){
          var reg = /[.+"'*?^${}()|[\]\\a-zA-Z\u00C0-\u017F]+/gi
          var val = fld.value;
          if(val.length > 0){
            var ok = 1;
            if(reg.test(val)){
              alert('Formato de Fecha debe ser aaaa-mm-dd');
              fld.value = '0000-00-00';
              fld.focus();
              ok = 0;
            }
            if(ok == 1 && (val.length < 10 || val.length > 10)){
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

              if ((val != "0000-00-00") && (anio < 1999)) {
                alert('El Anio debe ser mayor a 1999');
                fld.value = '0000-00-00';
                fld.focus();
              }
              if ((val != "0000-00-00") && (mes < 1 || mes > 12)) {
                alert('El mes debe ser mayor a 0 o menor a 13');
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
              if ((val != "0000-00-00") && (mes == 2 && dia < 1)) {
                alert('El dia debe ser mayor a 0');
                fld.value = '0000-00-00';
                fld.focus();
              }
            }else{
              if(val.length > 0){
                alert('Fecha erronea, verifique');
              }
              fld.value = '0000-00-00';
              fld.focus();
            }
          }else{
            fld.value = '0000-00-00';
            fld.focus();
          }
        }

        function fnGenerar(){
          var nSwitch = 0;
          var cMsj = "";

          //Validando que se haya escogido un rango de fechas
          if (document.forms['frnav']['dDesde'].value == '0000-00-00' || document.forms['frnav']['dHasta'].value == '0000-00-00') {
            nSwitch = 1;
            cMsj += "Debe Seleccionar un Rango de Fechas.\n";
          } else {
            //Se compara que los anios seleccionados sean los mismos
            var mFecIni = document.forms['frnav']['dDesde'].value.split("-");
            var mFecFin = document.forms['frnav']['dHasta'].value.split("-");
            if (mFecIni[0] != mFecFin[0]) {
              nSwitch = 1;
              cMsj += "El Rango de Fechas debe ser del Mismo Anio.\n";
            }
          }

          if (document.forms['frnav']['cTerId'].value == "") {
            nSwitch = 1;
            cMsj += "Debe Seleccionar el Filtro de Cliente.\n";
          }

          if (nSwitch == 0) {
            var cPathUrl = "frrbaprn.php?" +
                      '&gTerId='   +document.forms['frnav']['cTerId'].value	+
                      '&gTerNom='  +document.forms['frnav']['cTerNom'].value	+
                      '&gSucId='   +document.forms['frnav']['cSucId'].value	+
                      '&gDexId='   +document.forms['frnav']['cDexId'].value	+
                      '&gPerAno='  +document.forms['frnav']['cPerAno'].value +
                      '&gComId='   +document.forms['frnav']['cComId'].value  +
                      '&gComCod='  +document.forms['frnav']['cComCod'].value +
                      '&gComCsc='  +document.forms['frnav']['cComCsc'].value +
                      '&gComCsc2=' +document.forms['frnav']['cComCsc2'].value+
                      '&gDesde='   +document.forms['frnav']['dDesde'].value	+
                      '&gHasta='	 +document.forms['frnav']['dHasta'].value	+
                      '&cEjProBg=SI';

            document.forms['frnav'].target = 'fmpro';
            document.forms['frnav'].action = cPathUrl;
            document.forms['frnav'].submit();
            // parent.fmpro.location = cPathUrl;
          } else {
            alert(cMsj + "Verifique.");
          }
        }

        function fnDescargar(xArchivo){
          parent.fmwork.location = "frgendoc.php?cRuta="+xArchivo;
        }
      </script>
    </head>
    <body topmargin=0 leftmargin=0  marginwidth=0 marginheight=0 style = 'margin-right : 0'>
      <form name = 'frnav' action="frrbaprn.php" method = "post" target="fmpro">
        <center>
          <table border = "0" cellspacing="0" cellpadding="0"  width = "520">
            <tr>
              <td>
                <fieldset><legend>Reporte Proformas Bavaria</legend>
                <center> 
                  <table border="2" cellspacing="0" cellpadding="0" width="520">
                    <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
                      <td class="name" width="30%"><center><h4><br>Reporte Proformas Bavaria</h4></center></td>
                    </tr>
                  </table>
                  <table border = "0" cellspacing="0" cellpadding="0" width = "520">
                    <?php echo columnas(21,20); ?>
                    <tr>
                      <td Class = 'name' colspan = '6'><br><a href = "javascript:fnLinks('cTerId','WINDOW',-1)" title="Buscar Cliente">Cliente</a></td>
                      <td Class = 'name' colspan = '5'><br>
                        <input type = "text" Class = "letra" style = "width:100;" name = "cTerId"
                            onfocus="javascript:document.forms['frnav']['cTerDV'].value  = '';
                                                document.forms['frnav']['cTerId'].value  = '';
                                                document.forms['frnav']['cTerNom'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cTerId','VALID');
                                                this.style.background='#FFFFFF'">
                      </td> 
                      <td Class = 'name' colspan ='1'><br>
                        <input type = "text" Class = "letra" name = "cTerDV" style = "width:40;text-align:center" readonly>
                      </td>
                      <td Class = 'name' colspan = '13'><br>
                        <input type = "text" Class = "letra" name = "cTerNom" style = "width:260;text-align:left" readonly>
                      </td>
                    </tr>

                    <tr>
                      <td Class = 'name' colspan = '6'><br>Dex/Otros:</td>
                      <td Class = 'name' colspan = '4'><br>
                        <input type = "text" Class = "letra" name = "cSucId" style = "width:80;text-align:center" readonly>
                      </td> 
                      <td Class = 'name' colspan ='16'><br>
                        <input type = "text" Class = "letra" style = "width:320;" name = "cDexId"
                            onfocus="javascript:document.forms['frnav']['cSucId'].value = '';
                                                document.forms['frnav']['cDexId'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cDexId','VALID');
                                                this.style.background='#FFFFFF'">
                      </td>
                    </tr>

                    <br><br>
                    <tr>
                      <td Class = 'name' colspan = '6'><br>Factura:</td>
                      <td Class = 'name' colspan = '4'><br>
                        <select name = "cPerAno" style="width:80;height:20">
            	        	  <?php for($i=$vSysStr['financiero_ano_instalacion_modulo'];$i<=date('Y');$i++){ ?>
            	        	    <option value="<?php echo $i ?>"><?php echo $i ?></option>
            	        	  <?php  } ?>            	        	  
            	        	</select>
            	        	 <script language="javascript">
                					document.forms['frnav']['cPerAno'].value = "<?php  echo $cPerAno ?>";
               					 </script> 
                      </td>
                      <td Class = 'clase08' colspan = '2'>ID<br>
                        <input type = 'text' Class = 'letra' style = 'width:60' name = 'cComId' readonly>
                      </td>
                      <td Class = 'clase08' colspan = '3'>COD<br>
                        <input type = 'text' Class = 'letra' style = 'width:60' name = 'cComCod' readonly>
                      </td>
                      <td Class = "clase08" colspan = "10">FACTURA<br>
                        <input type = 'text' Class = 'letra' style = 'width:200' name = 'cComCsc' maxlength="10"
                            onBlur = "javascript:f_FixFloat(this);
                                                if(document.forms['frnav']['cComCsc'].value.length > 1){
                                                  fnLinks('cComCsc','VALID');
                                                  this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'
                                                }else{
                                                  alert('Debe Digitar al Menos Dos Digitos de la Factura');
                                                }"
                          	onFocus="javascript:document.forms['frnav']['cComId'].value  = '';
                                                document.forms['frnav']['cComCod'].value = '';
                                                document.forms['frnav']['cComCsc'].value = '';
                                                document.forms['frnav']['cComCsc2'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        <input type="hidden" name ="cComCsc2">
                      </td> 
                    </tr>

                    <tr>
                      <td class="name" colspan = "6"><br>Rango de Fechas:</td>
                      <td class="name" colspan = "2"><br>
                        <a href='javascript:show_calendar("frnav.dDesde")' id="id_dDesde">Del:</a>
                      </td>
                      <td class="name" colspan = "6"><br>
                        <input type="text" name="dDesde" style = "width:120;text-align:center" onblur="javascript:chDate(this);" value="<?php echo "0000-00-00" ?>">
                      </td>
                      <td class="name" colspan = "3"><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:show_calendar("frnav.dHasta")' id="id_dHasta">Al:</a>
                      </td>
                      <td class="name" colspan = "6" align="right"><br>
                        <input type="text" name="dHasta" style = "width:120;text-align:center" onblur="javascript:chDate(this);" value="<?php echo "0000-00-00" ?>">
                      </td>
                    </tr>
                    <tr id="EjProBg">
                      <td Class = "name" colspan = "27"><br>
                        <label><input type="checkbox" name="cEjProBg" value ="SI" onclick="javascript:this.checked = true" checked>Ejecutar Proceso en Background</label>
                      </td>
                    </tr>
                  </table><br> 
                  </center>
                </fieldset>
              </td>
            </tr>
          </table>
        </center>
      </form>
      <center>
      <table border="0" cellpadding="0" cellspacing="0" width="540">
        <tr height="21">
          <td width="358" height="21"></td>
          <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:fnGenerar()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
          <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
        </tr>
      </table>
      </center>

      <?php
      if(count($mArcProBg) > 0){ ?>
      <center>
        <table border="0" cellpadding="0" cellspacing="0" width="520">
          <tr>
            <td Class = "name" colspan = "21"><br>
              <fieldset>
                <legend>Reportes Generados. Fecha [<?php echo date('Y-m-d'); ?>]</legend>
                <label>
                  <table border="0" cellspacing="1" cellpadding="0" width="520">
                    <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="height:20px">
                      <td align="center"><strong>Usuario</strong></td>
                      <td align="center"><strong>Nit Cliente</strong></td>
                      <td align="center"><strong>Rango Fechas</strong></td>
                      <td align="center"><strong>Resultado</strong></td>
                      <td align="center"><strong>Estado</strong></td>
                      <td align="center"><img src = "<?php echo $cPlesk_Skin_Directory ?>/b_cambest.gif" onClick = "javascript:fnRecargar()" style = "cursor:pointer" title="Recargar"></td>
                    </tr>
                    <?php for ($i = 0; $i < count($mArcProBg); $i++) {
                      $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                      if($i % 2 == 0) {
                        $cColor = "{$vSysStr['system_row_par_color_ini']}";
                      } ?>
                      <tr bgcolor = "<?php echo $cColor ?>">
                        <td style="padding:2px"><?php echo $mArcProBg[$i]['regunomx']; ?></td>
                        
                        <td> 
                          <?php 
                          if($mArcProBg[$i]['gTerId'] != ""){?>
                              <?php echo $mArcProBg[$i]['gTerId']; ?>
                            <?php
                          }
                          ?>
                        </td>
                        <td>
                          <?php 
                          if($mArcProBg[$i]['gDesde'] != ""){
                            echo "Del " . $mArcProBg[$i]['gDesde'] . " Al " . $mArcProBg[$i]['gHasta'];
                          }
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
      <?php } ?> 
    </body>
  </html>
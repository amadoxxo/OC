<?php
  /**
	 * Filtros Reporte de Tarifas Consolidado.
	 * --- Descripcion: Permite seleccionar los filtros para generar el Reporte de Tarifas Consolidado
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @version 001
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
  $qSysProbg .= "pbatinxx = \"REPTARIFASCON\" ";
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
  <head>
    <title>Reportes</title>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script languaje = 'javascript'>
      /**
       * Permite retornar al tracking.
       */
      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      }

      /**
       * Permite cargar el valid/window dependiendo del campo seleccionado.
       */
      function f_Links(xLink,xSwitch) {
        var zX    = screen.width;
        var zY    = screen.height;
        fnHabilitaDeshabilitaFechas();

        switch (xLink) {
          case "cCliId":
            if(document.forms['frgrm']['cTarTip'].value == "CLIENTE") {
              var cRuta = "frctr350.php";
            } else {
              var cRuta = "frctr111.php";
            }
            
            if (xSwitch == "VALID") {
              var zRuta  = cRuta+"?gWhat=VALID"+
                                 "&gFunction=cCliId"+
                                 "&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase()+
                                 "&gOrigen=REPORTE";

              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = cRuta+"?gWhat=WINDOW"+
                                  "&gFunction=cCliId"+
                                  "&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase()+
                                  "&gOrigen=REPORTE";

              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cCliNom":
            if(document.forms['frgrm']['cTarTip'].value == "CLIENTE") {
              var cRuta = "frctr35n.php";
            } else {
              var cRuta = "frctr111.php";
            }
                  
            if (xSwitch == "VALID") {
              var zRuta  = cRuta+"?gWhat=VALID"+
                                 "&gFunction=cCliNom"+
                                 "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase()+
                                 "&gOrigen=REPORTE";

              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = cRuta+"?gWhat=WINDOW"+
                                   "&gFunction=cCliNom"+
                                   "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase()+
                                   "&gOrigen=REPORTE";

              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cSerId":
            if (xSwitch == "VALID") {
              var cRuta  = "frctr129.php?gWhat=VALID"+
                                        "&gFunction=cSerId"+
                                        "&gSerId="+document.forms['frgrm']['cSerId'].value.toUpperCase()+
                                        "&gTipo=REPORTE";
              parent.fmpro.location = cRuta;
            } else {
              var zNx     = (zX-750)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = "width=750,scrollbars=1,height=250,left="+zNx+",top="+zNy;
              var cRuta   = "frctr129.php?gWhat=WINDOW"+
                                        "&gFunction=cSerId"+
                                        "&gSerId="+document.forms['frgrm']['cSerId'].value.toUpperCase()+
                                        "&gTipo=REPORTE";
              var zWindow = window.open(cRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cSerDes":
            if (xSwitch == "VALID") {
              var cRuta  = "frctr129.php?gWhat=VALID"+
                                        "&gFunction=cSerDes"+
                                        "&gSerDes="+document.forms['frgrm']['cSerDes'].value.toUpperCase()+
                                        "&gTipo=REPORTE";
              parent.fmpro.location = cRuta;
            } else {
              var zNx     = (zX-750)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = "width=750,scrollbars=1,height=250,left="+zNx+",top="+zNy;
              var cRuta   = "frctr129.php?gWhat=WINDOW"+
                                        "&gFunction=cSerDes"+
                                        "&gSerDes="+document.forms['frgrm']['cSerDes'].value.toUpperCase()+
                                        "&gTipo=REPORTE";
              var zWindow = window.open(cRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cFcoId":
            if (xSwitch == "VALID") {
              var cRuta  = "frctr130.php?gWhat=VALID&gFunction=cFcoId"+
                                        "&gFcoId="+document.forms['frgrm']['cFcoId'].value.toUpperCase()+
                                        "&gFcoIds="+document.forms['frgrm']['cFcoIds'].value.toUpperCase()+
                                        "&gTipo=REPORTE";
              parent.fmpro.location = cRuta;
            } else {
              var zNx     = (zX-500)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = "width=500,scrollbars=1,height=250,left="+zNx+",top="+zNy;
              var cRuta   = "frctr130.php?gWhat=WINDOW&gFunction=cFcoId"+
                                        "&gFcoId="+document.forms['frgrm']['cFcoId'].value.toUpperCase()+
                                        "&gFcoIds="+document.forms['frgrm']['cFcoIds'].value.toUpperCase()+
                                        "&gTipo=REPORTE";
              var zWindow = window.open(cRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cFcoDes":
            if (xSwitch == "VALID") {
              var cRuta  = "frctr130.php?gWhat=VALID&gFunction=cFcoDes"+
                                        "&gFcoDes="+document.forms['frgrm']['cFcoDes'].value.toUpperCase()+
                                        "&gTipo=REPORTE";
              parent.fmpro.location = cRuta;
            } else {
              var zNx     = (zX-500)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = "width=500,scrollbars=1,height=250,left="+zNx+",top="+zNy;
              var cRuta   = "frctr130.php?gWhat=WINDOW&gFunction=cFcoDes"+
                                        "&gFcoDes="+document.forms['frgrm']['cFcoDes'].value.toUpperCase()+
                                        "&gTipo=REPORTE";
              var zWindow = window.open(cRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
        }
      }

      /**
       * Permite cambiar los titulos de Cliente o Grupo.
       */
      function fnCambiarTitulos(xOpcion) {
        document.forms['frgrm']['cCliId'].value  = "";
        document.forms['frgrm']['cCliDV'].value  = "";
        document.forms['frgrm']['cCliNom'].value = "";
            
        if (xOpcion == "CLIENTE") {
          document.getElementById('lblCliId').innerHTML  = "Nit";
          document.getElementById('lblCliDv').innerHTML  = "Dv";
          document.getElementById('lblCliNom').innerHTML = "Cliente";
          document.getElementById('lblEstado').innerHTML = "Estado Cliente:";
        } else {
          document.getElementById('lblCliId').innerHTML  = "Id";
          document.getElementById('lblCliDv').innerHTML  = "";
          document.getElementById('lblCliNom').innerHTML = "Grupo de Tarifas";
          document.getElementById('lblEstado').innerHTML = "Estado Grupo:";
        }            
      }
      
      /**
       * Genera la descarga del reporte mediante un agendamiento en Background.
       */
      function fnGenerar() {
        if (document.forms['frgrm']['dDesde'].value != "" && document.forms['frgrm']['dHasta'].value != "") {
          var cRuta = "frctrrcg.php?gEstTari="  +document.forms['frgrm']['cEstTari'].value+
                                  "&gTipoOpe="  +document.forms['frgrm']['cTipoOpe'].value+
                                  "&gApliTar="  +document.forms['frgrm']['cTarTip'].value+
                                  "&gCliId="    +document.forms['frgrm']['cCliId'].value+
                                  "&gEstCli="   +document.forms['frgrm']['cEstCli'].value+
                                  "&gSerId="    +document.forms['frgrm']['cSerId'].value+
                                  "&gFcoId="    +document.forms['frgrm']['cFcoId'].value+
                                  "&gTipoFec="  +document.forms['frgrm']['cTipoFec'].value+
                                  "&gDesde="    +document.forms['frgrm']['dDesde'].value+
                                  "&gHasta="    +document.forms['frgrm']['dHasta'].value+
                                  "&cEjProBg="  +document.forms['frgrm']['cEjProBg'].value;

          document.forms['frgrm'].target = 'fmpro';
          document.forms['frgrm'].action = cRuta;
          document.forms['frgrm'].submit();
        } else {
          alert("Debe Seleccionar el Rango de Fechas,\nVerifique.")
        }
      }

      /**
       * Habilita los campos de rango de fechas cuando se selecciona un Cliente o Grupo.
       */
      function fnHabilitaDeshabilitaFechas() {
        document.getElementById('id_href_dDesde').setAttribute('href', "javascript:show_calendar(\"frgrm.dDesde\")");
        document.getElementById('id_href_dHasta').setAttribute('href', "javascript:show_calendar(\"frgrm.dHasta\")");
        document.forms['frgrm']['dDesde'].readOnly = false;
        document.forms['frgrm']['dHasta'].readOnly = false;
      }

      /**
       * Valida las fechas seleccionadas.
       */
      function chDate(fld){
        var val = fld.value;

        if (val.length > 0) {
          var ok = 1;
          if (val.length < 10) {
            alert('Formato de Fecha debe ser aaaa-mm-dd');
            fld.value = '';
            fld.focus();
            ok = 0;
          }
          
          if (val.substr(4,1) == '-' && val.substr(7,1) == '-' && ok == 1) {
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

      /**
       * Permite descargar el Excel del agendamiento en Background.
       */
      function fnDescargar(xArchivo){
        parent.fmwork.location = "frgendoc.php?cRuta="+xArchivo;
      }

      /**
       * Permite recargar nuevamente el formulario.
       */
      function fnRecargar() {
				parent.fmwork.location="<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>";
			}

    </script>
  </head>
  <body>
    <form name='frgrm' action='frctrrcg.php' method="POST" target="fmpro">
      <center>
        <table width="540" cellspacing="0" cellpadding="0" border="0"><tr><td>
          <fieldset>
            <legend>Generaci&oacute;n de Reporte Tarifas Consolidado </legend>
            <table border = '0' cellpadding = '0' cellspacing = '0' width='540'>
              <?php $zCol = f_Format_Cols(25);
              echo $zCol;?>
              <tr>
                <td Class = "name" colspan = "5"><br>Estado Tarifas:</td>
                <td Class = "name" colspan = "20"><br>
                  <select Class = "letrase" name="cEstTari" style = "width:140">
                    <option value = 'TODOS'>TODOS</option>
                    <option value = 'ACTIVO' selected>ACTIVO</option>
                    <option value = 'INACTIVO'>INACTIVO</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td Class = "name" colspan = "5"><br>Tipo Operacion:</td>
                <td Class = "name" colspan = "20"><br>
                  <select class="letrase" size="1" name="cTipoOpe" style = "width:140">
                    <option value = "TODOS">TODOS</option>
                    <option value = "IMPORTACION">IMPORTACION</option>
                    <option value = "EXPORTACION">EXPORTACION</option>
                    <option value = "TRANSITO">TRANSITO</option>
                    <option value = "OTROS">OTROS</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td Class = "name" colspan = "5"><br><br>Tarifa por:</td>
                <td Class = "name" colspan = "5"><br><br>
                  <select Class = "letrase" name = "cTarTip" style = "width:100" onchange="javascript:fnCambiarTitulos(this.value)">
                    <option value = "CLIENTE" selected>CLIENTE</option>
                    <option value = "GRUPO">GRUPO</option>
                  </select>
                </td>
                <td Class = "name" colspan = "4">
                  <a href = "javascript:document.forms['frgrm']['cCliId'].value = '';
                                        document.forms['frgrm']['cCliNom'].value = '';
                                        document.forms['frgrm']['cCliDV'].value  = '';
                                        f_Links('cCliId','VALID')" id = "lCliId"><br><label id="lblCliId">Nit</label></a><br>
                  <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cCliId"
                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                              f_Links('cCliId','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                        onFocus="javascript:document.forms['frgrm']['cCliId'].value  ='';
                                            document.forms['frgrm']['cCliNom'].value = '';
                                            document.forms['frgrm']['cCliDV'].value  = '';
                                            this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                </td>
                <td Class = "name" colspan = "1"><br><label id="lblCliDv">Dv</label><br>
                  <input type = "text" Class = "letra" style = "width:020;text-align:center" name = "cCliDV" readonly>
                </td>
                <td Class = "name" colspan = "10"><br><label id="lblCliNom">Cliente</label><br>
                  <input type = "text" Class = "letra" style = "width:200" name = "cCliNom" id="cCliNom"
                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                              f_Links('cCliNom','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                        onFocus="javascript:document.forms['frgrm']['cCliId'].value  ='';
                                            document.forms['frgrm']['cCliNom'].value = '';
                                            document.forms['frgrm']['cCliDV'].value  = '';
                                            this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                </td>
              </tr>
              <tr>
                <td Class = "name" colspan = "5"><br><label id="lblEstado">Estado Cliente:</label></td>
                <td Class = "name" colspan = "20"><br>
                  <select Class = "letrase" name = "cEstCli" style = "width:120">
                    <option value = "TODOS" selected>TODOS</option>
                    <option value = "ACTIVO">ACTIVO</option>
                    <option value = "INACTIVO">INACTIVO</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td Class = "name" colspan = "5"><br><br><label>Concepto Cobro:</label></td>
                <td Class = "name" colspan = "3">
                  <a href = "javascript:document.forms['frgrm']['cSerId'].value    = '';
                                        document.forms['frgrm']['cSerDes'].value   = '';
                                        document.forms['frgrm']['cFcoId'].value    = '';
                                        document.forms['frgrm']['cFcoIds'].value   = '';
                                        document.forms['frgrm']['cFcoDes'].value   = '';
                                        f_Links('cSerId','WINDOW')" id="lSerId"><br>C&oacute;digo</a><br>
                  <input type = "text" Class = "letra" style = "width:060;text-align:center" name = "cSerId" id="cSerId"
                    onFocus="javascript:document.forms['frgrm']['cSerId'].value  ='';
                                        document.forms['frgrm']['cSerDes'].value = '';
                                        document.forms['frgrm']['cFcoId'].value  = '';
                                        document.forms['frgrm']['cFcoIds'].value = '';
                                        document.forms['frgrm']['cFcoDes'].value = '';
                                        this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';"
                    onBlur = "javascript:f_Links('cSerId','WINDOW');
                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'">
                  <input type = "hidden" name = "cFcoIds" readonly>
                </td>
                <td class="clase08" colspan="1"><br>&nbsp;<br>
                  <input type = "text" Class = "letra" style = "width:20;margin-top:1;text-align:center" readonly>
                </td>
                <td Class = "name" colspan = "17"><br>Descripci&oacute;n<br>
                  <input type = "text" Class = "letra" style = "width:360" name = "cSerDes"
                    onFocus="javascript:document.forms['frgrm']['cSerId'].value  = '';
                                        document.forms['frgrm']['cSerDes'].value = '';
                                        document.forms['frgrm']['cFcoId'].value  = '';
                                        document.forms['frgrm']['cFcoIds'].value = '';
                                        document.forms['frgrm']['cFcoDes'].value = '';
                                        this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';"
                    onBlur = "javascript:this.value=this.value.toUpperCase();
                                        f_Links('cSerDes','WINDOW');
                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'">
                </td>
              </tr>
              <tr>
                <td Class = "name" colspan = "5"><br><br><label>Forma Cobro:</label></td>
                <td Class = "name" colspan = "3">
                  <a href = "javascript:document.forms['frgrm']['cFcoId'].value = '';
                                        document.forms['frgrm']['cFcoDes'].value = '';
                                        f_Links('cFcoId','WINDOW')" id="lFcoId"><br>C&oacute;digo</a><br>
                  <input type = "text" Class = "letra" style = "width:060;text-align:center" name = "cFcoId"
                  onBlur = "javascript:this.value=this.value.toUpperCase();
                                        f_Links('cFcoId','VALID');
                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                  onFocus="javascript:document.forms['frgrm']['cFcoId'].value = '';
                                      document.forms['frgrm']['cFcoDes'].value = '';
                                      this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                </td>
                <td class="clase08" colspan="1"><br>&nbsp;<br>
                  <input type = "text" Class = "letra" style = "width:20;margin-top:1;text-align:center" readonly>
                </td>
                <td Class = "name" colspan = "17"><br>Descripci&oacute;n<br>
                  <input type = "text" Class = "letra" style = "width:360" name = "cFcoDes"
                  onBlur = "javascript:this.value=this.value.toUpperCase();
                                        f_Links('cFcoDes','VALID');
                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                  onFocus="javascript:document.forms['frgrm']['cFcoId'].value = '';
                                      document.forms['frgrm']['cFcoDes'].value = '';
                                      this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                </td>
              </tr>
              <tr>
                <td Class = "name" colspan = "5"><br>Filtrar por Fecha:</td>
                <td Class = "name" colspan = "6"><br>
                  <select Class = "letrase" name = "cTipoFec" style = "width:120">
                    <option value = "CREACION">CREACION</option>
                    <option value = "ACTUALIZACION">ACTUALIZACION</option>
                    <?php if ($vSysStr['system_control_vigencia_tarifas'] == "SI") { ?>
                      <option value = "VIGENCIA" selected>VIGENCIA</option>
                    <?php } ?>
                  </select>
                </td>
                <td class="name" colspan = "2" width="40"><br><center><a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">De</a></center></td>
                  <td class="name" colspan = "5"><br>
                    <input type="text" name="dDesde" style = "width:100;text-align:center" onblur="javascript:chDate(this);">
                  </td>
                  <td class="name" colspan = "2" width="40"><br><center><a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">A</a></center></td>
                  <td class="name" colspan = "5"><br>
                    <input type="text" name="dHasta" style = "width:100;text-align:center" onblur="javascript:chDate(this);">
                </td>
              </tr>
              <tr>
                <td Class = "name" colspan = "25"><br>
                  <label><input type="checkbox" name="cEjProBg" value ="SI" onclick="javascript:this.value = 'SI';this.checked = true" checked>Ejecutar Proceso en Background</label>
                </td>
              </tr>
            </table>
          </fieldset>
        <center>
        <table border="0" cellpadding="0" cellspacing="0" width="500">
          <tr height="21">
            <td width="318" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = 'javascript:fnGenerar()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
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
                        <td align="center"><strong>Cliente</strong></td>
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
                        
                        <td> 
                          <?php 
                          if($mArcProBg[$i]['gCliId'] != ""){?>
                              <?php echo $mArcProBg[$i]['gCliId']; ?>
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
    </form>
  </body>
</html>
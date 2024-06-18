<?php
  /**
   * Filtros Reporte Pedido.
   * --- Descripcion: Permite seleccionar los filtros para generar el Reporte de Pedido
   * @author Elian Amado. <elian.amado@openits.co>
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");
?>
<html>
  <head>
    <title>Reporte Pedido</title>
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
        parent.fmwork.location='<?php echo $cPlesk_Forms_Directory_Logistic ?>/frproces.php';
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      }

      /**
       * Permite cargar el valid/window dependiendo del campo seleccionado.
       */
      function fnLinks(xLink,xSwitch) {
        var nX = screen.width;
        var nY = screen.height;

        switch (xLink) {
          // Cliente
          case "cCliId":
            document.forms['frgrm']['cMifComId'].value   = '';
            document.forms['frgrm']['cMifComCod'].value  = '';
            document.forms['frgrm']['cMifComCsc'].value  = '';
            document.forms['frgrm']['cMifComCsc2'].value = '';
            document.forms['frgrm']['cMifId'].value      = '';
            document.forms['frgrm']['cDepNum'].value     = '';

            if (xSwitch == "VALID") {
              var zRuta = "frrpe150.php?gWhat=VALID"+
                                        "&gFunction=cCliId"+
                                        "&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();

              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frrpe150.php?gWhat=WINDOW"+
                                          "&gFunction=cCliId"+
                                          "&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();

              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cCliNom":
            if (xSwitch == "VALID") {
              var zRuta  = "frrpe150.php?gWhat=VALID"+
                                        "&gFunction=cCliNom"+
                                        "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();

              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frrpe150.php?gWhat=WINDOW"+
                                    "&gFunction=cCliNom"+
                                    "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();

              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          // MIF
          case "cMifComCsc":
            var nSwitch = 0;
            if(document.forms['frgrm']['cMifComCsc'].value != ""){
              if(document.forms['frgrm']['cMifComCsc'].value.length < 1){
                nSwitch = 1;
                alert('Debe Digitar al Menos Un Digito de la M.I.F,\nVerifique.');
              }

              if(document.forms['frgrm']['cCliId'].value == ""){
                nSwitch = 1;
                alert('Debe Seleccionar un Cliente para Consultar la M.I.F,\nVerifique.');
              }

              if (nSwitch == 0) {
                if (xSwitch == "VALID") {
                  var zRuta = "frlmca00.php?gModo="  +xSwitch+"&gFunction="+xLink+
                                          "&gPerAno="+document.forms['frgrm']['cPerAno'].value +
                                          "&gComCsc="+document.forms['frgrm']['cMifComCsc'].value +
                                          "&gCliId=" +document.forms['frgrm']['cCliId'].value;
                  parent.fmpro.location = zRuta;
                } else if(xSwitch == "WINDOW") {
                  var nNx      = (nX-500)/2;
                  var nNy      = (nY-250)/2;
                  var zWinPro  = "width=500,scrollbars=1,height=250,left="+nNx+",top="+nNy;
                  var zRuta = "frlmca00.php?gModo="  +xSwitch+"&gFunction="+xLink+
                                          "&gPerAno="+document.forms['frgrm']['cPerAno'].value +
                                          "&gComCsc="+document.forms['frgrm']['cMifComCsc'].value + 
                                          "&gCliId=" +document.forms['frgrm']['cCliId'].value;
                  zWindow = window.open(zRuta,xLink,zWinPro);
                  zWindow.focus();
                } else if (xSwitch == "EXACT") {
                  var zRuta = "frlmca00.php?gModo=EXACT&gFunction=" + xLink +
                                          "&gPerAno=" +document.forms['frgrm']['cPerAno'].value +
                                          "&gComCod=" +document.frgrm['cMifComCod'].value +
                                          "&gComCsc=" +document.frgrm['cMifComCsc'].value +
                                          "&gComCsc2="+document.frgrm['cMifComCsc2'].value +
                                          "&gCliId="  +document.frgrm['cCliId'].value;
                  parent.fmpro.location = zRuta;
                }
              } else {
                document.forms['frgrm']['cMifComCsc'].value = "";
              }
            }
          break;
          // Deposito
          case "cDepNum":
            if (document.forms['frgrm']['cCliId'].value == '') {
              alert("Debe Seleccionar el Cliente para Consultar los Depositos,\nVerifique.");
              document.forms['frgrm']['cDepNum'].value = '';
            } else {
              if (xSwitch == "VALID") {
                var zRuta = "frrpe155.php?gWhat=VALID" +
                                          "&gFunction=cDepNum" +
                                          "&gDepNum="+document.forms['frgrm']['cDepNum'].value +
                                          "&gCliId="+document.forms['frgrm']['cCliId'].value;
                parent.fmpro.location = zRuta;
              } else {
                var nNx     = (nX-600)/2;
                var nNy     = (nY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
                var zRuta   = "frrpe155.php?gWhat=WINDOW" +
                                            "&gFunction=cDepNum" +
                                            "&gDepNum="+document.forms['frgrm']['cDepNum'].value +
                                            "&gCliId="+document.forms['frgrm']['cCliId'].value;
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            }
          break;
        }
      }
      
      /**
       * Genera la descarga del reporte.
       */
      function fnGenerar() {
        var nSwitch = 0;
        if (document.forms['frgrm']['dDesde'].value == "" || document.forms['frgrm']['dHasta'].value == "" ||
            document.forms['frgrm']['dDesde'].value == "0000-00-00" || document.forms['frgrm']['dHasta'].value == "0000-00-00"
        ) {
          nSwitch = 1;
          alert("Debe Seleccionar el Rango de Fechas,\nVerifique.");
        } 

        if (nSwitch == 0) {
          var cRuta = "frrpeprn.php?gCliId="    +document.forms['frgrm']['cCliId'].value+
                                  "&gDepNum="   +document.forms['frgrm']['cDepNum'].value+
                                  "&gPerAno="   +document.forms['frgrm']['cPerAno'].value+
                                  "&gMifId="    +document.forms['frgrm']['cMifId'].value+
                                  "&gMifAnio="  +document.forms['frgrm']['cPerAno'].value+
                                  "&gDepNum="   +document.forms['frgrm']['cDepNum'].value+
                                  "&gEstCert="  +document.forms['frgrm']['cEstCert'].value+
                                  "&gDesde="    +document.forms['frgrm']['dDesde'].value+
                                  "&gHasta="    +document.forms['frgrm']['dHasta'].value;

          parent.fmpro.location = cRuta;
        }
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

    </script>
  </head>
  <body>
    <form name='frgrm' action='frrpeprn.php' method="POST" target="fmpro">
      <center>
        <table width="480" cellspacing="0" cellpadding="0" border="0"><tr><td>
          <fieldset>
            <legend>Consulta Pedidos </legend>
            <table border="2" cellspacing="0" cellpadding="0" width="480">
              <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
                <td class="name" width="30%"><center><h5><br>REPORTE MOVIMIENTO PEDIDOS</h5></center></td>
              </tr>
            </table>
            <table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
              <?php $zCol = f_Format_Cols(24);
              echo $zCol;?>
              <tr>
                <td Class = "clase08" colspan = "6"><br>
                  <a href = "javascript:document.forms['frgrm']['cCliId'].value  = '';
                                        document.forms['frgrm']['cCliNom'].value = '';
                                        document.forms['frgrm']['cCliDV'].value  = '';
                                        document.forms['frgrm']['cDepNum'].value = '';
                                        fnLinks('cCliId','VALID')" id = "lCliId">Nit</a><br>
                  <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cCliId"
                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cCliId','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                        onFocus="javascript:document.forms['frgrm']['cCliId'].value  ='';
                                            document.forms['frgrm']['cCliNom'].value = '';
                                            document.forms['frgrm']['cCliDV'].value  = '';
                                            document.forms['frgrm']['cDepNum'].value = '';
                                            this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                </td>
                <td Class = "clase08" colspan = "1"><br>Dv<br>
                  <input type = "text" Class = "letra" style = "width:020;text-align:center" name = "cCliDV" readonly>
                </td>
                <td Class = "clase08" colspan = "17"><br>Cliente<br>
                  <input type = "text" Class = "letra" style = "width:340" name = "cCliNom" id="cCliNom"
                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cCliNom','VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                        onFocus = "javascript:document.forms['frgrm']['cCliId'].value  ='';
                                              document.forms['frgrm']['cCliNom'].value = '';
                                              document.forms['frgrm']['cCliDV'].value  = '';
                                              document.forms['frgrm']['cDepNum'].value = '';
                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                </td>
              </tr>
              <tr>
                <td class = "clase08" colspan = "3">A&ntilde;o<br>
                  <select name = "cPerAno" style = "width:60;height:16">
                    <?php for($i=$vSysStr['logistica_ano_instalacion_modulo'];$i<=date('Y');$i++){ ?>
                      <option value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php  } ?>
                  </select>
                  <script language="javascript">
                    document.forms['frgrm']['cPerAno'].value = "<?php echo date('Y') ?>";
                  </script>
                </td>
                <input type="hidden" name="cMifId">
                <td Class = "clase08" colspan = "2">Id<br>
                  <input type = "text" Class = "letra" style = "width:40" name = "cMifComId" readonly>
                </td>
                <td Class = "clase08" colspan = "2">Cod<br>
                  <input type = "text" Class = "letra" style = "width:40" name = "cMifComCod" value = "<?php echo $cComCod ?>" readonly>
                </td>
                <td Class = "clase08" colspan = "5">M.I.F<br>
                  <input type = "text" Class = "letra" style = "width:100" name = "cMifComCsc"
                        onBlur = "javascript:f_FixFloat(this);
                                            this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'
                                            if(document.forms['frgrm']['cMifComCsc'].value.length > 0){
                                              fnLinks('cMifComCsc','VALID');
                                            }else{
                                              alert('Debe Digitar al Menos Un Digito de la M.I.F');
                                            }"
                        onFocus = "javascript:document.forms['frgrm']['cMifComId'].value   = '';
                                              document.forms['frgrm']['cMifComCod'].value  = '';
                                              document.forms['frgrm']['cMifComCsc'].value  = '';
                                              document.forms['frgrm']['cMifComCsc2'].value = '';
                                              document.forms['frgrm']['cMifId'].value      = '';
                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                </td>
                <input type="hidden" name="cMifComCsc2">

                <td Class = "clase08" colspan = "1"></td>
                <td Class = "clase08" colspan = "11">
                  <a href = "javascript:document.forms['frgrm']['cDepNum'].value = '';
                                        fnLinks('cDepNum','VALID')" id = "id_href_DepNum">Dep&oacute;sito</a><br>
                  <input type = "text" Class = "letra" style = "width:220" name = "cDepNum" maxlength="20"
                    onBlur = "javascript:fnLinks('cDepNum','VALID');
                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                    onFocus = "javascript:document.forms['frgrm']['cDepNum'].value = '';
                                          this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                </td>
              </tr>
              <tr>
                <td Class = "clase08" colspan = "3"><br>Estado:</td>
                <td Class = "clase08" colspan = "9"><br>
                  <select Class = "letrase" name = "cEstCert" style = "width:180">
                    <option value = "" selected>[SELECCIONE]</option>
                    <option value = "ENPROCESO">EN PROCESO</option>
                    <option value = "ANULADO">ANULADA</option>
                    <option value = "VALIDACION_FINANCIERA">VALIDACION FINANCIERA</option>
                    <option value = "CERTIFICADO">CERTIFICADA</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td Class = "clase08" colspan = "6"><br>Rango de Fechas <br>(Fecha Doc.):</td>
                <td Class = "clase08" colspan = "3" width="60"><br><br><center><a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">De</a></center></td>
                  <td class = "clase08" colspan = "6"><br><br>
                    <input type="text" name="dDesde" style = "width:120;text-align:center" onblur="javascript:chDate(this);">
                  </td>
                  <td Class = "clase08" colspan = "3" width="60"><br><br><center><a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">A</a></center></td>
                  <td Class = "clase08" colspan = "6"><br><br>
                    <input type="text" name="dHasta" style = "width:120;text-align:center" onblur="javascript:chDate(this);">
                </td>
              </tr>
            </table>
          </fieldset>
        <center>
        <table border="0" cellpadding="0" cellspacing="0" width="480">
          <tr height="21">
            <td width="298" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = 'javascript:fnGenerar()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
          </tr>
        </table>
      </center>
    </form>
  </body>
</html>
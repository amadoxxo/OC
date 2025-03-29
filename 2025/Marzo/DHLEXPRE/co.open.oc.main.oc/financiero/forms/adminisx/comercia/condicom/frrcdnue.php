<?php
/**
 * --- Descripción: Reporte de Condiciones Comerciales DHL Express
 * @author Elian Amado <elian.amado@openits.co>
 * @version 001
 * @package opencomex
 */
  include('../../../../libs/php/utility.php');
?>
<html>
<head>
  <title><?php echo $_COOKIE["kProDes"] ?></title>
  <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css">
  <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css">
  <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css">
  <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css">
  <script language="javascript" src="<?php echo $cSystem_Libs_JS_Directory_New ?>/utility.js"></script>
  <script language="javascript" src="<?php echo $cSystem_Libs_JS_Directory_New ?>/date_picker.js"></script>
  <script language="javascript">
    function fnLinks(xLink, xSwitch) {
      var nX = screen.width;
      var nY = screen.height;
      switch (xLink) {
        case "cCliId":
        case "cCliIdCom":
          if (xSwitch == "VALID") {
            var cRuta = "frrcd150.php?gWhat=VALID&gFunction="+xLink+"&gCliId="+document.forms['frnav'][xLink].value.toUpperCase();
            parent.fmpro.location = cRuta;
          } else {
            var nNx     = (nX-550)/2;
            var nNy     = (nY-300)/2;
            var cWinPro = 'width=650,scrollbars=1,height=500,left='+nNx+',top='+nNy;
            var cRuta   = "frrcd150.php?gWhat=WINDOW" + 
                                      "&gFunction="+xLink+
                                      "&gCliId="+document.forms['frnav'][xLink].value.toUpperCase();
            cWindow = window.open(cRuta,"cWindow",cWinPro);
            cWindow.focus();
          }
        break;
        case "cCliNom":
          if (xSwitch == "VALID") {
            var cRuta = "frrcd150.php?gWhat=VALID&gFunction="+xLink+"&gCliNom="+document.forms['frnav'][xLink].value.toUpperCase();
            parent.fmpro.location = cRuta;
          } else {
            var nNx     = (nX-550)/2;
            var nNy     = (nY-300)/2;
            var cWinPro = 'width=650,scrollbars=1,height=500,left='+nNx+',top='+nNy;
            var cRuta   = "frrcd150.php?gWhat=WINDOW" + 
                                      "&gFunction="+xLink+
                                      "&gCliNom="+document.forms['frnav'][xLink].value.toUpperCase();
            cWindow = window.open(cRuta,"cWindow",cWinPro);
            cWindow.focus();
          }
        break;
        case "cGtaId":
          if (xSwitch == "VALID") {
            var cRuta = "frrcd111.php?gWhat=VALID" +
                                      "&gFunction=cGtaId"
                                      "&gGtaId="+document.forms['frnav']['cGtaId'].value.toUpperCase();
            parent.fmpro.location = cRuta;
          } else {
            console.log(document.forms['frnav']['cGtaId'].value);
            var nNx     = (nX-650)/2;
            var nNy     = (nY-500)/2;
            var cWinPro = 'width=650,scrollbars=1,height=500,left='+nNx+',top='+nNy;
            var cRuta   = "frrcd111.php?gWhat=WINDOW" + 
                                      "&gFunction=cGtaId"
                                      "&gGtaId="+document.forms['frnav']['cGtaId'].value.toUpperCase();
            cWindow = window.open(cRuta,"cWindow",cWinPro);
            cWindow.focus();
          }
        break;
        case "cGtaDes":
          if (xSwitch == "VALID") {
            var cRuta = "frrcd111.php?gWhat=VALID" +
                                    "&gFunction=cGtaDes"+
                                    "&gGtaDes="+document.forms['frnav']['cGtaDes'].value.toUpperCase();
            parent.fmpro.location = cRuta;
          } else {
            var nNx     = (nX-550)/2;
            var nNy     = (nY-300)/2;
            var cWinPro = 'width=650,scrollbars=1,height=500,left='+nNx+',top='+nNy;
            var cRuta   = "frrcd111.php?gWhat=WINDOW" + 
                                      "&gFunction=cGtaDes"
                                      "&gGtaDes="+document.forms['frnav']['cGtaDes'].value.toUpperCase();
            cWindow = window.open(cRuta,"cWindow",cWinPro);
            cWindow.focus();
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

          if(mes > 12){
            alert('El mes debe ser menor a 13');
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
        }else{
          if(val.length > 0){
            alert('Fecha erronea, Verifique');
          }
          fld.value = '0000-00-00';
          fld.focus();
        }
      }else{
        alert("Debe Ingresar una Fecha");
        fld.value = '0000-00-00';
        fld.focus();
      }
    }
  </script>
  <?php $icsc = 1 ?>
</head>
<body topmargin="0" leftmargin="0" marginwidth="0" marginheight="0" style="margin-right: 0;">
  <form name="frnav" action="frrcdprn.php" method="post" target="fmpro">
    <center>
      <table border="0" cellpading="0" cellspacing="0" width="540">
        <tr>
          <td>
            <fieldset><legend>Reporte de Condiciones Comerciales DHL Express</legend>
              <table border="2" cellspacing="0" cellpadding="0" width="540">
                <tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>" style="height: 25;">
                  <td class="name"><center><h5><br>REPORTE CONDICIONES COMERCIALES DHL EXPRESS</h5></center></td>
                </tr>
              </table>
              <table border="0" cellpadding="0" cellspacing="0" width="540">
                <?php echo columnas(24,20); ?>
                <tr>
                  <td class="name" colspan="06" style="width: 120;"><br>Desplegar en<br></td>
                  <td class="name" colspan="09"><br><input type="radio" name="rTipo" value="1" checked> Pantalla<br></td>
                  <td class="name" colspan="09"><br><input type="radio" name="rTipo" value="2">Excel<br></td>
                </tr>
              </table>
              <table border="0" cellpadding="0" cellspacing="0" width="540">
                <?php echo columnas(24,20); ?>
                <tr>
                  <td class="name" colspan="05"><br>
                    <a href="javascript:document.forms['frnav']['cCliId'].value  = '';
                                        document.forms['frnav']['cCliDv'].value  = '';
                                        document.forms['frnav']['cCliNom'].value = '';
                                        fnLinks('cCliId','VALID')">Importador
                    </a><br>
                  </td>
                  <td class="name" colspan="06"><br>
                    <input type="text" class="letra" name="cCliId" style="width: 120;"
                      onfocus="javascript:document.forms['frnav']['cCliId'].value  = '';
                                          document.forms['frnav']['cCliDv'].value  = '';
                                          document.forms['frnav']['cCliNom'].value = '';
                                          this.style.background='#00FFFF'"
                      onblur="javascript:this.value=this.value.toUpperCase();
                                          fnLinks('cCliId', 'VALID');
                                          this.style.background='#FFFFFF'">
                  </td>
                  <td class="name" colspan="01"><br>
                    <input type="text" class="letra" name="cCliDv" style="width: 20; text-align: center;" readonly>
                  </td>
                  <td class="name" colspan="12"><br>
                    <input type="text" class="letra" name="cCliNom" style="width: 240;"
                      onfocus="javascript:document.forms['frnav']['cCliId'].value = '';
                                          document.forms['frnav']['cCliDv'].value = '';
                                          document.forms['frnav']['cCliNom'].value = '';
                                          this.style.background='#00FFFF'"
                      onblur="javascript:this.value=this.value.toUpperCase();
                                        fnLinks('cCliNom', 'VALID');
                                        this.style.background='#FFFFFF'">
                  </td>
                  <td class="name" colspan="2"><input type="checkbox" name="ch<?php echo $icsc ?>" id=""></td>
                  <td class="name" colspan="3"><input type="text" class="letra" name="" style="width: 40; text-align: right;" readonly></td>
                </tr>
                <tr>
                  <?php $icsc++ ?>
                  <td class="name" colspan="05"><br>Plazo de Cr&eacute;dito</td>
                  <td class="name" colspan="19"><br>
                    <input type="number" class="letra" style="width: 380;" name="">
                  </td>
                </tr>
                <tr>
                  <td class="name" colspan="05"><br>Tipo de Anticipo</td>
                  <td class="name" colspan="19"><br>
                    <select class="letrase" name="cAnticipo" style="width: 380;">
                      <option value="" selected>SELECCIONE</option>
                      <option value="CON">CON ANTICIPO</option>
                      <option value="SIN">SIN ANTICIPO</option>
                      <option value="CONDICIONADO">ANTICIPO CONDICIONADO</option>
                      <option value="ANTICIPO GLOBAL">GLOBAL</option>
                    </select>
                  </td>
                  <td class="name" colspan="2"><input type="checkbox" name="ch<?php echo $icsc ?>" id=""></td>
                  <td class="name" colspan="3"><input type="text" class="letra" name="" style="width: 40; text-align: right;" readonly></td>
                </tr>
                <tr>
                  <td class="name" colspan="05"><br>Aplica Inter&eacute;s Financiero</td>
                  <td class="name" colspan="19">
                    <select class="letrase" name="cInteres" style="width: 380;">
                      <option value="NO" selected>NO</option>
                      <option value="SI">SI</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="name" colspan="05"><br>Partir de qu&eacute; Monto</td>
                  <td class="name" colspan="19">
                    <input type="number" class="letra" style="width: 380;" name="">
                  </td>
                </tr>
                <tr>
                  <td class="name" colspan="05"><br>N&uacute;mero de Cotizaci&oacute;n</td>
                  <td class="name" colspan="19">
                    <input type="text" class="letra" style="width: 380;" name="">
                  </td>
                </tr>
                <tr>
                  <td class="name" colspan="06"><br>Fecha Vigencia:</td>
                  <td class="name" colspan="02"><br><a href="javascript:show_calendar('frnav.dFecVigDel')" id="id_href_dFecVigDel">Del</a></td>
                  <td class="name" colspan="06"><br>
                    <input type="text" name="dFecVigDel" style="width: 120; text-align: center;" onblur="javascript:chDate(this);" value="<?php echo "0000-00-00" ?>">
                  </td>
                  <td class="name" colspan="04"><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:show_calendar('frnav.dFecVigAl')" id="id_href_dFecVigAl">Al</a></td>
                  <td class="name" colspan="06" align="right"><br>
                    <input type="text" name="dFecVigAl" style="width: 120; text-align: center;" onblur="javascript:chDate(this);" value="<?php echo "0000-00-00" ?>">
                  </td>
                </tr>
                <tr>
                  <td class="name" colspan="05"><br>Cierre de Facturaci&oacute;n</td>
                  <td class="name" colspan="19">
                    <input type="number" class="letra" style="width: 380;" name="">
                  </td>
                </tr>
                <tr>
                  <td class="name" colspan="05"><br>
                    <a href="javascript:document.forms['frnav']['cCliIdCom'].value  = '';
                                        document.forms['frnav']['cCliDvCom'].value  = '';
                                        document.forms['frnav']['cCliNomCom'].value = '';
                                        fnLinks('cCliIdCom','VALID')">Comercial
                    </a><br>
                  </td>
                  <td class="name" colspan="06"><br>
                    <input type = "text" Class = "letra" name = "cCliIdCom" style = "width: 120;"
                      onfocus="javascript:document.forms['frnav']['cCliIdCom'].value  = '';
                                          document.forms['frnav']['cCliNomCom'].value = '';
                                          document.forms['frnav']['cCliDvCom'].value  = '';
                                          this.style.background='#00FFFF'"
                      onblur="javascript:this.value=this.value.toUpperCase();
                                            fnLinks('cCliIdCom','VALID');
                                            this.style.background='#FFFFFF'">
                  </td>
                  <td class="name" colspan="01"><br>
                    <input type = "text" class="letra" name="cCliDvCom" style="width: 20; text-align: center" readonly>
                  </td>
                  <td class="name" colspan="12"><br>
                    <input type = "text" class="letra" style = "width: 240;" name = "cCliNomCom" readonly>
                  </td>
                </tr>
                <tr>
                  <td class="name" colspan="05"><br>
                    <a href="javascript:document.forms['frnav']['cGtaId'].value  = '';
                                        document.forms['frnav']['cGtaDes'].value = '';
                                        fnLinks('cGtaId','VALID')">Grupo de Tarifa
                    </a><br>
                  </td>
                  <td class="name" colspan="06"><br>
                    <input type="text" class="letra" style ="width: 120;" name="cGtaId"
                      onfocus="javascript:document.forms['frnav']['cGtaId'].value  = '';
                                          document.forms['frnav']['cGtaDes'].value = '';
                                          this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'"
                      onblur="javascript:this.value=this.value.toUpperCase();
                                            fnLinks('cGtaId','VALID');
                                            this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'">
                  </td>
                  <td class="name" colspan="13"><br>
                    <input type="text" class="letra" style="width: 260;" name="cGtaDes"
                      onfocus="javascript:document.forms['frnav']['cGtaId'].value = '';
                                          document.forms['frnav']['cGtaDes'].value = '';
                                          this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'"
                      onblur="javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cGtaDes', 'VALID');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'">
                  </td>
                </tr>
                <tr>
                  <td class="name" colspan="05"><br>Cierre de Facturaci&oacute;n</td>
                </tr>
                <tr>
                  <td class="name" colspan="05"><br>Descuentos</td>
                </tr>
              </table><br>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
  </form>
</body>
</html>
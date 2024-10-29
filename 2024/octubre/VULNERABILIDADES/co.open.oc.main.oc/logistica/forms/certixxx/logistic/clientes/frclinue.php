<?php
  namespace openComex;
/**
 * Proceso Cliente.
 * --- Descripcion: Permite Crear un Nuevo Cliente.
 * @author oscar.perez@openits.co
 * @package opencomex
 * @version 001
 */
include("../../../../../financiero/libs/php/utility.php");
?>
<html>
  <head>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
    <script languaje = "javascript">
      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      }

      function fnHideShow(xId)	{
        if (xId == 'NATURAL')	{
          document.getElementById('DivNom1').style.display='none';
          document.getElementById('DivNom2').style.display='block';
        }	else	{
          document.getElementById('DivNom1').style.display='block';
          document.getElementById('DivNom2').style.display='none';
        }
      }

      function fnValidacCliId(xInput, xCampo)	{
        var nValor = xInput.value;
        if (nValor.length > 0)	{
          var nX      = screen.width;
          var nY      = screen.height;
          var nNx     = (nX-550)/2;
          var nNy     = (nY-250)/2;
          var cWinPro = 'width=550,scrollbars=1,height=250,left='+nNx+',top='+nNy;
          var cRuta   = 'frclicod.php?cCliId='+nValor+'&cCampo='+xCampo;
          cWindow     = window.open(cRuta,'cWindow',cWinPro);
          cWindow.focus();
        }	else	{
          alert('Debe digitar un DATO');
          xInput.blur();
        }
      }

      function fnValidaDv(){
        if(document.forms['frgrm']['cTdiId'].value == ''){
          document.forms['frgrm']['cCliDV'].value = '';
        }
      }

      function fnLinks(xLink,xSwitch,xSecuencia) {
        var nX    = screen.width;
        var nY    = screen.height;
        switch (xLink) {
          case "cTdiId":
            if (xSwitch == "VALID") {
              var cRuta  = "frpar109.php?gWhat=VALID&gFunction=cTdiId&cTdiId="+document.forms['frgrm']['cTdiId'].value.toUpperCase()+"&cCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              parent.fmpro.location = cRuta;
            } else {
              var nNx     = (nX-600)/2;
              var nNy     = (nY-250)/2;
              var cWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var cRuta   = "frpar109.php?gWhat=WINDOW&gFunction=cTdiId&cTdiId="+document.forms['frgrm']['cTdiId'].value.toUpperCase()+"&cCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              cWindow = window.open(cRuta,"cWindow",cWinPro);
              cWindow.focus();
            }
          break;
          case "cPaiId":
            if (xSwitch == "VALID") {
              var cRuta  = "frpai052.php?gWhat=VALID&gFunction=cPaiId&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase()+"";
              parent.fmpro.location = cRuta;
            } else {
              var nNx     = (nX-600)/2;
              var nNy     = (nY-250)/2;
              var cWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var cRuta   = "frpai052.php?gWhat=WINDOW&gFunction=cPaiId&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase()+"";
              cWindow = window.open(cRuta,"cWindow",cWinPro);
              cWindow.focus();
            }
          break;
          case "cDepId":
            if (xSwitch == "VALID") {
              var cRuta = "frdep054.php?gWhat=VALID&gFunction=cDepId&cDepId="+document.forms['frgrm']['cDepId'].value.toUpperCase()+
                          "&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase();
              parent.fmpro.location = cRuta;
            } else {
              var nNx     = (nX-600)/2;
              var nNy     = (nY-250)/2;
              var cWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var cRuta   = "frdep054.php?gWhat=WINDOW&gFunction=cDepId&cDepId="+document.forms['frgrm']['cDepId'].value.toUpperCase()+
                            "&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase();
              cWindow = window.open(cRuta,"cWindow",cWinPro);
              cWindow.focus();
            }
          break;
          case "cCiuId":
            if (xSwitch == "VALID") {
              var cRuta = "frciu055.php?gWhat=VALID&gFunction=cCiuId&cCiuId="+document.forms['frgrm']['cCiuId'].value.toUpperCase()+
                          "&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase()+
                          "&cDepId="+document.forms['frgrm']['cDepId'].value.toUpperCase();
              parent.fmpro.location = cRuta;
            } else {
              var nNx     = (nX-600)/2;
              var nNy     = (nY-250)/2;
              var cWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var cRuta   = "frciu055.php?gWhat=WINDOW&gFunction=cCiuId&CiuId="+document.forms['frgrm']['cCiuId'].value.toUpperCase()+
                            "&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase()+
                            "&cDepId="+document.forms['frgrm']['cDepId'].value.toUpperCase();
              cWindow = window.open(cRuta,"cWindow",cWinPro);
              cWindow.focus();
            }
        }
      }

      function fnGenDv(xnit){
        var resdv = '';
        if (document.forms['frgrm']['cTdiId'].value.length > 0) {
          var lnit = xnit.length;

          // Expresion regular para validar si es alfanumerico
          var alfanum = 0;
          var cExpReg = /^\d*$/;
          if(!xnit.search(cExpReg)) {
            alfanum = 0;
          } else {
            alfanum = 1;
          }

          if (lnit > 0 && alfanum == 0){
            var suma =0;
            var anit = new Array(97,89,83,79,73,71,67,59,53,47,43,41,37,29,23,19,17,13,7,3);

            var ini = 20-lnit;
            for(i=0;i<lnit;i++){
              var vdigito = xnit.charAt(i);
              var vl = 1*vdigito;
              var suma = suma + (vl * anit[ini]);
              ini+=1;
            }
            var resdv = suma % 11;
            if(resdv > 1){
              resdv = 11 - resdv;
            }
          }
        }
        document.forms['frgrm']['cCliDV'].value = resdv;
      }

      function fnValidacEstado() {
        var cEstado = document.forms['frgrm']['cEstado'].value.toUpperCase();
        if(cEstado == 'A' || cEstado == 'AC' || cEstado == 'ACT' || cEstado == 'ACTI' || cEstado == 'ACTIV' || cEstado == 'ACTIVO'){
          cEstado = 'ACTIVO';
        } else {
          if(cEstado == 'I' || cEstado == 'IN' || cEstado == 'INA' || cEstado == 'INAC' || cEstado == 'INACT' || cEstado == 'INACTI' || cEstado == 'INACTIV' || cEstado == 'INACTIVO') {
            cEstado = 'INACTIVO';
          } else {
            cEstado = '';
          }
        }
        document.forms['frgrm']['cEstado'].value = cEstado;
      }

      function fnGuardar() {
        document.forms['frgrm'].submit();
      }

    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="720">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo ($_COOKIE['kModo'] == "NUEVO") ? "Nuevo {$_COOKIE['kProDes']}" : "Editar {$_COOKIE['kProDes']}" ?></legend>
              <form name = 'frgrm' action = 'frcligra.php' method = 'post' target='fmpro'>
                <center>
                  <table border="0" cellpadding="0" cellspacing="0" width="720">
                    <?php $nCol = f_Format_Cols(36); echo $nCol; ?>
                    <tr>
                      <td class="clase08" colspan="8">Tipo de Persona<br>
                      <select class="letrase" size="1" name="cTpeId" style = "width:160" onchange="javascript:fnHideShow(this.value);">
                        <option value = "" selected>[SELECCIONE]</option>
                        <option value = "JURIDICA">PERSONA JURIDICA</option>
                        <option value = "NATURAL" >PERSONA NATURAL</option>
                      </select>
                      </td>
                      <td Class = "clase08" colspan = "4">
                        <a href = "javascript:document.frgrm.cTdiId.value  = '';
                                              document.frgrm.cTdiDes.value = '';
                                              fnValidaDv(this.value);
                                              fnLinks('cTdiId','VALID');" id="IdTdi">Id</a><br>
                        <input type = 'text' Class = 'letra' style = 'width:80' name = 'cTdiId' maxlength="2"
                              onBlur = "javascript:fnValidaDv(this.value);
                                        this.value=this.value.toUpperCase();
                                        fnLinks('cTdiId','VALID');
                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus="javascript:fnValidaDv(this.value);
                                        document.frgrm.cTdiId.value  = '';
                                        document.frgrm.cTdiDes.value = '';
                                        this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td Class = 'clase08' colspan = '12'>Tipo de Documento<br>
                        <input type="text" class="letra" style="width:240" name="cTdiDes" readonly>
                      </td>
                      <td Class = 'clase08' colspan = '9'>No. Identificaci&oacute;n<br>
                      <input type = 'text' Class = 'letra' style = 'width:180' name = "cCliId" maxlength="20"
                              
                              onBlur = "javascript:fnGenDv(this.value);
                                                  this.value=this.value.toUpperCase();
                                                  fnValidacCliId((this),'cliidxxx');
                                                  this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td Class = 'clase08' colspan = '3'>Dv<br>
                        <input type = 'text' Class = 'letra' style = 'width:60' name = "cCliDV" readonly>
                      </td>
                    </tr>
                    <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                      <td class="clase08" colspan="36">
                        <div id='DivNom1'>
                          <table border='0' cellpadding='0' cellspacing='0' width='720'>
                            <?php $nCol = f_Format_Cols(36); echo $nCol; ?>
                            <tr>
                              <td Class = "clase08" colspan = "18">Razon Social<br>
                                <input type = "text" Class = "letra" name = "cCliNom" style = "width:360" maxlength="100"
                                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                                            fnValidacCliId((this),'clinomxx');
                                                            this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                        onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td Class = "clase08" colspan = "1"><br>
                                <input type = "text" Class = "letra" style = "width:20" readOnly>
                              </td>
                              <td Class = "clase08" colspan = "17">Nombre Comercial<br>
                                <input type = "text" Class = "letra" name = "cCliNomC" style = "width:340" maxlength="100"
                                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                                        fnValidacCliId((this),'clinomxx');
                                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                        onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                            </tr>
                          </table>
                        </div>
                        <div id = 'DivNom2'>
                          <table border='0' cellpadding='0' cellspacing='0' width='720'>
                            <?php $nCol = f_Format_Cols(36); echo $nCol; ?>
                            <tr>
                              <td Class = "clase08" colspan = "9">Primer Apellido<br>
                                <input type = "text" Class = "letra" name = "cCliPApe" style = "width:180" maxlength="100"
                                  onBlur = "javascript:this.value=this.value.toUpperCase();
                                                      fnValidacCliId((this),'clinomxx');
                                                      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td Class = "clase08" colspan = "9">Segundo Apellido<br>
                                <input type = "text" Class = "letra" name = "cCliSApe" style = "width:180" maxlength="100"
                                  onBlur = "javascript:this.value=this.value.toUpperCase();
                                                      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td Class = "clase08" colspan = "9">Primer Nombre<br>
                                <input type = "text" Class = "letra" name = "cCliPNom" style = "width:180" maxlength="100"
                                  onBlur = "javascript:this.value=this.value.toUpperCase();
                                                      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td Class = "clase08" colspan = "9">Segundo Nombre<br>
                                <input type = "text" Class = "letra" name = "cCliSNom" style = "width:180" maxlength="100"
                                  onBlur = "javascript:this.value=this.value.toUpperCase();
                                                      this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td Class = "clase08" colspan = "4"><br>Clasificacion :<br></td>
                      <td Class = "clase08" colspan = "8"><br><input type="checkbox" name = "vChCli" onclick="javascript:if(this.checked == true){ this.value='SI'; } else { this.value='NO'; }">Cliente<br></td>
                      <td Class = "clase08" colspan = "8"><br><input type="checkbox" name = "vChUsu" onclick="javascript:if(this.checked == true){ this.value='SI'; } else { this.value='NO'; }">Usuario<br></td>
                      <td Class = "clase08" colspan = "8"><br><input type="checkbox" name = "vChDian" onclick="javascript:if(this.checked == true){ this.value='SI'; } else { this.value='NO'; }">Usuario DIAN<br></td>
                      <td Class = "clase08" colspan = "8"><br><input type="checkbox" name = "vChEmp" onclick="javascript:if(this.checked == true){ this.value='SI'; } else { this.value='NO'; }">Empleado<br></td>
                    </tr>
                    <tr>
                      <td Class = "clase08" colspan = "4"><br></td>
                      <td Class = "clase08" colspan = "8"><input type="checkbox" name = "vChCon" onclick="javascript:if(this.checked == true){ this.value='SI'; } else { this.value='NO'; }">Contacto<br></td>
                      <td Class = "clase08" colspan = "8"><input type="checkbox" name = "vChOtr" onclick="javascript:if(this.checked == true){ this.value='SI'; } else { this.value='NO'; }">Otros<br></td>
                      <td Class = "clase08" colspan = "8"><br></td>
                      <td Class = "clase08" colspan = "8"><br></td>
                    </tr>
                    <tr>
                      <td colspan="36">
                        <fieldset>
                          <legend>Domicilio Fiscal</legend>
                          <table border='0' cellpadding='0' cellspacing='0' width='700'>
                            <?php $nCol = f_Format_Cols(35); echo $nCol; ?>
                            <tr>
                              <td Class = "clase08" colspan = "2">
                                <a href = "javascript:document.forms['frgrm']['cPaiId'].value  = '';
                                                      document.forms['frgrm']['cPaiDes'].value = '';
                                                      document.forms['frgrm']['cDepId'].value  = '';
                                                      document.forms['frgrm']['cDepDes'].value = '';
                                                      document.forms['frgrm']['cCiuId'].value  = '';
                                                      document.forms['frgrm']['cCiuDes'].value = '';
                                                      fnLinks('cPaiId','VALID'); " id="IdPai">Id</a><br>
                                <input type = 'text' Class = 'letra' style = 'width:40' name = 'cPaiId' maxlength="10"
                                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                                            fnLinks('cPaiId','VALID');
                                                            this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                        onFocus = "javascript:document.forms['frgrm']['cPaiId'].value  = '';
                                                            document.forms['frgrm']['cPaiDes'].value = '';
                                                            document.forms['frgrm']['cDepId'].value  = '';
                                                            document.forms['frgrm']['cDepDes'].value = '';
                                                            document.forms['frgrm']['cCiuId'].value  = '';
                                                            document.forms['frgrm']['cCiuDes'].value = '';
                                                            this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td Class = 'clase08' colspan = '9'>Pa&iacute;s<br>
                                    <input type = 'text' Class = 'letra' style = 'width:180' name = 'cPaiDes' readonly>
                              </td>
                              <td Class = "clase08" colspan = "2">
                                <a href = "javascript:document.forms['frgrm']['cDepId'].value  = '';
                                                      document.forms['frgrm']['cDepDes'].value = '';
                                                      document.forms['frgrm']['cCiuId'].value  = '';
                                                      document.forms['frgrm']['cCiuDes'].value = '';
                                                      fnLinks('cDepId','WINDOW')" id="IdDep">Id</a><br>
                                <input type = 'text' Class = 'letra' style = 'width:40' name = 'cDepId' maxlength="10"
                                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                                          fnLinks('cDepId','VALID');
                                                          this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                        onFocus = "javascript:document.forms['frgrm']['cDepId'].value  = '';
                                                            document.forms['frgrm']['cDepDes'].value = '';
                                                            document.forms['frgrm']['cCiuId'].value  = '';
                                                            document.forms['frgrm']['cCiuDes'].value = '';
                                                            this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td Class = 'clase08' colspan = '10'>Departamento<br>
                                <input type = 'text' Class = 'letra' style = 'width:200' name = 'cDepDes' readonly>
                              </td>
                              <td Class = "clase08" colspan = "2">
                                <a href = "javascript:document.forms['frgrm']['cCiuId'].value  = '';
                                                      document.forms['frgrm']['cCiuDes'].value = '';
                                                      fnLinks('cCiuId','WINDOW')" id="IdCiu">Id</a><br>
                                <input type = 'text' Class = 'letra' style = 'width:40' name = 'cCiuId' maxlength="10"
                                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                                            fnLinks('cCiuId','VALID');
                                                            this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                        onFocus = "javascript:document.forms['frgrm']['cCiuId'].value  = '';
                                                              document.forms['frgrm']['cCiuDes'].value = '';
                                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td Class = 'clase08' colspan = '10'>Ciudad<br>
                                <input type = 'text' Class = 'letra' style = 'width:200' name = 'cCiuDes' readonly>
                              </td> 
                            </tr>
                            <tr>
                              <td Class = "clase08" colspan = "27">Direcci&oacute;n Domicilio Fiscal<br>
                                <input type = "text" Class = "letra" name = "cCliDir" style = "width:540" maxlength="50"
                                        onblur = "javascript:this.value=this.value.toUpperCase();
                                                                  this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                        onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td Class = "clase08" colspan = "8">C&oacute;digo Postal<br>
                                <input type = "text" Class = "letra" name = "cCliCPos" style = "width:160" maxlength="10"
                                        onblur = "javascript:this.value=this.value.toUpperCase();
                                                                  this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                        onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                            </tr>
                          </table>
                        </fieldset>
                      </td>
                    </tr>
                    <tr>
                      <td Class = "clase08" colspan = "6"><br>Tel&eacute;fono<br>
                        <input type = "text" Class = "letra" name = "cCliTel" style = "width:120" maxlength="20"
                                onblur = "javascript:this.value=this.value.toUpperCase();
                                                    this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td Class = "clase08" colspan = "7"><br>Tel&eacute;fono Movil<br>
                        <input type = "text" Class = "letra" name = "cCliMov" style = "width:140" maxlength="10"
                                onblur = "javascript:this.value=this.value.toUpperCase();
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td Class = "clase08" colspan = "15"><br>Correo Facturaci&oacute;n Electr&oacute;nica<br>
                        <input type = "text" Class = "letra" name = "cCliEma" style = "width:300"
                                onblur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td Class = "clase08" colspan = "8"><br>Apartado A&eacute;reo<br>
                        <input type = "text" Class = "letra" name = "cCliApa" style = "width:160"
                                onblur = "javascript:this.value=this.value.toUpperCase();
                                                  this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                    </tr>
                    <tr>
                      <td colspan="36">
                        <fieldset>
                        <legend>Integraci&oacute;n SAP</legend>
                          <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
                            <?php $nCol = f_Format_Cols(35); echo $nCol; ?>
                            <tr>
                              <td Class = "clase08" colspan = "35">C&oacute;digo SAP<br>
                                <input type = "text" Class = "letra" name = "cCliSap" style = "width:150" maxlength="10"
                                        onBlur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                        onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                            </tr>
                          </table>
                        </fieldset>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="36">
                        <fieldset>
                        <legend>Condiciones Factura</legend>
                          <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
                            <?php $nCol = f_Format_Cols(35); echo $nCol; ?>
                            <td Class = "clase08" colspan = "6">Requiere Prefactura:<br></td>
                            <td Class = "clase08" colspan = "2"><label><input type="radio" name="rCheckF" value="SI">SI</label><br></td>
                            <td Class = "clase08" colspan = "28"><label><input type="radio" name="rCheckF" value="NO">NO</label><br></td>
                          </table>
                        </fieldset>
                      </td>
                    </tr>
                    <tr>
                      <td Class = "clase08" colspan = "36"><br>Observaci&oacute;n<br>
                        <textarea Class = 'letrata' style = 'width:720;height:48' name = 'cCliObs'></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td Class = "clase08" colspan = "9">Fecha<br>
                        <input type = "text" Class = "letra"  style = "width:180;text-align:center"  name = "dFecCre"  value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "9">Hora<br>
                        <input type = 'text' Class = 'letra' style = "width:180;text-align:center" name = "dHorCre"  value = "<?php echo date('H:i:s') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "9">Modificado<br>
                        <input type = "text" Class = "letra"  style = "width:180;text-align:center"  name = "vFecMod"  value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "9">Estado<br>
                        <input type = "text" Class = "letra" style = "width:180;text-align:center" name = "cEstado"  value = "ACTIVO"
                                onblur = "javascript:this.value=this.value.toUpperCase();fnValidacEstado();
                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                    </tr>
                  </table>
                </center>
              </form>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="720">
        <tr height="21">
          <?php switch ($_COOKIE['kModo']) {
            case "VER": ?>
              <td width="629" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
            default: ?>
              <td width="538" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:fnGuardar()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
          } ?>
        </tr>
      </table>
    </center>
    <br>
  </body>
</html>
<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
<?php
switch ($_COOKIE['kModo']) {
  case "NUEVO":
    ?>
    <script languaje = "javascript">
      fnHideShow('');
      document.forms['frgrm']['cEstado'].readOnly  = true;
    </script>
    <?php
  break;
  case "EDITAR":
    fnCargaData($cCliId);
    ?>
    <script languaje = "javascript">
      document.forms['frgrm']['cCliId'].readOnly	 	= true;
      document.forms['frgrm']['cCliId'].onblur		  = "";
      if(document.forms['frgrm']['cTdiId'].value == ""){
        document.forms['frgrm']['cCliDV'].value = "";
      }
      fnHideShow(document.forms['frgrm']['cTpeId'].value);
    </script>
    <?php 
  break;
  case "VER":
    fnCargaData($cCliId);
    ?>
    <script languaje = "javascript">
      for (x=0;x<document.forms['frgrm'].elements.length;x++) {
        document.forms['frgrm'].elements[x].readOnly = true;
        document.forms['frgrm'].elements[x].onfocus  = "";
        document.forms['frgrm'].elements[x].onblur   = "";
        document.forms['frgrm'].elements[x].style.fontWeight = "bold";
      }
      document.forms['frgrm']['vChUsu'].disabled  = true;
      document.forms['frgrm']['vChDian'].disabled = true;
      document.forms['frgrm']['vChEmp'].disabled  = true;
      document.forms['frgrm']['vChCon'].disabled  = true;
      document.forms['frgrm']['vChOtr'].disabled  = true;
      document.getElementById('IdTdi').disabled   = true;
      document.getElementById('IdTdi').href       = 'javascript:alert("No permitido")';
      document.getElementById('IdPai').disabled   = true;
      document.getElementById('IdPai').href       = 'javascript:alert("No permitido")';
      document.getElementById('IdDep').disabled   = true;
      document.getElementById('IdDep').href       = 'javascript:alert("No permitido")';
      document.getElementById('IdCiu').disabled   = true;
      document.getElementById('IdCiu').href       = 'javascript:alert("No permitido")';
      document.forms['frgrm']['cTpeId'].disabled  = true;
      document.forms['frgrm']['rCheckF'].disabled = true;
      fnHideShow(document.forms['frgrm']['cTpeId'].value);
    </script>
  <?php break;
} ?>

<?php function fnCargaData($xCliId) {
  global $cAlfa; global $xConexion01; global $vSysStr; global $_COOKIE;

  $cBuscar01 = array('"',chr(13),chr(10),chr(27),chr(9));
  $cReempl01 = array('\"'," "," "," "," ");

  //Datos Cliente
  $qDatCli  = "SELECT * ";
  $qDatCli .= "FROM $cAlfa.lpar0150 ";
  $qDatCli .= "WHERE cliidxxx = \"$xCliId\" LIMIT 0,1";
  $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
  $vDatCli  = mysql_fetch_array($xDatCli);

  //Tipo Documento
  $qDatTdi  = "SELECT tdidesxx ";
  $qDatTdi .= "FROM $cAlfa.fpar0109 ";
  $qDatTdi .= "WHERE tdiidxxx =\"{$vDatCli['tdiidxxx']}\" LIMIT 0,1";
  $xDatTdi  = f_MySql("SELECT","",$qDatTdi,$xConexion01,"");
  $vDatTdi  = mysql_fetch_array($xDatTdi);

  //Pais Domicilio Fiscal
  $qDatPai  = "SELECT PAIDESXX, PAIIDNXX ";
  $qDatPai .= "FROM $cAlfa.SIAI0052 ";
  $qDatPai .= "WHERE ";
  $qDatPai .= "PAIIDXXX =\"{$vDatCli['paiidxxx']}\" LIMIT 0,1";
  $xDatPai  = f_MySql("SELECT","",$qDatPai,$xConexion01,"");
  $vDatPai  = mysql_fetch_array($xDatPai);
  $cPaisDes = $vDatPai['PAIDESXX']." "."(".$vDatPai['PAIIDNXX'].")";

  //Departamento
  $qDatDep = "SELECT DEPDESXX ";
  $qDatDep.= "FROM $cAlfa.SIAI0054 ";
  $qDatDep.= "WHERE ";
  $qDatDep.= "PAIIDXXX =\"{$vDatCli['paiidxxx']}\" AND ";
  $qDatDep.= "DEPIDXXX =\"{$vDatCli['depidxxx']}\" LIMIT 0,1";
  $xDatDep = f_MySql("SELECT","",$qDatDep,$xConexion01,"");
  $vDatDep = mysql_fetch_array($xDatDep);

  //Ciudad Domicilio Fiscal
  $qDatCiu = "SELECT CIUDESXX ";
  $qDatCiu.= "FROM $cAlfa.SIAI0055 ";
  $qDatCiu.= "WHERE ";
  $qDatCiu.= "PAIIDXXX =\"{$vDatCli['paiidxxx']}\" AND ";
  $qDatCiu.= "DEPIDXXX =\"{$vDatCli['depidxxx']}\" AND ";
  $qDatCiu.= "CIUIDXXX =\"{$vDatCli['ciuidxxx']}\" LIMIT 0,1";
  $xDatCiu = f_MySql("SELECT","",$qDatCiu,$xConexion01,"");
  $vDatCiu = mysql_fetch_array($xDatCiu);

  if($vDatCli['clitperx'] == "NATURAL"){
    $vDatCli['clinomxx'] = "";
    $vDatCli['clinomcx'] = "";
  } else {
    $vDatCli['clinom1x'] = "";
    $vDatCli['clinom2x'] = "";
    $vDatCli['cliape1x'] = "";
    $vDatCli['cliape2x'] = "";
  }
  ?>
  <script language = "javascript">
    document.forms['frgrm']['cTpeId'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['clitperx']) ?>";
    document.forms['frgrm']['cTdiId'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['tdiidxxx']) ?>";
    document.forms['frgrm']['cTdiDes'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTdi['tdidesxx']) ?>";
    document.forms['frgrm']['cCliId'].value		 = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['cliidxxx']) ?>";
    document.forms['frgrm']['cCliDV'].value		 = "<?php f_Digito_Verificacion(str_replace($cBuscar01,$cReempl01,$vDatCli['cliidxxx'])) ?>";
    document.forms['frgrm']['cCliNom'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['clinomxx']) ?>";
    document.forms['frgrm']['cCliNomC'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['clinomcx']) ?>";
    document.forms['frgrm']['cCliPNom'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['clinom1x']) ?>";
    document.forms['frgrm']['cCliSNom'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['clinom2x']) ?>";
    document.forms['frgrm']['cCliPApe'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['cliape1x']) ?>";
    document.forms['frgrm']['cCliSApe'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['cliape2x']) ?>";
    document.forms['frgrm']['cPaiId'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['paiidxxx']) ?>";
    document.forms['frgrm']['cPaiDes'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$cPaisDes) ?>";
    document.forms['frgrm']['cDepId'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['depidxxx']) ?>";
    document.forms['frgrm']['cDepDes'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatDep['DEPDESXX']) ?>";
    document.forms['frgrm']['cCiuId'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['ciuidxxx']) ?>";
    document.forms['frgrm']['cCiuDes'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCiu['CIUDESXX']) ?>";
    document.forms['frgrm']['cCliDir'].value	 = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['clidirxx']) ?>";
    document.forms['frgrm']['cCliCPos'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['clicposx']) ?>";
    document.forms['frgrm']['cCliTel'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['clitelxx']) ?>";
    document.forms['frgrm']['cCliMov'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['climovxx']) ?>";
    document.forms['frgrm']['cCliEma'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['cliemaxx']) ?>";
    document.forms['frgrm']['cCliApa'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['cliapaxx']) ?>";
    document.forms['frgrm']['cCliSap'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['clisapxx']) ?>";
    document.forms['frgrm']['rCheckF'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['cliprefa']) ?>";
    document.forms['frgrm']['cCliObs'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['cliobsxx']) ?>";
    document.forms['frgrm']['dFecCre'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['regfcrex']) ?>";
    document.forms['frgrm']['dHorCre'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['reghcrex']) ?>";
    document.forms['frgrm']['cEstado'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatCli['regestxx']) ?>";
  </script>
  <!-- Prendiendo Checks de Clasificacion -->
  <?php if ($vDatCli['cliclixx'] == "SI") { ?>
    <script language = "javascript">
    document.forms['frgrm']['vChCli'].checked = true;
    document.forms['frgrm']['vChCli'].value   = "SI";
    </script>
  <?php } ?>
  <?php if ($vDatCli['cliusuxx'] == "SI") { ?>
    <script language = "javascript">
    document.forms['frgrm']['vChUsu'].checked = true;
    document.forms['frgrm']['vChUsu'].value   = "SI";
    </script>
  <?php } ?>
  <?php if ($vDatCli['clidianx'] == "SI") { ?>
    <script language = "javascript">
    document.forms['frgrm']['vChDian'].checked = true;
    document.forms['frgrm']['vChDian'].value   = "SI";
    </script>
  <?php } ?>
  <?php if ($vDatCli['cliempxx'] == "SI") { ?>
    <script language = "javascript">
      document.forms['frgrm']['vChEmp'].checked = true;
      document.forms['frgrm']['vChEmp'].value   = "SI";
    </script>
  <?php } ?>
  <?php if ($vDatCli['cliotrxx'] == "SI") { ?>
    <script language = "javascript">
      document.forms['frgrm']['vChOtr'].checked = true;
      document.forms['frgrm']['vChOtr'].value   = "SI";
    </script>
  <?php } ?>
  <?php if ($vDatCli['cliconxx'] == "SI") { ?>
    <script language = "javascript">
      document.forms['frgrm']['vChCon'].checked = true;
      document.forms['frgrm']['vChCon'].value   = "SI";
    </script>
  <?php } ?>
<?php }?>

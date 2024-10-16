<?php
  namespace openComex;
/**
  * Reporte Cliente.
  * --- Descripcion: Permite Crear reportes a Cliente.
  * @author diego.cortes@openits.co
  * @package opencomex
  * @version 001
  */
  include("../../../../../financiero/libs/php/utility.php"); ?>
<html>
  <head>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language="javascript">
      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      }
      
      function fnValidaDv(){
        if(document.forms['frgrm']['cTdiId'].value == ''){
          document.forms['frgrm']['nTerDV'].value = '';
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
          break;
        }
      }
      
      function fnGenSql(){
        if (document.forms['frgrm']['vChCli'].value  == "NO" &&
            document.forms['frgrm']['vChUsu'].value == "NO" &&
            document.forms['frgrm']['vChDian'].value == "NO" &&
            document.forms['frgrm']['vChEmp'].value  == "NO" &&
            document.forms['frgrm']['vChCont'].value == "NO" &&
            document.forms['frgrm']['vChOtro'].value == "NO") {
          alert ("Debe seleccionar al menos un tipo de Clasificacion.\n");
        } else {
          var cPathUrl = 'frclireg.php?gBuscar='+document.forms['frgrm']['cBuscar'].value+
                          '&gChCli='     +document.forms['frgrm']['vChCli'].value+
                          '&gChUsu='     +document.forms['frgrm']['vChUsu'].value+
                          '&gChCont='    +document.forms['frgrm']['vChCont'].value +
                          '&gChDian='    +document.forms['frgrm']['vChDian'].value +
                          '&gChEmp='     +document.forms['frgrm']['vChEmp'].value +
                          '&gTpeId='     +document.forms['frgrm']['cTpeId'].value +
                          '&gChOtro='    +document.forms['frgrm']['vChOtro'].value +
                          '&gTdiId='     +document.forms['frgrm']['cTdiId'].value +
                          '&gCliId='     +document.forms['frgrm']['cCliId'].value +
                          '&gExcTerId='  +document.forms['frgrm']['oExcCliId'].value +
                          '&gCliNom='    +document.forms['frgrm']['cCliNom'].value +
                          '&gCliSap='    +document.forms['frgrm']['cCliSap'].value +
                          '&gExcTerNom=' +document.forms['frgrm']['oExcCliNom'].value +
                          '&gExCliSap='  +document.forms['frgrm']['oExCliSap'].value +
                          '&gCliNomC='   +document.forms['frgrm']['cCliNomC'].value +
                          '&gExcCliNomC='+document.forms['frgrm']['oExcCliNomC'].value +
                          '&gPaiId='     +document.forms['frgrm']['cPaiId'].value + 
                          '&gDepId='     +document.forms['frgrm']['cDepId'].value +
                          '&gCiuId='     +document.forms['frgrm']['cCiuId'].value +
                          '&gEstado='    +document.forms['frgrm']['cEstado'].value;
                          parent.fmpro.location = cPathUrl;
        }
      }
      
      function fnLimpiarConsulta(){
        document.forms['frgrm']['cTpeId'].value        = "";
        document.forms['frgrm']['cTdiId'].value        = "";
        document.forms['frgrm']['cTdiDes'].value       = "";
        document.forms['frgrm']['cCliId'].value        = "";
        document.forms['frgrm']['nTerDV'].value        = "";
        document.forms['frgrm']['oExcCliId'].checked   = false;
        document.forms['frgrm']['oExcCliId'].value     = "NO";
        document.forms['frgrm']['cCliNom'].value       = "";
        document.forms['frgrm']['cCliSap'].value       = "";
        document.forms['frgrm']['cCliNomC'].value      = "";
        document.forms['frgrm']['oExcCliNom'].checked  = false;
        document.forms['frgrm']['oExcCliNom'].value    = "NO";
        document.forms['frgrm']['oExCliSap'].checked   = false;
        document.forms['frgrm']['oExCliSap'].value     = "NO";
        document.forms['frgrm']['cCliNomC'].value      = "";
        document.forms['frgrm']['oExcCliNomC'].checked = false;
        document.forms['frgrm']['oExcCliNomC'].value   = "NO";
        document.forms['frgrm']['cPaiId'].value        = "";
        document.forms['frgrm']['cPaiDes'].value       = "";
        document.forms['frgrm']['cDepId'].value        = "";
        document.forms['frgrm']['cDepDes'].value       = "";
        document.forms['frgrm']['cCiuId'].value        = "";
        document.forms['frgrm']['cCiuDes'].value       = "";
        document.forms['frgrm']['cEstado'].value       = "ACTIVO";
        
        document.forms['frgrm']['vChCli'].checked      = false;
        document.forms['frgrm']['vChCli'].value    	   = "NO";
        document.forms['frgrm']['vChUsu'].checked      = false;
        document.forms['frgrm']['vChUsu'].value        = "NO";
        document.forms['frgrm']['vChDian'].checked     = false;
        document.forms['frgrm']['vChDian'].value       = "NO";
        document.forms['frgrm']['vChEmp'].checked      = false;
        document.forms['frgrm']['vChEmp'].value        = "NO";
        document.forms['frgrm']['vChCont'].checked     = false;
        document.forms['frgrm']['vChCont'].value       = "NO";
        document.forms['frgrm']['vChOtro'].checked     = false;
        document.forms['frgrm']['vChOtro'].value       = "NO";
      }  

      function fnRadio(xRadio,xValor){
        document.forms['frgrm'][xRadio].value=xValor;
      }
      
      function fnCheck(xValue,xCh){   
        if (xValue) {
          document.forms['frgrm'][xCh].value="SI";
        }else{
          document.forms['frgrm'][xCh].value="NO";
        }
      }
      
      function fnHabilita(xOpcion) {
        if (document.forms['frgrm']['cBuscar'].value == "AND") {
          switch (xOpcion) {
              default:
                if (document.forms['frgrm'][xOpcion].checked == true) {
                  document.forms['frgrm'][xOpcion].value = "SI";
                } else {
                  document.forms['frgrm'][xOpcion].value = "NO";              
                }             
              break;
          }
        }
      }
    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="580">
        <tr>
          <td>
            <fieldset>
              <legend>Reporte <?php echo $_COOKIE['kProDes'] ?></legend>
              <form name = 'frgrm' action = 'frclireg.php' method = 'post' target='fmpro'>
                <center>
                  <table border = '0' cellpadding = '0' cellspacing = '0' width='560'>  
                    <?php $nCol = f_Format_Cols(28);
                    echo $nCol;?>
                    <tr>
                      <td Class = "clase08" colspan = "10" height="25">Consultar Clientes que cumplan con:<br></td>
                      <td Class = "clase08" colspan = "9" height="25"><label><input type="radio" name = "oBuscar" onclick="javascript:fnRadio('cBuscar','OR');" onchange="javascript:fnRadio('cBuscar','OR');fnLimpiarConsulta();">Cualquier Criterio</label><br></td>
                      <td Class = "clase08" colspan = "9" height="25">
                          <input type="hidden" name="cBuscar" value="">
                          <label><input type="radio" name = "oBuscar" onclick="javascript:fnRadio('cBuscar','AND');" onchange="javascript:fnRadio('cBuscar','AND');fnLimpiarConsulta();">Todos de los Criterios</label><br>
                      </td>
                    </tr>
                    <tr>
                      <td Class = "clase08" colspan = "28">
                        <fieldset>
                          <legend>Clasificaci&oacute;n</legend>
                          <table border = '0' cellpadding = '0' cellspacing = '0' width='540'>  
                            <?php $nCol = f_Format_Cols(27);
                            echo $nCol;?>
                            <tr>
                              <td Class = "clase08" colspan = "7" height="25"><label><input type="checkbox" name = "vChCli"  value="NO" onclick="javascript:fnCheck(this.checked,'vChCli');">Cliente</label><br></td>
                              <td Class = "clase08" colspan = "7" height="25"><label><input type="checkbox" name = "vChUsu" value="NO" onclick="javascript:fnCheck(this.checked,'vChUsu');">Usuario</label><br></td>
                              <td Class = "clase08" colspan = "7" height="25"><label><input type="checkbox" name = "vChDian" value="NO" onclick="javascript:fnCheck(this.checked,'vChDian');">Usuario DIAN</label><br></td>
                              <td Class = "clase08" colspan = "6" height="25"><label><input type="checkbox" name = "vChEmp"  value="NO" onclick="javascript:fnCheck(this.checked,'vChEmp');">Empleado</label><br></t
                            </tr>
                            <tr>
                              <td Class = "clase08" colspan = "7" height="25"><label><input type="checkbox" name = "vChCont" value="NO" onclick="javascript:fnCheck(this.checked,'vChCont');" >Contacto</label><br></td>
                              <td Class = "clase08" colspan = "7" height="25"><label><input type="checkbox" name = "vChOtro" value="NO" onclick="javascript:fnCheck(this.checked,'vChOtro');">Otro</label><br></td>
                            </tr>
                          </table> 
                        </fieldset>
                      </td>
                    </tr>
                    <tr>
                      <td Class = "clase08" colspan = "28">
                        <fieldset>
                          <legend>Datos Generales</legend>
                          <table border = '0' cellpadding = '0' cellspacing = '0' width='540'>  
                            <?php $nCol = f_Format_Cols(27);
                            echo $nCol;?>
                            <tr>
                              <td Class = "clase08" colspan = "9" height="25">Tipo de Persona</td>
                                <td Class = "clase08" colspan = "18">
                                  <select class="letrase" size="1" name="cTpeId" style = "width:360">
                                    <option value = "" selected>[SELECCIONE]</option>
                                    <option value = "JURIDICA">PERSONA JURIDICA</option>
                                    <option value = "NATURAL" >PERSONA NATURAL</option>
                                  </select>
                                </td>
                            </tr>
                            <tr>
                              <td Class = "clase08" colspan = "9" height="25">
                                <a href = "javascript:document.frgrm.cTdiId.value  = '';
                                                      document.frgrm.cTdiDes.value = '';
                                                      fnValidaDv(this.value);
                                                      fnLinks('cTdiId','VALID');" id="IdTdi">Tipo de Documento</a>
                              </td>
                              <td Class = "clase08" colspan = "2">
                                <input type = 'text' Class = 'letra' style = 'width:40' name = 'cTdiId' maxlength="2"
                                        onBlur = "javascript:fnValidaDv(this.value);
                                                            this.value=this.value.toUpperCase();
                                                            fnLinks('cTdiId','VALID');
                                                            this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                        onFocus="javascript:fnValidaDv(this.value);
                                                            document.frgrm.cTdiId.value  = '';
                                                            document.frgrm.cTdiDes.value = '';
                                                            this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td Class = 'clase08' colspan = '16'>
                                <input type = 'text' Class = 'letra' style = 'width:320' name = 'cTdiDes' readonly>
                              </td>
                            </tr>
                            <tr>
                              <td Class = 'clase08' colspan = "9" height="25">No Identificaci&oacute;n</td>
                              <td Class = 'clase08' colspan = "15"> 
                                <input type = 'text' Class = 'letra' style = 'width:300' name = "cCliId" maxlength="20">
                                <input type = 'hidden' name = "nTerDV">
                              </td>
                              <td Class = 'clase08' colspan = "3">
                                <label><input type="checkbox" name = "oExcCliId" value="NO" onclick="javascript:fnCheck(this.checked,'oExcCliId');">Exacto</label>
                              </td>
                            </tr>
                            <tr>
                              <td Class = "clase08" colspan = "9" height="25">Nombre o Razon Social</td>
                              <td Class = "clase08" colspan = "15">
                                <input type = "text" Class = "letra" name = "cCliNom" style = "width:300" maxlength="100">
                              </td>
                              <td Class = 'clase08' colspan = "3">
                                <label><input type="checkbox" name = "oExcCliNom" value="NO" onclick="javascript:fnCheck(this.checked,'oExcCliNom');">Exacto</label>
                              </td>
                            </tr>
                            <tr>
                              <td Class = "clase08" colspan = "9" height="25">Nombre Comercial</td>
                              <td Class = "clase08" colspan = "15">
                                <input type = "text" Class = "letra" name = "cCliNomC" style = "width:300" maxlength="100">
                              </td>
                              <td Class = 'clase08' colspan = "3">
                                <label><input type="checkbox" name = "oExcCliNomC" value="NO" onclick="javascript:fnCheck(this.checked,'oExcCliNomC');">Exacto</label>
                              </td>
                            </tr>
                            <tr>
                              <td Class = "clase08" colspan = "9" height="25">
                                <a href = "javascript:document.forms['frgrm']['cPaiId'].value  = '';
                                                      document.forms['frgrm']['cPaiDes'].value = '';
                                                      document.forms['frgrm']['cDepId'].value  = '';
                                                      document.forms['frgrm']['cDepDes'].value = '';
                                                      document.forms['frgrm']['cCiuId'].value  = '';
                                                      document.forms['frgrm']['cCiuDes'].value = '';
                                                      fnLinks('cPaiId','VALID'); " id="IdPai">Pais Domicilio</a>
                              </td>
                              <td Class = "clase08" colspan = "2">
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
                              <td Class = "clase08" colspan = "16">
                                <input type = 'text' Class = 'letra' style = 'width:320' name = 'cPaiDes' readonly>
                              </td>
                            </tr>
                            <tr>
                              <td Class = "clase08" colspan = "9" height="25">
                                <a href = "javascript:document.forms['frgrm']['cDepId'].value  = '';
                                                      document.forms['frgrm']['cDepDes'].value = '';
                                                      document.forms['frgrm']['cCiuId'].value  = '';
                                                      document.forms['frgrm']['cCiuDes'].value = '';
                                                      fnLinks('cDepId','WINDOW')" id="IdDep">Departamento Domicilio</a>
                              </td>
                              <td Class = "clase08" colspan = "2">
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
                              <td Class = "clase08" colspan = "16">
                                <input type = 'text' Class = 'letra' style = 'width:320' name = 'cDepDes' readonly>
                              </td>
                            </tr>
                            <tr>
                              <td Class = "clase08" colspan = "9" height="25">
                                <a href = "javascript:document.forms['frgrm']['cCiuId'].value  = '';
                                                      document.forms['frgrm']['cCiuDes'].value = '';
                                                      fnLinks('cCiuId','WINDOW')" id="IdCiu">Ciudad Domicilio</a>
                              </td>
                              <td Class = "clase08" colspan = "2">
                                <input type = 'text' Class = 'letra' style = 'width:40' name = 'cCiuId' maxlength="10"
                                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                                          fnLinks('cCiuId','VALID');
                                                          this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                        onFocus = "javascript:document.forms['frgrm']['cCiuId'].value  = '';
                                                          document.forms['frgrm']['cCiuDes'].value = '';
                                                          this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                              </td>
                              <td Class = "clase08" colspan = "16">
                                <input type = 'text' Class = 'letra' style = 'width:320' name = 'cCiuDes' readonly>
                              </td>
                            </tr>
                            <tr>
                              <td Class = "clase08" colspan = "9" height="25">Codigo SAP</td>
                              <td Class = "clase08" colspan = "15">
                                <input type = "text" Class = "letra" name = "cCliSap" style = "width:300" maxlength="100">
                              </td>
                              <td Class = 'clase08' colspan = "3">
                                <label><input type="checkbox" name = "oExCliSap" value="NO" onclick="javascript:fnCheck(this.checked,'oExCliSap');">Exacto</label>
                              </td>
                            </tr>
                            <tr>
                              <td Class = "clase08" colspan = "9" height="25">Estado</td>
                              <td Class = "clase08" colspan = "18">  
                                <select class="letrase" size="1" name="cEstado" style = "width:360">
                                  <option value = "ACTIVO" selected="">ACTIVO</option>
                                  <option value = "INACTIVO">INACTIVO</option>
                                  <option value = "AMBOS">AMBOS</option>
                                </select>
                              </td>
                            </tr>   
                          </table>
                        </fieldset>
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
      <table border="0" cellpadding="0" cellspacing="0" width="580">
        <tr height="21">
          <td width="307" height="21"></td>
          <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_remove_bg.gif" style="cursor:pointer" onClick = "javascript:document.forms['frgrm']['oBuscar'][0].checked = true;fnRadio('cBuscar','OR');fnLimpiarConsulta();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Limpiar</td>
          <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:fnGenSql()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
          <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
        </tr>
      </table>
    </center>
    <script language = "javascript">
      document.forms['frgrm']['oBuscar'][0].checked = true;
      fnRadio('cBuscar','OR');
      fnLimpiarConsulta();
    </script>
    <br>
  </body>
</html>
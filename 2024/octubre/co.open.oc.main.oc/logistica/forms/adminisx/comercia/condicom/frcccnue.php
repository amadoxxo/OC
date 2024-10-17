<?php
  namespace openComex;
/**
 * Proceso Condicones Comerciales.
 * --- Descripcion: Permite Crear una Nueva condición.
 * @author Diego Fernando Cortes Rojas <diego.cortes@openits.co>
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

      function fnHideOtro(){
        if (document.forms['frgrm']['cCondCoIn'].value != "OTRO") {
          document.forms['frgrm']['cCondCoOt'].disabled = true;
          document.forms['frgrm']['cCondCoOt'].value    = "";
        } else {
          document.forms['frgrm']['cCondCoOt'].disabled = false;
        }
      }

      function fnLinks(xLink,xSwitch) {
        var zX    = screen.width;
				var zY    = screen.height;
        switch (xLink) {
          case "cCliId":
            if (xSwitch == "VALID") {
              var zRuta  = "frccc150.php?gWhat=VALID&gFunction=cCliId&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frccc150.php?gWhat=WINDOW&gFunction=cCliId&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cCliNom":
            if (xSwitch == "VALID") {
              var zRuta  = "frccc150.php?gWhat=VALID&gFunction=cCliNom&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frccc150.php?gWhat=WINDOW&gFunction=cCliNom&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
        }
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
      <table border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo $_COOKIE['kMenDes'] ?></legend>
              <form name = 'frgrm' action = 'frcccgra.php' method = 'post' target='fmpro'>
                <center>
                  <table border="0" cellpadding="0" cellspacing="0" width="600">
                    <?php $nCol = f_Format_Cols(30); echo $nCol; ?>
                    <tr>
                      <td class="clase08" colspan="6">No. Oferta Comercial<br>
                        <input type = 'text' Class = 'letra' style = 'width:120' name = "cCCoId" maxlength="15"
                                onBlur = "this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td Class = "clase08" colspan = "5">
                        <a href = "javascript:document.forms['frgrm']['cCliId'].value = '';
                                            document.forms['frgrm']['cCliNom'].value = '';
                                            document.forms['frgrm']['cCliDV'].value  = '';
                                            document.forms['frgrm']['cCliSap'].value  = '';
                                            fnLinks('cCliId','VALID')" id = "lCliId">Nit</a><br>
                        <input type = "text" Class = "letra" style = "width:100;text-align:center" name = "cCliId"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                    fnLinks('cCliId','VALID');
                                                    this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus="javascript:document.forms['frgrm']['cCliId'].value  ='';
                                                  document.forms['frgrm']['cCliNom'].value = '';
                                                  document.forms['frgrm']['cCliDV'].value  = '';
                                                  document.forms['frgrm']['cCliSap'].value  = '';
                                                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class="clase08" colspan = "1">Dv<br>
                      <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cCliDV" readonly>
                      </td>
                      <td class="clase08" colspan = "14">Cliente<br>
                      <input type = "text" Class = "letra" style = "width:280" name = "cCliNom" id="cCliNom"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cCliNom','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus="javascript:document.forms['frgrm']['cCliId'].value  ='';
                                              document.forms['frgrm']['cCliNom'].value = '';
                                              document.forms['frgrm']['cCliDV'].value  = '';
                                              document.forms['frgrm']['cCliSap'].value  = '';
                                              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class="clase08" colspan = "4">C&oacute;digo SAP<br>
                        <input type = 'text' Class = 'letra' style = 'width:80' name = "cCliSap" readonly>
                      </td>
                    </tr>
                    <tr>
                      <td class="clase08" colspan = "7">Tipo<br>
                        <select class="letrase" size="1" name="cCondCoTip" style = "width:140; height:19;">
                          <option value = "" selected>[SELECCIONE]</option>
                          <option value = "OFERTA_COMERCIAL">OFERTA COMERCIAL</option>
                          <option value = "CONTRATO" >CONTRATO</option>
                          <option value = "ALCANCE" >ALCANCE</option>
                          <option value = "OTRO_SI" >OTRO SI</option>
                          <option value = "OTRO" >OTRO</option>
                        </select>
                      </td>
                      <td class="clase08" colspan = "7">Cierre Facturaci&oacute;n<br>
                        <select class="letrase" size="1" name="cCondCoCie" style = "width:140; height:19;">
                          <option value = "" selected>[SELECCIONE]</option>
                          <?php for ($i=1; $i <= 31; $i++) { 
                            ?>
                            <option value="<?php echo $i ?>"><?php echo $i;?></option>
                            <?php
                          }?>
                        </select>
                      </td>
                      <td class="clase08" colspan = "8">
                        <a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">Fecha Vigencia Desde</a><br>
                        <input type="text" name="dDesde" style = "width:160;text-align:center">
                      </td>
                      <td class="clase08" colspan = "8">
                        <a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">Fecha Vigencia Hasta</a><br>
                        <input type="text" name="dHasta" style = "width:160;text-align:center">
                      </td>
                    </tr>
                    <tr>
                      <td class="clase08" colspan = "10">Tipo Incremento<br>
                        <select class="letrase" size="1" name="cCondCoIn" style = "width:200;" onchange = "javascript:fnHideOtro();">
                          <option value = "" selected>[SELECCIONE]</option>
                          <option value = "SMMLV">SMMLV</option>
                          <option value = "IPC" >IPC</option>
                          <option value = "IPC+1" >IPC+1</option>
                          <option value = "IPC+2" >IPC+2</option>
                          <option value = "OTRO" >OTRO</option>
                        </select>
                      </td>
                      <td class="clase08" colspan = "20">Especif&iacute;que<br>
                        <input type = 'text' Class = 'letra' style = 'width:400' name = "cCondCoOt" disabled
                              onBlur = "this.value=this.value.toUpperCase();
                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                              onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                    </tr>
                    <tr>
                      <td class="clase08" colspan="30">Observaciones<br>
                        <textarea name="cCondCoObs" id="" cols="30" rows="5" style="width:600"></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td Class = "clase08" colspan = "6">Fecha<br>
                        <input type = "text" Class = "letra"  style = "width:120;text-align:center"  name = "dFecCre"  value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "6">Hora<br>
                        <input type = 'text' Class = 'letra' style = "width:120;text-align:center" name = "dHorCre"  value = "<?php echo date('H:i:s') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "6">Modificado<br>
                        <input type = "text" Class = "letra"  style = "width:120;text-align:center"  name = "dFecMod"  value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "6">Hora<br>
                        <input type = "text" Class = "letra"  style = "width:120;text-align:center"  name = "dHorMod"  value = "<?php echo date('H:i:s') ?>" readonly>
                      </td>
                      <td Class = "clase08" colspan = "6">Estado<br>
                        <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cEstado"  value = "ACTIVO"
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
      <table border="0" cellpadding="0" cellspacing="0" width="600">
        <tr height="21">
          <?php switch ($_COOKIE['kModo']) {
            case "VER": ?>
              <td width="509" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
              <?php 
            break;
            default: ?>
              <td width="418" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:fnGuardar()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
              <?php 
            break;
          } ?>
        </tr>
      </table>
    </center>
    <br>
  </body>
    <!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
    <?php
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      ?>
      <script languaje = "javascript">
      document.forms['frgrm']['cEstado'].readOnly  = true;
      </script>
      <?php
    break;
    case "EDITAR":
      fnCargaData($cCCoId);
      ?>
      <script languaje = "javascript">
      if(document.forms['frgrm']['cCondCoIn'].value != "OTRO"){
          document.forms['frgrm']['cCondCoOt'].disabled = true;
      } else {
        document.forms['frgrm']['cCondCoOt'].disabled = false;
      }
      document.forms['frgrm']['cCCoId'].readOnly = true;
      </script>
      <?php 
    break;
    case "VER":
      fnCargaData($cCCoId);
      ?>
      <script languaje = "javascript">
        for (x=0;x<document.forms['frgrm'].elements.length;x++) {
          document.forms['frgrm'].elements[x].readOnly         = true;
          document.forms['frgrm'].elements[x].onfocus          = "";
          document.forms['frgrm'].elements[x].onblur           = "";
        }
        document.getElementById('lCliId').disabled         = true;
        document.getElementById('id_href_dDesde').disabled = true;
        document.getElementById('id_href_dHasta').disabled = true;
        document.getElementById('lCliId').href             = 'javascript: alert("No permitido")';
        document.getElementById('id_href_dDesde').href     = 'javascript: alert("No permitido")';
        document.getElementById('id_href_dHasta').href     = 'javascript: alert("No permitido")';

        document.forms['frgrm']['cCondCoTip'].disabled = true;
        document.forms['frgrm']['cCondCoCie'].disabled = true;
        document.forms['frgrm']['cCondCoIn'].disabled  = true;
      </script>
    <?php break;
  } ?>

  <?php function fnCargaData($cCCoId) {
    global $cAlfa; global $xConexion01;

    $qCondiCom  = "SELECT * ";
    $qCondiCom .= "FROM $cAlfa.lpar0151 ";
    $qCondiCom .= "WHERE ";
    $qCondiCom .= "ccoidocx = \"$cCCoId\" LIMIT 0,1";
    $xCondiCom  = f_MySql("SELECT","",$qCondiCom,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qCondiCom."~".mysql_num_rows($xCondiCom)."~".mysql_error($xConexion01));
    $vCondiCom  = mysql_fetch_array($xCondiCom);

    $qClientes  = "SELECT cliidxxx, cliidxxx, clinomxx, clisapxx ";
    $qClientes .= "FROM $cAlfa.lpar0150 ";
    $qClientes .= "WHERE ";
    $qClientes .= "cliidxxx = \"{$vCondiCom['cliidxxx']}\" LIMIT 0,1";
    $xClientes  = f_MySql("SELECT","",$qClientes,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qClientes."~".mysql_num_rows($xClientes)."~".mysql_error($xConexion01));
    $vClientes  = mysql_fetch_array($xClientes);

    ?>
    <script language = "javascript">
      document.forms['frgrm']['cCCoId'].value     = "<?php echo $vCondiCom['ccoidocx'] ?>";
      document.forms['frgrm']['cCliId'].value     = "<?php echo $vClientes['cliidxxx'] ?>";

      document.forms['frgrm']['cCliDV'].value     = "<?php echo gendv($vClientes['cliidxxx'])?>";

      document.forms['frgrm']['cCliNom'].value    = "<?php echo $vClientes['clinomxx'] ?>";
      document.forms['frgrm']['cCliSap'].value    = "<?php echo $vClientes['clisapxx'] ?>";
      document.forms['frgrm']['cCondCoTip'].value = "<?php echo $vCondiCom['ccotipxx'] ?>";
      document.forms['frgrm']['cCondCoCie'].value = "<?php echo $vCondiCom['ccociexx'] ?>";
      document.forms['frgrm']['dDesde'].value     = "<?php echo $vCondiCom['ccofvdxx'] ?>";
      document.forms['frgrm']['dHasta'].value     = "<?php echo $vCondiCom['ccofvhxx'] ?>";
      document.forms['frgrm']['cCondCoIn'].value  = "<?php echo $vCondiCom['ccoincxx'] ?>";
      document.forms['frgrm']['cCondCoOt'].value  = "<?php echo $vCondiCom['ccoincox'] ?>";
      document.forms['frgrm']['cCondCoObs'].value = "<?php echo $vCondiCom['ccoobsxx'] ?>";
      document.forms['frgrm']['dFecCre'].value    = "<?php echo $vCondiCom['regfcrex'] ?>";
      document.forms['frgrm']['dHorCre'].value    = "<?php echo $vCondiCom['reghcrex'] ?>";
      document.forms['frgrm']['dFecMod'].value    = "<?php echo $vCondiCom['regfmodx'] ?>";
      document.forms['frgrm']['dHorMod'].value    = "<?php echo $vCondiCom['reghmodx'] ?>";
      document.forms['frgrm']['cEstado'].value    = "<?php echo $vCondiCom['regestxx'] ?>";
    </script>
    <?php
  } ?>
</html>
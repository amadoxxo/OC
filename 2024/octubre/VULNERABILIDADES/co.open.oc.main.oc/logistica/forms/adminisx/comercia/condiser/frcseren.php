<?php
  namespace openComex;
  /**
   * Imprimir Condiciones de Servicios.
   * --- Descripcion:  Este programa permite realizar imprimir Condiciones de Servicios en PDF.
   * @author Diego Fernando Cortes Rojas <diego.cortes@openits.co>
   * @package openComex
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
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language="javascript">
      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      }

      function fnLinks(xLink,xSwitch) {
        var zX    = screen.width;
        var zY    = screen.height;
        switch (xLink) {
          // Cliente
          case "cCliId":
            if (xSwitch == "VALID") {
              var zRuta  = "frcse150.php?gWhat=VALID&gFunction=cCliId&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcse150.php?gWhat=WINDOW&gFunction=cCliId&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cCliNom":
            if (xSwitch == "VALID") {
              var zRuta  = "frcse150.php?gWhat=VALID&gFunction=cCliNom&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (zX-600)/2;
              var zNy     = (zY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frcse150.php?gWhat=WINDOW&gFunction=cCliNom&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cCcoIdOc":
            if (document.forms['frgrm']['cCliId'].value != "") {
              if (xSwitch == "VALID") {
                var zRuta  = "frcse151.php?gWhat=VALID" + 
                                          "&gFunction=cCcoIdOc" + 
                                          "&gCcoIdOc="+document.forms['frgrm']['cCcoIdOc'].value.toUpperCase() +
                                          "&gCliId="+document.forms['frgrm']['cCliId'].value;
                parent.fmpro.location = zRuta;
              } else {
                var zNx     = (zX-600)/2;
                var zNy     = (zY-250)/2;
                var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
                var zRuta   = "frcse151.php?gWhat=WINDOW" +
                                          "&gFunction=cCcoIdOc" + 
                                          "&gCcoIdOc="+document.forms['frgrm']['cCcoIdOc'].value.toUpperCase() +
                                          "&gCliId="+document.forms['frgrm']['cCliId'].value;
                zWindow = window.open(zRuta,"zWindow",zWinPro);
                zWindow.focus();
              }
            } else {
              alert('Debe seleccionar el cliente para poder cosultar las condiciones comerciales,\nVerifique.');
            }
          break;
        }
      }

      function fnGenerar() {
				if (document.forms['frgrm']['cCliId'].value != "") {
          var zX      = screen.width;
          var zY      = screen.height;
          var zNx     = (zX-1100)/2;
          var zNy     = (zY-700)/2;
          var zWinPro = "width=1100,scrollbars=1,height=700,resizable=YES,left="+zNx+",top="+zNy;
          var zRuta = 'frcserep.php?cCliId='+document.forms['frgrm']['cCliId'].value+
                                  '&cCliNom='+document.forms['frgrm']['cCliNom'].value+
                                  '&cCliSap='+document.forms['frgrm']['cCliSap'].value+
                                  '&cCliDV='+document.forms['frgrm']['cCliDV'].value+
                                  '&cCcoIdOc='+document.forms['frgrm']['cCcoIdOc'].value+
                                  '&cEstado='+document.forms['frgrm']['cEstado'].value;
          zWindow = window.open(zRuta,'zWinTrp',zWinPro);
				} else {
          alert("Debe Selecionar un Cliente, Verifique.")
				}
			}
    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="420">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo $_COOKIE['kMenDes']  ?></legend>
              <form name = 'frgrm' action = 'frsecgra.php' method = 'post' target='fmpro'>
                <center>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='420'>
                      <?php $cCol = f_Format_Cols(21);
                      echo $cCol;?>
                      <tr>
                        <td Class = 'clase08' colspan = '6'>
                          <p style = "width:126;">Estado</p>
                        </td>
                        <td Class = "clase08" colspan = "7">
                          <select name="cEstado" id="cEstado" style = "width:147;">
                            <option value="ACTIVO">ACTIVO</option>
                            <option value="INACTIVO">INACTIVO</option>
                            <option value="TODOS">TODOS</option>
                          </select>
                        </td>
                        <td Class = "clase08" colspan = "8"></td>
                      </tr>
                      <tr>
                        <td Class = 'clase08' colspan = '6'>
                          <p style = "width:126;"><a href = "javascript:document.forms['frgrm']['cCliId'].value = '';
                                            document.forms['frgrm']['cCliNom'].value  = '';
                                            document.forms['frgrm']['cCliDV'].value   = '';
                                            document.forms['frgrm']['cCliSap'].value  = '';
                                            document.forms['frgrm']['cCcoIdOc'].value = '';
                                            fnLinks('cCliId','VALID')" id = "lCliId">Cliente</a><br></p>
                        </td>
                        <td Class = "clase08" colspan = "6">
                          <input type = "text" Class = "letra" name="cCliId"  style = "width:126;" name = 'cCliId' maxlength="20"
                                onBlur = "javascript:this.value=this.value.toUpperCase();
                                                    fnLinks('cCliId','VALID');
                                                    this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                onFocus = "javascript:document.forms['frgrm']['cCliId'].value   = '';
                                                      document.forms['frgrm']['cCliNom'].value  = '';
                                                      document.forms['frgrm']['cCliDV'].value   = '';
                                                      document.forms['frgrm']['cCliSap'].value  = '';
                                                      document.forms['frgrm']['cCcoIdOc'].value = '';
                                                      this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                        <td Class = "clase08" colspan = "1">
                          <input type = "text" Class = "letra" name="cCliDV"  style = "width:21;" >
                        </td>
                        <td Class = "clase08" colspan = "8">
                          <input type = "text" Class = "letra" name="cCliNom" style = "width:168;" name = "cCliNom"
                                  onBlur = "javascript:this.value=this.value.toUpperCase();
                                                        fnLinks('cCliNom','VALID');
                                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                  onFocus="javascript:document.forms['frgrm']['cCliId'].value   ='';
                                                      document.forms['frgrm']['cCliNom'].value  = '';
                                                      document.forms['frgrm']['cCliDV'].value   = '';
                                                      document.forms['frgrm']['cCliSap'].value  = '';
                                                      document.forms['frgrm']['cCcoIdOc'].value = '';
                                                      this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                        <td Class = "clase08" colspan = "0">
                          <input type = "hidden" name="cCliSap">
                        </td>
                      </tr>
                      <tr>
                        <td Class = 'clase08' colspan = '6'>
                          <p style = "width:126;"><a href = "javascript:document.forms['frgrm']['cCcoIdOc'].value = '';
                                              fnLinks('cCcoIdOc','VALID')" id="idCcoIdOf">Condici&oacute;n Comercial</a></p>
                        </td>
                        <td Class = "clase08" colspan = "7">
                          <input type = "text" Class = "letra"  style = "width:147;" name = 'cCcoIdOc'
                            onBlur = "javascript:fnLinks('cCcoIdOc','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus="javascript:document.forms['frgrm']['cCcoIdOc'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                        <td Class = "clase08" colspan = "8"></td>
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
      <table border="0" cellpadding="0" cellspacing="0" width="420">
        <tr height="21">
          <td width="238" height="21"></td>
          <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:fnGenerar()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
          <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
        </tr>
      </table>
    </center>
  </body>
</html>


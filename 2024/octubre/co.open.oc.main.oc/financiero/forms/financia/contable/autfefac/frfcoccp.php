<?php
  namespace openComex;
  /**
   * Pegar Prefacturas
   * --- Descripcion: Permite Pegar Prefacturas por su Codigo, en Cada Linea de Texto, Separadas por un Enter.
   * @author Cristian Camilo Segura V <cristian.segura@open-eb.co>
   * @package Opencomex
   */

  include("../../../../libs/php/utility.php"); 
  
  if ($gFunction != "" && $gSecuencia != "") { ?>
  <html>
    <head>
    <title>Pegar PREFACTURADAS</title>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
    <script type="text/javascript">
      function fnPegar(){
        if(document.forms['frgrm']['cMemo'].value != ""){
          document.forms['frgrm'].submit();    
        }else{
          alert('Debe Ingresar el (los) Consecutivo(s) de la(s) PREFACTURA(S), Verifique.');
        }
      }
    </script>
    
    </head>
    <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
    <center>
      <table border = "0" cellpadding= "0" cellspacing= "0" width = "500">
        <tr>
          <td>
            <fieldset>
              <legend><b>Pegar solo el Codigo de la Prefactura</b></legend>
              <form name = "frgrm" action = "frafcocg.php" method = "post" target = "framepro">
                <input type="hidden" name="cModo"      value="PEGARPREFACTURADA">
                <input type="hidden" name="nSecuencia" value="<?php echo $gSecuencia ?>">
                <input type="hidden" name="cPreAnio"    value="<?php echo $gPreAnio ?>">
                <input type="hidden" name="cProvien"    value="<?php echo $gProvien ?>">
                <table border = "0" cellpadding = "0" cellspacing = "0" width = "480">
                  <tr>
                    <td Class = "name">
                      <textarea name = "cMemo" style="width:480;height:150"></textarea>
                    </td>
                  </tr>
                </table>
                </center>
              </form>
            </fieldset>
            <center>
            <table border="0" cellpadding="0" cellspacing="0" width="500">
              <tr height="21">
                <td width="318" height="21"></td>
                <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:fnPegar();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aceptar</td>
                <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:parent.window.close()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
              </tr>
            </table>
          </center>
          </td>
        </tr>
      </table>
    </center>
  </body>
</html>
<?php } else {
  f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
} ?>
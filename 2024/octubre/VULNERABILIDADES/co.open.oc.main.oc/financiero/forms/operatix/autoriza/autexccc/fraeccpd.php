<?php
  namespace openComex;
  /**
   * Pegar Dos
   * --- Descripcion: Permite Pegar Dos Desde un Archivo Externo
   * @author Johana Arboleda Ramos <johana.arboleda@opentecnologia.com.co>
   * @version 001
   */

  include("../../../../libs/php/utility.php"); 
  
  if ($gFunction != "" && $gSecuencia != "") { ?>
  <html>
    <head>
    <title>Pegar Do</title>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
    <script type="text/javascript">
      function f_Pegar(){
        if(document.forms['frgrm']['cMemo'].value != ""){
          document.forms['frgrm'].submit();    
        }else{
          alert('Debe Ingresar los Numero de Do, Verifique.');
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
              <legend><b>Pegar solo el Numero de DO</b></legend>
              <form name = "frgrm" action = "fraec20g.php" method = "post" target = "framepro">
                <input type="hidden" name="cModo"      value="PEGARDO">
                <input type="hidden" name="nSecuencia" value="<?php echo $gSecuencia ?>">
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
                <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:f_Pegar();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aceptar</td>
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
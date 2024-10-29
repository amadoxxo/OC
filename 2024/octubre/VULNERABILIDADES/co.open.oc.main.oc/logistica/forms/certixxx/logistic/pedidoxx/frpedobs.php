<?php
  namespace openComex;
  /**
   * Observaciones Acciones del Tracking de Pedido.
   * --- Descripcion: Permite guardar la observacion para las diferentes acciones del tracking de Pedido.
   * @author Juan Jose Trujillo Ch. juan.trujillo@openits.co
   * @package openComex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");

  $cTitulo = "";
  switch($_COOKIE['kModo']) {
    case "ANULAR":
      $cTitulo = "Anular Pedido";
    break;
    case "DEVOLUCION":
      $cTitulo = "Devolucion Pedido";
    break;
    default:
      // No hace nada
    break;
  }

  if (!empty($gComId) && !empty($gComCod) && !empty($gComCsc) && !empty($gComCsc2) && !empty($gRegFCre) && !empty($gRegEst)) { ?>
    <html>
      <head>
      <title><?php echo utf8_decode($cTitulo) ?></title>
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
      <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
      <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
      <script type="text/javascript">
        function fnGuardar(){
          window.opener.document.forms['frestado']['gComId'].value        = document.forms['frgrm']['gComId'].value;
          window.opener.document.forms['frestado']['gComCod'].value       = document.forms['frgrm']['gComCod'].value;
          window.opener.document.forms['frestado']['gComPre'].value       = document.forms['frgrm']['gComPre'].value;
          window.opener.document.forms['frestado']['gComCsc'].value       = document.forms['frgrm']['gComCsc'].value;
          window.opener.document.forms['frestado']['gComCsc2'].value      = document.forms['frgrm']['gComCsc2'].value;
          window.opener.document.forms['frestado']['gRegEst'].value       = document.forms['frgrm']['gRegEst'].value;
          window.opener.document.forms['frestado']['gObservacion'].value  = document.forms['frgrm']['gObservacion'].value;
          window.opener.document.forms['frestado']['cAnio'].value         = document.forms['frgrm']['cAnio'].value;
          window.opener.document.forms['frestado'].action = "frpedgra.php";
          window.opener.document.forms['frestado'].submit();
          window.close();
        }
      </script>
      </head>
      <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
      <center>
        <table border = "0" cellpadding= "0" cellspacing= "0" width = "300">
          <tr>
            <td>
              <fieldset>
                <legend>Observacion <?php echo utf8_decode($cTitulo) ?> <b><?php echo $gComId."-".$gComCod."-".$gComCsc ?></b></legend>
                <form name = "frgrm" action = "frpedgra.php" method = "post">
                  <input type = "hidden" name = "gComId"   value = <?php echo $gComId ?>>
                  <input type = "hidden" name = "gComCod"  value = <?php echo $gComCod ?>>
                  <input type = "hidden" name = "gComPre"  value = <?php echo $gComPre ?>>
                  <input type = "hidden" name = "gComCsc"  value = <?php echo $gComCsc ?>>
                  <input type = "hidden" name = "gComCsc2" value = <?php echo $gComCsc2 ?>>
                  <input type = "hidden" name = "gRegEst"  value = <?php echo $gRegEst ?>>
                  <input type = "hidden" name = "cAnio"    value = <?php echo substr($gRegFCre, 0, 4) ?>>
                  <center>
                  <table border = "0" cellpadding = "0" cellspacing = "0" width = "350">
                    <tr>
                      <td Class = "name">
                        <textarea name="gObservacion" style = "width:350; height:90" maxlength = "100"></textarea>
                      </td>
                    </tr>
                  </table>
                  </center>
                </form>
              </fieldset>
              <center>
              <table border="0" cellpadding="0" cellspacing="0" width="350">
                <tr height="21">
                  <td width="168" height="21"></td>
                  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:fnGuardar();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
                  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:window.close()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
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
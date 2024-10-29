<?php
  namespace openComex;
  /**
   * Desbloqueo Comprobante.
   * --- Descripcion: Permite Desbloquear Matriz de Insumos Facturables.
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
    <script language="javascript">
      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      }
    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
    <table border ="0" cellpadding="0" cellspacing="0" width="300">
        <tr>
          <td>
            <fieldset>
              <legend>Desbloqueo</legend>
              <form name = 'frgrm' action = 'frmifgra.php' method = 'post' target='fmpro'>
                <input type = "hidden" name = "cMifId"    value = "<?php echo $gMifId ?>">
                <input type = "hidden" name = "cAnio"     value = "<?php echo $cAnio ?>">
                <input type = "hidden" name = "dMifDesde" value = "<?php echo $gDesde ?>">
                <input type = "hidden" name = "dMifHasta" value = "<?php echo $gHasta ?>">

                <table border = '0' cellpadding = '0' cellspacing = '0' width='380'>
                  <?php $nCol = f_Format_Cols(19);
                  echo $nCol;?>
                  <tr>
                    <td Class = "clase08" colspan = "4">Prefijo</a><br>
                      <input type = 'text' Class = 'letra' style = 'width:100' name = "cComPre" value = "<?php echo $gComPre ?>" readonly>
                    </td>
                    <td Class = "clase08" colspan = "4">No M.I.F</a><br>
                      <input type = 'text' Class = 'letra' style = 'width:100' name = "cComCsc" value = "<?php echo $gComCsc ?>" readonly>
                    </td>
                    <td class="clase08" colspan = "4">
                      <a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">Fecha Desde</a><br>
                      <input type="text" name="dDesde" style = "width:100;height:15;text-align:center">
                    </td>
                    <td class="clase08" colspan = "4">
                      <a href='javascript:show_calendar("frgrm.dHasta");' id="id_href_dHasta">Fecha Hasta</a><br>
                      <input type="text" name="dHasta" style = "width:100;height:15;text-align:center">
                    </td>
                  </tr>
                </table>
              </form>
            </fieldset>
          </td>
        </tr>
    </table>
    </center>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="380">
        <tr height="21">
          <?php switch ($_COOKIE['kModo']) {
            case "VER": ?>
              <td width="289" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
            default: ?>
              <td width="198" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:document.forms['frgrm'].submit();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
          } ?>
        </tr>
      </table>
    </center>
    <script languaje = "javascript">
      document.forms['frgrm']['cComPre'].readOnly  = true;
      document.forms['frgrm']['cComCsc'].readOnly  = true;
    </script>
  </body>
</html>

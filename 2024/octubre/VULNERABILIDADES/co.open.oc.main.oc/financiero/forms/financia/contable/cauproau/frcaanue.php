<?php
  namespace openComex;
  /**
   * Ajuste Anulacion Automatico.
   * --- Descripcion: Permite guardar Ajuste Anulacion Automatico.
   * @author Camilo Dulce <camilo.dulce@open-eb.co>
   * @package openComex
   */

  include("../../../../libs/php/utility.php"); 
  
  if (!empty($gComId) && !empty($gComCod) && !empty($gComCsc) && !empty($gComCsc2) && !empty($gRegEst)) { ?>
  <html>
    <head>
    <title>Ajuste de Anulacion Automatica</title>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
    <script type="text/javascript">
      function fnGuardar(){
        document.forms['frgrm'].submit();
      }
    </script>
    </head>
    <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
    <center>
      <table border = "0" cellpadding= "0" cellspacing= "0" width = "340">
        <tr>
          <td>
            <fieldset>
              <legend>Ajuste de Anulacion Automatica: <b><?php echo $gComId."-".$gComCod."-".$gComCsc ?></b></legend>
              <form name = "frgrm" action = "frcpagra.php" method = "post" target="fmpro">
                <input type="hidden" name="gComId"   value=<?php echo $gComId ?>>
                <input type="hidden" name="gComCod"  value=<?php echo $gComCod ?>>
                <input type="hidden" name="gComCsc"  value=<?php echo $gComCsc ?>>
                <input type="hidden" name="gComCsc2" value=<?php echo $gComCsc2 ?>>
                <input type="hidden" name="gComFec"  value=<?php echo $gComFec ?>>
                <input type="hidden" name="gRegEst"  value=<?php echo $gRegEst ?>>
                <center>
                <table border = "0" cellpadding = "0" cellspacing = "0" width = "340">
                  <?php echo f_Columnas(17,20); ?>
                  <tr>
                    <td Class = "name" colspan = "9">Periodo<br>
                      <select Class = "letra" name = "cAno" style = "width:180">
                      <?php
                      for ($nAno=$vSysStr['financiero_ano_instalacion_modulo']; $nAno<=date("Y"); $nAno++) { ?>
                        <option value="<?php echo $nAno ?>"><?php echo $nAno ?></option><?php
                      }?>  
                      </select>
                    </td>
                    <td Class = "name" colspan = "8">Mes<br>
                      <select Class = "letra" name = "cMes" style = "width:160">
                      <?php
                      for ($nMes=1; $nMes<=12; $nMes++) { ?>  
                        <option value="<?php echo str_pad($nMes,2,'0',STR_PAD_LEFT); ?>"><?php echo str_pad($nMes,2,'0',STR_PAD_LEFT); ?></option><?php
                      }?>
                      </select>
                    </td>
                  </tr>
                </table>
                </center>
              </form>
            </fieldset>
            <center>
            <table border="0" cellpadding="0" cellspacing="0" width="340">
              <tr height="21">
                <td width="158" height="21"></td>
                <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:fnGuardar();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
                <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:parent.window.close()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
              </tr>
            </table>
          </center>
          </td>
        </tr>
      </table>
    </center>
  </body>
  <script languaje = "javascript">
    document.forms['frgrm']['cAno'].value = "<?php echo date("Y") ?>";
    document.forms['frgrm']['cMes'].value = "<?php echo date("m") ?>";
  </script>
</html>
<?php } else {
  f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
} ?>
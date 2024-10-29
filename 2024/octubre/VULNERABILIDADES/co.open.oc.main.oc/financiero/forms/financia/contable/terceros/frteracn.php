<?php
  namespace openComex;
/**
 * Cargue Correos Notificacion Rechazo Revisor Fiscal.
 * Descripcion: Permite Cambiar Correos Notificacion Rechazo Revisor Fiscal.
 * @author Camilo Dulce <opencomex@opencomex.com>
 * @package openComex
 *
 * Variables:
 */

# Librerias
include ("../../../../libs/php/utility.php");

# Cookie fija
$kDf = explode("~",$_COOKIE["kDatosFijos"]);
$kMysqlHost = $kDf[0];
$kMysqlUser = $kDf[1];
$kMysqlPass = $kDf[2];
$kMysqlDb   = $kDf[3];
$kUser      = $kDf[4];
$kLicencia  = $kDf[5];
$swidth     = $kDf[6];
$nSecuencia;

?>
<html>
  <title>Anulaci&oacute;n Factura</title>
  <head>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker2.js'></script>
    <script language = 'javascript'>
      function fnRetorna(){
        parent.window.close();
      }
      function fnGuardar(){
        
        if(document.forms['frnav']['cCliCnrRf'].value != ""){
          if (confirm('Esta Seguro de Asignar los Correos ?')){
            document.frnav.submit();
          }
        }else{
          alert("Debe Digitar Correos Notificacion Rechazos Revisor Fiscal.\nVerifique")
        }
      }
    </script>
  </head>
  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
    <form name = 'frnav' action = 'frteracg.php' method = 'post' target="fmpro">
      <input type = 'hidden' name = 'cTerIds' value = '<?php echo $gTerId?>'>
      <center>
        <table border ="0" cellpadding="0" cellspacing="0" width="580">
          <tr>
            <td>
              <input type = "hidden" name = "nSecuencia" value = 'value = '<?php echo $gSecuencia ?>''>
              <fieldset>
                <legend><b>Correos Notificaci&oacute;n Rechazos Revisor Fiscal</b></legend>
                <table border = "0" cellpadding = "0" cellspacing = "0" width="580">
                  <?php echo f_Columnas(29,20); ?>
                  <tr>
                    <td Class = 'name' colspan = '29'><font color ="#FF0000"> (Separe los correos por comas ',' y sin espacios)</font><br>
                      <textarea Class = 'letrata' style = 'width:580;height:48' name = 'cCliCnrRf'></textarea>
                    </td>
                  </tr>
                </table>
              </fieldset>
            </td>
          </tr>
        </table>
      </center>
    </form>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="580">
        <tr height="21">
          <td width="398" height="21"></td>
          <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand"
              onClick = "javascript:fnGuardar();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar
          </td>
          <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
              onClick = "javascript:fnRetorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
          </td>
        </tr>
      </table>
    </center>
  </body>
</html>

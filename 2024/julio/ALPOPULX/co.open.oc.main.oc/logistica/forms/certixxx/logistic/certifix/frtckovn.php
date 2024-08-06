<?php
/**
 * Orgainzacion de Ventas.
 * --- Descripcion: Permite Crear un nuevo estado de ticket.
 * @author cristian.perdomo@openits.co
 * @package openComex
 * @version 001
 */
include("../../../../../financiero/libs/php/utility.php");
?>
<html>
<head>
  <LINK rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
  <script languaje='javascript' src='<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
  <script language="javascript">
    function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
      document.location = "<?php echo $_COOKIE['kIniAnt'] ?>";
      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
    }

    function fnValidacEstado() {
      var cEstado = document.forms['frgrm']['cEstado'].value.toUpperCase();
      if (cEstado == 'A' || cEstado == 'AC' || cEstado == 'ACT' || cEstado == 'ACTI' || cEstado == 'ACTIV' || cEstado == 'ACTIVO') {
        cEstado = 'ACTIVO';
      } else {
        if (cEstado == 'I' || cEstado == 'IN' || cEstado == 'INA' || cEstado == 'INAC' || cEstado == 'INACT' || cEstado == 'INACTI' || cEstado == 'INACTIV' || cEstado == 'INACTIVO') {
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
  <style>
    .readonly {
      background-color: #e9ecef;
      pointer-events: none;
      user-select: none;
    }
  </style>
</head>
  <body topmargin=0 leftmargin=0 margnwidth=0 marginheight=0 style='margin-right:0'>
    <center><br>
      <table border="0" cellpadding="0" cellspacing="0" width="1050">
        <tr>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>TICKET</b></td>
          <td style="width: 15%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;"></td>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>POST ID</b></td>
          <td style="width: 15%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;"></td>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>PRIORIDAD</b></td>
          <td style="width: 15%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;"></td>
          <td style="width: 5%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>STATUS</b></td>
          <td style="width: 15%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;"></td>
        </tr>
        <tr>
          <td style="width: 20%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>APERTURA TICKET</b></td>
          <td style="width: 45%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="3"></td>
          <td style="width: 20%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;" colspan="2"><b>CIERRE TICKET</b></td>
          <td style="width: 25%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="2"></td>
        </tr>
        <tr>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>TIPO DE TICKET</b></td>
          <td style="width: 95%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"></td>
        </tr>
        <tr>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>TICKET ENVIADO A</b></td>
          <td style="width: 95%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"></td>
        </tr>
        <tr>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>TICKET CC A</b></td>
          <td style="width: 95%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"></td>
        </tr>
        <tr>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>CERTIFICACION</b></td>
          <td style="width: 95%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"></td>
        </tr>
        <tr>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>CLIENTE</b></td>
          <td style="width: 95%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"></td>
        </tr>
        <tr>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>ASUNTO</b></td>
          <td style="width: 95%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"></td>
        </tr>
      </table>
      <table border="0" cellpadding="0" cellspacing="0" width="850">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo ($_COOKIE['kModo'] == "NUEVO") ?  "Nuevo " . $_COOKIE['kProDes'] : $_COOKIE['kMenDes']  ?></legend>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
  </body>
</html>

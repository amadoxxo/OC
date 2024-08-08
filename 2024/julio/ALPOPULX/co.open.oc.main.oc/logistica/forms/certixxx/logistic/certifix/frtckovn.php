<?php
/**
 * Orgainzacion de Ventas.
 * --- Descripcion: Permite Ver la información del ticket.
 * @author elian.amado@openits.co
 * @package openComex
 * @version 001
 */
  include("../../../../../financiero/libs/php/utility.php");
  include("../../../../../logistica/libs/php/utiworkf.php");

  $verTickets = new cTickets();
  $cabecera = $verTickets->fnCabeceraTickets($cCerId);
  $detalles  = $verTickets->fnDetalleTickets($cCerId);

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
          <td style="width: 15%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;"><?php echo $cabecera['ticidxxx'];?></td>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>POST ID</b></td>
          <td style="width: 15%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;"><?php echo count($detalles);?></td>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>PRIORIDAD</b></td>
          <td style="width: 15%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;"><?php echo $cabecera['ptidesxx']; ?></td>
          <td style="width: 5%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>STATUS</b></td>
          <td style="width: 15%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;"><?php echo $cabecera['stidesxx']; ?></td>
        </tr>
        <tr>
          <td style="width: 20%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>APERTURA TICKET</b></td>
          <td style="width: 45%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="3"><?php echo date('Y-m-d', strtotime($cabecera['regfcrex'])); ?></td>
          <td style="width: 20%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;" colspan="2"><b>CIERRE TICKET</b></td>
          <td style="width: 25%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="2"><?php echo ($cabecera['ticcierx'] != '0000-00-00') ? date('Y-m-d', strtotime($cabecera['ticcierx'])) : ''; ?></td>
        </tr>
        <tr>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>TIPO DE TICKET</b></td>
          <td style="width: 95%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"><?php echo $cabecera['ttidesxx']; ?></td>
        </tr>
        <tr>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>TICKET ENVIADO A</b></td>
          <td style="width: 95%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"><?php echo $cabecera['emails']; ?></td>
        </tr>
        <tr>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>TICKET CC A</b></td>
          <td style="width: 95%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"><?php echo end($detalles)['ticccopx']; ?></td>
        </tr>
        <tr>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>CERTIFICACION</b></td>
          <td style="width: 95%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"><?php echo $cabecera['comprexx'] . $cabecera['comcscxx']; ?></td>
        </tr>
        <tr>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>CLIENTE</b></td>
          <td style="width: 95%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"><?php echo $cabecera['clinomxx']; ?></td>
        </tr>
        <tr>
          <td style="width: 10%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>ASUNTO</b></td>
          <td style="width: 95%; height: 20px; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"><?php echo $cabecera['ticasuxx']; ?></td>
        </tr>
      </table>
      <table border="0" cellpadding="0" cellspacing="0" width="1050">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo ($_COOKIE['kModo'] == "NUEVO") ?  "Nuevo " . $_COOKIE['kProDes'] : $_COOKIE['kMenDes'] ?></legend>
              <!-- vista historico -->
              <div style="display: flex; align-items: center; padding-bottom: 5px; width: 840px;">
                <div style="flex: 1;">Post Id Hecho Por:</div>
                <div style="display: flex; align-items: center; flex: 1; justify-content: center;">
                  <div style="background-color: <?php echo htmlspecialchars($vSysStr['system_row_title_color_ini']); ?>; width: 30px; height: 20px; margin-right: 5px;"></div>
                  <div>Responsable</div>
                </div>
                <div style="display: flex; align-items: center; flex: 1; justify-content: flex-end;">
                  <div style="background-color: #B4E197; width: 30px; height: 20px; margin-right: 5px;"></div>
                  <div>Tercero</div>
                </div>
              </div>
              <table border="1" cellpadding="0" cellspacing="0" width="1025">
                <?php foreach ($detalles as $detalle) : ?>
                <tr>
                  <td class="clase08" style="width: 15%; background-color: <?php echo ($detalle['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; padding-left: 2px;">
                    <b>USUARIO</b>
                  </td>
                  <td class="clase08" style="width: 20%; background-color: <?php echo ($detalle['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; font-weight: normal; padding-left: 2px;">
                    <?php echo $detalle['usrnomxx'] ?>
                  </td>
                  <td class="clase08" style="width: 10%; background-color: <?php echo ($detalle['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; padding-left: 2px;">
                    <b>POST ID</b>
                  </td>
                  <td class="clase08" style="width: 10%; background-color: <?php echo ($detalle['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; font-weight: normal; padding-left: 2px;">
                    <?php echo $detalle['repcscxx'] ?>
                  </td>
                  <td class="clase08" style="width: 10%; background-color: <?php echo ($detalle['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; padding-left: 2px;">
                    <b>FECHA</b>
                  </td>
                  <td class="clase08" style="width: 10%; background-color: <?php echo ($detalle['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; font-weight: normal; padding-left: 2px;">
                    <?php echo $detalle['regfcrex'] ?>
                  </td>
                  <td class="clase08" style="width: 10%; background-color: <?php echo ($detalle['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; padding-left: 2px;">
                    <b>HORA</b>
                  </td>
                  <td class="clase08" style="width: 10%; background-color: <?php echo ($detalle['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; font-weight: normal; padding-left: 2px;">
                    <?php echo $detalle['reghcrex'] ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="8" class="clase08" style="width: 100%; font-weight: normal;">
                  <?php echo $detalle['repreply'] ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </table>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
    <center>
    <table border="0" cellpadding="0" cellspacing="0" width="1050">
      <tr height="21">
        <?php switch ($_COOKIE['kModo']) {
          case "VER": ?>
            <td width="960" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick="javascript:fnRetorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
          <?php break;
          default: ?>
            <td width="755" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick="javascript:fnRetorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
        <?php break;
        } ?>
      </tr>
    </table>
  </center>
  </body>
</html>

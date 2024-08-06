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
    <table border="0" cellpadding="0" cellspacing="0" width="850">
      <tr>
        <td style="width: 20%; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>TICKET</b></td>
        <td style="width: 10%; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;"></td>
        <td style="width: 10%; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>POST ID</b></td>
        <td style="width: 10%; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;"></td>
        <td style="width: 10%; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>PRIORIDAD</b></td>
        <td style="width: 10%; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;"></td>
        <td style="width: 10%; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>STATUS</b></td>
        <td style="width: 10%; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;"></td>
      </tr>
      <tr>
        <td style="width: 20%; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>APERTURA TICKET</b></td>
        <td style="width: 40%; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="3"></td>
        <td style="width: 20%; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;" colspan="2"><b>CIERRE TICKET</b></td>
        <td style="width: 20%; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="2"></td>
      </tr>
      <tr>
        <td style="width: 10%; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>TIPO DE TICKET</b></td>
        <td style="width: 90%; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"></td>
      </tr>
      <tr>
        <td style="width: 10%; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>TICKET ENVIADO A</b></td>
        <td style="width: 90%; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"></td>
      </tr>
      <tr>
        <td style="width: 10%; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>TIKECT CC A</b></td>
        <td style="width: 90%; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"></td>
      </tr>
      <tr>
        <td style="width: 10%; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>CERTIFICACION</b></td>
        <td style="width: 90%; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"></td>
      </tr>
      <tr>
        <td style="width: 10%; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>CLIENTE</b></td>
        <td style="width: 90%; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"></td>
      </tr>
      <tr>
        <td style="width: 10%; border: 0.5px solid #CCCCCC; background-color: #E5E5E5; padding-left: 2px; font-size: 9px;"><b>ASUNTO</b></td>
        <td style="width: 90%; border: 0.5px solid #CCCCCC; background-color: #fff; padding-left: 2px;" colspan="7"></td>
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
  <center>
    <table border="0" cellpadding="0" cellspacing="0" width="850">
      <tr height="21">
        <?php switch ($_COOKIE['kModo']) {
          case "VER": ?>
            <td width="755" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick="javascript:parent.window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
          <?php break;
          default: ?>
            <td width="755" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick="javascript:parent.window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
        <?php break;
        } ?>
      </tr>
    </table>
  </center>
</body>

</html>

<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
<?php
switch ($_COOKIE['kModo']) {
  case "NUEVO":
?>
    <script languaje="javascript">
      document.forms['frgrm']['cEstado'].readOnly = true;
    </script>
  <?php
    break;
  case "EDITAR":
    fnCargaData($cStaTic);
  ?>
    <script languaje="javascript">
      var form = document.forms['frgrm'];
      form['cStaTic'].readOnly = true;
      var select = form['cStaPti'];
      select.classList.add('readonly');
    </script>
  <?php break;
  case "VER":
    fnCargaData($cStaTic); ?>
    <script languaje="javascript">
      for (x = 0; x < document.forms['frgrm'].elements.length; x++) {
        document.forms['frgrm'].elements[x].readOnly = true;
        document.forms['frgrm'].elements[x].onfocus = "";
        document.forms['frgrm'].elements[x].onblur = "";
        document.forms['frgrm'].elements[x].disabled = true;
      }
    </script>
<?php break;
} ?>

<?php function fnCargaData($xOrvSap)
{
  global $cAlfa;
  global $xConexion01;

  /* TRAIGO DATOS DE CABECERA*/
  $qOrgVen  = "SELECT * ";
  $qOrgVen .= "FROM $cAlfa.lpar0157 ";
  $qOrgVen .= "WHERE ";
  $qOrgVen .= "sticodxx = \"$xOrvSap\" LIMIT 0,1";
  $xOrgVen  = f_MySql("SELECT", "", $qOrgVen, $xConexion01, "");
  // f_Mensaje(__FILE__,__LINE__,$qOrgVen."~".mysql_num_rows($xOrgVen)."~".mysql_error($xConexion01));
  $vOrgVen  = mysql_fetch_array($xOrgVen);
?>
  <script language="javascript">
    document.forms['frgrm']['cStaTic'].value = "<?php echo $vOrgVen['sticodxx'] ?>";
    document.forms['frgrm']['cOrvDes'].value = "<?php echo $vOrgVen['stidesxx'] ?>";
    document.forms['frgrm']['cStaPti'].value = "<?php echo $vOrgVen['stitipxx'] ?>";
    document.forms['frgrm']['dFecCre'].value = "<?php echo $vOrgVen['regfcrex'] ?>";
    document.forms['frgrm']['dHorCre'].value = "<?php echo $vOrgVen['reghcrex'] ?>";
    document.forms['frgrm']['dFecMod'].value = "<?php echo $vOrgVen['regfmodx'] ?>";
    document.forms['frgrm']['dHorMod'].value = "<?php echo $vOrgVen['reghmodx'] ?>";
    document.forms['frgrm']['cEstado'].value = "<?php echo $vOrgVen['regestxx'] ?>";
  </script>
<?php
} ?>
<?php
  namespace openComex;
/**
 * Orgainzacion de Ventas.
 * --- Descripcion: Permite Crear una Nueva Organizacion de Ventas.
 * @author johana.arboleda@openits.co
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

      function fnValidacEstado() {
        var cEstado = document.forms['frgrm']['cEstado'].value.toUpperCase();
        if(cEstado == 'A' || cEstado == 'AC' || cEstado == 'ACT' || cEstado == 'ACTI' || cEstado == 'ACTIV' || cEstado == 'ACTIVO'){
          cEstado = 'ACTIVO';
        } else {
          if(cEstado == 'I' || cEstado == 'IN' || cEstado == 'INA' || cEstado == 'INAC' || cEstado == 'INACT' || cEstado == 'INACTI' || cEstado == 'INACTIV' || cEstado == 'INACTIVO') {
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
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="300">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo ($_COOKIE['kModo'] == "NUEVO") ?  "Nuevo ".$_COOKIE['kProDes'] : $_COOKIE['kMenDes']  ?></legend>
              <form name = 'frgrm' action = 'frorvgra.php' method = 'post' target='fmpro'>
                <center>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='460'>
                      <?php $cCol = f_Format_Cols(23);
                      echo $cCol;?>
                      <tr>
                        <td Class = 'clase08' colspan = '6'>Codigo SAP<br>
                          <input type = "text" Class = "letra" style = 'width:120' name = 'cOrvSap' onBlur = "javascript:this.value=this.value.toUpperCase()" maxlength="4">
                        </td>
                        <td Class = 'clase08' colspan = '17'>Organizaci&oacute;n de Ventas<br>
                          <input type = "text" Class = "letra" style = 'width:340' name = 'cOrvDes' onBlur = "javascript:this.value=this.value.toUpperCase()" maxlength="255">
                        </td>
                      </tr>
                      <tr>
                        <td Class = "clase08" colspan = "5">Creado<br>
                          <input type = "text" Class = "letra"  style = "width:100;text-align:center"  name = "dFecCre"  value = "<?php echo date('Y-m-d') ?>" readonly>
                        </td>
                        <td Class = "clase08" colspan = "4">Hora<br>
                          <input type = 'text' Class = 'letra' style = "width:80;text-align:center" name = "dHorCre"  value = "<?php echo date('H:i:s') ?>" readonly>
                        </td>
                        <td Class = "clase08" colspan = "5">Modificado<br>
                          <input type = "text" Class = "letra"  style = "width:100;text-align:center"  name = "dFecMod"  value = "<?php echo date('Y-m-d') ?>" readonly>
                        </td>
                        <td Class = "clase08" colspan = "4">Hora<br>
                          <input type = 'text' Class = 'letra' style = "width:80;text-align:center" name = "dHorMod"  value = "<?php echo date('H:i:s') ?>" readonly>
                        </td>
                        <td Class = "clase08" colspan = "5">Estado<br>
                          <input type = "text" Class = "letra" style = "width:100;text-align:center" name = "cEstado"  value = "ACTIVO"
                                onblur = "javascript:this.value=this.value.toUpperCase();fnValidacEstado();
                                                    this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
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
      <table border="0" cellpadding="0" cellspacing="0" width="460">
        <tr height="21">
          <?php switch ($_COOKIE['kModo']) {
            case "VER": ?>
              <td width="369" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
            default: ?>
              <td width="278" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:fnGuardar()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
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
    <script languaje = "javascript">
      document.forms['frgrm']['cEstado'].readOnly  = true;
    </script>
    <?php
  break;
  case "EDITAR":
    fnCargaData($cOrvSap);
    ?>
    <script languaje = "javascript">
      document.forms['frgrm']['cOrvSap'].readOnly	 = true;
    </script>
  <?php break;
  case "VER":
    fnCargaData($cOrvSap); ?>
    <script languaje = "javascript">
      for (x=0;x<document.forms['frgrm'].elements.length;x++) {
        document.forms['frgrm'].elements[x].readOnly = true;
        document.forms['frgrm'].elements[x].onfocus  = "";
        document.forms['frgrm'].elements[x].onblur   = "";
        document.forms['frgrm'].elements[x].disabled = true;
      }
    </script>
  <?php break;
} ?>

<?php function fnCargaData($xOrvSap) {
  global $cAlfa; global $xConexion01;

  /* TRAIGO DATOS DE CABECERA*/
  $qOrgVen  = "SELECT * ";
  $qOrgVen .= "FROM $cAlfa.lpar0001 ";
  $qOrgVen .= "WHERE ";
  $qOrgVen .= "orvsapxx = \"$xOrvSap\" LIMIT 0,1";
  $xOrgVen  = f_MySql("SELECT","",$qOrgVen,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qOrgVen."~".mysql_num_rows($xOrgVen)."~".mysql_error($xConexion01));
  $vOrgVen  = mysql_fetch_array($xOrgVen);
  ?>
  <script language = "javascript">
    document.forms['frgrm']['cOrvSap'].value = "<?php echo $vOrgVen['orvsapxx'] ?>";
    document.forms['frgrm']['cOrvDes'].value = "<?php echo $vOrgVen['orvdesxx'] ?>";
    document.forms['frgrm']['dFecCre'].value = "<?php echo $vOrgVen['regfcrex'] ?>";
    document.forms['frgrm']['dHorCre'].value = "<?php echo $vOrgVen['reghcrex'] ?>";
    document.forms['frgrm']['dFecMod'].value = "<?php echo $vOrgVen['regfmodx'] ?>";
    document.forms['frgrm']['dHorMod'].value = "<?php echo $vOrgVen['reghmodx'] ?>";
    document.forms['frgrm']['cEstado'].value = "<?php echo $vOrgVen['regestxx'] ?>";
  </script>
  <?php
} ?>

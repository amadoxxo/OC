<?php
  namespace openComex;
/**
 * Periodicidad de Facturación.
 * --- Permite Crear un Nueva Periodicidad de Facturación.
 * @author oscar.perez@openits.co
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
              <form name = 'frgrm' action = 'frpefgra.php' method = 'post' target='fmpro'>
                <center>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='460'>
                      <?php $cCol = f_Format_Cols(23);
                      echo $cCol;?>
                      <tr>
                        <td Class = 'clase08' colspan = '6'>ID<br>
                          <input type = "text" Class = "letra" style = 'width:120;text-align:center' name = 'cPfaId' onBlur = "javascript:this.value=this.value.toUpperCase()" readonly>
                        </td>
                        <td Class = 'clase08' colspan = '17'>Periodicidad de Facturaci&oacute;n<br>
                          <input type = "text" Class = "letra" style = 'width:340' name = 'cPFaDes' onBlur = "javascript:this.value=this.value.toUpperCase()" maxlength="255">
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

    $nMaxId = 0;
    $qMaximo  = "SELECT MAX(ABS(pfaidxxx)) AS pfaidxxx ";
    $qMaximo .= "FROM $cAlfa.lpar0005 ";
    $xMaximo = f_MySql("SELECT","",$qMaximo,$xConexion01,"");
    if (mysql_num_rows($xMaximo) > 0){
      $vMaximo = mysql_fetch_array($xMaximo);
      $nMaxId = $vMaximo['pfaidxxx'] + 1;
    } else {
      $nMaxId = 1;
    }

    ?>
    <script languaje = "javascript">
      document.forms['frgrm']['cPfaId'].value = "<?php echo str_pad($nMaxId, 3, "00", STR_PAD_LEFT) ?>";
      document.forms['frgrm']['cEstado'].readOnly  = true;
    </script>
    <?php
  break;
  case "EDITAR":
    fnCargaData($cPfaId);
    ?>
    <script languaje = "javascript">
      document.forms['frgrm']['cPfaId'].readOnly	 = true;
    </script>
  <?php break;
  case "VER":
    fnCargaData($cPfaId); ?>
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

<?php function fnCargaData($xPfaId) {
  global $cAlfa; global $xConexion01;
  
  /* TRAIGO DATOS DE CABECERA*/
  $qPerFac  = "SELECT * ";
  $qPerFac .= "FROM $cAlfa.lpar0005 ";
  $qPerFac .= "WHERE ";
  $qPerFac .= "pfaidxxx = \"$xPfaId\" LIMIT 0,1";
  $xPerFac  = f_MySql("SELECT","",$qPerFac,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qPerFac."~".mysql_num_rows($xPerFac)."~".mysql_error($xConexion01));
  $vPerFac  = mysql_fetch_array($xPerFac);
  ?>
  <script language = "javascript">
    document.forms['frgrm']['cPfaId'].value = "<?php echo $vPerFac['pfaidxxx'] ?>";
    document.forms['frgrm']['cPFaDes'].value = "<?php echo $vPerFac['pfadesxx'] ?>";
    document.forms['frgrm']['dFecCre'].value = "<?php echo $vPerFac['regfcrex'] ?>";
    document.forms['frgrm']['dHorCre'].value = "<?php echo $vPerFac['reghcrex'] ?>";
    document.forms['frgrm']['dFecMod'].value = "<?php echo $vPerFac['regfmodx'] ?>";
    document.forms['frgrm']['dHorMod'].value = "<?php echo $vPerFac['reghmodx'] ?>";
    document.forms['frgrm']['cEstado'].value = "<?php echo $vPerFac['regestxx'] ?>";
  </script>
  <?php
} ?>

<?php
namespace openComex;
/**
 * Tipo de Ticket.
 * --- Descripcion: Permite Crear un Nuevo Tipo de Ticket.
 * @author cristian.perdomo@openits.co
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

      function fnLinks(xLink,xSwitch,xCodTipTicta='') {
        var zX    = screen.width;
        var zY    = screen.height;
        switch (xLink) {
          // Responsables asigandos
          case 'cResTic':
            var zNx     = (zX-580)/2;
            var zNy     = (zY-500)/2;
            var zWinPro = 'width=580,scrollbars=1,height=500,left='+zNx+',top='+zNy;
            var zRuta   = 'frttiovn.php?&gCseTipTicta='+document.forms['frgrm']['cReAsig'].value;

            zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
            zWindow2.focus();
          break;
        }
      }

      function fnCargarGrillas() {
        var cRuta = "frttigri.php?gCseId=<?php echo $cCseId ?>" +
                    "&gCseTipTicta=" + document.forms['frgrm']['cReAsig'].value;
        parent.fmpro.location = cRuta;
      }

      function fnEliminarResponsable(valor) {
        if (confirm('Esta de borrar el usuario '+valor+'?')) {
          var ruta = "frttiovg.php?cCseId=<?php echo $cCseId ?>&tipsave=2&cIntId="+valor+"&cReAsig="+document.forms['frgrm']['cReAsig'].value;
          parent.fmpro.location = ruta;
        }
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
              <form name = 'frgrm' action = 'frttigra.php' method = 'post' target='fmpro'>
                <center>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='580'>
                      <?php $cCol = f_Format_Cols(29);
                      echo $cCol;?>
                      <tr>
                        <td Class = 'clase08' colspan = '04' style="padding:0;margin:0">Codigo<br>
                          <input type = "text" Class = "letra" style = 'width:080' name = 'cTtiCod' readonly>
                        </td>
                        <td Class = 'clase08' colspan = '25' style="padding:0;margin:0">Descripci&oacute;n<br>
                          <input type = "text" Class = "letra" style = 'width:500' name = 'cTtiDes' onBlur = "javascript:this.value=this.value.toUpperCase()" maxlength="255">
                        </td>
                      </tr>
                      <tr>
                        <td Class = "clase08" colspan="29">
                            <fieldset>
                              <legend>Responsable Asignados</legend>
                              <center>
                                <div id = 'overDivResponsable'></div>
                              </center>
                              <input type = 'hidden' name = 'cReAsig'>
                            </fieldset>
                        </td>
                      </tr>
                      <tr>
                        <td Class = "clase08" colspan = "6" style="padding:0;margin:0">Creado<br>
                          <input type = "text" Class = "letra"  style = "width:120;text-align:center"  name = "dFecCre"  value = "<?php echo date('Y-m-d') ?>" readonly>
                        </td>
                        <td Class = "clase08" colspan = "5" style="padding:0;margin:0">Hora<br>
                          <input type = 'text' Class = 'letra' style = "width:100;text-align:center" name = "dHorCre"  value = "<?php echo date('H:i:s') ?>" readonly>
                        </td>
                        <td Class = "clase08" colspan = "6" style="padding:0;margin:0">Modificado<br>
                          <input type = "text" Class = "letra"  style = "width:120;text-align:center"  name = "dFecMod"  value = "<?php echo date('Y-m-d') ?>" readonly>
                        </td>
                        <td Class = "clase08" colspan = "6" style="padding:0;margin:0">Hora<br>
                          <input type = 'text' Class = 'letra' style = "width:120;text-align:center" name = "dHorMod"  value = "<?php echo date('H:i:s') ?>" readonly>
                        </td>
                        <td Class = "clase08" colspan = "6" style="padding:0;margin:0">Estado<br>
                          <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cEstado"  value = "ACTIVO"
                                onblur = "javascript:this.value=this.value.toUpperCase();
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
      <table border="0" cellpadding="0" cellspacing="0" width="600">
        <tr height="21">
          <?php switch ($_COOKIE['kModo']) {
            case "VER": ?>
              <td width="509" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
            default: ?>
              <td width="418" height="21"></td>
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
      fnCargarGrillas();
    </script>
    <?php
  break;
  case "EDITAR":
    fnCargaData($cTtiCod);
    ?>
    <script languaje = "javascript">
      document.forms['frgrm']['cTtiCod'].readOnly	 = true;
      fnCargarGrillas();
    </script>
    <?php
  break;
  case "VER":
    fnCargaData($cTtiCod); ?>
    <script languaje = "javascript">
      for (x=0;x<document.forms['frgrm'].elements.length;x++) {
        document.forms['frgrm'].elements[x].readOnly = true;
        document.forms['frgrm'].elements[x].onfocus  = "";
        document.forms['frgrm'].elements[x].onblur   = "";
        document.forms['frgrm'].elements[x].disabled = true;
      }
      fnCargarGrillas();
    </script>
    <?php
  break;
} ?>

<?php function fnCargaData($xTtiCod) {
  global $cAlfa; global $xConexion01;
  /* TRAIGO DATOS DE CABECERA*/
  $qTipTic  = "SELECT * ";
  $qTipTic .= "FROM $cAlfa.lpar0158 ";
  $qTipTic .= "WHERE ";
  $qTipTic .= "tticodxx = \"$xTtiCod\" LIMIT 0,1";
  $xTipTic  = f_MySql("SELECT","",$qTipTic,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qTipTic."~".mysql_num_rows($xTipTic));
  $vTipTic  = mysql_fetch_array($xTipTic);

  // Consulta de usuarios asignados si los hay
  $cResTic  = "";
  $qOrganizacion  = "SELECT ";
  $qOrganizacion .= "tticodxx, ";
  $qOrganizacion .= "ttiusrxx ";
  $qOrganizacion .= "FROM $cAlfa.lpar0159 ";
  $qOrganizacion .= "WHERE ";
  $qOrganizacion .= "tticodxx = \"$xTtiCod\"";
  $xOrganizacion  = f_MySql("SELECT","",$qOrganizacion,$xConexion01,"");

  $mOrganizacion = array();
  if (mysql_num_rows($xOrganizacion) > 0) {
    while ($xRSS = mysql_fetch_array($xOrganizacion)) {
      if (!array_key_exists($xRSS['ttiusrxx'], $mOrganizacion)){
        $cResTic .= $xRSS['ttiusrxx'] . "~";
        $mOrganizacion[$xRSS['ttiusrxx']] = $xRSS['ttiusrxx'];
      }
    }
    $cResTic = rtrim($cResTic, "~");
  }
  ?>
  <script language = "javascript">
    document.forms['frgrm']['cTtiCod'].value = "<?php echo $vTipTic['tticodxx'] ?>";
    document.forms['frgrm']['cTtiDes'].value = "<?php echo $vTipTic['ttidesxx'] ?>";
    document.forms['frgrm']['cReAsig'].value = "<?php echo $cResTic ?>";
    document.forms['frgrm']['dFecCre'].value = "<?php echo $vTipTic['regfcrex'] ?>";
    document.forms['frgrm']['dHorCre'].value = "<?php echo $vTipTic['reghcrex'] ?>";
    document.forms['frgrm']['dFecMod'].value = "<?php echo $vTipTic['regfmodx'] ?>";
    document.forms['frgrm']['dHorMod'].value = "<?php echo $vTipTic['reghmodx'] ?>";
    document.forms['frgrm']['cEstado'].value = "<?php echo $vTipTic['regestxx'] ?>";
  </script>
  <?php
} ?>

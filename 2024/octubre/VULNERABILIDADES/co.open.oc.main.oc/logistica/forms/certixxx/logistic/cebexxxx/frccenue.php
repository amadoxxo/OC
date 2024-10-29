<?php
  namespace openComex;
/**
 * Codigo Cebe.
 * --- Descripcion: Permite Crear un Nuevo Codigo Cebe
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

      function fnLinks(xLink,xSwitch) {
        var nX    = screen.width;
        var nY    = screen.height;
        switch (xLink){
          case "cSecSap":
            if (xSwitch == "VALID") {
              var cRuta  = "frcce001.php?gWhat="+xSwitch+"&gFunction="+xLink+"&cSecSap="+document.forms['frgrm']['cSecSap'].value.toUpperCase()+"";
              parent.fmpro.location = cRuta;
            } else {
              var nNx     = (nX-600)/2;
              var nNy     = (nY-250)/2;
              var cWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var cRuta  = "frcce001.php?gWhat="+xSwitch+"&gFunction="+xLink+"&cSecSap="+document.forms['frgrm']['cSecSap'].value.toUpperCase()+"";
              zWindow = window.open(cRuta,"zWindow",cWinPro);
              zWindow.focus();
            }
          break;
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
              <form name = 'frgrm' action = 'frccegra.php' method = 'post' target='fmpro'>
                <center>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='460'>
                      <?php $cCol = f_Format_Cols(23);
                      echo $cCol;?>
                      <tr>
                        <td Class = 'clase08' colspan = '6'>Id<br>
                          <input type = "text" Class = "letra" style = 'width:120' name = 'cCebId' onBlur = "javascript:this.value=this.value.toUpperCase()" maxlength="4" readonly>
                        </td>
                        <td Class = 'clase08' colspan = '8'>Plataforma<br>
                          <input type = "text" Class = "letra" style = 'width:170' name = 'cCebPla' onBlur = "javascript:this.value=this.value.toUpperCase()" maxlength="255">
                        </td>
                        <td Class = 'clase08' colspan = '8'>Municipio<br>
                          <input type = "text" Class = "letra" style = 'width:170' name = 'cCebMun' onBlur = "javascript:this.value=this.value.toUpperCase()" maxlength="255">
                        </td>
                        <td Class = 'clase08' colspan = '1'><br>
                          <input type = "text" Class = "letra" style = 'width:15' readonly>
                        </td>
                      </tr>
                      <tr>
                        <td Class = "clase08" colspan = '6'>
                          <a href = "javascript:document.forms['frgrm']['cSecSap'].value = '';
                                                document.forms['frgrm']['cSecDes'].value = '';
                                                fnLinks('cSecSap','VALID')" id="IdOrvSap">Codigo SAP</a><br>
                          <input type = 'text' Class = 'letra' style = 'width:120' name = 'cSecSap' maxlength="2"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cSecSap','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus="javascript:document.forms['frgrm']['cSecSap'].value = '';
                                                document.forms['frgrm']['cSecDes'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                        <td Class = 'clase08' colspan = '16'>Sector<br>
                          <input type = 'text' Class = 'letra' style = 'width:340.5' name = 'cSecDes' readonly>
                        </td>
                        <td Class = 'clase08' colspan = '1'><br>
                          <input type = "text" Class = "letra" style = 'width:15' readonly>
                        </td>
                      </tr>
                      <tr>
                        <td Class = 'clase08' colspan = '6'>Cod Cebe<br>
                          <input type = "text" Class = "letra" style = 'width:120' name = 'cCebCod' onBlur = "javascript:this.value=this.value.toUpperCase()" maxlength="7">
                        </td>
                        <td Class = 'clase08' colspan = '16'>Descripci&oacute;n<br>
                          <input type = "text" Class = "letra" style = 'width:340.5' name = 'cCebDes' onBlur = "javascript:this.value=this.value.toUpperCase()" maxlength="255">
                        </td>
                        <td Class = 'clase08' colspan = '1'><br>
                          <input type = "text" Class = "letra" style = 'width:15' readonly>
                        </td>
                      </tr>
                      <tr>
                        <td Class = "clase08" colspan = "5">Fecha Cre<br>
                          <input type = "text" Class = "letra"  style = "width:96.5;text-align:center"  name = "dFecCre"  value = "<?php echo date('Y-m-d') ?>" readonly>
                        </td>
                        <td Class = "clase08" colspan = "4">Hora Cre<br>
                          <input type = 'text' Class = 'letra' style = "width:95;text-align:center" name = "dHorCre"  value = "<?php echo date('H:i:s') ?>" readonly>
                        </td>
                        <td Class = "clase08" colspan = "5">Fecha Mod<br>
                          <input type = "text" Class = "letra"  style = "width:99;text-align:center"  name = "dFecMod"  value = "<?php echo date('Y-m-d') ?>" readonly>
                        </td>
                        <td Class = "clase08" colspan = "4">Hora Mod<br>
                          <input type = 'text' Class = 'letra' style = "width:95;text-align:center" name = "dHorMod"  value = "<?php echo date('H:i:s') ?>" readonly>
                        </td>
                        <td Class = "clase08" colspan = "5">Estado<br>
                          <input type = "text" Class = "letra" style = "width:90;text-align:center" name = "cEstado"  value = "ACTIVO"
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
    $qMaximo  = "SELECT MAX(ABS(cebidxxx)) AS cebidxxx ";
    $qMaximo .= "FROM $cAlfa.lpar0010 ";
    $xMaximo = f_MySql("SELECT","",$qMaximo,$xConexion01,"");
    if (mysql_num_rows($xMaximo) > 0){
      $vMaximo = mysql_fetch_array($xMaximo);
      $nMaxId = $vMaximo['cebidxxx'] + 1;
    } else {
      $nMaxId = 1;
    }

    ?>
    <script languaje = "javascript">
      document.forms['frgrm']['cCebId'].value = "<?php echo str_pad($nMaxId, 4, "000", STR_PAD_LEFT) ?>";
      document.forms['frgrm']['cEstado'].readOnly  = true;
    </script>
    <?php
  break;
  case "EDITAR":
    fnCargaData($cSecSap, $cCebId, $cCebCod);
    ?>
    <script languaje = "javascript">
      document.forms['frgrm']['cCebId'].readOnly  = true;

      document.forms['frgrm']['cSecSap'].readOnly = true;
      document.forms['frgrm']['cCebId'].readOnly  = true;
      document.forms['frgrm']['cCebCod'].readOnly	= true;
      
      document.forms['frgrm']['cSecSap'].onfocus  = "";
      document.forms['frgrm']['cCebId'].onfocus   = "";
      document.forms['frgrm']['cCebCod'].onfocus  = "";

      document.forms['frgrm']['cSecSap'].onblur   = "";
      document.forms['frgrm']['cCebId'].onblur    = "";
      document.forms['frgrm']['cCebCod'].onblur   = "";
    </script>
  <?php break;
  case "VER":
    fnCargaData($cSecSap, $cCebId); ?>
    <script languaje = "javascript">
      for (x=0;x<document.forms['frgrm'].elements.length;x++) {
        document.forms['frgrm'].elements[x].readOnly = true;
        document.forms['frgrm'].elements[x].onfocus  = "";
        document.forms['frgrm'].elements[x].onblur   = "";
        document.forms['frgrm'].elements[x].disabled = true;
      }
      
      document.forms['frgrm']['cSecSap'].readOnly	= true;
      document.forms['frgrm']['cCebId'].readOnly	= true;

      document.forms['frgrm']['cSecSap'].onfocus  = "";
      document.forms['frgrm']['cCebId'].onfocus   = "";

      document.forms['frgrm']['cSecSap'].onblur   = "";
      document.forms['frgrm']['cCebId'].onblur    = "";

      document.getElementById('IdOrvSap').disabled = true;
			document.getElementById('IdOrvSap').href     = "#";
    </script>
  <?php break;
} ?>

<?php function fnCargaData($xSecSap, $xCebId) {
  global $cAlfa; global $xConexion01;

  $qCodCeb  = "SELECT * ";
  $qCodCeb .= "FROM $cAlfa.lpar0010 ";
  $qCodCeb .= "WHERE ";
  $qCodCeb .= "secsapxx = \"$xSecSap\" AND ";
  $qCodCeb .= "cebidxxx = \"$xCebId\" LIMIT 0,1";
  $xCodCeb  = f_MySql("SELECT","",$qCodCeb,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qCodCeb."~".mysql_num_rows($xCodCeb)."~".mysql_error($xConexion01));
  $vCodCeb  = mysql_fetch_array($xCodCeb);

  $qSector  = "SELECT * ";
  $qSector .= "FROM $cAlfa.lpar0009 ";
  $qSector .= "WHERE ";
  $qSector .= "secsapxx = \"$xSecSap\" LIMIT 0,1";
  $xSector  = f_MySql("SELECT","",$qSector,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qSector."~".mysql_num_rows($xSector)."~".mysql_error($xConexion01));
  $vSector  = mysql_fetch_array($xSector);

  ?>
  <script language = "javascript">
    document.forms['frgrm']['cSecSap'].value = "<?php echo $vCodCeb['secsapxx'] ?>";
    document.forms['frgrm']['cSecDes'].value = "<?php echo $vSector['secdesxx'] ?>";
    document.forms['frgrm']['cCebId'].value = "<?php echo $vCodCeb['cebidxxx'] ?>";
    document.forms['frgrm']['cCebPla'].value = "<?php echo $vCodCeb['cebplaxx'] ?>";
    document.forms['frgrm']['cCebMun'].value = "<?php echo $vCodCeb['cebmunxx'] ?>";
    document.forms['frgrm']['cCebCod'].value = "<?php echo $vCodCeb['cebcodxx'] ?>";
    document.forms['frgrm']['cCebDes'].value = "<?php echo $vCodCeb['cebdesxx'] ?>";
    document.forms['frgrm']['dFecCre'].value = "<?php echo $vCodCeb['regfcrex'] ?>";
    document.forms['frgrm']['dHorCre'].value = "<?php echo $vCodCeb['reghcrex'] ?>";
    document.forms['frgrm']['dFecMod'].value = "<?php echo $vCodCeb['regfmodx'] ?>";
    document.forms['frgrm']['dHorMod'].value = "<?php echo $vCodCeb['reghmodx'] ?>";
    document.forms['frgrm']['cEstado'].value = "<?php echo $vCodCeb['regestxx'] ?>";
  </script>
  <?php
} ?>

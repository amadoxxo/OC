<?php
  namespace openComex;
/**
 * Sub Servicios.
 * --- Descripcion: Permite Crear un Nuevo Sub Servicios.
 * @author diego.cortes@openits.co
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
          case "cSerSap":
            if (xSwitch == "VALID") {
              var cRuta  = "frpar001.php?gWhat="+xSwitch+"&gFunction="+xLink+"&cSerSap="+document.forms['frgrm']['cSerSap'].value.toUpperCase()+"";
              parent.fmpro.location = cRuta;
            } else {
              var nNx     = (nX-600)/2;
              var nNy     = (nY-250)/2;
              var cWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
              var cRuta  = "frpar001.php?gWhat="+xSwitch+"&gFunction="+xLink+"&cSerSap="+document.forms['frgrm']['cSerSap'].value.toUpperCase()+"";
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
              <form name = 'frgrm' action = 'frssegra.php' method = 'post' target='fmpro'>
                <center>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='460'>
                      <?php $cCol = f_Format_Cols(23);
                      echo $cCol;?>
                      <tr>
                        <td Class = "clase08" colspan = '6'>
                          <a href = "javascript:document.forms['frgrm']['cSerSap'].value = '';
                                                document.forms['frgrm']['cSerDes'].value = '';
                                                fnLinks('cSerSap','VALID')" id="IdSerSap">Codigo SAP</a><br>
                          <input type = 'text' Class = 'letra' style = 'width:120' name = 'cSerSap' maxlength="3"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                fnLinks('cSerSap','VALID');
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus="javascript:document.forms['frgrm']['cSerSap'].value = '';
                                                document.forms['frgrm']['cSerDes'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                        <td Class = 'clase08' colspan = '17'>Servicio<br>
                          <input type = 'text' Class = 'letra' style = 'width:340' name = 'cSerDes' readonly>
                        </td>
                      </tr>
                      <tr>
                        <td Class = 'clase08' colspan = '6'>Id<br>
                          <input type = "text" Class = "letra" style = 'width:120' name = 'cSSerId' onBlur = "javascript:this.value=this.value.toUpperCase()" readonly>
                        </td>
                        <td Class = 'clase08' colspan = '17'>SubServicio<br>
                          <input type = "text" Class = "letra" style = 'width:340' name = 'cSSeDes' onBlur = "javascript:this.value=this.value.toUpperCase()" maxlength="255">
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
    $qMaximo  = "SELECT MAX(ABS(subidxxx)) AS subidxxx ";
    $qMaximo .= "FROM $cAlfa.lpar0012 ";
    $xMaximo = f_MySql("SELECT","",$qMaximo,$xConexion01,"");
    if (mysql_num_rows($xMaximo) > 0){
      $vMaximo = mysql_fetch_array($xMaximo);
      $nMaxId = $vMaximo['subidxxx'] + 1;
    } else {
      $nMaxId = 1;
    }
    
    ?>
    <script languaje = "javascript">
      document.forms['frgrm']['cSSerId'].value = "<?php echo str_pad($nMaxId, 3, "00", STR_PAD_LEFT) ?>";
      document.forms['frgrm']['cEstado'].readOnly  = true;
    </script>
    <?php
  break;
  case "EDITAR":
    fnCargaData($cSerSap, $cSSerId);
    ?>
    <script languaje = "javascript">
      document.forms['frgrm']['cSSerId'].readOnly	= true;
      document.forms['frgrm']['cSerSap'].readOnly	= true;
      document.forms['frgrm']['cSSerId'].readOnly	= true;
      document.forms['frgrm']['cSerSap'].onfocus  = "";
      document.forms['frgrm']['cSSerId'].onfocus  = "";
      document.forms['frgrm']['cSerSap'].onblur   = "";
      document.forms['frgrm']['cSSerId'].onblur   = "";
      
      document.getElementById('IdSerSap').disabled = true;
			document.getElementById('IdSerSap').href     = "#";
    </script>
  <?php break;
  case "VER":
    fnCargaData($cSerSap, $cSSerId); ?>
    <script languaje = "javascript">
      for (x=0;x<document.forms['frgrm'].elements.length;x++) {
        document.forms['frgrm'].elements[x].readOnly = true;
        document.forms['frgrm'].elements[x].onfocus  = "";
        document.forms['frgrm'].elements[x].onblur   = "";
        document.forms['frgrm'].elements[x].disabled = true;
      }
      
      document.forms['frgrm']['cSerSap'].readOnly	  = true;
      document.forms['frgrm']['cSSerId'].readOnly	  = true;

      document.forms['frgrm']['cSerSap'].onfocus = "";
      document.forms['frgrm']['cSSerId'].onfocus = "";

      document.forms['frgrm']['cSerSap'].onblur = "";
      document.forms['frgrm']['cSSerId'].onblur = "";

      document.getElementById('IdSerSap').disabled=true;
			document.getElementById('IdSerSap').href="#";
    </script>
  <?php break;
} ?>

<?php function fnCargaData($xSerSap, $xSSerId) {
  global $cAlfa; global $xConexion01;

  $qSubSer  = "SELECT * ";
  $qSubSer .= "FROM $cAlfa.lpar0012 ";
  $qSubSer .= "WHERE ";
  $qSubSer .= "sersapxx = \"$xSerSap\" AND ";
  $qSubSer .= "subidxxx = \"$xSSerId\" LIMIT 0,1";
  $xSubSer  = f_MySql("SELECT","",$qSubSer,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qSubSer."~".mysql_num_rows($xSubSer)."~".mysql_error($xConexion01));
  $vSubSer  = mysql_fetch_array($xSubSer);

  $qServix  = "SELECT * ";
  $qServix .= "FROM $cAlfa.lpar0011 ";
  $qServix .= "WHERE ";
  $qServix .= "sersapxx = \"$xSerSap\" LIMIT 0,1";
  $xServix  = f_MySql("SELECT","",$qServix,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qServix."~".mysql_num_rows($xServix)."~".mysql_error($xConexion01));
  $vServix  = mysql_fetch_array($xServix);

  ?>
  <script language = "javascript">
    document.forms['frgrm']['cSerSap'].value = "<?php echo $vSubSer['sersapxx'] ?>";
    document.forms['frgrm']['cSerDes'].value = "<?php echo $vServix['serdesxx'] ?>";
    document.forms['frgrm']['cSSerId'].value = "<?php echo $vSubSer['subidxxx'] ?>";
    document.forms['frgrm']['cSSeDes'].value = "<?php echo $vSubSer['subdesxx'] ?>";
    document.forms['frgrm']['dFecCre'].value = "<?php echo $vSubSer['regfcrex'] ?>";
    document.forms['frgrm']['dHorCre'].value = "<?php echo $vSubSer['reghcrex'] ?>";
    document.forms['frgrm']['dFecMod'].value = "<?php echo $vSubSer['regfmodx'] ?>";
    document.forms['frgrm']['dHorMod'].value = "<?php echo $vSubSer['reghmodx'] ?>";
    document.forms['frgrm']['cEstado'].value = "<?php echo $vSubSer['regestxx'] ?>";
  </script>
  <?php
} ?>

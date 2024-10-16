<?php
  namespace openComex;
  /**
   * Nuevo Comprobante.
   * --- Descripcion: Permite Crear un Nuevo Comprobante.
   * @author Diego Fernando Cortes Rojas <diego.cortes@openits.co>
   * @package opencomex
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

      function fnEnabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.forms['frgrm']['cComId'].disabled  = false;
        document.forms['frgrm']['cComCod'].disabled = false;
      }

      function fnEnableParam(){
        switch (document.forms['frgrm']['cComId'].value) {
          case "M":
          case "C":
            document.getElementById('controlcsc').style.display="block";
          break;
          default:
            document.getElementById('controlcsc').style.display="none";
          break;
        }
      }

      function fnValidaEstado(){
        var zEstado = document.forms['frgrm']['cEstado'].value.toUpperCase();
        if(zEstado == 'A' || zEstado == 'AC' || zEstado == 'ACT' || zEstado == 'ACTI' || zEstado == 'ACTIV' || zEstado == 'ACTIVO'){
          zEstado = 'ACTIVO';
        } else {
          if(zEstado == 'I' || zEstado == 'IN' || zEstado == 'INA' || zEstado == 'INAC' || zEstado == 'INACT' || zEstado == 'INACTI' || zEstado == 'INACTIV' || zEstado == 'INACTIVO') {
            zEstado = 'INACTIVO';
          } else {
            zEstado = '';
          }
        }
        document.forms['frgrm']['cEstado'].value = zEstado;
      }

      function fnLimpiaChecks(xCheck){
        switch(xCheck){
          case "AUTOMATICO":
            document.forms['frgrm']['vChMensu'].checked = false;
            document.forms['frgrm']['vChAnual'].checked = false;
            document.forms['frgrm']['vChIndef'].checked = false;
          break;
        }
      }

      function fnHabilitaConsecutivos(xCheck){
        switch(xCheck){
          case "AUTOMATICO":
            document.forms['frgrm']['cCscInicial'].readOnly               = false;
            document.forms['frgrm']['vChMensu'].checked                   = true;
            document.getElementById('consecutivos').style.display         = "block";
            document.getElementById('cCheckPeriodo').style.display        = "flex";
            document.getElementById('cCheckPeriodo').style.justifyContent = "space-between";
            document.getElementById('cCscPer').innerHTML                  = "Mes";
            document.getElementById('contr_pre').style.display            = "block";
            document.getElementById('contr_ano').style.display            = "block";
            document.getElementById('conseIni').style.display             = "block";
          break;
          case "MANUAL":
            document.forms['frgrm']['cCscInicial'].readOnly        = true;
            document.forms['frgrm']['cCscInicial'].value           = "1";
            document.getElementById('cCscPer').innerHTML           = "";
            document.getElementById('contr_pre').style.display     = "block";
            document.getElementById('contr_ano').style.display     = "block";
            document.getElementById('conseIni').style.display      = "block";
            document.getElementById('consecutivos').style.display  = "block";
            document.getElementById('cCheckPeriodo').style.display = "none";
          break;
        }
      }

      function fnLimpiaChecks1(xCheck){
        switch(xCheck){
          case "MENSUAL":
            document.forms['frgrm']['vChAnual'].checked        = false;
            document.forms['frgrm']['vChIndef'].checked        = false;
            document.getElementById('cCscPer').innerHTML       = "Mes";
            document.getElementById('contr_pre').style.display = "block";
            document.getElementById('contr_ano').style.display = "block";
            document.getElementById('conseIni').style.display  = "block";
          break;
          case "ANUAL":
            document.forms['frgrm']['vChMensu'].checked        = false;
            document.forms['frgrm']['vChIndef'].checked        = false;
            document.getElementById('cCscPer').innerHTML       = "A&ntilde;o";
            document.getElementById('contr_pre').style.display = "block";
            document.getElementById('contr_ano').style.display = "block";
            document.getElementById('conseIni').style.display  = "block";
          break;
          case "INDEFINIDO":
            document.forms['frgrm']['vChMensu'].checked        = false;
            document.forms['frgrm']['vChAnual'].checked        = false;
            document.getElementById('cCscPer').innerHTML       = "Indefinido";
            document.getElementById('contr_pre').style.display = "block";
            document.getElementById('contr_ano').style.display = "block";
            document.getElementById('conseIni').style.display  = "block";
          break;
        }
      }
    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="300">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo $_COOKIE['kMenDes'] ?></legend>
              <form name = 'frgrm' action = 'frcomgra.php' method = 'post' target='fmpro'>
                <input type = "hidden" name = "cCscAnt"    value = "">
                <input type = "hidden" name = "cComIdEdit" value = "<?php echo $cComId ?>">
                <center>
                  <fieldset>
                    <legend>Datos Generales</legend>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='300'>
                      <?php $nCol = f_Format_Cols(18);
                      echo $nCol;?>
                      <tr>
                        <td Class = "clase08" colspan = "18">Id Comprobante</a><br>
                          <select class="letrase" size="1" name="cComId" style = "width:360" onchange="javascript:fnEnableParam();">
                            <option value = "" selected>[SELECCIONE]</option>
                            <option value = "M">MATRIZ DE INSUMOS FACTURABLES</option>
                            <option value = "C">CERTIFICACI&Oacute;N</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td Class = "clase08" colspan = "4">C&oacute;digo</a><br>
                          <input type = 'text' Class = 'letra' style = 'width:80' name = 'cComCod' 
                                onfocus = "javascript:this.style.background='#00FFFF'"
                                onblur  = "javascript:this.value=this.value.toUpperCase();
                                this.style.background='#FFFFFF'" maxlength ="3">
                        </td>
                        <td Class = 'clase08' colspan = '14'>Comprobantes<br>
                          <input type = 'text' Class = 'letra' style = 'width:280' name = 'cComDes'
                                onfocus = "javascript:this.style.background='#00FFFF'"
                                onBlur = "javascript:this.value=this.value.toUpperCase(); 
                                this.style.background='#FFFFFF'" maxlength ="255">
                        </td>
                      </tr>
                    </table>
                  </fieldset>

                  <fieldset id = "controlcsc">
                    <legend>Parametrizaci&oacute;n Consecutivo</legend>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='300'>
                      <?php $nCol = f_Format_Cols(15);
                      echo $nCol;?>
                        <tr>
                          <td Class = "clase08" colspan = "5" height="30">Tipo de Consecutivo :<br></td>
                          <td Class = "clase08" colspan = "5" height="30"><input type="radio" name = "rBtCt" id="rBtMan" value="MANUAL" onclick="javascript:fnLimpiaChecks(this.value);fnHabilitaConsecutivos(this.value);">Manual<br></td>
                          <td Class = "clase08" colspan = "5" height="30"><input type="radio" name = "rBtCt" id="rBtAut" value="AUTOMATICO" onclick="javascript:fnLimpiaChecks(this.value);fnHabilitaConsecutivos(this.value);">Automatico<br></td>
                          <input type = 'hidden' name = 'cComTcoEdit'>
                        </tr>
                    </table>
                    <br>
                    <fieldset id="consecutivos">
                      <legend>Control Consecutivo</legend>
                      <table border = '0' cellpadding = '0' cellspacing = '0' width='280'>
                        <?php $nCol = f_Format_Cols(14);
                        echo $nCol;?>
                        <tr id="cCheckPeriodo">
                          <td Class = "clase08" colspan = "5" style="text-align: center;" height="30"><input type="checkbox" style="width: 94;" name = "vChMensu" value = "MENSUAL" onclick="javascript:fnLimpiaChecks1(this.value);">Mensual<br></td>
                          <td Class = "clase08" colspan = "5" style="text-align: center;" height="30"><input type="checkbox" style="width: 94;" name = "vChAnual" value = "ANUAL" onclick="javascript:fnLimpiaChecks1(this.value);">Anual<br></td>
                          <td Class = "clase08" colspan = "4" style="text-align: center;" height="30"><input type="checkbox" style="width: 92;" name = "vChIndef" value = "INDEFINIDO" onclick="javascript:fnLimpiaChecks1(this.value);">Indefinido<br></td>
                          <input type = 'hidden' name = 'cComCcoEdit'>
                        </tr>
                        <tr id="contr_pre">
                          <td Class = 'clase08' colspan = '7'>
                            <br>
                            <p style = 'width:140;'>Prefijo</p>
                          </td>
                          <td Class = 'clase08' colspan = '7'>
                            <br>
                            <input type = 'text' Class = 'letra' style = 'width:140;text-align:right' name = 'cComPre' maxlength ="3"
                                onfocus = "javascript:this.style.background='#00FFFF'"
                                onblur  = "javascript:this.style.background='#FFFFFF'">
                          </td>
                        </tr>
                        <tr id="contr_ano">
                          <td Class = 'clase08' colspan = '7'>
                            <p style = 'width:140;'>A&ntilde;o</p>
                          </td>
                          <td Class = 'clase08' colspan = '7'>
                            <input type = 'text' Class = 'letra' style = 'width:140;text-align:right' value='<?php echo date('y');?>' name = 'cPerAno' 
                                onfocus = "javascript:this.style.background='#00FFFF'"
                                onblur  = "javascript:this.style.background='#FFFFFF'">
                          </td>
                        </tr>
                        <tr id="conseIni">
                          <td Class='clase08' colspan='7'>
                            <p style = 'width:140;'>Consecutivo Inicial <span id="cCscPer"></span></p>
                          </td>
                          <td Class = 'clase08' colspan = '7'>
                              <input type = 'text' Class = 'letra' style = 'width:140;text-align:right' name = 'cCscInicial' maxlength ="6" 
                                  onfocus = "javascript:this.style.background='#00FFFF'"
                                  onblur  = "javascript:this.style.background='#FFFFFF'">
                            </td>
                        </tr>
                      </table>
                    </fieldset>
                  </fieldset>
                  <fieldset>
                    <legend>Datos del Registro</legend>
                    <table border = '0' cellpadding = '0' cellspacing = '0' width='300'>
                      <?php $nCol = f_Format_Cols(20);
                      echo $nCol;?>
                      <tr>
                        <td Class = "name" colspan = "4">Fecha Cre<br>
                          <input type = "text" Class = "letra"  style = "width:80;text-align:center"  name = "dFecCre" value = "<?php echo date('Y-m-d') ?>" readonly>
                        </td>
                        <td Class = 'name' colspan = "3">Hora Cre<br>
                          <input type = 'text' Class = 'letra' style = "width:60;text-align:center" name = "cHorCre" value = "<?php echo date('H:i:s') ?>" readonly>
                        </td>
                        <td Class = "name" colspan = "4">Fecha Mod<br>
                          <input type = "text" Class = "letra"  style = "width:80;text-align:center"  name = "dFecMod" value = "<?php echo date('Y-m-d') ?>" readonly>
                        </td>
                        <td Class = 'name' colspan = "3">Hora Mod<br>
                          <input type = 'text' Class = 'letra' style = "width:60;text-align:center" name = "cHorMod" value = "<?php echo date('H:i:s') ?>" readonly>
                        </td>
                        <td Class = "name" colspan = "4">Estado<br>
                          <input type = "text" Class = "letra" style = "width:80;text-align:center" name = "cEstado"  value = "ACTIVO"
                            onblur = "javascript:this.value=this.value.toUpperCase();fnValidaEstado();
                                                this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                            onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                        </td>
                      </tr>
                    </table>
                  </fieldset>
                </center>
              </form>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="380">
        <tr height="21">
          <?php switch ($_COOKIE['kModo']) {
            case "VER": ?>
              <td width="289" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
            default: ?>
              <td width="198" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:document.forms['frgrm'].submit();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
          } ?>
        </tr>
      </table>
    </center>
    <!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
    <?php
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        ?>
        <script languaje = "javascript">
          document.getElementById('controlcsc').style.display="none";
          document.getElementById('consecutivos').style.display="none";
          document.forms['frgrm']['cEstado'].readOnly  = true;
          document.forms['frgrm']['cPerAno'].readOnly  = true;
        </script>
        <?php
      break;
      case "EDITAR":
        fnCargaData($cComId,$cComCod);
        ?>
        <script languaje = "javascript">
          document.forms['frgrm']['cComId'].disabled	    = true;
          document.forms['frgrm']['cComCod'].readOnly	    = true;
          document.forms['frgrm']['cComCod'].onfocus      = "";
          document.forms['frgrm']['cComCod'].onblur       = "";
          document.forms['frgrm']['cPerAno'].readOnly     = true;
          document.forms['frgrm']['cCscInicial'].readOnly = true;
        </script>
      <?php
      break;
      case "VER": ?>
        <script languaje = "javascript">
          for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
          }
        </script>
      <?php
      fnCargaData($cComId,$cComCod);
      break;
    } ?>

    <?php function fnCargaData($xComId,$xComCod) {
      global $cAlfa; global $xConexion01; global $vSysStr;

      // TRAIGO DATOS DE CABECERA
      $qCompro  = "SELECT * ";
      $qCompro .= "FROM $cAlfa.lpar0117 ";
      $qCompro .= "WHERE comidxxx = \"$xComId\" AND comcodxx = \"$xComCod\" LIMIT 0,1";
      $xCompro  = f_MySql("SELECT","",$qCompro,$xConexion01,"");
      //f_mensaje(__FILE__,__LINE__,$qCompro);

      // EMPIEZO A RECORRER EL CURSOR DE CABECERA
      while ($xRCO = mysql_fetch_array($xCompro)) {
        // Descripcion del Comprobante Reembolso
        $qDesCom  = "SELECT comdesxx ";
        $qDesCom .= "FROM $cAlfa.lpar0117 ";
        $qDesCom .= "WHERE comidxxx = \"{$xRCO['comidcxx']}\" AND ";
        $qDesCom .= "comcodxx = \"{$xRCO['comcodcx']}\" AND ";
        $qDesCom .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
        $xDesCom  = f_MySql("SELECT", "", $qDesCom, $xConexion01, "");
        $vRNCom = mysql_fetch_array($xDesCom);
        $vRNCom['comdesxx'] = ($vRNCom['comdesxx'] != "") ? trim($vRNCom['comdesxx']) : "COMPROBANTE SIN DESCRIPCION";
        // Descripcion del Comprobante Reembolso

        //f_Mensaje(__FILE__,__LINE__,$xRCO['comidxxx']);
        // Traigo el ultimo numero para el consecutivo
        $qUltCsc  = "SELECT peranoxx, MAX($cAlfa.lpar0122.permesxx) AS permesxx ";
        $qUltCsc .= "FROM $cAlfa.lpar0122 ";
        $qUltCsc .= "WHERE $cAlfa.lpar0122.comidxxx = \"$xComId\" AND ";
        $qUltCsc .= "$cAlfa.lpar0122.comcodxx = \"$xComCod\" AND  ";
        $qUltCsc .= "$cAlfa.lpar0122.peranoxx = (SELECT MAX($cAlfa.lpar0122.peranoxx) ";
        $qUltCsc .= "FROM $cAlfa.lpar0122 ";
        $qUltCsc .= "WHERE $cAlfa.lpar0122.comidxxx = \"$xComId\" AND $cAlfa.lpar0122.comcodxx = \"$xComCod\") ";
        $xUltCsc  = f_MySql("SELECT","",$qUltCsc,$xConexion01,"");
        $vUltCsc = mysql_fetch_array($xUltCsc);
        //f_mensaje(__FILE__,__LINE__,$qUltCsc);

        // Traigo el ultimo numero para el consecutivo
        $qUltCsc  = "SELECT comcscxx, peranoxx, permesxx ";
        $qUltCsc .= "FROM $cAlfa.lpar0122 ";
        $qUltCsc .= "WHERE $cAlfa.lpar0122.comidxxx = \"$xComId\" AND ";
        $qUltCsc .= "$cAlfa.lpar0122.comcodxx = \"$xComCod\" AND  ";
        $qUltCsc .= "$cAlfa.lpar0122.peranoxx = \"{$vUltCsc['peranoxx']}\" AND ";
        $qUltCsc .= "$cAlfa.lpar0122.permesxx = \"{$vUltCsc['permesxx']}\" ";
        $xUltCsc  = f_MySql("SELECT","",$qUltCsc,$xConexion01,"");
        $vUltCsc = mysql_fetch_array($xUltCsc);
        //f_mensaje(__FILE__,__LINE__,$qUltCsc);
        ?>
        <script language = "javascript">
          document.forms['frgrm']['cComId'].value		    = "<?php echo $xRCO['comidxxx'] ?>";
          document.forms['frgrm']['cComCod'].value	    = "<?php echo $xRCO['comcodxx'] ?>";
          document.forms['frgrm']['cComDes'].value      = "<?php echo $xRCO['comdesxx'] ?>";
          document.forms['frgrm']['cComPre'].value      = "<?php echo $xRCO['comprexx'] ?>";
          document.forms['frgrm']['cCscInicial'].value  = "<?php echo $vUltCsc['comcscxx'] ?>";
          document.forms['frgrm']['cPerAno'].value      = "<?php echo substr($vUltCsc['peranoxx'],-2) ?>";
          document.forms['frgrm']['cComTcoEdit'].value  = "<?php echo $xRCO['comtcoxx'] ?>";
          document.forms['frgrm']['cComCcoEdit'].value  = "<?php echo $xRCO['comccoxx'] ?>";
          document.forms['frgrm']['dFecCre'].value      = "<?php echo $xRCO['regfcrex'] ?>";
          document.forms['frgrm']['cHorCre'].value      = "<?php echo $xRCO['reghcrex'] ?>";
          document.forms['frgrm']['dFecMod'].value      = "<?php echo $xRCO['regfmodx'] ?>";
          document.forms['frgrm']['cHorMod'].value      = "<?php echo $xRCO['reghmodx'] ?>";
          document.forms['frgrm']['cEstado'].value      = "<?php echo $xRCO['regestxx'] ?>";
        </script>
        <?php
        if ($xRCO['comtcoxx']=="AUTOMATICO" || $xRCO['comtcoxx']=="") { ?>
          <script language = "javascript">
            document.forms['frgrm']['rBtAut'].checked = true;
            fnLimpiaChecks('AUTOMATICO');
            fnHabilitaConsecutivos('AUTOMATICO');
          </script> <?php

          switch ($xRCO['comccoxx']) {
            case "MENSUAL": 
              //Se quitan 
              ?>
              <script language = "javascript">
                document.forms['frgrm']['vChMensu'].checked  = true;
                document.forms['frgrm']['cCscInicial'].value = "<?php echo $vUltCsc['comcscxx'] ?>";
                document.forms['frgrm']['cCscAnt'].value     = "<?php echo $vUltCsc['comcscxx'] ?>";
                fnLimpiaChecks1('MENSUAL');
              </script> <?php
            break;
            case "ANUAL": ?>
              <script language = "javascript">
                document.forms['frgrm']['vChAnual'].checked  = true;
                document.forms['frgrm']['cCscInicial'].value = "<?php echo $vUltCsc['comcscxx'] ?>";
                document.forms['frgrm']['cCscAnt'].value     = "<?php echo $vUltCsc['comcscxx'] ?>";
                fnLimpiaChecks1('ANUAL');
              </script> <?php
            break;
              case "INDEFINIDO": ?>
              <script language = "javascript">
                document.forms['frgrm']['vChIndef'].checked  = true;
                document.forms['frgrm']['cCscInicial'].value = "<?php echo $vUltCsc['comcscxx'] ?>";
                document.forms['frgrm']['cCscAnt'].value     = "<?php echo $vUltCsc['comcscxx'] ?>";
                fnLimpiaChecks1('INDEFINIDO');
              </script> <?php
            break;
            default: ?>
              <script language = "javascript">
                document.forms['frgrm']['vChMensu'].checked  = true;
                document.forms['frgrm']['cCscInicial'].value = "<?php echo $vUltCsc['comcscxx'] ?>";
                document.forms['frgrm']['cCscAnt'].value     = "<?php echo $vUltCsc['comcscxx'] ?>";
                fnLimpiaChecks1('MENSUAL');
              </script> <?php
            break;
          }
        } else { ?>
          <script language = "javascript">
            document.forms['frgrm']['cCscAnt'].value = "<?php echo $vUltCsc['comcscxx'] ?>";
            document.forms['frgrm']['rBtMan'].checked = true;
            fnHabilitaConsecutivos('MANUAL');
          </script>
        <?php }
        // Fin Prendiendo Checks para Control de consecutivos
      }
    } ?>
  </body>
</html>

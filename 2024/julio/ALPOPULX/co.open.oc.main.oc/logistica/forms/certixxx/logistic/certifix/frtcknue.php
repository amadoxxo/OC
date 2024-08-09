<?php
  /**
   * Formulario Nuevo Ticket.
   * --- Descripcion: Permite Crear una Nuevo Ticket
   * @author Elian Amado. elian.amado@openits.co
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");
  include("../../../../../logistica/libs/php/utiworkf.php");

  $verTickets = new cTickets();
  $cabecera = $verTickets->fnCabeceraTickets($_GET['ticketId']);
  $detalle  = $verTickets->fnDetalleTickets($_GET['ticketId']);

?>
<html>
  <head>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
    <script languaje = "javascript">
      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      }

      /**
       * Permite abrir los valid/windows para las diferentes parametricas.
       */
      function fnLinks(xLink, xSwitch, nSecuencia = "", xGrid = "") {
        var nX = screen.width;
        var nY = screen.height;
        switch (xLink) {
          case "cTtiCod":
            if (xSwitch == "VALID") {
              var zRuta = "frtck158.php?gWhat=VALID"+
                                        "&gFunction=cTtiCod"+
                                        "&gTtiCod="+document.forms['frgrm']['cTtiCod'].value.toUpperCase();

              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frtck158.php?gWhat=WINDOW"+
                                          "&gFunction=cTtiCod"+
                                          "&gTtiCod="+document.forms['frgrm']['cTtiCod'].value.toUpperCase();

              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cTtiDes":
            if (xSwitch == "VALID") {
              var zRuta  = "frtck158.php?gWhat=VALID"+
                                        "&gFunction=cTtiDes"+
                                        "&gTtiDes="+document.forms['frgrm']['cTtiDes'].value.toUpperCase();

              parent.fmpro.location = zRuta;
            } else {
              var zNx     = (nX-600)/2;
              var zNy     = (nY-250)/2;
              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
              var zRuta   = "frtck158.php?gWhat=WINDOW"+
                                    "&gFunction=cTtiDes"+
                                    "&gTtiDes="+document.forms['frgrm']['cTtiDes'].value.toUpperCase();

              zWindow = window.open(zRuta,"zWindow",zWinPro);
              zWindow.focus();
            }
          break;
          case "cResTck":
            var zRuta = "frtckgri.php?gTtiCod="+document.forms['frgrm']['cTtiCod'].value.toUpperCase();
            parent.fmpro.location = zRuta;
          break;
        }
      }

    </script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="700">
        <tr>
          <td>
            <fieldset>
              <legend><?php echo $_COOKIE['kMenDes'] ?></legend>
              <form name = "frestado" action = "frtckgra.php" method = "post" target="fmpro">
                <input type = "hidden" name = "cCerId"       value = "">
                <input type = "hidden" name = "cAnio"        value = "">
              </form>
              <form name = "frgrm" action = "frtckgra.php" method = "post" target="fmpro">
                <input type = "hidden" name = "cCerId"    value = "<?php echo $cCerId ?>">
                <input type = "hidden" name = "cComCod"   value = "<?php echo $cComCod ?>">
                <input type = "hidden" name = "cComCsc2"  value = "<?php echo $cComCsc2 ?>">
                <input type = "hidden" name = "cAnio"     value = "<?php echo $cAnio ?>">
                <input type = "hidden" name = "cRegEst"   value = "">
                <?php
                    // Obtengo los datos del usuario
                    $qUsrNom  = "SELECT USRIDXXX, USRNOMXX, USREMAXX ";
                    $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
                    $qUsrNom .= "WHERE ";
                    $qUsrNom .= "USRIDXXX = \"{$_COOKIE['kUsrId']}\" AND ";
                    $qUsrNom .= "REGESTXX = \"ACTIVO\"";
                    $xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
                    if (mysql_num_rows($xUsrNom) > 0) {
                      $vUsrNom = mysql_fetch_array($xUsrNom);
                      $usrId   = $vUsrNom['USRIDXXX']; // Cod Usuario
                      $usrNom  = $vUsrNom['USRNOMXX']; // Nombre
                      $usrEma  = $vUsrNom['USREMAXX']; // Email
                    }
                    // Obtengo los datos de Prioridad Ticket
                    $mMatrizPti = array();
                    $qPtiCox  = "SELECT pticodxx, ptidesxx ";
                    $qPtiCox .= "FROM $cAlfa.lpar0156 ";
                    $qPtiCox .= "WHERE ";
                    $qPtiCox .= "regestxx = \"ACTIVO\"";
                    $xPtiCox  = f_MySql("SELECT","",$qPtiCox,$xConexion01,"");
                    if (mysql_num_rows($xPtiCox) > 0) {
                      while ($vPtiCox = mysql_fetch_array($xPtiCox)) {
                        $nInd_mMatrizPti = count($mMatrizPti);
                        $mMatrizPti[$nInd_mMatrizPti]['pticodxx'] = $vPtiCox['pticodxx'];
                        $mMatrizPti[$nInd_mMatrizPti]['ptidesxx'] = $vPtiCox['ptidesxx'];
                      }
                    }
                    // Obtengo los datos de Status Ticket
                    $mMatrizSti = array();
                    $qStiCod  = "SELECT sticodxx, stidesxx ";
                    $qStiCod .= "FROM $cAlfa.lpar0157 ";
                    $qStiCod .= "WHERE ";
                    $qStiCod .= "stitipxx = \"APERTURA\" AND ";
                    $qStiCod .= "regestxx = \"ACTIVO\"";
                    $xStiCod  = f_MySql("SELECT","",$qStiCod,$xConexion01,"");
                    if (mysql_num_rows($xStiCod) > 0) {
                      while ($vStiCod = mysql_fetch_array($xStiCod)) {
                        $nInd_mMatrizSti = count($mMatrizSti);
                        $mMatrizSti[$nInd_mMatrizSti]['sticodxx'] = $vStiCod['sticodxx'];
                        $mMatrizSti[$nInd_mMatrizSti]['stidesxx'] = $vStiCod['stidesxx'];
                      }
                    }
                ?>
                <center>
                  <table border="0" cellpadding="0" cellspacing="0" width="700">
                    <?php $nCol = f_Format_Cols(35); echo $nCol; ?>
                    <!-- Seccion 1 -->
                    <!-- Fila 1 -->
                    <tr>
                      <td Class="clase08" colspan="1">Id<br>
                        <input type = "text" Class = "letra" style = "width:20" name = "cComId" value="<?php echo $cComId ?>" readonly>
                      </td>
                      <td Class="clase08" colspan="3">Prefijo<br>
                        <input type = 'text' Class = 'letra' style = 'width:60' name = 'cComPre' value="<?php echo $cComPre ?>" readonly>
                      </td>
                      <td class="clase08" colspan="7">Consecutivo<br>
                        <input type = "text" Class = "letra" style = "width:140" name = "cComCsc" value="<?php echo $cComCsc ?>" readonly>
                      </td>
                      <td class="clase08" colspan="1"><br>
                        <input type = "text" Class = "letra" style = "width:20;" readonly>
                      </td>
                      <td Class="clase08" colspan="5">Nit<br>
                        <input type = "text" Class = "letra" style = "width:100" name = "cCliId" value="<?php echo $cCliId ?>" readonly>
                      </td>
                      <td class="clase08" colspan="1">Dv<br>
                        <input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cCliDV" value="<?php echo gendv($cCliId) ?>" readonly>
                      </td>
                      <td class="clase08" colspan="12">Cliente<br>
                        <input type = "text" Class = "letra" style = "width:240" name = "cCliNom" value="<?php echo $cCliNom ?>" readonly>
                      </td>
                      <td class="clase08" colspan="5">Fecha<br>
                        <input type="text" Class = "letra" name="cComFec" style = "width:100;text-align:center" value = "<?php echo $cComFec ?>" readonly>
                      </td>
                    </tr>
                    <!-- Fila 2 -->
                    <tr>
                      <td class = "clase08" colspan="5">Creado por<br>
                        <input type = "text" Class = "letra" style = "width:100" name = "cUsrId" value = "<?php echo $usrId ?>" readonly>
                      </td>
                      <td Class="clase08" colspan="15"><br>
                        <input type = "text" Class = "letra" style = "width:300" value = "<?php echo $usrNom ?>" readonly>
                      </td>
                      <td Class="clase08" colspan="15"><br>
                        <input type = "text" Class = "letra" style = "width:300" name = "cUsrEma" value = "<?php echo $usrEma ?>" readonly>
                      </td>
                    </tr>
                    <!-- Fila 3 -->
                    <tr>
                      <?php if($_COOKIE['kModo'] == "EDITAR"){ ?>
                        <td class="clase08" colspan="5">Ticket<br>
                          <input type="text" class="letra" style="width:100;" name="cTicket" value="<?php echo $cTicId ?>" readonly>
                        </td>
                      <?php } ?>
                      <td class="clase08" colspan="<?php echo ($_COOKIE['kModo'] != 'EDITAR') ? '35' : '30' ?>">Asunto<br>
                        <input type = "text" Class = "letra" style = "width:<?php echo ($_COOKIE['kModo'] != 'EDITAR') ? '700' : '600' ?>" name = "cAsuTck" value = "">
                      </td>
                    </tr>
                    <!-- Fila 4 -->
                    <tr>
                      <td class="clase08" colspan="5">
                        <a href="javascript:document.forms['frgrm']['cTtiCod'].value = '';
                                            document.forms['frgrm']['cTtiDes'].value = '';
                                            fnLinks('cTtiCod','WINDOW')">Tipo</a><br>
                        <input type = "text" Class = "letra" style = "width:100" name = "cTtiCod" id = "cTtiCod"
                          onBlur = "javascript:fnLinks('cTtiCod','WINDOW');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus = "javascript:document.forms['frgrm']['cTtiCod'].value = '';
                                                document.forms['frgrm']['cTtiDes'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class="clase08" colspan="1"><br>
                        <input type = "text" Class = "letra" style = "width:20;" readonly>
                      </td>
                      <td class="clase08" colspan="15"><br>
                        <input type = "text" Class = "letra" style = "width:300" name = "cTtiDes"
                          onBlur = "javascript:this.value=this.value.toUpperCase();
                                              fnLinks('cTtiDes','WINDOW');
                                              this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                          onFocus = "javascript:document.forms['frgrm']['cTtiCod'].value = '';
                                                document.forms['frgrm']['cTtiDes'].value = '';
                                                this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                      </td>
                      <td class = "clase08" colspan="7">Prioridad<br>
                        <select name = "cPriori" style = "width:140">
                          <option>[SELECCIONE]</option>
                          <?php for($i=0;$i<count($mMatrizPti);$i++){ ?>
                            <option value="<?php echo $mMatrizPti[$i]['pticodxx'] ?>"><?php echo $mMatrizPti[$i]['ptidesxx'] ?></option>
                          <?php } ?>
                        </select>
                      </td>
                      <td class = "clase08" colspan="7">Estado<br>
                        <select name = "cEstado" style = "width:140">
                          <?php for($i=0;$i<count($mMatrizSti);$i++){ ?>
                            <option value="<?php echo $mMatrizSti[$i]['sticodxx'] ?>"><?php echo $mMatrizSti[$i]['stidesxx'] ?></option>
                          <?php } ?>
                        </select>
                      </td>
                    </tr>
                    <!-- Responsables Asignados al Tipo de Ticket -->
                    <tr>
                      <td Class = "clase08" colspan="35">
                        <fieldset id = "serviciosAutomaticos">
                          <legend>Responsables Asignados al Tipo de Ticket</legend>
                          <div id = "overDivResponsable"></div>
                        </fieldset>
                      </td>
                    </tr>
                    <!-- Correos en Copia -->
                    <tr>
                      <td Class = "clase08" colspan = "35">Correos en Copia (separados por coma)<br>
                        <input type="text" Class = "letra" name = "cCliPCECn" id = "" style = 'width:700'>
                      </td>
                    </tr>
                    <!-- Contenido -->
                    <tr>
                      <td Class = "clase08" colspan = "35">Contenido<br>
                          <textarea Class="letra" name="cConten" style="width:700;height:100;" onblur="javascript:this.value=this.value.toUpperCase();"></textarea>
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
      <table border="0" cellpadding="0" cellspacing="0" width="700">
        <tr height="21">
          <?php switch ($_COOKIE['kModo']) {
            case "VER": ?>
              <td width="889" height="21"></td>
              <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
            default: ?>
              <td width="700" height="21"></td>
              <td width="98" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:document.forms['frgrm'].submit();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
              <td width="105" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
            <?php break;
          } ?>
        </tr>
      </table>
    </center>
    <?php
    switch ($_COOKIE['kModo']) {
      case "EDITAR":
        $cabecera = $verTickets->fnCabeceraTickets($cTicId);
        $detalle  = $verTickets->fnDetalleTickets($cTicId);

        ?>
        <script languaje = "javascript">
          // Deshabilito los campos de cabecera
          document.forms['frgrm']['cAsuTck'].value   = '<?php echo $cabecera['ticasuxx'] ?>';
          document.forms['frgrm']['cTtiCod'].value   = '<?php echo $cabecera['tticodxx'] ?>';
          document.forms['frgrm']['cTtiDes'].value   = '<?php echo $cabecera['ttidesxx'] ?>';
          document.forms['frgrm']['cPriori'].value   = '<?php echo $cabecera['pticodxx']?>';
          document.forms['frgrm']['cEstado'].value   = '<?php echo $cabecera['sticodxx']?>';

          document.forms['frgrm']['cAsuTck'].readOnly = true;
          document.forms['frgrm']['cTtiCod'].readOnly = true;
          document.forms['frgrm']['cTtiDes'].readOnly = true;
          document.forms['frgrm']['cPriori'].readOnly = true;
          document.forms['frgrm']['cEstado'].readOnly = true;
        </script>
      <?php
      break;
    }
    ?>
  </body>
</html>

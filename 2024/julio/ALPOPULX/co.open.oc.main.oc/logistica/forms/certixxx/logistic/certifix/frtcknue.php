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

      /**
       * Valida el tipo de certificacion seleccionado para habilitar y deshabilitar campos dependiendo si es MANUAL/AUTOMATICO.
       */
      function fnCambiaTipoCertificacion(value) {
        if (value == "AUTOMATICA") {
          document.forms['frgrm']['cPerAno'].disabled    = false;
          document.forms['frgrm']['cMifComCsc'].disabled = false;
          document.forms['frgrm']['dVigDesde'].readOnly  = true;
          document.forms['frgrm']['dVigHasta'].readOnly  = true;
          document.forms['frgrm']['cDepNum'].disabled    = true;
          document.forms['frgrm']['cTipoDep'].readOnly   = true;
          document.getElementById('id_href_dVigDesde').removeAttribute('href');
          document.getElementById('id_href_dVigHasta').removeAttribute('href');
          document.getElementById('id_href_DepNum').removeAttribute('href');

          document.getElementById('serviciosAutomaticos').style.display = "block";
          document.getElementById('serviciosManual').style.display = "none";
          document.getElementById('Grid_Servicios').innerHTML = "";
          document.getElementById('Grid_Certificacion').innerHTML = "";
          document.forms['frgrm']['nSecuencia_Servicio'].value = 0;

        } else {
          document.forms['frgrm']['cPerAno'].disabled    = true;
          document.forms['frgrm']['cMifComCsc'].disabled = true;
          document.forms['frgrm']['dVigDesde'].readOnly  = false;
          document.forms['frgrm']['dVigHasta'].readOnly  = false;
          document.forms['frgrm']['cDepNum'].disabled    = false;
          document.forms['frgrm']['cTipoDep'].readOnly   = false;
          document.getElementById('id_href_dVigDesde').setAttribute('href', "javascript:show_calendar(\"frgrm.dVigDesde\")");
          document.getElementById('id_href_dVigHasta').setAttribute('href', "javascript:show_calendar(\"frgrm.dVigHasta\")");
          document.getElementById('id_href_DepNum').setAttribute('href', "javascript:document.forms[\"frgrm\"][\"cDepNum\"].value = \"\"; document.forms[\"frgrm\"][\"cTipoDep\"].value = \"\";fnLinks(\"cDepNum\",\"VALID\")");
        
          document.getElementById('serviciosAutomaticos').style.display = "none";
          document.getElementById('overDivSubServicios').innerHTML = "";
          document.getElementById('serviciosManual').style.display = "block";
          fnAddNewRowServicio('Grid_Servicios');
          document.getElementById('Grid_Certificacion').innerHTML = "";
          document.forms['frgrm']['nSecuencia_Certificacion'].value = 0;
        }
      }

      /**
       * Habilita la seccion de servicios automaticos cuando se carga la MIF.
       */
      function fnHabilitaServicios(xValidaExisteSubservicio = '') {
        fnCargarGrillaSubServicio(xValidaExisteSubservicio);
        document.getElementById('serviciosAutomaticos').style.display = "block";
        document.getElementById('Grid_Certificacion').innerHTML = "";
      }

      /**
       * Permite habilitar o deshabilitar los campos de la seccion de certificacion.
       */
      function fnActivarDesactivarCamposCertificacion(xIndice, xAccion) {
        if (xAccion == true) {
          document.forms['frgrm']['cDesMaterial'  + xIndice].readOnly = true;
          document.forms['frgrm']['cCodSapSer'    + xIndice].readOnly = true;
          document.forms['frgrm']['cObfDes_Certi' + xIndice].readOnly = true;
          document.forms['frgrm']['cUfaDes_Certi' + xIndice].readOnly = true;
          document.forms['frgrm']['cBase'         + xIndice].readOnly = true;
          document.forms['frgrm']['cStatus'       + xIndice].readOnly = true;
          document.forms['frgrm']['oBtnDel_Certi' + xIndice].disabled = true;
        } else {
          document.forms['frgrm']['cDesMaterial'  + xIndice].readOnly = false;
          document.forms['frgrm']['cCodSapSer'    + xIndice].readOnly = false;
          document.forms['frgrm']['cObfDes_Certi' + xIndice].readOnly = false;
          document.forms['frgrm']['cUfaDes_Certi' + xIndice].readOnly = false;
          document.forms['frgrm']['cBase'         + xIndice].readOnly = false;
          document.forms['frgrm']['cStatus'       + xIndice].readOnly = false;
          document.forms['frgrm']['oBtnDel_Certi' + xIndice].disabled = false;
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
        $verTickets = new cTickets();
        $cabecera = $verTickets->fnCabeceraTickets($cTicId);
        $detalle  = $verTickets->fnDetalleTickets($cTicId);

        ?>
        <script languaje = "javascript">
          // Deshabilito los campos de cabecera
          document.forms['frgrm']['cAsuTck'].value   = '<?php echo $cabecera['ticasuxx'] ?>';
          document.forms['frgrm']['cTtiCod'].value   = '<?php echo $detalle[0]['tticodxx'] ?>';
          document.forms['frgrm']['cTtiDes'].value   = '<?php echo $cabecera['ttidesxx'] ?>';
          document.forms['frgrm']['cPriori'].value   = '<?php echo $detalle[0]['pticodxx']?>';
          document.forms['frgrm']['cEstado'].value   = '<?php echo $detalle[0]['sticodxx']?>';
          document.forms['frgrm']['cCliPCECn'].value = '<?php echo $detalle[0]['ticccopx']?>';

          document.forms['frgrm']['cAsuTck'].readOnly = true;
          document.forms['frgrm']['cTtiCod'].readOnly = true;
          document.forms['frgrm']['cTtiDes'].readOnly = true;
          document.forms['frgrm']['cPriori'].readOnly = true;
          document.forms['frgrm']['cEstado'].readOnly = true;
          // document.forms['frgrm']['cComCsc'].onblur     = "";
          // document.forms['frgrm']['cCliId'].onfocus     = "";
          // document.forms['frgrm']['cCliId'].onblur      = "";
          // document.forms['frgrm']['cCliNom'].onfocus    = "";
          // document.forms['frgrm']['cCliNom'].onblur     = "";
          // document.forms['frgrm']['cDepNum'].onfocus    = "";
          // document.forms['frgrm']['cDepNum'].onblur     = "";
          // document.forms['frgrm']['cCdiSap'].onfocus    = "";
          // document.forms['frgrm']['cCdiSap'].onblur     = "";
          // document.forms['frgrm']['cCdiDes'].onfocus    = "";
          // document.forms['frgrm']['cCdiDes'].onblur     = "";

          // Deshabilito los link de los Valid/Windows
          // document.getElementById('id_href_cCompre').removeAttribute('href');
          // document.getElementById('id_href_CliId').removeAttribute('href');
          // document.getElementById('id_href_DepNum').removeAttribute('href');
          // document.getElementById('id_href_dVigDesde').removeAttribute('href');
          // document.getElementById('id_href_dVigHasta').removeAttribute('href');
          // document.getElementById('id_href_CdiSap').removeAttribute('href');
        </script>
      <?php
      break;
    }

    function fnCargaData($gCerId, $gAnio){
      global $cAlfa; global $xConexion01;

      // Consulta la informacion del comprobante seleccionado
      $qCertificacion  = "SELECT ";
      $qCertificacion .= "$cAlfa.lcca$gAnio.*, ";
      $qCertificacion .= "$cAlfa.lcca$gAnio.cliidxxx,";
      $qCertificacion .= "$cAlfa.lpar0150.clisapxx, ";
      $qCertificacion .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) AS clinomxx, ";
      $qCertificacion .= "$cAlfa.lpar0008.cdisapxx, ";
      $qCertificacion .= "$cAlfa.lpar0008.cdidesxx ";
      $qCertificacion .= "FROM $cAlfa.lcca$gAnio ";
      $qCertificacion .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lcca$gAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
      $qCertificacion .= "LEFT JOIN $cAlfa.lpar0008 ON $cAlfa.lcca$gAnio.cdisapxx = $cAlfa.lpar0008.cdisapxx ";
      $qCertificacion .= "WHERE ";
      $qCertificacion .= "$cAlfa.lcca$gAnio.ceridxxx = \"$gCerId\" LIMIT 0,1 ";
      $xCertificacion  = f_MySql("SELECT","",$qCertificacion,$xConexion01,"");
      // echo $qCertificacion . " - " .  mysql_num_rows($xCertificacion);
      if (mysql_num_rows($xCertificacion) > 0) {
        $vCertificacion = mysql_fetch_array($xCertificacion);

        // Consulta la MIF en caso de haber sido guardada
        $vMatrizInsu = array();
        if ($vCertificacion['mifidxxx'] != "") {
          $cAnioMif = $vCertificacion['mifidano'];
          $qMatrizInsu  = "SELECT ";
          $qMatrizInsu .= "$cAlfa.lmca$cAnioMif.* ";
          $qMatrizInsu .= "FROM $cAlfa.lmca$cAnioMif ";
          $qMatrizInsu .= "WHERE ";
          $qMatrizInsu .= "$cAlfa.lmca$cAnioMif.mifidxxx = {$vCertificacion['mifidxxx']} LIMIT 0,1";
          $xMatrizInsu  = f_MySql("SELECT","",$qMatrizInsu,$xConexion01,"");
          // echo $qMatrizInsu . " - " .  mysql_num_rows($xMatrizInsu);
          if (mysql_num_rows($xMatrizInsu) > 0) {
            $vMatrizInsu = mysql_fetch_array($xMatrizInsu);
          }
        }

        // Consulta la información del Depósito
        $vDeposito  = array();
        $qDeposito  = "SELECT ";
        $qDeposito .= "lpar0155.depnumxx, ";
        $qDeposito .= "lpar0155.ccoidocx, ";
        $qDeposito .= "lpar0007.tdeidxxx, ";
        $qDeposito .= "lpar0007.tdedesxx, ";
        $qDeposito .= "lpar0001.orvsapxx, ";
        $qDeposito .= "lpar0001.orvdesxx, ";
        $qDeposito .= "lpar0002.ofvsapxx, ";
        $qDeposito .= "lpar0002.ofvdesxx, ";
        $qDeposito .= "lpar0003.closapxx, ";
        $qDeposito .= "lpar0003.clodesxx, ";
        $qDeposito .= "lpar0009.secsapxx, ";
        $qDeposito .= "lpar0009.secdesxx, ";
        $qDeposito .= "lpar0155.regestxx ";
        $qDeposito .= "FROM $cAlfa.lpar0155 ";
        $qDeposito .= "LEFT JOIN $cAlfa.lpar0007 ON $cAlfa.lpar0155.tdeidxxx = $cAlfa.lpar0007.tdeidxxx ";
        $qDeposito .= "LEFT JOIN $cAlfa.lpar0001 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0001.orvsapxx ";
        $qDeposito .= "LEFT JOIN $cAlfa.lpar0002 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0002.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0002.ofvsapxx ";
        $qDeposito .= "LEFT JOIN $cAlfa.lpar0003 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0003.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0003.ofvsapxx AND $cAlfa.lpar0155.closapxx = $cAlfa.lpar0003.closapxx ";
        $qDeposito .= "LEFT JOIN $cAlfa.lpar0009 ON $cAlfa.lpar0155.secsapxx = $cAlfa.lpar0009.secsapxx ";
        $qDeposito .= "WHERE ";
        $qDeposito .= "lpar0155.depnumxx = \"{$vCertificacion['depnumxx']}\"";
        $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
        if (mysql_num_rows($xDeposito) > 0) {
          $vDeposito = mysql_fetch_array($xDeposito);
        }

        // Consultando informacion del detalle de la certificacion
        $qCertifiDet  = "SELECT ";
        $qCertifiDet .= "$cAlfa.lcde$gAnio.*, ";
        $qCertifiDet .= "$cAlfa.lpar0004.obfidxxx, ";
        $qCertifiDet .= "$cAlfa.lpar0004.obfdesxx, ";
        $qCertifiDet .= "$cAlfa.lpar0006.ufaidxxx, ";
        $qCertifiDet .= "$cAlfa.lpar0006.ufadesxx, ";
        $qCertifiDet .= "$cAlfa.lpar0010.cebidxxx, ";
        $qCertifiDet .= "$cAlfa.lpar0010.cebcodxx, ";
        $qCertifiDet .= "$cAlfa.lpar0010.cebdesxx, ";
        $qCertifiDet .= "IF($cAlfa.lcde$gAnio.sersapxx != \"\",$cAlfa.lpar0011.serdesxx,\"\") AS serdesxx ";
        $qCertifiDet .= "FROM $cAlfa.lcde$gAnio ";
        $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0006 ON $cAlfa.lcde$gAnio.ufaidxxx = $cAlfa.lpar0006.ufaidxxx ";
        $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0004 ON $cAlfa.lcde$gAnio.obfidxxx = $cAlfa.lpar0004.obfidxxx ";
        $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0010 ON $cAlfa.lcde$gAnio.cebidxxx = $cAlfa.lpar0010.cebidxxx ";
        $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0011 ON $cAlfa.lcde$gAnio.sersapxx = $cAlfa.lpar0011.sersapxx ";
        $qCertifiDet .= "WHERE ";
        $qCertifiDet .= "$cAlfa.lcde$gAnio.ceridxxx = \"$gCerId\" ";
        $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
        // echo $qCertifiDet;

        ?>
        <script language = "javascript">
          fnCambiaTipoCertificacion('<?php echo $vCertificacion['certipxx'] ?>');

          document.forms['frgrm']['cComId'].value       = "<?php echo $vCertificacion['comidxxx'] ?>";
          document.forms['frgrm']['cComCod'].value      = "<?php echo $vCertificacion['comcodxx'] ?>";
          document.forms['frgrm']['cComPre'].value      = "<?php echo $vCertificacion['comprexx'] ?>";
          document.forms['frgrm']['cComCsc'].value      = "<?php echo $vCertificacion['comcscxx'] ?>";
          document.forms['frgrm']['cCliId'].value       = "<?php echo $vCertificacion['cliidxxx'] ?>";
          document.forms['frgrm']['cCliDV'].value       = "<?php echo gendv($vCertificacion['cliidxxx']) ?>";
          document.forms['frgrm']['cCliNom'].value      = "<?php echo $vCertificacion['clinomxx'] ?>";
          document.forms['frgrm']['cCliSap'].value      = "<?php echo $vCertificacion['clisapxx'] ?>";
          document.forms['frgrm']['dComFec'].value      = "<?php echo $vCertificacion['comfecxx'] ?>";
          document.forms['frgrm']['tComHCre'].value     = "<?php echo $vCertificacion['comhorxx'] ?>";
          document.forms['frgrm']['cCerTip'].value      = "<?php echo $vCertificacion['certipxx'] ?>";
          document.forms['frgrm']['cRegEst'].value      = "<?php echo $vCertificacion['regestxx'] ?>";

          // Informacion de la MIF
          document.forms['frgrm']['cPerAno'].value      = "<?php echo $vCertificacion['mifidano'] ?>";
          document.forms['frgrm']['cMifId'].value       = "<?php echo $vCertificacion['mifidxxx'] ?>";
          document.forms['frgrm']['cMifComId'].value    = "<?php echo $vMatrizInsu['comidxxx'] ?>";
          document.forms['frgrm']['cMifComCod'].value   = "<?php echo $vMatrizInsu['comcodxx'] ?>";
          document.forms['frgrm']['cMifComCsc'].value   = "<?php echo $vMatrizInsu['comcscxx'] ?>";
          document.forms['frgrm']['cMifComCsc2'].value  = "<?php echo $vMatrizInsu['comcsc2x'] ?>";
          document.forms['frgrm']['dVigDesde'].value    = "<?php echo $vCertificacion['cerfdexx'] ?>";
          document.forms['frgrm']['dVigHasta'].value    = "<?php echo $vCertificacion['cerfhaxx'] ?>";
          // Informacion del Deposito
          document.forms['frgrm']['cDepNum'].value      = "<?php echo $vDeposito['depnumxx'] ?>";
          document.forms['frgrm']['cDepNum_hidd'].value = "<?php echo $vDeposito['depnumxx'] ?>";
          document.forms['frgrm']['cTipoDep'].value     = "<?php echo $vDeposito['tdedesxx'] ?>";
          document.forms['frgrm']['cCcoIdOc'].value     = "<?php echo $vDeposito['ccoidocx'] ?>";
          document.forms['frgrm']['cOrvSap'].value      = "<?php echo $vDeposito['orvsapxx'] ?>";
          document.forms['frgrm']['cOrvDes'].value      = "<?php echo $vDeposito['orvdesxx'] ?>";
          document.forms['frgrm']['cOfvSap'].value      = "<?php echo $vDeposito['ofvsapxx'] ?>";
          document.forms['frgrm']['cOfvDes'].value      = "<?php echo $vDeposito['ofvdesxx'] ?>";
          document.forms['frgrm']['cCloSap'].value      = "<?php echo $vDeposito['closapxx'] ?>";
          document.forms['frgrm']['cCloDes'].value      = "<?php echo $vDeposito['closapxx'] ?>";
          document.forms['frgrm']['cSecSap'].value      = "<?php echo $vDeposito['secsapxx'] ?>";
          document.forms['frgrm']['cSecDes'].value      = "<?php echo $vDeposito['secdesxx'] ?>";
          document.forms['frgrm']['cCdiSap'].value      = "<?php echo $vCertificacion['cdisapxx'] ?>";
          document.forms['frgrm']['cCdiDes'].value      = "<?php echo $vCertificacion['cdidesxx'] ?>";
          document.forms['frgrm']['cCerTipMe'].value    = "<?php echo $vCertificacion['certipme'] ?>";
          document.forms['frgrm']['cCerObs'].value      = "<?php echo $vCertificacion['cerobsxx'] ?>";

          // Grilla Datos de Servicios a Certificar - AUTOMATICOS
          if ("<?php echo $vCertificacion['certipxx'] ?>" == "AUTOMATICA") {
            fnHabilitaServicios('SI');
          }

          // Grilla Datos de Servicios y Certificacion  - MANUAL
          <?php 
          $nCountCert = 0;
          $nCountServ = 0;
          if (mysql_num_rows($xCertifiDet) > 0) {
            while ($xRCD = mysql_fetch_array($xCertifiDet)) {
              $nCountCert++;
              
              if ($xRCD['cerdestx'] != "AUTOMATICO") {
                if ($xRCD['sersapxx'] != "") {
                  $nCountServ++;
                  if ($nCountServ > 1) { ?>
                    fnAddNewRowServicio('Grid_Servicios');
                  <?php } ?>
                  document.forms['frgrm']['cSerSap'+'<?php echo $nCountServ ?>'].value = '<?php echo $xRCD['sersapxx'] ?>';
                  document.forms['frgrm']['cSerDes'+'<?php echo $nCountServ ?>'].value = '<?php echo $xRCD['serdesxx'] ?>';
                  document.forms['frgrm']['cSubId'+'<?php echo $nCountServ ?>'].value  = '<?php echo $xRCD['subidxxx'] ?>';
                  document.forms['frgrm']['cSubDes'+'<?php echo $nCountServ ?>'].value = '<?php echo $xRCD['subdesxx'] ?>';
                  document.forms['frgrm']['cUfaId'+'<?php echo $nCountServ ?>'].value  = '<?php echo $xRCD['ufaidxxx'] ?>';
                  document.forms['frgrm']['cUfaDes'+'<?php echo $nCountServ ?>'].value = '<?php echo $xRCD['ufadesxx'] ?>';
                  <?php 
                  if ($_COOKIE['kModo'] == "VER") { ?>
                    document.forms['frgrm']['oBtnDel'+'<?php echo $nCountServ ?>'].disabled = true;
                  <?php }
                } 
                ?>

                // Asigna los valores a la grilla de Certificacion
                fnAddNewRowCertificacion('Grid_Certificacion', '<?php echo $nCountCert ?>', '<?php echo $xRCD['cerdorix'] ?>');
                setTimeout(function(){
                  document.forms['frgrm']['cCerdId'+'<?php echo $nCountCert ?>'].value       = '<?php echo $xRCD['cerdidxx'] ?>';
                  document.forms['frgrm']['cDesMaterial'+'<?php echo $nCountCert ?>'].value  = '<?php echo $xRCD['subdesxx'] ?>';
                  document.forms['frgrm']['cIndexServ'+'<?php echo $nCountCert ?>'].value    = '<?php echo $nCountServ ?>';
                  document.forms['frgrm']['cCodSapSer'+'<?php echo $nCountCert ?>'].value    = '<?php echo $xRCD['sersapxx'] ?>';
                  document.forms['frgrm']['cSubId_Certi'+'<?php echo $nCountCert ?>'].value  = '<?php echo $xRCD['subidxxx'] ?>';
                  document.forms['frgrm']['cObfId_Certi'+'<?php echo $nCountCert ?>'].value  = '<?php echo $xRCD['obfidxxx'] ?>';
                  document.forms['frgrm']['cObfDes_Certi'+'<?php echo $nCountCert ?>'].value = '<?php echo $xRCD['obfdesxx'] ?>';
                  document.forms['frgrm']['cUfaId_Certi'+'<?php echo $nCountCert ?>'].value  = '<?php echo $xRCD['ufaidxxx'] ?>';
                  document.forms['frgrm']['cUfaDes_Certi'+'<?php echo $nCountCert ?>'].value = '<?php echo $xRCD['ufadesxx'] ?>';
                  document.forms['frgrm']['cCebId'+'<?php echo $nCountCert ?>'].value        = '<?php echo $xRCD['cebidxxx'] ?>';
                  document.forms['frgrm']['cCebCod'+'<?php echo $nCountCert ?>'].value       = '<?php echo $xRCD['cebcodxx'] ?>';
                  document.forms['frgrm']['cCebDes'+'<?php echo $nCountCert ?>'].value       = '<?php echo $xRCD['cebdesxx'] ?>';
                  document.forms['frgrm']['cBase'+'<?php echo $nCountCert ?>'].value         = '<?php echo $xRCD['basexxxx'] ?>';
                  document.forms['frgrm']['cCondicion'+'<?php echo $nCountCert ?>'].value    = '<?php echo $xRCD['cerdconx'] ?>';
                  document.forms['frgrm']['cStatus'+'<?php echo $nCountCert ?>'].value       = '<?php echo $xRCD['cerdestx'] ?>';
                  document.forms['frgrm']['cTipoCerti'+'<?php echo $nCountCert ?>'].value    = '<?php echo $xRCD['cerdorix'] ?>';
                }, 500);

                <?php if ($_COOKIE['kModo'] == "VER") { ?>
                  fnActivarDesactivarCamposCertificacion('<?php echo $nCountCert ?>', true);
                <?php }
              }
            }
          }
          ?>
        </script>
      <?php
      }
    } ?>
  </body>
</html>

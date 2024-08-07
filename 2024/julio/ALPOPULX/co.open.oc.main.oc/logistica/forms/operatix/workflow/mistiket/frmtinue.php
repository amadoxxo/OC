<?php

/**
 * Formulario Editar Ticket.
 * --- Descripcion: Permite Editar Ticket
 * @author Cristian Perdomo. cristian.perdomo@openits.co
 * @package opencomex
 * @version 001
 */


include("../../../../../financiero/libs/php/utility.php");
include("../../../../../logistica/libs/php/utiworkf.php");

//cargo data del ticket en el utiworkf.php
$mTicket = new cTickets();
$ticketCabecera = $mTicket->fnCabeceraTickets($_GET['ticketId']);
$replys = $mTicket->fnDetalleTickets($_GET['ticketId']);
?>
<html>

<head>
  <LINK rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
  <script languaje="javascript" src="<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
  <script languaje="javascript" src="<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
  <script languaje="javascript">
    function fnRetorna() {
      window.close();
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
            var zRuta = "frmtitip.php?gWhat=VALID" +
              "&gFunction=cTtiCod" +
              "&gTtiCod=" + document.forms['frgrm']['cTtiCod'].value.toUpperCase();


            parent.fmpro.location = zRuta;
          } else {
            var zNx = (nX - 600) / 2;
            var zNy = (nY - 250) / 2;
            var zWinPro = 'width=600,scrollbars=1,height=250,left=' + zNx + ',top=' + zNy;
            var zRuta = "frmtitip.php?gWhat=WINDOW" +
              "&gFunction=cTtiCod" +
              "&gTtiCod=" + document.forms['frgrm']['cTtiCod'].value.toUpperCase();


            zWindow = window.open(zRuta, "zWindow", zWinPro);
            zWindow.focus();
          }
          break;
        case "cTtiDes":
          if (xSwitch == "VALID") {
            var zRuta = "frmtitip.php?gWhat=VALID" +
              "&gFunction=cTtiDes" +
              "&gTtiDes=" + document.forms['frgrm']['cTtiDes'].value.toUpperCase();


            parent.fmpro.location = zRuta;
          } else {
            var zNx = (nX - 600) / 2;
            var zNy = (nY - 250) / 2;
            var zWinPro = 'width=600,scrollbars=1,height=250,left=' + zNx + ',top=' + zNy;
            var zRuta = "frmtitip.php?gWhat=WINDOW" +
              "&gFunction=cTtiDes" +
              "&gTtiDes=" + document.forms['frgrm']['cTtiDes'].value.toUpperCase();


            zWindow = window.open(zRuta, "zWindow", zWinPro);
            zWindow.focus();
          }
          break;
        case "cResTck":
          /* var zRuta = "frmtigri.php?gTtiCod=" + document.forms['frgrm']['cTtiCod'].value.toUpperCase();
          parent.fmpro.location = zRuta; */
          var inputValue = document.getElementById('cTtiCod').value;
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'frmtigri.php', true); // Llama al archivo separado
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('overDivResponsable').innerHTML = xhr.responseText;
                }
            };
            xhr.send('gTtiCod=' + encodeURIComponent(inputValue));
          break;
      }
    }

  </script>
</head>

<body topmargin=0 leftmargin=0 margnwidth=0 marginheight=0 style='margin-right:0'>
  <center>
    <table border="0" cellpadding="0" cellspacing="0" width="700">
      <tr>
        <td>
          <fieldset>
            <legend><?php echo $_COOKIE['kMenDes'] ?></legend>
            <form name="frestado" action="frmtigra.php" method="post" target="fmpro">
              <input type="hidden" name="cCerId" value="">
              <input type="hidden" name="cAnio" value="">
            </form>
            <form name="frgrm" action="frmtigra.php" method="post" target="fmpro">
              <input type="hidden" name="cCerId" value="<?php echo $cCerId ?>">
              <input type="hidden" name="cComCod" value="<?php echo $cComCod ?>">
              <input type="hidden" name="cComCsc2" value="<?php echo $cComCsc2 ?>">
              <input type="hidden" name="cAnio" value="<?php echo $cAnio ?>">
              <input type="hidden" name="cRegEst" value="">
              <input type="hidden" name="cTicket" value="<?php echo $_GET['ticketId'] ?>">
              <?php
              // Obtengo los datos del usuario
              $qUsrNom  = "SELECT USRIDXXX, USRNOMXX, USREMAXX ";
              $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
              $qUsrNom .= "WHERE ";
              $qUsrNom .= "USRIDXXX = \"{$_COOKIE['kUsrId']}\" AND ";
              $qUsrNom .= "REGESTXX = \"ACTIVO\"";
              $xUsrNom  = f_MySql("SELECT", "", $qUsrNom, $xConexion01, "");
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
              $xPtiCox  = f_MySql("SELECT", "", $qPtiCox, $xConexion01, "");
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
              $qStiCod .= "regestxx = \"ACTIVO\"";
              $xStiCod  = f_MySql("SELECT", "", $qStiCod, $xConexion01, "");
              if (mysql_num_rows($xStiCod) > 0) {
                while ($vStiCod = mysql_fetch_array($xStiCod)) {
                  $nInd_mMatrizSti = count($mMatrizSti);
                  $mMatrizSti[$nInd_mMatrizSti]['sticodxx'] = $vStiCod['sticodxx'];
                  $mMatrizSti[$nInd_mMatrizSti]['stidesxx'] = $vStiCod['stidesxx'];
                }
              }

              /* RESPONSABLE O TERCERO */
              $ticketcId = $ticketCabecera['tticodxx'];

              $reprepor = "TERCERO"; // Valor predeterminado

              // Recupera el valor de la cookie
              $cookieUsrId = isset($_COOKIE['kUsrId']) ? $_COOKIE['kUsrId'] : "";
              $mMatrizRes = array();

              $qResTi  = "SELECT ";
              $qResTi .= "tticodxx, ";
              $qResTi .= "ttiusrxx ";
              $qResTi .= "FROM $cAlfa.lpar0159 ";
              $qResTi .= "WHERE ";
              $qResTi .= "tticodxx = \"$ticketcId\" AND ";
              $qResTi .= "regestxx = \"ACTIVO\" ";
              //$qResTi .= "ORDER BY repcscxx DESC ";
              //$qResTi .= "LIMIT 1"; // Asegúra de seleccionar solo el último registro
              $xResTi = f_MySql("SELECT", "", $qResTi, $xConexion01, "");
              if (mysql_num_rows($xResTi) > 0) {
                while ($vResTi = mysql_fetch_array($xResTi)) {
                  $nInd_mMatrizRes = count($mMatrizRes);
                  $mMatrizRes[$nInd_mMatrizRes]['tticodxx'] = $vResTi['tticodxx'];
                  $mMatrizRes[$nInd_mMatrizRes]['ttiusrxx'] = $vResTi['ttiusrxx'];

                  if ($vResTi['ttiusrxx'] === $cookieUsrId) {
                    $reprepor = "RESPONSABLE";
                  }
                }
              }
              ?>
              <center>
                <table border="0" cellpadding="0" cellspacing="0" width="700">
                <input type="hidden" name="cUsrEma" value="<?php echo $usrEma?>">
                <input type="hidden" name="cReprep" value="<?php echo $reprepor?>">
                  <?php $nCol = f_Format_Cols(35);
                  echo $nCol; ?>
                  <!-- Seccion 1 -->
                  <!-- Fila 1 -->
                  <tr>
                    <td Class="clase08" colspan="1">Id<br>
                      <input type="text" Class="letra" style="width:20" name="cComId" value="<?php echo $ticketCabecera['comidxxx'] ?>" readonly>
                    </td>
                    <td Class="clase08" colspan="3">Prefijo<br>
                      <input type='text' Class='letra' style='width:60' name='cComPre' value="<?php echo $ticketCabecera['comprexx'] ?>" readonly>
                    </td>
                    <td class="clase08" colspan="7">Consecutivo<br>
                      <input type="text" Class="letra" style="width:140" name="cComCsc" value="<?php echo $ticketCabecera['comcscxx'] ?>" readonly>
                    </td>
                    <td class="clase08" colspan="1"><br>
                      <input type="text" Class="letra" style="width:20;" readonly>
                    </td>
                    <td Class="clase08" colspan="5">Nit<br>
                      <input type="text" Class="letra" style="width:100" name="cCliId" value="<?php echo $ticketCabecera['cliidxxx'] ?>" readonly>
                    </td>
                    <td class="clase08" colspan="1">Dv<br>
                      <input type="text" Class="letra" style="width:20;text-align:center" name="cCliDV" value="<?php echo gendv($ticketCabecera['cliidxxx']) ?>" readonly>
                    </td>
                    <td class="clase08" colspan="12">Cliente<br>
                      <input type="text" Class="letra" style="width:240" name="cCliNom" value="<?php echo $ticketCabecera['clinomxx']  ?>" readonly>
                    </td>
                    <td class="clase08" colspan="5">Fecha<br>
                      <input type="text" Class="letra" name="cComFec" style="width:100;text-align:center" value="<?php echo $ticketCabecera['comfecxx'] ?>" readonly>
                    </td>
                  </tr>
                  <!-- Fila 2 -->
                  <tr>
                    <td class="clase08" colspan="5">Creado por<br>
                      <input type="text" Class="letra" style="width:100" name="cUsrId" value="<?php echo $ticketCabecera['usrnomxx'] ?>" readonly>
                    </td>
                    <td Class="clase08" colspan="15"><br>
                      <input type="text" Class="letra" style="width:300" value="<?php echo $usrNom ?>" readonly>
                    </td>
                    <td Class="clase08" colspan="15"><br>
                      <input type="text" Class="letra" style="width:300" name="cUsrEma" value="<?php echo $ticketCabecera['emailcre'] ?>" readonly>
                    </td>
                  </tr>
                  <!-- Fila 3 -->
                  <tr>
                    <td class="clase08" colspan="35">Asunto<br>
                      <input type="text" Class="letra" style="width:700" name="cAsuTck" value="<?php echo $ticketCabecera['ticasuxx'] ?>" readonly>
                    </td>
                  </tr>
                  <!-- Fila 4 -->
                   <?php 
                    if ($reprepor == 'RESPONSABLE') {
                      ?>
                        <tr>
                        <td class="clase08" colspan="5">
                            <a href="javascript:fnLinks('cTtiCod', 'WINDOW')">Tipo</a><br>
                            <input type="text" class="letra" style="width:100" name="cTtiCod" id="cTtiCod"
                                  value="<?php echo htmlspecialchars($ticketCabecera['tticodxx']); ?>"
                                  onBlur="fnLinks('cTtiCod', 'WINDOW');
                                          this.style.background='<?php echo $vSysStr['system_imput_onblur_color']; ?>'"
                                  onFocus="this.style.background='<?php echo $vSysStr['system_imput_onfocus_color']; ?>'">
                        </td>
                        <td class="clase08" colspan="1"><br>
                            <input type="text" class="letra" style="width:20;" readonly>
                        </td>
                        <td class="clase08" colspan="15"><br>
                            <input type="text" class="letra" style="width:300" name="cTtiDes"
                                  value="<?php echo htmlspecialchars($ticketCabecera['ttidesxx']); ?>"
                                  onBlur="this.value=this.value.toUpperCase();
                                          this.style.background='<?php echo $vSysStr['system_imput_onblur_color']; ?>'"
                                  onFocus="this.style.background='<?php echo $vSysStr['system_imput_onfocus_color']; ?>'">
                        </td>
                          <td class="clase08" colspan="7">Prioridad<br>
                            <select name="cPriori" style="width:140px">
                              <?php for ($i = 0; $i < count($mMatrizPti); $i++) {
                                $selected = ($mMatrizPti[$i]['pticodxx'] == $ticketCabecera['pticodxx']) ? 'selected' : '';
                              ?>
                                <option value="<?php echo $mMatrizPti[$i]['pticodxx']; ?>" <?php echo $selected; ?>>
                                  <?php echo $mMatrizPti[$i]['ptidesxx']; ?>
                                </option>
                              <?php } ?>
                            </select>
                          </td>
                          <td class="clase08" colspan="7">Estado<br>
                            <select name="cEstado" style="width:140px">
                              <?php for ($i = 0; $i < count($mMatrizSti); $i++) {
                                $selected = ($mMatrizSti[$i]['sticodxx'] == $ticketCabecera['sticodxx']) ? 'selected' : '';
                              ?>
                                <option value="<?php echo $mMatrizSti[$i]['sticodxx']; ?>" <?php echo $selected; ?>>
                                  <?php echo $mMatrizSti[$i]['stidesxx']; ?>
                                </option>
                              <?php } ?>
                            </select>
                          </td>
                        </tr>
                      <?php
                    }else {
                      ?>
                        <tr>
                        <td class="clase08" colspan="5">
                            <a href="javascript:void(0);" style="color: gray; pointer-events: none; text-decoration: none;"
                              onclick="return false;">Tipo</a><br>
                            <input type="text" class="letra" style="width:100" name="cTtiCod" id="cTtiCod" value="<?php echo $ticketCabecera['tticodxx']; ?>" readonly>
                        </td>
                        <td class="clase08" colspan="1"><br>
                            <input type="text" class="letra" style="width:20;" readonly>
                        </td>
                        <td class="clase08" colspan="15"><br>
                            <input type="text" class="letra" style="width:300" name="cTtiDes" value="<?php echo $ticketCabecera['ttidesxx']; ?>" readonly>
                        </td>
                          <td class="clase08" colspan="7">Prioridad<br>
                            <input type="hidden" name="cPriori" value="<?php echo $ticketCabecera['pticodxx']; ?>">
                            <select name="cPriori" style="width:140px" disabled>
                              <?php for ($i = 0; $i < count($mMatrizPti); $i++) {
                                $selected = ($mMatrizPti[$i]['pticodxx'] == $ticketCabecera['pticodxx']) ? 'selected' : '';
                              ?>
                                <option value="<?php echo $mMatrizPti[$i]['pticodxx']; ?>" <?php echo $selected; ?> readonly>
                                  <?php echo $mMatrizPti[$i]['ptidesxx']; ?>
                                </option>
                              <?php } ?>
                            </select>
                          </td>
                          <td class="clase08" colspan="7">Estado<br>
                          <input type="hidden" name="cEstado" value="<?php echo $ticketCabecera['sticodxx']; ?>">
                            <select name="cEstado" style="width:140px" disabled>
                              <?php for ($i = 0; $i < count($mMatrizSti); $i++) {
                                $selected = ($mMatrizSti[$i]['sticodxx'] == $ticketCabecera['sticodxx']) ? 'selected' : '';
                              ?>
                                <option value="<?php echo $mMatrizSti[$i]['sticodxx']; ?>" <?php echo $selected; ?> readonly>
                                  <?php echo $mMatrizSti[$i]['stidesxx']; ?>
                                </option>
                              <?php } ?>
                            </select>
                          </td>
                        </tr>
                      <?php
                    }
                   ?>
                  
                  <!-- Responsables Asignados al Tipo de Ticket -->
                  <tr>
                    <td Class="clase08" colspan="35">
                      <fieldset id="serviciosAutomaticos">
                        <legend>Responsables Asignados al Tipo de Ticket</legend>
                        <div id="overDivResponsable"></div>
                      </fieldset>
                    </td>
                  </tr>
                  <!-- Correos en Copia -->
                  <tr>
                    <td Class="clase08" colspan="35">Correos en Copia (separados por coma)<br>
                      <input type="text" Class="letra" name="cCliPCECn" id="" style='width:700'>
                    </td>
                  </tr>
                  <!-- Contenido -->
                  <tr>
                    <td Class="clase08" colspan="35">Contenido<br>
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
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick='javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
          <?php break;
          default: ?>
            <td width="700" height="21"></td>
            <td width="98" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif" style="cursor:pointer" onClick="javascript:document.forms['frgrm'].submit();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
            <td width="105" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick='javascript:fnRetorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
        <?php break;
        } ?>
      </tr>
    </table>
    <!-- historico -->
  <table border="0" cellpadding="0" cellspacing="0" width="850">
      <tr>
        <td>
          <fieldset>
            <legend>Historico Ticket</legend>
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

            <table border="1" cellpadding="0" cellspacing="0" width="840">
              <?php foreach ($replys as $reply) : ?>
                <tr>
                  <td class="clase08" style="width: 15%; background-color: <?php echo ($reply['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; padding-left: 2px;">
                    <b>USUARIO</b>
                  </td>
                  <td class="clase08" style="width: 20%; background-color: <?php echo ($reply['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; font-weight: normal; padding-left: 2px;">
                    <?php echo $reply['usrnomxx'] ?>
                  </td>

                  <td class="clase08" style="width: 10%; background-color: <?php echo ($reply['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; padding-left: 2px;">
                    <b>POST ID</b>
                  </td>
                  <td class="clase08" style="width: 10%; background-color: <?php echo ($reply['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; font-weight: normal; padding-left: 2px;">
                  <?php echo $reply['repcscxx'] ?>
                  </td>
                  <td class="clase08" style="width: 10%; background-color: <?php echo ($reply['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; padding-left: 2px;">
                    <b>FECHA</b>
                  </td>
                  <td class="clase08" style="width: 10%; background-color: <?php echo ($reply['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; font-weight: normal; padding-left: 2px;">
                  <?php echo $reply['regfcrex'] ?>
                  </td>
                  <td class="clase08" style="width: 10%; background-color: <?php echo ($reply['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; padding-left: 2px;">
                  <b>HORA</b>
                  </td>
                  <td class="clase08" style="width: 10%; background-color: <?php echo ($reply['reprepor'] == 'RESPONSABLE') ? $vSysStr['system_row_title_color_ini'] : '#B4E197'; ?>; font-weight: normal; padding-left: 2px;">
                  <?php echo $reply['reghcrex'] ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="8" class="clase08" style="width: 100%; font-weight: normal;">
                  <?php echo $reply['repreply'] ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </table>
          </fieldset>
        </td>
      </tr>
    </table>
  </center>
  <?php
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
  ?>
      <script languaje="javascript">
        fnAddNewRowServicio('Grid_Servicios');
        fnCambiaTipoCertificacion(document.forms['frgrm']['cCerTip'].value);
      </script>
    <?php
      break;
    case "EDITAR":
      fnCargaData($cCerId, $cAnio);
    ?>
      <script languaje="javascript">
        // Deshabilito los campos de cabecera
        document.forms['frgrm']['cComPre'].readOnly = true;
        document.forms['frgrm']['cComPre'].onfocus = "";
        document.forms['frgrm']['cComPre'].onblur = "";
        document.forms['frgrm']['cComCsc'].readOnly = true;
        document.forms['frgrm']['cComCsc'].onfocus = "";
        document.forms['frgrm']['cComCsc'].onblur = "";
        document.forms['frgrm']['cCliId'].readOnly = true;
        document.forms['frgrm']['cCliId'].onfocus = "";
        document.forms['frgrm']['cCliId'].onblur = "";
        document.forms['frgrm']['cCliNom'].readOnly = true;
        document.forms['frgrm']['cCliNom'].onfocus = "";
        document.forms['frgrm']['cCliNom'].onblur = "";
        document.forms['frgrm']['cCerTip'].disabled = true;
        document.forms['frgrm']['dVigDesde'].readOnly = true;
        document.forms['frgrm']['dVigHasta'].readOnly = true;
        document.forms['frgrm']['cDepNum'].readOnly = true;
        document.forms['frgrm']['cDepNum'].onfocus = "";
        document.forms['frgrm']['cDepNum'].onblur = "";
        document.forms['frgrm']['cTipoDep'].readOnly = true;
        document.forms['frgrm']['cCdiSap'].readOnly = true;
        document.forms['frgrm']['cCdiSap'].onfocus = "";
        document.forms['frgrm']['cCdiSap'].onblur = "";
        document.forms['frgrm']['cCdiDes'].readOnly = true;
        document.forms['frgrm']['cCdiDes'].onfocus = "";
        document.forms['frgrm']['cCdiDes'].onblur = "";
        document.forms['frgrm']['cCerTipMe'].readOnly = true;


        fnLinks("cResTck", "WINDOW");
      </script>
    <?php
      break;
    case "VER":
      fnCargaData($cCerId, $cAnio);
    ?>
      <script languaje="javascript">
        for (x = 0; x < document.forms['frgrm'].elements.length; x++) {
          document.forms['frgrm'].elements[x].readOnly = true;
          document.forms['frgrm'].elements[x].onfocus = "";
          document.forms['frgrm'].elements[x].onblur = "";
        }
        document.forms['frgrm']['cCerTip'].disabled = true;
        document.forms['frgrm']['cPerAno'].disabled = true;
      </script>
  <?php
      break;
  }
  ?>
  
</body>
</html>
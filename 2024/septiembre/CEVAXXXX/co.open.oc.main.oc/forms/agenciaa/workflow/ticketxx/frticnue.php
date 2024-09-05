<?php
/**
 * Nuevo Ticket.
 * --- Descripcion: Permite Crear Ticket al Do.
 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
 * @package openComex
 */
include("../../../../libs/php/utility.php");
/**
 *  Cookie fija
 */
$kDf = explode("~", $_COOKIE["kDatosFijos"]);
$kMysqlHost = $kDf[0];
$kMysqlUser = $kDf[1];
$kMysqlPass = $kDf[2];
$kMysqlDb = $kDf[3];
$kUser = $kDf[4];
$kLicencia = $kDf[5];
$swidth = $kDf[6];

switch ($gOrigen) {
  case "FADMINFINANCIERO":
    $_COOKIE['kModo'] = $gModo;
    break;
}

switch ($gDoiTip) {
  case "IMPORTACION":
    $qDatDoi = "SELECT ";
    $qDatDoi .= "$cAlfa.SIAI0200.DOIPEDXX,";
    $qDatDoi .= "$cAlfa.SIAI0200.CLIIDXXX,";
    $qDatDoi .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX "; //Nombre Cliente
    $qDatDoi .= "FROM $cAlfa.SIAI0200 ";
    $qDatDoi .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.SIAI0200.CLIIDXXX = $cAlfa.SIAI0150.CLIIDXXX ";
    $qDatDoi .= "WHERE ";
    $qDatDoi .= "$cAlfa.SIAI0200.DOIIDXXX = \"$gDocId\" AND ";
    $qDatDoi .= "$cAlfa.SIAI0200.DOISFIDX = \"$gDocSuf\" AND ";
    $qDatDoi .= "$cAlfa.SIAI0200.ADMIDXXX = \"$gSucId\" LIMIT 0,1 ";
    break;
  case "EXPORTACION":
    $qDatDoi = "SELECT ";
    $qDatDoi .= "$cAlfa.siae0199.dexpedxx AS DOIPEDXX,";
    $qDatDoi .= "$cAlfa.siae0199.cliidxxx AS CLIIDXXX,";
    $qDatDoi .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX "; //Nombre Cliente
    $qDatDoi .= "FROM $cAlfa.siae0199 ";
    $qDatDoi .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.siae0199.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
    $qDatDoi .= "WHERE ";
    $qDatDoi .= "$cAlfa.siae0199.dexidxxx = \"$gDocId\" AND ";
    $qDatDoi .= "$cAlfa.siae0199.admidxxx = \"$gSucId\" LIMIT 0,1 ";
    break;
}

$xDatDoi = f_MySql("SELECT", "", $qDatDoi, $xConexion01, "");
//f_Mensaje(__FILE__,__LINE__,$qDatDoi."~".mysql_num_rows($xDatDoi));
$vDatDoi = mysql_fetch_array($xDatDoi);

/**
 * Traigo Datos de correo del Usuario que esta creando el Ticket
 */
$qCorUsu = "SELECT * ";
$qCorUsu .= "FROM $cAlfa.SIAI0003 ";
$qCorUsu .= "WHERE ";
$qCorUsu .= "USRIDXXX = \"$kUser\" AND ";
$qCorUsu .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
$xCorUsu = f_MySql("SELECT", "", $qCorUsu, $xConexion01, "");
$vCorUsu = mysql_fetch_array($xCorUsu);
?>
<html>
  <head>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css'>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/utility.js'></script>
    <script language="javascript">
      function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        switch (document.forms['frnav']['cOrigen'].value) {
          case "IMPORTACION":
            parent.fmwork.document.location = '../../../importar/frdoiini.php';
            parent.fmnav.location = '../../../nivel3.php';
            break;
          case "EXPORTACION":
            parent.fmwork.document.location = '../../../exportar/frdtgini.php';
            parent.fmnav.location = '../../../nivel3.php';
            break;
          case "WORKFLOW":
            parent.fmwork.document.location = '../myticket/frmtiini.php';
            parent.fmnav.location = '../../../nivel3.php';
            break;
          case "ADMINTICKET":
            document.forms['fregresa'].submit();
            parent.fmnav.location = '../../../nivel3.php';
            break;
          case "FADMINFINANCIERO":
            parent.window.close();
            break;
          case "CABECERA":
            parent.window.close();
            break;
          default:
            document.forms['fregresa'].submit();
            parent.fmnav.location = '../../../nivel3.php';
            break;
        }
      }

      function f_Links(xLink, xSwitch, xIteration) {
        var nX = screen.width;
        var nY = screen.height;
        switch (xLink) {
          case "cTipId":
          case "cTipDes":
            document.forms['frnav']['cTipUsr'].value = "";
            if (xSwitch == "VALID") {
              var cRuta = "frtip001.php?gWhat=VALID&gFunction=" + xLink + "&gTipId=" + document.forms['frnav'][xLink].value.toUpperCase();
              parent.fmpro.location = cRuta;
            } else {
              var nNx = (nX - 550) / 2;
              var nNy = (nY - 300) / 2;
              var cWinPro = 'width=550,scrollbars=1,height=300,left=' + nNx + ',top=' + nNy;
              var cRuta = "frtip001.php?gWhat=WINDOW&gFunction=" + xLink + "&gTipId=" + document.forms['frnav'][xLink].value.toUpperCase();
              cWindow = window.open(cRuta, "cWindow", cWinPro);
              cWindow.focus();
            }
            break;
          case "cStsId":
          case "cStsDes":
            if (xSwitch == "VALID") {
              var cRuta = "frsts002.php?gWhat=VALID&gFunction=" + xLink + "&gStsId=" + document.forms['frnav'][xLink].value.toUpperCase();
              parent.fmpro.location = cRuta;
            } else {
              var nNx = (nX - 550) / 2;
              var nNy = (nY - 300) / 2;
              var cWinPro = 'width=550,scrollbars=1,height=300,left=' + nNx + ',top=' + nNy;
              var cRuta = "frsts002.php?gWhat=WINDOW&gFunction=" + xLink + "&gStsId=" + document.forms['frnav'][xLink].value.toUpperCase();
              cWindow = window.open(cRuta, "cWindow", cWinPro);
              cWindow.focus();
            }
            break;
          case "cPriId":
          case "cPriDes":
            if (xSwitch == "VALID") {
              var cRuta = "frpri003.php?gWhat=VALID&gFunction=" + xLink + "&gPriId=" + document.forms['frnav'][xLink].value.toUpperCase();
              parent.fmpro.location = cRuta;
            } else {
              var nNx = (nX - 550) / 2;
              var nNy = (nY - 300) / 2;
              var cWinPro = 'width=550,scrollbars=1,height=300,left=' + nNx + ',top=' + nNy;
              var cRuta = "frpri003.php?gWhat=WINDOW&gFunction=" + xLink + "&gPriId=" + document.forms['frnav'][xLink].value.toUpperCase();
              cWindow = window.open(cRuta, "cWindow", cWinPro);
              cWindow.focus();
            }
            break;
        }
      }

      function f_Verifica() {
        if (document.forms['frnav']['cCliAsi'].value > 0) {
          document.getElementById('UsuAsiTipo').style.display = "block";
        } else {
          document.getElementById('UsuAsiTipo').style.display = "none";
        }
      }

      function f_Activa() {
        if (document.forms['frnav']['cTipRes'].value == "TIPO" && document.forms['frnav']['cTipUsr'].value != "") {
          document.getElementById('UsuAsiTipo').style.display = "block";
          var cRuta = "frticrti.php?gTipo=1&gTipId=" + document.forms['frnav']['cTipId'].value + "&gTipUsr=" + document.forms['frnav']['cTipUsr'].value;
          parent.fmpro.location = cRuta;
        } else {
          if (document.forms['frnav']['cTipRes'].value == "CLIENTE") {
            var cRuta = "frticrtc.php?gTipo=1&gTipId=" + document.forms['frnav']['cTipId'].value + "&gCliId=" + document.forms['frnav']['cCliId'].value;
            parent.fmpro.location = cRuta;
          } else {
            document.getElementById('UsuAsiTipo').style.display = "none";
          }
        }
      }

      var checkbox = "";
      function f_Arma_Cadena(xName, fld) {
        var cade = document.forms['frnav']['cCadena'].value
        var name = 'OFF';
        if (fld.checked == true) {
          name = 'ON';
        }
        var otra = xName + '~';
        if (name == 'ON') {
          if (cade.indexOf(otra) < 0) {
            if ("<?php echo $kMysqlDb ?>" == "COLVANXX" || "<?php echo $kMysqlDb ?>" == "TECOLVANXX" || "<?php echo $kMysqlDb ?>" == "DECOLVANXX") {
              if (checkbox !== "" && checkbox !== fld) {
                checkbox.checked = false;
              }
              checkbox = fld;
              cade = otra;
            } else {
              cade = cade + otra;
            }
            document.forms['frnav']['cCadena'].value = cade;
          }
        }
        if (name == 'OFF') {
          cade = cade.replace(otra, '');
          document.forms['frnav']['cCadena'].value = cade;
        }
      }

      function f_Oculta() {
        if (document.forms['frnav']['cStsId'].value == "101") {
          document.getElementById('UsuAsiTip').style.display = "none";
          document.forms['frnav']['c<?php echo $kUser ?>'].checked = true;
          var cade = document.forms['frnav']['cCadena'].value;
          var otra = '<?php echo $kUser ?>' + '~';
          if (cade.indexOf(otra) < 0) {
            cade = cade + otra;
            document.forms['frnav']['cCadena1'].value = cade;
          } else {
            document.forms['frnav']['cCadena1'].value = document.forms['frnav']['cCadena'].value;
          }
          document.forms['frnav']['cCadena'].value = '<?php echo $kUser ?>' + '~';
        } else {
          document.getElementById('UsuAsiTip').style.display = "block";
          var mUsrId = document.forms['frnav']['cCadena1'].value.split('~');
          if (document.forms['frnav']['cCanRes'].value > 1) {
            for (i = 0; i < mUsrId.length; i++) {
              if (mUsrId[i] != "") {
                document.forms['frnav']['c' + mUsrId[i]].checked = false;
                document.forms['frnav']['cCadena'].value = '';
              }
            }
          }
          document.forms['frnav']['cCadena1'].value = '';
        }
      }

      function f_Guarda() {
        var nSwitch = 0;
        var cMsj = "";
        var kModo = '<?php echo $_COOKIE['kModo'] ?>';
        if (document.forms['frnav']['cCadena'].value == "" && kModo != "NUEVO" && document.forms['frnav']['cStsId'].value != "101") {
          nSwitch = 1;
          cMsj = "No Selecciono Responsable, Se Asignara Ticket al Usuario que lo Abrio\n";
          if (confirm(cMsj)) {
            nSwitch = 0;
            document.forms['frnav']['cCadena'].value = document.forms['frnav']['cUsrAti'].value + '~';
          } else {
            nSwitch = 1;
          }
        } else if (document.forms['frnav']['cCadena'].value == "" && kModo != "NUEVO" && document.forms['frnav']['cStsId'].value == "101") {
          nSwitch = 0;
          document.forms['frnav']['cCadena'].value = document.forms['frnav']['cUsrAti'].value + '~';
        }

        if (nSwitch == 0) {
          document.forms['frnav'].submit();
        }
      }
    </script>
  </head>
  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
  <center>
    <table border ="0" cellpadding="0" cellspacing="0" width="600">
      <tr>
        <td>
          <fieldset>
            <legend><?php echo ($_COOKIE['kModo'] == "NUEVO") ? ucfirst(strtolower($_COOKIE['kModo']))." Ticket" : "Gestion Ticket $gTicId " ?></legend>
            <form name = "fregresa" action = "../admticxx/fratiini.php" method = "post" target = "fmwork">
              <input type = "hidden" name = "cSQL" value = '<?php echo $gSQL ?>'>
              <input type = "hidden" name = "cCampos" value = '<?php echo $gCampos ?>'>
              <input type = "hidden" name = "cResId"  value = "<?php echo $gResId ?>">
            </form>
            <form name = "frnav" enctype="multipart/form-data" action = "frticgra.php" method = "post" target = "fmpro">
              <input type="hidden" name = "cOrigen" value = "<?php echo $gOrigen ?>">
              <input type="hidden" name = "kModo" value = "<?php echo $gModo ?>">
              <input type="hidden" name = "cSQL" value = '<?php echo $gSQL ?>'>
              <input type="hidden" name = "cCampos" value = '<?php echo $gCampos ?>'>
              <input type="hidden" name = "cResId"  value = "<?php echo $gResId ?>">
              <input type="hidden" name = "cSucId" value  = "<?php echo $gSucId ?>">
              <input type="hidden" name = "cDocId" value  = "<?php echo $gDocId ?>">
              <input type="hidden" name = "cDocSuf" value = "<?php echo $gDocSuf ?>">
              <input type="hidden" name = "cDocTip" value = "<?php echo $gDoiTip ?>">
              <input type="hidden" name = "cCliId"  value = "<?php echo $vDatDoi['CLIIDXXX'] ?>">
              <input type="hidden" name = "cTipUsr" value = "">
              <input type="hidden" name = "cCliAsi" value = "">
              <input type="hidden" name = "cCadena" value = "">
              <input type="hidden" name = "cCadena1" value = "">
              <input type="hidden" name = "cCanRes" value = "">
              <input type="hidden" name = "cTicId" value = "">
              <input type="hidden" name = "cUsrAti" value = "">
              <input type="hidden" name = "cFrom" value = "<?php echo $vCorUsu['USREMAXX'] ?>">
              <center>
                <table border = "0" cellpadding = "0" cellspacing = "0" width="600">
                  <?php echo f_Columnas(30, 20); ?>
                  <tr>
                    <td Class = "name" colspan = "8">Do<br>
                      <input type = "text" Class = "letra" name = "cDo" style = "width:160" value = "<?php echo ($gDoiTip == "IMPORTACION") ? $gSucId."-".$gDocId."-".$gDocSuf : $gSucId."-".$gDocId ?>" readonly>
                    </td>
                    <td Class = "name" colspan = "6">Pedido<br>
                      <input type = "text" Class = "letra" name = "cDoiPed" style = "width:120" value = "<?php echo $vDatDoi['DOIPEDXX'] ?>" readonly>
                    </td>
                    <td Class = "name" colspan = "16">Cliente<br>
                      <input type = "text" Class = "letra" name = "cCliNom" style = "width:320" value = "<?php echo $vDatDoi['CLINOMXX'] ?>" readonly>
                    </td>
                  </tr>
                  <tr>
                    <td Class = "name" colspan = "30">From<br>
                      <input type = "text" Class = "letra" name = "cPara" style = "width:600" value = "<?php echo htmlentities($vCorUsu['USRNOMXX']." <".$vCorUsu['USREMAXX'].">") ?>" readonly>
                    </td>
                  </tr>
                  <tr>
                    <td Class = "name" colspan = "30">Asunto<br>
                      <input type = "text" Class = "letra" name = "cTicAsu" style = "width:600" value = "" 
                             onFocus="javascript:this.style.background = '#00FFFF'";>
                    </td>
                  </tr>
                  <tr>
                    <td Class = "name" colspan = "2">
                      <a href = "javascript:document.forms['frnav']['cTipId'].value = '';
                         document.forms['frnav']['cTipDes'].value = '';
                         document.forms['frnav']['cTipRes'].value = '';
                         document.forms['frnav']['cTipUsr'].value = '';
                         f_Links('cTipId','VALID')" id="idTipId" title="Buscar Tipo">Tipo</a><br>
                      <input type = "text" Class = "letra" name = "cTipId" style = "width:40" 
                             onFocus="javascript:this.style.background = '#00FFFF'";
                             onblur = "javascript:this.value = this.value.toUpperCase();
                                 this.style.background = '#FFFFFF';
                                 f_Links('cTipId', 'VALID');"
                             >
                    </td>
                    <td Class = "name" colspan = "12"><br>
                      <input type = "text" Class = "letra" name = "cTipDes" style = "width:240" 
                             onFocus="javascript:this.style.background = '#00FFFF'";
                             onblur = "javascript:this.value = this.value.toUpperCase();
                                 this.style.background = '#FFFFFF';
                                 f_Links('cTipDes', 'VALID');">
                    </td>
                    <td Class = "name" colspan = "7">Aplica Responsable Por<br>
                      <input type = "text" Class = "letra" name = "cTipRes" style = "width:140" 
                             onFocus="javascript:this.style.background = '#00FFFF'";
                             onblur = "javascript:this.value = this.value.toUpperCase();
                                 this.style.background = '#FFFFFF';" readonly>
                    </td>
                    <td Class = "name" colspan = "5">Prioridad<br>
                      <select name = 'cPriId' style = 'width:100;height:19'>
                        <?php
                        /**
                         * Traigo Prioridades para pintar las opciones de la Lista 
                         */
                        $qDatPri = "SELECT * ";
                        $qDatPri .= "FROM $cAlfa.work0003 ";
                        $qDatPri .= "WHERE ";
                        $qDatPri .= "regestxx = \"ACTIVO\" ";
                        $xDatPri = f_MySql("SELECT", "", $qDatPri, $xConexion01, "");
                        //f_Mensaje(__FILE__,__LINE__,$qDatPri."~".mysql_num_rows($xDatPri));
                        while ($xRDP = mysql_fetch_array($xDatPri)) {
                          ?>
                          <option value ='<?php echo $xRDP['priidxxx'] ?>'><?php echo $xRDP['pridesxx'] ?></option>
                          <?php
                        }
                        ?>
                      </select>
                    </td>
                    <td Class = "name" colspan = "4">Status<br>
                      <select name = 'cStsId' style = 'width:80;height:19' onChange= "javascript:f_Oculta();">
                        <?php
                        /**
                         * Traigo Status para pintar las opciones de la Lista 
                         */
                        $qDatSts = "SELECT * ";
                        $qDatSts .= "FROM $cAlfa.work0002 ";
                        $qDatSts .= "WHERE ";
                        $qDatSts .= "regestxx = \"ACTIVO\" ";
                        $xDatSts = f_MySql("SELECT", "", $qDatSts, $xConexion01, "");
                        //f_Mensaje(__FILE__,__LINE__,$qDatSts."~".mysql_num_rows($xDatSts));
                        while ($xRDT = mysql_fetch_array($xDatSts)) {
                          switch ($_COOKIE['kModo']) {
                            case "NUEVO":
                              $cDisabled = ($xRDT['stsdesxx'] != "ABIERTO") ? "disabled" : "";
                              break;
                          }
                          ?>
                          <option value ='<?php echo $xRDT['stsidxxx'] ?>' <?php echo $cDisabled ?>><?php echo $xRDT['stsdesxx'] ?></option>
                          <?php
                        }
                        ?>
                      </select>
                    </td>
                  </tr>
                  <?php
                  switch ($_COOKIE['kModo']) {
                    case "NUEVO":
                      ?>
                      <tr>
                        <td Class = "name" colspan = "30">
                          <fieldset id= 'UsuAsiTipo'>
                            <legend>Responsables Asignados al Tipo de Ticket</legend>
                            <div id = 'overDivUsr'></div>
                          </fieldset>
                        </td>
                      </tr>
                      <?php
                      break;
                  }
                  ?>
                </table>
              </center>
              <?php
              switch ($_COOKIE['kModo']) {
                case "EDITAR":
                case "VER":
                  ?>
                  <center>
                    <?php
                    /**
                     * Traigo el Aplica Responsable Por "TIPO" o "CLIENTE", para saber si debo traer los usuarios Responsable de la tabla de Tipo
                     * work0001, campo tipusrxx o de la tabla de Clientes, SIAI0150 campo CLIRESTI
                     */
                    $qResTip = "SELECT ";
                    $qResTip .= "work1001.*,";
                    $qResTip .= "work0001.tipresxx,";
                    $qResTip .= "work0001.tipusrxx ";
                    $qResTip .= "FROM $cAlfa.work1001 ";
                    $qResTip .= "LEFT JOIN $cAlfa.work0001 ON $cAlfa.work1001.tipidxxx = $cAlfa.work0001.tipidxxx ";
                    $qResTip .= "WHERE ";
                    $qResTip .= "work1001.ticidxxx = \"$gTicId\" AND ";
                    $qResTip .= "work1001.regestxx = \"ACTIVO\" LIMIT 0,1 ";
                    $xDetTip = mysql_query($qResTip, $xConexion01);
                    $vDetTip = mysql_fetch_array($xDetTip);
                    //f_Mensaje(__FILE__,__LINE__,$qResTip."~".mysql_num_rows($xDetTip));

                    $mUsuarios = array();
                    $mResponsables = array();

                    switch ($vDetTip['tipresxx']) {
                      case "TIPO":
                        $vUsuarios = explode("~", $vDetTip['tipusrxx']);
                        for ($i = 0; $i < count($vUsuarios); $i++) {
                          if ($vUsuarios[$i] != "") {
                            /**
                             * Consulto el Nombre de cada uno de los Responsables Parametrizados
                             */
                            $qNomUsr = "SELECT * ";
                            $qNomUsr .= "FROM $cAlfa.SIAI0003 ";
                            $qNomUsr .= "WHERE ";
                            $qNomUsr .= "USRIDXXX = \"{$vUsuarios[$i]}\" AND ";
                            $qNomUsr .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
                            $xNomUsr = mysql_query($qNomUsr, $xConexion01);
                            $vNomUsr = mysql_fetch_array($xNomUsr);
                            //f_Mensaje(__FILE__,__LINE__,$qNomUsr."~".mysql_num_rows($xNomUsr));

                            /**
                             * Cargo Matriz para pintar Tabla de Responsables
                             */
                            $nInd_mUsuarios = count($mUsuarios);
                            $mUsuarios[$nInd_mUsuarios]['USRIDXXX'] = $vNomUsr['USRIDXXX'];
                            $mUsuarios[$nInd_mUsuarios]['USRNOMXX'] = $vNomUsr['USRNOMXX'];
                            $mResponsables[count($mResponsables)] = $vNomUsr['USRIDXXX'];
                          }
                        }
                        break;
                      case "CLIENTE":
                        /**
                         * Consulto en la Tabla SIAI0150 Clientes los Responsables parametrizados
                         */
                        switch ($vDetTip['doctipxx']) {
                          case "IMPORTACION":
                            $qCliTra = "SELECT CLIIDXXX ";
                            $qCliTra .= "FROM $cAlfa.SIAI0200 ";
                            $qCliTra .= "WHERE ";
                            $qCliTra .= "DOIIDXXX = \"{$vDetTip['docidxxx']}\" AND ";
                            $qCliTra .= "DOISFIDX = \"{$vDetTip['docsufxx']}\" AND ";
                            $qCliTra .= "ADMIDXXX = \"{$vDetTip['sucidxxx']}\" LIMIT 0,1 ";
                            $xCliTra = mysql_query($qCliTra, $xConexion01);
                            $vCliTra = mysql_fetch_array($xCliTra);
                            //f_Mensaje(__FILE__,__LINE__,$qCliTra."~".mysql_num_rows($xCliTra));
                            break;
                          case "EXPORTACION":
                            $qCliTra = "SELECT cliidxxx AS CLIIDXXX ";
                            $qCliTra .= "FROM $cAlfa.siae0199 ";
                            $qCliTra .= "WHERE ";
                            $qCliTra .= "dexidxxx = \"{$vDetTip['docidxxx']}\" AND ";
                            $qCliTra .= "admidxxx = \"{$vDetTip['sucidxxx']}\" LIMIT 0,1 ";
                            $xCliTra = mysql_query($qCliTra, $xConexion01);
                            $vCliTra = mysql_fetch_array($xCliTra);
                            break;
                        }

                        $qResCli = "SELECT CLIRESTI ";
                        $qResCli .= "FROM $cAlfa.SIAI0150 ";
                        $qResCli .= "WHERE ";
                        $qResCli .= "CLIIDXXX = \"{$vCliTra['CLIIDXXX']}\" ";
                        $xResCli = mysql_query($qResCli, $xConexion01);
                        $vResCli = mysql_fetch_array($xResCli);

                        $vUsuarios = f_Explode_Array($vResCli['CLIRESTI'], "|", "~");
                        for ($i = 0; $i < count($vUsuarios); $i++) {
                          if ($vUsuarios[$i] != "") {
                            if ($vDetTip['tipidxxx'] == $vUsuarios[$i][0]) {
                              /**
                               * Consulto el Nombre de cada uno de los Responsables Parametrizados
                               */
                              $qNomUsr = "SELECT * ";
                              $qNomUsr .= "FROM $cAlfa.SIAI0003 ";
                              $qNomUsr .= "WHERE ";
                              $qNomUsr .= "USRIDXXX = \"{$vUsuarios[$i][1]}\" AND ";
                              $qNomUsr .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
                              $xNomUsr = mysql_query($qNomUsr, $xConexion01);
                              $vNomUsr = mysql_fetch_array($xNomUsr);

                              /**
                               * Cargo Matriz para pintar Tabla de Responsables
                               */
                              $nInd_mUsuarios = count($mUsuarios);
                              $mUsuarios[$nInd_mUsuarios]['USRIDXXX'] = $vNomUsr['USRIDXXX'];
                              $mUsuarios[$nInd_mUsuarios]['USRNOMXX'] = $vNomUsr['USRNOMXX'];
                              $mResponsables[count($mResponsables)] = $vNomUsr['USRIDXXX'];
                            }
                          }
                        }
                        break;
                    }
                    ?>
                    <fieldset id= "UsuAsiTip"><legend>Responsable Asignado al Tipo</legend>
                      <table border = "1" cellpadding = 0 cellspacing = 0 width = "580">
                        <?php echo f_columnas(29, 20); ?>
                        <tr bgcolor = "#D6DFF7">
                          <td Class = "name" colspan = "1"><center>&nbsp;</center></td>
                        <td Class = "name" colspan = "5">&nbsp;Identificaci&oacute;n</td>
                        <td Class = "name" colspan = "23">Nombre</td>
                        </tr>
                        <?php
                        for ($i = 0; $i < count($mUsuarios); $i++) {
                          ?>
                          <tr>
                            <td Class = 'name' colspan = "1"><center><input type = "checkbox" name = "c<?php echo $mUsuarios[$i]['USRIDXXX'] ?>" width = "20" onClick = "javascript:f_Arma_Cadena('<?php echo $mUsuarios[$i]['USRIDXXX'] ?>', this)"></center></td>
                          <td Class = 'name' colspan = "5"><?php echo substr($mUsuarios[$i]['USRIDXXX'], 0, 10) ?></td>
                          <td Class = 'name' colspan = "23"><?php echo substr($mUsuarios[$i]['USRNOMXX'], 0, 60) ?></td>
                          </tr>
                          <?php
                        }
                        ?>	
                      </table>
                    </fieldset>
                    <?php
                    break;
                }
                ?>
                <center>
                  <table border = 0 cellpadding = 0 cellspacing = 0 width = "600">
                    <?php echo f_columnas(30, 20); ?>
                    <tr>
                      <td Class = 'name' colspan = '30'>CC &nbsp;&nbsp;&nbsp;&nbsp;<font color ="#FF0000">"Separe los Correos por Comas"</font></br>
                        <textarea name="cCc" style = "width:600"></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td Class = 'name' colspan = '30'>Contenido</br>
                        <textarea name="cTicCon" rows="4"  style = "width:600"></textarea>
                      </td>
                    </tr>
                    <?php
                    $cExtPer  = "application/zip,";
                    $cExtPer .= "application/x-zip-compressed,";
                    $cExtPer .= "multipart/x-zip,";
                    $cExtPer .= "application/pdf,";
                    $cExtPer .= "application/vnd.ms-excel,";
                    $cExtPer .= "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,";
                    $cExtPer .= "application/msword,";
                    $cExtPer .= "application/vnd.openxmlformats-officedocument.wordprocessingml.document,";
                    $cExtPer .= "image/png,";
                    $cExtPer .= "image/jpg,";
                    $cExtPer .= "image/jpeg";
                    ?>
                    <tr>
                      <td Class = "name" colspan = "30"><br>Adjunto<br>
                        <input type = "file" style = "width:600px;height:22px" name = "cAdjunto" id = "cAdjunto" accept="<?php echo $cExtPer ?>">
                      </td>
                    </tr> 
                    <tr>
                      <td  colspan = "30">
                        <span style="color:#0046D5">Extensiones permitidas: .zip, .pdf, .xls, .xlsx, .doc, .docx, .png, jpg, .jpeg</span><br>
                      </td>
                    </tr> 
                    <tr>
                      <td Class = "name" colspan = "30">&nbsp;</td>
                    </tr> 
                    <tr>
                      <td Class = "name" colspan = "6">Fecha<br>
                        <input type = "text" Class = "letra"  style = "width:120;text-align:center"  name = "dRegFcre"
                               value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                      <td Class = "name" colspan = "6">Hora<br>
                        <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "hRegHcre"
                               value = "<?php echo date('H:i:s') ?>" readonly>
                      </td>
                      <td Class = "name" colspan = "6">Modificado<br>
                        <input type = "text" Class = "letra"  style = "width:120;text-align:center"  name = "dRegFmod"
                               value = "<?php echo date('Y-m-d') ?>" readonly>
                      </td>
                      <td Class = "name" colspan = "6">Hora<br>
                        <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "hRegHmod"
                               value = "<?php echo date('H:i:s') ?>" readonly>
                      </td>
                      <td Class = "name" colspan = "6">Estado<br>
                        <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cRegEst" value = "ACTIVO" readonly>
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
        <?php
        switch ($_COOKIE['kModo']) {
          case "VER":
            ?>
            <td width="509" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_cancel_bg.gif" style="cursor:hand"
                onClick = "javascript:f_Retorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
            </td>
            <?php
            break;
          default:
            ?>
            <td width="418" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_ok_bg.gif" style="cursor:hand"
                onClick = "javascript:f_Guarda()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar
            </td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_cancel_bg.gif" style="cursor:hand"
                onClick = "javascript:f_Retorna()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
            </td>
            <?php
            break;
        }
        ?>
      </tr>
    </table>
  </center>

  <?php
  switch ($_COOKIE['kModo']) {
    case "EDITAR":
    case "VER":
      ?>
      <br>
      <center>
        <table border ="0" cellpadding="0" cellspacing="0" width="600">
          <tr>
            <td>
              <fieldset>
                <legend>Hist&oacute;rico Tiket</legend>
                <center>
                  <table border = "0" cellpadding = "0" cellspacing = "0" width="600">
                    <?php echo f_Columnas(30, 20); ?>
                    <tr>
                      <td Class = "name" colspan = "10">Post Id Hecho Por:</td>
                      <td class="name" colspan="10" valign="middle"><img src = '<?php echo $cPlesk_Skin_Directory_New ?>/responsable.gif' align="baseline">&nbsp;&nbsp;Responsable</td>
                      <td class="name" colspan= "10" valign="middle"><img src = '<?php echo $cPlesk_Skin_Directory_New ?>/tercero.gif' align="baseline">&nbsp;&nbsp;Tercero</td>
                    </tr>
                  </table><br>
                  <?php
                  /**
                   * Traigo Historico de Reply's Hechos al Ticket
                   */
                  $mHistorico = array();

                  $qDetRep = "SELECT work1002.*,";
                  $qDetRep .= "SIAI0003.USRNOMXX AS usrnomxx ";
                  $qDetRep .= "FROM $cAlfa.work1002 ";
                  $qDetRep .= "LEFT JOIN $cAlfa.SIAI0003 ON work1002.regusrxx = SIAI0003.USRIDXXX ";
                  $qDetRep .= "WHERE ";
                  $qDetRep .= "work1002.ticidxxx = \"$gTicId\" AND ";
                  $qDetRep .= "work1002.regestxx = \"ACTIVO\" ";
                  $qDetRep .= "ORDER BY ABS(work1002.repidxxx) ASC ";
                  $xDetRep = mysql_query($qDetRep, $xConexion01);
                  if (mysql_num_rows($xDetRep) > 0) {
                    while ($xRDR = mysql_fetch_array($xDetRep)) {
                      $mHistorico[count($mHistorico)] = $xRDR;
                    }
                  }
                  for ($i = 0; $i < count($mHistorico); $i++) {
                    ?>
                    <table border = "1" cellpadding = "0" cellspacing = "0" width="600">
                      <?php
                      echo f_Columnas(30, 20);
                      if ($mHistorico[$i]['ticadmxx'] <> "") {
                        //$cColor = "#D6F0F7";
                        $cColor = "#C1E8F3";
                      } else {
                        $cColor = "#D6DFF7";
                      }
                      ?>
                      <tr bgcolor="<?php echo $cColor ?>" height="15">
                        <td Class = "name" colspan = "4">&nbsp;Usuario</td>
                        <td Class = "name" colspan = "11"><?php echo ($mHistorico[$i]['usrnomxx'] != "") ? $mHistorico[$i]['usrnomxx'] : "&nbsp;" ?></td>
                        <td Class = "name" colspan = "3">&nbsp;Post Id</td>
                        <td Class = "name" colspan = "2"><center><?php echo ($mHistorico[$i]['repidxxx'] != "") ? $mHistorico[$i]['repidxxx'] : "&nbsp;" ?></center></td>
                      <td Class = "name" colspan = "2"><center>Fecha</center></td>
                      <td Class = "name" colspan = "4"><center><?php echo ($mHistorico[$i]['regfcrex'] != "") ? $mHistorico[$i]['regfcrex'] : "&nbsp;" ?></center></td>
                      <td Class = "name" colspan = "2"><center>Hora</center></td>
                      <td Class = "name" colspan = "2"><center><?php echo ($mHistorico[$i]['reghcrex'] != "") ? $mHistorico[$i]['reghcrex'] : "&nbsp;" ?></center></td>
                      </tr>
                      <tr>
                        <td Class = "name" colspan = "30"><?php echo ($mHistorico[$i]['ticconxx'] != "") ? $mHistorico[$i]['ticconxx'] : "&nbsp;" ?></td>
                      </tr>
                    </table><br>
                    <?php
                  }
                  ?>	
                </center>
              </fieldset>
            </td>
          </tr>
        </table>
      </center>
      <?php
      break;
  }
  ?>
  <!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
  <?php
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      ?>
      <script languaje = "javascript">
        document.getElementById('UsuAsiTipo').style.display = "none";
      </script>
      <?php
      break;
    case "EDITAR":
      f_CargaData($gTicId);
      ?>
      <script languaje = "javascript">
        document.forms['frnav']['dRegFcre'].readOnly = true;
        document.forms['frnav']['cRegEst'].readOnly = true;
        document.forms['frnav']['dRegFmod'].value = "<?php echo date('Y-m-d'); ?>";
        document.forms['frnav']['hRegHmod'].value = "<?php echo date('H:i:s'); ?>";
        document.getElementById('idTipId').href = "javascript:alert('No Permitido')";
        document.forms['frnav']['cTipId'].readOnly = true;
        document.forms['frnav']['cTipId'].onfocus = "";
        document.forms['frnav']['cTipId'].onblur = "";
        document.forms['frnav']['cTipDes'].readOnly = true;
        document.forms['frnav']['cTipDes'].onfocus = "";
        document.forms['frnav']['cTipDes'].onblur = "";
        document.forms['frnav']['cTicAsu'].readOnly = true;
        document.forms['frnav']['cTicAsu'].onfocus = "";
        document.forms['frnav']['cTicAsu'].onblur = "";
      </script>
      <?php
      break;
    case "VER":
      f_CargaData($gTicId);
      ?>
      <script languaje = "javascript">
        document.getElementById('idTipId').href = "javascript:alert('No Permitido')";
        for (x = 0; x < document.forms['frnav'].elements.length; x++) {
          document.forms['frnav'].elements[x].readOnly = true;
          document.forms['frnav'].elements[x].onfocus = "";
          document.forms['frnav'].elements[x].onblur = "";
          document.forms['frnav'].elements[x].disabled = true;
        }
      </script>
      <?php
      break;
    default:
      ?>
      <script languaje = "javascript">
        document.getElementById('idTipId').href = "javascript:alert('No Permitido')";
        for (x = 0; x < document.forms['frnav'].elements.length; x++) {
          document.forms['frnav'].elements[x].readOnly = true;
          document.forms['frnav'].elements[x].onfocus = "";
          document.forms['frnav'].elements[x].onblur = "";
          document.forms['frnav'].elements[x].disabled = true;
        }
      </script>
      <?php
      break;
  }
  ?>

  <?php

  function f_CargaData($xTicId) {
    global $xConexion01;
    global $cAlfa;
    global $kUser;
    global $mUsuarios;
    global $mResponsables;

    $qCabTic = "SELECT $cAlfa.work1001.*,";
    $qCabTic .= "$cAlfa.work0001.tipdesxx,";
    $qCabTic .= "$cAlfa.work0001.tipresxx,";
    $qCabTic .= "$cAlfa.work0002.stsdesxx,";
    $qCabTic .= "$cAlfa.work0003.pridesxx ";
    $qCabTic .= "FROM $cAlfa.work1001 ";
    $qCabTic .= "LEFT JOIN $cAlfa.work0001 ON $cAlfa.work1001.tipidxxx = $cAlfa.work0001.tipidxxx ";
    $qCabTic .= "LEFT JOIN $cAlfa.work0002 ON $cAlfa.work1001.stsidxxx = $cAlfa.work0002.stsidxxx ";
    $qCabTic .= "LEFT JOIN $cAlfa.work0003 ON $cAlfa.work1001.priidxxx = $cAlfa.work0003.priidxxx ";
    $qCabTic .= "WHERE ";
    $qCabTic .= "ticidxxx = \"$xTicId\" LIMIT 0,1 ";
    $xCabTic = f_MySql("SELECT", "", $qCabTic, $xConexion01, "");
    //f_Mensaje(__FILE__,__LINE__,$qCabTic."~".mysql_num_rows($xCabTic));
    $vCabTic = mysql_fetch_array($xCabTic);

    /**
     * Consultando Usuario que Abrio el Ticket
     */
    $qDetTic = "SELECT * ";
    $qDetTic .= "FROM $cAlfa.work1002 ";
    $qDetTic .= "WHERE ";
    $qDetTic .= "ticidxxx = \"$xTicId\" AND ";
    $qDetTic .= "repidxxx = \"1\" LIMIT 0,1 ";
    $xDetTic = f_MySql("SELECT", "", $qDetTic, $xConexion01, "");
    //f_Mensaje(__FILE__,__LINE__,$qDetTic."~".mysql_num_rows($xDetTic));
    $vDetTic = mysql_fetch_array($xDetTic);
    ?>
    <script language = "javascript">
      document.forms['frnav']['cTicId'].value = "<?php echo $vCabTic['ticidxxx'] ?>";
      document.forms['frnav']['cTipId'].value = "<?php echo $vCabTic['tipidxxx'] ?>";
      document.forms['frnav']['cTipDes'].value = "<?php echo $vCabTic['tipdesxx'] ?>";
      document.forms['frnav']['cTipRes'].value = "<?php echo $vCabTic['tipresxx'] ?>";
      document.forms['frnav']['cTicAsu'].value = "<?php echo $vCabTic['ticasuxx'] ?>";
      document.forms['frnav']['cTipId'].value = "<?php echo $vCabTic['tipidxxx'] ?>";
      document.forms['frnav']['cTipDes'].value = "<?php echo $vCabTic['tipdesxx'] ?>";
      document.forms['frnav']['cTipRes'].value = "<?php echo $vCabTic['tipresxx'] ?>";
      document.forms['frnav']['cPriId'].value = "<?php echo $vCabTic['priidxxx'] ?>";
      document.forms['frnav']['cStsId'].value = "<?php echo $vCabTic['stsidxxx'] ?>";
      document.forms['frnav']['cCc'].value = "<?php echo $vCabTic['ticoccxx'] ?>";
      document.forms['frnav']['cUsrAti'].value = "<?php echo $vDetTic['regusrxx'] ?>";
    </script>
    <?php
    /**
     * Si el Usuario que esta haciendo Reply es un Tercero, se deshabilita la opcion de cambiar el Responsable
     * y se carga por default el ultimo Responsable del Ticket.
     */
    if ((!in_array($kUser, $mResponsables) == true) && $kUser != $vDetTic['regusrxx']) {

      $vResponsables = explode("~", $vCabTic['ticresxx']);

      if (count($vResponsables) > 0) {
        ?>
        <script language = "javascript">
          document.forms['frnav']['cCadena'].value = '<?php echo $vCabTic['ticresxx'] ?>';
        </script>
        <?php
      }

      for ($i = 0; $i < count($vResponsables); $i++) {
        if ($vResponsables[$i] != "") {
          ?>
          <script language = "javascript">
            document.forms['frnav']['c<?php echo $vResponsables[$i] ?>'].checked = true;
          </script>
          <?php
        }
      }

      for ($i = 0; $i < count($mResponsables); $i++) {
        ?>
        <script language = "javascript">
          document.forms['frnav']['c<?php echo $mResponsables[$i] ?>'].disabled = true;
        </script>
        <?php
      }
    }
  }

  if (count($mUsuarios) == 1) {
    ?>
    <script language = "javascript">
      document.forms['frnav']['c<?php echo $mUsuarios[0]['USRIDXXX'] ?>'].checked = true;
      document.forms['frnav']['cCadena'].value = '<?php echo $mUsuarios[0]['USRIDXXX'] ?>' + '~';
    </script>
    <?php
  }
  ?>
  <script language = "javascript">
    document.forms['frnav']['cCanRes'].value = '<?php echo count($mUsuarios) ?>';
  </script>
</body>
</html>
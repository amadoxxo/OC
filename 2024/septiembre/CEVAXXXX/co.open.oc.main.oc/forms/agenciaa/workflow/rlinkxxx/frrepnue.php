<?php
/**
 * Nuevo Ticket.
 * --- Descripcion: Permite Hacer Reply o Dar Respuesta al Ticket Abierto Previamente.
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
$cAlfa = $gAlfa;

$_COOKIE['kModo'] = "EDITAR";
include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
$xConexion01 = mysql_connect(OC_SERVER, OC_USERROBOT, OC_PASSROBOT);

/**
 * Busco la estructura de directorios del sistema.
 */
$qSysStr = "SELECT * ";
$qSysStr .= "FROM $cAlfa.sys00002 ";
$qSysStr .= "WHERE ";
$qSysStr .= "regestxx = \"ACTIVO\"";
$xSysStr = mysql_query($qSysStr, $xConexion01);
$vSysStr = array();
while ($xRSS = mysql_fetch_array($xSysStr)) {
  $vSysStr[$xRSS['stridxxx']] = $xRSS['strvlrxx'];
}

/**
 * Cargo las Variables del Sistema.
 */
$cSystem_Root_Directory = "/".$vSysStr['system_root_directory'];
$cPlesk_Root_Directory = $cSystem_Root_Directory."/".$vSysStr['plesk_root_directory'];
$cPlesk_Forms_Directory = $cPlesk_Root_Directory."/".$vSysStr['plesk_forms_directory'];
$cPlesk_Graphics_Directory = $cPlesk_Root_Directory."/".$vSysStr['plesk_graphics_directory'];
$cPlesk_Skin_Directory = $cPlesk_Graphics_Directory."/".$vSysStr['plesk_skin_directory'];
$cPlesk_XP_Memu_Directory = $cPlesk_Root_Directory."/".$vSysStr['plesk_xpmenu_directory'];

$cSystem_Alerts_Directory = $cSystem_Root_Directory."/".$vSysStr['system_alerts_directory'];
$cSystem_Download_Directory = $cSystem_Root_Directory."/".$vSysStr['system_download_directory'];
$cSystem_Files_Directory = $cSystem_Root_Directory."/".$vSysStr['system_files_directory'];
$cSystem_Fonts_Directory = $cSystem_Root_Directory."/".$vSysStr['system_fonts_directory'];
$cSystem_Forms_Directory = $cSystem_Root_Directory."/".$vSysStr['system_forms_directory'];
$cSystem_Class_Directory = $cSystem_Root_Directory."/".$vSysStr['system_class_directory'];
$cSystem_Graphics_Directory = $cSystem_Root_Directory."/".$vSysStr['system_graphics_directory'];
$cSystem_Libs_Directory = $cSystem_Root_Directory."/".$vSysStr['system_libs_directory'];
$cSystem_Libs_JS_Directory = $cSystem_Libs_Directory."/".$vSysStr['system_libs_js_directory'];
$cSystem_Libs_Php_Directory = $cSystem_Libs_Directory."/".$vSysStr['system_libs_php_directory'];

$cSystem_Root_Directory_New = "/".$vSysStr['system_root_directory_new'];
$cPlesk_Root_Directory_New = $cSystem_Root_Directory_New."/".$vSysStr['plesk_root_directory'];
$cPlesk_Forms_Directory_New = $cPlesk_Root_Directory_New."/".$vSysStr['plesk_forms_directory'];
$cPlesk_Graphics_Directory_New = $cPlesk_Root_Directory_New."/".$vSysStr['plesk_graphics_directory'];
$cPlesk_Skin_Directory_New = $cPlesk_Graphics_Directory_New."/".$vSysStr['plesk_skin_directory'];
$cPlesk_XP_Memu_Directory_New = $cPlesk_Root_Directory_New."/".$vSysStr['plesk_xpmenu_directory'];

$cSystem_Alerts_Directory_New = $cSystem_Root_Directory_New."/".$vSysStr['system_alerts_directory'];
$cSystem_Download_Directory_New = $cSystem_Root_Directory_New."/".$vSysStr['system_download_directory'];
$cSystem_Files_Directory_New = $cSystem_Root_Directory_New."/".$vSysStr['system_files_directory'];
$cSystem_Fonts_Directory_New = $cSystem_Root_Directory_New."/".$vSysStr['system_fonts_directory'];
$cSystem_Forms_Directory_New = $cSystem_Root_Directory_New."/".$vSysStr['system_forms_directory'];
$cSystem_Class_Directory_New = $cSystem_Root_Directory_New."/".$vSysStr['system_class_directory'];
$cSystem_Graphics_Directory_New = $cSystem_Root_Directory_New."/".$vSysStr['system_graphics_directory'];
$cSystem_Libs_Directory_New = $cSystem_Root_Directory_New."/".$vSysStr['system_libs_directory'];
$cSystem_Libs_JS_Directory_New = $cSystem_Libs_Directory_New."/".$vSysStr['system_libs_js_directory'];
$cSystem_Libs_Php_Directory_New = $cSystem_Libs_Directory_New."/".$vSysStr['system_libs_php_directory'];
?>
<html>
  <head>
    <meta charset="utf-8">
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css'>
    <script type="text/javascript" src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/utility.js'></script>
    <script type="text/javascript">
      function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
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

      function f_Activa() {
        //alert(document.forms['frnav']['cTipUsr'].value);
        if (document.forms['frnav']['cTipUsr'].value != "") {
          document.getElementById('UsuAsiTipo').style.display = "block";
          var cRuta = "frtipusr.php?gTipo=1&gTipId=<?php echo $cTipId ?>&gTipUsr=" + document.forms['frnav']['cTipUsr'].value;
          parent.fmpro2.location = cRuta;
        } else {
          document.getElementById('UsuAsiTipo').style.display = "none";
        }
      }

      //Funcion para recargar las tablas
      function f_Guardar() {

        var nSwitch = 0;
        var cMsj = "";
        var cCadena = document.getElementById("cCadena");


        if (cCadena.value == "") {
          nSwitch = 1;
          cMsj = "No Selecciono Responsable, Se Asignara Ticket al Usuario que lo Abrio\n";
          if (confirm(cMsj)) {
            nSwitch = 0;
            var cPreUsrAti = document.getElementById("cUsrAti");
            var cUsrAti = cPreUsrAti + "~";
            cCadena.value = cUsrAti;
          } else {
            nSwitch = 1;
          }
        }

        if (nSwitch == 0) {
          var formulario = document.getElementById("frnav");
          formulario.submit();
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
            if ("<?php echo $cAlfa ?>" == "COLVANXX" || "<?php echo $cAlfa ?>" == "TECOLVANXX" || "<?php echo $cAlfa ?>" == "DECOLVANXX") {
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
          document.forms['frnav']['c<?php echo $gUsrId ?>'].checked = true;
          var cade = document.forms['frnav']['cCadena'].value;
          var otra = '<?php echo $gUsrId ?>' + '~';
          if (cade.indexOf(otra) < 0) {
            cade = cade + otra;
            document.forms['frnav']['cCadena1'].value = cade;
          } else {
            document.forms['frnav']['cCadena1'].value = document.forms['frnav']['cCadena'].value;
          }
          document.forms['frnav']['cCadena'].value = '<?php echo $gUsrId ?>' + '~';
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

      function f_Recargar() {
        location.replace("frrepfre.php");
      }
    </script>
  </head>
  <body>
  <center>
    <table border ="0" cellpadding="0" cellspacing="0" width="600">
      <tr>
        <td>
          <fieldset>
            <legend><?php echo ($_COOKIE['kModo'] == "NUEVO") ? ucfirst(strtolower($_COOKIE['kModo']))." Ticket" : "Gestion Ticket $gTicId " ?></legend>
            <form name = "frnav" id = "frnav" enctype="multipart/form-data" action = "frrepgra.php" method = "post" target = "submit_target">
              <input type="hidden" name = "cTipUsr" value = "">
              <input type="hidden" name = "cSucId" id ="cSucId" value  = "<?php echo $vTramite['sucidxxx'] ?>">
              <input type="hidden" name = "cDocId"  id = "cDocId" value  = "<?php echo $vTramite['docidxxx'] ?>">
              <input type="hidden" name = "cDocSuf" id = "cDocSuf" value = "<?php echo $vTramite['docsufxx'] ?>">
              <input type="hidden" name = "cDocTip" id = "cDocTip" value = "<?php echo $vTramite['doctipxx'] ?>">
              <input type="hidden" name = "cTicId" value   = "<?php echo $gTicId ?>">
              <input type="hidden" name = "cUsrId" value   = "<?php echo $gUsrId ?>">
              <input type="hidden" name = "cAlfa" value   = "<?php echo $cAlfa ?>">
              <input type="hidden" name = "kModo" value   = "EDITAR">
              <input type="hidden" name = "cCliId" id = "cCliId" value  = "">
              <input type="hidden" name = "cCadena" id = "cCadena" value = "">
              <input type="hidden" name = "cCadena1" value = "">
              <input type="hidden" name = "cCanRes" id = "cCanRes" value = "">
              <input type="hidden" name = "cUsrAti" id = "cUsrAti" value = "">
              <input type="hidden" name = "cFrom" id = "cFrom" value = "">
              <center>
                <table border = "0" cellpadding = "0" cellspacing = "0" width="600">
                  <?php echo f_Columnas(30, 20); ?>
                  <tr>
                    <td Class = "name" colspan = "8">Do<br>
                      <input type = "text" Class = "letra" name = "cDo" id = "cDo" style = "width:160" value = "" readonly>
                    </td>
                    <td Class = "name" colspan = "6">Pedido<br>
                      <input type = "text" Class = "letra" name = "cDoiPed" id = "cDoiPed" style = "width:120" value = "" readonly>
                    </td>
                    <td Class = "name" colspan = "16">Cliente<br>
                      <input type = "text" Class = "letra" name = "cCliNom" id = "cCliNom" style = "width:320" value = "" readonly>
                    </td>
                  </tr>
                  <tr>
                    <td Class = "name" colspan = "30">From<br>
                      <input type = "text" Class = "letra" name = "cDe" id = "cDe" style = "width:600" value = "" readonly>
                    </td>
                  </tr>
                  <tr>
                    <td Class = "name" colspan = "30">Asunto<br>
                      <input type = "text" Class = "letra" name = "cTicAsu" id = "cTicAsu"  style = "width:600" value = "" 
                             onFocus="javascript:this.style.background = '#00FFFF'" readonly>
                    </td>
                  </tr>
                  <tr>
                    <td Class = "name" colspan = "2">
                      <a href = "javascript:document.forms['frnav']['cTipId'].value = '';
                         document.forms['frnav']['cTipDes'].value = '';
                         document.forms['frnav']['cTipUsr'].value = '';
                         f_Links('cTipId','VALID')" id="idTipId" title="Buscar Tipo">Tipo</a><br>
                      <input type = "text" Class = "letra" name = "cTipId" id = "cTipId" style = "width:40" 
                             onFocus="javascript:this.style.background = '#00FFFF'"
                             onblur = "javascript:this.value = this.value.toUpperCase();
                                 this.style.background = '#FFFFFF';
                                 f_Links('cTipId', 'VALID');" readonly>
                    </td>
                    <td Class = "name" colspan = "12"><br>
                      <input type = "text" Class = "letra" name = "cTipDes" id = "cTipDes" style = "width:240" 
                             onFocus="javascript:this.style.background = '#00FFFF'"
                             onblur = "javascript:this.value = this.value.toUpperCase();
                                 this.style.background = '#FFFFFF';
                                 f_Links('cTipDes', 'VALID');" readonly>
                    </td>
                    <td Class = "name" colspan = "7">Aplica Responsable Por<br>
                      <input type = "text" Class = "letra" name = "cTipRes" id = "cTipRes" style = "width:140" readonly>
                    </td>
                    <td Class = "name" colspan = "5">Prioridad<br>
                      <select name = 'cPriId' id = 'cPriId'  style = 'width:100;height:19'>
                        <?php
                        /**
                         * Traigo Prioridades para pintar las opciones de la Lista 
                         */
                        $qDatPri = "SELECT * ";
                        $qDatPri .= "FROM $cAlfa.work0003 ";
                        $qDatPri .= "WHERE ";
                        $qDatPri .= "regestxx = \"ACTIVO\" ";
                        $xDatPri = mysql_query($qDatPri, $xConexion01);
//$xDatPri  = f_MySql("SELECT","",$qDatPri,$xConexion01,"");
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
                      <select name = 'cStsId' id = 'cStsId' style = 'width:80;height:19' onChange= "javascript:f_Oculta();">
                        <?php
                        /**
                         * Traigo Status para pintar las opciones de la Lista 
                         */
                        $qDatSts = "SELECT * ";
                        $qDatSts .= "FROM $cAlfa.work0002 ";
                        $qDatSts .= "WHERE ";
                        $qDatSts .= "regestxx = \"ACTIVO\" ";
                        $xDatSts = mysql_query($qDatSts, $xConexion01);
//$xDatSts  = f_MySql("SELECT","",$qDatSts,$xConexion01,"");
//f_Mensaje(__FILE__,__LINE__,$qDatSts."~".mysql_num_rows($xDatSts));
                        while ($xRDS = mysql_fetch_array($xDatSts)) {
                          ?>
                          <option value ='<?php echo $xRDS['stsidxxx'] ?>'><?php echo $xRDS['stsdesxx'] ?></option>
                          <?php
                        }
                        ?>
                      </select>
                    </td>
                  </tr>
                </table>
              </center>
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
                      <td Class = "name" colspan = "1"><center></center></td>
                    <td Class = "name" colspan = "5">&nbsp;Identificaci&oacute;n</td>
                    <td Class = "name" colspan = "23">Nombre</td>
                    </tr>
                    <?php
                    for ($i = 0; $i < count($mUsuarios); $i++) {
                      ?>
                      <tr>
                        <td Class = 'name' colspan = "1"><center><input type = "checkbox" name = "c<?php echo $mUsuarios[$i]['USRIDXXX'] ?>" id = "c<?php echo $mUsuarios[$i]['USRIDXXX'] ?>" width = "20" onClick = "javascript:f_Arma_Cadena('<?php echo $mUsuarios[$i]['USRIDXXX']; ?>', this);"></center></td>
                      <td Class = 'name' colspan = "5"><?php echo substr($mUsuarios[$i]['USRIDXXX'], 0, 10) ?></td>
                      <td Class = 'name' colspan = "23"><?php echo substr($mUsuarios[$i]['USRNOMXX'], 0, 60) ?></td>
                      </tr>
                      <?php
                    }
                    ?>	
                  </table>
                </fieldset>
                <table border = 0 cellpadding = 0 cellspacing = 0 width = "600">
                  <?php echo f_columnas(30, 20); ?>
                  <tr>
                    <td Class = 'name' colspan = '30'>CC &nbsp;&nbsp;&nbsp;&nbsp;<font color ="#FF0000">"Separe los Correos por Comas"</font></br>
                      <textarea name="cCc" id="cCc" style = "width:600"></textarea>
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
                      <input type = "text" Class = "letra"  style = "width:120;text-align:center"  name = "dRegFcre" id = "dRegFcre"
                             value = "<?php echo date('Y-m-d') ?>" readonly>
                    </td>
                    <td Class = "name" colspan = "6">Hora<br>
                      <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "hRegHcre" id = "hRegHcre"
                             value = "<?php echo date('H:i:s') ?>" readonly>
                    </td>
                    <td Class = "name" colspan = "6">Modificado<br>
                      <input type = "text" Class = "letra"  style = "width:120;text-align:center"  name = "dRegFmod" id = "dRegFmod"
                             value = "<?php echo date('Y-m-d') ?>" readonly>
                    </td>
                    <td Class = "name" colspan = "6">Hora<br>
                      <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "hRegHmod" id = "hRegHmod"
                             value = "<?php echo date('H:i:s') ?>" readonly>
                    </td>
                    <td Class = "name" colspan = "6">Estado<br>
                      <input type = "text" Class = "letra" style = "width:120;text-align:center" name = "cRegEst" value = "ACTIVO" readonly>
                    </td>
                  </tr>
                </table>
              </center>
            </form>
            <iframe id="submit_target" name="submit_target" src="" style="width:0px;height:0px;border-width:0px;" class="display:none"></iframe>
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
                onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
            </td>
            <?php
            break;
          default:
            ?>
            <td width="509" height="21"></td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory_New ?>/btn_ok_bg.gif" style="cursor:hand"
                onClick = "javascript:f_Guardar();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar
            </td>
            <?php
            break;
        }
        ?>
      </tr>
    </table>
  </center>
  <br>
  <center>
    <table border ="0" cellpadding="0" cellspacing="0" width="600">
      <tr>
        <td>
          <fieldset>
            <legend>Hist&oacute;rico Tiket</legend>
            <form name = "frnav" action = "" method = "post" target = "fmpro">
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
                      $cColor = "#C1E8F3";
                    } else {
                      $cColor = "#D6DFF7";
                    }
                    ?>
                    <tr bgcolor="<?php echo $cColor ?>" height="15">
                      <td Class = "name" colspan = "4">&nbsp;Usuario</td>
                      <td Class = "name" colspan = "11"><?php echo ($mHistorico[$i]['usrnomxx'] != "") ? substr($mHistorico[$i]['usrnomxx'], 0, 20) : "&nbsp;" ?></td>
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
            </form>
          </fieldset>
        </td>
      </tr>
    </table>
  </center>
  <?php
  switch ($_COOKIE['kModo']) {
    case "EDITAR":
      f_CargaData($cAlfa, $gTicId, $gUsrId);
      ?>
      <script type="text/javascript">
        var dRegFcre = document.getElementById("dRegFcre");
        dRegFcre.value = "<?php echo date('Y-m-d'); ?>";
        var hRegHcre = document.getElementById("hRegHcre");
        hRegHcre.value = "<?php echo date('H:i:s'); ?>";
        var dRegFmod = document.getElementById("dRegFmod");
        dRegFmod.value = "<?php echo date('Y-m-d'); ?>";
        var hRegHmod = document.getElementById("hRegHmod");
        hRegHmod.value = "<?php echo date('H:i:s'); ?>";
        document.getElementById('idTipId').href = "javascript:alert('No Permitido');";
      </script>
      <?php
      break;
    default:
      ?>
      <script type="text/javascript">
        document.forms['frnav']['cPriId'].readOnly = false;
        document.forms['frnav']['cPriDes'].readOnly = false;
        document.getElementById('idTipId').href = "javascript:alert('No Permitido');";
      </script>
      <?php
      break;
  }
  ?>

  <?php

  function f_CargaData($cAlfa, $gTicId, $gUsrId) {
    global $xConexion01;

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
    $qCabTic .= "$cAlfa.work1001.ticidxxx = \"{$gTicId}\" AND ";
    $qCabTic .= "$cAlfa.work1001.regestxx = \"ACTIVO\" LIMIT 0,1 ";
    //$xCabTic  = f_MySql("SELECT","",$qCabTic,$xConexion01,"");
    $xCabTic = mysql_query($qCabTic, $xConexion01);
    //f_Mensaje(__FILE__,__LINE__,$qCabTic."~".mysql_num_rows($xCabTic));
    $vCabTic = mysql_fetch_array($xCabTic);

    /**
     * Consultando Detalle del ticket para saber quien fue el Usuario que Abrio el Ticket
     */
    $qDetTic = "SELECT * ";
    $qDetTic .= "FROM $cAlfa.work1002 ";
    $qDetTic .= "WHERE ";
    $qDetTic .= "ticidxxx = \"{$gTicId}\" AND ";
    $qDetTic .= "repidxxx = \"1\" LIMIT 0,1 ";
    $xDetTic = mysql_query($qDetTic, $xConexion01);
    //f_Mensaje(__FILE__,__LINE__,$qDetTic."~".mysql_num_rows($xDetTic));
    $vDetTic = mysql_fetch_array($xDetTic);

    switch ($vCabTic['doctipxx']) {
      case "IMPORTACION":
        $qTramite = "SELECT ";
        $qTramite .= "$cAlfa.SIAI0200.ADMIDXXX,";
        $qTramite .= "$cAlfa.SIAI0200.DOIIDXXX,";
        $qTramite .= "$cAlfa.SIAI0200.DOISFIDX,";
        $qTramite .= "$cAlfa.SIAI0200.DOIPEDXX,";
        $qTramite .= "$cAlfa.SIAI0200.CLIIDXXX,";
        $qTramite .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX "; //Nombre Cliente
        $qTramite .= "FROM $cAlfa.SIAI0200 ";
        $qTramite .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.SIAI0200.CLIIDXXX = $cAlfa.SIAI0150.CLIIDXXX ";
        $qTramite .= "WHERE ";
        $qTramite .= "$cAlfa.SIAI0200.DOIIDXXX = \"{$vCabTic['docidxxx']}\" AND ";
        $qTramite .= "$cAlfa.SIAI0200.DOISFIDX = \"{$vCabTic['docsufxx']}\" AND ";
        $qTramite .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$vCabTic['sucidxxx']}\" LIMIT 0,1 ";
        break;
      case "EXPORTACION":
        $qTramite = "SELECT ";
        $qTramite .= "$cAlfa.siae0199.admidxxx AS ADMIDXXX,";
        $qTramite .= "$cAlfa.siae0199.dexidxxx AS DOIIDXXX,";
        $qTramite .= "$cAlfa.siae0199.dexpedxx AS DOIPEDXX,";
        $qTramite .= "$cAlfa.siae0199.cliidxxx AS CLIIDXXX,";
        $qTramite .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX "; //Nombre Cliente
        $qTramite .= "FROM $cAlfa.siae0199 ";
        $qTramite .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.siae0199.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
        $qTramite .= "WHERE ";
        $qTramite .= "$cAlfa.siae0199.dexidxxx = \"{$vCabTic['docidxxx']}\" AND ";
        $qTramite .= "$cAlfa.siae0199.admidxxx = \"{$vCabTic['sucidxxx']}\" LIMIT 0,1 ";
        break;
    }

    //$xTramite  = f_MySql("SELECT","",$qTramite,$xConexion01,"");
    $xTramite = mysql_query($qTramite, $xConexion01);
    //f_Mensaje(__FILE__,__LINE__,$qTramite."~".mysql_num_rows($xTramite));
    $vTramite = mysql_fetch_array($xTramite);
    $vTramite['DOCIDXXX'] = ($vTramite['DOISFIDX'] != "") ? ($vTramite['ADMIDXXX']."-".$vTramite['DOIIDXXX']."-".$vTramite['DOISFIDX']) : ($vTramite['ADMIDXXX']."-".$vTramite['DOIIDXXX']);

    /**
     * Traigo Datos de correo del Usuario que esta creando el Ticket
     */
    $qCorUsu = "SELECT * ";
    $qCorUsu .= "FROM $cAlfa.SIAI0003 ";
    $qCorUsu .= "WHERE ";
    $qCorUsu .= "USRIDXXX = \"$gUsrId\" AND ";
    $qCorUsu .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
    $xCorUsu = mysql_query($qCorUsu, $xConexion01);
    //$xCorUsu  = f_MySql("SELECT","",$qCorUsu,$xConexion01,"");
    $vCorUsu = mysql_fetch_array($xCorUsu);
    ?>
    <script type="text/javascript">
      var cSucId = document.getElementById("cSucId");
      cSucId.value = "<?php echo $vCabTic['sucidxxx']; ?>";
      var cDocId = document.getElementById("cDocId");
      cDocId.value = "<?php echo $vCabTic['docidxxx']; ?>";
      var cDocSuf = document.getElementById("cDocSuf");
      cDocSuf.value = "<?php echo $vCabTic['docsufxx']; ?>";
      var cDocTip = document.getElementById("cDocTip");
      cDocTip.value = "<?php echo $vCabTic['doctipxx']; ?>";
      var cDo = document.getElementById("cDo");
      cDo.value = "<?php echo $vTramite['DOCIDXXX']; ?>";
      var cDoiPed = document.getElementById("cDoiPed");
      cDoiPed.value = "<?php echo $vTramite['DOIPEDXX']; ?>";
      var cCliId = document.getElementById("cCliId");
      cCliId.value = "<?php echo $vTramite['CLIIDXXX']; ?>";
      var cCliNom = document.getElementById("cCliNom");
      cCliNom.value = "<?php echo $vTramite['CLINOMXX']; ?>";
      var cDe = document.getElementById("cDe");
      cDe.value = "<?php echo $vCorUsu['USRNOMXX']." <".$vCorUsu['USREMAXX'].">"; ?>";
      var cFrom = document.getElementById("cFrom");
      cFrom.value = "<?php echo $vCorUsu['USREMAXX']; ?>";
      var cTicAsu = document.getElementById("cTicAsu");
      cTicAsu.value = "<?php echo $vCabTic['ticasuxx']; ?>";
      var cTipId = document.getElementById("cTipId");
      cTipId.value = "<?php echo $vCabTic['tipidxxx']; ?>";
      var cTipDes = document.getElementById("cTipDes");
      cTipDes.value = "<?php echo $vCabTic['tipdesxx']; ?>";
      var cTipRes = document.getElementById("cTipRes");
      cTipRes.value = "<?php echo $vCabTic['tipresxx']; ?>";
      var cPriId = document.getElementById("cPriId");
      cPriId.value = "<?php echo $vCabTic['priidxxx']; ?>";
      var cStsId = document.getElementById("cStsId");
      cStsId.value = "<?php echo $vCabTic['stsidxxx']; ?>";
      var cCc = document.getElementById("cCc");
      cCc.value = "<?php echo $vCabTic['ticoccxx']; ?>";
      var cUsrAti = document.getElementById("cUsrAti");
      cUsrAti.value = "<?php echo $vDetTic['regusrxx']; ?>";
    </script>
    <?php
  }

  if (count($mUsuarios) == 1) {
    ?>
    <script type="text/javascript">
      var cCheck = document.getElementById("c<?php echo $mUsuarios[0]['USRIDXXX'] ?>");
      cCheck.checked = true;
      var cCadena = document.getElementById("cCadena");
      cCadena.value = '<?php echo $mUsuarios[0]['USRIDXXX']; ?>' + '~';
    </script>
    <?php
  }
  ?>

  <script type="text/javascript">
    var cCanRes = document.getElementById("cCanRes");
    cCanRes.value = '<?php echo count($mUsuarios); ?>';
  </script>
</body>
</html>

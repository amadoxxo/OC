<?php

/**
 * Tracking Mis Tickets.
 * --- Descripcion: Este programa permite listar y consultar los registros de Mis Tickets asignados que se encuentran en la Base de Datos
 * @author Cristian Perdomo. <cristian.perdomo@openits.co>
 * @package opencomex
 * @version 001
 */
include("../../../../../financiero/libs/php/utility.php");

$kDf = explode("~", $_COOKIE["kDatosFijos"]);
$kMysqlDb = $kDf[3];

/* Busco en la 05 que Tiene Permiso el Usuario*/
$qUsrMen  = "SELECT * ";
$qUsrMen .= "FROM $cAlfa.sys00039 ";
$qUsrMen .= "WHERE ";
$qUsrMen .= "sys00039.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
$qUsrMen .= "sys00039.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
$qUsrMen .= "sys00039.menimgon != \"\" ";
$qUsrMen .= "ORDER BY sys00039.menordxx";
$xUsrMen  = f_MySql("SELECT", "", $qUsrMen, $xConexion01, "");
?>
<html>

<head>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
  <LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
  <script languaje='javascript' src='<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
  <script languaje='javascript' src='<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
  <script language="javascript">
    function fnVer(xTickId, xRegfcre) {
      var nWidth = 1000;
      var nHeight = 500;
      var nX = screen.width;
      var nY = screen.height;
      var nNx = (nX - nWidth) / 2;
      var nNy = (nY - nHeight) / 2;
      var cWinOpt = "width=" + nWidth + ",scrollbars=1,height=" + nHeight + ",left=" + nNx + ",top=" + nNy;
      document.cookie = "kMenDes=Historico Ticket;path=" + "/";
      document.cookie = "kModo=VER;path=" + "/";
      // Construye la URL con los parámetros de consulta
      var url = 'frmtiovn.php?xTickId=' + encodeURIComponent(xTickId);

      // Abre una nueva ventana con la URL generada
      var cWindow = window.open(url, 'cConInd', cWinOpt);
      cWindow.focus();
    }

   function fnEditar(xModo) {
      var countChecked = 0;
      var selectedId = "";
      var selectedStatus = "";

      // Recorre todos los checkboxes y cuenta cuántos están marcados
      var checkboxes = document.forms['frgrm']['oCheck'];
      for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked == true) {
          countChecked++;
          selectedId = checkboxes[i].id.split('~')[0]; // Extrae ticidxxx del id
          selectedStatus = checkboxes[i].id.split('~')[1];
        }
      }

      // Comprueba si hay más de un checkbox marcado
      if (countChecked == 1) {
        if (selectedStatus == 'CIERRE') {
          alert('No puede editar un ticket que ya esta en CIERRE');
        }else{
          var nWidth = 900;
          var nHeight = 500;
          var nX = screen.width;
          var nY = screen.height;
          var nNx = (nX - nWidth) / 2;
          var nNy = (nY - nHeight) / 2;
          var cWinOpt = "width=" + nWidth + ",scrollbars=1,height=" + nHeight + ",left=" + nNx + ",top=" + nNy;
          document.cookie = "kMenDes=Reply Ticket;path=" + "/";
          document.cookie = "kModo=EDITAR;path=" + "/";
  
          // Construye la URL con los parámetros de consulta, incluyendo ticidxxx
          var url = 'frmtinue.php?ticketId=' + encodeURIComponent(selectedId);
  
          // Abre una nueva ventana con la URL generada
          var cWindow = window.open(url, 'cConInd', cWinOpt);
          cWindow.focus();
        }
      } else if (countChecked > 1) {
        alert('Solo puede seleccionar un solo Ticket');
      } else {
        alert('No hay ningun Ticket Seleccionado');
      }
    }

    function fnLink(xModId, xProId, xMenId, xForm, xOpcion, xMenDes) {
      document.cookie = "kIniAnt=<?php echo substr($_SERVER['PHP_SELF'], (strrpos($_SERVER['PHP_SELF'], "/") + 1), strlen($_SERVER['PHP_SELF'])) ?>;path=" + "/";
      document.cookie = "kMenDes=" + xMenDes + ";path=" + "/";
      document.cookie = "kModo=" + xOpcion + ";path=" + "/";
      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
      document.location = xForm; // Invoco el menu.
    }

    function fnMarca() {
      if (document.forms['frgrm']['oCheckAll'].checked == true) {
        if (document.forms['frgrm']['vRecords'].value == 1) {
          document.forms['frgrm']['oCheck'].checked = true;
        } else {
          if (document.forms['frgrm']['vRecords'].value > 1) {
            for (i = 0; i < document.forms['frgrm']['oCheck'].length; i++) {
              document.forms['frgrm']['oCheck'][i].checked = true;
            }
          }
        }
      } else {
        if (document.forms['frgrm']['vRecords'].value == 1) {
          document.forms['frgrm']['oCheck'].checked = false;
        } else {
          if (document.forms['frgrm']['vRecords'].value > 1) {
            for (i = 0; i < document.forms['frgrm']['oCheck'].length; i++) {
              document.forms['frgrm']['oCheck'][i].checked = false;
            }
          }
        }
      }
    }

    function fnOrderBy(xEvento, xCampo) {
      if (document.forms['frgrm'][xCampo].value != '') {
        var vSwitch = document.forms['frgrm'][xCampo].value.split(' ');
        var cSwitch = vSwitch[1];
      } else {
        var cSwitch = '';
      }

      if (xEvento == 'onclick') {
        switch (cSwitch) {
          case '':
            document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id + ' ASC,';
            document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/s_asc.png';
            if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
              document.forms['frgrm']['cOrderByOrder'].value += xCampo + "~";
            }
            break;
          case 'ASC,':
            document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id + ' DESC,';
            document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/s_desc.png';
            if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
              document.forms['frgrm']['cOrderByOrder'].value += xCampo + "~";
            }
            break;
          case 'DESC,':
            document.forms['frgrm'][xCampo].value = '';
            document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png';
            if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) >= 0) {
              document.forms['frgrm']['cOrderByOrder'].value = document.forms['frgrm']['cOrderByOrder'].value.replace(xCampo, "");
            }
            break;
        }

        document.forms['frgrm']['vSearch'].value = document.forms['frgrm']['vSearch'].value.toUpperCase();
        document.forms['frgrm']['vLimInf'].value = '00';
        document.forms['frgrm']['vLimSup'].value = '30';
        document.forms['frgrm']['vPaginas'].value = '1';
        document.forms['frgrm'].submit();
      } else {
        switch (cSwitch) {
          case '':
            document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png';
            break;
          case 'ASC,':
            document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/s_asc.png';
            break;
          case 'DESC,':
            document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/s_desc.png';
            break;
        }
      }
    }

    function fnConsultaInducida() {
      var nWidth = 600;
      var nHeight = 450;
      var nX = screen.width;
      var nY = screen.height;
      var nNx = (nX - nWidth) / 2;
      var nNy = (nY - nHeight) / 2;
      var cWinOpt = "width=" + nWidth + ",scrollbars=1,height=" + nHeight + ",left=" + nNx + ",top=" + nNy;
      cWindow = window.open('', 'cConInd', cWinOpt);
      document.forms['frgrm'].action = 'frmtimex.php';
      document.forms['frgrm'].target = 'cConInd';
      document.forms['frgrm'].submit();
      document.forms['frgrm'].target = 'fmwork';
      document.forms['frgrm'].action = 'frmtiini.php';
      cWindow.focus();
    }

    function fnEnviarConsultaInducida(xDatos) {
      document.forms['frgrm']['cPeriodos'].value = xDatos['cPeriodos'];
      document.forms['frgrm']['dDesde'].value = xDatos['dDesde'];
      document.forms['frgrm']['dHasta'].value = xDatos['dHasta'];
      document.forms['frgrm']['cTicket'].value = xDatos['cTicket'];
      document.forms['frgrm']['cTiAsun'].value = xDatos['cTiAsun'];
      document.forms['frgrm']['cCerId'].value = xDatos['cCerId'];
      document.forms['frgrm']['cCerComCod'].value = xDatos['cCerComCod'];
      document.forms['frgrm']['cCerComCsc'].value = xDatos['cCerComCsc'];
      document.forms['frgrm']['cCliId'].value = xDatos['cCliId'];
      document.forms['frgrm']['cCpiId'].value = xDatos['cCpiId'];
      document.forms['frgrm']['cResId'].value = xDatos['cResId'];
      document.forms['frgrm']['cTipId'].value = xDatos['cTipId'];
      document.forms['frgrm']['cPriori'].value = xDatos['cPriori'];
      document.forms['frgrm']['cStatus'].value = xDatos['cStatus'];
      document.forms['frgrm'].submit();
    }

    function fnImprimir() {
      var form = document.getElementById('frgrm');
      var formData = new FormData(form);
      var queryString = new URLSearchParams(formData).toString();

      // Redirigir a la URL con los datos del formulario
      parent.fmpro.location = 'frmtiprn.php?' + queryString;
    }
  </script>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginheight="0" marginwidth="0">
  <form name="frestado" action="frcergra.php" method="post" target="fmpro">
    <input type="hidden" name="cCerId" value="">
    <input type="hidden" name="cCerComCod" value="">
    <input type="hidden" name="cCerComCsc" value="">
    <input type="hidden" name="gComId" value="">
    <input type="hidden" name="gComCod" value="">
    <input type="hidden" name="gComPre" value="">
    <input type="hidden" name="gComCsc" value="">
    <input type="hidden" name="gComCsc2" value="">
    <input type="hidden" name="cAnio" value="">
    <input type="hidden" name="gRegEst" value="">
    <input type="hidden" name="gObservacion" id="gObservacion">
  </form>

  <form name="frgrm" id="frgrm" action="frmtiini.php" method="post" target="fmwork">
    <input type="hidden" name="vRecords" value="">
    <input type="hidden" name="vLimInf" value="<?php echo $vLimInf ?>">
    <input type="hidden" name="vSortField" value="<?php echo $vSortField ?>">
    <input type="hidden" name="vSortType" value="<?php echo $vSortType ?>">
    <input type="hidden" name="vTimes" value="<?php echo $vTimes ?>">
    <input type="hidden" name="vBuscar" value="<?php echo $_POST['vBuscar'] ?>">
    <input type="hidden" name="cOrderByOrder" value="<?php echo $_POST['cOrderByOrder'] ?>" style="width:1000">
    <!--Campos ocultos de la consulta inducida-->
    <input type="hidden" name="cTicket" value="<?php echo $cTicket ?>">
    <input type="hidden" name="cTiAsun" value="<?php echo $cTiAsun ?>">
    <input type="hidden" name="cCerId" value="<?php echo $cCerId ?>">
    <input type="hidden" name="cCerComCod" value="<?php echo $cCerComCod ?>">
    <input type="hidden" name="cCerComCsc" value="<?php echo $cCerComCsc ?>">
    <input type="hidden" name="cCliId" value="<?php echo $cCliId ?>">
    <input type="hidden" name="cCpiId" value="<?php echo $cCpiId ?>">
    <input type="hidden" name="cResId" value="<?php echo $cResId ?>">
    <input type="hidden" name="cTipId" value="<?php echo $cTipId ?>">
    <input type="hidden" name="cPriori" value="<?php echo $cPriori ?>">
    <input type="hidden" name="cStatus" value="<?php echo $cStatus ?>">

    <!-- Inicia Nivel de Procesos -->
    <?php if (mysql_num_rows($xUsrMen) > 0) { ?>
      <center>
        <table width="95%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td>
              <fieldset>
                <legend>Proceso <?php echo $_COOKIE['kProDes'] ?></legend>
                <center>
                  <table cellspacing="0" width="100%">
                    <?php
                    $y = 0;
                    // Empiezo a Leer la sys00039
                    while ($mUsrMen = mysql_fetch_array($xUsrMen)) {
                      if ($y == 0 || $y % 5 == 0) {
                        if ($y == 0) { ?>
                          <tr>
                          <?php } else { ?>
                          </tr>
                          <tr>
                          <?php }
                      }
                      // Busco de la sys00039 en la sys00040
                      $qUsrPer  = "SELECT * ";
                      $qUsrPer .= "FROM $cAlfa.sys00040 ";
                      $qUsrPer .= "WHERE ";
                      $qUsrPer .= "usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
                      $qUsrPer .= "modidxxx = \"{$mUsrMen['modidxxx']}\"  AND ";
                      $qUsrPer .= "proidxxx = \"{$mUsrMen['proidxxx']}\"  AND ";
                      $qUsrPer .= "menidxxx = \"{$mUsrMen['menidxxx']}\"  LIMIT 0,1";
                      $xUsrPer = f_MySql("SELECT", "", $qUsrPer, $xConexion01, "");
                      if (mysql_num_rows($xUsrPer) > 0) { ?>
                          <td Class="clase08" width="20%">
                            <center><img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/<?php echo $mUsrMen['menimgon'] ?>" style="cursor:pointer" onClick="javascript:fnLink('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx'] ?>','<?php echo $mUsrMen['menopcxx'] ?>','<?php echo $mUsrMen['mendesxx'] ?>')"><br>
                              <a href="javascript:fnLink('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx'] ?>','<?php echo $mUsrMen['menopcxx'] ?>','<?php echo $mUsrMen['mendesxx'] ?>')" style="color:<?php echo $vSysStr['system_link_menu_color'] ?>"><?php echo $mUsrMen['mendesxx'] ?></a>
                            </center>
                          </td>
                        <?php } else { ?>
                          <td Class="clase08" width="20%">
                            <center><img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/<?php echo $mUsrMen['menimgof'] ?>"><br>
                              <?php echo $mUsrMen['mendesxx'] ?></center>
                          </td>
                      <?php }
                      $y++;
                    }
                    $celdas = "";
                    $nf = intval($y / 5);
                    $resto = $y - $nf;
                    $restan = 5 - $resto;
                    if ($restan > 0) {
                      for ($i = 0; $i < $restan; $i++) {
                        $celdas .= "<td width='20%'></td>";
                      }
                      echo $celdas;
                    } ?>
                          </tr>
                  </table>
                </center>
              </fieldset>
            </td>
          </tr>
        </table>
      </center>
    <?php } ?>
    <!-- Fin Nivel de Procesos -->
    <?php
    if ($vLimInf == "" && $vLimSup == "") {
      $vLimInf = "00";
      $vLimSup = $vSysStr['system_rows_page_ini'];
    } elseif ($vLimInf == "") {
      $vLimInf = "00";
    }

    if (substr_count($vLimInf, "-") > 0) {
      $vLimInf = "00";
    }

    if ($vPaginas == "") {
      $vPaginas = "1";
    }

    // Si viene vacio el $cOfvSap lo cargo con la Cookie de la Oficina de Venta
    // Si no hago el SELECT con el Oficina de Venta que me entrega el combo del INI
    if (empty($cOfvSap)) {
      $cOfvSap  = "";
    } else {
      // Si el $cOfvSap viene cargado del combo con "ALL" es porque Debo mostrar todos las Oficina de Venta
      // Si no dejo la Oficina de Venta que viene cargada
      if ($cOfvSap == "ALL") {
        $cOfvSap = "";
      }
    }

    /**INICIO SQL**/
    if ($_POST['cPeriodos'] == "") {
      $_POST['cPeriodos'] == "20";
      $_POST['dDesde'] = substr(date('Y-m-d'), 0, 8) . "01";
      $_POST['dHasta'] = date('Y-m-d');
    }

    $nAnioDesde = substr($_POST['dDesde'], 0, 4);
    $nAnioDesde = ($nAnioDesde < $vSysStr['logistica_ano_instalacion_modulo']) ? $vSysStr['logistica_ano_instalacion_modulo'] : $nAnioDesde;

    $mMiTicket = array();
    for ($iAno = $nAnioDesde; $iAno <= substr($_POST['dHasta'], 0, 4); $iAno++) { // Recorro desde el anio de inicio hasta el anio de fin de la consulta

      /* CONSULTA TIPOS TICKETS */
      $qTipoTicket .= "SELECT ";
      $qTipoTicket .= "$cAlfa.lpar0159.tticodxx ";
      $qTipoTicket .= "FROM $cAlfa.lpar0159 ";
      $qTipoTicket .= "LEFT JOIN $cAlfa.lpar0158 ON $cAlfa.lpar0159.tticodxx = $cAlfa.lpar0158.tticodxx ";
      $qTipoTicket .= "WHERE $cAlfa.lpar0159.ttiusrxx = \"{$_COOKIE['kUsrId']}\" AND ";
      $qTipoTicket .= "$cAlfa.lpar0158.regestxx = \"ACTIVO\"";
      $xTipoTicket  = f_MySql("SELECT", "", $qTipoTicket, $xConexion01, "");

      $tticodxxArray = [];
      while ($vTipoTicket = mysql_fetch_assoc($xTipoTicket)) {
        $tticodxxArray[] = $vTipoTicket['tticodxx'];
      }

      $tticodxxList = "'" . implode("','", $tticodxxArray) . "'";


      if ($iAno == $nAnioDesde) {
        $qMiTicket  = "SELECT DISTINCT ";
        //$qMiTicket .= "SQL_CALC_FOUND_ROWS ";
      } else {
        $qMiTicket  .= "SELECT DISTINCT ";
      }

      $qMiTicket .= "$cAlfa.ltic$iAno.ticidxxx, ";   // Id Ticket
      $qMiTicket .= "$cAlfa.ltic$iAno.ceridxxx, ";   // Id certificacion
      $qMiTicket .= "$cAlfa.ltic$iAno.comidxxx, ";   // Id del Comprobante
      $qMiTicket .= "$cAlfa.ltic$iAno.comcodxx, ";   // Codigo del Comprobante
      $qMiTicket .= "$cAlfa.ltic$iAno.comprexx, ";   // Prefijo
      $qMiTicket .= "$cAlfa.ltic$iAno.comcscxx, ";   // Consecutivo Uno
      $qMiTicket .= "$cAlfa.ltic$iAno.comcsc2x, ";   // Consecutivo Dos
      $qMiTicket .= "$cAlfa.ltic$iAno.comfecxx, ";   // Fecha Comprobante
      $qMiTicket .= "$cAlfa.ltic$iAno.cliidxxx, ";    // Id cliente
      $qMiTicket .= "$cAlfa.ltic$iAno.tticodxx, ";   // Codigo Tipo Ticket
      $qMiTicket .= "$cAlfa.ltic$iAno.pticodxx, ";   // Codigo Prioridad Ticket
      $qMiTicket .= "$cAlfa.ltic$iAno.sticodxx, ";   // Codigo Status Ticket
      $qMiTicket .= "$cAlfa.ltic$iAno.ticasuxx, ";   // Asunto
      $qMiTicket .= "$cAlfa.ltic$iAno.ticcierx, ";   // Fecha de cierre
      $qMiTicket .= "$cAlfa.ltic$iAno.regusrxx, ";   // Usuario que creo el registro
      $qMiTicket .= "$cAlfa.ltic$iAno.regfcrex, ";   // Fecha de creación
      $qMiTicket .= "$cAlfa.ltic$iAno.reghcrex, ";   // Hora de creación
      $qMiTicket .= "$cAlfa.ltic$iAno.regfmodx, ";   // Fecha de modificación
      $qMiTicket .= "$cAlfa.ltic$iAno.reghmodx, ";   // Hora de modificación
      $qMiTicket .= "$cAlfa.ltic$iAno.regestxx, ";   // Estado
      $qMiTicket .= "$cAlfa.lpar0150.clinomxx, ";   // Razon social
      $qMiTicket .= "$cAlfa.lpar0158.ttidesxx, ";   // Descripcion Ticket
      $qMiTicket .= "$cAlfa.lpar0156.pticolxx, ";   // Color
      $qMiTicket .= "$cAlfa.lpar0156.ptidesxx, ";   // Proiridad descripcion
      $qMiTicket .= "$cAlfa.lpar0157.stidesxx, ";   // Status descripcion
      $qMiTicket .= "$cAlfa.lpar0157.stitipxx, ";   // Status tipo
      $qMiTicket .= "$cAlfa.SIAI0003.USRNOMXX AS usrnomxx, ";   // Creado por
      $qMiTicket .= "GROUP_CONCAT(SIAI0003_2.USRNOMXX SEPARATOR ', ') AS responsables ";   // Responsables
      $qMiTicket .= "FROM $cAlfa.ltic$iAno ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.ltic$iAno.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0158 ON $cAlfa.ltic$iAno.tticodxx = $cAlfa.lpar0158.tticodxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0156 ON $cAlfa.ltic$iAno.pticodxx = $cAlfa.lpar0156.pticodxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0157 ON $cAlfa.ltic$iAno.sticodxx = $cAlfa.lpar0157.sticodxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.ltic$iAno.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
      $qMiTicket .= "LEFT JOIN $cAlfa.lpar0159 ON $cAlfa.ltic$iAno.tticodxx = $cAlfa.lpar0159.tticodxx ";
      $qMiTicket .= "LEFT JOIN $cAlfa.SIAI0003 AS SIAI0003_2 ON $cAlfa.lpar0159.ttiusrxx = SIAI0003_2.USRIDXXX ";
      $qMiTicket .= "WHERE ";
      if ($_POST['vSearch'] != "") {
        $qMiTicket .= "(";
        $qMiTicket .= "CONCAT($cAlfa.ltic$iAno.comprexx ,\"\",$cAlfa.ltic$iAno.comcscxx ) LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qMiTicket .= "$cAlfa.ltic$iAno.cliidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qMiTicket .= "$cAlfa.ltic$iAno.ticidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qMiTicket .= "$cAlfa.lpar0158.ttidesxx  LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qMiTicket .= "$cAlfa.ltic$iAno.ticasuxx LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qMiTicket .= "$cAlfa.SIAI0003.usrnomxx  LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qMiTicket .= "$cAlfa.lpar0156.ptidesxx  LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qMiTicket .= "$cAlfa.lpar0157.stidesxx  LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qMiTicket .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qMiTicket .= "$cAlfa.ltic$iAno.regestxx LIKE \"%{$_POST['vSearch']}%\") AND ";
      }

      // Campos de la Consulta inducida

      // Buscando por Ticket id
      if ($_POST['cTicket'] != "") {
        $qMiTicket .= "$cAlfa.ltic$iAno.ticidxxx = \"{$_POST['cTicket']}\" AND ";
      }
      // Buscando por Asunto
      if ($_POST['cTiAsun'] != "") {
        $qMiTicket .= "$cAlfa.ltic$iAno.ticasuxx LIKE \"%{$_POST['cTiAsun']}%\" AND ";
      }
      // Buscando por certificado
      if ($_POST['cCerId'] != "") {
        $qMiTicket .= "$cAlfa.ltic$iAno.comcodxx = \"{$_POST['cCerComCod']}\" AND ";
        $qMiTicket .= "$cAlfa.ltic$iAno.comcscxx = \"{$_POST['cCerComCsc']}\" AND ";
      }
      // Buscando por Cliente
      if ($_POST['cCliId'] != "") {
        $qMiTicket .= "$cAlfa.ltic$iAno.cliidxxx = \"{$_POST['cCliId']}\" AND ";
      }
      // Creado por
      if ($_POST['cCpiId'] != "") {
        $qMiTicket .= "$cAlfa.ltic$iAno.regusrxx = \"{$_POST['cCpiId']}\" AND ";
      }
      // Tipo ticket
      if ($_POST['cTipId'] != "") {
        $qMiTicket .= "$cAlfa.ltic$iAno.tticodxx = \"{$_POST['cTipId']}\" AND ";
      }
      // Prioridad ticket
      if ($_POST['cPriori'] != "") {
        $qMiTicket .= "$cAlfa.ltic$iAno.pticodxx = \"{$_POST['cPriori']}\" AND ";
      }
      // Status
      if ($_POST['cStatus'] != "") {
        $qMiTicket .= "$cAlfa.ltic$iAno.sticodxx = \"{$_POST['cStatus']}\" AND ";
      }

      /*  //certificacion
          if ($_POST['cStatus'] != "") {
            $qMiTicket .= "$cAlfa.ltic$iAno.sticodxx = \"{$_POST['cStatus']}\" AND ";
          } */

      // Fin Campos de la Consulta inducida
      $qMiTicket .= "$cAlfa.ltic$iAno.regfcrex BETWEEN \"{$_POST['dDesde']}\" AND \"{$_POST['dHasta']}\" AND ";
      $qMiTicket .= "$cAlfa.ltic$iAno.tticodxx IN ($tticodxxList) ";
      /***** FIN SQL *****/

      if ($iAno >= $nAnioDesde && $iAno < substr($_POST['dHasta'], 0, 4)) {
        $qMiTicket .= " UNION ";
      }
    } ## for ($iAno=$nAnioDesde;$iAno<=substr($_POST['dHasta'],0,4);$iAno++) { ##

    //// CODIGO NUEVO PARA ORDER BY
    $cOrderBy = "";
    $vOrderByOrder = explode("~", $_POST['cOrderByOrder']);
    for ($z = 0; $z < count($vOrderByOrder); $z++) {
      if ($vOrderByOrder[$z] != "") {
        if ($_POST[$vOrderByOrder[$z]] != "") {
          $cOrderBy .= $_POST[$vOrderByOrder[$z]];
        }
      }
    }
    if (strlen($cOrderBy) > 0) {
      $cOrderBy = substr($cOrderBy, 0, strlen($cOrderBy) - 1);
      $cOrderBy = "ORDER BY " . $cOrderBy;
    } else {
      $cOrderBy = "ORDER BY regfmodx DESC ";
    }

    $qMiTicket .= "GROUP BY ticidxxx ";
    //// FIN CODIGO NUEVO PARA ORDER BY
    $qMiTicket .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
    $xMiTicket  = f_MySql("SELECT", "", $qMiTicket, $xConexion01, "");
    //f_Mensaje(__FILE__,__LINE__,$qMiTicket."~".mysql_num_rows($xMiTicket));
    // echo $qMiTicket."~".mysql_num_rows($xMiTicket);

    $xNumRows = mysql_query("SELECT FOUND_ROWS();", $xConexion01);
    $xRNR = mysql_fetch_array($xNumRows);
    $nRNR += $xRNR['FOUND_ROWS()'];

    $mMatrizUsr = array();
    $vExisteUsr = array();
    while ($xRMI = mysql_fetch_array($xMiTicket)) {
      if (!in_array($xRMI['regusrxx'], $vExisteUsr)) {
        $vExisteUsr[] = $xRMI['regusrxx'];

        // Busco la informacion del usuario autenticado
        $qUsrNom  = "SELECT USRIDXXX, USRNOMXX, REGESTXX ";
        $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
        $qUsrNom .= "WHERE ";
        $qUsrNom .= "USRIDXXX = \"{$xRMI['regusrxx']}\"";
        $xUsrNom = f_MySql("SELECT", "", $qUsrNom, $xConexion01, "");
        if (mysql_num_rows($xUsrNom) > 0) {
          $vUsrNom = mysql_fetch_array($xUsrNom);
          $nInd_mMatrizUsr = count($mMatrizUsr);
          $mMatrizUsr[$nInd_mMatrizUsr]['usridxxx'] = $vUsrNom['USRIDXXX'];
          $mMatrizUsr[$nInd_mMatrizUsr]['usrnomxx'] = $vUsrNom['USRNOMXX'];
          $mMatrizUsr[$nInd_mMatrizUsr]['regestxx'] = $vUsrNom['REGESTXX'];
        }
      }
      $mMiTicket[count($mMiTicket)] = $xRMI;
    }
    ?>
    <center>
      <table width="95%" cellspacing="0" cellpadding="0" border="0">
        <tr>
          <td>
            <fieldset>
              <legend>Registros en la Consulta (<?php echo $nRNR ?>)</legend>
              <center>
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                  <tr>
                    <td class="clase08" width="40%" align="left">
                      <input type="text" class="letra" name="vSearch" maxlength="20" value="<?php echo $vSearch ?>" style="width:80" onblur="javascript:this.value=this.value.toUpperCase();
                                            document.frgrm.vLimInf.value='00'; ">
                      <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_search.png" style="cursor:pointer" title="Buscar" onClick="javascript:document.forms['frgrm']['vBuscar'].value = 'ON';
                                                document.frgrm.vSearch.value=document.frgrm.vSearch.value.toUpperCase();
                                                if ((document.forms['frgrm']['dHasta'].value < document.forms['frgrm']['dDesde'].value) ||
                                                  document.forms['frgrm']['dDesde'].value == '' || document.forms['frgrm']['dHasta'].value == '') {
                                                  alert('El Sistema no Puede Hacer la Busqueda por Error en las Fechas del Periodo a Buscar, Verifique.');
                                                } else {
                                                  if (document.forms['frgrm']['vPaginas'].id == 'ON') {
                                                    document.forms['frgrm']['vPaginas'].id = 'OFF'
                                                  } else {
                                                    document.forms['frgrm']['vPaginas'].value='1';
                                                  };
                                                  document.forms['frgrm']['vLimInf'].value='00';
                                                  document.forms['frgrm'].submit();
                                                }">
                      <img src="<?php echo $cPlesk_Skin_Directory ?>/cert_ca_cert_on.gif" style="cursor:pointer" title="Consulta inducida" onClick="javascript:fnConsultaInducida()">
                      <script language="javascript">
                        if ("<?php echo $_POST['cBusExc'] ?>" == "SI") {
                          document.forms['frgrm']['cBusExc'].value = "SI";
                          document.forms['frgrm']['cBusExc'].checked = true;
                        } else {
                          document.forms['frgrm']['cBusExc'].value = "NO";
                          document.forms['frgrm']['cBusExc'].checked = false;
                        }
                      </script>
                    </td>
                    <td class="name" width="03%" align="left">Filas&nbsp;
                      <input type="text" class="letra" name="vLimSup" value="<?php echo $vLimSup ?>" style="width:30;text-align:right" onfocus="javascript:document.forms['frgrm']['vPaginas'].value='1'" onblur="javascript:f_FixFloat(this);
                                                document.forms['frgrm']['vLimInf'].value='00';
                                                document.forms['frgrm'].submit()">
                    </td>
                    <td class="name" width="20%" align="center">
                      <?php if (ceil($nRNR / $vLimSup) > 1) { ?>
                        <?php if ($vPaginas == "1") { ?>
                          <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_firstpage.png" style="cursor:pointer" title="Primera Pagina">
                          <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_prevpage.png" style="cursor:pointer" title="Pagina Anterior">
                          <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_nextpage.png" style="cursor:pointer" title="Pagina Siguiente" onClick="javascript:document.frgrm.vPaginas.value++;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_lastpage.png" style="cursor:pointer" title="Ultima Pagina" onClick="javascript:document.frgrm.vPaginas.value='<?php echo ceil($nRNR / $vLimSup) ?>';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                        <?php } ?>
                        <?php if ($vPaginas > "1" && $vPaginas < ceil($nRNR / $vLimSup)) { ?>
                          <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_firstpage.png" style="cursor:pointer" title="Primera Pagina" onClick="javascript:document.frgrm.vPaginas.value='1';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_prevpage.png" style="cursor:pointer" title="Pagina Anterior" onClick="javascript:document.frgrm.vPaginas.value--;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_nextpage.png" style="cursor:pointer" title="Pagina Siguiente" onClick="javascript:document.frgrm.vPaginas.value++;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_lastpage.png" style="cursor:pointer" title="Ultima Pagina" onClick="javascript:document.frgrm.vPaginas.value='<?php echo ceil($nRNR / $vLimSup) ?>';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                        <?php } ?>
                        <?php if ($vPaginas == ceil($nRNR / $vLimSup)) { ?>
                          <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_firstpage.png" style="cursor:pointer" title="Primera Pagina" onClick="javascript:document.frgrm.vPaginas.value='1';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_prevpage.png" style="cursor:pointer" title="Pagina Anterior" onClick="javascript:document.frgrm.vPaginas.value--;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_nextpage.png" style="cursor:pointer" title="Pagina Siguiente">
                          <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_lastpage.png" style="cursor:pointer" title="Ultima Pagina">
                        <?php } ?>
                      <?php } else { ?>
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_firstpage.png" style="cursor:pointer" title="Primera Pagina">
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_prevpage.png" style="cursor:pointer" title="Pagina Anterior">
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_nextpage.png" style="cursor:pointer" title="Pagina Siguiente">
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_lastpage.png" style="cursor:pointer" title="Ultima Pagina">
                      <?php } ?>
                    </td>
                    <td class="name" width="09%" align="center">Pag&nbsp;
                      <select Class="letrase" name="vPaginas" value="<?php echo $vPaginas ?>" style="width:60%" onchange="javascript:this.id = 'ON'; // Cambio 18, Incluir este Codigo.
                                              document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(this.value-1));
                                              document.frgrm.submit();">
                        <?php for ($i = 0; $i < ceil($nRNR / $vLimSup); $i++) {
                          if ($i + 1 == $vPaginas) { ?>
                            <option value="<?php echo $i + 1 ?>" selected><?php echo $i + 1 ?></option>
                          <?php } else { ?>
                            <option value="<?php echo $i + 1 ?>"><?php echo $i + 1 ?></option>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </td>
                    <td class="name" width="12%" align="center">
                      <select class="letrase" size="1" name="cPeriodos" style="width:100%" value="<?php echo $_POST['cPeriodos'] ?>" onChange="javascript:
                                      parent.fmpro.location='<?php echo $cSystem_Libs_Php_Directory ?>/utilfepe.php?gTipo='+this.value+'&gForm='+'frgrm'+'&gFecIni='+'dDesde'+'&gFecFin='+'dHasta';
                                      if (document.forms['frgrm']['cPeriodos'].value == '99') {
                                        document.forms['frgrm']['dDesde'].readOnly = false;
                                        document.forms['frgrm']['dHasta'].readOnly = false;
                                        document.forms['frgrm']['vLimInf'].value = '00';
                                      } else {
                                        document.forms['frgrm']['dDesde'].readOnly = true;
                                        document.forms['frgrm']['dHasta'].readOnly = true;
                                        document.forms['frgrm']['vLimInf'].value = '00';
                                      }">
                        <option value="10">Hoy</option>
                        <option value="15">Esta Semana</option>
                        <option value="20">Este Mes</option>
                        <option value="25">Este A&ntilde;o</option>
                        <option value="30">Ayer</option>
                        <option value="35">Semana Pasada</option>
                        <option value="40">Semana Pasada Hasta Hoy</option>
                        <option value="45">Mes Pasado</option>
                        <option value="50">Mes Pasado Hasta Hoy</option>
                        <option value="55">Ultimos Tres Meses</option>
                        <option value="60">Ultimos Seis Meses</option>
                        <option value="65">Ultimo A&ntilde;o</option>
                        <option value="99">Periodo Especifico</option>
                      </select>
                      <script language="javascript">
                        if ("<?php echo $_POST['cPeriodos'] ?>" == "") {
                          document.forms['frgrm']['cPeriodos'].value = "20";
                        } else {
                          document.forms['frgrm']['cPeriodos'].value = "<?php echo $_POST['cPeriodos'] ?>";
                        }
                      </script>
                    </td>
                    <td class="name" width="06%" align="center">
                      <input type="text" Class="letra" style="width:90%;text-align:center" name="dDesde" value="<?php
                                                                                                                if ($_POST['dDesde'] == "" && $_POST['cPeriodos'] == "") {
                                                                                                                  echo substr(date('Y-m-d'), 0, 8) . "01";
                                                                                                                } else {
                                                                                                                  echo $_POST['dDesde'];
                                                                                                                } ?>" onblur="javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));" readonly>
                    </td>
                    <td class="name" width="06%" align="center">
                      <input type="text" Class="letra" style="width:90%;text-align:center" name="dHasta" value="<?php
                                                                                                                if ($_POST['dHasta'] == "" && $_POST['cPeriodos'] == "") {
                                                                                                                  echo date('Y-m-d');
                                                                                                                } else {
                                                                                                                  echo $_POST['dHasta'];
                                                                                                                }  ?>" onblur="javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1)); " readonly>
                    </td>
                    <script language="javascript">
                      if (document.forms['frgrm']['cPeriodos'].value == "99") {
                        document.forms['frgrm']['dDesde'].readOnly = false;
                        document.forms['frgrm']['dHasta'].readOnly = false;
                      } else {
                        document.forms['frgrm']['dDesde'].readOnly = true;
                        document.forms['frgrm']['dHasta'].readOnly = true;
                      }
                    </script>

                    <td Class="name" align="right">&nbsp;
                      <?php
                      /***** Botones de Acceso Rapido *****/
                      $qBotAcc  = "SELECT * ";
                      $qBotAcc .= "FROM $cAlfa.sys00039,$cAlfa.sys00040 ";
                      $qBotAcc .= "WHERE ";
                      $qBotAcc .= "sys00040.usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
                      $qBotAcc .= "sys00040.modidxxx = sys00039.modidxxx        AND ";
                      $qBotAcc .= "sys00040.proidxxx = sys00039.proidxxx        AND ";
                      $qBotAcc .= "sys00040.menidxxx = sys00039.menidxxx        AND ";
                      $qBotAcc .= "sys00040.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
                      $qBotAcc .= "sys00040.proidxxx = \"{$_COOKIE['kProId']}\" ";
                      $qBotAcc .= "ORDER BY sys00039.menordxx";

                      $xBotAcc  = f_MySql("SELECT", "", $qBotAcc, $xConexion01, "");
                      // f_Mensaje(__FILE__, __LINE__, $qBotAcc."~".mysql_num_rows($xBotAcc));
                      while ($mBotAcc = mysql_fetch_array($xBotAcc)) {
                        switch ($mBotAcc['menopcxx']) {
                          case "REPORTE": ?>
                            <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/excel_icon.gif" onClick="javascript:fnImprimir('<?php echo $mBotAcc['menopcxx'] ?>')" style="cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                          <?php break;
                          case "EDITAR": ?>
                            <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_edit.png" onClick="javascript:fnEditar('<?php echo $mBotAcc['menopcxx'] ?>')" style="cursor:pointer" title="Editar Reply">
                      <?php break;
                        }
                      }
                      /***** Fin Botones de Acceso Rapido *****/
                      ?>
                    </td>
                  </tr>
                </table>
              </center>
              <hr>
              </hr>
              <center>
                <table cellspacing="0" width="100%">
                  <tr bgcolor='<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                    <td class="name" width="09%">
                      <a href="javascript:fnOrderBy('onclick','ceridxxx');" title="Ordenar">Ticket</a>&nbsp;
                      <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="ceridxxx">
                      <input type="hidden" name="ceridxxx" value="<?php echo $_POST['ceridxxx'] ?>" id="ceridxxx">
                      <script language="javascript">
                        fnOrderBy('', 'ceridxxx')
                      </script>
                    </td>
                    <td class="name" width="11%">
                      <a href="javascript:fnOrderBy('onclick','cliidxxx');" title="Ordenar">Cliente</a>&nbsp;
                      <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="cliidxxx">
                      <input type="hidden" name="cliidxxx" value="<?php echo $_POST['cliidxxx'] ?>" id="cliidxxx">
                      <script language="javascript">
                        fnOrderBy('', 'cliidxxx')
                      </script>
                    </td>
                    <td class="name" width="11%">
                      <a href="javascript:fnOrderBy('onclick','clisapxx');" title="Ordenar">Certificaci&oacute;n</a>&nbsp;
                      <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="clisapxx">
                      <input type="hidden" name="clisapxx" value="<?php echo $_POST['clisapxx'] ?>" id="clisapxx">
                      <script language="javascript">
                        fnOrderBy('', 'clisapxx')
                      </script>
                    </td>
                    <td class="name" width="12%">
                      <a href="javascript:fnOrderBy('onclick','clinomxx');" title="Ordenar">Tipo</a>&nbsp;
                      <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="clinomxx">
                      <input type="hidden" name="clinomxx" value="<?php echo $_POST['clinomxx'] ?>" id="clinomxx">
                      <script language="javascript">
                        fnOrderBy('', 'clinomxx')
                      </script>
                    </td>
                    <td class="name" width="15%">
                      <a href="javascript:fnOrderBy('onclick','depnumxx');" title="Ordenar">Creado por</a>&nbsp;
                      <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="depnumxx">
                      <input type="hidden" name="depnumxx" value="<?php echo $_POST['depnumxx'] ?>" id="depnumxx">
                      <script language="javascript">
                        fnOrderBy('', 'depnumxx')
                      </script>
                    </td>
                    <td class="name" width="11%">
                      <a href="javascript:fnOrderBy('onclick','cerfdexx');" title="Ordenar">Asunto</a>&nbsp;
                      <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="cerfdexx">
                      <input type="hidden" name="cerfdexx" value="<?php echo $_POST['cerfdexx'] ?>" id="cerfdexx">
                      <script language="javascript">
                        fnOrderBy('', 'cerfdexx')
                      </script>
                    </td>
                    <td class="name" width="11%">
                      <a href="javascript:fnOrderBy('onclick','cerfhaxx');" title="Ordenar">Responsable</a>&nbsp;
                      <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="cerfhaxx">
                      <input type="hidden" name="cerfhaxx" value="<?php echo $_POST['cerfhaxx'] ?>" id="cerfhaxx">
                      <script language="javascript">
                        fnOrderBy('', 'cerfhaxx')
                      </script>
                    </td>
                    <td class="name" width="06%">
                      <a href="javascript:fnOrderBy('onclick','regfcrex');" title="Ordenar">Creado</a>&nbsp;
                      <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="regfcrex">
                      <input type="hidden" name="regfcrex" value="<?php echo $_POST['regfcrex'] ?>" id="regfcrex">
                      <script language="javascript">
                        fnOrderBy('', 'regfcrex')
                      </script>
                    </td>
                    <td class="name" width="08%">
                      <a href="javascript:fnOrderBy('onclick','regfmodx');" title="Ordenar">Prioridad</a>&nbsp;
                      <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="regfmodx">
                      <input type="hidden" name="regfmodx" value="<?php echo $_POST['regfmodx'] ?>" id="regfmodx">
                      <script language="javascript">
                        fnOrderBy('', 'regfmodx')
                      </script>
                    </td>
                    <td class="name" width="08%">
                      <a href="javascript:fnOrderBy('onclick','regfmodx');" title="Ordenar">Status</a>&nbsp;
                      <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="regfmodx">
                      <input type="hidden" name="regfmodx" value="<?php echo $_POST['regfmodx'] ?>" id="regfmodx">
                      <script language="javascript">
                        fnOrderBy('', 'regfmodx')
                      </script>
                    </td>

                    <td Class='name' width="02%" align="right">
                      <input type="checkbox" name="oCheckAll" onClick='javascript:fnMarca()'>
                    </td>
                  </tr>
                  <script languaje="javascript">
                    document.forms['frgrm']['vRecords'].value = "<?php echo count($mMiTicket) ?>";
                  </script>

                  <?php
                  for ($i = 0; $i < count($mMiTicket); $i++) {
                    if ($i < count($mMiTicket)) { // Para Controlar el Error
                      $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                      if ($y % 2 == 0) {
                        $cColor = "{$vSysStr['system_row_par_color_ini']}";
                      } ?>
                      <tr bgcolor="<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
                        <td class="letra7"><a href=javascript:fnVer('<?php echo $mMiTicket[$i]['ticidxxx'] ?>','<?php echo $mMiTicket[$i]['regfcrex'] ?>')>
                            <?php echo $mMiTicket[$i]['ticidxxx'] ?> </a></td>
                        <td class="letra7"><?php echo $mMiTicket[$i]['clinomxx'] ?></td>
                        <td class="letra7"><?php echo $mMiTicket[$i]['comidxxx'] . "-" . $mMiTicket[$i]['comprexx'] . $mMiTicket[$i]['comcscxx'] ?></td>
                        <td class="letra7"><?php echo $mMiTicket[$i]['ttidesxx'] ?></td>
                        <td class="letra7"><?php echo $mMiTicket[$i]['usrnomxx'] ?></td>
                        <td class="letra7"><?php echo $mMiTicket[$i]['ticasuxx'] ?></td>
                        <td class="letra7"><?php echo $mMiTicket[$i]['responsables'] ?></td>
                        <td class="letra7"><?php echo $mMiTicket[$i]['regfcrex'] ?></td>
                        <td class="letra7" style="color: <?php echo $mMiTicket[$i]['pticolxx']; ?>;"><b><?php echo $mMiTicket[$i]['ptidesxx'] ?></b></td>
                        <td class="letra7"><?php echo $mMiTicket[$i]['stidesxx'] ?></td>

                        <td Class="letra7" align="right">
                          <input type="checkbox" name="oCheck" value="<?php echo  mysql_num_rows($xMiTicket) ?>" id="<?php echo $mMiTicket[$i]['ticidxxx'] . '~' . //[0] Id Ticket
                                                                                                                        $mMiTicket[$i]['stitipxx'] ?>" onclick="javascript:document.forms['frgrm']['vRecords'].value='<?php echo count($mMiTicket) ?>'">
                        </td>
                      </tr>
                    <?php $y++;
                    }
                  }

                  if (count($mMiTicket) == 1) { ?>
                    <script language="javascript">
                      document.forms['frgrm']['oCheck'].checked = true;
                    </script>
                  <?php
                  }
                  ?>
                </table>
              </center>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
  </form>
</body>

</html>
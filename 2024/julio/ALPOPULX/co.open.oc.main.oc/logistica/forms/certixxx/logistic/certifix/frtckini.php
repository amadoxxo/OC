<?php
  /**
   * Tracking Ver Tickets.
   * --- Descripcion: Este programa permite listar y consultar los registros de Ver Tickets.
   * @author Elian Amado. <elian.amado@openits.co>
   * @package opencomex
   * @version 001
   */
  include("../../../../../financiero/libs/php/utility.php");

?>
<html>
  <head>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language = "javascript">

      function fnVer(xCerId,xRegfcre) {
        var cPathUrl = "frtckovn.php?cCerId="+xCerId+"&cAnio="+xRegfcre.substr(0,4);
        document.cookie = "kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie = "kMenDes=Ver Tickets;path="+"/";
        document.cookie = "kModo=VER;path="+"/";
        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
        document.location = cPathUrl; // Invoco el menu.
      }

      function fnEditar(xModo, otherData) {
        var countChecked = 0; // Variable para contar los checkboxes marcados

        // Recorre todos los checkboxes y cuenta cuántos están marcados
        for (var i = 0; i < document.forms['frgrm']['oCheck'].length; i++) {
            if (document.forms['frgrm']['oCheck'][i].checked == true) {
                countChecked++;
            }
        }

        // Comprueba si hay más de un checkbox marcado
        if (countChecked > 1) {
            alert('Hay más de uno marcado');
        } else if (condition){
            alert('Hay uno o ninguno marcado');
        }

        // Configuración de la ventana emergente
        var nWidth  = 900;
        var nHeight = 500;
        var nX      = screen.width;
        var nY      = screen.height;
        var nNx     = (nX - nWidth) / 2;
        var nNy     = (nY - nHeight) / 2;
        var cWinOpt = "width=" + nWidth + ",scrollbars=1,height=" + nHeight + ",left=" + nNx + ",top=" + nNy;
        
        // Construye la URL con los parámetros de consulta
        var url = 'frmtiovn.php?xModo=' + encodeURIComponent(xModo) + '&otherData=' + encodeURIComponent(otherData);
        
        // Abre una nueva ventana con la URL generada
        var cWindow = window.open(url, 'cConInd', cWinOpt);
        cWindow.focus();
      }

      function fnLink(xModId,xProId,xMenId,xForm,xOpcion,xMenDes) {
        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes="+xMenDes+";path="+"/";
        document.cookie="kModo="+xOpcion+";path="+"/";
        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
        document.location = xForm; // Invoco el menu.
      }

      function fnMarca() {
        if (document.forms['frgrm']['oCheckAll'].checked == true){
          if (document.forms['frgrm']['vRecords'].value == 1){
            document.forms['frgrm']['oCheck'].checked = true;
          } else {
            if (document.forms['frgrm']['vRecords'].value > 1){
              for (i = 0; i < document.forms['frgrm']['oCheck'].length; i++){
                document.forms['frgrm']['oCheck'][i].checked = true;
              }
            }
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value == 1){
            document.forms['frgrm']['oCheck'].checked = false;
          } else {
            if (document.forms['frgrm']['vRecords'].value > 1){
              for (i = 0; i < document.forms['frgrm']['oCheck'].length; i++){
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

          document.forms['frgrm']['vSearch'].value=document.forms['frgrm']['vSearch'].value.toUpperCase();
          document.forms['frgrm']['vLimInf'].value='00';
          document.forms['frgrm']['vLimSup'].value='30';
          document.forms['frgrm']['vPaginas'].value='1';
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
    </script>
  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
    <form name = "frestado" action = "frcergra.php" method = "post" target="fmpro">
      <input type = "hidden" name = "cCerId"       value = "">
      <input type = "hidden" name = "gComId"       value = "">
      <input type = "hidden" name = "gComCod"      value = "">
      <input type = "hidden" name = "gComPre"      value = "">
      <input type = "hidden" name = "gComCsc"      value = "">
      <input type = "hidden" name = "gComCsc2"     value = "">
      <input type = "hidden" name = "cAnio"        value = "">
      <input type = "hidden" name = "gRegEst"      value = "">
      <input type = "hidden" name = "gObservacion" id="gObservacion">
    </form>

    <form name = "frgrm" id="frgrm" action = "frtckini.php" method = "post" target="fmwork">
      <input type = "hidden" name = "vRecords"   value = "">
      <input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
      <input type = "hidden" name = "vSortField" value = "<?php echo $vSortField ?>">
      <input type = "hidden" name = "vSortType"  value = "<?php echo $vSortType ?>">
      <input type = "hidden" name = "vTimes"     value = "<?php echo $vTimes ?>">
      <input type = "hidden" name = "vBuscar"    value = "<?php echo $_POST['vBuscar'] ?>">
      <input type = "hidden" name = "cOrderByOrder"  value = "<?php echo $_POST['cOrderByOrder'] ?>" style = "width:1000">
      <input type = "text" name = "cCerId"        value = "<?php echo $cCerId ?>">

      <?php
        if ($vLimInf == "" && $vLimSup == "") {
          $vLimInf = "00";
          $vLimSup = $vSysStr['system_rows_page_ini'];
        } elseif ($vLimInf == "") {
          $vLimInf = "00";
        }

        if (substr_count($vLimInf,"-") > 0) {
          $vLimInf = "00";
        }

        if ($vPaginas == "") {
          $vPaginas = "1";
        }

        /**INICIO SQL**/
        if ($_POST['cPeriodos'] == "") {
          $_POST['cPeriodos'] == "20";
          $_POST['dDesde'] = substr(date('Y-m-d'),0,8)."01";
          $_POST['dHasta'] = date('Y-m-d');
        }

        $nAnioDesde = substr($_POST['dDesde'], 0, 4);
        $nAnioDesde = ($nAnioDesde < $vSysStr['logistica_ano_instalacion_modulo']) ? $vSysStr['logistica_ano_instalacion_modulo'] : $nAnioDesde;
        
        $mMiTicket = array();
        for ($iAno = $nAnioDesde; $iAno <= substr($_POST['dHasta'],0,4); $iAno++) { // Recorro desde el anio de inicio hasta el anio de fin de la consulta

          /* CONSULTA TIPOS TICKETS */
          $qTipoTicket .= "SELECT ";
          $qTipoTicket .= "$cAlfa.lpar0159.tticodxx ";
          $qTipoTicket .= "FROM $cAlfa.lpar0159 ";
          $qTipoTicket .= "LEFT JOIN $cAlfa.lpar0158 ON $cAlfa.lpar0159.tticodxx = $cAlfa.lpar0158.tticodxx ";
          $qTipoTicket .= "WHERE $cAlfa.lpar0159.ttiusrxx = \"{$_COOKIE['kUsrId']}\" AND ";
          $qTipoTicket .= "$cAlfa.lpar0158.regestxx = \"ACTIVO\"";
          $xTipoTicket  = f_MySql("SELECT","",$qTipoTicket,$xConexion01,"");

          $tticodxxArray = [];
          while ($vTipoTicket = mysql_fetch_assoc($xTipoTicket)) {
              $tticodxxArray[] = $vTipoTicket['tticodxx'];
          }

          $tticodxxList = "'" . implode("','", $tticodxxArray) . "'";


          if ($iAno == $nAnioDesde) {
            $qMiTicket  = "SELECT DISTINCT ";
            //$qMiTicket .= "SQL_CALC_FOUND_ROWS ";
          }else {
            $qMiTicket  .= "SELECT DISTINCT ";
          }

          $qMiTicket .= "$cAlfa.ltic$iAno.ticidxxx, ";  // Id Ticket
          $qMiTicket .= "$cAlfa.ltic$iAno.ceridxxx, ";  // Id certificacion
          $qMiTicket .= "$cAlfa.ltic$iAno.comidxxx, ";  // Id del Comprobante
          $qMiTicket .= "$cAlfa.ltic$iAno.comcodxx, ";  // Codigo del Comprobante
          $qMiTicket .= "$cAlfa.ltic$iAno.comprexx, ";  // Prefijo
          $qMiTicket .= "$cAlfa.ltic$iAno.comcscxx, ";  // Consecutivo Uno
          $qMiTicket .= "$cAlfa.ltic$iAno.comcsc2x, ";  // Consecutivo Dos
          $qMiTicket .= "$cAlfa.ltic$iAno.comfecxx, ";  // Fecha Comprobante
          $qMiTicket .= "$cAlfa.ltic$iAno.cliidxxx,";   // Id cliente
          $qMiTicket .= "$cAlfa.ltic$iAno.tticodxx, ";  // Codigo Tipo Ticket
          $qMiTicket .= "$cAlfa.ltic$iAno.pticodxx, ";  // Codigo Prioridad Ticket
          $qMiTicket .= "$cAlfa.ltic$iAno.sticodxx, ";  // Codigo Status Ticket
          $qMiTicket .= "$cAlfa.ltic$iAno.ticasuxx, ";  // Asunto
          $qMiTicket .= "$cAlfa.ltic$iAno.ticcierx, ";  // Fecha de cierre
          $qMiTicket .= "$cAlfa.ltic$iAno.regusrxx, ";  // Usuario que creo el registro
          $qMiTicket .= "$cAlfa.ltic$iAno.regfcrex, ";  // Fecha de creación
          $qMiTicket .= "$cAlfa.ltic$iAno.reghcrex, ";  // Hora de creación
          $qMiTicket .= "$cAlfa.ltic$iAno.regfmodx, ";  // Fecha de modificación
          $qMiTicket .= "$cAlfa.ltic$iAno.reghmodx, ";  // Hora de modificación
          $qMiTicket .= "$cAlfa.ltic$iAno.regestxx, ";  // Estado
          $qMiTicket .= "$cAlfa.lpar0150.clinomxx, ";   // Razon social
          $qMiTicket .= "$cAlfa.lpar0158.ttidesxx, ";   // Descripcion Ticket
          $qMiTicket .= "$cAlfa.lpar0156.pticolxx, ";   // Color
          $qMiTicket .= "$cAlfa.lpar0156.ptidesxx, ";   // Proiridad descripcion
          $qMiTicket .= "$cAlfa.lpar0157.stidesxx, ";   // Status
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
            $qMiTicket .= "$cAlfa.ltic$iAno.ticidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
            $qMiTicket .= "$cAlfa.lpar0158.ttidesxx  LIKE \"%{$_POST['vSearch']}%\" OR ";
            $qMiTicket .= "$cAlfa.ltic$iAno.ticasuxx LIKE \"%{$_POST['vSearch']}%\" OR ";
            $qMiTicket .= "$cAlfa.SIAI0003.usrnomxx  LIKE \"%{$_POST['vSearch']}%\" OR ";
            $qMiTicket .= "$cAlfa.lpar0156.ptidesxx  LIKE \"%{$_POST['vSearch']}%\" OR ";
            $qMiTicket .= "$cAlfa.lpar0157.stidesxx  LIKE \"%{$_POST['vSearch']}%\" OR ";
          }

          // Consulta por el usuario
          if ($cUsrId != "" && $cUsrId != "ALL") {
            $qMiTicket .= "$cAlfa.ltic$iAno.regusrxx = \"$cUsrId\" AND ";
          }
          // Consulta por el estado
          if ($cEstado != "") {
            $qMiTicket .= "$cAlfa.ltic$iAno.regestxx = \"$cEstado\" AND ";
          }
          // Consulta por Id del certificado
          if ($cCerId != "" ) {
            $qMiTicket .= "$cAlfa.ltic$iAno.ceridxxx = \"$cCerId\" AND ";
          }
          $qMiTicket .= "$cAlfa.ltic$iAno.regfcrex BETWEEN \"{$_POST['dDesde']}\" AND \"{$_POST['dHasta']}\" AND ";
          $qMiTicket .= "$cAlfa.ltic$iAno.tticodxx IN ($tticodxxList) ";
          /***** FIN SQL *****/

          if ($iAno >= $nAnioDesde && $iAno < substr($_POST['dHasta'],0,4)) {
            $qMiTicket .= " UNION ";
          }
        } ## for ($iAno=$nAnioDesde;$iAno<=substr($_POST['dHasta'],0,4);$iAno++) { ##
        // CODIGO NUEVO PARA ORDER BY
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
        // FIN CODIGO NUEVO PARA ORDER BY
        $qMiTicket .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
        $xMiTicket  = f_MySql("SELECT","",$qMiTicket,$xConexion01,"");
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
            $xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
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
                        <input type="text" class="letra" name = "vSearch" maxlength="20" value = "<?php echo $vSearch ?>" style= "width:80"
                          onblur="javascript:this.value=this.value.toUpperCase();
                                            document.frgrm.vLimInf.value='00'; ">
                        <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_search.png" style = "cursor:pointer" title="Buscar"
                          onClick = "javascript:document.forms['frgrm']['vBuscar'].value = 'ON';
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
                      </td>
                      <td class="name" width="03%" align="left">Filas&nbsp;
                        <input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
                          onfocus = "javascript:document.forms['frgrm']['vPaginas'].value='1'"
                          onblur = "javascript:f_FixFloat(this);
                                                document.forms['frgrm']['vLimInf'].value='00';
                                                document.forms['frgrm'].submit()">
                      </td>
                      <td class="name" width="20%" align="center">
                        <?php if (ceil($nRNR/$vLimSup) > 1) { ?>
                          <?php if ($vPaginas == "1") { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_nextpage.png"    style = "cursor:pointer" title="Pagina Siguiente"
                              onClick = "javascript:document.frgrm.vPaginas.value++;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_lastpage.png"    style = "cursor:pointer" title="Ultima Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <?php } ?>
                          <?php if ($vPaginas > "1" && $vPaginas < ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='1';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
                              onClick = "javascript:document.frgrm.vPaginas.value--;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente"
                              onClick = "javascript:document.frgrm.vPaginas.value++;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <?php } ?>
                          <?php if ($vPaginas == ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='1';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
                              onClick = "javascript:document.frgrm.vPaginas.value--;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_nextpage.png" style = "cursor:pointer" title="Pagina Siguiente">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_lastpage.png" style = "cursor:pointer" title="Ultima Pagina">
                          <?php } ?>
                        <?php } else { ?>
                          <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
                          <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
                          <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente">
                          <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina">
                        <?php } ?>
                      </td>
                      <td class="name" width="09%" align="center">Pag&nbsp;
                        <select Class = "letrase" name = "vPaginas" value = "<?php echo $vPaginas ?>" style = "width:60%"
                          onchange="javascript:this.id = 'ON'; // Cambio 18, Incluir este Codigo.
                                              document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(this.value-1));
                                              document.frgrm.submit();">
                          <?php for ($i=0;$i<ceil($nRNR/$vLimSup);$i++) {
                            if ($i+1 == $vPaginas) { ?>
                              <option value = "<?php echo $i+1 ?>" selected><?php echo $i+1 ?></option>
                            <?php } else { ?>
                              <option value = "<?php echo $i+1 ?>"><?php echo $i+1 ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </td>
                      <td class="name" width="12%" align="center" >
                        <select class="letrase" size="1" name="cPeriodos" style = "width:100%" value = "<?php echo $_POST['cPeriodos'] ?>"
                          onChange = "javascript:
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
                          <option value = "10">Hoy</option>
                          <option value = "15">Esta Semana</option>
                          <option value = "20">Este Mes</option>
                          <option value = "25">Este A&ntilde;o</option>
                          <option value = "30">Ayer</option>
                          <option value = "35">Semana Pasada</option>
                          <option value = "40">Semana Pasada Hasta Hoy</option>
                          <option value = "45">Mes Pasado</option>
                          <option value = "50">Mes Pasado Hasta Hoy</option>
                          <option value = "55">Ultimos Tres Meses</option>
                          <option value = "60">Ultimos Seis Meses</option>
                          <option value = "65">Ultimo A&ntilde;o</option>
                          <option value = "99">Periodo Especifico</option>
                        </select>
                        <script language = "javascript">
                          if ("<?php echo $_POST['cPeriodos'] ?>" == "") {
                            document.forms['frgrm']['cPeriodos'].value = "20";
                          } else {
                            document.forms['frgrm']['cPeriodos'].value = "<?php echo $_POST['cPeriodos'] ?>";
                          }
                        </script>
                      </td>
                      <td class="name" width="06%" align="center">
                        <input type = "text" Class = "letra" style = "width:90%;text-align:center" name = "dDesde" value = "<?php
                        if($_POST['dDesde']=="" && $_POST['cPeriodos'] == ""){
                          echo substr(date('Y-m-d'),0,8)."01";
                        } else{
                          echo $_POST['dDesde'];
                        } ?>"
                          onblur="javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));" readonly>
                      </td>
                      <td class="name" width="06%" align="center">
                        <input type = "text" Class = "letra" style = "width:90%;text-align:center" name = "dHasta" value = "<?php
                          if($_POST['dHasta']=="" && $_POST['cPeriodos'] == ""){
                            echo date('Y-m-d');
                          } else{
                            echo $_POST['dHasta'];
                          }  ?>"
                          onblur = "javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1)); " readonly>
                      </td>
                      <script language = "javascript">
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

                          $xBotAcc  = f_MySql("SELECT","",$qBotAcc,$xConexion01,"");
                          // f_Mensaje(__FILE__, __LINE__, $qBotAcc."~".mysql_num_rows($xBotAcc));
                          while ($mBotAcc = mysql_fetch_array($xBotAcc)) {
                            switch ($mBotAcc['menopcxx']) {
                              case "EDITAR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_edit.png" onClick = "javascript:fnEditar('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="Editar Reply">
                              <?php break;
                            }
                          }
                          /***** Fin Botones de Acceso Rapido *****/
                        ?>
                      </td>
                    </tr>
                  </table>
                </center>
                <hr></hr>
                <center>
                  <table cellspacing="0" width="100%">
                    <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                      <td class="name" width="05%">
                        <a href = "javascript:fnOrderBy('onclick','ticidxxx');" title="Ordenar">Ticket</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ticidxxx">
                        <input type = "hidden" name = "ticidxxx" value = "<?php echo $_POST['ticidxxx'] ?>" id = "ticidxxx">
                        <script language="javascript">fnOrderBy('','ticidxxx')</script>
                      </td>
                      <td class="name" width="12%">
                        <a href = "javascript:fnOrderBy('onclick','ttidesxx');" title="Ordenar">Tipo</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ttidesxx">
                        <input type = "hidden" name = "ttidesxx" value = "<?php echo $_POST['ttidesxx'] ?>" id = "ttidesxx">
                        <script language="javascript">fnOrderBy('','ttidesxx')</script>
                      </td>
                      <td class="name" width="20%">
                        <a href = "javascript:fnOrderBy('onclick','ticasuxx');" title="Ordenar">Asunto</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ticasuxx">
                        <input type = "hidden" name = "ticasuxx" value = "<?php echo $_POST['ticasuxx'] ?>" id = "ticasuxx">
                        <script language="javascript">fnOrderBy('','ticasuxx')</script>
                      </td>
                      <td class="name" width="16%">
                        <a href = "javascript:fnOrderBy('onclick','usrnomxx');" title="Ordenar">Creado por</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "usrnomxx">
                        <input type = "hidden" name = "usrnomxx" value = "<?php echo $_POST['usrnomxx'] ?>" id = "usrnomxx">
                        <script language="javascript">fnOrderBy('','usrnomxx')</script>
                      </td>
                      <td class="name" width="20%">
                        <a href = "javascript:fnOrderBy('onclick','responsables');" title="Ordenar">Responsable</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "responsables">
                        <input type = "hidden" name = "responsables" value = "<?php echo $_POST['responsables'] ?>" id = "responsables">
                        <script language="javascript">fnOrderBy('','responsables')</script>
                      </td>
                      <td class="name" width="10%">
                        <a href = "javascript:fnOrderBy('onclick','regfcrex');" title="Ordenar">Creado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfcrex">
                        <input type = "hidden" name = "regfcrex" value = "<?php echo $_POST['regfcrex'] ?>" id = "regfcrex">
                        <script language="javascript">fnOrderBy('','regfcrex')</script>
                      </td>
                      <td class="name" width="10%">
                        <a href = "javascript:fnOrderBy('onclick','ptidesxx');" title="Ordenar">Prioridad</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ptidesxx">
                        <input type = "hidden" name = "ptidesxx" value = "<?php echo $_POST['ptidesxx'] ?>" id = "ptidesxx">
                        <script language="javascript">fnOrderBy('','ptidesxx')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:fnOrderBy('onclick','stidesxx');" title="Ordenar">Status</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "stidesxx">
                        <input type = "hidden" name = "stidesxx" value = "<?php echo $_POST['stidesxx'] ?>" id = "stidesxx">
                        <script language="javascript">fnOrderBy('','stidesxx')</script>
                      </td>
                      
                      <td Class='name' width="02%" align="right">
                        <input type="checkbox" name="oCheckAll" onClick = 'javascript:fnMarca()'>
                      </td>
                    </tr>
                    <script languaje="javascript">
                      document.forms['frgrm']['vRecords'].value = "<?php echo count($mMiTicket) ?>";
                    </script>

                    <?php
                      for ($i=0;$i<count($mMiTicket);$i++) {
                        if ($i < count($mMiTicket)) { // Para Controlar el Error
                          $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                          if($y % 2 == 0) {
                            $cColor = "{$vSysStr['system_row_par_color_ini']}";
                          } ?>
                          <tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')"
                            onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
                            <td class="letra7"><a href = javascript:fnVer('<?php echo $mMiTicket[$i]['ceridxxx']?>','<?php echo $mMiTicket[$i]['regfcrex']?>')>
                                                        <?php echo $mMiTicket[$i]['ticidxxx'] ?> </a></td>
                            <td class="letra7"><?php echo $mMiTicket[$i]['ttidesxx'] ?></td>
                            <td class="letra7"><?php echo $mMiTicket[$i]['ticasuxx'] ?></td>
                            <td class="letra7"><?php echo $mMiTicket[$i]['usrnomxx'] ?></td>
                            <td class="letra7"><?php echo $mMiTicket[$i]['responsables'] ?></td>
                            <td class="letra7"><?php echo $mMiTicket[$i]['regfcrex'] ?></td>
                            <td class="letra7"><?php echo $mMiTicket[$i]['ptidesxx'] ?></td>
                            <td class="letra7"><?php echo $mMiTicket[$i]['stidesxx'] ?></td>
                            
                            <td Class="letra7" align="right">
                              <input type="checkbox" name="oCheck" value = "<?php echo  mysql_num_rows($xMiTicket) ?>"
                              id="<?php echo $mMiTicket[$i]['ticidxxx'].'~'. //[0] Id Ticket
                                            $mMiTicket[$i]['stidesxx'].'~'.  //[1] Status Ticket
                                            $mMiTicket[$i]['comidxxx'].'~'.  //[2]
                                            $mMiTicket[$i]['comcodxx'].'~'.  //[3]
                                            $mMiTicket[$i]['comcscxx'].'~'.  //[4]
                                            $mMiTicket[$i]['comcsc2x'].'~'.  //[5]
                                            $mMiTicket[$i]['regestxx'].'~'.  //[6]
                                            $mMiTicket[$i]['comprexx']       //[7] ?>"
                              onclick="javascript:document.forms['frgrm']['vRecords'].value='<?php echo count($mMiTicket) ?>'">
                            </td>
                          </tr>
                          <?php $y++;
                        }
                      }

                      if(count($mMiTicket) == 1){ ?>
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
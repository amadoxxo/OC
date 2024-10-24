<?php
  namespace openComex;
  /**
   * Autorizacion Pagos a Terceros.
   * --- Descripcion: lista Las Observaciones a DO's.
   * @author Juan Jose Hernandez <juan.hernandez@openits.co>
   * @version 001
   */
  include("../../../../libs/php/utility.php");
  
  // Busco en la 05 que Tiene Permiso el Usuario
  $qUsrMen  = "SELECT * ";
  $qUsrMen .= "FROM $cAlfa.sys00005 ";
  $qUsrMen .= "WHERE ";
  $qUsrMen .= "sys00005.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
  $qUsrMen .= "sys00005.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
  $qUsrMen .= "sys00005.menimgon != \"\" ";
  $qUsrMen .= "ORDER BY sys00005.menordxx";
  $xUsrMen  = f_MySql("SELECT","",$qUsrMen,$xConexion01,"");

  $cPerAno = date('Y');
  $cPerMes = date('m');
?>

<html>
  <head>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    <script language="javascript">
      function fnLink(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes="+xMenDes+";path="+"/";
        document.cookie="kModo="+xOpcion;
        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
        document.location = xForm; // Invoco el menu.
      }

      function fnEnviarConsultaInducida(xDatos){
        document.forms['frgrm']['cConsultaInducida'].value = xDatos['cConsultaInducida'];
        document.forms['frgrm']['cPeriodos'].value = xDatos['cPeriodos'];
        document.forms['frgrm']['dDesde'].value    = xDatos['dDesde'];
        document.forms['frgrm']['dHasta'].value    = xDatos['dHasta'];
        document.forms['frgrm']['cCcoId'].value    = xDatos['cCcoId'];
        document.forms['frgrm']['cUsrId'].value    = xDatos['cUsrId'];
        document.forms['frgrm']['cDo'].value       = xDatos['cDo'];

        document.forms['frgrm'].submit();
      }

      function fnConsultaInducida(){
        var nWidth  = 520;
        var nHeight = 400;
        var nX      = screen.width;
        var nY      = screen.height;
        var nNx     = (nX-nWidth)/2;
        var nNy     = (nY-nHeight)/2;
        var cWinOpt = "width="+nWidth+",scrollbars=1,height="+nHeight+",left="+nNx+",top="+nNy;

        cWindow = window.open('', 'cConInd', cWinOpt);
        document.forms['frgrm'].action = 'fraramex.php';
        document.forms['frgrm'].target = 'cConInd';
        document.forms['frgrm'].submit();
        document.forms['frgrm'].target = 'fmwork';
        document.forms['frgrm'].action = 'fraptini.php';
        cWindow.focus();
      }

      function fnMarca() {
        if (document.forms['frgrm']['vCheckAll'].checked == true){
          if (document.forms['frgrm']['vRecords'].value == 1){
            document.forms['frgrm']['vCheck'].checked=true;
          } else {
            if (document.forms['frgrm']['vRecords'].value > 1){
              for (i=0;i<document.forms['frgrm']['vCheck'].length;i++){
                document.forms['frgrm']['vCheck'][i].checked = true;
              }
            }
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value == 1){
            document.forms['frgrm']['vCheck'].checked=false;
          } else {
            if (document.forms['frgrm']['vRecords'].value > 1){
              for (i=0;i<document.forms['frgrm']['vCheck'].length;i++){
                document.forms['frgrm']['vCheck'][i].checked = false;
              }
            }
          }
        }
      }

      function fnVerificarCheck() {
        if(document.forms['frgrm']['vCheckAll'].checked == true)
          document.forms['frgrm']['vChekeados'].value=1;
        if (document.forms['frgrm']['vRecords'].value == 1){
          if(document.forms['frgrm']['vCheck'].checked == true)
            document.forms['frgrm']['vChekeados'].value=1;
        }else {
          if (document.forms['frgrm']['vRecords'].value > 1){
            for (i=0;i<document.forms['frgrm']['vCheck'].length;i++){
              if(document.forms['frgrm']['vCheck'][i].checked == true){
                document.forms['frgrm']['vChekeados'].value=1;
                i=document.forms['frgrm']['vCheck'].length;
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
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
                document.forms['frgrm']['cOrderByOrder'].value += xCampo + "~";
              }
            break;
            case 'ASC,':
              document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id + ' DESC,';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
                document.forms['frgrm']['cOrderByOrder'].value += xCampo + "~";
              }
            break;
            case 'DESC,':
              document.forms['frgrm'][xCampo].value = '';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
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
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
            break;
            case 'ASC,':
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
            break;
            case 'DESC,':
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
            break;
          }
        }
      }

      function fnGenerarExcel() {
        parent.fmpro.location = "fraptprn.php";
      }

    </script>
    <style type="text/css">
      SELECT{ font-family: verdana; font-size: 10px; color:#2B547D; background-color:#D8E4F1;}
    </style>
  </head>

  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
    <form name = "frgrm" method = "post" action = "fraptini.php" target="fmwork">
      <input type = "hidden" name = "vRecords"       value = "">
      <input type = "hidden" name = "vLimInf"        value = "<?php echo $vLimInf ?>">
      <input type = "hidden" name = "vSortField"     value = "<?php echo $vSortField ?>">
      <input type = "hidden" name = "vSortType"      value = "<?php echo $vSortType ?>">
      <input type = "hidden" name = "cOrderByOrder"  value = "<?php echo $_POST['cOrderByOrder'] ?>" style = "width:1000">
      <!--Campos ocultos de la consulta inducida-->
      <input type = "hidden" name = "cConsultaInducida" value = "<?php echo $_POST['cConsultaInducida'] ?>">
      <input type = "hidden" name = "cUsrId" value = "<?php echo $_POST['cUsrId'] ?>">
      <input type = "hidden" name = "cDo" value = "<?php echo $_POST['cDo'] ?>">

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
                        /* Empiezo a Leer la sys00005 */
                        while($xRUM = mysql_fetch_array($xUsrMen)) {
                          if($y == 0 || $y % 5 == 0) {
                            if ($y == 0) {?>
                            <tr>
                            <?php } else { ?>
                            </tr><tr>
                            <?php }
                          }
                          /* Busco de la sys00005 en la sys00006 */
                          $qUsrPer  = "SELECT * ";
                          $qUsrPer .= "FROM $cAlfa.sys00006 ";
                          $qUsrPer .= "WHERE ";
                          $qUsrPer .= "usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
                          $qUsrPer .= "modidxxx = \"{$xRUM['modidxxx']}\"  AND ";
                          $qUsrPer .= "proidxxx = \"{$xRUM['proidxxx']}\"  AND ";
                          $qUsrPer .= "menidxxx = \"{$xRUM['menidxxx']}\"  LIMIT 0,1";
                          $xUsrPer  = f_MySql("SELECT","",$qUsrPer,$xConexion01,"");
                          if (mysql_num_rows($xUsrPer) > 0) { ?>
                            <td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory ?>/<?php echo $xRUM['menimgon'] ?>" style = "cursor:pointer" onClick ="javascript:fnLink('<?php echo $xRUM['modidxxx'] ?>','<?php echo $xRUM['proidxxx'] ?>','<?php echo $xRUM['menidxxx'] ?>','<?php echo $xRUM['menformx']?>','<?php echo $xRUM['menopcxx']?>','<?php echo $xRUM['mendesxx']?>')"><br>
                            <a href = "javascript:fnLink('<?php echo $xRUM['modidxxx'] ?>','<?php echo $xRUM['proidxxx'] ?>','<?php echo $xRUM['menidxxx'] ?>','<?php echo $xRUM['menformx']?>','<?php echo $xRUM['menopcxx']?>','<?php echo $xRUM['mendesxx']?>')"
                              style="color:<?php echo $vSysStr['system_link_menu_color'] ?>"><?php echo $xRUM['mendesxx'] ?></a></center></td>
                          <?php	} else { ?>
                            <td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory ?>/<?php echo $xRUM['menimgof']?>"><br>
                            <?php echo $xRUM['mendesxx'] ?></center></td>
                          <?php }
                          $y++;
                        }
                        $nCeldas = "";
                        $nf = intval($y/5);
                        $nResto = $y-$nf;
                        $nRestan = 5-$nResto;
                        if ($nRestan > 0) {
                          for ($i=0;$i<$nRestan;$i++) {
                            $nCeldas.="<td width='20%'></td>";
                          }
                          echo $nCeldas;
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
        }elseif ($vLimInf == "") {
          $vLimInf = "00";
        }

        if (substr_count($vLimInf,"-") > 0) {
          $vLimInf = "00";
        }

        if ($vPaginas == "") {
          $vPaginas = "1";
        }

        // Si viene vacio el $cCcoId lo cargo con la Cookie de la sucursal
        // Si no hago el SELECT con la sucursal que me entrega el combo del INI
        if (empty($cCcoId)) {
          $cCcoId  = "";
        } else {
          // Si el $cCcoId viene cargado del combo con "ALL" es porque debo mostrar todas las sucursales
          // Si no dejo la Sucursal que viene cargada
          if ($cCcoId == "ALL") {
            $cCcoId = "";
          }
        }

        /**
         * Si viene vacio el $cUsrId lo cargo con la Cookie del Usuario
         * Si no hago el SELECT con el Usuario que me metrega el combo de INI
         */
        if ($cUsrId == "") {
          $cUsrId = ($_COOKIE['kUsrId'] == "ADMIN" || $cUsrInt == "SI") ? "ALL":$_COOKIE['kUsrId'];
        }

        if ($_POST['cPeriodos'] == "") {
          $_POST['cPeriodos'] == "20";
          $_POST['dDesde'] = substr(date('Y-m-d'),0,8)."01";
          $_POST['dHasta'] = date('Y-m-d');
        }

        // Realizo la consulta de las observaciones del Do
        $y=0;
        $qObservacion  = "SELECT DISTINCT ";
        $qObservacion .= "SQL_CALC_FOUND_ROWS ";
        $qObservacion .= "CONCAT($cAlfa.fdob0000.sucidxxx,\"-\",$cAlfa.fdob0000.docidxxx,\"-\",$cAlfa.fdob0000.docsufxx) AS docidcom, ";
        $qObservacion .= "$cAlfa.fdob0000.fdobidxx, ";
        $qObservacion .= "$cAlfa.fdob0000.sucidxxx, ";
        $qObservacion .= "$cAlfa.fdob0000.docidxxx, ";
        $qObservacion .= "$cAlfa.fdob0000.docsufxx, ";
        $qObservacion .= "$cAlfa.fdob0000.obsobsxx, ";
        $qObservacion .= "$cAlfa.fdob0000.regusrxx, ";
        $qObservacion .= "$cAlfa.fdob0000.regfcrex, ";
        $qObservacion .= "$cAlfa.fdob0000.reghcrex, ";
        $qObservacion .= "$cAlfa.fdob0000.regfmodx, ";
        $qObservacion .= "$cAlfa.fdob0000.reghmodx, ";
        $qObservacion .= "$cAlfa.fdob0000.regestxx, ";
        $qObservacion .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS usrnomxx ";
        $qObservacion .= "FROM $cAlfa.fdob0000 ";
        $qObservacion .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.fdob0000.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
        $qObservacion .= "WHERE ";

        // // DO
        // if($_POST['cDo'] != "" && $_POST['vSearch'] == "") {
        //   $qObservacion .= "(CONCAT($cAlfa.fdob0000.sucidxxx,\"-\",$cAlfa.fdob0000.docidxxx,\"-\",$cAlfa.fdob0000.docsufxx) LIKE \"%{$_POST['cDo']}%\") AND ";
        // }
        // // Sucursales
        // if($cCcoId != "") {
        //   $qObservacion .= "$cAlfa.fdob0000.sucidxxx LIKE \"%$cCcoId%\" AND ";
        // }
        // // Usuario
        // if ($cUsrId != "" && $cUsrId != "ALL") {
        //   $qObservacion .= "$cAlfa.fdob0000.regusrxx = \"$cUsrId\" AND ";
        // }
        // // Campo de búsqueda
        // if($_POST['vSearch'] != "") {
        //   $qObservacion .= "(CONCAT($cAlfa.fdob0000.sucidxxx,\"-\",$cAlfa.fdob0000.docidxxx,\"-\",$cAlfa.fdob0000.docsufxx) LIKE \"%{$_POST['vSearch']}%\" OR ";
        //   $qObservacion .= "$cAlfa.fdob0000.obsobsxx LIKE \"%{$_POST['vSearch']}%\" OR ";
        //   $qObservacion .= "$cAlfa.fdob0000.regestxx LIKE \"%{$_POST['vSearch']}%\" ) AND ";
        // }
        // // Campo de fechas
        // if($_POST['dDesde'] != "" && $_POST['dHasta']) {
        //   $qObservacion .= "($cAlfa.fdob0000.regfcrex BETWEEN \"{$_POST['dDesde']}\" AND \"{$_POST['dHasta']}\") AND ";
        // }
        $qObservacion .= "$cAlfa.fdob0000.regestxx = \"ACTIVO\" ";

        // CODIGO PARA ORDER BY
        $vOrderByOrder = explode("~",$cOrderByOrder);
        for ($z=0;$z<count($vOrderByOrder);$z++) {
          if ($vOrderByOrder[$z] != "") {
            $cOrderBy .= $_POST[$vOrderByOrder[$z]];
          }
        }
        if (strlen($cOrderBy)>0) {
          $cOrderBy = substr($cOrderBy,0,strlen($cOrderBy)-1);
          $cOrderBy = "ORDER BY ".$cOrderBy;
        }else{
          $cOrderBy = "ORDER BY regfmodx,reghmodx DESC ";
        }
        // FIN CODIGO PARA ORDER BY

        $qObservacion .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
        $xObservacion  = f_MySql("SELECT","",$qObservacion,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qFactMan." ~ ".mysql_num_rows($xObservacion));

        $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
        $xRNR = mysql_fetch_array($xNumRows);
        $nRNR = $xRNR['FOUND_ROWS()'];

        // Cargo la Matriz con los ROWS del Cursor
        $i=0;
        while ($zRCab = mysql_fetch_array($xObservacion)) {
          $mMatrizTra[$i] = $zRCab;
          $i++;
        }
        // Fin de Recorro la Matriz para Traer Datos Externos
      ?>

      <center>
        <table width="95%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td>
              <fieldset>
                <legend>Autorizaciones Realizadas(<?php echo $nRNR ?>)</legend>
                <center>
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td class="clase08" width="14%">
                        <input type="text" class="letra" name = "vSearch" maxlength="20" value = "<?php echo $vSearch ?>" style= "width:80"
                          onblur="javascript:this.value=this.value.toUpperCase();
                                            document.forms['frgrm']['vLimInf'].value='00';
                                            document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
                                            document.forms['frgrm']['vPaginas'].value='1'
                                            document.forms['frgrm'].submit()">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:hand" title="Buscar"
                          onClick = "javascript:document.forms['frgrm']['vSearch'].value=document.forms['frgrm']['vSearch'].value.toUpperCase();
                                                document.forms['frgrm']['vLimInf'].value='00';
                                                document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
                                                document.forms['frgrm']['vPaginas'].value='1'
                                                document.forms['frgrm'].submit()">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:hand" title="Mostrar Todo"
                          onClick ="javascript:document.forms['frgrm']['vSearch'].value='';
                                              document.forms['frgrm']['vLimInf'].value='00';
                                              document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
                                              document.forms['frgrm']['vPaginas'].value='1';
                                              document.forms['frgrm']['vSortField'].value='';
                                              document.forms['frgrm']['vSortType'].value='';
                                              document.forms['frgrm']['cOrderByOrder'].value='';
                                              document.forms['frgrm'].submit()">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/cert_ca_cert_on.gif" style = "cursor:pointer" title="Consulta inducida"
                          onClick = "javascript:fnConsultaInducida()">
                      </td>
                      <td class="name" width="03%" align="left">Filas&nbsp;
                        <input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
                          onfocus = "javascript:document.forms['frgrm']['vPaginas'].value='1'"
                          onblur = "javascript:uFixFloat(this);
                                              document.forms['frgrm']['vLimInf'].value='00';
                                              document.forms['frgrm'].submit()">
                      </td>
                      <td class="name" width="05%">
                        <?php if (ceil($nRNR/$vLimSup) > 1) { ?>
                          <?php if ($vPaginas == "1") { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:hand" title="Primera Pagina">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:hand" title="Pagina Anterior">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:hand" title="Pagina Siguiente"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:hand" title="Ultima Pagina"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                          <?php } ?>
                          <?php if ($vPaginas > "1" && $vPaginas < ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value='1';
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value--;
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:hand" title="Pagina Siguiente"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:hand" title="Ultima Pagina"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                          <?php } ?>
                          <?php if ($vPaginas == ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value='1';
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value--;
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_nextpage.png" style = "cursor:hand" title="Pagina Siguiente">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_lastpage.png" style = "cursor:hand" title="Ultima Pagina">
                          <?php } ?>
                        <?php } else { ?>
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:hand" title="Primera Pagina">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:hand" title="Pagina Anterior">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_nextpage.png"  style = "cursor:hand" title="Pagina Siguiente">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_lastpage.png"  style = "cursor:hand" title="Ultima Pagina">
                        <?php } ?>
                      </td>
                      <td class="name" width="09%" align="left">Pag&nbsp;
                        <select Class = "letra" name = "vPaginas" value = "<?php echo $vPaginas ?>" style = "width:60%"
                          onchange="javascript:document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(this.value-1));
                                              document.forms['frgrm'].submit()">
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
                        <select class="letra" size="1" name="cPeriodos" style = "width:100%" value = "<?php echo $_POST['cPeriodos'] ?>"
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
                      <td class="name" width="10%" align="center">
                        <select Class = "letra" name = "cCcoId" value = "<?php echo $cCcoId ?>" style = "width:99%">
                          <option value = "ALL" selected>SUCURSALES</option>
                          <?php
                            $qSucDat  = "SELECT sucidxxx,sucdesxx FROM $cAlfa.fpar0008 WHERE ";
                            $qSucDat .= "regestxx = \"ACTIVO\" ORDER BY sucdesxx";
                            $xSucDat = f_MySql("SELECT","",$qSucDat,$xConexion01,"");
                            if (mysql_num_rows($xSucDat) > 0) {
                              while ($xRSD = mysql_fetch_array($xSucDat)) {
                                if ($xRSD['sucidxxx'] == $cCcoId) { ?>
                                  <option value = "<?php echo $xRSD['sucidxxx']?>" selected><?php echo $xRSD['sucdesxx'] ?></option>
                                <?php } else { ?>
                                  <option value = "<?php echo $xRSD['sucidxxx']?>"><?php echo $xRSD['sucdesxx'] ?></option>
                                <?php }
                              }
                            }
                          ?>
                        </select>
                      </td>
                      <!--fin de codigo nuevo-->
                      <td Class="name" align="right">&nbsp;
                        <?php
                          // Botones de Acceso Rapido
                          $qBotAcc  = "SELECT * ";
                          $qBotAcc .= "FROM $cAlfa.sys00005,$cAlfa.sys00006 ";
                          $qBotAcc .= "WHERE ";
                          $qBotAcc .= "sys00006.usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
                          $qBotAcc .= "sys00006.modidxxx = sys00005.modidxxx        AND ";
                          $qBotAcc .= "sys00006.proidxxx = sys00005.proidxxx        AND ";
                          $qBotAcc .= "sys00006.menidxxx = sys00005.menidxxx        AND ";
                          $qBotAcc .= "sys00006.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
                          $qBotAcc .= "sys00006.proidxxx = \"{$_COOKIE['kProId']}\" ";
                          $qBotAcc .= "ORDER BY sys00005.menordxx";
                          $xBotAcc  = f_MySql("SELECT","",$qBotAcc,$xConexion01,"");
                          // f_Mensaje(__FILE__, __LINE__, $qBotAcc."~".mysql_num_rows($xBotAcc));
                          while ($mBotAcc = mysql_fetch_array($xBotAcc)) {
                            switch ($mBotAcc['menopcxx']) {
                              case "REPORTE": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/excel_icon.gif" onClick = "javascript:fnGenerarExcel()" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
                              <?php break;
                            }
                          }
                          // Fin Botones de Acceso Rapido
                        ?>
                      </td>
                    </tr>
                  </table>
                  <br>
                  <table cellspacing="0" width="100%">
                    <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                      <td class="name" width="10%">
                        <a href = "javascript:fnOrderBy('onclick','docidcom')" title="Ordenar">DO</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docidcom">
                          <input type = "hidden" name = "docidcom" value = "<?php echo $_POST['docidcom'] ?>" id = "docidcom">
                        <script language="javascript">fnOrderBy('','docidcom','')</script>
                      </td>
                      <td class="name" width="20%">
                        <a href = "javascript:fnOrderBy('onclick','obsobsxx')" title="Ordenar">Observacion</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "obsobsxx">
                          <input type = "hidden" name = "obsobsxx" value = "<?php echo $_POST['obsobsxx'] ?>" id = "obsobsxx">
                        <script language="javascript">fnOrderBy('','obsobsxx','')</script>
                      </td>
                      <td class="name" width="21%">
                        <a href = "javascript:fnOrderBy('onclick','usrnomxx')" title="Ordenar">Usuario Creacion</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "usrnomxx">
                          <input type = "hidden" name = "usrnomxx" value = "<?php echo $_POST['usrnomxx'] ?>" id = "usrnomxx">													
                        <script language="javascript">fnOrderBy('','usrnomxx','')</script>
                      </td>
                      <td class="name" width="06%">
                        <a href = "javascript:fnOrderBy('onclick','regfcrex')" title="Ordenar">Creado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfcrex">
                          <input type = "hidden" name = "regfcrex" value = "<?php echo $_POST['regfcrex'] ?>" id = "regfcrex">													
                        <script language="javascript">fnOrderBy('','regfcrex','')</script>
                      </td>
                      <td class="name" width="06%">
                        <a href = "javascript:fnOrderBy('onclick','reghcrex')" title="Ordenar">Hora</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghcrex">
                          <input type = "hidden" name = "reghcrex" value = "<?php echo $_POST['reghcrex'] ?>" id = "reghcrex">													
                        <script language="javascript">fnOrderBy('','reghcrex','')</script>
                      </td>
                      <td class="name" width="06%">
                        <a href = "javascript:fnOrderBy('onclick','regfmodx')" title="Ordenar">Modificado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfmodx">
                          <input type = "hidden" name = "regfmodx" value = "<?php echo $_POST['regfmodx'] ?>" id = "regfmodx">													
                        <script language="javascript">fnOrderBy('','regfmodx','')</script>
                      </td>
                      <td class="name" width="06%">
                        <a href = "javascript:fnOrderBy('onclick','reghmodx')" title="Ordenar">Hora</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghmodx">
                          <input type = "hidden" name = "reghmodx" value = "<?php echo $_POST['reghmodx'] ?>" id = "reghmodx">													
                        <script language="javascript">fnOrderBy('','reghmodx','')</script>
                      </td>
                      <td class="name" width="02%" align="right">
                        <input type="checkbox" name="vCheckAll" onClick = 'javascript:fnMarca()'>
                      </td>
                    </tr>
                    <script languaje="javascript">
                      document.forms['frgrm']['vRecords'].value = "<?php echo count($mMatrizTra) ?>";
                    </script>
                    <?php 
                      for($i=0;$i<count($mMatrizTra);$i++) {
                        if ($y < count($mMatrizTra)) { // Para Controlar el Error
                          $zColor = "{$vSysStr['system_row_impar_color_ini']}";
                          if($y % 2 == 0) {
                            $zColor = "{$vSysStr['system_row_par_color_ini']}";
                          } ?>
                          <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
                            <td class="letra7"><?php echo $mMatrizTra[$i]['docidcom'] ?></td>
                            <td class="letra7"><?php echo $mMatrizTra[$i]['obsobsxx'] ?></td>
                            <td class="letra7"><?php echo $mMatrizTra[$i]['usrnomxx'] ?></td>
                            <td class="letra7"><?php echo $mMatrizTra[$i]['regfcrex'] ?></td>
                            <td class="letra7"><?php echo $mMatrizTra[$i]['reghcrex'] ?></td>
                            <td class="letra7"><?php echo $mMatrizTra[$i]['regfmodx'] ?></td>
                            <td class="letra7"><?php echo $mMatrizTra[$i]['reghmodx'] ?></td>
                            <td class="letra7" align="right">
                              <input type="checkbox" name="vCheck" value = "<?php echo count($mMatrizTra) ?>"
                                id = "<?php echo $mMatrizTra[$i]['sucidxxx'].'~'.
                                                $mMatrizTra[$i]['docidxxx'].'~'.
                                                $mMatrizTra[$i]['docsufxx'].'~'.
                                                $mMatrizTra[$i]['fdobidxx'] ?>"
                                onclick="javascript:document.forms['frgrm']['vRecords'].value='<?php echo count($mMatrizTra) ?>'">
                            </td>
                          </tr>
                          <?php $y++;
                        }
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
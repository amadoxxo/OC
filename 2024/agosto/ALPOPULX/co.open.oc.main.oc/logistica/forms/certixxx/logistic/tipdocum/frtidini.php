<?php
/**
 * Tracking Tipos Documentales
 * --- Este programa permite realizar consultas rapidas del tracking Tipos Documentales que se Encuentra en la Base de Datos.
 * @author elian.amado@openits.co
 * @package openComex
 * @version 001
 */

// ini_set('error_reporting', E_ERROR);
// ini_set("display_errors","1");

include("../../../../../financiero/libs/php/utility.php");

/* Busco en la 05 que Tiene Permiso el Usuario*/
$qUsrMen  = "SELECT * ";
$qUsrMen .= "FROM $cAlfa.sys00039 ";
$qUsrMen .= "WHERE ";
$qUsrMen .= "sys00039.modidxxx  = \"{$_COOKIE['kModId']}\" AND ";
$qUsrMen .= "sys00039.proidxxx  = \"{$_COOKIE['kProId']}\" AND ";
$qUsrMen .= "sys00039.menimgon != \"\" ";
$qUsrMen .= "ORDER BY sys00039.menordxx";
$xUsrMen = f_MySql("SELECT","",$qUsrMen,$xConexion01,"");
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

      /************************ FUNCION PARA GUARDAR EL ORDEN DEL ORDER BY DEL SQL ***********************/
      function fnOrder_By(xEvento, xCampo) {
        if (document.forms['frnav'][xCampo].value != '') {
          var vSwitch = document.forms['frnav'][xCampo].value.split(' ');
          var cSwitch = vSwitch[1];
        } else {
          var cSwitch = '';
        }
        if (xEvento == 'onclick') {
          switch (cSwitch) {
            case '':
              document.forms['frnav'][xCampo].value = document.forms['frnav'][xCampo].id + ' ASC,';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/s_asc.png';
              if (document.forms['frnav']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
                document.forms['frnav']['cOrderByOrder'].value += xCampo + "~";
              }
            break;
            case 'ASC,':
              document.forms['frnav'][xCampo].value = document.forms['frnav'][xCampo].id + ' DESC,';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/s_desc.png';
              if (document.forms['frnav']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
                document.forms['frnav']['cOrderByOrder'].value += xCampo + "~";
              }
            break;
            case 'DESC,':
              document.forms['frnav'][xCampo].value = '';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png';
              if (document.forms['frnav']['cOrderByOrder'].value.lastIndexOf(xCampo) >= 0) {
                document.forms['frnav']['cOrderByOrder'].value = document.forms['frnav']['cOrderByOrder'].value.replace(xCampo, "");
              }
            break;
          }
          document.forms['frnav']['vSearch'].value=document.forms['frnav']['vSearch'].value.toUpperCase();
          document.forms['frnav']['vLimInf'].value='00';
          document.forms['frnav']['vLimSup'].value='30';
          document.forms['frnav']['vPaginas'].value='1';
          document.forms['frnav'].submit();
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
    <form name = "frnav" action="frtidini.php" method="post" target="fmwork">
      <input type = "hidden" name = "nRecords"   value = "">
      <input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
      <input type = "hidden" name = "cSortField" value = "<?php echo $cSortField ?>">
      <input type = "hidden" name = "cSortType"  value = "<?php echo $cSortType ?>">
      <input type = "hidden" name = "vBuscar"    value = "<?php echo $_POST['vBuscar'] ?>">
      <input type = "hidden" name = "cOrderByOrder"  value = "<?php echo $_POST['cOrderByOrder'] ?>" style = "width:1000">

      <?php

        if (empty($vLimInf) && empty($vLimSup)) {
          $vLimInf = "00";
          $vLimSup = "30";
        }

        if (empty($vPaginas)) {
          $vPaginas = "1";
        }

        if ($_POST['vSearch'] != "") {
          /**
           * Buscando los id que corresponden a las busquedas de los leftjoin
           */
          $qUsuario  = "SELECT ";
          $qUsuario .= "USRIDXXX, USRNOMXX ";
          $qUsuario .= "FROM $cAlfa.SIAI0003 ";
          $qUsuario .= "WHERE ";
          $qUsuario .= "USRNOMXX LIKE \"%{$_POST['vSearch']}%\" ";
          $xUsuario = f_MySql("SELECT","",$qUsuario,$xConexion01,"");
          $cNombreSearch = "";
          while ($xRCN = mysql_fetch_array($xUsuario)) {
            $cNombreSearch .= "\"{$xRCN['USRIDXXX']}\",";
          }
          $cNombreSearch = substr($cNombreSearch,0,strlen($cNombreSearch)-1);
        }

        $y=0;

        $mFormCo = array();
        $qFormCo  = "SELECT DISTINCT SQL_CALC_FOUND_ROWS ";
        $qFormCo .= "$cAlfa.lpar0130.fcoidxxx, ";
        $qFormCo .= "$cAlfa.lpar0130.fcodesxx, ";
        $qFormCo .= "$cAlfa.lpar0130.regusrxx, "; // Código Usuario
        $qFormCo .= "$cAlfa.lpar0130.regfcrex, "; // Fecha de creación
        $qFormCo .= "$cAlfa.lpar0130.reghcrex, "; // Hora de creación
        $qFormCo .= "$cAlfa.lpar0130.regfmodx, "; // Fecha de modificación
        $qFormCo .= "$cAlfa.lpar0130.reghmodx, "; // Hora de modificación
        if (substr_count($_POST['cOrderByOrder'],"usrnomxx") > 0) {
          $qFormCo .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,TRIM(CONCAT($cAlfa.SIAI0003.USRPAPEX,\" \",$cAlfa.SIAI0003.USRSAPEX,\" \",$cAlfa.SIAI0003.USRPNOMX,\" \",$cAlfa.SIAI0003.USRSNOMX))) AS usrnomxx, ";
        }
        $qFormCo .= "$cAlfa.lpar0130.regestxx ";
        $qFormCo .= "FROM $cAlfa.lpar0130 ";
        if (substr_count($_POST['cOrderByOrder'],"usrnomxx") > 0) {
          $qFormCo .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.lpar0130.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
        }
        if ($_POST['vSearch'] != "") {
          $qFormCo .= "WHERE ";
          $qFormCo .= "$cAlfa.lpar0130.fcoidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qFormCo .= "$cAlfa.lpar0130.fcodesxx LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qFormCo .= "$cAlfa.lpar0130.regfcrex LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qFormCo .= "$cAlfa.lpar0130.reghcrex LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qFormCo .= "$cAlfa.lpar0130.regfmodx LIKE \"%{$_POST['vSearch']}%\" OR ";
          if ($cNombreSearch != "") {
            $qFormCo .= "$cAlfa.lpar0130.regusrxx IN ($cNombreSearch) OR ";
          }
          $qFormCo .= "$cAlfa.lpar0130.regestxx LIKE \"%{$_POST['vSearch']}%\" ";
        }
        //// CODIGO NUEVO PARA ORDER BY
        $cOrderBy = "";
        $vOrderByOrder = explode("~",$_POST['cOrderByOrder']);
        for ($z=0;$z<count($vOrderByOrder);$z++) {
          if ($vOrderByOrder[$z] != "") {
            if ($_POST[$vOrderByOrder[$z]] != "") {
              $cOrderBy .= $_POST[$vOrderByOrder[$z]];
            }
          }
        }
        if (strlen($cOrderBy)>0) {
          $cOrderBy = substr($cOrderBy,0,strlen($cOrderBy)-1);
          $cOrderBy = "ORDER BY ".$cOrderBy;
        } else {
          $cOrderBy = "ORDER BY regfmodx DESC ";
        }
        //// FIN CODIGO NUEVO PARA ORDER BY
        $qFormCo .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
        $xFormCo  = f_MySql("SELECT","",$qFormCo,$xConexion01,"");
        //echo $qFormCo." ~ ".mysql_num_rows($xFormCo);

        /***** FIN SQL *****/

        $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
        $xRNR = mysql_fetch_array($xNumRows);
        $nRNR += $xRNR['FOUND_ROWS()'];

        while ($xRDC = mysql_fetch_array($xFormCo)) {
          //Buscando nombre del cliente
          $qUsuario  = "SELECT ";
          $qUsuario .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,TRIM(CONCAT($cAlfa.SIAI0003.USRPAPEX,\" \",$cAlfa.SIAI0003.USRSAPEX,\" \",$cAlfa.SIAI0003.USRPNOMX,\" \",$cAlfa.SIAI0003.USRSNOMX))) AS USRNOMXX ";
          $qUsuario .= "FROM $cAlfa.SIAI0003 ";
          $qUsuario .= "WHERE ";
          $qUsuario .= "USRIDXXX = \"{$xRDC['regusrxx']}\" LIMIT 0,1 ";
          $xUsuario = f_MySql("SELECT","",$qUsuario,$xConexion01,"");
          if (mysql_num_rows($xUsuario) > 0) {
            $vUsuario = mysql_fetch_array($xUsuario);
            $xRDC['usrnomxx'] = $vUsuario['USRNOMXX'];
          } else {
            $xRDC['usrnomxx'] = "SIN NOMBRE";
          }
          $mFormCo[count($mFormCo)] = $xRDC;
        }
      ?>
      <center>
        <table width="95%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td>
              <fieldset>
                <legend>Tipos Documentales (<?php echo $nRNR?>)</legend>
                <center>
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td class="clase08" width="14%">
                        <input type="text" class="letra" name = "vSearch" maxlength="50" value = "<?php echo $vSearch ?>" style= "width:80"
                          onblur="javascript:this.value=this.value.toUpperCase();
                                              document.forms['frnav']['vLimInf'].value='00';
                                              document.forms['frnav']['vLimSup'].value='30';
                                              document.forms['frnav']['vPaginas'].value='1';
                                              document.forms['frnav'].submit()">
                        <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_search.png" style = "cursor:pointer" title="Buscar"
                          onClick = "javascript:document.forms['frnav']['vBuscar'].value = 'ON'
                                                document.forms['frnav']['vSearch'].value=document.forms['frnav']['vSearch'].value.toUpperCase();
                                                document.forms['frnav']['vLimInf'].value='00';
                                                document.forms['frnav']['vLimSup'].value='30';
                                                document.forms['frnav']['vPaginas'].value='1'
                                                document.forms['frnav'].submit()">
                        <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_show-all_bg.gif" style = "cursor:pointer" title="Mostrar Todo"
                          onClick ="javascript:document.forms['frnav']['vSearch'].value='';
                                                document.forms['frnav']['vLimInf'].value='00';
                                                document.forms['frnav']['vLimSup'].value='30';
                                                document.forms['frnav']['vPaginas'].value='1';
                                                document.forms['frnav']['cSortField'].value='';
                                                document.forms['frnav']['cSortType'].value='';
                                                document.forms['frnav']['vBuscar'].value='';
                                                document.forms['frnav'].submit()">
                      </td>
                      <td class="name" width="06%" align="left">Filas&nbsp;
                        <input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
                          onfocus = "javascript:document.forms['frnav']['vPaginas'].value='1'"
                          onblur = "javascript:f_FixFloat(this);
                                                document.forms['frnav']['vLimInf'].value='00';
                                                document.forms['frnav'].submit()">
                      </td>
                      <td class="name" width="08%">
                        <?php if (ceil($nRNR/$vLimSup) > 1) { ?>
                          <?php if ($vPaginas == "1") { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_firstpage.png" style = "cursor:hand" title="Primera Pagina">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_prevpage.png"  style = "cursor:hand" title="Pagina Anterior">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_nextpage.png"  	style = "cursor:hand" title="Pagina Siguiente"
                              onClick = "javascript:document.forms['frnav']['vPaginas'].value++;
                                                    document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
                                                    document.forms['frnav'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_lastpage.png"  	style = "cursor:hand" title="Ultima Pagina"
                              onClick = "javascript:document.forms['frnav']['vPaginas'].value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
                                                    document.forms['frnav'].submit()">
                          <?php } ?>
                          <?php if ($vPaginas > "1" && $vPaginas < ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
                              onClick = "javascript:document.forms['frnav']['vPaginas'].value='1';
                                                    document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
                                                    document.forms['frnav'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
                              onClick = "javascript:document.forms['frnav']['vPaginas'].value--;
                                                    document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
                                                    document.forms['frnav'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_nextpage.png"  style = "cursor:hand" title="Pagina Siguiente"
                              onClick = "javascript:document.forms['frnav']['vPaginas'].value++;
                                                      document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
                                                    document.forms['frnav'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_lastpage.png"  style = "cursor:hand" title="Ultima Pagina"
                              onClick = "javascript:document.forms['frnav']['vPaginas'].value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
                                                    document.forms['frnav'].submit()">
                          <?php } ?>
                          <?php if ($vPaginas == ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
                              onClick = "javascript:document.forms['frnav']['vPaginas'].value='1';
                                                    document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
                                                    document.forms['frnav'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
                              onClick = "javascript:document.frnav.vPaginas.value--;
                                                      document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.frnav.vPaginas.value-1));
                                                    document.frnav.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_nextpage.png" style = "cursor:hand" title="Pagina Siguiente">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_lastpage.png" style = "cursor:hand" title="Ultima Pagina">
                          <?php } ?>
                        <?php } else { ?>
                          <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_firstpage.png" style = "cursor:hand" title="Primera Pagina">
                          <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_prevpage.png"  style = "cursor:hand" title="Pagina Anterior">
                          <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_nextpage.png"  style = "cursor:hand" title="Pagina Siguiente">
                          <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_lastpage.png"  style = "cursor:hand" title="Ultima Pagina">
                        <?php } ?>
                      </td>
                      <td class="name" width="08%" align="left">Pag&nbsp;
                        <select Class = "letrase" name = "vPaginas" value = "<?php echo $vPaginas ?>" style = "width:60%"
                          onchange="javascript:this.id = 'ON';
                                                document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(this.value-1));
                                                document.forms['frnav'].submit()">
                          <?php for ($i=0;$i<ceil($nRNR/$vLimSup);$i++) {
                            if ($i+1 == $vPaginas) { ?>
                              <option value = "<?php echo $i+1 ?>" selected><?php echo $i+1 ?></option>
                            <?php } else { ?>
                              <option value = "<?php echo $i+1 ?>"><?php echo $i+1 ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </td>
                    </tr>
                  </table>
                </center>
                <hr></hr>
                <center>
                  <table cellspacing="0" width="100%">
                    <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                      <td class="name" width="20%">
                        <a href = "javascript:fnOrder_By('onclick','fcoidxxx');" title="Ordenar">Cliente ECM</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="15" title = "" id = "fcoidxxx">
                        <input type = "hidden" name = "fcoidxxx" value = "<?php echo $_POST['fcoidxxx'] ?>" id = "fcoidxxx">
                        <script language="javascript">fnOrder_By('','fcoidxxx')</script>
                      </td>
                      <td class="name" width="20%">
                        <a href = "javascript:fnOrder_By('onclick','fcodesxx');" title="Ordenar">Servicio</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="15" title = "" id = "fcodesxx">
                        <input type = "hidden" name = "fcodesxx" value = "<?php echo $_POST['fcodesxx'] ?>" id = "fcodesxx">
                        <script language="javascript">fnOrder_By('','fcodesxx')</script>
                      </td>
                      <td class="name" width="15%">
                        <a href = "javascript:fnOrder_By('onclick','usrnomxx');" title="Ordenar">Sitio</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="15" title = "" id = "usrnomxx">
                        <input type = "hidden" name = "usrnomxx" value = "<?php echo $_POST['usrnomxx'] ?>" id = "usrnomxx">
                        <script language="javascript">fnOrder_By('','usrnomxx')</script>
                      </td>
                      <td class="name" width="15%">
                        <a href = "javascript:fnOrder_By('onclick','regfcrex');" title="Ordenar">Grupo</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="15" title = "" id = "regfcrex">
                        <input type = "hidden" name = "regfcrex" value = "<?php echo $_POST['regfcrex'] ?>" id = "regfcrex">
                        <script language="javascript">fnOrder_By('','regfcrex')</script>
                      </td>
                      <td class="name" width="15%">
                        <a href = "javascript:fnOrder_By('onclick','reghcrex');" title="Ordenar">Tipo Documental</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="15" title = "" id = "reghcrex">
                        <input type = "hidden" name = "reghcrex" value = "<?php echo $_POST['reghcrex'] ?>" id = "reghcrex">
                        <script language="javascript">fnOrder_By('','reghcrex')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:fnOrder_By('onclick','regfmodx');" title="Ordenar">Creado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="15" title = "" id = "regfmodx">
                        <input type = "hidden" name = "regfmodx" value = "<?php echo $_POST['regfmodx'] ?>" id = "regfmodx">
                        <script language="javascript">fnOrder_By('','regfmodx')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:fnOrder_By('onclick','reghmodx');" title="Ordenar">Modificado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="15" title = "" id = "reghmodx">
                        <input type = "hidden" name = "reghmodx" value = "<?php echo $_POST['reghmodx'] ?>" id = "reghmodx">
                        <script language="javascript">fnOrder_By('','reghmodx')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:fnOrder_By('onclick','regestxx');" title="Ordenar">Estado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="15" title = "" id = "regestxx">
                        <input type = "hidden" name = "regestxx" value = "<?php echo $_POST['regestxx'] ?>" id = "regestxx">
                        <script language="javascript">fnOrder_By('','regestxx')</script>
                      </td>
                    </tr>
                    <script languaje="javascript">
                      document.forms['frnav']['nRecords'].value = "<?php echo count($mFormCo) ?>";
                    </script>
                      <?php for ($i=0;$i<count($mFormCo);$i++) {
                        if ($i < count($mFormCo)) { // Para Controlar el Error
                        $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                        if($y % 2 == 0) {
                          $cColor = "{$vSysStr['system_row_par_color_ini']}";
                        } ?>
                        <!--<tr bgcolor = "<?php echo $cColor ?>">-->
                        <tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
                          <td class="letra7" height="20" width="20%"><?php echo $mFormCo[$i]['fcoidxxx']?></td>
                          <td class="letra7" height="20" width="20%"><?php echo $mFormCo[$i]['fcodesxx'] ?></td>
                          <td class="letra7" height="20" width="15%"><?php echo substr($mFormCo[$i]['usrnomxx'],0,20) ?></td>
                          <td class="letra7" height="20" width="15%"><?php echo $mFormCo[$i]['regfcrex'] ?></td>
                          <td class="letra7" height="20" width="15%"><?php echo $mFormCo[$i]['reghcrex'] ?></td>
                          <td class="letra7" height="20" width="05%"><?php echo $mFormCo[$i]['regfmodx'] ?></td>
                          <td class="letra7" height="20" width="05%"><?php echo $mFormCo[$i]['reghmodx'] ?></td>
                          <td class="letra7" height="20" width="05%"><?php echo $mFormCo[$i]['regestxx'] ?></td>
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

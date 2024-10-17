<?php
  namespace openComex;
  /**
   * Tracking Deposito.
   * --- Descripcion: Permite realizar consultas sobre las Deposito que se encuentran en la Base de Datos.
   * @author juan.trujillo@openits.co
   * @package openComex
   * @version 001
   */

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

      function fnVer(xDepNum) {
        var cPathUrl = "frdepnue.php?cDepNum="+xDepNum;
        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes=Ver Deposito;path="+"/";
        document.cookie="kModo=VER;path="+"/";
        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
        document.location = cPathUrl; // Invoco el menu.
      }

      function fnEditar(xModo) {
        switch (document.forms['frnav']['nRecords'].value) {
        case "1":
          if (document.forms['frnav']['oCheck'].checked == true) {
            var mMatriz = document.forms['frnav']['oCheck'].id.split('~');
            var cPathUrl = "frdepnue.php?cDepNum="+mMatriz[0];
            document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
            document.cookie="kMenDes=Editar Deposito;path="+"/";
            document.cookie="kModo="+xModo+";path="+"/";
            parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
            document.location = cPathUrl; // Invoco el menu.
          }
        break;
        default:
          var zSw_Prv = 0;
          for (i=0;i<document.forms['frnav']['oCheck'].length;i++) {
            if (document.forms['frnav']['oCheck'][i].checked == true && zSw_Prv == 0) {
              // Solo Deja Legalizar el Primero Seleccionado
              zSw_Prv = 1;
              var mMatriz = document.forms['frnav']['oCheck'][i].id.split('~');
              var cPathUrl = "frdepnue.php?cDepNum="+mMatriz[0];
              document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
              document.cookie="kMenDes=Editar Deposito;path="+"/";
              document.cookie="kModo="+xModo+";path="+"/";
              parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
              document.location = cPathUrl; // Invoco el menu.
            }
          }
        break;
        }
      }

      function fnCambiaEstado(xModo) {
        if (document.forms['frnav']['nRecords'].value!="0"){
          switch (document.forms['frnav']['nRecords'].value) {
            case "1":
              if (document.forms['frnav']['oCheck'].checked == true) {
                var mMatriz = document.forms['frnav']['oCheck'].id.split('~');
                if (confirm("Esta Seguro de Cambiar el Estado para el Deposito ["+mMatriz[0]+"] ?")) {
                  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                  document.forms['frestado']['cDepNum'].value=mMatriz[0];
                  document.forms['frestado']['cEstado'].value=mMatriz[1];
                  document.cookie="kModo="+xModo+";path="+"/";
                  document.forms['frestado'].submit();
                }
              }
            break;
            default:
            var zSw_Prv = 0;
              for (i=0;i<document.forms['frnav']['oCheck'].length;i++) {
                if (document.forms['frnav']['oCheck'][i].checked == true && zSw_Prv == 0) {
                  var mMatriz = document.forms['frnav']['oCheck'][i].id.split('~');
                  if (confirm("Esta Seguro de Cambiar el Estado para el Deposito ["+mMatriz[0]+"] ?")) {
                    zSw_Prv = 1;
                    var mMatriz = document.forms['frnav']['oCheck'][i].id.split('~');
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.forms['frestado']['cDepNum'].value=mMatriz[0];
                    document.forms['frestado']['cEstado'].value=mMatriz[1];
                    document.cookie="kModo="+xModo+";path="+"/";
                    document.forms['frestado'].submit();
                  }
                }
              }
            break;
          }
        }
      }

      function fnLink(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes="+xMenDes+";path="+"/";
        document.cookie="kModo="+xOpcion+";path="+"/";
        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
        document.location = xForm; // Invoco el menu.
      }

      function fnOrderBy(xEvento, xCampo) {
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
    <form name = "frestado" action = "frdepgra.php" method = "post" target="fmpro">
      <input type = "hidden" name = "cDepNum" value = "">
      <input type = "hidden" name = "cEstado" value = "">
    </form>


    <form name = "frnav" action="frdepini.php" method="post" target="fmwork">
      <input type = "hidden" name = "nRecords"   value = "">
      <input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
      <input type = "hidden" name = "cSortField" value = "<?php echo $cSortField ?>">
      <input type = "hidden" name = "cSortType"  value = "<?php echo $cSortType ?>">
      <input type = "hidden" name = "vBuscar"    value = "<?php echo $_POST['vBuscar'] ?>">
      <input type = "hidden" name = "cOrderByOrder"  value = "<?php echo $_POST['cOrderByOrder'] ?>" style = "width:1000">

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
                      /* Empiezo a Leer la sys00039 */
                      while($mUsrMen = mysql_fetch_array($xUsrMen)) {
                        if($y == 0 || $y % 5 == 0) {
                          if ($y == 0) { ?>
                            <tr>
                          <?php } else { ?>
                            </tr><tr>
                          <?php }
                        }

                        /* Busco de la sys00039 en la sys00040 */
                        $qUsrPer  = "SELECT * ";
                        $qUsrPer .= "FROM $cAlfa.sys00040 ";
                        $qUsrPer .= "WHERE ";
                        $qUsrPer .= "usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
                        $qUsrPer .= "modidxxx = \"{$mUsrMen['modidxxx']}\"  AND ";
                        $qUsrPer .= "proidxxx = \"{$mUsrMen['proidxxx']}\" AND ";
                        $qUsrPer .= "menidxxx = \"{$mUsrMen['menidxxx']}\"  LIMIT 0,1";
                        $xUsrPer = f_MySql("SELECT","",$qUsrPer,$xConexion01,"");
                        // f_mensaje(__FILE__,__LINE__,$qUsrPer."~".mysql_num_rows($xUsrPer));
                        if (mysql_num_rows($xUsrPer) > 0) { ?>
                          <td Class="clase08" width="20%">
                            <center>
                              <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/<?php echo $mUsrMen['menimgon'] ?>" style = "cursor:pointer" onClick ="javascript:fnLink('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')"><br>
                              <a href = "javascript:fnLink('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')"
                              style="color:<?php echo $vSysStr['system_link_menu_color'] ?>"><?php echo $mUsrMen['mendesxx'] ?></a>
                            </center>
                          </td>
                        <?php } else { ?>
                          <td Class="clase08" width="20%">
                            <center>
                              <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/<?php echo $mUsrMen['menimgof']?>"><br>
                              <?php echo $mUsrMen['mendesxx'] ?>
                            </center>
                          </td>
                        <?php }
                        $y++;
                      }

                      $celdas = "";
                      $nFila  = intval($y/5);
                      $resto  = $y-$nFila;
                      $diferencia = 5-$resto;
                      if ($diferencia > 0) {
                        for ($i=0;$i<$diferencia;$i++) {
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

      if (empty($vLimInf) && empty($vLimSup)) {
        $vLimInf = "00";
        $vLimSup = "30";
      }

      if (empty($vPaginas)) {
        $vPaginas = "1";
      }

      $y = 0;
      $mDepositos = array();
      // Consulta principal de los depositos
      $qDepositos  = "SELECT DISTINCT SQL_CALC_FOUND_ROWS ";
      $qDepositos .= "$cAlfa.lpar0155.*, ";
      $qDepositos .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) AS clinomxx, ";
      $qDepositos .= "$cAlfa.lpar0150.clisapxx, ";
      $qDepositos .= "$cAlfa.lpar0007.tdedesxx, ";
      if (substr_count($_POST['cOrderByOrder'],"usrnomxx") > 0) {
        $qDepositos .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,TRIM(CONCAT($cAlfa.SIAI0003.USRPAPEX,\" \",$cAlfa.SIAI0003.USRSAPEX,\" \",$cAlfa.SIAI0003.USRPNOMX,\" \",$cAlfa.SIAI0003.USRSNOMX))) AS usrnomxx, ";
      }
      $qDepositos .= "$cAlfa.lpar0005.pfadesxx ";
      $qDepositos .= "FROM $cAlfa.lpar0155 ";
      $qDepositos .= "LEFT JOIN $cAlfa.lpar0150 ON lpar0155.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
      $qDepositos .= "LEFT JOIN $cAlfa.lpar0007 ON lpar0155.tdeidxxx = $cAlfa.lpar0007.tdeidxxx ";
      $qDepositos .= "LEFT JOIN $cAlfa.lpar0005 ON lpar0155.pfaidxxx = $cAlfa.lpar0005.pfaidxxx ";
      if (substr_count($_POST['cOrderByOrder'],"usrnomxx") > 0) {
        $qDepositos .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.lpar0155.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
      }
      if ($_POST['vSearch'] != "") {
        $qDepositos .= "WHERE ";
        $qDepositos .= "$cAlfa.lpar0155.depnumxx LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qDepositos .= "$cAlfa.lpar0007.tdedesxx LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qDepositos .= "$cAlfa.lpar0155.cliidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qDepositos .= "$cAlfa.lpar0150.clisapxx LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qDepositos .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qDepositos .= "$cAlfa.lpar0005.pfadesxx LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qDepositos .= "$cAlfa.lpar0155.regfcrex LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qDepositos .= "$cAlfa.lpar0155.reghcrex LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qDepositos .= "$cAlfa.lpar0155.regfmodx LIKE \"%{$_POST['vSearch']}%\" OR ";
        $qDepositos .= "$cAlfa.lpar0155.regestxx LIKE \"%{$_POST['vSearch']}%\" ";
      }

      //// CODIGO NUEVO PARA ORDER BY
      $cOrderBy = "";
      $vOrderByOrder = explode("~",$_POST['cOrderByOrder']);
      for ($z=0;$z<count($vOrderByOrder);$z++) {
        if ($vOrderByOrder[$z] != "") {
          if ($_POST[$vOrderByOrder[$z]] != "") {
            if ($vOrderByOrder[$z] == "clinomxx") {
              $cOrderBy .= "lpar0150.".$_POST[$vOrderByOrder[$z]];
            } else if ($vOrderByOrder[$z] == "usrnomxx") {
              $cOrderBy .= "SIAI0003.".$_POST[$vOrderByOrder[$z]];
            } else if ($vOrderByOrder[$z] == "tdedesxx") {
              $cOrderBy .= "lpar0007.".$_POST[$vOrderByOrder[$z]];
            } else {
              $cOrderBy .= $_POST[$vOrderByOrder[$z]];
            }
          }
        }
      }

      if (strlen($cOrderBy) > 0) {
        $cOrderBy = substr($cOrderBy,0,strlen($cOrderBy)-1);
        $cOrderBy = "ORDER BY ".$cOrderBy;
      } else {
        $cOrderBy = "ORDER BY regfmodx DESC ";
      }

      //// FIN CODIGO NUEVO PARA ORDER BY
      $qDepositos .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
      $cIdCountRow = mt_rand(1000000000, 9999999999);
      $xDepositos = mysql_query($qDepositos, $xConexion01, true, $cIdCountRow);
      // echo $qDepositos." ~ ".mysql_num_rows($xDepositos);

      $xNumRows = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD",$xConexion01);
      $xRNR     = mysql_fetch_array($xNumRows);
      $nRNR     = $xRNR['CANTIDAD'];

      while ($xRCS = mysql_fetch_array($xDepositos)) {
        $mDepositos[count($mDepositos)] = $xRCS;
      }

      ?>
      <center>
        <table width="95%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td>
              <fieldset>
                <legend>Dep&oacute;sitos (<?php echo $nRNR?>)</legend>
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

                      <td Class="name" width="15%" align="right">&nbsp;
                        <?php
                        /***** Botones de Acceso Rapido *****/
                        $qBotonAcc  = "SELECT sys00039.menopcxx ";
                        $qBotonAcc .= "FROM $cAlfa.sys00039,$cAlfa.sys00040 ";
                        $qBotonAcc .= "WHERE ";
                        $qBotonAcc .= "sys00040.usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
                        $qBotonAcc .= "sys00040.modidxxx = sys00039.modidxxx        AND ";
                        $qBotonAcc .= "sys00040.proidxxx = sys00039.proidxxx        AND ";
                        $qBotonAcc .= "sys00040.menidxxx = sys00039.menidxxx        AND ";
                        $qBotonAcc .= "sys00040.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
                        $qBotonAcc .= "sys00040.proidxxx = \"{$_COOKIE['kProId']}\" ";
                        $qBotonAcc .= "ORDER BY sys00039.menordxx";
                        $xBotonAcc  = f_MySql("SELECT","",$qBotonAcc,$xConexion01,"");

                        while ($xRBA = mysql_fetch_array($xBotonAcc)) {
                          switch ($xRBA['menopcxx']) {
                          case "EDITAR": ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_edit.png" onClick = "javascript:fnEditar('<?php echo $xRBA['menopcxx'] ?>')" style = "cursor:pointer" title="Editar, Solo Uno">
                          <?php break;
                          case "CAMBIAESTADO": ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_cambest.gif" onClick = "javascript:fnCambiaEstado('<?php echo $xRBA['menopcxx'] ?>')" style = "cursor:pointer" title="Cambiar Estado, Solo Uno">
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
                      <td class="name" width="07%">
                        <a href = "javascript:fnOrderBy('onclick','depnumxx');" title="Ordenar">No. Dep&oacute;sito</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "depnumxx">
                        <input type = "hidden" name = "depnumxx" value = "<?php echo $_POST['depnumxx'] ?>" id = "depnumxx">
                        <script language="javascript">fnOrderBy('','depnumxx')</script>
                      </td>
                      <td class="name" width="10%">
                        <a href = "javascript:fnOrderBy('onclick','tdedesxx');" title="Ordenar">Tipo Dep&oacute;sito</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "tdedesxx">
                        <input type = "hidden" name = "tdedesxx" value = "<?php echo $_POST['tdedesxx'] ?>" id = "tdedesxx">
                        <script language="javascript">fnOrderBy('','tdedesxx')</script>
                      </td>
                      <td class="name" width="10%">
                        <a href = "javascript:fnOrderBy('onclick','cliidxxx');" title="Ordenar">Nit</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "cliidxxx">
                        <input type = "hidden" name = "cliidxxx" value = "<?php echo $_POST['cliidxxx'] ?>" id = "cliidxxx">
                        <script language="javascript">fnOrderBy('','cliidxxx')</script>
                      </td>
                      <td class="name" width="10%">
                        <a href = "javascript:fnOrderBy('onclick','clisapxx');" title="Ordenar">Cod. SAP</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "clisapxx">
                        <input type = "hidden" name = "clisapxx" value = "<?php echo $_POST['clisapxx'] ?>" id = "clisapxx">
                        <script language="javascript">fnOrderBy('','clisapxx')</script>
                      </td>
                      <td class="name" width="12%">
                        <a href = "javascript:fnOrderBy('onclick','clinomxx');" title="Ordenar">Raz&oacute;n Social</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "clinomxx">
                        <input type = "hidden" name = "clinomxx" value = "<?php echo $_POST['clinomxx'] ?>" id = "clinomxx">
                        <script language="javascript">fnOrderBy('','clinomxx')</script>
                      </td>
                      <td class="name" width="12%">
                        <a href = "javascript:fnOrderBy('onclick','pfadesxx');" title="Ordenar">Periodicidad</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "pfadesxx">
                        <input type = "hidden" name = "pfadesxx" value = "<?php echo $_POST['pfadesxx'] ?>" id = "pfadesxx">
                        <script language="javascript">fnOrderBy('','pfadesxx')</script>
                      </td>
                      <td class="name" width="10%">
                        <a href = "javascript:fnOrderBy('onclick','regusrxx');" title="Ordenar">Usuario</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regusrxx">
                        <input type = "hidden" name = "regusrxx" value = "<?php echo $_POST['regusrxx'] ?>" id = "regusrxx">
                        <script language="javascript">fnOrderBy('','regusrxx')</script>
                      </td>
                      <td class="name" width="06%">
                        <a href = "javascript:fnOrderBy('onclick','regfcrex');" title="Ordenar">Creado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfcrex">
                        <input type = "hidden" name = "regfcrex" value = "<?php echo $_POST['regfcrex'] ?>" id = "regfcrex">
                        <script language="javascript">fnOrderBy('','regfcrex')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:fnOrderBy('onclick','reghcrex');" title="Ordenar">Hora</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghcrex">
                        <input type = "hidden" name = "reghcrex" value = "<?php echo $_POST['reghcrex'] ?>" id = "reghcrex">
                        <script language="javascript">fnOrderBy('','reghcrex')</script>
                      </td>
                      <td class="name" width="06%">
                        <a href = "javascript:fnOrderBy('onclick','regfmodx');" title="Ordenar">Modificado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfmodx">
                        <input type = "hidden" name = "regfmodx" value = "<?php echo $_POST['regfmodx'] ?>" id = "regfmodx">
                        <script language="javascript">fnOrderBy('','regfmodx')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:fnOrderBy('onclick','reghmodx');" title="Ordenar">Hora</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghmodx">
                        <input type = "hidden" name = "reghmodx" value = "<?php echo $_POST['reghmodx'] ?>" id = "reghmodx">
                        <script language="javascript">fnOrderBy('','reghmodx')</script>
                      </td>
                      <td class="name" width="06%">
                        <a href = "javascript:fnOrderBy('onclick','regestxx');" title="Ordenar">Estado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
                        <input type = "hidden" name = "regestxx" value = "<?php echo $_POST['regestxx'] ?>" id = "regestxx">
                        <script language="javascript">fnOrderBy('','regestxx')</script>
                      </td>
                      <td Class='name' width="02%" align="right">
                        <input type="checkbox" name="oCheckAll" onClick = 'javascript:fnMarca()'>
                      </td>
                    </tr>
                    <script languaje="javascript">
                        document.forms['frnav']['nRecords'].value = "<?php echo count($mDepositos) ?>";
                    </script>
                    <?php for ($i=0;$i<count($mDepositos);$i++) {
                      if ($i < count($mDepositos)) { // Para Controlar el Error
                        $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                        if($y % 2 == 0) {
                          $cColor = "{$vSysStr['system_row_par_color_ini']}";
                        } ?>

                        <tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
                          <td class="letra7" width="07%">
                            <a href = javascript:fnVer('<?php echo $mDepositos[$i]['depnumxx']?>')>
                              <?php echo $mDepositos[$i]['depnumxx']?>
                            </a>
                          </td>
                          <td class="letra7"><?php echo $mDepositos[$i]['tdedesxx'] ?></td>
                          <td class="letra7"><?php echo $mDepositos[$i]['cliidxxx'] ?></td>
                          <td class="letra7"><?php echo $mDepositos[$i]['clisapxx'] ?></td>
                          <td class="letra7"><?php echo $mDepositos[$i]['clinomxx'] ?></td>
                          <td class="letra7"><?php echo $mDepositos[$i]['pfadesxx'] ?></td>
                          <td class="letra7"><?php echo $mDepositos[$i]['regusrxx'] ?></td>
                          <td class="letra7"><?php echo $mDepositos[$i]['regfcrex'] ?></td>
                          <td class="letra7"><?php echo $mDepositos[$i]['reghcrex'] ?></td>
                          <td class="letra7"><?php echo $mDepositos[$i]['regfmodx'] ?></td>
                          <td class="letra7"><?php echo $mDepositos[$i]['reghmodx'] ?></td>
                          <td class="letra7"><?php echo $mDepositos[$i]['regestxx'] ?></td>
                          <td Class="letra7" align="right">
                            <input type="checkbox" name="oCheck" value = "<?php echo count($mDepositos) ?>"
                              id="<?php echo $mDepositos[$i]['depnumxx'].'~'.
                                              $mDepositos[$i]['regestxx'] ?>"
                              onclick="javascript:document.forms['frnav']['nRecords'].value='<?php echo count($mDepositos) ?>'">
                          </td>
                        </tr>
                        <?php $y++;
                      }
                    }

                    if(count($mDepositos) == 1){ ?>
                      <script language="javascript">
                        document.forms['frnav']['oCheck'].checked = true;
                      </script>
                      <?php
                    }?>
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
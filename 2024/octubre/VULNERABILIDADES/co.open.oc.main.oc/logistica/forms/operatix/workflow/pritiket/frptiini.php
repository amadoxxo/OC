<?php
namespace openComex;
/**
 * Tracking Prioridad de Ticket.
 * --- Descripcion: Este programa permite realizar consultas rapidas Prioridad de Ticket que se Encuentra en la Base de Datos.
 * @author cristian.perdomo@openits.co
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

      function fnVer(xPtiCod) {
        var cPathUrl = "frptinue.php?cPtiCod="+xPtiCod;
        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes=Ver Prioridad de Ticket;path="+"/";
        document.cookie="kModo=VER;path="+"/";
        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
        document.location = cPathUrl; // Invoco el menu.
      }

      function fnEditar(xModo) {
        switch (document.forms['frnav']['nRecords'].value) {
          case "1":
            if (document.forms['frnav']['oCheck'].checked == true) {
              var mMatriz = document.forms['frnav']['oCheck'].id.split('~');
              var cPathUrl = "frptinue.php?cPtiCod="+mMatriz[0];
              document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
              document.cookie="kMenDes=Editar Prioridad de Ticket;path="+"/";
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
                var cPathUrl = "frptinue.php?cPtiCod="+mMatriz[0];
                document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                document.cookie="kMenDes=Editar Organizacion de Ventas;path="+"/";
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
                if (confirm("Esta Seguro de Cambiar el Estado de la Prioridad del Ticket ["+mMatriz[0]+"] ?")) {
                  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                  document.forms['frestado']['cPtiCod'].value=mMatriz[0];
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
                  if (confirm("Esta Seguro de Cambiar el Estado de la Prioridad del Ticket ["+mMatriz[0]+"] ?")) {
                    zSw_Prv = 1;
                    var mMatriz = document.forms['frnav']['oCheck'][i].id.split('~');
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.forms['frestado']['cPtiCod'].value=mMatriz[0];
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

      function fnEliminar(xModo) {
        if (document.forms['frnav']['nRecords'].value!="0"){
          switch (document.forms['frnav']['nRecords'].value) {
            case "1":
              if (document.forms['frnav']['oCheck'].checked == true) {
                var mMatriz = document.forms['frnav']['oCheck'].id.split('~');
                if (confirm("Esta Seguro de Eliminar la Prioridad del Ticket ["+mMatriz[0]+"] ?")) {
                  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                  document.forms['frestado']['cPtiCod'].value=mMatriz[0];
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
                  if (confirm("Esta Seguro de Eliminar la Prioridad del Ticket ["+mMatriz[0]+"] ?")) {
                    zSw_Prv = 1;
                    var mMatriz = document.forms['frnav']['oCheck'][i].id.split('~');
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.forms['frestado']['cPtiCod'].value=mMatriz[0];
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

      function fnMarca() {
        if (document.forms['frnav']['oCheckAll'].checked == true){
          if (document.forms['frnav']['nRecords'].value == 1){
            document.forms['frnav']['oCheck'].checked=true;
          } else {
            if (document.forms['frnav']['nRecords'].value > 1){
              for (i=0;i<document.forms['frnav']['oCheck'].length;i++){
                document.forms['frnav']['oCheck'][i].checked = true;
              }
            }
          }
        } else {
          if (document.forms['frnav']['nRecords'].value == 1){
            document.forms['frnav']['oCheck'].checked=false;
          } else {
            if (document.forms['frnav']['nRecords'].value > 1){
              for (i=0;i<document.forms['frnav']['oCheck'].length;i++){
                document.forms['frnav']['oCheck'][i].checked = false;
              }
            }
          }
        }
      }

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
    <form name = "frestado" action = "frptigra.php" method = "post" target="fmpro">
      <input type = "hidden" name = "cPtiCod" value = "">
      <input type = "hidden" name = "cEstado" value = "">
    </form>

    <form name = "frnav" action="frptiini.php" method="post" target="fmwork">
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
                            if ($y == 0) {?>
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
                          if (mysql_num_rows($xUsrPer) > 0) {?>
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
                        $nf = intval($y/5);
                        $resto = $y-$nf;
                        $restan = 5-$resto;
                        if ($restan > 0) {
                          for ($i=0;$i<$restan;$i++) {
                            $celdas.="<td width='20%'></td>";
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

        if ($_POST['vSearch'] != "") {
          /**
           * Buscando los id que corresponden a las busquedas de los lefjoin
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

        $mPtiCod = array();
        $qPtiCod  = "SELECT DISTINCT SQL_CALC_FOUND_ROWS ";
        $qPtiCod .= "$cAlfa.lpar0156.pticodxx, ";
        $qPtiCod .= "$cAlfa.lpar0156.ptidesxx, ";
        $qPtiCod .= "$cAlfa.lpar0156.pticolxx, ";
        $qPtiCod .= "$cAlfa.lpar0156.regusrxx, "; // Código Usuario
        $qPtiCod .= "$cAlfa.lpar0156.regfcrex, "; // Fecha de creación
        $qPtiCod .= "$cAlfa.lpar0156.reghcrex, "; // Hora de creación
        $qPtiCod .= "$cAlfa.lpar0156.regfmodx, "; // Fecha de modificación
        $qPtiCod .= "$cAlfa.lpar0156.reghmodx, "; // Hora de modificación
        if (substr_count($_POST['cOrderByOrder'],"usrnomxx") > 0) {
          $qPtiCod .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,TRIM(CONCAT($cAlfa.SIAI0003.USRPAPEX,\" \",$cAlfa.SIAI0003.USRSAPEX,\" \",$cAlfa.SIAI0003.USRPNOMX,\" \",$cAlfa.SIAI0003.USRSNOMX))) AS usrnomxx, ";
        }
        $qPtiCod .= "$cAlfa.lpar0156.regestxx ";
        $qPtiCod .= "FROM $cAlfa.lpar0156 ";
        if (substr_count($_POST['cOrderByOrder'],"usrnomxx") > 0) {
          $qPtiCod .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.lpar0156.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
        }
        if ($_POST['vSearch'] != "") {
          $qPtiCod .= "WHERE ";
          $qPtiCod .= "$cAlfa.lpar0156.pticodxx LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qPtiCod .= "$cAlfa.lpar0156.ptidesxx LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qPtiCod .= "$cAlfa.lpar0156.regfcrex LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qPtiCod .= "$cAlfa.lpar0156.reghcrex LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qPtiCod .= "$cAlfa.lpar0156.regfmodx LIKE \"%{$_POST['vSearch']}%\" OR ";
          if ($cNombreSearch != "") {
            $qPtiCod .= "$cAlfa.lpar0156.regusrxx IN ($cNombreSearch) OR ";
          }
          $qPtiCod .= "$cAlfa.lpar0156.regestxx LIKE \"%{$_POST['vSearch']}%\" ";
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
        $qPtiCod     .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
        $cIdCountRow  = mt_rand(1000000000, 9999999999);
        $xPtiCod      = mysql_query($qPtiCod, $xConexion01, true, $cIdCountRow);

        /***** FIN SQL *****/
        $xNumRows = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD",$xConexion01);
        $xRNR     = mysql_fetch_array($xNumRows);
        $nRNR     = $xRNR['CANTIDAD'];

        while ($xRDC = mysql_fetch_array($xPtiCod)) {
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
          $mPtiCod[count($mPtiCod)] = $xRDC;
        }
      ?>
      <center>
        <table width="95%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td>
              <fieldset>
                <legend>Registros en la Consulta (<?php echo $nRNR?>)</legend>
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
                          $qBotAcc  = "SELECT sys00039.menopcxx ";
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

                          while ($mBotAcc = mysql_fetch_array($xBotAcc)) {
                            switch ($mBotAcc['menopcxx']) {
                              case "EDITAR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_edit.png" onClick = "javascript:fnEditar('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="Editar, Solo Uno">
                              <?php break;
                              case "CAMBIAESTADO": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_cambest.gif" onClick = "javascript:fnCambiaEstado('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="Cambiar Estado, Solo Uno">
                              <?php break;
                              case "ELIMINAR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_drop.png" onClick = "javascript:fnEliminar('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="Eliminar, Solo Uno">
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
                      <td class="name" width="10%">
                        <a href = "javascript:fnOrder_By('onclick','pticodxx');" title="Ordenar">C&oacute;digo</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "pticodxx">
                        <input type = "hidden" name = "pticodxx" value = "<?php echo $_POST['pticodxx'] ?>" id = "pticodxx">
                        <script language="javascript">fnOrder_By('','pticodxx')</script>
                      </td>
                      <td class="name" width="43%">
                        <a href = "javascript:fnOrder_By('onclick','ptidesxx');" title="Ordenar">Descripci&oacute;n</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ptidesxx">
                        <input type = "hidden" name = "ptidesxx" value = "<?php echo $_POST['ptidesxx'] ?>" id = "ptidesxx">
                        <script language="javascript">fnOrder_By('','ptidesxx')</script>
                      </td>
                      <td class="name" width="07%">
                        <a href = "javascript:fnOrder_By('onclick','regfcrex');" title="Ordenar">Creado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfcrex">
                        <input type = "hidden" name = "regfcrex" value = "<?php echo $_POST['regfcrex'] ?>" id = "regfcrex">
                        <script language="javascript">fnOrder_By('','regfcrex')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:fnOrder_By('onclick','reghcrex');" title="Ordenar">Hora</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghcrex">
                        <input type = "hidden" name = "reghcrex" value = "<?php echo $_POST['reghcrex'] ?>" id = "reghcrex">
                        <script language="javascript">fnOrder_By('','reghcrex')</script>
                      </td>
                      <td class="name" width="07%">
                        <a href = "javascript:fnOrder_By('onclick','regfmodx');" title="Ordenar">Modificado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfmodx">
                        <input type = "hidden" name = "regfmodx" value = "<?php echo $_POST['regfmodx'] ?>" id = "regfmodx">
                        <script language="javascript">fnOrder_By('','regfmodx')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:fnOrder_By('onclick','reghmodx');" title="Ordenar">Hora</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghmodx">
                        <input type = "hidden" name = "reghmodx" value = "<?php echo $_POST['reghmodx'] ?>" id = "reghmodx">
                        <script language="javascript">fnOrder_By('','reghmodx')</script>
                      </td>
                      <td class="name" width="05%">
                        <a href = "javascript:fnOrder_By('onclick','regestxx');" title="Ordenar">Estado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
                        <input type = "hidden" name = "regestxx" value = "<?php echo $_POST['regestxx'] ?>" id = "regestxx">
                        <script language="javascript">fnOrder_By('','regestxx')</script>
                      </td>
                      <td Class='name' width="02%" align="right">
                        <input type="checkbox" name="oCheckAll" onClick = 'javascript:fnMarca()'>
                      </td>
                    </tr>
                    <script languaje="javascript">
                      document.forms['frnav']['nRecords'].value = "<?php echo count($mPtiCod) ?>";
                    </script>
                      <?php for ($i=0;$i<count($mPtiCod);$i++) {
                        if ($i < count($mPtiCod)) { // Para Controlar el Error
                        $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                        if($y % 2 == 0) {
                          $cColor = "{$vSysStr['system_row_par_color_ini']}";
                        } ?>
                        <!--<tr bgcolor = "<?php echo $cColor ?>">-->
                        <tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
                          <td class="letra7">
                            <a href = javascript:fnVer('<?php echo $mPtiCod[$i]['pticodxx']?>')>
                                                        <?php echo $mPtiCod[$i]['pticodxx']?>
                            </a>
                          <td class="letra7"><div style="border:1px;width:10px;height:10px;background-color:<?php echo $mPtiCod[$i]['pticolxx'] ?>;display:inline-block;margin-right:5px;vertical-align:middle;"></div><?php echo $mPtiCod[$i]['ptidesxx'] ?></td>
                          <td class="letra7"><?php echo $mPtiCod[$i]['regfcrex'] ?></td>
                          <td class="letra7"><?php echo $mPtiCod[$i]['reghcrex'] ?></td>
                          <td class="letra7"><?php echo $mPtiCod[$i]['regfmodx'] ?></td>
                          <td class="letra7"><?php echo $mPtiCod[$i]['reghmodx'] ?></td>
                          <td class="letra7"><?php echo $mPtiCod[$i]['regestxx'] ?></td>
                          <td Class="letra7" align="right">
                            <input type="checkbox" name="oCheck" value = "<?php echo count($mPtiCod) ?>"
                            id="<?php echo $mPtiCod[$i]['pticodxx'].'~'.
                                            $mPtiCod[$i]['regestxx'] ?>"
                            onclick="javascript:document.forms['frnav']['nRecords'].value='<?php echo count($mPtiCod) ?>'">
                          </td>
                        </tr>
                        <?php $y++;
                        }
                      }
                    if(count($mPtiCod) == 1){ ?>
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

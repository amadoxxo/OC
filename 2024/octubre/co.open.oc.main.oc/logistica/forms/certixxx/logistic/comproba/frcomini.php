<?php
  namespace openComex;
  /**
   * Tracking Comprobantes.
   * --- Descripcion: Este programa permite realizar consultas rapidas de los Comprobantes que se Encuentran en la Base de Datos
   * @author Diego Fernando Cortes Rojas <diego.cortes@openits.co>
   * @package opencomex
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
  $xUsrMen  = f_MySql("SELECT","",$qUsrMen,$xConexion01,"");
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

      function fnVer(xComId,xComCod) {
        var ruta = "frcomnue.php?cComId=" + xComId + "&cComCod=" + xComCod;
        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes=Ver Comprobante;path="+"/";
        document.cookie="kModo=VER;path="+"/";
        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
        document.location = ruta; // Invoco el menu.
      }

      function fnEditar(xModo) {
        switch (document.forms['frgrm']['vRecords'].value) {
          case "1":
            if (document.forms['frgrm']['oChkCom'].checked == true) {
              var zMatriz = document.forms['frgrm']['oChkCom'].id.split('-');
              var ruta = "frcomnue.php?cComId="+zMatriz[0]+"&cComCod="+zMatriz[1];
              document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
              document.cookie="kMenDes=Editar Comprobante;path="+"/";
              document.cookie="kModo="+xModo+";path="+"/";
              parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
              document.location = ruta; // Invoco el menu.
            }
          break;
          default:
            var zSw_Prv = 0;
            for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
              if (document.forms['frgrm']['oChkCom'][i].checked == true && zSw_Prv == 0) {
                // Solo Deja Legalizar el Primero Seleccionado
                zSw_Prv = 1;
                var zMatriz = document.forms['frgrm']['oChkCom'][i].id.split('-');
                var ruta = "frcomnue.php?cComId="+zMatriz[0]+"&cComCod="+zMatriz[1];
                document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                document.cookie="kMenDes=Editar Comprobante;path="+"/";
                document.cookie="kModo="+xModo+";path="+"/";
                parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel4.php";
                document.location = ruta; // Invoco el menu.
              }
            }
          break;
        }
      }

      function fnCambiaEstado(xModo) {
        if (document.forms['frgrm']['vRecords'].value!="0"){
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oChkCom'].checked == true) {
                var zMatriz = document.forms['frgrm']['oChkCom'].id.split('-');
                if (confirm("Esta Seguro de Cambiar el Estado de la Comprobante No. "+zMatriz[0]+"-"+zMatriz[1]+" ?")) {
                  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                  document.forms['frestado']['cComId'].value=zMatriz[0];
                  document.forms['frestado']['cComCod'].value=zMatriz[1];
                  document.forms['frestado']['cCliEst'].value=zMatriz[2];
                  document.cookie="kModo="+xModo+";path="+"/";
                  document.forms['frestado'].submit();
                }
              }
            break;
            default:
              var zSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true && zSw_Prv == 0) {
                  var zMatriz = document.forms['frgrm']['oChkCom'][i].id.split('-');
                  if (confirm("Esta Seguro de Cambiar el Estado de la Comprobante No. "+zMatriz[0]+"-"+zMatriz[1]+" ?")) {
                    zSw_Prv = 1;
                    var zMatriz = document.forms['frgrm']['oChkCom'][i].id.split('-');
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.forms['frestado']['cComId'].value=zMatriz[0];
                    document.forms['frestado']['cComCod'].value=zMatriz[1];
                    document.forms['frestado']['cCliEst'].value=zMatriz[2];
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
        if (document.forms['frgrm']['vRecords'].value!="0"){
          switch (document.forms['frgrm']['vRecords'].value) {
            case "1":
              if (document.forms['frgrm']['oChkCom'].checked == true) {
                var zMatriz = document.forms['frgrm']['oChkCom'].id.split('-');
                if (confirm("Esta Seguro de querer Eliminar el Comprobante No. "+zMatriz[0]+"-"+zMatriz[1]+" ?")) {
                  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                  document.forms['frestado']['cComId'].value=zMatriz[0];
                  document.forms['frestado']['cComCod'].value=zMatriz[1];
                  document.cookie="kModo="+xModo+";path="+"/";
                  document.forms['frestado'].submit();
                }
              }
            break;
            default:
              var zSw_Prv = 0;
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
                if (document.forms['frgrm']['oChkCom'][i].checked == true && zSw_Prv == 0) {
                  var zMatriz = document.forms['frgrm']['oChkCom'][i].id.split('-');
                  if (confirm("Esta Seguro de querer Eliminar el Comprobante No. "+zMatriz[0]+"-"+zMatriz[1]+" ?")) {
                    zSw_Prv = 1;
                    var zMatriz = document.forms['frgrm']['oChkCom'][i].id.split('-');
                    document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
                    document.forms['frestado']['cComId'].value=zMatriz[0];
                    document.forms['frestado']['cComCod'].value=zMatriz[1];
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
        if (document.forms['frgrm']['oChkComAll'].checked == true){
          if (document.forms['frgrm']['vRecords'].value == 1){
            document.forms['frgrm']['oChkCom'].checked=true;
          } else {
            if (document.forms['frgrm']['vRecords'].value > 1){
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
                document.forms['frgrm']['oChkCom'][i].checked = true;
              }
            }
          }
        } else {
          if (document.forms['frgrm']['vRecords'].value == 1){
            document.forms['frgrm']['oChkCom'].checked=false;
          } else {
            if (document.forms['frgrm']['vRecords'].value > 1){
              for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
                document.forms['frgrm']['oChkCom'][i].checked = false;
              }
            }
          }
        }
      }

      /************************ FUNCION PARA GUARDAR EL ORDEN DEL ORDER BY DEL SQL ***********************/
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
    </script>
  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
    <form name = "frestado" action = "frcomgra.php" method = "post" target="fmpro">
      <input type = "hidden" name = "cComId"  value = "">
      <input type = "hidden" name = "cComCod" value = "">
      <input type = "hidden" name = "cCliEst" value = "">
    </form>

    <form name = "frgrm" action = "frcomini.php" method = "post" target = "fmwork">
      <input type = "hidden" name = "vRecords"   value = "">
      <input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
      <input type = "hidden" name = "vSortField" value = "<?php echo $vSortField ?>">
      <input type = "hidden" name = "vSortType"  value = "<?php echo $vSortType ?>">
      <input type = "hidden" name = "vBuscar"    value = "<?php echo $_POST['vBuscar'] ?>">
      <input type = "hidden" name = "cOrderByOrder" value = "<?php echo $_POST['cOrderByOrder'] ?>" style="width:1000">

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
                          $qUsrPer .= "proidxxx = \"{$mUsrMen['proidxxx']}\"  AND ";
                          $qUsrPer .= "menidxxx = \"{$mUsrMen['menidxxx']}\"  LIMIT 0,1";
                          $xUsrPer  = f_MySql("SELECT","",$qUsrPer,$xConexion01,"");

                          if (mysql_num_rows($xUsrPer) > 0) { ?>
                            <td Class="clase08" width="20%">
                              <center>
                                <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/<?php echo $mUsrMen['menimgon'] ?>" style = "cursor:pointer" onClick ="javascript:fnLink('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')"><br>
                                <a href = "javascript:fnLink('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')" style="color:<?php echo $vSysStr['system_comk_menu_color'] ?>"><?php echo $mUsrMen['mendesxx'] ?></a>
                              </center>
                            </td>
                          <?php	} else { ?>
                            <td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/<?php echo $mUsrMen['menimgof']?>"><br>
                            <?php echo $mUsrMen['mendesxx'] ?></center></td>
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

        $y = 0;
        $mComproba = array();
        $qComproba  = "SELECT DISTINCT SQL_CALC_FOUND_ROWS ";
        $qComproba .= "$cAlfa.lpar0117.comidxxx, "; // Id del comprobante
        $qComproba .= "$cAlfa.lpar0117.comcodxx, "; // Codigo del comprobante
        $qComproba .= "$cAlfa.lpar0117.comdesxx, "; // Descripcion del Comprobante
        $qComproba .= "$cAlfa.lpar0117.comprexx, "; // Prefijo Consecutivo
        $qComproba .= "(SELECT IF($cAlfa.lpar0117.comtcoxx = \"MANUAL\" OR ($cAlfa.lpar0117.comtcoxx = \"AUTOMATICO\" AND ($cAlfa.lpar0117.comccoxx = \"ANUAL\" OR $cAlfa.lpar0117.comccoxx = \"INDEFINIDO\")), CONCAT(SUBSTRING($cAlfa.lpar0122.peranoxx,-2), LPAD($cAlfa.lpar0122.comcscxx, 6, 0)),CONCAT(SUBSTRING($cAlfa.lpar0122.peranoxx,-2), $cAlfa.lpar0122.permesxx, LPAD($cAlfa.lpar0122.comcscxx, 4, 0))) FROM $cAlfa.lpar0122 WHERE $cAlfa.lpar0122.comidxxx = lpar0117.comidxxx AND $cAlfa.lpar0122.comcodxx = $cAlfa.lpar0117.comcodxx ORDER BY peranoxx DESC, permesxx DESC LIMIT 0,1) AS comcscxx, "; // Consecutivo Actual
        $qComproba .= "$cAlfa.lpar0117.regusrxx, "; // Usuario que creo el registro
        $qComproba .= "$cAlfa.lpar0117.regfcrex, "; // Fecha de vigencia hasta
        $qComproba .= "$cAlfa.lpar0117.reghcrex, "; // Hora de creación
        $qComproba .= "$cAlfa.lpar0117.regfmodx, "; // Fecha de modificación
        $qComproba .= "$cAlfa.lpar0117.reghmodx, "; // Hora de modificación
        if (substr_count($_POST['cOrderByOrder'], "usrnomxx") > 0) {
          $qComproba .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,TRIM(CONCAT($cAlfa.SIAI0003.USRPAPEX,\" \",$cAlfa.SIAI0003.USRSAPEX,\" \",$cAlfa.SIAI0003.USRPNOMX,\" \",$cAlfa.SIAI0003.USRSNOMX))) AS usrnomxx, ";
        }
        $qComproba .= "$cAlfa.lpar0117.regestxx ";
        $qComproba .= "FROM $cAlfa.lpar0117 ";
        $qComproba .= "LEFT JOIN $cAlfa.lpar0122 ON lpar0117.comidxxx = $cAlfa.lpar0122.comidxxx AND lpar0117.comcodxx = $cAlfa.lpar0122.comcodxx ";
        if (substr_count($_POST['cOrderByOrder'], "usrnomxx") > 0) {
          $qComproba .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.lpar0117.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
        }
        if ($_POST['vSearch'] != "") {
          $qComproba .= "WHERE ";
          $qComproba .= "$cAlfa.lpar0117.comidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qComproba .= "$cAlfa.lpar0117.comcodxx LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qComproba .= "$cAlfa.lpar0117.comdesxx LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qComproba .= "$cAlfa.lpar0117.comprexx LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qComproba .= "IF($cAlfa.lpar0117.comtcoxx = \"MANUAL\" OR ($cAlfa.lpar0117.comtcoxx = \"AUTOMATICO\" AND ($cAlfa.lpar0117.comccoxx = \"ANUAL\" OR $cAlfa.lpar0117.comccoxx = \"INDEFINIDO\")), CONCAT(SUBSTRING($cAlfa.lpar0122.peranoxx,-2), LPAD($cAlfa.lpar0122.comcscxx, 6, 0)),CONCAT(SUBSTRING($cAlfa.lpar0122.peranoxx,-2), $cAlfa.lpar0122.permesxx, LPAD($cAlfa.lpar0122.comcscxx, 4, 0))) LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qComproba .= "$cAlfa.lpar0117.regfcrex LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qComproba .= "$cAlfa.lpar0117.reghcrex LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qComproba .= "$cAlfa.lpar0117.regfmodx LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qComproba .= "$cAlfa.lpar0117.regestxx LIKE \"%{$_POST['vSearch']}%\" ";
        }
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
          $cOrderBy = "ORDER BY $cAlfa.lpar0117.regfmodx DESC ";
        }
        //// FIN CODIGO NUEVO PARA ORDER BY
        $qComproba .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
        $cIdCountRow = mt_rand(1000000000, 9999999999);
        $xComproba = mysql_query($qComproba, $xConexion01, true, $cIdCountRow);
        // echo $qComproba." ~ ".mysql_num_rows($xComproba);
        /***** FIN SQL *****/

        $xNumRows = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD",$xConexion01);
        $xRNR     = mysql_fetch_array($xNumRows);
        $nRNR     = $xRNR['CANTIDAD'];

        while ($xRDC = mysql_fetch_array($xComproba)) {
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

          $mComproba[count($mComproba)] = $xRDC;
        }
      ?>
      <center>
        <table width="95%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td>
              <fieldset>
                <legend>Comprobantes (<?php echo $nRNR ?>)</legend>
                <center>
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td class="clase08" width="14%">
                        <input type="text" class="letra" name = "vSearch" maxlength="50" value = "<?php echo $vSearch ?>" style= "width:80"
                          onblur="javascript:this.value=this.value.toUpperCase();
                                              document.forms['frgrm']['vLimInf'].value='00';
                                              document.forms['frgrm']['vLimSup'].value='30';
                                              document.forms['frgrm']['vPaginas'].value='1';
                                              document.forms['frgrm'].submit()">
                        <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_search.png" style = "cursor:pointer" title="Buscar"
                          onClick = "javascript:document.forms['frgrm']['vBuscar'].value = 'ON'
                                                document.forms['frgrm']['vSearch'].value=document.forms['frgrm']['vSearch'].value.toUpperCase();
                                                document.forms['frgrm']['vLimInf'].value='00';
                                                document.forms['frgrm']['vLimSup'].value='30';
                                                document.forms['frgrm']['vPaginas'].value='1'
                                                document.forms['frgrm'].submit()">
                        <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_show-all_bg.gif" style = "cursor:pointer" title="Mostrar Todo"
                          onClick ="javascript:document.forms['frgrm']['vSearch'].value='';
                                                document.forms['frgrm']['vLimInf'].value='00';
                                                document.forms['frgrm']['vLimSup'].value='30';
                                                document.forms['frgrm']['vPaginas'].value='1';
                                                document.forms['frgrm']['vSortField'].value='';
                                                document.forms['frgrm']['vSortType'].value='';
                                                document.forms['frgrm']['vBuscar'].value='';
                                                document.forms['frgrm'].submit()">
                      </td>
                      <td class="name" width="06%" align="left">Filas&nbsp;
                        <input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
                              onfocus = "javascript:document.forms['frgrm']['vPaginas'].value='1'"
                              onblur = "javascript:f_FixFloat(this);
                                                    document.forms['frgrm']['vLimInf'].value='00';
                                                    document.forms['frgrm'].submit()">
                      </td>
                      <td class="name" width="08%">
                        <?php if (ceil($nRNR/$vLimSup) > 1) { ?>
                          <?php if ($vPaginas == "1") { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_nextpage.png"  	style = "cursor:pointer" title="Pagina Siguiente"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_lastpage.png"  	style = "cursor:pointer" title="Ultima Pagina"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                          <?php } ?>
                          <?php if ($vPaginas > "1" && $vPaginas < ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value='1';
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value--;
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                          <?php } ?>
                          <?php if ($vPaginas == ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value='1';
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
                              onClick = "javascript:document.forms['frgrm']['vPaginas'].value--;
                                                    document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
                                                    document.forms['frgrm'].submit()">
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
                      <td class="name" width="08%" align="left">Pag&nbsp;
                        <select Class = "letrase" name = "vPaginas" value = "<?php echo $vPaginas ?>" style = "width:60%"
                          onchange="javascript:this.id = 'ON';
                                              document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(this.value-1));
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
															case "BORRAR": ?>
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
                        <td class="name" width="04%">
                        <a href="javascript:fnOrderBy('onclick','comidxxx');" title="Ordenar">Id</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="comidxxx">
                        <input type="hidden" name="comidxxx" value="<?php echo $_POST['comidxxx'] ?>" id="comidxxx">
                        <script language="javascript">
                          fnOrderBy('', 'comidxxx')
                        </script>
                      </td>
                      <td class="name" width="05%">
                        <a href="javascript:fnOrderBy('onclick','comcodxx');" title="Ordenar">C&oacute;digo</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="comcodxx">
                        <input type="hidden" name="comcodxx" value="<?php echo $_POST['comcodxx'] ?>" id="comcodxx">
                        <script language="javascript">
                          fnOrderBy('', 'comcodxx')
                        </script>
                      </td>
                      <td class="name" width="20%">
                        <a href="javascript:fnOrderBy('onclick','comdesxx');" title="Ordenar">Comprobante</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="comdesxx">
                        <input type="hidden" name="comdesxx" value="<?php echo $_POST['comdesxx'] ?>" id="comdesxx">
                        <script language="javascript">
                          fnOrderBy('', 'comdesxx')
                        </script>
                      </td>
                      <td class="name" width="06%">
                        <a href="javascript:fnOrderBy('onclick','comprexx');" title="Ordenar">Prefijo</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="comprexx">
                        <input type="hidden" name="comprexx" value="<?php echo $_POST['comprexx'] ?>" id="comprexx">
                        <script language="javascript">
                          fnOrderBy('', 'comprexx')
                        </script>
                      </td>
                      <td class="name" width="10%">
                        <a href="javascript:fnOrderBy('onclick','comcscxx');" title="Ordenar">Consecutivo Actual</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="comcscxx">
                        <input type="hidden" name="comcscxx" value="<?php echo $_POST['comcscxx'] ?>" id="comcscxx">
                        <script language="javascript">
                          fnOrderBy('', 'comcscxx')
                        </script>
                      </td>
                      <td class="name" width="14%">
                        <a href="javascript:fnOrderBy('onclick','usrnomxx');" title="Ordenar">Usuario</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="usrnomxx">
                        <input type="hidden" name="usrnomxx" value="<?php echo $_POST['usrnomxx'] ?>" id="usrnomxx">
                        <script language="javascript">
                          fnOrderBy('', 'usrnomxx')
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
                      <td class="name" width="06%">
                        <a href="javascript:fnOrderBy('onclick','reghcrex');" title="Ordenar">Hora</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="reghcrex">
                        <input type="hidden" name="reghcrex" value="<?php echo $_POST['reghcrex'] ?>" id="reghcrex">
                        <script language="javascript">
                          fnOrderBy('', 'reghcrex')
                        </script>
                      </td>
                      <td class="name" width="06%">
                        <a href="javascript:fnOrderBy('onclick','regfmodx');" title="Ordenar">Modificado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="regfmodx">
                        <input type="hidden" name="regfmodx" value="<?php echo $_POST['regfmodx'] ?>" id="regfmodx">
                        <script language="javascript">
                          fnOrderBy('', 'regfmodx')
                        </script>
                      </td>
                      <td class="name" width="06%">
                        <a href="javascript:fnOrderBy('onclick','reghmodx');" title="Ordenar">Hora</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="reghmodx">
                        <input type="hidden" name="reghmodx" value="<?php echo $_POST['reghmodx'] ?>" id="reghmodx">
                        <script language="javascript">
                          fnOrderBy('', 'reghmodx')
                        </script>
                      </td>
                      <td class="name" width="05%">
                        <a href="javascript:fnOrderBy('onclick','regestxx');" title="Ordenar">Estado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory_Logistic ?>/spacer.png" border="0" width="11" height="9" title="" id="regestxx">
                        <input type="hidden" name="regestxx" value="<?php echo $_POST['regestxx'] ?>" id="regestxx">
                        <script language="javascript">
                          fnOrderBy('', 'regestxx')
                        </script>
                      </td>
                      <td Class='name' width="2%" align="right">
                        <input type="checkbox" name="oChkComAll" onClick = 'javascript:fnMarca()'>
                      </td>
                    </tr>
                      <script languaje="javascript">
                        document.forms['frgrm']['vRecords'].value = "<?php echo count($mComproba) ?>";
                      </script>
                      <?php  for ($i=0;$i<count($mComproba);$i++) {
                        if ($i < count($mComproba)) { // Para Controlar el Error
                          $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                          if($y % 2 == 0) {
                            $cColor = "{$vSysStr['system_row_par_color_ini']}";
                          } ?>
                          <!--<tr bgcolor = "<?php echo $cColor ?>">-->
                          <tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
                            <td class="letra7"><a href = javascript:fnVer('<?php echo $mComproba[$i]['comidxxx']?>','<?php echo $mComproba[$i]['comcodxx'] ?>')><?php echo $mComproba[$i]['comidxxx'] ?></a></td>
                            <td class="letra7"><?php echo $mComproba[$i]['comcodxx'] ?></td>
                            <td class="letra7"><?php echo $mComproba[$i]['comdesxx'] ?></td>
                            <td class="letra7"><?php echo $mComproba[$i]['comprexx'] ?></td>
                            <td class="letra7" align="center"><?php echo $mComproba[$i]['comcscxx'] ?></td>
                            <td class="letra7"><?php echo $mComproba[$i]['usrnomxx'] ?></td>
                            <td class="letra7"><?php echo $mComproba[$i]['regfcrex'] ?></td>
                            <td class="letra7"><?php echo $mComproba[$i]['reghcrex'] ?></td>
                            <td class="letra7"><?php echo $mComproba[$i]['regfmodx'] ?></td>
                            <td class="letra7"><?php echo $mComproba[$i]['reghmodx'] ?></td>
                            <td class="letra7"><?php echo $mComproba[$i]['regestxx'] ?></td>
                            <td Class="letra7" align="right"><input type="checkbox" name="oChkCom"  value = "<?php echo count($mComproba) ?>"
                              id="<?php echo $mComproba[$i]['comidxxx'].'-'.$mComproba[$i]['comcodxx'].'-'.$mComproba[$i]['regestxx']?>"
                              onclick="javascript:document.forms['frgrm']['vRecords'].value='<?php echo count($mComproba) ?>'">
                            </td>
                          </tr>
                          <?php $y++;
                        }
                      } ?>
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
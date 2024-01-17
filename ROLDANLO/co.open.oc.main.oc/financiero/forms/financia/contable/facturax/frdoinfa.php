<?php
 /**
  * Tracking Historico de Observaciones a Documentos.
  * Este programa permite realizar consultas rapidas de Historico de Observaciones a Documentos que se Encuentran en la Base de Datos.
  * @author Elian Amado <elian.amado@opencomex.com>
  * @package opencomex
  */

  include("../../../../libs/php/utility.php");
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
      function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      }

      /************************ FUNCION PARA GUARDAR EL ORDEN DEL ORDER BY DEL SQL ***********************/
      function fnOrderBy(xEvento,xCampo) {
        //alert(document.forms['frgrm'][xCampo].value);
        if (document.forms['frgrm'][xCampo].value != '') {
          var vSwitch = document.forms['frgrm'][xCampo].value.split(' ');
          var cSwitch = vSwitch[1];
        } else {
          var cSwitch = '';
        }
        //alert(cSwitch);
        if (xEvento == 'onclick') {
          switch (cSwitch) {
            case '':
              document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id+' ASC,';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
                document.forms['frgrm']['cOrderByOrder'].value += xCampo+"~";
              }
            break;
            case 'ASC,':
              document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id+' DESC,';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
                document.forms['frgrm']['cOrderByOrder'].value += xCampo+"~";
              }
            break;
            case 'DESC,':
              document.forms['frgrm'][xCampo].value = '';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) >= 0) {
                document.forms['frgrm']['cOrderByOrder'].value = document.forms['frgrm']['cOrderByOrder'].value.replace(xCampo,"");
              }
            break;
          }
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

      function fnEnviarConsultaInducida(xDatos){
        document.forms['frgrm']['cPeriodos'].value     = xDatos['cPeriodos'];
		    document.forms['frgrm']['dDesde'].value        = xDatos['dDesde'];
		    document.forms['frgrm']['dHasta'].value        = xDatos['dHasta'];
		    document.forms['frgrm']['cCcoId'].value        = xDatos['cCcoId'];
		    document.forms['frgrm']['cUsrId'].value        = xDatos['cUsrId'];
        document.forms['frgrm']['cDo'].value           = xDatos['cDo'];
        document.forms['frgrm']['cTerId'].value        = xDatos['cTerId'];
        document.forms['frgrm']['cTerDV'].value        = xDatos['cTerDV'];
        document.forms['frgrm']['cTerNom'].value       = xDatos['cTerNom'];
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
        document.forms['frgrm'].action = 'frframdo.php';
        document.forms['frgrm'].target = 'cConInd';
        document.forms['frgrm'].submit();
        document.forms['frgrm'].target = 'fmwork';
        document.forms['frgrm'].action = 'frdoinfa.php';
        cWindow.focus();
      }

      if("<?php echo $_POST['cConsultaInducida']?>" == "SI"){
        if("<?php echo $_POST['cPeriodos']?>" != "99"){
          parent.fmpro.location='<?php echo $cSystem_Libs_Php_Directory ?>/utilfepe.php?gTipo='+"<?php echo $_POST['cPeriodos']?>"+'&gForm='+'frgrm'+'&gFecIni='+'dDesde'+'&gFecFin='+'dHasta';
        }
      }

    </script>
  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
    <form name = "frgrm" action = "frdoinfa.php" method = "post" target="fmwork">
      <input type = "hidden" name = "vRecords"   value = "">
      <input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
      <input type = "hidden" name = "vSortField" value = "<?php echo $vSortField ?>">
      <input type = "hidden" name = "vSortType"  value = "<?php echo $vSortType ?>">
      <input type = "hidden" name = "vTimes"     value = "<?php echo $vTimes ?>">
      <input type = "hidden" name = "vTimesSave" value = "0">
      <input type = "hidden" name = "vBuscar"    value = "<?php echo $_POST['vBuscar'] ?>">
      <input type = "hidden" name = "cOrderByOrder"  value = "<?php echo $_POST['cOrderByOrder'] ?>" style = "width:1000">
      <!--Campos ocultos de la consulta inducida-->
      <input type = "hidden" name = "cPeriodos"   value = "<?php echo $cPeriodos ?>">
      <input type = "hidden" name = "cEstadoDian"   value = "<?php echo $cEstadoDian ?>">
      <input type = "hidden" name = "cConsecutivo"  value = "<?php echo $cConsecutivo ?>">
      <input type = "hidden" name = "cDo"           value = "<?php echo $cDo ?>">
      <input type = "hidden" name = "cCcoId"        value = "<?php echo $cCcoId ?>">
      <input type = "hidden" name = "cTerId"        value = "<?php echo $cTerId ?>">
      <input type = "hidden" name = "cTerDV"        value = "<?php echo $cTerDV ?>">
      <input type = "hidden" name = "cTerNom"       value = "<?php echo $cTerNom ?>">
      <?php

         if ($vLimInf == "" && $vLimSup == "") {
          $vLimInf = "00";
          $vLimSup = $vSysStr['system_rows_page_ini'];
        }elseif ($vLimInf == "") {
          $vLimInf = "00";
        }

        if ($vPaginas == "") {
          $vPaginas = "1";
        }

        /**
         * Si Viene Vacio el $cUsrId lo Cargo con la Cookie del Usuario
         * Si no Hago el SELECT con el Usuario que me Entrega el Combo de INI
         */
        if ($cUsrId == "") {
          $cUsrId = ($_COOKIE['kUsrId'] == "ADMIN") ? "ALL":$_COOKIE['kUsrId'];
        }

        /**INICIO SQL**/
        if ($_POST['cPeriodos'] == "") {
          $_POST['cPeriodos'] == "20";
          $_POST['dDesde'] = substr(date('Y-m-d'),0,8)."01";
          $_POST['dHasta'] = date('Y-m-d');
        }

        if ($_POST['vSearch'] != "") {
          /**
           * Buscando los id que corresponden a las busquedas de los lefjoin
           */
           $qUsrNom  = "SELECT ";
           $qUsrNom .= "USRIDXXX ";
           $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
           $qUsrNom .= "WHERE IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") LIKE \"%{$_POST['vSearch']}%\" ";
           $xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
           $cUsrSearch = "";
           while ($xRUN = mysql_fetch_array($xUsrNom)) {
            $cUsrSearch .= "\"{$xRUN['USRIDXXX']}\",";
           }
           $cUsrSearch = substr($cUsrSearch,0,strlen($cUsrSearch)-1);

          $qCliNom  = "SELECT ";
          $qCliNom .= "CLIIDXXX ";
          $qCliNom .= "FROM $cAlfa.SIAI0150 ";
          $qCliNom .= "WHERE IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) != \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) LIKE \"%{$_POST['vSearch']}%\" ";
          $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
          $cCliIdSearch = "";
          while ($xRCN = mysql_fetch_array($xCliNom)) {
            $cCliIdSearch .= "\"{$xRCN['CLIIDXXX']}\",";
          }
          $cCliIdSearch = substr($cCliIdSearch,0,strlen($cCliIdSearch)-1);

        }

        // alert('Entro');
        // f_Mensaje(__FILE__,__LINE__,'Entra');
        if ($_POST['cConsultaInducida'] == 'SI') {
          
        }
        $mCabMov = array();
        $qCabMov  = "(SELECT ";
        $qCabMov .= "CONCAT($cAlfa.sys00121.sucidxxx,\"-\",$cAlfa.sys00121.docidxxx,\"-\",$cAlfa.sys00121.docsufxx) AS docidcom, ";
        $qCabMov .= "$cAlfa.sys00121.sucidxxx, ";
        $qCabMov .= "$cAlfa.sys00121.docidxxx, ";
        $qCabMov .= "$cAlfa.sys00121.docsufxx, ";
        $qCabMov .= "$cAlfa.sys00121.cliidxxx, ";
        $qCabMov .= "$cAlfa.sys00121.diridxxx, ";
        $qCabMov .= "$cAlfa.sys00121.regfcrex, ";
        $qCabMov .= "$cAlfa.sys00121.reghcrex ";

        if (substr_count($cOrderByOrder,"USRNOMXX") > 0) {
         $qCabMov .= ", IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
        }
        // SE HACE LEFT JOIN POR CADA TABLA ADICONAL DE LA QUE SE REQUIERE INFORMACION (DESCRIPCIONES Y NOMBRES)
        $qCabMov .= "FROM $cAlfa.sys00121 ";
        if (substr_count($cOrderByOrder,"USRNOMXX") > 0) {
          $qCabMov .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.sys00121.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
        }
        if (substr_count($cOrderByOrder,"CLINOMXX") > 0) {
          $qCabMov .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
        }
        if (substr_count($cOrderByOrder,"PRONOMXX") > 0) {
          $qCabMov .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.sys00121.terid2xx = $cAlfa.A.CLIIDXXX ";
        }
        // LAS CONDICIONES PROPIAS DEL INI
        $qCabMov .= "WHERE ";
        // CODIGO NUEVO PARA REEEMPLAZAR EL {$_POST['vSearch']}
        // $qCabMov .= "$cAlfa.sys00121.gofidxxx != \"100\" AND ";

        //Buscando por DO exacto o contenido
        if ($_POST['cDo'] != "" && $_POST['vSearch'] == "") {
          $qCabMov .= "(CONCAT($cAlfa.sys00121.sucidxxx,\"-\",$cAlfa.sys00121.docidxxx,\"-\",$cAlfa.sys00121.docsufxx) LIKE \"%{$_POST['cDo']}%\") AND ";
        }
        //Buscando por Cliente
        if ($_POST['cTerId'] != "") {
          $qCabMov .= "$cAlfa.sys00121.cliidxxx = \"{$_POST['cTerId']}\" AND ";
        }
        //Buscando por fecha
        if ($_POST['dDesde'] != "" && $_POST['dHasta']) {
          $qCabMov .= "$cAlfa.sys00121.regfcrex BETWEEN \"{$_POST['dDesde']}\" AND \"{$_POST['dHasta']}\") ";
        }

        if ($_POST['vSearch'] != "") {
          $qCabMov .= "(";
          $qCabMov .= "CONCAT($cAlfa.sys00121.sucidxxx,"-",$cAlfa.sys00121.docidxxx,"-",$cAlfa.sys00121.docsufxx) LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qCabMov .= "$cAlfa.sys00121.cliidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qCabMov .= "$cAlfa.sys00121.diridxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qCabMov .= "$cAlfa.sys00121.regfcrex LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qCabMov .= "$cAlfa.sys00121.reghcrex LIKE \"%{$_POST['vSearch']}%\" OR ";
        }
        if ($cUsrId <> "" && $cUsrId <> "ALL") {
         $qCabMov .= "$cAlfa.sys00121.regusrxx = \"$cUsrId\" AND ";
        }
        /***** FIN SQL *****/
        // CODIGO NUEVO PARA ORDER BY
        $cOrderBy = "";
        $vOrderByOrder = explode("~",$cOrderByOrder);
        for ($z=0;$z<count($vOrderByOrder);$z++) {
          if ($vOrderByOrder[$z] != "") {
            if (substr_count($_POST[$vOrderByOrder[$z]], "docidcom") > 0) {
              //Ordena por docidcom, comcodxx, comcscxx, comcsc2x
              $cOrdComId = str_replace("docidcom", "CONCAT(docidcom,\"-\",comcodxx,\"-\",comcscxx,\"-\", comcsc2x)", $_POST[$vOrderByOrder[$z]]);
              $cOrderBy .= $cOrdComId;
            } else {
              $cOrderBy .= $_POST[$vOrderByOrder[$z]];
            }
          }
        }
        if (strlen($cOrderBy)>0) {
          $cOrderBy = substr($cOrderBy,0,strlen($cOrderBy)-1);
          $cOrderBy = "ORDER BY ".$cOrderBy;
        } else {
          //Ordenamiento por Consecutivo 1,Consecutivo 2 o Fecha de Modificado
          if($cOrderTramite != ""){
            $cOrderBy = "ORDER BY ".$cOrderTramite. " DESC ";
          }else{
          $cOrderBy = "ORDER BY regfcrex DESC,reghcrex  DESC";
          }
        }
        // FIN CODIGO NUEVO PARA ORDER BY
        $qCabMov .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
        // echo $qCabMov;
        $xCabMov = f_MySql("SELECT","",$qCabMov,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,mysql_num_rows($xCabMov)."~".$qCabMov);

        $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
        $xRNR = mysql_fetch_array($xNumRows);
        $nRNR = $xRNR['FOUND_ROWS()'];

          while ($xRCC = mysql_fetch_array($xCabMov)) {
            //Busando Nombre del usuario
            if (substr_count($cOrderByOrder,"USRNOMXX") == 0) {
              $qUsrNom  = "SELECT ";
              $qUsrNom .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
              $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
              $qUsrNom .= "WHERE $cAlfa.SIAI0003.USRIDXXX = \"{$xRCC['regusrxx']}\" LIMIT 0,1 ";
              $xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
              if (mysql_num_rows($xUsrNom) > 0) {
                $xRUN = mysql_fetch_array($xUsrNom);
                $xRCC['USRNOMXX'] = $xRUN['USRNOMXX'];
              } else {
                $xRCC['USRNOMXX'] = "USUARIO SIN NOMBRE";
              }
            }
            //Buscando nombre del cliente
            if (substr_count($cOrderByOrder,"CLINOMXX") == 0) {
              $qCliNom  = "SELECT ";
              $qCliNom .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) != \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX ";
              $qCliNom .= "FROM $cAlfa.SIAI0150 ";
              $qCliNom .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xRCC['cliidxxx']}\" LIMIT 0,1 ";
              $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
              if (mysql_num_rows($xCliNom) > 0) {
                $xRCN = mysql_fetch_array($xCliNom);
                $xRCC['CLINOMXX'] = $xRCN['CLINOMXX'];
              } else {
                $xRCC['CLINOMXX'] = "SIN NOMBRE";
              }
            }
            //Buscando nombre del proveedor
            if (substr_count($cOrderByOrder,"PRONOMXX") == 0) {
              $qProNom  = "SELECT ";
              $qProNom .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) != \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX ";
              $qProNom .= "FROM $cAlfa.SIAI0150 ";
              $qProNom .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xRCC['terid2xx']}\" LIMIT 0,1 ";
              $xProNom = f_MySql("SELECT","",$qProNom,$xConexion01,"");
              if (mysql_num_rows($xProNom) > 0) {
                $xRPN = mysql_fetch_array($xProNom);
                $xRCC['PRONOMXX'] = $xRPN['CLINOMXX'];
              } else {
                $xRCC['PRONOMXX'] = "SIN NOMBRE";
              }
            }

            $mCabMov[count($mCabMov)] = $xRCC;
          }

      ?>
      <center>
        <table width="95%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td>
              <table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr height="21">
                  <td colspan="2">&nbsp;</td>
                </tr>
                <tr height="21">
                  <td height="21">&nbsp;</td>
                  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
                </tr>
              </table>
              <fieldset>
                <legend>Registros Seleccionados (<?php echo $nRNR ?>)</legend>
                <center>
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td class="clase08" width="15%" align="left">
                      <input type="text" class="letra" name = "vSearch" maxlength="20" value = "<?php echo $vSearch ?>" style= "width:80"
                        onblur="javascript:this.value=this.value.toUpperCase();
                                           document.frgrm.vLimInf.value='00'; ">
                      <input type="checkbox" name="cBusExc" value ="NO" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}">
                      <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:pointer" title="Buscar"
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
                                              };">
                      <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:hand" title="Mostrar Todo"
                        onClick ="javascript:document.forms['frgrm']['cUsrId'].value='<?php echo $_COOKIE['kUsrId']?>';
                                             document.forms['frgrm']['vSearch'].value='';
                                             document.forms['frgrm']['cDo'].value='';
                                             document.forms['frgrm']['vLimInf'].value='00';
                                             document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
                                             document.forms['frgrm']['vPaginas'].value='1';
                                             document.forms['frgrm']['vSortField'].value='';
                                             document.forms['frgrm']['vSortType'].value='';
                                             document.forms['frgrm']['vTimes'].value='';
                                             document.forms['frgrm']['dDesde'].value='';
                                             document.forms['frgrm']['dDesde'].value='<?php echo substr(date('Y-m-d'),0,8)."01";  ?>';
                                             document.forms['frgrm']['dHasta'].value='<?php echo date('Y-m-d');  ?>';
                                             document.forms['frgrm']['vBuscar'].value='';
                                             document.forms['frgrm']['cPeriodos'].value='20';
                                             document.forms['frgrm']['cOrderByOrder'].value='';
                                             document.forms['frgrm'].submit()">
                      <img src = "<?php echo $cPlesk_Skin_Directory ?>/cert_ca_cert_on.gif" style = "cursor:pointer" title="Consulta inducida"
                        onClick ="javascript:fnConsultaInducida()">
                        <script language = "javascript">
                            if ("<?php echo $_POST['cBusExc'] ?>" == "SI") {
                                document.forms['frgrm']['cBusExc'].value   = "SI";
                                document.forms['frgrm']['cBusExc'].checked = true;
                            } else {
                                document.forms['frgrm']['cBusExc'].value   = "NO";
                                document.forms['frgrm']['cBusExc'].checked = false;
                            }
                        </script>
                      </td>
                      <td class="name" width="03%" align="left">Filas&nbsp;
                        <input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
                          onblur = "javascript:uFixFloat(this);
                                               document.frgrm.vLimInf.value='00'; ">
                      </td>
                      <td class="name" width="06%" align="center">
                        <?php if (ceil($nRNR/$vLimSup) > 1) { ?>
                          <?php if ($vPaginas == "1") { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"    style = "cursor:pointer" title="Pagina Siguiente"
                              onClick = "javascript:document.frgrm.vPaginas.value++;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"    style = "cursor:pointer" title="Ultima Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <?php } ?>
                          <?php if ($vPaginas > "1" && $vPaginas < ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='1';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
                              onClick = "javascript:document.frgrm.vPaginas.value--;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente"
                              onClick = "javascript:document.frgrm.vPaginas.value++;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='<?php echo ceil($nRNR/$vLimSup) ?>';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                          <?php } ?>
                          <?php if ($vPaginas == ceil($nRNR/$vLimSup)) { ?>
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
                              onClick = "javascript:document.frgrm.vPaginas.value='1';
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
                              onClick = "javascript:document.frgrm.vPaginas.value--;
                                                    document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
                                                    document.frgrm.submit()">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_nextpage.png" style = "cursor:pointer" title="Pagina Siguiente">
                            <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_lastpage.png" style = "cursor:pointer" title="Ultima Pagina">
                          <?php } ?>
                        <?php } else { ?>
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina">
                        <?php } ?>
                      </td>
                      <td class="name" width="08%" align="center">Pag&nbsp;
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
                      <td class="name" width="14%" align="center" >
                        <select class="letrase" size="1" name="cPeriodos" style = "width:100%" value = "<?php echo $_POST['cPeriodos'] ?>"
                          onChange = "javascript:
                                      parent.fmpro.location='<?php echo $cSystem_Libs_Php_Directory ?>/utilfepe.php?gTipo='+this.value+'&gForm='+'frgrm'+'&gFecIni='+'dDesde'+'&gFecFin='+'dHasta';
                                      if (document.forms['frgrm']['cPeriodos'].value == '99') {
                                        document.forms['frgrm']['dDesde'].readOnly = false;
                                        document.forms['frgrm']['dHasta'].readOnly = false;
                                      } else {
                                        document.forms['frgrm']['dDesde'].readOnly = true;
                                        document.forms['frgrm']['dHasta'].readOnly = true;
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
                      <td class="name" width="08%" align="center">
                        <input type = "text" Class = "letra" style = "width:70%;text-align:center" name = "dDesde" value = "<?php
                        if($_POST['dDesde']=="" && $_POST['cPeriodos'] == ""){
                          echo substr(date('Y-m-d'),0,8)."01";
                        } else{
                          echo $_POST['dDesde'];
                        } ?>" readOnly
                          onblur="javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));">
                      </td>
                      <td class="name" width="08%" align="center">
                        <input type = "text" Class = "letra" style = "width:70%;text-align:center" name = "dHasta" value = "<?php
                          if($_POST['dHasta']=="" && $_POST['cPeriodos'] == ""){
                            echo date('Y-m-d');
                          } else{
                            echo $_POST['dHasta'];
                          }  ?>" readOnly
                          onblur = "javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));">
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
                        <select Class = "letrase" name = "cCcoId" value = "<?php echo $cCcoId ?>" style = "width:99%">
                          <option value = "ALL" selected>SUCURSALES</option>
                          <?php
                          //  if (empty($vSearch)) {
                              $qSucDat  = "SELECT sucidxxx,ccoidxxx,sucdesxx FROM $cAlfa.fpar0008 WHERE ";
                              $qSucDat .= "regestxx = \"ACTIVO\" ORDER BY sucdesxx";
                              $xSucDat = f_MySql("SELECT","",$qSucDat,$xConexion01,"");
                              if (mysql_num_rows($xSucDat) > 0) {
                                while ($xRSD = mysql_fetch_array($xSucDat)) {
                                  if ($xRSD['ccoidxxx'] == $cCcoId) { ?>
                                    <option value = "<?php echo $xRSD['ccoidxxx']?>" selected><?php echo $xRSD['sucdesxx'] ?></option>
                                  <?php } else { ?>
                                    <option value = "<?php echo $xRSD['ccoidxxx']?>"><?php echo $xRSD['sucdesxx'] ?></option>
                                  <?php }
                                }
                              } else {
                                //f_Mensaje(__FILE__,__LINE__,"No se Encontraron Sucursales");
                              }
                            //}
                          ?>
                        </select>
                      </td>
                        <td class="name" width="13%" align="left">
                          <select Class = "letrase" name = "cUsrId" value = "<?php echo $cUsrId ?>" style = "width:99%" >
                            <option value = "ALL" selected>USUARIOS</option>
                            <?php
                            $qUsuDat  = "SELECT DISTINCT regusrxx, ";
                            $qUsuDat .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
                            $qUsuDat .= "FROM $cAlfa.sys00121 ";
                            $qUsuDat .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.sys00121.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
                            //$qUsuDat .= "regestxx = \"ACTIVO\" ORDER BY sucdesxx";
                            $xUsuDat = f_MySql("SELECT","",$qUsuDat,$xConexion01,"");
                            if (mysql_num_rows($xUsuDat) > 0) {
                              while ($xRUD = mysql_fetch_array($xUsuDat)) { ?>
                               <option value = "<?php echo $xRUD['regusrxx'] ?>"><?php echo
                               $xRUD['USRNOMXX'] ?></option>
                              <?php }
                            }  ?>
                          </select>
                        </td>
                        <td class="name" width="10%" align="left">
                          <select Class = "letrase" name = "cOrderTramite" value = "<?php echo $cOrderTramite ?>" style = "width:99%" >
                            <option value = "" >ORDENAR POR</option>
                            <option value = "comcscxx">CONSECUTIVO 1</option>
                            <option value = "comcsc2x">CONSECUTIVO 2</option>
                            <option value = "cliidxxx">FECHA COMPROBANTE</option>
                          </select>
                          <script language='javascript'>
                            document.forms['frgrm']['cOrderTramite'].value = "<?php echo $cOrderTramite ?>";
                          </script>
                        </td>
                        <td Class="name"align="right">&nbsp;</td>
                      </tr>
                    </table>
                  </center>
                  <hr></hr>
                  <center>
                    <table cellspacing="0" width="100%">
                      <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                        <td class="name" width="25%">
                          <a href = "javascript:fnOrderBy('onclick','docidcom');" title="Ordenar">Do</a>&nbsp;
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docidcom">
                          <input type = "hidden" name = "docidcom" value = "<?php echo $_POST['docidcom'] ?>" id = "docidcom">
                          <script language="javascript">fnOrderBy('','docidcom')</script>
                        </td>
                        <td class="name" width="25%">
                          <a href = "javascript:fnOrderBy('onclick','CLINOMXX');" title="Ordenar">Cliente</a>&nbsp;
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "CLINOMXX">
                          <input type = "hidden" name = "CLINOMXX" value = "<?php echo $_POST['CLINOMXX'] ?>" id = "CLINOMXX">
                          <script language="javascript">fnOrderBy('','CLINOMXX')</script>
                        </td>
                        <td class="name" width="20%">
                          <a href = "javascript:fnOrderBy('onclick','diridxxx');" title="Ordenar">Director</a>&nbsp;
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "diridxxx">
                          <input type = "hidden" name = "diridxxx" value = "<?php echo $_POST['diridxxx'] ?>" id = "diridxxx">
                          <script language="javascript">fnOrderBy('','diridxxx')</script>
                        </td>
                        <td class="name" width="15%">
                        <a href = "javascript:fnOrderBy('onclick','regfcrex');" title="Ordenar">Fecha Instruccion Facturacion</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfcrex">
                        <input type = "hidden" name = "regfcrex" value = "<?php echo $_POST['regfcrex'] ?>" id = "regfcrex">
                        <script language="javascript">fnOrderBy('','regfcrex')</script>
                        </td>
                        <td class="name" width="15%">
                          <a href = "javascript:fnOrderBy('onclick','reghcrex');" title="Ordenar">Hora Instruccion Facturacion</a>&nbsp;
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghcrex">
                          <input type = "hidden" name = "reghcrex" value = "<?php echo $_POST['reghcrex'] ?>" id = "reghcrex">
                          <script language="javascript">fnOrderBy('','reghcrex')</script>
                        </td>
                      </tr>
                        <script languaje="javascript">
                          document.forms['frgrm']['vRecords'].value = "<?php echo count($mCabMov) ?>";
                        </script>
                        <?php

                         $y = 0;

                         for ($i=0;$i<count($mCabMov);$i++) {

                          if ($y <= count($mCabMov)) { // Para Controlar el Error
                            $zColor = "{$vSysStr['system_row_impar_color_ini']}";
                            if($y % 2 == 0) {
                              $zColor = "{$vSysStr['system_row_par_color_ini']}";
                            } ?>
                            <!--<tr bgcolor = "<?php echo $zColor ?>">-->
                            <tr id="<?php echo $mCabMov[$i]['sucidxxx'].'-'.$mCabMov[$i]['docidxxx'].'-'.$mCabMov[$i]['docsufxx'] ?>" bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')"
                              onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')"  >
                              <td class="letra7"><?php echo $mCabMov[$i]['docidcom'] ?></td>
                              <td class="letra7"><?php echo $mCabMov[$i]['CLINOMXX'] ?></td>
                              <td class="letra7"><?php echo $mCabMov[$i]['diridxxx'] ?></td>
                              <td class="letra7"><?php echo $mCabMov[$i]['regfcrex'] ?></td>
                              <td class="letra7"><?php echo $mCabMov[$i]['reghcrex'] ?></td>
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

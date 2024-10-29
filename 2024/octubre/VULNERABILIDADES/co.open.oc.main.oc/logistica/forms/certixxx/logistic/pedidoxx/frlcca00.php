<?php
namespace openComex;
/**
 * --- Descripcion: Consulta las Certificaciones:
 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
 * @package openComex
 * @version 001
 */

include("../../../../../financiero/libs/php/utility.php");

$cAnioIni = (date('Y') - 2);

if ($gModo != "" && $gFunction != "") { ?>
  <html>
    <head>
      <title>Param&eacute;trica de las Certificaci&oacute;n</title>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
    <script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    </head>
    <body topmargin = 0 leftmargin = 0 marginwidth = 0 marginheight = 0 style = 'margin-right:0'>
      <center>
        <table border ="0" cellpadding="0" cellspacing="0" width="300">
          <tr>
            <td>
              <fieldset>
                <legend>Param&eacute;trica de las Certificaci&oacute;n</legend>
                <form name = "frgrm" action = "" method = "post" target = "fmpro">
                  <?php
                    switch ($gModo) {
                      case "WINDOW":
                        // Traigo Datos de la Certificacion
                        $mDatos = array();
                        for ($nAnio=$cAnioIni;$nAnio<=date('Y');$nAnio++) {
                          $qCertiCab  = "SELECT ";
                          $qCertiCab .= "$cAlfa.lcca$nAnio.*, ";
                          $qCertiCab .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,\"CLIENTE SIN NOMBRE\") AS clinomxx ";
                          $qCertiCab .= "FROM $cAlfa.lcca$nAnio ";
                          $qCertiCab .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lcca$nAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
                          $qCertiCab .= "WHERE ";
                          if ($gCliId != "") {
                            $qCertiCab .= "$cAlfa.lcca$nAnio.cliidxxx LIKE \"%$gCliId%\" AND ";
                          }
                          $qCertiCab .= "$cAlfa.lcca$nAnio.regestxx IN (\"CERTIFICADO\",\"PREFACTURADO_PARCIAL\",\"FACTURADO_PARCIAL\") ";
                          $qCertiCab .= "ORDER BY ABS($cAlfa.lcca$nAnio.comcscxx) ASC ";
                          $xCertiCab = f_MySql("SELECT","",$qCertiCab,$xConexion01,"");
                          // f_Mensaje(__FILE__,__LINE__,$qCertiCab."~".mysql_num_rows($xCertiCab));
                          // FIN Traigo Datos de la Certificacion
                          if (mysql_num_rows($xCertiCab) > 0) {
                            while ($xRCC = mysql_fetch_array($xCertiCab)) {
                              $nInd_mDatos = count($mDatos);
                              $mDatos[$nInd_mDatos] = $xRCC;
                              $mDatos[$nInd_mDatos]['cceranox'] = $nAnio;
                            }
                          }
                        }
                        
                        if (count($mDatos) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "470">
                              <tr>
                                <td width = "050" Class = "name"><center>ID</center></td>
                                <td width = "110" Class = "name"><center>&nbsp;</center></td>
                                <td width = "010" Class = "name"><center>&nbsp;</center></td>
                              </tr>
                              <?php 
                              for ($i=0; $i < count($mDatos); $i++) { 
                                // Consulto información de la MIF
                                $gPerAno  = $mDatos[$i]['mifidano'];
                                $qMifCab  = "SELECT ";
                                $qMifCab .= "$cAlfa.lmca$gPerAno.* ";
                                $qMifCab .= "FROM $cAlfa.lmca$gPerAno ";
                                $qMifCab .= "WHERE ";
                                $qMifCab .= "$cAlfa.lmca$gPerAno.mifidxxx = \"{$mDatos[$i]['mifidxxx']}\" LIMIT 0,1 ";
                                $xMifCab  = f_MySql("SELECT","",$qMifCab,$xConexion01,"");
                                $vMifCab  = array();
                                if (mysql_num_rows($xMifCab) > 0) {
                                  $vMifCab = mysql_fetch_array($xMifCab);
                                }

                                // Consulta la información del Depósito
                                $qDeposito  = "SELECT ";
                                $qDeposito .= "lpar0155.depnumxx, ";
                                $qDeposito .= "lpar0155.ccoidocx, ";
                                $qDeposito .= "lpar0001.orvsapxx, ";
                                $qDeposito .= "lpar0002.ofvsapxx, ";
                                $qDeposito .= "lpar0002.ofvdesxx, ";
                                $qDeposito .= "lpar0151.ccofvdxx, ";
                                $qDeposito .= "lpar0151.ccofvhxx, ";
                                $qDeposito .= "lpar0155.regestxx ";
                                $qDeposito .= "FROM $cAlfa.lpar0155 ";
                                $qDeposito .= "LEFT JOIN $cAlfa.lpar0001 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0001.orvsapxx ";
                                $qDeposito .= "LEFT JOIN $cAlfa.lpar0002 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0002.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0002.ofvsapxx ";
                                $qDeposito .= "LEFT JOIN $cAlfa.lpar0151 ON $cAlfa.lpar0155.ccoidocx = $cAlfa.lpar0151.ccoidocx ";
                                $qDeposito .= "WHERE ";
                                $qDeposito .= "lpar0155.depnumxx = \"{$mDatos[$i]['depnumxx']}\" AND ";
                                $qDeposito .= "lpar0155.regestxx = \"ACTIVO\" ";
                                $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
                                $vDeposito = array();
                                if (mysql_num_rows($xDeposito) > 0) {
                                  $vDeposito = mysql_fetch_array($xDeposito);
                                }

                                // Si la poción de Consolidado está en NO se debe validar que la oficina de la certificación sea igual a la seleccionada en el paso 1
                                $nIncluir = 1;
                                if ($gComConso == "NO" && $gOfvSap != "") {
                                  if ($gOfvSap == $vDeposito['ofvsapxx']) {
                                    $nIncluir = 1;
                                  } else {
                                    $nIncluir = 0;
                                  }
                                }

                                if ($nIncluir == 1) {
                                  ?>
                                  <tr>
                                    <td width = "050" class= "name">
                                      <a href = "javascript:window.opener.document.forms['frgrm']['cCerId'+'<?php echo $gGrid . $gSecuencia ?>'].value     = '<?php echo $mDatos[$i]['ceridxxx']?>';
                                                            window.opener.document.forms['frgrm']['cCerComCsc'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $mDatos[$i]['comidxxx']."-".$mDatos[$i]['comprexx']."-".$mDatos[$i]['comcscxx'] ?>';
                                                            window.opener.document.forms['frgrm']['cCerAno'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $mDatos[$i]['cceranox']?>';
                                                            window.opener.document.forms['frgrm']['cMifId'+'<?php echo $gGrid . $gSecuencia ?>'].value     = '<?php echo $vMifCab['mifidxxx'] ?>';
                                                            window.opener.document.forms['frgrm']['cMifIdAno'+'<?php echo $gGrid . $gSecuencia ?>'].value  = '<?php echo $gPerAno ?>';
                                                            window.opener.document.forms['frgrm']['cMifComCsc'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $vMifCab['comidxxx']."-".$vMifCab['comprexx']."-".$vMifCab['comcscxx'] ?>';
                                                            window.opener.document.forms['frgrm']['cOrvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['orvsapxx'] ?>';
                                                            window.opener.document.forms['frgrm']['cOfvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['ofvsapxx'] ?>';
                                                            window.opener.document.forms['frgrm']['cOfvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['ofvdesxx'] ?>';
                                                            window.opener.document.forms['frgrm']['cCerFde'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $mDatos[$i]['cerfdexx'] ?>';
                                                            window.opener.document.forms['frgrm']['cCerFha'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $mDatos[$i]['cerfhaxx'] ?>';
                                                            window.opener.document.forms['frgrm']['cDepNum'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vMifCab['depnumxx'] ?>';
                                                            window.opener.document.forms['frgrm']['cCcoIdOc'+'<?php echo $gGrid . $gSecuencia ?>'].value   = '<?php echo $vDeposito['ccoidocx'] ?>';
                                                            window.opener.document.forms['frgrm']['cCcoFvD'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['ccofvdxx'] ?>';
                                                            window.opener.document.forms['frgrm']['cCcoFvH'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['ccofvhxx'] ?>';
                                                            window.opener.fnLinks('<?php echo $gFunction ?>','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
                                                            window.close();">
                                                            <?php echo $mDatos[$i]['comidxxx']."-".$mDatos[$i]['comprexx']."-".$mDatos[$i]['comcscxx'] ?>
                                      </a>
                                    </td>
                                    <td width = "110" class= "name"> <?php echo $mDatos[$i]['clinomxx'] ?></td>
                                    <td width = "010" class= "name">
                                      <center>
                                        <img src = "<?php echo $cPlesk_Skin_Directory_Logistic ?>/ver_anexos.png" onClick = "javascript:window.opener.fnImprimirCertificacion('<?php echo $mDatos[$i]['ceridxxx'] ?>', '<?php echo $mDatos[$i]['cceranox'] ?>')" style = "cursor:pointer;" title="<?php echo $mBotAcc['mendesxx'] ?>">
                                      </center>
                                    </td>
                                  </tr>
                                  <?php 
                                }
                              } ?>
                            </table>
                          </center>
                          <?php
                        } else {
                          f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros."); ?>
                          <script language="javascript">
                            window.opener.document.forms['frgrm']['cCerId'+'<?php echo $gGrid . $gSecuencia ?>'].value     = '';
                            window.opener.document.forms['frgrm']['cCerComCsc'+'<?php echo $gGrid . $gSecuencia ?>'].value = '';
                            window.opener.document.forms['frgrm']['cCerAno'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            window.opener.document.forms['frgrm']['cMifId'+'<?php echo $gGrid . $gSecuencia ?>'].value     = '';
                            window.opener.document.forms['frgrm']['cMifIdAno'+'<?php echo $gGrid . $gSecuencia ?>'].value  = '';
                            window.opener.document.forms['frgrm']['cMifComCsc'+'<?php echo $gGrid . $gSecuencia ?>'].value = '';
                            window.opener.document.forms['frgrm']['cOrvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            window.opener.document.forms['frgrm']['cOfvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            window.opener.document.forms['frgrm']['cOfvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            window.opener.document.forms['frgrm']['cCerFde'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            window.opener.document.forms['frgrm']['cCerFha'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            window.opener.document.forms['frgrm']['cDepNum'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            window.opener.document.forms['frgrm']['cCcoIdOc'+'<?php echo $gGrid . $gSecuencia ?>'].value   = '';
                            window.opener.document.forms['frgrm']['cCcoFvD'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            window.opener.document.forms['frgrm']['cCcoFvH'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            window.close();
                          </script>
                        <?php 
                        }
                      break;
                      case "VALID":
                        // Traigo Datos de la Certificacion
                        $mDatos = array();
                        for ($nAnio=$cAnioIni;$nAnio<=date('Y');$nAnio++) {
                          $qCertiCab  = "SELECT ";
                          $qCertiCab .= "$cAlfa.lcca$nAnio.*, ";
                          $qCertiCab .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,\"CLIENTE SIN NOMBRE\") AS clinomxx ";
                          $qCertiCab .= "FROM $cAlfa.lcca$nAnio ";
                          $qCertiCab .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lcca$nAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
                          $qCertiCab .= "WHERE ";
                          if ($gCliId != "") {
                            $qCertiCab .= "$cAlfa.lcca$nAnio.cliidxxx LIKE \"%$gCliId%\" AND ";
                          }
                          $qCertiCab .= "$cAlfa.lcca$nAnio.regestxx IN (\"CERTIFICADO\",\"PREFACTURADO_PARCIAL\",\"FACTURADO_PARCIAL\") ";
                          $xCertiCab  = f_MySql("SELECT","",$qCertiCab,$xConexion01,"");
                          // f_Mensaje(__FILE__,__LINE__,$qCertiCab."~".mysql_num_rows($xCertiCab));
                          if (mysql_num_rows($xCertiCab) > 0) {
                            while ($xRCC = mysql_fetch_array($xCertiCab)) {
                              $nInd_mDatos = count($mDatos);
                              $mDatos[$nInd_mDatos] = $xRCC;
                              $mDatos[$nInd_mDatos]['cceranox'] = $nAnio;
                            }
                          }
                        }

                        if (count($mDatos) > 0) { 
                          if (count($mDatos) == 1) { 
                            for ($i=0; $i < count($mDatos); $i++) { 
                              // Consulto información de la MIF
                              $gPerAno  = $mDatos[$i]['mifidano'];
                              $qMifCab  = "SELECT ";
                              $qMifCab .= "$cAlfa.lmca$gPerAno.* ";
                              $qMifCab .= "FROM $cAlfa.lmca$gPerAno ";
                              $qMifCab .= "WHERE ";
                              $qMifCab .= "$cAlfa.lmca$gPerAno.mifidxxx = \"{$mDatos[$i]['mifidxxx']}\" LIMIT 0,1 ";
                              $xMifCab  = f_MySql("SELECT","",$qMifCab,$xConexion01,"");
                              // f_Mensaje(__FILE__,__LINE__,$qMifCab."~".mysql_num_rows($xMifCab));
                              $vMifCab  = array();
                              if (mysql_num_rows($xMifCab) > 0) {
                                $vMifCab = mysql_fetch_array($xMifCab);
                              }

                              // Consulta la información del Depósito
                              $qDeposito  = "SELECT ";
                              $qDeposito .= "lpar0155.depnumxx, ";
                              $qDeposito .= "lpar0155.ccoidocx, ";
                              $qDeposito .= "lpar0001.orvsapxx, ";
                              $qDeposito .= "lpar0002.ofvsapxx, ";
                              $qDeposito .= "lpar0002.ofvdesxx, ";
                              $qDeposito .= "lpar0151.ccofvdxx, ";
                              $qDeposito .= "lpar0151.ccofvhxx, ";
                              $qDeposito .= "lpar0155.regestxx ";
                              $qDeposito .= "FROM $cAlfa.lpar0155 ";
                              $qDeposito .= "LEFT JOIN $cAlfa.lpar0001 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0001.orvsapxx ";
                              $qDeposito .= "LEFT JOIN $cAlfa.lpar0002 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0002.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0002.ofvsapxx ";
                              $qDeposito .= "LEFT JOIN $cAlfa.lpar0151 ON $cAlfa.lpar0155.ccoidocx = $cAlfa.lpar0151.ccoidocx ";
                              $qDeposito .= "WHERE ";
                              $qDeposito .= "lpar0155.depnumxx = \"{$mDatos[$i]['depnumxx']}\" AND ";
                              $qDeposito .= "lpar0155.regestxx = \"ACTIVO\" ";
                              $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
                              $vDeposito = array();
                              if (mysql_num_rows($xDeposito) > 0) {
                                $vDeposito = mysql_fetch_array($xDeposito);
                              }

                              // Si la poción de Consolidado está en NO se debe validar que la oficina de la certificación sea igual a la seleccionada en el paso 1
                              $nIncluir = 1;
                              if ($gComConso == "NO" && $gOfvSap != "") {
                                if ($gOfvSap == $vDeposito['ofvsapxx']) {
                                  $nIncluir = 1;
                                } else {
                                  $nIncluir = 0;
                                }
                              }

                              if ($nIncluir == 1) {
                                ?>
                                <script language = "javascript">
                                  parent.fmwork.document.forms['frgrm']['cCerId'+'<?php echo $gGrid . $gSecuencia ?>'].value     = '<?php echo $mDatos[$i]['ceridxxx'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cCerComCsc'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $mDatos[$i]['comidxxx']."-".$mDatos[$i]['comprexx']."-".$mDatos[$i]['comcscxx'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cCerAno'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $mDatos[$i]['cceranox'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cMifId'+'<?php echo $gGrid . $gSecuencia ?>'].value     = '<?php echo $vMifCab['mifidxxx'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cMifIdAno'+'<?php echo $gGrid . $gSecuencia ?>'].value  = '<?php echo $gPerAno ?>';
                                  parent.fmwork.document.forms['frgrm']['cMifComCsc'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $vMifCab['comidxxx']."-".$vMifCab['comprexx']."-".$vMifCab['comcscxx'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cOrvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['orvsapxx'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cOfvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['ofvsapxx'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cOfvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['ofvdesxx'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cCerFde'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $mDatos[$i]['cerfdexx'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cCerFha'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $mDatos[$i]['cerfhaxx'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cDepNum'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vMifCab['depnumxx'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cCcoIdOc'+'<?php echo $gGrid . $gSecuencia ?>'].value   = '<?php echo $vDeposito['ccoidocx'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cCcoFvD'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['ccofvdxx'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cCcoFvH'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['ccofvhxx'] ?>';
                                </script>
                                <?php
                              }
                            }
                          }else{ ?>
                            <script language = "javascript">
                              parent.fmwork.fnLinks('<?php echo $gFunction ?>','WINDOW','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
                            </script>
                          <?php
                          }
                        }else{ 
                          f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros."); 
                        }
                      break;
                      case "EXACT":
                        // Traigo Datos de la Certificacion
                        $qCertiCab  = "SELECT ";
                        $qCertiCab .= "$cAlfa.lcca$cPerAno.* ";
                        $qCertiCab .= "FROM $cAlfa.lcca$cPerAno ";
                        $qCertiCab .= "WHERE ";
                        $qCertiCab .= "$cAlfa.lcca$cPerAno.ceridxxx = \"$gCerId\" AND ";
                        $qCertiCab .= "$cAlfa.lcca$cPerAno.comidxxx = \"C\" AND ";
                        if ($gCliId != "") {
                          $qCertiCab .= "$cAlfa.lcca$cPerAno.cliidxxx = \"$gCliId\" AND ";
                        }
                        $qCertiCab .= "$cAlfa.lcca$cPerAno.regestxx IN (\"CERTIFICADO\",\"PREFACTURADO_PARCIAL\",\"FACTURADO_PARCIAL\") ";
                        $xCertiCab  = f_MySql("SELECT","",$qCertiCab,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qCertiCab."~".mysql_num_rows($xCertiCab));
                        if (mysql_num_rows($xCertiCab) == 1) { 
                          $vCertiCab  = mysql_fetch_array($xCertiCab);
                          // Consulto información de la MIF
                          $gPerAno  = $vCertiCab['mifidano'];
                          $qMifCab  = "SELECT ";
                          $qMifCab .= "$cAlfa.lmca$gPerAno.* ";
                          $qMifCab .= "FROM $cAlfa.lmca$gPerAno ";
                          $qMifCab .= "WHERE ";
                          $qMifCab .= "$cAlfa.lmca$gPerAno.mifidxxx = \"{$vCertiCab['mifidxxx']}\" LIMIT 0,1 ";
                          $xMifCab  = f_MySql("SELECT","",$qMifCab,$xConexion01,"");
                          // f_Mensaje(__FILE__,__LINE__,$qMifCab."~".mysql_num_rows($xMifCab));
                          $vMifCab  = array();
                          if (mysql_num_rows($xMifCab) > 0) {
                            $vMifCab = mysql_fetch_array($xMifCab);
                          }

                          // Consulta la información del Depósito
                          $qDeposito  = "SELECT ";
                          $qDeposito .= "lpar0155.depnumxx, ";
                          $qDeposito .= "lpar0155.ccoidocx, ";
                          $qDeposito .= "lpar0001.orvsapxx, ";
                          $qDeposito .= "lpar0002.ofvsapxx, ";
                          $qDeposito .= "lpar0002.ofvdesxx, ";
                          $qDeposito .= "lpar0151.ccofvdxx, ";
                          $qDeposito .= "lpar0151.ccofvhxx, ";
                          $qDeposito .= "lpar0155.regestxx ";
                          $qDeposito .= "FROM $cAlfa.lpar0155 ";
                          $qDeposito .= "LEFT JOIN $cAlfa.lpar0001 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0001.orvsapxx ";
                          $qDeposito .= "LEFT JOIN $cAlfa.lpar0002 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0002.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0002.ofvsapxx ";
                          $qDeposito .= "LEFT JOIN $cAlfa.lpar0151 ON $cAlfa.lpar0155.ccoidocx = $cAlfa.lpar0151.ccoidocx ";
                          $qDeposito .= "WHERE ";
                          $qDeposito .= "lpar0155.depnumxx = \"{$vCertiCab['depnumxx']}\" AND ";
                          $qDeposito .= "lpar0155.regestxx = \"ACTIVO\" ";
                          $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
                          $vDeposito = array();
                          if (mysql_num_rows($xDeposito) > 0) {
                            $vDeposito = mysql_fetch_array($xDeposito);
                          }

                          // Si la poción de Consolidado está en NO se debe validar que la oficina de la certificación sea igual a la seleccionada en el paso 1
                          $nIncluir = 1;
                          if ($gComConso == "NO" && $gOfvSap != "") {
                            if ($gOfvSap == $vDeposito['ofvsapxx']) {
                              $nIncluir = 1;
                            } else {
                              $nIncluir = 0;
                            }
                          }

                          if ($nIncluir == 1) {
                            ?>
                            <script language = "javascript">
                              parent.fmwork.document.forms['frgrm']['cCerId'+'<?php echo $gGrid . $gSecuencia ?>'].value     = '<?php echo $vCertiCab['ceridxxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cCerComCsc'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $vCertiCab['comidxxx']."-".$vCertiCab['comprexx']."-".$vCertiCab['comcscxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cCerAno'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $cPerAno ?>';
                              parent.fmwork.document.forms['frgrm']['cMifId'+'<?php echo $gGrid . $gSecuencia ?>'].value     = '<?php echo $vMifCab['mifidxxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cMifIdAno'+'<?php echo $gGrid . $gSecuencia ?>'].value  = '<?php echo $gPerAno ?>';
                              parent.fmwork.document.forms['frgrm']['cMifComCsc'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $vMifCab['comidxxx']."-".$vMifCab['comprexx']."-".$vMifCab['comcscxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cOrvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['orvsapxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cOfvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['ofvsapxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cOfvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['ofvdesxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cCerFde'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vCertiCab['cerfdexx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cCerFha'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vCertiCab['cerfhaxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cDepNum'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vMifCab['depnumxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cCcoIdOc'+'<?php echo $gGrid . $gSecuencia ?>'].value   = '<?php echo $vDeposito['ccoidocx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cCcoFvD'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['ccofvdxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cCcoFvH'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '<?php echo $vDeposito['ccofvhxx'] ?>';
                            </script>
                            <?php
                          }
                        } else { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cCerId'+'<?php echo $gGrid . $gSecuencia ?>'].value     = '';
                            parent.fmwork.document.forms['frgrm']['cCerComCsc'+'<?php echo $gGrid . $gSecuencia ?>'].value = '';
                            parent.fmwork.document.forms['frgrm']['cCerAno'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            parent.fmwork.document.forms['frgrm']['cMifId'+'<?php echo $gGrid . $gSecuencia ?>'].value     = '';
                            parent.fmwork.document.forms['frgrm']['cMifIdAno'+'<?php echo $gGrid . $gSecuencia ?>'].value  = '';
                            parent.fmwork.document.forms['frgrm']['cMifComCsc'+'<?php echo $gGrid . $gSecuencia ?>'].value = '';
                            parent.fmwork.document.forms['frgrm']['cOrvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            parent.fmwork.document.forms['frgrm']['cOfvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            parent.fmwork.document.forms['frgrm']['cOfvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            parent.fmwork.document.forms['frgrm']['cCerFde'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            parent.fmwork.document.forms['frgrm']['cCerFha'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            parent.fmwork.document.forms['frgrm']['cDepNum'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            parent.fmwork.document.forms['frgrm']['cCcoIdOc'+'<?php echo $gGrid . $gSecuencia ?>'].value   = '';
                            parent.fmwork.document.forms['frgrm']['cCcoFvD'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                            parent.fmwork.document.forms['frgrm']['cCcoFvH'+'<?php echo $gGrid . $gSecuencia ?>'].value    = '';
                          </script>
                          <?php
                        }
                      break;
                    }
                  ?>
                </form>
              </fieldset>
            </td>
          </tr>
        </table>
      </center>
    </body>
  </html>
<?php 
} else {
  f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
} 
?>
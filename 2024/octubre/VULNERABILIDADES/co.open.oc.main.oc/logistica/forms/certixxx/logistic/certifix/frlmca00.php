<?php
  namespace openComex;
  /**
   * --- Descripcion: Consulta las MIF:
   * @author Juan Jose Trujillo Ch <juan.trujillo@openits.co>
   * @package openComex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");

?>

<?php 
if ($gModo != "" && $gFunction != "") { ?>
  <html>
    <head>
      <title>Param&eacute;trica de las M.I.F</title>
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
                <legend>Param&eacute;trica de las M.I.F</legend>
                <form name = "frgrm" action = "" method = "post" target = "fmpro">
                  <?php
                    switch ($gModo) {
                      case "WINDOW":
                        // Traigo Datos de la MIF
                        $qMifCab  = "SELECT ";
                        $qMifCab .= "$cAlfa.lmca$cPerAno.* ";
                        $qMifCab .= "FROM $cAlfa.lmca$cPerAno ";
                        $qMifCab .= "WHERE ";
                        $qMifCab .= "$cAlfa.lmca$cPerAno.comcscxx LIKE \"%$gComCsc%\" AND ";
                        $qMifCab .= "$cAlfa.lmca$cPerAno.comprexx = \"$gComPre\" AND ";
                        if ($gCliId != "") {
                          $qMifCab .= "$cAlfa.lmca$cPerAno.cliidxxx = \"$gCliId\" AND ";
                        }
                        $qMifCab .= "$cAlfa.lmca$cPerAno.regestxx IN (\"ACTIVO\",\"CERTIFICADO_PARCIAL\") ";
                        $qMifCab .= "ORDER BY ABS($cAlfa.lmca$cPerAno.comcscxx) ASC ";
                        $xMifCab = f_MySql("SELECT","",$qMifCab,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qMifCab."~".mysql_num_rows($xMifCab));
                        // FIN Traigo Datos de la MIF
                        
                        if ($xMifCab && mysql_num_rows($xMifCab) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "450">
                              <tr>
                                <td width = "050" Class = "name"><center>ID</center></td>
                                <td width = "050" Class = "name"><center>COD</center></td>
                                <td width = "070" Class = "name"><center>PREFIJO</center></td>
                                <td width = "080" Class = "name"><center>M.I.F</center></td>
                                <td width = "150" Class = "name"><center>CONSECUTIVO 2</center></td>
                                <td width = "050" Class = "name"><center>ESTADO</center></td>
                              </tr>
                              <?php 
                              while ($xRMC = mysql_fetch_array($xMifCab)) {
                                // Consulta la información del Depósito
                                $qDeposito  = "SELECT ";
                                $qDeposito .= "lpar0155.depnumxx, ";
                                $qDeposito .= "lpar0155.ccoidocx, ";
                                $qDeposito .= "lpar0007.tdeidxxx, ";
                                $qDeposito .= "lpar0007.tdedesxx, ";
                                $qDeposito .= "lpar0001.orvsapxx, ";
                                $qDeposito .= "lpar0001.orvdesxx, ";
                                $qDeposito .= "lpar0002.ofvsapxx, ";
                                $qDeposito .= "lpar0002.ofvdesxx, ";
                                $qDeposito .= "lpar0003.closapxx, ";
                                $qDeposito .= "lpar0003.clodesxx, ";
                                $qDeposito .= "lpar0009.secsapxx, ";
                                $qDeposito .= "lpar0009.secdesxx, ";
                                $qDeposito .= "lpar0155.regestxx ";
                                $qDeposito .= "FROM $cAlfa.lpar0155 ";                        
                                $qDeposito .= "LEFT JOIN $cAlfa.lpar0007 ON $cAlfa.lpar0155.tdeidxxx = $cAlfa.lpar0007.tdeidxxx ";
                                $qDeposito .= "LEFT JOIN $cAlfa.lpar0001 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0001.orvsapxx ";
                                $qDeposito .= "LEFT JOIN $cAlfa.lpar0002 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0002.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0002.ofvsapxx ";
                                $qDeposito .= "LEFT JOIN $cAlfa.lpar0003 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0003.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0003.ofvsapxx AND $cAlfa.lpar0155.closapxx = $cAlfa.lpar0003.closapxx ";
                                $qDeposito .= "LEFT JOIN $cAlfa.lpar0009 ON $cAlfa.lpar0155.secsapxx = $cAlfa.lpar0009.secsapxx ";
                                $qDeposito .= "WHERE ";
                                $qDeposito .= "lpar0155.depnumxx = \"{$xRMC['depnumxx']}\" AND ";
                                $qDeposito .= "lpar0155.regestxx = \"ACTIVO\" ";
                                $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
                                $vDeposito = array();
                                if (mysql_num_rows($xDeposito) > 0) {
                                  $vDeposito = mysql_fetch_array($xDeposito);
                                }

                                if (mysql_num_rows($xMifCab) > 1) { ?>
                                  <tr>
                                    <td width = "050" class= "name"> <?php echo $xRMC['comidxxx'] ?></td>
                                    <td width = "050" class= "name"> <?php echo $xRMC['comcodxx'] ?></td>
                                    <td width = "070" class= "name"> <?php echo $xRMC['comprexx'] ?></td>
                                    <td width = "100" class= "name">
                                      <a href = "javascript:window.opener.document.forms['frgrm']['cMifId'].value      = '<?php echo $xRMC['mifidxxx']?>';
                                                            window.opener.document.forms['frgrm']['cMifComId'].value   = '<?php echo $xRMC['comidxxx']?>';
                                                            window.opener.document.forms['frgrm']['cMifComCod'].value  = '<?php echo $xRMC['comcodxx']?>';
                                                            window.opener.document.forms['frgrm']['cMifComCsc'].value  = '<?php echo $xRMC['comcscxx']?>';
                                                            window.opener.document.forms['frgrm']['cMifComCsc2'].value = '<?php echo $xRMC['comcsc2x']?>';
                                                            if ('<?php echo $gOrigen ?>' == 'NUEVO') {
                                                              window.opener.document.forms['frgrm']['dVigDesde'].value = '<?php echo $xRMC['miffdexx']?>';
                                                              window.opener.document.forms['frgrm']['dVigHasta'].value = '<?php echo $xRMC['miffhaxx']?>';
                                                              window.opener.document.forms['frgrm']['cDepNum'].value   = '<?php echo $xRMC['depnumxx']?>';
                                                              window.opener.document.forms['frgrm']['cDepNum_hidd'].value = '<?php echo $xRMC['depnumxx']?>';
                                                              window.opener.document.forms['frgrm']['cCcoIdOc'].value  = '<?php echo $vDeposito['ccoidocx']?>';
                                                              window.opener.document.forms['frgrm']['cTipoDep'].value  = '<?php echo $vDeposito['tdedesxx']?>';
                                                              window.opener.document.forms['frgrm']['cOrvSap'].value   = '<?php echo $vDeposito['orvsapxx']?>';
                                                              window.opener.document.forms['frgrm']['cOrvDes'].value   = '<?php echo $vDeposito['orvdesxx']?>';
                                                              window.opener.document.forms['frgrm']['cOfvSap'].value   = '<?php echo $vDeposito['ofvsapxx']?>';
                                                              window.opener.document.forms['frgrm']['cOfvDes'].value   = '<?php echo $vDeposito['ofvdesxx']?>';
                                                              window.opener.document.forms['frgrm']['cCloSap'].value   = '<?php echo $vDeposito['closapxx']?>';
                                                              window.opener.document.forms['frgrm']['cCloDes'].value   = '<?php echo $vDeposito['clodesxx']?>';
                                                              window.opener.document.forms['frgrm']['cSecSap'].value   = '<?php echo $vDeposito['secsapxx']?>';
                                                              window.opener.document.forms['frgrm']['cSecDes'].value   = '<?php echo $vDeposito['secdesxx']?>';
                                                              window.opener.fnHabilitaServicios();
                                                            }
                                                            window.opener.fnLinks('<?php echo $gFunction ?>','EXACT',0);
                                                            window.close();">
                                                            <?php echo $xRMC['comcscxx'] ?>
                                      </a>
                                    </td>
                                    <td width = "050" class= "name"> <?php echo $xRMC['comcsc2x'] ?></td>
                                    <td width = "050" class= "name"> <?php echo $xRMC['regestxx'] ?></td>
                                  </tr>
                                <?php
                                }else{ ?>
                                  <script language="javascript">
                                    window.opener.document.forms['frgrm']['cMifId'].value      = '<?php echo $xRMC['mifidxxx'] ?>';
                                    window.opener.document.forms['frgrm']['cMifComId'].value   = '<?php echo $xRMC['comidxxx'] ?>';
                                    window.opener.document.forms['frgrm']['cMifComCod'].value  = '<?php echo $xRMC['comcodxx'] ?>';
                                    window.opener.document.forms['frgrm']['cMifComCsc'].value  = '<?php echo $xRMC['comcscxx'] ?>';
                                    window.opener.document.forms['frgrm']['cMifComCsc2'].value = '<?php echo $xRMC['comcsc2x'] ?>';
                                    if ('<?php echo $gOrigen ?>' == 'NUEVO') {
                                      window.opener.document.forms['frgrm']['dVigDesde'].value    = '<?php echo $xRMC['miffdexx'] ?>';
                                      window.opener.document.forms['frgrm']['dVigHasta'].value    = '<?php echo $xRMC['miffhaxx'] ?>';
                                      window.opener.document.forms['frgrm']['cDepNum'].value      = '<?php echo $xRMC['depnumxx'] ?>';
                                      window.opener.document.forms['frgrm']['cDepNum_hidd'].value = '<?php echo $xRMC['depnumxx'] ?>';
                                      window.opener.document.forms['frgrm']['cCcoIdOc'].value     = '<?php echo $vDeposito['ccoidocx'] ?>';
                                      window.opener.document.forms['frgrm']['cTipoDep'].value     = '<?php echo $vDeposito['tdedesxx'] ?>';
                                      window.opener.document.forms['frgrm']['cOrvSap'].value      = '<?php echo $vDeposito['orvsapxx'] ?>';
                                      window.opener.document.forms['frgrm']['cOrvDes'].value      = '<?php echo $vDeposito['orvdesxx'] ?>';
                                      window.opener.document.forms['frgrm']['cOfvSap'].value      = '<?php echo $vDeposito['ofvsapxx'] ?>';
                                      window.opener.document.forms['frgrm']['cOfvDes'].value      = '<?php echo $vDeposito['ofvdesxx'] ?>';
                                      window.opener.document.forms['frgrm']['cCloSap'].value      = '<?php echo $vDeposito['closapxx'] ?>';
                                      window.opener.document.forms['frgrm']['cCloDes'].value      = '<?php echo $vDeposito['clodesxx'] ?>';
                                      window.opener.document.forms['frgrm']['cSecSap'].value      = '<?php echo $vDeposito['secsapxx'] ?>';
                                      window.opener.document.forms['frgrm']['cSecDes'].value      = '<?php echo $vDeposito['secdesxx'] ?>';
                                      window.opener.fnHabilitaServicios();
                                    }
                                    window.close();
                                  </script>
                                <?php 
                                }
                              } ?>
                            </table>
                          </center>
                        <?php
                        } else {
                          f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); ?>
                          <script language="javascript">
                            window.opener.document.forms['frgrm']['cMifId'].value      = '';
                            window.opener.document.forms['frgrm']['cMifComId'].value   = '';
                            window.opener.document.forms['frgrm']['cMifComCod'].value  = '';
                            window.opener.document.forms['frgrm']['cMifComCsc'].value  = '';
                            window.opener.document.forms['frgrm']['cMifComCsc2'].value = '';
                            if ('<?php echo $gOrigen ?>' == 'NUEVO') {
                              window.opener.document.forms['frgrm']['dVigDesde'].value    = '';
                              window.opener.document.forms['frgrm']['dVigHasta'].value    = '';
                              window.opener.document.forms['frgrm']['cDepNum'].value      = '';
                              window.opener.document.forms['frgrm']['cDepNum_hidd'].value = '';
                              window.opener.document.forms['frgrm']['cCcoIdOc'].value     = '';
                              window.opener.document.forms['frgrm']['cTipoDep'].value     = '';
                              window.opener.document.forms['frgrm']['cOrvSap'].value      = '';
                              window.opener.document.forms['frgrm']['cOrvDes'].value      = '';
                              window.opener.document.forms['frgrm']['cOfvSap'].value      = '';
                              window.opener.document.forms['frgrm']['cOfvDes'].value      = '';
                              window.opener.document.forms['frgrm']['cCloSap'].value      = '';
                              window.opener.document.forms['frgrm']['cCloDes'].value      = '';
                              window.opener.document.forms['frgrm']['cSecSap'].value      = '';
                              window.opener.document.forms['frgrm']['cSecDes'].value      = '';
                            }
                            window.close();
                          </script>
                        <?php 
                        }
                      break;
                      case "VALID":
                        // Traigo Datos de la MIF
                        $qMifCab  = "SELECT ";
                        $qMifCab .= "$cAlfa.lmca$cPerAno.* ";
                        $qMifCab .= "FROM $cAlfa.lmca$cPerAno ";
                        $qMifCab .= "WHERE ";
                        $qMifCab .= "$cAlfa.lmca$cPerAno.comcscxx LIKE \"%$gComCsc%\" AND ";
                        $qMifCab .= "$cAlfa.lmca$cPerAno.comprexx = \"$gComPre\" AND ";
                        if ($gCliId != "") {
                          $qMifCab .= "$cAlfa.lmca$cPerAno.cliidxxx = \"$gCliId\" AND ";
                        }
                        $qMifCab .= "$cAlfa.lmca$cPerAno.regestxx IN (\"ACTIVO\",\"CERTIFICADO_PARCIAL\") ";
                        $xMifCab  = f_MySql("SELECT","",$qMifCab,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qMifCab."~".mysql_num_rows($xMifCab));
                        $vMifCab  = mysql_fetch_array($xMifCab);
                        if (mysql_num_rows($xMifCab) > 0){
                          if (mysql_num_rows($xMifCab) == 1){ 
                            // Consulta la información del Depósito
                            $qDeposito  = "SELECT ";
                            $qDeposito .= "lpar0155.depnumxx, ";
                            $qDeposito .= "lpar0155.ccoidocx, ";
                            $qDeposito .= "lpar0007.tdeidxxx, ";
                            $qDeposito .= "lpar0007.tdedesxx, ";
                            $qDeposito .= "lpar0001.orvsapxx, ";
                            $qDeposito .= "lpar0001.orvdesxx, ";
                            $qDeposito .= "lpar0002.ofvsapxx, ";
                            $qDeposito .= "lpar0002.ofvdesxx, ";
                            $qDeposito .= "lpar0003.closapxx, ";
                            $qDeposito .= "lpar0003.clodesxx, ";
                            $qDeposito .= "lpar0009.secsapxx, ";
                            $qDeposito .= "lpar0009.secdesxx, ";
                            $qDeposito .= "lpar0155.regestxx ";
                            $qDeposito .= "FROM $cAlfa.lpar0155 ";                        
                            $qDeposito .= "LEFT JOIN $cAlfa.lpar0007 ON $cAlfa.lpar0155.tdeidxxx = $cAlfa.lpar0007.tdeidxxx ";
                            $qDeposito .= "LEFT JOIN $cAlfa.lpar0001 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0001.orvsapxx ";
                            $qDeposito .= "LEFT JOIN $cAlfa.lpar0002 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0002.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0002.ofvsapxx ";
                            $qDeposito .= "LEFT JOIN $cAlfa.lpar0003 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0003.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0003.ofvsapxx AND $cAlfa.lpar0155.closapxx = $cAlfa.lpar0003.closapxx ";
                            $qDeposito .= "LEFT JOIN $cAlfa.lpar0009 ON $cAlfa.lpar0155.secsapxx = $cAlfa.lpar0009.secsapxx ";
                            $qDeposito .= "WHERE ";
                            $qDeposito .= "lpar0155.depnumxx = \"{$vMifCab['depnumxx']}\" AND ";
                            $qDeposito .= "lpar0155.regestxx = \"ACTIVO\" ";
                            $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
                            $vDeposito = array();
                            if (mysql_num_rows($xDeposito) > 0) {
                              $vDeposito = mysql_fetch_array($xDeposito);
                            }
                            ?>
                            <script language = "javascript">
                              parent.fmwork.document.forms['frgrm']['cMifId'].value      = '<?php echo $vMifCab['mifidxxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cMifComId'].value   = '<?php echo $vMifCab['comidxxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cMifComCod'].value  = '<?php echo $vMifCab['comcodxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cMifComCsc'].value  = '<?php echo $vMifCab['comcscxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cMifComCsc2'].value = '<?php echo $vMifCab['comcsc2x'] ?>';
                              if ('<?php echo $gOrigen ?>' == 'NUEVO') {
                                parent.fmwork.document.forms['frgrm']['dVigDesde'].value    = '<?php echo $vMifCab['miffdexx'] ?>';
                                parent.fmwork.document.forms['frgrm']['dVigHasta'].value    = '<?php echo $vMifCab['miffhaxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cDepNum'].value      = '<?php echo $vMifCab['depnumxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cDepNum_hidd'].value = '<?php echo $vMifCab['depnumxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cCcoIdOc'].value     = '<?php echo $vDeposito['ccoidocx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cTipoDep'].value     = '<?php echo $vDeposito['tdedesxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cOrvSap'].value      = '<?php echo $vDeposito['orvsapxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cOrvDes'].value      = '<?php echo $vDeposito['orvdesxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cOfvSap'].value      = '<?php echo $vDeposito['ofvsapxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cOfvDes'].value      = '<?php echo $vDeposito['ofvdesxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cCloSap'].value      = '<?php echo $vDeposito['closapxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cCloDes'].value      = '<?php echo $vDeposito['clodesxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cSecSap'].value      = '<?php echo $vDeposito['secsapxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cSecDes'].value      = '<?php echo $vDeposito['secdesxx'] ?>';
                                parent.fmwork.fnHabilitaServicios();
                              }
                            </script>
                          <?php
                          }else{ ?>
                            <script language = "javascript">
                              parent.fmwork.fnLinks('<?php echo $gFunction ?>','WINDOW');
                            </script>
                          <?php
                          }
                        }else{ 
                          f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); 
                        }
                      break;
                      case "EXACT":
                        // Traigo Datos de la MIF
                        $qMifCab  = "SELECT ";
                        $qMifCab .= "$cAlfa.lmca$cPerAno.* ";
                        $qMifCab .= "FROM $cAlfa.lmca$cPerAno ";
                        $qMifCab .= "WHERE ";
                        $qMifCab .= "$cAlfa.lmca$cPerAno.comidxxx = \"M\" AND ";
                        $qMifCab .= "$cAlfa.lmca$cPerAno.comcodxx = \"$gComCod\" AND ";
                        $qMifCab .= "$cAlfa.lmca$cPerAno.comcscxx = \"$gComCsc\" AND ";
                        $qMifCab .= "$cAlfa.lmca$cPerAno.comcsc2x = \"$gComCsc2\" AND ";
                        $qMifCab .= "$cAlfa.lmca$cPerAno.comprexx = \"$gComPre\" AND ";
                        if ($gCliId != "") {
                          $qMifCab .= "$cAlfa.lmca$cPerAno.cliidxxx = \"$gCliId\" AND ";
                        }
                        $qMifCab .= "$cAlfa.lmca$cPerAno.regestxx IN (\"ACTIVO\",\"CERTIFICADO_PARCIAL\") LIMIT 0,1 ";
                        $xMifCab  = f_MySql("SELECT","",$qMifCab,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qMifCab."~".mysql_num_rows($xMifCab));
                        $vMifCab  = mysql_fetch_array($xMifCab);
                        if (mysql_num_rows($xMifCab) == 1) { 
                          // Consulta la información del Depósito
                          $qDeposito  = "SELECT ";
                          $qDeposito .= "lpar0155.depnumxx, ";
                          $qDeposito .= "lpar0155.ccoidocx, ";
                          $qDeposito .= "lpar0007.tdeidxxx, ";
                          $qDeposito .= "lpar0007.tdedesxx, ";
                          $qDeposito .= "lpar0001.orvsapxx, ";
                          $qDeposito .= "lpar0001.orvdesxx, ";
                          $qDeposito .= "lpar0002.ofvsapxx, ";
                          $qDeposito .= "lpar0002.ofvdesxx, ";
                          $qDeposito .= "lpar0003.closapxx, ";
                          $qDeposito .= "lpar0003.clodesxx, ";
                          $qDeposito .= "lpar0009.secsapxx, ";
                          $qDeposito .= "lpar0009.secdesxx, ";
                          $qDeposito .= "lpar0155.regestxx ";
                          $qDeposito .= "FROM $cAlfa.lpar0155 ";                        
                          $qDeposito .= "LEFT JOIN $cAlfa.lpar0007 ON $cAlfa.lpar0155.tdeidxxx = $cAlfa.lpar0007.tdeidxxx ";
                          $qDeposito .= "LEFT JOIN $cAlfa.lpar0001 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0001.orvsapxx ";
                          $qDeposito .= "LEFT JOIN $cAlfa.lpar0002 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0002.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0002.ofvsapxx ";
                          $qDeposito .= "LEFT JOIN $cAlfa.lpar0003 ON $cAlfa.lpar0155.orvsapxx = $cAlfa.lpar0003.orvsapxx AND $cAlfa.lpar0155.ofvsapxx = $cAlfa.lpar0003.ofvsapxx AND $cAlfa.lpar0155.closapxx = $cAlfa.lpar0003.closapxx ";
                          $qDeposito .= "LEFT JOIN $cAlfa.lpar0009 ON $cAlfa.lpar0155.secsapxx = $cAlfa.lpar0009.secsapxx ";
                          $qDeposito .= "WHERE ";
                          $qDeposito .= "lpar0155.depnumxx = \"{$vMifCab['depnumxx']}\" AND ";
                          $qDeposito .= "lpar0155.regestxx = \"ACTIVO\" ";
                          $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
                          $vDeposito = array();
                          if (mysql_num_rows($xDeposito) > 0) {
                            $vDeposito = mysql_fetch_array($xDeposito);
                          }
                          ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cMifId'].value      = '<?php echo $vMifCab['mifidxxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cMifComId'].value   = '<?php echo $vMifCab['comidxxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cMifComCod'].value  = '<?php echo $vMifCab['comcodxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cMifComCsc'].value  = '<?php echo $vMifCab['comcscxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cMifComCsc2'].value = '<?php echo $vMifCab['comcsc2x'] ?>';
                            if ('<?php echo $gOrigen ?>' == 'NUEVO') {
                              parent.fmwork.document.forms['frgrm']['dVigDesde'].value    = '<?php echo $vMifCab['miffdexx'] ?>';
                              parent.fmwork.document.forms['frgrm']['dVigHasta'].value    = '<?php echo $vMifCab['miffhaxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cDepNum'].value      = '<?php echo $vMifCab['depnumxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cDepNum_hidd'].value = '<?php echo $vMifCab['depnumxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cCcoIdOc'].value     = '<?php echo $vDeposito['ccoidocx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cTipoDep'].value     = '<?php echo $vDeposito['tdedesxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cOrvSap'].value      = '<?php echo $vDeposito['orvsapxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cOrvDes'].value      = '<?php echo $vDeposito['orvdesxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cOfvSap'].value      = '<?php echo $vDeposito['ofvsapxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cOfvDes'].value      = '<?php echo $vDeposito['ofvdesxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cCloSap'].value      = '<?php echo $vDeposito['closapxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cCloDes'].value      = '<?php echo $vDeposito['clodesxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cSecSap'].value      = '<?php echo $vDeposito['secsapxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cSecDes'].value      = '<?php echo $vDeposito['secdesxx'] ?>';
                              parent.fmwork.fnHabilitaServicios();

                            }
                          </script>
                        <?php
                        } else { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cMifId'].value      = '';
                            parent.fmwork.document.forms['frgrm']['cMifComId'].value   = '';
                            parent.fmwork.document.forms['frgrm']['cMifComCod'].value  = '';
                            parent.fmwork.document.forms['frgrm']['cMifComCsc'].value  = '';
                            parent.fmwork.document.forms['frgrm']['cMifComCsc2'].value = '';
                            if ('<?php echo $gOrigen ?>' == 'NUEVO') {
                              parent.fmwork.document.forms['frgrm']['dVigDesde'].value    = '';
                              parent.fmwork.document.forms['frgrm']['dVigHasta'].value    = '';
                              parent.fmwork.document.forms['frgrm']['cDepNum'].value      = '';
                              parent.fmwork.document.forms['frgrm']['cDepNum_hidd'].value = '';
                              parent.fmwork.document.forms['frgrm']['cCcoIdOc'].value     = '';
                              parent.fmwork.document.forms['frgrm']['cTipoDep'].value     = '';
                              parent.fmwork.document.forms['frgrm']['cOrvSap'].value      = '';
                              parent.fmwork.document.forms['frgrm']['cOrvDes'].value      = '';
                              parent.fmwork.document.forms['frgrm']['cOfvSap'].value      = '';
                              parent.fmwork.document.forms['frgrm']['cOfvDes'].value      = '';
                              parent.fmwork.document.forms['frgrm']['cCloSap'].value      = '';
                              parent.fmwork.document.forms['frgrm']['cCloDes'].value      = '';
                              parent.fmwork.document.forms['frgrm']['cSecSap'].value      = '';
                              parent.fmwork.document.forms['frgrm']['cSecDes'].value      = '';
                            }
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
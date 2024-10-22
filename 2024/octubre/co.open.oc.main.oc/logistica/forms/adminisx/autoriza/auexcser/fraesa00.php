<?php
  namespace openComex;
  /**
   * --- Descripcion: Consulta las Certificaciones:
   * @author Cristian Stiven Perdomo Garcia. <cristian.perdomo@openits.co>
   * @package openComex
   * @version 001
   */

include("../../../../../financiero/libs/php/utility.php");

$cAnioIni = (date('Y') - 2);

if ($gModo != "" && $gFunction != "") { ?>
  <html>
    <head>
      <title>Certificaci&oacute;n</title>
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
                <legend>Certificaci&oacute;n</legend>
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
                                <td width = "120" Class = "name"><center>&nbsp;</center></td>
                                <!-- <td width = "010" Class = "name"><center>&nbsp;</center></td> -->
                              </tr>
                              <?php 
                              for ($i=0; $i < count($mDatos); $i++) { 
                                ?>
                                <tr>
                                  <td width = "050" class= "name">
                                    
                                    <a href = "javascript:window.opener.document.forms['frgrm']['vCerIds'+'<?php echo $gSecuencia ?>'].value    = '<?php echo $mDatos[$i]['ceridxxx']?>';
                                                          window.opener.document.forms['frgrm']['vAnio'+'<?php echo $gSecuencia ?>'].value      = '<?php echo substr($mDatos[$i]['comfecxx'], 0, 4)?>';
                                                          window.opener.document.forms['frgrm']['cCerComCsc'+'<?php echo $gSecuencia ?>'].value = '<?php echo $mDatos[$i]['comidxxx']."-".$mDatos[$i]['comprexx']."-".$mDatos[$i]['comcscxx'] ?>';
                                                          window.close();">
                                                          <?php echo $mDatos[$i]['comidxxx']."-".$mDatos[$i]['comprexx']."-".$mDatos[$i]['comcscxx'] ?>
                                    </a>
                                  </td>
                                  <td width = "120" class= "name"> <?php echo $mDatos[$i]['clinomxx'] ?></td>
                                </tr>
                                <?php 
                              } ?>
                            </table>
                          </center>
                          <?php
                        } else {
                          f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros."); ?>
                          <script language="javascript">
                            window.opener.document.forms['frgrm']['cCerComCsc'+'<?php echo $gSecuencia ?>'].value = '';
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
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm']['cCerComCsc'+'<?php echo $gSecuencia ?>'].value = '<?php echo $mDatos[$i]['comidxxx']."-".$mDatos[$i]['comprexx']."-".$mDatos[$i]['comcscxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['vCerIds'+'<?php echo $gSecuencia ?>'].value = '<?php echo $mDatos[$i]['ceridxxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['vAnio'+'<?php echo $gSecuencia ?>'].value = '<?php echo substr($mDatos[$i]['comfecxx'], 0, 4) ?>';
                              </script>
                              <?php
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
                          ?>
                            <script language = "javascript">
                              parent.fmwork.document.forms['frgrm']['cCerComCsc'+'<?php echo $gSecuencia ?>'].value = '<?php echo $vCertiCab['comidxxx']."-".$vCertiCab['comprexx']."-".$vCertiCab['comcscxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['vCerIds'+'<?php echo $gSecuencia ?>'].value = '<?php echo $vCertiCab['ceridxxx']?>';
                              parent.fmwork.document.forms['frgrm']['vAnio'+'<?php echo $gSecuencia ?>'].value = '<?php echo substr($vCertiCab['comfecxx'], 0, 4) ?>';
                            </script>
                            <?php
                        } else { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cCerComCsc'+'<?php echo $gSecuencia ?>'].value = '';
                            parent.fmwork.document.forms['frgrm']['vCerIds'+'<?php echo $gSecuencia ?>'].value = '';
                            parent.fmwork.document.forms['frgrm']['vAnio'+'<?php echo $gSecuencia ?>'].value = '';
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
<?php
  namespace openComex;
  /**
   * --- Descripcion: Consulta las Certificaciones:
   * @author Cristian Perdomo <cristian.perdomo@openits.co>
   * @package openComex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");

?>

<?php 
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
                        $qCertiCab  = "SELECT ";
                        $qCertiCab .= "ceridxxx,";
                        $qCertiCab .= "comidxxx,";
                        $qCertiCab .= "comcodxx,";
                        $qCertiCab .= "comprexx,";
                        $qCertiCab .= "comcscxx,";
                        $qCertiCab .= "comcsc2x,";
                        $qCertiCab .= "comfecxx ";
                        $qCertiCab .= "FROM $cAlfa.lcca$cPerAno ";
                        $qCertiCab .= "WHERE ";
                        $qCertiCab .= "$cAlfa.lcca$cPerAno.comcscxx LIKE \"%$gComCsc%\" AND ";
                        if ($gCliId != "") {
                          $qCertiCab .= "$cAlfa.lcca$cPerAno.cliidxxx = \"$gCliId\" AND ";
                        }
                        $qCertiCab .= "$cAlfa.lcca$cPerAno.regestxx IN (\"CERTIFICADO\",\"PREFACTURADO_PARCIAL\",\"FACTURADO_PARCIAL\") ";
                        $qCertiCab .= "ORDER BY ABS($cAlfa.lcca$cPerAno.comcscxx) ASC ";
                        $xCertiCab = f_MySql("SELECT","",$qCertiCab,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qCertiCab."~".mysql_num_rows($xCertiCab));
                        // FIN Traigo Datos de la Certificacion
                        
                        if ($xCertiCab && mysql_num_rows($xCertiCab) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "450">
                              <tr>
                                <td width = "050" Class = "name"><center>ID</center></td>
                                <td width = "050" Class = "name"><center>COD</center></td>
                                <td width = "070" Class = "name"><center>PREFIJO</center></td>
                                <td width = "080" Class = "name"><center>CERTIFICACION</center></td>
                                <td width = "150" Class = "name"><center>CONSECUTIVO 2</center></td>
                                <td width = "050" Class = "name"><center>ESTADO</center></td>
                              </tr>
                              <?php 
                              while ($xRMC = mysql_fetch_array($xCertiCab)) {
                                if (mysql_num_rows($xCertiCab) > 1) { ?>
                                  <tr>
                                    <td width = "050" class= "name"> <?php echo $xRMC['comidxxx'] ?></td>
                                    <td width = "050" class= "name"> <?php echo $xRMC['comcodxx'] ?></td>
                                    <td width = "070" class= "name"> <?php echo $xRMC['comprexx'] ?></td>
                                    <td width = "100" class= "name">
                                      <a href = "javascript:window.opener.document.forms['frgrm']['cCerId'].value      = '<?php echo $xRMC['ceridxxx']?>';
                                                            window.opener.document.forms['frgrm']['cCerComId'].value   = '<?php echo $xRMC['comidxxx']?>';
                                                            window.opener.document.forms['frgrm']['cCerComCod'].value  = '<?php echo $xRMC['comcodxx']?>';
                                                            window.opener.document.forms['frgrm']['cCerComCsc'].value  = '<?php echo $xRMC['comcscxx']?>';
                                                            window.opener.document.forms['frgrm']['cCerComCsc2'].value = '<?php echo $xRMC['comcsc2x']?>';
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
                                    window.opener.document.forms['frgrm']['cCerId'].value      = '<?php echo $xRMC['ceridxxx'] ?>';
                                    window.opener.document.forms['frgrm']['cCerComId'].value   = '<?php echo $xRMC['comidxxx'] ?>';
                                    window.opener.document.forms['frgrm']['cCerComCod'].value  = '<?php echo $xRMC['comcodxx'] ?>';
                                    window.opener.document.forms['frgrm']['cCerComCsc'].value  = '<?php echo $xRMC['comcscxx'] ?>';
                                    window.opener.document.forms['frgrm']['cCerComCsc2'].value = '<?php echo $xRMC['comcsc2x'] ?>';
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
                            window.opener.document.forms['frgrm']['cCerId'].value      = '';
                            window.opener.document.forms['frgrm']['cCerComId'].value   = '';
                            window.opener.document.forms['frgrm']['cCerComCod'].value  = '';
                            window.opener.document.forms['frgrm']['cCerComCsc'].value  = '';
                            window.opener.document.forms['frgrm']['cCerComCsc2'].value = '';
                            window.close();
                          </script>
                        <?php 
                        }
                      break;
                      case "VALID":
                        // Traigo Datos de la Certificacion
                        $qCertiCab  = "SELECT ";
                        $qCertiCab .= "ceridxxx,";
                        $qCertiCab .= "comidxxx,";
                        $qCertiCab .= "comcodxx,";
                        $qCertiCab .= "comprexx,";
                        $qCertiCab .= "comcscxx,";
                        $qCertiCab .= "comcsc2x,";
                        $qCertiCab .= "comfecxx ";
                        $qCertiCab .= "FROM $cAlfa.lcca$cPerAno ";
                        $qCertiCab .= "WHERE ";
                        $qCertiCab .= "$cAlfa.lcca$cPerAno.comcscxx LIKE \"%$gComCsc%\" AND ";
                        if ($gCliId != "") {
                          $qCertiCab .= "$cAlfa.lcca$cPerAno.cliidxxx = \"$gCliId\" AND ";
                        }
                        $qCertiCab .= "$cAlfa.lcca$cPerAno.regestxx IN (\"CERTIFICADO\",\"PREFACTURADO_PARCIAL\",\"FACTURADO_PARCIAL\") ";
                        $xCertiCab  = f_MySql("SELECT","",$qCertiCab,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qCertiCab."~".mysql_num_rows($xCertiCab));
                        if (mysql_num_rows($xCertiCab) == 1){ 
                          $vCertiCab  = mysql_fetch_array($xCertiCab);
                          ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cCerId'].value      = '<?php echo $vCertiCab['ceridxxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cCerComId'].value   = '<?php echo $vCertiCab['comidxxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cCerComCod'].value  = '<?php echo $vCertiCab['comcodxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cCerComCsc'].value  = '<?php echo $vCertiCab['comcscxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cCerComCsc2'].value = '<?php echo $vCertiCab['comcsc2x'] ?>';
                          </script>
                        <?php
                        }else{ ?>
                          <script language = "javascript">
                            parent.fmwork.fnLinks('<?php echo $gFunction ?>','WINDOW');
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
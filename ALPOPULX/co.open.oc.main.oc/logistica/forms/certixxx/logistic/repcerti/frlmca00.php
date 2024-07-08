<?php
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
                        $qMifCab .= "$cAlfa.lmca$gPerAno.* ";
                        $qMifCab .= "FROM $cAlfa.lmca$gPerAno ";
                        $qMifCab .= "WHERE ";
                        $qMifCab .= "$cAlfa.lmca$gPerAno.comcscxx LIKE \"%$gComCsc%\" AND ";
                        if ($gCliId != "") {
                          $qMifCab .= "$cAlfa.lmca$gPerAno.cliidxxx = \"$gCliId\" AND ";
                        }
                        $qMifCab .= "$cAlfa.lmca$gPerAno.regestxx IN (\"ACTIVO\",\"CERTIFICADO_PARCIAL\") ";
                        $qMifCab .= "ORDER BY ABS($cAlfa.lmca$gPerAno.comcscxx) ASC ";
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
                                                            window.opener.fnLinks('<?php echo $gFunction ?>','EXACT',0);
                                                            window.close();">
                                                            <?php echo $xRMC['comcscxx'] ?>
                                      </a>
                                    </td>
                                    <td width = "100" class= "name"> <?php echo $xRMC['comcsc2x'] ?></td>
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
                            window.close();
                          </script>
                        <?php 
                        }
                      break;
                      case "VALID":
                        // Traigo Datos de la MIF
                        $qMifCab  = "SELECT ";
                        $qMifCab .= "$cAlfa.lmca$gPerAno.* ";
                        $qMifCab .= "FROM $cAlfa.lmca$gPerAno ";
                        $qMifCab .= "WHERE ";
                        $qMifCab .= "$cAlfa.lmca$gPerAno.comcscxx LIKE \"%$gComCsc%\" AND ";
                        if ($gCliId != "") {
                          $qMifCab .= "$cAlfa.lmca$gPerAno.cliidxxx = \"$gCliId\" AND ";
                        }
                        $qMifCab .= "$cAlfa.lmca$gPerAno.regestxx IN (\"ACTIVO\",\"CERTIFICADO_PARCIAL\") ";
                        $xMifCab  = f_MySql("SELECT","",$qMifCab,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qMifCab."~".mysql_num_rows($xMifCab));
                        $vMifCab  = mysql_fetch_array($xMifCab);
                        if (mysql_num_rows($xMifCab) > 0){
                          if (mysql_num_rows($xMifCab) == 1){ 
                            ?>
                            <script language = "javascript">
                              parent.fmwork.document.forms['frgrm']['cMifId'].value      = '<?php echo $vMifCab['mifidxxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cMifComId'].value   = '<?php echo $vMifCab['comidxxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cMifComCod'].value  = '<?php echo $vMifCab['comcodxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cMifComCsc'].value  = '<?php echo $vMifCab['comcscxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cMifComCsc2'].value = '<?php echo $vMifCab['comcsc2x'] ?>';
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
                        $qMifCab .= "$cAlfa.lmca$gPerAno.* ";
                        $qMifCab .= "FROM $cAlfa.lmca$gPerAno ";
                        $qMifCab .= "WHERE ";
                        $qMifCab .= "$cAlfa.lmca$gPerAno.comidxxx = \"M\" AND ";
                        $qMifCab .= "$cAlfa.lmca$gPerAno.comcodxx = \"$gComCod\" AND ";
                        $qMifCab .= "$cAlfa.lmca$gPerAno.comcscxx = \"$gComCsc\" AND ";
                        $qMifCab .= "$cAlfa.lmca$gPerAno.comcsc2x = \"$gComCsc2\" AND ";
                        if ($gCliId != "") {
                          $qMifCab .= "$cAlfa.lmca$gPerAno.cliidxxx = \"$gCliId\" AND ";
                        }
                        $qMifCab .= "$cAlfa.lmca$gPerAno.regestxx IN (\"ACTIVO\",\"CERTIFICADO_PARCIAL\") LIMIT 0,1 ";
                        $xMifCab  = f_MySql("SELECT","",$qMifCab,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qMifCab."~".mysql_num_rows($xMifCab));
                        $vMifCab  = mysql_fetch_array($xMifCab);
                        if (mysql_num_rows($xMifCab) == 1) { 
                          ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cMifId'].value      = '<?php echo $vMifCab['mifidxxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cMifComId'].value   = '<?php echo $vMifCab['comidxxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cMifComCod'].value  = '<?php echo $vMifCab['comcodxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cMifComCsc'].value  = '<?php echo $vMifCab['comcscxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cMifComCsc2'].value = '<?php echo $vMifCab['comcsc2x'] ?>';
                          </script>
                        <?php
                        } else { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cMifId'].value      = '';
                            parent.fmwork.document.forms['frgrm']['cMifComId'].value   = '';
                            parent.fmwork.document.forms['frgrm']['cMifComCod'].value  = '';
                            parent.fmwork.document.forms['frgrm']['cMifComCsc'].value  = '';
                            parent.fmwork.document.forms['frgrm']['cMifComCsc2'].value = '';
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
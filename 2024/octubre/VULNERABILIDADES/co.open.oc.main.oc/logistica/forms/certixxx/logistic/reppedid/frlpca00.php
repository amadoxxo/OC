<?php
  namespace openComex;
  /**
   * --- Descripcion: Consulta Pedido:
   * @author Elian Amado Ramirez <elian.amado@openits.co>
   * @package openComex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");

?>

<?php 
if ($gModo != "" && $gFunction != "") { ?>
  <html>
    <head>
      <title>Param&eacute;trica de Pedido</title>
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
                <legend>Param&eacute;trica de Pedido</legend>
                <form name = "frgrm" action = "" method = "post" target = "fmpro">
                  <?php
                    switch ($gModo) {
                      case "WINDOW":
                        // Traigo Datos del Pedido
                        $qPedCab  = "SELECT ";
                        $qPedCab .= "$cAlfa.lpca$gPerAno.* ";
                        $qPedCab .= "FROM $cAlfa.lpca$gPerAno ";
                        $qPedCab .= "WHERE ";
                        if ($gFunction == "cPedComCsc") {
                          $qPedCab .= "$cAlfa.lpca$gPerAno.comcscxx LIKE \"%$gComCsc%\" AND ";
                        }
                        $qPedCab .= "$cAlfa.lpca$gPerAno.cliidxxx = \"$gCliId\" AND ";
                        $qPedCab .= "$cAlfa.lpca$gPerAno.regestxx IN (\"PROVISIONAL\",\"ACTIVO\", \"FACTURADO\", \"ANULADO\", \"RECHAZADO\") ";
                        $qPedCab .= "ORDER BY ABS($cAlfa.lpca$gPerAno.comcscxx) ASC ";
                        $xPedCab = f_MySql("SELECT","",$qPedCab,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qPedCab."~".mysql_num_rows($xPedCab));
                        if ($xPedCab && mysql_num_rows($xPedCab) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "450">
                              <tr>
                                <td width = "050" Class = "name"><center>ID</center></td>
                                <td width = "050" Class = "name"><center>COD</center></td>
                                <td width = "070" Class = "name"><center>PREFIJO</center></td>
                                <td width = "080" Class = "name"><center>CONSECUTIVO 1</center></td>
                                <td width = "150" Class = "name"><center>CONSECUTIVO 2</center></td>
                                <td width = "050" Class = "name"><center>ESTADO</center></td>
                              </tr>
                              <?php 
                              while ($xRMC = mysql_fetch_array($xPedCab)) {
                                if (mysql_num_rows($xPedCab) > 1) { ?>
                                  <tr>
                                    <td width = "050" class= "name"> <?php echo $xRMC['comidxxx'] ?></td>
                                    <td width = "050" class= "name"> <?php echo $xRMC['comcodxx'] ?></td>
                                    <td width = "070" class= "name"> <?php echo $xRMC['comprexx'] ?></td>
                                    <td width = "100" class= "name">
                                      <a href = "javascript:window.opener.document.forms['frgrm']['cPedId'].value      = '<?php echo $xRMC['pedidxxx']?>';
                                                            window.opener.document.forms['frgrm']['cPedComId'].value   = '<?php echo $xRMC['comidxxx']?>';
                                                            window.opener.document.forms['frgrm']['cPedComCod'].value  = '<?php echo $xRMC['comcodxx']?>';
                                                            window.opener.document.forms['frgrm']['cPedComCsc'].value  = '<?php echo $xRMC['comcscxx']?>';
                                                            window.opener.document.forms['frgrm']['cPedComCsc2'].value = '<?php echo $xRMC['comcsc2x']?>';
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
                                    window.opener.document.forms['frgrm']['cPedId'].value      = '<?php echo $xRMC['pedidxxx'] ?>';
                                    window.opener.document.forms['frgrm']['cPedComId'].value   = '<?php echo $xRMC['comidxxx'] ?>';
                                    window.opener.document.forms['frgrm']['cPedComCod'].value  = '<?php echo $xRMC['comcodxx'] ?>';
                                    window.opener.document.forms['frgrm']['cPedComCsc'].value  = '<?php echo $xRMC['comcscxx'] ?>';
                                    window.opener.document.forms['frgrm']['cPedComCsc2'].value = '<?php echo $xRMC['comcsc2x'] ?>';
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
                            window.opener.document.forms['frgrm']['cPedId'].value      = '';
                            window.opener.document.forms['frgrm']['cPedComId'].value   = '';
                            window.opener.document.forms['frgrm']['cPedComCod'].value  = '';
                            window.opener.document.forms['frgrm']['cPedComCsc'].value  = '';
                            window.opener.document.forms['frgrm']['cPedComCsc2'].value = '';
                            window.close();
                          </script>
                        <?php 
                        }
                      break;
                      case "VALID":
                        // Traigo Datos de la MIF
                        $qPedCab  = "SELECT ";
                        $qPedCab .= "$cAlfa.lpca$gPerAno.* ";
                        $qPedCab .= "FROM $cAlfa.lpca$gPerAno ";
                        $qPedCab .= "WHERE ";
                        if ($gFunction == "cPedComCsc") {
                          $qPedCab .= "$cAlfa.lpca$gPerAno.comcscxx LIKE \"%$gComCsc%\" AND ";
                        }
                        $qPedCab .= "$cAlfa.lpca$gPerAno.cliidxxx = \"$gCliId\" AND ";
                        $qPedCab .= "$cAlfa.lpca$gPerAno.regestxx IN (\"PROVISIONAL\",\"ACTIVO\", \"FACTURADO\", \"ANULADO\", \"RECHAZADO\") ";
                        $xPedCab  = f_MySql("SELECT","",$qPedCab,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qPedCab."~".mysql_num_rows($xPedCab));
                        $vPedCab  = mysql_fetch_array($xPedCab);
                        if (mysql_num_rows($xPedCab) > 0){
                          if (mysql_num_rows($xPedCab) == 1){ 
                            ?>
                            <script language = "javascript">
                              parent.fmwork.document.forms['frgrm']['cPedId'].value      = '<?php echo $vPedCab['pedidxxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cPedComId'].value   = '<?php echo $vPedCab['comidxxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cPedComCod'].value  = '<?php echo $vPedCab['comcodxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cPedComCsc'].value  = '<?php echo $vPedCab['comcscxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cPedComCsc2'].value = '<?php echo $vPedCab['comcsc2x'] ?>';
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
                        $qPedCab  = "SELECT ";
                        $qPedCab .= "$cAlfa.lpca$gPerAno.* ";
                        $qPedCab .= "FROM $cAlfa.lpca$gPerAno ";
                        $qPedCab .= "WHERE ";
                        $qPedCab .= "$cAlfa.lpca$gPerAno.comidxxx = \"P\" AND ";
                        $qPedCab .= "$cAlfa.lpca$gPerAno.comcodxx = \"$gComCod\" AND ";
                        $qPedCab .= "$cAlfa.lpca$gPerAno.comcscxx = \"$gComCsc\" AND ";
                        $qPedCab .= "$cAlfa.lpca$gPerAno.comcsc2x = \"$gComCsc2\" AND ";
                        $qPedCab .= "$cAlfa.lpca$gPerAno.cliidxxx = \"$gCliId\" AND ";
                        $qPedCab .= "$cAlfa.lpca$gPerAno.regestxx IN (\"PROVISIONAL\",\"ACTIVO\", \"FACTURADO\", \"ANULADO\", \"RECHAZADO\") LIMIT 0,1 ";
                        $xPedCab  = f_MySql("SELECT","",$qPedCab,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qPedCab."~".mysql_num_rows($xPedCab));
                        $vPedCab  = mysql_fetch_array($xPedCab);
                        if (mysql_num_rows($xPedCab) == 1) { 
                          ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cPedId'].value      = '<?php echo $vPedCab['pedidxxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cPedComId'].value   = '<?php echo $vPedCab['comidxxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cPedComCod'].value  = '<?php echo $vPedCab['comcodxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cPedComCsc'].value  = '<?php echo $vPedCab['comcscxx'] ?>';
                            parent.fmwork.document.forms['frgrm']['cPedComCsc2'].value = '<?php echo $vPedCab['comcsc2x'] ?>';
                          </script>
                        <?php
                        } else { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cPedId'].value      = '';
                            parent.fmwork.document.forms['frgrm']['cPedComId'].value   = '';
                            parent.fmwork.document.forms['frgrm']['cPedComCod'].value  = '';
                            parent.fmwork.document.forms['frgrm']['cPedComCsc'].value  = '';
                            parent.fmwork.document.forms['frgrm']['cPedComCsc2'].value = '';
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
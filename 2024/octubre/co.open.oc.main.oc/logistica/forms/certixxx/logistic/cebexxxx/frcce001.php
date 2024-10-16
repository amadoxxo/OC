<?php
  namespace openComex;
include("../../../../../financiero/libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
  <html>
    <head>
      <title>Parametrica Sector</title>
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
      <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    </head>
    <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
      <center>
        <table border ="0" cellpadding="0" cellspacing="0" width="95%">
          <tr>
            <td>
              <fieldset>
                <legend>Sector</legend>
                <form name = "frgrm" action = "" method = "post" target = "fmpro">
                  <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qSector  = "SELECT * ";
                        $qSector .= "FROM $cAlfa.lpar0009 ";
                        $qSector .= "WHERE ";
                        $qSector .= "secsapxx LIKE \"%$cSecSap%\" AND ";
                        $qSector .= "regestxx = \"ACTIVO\" ";
                        $xSector  = f_MySql("SELECT","",$qSector,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qSector."~".mysql_num_rows($xSector)."~".mysql_error($xConexion01));

                        if ($xSector && mysql_num_rows($xSector) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "560">
                              <tr>
                                <td width = "100" Class = "name"><center>CODIGO SAP</center></td>
                                <td Class = "name"><center>SECTOR</center></td>
                                <td width = "050" Class = "name"><center>ESTADO</center></td>
                              </tr>
                              <?php while ($mSector = mysql_fetch_array($xSector)) {
                                if (mysql_num_rows($xSector) > 1) { ?>
                                  <tr>
                                    <td width = "050" class= "name" align="center">
                                      <a href = "javascript:window.opener.document.forms['frgrm']['cSecSap'].value ='<?php echo $mSector['secsapxx']?>';
                                                            window.opener.document.forms['frgrm']['cSecDes'].value ='<?php echo $mSector['secdesxx']?>';
                                                            window.close()"><?php echo $mSector['secsapxx'] ?></a></td>
                                    <td width = "400" class= "name"> <?php echo $mSector['secdesxx'] ?></td>
                                    <td width = "050" class= "name"> <?php echo $mSector['regestxx'] ?></td>
                                  </tr>
                                <?php	} else { ?>
                                  <script languaje="javascript">
                                    window.opener.document.forms['frgrm']['cSecSap'].value = '<?php echo $mSector['secsapxx'] ?>';
                                    window.opener.document.forms['frgrm']['cSecDes'].value = '<?php echo $mSector['secdesxx'] ?>';
                                    window.close();
                                  </script>
                                <?php }
                              } ?>
                            </table>
                          </center>
                        <?php	} else {
                          f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
                        }
                      break;
                      case "VALID":
                        $qSector  = "SELECT * ";
                        $qSector .= "FROM $cAlfa.lpar0009 ";
                        $qSector .= "WHERE ";
                        $qSector .= "secsapxx = \"$cSecSap\" AND ";
                        $qSector .= "regestxx = \"ACTIVO\" ";
                        $xSector  = f_MySql("SELECT","",$qSector,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qSector."~".mysql_num_rows($xSector)."~".mysql_error($xConexion01));

                        if (mysql_num_rows($xSector) > 0) {
                          while ($xROV = mysql_fetch_array($xSector)) { ?>
                            <script languaje = "javascript">
                              parent.fmwork.document.forms['frgrm']['cSecSap'].value = '<?php echo $xROV['Sectorxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cSecDes'].value = '<?php echo $xROV['secdesxx'] ?>';
                            </script>
                          <?php break;
                          }
                        } else { ?>
                          <script languaje = "javascript">
                            parent.fmwork.fnLinks('<?php echo $gFunction ?>','WINDOW');
                          </script>
                        <?php }
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
<?php } else {
  f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
} ?>
<?php
  namespace openComex;
include("../../../../../financiero/libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
  <html>
    <head>
      <title>Parametrica Organizacion de Ventas</title>
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
                <legend>Organizacion de Ventas</legend>
                <form name = "frgrm" action = "" method = "post" target = "fmpro">
                  <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qOrgVen  = "SELECT * ";
                        $qOrgVen .= "FROM $cAlfa.lpar0001 ";
                        $qOrgVen .= "WHERE ";
                        $qOrgVen .= "orvsapxx LIKE \"%$cOrvSap%\" AND ";
                        $qOrgVen .= "regestxx = \"ACTIVO\" ";
                        $xOrgVen  = f_MySql("SELECT","",$qOrgVen,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qOrgVen."~".mysql_num_rows($xOrgVen)."~".mysql_error($xConexion01));

                        if ($xOrgVen && mysql_num_rows($xOrgVen) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "560">
                              <tr>
                                <td width = "100" Class = "name"><center>CODIGO SAP</center></td>
                                <td Class = "name"><center>ORGANIZACI&Oacute;N DE VENTAS</center></td>
                                <td width = "050" Class = "name"><center>ESTADO</center></td>
                              </tr>
                              <?php while ($mOrgVen = mysql_fetch_array($xOrgVen)) {
                                if (mysql_num_rows($xOrgVen) > 1) { ?>
                                  <tr>
                                    <td width = "050" class= "name" align="center">
                                      <a href = "javascript:window.opener.document.forms['frgrm']['cOrvSap'].value ='<?php echo $mOrgVen['orvsapxx']?>';
                                                            window.opener.document.forms['frgrm']['cOrvDes'].value ='<?php echo $mOrgVen['orvdesxx']?>';
                                                            window.close()"><?php echo $mOrgVen['orvsapxx'] ?></a></td>
                                    <td width = "400" class= "name"> <?php echo $mOrgVen['orvdesxx'] ?></td>
                                    <td width = "050" class= "name"> <?php echo $mOrgVen['regestxx'] ?></td>
                                  </tr>
                                <?php	} else { ?>
                                  <script languaje="javascript">
                                    window.opener.document.forms['frgrm']['cOrvSap'].value = '<?php echo $mOrgVen['orvsapxx'] ?>';
                                    window.opener.document.forms['frgrm']['cOrvDes'].value = '<?php echo $mOrgVen['orvdesxx'] ?>';
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
                        $qOrgVen  = "SELECT * ";
                        $qOrgVen .= "FROM $cAlfa.lpar0001 ";
                        $qOrgVen .= "WHERE ";
                        $qOrgVen .= "orvsapxx = \"$cOrvSap\" AND ";
                        $qOrgVen .= "regestxx = \"ACTIVO\" ";
                        $xOrgVen  = f_MySql("SELECT","",$qOrgVen,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qOrgVen."~".mysql_num_rows($xOrgVen)."~".mysql_error($xConexion01));

                        if (mysql_num_rows($xOrgVen) > 0) {
                          while ($xROV = mysql_fetch_array($xOrgVen)) { ?>
                            <script languaje = "javascript">
                              parent.fmwork.document.forms['frgrm']['cOrvSap'].value = '<?php echo $xROV['OrgVenxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cOrvDes'].value = '<?php echo $xROV['orvdesxx'] ?>';
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
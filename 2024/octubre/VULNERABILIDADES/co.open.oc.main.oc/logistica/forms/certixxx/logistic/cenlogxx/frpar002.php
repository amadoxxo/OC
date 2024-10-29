<?php
  namespace openComex;
include("../../../../../financiero/libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
  <html>
    <head>
      <title>Parametrica Oficina de Ventas</title>
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
                <legend>Oficina de Ventas</legend>
                <form name = "frgrm" action = "" method = "post" target = "fmpro">
                  <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qOfiVen  = "SELECT * ";
                        $qOfiVen .= "FROM $cAlfa.lpar0002 ";
                        $qOfiVen .= "WHERE ";
                        $qOfiVen .= "orvsapxx = \"$cOrvSap\" AND ";
                        $qOfiVen .= "ofvsapxx LIKE \"%$cOfvSap%\" AND ";
                        $qOfiVen .= "regestxx = \"ACTIVO\" ";
                        $xOfiVen  = f_MySql("SELECT","",$qOfiVen,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qOfiVen."~".mysql_num_rows($xOfiVen)."~".mysql_error($xConexion01));

                        if ($xOfiVen && mysql_num_rows($xOfiVen) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "560">
                              <tr>
                                <td width = "100" Class = "name"><center>CODIGO SAP</center></td>
                                <td Class = "name"><center>OFICINA DE VENTAS</center></td>
                                <td width = "050" Class = "name"><center>ESTADO</center></td>
                              </tr>
                              <?php while ($mOfiVen = mysql_fetch_array($xOfiVen)) {
                                if (mysql_num_rows($xOfiVen) >= 1) { ?>
                                  <tr>
                                    <td width = "050" class= "name" align="center">
                                      <a href = "javascript:window.opener.document.forms['frgrm']['cOfvSap'].value ='<?php echo $mOfiVen['ofvsapxx']?>';
                                                            window.opener.document.forms['frgrm']['cOfvDes'].value ='<?php echo $mOfiVen['ofvdesxx']?>';
                                                            window.close()"><?php echo $mOfiVen['ofvsapxx'] ?></a></td>
                                    <td width = "400" class= "name"> <?php echo $mOfiVen['ofvdesxx'] ?></td>
                                    <td width = "050" class= "name"> <?php echo $mOfiVen['regestxx'] ?></td>
                                  </tr>
                                <?php	} else { ?>
                                  <script languaje="javascript">
                                    window.opener.document.forms['frgrm']['cOfvSap'].value = '<?php echo $mOfiVen['ofvsapxx'] ?>';
                                    window.opener.document.forms['frgrm']['cOfvDes'].value = '<?php echo $mOfiVen['ofvdesxx'] ?>';
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
                        $qOfiVen  = "SELECT * ";
                        $qOfiVen .= "FROM $cAlfa.lpar0002 ";
                        $qOfiVen .= "WHERE ";
                        $qOfiVen .= "orvsapxx = \"$cOrvSap\" AND ";
                        $qOfiVen .= "orvsapxx = \"$cOfvSap\" AND ";
                        $qOfiVen .= "regestxx = \"ACTIVO\" ";
                        $xOfiVen  = f_MySql("SELECT","",$qOfiVen,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qOfiVen."~".mysql_num_rows($xOfiVen)."~".mysql_error($xConexion01));

                        if (mysql_num_rows($xOfiVen) > 0) {
                          while ($xROV = mysql_fetch_array($xOfiVen)) { ?>
                            <script languaje = "javascript">
                              parent.fmwork.document.forms['frgrm']['cOfvSap'].value = '<?php echo $xROV['ofvsapxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cOfvDes'].value = '<?php echo $xROV['ofvdesxx'] ?>';
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
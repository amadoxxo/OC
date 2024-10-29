<?php
  namespace openComex;
include("../../../../../financiero/libs/php/utility.php");
?>
<?php if ($gWhat != "" && $gFunction != "") { ?>
  <html>
    <head>
      <title>Parametrica SubServicios</title>
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
                <legend>Servicios</legend>
                <form name = "frgrm" action = "" method = "post" target = "fmpro">
                  <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qServix  = "SELECT * ";
                        $qServix .= "FROM $cAlfa.lpar0011 ";
                        $qServix .= "WHERE ";
                        $qServix .= "sersapxx LIKE \"%$cSerSap%\" AND ";
                        $qServix .= "regestxx = \"ACTIVO\" ";
                        $xServix  = f_MySql("SELECT","",$qServix,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qServix."~".mysql_num_rows($xServix)."~".mysql_error($xConexion01));

                        if ($xServix && mysql_num_rows($xServix) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "560">
                              <tr>
                                <td width = "100" Class = "name"><center>CODIGO SAP</center></td>
                                <td Class = "name"><center>SERVICIO</center></td>
                                <td width = "050" Class = "name"><center>ESTADO</center></td>
                              </tr>
                              <?php while ($mServix = mysql_fetch_array($xServix)) {
                                if (mysql_num_rows($xServix) > 1) { ?>
                                  <tr>
                                    <td width = "050" class= "name" align="center">
                                      <a href = "javascript:window.opener.document.forms['frgrm']['cSerSap'].value ='<?php echo $mServix['sersapxx']?>';
                                                            window.opener.document.forms['frgrm']['cSerDes'].value ='<?php echo $mServix['serdesxx']?>';
                                                            window.close()"><?php echo $mServix['sersapxx'] ?></a></td>
                                    <td width = "400" class= "name"> <?php echo $mServix['serdesxx'] ?></td>
                                    <td width = "050" class= "name"> <?php echo $mServix['regestxx'] ?></td>
                                  </tr>
                                <?php	} else { ?>
                                  <script languaje="javascript">
                                    window.opener.document.forms['frgrm']['cSerSap'].value = '<?php echo $mServix['sersapxx'] ?>';
                                    window.opener.document.forms['frgrm']['cSerDes'].value = '<?php echo $mServix['serdesxx'] ?>';
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
                        $qServix  = "SELECT * ";
                        $qServix .= "FROM $cAlfa.lpar0011 ";
                        $qServix .= "WHERE ";
                        $qServix .= "sersapxx = \"$cSerSap\" AND ";
                        $qServix .= "regestxx = \"ACTIVO\" ";
                        $xServix  = f_MySql("SELECT","",$qServix,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qServix."~".mysql_num_rows($xServix)."~".mysql_error($xConexion01));

                        if (mysql_num_rows($xServix) > 0) {
                          while ($xROV = mysql_fetch_array($xServix)) { ?>
                            <script languaje = "javascript">
                              parent.fmwork.document.forms['frgrm']['cSerSap'].value = '<?php echo $xROV['sersapxx'] ?>';
                              parent.fmwork.document.forms['frgrm']['cSerDes'].value = '<?php echo $xROV['serdesxx'] ?>';
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
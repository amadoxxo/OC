<?php
  namespace openComex;
include("../../../../../financiero/libs/php/utility.php"); 

if ($gWhat != "" && $gFunction != "") { ?>
  <html>
      <head>
        <title>Parametrica de Ciudades</title>
        <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
        <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
        <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
        <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
        <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
        <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
        </script>
      </head>
    <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
      <center>
        <table border ="0" cellpadding="0" cellspacing="0" width="300">
          <tr>
            <td>
              <fieldset>
                <legend>Param&eacute;trica de Ciudades</legend>
                <form name = "frgrm" action = "" method = "post" target = "fmpro">
                  <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qCiuDes  = "SELECT * FROM $cAlfa.SIAI0055 WHERE PAIIDXXX = \"$cPaiId\" AND DEPIDXXX = \"$cDepId\" AND CIUIDXXX LIKE \"%$cCiuId%\" AND REGESTXX = \"ACTIVO\" ";
                        $xCiuDes = f_MySql("SELECT","",$qCiuDes,$xConexion01,"");
                        if ($xCiuDes && mysql_num_rows($xCiuDes) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr>
                                <td widht = "050" Class = "name"><center>ID</center></td>
                                <td widht = "400" Class = "name"><center>CIUDADES</center></td>
                                <td widht = "050" Class = "name"><center>ESTADO</center></td>
                              </tr>
                              <?php while ($xCD = mysql_fetch_array($xCiuDes)) {
                                if (mysql_num_rows($xCiuDes) > 1) { ?>
                                  <?php
                                    switch ($gFunction) {
                                      case "cCiuId":
                                        ?>
                                        <tr>
                                          <td width = "050" class= "name">
                                            <a href = "javascript:window.opener.document.forms['frgrm']['cCiuId'].value  ='<?php echo $xCD['CIUIDXXX']?>';
                                                                  window.opener.document.forms['frgrm']['cCiuDes'].value ='<?php echo $xCD['CIUDESXX']?>';
                                                                  close()"><?php echo $xCD['CIUIDXXX'] ?></a></td>
                                          <td width = "400" class= "name"> <?php echo $xCD['CIUDESXX'] ?></td>
                                          <td width = "050" class= "name"> <?php echo $xCD['REGESTXX'] ?></td>
                                        </tr>
                                        <?php
                                      break;
                                    }
                                } else {
                                  switch ($gFunction) {
                                    case "cCiuId":
                                      ?>
                                        <script languaje="javascript">
                                          window.opener.document.forms['frgrm']['cCiuId'].value  = '<?php echo $xCD['CIUIDXXX'] ?>';
                                          window.opener.document.forms['frgrm']['cCiuDes'].value = '<?php echo $xCD['CIUDESXX'] ?>';
                                          window.close();
                                        </script>
                                      <?php
                                    break;
                                  }
                                }
                              } ?>
                            </table>
                          </center>
                        <?php	} else {
                          f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
                          ?>
                            <script languaje="javascript">
                              window.opener.document.forms['frgrm']['cCiuId'].value  = '';
                              window.opener.document.forms['frgrm']['cCiuDes'].value = '';
                              window.close();
                            </script>
                          <?php
                        }
                      break;
                      case "VALID":
                        $qCiuDes  = "SELECT * FROM $cAlfa.SIAI0055 WHERE PAIIDXXX = \"$cPaiId\" AND DEPIDXXX = \"$cDepId\" AND CIUIDXXX LIKE \"%$cCiuId%\" AND REGESTXX = \"ACTIVO\" ";
                        $xCiuDes = f_MySql("SELECT","",$qCiuDes,$xConexion01,"");
                        if ($xCiuDes && mysql_num_rows($xCiuDes) > 0) {
                          while ($xCD = mysql_fetch_array($xCiuDes)) {
                            switch ($gFunction) {
                              case "cCiuId":
                                ?>
                                <script languaje = "javascript">
                                  parent.fmwork.document.forms['frgrm']['cCiuId'].value  = '<?php echo $xCD['CIUIDXXX'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cCiuDes'].value = '<?php echo $xCD['CIUDESXX'] ?>';
                                  window.close();
                                </script>
                                <?php
                              break;
                            }
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
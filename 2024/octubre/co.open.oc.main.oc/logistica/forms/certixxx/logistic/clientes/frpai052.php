<?php
  namespace openComex;
include("../../../../../financiero/libs/php/utility.php");

if ($gWhat != "" && $gFunction != "") { ?>
  <html>
    <head>
      <title>Parametrica Paises</title>
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
                <legend>Param&eacute;trica Paises</legend>
                <form name = "frgrm" action = "" method = "post" target = "fmpro">
                  <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qPaiDes  = "SELECT * FROM $cAlfa.SIAI0052 WHERE PAIIDXXX LIKE \"%$cPaiId%\" AND REGESTXX  = \"ACTIVO\" ";
                        $xPaiDes = f_MySql("SELECT","",$qPaiDes,$xConexion01,"");
                        if ($xPaiDes && mysql_num_rows($xPaiDes) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr>
                                <td widht = "050" Class = "name"><center>ID</center></td>
                                <td widht = "050" Class = "name"><center>ID NRO</center></td>
                                <td widht = "350" Class = "name"><center>PAIS</center></td>
                                <td widht = "050" Class = "name"><center>ESTADO</center></td>
                              </tr>
                              <?php while ($xPD = mysql_fetch_array($xPaiDes)) {
                                if (mysql_num_rows($xPaiDes) > 1) { ?>
                                  <?php
                                  switch ($gFunction) {
                                    case "cPaiId":
                                      ?>
                                      <tr>
                                        <td width = "050" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm']['cPaiId'].value  ='<?php echo $xPD['PAIIDXXX']?>';
                                                                window.opener.document.forms['frgrm']['cPaiDes'].value ='<?php echo $xPD['PAIDESXX']." "."(".$xPD['PAIIDNXX'].")"?>';
                                                                close()"><?php echo $xPD['PAIIDXXX'] ?></a></td>
                                        <td width = "050" class= "name"> <?php echo $xPD['PAIIDNXX'] ?></td>
                                        <td width = "350" class= "name"> <?php echo $xPD['PAIDESXX'] ?></td>
                                        <td width = "050" class= "name"> <?php echo $xPD['REGESTXX'] ?></td>
                                      </tr>
                                      <?php
                                    break;
                                  }
                                } else {
                                  switch ($gFunction) {
                                    case "cPaiId":
                                      ?>
                                      <script languaje="javascript">
                                        window.opener.document.forms['frgrm']['cPaiId'].value  = '<?php echo $xPD['PAIIDXXX'] ?>';
                                        window.opener.document.forms['frgrm']['cPaiDes'].value = '<?php echo $xPD['PAIDESXX']." "."(".$xPD['PAIIDNXX'].")" ?>';
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
                            window.opener.document.forms['frgrm']['cPaiId'].value  = "";
                            window.opener.document.forms['frgrm']['cPaiDes'].value = "";
                            window.close();
                          </script>
                          <?php
                        }
                      break;
                      case "VALID":
                        $qPaiDes  = "SELECT * FROM $cAlfa.SIAI0052 WHERE PAIIDXXX = \"$cPaiId\" AND REGESTXX = \"ACTIVO\" ";
                        $xPaiDes = f_MySql("SELECT","",$qPaiDes,$xConexion01,"");
                        if ($xPaiDes && mysql_num_rows($xPaiDes) > 0) {
                          while ($xPD = mysql_fetch_array($xPaiDes)) {
                            switch ($gFunction) {
                              case "cPaiId":
                                ?>
                                <script languaje = "javascript">
                                  parent.fmwork.document.forms['frgrm']['cPaiId'].value  = '<?php echo $xPD['PAIIDXXX'] ?>';
                                  parent.fmwork.document.forms['frgrm']['cPaiDes'].value = '<?php echo $xPD['PAIDESXX']." "."(".$xPD['PAIIDNXX'].")" ?>';
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
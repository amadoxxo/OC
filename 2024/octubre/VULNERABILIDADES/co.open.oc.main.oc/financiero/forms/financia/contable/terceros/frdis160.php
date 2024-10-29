<?php
  namespace openComex;
/**
 * Validacion de 0, 1, o N Disconformidades
 * --- Descripcion: Permite Abrir formulario o pasar datos para seleccion de Disconformidades
 * @author Juan Jose Trujillo. <juan.trujillo@open-eb.co>
 * @package openComex
 */
include ("../../../../libs/php/utility.php");
/**
 *  Cookie fija
 */
$kDf = explode("~", $_COOKIE["kDatosFijos"]);
$kMysqlHost = $kDf[0];
$kMysqlUser = $kDf[1];
$kMysqlPass = $kDf[2];
$kMysqlDb = $kDf[3];
$kUser = $kDf[4];
$kLicencia = $kDf[5];
$swidth = $kDf[6];

if (!empty($gWhat) && !empty($gFunction)) {
  ?>
  <html>
    <head>
      <title>Param&eacute;trica de Disconformidades</title>
      <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
      <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
      <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    </head>
    <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="300">
        <tr>
          <td>
            <fieldset>
              <legend>Param&eacute;trica de Disconformidades</legend>
              <form name = "frgrm" action = "" method = "post" target = "fmpro">
                <?php
                switch ($gWhat) {
                  case "WINDOW":
                    $qDisconformidad  = "SELECT ";
                    $qDisconformidad .= "disidxxx,";
                    $qDisconformidad .= "disdesxx,";
                    $qDisconformidad .= "regestxx ";
                    $qDisconformidad .= "FROM $cAlfa.fpar0160 ";
                    $qDisconformidad .= "WHERE ";
                    $qDisconformidad .= "disidxxx LIKE \"%$gDiscId%\" AND ";
                    $qDisconformidad .= "regestxx = \"ACTIVO\" ORDER BY $cAlfa.fpar0160.disidxxx";
                    $xDisconformidad  = f_MySql("SELECT", "", $qDisconformidad, $xConexion01, "");
                    // f_Mensaje(__FILE__, __LINE__, $gFunction."~".$gDiscId."~".$qDisconformidad."~".mysql_num_rows($xDisconformidad));
                    if ($xDisconformidad && mysql_num_rows($xDisconformidad) > 0) {
                      ?>
                      <center>
                        <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                          <tr bgcolor = '#D6DFF7'>
                          <td widht = "050" Class = "name"><center>Id</center></td>
                          <td widht = "400" Class = "name"><center>Descripci&oacute;n</center></td>
                          <td widht = "050" Class = "name"><center>Estado</center></td>
                          </tr>
                          <?php
                          while ($xRD = mysql_fetch_array($xDisconformidad)) {
                            if (mysql_num_rows($xDisconformidad) > 0) {
                              ?>
                              <tr>
                                <?php
                                switch ($gFunction) {
                                  case "cDiscId":
                                    ?>
                                    <td width = "050" class= "name">
                                      <a href = "javascript:window.opener.document.forms['frgrm'].cDiscId.value = '<?php echo $xRD['disidxxx'] ?>';
                                         window.opener.document.forms['frgrm'].cDiscDes.value = '<?php echo $xRD['disdesxx'] ?>';
                                         window.opener.f_Links('cDiscId','EXACT',0);
                                         window.close();"><?php echo $xRD['disidxxx'] ?></a>
                                    </td>
                                    <?php
                                  break;
                                }
                                ?>
                                <td width = "400" class= "name"><?php echo $xRD['disdesxx'] ?></td> 
                                <td width = "050" class= "name"><?php echo $xRD['regestxx'] ?></td>
                              </tr>
                              <?php
                            }
                          }
                          ?>
                        </table>
                      </center>
                      <?php
                    } else {
                      f_Mensaje(__FILE__, __LINE__, "No se Encontraron Registros");
                      ?>
                      <script languaje="javascript">
                        window.opener.document.forms['frgrm'].cDiscId.value  = "";
                        window.opener.document.forms['frgrm'].cDiscDes.value = "";
                        window.close();
                      </script>
                      <?php
                    }
                  break;
                  case "VALID":
                    $qDisconformidad  = "SELECT ";
                    $qDisconformidad .= "disidxxx,";
                    $qDisconformidad .= "disdesxx ";
                    $qDisconformidad .= "FROM $cAlfa.fpar0160 ";
                    $qDisconformidad .= "WHERE ";
                    $qDisconformidad .= "disidxxx LIKE \"%$gDiscId%\" AND ";
                    $qDisconformidad .= "regestxx = \"ACTIVO\" ORDER BY disidxxx";
                    $xDisconformidad = f_MySql("SELECT", "", $qDisconformidad, $xConexion01, "");
                    // f_Mensaje(__FILE__,__LINE__,$qDisconformidad."~".mysql_num_rows($xDisconformidad)."~".$gFunction);
                    if (mysql_num_rows($xDisconformidad) > 0) {
                      if (mysql_num_rows($xDisconformidad) == 1 && $gDiscId != "") {
                        while ($xRD = mysql_fetch_array($xDisconformidad)) {
                          switch ($gFunction) {
                            case "cDiscId":
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm'].cDiscId.value = "<?php echo $xRD['disidxxx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cDiscDes.value = "<?php echo $xRD['disdesxx'] ?>";
                                parent.fmwork.f_Links('cDiscId', 'EXACT', 0);
                              </script>
                              <?php
                            break;
                          }
                        }
                      } else {
                        ?>
                        <script language = "javascript">
                          parent.fmwork.document.forms['frgrm'].cDiscDes.value = "";
                          parent.fmwork.f_Links('<?php echo $gFunction ?>', 'WINDOW');
                          window.close();
                        </script>
                        <?php
                      }
                    } else {
                      switch ($gFunction) {
                        case "cDiscId":
                          f_Mensaje(__FILE__, __LINE__, "No se Encontraron Registros");
                          ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm'].cDiscId.value = "";
                            parent.fmwork.document.forms['frgrm'].cDiscDes.value = "";
                          </script>
                          <?php
                        break;
                      }
                    }
                  break;
                  case "EXACT":
                    $qDisconformidad = "SELECT ";
                    $qDisconformidad .= "disidxxx,";
                    $qDisconformidad .= "disdesxx,";
                    $qDisconformidad .= "regestxx ";
                    $qDisconformidad .= "FROM $cAlfa.fpar0160 ";
                    $qDisconformidad .= "WHERE ";
                    $qDisconformidad .= "disidxxx = \"$gDiscId\" AND ";
                    $qDisconformidad .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                    $xDisconformidad = f_MySql("SELECT", "", $qDisconformidad, $xConexion01, "");
                    // f_Mensaje(__FILE__,__LINE__,$qDisconformidad."~".mysql_num_rows($xDisconformidad));
                    while ($xRD = mysql_fetch_array($xDisconformidad)) {
                      switch ($gFunction) {
                        case "cDiscId":
                          ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm'].cDiscId.value = "<?php echo $xRD['disidxxx'] ?>";
                            parent.fmwork.document.forms['frgrm'].cDiscDes.value = "<?php echo str_replace(array('"', "'"), array('\"', "\'"), $xRD['disdesxx']) ?>";
                          </script>
                          <?php
                        break;
                      }
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
  f_Mensaje(__FILE__, __LINE__, "No se Recibieron Parametros Completos");
}
?>

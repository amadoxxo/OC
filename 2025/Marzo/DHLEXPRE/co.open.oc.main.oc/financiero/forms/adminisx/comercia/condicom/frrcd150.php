<?php
  /**
   * --- Descripción: Paramétrica de Importadores:
   * @author Elian Amado <elian.amado@openits.co>
   * @package openComex
   * @version 001
   */
  include('../../../../libs/php/utility.php');

  if ($gWhat != "" && $gFunction != "") { ?>
    <html>
      <head>
        <title>Param&eacute;trica de Importadores</title>
        <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
        <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
        <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
        <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
        <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
      </head>
      <body topmargin="0" leftmargin="0" marginwidth="0" marginheight="0" style="margin-right: 0;">
        <center>
          <table border="0" cellpadding="0" cellspacing="0" width="300">
            <tr>
              <td>
                <fieldset>
                  <legend>Param&eacute;trica de Importadores</legend>
                  <form name="frnav" method="post" target="fmpro">
                    <?php
                      switch ($gWhat) {
                        case 'WINDOW':
                          $qCliImp  = "SELECT ";
                          $qCliImp .= "CLIIDXXX, ";
                          $qCliImp .= "CONCAT(CLINOMXX,\" \",CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X) AS CLINOMXX, ";
                          $qCliImp .= "REGESTXX ";
                          $qCliImp .= "FROM $cAlfa.SIAI0150 ";
                          $qCliImp .= "WHERE ";
                          switch ($gFunction) {
                            case 'cCliId':
                              $qCliImp .= "CLIIDXXX LIKE \"%$gCliId%\" AND ";
                            break;
                            case 'cCliNom':
                              $qCliImp .= "CLINOMXX LIKE \"%$gCliNom%\" AND ";
                            break;
                          }
                          $qCliImp .= "REGESTXX = \"ACTIVO\" ";
                          $qCliImp .= "ORDER BY CLINOMXX ";
                          $xClidImp = f_MySql("SELECT","",$qCliImp,$xConexion01,"");
                          if ($xClidImp && mysql_num_rows($xClidImp) > 0) { ?>
                            <center>
                              <table cellspacing="0" cellpadding="1" border="1" width="500">
                                <tr>
                                  <td width="050" class="name"><center>NIT</center></td>
                                  <td width="400" class="name"><center>NOMBRE</center></td>
                                  <td width="050" class="name"><center>ESTADO</center></td>
                                </tr>
                                <?php while ($xRCD = mysql_fetch_array($xClidImp)) {
                                  if (mysql_num_rows($xClidImp) > 1) { ?>
                                    <tr>
                                      <td width="050" class="name">
                                        <a href="javascript:window.opener.document.forms['frnav']['cCliId'].value  = '<?php echo $xRCD['CLIIDXXX'] ?>';
                                                            window.opener.document.forms['frnav']['cCliDv'].value  = '<?php echo f_Digito_Verificacion($xRCD['CLIIDXXX']) ?>';
                                                            window.opener.document.forms['frnav']['cCliNom'].value = '<?php echo $xRCD['CLINOMXX'] ?>';
                                                            window.opener.fnLinks('cCliId', 'EXACT');
                                                            window.close()"><?php echo $xRCD['CLIIDXXX'] ?></a></td>
                                      <td width="400" class="name"><?php echo $xRCD['CLINOMXX'] ?></td>
                                      <td width="050" class="name"><?php echo $xRCD['REGESTXX'] ?></td>
                                    </tr>
                                  <?php 
                                  } else { ?>
                                  <script language="javascript">
                                    window.opener.document.forms['frnav']['cCliId'].value  = "<?php echo $xRCD['CLIIDXXX'] ?>";
                                    window.opener.document.forms['frnav']['cCliDv'].value  = "<?php echo f_Digito_Verificacion($xRCD['CLIIDXXX']) ?>";
                                    window.opener.document.forms['frnav']['cCliNom'].value = "<?php echo $xRCD['CLINOMXX'] ?>";
                                    window.close();
                                  </script>
                                  <?php
                                  }
                                } ?>
                              </table>
                            </center>
                          <?php } else {
                            f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); ?>
                            <script language="javascript">
                              window.opener.document.forms['frnav']['cCliId'].value  = "";
                              window.opener.document.forms['frnav']['cCliDv'].value  = "";
                              window.opener.document.forms['frnav']['cCliNom'].value = "";
                              window.close();
                            </script>
                          <?php }
                        break;
                        case "VALID":
                          $qCliImp  = "SELECT ";
                          $qCliImp .= "$cAlfa.SIAI0150.CLIIDXXX,";
                          $qCliImp .= "CONCAT(CLINOMXX,\" \",CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X) AS CLINOMXX,";
                          $qCliImp .= "$cAlfa.SIAI0150.REGESTXX ";
                          $qCliImp .= "FROM $cAlfa.SIAI0150 ";
                          $qCliImp .= "WHERE ";
                          switch ($gFunction) {
                            case 'cCliId':
                              $qCliImp .= "CLIIDXXX = \"$gCliId\" AND ";
                            break;
                            case 'cCliNom':
                              $qCliImp .= "CLINOMXX = \"$gCliNom\" AND ";
                            break;
                          }
                          $qCliImp .= "REGESTXX = \"ACTIVO\" ";
                          $qCliImp .= "ORDER BY CLINOMXX ";
                          $xClidImp  = f_MySql("SELECT","",$qCliImp,$xConexion01,"");
                          if ($xClidImp && mysql_num_rows($xClidImp) > 0) {
                            while ($xRCD = mysql_fetch_array($xClidImp)) { ?>
                              <script language="javascript">
                                parent.fmwork.document.forms['frnav']['cCliId'].value  = '<?php echo $xRCD['CLIIDXXX'] ?>';
                                parent.fmwork.document.forms['frnav']['cCliDv'].value  = '<?php echo f_Digito_Verificacion($xRCD['CLIIDXXX']) ?>';
                                parent.fmwork.document.forms['frnav']['cCliNom'].value = '<?php echo $xRCD['CLINOMXX'] ?>';
                              </script>
                            <?php
                            }
                          } else { ?>
                            <script language="javascript">
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
    f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos.");
  } ?>
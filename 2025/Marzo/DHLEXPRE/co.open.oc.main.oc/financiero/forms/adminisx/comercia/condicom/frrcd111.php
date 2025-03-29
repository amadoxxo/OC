<?php
  /**
   * --- Descripción: Grupo de Tarifas:
   * @author Elian Amado <elian.amado@openits.co>
   * @package openComex
   * @version 001
   */
  include("../../../../libs/php/utility.php");

  if ($gWhat != "" && $gFunction != "") { ?>
    <html>
      <head>
        <title>Parametrica de Grupo de Tarifas</title>
        <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory?>/estilo.css">
        <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory?>/general.css">
        <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory?>/layout.css">
        <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory?>/custom.css">
        <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory?>/overlib.css">
      </head>
      <body topmargin="0" leftmargin="0" marginwidth="0" marginheight="0" style="margin-right: 0;">
        <center>
          <table border="0" cellpadding="0" cellspacing="0" width="550">
            <tr>
              <td>
                <fieldset>
                  <legend>Parametrica de Grupo de Tarifas</legend>
                  <form name="frnav" method="post" target="fmpro">
                    <?php
                      switch ($gWhat) {
                        case 'WINDOW':
                          $qGruTar  = "SELECT gtaidxxx, ";
                          $qGruTar .= "IF(gtadesxx != \"\",gtadesxx,\"SIN DESCRIPCION\") AS gtadesxx ";
                          $qGruTar .= "FROM $cAlfa.fpar0111 ";
                          $qGruTar .= "WHERE ";
                          $qGruTar .= "gtaidxxx LIKE \"%$gGtaId%\" AND ";
                          // switch ($gFunction) {
                          //   case 'cGtaId':
                          //     $qGruTar .= "gtaidxxx LIKE \"%$gGtaId%\" AND ";
                          //   break;
                          //   case 'cGtaDes':
                          //     $qGruTar .= "gtadesxx LIKE \"%$gGtaDes%\" AND ";
                          //   break;
                          // }
                          $qGruTar .= "regestxx = \"ACTIVO\" ORDER BY gtaidxxx";
                          $xGruTar  = f_MySql("SELECT","",$qGruTar,$xConexion01,"");

                          echo "<pre>";
                          print_r($qGruTar);

                          if ($xGruTar && mysql_num_rows($xGruTar) > 0) { ?>
                            <center>
                              <table cellspacing="0" cellpadding="1" border="1" width="550">
                                <tr>
                                  <td width="050" class="name"><center>Id</center></td>
                                  <td width="150" class="name"><center>Descripci&oacute;n</center></td>
                                  <td width="050" class="name"><center>Estado</center></td>
                                </tr>
                                <?php while ($xRGT = mysql_fetch_array($xGruTar)) {
                                  if (mysql_num_rows($xGruTar) > 1) { ?>
                                    <tr>
                                      <td class="name">
                                        <a href="javascript:window.opener.document.forms['frnav']['cGtaId'].value  ='<?php echo $xRGT['gtaidxxx'] ?>';
                                                            window.opener.document.forms['frnav']['cGtaDes'].value ='<?php echo $xRGT['gtadesxx'] ?>';
                                                            window.opener.fnLinks('cGtaId', 'EXACT');
                                                            window.close()">&nbsp;<?php echo $xRGT['gtaidxxx'] ?></a></td>
                                        <td class="name">&nbsp;<?php echo $xRGT['gtadesxx'] ?></td>
                                        <td class="name"><center><?php echo $xRGT['regestxx'] ?></center></td>
                                    </tr>
                                    <?php
                                  } else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frnav']['cGtaId'].value  = "<?php echo $xRGT['gtaidxxx'] ?>";
                                      window.opener.document.forms['frnav']['cGtaDes'].value = "<?php echo $xRGT['gtadesxx'] ?>";
                                      window.close();
                                    </script>
                                  <?php }
                                } ?>
                              </table>
                            </center>
                          <?php } else {
                            f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); ?>
                            <script language="javascript">
                              window.opener.document.forms['frnav']['cGtaId'].value  = "";
                              window.opener.document.forms['frnav']['cGtaDes'].value  = "";
                              window.close();
                            </script>
                          <?php }
                        break;
                        case 'VALID':
                          $qGruTar  = "SELECT gtaidxxx, ";
                          $qGruTar .= "IF(gtadesxx != \"\",gtadesxx,\"SIN DESCRIPCION\") AS gtadesxx ";
                          $qGruTar .= "FROM $cAlfa.fpar0111 ";
                          $qGruTar .= "WHERE ";
                          switch ($gFunction) {
                            case 'cGtaId':
                              $qGruTar .= "gtaidxxx = \"$gGtaId\" AND ";
                            break;
                            case 'cGtaDes':
                              $qGruTar .= "gtadesxx = \"$gGtaDes\" AND ";
                            break;
                          }
                          $qGruTar .= "regestxx = \"ACTIVO\" ORDER BY gtaidxxx";
                          echo "<pre>";
                          print_r($qGruTar);
                          $xGruTar  = f_MySql("SELECT","",$qGruTar,$xConexion01,"");

                          if (mysql_num_rows($xGruTar) == 1) {
                            while ($xRGT = mysql_fetch_array($xGruTar)) { ?>
                              <script language="javascript">
                                parent.fmwork.document.forms['frnav']['cGtaId'].value  = '<?php echo $xRGT['gtaidxxx'] ?>';
                                parent.fmwork.document.forms['frnav']['cGtaDes'].value = '<?php echo $xRGT['gtadesxx'] ?>';
                              </script>
                            <?php }
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
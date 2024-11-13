<?php
  /**
   * Parametrica de Linea de Negocio
   * @author Elian Amado <elian.amado@openits.co>
   * @package opentecnologia
   */
include("../../../../libs/php/utility.php");

if ($gWhat != "" && $gFunction != "") { ?>
<html>
  <head>
    <title>Parametrica de Unidades de Medida </title>
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
    <link rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    </script>
  </head>
  <body topmargin="0" leftmargin="0" marginwidth="0" marginheight="0" style="margin-right:0">
    <center>
      <table border ="0" cellpadding="0" cellspacing="0" width="300">
        <tr>
          <td>
            <fieldset>
              <legend>Param&eacute;trica de Linea de Negocio</legend>
              <form name="frgrm" action="" method="post" target="fmpro">
                <?php
                  switch ($gWhat) {
                    case "WINDOW":
                      $qUniMed  = "SELECT ";
                      $qUniMed .= "umeidxxx, ";
                      $qUniMed .= "umedesxx ";
                      $qUniMed .= "FROM $cAlfa.fpar0157 ";
                      $qUniMed .= "WHERE ";
                      $qUniMed .= "umeidxxx LIKE \"%$gCodLineaNeg%\" AND ";
                      $qUniMed .= "regestxx = \"ACTIVO\" ";
                      $qUniMed .= "ORDER BY abs(umeidxxx) ASC";
                      $xUniMed  = f_MySql("SELECT","",$qUniMed,$xConexion01,"");
                      // f_Mensaje(__FILE__,__LINE__,$qUniMed."~".mysql_num_rows($xUniMed));

                      if (mysql_num_rows($xUniMed) > 0) { ?>
                        <center>
                          <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                            <tr bgcolor = '#D6DFF7'>
                              <td widht = "080" Class = "name"><center>C&oacute;digo</center></td>
                              <td widht = "420" Class = "name"><center>Descripci&oacute;n</center></td>
                            </tr>
                            <?php 
                            while ($xRMP = mysql_fetch_array($xUniMed)){?>
                              <tr>
                                <?php
                                switch($gFunction){
                                  case "cCodLineaNeg": ?>
                                    <td width = "050" class= "name" style = "text-align:center">
                                      <a href = "javascript:window.opener.document.forms['frgrm']['cCodLineaNeg'+'<?php echo $gSecuencia ?>'].value = '<?php echo $xRMP['umeidxxx']?>';
                                                            window.opener.document.forms['frgrm']['cDesLineaNeg'+'<?php echo $gSecuencia ?>'].value = '<?php echo $xRMP['umedesxx']?>';
                                        window.opener.f_Links('cCodLineaNeg','EXACT');
                                        window.close();"><?php echo $xRMP['umeidxxx'] ?></a>
                                    </td>
                                    <?php
                                  break;
                                }?>
                                <td width = "400" class= "name"><?php echo $xRMP['umedesxx'] ?></td>
                              </tr>
                              <?php 
                            }?>
                          </table>
                        </center>
                        <?php
                      }else{
                        f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
                      }
                    break;
                    case "VALID":
                      $qUniMed  = "SELECT ";
                      $qUniMed .= "umeidxxx, ";
                      $qUniMed .= "umedesxx ";
                      $qUniMed .= "FROM $cAlfa.fpar0157 ";
                      $qUniMed .= "WHERE ";
                      $qUniMed .= "umeidxxx LIKE \"%$gCodLineaNeg%\" AND ";
                      $qUniMed .= "regestxx = \"ACTIVO\" ";
                      $qUniMed .= "ORDER BY abs(umeidxxx) ASC";
                      $xUniMed  = f_MySql("SELECT","",$qUniMed,$xConexion01,"");
                      // f_Mensaje(__FILE__,__LINE__,$qUniMed."~".mysql_num_rows($xUniMed));

                      if (mysql_num_rows($xUniMed) > 0){
                        if (mysql_num_rows($xUniMed) == 1){
                          while ($xRMP = mysql_fetch_array($xUniMed)) {
                            switch ($gFunction){
                              case "cCodLineaNeg": ?>
                                <script language = "javascript">
                                  parent.fmwork.document.forms['frgrm']['cCodLineaNeg'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRMP['umeidxxx'] ?>";
                                  parent.fmwork.f_Links('<?php echo $gFunction ?>','EXACT');
                                </script>
                                <?php
                              break;
                            }
                          }
                        } else{ ?>
                          <script language = "javascript">
                            parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW');
                          </script>
                        <?php }
                      }else{
                        switch ($gFunction){
                          case "cCodLineaNeg": ?>
                            <script language = "javascript">
                              alert('No hay registros coincidentes');
                              parent.fmwork.document.forms['frgrm']['cCodLineaNeg'+'<?php echo $gSecuencia ?>'].value = "";
                            </script>
                            <?php
                          break;
                        }
                      }
                    break;
                    case "EXACT":
                      $qUniMed  = "SELECT ";
                      $qUniMed .= "umeidxxx, ";
                      $qUniMed .= "umedesxx ";
                      $qUniMed .= "FROM $cAlfa.fpar0157 ";
                      $qUniMed .= "WHERE ";
                      $qUniMed .= "umeidxxx = \"$gCodLineaNeg\" AND ";
                      $qUniMed .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
                      $xUniMed  = f_MySql("SELECT","",$qUniMed,$xConexion01,"");
                      // f_Mensaje(__FILE__,__LINE__,$qUniMed."~ ".mysql_num_rows($xUniMed));

                      $vUniMed = mysql_fetch_array($xUniMed);
                      switch ($gFunction){
                        case "cCodLineaNeg": ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cCodLineaNeg'+'<?php echo $gSecuencia ?>'].value = "<?php echo $vUniMed['umeidxxx'] ?>";
                            parent.fmwork.document.forms['frgrm']['cDesLineaNeg'+'<?php echo $gSecuencia ?>'].value = "<?php echo str_replace('"','\"',$vUniMed['umedesxx']) ?>";
                          </script>
                          <?php
                        break;
                      }
                    break;
                  } ?>
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
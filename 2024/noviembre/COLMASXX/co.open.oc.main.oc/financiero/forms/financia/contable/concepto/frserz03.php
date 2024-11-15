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
                      $qLinNeg  = "SELECT ";
                      $qLinNeg .= "lnecodxx, ";
                      $qLinNeg .= "lnedesxx ";
                      $qLinNeg .= "FROM $cAlfa.zcol0003 ";
                      $qLinNeg .= "WHERE ";
                      $qLinNeg .= "lnecodxx LIKE \"%$gCodLineaNeg%\" AND ";
                      $qLinNeg .= "regestxx = \"ACTIVO\" ";
                      $qLinNeg .= "ORDER BY abs(lnecodxx) ASC";
                      $xLinNeg  = f_MySql("SELECT","",$qLinNeg,$xConexion01,"");
                      // f_Mensaje(__FILE__,__LINE__,$qLinNeg."~".mysql_num_rows($xLinNeg));

                      if (mysql_num_rows($xLinNeg) > 0) { ?>
                        <center>
                          <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                            <tr bgcolor = '#D6DFF7'>
                              <td widht = "080" Class = "name"><center>C&oacute;digo</center></td>
                              <td widht = "420" Class = "name"><center>Descripci&oacute;n</center></td>
                            </tr>
                            <?php 
                            while ($xRMP = mysql_fetch_array($xLinNeg)){
                              if (mysql_num_rows($xLinNeg) > 1) { ?>
                              <tr>
                                <?php
                                switch($gFunction){
                                  case "cCodLineaNeg": ?>
                                    <td width = "050" class= "name" style = "text-align:center">
                                      <a href = "javascript:window.opener.document.forms['frgrm']['cCodLineaNeg'+'<?php echo $gSecuencia ?>'].value = '<?php echo $xRMP['lnecodxx']?>';
                                                            window.opener.document.forms['frgrm']['cDesLineaNeg'+'<?php echo $gSecuencia ?>'].value = '<?php echo $xRMP['lnedesxx']?>';
                                                            window.opener.f_Links('cCodLineaNeg','EXACT', '<?php echo $gSecuencia ?>');
                                                            window.close();"><?php echo $xRMP['lnecodxx'] ?></a>
                                    </td>
                                    <?php
                                  break;
                                }?>
                                <td width = "400" class= "name"><?php echo $xRMP['lnedesxx'] ?></td>
                              </tr>
                              <?php 
                              } else { ?>
                                <script language = "javascript">
                                  window.opener.document.forms['frgrm']['cCodLineaNeg'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRMP['lnecodxx'] ?>";
                                  window.opener.document.forms['frgrm']['cDesLineaNeg'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRMP['lnedesxx'] ?>";
                                  window.opener.f_Links('<?php echo $gFunction ?>','EXACT', '<?php echo $gSecuencia ?>');
                                  window.close();
                                </script>
                              <?php 
                              }
                            } ?>
                          </table>
                        </center>
                        <?php
                      }else{
                        f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); ?>
                        <script language="javascript">
                          window.close();
                        </script>
                        <?php
                      }
                    break;
                    case "VALID":
                      $qLinNeg  = "SELECT ";
                      $qLinNeg .= "lnecodxx, ";
                      $qLinNeg .= "lnedesxx ";
                      $qLinNeg .= "FROM $cAlfa.zcol0003 ";
                      $qLinNeg .= "WHERE ";
                      $qLinNeg .= "lnecodxx LIKE \"%$gCodLineaNeg%\" AND ";
                      $qLinNeg .= "regestxx = \"ACTIVO\" ";
                      $qLinNeg .= "ORDER BY abs(lnecodxx) ASC";
                      $xLinNeg  = f_MySql("SELECT","",$qLinNeg,$xConexion01,"");
                      // f_Mensaje(__FILE__,__LINE__,$qLinNeg."~".mysql_num_rows($xLinNeg));

                      if (mysql_num_rows($xLinNeg) > 0){
                        if (mysql_num_rows($xLinNeg) == 1){
                          while ($xRMP = mysql_fetch_array($xLinNeg)) {
                            switch ($gFunction){
                              case "cCodLineaNeg": ?>
                                <script language = "javascript">
                                  parent.fmwork.document.forms['frgrm']['cCodLineaNeg'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRMP['lnecodxx'] ?>";
                                  parent.fmwork.document.forms['frgrm']['cDesLineaNeg'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRMP['lnedesxx'] ?>";
                                  parent.fmwork.f_Links('<?php echo $gFunction ?>','EXACT', '<?php echo $gSecuencia ?>');
                                </script>
                                <?php
                              break;
                            }
                          }
                        } else{ ?>
                          <script language = "javascript">
                            parent.fmwork.f_Links('<?php echo $gFunction ?>','WINDOW', '<?php echo $gSecuencia ?>');
                            window.close();
                          </script>
                        <?php }
                      }else{
                        switch ($gFunction){
                          case "cCodLineaNeg": ?>
                            <script language = "javascript">
                              alert('No hay registros coincidentes');
                              parent.fmwork.document.forms['frgrm']['cCodLineaNeg'+'<?php echo $gSecuencia ?>'].value = "";
                              parent.fmwork.document.forms['frgrm']['cDesLineaNeg'+'<?php echo $gSecuencia ?>'].value = "";
                            </script>
                            <?php
                          break;
                        }
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
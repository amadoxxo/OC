<?php
  /**
   * --- Descripcion: Consulta Pedido:
   * @author Elian Amado Ramirez <elian.amado@openits.co>
   * @package openComex
   * @version 001
   */

include("../../../../../financiero/libs/php/utility.php");

$cAnioIni = (date('Y') - 2);

if ($gWhat != "" && $gFunction != "") { ?>
  <html>
    <head>
      <title>Param&eacute;trica de Pedido</title>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
    <script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
    </head>
    <body topmargin = 0 leftmargin = 0 marginwidth = 0 marginheight = 0 style = 'margin-right:0'>
      <center>
        <table border ="0" cellpadding="0" cellspacing="0" width="300">
          <tr>
            <td>
              <fieldset>
                <legend>Param&eacute;trica de Pedido</legend>
                <form name = "frgrm" action = "" method = "post" target = "fmpro">
                  <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        // Traigo Datos del Pedido
                        $mDatos = array();
                        for ($nAnio=$cAnioIni;$nAnio<=date('Y');$nAnio++) {
                          $qPedCab  = "SELECT ";
                          $qPedCab .= "$cAlfa.lpca$nAnio.*, ";
                          $qPedCab .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,\"CLIENTE SIN NOMBRE\") AS clinomxx ";
                          $qPedCab .= "FROM $cAlfa.lpca$nAnio ";
                          $qPedCab .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lpca$nAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
                          $qPedCab .= "WHERE ";
                          if ($gCliId != "") {
                            $qPedCab .= "$cAlfa.lpca$nAnio.cliidxxx LIKE \"%$gCliId%\" AND ";
                          }
                          $qPedCab .= "$cAlfa.lpca$nAnio.regestxx IN (\"PROVISIONAL\") ";
                          $qPedCab .= "ORDER BY ABS($cAlfa.lpca$nAnio.comcscxx) ASC ";
                          $xPedCab = f_MySql("SELECT","",$qPedCab,$xConexion01,"");
                          // f_Mensaje(__FILE__,__LINE__,$qPedCab."~".mysql_num_rows($xPedCab));
                          if (mysql_num_rows($xPedCab) > 0) {
                            while($xRCC = mysql_fetch_array($xPedCab)) {
                              $nInd_mDatos = count($mDatos);
                              $mDatos[$nInd_mDatos] = $xRCC;
                              $mDatos[$nInd_mDatos]['cceranox'] = $nAnio;
                            }
                          }
                        }
                        if (count($mDatos) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "470">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "050" Class = "name"><center>ID</center></td>
                                <td width = "110" Class = "name"><center>Cliente</center></td>
                              </tr>
                              <?php 
                              for ($i=0; $i < count($mDatos); $i++) {
                                if (mysql_num_rows($xPedCab) > 1) { ?>
                                  <tr>
                                    <td width = "100" class= "name">
                                      <a href = "javascript:window.opener.document.forms['frgrm']['cPedComCsc'+'<?php echo $gSecuencia ?>'].value = '<?php echo $mDatos[$i]['comidxxx']."-".$mDatos[$i]['comprexx']."-".$mDatos[$i]['comcsc2x'] ?>';
                                                            window.opener.document.forms['frgrm']['cPedIds'   +'<?php echo $gSecuencia ?>'].value = '<?php echo $mDatos[$i]['pedidxxx'] ?>';
                                                            window.opener.document.forms['frgrm']['cAnio'     +'<?php echo $gSecuencia ?>'].value = '<?php echo substr($mDatos[$i]['comfecxx'], 0, 4) ?>';
                                                            window.opener.fnLinks('<?php echo $gFunction ?>','EXACT','<?php echo $gSecuencia ?>');
                                                            window.close();">
                                                            <?php echo $mDatos[$i]['comidxxx']."-".$mDatos[$i]['comprexx']."-".$mDatos[$i]['comcsc2x'] ?>
                                      </a>
                                    </td>
                                    <td width = "110" class= "name"> <?php echo $mDatos[$i]['clinomxx'] ?></td>
                                  </tr>
                                <?php
                                }else{ ?>
                                  <script language="javascript">
                                    window.opener.document.forms['frgrm']['cPedComCsc'+'<?php echo $gSecuencia ?>'].value = '<?php echo $mDatos[$i]['comidxxx']."-".$mDatos[$i]['comprexx']."-".$mDatos[$i]['comcsc2x'] ?>';
                                    window.opener.document.forms['frgrm']['cPedIds'   +'<?php echo $gSecuencia ?>'].value = '<?php echo $mDatos[$i]['pedidxxx'] ?>';
                                    window.opener.document.forms['frgrm']['cAnio'  +'<?php echo $gSecuencia ?>'].value = '<?php echo substr($mDatos[$i]['comfecxx'], 0, 4) ?>';
                                    window.close();
                                  </script>
                                <?php 
                                }
                              } ?>
                            </table>
                          </center>
                        <?php
                        } else {
                          f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros"); ?>
                          <script language="javascript">
                            window.opener.document.forms['frgrm']['cPedComCsc'+'<?php echo $gSecuencia ?>'].value = "";
                            window.opener.document.forms['frgrm']['cPedIds'+'<?php echo $gSecuencia ?>'].value    = "";
                            window.opener.document.forms['frgrm']['cAnio'+'<?php echo $gSecuencia ?>'].value   = "";
                            window.close();
                          </script>
                        <?php 
                        }
                      break;
                      case "VALID":
                        // Traigo Datos del Pedido
                        $mDatos = array();
                        for ($nAnio=$cAnioIni;$nAnio<=date('Y');$nAnio++) {
                          $qPedCab  = "SELECT ";
                          $qPedCab .= "$cAlfa.lpca$nAnio.*, ";
                          $qPedCab .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,\"CLIENTE SIN NOMBRE\") AS clinomxx ";
                          $qPedCab .= "FROM $cAlfa.lpca$nAnio ";
                          $qPedCab .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lpca$nAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
                          $qPedCab .= "WHERE ";
                          if ($gCliId != "") {
                            $qPedCab .= "$cAlfa.lpca$nAnio.cliidxxx LIKE \"%$gCliId%\" AND ";
                          }
                          $qPedCab .= "$cAlfa.lpca$nAnio.regestxx IN (\"PROVISIONAL\") ";
                          $xPedCab = f_MySql("SELECT","",$qPedCab,$xConexion01,"");
                          // f_Mensaje(__FILE__,__LINE__,$qPedCab."~".mysql_num_rows($xPedCab));
                          if (mysql_num_rows($xPedCab) > 0) {
                            while($xRCC = mysql_fetch_array($xPedCab)) {
                              $nInd_mDatos = count($mDatos);
                              $mDatos[$nInd_mDatos] = $xRCC;
                              $mDatos[$nInd_mDatos]['cceranox'] = $nAnio;
                            }
                          }
                        }
                        if (count($mDatos) > 0){
                          if (count($mDatos) == 1){
                            for ($i=0; $i<count($mDatos); $i++) { 
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm']['cPedComCsc'+'<?php echo $gSecuencia ?>'].value = '<?php echo $mDatos[$i]['comidxxx']."-".$mDatos[$i]['comprexx']."-".$mDatos[$i]['comcsc2x'] ?>';
                                parent.fmwork.document.forms['frgrm']['cPedIds'   +'<?php echo $gSecuencia ?>'].value = '<?php echo $mDatos[$i]['pedidxxx'] ?>';
                                parent.fmwork.document.forms['frgrm']['cAnio'  +'<?php echo $gSecuencia ?>'].value = '<?php echo substr($mDatos[$i]['comfecxx'], 0, 4) ?>';
                              </script>
                              <?php
                            }
                          } else { ?>
                            <script language = "javascript">
                              parent.fmwork.fnLinks('<?php echo $gFunction ?>','WINDOW', '<?php echo $gSecuencia ?>');
                              window.close();
                            </script>
                            <?php
                          }
                        }else{ 
                          f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");
                          ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cPedComCsc'+'<?php echo $gSecuencia ?>'].value = "";
                            parent.fmwork.document.forms['frgrm']['cPedIds'+'<?php echo $gSecuencia ?>'].value    = "";
                            parent.fmwork.document.forms['frgrm']['cAnio'+'<?php echo $gSecuencia ?>'].value   = "";
                          </script>
                          <?php
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
  f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
} 
?>
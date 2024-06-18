<?php
  /**
   * --- Descripcion: Consulta los Depósitos: 
   * @author Elian Amado. <elian.amado@openits.co>
   * @package openComex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");

  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Dep&oacute;sito</title>
        <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
        <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
        <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
        <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
        <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
      </head>
      <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
        <center>
          <table border ="0" cellpadding="0" cellspacing="0" width="300">
            <tr>
              <td>
                <fieldset>
                  <legend>Param&eacute;trica de Dep&oacute;sito</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qDeposito  = "SELECT ";
                        $qDeposito .= "lpar0155.depnumxx, ";
                        $qDeposito .= "lpar0007.tdeidxxx, ";
                        $qDeposito .= "lpar0007.tdedesxx, ";
                        $qDeposito .= "lpar0155.regestxx ";
                        $qDeposito .= "FROM $cAlfa.lpar0155 ";
                        $qDeposito .= "LEFT JOIN $cAlfa.lpar0007 ON $cAlfa.lpar0155.tdeidxxx = $cAlfa.lpar0007.tdeidxxx ";
                        $qDeposito .= "WHERE ";
                        $qDeposito .= "lpar0155.cliidxxx = \"$gCliId\" AND ";
                        $qDeposito .= "lpar0155.depnumxx LIKE \"%$gDepNum%\" AND ";
                        $qDeposito .= "lpar0155.regestxx = \"ACTIVO\" ";
                        $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qDeposito."~".mysql_num_rows($xDeposito));

                        if (mysql_num_rows($xDeposito) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "050" Class = "name"><center>Id</center></td>
                                <td width = "400" Class = "name"><center>Tipo Dep&oacute;sito</center></td>
                              </tr>
                                <?php
                                while ($xRDE = mysql_fetch_array($xDeposito)){
                                  if (mysql_num_rows($xDeposito) > 1) { ?>
                                    <tr>
                                      <td width = "050" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm']['cDepNum'].value = '<?php echo $xRDE['depnumxx']?>';
                                                                window.opener.fnLinks('cDepNum','EXACT',0);
                                                                window.close();"><?php echo $xRDE['depnumxx'] ?></a>
                                      </td>
                                      <td width = "400" class= "name"><?php echo $xRDE['tdedesxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm']['cDepNum'].value = "<?php echo $xRDE['depnumxx'] ?>";
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
                        $qDeposito  = "SELECT ";
                        $qDeposito .= "lpar0155.depnumxx, ";
                        $qDeposito .= "lpar0155.regestxx ";
                        $qDeposito .= "FROM $cAlfa.lpar0155 ";
                        $qDeposito .= "WHERE ";
                        $qDeposito .= "lpar0155.cliidxxx = \"$gCliId\" AND ";
                        $qDeposito .= "lpar0155.depnumxx LIKE \"%$gDepNum%\" AND ";
                        $qDeposito .= "lpar0155.regestxx = \"ACTIVO\" ";
                        $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qDeposito."~".mysql_num_rows($xDeposito));
                        if (mysql_num_rows($xDeposito) > 0){
                          if (mysql_num_rows($xDeposito) == 1){
                            while ($xRDE = mysql_fetch_array($xDeposito)) { 
                              $gDepNum = $xRDE['depnumxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm']['cDepNum'].value = "<?php echo $xRDE['depnumxx'] ?>";
                                parent.fmwork.fnLinks('cDepNum','EXACT',0);
                              </script>
                            <?php }
                          }else{ ?>
                            <script language = "javascript">
                              parent.fmwork.fnLinks('<?php echo $gFunction ?>','WINDOW');
                              window.close();
                            </script>
                          <?php
                          }
                        }else{ ?>
                          <script language = "javascript">
                            alert('No hay registros coincidentes');
                            parent.fmwork.document.forms['frgrm']['cDepNum'].value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qDeposito  = "SELECT ";
                        $qDeposito .= "lpar0155.depnumxx, ";
                        $qDeposito .= "lpar0155.regestxx ";
                        $qDeposito .= "FROM $cAlfa.lpar0155 ";
                        $qDeposito .= "WHERE ";
                        $qDeposito .= "lpar0155.cliidxxx = \"$gCliId\" AND ";
                        $qDeposito .= "lpar0155.depnumxx = \"$gDepNum\" AND ";
                        $qDeposito .= "lpar0155.regestxx = \"ACTIVO\" ";
                        $qDeposito .= "ORDER BY lpar0155.depnumxx LIMIT 0,1 ";
                        $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
                        if (mysql_num_rows($xDeposito) > 0) {
                          $vDeposito = mysql_fetch_array($xDeposito); ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cDepNum'].value = "<?php echo $vDeposito['depnumxx'] ?>";
                          </script>
                          <?php
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
  <?php
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos.");
  } 
?>
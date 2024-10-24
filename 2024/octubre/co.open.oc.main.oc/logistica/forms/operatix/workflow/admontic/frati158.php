<?php
  namespace openComex;
  /**
   * --- Descripcion: Consulta los Tipos de Tickets:
   * @author Cristian Perdomo <cristian.perdomo@openits.co>
   * @package openComex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Tipo Ticket</title>
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
                  <legend>Param&eacute;trica de Tipo Ticket</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qTipTic  = "SELECT ";
                        $qTipTic .= "tticodxx, ";
                        $qTipTic .= "ttidesxx ";
                        $qTipTic .= "FROM $cAlfa.lpar0158 ";
                        $qTipTic .= "WHERE ";
                        switch ($gFunction) {
                          case 'cTipId':
                            $qTipTic .= "tticodxx LIKE \"%$gTipId%\" AND ";
                          break;
                          case 'cTipDes':
                            $qTipTic .= "ttidesxx LIKE \"%$gTipId%\" AND ";
                          break;
                        }
                        $qTipTic .= "regestxx = \"ACTIVO\"";
                        $xTipTic  = f_MySql("SELECT","",$qTipTic,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qTipTic."~".mysql_num_rows($xTipTic));
                        if (mysql_num_rows($xTipTic) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "050" Class = "name"><center>Codigo</center></td>
                                <td width = "450" Class = "name"><center>Descripcion</center></td>
                              </tr>
                                <?php
                                while ($xRDC = mysql_fetch_array($xTipTic)){
                                  if (mysql_num_rows($xTipTic) > 1) { ?>
                                    <tr>
                                      <td width = "050" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm']['cTipId'].value  = '<?php echo $xRDC['tticodxx']?>';
                                                                window.opener.document.forms['frgrm']['cTipDes'].value = '<?php echo $xRDC['ttidesxx']?>';
                                                                window.close();"><?php echo $xRDC['tticodxx'] ?></a>
                                      </td>
                                      <td width = "450" class= "name"><?php echo $xRDC['ttidesxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm']['cTipId'].value  = "<?php echo $xRDC['tticodxx'] ?>";
                                      window.opener.document.forms['frgrm']['cTipDes'].value = "<?php echo $xRDC['ttidesxx'] ?>";
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
                            window.opener.document.forms['frgrm']['cTipId'].value  = "";
                            window.opener.document.forms['frgrm']['cTipDes'].value = "";
                            window.close();
                          </script>
                        <?php
                        }
                      break;
                      case "VALID":
                        $qTipTic  = "SELECT ";
                        $qTipTic .= "tticodxx, ";
                        $qTipTic .= "ttidesxx ";
                        $qTipTic .= "FROM $cAlfa.lpar0158 ";
                        $qTipTic .= "WHERE ";
                        switch ($gFunction) {
                          case 'cTipId':
                            $qTipTic .= "tticodxx LIKE \"%$gTipId%\" AND ";
                          break;
                          case 'cTipDes':
                            $qTipTic .= "ttidesxx LIKE \"%$gTipId%\" AND ";
                          break;
                        }
                        $qTipTic .= "regestxx = \"ACTIVO\"";
                        $xTipTic  = f_MySql("SELECT","",$qTipTic,$xConexion01,"");
                        if (mysql_num_rows($xTipTic) == 1){
                          while ($xRDC = mysql_fetch_array($xTipTic)) { 
                            ?>
                            <script language = "javascript">
                              parent.fmwork.document.forms['frgrm']['cTipId'].value  = "<?php echo $xRDC['tticodxx'] ?>";
                              parent.fmwork.document.forms['frgrm']['cTipDes'].value = "<?php echo $xRDC['ttidesxx'] ?>";
                            </script>
                          <?php }
                        }else{ ?>
                          <script language = "javascript">
                            parent.fmwork.fnLinks('<?php echo $gFunction ?>','WINDOW');
                            window.close();
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
  } ?>
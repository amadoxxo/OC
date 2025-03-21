<?php
  namespace openComex;
	/**
	 * --- Descripcion: Consulta los Sectores:
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */
	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Sector</title>
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
                  <legend>Param&eacute;trica de Sectores</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qSectores  = "SELECT ";
                        $qSectores .= "secsapxx, ";
                        $qSectores .= "secdesxx, ";
                        $qSectores .= "regestxx ";
                        $qSectores .= "FROM $cAlfa.lpar0009 ";                        
                        $qSectores .= "WHERE ";
                        if ($gSecSap != "") {
                          $qSectores .= "secsapxx LIKE \"%$gSecSap%\" AND ";
                        }
                        if ($gSecDes != "") {
                          $qSectores .= "secdesxx LIKE \"%$gSecDes%\" AND ";
                        }
                        $qSectores .= "regestxx = \"ACTIVO\" ";
                        $qSectores .= "ORDER BY secsapxx ";
                        $xCentroLog  = f_MySql("SELECT","",$qSectores,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qSectores."~".mysql_num_rows($xCentroLog));

                        if (mysql_num_rows($xCentroLog) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "100" Class = "name"><center>Cod. SAP</center></td>
                                <td width = "350" Class = "name"><center>Descripci&oacute;n</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRCL = mysql_fetch_array($xCentroLog)){
                                  if (mysql_num_rows($xCentroLog) > 1) { ?>
                                    <tr>
                                      <td width = "100" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm'].cSecSap.value = '<?php echo $xRCL['secsapxx']?>';
                                                                window.opener.document.forms['frgrm'].cSecDes.value = '<?php echo $xRCL['secdesxx']?>';
                                                                window.opener.fnLinks('cSecSap','EXACT',0);
                                                                window.close();"><?php echo $xRCL['secsapxx'] ?></a>
                                      </td>
                                      <td width = "350" class= "name"><?php echo $xRCL['secdesxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xRCL['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm'].cSecSap.value = "<?php echo $xRCL['secsapxx'] ?>";
                                      window.opener.document.forms['frgrm'].cSecDes.value = "<?php echo $xRCL['secdesxx'] ?>";
                                      window.opener.fnLinks('cSecSap', 'EXACT', 0);
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
                        $qSectores  = "SELECT ";
                        $qSectores .= "secsapxx, ";
                        $qSectores .= "secdesxx, ";
                        $qSectores .= "regestxx ";
                        $qSectores .= "FROM $cAlfa.lpar0009 ";                        
                        $qSectores .= "WHERE ";
                        if ($gSecSap != "") {
                          $qSectores .= "secsapxx LIKE \"%$gSecSap%\" AND ";
                        }
                        if ($gSecDes != "") {
                          $qSectores .= "secdesxx LIKE \"%$gSecDes%\" AND ";
                        }
                        $qSectores .= "regestxx = \"ACTIVO\" ";
                        $qSectores .= "ORDER BY secsapxx ";
                        $xCentroLog  = f_MySql("SELECT","",$qSectores,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qSectores."~".mysql_num_rows($xCentroLog));

                        if (mysql_num_rows($xCentroLog) > 0){
                          if (mysql_num_rows($xCentroLog) == 1){
                            while ($xRCL = mysql_fetch_array($xCentroLog)) { 
                              $gSecSap = $xRCL['secsapxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm'].cSecSap.value = "<?php echo $xRCL['secsapxx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cSecDes.value = "<?php echo $xRCL['secdesxx'] ?>";
                                parent.fmwork.fnLinks('cSecSap','EXACT',0);
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
                            parent.fmwork.document.forms['frgrm'].cSecSap.value = "";
                            parent.fmwork.document.forms['frgrm'].cSecDes.value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qSectores  = "SELECT ";
                        $qSectores .= "secsapxx, ";
                        $qSectores .= "secdesxx, ";
                        $qSectores .= "regestxx ";
                        $qSectores .= "FROM $cAlfa.lpar0009 ";                        
                        $qSectores .= "WHERE ";
                        if ($gSecSap != "") {
                          $qSectores .= "secsapxx = \"$gSecSap\" AND ";
                        }
                        if ($gSecDes != "") {
                          $qSectores .= "secdesxx = \"$gSecDes\" AND ";
                        }
                        $qSectores .= "regestxx = \"ACTIVO\" ";
                        $qSectores .= "ORDER BY secsapxx ";
                        $xCentroLog  = f_MySql("SELECT","",$qSectores,$xConexion01,"");
                        while ($xRCL = mysql_fetch_array($xCentroLog)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm'].cSecSap.value = "<?php echo $xRCL['secsapxx'] ?>";
                            parent.fmwork.document.forms['frgrm'].cSecDes.value = "<?php echo $xRCL['secdesxx'] ?>";
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
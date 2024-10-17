<?php
  namespace openComex;
	/**
	 * --- Descripcion: Consulta las condiciones comerciales de un cliente: 
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */
	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Condiciones Comerciales</title>
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
                  <legend>Param&eacute;trica de Condiciones Comerciales</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qCondiCom  = "SELECT ";
                        $qCondiCom .= "ccoidocx, ";
                        $qCondiCom .= "cliidxxx, ";
                        $qCondiCom .= "ccotipxx, ";
                        $qCondiCom .= "regestxx ";
                        $qCondiCom .= "FROM $cAlfa.lpar0151 ";                        
                        $qCondiCom .= "WHERE ";
                        $qCondiCom .= "ccoidocx LIKE \"%$gCcoIdOc%\" AND ";
                        $qCondiCom .= "cliidxxx = \"$gCliId\" AND ";
                        $qCondiCom .= "regestxx = \"ACTIVO\" ";
                        $qCondiCom .= "ORDER BY ccoidocx ";
                        $xCondiCom  = f_MySql("SELECT","",$qCondiCom,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qCondiCom."~".mysql_num_rows($xCondiCom));

                        if (mysql_num_rows($xCondiCom) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "150" Class = "name"><center>Id Oferta Comercial</center></td>
                                <td width = "150" Class = "name"><center>Nit Cliente</center></td>
                                <td width = "150" Class = "name"><center>Tipo</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRCC = mysql_fetch_array($xCondiCom)){
                                  if (mysql_num_rows($xCondiCom) > 1) { ?>
                                    <tr>
                                      <td width = "150" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm'].cCcoIdOc.value = '<?php echo $xRCC['ccoidocx']?>';
                                                                window.opener.fnLinks('cCcoIdOc','EXACT',0);
                                                                window.close();"><?php echo $xRCC['ccoidocx'] ?></a>
                                      </td>
                                      <td width = "150" class= "name"><?php echo $xRCC['cliidxxx'] ?></td>
                                      <td width = "150" class= "name"><?php echo str_replace('_', ' ', $xRCC['ccotipxx']) ?></td>
                                      <td width = "050" class= "name"><?php echo $xRCC['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm'].cCcoIdOc.value = "<?php echo $xRCC['ccoidocx'] ?>";
                                      window.opener.fnLinks('cCcoIdOc', 'EXACT', 0);
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
                        $qCondiCom  = "SELECT ";
                        $qCondiCom .= "ccoidocx, ";
                        $qCondiCom .= "cliidxxx, ";
                        $qCondiCom .= "ccotipxx, ";
                        $qCondiCom .= "regestxx ";
                        $qCondiCom .= "FROM $cAlfa.lpar0151 ";                        
                        $qCondiCom .= "WHERE ";
                        $qCondiCom .= "ccoidocx LIKE \"%$gCcoIdOc%\" AND ";
                        $qCondiCom .= "cliidxxx = \"$gCliId\" AND ";
                        $qCondiCom .= "regestxx = \"ACTIVO\" ";
                        $qCondiCom .= "ORDER BY ccoidocx ";
                        $xCondiCom  = f_MySql("SELECT","",$qCondiCom,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qCondiCom."~".mysql_num_rows($xCondiCom));

                        if (mysql_num_rows($xCondiCom) > 0){
                          if (mysql_num_rows($xCondiCom) == 1){
                            while ($xRCC = mysql_fetch_array($xCondiCom)) { 
                              $gCcoIdOc = $xRCC['ccoidocx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm'].cCcoIdOc.value = "<?php echo $xRCC['ccoidocx'] ?>";
                                parent.fmwork.fnLinks('cCcoIdOc','EXACT',0);
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
                            parent.fmwork.document.forms['frgrm'].cCcoIdOc.value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qCondiCom  = "SELECT ";
                        $qCondiCom .= "ccoidocx, ";
                        $qCondiCom .= "cliidxxx, ";
                        $qCondiCom .= "ccotipxx, ";
                        $qCondiCom .= "regestxx ";
                        $qCondiCom .= "FROM $cAlfa.lpar0151 ";                        
                        $qCondiCom .= "WHERE ";
                        $qCondiCom .= "ccoidocx =\"$gCcoIdOc\" AND ";
                        $qCondiCom .= "cliidxxx = \"$gCliId\" AND ";
                        $qCondiCom .= "regestxx = \"ACTIVO\" ";
                        $qCondiCom .= "ORDER BY ccoidocx ";
                        $xCondiCom  = f_MySql("SELECT","",$qCondiCom,$xConexion01,"");
                        while ($xRCC = mysql_fetch_array($xCondiCom)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm'].cCcoIdOc.value = "<?php echo $xRCC['ccoidocx'] ?>";

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
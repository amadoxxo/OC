<?php
	/**
	 * --- Descripcion: Consulta los Subservicios:
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */
	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Subservicios</title>
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
                  <legend>Param&eacute;trica de Subservicios</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qSubservicio  = "SELECT ";
                        $qSubservicio .= "subidxxx, ";
                        $qSubservicio .= "subdesxx, ";
                        $qSubservicio .= "regestxx ";
                        $qSubservicio .= "FROM $cAlfa.lpar0012 ";                        
                        $qSubservicio .= "WHERE ";
                        $qSubservicio .= "sersapxx = \"$gSerSap\" AND ";
                        if ($gSubId != "") {
                          $qSubservicio .= "subidxxx LIKE \"%$gSubId%\" AND ";
                        }
                        if ($gSubDes != "") {
                          $qSubservicio .= "subdesxx LIKE \"%$gSubDes%\" AND ";
                        }
                        $qSubservicio .= "regestxx = \"ACTIVO\" ";
                        $qSubservicio .= "ORDER BY subidxxx ";
                        $xSubservicio  = f_MySql("SELECT","",$qSubservicio,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qSubservicio."~".mysql_num_rows($xSubservicio));

                        if (mysql_num_rows($xSubservicio) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "100" Class = "name"><center>C&oacute;digo SAP</center></td>
                                <td width = "350" Class = "name"><center>Descripci&oacute;n</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRSE = mysql_fetch_array($xSubservicio)){
                                  if (mysql_num_rows($xSubservicio) > 1) { ?>
                                    <tr>
                                      <td width = "100" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm'].cSubId.value  = '<?php echo $xRSE['subidxxx']?>';
                                                                window.opener.document.forms['frgrm'].cSubDes.value = '<?php echo $xRSE['subdesxx']?>';
                                                                window.opener.fnLinks('cSubId','EXACT',0);
                                                                window.close();"><?php echo $xRSE['subidxxx'] ?></a>
                                      </td>
                                      <td width = "350" class= "name"><?php echo $xRSE['subdesxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xRSE['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm'].cSubId.value  = "<?php echo $xRSE['subidxxx'] ?>";
                                      window.opener.document.forms['frgrm'].cSubDes.value = "<?php echo $xRSE['subdesxx'] ?>";
                                      window.opener.fnLinks('cSubId', 'EXACT', 0);
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
                        $qSubservicio  = "SELECT ";
                        $qSubservicio .= "subidxxx, ";
                        $qSubservicio .= "subdesxx, ";
                        $qSubservicio .= "regestxx ";
                        $qSubservicio .= "FROM $cAlfa.lpar0012 ";
                        $qSubservicio .= "WHERE ";
                        $qSubservicio .= "sersapxx = \"$gSerSap\" AND ";
                        if ($gSubId != "") {
                          $qSubservicio .= "subidxxx LIKE \"%$gSubId%\" AND ";
                        }
                        if ($gSubDes != "") {
                          $qSubservicio .= "subdesxx LIKE \"%$gSubDes%\" AND ";
                        }
                        $qSubservicio .= "regestxx = \"ACTIVO\" ";
                        $qSubservicio .= "ORDER BY subidxxx ";
                        $xSubservicio  = f_MySql("SELECT","",$qSubservicio,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qSubservicio."~".mysql_num_rows($xSubservicio));

                        if (mysql_num_rows($xSubservicio) > 0){
                          if (mysql_num_rows($xSubservicio) == 1){
                            while ($xRSE = mysql_fetch_array($xSubservicio)) { 
                              $gSubId = $xRSE['subidxxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm'].cSubId.value  = "<?php echo $xRSE['subidxxx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cSubDes.value = "<?php echo $xRSE['subdesxx'] ?>";
                                parent.fmwork.fnLinks('cSubId','EXACT',0);
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
                            parent.fmwork.document.forms['frgrm'].cSubId.value  = "";
                            parent.fmwork.document.forms['frgrm'].cSubDes.value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qSubservicio  = "SELECT ";
                        $qSubservicio .= "subidxxx, ";
                        $qSubservicio .= "subdesxx, ";
                        $qSubservicio .= "regestxx ";
                        $qSubservicio .= "FROM $cAlfa.lpar0012 ";                        
                        $qSubservicio .= "WHERE ";
                        $qSubservicio .= "sersapxx = \"$gSerSap\" AND ";
                        if ($gSubId != "") {
                          $qSubservicio .= "subidxxx = \"$gSubId\" AND ";
                        }
                        if ($gSubDes != "") {
                          $qSubservicio .= "subdesxx = \"$gSubDes\" AND ";
                        }
                        $qSubservicio .= "regestxx = \"ACTIVO\" ";
                        $qSubservicio .= "ORDER BY subidxxx ";
                        $xSubservicio  = f_MySql("SELECT","",$qSubservicio,$xConexion01,"");
                        while ($xRSE = mysql_fetch_array($xSubservicio)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm'].cSubId.value  = "<?php echo $xRSE['subidxxx'] ?>";
                            parent.fmwork.document.forms['frgrm'].cSubDes.value = "<?php echo $xRSE['subdesxx'] ?>";
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
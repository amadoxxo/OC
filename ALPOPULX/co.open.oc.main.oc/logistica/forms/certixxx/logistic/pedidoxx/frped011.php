<?php
	/**
	 * --- Descripcion: Consulta los Servicios:
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */
	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Servicios</title>
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
                  <legend>Param&eacute;trica de Servicios</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qSercicio  = "SELECT ";
                        $qSercicio .= "sersapxx, ";
                        $qSercicio .= "serdesxx, ";
                        $qSercicio .= "regestxx ";
                        $qSercicio .= "FROM $cAlfa.lpar0011 ";                        
                        $qSercicio .= "WHERE ";
                        if ($gSerSap != "") {
                          $qSercicio .= "sersapxx LIKE \"%$gSerSap%\" AND ";
                        }
                        $qSercicio .= "regestxx = \"ACTIVO\" ";
                        $qSercicio .= "ORDER BY sersapxx ";
                        $xServicio  = f_MySql("SELECT","",$qSercicio,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qSercicio."~".mysql_num_rows($xServicio));

                        if (mysql_num_rows($xServicio) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "100" Class = "name"><center>Cod. SAP</center></td>
                                <td width = "350" Class = "name"><center>Descripci&oacute;n</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRSE = mysql_fetch_array($xServicio)){
                                  if (mysql_num_rows($xServicio) > 1) { ?>
                                    <tr>
                                      <td width = "100" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm']['cSerSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $xRSE['sersapxx']?>';
                                                                window.opener.document.forms['frgrm']['cSerDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $xRSE['serdesxx']?>';
                                                                window.opener.fnLinks('cSerDes','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
                                                                window.close();"><?php echo $xRSE['sersapxx'] ?></a>
                                      </td>
                                      <td width = "350" class= "name"><?php echo $xRSE['serdesxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xRSE['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm']['cSerSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRSE['sersapxx'] ?>";
                                      window.opener.document.forms['frgrm']['cSerDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRSE['serdesxx'] ?>";
                                      window.opener.fnLinks('cSerDes', 'EXACT', '<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
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
                        $qSercicio  = "SELECT ";
                        $qSercicio .= "sersapxx, ";
                        $qSercicio .= "serdesxx, ";
                        $qSercicio .= "regestxx ";
                        $qSercicio .= "FROM $cAlfa.lpar0011 ";                        
                        $qSercicio .= "WHERE ";
                        if ($gSerSap != "") {
                          $qSercicio .= "sersapxx LIKE \"%$gSerSap%\" AND ";
                        }
                        if ($gSecDes != "") {
                          $qSercicio .= "serdesxx LIKE \"%$gSecDes%\" AND ";
                        }
                        $qSercicio .= "regestxx = \"ACTIVO\" ";
                        $qSercicio .= "ORDER BY sersapxx ";
                        $xServicio  = f_MySql("SELECT","",$qSercicio,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qSercicio."~".mysql_num_rows($xServicio));

                        if (mysql_num_rows($xServicio) > 0){
                          if (mysql_num_rows($xServicio) == 1){
                            while ($xRSE = mysql_fetch_array($xServicio)) { 
                              $gSerSap = $xRSE['sersapxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm']['cSerSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRSE['sersapxx'] ?>";
                                parent.fmwork.document.forms['frgrm']['cSerDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRSE['serdesxx'] ?>";
                                parent.fmwork.fnLinks('cSerDes','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
                              </script>
                            <?php }
                          }else{ ?>
                            <script language = "javascript">
                              parent.fmwork.fnLinks('<?php echo $gFunction ?>','WINDOW','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
                              window.close();
                            </script>
                          <?php
                          }
                        }else{ ?>
                          <script language = "javascript">
                            alert('No hay registros coincidentes');
                            parent.fmwork.document.forms['frgrm']['cSerSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "";
                            parent.fmwork.document.forms['frgrm']['cSerDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qSercicio  = "SELECT ";
                        $qSercicio .= "sersapxx, ";
                        $qSercicio .= "serdesxx, ";
                        $qSercicio .= "regestxx ";
                        $qSercicio .= "FROM $cAlfa.lpar0011 ";                        
                        $qSercicio .= "WHERE ";
                        if ($gSerSap != "") {
                          $qSercicio .= "sersapxx = \"$gSerSap\" AND ";
                        }
                        if ($gSecDes != "") {
                          $qSercicio .= "serdesxx = \"$gSecDes\" AND ";
                        }
                        $qSercicio .= "regestxx = \"ACTIVO\" ";
                        $qSercicio .= "ORDER BY sersapxx ";
                        $xServicio  = f_MySql("SELECT","",$qSercicio,$xConexion01,"");
                        while ($xRSE = mysql_fetch_array($xServicio)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cSerSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRSE['sersapxx'] ?>";
                            parent.fmwork.document.forms['frgrm']['cSerDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRSE['serdesxx'] ?>";
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
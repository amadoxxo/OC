<?php
	/**
	 * --- Descripcion: Consulta los SubServicios:
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
                        $qSubServicios  = "SELECT ";
                        $qSubServicios .= "sersapxx, ";
                        $qSubServicios .= "subidxxx, ";
                        $qSubServicios .= "subdesxx, ";
                        $qSubServicios .= "regestxx ";
                        $qSubServicios .= "FROM $cAlfa.lpar0012 ";
                        $qSubServicios .= "WHERE ";
                        $qSubServicios .= "sersapxx LIKE \"%$gSerSap%\" AND ";
                        if ($gSubDes != "") {
                          $qSubServicios .= "subdesxx LIKE \"%$gSubDes%\" AND ";
                        }
                        $qSubServicios .= "regestxx = \"ACTIVO\" ";
                        $qSubServicios .= "ORDER BY sersapxx ";
                        $xSubServicios  = f_MySql("SELECT","",$qSubServicios,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qSubServicios."~".mysql_num_rows($xSubServicios));

                        if (mysql_num_rows($xSubServicios) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "100" Class = "name"><center>Cod. SAP</center></td>
                                <td width = "350" Class = "name"><center>Descripci&oacute;n</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRSS = mysql_fetch_array($xSubServicios)){
                                  if (mysql_num_rows($xSubServicios) > 1) { ?>
                                    <tr>
                                      <td width = "100" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm']['cSubId'+'<?php echo $gGrid . $gSecuencia ?>'].value  = '<?php echo $xRSS['subidxxx']?>';
                                                                window.opener.document.forms['frgrm']['cSubDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $xRSS['subdesxx']?>';
                                                                window.opener.fnLinks('cSubDes','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
                                                                window.close();"><?php echo $xRSS['subidxxx'] ?></a>
                                      </td>
                                      <td width = "350" class= "name"><?php echo $xRSS['subdesxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xRSS['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm']['cSubId'+'<?php echo $gGrid . $gSecuencia ?>'].value  = "<?php echo $xRSS['subidxxx'] ?>";
                                      window.opener.document.forms['frgrm']['cSubDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRSS['subdesxx'] ?>";
                                      window.opener.fnLinks('cSubDes', 'EXACT', '<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
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
                        $qSubServicios  = "SELECT ";
                        $qSubServicios .= "sersapxx, ";
                        $qSubServicios .= "subidxxx, ";
                        $qSubServicios .= "subdesxx, ";
                        $qSubServicios .= "regestxx ";
                        $qSubServicios .= "FROM $cAlfa.lpar0012 ";
                        $qSubServicios .= "WHERE ";
                        $qSubServicios .= "sersapxx LIKE \"%$gSerSap%\" AND ";
                        if ($gSubDes != "") {
                          $qSubServicios .= "subdesxx LIKE \"%$gSubDes%\" AND ";
                        }
                        $qSubServicios .= "regestxx = \"ACTIVO\" ";
                        $qSubServicios .= "ORDER BY subidxxx ";
                        $xSubServicios  = f_MySql("SELECT","",$qSubServicios,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qSubServicios."~".mysql_num_rows($xSubServicios));

                        if (mysql_num_rows($xSubServicios) > 0){
                          if (mysql_num_rows($xSubServicios) == 1){
                            while ($xRSS = mysql_fetch_array($xSubServicios)) { 
                              $gSubDes = $xRSS['subdesxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm']['cSubId'+'<?php echo $gGrid . $gSecuencia ?>'].value  = "<?php echo $xRSS['subidxxx'] ?>";
                                parent.fmwork.document.forms['frgrm']['cSubDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRSS['subdesxx'] ?>";
                                parent.fmwork.fnLinks('cSubDes','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
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
                            parent.fmwork.document.forms['frgrm']['cSubId'+'<?php echo $gGrid . $gSecuencia ?>'].value  = "";
                            parent.fmwork.document.forms['frgrm']['cSubDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qSubServicios  = "SELECT ";
                        $qSubServicios .= "sersapxx, ";
                        $qSubServicios .= "subidxxx, ";
                        $qSubServicios .= "subdesxx, ";
                        $qSubServicios .= "regestxx ";
                        $qSubServicios .= "FROM $cAlfa.lpar0012 ";
                        $qSubServicios .= "WHERE ";
                        $qSubServicios .= "sersapxx = \"$gSerSap\" AND ";
                        if ($gSubDes != "") {
                          $qSubServicios .= "subdesxx = \"$gSubDes\" AND ";
                        }
                        $qSubServicios .= "regestxx = \"ACTIVO\" ";
                        $qSubServicios .= "ORDER BY sersapxx ";
                        $xSubServicios  = f_MySql("SELECT","",$qSubServicios,$xConexion01,"");
                        while ($xRSS = mysql_fetch_array($xSubServicios)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cSubId'+'<?php echo $gGrid . $gSecuencia ?>'].value  = "<?php echo $xRSS['sersapxx'] ?>";
                            parent.fmwork.document.forms['frgrm']['cSubDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRSS['subdesxx'] ?>";
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
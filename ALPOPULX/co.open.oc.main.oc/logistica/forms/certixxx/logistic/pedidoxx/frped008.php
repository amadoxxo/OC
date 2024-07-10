<?php
	/**
	 * --- Descripcion: Consulta los Canales de Distribucion:
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */
	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Canales de Distribuci&oacute;n</title>
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
                  <legend>Param&eacute;trica de Canales de Distribuci&oacute;ns</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qCanalDist  = "SELECT ";
                        $qCanalDist .= "cdisapxx, ";
                        $qCanalDist .= "cdidesxx, ";
                        $qCanalDist .= "regestxx ";
                        $qCanalDist .= "FROM $cAlfa.lpar0008 ";                        
                        $qCanalDist .= "WHERE ";
                        if ($gCdiSap != "") {
                          $qCanalDist .= "cdisapxx LIKE \"%$gCdiSap%\" AND ";
                        }
                        if ($gCdiDes != "") {
                          $qCanalDist .= "cdidesxx LIKE \"%$gCdiDes%\" AND ";
                        }
                        $qCanalDist .= "regestxx = \"ACTIVO\" ";
                        $qCanalDist .= "ORDER BY cdisapxx ";
                        $xCanalDist  = f_MySql("SELECT","",$qCanalDist,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qCanalDist."~".mysql_num_rows($xCanalDist));

                        if (mysql_num_rows($xCanalDist) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "100" Class = "name"><center>C&oacute;digo SAP</center></td>
                                <td width = "350" Class = "name"><center>Descripci&oacute;n</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRCD = mysql_fetch_array($xCanalDist)){
                                  if (mysql_num_rows($xCanalDist) > 1) { ?>
                                    <tr>
                                      <td width = "100" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm']['cCdiSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $xRCD['cdisapxx']?>';
                                                                window.opener.document.forms['frgrm']['cCdiDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $xRCD['cdidesxx']?>';
                                                                window.opener.fnLinks('cCdiDes','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
                                                                window.close();"><?php echo $xRCD['cdisapxx'] ?></a>
                                      </td>
                                      <td width = "350" class= "name"><?php echo $xRCD['cdidesxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xRCD['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm']['cCdiSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRCD['cdisapxx'] ?>";
                                      window.opener.document.forms['frgrm']['cCdiDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRCD['cdidesxx'] ?>";
                                      window.opener.fnLinks('cCdiDes', 'EXACT', '<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
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
                        $qCanalDist  = "SELECT ";
                        $qCanalDist .= "cdisapxx, ";
                        $qCanalDist .= "cdidesxx, ";
                        $qCanalDist .= "regestxx ";
                        $qCanalDist .= "FROM $cAlfa.lpar0008 ";                        
                        $qCanalDist .= "WHERE ";
                        if ($gCdiSap != "") {
                          $qCanalDist .= "cdisapxx LIKE \"%$gCdiSap%\" AND ";
                        }
                        if ($gCdiDes != "") {
                          $qCanalDist .= "cdidesxx LIKE \"%$gCdiDes%\" AND ";
                        }
                        $qCanalDist .= "regestxx = \"ACTIVO\" ";
                        $qCanalDist .= "ORDER BY cdisapxx ";
                        $xCanalDist  = f_MySql("SELECT","",$qCanalDist,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qCanalDist."~".mysql_num_rows($xCanalDist));

                        if (mysql_num_rows($xCanalDist) > 0){
                          if (mysql_num_rows($xCanalDist) == 1){
                            while ($xRCD = mysql_fetch_array($xCanalDist)) { 
                              $gCdiSap = $xRCD['cdisapxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm']['cCdiSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRCD['cdisapxx'] ?>";
                                parent.fmwork.document.forms['frgrm']['cCdiDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRCD['cdidesxx'] ?>";
                                parent.fmwork.fnLinks('cCdiDes','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
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
                            parent.fmwork.document.forms['frgrm']['cCdiSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "";
                            parent.fmwork.document.forms['frgrm']['cCdiDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qCanalDist  = "SELECT ";
                        $qCanalDist .= "cdisapxx, ";
                        $qCanalDist .= "cdidesxx, ";
                        $qCanalDist .= "regestxx ";
                        $qCanalDist .= "FROM $cAlfa.lpar0008 ";                        
                        $qCanalDist .= "WHERE ";
                        if ($gCdiSap != "") {
                          $qCanalDist .= "cdisapxx = \"$gCdiSap\" AND ";
                        }
                        if ($gCdiDes != "") {
                          $qCanalDist .= "cdidesxx = \"$gCdiDes\" AND ";
                        }
                        $qCanalDist .= "regestxx = \"ACTIVO\" ";
                        $qCanalDist .= "ORDER BY cdisapxx ";
                        $xCanalDist  = f_MySql("SELECT","",$qCanalDist,$xConexion01,"");
                        while ($xRCD = mysql_fetch_array($xCanalDist)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cCdiSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRCD['cdisapxx'] ?>";
                            parent.fmwork.document.forms['frgrm']['cCdiDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRCD['cdidesxx'] ?>";
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
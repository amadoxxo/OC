<?php
  namespace openComex;
	/**
	 * --- Descripcion: Consulta los Codigos CEBE:
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */
	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de C&oacute;digos CEBE</title>
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
                  <legend>Param&eacute;trica de C&oacute;digos CEBE</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qCodCebe  = "SELECT ";
                        $qCodCebe .= "cebidxxx, ";
                        $qCodCebe .= "cebcodxx, ";
                        $qCodCebe .= "cebdesxx, ";
                        $qCodCebe .= "regestxx ";
                        $qCodCebe .= "FROM $cAlfa.lpar0010 ";                        
                        $qCodCebe .= "WHERE ";
                        if ($gFunction == "cCebCod") {
                          $qCodCebe .= "cebcodxx LIKE \"%$gCebCod%\" AND ";
                        } 
                        if ($gFunction == "cCebDes") {
                          $qCodCebe .= "cebdesxx LIKE \"%$gCebDes%\" AND ";
                        }
                        $qCodCebe .= "regestxx = \"ACTIVO\" ";
                        $qCodCebe .= "ORDER BY cebidxxx ";
                        $xCodCebe  = f_MySql("SELECT","",$qCodCebe,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qCodCebe."~".mysql_num_rows($xCodCebe));

                        if (mysql_num_rows($xCodCebe) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "100" Class = "name"><center>ID</center></td>
                                <td width = "100" Class = "name"><center>Cod. CEBE</center></td>
                                <td width = "250" Class = "name"><center>Descripci&oacute;n Corta</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRCC = mysql_fetch_array($xCodCebe)){
                                  if (mysql_num_rows($xCodCebe) > 1) { ?>
                                    <tr>
                                      <td width = "100" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm']['cCebId'+'<?php echo $gSecuencia ?>'].value  = '<?php echo $xRCC['cebidxxx']?>';
                                                                window.opener.document.forms['frgrm']['cCebCod'+'<?php echo $gSecuencia ?>'].value = '<?php echo $xRCC['cebcodxx']?>';
                                                                window.opener.document.forms['frgrm']['cCebDes'+'<?php echo $gSecuencia ?>'].value = '<?php echo $xRCC['cebdesxx']?>';
                                                                window.opener.fnLinks('cCebCod','EXACT','<?php echo $gSecuencia ?>');
                                                                window.close();"><?php echo $xRCC['cebidxxx'] ?></a>
                                      </td>
                                      <td width = "350" class= "name"><?php echo $xRCC['cebcodxx'] ?></td>
                                      <td width = "350" class= "name"><?php echo $xRCC['cebdesxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xRCC['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm']['cCebId'+'<?php echo $gSecuencia ?>'].value  = "<?php echo $xRCC['cebidxxx'] ?>";
                                      window.opener.document.forms['frgrm']['cCebCod'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRCC['cebcodxx'] ?>";
                                      window.opener.document.forms['frgrm']['cCebDes'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRCC['cebdesxx'] ?>";
                                      window.opener.fnLinks('cCebCod', 'EXACT', '<?php echo $gSecuencia ?>');
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
                        $qCodCebe  = "SELECT ";
                        $qCodCebe .= "cebidxxx, ";
                        $qCodCebe .= "cebcodxx, ";
                        $qCodCebe .= "cebdesxx, ";
                        $qCodCebe .= "regestxx ";
                        $qCodCebe .= "FROM $cAlfa.lpar0010 ";                        
                        $qCodCebe .= "WHERE ";
                        if ($gFunction == "cCebCod") {
                          $qCodCebe .= "cebcodxx LIKE \"%$gCebCod%\" AND ";
                        } 
                        if ($gFunction == "cCebDes") {
                          $qCodCebe .= "cebdesxx LIKE \"%$gCebDes%\" AND ";
                        }
                        $qCodCebe .= "regestxx = \"ACTIVO\" ";
                        $qCodCebe .= "ORDER BY cebidxxx ";
                        $xCodCebe  = f_MySql("SELECT","",$qCodCebe,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qCodCebe."~".mysql_num_rows($xCodCebe));

                        if (mysql_num_rows($xCodCebe) > 0){
                          if (mysql_num_rows($xCodCebe) == 1){
                            while ($xRCC = mysql_fetch_array($xCodCebe)) { 
                              $gCebCod = $xRCC['cebcodxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm']['cCebId'+'<?php echo $gSecuencia ?>'].value  = "<?php echo $xRCC['cebidxxx'] ?>";
                                parent.fmwork.document.forms['frgrm']['cCebCod'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRCC['cebcodxx'] ?>";
                                parent.fmwork.document.forms['frgrm']['cCebDes'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRCC['cebdesxx'] ?>";
                                parent.fmwork.fnLinks('cCebCod','EXACT','<?php echo $gSecuencia ?>');
                              </script>
                            <?php }
                          }else{ ?>
                            <script language = "javascript">
                              parent.fmwork.fnLinks('<?php echo $gFunction ?>','WINDOW','<?php echo $gSecuencia ?>');
                              window.close();
                            </script>
                          <?php
                          }
                        }else{ ?>
                          <script language = "javascript">
                            alert('No hay registros coincidentes');
                            parent.fmwork.document.forms['frgrm']['cCebId'+'<?php echo $gSecuencia ?>'].value  = "";
                            parent.fmwork.document.forms['frgrm']['cCebCod'+'<?php echo $gSecuencia ?>'].value = "";
                            parent.fmwork.document.forms['frgrm']['cCebDes'+'<?php echo $gSecuencia ?>'].value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qCodCebe  = "SELECT ";
                        $qCodCebe .= "cebidxxx, ";
                        $qCodCebe .= "cebcodxx, ";
                        $qCodCebe .= "cebdesxx, ";
                        $qCodCebe .= "regestxx ";
                        $qCodCebe .= "FROM $cAlfa.lpar0010 ";                        
                        $qCodCebe .= "WHERE ";
                        if ($gFunction == "cCebCod") {
                          $qCodCebe .= "cebcodxx = \"$gCebCod\" AND ";
                        } 
                        if ($gFunction == "cCebDes") {
                          $qCodCebe .= "cebdesxx = \"$gCebDes\" AND ";
                        }
                        $qCodCebe .= "regestxx = \"ACTIVO\" ";
                        $qCodCebe .= "ORDER BY cebidxxx ";
                        $xCodCebe  = f_MySql("SELECT","",$qCodCebe,$xConexion01,"");
                        while ($xRCC = mysql_fetch_array($xCodCebe)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cCebId'+'<?php echo $gSecuencia ?>'].value  = "<?php echo $xRUF['cebidxxx'] ?>";
                            parent.fmwork.document.forms['frgrm']['cCebCod'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRUF['cebcodxx'] ?>";
                            parent.fmwork.document.forms['frgrm']['cCebDes'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRUF['cebdesxx'] ?>";
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
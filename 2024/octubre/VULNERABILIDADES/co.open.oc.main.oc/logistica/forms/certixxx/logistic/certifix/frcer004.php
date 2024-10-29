<?php
  namespace openComex;
	/**
	 * --- Descripcion: Consulta los Objetos Facturables:
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */
	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Objeto Facturable</title>
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
                  <legend>Param&eacute;trica de Objeto Facturable</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qObjFact  = "SELECT ";
                        $qObjFact .= "obfidxxx, ";
                        $qObjFact .= "obfdesxx, ";
                        $qObjFact .= "regestxx ";
                        $qObjFact .= "FROM $cAlfa.lpar0004 ";                        
                        $qObjFact .= "WHERE ";
                        $qObjFact .= "(obfidxxx LIKE \"%$gObfId%\" OR ";
                        $qObjFact .= "obfdesxx LIKE \"%$gObfId%\") AND ";
                        $qObjFact .= "regestxx = \"ACTIVO\" ";
                        $qObjFact .= "ORDER BY obfidxxx ";
                        $xObjFact  = f_MySql("SELECT","",$qObjFact,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qObjFact."~".mysql_num_rows($xObjFact));

                        if (mysql_num_rows($xObjFact) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "100" Class = "name"><center>ID</center></td>
                                <td width = "350" Class = "name"><center>Descripci&oacute;n</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xROF = mysql_fetch_array($xObjFact)){
                                  if (mysql_num_rows($xObjFact) > 1) { ?>
                                    <tr>
                                      <td width = "100" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm']['cObfId'+'<?php echo $gGrid.$gSecuencia ?>'].value  = '<?php echo $xROF['obfidxxx']?>';
                                                                window.opener.document.forms['frgrm']['cObfDes'+'<?php echo $gGrid.$gSecuencia ?>'].value = '<?php echo $xROF['obfdesxx']?>';
                                                                window.opener.fnLinks('cObfId','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
                                                                window.close();"><?php echo $xROF['obfidxxx'] ?></a>
                                      </td>
                                      <td width = "350" class= "name"><?php echo $xROF['obfdesxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xROF['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm']['cObfId'+'<?php echo $gGrid.$gSecuencia ?>'].value  = "<?php echo $xROF['obfidxxx'] ?>";
                                      window.opener.document.forms['frgrm']['cObfDes'+'<?php echo $gGrid.$gSecuencia ?>'].value = "<?php echo $xROF['obfdesxx'] ?>";
                                      window.opener.fnLinks('cObfId', 'EXACT', '<?php echo $gSecuencia ?>', '<?php echo $gGrid ?>');
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
                        $qObjFact  = "SELECT ";
                        $qObjFact .= "obfidxxx, ";
                        $qObjFact .= "obfdesxx, ";
                        $qObjFact .= "regestxx ";
                        $qObjFact .= "FROM $cAlfa.lpar0004 ";                        
                        $qObjFact .= "WHERE ";
                        $qObjFact .= "(obfidxxx LIKE \"%$gObfId%\" OR ";
                        $qObjFact .= "obfdesxx LIKE \"%$gObfId%\") AND ";
                        $qObjFact .= "regestxx = \"ACTIVO\" ";
                        $qObjFact .= "ORDER BY obfidxxx ";
                        $xObjFact  = f_MySql("SELECT","",$qObjFact,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qObjFact."~".mysql_num_rows($xObjFact));

                        if (mysql_num_rows($xObjFact) > 0){
                          if (mysql_num_rows($xObjFact) == 1){
                            while ($xROF = mysql_fetch_array($xObjFact)) { 
                              $gObfId = $xROF['obfidxxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm']['cObfId'+'<?php echo $gGrid.$gSecuencia ?>'].value  = "<?php echo $xROF['obfidxxx'] ?>";
                                parent.fmwork.document.forms['frgrm']['cObfDes'+'<?php echo $gGrid.$gSecuencia ?>'].value = "<?php echo $xROF['obfdesxx'] ?>";
                                parent.fmwork.fnLinks('cObfId','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
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
                            parent.fmwork.document.forms['frgrm']['cObfId'+'<?php echo $gGrid.$gSecuencia ?>'].value  = "";
                            parent.fmwork.document.forms['frgrm']['cObfDes'+'<?php echo $gGrid.$gSecuencia ?>'].value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qObjFact  = "SELECT ";
                        $qObjFact .= "obfidxxx, ";
                        $qObjFact .= "obfdesxx, ";
                        $qObjFact .= "regestxx ";
                        $qObjFact .= "FROM $cAlfa.lpar0004 ";                        
                        $qObjFact .= "WHERE ";
                        $qObjFact .= "(obfidxxx = \"$gObfId\" OR ";
                        $qObjFact .= "obfdesxx = \"$gObfId\") AND ";
                        $qObjFact .= "regestxx = \"ACTIVO\" ";
                        $qObjFact .= "ORDER BY obfidxxx ";
                        $xObjFact  = f_MySql("SELECT","",$qObjFact,$xConexion01,"");
                        while ($xROF = mysql_fetch_array($xObjFact)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cObfId'+'<?php echo $gGrid.$gSecuencia ?>'].value  = "<?php echo $xROF['obfidxxx'] ?>";
                            parent.fmwork.document.forms['frgrm']['cObfDes'+'<?php echo $gGrid.$gSecuencia ?>'].value = "<?php echo $xROF['obfdesxx'] ?>";
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
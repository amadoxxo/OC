<?php
	/**
	 * --- Descripcion: Consulta las Unidades Facturables:
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */
	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Unidad Facturable</title>
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
                  <legend>Param&eacute;trica de Unidad Facturable</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qUniFact  = "SELECT ";
                        $qUniFact .= "ufaidxxx, ";
                        $qUniFact .= "ufadesxx, ";
                        $qUniFact .= "regestxx ";
                        $qUniFact .= "FROM $cAlfa.lpar0006 ";                        
                        $qUniFact .= "WHERE ";
                        if ($gUfaId != "") {
                          $qUniFact .= "ufaidxxx LIKE \"%$gUfaId%\" AND ";
                        }
                        if ($gUfaDes != "") {
                          $qUniFact .= "ufadesxx LIKE \"%$gUfaDes%\" AND ";
                        }
                        $qUniFact .= "regestxx = \"ACTIVO\" ";
                        $qUniFact .= "ORDER BY ufaidxxx ";
                        $xUniFact  = f_MySql("SELECT","",$qUniFact,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qUniFact."~".mysql_num_rows($xUniFact));

                        if (mysql_num_rows($xUniFact) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "100" Class = "name"><center>Id</center></td>
                                <td width = "350" Class = "name"><center>Descripci&oacute;n</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRUF = mysql_fetch_array($xUniFact)){
                                  if (mysql_num_rows($xUniFact) > 1) { ?>
                                    <tr>
                                      <td width = "100" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm'].cUfaId.value  = '<?php echo $xRUF['ufaidxxx']?>';
                                                                window.opener.document.forms['frgrm'].cUfaDes.value = '<?php echo $xRUF['ufadesxx']?>';
                                                                window.opener.fnLinks('cUfaId','EXACT',0);
                                                                window.close();"><?php echo $xRUF['ufaidxxx'] ?></a>
                                      </td>
                                      <td width = "350" class= "name"><?php echo $xRUF['ufadesxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xRUF['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm'].cUfaId.value  = "<?php echo $xRUF['ufaidxxx'] ?>";
                                      window.opener.document.forms['frgrm'].cUfaDes.value = "<?php echo $xRUF['ufadesxx'] ?>";
                                      window.opener.fnLinks('cUfaId', 'EXACT', 0);
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
                        $qUniFact  = "SELECT ";
                        $qUniFact .= "ufaidxxx, ";
                        $qUniFact .= "ufadesxx, ";
                        $qUniFact .= "regestxx ";
                        $qUniFact .= "FROM $cAlfa.lpar0006 ";                        
                        $qUniFact .= "WHERE ";
                        if ($gUfaId != "") {
                          $qUniFact .= "ufaidxxx LIKE \"%$gUfaId%\" AND ";
                        }
                        if ($gUfaDes != "") {
                          $qUniFact .= "ufadesxx LIKE \"%$gUfaDes%\" AND ";
                        }
                        $qUniFact .= "regestxx = \"ACTIVO\" ";
                        $qUniFact .= "ORDER BY ufaidxxx ";
                        $xUniFact  = f_MySql("SELECT","",$qUniFact,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qUniFact."~".mysql_num_rows($xUniFact));

                        if (mysql_num_rows($xUniFact) > 0){
                          if (mysql_num_rows($xUniFact) == 1){
                            while ($xRUF = mysql_fetch_array($xUniFact)) { 
                              $gUfaId = $xRUF['ufaidxxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm'].cUfaId.value  = "<?php echo $xRUF['ufaidxxx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cUfaDes.value = "<?php echo $xRUF['ufadesxx'] ?>";
                                parent.fmwork.fnLinks('cUfaId','EXACT',0);
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
                            parent.fmwork.document.forms['frgrm'].cUfaId.value  = "";
                            parent.fmwork.document.forms['frgrm'].cUfaDes.value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qUniFact  = "SELECT ";
                        $qUniFact .= "ufaidxxx, ";
                        $qUniFact .= "ufadesxx, ";
                        $qUniFact .= "regestxx ";
                        $qUniFact .= "FROM $cAlfa.lpar0006 ";                        
                        $qUniFact .= "WHERE ";
                        if ($gUfaId != "") {
                          $qUniFact .= "ufaidxxx = \"$gUfaId\" AND ";
                        }
                        if ($gUfaDes != "") {
                          $qUniFact .= "ufadesxx = \"$gUfaDes\" AND ";
                        }
                        $qUniFact .= "regestxx = \"ACTIVO\" ";
                        $qUniFact .= "ORDER BY ufaidxxx ";
                        $xUniFact  = f_MySql("SELECT","",$qUniFact,$xConexion01,"");
                        while ($xRUF = mysql_fetch_array($xUniFact)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm'].cUfaId.value  = "<?php echo $xRUF['ufaidxxx'] ?>";
                            parent.fmwork.document.forms['frgrm'].cUfaDes.value = "<?php echo $xRUF['ufadesxx'] ?>";
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
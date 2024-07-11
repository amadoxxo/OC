<?php
	/**
	 * --- Descripcion: Consulta las Formas de Cobro:
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */
	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Formas de Cobro</title>
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
                  <legend>Param&eacute;trica de Formas de Cobro</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qFormaCobro  = "SELECT ";
                        $qFormaCobro .= "fcoidxxx, ";
                        $qFormaCobro .= "fcodesxx, ";
                        $qFormaCobro .= "regestxx ";
                        $qFormaCobro .= "FROM $cAlfa.lpar0130 ";
                        $qFormaCobro .= "WHERE ";
                        switch ($gFunction) {
                          case 'cFcoId':
                            $qFormaCobro .= "fcoidxxx LIKE \"%$gFcoId%\" AND ";
                          break;
                          case 'cFcoDes':
                            $qFormaCobro .= "fcodesxx LIKE \"%$gFcoDes%\" AND ";
                          break;
                        }
                        $qFormaCobro .= "regestxx = \"ACTIVO\" ";
                        $qFormaCobro .= "ORDER BY fcoidxxx ";
                        $xFormaCobro  = f_MySql("SELECT","",$qFormaCobro,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qFormaCobro."~".mysql_num_rows($xFormaCobro));

                        if (mysql_num_rows($xFormaCobro) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "100" Class = "name"><center>Id</center></td>
                                <td width = "350" Class = "name"><center>Descripci&oacute;n</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRFC = mysql_fetch_array($xFormaCobro)){
                                  if (mysql_num_rows($xFormaCobro) > 1) { ?>
                                    <tr>
                                      <td width = "100" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm'].cFcoId.value  = '<?php echo $xRFC['fcoidxxx']?>';
                                                                window.opener.document.forms['frgrm'].cFcoDes.value = '<?php echo $xRFC['fcodesxx']?>';
                                                                window.opener.fnLinks('cFcoId','EXACT',0);
                                                                window.close();
                                                                window.opener.fnOcultarMostrarTarifa('<?php echo $xRFC['fcoidxxx']?>');"><?php echo $xRFC['fcoidxxx'] ?></a>
                                      </td>
                                      <td width = "350" class= "name"><?php echo $xRFC['fcodesxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xRFC['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm'].cFcoId.value  = "<?php echo $xRFC['fcoidxxx'] ?>";
                                      window.opener.document.forms['frgrm'].cFcoDes.value = "<?php echo $xRFC['fcodesxx'] ?>";
                                      window.opener.fnLinks('cFcoId', 'EXACT', 0);
                                      window.close();
                                      window.opener.fnOcultarMostrarTarifa('<?php echo $xRFC['fcodesxx'] ?>');
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
                        $qFormaCobro  = "SELECT ";
                        $qFormaCobro .= "fcoidxxx, ";
                        $qFormaCobro .= "fcodesxx, ";
                        $qFormaCobro .= "regestxx ";
                        $qFormaCobro .= "FROM $cAlfa.lpar0130 ";
                        $qFormaCobro .= "WHERE ";
                        switch ($gFunction) {
                          case 'cFcoId':
                            $qFormaCobro .= "fcoidxxx LIKE \"%$gFcoId%\" AND ";
                          break;
                          case 'cFcoDes':
                            $qFormaCobro .= "fcodesxx LIKE \"%$gFcoDes%\" AND ";
                          break;
                        }
                        $qFormaCobro .= "regestxx = \"ACTIVO\" ";
                        $qFormaCobro .= "ORDER BY fcoidxxx ";
                        $xFormaCobro  = f_MySql("SELECT","",$qFormaCobro,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qFormaCobro."~".mysql_num_rows($xFormaCobro));

                        if (mysql_num_rows($xFormaCobro) > 0){
                          if (mysql_num_rows($xFormaCobro) == 1){
                            while ($xRFC = mysql_fetch_array($xFormaCobro)) { 
                              $gFcoId = $xRFC['fcoidxxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm'].cFcoId.value  = "<?php echo $xRFC['fcoidxxx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cFcoDes.value = "<?php echo $xRFC['fcodesxx'] ?>";
                                parent.fmwork.fnLinks('cFcoId','EXACT',0);
                                parent.fmwork.fnOcultarMostrarTarifa('<?php echo $xRFC['fcoidxxx'] ?>');
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
                            parent.fmwork.document.forms['frgrm'].cFcoId.value  = "";
                            parent.fmwork.document.forms['frgrm'].cFcoDes.value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qFormaCobro  = "SELECT ";
                        $qFormaCobro .= "fcoidxxx, ";
                        $qFormaCobro .= "fcodesxx, ";
                        $qFormaCobro .= "regestxx ";
                        $qFormaCobro .= "FROM $cAlfa.lpar0130 ";
                        $qFormaCobro .= "WHERE ";
                        switch ($gFunction) {
                          case 'cFcoId':
                            $qFormaCobro .= "fcoidxxx = \"$gFcoId\" AND ";
                          break;
                          case 'cFcoDes':
                            $qFormaCobro .= "fcodesxx = \"$gFcoDes\" AND ";
                          break;
                        }
                        $qFormaCobro .= "regestxx = \"ACTIVO\" ";
                        $qFormaCobro .= "ORDER BY fcoidxxx ";
                        $xFormaCobro  = f_MySql("SELECT","",$qFormaCobro,$xConexion01,"");
                        while ($xRFC = mysql_fetch_array($xFormaCobro)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm'].cFcoId.value  = "<?php echo $xRFC['fcoidxxx'] ?>";
                            parent.fmwork.document.forms['frgrm'].cFcoDes.value = "<?php echo $xRFC['fcodesxx'] ?>";
                            parent.fmwork.fnOcultarMostrarTarifa('<?php echo $xRFC['fcoidxxx'] ?>');
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
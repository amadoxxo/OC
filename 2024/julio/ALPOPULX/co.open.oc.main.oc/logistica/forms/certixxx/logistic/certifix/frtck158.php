<?php
	/**
	 * --- Parametrica de Parametrica de Tipo Ticket: 
	 * @author Elian Amado. <elian.amado@openits.co>
	 * @package openComex
   * @version 001
	 */

	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Tipo Ticket</title>
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
                  <legend>Param&eacute;trica de Tipo Ticket</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qCliente  = "SELECT ";
                        $qCliente .= "tticodxx, ";
                        $qCliente .= "ttidesxx, ";
                        $qCliente .= "regestxx ";
                        $qCliente .= "FROM $cAlfa.lpar0158 ";
                        $qCliente .= "WHERE ";
                        switch ($gFunction) {
                          case 'cTtiCod':
                            $qCliente .= "tticodxx LIKE \"%$gTtiCod%\" AND ";
                          break;
                          case 'cTtiDes':
                            $qCliente .= "ttidesxx LIKE \"%$gTtiDes%\" AND ";
                          break;
                        }
                        $qCliente .= "regestxx = \"ACTIVO\" ";
                        $qCliente .= "ORDER BY tticodxx ";
                        $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qCliente."~".mysql_num_rows($xCliente));
                        if (mysql_num_rows($xCliente) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "050" Class = "name"><center>ID</center></td>
                                <td width = "400" Class = "name"><center>Descripcion</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRCL = mysql_fetch_array($xCliente)){
                                  if (mysql_num_rows($xCliente) > 1) { ?>
                                    <tr>
                                      <td width = "050" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm'].cTtiCod.value = '<?php echo $xRCL['tticodxx']?>';
                                                                window.opener.document.forms['frgrm'].cTtiDes.value = '<?php echo $xRCL['ttidesxx']?>';
                                                                window.opener.fnLinks('cTtiCod','EXACT',0);
                                                                window.close();"><?php echo $xRCL['tticodxx'] ?></a>
                                      </td>
                                      <td width = "400" class= "name"><?php echo $xRCL['ttidesxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xRCL['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm'].cTtiCod.value  = "<?php echo $xRCL['tticodxx'] ?>";
                                      window.opener.document.forms['frgrm'].cTtiDes.value = "<?php echo $xRCL['ttidesxx'] ?>";
                                      window.opener.fnLinks('cTtiCod', 'EXACT', 0);
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
                        $qCliente  = "SELECT ";
                        $qCliente .= "tticodxx, ";
                        $qCliente .= "FROM $cAlfa.lpar0158 ";
                        $qCliente .= "WHERE ";
                        switch ($gFunction) {
                          case 'cTtiCod':
                            $qCliente .= "tticodxx LIKE \"%$gTtiCod%\" AND ";
                          break;
                          case 'cTtiDes':
                            $qCliente .= "ttidesxx LIKE \"%$gTtiDes%\" AND ";
                          break;
                        }
                        $qCliente .= "regestxx = \"ACTIVO\" ";
                        $qCliente .= "ORDER BY tticodxx";
                        $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qCliente."~".mysql_num_rows($xCliente));

                        if (mysql_num_rows($xCliente) > 0){
                          if (mysql_num_rows($xCliente) == 1){
                            while ($xRCL = mysql_fetch_array($xCliente)) { 
                              $gCliId = $xRCL['tticodxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm'].cTtiCod.value = "<?php echo $xRCL['tticodxx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cTtiDes.value = "<?php echo $xRCL['ttidesxx'] ?>";
                                parent.fmwork.fnLinks('cTtiCod','EXACT',0);
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
                            parent.fmwork.document.forms['frgrm'].cTtiCod.value  = "";
                            parent.fmwork.document.forms['frgrm'].cTtiDes.value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qCliente  = "SELECT ";
                        $qCliente .= "tticodxx, ";
                        $qCliente .= "FROM $cAlfa.lpar0158 ";
                        $qCliente .= "WHERE ";
                        switch ($gFunction) {
                          case 'cTtiCod':
                            $qCliente .= "tticodxx = \"$gTtiCod\" AND ";
                          break;
                          case 'cTtiDes':
                            $qCliente .= "ttidesxx = \"$gTtiDes\" AND ";
                          break;
                        }
                        $qCliente .= "regestxx = \"ACTIVO\" ";
                        $qCliente .= "LIMIT 0,1";
                        $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
                        if (mysql_num_rows($xDatDex) == 1) {
                          $vCliente = mysql_fetch_array($xCliente);
                          ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cTtiCod'].value  = "<?php echo $vCliente['tticodxx'] ?>";
                            parent.fmwork.document.forms['frgrm']['cTtiDes'].value = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$vCliente['ttidesxx']) ?>";
                          </script>
                          <?php 
                        } else { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cTtiCod'].value  = "";
                            parent.fmwork.document.forms['frgrm']['cTtiDes'].value = "";
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
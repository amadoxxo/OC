<?php
	/**
	 * --- Descripcion: Consulta los Clientes: 
	 * @author Elian Amado. <elian.amado@openits.co>
	 * @package openComex
   * @version 001
	 */

	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Clientes</title>
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
                  <legend>Param&eacute;trica de Clientes</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qCliente  = "SELECT ";
                        $qCliente .= "cliidxxx, ";
                        $qCliente .= "IF(clinomxx != \"\",clinomxx,(TRIM(CONCAT(clinomxx,\" \",clinom1x,\" \",clinom2x,\" \",cliape1x,\" \",cliape2x)))) AS clinomxx,";
                        $qCliente .= "regestxx ";
                        $qCliente .= "FROM $cAlfa.lpar0150 ";                        
                        $qCliente .= "WHERE ";
                        $qCliente .= "cliidxxx LIKE \"%$gCliId%\" AND ";
                        $qCliente .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) LIKE \"%$gCliNom%\" AND ";
                        $qCliente .= "cliclixx = \"SI\" AND ";
                        $qCliente .= "regestxx = \"ACTIVO\" ";
                        $qCliente .= "ORDER BY cliidxxx ";
                        $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qCliente."~".mysql_num_rows($xCliente));
                        if (mysql_num_rows($xCliente) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "050" Class = "name"><center>Nit</center></td>
                                <td width = "400" Class = "name"><center>Nombre</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRCL = mysql_fetch_array($xCliente)){
                                  if (mysql_num_rows($xCliente) > 1) { ?>
                                    <tr>
                                      <td width = "050" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm'].cCliId.value  = '<?php echo $xRCL['cliidxxx']?>';
                                                                window.opener.document.forms['frgrm'].cCliDV.value  = '<?php echo gendv($xRCL['cliidxxx'])?>';
                                                                window.opener.document.forms['frgrm'].cCliNom.value = '<?php echo $xRCL['clinomxx']?>';
                                                                window.opener.fnLinks('cCliId','EXACT',0);
                                                                window.close();"><?php echo $xRCL['cliidxxx'] ?></a>
                                      </td>
                                      <td width = "400" class= "name"><?php echo $xRCL['clinomxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xRCL['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm'].cCliId.value  = "<?php echo $xRCL['cliidxxx'] ?>";
                                      window.opener.document.forms['frgrm'].cCliDV.value  = "<?php echo gendv($xRCL['cliidxxx'])?>";
                                      window.opener.document.forms['frgrm'].cCliNom.value = "<?php echo $xRCL['clinomxx'] ?>";
                                      window.opener.fnLinks('cCliId', 'EXACT', 0);
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
                        $qCliente .= "cliidxxx, ";
                        $qCliente .= "IF(clinomxx != \"\",clinomxx,(TRIM(CONCAT(clinomxx,\" \",clinom1x,\" \",clinom2x,\" \",cliape1x,\" \",cliape2x)))) AS clinomxx ";
                        $qCliente .= "FROM $cAlfa.lpar0150 ";
                        $qCliente .= "WHERE ";
                        $qCliente .= "cliidxxx LIKE \"%$gCliId%\" AND ";
                        $qCliente .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,REPLACE(CONCAT($cAlfa.lpar0150.clinom1x,\" \",$cAlfa.lpar0150.clinom2x,\" \",$cAlfa.lpar0150.cliape1x,\" \",$cAlfa.lpar0150.cliape2x), \"  \", \" \")) LIKE \"%$gCliNom%\" AND ";
                        $qCliente .= "cliclixx = \"SI\" AND ";
                        $qCliente .= "regestxx = \"ACTIVO\" ";
                        $qCliente .= "ORDER BY cliidxxx";
                        $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qCliente."~".mysql_num_rows($xCliente));

                        if (mysql_num_rows($xCliente) > 0){
                          if (mysql_num_rows($xCliente) == 1){
                            while ($xRCL = mysql_fetch_array($xCliente)) { 
                              $gCliId = $xRCL['cliidxxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm'].cCliId.value  = "<?php echo $xRCL['cliidxxx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cCliDV.value  = "<?php echo gendv($xRCL['cliidxxx'])?>";
                                parent.fmwork.document.forms['frgrm'].cCliNom.value = "<?php echo $xRCL['clinomxx'] ?>";
                                parent.fmwork.fnLinks('cCliId','EXACT',0);
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
                            parent.fmwork.document.forms['frgrm'].cCliId.value  = "";
                            parent.fmwork.document.forms['frgrm'].cCliDV.value  = "";
                            parent.fmwork.document.forms['frgrm'].cCliNom.value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qCliente  = "SELECT ";
                        $qCliente .= "cliidxxx, ";
                        $qCliente .= "IF(clinomxx != \"\",clinomxx,(TRIM(CONCAT(clinomxx,\" \",clinom1x,\" \",clinom2x,\" \",cliape1x,\" \",cliape2x)))) AS clinomxx ";
                        $qCliente .= "FROM $cAlfa.lpar0150 ";
                        $qCliente .= "WHERE ";
                        $qCliente .= "cliidxxx = \"$gCliId\" AND ";
                        $qCliente .= "cliclixx = \"SI\" AND ";
                        $qCliente .= "regestxx = \"ACTIVO\" ";
                        $qCliente .= "LIMIT 0,1";
                        $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
                        while ($xRCL = mysql_fetch_array($xCliente)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm'].cCliId.value  = "<?php echo $xRCL['cliidxxx'] ?>";
                            parent.fmwork.document.forms['frgrm'].cCliDV.value  = "<?php echo gendv($xRCL['cliidxxx'])?>";
                            parent.fmwork.document.forms['frgrm'].cCliNom.value = "<?php echo str_replace(array('"',"'"),array('\"',"\'"),$xRCL['clinomxx']) ?>";
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
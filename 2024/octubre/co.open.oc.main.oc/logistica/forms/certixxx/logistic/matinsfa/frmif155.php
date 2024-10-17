<?php
  namespace openComex;
	/**
	 * --- Descripcion: Consulta los Depósitos:
	 * @author Diego Fernando Cortes Rojas <diego.cortes@openits.co>
	 * @package openComex
   * @version 001
	 */

	include("../../../../../financiero/libs/php/utility.php");

  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Dep&oacute;sito</title>
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
                  <legend>Param&eacute;trica de Dep&oacute;sito</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qDatDepo  = "SELECT ";
                        $qDatDepo .= "lpar0155.depnumxx, ";
                        $qDatDepo .= "lpar0155.ccoidocx, ";
                        $qDatDepo .= "lpar0007.tdeidxxx, ";
                        $qDatDepo .= "lpar0007.tdedesxx, ";
                        $qDatDepo .= "lpar0005.pfaidxxx, ";
                        $qDatDepo .= "lpar0005.pfadesxx, ";
                        $qDatDepo .= "lpar0155.regestxx ";
                        $qDatDepo .= "FROM $cAlfa.lpar0155 ";                        
                        $qDatDepo .= "LEFT JOIN $cAlfa.lpar0007 ON $cAlfa.lpar0155.tdeidxxx = $cAlfa.lpar0007.tdeidxxx ";
                        $qDatDepo .= "LEFT JOIN $cAlfa.lpar0005 ON $cAlfa.lpar0155.pfaidxxx = $cAlfa.lpar0005.pfaidxxx ";
                        $qDatDepo .= "WHERE ";
                        $qDatDepo .= "lpar0155.cliidxxx = \"$gCliId\" AND ";
                        $qDatDepo .= "lpar0155.depnumxx LIKE \"%$gDepNum%\" AND ";
                        $qDatDepo .= "lpar0155.regestxx = \"ACTIVO\" ";
                        $xDatDepo  = f_MySql("SELECT","",$qDatDepo,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qDatDepo."~".mysql_num_rows($xDatDepo));

                        if (mysql_num_rows($xDatDepo) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "050" Class = "name"><center>Id</center></td>
                                <td width = "400" Class = "name"><center>Tipo Dep&oacute;sito</center></td>
                              </tr>
                                <?php
                                while ($xRDC = mysql_fetch_array($xDatDepo)){
                                  if (mysql_num_rows($xDatDepo) > 1) { ?>
                                    <tr>
                                      <td width = "050" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm'].cDepNum.value    = '<?php echo $xRDC['depnumxx']?>';
                                                                window.opener.document.forms['frgrm'].cTipoDep.value   = '<?php echo $xRDC['tdedesxx']?>';
                                                                window.opener.document.forms['frgrm'].cPerFacDes.value = '<?php echo $xRDC['pfadesxx']?>';
                                                                window.opener.document.forms['frgrm'].cCcoIdOc.value   = '<?php echo $xRDC['ccoidocx']?>';
                                                                window.opener.fnLinks('cDepNum','EXACT',0);
                                                                window.close();
                                                                window.opener.fnHabilitaSubServicio('<?php echo "SI" ?>','<?php echo $xRDC['depnumxx']?>');"><?php echo $xRDC['depnumxx'] ?></a>
                                      </td>
                                      <td width = "400" class= "name"><?php echo $xRDC['tdedesxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm'].cDepNum.value    = "<?php echo $xRDC['depnumxx'] ?>";
                                      window.opener.document.forms['frgrm'].cTipoDep.value   = "<?php echo $xRDC['tdedesxx'] ?>";
                                      window.opener.document.forms['frgrm'].cPerFacDes.value = "<?php echo $xRDC['pfadesxx'] ?>";
                                      window.opener.document.forms['frgrm'].cCcoIdOc.value   = "<?php echo $xRDC['ccoidocx'] ?>";
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
                        $qDatDepo  = "SELECT ";
                        $qDatDepo .= "lpar0155.depnumxx, ";
                        $qDatDepo .= "lpar0155.ccoidocx, ";
                        $qDatDepo .= "lpar0007.tdeidxxx, ";
                        $qDatDepo .= "lpar0007.tdedesxx, ";
                        $qDatDepo .= "lpar0005.pfaidxxx, ";
                        $qDatDepo .= "lpar0005.pfadesxx, ";
                        $qDatDepo .= "lpar0155.regestxx ";
                        $qDatDepo .= "FROM $cAlfa.lpar0155 ";                        
                        $qDatDepo .= "LEFT JOIN $cAlfa.lpar0007 ON $cAlfa.lpar0155.tdeidxxx = $cAlfa.lpar0007.tdeidxxx ";
                        $qDatDepo .= "LEFT JOIN $cAlfa.lpar0005 ON $cAlfa.lpar0155.pfaidxxx = $cAlfa.lpar0005.pfaidxxx ";
                        $qDatDepo .= "WHERE ";
                        $qDatDepo .= "lpar0155.cliidxxx = \"$gCliId\" AND ";
                        $qDatDepo .= "lpar0155.depnumxx LIKE \"%$gDepNum%\" AND ";
                        $qDatDepo .= "lpar0155.regestxx = \"ACTIVO\" ";
                        $xDatDepo  = f_MySql("SELECT","",$qDatDepo,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qDatDepo."~".mysql_num_rows($xDatDepo));
                        if (mysql_num_rows($xDatDepo) > 0){
                          if (mysql_num_rows($xDatDepo) == 1){
                            while ($xRDC = mysql_fetch_array($xDatDepo)) { 
                              $gDepNum = $xRDC['depnumxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm'].cDepNum.value    = "<?php echo $xRDC['depnumxx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cTipoDep.value   = "<?php echo $xRDC['tdedesxx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cPerFacDes.value = "<?php echo $xRDC['pfadesxx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cCcoIdOc.value   = "<?php echo $xRDC['ccoidocx'] ?>";
                                parent.fmwork.fnLinks('cDepNum','EXACT',0);
                                parent.fmwork.fnHabilitaSubServicio('<?php echo "SI" ?>','<?php echo $xRDC['depnumxx']?>');
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
                            parent.fmwork.document.forms['frgrm'].cDepNum.value    = "";
                            parent.fmwork.document.forms['frgrm'].cTipoDep.value   = "";
                            parent.fmwork.document.forms['frgrm'].cPerFacDes.value = "";
                            parent.fmwork.document.forms['frgrm'].cCcoIdOc.value   = "";
                            parent.fmwork.fnHabilitaSubServicio('<?php echo "NO" ?>','','');
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qDatDepo  = "SELECT ";
                        $qDatDepo .= "lpar0155.depnumxx, ";
                        $qDatDepo .= "lpar0155.ccoidocx, ";
                        $qDatDepo .= "lpar0007.tdeidxxx, ";
                        $qDatDepo .= "lpar0007.tdedesxx, ";
                        $qDatDepo .= "lpar0005.pfaidxxx, ";
                        $qDatDepo .= "lpar0005.pfadesxx, ";
                        $qDatDepo .= "lpar0155.regestxx ";
                        $qDatDepo .= "FROM $cAlfa.lpar0155 ";                        
                        $qDatDepo .= "LEFT JOIN $cAlfa.lpar0007 ON $cAlfa.lpar0155.tdeidxxx = $cAlfa.lpar0007.tdeidxxx ";
                        $qDatDepo .= "LEFT JOIN $cAlfa.lpar0005 ON $cAlfa.lpar0155.pfaidxxx = $cAlfa.lpar0005.pfaidxxx ";
                        $qDatDepo .= "WHERE ";
                        $qDatDepo .= "lpar0155.cliidxxx = \"$gCliId\" AND ";
                        $qDatDepo .= "lpar0155.depnumxx = \"$gDepNum\" AND ";
                        $qDatDepo .= "lpar0155.regestxx = \"ACTIVO\" ";
                        $qDatDepo .= "ORDER BY lpar0155.depnumxx ";
                        $xDatDepo  = f_MySql("SELECT","",$qDatDepo,$xConexion01,"");
                        while ($xRDC = mysql_fetch_array($xDatDepo)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm'].cDepNum.value    = "<?php echo $xRDC['depnumxx'] ?>";
                            parent.fmwork.document.forms['frgrm'].cTipoDep.value   = "<?php echo $xRDC['tdedesxx'] ?>";
                            parent.fmwork.document.forms['frgrm'].cPerFacDes.value = "<?php echo $xRDC['pfadesxx'] ?>";
                            parent.fmwork.document.forms['frgrm'].cCcoIdOc.value   = "<?php echo $xRDC['ccoidocx'] ?>";
                            parent.fmwork.fnHabilitaSubServicio('<?php echo "SI" ?>','<?php echo $xRDC['depnumxx']?>');
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
	} 
?>
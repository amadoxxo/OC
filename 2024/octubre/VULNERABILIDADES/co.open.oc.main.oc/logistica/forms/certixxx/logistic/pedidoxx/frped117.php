<?php
  namespace openComex;
	/**
	 * --- Descripcion: Consulta los Comprobantes:
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */

	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Comprobantes</title>
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
                  <legend>Param&eacute;trica de Comprobantes</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qDatComp  = "SELECT ";
                        $qDatComp .= "comidxxx, ";
                        $qDatComp .= "comcodxx, ";
                        $qDatComp .= "comprexx, ";
                        $qDatComp .= "comdesxx, ";
                        $qDatComp .= "comtcoxx, ";
                        $qDatComp .= "comccoxx, ";
                        $qDatComp .= "regestxx ";
                        $qDatComp .= "FROM $cAlfa.lpar0117 ";
                        $qDatComp .= "WHERE ";
                        switch ($gFunction) {
                          case 'cComPre':
                            $qDatComp .= "comprexx LIKE \"%$gComPre%\" AND ";
                          break;
                        }
                        $qDatComp .= "comidxxx = \"P\" AND ";
                        $qDatComp .= "regestxx = \"ACTIVO\" ";
                        $qDatComp .= "ORDER BY comcodxx ";
                        $xDatComp  = f_MySql("SELECT","",$qDatComp,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qDatComp."~".mysql_num_rows($xDatComp));
                        if (mysql_num_rows($xDatComp) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "050" Class = "name"><center>Prefijo</center></td>
                                <td width = "400" Class = "name"><center>Comprobante</center></td>
                              </tr>
                                <?php
                                while ($xRDC = mysql_fetch_array($xDatComp)){
                                  if (mysql_num_rows($xDatComp) > 1) { ?>
                                    <tr>
                                      <td width = "050" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm'].cComPre.value = '<?php echo $xRDC['comprexx']?>';
                                                                window.opener.document.forms['frgrm'].cComId.value  = '<?php echo $xRDC['comidxxx']?>';
                                                                window.opener.document.forms['frgrm'].cComCod.value = '<?php echo $xRDC['comcodxx']?>';
                                                                window.opener.document.forms['frgrm'].cComTCo.value = '<?php echo $xRDC['comtcoxx']?>';
                                                                window.opener.document.forms['frgrm'].cComCco.value = '<?php echo $xRDC['comccoxx']?>';
                                                                window.opener.fnLinks('cComPre','EXACT',0);
                                                                window.close();"><?php echo $xRDC['comprexx'] ?></a>
                                      </td>
                                      <td width = "400" class= "name"><?php echo $xRDC['comdesxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm'].cComPre.value = "<?php echo $xRDC['comprexx'] ?>";
                                      window.opener.document.forms['frgrm'].cComId.value  = "<?php echo $xRDC['comidxxx'] ?>";
                                      window.opener.document.forms['frgrm'].cComCod.value = "<?php echo $xRDC['comcodxx'] ?>";
                                      window.opener.document.forms['frgrm'].cComTCo.value = "<?php echo $xRDC['comtcoxx'] ?>";
                                      window.opener.document.forms['frgrm'].cComCco.value = "<?php echo $xRDC['comccoxx'] ?>";
                                      window.opener.fnLinks('cComPre', 'EXACT', 0);
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
                        $qDatComp  = "SELECT ";
                        $qDatComp .= "comidxxx, ";
                        $qDatComp .= "comcodxx, ";
                        $qDatComp .= "comprexx, ";
                        $qDatComp .= "comdesxx, ";
                        $qDatComp .= "comtcoxx, ";
                        $qDatComp .= "comccoxx, ";
                        $qDatComp .= "regestxx ";
                        $qDatComp .= "FROM $cAlfa.lpar0117 ";
                        $qDatComp .= "WHERE ";
                        switch ($gFunction) {
                          case 'cComPre':
                            $qDatComp .= "comprexx LIKE \"%$gComPre%\" AND ";
                          break;
                        }
                        $qDatComp .= "comidxxx = \"P\" AND ";
                        $qDatComp .= "regestxx = \"ACTIVO\" ";
                        $qDatComp .= "ORDER BY comcodxx ";
                        $xDatComp  = f_MySql("SELECT","",$qDatComp,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qDatComp."~".mysql_num_rows($xDatComp));
                        if (mysql_num_rows($xDatComp) > 0){
                          if (mysql_num_rows($xDatComp) == 1){
                            while ($xRDC = mysql_fetch_array($xDatComp)) { 
                              $gComPre = $xRDC['comprexx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm'].cComPre.value = "<?php echo $xRDC['comprexx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cComId.value  = "<?php echo $xRDC['comidxxx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cComCod.value = "<?php echo $xRDC['comcodxx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cComTCo.value = "<?php echo $xRDC['comtcoxx'] ?>";
                                parent.fmwork.document.forms['frgrm'].cComCco.value = "<?php echo $xRDC['comccoxx'] ?>";
                                parent.fmwork.fnLinks('cComPre','EXACT',0);
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
                            parent.fmwork.document.forms['frgrm'].cComPre.value = "";
                            parent.fmwork.document.forms['frgrm'].cComId.value  = "";
                            parent.fmwork.document.forms['frgrm'].cComCod.value = "";
                            parent.fmwork.document.forms['frgrm'].cComTCo.value = "";
                            parent.fmwork.document.forms['frgrm'].cComCco.value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qDatComp  = "SELECT ";
                        $qDatComp .= "comidxxx, ";
                        $qDatComp .= "comcodxx, ";
                        $qDatComp .= "comprexx, ";
                        $qDatComp .= "comdesxx, ";
                        $qDatComp .= "comtcoxx, ";
                        $qDatComp .= "comccoxx, ";
                        $qDatComp .= "regestxx ";
                        $qDatComp .= "FROM $cAlfa.lpar0117 ";
                        $qDatComp .= "WHERE ";
                        switch ($gFunction) {
                          case 'cComPre':
                            $qDatComp .= "comprexx = \"$gComPre\" AND ";
                          break;
                        }
                        $qDatComp .= "comidxxx = \"P\" AND ";
                        $qDatComp .= "regestxx = \"ACTIVO\" ";
                        $qDatComp .= "LIMIT 0,1";
                        $xDatComp  = f_MySql("SELECT","",$qDatComp,$xConexion01,"");
                        while ($xRDC = mysql_fetch_array($xDatComp)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm'].cComPre.value = "<?php echo $xRDC['comprexx'] ?>";
                            parent.fmwork.document.forms['frgrm'].cComId.value  = "<?php echo $xRDC['comidxxx'] ?>";
                            parent.fmwork.document.forms['frgrm'].cComCod.value = "<?php echo $xRDC['comcodxx'] ?>";
                            parent.fmwork.document.forms['frgrm'].cComTCo.value = "<?php echo $xRDC['comtcoxx'] ?>";
                            parent.fmwork.document.forms['frgrm'].cComCco.value = "<?php echo $xRDC['comccoxx'] ?>";
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
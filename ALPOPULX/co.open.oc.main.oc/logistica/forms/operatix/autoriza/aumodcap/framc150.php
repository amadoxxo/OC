<?php
	/**
	 * --- Descripcion: Consulta los Clientes:
	 * @author Elian Amado. <elian.amado@openits.co>
	 * @package openComex
   * @version 001
	 */
	include("../../../../../libs/php/utility.php");
  
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
                        $qDatCli  = "SELECT ";
                        $qDatCli .= "cliidxxx, clisapxx, ";
                        $qDatCli .= "IF(clinomxx != \"\",clinomxx,(TRIM(CONCAT(clinomxx,\" \",clinom1x,\" \",clinom2x,\" \",cliape1x,\" \",cliape2x)))) AS clinomxx,";
                        $qDatCli .= "regestxx ";
                        $qDatCli .= "FROM $cAlfa.lpar0150 ";
                        $qDatCli .= "WHERE ";
                        switch ($gFunction) {
                          case 'cCliId':
                            $qDatCli .= "cliidxxx LIKE \"%$gCliId%\" AND ";
                          break;
                        }
                        $qDatCli .= "cliclixx = \"SI\" AND ";
                        $qDatCli .= "regestxx = \"ACTIVO\" ";
                        $qDatCli .= "ORDER BY cliidxxx ";
                        $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qDatCli."~".mysql_num_rows($xDatCli));
                        if (mysql_num_rows($xDatCli) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "050" Class = "name"><center>Nit</center></td>
                                <td width = "400" Class = "name"><center>Nombre</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRDC = mysql_fetch_array($xDatCli)){
                                  if (mysql_num_rows($xDatCli) > 1) { ?>
                                    <tr>
                                      <td width = "050" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm']['cCliId'+'<?php echo $gSecuencia ?>'].value  = '<?php echo $xRDC['cliidxxx']?>';
                                                                window.opener.document.forms['frgrm']['cCliDV'+'<?php echo $gSecuencia ?>'].value  = '<?php echo gendv($xRDC['cliidxxx'])?>';
                                                                window.opener.document.forms['frgrm']['cCliSap'+'<?php echo $gSecuencia ?>'].value = '<?php echo $xRDC['clisapxx']?>';
                                                                window.opener.document.forms['frgrm']['cCliNom'+'<?php echo $gSecuencia ?>'].value = '<?php echo $xRDC['clinomxx']?>';
                                                                window.opener.document.forms['frgrm']['cPedComCsc'+'<?php echo $gSecuencia ?>'].value = '';
                                                                window.opener.fnLinks('cCliId','EXACT','<?php echo $gSecuencia ?>');
                                                                window.close();"><?php echo $xRDC['cliidxxx'] ?></a>
                                      </td>
                                      <td width = "400" class= "name"><?php echo $xRDC['clinomxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xRDC['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm']['cCliId'+'<?php echo $gSecuencia ?>'].value  = "<?php echo $xRDC['cliidxxx'] ?>";
                                      window.opener.document.forms['frgrm']['cCliDV'+'<?php echo $gSecuencia ?>'].value  = "<?php echo gendv($xRDC['cliidxxx'])?>";
                                      window.opener.document.forms['frgrm']['cCliSap'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRDC['clisapxx'] ?>";
                                      window.opener.document.forms['frgrm']['cCliNom'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRDC['clinomxx'] ?>";
                                      window.opener.document.forms['frgrm']['cPedComCsc'+'<?php echo $gSecuencia ?>'].value = "";
                                      window.opener.fnLinks('cCliId', 'EXACT', '<?php echo $gSecuencia ?>');
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
                        $qDatCli  = "SELECT ";
                        $qDatCli .= "cliidxxx, clisapxx, ";
                        $qDatCli .= "IF(clinomxx != \"\",clinomxx,(TRIM(CONCAT(clinomxx,\" \",clinom1x,\" \",clinom2x,\" \",cliape1x,\" \",cliape2x)))) AS clinomxx ";
                        $qDatCli .= "FROM $cAlfa.lpar0150 ";
                        $qDatCli .= "WHERE ";
                        switch ($gFunction) {
                          case 'cCliId':
                            $qDatCli .= "cliidxxx LIKE \"%$gCliId%\" AND ";
                          break;
                        }
                        $qDatCli .= "cliclixx = \"SI\" AND ";
                        $qDatCli .= "regestxx = \"ACTIVO\" ";
                        $qDatCli .= "ORDER BY cliidxxx";
                        $xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qDatCli."~".mysql_num_rows($xDatCli));

                        if (mysql_num_rows($xDatCli) > 0){
                          if (mysql_num_rows($xDatCli) == 1){
                            while ($xRDC = mysql_fetch_array($xDatCli)) { 
                              $gCliId = $xRDC['cliidxxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm']['cCliId'+'<?php echo $gSecuencia ?>'].value  = "<?php echo $xRDC['cliidxxx'] ?>";
                                parent.fmwork.document.forms['frgrm']['cCliDV'+'<?php echo $gSecuencia ?>'].value  = "<?php echo gendv($xRDC['cliidxxx'])?>";
                                parent.fmwork.document.forms['frgrm']['cCliSap'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRDC['clisapxx'] ?>";
                                parent.fmwork.document.forms['frgrm']['cCliNom'+'<?php echo $gSecuencia ?>'].value = "<?php echo $xRDC['clinomxx'] ?>";
                                parent.fmwork.document.forms['frgrm']['cPedComCsc'+'<?php echo $gSecuencia ?>'].value = "";
                                parent.fmwork.fnLinks('cCliId','EXACT','<?php echo $gSecuencia ?>');
                              </script>
                            <?php }
                          }else{ ?>
                            <script language = "javascript">
                              parent.fmwork.fnLinks('<?php echo $gFunction ?>','WINDOW', '<?php echo $gSecuencia ?>');
                              window.close();
                            </script>
                          <?php
                          }
                        }else{ ?>
                          <script language = "javascript">
                            alert('No hay registros coincidentes');
                            parent.fmwork.document.forms['frgrm']['cCliId'+'<?php echo $gSecuencia ?>'].value  = "";
                            parent.fmwork.document.forms['frgrm']['cCliDV'+'<?php echo $gSecuencia ?>'].value  = "";
                            parent.fmwork.document.forms['frgrm']['cCliSap'+'<?php echo $gSecuencia ?>'].value  = "";
                            parent.fmwork.document.forms['frgrm']['cCliNom'+'<?php echo $gSecuencia ?>'].value = "";
                            parent.fmwork.document.forms['frgrm']['cPedComCsc'+'<?php echo $gSecuencia ?>'].value = "";
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
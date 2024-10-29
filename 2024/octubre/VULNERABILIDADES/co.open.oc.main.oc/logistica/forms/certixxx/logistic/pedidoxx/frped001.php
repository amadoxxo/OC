<?php
  namespace openComex;
	/**
	 * --- Descripcion: Consulta los Organizacion de Ventas:
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */
	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Organizaci&oacute;n de Venta</title>
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
                  <legend>Param&eacute;trica de Organizaci&oacute;n de Ventas</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qOrgVenta  = "SELECT ";
                        $qOrgVenta .= "orvsapxx, ";
                        $qOrgVenta .= "orvdesxx, ";
                        $qOrgVenta .= "regestxx ";
                        $qOrgVenta .= "FROM $cAlfa.lpar0001 ";
                        $qOrgVenta .= "WHERE ";
                        switch ($gFunction) {
                          case 'cOrvSap':
                            $qOrgVenta .= "orvsapxx LIKE \"%$gOrvSap%\" AND ";
                          break;
                          case 'cOrvDes':
                            $qOrgVenta .= "orvdesxx LIKE \"%$gOrvDes%\" AND ";
                          break;
                        }
                        $qOrgVenta .= "regestxx = \"ACTIVO\" ";
                        $qOrgVenta .= "ORDER BY orvsapxx ";
                        $xOrgVenta  = f_MySql("SELECT","",$qOrgVenta,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qOrgVenta."~".mysql_num_rows($xOrgVenta));

                        if (mysql_num_rows($xOrgVenta) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "100" Class = "name"><center>Cod. SAP</center></td>
                                <td width = "350" Class = "name"><center>Descripci&oacute;n</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xROV = mysql_fetch_array($xOrgVenta)){
                                  if (mysql_num_rows($xOrgVenta) > 1) { ?>
                                    <tr>
                                      <td width = "100" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm']['cOrvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $xROV['orvsapxx']?>';
                                                                window.opener.document.forms['frgrm']['cOrvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $xROV['orvdesxx']?>';
                                                                window.opener.fnLinks('cOrvSap','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
                                                                window.close();"><?php echo $xROV['orvsapxx'] ?></a>
                                      </td>
                                      <td width = "350" class= "name"><?php echo $xROV['orvdesxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xROV['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm']['cOrvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xROV['orvsapxx'] ?>";
                                      window.opener.document.forms['frgrm']['cOrvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xROV['orvdesxx'] ?>";
                                      window.opener.fnLinks('cOrvSap', 'EXACT', ,'<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
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
                        $qOrgVenta  = "SELECT ";
                        $qOrgVenta .= "orvsapxx, ";
                        $qOrgVenta .= "orvdesxx, ";
                        $qOrgVenta .= "regestxx ";
                        $qOrgVenta .= "FROM $cAlfa.lpar0001 ";
                        $qOrgVenta .= "WHERE ";
                        switch ($gFunction) {
                          case 'cOrvSap':
                            $qOrgVenta .= "orvsapxx LIKE \"%$gOrvSap%\" AND ";
                          break;
                          case 'cOrvDes':
                            $qOrgVenta .= "orvdesxx LIKE \"%$gOrvDes%\" AND ";
                          break;
                        }
                        $qOrgVenta .= "regestxx = \"ACTIVO\" ";
                        $qOrgVenta .= "ORDER BY orvsapxx ";
                        $xOrgVenta  = f_MySql("SELECT","",$qOrgVenta,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qOrgVenta."~".mysql_num_rows($xOrgVenta));

                        if (mysql_num_rows($xOrgVenta) > 0){
                          if (mysql_num_rows($xOrgVenta) == 1){
                            while ($xROV = mysql_fetch_array($xOrgVenta)) { 
                              $gOrvSap = $xROV['orvsapxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm']['cOrvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xROV['orvsapxx'] ?>";
                                parent.fmwork.document.forms['frgrm']['cOrvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xROV['orvdesxx'] ?>";
                                parent.fmwork.fnLinks('cOrvSap','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
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
                            parent.fmwork.document.forms['frgrm']['cOrvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "";
                            parent.fmwork.document.forms['frgrm']['cOrvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qOrgVenta  = "SELECT ";
                        $qOrgVenta .= "orvsapxx, ";
                        $qOrgVenta .= "orvdesxx, ";
                        $qOrgVenta .= "regestxx ";
                        $qOrgVenta .= "FROM $cAlfa.lpar0001 ";
                        $qOrgVenta .= "WHERE ";
                        switch ($gFunction) {
                          case 'cOrvSap':
                            $qOrgVenta .= "orvsapxx = \"$gOrvSap\" AND ";
                          break;
                          case 'cOrvDes':
                            $qOrgVenta .= "orvdesxx = \"$gOrvDes\" AND ";
                          break;
                        }
                        $qOrgVenta .= "regestxx = \"ACTIVO\" ";
                        $qOrgVenta .= "ORDER BY orvsapxx ";
                        $xOrgVenta  = f_MySql("SELECT","",$qOrgVenta,$xConexion01,"");
                        while ($xROV = mysql_fetch_array($xOrgVenta)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cOrvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xROV['orvsapxx'] ?>";
                            parent.fmwork.document.forms['frgrm']['cOrvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xROV['orvdesxx'] ?>";
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
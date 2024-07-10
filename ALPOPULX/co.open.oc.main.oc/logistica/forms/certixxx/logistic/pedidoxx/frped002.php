<?php
	/**
	 * --- Descripcion: Consulta los Oficina de Ventas:
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */
	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Oficina de Venta</title>
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
                  <legend>Param&eacute;trica de Oficina de Ventas</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qOfiVenta  = "SELECT ";
                        $qOfiVenta .= "ofvsapxx, ";
                        $qOfiVenta .= "ofvdesxx, ";
                        $qOfiVenta .= "regestxx ";
                        $qOfiVenta .= "FROM $cAlfa.lpar0002 ";
                        $qOfiVenta .= "WHERE ";
                        switch ($gFunction) {
                          case 'cOfvSap':
                            $qOfiVenta .= "ofvsapxx LIKE \"%$gOfvSap%\" AND ";
                          break;
                          case 'cOfvDes':
                            $qOfiVenta .= "ofvdesxx LIKE \"%$gOfvDes%\" AND ";
                          break;
                        }
                        if ($gGrid != "") {
                          $qOfiVenta .= "orvsapxx = \"$gOrvSap\" AND ";
                        }
                        $qOfiVenta .= "regestxx = \"ACTIVO\" ";
                        $qOfiVenta .= "ORDER BY ofvsapxx ";
                        $xOfiVenta  = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qOfiVenta."~".mysql_num_rows($xOfiVenta));

                        if (mysql_num_rows($xOfiVenta) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "100" Class = "name"><center>Cod. SAP</center></td>
                                <td width = "350" Class = "name"><center>Descripci&oacute;n</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xROV = mysql_fetch_array($xOfiVenta)){
                                  if (mysql_num_rows($xOfiVenta) > 1) { ?>
                                    <tr>
                                      <td width = "100" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm']['cOfvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $xROV['ofvsapxx']?>';
                                                                window.opener.document.forms['frgrm']['cOfvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $xROV['ofvdesxx']?>';
                                                                window.opener.fnLinks('cOfvSap','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
                                                                window.close();"><?php echo $xROV['ofvsapxx'] ?></a>
                                      </td>
                                      <td width = "350" class= "name"><?php echo $xROV['ofvdesxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xROV['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm']['cOfvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xROV['ofvsapxx'] ?>";
                                      window.opener.document.forms['frgrm']['cOfvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xROV['ofvdesxx'] ?>";
                                      window.opener.fnLinks('cOfvSap', 'EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
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
                        $qOfiVenta  = "SELECT ";
                        $qOfiVenta .= "ofvsapxx, ";
                        $qOfiVenta .= "ofvdesxx, ";
                        $qOfiVenta .= "regestxx ";
                        $qOfiVenta .= "FROM $cAlfa.lpar0002 ";
                        $qOfiVenta .= "WHERE ";
                        switch ($gFunction) {
                          case 'cOfvSap':
                            $qOfiVenta .= "ofvsapxx LIKE \"%$gOfvSap%\" AND ";
                          break;
                          case 'cOfvDes':
                            $qOfiVenta .= "ofvdesxx LIKE \"%$gOfvDes%\" AND ";
                          break;
                        }
                        if ($gGrid != "") {
                          $qOfiVenta .= "orvsapxx = \"$gOrvSap\" AND ";
                        }
                        $qOfiVenta .= "regestxx = \"ACTIVO\" ";
                        $qOfiVenta .= "ORDER BY ofvsapxx ";
                        $xOfiVenta  = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qOfiVenta."~".mysql_num_rows($xOfiVenta));

                        if (mysql_num_rows($xOfiVenta) > 0){
                          if (mysql_num_rows($xOfiVenta) == 1){
                            while ($xROV = mysql_fetch_array($xOfiVenta)) { 
                              $gOfvSap = $xROV['ofvsapxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm']['cOfvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xROV['ofvsapxx'] ?>";
                                parent.fmwork.document.forms['frgrm']['cOfvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xROV['ofvdesxx'] ?>";
                                parent.fmwork.fnLinks('cOfvSap','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
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
                            parent.fmwork.document.forms['frgrm']['cOfvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "";
                            parent.fmwork.document.forms['frgrm']['cOfvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qOfiVenta  = "SELECT ";
                        $qOfiVenta .= "ofvsapxx, ";
                        $qOfiVenta .= "ofvdesxx, ";
                        $qOfiVenta .= "regestxx ";
                        $qOfiVenta .= "FROM $cAlfa.lpar0002 ";
                        $qOfiVenta .= "WHERE ";
                        switch ($gFunction) {
                          case 'cOfvSap':
                            $qOfiVenta .= "ofvsapxx = \"$gOfvSap\" AND ";
                          break;
                          case 'cOfvDes':
                            $qOfiVenta .= "ofvdesxx = \"$gOfvDes\" AND ";
                          break;
                        }
                        if ($gGrid != "") {
                          $qOfiVenta .= "orvsapxx = \"$gOrvSap\" AND ";
                        }
                        $qOfiVenta .= "regestxx = \"ACTIVO\" ";
                        $qOfiVenta .= "ORDER BY ofvsapxx ";
                        $xOfiVenta  = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");
                        while ($xROV = mysql_fetch_array($xOfiVenta)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cOfvSap'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xROV['ofvsapxx'] ?>";
                            parent.fmwork.document.forms['frgrm']['cOfvDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xROV['ofvdesxx'] ?>";
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
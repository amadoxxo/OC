<?php
	/**
	 * --- Descripcion: Consulta los Tipo Deposito:
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
	 */
	include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Tipo Dep&oacute;sito</title>
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
                  <legend>Param&eacute;trica de Tipo Dep&oacute;sitos</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qTipoDep  = "SELECT ";
                        $qTipoDep .= "tdeidxxx, ";
                        $qTipoDep .= "tdedesxx, ";
                        $qTipoDep .= "regestxx ";
                        $qTipoDep .= "FROM $cAlfa.lpar0007 ";
                        $qTipoDep .= "WHERE ";
                        switch ($gFunction) {
                          case 'cTdeDes':
                            $qTipoDep .= "tdedesxx LIKE \"%$gTdeDes%\" AND ";
                          break;
                        }
                        if ($gTdeId != "") {
                          $qTipoDep .= "tdeidxxx LIKE \"%$gTdeId%\" AND ";
                        }
                        $qTipoDep .= "regestxx = \"ACTIVO\" ";
                        $qTipoDep .= "ORDER BY tdeidxxx ";
                        $xTipoDep  = f_MySql("SELECT","",$qTipoDep,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qTipoDep."~".mysql_num_rows($xTipoDep));

                        if (mysql_num_rows($xTipoDep) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "100" Class = "name"><center>Id</center></td>
                                <td width = "350" Class = "name"><center>Descripci&oacute;n</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRTD = mysql_fetch_array($xTipoDep)){
                                  if (mysql_num_rows($xTipoDep) > 1) { ?>
                                    <tr>
                                      <td width = "100" class= "name">
                                          <a href = "javascript:window.opener.document.forms['frgrm']['cTdeId'+'<?php echo $gGrid . $gSecuencia ?>'].value  = '<?php echo $xRTD['tdeidxxx']?>';
                                                                window.opener.document.forms['frgrm']['cTdeDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = '<?php echo $xRTD['tdedesxx']?>';
                                                                window.opener.fnLinks('cTdeDes','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
                                                                window.close();"><?php echo $xRTD['tdeidxxx'] ?></a>
                                      </td>
                                      <td width = "350" class= "name"><?php echo $xRTD['tdedesxx'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xRTD['regestxx'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      window.opener.document.forms['frgrm']['cTdeId'+'<?php echo $gGrid . $gSecuencia ?>'].value  = "<?php echo $xRTD['tdeidxxx'] ?>";
                                      window.opener.document.forms['frgrm']['cTdeDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRTD['tdedesxx'] ?>";
                                      window.opener.fnLinks('cTdeDes', 'EXACT', '<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
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
                        $qTipoDep  = "SELECT ";
                        $qTipoDep .= "tdeidxxx, ";
                        $qTipoDep .= "tdedesxx, ";
                        $qTipoDep .= "regestxx ";
                        $qTipoDep .= "FROM $cAlfa.lpar0007 ";
                        $qTipoDep .= "WHERE ";
                        switch ($gFunction) {
                          case 'cTdeDes':
                            $qTipoDep .= "tdedesxx LIKE \"%$gTdeDes%\" AND ";
                          break;
                        }
                        if ($gTdeId != "") {
                          $qTipoDep .= "tdeidxxx LIKE \"%$gTdeId%\" AND ";
                        }
                        $qTipoDep .= "regestxx = \"ACTIVO\" ";
                        $qTipoDep .= "ORDER BY tdeidxxx ";
                        $xTipoDep  = f_MySql("SELECT","",$qTipoDep,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qTipoDep."~".mysql_num_rows($xTipoDep));

                        if (mysql_num_rows($xTipoDep) > 0){
                          if (mysql_num_rows($xTipoDep) == 1){
                            while ($xRTD = mysql_fetch_array($xTipoDep)) { 
                              $gTdeId = $xRTD['tdeidxxx'];
                              ?>
                              <script language = "javascript">
                                parent.fmwork.document.forms['frgrm']['cTdeId'+'<?php echo $gGrid . $gSecuencia ?>'].value  = "<?php echo $xRTD['tdeidxxx'] ?>";
                                parent.fmwork.document.forms['frgrm']['cTdeDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRTD['tdedesxx'] ?>";
                                parent.fmwork.fnLinks('cTdeDes','EXACT','<?php echo $gSecuencia ?>','<?php echo $gGrid ?>');
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
                            parent.fmwork.document.forms['frgrm']['cTdeId'+'<?php echo $gGrid . $gSecuencia ?>'].value  = "";
                            parent.fmwork.document.forms['frgrm']['cTdeDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "";
                          </script>
                          <?php
                        }
                      break;
                      case "EXACT":
                        $qTipoDep  = "SELECT ";
                        $qTipoDep .= "tdeidxxx, ";
                        $qTipoDep .= "tdedesxx, ";
                        $qTipoDep .= "regestxx ";
                        $qTipoDep .= "FROM $cAlfa.lpar0007 ";
                        $qTipoDep .= "WHERE ";
                        switch ($gFunction) {
                          case 'cTdeDes':
                            $qTipoDep .= "tdedesxx = \"$gTdeDes\" AND ";
                          break;
                        }
                        if ($gTdeId != "") {
                          $qTipoDep .= "tdeidxxx = \"$gTdeId\" AND ";
                        }
                        $qTipoDep .= "regestxx = \"ACTIVO\" ";
                        $qTipoDep .= "ORDER BY tdeidxxx ";
                        $xTipoDep  = f_MySql("SELECT","",$qTipoDep,$xConexion01,"");
                        while ($xRTD = mysql_fetch_array($xTipoDep)) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frgrm']['cTdeId'+'<?php echo $gGrid . $gSecuencia ?>'].value  = "<?php echo $xRTD['tdeidxxx'] ?>";
                            parent.fmwork.document.forms['frgrm']['cTdeDes'+'<?php echo $gGrid . $gSecuencia ?>'].value = "<?php echo $xRTD['tdedesxx'] ?>";
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
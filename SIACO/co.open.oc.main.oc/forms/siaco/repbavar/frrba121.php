<?php
  /**
   * --- Descripcion: Consulta Tramites:
   * @author Juan Jose Trujillo Ch. <juan.trujillo@open-eb.co>
   * @package openComex
   * @version 001
   */
  include("../../../libs/php/utility.php");    
  
  if ($gWhat != "" && $gFunction != "") { ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Tramites</title>
        <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css">
        <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css">
        <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css">
        <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css">
        <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory_New ?>/overlib.css">
      </head>
      <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
        <center>
          <table border ="0" cellpadding="0" cellspacing="0" width="300">
            <tr>
              <td>
                <fieldset>
                  <legend>Param&eacute;trica de Tramites</legend>
                  <form name = "frnav" action = "" method = "post" target = "fmpro">
                    <?php
                    $vNitCli = explode(",", $vSysStr['siacosia_reporte_proformas']);
                    $cNitCli = "\"".implode("\",\"", $vNitCli)."\"";

                    switch ($gWhat) {
                      case "WINDOW":
                        $qDatDex  = "SELECT ";
                        $qDatDex .= "sucidxxx,";
                        $qDatDex .= "docidxxx,";
                        $qDatDex .= "docsufxx, ";
                        $qDatDex .= "doctipxx ";
                        $qDatDex .= "FROM $cAlfa.sys00121 ";
                        $qDatDex .= "WHERE ";
                        $qDatDex .= "docidxxx LIKE \"%$gDexId%\" AND ";
                        if ($gTerId != '') {
                          $qDatDex .= "cliidxxx = \"$gTerId\" AND ";
                        } else {
                          $qDatDex .= "cliidxxx IN($cNitCli) AND ";
                        }
                        $qDatDex .= "doctipxx IN(\"EXPORTACION\", \"OTROS\") AND ";
                        $qDatDex .= "regestxx = \"FACTURADO\" ";
                        $xDatDex  = f_MySql("SELECT","",$qDatDex,$xConexion01,"");
                        if (mysql_num_rows($xDatDex) > 0){ ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "320">
                              <tr>
                                <td widht = "60" Class = "name" ><center>Sucursal</center></td>
                                <td widht = "260" Class = "name"><center>Tipos Operaci&oacute;n</center></td>
                                <td widht = "260" Class = "name"><center>Dex/Otros</center></td>
                              </tr>
                              <?php
                              while ($xRDD = mysql_fetch_array($xDatDex)){
                                if (mysql_num_rows($xDatDex) > 1){ ?>
                                  <tr>
                                    <td width = "60" Class = "name" align="center"><?php echo $xRDD['sucidxxx'] ?></td>
                                    <td width = "260" Class = "name" align="center"><?php echo $xRDD['doctipxx'] ?></td>
                                    <td width = "60" Class = "name" align="center">
                                      <a href = "javascript:window.opener.document.forms['frnav']['cDexId'].value = '<?php echo $xRDD['docidxxx'] ?>';
                                                            window.opener.document.forms['frnav']['cSucId'].value = '<?php echo $xRDD['sucidxxx'] ?>';
                                                            window.opener.fnLinks('cDexId','EXACT',0);
                                        window.close();"><?php echo $xRDD['docidxxx'] ?>
                                      </a>
                                    </td>
                                  </tr>
                                <?php
                                }else{ ?>
                                  <script type ="text/javascript">
                                    window.opener.document.forms['frnav']['cSucId'].value = '<?php echo $xRDD['sucidxxx'] ?>';
                                    window.opener.document.forms['frnav']['cDexId'].value = '<?php echo $xRDD['docidxxx'] ?>';
                                    window.close();
                                  </script>
                                <?php
                                }
                              } ?>
                            </table>
                          </center>
                        <?php
                        }else{ ?>
                          <script language="JavaScript">
                            window.opener.document.forms['frnav']['cDexId'].value = '';
                            window.opener.document.forms['frnav']['cSucId'].value = '';
                          </script>
                          <?php
                          f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique."); ?>
                          <script language="JavaScript">
                            window.close();
                          </script>
                        <?php
                        }
                      break;
                      case "VALID":
                        $qDatDex  = "SELECT ";
                        $qDatDex .= "sucidxxx,";
                        $qDatDex .= "docidxxx,";
                        $qDatDex .= "docsufxx,";
                        $qDatDex .= "doctipxx ";
                        $qDatDex .= "FROM $cAlfa.sys00121 ";
                        $qDatDex .= "WHERE ";
                        $qDatDex .= "docidxxx LIKE \"%$gDexId%\" AND ";
                        if ($gTerId != '') {
                          $qDatDex .= "cliidxxx = \"$gTerId\" AND ";
                        } else {
                          $qDatDex .= "cliidxxx IN($cNitCli) AND ";
                        }
                        $qDatDex .= "doctipxx IN(\"EXPORTACION\", \"OTROS\") AND ";
                        $qDatDex .= "regestxx = \"FACTURADO\" ";
                        $xDatDex  = f_MySql("SELECT","",$qDatDex,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qDatDex."~".mysql_num_rows($xDatDex));

                        $vDatDoi  = mysql_fetch_array($xDatDex);
                        if (mysql_num_rows($xDatDex) > 0) {
                          if (mysql_num_rows($xDatDex) == 1){ ?>
                            <script type = "text/javascript">
                              parent.fmwork.document.forms['frnav']['cSucId'].value = "<?php echo $vDatDoi['sucidxxx']?>";
                              parent.fmwork.document.forms['frnav']['cDexId'].value = "<?php echo $vDatDoi['docidxxx']?>";
                            </script>
                          <?php
                          }else{ ?>
                            <script type = "text/javascript">
                              parent.fmwork.fnLinks("<?php echo $gFunction ?>","WINDOW"); 
                            </script>
                          <?php
                          }
                        }else{
                          f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique.");?>
                          <script type = "text/javascript">
                            parent.fmwork.document.forms['frnav']['cDexId'].value = '';
                            parent.fmwork.document.forms['frnav']['cSucId'].value = '';
                          </script>
                        <?php 
                        }
                      break;
                      case "EXACT":
                        $qDatDex  = "SELECT ";
                        $qDatDex .= "sucidxxx,";
                        $qDatDex .= "docidxxx,";
                        $qDatDex .= "docsufxx,";
                        $qDatDex .= "doctipxx ";
                        $qDatDex .= "FROM $cAlfa.sys00121 ";
                        $qDatDex .= "WHERE ";
                        $qDatDex .= "docidxxx = \"$gDexId\" AND ";
                        $qDatDex .= "sucidxxx = \"$gSucId\" AND ";
                        if ($gTerId != '') {
                          $qDatDex .= "cliidxxx = \"$gTerId\" AND ";
                        } else {
                          $qDatDex .= "cliidxxx IN($cNitCli) AND ";
                        }
                        $qDatDex .= "doctipxx IN(\"EXPORTACION\", \"OTROS\") AND ";
                        $qDatDex .= "regestxx = \"FACTURADO\" ";
                        $xDatDex  = f_MySql("SELECT","",$qDatDex,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qDatDex."~".mysql_num_rows($xDatDex));

                        $vDatDoi  = mysql_fetch_array($xDatDex);
                        if (mysql_num_rows($xDatDex) == 1) { ?>
                          <script language = "javascript">
                            parent.fmwork.document.forms['frnav']['cSucId'].value = "<?php echo $vDatDoi['sucidxxx']?>";
                            parent.fmwork.document.forms['frnav']['cDexId'].value = "<?php echo $vDatDoi['docidxxx']?>";
                          </script>
                        <?php
                        }else{ ?>
                          <script type = "text/javascript">
                            parent.fmwork.document.forms['frnav']['cDexId'].value = '';
                            parent.fmwork.document.forms['frnav']['cSucId'].value = '';
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
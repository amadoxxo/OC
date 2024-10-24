<?php
  namespace openComex;
  /**
   * --- Descripcion: Consulta los Usuarios:
   * @author Cristian Perdomo <cristian.perdomo@openits.co>
   * @package openComex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");
  
  if ($gWhat != "" && $gFunction != "") { 
    ?>
    <!DOCTYPE html>
    <html>
      <head>
        <title>Param&eacute;trica de Usuarios</title>
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
                  <legend>Param&eacute;trica de Usuarios</legend>
                  <form name = "frgrm" action = "" method = "post" target = "fmpro">
                    <?php
                    switch ($gWhat) {
                      case "WINDOW":
                        $qDatUsr  = "SELECT ";
                        $qDatUsr .= "USRIDXXX, ";
                        $qDatUsr .= "USRNOMXX, ";
                        $qDatUsr .= "USRNOMXX,";
                        $qDatUsr .= "REGESTXX ";
                        $qDatUsr .= "FROM $cAlfa.SIAI0003 ";
                        $qDatUsr .= "WHERE ";
                        $qDatUsr .= "USRIDXXX != \"ADMIN\" AND ";
                        $qDatUsr .= "USRINTXX != \"SI\"    AND ";
                        $qDatUsr .= "USRMOPLO = \"1\"      AND ";
                        switch ($gFunction) {
                          case 'cUsrId':
                          case 'cResId':
                            $qDatUsr .= "USRIDXXX LIKE \"%$gUsrId%\" AND ";
                          break;
                          case 'cUsrNom':
                          case 'cResNom':
                            $qDatUsr .= "USRNOMXX LIKE \"%$gUsrId%\" AND ";
                          break;
                        }
                        $qDatUsr .= "REGESTXX = \"ACTIVO\"";
                        // $qDatUsr .= " ORDER BY USRNOMXX ";
                        $xDatUsr  = f_MySql("SELECT","",$qDatUsr,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qDatUsr."~".mysql_num_rows($xDatUsr));
                        if (mysql_num_rows($xDatUsr) > 0) { ?>
                          <center>
                            <table cellspacing = "0" cellpadding = "1" border = "1" width = "500">
                              <tr bgcolor = '#D6DFF7'>
                                <td width = "050" Class = "name"><center>Nit</center></td>
                                <td width = "400" Class = "name"><center>Nombre</center></td>
                                <td width = "050" Class = "name"><center>Estado</center></td>
                              </tr>
                                <?php
                                while ($xRDC = mysql_fetch_array($xDatUsr)){
                                  if (mysql_num_rows($xDatUsr) > 1) { ?>
                                    <tr>
                                      <td width = "050" class= "name">
                                          <a href = "javascript:switch ('<?php echo $gFunction ?>') {
                                                                  case 'cUsrId':
                                                                  case 'cUsrNom':
                                                                    window.opener.document.forms['frgrm']['cUsrId'].value  = '<?php echo $xRDC['USRIDXXX']?>';
                                                                    window.opener.document.forms['frgrm']['cUsrNom'].value = '<?php echo $xRDC['USRNOMXX']?>';
                                                                  break;
                                                                  case 'cResId':
                                                                  case 'cResNom':
                                                                    window.opener.document.forms['frgrm']['cResId'].value  = '<?php echo $xRDC['USRIDXXX']?>';
                                                                    window.opener.document.forms['frgrm']['cResNom'].value = '<?php echo $xRDC['USRNOMXX']?>';
                                                                  break;                                                                  
                                                                }
                                                                window.close();"><?php echo $xRDC['USRIDXXX'] ?></a>
                                      </td>
                                      <td width = "400" class= "name"><?php echo $xRDC['USRNOMXX'] ?></td>
                                      <td width = "050" class= "name"><?php echo $xRDC['REGESTXX'] ?></td>
                                    </tr>
                                    <?php
                                  }else { ?>
                                    <script languaje="javascript">
                                      switch ('<?php echo $gFunction ?>') {
                                        case 'cUsrId':
                                        case 'cUsrNom':
                                          window.opener.document.forms['frgrm']['cUsrId'].value  = '<?php echo $xRDC['USRIDXXX']?>';
                                          window.opener.document.forms['frgrm']['cUsrNom'].value = '<?php echo $xRDC['USRNOMXX']?>';
                                        break;
                                        case 'cResId':
                                        case 'cResNom':
                                          window.opener.document.forms['frgrm']['cResId'].value  = '<?php echo $xRDC['USRIDXXX']?>';
                                          window.opener.document.forms['frgrm']['cResNom'].value = '<?php echo $xRDC['USRNOMXX']?>';
                                        break;                                                                  
                                      }
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
                            switch ('<?php echo $gFunction ?>') {
                              case 'cUsrId':
                              case 'cUsrNom':
                                window.opener.document.forms['frgrm']['cUsrId'].value  = '';
                                window.opener.document.forms['frgrm']['cUsrNom'].value = '';
                              break;
                              case 'cResId':
                              case 'cResNom':
                                window.opener.document.forms['frgrm']['cResId'].value  = '';
                                window.opener.document.forms['frgrm']['cResNom'].value = '';
                              break;                                                                  
                            }
                            window.close();
                          </script>
                        <?php
                        }
                      break;
                      case "VALID":
                        $qDatUsr  = "SELECT ";
                        $qDatUsr .= "USRIDXXX, ";
                        $qDatUsr .= "USRNOMXX, ";
                        $qDatUsr .= "USRNOMXX,";
                        $qDatUsr .= "REGESTXX ";
                        $qDatUsr .= "FROM $cAlfa.SIAI0003 ";
                        $qDatUsr .= "WHERE ";
                        $qDatUsr .= "USRIDXXX != \"ADMIN\" AND ";
                        $qDatUsr .= "USRINTXX != \"SI\"    AND ";
                        $qDatUsr .= "USRMOPLO = \"1\"      AND ";
                        switch ($gFunction) {
                          case 'cUsrId':
                          case 'cResId':
                            $qDatUsr .= "USRIDXXX LIKE \"%$gUsrId%\" AND ";
                          break;
                          case 'cUsrNom':
                          case 'cResNom':
                            $qDatUsr .= "USRNOMXX LIKE \"%$gUsrId%\" AND ";
                          break;
                        }
                        $qDatUsr .= "REGESTXX = \"ACTIVO\"";
                        // $qDatUsr .= " ORDER BY USRNOMXX ";
                        $xDatUsr  = f_MySql("SELECT","",$qDatUsr,$xConexion01,"");
                        // f_Mensaje(__FILE__, __LINE__,$qDatUsr."~".mysql_num_rows($xDatUsr));
                        if (mysql_num_rows($xDatUsr) == 1){
                          while ($xRDC = mysql_fetch_array($xDatUsr)) { 
                            ?>
                            <script language = "javascript">
                              switch ('<?php echo $gFunction ?>') {
                                case 'cUsrId':
                                case 'cUsrNom':
                                  parent.fmwork.document.forms['frgrm']['cUsrId'].value  = "<?php echo $xRDC['USRIDXXX'] ?>";
                                  parent.fmwork.document.forms['frgrm']['cUsrNom'].value = "<?php echo $xRDC['USRNOMXX'] ?>";
                                break;
                                case 'cResId':
                                case 'cResNom':
                                  parent.fmwork.document.forms['frgrm']['cResId'].value  = "<?php echo $xRDC['USRIDXXX'] ?>";
                                  parent.fmwork.document.forms['frgrm']['cResNom'].value = "<?php echo $xRDC['USRNOMXX'] ?>";
                                break;                                                                  
                              }
                            </script>
                          <?php }
                        } else { ?>
                          <script language = "javascript">
                            parent.fmwork.fnLinks('<?php echo $gFunction ?>','WINDOW');
                            window.close();
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
<?php
  namespace openComex;
/**
 * Nuevo Cargar Codigo Cebe.
 * @package opencomex
 * @author oscar.perez@openits.co
 * 
 * Variables:
 * @var int     $nCol
 * @var string  $qBotAcc
 * @var mixed   $xBotAcc 
 * @var array   $mBotAcc 
 */
# Librerias
include("../../../../../financiero/libs/php/utility.php");
?>
  <head>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
    <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>

    <script languaje = 'javascript'>
      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      }
      
      function fnGuardar() {
        document.forms['frgrm'].action = "frcceccg.php";
        document.forms['frgrm'].submit();
      }
      
      function fnDownLoad(xTipo) {
        document.forms['frgrm']['cTipo'].value = xTipo;
        document.forms['frgrm'].action = "frccwexc.php";
        document.forms['frgrm'].submit();
      }
    </script>
  </head>
  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
    <!-- PRIMERO PINTO EL FORMULARIO -->
    <center>
      <table border ="0" cellpadding="0" cellspacing="0">
        <tr>
          <td>
            <form name = "frgrm" enctype='multipart/form-data' action = "frcceccg.php" method = "post" target="fmpro">
              <input type="hidden" name="cTipo" value="0">
              <table border ="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td>
                    <fieldset>
                      <legend>Carga de <?php echo $_COOKIE['kProDes'] ?></legend>
                      <table border = "0" cellpadding = "0" cellspacing = "0" width="380">
                        <?php $nCol = f_Format_Cols(19); echo $nCol; ?>
                        <tr>
                          <td Class="name" colspan="19">El archivo debe ser un TXT separado por tabulaciones<br>
                            <input type = "file" Class = "letra" style = "width:380px;height:22px" name = "cArcPla">
                          </td>
                        </tr>
                        <tr>
                          <td Class="name" colspan="9"><br>
                            <a href = "javascript:fnDownLoad('0')">Descargar Formato</a>
                          </td>
                          <td Class="name" colspan="10" align="right"><br>
                            <?php
                              $qBotAcc  = "SELECT sys00039.mendesxx ";
                              $qBotAcc .= "FROM $cAlfa.sys00039,$cAlfa.sys00040 ";
                              $qBotAcc .= "WHERE ";
                              $qBotAcc .= "sys00040.usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
                              $qBotAcc .= "sys00040.modidxxx = sys00039.modidxxx        AND ";
                              $qBotAcc .= "sys00040.proidxxx = sys00039.proidxxx        AND ";
                              $qBotAcc .= "sys00040.menidxxx = sys00039.menidxxx        AND ";
                              $qBotAcc .= "sys00040.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
                              $qBotAcc .= "sys00040.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
                              $qBotAcc .= "sys00040.menidxxx = \"50\" LIMIT 0,1";
                              $xBotAcc  = f_MySql("SELECT","",$qBotAcc,$xConexion01,"");
                              // f_mensaje(__FILE__,__LINE__,$qBotAcc."~".mysql_num_rows($xBotAcc));
                              $mBotAcc = mysql_fetch_array($xBotAcc);
                              if (mysql_num_rows($xBotAcc) > 0) {
                                ?>
                                <a href = "javascript:fnDownLoad('1')"><?php echo $mBotAcc['mendesxx'] ?></a>
                                <?php
                              }
                              ?>
                          </td>
                        </tr>
                      </table>
                    </fieldset>
                    <table border = "0" cellpadding = "0" cellspacing = "0" width="400">
                      <tr height="21">
                        <td width="218" height="21">&nbsp;</td>
                        <td width="91" height="21" Class="name" >
                          <input type="button" name="Btn_Subir" id="Btn_Subir" value="Subir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_ok_bg.gif) no-repeat;border:0px"
                            onclick = "javascript:fnGuardar()">
                        </td>
                        <td width="91" height="21" Class="name" >
                          <input type="button" value="Salir" Class = "name"  style = "width:91;height:21;background:url(<?php echo $cPlesk_Skin_Directory_Logistic ?>/btn_cancel_bg.gif) no-repeat;border:0px"
                            onClick = "javascript:fnRetorna()">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>                                    
            </form>
          </td>
        </tr>
      </table>
    </center>
  </body>
</html>
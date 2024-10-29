<?php
  namespace openComex;
/**
 * Proceso Clientes.
 * --- Descripcion: Permite Buscar concidencias de Clientes por el codigo.
 * @author oscar.perez@openits.co
 * @package emisioncero
 * @version 001
*/
include("../../../../../financiero/libs/php/utility.php");
$qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_SCHEMA = \"$cAlfa\" AND TABLE_NAME = \"par00101\" LIMIT 0,1";
$xTblCom = f_MySql("SELECT","",$qTblCom,$xConexion01,"");
$vTblCom = mysql_fetch_array($xTblCom);
?>
<html>
  <title><?php echo $vTblCom['TABLE_COMMENT'] ?></title>
  <head>
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
    <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
  </head>
  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
    <div id = 'overDiv' style = 'position:absolute; visibility:hide; z-index:1'></div>
    <?php
    if ($cCampo == 'cliidxxx')	{
      $nInd = 0;
      $qDatCli  = "SELECT * ";
      $qDatCli .= "FROM $cAlfa.lpar0150 ";
      $qDatCli .= "WHERE ";
      $qDatCli .= "cliidxxx = \"$cCliId\" ";
    } else {
      $nInd = 1;
      $qDatCli  = "SELECT * ";
      $qDatCli .= "FROM $cAlfa.lpar0150 ";
      $qDatCli .= "WHERE ";
      $qDatCli .= "clinomxx LIKE \"%$cCliId%\" ";
    }
    $xDatCli = f_MySql("SELECT","",$qDatCli,$xConexion01,"");

    if (mysql_num_rows($xDatCli) > 0) {
      $y = 0;
      $cadena = '';
      ?>
      <center><br><br><br>
        <table cellspacing = 0 cellpadding = 0 border = 1 style='width:500'>
          <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
            <td widht = "060" Class = "letra7"><center>Cod</center></td>
            <td widht = "400" Class = "letra7"><center>Nombres</center></td>
            <td widht = "040" Class = "letra7"><center>Estado</center></td>
          </tr>
          <?php
          while ($mDatCli = mysql_fetch_array($xDatCli)) {
            $y ++;
            $cColor = "{$vSysStr['system_row_impar_color_ini']}";
            if($y % 2 == 0) {
              $cColor = "{$vSysStr['system_row_par_color_ini']}";
            }
            ?>
            <tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
              <td width = "060" class= "letra7"><?php echo $mDatCli['cliidxxx'] ?></td>
              <td width = "400" class= "letra7"><?php
                if ($mDatCli['clinomxx'] != "") {
                  echo $mDatCli['clinomxx'];
                } else {
                  echo $mDatCli['cliape1x']." ".$mDatCli['cliape2x']." ".$mDatCli['clinom1x']." ".$mDatCli['clinom2x'];
                }
              ?></td>
              <td width = "040" class= "letra7"><?php echo $mDatCli['regestxx'] ?></td>
            </tr>
            <?php
          }
          ?>
        </table>
      </center>
      <?php
      if ($nInd == 0) {
        f_Mensaje(__FILE__,__LINE__,"El numero de Identificacion digitado ya existe, favor intentar de nuevo");
      } else {
        f_Mensaje(__FILE__,__LINE__,"Registros que coinciden con la descripcion digitada");
      }
    }	else {
      ?>
      <script languaje='javascript'>
        window.close();
      </script>
      <?php
    }
    ?>
  </body>
</html>
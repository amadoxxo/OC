<?php
namespace openComex;

/**
 * Grillas para el formulario de Tipo de Ticket.
 * --- Descripcion: Permite cargar las grillas en el formulario.
 * @author cristian.perdomo@openits.co
 * @package opencomex
 * @version 001
 */

include("../../../../../financiero/libs/php/utility.php");

$cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"540\">";
$cTexto .= "<tr bgcolor = \"" . $vSysStr['system_row_title_color_ini'] . "\">";
$cTexto .= "<td Class = \"clase08\" width = \"20\" ><center>" . (($_COOKIE['kModo'] != "VER") ? "<img src = \"" . $cPlesk_Skin_Directory . "/btn_create-dir_bg.gif\" onClick =\"javascript:fnLinks(\'cResTic\',\'VALID\')\" style = \"cursor:hand\" alt=\"Adicionar Organizaci&oacute;n de Venta\" >" : "") . "</center></td>";
$cTexto .= "<td Class = \"clase08\" width = \"120\" style=\"padding-left:5px\">Identificaci&oacute;n</td>";
$cTexto .= "<td Class = \"clase08\" width = \"400\" style=\"padding-left:5px\">Nombre</td>";
$cTexto .= "</tr>";

//Primero Cargo una Matriz con los Clientes
if ($gCseTipTicta != "") {
  $mMatrizInt = explode("~", $gCseTipTicta);

  $cadena = '';
  $y = 0;
  for ($i = 0; $i < count($mMatrizInt); $i++) {
    if ($mMatrizInt[$i] != "") {
      $qTipTicta  = "SELECT ";
      $qTipTicta .= "USRIDXXX, ";
      $qTipTicta .= "USRNOMXX ";
      $qTipTicta .= "FROM $cAlfa.SIAI0003 ";
      $qTipTicta .= "WHERE ";
      $qTipTicta .= "USRIDXXX = \"{$mMatrizInt[$i]}\" AND ";
      $qTipTicta .= "USRIDXXX != \"ADMIN\" AND ";
      $qTipTicta .= "USRINTXX != \"SI\" AND ";
      $qTipTicta .= "USRMOPLO = \"1\" AND ";
      $qTipTicta .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
      $xTipTicta = f_MySql("SELECT", "", $qTipTicta, $xConexion01, "");

      if (mysql_num_rows($xTipTicta) > 0) {
        $xTipTicta = mysql_fetch_array($xTipTicta);

        $y++;

        $cId   = $xTipTicta['USRIDXXX'];
        $zColor = "{$vSysStr['system_row_impar_color_ini']}";
        if ($y % 2 == 0) {
          $zColor = "{$vSysStr['system_row_par_color_ini']}";
        }
        $cTexto .= "<tr bgcolor = \"$zColor\" onmouseover=\"javascript:uRowColor(this,\'" . $vSysS['system_row_select_color_ini'] . "\')\" onmouseout=\"javascript:uRowColor(this,\'$zColor\')\">";
        $cTexto .= "<td Class = \"clase08\"><center>" . (($_COOKIE['kModo'] != "VER") ? "<img src = \"" . $cPlesk_Skin_Directory . "/btn_remove-selected_bg.gif\" onClick =\"javascript:fnEliminarResponsable(\'$cId\')\" style = \"cursor:hand\" alt=\"Borrar Organizacion de Venta: " . $mMatrizInt[$i] . " - " . substr($xTipTicta['USRNOMXX'], 0, 60) . "\">" : "") . "</center></td>";
        $cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">" . substr($xTipTicta['USRIDXXX'], 0, 10) . "</td>";
        $cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">" . substr($xTipTicta['USRNOMXX'], 0, 60) . "</td>";
        $cTexto .= "</tr>";
      }
    }
  }
}
$cTexto .= "</table>";
?>
<script languaje="javascript">
  parent.fmwork.document.getElementById('overDivResponsable').innerHTML = '<?php echo $cTexto ?>';
</script>
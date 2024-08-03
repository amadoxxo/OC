<?php

/**
 * Grillas para el formulario de Tipo de Ticket.
 * --- Descripcion: Permite cargar las grillas en el formulario.
 * @author cristian.perdomo@openits.co
 * @package opencomex
 * @version 001
 */

include("../../../../../financiero/libs/php/utility.php");

$cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"570\">";
$cTexto .= "<tr bgcolor = \"" . $vSysStr['system_row_title_color_ini'] . "\">";
$cTexto .= "<td Class = \"clase08\" width = \"20\" ><center>" . (($_COOKIE['kModo'] != "VER") ? "<img src = \"" . $cPlesk_Skin_Directory . "/btn_create-dir_bg.gif\" onClick =\"javascript:fnLinks(\'cOrganizacionVenta\',\'VALID\')\" style = \"cursor:hand\" alt=\"Adicionar Organizaci&oacute;n de Venta\" >" : "") . "</center></td>";
$cTexto .= "<td Class = \"clase08\" width = \"150\" style=\"padding-left:5px\">Identificaci&oacute;n</td>";
$cTexto .= "<td Class = \"clase08\" width = \"400\" style=\"padding-left:5px\">Nombre</td>";
$cTexto .= "</tr>";

//Primero Cargo una Matriz con los Clientes
// if ($gCseOrgVenta != "") {
//   $mMatrizInt = explode("~", $gCseOrgVenta);

//   $cadena = '';
//   $y = 0;
//   for ($i = 0; $i < count($mMatrizInt); $i++) {
//     if ($mMatrizInt[$i] != "") {
//       $qOrgVenta  = "SELECT ";
//       $qOrgVenta .= "USRIDXXX, ";
//       $qOrgVenta .= "USRNOMXX ";
//       $qOrgVenta .= "FROM $cAlfa.SIAI0003 ";
//       $qOrgVenta .= "WHERE ";
//       $qOrgVenta .= "USRIDXXX = \"{$mMatrizInt[$i]}\" AND ";
//       //$qOrgVenta .= "USRMOPLO = \"1\"";
//       $qOrgVenta .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
//       $xOrgVenta = f_MySql("SELECT", "", $qOrgVenta, $xConexion01, "");

//       if (mysql_num_rows($xOrgVenta) > 0) {
//         $xOrgVenta = mysql_fetch_array($xOrgVenta);

//         $y++;

//         $cId   = $xOrgVenta['USRIDXXX'];
//         $zColor = "{$vSysStr['system_row_impar_color_ini']}";
//         if ($y % 2 == 0) {
//           $zColor = "{$vSysStr['system_row_par_color_ini']}";
//         }
//         $cTexto .= "<tr bgcolor = \"$zColor\" onmouseover=\"javascript:uRowColor(this,\'" . $vSysS['system_row_select_color_ini'] . "\')\" onmouseout=\"javascript:uRowColor(this,\'$zColor\')\">";
//         $cTexto .= "<td Class = \"clase08\"><center>" . (($_COOKIE['kModo'] != "VER") ? "<img src = \"" . $cPlesk_Skin_Directory . "/btn_remove-selected_bg.gif\" onClick =\"javascript:fnEliminarResponsable(\'$cId\')\" style = \"cursor:hand\" alt=\"Borrar Organizacion de Venta: " . $mMatrizInt[$i] . " - " . substr($xOrgVenta['USRNOMXX'], 0, 60) . "\">" : "") . "</center></td>";
//         $cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">" . substr($xOrgVenta['USRIDXXX'], 0, 10) . "</td>";
//         $cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">" . substr($xOrgVenta['USRNOMXX'], 0, 60) . "</td>";
//         $cTexto .= "</tr>";
//       }
//     }
//   }
// }
$cTexto .= "</table>"; ?>
<script languaje="javascript">
  parent.fmwork.document.getElementById('overDivResponsable').innerHTML = '<?php echo $cTexto ?>';
</script>
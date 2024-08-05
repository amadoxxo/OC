<?php

/**
 * Grillas para el formulario de Responsables Asignados al Tipo de Ticket.
 * --- Descripcion: Permite cargar las grillas en el formulario.
 * @author elian.amado@openits.co
 * @package opencomex
 * @version 001
 */

include("../../../../../financiero/libs/php/utility.php");


$cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"670\">";
$cTexto .= "<tr bgcolor = \"". $vSysStr['system_row_title_color_ini'] ."\">";
$cTexto .= "<td Class = \"clase08\" width = \"230\" style = \"padding-left:5px;\">Identificaci&oacute;n</td>";
$cTexto .= "<td Class = \"clase08\" width = \"440\" style = \"padding-left:5px;\">Nombre</td>";
$cTexto .= "</tr>";

if ($gTtiCod != "") {
  $mMatrizRes = array();

  // Obtengo los datos de Responsable Asignado Ticket
  $qResTi  = "SELECT ";
  $qResTi .= "tticodxx, ";
  $qResTi .= "ttiusrxx ";
  $qResTi .= "FROM $cAlfa.lpar0159 ";
  $qResTi .= "WHERE ";
  $qResTi .= "tticodxx = \"$gTtiCod\" AND ";
  $qResTi .= "regestxx = \"ACTIVO\" ";
  $xResTi = f_MySql("SELECT","",$qResTi,$xConexion01,"");
  if (mysql_num_rows($xResTi) > 0) {
    while ($vResTi = mysql_fetch_array($xResTi)) {
      $nInd_mMatrizRes = count($mMatrizRes);
      $mMatrizRes[$nInd_mMatrizRes]['tticodxx'] = $vResTi['tticodxx'];
      $mMatrizRes[$nInd_mMatrizRes]['ttiusrxx'] = $vResTi['ttiusrxx'];
    }
  }

  for ($i=0;$i<count($mMatrizRes);$i++) { 
    $cTexto .= "<tr bgcolor=\"". $vSysStr['system_row_impar_color_ini'] ."\">";
    $cTexto .= "<td Class = \"clase08\" style = \"padding-left:5px;\">". $mMatrizRes[$i]['tticodxx'] ."</td>";
    $cTexto .= "<td Class = \"clase08\" style = \"padding-left:5px;\">". $mMatrizRes[$i]['ttiusrxx'] ."</td>";
    $cTexto .= "</tr>";
  }
  $cRePre = in_array($_COOKIE['kUsrId'], $mMatrizRes) ? "TERCERO" : "RESPONSABLE";
}
$cTexto .= "</table>"; 
$cTexto .= "<input type = \"hidden\" name = \"cRePre\" value = \"$cRePre\">"; ?>
<script languaje="javascript">
    parent.fmwork.document.getElementById('overDivResponsable').innerHTML = '<?php echo $cTexto ?>';
</script>
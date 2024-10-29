<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
 
  $qComId  = "SELECT * ";
	$qComId .= "FROM $cAlfa.fpar0117 ";
	$qComId .= "WHERE ";
	$qComId .= "comidxxx = \"P\"   AND ";
	switch ($gTipo) {
		case "PROPIOS":
			$qComId .= "comtipxx = \"CPE\" AND ";
		break;
		default:
			$qComId .= "comtipxx = \"CPC\" AND ";
		break;
	}
	$qComId .= "regestxx = \"ACTIVO\" ";
	$qComId .= "ORDER BY comidxxx,comcodxx ";
	$xComId  = f_MySql("SELECT","",$qComId,$xConexion01,"");
	//f_Mensaje(__FILE__,__LINE__,$qComId." ~ ".mysql_num_rows($xComId));
	
	$cLisCom = "";
	$nSec = 0;
	while ($xRCI = mysql_fetch_array($xComId)) {
		$cLisCom .= "<tr>";
			$cLisCom .= "<td Class = \"name\" align=\"center\">".$xRCI['comidxxx']."</td>";
			$cLisCom .= "<td Class = \"name\" align=\"center\">".str_pad($xRCI['comcodxx'],3,"0",STR_PAD_LEFT)."</td>";
			$cLisCom .= "<td Class = \"name\" align=\"left\" style=\"padding-left:5px\">".utf8_encode(substr($xRCI['comdesxx'],0,42))."</td>";
			$cLisCom .= "<td Class = \"name\" align=\"center\"><input type=\"checkbox\" name=\"oCheck\" value=\"".$xRCI['comidxxx'].'~'.$xRCI['comcodxx']."\" onclick=\"javascript:f_Activar_Check();f_Carga_Data();\" checked></td>";
			$cLisCom .= "<td Class = \"name\" align=\"center\"><input type=\"checkbox\" name=\"oCheckD\" value=\"".$xRCI['comidxxx'].'~'.$xRCI['comcodxx']."\" onclick=\"javascript:f_Activar_Check();f_Carga_Data();\" checked></td>";
			$cLisCom .= "<td Class = \"name\" align=\"center\"><input type=\"checkbox\" name=\"oCheckC\" value=\"".$xRCI['comidxxx'].'~'.$xRCI['comcodxx']."\" onclick=\"javascript:f_Activar_Check();f_Carga_Data();\" disabled></td>";
		$cLisCom .= "</tr>";
		$nSec++;
	}
	
	if($cLisCom <> "") {
		$cTexto  = "<table border = \"1\" cellpadding = \"0\" cellspacing = \"0\" width=\"700\">";
			$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
				$cTexto .= "<td Class = \"name\" width = \"100\" align=\"center\">Comprobante</td>";
				$cTexto .= "<td Class = \"name\" width = \"080\" align=\"center\">Codigo</td>";
				$cTexto .= "<td Class = \"name\" width = \"320\" align=\"center\">Descripci&oacute;n</td>";
				$cTexto .= "<td Class = \"name\" width = \"080\" align=\"center\">Seleccione</td>";
				$cTexto .= "<td Class = \"name\" width = \"060\" align=\"center\">Debito</td>";
				$cTexto .= "<td Class = \"name\" width = \"060\" align=\"center\">Credito</td>";
		$cTexto .= "</tr>";
		$cTexto .= $cLisCom;
		$cTexto .= "</table>";
	} else {
		$cTexto = "No Se Encontraron Comprobantes para el Tipo Concepto $gTipo";
	}
?>
<script languaje = "javascript">
	parent.fmwork.document.forms['frgrm']['gIteration'].value = '<?php echo $nSec ?>';
	parent.fmwork.document.getElementById('tblCom').innerHTML = '<?php echo $cTexto ?>';
	parent.fmwork.f_Activar_Check();
	parent.fmwork.f_Carga_Data();
</script>
	
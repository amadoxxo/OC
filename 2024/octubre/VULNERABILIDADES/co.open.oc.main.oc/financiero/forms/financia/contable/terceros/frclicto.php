<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  
  $cLisCom = "";
	
  $gCliCto = trim(strtoupper($gCliCto));
  $mCliCto = explode('|',$gCliCto);
  
  for ($i=0; $i<count($mCliCto); $i++) {
  	if($mCliCto[$i] <> "") {
		  $qDatIca  = "SELECT $cAlfa.fpar0121.* ";
		  $qDatIca .= "FROM $cAlfa.fpar0121 ";
		  $qDatIca .= "WHERE ";
		  $qDatIca .= "$cAlfa.fpar0121.ctoidxxx = \"{$mCliCto[$i]}\" AND ";
		  $qDatIca .= "$cAlfa.fpar0121.regestxx = \"ACTIVO\" ";
		  $qDatIca .= "ORDER BY ABS($cAlfa.fpar0121.ctoidxxx) ";
		  $xDatIca  = f_MySql("SELECT","",$qDatIca,$xConexion01,"");
		  // f_Mensaje(__FILE__,__LINE__,$qDatIca."~".mysql_num_rows($xDatIca));
			$nSec = 1;
		  while ($xRDI = mysql_fetch_array($xDatIca)) {
				$cLisCom .= "<tr height=\"25\">";
					$cLisCom .= "<td Class = \"name\" align=\"center\">".$xRDI['ctoidxxx']."</td>";
					$cLisCom .= "<td Class = \"name\" align=\"left\" style=\"padding-left:5px\">".utf8_encode(substr($xRDI['ctodesxx'],0,55))."</td>";
					$cLisCom .= "<td Class = \"name\" align=\"center\">".$xRDI['pucidxxx']."</td>";
					if($_COOKIE['kModo'] == 'VER') {
						$cLisCom .= "<td Class = \"name\" align=\"center\"><img src = \"$cPlesk_Skin_Directory/btn_remove-selected_bg.gif\" style=\"cursor:pointer\" onclick=\"javascript:alert(\'No Permitido.\')\"></td>";
					} else {
						$cLisCom .= "<td Class = \"name\" align=\"center\"><img src = \"$cPlesk_Skin_Directory/btn_remove-selected_bg.gif\" style=\"cursor:pointer\" onclick=\"javascript:f_Borrar_Conceptos(\'".$xRDI['ctoidxxx']."\')\"></td>";
					}
				$cLisCom .= "</tr>";
				$nSec++;
			}
  	}
  }
	$cTexto  = "<table border = \"1\" cellpadding = \"0\" cellspacing = \"0\" width=\"700\">";
		$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" height=\"21\">";
			$cTexto .= "<td Class = \"name\" width = \"100\" align=\"center\">Concepto</td>";
			$cTexto .= "<td Class = \"name\"  align=\"center\">Descripci&oacute;n</td>";
			$cTexto .= "<td Class = \"name\" width = \"100\" align=\"center\">Cuenta</td>";
			if($_COOKIE['kModo'] == 'VER') {
				$cTexto .= "<td Class = \"name\" width = \"025\" align=\"center\"><img src = \"$cPlesk_Skin_Directory/btn_create-dir_bg.gif\" style=\"cursor:pointer\" onclick=\"javascript:alert(\'No Permitido.\')\"></td>";		
			} else {
				$cTexto .= "<td Class = \"name\" width = \"025\" align=\"center\"><img src = \"$cPlesk_Skin_Directory/btn_create-dir_bg.gif\" style=\"cursor:pointer\" onclick=\"javascript:f_Links(\'cCliCto\',\'WINDOW\')\"></td>";
			}
	$cTexto .= "</tr>";
	$cTexto .= $cLisCom;
	$cTexto .= "</table>";
	
?>
<script languaje = "javascript">
	parent.fmwork.document.getElementById('tblCliCto').innerHTML = '<?php echo $cTexto ?>';
</script>
	
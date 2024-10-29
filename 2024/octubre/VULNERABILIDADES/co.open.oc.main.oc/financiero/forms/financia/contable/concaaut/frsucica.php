<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  
  $cLisCom = "";
	
  $gCtoCtori = trim(strtoupper($gCtoCtori));
  $mCtoCtori = explode('|',$gCtoCtori);
  
  for ($i=0; $i<count($mCtoCtori); $i++) {
  	if($mCtoCtori[$i] <> "") {
		  $qDatIca  = "SELECT $cAlfa.fpar0119.*, ";
		  $qDatIca .= "IF($cAlfa.fpar0119.ctodesxp <> \"\",$cAlfa.fpar0119.ctodesxp,IF($cAlfa.fpar0119.ctodesxx <> \"\",$cAlfa.fpar0119.ctodesxx,\"CONCEPTO SIN DESCRIPCION\")) AS ctodesxp, ";
		  $qDatIca .= "$cAlfa.fpar0115.pucretxx ";
		  $qDatIca .= "FROM $cAlfa.fpar0119 ";
		  $qDatIca .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
		  $qDatIca .= "WHERE ";
		  $qDatIca .= "$cAlfa.fpar0119.ctoidxxx = \"{$mCtoCtori[$i]}\" AND ";
		  $qDatIca .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
		  $qDatIca .= "ORDER BY ABS($cAlfa.fpar0119.ctoidxxx) ";
		  $xDatIca  = f_MySql("SELECT","",$qDatIca,$xConexion01,"");
		  //f_Mensaje(__FILE__,__LINE__,$qDatIca."~".mysql_num_rows($xDatIca));
			$nSec = 1;
		  while ($xRDI = mysql_fetch_array($xDatIca)) {
				$cLisCom .= "<tr height=\"25\">";
					$cLisCom .= "<td Class = \"name\" align=\"center\">".$xRDI['ctoidxxx']."</td>";
					$cLisCom .= "<td Class = \"name\" align=\"left\" style=\"padding-left:5px\">".utf8_encode(substr($xRDI['ctodesxp'],0,55))."</td>";
					$cLisCom .= "<td Class = \"name\" align=\"center\">".$xRDI['ctosucri']."</td>";
					$cLisCom .= "<td Class = \"name\" align=\"center\">".$xRDI['pucretxx']."</td>";
					if($_COOKIE['kModo'] == 'VER') {
						$cLisCom .= "<td Class = \"name\" align=\"center\"><img src = \"$cPlesk_Skin_Directory/btn_remove-selected_bg.gif\" style=\"cursor:pointer\" onclick=\"javascript:alert(\'No Permitido.\')\"></td>";
					} else {
						$cLisCom .= "<td Class = \"name\" align=\"center\"><img src = \"$cPlesk_Skin_Directory/btn_remove-selected_bg.gif\" style=\"cursor:pointer\" onclick=\"javascript:f_Borrar_SucRetIca(\'".$xRDI['ctoidxxx']."\')\"></td>";
					}
				$cLisCom .= "</tr>";
				$nSec++;
			}
  	}
  }
	$cTexto  = "<table border = \"1\" cellpadding = \"0\" cellspacing = \"0\" width=\"680\">";
		$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" height=\"21\">";
			$cTexto .= "<td Class = \"name\" width = \"100\" align=\"center\">Concepto</td>";
			$cTexto .= "<td Class = \"name\" width = \"335\" align=\"center\">Descripci&oacute;n</td>";
			$cTexto .= "<td Class = \"name\" width = \"080\" align=\"center\">Sucursal</td>";
			$cTexto .= "<td Class = \"name\" width = \"060\" align=\"center\">Tarifa</td>";
			if($_COOKIE['kModo'] == 'VER') {
				$cTexto .= "<td Class = \"name\" width = \"025\" align=\"center\"><img src = \"$cPlesk_Skin_Directory/btn_create-dir_bg.gif\" style=\"cursor:pointer\" onclick=\"javascript:alert(\'No Permitido.\')\"></td>";		
			} else {
				$cTexto .= "<td Class = \"name\" width = \"025\" align=\"center\"><img src = \"$cPlesk_Skin_Directory/btn_create-dir_bg.gif\" style=\"cursor:pointer\" onclick=\"javascript:f_Links(\'cCtoCtori\',\'WINDOW\')\"></td>";
			}
	$cTexto .= "</tr>";
	$cTexto .= $cLisCom;
	$cTexto .= "</table>";
	
?>
<script languaje = "javascript">
	parent.fmwork.document.getElementById('tblConIca').innerHTML = '<?php echo $cTexto ?>';
</script>
	
<?php
include("../../../libs/php/utility.php");

$vTipos = array();

//esta enviando un solo caso
if (isset($gTipo)) {
	$vTipos[0] = $gTipo;
}

//Esta enviando varios casos
if (isset($gParametro)) {
	$mAux = f_Explode_Array($gParametro,"|","^");
	for($i=0; $i<count($mAux);$i++) {
		if ($mAux[$i][0] != "") {
			switch ($mAux[$i][0]) {
				case "1";
					$vTipos[]  = $mAux[$i][0];
					$gColCtoId = $mAux[$i][1];
					$gDesPorc  = $mAux[$i][2];
				break;
				default:
					//No hace nada
				break;
			}
		}
	}
}
//Recorriendo todos los casos
for ($nT=0; $nT<count($vTipos);$nT++) {
	switch ($vTipos[$nT]) {
		case "1": //Responsabilidad Fiscal
			$cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"370\">";
				$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
					$cTexto .= "<td Class = \"clase08\" width = \"10\" ><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:fnLinks(\'cConceptoCobro\',\'VALID\')\" style = \"cursor:hand\" alt=\"Adicionar Responsabilidad Fiscal\" >" : "")."</center></td>";
					$cTexto .= "<td Class = \"clase08\" width = \"60\" style=\"padding-left:5px\">ID</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"300\" style=\"padding-left:5px\">Descripci&oacute;n Personalizada</td>";
				$cTexto .= "</tr>";
				//Primero Cargo una Matriz con los Clientes
				if ($gColCtoId != "") {
					$mMatrizInt = explode(",",$gColCtoId);
															
					$cadena = '';
					$y = 0;
					for ($i=0;$i<count($mMatrizInt);$i++) {
						if ($mMatrizInt[$i] != "") {
							$qResFis  = "SELECT seridxxx, serdespx ";
							$qResFis .= "FROM $cAlfa.fpar0129 ";
							$qResFis .= "WHERE ";
							$qResFis .= "seridxxx = \"{$mMatrizInt[$i]}\" AND ";
							$qResFis .= "regestxx = \"ACTIVO\" LIMIT 0,1";
							$xResFis = f_MySql("SELECT","",$qResFis,$xConexion01,"");
	
							if (mysql_num_rows($xResFis) > 0) {							
								while ($xRRF = mysql_fetch_array($xResFis)) {
									$y ++;
	
									$cId 	= $xRRF['seridxxx'];
									$zColor = "{$vSysStr['system_row_impar_color_ini']}";
									if($y % 2 == 0) {
										$zColor = "{$vSysStr['system_row_par_color_ini']}";
									}
									$cTexto .= "<tr bgcolor = \"$zColor\" onmouseover=\"javascript:uRowColor(this,\'".$vSysStr['system_row_select_color_ini']."\')\" onmouseout=\"javascript:uRowColor(this,\'$zColor\')\">";
										$cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelRes(\'$cId\')\" style = \"cursor:hand\" alt=\"Borrar Responsabilidad Fiscal: ".$mMatrizInt[$i]." - ".substr($xRRF['seridxxx'],0,60)."\">" : "")."</center></td>";
										$cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".substr($xRRF['seridxxx'],0,10)."</td>";
										$cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".substr($xRRF['serdespx'],0,60)."</td>";
										$cTexto .= "<td></td>";
									$cTexto .= "</tr>";
								}
							}
						}
					}
				}
			$cTexto .= "</table>"; ?>  
			<script languaje = "javascript">
				parent.fmwork.document.getElementById('overDivVen').innerHTML = '<?php echo $cTexto ?>';
			</script>
		<?php break;	
		default:
		 //No Hace Nada
		break;
	}
}
?>
	
<?php
include("../../../../libs/php/utility.php");

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
					$vTipos[] = $mAux[$i][0];
					$gCliDrl = $mAux[$i][1];
				break;
				case "2";
					$vTipos[] = $mAux[$i][0];
					$gCliVen = $mAux[$i][1];
				break;
				case "3";
					$vTipos[] = $mAux[$i][0];
					$gCliCon = $mAux[$i][1];
				break;
				case "4";
					$vTipos[] = $mAux[$i][0];
					$gCliCueBa = $mAux[$i][1];
				break;
				case "5";
					$vTipos[] = $mAux[$i][0];
					$gCliResFi = $mAux[$i][1];
				break;
				case "6";
					$vTipos[] = $mAux[$i][0];
					$gCliTri = $mAux[$i][1];
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
		case "1": //REQUISITO LEGAL
			// Buscando todos los requisitos legales
			$qDocLeg  = "SELECT drlidxxx, drldesxx ";
			$qDocLeg .= "FROM $cAlfa.fpar0110 ";
			$qDocLeg .= "WHERE ";
			$qDocLeg .= "regestxx LIKE \"ACTIVO\" ORDER BY drlidxxx ";
			$xDocLeg  = f_MySql("SELECT","",$qDocLeg,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qDocLeg." ~ ".mysql_num_rows($xDocLeg));
			
			$cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"700\">";
				$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
					$cTexto .= "<td Class = \"clase08\" width = \"020\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:f_Links(\'cRequisito\',\'VALID\')\" style = \"cursor:hand\" alt=\"Adicionar Documento Requisito Legal\" >" : "")."</center></td>";
					$cTexto .= "<td Class = \"clase08\" width = \"040\" style=\"padding-left:5px\">ID</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"480\" style=\"padding-left:5px\">Descripci&oacute;n</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"080\" style=\"padding-left:5px\">Fecha</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"080\" style=\"padding-left:5px\">Vencimiento</td>";
				$cTexto .= "</tr>";
				//Primero Cargo una Matriz con los Clientes
				
				if ($gCliDrl != "") {
					$mMatrizInt = array();
					$mId    = array();
					$mFecha = array();
					$mVenci = array();
					$mMatrizInt = explode("~",$gCliDrl);
					for ($i=0;$i<count($mMatrizInt);$i++) {
						if (strlen($mMatrizInt[$i]) > 0) {
							$mMatrizIntD = explode(",",$mMatrizInt[$i]);
							$Id     = $mMatrizIntD[0];
							$dFecha = $mMatrizIntD[1];
							$dVenci = $mMatrizIntD[2];
						}
						$mId[count($mId)]     = $Id;
						$mFecha["$Id"]  = $dFecha;
						$mVenci["$Id"]  = $dVenci;
					}
					
					$y = 0;
					while ($xRDL = mysql_fetch_array($xDocLeg)) {
						if (in_array($xRDL['drlidxxx'], $mId) == true) {
							$y ++;
							$cId    = $xRDL['drlidxxx'];
							$cFecha = $mFecha[$i];
							$cadena .= "|".$cId."|";
							$zColor = "{$vSysStr['system_row_impar_color_ini']}";
							if($y % 2 == 0) {
								$zColor = "{$vSysStr['system_row_par_color_ini']}";
							}
							
							$dFecVen = ($mVenci["{$xRDL['drlidxxx']}"] == "0000-00-00" || $mVenci["{$xRDL['drlidxxx']}"] == "") ? "&nbsp;" : $mVenci["{$xRDL['drlidxxx']}"];
							
							$cTexto .= "<tr bgcolor = \"".$zColor."\" onmouseover=\"javascript:uRowColor(this,\'".$vSysStr['system_row_select_color_ini']."\')\" onmouseout=\"javascript:uRowColor(this,\'".$zColor."\')\">";
							$cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelDrl(\'$cId\',\'$cFecha\')\" style = \"cursor:hand\" alt=\"Borrar Documento Requisito Legal: ".$mMatrizInt[$i]." - ".substr($xRDL['drldesxx'],0,60)."\">" : "")."</center></td>";
							$cTexto .= "<td Class = \"clase08\" style=\"text-align:center;padding-left:5px\">".substr($xRDL['drlidxxx'],0,10)."</td>";
							$cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".$xRDL['drldesxx']."</td>";
							$cTexto .= "<td Class = \"clase08\" style=\"text-align:center;padding-left:5px\">".$mFecha["{$xRDL['drlidxxx']}"]."</td>";
							$cTexto .= "<td Class = \"clase08\" style=\"text-align:center;padding-left:5px\">".$dFecVen."</td>";
							$cTexto .= "</tr>";
						}
					}				
				}
			$cTexto .= "</table>";  ?>	  
			<script languaje = "javascript">
				parent.fmwork.document.getElementById('overDivReq').innerHTML = '<?php echo $cTexto ?>';
			</script>
		<?php break;	  	
		case "2": //Vendedores
			$cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"700\">";
				$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
					$cTexto .= "<td Class = \"clase08\" width = \"20\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:f_Links(\'cTercero\',\'VALID\')\" style = \"cursor:hand\" alt=\"Adicionar Vendedor\">" : "")."</center></td>";
					$cTexto .= "<td Class = \"clase08\" width = \"120\">Nit</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"20\">DV</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"540\">Nombre</td>";
				$cTexto .= "</tr>";
				//Primero Cargo una Matriz con los Clientes
				if ($gCliVen != "") {
					$mMatrizInt = explode("~",$gCliVen);
																						
					$cadena = '';
					$y = 0;
					for ($i=0;$i<count($mMatrizInt);$i++) {
						if ($mMatrizInt[$i] != "") {
							$qCliDat  = "SELECT CLIIDXXX,CLINOMXX,CONCAT(CLIAPE1X,' ',CLIAPE2X,' ',CLINOM1X,' ',CLINOM2X) AS NOMBRE ";
							$qCliDat .= "FROM $cAlfa.SIAI0150 ";
							$qCliDat .= "WHERE ";
							$qCliDat .= "CLIIDXXX = \"{$mMatrizInt[$i]}\" AND ";
							$qCliDat .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
							$xCliDat = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
							if (mysql_num_rows($xCliDat) > 0) {							
								while ($xRCD = mysql_fetch_array($xCliDat)) {
									$y ++;
									if($xRCD['CLINOMXX'] == ''){
										$xRCD['NOMBREXX'] = $xRCD['NOMBRE'];
									}else{
										$xRCD['NOMBREXX'] = $xRCD['CLINOMXX'];
									}
									$cId  = $xRCD['CLIIDXXX'];
									$cadena .= '|'.$cId.'|';
									$zColor = "{$vSysStr['system_row_impar_color_ini']}";
									if($y % 2 == 0) {
										$zColor = "{$vSysStr['system_row_par_color_ini']}";
									}
									$cTexto .= "<tr bgcolor = \"$zColor\" onmouseover=\"javascript:uRowColor(this,\'".$vSysStr['system_row_select_color_ini']."\')\" onmouseout=\"javascript:uRowColor(this,\'$zColor\')\">";
										$cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelCom(\'$cId\')\" style = \"cursor:hand\" alt=\"Borrar Vendedor: ".$mMatrizInt[$i]." - ".substr($xRCD['NOMBRE'],0,60)."\">" : "")."</center></td>";
										$cTexto .= "<td Class = \"clase08\">".substr($xRCD['CLIIDXXX'],0,10)."</td>";
										$cTexto .= "<td Class = \"clase08\">".f_Digito_Verificacion($xRCD['CLIIDXXX'])."</td>";
										$cTexto .= "<td Class = \"clase08\">".substr($xRCD['NOMBREXX'],0,60)."</td>";
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
		case "3":
			$cTexto = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"700\">";
				$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
					$cTexto .= "<td Class = \"clase08\" width = \"20\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:f_Links(\'cContacto\',\'VALID\')\" style = \"cursor:hand\" alt=\"Adicionar Contacto\" >" : "")."</center></td>";
					$cTexto .= "<td Class = \"clase08\" width = \"80\">Nit</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"20\">DV</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"250\">Nombre Contacto</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"80\">Telefono</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"250\">Correo Electronico</td>";
				$cTexto .= "</tr>";
				$mMatrizInt = array();
				$mMatrizInt = explode("~",$gCliCon);
				$cadena = '';
				$y = 0;
				for ($i=0;$i<count($mMatrizInt);$i++) {
					if ($mMatrizInt[$i] != "") {
						$qCliDat  = "SELECT CLIIDXXX,CLINOMXX,CLITELXX,CLIEMAXX,CONCAT(CLIAPE1X,' ',CLIAPE2X,' ',CLINOM1X,' ',CLINOM2X) AS NOMBRE ";
						$qCliDat .= "FROM $cAlfa.SIAI0150 ";
						$qCliDat .= "WHERE ";
						$qCliDat .= "CLIIDXXX = \"{$mMatrizInt[$i]}\" AND ";
						$qCliDat .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
						$xCliDat = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
						if (mysql_num_rows($xCliDat) > 0) {						
							while ($xRCD = mysql_fetch_array($xCliDat)) {
								$y++;
								if($xRCD['CLINOMXX'] == ''){
								 $xRCD['NOMBREXX'] = $xRCD['NOMBRE'];
								}else{
								 $xRCD['NOMBREXX'] = $xRCD['CLINOMXX'];
								}
								$cId  = $xRCD['CLIIDXXX'];
								$cadena .= '|'.$cId.'|';
								$zColor = "{$vSysStr['system_row_impar_color_ini']}";
								if($y % 2 == 0) {
								 $zColor = "{$vSysStr['system_row_par_color_ini']}";
								}
								$cTexto .= "<tr bgcolor = \"$zColor\" onmouseover=\"javascript:uRowColor(this,\'".$vSysStr['system_row_select_color_ini']."\')\" onmouseout=\"javascript:uRowColor(this,\'$zColor\')\">";
									$cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelCon(\'$cId\')\" style = \"cursor:hand\" alt=\"Borrar Contacto: ".$mMatrizInt[$i]." - ".substr($xRCD['NOMBRE'],0,60)."\">" : "")."</center></td>";
									$cTexto .= "<td Class = \"clase08\">".substr($xRCD['CLIIDXXX'],0,10)."</td>";
									$cTexto .= "<td Class = \"clase08\">".f_Digito_Verificacion($xRCD['CLIIDXXX'])."</td>";
									$cTexto .= "<td Class = \"clase08\">".substr($xRCD['NOMBREXX'],0,33)."</td>";
									$cTexto .= "<td Class = \"clase08\">".substr($xRCD['CLITELXX'],0,12)."</td>";
									$cTexto .= "<td Class = \"clase08\">".substr($xRCD['CLIEMAXX'],0,33)."</td>";
								$cTexto .= "</tr>";
							}
						} 
					}
				}
			$cTexto .= "</table>"; ?>  
			<script languaje = "javascript">
			parent.fmwork.document.getElementById('overDivCon').innerHTML = '<?php echo $cTexto ?>';
			</script>
		<?php break;
		case "4": //Cuentas Bancarias
	
			$cTexto  = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"700\">";
			$cTexto .= "<tr>";
			if($_COOKIE['kModo'] == "VER"){
				$cTexto .= "<td width=\"700\" bgcolor=\"#85BAD5\" align=\"Center\"><font size=2> Cuentas Bancarias</font></td>";
			}else{
				$cTexto .= "<td width=\"600\" bgcolor=\"#85BAD5\" align=\"Center\"><font size=2> Cuentas Bancarias</font></td>";
				$cTexto .= "<td width=\"100\" bgcolor=\"#85BAD5\" align=\"right\">&nbsp";
					$cTexto .= "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:f_Links(\'cCuentaBancaria\',\'VALID\')\" style = \"cursor:hand\" title=\"Adicionar Cuenta Bancaria\">";
	
					/***** Botones de Acceso Rapido *****/
					$zSqlPer  = "SELECT sys00005.menopcxx,sys00005.mendesxx,sys00006.modidxxx ";
					$zSqlPer .= "FROM $cAlfa.sys00005,$cAlfa.sys00006 ";
					$zSqlPer .= "WHERE ";
					$zSqlPer .= "sys00006.usridxxx = \"{$kUser}\" AND ";
					$zSqlPer .= "sys00006.modidxxx = sys00005.modidxxx        AND ";
					$zSqlPer .= "sys00006.proidxxx = sys00005.proidxxx        AND ";
					$zSqlPer .= "sys00006.menidxxx = sys00005.menidxxx        AND ";
					$zSqlPer .= "sys00006.modidxxx = \"1000\" AND ";
					$zSqlPer .= "sys00006.proidxxx = \"3620\" ";
					$zSqlPer .= "ORDER BY sys00005.menordxx";
					$zCrsPer  = f_MySql("SELECT","",$zSqlPer,$xConexion01,"");
					while ($zRPer = mysql_fetch_array($zCrsPer)) {
						switch ($zRPer['menopcxx']) {
							case "NUEVO": 
								$cTexto .= "<img src = \"".$cPlesk_Skin_Directory."/btn_global-changes_bg.gif\" onClick =\"javascript:fnNuevaCuenta()\" style = \"cursor:hand\" title=\"Nueva Cuenta Bancaria\">";
							break;
							case "EDITAR": 
								$cTexto .= "<img src = \"".$cPlesk_Skin_Directory."/b_edit.png\" onClick =\"javascript:fEditarCuenta()\" style = \"cursor:hand\" title=\"Editar Cuenta Bancaria\">";
							break;
						}
					}
					$cTexto .= "<img src = \"".$cPlesk_Skin_Directory."/b_drop.png\" onClick =\"javascript:fnEliminarCuenta()\" style = \"cursor:hand\" title=\"Eliminar Cuenta Bancaria\">";
				$cTexto .= "</td>";
			}
			$cTexto .= "</tr>";
			$cTexto .= "</table>";
	
			$cTexto  .= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"700\">";
				$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
					$cTexto .= "<td Class = \"clase08\" width = \"020\" style=\"height:18px;\"><input type=\"checkbox\" name=\"oChkCueAll\" onclick=\"javascript:fnMarcaCueAll()\" ></td>";
					$cTexto .= "<td Class = \"clase08\" width = \"200\" style=\"padding-left:5px; height:18px;\">Banco</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"220\" style=\"padding-left:5px; height:18px;\">Tipo de Cuenta</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"260\" style=\"padding-left:5px; height:18px;\">N&uacute;mero de Cuenta</td>";
				$cTexto .= "</tr>";
				//Primero Cargo una Matriz con las Cuentas Bancarias
				
				$mMatrizInt = explode("~",$gCliCueBa);
				$cadena = '';
				
				$y = 0;
				$nReg = 0;
				for ($i=0;$i<count($mMatrizInt);$i++) {
					if ($mMatrizInt[$i] != "") {
						$qCueBan  = "SELECT * ";
						$qCueBan .= "FROM $cAlfa.fpar0150 ";
						$qCueBan .= "WHERE ";
						$qCueBan .= "banctaxx = \"{$mMatrizInt[$i]}\" AND ";
						$qCueBan .= "cliidxxx = \"{$gTerId}\" AND ";
						$qCueBan .= "regestxx = \"ACTIVO\" LIMIT 0,1";
						$xCueBan = f_MySql("SELECT","",$qCueBan,$xConexion01,"");
						if (mysql_num_rows($xCueBan) > 0) {						
							while ($xRCB = mysql_fetch_array($xCueBan)) {
								$nReg++;
								$y++;
	
								/*** Busco la descripcion del banco. ***/
								$qDesBan  = "SELECT bandesxx ";
								$qDesBan .= "FROM $cAlfa.fpar0124 ";
								$qDesBan .= "WHERE ";
								$qDesBan .= "banidxxx = \"{$xRCB['banidxxx']}\" LIMIT 0,1";
								$xDesBan  = f_MySql("SELECT","",$qDesBan,$xConexion01,"");
								$vDesBan = mysql_fetch_array($xDesBan);
	
								/*** Tipo de Cuenta ***/
								switch ($xRCB['banticta']) {
									case "CTAAHO":
										$xRCB['banticta'] = "CUENTA DE AHORROS";
									break;
									case "CREROT":
										$xRCB['banticta'] = "CREDITO ROTATIVO";
									break;
									case "CTACTE":
									default:
										$xRCB['banticta'] = "CUENTA CORRIENTE";
									break;
								}
	
								$cadena .= '|'.$xRCB['banctaxx'].'|';
								$zColor = "{$vSysStr['system_row_impar_color_ini']}";
								if($y % 2 == 0) {
									$zColor = "{$vSysStr['system_row_par_color_ini']}";
								}
								$cTexto .= "<tr bgcolor = \"$zColor\" onmouseover=\"javascript:uRowColor(this,\'".$vSysStr['system_row_select_color_ini']."\')\" onmouseout=\"javascript:uRowColor(this,\'$zColor\')\">";
									// $cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:fnDelCueBan(\'{$xRCB['banctaxx']}\')\" style = \"cursor:hand\" alt=\"Borrar Cuenta Bancaria: ".$mMatrizInt[$i]."\">" : "")."</center></td>";
									$cTexto .= "<td Class = \"clase08\"><center><input type=\"checkbox\" name=\"oCheckCue\" id=\"".$xRCB['banctaxx']."\"></center></td>";
									$cTexto .= "<td Class = \"clase08\">".$vDesBan['bandesxx']."</td>";
									$cTexto .= "<td Class = \"clase08\">".$xRCB['banticta']."</td>";
									$cTexto .= "<td Class = \"clase08\">".$xRCB['banctaxx']."</td>";
								$cTexto .= "</tr>";
							}
						} 
					}
				}
	
			$cTexto .= "</table>"; ?>  
			<script languaje = "javascript">
				parent.fmwork.document.getElementById('overDivCueBan').innerHTML = '<?php echo $cTexto ?>';
				parent.fmwork.document['frgrm']['vRecdordsCue'].value='<?php echo $nReg ?>';
			</script>
		<?php break;
		case "5": //Responsabilidad Fiscal
			$cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"680\">";
				$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
					$cTexto .= "<td Class = \"clase08\" width = \"20\" ><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:f_Links(\'cResponsabilidadFiscal\',\'VALID\')\" style = \"cursor:hand\" alt=\"Adicionar Responsabilidad Fiscal\" >" : "")."</center></td>";
					$cTexto .= "<td Class = \"clase08\" width = \"160\" style=\"padding-left:5px\">C&oacute;digo</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"420\" style=\"padding-left:5px\">Descripci&oacute;n</td>";
          $cTexto .= "<td Class = \"clase08\" width = \"80\" style=\"padding-left:5px\">Aplica para</td>";
				$cTexto .= "</tr>";
				//Primero Cargo una Matriz con los Clientes
				if ($gCliResFi != "") {
					$mMatrizInt = explode("~",$gCliResFi);
															
					$cadena = '';
					$y = 0;
					for ($i=0;$i<count($mMatrizInt);$i++) {
						if ($mMatrizInt[$i] != "") {
							$qResFis  = "SELECT rfiidxxx, rfidesxx, rfiaplxx ";
							$qResFis .= "FROM $cAlfa.fpar0152 ";
							$qResFis .= "WHERE ";
							$qResFis .= "rfiidxxx = \"{$mMatrizInt[$i]}\" AND ";
							$qResFis .= "regestxx = \"ACTIVO\" LIMIT 0,1";
							$xResFis = f_MySql("SELECT","",$qResFis,$xConexion01,"");
	
							if (mysql_num_rows($xResFis) > 0) {							
								while ($xRRF = mysql_fetch_array($xResFis)) {
									$y ++;
	
									$cId 	= $xRRF['rfiidxxx'];
									$zColor = "{$vSysStr['system_row_impar_color_ini']}";
									if($y % 2 == 0) {
										$zColor = "{$vSysStr['system_row_par_color_ini']}";
									}
									$cTexto .= "<tr bgcolor = \"$zColor\" onmouseover=\"javascript:uRowColor(this,\'".$vSysStr['system_row_select_color_ini']."\')\" onmouseout=\"javascript:uRowColor(this,\'$zColor\')\">";
										$cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelRes(\'$cId\')\" style = \"cursor:hand\" alt=\"Borrar Responsabilidad Fiscal: ".$mMatrizInt[$i]." - ".substr($xRRF['rfidesxx'],0,60)."\">" : "")."</center></td>";
										$cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".substr($xRRF['rfiidxxx'],0,10)."</td>";
										$cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".substr($xRRF['rfidesxx'],0,60)."</td>";
                    $cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".$xRRF['rfiaplxx']."</td>";
									$cTexto .= "</tr>";
								}
							}
						}
					}
				}
			$cTexto .= "</table>"; ?>  
			<script languaje = "javascript">
				parent.fmwork.document.getElementById('overDivResFi').innerHTML = '<?php echo $cTexto ?>';
			</script>
		<?php break;
		case "6": //Tributo
			$cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"680\">";
				$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
					$cTexto .= "<td Class = \"clase08\" width = \"20\" ><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:f_Links(\'cTributo\',\'VALID\')\" style = \"cursor:hand\" alt=\"Adicionar Tributo\" >" : "")."</center></td>";
					$cTexto .= "<td Class = \"clase08\" width = \"160\" style=\"padding-left:5px\">C&oacute;digo</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"420\" style=\"padding-left:5px\">Descripci&oacute;n</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"80\" style=\"padding-left:5px\">Aplica para</td>";
				$cTexto .= "</tr>";
				//Primero Cargo una Matriz con los Clientes
				if ($gCliTri != "") {
					$mMatrizInt = explode("~",$gCliTri);
															
					$cadena = '';
					$y = 0;
					for ($i=0;$i<count($mMatrizInt);$i++) {
						if ($mMatrizInt[$i] != "") {
							$qTributo  = "SELECT triidxxx, tridesxx, triaplxx ";
							$qTributo .= "FROM $cAlfa.fpar0153 ";
							$qTributo .= "WHERE ";
							$qTributo .= "triidxxx = \"{$mMatrizInt[$i]}\" AND ";
							$qTributo .= "regestxx = \"ACTIVO\" LIMIT 0,1";
							$xTributo = f_MySql("SELECT","",$qTributo,$xConexion01,"");
	
							if (mysql_num_rows($xTributo) > 0) {							
								while ($RTR = mysql_fetch_array($xTributo)) {
									$y ++;
	
									$cId 	= $RTR['triidxxx'];
									$zColor = "{$vSysStr['system_row_impar_color_ini']}";
									if($y % 2 == 0) {
										$zColor = "{$vSysStr['system_row_par_color_ini']}";
									}
									$cTexto .= "<tr bgcolor = \"$zColor\" onmouseover=\"javascript:uRowColor(this,\'".$vSysStr['system_row_select_color_ini']."\')\" onmouseout=\"javascript:uRowColor(this,\'$zColor\')\">";
										$cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelTri(\'$cId\')\" style = \"cursor:hand\" alt=\"Borrar Tributo: ".$mMatrizInt[$i]." - ".substr($RTR['tridesxx'],0,60)."\">" : "")."</center></td>";
										$cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".substr($RTR['triidxxx'],0,10)."</td>";
										$cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".substr($RTR['tridesxx'],0,60)."</td>";
										$cTexto .= "<td Class = \"clase08\" style=\"padding-left:5px\">".$RTR['triaplxx']."</td>";
									$cTexto .= "</tr>";
								}
							}
						}
					}
				}
			$cTexto .= "</table>"; ?>  
			<script languaje = "javascript">
				parent.fmwork.document.getElementById('overDivTri').innerHTML = '<?php echo $cTexto ?>';
			</script>
	<?php break;	
		default:
		 //No Hace Nada
		break;
	}
}
?>
	
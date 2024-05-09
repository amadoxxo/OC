<?php
include("../../../../libs/php/utility.php");

switch ($gTipo) {
	case "1": //FACTURAR A
	 $cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"580\">";
	   $cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
		   $cTexto .= "<td Class = \"clase08\" width = \"20\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:f_Links(\'cIntermediario\',\'VALID\');\" style = \"cursor:hand\" alt=\"Adicionar Intermediario\" >" : "" )."</center></td>";
		   $cTexto .= "<td Class = \"clase08\" width = \"100\">Nit</td>";
		   $cTexto .= "<td Class = \"clase08\" width = \"20\">DV</td>";
		   $cTexto .= "<td Class = \"clase08\" width = \"440\">Nombre</td>";
	   $cTexto .= "</tr>";
     /***** Primero Cargo una Matriz con los Clientes *****/
		 if ($gFacA != "") {
		  $mMatrizInt = explode("~",$gFacA);
		 } else {
		  $mMatrizInt = array();
		 }
		 
		 /***** Fin de Explotar el Campo de Conceptos Padres e Hijos *****/
		 /***** Cuando Salgo de Este Proceso Tengo Cargada la Matriz $zMatrizCon con los Conceptos *****/
		 for ($i=0;$i<count($mMatrizInt);$i++) {
		   if ($mMatrizInt[$i] != "") {
			   $qCliDat  = "SELECT CLIIDXXX,CONCAT(CLINOMXX,\" \",CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X) AS NOMBRE ";
				 $qCliDat .= "FROM $cAlfa.SIAI0150 ";
				 $qCliDat .= "WHERE ";
				 $qCliDat .= "CLIIDXXX = \"{$mMatrizInt[$i]}\" AND ";
				 $qCliDat .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
				 $xCliDat = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
				 if (mysql_num_rows($xCliDat) > 0) {
					 $y = 0;
					 while ($xRCD = mysql_fetch_array($xCliDat)) {
						 $y ++;
						 $cId  = $xRCD['CLIIDXXX'];
						 $zColor = "{$vSysStr['system_row_impar_color_ini']}";
						 if($y % 2 == 0) {
						   $zColor = "{$vSysStr['system_row_par_color_ini']}";
						 }
						 $cTexto .= "<tr bgcolor = \"".$zColor."\">";
						   $cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelCom(\'$cId\');\" style = \"cursor:hand\" alt=\"Borrar Intermediario: ".$mMatrizInt[$i]." - ".substr($xRCD['NOMBRE'],0,60)."\" >" : "")."</center></td>";
						   $cTexto .= "<td Class = \"clase08\">".substr($xRCD['CLIIDXXX'],0,10)."</td>";
							 $cTexto .= "<td Class = \"clase08\">".f_Digito_Verificacion($xRCD['CLIIDXXX'])."</td>";
							 $cTexto .= "<td Class = \"clase08\">".substr($xRCD['NOMBRE'],0,60)."</td>";
						 $cTexto .= "</tr>";
					 }
				 } else {
					 $cId  = $mMatrizInt[$i];
					 $zColor = "{$vSysStr['system_row_impar_color_ini']}";
					 if($y % 2 == 0) {
					  $zColor = "{$vSysStr['system_row_par_color_ini']}";
					 }
					 $cTexto .= "<tr bgcolor = \"".$zColor."\">";
					 	 $cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelCom(\'$cId\');\" style = \"cursor:hand\" alt=\"Borrar Intermediario: ".$mMatrizInt[$i]." - ".substr($xRCD['NOMBRE'],0,60)."\" >" : "")."</center></td>";
						 $cTexto .= "<td Class = \"clase08\">".substr($mMatrizInt[$i],0,10)."</td>";
						 $cTexto .= "<td Class = \"clase08\">".f_Digito_Verificacion($mMatrizInt[$i])."</td>";
						 $cTexto .= "<td Class = \"clase08\">".substr("CLIENTE SIN NOMBRE",0,60)."</td>";
					 $cTexto .= "</tr>";
				 }
			 }
		 }
	  $cTexto .= "</table>"; ?>	  
	  <script languaje = "javascript">
	    parent.fmwork.document.getElementById('overDivFacA').innerHTML = '<?php echo $cTexto ?>';
    </script>
  <?php break;
	case "2": //EXCLUSION PAGOS A TERCEROS EN FACTURACION
		$cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"580\">";
			$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
				$cTexto .= "<td Class = \"clase08\" width = \"20\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:f_Links(\'cExclusionPagTer\',\'VALID\');\" style = \"cursor:hand\" alt=\"Adicionar Concepto Contable\" >" : "" )."</center></td>";
				$cTexto .= "<td Class = \"clase08\" width = \"120\">Concepto</td>";
				$cTexto .= "<td Class = \"clase08\" width = \"440\">Descripci&oacute;n</td>";
			$cTexto .= "</tr>";
			/***** Primero Cargo una Matriz con los Conceptos Contables *****/
			if ($gExcPt != "") {
				$mMatrizInt = explode("~",$gExcPt);
			} else {
				$mMatrizInt = array();
			}
			
			/***** Cuando Salgo de Este Proceso Tengo Cargada la Matriz $zMatrizCon con los Conceptos *****/
			for ($i=0;$i<count($mMatrizInt);$i++) {
				if ($mMatrizInt[$i] != "") {
					$qDesCon  = "SELECT * ";
					$qDesCon .= "FROM $cAlfa.fpar0121 ";
					$qDesCon .= "WHERE ";
					$qDesCon .= "ctoidxxx = \"{$mMatrizInt[$i]}\" AND ";
					$qDesCon .= "regestxx = \"ACTIVO\" LIMIT 0,1";
					$xDesCon = f_MySql("SELECT","",$qDesCon,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qDesCon."~".mysql_num_rows($xDesCon));
					
					if (mysql_num_rows($xDesCon) > 0) {
						$y = 0;
						while ($xRDC = mysql_fetch_array($xDesCon)) {
							$y ++;
							$cId  = $xRDC['ctoidxxx'];
							$zColor = "{$vSysStr['system_row_impar_color_ini']}";
							if($y % 2 == 0) {
								$zColor = "{$vSysStr['system_row_par_color_ini']}";
							}
							$cTexto .= "<tr bgcolor = \"".$zColor."\">";
								$cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelConCon(\'$cId\');\" style = \"cursor:hand\" alt=\"Borrar Concepto Contable: ".$mMatrizInt[$i]." - ".$xRDC['ctodesxp']."\" >" : "")."</center></td>";
								$cTexto .= "<td Class = \"clase08\">".$xRDC['ctoidxxx']."</td>";
								$cTexto .= "<td Class = \"clase08\">".$xRDC['ctodesxx']."</td>";
							$cTexto .= "</tr>";
						}
					}else{
						$qDesCca  = "SELECT * ";
						$qDesCca .= "FROM $cAlfa.fpar0119 ";
						$qDesCca .= "WHERE ";
						$qDesCca .= "ctoidxxx = \"{$mMatrizInt[$i]}\" AND ";
						$qDesCca .= "regestxx = \"ACTIVO\" LIMIT 0,1";
						$xDesCca = f_MySql("SELECT","",$qDesCca,$xConexion01,"");	
						// f_Mensaje(__FILE__,__LINE__,$qDesCca."~".mysql_num_rows($xDesCca));
						
						if (mysql_num_rows($xDesCca) > 0) {
							$y = 0;
							while ($xRDCA = mysql_fetch_array($xDesCca)) {
								$y ++;
								$cId  = $xRDCA['ctoidxxx'];
								$zColor = "{$vSysStr['system_row_impar_color_ini']}";
								if($y % 2 == 0) {
									$zColor = "{$vSysStr['system_row_par_color_ini']}";
								}
								$cTexto .= "<tr bgcolor = \"".$zColor."\">";
									$cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelConCon(\'$cId\');\" style = \"cursor:hand\" alt=\"Borrar Concepto Contable: ".$mMatrizInt[$i]." - ".$xRDCA['ctodesxp']."\" >" : "")."</center></td>";
									$cTexto .= "<td Class = \"clase08\">".$xRDCA['ctoidxxx']."</td>";
									$cTexto .= "<td Class = \"clase08\">".$xRDCA['ctodesxp']."</td>";
								$cTexto .= "</tr>";
							}
						}else {
							$cId  = $mMatrizInt[$i];
							$zColor = "{$vSysStr['system_row_impar_color_ini']}";
							if($y % 2 == 0) {
								$zColor = "{$vSysStr['system_row_par_color_ini']}";
							}
							$cTexto .= "<tr bgcolor = \"".$zColor."\">";
								$cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelConCon(\'$cId\');\" style = \"cursor:hand\" alt=\"Borrar Concepto Contable: ".$mMatrizInt[$i]."\" >" : "")."</center></td>";
								$cTexto .= "<td Class = \"clase08\">".substr($mMatrizInt[$i],0,10)."</td>";
								$cTexto .= "<td Class = \"clase08\">".substr("CONCEPTO SIN NOMBRE",0,60)."</td>";
							$cTexto .= "</tr>";
						}
					}
				}
			}
		$cTexto .= "</table>"; ?>	  
	  <script languaje = "javascript">
	    parent.fmwork.document.getElementById('overDivExcPt').innerHTML = '<?php echo $cTexto ?>';
    </script>
  <?php 
  break;
	case "3": //DESCUENTOS
		$cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"580\">";
			$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
				$cTexto .= "<td Class = \"clase08\" width = \"20\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:f_Links(\'cDescuentos\',\'VALID\');\" style = \"cursor:hand\" alt=\"Adicionar Intermediario\" >" : "" )."</center></td>";
				$cTexto .= "<td Class = \"clase08\" width = \"70\">C&oacute;digo</td>";
				$cTexto .= "<td Class = \"clase08\" width = \"50\">ID</td>";
				$cTexto .= "<td Class = \"clase08\" width = \"290\">Servicio</td>";
				$cTexto .= "<td Class = \"clase08\" width = \"70\">Descuento</td>";
				$cTexto .= "<td Class = \"clase08\" width = \"80\">Estado</td>";

			$cTexto .= "</tr>";
			/***** Primero Cargo una Matriz con los Descuentos *****/
			if ($gDescuen != "") {
			 $mMatrizInt = explode("|",$gDescuen);
			} else {
			 $mMatrizInt = array();
			}
			
			/***** Fin de Explotar el Campo de Conceptos Padres e Hijos *****/
			/***** Cuando Salgo de Este Proceso Tengo Cargada la Matriz $zMatrizCon con los Conceptos *****/
			for ($i=0;$i<count($mMatrizInt);$i++) {
				if ($mMatrizInt[$i] != "") {
					$vDesCod = explode("~",$mMatrizInt[$i]);
					$qDescuento  = "SELECT ";
					$qDescuento .= "$cAlfa.fpar0164.desidxxx, ";
					$qDescuento .= "$cAlfa.fpar0164.descodxx, ";
					$qDescuento .= "$cAlfa.fpar0164.seridxxx, ";
					$qDescuento .= "$cAlfa.fpar0164.fcoidxxx, ";
					$qDescuento .= "$cAlfa.fpar0164.desporce, ";
					$qDescuento .= "$cAlfa.fpar0164.regfcrex, ";
					$qDescuento .= "$cAlfa.fpar0164.reghcrex, ";
					$qDescuento .= "$cAlfa.fpar0164.regfmodx, ";
					$qDescuento .= "$cAlfa.fpar0164.reghmodx, ";
					$qDescuento .= "$cAlfa.fpar0164.regestxx, ";
					$qDescuento .= "$cAlfa.fpar0129.serdesxx, ";
					$qDescuento .= "$cAlfa.fpar0129.fcoidxxx AS serfcoid, ";
					$qDescuento .= "$cAlfa.fpar0130.fcodesxx ";
					$qDescuento .= "FROM $cAlfa.fpar0164 ";
					$qDescuento .= "LEFT JOIN $cAlfa.fpar0129 ON $cAlfa.fpar0164.seridxxx = $cAlfa.fpar0129.seridxxx ";
					$qDescuento .= "LEFT JOIN $cAlfa.fpar0130 ON $cAlfa.fpar0164.fcoidxxx = $cAlfa.fpar0130.fcoidxxx ";
					$qDescuento .= "WHERE ";
					$qDescuento .= "$cAlfa.fpar0164.seridxxx = \"{$vDesCod[0]}\" AND ";
					$qDescuento .= "$cAlfa.fpar0164.fcoidxxx = \"{$vDesCod[1]}\" AND ";
					$qDescuento .= "$cAlfa.fpar0164.descodxx = \"{$vDesCod[2]}\" LIMIT 0,1";
					$xDescuento  = f_MySql("SELECT","",$qDescuento,$xConexion01,"");
					if (mysql_num_rows($xDescuento) > 0) {
						$y = 0;
						while ($xRDE = mysql_fetch_array($xDescuento)) {
							$y ++;
							$cId    = $xRDE['seridxxx']."~".$xRDE['fcoidxxx']."~".$xRDE['descodxx'];
							$zColor = "{$vSysStr['system_row_impar_color_ini']}";
							if($y % 2 == 0) {
								$zColor = "{$vSysStr['system_row_par_color_ini']}";
							}
							$cTexto .= "<tr bgcolor = \"".$zColor."\">";
								$cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelDescuento(\'$cId\');\" style = \"cursor:hand\" alt=\"Borrar Descuento con ID: ".$mMatrizInt[$i] ."\">" : "")."</center></td>";
								$cTexto .= "<td Class = \"clase08\">".$xRDE['descodxx']."</td>";
								$cTexto .= "<td Class = \"clase08\">".$xRDE['seridxxx']."</td>";
								$cTexto .= "<td Class = \"clase08\">".$xRDE['serdesxx']."</td>";
								$cTexto .= "<td Class = \"clase08\">".$xRDE['desporce']."</td>";
								$cTexto .= "<td Class = \"clase08\">".$xRDE['regestxx']."</td>";
							$cTexto .= "</tr>";
						}
					} else {
						$cId    = $mMatrizInt[$i];
						$zColor = "{$vSysStr['system_row_impar_color_ini']}";
						if($y % 2 == 0) {
						 $zColor = "{$vSysStr['system_row_par_color_ini']}";
						}
						$cTexto .= "<tr bgcolor = \"".$zColor."\">";
							 $cTexto .= "<td Class = \"clase08\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_remove-selected_bg.gif\" onClick =\"javascript:uDelDescuento(\'$cId\');\" style = \"cursor:hand\" alt=\"Borrar Descuento con ID: ".$mMatrizInt[$i] ."\">" : "")."</center></td>";
							$cTexto .= "<td Class = \"clase08\">".$mMatrizInt[$i]."</td>";
							$cTexto .= "<td Class = \"clase08\"></td>";
							$cTexto .= "<td Class = \"clase08\"></td>";
							$cTexto .= "<td Class = \"clase08\"></td>";
						$cTexto .= "</tr>";
					}
				}
			}
		 $cTexto .= "</table>"; ?>	  
			<script languaje = "javascript">
				parent.fmwork.document.getElementById('overDescuentos').innerHTML = '<?php echo $cTexto ?>';
			</script>
	 <?php break;
		case "4":
			$cTexto  = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"580\">";
				$cTexto .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\">";
					$cTexto .= "<td Class = \"clase08\" width = \"20\"><center>".(($_COOKIE['kModo'] != "VER") ? "<img src = \"".$cPlesk_Skin_Directory."/btn_create-dir_bg.gif\" onClick =\"javascript:f_Links(\'cExclusionPagTer\',\'VALID\');\" style = \"cursor:hand\" alt=\"Adicionar Concepto Contable\" >" : "" )."</center></td>";
					$cTexto .= "<td Class = \"clase08\" width = \"120\">Concepto</td>";
					$cTexto .= "<td Class = \"clase08\" width = \"440\">Descripci&oacute;n</td>";
				$cTexto .= "</tr>";
			$cTexto .= "</table>"; ?>
			<script>
				parent.fmwork.document.getElementById('overDivValUniTer').innerHTML = '<?php echo $cTexto ?>';
			</script>
		<?php break;
  default:
	 //No Hace Nada
	break;
}
?>
	
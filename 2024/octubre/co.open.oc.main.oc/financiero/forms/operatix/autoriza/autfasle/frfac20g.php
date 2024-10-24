<?php // Hola Mundo ...
  namespace openComex;
	ini_set("memory_limit","512M");
	set_time_limit(0);
 
	 //ini_set('error_reporting', E_ERROR);
	 //ini_set("display_errors","1");
	
include("../../../../libs/php/utility.php"); 
	
switch ($_POST['cModo']) {
	case "VALID":
	case "WINDOW":
		//Adicionar DO's
		if ($_POST['cTramites'] != "") {
			$nSecuencia = ($_POST['nSecuencia'] == "") ? "001" : $_POST['nSecuencia'];
			
			//Trayendo la secuencia maxima para ese id de transaccion
			
			$mTramites = f_Explode_Array($_POST['cTramites'],"|","~");
			for ($i=0; $i<count($mTramites); $i++) {
				if ($mTramites[$i][0] != "") {
					/**
					 * Columnas del vector de tramites
					 * cDosNro_DOS -> [0]
  				 * cDosFec_DOS -> [1]
  				 * cDosPed_DOS -> [2]
					 * cDosTip_DOS -> [3]
					 * cDosMtr_DOS -> [4]
					 * nDosVlr_DOS -> [5]
					 * cDosFor_DOS -> [6]
					 * cDosRec_DOS -> [7]
					 * cDosCE_DOS  -> [8]
					 * cDosId_DOS  -> [9]
					 * cDosCod_DOS -> [10]
					 * cDosSuf_DOS -> [11]
					 * nDosCla_DOS -> [12]
					 * cPucId_DOS  -> [13]
					 * cPucDet_DOS -> [14]
					 * cSucId_DOS  -> [15]
					 * cCcoId_DOS  -> [16]
					 * cDirId_DOS  -> [17]
					 * cVenId_DOS  -> [18]
					 * cSucCom_DOS -> [19]
					 * cDosTex_DOS -> [20]
					 * cTerId_DOS  -> [21]
					 * cTerNom_DOS -> [22]
					 * cSccId_DOS  -> [23]
					 * cColor      -> [24]
					 */
					##Fin de Buscando los Do##
					$qTramites  = "SELECT * ";
					$qTramites .= "FROM $cAlfa.sys00121 ";
					$qTramites .= "WHERE ";
					$qTramites .= "sucidxxx = \"{$mTramites[$i][0]}\" AND ";
					$qTramites .= "docidxxx = \"{$mTramites[$i][1]}\" AND ";
					$qTramites .= "docsufxx = \"{$mTramites[$i][2]}\" AND ";
					$qTramites .= "regestxx = \"ACTIVO\" ";
					//f_Mensaje(__FILE__,__LINE__,$qTramites);
					$xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
					
					while ($vTramites = mysql_fetch_array($xTramites)) {
						$i = count($mTramites);
						//$mTramites[$i] = $vTramites;
						$nIncluir = 1; //Variable que indica que el tramite fue encontrado en el modulo de aduana con las condiciones necesaria para que se muestre
						if ( $vTramites['docdosre'] != "" && $vTramites['doctipxx'] == "REGISTRO") {
							
							$mAuxDo = explode("~", $vTramites['docdosre']);
							for ($nA=0; $nA<count($mAuxDo);$nA++) {
								if ($mAuxDo[$nA] != "") {
									//Verifico que si es un DO de Registro no este asociado a un DO de Importacion
									$qDoReg  = "SELECT $cAlfa.sys00121.*, ";
									$qDoReg .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) AS clinomxx ";
									$qDoReg .= "FROM $cAlfa.sys00121 ";
									$qDoReg .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
									$qDoReg .= "WHERE ";
									$qDoReg .= "$cAlfa.sys00121.doctipxx != \"REGISTRO\" AND ";
									$qDoReg .= "$cAlfa.sys00121.docidxxx LIKE \"%{$mAuxDo[$nA]}%\" AND ";
									$qDoReg .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" ";
									$xDoReg  = f_MySql("SELECT","",$qDoReg,$xConexion01,"");
									//f_Mensaje(__FILE__,__LINE__,$qDoReg." ~ ".mysql_num_rows($xDoReg));
									$nEncontro = 0; $cDoAso = "";
									while ($xRDR = mysql_fetch_array($xDoReg)) {
										$i = count($xDoReg);
										$doAsociado = array(0 => $xRDR['sucidxxx'], 
																					1 => $xRDR['docidxxx'],
																					2 => $xRDR['docsufxx'],
																					3 => $xRDR['doctipxx'],
																					4 => $xRDR['cliidxxx'],
																					5 => f_Digito_Verificacion($xRR['cliidxxx']),
																					6 => $xRDR['clinomxx'] );
										$mTramites[$i] = $doAsociado;
									}
								}
							}
						}
						//f_Mensaje(__FILE__,__LINE__,'llego '.$vTramites['docdosre'].' '.$vTramites['doctipxx']);
						//Buscando los DO de registro asociados  al DO
						if ($vTramites['docdosre'] != "" && $vTramites['doctipxx'] != "REGISTRO") {
							$mAuxDo = explode("~", $vTramites['docdosre']);
							for ($nA=0; $nA<count($mAuxDo);$nA++) {
								if ($mAuxDo[$nA] != "") {
									if (in_array("{$mAuxDo[$nA]}", $vTramites) == false) {
										$vTramites[count($vTramites)] = "{$mAuxDo[$nA]}";
										//Busco el DO de Registro
										//Busco el tramite en la sys00121 de modulo de facturacion
										$qRegistro  = "SELECT $cAlfa.sys00121.*, ";
										$qRegistro .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) AS clinomxx ";
										$qRegistro .= "FROM $cAlfa.sys00121 ";
										$qRegistro .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
										$qRegistro .= "WHERE ";
										$qRegistro .= "$cAlfa.sys00121.doctipxx = \"REGISTRO\"       AND ";
										$qRegistro .= "$cAlfa.sys00121.docidxxx = \"{$mAuxDo[$nA]}\" AND ";
										$qRegistro .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" ";
										$xRegistro  = f_MySql("SELECT","",$qRegistro,$xConexion01,"");
										//f_Mensaje(__FILE__,__LINE__,$qRegistro." ~ ".mysql_num_rows($xRegistro));
										
										while ($xRR = mysql_fetch_array($xRegistro)) {
											$i = count($mTramites);
											$doAsociado = array(0 => $xRR['sucidxxx'], 
																					1 => $xRR['docidxxx'],
																					2 => $xRR['docsufxx'],
																					3 => $xRR['doctipxx'],
																					4 => $xRR['cliidxxx'],
																					5 => f_Digito_Verificacion($xRR['cliidxxx']),
																					6 => $xRR['clinomxx']);
																				
											$mTramites[$i] = $doAsociado;
										} ## while ($xRR = mysql_fetch_array($xRegistro)) { ##
									} ## if (in_array("{$mAux01[0]}~{$mAux01[1]}", $vTramites) == false) { ##
								} ## if ($mAuxDo[$nA] != "") { ##
							} ## for ($nA=0; $nA<count($mAuxDo);$nA++) { ##
						} ## if ($vTramites['docdosre'] != "") { ##
					} ## while ($vTramites = mysql_fetch_array($xTramites)) { ##
				}
			}?>
			<script languaje = "javascript">
				var variable = parent.fmwork;
				if (typeof variable === 'undefined') {
					var tabla = parent.window.opener.document.getElementById("Grid_Do");
					var nIndex =  tabla.rows.length;
				} else {
					var tabla = parent.fmwork.document.getElementById("Grid_Do");
					var nIndex =  tabla.rows.length;
				}
			</script>
		<?php	for ($i=0; $i<count($mTramites); $i++) {
							
					$cColor = (($mTramites[$i][3] == "REGISTRO") ? "red" : "");
					$nAdiciono = 1;
					if ($nAdiciono == 1) { ?>
						<script languaje = "javascript">
								// valido si el DO existe en la tabla. si existe lo ignoro.
							var cExisteDo = false;
							for (var j = 0, row; row = tabla.rows[j]; j++) {
								if ( row.cells[0].getElementsByTagName('input')[0].value == "<?php echo $mTramites[$i][0] ?>"  && 
										 row.cells[1].getElementsByTagName('input')[0].value == "<?php echo $mTramites[$i][1] ?>"  &&
										 row.cells[2].getElementsByTagName('input')[0].value == "<?php echo $mTramites[$i][2] ?>" ) {
										cExisteDo = true;
								}
							}
							
							if ("<?php echo $cColor ?>" == "red") {
								var cBgColor = "#FF0000";
								var cColor   = "#FFFFFF";
							} else {
								var cBgColor = "#FFFFFF";
								var cColor   = "#000000";
							}
							
							if ( !cExisteDo ) {
							
								if ("<?php echo str_pad($nSecuencia,3,"0",STR_PAD_LEFT) ?>" != "<?php echo str_pad($_POST['nSecuencia'],3,"0",STR_PAD_LEFT) ?>") {
									//Se debe crear la secuencia
									if ("<?php echo $_POST['cModo'] ?>" == "VALID") {
										parent.fmwork.f_Add_New_Row_Do();
									} else {
										parent.window.opener.f_Add_New_Row_Do();
									}
								}
								
								
								if ("<?php echo $_POST['cModo'] ?>" == "VALID") {
									parent.fmwork.document.forms['frgrm']['cSucId'+nIndex].value     = '<?php echo $mTramites[$i][0] ?>';
									parent.fmwork.document.forms['frgrm']['cDocId'+nIndex].value     = '<?php echo $mTramites[$i][1]  ?>';
									parent.fmwork.document.forms['frgrm']['cDocSuf'+nIndex].value     = '<?php echo $mTramites[$i][2]  ?>';
									parent.fmwork.document.forms['frgrm']['cDocTip'+nIndex].value     = '<?php echo $mTramites[$i][3]  ?>';
									parent.fmwork.document.forms['frgrm']['cCliId'+nIndex].value     = '<?php echo $mTramites[$i][4]  ?>';
									parent.fmwork.document.forms['frgrm']['cCliDv'+nIndex].value     = '<?php echo $mTramites[$i][5]  ?>';
									parent.fmwork.document.forms['frgrm']['cCliNom'+nIndex].value     = '<?php echo $mTramites[$i][6]  ?>';
									
									parent.fmwork.document.forms['frgrm']['cSucId'+nIndex].style.backgroundColor = cBgColor;
									parent.fmwork.document.forms['frgrm']['cDocId'+nIndex].style.backgroundColor = cBgColor;
									parent.fmwork.document.forms['frgrm']['cDocSuf'+nIndex].style.backgroundColor = cBgColor;
									parent.fmwork.document.forms['frgrm']['cDocTip'+nIndex].style.backgroundColor = cBgColor;
									parent.fmwork.document.forms['frgrm']['cCliId'+nIndex].style.backgroundColor = cBgColor;
									parent.fmwork.document.forms['frgrm']['cCliDv'+nIndex].style.backgroundColor = cBgColor;
									parent.fmwork.document.forms['frgrm']['cCliNom'+nIndex].style.backgroundColor = cBgColor;
									
								} else {
									parent.window.opener.document.forms['frgrm']['cSucId'+nIndex].value     = '<?php echo $mTramites[$i][0] ?>';
									parent.window.opener.document.forms['frgrm']['cDocId'+nIndex].value     = '<?php echo $mTramites[$i][1]  ?>';
									parent.window.opener.document.forms['frgrm']['cDocSuf'+nIndex].value     = '<?php echo $mTramites[$i][2]  ?>';
									parent.window.opener.document.forms['frgrm']['cDocTip'+nIndex].value     = '<?php echo $mTramites[$i][3]  ?>';
									parent.window.opener.document.forms['frgrm']['cCliId'+nIndex].value     = '<?php echo $mTramites[$i][4]  ?>';
									parent.window.opener.document.forms['frgrm']['cCliDv'+nIndex].value     = '<?php echo $mTramites[$i][5]  ?>';
									parent.window.opener.document.forms['frgrm']['cCliNom'+nIndex].value     = '<?php echo $mTramites[$i][6]  ?>';
									
									parent.window.opener.document.forms['frgrm']['cSucId'+nIndex].style.backgroundColor = cBgColor;
									parent.window.opener.document.forms['frgrm']['cDocId'+nIndex].style.backgroundColor = cBgColor;
									parent.window.opener.document.forms['frgrm']['cDocSuf'+nIndex].style.backgroundColor = cBgColor;
									parent.window.opener.document.forms['frgrm']['cDocTip'+nIndex].style.backgroundColor = cBgColor;
									parent.window.opener.document.forms['frgrm']['cCliId'+nIndex].style.backgroundColor = cBgColor;
									parent.window.opener.document.forms['frgrm']['cCliDv'+nIndex].style.backgroundColor = cBgColor;
									parent.window.opener.document.forms['frgrm']['cCliNom'+nIndex].style.backgroundColor = cBgColor;
								
								}
								nIndex++;
							}
						</script>
						<?php $nSecuencia++;
					}
				}
			//}
		} ?>
		<script languaje = "javascript">
			//cerrar la ventana
			if ("<?php echo $_POST['cModo'] ?>" == "WINDOW") {
				parent.window.close();	
			}
		</script>
	<?php break;
	case "PEGARDO";
	
		$nSecuencia = ($_POST['nSecuencia'] == "") ? "001" : $_POST['nSecuencia'];
		
		if ( $_POST['nValueSec'] != "" ) {
			$nSecuencia++;
		}
		
		if ($_POST['cMemo'] == "") {
			f_Mensaje(__FILE__,__LINE__,"Debe Pegar los Numero de Do, Verifique.");
		} else {
			
			$nCanDo = 0;
				
			$cBuscar = array(","," ",chr(13),chr(10),chr(27),chr(9));
  		$cReempl = array("~","~","~","~","~","~");
			
			$_POST['cMemo'] = str_replace($cBuscar,$cReempl,$_POST['cMemo']);
			$vTramAxu = explode("~", $_POST['cMemo']);
			
			$vTramPeg = array();
			for ($nT=0; $nT<count($vTramAxu); $nT++) {
				if ($vTramAxu[$nT] != "") {
					if (in_array($vTramAxu[$nT], $vTramPeg) == false) {
						$vTramPeg[count($vTramPeg)] = $vTramAxu[$nT];
					}
				}
			}
			
			$cTramites = ""; $mTramites = array();
			
			for ($nT=0; $nT<count($vTramPeg); $nT++) {
				if ($vTramPeg[$nT] != "") {
					##Fin de Buscando los Do##
					$qTramites  = "SELECT * ";
					$qTramites .= "FROM $cAlfa.sys00121 ";
					$qTramites .= "WHERE ";
				  $qTramites .= "docidxxx = \"{$vTramPeg[$nT]}\" ";
				  //f_Mensaje(__FILE__,__LINE__,$qTramites);
				
					$xTramites  = f_MySql("SELECT","",$qTramites,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qTramites." ~ ".mysql_num_rows($xTramites));
					
					while ($vTramites = mysql_fetch_array($xTramites)) {
						$i = count($mTramites);
						$mTramites[$i] = $vTramites;
						
							$nIncluir = 1; //Variable que indica que el tramite fue encontrado en el modulo de aduana con las condiciones necesaria para que se muestre
						if ($vTramites['doctipxx'] == "REGISTRO") {
							
							$mAuxDo = explode("~", $vTramites['docdosre']);
							for ($nA=0; $nA<count($mAuxDo);$nA++) {
								if ($mAuxDo[$nA] != "") {
									//Verifico que si es un DO de Registro no este asociado a un DO de Importacion
									$qDoReg  = "SELECT *";
									$qDoReg .= "FROM $cAlfa.sys00121 ";
									$qDoReg .= "WHERE ";
									$qDoReg .= "doctipxx != \"REGISTRO\" AND ";
									$qDoReg .= "docidxxx LIKE \"%{$mAuxDo[$nA]}%\" AND ";
									$qDoReg .= "regestxx = \"ACTIVO\" ";
									$xDoReg  = f_MySql("SELECT","",$qDoReg,$xConexion01,"");
									//f_Mensaje(__FILE__,__LINE__,$qDoReg." ~ ".mysql_num_rows($xDoReg));
									$nEncontro = 0; $cDoAso = "";
									while ($xRDR = mysql_fetch_array($xDoReg)) {
										$i = count($xDoReg);
										$mTramites[$i] = $xRDR;
									}
								}
							}
						}
						
						//Buscando los DO de registro asociados  al DO
						if ($vTramites['docdosre'] != "" && $vTramites['doctipxx'] != "REGISTRO") {
							$mAuxDo = explode("~", $vTramites['docdosre']);
							for ($nA=0; $nA<count($mAuxDo);$nA++) {
								if ($mAuxDo[$nA] != "") {
									if (in_array("{$mAuxDo[$nA]}", $vTramites) == false) {
										$vTramites[count($vTramites)] = "{$mAuxDo[$nA]}";
										//Busco el DO de Registro
										//Busco el tramite en la sys00121 de modulo de facturacion
										$qRegistro  = "SELECT * ";
										$qRegistro .= "FROM $cAlfa.sys00121 ";
										$qRegistro .= "WHERE ";
										$qRegistro .= "doctipxx = \"REGISTRO\"       AND ";
										$qRegistro .= "docidxxx = \"{$mAuxDo[$nA]}\" AND ";
										$qRegistro .= "regestxx = \"ACTIVO\" ";
										$xRegistro  = f_MySql("SELECT","",$qRegistro,$xConexion01,"");
										//f_Mensaje(__FILE__,__LINE__,$qRegistro." ~ aaaa ".mysql_num_rows($xRegistro));
										
										while ($xRR = mysql_fetch_array($xRegistro)) {
											$i = count($mTramites);
											$mTramites[$i] = $xRR;
										} ## while ($xRR = mysql_fetch_array($xRegistro)) { ##
									} ## if (in_array("{$mAux01[0]}~{$mAux01[1]}", $vTramites) == false) { ##
								} ## if ($mAuxDo[$nA] != "") { ##
							} ## for ($nA=0; $nA<count($mAuxDo);$nA++) { ##
						} ## if ($vTramites['docdosre'] != "") { ##
					} ## while ($vTramites = mysql_fetch_array($xTramites)) { ##
					##Fin de Buscando los Do##
				}	
			}
			?>
			<script languaje = "javascript">
				var variable = parent.fmwork;
				if (typeof variable === 'undefined') {
					var tabla = parent.window.opener.document.getElementById("Grid_Do");
					var nIndex =  tabla.rows.length;
				} else {
					var tabla = parent.fmwork.document.getElementById("Grid_Do");
					var nIndex =  tabla.rows.length;
				}
			</script>
		<?php	
			$vTramites = array();
			for ($i=0; $i<count($mTramites); $i++) {
			  //Busco la el nombre del cliente
        $qSqlCli  = "SELECT ";
        $qSqlCli .= "$cAlfa.SIAI0150.*, ";
        $qSqlCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
        $qSqlCli .= "FROM $cAlfa.SIAI0150 ";
        $qSqlCli .= "WHERE ";
        $qSqlCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$mTramites[$i]['cliidxxx']}\" LIMIT 0,1";
        $xSqlCli  = f_MySql("SELECT","",$qSqlCli,$xConexion01,"");
        if(mysql_num_rows($xSqlCli) > 0) {
          $zRCli = mysql_fetch_array($xSqlCli);
          $mTramites[$i]['clinomxx'] = $zRCli['CLINOMXX'];
					$mTramites[$i]['cliidxx'] = $zRCli['CLIIDXXX'];
        } else {
          $mTramites[$i]['clinomxx'] = "CLIENTE SIN NOMBRE";
					$mTramites[$i]['cliidxx'] = $zRCli['CLIIDXXX'];
        }
				
				$cColor = (($mTramites[$i]['doctipxx'] == "REGISTRO") ? "red" : "");
				
				$nAdiciono = 1;
				if ($nAdiciono == 1) {
					$nCanDo++; 
					?>
					<script languaje = "javascript">
						var variable = parent.fmwork;
						if (typeof variable === 'undefined') {
							var tabla = parent.window.opener.document.getElementById("Grid_Do");
						} else {
							var tabla = parent.fmwork.document.getElementById("Grid_Do");
						}
						
						// valido si el DO existe en la tabla. si existe lo ignoro.
						var cExisteDo = false;
						for (var j = 0, row; row = tabla.rows[j]; j++) {
							if ( row.cells[0].getElementsByTagName('input')[0].value == "<?php echo $mTramites[$i]['sucidxxx'] ?>"  && 
									 row.cells[1].getElementsByTagName('input')[0].value == "<?php echo $mTramites[$i]['docidxxx'] ?>"  &&
									 row.cells[2].getElementsByTagName('input')[0].value == "<?php echo $mTramites[$i]['docsufxx'] ?>" ) {
									cExisteDo = true;
							}
						}
						
						if ("<?php echo $cColor ?>" == "red") {
							var cBgColor = "#FF0000";
							var cColor   = "#FFFFFF";
						} else {
							var cBgColor = "#FFFFFF";
							var cColor   = "#000000";
						}
						if ( !cExisteDo ) {
							if ("<?php echo str_pad($nSecuencia,3,"0",STR_PAD_LEFT) ?>" != "<?php echo str_pad($_POST['nSecuencia'],3,"0",STR_PAD_LEFT) ?>") {
								parent.window.opener.f_Add_New_Row_Do();	
							} else {
								if ( "<?php echo $_POST['nValueSec'] ?>" != "" ){
									parent.window.opener.f_Add_New_Row_Do();
								}
								
							}
							
							var nSecuencia = parent.window.opener.document.forms['frgrm']['nSecuencia_Dos'].value;
							parent.window.opener.document.forms['frgrm']['cSucId'+nIndex].value     = '<?php echo $mTramites[$i]['sucidxxx'] ?>';
							parent.window.opener.document.forms['frgrm']['cDocId'+nIndex].value     = '<?php echo $mTramites[$i]['docidxxx']  ?>';
							parent.window.opener.document.forms['frgrm']['cDocSuf'+nIndex].value     = '<?php echo $mTramites[$i]['docsufxx']  ?>';
							parent.window.opener.document.forms['frgrm']['cDocTip'+nIndex].value     = '<?php echo $mTramites[$i]['doctipxx']  ?>';
							parent.window.opener.document.forms['frgrm']['cCliId'+nIndex].value     = '<?php echo $mTramites[$i]['cliidxxx']  ?>';
							parent.window.opener.document.forms['frgrm']['cCliDv'+nIndex].value     = '<?php echo f_Digito_Verificacion($mTramites[$i]['cliidxxx'])  ?>';
							parent.window.opener.document.forms['frgrm']['cCliNom'+nIndex].value     = '<?php echo $mTramites[$i]['clinomxx']  ?>';
							
							parent.window.opener.document.forms['frgrm']['cSucId'+nIndex].style.backgroundColor = cBgColor;
							parent.window.opener.document.forms['frgrm']['cDocId'+nIndex].style.backgroundColor = cBgColor;
							parent.window.opener.document.forms['frgrm']['cDocSuf'+nIndex].style.backgroundColor = cBgColor;
							parent.window.opener.document.forms['frgrm']['cDocTip'+nIndex].style.backgroundColor = cBgColor;
							parent.window.opener.document.forms['frgrm']['cCliId'+nIndex].style.backgroundColor = cBgColor;
							parent.window.opener.document.forms['frgrm']['cCliDv'+nIndex].style.backgroundColor = cBgColor;
							parent.window.opener.document.forms['frgrm']['cCliNom'+nIndex].style.backgroundColor = cBgColor;
							
							nIndex++;
						}
					</script>
						<?php $nSecuencia++;
				}
			}

			if ($nCanDo == 0) {
				f_Mensaje(__FILE__,__LINE__,"No se encontraron registros.");
			}
		} ?>
		<script languaje = "javascript">
			//cerrar la ventana
			parent.window.close();	
		</script>
	<?php break;
	default:
		//No hace nada	
	break;
}
	
	?>
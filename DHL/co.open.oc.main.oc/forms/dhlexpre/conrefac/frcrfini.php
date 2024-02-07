<?php
	/**
	 * Tracking Conceptos Reporte Facturacion DHL
	 * --- Descripcion: Parametrica para Creacion/Edicion Conceptos Reporte Facturacion DHL
	 * @author Elian Amado Ramirez <elian.amado@openits.co>
	 * @version 001
	 */

	include("../../../libs/php/utility.php");

	/**
	 *  Cookie fija
	 */
	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb   = $kDf[3];
	$kUser      = $kDf[4];
	$kLicencia  = $kDf[5];
	$swidth     = $kDf[6];

	/* Busco en la 05 que Tiene Permiso el Usuario*/
	$qUsrMen  = "SELECT * ";
	$qUsrMen .= "FROM $cAlfa.sys00013 ";
	$qUsrMen .= "WHERE ";
	$qUsrMen .= "sys00013.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
	$qUsrMen .= "sys00013.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
	$qUsrMen .= "sys00013.menimgon <> '' ";
	$qUsrMen .= "ORDER BY sys00013.menordxx";
	$xUsrMen  = f_MySql("SELECT","",$qUsrMen,$xConexion01,"");
	// echo $qUsrMen."~".mysql_num_rows($xUsrMen);
?>
<html>
	<head>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/date_picker.js'></script>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/utility.js'></script>
		<script language="javascript">

			function fnVer(xDesId) {
				var ruta = "frcrfnue.php?gDesId="+xDesId;
				document.cookie="kIniAnt=dhlexpre/conrefac/<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
				document.cookie="kMenDes=Ver Descuento;path="+"/";
				document.cookie="kModo=VER;path="+"/";
				parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_New ?>/nivel4.php";
				document.location = ruta; // Invoco el menu.
			}

			function fnEditar(xModo) {
				switch (document.forms['frnav']['vRecords'].value) {
					case "1":
						if (document.forms['frnav']['oCheck'].checked == true) {
							var zMatriz = document.forms['frnav']['oCheck'].id.split('~');
							var ruta = "frcrfnue.php?gDesId="+zMatriz[0];
							document.cookie="kIniAnt=dhlexpre/conrefac/<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
							document.cookie="kMenDes=Editar Descuento;path="+"/";
							document.cookie="kModo="+xModo+";path="+"/";
							parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_New ?>/nivel4.php";
							document.location = ruta; // Invoco el menu.
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frnav']['oCheck'].length;i++) {
							if (document.forms['frnav']['oCheck'][i].checked == true && zSw_Prv == 0) {
								// Solo Deja Legalizar el Primero Seleccionado
								zSw_Prv = 1;
								var zMatriz = document.forms['frnav']['oCheck'][i].id.split('~');
								var ruta = "frcrfnue.php?gDesId="+zMatriz[0];
								document.cookie="kIniAnt=dhlexpre/conrefac/<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
								document.cookie="kMenDes=Editar Descuento;path="+"/";
								document.cookie="kModo="+xModo+";path="+"/";
								parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_New ?>/nivel4.php";
								document.location = ruta; // Invoco el menu.
							}
						}
					break;
				}
			}

			function fnBorrar(xModo) {
				switch (document.forms['frnav']['vRecords'].value) {
					case "1":
						if (document.forms['frnav']['oCheck'].checked == true) {
							var zMatriz  = document.forms['frnav']['oCheck'].id.split("~");
							var xMensaje = "Esta Seguro de BORRAR el Descuento con Id ["+zMatriz[0]+"].";
							if (confirm(xMensaje)) {
								document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
								document.cookie="kModo="+xModo+";path="+"/";
								document.forms['frestado']['cDesId'].value  = zMatriz[0];
								document.forms['frestado'].submit();
							}
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frnav']['oCheck'].length;i++) {
							if (document.forms['frnav']['oCheck'][i].checked == true && zSw_Prv == 0) {
								// Solo Deja Borrar el Primero Seleccionado
								zSw_Prv = 1;
								var zMatriz  = document.forms['frnav']['oCheck'][i].id.split("~");
								var xMensaje = "Esta Seguro de BORRAR el Descuento Id ["+zMatriz[0]+"].";
								if (confirm(xMensaje)) {
									document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
									document.cookie="kModo="+xModo+";path="+"/";
									document.forms['frestado']['cDesId'].value  = zMatriz[0];
									document.forms['frestado'].submit();
								}
							}
						}
					break;
				}
			}

			function fnLink(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
				document.cookie="kIniAnt=dhlexpre/conrefac/<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
				document.cookie="kMenDes="+xMenDes+";path="+"/";
				document.cookie="kModo="+xOpcion+";path="+"/";
				parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_New ?>/nivel4.php";
				document.location = xForm; // Invoco el menu.
			}

			function fnMarca() {
				if (document.forms['frnav']['oCheckAll'].checked == true){
					if (document.forms['frnav']['vRecords'].value == 1){
						document.forms['frnav']['oCheck'].checked=true;
					} else {
						if (document.forms['frnav']['vRecords'].value > 1){
							for (i=0;i<document.forms['frnav']['oCheck'].length;i++){
								document.forms['frnav']['oCheck'][i].checked = true;
							}
						}
					}
				} else {
					if (document.forms['frnav']['vRecords'].value == 1){
						document.forms['frnav']['oCheck'].checked=false;
					} else {
						if (document.forms['frnav']['vRecords'].value > 1){
							for (i=0;i<document.forms['frnav']['oCheck'].length;i++){
								document.forms['frnav']['oCheck'][i].checked = false;
							}
						}
					}
				}
			}

			/************************ FUNCION PARA GUARDAR EL ORDEN DEL ORDER BY DEL SQL ***********************/
			function fnOrderBy(xEvento,xCampo) {
				//alert(document.forms['frnav'][xCampo].value);
				if (document.forms['frnav'][xCampo].value != '') {
					var vSwitch = document.forms['frnav'][xCampo].value.split(' ');
					var cSwitch = vSwitch[1];
				} else {
					var cSwitch = '';
				}

				if (xEvento == 'onclick') {
					switch (cSwitch) {
						case '':
							document.forms['frnav'][xCampo].value = document.forms['frnav'][xCampo].id+' ASC,';
							document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_New ?>/s_asc.png';
							if (document.forms['frnav']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
								document.forms['frnav']['cOrderByOrder'].value += xCampo+"~";
								document.forms['frnav'].submit();
							} else {
								document.forms['frnav'].submit();
							}
						break;
						case 'ASC,':
							document.forms['frnav'][xCampo].value = document.forms['frnav'][xCampo].id+' DESC,';
							document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_New ?>/s_desc.png';
							if (document.forms['frnav']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
								document.forms['frnav']['cOrderByOrder'].value += xCampo+"~";
								document.forms['frnav'].submit();
							} else {
								document.forms['frnav'].submit();
							}
						break;
						case 'DESC,':
							document.forms['frnav'][xCampo].value = '';
							document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png';
							if (document.forms['frnav']['cOrderByOrder'].value.lastIndexOf(xCampo) >= 0) {
								document.forms['frnav']['cOrderByOrder'].value = document.forms['frnav']['cOrderByOrder'].value.replace(xCampo,"");
								document.forms['frnav'].submit();
							} else {
								document.forms['frnav'].submit();
							}
						break;
					}
				} else {
					switch (cSwitch) {
						case '':
							document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png';
						break;
						case 'ASC,':
							document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_New ?>/s_asc.png';
						break;
						case 'DESC,':
							document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory_New ?>/s_desc.png';
						break;
					}
				}
			}

		</script>
	</head>
	<body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
		<form name = "frestado" action = "frcrfgra.php" method = "post" target="fmpro">
			<input type = "hidden" name = "cDesId"  value = "">
			<input type = "hidden" name = "cRegEst" value = "">
		</form>

		<form name = "frnav" action="frcrfini.php" method="post" target="fmwork">
			<input type = "hidden" name = "vRecords"   		value = "">
			<input type = "hidden" name = "vLimInf"    		value = "<?php echo $vLimInf ?>">
			<input type = "hidden" name = "vSortField" 		value = "<?php echo $vSortField ?>">
			<input type = "hidden" name = "vSortType"  		value = "<?php echo $vSortType ?>">
			<input type = "hidden" name = "vBuscar"    	  value = "<?php echo $_POST['vBuscar'] ?>">
			<input type = "hidden" name = "cOrderByOrder" value = "<?php echo $_POST['cOrderByOrder'] ?>" style = "width:1000">

			<!-- Inicia Nivel de Procesos -->
			<?php if (mysql_num_rows($xUsrMen) > 0) { ?>
				<center>
					<table width="95%" cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td>
								<fieldset>
									<legend>Procesos <?php echo $_COOKIE['kProDes'] ?></legend>
									<center>
										<table cellspacing="0" width="100%">
											<?php
												$y = 0;
												/* Empiezo a Leer la sys00013 */
												while($mUsrMen = mysql_fetch_array($xUsrMen)) {
													if($y == 0 || $y % 5 == 0) {
														if ($y == 0) {?>
														<tr>
														<?php } else { ?>
														</tr><tr>
														<?php }
													}
													/* Busco de la sys00013 en la sys00014 */
													$qUsrPer  = "SELECT * ";
													$qUsrPer .= "FROM $cAlfa.sys00014 ";
													$qUsrPer .= "WHERE ";
													$qUsrPer .= "usridxxx = \"{$kUser}\" AND ";
													$qUsrPer .= "modidxxx = \"{$mUsrMen['modidxxx']}\"  AND ";
													$qUsrPer .= "proidxxx = \"{$mUsrMen['proidxxx']}\"  AND ";
													$qUsrPer .= "menidxxx = \"{$mUsrMen['menidxxx']}\"  LIMIT 0,1";
													$xUsrPer  = f_MySql("SELECT","",$qUsrPer,$xConexion01,"");
													// echo $qUsrPer."~".mysql_num_rows($xUsrPer);

													if (mysql_num_rows($xUsrPer) > 0) { ?>
														<td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory_New ?>/<?php echo $mUsrMen['menimgon'] ?>" style = "cursor:pointer" onClick ="javascript:fnLink('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')"><br>
														<a href = "javascript:fnLink('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')"
															style="color:<?php echo $vSysStr['system_link_menu_color'] ?>"><?php echo $mUsrMen['mendesxx'] ?></a></center></td>
													<?php	} else { ?>
														<td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory_New ?>/<?php echo $mUsrMen['menimgof']?>"><br>
														<?php echo $mUsrMen['mendesxx'] ?></center></td>
													<?php }
													$y++;
												}
												$celdas = "";
												$nf = intval($y/5);
												$resto = $y-$nf;
												$restan = 5-$resto;
												if ($restan > 0) {
													for ($i=0;$i<$restan;$i++) {
														$celdas.="<td width='20%'></td>";
													}
													echo $celdas;
												} ?>
												</tr>
										</table>
									</center>
								</fieldset>
							</td>
						</tr>
					</table>
				</center>
			<?php } ?>
			<!-- Fin Nivel de Procesos -->
			<?php
				if ($vLimInf == "" && $vLimSup == "") {
					$vLimInf = "00";
					$vLimSup = $vSysStr['system_rows_page_ini'];
				}elseif ($vLimInf == "") {
					$vLimInf = "00";
				}

				if ($vPaginas == "") {
					$vPaginas = "1";
				}

				$y=0;
				$mDescuentos = array();
				$qDescuento  = "SELECT DISTINCT SQL_CALC_FOUND_ROWS ";
				$qDescuento .= "$cAlfa.fpar0166.colidxxx, ";
				$qDescuento .= "$cAlfa.fpar0166.coldesxx, ";
				$qDescuento .= "$cAlfa.fpar0166.colorden, ";
				$qDescuento .= "$cAlfa.fpar0166.colctoid, ";
				$qDescuento .= "$cAlfa.fpar0166.colctode, ";
				$qDescuento .= "$cAlfa.fpar0166.regfcrex, ";
				$qDescuento .= "$cAlfa.fpar0166.reghcrex, ";
				$qDescuento .= "$cAlfa.fpar0166.regfmodx, ";
				$qDescuento .= "$cAlfa.fpar0166.reghmodx, ";
				$qDescuento .= "$cAlfa.fpar0166.regestxx ";
				$qDescuento .= "FROM $cAlfa.fpar0166 ";
				if ($_POST['vSearch'] != "") {
					$qDescuento .= "WHERE ";
					$qDescuento .= "($cAlfa.fpar0166.colidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qDescuento .= "$cAlfa.fpar0166.coldesxx LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qDescuento .= "$cAlfa.fpar0166.colorden LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qDescuento .= "$cAlfa.fpar0129.serdespx LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qDescuento .= "$cAlfa.fpar0129.serdesxx LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qDescuento .= "$cAlfa.fpar0166.colctoid LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qDescuento .= "$cAlfa.fpar0130.fcodesxx LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qDescuento .= "$cAlfa.fpar0166.colctode LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qDescuento .= "$cAlfa.fpar0166.regestxx LIKE \"%{$_POST['vSearch']}%\") ";
				}

				// echo $qDescuento;

				/*** CODIGO NUEVO PARA ORDER BY ***/
				$cOrderBy = "";
				$vOrderByOrder = explode("~",$_POST['cOrderByOrder']);

				for ($z=0;$z<count($vOrderByOrder);$z++) {
					if ($vOrderByOrder[$z] != "") {
						if ($_POST[$vOrderByOrder[$z]] != "") {
							if (substr_count($_POST[$vOrderByOrder[$z]], "regfcrex") > 0) {
								//Ordena por "regfcrex reghcrexs"
								$cOrdFecCre = str_replace("regfcrex", "CONCAT(fpar0166.regfcrex,\" \",fpar0166.reghcrex)", $_POST[$vOrderByOrder[$z]]);
								$cOrderBy .= $cOrdFecCre;
							} else if (substr_count($_POST[$vOrderByOrder[$z]], "regfmodx") > 0) {
								//Ordena por "regfmodx regfmodx"
								$cOrdFecMod = str_replace("regfmodx", "CONCAT(fpar0166.regfmodx,\" \",fpar0166.reghmodx)", $_POST[$vOrderByOrder[$z]]);
								$cOrderBy .= $cOrdFecMod;
							} else if (substr_count($_POST[$vOrderByOrder[$z]], "serdesxx") > 0) {
								$cOrdFecSer = str_replace("serdesxx", "IF(fpar0129.serdespx != \"\",fpar0129.serdespx,fpar0129.serdesxx)", $_POST[$vOrderByOrder[$z]]);
								$cOrderBy .= $cOrdFecSer;
							} else{
								$cOrderBy .= $_POST[$vOrderByOrder[$z]];
							}
						}
					}
				}

				if (strlen($cOrderBy)>0) {
					$cOrderBy = substr($cOrderBy,0,strlen($cOrderBy)-1);
					$cOrderBy = "ORDER BY ".$cOrderBy;
				} else {
					$cOrderBy = "ORDER BY fpar0166.regfmodx DESC, fpar0166.reghmodx DESC";
				}
				/*** FIN CODIGO NUEVO PARA ORDER BY ***/
				$qDescuento .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
				$xDescuento  = f_MySql("SELECT","",$qDescuento,$xConexion01,"");
				// echo "<br>".$qDescuento."~".mysql_num_rows($xDescuento);
				/***** FIN SQL *****/

				$xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
				$xRNR = mysql_fetch_array($xNumRows);
				$nRNR += $xRNR['FOUND_ROWS()'];
				// echo "<br>Registros: ".$nRNR;

				while ($xRD = mysql_fetch_array($xDescuento)) {
					$mDescuentos[count($mDescuentos)] = $xRD;
				}
			?>
			<center>
				<table width="95%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<fieldset>
								<legend>Registros en la Consulta (<?php echo $nRNR?>)</legend>
								<center>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>
											<td class="clase08" width="14%">
												<input type="text" class="letra" name = "vSearch" maxlength="50" value = "<?php echo $vSearch ?>" style= "width:80"
													onblur="javascript:this.value=this.value.toUpperCase();
																						document.forms['frnav']['vLimInf'].value='00';
																						document.forms['frnav']['vPaginas'].value='1'">
												<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_search.png" style = "cursor:pointer" title="Buscar"
													onClick = "javascript:document.forms['frnav']['vBuscar'].value = 'ON'
																								document.forms['frnav']['vSearch'].value=document.forms['frnav']['vSearch'].value.toUpperCase();
																								document.forms['frnav'].submit()">
												<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/btn_show-all_bg.gif" style = "cursor:pointer" title="Mostrar Todo"
													onClick ="javascript:document.forms['frnav']['vSearch'].value='';
																							document.forms['frnav']['vLimInf'].value='00';
																							document.forms['frnav']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
																							document.forms['frnav']['vPaginas'].value='1';
																							document.forms['frnav']['vSortField'].value='';
																							document.forms['frnav']['vSortType'].value='';
																							document.forms['frnav']['vBuscar'].value='';
																							document.forms['frnav'].submit()">
											</td>
											<td class="name" width="06%" align="left">Filas&nbsp;
												<input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
													onblur = "javascript:f_FixFloat(this);
																							document.forms['frnav']['vLimInf'].value='00';">
											</td>
											<td class="name" width="08%">
												<?php

												if (ceil($nRNR/$vLimSup) > 1) { ?>
													<?php if ($vPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
														<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
														<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_nextpage.png"  	style = "cursor:pointer" title="Pagina Siguiente"
															onClick = "javascript:document.forms['frnav']['vPaginas'].value++;
																										document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
																										document.forms['frnav'].submit()">
														<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_lastpage.png"  	style = "cursor:pointer" title="Ultima Pagina"
															onClick = "javascript:document.forms['frnav']['vPaginas'].value='<?php echo ceil($nRNR/$vLimSup) ?>';
																										document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
																										document.forms['frnav'].submit()">
													<?php } ?>
													<?php if ($vPaginas > "1" && $vPaginas < ceil($nRNR/$vLimSup)) { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
															onClick = "javascript:document.forms['frnav']['vPaginas'].value='1';
																										document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
																										document.forms['frnav'].submit()">
														<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
															onClick = "javascript:document.forms['frnav']['vPaginas'].value--;
																										document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
																										document.forms['frnav'].submit()">
														<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente"
															onClick = "javascript:document.forms['frnav']['vPaginas'].value++;
																										document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
																										document.forms['frnav'].submit()">
														<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina"
															onClick = "javascript:document.forms['frnav']['vPaginas'].value='<?php echo ceil($nRNR/$vLimSup) ?>';
																										document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
																										document.forms['frnav'].submit()">
													<?php } ?>
													<?php if ($vPaginas == ceil($nRNR/$vLimSup)) { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
															onClick = "javascript:document.forms['frnav']['vPaginas'].value='1';
																										document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
																										document.forms['frnav'].submit()">
														<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
															onClick = "javascript:document.forms['frnav']['vPaginas'].value--;
																										document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frnav']['vPaginas'].value-1));
																										document.forms['frnav'].submit()">
														<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_nextpage.png" style = "cursor:pointer" title="Pagina Siguiente">
														<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_lastpage.png" style = "cursor:pointer" title="Ultima Pagina">
													<?php } ?>
												<?php } else { ?>
													<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
													<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
													<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente">
													<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/bd_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina">
												<?php } ?>
											</td>
											<td class="name" width="08%" align="left">Pag&nbsp;
												<select Class = "letrase" name = "vPaginas" value = "<?php echo $vPaginas ?>" style = "width:60%"
													onchange="javascript:this.id = 'ON';
																							document.forms['frnav']['vLimInf'].value=('<?php echo $vLimSup ?>'*(this.value-1));
																							document.forms['frnav'].submit()">
													<?php for ($i=0;$i<ceil($nRNR/$vLimSup);$i++) {
														if ($i+1 == $vPaginas) { ?>
															<option value = "<?php echo $i+1 ?>" selected><?php echo $i+1 ?></option>
														<?php } else { ?>
															<option value = "<?php echo $i+1 ?>"><?php echo $i+1 ?></option>
														<?php } ?>
													<?php } ?>
												</select>
											</td>
											<td Class="name" width="15%" align="right">&nbsp;
												<?php
													/***** Botones de Acceso Rapido *****/
													$qBotAcc  = "SELECT sys00013.menopcxx,sys00013.mendesxx,sys00014.modidxxx  ";
													$qBotAcc .= "FROM $cAlfa.sys00013,$cAlfa.sys00014 ";
													$qBotAcc .= "WHERE ";
													$qBotAcc .= "sys00014.usridxxx = \"{$kUser}\" AND ";
													$qBotAcc .= "sys00014.modidxxx = sys00013.modidxxx        AND ";
													$qBotAcc .= "sys00014.proidxxx = sys00013.proidxxx        AND ";
													$qBotAcc .= "sys00014.menidxxx = sys00013.menidxxx        AND ";
													$qBotAcc .= "sys00014.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
													$qBotAcc .= "sys00014.proidxxx = \"{$_COOKIE['kProId']}\" ";
													$qBotAcc .= "ORDER BY sys00013.menordxx";
													$xBotAcc  = f_MySql("SELECT","",$qBotAcc,$xConexion01,"");

													while ($xRBA = mysql_fetch_array($xBotAcc)) {
														switch ($xRBA['menopcxx']) {
															case "EDITAR": ?>
																<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_edit.png" onClick = "javascript:fnEditar('<?php echo $xRBA['menopcxx'] ?>')"
																style = "cursor:pointer" title="<?php echo ucwords(strtolower($xRBA['mendesxx'])) ?>">
															<?php break;
															case "BORRAR": ?>
																<img src = "<?php echo $cPlesk_Skin_Directory_New ?>/b_drop.png" onClick = "javascript:fnBorrar('<?php echo $xRBA['menopcxx'] ?>')"
																style = "cursor:hand" title="<?php echo ucwords(strtolower($xRBA['mendesxx'])) ?>">
															<?php break;
														}
													}
													/***** Fin Botones de Acceso Rapido *****/
												?>
											</td>
										</tr>
									</table>
								</center>
								<hr></hr>
								<center>
									<table cellspacing="0" width="100%">
										<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
											<td class="name" width="5%">
												<a href = "javascript:fnOrderBy('onclick','colidxxx');" title="Ordenar">Id</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "colidxxx">
												<input type = "hidden" name = "colidxxx" value = "<?php echo $_POST['colidxxx'] ?>" id = "colidxxx">
												<script language="javascript">fnOrderBy('','colidxxx')</script>
											</td>
											<td class="name" width="20%">
												<a href = "javascript:fnOrderBy('onclick','coldesxx');" title="Ordenar">Descripci&oacute;n Columna</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "coldesxx">
												<input type = "hidden" name = "coldesxx" value = "<?php echo $_POST['coldesxx'] ?>" id = "coldesxx">
												<script language="javascript">fnOrderBy('','coldesxx')</script>
											</td>
											<td class="name" width="6%">
												<a href = "javascript:fnOrderBy('onclick','colorden');" title="Ordenar">Orden</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "colorden">
												<input type = "hidden" name = "colorden" value = "<?php echo $_POST['colorden'] ?>" id = "colorden">
												<script language="javascript">fnOrderBy('','colorden')</script>
											</td>
											<td class="name" width="8%">
												<a href = "javascript:fnOrderBy('onclick','colctoid');" title="Ordenar">Conceptos</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "colctoid">
												<input type = "hidden" name = "colctoid" value = "<?php echo $_POST['colctoid'] ?>" id = "colctoid">
												<script language="javascript">fnOrderBy('','colctoid')</script>
											</td>
											<td class="name" width="26%">
												<a href = "javascript:fnOrderBy('onclick','colctode');" title="Ordenar">Descripci&oacute;n Personalizada</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "colctode">
												<input type = "hidden" name = "colctode" value = "<?php echo $_POST['colctode'] ?>" id = "colctode">
												<script language="javascript">fnOrderBy('','colctode')</script>
											</td>
											<td class="name" width="10%">
												<a href = "javascript:fnOrderBy('onclick','regfcrex');" title="Ordenar">Creado</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfcrex">
												<input type = "hidden" name = "regfcrex" value = "<?php echo $_POST['regfcrex'] ?>" id = "regfcrex">
												<script language="javascript">fnOrderBy('','regfcrex')</script>
											</td>
											<td class="name" width="8%">
												<a href = "javascript:fnOrderBy('onclick','regfhrex');" title="Ordenar">Hora</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfhrex">
												<input type = "hidden" name = "regfhrex" value = "<?php echo $_POST['regfhrex'] ?> %" id = "regfhrex">
												<script language="javascript">fnOrderBy('','regfhrex')</script>
											</td>
											<td class="name" width="10%">
												<a href = "javascript:fnOrderBy('onclick','regfmodx');" title="Ordenar">Modificado</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfmodx">
												<input type = "hidden" name = "regfmodx" value = "<?php echo $_POST['regfmodx']?>" id = "regfmodx">
												<script language="javascript">fnOrderBy('', 'regfmodx')</script>
											</td>
											<td class="name" width="8%">
												<a href = "javascript:fnOrderBy('onclick','reghmodx');" title="Ordenar">Hora</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghmodx">
												<input type = "hidden" name = "reghmodx" value = "<?php echo $_POST['reghmodx']?>" id = "reghmodx">
												<script language="javascript">fnOrderBy('', 'reghmodx')</script>
											</td>																																					
											<td class="name" width="5%">
												<a href = "javascript:fnOrderBy('onclick','regestxx');" title="Ordenar">Estado</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory_New ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
												<input type = "hidden" name = "regestxx" value = "<?php echo $_POST['regestxx'] ?>" id = "regestxx">
												<script language="javascript">fnOrderBy('','regestxx')</script>
											</td>
											<td Class='name' width="2%" align="right">
												<input type="checkbox" name="oCheckAll" onClick = 'javascript:fnMarca()'>
											</td>
										</tr>
										<script languaje="javascript">
											document.forms['frnav']['vRecords'].value = "<?php echo count($mDescuentos) ?>";
										</script>
											<?php
										$y = 0;
										for ($i=0;$i<count($mDescuentos);$i++) {
											if ($i < count($mDescuentos)) { // Para Controlar el Error
												$cColor = "{$vSysStr['system_row_impar_color_ini']}";
											if($y % 2 == 0) {
												$cColor = "{$vSysStr['system_row_par_color_ini']}";
											}
											?>
											<!--<tr bgcolor = "<?php echo $cColor ?>">-->
											<tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
												<td class="letra7"><a href = javascript:fnVer('<?php echo $mDescuentos[$i]['colidxxx']?>')><?php echo $mDescuentos[$i]['colidxxx'] ?></a></td>
												<td class="letra7"><?php echo $mDescuentos[$i]['coldesxx'] ?></td>
												<td class="letra7"><?php echo $mDescuentos[$i]['colorden'] ?></td>
												<td class="letra7"><?php echo $mDescuentos[$i]['colctoid'] ?></td>
												<td class="letra7"><?php echo $mDescuentos[$i]['colctode'] ?></td>
												<td class="letra7"><?php echo $mDescuentos[$i]['regfcrex'] ?></td>
												<td class="letra7"><?php echo $mDescuentos[$i]['regfhrex'] ?></td>
												<td class="letra7"><?php echo $mDescuentos[$i]['regfmodx']." ".$mDescuentos[$i]['reghcrex'] ?></td>
												<td class="letra7"><?php echo $mDescuentos[$i]['reghmodx']." ".$mDescuentos[$i]['reghmodx'] ?></td>
												<td class="letra7"><?php echo $mDescuentos[$i]['regestxx'] ?></td>
												<td Class="letra7" align="right"><input type="checkbox" name="oCheck"  value = "<?php echo count($mDescuentos) ?>"
													id="<?php echo $mDescuentos[$i]['colidxxx'].'~'.$mDescuentos[$i]['regestxx']?>"
													onclick="javascript:document.forms['frnav']['vRecords'].value='<?php echo count($mDescuentos) ?>'">
												</td>
											</tr>
											<?php $y++;
											}
										} ?>
									</table>
								</center>
							</fieldset>
						</td>
					</tr>
				</table>
			</center>
		</form>
	</body>
</html>

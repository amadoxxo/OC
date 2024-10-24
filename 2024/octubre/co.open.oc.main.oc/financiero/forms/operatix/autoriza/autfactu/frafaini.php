<?php
  namespace openComex;
	/**
	 * Anular Formularios.
	 * --- Descripcion: Me lista los formularios con estado ASIGNADO. de todos los Directores de Cuenta de Toda Colombia.
	 * @author Pedro Leon Burbano Suarez <pedrob@repremundo.com.co>
	 * @version 001
	 */
	include("../../../../libs/php/utility.php");
	
	/* Busco en la 05 que Tiene Permiso el Usuario*/
	$qUsrMen  = "SELECT * ";
	$qUsrMen .= "FROM $cAlfa.sys00005 ";
	$qUsrMen .= "WHERE ";
	$qUsrMen .= "sys00005.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
	$qUsrMen .= "sys00005.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
	$qUsrMen .= "sys00005.menimgon <> \"\" ";
	$qUsrMen .= "ORDER BY sys00005.menordxx";
	$xUsrMen  = f_MySql("SELECT","",$qUsrMen,$xConexion01,"");

	$cPerAno = date('Y');
	$cPerMes = date('m');
?>

<html>
	<head>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
			<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script language="javascript">


			function f_Link(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
				document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
				document.cookie="kMenDes="+xMenDes+";path="+"/";
				document.cookie="kModo="+xOpcion;
				//document.cookie="kModo="+xOpcion+";path="+"/";
				parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
				document.location = xForm; // Invoco el menu.
			}

			function f_Anular() {
				switch (document.forms['frgrm']['vRecords'].value) {
					case "1":
						if (document.forms['frgrm']['vCheck'].checked == true) {
							var zMatriz = document.forms['frgrm']['vCheck'].id.split('~');
							if (zMatriz[3] != "NO") {
								document.cookie="kModo=ANULAR";
								document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
								// console.log(zMatriz[0] + '~' + zMatriz[1] + '~' + zMatriz[2] + '|');
								document.forms['frestado']['cComMemo'].value  = zMatriz[0] + '~' + zMatriz[1] + '~' + zMatriz[2] + '|';

								if ( document.forms['frestado']['cComMemo'].value != "" ) {
									if (confirm("Esta Seguro de Anular la Autorizacion para Facturacion Manual de los Do Seleccionados?")) {
										document.forms['frestado'].submit();
									}
								}
							} else {
								alert("El DO: "+zMatriz[0]+"-"+zMatriz[1]+"-"+zMatriz[2]+" ya esta Anulado para Facturacion Manual");
							}
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frgrm']['vCheck'].length;i++) {
							if (document.forms['frgrm']['vCheck'][i].checked == true ) {
								// Solo Deja Legalizar el Primero Seleccionado
								zSw_Prv++;
								var zMatriz = document.forms['frgrm']['vCheck'][i].id.split('~');
								if (zMatriz[3] != "NO") {
									
									document.cookie="kModo=ANULAR";
									document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
									document.forms['frestado']['cComMemo'].value  += zMatriz[0] + '~' + zMatriz[1] + '~' + zMatriz[2] + '|';
								} else {
									alert("El DO: "+zMatriz[0]+"-"+zMatriz[1]+"-"+zMatriz[2]+" ya esta Anulado para Facturacion Manual");
								}

								if ( document.forms['frestado']['cComMemo'].value != "" ) {
									if (confirm("Esta Seguro de Anular la Autorizacion para Facturacion Manual de los Do Seleccionados?")) {
										document.forms['frestado'].submit();
									}
								}
							}
						}
					break;
				}
			}

			function f_CargaVariable(xRecords) {
				var paso=0;
				switch (xRecords) {
					case "1":
						if (document.forms['frgrm']['vCheck'].checked == true) {
							document.forms['frgrm']['vDo'].value = document.forms['frgrm']['vCheck'].value;
						}
					break;
					default:
						for (i=0;i<document.forms['frgrm']['vCheck'].length;i++) {
							if (document.forms['frgrm']['vCheck'][i].checked == true && paso==0) {
								document.forms['frgrm']['vDo'].value += document.forms['frgrm']['vCheck'][i].value;
								paso=1;
							}
						}
					break;
				}
			}

			function f_Marca() {
				if (document.forms['frgrm']['vCheckAll'].checked == true){
					if (document.forms['frgrm']['vRecords'].value == 1){
						document.forms['frgrm']['vCheck'].checked=true;
					} else {
						if (document.forms['frgrm']['vRecords'].value > 1){
							for (i=0;i<document.forms['frgrm']['vCheck'].length;i++){
								document.forms['frgrm']['vCheck'][i].checked = true;
							}
						}
					}
				} else {
					if (document.forms['frgrm']['vRecords'].value == 1){
						document.forms['frgrm']['vCheck'].checked=false;
					} else {
						if (document.forms['frgrm']['vRecords'].value > 1){
							for (i=0;i<document.forms['frgrm']['vCheck'].length;i++){
								document.forms['frgrm']['vCheck'][i].checked = false;
							}
						}
					}
				}
			}

			function f_VerificarCheck() {
				if(document.forms['frgrm']['vCheckAll'].checked == true)
					document.forms['frgrm']['vChekeados'].value=1;
				if (document.forms['frgrm']['vRecords'].value == 1){
					if(document.forms['frgrm']['vCheck'].checked == true)
						document.forms['frgrm']['vChekeados'].value=1;
				}else {
					if (document.forms['frgrm']['vRecords'].value > 1){
						for (i=0;i<document.forms['frgrm']['vCheck'].length;i++){
							if(document.forms['frgrm']['vCheck'][i].checked == true){
								document.forms['frgrm']['vChekeados'].value=1;
								i=document.forms['frgrm']['vCheck'].length;
							}
						}
					}
				}
			}

			/************************ FUNCION PARA GUARDAR EL ORDEN DEL ORDER BY DEL SQL ***********************/
			function f_Order_By(xEvento,xCampo) {
				// alert(document.forms['frgrm'][xCampo].value);
				if (document.forms['frgrm'][xCampo].value != '') {
					var vSwitch = document.forms['frgrm'][xCampo].value.split(' ');
					var cSwitch = vSwitch[1];
				} else {
					var cSwitch = '';
				}
				// alert(cSwitch);
				if (xEvento == 'onclick') {
					switch (cSwitch) {
						case '':
							document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id+' ASC,';
							document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
							if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
								document.forms['frgrm']['cOrderByOrder'].value += xCampo+"~";
							}
						break;
						case 'ASC,':
							document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id+' DESC,';
							document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
							if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
								document.forms['frgrm']['cOrderByOrder'].value += xCampo+"~";
							}
						break;
						case 'DESC,':
							document.forms['frgrm'][xCampo].value = '';
							document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
							if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) >= 0) {
								document.forms['frgrm']['cOrderByOrder'].value = document.forms['frgrm']['cOrderByOrder'].value.replace(xCampo,"");
							}
						break;
					}
				} else {
					switch (cSwitch) {
						case '':
							document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
						break;
						case 'ASC,':
							document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
						break;
						case 'DESC,':
							document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
						break;
					}
				}
			}

		</script>
		<style type="text/css">
			SELECT{ font-family: verdana; font-size: 10px; color:#2B547D; background-color:#D8E4F1;}
		</style>

	</head>
	<body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
		<form name = "frestado" action = "frafagra.php" method = "post" target="fmpro">
			<textarea  name = "cComMemo" id = "cComMemo" ></textarea>
		</form>

		<form name = "frgrm" method = "post" >
			<input type = "hidden" name = "vChekeados" value = "">
			<input type = "hidden" name = "vDo"        value = "">
			<input type = "hidden" name = "vComMemo"   value = "">
			<input type = "hidden" name = "gTipSav"    value = "">
			<input type = "hidden" name = "vEstado"    value = "">
			<input type = "hidden" name = "vRecords"   value = "">
			<input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
			<input type = "hidden" name = "vSortField" value = "<?php echo $vSortField ?>">
			<input type = "hidden" name = "vSortType"  value = "<?php echo $vSortType ?>">
			<input type = "hidden" name = "vTimes"     value = "<?php echo $vTimes ?>">
			<input type = "hidden" name = "cOrderByOrder"  value = "<?php echo $_POST['cOrderByOrder'] ?>" style = "width:1000">

			<!-- Inicia Nivel de Procesos -->
			<?php if (mysql_num_rows($xUsrMen) > 0) { ?>
				<center>
					<script languaje = "javascript">
						document.getElementById("cComMemo").style.display="none";
					</script>
					<table width="95%" cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td>
								<fieldset>
									<legend>Proceso <?php echo $_COOKIE['kProDes'] ?></legend>
									<center>
										<table cellspacing="0" width="100%">
											<?php
												$y = 0;
												/* Empiezo a Leer la sys00005 */
												while($xRUM = mysql_fetch_array($xUsrMen)) {
													if($y == 0 || $y % 5 == 0) {
														if ($y == 0) {?>
														<tr>
														<?php } else { ?>
														</tr><tr>
														<?php }
													}
													/* Busco de la sys00005 en la sys00006 */
													$qUsrPer  = "SELECT * ";
													$qUsrPer .= "FROM $cAlfa.sys00006 ";
													$qUsrPer .= "WHERE ";
													$qUsrPer .= "usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
													$qUsrPer .= "modidxxx = \"{$xRUM['modidxxx']}\"  AND ";
													$qUsrPer .= "proidxxx = \"{$xRUM['proidxxx']}\"  AND ";
													$qUsrPer .= "menidxxx = \"{$xRUM['menidxxx']}\"  LIMIT 0,1";
													$xUsrPer  = f_MySql("SELECT","",$qUsrPer,$xConexion01,"");
													if (mysql_num_rows($xUsrPer) > 0) { ?>
														<td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory ?>/<?php echo $xRUM['menimgon'] ?>" style = "cursor:pointer" onClick ="javascript:f_Link('<?php echo $xRUM['modidxxx'] ?>','<?php echo $xRUM['proidxxx'] ?>','<?php echo $xRUM['menidxxx'] ?>','<?php echo $xRUM['menformx']?>','<?php echo $xRUM['menopcxx']?>','<?php echo $xRUM['mendesxx']?>')"><br>
														<a href = "javascript:f_Link('<?php echo $xRUM['modidxxx'] ?>','<?php echo $xRUM['proidxxx'] ?>','<?php echo $xRUM['menidxxx'] ?>','<?php echo $xRUM['menformx']?>','<?php echo $xRUM['menopcxx']?>','<?php echo $xRUM['mendesxx']?>')"
															style="color:<?php echo $vSysStr['system_link_menu_color'] ?>"><?php echo $xRUM['mendesxx'] ?></a></center></td>
													<?php	} else { ?>
														<td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory ?>/<?php echo $xRUM['menimgof']?>"><br>
														<?php echo $xRUM['mendesxx'] ?></center></td>
													<?php }
													$y++;
												}
												$nCeldas = "";
												$nf = intval($y/5);
												$nResto = $y-$nf;
												$nRestan = 5-$nResto;
												if ($nRestan > 0) {
													for ($i=0;$i<$nRestan;$i++) {
														$nCeldas.="<td width='20%'></td>";
													}
													echo $nCeldas;
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

				if ($_POST['vSearch'] != "") {
					/**
					 * Buscando los id que corresponden a las busquedas de los lefjoin
					 */
					$qCliNom  = "SELECT ";
					$qCliNom .= "CLIIDXXX ";
					$qCliNom .= "FROM $cAlfa.SIAI0150 ";
					$qCliNom .= "WHERE IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) <> \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) LIKE \"%{$_POST['vSearch']}%\" ";
					$xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
					$cCliIdSearch = "";
					while ($xRCN = mysql_fetch_array($xCliNom)) {
					$cCliIdSearch .= "\"{$xRCN['CLIIDXXX']}\",";
					}
					$cCliIdSearch = substr($cCliIdSearch,0,strlen($cCliIdSearch)-1);
				}

				/**
				 * Realizo la consulta de los formularios legalizados o no.
				 */

				$y=0;
				$qFactMan  = "SELECT DISTINCT ";
				$qFactMan .= "SQL_CALC_FOUND_ROWS ";
				$qFactMan .= "CONCAT($cAlfa.sys00121.sucidxxx,\"-\",$cAlfa.sys00121.docidxxx,\"-\",$cAlfa.sys00121.docsufxx) AS docidcom, ";
				$qFactMan .= "$cAlfa.sys00121.sucidxxx, ";
				$qFactMan .= "$cAlfa.sys00121.docidxxx, ";
				$qFactMan .= "$cAlfa.sys00121.docsufxx, ";
				$qFactMan .= "$cAlfa.sys00121.doctipxx, ";
				$qFactMan .= "$cAlfa.sys00121.cliidxxx, ";
				$qFactMan .= "$cAlfa.sys00121.docfmaxx, ";
				$qFactMan .= "$cAlfa.sys00121.regfcrex, ";
				$qFactMan .= "$cAlfa.sys00121.reghcrex, ";
				$qFactMan .= "$cAlfa.sys00121.regfmodx, ";
				$qFactMan .= "$cAlfa.sys00121.reghmodx, ";
				$qFactMan .= "$cAlfa.sys00121.regestxx ";
				if (substr_count($cOrderByOrder,"CLINOMXX") > 0) {
					$qFactMan .= ", IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
				}
				$qFactMan .= "FROM $cAlfa.sys00121 ";
				if (substr_count($cOrderByOrder,"CLINOMXX") > 0) {
					$qFactMan .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
				}
				$qFactMan .= "WHERE ";
				$qFactMan .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" AND ";
				$qFactMan .= "$cAlfa.sys00121.docfmaxx = \"SI\" AND ";

				### CODIGO PARA REEEMPLAZAR EL {$_POST['vSearch']} ###
				$qFactMan .= "(CONCAT($cAlfa.sys00121.sucidxxx,\"-\",$cAlfa.sys00121.docidxxx,\"-\",$cAlfa.sys00121.docsufxx) LIKE \"%{$_POST['vSearch']}%\" OR ";
				$qFactMan .= "$cAlfa.sys00121.doctipxx LIKE \"%{$_POST['vSearch']}%\" OR ";
				$qFactMan .= "$cAlfa.sys00121.cliidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
				$qFactMan .= "$cAlfa.sys00121.regestxx LIKE \"%{$_POST['vSearch']}%\" ";
				if ($cCliIdSearch != "") {
					$qFactMan .= "OR $cAlfa.sys00121.cliidxxx IN ($cCliIdSearch) ";
				}
				$qFactMan .= ")";

				### CODIGO PARA ORDER BY ###
				$vOrderByOrder = explode("~",$cOrderByOrder);
				for ($z=0;$z<count($vOrderByOrder);$z++) {
					if ($vOrderByOrder[$z] != "") {
						$cOrderBy .= $_POST[$vOrderByOrder[$z]];
					}
				}
				if (strlen($cOrderBy)>0) {
					$cOrderBy = substr($cOrderBy,0,strlen($cOrderBy)-1);
					$cOrderBy = "ORDER BY ".$cOrderBy;
				}else{
					$cOrderBy = "ORDER BY regfmodx DESC, reghmodx DESC ";
				}
				### FIN CODIGO PARA ORDER BY ###

				$qFactMan .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
				$cIdCountRow = mt_rand(1000000000, 9999999999);
				$xFactMan = mysql_query($qFactMan, $xConexion01, true, $cIdCountRow);
				// f_Mensaje(__FILE__,__LINE__,$qFactMan." ~ ".mysql_num_rows($xFactMan));

				$xNumRows = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD", $xConexion01, false);
				$xRNR     = mysql_fetch_array($xNumRows);
				$nRNR     = $xRNR['CANTIDAD'];

				/* Cargo la Matriz con los ROWS del Cursor */
				$i=0;
				while ($zRCab = mysql_fetch_array($xFactMan)) {
					
					//Busco la el nombre del cliente
					if (substr_count($cOrderByOrder,"CLINOMXX") == 0) {
						$qDatCli  = "SELECT ";
						$qDatCli .= "$cAlfa.SIAI0150.*, ";
						$qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
						$qDatCli .= "FROM $cAlfa.SIAI0150 ";
						$qDatCli .= "WHERE ";
						$qDatCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$zRCab['cliidxxx']}\" LIMIT 0,1";
						$xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
						if(mysql_num_rows($xDatCli) > 0) {
							$xRDC = mysql_fetch_array($xDatCli);
							$zRCab['CLINOMXX'] = $xRDC['CLINOMXX'];
						} else {
							$zRCab['CLINOMXX'] = "CLIENTE SIN NOMBRE";
						}
					}

					$mMatrizTra[$i] = $zRCab;
					$i++;
				}

				/* Fin de Recorro la Matriz para Traer Datos Externos */
			?>
			<center>
				<table width="95%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<fieldset>
								<legend>Autorizaciones Realizadas(<?php echo $nRNR ?>)</legend>
								<center>
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>
											<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>
											<td class="clase08" width="14%">
												<input type="text" class="letra" name = "vSearch" maxlength="20" value = "<?php echo $vSearch ?>" style= "width:80"
													onblur="javascript:this.value=this.value.toUpperCase();
																						document.forms['frgrm']['vLimInf'].value='00';
																						document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
																						document.forms['frgrm']['vPaginas'].value='1'
																						document.forms['frgrm'].submit()">
												<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:hand" title="Buscar"
													onClick = "javascript:document.forms['frgrm']['vSearch'].value=document.forms['frgrm']['vSearch'].value.toUpperCase();
																								document.forms['frgrm']['vLimInf'].value='00';
																								document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
																								document.forms['frgrm']['vPaginas'].value='1'
																								document.forms['frgrm'].submit()">
												<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:hand" title="Mostrar Todo"
													onClick ="javascript:document.forms['frgrm']['vSearch'].value='';
																							document.forms['frgrm']['vLimInf'].value='00';
																							document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
																							document.forms['frgrm']['vPaginas'].value='1';
																							document.forms['frgrm']['vSortField'].value='';
																							document.forms['frgrm']['vSortType'].value='';
																							document.forms['frgrm']['cOrderByOrder'].value='';
																							document.forms['frgrm'].submit()">
											</td>
											<td class="name" width="08%" align="left">Filas&nbsp;
												<input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
													onfocus = "javascript:document.forms['frgrm']['vPaginas'].value='1'"
													onblur = "javascript:uFixFloat(this);
																							document.forms['frgrm']['vLimInf'].value='00';
																							document.forms['frgrm'].submit()">
											</td>
											<td class="name" width="08%">
												<?php if (ceil($nRNR/$vLimSup) > 1) { ?>
													<?php if ($vPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:hand" title="Primera Pagina">
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:hand" title="Pagina Anterior">
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:hand" title="Pagina Siguiente"
															onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
																										document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
																										document.forms['frgrm'].submit()">
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:hand" title="Ultima Pagina"
															onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil($nRNR/$vLimSup) ?>';
																										document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
																										document.forms['frgrm'].submit()">
													<?php } ?>
													<?php if ($vPaginas > "1" && $vPaginas < ceil($nRNR/$vLimSup)) { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
															onClick = "javascript:document.forms['frgrm']['vPaginas'].value='1';
																										document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
																										document.forms['frgrm'].submit()">
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
															onClick = "javascript:document.forms['frgrm']['vPaginas'].value--;
																										document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
																										document.forms['frgrm'].submit()">
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:hand" title="Pagina Siguiente"
															onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
																										document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
																										document.forms['frgrm'].submit()">
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:hand" title="Ultima Pagina"
															onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil($nRNR/$vLimSup) ?>';
																										document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
																										document.forms['frgrm'].submit()">
													<?php } ?>
													<?php if ($vPaginas == ceil($nRNR/$vLimSup)) { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
															onClick = "javascript:document.forms['frgrm']['vPaginas'].value='1';
																										document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
																										document.forms['frgrm'].submit()">
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
															onClick = "javascript:document.forms['frgrm']['vPaginas'].value--;
																										document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
																										document.forms['frgrm'].submit()">
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_nextpage.png" style = "cursor:hand" title="Pagina Siguiente">
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_lastpage.png" style = "cursor:hand" title="Ultima Pagina">
													<?php } ?>
												<?php } else { ?>
													<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:hand" title="Primera Pagina">
													<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:hand" title="Pagina Anterior">
													<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_nextpage.png"  style = "cursor:hand" title="Pagina Siguiente">
													<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_lastpage.png"  style = "cursor:hand" title="Ultima Pagina">
												<?php } ?>
											</td>
											<td class="name" width="08%" align="left">Pag&nbsp;
												<select Class = "letrase" name = "vPaginas" value = "<?php echo $vPaginas ?>" style = "width:60%"
													onchange="javascript:document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(this.value-1));
																							document.forms['frgrm'].submit()">
													<?php for ($i=0;$i<ceil($nRNR/$vLimSup);$i++) {
														if ($i+1 == $vPaginas) { ?>
															<option value = "<?php echo $i+1 ?>" selected><?php echo $i+1 ?></option>
														<?php } else { ?>
															<option value = "<?php echo $i+1 ?>"><?php echo $i+1 ?></option>
														<?php } ?>
													<?php } ?>
												</select>

											</td>
											<td Class="name" width="20%" align="right">
												<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_cambest.gif" name = "IdImg" onClick = "javascript:f_VerificarCheck();
																																																				f_CargaVariable(document.forms['frgrm']['vRecords'].value);
																																																				f_Anular('ANULAR')"
																																																				style = "cursor:pointer" title="Anular Autorizacion, Solo Uno">
												<script languaje="javascript">
													if(document.forms['frgrm']['vRecords'].value ==0){
														document.getElementById("IdImg").onclick="";
													}
												</script>
											</td>
										</tr>
									</table>
									<br>
									<table cellspacing="0" width="100%">
									<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
											<td class="name" width="20%">
												<a href = "javascript:f_Order_By('onclick','docidcom')" title="Ordenar">DO</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docidcom">
													<input type = "hidden" name = "docidcom" value = "<?php echo $_POST['docidcom'] ?>" id = "docidcom">
												<script language="javascript">f_Order_By('','docidcom','')</script>
											</td>
											<td class="name" width="10%">
												<a href = "javascript:f_Order_By('onclick','doctipxx')" title="Ordenar">Tipo Operacion</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "doctipxx">
													<input type = "hidden" name = "doctipxx" value = "<?php echo $_POST['doctipxx'] ?>" id = "doctipxx">
												<script language="javascript">f_Order_By('','doctipxx','')</script>
											</td>
											<td class="name" width="10%">
												<a href = "javascript:f_Order_By('onclick','cliidxxx')" title="Ordenar">Nit</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "cliidxxx">
													<input type = "hidden" name = "cliidxxx" value = "<?php echo $_POST['cliidxxx'] ?>" id = "cliidxxx">													
												<script language="javascript">f_Order_By('','cliidxxx','')</script>
											</td>
											<td class="name" width="43%">
												<a href = "javascript:f_Order_By('onclick','CLINOMXX')" title="Ordenar">Cliente</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "CLINOMXX">
													<input type = "hidden" name = "CLINOMXX" value = "<?php echo $_POST['CLINOMXX'] ?>" id = "CLINOMXX">				
												<script language="javascript">f_Order_By('','CLINOMXX','')</script>
											</td>
											<td class="name" width="10%">
												<a href = "javascript:f_Order_By('onclick','docfmaxx')" title="Ordenar">Fact.Manual</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docfmaxx">
													<input type = "hidden" name = "docfmaxx" value = "<?php echo $_POST['docfmaxx'] ?>" id = "docfmaxx">													
												<script language="javascript">f_Order_By('','docfmaxx','')</script>
											</td>
											<td class="name" width="05%">
												<a href = "javascript:f_Order_By('onclick','regestxx')" title="Ordenar">Estado</a>&nbsp;
												<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
													<input type = "hidden" name = "regestxx" value = "<?php echo $_POST['regestxx'] ?>" id = "regestxx">													
												<script language="javascript">f_Order_By('','regestxx','')</script>
											</td>
											<td class="name" width="02%" align="right">
												<input type="checkbox" name="vCheckAll" onClick = 'javascript:f_Marca()'>
											</td>
										</tr>
										<script languaje="javascript">
											document.forms['frgrm']['vRecords'].value = "<?php echo count($mMatrizTra) ?>";
										</script>
										<?php 
											for($i=0;$i<count($mMatrizTra);$i++) {
												if ($y < count($mMatrizTra)) { // Para Controlar el Error
													$zColor = "{$vSysStr['system_row_impar_color_ini']}";
													if($y % 2 == 0) {
														$zColor = "{$vSysStr['system_row_par_color_ini']}";
													} ?>
												<tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
														<td class="letra7"><?php echo $mMatrizTra[$i]['docidcom'] ?></td>
														<td class="letra7"><?php echo $mMatrizTra[$i]['doctipxx'] ?></td>
														<td class="letra7"><?php echo $mMatrizTra[$i]['cliidxxx'] ?></td>
														<td class="letra7"><?php echo $mMatrizTra[$i]['CLINOMXX'] ?></td>
														<td class="letra7"><?php echo $mMatrizTra[$i]['docfmaxx'] ?></td>
														<td class="letra7"><?php echo $mMatrizTra[$i]['regestxx'] ?></td>
														<td class="letra7" align="right">
															<input type="checkbox" name="vCheck" value = "<?php echo count($mMatrizTra) ?>"
																id = "<?php echo $mMatrizTra[$i]['sucidxxx'].'~'.
																								$mMatrizTra[$i]['docidxxx'].'~'.
																								$mMatrizTra[$i]['docsufxx'].'~'.
																								$mMatrizTra[$i]['docfmaxx'].'~'.
																								$mMatrizTra[$i]['regestxx'] ?>"
																onclick="javascript:document.forms['frgrm']['vRecords'].value='<?php echo count($mMatrizTra) ?>'">
														</td>
												</tr>
													<?php $y++;
												}
											}
										?>
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
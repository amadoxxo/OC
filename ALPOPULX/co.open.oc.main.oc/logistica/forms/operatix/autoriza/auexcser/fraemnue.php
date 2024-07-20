<?php

/**
 * Proceso Autorizacion Excluir servicios
 * --- Descripcion: Permite Crear un Nueva autorizacion para Excluir servicios.
 * @author Cristian Perdomo <cristian.perdomo@openits.co>
 * @version 001
 */
include("../../../../../financiero/libs/php/utility.php");
?>
<html>

<head>
  <LINK rel="stylesheet" href="<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
	<LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
	<LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
	<LINK rel='stylesheet' href='<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
	<script languaje='javascript' src='<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
	<script language="javascript">
		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
			document.location = "<?php echo $_COOKIE['kIniAnt'] ?>";
			parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
		}

		function f_Marca() { //Marca y Desmarca los registros seleccionados en la tabla de Conceptos de Cobro
			if (document.forms['frgrm']['nCheckAll'].checked == true) {
				if (document.forms['frgrm']['nRecords'].value == 1) {
					document.forms['frgrm']['cCheck'].checked = true;
				} else {
					if (document.forms['frgrm']['nRecords'].value > 1) {
						for (i = 0; i < document.forms['frgrm']['cCheck'].length; i++) {
							document.forms['frgrm']['cCheck'][i].checked = true;
						}
					}
				}
			} else {
				if (document.forms['frgrm']['nRecords'].value == 1) {
					document.forms['frgrm']['cCheck'].checked = false;
				} else {
					if (document.forms['frgrm']['nRecords'].value > 1) {
						for (i = 0; i < document.forms['frgrm']['cCheck'].length; i++) {
							document.forms['frgrm']['cCheck'][i].checked = false;
						}
					}
				}
			}
		}

		function f_Carga_Data() { //Arma cadena para guardar en campo matriz de la sys00121
			document.forms['frgrm']['cComMemo'].value = "|";
			switch (document.forms['frgrm']['nRecords'].value) {
				case "1":
					if (document.forms['frgrm']['cCheck'].checked == true) {
						document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'].id + "|";
					}
					break;
				default:
					if (document.forms['frgrm']['cCheck'] !== undefined) {
						for (i = 0; i < document.forms['frgrm']['cCheck'].length; i++) {
							if (document.forms['frgrm']['cCheck'][i].checked == true) {
								document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'][i].id + "|";
							}
						}
					}
					break;
			}
			if (document.forms['frgrm']['cComMemo'].value == "|") {
				document.forms['frgrm']['cComMemo'].value = "";
			}
		}

		function f_Links(xLink, xSwitch) {
			var nX = screen.width;
			var nY = screen.height;
			switch (xLink) {
				case "cTerIdInt":
				case "cTerNomInt":
					if (xSwitch == "VALID") {
						var cRuta = "fraecint.php?gModo=" + xSwitch + "&gFunction=" + xLink +
							"&gTerId=" + document.forms['frgrm']['cCliId'].value +
							"&gTerIdInt=" + document.forms['frgrm'][xLink].value;
						// alert(cRuta);
						parent.fmpro.location = cRuta;
					} else {
						var nNx = (nX - 600) / 2;
						var nNy = (nY - 250) / 2;
						var cWinOpt = "width=600,scrollbars=1,height=250,left=" + nNx + ",top=" + nNy;
						var cRuta = "fraecint.php?gModo=" + xSwitch + "&gFunction=" + xLink +
							"&gTerId=" + document.forms['frgrm']['cCliId'].value +
							"&gTerIdInt=" + document.forms['frgrm'][xLink].value;
						cWindow = window.open(cRuta, xLink, cWinOpt);
						cWindow.focus();
					}
					break;
			}
		}

		function f_CargarTarifasFacturaA() {
			document.forms['frgrm'].target = 'fmwork';
			document.forms['frgrm'].action = 'fraecnue.php';
			document.forms['frgrm'].submit();
		}
	</script>
</head>

<body topmargin=0 leftmargin=0 margnwidth=0 marginheight=0 style='margin-right:0'>
	<center>
		<table border="0" cellpadding="0" cellspacing="0" width="560">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo $_COOKIE['kModo'] . " " . $_COOKIE['kProDes'] ?></legend>
						<form name='frgrm' action='fraesgra.php' method='post' target='fmpro'>
							<?php 
							$certificadoId = $_GET['ceridxxx'];
							$certiNomb = $_GET['cercscxx'];
							$cerAno = $_GET['ceranoxx'];
							$certiOb = $_GET['aesobsxx'];
							$nit = $_GET['cliidxxx'];
							$nomCliente = $_GET['clinomxx'];
							?>
							<input type="hidden" name="cStep" value="<?php echo $_POST['cStep'] ?>">
							<input type="hidden" name="nRecords" value="<?php echo $_POST['nRecords'] ?>">
							<input type="hidden" name="cCertiId" value="<?php echo $certificadoId ?>">
							<input type="hidden" name="cCerNomc" value="<?php echo $_GET['cercscxx'] ?>">
							<input type="hidden" name="cCerAno" value="<?php echo $cerAno ?>">
							<input type="hidden" name="cCerObser" value="<?php echo $certiOb ?>">
							<input type="hidden" name="nTimesSave" value="0">
							<textarea name="cComMemo" id="cComMemo"><?php echo $_POST['cComMemo'] ?></textarea>
							<script languaje="javascript">
								document.getElementById("cComMemo").style.display = "none";
							</script>
							<center>
								<fieldset id="Grid_Paso1">
									<legend>Datos Certificacion</legend>
									<table border='0' cellpadding='0' cellspacing='0' width='560'>
										<?php $nCol = f_Format_Cols(28); echo $nCol; ?>
										<tr>
											<td class="name" colspan="2">Nit<br>
												<input type="text" class="letra" name="cNit" style="width:100" value="<?php echo $nit ?>" readonly>
											</td>
											<td class="name" colspan="5">Cliente<br>
												<input type="text" class="letra" style="width:300;text-align:left" name="cNomCli" value="<?php echo $nomCliente ?>" readonly>
											</td>
											<td class="name" colspan="2">Certificacion<br>
												<input type="text" class="letra" name="cCerti" style="width:160" value="<?php echo $certiNomb ?>" readonly>
											</td>
										</tr>
									</table>
									<table border="0" cellspacing="0" cellpadding="0" width="560" id="tblFacA">
			</tr>
		</table>
		</fieldset>
		<?php
		switch ($_COOKIE['kModo']) {
			case "EDITAR":
			case "VER":
				$_POST['cSucId']  = $gSucId;
				$_POST['cDocId']  = $gDocId;
				$_POST['cDocSuf'] = $gDocSuf;
				break;
			default: //No hace nada 
				break;
		}

		$cAnio = $_GET['ceranoxx'];
		$vCerIds = $_GET['ceridxxx'];

		#CONSULTA CERTIFICADOS
		// Consulta la información de detalle de la certificación
		$qCertifiDet  = "SELECT ";
		$qCertifiDet .= "$cAlfa.lcca$cAnio.comidxxx, ";
		$qCertifiDet .= "$cAlfa.lcca$cAnio.comcodxx, ";
		$qCertifiDet .= "$cAlfa.lcca$cAnio.comprexx, ";
		$qCertifiDet .= "$cAlfa.lcca$cAnio.comcscxx, ";
		$qCertifiDet .= "$cAlfa.lcca$cAnio.comcsc2x, ";
		$qCertifiDet .= "$cAlfa.lcca$cAnio.cliidxxx, ";
		$qCertifiDet .= "$cAlfa.lcca$cAnio.comfecxx, ";
		$qCertifiDet .= "$cAlfa.lpar0150.clinomxx, ";
		$qCertifiDet .= "$cAlfa.lcde$cAnio.*, ";
		$qCertifiDet .= "$cAlfa.lpar0011.sersapxx, ";
		$qCertifiDet .= "$cAlfa.lpar0011.serdesxx ";
		$qCertifiDet .= "FROM $cAlfa.lcde$cAnio ";
		$qCertifiDet .= "LEFT JOIN $cAlfa.lcca$cAnio ON $cAlfa.lcde$cAnio.ceridxxx = $cAlfa.lcca$cAnio.ceridxxx ";
		$qCertifiDet .= "LEFT JOIN $cAlfa.lpar0011 ON $cAlfa.lcde$cAnio.sersapxx = $cAlfa.lpar0011.sersapxx ";
		$qCertifiDet .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lcca$cAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
		$qCertifiDet .= "WHERE ";
		$qCertifiDet .= "$cAlfa.lcde$cAnio.ceridxxx = $vCerIds";

		// Depuración
    //var_dump($qCertifiDet);
		$xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
		?>
		<script type="text/javascript">
			document.forms['frgrm']['cDocId'].readOnly = true;
			document.forms['frgrm']['cDocId'].onblur = "";
			document.forms['frgrm']['cDocId'].onfocus = "";
		</script>
		<?php
		if (mysql_num_rows($xCertifiDet) > 0) {
		?>
			<fieldset id="Tarifas">
				<legend>Servicios a Excluir</legend>
				<center>
					<table border="0" cellpadding="0" cellspacing="0" width="560">
						<tr bgcolor='<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
							<td class="clase08" width="060" style="padding-left:5px;padding-right:5px" align="center">Cod SAP</td>
							<td class="clase08" width="240" style="padding-left:5px;padding-right:5px" align="center">Servicio</td>
							<td class="clase08" width="240" style="padding-left:5px;padding-right:5px" align="center">Subservicio</td>
							<td class="clase08" width="020" style="padding-left:5px;padding-right:5px" align="center"><input type="checkbox" name="nCheckAll" onClick="javascript:f_Marca()"></td>
						</tr>
						<script languaje="javascript">
							document.forms['frgrm']['nRecords'].value = "<?php echo mysql_num_rows($xCertifiDet) ?>";
						</script>
						<?php
						$y = 0;
						while ($xRT = mysql_fetch_array($xCertifiDet)) {
						?>
							<tr>
								<td bgcolor="<?php echo $vSysStr['system_row_impar_color_ini'] ?>" class="letra7" style="padding-left:5px;padding-right:2px;border:1px solid #E6E6E6" align="center"><?php echo $xRT['sersapxx'] ?></td>
								<td bgcolor="<?php echo $vSysStr['system_row_impar_color_ini'] ?>" class="letra7" style="padding-left:5px;padding-right:2px;border:1px solid #E6E6E6"><?php echo $xRT['serdesxx'] ?></td>
								<td bgcolor="<?php echo $vSysStr['system_row_impar_color_ini'] ?>" class="letra7" style="padding-left:5px;padding-right:2px;border:1px solid #E6E6E6" align="center"><?php echo $xRT['subdesxx'] ?></td>
								<td bgcolor="<?php echo $vSysStr['system_row_impar_color_ini'] ?>" class="letra7" style="padding-left:5px;padding-right:2px;border:1px solid #E6E6E6" align="center">
									<input type="checkbox" name="cCheck" value="<?php echo mysql_num_rows($xTarifas) ?>" id="<?php echo $xRT['subidxxx'].'~'.$xRT['sersapxx'] ?>">
								</td>
							</tr>
						<?php $y++;
						} //while ($xRT = mysql_fetch_array($xTarifas)) { 
						?>
					</table>
				</center>
			</fieldset>
		<?php } else { //if(mysql_num_rows($xTarifas) > 0){
			f_Mensaje(__FILE__, __LINE__, "No hay Tarifas Parametrizadas para el certificado");
		}
		##Fin Traigo Tarifas parametrizadas al cliente para excluir Conceptos de Cobro al momento de facturar##
		?>
	</center>
	</form>
	</fieldset>
	</td>
	</tr>
	</table>
	</center>
	<center>
		<?php
		switch ($_COOKIE['kModo']) {
			case "EDITAR": ?>
				<table border="0" cellpadding="0" cellspacing="0" id='bnt_Paso2' width="560">
					<tr>
						<td width="378" height="21"></td>
						<td width="91" height="21" class="name">
							<input type="button" class="name" name="Btn_Guardar" value="Guardar" style="background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif);width:91;height:21;border:0px;" onclick="javascript:f_Carga_Data();
  								                      document.forms['frgrm'].target='fmpro';
  	      	                        		document.forms['frgrm'].action='fraesgra.php';
  																			document.forms['frgrm']['nTimesSave'].value++;
  																			document.forms['frgrm'].submit();">
						</td>
						<td width="91" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick="javascript:f_Retorna();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
						</td>
					</tr>
				</table>
			<?php break;
			default: ?>
				<table border="0" cellpadding="0" cellspacing="0" id='bnt_Paso2' width="560">
					<tr>
						<td width="469" height="21"></td>
						<td width="91" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick='javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
					</tr>
				</table>
		<?php break;
		}
		?>
	</center>
	<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
	<?php
	switch ($_COOKIE['kModo']) {
		case "EDITAR":
			f_CargaData();
	?>
	<script languaje = "javascript">
          document.forms['frgrm']['cCerti'].readOnly = true;
          document.forms['frgrm']['cCerti'].onblur = "";
          document.forms['frgrm']['cCerti'].onfocus = "";
        </script>
		<?php break;
		case "VER":
			f_CargaData();
		?>
		<script languaje = "javascript">
          document.forms['frgrm']['cCerti'].readOnly = true;
          document.forms['frgrm']['cCerti'].onblur = "";
          document.forms['frgrm']['cCerti'].onfocus = "";
          for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
          }
    </script>
	<?php break;
	} ?>

<?php
function f_CargaData()
{
    global $xConexion01;
    global $cAlfa;

    // Verifica si tiene asociacion por grupo
    $cAnio = $_GET['ceranoxx'];
    $vCerIds = $_GET['ceridxxx'];

    // Consulta la información de detalle de la certificación
    $qCertifiDet  = "SELECT ";
    $qCertifiDet .= "$cAlfa.lcca$cAnio.comidxxx, ";
    $qCertifiDet .= "$cAlfa.lcca$cAnio.comcodxx, ";
    $qCertifiDet .= "$cAlfa.lcca$cAnio.comprexx, ";
    $qCertifiDet .= "$cAlfa.lcca$cAnio.comcscxx, ";
    $qCertifiDet .= "$cAlfa.lcca$cAnio.comcsc2x, ";
    $qCertifiDet .= "$cAlfa.lcca$cAnio.cliidxxx, ";
    $qCertifiDet .= "$cAlfa.lcca$cAnio.comfecxx, ";
    $qCertifiDet .= "$cAlfa.lpar0150.clinomxx, ";
    $qCertifiDet .= "$cAlfa.lcde$cAnio.*, ";
    $qCertifiDet .= "$cAlfa.lpar0011.sersapxx, ";
    $qCertifiDet .= "$cAlfa.lpar0011.serdesxx ";
    $qCertifiDet .= "FROM $cAlfa.lcde$cAnio ";
    $qCertifiDet .= "LEFT JOIN $cAlfa.lcca$cAnio ON $cAlfa.lcde$cAnio.ceridxxx = $cAlfa.lcca$cAnio.ceridxxx ";
    $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0011 ON $cAlfa.lcde$cAnio.sersapxx = $cAlfa.lpar0011.sersapxx ";
    $qCertifiDet .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lcca$cAnio.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
    $qCertifiDet .= "WHERE $cAlfa.lcde$cAnio.ceridxxx = $vCerIds";

		$xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");

		
    // Consulta para obtener los registros guardados en la tabla lpar0160
    $qLpar0160 = "SELECT ";
		$qLpar0160 .= "$cAlfa.lpar0160.sersapxx, ";
		$qLpar0160 .= "$cAlfa.lpar0160.subidxxx, ";
		$qLpar0160 .= "$cAlfa.lpar0160.ceridxxx ";
		$qLpar0160 .= "FROM $cAlfa.lpar0160 ";
		$qLpar0160 .= "WHERE $cAlfa.lpar0160.ceridxxx = $vCerIds";
    $xLpar0160 = f_MySql("SELECT","",$qLpar0160,$xConexion01,"");


    // Crear un array para almacenar los resultados de xLpar0160
    $lpar0160Data = [];
    while ($row = mysql_fetch_array($xLpar0160)) {
        $lpar0160Data[] = $row;
    }
		
    // Almacena los IDs de los checkboxes a marcar en un array
    $checkboxIdsToCheck = [];
    while ($xRT = mysql_fetch_array($xCertifiDet)) {
			
        foreach ($lpar0160Data as $lparRow) {
						
            if ($xRT['subidxxx'] == $lparRow['subidxxx']) {
	
                $checkboxIdsToCheck[] = $xRT['subidxxx'].'~'.$xRT['sersapxx'];
            }
        }
    }
    ?>
    <script>
			window.onload = function() {
					<?php
					foreach ($checkboxIdsToCheck as $checkboxId) {
							?>
							var checkbox = document.getElementById('<?php echo $checkboxId; ?>');
							if (checkbox) {
									checkbox.checked = true;
							}
							<?php
					}
					?>
			};
		</script>
    <?php
}
?>
</body>

</html>
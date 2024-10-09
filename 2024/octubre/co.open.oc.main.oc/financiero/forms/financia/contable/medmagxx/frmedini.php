<?php
  namespace openComex;
	/**
	 * Imprime Reporte de Medios Magneticos.
	 * --- Descripcion: Permite Imprimir Reporte de Medios Magneticos.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 * @version 002
	 */

	include("../../../../libs/php/utility.php");
	//f_Mensaje(__FILE__,__LINE__,$_COOKIE['kModo']);

	$dHoy = date('Y-m-d');

  $qSysProbg = "SELECT * ";
  $qSysProbg .= "FROM $cBeta.sysprobg ";
  $qSysProbg .= "WHERE ";
  $qSysProbg .= "DATE(regdcrex) =\"$dHoy\" AND ";
  $qSysProbg .= "regusrxx = \"$kUser\" AND ";
  $qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
  $qSysProbg .= "pbamodxx = \"FACTURACION\" AND ";
  $qSysProbg .= "pbatinxx = \"MEDIOSMAGNETICOS\" ";
  $qSysProbg .= "ORDER BY regdcrex DESC";
  $xSysProbg = f_MySql("SELECT", "", $qSysProbg, $xConexion01, "");

  $mArcProBg = array();

  while ($xRB = mysql_fetch_array($xSysProbg)) {
    $vArchivos = explode("~", trim($xRB['pbaexcxx'], "~"));
    for ($nA = 0; $nA < count($vArchivos); $nA++) {
      $nInd_mArcProBg = count($mArcProBg);
      $cRuta = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $vArchivos[$nA];
      if ($vArchivos[$nA] != "" && file_exists($cRuta)) {
        $mArcProBg[$nInd_mArcProBg]['pbaexcxx'] = $vArchivos[$nA];
      } else {
        $mArcProBg[$nInd_mArcProBg]['pbaexcxx'] = "";
      }

      $mArcProBg[$nInd_mArcProBg]['pbaidxxx'] = $xRB['pbaidxxx'];
      $mArcProBg[$nInd_mArcProBg]['regunomx'] = $xRB['regunomx'];

      $mArcProBg[$nInd_mArcProBg]['pbarespr'] = $xRB['pbarespr'];
      $mArcProBg[$nInd_mArcProBg]['pbaerrxx'] = $xRB['pbaerrxx'];
      $mArcProBg[$nInd_mArcProBg]['regestxx'] = ($xRB['regdinix'] != "0000-00-00 00:00:00" && $xRB['regdfinx'] == "0000-00-00 00:00:00") ? "EN PROCESO" : $xRB['regestxx'];

      $mPost = f_Explode_Array($xRB['pbapostx'], "|", "~");
      for ($nP = 0; $nP < count($mPost); $nP++) {
        if ($mPost[$nP][0] != "") {
          $mArcProBg[$nInd_mArcProBg][$mPost[$nP][0]] = $mPost[$nP][1];
        }
      }
    }
	}
	
?>
<html>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script language="javascript">
			function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				parent.fmwork.location='<?php echo $cPlesk_Forms_Directory ?>/frproces.php';
				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			}

			function fnRecargar() {
				parent.fmwork.location="<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>";
			}

			function f_Links(xLink,xSwitch,xSecuencia,xGrid,xType) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
					case "cPucId":
						var zNx     = (nX-550)/2;
						var zNy     = (nY-250)/2;
						var zWinPro = 'width=550,scrollbars=1,height=250,left='+zNx+',top='+zNy;
						var zRuta  = "frmed115.php?gWhat=WINDOW&gFunction=cPucId&gPucId="+document.forms['frgrm']['cPucId'+xSecuencia].value.toUpperCase()+
												"&nSecuencia="+xSecuencia;
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
					break;
				}
			}

			function chDate(fld){
				var val = fld.value;
				if (val.length > 0){
					var ok = 1;
					if (val.length < 10){
						alert('Formato de Fecha debe ser aaaa-mm-dd');
						fld.value = '';
						fld.focus();
						ok = 0;
					}
					if(val.substr(4,1) == '-' && val.substr(7,1) == '-' && ok == 1){
						var anio = val.substr(0,4);
						var mes  = val.substr(5,2);
						var dia  = val.substr(8,2);
						if (mes.substr(0,1) == '0'){
							mes = mes.substr(1,1);
						}
						if (dia.substr(0,1) == '0'){
							dia = dia.substr(1,1);
						}
						
						if(mes > 12){
							alert('El mes debe ser menor a 13');
							fld.value = '';
							fld.focus();
						}
						if (dia > 31){
							alert('El dia debe ser menor a 32');
							fld.value = '';
							fld.focus();
						}
						var aniobi = 28;
						if(anio % 4 ==  0){
							aniobi = 29;
						}
						if (mes == 4 || mes == 6 || mes == 9 || mes == 11){
							if (dia < 1 || dia > 30){
								alert('El dia debe ser menor a 31, dia queda en 30');
								fld.value = val.substr(0,8)+'30';
							}
						}
						if (mes == 1 || mes == 3 || mes == 5 || mes == 7 || mes == 8 || mes == 10 || mes == 12){
							if (dia < 1 || dia > 32){
								alert('El dia debe ser menor a 32');
								fld.value = '';
								fld.focus();
							}
						}
						if(mes == 2 && aniobi == 28 && dia > 28 ){
							alert('El dia debe ser menor a 29');
							fld.value = '';
							fld.focus();
						}
						if(mes == 2 && aniobi == 29 && dia > 29){
							alert('El dia debe ser menor a 30');
							fld.value = '';
							fld.focus();
						}
					}else{
						if(val.length > 0){
							alert('Fecha erronea, verifique');
						}
						fld.value = '';
						fld.focus();
					}
				}
			}

			function f_Last_Button() {
				var cGrid = document.getElementById("Grid_Cuenta");
				var nLastRow = cGrid.rows.length;
				for (i=1;i<=nLastRow;i++) {
					var cRow = document.getElementById("oBtnDel" + i);
					if (i < nLastRow) {
						cRow.value = "";
					} else {
						cRow.value = "X";
					}
				}
			}

			function f_Enter(e,xName) {
				var code;
				if (!e) {
					var e = window.event;
				}
				if (e.keyCode) {
					code = e.keyCode;
				} else {
					if (e.which) {
						code = e.which;
					}
				}
				if (code == 13){
					if (xName == "nPucDes"+eval(document.forms['frgrm']['nSecuencia'].value)) {
						f_Add_New_Row_Cuenta();
					}
				}
			}

			function f_Add_New_Row_Cuenta() {
				var cGrid      = document.getElementById("Grid_Cuenta");
				var nLastRow   = cGrid.rows.length;
				var nSecuencia = nLastRow+1;
				var cTableRow  = cGrid.insertRow(nLastRow);

				var cPucId   = 'cPucId'   + nSecuencia;	// Nro de Cuenta
				var cPucDes  = 'cPucDes'  + nSecuencia;	// Desccripcion de la Cuenta
				var oBtnDel  = 'oBtnDel'  + nSecuencia; // Boton de Borrar Row

				var TD_xAll = cTableRow.insertCell(0);
				TD_xAll.innerHTML = "<input type = 'text'   Class = 'letra' style = 'width:080' name = "+cPucId+" onBlur = 'javascript:f_Links(\"cPucId\",\"VALID\",\""+nSecuencia+"\")'>"+
														"<input type = 'text'   Class = 'letra' style = 'width:500' name = "+cPucDes+" readonly>"+
														"<input type = 'button' Class = 'letra' style = 'width:020' id = "+oBtnDel+" value = '..' onClick = 'javascript:f_Delete_Row(this.value,\"oBtnDel\")'>";

				document.forms['frgrm']['nSecuencia'].value = nSecuencia;
				f_Last_Button();
			}

			function f_Delete_Row(xNumRow) {
				var cGrid = document.getElementById("Grid_Cuenta");
				var nLastRow = cGrid.rows.length;
				if (nLastRow > 1 && xNumRow == "X"){
					if (confirm("Realmente Desea Eliminar la Secuencia?")){
						cGrid.deleteRow(nLastRow - 1);
						document.forms['frgrm']['nSecuencia'].value = nLastRow - 1;
						f_Last_Button();
					}
				} else {
					alert("No se Pueden Eliminar Todas las Secuencias del Comprobante, Verifique.");
				}
			}

			function f_GenSql()  {
			
				//Validaciones
				var nSwicht = 0;
				var cMen = "\n";
				
				switch(document.forms['frgrm']['cFormato'].value) {
					case "1001": 
					case "1003": 
					case "1005": 
					case "1006": 
					case "1007": 
					case "1016":
					case "1054":
					case "5247": 
					case "5248": 
					case "5249": 
					case "5250": 

						if(document.forms['frgrm']['dDesde'].value == "" || document.forms['frgrm']['dHasta'].value == ""){
							nSwicht = 1;
							cMen += "Debe Seleccionar un Rango de Fechas.\n";
						}
					
						if((Date.parse(document.forms['frgrm']['dDesde'].value)) > (Date.parse(document.forms['frgrm']['dHasta'].value))){
							nSwitch = 1;
							cMen += "La Fecha Inicial no puede ser mayor a la Fecha Final.\n";
						}
					
						if(document.forms['frgrm']['dDesde'].value.substr(0,4) != document.forms['frgrm']['dHasta'].value.substr(0,4)){
							cMen += "El Rango de Fechas Debe Estar Dentro del Mismo A\u00f1o.\n";
							nSwitch = 1;
						} 
					break;
					case "1008":
					case "1009":
					case "1012":
					case "1018":
					case "1027":
					case "5251": 
					case "5252": 

						if(document.forms['frgrm']['dHastaC'].value == ""){
							nSwicht = 1;
							cMen += "Debe Seleccionar una Fecha de Corte.\n";
						}
						document.forms['frgrm']['dDesde'].value = document.forms['frgrm']['dHastaC'].value;
					break;
					default:
						nSwicht = 1;
						cMen += "Debe Seleccionar un Formato.\n";
					break;
				}
			
				if(document.forms['frgrm']['nSecuencia'].value == ''){
					nSwicht = 1;
					cMen += "Debe Seleccionar una Cuenta.\n";
				}else{
					for(i=1;i<=document.forms['frgrm']['nSecuencia'].value;i++){
						if(document.forms['frgrm']['cPucId'+i].value == ''){
							nSwicht = 1;
							cMen += "Debe Seleccionar una Cuenta en la secuencia " + i + ".\n";
						}
					}
				}
				
				if(nSwicht == 0){
					var cTipo = 0;
					for (i=0;i<2;i++){
						if (document.forms['frgrm']['rTipo'][i].checked == true){
							cTipo = i+1;
							break;
						}
					}

          if (cTipo != 2) {
            document.forms['frgrm']['cEjProBg'].checked = false;
            document.forms['frgrm']['cEjProBg'].value = "NO";
          }
									
					var cRuta = 'frmedprn.php';
					if(cTipo == 2){
						var indiceF = document.frgrm.cFormato.selectedIndex;
						document.forms['frgrm']['cFormatoDes'].value = document.forms.frgrm['cFormato'].options[indiceF].text;
						
						document.forms['frgrm'].target='fmpro'; 
						document.forms['frgrm'].action=cRuta; 
						document.forms['frgrm'].submit();
					}else{
						var zX      = screen.width;
						var zY      = screen.height;
						var zNx     = (zX-30)/2;
						var zNy     = (zY-100)/2;
						var zNy2    = (zY-100);
						var zWinPro = "width="+zX+",scrollbars=1,height="+zNy2+",left="+1+",top="+50;
						var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
						zWindow = window.open('',cNomVen,zWinPro);

						document.forms['frgrm'].target=cNomVen; 
						document.forms['frgrm'].action=cRuta; 
						document.forms['frgrm'].submit();
					}
				}else{
					alert(cMen+"Verifique.");
				} 
			}
		
			function fnMostrar(xValor) {
				switch(xValor) {
					case "1001": 
					case "1003": 
					case "1005": 
					case "1006": 
					case "1007": 
					case "1016":
					case "1054":
					case "5247": 
					case "5248": 
					case "5249": 
					case "5250": 

						document.getElementById("tblRango").style.display="block";
						document.getElementById("tblCorte").style.display="none"; 
					break;
					case "1008":
					case "1009":
					case "1012":
					case "1018":
					case "1027": 
					case "5251": 
					case "5252": 

						document.getElementById("tblRango").style.display="none";
						document.getElementById("tblCorte").style.display="block";
					break;
					default:
						document.getElementById("tblRango").style.display="none";
						document.getElementById("tblCorte").style.display="none";
					break;
				}
			}

			function fnHabilitarProBg(cTipo){
        if(cTipo == 2 || cTipo == 3){
          document.getElementById('EjProBg').style.display = '';
        } else{
          document.forms['frgrm']['cEjProBg'].checked = false;
          document.forms['frgrm']['cEjProBg'].value = "NO";
          document.getElementById('EjProBg').style.display = 'none';
        }
      }

			function fnDescargar(xArchivo){
        parent.fmwork.location = "frgendoc.php?cRuta="+xArchivo;
      }

			function fnVerFiltros(xPbaId){
				w=550;
     	  h=250;
     	  LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
        TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
        settings ='height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars=YES,resizable'
        var cRuta = 'frmedfil.php?gPbaId=' + xPbaId;
  		 	zWin = window.open(cRuta,'Ver Filtros',settings);
  		 	zWin.focus();
			}

		</script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
	<center>
		<table border ="0" cellpadding="0" cellspacing="0" width="600">
			<tr>
				<td>
					<form name='frgrm' action='frmedprn.php' method="POST">
						<input type = "hidden" name = "nSecuencia"  value = "0">
						<fieldset>
							<legend>Informe de Medios Magneticos</legend>
							<center>
								<table border="2" cellspacing="0" cellpadding="0" width="600">
									<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
										<td class="name"><center><h5><br>INFORME DE MEDIOS MAGNETICOS</h5></center></td>
									</tr>
								</table>
								<table border = '0' cellpadding = '0' cellspacing = '0' width='600'>
									<?php $nCol = f_Format_Cols(30);
									echo $nCol;?>
									<tr>
										<td class="name" colspan = "5"><br>Desplegar en:
										</td>
										<td class="name" colspan = "5"><br>
											<input type="radio" name="rTipo" value="1" checked onclick="fnHabilitarProBg(this.value)">Pantalla
										</td>
										<td class="name" colspan = "20"><br>
											<input type="radio" name="rTipo" value="2" onclick="fnHabilitarProBg(this.value)">Excel
										</td>
									</tr>
								</table>
								<table border = '0' cellpadding = '0' cellspacing = '0' width='600'>
									<?php $nCol = f_Format_Cols(30);
									echo $nCol;?>
									<tr>
										<td class="name" colspan = "3"><br>Formato:</td>
										<td class="name" colspan = "27"><br>
											<input type="hidden" name="cFormatoDes" value="">
											<select Class = "letrase" name = "cFormato" id="cFormato" style = "width:540" onchange="javascript:fnMostrar(this.value)">
												<option value = '1001' selected>1001 - PAGOS O ABONOS EN CUENTAS Y RETENCIONES PRACTICADAS</option>
												<option value = '1003'>1003 - RETENCIONES EN LA FUENTE QUE LE PRACTICARON</option>
												<option value = '1005'>1005 - IMPUESTO A LAS VENTAS (DESCONTABLE)</option>
												<option value = '1006'>1006 - IMPUESTO A LAS VENTAS POR PAGAR (GENERADO)</option>
												<option value = '1007'>1007 - INGRESOS RECIBIDOS</option>
												<option value = '1008'>1008 - SALDO DE CUENTAS POR COBRAR AL 31 DE DICIEMBRE</option>
												<option value = '1009'>1009 - SALDO DE CUENTAS POR PAGAR AL 31 DE DICIEMBRE</option>
												<option value = '1012'>1012 - INFORMACI&Oacute;N DE DECLARACIONES TRIBUTARIAS, ACCIONES, INVERSIONES EN BONOS T&Iacute;TULOS VALORES Y CUENTAS DE AHORRO Y CUENTAS CORRIENTES</option>
												<option value = '1016'>1016 - PAGOS O ABONOS EN CUENTAS Y RETENCIONES PRACTICADAS EN CONTRATOS DE MANDATO O ADMINISTRACI&Oacute;N DELEGADA</option>
												<option value = '1018'>1018 - CUENTAS POR COBRAR EN CONTRATOS DE MANDATO O ADMINISTRACION DELEGADA</option>
												<option value = '1027'>1027 - CUENTAS POR PAGAR EN CONTRATOS DE MANDATO O ADMINISTRACION DELEGADA</option>
												<option value = '1054'>1054 - IMPUESTO A LAS VENTAS POR PAGA (DESCONTABLE) EN CONTRATOS DE MANDATO O DE ADMINISTRACI&Oacute;N DELEGADA</option>
												<option value = '' disabled="disabled">==========================================================================================================================================</option>
												<option value = '5247'>5247 - PAGOS O RETENCIONES</option>
												<option value = '5248'>5248 - INGRESOS</option>
												<option value = '5249'>5249 - IVA DESCONTABLE</option>
												<option value = '5250'>5250 - IVA GENERADO</option>
												<option value = '5251'>5251 - CUENTAS POR COBRAR</option>
												<option value = '5252'>5252 - CUENTAS POR PAGAR</option>

											</select>
										</td>
									</tr>
									<tr>
										<td class="name" colspan = "30">
											<table border = '0' cellpadding = '0' cellspacing = '0' width='600' id="tblRango">
												<?php $nCol = f_Format_Cols(30);
												echo $nCol;?> 
												<td class="name" colspan = "10"><br>
													Rango De Fechas (Fecha Doc.):
												</td>
												<td class="name" colspan = "3"><br><center><a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">De</a></center></td>
												<td class="name" colspan = "7"><br>
													<input type="text" name="dDesde" style = "width:140;text-align:center"
														onblur="javascript:chDate(this);">
												</td>
												<td class="name" colspan = "3"><br><center><a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">A</a></center></td>
												<td class="name" colspan = "7"><br>
													<input type="text" name="dHasta" style = "width:140;text-align:center"
														onblur="javascript:chDate(this);">
												</td>
											</table>
											<table border = '0' cellpadding = '0' cellspacing = '0' width='600' id="tblCorte">
												<?php $nCol = f_Format_Cols(30);
												echo $nCol;?> 
												<td class="name" colspan = "9"><br>
													<a href='javascript:show_calendar("frgrm.dHastaC")' id="id_href_dHastaC">Fecha de Corte (Fecha Doc.):</a>
												</td>
												<td class="name" colspan = "7"><br>
													<input type="text" name="dHastaC" style = "width:140;text-align:center"
														onblur="javascript:chDate(this);">
												</td>
												<td class="name" colspan = "14"></td>
											</table>
										</td>
									</tr>
									</table>
								</center>
								
								<fieldset id="Id_Cuentas">
									<legend><b>Cuentas</b></legend>
									<br>
									<center>
										<table border = "0" cellpadding = "0" cellspacing = "0" width = "600">
											<td width = "080"  class = "name"><center>Cuenta</center></td>
											<td width = "500"  class = "name"><center>Descripcion</center></td>
											<td width = "020"  class = "name" align = "right"></td>
										</table>
										<table border = "0" cellpadding = "0" cellspacing = "0" width = "600" id = "Grid_Cuenta"></table>
									</center>
								</fieldset>
								<table>
									<tr id="EjProBg" style="display: none">
										<td Class = "name" colspan = "25"><br>
											<label><input type="checkbox" name="cEjProBg" value ="SI" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}" checked>Ejecutar Proceso en Background</label>
										</td>
									</tr> 
								</table>
							</fieldset>
						<center>
							<table border="0" cellpadding="0" cellspacing="0" width="600">
								<tr height="21">
									<td width="418" height="21"></td>
									<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:f_GenSql()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
									<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
								</tr>
							</table>
						</center>
						<script languaje = "javascript">
							f_Add_New_Row_Cuenta();
							fnMostrar('1001');
						</script>
					</form>
				</td>
			</tr>
		</table>
	</center>
	<?php if(count($mArcProBg) > 0){ ?>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" width="620">
        <tr>
          <td Class = "name" colspan = "19"><br>
            <fieldset>
              <legend>Reportes Generados. Fecha [<?php echo date('Y-m-d'); ?>]</legend>
              <label>
                <table border="0" cellspacing="1" cellpadding="0" width="620">
                  <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="height:20px">
                    <td align="center"><strong>Usuario</strong></td>
                    <td align="center"><strong>Filtros</strong></td>
                    <td align="center"><strong>Resultado</strong></td>
										<td align="center"><strong>Estado</strong></td>
                    <td align="center"><img src = "<?php echo $cPlesk_Skin_Directory ?>/b_cambest.gif" onClick = "javascript:fnRecargar()" style = "cursor:pointer" title="Recargar"></td>
                  </tr>
                  <?php for ($i = 0; $i < count($mArcProBg); $i++) {
                    $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                    if($i % 2 == 0) {
                      $cColor = "{$vSysStr['system_row_par_color_ini']}";
                    }
                    ?>
                  <tr bgcolor = "<?php echo $cColor ?>">
                    <td style="padding:2px"><?php echo $mArcProBg[$i]['regunomx']; ?></td>
										<td style="padding:2px"><?php echo ($mArcProBg[$i]['dHastaC'] == "") ? 
																												'<strong>Rango de Fechas: </strong>'.$mArcProBg[$i]['dDesde']." A ".$mArcProBg[$i]['dHasta'].'<br>' : 
																												'<strong>Fecha de Corte: </strong>'.$mArcProBg[$i]['dHastaC'].'<br>'; 
																									echo '<strong>Formato: </strong>'.$mArcProBg[$i]['cFormatoDes'].'<br>'; 
																									echo '<strong>Cuentas: </strong>' ?><a href = "javascript:fnVerFiltros('<?php echo $mArcProBg[$i]['pbaidxxx']; ?>')" title="Ver Cuentas">Ver</a></td>
                    <td style="padding:2px"><?php echo $mArcProBg[$i]['pbarespr']; ?></td>
                    <td style="padding:2px"><?php echo $mArcProBg[$i]['regestxx']; ?></td>
                    <td>
                      <?php if ($mArcProBg[$i]['pbaexcxx'] != "") { ?>
                        <a href = "javascript:fnDescargar('<?php echo $mArcProBg[$i]['pbaexcxx']; ?>')">
                          Descargar
                        </a>
                      <?php } ?>
                      <?php if ($mArcProBg[$i]['pbaerrxx'] != "") { ?>
                        <a href = "javascript:alert('<?php echo str_replace(array("<br>","'",'"'),array("\n"," "," "),$mArcProBg[$i]['pbaerrxx']) ?>')">
                          Ver
                        </a>
                      <?php } ?>
                    </td>
                  </tr>
                  <?php } ?>
                </table>
              </label>
            </fieldset>
          </td>
        </tr>
      </table>
    </center>
  <?php } ?>
	</body>
</html>
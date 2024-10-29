<?php
  namespace openComex;
	/**
	* Imprime Estado de Cuenta.
	* --- Descripcion: Permite Imprimir Estado de Cuenta(por Cobrar / por Pagar).
	* @author Hernan Gordillo <hernang@repremundo.com.co>
	* @version 002
	*/

	include("../../../../libs/php/utility.php");

$dHoy = date('Y-m-d');

$qSysProbg = "SELECT * ";
$qSysProbg .= "FROM $cBeta.sysprobg ";
$qSysProbg .= "WHERE ";
$qSysProbg .= "DATE(regdcrex) =\"$dHoy\" AND ";
$qSysProbg .= "regusrxx = \"$kUser\" AND ";
$qSysProbg .= "pbadbxxx = \"$cAlfa\" AND ";
$qSysProbg .= "pbamodxx = \"FACTURACION\" AND ";
$qSysProbg .= "pbatinxx = \"ESTADOCUENTA\" ";
$qSysProbg .= "ORDER BY regdcrex DESC";
$xSysProbg = f_MySql("SELECT", "", $qSysProbg, $xConexion01, "");

// f_Mensaje(__FILE__,__LINE__,$qSysProbg."~".mysql_num_rows($xSysProbg));

// $xRB = mysql_fetch_array($xSysProbg);

// echo "<pre>";
// var_dump($xRB);

// die();


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

		if ($xRB['regestxx'] != "INACTIVO") {
			$nTieEst = round(((strtotime(date('Y-m-d H:i:s')) - strtotime($xRB['regdinix'])) / ($xRB['pbatxixx'] * $xRB['pbacrexx'])), 2) . "&#37";
		} else {
			$nTieEst = "";
		}
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
				  case "cTerId":
					case "cTerNom":
					  if (xLink == "cTerId" || xLink == "cTerNom") {
					    var cTerId  = document.forms['frgrm']['cTerId'].value.toUpperCase();
					    var cTerNom = document.forms['frgrm']['cTerNom'].value.toUpperCase();
					  }
						if (xSwitch == "VALID") {
							var cPathUrl = "fresc150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gTerId="+cTerId+
																				"&gTerNom="+cTerNom;
							//alert(cPathUrl);
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-600)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "fresc150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				 "&gTerId="+cTerId+
																				 "&gTerNom="+cTerNom;
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
				  		cWindow.focus();
						}
					break;
				}
	    }

			// FUNCION DE SELECT PARA CONSULTA //
			function f_GenSql()  {
			  var cTipo = 0;
			  var cTipCta = "";
			  for (i=0;i<3;i++){
			    if (document.forms['frgrm']['rTipo'][i].checked == true){
			  	  cTipo = i+1;
			      break;
			    }
			  }

			  if(document.forms['frgrm']['rTipCta'][0].checked == true){
			   cTipCta = "PAGAR";
			  }else{
			   cTipCta = "COBRAR";
			  }

        if (cTipo != 2) {
          document.forms['frgrm']['cEjProBg'].checked = false;
          document.forms['frgrm']['cEjProBg'].value = "NO";
        }

			  if(cTipo == 2){
					var zRuta = 'frescprn.php?cTipo='			+cTipo+
																	'&cTipoCta='	+cTipCta+
																	'&cTerId='		+document.forms['frgrm']['cTerId'].value+
																	'&cEjProBg=' 	+document.forms['frgrm']['cEjProBg'].value;

			  	parent.fmpro.location = zRuta;
			  }else{
	 				var zX      = screen.width;
  				var zY      = screen.height;
  				var zNx     = (zX-30)/2;
  				var zNy     = (zY-100)/2;
  				var zNy2    = (zY-100);
  				var zWinPro = "width="+zX+",scrollbars=1,height="+zNy2+",left="+1+",top="+50;
					var zRuta = 'frescprn.php?cTipo='			+document.forms['frgrm']['rTipo'][i].value+
																	'&cTipoCta='	+cTipCta+
																	'&cTerId='		+document.forms['frgrm']['cTerId'].value;
					var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
  				zWindow = window.open(zRuta,cNomVen,zWinPro);
  				zWindow.focus();
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
	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="500">
				<tr>
					<td>
            <form name='frgrm' action='rpsdi1.php' method="POST">
              <center>
          	    <fieldset>
          		    <legend>Consulta Estado de Cuenta </legend>
          		    <table border="2" cellspacing="0" cellpadding="0" width="500">
          		     	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
          			     	<td class="name" width="30%"><center><h5><br>REPORTE DE ESTADO DE CUENTAS</h5></center></td>
          			    </tr>
          			  </table>
          			  <table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
  							 		<?php $nCol = f_Format_Cols(25);
  							 		echo $nCol;?>
          			    <tr>
          	          <td class="name" colspan = "5"><br>Desplegar en:
          			      </td>
          			      <td class="name" colspan = "7"><br>
          	            <input type="radio" name="rTipo" value="1" checked onclick="fnHabilitarProBg(this.value)">Pantalla
          	          </td>
          	          <td class="name" colspan = "7"><br>
          			         <input type="radio" name="rTipo" value="2" onclick="fnHabilitarProBg(this.value)">Excel
          			      </td>
          			      <td class="name" colspan = "6"><br>
          			        <input type="radio" name="rTipo" value="3" onclick="fnHabilitarProBg(this.value)">Pdf<br>
          			      </td>
          	        </tr>
          	       <!-- <tr>
          	          <td class="name" colspan = "3"><br>Mes &nbsp;:
          			      </td>
          			      <td class="name" colspan = "5"><br>
          	            <select Class = "letrase" name = "cMes" style = "width:100">
              					  <option value = "" selected>-- SELECCIONE --</option>
              						<option value = '01'>ENERO</option>
              						<option value = '02'>FEBRERO</option>
              						<option value = '03'>MARZO</option>
              						<option value = '04'>ABRIL</option>
              						<option value = '05'>MAYO</option>
              						<option value = '06'>JUNIO</option>
              						<option value = '07'>JULIO</option>
              						<option value = '08'>AGOSTO</option>
              						<option value = '09'>SEPTIEMBRE</option>
              						<option value = '10'>OCTUBRE</option>
              						<option value = '11'>NOVIEMBRE</option>
              						<option value = '12'>DICIEMBRE</option>
          					    </select>
          			      </td>
          			      <td class="name" colspan = "2"><br>
          			      </td>
          			      <td class="name" colspan = "3"><br>Anio &nbsp;:
          			      </td>
          			      <td class="name" colspan = "5"><br>
          	            <select Class = "letrase" name = "cAnio" style = "width:100">
              					  <option value = "" selected>-- SELECCIONE --</option>
              						<option value = '2008'>2008</option>
              						<option value = '2009'>2009</option>
              						<option value = '2010'>2010</option>
          					    </select>
          			      </td>
          	        </tr>
          	        <tr>
          	          <td class="name" colspan = "3"><br>Mostrar Act/Inact/Todos &nbsp;:
          			      </td>
          			      <td class="name" colspan = "15"><br>
          	            <select Class = "letrase" name = "cEstado" style = "width:100">
              					  <option value = "" selected>-- SELECCIONE --</option>
              						<option value = 'ACTIVO'>ACTIVO</option>
              						<option value = 'INACTIVO'>INACTIVO</option>
              						<option value = 'ALL'>TODOS</option>
          					    </select>
          			      </td>
          	        </tr>-->
										<tr>
											<td class="name" colspan = "12"><br>
													<input type="radio" name="rTipCta" value="PAGAR" checked>Cuentas Por Pagar
												</td>
												<td class="name" colspan = "13"><br>
													<input type="radio" name="rTipCta" value="COBRAR">Cuentas por Cobrar
												</td>
										</tr>
          	       	<tr>
											<td Class = "name" colspan = "5">
												<a href = "javascript:document.forms['frgrm']['cTerId'].value  = '';
																							document.forms['frgrm']['cTerNom'].value = '';
																							document.forms['frgrm']['cTerDV'].value  = '';
																							f_Links('cTerId','VALID')" id="id_href_cTerId"><br>Nit</a><br>
												<input type = "text" Class = "letra" style = "width:100;text-align:center" name = "cTerId"
													onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
																							document.forms['frgrm']['cTerNom'].value = '';
																							document.forms['frgrm']['cTerDV'].value  = '';
																							this.style.background='#00FFFF'"
													onBlur = "javascript:this.value=this.value.toUpperCase();
																							f_Links('cTerId','VALID');
																							this.style.background='#FFFFFF'">
											</td>
											<td Class = "name" colspan = "1"><br>Dv<br>
												<input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDV" readonly>
											</td>
											<td Class = "name" colspan = "19"><br>Cliente<br>
												<input type = "text" Class = "letra" style = "width:380" name = "cTerNom"
													onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
																							document.forms['frgrm']['cTerNom'].value = '';
																							document.forms['frgrm']['cTerDV'].value  = '';
																							this.style.background='#00FFFF'"
													onBlur = "javascript:this.value=this.value.toUpperCase();
																							f_Links('cTerNom','VALID');
																							this.style.background='#FFFFFF'">
											</td>
          	       	</tr>
										<tr id="EjProBg" style="display: none">
											<td Class = "name" colspan = "25"><br>
												<label><input type="checkbox" name="cEjProBg" value ="SI" onclick="javascript:if(this.checked == true) { this.value = 'SI'} else { this.value = 'NO'}" checked>Ejecutar Proceso en Background</label>
											</td>
										</tr>
          		    </table>
          		  </fieldset>
                <center>
          				<table border="0" cellpadding="0" cellspacing="0" width="500">
            				<tr height="21">
            					<td width="318" height="21"></td>
            					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:f_GenSql()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Buscar</td>
            					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
          				  </tr>
          				</table>
          			</center>
          	  </form>
					</td>
				</tr>
		 	</table>
		</center>
		<?php if(count($mArcProBg) > 0){ ?>
			<center>
				<table border="0" cellpadding="0" cellspacing="0" width="500">
					<tr>
						<td Class = "name" colspan = "19"><br>
							<fieldset>
								<legend>Reportes Generados. Fecha [<?php echo date('Y-m-d'); ?>]</legend>
								<label>
									<table border="0" cellspacing="1" cellpadding="0" width="500">
										<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="height:20px">
											<td align="center"><strong>Usuario</strong></td>
											<td align="center"><strong>Par&aacute;metross</strong></td>
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
											
											<td>
												<?php 
												if($mArcProBg[$i]['cTipoCta'] != ""){?>
													<?php echo "<strong>&raquo; Cuenta:</strong> CUENTA POR ". $mArcProBg[$i]['cTipoCta']."<br>";
												}
												?>

												<?php 
												if($mArcProBg[$i]['cTerId'] != ""){?>
													<?php echo "<strong>&raquo; Cliente:</strong> ". $mArcProBg[$i]['cTerId']."<br>"; 
												}
												?>
											</td>

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

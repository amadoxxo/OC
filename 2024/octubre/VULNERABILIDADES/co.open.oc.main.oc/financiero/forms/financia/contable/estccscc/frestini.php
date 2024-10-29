<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  $mMeses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
  $cAno = substr(date('Y-m-d'),0,4);
	$cMes = substr(date('Y-m-d'),5,2);
?>
<html>
	<head>
		<title>Reportes</title>
	  <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>

		<script languaje = 'javascript'>
			function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				parent.fmwork.location='<?php echo $cPlesk_Forms_Directory ?>/frproces.php';
				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			}
			
			function f_Imprimir() {
				var nSwicht = 0;
	  		var cMsj = "\n";
	  		
				if (document.forms['frgrm']['dAnoDes'].value != document.forms['frgrm']['dAnoHas'].value) {
					cMsj += 'No se Puede Generar el Reporte Para A\u00f1os Diferentes.\n';
					nSwicht = 1;
				}
				
				if(nSwicht != 1){
					 // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
					if(document.forms['frgrm']['cCcoId'].value != ""){
						var zX    = screen.width;
						var zY    = screen.height;
						var zNx     = 0;
						var zNy     = 0;
						var zWinPro = 'width='+zX+',scrollbars=1,height='+zY+',left='+zNx+',top='+zNy;
						var zRuta   = "frestprn.php?gWhat=WINDOW&gFunction=cSccId&gCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase()+"&gSccId="+document.forms['frgrm']['cSccId'].value.toUpperCase();
								zRuta  += "&gMesDes="+document.forms['frgrm']['dMesDes'].value.toUpperCase()+"&gAnoDes="+document.forms['frgrm']['dAnoDes'].value.toUpperCase();
								zRuta  += "&gMesHas="+document.forms['frgrm']['dMesHas'].value.toUpperCase()+"&gAnoHas="+document.forms['frgrm']['dAnoHas'].value.toUpperCase();
						zWindow = window.open(zRuta,"zWindow",zWinPro);
						zWindow.focus();
					} else {
						alert("Debe Seleccionar un Centro de Costo, Verifique");
					}
				}else {
		      alert(cMsj + "Verifique !.");
		    }
				
			}
			
			function f_Links(xLink,xSwitch,xIteration) {
				var zX    = screen.width;
				var zY    = screen.height;
				switch (xLink){
				case "cCcoId":
					if (xSwitch == "VALID") {
						var zRuta  = "frpar116.php?gWhat=VALID&gFunction=cCcoId&gCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase();
						parent.fmpro.location = zRuta;
					} else {
						var zNx     = (zX-600)/2;
						var zNy     = (zY-250)/2;
						var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
						var zRuta   = "frpar116.php?gWhat=WINDOW&gFunction=cCcoId&gCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase();
						zWindow = window.open(zRuta,"zWindow",zWinPro);
						zWindow.focus();
					}
					break;
					case "cSccId":
						if(document.forms['frgrm']['cCcoId'].value != ""){
							if (xSwitch == "VALID") {
								var zRuta  = "frpar120.php?gWhat=VALID&gFunction=cSccId&gCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase()+"&gSccId="+document.forms['frgrm']['cSccId'].value.toUpperCase();
								parent.fmpro.location = zRuta;
							} else {
								var zNx     = (zX-600)/2;
								var zNy     = (zY-250)/2;
								var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
								var zRuta   = "frpar120.php?gWhat=WINDOW&gFunction=cSccId&gCcoId="+document.forms['frgrm']['cCcoId'].value.toUpperCase()+"&gSccId="+document.forms['frgrm']['cSccId'].value.toUpperCase();
								zWindow = window.open(zRuta,"zWindow",zWinPro);
								zWindow.focus();
							}
						}else{
							alert("Debe Seleccionar un Centro de Costo, Verifique");
						}
					break;
				}
			}
		</script>
  </head>
	<body>
		<form name = "frgrm" action = "frmovdo.php" method = "post" target="fmwork">
	  	<center>
	    	<table width="500" cellspacing="0" cellpadding="0" border="0"><tr><td>
			  	<fieldset>
		      	<legend><?php echo $_COOKIE['kProDes'] ?> </legend>
		        <table border="2" cellspacing="0" cellpadding="0" width="500">
		        	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
			        	<td class="name" width="30%"><center><h5><br>REPORTE ESTADO DE RESULTADOS POR CENTRO DE COSTO<BR> Y SUBCENTRO DE COSTO</h5></center></td>
			        </tr>
			       </table>
			       <table border="0" cellspacing="0" cellpadding="0" width="500">
			        <?php $nCol = f_Format_Cols(20);
							echo $nCol;?>
							<tr>
								<td Class = "clase08" colspan = "2">
									<a href = "javascript:document.forms['frgrm']['cCcoId'].value  = '';
																				document.forms['frgrm']['cCcoDes'].value = '';
																				document.forms['frgrm']['cSccId'].value  = '';
																				document.forms['frgrm']['cSccDes'].value = '';
																				f_Links('cCcoId','VALID')" id="IdCco">Id</a><br>
									<input type = 'text' Class = 'letra' style = 'width:50' name = 'cCcoId' maxlength="20"
												onBlur = "javascript:this.value=this.value.toUpperCase();
																							f_Links('cCcoId','VALID');
																							this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
												onFocus="javascript:document.forms['frgrm']['cCcoId'].value  = '';
																						document.forms['frgrm']['cCcoDes'].value = '';
																						document.forms['frgrm']['cSccId'].value  = '';
																						document.forms['frgrm']['cSccDes'].value = '';
																						this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
								</td>
								<td Class = 'clase08' colspan = '18'>Centro Costo<br>
									<input type = 'text' Class = 'letra' style = 'width:450' name = 'cCcoDes' readonly>
								</td>
							</tr>
							<tr>
								<td Class = "clase08" colspan = "2">
									<a href = "javascript:document.forms['frgrm']['cSccId'].value  = '';
																				document.forms['frgrm']['cSccDes'].value = '';
																				f_Links('cSccId','VALID')" id="IdScc">Id</a><br>
									<input type = 'text' Class = 'letra' style = 'width:50' name = 'cSccId' maxlength="20"
												onBlur = "javascript:this.value=this.value.toUpperCase();
																							f_Links('cSccId','VALID');
																							this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
												onFocus="javascript:document.forms['frgrm']['cSccId'].value  = '';
																						document.forms['frgrm']['cSccDes'].value = '';
																						this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
								</td>
								<td Class = 'clase08' colspan = '20'>Subcentro de Costos<br>
									<input type = 'text' Class = 'letra' style = 'width:450' name = 'cSccDes' maxlength="50"
												 onBlur = "javascript:this.value=this.value.toUpperCase()">
								</td>
							</tr>
							<tr>
								<td Class = "clase08" colspan = "2"><br>Desde</td>
								<td Class = "clase08" colspan = "8"><br>
									<select name="dMesDes" style = 'width:98'>
										<?php foreach ($mMeses as $cKey => $cValor) { ?>
											<option value="<?php echo str_pad(($cKey+1), 2, "0", STR_PAD_LEFT); ?>" <?php if(($cKey+1) == $cMes){ echo "selected";} ?>><?php echo $cValor ?></option>
										<?php }	?>
									</select>
									<select name="dAnoDes" style = 'width:98'>
										<?php for($i=($cAno-2);$i<=$cAno;$i++) { ?>
											<option value="<?php echo $i ?>" <?php if($i == $cAno){ echo "selected";} ?>><?php echo $i ?></option>
										<?php }	?>
									</select>
								</td>
								<td Class = 'clase08' colspan = '2'><br>Hasta</td>
								<td Class = 'clase08' colspan = '8'><br>
									<select name="dMesHas" style = 'width:98'>
										<?php foreach ($mMeses as $cKey => $cValor) { ?>
											<option value="<?php echo str_pad(($cKey+1), 2, "0", STR_PAD_LEFT); ?>" <?php if(($cKey+1) == $cMes){ echo "selected";} ?>><?php echo $cValor ?></option>
										<?php }	?>
									</select>
									<select name="dAnoHas" style = 'width:98'>
										<?php for($i=($cAno-2);$i<=$cAno;$i++) { ?>
											<option value="<?php echo $i ?>" <?php if($i == $cAno){ echo "selected";} ?>><?php echo $i ?></option>
										<?php }	?>
									</select>
								</td>
							</tr>
		       </table>
		      </fieldset>
          <center>
						<table border="0" cellpadding="0" cellspacing="0" width="500">
							<tr height="21">
								<td width="318" height="21"></td>
								<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:f_Imprimir()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Imprimir</td>
								<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
							</tr>
						</table>
					</center>
	  </form>
  </body>
</html>
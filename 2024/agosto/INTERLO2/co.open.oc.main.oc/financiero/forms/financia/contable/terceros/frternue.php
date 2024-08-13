<?php
/**
	 * Proceso Tercero.
	 * --- Descripcion: Permite Crear un Nuevo Tercero.
	 * @author
	 * @package opencomex
	 * @version 001
	 */
	include("../../../../libs/php/utility.php"); 
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
					document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
					parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			}

			function f_HideShow(xId)	{
				if (xId == 'NATURAL')	{
					document.getElementById('DivNom1').style.display='none';
					document.getElementById('DivNom2').style.display='block';
				}	else	{
					document.getElementById('DivNom1').style.display='block';
					document.getElementById('DivNom2').style.display='none';
				}
			}

			function f_ValidacTerId(input, campo)	{
				var valor = input.value;
				if (valor.length > 0)	{
					var zX      = screen.width;
					var zY      = screen.height;
					var zNx     = (zX-550)/2;
					var zNy     = (zY-250)/2;
					var zWinPro = 'width=550,scrollbars=1,height=250,left='+zNx+',top='+zNy;
					var zRuta   = 'frtercod.php?cTerId='+valor+'&cCampo='+campo;
					zWindow     = window.open(zRuta,'zWindow',zWinPro);
					zWindow.focus();
				}	else	{
					alert('Debe digitar un DATO');
					input.blur();
				}
			}

			function f_Sucursales(xCampo){
				var x = screen.width;
				var y = screen.height;
				var nx = (x-450)/2;
				var ny = (y-350)/2;
				var str = 'width=400,scrollbars=1,height=350,left='+nx+',top='+ny;
				var rut = 'frpar008.php?gCampo='+xCampo+'&gSucIca='+document.forms['frgrm'][xCampo].value;
				msg = window.open(rut,'myw',str);
				msg.focus();
			}

			function f_DiascTerPla(xplazo){
				if(xplazo >=1 && xplazo <=360){
				}else{
				alert('El rango de Dias para el Plazo debe estar entre 1 y 360');
				}
			}

			function f_Valida_Pais(xPais,xCampo){
				switch(xCampo){
					case "cPaiId":
						if(xPais == 'CO'){
							document.getElementById('IdDep').disabled = false;
							document.getElementById('IdDep').href="javascript:f_Links('cDepId','WINDOW')";
							document.forms['frgrm']['cDepId'].disabled = false;
							document.getElementById('IdCiu').disabled=false;
							document.getElementById('IdCiu').href="javascript:f_Links('cCiuId','WINDOW')";
							document.forms['frgrm']['cCiuId'].disabled = false;
						}else{
								document.getElementById('IdDep').disabled = true;
								document.getElementById('IdDep').href="#";
								document.forms['frgrm']['cDepId'].disabled = true;
								document.getElementById('IdCiu').disabled  = true;
								document.getElementById('IdCiu').href="#";
								document.forms['frgrm']['cCiuId'].disabled = true;
							}
					break;
					case "cPaiId1":
					if(xPais == 'CO'){
							document.getElementById('IdDep1').disabled=false;
							document.getElementById('IdDep1').href="javascript:f_Links('cDepId1','WINDOW')";
							document.forms['frgrm']['cDepId1'].disabled = false;
							document.getElementById('IdCiu1').disabled=false;
							document.getElementById('IdCiu1').href="javascript:f_Links('cCiuId1','WINDOW')";
							document.forms['frgrm']['cCiuId1'].disabled = false;
						}else{
								document.getElementById('IdDep1').disabled=true;
								document.getElementById('IdDep1').href="#";
								document.forms['frgrm']['cDepId1'].disabled = true;
								document.getElementById('IdCiu1').disabled=true;
								document.getElementById('IdCiu1').href="#";
								document.forms['frgrm']['cCiuId1'].disabled = true;
						}
						break;
				}
			}

			function uDelCom(valor)	{
				if (confirm('ELIMINAR EL VENDEDOR '+valor+'?'))	{
					var ruta = "frtersav.php?cTerId=<?php echo $cTerId ?>&tipsave=4&cIntId="+valor+"&cCliVen="+document.forms['frgrm']['cCliVen'].value;
					parent.fmpro.location = ruta;
				}
			}

			function uDelCon(valor)	{
				if (confirm('ELIMINAR EL CONTACTO '+valor+'?'))	{
					var ruta = "frtercav.php?cTerId=<?php echo $cTerId ?>&tipsave=4&cIntId="+valor+"&cCliCon="+document.forms['frgrm']['cCliCon'].value;
					parent.fmpro.location = ruta;
				}
			}

			function uDelDrl(valor,fecha)	{
				if (confirm('ELIMINAR EL REQUISITO LEGAL '+valor+'?'))	{
					var ruta = "frterdrl.php?cTerId=<?php echo $cTerId ?>&tipsave=4&cIntId="+valor+"&cCliDrl="+document.forms['frgrm']['cCliDrl'].value;
					parent.fmpro.location = ruta;
				}
			}

			function uDelRes(valor,fecha)	{
				if (confirm('ELIMINAR LA RESPONSABILIDAD FISCAL '+valor+'?'))	{
					var ruta = "frterrfg.php?cTerId=<?php echo $cTerId ?>&tipsave=4&cIntId="+valor+"&cCliResFi="+document.forms['frgrm']['cCliResFi'].value;
					parent.fmpro.location = ruta;
				}
			}

			function uDelTri(valor,fecha)	{
				if (confirm('ELIMINAR EL TRIBUTO '+valor+'?'))	{
					var ruta = "frtertrg.php?cTerId=<?php echo $cTerId ?>&tipsave=4&cIntId="+valor+"&cCliTri="+document.forms['frgrm']['cCliTri'].value;
					parent.fmpro.location = ruta;
				}
			}

			function f_GenDv(xnit){
				var resdv = '';
				if (document.forms['frgrm']['cTdiId'].value.length > 0){
					var lnit = xnit.length;

					// Expresion regular para validar si es alfanumerico
					var alfanum = 0;
					var cExpReg = /^\d*$/;
					if(!xnit.search(cExpReg)) {
						alfanum = 0;
					} else {
						alfanum = 1;
					}

					if (lnit > 0 && alfanum == 0){
						var suma =0;
						var anit = new Array(97,89,83,79,73,71,67,59,53,47,43,41,37,29,23,19,17,13,7,3);

						var ini = 20-lnit;
						for(i=0;i<lnit;i++){
							var vdigito = xnit.charAt(i);
							var vl = 1*vdigito;
							var suma = suma + (vl * anit[ini]);
							ini+=1;
						}
						var resdv = suma % 11;
						if(resdv > 1){
							resdv = 11 - resdv;
						}
					}
				}
				document.forms['frgrm']['nTerDV'].value = resdv;
			}

			function f_Valida_Dv(){
			var kModo = '<?php echo $_COOKIE['kModo'] ?>';
				if(document.forms['frgrm']['cTdiId'].value == ''){
					document.forms['frgrm']['nTerDV'].value = '';
				}
			}

			function entra(tipo){
				switch(tipo){
					case 1:
						document.forms['frgrm']['ob1'].src = '<?php echo $cPlesk_Skin_Directory ?>/obsb.bmp';
					break;
				}
			}

			function sale(tipo){
				switch(tipo){
					case 1:
						document.forms['frgrm']['ob1'].src = '<?php echo $cPlesk_Skin_Directory ?>/obsa.bmp';
					break;
				}
			}

			function f_Links(xLink,xSwitch,xSecuencia) {
				var zX    = screen.width;
				var zY    = screen.height;
				switch (xLink) {
					case 'cTercero':
						var zTerId  =  document.forms['frgrm']['cTerId'].value.toUpperCase();
						var zNx     = (zX-520)/2;
						var zNy     = (zY-500)/2;
						var zWinPro = 'width=520,scrollbars=1,height=500,left='+zNx+',top='+zNy;
						var zRuta   = 'frterint.php?cTerId='+zTerId+'&gCliVen='+document.forms['frgrm']['cCliVen'].value;
						zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
						zWindow2.focus();
					break;
					case 'cContacto':
						var zTerId  =  document.forms['frgrm']['cTerId'].value.toUpperCase();
						var zNx     = (zX-520)/2;
						var zNy     = (zY-500)/2;
						var zWinPro = 'width=520,scrollbars=1,height=500,left='+zNx+',top='+zNy;
						var zRuta   = 'frtercon.php?cTerId='+zTerId+'&gCliCon='+document.forms['frgrm']['cCliCon'].value;
						zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
						zWindow2.focus();
					break;
					case 'cCuentaBancaria':
						var zTerId  =  document.forms['frgrm']['cTerId'].value.toUpperCase();
						var zNx     = (zX-580)/2;
						var zNy     = (zY-500)/2;
						var zWinPro = 'width=580,scrollbars=1,height=500,left='+zNx+',top='+zNy;
						var zRuta   = 'frtercbn.php?cTerId='+zTerId+'&gCliCueBa='+document.forms['frgrm']['cCliCueBa'].value;
						zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
						zWindow2.focus();
					break;
					case 'cResponsabilidadFiscal':
						var zTerId  =  document.forms['frgrm']['cTerId'].value.toUpperCase();
						var zNx     = (zX-580)/2;
						var zNy     = (zY-500)/2;
						var zWinPro = 'width=580,scrollbars=1,height=500,left='+zNx+',top='+zNy;
						var zRuta   = 'frterrfn.php?cTerId='+zTerId+'&gCliResFi='+document.forms['frgrm']['cCliResFi'].value;
						zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
						zWindow2.focus();
					break;
					case 'cTributo':
						var zTerId  =  document.forms['frgrm']['cTerId'].value.toUpperCase();
						var zNx     = (zX-580)/2;
						var zNy     = (zY-500)/2;
						var zWinPro = 'width=580,scrollbars=1,height=500,left='+zNx+',top='+zNy;
						var zRuta   = 'frtertrn.php?cTerId='+zTerId+'&gCliTri='+document.forms['frgrm']['cCliTri'].value;
						zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
						zWindow2.focus();
					break;
					case 'cRequisito':
						var zNx     = (zX-600)/2;
						var zNy     = (zY-500)/2;
						var zWinPro = 'width=600,scrollbars=1,height=500,left='+zNx+',top='+zNy;
						var zWindow = window.open("","zWindowRL",zWinPro);

						document.forms['frestado']['cTerId'].value  = document.forms['frgrm']['cTerId'].value.toUpperCase();
						document.forms['frestado']['cCliDrl'].value = document.forms['frgrm']['cCliDrl'].value.toUpperCase();
						document.forms['frestado'].action = "frverdrl.php";
						document.forms['frestado'].target = "zWindowRL";
						document.forms['frestado'].submit();

					break;
					case "cPaiId":
						if (xSwitch == "VALID") {
							var zRuta  = "frpai052.php?gWhat=VALID&gFunction=cPaiId&cPaiId="+document.frgrm.cPaiId.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpai052.php?gWhat=WINDOW&gFunction=cPaiId&cPaiId="+document.frgrm.cPaiId.value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cPaiId1":
						if (xSwitch == "VALID") {
							var zRuta  = "frpai052.php?gWhat=VALID&gFunction=cPaiId1&cPaiId="+document.frgrm.cPaiId1.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpai052.php?gWhat=WINDOW&gFunction=cPaiId1&cPaiId="+document.frgrm.cPaiId1.value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cDepId":
						if (xSwitch == "VALID") {
							var zRuta  = "frdep054.php?gWhat=VALID&gFunction=cDepId&cDepId="+document.forms['frgrm']['cDepId'].value.toUpperCase()+
																																				"&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase();
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frdep054.php?gWhat=WINDOW&gFunction=cDepId&cDepId="+document.forms['frgrm']['cDepId'].value.toUpperCase()+
																																				"&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase();
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cDepId1":
						if (xSwitch == "VALID") {
							var zRuta  = "frdep054.php?gWhat=VALID&gFunction=cDepId1&cDepId="+document.forms['frgrm']['cDepId1'].value.toUpperCase()+
																																				"&cPaiId="+document.forms['frgrm']['cPaiId1'].value.toUpperCase();
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frdep054.php?gWhat=WINDOW&gFunction=cDepId1&cDepId="+document.forms['frgrm']['cDepId1'].value.toUpperCase()+
																																				"&cPaiId="+document.forms['frgrm']['cPaiId1'].value.toUpperCase();
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cCiuId":
						if (xSwitch == "VALID") {
							var zRuta  = "frciu055.php?gWhat=VALID&gFunction=cCiuId&cCiuId="+document.forms['frgrm']['cCiuId'].value.toUpperCase()+
																																				"&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase()+
																																				"&cDepId="+document.forms['frgrm']['cDepId'].value.toUpperCase();
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frciu055.php?gWhat=WINDOW&gFunction=cCiuId&CiuId="+document.forms['frgrm']['cCiuId'].value.toUpperCase()+
																																				"&cPaiId="+document.forms['frgrm']['cPaiId'].value.toUpperCase()+
																																				"&cDepId="+document.forms['frgrm']['cDepId'].value.toUpperCase();
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cCiuId1":
						if (xSwitch == "VALID") {
							var zRuta  = "frciu055.php?gWhat=VALID&gFunction=cCiuId1&cCiuId="+document.forms['frgrm']['cCiuId1'].value.toUpperCase()+
																																				"&cPaiId="+document.forms['frgrm']['cPaiId1'].value.toUpperCase()+
																																				"&cDepId="+document.forms['frgrm']['cDepId1'].value.toUpperCase();
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frciu055.php?gWhat=WINDOW&gFunction=cCiuId1&CiuId="+document.forms['frgrm']['cCiuId1'].value.toUpperCase()+
																																				"&cPaiId="+document.forms['frgrm']['cPaiId1'].value.toUpperCase()+
																																				"&cDepId="+document.forms['frgrm']['cDepId1'].value.toUpperCase();
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cTdiId":
						if (xSwitch == "VALID") {
							var zRuta  = "frpar109.php?gWhat=VALID&gFunction=cTdiId&cTdiId="+document.frgrm.cTdiId.value.toUpperCase()+"&cTerId="+document.frgrm.cTerId.value.toUpperCase();
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpar109.php?gWhat=WINDOW&gFunction=cTdiId&cTdiId="+document.frgrm.cTdiId.value.toUpperCase()+"&cTerId="+document.frgrm.cTerId.value.toUpperCase();
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cGruId":
						if (xSwitch == "VALID") {
							var zRuta  = "frpar139.php?gWhat=VALID&gFunction=cGruId&cGruId="+document.frgrm.cGruId.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpar139.php?gWhat=WINDOW&gFunction=cGruId&cGruId="+document.frgrm.cGruId.value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cAecId":
						if (xSwitch == "VALID") {
							var zRuta  = "fraec101.php?gWhat=VALID&gFunction=cAecId&cAecId="+document.frgrm.cAecId.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "fraec101.php?gWhat=WINDOW&gFunction=cAecId&cAecId="+document.frgrm.cAecId.value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cCliTpCto":
						if (xSwitch == "VALID") {
							var zRuta  = "frter119.php?gWhat=VALID&gFunction=cCliTpCto&gCliTpCto="+document.frgrm.cCliTpCto.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frter119.php?gWhat=WINDOW&gFunction=cCliTpCto&gCliTpCto="+document.frgrm.cCliTpCto.value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case 'cTerObs':
						var zNx    = (zX-440)/2;
						var zNy    = (zY-200)/2;
						var zWinPro= 'width=440,scrollbars=1,height=200,left='+zNx+',top='+zNy;
						var zTerId = document.forms['frgrm']['cTerId'].value.toUpperCase();
						if (zTerId.length > 0){
							var zRuta   = 'frterobs.php?gForm=frternue.php';
							zWindow     = window.open(zRuta,'zWindow',zWinPro);
							zWindow.focus();
						}
						else{
							alert('Debe Seleccionar un Cliente Valido');
						}
					break;
					case "cReqNro":
						//alert("Entre Sec= "+xSecuencia);
						var zNx     = (zX-550)/2;
						var zNy     = (zY-250)/2;
						var zWinPro = 'width=550,scrollbars=1,height=250,left='+zNx+',top='+zNy;
						var zRuta  = "frpar110.php?gWhat=WINDOW&gFunction=cReqNro&gReqNro="+
													document.forms['frgrm']['cReqNro'+xSecuencia].value.toUpperCase()+
													"&nSecuencia="+xSecuencia;
						zWindow = window.open(zRuta,"zWindow",zWinPro);
						zWindow.focus();
					break;
					case "cCliCto":
							/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/
							if("<?php echo $vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] ?>"  == "SI"){
								var zNx      = (zX-750)/2;
								var zNy      = (zY-400)/2;
								var zWinPro  = "width=750,scrollbars=1,height=400,left="+zNx+",top="+zNy;
							}else{
								var zNx      = (zX-600)/2;
								var zNy      = (zY-250)/2;
								var zWinPro  = "width=600,scrollbars=1,height=250,left="+zNx+",top="+zNy;
							}
							var zRuta   = "frparfrm.php?gCliCto="+document.forms['frgrm']['cCliCto'].value.toUpperCase()+"&gCampo=cCliCto";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
					break;
					case "cDiscId":
						if (xSwitch == "VALID") {
							var zRuta  = "frdis160.php?gWhat=VALID&gFunction=cDiscId&gDiscId="+document.frgrm.cDiscId.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
							var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frdis160.php?gWhat=WINDOW&gFunction=cDiscId&gDiscId="+document.frgrm.cDiscId.value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
							zWindow.focus();
						}
					break;
					case "cBanId":
						if (xSwitch == "VALID") {
							var zRuta  = "frpar124.php?gWhat=VALID&gFunction=cBanId&cBanId="+document.frgrm.cBanId.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
		  				var zNx     = (zX-600)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frpar124.php?gWhat=WINDOW&gFunction=cBanId&cBanId="+document.frgrm.cBanId.value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
				}
			}

			function chDate(fld) {
				var reg = /[.+"'*?^${}()|[\]\\a-zA-Z\u00C0-\u017F]+/gi
				var val = fld.value;
				var ok = 1;
				if (val.length > 0) {

					if(val != "" && reg.test(val)){
						alert('Formato de Fecha debe ser aaaa-mm-dd');
						fld.value = '';
						fld.focus();
						ok = 0;
					}

					if (ok == 1 && (val.length < 10 || val.length > 10)) {
						alert('Formato de Fecha debe ser aaaa-mm-dd');
						fld.value = '';
						fld.focus();
						ok = 0;
					}
					
					if(val.substr(4,1) == '-' && val.substr(7,1) == '-' && ok == 1) {
						var anio = val.substr(0,4);
						var mes  = val.substr(5,2);
						var dia  = val.substr(8,2);
						
						if (mes.substr(0,1) == '0') {
							mes = mes.substr(1,1);
						}

						if (dia.substr(0,1) == '0') {
							dia = dia.substr(1,1);
						}

						if ((val != "") && (anio < 1999)) {
							alert('El Anio debe ser mayor a 1999');
							fld.value = '';
							fld.focus();
						}

						if((val != "") && (mes < 1 || mes > 12)) {
							alert('El mes debe ser mayor a 0 o menor a 13');
							fld.value = '';
							fld.focus();
						}
						
						if (dia > 31) {
							alert('El dia debe ser menor a 32');
							fld.value = '';
							fld.focus();
						}
						
						var aniobi = 28;
						if(anio % 4 ==  0) {
							aniobi = 29;
						}
						
						if (mes == 4 || mes == 6 || mes == 9 || mes == 11) {
							if (dia < 1 || dia > 30){
								alert('El dia debe ser menor a 31, dia queda en 30');
								fld.value = val.substr(0,8)+'30';
							}
						}
						
						if (mes == 1 || mes == 3 || mes == 5 || mes == 7 || mes == 8 || mes == 10 || mes == 12) {
							if (dia < 1 || dia > 32){
								alert('El dia debe ser menor a 32');
								fld.value = '';
								fld.focus();
							}
						}
						
						if(mes == 2 && aniobi == 28 && dia > 28 ) {
							alert('El dia debe ser menor a 29');
							fld.value = '';
							fld.focus();
						}
						
						if(mes == 2 && aniobi == 29 && dia > 29) {
							alert('El dia debe ser menor a 30');
							fld.value = '';
							fld.focus();
						}

						if ((val != "") && (mes == 2 && dia < 1)) {
							alert('El dia debe ser mayor a 0');
							fld.value = '';
							fld.focus();
						}
					} else {
						if(val.length > 0) {
							alert('Fecha erronea, verifique');
						}
						fld.value = '';
						fld.focus();
					}
				}
			}

			function f_EnabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				//document.forms['frgrm']['cTpeId'].disabled =false;
				//document.forms['frgrm']['cTdoId'].disabled =false;
			}

			function f_DisabledCombos() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				//document.forms['frgrm']['cTpeId'].disabled =true;
				//document.forms['frgrm']['cTdoId'].disabled =true;
			}

			function f_ValidacEstado(){
				var zEstado = document.forms['frgrm']['cEstado'].value.toUpperCase();
				if(zEstado == 'A' || zEstado == 'AC' || zEstado == 'ACT' || zEstado == 'ACTI' || zEstado == 'ACTIV' || zEstado == 'ACTIVO'){
					zEstado = 'ACTIVO';
				} else {
					if(zEstado == 'I' || zEstado == 'IN' || zEstado == 'INA' || zEstado == 'INAC' || zEstado == 'INACT' || zEstado == 'INACTI' || zEstado == 'INACTIV' || zEstado == 'INACTIVO') {
						zEstado = 'INACTIVO';
					} else {
						zEstado = '';
					}
				}
				document.forms['frgrm']['cEstado'].value = zEstado;
			}

			function f_Habilita(xOpcion) {
				switch (xOpcion) {
					case "oCliReIva":
						if (document.forms['frgrm'][xOpcion].checked == true) {
							document.getElementById('oCliReCom').disabled    = false;
							document.getElementById('oCliReSim').disabled    = false;
							document.getElementById('tblCliReIva').style.display='block';
							document.forms['frgrm'][xOpcion].value = "SI";
						} else {
							document.getElementById('oCliReCom').disabled    = true;
							document.getElementById('oCliReSim').disabled    = true;
							document.getElementById('tblCliReIva').style.display='none';
							document.forms['frgrm'][xOpcion].value = "NO";
						}
						document.getElementById('oCliReCom').checked = false;
						document.getElementById('oCliReSim').checked = false;
						document.forms['frgrm']['oCliReg'].value     = "";

						document.forms['frgrm']['oCliAr'].disabled    = false;
						document.forms['frgrm']['oCliNrp'].disabled   = false;
						document.forms['frgrm']['oCliArr'].disabled   = false;
						document.forms['frgrm']['oCliAriva'].disabled = false;
						document.forms['frgrm']['oCliArcr'].disabled  = false;
						document.forms['frgrm']['oCliArrI'].disabled  = false;
						document.forms['frgrm']['oCliPci'].disabled   = false;
						document.forms['frgrm']['oCliGc'].disabled    = false;
					break;
					case "oCliReCom":
						document.forms['frgrm']['oCliReg'].value      = "COMUN";
						document.forms['frgrm']['oCliAr'].disabled    = false;
						document.forms['frgrm']['oCliNrp'].disabled   = false;
						document.forms['frgrm']['oCliArr'].disabled   = false;
						document.forms['frgrm']['oCliAriva'].disabled = false;
						document.forms['frgrm']['oCliArcr'].disabled  = false;
						document.forms['frgrm']['oCliArrI'].disabled  = false;
						document.forms['frgrm']['oCliPci'].disabled   = false;
					break;
					case "oCliReSim":
						document.forms['frgrm']['oCliReg'].value = "SIMPLIFICADO";
						document.forms['frgrm']['oCliGc'].checked  = false;

						document.forms['frgrm']['oCliAr'].checked  = false;
						document.forms['frgrm']['oCliAr'].value    = "NO";
						document.forms['frgrm']['oCliAr'].disabled = true;

						document.getElementById('oCliArAre').disabled    = true;
						document.getElementById('oCliArAiv').disabled    = true;
						document.getElementById('oCliArAic').disabled    = true;
						document.getElementById('oCliArAcr').disabled    = true;
						document.getElementById('oCliArAis').disabled    = true;

						document.getElementById('tblCliAr').style.display    ='none';
						document.getElementById('tblCliArAic').style.display ='none';
						document.forms['frgrm']['cCliArAis'].value           = "";

						document.forms['frgrm']['oCliArAre'].checked = false;
						document.forms['frgrm']['oCliArAre'].value   = "NO";
						document.forms['frgrm']['oCliArAiv'].checked = false;
						document.forms['frgrm']['oCliArAiv'].value   = "NO";
						document.forms['frgrm']['oCliArAic'].checked = false;
						document.forms['frgrm']['oCliArAic'].value   = "NO";
						document.forms['frgrm']['oCliArAcr'].checked = false;
						document.forms['frgrm']['oCliArAcr'].value   = "NO";

						document.forms['frgrm']['oCliNrp'].checked           = false;
						document.forms['frgrm']['oCliNrp'].value             = "NO";
						document.forms['frgrm']['oCliNrp'].disabled          = true;
						document.getElementById('oCliNrpai').disabled        = true;
						document.getElementById('oCliNrpif').disabled        = true;
            document.getElementById('oCliNrpNsr').disabled       = true;
						document.getElementById('tblCliNrpai').style.display = 'none';

						document.forms['frgrm']['oCliNsrr'].disabled        = false;
						document.forms['frgrm']['oCliNsriv'].disabled       = false;
						document.forms['frgrm']['oCliNsrri'].disabled       = false;
            document.forms['frgrm']['oCliNsrri'].value          = "NO";
						document.getElementById('oCliNsrris').disabled      = true;
						document.getElementById('oCliNsrris').style.display = 'none';
						document.forms['frgrm']['cCliNsrris'].value         = "";

						document.forms['frgrm']['oCliNsrcr'].disabled = false;
						document.forms['frgrm']['oCliNrpai'].checked  = false;
						document.forms['frgrm']['oCliNrpai'].value    = "NO";
						document.forms['frgrm']['oCliNrpif'].checked  = false;
						document.forms['frgrm']['oCliNrpif'].value    = "NO";
            document.forms['frgrm']['oCliNrpNsr'].checked = false;
						document.forms['frgrm']['oCliNrpNsr'].value   = "NO";

						document.forms['frgrm']['oCliArr'].checked  = false;
						document.forms['frgrm']['oCliArr'].value    = "NO";
						document.forms['frgrm']['oCliArr'].disabled = true;

						document.forms['frgrm']['oCliAriva'].checked  = false;
						document.forms['frgrm']['oCliAriva'].value    = "NO";
						document.forms['frgrm']['oCliAriva'].disabled = true;

						//document.forms['frgrm']['oCliArcr'].checked  = false;
						//document.forms['frgrm']['oCliArcr'].value    = "NO";
						//document.forms['frgrm']['oCliArcr'].disabled = true;

						document.forms['frgrm']['oCliArrI'].checked        = false;
						document.forms['frgrm']['oCliArrI'].value          = "NO";
						document.forms['frgrm']['oCliArrI'].disabled       = true;
						document.getElementById('oCliArrIs').disabled      = true;
						document.getElementById('oCliArrIs').style.display = 'none';
						document.forms['frgrm']['cCliArrIs'].value         = "";

						document.forms['frgrm']['oCliPci'].checked  = false;
						document.forms['frgrm']['oCliPci'].value    = "NO";
						document.forms['frgrm']['oCliPci'].disabled = true;
					break;
					case "oCliRegST":
						if (document.forms['frgrm'][xOpcion].checked == true) {
							document.forms['frgrm']['oCliRegST'].value   = "SI";
							document.forms['frgrm']['oCliNsrr'].checked  = false;
							document.forms['frgrm']['oCliArr'].checked   = false;
							document.forms['frgrm']['oCliGc'].checked    = false;
						} else {
							document.forms['frgrm']['oCliRegST'].value   = "NO";
						}
					break;
					case "oCliGc":
					case "oCliPci":
						if (document.forms['frgrm'][xOpcion].checked == true) {
							document.forms['frgrm'][xOpcion].value = "SI";

							document.forms['frgrm']['oCliReIva'].checked  = true;
							document.forms['frgrm']['oCliReIva'].value    = "SI";
							document.forms['frgrm']['oCliReIva'].disabled = false;
							document.getElementById('oCliReCom').checked  = true;
							document.getElementById('oCliReSim').checked  = false;
							document.getElementById('oCliReCom').disabled = false;
							document.getElementById('oCliReSim').disabled = false;
							document.forms['frgrm']['oCliReg'].value      = "COMUN";
							document.getElementById('tblCliReIva').style.display='block';

							document.forms['frgrm']['oCliAr'].disabled    = false;
							document.forms['frgrm']['oCliNrp'].disabled   = false;
							document.forms['frgrm']['oCliArr'].disabled   = false;
							document.forms['frgrm']['oCliAriva'].disabled = false;
							document.forms['frgrm']['oCliArcr'].disabled  = false;
							document.forms['frgrm']['oCliArrI'].disabled  = false;
							document.forms['frgrm']['oCliPci'].disabled   = false;
						} else {
							document.forms['frgrm'][xOpcion].value = "NO";
						}

						if(xOpcion == "oCliGc"){
							document.forms['frgrm']['oCliRegST'].checked  = false;
							document.forms['frgrm']['oCliRegST'].disabled = false;
						}

					break;
					case "oCliNrp":
						if (document.forms['frgrm'][xOpcion].checked == true) {
							document.getElementById('oCliNrpai').disabled        = false;
							document.getElementById('oCliNrpif').disabled        = false;
              document.getElementById('oCliNrpNsr').disabled       = false;
							document.getElementById('tblCliNrpai').style.display = 'block';

							document.forms['frgrm'][xOpcion].value = "SI";

							document.forms['frgrm']['oCliReIva'].checked         = false;
							document.forms['frgrm']['oCliReIva'].value           = "NO";
							document.forms['frgrm']['oCliReIva'].disabled        = true;
							document.getElementById('oCliReCom').disabled        = true;
							document.getElementById('oCliReSim').disabled        = true;
							document.getElementById('tblCliReIva').style.display = 'none';
							document.getElementById('oCliReCom').checked         = false;
							document.getElementById('oCliReSim').checked         = false;
							document.forms['frgrm']['oCliReg'].value             = "";

							document.forms['frgrm']['oCliGc'].checked  = false;
							document.forms['frgrm']['oCliGc'].disabled = true;

              document.forms['frgrm']['oCliAr'].checked            = false;
              document.forms['frgrm']['oCliAr'].value              = "NO";
              document.forms['frgrm']['oCliAr'].disabled           = true;
              document.getElementById('oCliArAre').disabled        = true;
              document.getElementById('oCliArAiv').disabled        = true;
              document.getElementById('oCliArAic').disabled        = true;
              document.getElementById('oCliArAcr').disabled        = true;
              document.getElementById('oCliArAis').disabled        = true;
              document.getElementById('tblCliAr').style.display    = 'none';
              document.getElementById('tblCliArAic').style.display = 'none';
              document.forms['frgrm']['cCliArAis'].value           = "";
              document.forms['frgrm']['oCliArAre'].checked         = false;
              document.forms['frgrm']['oCliArAre'].value           = "NO";
              document.forms['frgrm']['oCliArAiv'].checked         = false;
              document.forms['frgrm']['oCliArAiv'].value           = "NO";
              document.forms['frgrm']['oCliArAic'].checked         = false;
              document.forms['frgrm']['oCliArAic'].value           = "NO";
              document.forms['frgrm']['oCliArAcr'].checked         = false;
              document.forms['frgrm']['oCliArAcr'].value           = "NO";

              document.forms['frgrm']['oCliNsrr'].checked  = false;
              document.forms['frgrm']['oCliNsrr'].value    = "NO";
              document.forms['frgrm']['oCliNsrr'].disabled = true;

              document.forms['frgrm']['oCliNsriv'].checked  = false;
              document.forms['frgrm']['oCliNsriv'].value    = "NO";
              document.forms['frgrm']['oCliNsriv'].disabled = true;

              document.forms['frgrm']['oCliNsrri'].checked        = false;
              document.forms['frgrm']['oCliNsrri'].value          = "NO";
              document.forms['frgrm']['oCliNsrri'].disabled       = true;
              document.getElementById('oCliNsrris').disabled      = true;
              document.getElementById('oCliNsrris').style.display = 'none';
              document.forms['frgrm']['cCliNsrris'].value         = "";

              document.forms['frgrm']['oCliNsrcr'].checked  = false;
              document.forms['frgrm']['oCliNsrcr'].value    = "NO";
              document.forms['frgrm']['oCliNsrcr'].disabled = true;

              document.forms['frgrm']['oCliArr'].checked  = false;
              document.forms['frgrm']['oCliArr'].value    = "NO";
              document.forms['frgrm']['oCliArr'].disabled = true;

              document.forms['frgrm']['oCliAriva'].checked  = false;
              document.forms['frgrm']['oCliAriva'].value    = "NO";
              document.forms['frgrm']['oCliAriva'].disabled = true;

              document.forms['frgrm']['oCliArcr'].checked  = false;
              document.forms['frgrm']['oCliArcr'].value    = "NO";
              document.forms['frgrm']['oCliArcr'].disabled = true;

              document.forms['frgrm']['oCliArrI'].checked        = false;
              document.forms['frgrm']['oCliArrI'].value          = "NO";
              document.forms['frgrm']['oCliArrI'].disabled       = true;
              document.getElementById('oCliArrIs').disabled      = true;
              document.getElementById('oCliArrIs').style.display = 'none';
              document.forms['frgrm']['cCliArrIs'].value         = "";

              document.forms['frgrm']['oCliPci'].checked  = false;
              document.forms['frgrm']['oCliPci'].value    = "NO";
              document.forms['frgrm']['oCliPci'].disabled = true;
              if ("<?php echo $cAlfa ?>" == 'ROLDANLO' || "<?php echo $cAlfa ?>" == 'DEROLDANLO' || "<?php echo $cAlfa ?>" == 'TEROLDANLO') {
								document.getElementById('tblCliArNr').style.display     ='none';
								document.getElementById('tblNoResidente').style.display ='block';
							}
						} else {
              document.getElementById('oCliNrpai').disabled        = true;
              document.getElementById('oCliNrpif').disabled        = true;
              document.getElementById('oCliNrpNsr').disabled       = true;
              document.getElementById('tblCliNrpai').style.display = 'none';
              document.forms['frgrm'][xOpcion].value               = "NO";

              document.forms['frgrm']['oCliReIva'].disabled = false;
              document.forms['frgrm']['oCliGc'].disabled    = false;
							document.forms['frgrm']['oCliAr'].disabled    = false;
							document.forms['frgrm']['oCliNsrr'].disabled  = false;
							document.forms['frgrm']['oCliNsriv'].disabled = false;
							document.forms['frgrm']['oCliNsrri'].disabled = false;
							document.forms['frgrm']['oCliNsrcr'].disabled = false;
							document.forms['frgrm']['oCliArr'].disabled   = false;
							document.forms['frgrm']['oCliAriva'].disabled = false;
							document.forms['frgrm']['oCliArcr'].disabled  = false;
							document.forms['frgrm']['oCliArrI'].disabled  = false;
              document.forms['frgrm']['oCliPci'].disabled   = false;
              if ("<?php echo $cAlfa ?>" == 'ROLDANLO' || "<?php echo $cAlfa ?>" == 'DEROLDANLO' || "<?php echo $cAlfa ?>" == 'TEROLDANLO') {
								document.getElementById('tblNoResidente').style.display ='none';
							}
						}
						document.forms['frgrm']['oCliNrpai'].checked  = false;
						document.forms['frgrm']['oCliNrpai'].value    = "NO";
						document.forms['frgrm']['oCliNrpif'].checked  = false;
						document.forms['frgrm']['oCliNrpif'].value    = "NO";
            document.forms['frgrm']['oCliNrpNsr'].checked = false;
						document.forms['frgrm']['oCliNrpNsr'].value   = "NO";

            if ("<?php echo $cAlfa ?>" == 'ROLDANLO' || "<?php echo $cAlfa ?>" == 'DEROLDANLO' || "<?php echo $cAlfa ?>" == 'TEROLDANLO') {
							document.forms['frgrm']['oCliArNr'].checked   = false;
							document.forms['frgrm']['oCliArNr'].value     = "NO";
							document.forms['frgrm']['oCliArAre'].checked  = false;
							document.forms['frgrm']['oCliArAre'].value    = "NO";
							document.forms['frgrm']['oCliArAiv'].checked  = false;
							document.forms['frgrm']['oCliArAiv'].value    = "NO";
							document.forms['frgrm']['oCliArAic'].checked  = false;
							document.forms['frgrm']['oCliArAic'].value    = "NO";
							document.forms['frgrm']['oCliArAcr'].checked  = false;
							document.forms['frgrm']['oCliArAcr'].value    = "NO";
							document.forms['frgrm']['oCliArrNr'].checked  = false;
							document.forms['frgrm']['oCliArrNr'].value    = "NO";
							document.forms['frgrm']['oCliArcrNr'].checked = false;
							document.forms['frgrm']['oCliArcrNr'].value   = "NO";
						}
					break;
					case "oCliAr":
						if (document.forms['frgrm'][xOpcion].checked == true) {
							document.getElementById('oCliArAre').disabled    = false;
							document.getElementById('oCliArAiv').disabled    = false;
							document.getElementById('oCliArAic').disabled    = false;
							document.getElementById('oCliArAcr').disabled    = false;
							document.getElementById('tblCliAr').style.display='block';
							document.forms['frgrm'][xOpcion].value = "SI";
						} else {
							document.getElementById('oCliArAre').disabled        = true;
							document.getElementById('oCliArAiv').disabled        = true;
							document.getElementById('oCliArAic').disabled        = true;
							document.getElementById('oCliArAcr').disabled        = true;
							document.getElementById('oCliArAis').disabled        = true;
							document.getElementById('tblCliAr').style.display    = 'none';
							document.getElementById('tblCliArAic').style.display = 'none';
							document.forms['frgrm']['cCliArAis'].value           = "";
							document.forms['frgrm'][xOpcion].value               = "NO";
						}
						document.forms['frgrm']['oCliArAre'].checked = false;
						document.forms['frgrm']['oCliArAre'].value   = "NO";
						document.forms['frgrm']['oCliArAiv'].checked = false;
						document.forms['frgrm']['oCliArAiv'].value   = "NO";
						document.forms['frgrm']['oCliArAic'].checked = false;
						document.forms['frgrm']['oCliArAic'].value   = "NO";
						document.forms['frgrm']['oCliArAcr'].checked = false;
						document.forms['frgrm']['oCliArAcr'].value   = "NO";
					break;
					case "oCliArrI":
						if (document.forms['frgrm'][xOpcion].checked == true) {
							document.getElementById('oCliArrIs').disabled      = false;
							document.getElementById('oCliArrIs').style.display = 'block';
							document.forms['frgrm'][xOpcion].value             = "SI";
						} else {
							document.getElementById('oCliArrIs').disabled      = true;
							document.getElementById('oCliArrIs').style.display = 'none';
							document.forms['frgrm']['cCliArrIs'].value         = "";
							document.forms['frgrm'][xOpcion].value             = "NO";
						}
					break;
					case "oCliArAic":
						if (document.forms['frgrm'][xOpcion].checked == true) {
							document.getElementById('oCliArAis').disabled        = false;
							document.getElementById('tblCliArAic').style.display = 'block';
							document.forms['frgrm'][xOpcion].value               = "SI";
						} else {
							document.getElementById('oCliArAis').disabled        = true;
							document.getElementById('tblCliArAic').style.display = 'none';
							document.forms['frgrm']['cCliArAis'].value           = "";
							document.forms['frgrm'][xOpcion].value               = "NO";
						}
					break;
          case "oCliNsrri":
						if (document.forms['frgrm'][xOpcion].checked == true) {
							document.getElementById('oCliNsrris').disabled      = false;
							document.getElementById('oCliNsrris').style.display = 'block';
							document.forms['frgrm'][xOpcion].value              = "SI";
						} else {
							document.getElementById('oCliNsrris').disabled      = true;
							document.getElementById('oCliNsrris').style.display = 'none';
							document.forms['frgrm']['cCliNsrris'].value         = "";
							document.forms['frgrm'][xOpcion].value              = "NO";
						}
					break;
					case "oCliNsrr":
					case "oCliArr":
						if (document.forms['frgrm'][xOpcion].checked == true) {
							document.forms['frgrm'][xOpcion].value = "SI";
						} else {
							document.forms['frgrm'][xOpcion].value = "NO";
						}
						document.forms['frgrm']['oCliRegST'].checked  = false;
						document.forms['frgrm']['oCliRegST'].value    = "";
					break;
          case "oCliArNr":
						if (document.forms['frgrm'][xOpcion].checked == true) {
							document.getElementById('oCliArAreNr').disabled     = false;
							document.getElementById('oCliArAcrNr').disabled     = false;
							document.getElementById('tblCliArNr').style.display ='block';
							document.forms['frgrm'][xOpcion].value 							= "SI";
						} else {
							document.getElementById('oCliArAreNr').disabled     = true;
							document.getElementById('oCliArAcrNr').disabled     = true;
							document.getElementById('oCliArAis').disabled       = true;
							document.getElementById('tblCliArNr').style.display = 'none';
							document.forms['frgrm'][xOpcion].value 							= "NO";
						}
						document.forms['frgrm']['oCliArAreNr'].checked = false;
						document.forms['frgrm']['oCliArAreNr'].value   = "NO";
						document.forms['frgrm']['oCliArAcrNr'].checked = false;
						document.forms['frgrm']['oCliArAcrNr'].value   = "NO";
					break;
					default:
						if (document.forms['frgrm'][xOpcion].checked == true) {
							document.forms['frgrm'][xOpcion].value = "SI";
						} else {
							document.forms['frgrm'][xOpcion].value = "NO";
						}
					break;
				}
			}

			function f_Borrar_Conceptos(xCtoId) {
					var cCliCto = "";
					var mCto = document.forms['frgrm']['cCliCto'].value.split("|");
					for (i=0;i<mCto.length;i++) {
						if(mCto[i] != xCtoId && mCto[i] != ''){
							cCliCto += mCto[i]+"|";
						}
					}
					document.forms['frgrm']['cCliCto'].value = cCliCto;
					f_Mostrar_Conceptos();
			}

			function f_Mostrar() {
				if (document.forms['frgrm']['vChProC'].checked == true || document.forms['frgrm']['vChProE'].checked == true) {
					document.getElementById('tblConceptos').style.display='block';
				} else {
					document.getElementById('tblConceptos').style.display='none';
					document.forms['frgrm']['cCliCto'].value = "";
					f_Mostrar_Conceptos();
				}
			}

			function f_CargarGrillas() {
				var cParametro = "1^"+document.forms['frgrm']['cCliDrl'].value +
												"|2^"+document.forms['frgrm']['cCliVen'].value +
												"|3^"+document.forms['frgrm']['cCliCon'].value +
												"|5^"+document.forms['frgrm']['cCliResFi'].value +
												"|6^"+document.forms['frgrm']['cCliTri'].value;

				var cRuta = "frtergri.php?gTerId=<?php echo $cTerId ?>&gCliDrl="+document.forms['frgrm']['cCliDrl'].value+"&gParametro="+cParametro;
				parent.fmpro.location = cRuta;
			}

			function fnCargarCuentasBancarias() {
				var cRuta = "../terceros/frtergri.php?gTipo=4&gTerId=<?php echo $cTerId ?>&gCliCueBa="+document.forms['frgrm']['cCliCueBa'].value;
				parent.fmpro5.location = cRuta;
			}

			function f_CargarRequisitos() {
				var cRuta = "frtergri.php?gTipo=1&gTerId=<?php echo $cTerId ?>&gCliDrl="+document.forms['frgrm']['cCliDrl'].value;
				parent.fmpro2.location = cRuta;
			}

			function f_CargarVendedores() {
				var cRuta = "frtergri.php?gTipo=2&gTerId=<?php echo $cTerId ?>&gCliVen="+document.forms['frgrm']['cCliVen'].value;
				parent.fmpro3.location = cRuta;
			}

			function f_CargarContactos() {
				var cRuta = "frtergri.php?gTipo=3&gTerId=<?php echo $cTerId ?>&gCliCon="+document.forms['frgrm']['cCliCon'].value;
				parent.fmpro4.location = cRuta;
			}

			function fnCargarResponsabilidadFiscal() {
				var cRuta = "frtergri.php?gTipo=5&gTerId=<?php echo $cTerId ?>&gCliResFi="+document.forms['frgrm']['cCliResFi'].value;
				parent.fmpro5.location = cRuta;
			}

			function fnCargarTributo() {
				var cRuta = "frtergri.php?gTipo=6&gTerId=<?php echo $cTerId ?>&gCliTri="+document.forms['frgrm']['cCliTri'].value;
				parent.fmpro.location = cRuta;
			}

			function f_Mostrar_Conceptos(){
				var cRuta  = "frclicto.php?gCliCto="+document.forms['frgrm']['cCliCto'].value;
				parent.fmpro2.location = cRuta;
			}

			function fnHabilitarCuentasBancarias() {
				if(document.forms['frgrm']['cTerMedP'].value == "TRANSFERENCIA"){
					document.getElementById("idCliCueBa").style.display="block";
				}else{
					document.getElementById("idCliCueBa").style.display="none";
					document.forms['frgrm']['cCliCueBa'].value = "";
					fnCargarCuentasBancarias();
				}
			}

			function fnMarcaCueAll() {
				if (document.forms['frgrm']['oChkCueAll'].checked == true){
					if (document.forms['frgrm']['vRecdordsCue'].value == 1){
						document.forms['frgrm']['oCheckCue'].checked=true;
					} else {
						if (document.forms['frgrm']['vRecdordsCue'].value > 1){
							for (i=0;i<document.forms['frgrm']['oCheckCue'].length;i++){
								document.forms['frgrm']['oCheckCue'][i].checked = true;
							}
						}
					}
				} else {
					if (document.forms['frgrm']['vRecdordsCue'].value == 1){
						document.forms['frgrm']['oCheckCue'].checked=false;
					} else {
						if (document.forms['frgrm']['vRecdordsCue'].value > 1){
							for (i=0;i<document.forms['frgrm']['oCheckCue'].length;i++){
								document.forms['frgrm']['oCheckCue'][i].checked = false;
							}
						}
					}
				}
			}
				
			function fnEliminarCuenta(){

				switch (document.forms['frgrm']['vRecdordsCue'].value) {
					case "1":
						if (document.forms['frgrm']['oCheckCue'].checked == true) {
							var cCueBan = document.forms['frgrm']['oCheckCue'].id;
							if (confirm('Desea Eliminar La Cuenta Bancaria ['+cCueBan+']?'))	{
								var ruta = "frtercbg.php?cTerId=<?php echo $cTerId ?>&tipsave=4&cIntId="+cCueBan+"&cCliCueBa="+document.forms['frgrm']['cCliCueBa'].value;
								parent.fmpro.location = ruta;
							}
						}
					break;
					default:
						var cCueBan = '';
						var nCueBan = 0;
						for (i=0;i<document.forms['frgrm']['oCheckCue'].length;i++) {
							if (document.forms['frgrm']['oCheckCue'][i].checked == true) {
								nCueBan++;
								cCueBan += cCueBan+document.forms['frgrm']['oCheckCue'][i].id+'~';
							}
						}

						if(nCueBan > 0){
							if(nCueBan == 1){
								if (confirm('Desea Eliminar La Cuenta Bancaria ['+cCueBan.substring(0,(cCueBan.length)-1)+']?'))	{
									var ruta = "frtercbg.php?cTerId=<?php echo $cTerId ?>&tipsave=4&cIntId="+cCueBan+"&cCliCueBa="+document.forms['frgrm']['cCliCueBa'].value;
									parent.fmpro.location = ruta;
								}
							}else{
								if (confirm('Desea Eliminar Las Cuentas Bancarias Seleccionadas?'))	{
									var ruta = "frtercbg.php?cTerId=<?php echo $cTerId ?>&tipsave=4&cIntId="+cCueBan+"&cCliCueBa="+document.forms['frgrm']['cCliCueBa'].value;
									parent.fmpro.location = ruta;
								}
							}
						}
					break;
				}
			}

			function fEditarCuenta() {
				switch (document.forms['frgrm']['vRecdordsCue'].value) {
					case "1":
						if (document.forms['frgrm']['oCheckCue'].checked == true) {
							var kModo01 ="EDITAR";
							var cCueBan = document.forms['frgrm']['oCheckCue'].id;
							var zX	= screen.width;
							var zY  = screen.height;
							var zNx     = (zX-480)/2;
							var zNy     = (zY-280)/2;
							var zWinPro = 'width=480,scrollbars=1,height=280,left='+zNx+',top='+zNy;
							var zRuta   = 'frterfrm.php?gOrigen=TERCEROS&kModo01='+kModo01+"&gTerId="+document.forms['frgrm']['cTerId'].value+"&cBanCta="+cCueBan;
							zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
							zWindow2.focus();
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frgrm']['oCheckCue'].length;i++) {
							if (document.forms['frgrm']['oCheckCue'][i].checked == true && zSw_Prv == 0) {
								// Solo Deja Editar el Primero Seleccionado
								zSw_Prv = 1;
								var kModo01="EDITAR";
								var cCueBan = document.forms['frgrm']['oCheckCue'][i].id;
								var zX	= screen.width;
								var zY  = screen.height;
								var zNx     = (zX-480)/2;
								var zNy     = (zY-280)/2;
								var zWinPro = 'width=480,scrollbars=1,height=280,left='+zNx+',top='+zNy;
								var zRuta   = 'frterfrm.php?gOrigen=TERCEROS&kModo01='+kModo01+"&gTerId="+document.forms['frgrm']['cTerId'].value+"&cBanCta="+cCueBan;
								zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
								zWindow2.focus();
							}
						}
					break;
				}
			}

			function fnNuevaCuenta() {
				var kModo01 = "NUEVO";
				var zX	    = screen.width;
				var zY      = screen.height;
				var zNx     = (zX-480)/2;
				var zNy     = (zY-280)/2;
				var zWinPro = 'width=480,scrollbars=1,height=280,left='+zNx+',top='+zNy;
				var zRuta   = 'frterfrm.php?gOrigen=TERCEROS&kModo01='+kModo01+'&gTerId='+document.forms['frgrm']['cTerId'].value;
				zWindow2    = window.open(zRuta,'zWindow2',zWinPro);
				zWindow2.focus();
      }

			function fnHabilitarFacturadorElectronico(){
        if(document.getElementById('idFacturador').checked == true){
					document.getElementById('tblFacturadorElectronico').style.display='block';
        } else{
					document.getElementById('tblFacturadorElectronico').style.display='none';
					document.getElementById('idCliVer').value   = '';
					document.getElementById('idCliOper').value  = '';
					document.getElementById('idCliFec').value   = '';
					document.getElementById('idCliCoEmi').value = '';
					document.getElementById('idCliCoRep').value = '';
        }
      }

      function fnAddNewRowImp(xTabla) {
      
        var cGrid      = document.getElementById(xTabla);
        var nLastRow   = cGrid.rows.length;
        var nSecuencia = nLastRow+1;
        var cTableRow  = cGrid.insertRow(nLastRow);
        var cCuenta    = 'cCuenta' + xTabla + nSecuencia; // Cuenta
        var cEstado    = 'cEstado' + xTabla + nSecuencia; // Estado
        var oBtnDel    = 'oBtnDel' + xTabla + nSecuencia; // Boton de Borrar Row
        
        TD_xAll = cTableRow.insertCell(0);
        TD_xAll.style.width  = "440px";
        TD_xAll.innerHTML    = "<input type = 'text' class = 'clase08' style = 'width:440;text-align:left' name = '"+cCuenta+"' id = '"+cCuenta+"' onKeyUp='javascript:f_Enter(event,this.name,\""+xTabla+"\");'>";

        TD_xAll = cTableRow.insertCell(1);
        TD_xAll.style.width  = "240px";
        TD_xAll.innerHTML    = "<select class='letrase' style = 'width:240;text-align:left' name = '"+cEstado+"' id = '"+cEstado+"'>"+
                              "<option value = 'ACTIVO'>ACTIVO</option>"+
                              "<option value = 'INACTIVO'>INACTIVO</option>"+
                              "</select>";

        TD_xAll = cTableRow.insertCell(2);
        TD_xAll.style.width  = "20px";
        TD_xAll.innerHTML    = "<input type = 'button' style = 'width:020;text-align:center' name = "+oBtnDel+" id = "+oBtnDel+" value = 'X' "+
                                "onClick = 'javascript:fnDeleteRowImp(this.value,\""+nSecuencia+"\",\""+xTabla+"\");'>";
                                
        document.forms['frgrm']['nSecuencia_' + xTabla].value = nSecuencia;
      }
    
      function f_Enter(e,xName,xTabla) {
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
        if (code == 13) {
          if (xName == 'cCuenta' + xTabla + eval(document.forms['frgrm']['nSecuencia_' + xTabla].value)) {
            if (document.forms['frgrm'][xName].value !== '' ) {
              fnAddNewRowImp(xTabla);
            } else {
              alert("Digite una Cuenta antes de Adicionar una nueva Fila.");
            }
          }
        }
      }
      
      function fnDeleteRowImp(xNumRow,xSecuencia,xTabla) {
        var cGrid = document.getElementById(xTabla);
        var nLastRow = cGrid.rows.length;
        if (nLastRow > 1 && xNumRow == "X") {
          if (confirm("Realmente Desea Eliminar La Cuenta ["+document.forms['frgrm']['cCuenta' + xTabla + xSecuencia].value+"]?")){ 
            if(xSecuencia < nLastRow){
              var j=0;
              for(var i=xSecuencia;i<nLastRow;i++){
                j = parseFloat(i)+1;
                document.forms['frgrm']['cCuenta' + xTabla + i].value = document.forms['frgrm']['cCuenta' + xTabla + j].value; 
                document.forms['frgrm']['cEstado' + xTabla + i].value = document.forms['frgrm']['cEstado' + xTabla + j].value; 
              }
            }
            cGrid.deleteRow(nLastRow - 1);
            document.forms['frgrm']['nSecuencia_' + xTabla].value = nLastRow - 1;
          }
        } else {
          alert("No se Pueden Eliminar Todas las Secuencias, Verifique.");
        }
      }

      function fnBorrarCuentaIMP(xTabla){
        document.getElementById(xTabla).innerHTML = "";
        fnAddNewRowImp(xTabla);
      }

		</script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
	<form name = 'frestado' action = 'frverdrl.php' method = 'post'>
		<input type = "hidden" name = "cTerId">
		<input type = "hidden" name = "cCliDrl">
	</form>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="300">
				<tr>
					<td>
						<fieldset>
							<legend><?php echo ($_COOKIE['kModo'] == "NUEVO") ? "Nuevo {$_COOKIE['kProDes']}" : "Editar {$_COOKIE['kProDes']}" ?></legend>
							<form name = 'frgrm' action = 'frtergra.php' method = 'post' target='fmpro'>
								<input type = "hidden" name = "nSecuencia"  value = "0">
								<input type = "hidden" name = "vChCliCli">
								<input type = "hidden" name = "vRecdordsCue">
                <input type = "hidden" name = "nSecuencia_Grid_ImpCash">
                <input type = "hidden" name = "nSecuencia_Grid_ImpCre">
								<center>
										<table border = '0' cellpadding = '0' cellspacing = '0' width='300'>
											<?php $zCol = f_Format_Cols(36);
											echo $zCol;?>
											<td Class = "clase08" colspan = "12">Tipo de Persona</a><br>
													<select class="letrase" size="1" name="cTpeId" style = "width:240"
														onchange="javascript:f_HideShow(this.value);">
														<option value = "" selected>-- SELECCIONE --</option>
														<option value = "PUBLICA" >ENTIDAD PUBLICA</option>
														<option value = "JURIDICA">PERSONA JURIDICA</option>
														<option value = "NATURAL" >PERSONA NATURAL</option>
													</select>

												</td>
												<td Class = "clase08" colspan = "4">
													<a href = "javascript:document.frgrm.cTdiId.value  = '';
																								document.frgrm.cTdiDes.value = '';
																								f_Valida_Dv(this.value);
																								f_Links('cTdiId','VALID');" id="IdTdi">Id</a><br>
														<input type = 'text' Class = 'letra' style = 'width:80' name = 'cTdiId' maxlength="2"
															onBlur = "javascript:f_Valida_Dv(this.value);
																									this.value=this.value.toUpperCase();
																									f_Links('cTdiId','VALID');
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus="javascript:f_Valida_Dv(this.value);
																									document.frgrm.cTdiId.value  = '';
																									document.frgrm.cTdiDes.value = '';
																									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
												<td Class = 'clase08' colspan = '8'>Tipo de Documento<br>
													<input type = 'text' Class = 'letra' style = 'width:160' name = 'cTdiDes' readonly>
												</td>
												<td Class = 'clase08' colspan = '9'>No Identificaci&oacute;n<br>
													<input type = 'text' Class = 'letra' style = 'width:180' name = "cTerId" maxlength="20"
														onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"
														onBlur = "javascript:f_GenDv(this.value);
																								this.value=this.value.toUpperCase();
																								f_ValidacTerId((this),'CLIIDXXX');
																								this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
														onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
												<td Class = 'clase08' colspan = '3'>Dv<br>
													<input type = 'text' Class = 'letra' style = 'width:60' name = "nTerDV" readonly>
												</td>
											</tr>
											<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
												<td Class = "clase08" colspan = "36">
													<div id = 'DivNom1'>
														<table>
															<tr>
																<td Class = "clase08" colspan = "17">Razon Social<br>
																	<input type = "text" Class = "letra" name = "cTerNom" style = "width:340" maxlength="100"
																		onBlur = "javascript:this.value=this.value.toUpperCase();
																												f_ValidacTerId((this),'CLINOMXX');
																												this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																</td>
																<td Class = "clase08" colspan = "1"><br>
																	<input type = "text" Class = "letra" style = "width:20" readOnly>
																</td>
																<td Class = "clase08" colspan = "17">Nombre Comercial<br>
																	<input type = "text" Class = "letra" name = "cTerNomC" style = "width:340" maxlength="100"
																		onBlur = "javascript:this.value=this.value.toUpperCase();
																												f_ValidacTerId((this),'CLINOMXX');
																												this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																</td>
															</tr>
														</table>
													</div>
													<div id = 'DivNom2'>
														<table>
															<tr>
																<td Class = "clase08" colspan = "9">Primer Apellido<br>
																	<input type = "text" Class = "letra" name = "cTerPApe" style = "width:180" maxlength="100"
																		onBlur = "javascript:this.value=this.value.toUpperCase();
																												f_ValidacTerId((this),'CLINOMXX');
																												this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																</td>
																<td Class = "clase08" colspan = "9">Segundo Apellido<br>
																	<input type = "text" Class = "letra" name = "cTerSApe" style = "width:180" maxlength="100"
																		onBlur = "javascript:this.value=this.value.toUpperCase();
																												this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																</td>
																<td Class = "clase08" colspan = "9">Primer Nombre<br>
																	<input type = "text" Class = "letra" name = "cTerPNom" style = "width:180" maxlength="100"
																		onBlur = "javascript:this.value=this.value.toUpperCase();
																												this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																</td>
																<td Class = "clase08" colspan = "8">Segundo Nombre<br>
																	<input type = "text" Class = "letra" name = "cTerSNom" style = "width:160" maxlength="100"
																		onBlur = "javascript:this.value=this.value.toUpperCase();
																												this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																</td>
															</tr>
														</table>
													</div>
												</td>
											</tr>
											<tr>
												<td Class = "clase08" colspan = "4" height="30">Clasificacion :<br></td>
												<td Class = "clase08" colspan = "5" height="30"><input type="checkbox" name = "vChCli" disabled>Cliente<br></td>
												<td Class = "clase08" colspan = "8" height="30"><input type="checkbox" name = "vChProC" onclick="javascript:f_Mostrar();">Proveedor Cliente<br></td>
												<td Class = "clase08" colspan = "8" height="30"><input type="checkbox" name = "vChProE" onclick="javascript:f_Mostrar();">Proveedor Empresa<br></td>
												<td Class = "clase08" colspan = "5" height="30"><input type="checkbox" name = "vChEmp">Empleado<br></td>
												<td Class = "clase08" colspan = "6" height="30"><input type="checkbox" name = "vChCliVenCo">Vendedor<br></td>
											</tr>
											<tr>
												<td Class = "clase08" colspan = "4" height="30"><br></td>
												<td Class = "clase08" colspan = "5" height="30"><input type="checkbox" name = "vChSoc">Socio<br></td>
												<td Class = "clase08" colspan = "8" height="30"><input type="checkbox" name = "vChEfi">E.Financiera<br></td>
												<td Class = "clase08" colspan = "8" height="30"><input type="checkbox" name = "vChOtr">Otro<br></td>
												<td Class = "clase08" colspan = "5" height="30"><input type="checkbox" name = "vChCon">Contacto<br></td>
												<td Class = "clase08" colspan = "6" height="30"><br></td>
											</tr>
											<tr>
												<td colspan = "36">
													<fieldset>
														<legend>Vendedores</legend>
														<input type = 'hidden' name = "cCliVen">
														<div id = 'overDivVen'>
														</div>
													</fieldset>
												</td>
											</tr>
											<tr>
												<td colspan="36">
													<fieldset>
														<legend>Domicilio Fiscal</legend>
														<table border = '0' cellpadding = '0' cellspacing = '0' width='300'>
															<?php $zCol = f_Format_Cols(35);
															echo $zCol;?>
															<tr>
																	<td Class = "clase08" colspan = "2">
																	<a href = "javascript:document.forms['frgrm']['cPaiId'].value  = '';
																												document.forms['frgrm']['cPaiDes'].value = '';
																												document.forms['frgrm']['cDepId'].value  = '';
																												document.forms['frgrm']['cDepDes'].value = '';
																												document.forms['frgrm']['cCiuId'].value  = '';
																												document.forms['frgrm']['cCiuDes'].value = '';
																												f_Links('cPaiId','VALID'); " id="IdPai">Id</a><br>
																		<input type = 'text' Class = 'letra' style = 'width:40' name = 'cPaiId' maxlength="10"
																			onBlur = "javascript:this.value=this.value.toUpperCase();
																													f_Links('cPaiId','VALID');
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																			onFocus = "javascript:document.forms['frgrm']['cPaiId'].value  = '';
																														document.forms['frgrm']['cPaiDes'].value = '';
																														document.forms['frgrm']['cDepId'].value  = '';
																														document.forms['frgrm']['cDepDes'].value = '';
																														document.forms['frgrm']['cCiuId'].value  = '';
																														document.forms['frgrm']['cCiuDes'].value = '';
																														this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																	</td>
																	<td Class = 'clase08' colspan = '9'>Pais<br>
																		<input type = 'text' Class = 'letra' style = 'width:180' name = 'cPaiDes' readonly>
																	</td>
																	<td Class = "clase08" colspan = "2">
																	<a href = "javascript:document.forms['frgrm']['cDepId'].value  = '';
																												document.forms['frgrm']['cDepDes'].value = '';
																												document.forms['frgrm']['cCiuId'].value  = '';
																												document.forms['frgrm']['cCiuDes'].value = '';
																												f_Links('cDepId','WINDOW')" id="IdDep">Id</a><br>
																		<input type = 'text' Class = 'letra' style = 'width:40' name = 'cDepId' maxlength="10"
																			onBlur = "javascript:this.value=this.value.toUpperCase();
																													f_Links('cDepId','VALID');
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																			onFocus = "javascript:document.forms['frgrm']['cDepId'].value  = '';
																														document.forms['frgrm']['cDepDes'].value = '';
																														document.forms['frgrm']['cCiuId'].value  = '';
																														document.forms['frgrm']['cCiuDes'].value = '';
																														this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																	</td>
																	<td Class = 'clase08' colspan = '10'>Departamento<br>
																		<input type = 'text' Class = 'letra' style = 'width:200' name = 'cDepDes' readonly>
																	</td>
																	<td Class = "clase08" colspan = "2">
																	<a href = "javascript:document.forms['frgrm']['cCiuId'].value  = '';
																												document.forms['frgrm']['cCiuDes'].value = '';
																												f_Links('cCiuId','WINDOW')" id="IdCiu">Id</a><br>
																		<input type = 'text' Class = 'letra' style = 'width:40' name = 'cCiuId' maxlength="10"
																			onBlur = "javascript:this.value=this.value.toUpperCase();
																													f_Links('cCiuId','VALID');
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																			onFocus = "javascript:document.forms['frgrm']['cCiuId'].value  = '';
																														document.forms['frgrm']['cCiuDes'].value = '';
																														this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																	</td>
																	<td Class = 'clase08' colspan = '10'>Ciudad<br>
																		<input type = 'text' Class = 'letra' style = 'width:200' name = 'cCiuDes' readonly>
																	</td>
															</tr>
															<tr>
																	<td Class = "clase08" colspan = "25">Direcci&oacute;n Domicilio Fiscal<br>
																		<input type = "text" Class = "letra" name = "cTerDir" style = "width:500" maxlength="50"
																			onblur = "javascript:this.value=this.value.toUpperCase();
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																			onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																	</td>
																	<td Class = "clase08" colspan = "10">C&oacute;digo Postal<br>
																		<input type = "text" Class = "letra" name = "cTerCPosF" style = "width:200" maxlength="10"
																			onblur = "javascript:this.value=this.value.toUpperCase();
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																			onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																	</td>
															</tr>
														</table>
													</fieldset>
												</td>
											</tr>
											<tr>
												<td Class = "clase08" colspan = "12">Telefono<br>
												<input type = "text" Class = "letra" name = "cTerTel" style = "width:240" maxlength="20"
													onblur = "javascript:this.value=this.value.toUpperCase();
																							this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
													onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
												<td Class = "clase08" colspan = "12">Fax<br>
													<input type = "text" Class = "letra" name = "cTerFax" style = "width:240" maxlength="10"
														onblur = "javascript:this.value=this.value.toUpperCase();
																								this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
														onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
												<td Class = "clase08" colspan = "12">Correo Electronico<br>
												<input type = "text" Class = "letra" name = "cTerEma" style = "width:240"
													onblur = "javascript:this.value=this.value.toUpperCase();
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
													onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
											</tr>
											<tr>
												<td Class = "clase08" colspan = "12">Apartado A&eacute;reo<br>
												<input type = "text" Class = "letra" name = "cTerApar" style = "width:240"
													onblur = "javascript:this.value=this.value.toUpperCase();
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
													onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
												<td Class = "clase08" colspan = "2">
													<a href = "javascript:document.frgrm.cGruId.value  = '';
																									document.frgrm.cGruDes.value = '';
																									f_Links('cGruId','VALID')" id="IdGru">Id</a><br>
														<input type = 'text' Class = 'letra' style = 'width:40' name = 'cGruId' maxlength="10"
															onBlur = "javascript:this.value=this.value.toUpperCase();
																									f_Links('cGruId','VALID');
																									this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus = "javascript:document.frgrm.cGruId.value  = '';
																										document.frgrm.cGruDes.value = '';
																										this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
												<td Class = 'clase08' colspan = '10'>Grupo de clientes<br>
													<input type = 'text' Class = 'letra' style = 'width:200' name = 'cGruDes' readonly>
												</td>
												<td Class = "clase08" colspan = "12"></td>
											</tr>
											<tr>
												<td colspan="36">
													<fieldset>
														<legend>Correspondencia </legend>
														<table border = '0' cellpadding = '0' cellspacing = '0' width='300'>
															<?php $zCol = f_Format_Cols(35);
															echo $zCol;?>
															<tr>
																	<td Class = "clase08" colspan = "2">
																	<a href = "javascript:document.forms['frgrm']['cPaiId1'].value  = '';
																												document.forms['frgrm']['cPaiDes1'].value = '';
																												document.forms['frgrm']['cDepId1'].value  = '';
																												document.forms['frgrm']['cDepDes1'].value = '';
																												document.forms['frgrm']['cCiuId1'].value  = '';
																												document.forms['frgrm']['cCiuDes1'].value = '';
																												f_Links('cPaiId1','VALID');" id="IdPai1">Id</a><br>
																		<input type = 'text' Class = 'letra' style = 'width:40' name = 'cPaiId1' maxlength="10"
																			onBlur = "javascript:this.value=this.value.toUpperCase();
																													f_Links('cPaiId1','VALID');
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																			onFocus = "javascript:document.forms['frgrm']['cPaiId1'].value  = '';
																														document.forms['frgrm']['cPaiDes1'].value = '';
																														document.forms['frgrm']['cDepId1'].value  = '';
																														document.forms['frgrm']['cDepDes1'].value = '';
																														document.forms['frgrm']['cCiuId1'].value  = '';
																														document.forms['frgrm']['cCiuDes1'].value = '';
																														this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																	</td>
																	<td Class = 'clase08' colspan = '9'>Pais<br>
																		<input type = 'text' Class = 'letra' style = 'width:180' name = 'cPaiDes1' readonly>
																	</td>
																	<td Class = "clase08" colspan = "2">
																	<a href = "javascript:document.forms['frgrm']['cDepId1'].value  = '';
																												document.forms['frgrm']['cDepDes1'].value = '';
																												document.forms['frgrm']['cCiuId1'].value  = '';
																												document.forms['frgrm']['cCiuDes1'].value = '';
																													f_Links('cDepId1','WINDOW')" id="IdDep1">Id</a><br>
																		<input type = 'text' Class = 'letra' style = 'width:40' name = 'cDepId1' maxlength="10"
																			onBlur = "javascript:this.value=this.value.toUpperCase();
																													f_Links('cDepId1','VALID');
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																			onFocus = "javascript:document.forms['frgrm']['cDepId1'].value  = '';
																														document.forms['frgrm']['cDepDes1'].value = '';
																														document.forms['frgrm']['cCiuId1'].value  = '';
																														document.forms['frgrm']['cCiuDes1'].value = '';
																														this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																	</td>
																	<td Class = 'clase08' colspan = '10'>Departamento<br>
																		<input type = 'text' Class = 'letra' style = 'width:200' name = 'cDepDes1' readonly>
																	</td>
																	<td Class = "clase08" colspan = "2">
																	<a href = "javascript:document.forms['frgrm']['cCiuId1'].value  = '';
																												document.forms['frgrm']['cCiuDes1'].value = '';
																												f_Links('cCiuId1','WINDOW')" id="IdCiu1">Id</a><br>
																		<input type = 'text' Class = 'letra' style = 'width:40' name = 'cCiuId1' maxlength="10"
																			onBlur = "javascript:this.value=this.value.toUpperCase();
																													f_Links('cCiuId1','VALID');
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																			onFocus = "javascript:document.forms['frgrm']['cCiuId1'].value  = '';
																														document.forms['frgrm']['cCiuDes1'].value = '';
																														this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																	</td>
																	<td Class = 'clase08' colspan = '10'>Ciudad<br>
																		<input type = 'text' Class = 'letra' style = 'width:200' name = 'cCiuDes1' readonly>
																	</td>
															</tr>
															<tr>
																<td Class = "clase08" colspan = "25">Direcci&oacute;n Correspondencia<br>
																		<input type = "text" Class = "letra" name = "cTerDirC" style = "width:500" maxlength="50"
																			onblur = "javascript:this.value=this.value.toUpperCase();
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																			onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																</td>
																<td Class = "clase08" colspan = "10">C&oacute;digo Postal<br>
																	<input type = "text" Class = "letra" name = "cTerCPosC" style ="width:200" maxlength="10"
																		onblur = "javascript:this.value=this.value.toUpperCase();
																												this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																</td>
															</tr>
														</table>
													</fieldset>
												</td>
											</tr>
											<tr>
											<td Class = "clase08" colspan = "36">
												<fieldset>
													<input type = 'hidden' name = 'cCliCon'>
													<legend>Contactos</legend>
													<div id = 'overDivCon'>
													</div>
													</fieldset>
												</td>
											</tr>
											<tr>
												<td Class = "clase08" colspan = "12">Forma de Pago</a><br>
													<select class="letrase" size="1" name="cTerFPa" style = "width:240">
														<option value = "" selected>-- SELECCIONE --</option>
														<option value = "CONTADO" >CONTADO</option>
														<option value = "CREDITO">CREDITO</option>
													</select>
												</td>
												<td Class = "clase08" colspan = "12">Plazo<br>
													<input type = "text" Class = "letra" name = "cTerPla" style = "width:240" maxlength="3"
														onblur = "javascript:this.value=this.value.toUpperCase();
																			javascript:f_DiascTerPla(this.value);
																			this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
														onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
												<td Class = "clase08" colspan = "12">Medio de Pago</a><br>
													<?php 
													$cOnChange = "";
													if ($_COOKIE['kModo'] == "EDITAR") {
														switch($cAlfa){
															case "DESIACOSIP":
															case "TESIACOSIP":
															case "SIACOSIA":
																$cOnChange = "onChange = \"javascript:fnHabilitarCuentasBancarias();\"";
															break;
														}
													}
													?>
													<select class="letrase" size="1" name="cTerMedP" style = "width:240"<?php echo ($cOnChange != '') ? " ".$cOnChange : ""?>>
														<option value = "" selected>-- SELECCIONE --</option>
														<option value = "EFECTIVO" >EFECTIVO</option>
														<option value = "CHEQUE">CHEQUE</option>
														<option value = "TRANSFERENCIA">TRANSFERENCIA</option>
													</select>
												</td>
											</tr>
											<tr>
												<td Class = "clase08" colspan = "2">
													<a href = "javascript:document.frgrm.cAecId.value  = '';
																								document.frgrm.cAecId.value = '';
																								f_Links('cAecId','WINDOW')" id="IdAec">Id</a><br>
													<input type = 'text' Class = 'letra' style = 'width:40' name = 'cAecId' maxlength="10"
														onBlur = "javascript:this.value=this.value.toUpperCase();
																								f_Links('cAecId','VALID');
																								this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
														onFocus = "javascript:document.frgrm.cAecId.value  = '';
																									document.frgrm.cAecId.value = '';
																									this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
												<td Class = 'clase08' colspan = '30'>Actividad Econ&oacute;mica<br>
													<input type = 'text' Class = 'letra' style = 'width:600' name = 'cAecDes' readonly>
												</td>
												<td Class = 'clase08' colspan = '04'>% Ret. CREE<br>
													<input type = 'text' Class = 'letra' style = 'width:080;text-align:right' name = 'cAecRet' readonly>
												</td>
											</tr>
											<?php 
											switch($cAlfa){
												case "TESIACOSIP":
												case "DESIACOSIP":
												case "SIACOSIA":
													?>
													<tr>
														<td Class = "clase08" colspan = "36">
															<fieldset id="idCliCueBa">
															<input type = 'hidden' name = 'cCliCueBa'>
															<legend>Cuentas Bancarias</legend>
															<div id = 'overDivCueBan'>
															</div>
															</fieldset>
														</td>
													</tr>
													<?php
												break;
											}
											?>
											<tr>
												<td colspan="36">
													<fieldset>
														<legend>Responsabilidad Inscrita para el Tercero </legend>
														<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
															<?php $zCol = f_Format_Cols(35);
															echo $zCol;?>
															<tr>
																<td colspan="35">
																	<center>
																	<fieldset>
																		<legend>Condiciones Tributarias</legend>
																		<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
																			<?php $zCol = f_Format_Cols(34);
																			echo $zCol;?>
																			<tr>
																				<td Class = "clase08" colspan = "14" height="30"><input type="checkbox" name = "oCliReIva" onclick="javascript:f_Habilita(this.name);">Responsable IVA</td>
																				<td Class = "clase08" colspan = "20" height="30">
																					<div id="tblCliReIva">
																						<input type="radio"    name = "oCliReg" id="oCliReCom" value="COMUN" onclick="javascript:f_Habilita(this.id);">R&eacute;gimen com&uacute;n
																						&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name = "oCliReg" id="oCliReSim" value="SIMPLIFICADO" onclick="javascript:f_Habilita(this.id);">R&eacute;gimen Simplificado (No Responsable IVA)
																					</div>
																				</td>
																			</tr>
																			<tr>
																				<td Class = "clase08" colspan = "34" height="30"><input type="checkbox" name = "oCliGc" onclick="javascript:f_Habilita(this.name);">Gran Contribuyente</td>
																			</tr>
																			<tr>
																				<td Class = "clase08" colspan = "34" height="30"><input type="checkbox" name = "oCliRegST" onclick="javascript:f_Habilita(this.name);">R&eacute;gimen Simple Tributario</td>
																			</tr>
																			<tr>
																				<td Class = "clase08" colspan = "10" height="30"><input type="checkbox" name = "oCliNrp" onclick="javascript:f_Habilita(this.name);">No Residente en el Pa&iacute;s</td>
																				<td Class = "clase08" colspan = "24" height="30">
																					<div id="tblCliNrpai">
																						<input type="checkbox" name = "oCliNrpai" id = "oCliNrpai" onclick="javascript:f_Habilita(this.name);">Aplica IVA
																						&nbsp;&nbsp;&nbsp;<input type="checkbox" name = "oCliNrpif" id = "oCliNrpif" onclick="javascript:f_Habilita(this.name);">Aplica Gravamen Financiero
                                            &nbsp;&nbsp;&nbsp;<input type="checkbox" name = "oCliNrpNsr" id = "oCliNrpNsr" onclick="javascript:f_Habilita(this.name);">No Sujeto RETEFTE por Renta
																					</div>
																				</td>
																			</tr>
																			<tr>
																				<td Class = "clase08" colspan = "14" height="30"><input type="checkbox" name = "oCliAr" onclick="javascript:f_Habilita(this.name);">Autorretenedor</td>
																				<td Class = "clase08" colspan = "13" height="30">
																					<div id="tblCliAr">
																						<input type="checkbox" name = "oCliArAre" id = "oCliArAre" onclick="javascript:f_Habilita(this.name);">Renta
																						&nbsp;&nbsp;&nbsp;<input type="checkbox" name = "oCliArAiv" id = "oCliArAiv" onclick="javascript:f_Habilita(this.name);">IVA
																						&nbsp;&nbsp;&nbsp;<input type="checkbox" name = "oCliArAic" id = "oCliArAic" onclick="javascript:f_Habilita(this.name);">ICA
																						&nbsp;&nbsp;&nbsp;<input type="checkbox" name = "oCliArAcr" id = "oCliArAcr" onclick="javascript:f_Habilita(this.name);">CREE
																					</div>
																				</td>
																				<td Class = "clase08" colspan = "07" height="30">
																					<div id="tblCliArAic">
																						<input type="button" name = "oCliArAis" id = "oCliArAis" value="Ica x Sucursales" onclick="javascript:f_Sucursales('cCliArAis')">
																						<input type="hidden" name ="cCliArAis">
																					</div>
																				</td>
																			</tr>
																			<tr>
																				<td Class = "clase08" colspan = "34" height="30"><input type="checkbox" name = "oCliNsrr" onclick="javascript:f_Habilita(this.name);">No Sujeto RETEFTE por Renta</td>
																			</tr>
																			<tr>
																				<td Class = "clase08" colspan = "34" height="30"><input type="checkbox" name = "oCliNsriv" onclick="javascript:f_Habilita(this.name);">No Sujeto RETEFTE por IVA</td>
																			</tr>
																			<tr>
																				<td Class = "clase08" colspan = "34" height="30"><input type="checkbox" name = "oCliNsrcr" onclick="javascript:f_Habilita(this.name);">No Sujeto Retenci&oacute;n CREE</td>
																			</tr>
																			<tr>
																				<td Class = "clase08" colspan = "30" height="30"><input type="checkbox" name = "oCliArr" onclick="javascript:f_Habilita(this.name);">Agente Retenedor en Renta</td>
																			</tr>
																			<tr>
																				<td Class = "clase08" colspan = "30" height="30"><input type="checkbox" name = "oCliAriva" onclick="javascript:f_Habilita(this.name);">Agente Retenedor en IVA</td>
																			</tr>
																			<tr>
																				<td Class = "clase08" colspan = "30" height="30"><input type="checkbox" name = "oCliArcr" onclick="javascript:f_Habilita(this.name);">Agente Retenedor CREE</td>
																			</tr>
																			<tr>
																				<td Class = "clase08" colspan = "14" height="30"><input type="checkbox" name = "oCliArrI"  id = "oCliArrI" onclick="javascript:f_Habilita(this.name);">Agente Retenedor ICA en</td>
																				<td Class = "clase08" colspan = "20" height="30">
																						<input type="button" name = "oCliArrIs" id = "oCliArrIs" value="Ica x Sucursales" onclick="javascript:f_Sucursales('cCliArrIs')">
																						<input type="hidden" name ="cCliArrIs">
																				</td>
																			</tr>
																			<tr>
																				<td Class = "clase08" colspan = "14" height="30"><input type="checkbox" name = "oCliNsrri" onclick="javascript:f_Habilita(this.name);">No Sujeto a Retenci&oacute;n ICA</td>
                                        <td Class = "clase08" colspan = "20" height="30">
																						<input type="button" name = "oCliNsrris" id = "oCliNsrris" value="Ica x Sucursales" onclick="javascript:f_Sucursales('cCliNsrris')">
																						<input type="hidden" name ="cCliNsrris">
																				</td>
																			</tr>
																			<tr>
																				<td Class = "clase08" colspan = "34" height="30"><input type="checkbox" name = "oCliPci" onclick="javascript:f_Habilita(this.name);">Proveedor Comercializadora Internacional</td>
																				</td>
																			</tr>
                                      <tr>
																				<td Class = "clase08" colspan = "34" height="30"><input type="checkbox" name = "oCliNsOfe" onclick="javascript:f_Habilita(this.name);">No Sujeto a Expedir Factura de Venta o Documento Equivalente</td>
																				</td>
																			</tr>
																		</table>
																	</fieldset>

                                  <?php if (f_InList($cAlfa,"ROLDANLO","TEROLDANLO","DEROLDANLO")) { ?>
																		<fieldset id="tblNoResidente">
																			<legend>Condiciones Tributarias No Residente en el Pa&iacute;s para Facturaci&oacute;n</legend>
																			<table border = '0' cellpadding = '0' cellspacing = '0' width='680'>
																				<?php $zCol = f_Format_Cols(34);
																				echo $zCol;?>
																				<tr>
																					<td Class = "clase08" colspan = "14" height="30"><input type="checkbox" name = "oCliArNr" onclick="javascript:f_Habilita(this.name);">Autorretenedor</td>
																					<td Class = "clase08" colspan = "13" height="30">
																						<div id="tblCliArNr">
																							<input type="checkbox" name = "oCliArAreNr" id = "oCliArAreNr" onclick="javascript:f_Habilita(this.name);">Renta
																							&nbsp;&nbsp;&nbsp;<input type="checkbox" name = "oCliArAcrNr" id = "oCliArAcrNr" onclick="javascript:f_Habilita(this.name);">CREE
																						</div>
																					</td>
																				</tr>
																				<tr>
																					<td Class = "clase08" colspan = "30" height="30"><input type="checkbox" name = "oCliArrNr" onclick="javascript:f_Habilita(this.name);">Agente Retenedor en Renta</td>
																				</tr>
																				<tr>
																					<td Class = "clase08" colspan = "30" height="30"><input type="checkbox" name = "oCliArcrNr" onclick="javascript:f_Habilita(this.name);">Agente Retenedor CREE</td>
																				</tr>
																			</table>
																		</fieldset>
																	<?php } ?>

																	<fieldset>
																		<input type = 'hidden' name = 'cCliResFi'>
																		<legend>Responsabilidad Fiscal</legend>
																		<div id = 'overDivResFi'></div>
																	</fieldset>

																	<fieldset>
																		<input type = 'hidden' name = 'cCliTri'>
																		<legend>Responsable Tributo</legend>
																		<div id = 'overDivTri'></div>
																	</fieldset>

																	</center>
																</td>
															</tr>
														</table>
													</fieldset>
												</td>
											</tr>
											<!-- Condiciones Especiales Tasa de Cambio -->
											<?php if (f_InList($cAlfa,"SIACOSIA","TESIACOSIP","DESIACOSIP")) { ?>
											<tr>
												<td colspan="36">
													<fieldset id="tblTasaCambio">
														<legend>Condiciones Especiales Tasa de Cambio</legend>
														<table border="0" cellpadding="0" cellspacing="0" width="700">
																<?php $nCol = f_Format_Cols(35);
																			echo $nCol; ?>
																<tr>
																	<td Class = "clase08" colspan = "14">Tasa de cambio Pactada<br></td>
																	<td Class = "clase08" colspan = "21">
																			<input type = 'text' Class = 'letra' style = 'width:100;text-align:right' name = "cCliTp"
																			onBlur = "javascript:f_FixFloat(this);
																													this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																			onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																	</td>
																</tr>
																<tr>
																	<td Class = "clase08" colspan = "14"><br>
																		<a href = "javascript:document.forms['frgrm']['cCliTpCto'].value  = '';
																													document.forms['frgrm']['cCliTpDes'].value = '';
																													document.forms['frgrm']['cCliTpPuc'].value  = '';
																													f_Links('cCliTpCto','WINDOW')" id="IdCliTpCto">Concepto para diferencia en cambio tasa pactada</a><br></td>
																	<td Class = "clase08" colspan = "4"><br>Concepto<br>
																			<input type = 'text' Class = 'letra' style = 'width:80' name = 'cCliTpCto' maxlength="10"
																				onBlur = "javascript:this.value=this.value.toUpperCase();
																														f_Links('cCliTpCto','VALID');
																														this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																				onFocus = "javascript:document.forms['frgrm']['cCliTpCto'].value  = '';
																															document.forms['frgrm']['cCliTpDes'].value = '';
																															document.forms['frgrm']['cCliTpPuc'].value  = '';
																															this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																		</td>
																		<td Class = 'clase08' colspan = '13'><br>Descripci&oacute;n<br>
																			<input type = 'text' Class = 'letra' style = 'width:260' name = 'cCliTpDes' readonly>
																		</td>
																		<td Class = 'clase08' colspan = '4'><br>Cuenta PUC<br>
																			<input type = 'text' Class = 'letra' style = 'width:80' name = 'cCliTpPuc' readonly>
																		</td>
																	</tr>
																</tr>
															</table>
														<br>
													</fieldset>
												</td>
											</tr>
											<?php } else { ?>
												<tr>
													<td colspan="36">
														<input type = 'hidden' name = 'cCliTp'   readonly>
														<input type = 'hidden' name = 'cCliTpCto'  readonly>
														<input type = 'hidden' name = 'cCliTpDes' readonly>
														<input type = 'hidden' name = 'cCliTpPuc'  readonly>
													</td>
												</tr>
											<?php }?>
											<!-- Fin Condiciones Especiales Tasa de Cambio -->
											<tr>
												<td colspan="36">
													<fieldset id="tblConceptos">
														<legend>Asignacion de Conceptos</legend>
														<table border = '0' cellpadding = '0' cellspacing = '0' width='640'>
															<tr>
																<td id="tblCliCto"></td>
															</tr>
														</table>
														<input type = "hidden" name="cCliCto">
														<br>
													</fieldset>
												</td>
											</tr>
											<tr>
												<td colspan="36">
													<fieldset>
															<legend>Verificaci&oacute;n de Requisitos Legales</legend>
															<input type = 'hidden' name = 'cCliDrl'>
															<div id = 'overDivReq'></div>
														</fieldset>
												</td>
											</tr>
											<!-- Se inserta un fieldset llamado Integración SAP, solo aplica para ALMACAFE -->
											<?php
											switch($cAlfa) {
												case "TEALMACAFE":
												case "DEALMACAFE":
                        case "ALMACAFE":
                        case "DEALPOPULX":
                        case "TEALPOPULP":
                        case "ALPOPULX":
                        case "DEALMAVIVA":
                        case "TEALMAVIVA":
                        case "ALMAVIVA":
												?>
													<tr>
														<td colspan="36">
															<fieldset>
																	<legend>Integraci&oacute;n SAP</legend>
																		<table border = '0' cellpadding = '0' cellspacing = '0' width='300'>
																			<tr>
																				<td Class = "clase08" colspan = "12">C&oacute;digo SAP<br>
																					<input type = "text" Class = "letra" name = "cCliSap" style = "width:100" maxlength="10"
																						onBlur = "javascript:f_FixInt(this); this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																						onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																				</td>
																			</tr>
																		</table>
															</fieldset>
														</td>
													</tr>
												<?php
												break;
												default:
													// no hace nada
												break;
											}
											?>
											<!-- FIN de inserción de fieldset llamado Integración SAP, solo aplica para ALMACAFE -->
											<?php if (f_InList($cAlfa,"TEUPSXXXXX","DEUPSXXXXX","UPSXXXXX")) { ?>
												<!-- Datos Adicionales UPS -->
												<tr>
													<td colspan="36">
														<fieldset>
															<legend>Datos Adicionales UPS</legend>
															<table border="0" cellpadding="0" cellspacing="0" width="700">
																<?php $nCol = f_Format_Cols(35);
																			echo $nCol; ?>
																<tr>
																	<td Class = "clase08" colspan = "35">Nit de Identificacion corporativo UPS<br>
																			<input type = 'text' Class = 'letra' style = 'width:240' name = "cCliIdCor" maxlength="20"
																		onBlur = "javascript:this.value=this.value.toUpperCase();
																												this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																	</td>
																	<td Class = "clase08" colspan = "35">ID Corporativo del Proveedor<br>
																			<input type = 'text' Class = 'letra' style = 'width:240' name = "cCliIdProv" maxlength="20"
																		onBlur = "javascript:this.value=this.value.toUpperCase();
																												this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																	</td>
																	<td Class = "clase08" colspan = "35">ID Adicional del proveedor<br>
																			<input type = 'text' Class = 'letra' style = 'width:240' name = "cCliIdApr" maxlength="20"
																		onBlur = "javascript:this.value=this.value.toUpperCase();
																												this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																		onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																	</td>
																</tr>
															</table>
														</fieldset>
													</td>
												</tr>
												<!-- Fin Datos Adicionales UPS -->
											<?php } else { ?>
												<tr>
													<td colspan="36">
														<input type = 'hidden' name = "cCliIdCor">
														<input type = 'hidden' name = "cCliIdProv">
														<input type = 'hidden' name = "cCliIdApr">
													</td>
												</tr>
											<?php }?>
											<tr>
												<td Class = "clase08" colspan = "12"><br>Codigo UAP<br>
													<input type = "text" Class = "letra" name = "cTerUapco" style = "width:240" maxlength="5"
														onBlur = "javascript:f_FixInt(this);
																								this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
														onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
												<td Class = "clase08" colspan = "12"><br>Codigo ALTEX<br>
													<input type = "text" Class = "letra" name = "cTerAltex" style = "width:240" maxlength="5"
														onBlur = "javascript:f_FixInt(this);
																								this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
														onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												</td>
											</tr>
										<tr>
											<td colspan = '36'><br><center><img src = '<?php echo $cPlesk_Skin_Directory ?>/obsa.bmp' id = 'ob1' onMousedown = "javascript:f_Links('cTerObs','VALID')" style = 'cursor:pointer' onmouseover = 'javascript:entra(1)' onMouseOut = 'javascript:sale(1)'></center></td>
										</tr>
										<tr>
											<td Class = 'letra7' colspan = '36'>
												<textarea Class = 'letrata' style = 'width:720;height:48' name = 'cTerObs' readonly></textarea></td>
										</tr>

                    <!-- Inicio Integracion DSV -->
                    <tr>
                      <td colspan="36">
                        <?php if($cAlfa == "DSVSASXX" || $cAlfa == "TEDSVSASXX" || $cAlfa == "DEDSVSASXX") { ?>
                        <fieldset>
                        <legend>Integraci&oacute;n DSV</legend>
                        <table border="0" cellpadding="0" cellspacing="0" width="700">
                          <?php $nCol = f_Format_Cols(35); echo $nCol; ?>
                          <tr>
                            <td Class = "clase08" colspan = "12">C&oacute;digo Cargowise<br>
                                    <input type = "text" Class = "letra" name = "cClicWccX" style = "width:240" maxlength="20"
                                      onblur = "javascript:this.value=this.value.toUpperCase();
                                                          this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                      onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                            </td>
                            <td Class = "clase08" colspan = "25">C&oacute;digo Forward<br>
                                  <input type = "text" Class = "letra" name = "cClifRccX" style = "width:240" maxlength="20"
                                    onblur = "javascript:this.value=this.value.toUpperCase();
                                                        this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
                                    onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
                            </td>
                          </tr>
                        </table>
                      </fieldset>
                      <?php } else { ?>
                        <input type = "hidden" name = "cClicWccX" value = "">
                        <input type = "hidden" name = "cClifRccX" value = "">
                      <?php } ?>
                      </td>
                    </tr>
                    <!-- Fin Integracion DSV -->

										<tr>
											<td colspan="36">
												<fieldset>
												<legend>Datos adicionales para Facturaci&oacute;n Electr&oacute;nica</legend>
													<table border="0" cellpadding="0" cellspacing="0" width="700">
														<?php $nCol = f_Format_Cols(35); echo $nCol; ?>
														<tr>
															<td Class = 'clase08' colspan = '35'>Correos Notificaci&oacute;n <font color ="#FF0000"> (Separe los correos por comas ',' y sin espacios)</font><br>
																<textarea Class = 'letrata' style = 'width:700;height:48' name = 'cCliPCECn'></textarea>
															</td>
														</tr>
														<tr>
															<td Class = "clase08" colspan = "12"><br>Matr&iacute;cula Mercantil<br>
																<input type = "text" Class = "letra" name = "cTerMaMer" style = "width:240" maxlength="100"
																	onblur = "javascript:this.value=this.value.toUpperCase();
																											this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																	onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
															</td>
                              <td Class = "clase08" colspan = "23"><br>ID Personalizado<br>
																<input type = "text" Class = "letra" name = "cTerIdPer" style = "width:240" maxlength="100"
																	onblur = "javascript:this.value=this.value.toUpperCase();
																											this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																	onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
															</td>
														</tr>
													</table>
												</fieldset>
											</td>
										</tr>
										<br>
                    <?php
											switch($cAlfa) {
												case "TEGRUMALCO":
												case "DEGRUMALCO":
												case "GRUMALCO":
												?>
													<tr>
														<td colspan="36">
															<fieldset>
                                <legend>Otros</legend>
                                  <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
                                    <?php $nCol = f_Format_Cols(35); echo $nCol; ?>
                                    <tr>
                                      <td Class = 'clase08' colspan = '35'>Correos Notificaci&oacute;n Rechazos Revisor Fiscal <font color ="#FF0000"> (Separe los correos por comas ',' y sin espacios)</font><br>
                                        <textarea Class = 'letrata' style = 'width:700;height:48' name = 'cCliCnrRf'></textarea>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td Class = 'clase08' colspan = '12'>Acuerdo de Pago<br>
                                        <select class="letrase" size="1" name="cCliAcuPa" style = "width:240">
                                          <option value = "" selected>-- SELECCIONE --</option>
                                          <option value = "FONDO">FONDO</option>
                                          <option value = "ANTICIPO">ANTICIPO</option>
                                          <option value = "FINANCIADO">FINANCIADO</option>
                                        </select>
                                      </td>
                                      <td Class = 'clase08' colspan = '23'>Estado Cliente<br>
                                        <select class="letrase" size="1" name="cCliEstGm" style = "width:240">
                                          <option value = "ACTIVO">ACTIVO</option>
                                          <option value = "INACTIVO">INACTIVO</option>
                                        </select>
                                      </td>
                                    </tr>
																		<!-- Datos Facturador Electronico -->
																		<tr>
																			<td class="name" colspan = '10'><br>
																				<input type="radio" name="rCliFacCel" id="idFacturador" value="FACTURADOR" onclick="fnHabilitarFacturadorElectronico()">Facturador Electr&oacute;nico
																			</td>
																			<td class="name" colspan = '15'><br>
																				<input type="radio" name="rCliFacCel" id="idSoporte" value="SOPORTE" onclick="fnHabilitarFacturadorElectronico()">Documento Soporte Para No Obligados
																			</td>
																		</tr>
																		<tr>
																			<td colspan="35">
																				<table id="tblFacturadorElectronico" border = '0' cellpadding = '0' cellspacing = '0' width='700'>
																					<tr >
																						<td Class = "clase08" colspan = '12'><br>Versi&oacute;n DIAN<br>
																							<input type = "text" Class = "letra" name = "cCliVer" id="idCliVer" style = "width:240" maxlength="255"
																								onblur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																								onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																						</td>
																						<td Class = "clase08" colspan = "12"><br>Operador<br>
																							<input type = "text" Class = "letra" name = "cCliOper" id="idCliOper" style = "width:240" maxlength="255"
																								onblur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																								onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																						</td>
																						<td Class = "clase08" colspan = "11"><br><a href='javascript:show_calendar("frgrm.cCliFec")' id="id_href_dinicial">Fecha Inicial FE:</a><br>
																							<input type = "text" Class = "letra" name = "cCliFec" id="idCliFec" style = "width:220"
																								onblur  = "javascript:chDate(this);"
																								onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																						</td>
																					</tr>
																					<tr>
																						<td Class = "clase08" colspan = "35"><br>Correo de Emis&oacute;n<br>
																							<input type = "text" Class = "letra" name = "cCliCoEmi" id="idCliCoEmi" style = "width:700" maxlength="255"
																								onblur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																								onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																						</td>
																					</tr>
																					<tr>
																						<td Class = "clase08" colspan = "35"><br>Correo de Recepci&oacute;n<br>
																							<input type = "text" Class = "letra" name = "cCliCoRep" id="idCliCoRep" style = "width:700" maxlength="255"
																								onblur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																								onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																						</td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																		<!-- Fin Datos Facturador Electronico -->
                                  </table>
															</fieldset>
														</td>
													</tr>
                          <tr>
                            <td colspan="36">
                              <fieldset>
                              <legend>Asignar Disconformidad</legend>
                                <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
                                  <?php $nCol = f_Format_Cols(35); echo $nCol; ?>
                                  <tr>
																		<td Class = "clase08" colspan = "05">
																			<a href = "javascript:document.frgrm.cDiscId.value  = '';
																														document.frgrm.cDiscId.value = '';
																														f_Links('cDiscId','WINDOW')" id="IdDisc">Id</a><br>
																			<input type = 'text' Class = 'letra' style = 'width:100' name = 'cDiscId' maxlength="10"
																				onBlur = "javascript:this.value=this.value.toUpperCase();
																														f_Links('cDiscId','VALID');
																														this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																				onFocus = "javascript:document.frgrm.cDiscId.value  = '';
																															document.frgrm.cDiscId.value = '';
																															this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																		</td>
																		<td Class = 'clase08' colspan = '30'>Descripci&oacute;n<br>
																			<input type = 'text' Class = 'letra' style = 'width:600' name = 'cDiscDes' readonly>
																		</td>
                                  </tr>
                                </table>
                              </fieldset>
                            </td>
                          </tr>
                          <br>
												<?php
                        break;
                        case "TESIACOSIP":
                        case "DESIACOSIP":
                        case "SIACOSIA": ?>
                          <tr>
                            <td colspan="36">
                              <fieldset>
                              <legend>Otros</legend>
                                <table border="0" cellpadding="0" cellspacing="0" width="700">
                                  <?php $nCol = f_Format_Cols(35); echo $nCol; ?>
                                  <tr>
                                    <td Class = 'clase08' colspan = '35'>Correos Notificaci&oacute;n Cliente HP Colombia<font color ="#FF0000"> (Separe los correos por comas ',' y sin espacios)</font><br>
                                      <textarea Class = 'letrata' style = 'width:700;height:48' name = 'cCliHPCnx'></textarea>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td Class = 'clase08' colspan = '35'>Correos Notificaci&oacute;n Reporte Siemens<font color ="#FF0000"> (Separe los correos por comas ',' y sin espacios)</font><br>
																			<textarea Class = 'letrata' style = 'width:700;height:48' name = 'cCliEmaSi'></textarea>
																		</td>
																	</tr>
                                  <tr>
                                    <td Class = 'clase08' colspan = '35'>Correos Notificaci&oacute;n Reporte BAVARIA<font color ="#FF0000"> (Separe los correos por comas ',' y sin espacios)</font><br>
                                      <textarea Class = 'letrata' style = 'width:700;height:48' name = 'cCliBavCn'></textarea>
                                    </td>
                                  </tr>
                                </table>
                              </fieldset>
                            </td>
													</tr>
                          <br>
                        <?php
                        break;
                        case "TEROLDANLO":
												case "DEROLDANLO":
												case "ROLDANLO": ?>
													<tr>
														<td colspan="36">
															<fieldset>
															<legend>Otros</legend>
																<table border="0" cellpadding="0" cellspacing="0" width="700">
																	<?php $nCol = f_Format_Cols(35); echo $nCol; ?>
																	<tr>
																		<td Class = "clase08" colspan = "12">Cobrador Cartera<br>
																			<input type = "text" Class = "letra" name = "cCliRolCc" style = "width:240" maxlength="255"
																				onblur = "javascript:this.value=this.value.toUpperCase();
																														this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																				onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																		</td>
                                    <td Class = "clase08" colspan = "23">Moneda de Facturaci&oacute;n<br>
                                      <select Class = "letrase" name = "cCliMon" style = "width:240">
																				<option value = ''>[SELECCIONE]</option>
																				<option value = 'COP'>PESOS</option>
																				<option value = 'USD'>DOLARES</option>
																			</select>
																		</td>
																	</tr>
																</table>
															</fieldset>
														</td>
													</tr>
                          <br>
                        <?php
                        break;
                        case "TEDHLEXPRE":
                        case "DEDHLEXPRE":
                        case "DHLEXPRE": ?>
													<tr>
														<td colspan="36">
                              <fieldset>
                                <legend>Cuenta IMP Cash</legend>
                                <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
                                  <?php $nCol = f_Format_Cols(35); echo $nCol;?>
                                  <tr>
                                    <td colspan="35" class= "clase08" align="right">
                                      <?php if ($_COOKIE['kModo'] != "VER") { ?>
                                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" onClick = "javascript:fnAddNewRowImp('Grid_ImpCash')" style = "cursor:pointer" title="Adicionar">
                                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:fnBorrarCuentaIMP('Grid_ImpCash')" style = "cursor:pointer" title="Eliminar Todos">
                                      <?php } ?>
                                    </td>                       
                                  </tr>
                                  <tr>
                                    <td class = "clase08" colspan="22" align="left">Cuenta</td>                                                          
                                    <td class = "clase08" colspan="12" align="left">Estado</td>
                                    <td class = "clase08" colspan="01" align="right">&nbsp;</td>                       
                                  </tr>
                                </table>
                                <table border = "0" cellpadding = "0" cellspacing = "0" width = "700" id = "Grid_ImpCash"></table>
                              </fieldset>
														</td>
													</tr>
                          <tr>
														<td colspan="36">
                              <fieldset>
                                <legend>Cuenta IMP Cr&eacute;dito</legend>
                                <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
                                  <?php $nCol = f_Format_Cols(35); echo $nCol;?>
                                  <tr>
                                    <td colspan="35" class= "clase08" align="right">
                                      <?php if ($_COOKIE['kModo'] != "VER") { ?>
                                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_create-dir_bg.gif" onClick = "javascript:fnAddNewRowImp('Grid_ImpCre')" style = "cursor:pointer" title="Adicionar">
                                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:fnBorrarCuentaIMP('Grid_ImpCre')" style = "cursor:pointer" title="Eliminar Todos">
                                      <?php } ?>
                                    </td>                       
                                  </tr>
                                  <tr>
                                    <td class = "clase08" colspan="22" align="left">Cuenta</td>                                                          
                                    <td class = "clase08" colspan="10" align="left">Estado</td>
                                    <td class = "clase08" colspan="01" align="right">&nbsp;</td>                       
                                  </tr>
                                </table>
                                <table border = "0" cellpadding = "0" cellspacing = "0" width = "700" id = "Grid_ImpCre"></table>
                              </fieldset>
														</td>
													</tr>
													<tr>
														<td colspan="36">
                              <fieldset>
                                <legend>Cuenta</legend>
                                <table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
                                  <?php $nCol = f_Format_Cols(35); echo $nCol;?>
																	<tr>
																		<td Class = "clase08" colspan = "2">
																			<a href = "javascript:document.frgrm.cBanId.value  = '';
																														document.frgrm.cBanDes.value = '';
																														f_Links('cBanId','VALID')" id="IdBan">Id</a><br>
																			<input type = 'text' Class = 'letra' style = 'width:40' name = 'cBanId' maxlength="3"
																				onBlur = "javascript:this.value=this.value.toUpperCase();
																														f_Links('cBanId','VALID');
																														this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																				onFocus="javascript:document.frgrm.cBanId.value  = '';
																														document.frgrm.cBanDes.value = '';
																														this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																		</td>
																		<td Class = 'clase08' colspan = '16'>Banco<br>
																			<input type = 'text' Class = 'letra' style = 'width:240' name = 'cBanDes' readonly>
																		</td>
																		<td Class = "clase08" colspan = "9">Tipo de Cuenta<br>
																			<select Class = "letrase" name = "cTipCta" style = "width:140">
																				<option value = ''>[SELECCIONE]</option>
																				<option value = 'AHORROS'>AHORROS</option>
																				<option value = 'CORRIENTE'>CORRIENTE</option>
																			</select>
																		</td>
																		<td Class = 'clase08' colspan = '9'>Numero de Cuenta<br>
																			<input type = 'text' Class = 'letra' style = 'width:140' name = 'cBanCta' maxlength="30"
																				onBlur = "javascript:this.value=this.value.toUpperCase();
																														this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																				onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																		</td>
																		<td Class = "clase08" colspan = "9">Estado<br>
																			<select Class = "letrase" name = "cEstCta" style = "width:140">
																				<option value = ''>[SELECCIONE]</option>
																				<option value = 'ACTIVO'>ACTIVO</option>
																				<option value = 'INACTIVO'>INACTIVO</option>
																			</select>
																		</td>
																	</tr>
                                </table>
                              </fieldset>
														</td>
													</tr>
                          <tr>
														<td colspan="36">
															<fieldset>
																<legend>Notificaciones Checkpoints</legend>
																	<table border = '0' cellpadding = '0' cellspacing = '0' width='700'>
																		<?php $nCol = f_Format_Cols(35); echo $nCol;?>
																		<tr>
																			<td colspan="35" class= "clase08" align="left">URL Chat Privado<br>
																				<input type = "text" Class = "letra" name = "cCliCecNc" style = "width:700"
																					onblur = "this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
																					onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
																			</td>
																		</tr>
																	</table>
															</fieldset>
														</td>
													</tr>
                          <tr>
                            <td colspan="36">
                              <fieldset>
                              <legend>Otros</legend>
                                <table border="0" cellpadding="0" cellspacing="0" width="700">
                                  <?php $nCol = f_Format_Cols(35); echo $nCol; ?>
                                  <tr>
                                    <td Class = "clase08" colspan = "8">Requiere Env&iacute;o Pre-Facturas<br>
                                      <select Class = "letrase" name = "cCliEnPrF" style = "width:160">
                                        <option value = ''>[SELECCIONE]</option>
                                        <option value = 'SI'>SI</option>
                                        <option value = 'NO'>NO</option>
                                      </select>
                                    </td>
                                  </tr>
                                </table>
                              </fieldset>
                            </td>
                          </tr>
                          <br>
                        <?php
                        break;
                        case "TEADUANERA":
                        case "DEADUANERA":
                        case "ADUANERA": ?>
                          <tr>
                            <td colspan="36">
                              <fieldset>
                              <legend>Otros</legend>
                                <table border="0" cellpadding="0" cellspacing="0" width="700">
                                  <?php $nCol = f_Format_Cols(35); echo $nCol; ?>
                                  <tr>
                                    <td Class = "clase08" colspan = "35">Orden de Compra
                                      <textarea Class = "letrata" style = 'width:700;height:36' name = "cCliOrCom"></textarea>
                                    </td>
                                  </tr>
                                </table>
                              </fieldset>
                            </td>
                          </tr>
                          <br>
                        <?php
                        break;
                        case "TEINTERLO2":
                        case "DEINTERLO2":
                        case "INTERLO2": ?>
                          <tr>
                            <td colspan="36">
                              <fieldset>
                                <legend>Otros Transmisi&oacute;n a SIESA</legend>
                                  <table border="0" cellpadding="0" cellspacing="0" width="700">
                                    <?php $nCol = f_Format_Cols(35); echo $nCol; ?>
                                    <tr>
                                      <td colspan="35">
                                        <center>
                                          <fieldset>
                                            <legend>Criterios Clientes</legend>
                                            <table border="0" cellpadding="0" cellspacing="0" width="680">
                                              <?php $zCol = f_Format_Cols(34); echo $zCol; ?>
                                              <tr>
                                                <td class="clase08" colspan="12">Plan Criterios<br>
                                                  <input type="text" style="width:240" name="cCliCPlCr">
                                                </td>
                                                <td class="clase08" colspan="23">Criterio Mayor<br>
                                                  <input type="text" style="width:240" name="cCliCCrMa">
                                                </td>
                                              </tr>
                                            </table>
                                          </fieldset>
                                          <fieldset>
                                            <legend>Criterios Proveedores</legend>
                                            <table border="0" cellpadding="0" cellspacing="0" width="680">
                                              <?php $zCol = f_Format_Cols(34); echo $zCol; ?>
                                              <tr>
                                                <td class="clase08" colspan="12">Plan Criterios<br>
                                                  <input type="text" style="width:240" name="cCliPPlCr">
                                                </td>
                                                <td class="clase08" colspan="23">Criterio Mayor<br>
                                                  <input type="text" style="width:240" name="cCliPCrMa">
                                                </td>
                                              </tr>
                                            </table>
                                          </fieldset>
                                        </center>
                                      </td>
                                    </tr>
                                  </table>
                              </fieldset>
                            </td>
                          </tr>
                          <br>
                        <?php
                        break;
												default:
													// no hace nada
												break;
                      }
                    ?>
										<tr>
											<td Class = "clase08" colspan = "9">Fecha<br>
												<input type = "text" Class = "letra"  style = "width:180;text-align:center"  name = "dFecCre"  value = "<?php echo date('Y-m-d') ?>" readonly>
											</td>
											<td Class = "clase08" colspan = "9">Hora<br>
												<input type = 'text' Class = 'letra' style = "width:180;text-align:center" name = "dHorCre"  value = "<?php echo date('H:i:s') ?>" readonly>
											</td>
											<td Class = "clase08" colspan = "9">Modificado<br>
												<input type = "text" Class = "letra"  style = "width:180;text-align:center"  name = "vFecMod"  value = "<?php echo date('Y-m-d') ?>" readonly>
											</td>
											<td Class = "clase08" colspan = "9">Estado<br>
												<input type = "text" Class = "letra" style = "width:180;text-align:center" name = "cEstado"  value = "ACTIVO"
															onblur = "javascript:this.value=this.value.toUpperCase();f_ValidacEstado();
																				this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
															onFocus = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
											</td>
										</tr>
									</table>
								</center>
							</form>
						</fieldset>
					</td>
				</tr>
			</table>
		</center>
		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="720">
				<tr height="21">
					<?php switch ($_COOKIE['kModo']) {
						case "VER": ?>
							<td width="629" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
						case "EDITAR": ?>
							<td width="447" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:document.cookie='kModo=APLICAR;path='+'/';
																																																																												document.forms['frgrm'].action='frtergra.php';
																																																																												document.forms['frgrm'].target='fmpro';
																																																																												document.forms['frgrm'].submit();
																																																																												document.forms['frgrm'].action='';
																																																																												document.forms['frgrm'].target='';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aplicar</td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:f_EnabledCombos();document.forms['frgrm'].submit();f_DisabledCombos()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
						default: ?>
							<td width="538" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:f_EnabledCombos();document.forms['frgrm'].submit();f_DisabledCombos()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						<?php break;
					} ?>
				</tr>
			</table>
		</center>
		<br>
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<?php
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
				?>
				<script languaje = "javascript">
					f_HideShow('');
					document.forms['frgrm']['cEstado'].readOnly  = true;
					f_Habilita('oCliReIva');
					f_Habilita('oCliNrp');
					f_Habilita('oCliAr');
					f_Habilita('oCliArr');
					f_Habilita('oCliAriva');
					f_Habilita('oCliArcr');
          f_Habilita('oCliNsrri');
					f_Habilita('oCliArrI');
					f_Habilita('oCliArAic');
					f_Habilita('oCliArAcr');
					f_Mostrar_Conceptos();
					f_Mostrar();
					switch("<?php echo $cAlfa ?>"){
						case "TESIACOSIP":
						case "DESIACOSIP":
						case "SIACOSIA":
							fnCargarCuentasBancarias();
              fnHabilitarCuentasBancarias();
						break;
            case "TEGRUMALCO":
            case "DEGRUMALCO":
            case "GRUMALCO":
              fnHabilitarFacturadorElectronico();
            break;
            case "TEDHLEXPRE":
            case "DEDHLEXPRE":
            case "DHLEXPRE":
              fnAddNewRowImp('Grid_ImpCash');
              fnAddNewRowImp('Grid_ImpCre');
            break;
					}
					f_CargarGrillas();
				</script>
				<?php
			break;
			case "EDITAR":
				f_CargaData($cTerId);
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cTerId'].readOnly	 	= true;
					document.forms['frgrm']['cTerId'].onblur		  = "";
					if(document.forms['frgrm']['cTdiId'].value == ""){
					document.forms['frgrm']['nTerDV'].value = "";
					}
					f_HideShow(document.forms['frgrm']['cTpeId'].value);
					switch("<?php echo $cAlfa ?>"){
						case "TESIACOSIP":
						case "DESIACOSIP":
						case "SIACOSIA":
							fnCargarCuentasBancarias();
              fnHabilitarCuentasBancarias();
						break;
					}
					f_CargarGrillas();
					f_Habilita('oCliNrp');
					f_Habilita('oCliAr');
					f_Habilita('oCliArr');
					f_Habilita('oCliAriva');
					f_Habilita('oCliArcr');
          f_Habilita('oCliNsrri');
					f_Habilita('oCliArrI');
					f_Habilita('oCliArAic');
					f_Habilita('oCliArAcr');
				</script>
			<?php break;
			case "VER":
				f_CargaData($cTerId); ?>
				<script languaje = "javascript">
					for (x=0;x<document.forms['frgrm'].elements.length;x++) {
						document.forms['frgrm'].elements[x].readOnly = true;
						document.forms['frgrm'].elements[x].onfocus  = "";
						document.forms['frgrm'].elements[x].onblur   = "";
						document.forms['frgrm'].elements[x].style.fontWeight = "bold";
					}
					document.forms['frgrm']['cTerFPa'].disabled  = true;
					document.forms['frgrm']['cTerMedP'].disabled = true;
					document.forms['frgrm']['vChSoc'].disabled   = true;
					document.forms['frgrm']['vChProC'].disabled  = true;
					document.forms['frgrm']['vChProE'].disabled  = true;
					document.forms['frgrm']['vChProE'].disabled  = true;
					document.forms['frgrm']['vChEmp'].disabled   = true;
					document.forms['frgrm']['vChCliVenCo'].disabled   = true;
					document.forms['frgrm']['vChEfi'].disabled   = true;
					document.forms['frgrm']['vChOtr'].disabled   = true;
					document.forms['frgrm']['vChCon'].disabled   = true;
					document.getElementById('IdGru').disabled    = true;
					document.getElementById('IdGru').href="#";
					document.getElementById('IdPai').disabled    = true;
					document.getElementById('IdPai').href="#";
					document.getElementById('IdDep').disabled    = true;
					document.getElementById('IdDep').href="#";
					document.getElementById('IdCiu').disabled    = true;
					document.getElementById('IdCiu').href="#";
					document.getElementById('IdPai1').href="#";
					document.getElementById('IdDep1').disabled   = true;
					document.getElementById('IdDep1').href="#";
					document.getElementById('IdCiu1').disabled   = true;
					document.getElementById('IdCiu1').href="#";
					document.forms['frgrm']['cTpeId'].disabled   = true;
					document.getElementById('IdAec').disabled    = true;
					document.getElementById('IdAec').href="#";
					if ("<?php echo $cAlfa ?>" == "SIACOSIA"   || "<?php echo $cAlfa ?>" == "TESIACOSIP" || "<?php echo $cAlfa ?>" == "DESIACOSIP") {
						document.getElementById('IdCliTpCto').disabled = true;
						document.getElementById('IdCliTpCto').href="#";
					}
          if ("<?php echo $cAlfa ?>" == "GRUMALCO"   || "<?php echo $cAlfa ?>" == "TEGRUMALCO" || "<?php echo $cAlfa ?>" == "DEGRUMALCO") {
						document.getElementById('IdDisc').disabled    = true;
						document.getElementById('IdDisc').href="#";
					}
					document.forms['frgrm']['ob1'].disabled      = true;
					if (document.forms['frgrm']['cTpeId'].value == 'NATURAL')	{
						document.forms['frgrm']['cTerPApe'].disabled	 = true;
						document.forms['frgrm']['cTerSApe'].disabled	 = true;
						document.forms['frgrm']['cTerPNom'].disabled	 = true;
						document.forms['frgrm']['cTerSNom'].disabled	 = true;
					}	else	{
						document.forms['frgrm']['cTerNom'].disabled	 = true;
					}
					f_HideShow(document.forms['frgrm']['cTpeId'].value);

					document.forms['frgrm']['oCliReIva'].disabled  = true;
					document.forms['frgrm']['oCliReg'].disabled	   = true;
					document.forms['frgrm']['oCliReCom'].disabled	 = true;
					document.forms['frgrm']['oCliReSim'].disabled	 = true;
					document.forms['frgrm']['oCliRegST'].disabled	 = true;
					document.forms['frgrm']['oCliArAis'].disabled	 = true;
					document.forms['frgrm']['oCliArrIs'].disabled	 = true;
					document.forms['frgrm']['oCliGc'].disabled	   = true;
					document.forms['frgrm']['oCliNrp'].disabled	   = true;
					document.forms['frgrm']['oCliNrpai'].disabled  = true;
					document.forms['frgrm']['oCliNrpif'].disabled  = true;
          document.forms['frgrm']['oCliNrpNsr'].disabled = true;
					document.forms['frgrm']['oCliAr'].disabled	   = true;
					document.forms['frgrm']['oCliArAre'].disabled  = true;
					document.forms['frgrm']['oCliArAiv'].disabled  = true;
					document.forms['frgrm']['oCliArAic'].disabled  = true;
					document.forms['frgrm']['oCliArAcr'].disabled  = true;
					document.forms['frgrm']['oCliNsrri'].disabled  = true;
					document.forms['frgrm']['oCliNsrr'].disabled   = true;
					document.forms['frgrm']['oCliNsriv'].disabled  = true;
					document.forms['frgrm']['oCliNsrcr'].disabled  = true;
					document.forms['frgrm']['oCliArr'].disabled	   = true;
					document.forms['frgrm']['oCliAriva'].disabled	 = true;
					document.forms['frgrm']['oCliArcr'].disabled	 = true;
					document.forms['frgrm']['oCliArrI'].disabled   = true;
					document.forms['frgrm']['oCliPci'].disabled	   = true;
          document.forms['frgrm']['oCliNsOfe'].disabled	 = true;

					switch("<?php echo $cAlfa ?>"){
						case "TESIACOSIP":
						case "DESIACOSIP":
						case "SIACOSIA":
							fnCargarCuentasBancarias();
              fnHabilitarCuentasBancarias();
						break;
						case "TEDHLEXPRE":
						case "DEDHLEXPRE":
						case "DHLEXPRE":
							document.getElementById('IdBan').disabled   = true;
							document.getElementById('IdBan').href="#";
							document.forms['frgrm']['cTipCta'].disabled = true;
							document.forms['frgrm']['cEstCta'].disabled = true;
              document.forms['frgrm']['cCliCecNc'].disabled = true;
						break;
            case "TEROLDANLO":
						case "DEROLDANLO":
						case "ROLDANLO":
							document.forms['frgrm']['oCliArNr'].disabled	  = true;
							document.forms['frgrm']['oCliArAreNr'].disabled	= true;
							document.forms['frgrm']['oCliArAcrNr'].disabled	= true;
							document.forms['frgrm']['oCliArrNr'].disabled	  = true;
							document.forms['frgrm']['oCliArcrNr'].disabled	= true;
						break;
					}
					f_CargarGrillas();
				</script>
			<?php break;
		} ?>

		<?php function f_CargaData($xTerId) {
			global $cAlfa; global $xConexion01; global $vSysStr; global $_COOKIE;

			$cBuscar01 = array('"',chr(13),chr(10),chr(27),chr(9));
			$cReempl01 = array('\"'," "," "," "," ");

			/* TRAIGO DATOS DE CABECERA*/
			$qDatTer  = "SELECT * ";
			$qDatTer .= "FROM $cAlfa.SIAI0150 ";
			$qDatTer .= "WHERE CLIIDXXX = \"$xTerId\" LIMIT 0,1";
			$xDatTer  = f_MySql("SELECT","",$qDatTer,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qDatTer."~".mysql_num_rows($xDatTer));

			$cTdDes	   = "";
			$cPaisDes  = "";
			$cDeptoDes = "";
			$cCiudDes	 = "";
			$cGruCDes  = "";
			$cPaisDesC = "";
			$cDeptoDesC= "";
			$cCiudDesC = "";

			/* EMPIEZO A RECORRER EL CURSOR DE CABECERA */
			while ($vDatTer = mysql_fetch_array($xDatTer)) {
				/*TRAIGO DATOS DE TIPO DE DOCUMENTO*/
				$qDatTdi = "SELECT * ";
				$qDatTdi.= "FROM $cAlfa.fpar0109 ";
				$qDatTdi.= "WHERE tdiidxxx =\"{$vDatTer['TDIIDXXX']}\" LIMIT 0,1";
				$xDatTdi = f_MySql("SELECT","",$qDatTdi,$xConexion01,"");
				while($xDT = mysql_fetch_array($xDatTdi)){
					$cTdDes = $xDT['tdidesxx'];
				}

				/* TRAIGO DATOS DE PAIS DOMICILIO FISCAL*/
				$qDatPai = "SELECT * ";
				$qDatPai.= "FROM $cAlfa.SIAI0052 ";
				$qDatPai.= "WHERE ";
				$qDatPai.= "PAIIDXXX =\"{$vDatTer['PAIIDXXX']}\" LIMIT 0,1";
				$xDatPai = f_MySql("SELECT","",$qDatPai,$xConexion01,"");
				while($xDP = mysql_fetch_array($xDatPai)){
					$cPaisDes = $xDP['PAIDESXX']." "."(".$xDP['PAIIDNXX'].")";
				}

				/*TRAIDO DATOS DE DEPARTAMENTO DOMICILIO FISCAL*/
				$qDatDep = "SELECT * ";
				$qDatDep.= "FROM $cAlfa.SIAI0054 ";
				$qDatDep.= "WHERE ";
				$qDatDep.= "PAIIDXXX =\"{$vDatTer['PAIIDXXX']}\" AND ";
				$qDatDep.= "DEPIDXXX =\"{$vDatTer['DEPIDXXX']}\" LIMIT 0,1";
				$xDatDep = f_MySql("SELECT","",$qDatDep,$xConexion01,"");
				while($xDD = mysql_fetch_array($xDatDep)){
					$cDeptoDes = $xDD['DEPDESXX'];
				}

				/*TRAIGO DATOS DE CIUDAD DOMICILIO FISCAL*/
				$qDatCiu = "SELECT * ";
				$qDatCiu.= "FROM $cAlfa.SIAI0055 ";
				$qDatCiu.= "WHERE ";
				$qDatCiu.= "PAIIDXXX =\"{$vDatTer['PAIIDXXX']}\" AND ";
				$qDatCiu.= "DEPIDXXX =\"{$vDatTer['DEPIDXXX']}\" AND ";
				$qDatCiu.= "CIUIDXXX =\"{$vDatTer['CIUIDXXX']}\" LIMIT 0,1";
				$xDatCiu = f_MySql("SELECT","",$qDatCiu,$xConexion01,"");
				while($xDC = mysql_fetch_array($xDatCiu)){
					$cCiudDes = $xDC['CIUDESXX'];
				}

				/*TRAIGO DATOS DE GRUPO DE CLIENTES*/
				$qDatGru = "SELECT * ";
				$qDatGru.= "FROM $cAlfa.fpar0139 ";
				$qDatGru.= "WHERE gruidxxx =\"{$vDatTer['GRUIDXXX']}\" LIMIT 0,1";
				$xDatGru = f_MySql("SELECT","",$qDatGru,$xConexion01,"");
				while($xDG = mysql_fetch_array($xDatGru)){
					$cGruCDes = $xDG['grudesxx'];
				}
				/* TRAIGO DATOS DE PAIS CORRESPONDENCIA*/
				$qDatPaiC = "SELECT * ";
				$qDatPaiC.= "FROM $cAlfa.SIAI0052 ";
				$qDatPaiC.= "WHERE PAIIDXXX =\"{$vDatTer['PAIID3XX']}\" LIMIT 0,1";
				$xDatPaiC = f_MySql("SELECT","",$qDatPaiC,$xConexion01,"");
				while($xDPC = mysql_fetch_array($xDatPaiC)){
					$cPaisDesC = $xDPC['PAIDESXX']." "."(".$xDPC['PAIIDNXX'].")";
				}

				/*TRAIGO DATOS DE DEPARTAMENTO CORRESPONDENCIA*/
				$qDatDepC = "SELECT * ";
				$qDatDepC.= "FROM $cAlfa.SIAI0054 ";
				$qDatDepC.= "WHERE DEPIDXXX =\"{$vDatTer['DEPID3XX']}\" LIMIT 0,1";
				$xDatDepC = f_MySql("SELECT","",$qDatDepC,$xConexion01,"");
				while($xDDC = mysql_fetch_array($xDatDepC)){
					$cDeptoDesC = $xDDC['DEPDESXX'];
				}

				/*TRAIGO DATOS DE CIUDAD CORRESPONDENCIA*/
				$qDatCiuC = "SELECT * ";
				$qDatCiuC.= "FROM $cAlfa.SIAI0055 ";
				$qDatCiuC.= "WHERE DEPIDXXX =\"{$vDatTer['DEPID3XX']}\" AND CIUIDXXX =\"{$vDatTer['CIUID3XX']}\" LIMIT 0,1";
				$xDatCiuC = f_MySql("SELECT","",$qDatCiuC,$xConexion01,"");
				while($xDCC = mysql_fetch_array($xDatCiuC)){
					$cCiudDesC = $xDCC['CIUDESXX'];
				}

				/*TRAIGO DATOS DE ACTIVIDAD ECONOMICA*/
				$qDatAec = "SELECT * ";
				$qDatAec.= "FROM $cAlfa.SIAI0101 ";
				$qDatAec.= "WHERE AECIDXXX =\"{$vDatTer['AECIDXXX']}\" LIMIT 0,1";
				$xDatAec = f_MySql("SELECT","",$qDatAec,$xConexion01,"");
				while($xDA = mysql_fetch_array($xDatAec)){
					$cAecoDes = $xDA['AECDESXX'];
					$cAecoRet = $xDA['AECRETXX'];
				}

				/*TRAIGO DATOS DE LA CTO DE LA TASA PACTADA*/
				$qDatTp = "SELECT pucidxxx, ctoidxxx, IF(ctodesxp != \"\",ctodesxp,ctodesxx) AS ctodesxp ";
				$qDatTp.= "FROM $cAlfa.fpar0119 ";
				$qDatTp.= "WHERE ctoidxxx =\"{$vDatTer['CLITPCTO']}\" LIMIT 0,1";
				$xDatTp = f_MySql("SELECT","",$qDatTp,$xConexion01,"");
				while($xDTP = mysql_fetch_array($xDatTp)){
					$cTpDes = $xDTP['ctodesxp'];
					$cTpPuc = $xDTP['pucidxxx'];
				}

				/*TRAIGO DATOS DE DISCONFORMIDAD*/
				$qDisconf = "SELECT ";
				$qDisconf.= "disidxxx,";
				$qDisconf.= "disdesxx ";
				$qDisconf.= "FROM $cAlfa.fpar0160 ";
				$qDisconf.= "WHERE ";
				$qDisconf.= "disidxxx = \"{$vDatTer['CLIDISID']}\" LIMIT 0,1";
				$xDisconf = f_MySql("SELECT","",$qDisconf,$xConexion01,"");
				while($xDI = mysql_fetch_array($xDisconf)){
					$cDiscDes = $xDI['disdesxx'];
				}

				/* TRAIGO DATOS DEL BANCO */
				if ($vDatTer['CLIBANID'] != "") {
					$cDesBanc = '';
					$qDatBanc = "SELECT bandesxx ";
					$qDatBanc.= "FROM $cAlfa.fpar0124 ";
					$qDatBanc.= "WHERE banidxxx =\"{$vDatTer['CLIBANID']}\" LIMIT 0,1";
					$xDatBanc= f_MySql("SELECT","",$qDatBanc,$xConexion01,"");
					while($xRDB = mysql_fetch_array($xDatBanc)){
						$cDesBanc = $xRDB['bandesxx'];
					}
				}
			?>
			<script language = "javascript">
				document.forms['frgrm']['cTerId'].value		 = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIIDXXX']) ?>";
				document.forms['frgrm']['cTerNom'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLINOMXX']) ?>";
				document.forms['frgrm']['cTerPNom'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLINOM1X']) ?>";
				document.forms['frgrm']['cTerSNom'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLINOM2X']) ?>";
				document.forms['frgrm']['cTerPApe'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIAPE1X']) ?>";
				document.forms['frgrm']['cTerSApe'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIAPE2X']) ?>";
				document.forms['frgrm']['cTerTel'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLITELXX']) ?>";
				document.forms['frgrm']['cTerFax'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIFAXXX']) ?>";
				document.forms['frgrm']['cTerDir'].value	 = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIDIRXX']) ?>";
				document.forms['frgrm']['cTerCPosF'].value = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLICPOSX']) ?>";
				document.forms['frgrm']['cTerEma'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIEMAXX']) ?>";
				document.forms['frgrm']['dFecCre'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['REGFECXX']) ?>";
				document.forms['frgrm']['dHorCre'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['REGHORXX']) ?>";
				document.forms['frgrm']['cEstado'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['REGESTXX']) ?>";
				document.forms['frgrm']['cTpeId'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLITPERX']) ?>";
				document.forms['frgrm']['cTerNomC'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLINOMCX']) ?>";
				document.forms['frgrm']['cTdiId'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['TDIIDXXX']) ?>";
				document.forms['frgrm']['cTdiDes'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$cTdDes) ?>";
				document.forms['frgrm']['cPaiId'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['PAIIDXXX']) ?>";
				document.forms['frgrm']['cPaiDes'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$cPaisDes) ?>";
				document.forms['frgrm']['cDepId'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['DEPIDXXX']) ?>";
				document.forms['frgrm']['cDepDes'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$cDeptoDes) ?>";
				document.forms['frgrm']['cCiuId'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CIUIDXXX']) ?>";
				document.forms['frgrm']['cCiuDes'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$cCiudDes) ?>";
				document.forms['frgrm']['cTerApar'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIAPAXX']) ?>";
				document.forms['frgrm']['cGruId'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['GRUIDXXX']) ?>";
				document.forms['frgrm']['cGruDes'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$cGruCDes) ?>";
				document.forms['frgrm']['cPaiId1'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['PAIID3XX']) ?>";
				document.forms['frgrm']['cPaiDes1'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$cPaisDesC) ?>";
				document.forms['frgrm']['cDepId1'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['DEPID3XX']) ?>";
				document.forms['frgrm']['cDepDes1'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$cDeptoDesC) ?>";
				document.forms['frgrm']['cCiuId1'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CIUID3XX']) ?>";
				document.forms['frgrm']['cCiuDes1'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$cCiudDesC) ?>";
				document.forms['frgrm']['cTerDirC'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIDIR3X']) ?>";
				document.forms['frgrm']['cTerCPosC'].value = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLICPOS3']) ?>";
				document.forms['frgrm']['cTerFPa'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIFORPX']) ?>";
				document.forms['frgrm']['cTerPla'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIPLAXX']) ?>";
				document.forms['frgrm']['cTerMedP'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIMEDPX']) ?>";
				document.forms['frgrm']['cAecId'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['AECIDXXX']) ?>";
				document.forms['frgrm']['cAecDes'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$cAecoDes) ?>";
				document.forms['frgrm']['cAecRet'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,(($cAecoRet > 0) ? $cAecoRet+0 : "")) ?>";
				document.forms['frgrm']['cTerUapco'].value = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIUAPXX']) ?>";
				document.forms['frgrm']['cTerAltex'].value = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIALTEX']) ?>";
				document.forms['frgrm']['cTerObs'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIOBSXX']) ?>";

				document.forms['frgrm']['cCliArAis'].value = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIARAIS']) ?>";
				document.forms['frgrm']['cCliArrIs'].value = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIARRIS']) ?>";
        document.forms['frgrm']['cCliNsrris'].value= "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLINICAS']) ?>";

				document.forms['frgrm']['cCliCto'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLICTOXX']) ?>";
				document.forms['frgrm']['cCliIdCor'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIIDCOR']) ?>";
				document.forms['frgrm']['cCliIdProv'].value = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIIDCPX']) ?>";
				document.forms['frgrm']['cCliIdApr'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIIDAPX']) ?>";

				document.forms['frgrm']['cCliDrl'].value  	= "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIDRLXX']) ?>";
				document.forms['frgrm']['cCliVen'].value  	= "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIVENXX']) ?>";
				document.forms['frgrm']['cCliCon'].value  	= "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLICONTX']) ?>";
				document.forms['frgrm']['cCliResFi'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIRESFI']) ?>";
				document.forms['frgrm']['cCliTri'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLITRIBU']) ?>";
				
				switch("<?php echo $cAlfa ?>"){
					case "TESIACOSIP":
					case "DESIACOSIP":
					case "SIACOSIA":
						document.forms['frgrm']['cCliCueBa'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLICUEBA']) ?>";
						document.forms['frgrm']['cCliHPCnx'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIHPCNX']) ?>";
						document.forms['frgrm']['cCliEmaSi'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIEMASI']) ?>";
            document.forms['frgrm']['cCliBavCn'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIBAVCN']) ?>";
					break;
				}
				
        document.forms['frgrm']['cCliPCECn'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIPCECN']) ?>";
        document.forms['frgrm']['cTerMaMer'].value 	= "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIMMERX']) ?>";
        document.forms['frgrm']['cTerIdPer'].value 	= "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIIDPER']) ?>";

        switch("<?php echo $cAlfa ?>"){
					case "DSVSASXX":
					case "TEDSVSASXX":
					case "DEDSVSASXX":
						document.forms['frgrm']['cClicWccX'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLICWCCX']) ?>";
						document.forms['frgrm']['cClifRccX'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIFRCCX']) ?>";
					break;
				}

        switch("<?php echo $cAlfa ?>"){
					case "TEGRUMALCO":
					case "DEGRUMALCO":
					case "GRUMALCO":
						document.forms['frgrm']['cCliCnrRf'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLICNRRF']) ?>";
            document.forms['frgrm']['cCliAcuPa'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIACUPA']) ?>";
            document.forms['frgrm']['cCliEstGm'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIESTGM']) ?>";
						document.forms['frgrm']['cDiscId'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIDISID']) ?>";
						document.forms['frgrm']['cDiscDes'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$cDiscDes) ?>";
						document.forms['frgrm']['rCliFacCel'].value = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIFACEL']) ?>";
						document.forms['frgrm']['cCliVer'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIVERDI']) ?>";
						document.forms['frgrm']['cCliOper'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIOPERA']) ?>";
						document.forms['frgrm']['cCliFec'].value    = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIFECFE']) ?>";
						document.forms['frgrm']['cCliCoEmi'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLICOEMI']) ?>";
						document.forms['frgrm']['cCliCoRep'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLICOREP']) ?>";
            fnHabilitarFacturadorElectronico(); //Mostrando formulario de Facturador electronico
					break;
				}

				switch("<?php echo $cAlfa ?>"){
					case "ROLDANLO":
					case "TEROLDANLO":
					case "DEROLDANLO":
						document.forms['frgrm']['cCliRolCc'].value = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIROLCC']) ?>";
            document.forms['frgrm']['cCliMon'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIMONXX']) ?>";
					break;
          case "ADUANERA":
					case "TEADUANERA":
					case "DEADUANERA":
						document.forms['frgrm']['cCliOrCom'].value = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIORCOM']) ?>";
					break;
					default:
						// no hace nada
					break;
				}

				f_GenDv("<?php echo $vDatTer['CLIIDXXX'] ?>");

				// f_Valida_Pais("<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['PAIIDXXX']) ?>","cPaiId");
				// f_Valida_Pais("<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['PAIID3XX']) ?>","cPaiId1");

				document.forms['frgrm']['cCliTp'].value 		= "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLITPXXX']) ?>";
				document.forms['frgrm']['cCliTpCto'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLITPCTO']) ?>";
				document.forms['frgrm']['cCliTpDes'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$cTpDes) ?>";
				document.forms['frgrm']['cCliTpPuc'].value  = "<?php echo str_replace($cBuscar01,$cReempl01,$cTpPuc) ?>";

				f_Mostrar_Conceptos();
			</script>
			<!-- Prendiendo Checks -->
			<?php if ($vDatTer['CLICLIXX'] == "SI" || $vDatTer['TERCLIXX'] == "SI") { ?>
				<script language = "javascript">
				document.forms['frgrm']['vChCli'].checked = true;
				document.forms['frgrm']['vChCliCli'].value = 1;
				</script>
			<?php } ?>
			<?php if ($vDatTer['CLIPROCX'] == "SI") { ?>
				<script language = "javascript">
				document.forms['frgrm']['vChProC'].checked = true;
				</script>
			<?php } ?>
			<?php if ($vDatTer['CLIPROEX'] == "SI") { ?>
				<script language = "javascript">
				document.forms['frgrm']['vChProE'].checked = true;
				</script>
			<?php } ?>
			<?php if ($vDatTer['CLIEMPXX'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['vChEmp'].checked = true;
				</script>
			<?php } ?>
			<?php if ($vDatTer['CLIVENCO'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['vChCliVenCo'].checked = true;
				</script>
			<?php } ?>
			<?php if ($vDatTer['CLISOCXX'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['vChSoc'].checked = true;
				</script>
			<?php } ?>
			<?php if ($vDatTer['CLIEFIXX'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['vChEfi'].checked = true;
				</script>
			<?php } ?>
			<?php if ($vDatTer['CLIOTRXX'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['vChOtr'].checked = true;
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLITCONX'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['vChCon'].checked = true;
				</script>
			<?php } ?>

			<script language = "javascript">
			f_Mostrar(); //Mostrando el fielset de conceptos contables
			</script>

			<?php
			//Condiciones Tributarias
			if ($vDatTer['CLIREIVA'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliReIva'].checked = true;
					document.forms['frgrm']['oCliReIva'].value = "SI";
					document.forms['frgrm']['oCliReIva'].disabled = false;
					document.getElementById('tblCliReIva').style.display='block';
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliReIva'].checked = false;
					document.forms['frgrm']['oCliReIva'].value = "NO";
					document.getElementById('tblCliReIva').style.display='none';
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLIRECOM'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliReg'].disabled = false;
					document.getElementById('oCliReCom').checked = true;
					document.getElementById('oCliReSim').checked = false;
					document.forms['frgrm']['oCliReg'].value = "COMUN";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLIRESIM'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliReg'].disabled = false;
					document.getElementById('oCliReCom').checked = false;
					document.getElementById('oCliReSim').checked = true;
					document.forms['frgrm']['oCliReg'].value = "SIMPLIFICADO";

					document.forms['frgrm']['oCliAr'].checked  = false;
					document.forms['frgrm']['oCliAr'].disabled = true;

					document.forms['frgrm']['oCliNrp'].checked = false;
					document.forms['frgrm']['oCliNrp'].disabled = true;

					document.forms['frgrm']['oCliArr'].checked = false;
					document.forms['frgrm']['oCliArr'].disabled = true;

					document.forms['frgrm']['oCliAriva'].checked = false;
					document.forms['frgrm']['oCliAriva'].disabled = true;

					document.forms['frgrm']['oCliArcr'].checked = false;
					document.forms['frgrm']['oCliArcr'].disabled = true;

					document.forms['frgrm']['oCliArrI'].checked = false;
					document.forms['frgrm']['oCliArrI'].disabled = true;

					document.forms['frgrm']['oCliGc'].checked  = false;

					document.forms['frgrm']['oCliPci'].checked  = false;
					document.forms['frgrm']['oCliPci'].disabled = true;
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLIREGST'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliRegST'].checked = true;
					document.forms['frgrm']['oCliRegST'].value   = "SI";
					
					document.forms['frgrm']['oCliNsrr'].checked  = false;
					document.forms['frgrm']['oCliArr'].checked   = false;
					document.forms['frgrm']['oCliGc'].checked    = false;
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliRegST'].checked = false;
					document.forms['frgrm']['oCliRegST'].value = "NO";
				</script>
			<?php } ?>
			
			<?php if ($vDatTer['CLIGCXXX'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliGc'].disabled = false;
					document.forms['frgrm']['oCliGc'].checked = true;
					document.forms['frgrm']['oCliGc'].value = "SI";
					document.forms['frgrm']['oCliReIva'].checked  = true;
					document.forms['frgrm']['oCliReIva'].disabled = false;
					document.getElementById('oCliReCom').checked  = true;
					document.getElementById('oCliReSim').checked  = false;
					document.forms['frgrm']['oCliRegST'].checked  = false;
					document.getElementById('oCliReCom').disabled = false;
					document.getElementById('oCliReSim').disabled = false;
					document.forms['frgrm']['oCliRegST'].disabled  = false;
					document.forms['frgrm']['oCliReg'].value      = "COMUN";
					document.getElementById('tblCliReIva').style.display='block';
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliGc'].checked = false;
					document.forms['frgrm']['oCliGc'].value = "NO";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLINRPXX'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNrp'].disabled = false;
					document.forms['frgrm']['oCliNrp'].checked = true;
					document.forms['frgrm']['oCliNrp'].value = "SI";
					document.getElementById('tblCliNrpai').style.display='block';

					document.forms['frgrm']['oCliReIva'].checked  = false;
					document.forms['frgrm']['oCliReIva'].disabled = true;

					document.forms['frgrm']['oCliGc'].checked  = false;
					document.forms['frgrm']['oCliGc'].disabled = true;

					document.forms['frgrm']['oCliAr'].checked  = false;
					document.forms['frgrm']['oCliAr'].disabled = true;

					document.forms['frgrm']['oCliNsrr'].checked  = false;
					document.forms['frgrm']['oCliNsrr'].disabled = true;

					document.forms['frgrm']['oCliNsriv'].checked  = false;
					document.forms['frgrm']['oCliNsriv'].disabled = true;

					document.forms['frgrm']['oCliNsrri'].checked  = false;
					document.forms['frgrm']['oCliNsrri'].disabled = true;

					document.forms['frgrm']['oCliNsrcr'].checked  = false;
					document.forms['frgrm']['oCliNsrcr'].disabled = true;

					document.forms['frgrm']['oCliArr'].checked  = false;
					document.forms['frgrm']['oCliArr'].disabled = true;

					document.forms['frgrm']['oCliAriva'].checked  = false;
					document.forms['frgrm']['oCliAriva'].disabled = true;

					document.forms['frgrm']['oCliArcr'].checked  = false;
					document.forms['frgrm']['oCliArcr'].disabled = true;

					document.forms['frgrm']['oCliArrI'].checked  = false;
					document.forms['frgrm']['oCliArrI'].disabled = true;

					document.forms['frgrm']['oCliPci'].checked  = false;
					document.forms['frgrm']['oCliPci'].disabled = true;
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNrp'].checked = false;
					document.forms['frgrm']['oCliNrp'].value = "NO";
					document.getElementById('tblCliNrpai').style.display='none';
          if ("<?php echo $cAlfa ?>" == 'ROLDANLO' || "<?php echo $cAlfa ?>" == 'DEROLDANLO' || "<?php echo $cAlfa ?>" == 'TEROLDANLO') {
						document.getElementById('tblNoResidente').style.display='none';
					}
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLINRPAI'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNrpai'].disabled = false;
					document.forms['frgrm']['oCliNrpai'].checked = true;
					document.forms['frgrm']['oCliNrpai'].value = "SI";
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNrpai'].checked = false;
					document.forms['frgrm']['oCliNrpai'].value = "NO";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLINRPIF'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNrpif'].disabled = false;
					document.forms['frgrm']['oCliNrpif'].checked = true;
					document.forms['frgrm']['oCliNrpif'].value = "SI";
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNrpif'].checked = false;
					document.forms['frgrm']['oCliNrpif'].value = "NO";
				</script>
			<?php } ?>

      <?php if ($vDatTer['CLINRNSR'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNrpNsr'].disabled = false;
					document.forms['frgrm']['oCliNrpNsr'].checked = true;
					document.forms['frgrm']['oCliNrpNsr'].value = "SI";
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNrpNsr'].checked = false;
					document.forms['frgrm']['oCliNrpNsr'].value = "NO";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLIARXXX'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliAr'].disabled = false;
					document.forms['frgrm']['oCliAr'].checked = true;
					document.forms['frgrm']['oCliAr'].value = "SI";
					document.getElementById('tblCliAr').style.display='block';
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliAr'].checked = false;
					document.forms['frgrm']['oCliAr'].value = "NO";
					document.getElementById('tblCliAr').style.display='none';
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLIARARE'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArAre'].disabled = false;
					document.forms['frgrm']['oCliArAre'].checked = true;
					document.forms['frgrm']['oCliArAre'].value = "SI";
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArAre'].checked = false;
					document.forms['frgrm']['oCliArAre'].value = "NO";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLIARAIV'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArAiv'].disabled = false;
					document.forms['frgrm']['oCliArAiv'].checked = true;
					document.forms['frgrm']['oCliArAiv'].value = "SI";
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArAiv'].checked = false;
					document.forms['frgrm']['oCliArAiv'].value = "NO";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLIARAIC'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArAic'].disabled = false;
					document.forms['frgrm']['oCliArAic'].checked = true;
					document.forms['frgrm']['oCliArAic'].value = "SI";
					document.getElementById('tblCliArAic').style.display='block';
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArAic'].checked = false;
					document.forms['frgrm']['oCliArAic'].value = "NO";
					document.getElementById('tblCliArAic').style.display='none';
					document.forms['frgrm']['cCliArAis'].value = "";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLIARACR'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArAcr'].disabled = false;
					document.forms['frgrm']['oCliArAcr'].checked = true;
					document.forms['frgrm']['oCliArAcr'].value = "SI";
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArAcr'].checked = false;
					document.forms['frgrm']['oCliArAcr'].value = "NO";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLINSRRX'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNsrr'].disabled = false;
					document.forms['frgrm']['oCliNsrr'].checked = true;
					document.forms['frgrm']['oCliNsrr'].value = "SI";
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNsrr'].checked = false;
					document.forms['frgrm']['oCliNsrr'].value = "NO";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLINSRIV'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNsriv'].disabled = false;
					document.forms['frgrm']['oCliNsriv'].checked = true;
					document.forms['frgrm']['oCliNsriv'].value = "SI";
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNsriv'].checked = false;
					document.forms['frgrm']['oCliNsriv'].value = "NO";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLINSRRI'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNsrri'].disabled = false;
					document.forms['frgrm']['oCliNsrri'].checked = true;
					document.forms['frgrm']['oCliNsrri'].value = "SI";
          document.forms['frgrm']['oCliNsrris'].disabled = false;
					document.getElementById('oCliNsrris').style.display='block';
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNsrri'].checked = false;
					document.forms['frgrm']['oCliNsrri'].value = "NO";
          document.forms['frgrm']['oCliNsrris'].disabled = true;
					document.getElementById('oCliNsrris').style.display='none';
					document.forms['frgrm']['cCliNsrris'].value = "";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLINSRCR'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNsrcr'].disabled = false;
					document.forms['frgrm']['oCliNsrcr'].checked = true;
					document.forms['frgrm']['oCliNsrcr'].value = "SI";
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNsrcr'].checked = false;
					document.forms['frgrm']['oCliNsrcr'].value = "NO";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLIARRXX'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArr'].disabled = false;
					document.forms['frgrm']['oCliArr'].checked = true;
					document.forms['frgrm']['oCliArr'].value = "SI";
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArr'].checked = false;
					document.forms['frgrm']['oCliArr'].value = "NO";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLIARIVA'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliAriva'].disabled = false;
					document.forms['frgrm']['oCliAriva'].checked = true;
					document.forms['frgrm']['oCliAriva'].value = "SI";
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliAriva'].checked = false;
					document.forms['frgrm']['oCliAriva'].value = "NO";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLIARCRX'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArcr'].disabled = false;
					document.forms['frgrm']['oCliArcr'].checked = true;
					document.forms['frgrm']['oCliArcr'].value = "SI";
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArcr'].checked = false;
					document.forms['frgrm']['oCliArcr'].value = "NO";
				</script>
			<?php } ?>

			<?php if ($vDatTer['CLIARRIX'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArrI'].disabled = false;
					document.forms['frgrm']['oCliArrI'].checked = true;
					document.forms['frgrm']['oCliArrI'].value = "SI";
					document.forms['frgrm']['oCliArrIs'].disabled = false;
					document.getElementById('oCliArrIs').style.display='block';
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliArrI'].checked = false;
					document.forms['frgrm']['oCliArrI'].value = "NO";
					document.forms['frgrm']['oCliArrIs'].disabled = true;
					document.getElementById('oCliArrIs').style.display='none';
					document.forms['frgrm']['cCliArrIs'].value = "";
				</script>
			<?php } ?>

      <?php if ($vDatTer['CLIPCIXX'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliPci'].disabled = false;
					document.forms['frgrm']['oCliPci'].checked = true;
					document.forms['frgrm']['oCliPci'].value = "SI";

					document.forms['frgrm']['oCliReIva'].checked  = true;
					document.forms['frgrm']['oCliReIva'].disabled = false;
					document.getElementById('oCliReCom').checked  = true;
					document.getElementById('oCliReSim').checked  = false;
					document.getElementById('oCliReCom').disabled = false;
					document.getElementById('oCliReSim').disabled = false;
					document.forms['frgrm']['oCliReg'].value      = "COMUN";
					document.getElementById('tblCliReIva').style.display='block';
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliPci'].checked = false;
					document.forms['frgrm']['oCliPci'].value = "NO";
				</script>
			<?php } ?>

      <?php if ($vDatTer['CLINSOFE'] == "SI") { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNsOfe'].disabled = false;
					document.forms['frgrm']['oCliNsOfe'].checked = true;
					document.forms['frgrm']['oCliNsOfe'].value = "SI";
				</script>
			<?php } else { ?>
				<script language = "javascript">
					document.forms['frgrm']['oCliNsOfe'].checked = false;
					document.forms['frgrm']['oCliNsOfe'].value = "NO";
				</script>
			<?php } ?>

			<!-- Fin de Prendiendo Checks -->
			<?php
				## Carga de Código SAP ##
				switch ($cAlfa) {
					case "TEALMACAFE":
					case "DEALMACAFE":
          case "ALMACAFE":
          case "DEALPOPULX":
          case "TEALPOPULP":
          case "ALPOPULX":
          case "DEALMAVIVA":
          case "TEALMAVIVA":
          case "ALMAVIVA":
					?>
						<script language = "javascript">
							document.forms['frgrm']['cCliSap'].value = <?php echo $vDatTer['CLISAPXX'] ?>;
						</script>
					<?php
					break;
					default:
						// no hace nada
					break;
				}
				## FIN Carga de Código SAP

				#Validacion Cuenta IMP
				switch ($cAlfa) {
          case "TEDHLEXPRE":
          case "DEDHLEXPRE":
          case "DHLEXPRE":
            $mCuentasCs = f_explode_array($vDatTer['CLIIMPCS'],"|","~");
						$nCanCueCs  = 0;?>
            <script>
							document.forms['frgrm']['cCliCecNc'].value = "<?php echo $vDatTer['CLICECNC'] ?>";
						</script>
            <?php
            for ($i=0;$i<count($mCuentasCs);$i++) {
              if ($mCuentasCs[$i][0] != "") { 
								$nCanCueCs++; ?>
                <script languaje = "javascript">
                  fnAddNewRowImp('Grid_ImpCash');
                  document.forms['frgrm']['cCuentaGrid_ImpCash' + document.forms['frgrm']['nSecuencia_Grid_ImpCash'].value].value     = "<?php echo $mCuentasCs[$i][0] ?>"
                  document.forms['frgrm']['cEstadoGrid_ImpCash' + document.forms['frgrm']['nSecuencia_Grid_ImpCash'].value].value     = "<?php echo $mCuentasCs[$i][1] ?>";
                  if ("<?php echo $_COOKIE['kModo'] ?>" == "VER") {
                    document.forms['frgrm']['cEstadoGrid_ImpCash' + document.forms['frgrm']['nSecuencia_Grid_ImpCash'].value].disabled = true;
                    document.forms['frgrm']['oBtnDelGrid_ImpCash' + document.forms['frgrm']['nSecuencia_Grid_ImpCash'].value].disabled = true;
                  }
                </script>
              <?php
              }
            }
						if ($nCanCueCs == 0) { ?>
							<script languaje = "javascript">
								fnAddNewRowImp('Grid_ImpCash');
							</script>
            <?php }

            $mCuentasCr = f_explode_array($vDatTer['CLIIMPCR'],"|","~");
						$nCanCueCr  = 0;
            for ($i=0;$i<count($mCuentasCr);$i++) {
              if ($mCuentasCr[$i][0] != "") { 
								$nCanCueCr++; ?>
                <script languaje = "javascript">
                  fnAddNewRowImp('Grid_ImpCre');
                  document.forms['frgrm']['cCuentaGrid_ImpCre' + document.forms['frgrm']['nSecuencia_Grid_ImpCre'].value].value     = "<?php echo $mCuentasCr[$i][0] ?>"
                  document.forms['frgrm']['cEstadoGrid_ImpCre' + document.forms['frgrm']['nSecuencia_Grid_ImpCre'].value].value     = "<?php echo $mCuentasCr[$i][1] ?>";
                  if ("<?php echo $_COOKIE['kModo'] ?>" == "VER") {
                    document.forms['frgrm']['cEstadoGrid_ImpCre' + document.forms['frgrm']['nSecuencia_Grid_ImpCre'].value].disabled = true;
                    document.forms['frgrm']['oBtnDelGrid_ImpCre' + document.forms['frgrm']['nSecuencia_Grid_ImpCre'].value].disabled = true;
                  }
                </script>
              <?php
              }
            }
						if ($nCanCueCr == 0) { ?>
							<script languaje = "javascript">
								fnAddNewRowImp('Grid_ImpCre');
							</script>
            <?php } ?>
						<script languaje = "javascript">
							document.forms['frgrm']['cBanId'].value	   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIBANID']) ?>";
							document.forms['frgrm']['cBanDes'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$cDesBanc) ?>";
							document.forms['frgrm']['cTipCta'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLITIPCU']) ?>";
							document.forms['frgrm']['cBanCta'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLINUMCU']) ?>";
							document.forms['frgrm']['cEstCta'].value   = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIESTCU']) ?>";
              document.forms['frgrm']['cCliEnPrF'].value = "<?php echo str_replace($cBuscar01,$cReempl01,$vDatTer['CLIENPRF']) ?>";
						</script>
						<?php
					break;
          case "TEROLDANLO":
            case "DEROLDANLO":
            case "ROLDANLO":
              #Condiciones Tributarias No Residente en el Pais para Facturacion
              if ($vDatTer['CLIARNRX'] == "SI") { ?>
                <script language = "javascript">
                  document.forms['frgrm']['oCliArNr'].disabled = false;
                  document.forms['frgrm']['oCliArNr'].checked = true;
                  document.forms['frgrm']['oCliArNr'].value = "SI";
                  document.getElementById('tblCliArNr').style.display='block';
                </script>
              <?php } else { ?>
                <script language = "javascript">
                  document.forms['frgrm']['oCliArNr'].checked = false;
                  document.forms['frgrm']['oCliArNr'].value = "NO";
                  document.getElementById('tblCliArNr').style.display='none';
                </script>
              <?php }
  
              if ($vDatTer['CLIARENR'] == "SI") { ?>
                <script language = "javascript">
                  document.forms['frgrm']['oCliArAreNr'].disabled = false;
                  document.forms['frgrm']['oCliArAreNr'].checked = true;
                  document.forms['frgrm']['oCliArAreNr'].value = "SI";
                </script>
              <?php } else { ?>
                <script language = "javascript">
                  document.forms['frgrm']['oCliArAreNr'].checked = false;
                  document.forms['frgrm']['oCliArAreNr'].value = "NO";
                </script>
              <?php }
  
              if ($vDatTer['CLIACRNR'] == "SI") { ?>
                <script language = "javascript">
                  document.forms['frgrm']['oCliArAcrNr'].disabled = false;
                  document.forms['frgrm']['oCliArAcrNr'].checked = true;
                  document.forms['frgrm']['oCliArAcrNr'].value = "SI";
                </script>
              <?php } else { ?>
                <script language = "javascript">
                  document.forms['frgrm']['oCliArAcrNr'].checked = false;
                  document.forms['frgrm']['oCliArAcrNr'].value = "NO";
                </script>
              <?php }
  
              if ($vDatTer['CLIARRNR'] == "SI") { ?>
                <script language = "javascript">
                  document.forms['frgrm']['oCliArrNr'].disabled = false;
                  document.forms['frgrm']['oCliArrNr'].checked = true;
                  document.forms['frgrm']['oCliArrNr'].value = "SI";
                </script>
              <?php } else { ?>
                <script language = "javascript">
                  document.forms['frgrm']['oCliArrNr'].checked = false;
                  document.forms['frgrm']['oCliArrNr'].value = "NO";
                </script>
              <?php } ?>
  
              <?php if ($vDatTer['CLIRCRNR'] == "SI") { ?>
                <script language = "javascript">
                  document.forms['frgrm']['oCliArcrNr'].disabled = false;
                  document.forms['frgrm']['oCliArcrNr'].checked = true;
                  document.forms['frgrm']['oCliArcrNr'].value = "SI";
                </script>
              <?php } else { ?>
                <script language = "javascript">
                  document.forms['frgrm']['oCliArcrNr'].checked = false;
                  document.forms['frgrm']['oCliArcrNr'].value = "NO";
                </script>
              <?php }
              #FIN VCondiciones Tributarias No Residente en el Pais para Facturacion
            break;
					default:
						// no hace nada
					break;
				}
				#FIN Validacion Cuenta IMP
			}
		} ?>
	</body>
</html>
<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
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
    	
    	function f_Imprimir() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion

				var nSwitch = 0; var cMsj = "\n";
				
				if(document.forms['frgrm']['cComId'].value == "") {
					nSwitch = 1; 
					cMsj    = "El tipo de Comprobante del DO no puede ser vacio.\n";
				}
    	  if(document.forms['frgrm']['cComCod'].value == "") {
					nSwitch = 1; 
					cMsj    = "El Codigo de Comprobante del DO no puede ser vacio.\n";
				}
    	  if(document.forms['frgrm']['cSucId'].value == "") {
					nSwitch = 1; 
					cMsj    = "La Sucursal del DO no puede ser vacio.\n";
				}
				if(document.forms['frgrm']['cDocTip'].value == "") {
					nSwitch = 1; 
					cMsj    = "El Tipo de Operacion del DO no puede ser vacio.\n";
				}
				if(document.forms['frgrm']['cDocNro'].value == "") {
					nSwitch = 1; 
					cMsj    = "El numero del DO no puede ser vacio.\n";
				}
				if(document.forms['frgrm']['cDocSuf'].value == "") {
					nSwitch = 1; 
					cMsj    = "El Sufijo del DO no puede ser vacio.\n";
				}
				if(document.forms['frgrm']['cPucId'].value == "") {
					nSwitch = 1; 
					cMsj    = "La Cuenta PUC del DO no puede ser vacio.\n";
				}
				if(document.forms['frgrm']['cCcoId'].value == "") {
					nSwitch = 1; 
					cMsj    = "El Centro de Costo del DO no puede ser vacio.\n";
				}
				if(document.forms['frgrm']['cCliId'].value == "") {
					nSwitch = 1; 
					cMsj    = "El Cliente del DO no puede ser vacio.\n";
				}
				if(document.forms['frgrm']['dRegFCre'].value == "") {
					nSwitch = 1; 
					cMsj    = "La Fecha de Creacion del DO no puede ser vacio.\n";
				}
				
    	  if(nSwitch == 0) {

			   	if(document.forms['frgrm']['cPyG'].checked == true){
				   	var cPyG = 1;
			   	}else{
			   		var cPyG = 0;
			   	}
				  
					 var cRuta = "frmdoprn.php?gComId="+document.forms['frgrm']['cComId'].value+
                                  "&gComCod="+document.forms['frgrm']['cComCod'].value+
					                        "&gSucId="+document.forms['frgrm']['cSucId'].value+
																  "&gDocTip="+document.forms['frgrm']['cDocTip'].value+
																  "&gDocId="+document.forms['frgrm']['cDocNro'].value+
																  "&gDocSuf="+document.forms['frgrm']['cDocSuf'].value+
																  "&gPucId="+document.forms['frgrm']['cPucId'].value+
																  "&gCcoId="+document.forms['frgrm']['cCcoId'].value+
																  "&gCliId="+document.forms['frgrm']['cCliId'].value+
																  "&gRegFCre="+document.forms['frgrm']['dRegFCre'].value+
																  "&gMov="+document.forms['frgrm']['cMov'].value+
																	"&gPyG="+cPyG;
          document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),
                           strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
          document.cookie="kMenDes=Imprimir;path="+"/";
          document.cookie="kModo=IMPRIMIR;path="+"/";
          parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
          document.location = cRuta; // Invoco el menu.
				} else {
					alert(cMsj+"Datos Incompletos en el DO, Verifique");
				}
	  	}

			function uLinks(xLink,xSwitch,xIteration) {
				if (document.forms['frgrm']['cDocNro'].value.length > 0){
					var zX    = screen.width;
					var zY    = screen.height;
					switch (xLink) {
						case "cDocNro":
							if (xSwitch == "VALID") {
								var zRuta  = "frmdo121.php?gWhat=VALID&gFunction=cDocNro&gDocNro="+document.forms['frgrm']['cDocNro'].value.toUpperCase()+
																	  "&gDocSuf="+document.forms['frgrm']['cDocSuf'].value;
								parent.fmpro.location = zRuta;
							} else {
				  			var zNx     = (zX-400)/2;
								var zNy     = (zY-250)/2;
								var zWinPro = 'width=400,scrollbars=1,height=250,left='+zNx+',top='+zNy;
								var zRuta  = "frmdo121.php?gWhat=WINDOW&gFunction=cDocNro&gDocNro="+document.forms['frgrm']['cDocNro'].value.toUpperCase();
								zWindow = window.open(zRuta,"zWindow",zWinPro);
						  	zWindow.focus();
							}
						break;
					}
				}
				else{
					alert("Debe ingresar al menos 1 caracter para la busqueda del DO");	
				}
			}
		</script>
  </head>
	<body>
		<form name = "frgrm" action = "frmovdo.php" method = "post" target="fmwork">
	  	<center>
	    	<table width="550" cellspacing="0" cellpadding="0" border="0"><tr><td>
			  	<fieldset>
		      	<legend><?php echo $_COOKIE['kProDes'] ?> </legend>
		        <table border="2" cellspacing="0" cellpadding="0" width="100%">
		        	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
			        	<td class="name" width="30%"><center><h5><br>REPORTE MOVIMIENTO DEL DO</h5></center></td>
			        </tr>
			       </table>
			       <table border="0" cellspacing="0" cellpadding="0" width="100%">
			        <tr>
			        	<td class="name"><br>Buscar Do :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			        	  <input type="hidden" class="letra" name="cComId"  style="width:30" readonly>
			        	  <input type="hidden" class="letra" name="cComCod" style="width:30" readonly>
			        		<input type="text"   class="letra" name="cSucId"  style="width:30" readonly>
			        		<input type="text"   class="letra" name="cDocTip" style="width:80" readonly>
			        		<input type="text"   class="letra" name="cDocNro" style="width:80"
			        				onblur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';
														   	uLinks('cDocNro','VALID');"
										  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';
														   document.forms['frgrm']['cComId'].value='';
														   document.forms['frgrm']['cComCod'].value='';
														   document.forms['frgrm']['cSucId'].value='';
														   document.forms['frgrm']['cDocTip'].value='';
														   document.forms['frgrm']['cDocNro'].value='';
														   document.forms['frgrm']['cDocSuf'].value='';
														   document.forms['frgrm']['cPucId'].value='';
														   document.forms['frgrm']['cCcoId'].value='';
														   document.forms['frgrm']['cCliId'].value='';
														   document.forms['frgrm']['dRegFCre'].value='';">
			        		<input type="text" class="letra"   name="cDocSuf" style="width:30" readonly>
			        		<input type="hidden" class="letra" name="cPucId">
			        		<input type="hidden" class="letra" name="cCcoId">
			        		<input type="hidden" class="letra" name="cCliId">
			        		<input type="hidden" class="letra" name="dRegFCre">
			        		&nbsp;&nbsp;Solo Aplica para Pantalla
			        	</td>
			        </tr>
			        <tr>
			        	<td class="name" width="30%"><br>Imprimir Movimiento por :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			        	  <select Class = "letrase" name = "cMov" style = "width:150">
										<option value = 'CONCEPTO' selected>CONCEPTO</option>
										<option value = 'CUENTA'>CUENTA</option>
									</select>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Incluir P&G&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="cPyG"> 
			        	</td> 
			        </tr>
		       </table>
		      </fieldset>
          <center>
						<table border="0" cellpadding="0" cellspacing="0" width="550">
							<tr height="21">
								<td width="370" height="21"></td>
								<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:f_Imprimir()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Imprimir</td>
								<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
							</tr>
						</table>
					</center>
	  </form>
  </body>
</html>
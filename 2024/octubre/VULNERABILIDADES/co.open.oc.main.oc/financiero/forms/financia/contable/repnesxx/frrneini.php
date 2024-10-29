<?php
  namespace openComex;
/**
 * Imprime Reporte Nestle.
* --- Descripcion: Permite Imprimir Reporte Nestle.
* @author Hector Fabio Mendoza <hector.mendoza@opentecnologia.com.co>
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
		<!--<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>-->
		<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script language="javascript">
  		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				parent.fmwork.location='<?php echo $cPlesk_Forms_Directory ?>/frproces.php';
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
    	}
     	function f_Links(xLink,xSwitch) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
				  case "cCliId":
					case "cCliNom":
						if (xSwitch == "VALID") {
							var cPathUrl = "frrne150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gCliTip=CLICLIXX"+
																				"&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase()+
																				"&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();
							parent.fmpro.location = cPathUrl;
						} else {
								var nNx      = (nX-600)/2;
								var nNy      = (nY-250)/2;
								var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
								var cPathUrl = "frrne150.php?gModo="+xSwitch+"&gFunction="+xLink+
																					 "&gCliTip=CLICLIXX"+
																					 "&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase()+
																					 "&gCliNom="+document.forms['frgrm']['cCliNom'].value.toUpperCase();								
								cWindow = window.open(cPathUrl,xLink,cWinOpt);
					  		cWindow.focus();
						}
					break;
					case "cSucId":
					case "cSucDes":
						if (xSwitch == "VALID") {
							var cPathUrl = "frrne119.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gSucId="+document.forms['frgrm']['cSucId'].value.toUpperCase()+
																				"&gSucDes="+document.forms['frgrm']['cSucDes'].value.toUpperCase();
							parent.fmpro.location = cPathUrl;
						} else {
								var nNx      = (nX-600)/2;
								var nNy      = (nY-250)/2;
								var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
								var cPathUrl = "frrne119.php?gModo="+xSwitch+"&gFunction="+xLink+
																					"&gSucId="+document.forms['frgrm']['cSucId'].value.toUpperCase()+
																					"&gSucDes="+document.forms['frgrm']['cSucDes'].value.toUpperCase();
								cWindow = window.open(cPathUrl,xLink,cWinOpt);
					  		cWindow.focus();
						}
					break;
					case "cTdbId":
					case "cTdbDes":
						var nSwicht = 0;
						if(document.forms['frgrm']['cCliId'].value == ''){
							nSwicht = 1;
							alert('Debe Seleccionar un Importador');
						}
						if (nSwicht == 0) {
							if (xSwitch == "VALID") {
								var cPathUrl = "frrne231.php?gModo="+xSwitch+"&gFunction="+xLink+
																					"&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase()+	
																					"&gTdbId="+document.forms['frgrm']['cTdbId'].value.toUpperCase()+
																					"&gTdbDes="+document.forms['frgrm']['cTdbDes'].value.toUpperCase();
								parent.fmpro.location = cPathUrl;
							} else {							
								var nNx      = (nX-600)/2;
								var nNy      = (nY-250)/2;
								var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
								var cPathUrl = "frrne231.php?gModo="+xSwitch+"&gFunction="+xLink+
																					"&gCliId="+document.forms['frgrm']['cCliId'].value.toUpperCase()+
																					"&gTdbId="+document.forms['frgrm']['cTdbId'].value.toUpperCase()+
																					"&gTdbDes="+document.forms['frgrm']['cTdbDes'].value.toUpperCase();
								cWindow = window.open(cPathUrl,xLink,cWinOpt);
					  		cWindow.focus();
							}
						}
					break;
					case "cMtrId":
					case "cMtrDes":
						if (xSwitch == "VALID") {
							var cPathUrl = "frrne120.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gMtrId="+document.forms['frgrm']['cMtrId'].value.toUpperCase()+
																				"&gMtrDes="+document.forms['frgrm']['cMtrDes'].value.toUpperCase();
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-600)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "frrne120.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gMtrId="+document.forms['frgrm']['cMtrId'].value.toUpperCase()+
																				"&gMtrDes="+document.forms['frgrm']['cMtrDes'].value.toUpperCase();
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
				  		cWindow.focus();							
						}
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

     	function f_Generar(){	
				var nSwitch = 0;
  			var cMsj = "";
	    	      
  			//Validando que si se escogio Fecha inicial del levante
  			if(document.forms['frgrm']['dFecIni'].value != "" && 
	  			document.forms['frgrm']['dFecFin'].value == ""){
  				nSwitch = 1;
        	cMsj += 'Debe Seleccionar Fecha Final del Levante.\n';
  			}

  			//Validando que si se escogio Fecha final del levante
  			if(document.forms['frgrm']['dFecIni'].value == "" && 
    			document.forms['frgrm']['dFecFin'].value != ""){
      		nSwitch = 1;
	      	cMsj += 'Debe Seleccionar Fecha Inicial del Levante.\n';
      	}

      	//Validando si la fechas son del mismo a�o
  			if(document.forms['frgrm']['dFecIni'].value != "" && 
    			document.forms['frgrm']['dFecFin'].value != ""){ 
  				if(document.forms['frgrm']['dFecIni'].value.substr(0,4) != document.forms['frgrm']['dFecFin'].value.substr(0,4)){
        		nSwitch = 1;
          	cMsj += 'Debe Seleccionar un Rango de Fechas del mismo A\u00f1o.\n';
        	}
    		} 

    		//Validando si se aplico un rango de fechas
  			if(document.forms['frgrm']['dFecIni'].value == "" && 
	    		document.forms['frgrm']['dFecFin'].value == ""){
        	nSwitch = 1;
	        cMsj += 'Debe Aplicar un Rango de Fechas.\n';
        }

  	  	//Validando que la fecha inicial sea menor a la fecha final

  			if(document.forms['frgrm']['dFecIni'].value != "" && 
  		  			document.forms['frgrm']['dFecFin'].value != ""){

  				if(Date.parse(document.forms['frgrm']['dFecIni'].value) > Date.parse( document.forms['frgrm']['dFecFin'].value)){
  					nSwitch = 1;
  			    cMsj += 'La Fecha Inicial Debe Ser Menor que la Fecha Final.\n';
  	    	}

				}	        
   			
		  	var cRuta = 'frrneprn.php';

		  	for (i=0;i<2;i++){
		    	if (document.forms['frgrm']['rTipo'][i].checked == true){
		  	  	cTipo = i+1;
		      	break;
		    	}
		  	}

			  if(nSwitch == 0){
				  var cRuta = 'frrneprn.php';
				  	if(cTipo == 2){
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
					alert(cMsj + "Verifique.");
				}
    }
	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
  <center>
    <table border ="0" cellpadding="0" cellspacing="0" width="500">
      <tr>
        <td>
          <form name='frgrm' action='frrneprn.php' method="post">
            <fieldset>
              <legend>Reporte Nestle</legend>
              <center>
                <table border="2" cellspacing="0" cellpadding="0" width="480">
                  <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
                    <td Class = "name" width="30%"><center><h4><br>REPORTE NESTLE</h4></center></td>
                  </tr>
                </table>
                <table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
                  <?php $nCol = f_Format_Cols(24);
                  echo $nCol;?>
                  <tr>
                    <td Class = "name" colspan = "08"><br>Desplegar en:</td>
                    <td Class = "name" colspan = "08"><br><input type="radio" name="rTipo" value="1" checked>Pantalla</td>
                    <td Class = "name" colspan = "08"><br><input type="radio" name="rTipo" value="2">Excel</td>
                  </tr>
                 </table>
                <table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
                  <?php $nCol = f_Format_Cols(24);
                  echo $nCol;?>
                  <tr>
                    <td Class = "name" colspan = "06"><br>
                      <a href = "javascript:document.forms['frgrm']['cCliId'].value  = '';
                                            document.forms['frgrm']['cCliDV'].value  = '';
                                            document.forms['frgrm']['cCliNom'].value = '';
                                            f_Links('cCliId','WINDOW')" id="id_href_cCliId">Importador:</a>
                    </td>
                    <td Class = "name" colspan = "05"><br>
                      <input type="text" name="cCliId" style = "width:100;text-align: center"
                            onfocus="javascript:document.forms['frgrm']['cCliId'].value  = '';
                                                document.forms['frgrm']['cCliDV'].value  = '';
                                                document.forms['frgrm']['cCliNom'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                f_Links('cCliId','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                    <td Class = "name" colspan = "01"><br>
                      <input type = "text" style = "width:20;text-align:center" name = "cCliDV" readonly>
                    </td>
                    <td Class = "name" colspan = "12"><br>
                      <input type="text" name="cCliNom" style = "width:240"
                            onfocus="javascript:this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                f_Links('cCliNom','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                  </tr>
                  <tr>
                    <td Class = "name" colspan = "06"><br>
                      <a href = "javascript:document.forms['frgrm']['cSucId'].value  = '';
                                            document.forms['frgrm']['cSucDes'].value = '';
                                            f_Links('cSucId','WINDOW')" id="cSucId">Sucursal:</a>
                    </td>
                    <td Class = "name" colspan = "05"><br>
                      <input type="text" name="cSucId" style = "width:100;text-align: center"
                            onfocus="javascript:document.forms['frgrm']['cSucId'].value  = '';
                                                document.forms['frgrm']['cSucDes'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                f_Links('cSucId','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                    <td Class = "name" colspan = "01"><br>
                      <input type = "text" style = "width:20;text-align:center" readonly>
                    </td>
                    <td Class = "name" colspan = "12"><br>
                      <input type="text" name="cSucDes" style = "width:240"
                            onfocus="javascript:this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                f_Links('cSucDes','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                  </tr>
                  <tr>
                    <td Class = "name" colspan = "06"><br>
                      <a href = "javascript:document.forms['frgrm']['cTdbId'].value  = '';
                                            document.forms['frgrm']['cTdbDes'].value = '';
                                            f_Links('cTdbId','WINDOW')" id="idTdbId">Tipo Bien:</a>
                    </td>
                    <td Class = "name" colspan = "05"><br>
                      <input type="text" name="cTdbId" style = "width:100;text-align: center"
                            onfocus="javascript:document.forms['frgrm']['cTdbId'].value  = '';
                                                document.forms['frgrm']['cTdbDes'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                f_Links('cTdbId','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                    <td Class = "name" colspan = "01"><br>
                      <input type = "text" style = "width:20;text-align:center" readonly>
                    </td>
                    <td Class = "name" colspan = "12"><br>
                      <input type="text" name="cTdbDes" style = "width:240"
                            onfocus="javascript:this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                f_Links('cTdbDes','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                  </tr>
                  <tr>
                    <td Class = "name" colspan = "06"><br>
                      <a href = "javascript:document.forms['frgrm']['cMtrId'].value  = '';
                                            document.forms['frgrm']['cMtrDes'].value = '';
                                            f_Links('cMtrId','WINDOW')" id="idTdbId">Modo de Transporte:</a>
                    </td>
                    <td Class = "name" colspan = "05"><br>
                      <input type="text" name="cMtrId" style = "width:100;text-align: center"
                            onfocus="javascript:document.forms['frgrm']['cMtrId'].value  = '';
                                                document.forms['frgrm']['cMtrDes'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                f_Links('cMtrId','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                    <td Class = "name" colspan = "01"><br>
                      <input type = "text" style = "width:20;text-align:center" readonly>
                    </td>
                    <td Class = "name" colspan = "12"><br>
                      <input type="text" name="cMtrDes" style = "width:240"
                            onfocus="javascript:this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                f_Links('cMtrDes','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                  </tr>
                  <tr>
	          	      	<td Class = "name" colspan = "08"><br>Rango de Fechas (Levante):</td>
	          	        <td Class = "name" colspan = "02" align="center"><br><a href='javascript:show_calendar("frgrm.dFecIni")' id="id_href_dFecIni">Del</a></td>
	          	        <td Class = "name" colspan = "06"><br>
	          	        	<input type="text" name="dFecIni" style = "width:120;text-align:center" onblur="javascript:chDate(this);" value="">
	          	        </td>
	          	        <td Class = "name" colspan = "02" align="center"><br><a href='javascript:show_calendar("frgrm.dFecFin")' id="id_href_dFecFin">Al</a></td>
	          	        <td Class = "name" colspan = "06" align="right"><br>
	          	        	<input type="text" name="dFecFin" style = "width:120;text-align:center" onblur="javascript:chDate(this);" value="">
	          	        </td>
	          	      </tr>              
                </table>
              </center>
            </fieldset>
            <center>
              <table border="0" cellpadding="0" cellspacing="0" width="480">
                <tr height="21">
                  <td width="298" height="21"></td>
                  <td width="91" height="21" Class = "name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:f_Generar()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
                  <td width="91" height="21" Class = "name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
                </tr>
              </table>
            </center>
          </form>
        </td>
      </tr>
    </table>
  </center>
	</body>
</html>
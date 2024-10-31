<?php
  namespace openComex;
	 /**
	 * Imprime Reporte de Productividad por Cliente.
	 * --- Descripcion: Permite Imprimir Reporte de Productividad por Cliente.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 * @version 002
	 */

	include("../../../../libs/php/utility.php");
	$cAno = date('Y');
	//f_Mensaje(__FILE__,__LINE__,$cAno);
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


	     function f_Links(xLink,xSwitch) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
				  case "cDocNro":
						if (xSwitch == "VALID") {
							var cPathUrl  = "frest121.php?gWhat=VALID&gFunction=cDocNro&gDocNro="+document.forms['frgrm']['cDocNro'].value.toUpperCase()+
													    "&gDocSuf="+document.forms['frgrm']['cDocSuf'].value;
							parent.fmpro.location = cPathUrl;
						} else {
			  			var nNx     = (nX-400)/2;
							var nNy     = (nY-250)/2;
							var cWinOpt = 'width=400,scrollbars=1,height=250,left='+nNx+',top='+nNy;
							var cPathUrl  = "frest121.php?gWhat=WINDOW&gFunction=cDocNro&gDocNro="+document.forms['frgrm']['cDocNro'].value.toUpperCase();
							cWindow = window.open(cPathUrl,"cWindow",cWinOpt);
					  	cWindow.focus();
						}
					break;
				  case "cTerId":
					case "cTerNom":
					  if (xLink == "cTerId" || xLink == "cTerNom") {
					    var cTerTip = 'CLICLIXX';
					    var cTerId = document.forms['frgrm']['cTerId'].value.toUpperCase();
					    var cTerNom = document.forms['frgrm']['cTerNom'].value.toUpperCase();
					  }
						if (xSwitch == "VALID") {
							var cPathUrl = "frest150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gTerTip="+cTerTip+
																				"&gTerId="+cTerId+
																				"&gTerNom="+cTerNom;
							//alert(cPathUrl);
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-600)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "frest150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				 "&gTerTip="+cTerTip+
																				 "&gTerId="+cTerId+
																				 "&gTerNom="+cTerNom;
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
				  		cWindow.focus();
						}
					break;
					case "cDirId":
					case "cDirNom":
						if (xSwitch == "VALID") {
							var cPathUrl  = "frest003.php?gWhat=VALID&gFunction="+xLink+
							                          "&gDirId="+document.frgrm['cDirId'].value.toUpperCase()+
							                          "&gDirNom="+document.frgrm['cDirNom'].value.toUpperCase();
							parent.fmpro.location = cPathUrl;
						} else {
	  					var nNx     = (nX-600)/2;
							var nNy     = (nY-250)/2;
							var cWinOpt = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
							var cPathUrl   = "frest003.php?gWhat=WINDOW&gFunction="+xLink+
							                           "&gDirId="+document.frgrm['cDirId'].value.toUpperCase()+
							                           "&gDirNom="+document.frgrm['cDirNom'].value.toUpperCase();
							cWindow = window.open(cPathUrl,"cWindow",cWinOpt);
					  	cWindow.focus();
						}
			    break;
				}
	    }

			function f_GenSql(){
				var nSwitch = 0;
				var dAnioD = document.forms['frgrm']['dDesde'].value;
				var dAnioH = document.forms['frgrm']['dHasta'].value;
				
			
  			if(document.forms['frgrm']['dDesde'].value == '' || document.forms.frgrm['dHasta'].value == ''){
  				alert("Debe Seleccionar un Rango de Fechas, Verifique.");
   				nSwitch = 1;
  			}

  			if(dAnioD.substr(0,4) != dAnioH.substr(0,4)){
  				alert("Debe Seleccionar un Rango de Fechas del mismo Ano, Verifique.");
   				nSwitch = 1;
  			}

  			if(nSwitch == 0){
  			 var cTipo = 0;
    		  for (i=0;i<2;i++){
    		    if (document.forms['frgrm']['rTipo'][i].checked == true){
    		  	  cTipo = i+1;
    		      break;
    		    }
    		  }
    		  
    		  var cPathUrl = 'frestprn.php?gDesde='+document.forms['frgrm']['dDesde'].value+
                         '&gHasta=' +document.forms.frgrm['dHasta'].value     +
                         '&gSucId=' +document.forms['frgrm']['cSucId'].value  +
                         '&gDocNro='+document.forms['frgrm']['cDocNro'].value +
                         '&gDocSuf='+document.forms['frgrm']['cDocSuf'].value +
                         '&gCcoId=' +document.forms['frgrm']['cCcoId'].value  +
                         '&gDirId=' +document.forms['frgrm']['cDirId'].value  +
                         '&gTerId=' +document.forms['frgrm']['cTerId'].value  +
                         '&gEstado=' +document.forms['frgrm']['cEstado'].value+
                         '&cTipo='  +document.forms['frgrm']['rTipo'][i].value;
  			  if(cTipo == 2){        			  	
  			  	parent.fmpro.location = cPathUrl;
  			  }else{
     				var nX      = screen.width;
    				var nY      = screen.height;
    				var nNx     = 0;  				
    				var nNy     = 0;
    				var cWinOpt = "width="+nX+",scrollbars=1,resizable=YES,height="+nY+",left="+nNx+",top="+nNy;
    				var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
    				cWindow = window.open(cPathUrl,cNomVen,cWinOpt);
    				cWindow.focus();
  			  }
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

	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
  <center>
    <table border ="0" cellpadding="0" cellspacing="0" width="500">
      <tr>
        <td>
          <form name='frgrm' action='frestprn.php' method="POST">
            <fieldset>
              <legend>Consulta Estado de Cuenta Tramites</legend>
              <center>
                <table border="2" cellspacing="0" cellpadding="0" width="500">
                  <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
                    <td class="name" width="30%"><center><h5><br>REPORTE DE ESTADO DE CUENTA TRAMITES</h5></center></td>
                  </tr>
                </table>
                <table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
                  <?php $nCol = f_Format_Cols(25);
                  echo $nCol;?>
                  <tr>
        	          <td class="name" colspan = "7"><br>Desplegar en:
        			      </td>
        			      <td class="name" colspan = "6"><br>
        	            <input type="radio" name="rTipo" value="1" checked>Pantalla
        	          </td>
        	          <td class="name" colspan = "6"><br>
        			         <input type="radio" name="rTipo" value="2">Excel
        			      </td>
        			      <td class="name" colspan = "6"><br>
        			        <input type="radio" name="rTipo" value="3">Pdf<br>
        			      </td>
        	        </tr>
        	       </table>
                <table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
                  <?php $nCol = f_Format_Cols(24);
                  echo $nCol;?>
                  <tr>
          	         <td class="name" colspan = "10"><br>Rango De Fechas (Fecha Doc.):</td>
          	         <td class="name" colspan = "2"><br><center><a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">De</a></center></td>
          	         <td class="name" colspan = "5"><br>
          	            <input type="text" name="dDesde" style = "width:100;text-align:center"
          	               onblur="javascript:chDate(this);">
          	         </td>
          	         <td class="name" colspan = "2"><br><center><a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">A</a></center></td>
          	         <td class="name" colspan = "5"><br>
          	            <input type="text" name="dHasta" style = "width:100;text-align:center"
          	              onblur="javascript:chDate(this);">
          	         </td>
          	       </tr>
                   <tr>
                    <tr>
                      <td class="name" colspan = "03"><br>
                        <a href = "javascript: document.forms['frgrm']['cSucId'].value='';
                														   document.forms['frgrm']['cDocTip'].value='';
                														   document.forms['frgrm']['cDocNro'].value='';
                														   document.forms['frgrm']['cDocSuf'].value='';
                														   f_Links('cDocNro','VALID');" id="vDir">Do:</a>
                     </td>
                      <td class="name" colspan = "24"><br>
    			        		<input type="text"   class="letra" name="cSucId"  style="width:30" readonly>
    			        		<input type="text"   class="letra" name="cDocTip" style="width:80" readonly>
    			        		<input type="text"   class="letra" name="cDocNro" style="width:80"
    			        				onBlur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';
    														   	f_Links('cDocNro','VALID');"
    										  onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';
    														   document.forms['frgrm']['cSucId'].value='';
    														   document.forms['frgrm']['cDocTip'].value='';
    														   document.forms['frgrm']['cDocNro'].value='';
    														   document.forms['frgrm']['cDocSuf'].value='';">
    			        	<input type="text"   class="letra" name="cDocSuf" style="width:30" readonly>
    			        	</td>
			        	  </tr>
			         	   <tr>
			        	    <td class="name" colspan = "03"><br>Sucursal:</td>
          	       	<td Class = "name" colspan = "24"><br>
											<select Class = "letrase" style = "width:160;text-align:left" name = "cCcoId">
												<option value="">TODAS</option>
												<?php //Busco sucrsales
	  							 				$qSucDes = "SELECT ccoidxxx,sucidxxx, sucdesxx FROM $cAlfa.fpar0008 WHERE regestxx = \"ACTIVO\" ORDER BY sucdesxx";
	  							 				$xSucDes  = f_MySql("SELECT","",$qSucDes,$xConexion01,"");
	  							 				while ($xRSD = mysql_fetch_array($xSucDes)){ ?>
	  							 					 <option value="<?php echo $xRSD['ccoidxxx']."~".$xRSD['sucidxxx']."~".$xRSD['sucdesxx'] ?>"><?php echo $xRSD['sucdesxx'] ?></option>
	  							 				<?php } ?>  							 		
											</select>
										</td>
									</tr>
                  <tr>
                    <td class="name" colspan = "03"><br>
                      <a href = "javascript:document.frgrm.cDirId.value='';
                                            document.frgrm.cDirNom.value='';
                                            f_Links('cDirId','VALID')" id="vDir">Director:</a>
                    </td>
                    <td class="name" colspan = "06"><br>
                      <input type="text" name="cDirId" style = "width:120"
                            onfocus="javascript:document.forms['frgrm']['cDirId'].value  = '';
                                                document.forms['frgrm']['cDirNom'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                f_Links('cDirId','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                    <td class="name" colspan = "15"><br>
                      <input type="text" name="cDirNom" style = "width:300"
                            onfocus="javascript:document.forms['frgrm']['cDirId'].value  = '';
                                                document.forms['frgrm']['cDirNom'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                f_Links('cDirNom','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                  </tr>
                  <tr>
                    <td class="name" colspan = "03"><br>
                      <a href = "javascript:document.forms['frgrm']['cTerId'].value  = '';
                                            document.forms['frgrm']['cTerDV'].value  = '';
                                            document.forms['frgrm']['cTerNom'].value = '';
                                            f_Links('cTerId','VALID')" id="id_href_cTerId">Cliente:</a>
                    </td>
                    <td class="name" colspan = "05"><br>
                      <input type="text" name="cTerId" style = "width:100"
                            onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
                                                document.forms['frgrm']['cTerDV'].value  = '';
                                                document.forms['frgrm']['cTerNom'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                f_Links('cTerId','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                    <td Class = "name" colspan = "02"><br>
                      <input type = "text" style = "width:40;text-align:center" name = "cTerDV" readonly>
                    </td>
                    <td class="name" colspan = "14"><br>
                      <input type="text" name="cTerNom" style = "width:280"
                            onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
                                                document.forms['frgrm']['cTerDV'].value  = '';
                                                document.forms['frgrm']['cTerNom'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                f_Links('cTerNom','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                  </tr>
               </table>
               <table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
                  <?php $nCol = f_Format_Cols(24);
                  echo $nCol;?>
                  <tr>
			        	    <td class="name" colspan = "03"><br>Estado:</td>
          	       	<td Class = "name" colspan = "24"><br>
											<select Class = "letrase" style = "width:100;text-align:left;" name = "cEstado">
												<option value="ACTIVO" selected>ACTIVO</option>
												<option value="FACTURADO">FACTURADO</option>
											</select>
										</td>
									</tr>
                </table>
              </center>
            </fieldset>
            <center>
              <table border="0" cellpadding="0" cellspacing="0" width="500">
                <tr height="21">
                  <td width="318" height="21"></td>
                  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:f_GenSql()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
                  <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
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
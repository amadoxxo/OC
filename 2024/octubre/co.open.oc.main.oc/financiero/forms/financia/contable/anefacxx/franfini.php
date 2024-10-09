<?php
  namespace openComex;
	 /**
	 * Imprime Anexo Factura Pagos a Terceros y Servicios.
	 * --- Descripcion: Permite Imprimir Anexo Factura Pagos a Terceros y Sevicios.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 * @version 002
	 */

	include("../../../../libs/php/utility.php");
	$cAno = date('Y');
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


	    function f_Links(xLink,xSwitch,xSecuencia,xGrid,xType) {
				var nX    = screen.width;
				var nY    = screen.height;
				var cTerTip = 'CLICLIXX';
				switch (xLink) {
				 case "cTerId":
					case "cTerNom":
					  if (xLink == "cTerId" || xLink == "cTerNom") {
					    //var cTerTip = 'CLICLIXX';
					    var cTerId = document.forms['frgrm']['cTerId'].value.toUpperCase();
					    var cTerNom = document.forms['frgrm']['cTerNom'].value.toUpperCase();
					  }
						if (xSwitch == "VALID") {
							var cPathUrl = "franf150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gTerTip="+cTerTip+
																				"&gTerId="+cTerId+
																				"&gTerNom="+cTerNom;
							//alert(cPathUrl);
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-600)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "franf150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				 "&gTerTip="+cTerTip+
																				 "&gTerId="+cTerId+
																				 "&gTerNom="+cTerNom;
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

		
	    function f_Anexo_Factura_Aduana() {
	    	var band = 0;
  			var dDesde = document.forms['frgrm']['dDesde'].value;
  			var dHasta = document.forms['frgrm']['dHasta'].value;
  			var ini = dDesde.replace('-','');
  		  var fin = dHasta.replace('-','');
  			var fsi = '<?php echo date('Y-m-d') ?>';
  			var fsis = fsi.replace('-','');
  			var fsis1 = fsis.replace('-','');
  			var ini2 = ini.replace('-','');
  			var fin2 = fin.replace('-','');
  			inii = 1 * ini2;
  			fini = 1 * fin2;
  			fsi2 = 1 * fsis1;
  			if(fini > fsi2 ){
           alert('Fecha Final no puede ser mayor a la Fecha de Hoy,verifique');
           document.forms['frgrm']['dDesde'].focus();
           band = 1;
        }
         if (fini < inii){
    			 alert('Fecha Final es Menor a Inicial,verifique');
    			 document.forms['frgrm']['dHasta'].focus();
    			 band = 1;
    		 }

		    
         if(band != 1){
        	 if (document.forms['frgrm']['dDesde'].value.length >   0   &&
    				   document.forms['frgrm']['dHasta'].value.length >   0   &&
    				   band == 0){    
							var zRuta = "franeprn.php?gSucId="+document.forms['frgrm']['cSucId'].value+
					                             "&gTerId="+document.forms['frgrm']['cTerId'].value+
								                       "&gComCsc="+document.forms['frgrm']['cComCsc'].value+
																			 "&gDesde="+document.forms['frgrm']['dDesde'].value+
																			 "&gHasta="+document.forms['frgrm']['dHasta'].value;
					        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),
					                         strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
					        document.cookie="kMenDes=Imprimir;path="+"/";
					        document.cookie="kModo=IMPRIMIR;path="+"/";
					        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
					        document.location = zRuta; // Invoco el menu.
		       } else {
             	alert("Como Minimo Debe Escoger Periodo, Verifique");
           }
        }  
	    }
	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="520">
				<tr>
					<td>
            <form name='frgrm' action='frtsaexc.php' method="POST">
              <center>
          	    <fieldset>
          		    <legend>Anexo Factura Aduana Pagos a Terceros Servicios y Anticipos </legend>
          		    <table border="2" cellspacing="0" cellpadding="0" width="">
          		     	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
          			     	<td class="name" width="30%"><center><h5><br>ANEXO FACTURA ADUANA PAGOS A TERCEROS SERVICIOS Y ANTICIPOS</h5></center></td>
          			    </tr>
          			  </table>
          			  <table border = '0' cellpadding = '0' cellspacing = '0' width='520'>
  							 		<?php $nCol = f_Format_Cols(26);
  							 		echo $nCol;?>
  							 	 <tr>
  							 	 	<tr>
          	        <td class="name" colspan = "5"><br>Sucursal:</td>
          	        <td Class = "name" colspan = "6"><br>
	          	        <select Class = "letrase" name = "cSucId" style = "width:120">
		          	        <option value = '' selected>--SELECCIONE--</option>
		          	        <?php 
		          	        	$qSuc008  = "SELECT * ";
		          	        	$qSuc008 .= "FROM $cAlfa.fpar0008 ";
		          	        	$qSuc008 .= "WHERE ";
		          	        	$qSuc008 .=" regestxx = \"ACTIVO\" ORDER BY sucdesxx ";
		                      $xSuc008  = f_MySql("SELECT","",$qSuc008,$xConexion01,"");
		                      while($xRSD = mysql_fetch_array($xSuc008)){
				          	        ?>
			          	        	<option value = '<?php echo $xRSD['ccoidxxx']?>' ><?php echo $xRSD['sucdesxx'] ?></option>
														<?php 
		                      }
												?>
										  </select>
										</td>
          	       </tr>
										<td Class = "name" colspan = "5"><br>Cliente:</td>
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
										<td Class = "name" colspan = "14"><br>Cliente<br>
											<input type = "text" Class = "letra" style = "width:280" name = "cTerNom"
									    	onfocus="javascript:document.forms['frgrm']['cTerId'].value  = '';
            						  									document.forms['frgrm']['cTerNom'].value = '';
																				    document.forms['frgrm']['cTerDV'].value  = '';
													                  this.style.background='#00FFFF'"
									    	onBlur = "javascript:this.value=this.value.toUpperCase();
													                   f_Links('cTerNom','VALID');
													                   this.style.background='#FFFFFF'">
										</td>
          	       </tr>
          	       <tr>
          	        <td class="name" colspan = "5"><br>Factura:</td>
          	        <td Class = "name" colspan = "6"><br>
											<input type = "text" Class = "letra" style = "width:120" name = "cComCsc"
  												onfocus="javascript:this.style.background='#00FFFF'"
  									    	onBlur = "javascript:this.value=this.value.toUpperCase();
  																		         this.style.background='#FFFFFF'">
										</td>
          	       </tr>
          	       <tr>
          	         <td class="name" colspan = "3"><br>Fecha:</td>
          	         <td class="name" colspan = "2"><br><center><a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">De</a></center></td>
          	         <td class="name" colspan = "6"><br>
          	            <input type="text" name="dDesde" style = "width:120;text-align:center"
          	               onblur="javascript:chDate(this);">
          	         </td>
          	         <td class="name" colspan = "7"><br><center><a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">A</a></center></td>
          	         <td class="name" colspan = "7"><br>
          	            <input type="text" name="dHasta" style = "width:140;text-align:center"
          	              onblur="javascript:chDate(this);">
          	         </td>
          	       </tr>
          		    </table>
          		  </fieldset>
                <center>
          				<table border="0" cellpadding="0" cellspacing="0" width="520">
            				<tr height="21">
            					<td width="338" height="21"></td>
            					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:f_Anexo_Factura_Aduana()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar</td>
            					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
          				  </tr>
          				</table>
          			</center>
          			<script language="javascript">
            		  //document.forms['frgrm']['cMes'].value='<?php echo date('m') ?>';
            		</script>
          	  </form>
					</td>
				</tr>
		 	</table>
		</center>
	</body>
</html>
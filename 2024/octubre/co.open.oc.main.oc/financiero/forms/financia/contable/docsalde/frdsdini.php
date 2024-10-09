<?php
  namespace openComex;
	 /**
	 * Imprime Movimiento por Documento.
	 * --- Descripcion: Permite Imprimir Movimiento por Documento.
	 * @author Oscar  Hernandez <oscar.hernandez@opentecnologia.com.co>
	 * @version 002
	 */

	include("../../../../libs/php/utility.php");
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

	    function f_Envia(){
		    
	    	var nBand = 0;
	    	if(document.forms['frgrm']['nAnoPer'].value == "" && document.forms['frgrm']['nMesPer'].value == ""){
  				nBand = 0;
  			}else if(document.forms['frgrm']['nAnoPer'].value == "" && document.forms['frgrm']['nMesPer'].value != ""){
	  			nBand =1;
	  			alert("Debe Seleccionar un A\u00f1o.\nVerifique"); 
	  			
  			}else if (document.forms['frgrm']['nAnoPer'].value != "" && document.forms['frgrm']['nMesPer'].value == ""){
  				nBand =1;
  				alert("Debe Seleccionar un Mes.\nVerifique"); 
  			}
  			if(nBand == 0){
		    	switch(document.forms['frgrm']['rTipo'].value){
		    		case "1":// Reporte Por Pantalla
		 					var zX      = screen.width;
		  				var zY      = screen.height;
		  				var zNx     = (zX-30)/2;
		  				var zNy     = (zY-100)/2;
		  				var zNy2    = (zY-100);
		  				
		  				var zWinPro = "width="+zX+",scrollbars=1,height="+zNy2+",left="+1+",top="+50;
							var zRuta = 'frdsdprn.php?cTipo='+document.forms['frgrm']['rTipo'].value
																							+'&gPeriodo='+document.forms['frgrm']['nAnoPer'].value+document.forms['frgrm']['nMesPer'].value
																							+'&gComId='+document.forms['frgrm']['cComId'].value
																							+'&gComCod='+document.forms['frgrm']['cComCod'].value
																							+'&gVerificar='+document.forms['frgrm']['rVerificar'].value;
							var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
		  				zWindow = window.open(zRuta,cNomVen,zWinPro);
		  				zWindow.focus();
		  				//document.forms['frgrm']['rTipo'].value= "1";
		  				//document.forms['frgrm']['nAnoPer'].value = "";
		  				//document.forms['frgrm']['nMesPer'].value = "";
		  				//document.forms['frgrm']['cComId'].value = "";
		  				//document.forms['frgrm']['cComCod'].value = "";
		  				//document.forms['frgrm']['cComDes'].value = "";
		  				
			  		break;
		    		case "2":// Reporte por Excel
				  		var zRuta = 'frdsdprn.php?cTipo='+document.forms['frgrm']['rTipo'].value
				  																	 	 +'&gPeriodo='+document.forms['frgrm']['nAnoPer'].value+document.forms['frgrm']['nMesPer'].value
				  																	 	 +'&gComId='+document.forms['frgrm']['cComId'].value
				  																	 	 +'&gComCod='+document.forms['frgrm']['cComCod'].value
				  																	 	 +'&gVerificar='+document.forms['frgrm']['rVerificar'].value;
				  		parent.fmpro.location = zRuta;
				  		//document.forms['frgrm']['rTipo'].value= "1";
					  	//document.forms['frgrm']['nAnoPer'].value = "";
		  				//document.forms['frgrm']['nMesPer'].value = "";
		  				//document.forms['frgrm']['cComId'].value = "";
		  				//document.forms['frgrm']['cComCod'].value = "";
		  				//document.forms['frgrm']['cComDes'].value = "";
			    	break;
		    	}
  			}
	    }
    	function f_Links(xLink,xSwitch,xSecuencia,xGrid,xType) {
    		var nX    = screen.width;
    		var nY    = screen.height;
    		switch (xLink) {
    		  case "cComCod":
    			case "cComDes":
    				if(document.forms['frgrm']['cComId'].value != ""){
    				  if (xLink == "cComCod" || xLink == "cComDes") {
    				    var cComId  = document.forms['frgrm']['cComId'].value.toUpperCase();
    				    var cComCod  = document.forms['frgrm']['cComCod'].value.toUpperCase();
    				    var cComDes = document.forms['frgrm']['cComDes'].value.toUpperCase();
    				  }
    					if (xSwitch == "VALID") {
    						var cPathUrl = "frpar117.php?gModo="+xSwitch+"&gFunction="+xLink+
    																			"&gComId="+cComId+
    																			"&gComCod="+cComCod+
    																			"&gComDes="+cComDes;
    						//alert(cPathUrl);
    						parent.fmpro.location = cPathUrl;
    					} else {
    						var nNx      = (nX-600)/2;
    						var nNy      = (nY-250)/2;
    						var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
    						var cPathUrl = "frpar117.php?gModo="+xSwitch+"&gFunction="+xLink+
    																			 "&gComId="+cComId+
    																			 "&gComDes="+cComDes;
    						cWindow = window.open(cPathUrl,xLink,cWinOpt);
    			  		cWindow.focus();
    					}
    				}else{
    					alert("Debe Seleccionar un Comprobante.");
    				}
    			break;
    		}
    	}
	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="600">
				<tr>
					<td>
            <form name='frgrm' action='frfsdprn.php' method="POST">
              <center>
          	    <fieldset>
          		    <legend>Consulta Documentos con Saldo Descuadrado </legend>
          		    <table border="2" cellspacing="0" cellpadding="0" width="600">
          		     	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
          			     	<td class="name" width="30%">
          			     		<center>
          			     			<h4>
          			     				<br>
          			     				REPORTE DE DOCUMENTOS CON SALDO DESCUADRADO
          			     				<br>
          			     				<u>
          			     					La Fecha de hoy es: <?php echo date('Y-m-d') ?>
          			     				</u>
          			     			</h4>
          			     		</center>
          			     	</td>
          			    </tr>
          			  </table>
          			  <table border = '0' cellpadding = '0' cellspacing = '0' width='600'>
  							 		<?php $nCol = f_Format_Cols(26);
  							 		echo $nCol;?>
          			    <tr>
          	          <td class="name" colspan = "6"><br>Desplegar en:
          			      </td>
          			      <td class="name" colspan = "6"><br>
          	            <input type="radio" name="rTipo" value="1" checked>Pantalla
          	          </td>
          	          <td class="name" colspan = "14"><br>
          			         <input type="radio" name="rTipo" value="2">Excel
          			      </td>
          	        </tr>
									</table>
									<table border = '0' cellpadding = '0' cellspacing = '0' width='600'>
  							 		<?php $nCol = f_Format_Cols(26);
  							 		echo $nCol;?>
          			    <tr>
          	          <td class="name" colspan = "6"><br>Verificar:
          			      </td>
          			      <td class="name" colspan = "6"><br>
          	            <input type="radio" name="rVerificar" value="1" checked>Movimiento
          	          </td>
          	          <td class="name" colspan = "6"><br>
          			         <input type="radio" name="rVerificar" value="2">Saldos CxP
											</td>
											<td class="name" colspan = "8"><br>
          			         <input type="radio" name="rVerificar" value="3">Saldos CxC
          			      </td>
          	        </tr>
          	      </table>
          	      <br/>
          	       <table border = '0' cellpadding = '0' cellspacing = '0' width='600'>
  							 		<?php $nCol = f_Format_Cols(27);
  							 		echo $nCol;?>
          	       <tr>
									</tr>
									<tr>
										<td Class = "name" colspan = "3">A&ntilde;o<br>
					  					<select Class = "letrase" name = "nAnoPer" style = "width:80">
					  					<option value=""></option>
						  				 	<?php $cAnoIni =  ((date('Y')-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (date('Y')-1);
						  						for ($i=$cAnoIni; $i<=date('Y'); $i++) { ?>
							  							<option value = "<?php echo $i ?>"<?php echo date('Y')?>><?php echo $i ?></option>
							  							<?php 
						  						}?>
					  						  </select>
					  						  
					  				</td>
					  				<td Class = "name" colspan = "2">Mes<br>
					  					<select Class = "letrase" name = "nMesPer" style = "width:80">
					  						<option value=""></option>
					  						<?php 
					  						for($n=1; $n<=12; $n++){?>
					  						<option value = "<?php echo str_pad($n,2,"0",STR_PAD_LEFT) ?>"<?php echo  date('m')?>><?php echo str_pad($n,2,"0",STR_PAD_LEFT) ?></option>
					  							<?php 
					  						}?>
					  					</select>
					  				</td>
										<td Class = "name" colspan = "10">Documento<br>
											<select class="letrase" size="1" name="cComId" style = "width:200"
														onchange="javascript: document.forms['frgrm']['cComCod'].value = '';
																								  document.forms['frgrm']['cComDes'].value = '';">
														<option value = "" selected>-- TODOS --</option>
														<option value = "A">A - AJUSTES X INF.</option>
														<option value = "B">B - ORDEN DE PRODUCCION</option>
														<option value = "C">C - NOTA CREDITO</option>
														<option value = "D">D - NOTA DEBITO</option>
														<option value = "E">E - NOTA ENTREGA</option>
														<option value = "F">F - FACTURAS</option>
														<option value = "G">G - EGRESOS</option>
														<option value = "H">H - NOTA SALIDA</option>
														<option value = "J">J - NOTA DEVOLUCION</option>
														<option value = "K">K - MINUTAS</option>
														<option value = "L">L - OTROS</option>
														<option value = "M">M - CAJA MENOR</option>
														<option value = "N">N - NOTA INTERNA</option>
														<option value = "O">O - NOTA PRODUCCION</option>
														<option value = "P">P - COMPRAS</option>
														<option value = "R">R - RECIBOS</option>
														<option value = "S">S - NOTA REMISION</option>
														<option value = "T">T - NOTA TRASLADO</option>
														<option value = "V">V - COTIZACION</option>
														<option value = "X">X - INVENTARIO DE FORMULARIOS</option>
														<option value = "Y">Y - ORDEN COMPRA</option>
														<option value = "Z">Z - ORDEN PEDIDO</option>
													</select>
										</td>
										<td Class = "name" colspan = "3">
											<a href = "javascript:document.forms['frgrm']['cComCod'].value = '';
																								document.forms['frgrm']['cComDes'].value  = '';
																								f_Links('cComCod','VALID')" id="id_href_cComCod">Codigo</a><br>
													<input type = "text" Class = "letra" style = "width:60;text-align:center" name = "cComCod"
														onfocus="javascript:document.forms['frgrm']['cComCod'].value = '';
																				    		document.forms['frgrm']['cComDes'].value  = '';
													                  		this.style.background='#00FFFF'"
									    			onBlur ="javascript:this.value=this.value.toUpperCase();
																		         		f_Links('cComCod','VALID');
																		         		this.style.background='#FFFFFF'">
										</td>
										<td Class = "name" colspan = "9">Descripci&oacute;n<br>
												<input type = "text" Class = "letra" style = "width:180" name = "cComDes" readonly>
											</td>
          	       </tr>
          		    </table>
          		  </fieldset>
                <center>
          				<table border="0" cellpadding="0" cellspacing="0" width="600">
            				<tr height="21">
            					<td width="418" height="21"></td>
            					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javasript:f_Envia()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Buscar</td>
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
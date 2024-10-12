<?php
  namespace openComex;
	/**
	 * Desbloquear Consecutivo 2
	 * --- Descripcion: Permite desbloquear el consecutivo dos cuando se queda pegado
	 * @author Jeison Javier Escobar Villanueva <jeison.escobar@opentecnologia.com.co>
	 * @version 001
	 */
	include("../../../../libs/php/utility.php");
?>
<html>
	<head>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
    <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
    <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
    <script language="javascript">
   	  
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

		  function f_Envia(){
					
				if (document.forms['frgrm']['nPeriodo'].value.length == 6) {
		  			var cRuta = 'frdcddes.php?gPeriodo='+document.forms['frgrm']['nPeriodo'].value+
			  								'&gComId='+document.forms['frgrm']['cComId'].value+
			  								'&gComCod='+document.forms['frgrm']['cComCod'].value;
						parent.fmpro.location = cRuta;
				} else {
					alert('Verifique El Perido Contable.');
				}
			}
		</script>
  </head>
  	<body>
  		<center>
				<table border ="0" cellpadding="0" cellspacing="0" width="320">
					<tr>
						<td>
			  			<fieldset>
						 	<legend>Desbloquear consecutivo</legend>
				  			<form name = 'frgrm'>
				  				<table border = "0" cellpadding = "0" cellspacing = "0" style = "width:520">
										<center>
		       	     			<table cellspacing="0" width="100%">
		         	         	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
		           	         	<td class="name" width="100%"><br><center><u>Este Proceso Desbloquea el Consecutivo Dos del Sistema</u><br><br></center>
		            	      </tr>
		                  </table>
		                </center>
		                <br>
		                <table border = "0" cellpadding = "0" cellspacing = "0" style = "width:520">
		                  <?php $zCol = f_Format_Cols(26); echo $zCol; ?>
				  					  <tr>
				  						  <td Class = "name" colspan = "4">Periodo<br>
					  						  <select Class = "letrase" name = "nPeriodo" style = "width:80">
						  						  <?php $cAnoIni =  ((date('Y')-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (date('Y')-1);
						  						  for ($i=$cAnoIni; $i<=date('Y'); $i++) { ?>
							  						  <?php for ($n=1; $n<=12; $n++) { ?>
							  						  	<option value = "<?php echo $i.str_pad($n,2,"0",STR_PAD_LEFT) ?>"<?php echo (date('Y').str_pad($n,2,"0",STR_PAD_LEFT) == date('Ym')) ? " selected" : "" ?>><?php echo $i.str_pad($n,2,"0",STR_PAD_LEFT) ?></option>
							  						  <?php }?>	
						  						  <?php }?>
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
																								f_Links('cComCod','VALID')" id="id_href_cComCod">Prefijo</a><br>
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
				        </form>
				      </fieldset>
				    </td>
				  </tr>
				</table>
      </center>
      <center>
			<table border="0" cellpadding="0" cellspacing="0" width="520">
        <tr height="21">
	        <td width="429" height="21"></td>
					<td width="91" height="21" Class="name" >
						<input type="button" name="Btn_Guardar" value="Generar" style = "width:95;height:21;"
							onclick = "javascript:f_Envia();"></td>
			  </tr>
		  </table>
    </center>
  </body>
</html>
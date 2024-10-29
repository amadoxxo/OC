<?php
  namespace openComex;
	 /**
	 * Imprime MOVIMIENTO CONTABLE CONSOLIDADO.
	 * --- Descripcion: Permite Imprimir MOVIMIENTO CONTABLE CONSOLIDADO.
	 * @author TecnoSmart 
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
				parent.fmwork.location='<?php echo $cPlesk_Forms_Directory ?>/frproces.php';
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
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

			// FUNCION DE SELECT PARA CONSULTA //
			function f_GenSql()  {
  			var band = 0;
  			var dDesde = document.forms['frgrm']['dDesde'].value;
  			var dHasta = document.forms['frgrm']['dHasta'].value;
  				//if (dDesde.length == 10 && dHasta.length == 10){
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
      				if (document.forms['frgrm']['dDesde'].value.length > 0 && document.forms['frgrm']['dHasta'].value.length > 0 && band == 0){
        			  var cTipo = 0;
        			  var cTipCta = "";
        			  for (i=0;i<3;i++){
        			    if (document.forms['frgrm']['rTipo'][i].checked == true){
        			  	  cTipo = i+1;
        			      break;
        			    }
        			  }
        			  var zRuta = 'frmccprn.php?cTipo='+document.forms['frgrm']['rTipo'][i].value+'&cCcoId='+document.forms['frgrm']['cCcoId'].value+'&dDesde='+document.forms['frgrm']['dDesde'].value+'&dHasta='+document.forms['frgrm']['dHasta'].value+'&cComeAlpo='+document.forms['frgrm']['cComeAlpo'].value;
        			  if(document.forms['frgrm']['rTipo'][i].value == 2){        			  	
        			  	parent.fmpro.location = zRuta;
        			  }else{
	         				var zX      = screen.width;
	        				var zY      = screen.height;
	        				var zNx     = (zX-30)/2;
	        				var zNy     = (zY-100)/2;
	        				var zNy2    = (zY-100);
	        				var zWinPro = "width="+zX+",scrollbars=1,height="+zNy2+",left="+1+",top="+50;
	        				var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
	        				zWindow = window.open(zRuta,cNomVen,zWinPro);
	        				zWindow.focus();
        			  }
              } else {
                  alert("Verifique Rango de Fechas.  No Pueden ser Vacios");
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
            <form name='frgrm' action='frmccprn.php' method="POST">
              <center>
          	    <fieldset>
          		    <legend>Consulta Movimiento Contable Consolidado</legend>
          		    <table border="2" cellspacing="0" cellpadding="0" width="500">
          		     	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
          			     	<td class="name" width="30%"><center><h5><br>MOVIMIENTO CONTABLE CONSOLIDADO</h5></center></td>
          			    </tr>
          			  </table>
          			  <table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
  							 		<?php $nCol = f_Format_Cols(25);
  							 		echo $nCol;?>
          			    <tr>
          	          <td class="name" colspan = "5"><br>Desplegar en:
          			      </td>
          			      <td class="name" colspan = "7"><br>
          	            <input type="radio" name="rTipo" value="1" checked>Pantalla
          	          </td>
          	          <td class="name" colspan = "7"><br>
          			         <input type="radio" name="rTipo" value="2">Excel
          			      </td>
          			      <td class="name" colspan = "6"><br>
          			        <input type="radio" name="rTipo" value="3">Pdf<br>
          			      </td>
          	        </tr>
          	       	<td Class = "name" colspan = "6"><br>Sucursal<br>
								<select Class = "letrase" style = "width:120;text-align:center" name = "cCcoId">
									<option value="">TODAS</option>
										<?php //Busco sucursales
	  							 			$qSucDes = "SELECT ccoidxxx, sucdesxx FROM $cAlfa.fpar0008 WHERE regestxx = \"ACTIVO\" ORDER BY sucdesxx";
	  							 			$xSucDes  = f_MySql("SELECT","",$qSucDes,$xConexion01,"");
	  							 			while ($xRSD = mysql_fetch_array($xSucDes)){ ?>
	  							 				<option value="<?php echo $xRSD['ccoidxxx'] ?>"><?php echo $xRSD['sucdesxx'] ?></option>
	  							 		<?php } ?>  							 		
								</select>
							</td>
							<td Class = "name" colspan = "6"><br>Estado</br>
								<select Class = "letrase" style = "width:120;text-align:center" name = "cComeAlpo">
									<option value="">TODOS</option>
		  							<option value="CONTABILIZADO">CONTABILIZADO</option>
		  							<option value="ANULADO">ANULADO</option>
								</select>
							</td>
							<td Class = "name" colspan = "1">&nbsp;</td>
							<td class="name" colspan = "12"><br>Rango De Fechas:<br>
								<a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">De</a>&nbsp;&nbsp;&nbsp;&nbsp;								     	
									<input type="text" name="dDesde" style = "width:80;text-align:center"
          	               	onblur="javascript:chDate(this);">&nbsp;&nbsp;&nbsp;&nbsp;
								<a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">A</a>&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="text" name="dHasta" style = "width:80;text-align:center"
          	              		onblur="javascript:chDate(this);">
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
	</body>
</html>
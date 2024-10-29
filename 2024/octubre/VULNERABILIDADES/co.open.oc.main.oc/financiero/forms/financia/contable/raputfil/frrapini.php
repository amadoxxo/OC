<?php
  namespace openComex;
/**
* Genera reporte chech registro.
* --- Descripcion: Permite generar reporte chech registro.
* @author Marcio Vilalta <marcio.vilalta@opentecnologia.com.co>
* @version 001
*/

include("../../../../libs/php/utility.php");
?>
<html>
	<head>
		<LINK rel = 'stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
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
		restaFechas = function(pFechaDesde,pFechaHasta) {
		 var dFechaDesde = pFechaDesde.split('-'); 
		 var dFechaHasta = pFechaHasta.split('-'); 
		 var dFechaDesdeF = Date.UTC(dFechaDesde[0],dFechaDesde[1]-1,dFechaDesde[2]); 
		 var dFechaHastaF = Date.UTC(dFechaHasta[0],dFechaHasta[1]-1,dFechaHasta[2]); 
		 var dif = dFechaHastaF - dFechaDesdeF;
		 var dias = Math.floor(dif / (1000 * 60 * 60 * 24)); 
		 return dias;
		}
		// FUNCION DE SELECT PARA CONSULTA //
		function f_GenSql()  {
			var nSwicht = 0;
			var cFechaFin;
			var dMyDate = new Date();
			
			if( document.forms['frgrm']['dDesde'].value == "" ) {
				alert("Debe Seleccionar un Rango de Fechas.");
				nSwicht = 1;
			}
			
			if((Date.parse(document.forms['frgrm']['dDesde'].value)) > (Date.parse(document.forms['frgrm']['dHasta'].value))){
				alert('La fecha inicial no puede ser mayor que la fecha final');
				nSwicht = 1;
			}
			
			if ( restaFechas(document.forms['frgrm']['dDesde'].value,document.forms['frgrm']['dHasta'].value) > 30 ) {
				alert('Las diferencias entre fechas puede ser hasta 30 dias.');
				nSwicht = 1;
			} 
			
			if ( nSwicht == 0 ) {
				var tipo;
			  for(var i = 0; i < document.getElementsByName('rTipo').length; i++) {
			    if(document.getElementsByName('rTipo')[i].checked) {
			      tipo = document.getElementsByName('rTipo')[i].value;
			    }
			  }
				var zRuta = 'frrapfxx.php?dDesde='+document.forms['frgrm']['dDesde'].value+'&dHasta='+document.forms['frgrm']['dHasta'].value + '&tipo=' + tipo ;
				//alert(zRuta);
				parent.fmpro.location = zRuta;
			}
		}
		
		function chDate(fld) {
			var val = fld.value;
			if (val.length > 0) {
				var ok = 1;
				if(val.length < 10){
					alert('Formato de Fecha debe ser aaaa-mm-dd');
					fld.value = '';
					fld.focus();
					ok = 0;
				}
				if(val.substr(4,1) == '-' && val.substr(7,1) == '-' && ok == 1) {
					var anio = val.substr(0,4);
					var mes  = val.substr(5,2);
					var dia  = val.substr(8,2);
					if (mes.substr(0,1) == '0'){
						mes = mes.substr(1,1);
					}
				
					if (dia.substr(0,1) == '0') {
						dia = dia.substr(1,1);
					}
			
					if(mes > 12) {
						alert('El mes debe ser menor a 13');
						fld.value = '';
						fld.focus();
					}
				
					if(dia > 31) {
						alert('El dia debe ser menor a 32');
						fld.value = '';
						fld.focus();
					}
					var aniobi = 28;
					
					if(anio % 4 ==  0){
						aniobi = 29;
					}
					
					if(mes == 4 || mes == 6 || mes == 9 || mes == 11) {
						if (dia < 1 || dia > 30){
							alert('El dia debe ser menor a 31, dia queda en 30');
							fld.value = val.substr(0,8)+'30';
						}
					}
			
					if(mes == 1 || mes == 3 || mes == 5 || mes == 7 || mes == 8 || mes == 10 || mes == 12){
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
				}else {
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
            <form name='frgrm' action='frmdcprn.php' method="POST">
              <center>
          	    <fieldset>
          		    <legend>Reporte AputFile</legend>
          		    <table border="2" cellspacing="0" cellpadding="0" width="500">
          		     	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
          			     	<td class="name" width="30%"><center><h3>REPORTE APUTFILE<br></h3></center></td>
          			    </tr>
          			  </table>
          		    <table border = '0' cellpadding = '0' cellspacing = '0' width='500'>
  							 		<?php $nCol = f_Format_Cols(25);
  							 		echo $nCol;?>
          			    <tr>
          			    	<td class="name" colspan = "3"><br>Tipo:</td>
          			      <td class="name" colspan = "7"><br>
          	            <input type="radio" name="rTipo" value="1" checked>APUT General
          	          </td>
          	          <td class="name" colspan = "7"><br>
          			         <input type="radio" name="rTipo" value="2">APUT DUTY
          			      </td>
          			      <td class="name" colspan = "11">&nbsp;</td>
          			      
          	        </tr>
          	       	<tr>
          	         	<td class="name" colspan = "09" width="180"><br>Rango De Fechas (Fecha Doc.):</td>
          	         	<td class="name" colspan = "2"><br><center><a href='javascript:show_calendar("frgrm.dDesde")' id="id_href_dDesde">De</a></center></td>
          	         	<td class="name" colspan = "6"><br>
          	            <input type="text" name="dDesde" style = "width:120;text-align:center"
          	               onblur="javascript:chDate(this);">
          	         	</td>
          	         	<td class="name" colspan = "2"><br><center><a href='javascript:show_calendar("frgrm.dHasta")' id="id_href_dHasta">A</a></center></td>
          	         	<td class="name" colspan = "6"><br>
          	            <input type="text" name="dHasta" style = "width:120;text-align:center"
          	              onblur="javascript:chDate(this);">
          	         	</td>
          	       	</tr>
          		    </table>
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
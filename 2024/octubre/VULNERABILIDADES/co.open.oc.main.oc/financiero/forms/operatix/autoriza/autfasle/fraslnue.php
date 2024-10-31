<?php 
  namespace openComex;
	include("../../../../libs/php/utility.php");
?>
<html>
<head>
	  <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
   	<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
   	<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
   	<script language = 'javascript'>
   	
		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
			document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
		}

		function fnLinks(xLink,xSwitch,xSecuencia,xGrid) {
			var zX    = screen.width;
			var zY    = screen.height;
			switch (xLink){
				case "cDocId":
				  if (document.forms['frgrm']['cDocId'+xSecuencia].value != "") {
  					if (xSwitch == "VALID") {						
  						var cRuta  = "frasl121.php?gModo=VALID&gFunction="+xLink+
  						             "&gDocId="+document.forms['frgrm']['cDocId'+xSecuencia].value.toUpperCase()+
  						             "&gSecuencia="+xSecuencia;
  						// alert(cRuta);
  						parent.fmpro.location = cRuta;
  					} else {
  	  				var zNx     = (zX-800)/2;
  						var zNy     = (zY-300)/2;
  						var zWinPro = 'width=800,scrollbars=1,height=350,left='+zNx+',top='+zNy;
  						var cRuta   = "fraslfrm.php?gModo=WINDOW&gArchivo=frasl121.php&gFunction="+xLink+
  						              "&gDocId="+document.forms['frgrm']['cDocId'+xSecuencia].value.toUpperCase()+
  						              "&gSecuencia="+xSecuencia;
  						// alert(cRuta);
  						zWindow = window.open(cRuta,"zWindow",zWinPro);
  				  	zWindow.focus();
  					}
					} else {
					  alert("Digite el Numero del DO.");
					}
				break;
			}
		}
		
		function f_Add_New_Row_Do() {
			
			var cGrid      = document.getElementById("Grid_Do");
			var nLastRow   = cGrid.rows.length;
			var nSecuencia = nLastRow+1;
			var cTableRow  = cGrid.insertRow(nLastRow);
			var cDocSeq  = 'cDocSeq'+ nSecuencia; // Hidden: Sucursal del DO
			var cSucId   = 'cSucId' + nSecuencia; // Hidden: Sucursal del DO
			var cDocId   = 'cDocId' + nSecuencia; // Numero del DO
			var cDocSuf  = 'cDocSuf'+ nSecuencia; // Hidden: Sufijo del DO
			var cDocTip  = 'cDocTip'+ nSecuencia;
			var cCliId   = 'cCliId' + nSecuencia;
			var cCliDv   = 'cCliDv' + nSecuencia;
			var cCliNom  = 'cCliNom'+ nSecuencia; //Estado
			var oBtnDel  = 'oBtnDel'+ nSecuencia; // Boton de Borrar Row
			
			
			var TD_xAll = cTableRow.insertCell(0);
			TD_xAll.style.border= "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040;border:0;text-align:center' name = '"+cDocSeq+"' id = '"+cDocSeq+"' value = '"+f_Str_Pad(nSecuencia,3,"0","STR_PAD_LEFT")+"'readonly>";
			
			TD_xAll = cTableRow.insertCell(1);
			TD_xAll.style.border= "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040;border:0;text-align:center' name = '"+cSucId+"' id = '"+cSucId+"' readonly>";
			
			TD_xAll = cTableRow.insertCell(2);
			TD_xAll.style.border= "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:140;border:0;text-align:left'  name = '"+cDocId+"' id = '"+cDocId+"'  "+
													"onBlur = 'javascript:this.value=this.value.toUpperCase(); "+
																		"fnLinks(\"cDocId\",\"VALID\",\""+nSecuencia+"\")'>";
																		
			TD_xAll = cTableRow.insertCell(3);
			TD_xAll.style.border= "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:040;border:0;text-align:center' name = '"+cDocSuf+"' id = '"+cDocSuf+"' readonly>";
			
			TD_xAll = cTableRow.insertCell(4);
			TD_xAll.style.border= "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:120;border:0;text-align:center' name = '"+cDocTip+"' id = '"+cDocTip+"' readonly>";
			
			TD_xAll = cTableRow.insertCell(5);
			TD_xAll.style.border= "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:100;border:0;text-align:center' name = '"+cCliId+"' id = '"+cCliId+"' readonly>";
			
			TD_xAll = cTableRow.insertCell(6);
			TD_xAll.style.border= "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:020;border:0;text-align:center' name = '"+cCliDv+"' id = '"+cCliDv+"' readonly>";
			
			TD_xAll = cTableRow.insertCell(7);
			TD_xAll.style.border= "1px solid #E6E6E6";
      TD_xAll.innerHTML    = "<input type = 'text' class = 'letra' style = 'width:280;border:0;text-align:left' name = '"+cCliNom+"' id = '"+cCliNom+"' onKeyUp='javascript:f_Enter(event,this.name,\"Grid_Do\");' readonly>";
			
			TD_xAll = cTableRow.insertCell(8);
      TD_xAll.innerHTML    = "<input type = 'button' class = 'letra' style = 'width:020;text-align:center' id = "+nSecuencia+" value = 'X' "+
														  "onClick = 'javascript:f_Delete_Row(this.value,\""+nSecuencia+"\",\"Grid_Do\", this);'>";
														  
			document.forms['frgrm']['nSecuencia'].value = nSecuencia;
		}
		
		function f_Enter(e,xName,xGrilla) {
			var nSecuencia = document.forms['frgrm']['nSecuencia'].value;
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
				switch (xGrilla) {
					case "Grid_Do":
						if (xName == 'cCliNom'+eval(document.forms['frgrm']['nSecuencia'].value)) {
							if (document.forms["frgrm"][xName].value !== '' ) {
								f_Add_New_Row_Do();
							} else {
								alert("Seleccione un DO antes de Adicionar una nueva Fila.");
							}
						}
					break;
				}
			}
		}
		
		function f_Delete_Row(xNumRow,xSecuencia,xTabla) {        
      switch (xTabla) {
        case "Grid_Do": 
          var cGrid = document.getElementById(xTabla);
          var nLastRow = cGrid.rows.length;
          if (nLastRow > 1 && xNumRow == "X") {
            if (confirm("Realmente Desea Eliminar el DO ["+document.forms["frgrm"]["cSucId"+xSecuencia].value+"-"+document.forms["frgrm"]["cDocId"+xSecuencia].value+"-"+document.forms['frgrm']['cDocSuf'+xSecuencia].value+"]?")){ 
              if(xSecuencia < nLastRow){
                var j=0;
                for(var i=xSecuencia;i<nLastRow;i++){
                  j = parseFloat(i)+1;
                  document.forms['frgrm']['cDocSeq'+ i].value = f_Str_Pad(i,3,"0","STR_PAD_LEFT"); 
                  document.forms['frgrm']['cSucId' + i].value = document.forms['frgrm']['cSucId' + j].value; 
                  document.forms['frgrm']['cDocId' + i].value = document.forms['frgrm']['cDocId' + j].value; 
                  document.forms['frgrm']['cDocSuf'+ i].value = document.forms['frgrm']['cDocSuf'+ j].value;
                  document.forms['frgrm']['cDocTip'+ i].value = document.forms['frgrm']['cDocTip'+ j].value;
                  document.forms['frgrm']['cCliId' + i].value = document.forms['frgrm']['cCliId' + j].value; 
                  document.forms['frgrm']['cCliDv' + i].value = document.forms['frgrm']['cCliDv' + j].value; 
                  document.forms['frgrm']['cCliNom'+ i].value = document.forms['frgrm']['cCliNom'+ j].value;
                }
              }
              cGrid.deleteRow(nLastRow - 1);
              document.forms['frgrm']['nSecuencia'].value = nLastRow - 1;
            }
          } else {
            alert("No se Pueden Eliminar Todas las Secuencias, Verifique.");
          }
        break;
        default: //No hace nada
        break;
      }
    }
    
		function fnPegarDo() {
			var nSecuencia = document.forms['frgrm']['nSecuencia'].value;
			var nX    = screen.width;
			var nY    = screen.height;
			var nAncho = 550;
			var nAlto  = 250;
			var nNx      = (nX-nAncho)/2;
			var nNy      = (nY-nAlto)/2;
			var cWinOpt  = "width="+nAncho+",scrollbars=1,height="+nAlto+",left="+nNx+",top="+nNy;
			var cPathUrl = "fraslfrm.php?gFunction=PegarDo&gArchivo=fraslcpd.php&gSecuencia="+document.forms['frgrm']['nSecuencia'].value;
			cWindow = window.open(cPathUrl,"PegarDo",cWinOpt);
  		cWindow.focus();
		}
	</script>
  </head>
		<body topmargin = 0 leftmargin = 0 marginwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="800">
				<tr>
					<td>
				  	<fieldset>
				  		<!-- se pinta el fielset con el proceso y la descripcion de la cookie -->
					  	<legend>Nuevo <?php echo $_COOKIE['kProDes'] ?></legend>
						 	<form name = 'frgrm' action = 'fraslgra.php' method = 'post' target='fmpro'>
						 		<input type = "hidden" name = "nSecuencia"  value = "">
							 	<center>
							 	    <table border = "0" cellpadding = "0" cellspacing = "0" width = "800">
                      <tr>
                        <td class = "clase08" width = "020" align="right"><img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png" onClick = "javascript:fnPegarDo()" style = "cursor:pointer" title="Pegar DOs"></td>                       
                      </tr>
                    </table>
      							<table border = "0" cellpadding = "0" cellspacing = "0" width = "800">
  									  <tr>
                        <td class = "clase08" width = "040" align="left">Seq.</td>                                                         
                        <td class = "clase08" width = "040" align="left">Suc</td>                                                          
                        <td class = "clase08" width = "140" align="left">Do</td>
                        <td class = "clase08" width = "040" align="left">Suf</td>
                        <td class = "clase08" width = "120" align="left">&nbsp;&nbsp;Operaci&oacute;n</td>                                                                     
                        <td class = "clase08" width = "100" align="left">&nbsp;&nbsp;Nit</td>
                        <td class = "clase08" width = "020" align="left">&nbsp;&nbsp;Dv</td>
                        <td class = "clase08" width = "280" align="left">&nbsp;&nbsp;&nbsp;Importador</td>
                        <td class = "clase08" width = "020" align="right">&nbsp;</td>                       
                      </tr>
  									</table>
										<table border = "0" cellpadding = "0" cellspacing = "0" width = "800" id = "Grid_Do"></table>
								</center>
			 	      </form>
						</fieldset>
					</td>
				</tr>
		 	</table>
		
  		<table border="0" cellpadding="0" cellspacing="0" width="800">
  			<tr height="21">
  						<td width="618" height="21">&nbsp;</td>
  						<td width="91" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer"
  						 onClick = "javascript:document.forms['frgrm'].submit();"
  												style = "cursor:hand" id="IdImg">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Autorizar</td>
  						<td width="91" height="21" class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>			  	
  			</tr>
  		</table>
    </center>
    <script languaje = "javascript">
      f_Add_New_Row_Do();
    </script>
		
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->

		<?php  function f_CargaData($xSerId) {
			
		} ?>
	</body>        
  </body> 
</html>
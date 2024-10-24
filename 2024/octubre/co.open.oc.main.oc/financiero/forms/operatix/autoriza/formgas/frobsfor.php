<?php
  namespace openComex;
   /**
	 * Reporte por Observaciones Formulario
	 * --- Descripcion: Me lista todas observaciones digitadas para autorizacion Formulario al Gasto.
	 * @author Johana Arboleda Ramos <dp1@opentecnologia.com.co>
	 * @version 001
	 */
	include("../../../../libs/php/utility.php"); ?>

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

	     function f_Links(xLink,xSwitch) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink) {
				  case "cGofId":
						if (xSwitch == "VALID") {
							var cPathUrl  = "frpa123a.php?gWhat=VALID&gFunction="+xLink+
							                          "&gGofId="+document.frgrm['cGofId'].value.toUpperCase();
							parent.fmpro.location = cPathUrl;
						} else {
	  					var nNx     = (nX-400)/2;
							var nNy     = (nY-250)/2;
							var cWinOpt = 'width=400,scrollbars=1,height=250,left='+nNx+',top='+nNy;
							var cPathUrl   = "frpa123a.php?gWhat=WINDOW&gFunction="+xLink+
							                           "&gGofId="+document.frgrm['cGofId'].value.toUpperCase();
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

  			if(nSwitch == 0){
  			 var cTipo = 0;
    		  for (i=0;i<2;i++){
    		    if (document.forms['frgrm']['rTipo'][i].checked == true){
    		  	  cTipo = i+1;
    		      break;
    		    }
    		  }
    		  
    		  var cPathUrl = 'frobsprn.php?gDesde='+document.forms['frgrm']['dDesde'].value+
                         '&gHasta=' +document.forms.frgrm['dHasta'].value     +
                         '&gGofId=' +document.forms['frgrm']['cGofId'].value  +
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
    <table border ="0" cellpadding="0" cellspacing="0" width="460">
      <tr>
        <td>
        	<form name='frgrm' action='frobsprn.php' method="POST">
            <fieldset>
              <legend>Reporte de Observaciones Formulario</legend>
              <br>
              <center>
                <table border="2" cellspacing="0" cellpadding="0" width="440">
                  <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
                    <td class="name"><center><h5><br>REPORTE DE OBSERVACIONES POR FORMULARIO</h5></center></td>
                  </tr>
                </table>
                <table border = '0' cellpadding = '0' cellspacing = '0' width='440'>
                  <?php $nCol = f_Format_Cols(22);
                  echo $nCol;?>
                  <tr>
        	          <td class="name" colspan = "7"><br>Desplegar en:
        			      </td>
        			      <td class="name" colspan = "5"><br>
        	            <input type="radio" name="rTipo" value="1" checked>Pantalla
        	          </td>
        	          <td class="name" colspan = "5"><br>
        			         <input type="radio" name="rTipo" value="2">Excel
        			      </td>
        			      <td class="name" colspan = "5"><br>
        			        <input type="radio" name="rTipo" value="3">Pdf<br>
        			      </td>
        	        </tr>
        	       </table>
                <table border = '0' cellpadding = '0' cellspacing = '0' width='440'>
                  <?php $nCol = f_Format_Cols(22);
                  echo $nCol;?>
                  <tr>
          	         <td class="name" colspan = "8"><br>Rango De Fechas:</td>
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
                    <td class="name" colspan = "03"><br>
                      <a href = "javascript:document.forms['frgrm']['cGofId'].value  = '';
                                            document.forms['frgrm']['cGofDes'].value = '';
                                            f_Links('cGofId','VALID')" id="vDir">Grupo:</a>
                    </td>
                    <td class="name" colspan = "04"><br>
                      <input type="text" name="cGofId" style = "width:080"
                            onfocus="javascript:document.forms['frgrm']['cGofId'].value  = '';
                                                document.forms['frgrm']['cGofDes'].value = '';
                                                this.style.background='#00FFFF'"
                            onBlur = "javascript:this.value=this.value.toUpperCase();
                                                f_Links('cGofId','VALID');
                                                this.style.background='#FFFFFF'">
                    </td>
                    <td class="name" colspan = "15"><br>
                      <input type="text" name="cGofDes" style = "width:300" readonly>
                    </td>
                  </tr>
               </table>
               <br>
              </center>
            </fieldset>
            <center>
              <table border="0" cellpadding="0" cellspacing="0" width="440">
                <tr height="21">
                  <td width="258" height="21"></td>
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
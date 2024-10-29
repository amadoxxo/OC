<?php
  namespace openComex;
	 /**
	 * Imprime AUDITORIA RENTABILIDAD.
	 * --- Descripcion: Permite Imprimir AUDITORIA RENTABILIDAD.
	 * @author Johana Arboleda <dp5@opentecnologia.com.co> 
	 * @version 001
	 */

	include("../../../../libs/php/utility.php");
?>
<html>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
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
				  case "cVenId":
					case "cVenNom":
					 if (xSwitch == "VALID") {
							var cPathUrl = "frpar150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gVenId="+document.forms['frgrm']['cVenId'].value.toUpperCase()+
																				"&gVenNom="+document.forms['frgrm']['cVenNom'].value.toUpperCase();
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-600)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "frpar150.php?gModo="+xSwitch+"&gFunction="+xLink+
																				 "&gVenId="+document.forms['frgrm']['cVenId'].value.toUpperCase()+
																				 "&gVenNom="+document.forms['frgrm']['cVenNom'].value.toUpperCase();
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
				  		cWindow.focus();
						}
					break;
					case "cLinId":
					 if (xSwitch == "VALID") {
							var cPathUrl = "frpar119.php?gModo="+xSwitch+"&gFunction="+xLink+
																				"&gLinId="+document.forms['frgrm']['cLinId'].value.toUpperCase();
							parent.fmpro.location = cPathUrl;
						} else {
							var nNx      = (nX-600)/2;
							var nNy      = (nY-250)/2;
							var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
							var cPathUrl = "frpar119.php?gModo="+xSwitch+"&gFunction="+xLink+
																				 "&gLinId="+document.forms['frgrm']['cLinId'].value.toUpperCase();
							cWindow = window.open(cPathUrl,xLink,cWinOpt);
				  		cWindow.focus();
						}
					break;
				}
	    }
			// FUNCION DE SELECT PARA CONSULTA //
			function f_GenSql()  {
  			var nSwicht = 0;
  			var cMsj = "\n";
  			var cAplCom;
  			if (document.forms['frgrm']['rAplCom'][0].checked == true) {
  			  var cAplCom = "SI";
  			 
	 		    if (document.forms['frgrm']['cAnoIni'].value == '' || document.forms['frgrm']['cAnoFin'].value == '') {
	 		      cMsj += 'Debe Seleccionar los Años a Comparar.\n';
	 		      nSwicht = 1;
	 		    }
	 		    
	 		    var cAnoIni = document.forms['frgrm']['cAnoIni'].value * 1;
	 		    var cAnoFin = document.forms['frgrm']['cAnoFin'].value * 1;
	 		    
	 		    if (cAnoFin <= cAnoIni) {
	 		      cMsj += 'El Año Final debe ser Mayor al Incial.\n';
	 		      nSwicht = 1;
	 		    }
	 		    
	 		  } else {
	 		    var cAplCom = "NO";
	 		    var dFecIni = document.forms['frgrm']['cAnoN'].value + "-" + document.forms['frgrm']['cMesIniN'].value;
	 		    var dFecFin = document.forms['frgrm']['cAnoN'].value + "-" + document.forms['frgrm']['cMesFinN'].value;
	 		    
	 		    if (document.forms['frgrm']['cAnoN'].value == '' || document.forms['frgrm']['cMesIniN'].value == '' || document.forms['frgrm']['cMesFinN'].value == '') {
	 		      cMsj += 'Debe Ingresar el Rango de Fechas.\n';
	 		      nSwicht = 1;
	 		    }
	 		    
	 		    var dHasta = dFecFin.replace(/-/gi,'') * 1;
	 		    var dDesde = dFecIni.replace(/-/gi,'') * 1;
	 		  
	 		    if (dHasta < dDesde) {
	 		      cMsj += 'La Fecha Final debe ser Mayor a la Inicial.\n';
	 		      nSwicht = 1;
	 		    }
	 		  }
  			
				 if(nSwicht != 1){
    			  var cTipo = 0;
    			  var cTipCta = "";
    			  for (i=0;i<2;i++){
    			    if (document.forms['frgrm']['rTipo'][i].checked == true){
    			  	  cTipo = i+1;
    			      break;
    			    }
    			  }
    			  var cRuta = 'frareprn.php?cTipo='    + document.forms['frgrm']['rTipo'][i].value +
    			                           '&cAplCom=' + cAplCom  +    			                           
    			                           '&cAnoIni=' + document.forms['frgrm']['cAnoIni'].value  +
    			                           '&cAnoFin=' + document.forms['frgrm']['cAnoFin'].value  +
    			                           '&cMesIni=' + document.forms['frgrm']['cMesIni'].value  +
    			                           '&cMesFin=' + document.forms['frgrm']['cMesFin'].value  +       			                            			                           
    			                           '&cAnoN='   + document.forms['frgrm']['cAnoN'].value    +
    			                           '&cMesIniN='+ document.forms['frgrm']['cMesIniN'].value +
    			                           '&cMesFinN='+ document.forms['frgrm']['cMesFinN'].value +    			                           
    			                           '&cLinId='  + document.forms['frgrm']['cLinId'].value   +
    			                           '&cSucId='  + document.forms['frgrm']['cSucId'].value   +
    			                           '&cVenId='  + document.forms['frgrm']['cVenId'].value  +
    			                           '&cVenNom=' + document.forms['frgrm']['cVenNom'].value  +
    			                           '&cEstId='  + document.forms['frgrm']['cEstId'].value   +
    			                           '&cTipOpe=' + document.forms['frgrm']['cTipOpe'].value;
    			                           
    			  if(document.forms['frgrm']['rTipo'][i].value == 2){        			  	
    			  	parent.fmpro.location = cRuta;
    			  }else{
       				var zX      = screen.width;
      				var zY      = screen.height;
      				var zNx     = (zX-30)/2;
      				var zNy     = (zY-100)/2;
      				var zNy2    = (zY-100);
      				var zWinPro = "width="+zX+",scrollbars=1,height="+zNy2+",left="+1+",top="+50;
      				var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
      				zWindow = window.open(cRuta,cNomVen,zWinPro);
      				zWindow.focus();
    			  }
          } else {
            alert(cMsj + "Verifique.");
          }
		   }
		   
		   function f_Aplica_Comparativo(){
	 		  if (document.forms['frgrm']['rAplCom'][0].checked == true) {
	 		    document.getElementById('tblNoAplica').style.display = "none";
	 		    document.getElementById('tblAplica').style.display   = "block";
	 		  } else {
	 		    document.getElementById('tblNoAplica').style.display = "block";
	 		    document.getElementById('tblAplica').style.display   = "none";
	 		  }
	 		}
	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="500">
				<tr>
					<td>
            <form name='frgrm' action='frfacprn.php' method="POST">
              <center>
          	    <fieldset>
          		    <legend>Consulta Auditoria Rentabilidad</legend>
          		    <table border="2" cellspacing="0" cellpadding="0" width="500">
          		     	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
          			     	<td class="name" width="30%"><center><h5><br>AUDITORIA RENTABILIDAD</h5></center></td>
          			    </tr>
          			  </table>
                  <table border = '0' cellpadding = '0' cellspacing = '0' width='480'>
                    <?php $nCol = f_Format_Cols(24);
                    echo $nCol;?>
                    <tr>
                      <td class="name" colspan = "8" style = "width:160"><br>Desplegar en<br></td>
                      <td class="name" colspan = "5"><br>
                        <input type="radio" name="rTipo" value="1" checked>Pantalla
                      </td>
                      <td class="name" colspan = "11"><br>
                        <input type="radio" name="rTipo" value="2">Excel
                      </td>                     
                    </tr>
                    <tr>
                      <td class="name" colspan = "8"><br>Aplica Comparativo<br></td>
                      <td class="name" colspan = "5"><br>
                        <label><input type="radio" name="rAplCom" value="SI" onclick="javascript:f_Aplica_Comparativo()">SI</label>
                      </td>
                      <td class="name" colspan = "11"><br>
                        <label><input type="radio" name="rAplCom" value="NO" onclick="javascript:f_Aplica_Comparativo()" checked>NO</label>
                      </td>
                    </tr>
                    <tr>
                      <td class="name" colspan = "24">
                        <table border = '0' cellpadding = '0' cellspacing = '0' width='460' id="tblAplica">
                          <?php $nCol = f_Format_Cols(23);
                          echo $nCol;?>
                          <tr>
                            <td class="name" colspan = "8"><br>A&ntilde;os a Comparar<br></td>
                            <td class="name" colspan = "15"><br>
                              <select Class = "letrase" style = "width:80;text-align:left" name = "cAnoIni">
                                <?php 
                                for ($i=$vSysStr['financiero_ano_instalacion_modulo']; $i<=date('Y');$i++){ ?>
                                  <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                <?php } ?>  							 		
                              </select>
                              &nbsp;&nbsp;&nbsp;&nbsp;Y&nbsp;&nbsp;&nbsp;&nbsp;
                              <select Class = "letrase" style = "width:80;text-align:left" name = "cAnoFin">
                                <?php for ($i=$vSysStr['financiero_ano_instalacion_modulo']; $i<=date('Y');$i++){ ?>
                                  <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                <?php } ?>  							 		
                              </select>
                            </td>
                          </tr>
                          <tr>
                            <td class="name" colspan = "8"><br>Rango De Meses<br></td>
                            <td class="name" colspan = "15" ><br>
                              <select Class = "letrase" style = "width:80;text-align:left" name = "cMesIni">
                                <option value="" selected></option>
                                <?php for ($i=1; $i<=12;$i++){ ?>
                                  <option value="<?php echo str_pad($i,2,"0",STR_PAD_LEFT) ?>"><?php echo str_pad($i,2,"0",STR_PAD_LEFT) ?></option>
                                <?php } ?>  							 		
                              </select>
                              &nbsp;&nbsp;&nbsp;&nbsp;A&nbsp;&nbsp;&nbsp;&nbsp;
                              <select Class = "letrase" style = "width:80;text-align:left" name = "cMesFin">
                                <option value="" selected></option>
                                <?php for ($i=1; $i<=12;$i++){ ?>
                                  <option value="<?php echo str_pad($i,2,"0",STR_PAD_LEFT) ?>"><?php echo str_pad($i,2,"0",STR_PAD_LEFT) ?></option>
                                <?php } ?> 							 		
                              </select>
                            </td>
                          </tr>
                        </table>
                        <table border = '0' cellpadding = '0' cellspacing = '0' width='460' id="tblNoAplica">
                          <?php $nCol = f_Format_Cols(23);
                          echo $nCol;?>
                          <tr>
                            <td class="name" colspan = "8"><br>A&ntilde;o<br></td>
                            <td class="name" colspan = "15"><br>
                              <select Class = "letrase" style = "width:80;text-align:left" name = "cAnoN">
                                <?php 
                                for ($i=$vSysStr['financiero_ano_instalacion_modulo']; $i<=date('Y');$i++){ ?>
                                  <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                <?php } ?>  							 		
                              </select>
                            </td>
                          </tr>
                          <tr>
                            <td class="name" colspan = "8"><br>Rango De Meses<br></td>
                            <td class="name" colspan = "15"><br>
                              <select Class = "letrase" style = "width:80;text-align:left" name = "cMesIniN">
                                <?php for ($i=1; $i<=12;$i++){ ?>
                                  <option value="<?php echo str_pad($i,2,"0",STR_PAD_LEFT) ?>"><?php echo str_pad($i,2,"0",STR_PAD_LEFT) ?></option>
                                <?php } ?>  							 		
                              </select>
                              &nbsp;&nbsp;&nbsp;&nbsp;A&nbsp;&nbsp;&nbsp;&nbsp;
                              <select Class = "letrase" style = "width:80;text-align:left" name = "cMesFinN">
                                <?php for ($i=1; $i<=12;$i++){ ?>
                                  <option value="<?php echo str_pad($i,2,"0",STR_PAD_LEFT) ?>"><?php echo str_pad($i,2,"0",STR_PAD_LEFT) ?></option>
                                <?php } ?> 							 		
                              </select>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td Class = "name" colspan = "8"><br>
                        <a href = "javascript:document.forms['frgrm']['cLinId'].value  = '';
                                              document.forms['frgrm']['cLinDes'].value = '';
                                              f_Links('cLinId','WINDOW')" id="id_href_SucId">Sucursal Comercial</a><br>
                      </td>
                      <td Class = "name" colspan = "16"><br>
                        <input type = "text" Class = "letra" style = "width:60;text-align:left" name = "cLinId"
                               onfocus="javascript:this.style.background='#00FFFF'"
                               onBlur = "javascript:this.value=this.value.toUpperCase();
                                                    f_Links('cLinId','VALID');
                                                    this.style.background='#FFFFFF'">
                        <input type = "text" Class = "letra" style = "width:140" name = "cLinDes" readonly>
                      </td>
                    </tr> 
                    <tr>
                      <td Class = "name" colspan = "8"><br>Sucursal Operativa<br></td>
                      <td Class = "name" colspan = "16"><br>
                        <select Class = "letrase" style = "width:200;text-align:left" name = "cSucId">
                          <option value="">TODAS</option>
                          <?php //Busco sucrsales
                          $qSucDes = "SELECT sucidxxx, sucdesxx FROM $cAlfa.fpar0008 WHERE regestxx = \"ACTIVO\" ORDER BY sucdesxx";
                          $xSucDes  = f_MySql("SELECT","",$qSucDes,$xConexion01,"");
                          while ($xRSD = mysql_fetch_array($xSucDes)){ ?>
                            <option value="<?php echo $xRSD['sucidxxx'] ?>"><?php echo $xRSD['sucdesxx'] ?></option>
                          <?php } ?>  							 		
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td Class = "name" colspan = "8"><br>
                        <a href = "javascript:document.forms['frgrm']['cVenId'].value  = '';
                                              document.forms['frgrm']['cVenNom'].value = '';
                                              f_Links('cVenId','WINDOW')" id="id_href_cVenId">Vendedor</a><br>
                      </td>
                      <td Class = "name" colspan = "5"><br>
                        <input type = "text" Class = "letra" style = "width:100;text-align:left" name = "cVenId"
                               onfocus="javascript:this.style.background='#00FFFF'"
                              onBlur = "javascript:this.value=this.value.toUpperCase();
                                                   f_Links('cVenId','VALID');
                                                   this.style.background='#FFFFFF'">
                      	<a href="#"></a>
                      </td>
                       <td Class = "name" colspan = "11"><br>
                        <input type = "text" Class = "letra" style = "width:220" name = "cVenNom"
                               onfocus="javascript:this.style.background='#00FFFF'"
                        onBlur = "javascript:this.value=this.value.toUpperCase();
                                             f_Links('cVenNom','VALID');
                                             this.style.background='#FFFFFF'">
                      </td>
                    </tr>
                    <tr>
                      <td Class = "name" colspan = "8"><br>Estado DO<br></td>
                      <td Class = "name" colspan = "16"><br>
                        <select Class = "letrase" style = "width:140;text-align:left" name = "cEstId">
                          <option value="">TODOS</option>
                          <option value="ACTIVO">ACTIVO</option>
                          <option value="FACTURADO">FACTURADO</option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td Class = "name" colspan = "8"><br>Tipo Operaci&oacute;n<br></td>
                      <td Class = "name" colspan = "16"><br>
                        <select Class = "letrase" style = "width:140;text-align:left" name = "cTipOpe">
                          <option value="">TODOS</option>
                          <option value="IMPORTACION">IMPORTACION</option>
                          <option value="EXPORTACION">EXPORTACION</option>
                          <option value="TRANSITO">TRANSITO</option>
                          <option value="OTROS">OTROS</option>
                        </select>
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
          	  <script languaje="javascript">
          	   f_Aplica_Comparativo();
          	  </script>
					</td>
				</tr>
		 	</table>
		</center>
	</body>
</html>
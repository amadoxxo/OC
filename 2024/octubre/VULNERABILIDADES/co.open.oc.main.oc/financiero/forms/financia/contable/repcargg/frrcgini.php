<?php
  namespace openComex;
	 /**
	 * Reporte Cartera Grupos de Gestion
	 * @author Cristian Cardona <cristian.cardona@opentecnologia.com.co>
	 * @version 001
	 */

	include("../../../../libs/php/utility.php");
	
	/**
	 *  Cookie fija
	 */
	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb   = $kDf[3];
	$kUser      = $kDf[4];
	$kLicencia  = $kDf[5];
	$swidth     = $kDf[6];
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
				if (document.forms['frnav']['cGruSer'].value !=""){ 
					var cGruSer = document.forms['frnav']['cGruSer'].value
					var nX = screen.width;
					var nY = screen.height;
					
					switch (xLink) {
						case "cTerId":
						case "cTerNom":
							if (xLink == "cTerId" || xLink == "cTerNom") {
								var cTerId  = document.forms['frnav']['cTerId'].value.toUpperCase();
								var cTerNom = document.forms['frnav']['cTerNom'].value.toUpperCase();
							}
							
							if (xSwitch == "VALID") {
								var cPathUrl = "fresc151.php?gModo="+xSwitch+"&gFunction="+xLink+"&gTerId="+cTerId+"&gTerNom="+cTerNom+"&gGruSer="+cGruSer;
								parent.fmpro.location = cPathUrl;
							} else {
								var nNx      = (nX-600)/2;
								var nNy      = (nY-250)/2;
								var cWinOpt  = "width=600,scrollbars=1,height=250,left="+nNx+",top="+nNy;
								var cPathUrl = "fresc151.php?gModo="+xSwitch+"&gFunction="+xLink+"&gTerId="+cTerId+"&gTerNom="+cTerNom+"&gGruSer="+cGruSer;
								cWindow = window.open(cPathUrl,xLink,cWinOpt);
								cWindow.focus();
							}
						break;
					}
				} else { 
					alert('Debe seleccionar un grupo de gestion');
					document.forms['frnav']['cTerId'].value = '';
					document.forms['frnav']['cTerNom'].value = ''; 
				}
	    }

			// FUNCION DE SELECT PARA CONSULTA //
			function f_GenSql()  {
				var nSwicht = 0;
				var cMsj = "\n";

				if (document.forms['frnav']['dHasta'].value == '') {
					nSwicht = 1;
					cMsj += "La Fecha de Corte no Puede Ser Vacia.\n";
				}
 
        if (nSwicht == 0) {
          var cTipo = 0;
          for (i=0;i<2;i++){
            if (document.forms['frnav']['rTipo'][i].checked == true){
              cTipo = i+1;
              break;
            }
          }
          
          if(cTipo == 2) {                 
        	  document.forms['frnav'].target='fmpro';
            document.forms['frnav'].submit();
          }else{
        	  var zX      = screen.width;
            var zY      = screen.height;
            var zNx     = (zX-30)/2;
            var zNy     = (zY-100)/2;
            var zNy2    = (zY-100);
            var zWinPro = "width="+zX+",scrollbars=1,height="+zNy2+",left="+1+",top="+50;
            var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
            zWindow = window.open('',cNomVen,zWinPro);
                    
            document.forms['frnav'].target=cNomVen;
            document.forms['frnav'].submit();
            zWindow.focus();
          }
        } else {
           alert(cMsj + "Verifique.")
        }	
		  }
	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="500">
				<tr>
					<td>
            <form name='frnav' action='frrcgprn.php' method="post" target="fmpro">
              <center>
          	    <fieldset>
          		    <legend>Consulta Estado de Cuenta </legend>
          		    <table border="2" cellspacing="0" cellpadding="0" width="500">
          		     	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' style="heigth:25">
          			     	<td class="name" width="30%"><center><h5><br>REPORTE ESTADO DE CARTERA</h5></center></td>
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
                    <tr height="10">
                    	<!-- Espacio -->
                    </tr>
          			    <tr>
          			      <td class="name" colspan = "25">Grupo de Servicios<br>
          	            <select name = 'cGruSer' style = 'width:500' onChange= "javascript:document.forms['frnav']['cTerId'].value  = '';
																																										  		 document.forms['frnav']['cTerNom'].value = '';
																																													 document.forms['frnav']['cTerDV'].value  = ''">
													<?php
	          	            $qGruGes  = "SELECT "; 
	          	            $qGruGes .= "zsiac002.usridxxx, "; 
	          	            $qGruGes .= "zsiac002.grugesid, "; 
	          	            $qGruGes .= "zsiac002.regestxx, "; 
	          	            $qGruGes .= "zsiac001.grugesde ";
	          	            $qGruGes .= "FROM $cAlfa.zsiac002 ";
	          	            $qGruGes .= "LEFT JOIN $cAlfa.zsiac001 ON $cAlfa.zsiac002.grugesid = $cAlfa.zsiac001.grugesid ";
	          	            $qGruGes .= "WHERE "; 
													if ($kUser != "ADMIN" && $cUsrInt != "SI") {
	          	            	$qGruGes .= "zsiac002.usridxxx = \"$kUser\" AND ";
													}
	          	            $qGruGes .= "zsiac002.regestxx = \"ACTIVO\"";
	          	            $xGruGes = f_MySql("SELECT","",$qGruGes,$xConexion01,"");
	          	            
          	            	if (mysql_num_rows($xGruGes) > 0) {
          	            		while ($xRSD = mysql_fetch_array($xGruGes)) { ?>
          	            			<option value = "<?php echo $xRSD['grugesid']?>"><?php echo $xRSD['grugesde'] ?></option>
          	            	<?php }
													} ?>
                        </select>
          	          </td>
          	        </tr>
          	       <tr>
										<td Class = "name" colspan = "5"><br>
											<a href = "javascript:document.forms['frnav']['cTerId'].value  = '';
																		  		  document.forms['frnav']['cTerNom'].value = '';
																						document.forms['frnav']['cTerDV'].value  = '';
																						f_Links('cTerId','VALID')" id="id_href_cTerId">Nit</a><br>
											<input type = "text" Class = "letra" style = "width:100;text-align:center" name = "cTerId"
												onfocus="javascript:document.forms['frnav']['cTerId'].value  = '';
            						  									document.forms['frnav']['cTerNom'].value = '';
																				    document.forms['frnav']['cTerDV'].value  = '';
													                  this.style.background='#00FFFF'"
									    	onBlur = "javascript:this.value=this.value.toUpperCase();
																		         f_Links('cTerId','VALID');
																		         this.style.background='#FFFFFF'">
										</td>
										<td Class = "name" colspan = "1"><br>Dv<br>
											<input type = "text" Class = "letra" style = "width:20;text-align:center" name = "cTerDV" readonly>
										</td>
										<td Class = "name" colspan = "19"><br>Cliente<br>
											<input type = "text" Class = "letra" style = "width:380" name = "cTerNom"
									    	onfocus="javascript:document.forms['frnav']['cTerId'].value  = '';
            						  									document.forms['frnav']['cTerNom'].value = '';
																				    document.forms['frnav']['cTerDV'].value  = '';
													                  this.style.background='#00FFFF'"
									    	onBlur = "javascript:this.value=this.value.toUpperCase();
													                   f_Links('cTerNom','VALID');
													                   this.style.background='#FFFFFF'">
										</td>
          	       </tr>
          	       <tr>
										<td Class = "name" colspan = "5">
											<br><a href="javascript:show_calendar('frnav.dHasta')" id="idFCor">Fecha de Corte:</a>
										</td>
										<td Class = "name" colspan = "5">
											<br><input type = "text" Class = "letra" style = "width:100;text-align:center" name = "dHasta" readonly>
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
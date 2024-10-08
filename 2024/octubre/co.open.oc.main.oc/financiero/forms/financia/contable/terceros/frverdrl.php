<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
?>
<!--
	Los Parametros que Recibo son :
	$gSearch = Un dato para filtrar la consulta con el WHERE
	$gFields = A que campos del formulario voy a devolver los datos
	$gWhat   = Que voy a hacer, una ventana o una validacion
-->
<?php
  //Armando matriz con los requisitos legales seleccionados
  $mReqLeg = array();
 	$mAxu = explode("~",$cCliDrl);
 	for ($i=0; $i<count($mAxu); $i++) {
		if ($mAxu[$i] != "") {
			$mAux02 = array();
			$mAux02 = explode(",",$mAxu[$i]);
			if ($mAux02[0] != "") {
				$mReqLeg[$mAux02[0]]['fechaxxx'] = $mAux02[1];
				$mReqLeg[$mAux02[0]]['vencimie'] = $mAux02[2];
			}
		}
	}
  
?>
	<html>
		<head>
			<title>Documentos Requisitos Legales</title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
	   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
	   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
	   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
	   	<script languaje = 'javascript'>
	   	function f_Guardar(){
	   		var nSwich = 0;
	      var cMsj = "\n";
	        
	   		var cadeni   = document.forms['frcotoplo']['cCadena'].value;
	   		var cadeni1  = "";

	   		if (cadeni.length < 1)  {
            nSwich = 1;
            cMsj += "Debe Seleccionar al Menos un Documento.\n";
        } else {
	        
		   		var mAuxId = cadeni.split("~");
		   		
		   		for (var i=0; i<mAuxId.length; i++) {
		   			if (mAuxId[i] != "") {
		   			  if(document.forms['frgrm']['cDrlFec'+mAuxId[i]].value == "0000-00-00" || document.forms['frgrm']['cDrlFec'+mAuxId[i]].value == "") {
				   		 	nSwich = 1;
				        cMsj += "La Fecha del Requisito Legal "+mAuxId[i]+" No Puede Ser Vacia.\n";
			   		 	} else {
								//Validando que fecha de vencimiento sea mayor a la fecha incial
				   			if(document.forms['frgrm']['cDrlVen'+mAuxId[i]].value != "0000-00-00" && document.forms['frgrm']['cDrlVen'+mAuxId[i]].value != "") {

				   				if (Date.parse(document.forms['frgrm']['cDrlFec'+mAuxId[i]].value) > Date.parse(document.forms['frgrm']['cDrlVen'+mAuxId[i]].value)) {
					   		 		nSwich = 1;
					        	cMsj += "La Fecha del Requisito Legal "+mAuxId[i]+" No Puede ser Mayor a la Fecha del Vencimiento.\n";
				   				}
				   		 	}
			   		 	}

			   			if (nSwich == 0)	{
			   				var vencimiento = (document.forms['frgrm']['cDrlVen'+mAuxId[i]].value == "0000-00-00" || document.forms['frgrm']['cDrlVen'+mAuxId[i]].value == "") ? "0000-00-00" : document.forms['frgrm']['cDrlVen'+mAuxId[i]].value;
				   		 	cadeni1 += document.forms['frgrm']['cDrlFec'+mAuxId[i]].value + "," + vencimiento + "~";
			   			}			   		 	
		   			}
		   		}
        }	        
	   		if (nSwich == 0)	{
		   		var cRuta = "frterdrl.php?cTerId=<?php echo $cTerId ?>&cCadena1="+cadeni+"&cCadena2="+cadeni1+"&cCliDrl=<?php echo $cCliDrl ?>&tipsave=5";
		   		var Msj  = f_makeRequest(cRuta);
	   		}	else	{
	   			alert(cMsj + "Verifique.");
	   		}
			}

	    //Funcion para cargar y validar los documentos de requisitos legales de un cliente
      function f_makeRequest(xRuta){
        http_request = false;
        if (window.XMLHttpRequest) { // Mozilla, Safari,...
          http_request = new XMLHttpRequest();
          if (http_request.overrideMimeType) {
            http_request.overrideMimeType('text/xml');
            // Ver nota sobre esta linea al final
          }
        }else if (window.ActiveXObject) { // IE
          try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
          } catch (e) {
            try {
              http_request = new ActiveXObject("Microsoft.XMLHTTP");
            }  catch (e) {}
          }
        }        
        if (!http_request) {
          alert('Falla :( No es posible crear una instancia XMLHTTP');
          return false;
        }
        
        http_request.onreadystatechange = f_alertContents;
        http_request.open('GET', xRuta, true);
        http_request.send(null);
      }
      
      function f_alertContents() {
        if(http_request.readyState==1){          
        }else if(http_request.readyState == 4) {
          if (http_request.status == 200) {
            if(http_request.responseText!=""){
              var cRetorno = http_request.responseText.replace(/^\s+|\s+$/g,"");
              var mRetorno = cRetorno.split("|");
              if (mRetorno[0] == "true") {
            	  window.opener.document.forms['frgrm']['cCliDrl'].value = mRetorno[1];
            	  window.opener.f_CargarRequisitos();                
            	  window.close();
              } else {
            	  alert(mRetorno[1]);
              }
            }else{
            	//No Hace Nada
            }
          } else {
            alert('Hubo problemas con la peticion.');
          }
        }
      }

	   	function f_Co(fld){
	   		var cade = document.forms['frcotoplo']['cCadena'].value;
	
	   		if (fld.checked == true)	{
	   			if (cade.indexOf(fld.name) < 0) {
	   				cade = cade + fld.name+'~';
	   				document.forms['frcotoplo']['cCadena'].value = cade;
	   			}
	   		}
	   		
	   		if (fld.checked == false)	{
	   			var cadeAux01 = cade.split("~");
	   			var cadeAux   = "";	   			
	   			for (var i=0; i< cadeAux01.length; i++) {
	   				cadeAux02  = cadeAux01[i].split(',');
	   				if (cadeAux02[0] != "") {
		   				if (cadeAux02[0] != fld.name) {
		   					cadeAux = cadeAux + cadeAux01[i]+'~';
		   				} else {
		   		      document.forms['frgrm']['cDrlFec' + fld.name].value = "";
		   		      document.forms['frgrm']['cDrlVen' + fld.name].value = "";
		   				}
	   				}
	   			}
	   			cade = cadeAux;
	   		}
	   		
		   	document.forms['frcotoplo']['cCadena'].value = cade;
			}
	   	</script>
	  </head>
	  <body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
			<form name = 'frcotoplo' action = '' method = 'post' target = 'fmpro'>
				<input type = 'hidden' name = 'cCadena' value = ''>
			</form>

	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="480">
				<tr>
					<td>
						<fieldset>
			   			<legend>Documentos Requisitos Legales</legend>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  						$qTerDrl  = "SELECT * ";
								$qTerDrl .= "FROM $cAlfa.fpar0110 ";
								$qTerDrl .= "WHERE regestxx = \"ACTIVO\" ORDER BY drlidxxx ";
								$xTerDrl = f_MySql("SELECT","",$qTerDrl,$xConexion01,"");
								if (mysql_num_rows($xTerDrl) > 0) {
									?>
									<center>
					    			<table cellspacing = "0" cellpadding = "1" border = "1" width = "580">
											<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
												<td widht = "080" Class = "name"><center>ID</center></td>
												<td widht = "260" Class = "name"><center>Descripci&oacute;n</center></td>
												<td widht = "120" Class = "name"><center>Fecha</center></td>
												<td widht = "120" Class = "name"><center>Vencimiento</center></td>
											</tr>
											<?php
											$y = 0;
											$nCon = 0;
											while ($zRDrl = mysql_fetch_array($xTerDrl)) {
												$serv = $zRDrl['drlidxxx'];
							          $vb   = $serv;
							          $cvb  = substr_count($cadena,$vb);
							          $y ++;
							          $zColor = "{$vSysStr['system_row_impar_color_ini']}";
								        if($y % 2 == 0) {
								          $zColor = "{$vSysStr['system_row_par_color_ini']}";
											   }
												?>
										   	<tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
													<td Class = 'letra7'>
														<input type = 'checkbox' style = 'width:20' name = '<?php echo $serv ?>' onClick ='javascript:f_Co(this);'<?php echo ($mReqLeg[$serv]['fechaxxx'] != "") ? "checked" : "" ?>><?php echo $zRDrl['drlidxxx'] ?>
														<?php if ($mReqLeg[$serv]['fechaxxx'] != "") { ?>
															<script languaje = 'javascript'>
																document.forms['frcotoplo']['cCadena'].value = document.forms['frcotoplo']['cCadena'].value +'<?php echo $serv ?>'+'~';
		  												</script>	
	  												<?php } ?>
													</td>
													<td Class = 'letra7'><?php echo utf8_encode($zRDrl['drldesxx']) ?></td>
													<td Class = 'letra7'>
														<input type = "text" name = 'cDrlFec<?php echo $serv ?>' value= '<?php echo ($mReqLeg[$serv]['fechaxxx'] != "" && $mReqLeg[$serv]['fechaxxx'] != "0000-00-00") ? $mReqLeg[$serv]['fechaxxx'] : "" ?>' style = 'text-align:center;width:80' onblur = "javascript:f_Date(this);">
                            <img src = '<?php echo $cPlesk_Skin_Directory ?>/b_calendar.png' style = 'cursor:hand' width = '18' onclick='javascript:show_calendar("frgrm.cDrlFec<?php echo $serv ?>");' >
													</td>
													<td Class = 'letra7'>
														<input type = "text" name = 'cDrlVen<?php echo $serv ?>' value= '<?php echo ($mReqLeg[$serv]['vencimie'] != "" && $mReqLeg[$serv]['vencimie'] != "0000-00-00") ? $mReqLeg[$serv]['vencimie'] : "" ?>' style = 'text-align:center;width:80' onblur = "javascript:f_Date(this);">
                            <img src = '<?php echo $cPlesk_Skin_Directory ?>/b_calendar.png' style = 'cursor:hand' width = '18' onclick='javascript:show_calendar("frgrm.cDrlVen<?php echo $serv ?>");' >
													</td>													
												</tr>
												<?php
							        }
							        ?>
										</table>
									</center>
									<center>
		      		    	<table border="0" cellpadding="0" cellspacing="0" width="580">
											<tr height="21">
												<td width="398" height="21"></td>
								 	      <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:hand" onClick = 'javascript:f_Guardar()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Guardar</td>
								 	      <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:window.close()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
											</tr>
										</table>
									</center>
									<?php
								}	else {
				 					f_Mensaje(__FILE__,__LINE__,"No Se Encontraron Registros");
				 				}
	  						?>
	  					</form>
	  				</fieldset>
	  			</td>
	  		</tr>
	  	</table>
	  </center>
	</body>
</html>
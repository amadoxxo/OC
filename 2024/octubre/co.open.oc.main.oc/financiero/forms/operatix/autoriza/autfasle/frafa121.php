<?php
  namespace openComex;
	include("../../../../libs/php/utility.php");

	if ($gModo != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title>Param&eacute;trica de Cupos</title>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script languaje = 'javascript'>
				function f_Carga_Matriz(xValue,xChecked) {
	
					var mDoSel = document.forms['frgrm']['vMatriz'].value.split("|");
					if (xChecked == true) {
		   			var nEncontro = 0;
		   			for (y=0;y<mDoSel.length;y++) {
			   			if (mDoSel[y] == xValue) {
			   				nEncontro = 1;
			   			}
		   			}
		   			if (nEncontro == 0) {
		   				document.forms['frgrm']['vMatriz'].value += xValue + "|";
		   			}		   			
		   		} else {
		   			var cDoSel = "";
		   			for (y=0;y<mDoSel.length;y++) {
			   			if (mDoSel[y] != xValue) {
			   				cDoSel += mDoSel[y] + "|";
			   			}
		   			}
		   			document.forms['frgrm']['vMatriz'].value = cDoSel;
		   		}
		   	}
				function fnMarcarRegistro(xItem,xRows,xCheck) {
				  f_Carga_Matriz(xCheck.value,xCheck.checked);
				  if (document.forms['frgrm']['cDocDosRe'+xItem].value != "") {
				  	var mRegistros = document.forms['frgrm']['cDocDosRe'+xItem].value.split("~");
				  }
				  
				  switch (xRows) {
		       	case "1":
			       	//Un solo registro
			       	if (document.forms['frgrm']['cDocAsoIm'+xItem].value == "SI") {
				       	//No se deja marcar
			       		if (document.forms['frgrm']['oChkTra'].checked==true) {
			       			document.forms['frgrm']['oChkTra'].checked=false;
			       		} else {
			       			document.forms['frgrm']['oChkTra'].checked=true;
			       		}
			       	}
			      break;
			      default:
			    	  var xSel = false;
			    	  for (i=0;i<document.forms['frgrm']['oChkTra'].length;i++){
			    		  if (xItem == i) {
				    		  //Es en el check que dio click
				    		  if (document.forms['frgrm']['cDocAsoIm'+xItem].value == "SI") {
					    		  //Es un DO de registro y no puede cambiar el valor
					    		  if (document.forms['frgrm']['oChkTra'][i].checked == true){
					    			  document.forms['frgrm']['oChkTra'][i].checked = false;
					    		  } else {
					    			  document.forms['frgrm']['oChkTra'][i].checked = true;
					    		  }
				    		  }
			    		  }
								//Marcando o desmarcando DO de registros asociados
			    		  if (document.forms['frgrm']['cDocDosRe'+xItem].value != "") {
			    			  var mDatos = document.forms['frgrm']['oChkTra'][i].id.split("~");
			    			  for (j=0; j<mRegistros.length; j++) {
				    			  if (mRegistros[j] != "") {
					    			  if (mDatos[0] == mRegistros[j]) {
					    				  document.forms['frgrm']['oChkTra'][i].checked = xCheck.checked;
					    				  //Se cargan los DO de registro
					    				  f_Carga_Matriz(document.forms['frgrm']['oChkTra'][i].value,document.forms['frgrm']['oChkTra'][i].checked);
					    			  }
				    			  }
			    			  }
			    		  }			    		  
					    }
				    break;
				  }
			  }
				function f_Carga_Grilla(xRows) {
					var nSwitch = 0;
					document.forms['frestado']['cTramites'].value = "";
					switch (xRows) {
					  case "1":
					    if (document.forms['frgrm']['oChkTra'].checked == true) {
					    	document.forms['frestado']['cTramites'].value += document.forms['frgrm']['oChkTra'].id+"|";
					    }
					  break;
					  default:
					  	if (parent.window.opener.document.forms['frgrm']['nSecuencia_Dos'].value == "<?php echo $gSecuencia ?>") { // Estoy en la ultima fila.
					      var mCheckOn = document.forms['frgrm']['vMatriz'].value.split("|");
								var nSwPrv = 0; // Switch de Primera Vez
					  		for (i=0;i<mCheckOn.length;i++) {
					  			if (mCheckOn[i] != "") {
					  				document.forms['frestado']['cTramites'].value += document.forms['frgrm']['oChkTra'][mCheckOn[i]].id+"|";
					  			}
					  		}
					  	} else {
					  		nSwitch = 1;
								alert("Solo se Puede Ingresar Multiples Registros si esta Ubicado en la Ultima Posicion de los Items, Verifique.");
							}
						break;
					}
					
					if (nSwitch == 0) {
						document.forms['frestado'].target = "framepro";
						document.forms['frestado'].submit();
					}
				}
				
				function f_Marca() {//Marca y Desmarca los registros seleccionados en la tabla de Conceptos de Cobro
				console.log(document.forms['frgrm']['nRecords'].value);
		  	if (document.forms['frgrm']['nCheckAll'].checked == true){
		    	if (document.forms['frgrm']['nRecords'].value == 1){
		     		document.forms['frgrm']['oChkTra'].checked=true;
		      } else {
			      	if (document.forms['frgrm']['nRecords'].value > 1){
					    	for (i=0;i<document.forms['frgrm']['oChkTra'].length;i++){
		   	   	    	document.forms['frgrm']['oChkTra'][i].checked = true;
					      }
					    }
		      }
		     } else {
			     	if (document.forms['frgrm']['nRecords'].value == 1){
		      		document.forms['frgrm']['oChkTra'].checked=false;
		      	} else {
		      	  	if (document.forms['frgrm']['nRecords'].value > 1){
						    	for (i=0;i<document.forms['frgrm']['oChkTra'].length;i++){
						      	document.forms['frgrm']['oChkTra'][i].checked = false;
						      }
		      	  	}
		 	  	   }
			    }
			}
			
			function f_Carga_Data() {//Arma cadena para guardar en campo matriz de la sys00121
					document.forms['frgrm']['vMatriz'].value="|";
		  	  switch (document.forms['frgrm']['nRecords'].value) {
	  			  case "1":
	  				  if (document.forms['frgrm']['oChkTra'].checked == true) {
	  					  document.forms['frgrm']['vMatriz'].value += document.forms['frgrm']['oChkTra'].value+"|";
	   					}
	  				break;
	  				default:
	  					var zSw_Prv = 0;
	  					for (i=0;i<document.forms['frgrm']['oChkTra'].length;i++) {
	  						if (document.frgrm.oChkTra[i].checked == true) {
	  							if ( document.forms['frgrm']['vMatriz'].value == "" ) {
			  						document.forms['frgrm']['vMatriz'].value="|";
			  					}
	  							document.frgrm.vMatriz.value += document.frgrm.oChkTra[i].value+"|";
	  						}
	  					}
	  				break;
	  			}
		  	}
			</script>
	  </head>
	  <body topmargin = 0 leftmargin = 0 marginwidth = 0 marginheight = 0 style = 'margin-right:0'>

	  <center>
			<table border ="0" cellpadding="0" cellspacing="0" width="500">
				<tr>
					<td>
						<fieldset>
			   			<legend>Param&eacute;trica de C</legend>
			   			<form name = "frestado" action = "frfac20g.php" method = "post" target = "framepro">
			   				<input type = "hidden" name = "nSecuencia" value = "<?php echo $gSecuencia ?>">
	  						<input type = "hidden" name = "cModo"      value = "<?php echo $gModo ?>">
	  						<textarea name = "cTramites" id = "cTramites"></textarea>
	  						<script languaje = "javascript">
	  							document.getElementById("cTramites").style.display="none";
	  						</script>
				   		</form>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<textarea name = "vMatriz" id = "vMatriz"></textarea>
	  						<input type = "hidden" name = "nRecords"   value = "0">
	  						<script languaje = "javascript">
	  							document.getElementById("vMatriz").style.display="none";
	  						</script>
	  						<?php
	  							switch ($gModo) {
	  								case "WINDOW":
	  								
											## formularios NO LEGALIZADOS ##
	  											$zSqlCab  = "SELECT ";
	  											$zSqlCab  = "SELECT $cAlfa.sys00121.*, ";
													$zSqlCab .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) AS clinomxx ";
												  $zSqlCab .= "FROM $cAlfa.sys00121 ";
													$zSqlCab .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
													$zSqlCab .= "WHERE ";
													$zSqlCab .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" AND ";
													$zSqlCab .= "$cAlfa.sys00121.docidxxx LIKE \"%$cDocId%\" AND ";
													$zSqlCab .= "($cAlfa.sys00121.docafasl = \"NO\"  OR $cAlfa.sys00121.docafasl = \"\") " ;
													$zSqlCab .= "ORDER BY CONVERT($cAlfa.sys00121.docidxxx,signed) ASC ";
													$zSqlCab .= "LIMIT 0,250 ";
													//f_Mensaje(__FILE__,__LINE__,$zSqlCab);
													$xSqlCab = f_MySql("SELECT","",$zSqlCab,$xConexion01,"");
													
											## FIN formularios NO LEGALIZADOS ##
	  													
											
	  									if ($xSqlCab && mysql_num_rows($xSqlCab) > 0) { ?>
	  										<center>
					    						<table cellspacing = "0" cellpadding = "1" border = "1" width = "450">
														<tr>
														<td width = "050" Class = "name"><center>SUC</center></td>
														<td width = "100" Class = "name"><center>DO</center></td>
														<td width = "050" Class = "name"><center>SUF</center></td>
														<td width = "050" Class = "name"><center>OPERACION</center></td>
														<td widht = "020" Class = "name" style = "text-align:right"><input type="checkbox" name="nCheckAll" onClick = "javascript:f_Marca();f_Carga_Data();"></td>
														</tr>
														<?php 
														$nPaintedRows =0;
														$nRows=0;
														while ($mSqlCab = mysql_fetch_array($xSqlCab)) {
															
															//if (mysql_num_rows($xSqlCab) > 1) { 
															$nPaintedRows++;
															?>
																<tr>
																	<td width = "050" class= "name"> <?php echo $mSqlCab['sucidxxx'] ?></td>																	
																	<td width = "100" class= "name"><?php echo $mSqlCab['docidxxx'] ?></td>
																	<td width = "050" class= "name"> <?php echo $mSqlCab['docsufxx'] ?></td>
																	<td width = "050" class= "name"> <?php echo $mSqlCab['doctipxx'] ?></td>
																	<td width = "020" class= "name" style = "text-align:right">
																		<input type="hidden" name = "cDocDosRe<?php echo $nRows ?>" value = "<?php echo $mSqlCab['docdosre'] ?>">
																		<input type="hidden" name = "cDocAsoIm<?php echo $nRows ?>" value = "<?php echo $mSqlCab['docidxxx'] ?>">
																		<input type="checkbox" name="oChkTra" value = "<?php echo $nRows ?>"
		  															id = "<?php echo $mSqlCab['sucidxxx']."~".$mSqlCab['docidxxx']."~".$mSqlCab['docsufxx']."~".$mSqlCab['doctipxx']."~".$mSqlCab['cliidxxx']."~".f_Digito_Verificacion($mSqlCab['cliidxxx'])."~".$mSqlCab['clinomxx'] ?>"
																			onclick = "javascript:fnMarcarRegistro('<?php echo $nRows ?>','<?php echo mysql_num_rows($xSqlCab) ?>',this);">
																	</td>
																</tr>
															<?php $nRows++;
														} ?>
														<tr>
															<td colspan="10">
																<center>
																	<input type="button" name="Btn_Aceptar" value = "Aceptar" style="width:50;text-align:center"
																		onclick="javascript:f_Carga_Grilla('<?php echo $nPaintedRows ?>');" readonly>
																	<input type="button" name="Btn_Salir"   value = "Salir"   style="width:50;text-align:center"
																		onclick="javascript:parent.window.close()" readonly>
																</center>
															</td>
														</tr>
													</table>
													<script languaje = "javascript">
													document.forms['frgrm']['nRecords'].value = '<?php echo $nRows-1?>'
													</script>
												</center>
	  									<?php	} else {
	  										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros");?>
	  								<?php	}
	  								break;

	  								case "VALID":
	  									
											if($cDocId != '') {
											## formularios NO LEGALIZADOS ##
	  											$zSqlCab  = "SELECT ";
													$zSqlCab .= "$cAlfa.sys00121.*, ";
													$zSqlCab .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) AS clinomxx ";
												  $zSqlCab .= "FROM $cAlfa.sys00121 ";
													$zSqlCab .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
													$zSqlCab .= "WHERE ";
													$zSqlCab .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" AND ";
													$zSqlCab .= "$cAlfa.sys00121.docidxxx = \"$cDocId\" AND ";
													$zSqlCab .= "($cAlfa.sys00121.docafasl = \"NO\"  OR $cAlfa.sys00121.docafasl = \"\" )" ;
													$zSqlCab .= "ORDER BY CONVERT($cAlfa.sys00121.docidxxx,signed) ASC ";
													$xSqlCab = f_MySql("SELECT","",$zSqlCab,$xConexion01,"");
													//f_Mensaje(__FILE__,__LINE__,$zSqlCab);	
											## FIN formularios NO LEGALIZADOS ##
	  									if ($xSqlCab && mysql_num_rows($xSqlCab) == 1) {
	  										while ($mSqlCab = mysql_fetch_array($xSqlCab)) { 
	  												$cDatTra  = $mSqlCab['sucidxxx']."~";
														$cDatTra  .= $mSqlCab['docidxxx']."~";
														$cDatTra  .= $mSqlCab['docsufxx']."~";
														$cDatTra  .=  $mSqlCab['doctipxx']."~";
														$cDatTra  .= $mSqlCab['cliidxxx']."~";
														$cDatTra  .= f_Digito_Verificacion($mSqlCab['cliidxxx'])."~";
														$cDatTra  .= $mSqlCab['clinomxx'];
	  	  									?>
	  	  									
	  	  									<script language="javascript">
			    											document.forms['frestado']['cTramites'].value += "<?php echo $cDatTra."|" ?>";
			        						</script>
	  	  									<script languaje = "javascript">
														document.forms['frestado'].target = "fmpro2";
														document.forms['frestado'].submit();
	  	  									</script>
  	  									
	      	      				<?php break;
	  										}
	  									} else { ?>
												<script language = "javascript">
													parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW","<?php echo $gSecuencia ?>");
													window.close();
												</script>
	  									<?php }
										} else { ?>
											<script language = "javascript">
												parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW","<?php echo $gSecuencia ?>");
												window.close();
										</script>
  									<?php }
	  									
	  								break;
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
<?php } else {
	f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos");
} 
?>
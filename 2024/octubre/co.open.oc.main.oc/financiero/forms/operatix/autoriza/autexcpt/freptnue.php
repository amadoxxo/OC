<?php
  namespace openComex;
/**
	 * Proceso Autorizacion Excluir Conceptos de Pagos a Terceros
	 * --- Descripcion: Permite Crear un Nueva autorizacion para Excluir conceptos de Pagos a Terceros para Facturacion.
	 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
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
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		<script language="javascript">
  		function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
  			document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
  	  }

			function f_Marca() {//Marca y Desmarca los registros seleccionados en la tabla de Conceptos de Cobro
		  	if (document.forms['frgrm']['nCheckAll'].checked == true){
		    	if (document.forms['frgrm']['nRecords'].value == 1){
		     		document.forms['frgrm']['cCheck'].checked=true;
		      } else {
			      	if (document.forms['frgrm']['nRecords'].value > 1){
					    	for (i=0;i<document.forms['frgrm']['cCheck'].length;i++){
		   	   	    	document.forms['frgrm']['cCheck'][i].checked = true;
					      }
					    }
		      }
		     } else {
			     	if (document.forms['frgrm']['nRecords'].value == 1){
		      		document.forms['frgrm']['cCheck'].checked=false;
		      	} else {
		      	  	if (document.forms['frgrm']['nRecords'].value > 1){
						    	for (i=0;i<document.forms['frgrm']['cCheck'].length;i++){
						      	document.forms['frgrm']['cCheck'][i].checked = false;
						      }
		      	  	}
		 	  	   }
			    }
			}

			function f_Valida(){//Valida datos de formulario, para poder pintar conceptos de Cobro a excluir
				if(document.forms['frgrm']['cDocId'].value == ''  ||
					 document.forms['frgrm']['cDocSuf'].value == '' ||
					 document.forms['frgrm']['cSucId'].value == ''  ||
					 document.forms['frgrm']['cCliId'].value == '' ){
					alert("Debe seleccionar Do");
				}else{
					document.forms['frgrm'].target='fmwork';
          document.forms['frgrm'].action='freptnue.php';
					document.forms['frgrm']['cStep'].value = '2';
          document.forms['frgrm'].submit();
		  	}
			}  

			function f_Carga_Data() { //Arma cadena para guardar en campo matriz de la sys00121
        document.forms['frgrm']['cComMemo'].value="|";
        switch (document.forms['frgrm']['nRecords'].value) {
          case "1":
            if (document.forms['frgrm']['cCheck'].checked == true) {
              if ("<?php echo $vSysStr['system_habilitar_liquidacion_do_facturacion'] ?>" == "SI") {
                document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'].id+"~"+document.forms['frgrm']['cEnviarA1'].value+"|";
              } else {
                document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'].id+"|";
              }
            }
          break;
          default:
            if (document.forms['frgrm']['nRecords'].value > 1) {
              for (i=0;i<document.forms['frgrm']['cCheck'].length;i++) {
                if (document.forms['frgrm']['cCheck'][i].checked == true) {
                  if ("<?php echo $vSysStr['system_habilitar_liquidacion_do_facturacion'] ?>" == "SI") {
                    document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'][i].id+"~"+document.forms['frgrm']['cEnviarA'+ (i+1)].value+"|";
                  } else {
                    document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'][i].id+"|";
                  }
                }
              }
            }
          break;
        }
        if (document.forms['frgrm']['cComMemo'].value == "|"){
          document.forms['frgrm']['cComMemo'].value = "";
        }
      }

		  function f_Carga_Pagos(xSucId, xDocId, xDocSuf) {
		    var cRuta  = "freptpta.php?"+"gSucId="+xSucId+
		  							 "&gDocId="+xDocId+
		  							 "&gDocSuf="+xDocSuf;

	 		  parent.fmpro.location = cRuta;
		  }
		  
	  	function f_Marcar_Iguales(xId,xValor) {
	  		switch (document.forms['frgrm']['nRecords'].value) {
  			  case "1":
  				  //No hace nada porque existe solo uno
  				break;
  				default:
  					for (i=0;i<document.forms['frgrm']['cCheck'].length;i++) {
  						if (document.frgrm.cCheck[i].id == xId) {
  							document.frgrm.cCheck[i].checked = xValor;
  						}
  					}
  				break;
  			}
	  	}	
	  </script>
	</head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="800">
				<tr>
					<td>
				  	<fieldset>
					  	<legend><?php echo $_COOKIE['kModo']." ".$_COOKIE['kProDes'] ?></legend>
						 	<form name = 'frgrm' action = 'freptgra.php' method = 'post' target='fmpro'>
						 		<input type = "hidden" name = "cStep"      value = "">
						 		<input type = "hidden" name = "nRecords"   value = "">
						 		<input type = "hidden" name = "nTimesSave" value = "0">
						 		<textarea name = "cComMemo"  id = "cComMemo"></textarea>
                <script languaje = "javascript">
                  document.getElementById("cComMemo").style.display ="none";
                </script>
							 	<center>
							 	<fieldset>
                	<legend>Datos Do</legend>
									<table border = '0' cellpadding = '0' cellspacing = '0' width='800'>
							 			<?php $nCol = f_Format_Cols(40);
  							 		echo $nCol;?>
							    	<tr>
											<td Class = "name" colspan = "03">Suc<br>
										 		<input type = "text" Class = "letra" name = "cSucId" style = "width:60;text-align:center" readonly>
											</td>
								    	<td Class = "name" colspan = "06">Do<br>
												<input type = "text" Class = "letra" style = "width:120;text-align:left" name = "cDocId" 
													onBlur = "javascript:this.value=this.value.toUpperCase();
																					         f_Links('cDocId','VALID');
																					         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
													onFocus="javascript:document.forms['frgrm']['cDocId'].value  ='';
												 										  document.forms['frgrm']['cSucId'].value  = '';
												 										  document.forms['frgrm']['cDocSuf'].value  = '';
												 										  document.forms['frgrm']['cDocTip'].value  = '';
												 										  document.forms['frgrm']['cCliId'].value  = '';
			            						  						  document.forms['frgrm']['cCliNom'].value = '';
			            						  						  document.getElementById('tblPagTer').innerHTML='';
																              this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
										 	</td>
										 	<td Class = "name" colspan = "03">Sufijo<br>
										 		<input type = "text" Class = "letra" name = "cDocSuf" style = "width:60;text-align:center" readonly>
										 	</td>
										 	<td Class = "name" colspan = "05">Tipo Operacion<br>
										 		<input type = "text" Class = "letra" name = "cDocTip" style = "width:100;text-align:center" readonly>
										 	</td>
										 	<td Class = "name" colspan = "05">Nit<br>
										 		<input type = "text" Class = "letra" name = "cCliId" style = "width:100;text-align:center" readonly>
										 	</td>
										 	<td Class = "name" colspan = "01">Dv<br>
										 		<input type = "text" Class = "letra" name = "cCliDv" style = "width:020" readonly>
										 	</td>
										 	<td Class = "name" colspan = "17">Cliente<br>
										 		<input type = "text" Class = "letra" name = "cCliNom" style = "width:340" readonly>
										 	</td>
										</tr>
									</table>
								</fieldset>
								
								<fieldset>
									<legend>Conceptos de Pagos a Terceros a Excluir</legend>
									<center>
										<table border ="0" cellpadding="0" cellspacing="0" width="800">
											<tr>
												<td id="tblPagTer"></td>
											</tr>
										</table>
									</center>
								</fieldset>
								</center>
			 	      </form>
						</fieldset>
					</td>
				</tr>
		 	</table>
		</center>
		<center>
		<?php switch ($_COOKIE['kModo']) {
			case "EDITAR": ?>
				<table border="0" cellpadding="0" cellspacing="0" id='bnt_Paso2' width="800">
					<tr>
						<td width="618" height="21"></td>
			  		<td width="91" height="21" Class="name">
							<input type="button" name="Btn_Guardar" value="Guardar" Class = "name" style = "background:url(<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif);width:91;height:21;border:0px;"
								onclick = "javascript:f_Carga_Data();
								                      document.forms['frgrm'].target='fmpro';
	      	                        		document.forms['frgrm'].action='freptgra.php';
																			document.forms['frgrm']['nTimesSave'].value++;
																			document.forms['frgrm'].submit();"></td>
						<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:hand"
							onClick ="javascript:f_Retorna();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
						</td>
					</tr>
				</table>
			<?php break;
			default: ?>
				<table border="0" cellpadding="0" cellspacing="0" id='bnt_Paso2' width="800">
					<tr>
						<td width="709" height="21"></td>
						<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
					</tr>
				</table>
			<?php break;
		} ?>
		</center>
		<!-- Termine de Pintar el Formulario y lo Mando a la Funcion wModo() con el Modo que Vengo -->
		<?php
		switch ($_COOKIE['kModo']) {
			case "EDITAR":
				f_CargaData($gSucId,$gDocId,$gDocSuf); 
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cSucId'].readOnly  = true;
					document.forms['frgrm']['cDocId'].readOnly  = true;
					document.forms['frgrm']['cDocSuf'].readOnly = true;
					document.forms['frgrm']['cDocId'].onblur    = "";
					document.forms['frgrm']['cDocId'].onfocus   = "";
				</script>
			<?php break;
			case "VER":
				f_CargaData($gSucId,$gDocId,$gDocSuf); 
				?>
				<script languaje = "javascript">
					document.forms['frgrm']['cSucId'].readOnly  = true;
					document.forms['frgrm']['cDocId'].readOnly  = true;
					document.forms['frgrm']['cDocSuf'].readOnly = true;
					document.forms['frgrm']['cDocId'].onblur = "";
					document.forms['frgrm']['cDocId'].onfocus = "";
					for (x=0;x<document.forms['frgrm'].elements.length;x++) {
            document.forms['frgrm'].elements[x].readOnly = true;
            document.forms['frgrm'].elements[x].onfocus  = "";
            document.forms['frgrm'].elements[x].onblur   = "";
            document.forms['frgrm'].elements[x].disabled = true;
          }
				</script>
			<?php break;
		} ?>

		<?php
		function f_CargaData($gSucId,$gDocId,$gDocSuf) {
  	  global $xConexion01; global $cAlfa;
    	## Traigo Datos Proyecto por Cliente ##
			$qTarifas  = "SELECT $cAlfa.sys00121.*, ";
			$qTarifas .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS clinomxx ";
			$qTarifas .= "FROM $cAlfa.sys00121 ";
			$qTarifas .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
			$qTarifas .= "WHERE ";
			$qTarifas .= "$cAlfa.sys00121.docidxxx = \"$gDocId\" AND ";
			$qTarifas .= "$cAlfa.sys00121.docsufxx = \"$gDocSuf\" AND ";
			$qTarifas .= "$cAlfa.sys00121.sucidxxx = \"$gSucId\" LIMIT 0,1 ";
			// f_Mensaje(__FILE__,__LINE__,$qTarifas);
			$xTarifas  = f_MySql("SELECT","",$qTarifas,$xConexion01,"");
			$vTarifas = mysql_fetch_array($xTarifas);
			
			##Cargo Matriz con Valores de Conceptos Excluidos para Facturacion ##
			$vTarExc = f_Explode_Array($vTarifas['doctexpt'],"|","~");
		
			##Fin Cargo Matriz con Valores de Conceptos Excluidos para Facturacion ##
			?>
			<script language = "javascript">
				document.forms['frgrm']['cSucId'].value    = "<?php echo $vTarifas['sucidxxx'] ?>";
				document.forms['frgrm']['cDocId'].value    = "<?php echo $vTarifas['docidxxx'] ?>";
				document.forms['frgrm']['cDocSuf'].value   = "<?php echo $vTarifas['docsufxx'] ?>";
				document.forms['frgrm']['cDocTip'].value   = "<?php echo $vTarifas['doctipxx'] ?>";
				document.forms['frgrm']['cCliId'].value    = "<?php echo $vTarifas['cliidxxx'] ?>";
				document.forms['frgrm']['cCliNom'].value   = "<?php echo $vTarifas['clinomxx'] ?>";
				document.forms['frgrm']['cComMemo'].value  = "<?php echo $vTarifas['doctexpt'] ?>";
				
				f_Carga_Pagos("<?php echo $vTarifas['sucidxxx'] ?>", "<?php echo $vTarifas['docidxxx'] ?>", "<?php echo $vTarifas['docsufxx'] ?>");
			</script>
		 <?php }

		?>
	</body>
</html>

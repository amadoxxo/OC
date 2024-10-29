<?php
  namespace openComex;
/**
	 * Listado Conceptos Contables
	 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
	 * @package openComex
	 */
	include("../../../../libs/php/utility.php");
	?>
<html>
  <title>Conceptos Contables Causaciones Automaticas</title>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
		</script>
		<script language="javascript">
		
			function 	fnBuscarCategoriaConcepto(xCacId,xCacDes){
        	
      	if(xCacId){
      		document.forms['frcac']['gCacId'].value = xCacId;
      		document.forms['frcac']['gCacDes'].value = xCacDes;
      	}else{
      		document.forms['frcac']['gCacId'].value = document.forms['frgrm']['cCacId'].value;	
      		document.forms['frcac']['gCacDes'].value = document.forms['frgrm']['cCacDes'].value;
      	}
    		document.forms['frcac']['gModo'].value = "WINDOW";
    		document.forms['frcac'].submit();
      	
      }
		
			function f_Links(xLink,xSwitch,xIteration) {
					var zX    = screen.width;
					var zY    = screen.height;
					switch (xLink){
						case "cCacId":
							if (xSwitch == "VALID") {
								var zRuta  = "frcac144.php?gWhat=VALID&gFunction=cCacId&gCacId="+document.frgrm.cCacId.value.toUpperCase()+"";
								parent.fmpro2.location = zRuta;
							} else{ 
								if(xSwitch == "WINDOW"){
				  				var zNx     = (zX-560)/2;
									var zNy     = (zY-300)/2;
									var zWinPro = 'width=560,scrollbars=1,height=300,left='+zNx+',top='+zNy;
									var zRuta   = "frcac144.php?gWhat=WINDOW&gFunction=cCacId&gCacId="+document.frgrm.cCacId.value.toUpperCase()+"";
									zWindow = window.open(zRuta,"zCategoriasWindow",zWinPro);
							  	zWindow.focus();
								}else{
		              if (xSwitch == "EXACT"){
		                var zRuta  = "frcac144.php?gWhat=EXACT&gFunction=cCacId&gCacId="+document.frgrm.cCacId.value.toUpperCase()+"";
		                parent.fmpro2.location = zRuta;
		              }
		            }
	            }
					  break;
					  case "cCacDes":
							if (xSwitch == "VALID") {
								var zRuta  = "frcac144.php?gWhat=VALID&gFunction=cCacDes&gCacDes="+document.frgrm.cCacDes.value.toUpperCase()+"";
								parent.fmpro2.location = zRuta;
							} else{ 
								if(xSwitch == "WINDOW"){
				  				var zNx     = (zX-560)/2;
									var zNy     = (zY-300)/2;
									var zWinPro = 'width=560,scrollbars=1,height=300,left='+zNx+',top='+zNy;
									var zRuta   = "frcac144.php?gWhat=WINDOW&gFunction=cCacDes&gCacDes="+document.frgrm.cCacDes.value.toUpperCase()+"";
									zWindow = window.open(zRuta,"zCategoriasWindow",zWinPro);
							  	zWindow.focus();
								}else{
		              if (xSwitch == "EXACT"){
		                var zRuta  = "frcac144.php?gWhat=EXACT&gFunction=cCacDes&gCacDes="+document.frgrm.cCacDes.value.toUpperCase()+"";
		                parent.fmpro2.location = zRuta;
		              }
		            }
	            }
					  break;
			    }
				}
			function f_Todas(){
				for (i=0;i<document.forms['frgrm']['nSecuencia'].value;i++) {
	  	    if (document.frgrm["chAll"].checked == true) {
	  	    	document.frgrm["ch"+i].checked = true;
	  	    } else {
	  	    	document.frgrm["ch"+i].checked = false;
	  	    }
				}
			}
			
  		function f_Aceptar(xSecuencia){
  	  	var nBan = 0;
  	  	var nConSel = 0;
  			var nSecuencia = xSecuencia;
  			
          for (i=0;i<document.forms['frgrm']['nSecuencia'].value;i++) {
	  		    if (document.frgrm["ch"+i].checked == true) {
	  		    	
	  		        var mMatriz = document.frgrm["ch"+i].id.split("~");
	  		        var nEncontro = 0;
		  		      var cSucId = "";
		  		      var cDocId = "";
		  		      var cDocSuf= "";
		  		      var nEncDo;
		  		      nConSel++;
		  		      
	  		        //Codigo para insertar solo una vez cada concepto
	  		        //Esto aplica cuando el tipo de prorrateo es PORCENTAJE 
	  		        //o cuando el tipo de prorrateo es VALOR pero solo hay seleccionado un DO
		  		      if(parent.window.opener.document.forms['frgrm']['cTipPro'].value == 'PORCENTAJE' || 
				  		    (parent.window.opener.document.forms['frgrm']['cTipPro'].value == 'VALOR' && 
						  		 parent.window.opener.document.forms['frgrm']['nSecuencia_DO'].value == 1)) {

			  		    	if(parent.window.opener.document.forms['frgrm']['cTipPro'].value == 'VALOR' && 
				  		      parent.window.opener.document.forms['frgrm']['nSecuencia_DO'].value == 1) {
                    cSucId  = parent.window.opener.document.forms['frgrm']['cSucId_DO1'].value;
                    cDocId  = parent.window.opener.document.forms['frgrm']['cDocId_DO1'].value;
                    cDocSuf = parent.window.opener.document.forms['frgrm']['cDocSuf_DO1'].value;
                  }

				  		    //Busco si ese concepto ya esta en la gilla
	                for (j=1;j<=parent.window.opener.document.forms['frgrm']['nSecuencia_CCO'].value;j++) {
	                  if(parent.window.opener.document.forms['frgrm']['cCcoId_CCO'+j].value == mMatriz[0]) {
	                    nEncontro = 1;                                                          
	                  }	                  
	                }

			  		    	if (nEncontro == 0) {
			            	if(nBan > 0) {
                		  nSecuencia++;
                    }
		                if (nSecuencia > parent.window.opener.document.forms['frgrm']['nSecuencia_CCO'].value) {
			            		parent.window.opener.f_Add_New_Row_Conceptos();
                    }                      
                	  parent.window.opener.document.forms['frgrm']['cCcoId_CCO'   +nSecuencia].id    = mMatriz[0];
                    parent.window.opener.document.forms['frgrm']['cCcoId_CCO'   +nSecuencia].value = mMatriz[0];        
                    parent.window.opener.document.forms['frgrm']['cCcoDes_CCO'  +nSecuencia].value = mMatriz[1];
                    parent.window.opener.document.forms['frgrm']['cSucId_CCO'   +nSecuencia].value = cSucId;
                    parent.window.opener.document.forms['frgrm']['cDocId_CCO'   +nSecuencia].value = cDocId;
                    parent.window.opener.document.forms['frgrm']['cDocSuf_CCO'  +nSecuencia].value = cDocSuf;
                    parent.window.opener.document.forms['frgrm']['nVlrBase_CCO' +nSecuencia].value = "";
                    parent.window.opener.document.forms['frgrm']['nVlrIva_CCO'  +nSecuencia].value = "";
                    parent.window.opener.document.forms['frgrm']['nVlr_CCO'     +nSecuencia].value = "";
                    parent.window.opener.document.forms['frgrm']['cCtoVrl02_CCO'+nSecuencia].value = document.forms['frgrm']['cCtoVrl02_CCO'+i].value;
                    nBan++;
                  }
	  		        }

		  		      //Codigo para insertar varias veces el mismo concepto segun el numero de DO que haya seleccionado
			  		    if(parent.window.opener.document.forms['frgrm']['cTipPro'].value == 'VALOR' && 
					  		   parent.window.opener.document.forms['frgrm']['nSecuencia_DO'].value > 1) {

				  		    //Busco si ese concepto ya esta en la gilla
	                var nPosIni = 0;
	                for (j=1;j<=parent.window.opener.document.forms['frgrm']['nSecuencia_CCO'].value;j++) {
	                  if(parent.window.opener.document.forms['frgrm']['cCcoId_CCO'+j].value == mMatriz[0]) {
	                    if (nEncontro == 0) {
	                      nPosIni = j;
	                    }
	                    nPosFin = j;
	                    nEncontro = 1;                                                          
	                  }
	                }
			  		    	
			  		    	//Se debe averiguar cuantas filas hay que agregar, esto depende de la cantidad de DO
				  		    //Si no encontro el concepto significa que se se deben agregar tantas filas como DO al final de la grilla
			  		    	if (nEncontro == 0) {
			  		    		for(var j=1; j<=parent.window.opener.document.forms['frgrm']['nSecuencia_DO'].value; j++) {
				  		    		if (parent.window.opener.document.forms['frgrm']['cSucId_DO' +j].value != '' &&
				  		    				parent.window.opener.document.forms['frgrm']['cDocId_DO' +j].value != '' &&
				  		    				parent.window.opener.document.forms['frgrm']['cDocSuf_DO'+j].value != '') {
				  		    			if(nBan > 0) {
	                        nSecuencia++;
	                      }
	                      if (nSecuencia > parent.window.opener.document.forms['frgrm']['nSecuencia_CCO'].value) {
	                        parent.window.opener.f_Add_New_Row_Conceptos();
	                      }
	                      parent.window.opener.document.forms['frgrm']['cCcoId_CCO'   +nSecuencia].id    = mMatriz[0];
	                      parent.window.opener.document.forms['frgrm']['cCcoId_CCO'   +nSecuencia].value = mMatriz[0];        
	                      parent.window.opener.document.forms['frgrm']['cCcoDes_CCO'  +nSecuencia].value = mMatriz[1];
	                      parent.window.opener.document.forms['frgrm']['cSucId_CCO'   +nSecuencia].value = parent.window.opener.document.forms['frgrm']['cSucId_DO' +j].value;
	                      parent.window.opener.document.forms['frgrm']['cDocId_CCO'   +nSecuencia].value = parent.window.opener.document.forms['frgrm']['cDocId_DO' +j].value;
	                      parent.window.opener.document.forms['frgrm']['cDocSuf_CCO'  +nSecuencia].value = parent.window.opener.document.forms['frgrm']['cDocSuf_DO'+j].value;
	                      parent.window.opener.document.forms['frgrm']['nVlrBase_CCO' +nSecuencia].value = "";
	                      parent.window.opener.document.forms['frgrm']['nVlrIva_CCO'  +nSecuencia].value = "";
	                      parent.window.opener.document.forms['frgrm']['nVlr_CCO'     +nSecuencia].value = "";
	                      parent.window.opener.document.forms['frgrm']['cCtoVrl02_CCO'+nSecuencia].value = document.forms['frgrm']['cCtoVrl02_CCO'+i].value;
	                      nBan++;
			  		    		  }                      
				  		    	}
			  		    	} else {
				  		    	//Se buscan los DO que hacen falta y se agregan a la grilla
				  		    	for(var j=1; j<=parent.window.opener.document.forms['frgrm']['nSecuencia_DO'].value; j++) {
				  		    		if (parent.window.opener.document.forms['frgrm']['cSucId_DO'+j].value != '' &&
				  		    				parent.window.opener.document.forms['frgrm']['cDocId_DO'+j].value != '' &&
				  		    				parent.window.opener.document.forms['frgrm']['cDocSuf_DO'+j].value != '') {
						  		     nEncDo = 0;
					  		    	 for (var nC=nPosIni; nC<=nPosFin; nC++) {
					  		    	   if(parent.window.opener.document.forms['frgrm']['cSucId_DO' +j].value == parent.window.opener.document.forms['frgrm']['cSucId_CCO' +nC].value &&
						  		    			parent.window.opener.document.forms['frgrm']['cDocId_DO' +j].value == parent.window.opener.document.forms['frgrm']['cDocId_CCO' +nC].value &&
							  		    		parent.window.opener.document.forms['frgrm']['cDocSuf_DO'+j].value == parent.window.opener.document.forms['frgrm']['cDocSuf_CCO'+nC].value) {
						  		    		 //Encontro DO
					  		    			 nEncDo = 1;
					  		    	   }			
					  		    	 }
	
					  		    	 //Si el DO No esta se agrega a la grilla
					  		    	if (nEncDo == 0) {
					  		    	  nSecuencia = parseInt(nPosFin) + 1;
					  	          parent.window.opener.f_Insert_Row("Grid_Conceptos",nSecuencia);
	
						  	        parent.window.opener.document.forms['frgrm']['cCcoId_CCO'   +nSecuencia].id    = mMatriz[0];
	                      parent.window.opener.document.forms['frgrm']['cCcoId_CCO'   +nSecuencia].value = mMatriz[0];        
	                      parent.window.opener.document.forms['frgrm']['cCcoDes_CCO'  +nSecuencia].value = mMatriz[1];
	                      parent.window.opener.document.forms['frgrm']['cSucId_CCO'   +nSecuencia].value = parent.window.opener.document.forms['frgrm']['cSucId_DO' +j].value;
	                      parent.window.opener.document.forms['frgrm']['cDocId_CCO'   +nSecuencia].value = parent.window.opener.document.forms['frgrm']['cDocId_DO' +j].value;
	                      parent.window.opener.document.forms['frgrm']['cDocSuf_CCO'  +nSecuencia].value = parent.window.opener.document.forms['frgrm']['cDocSuf_DO'+j].value;
	                      parent.window.opener.document.forms['frgrm']['nVlrBase_CCO' +nSecuencia].value = "";
	                      parent.window.opener.document.forms['frgrm']['nVlrIva_CCO'  +nSecuencia].value = "";
	                      parent.window.opener.document.forms['frgrm']['nVlr_CCO'     +nSecuencia].value = "";
	                      parent.window.opener.document.forms['frgrm']['cCtoVrl02_CCO'+nSecuencia].value = document.forms['frgrm']['cCtoVrl02_CCO'+i].value;
	                      nBan++;	
	                      nPosFin = nSecuencia;			  	          
					  		      }		
						  		  }		  		    		
			  		    	}				  		    	
			  		    }			  		    	 		         
              }   
		  		  }
	  		  }
		  		  
	  		  if(nConSel > 0){
		  			parent.window.opener.f_Asignar_Base_Conceptos();
	  			  parent.window.close();
	  		  } else {
	  	  		  alert('Debe Seleccionar un Concepto.');
	  		  }
			}
		
     	</script>
  </head>
	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
	<form name = "frcac" method = "post" action="frcpacco.php" target="fmwork">
    <input type = "hidden" name = "gComId"    	value = "<?php echo $gComId ?>">
    <input type = "hidden" name = "gComCod"   	value = "<?php echo $gComCod ?>">
    <input type = "hidden" name = "gCtoId"    	value = "<?php echo $gCtoId ?>">
    <input type = "hidden" name = "gSecuencia"  value = "<?php echo $gSecuencia ?>">
    <input type = "hidden" name = "gModo"  			value = "<?php echo $gModo?>">
    <input type = "hidden" name = "gFunction"  	value = "<?php echo $gFunction?>">
    <input type = "hidden" name = "gTerTipB"  	value = "<?php echo $gTerTipB ?>">
    <input type = "hidden" name = "gTerIdB"  		value = "<?php echo $gTerIdB ?>">
    <input type = "hidden" name = "gCacId"  		value = "<?php echo $gCacId ?>">
    <input type = "hidden" name = "gCacDes"  		value = "<?php echo $gCacDes ?>">
  </form>	
	<form name = "frgrm" action = "frcpados.php" method = "post" target="framework">
	<input type="hidden" name = "nSecuencia" value="0">
	 <center>
	    <?php 
				/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/ 
				if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
					<table border = "0" cellpadding = "0" cellspacing = "0" width = "700">
					<?
				}else{
					?>
					<table border = "0" cellpadding = "0" cellspacing = "0" width = "460">	
					<?php 
				}?>
        <tr>
          <td>
            <fieldset>
					  	<legend>Conceptos Contables Causaciones Automaticas</legend>
						 	  <center>
						 	  	<?php 
									/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/ 
									if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
										<fieldset>
	   									<legend>B&uacute;squeda por Categor&iacute;a</legend>
 											<table border = '0' cellpadding = '0' cellspacing = '0' width='600'>
												<tr>
													<td Class = "name" widht = "090">
	  												<a href = "javascript:document.frgrm.cCacId.value  = '';
	  																			  		  document.frgrm.cCacDes.value = '';
	  																							f_Links('cCacId','VALID')" id="IdCac">C&oacute;digo</a><br>
	  												<input type = "text" Class = "letra" style = "width:090" name = "cCacId" value= "<?php echo $gCacId ?>"
	  										    	onBlur = "javascript:this.value=this.value.toUpperCase();
	  																			         f_Links('cCacId','VALID');
	  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'"
	  										    	onFocus="javascript:document.frgrm.cCacId.value  ='';
	  	            						  									document.frgrm.cCacDes.value = '';
	  														                  this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
	  										  </td>
	  										  <td Class = "name" widht = "490">Descripci&oacute;n<br>
	  												 <input type = "text" Class = "letra" style = "width:490" name = "cCacDes" value = "<?php echo $gCacDes ?>"
	  												 onBlur = "javascript:this.value=this.value.toUpperCase();
	  																			         f_Links('cCacDes','VALID');
	  																			         this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'">
	  										  </td>
	  										  <td Class = "name" widht = "091" ><br>
		                        <input type="button" class="name" 
		                               style="width: 91; height: 21; border: 0; background: url(../../../../plesk/graphics/standar/btn_ok_bg.gif) no-repeat; cursor:hand"
		                               value= "Buscar" onClick = 'javascript:fnBuscarCategoriaConcepto()'/>
		                      </td>
	  									  </tr> 
  									  </table>
									  </fieldset>
									 	<br>
									 	<?php 
										}
										?>
										<?php 
										/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/ 
										if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
		    							<table cellspacing = "0" cellpadding = "1" border = "1" width = "680">
	    							<?php 
										}else{?>
											<table cellspacing = "0" cellpadding = "1" border = "1" width = "460"><?php 
										}?>
										<tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">
											<td Class = "name" width = "100"><center>CONCEPTO</center></td>
											<td Class = "name" width = "300"><center>DESCRIPCION</center></td>
											<?php
											 /*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/ 
											if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
											<td widht = "260" Class = "name"><center>CATEGORIA CONCEPTO</center></td>
											<?php }?>
											<td Class = "name" width = "020"><center><input type = 'checkbox' name = 'chAll' onClick="javascript:f_Todas()"></input></center></td>
										</tr>
										<?php
										    $qDatExt  = "SELECT CLICTOXX ";
	                      $qDatExt .= "FROM $cAlfa.SIAI0150 ";
	                      $qDatExt .= "WHERE ";
	                      $qDatExt .= "$gTerTipB = \"SI\" AND ";
	                      $qDatExt .= "CLIIDXXX = \"$gTerIdB\" AND ";
	                      $qDatExt .= "REGESTXX = \"ACTIVO\" ORDER BY CLIIDXXX LIMIT 0,1";
	                      $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	                      // f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
	                      $vDatExt = mysql_fetch_array($xDatExt); 
	                      
	                      $cCloCto = "";
	                      if($vDatExt['CLICTOXX'] <> '') {
	                       $mAux = explode("|",$vDatExt['CLICTOXX']);
	                       for($i=0; $i<count($mAux); $i++) {
	                         if ($mAux[$i] <> "") {
	                           $mAux01 = explode("~",$mAux[$i]);
	                           $cCloCto .= "ctoidxxx = \"{$mAux01[0]}\" OR ";
	                         }
	                       }
	                      }
	                      
	                      $cCloCto = substr($cCloCto,0,strlen($cCloCto)-4);
	                      
	                      if ($cCloCto <> "") {
											    $qCliCco  = "SELECT ctoidxxx, ctodesxx, ctovlr02,cacidxxx ";
											    $qCliCco .= "FROM $cAlfa.fpar0121 ";
											    $qCliCco .= "WHERE ";
											    $qCliCco .= "($cCloCto) AND ";
											    $qCliCco .= "ctocomxx LIKE \"%|$gComId~$gComCod%\" AND ";
											    $qCliCco .= "ctoidxxx LIKE \"%$gCcoId%\" AND ";
													if($gCacId != ""){
														$qCliCco .= "$cAlfa.fpar0121.cacidxxx = \"$gCacId\" AND ";
													}
											    $qCliCco .= "regestxx = \"ACTIVO\" ";
											    $qCliCco .= "ORDER BY ctoidxxx ";
	                        $xCliCco  = f_MySql("SELECT","",$qCliCco,$xConexion01,"");
	                        // f_Mensaje(__FILE__,__LINE__,$qCliCco." ~ ".mysql_num_rows($xCliCco));
	                        
	                        $y = 0;
	                        $nCanReg = 0;
											    while ($xRCC = mysql_fetch_array($xCliCco)){
											    	$qCatCon  = "SELECT * ";
														$qCatCon .= "FROM $cAlfa.fpar0144 ";
														$qCatCon .= "WHERE ";
														$qCatCon .= "cacidxxx = \"{$xRCC['cacidxxx']}\" LIMIT 0,1";
														$xCatCon  = f_MySql("SELECT","",$qCatCon,$xConexion01,"");
														$vCatCon  = mysql_fetch_array($xCatCon);
														?>
 					                  <tr bgcolor="<?php echo $vSysStr['system_row_impar_color_ini'] ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_impar_color_ini'] ?>')">
                              <td Class = "letra8" align="center"><?php echo $xRCC['ctoidxxx'] ?></td>
                              <td Class = "letra8"><?php echo $xRCC['ctodesxx'] ?></td>
                              <?php
															/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/ 
															if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
															<td class= "letra8" align="left">
																<a href = "javascript:fnBuscarCategoriaConcepto('<?php echo $xRCC['cacidxxx'] ?>','<?php echo $vCatCon['cacdesxx']?>')" id="IdCac"><?php echo ($xRCC['cacidxxx'] != "")  ? "[".$xRCC['cacidxxx']."] ".$vCatCon['cacdesxx'] : "" ?></a>
															</td>
															<?php }?>
                              <td Class = "letra8">
                                 <input type = 'checkbox' name = 'ch<?php echo $nCanReg ?>' id="<?php echo $xRCC['ctoidxxx']."~".$xRCC['ctodesxx'] ?> ">
                                 <input type = 'hidden' name = 'cCtoVrl02_CCO<?php echo $nCanReg ?>' value="<?php echo $xRCC['ctovlr02'] ?>">
                              </td>
    										    </tr>
                			     <?php $nCanReg++;
											    } 
											  }?>
										    <script language="javascript">
										    	document.forms['frgrm']['nSecuencia'].value = '<?php echo $nCanReg ?>';
                		    </script>
										</table>
								</center>
			 	    </fieldset>
					</td>
				</tr>
		 	</table>
		  </center>
		</form>
		<center>
			 <?php
  	  if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
        <table cellspacing = "0" cellpadding = "1" border = "0" width = "700">
      <?php 
      }else{?>
        <table cellspacing = "0" cellpadding = "1" border = "0" width = "460"><?php 
      }?>
      
				<tr height="21">
				  <?php 
				  if ($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI') { ?>
            <td width="530" height="21"></td>
          <?php
          } else {?>
            <td width="278" height="21"></td>
          <?php 
          } ?>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer"
						onClick = "javascript:f_Aceptar('<?php echo $gSecuencia ?>')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aceptar
					</td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer"
						onClick = "javascript:parent.window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
					</td>
				</tr>
			</table>
			<br>
		</center>
    <?php 
    if ($nCanReg == 0) {
      f_Mensaje(__FILE__,__LINE__," No Se Encontraron Conceptos Contables para el Proveedor.");
    }
    ?>
	</body>
</html>
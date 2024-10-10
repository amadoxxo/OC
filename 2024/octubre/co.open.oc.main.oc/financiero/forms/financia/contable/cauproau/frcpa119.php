<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  $qTblCom = "SELECT TABLE_COMMENT FROM $cOmega.TABLES WHERE TABLE_NAME = \"fpar0119\" LIMIT 0,1";
  $xTblCom = f_MySql("SELECT","",$qTblCom,$xConexion01,"");
  $vTblCom = mysql_fetch_array($xTblCom);
  // f_Mensaje(__FILE__,__LINE__,$gModo." ~ ".$gFunction." ~ ".$gComCod);

  if ($gModo != "" && $gFunction != "") { ?>
	<html>
		<head>
			<title><?php echo $vTblCom['TABLE_COMMENT'] ?></title>
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
			<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css">
			<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/utility.js"></script>
      <script languaje = "javascript">
      
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
    		function f_Nit_Comprobante(xComNit,xPucTipEj,xModo) {
    		  switch (xModo) {
    		    case "VALID":
    		    	switch (xPucTipEj) {
						    case "L": //Tipo ejecucion Local
						    	parent.parent.fmwork.document.forms['frgrm']['nComVlr'  +<?php echo $gSecuencia ?>].disabled = false;
						    	parent.parent.fmwork.document.forms['frgrm']['nComVlrNF'+<?php echo $gSecuencia ?>].disabled = true;
							  break;
							  case "N": //Ejecucion NIIF
									parent.parent.fmwork.document.forms['frgrm']['nComVlr'  +<?php echo $gSecuencia ?>].disabled = true;
							    parent.parent.fmwork.document.forms['frgrm']['nComVlrNF'+<?php echo $gSecuencia ?>].disabled = false;
								break;
								default: //Ambas
									parent.parent.fmwork.document.forms['frgrm']['nComVlr'  +<?php echo $gSecuencia ?>].disabled = false;
						    	parent.parent.fmwork.document.forms['frgrm']['nComVlrNF'+<?php echo $gSecuencia ?>].disabled = false;
								break;
					    }
              switch (xComNit) {
                case "CLIENTE":
                  parent.parent.fmwork.document.forms['frgrm']['cTerTip' +<?php echo $gSecuencia ?>].value = parent.parent.fmwork.document.forms['frgrm']['cTerTip'].value;
                  parent.parent.fmwork.document.forms['frgrm']['cTerId'  +<?php echo $gSecuencia ?>].value = parent.parent.fmwork.document.forms['frgrm']['cTerId'].value;
                  parent.parent.fmwork.document.forms['frgrm']['cTerTipB'+<?php echo $gSecuencia ?>].value = parent.parent.fmwork.document.forms['frgrm']['cTerTipB'].value;
                  parent.parent.fmwork.document.forms['frgrm']['cTerIdB' +<?php echo $gSecuencia ?>].value = parent.parent.fmwork.document.forms['frgrm']['cTerIdB'].value;
                break;
                case "TERCERO":
                  parent.parent.fmwork.document.forms['frgrm']['cTerTip' +<?php echo $gSecuencia ?>].value = parent.parent.fmwork.document.forms['frgrm']['cTerTipB'].value;
                  parent.parent.fmwork.document.forms['frgrm']['cTerId'  +<?php echo $gSecuencia ?>].value = parent.parent.fmwork.document.forms['frgrm']['cTerIdB'].value;
                  parent.parent.fmwork.document.forms['frgrm']['cTerTipB'+<?php echo $gSecuencia ?>].value = parent.parent.fmwork.document.forms['frgrm']['cTerTip'].value;
                  parent.parent.fmwork.document.forms['frgrm']['cTerIdB' +<?php echo $gSecuencia ?>].value = parent.parent.fmwork.document.forms['frgrm']['cTerId'].value;
                break;
              }
    		    break;
    		    case "WINDOW":
    		    	switch (xPucTipEj) {
						    case "L": //Tipo ejecucion Local
						    	parent.window.opener.document.forms['frgrm']['nComVlr'  +<?php echo $gSecuencia ?>].disabled = false;
						    	parent.window.opener.document.forms['frgrm']['nComVlrNF'+<?php echo $gSecuencia ?>].disabled = true;
							  break;
							  case "N": //Ejecucion NIIF
									parent.window.opener.document.forms['frgrm']['nComVlr'  +<?php echo $gSecuencia ?>].disabled = true;
									parent.window.opener.document.forms['frgrm']['nComVlrNF'+<?php echo $gSecuencia ?>].disabled = false;
								break;
								default: //Ambas
									parent.window.opener.document.forms['frgrm']['nComVlr'  +<?php echo $gSecuencia ?>].disabled = false;
									parent.window.opener.document.forms['frgrm']['nComVlrNF'+<?php echo $gSecuencia ?>].disabled = false;
								break;
					    }
              switch (xComNit) {
                case "CLIENTE":
                  parent.window.opener.document.forms['frgrm']['cTerTip' +<?php echo $gSecuencia ?>].value = parent.window.opener.document.forms['frgrm']['cTerTip'].value;
                  parent.window.opener.document.forms['frgrm']['cTerId'  +<?php echo $gSecuencia ?>].value = parent.window.opener.document.forms['frgrm']['cTerId'].value;
                  parent.window.opener.document.forms['frgrm']['cTerTipB'+<?php echo $gSecuencia ?>].value = parent.window.opener.document.forms['frgrm']['cTerTipB'].value;
                  parent.window.opener.document.forms['frgrm']['cTerIdB' +<?php echo $gSecuencia ?>].value = parent.window.opener.document.forms['frgrm']['cTerIdB'].value;
                break;
                case "TERCERO":
                  parent.window.opener.document.forms['frgrm']['cTerTip' +<?php echo $gSecuencia ?>].value = parent.window.opener.document.forms['frgrm']['cTerTipB'].value;
                  parent.window.opener.document.forms['frgrm']['cTerId'  +<?php echo $gSecuencia ?>].value = parent.window.opener.document.forms['frgrm']['cTerIdB'].value;
                  parent.window.opener.document.forms['frgrm']['cTerTipB'+<?php echo $gSecuencia ?>].value = parent.window.opener.document.forms['frgrm']['cTerTip'].value;
                  parent.window.opener.document.forms['frgrm']['cTerIdB' +<?php echo $gSecuencia ?>].value = parent.window.opener.document.forms['frgrm']['cTerId'].value;
                break;
              }
            break;
    		  }
    	  }
    	</script>
	  </head>
	  <body topmargin = "0" leftmargin = "0" margnwidth = "0" marginheight = "0" style = "margin-right:0">
	  <center>
	  	<?php 
			/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/ 
			if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
				<table border = "0" cellpadding = "0" cellspacing = "0" width = "700">
				<?
			}else{
				?>
				<table border = "0" cellpadding = "0" cellspacing = "0" width = "400">	
				<?php 
			}?>
				<tr>
					<td>
						<fieldset>
			   			<legend><?php echo $vTblCom['TABLE_COMMENT'] ?></legend>
			   			<form name = "frcac" method = "post" action="frcpa119.php" target="fmwork">
						    <input type = "hidden" name = "gComId"    	value = "<?php echo $gComId ?>">
						    <input type = "hidden" name = "gComCod"   	value = "<?php echo $gComCod ?>">
						    <input type = "hidden" name = "gCtoId"    	value = "<?php echo $gCtoId ?>">
						    <input type = "hidden" name = "gSecuencia"  value = "<?php echo $gSecuencia ?>">
						    <input type = "hidden" name = "gModo"  			value = "<?php echo $gModo?>">
						    <input type = "hidden" name = "gFunction"  	value = "<?php echo $gFunction?>">
						    <input type = "hidden" name = "gCacId"  		value = "<?php echo $gCacId ?>">
						    <input type = "hidden" name = "gCacDes"  		value = "<?php echo $gCacDes ?>">
						  </form>
	  					<form name = "frgrm" action = "" method = "post" target = "fmpro">
	  						<?php
	  							switch ($gModo) {
	  								case "VALID":
	  									$qDatExt  = "SELECT fpar0119.*,fpar0115.* ";
			  							$qDatExt .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
			  							$qDatExt .= "WHERE ";
			  							$qDatExt .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
			  							$qDatExt .= "$cAlfa.fpar0119.ctoidxxx LIKE \"%$gCtoId%\" AND ";
			  							$qDatExt .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
			  							$qDatExt .= "ORDER BY $cAlfa.fpar0119.pucidxxx,$cAlfa.fpar0119.ctoidxxx ";
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

	  									$mConCom  = array(); $nIndCon = 0;
	  									while ($xRCon = mysql_fetch_array($xDatExt)) {
	  									  $mConceptos = f_explode_array($xRCon['ctocomxx'],"|","~");
	  									  for ($i=0;$i<count($mConceptos);$i++) {
	  									    if (($mConceptos[$i][0] == $gComId && $mConceptos[$i][1] == "") ||
	  									        ($mConceptos[$i][0] == $gComId && $mConceptos[$i][1] == $gComCod)) {
	  									      $mConCom[$nIndCon] = $xRCon;
	  									      $mConCom[$nIndCon]['ctomovxx'] = $mConceptos[$i][2];
	  									      $nIndCon++;
	  									    }
	  									  }
	  									}

											if (count($mConCom) == 1) {
	  										for ($i=0;$i<count($mConCom);$i++) {
													// Si la Cuenta no es de Retencion paso cero en Porcentanje
													if ($mConCom[$i]['pucterxx'] != "R") { $mConCom[$i]['pucretxx'] = "0"; } ?>
													<script languaje = "javascript">
														parent.parent.fmwork.document.forms['frgrm']['cCtoId'   +<?php echo $gSecuencia ?>].id    = "<?php echo $mConCom[$i]['ctoidxxx']?>";
														parent.parent.fmwork.document.forms['frgrm']['cCtoId'   +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['ctoidxxx']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['cCtoDes'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['ctodesxp']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['cComNit'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['ctonitxx']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['cComMov'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['ctomovxx']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['cPucId'   +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucidxxx']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['cPucDet'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucdetxx']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['cPucTer'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucterxx']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['nPucBRet' +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucbaret']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['nPucRet'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucretxx']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['cPucNat'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucnatxx']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['cPucInv'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucinvxx']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['cPucCco'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['puccccxx']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['cPucDoSc' +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucdoscc']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['cPucTipEj'+<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['puctipej']?>";
		                        parent.parent.fmwork.document.forms['frgrm']['cComVlr1' +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['ctovlr01']?>";
		  								      parent.parent.fmwork.document.forms['frgrm']['cComVlr2' +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['ctovlr02']?>";
			  								    f_Nit_Comprobante("<?php echo $mConCom[$i]['ctonitxx']?>","<?php echo $mConCom[$i]['puctipej'] ?>","<?php echo $gModo ?>");
		  								      parent.parent.fmwork.f_Valores_Automaticos("<?php echo $gSecuencia ?>");
					  		            parent.parent.fmwork.f_Cuadre_Debitos_Creditos();
					  		            parent.parent.fmwork.f_Links("cComCscC","VALID","<?php echo $gSecuencia ?>");
													</script>
												<?php }
  										} else { ?>
												<script languaje = "javascript">
	      	    						parent.parent.fmwork.f_Links("<?php echo $gFunction ?>","WINDOW","<?php echo $gSecuencia ?>");
												</script>
											<?php }
	      	      		break;
	  								case "WINDOW":
											$qDatExt  = "SELECT fpar0119.*,fpar0115.* ";
			  							$qDatExt .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
			  							$qDatExt .= "WHERE ";
			  							$qDatExt .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
			  							$qDatExt .= "$cAlfa.fpar0119.ctoidxxx LIKE \"%$gCtoId%\" AND ";
											if($gCacId != ""){
												$qDatExt .= "$cAlfa.fpar0119.cacidxxx = \"$gCacId\" AND ";
											}
			  							$qDatExt .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
			  							$qDatExt .= "ORDER BY $cAlfa.fpar0119.pucidxxx,$cAlfa.fpar0119.ctoidxxx ";
	  									$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
	  									//f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));

	  									$mConCom  = array(); $nIndCon = 0;
	  									while ($xRCon = mysql_fetch_array($xDatExt)) {
	  									  $mConceptos = f_explode_array($xRCon['ctocomxx'],"|","~");
	  									  for ($i=0;$i<count($mConceptos);$i++) {
	  									    if (($mConceptos[$i][0] == $gComId && $mConceptos[$i][1] == "") ||
	  									        ($mConceptos[$i][0] == $gComId && $mConceptos[$i][1] == $gComCod)) {
	  									      $mConCom[$nIndCon] = $xRCon;
	  									      $mConCom[$nIndCon]['ctomovxx'] = $mConceptos[$i][2];
	  									      $nIndCon++;
	  									    }
	  									  }
	  									}

 											if (count($mConCom) > 0) { ?>
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
		 										<center>
		 											<?php 
		 											/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/ 
													if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
					    							<table cellspacing = "0" cellpadding = "1" border = "1" width = "680">
				    							<?php 
													}else{?>
														<table cellspacing = "0" cellpadding = "1" border = "1" width = "400"><?php 
													}?>
														<tr>
															<td widht = "050" Class = "name"><center>Cto</center></td>
															<td widht = "280" Class = "name"><center>Descripcion</center></td>
															<?php
															 /*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/ 
															if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
															<td widht = "150" Class = "name"><center>Categoria Concepto</center></td>
															<?php }?>
															<td widht = "050" Class = "name"><center>Cuenta</center></td>
															<td widht = "020" Class = "name"><center>Mov</center></td>
														</tr>
														<?php for ($i=0;$i<count($mConCom);$i++) {
															// Si la Cuenta no es de Retencion paso cero en Porcentanje
															if ($mConCom[$i]['pucterxx'] != "R") { $mConCom[$i]['pucretxx'] = "0"; }
															if (count($mConCom) > 1) {
																	 
																$qCatCon  = "SELECT * ";
																$qCatCon .= "FROM $cAlfa.fpar0144 ";
																$qCatCon .= "WHERE ";
																$qCatCon .= "cacidxxx = \"{$mConCom[$i]['cacidxxx']}\" LIMIT 0,1";
																$xCatCon  = f_MySql("SELECT","",$qCatCon,$xConexion01,"");
																$vCatCon  = mysql_fetch_array($xCatCon);
																?>
																<tr>
																	<td width = "050" Class = "name"><?php echo $mConCom[$i]['ctoidxxx'] ?></td>
																	<td width = "280" Class = "name">
																		<a href = "javascript:parent.window.opener.document.forms['frgrm']['cCtoId'   +<?php echo $gSecuencia ?>].id   ='<?php echo $mConCom[$i]['ctoidxxx']?>';
																													parent.window.opener.document.forms['frgrm']['cCtoId'   +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['ctoidxxx']?>';
																	                        parent.window.opener.document.forms['frgrm']['cCtoDes'  +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['ctodesxp']?>';
																	                        parent.window.opener.document.forms['frgrm']['cComNit'  +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['ctonitxx']?>';
																	                        parent.window.opener.document.forms['frgrm']['cComMov'  +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['ctomovxx']?>';
																	                        parent.window.opener.document.forms['frgrm']['cPucId'   +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['pucidxxx']?>';
																	                        parent.window.opener.document.forms['frgrm']['cPucDet'  +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['pucdetxx']?>';
																	                        parent.window.opener.document.forms['frgrm']['cPucTer'  +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['pucterxx']?>';
																	                        parent.window.opener.document.forms['frgrm']['nPucBRet' +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['pucbaret']?>';
																	                        parent.window.opener.document.forms['frgrm']['nPucRet'  +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['pucretxx']?>';
																	                        parent.window.opener.document.forms['frgrm']['cPucNat'  +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['pucnatxx']?>';
																	                        parent.window.opener.document.forms['frgrm']['cPucInv'  +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['pucinvxx']?>';
																	                        parent.window.opener.document.forms['frgrm']['cPucCco'  +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['puccccxx']?>';																	                        
																	                        parent.window.opener.document.forms['frgrm']['cPucDoSc' +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['pucdoscc']?>';
																	                        parent.window.opener.document.forms['frgrm']['cPucTipEj'+<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['puctipej']?>';
																	                        parent.window.opener.document.forms['frgrm']['cComVlr1' +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['ctovlr01']?>';
																	  								      parent.window.opener.document.forms['frgrm']['cComVlr2' +<?php echo $gSecuencia ?>].value='<?php echo $mConCom[$i]['ctovlr02']?>';
																	  								      f_Nit_Comprobante('<?php echo $mConCom[$i]['ctonitxx']?>','<?php echo $mConCom[$i]['puctipej'] ?>','<?php echo $gModo ?>');
																	  								      parent.window.opener.f_Valores_Automaticos('<?php echo $gSecuencia ?>');
					  		                                          parent.window.opener.f_Cuadre_Debitos_Creditos();
					  		                                          parent.window.opener.f_Links('cComCscC','VALID','<?php echo $gSecuencia ?>');
																	                        parent.window.close()"><?php echo $mConCom[$i]['ctodesxp'] ?></a>
																	</td>
																	<?php
																	/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/ 
																	if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
																	<td widht = "150" class= "name" align="left">
																		<a href = "javascript:fnBuscarCategoriaConcepto('<?php echo $mConCom[$i]['cacidxxx'] ?>','<?php echo $vCatCon['cacdesxx']?>')" id="IdCac"><?php echo ($mConCom[$i]['cacidxxx'] != "")  ? "[".$mConCom[$i]['cacidxxx']."] ".$vCatCon['cacdesxx'] : "" ?></a>
																	</td>
																	<?php }?>
																	<td width = "050" class= "name"><?php echo $mConCom[$i]['pucidxxx'] ?></td>
																	<td width = "020" class= "name"><?php echo $mConCom[$i]['ctomovxx'] ?></td>
																</tr>
															<?php	} else { ?>
																<script languaje="javascript">
																	parent.window.opener.document.forms['frgrm']['cCtoId'   +<?php echo $gSecuencia ?>].id    = "<?php echo $mConCom[$i]['ctoidxxx']?>";
																	parent.window.opener.document.forms['frgrm']['cCtoId'   +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['ctoidxxx']?>";
					                        parent.window.opener.document.forms['frgrm']['cCtoDes'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['ctodesxp']?>";
					                        parent.window.opener.document.forms['frgrm']['cComNit'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['ctonitxx']?>";
					                        parent.window.opener.document.forms['frgrm']['cComMov'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['ctomovxx']?>";
					                        parent.window.opener.document.forms['frgrm']['cPucId'   +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucidxxx']?>";
					                        parent.window.opener.document.forms['frgrm']['cPucDet'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucdetxx']?>";
					                        parent.window.opener.document.forms['frgrm']['cPucTer'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucterxx']?>";
					                        parent.window.opener.document.forms['frgrm']['nPucBRet' +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucbaret']?>";
					                        parent.window.opener.document.forms['frgrm']['nPucRet'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucretxx']?>";
					                        parent.window.opener.document.forms['frgrm']['cPucNat'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucnatxx']?>";
					                        parent.window.opener.document.forms['frgrm']['cPucInv'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucinvxx']?>";
					                        parent.window.opener.document.forms['frgrm']['cPucCco'  +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['puccccxx']?>";
					                        parent.window.opener.document.forms['frgrm']['cPucDoSc' +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['pucdoscc']?>";
					                        parent.window.opener.document.forms['frgrm']['cPucTipEj'+<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['puctipej']?>";
					                        parent.window.opener.document.forms['frgrm']['cComVlr1' +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['ctovlr01']?>";
					  								      parent.window.opener.document.forms['frgrm']['cComVlr2' +<?php echo $gSecuencia ?>].value = "<?php echo $mConCom[$i]['ctovlr02']?>";
						  								    f_Nit_Comprobante("<?php echo $mConCom[$i]['ctonitxx']?>","<?php echo $mConCom[$i]['puctipej'] ?>","<?php echo $gModo ?>");
					  								      parent.window.opener.f_Valores_Automaticos("<?php echo $gSecuencia ?>");
					  		                  parent.window.opener.f_Cuadre_Debitos_Creditos();
					  		                  parent.window.opener.f_Links("cComCscC","VALID","<?php echo $gSecuencia ?>");
																	parent.window.close();
																</script>
															<?php }
														} ?>
													</table>
												</center>
		 									<?php	} else {
		 										f_Mensaje(__FILE__,__LINE__,"No se Encontraron Registros, Verifique.");?>
												<script>
													parent.window.close();
												</script>
												<?php
		 									}
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
	f_Mensaje(__FILE__,__LINE__,"No se Recibieron Parametros Completos, Verifique.");
} ?>
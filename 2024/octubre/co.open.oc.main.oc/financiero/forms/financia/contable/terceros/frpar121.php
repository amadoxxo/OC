<?php
  namespace openComex;
/**
	 * Listado de Conceptos 
	 * @author Stefany Bravo Perez <stefany.bravo@opentecnologia.com.co>
	 * @package openComex
	 */
	include("../../../../libs/php/utility.php");


  $gCliCto = trim(strtoupper($gCliCto));
  $mCliCto = explode('|',$gCliCto);
?>
<html>
  <title>Param&eacute;trica de Conceptos Contables</title>
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
			
  		function f_Aceptar(xCampo){
  		  var ccadena = '';
  		  for (i=0;i<document.forms['frgrm']['nSecuencia'].value;i++) {
  		    if (document.frgrm["ch"+i].checked == true) {
  		        ccadena += document.frgrm["ch"+i].id+'|';
  		    }
  		  }
  		  parent.window.opener.document.forms['frgrm'][xCampo].value = ccadena;
    		parent.window.opener.f_Mostrar_Conceptos();
				parent.window.close();
			}

  	</script>
  </head>

	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
	<form name = "frcac" method = "post" action="frpar121.php" target="fmwork">
    <input type = "hidden" name = "gCliCto"    	value = "<?php echo $gCliCto ?>">
    <input type = "hidden" name = "gCampo"   		value = "<?php echo $gCampo ?>">
    <input type = "hidden" name = "gCacId"  		value = "<?php echo $gCacId ?>">
    <input type = "hidden" name = "gCacDes"  		value = "<?php echo $gCacDes ?>">
  </form>
	<form name = 'frgrm'>
	<input type="hidden" name = "nSecuencia" value="0">
	<input type = "hidden" name = "gModo"  			value = "<?php echo $gModo?>">
	<input type = "hidden" name = "gFunction"  	value = "<?php echo $gFunction?>">
	 <center>
  		<?php	if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
					<table border = "0" cellpadding = "0" cellspacing = "0" width = "700">
					<?
				}else{
					?>
					<table border = "0" cellpadding = "0" cellspacing = "0" width = "550">	
					<?php 
				}?>

				<tr>
					<td>
						<fieldset>
					  	<legend>Param&eacute;trica de Conceptos Contables</legend>
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
						 	  <center>
										<?php 
							/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/ 
							if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
  							<table cellspacing = "0" cellpadding = "1" border = "1" width = "680">
							<?php 
							}else{?>
								<table cellspacing = "0" cellpadding = "1" border = "1" width = "550"><?php 
							}?>
									<tr bgcolor = '#D6DFF7'>
											<td Class = "name" width = "120"><center>Concepto</center></td>
											<td Class = "name"><center>Descripci&oacute;n</center></td>
											<td Class = "name" width = "120"><center>Cuenta</center></td>
											<?php	
												if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
													<td width = "120" Class = "name"><center>Categoria Concepto</center></td>
												<?php
												}?>
											
											<td Class = "name" width = "20"><center><input type = 'checkbox' name = 'chAll' onClick="javascript:f_Todas()"></input></center></td>
										</tr>
										<?php
										    $qDatIca  = "SELECT $cAlfa.fpar0121.* ";
											  $qDatIca .= "FROM $cAlfa.fpar0121 ";
											  $qDatIca .= "WHERE ";
												if($gCacId != ""){
													$qDatIca .= "$cAlfa.fpar0121.cacidxxx = \"$gCacId\" AND ";
												}
											  $qDatIca .= "$cAlfa.fpar0121.regestxx = \"ACTIVO\" ";
											  $qDatIca .= "ORDER BY ABS($cAlfa.fpar0121.ctoidxxx) ";
                        $xDatIca  = f_MySql("SELECT","",$qDatIca,$xConexion01,"");
												// f_Mensaje(__FILE__,__LINE__,$qDatIca." ~ ".mysql_num_rows($xDatIca));
												
                        $y = 0;
                        $nCanReg = 0;
										    while ($xDI = mysql_fetch_array($xDatIca)){
										    	
													$qCatCon  = "SELECT * ";
													$qCatCon .= "FROM $cAlfa.fpar0144 ";
													$qCatCon .= "WHERE ";
													$qCatCon .= "cacidxxx = \"{$xDI['cacidxxx']}\" LIMIT 0,1";
													$xCatCon  = f_MySql("SELECT","",$qCatCon,$xConexion01,"");
													$vCatCon  = mysql_fetch_array($xCatCon);
										    	$y++;
										      $zColor = "{$vSysStr['system_row_impar_color_ini']}";
 					                if($y % 2 == 0) {
    												 $zColor = "{$vSysStr['system_row_par_color_ini']}";
    											}
 					                if (in_array($xDI['ctoidxxx'],$mCliCto,true)) { ?>
 					                  <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
                              <td Class = "letra8" align="center"><?php echo $xDI['ctoidxxx'] ?></td>
                              <td Class = "letra8"><?php echo $xDI['ctodesxx'] ?></td>
                              <td Class = "letra8" align="center"><?php echo $xDI['pucidxxx'] ?></td>
                              <!-- <td Class = "letra8" align="left"><?php echo ($xDI['cacidxxx'] != "")  ? "[".$xDI['cacidxxx']."] ".$vCatCon['cacdesxx'] : "" ?></td> -->
                              <?php
																	/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/ 
																	if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
																	<td widht = "150" class= "name" align="left">
																		<a href = "javascript:fnBuscarCategoriaConcepto('<?php echo $xDI['cacidxxx'] ?>','<?php echo $vCatCon['cacdesxx']?>')" id="IdCac"><?php echo ($xDI['cacidxxx'] != "")  ? "[".$xDI['cacidxxx']."] ".$vCatCon['cacdesxx'] : "" ?></a>
																	</td>
																	<?php }?>
                              <td Class = "letra8"><input type = 'checkbox' name = 'ch<?php echo $nCanReg ?>' id="<?php echo $xDI['ctoidxxx'] ?>" checked></td>
    										    </tr>
  										    <?php } else { ?>
	 					                  <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
	                              <td Class = "letra8" align="center"><?php echo $xDI['ctoidxxx'] ?></td>
	                              <td Class = "letra8"><?php echo $xDI['ctodesxx'] ?></td>
	                              <td Class = "letra8" align="center"><?php echo $xDI['pucidxxx'] ?></td>
	                              <!-- <td Class = "letra8" align="left"><?php echo ($xDI['cacidxxx'] != "")  ? "[".$xDI['cacidxxx']."] ".$vCatCon['cacdesxx'] : "" ?></td>  -->
	                              <?php
																	/*** Si la variable Categoriacion Conceptos Factuacion esta encendida se habilita el menu de Categoria Conceptos***/ 
																	if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){?>
																	<td widht = "150" class= "name" align="left">
																		<a href = "javascript:fnBuscarCategoriaConcepto('<?php echo $xDI['cacidxxx'] ?>','<?php echo $vCatCon['cacdesxx']?>')" id="IdCac"><?php echo ($xDI['cacidxxx'] != "")  ? "[".$xDI['cacidxxx']."] ".$vCatCon['cacdesxx'] : "" ?></a>
																	</td>
																	<?php }?>
	                              <td Class = "letra8"><input type = 'checkbox' name = 'ch<?php echo $nCanReg ?>' id="<?php echo $xDI['ctoidxxx'] ?>"></td>
	    										    </tr>
	  										    <?php
                					}
                			     if($_COOKIE['kModo'] == "VER"){ ?>
                			     <script language="javascript">
                			       document.getElementById('<?php echo $xDI['ctoidxxx'] ?>').disabled  = true;
                			     </script>
                			     <?php }
                			     $nCanReg++;
										    } ?>
										    <script language="javascript">
										    	document.forms['frgrm']['nSecuencia'].value = '<?php echo $nCanReg ?>';
                		    </script>
										</table>
								</center>
			 	    
					</td>
				</tr>
		 	</table>
		  </center>
		</form>
		<center>
			  <?php  
			  if ($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI') { ?>
          <table border = "0" cellpadding = "0" cellspacing = "0" width = "700">
        <? 
        } else {  ?>
          <table border = "0" cellpadding = "0" cellspacing = "0" width = "550">  
        <?php 
        } ?>
        
				<tr height="21">
				<?php
				  if($_COOKIE['kModo'] != "VER"){
				 	
  				 	if ($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI') { ?>
            <td width="520" height="21"></td>
            <?php
            } else {?>
            <td width="368" height="21"></td>
         <?php 
            } ?>
            
  					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer"
  						onClick = "javascript:f_Aceptar('<?php echo $gCampo ?>')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aceptar
  					</td>
  					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer"
  						onClick = "javascript:parent.window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
  					</td>
					<?php 
          }else{
					?>
  					<td width="459" height="21"></td>
  					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer"
  						onClick = "javascript:parent.window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
  					</td>
					<?php
					}
					?>
				</tr>
			</table>
		</center>
	</body>
</html>
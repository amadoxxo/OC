<?php
  namespace openComex;
/**
	 * Listado de Conceptos Marcados como de Retencion ICA
	 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
	 * @package openComex
	 */
	include("../../../../libs/php/utility.php");


  $gCtoCtori = trim(strtoupper($gCtoCtori));
  $mCtoCtori = explode('|',$gCtoCtori);
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
  		  var cEncontro = 0;
  		  for (i=0;i<document.forms['frgrm']['nSecuencia'].value;i++) {
  		    if (document.frgrm["ch"+i].checked == true) {
  	        ccadena += document.frgrm["ch"+i].id+'|';
  	        //busco si alguno otro seleccionado tiene la misma sucursal
    	      for (j=0;j<document.forms['frgrm']['nSecuencia'].value;j++) {
                if (document.frgrm["ch"+j].checked == true && 
                  document.frgrm["ch"+i].id != document.frgrm["ch"+j].id &&
                  document.frgrm["suc"+i].value == document.frgrm["suc"+j].value) {
                cEncontro = 1;        		        	
                j = document.forms['frgrm']['nSecuencia'].value;
                i = document.forms['frgrm']['nSecuencia'].value;
                }
    	      }
  		    }
  		  }
  		  if (cEncontro == 0) {
	  		  window.opener.document.forms['frgrm'][xCampo].value = ccadena;
	    		window.opener.f_Mostrar_SucRetIca();
					window.close();
  		  } else {
  	  		alert("No Puede Seleccionar Conceptos Contables con la Misma Sucursal.");
  		  }
			}

  	</script>
  </head>

	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
	<form name = 'frgrm'>
	<input type="hidden" name = "nSecuencia" value="0">
	 <center>
  		<table border ="0" cellpadding="0" cellspacing="0" width="550">
				<tr>
					<td>
				  	<fieldset>
					  	<legend>Param&eacute;trica de Conceptos Contables</legend>
						 	  <center>
									<table border = "1" cellpadding = "0" cellspacing = "0" width="550">
									<tr bgcolor = '#D6DFF7'>
											<td Class = "name" width = "120"><center>Concepto</center></td>
											<td Class = "name" width = "200"><center>Descripci&oacute;n</center></td>
											<td Class = "name" width = "80"><center>Sucursal</center></td>
											<td Class = "name" width = "130"><center>Tarifa</center></td>
											<td Class = "name" width = "20"><center><input type = 'checkbox' name = 'chAll' onClick="javascript:f_Todas()"></input></center></td>
										</tr>
										<?php
												$vCtaRtIca = explode(",",$vSysStr['financiero_cuentas_reteica']);
												$cCtaRtIca = "\"".implode("\",\"", $vCtaRtIca)."\"";

										    $qDatIca  = "SELECT $cAlfa.fpar0119.*, ";
											  $qDatIca .= "IF($cAlfa.fpar0119.ctodesxp != \"\",$cAlfa.fpar0119.ctodesxp,IF($cAlfa.fpar0119.ctodesxx != \"\",$cAlfa.fpar0119.ctodesxx,\"CONCEPTO SIN DESCRIPCION\")) AS ctodesxp, ";
											  $qDatIca .= "$cAlfa.fpar0115.pucretxx ";
											  $qDatIca .= "FROM $cAlfa.fpar0119 ";
											  $qDatIca .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
											  $qDatIca .= "WHERE ";
											  $qDatIca .= "SUBSTRING($cAlfa.fpar0119.pucidxxx,1,4) IN ($cCtaRtIca) AND ";
										    $qDatIca .= "$cAlfa.fpar0119.ctosucri != \"\" AND ";
											  $qDatIca .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
											  $qDatIca .= "ORDER BY ABS($cAlfa.fpar0119.ctoidxxx) ";
                        $xDatIca  = f_MySql("SELECT","",$qDatIca,$xConexion01,"");
												//f_Mensaje(__FILE__,__LINE__,$qDatIca." ~ ".mysql_num_rows($xDatIca));
												
                        $y = 0;
                        $nCanReg = 0;
										    while ($xDI = mysql_fetch_array($xDatIca)){
										    $y++;
										      $zColor = "{$vSysStr['system_row_impar_color_ini']}";
 					                if($y % 2 == 0) {
    												 $zColor = "{$vSysStr['system_row_par_color_ini']}";
    											}
 					                if (in_array($xDI['ctoidxxx'],$mCtoCtori,true)) { ?>
 					                  <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
                              <td Class = "letra8" align="center"><?php echo $xDI['ctoidxxx'] ?></td>
                              <td Class = "letra8"><?php echo $xDI['ctodesxp'] ?></td>
                              <td Class = "letra8" align="center"><?php echo $xDI['ctosucri'] ?></td>
                              <td Class = "letra8" align="center"><?php echo $xDI['pucretxx'] ?></td>
                              <td Class = "letra8">
                                <input type = 'checkbox' name = 'ch<?php echo $nCanReg ?>' id="<?php echo $xDI['ctoidxxx'] ?>" checked>
                                <input type = 'hidden' name = 'suc<?php echo $nCanReg ?>' value='<?php echo $xDI['ctosucri'] ?>'>
                              </td>
    										    </tr>
  										    <?php } else { ?>
	 					                  <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
	                              <td Class = "letra8" align="center"><?php echo $xDI['ctoidxxx'] ?></td>
	                              <td Class = "letra8"><?php echo $xDI['ctodesxp'] ?></td>
	                              <td Class = "letra8" align="center"><?php echo $xDI['ctosucri'] ?></td>
	                              <td Class = "letra8" align="center"><?php echo $xDI['pucretxx'] ?></td>
	                              <td Class = "letra8">
	                               <input type = 'checkbox' name = 'ch<?php echo $nCanReg ?>' id="<?php echo $xDI['ctoidxxx'] ?>">
	                               <input type = 'hidden' name = 'suc<?php echo $nCanReg ?>' value='<?php echo $xDI['ctosucri'] ?>'>
	                              </td>
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
			 	    </fieldset>
					</td>
				</tr>
		 	</table>
		  </center>
		</form>
		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="550">
				<tr height="21">
				<?php
				  if($_COOKIE['kModo'] != "VER"){
				?>
				 	<td width="368" height="21"></td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer"
						onClick = "javascript:f_Aceptar('<?php echo $gCampo ?>')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aceptar
					</td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer"
						onClick = "javascript:window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
					</td>
					<?php }else{
					?>
					<td width="459" height="21"></td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer"
						onClick = "javascript:window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
					</td>
					<?php
					}
					?>
				</tr>
			</table>
		</center>
	</body>
</html>
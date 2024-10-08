<?php
  namespace openComex;
/**
	 * Listado de Ica x Sucursales
	 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
	 * @package openComex
	 */
	include("../../../../libs/php/utility.php");


  $gSucIca = trim(strtoupper($gSucIca));
  $mSucIca = explode('~',$gSucIca);
?>
<html>
  <title>Ica x Sucursales </title>
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
  		  for (i=0;i<document.forms['frgrm']['nSecuencia'].value;i++) {
  		    if (document.frgrm["ch"+i].checked == true) {
  		        ccadena += document.frgrm["ch"+i].id+'~';
  		    }
  		  }
  		  window.opener.document.forms['frgrm'][xCampo].value = ccadena;
				window.close();
			}

  	</script>
  </head>

	<body topmargin = 0 leftmargin = 0 margnwidth = 0 marginheight = 0 style = 'margin-right:0'>
	<form name = 'frgrm'>
	<input type="hidden" name = "nSecuencia" value="0">
	 <center>
  		<table border ="0" cellpadding="0" cellspacing="0" width="340">
				<tr>
					<td>
				  	<fieldset>
					  	<legend>Listado Ica x Sucursales</legend>
						 	  <center>
									<table border = "1" cellpadding = "0" cellspacing = "0" width="340">
									<tr bgcolor = '#D6DFF7'>
											<td Class = "name" width = "60"><center>Id</center></td>
											<td Class = "name" width = "260"><center>Sucursal</center></td>
											<td Class = "name" width = "20"><center><input type = 'checkbox' name = 'chAll' onClick="javascript:f_Todas()"></input></center></td>
										</tr>
										<?php
										    $qDatIca  = "SELECT * ";
										    $qDatIca .= "FROM $cAlfa.fpar0008 ";
										    $qDatIca .= "WHERE regestxx = \"ACTIVO\" ";
                        $qDatIca .= "GROUP BY sucidxxx ";
										    $qDatIca .= "ORDER BY sucdesxx ";
                        $xDatIca  = f_MySql("SELECT","",$qDatIca,$xConexion01,"");
                        // f_Mensaje(__FILE__,__LINE__,$qDatIca."~".mysql_num_rows($xDatIca));
                        
                        $y = 0;
                        $nCanReg = 0;
										    while ($xDI = mysql_fetch_array($xDatIca)){
										    $y++;
										      $zColor = "{$vSysStr['system_row_impar_color_ini']}";
 					                if($y % 2 == 0) {
    												 $zColor = "{$vSysStr['system_row_par_color_ini']}";
    											}
 					                if (in_array($xDI['sucidxxx'],$mSucIca,true)) { ?>
 					                  <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
                              <td Class = "letra8" align="center"><?php echo $xDI['sucidxxx'] ?></td>
                              <td Class = "letra8"><?php echo $xDI['sucdesxx'] ?></td>
                              <td Class = "letra8"><input type = 'checkbox' name = 'ch<?php echo $nCanReg ?>' id="<?php echo $xDI['sucidxxx']?>" checked></td>
    										    </tr>
  										    <?php } else { ?>
	 					                  <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
	                              <td Class = "letra8" align="center"><?php echo $xDI['sucidxxx'] ?></td>
	                              <td Class = "letra8"><?php echo $xDI['sucdesxx'] ?></td>
	                              <td Class = "letra8"><input type = 'checkbox' name = 'ch<?php echo $nCanReg ?>' id="<?php echo $xDI['sucidxxx']?>"></td>
	    										    </tr>
	  										    <?php
                					}
                			     if($_COOKIE['kModo'] == "VER"){ ?>
                			     <script language="javascript">
                			       document.getElementById('<?php echo $xDI['sucidxxx'] ?>').disabled  = true;
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
			<table border="0" cellpadding="0" cellspacing="0" width="340">
				<tr height="21">
				<?php
				  if($_COOKIE['kModo'] != "VER"){
				?>
				 	<td width="158" height="21"></td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer"
						onClick = "javascript:f_Aceptar('<?php echo $gCampo ?>')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aceptar
					</td>
					<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer"
						onClick = "javascript:window.close()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir
					</td>
					<?php }else{
					?>
					<td width="249" height="21"></td>
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
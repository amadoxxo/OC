<?php 
  namespace openComex;
	/**
   * Formulario de Historico de Observaciones a Documentos.
   * --- Descripcion: Permite Filtrar por una Factura Determinada y Mostrar su Historico de Observaciones.
   * @author Juan jose Trujillo Ch. <juan.trujillo@open-eb.co>
   * @package Opencomex
   */
	include("../../../../libs/php/utility.php");
	
	if($_POST['cPerAno'] == ""){
	  $_POST['cPerAno'] = date('Y');
	}
?>
<html>
<head>
		
	  <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
   	<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
   	<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
   	<script language = 'javascript'>
   	
		  function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			}
			
		  function f_Links(xLink,xSwitch,xIteration) {
			  var zX    = screen.width;
				var zY    = screen.height;
				switch (xLink) {
					case "cComCsc":
						if (xSwitch == "VALID") {
							
							var zRuta   = "frfcochi.php?gWhat=VALID&gFunction=cComCsc&cComCsc="+
														document.forms['frgrm']['cComCsc'].value+
														"&cPerAno="+document.forms['frgrm']['cPerAno'].value+
														"&cTipoFac="+document.forms['frgrm']['cTipoFac'].value+"";
							parent.fmpro.location = zRuta;
																				
						} else {
							var zNx     = (zX-400)/2;
							var zNy     = (zY-250)/2;
							var zWinPro = "width=400,scrollbars=1,height=250,left="+zNx+",top="+zNy;
							var zRuta   = "frfcochi.php?gWhat=WINDOW&gFunction=cComCsc&cComCsc="+
														document.forms['frgrm']['cComCsc'].value+
														"&cPerAno="+document.forms['frgrm']['cPerAno'].value+
														"&cTipoFac="+document.forms['frgrm']['cTipoFac'].value+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
				 }			   
			}

		  function f_Buscar(){
			  if(document.forms['frgrm']['cPerAno'].value == "" || 
					 document.forms['frgrm']['cComId'].value  == "" || 
					 document.forms['frgrm']['cComCod'].value == "" || 
					 document.forms['frgrm']['cComCsc'].value == "" ||
					 document.forms['frgrm']['cComCsc2'].value == "") {
					 alert("Debe Seleccionar una Factura. Verifique.\n");
			  } else {
					document.forms['frgrm'].submit();
			  }
			}
		</script>
		<style type="text/css"> 
		  tr .fondo { 
        color: #15428B;
        font: bold 11px tahoma,arial,verdana,sans-serif;
        padding: 5px 3px 4px 5px;
        border: 1px solid #99BBE8;
        line-height: 15px;
        background: transparent url(<?php echo $cPlesk_Skin_Directory ?>/white-top-bottom.gif) repeat-x 0 -1px;
        height:25px;
      } 
      td .fondo1 { 
        vertical-align: middle;
        border: 1px solid #D0D0D0;
        background-color:#f1f1f1;
        padding-left:5px;
      }
      table .tabla {
        border: 1px solid #99BBE8;
      }
      td .blanco {
        vertical-align: middle;
        border: 1px solid #D0D0D0;
        background-color:#FFF;
        padding-left:5px;
      } 
    </style> 
  </head>
		<body topmargin = 0 leftmargin = 0 marginwidth = 0 marginheight = 0 style = 'margin-right:0'>
		<br>
		<center>
			<table border="0" cellpadding="0" cellspacing="0" width="95%">
				<tr height="21">
							<td height="21">&nbsp;</td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>			  	
				</tr>
			</table>
		</center>
		<center>
			<table border ="0" cellpadding="0" cellspacing="0" width="95%">
				<tr>
					<td>
				  	<fieldset>
					  	<legend><?php echo $_COOKIE['kMenDes'] ?></legend>
						 	<form name = 'frgrm' action = 'fraffprn.php' method = 'post' target='fmwork'>
							 	<center>
      						<table border = '0' cellpadding = '0' cellspacing = '0' width='100%'>
  							 		<tr> 
  							 			<td class ='clase08' style="width:80"> A&NtildeO <br>
  											<select name = "cPerAno" style="width:80;height:20">
            	        	  <?php for($i=$vSysStr['financiero_ano_instalacion_modulo'];$i<=date('Y');$i++){ ?>
            	        	    <option value="<?php echo $i ?>"><?php echo $i ?></option>
            	        	  <?php  } ?>            	        	  
            	        	</select>
            	        	<script language="javascript">
                					document.forms['frgrm']['cPerAno'].value = "<?php  echo $_POST['cPerAno'] ?>";
               					</script> 
               				</td> 
  							 			<td Class = 'clase08' style="width:20">ID<br>
  											<input type = 'text' Class = 'letra' style = 'width:20' name = 'cComId' value="<?php  echo $_POST['cComId'] ?>" readonly>
  							 			</td>
  							 			<td Class = 'clase08' style="width:40">COD<br>
  											<input type = 'text' Class = 'letra' style = 'width:40' name = 'cComCod' value="<?php  echo $_POST['cComCod'] ?>" readonly>
  							 			</td>

											<?php if($cAlfa == "GRUMALCO" || $cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO") { ?>
												<td Class = 'clase08' style="width:150">TIPO DE FACTURA<br>
													<select name = "cTipoFac" style="width:150;height:20">
														<option value="">[SELECCIONE]</option>
														<option value="DEFINITIVA">DEFINITIVA</option>
														<option value="PROVISIONAL">PROVISIONAL</option>
													</select>
												</td>
												<script language="javascript">
                					document.forms['frgrm']['cTipoFac'].value = "<?php echo $_POST['cTipoFac'] ?>";
               					</script> 
											<?php } else { ?>
							 					<input type = 'hidden' Class = 'letra' style = 'width:20' name = 'cTipoFac' value="">
											<?php } ?>

											<td Class = "clase08" style="width:100">CONSECUTIVO<br>
												<input type = 'text' Class = 'letra' style = 'width:100' name = 'cComCsc' value="<?php  echo $_POST['cComCsc'] ?>" maxlength="10"
													onBlur = "if(document.forms['frgrm']['cComCsc'].value.length > 1){
																			f_Links('cComCsc','VALID');
																			this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>'
																		}else{
																			alert('Debe Digitar al Menos Dos Digitos de la Factura');
																		}"
													onFocus="javascript:document.forms['frgrm']['cComId'].value   = '';
																							document.forms['frgrm']['cComCod'].value  = '';
																							document.forms['frgrm']['cComCsc'].value  = '';
																							document.forms['frgrm']['cComCsc2'].value = '';
																							this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>'">
												<input type="hidden" name ="cComCsc2" value="<?php  echo $_POST['cComCsc2'] ?>" readonly>
											</td>
											<td Class = 'clase08' style="width:50" align="center"><br>
												<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:pointer" title="Buscar"
												onClick = "javascript:f_Buscar()">
												<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:pointer" title="Mostrar Todo"
												onClick ="javascript:document.forms['frgrm']['cPerAno'].value  = '<?php echo date('Y') ?>';
																							document.forms['frgrm']['cComId'].value   = '';
																							document.forms['frgrm']['cComCod'].value  = '';
																							document.forms['frgrm']['cComCsc'].value  = '';
																							document.forms['frgrm']['cComCsc2'].value = '';
																							document.forms['frgrm'].submit()">
											</td>
											<td Class = 'clase08' align="left">&nbsp;
											</td>
	  								</tr>
  								</table>
								</center>
								<hr></hr>
								<?php 
								#Buscando las observaciones de la factura
								if ($_POST['cPerAno'] != "" && $_POST['cComId'] != "" && $_POST['cComCod'] != "" && $_POST['cComCsc'] != "" && $_POST['cComCsc2'] != "") {
									#Busco el Comprobante
									$cPerAno = $_POST['cPerAno'];
									 						  		
				 					$qFacDat  = "SELECT ";
									$qFacDat .= "$cAlfa.fcoc$cPerAno.comidxxx, ";
									$qFacDat .= "$cAlfa.fcoc$cPerAno.comcodxx, ";
									$qFacDat .= "$cAlfa.fcoc$cPerAno.comcscxx, ";
									$qFacDat .= "$cAlfa.fcoc$cPerAno.comcsc2x, ";
									$qFacDat .= "$cAlfa.fcoc$cPerAno.teridxxx, ";
									$qFacDat .= "$cAlfa.fcoc$cPerAno.diridxxx, ";
									$qFacDat .= "$cAlfa.fcoc$cPerAno.comfacpr, ";
									$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx, ";
									$qFacDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\", $cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X)) AS clinomxx, ";
									$qFacDat .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"DIRECTOR SIN NOMBRE\") AS dirnomxx ";
									$qFacDat .= "FROM $cAlfa.fcoc$cPerAno ";
									$qFacDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cPerAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
									$qFacDat .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.fcoc$cPerAno.diridxxx = $cAlfa.SIAI0003.USRIDXXX ";
									$qFacDat .= "WHERE ";
									//Se valida el tipo de factura (Provisional o Definitiva) para realizar la consulta
									if($_POST['cTipoFac'] == "PROVISIONAL"){
										$qFacDat .= "(($cAlfa.fcoc$cPerAno.comidxxx = \"{$_POST['cComId']}\" AND ";
										$qFacDat .= "$cAlfa.fcoc$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";
										$qFacDat .= "$cAlfa.fcoc$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";
										$qFacDat .= "$cAlfa.fcoc$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\" AND ";
										$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx = \"PROVISIONAL\") OR ";
										$qFacDat .= "(\"{$_POST['cComCsc']}\" = SUBSTRING_INDEX(SUBSTRING_INDEX(comfacpr, \"-\", -2) , \"-\" ,1) AND ";
										$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx = \"ACTIVO\")) ";
									}else{
										$qFacDat .= "$cAlfa.fcoc$cPerAno.comidxxx = \"{$_POST['cComId']}\" AND ";
										$qFacDat .= "$cAlfa.fcoc$cPerAno.comcodxx = \"{$_POST['cComCod']}\" AND ";
										$qFacDat .= "$cAlfa.fcoc$cPerAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";
										$qFacDat .= "$cAlfa.fcoc$cPerAno.comcsc2x = \"{$_POST['cComCsc2']}\" AND ";
										$qFacDat .= "$cAlfa.fcoc$cPerAno.regestxx = \"ACTIVO\" ";
									}
									$qFacDat .= "LIMIT 0,1 ";
									$xFacDat = f_MySql("SELECT","",$qFacDat,$xConexion01,"");
									$vFacDat = mysql_fetch_array($xFacDat);

									if (mysql_num_rows($xFacDat) > 0 ) { 
										// Se asigna el consecutivo de la factura segun el tipo (Provisional o Definita)
										if($vFacDat['regestxx'] == "PROVISIONAL" && $_POST['cTipoFac'] == "PROVISIONAL"){
											$cComIdxx = $vFacDat['comidxxx'];
											$cComCodx = $vFacDat['comcodxx'];
											$cComCscx = $vFacDat['comcscxx'];
											$cComCsc2 = $vFacDat['comcsc2x'];
										}elseif($vFacDat['regestxx'] == "ACTIVO" && $_POST['cTipoFac'] == "PROVISIONAL"){
											$vComFacpr = explode('-', $vFacDat['comfacpr']);
											$cComIdxx = $vComFacpr[0];
											$cComCodx = $vComFacpr[1];
											$cComCscx = $vComFacpr[2];
											$cComCsc2 = $vComFacpr[3];
										}else{
											$cComIdxx = $vFacDat['comidxxx'];
											$cComCodx = $vFacDat['comcodxx'];
											$cComCscx = $vFacDat['comcscxx'];
											$cComCsc2 = $vFacDat['comcsc2x'];
										}
										?>
										<table cellspacing="0" cellpadding="0" width="100%" class="tabla">
											<tr class="fondo">
												<td colspan="2" style="padding-left:5px;font-size:14px;font-weight:bold;height:30px">FACTURA: <?php echo $cComIdxx."-".$cComCodx."-".$cComCscx ?></td>
											</tr>
											<tr>
												<td class="fondo1 name" width="80">CLIENTE</td>
												<td class="blanco letra7"><?php echo $vFacDat['clinomxx']." [{$vFacDat['teridxxx']}]" ?></td>
											</tr>
											<tr>
												<td class="fondo1 name" width="80">DIRECTOR</td>
												<td class="blanco letra7"><?php echo $vFacDat['dirnomxx']." [{$vFacDat['diridxxx']}]" ?></td>
											</tr>
										</table>
										<br>
										<?php #Busco las observaciones
										$qObsDat  = "SELECT ";
										$qObsDat .= "$cAlfa.fcob0000.*, ";
										$qObsDat .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS usrnomxx, ";
										$qObsDat .= "IF($cAlfa.fpar0123.gofdesxx != \"\",$cAlfa.fpar0123.gofdesxx,\"SIN DESCRIPCION\") AS gofdesxx ";
										$qObsDat .= "FROM $cAlfa.fcob0000 ";
										$qObsDat .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.fcob0000.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";								  
										$qObsDat .= "LEFT JOIN $cAlfa.fpar0123 ON $cAlfa.fcob0000.gofidxxx = $cAlfa.fpar0123.gofidxxx AND $cAlfa.fpar0123.goftipxx = \"FACTURACION\" ";
										$qObsDat .= "WHERE " ;
										$qObsDat .= "$cAlfa.fcob0000.comidxxx = \"$cComIdxx\" AND ";
										$qObsDat .= "$cAlfa.fcob0000.comcodxx = \"$cComCodx\" AND ";
										$qObsDat .= "$cAlfa.fcob0000.comcscxx = \"$cComCscx\" AND ";
										$qObsDat .= "$cAlfa.fcob0000.comcsc2x = \"$cComCsc2\" ";
										$qObsDat .= "ORDER BY ABS($cAlfa.fcob0000.obscscxx) ";
										
										$xObsDat = f_MySql("SELECT","",$qObsDat,$xConexion01,""); 
										
										if (mysql_num_rows($xObsDat) > 0 ) { 
											while ($xROD = mysql_fetch_array($xObsDat)) {  ?>
												<table cellspacing="0" cellpadding="0" width="100%" class="tabla">
													<tr class="fondo">
														<td colspan="2" style="padding-left:5px;font-size:14px;font-weight:bold;height:30px">TIPO: <?php echo $xROD['gofdesxx'] ?></td>
													</tr>
												</table>
												<table cellspacing="0" cellpadding="0" width="100%" class="tabla">
													<tr>
														<td class="fondo1 name" width="100">Usuario</td>
														<td class="blanco letra7"><?php echo $xROD['usrnomxx']." [{$xROD['regusrxx']}]" ?></td>
														<td class="fondo1 name" width="80">Fecha</td>
														<td class="blanco letra7"><?php echo $xROD['regfcrex'] ?></td>
													</tr>
													<tr>
														<td class="fondo1 name">Fecha Nueva</td>
														<td class="blanco letra7" colspan="3"><?php echo $xROD['comfecxx'] ?></td>
													</tr>
													<tr>
														<td class="fondo1 name">Fecha Anterior</td>
														<td class="blanco letra7" colspan="3"><?php echo $xROD['comfecan'] ?></td>
													</tr>
													<tr>
													<td class="fondo1 name" width="80">Observaci&oacute;n</td>
													<td class="blanco letra7" colspan="3"><?php echo $xROD['obsobsxx'] ?></td>
												</tr>
												</table>
												<br>
											<?php  }
										} else {
											echo "No se encontraron observaciones.";
										}
									} else {
										echo "Factura no encontrada en el a&ntilde;o $cPerAno.";
									}
								}?>
			 	      </form>
						</fieldset>
					</td>
				</tr>
		 	</table>
		</center>        
  </body> 
</html>
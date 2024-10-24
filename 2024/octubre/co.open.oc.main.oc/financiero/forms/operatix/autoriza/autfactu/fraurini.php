<?php
  namespace openComex;
   /**
	 * Legaliazar Formularios al Gasto .
	 * --- Descripcion: Me lista los formularios con estado PRVGASTO. de todos los Directores de Cuenta de Toda Colombia.
	 * @author Paola Garay <dp3@opentecnologia.com.co>
	 * @version 001
	 */
	include("../../../../libs/php/utility.php");
  
	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlDb = $kDf[3];

  /* Busco en la 05 que Tiene Permiso el Usuario*/
  $qUsrMen  = "SELECT * ";
  $qUsrMen .= "FROM $cAlfa.sys00005 ";
  $qUsrMen .= "WHERE ";
  $qUsrMer .= "sys00005.usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
  $qUsrMen .= "sys00005.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
  $qUsrMen .= "sys00005.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
  $qUsrPer .= "sys00005.menidxxx = \"{$mUsrMen['menidxxx']}\"  LIMIT 0,1";
  $qUsrMen .= "sys00005.menimgon <> '' ";
  $qUsrMen .= "ORDER BY sys00005.menordxx";
  $xUsrMen  = f_MySql("SELECT","",$qUsrMen,$xConexion01,"");
 
?>

<html>
	<head>
  	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
   	<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
   	<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
   	<script language="javascript">
   	

      
   	function f_Link(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
     	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      document.cookie="kMenDes="+xMenDes+";path="+"/";
     	document.cookie="kModo="+xOpcion;
     	//document.cookie="kModo="+xOpcion+";path="+"/";
     	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
     	document.location = xForm; // Invoco el menu.
     	
    }

    	function uCargaVariable(xRecords) {
	  	  var paso=0;
	  		switch (xRecords) {
					case "1":
			  		if (document.forms['frgrm']['vCheck'].checked == true) {
							document.forms['frgrm']['vDo'].value = document.forms['frgrm']['vCheck'].value;
		  			}
	  			break;
	  			default:
			  		for (i=0;i<document.forms['frgrm']['vCheck'].length;i++) {
			  			if (document.forms['frgrm']['vCheck'][i].checked == true && paso==0) {
				  			document.forms['frgrm']['vDo'].value += document.forms['frgrm']['vCheck'][i].value;
				  			paso=1;
		  				}
		  			}
	  			break;
	  		}
	  	}

    	function f_Autoriza_Reimpresion() {
				switch (document.forms['frgrm']['vRecords'].value) {
					case "1":
						if (document.forms['frgrm']['vCheck'].checked == true) {
							var zMatriz = document.forms['frgrm']['vCheck'].id.split('~');
							if (confirm("Esta Seguro de AUTORIZAR REIMPRESION para el Comprobante "+zMatriz[0]+"-"+zMatriz[1]+"-"+zMatriz[2]+"-"+zMatriz[3])) {
								document.cookie="kModo=AUTREIMP";
								document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
								document.forms['frestado']['cComId'].value   = zMatriz[0];
								document.forms['frestado']['cComCod'].value  = zMatriz[1];
								document.forms['frestado']['cComCsc'].value  = zMatriz[2];
								document.forms['frestado']['cComCsc2'].value = zMatriz[3];
								document.forms['frestado']['dFecCre'].value  = zMatriz[4];
								document.forms['frestado'].submit();
							}
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frgrm']['vCheck'].length;i++) {
							if (document.forms['frgrm']['vCheck'][i].checked == true && zSw_Prv == 0) {
								// Solo Deja Legalizar el Primero Seleccionado
								zSw_Prv = 1;
								var zMatriz = document.forms['frgrm']['vCheck'][i].id.split('~');
								//alert(zMatriz[3]);
								if (confirm("Esta Seguro de AUTORIZAR REIMPRESION para el Comprobante "+zMatriz[0]+"-"+zMatriz[1]+"-"+zMatriz[2]+"-"+zMatriz[3])) {
									document.cookie="kModo=AUTREIMP";
									document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
									document.forms['frestado']['cComId'].value   = zMatriz[0];
									document.forms['frestado']['cComCod'].value  = zMatriz[1];
									document.forms['frestado']['cComCsc'].value  = zMatriz[2];
									document.forms['frestado']['cComCsc2'].value = zMatriz[3];
									document.forms['frestado']['dFecCre'].value  = zMatriz[4];
									document.forms['frestado'].submit();
								}
							}
						}
					break;
				}
		 	}

		function uVerificarCheck() {
			  if(document.forms['frgrm']['vCheckAll'].checked == true)
			    document.forms['frgrm']['vChekeados'].value=1;
   	  if (document.forms['frgrm']['vRecords'].value == 1){
   	    if(document.forms['frgrm']['vCheck'].checked == true)
    	     document.forms['frgrm']['vChekeados'].value=1;
   	  }else {
    		if (document.forms['frgrm']['vRecords'].value > 1){
		      	for (i=0;i<document.forms['frgrm']['vCheck'].length;i++){
	   	      	if(document.forms['frgrm']['vCheck'][i].checked == true){
 	   	      	document.forms['frgrm']['vChekeados'].value=1;
 	   	      	i=document.forms['frgrm']['vCheck'].length;
 	   	      }
			      }
			    }
    	}
	 		}

     	function uMarca() {
        if (document.forms['frgrm']['vCheckAll'].checked == true){
          if (document.forms['frgrm']['vRecords'].value == 1){
          	document.forms['frgrm']['vCheck'].checked=true;
          } else {
    	   		if (document.forms['frgrm']['vRecords'].value > 1){
    		     	for (i=0;i<document.forms['frgrm']['vCheck'].length;i++){
       	      	document.forms['frgrm']['vCheck'][i].checked = true;
    		     	}
    		    }
          }
        } else {
    	   	if (document.forms['frgrm']['vRecords'].value == 1){
          	document.forms['frgrm']['vCheck'].checked=false;
          } else {
          	if (document.forms['frgrm']['vRecords'].value > 1){
    			    for (i=0;i<document.forms['frgrm']['vCheck'].length;i++){
    			     	document.forms['frgrm']['vCheck'][i].checked = false;
    			    }
           	}
     	  	}
    	  }
    	}
    	
			function uVerificarCheck() {
			  if(document.forms['frgrm']['vCheckAll'].checked == true)
			    document.forms['frgrm']['vChekeados'].value=1;
		   	  if (document.forms['frgrm']['vRecords'].value == 1){
		   	    if(document.forms['frgrm']['vCheck'].checked == true)
		    	     document.forms['frgrm']['vChekeados'].value=1;
		   	  }else {
		    		if (document.forms['frgrm']['vRecords'].value > 1){
				      	for (i=0;i<document.forms['frgrm']['vCheck'].length;i++){
			   	      	if(document.forms['frgrm']['vCheck'][i].checked == true){
		 	   	      	document.forms['frgrm']['vChekeados'].value=1;
		 	   	      	i=document.forms['frgrm']['vCheck'].length;
		 	   	      }
			      }
			    }
    		}
	 		}
			function uAnula() {
				switch (document.forms['frgrm']['vRecords'].value) {
					case "1":
						if (document.forms['frgrm']['vCheck'].checked == true) {
							var zMatriz = document.forms['frgrm']['vCheck'].id.split('~');
								if (zMatriz[4]!= "IMPRESO") {
									if (confirm("Esta Seguro de Cambiar la Autorizacion para el Comprobante "+zMatriz[0]+"-"+zMatriz[1]+"-"+zMatriz[2])) {
										document.cookie="kModo=ANULAR";
  									document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
  									document.forms['frestado']['cComId'].value  = zMatriz[0];
  									document.forms['frestado']['cComCod'].value = zMatriz[1];
  									document.forms['frestado']['cComCsc'].value = zMatriz[2];
  									document.forms['frestado']['cComFec'].value = zMatriz[5];
										document.forms['frestado'].submit();
									}
								} else {
									alert("No se Puede Inactivar Causaciones Inactivas");
								}
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frgrm']['vCheck'].length;i++) {
							if (document.forms['frgrm']['vCheck'][i].checked == true && zSw_Prv == 0) {
								// Solo Deja Legalizar el Primero Seleccionado
								zSw_Prv = 1;
								var zMatriz = document.forms['frgrm']['vCheck'][i].id.split('~');
								if (zMatriz[4] != "IMPRESO") {
										if (confirm("Esta Seguro de Cambiar la Autorizacion para el Comprobante "+zMatriz[0]+"-"+zMatriz[1]+"-"+zMatriz[2])) {
											document.cookie="kModo=ANULAR";
    									document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
    									document.forms['frestado']['cComId'].value  = zMatriz[0];
      								document.forms['frestado']['cComCod'].value = zMatriz[1];
      								document.forms['frestado']['cComCsc'].value = zMatriz[2];
    									document.forms['frestado']['cComFec'].value = zMatriz[5];
											document.forms['frestado'].submit();
										}
							    
								} else {
									alert("El Periodo Contable del Comprobante ["+zMatriz[0]+"-"+zMatriz[1]+"-"+zMatriz[2]+"] no esta [ABIERTO], Verifique");
								}
							}
						}
					break;
				}
		 	}

			function uButtonsAscDes(xEvent,xSortField,xSortType) {
		    	var zSortType = "";
		    	switch (document.forms['frgrm']['vSortType'].value) {
		    		case "ASC_NUM":
		    			zSortType = "DESC_NUM";
		    		break;
		    		case "DESC_NUM":
		    			zSortType = "ASC_NUM";
		    		break;
		    		case "ASC_AZ":
		    			zSortType = "DESC_AZ";
		    		break;
		    		case "DESC_AZ":
		    			zSortType = "ASC_AZ";
		    		break;
		    	}
		    	switch (xEvent) {
		    		case "onclick":
							if (document.getElementById(xSortField).id != document.forms['frgrm']['vSortField'].value) {
								document.forms['frgrm']['vSortField'].value=xSortField;
								document.forms['frgrm']['vSortType'].value=xSortType;
								document.forms['frgrm'].submit();
							} else {
								document.forms['frgrm'].submit();
							}
		    		break;
		    		case "onmouseover":
							if(document.forms['frgrm']['vSortField'].value == xSortField) {
								if (document.forms['frgrm']['vSortType'].value == 'ASC_NUM' || document.forms['frgrm']['vSortType'].value == 'ASC_AZ') {
									document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
									document.forms['frgrm']['vSortField'].value = xSortField;
									document.forms['frgrm']['vSortType'].value = zSortType;
								} else {
									document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
									document.forms['frgrm']['vSortField'].value = xSortField;
									document.forms['frgrm']['vSortType'].value = zSortType;
								}
							} else {
								document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
							}
		    		break;
		    		case "onmouseout":
							if(document.forms['frgrm']['vSortField'].value == xSortField) {
								if (document.forms['frgrm']['vSortType'].value == 'ASC_NUM' || document.forms['frgrm']['vSortType'].value == 'ASC_AZ') {
									document.getElementById(xSortField).src='<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
									document.forms['frgrm']['vSortField'].value = xSortField;
									document.forms['frgrm']['vSortType'].value = zSortType;
								} else {
									document.getElementById(xSortField).src='<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
									document.forms['frgrm']['vSortField'].value = xSortField;
									document.forms['frgrm']['vSortType'].value = zSortType;
								}
							} else {
								document.getElementById(xSortField).src='<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
							}
		    		break;

		    	}
	     	  if(document.forms['frgrm']['vSortField'].value == xSortField) {
	       		if (document.forms['frgrm']['vSortType'].value == 'ASC_NUM' || document.forms['frgrm']['vSortType'].value == 'ASC_AZ') {
	       	  	document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
	       	    document.getElementById(xSortField).title = 'Ascendente';
	       	  } else {
	       	    document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
	       	    document.getElementById(xSortField).title = 'Descendente';
	       	  }
	       	}
		    }

	  	
  	</script>
  	
  	<style type="text/css">
      SELECT{ font-family: verdana; font-size: 10px; color:#2B547D; background-color:#D8E4F1;}
    </style>

  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
		<form name = "frestado" action = "fraurgra.php" method = "post" target="fmpro">
		  <input type = "hidden" name = "cComId"    value = "">
		  <input type = "hidden" name = "cComCod"   value = "">
		  <input type = "hidden" name = "cComCsc"   value = "">
		  <input type = "hidden" name = "cComCsc2"  value = "">
		  <input type = "hidden" name = "cComFec"   value = "">
		  <input type = "hidden" name = "dFecCre"  value = "">
		  
		</form>
		
		<form name = "frgrm" action = "fraurini.php" method = "post" target="fmwork">
		  <input type = "hidden" name = "vChekeados" value = "">
		  <input type = "hidden" name = "vDo"  value = "">
		  <input type = "hidden" name = "vComMemo"    value = "">
		  <input type = "hidden" name = "gTipSav" value = "">
		  <input type = "hidden" name = "vEstado"  value = "">
   		<input type = "hidden" name = "vRecords"   value = "">
   		<input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
   		<input type = "hidden" name = "vSortField" value = "<?php echo $vSortField ?>">
   		<input type = "hidden" name = "vSortType"  value = "<?php echo $vSortType ?>">
   		<input type = "hidden" name = "vTimes"     value = "<?php echo $vTimes ?>">

     
					<!-- Inicia Nivel de Procesos -->
   		<?php if (mysql_num_rows($xUsrMen) > 0) { 
   		?>
   		
   		  <center>
 	 				<table width="95%" cellspacing="0" cellpadding="0" border="0">
	  				<tr>
  						<td>
				    		<fieldset>
  	  		    		<legend>Proceso <?php echo $_COOKIE['kProDes'] ?></legend>
 	 			  	  		<center>
	       	  				<table cellspacing="0" width="100%">
	        	  	  		<?php
     			    		   		$y = 0;
     			    		   		/* Empiezo a Leer la sys00005 */
												while($mUsrMen = mysql_fetch_array($xUsrMen)) {
													if($y == 0 || $y % 5 == 0) {
				  	      					if ($y == 0) {?>
											  	  <tr>
													  <?php } else { ?>
												    </tr><tr>
												    <?php }
												  }
												  /* Busco de la sys00005 en la sys00006 */
												  $qUsrPer  = "SELECT * ";
												  $qUsrPer .= "FROM $cAlfa.sys00006 ";
												  $qUsrPer .= "WHERE ";
												  $qUsrPer .= "usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
												  $qUsrPer .= "modidxxx = \"{$mUsrMen['modidxxx']}\"  AND ";
												  $qUsrPer .= "proidxxx = \"{$mUsrMen['proidxxx']}\"  AND ";
												  $qUsrPer .= "menidxxx = \"{$mUsrMen['menidxxx']}\"  LIMIT 0,1";
												  $xUsrPer  = f_MySql("SELECT","",$qUsrPer,$xConexion01,"");
												  												  
												  if (mysql_num_rows($xUsrPer) > 0) { ?>
													  <td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory ?>/<?php echo $mUsrMen['menimgon'] ?>" style = "cursor:pointer" onClick ="javascript:f_Link('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')"><br>
				                    <a href = "javascript:f_Link('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')"
															style="color:<?php echo $vSysStr['system_link_menu_color'] ?>"><?php echo $mUsrMen['mendesxx'] ?></a></center></td>
													<?php	} else { ?>
														<td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory ?>/<?php echo $mUsrMen['menimgof']?>"><br>
   			    		          	<?php echo $mUsrMen['mendesxx'] ?></center></td>
													<?php }
													$y++;
												}
												
												/*para pintar solo 5 modulos por fila*/
												$celdas = "";
				      	  	  	$nf = intval($y/5);
				        	  	  $resto = $y-$nf;
					        	  	$restan = 5-$resto;
					          	  if ($restan > 0) {
		    			        		for ($i=0;$i<$restan;$i++) {
		        			      		$celdas.="<td width='20%'></td>";
				      	      		}
						    	        echo $celdas;
					  	    	    } ?>
   		      		  			</tr>
     		        		</table>
      		      	</center>
 		    		  	</fieldset>
         	  	</td>
          	</tr>
      		</table>
 	      </center>
 	    <?php } ?>
 	    <!-- Fin Nivel de Procesos -->
 	    <?php

				if ($vLimInf == "" && $vLimSup == "") {
					$vLimInf = "00";
          $vLimSup = $vSysStr['system_rows_page_ini'];
				}

				if ($vPaginas == "") {
        	$vPaginas = "1";
				}

				/***** Si Viene Vacio el $vCcoId lo Cargo con la Cookie del Centro de Costo *****/
				/***** Si no Hago el SELECT con el Centro de Costo que me Entrega el Combo del INI *****/
				if ($vCcoId == "") {
        	$vCcoId = $_COOKIE['kUsrCco'];
				} else {
					/***** Si el $vCcoId Viene Cargado del Combo con "ALL" es porque Debo Mostrar Todos los Centros de Costos *****/
					/***** Si no Dejo la Sucursal que Viene Cargada *****/
					if ($vCcoId == "ALL") {
						$vCcoId = "";
					}
				}

				/***** Si Viene Vacio el $vUsrId lo Cargo con la Cookie del Usuario *****/
				/***** Si no Hago el SELECT con el Usuario que me Entrega el Combo de INI *****/
				if ($vUsrId == "") {
        	$vUsrId = $_COOKIE['kUsrId'];
				} else {
					/***** Si el $vUsrId Viene Cargado del Combo con "ALL" es porque Debo Mostrar Todos los Usuarios *****/
					/***** Si no Dejo el Usuario que Viene Cargado *****/
					if ($vUsrId == "ALL") {
						$vUsrId = "";
					}
				}
				
				
				
				
				
				/**
				 * Realizo la consulta de los formularios legalizados o no.
				 */
				if($cPerAno == ""){
				  $cPerAno = date('Y');
				}
          
		   	$y=0;
				$zSqlCab  = "SELECT ";
				$zSqlCab .= "$cAlfa.fcoc$cPerAno.comidxxx, ";
				$zSqlCab .= "$cAlfa.fcoc$cPerAno.comcodxx, ";
				$zSqlCab .= "$cAlfa.fcoc$cPerAno.comcscxx, ";
				$zSqlCab .= "$cAlfa.fcoc$cPerAno.teridxxx, ";
				$zSqlCab .= "$cAlfa.fcoc$cPerAno.comfecxx ";
				$zSqlCab .= "FROM $cAlfa.fcoc$cPerAno ";
				$zSqlCab .= "WHERE ";
				$zSqlcab .= "regestxx = \"ACTIVO\" AND ";
				$zSqlCab .= "comprnxx = \"\" ";
				$zSqlCab .= "ORDER BY CONVERT(comcscxx,signed) ASC ";
				//f_Mensaje(__FILE__,__LINE__,$zSqlCab);
				$zCrsCab = mysql_query($zSqlCab,$xConexion01);
				//wMenssage(__FILE__,__LINE__,mysql_num_rows($zCrsCab));
				/* Cargo la Matriz con los ROWS del Cursor */
				$i=0;
				while ($zRCab = mysql_fetch_array($zCrsCab)) {
					$zMatrizTmp[$i] = $zRCab;
					$i++;
				}
				/* Fin de Cargo la Matriz con los ROWS del Cursor */
				/* Recorro la Matriz para Traer Datos Externos */


				/* Traigo el Nombre del Usuario de la matriz ya cargada, es decir que si no hay registros en la consulta
				anterior no trae descripcion de usuario, ni ninguna de las consultas siguientes tendran valores*/
				for ($i=0;$i<count($zMatrizTmp);$i++) {
				  //$zMatrizTmp[$i]['SERIDXXX'] = intval($zMatrizTmp[$i]['SERIDXXX']);

					/* Traigo el Nombre del Cliente del Do */
					$zSqlCli = "SELECT ";
					$zSqlCli .= "$cAlfa.SIAI0150.CLINOMXX ";
					$zSqlCli .= "FROM $cAlfa.SIAI0150 ";
					$zSqlCli .= "WHERE ";
					$zSqlCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$zMatrizTmp[$i]['teridxxx']}\" LIMIT 0,1";					  
					$zCrsCli = mysql_query($zSqlCli,$xConexion01);
					if (mysql_num_rows($zCrsCli) > 0) {
						while ($zRCli = mysql_fetch_array($zCrsCli)) {
							$zMatrizTmp[$i]['CLINOMXX'] = trim($zRCli['CLINOMXX']." ".$zRCli['CLIAPE1X']." ".$zRCli['CLIAPE2X']." ".$zRCli['CLINOM1X']." ".$zRCli['CLINOM2X']);
						}
					} else {
						$zMatrizTmp[$i]['CLINOMXX'] = "CLIENTE SIN NOMBRE";
					}

					/* Fin Traigo el Cliente del Do */

					/* Traigo la descripcion del tipo de formulario */
					$zSqlPto = "SELECT ";
					$zSqlPto .= "$cAlfa.fpar0116.ccodesxx ";
					$zSqlPto .= "FROM $cAlfa.fpar0116 ";
					$zSqlPto .= "WHERE ";
					$zSqlPto .= "$cAlfa.fpar0116.ccoidxxx = \"{$zMatrizTmp[$i]['ccoidxxx']}\" LIMIT 0,1";
					$zCrsPto = mysql_query($zSqlPto,$xConexion01);
					if (mysql_num_rows($zCrsPto) > 0) {
						while ($zRPto = mysql_fetch_array($zCrsPto)) {
							$zMatrizTmp[$i]['ccodesxx'] = $zRPto['ccodesxx'];
						}
					} else {
						$zMatrizTmp[$i]['ccodesxx'] = "SUCURSAL SIN NOMBRE";
					}
					/* Fin Traigo la descripcion del tipo de formulario */




				}

				/***** Extraigo el nombre del usuario *****/
				$zSqlNom = "SELECT ";
				$zSqlNom .= "$cAlfa.SIAI0003.USRNOMXX ";
				$zSqlNom .= "FROM $cAlfa.SIAI0003 ";
				$zSqlNom .= "WHERE ";
				$zSqlNom .= "$cAlfa.SIAI0003.USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
				$zCrsNom = mysql_query($zSqlNom,$xConexion01);
				$zRNom   = mysql_fetch_array($zCrsNom);
				$zNomUsr = $zRNom['USRNOMXX'];
				/***** Fin Extracción nombre del usuario General. *****/


				/***** Si el $vSearch Viene Cargado Busco el Patron en Todos los Campos de la Matriz *****/
				if ($vSearch != "") {
					$zMatrizTra = array();
					for ($i=0,$j=0;$i<count($zMatrizTmp);$i++) {
						$zArray = array_values($zMatrizTmp[$i]);
						for ($k=0;$k<count($zArray);$k++) {
							if (substr_count($zArray[$k],strtoupper($vSearch)) > 0) {
								$k = count($zArray)+1;
								$zMatrizTra[$j] = $zMatrizTmp[$i];
								$j++;
							}
						}
					}
				} else {
					$zMatrizTra = $zMatrizTmp;
				}
				/***** Fin de Buscar Patron en la Matriz *****/

				if ($vSortField != "" && $vSortType != "") {
					$zMatrizTra = f_Sort_Array_By_Field($zMatrizTra,$vSortField,$vSortType);
				}
				/* Fin de Recorro la Matriz para Traer Datos Externos */
			?>
      
      <center>
        <script language="javascript">
					document.forms['frgrm']['vRecords'].value = "<?php echo count($zMatrizTra) ?>";
				</script>
       	
       	
       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Autorizaciones Realizadas</legend>
     	       		<center>
     	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      	<tr>
         	      		<td class="clase08" width="18%">
            	      	<input type="text" class="letra" name = "vSearch" maxlength="20" value = "<?php echo $vSearch ?>" style= "width:80"
            	      		onblur="javascript:this.value=this.value.toUpperCase();
																					 document.forms['frgrm']['vLimInf'].value='00';
								      										 document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      										 document.forms['frgrm']['vPaginas'].value='1'								      	
								      										 document.forms['frgrm'].submit()">								      										
            	        	<select class="letra" name = "cPerAno" style="width:80">
            	        	  <?php for($i=$vSysStr['financiero_ano_instalacion_modulo'];$i<=date('Y');$i++){ ?>
            	        	    <option value="<?php  echo $i ?>"><?php echo $i ?></option>
            	        	  <?php  } ?>
            	        	</select>
            	        	 <script language="javascript">
                					document.forms['frgrm']['cPerAno'].value = "<?php  echo $cPerAno ?>";               					 
                				</script>
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:hand" title="Buscar"
								      		onClick = "javascript:document.forms['frgrm']['vSearch'].value=document.forms['frgrm']['vSearch'].value.toUpperCase();
								      											 		document.forms['frgrm']['vLimInf'].value='00';
								      												  document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												  document.forms['frgrm']['vPaginas'].value='1'
								      												  document.forms['frgrm'].submit()">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:hand" title="Mostrar Todo"
								      		onClick ="javascript:document.forms['frgrm']['vSearch'].value='';
								      												 document.forms['frgrm']['vLimInf'].value='00';
								      												 document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												 document.forms['frgrm']['vPaginas'].value='1';
								      												 document.forms['frgrm']['vSortField'].value='';
								      												 document.forms['frgrm']['vSortType'].value='';
								      												 document.forms['frgrm'].submit()">
   	              	  </td>
       	       				<td class="name" width="08%" align="left">Filas&nbsp;
       	       					<input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
								      		onfocus = "javascript:document.forms['frgrm']['vPaginas'].value='1'"
       	       						onblur = "javascript:uFixFloat(this);
								      												 document.forms['frgrm']['vLimInf'].value='00';
								      												 document.forms['frgrm'].submit()">
       	       				</td>
       	       				<td class="name" width="08%">
       	       					<?php if (ceil(count($zMatrizTra)/$vLimSup) > 1) { ?>
       	       						<?php if ($vPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:hand" title="Primera Pagina">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:hand" title="Pagina Anterior">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:hand" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:hand" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil(count($zMatrizTra)/$vLimSup) ?>';
								      				    						 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
       	       						<?php } ?>
       	       						<?php if ($vPaginas > "1" && $vPaginas < ceil(count($zMatrizTra)/$vLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='1';
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value--;
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:hand" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:hand" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil(count($zMatrizTra)/$vLimSup) ?>';
								      				    						 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
	       	       					<?php } ?>
       	       						<?php if ($vPaginas == ceil(count($zMatrizTra)/$vLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='1';
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value--;
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_nextpage.png" style = "cursor:hand" title="Pagina Siguiente">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_lastpage.png" style = "cursor:hand" title="Ultima Pagina">
	       	       					<?php } ?>
	       	       				<?php } else { ?>
	       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:hand" title="Primera Pagina">
	       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:hand" title="Pagina Anterior">
	       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_nextpage.png"  style = "cursor:hand" title="Pagina Siguiente">
	       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_lastpage.png"  style = "cursor:hand" title="Ultima Pagina">
	       	       				<?php } ?>
       	       				</td>
       	       				<td class="name" width="08%" align="left">Pag&nbsp;
												<select Class = "letrase" name = "vPaginas" value = "<?php echo $vPaginas ?>" style = "width:60%"
       	       						onchange="javascript:document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(this.value-1));
       	       						                     document.forms['frgrm'].submit()">
													<?php for ($i=0;$i<ceil(count($zMatrizTra)/$vLimSup);$i++) {
														if ($i+1 == $vPaginas) { ?>
															<option value = "<?php echo $i+1 ?>" selected><?php echo $i+1 ?></option>
														<?php } else { ?>
															<option value = "<?php echo $i+1 ?>"><?php echo $i+1 ?></option>
														<?php } ?>
													<?php } ?>
												</select>

       	       				</td>
			        		    <td Class="name" width="20%" align="right">
   	         	        	<?php
												  /***** Botones de Acceso Rapido *****/
												  $qBotAcc  = "SELECT sys00005.menopcxx ";
													$qBotAcc .= "FROM $cAlfa.sys00005,$cAlfa.sys00006 ";
													$qBotAcc .= "WHERE ";
													$qBotAcc .= "sys00006.usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
													$qBotAcc .= "sys00006.modidxxx = sys00005.modidxxx        AND ";
													$qBotAcc .= "sys00006.proidxxx = sys00005.proidxxx        AND ";
													$qBotAcc .= "sys00006.menidxxx = sys00005.menidxxx        AND ";
													$qBotAcc .= "sys00006.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
													$qBotAcc .= "sys00006.proidxxx = \"{$_COOKIE['kProId']}\" ";
													$qBotAcc .= "ORDER BY sys00005.menordxx";
													$xBotAcc  = f_MySql("SELECT","",$qBotAcc,$xConexion01,"");
													while ($xRBA = mysql_fetch_array($xBotAcc)) {
														switch ($xRBA['menopcxx']) {
															case "ANULAR": ?>
															  <!--
																<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_edit.png"
																  onClick = "javascript:uVerificarCheck();
																                        uCargaVariable(document.forms['frgrm']['vRecords'].value);uEditar();"
																                        style = "cursor:hand" title="Edita solo Uno" id="IdImg">
																<script languaje="javascript">
				                            if(document.forms['frgrm']['vRecords'].value ==0)
				                            {
				                              document.getElementById("IdImg").onclick="";
				                            }
			                          </script>
			                          -->
			                          
			                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_cambest.gif" name = "IdImg" onClick = "javascript:uVerificarCheck();
																																																				uCargaVariable(document.forms['frgrm']['vRecords'].value);
																																																				uAnula('ANULAR')"
																																																				style = "cursor:pointer" title="Anular Autorizacion, Solo Uno">	                          
			                         <!-- uCargaVariable(document.forms['frgrm']['vRecords'].value);
			                          												uAnula()";
																                        style = "cursor:hand" title="Autoriza solo Uno" id="IdImg" > -->
                                <script languaje="javascript">
				                          if(document.forms['frgrm']['vRecords'].value ==0){
				                            document.getElementById("IdImg").onclick="";
				                          }
			                          </script>
															<?php break;
														}
												  }
												  /***** Fin Botones de Acceso Rapido *****/
  	         	        	?>
             	        </td>

         	      		</tr>
         	      	</table>
         	      	<br>
       	     			
       	     			
       	     			<table cellspacing="0" width="100%">
         	         
         	         <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
           	          <td class="name" width="10%">
           	         		<a href = "javascript:uButtonsAscDes('onclick','comcscxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:uButtonsAscDes('onmouseover','comcscxx','')"
       	       					onmouseout="javascript:uButtonsAscDes('onmouseout','comcscxx','')">Factura</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comcscxx">
           	         		<script language="javascript">uButtonsAscDes('','comcscxx','')</script>
          	           </td>
         	            <td class="name" width="10%">
           	         		<a href = "javascript:uButtonsAscDes('onclick','teridxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:uButtonsAscDes('onmouseover','teridxxx','')"
       	       					onmouseout="javascript:uButtonsAscDes('onmouseout','teridxxx','')">Nit</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "teridxxx">
           	         		<script language="javascript">uButtonsAscDes('','teridxxx','')</script>
           	         	</td>
           	         	<td class="name" width="40%">
           	         		<a href = "javascript:uButtonsAscDes('onclick','CLINOMXX','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:uButtonsAscDes('onmouseover','CLINOMXX','')"
       	       					onmouseout="javascript:uButtonsAscDes('onmouseout','CLINOMXX','')">Cliente</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "CLINOMXX">
           	         		<script language="javascript">uButtonsAscDes('','CLINOMXX','')</script>
          	         	</td>
           	         	<td class="name" width="30%">
           	         		<a href = "javascript:uButtonsAscDes('onclick','ccodesxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:uButtonsAscDes('onmouseover','ccodesxx','')"
       	       					onmouseout="javascript:uButtonsAscDes('onmouseout','ccodesxx','')">Centro de Costo</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ccodesxx">
           	         		<script language="javascript">uButtonsAscDes('','ccodesxx','')</script>
          	          </td>
             	        <td class="name" width="10%" align="right">
           	         		<input type="checkbox" name="vCheckAll" onClick = 'javascript:uMarca()'>
           	         	</td>
             	      </tr>
         	         
             	      <tr bgcolor = 'white'>
           	         	<td class="name" width="10%">&nbsp;
          	         	</td>
             	        <td class="name" width="10%">&nbsp;
           	         	</td>
           	         	<td class="name" width="40%">&nbsp;
          	         	</td>
           	         	<td class="name" width="30%">&nbsp;
           	         	</td>
             	        <td class="name" width="10%" align="right">&nbsp;
           	         	</td>
             	     </tr>
					         
					         
					         <script languaje="javascript">
						          document.forms['frgrm']['vRecords'].value = "<?php echo count($zMatrizTra) ?>";
						       </script>
 	                    <?php for($i=intval($vLimInf);$i<intval($vLimInf+$vLimSup);$i++) {
 	                    	if ($i < count($zMatrizTra)) { // Para Controlar el Error
	 	                    	$zColor = "{$vSysStr['system_row_impar_color_ini']}";
	                   	    if($y % 2 == 0) {
	                   	    	$zColor = "{$vSysStr['system_row_par_color_ini']}";
													} ?>
								        <!--<tr bgcolor = "<?php echo $zColor ?>">-->
								        <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
		                      	<td class="letra7" width="10%"><?php echo $zMatrizTra[$i]['comidxxx'].'-'.$zMatrizTra[$i]['comcodxx'].'-'.$zMatrizTra[$i]['comcscxx'] ?></a></td>
								            <td class="letra7" width="10%"><?php echo substr($zMatrizTra[$i]['teridxxx'],0,14);?></td>
	       	                	<td class="letra7" width="40%"><?php echo $zMatrizTra[$i]['CLINOMXX'] ?></td>
								            <td class="letra7" width="30%"><?php echo substr($zMatrizTra[$i]['ccodesxx'],0,35) ?></td>
	        	              	<td class="letra7" width="10%" align="right">
	        	              	  <input type="checkbox" name="vCheck"
													       value = "<?php echo $zMatrizTra[$i]['comidxxx'].'~'.$zMatrizTra[$i]['comcodxx'].'~'.$zMatrizTra[$i]['comcscxx'].'~'.$zMatrizTra[$i]['comcsc2x'].'~'.$zMatrizTra[$i]['comprnxx'].'~'.$zMatrizTra[$i]['comfecxx'] ?>"
													       id = "<?php echo $zMatrizTra[$i]['comidxxx'].'~'.$zMatrizTra[$i]['comcodxx'].'~'.$zMatrizTra[$i]['comcscxx'].'~'.$zMatrizTra[$i]['comcsc2x'].'~'.$zMatrizTra[$i]['comprnxx'].'~'.$zMatrizTra[$i]['comfecxx'] ?>">
	        	              	</td>
	        	        		</tr>
	                	    	<?php $y++;
 	                    	}
 	                    }
 	                    ?>
					         
					         
 	                </table>
                </center>
   	          </fieldset>
     	      </td>
          </tr>
        </table>  
      </center>
    </form>
	</body>
</html>
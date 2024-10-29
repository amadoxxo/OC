<?php
  namespace openComex;
 /**
  * Tracking Documentos Borrados.
  * Este programa permite realizar consultas rapidas de los Comprobantes borrados.
  * @author Johana Arboleda Ramos <johana.arboleda@opentecnologia.com.co>
  * @package openComex
  */

	include("../../../../libs/php/utility.php");

	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
	$kMysqlDb = $kDf[3];

	/* Busco en la 05 que Tiene Permiso el Usuario*/
  $qUsrMen  = "SELECT * ";
  $qUsrMen .= "FROM $cAlfa.sys00005 ";
  $qUsrMen .= "WHERE ";
  $qUsrMen .= "sys00005.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
  $qUsrMen .= "sys00005.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
  $qUsrMen .= "sys00005.menimgon != '' ";
  $qUsrMen .= "ORDER BY sys00005.menordxx";
  $xUsrMen  = f_MySql("SELECT","",$qUsrMen,$xConexion01,"");
  
 ?>

<html>
	<head>
  	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
   	<script language="javascript">

    	function f_Ver(xComId,xComCod,xComCsc,xComCsc2,xComFec) {
      	var cPathUrl = "frrecnue.php?gComId="+xComId+'&gComCod='+xComCod+'&gComCsc='+xComCsc+'&gComCsc2='+xComCsc2+'&gComFec='+xComFec;
      	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      	document.cookie="kMenDes=Ver Recibo;path="+"/";
      	document.cookie="kModo=VER;path="+"/";
      	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	document.location = cPathUrl; // Invoco el menu.
	    }

			function f_Recuperar(xModo) {
				switch (document.forms['frgrm']['vRecords'].value){
					case "1":
						if (document.forms['frgrm']['oChkCom'].checked == true) {
							var cLogId = document.forms['frgrm']['oChkCom'].id.split("~");
							var cCsc = (cLogId[5] != "") ? cLogId[5] : cLogId[4];
							var xMensaje  = "Esta Seguro de Recuperar el Comprobante "+cLogId[1]+"-"+cLogId[2]+"-"+cLogId[3]+"-"+cCsc+"?";
							if (confirm(xMensaje)) {
								document.cookie="kModo="+xModo+";path="+"/";
								document.forms['frestado']['cLogId'].value    = cLogId[0];
								document.forms['frestado'].submit();
							}
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
							if (document.forms['frgrm']['oChkCom'][i].checked == true && zSw_Prv == 0) {
								zSw_Prv = 1;
								var cLogId = document.forms['frgrm']['oChkCom'][i].id.split("~");
								var xMensaje  = "Esta Seguro de Recuperar el Comprobante "+cLogId[1]+"-"+cLogId[2]+"-"+cLogId[3]+"-"+cLogId[4]+"?";
								if (confirm(xMensaje)) {
									document.cookie="kModo="+xModo+";path="+"/";
									document.forms['frestado']['cLogId'].value  = cLogId[0];
									document.forms['frestado'].submit();
								}
							}
						}
					break;
				}
	    }

     	function f_Link(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
      	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes="+xMenDes+";path="+"/";
      	document.cookie="kModo="+xOpcion+";path="+"/";
      	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	document.location = xForm; // Invoco el menu.
      }

       function f_Marca() {
      	if (document.forms['frgrm']['oChkComAll'].checked == true){
      	  if (document.forms['frgrm']['vRecords'].value == 1){
      	  	document.forms['frgrm']['oChkCom'].checked=true;
      	  } else {
	      		if (document.forms['frgrm']['vRecords'].value > 1){
			      	for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
   	   	      	document.forms['frgrm']['oChkCom'][i].checked = true;
			      	}
			      }
      	  }
      	} else {
	      	if (document.forms['frgrm']['vRecords'].value == 1){
      	  	document.forms['frgrm']['oChkCom'].checked=false;
      	  } else {
      	  	if (document.forms['frgrm']['vRecords'].value > 1){
				      for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
				      	document.forms['frgrm']['oChkCom'][i].checked = false;
				      }
      	  	}
 	  	   	}
	      }
	 		}

	 		/************************ FUNCION PARA GUARDAR EL ORDEN DEL ORDER BY DEL SQL ***********************/
	 		function f_Order_By(xEvento,xCampo) {
  	 		//alert(document.forms['frgrm'][xCampo].value);
  			if (document.forms['frgrm'][xCampo].value != '') {
  				var vSwitch = document.forms['frgrm'][xCampo].value.split(' ');
  				var cSwitch = vSwitch[1];
  			} else {
  				var cSwitch = '';
  			}
  			//alert(cSwitch);
  			if (xEvento == 'onclick') {
    			switch (cSwitch) {
    				case '':
    					document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id+' ASC,';
    					document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
    					if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
   					    document.forms['frgrm']['cOrderByOrder'].value += xCampo+"~";
    					}
    				break;
    				case 'ASC,':
    					document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id+' DESC,';
    					document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
    					if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
    					  document.forms['frgrm']['cOrderByOrder'].value += xCampo+"~";
    					}
    				break;
    				case 'DESC,':
    					document.forms['frgrm'][xCampo].value = '';
    					document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
    					if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) >= 0) {
    					  document.forms['frgrm']['cOrderByOrder'].value = document.forms['frgrm']['cOrderByOrder'].value.replace(xCampo,"");
    					}
    				break;
    			}
  			} else {
  			  switch (cSwitch) {
    				case '':
    				  document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
    				break;
    				case 'ASC,':
    				  document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
    				break;
    				case 'DESC,':
    				  document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
    				break;
    			}
  			}
	 		}	 		
  	</script>
  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">

    <form name = "frestado" action = "frrdbgra.php" method = "post" target="fmpro">
  	  <input type = "hidden" name = "cEstado"  value = "">
  		<input type = "hidden" name = "cComId"   value = "">
  		<input type = "hidden" name = "cComCod"  value = "">
  		<input type = "hidden" name = "cComCsc"  value = "">
  		<input type = "hidden" name = "cComCsc2" value = "">
  		<input type = "hidden" name = "cComCsc3" value = "">
  		<input type = "hidden" name = "cLogId"   value = "">
  	</form>

    <form name = "frgrm" action = "frrdbini.php" method = "post" target="fmwork">
   		<input type = "hidden" name = "vRecords"   value = "">
   		<input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
   		<input type = "hidden" name = "vSortField" value = "<?php echo $vSortField ?>">
   		<input type = "hidden" name = "vSortType"  value = "<?php echo $vSortType ?>">
   		<input type = "hidden" name = "vTimes"     value = "<?php echo $vTimes ?>">
   		<input type = "hidden" name = "vTimesSave" value = "0">
   		<input type = "hidden" name = "vBuscar"    value = "<?php echo $_POST['vBuscar'] ?>">
   		<input type = "hidden" name = "cOrderByOrder"  value = "<?php echo $_POST['cOrderByOrder'] ?>" style = "width:1000">


   		<!-- Inicia Nivel de Procesos -->
   		<?php if (mysql_num_rows($xUsrMen) > 0) { ?>
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
			} elseif ($vLimInf == "") {
				$vLimInf = "00";
			}

			if ($vPaginas == "") {
      	$vPaginas = "1";
			}
				
			/**INICIO SQL**/
			if ($_POST['cPeriodos'] == "") {
				$_POST['cPeriodos'] == "20";
				$_POST['dDesde'] = substr(date('Y-m-d'),0,8)."01";
				$_POST['dHasta'] = date('Y-m-d');
			}
			
			if ($_POST['vSearch'] != "") {
				/**
				 * Buscando los id que corresponden a las busquedas de los lefjoin
				 */
				$qUsrBor  = "SELECT ";
        $qUsrBor .= "USRIDXXX ";
        $qUsrBor .= "FROM $cAlfa.SIAI0003 ";
        $qUsrBor .= "WHERE IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") LIKE \"%{$_POST['vSearch']}%\" ";
        $xUsrBor = f_MySql("SELECT","",$qUsrBor,$xConexion01,"");
        $cUsrSearch = "";
        while ($xRUN = mysql_fetch_array($xUsrBor)) {
        	$cUsrSearch .= "\"{$xRUN['USRIDXXX']}\",";
        }
        $cUsrSearch = substr($cUsrSearch,0,strlen($cUsrSearch)-1);
      }
				
			$mCabMov = array(); $nCanPorRev = 0;
				
			$qCabMov  = "(SELECT DISTINCT ";
			$qCabMov .= "SQL_CALC_FOUND_ROWS  * ";
			if (substr_count($cOrderByOrder,"usrnomxx") > 0) {
       	$qCabMov .= ", IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS usrnomxx ";
      }
			if (substr_count($cOrderByOrder,"usrnomre") > 0) {
       	$qCabMov .= ", IF(A.USRNOMXX != \"\",A.USRNOMXX,\"USUARIO SIN NOMBRE\") AS usrnomre ";
      } 
      $qCabMov .= "FROM $cAlfa.fclogxxx ";
			if (substr_count($cOrderByOrder,"usrnomxx") > 0) {
       	$qCabMov .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.fclogxxx.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
      }
			if (substr_count($cOrderByOrder,"usrnomre") > 0) {
       	$qCabMov .= "LEFT JOIN $cAlfa.SIAI0003 AS A ON $cAlfa.fclogxxx.logusrre = $cAlfa.SIAI0003.USRIDXXX ";
      }
			$qCabMov .= "WHERE ";
			if ($_POST['vSearch'] != "") {
				$qCabMov .= "(";
				$qCabMov .= "$cAlfa.fclogxxx.comcodxx LIKE \"%{$_POST['vSearch']}%\" OR ";
				$qCabMov .= "$cAlfa.fclogxxx.comcscxx LIKE \"%{$_POST['vSearch']}%\" OR ";
				$qCabMov .= "$cAlfa.fclogxxx.comcsc2x LIKE \"%{$_POST['vSearch']}%\" OR ";
				$qCabMov .= "$cAlfa.fclogxxx.comcsc3x LIKE \"%{$_POST['vSearch']}%\" OR ";
	      $qCabMov .= "$cAlfa.fclogxxx.comfecxx LIKE \"%{$_POST['vSearch']}%\" OR ";
	      $qCabMov .= "$cAlfa.fclogxxx.regfcrex LIKE \"%{$_POST['vSearch']}%\" OR ";
				if ($cUsrSearch != "") {
	      	$qCabMov .= "$cAlfa.fclogxxx.regusrxx IN ($cUsrSearch) OR ";
	      }
	      $qCabMov .= "$cAlfa.fclogxxx.regestxx LIKE \"%{$_POST['vSearch']}%\") AND ";
			}					
			$qCabMov .= "$cAlfa.fclogxxx.comfecxx BETWEEN \"{$_POST['dDesde']}\" AND \"{$_POST['dHasta']}\") ";
	    /***** FIN SQL *****/
			//f_mensaje(__FILE__,__LINE__,$qCabMov);
	    // CODIGO NUEVO PARA ORDER BY
	    $cOrderBy = "";
	    $vOrderByOrder = explode("~",$cOrderByOrder);
	    for ($z=0;$z<count($vOrderByOrder);$z++) {
	    	if ($vOrderByOrder[$z] != "") {
	      	if ($_POST[$vOrderByOrder[$z]] != "") {
	         	if (substr_count($_POST[$vOrderByOrder[$z]], "comidxxx") > 0) {
	          	//Ordena por comidxxx, comcodxx, comcscxx, comcsc2x
	          	$cOrdComId = str_replace("comidxxx", "CONCAT(comidxxx,\"-\",comcodxx,\"-\",comcscxx,\"-\",comcsc2x)", $_POST[$vOrderByOrder[$z]]);
	          	$cOrderBy .= $cOrdComId;	          		
	          } else {
	          	$cOrderBy .= $_POST[$vOrderByOrder[$z]];
	          }
	        }
	      }
	    }
				
	    if (strlen($cOrderBy)>0) {
	    	$cOrderBy = substr($cOrderBy,0,strlen($cOrderBy)-1);
	      $cOrderBy = "ORDER BY ".$cOrderBy;
	     } else {
			 	$cOrderBy = "ORDER BY regfcrex DESC,reghcrex  DESC";
	    }
	    //// FIN CODIGO NUEVO PARA ORDER BY
	      
	    $qCabMov .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
			$cIdCountRow = mt_rand(1000000000, 9999999999);
			$xCabMov = mysql_query($qCabMov, $xConexion01, true, $cIdCountRow);
			//f_Mensaje(__FILE__,__LINE__,$qCabMov."~".mysql_num_rows($xCabMov));
					
			$xNumRows = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD", $xConexion01, false);
			$xRNR     = mysql_fetch_array($xNumRows);
			$nRNR     = $xRNR['CANTIDAD'];
					
			while ($xRCC = mysql_fetch_array($xCabMov)) {
				//Busando Nombre del usuario
        if (substr_count($cOrderByOrder,"usrnomxx") == 0) {
        	$qUsrBor  = "SELECT ";
          $qUsrBor .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
          $qUsrBor .= "FROM $cAlfa.SIAI0003 ";
          $qUsrBor .= "WHERE $cAlfa.SIAI0003.USRIDXXX = \"{$xRCC['regusrxx']}\" LIMIT 0,1 ";
          $xUsrBor = f_MySql("SELECT","",$qUsrBor,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qUsrBor."~".mysql_num_rows($xUsrBor));
          if (mysql_num_rows($xUsrBor) > 0) {
          	$xRUB = mysql_fetch_array($xUsrBor);
          	$xRCC['usrnomxx'] = $xRUB['USRNOMXX'];
          } 
        }
				
				if (substr_count($cOrderByOrder,"usrnomre") == 0) {
        	$qUsrRec  = "SELECT ";
          $qUsrRec .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
          $qUsrRec .= "FROM $cAlfa.SIAI0003 ";
          $qUsrRec .= "WHERE $cAlfa.SIAI0003.USRIDXXX = \"{$xRCC['logusrre']}\" LIMIT 0,1 ";
          $xUsrRec = f_MySql("SELECT","",$qUsrRec,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qUsrRec."~".mysql_num_rows($xUsrRec));
          if (mysql_num_rows($xUsrRec) > 0) {
          	$xRUC = mysql_fetch_array($xUsrRec);
          	$xRCC['usrnomre'] = $xRUC['USRNOMXX'];
          } 
        }
        $mCabMov[count($mCabMov)] = $xRCC; 
			} ?>
      <center>
       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td><!--Inicio Codigo nuevo-->
					<fieldset>
		        <legend>Registros en la Consulta (<?php echo $nRNR ?>)</legend>
	         	<center>
 	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
   	      		<tr>
     	        	<td class="clase08" width="11%" align="left">
      	        	<input type="text" class="letra" name = "vSearch" maxlength="20" value = "<?php echo $vSearch ?>" style= "width:80"
      	        		onblur="javascript:this.value=this.value.toUpperCase();
																			 document.frgrm.vLimInf.value='00'; ">
        	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:pointer" title="Buscar"
					      		onClick = "javascript:document.forms['frgrm']['vBuscar'].value = 'ON';
																			    document.frgrm.vSearch.value=document.frgrm.vSearch.value.toUpperCase();
																			    if ((document.forms['frgrm']['dHasta'].value < document.forms['frgrm']['dDesde'].value) ||
    																			    		document.forms['frgrm']['dDesde'].value == '' || document.forms['frgrm']['dHasta'].value == '') {
    																	    	alert('El Sistema no Puede Hacer la Busqueda por Error en las Fechas del Periodo a Buscar, Verifique.');
    																	    } else {
    			      												  	if (document.forms['frgrm']['vPaginas'].id == 'ON') {
    			      												  	  document.forms['frgrm']['vPaginas'].id = 'OFF'
    			      												  	} else {
    			      												  	  document.forms['frgrm']['vPaginas'].value='1';
    			      												  	};    								
    			      												  	document.forms['frgrm']['vLimInf'].value='00';											    
    																	    	document.forms['frgrm'].submit();
    																	    };">
        	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:hand" title="Mostrar Todo"
					      		onClick ="javascript:document.forms['frgrm']['vSearch'].value='';
					      												 document.forms['frgrm']['vLimInf'].value='00';
					      												 document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
					      												 document.forms['frgrm']['vPaginas'].value='1';
					      												 document.forms['frgrm']['vSortField'].value='';
					      												 document.forms['frgrm']['vSortType'].value='';
					      												 document.forms['frgrm']['vTimes'].value='';
					      												 document.forms['frgrm']['dDesde'].value='<?php echo substr(date('Y-m-d'),0,8)."01";  ?>';
    					      										 document.forms['frgrm']['dHasta'].value='<?php echo date('Y-m-d');  ?>';
					      												 document.forms['frgrm']['vBuscar'].value='';
					      												 document.forms['frgrm']['cPeriodos'].value='20';
					      												 document.forms['frgrm'].submit()">
              	</td>
 	       				<td class="name" width="03%" align="left">Filas&nbsp;
 	       					<input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
					      		onblur = "javascript:uFixFloat(this);
					      												 document.frgrm.vLimInf.value='00'; ">
 	       				</td>
 	       				<td class="name" width="06%" align="center">
 	       					<?php if (ceil($nRNR/$vLimSup) > 1) { ?>
 	       						<?php if ($vPaginas == "1") { ?>
											<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
     	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
     	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:pointer" title="Pagina Siguiente"
 	       								onClick = "javascript:document.frgrm.vPaginas.value++;
					      												 			document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
					      												 			document.frgrm.submit()">
     	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:pointer" title="Ultima Pagina"
 	       								onClick = "javascript:document.frgrm.vPaginas.value='<?php echo ceil($nRNR/$vLimSup) ?>';
					      				    						 			document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
					      												 			document.frgrm.submit()">
 	       						<?php } ?>
 	       						<?php if ($vPaginas > "1" && $vPaginas < ceil($nRNR/$vLimSup)) { ?>
     	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
 	       								onClick = "javascript:document.frgrm.vPaginas.value='1';
					      												 			document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
					      												 			document.frgrm.submit()">
     	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
 	       								onClick = "javascript:document.frgrm.vPaginas.value--;
					      												 			document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
					      												 			document.frgrm.submit()">
     	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente"
 	       								onClick = "javascript:document.frgrm.vPaginas.value++;
					      												 			document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
					      												 			document.frgrm.submit()">
     	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina"
 	       								onClick = "javascript:document.frgrm.vPaginas.value='<?php echo ceil($nRNR/$vLimSup) ?>';
					      				    						 			document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
					      												 			document.frgrm.submit()">
   	       					<?php } ?>
 	       						<?php if ($vPaginas == ceil($nRNR/$vLimSup)) { ?>
     	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
 	       								onClick = "javascript:document.frgrm.vPaginas.value='1';
					      												 			document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
					      												 			document.frgrm.submit()">
     	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
 	       								onClick = "javascript:document.frgrm.vPaginas.value--;
					      												 			document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
					      												 			document.frgrm.submit()">
     	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_nextpage.png" style = "cursor:pointer" title="Pagina Siguiente">
     	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_lastpage.png" style = "cursor:pointer" title="Ultima Pagina">
   	       					<?php } ?>
   	       				<?php } else { ?>
   	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
   	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
   	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente">
   	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina">
   	       				<?php } ?>
 	       				</td>
 	       				<td class="name" width="08%" align="center">Pag&nbsp;
									<select Class = "letrase" name = "vPaginas" value = "<?php echo $vPaginas ?>" style = "width:60%"
 	       						onchange="javascript:this.id = 'ON'; // Cambio 18, Incluir este Codigo.
					      												 document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(this.value-1)); 
					      												 document.frgrm.submit();">
										<?php for ($i=0;$i<ceil($nRNR/$vLimSup);$i++) {
											if ($i+1 == $vPaginas) { ?>
												<option value = "<?php echo $i+1 ?>" selected><?php echo $i+1 ?></option>
											<?php } else { ?>
												<option value = "<?php echo $i+1 ?>"><?php echo $i+1 ?></option>
											<?php } ?>
										<?php } ?>
									</select>
 	       				</td>
 	       				<td class="name" width="14%" align="center" >
      	       	  <select class="letrase" size="1" name="cPeriodos" style = "width:100%" value = "<?php echo $_POST['cPeriodos'] ?>"
      	       	    onChange = "javascript:
      	       	    						parent.fmpro.location='<?php echo $cSystem_Libs_Php_Directory ?>/utilfepe.php?gTipo='+this.value+'&gForm='+'frgrm'+'&gFecIni='+'dDesde'+'&gFecFin='+'dHasta';
      	       	    						if (document.forms['frgrm']['cPeriodos'].value == '99') {
																	document.forms['frgrm']['dDesde'].readOnly = false;
																	document.forms['frgrm']['dHasta'].readOnly = false;
																} else {
																	document.forms['frgrm']['dDesde'].readOnly = true;
																	document.forms['frgrm']['dHasta'].readOnly = true;
																}">
      							 <option value = "10">Hoy</option>
      						   <option value = "15">Esta Semana</option>
      						   <option value = "20">Este Mes</option>
      						   <option value = "25">Este A&ntilde;o</option>
      						   <option value = "30">Ayer</option>
      						   <option value = "35">Semana Pasada</option>
      					     <option value = "40">Semana Pasada Hasta Hoy</option>
      						   <option value = "45">Mes Pasado</option>
      					     <option value = "50">Mes Pasado Hasta Hoy</option>
      						   <option value = "55">Ultimos Tres Meses</option>
      						   <option value = "60">Ultimos Seis Meses</option>
      					     <option value = "65">Ultimo A&ntilde;o</option>
      						   <option value = "99">Periodo Especifico</option>
      						</select>
      						<script language = "javascript">
      						  if ("<?php echo $_POST['cPeriodos'] ?>" == "") {
      						    document.forms['frgrm']['cPeriodos'].value = "20";
      						  } else {
      						    document.forms['frgrm']['cPeriodos'].value = "<?php echo $_POST['cPeriodos'] ?>";
      						  }
								  </script>
 	       				</td>
 	       				<td class="name" width="08%" align="center">
 	       					<input type = "text" Class = "letra" style = "width:70%;text-align:center" name = "dDesde" value = "<?php
 	       					if($_POST['dDesde']=="" && $_POST['cPeriodos'] == ""){
 	       					  echo substr(date('Y-m-d'),0,8)."01";
 	       					} else{
 	       					  echo $_POST['dDesde'];
 	       					} ?>"
 	       						onblur="javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));" readonly>
 	       				</td>
 	       				<td class="name" width="08%" align="center">
 	       					<input type = "text" Class = "letra" style = "width:70%;text-align:center" name = "dHasta" value = "<?php
 	       					  if($_POST['dHasta']=="" && $_POST['cPeriodos'] == ""){
 	       					    echo date('Y-m-d');
 	       					  } else{
 	       					    echo $_POST['dHasta'];
 	       					  }  ?>"
 	       				    onblur = "javascript:document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1)); " readonly>
 	       				</td>
 	       				<script language = "javascript">
            		  if (document.forms['frgrm']['cPeriodos'].value == "99") {
										document.forms['frgrm']['dDesde'].readOnly = false;
										document.forms['frgrm']['dHasta'].readOnly = false;
									} else {
										document.forms['frgrm']['dDesde'].readOnly = true;
										document.forms['frgrm']['dHasta'].readOnly = true;
									}
      				  </script>
								<td class="name" width="10%" align="center">
 	       				</td>
								<td Class="name" align="right">&nbsp;
   	         	  	<?php
									  /***** Botones de Acceso Rapido *****/
										$qBotAcc  = "SELECT * ";
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

										while ($mBotAcc = mysql_fetch_array($xBotAcc)) {
											switch ($mBotAcc['menopcxx']) {
												case "RECUPERAR": ?>
													<img src = "<?php echo $cPlesk_Skin_Directory ?>/ok.gif" onClick = "javascript:f_Recuperar('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
												<?php break;
												case "CAMBIAESTADO": ?>
													<img src = "<?php echo $cPlesk_Skin_Directory ?>/failed.jpg" onClick = "javascript:f_Cambia_Estado('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
												<?php break;
														}
												  }
												  /***** Fin Botones de Acceso Rapido *****/
  	         	        	?>
             	        </td>
	       	         	</tr>
 	     	         	</table>
 	   	         	</center>
   	         		<hr></hr>
 								<center>
       	     			<table cellspacing="0" width="100%">
         	         	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
         	         		<td class="name" width="12%">
           	         		<a href = "javascript:f_Order_By('onclick','comidxxx');" title="Ordenar">Comprobante</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comidxxx">
           	         		<input type = "hidden" name = "comidxxx" value = "<?php echo $_POST['comidxxx'] ?>" id = "comidxxx">
           	         		<script language="javascript">f_Order_By('','comidxxx')</script>
           	         	</td>
                      <td class="name" width="08%">
           	         		<a href = "javascript:f_Order_By('onclick','logtipxx');" title="Ordenar">M&oacute;dulo</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "logtipxx">
           	         		<input type = "hidden" name = "logtipxx" value = "<?php echo $_POST['logtipxx'] ?>" id = "logtipxx">
           	         		<script language="javascript">f_Order_By('','logtipxx')</script>
           	         	</td>
           	         	<td class="name" width="08%">
           	         		<a href = "javascript:f_Order_By('onclick','comfecxx');" title="Ordenar">Fecha</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comfecxx">
           	         		<input type = "hidden" name = "comfecxx" value = "<?php echo $_POST['comfecxx'] ?>" id = "comfecxx">
           	         		<script language="javascript">f_Order_By('','comfecxx')</script>
           	         	</td>
           	         	<td class="name" width="10%">
           	         		<a href = "javascript:f_Order_By('onclick','regusrxx');" title="Ordenar">Id</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "USRNOMXX">
           	         		<input type = "hidden" name = "regusrxx" value = "<?php echo $_POST['regusrxx'] ?>" id = "regusrxx">
           	         		<script language="javascript">f_Order_By('','regusrxx')</script>
           	         	</td>
           	         	<td class="name" width="13%">
           	         		<a href = "javascript:f_Order_By('onclick','usrnomxx');" title="Ordenar">Usuario que Borro</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "USRNOMXX">
           	         		<input type = "hidden" name = "usrnomxx" value = "<?php echo $_POST['usrnomxx'] ?>" id = "usrnomxx">
           	         		<script language="javascript">f_Order_By('','usrnomxx')</script>
           	         	</td>
           	         	<td class="name" width="10%">
           	         		<a href = "javascript:f_Order_By('onclick','logusrre');" title="Ordenar">Id</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "USRNOMXX">
           	         		<input type = "hidden" name = "logusrre" value = "<?php echo $_POST['logusrre'] ?>" id = "logusrre">
           	         		<script language="javascript">f_Order_By('','logusrre')</script>
           	         	</td>
           	         	<td class="name" width="13%">
           	         		<a href = "javascript:f_Order_By('onclick','usrnomre');" title="Ordenar">Usuario que Recupero</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "USRNOMXX">
           	         		<input type = "hidden" name = "usrnomre" value = "<?php echo $_POST['usrnomre'] ?>" id = "usrnomre">
           	         		<script language="javascript">f_Order_By('','usrnomre')</script>
           	         	</td>
           	         	<td class="name" width="08%">
           	         		<a href = "javascript:f_Order_By('onclick','regfcrex');" title="Ordenar">Borrado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfcrex">
           	         		<input type = "hidden" name = "regfcrex" value = "<?php echo $_POST['regfcrex'] ?>" id = "regfcrex">
           	         		<script language="javascript">f_Order_By('','regfcrex')</script>
           	         	</td>
           	         	<td class="name" width="08%">
           	         		<a href = "javascript:f_Order_By('onclick','reghcrex');" title="Ordenar">Hora</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghcrex">
           	         		<input type = "hidden" name = "reghcrex" value = "<?php echo $_POST['reghcrex'] ?>" id = "reghcrex">
           	         		<script language="javascript">f_Order_By('','reghcrex')</script>
           	         	</td>
           	         	<td class="name" width="08%">
           	         		<a href = "javascript:f_Order_By('onclick','regestxx');" title="Ordenar">Estado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
           	         		<input type = "hidden" name = "regestxx" value = "<?php echo $_POST['regestxx'] ?>" id = "regestxx">
           	         		<script language="javascript">f_Order_By('','regestxx')</script>
           	         	</td>
                 	    <td Class='name' width="02%" align="right">
                 	    	<input type="checkbox" name="oChkComAll" onClick = 'javascript:f_Marca()'>
                 	    </td>
                 		</tr>
								   	<script languaje="javascript">
											document.forms['frgrm']['vRecords'].value = "<?php echo count($mCabMov) ?>";
										</script>
 	                 	<?php $y = 0;

 	                  for ($i=0;$i<count($mCabMov);$i++) {

 	                  	if ($y <= count($mCabMov)) { // Para Controlar el Error
	 	                  	$zColor = "{$vSysStr['system_row_impar_color_ini']}";
	                   	  if($y % 2 == 0) {
	                   	  	$zColor = "{$vSysStr['system_row_par_color_ini']}";
												} ?>
											  <tr id="<?php echo $mCabMov[$i]['comidxxx'].'-'.$mCabMov[$i]['comcodxx'].'-'.$mCabMov[$i]['comcscxx'].'-'.$mCabMov[$i]['comcsc2x'] ?>" bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
											  	<td class="letra7"><?php echo $mCabMov[$i]['comidxxx'].'-'.$mCabMov[$i]['comcodxx'].'-'.$mCabMov[$i]['comcscxx'].'-'.(($mCabMov[$i]['comcsc3x'] != "") ? $mCabMov[$i]['comcsc3x'] : $mCabMov[$i]['comcsc2x']) ?></td>
                          <td class="letra7"><?php echo (($mCabMov[$i]['logtipxx'] == "DS") ? "DOCUMENTO SOPORTE" : "CONTABLE") ?></td>
	       	                <td class="letra7"><?php echo $mCabMov[$i]['comfecxx'] ?></td>
	                      	<td class="letra7"><?php echo $mCabMov[$i]['regusrxx'] ?></td>
	                      	<td class="letra7"><?php echo $mCabMov[$i]['usrnomxx'] ?></td>
	                      	<td class="letra7"><?php echo $mCabMov[$i]['logusrre'] ?></td>
	                      	<td class="letra7"><?php echo $mCabMov[$i]['usrnomre'] ?></td>
	                      	<td class="letra7"><?php echo $mCabMov[$i]['regfcrex'] ?></td>
	        	              <td class="letra7"><?php echo $mCabMov[$i]['reghcrex'] ?></td>
	        	              <td class="letra7"><?php echo $mCabMov[$i]['regestxx'] ?></td>
	          	            <td Class="letra7" align="right">
	            	           	<input type="checkbox" name="oChkCom" value = "<?php echo count($mCabMov) ?>"
	                   	    				 id="<?php echo $mCabMov[$i]['logidxxx'].'~'.
	                   	    							 				  $mCabMov[$i]['comidxxx'].'~'.
	                   	    		               			$mCabMov[$i]['comcodxx'].'~'.
	                   	    		               			$mCabMov[$i]['comcscxx'].'~'.
	                   	    		               			$mCabMov[$i]['comcsc2x'].'~'.
	                   	    		               			$mCabMov[$i]['comcsc3x']; ?>"
	                   	    				 onclick="javascript:document.forms['frgrm']['vRecords'].value='<?php echo count($mCabMov) ?>'">
	            	          </td>
	              	      </tr>
	                	    <?php $y++;
 	                  	}
 	                	} ?>
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
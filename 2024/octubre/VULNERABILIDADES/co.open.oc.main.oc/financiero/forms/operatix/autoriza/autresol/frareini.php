<?php
  namespace openComex;
 		/*
	 * Anular Formularios.
	 * --- Descripcion: Me lista los formularios con estado ASIGNADO. de todos los Directores de Cuenta de Toda Colombia.
	 * @author Pedro Leon Burbano Suarez <pedrob@repremundo.com.co>
	 * @version 001
	 */
	include("../../../../libs/php/utility.php");

  /* Busco en la 05 que Tiene Permiso el Usuario*/
  $qUsrMen  = "SELECT * ";
  $qUsrMen .= "FROM $cAlfa.sys00005 ";
  $qUsrMen .= "WHERE ";
  $qUsrMen .= "sys00005.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
  $qUsrMen .= "sys00005.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
  $qUsrMen .= "sys00005.menimgon <> '' ";
  $qUsrMen .= "ORDER BY sys00005.menordxx";
  $xUsrMen  = f_MySql("SELECT","",$qUsrMen,$xConexion01,"");

  $cPerAno = date('Y');
  $cPerMes = date('m');
  
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

	function f_Link(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
     	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      document.cookie="kMenDes="+xMenDes+";path="+"/";
     	document.cookie="kModo="+xOpcion;
     	//document.cookie="kModo="+xOpcion+";path="+"/";
     	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
     	document.location = xForm; // Invoco el menu.
    }

		 	function f_Cambio_Resolucion() {
				switch (document.forms['frgrm']['vRecords'].value) {
					case "1":
						if (document.forms['frgrm']['vCheck'].checked == true) {
							document.cookie="kModo=EDITAR";
							var zMatriz = document.forms['frgrm']['vCheck'].id.split('~');
							if(zMatriz[3] == 'PRINCIPAL'){
							 var cTitulo = "SECUNDARIA";
							}else{
							 var cTitulo = "PRINCIPAL";
							}
							if (confirm("Esta Seguro de Cambiar el Tipo de Resolucion a "+cTitulo+"?")) {
  							document.cookie="kModo=EDITAR";
								document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
								document.forms['frestado']['cSucId'].value   = zMatriz[0];
								document.forms['frestado']['cDocId'].value   = zMatriz[1];
								document.forms['frestado']['cDocSuf'].value  = zMatriz[2];
								document.forms['frestado']['cDocFsr'].value  = zMatriz[3];
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
								if(zMatriz[3] == 'PRINCIPAL'){
  							 var cTitulo = "SECUNDARIA";
  							}else{
  							 var cTitulo = "PRINCIPAL";
  							}
  							if (confirm("Esta Seguro de Cambiar el Tipo de Resolucion a "+cTitulo+"?")) {
  								document.cookie="kModo=EDITAR";
  								document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
  								document.forms['frestado']['cSucId'].value   = zMatriz[0];
  								document.forms['frestado']['cDocId'].value   = zMatriz[1];
  								document.forms['frestado']['cDocSuf'].value  = zMatriz[2];
  								document.forms['frestado']['cDocFsr'].value  = zMatriz[3];
  								document.forms['frestado'].submit();
  							}
							}
						}
					break;
				}
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

	    function f_ButtonsAscDes(xEvent,xSortField,xSortType) {
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
		<form name = "frestado" action = "fraregra.php" method = "post" target="fmpro">
		  <input type = "hidden" name = "cSucId"    value = "">
		  <input type = "hidden" name = "cDocId"    value = "">
		  <input type = "hidden" name = "cDocSuf"   value = "">
		  <input type = "hidden" name = "cDocFsr"   value = "">
		</form>

    <form name = "frgrm" action = "frareini.php" method = "post" target="fmwork">
		  <input type = "hidden" name = "vChekeados" value = "">
		  <input type = "hidden" name = "vDo"        value = "">
		  <input type = "hidden" name = "vComMemo"   value = "">
		  <input type = "hidden" name = "gTipSav"    value = "">
		  <input type = "hidden" name = "vEstado"    value = "">
   		<input type = "hidden" name = "vRecords"   value = "">
   		<input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
   		<input type = "hidden" name = "vSortField" value = "<?php echo $vSortField ?>">
   		<input type = "hidden" name = "vSortType"  value = "<?php echo $vSortType ?>">
   		<input type = "hidden" name = "vTimes"     value = "<?php echo $vTimes ?>">

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
												while($xRUM = mysql_fetch_array($xUsrMen)) {
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
												  $qUsrPer .= "modidxxx = \"{$xRUM['modidxxx']}\"  AND ";
												  $qUsrPer .= "proidxxx = \"{$xRUM['proidxxx']}\"  AND ";
												  $qUsrPer .= "menidxxx = \"{$xRUM['menidxxx']}\"  LIMIT 0,1";
												  $xUsrPer  = f_MySql("SELECT","",$qUsrPer,$xConexion01,"");
												  if (mysql_num_rows($xUsrPer) > 0) { ?>
													  <td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory ?>/<?php echo $xRUM['menimgon'] ?>" style = "cursor:pointer" onClick ="javascript:f_Link('<?php echo $xRUM['modidxxx'] ?>','<?php echo $xRUM['proidxxx'] ?>','<?php echo $xRUM['menidxxx'] ?>','<?php echo $xRUM['menformx']?>','<?php echo $xRUM['menopcxx']?>','<?php echo $xRUM['mendesxx']?>')"><br>
				                    <a href = "javascript:f_Link('<?php echo $xRUM['modidxxx'] ?>','<?php echo $xRUM['proidxxx'] ?>','<?php echo $xRUM['menidxxx'] ?>','<?php echo $xRUM['menformx']?>','<?php echo $xRUM['menopcxx']?>','<?php echo $xRUM['mendesxx']?>')"
															style="color:<?php echo $vSysStr['system_link_menu_color'] ?>"><?php echo $xRUM['mendesxx'] ?></a></center></td>
													<?php	} else { ?>
														<td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory ?>/<?php echo $xRUM['menimgof']?>"><br>
   			    		          	<?php echo $xRUM['mendesxx'] ?></center></td>
													<?php }
													$y++;
												}
												$nCeldas = "";
				      	  	  	$nf = intval($y/5);
				        	  	  $nResto = $y-$nf;
					        	  	$nRestan = 5-$nResto;
					          	  if ($nRestan > 0) {
		    			        		for ($i=0;$i<$nRestan;$i++) {
		        			      		$nCeldas.="<td width='20%'></td>";
				      	      		}
						    	        echo $nCeldas;
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
				
		   	$y=0;
				$zSqlCab  = "SELECT * ";
			  $zSqlCab .= "FROM $cAlfa.sys00121 ";
				$zSqlCab .= "WHERE ";
				$zSqlCab .= "regestxx = \"ACTIVO\" AND ";
				$zSqlCab .= "$cAlfa.sys00121.docfrsxx = \"SECUNDARIA\" ";
				$zSqlCab .= "ORDER BY CONVERT(docidxxx,signed) ASC ";
				//wMenssage(__FILE__,__LINE__,$zSqlCab);
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
				  $zSqlCli .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$zMatrizTmp[$i]['cliidxxx']}\" LIMIT 0,1";
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
					$mMatrizTra = array();
					for ($i=0,$j=0;$i<count($zMatrizTmp);$i++) {
						$zArray = array_values($zMatrizTmp[$i]);
						for ($k=0;$k<count($zArray);$k++) {
							if (substr_count($zArray[$k],strtoupper($vSearch)) > 0) {
								$k = count($zArray)+1;
								$mMatrizTra[$j] = $zMatrizTmp[$i];
								$j++;
							}
						}
					}
				} else {
					$mMatrizTra = $zMatrizTmp;
				}
				/***** Fin de Buscar Patron en la Matriz *****/

				if ($vSortField != "" && $vSortType != "") {
					$mMatrizTra = f_Sort_Array_By_Field($mMatrizTra,$vSortField,$vSortType);
				}
				/* Fin de Recorro la Matriz para Traer Datos Externos */
			?>
      <center>
        <script languaje="javascript">
					document.forms['frgrm']['vRecords'].value = "<?php echo count($mMatrizTra) ?>";
				</script>
       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Autorizaciones Realizadas(<?php echo count($mMatrizTra) ?>)</legend>
     	       		<center>
     	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
         	      			<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
           	        	<td class="clase08" width="14%">
            	        	<input type="text" class="letra" name = "vSearch" maxlength="20" value = "<?php echo $vSearch ?>" style= "width:80"
            	        		onblur="javascript:this.value=this.value.toUpperCase();
																						 document.forms['frgrm']['vLimInf'].value='00';
								      											 document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      											 document.forms['frgrm']['vPaginas'].value='1'
								      											 document.forms['frgrm'].submit()">
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
       	       					<?php if (ceil(count($mMatrizTra)/$vLimSup) > 1) { ?>
       	       						<?php if ($vPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:hand" title="Primera Pagina">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:hand" title="Pagina Anterior">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:hand" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:hand" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil(count($mMatrizTra)/$vLimSup) ?>';
								      				    						 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
       	       						<?php } ?>
       	       						<?php if ($vPaginas > "1" && $vPaginas < ceil(count($mMatrizTra)/$vLimSup)) { ?>
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
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil(count($mMatrizTra)/$vLimSup) ?>';
								      				    						 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
	       	       					<?php } ?>
       	       						<?php if ($vPaginas == ceil(count($mMatrizTra)/$vLimSup)) { ?>
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
													<?php for ($i=0;$i<ceil(count($mMatrizTra)/$vLimSup);$i++) {
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
															case "EDITAR": ?>
															<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_cambest.gif" onClick = "javascript:uVerificarCheck
																																																			uCargaVariable(document.forms['frgrm']['vRecords'].value);
																																																			f_Cambio_Resolucion();"
																																													 style = "cursor:pointer" title="Cambiar Resolucion, Solo Uno">
			                          
															    
																                        
                                <!--script languaje="javascript">
				                          if(document.forms['frgrm']['vRecords'].value ==0){
				                            document.getElementById("IdImg").onclick="";
				                          }
			                          </script-->
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
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','docidxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','docidxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','docidxxx','')">DO</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docidxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','docidxxx','')</script>
          	          </td>
          	          <td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','doctipxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','doctipxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','doctipxx','')">Tipo Operacion</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "doctipxx">
           	         		<script language="javascript">f_ButtonsAscDes('','doctipxx','')</script>
          	          </td>
         	            <td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','cliidxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','cliidxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','cliidxxx','')">Nit</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "cliidxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','cliidxxx','')</script>
           	         	</td>
           	         	<td class="name" width="40%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','CLINOMXX','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','CLINOMXX','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','CLINOMXX','')">Cliente</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "CLINOMXX">
           	         		<script language="javascript">f_ButtonsAscDes('','CLINOMXX','')</script>
          	         	</td>

          	         	<td class="name" width="03%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','docfrsxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','docfrsxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','docfrsxx','')">Resoluci&oacute;n</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docfrsxx">
           	         		<script language="javascript">f_ButtonsAscDes('','docfrsxx','')</script>
          	          </td>
          	          <td class="name" width="07%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','regfcrex','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','regfcrex','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','regfcrex','')">Creado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfcrex">
           	         		<script language="javascript">f_ButtonsAscDes('','regfcrex','')</script>
             	        </td>
             	        <td class="name" width="05%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','reghcrex','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','reghcrex','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','reghcrex','')">Hora</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghcrex">
           	         		<script language="javascript">f_ButtonsAscDes('','reghcrex','')</script>
             	        </td>
             	        <td class="name" width="05%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','regfmodx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','regfmodx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','regfmodx','')">Modificado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfmodx">
           	         		<script language="javascript">f_ButtonsAscDes('','regfmodx','')</script>
             	        </td>
             	        <td class="name" width="05%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','reghmodx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','reghmodx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','reghmodx','')">Hora</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghmodx">
           	         		<script language="javascript">f_ButtonsAscDes('','reghmodx','')</script>
             	        </td>
               	      <td class="name" width="05%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','regestxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','regestxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','regestxx','')">Estado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
           	         		<script language="javascript">f_ButtonsAscDes('','regestxx','')</script>
               	      </td>
               	      <td class="name" width="10%" align="right">
           	         		<input type="checkbox" name="vCheckAll" onClick = 'javascript:uMarca()'>
           	         	</td>
             	     </tr>
					         <script languaje="javascript">
						          document.forms['frgrm']['vRecords'].value = "<?php echo count($mMatrizTra) ?>";
						       </script>
 	                    <?php for($i=intval($vLimInf);$i<intval($vLimInf+$vLimSup);$i++) {
 	                    	if ($i < count($mMatrizTra)) { // Para Controlar el Error
	 	                    	$zColor = "{$vSysStr['system_row_impar_color_ini']}";
	                   	    if($y % 2 == 0) {
	                   	    	$zColor = "{$vSysStr['system_row_par_color_ini']}";
													} ?>
								        <!--<tr bgcolor = "<?php echo $zColor ?>">-->
								        <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
		                      	<td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['docidxxx'] ?></td>
		                      	<td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['doctipxx'] ?></td>
								            <td class="letra7" width="10%"><?php echo substr($mMatrizTra[$i]['cliidxxx'],0,14);?></td>
	       	                	<td class="letra7" width="40%"><?php echo $mMatrizTra[$i]['CLINOMXX'] ?></td>
	       	                	<td class="letra7" width="03%"><?php echo $mMatrizTra[$i]['docfrsxx'] ?></td>
	       	                	<td class="letra7" width="07%"><?php echo $mMatrizTra[$i]['regfcrex'] ?></td>
	          	              <td class="letra7" width="05%"><?php echo $mMatrizTra[$i]['reghcrex'] ?></td>
	          	              <td class="letra7" width="05%"><?php echo $mMatrizTra[$i]['regfmodx'] ?></td>
	          	              <td class="letra7" width="05%"><?php echo $mMatrizTra[$i]['reghmodx'] ?></td>
	          	              <td class="letra7" width="05%"><?php echo $mMatrizTra[$i]['regestxx'] ?></td>
	        	              	<td class="letra7" width="10%" align="right">
	        	              	  <input type="checkbox" name="vCheck"
													       value = "<?php echo $mMatrizTra[$i]['sucidxxx'].'~'.$mMatrizTra[$i]['docidxxx'].'~'.$mMatrizTra[$i]['docsufxx'].'~'.$mMatrizTra[$i]['docfrsxx'].'~'.$mMatrizTra[$i]['regestxx'] ?>"
													       id = "<?php echo $mMatrizTra[$i]['sucidxxx'].'~'.$mMatrizTra[$i]['docidxxx'].'~'.$mMatrizTra[$i]['docsufxx'].'~'.$mMatrizTra[$i]['docfrsxx'].'~'.$mMatrizTra[$i]['regestxx'] ?>">
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
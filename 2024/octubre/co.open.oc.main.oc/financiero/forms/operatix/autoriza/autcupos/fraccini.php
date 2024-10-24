<?php
  namespace openComex;

   /**
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
      	document.cookie="kModo="+xOpcion+";path="+"/";
      	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	document.location = xForm; // Invoco el menu.
      }

		 	function f_Editar() {

  				switch (document.forms['frgrm']['vRecords'].value) {
  					case "1":
  						if (document.forms['frgrm']['vCheck'].checked == true) {
  							var zMatriz = document.forms['frgrm']['vCheck'].id.split('~');
  							var ruta = "fraccnue.php?cSucId="+zMatriz[0]+"&cDocId="+zMatriz[1]+"&cDocSuf="+zMatriz[2];
        	      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        	      document.cookie="kMenDes=Editar Condicion Comercial;path="+"/";
        	      document.cookie="kModo=EDITAR;path="+"/";
        	      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
        	      document.location = ruta; // Invoco el menu.
  						}
  					break;
  					default:
  						var zSw_Prv = 0;
  						for (i=0;i<document.forms['frgrm']['vCheck'].length;i++) {
  							if (document.forms['frgrm']['vCheck'][i].checked == true && zSw_Prv == 0) {
  								// Solo Deja Legalizar el Primero Seleccionado
  								zSw_Prv = 1;
  								var zMatriz = document.forms['frgrm']['vCheck'][i].id.split('~');
								  var ruta = "fraccnue.php?cSucId="+zMatriz[0]+"&cDocId="+zMatriz[1]+"&cDocSuf="+zMatriz[2];
          	      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
          	      document.cookie="kMenDes=Editar Condicion Comercial;path="+"/";
          	      document.cookie="kModo=EDITAR;path="+"/";
          	      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
        	        document.location = ruta; // Invoco el menu.
  							}
  						}
  					break;
  				}
		 	}
		 	 
	  	function f_Marca() {
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


    <form name = "frgrm" method = "post" >
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
				$qDatDo  = "SELECT * ";
			  $qDatDo .= "FROM $cAlfa.sys00121 ";
				$qDatDo .= "WHERE ";
				$qDatDo .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" AND ";
				$qDatDo .= "$cAlfa.sys00121.doccupxx <> \"\" ";
				$qDatDo .= "ORDER BY CONVERT(docidxxx,signed) ASC ";
				//wMenssage(__FILE__,__LINE__,$qDatDo);
				$xDatDo = mysql_query($qDatDo,$xConexion01);
				//wMenssage(__FILE__,__LINE__,mysql_num_rows($xDatDo));
				/* Cargo la Matriz con los ROWS del Cursor */
				$i=0;
				while ($xRDD = mysql_fetch_array($xDatDo)) {
					$mzMatrizTmp[$i] = $xRDD;
					
					
					
					/* Traigo el Nombre del Cliente del Do */
					$qCliNom = "SELECT * ";
					$qCliNom .= "FROM $cAlfa.SIAI0150 ";
					$qCliNom .= "WHERE ";
					$qCliNom .= "CLIIDXXX = \"{$mzMatrizTmp[$i]['cliidxxx']}\" LIMIT 0,1";
					$xCliNom = mysql_query($qCliNom,$xConexion01);
					if (mysql_num_rows($xCliNom) > 0) {
						while ($xRCN = mysql_fetch_array($xCliNom)) {
							$mzMatrizTmp[$i]['clinomxx'] = trim($xRCN['CLINOMXX']." ".$xRCN['CLIAPE1X']." ".$xRCN['CLIAPE2X']." ".$xRCN['CLINOM1X']." ".$xRCN['CLINOM2X']);
							$mzMatrizTmp[$i]['clicupti'] = $xRCN['CLICUPTI'];
						}
					} else {
						$mzMatrizTmp[$i]['clinomxx'] = "CLIENTE SIN NOMBRE";
					}
					/* Fin Traigo el Cliente del Do */
					$i++;
				}
				/* Fin de Cargo la Matriz con los ROWS del Cursor */
				/* Recorro la Matriz para Traer Datos Externos */

				/***** Si el $vSearch Viene Cargado Busco el Patron en Todos los Campos de la Matriz *****/
				if ($vSearch != "") {
        	$mMatrizTra = array();
	        for ($i=0,$j=0;$i<count($mzMatrizTmp);$i++) {

	          if (substr_count(strtoupper($vSearch),"=") > 0) {
	          	$mPatron = explode("=",$vSearch); $cCriterio = "EQUAL";
	          } else {
		          if (substr_count(strtoupper($vSearch),".") > 0) {
		            $mPatron = explode(".",$vSearch); $cCriterio = "AND";
		          } else {
		            if (substr_count(strtoupper($vSearch),",") > 0) {
		              $mPatron = explode(",",$vSearch); $cCriterio = "OR";
		            } else {
		              $mPatron = array("{$vSearch}"); $cCriterio = "NOTHING";
		            }
		          }
	          }

	          $cCadena  = $mzMatrizTmp[$i]['docidxxx']."~";
	          $cCadena  = $mzMatrizTmp[$i]['docidxxx']."~";
	          $cCadena  = $mzMatrizTmp[$i]['docidxxx']."~";
	          $cCadena .= $mzMatrizTmp[$i]['doctipxx']."~";
	          $cCadena .= $mzMatrizTmp[$i]['cliidxxx']."~";
	          $cCadena .= $mzMatrizTmp[$i]['clinomxx']."~";
	          $cCadena .= $mzMatrizTmp[$i]['regfcrex']."~";
	          $cCadena .= $mzMatrizTmp[$i]['reghcrex']."~";
	          $cCadena .= $mzMatrizTmp[$i]['regfmodx']."~";
	          $cCadena .= $mzMatrizTmp[$i]['reghmodx']."~";
	          $cCadena .= $mzMatrizTmp[$i]['regestxx'];

	          $nCont_Find = 0;
	          switch ($cCriterio) {
	          	case "EQUAL":
	              for ($k=0;$k<count($mPatron);$k++) {
	              	if (in_array(strtoupper($mPatron[$k]),$mzMatrizTmp[$i])) {
	                  $nCont_Find++;
	                }
	              }
	            break;
	            case "AND":
	            	$nContador = 0;
	              for ($k=0;$k<count($mPatron);$k++) {
	                if (substr_count(strtoupper($cCadena),trim($mPatron[$k])) > 0) {
	                	$nContador++;
	                }
	              }
	              if (count($mPatron) == $nContador) { $nCont_Find++; }
	            break;
	            case "OR":
	            case "NOTHING";
		            for ($k=0;$k<count($mPatron);$k++) {
		              if (substr_count(strtoupper($cCadena),trim($mPatron[$k])) > 0) {
		                $nCont_Find++;
		              }
		            }
	            break;
	          }
	          if ($nCont_Find > 0) {
		          $mMatrizTra[$j] = $mzMatrizTmp[$i]; $j++;
		        }
	        }
				} else {
					$mMatrizTra = $mzMatrizTmp;
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
   			        <legend>Registros Seleccionados (<?php echo count($mMatrizTra) ?>)</legend>
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
															<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_edit.png" name = "IdImg" id = "IdImg" onClick = "javascript:f_Editar('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="Editar, Solo Uno">
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
           	          <td class="name" width="12%">
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
           	         	<td class="name" width="32%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','clinomxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','clinomxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','clinomxx','')">Cliente</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "clinomxx">
           	         		<script language="javascript">f_ButtonsAscDes('','clinomxx','')</script>
          	         	</td>
          	         	<td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','doccupxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','doccupxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','doccupxx','')">Cupo Autorizado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "doccupxx">
           	         		<script language="javascript">f_ButtonsAscDes('','doccupxx','')</script>
          	          </td>
          	          <td class="name" width="10%" align="center">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','doccupaf','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','doccupaf','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','doccupaf','')">Aut. Facturar</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "doccupaf">
           	         		<script language="javascript">f_ButtonsAscDes('','doccupaf','')</script>
          	          </td>
          	          <td class="name" width="07%" align="center">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','doccupfe','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','doccupfe','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','doccupfe','')">Fecha</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "doccupfe">
           	         		<script language="javascript">f_ButtonsAscDes('','doccupfe','')</script>
             	        </td>
             	        <td class="name" width="07%" align="center">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','doccupho','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','doccupho','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','doccupho','')">Hora</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "doccupho">
           	         		<script language="javascript">f_ButtonsAscDes('','doccupho','')</script>
             	        </td>
               	      <td class="name" width="02%" align="right">
           	         		<input type="checkbox" name="vCheckAll" onClick = 'javascript:f_Marca()'>
           	         	</td>
             	     </tr>
					         <script language="javascript">
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
		                      	<td class="letra7"><?php echo $mMatrizTra[$i]['sucidxxx'].'-'.$mMatrizTra[$i]['docidxxx'].'-'.$mMatrizTra[$i]['docsufxx'] ?></td>
		                      	<td class="letra7"><?php echo $mMatrizTra[$i]['doctipxx'] ?></td>
								            <td class="letra7"><?php echo $mMatrizTra[$i]['cliidxxx'] ?></td>
	       	                	<td class="letra7"><?php echo $mMatrizTra[$i]['clinomxx'] ?></td>
	       	                	<td class="letra7" align="right" style="paddign-right:5px"><?php echo ($mMatrizTra[$i]['doccupxx'] > 0)?number_format($mMatrizTra[$i]['doccupxx'],0,',','.'):"&nbsp;"; ?></td>
	       	                	<td class="letra7" align="center"><?php echo $mMatrizTra[$i]['doccupaf'] ?></td>
	       	                	<td class="letra7" align="center"><?php echo ($mMatrizTra[$i]['doccupfe'] <> "0000-00-00")?$mMatrizTra[$i]['doccupfe']:"&nbsp;"; ?></td>
	       	                	<td class="letra7" align="center"><?php echo ($mMatrizTra[$i]['doccupho'] <> "00:00:00")?$mMatrizTra[$i]['doccupho']:"&nbsp;"; ?></td>
	        	              	<td class="letra7" align="right">
	        	              	  <input type="checkbox" name="vCheck"
													       value = "<?php echo $mMatrizTra[$i]['sucidxxx'].'~'.$mMatrizTra[$i]['docidxxx'].'~'.$mMatrizTra[$i]['docsufxx'].'~'.$mMatrizTra[$i]['regestxx'].'~'.$mMatrizTra[$i]['clicupti'] ?>"
													       id = "<?php echo $mMatrizTra[$i]['sucidxxx'].'~'.$mMatrizTra[$i]['docidxxx'].'~'.$mMatrizTra[$i]['docsufxx'].'~'.$mMatrizTra[$i]['regestxx'].'~'.$mMatrizTra[$i]['clicupti'] ?>">
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
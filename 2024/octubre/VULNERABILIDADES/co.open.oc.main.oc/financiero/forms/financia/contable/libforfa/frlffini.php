<?php
  namespace openComex;
/**
 * Tracking Liberacion de Formularios Facturados.
 * Este programa permite realizar consultas rapidas de los Formularios que han sido liberados y no se ha utilizado nuevamente.
 * @author Johana Arboleda <dp5@opentecnologia.com.co>
 * @package Opencomex
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

       function f_Marca() {
      	if (document.forms['frgrm']['oChkComAll'].checked == true){
      	  if (document.forms['frgrm']['nRecords'].value == 1){
      	  	document.forms['frgrm']['oChkCom'].checked=true;
      	  } else {
	      		if (document.forms['frgrm']['nRecords'].value > 1){
			      	for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
   	   	      	document.forms['frgrm']['oChkCom'][i].checked = true;
			      	}
			      }
      	  }
      	} else {
	      	if (document.forms['frgrm']['nRecords'].value == 1){
      	  	document.forms['frgrm']['oChkCom'].checked=false;
      	  } else {
      	  	if (document.forms['frgrm']['nRecords'].value > 1){
				      for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
				      	document.forms['frgrm']['oChkCom'][i].checked = false;
				      }
      	  	}
 	  	   	}
	      }
	 		}

	    function f_ButtonsAscDes(xEvent,xSortField,xSortType) {
	    	var zSortType = "";
	    	switch (document.forms['frgrm']['cSortType'].value) {
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
						if (document.getElementById(xSortField).id != document.forms['frgrm']['cSortField'].value) {
							document.forms['frgrm']['cSortField'].value=xSortField;
							document.forms['frgrm']['cSortType'].value=xSortType;
							document.forms['frgrm'].submit();
						} else {
							document.forms['frgrm'].submit();
						}
	    		break;
	    		case "onmouseover":
						if(document.forms['frgrm']['cSortField'].value == xSortField) {
							if (document.forms['frgrm']['cSortType'].value == 'ASC_NUM' || document.forms['frgrm']['cSortType'].value == 'ASC_AZ') {
								document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
								document.forms['frgrm']['cSortField'].value = xSortField;
								document.forms['frgrm']['cSortType'].value = zSortType;
							} else {
								document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
								document.forms['frgrm']['cSortField'].value = xSortField;
								document.forms['frgrm']['cSortType'].value = zSortType;
							}
						} else {
							document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
						}
	    		break;
	    		case "onmouseout":
						if(document.forms['frgrm']['cSortField'].value == xSortField) {
							if (document.forms['frgrm']['cSortType'].value == 'ASC_NUM' || document.forms['frgrm']['cSortType'].value == 'ASC_AZ') {
								document.getElementById(xSortField).src='<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
								document.forms['frgrm']['cSortField'].value = xSortField;
								document.forms['frgrm']['cSortType'].value = zSortType;
							} else {
								document.getElementById(xSortField).src='<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
								document.forms['frgrm']['cSortField'].value = xSortField;
								document.forms['frgrm']['cSortType'].value = zSortType;
							}
						} else {
							document.getElementById(xSortField).src='<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
						}
	    		break;

	    	}
     	  if(document.forms['frgrm']['cSortField'].value == xSortField) {
       		if (document.forms['frgrm']['cSortType'].value == 'ASC_NUM' || document.forms['frgrm']['cSortType'].value == 'ASC_AZ') {
       	  	document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
       	    document.getElementById(xSortField).title = 'Ascendente';
       	  } else {
       	    document.getElementById(xSortField).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
       	    document.getElementById(xSortField).title = 'Descendente';
       	  }
       	}
	    }
  	</script>
  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
		<form name = "frgrm" action = "frlffini.php" method = "post">
   		<input type = "hidden" name = "nRecords"   value = "">
   		<input type = "hidden" name = "nLimInf"    value = "<?php echo $nLimInf ?>">
   		<input type = "hidden" name = "cSortField" value = "<?php echo $cSortField ?>">
   		<input type = "hidden" name = "cSortType"  value = "<?php echo $cSortType ?>">
   		<input type = "hidden" name = "cBuscar"    value = "<?php echo $_POST['cBuscar'] ?>">

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

				if ($nLimInf == "" && $nLimSup == "") {
					$nLimInf = "00";
          $nLimSup = $vSysStr['system_rows_page_ini'];
				}

				if ($nPaginas == "") {
        	$nPaginas = "1";
				}

			  $y=0;
        $qForDat  = "SELECT ";
				$qForDat .= "ptoidxxx, ";
				$qForDat .= "seridxxx, ";
				$qForDat .= "sucidxxx, ";
				$qForDat .= "doccomex, ";
				$qForDat .= "docsufxx, ";
				$qForDat .= "regestxx ";
				$qForDat .= "FROM $cAlfa.ffoi0000 ";
				$qForDat .= "WHERE  ";
				$qForDat .= "regestxx = \"CONDO\" AND  ";
				$qForDat .= "forobsli <> \"\" ";
				$qForDat .= "ORDER BY sucidxxx, doccomex, docsufxx, seridxxx";
				$xForDat  = f_MySql("SELECT","",$qForDat,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qForDat."~".mysql_num_rows($xForDat));
        # Cargo la Matriz con los ROWS del Cursor 
				/* Cargo la Matriz con los ROWS del Cursor */
				$i=0;
				while ($xDT = mysql_fetch_array($xForDat)) {
				  ## Traigo la descripcion del tipo Productos Formulario ##
          $qPtoDes  = "SELECT ";
          $qPtoDes .= "$cAlfa.fpar0132.ptodesxx ";
          $qPtoDes .= "FROM $cAlfa.fpar0132 ";
          $qPtoDes .= "WHERE ";
          $qPtoDes .= "$cAlfa.fpar0132.ptoidxxx = \"{$xDT['ptoidxxx']}\" LIMIT 0,1";
          $xPtoDes  = f_MySql("SELECT","",$qPtoDes,$xConexion01,"");
          if (mysql_num_rows($xPtoDes) > 0) {
            while ($xRFD = mysql_fetch_array($xPtoDes)) {
              $xDT['ptodesxx'] = $xRFD['ptodesxx'];
            }
          } else {
            $xDT['ptodesxx'] = "TIPO FORMULARIO SIN NOMBRE";
          }
          
					$zMatrizTmp[$i] = $xDT;
					$i++;
				}
				/* Fin de Cargo la Matriz con los ROWS del Cursor */
				
				/***** Si el $cSearch Viene Cargado Busco el Patron en Todos los Campos de la Matriz *****/
				if ($cSearch != "") {
        	$mMatrizTra = array();
	        for ($i=0,$j=0;$i<count($zMatrizTmp);$i++) {

	          if (substr_count(strtoupper($cSearch),"=") > 0) {
	          	$mPatron = explode("=",$cSearch); $cCriterio = "EQUAL";
	          } else {
		          if (substr_count(strtoupper($cSearch),".") > 0) {
		            $mPatron = explode(".",$cSearch); $cCriterio = "AND";
		          } else {
		            if (substr_count(strtoupper($cSearch),",") > 0) {
		              $mPatron = explode(",",$cSearch); $cCriterio = "OR";
		            } else {
		              $mPatron = array("{$cSearch}"); $cCriterio = "NOTHING";
		            }
		          }
	          }

	          $cCadena  = $zMatrizTmp[$i]['ptodesxx']."~";
	          $cCadena .= $zMatrizTmp[$i]['seridxxx']."~";
	          $cCadena .= $zMatrizTmp[$i]['sucidxxx']."~";
	          $cCadena .= $zMatrizTmp[$i]['doccomex']."~";
	          $cCadena .= $zMatrizTmp[$i]['docsufxx']."~";
	          $cCadena .= $zMatrizTmp[$i]['regestxx'];

	          $nCont_Find = 0;
	          switch ($cCriterio) {
	          	case "EQUAL":
	              for ($k=0;$k<count($mPatron);$k++) {
	              	if (in_array(strtoupper($mPatron[$k]),$zMatrizTmp[$i])) {
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
		          $mMatrizTra[$j] = $zMatrizTmp[$i]; $j++;
		        }
	        }
				} else {
					$mMatrizTra = $zMatrizTmp;
				}
				/***** Fin de Buscar Patron en la Matriz *****/

				if ($cSortField != "" && $cSortType != "") {
					$mMatrizTra = f_Sort_Array_By_Field($mMatrizTra,$cSortField,$cSortType);
				}
			?>
      <center>
       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Registros Seleccionados (<?php echo count($mMatrizTra)?>)</legend>
     	         	<center>
       	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
           	        	<td class="clase08" width="14%">
            	        	<input type="text" class="letra" name = "cSearch" maxlength="50" value = "<?php echo $cSearch ?>" style= "width:80"
            	        		onblur="javascript:this.value=this.value.toUpperCase();
																						 document.forms['frgrm']['nLimInf'].value='00';
								      											 document.forms['frgrm']['nPaginas'].value='1'">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:pointer" title="Buscar"
								      		onClick = "javascript:document.forms['frgrm']['cBuscar'].value = 'ON'
								      											    document.forms['frgrm']['cSearch'].value=document.forms['frgrm']['cSearch'].value.toUpperCase();
								      												  document.forms['frgrm'].submit()">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:pointer" title="Mostrar Todo"
								      		onClick ="javascript:document.forms['frgrm']['cSearch'].value='';
								      												 document.forms['frgrm']['nLimInf'].value='00';
								      												 document.forms['frgrm']['nLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												 document.forms['frgrm']['nPaginas'].value='1';
								      												 document.forms['frgrm']['cSortField'].value='';
								      												 document.forms['frgrm']['cSortType'].value='';
								      												 document.forms['frgrm']['cBuscar'].value='';
								      												 document.forms['frgrm'].submit()">
   	              	  </td>
       	       				<td class="name" width="06%" align="left">Filas&nbsp;
       	       					<input type="text" class="letra" name = "nLimSup" value = "<?php echo $nLimSup ?>" style="width:30;text-align:right"
       	       						onblur = "javascript:f_FixFloat(this);
								      												 document.forms['frgrm']['nLimInf'].value='00';">
       	       				</td>
       	       				<td class="name" width="08%">
       	       					<?php if (ceil(count($mMatrizTra)/$nLimSup) > 1) { ?>
       	       						<?php if ($nPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value++;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='<?php echo ceil(count($mMatrizTra)/$nLimSup) ?>';
								      				    						 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
       	       						<?php } ?>
       	       						<?php if ($nPaginas > "1" && $nPaginas < ceil(count($mMatrizTra)/$nLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='1';
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value--;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value++;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='<?php echo ceil(count($mMatrizTra)/$nLimSup) ?>';
								      				    						 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
	       	       					<?php } ?>
       	       						<?php if ($nPaginas == ceil(count($mMatrizTra)/$nLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='1';
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value--;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
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
       	       				<td class="name" width="08%" align="left">Pag&nbsp;
												<select Class = "letrase" name = "nPaginas" value = "<?php echo $nPaginas ?>" style = "width:60%"
       	       						onchange="javascript:this.id = 'ON';
								      												 document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(this.value-1));
       	       						                     document.forms['frgrm'].submit()">
													<?php for ($i=0;$i<ceil(count($mMatrizTra)/$nLimSup);$i++) {
														if ($i+1 == $nPaginas) { ?>
															<option value = "<?php echo $i+1 ?>" selected><?php echo $i+1 ?></option>
														<?php } else { ?>
															<option value = "<?php echo $i+1 ?>"><?php echo $i+1 ?></option>
														<?php } ?>
													<?php } ?>
												</select>
       	       				</td>
   	         	        <td Class="name" width="15%" align="right">&nbsp;
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

													while ($mBotAcc = mysql_fetch_array($xBotAcc)) {
														switch ($mBotAcc['menopcxx']) {
															case "EDITAR": ?>
																<!-- <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_edit.png" onClick = "javascript:f_Editar('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="Editar, Solo Uno"> -->
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
         	         		<td class="name" width="08%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','sucidxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','sucidxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','sucidxxx','')">Sucursal</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "sucidxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','sucidxxx','')</script>
           	         	</td>
           	         	<td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','doccomex','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','doccomex','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','doccomex','')">DO</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "doccomex">
           	         		<script language="javascript">f_ButtonsAscDes('','doccomex','')</script>
           	         	</td>
           	         	<td class="name" width="05%">
                        <a href = "javascript:f_ButtonsAscDes('onclick','docsufxx','ASC_AZ')" title="Ordenar"
                        onmouseover="javascript:f_ButtonsAscDes('onmouseover','docsufxx','')"
                        onmouseout="javascript:f_ButtonsAscDes('onmouseout','docsufxx','')">Suf</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docsufxx">
                        <script language="javascript">f_ButtonsAscDes('','docsufxx','')</script>
                      </td>
           	         	<td class="name" width="20%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','seridxxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','seridxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','seridxxx','')">Serial</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "seridxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','seridxxx','')</script>
           	         	</td>
           	         	<td class="name" width="47%">
                        <a href = "javascript:f_ButtonsAscDes('onclick','ptodesxx','ASC_AZ')" title="Ordenar"
                        onmouseover="javascript:f_ButtonsAscDes('onmouseover','ptodesxx','')"
                        onmouseout="javascript:f_ButtonsAscDes('onmouseout','ptodesxx','')">Descripci&oacute;n</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ptodesxx">
                        <script language="javascript">f_ButtonsAscDes('','ptodesxx','')</script>
                      </td>
               	      <td class="name" width="08%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','regestxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','regestxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','regestxx','')">Estado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
           	         		<script language="javascript">f_ButtonsAscDes('','regestxx','')</script>
               	      </td>
                 	    <td Class='name' width="02%" align="right">
                 	    	<input type="checkbox" name="oChkComAll" onClick = 'javascript:f_Marca()'>
                 	    </td>
                 		</tr>
								      <script languaje="javascript">
												document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra) ?>";
											</script>
 	                    <?php for ($i=intval($nLimInf);$i<intval($nLimInf+$nLimSup);$i++) {
 	                    	if ($i < count($mMatrizTra)) { // Para Controlar el Error
	 	                    	$cColor = "{$vSysStr['system_row_impar_color_ini']}";
	                   	    if($y % 2 == 0) {
	                   	    	$cColor = "{$vSysStr['system_row_par_color_ini']}";
													} ?>
													<!--<tr bgcolor = "<?php echo $cColor ?>">-->
													<tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
		                      	<td class="letra7"><?php echo $mMatrizTra[$i]['sucidxxx'] ?></td>
	       	                	<td class="letra7"><?php echo $mMatrizTra[$i]['doccomex'] ?></td>
	       	                	<td class="letra7"><?php echo $mMatrizTra[$i]['docsufxx'] ?></td>
	          	              <td class="letra7"><?php echo $mMatrizTra[$i]['seridxxx'] ?></td>
	          	              <td class="letra7"><?php echo $mMatrizTra[$i]['ptodesxx'] ?></td>
	          	              <td class="letra7"><?php echo $mMatrizTra[$i]['regestxx'] ?></td>
	            	            <td Class="letra7" align="right"><input type="checkbox" name="oChkCom"  value = "<?php echo count($mMatrizTra) ?>"
	                   	    		id="<?php echo $mMatrizTra[$i]['sucidxxx'].'-'.$mMatrizTra[$i]['doccomex'].'-'.$mMatrizTra[$i]['docsufxx'].'-'.$mMatrizTra[$i]['seridxxx'].'-'.$mMatrizTra[$i]['regestxx']?>"
	                   	    		onclick="javascript:document.forms['frgrm']['nRecords'].value='<?php echo count($mMatrizTra) ?>'">
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
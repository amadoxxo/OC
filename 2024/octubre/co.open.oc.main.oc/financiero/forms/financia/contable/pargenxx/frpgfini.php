<?php
  namespace openComex;
/**
 * Tracking.
 * Este programa permite realizar consultas rapidas de los Parametro Generals que se Encuentran en la Base de Datos.
 * @author
 * @package emisioncero
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

    	function f_Ver(xStrId) {
      	var cRuta = "frpgfnue.php?gStrId="+xStrId;
      	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      	document.cookie="kMenDes=Ver Parametro General;path="+"/";
      	document.cookie="kModo=VER;path="+"/";
      	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	document.location = cRuta; // Invoco el menu.
	    }

	  	function f_Editar(xModo) {
				switch (document.forms['frgrm']['nRecords'].value) {
					case "1":
						if (document.forms['frgrm']['oCheck'].checked == true) {
							var zMatriz = document.forms['frgrm']['oCheck'].id.split('~');
							var cRuta = "frpgfnue.php?gStrId="+zMatriz[0];
      	      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      	      document.cookie="kMenDes=Editar Parametro General;path="+"/";
      	      document.cookie="kModo="+xModo+";path="+"/";
      	      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	      document.location = cRuta; // Invoco el menu.
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
							if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
								// Solo Deja Legalizar el Primero Seleccionado
								zSw_Prv = 1;
								var zMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
								var cRuta = "frpgfnue.php?gStrId="+zMatriz[0];
      	      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      	      document.cookie="kMenDes=Editar Parametro General;path="+"/";
      	      document.cookie="kModo="+xModo+";path="+"/";
      	      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	      document.location = cRuta; // Invoco el menu.
							}
						}
					break;
				}
	    }


	    function f_Anular(xModo) {
				switch (document.forms['frgrm']['nRecords'].value) {
					case "1":
						if (document.forms['frgrm']['oCheck'].checked == true) {
							var zMatriz = document.forms['frgrm']['oCheck'].id.split('~');
							if (confirm("Esta Seguro de Cambiar el Estado de la Parametro General No. "+zMatriz[1]+" ")) {
							  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
							  document.cookie="kModo="+xModo+";path="+"/";
								document.forms['frestado']['cEstado'].value = zMatriz[2];
								document.forms['frestado']['cStrId'].value  = zMatriz[0];
								document.forms['frestado'].submit();
							}
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
							if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
								// Solo Deja Legalizar el Primero Seleccionado
								zSw_Prv = 1;
								var zMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
								if (confirm("Esta Seguro de Cambiar el Estado de la Parametro General No. "+zMatriz[1]+" ")) {
								  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
								  document.cookie="kModo="+xModo+";path="+"/";
									document.forms['frestado']['cEstado'].value = zMatriz[2];
									document.forms['frestado']['cStrId'].value  = zMatriz[0];
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
      	if (document.forms['frgrm']['oCheckAll'].checked == true){
      	  if (document.forms['frgrm']['nRecords'].value == 1){
      	  	document.forms['frgrm']['oCheck'].checked=true;
      	  } else {
	      		if (document.forms['frgrm']['nRecords'].value > 1){
			      	for (i=0;i<document.forms['frgrm']['oCheck'].length;i++){
   	   	      	document.forms['frgrm']['oCheck'][i].checked = true;
			      	}
			      }
      	  }
      	} else {
	      	if (document.forms['frgrm']['nRecords'].value == 1){
      	  	document.forms['frgrm']['oCheck'].checked=false;
      	  } else {
      	  	if (document.forms['frgrm']['nRecords'].value > 1){
				      for (i=0;i<document.forms['frgrm']['oCheck'].length;i++){
				      	document.forms['frgrm']['oCheck'][i].checked = false;
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
		<form name = "frestado" action = "frpgfgra.php" method = "post" target="fmpro">
			<input type = "hidden" name = "cStrId" value = "">
			<input type = "hidden" name = "cEstado" value = "">
		</form>

		<form name = "frgrm">
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

				if ($cPaginas == "") {
        	$cPaginas = "1";
				}

				/***** Si Viene Vacio el $cUsrId lo Cargo con la Cookie del Usuario *****/
				/***** Si no Hago el SELECT con el Usuario que me Entrega el Combo de INI *****/
				if ($cUsrId == "") {
        	$cUsrId = $_COOKIE['kUsrId'];
				} else {
					/***** Si el $cUsrId Viene Cargado del Combo con "ALL" es porque Debo Mostrar Todos los Usuarios *****/
					/***** Si no Dejo el Usuario que Viene Cargado *****/
					if ($cUsrId == "ALL") {
						$cUsrId = "";
					}
				}

        $y=0;
        $qDatPgf  = "SELECT * ";
				$qDatPgf .= "FROM $cAlfa.sys00002 ";
				$qDatPgf .= "WHERE stridxxx LIKE \"financiero%\" ";
				$qDatPgf .= "ORDER BY regfmodx DESC,reghmodx DESC";
				$xDatPgf  = f_MySql("SELECT","",$qDatPgf,$xConexion01,"");

				/* Cargo la Matriz con los ROWS del Cursor */
				$i=0;
				while ($xRDP = mysql_fetch_array($xDatPgf)) {
					$mMatrizTmp[$i] = $xRDP;
					$i++;
				}
				/* Fin de Cargo la Matriz con los ROWS del Cursor */

				/* Recorro la Matriz para Traer Datos Externos */
				/*
				for ($i=0;$i<count($mMatrizTmp);$i++) {
					// Traigo el Nombre del Usuario
					$qNomUsr = "SELECT usrnomxx FROM $cAlfa.sys00001 WHERE usridxxx = \"{$mMatrizTmp[$i]['regusrxx']}\" LIMIT 0,1";
					$xNomUsr  = f_MySql("SELECT","",$qNomUsr,$xConexion01,"");

					$vNomUsr = mysql_fetch_array($xNomUsr);
					$mMatrizTmp[$i]['usrnomxx'] = ($vNomUsr['usrnomxx'] != "") ? trim($vNomUsr['usrnomxx']) : "USUARIO SIN NOMBRE";
					// Fin Traigo el Nombre del Usuario
				}
				*/
				/* Fin de Recorro la Matriz para Traer Datos Externos */

				/***** Si el $cSearch Viene Cargado Busco el Patron en Todos los Campos de la Matriz *****/
				if ($cSearch != "") {
        	$mMatrizTra = array();
	        for ($i=0,$j=0;$i<count($mMatrizTmp);$i++) {

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

	          $cCadena  = $mMatrizTmp[$i]['stridxxx']."~";
	          $cCadena .= $mMatrizTmp[$i]['strvlrxx']."~";
	          $cCadena .= $mMatrizTmp[$i]['strdesxx']."~";
	          $cCadena .= $mMatrizTmp[$i]['regfmodx']."~";
	          $cCadena .= $mMatrizTmp[$i]['reghmodx']."~";
	          $cCadena .= $mMatrizTmp[$i]['regestxx'];

	          $nCont_Find = 0;
	          switch ($cCriterio) {
	          	case "EQUAL":
	              for ($k=0;$k<count($mPatron);$k++) {
	              	if (in_array(strtoupper($mPatron[$k]),$mMatrizTmp[$i])) {
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
		          $mMatrizTra[$j] = $mMatrizTmp[$i]; $j++;
		        }
	        }
				} else {
					$mMatrizTra = $mMatrizTmp;
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
   			        <legend>Parametro Generals del Periodo Seleccionado (<?php echo count($mMatrizTra)?>)</legend>
     	         	<center>
       	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
           	        	<td class="clase08" width="14%">
            	        	<input type="text" class="letra" name = "cSearch" maxlength="50" value = "<?php echo $cSearch ?>" style= "width:80"
            	        		onblur="javascript:this.value=this.value.toUpperCase();
																						 document.forms['frgrm']['nLimInf'].value='00';
								      											 document.forms['frgrm']['cPaginas'].value='1'">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:pointer" title="Buscar"
								      		onClick = "javascript:document.forms['frgrm']['cBuscar'].value = 'ON'
								      											    document.forms['frgrm']['cSearch'].value=document.forms['frgrm']['cSearch'].value.toUpperCase();
								      												  document.forms['frgrm'].submit()">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:pointer" title="Mostrar Todo"
								      		onClick ="javascript:document.forms['frgrm']['cSearch'].value='';
								      												 document.forms['frgrm']['nLimInf'].value='00';
								      												 document.forms['frgrm']['nLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												 document.forms['frgrm']['cPaginas'].value='1';
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
       	       						<?php if ($cPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value++;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value='<?php echo ceil(count($mMatrizTra)/$nLimSup) ?>';
								      				    						 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
       	       						<?php } ?>
       	       						<?php if ($cPaginas > "1" && $cPaginas < ceil(count($mMatrizTra)/$nLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value='1';
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value--;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value++;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value='<?php echo ceil(count($mMatrizTra)/$nLimSup) ?>';
								      				    						 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
	       	       					<?php } ?>
       	       						<?php if ($cPaginas == ceil(count($mMatrizTra)/$nLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value='1';
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value--;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
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
												<select Class = "letrase" name = "cPaginas" value = "<?php echo $cPaginas ?>" style = "width:60%"
       	       						onchange="javascript:this.id = 'ON';
								      												 document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(this.value-1));
       	       						                     document.forms['frgrm'].submit()">
													<?php for ($i=0;$i<ceil(count($mMatrizTra)/$nLimSup);$i++) {
														if ($i+1 == $cPaginas) { ?>
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

													while ($xRBA = mysql_fetch_array($xBotAcc)) {
														switch ($xRBA['menopcxx']) {
															case "EDITAR": ?>
																<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_edit.png" onClick = "javascript:f_Editar('<?php echo $xRBA['menopcxx'] ?>')" style = "cursor:pointer" title="Editar, Solo Uno">
															<?php break;
															case "ANULAR": ?>
																<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:f_Anular('<?php echo $xRBA['menopcxx'] ?>')" style = "cursor:pointer" title="Cambiar Estado, Solo Uno">
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
         	         		<td class="name" width="30%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','stridxxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','stridxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','stridxxx','')">Id</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "stridxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','stridxxx','')</script>
           	         	</td>
           	         	<td class="name" width="30%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','strvlrxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','strvlrxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','strvlrxx','')">Valor</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "strvlrxx">
           	         		<script language="javascript">f_ButtonsAscDes('','strvlrxx','')</script>
           	         	</td>
           	         	<td class="name" width="30%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','strdesxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','strdesxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','strdesxx','')">Descripcion</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "strdesxx">
           	         		<script language="javascript">f_ButtonsAscDes('','strdesxx','')</script>
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
                 	    <td Class='name' width="02%" align="right">
                 	    	<input type="checkbox" name="oCheckAll" onClick = 'javascript:f_Marca()'>
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
		                      	<td class="letra7" width="30%"><a href = javascript:f_Ver('<?php echo $mMatrizTra[$i]['stridxxx']?>')><?php echo $mMatrizTra[$i]['stridxxx'] ?></a></td>
	       	                	<td class="letra7" width="30%"><?php echo $mMatrizTra[$i]['strvlrxx'] ?></td>
	       	                	<td class="letra7" width="30%"><?php echo substr($mMatrizTra[$i]['strdesxx'],0,40) ?></td>
	        	              	<td class="letra7" width="07%"><?php echo $mMatrizTra[$i]['regfcrex'] ?></td>
	          	              <td class="letra7" width="05%"><?php echo $mMatrizTra[$i]['reghcrex'] ?></td>
	          	              <td class="letra7" width="05%"><?php echo $mMatrizTra[$i]['regfmodx'] ?></td>
	          	              <td class="letra7" width="05%"><?php echo $mMatrizTra[$i]['reghmodx'] ?></td>
	          	              <td class="letra7" width="05%"><?php echo $mMatrizTra[$i]['regestxx'] ?></td>
	            	            <td Class="letra7" width="02%" align="right"><input type="checkbox" name="oCheck"  value = "<?php echo count($mMatrizTra) ?>"
	                   	    		id="<?php echo $mMatrizTra[$i]['stridxxx'].'~'.$mMatrizTra[$i]['strvlrxx'].'~'.$mMatrizTra[$i]['regestxx']?>"
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

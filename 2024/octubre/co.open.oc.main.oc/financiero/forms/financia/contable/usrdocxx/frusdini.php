<?php
  namespace openComex;
/**
 * Tracking  Terceros .
 * Este programa permite realizar consultas rapidas de las Compras a Terceros que se Encuentran en la Base de Datos.
 * @author
 * @package emisioncero
 */

	include("../../../../libs/php/utility.php");

	$cPerAno = date('Y');

	/* Busco en la 05 que Tiene Permiso el Usuario*/
  $qUsrMen  = "SELECT * ";
  $qUsrMen .= "FROM $cAlfa.sys00005 ";
  $qUsrMen .= "WHERE ";
  $qUsrMen .= "sys00005.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
  $qUsrMen .= "sys00005.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
  $qUsrMen .= "sys00005.menimgon <> '' ";
  $qUsrMen .= "ORDER BY sys00005.menordxx";
  $xUsrMen = f_MySql("SELECT","",$qUsrMen,$xConexion01,"");
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

    	function f_Ver(xUsrId) {
      	var cPathUrl = "frusdnue.php?gUsrId="+xUsrId;
      	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      	document.cookie="kMenDes=Ver Compra;path="+"/";
      	document.cookie="kModo=VER;path="+"/";
      	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	document.location = cPathUrl; // Invoco el menu.
	    }

	  	 function f_Editar(xModo) {
				switch (document.forms['frgrm']['vRecords'].value) {
					case "1":
						if (document.forms['frgrm']['oCheck'].checked == true) {
							var zMatriz = document.forms['frgrm']['oCheck'].id.split('~');
							var ruta = "frusdnue.php?gUsrId="+zMatriz[0];
      	      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      	      document.cookie="kMenDes=Editar Documentos Contables por Usuario;path="+"/";
      	      document.cookie="kModo="+xModo+";path="+"/";
      	      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	      document.location = ruta; // Invoco el menu.
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
							if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
								// Solo Deja Legalizar el Primero Seleccionado
								zSw_Prv = 1;
								var zMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
								var ruta = "frusdnue.php?gUsrId="+zMatriz[0];
      	      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      	      document.cookie="kMenDes=Editar Documentos Contables por Usuario;path="+"/";
      	      document.cookie="kModo="+xModo+";path="+"/";
      	      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	      document.location = ruta; // Invoco el menu.
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
		  <form name = "frestado" action = "frusdgra.php" method = "post" target="fmpro">
  			<input type = "hidden" name = "cComId"  value = "">
		  </form>

		  <form name = "frgrm" action = "frusdini.php" method = "post" target="fmwork">
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
												  $xUsrPer = f_MySql("SELECT","",$qUsrPer,$xConexion01,"");
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
				}elseif ($vLimInf == "") {
				  $vLimInf = "00";
				}

				if ($vPaginas == "") {
        	$vPaginas = "1";
				}

        /**INICIO SQL**/
        $qUsrDat  = "SELECT ";
				$qUsrDat .= "SQL_CALC_FOUND_ROWS ";
				$qUsrDat .= "$cAlfa.SIAI0003.USRIDXXX,";
				$qUsrDat .= "$cAlfa.SIAI0003.USRID2XX,";
				$qUsrDat .= "$cAlfa.SIAI0003.USRNOMXX,";
				$qUsrDat .= "$cAlfa.SIAI0003.REGFECXX,";
				$qUsrDat .= "$cAlfa.SIAI0003.REGHORXX,";
				$qUsrDat .= "$cAlfa.SIAI0003.REGESTXX ";
				$qUsrDat .= "FROM $cAlfa.SIAI0003 ";
				
        //////// LAS CONDICIONES PROPIAS DEL INI
				$qUsrDat .= "WHERE ";
				$qUsrDat .= "$cAlfa.SIAI0003.REGESTXX = \"ACTIVO\" AND ";
				//// CODIGO NUEVO PARA REEEMPLAZAR EL {$_POST['vSearch']}
				$qUsrDat .= "(";
				$qUsrDat .= "$cAlfa.SIAI0003.USRIDXXX LIKE \"%{$_POST['vSearch']}%\" OR ";
				$qUsrDat .= "$cAlfa.SIAI0003.USRID2XX LIKE \"%{$_POST['vSearch']}%\" OR ";
				$qUsrDat .= "$cAlfa.SIAI0003.USRNOMXX LIKE \"%{$_POST['vSearch']}%\" OR ";
				$qUsrDat .= "$cAlfa.SIAI0003.REGFECXX LIKE \"%{$_POST['vSearch']}%\" OR ";
				$qUsrDat .= "$cAlfa.SIAI0003.REGHORXX LIKE \"%{$_POST['vSearch']}%\" OR ";
				$qUsrDat .= "$cAlfa.SIAI0003.REGESTXX LIKE \"%{$_POST['vSearch']}%\") ";
        /***** FIN SQL *****/
        //// CODIGO NUEVO PARA ORDER BY
        $cOrderBy = "";
        $vOrderByOrder = explode("~",$cOrderByOrder);
        for ($z=0;$z<count($vOrderByOrder);$z++) {
          if ($vOrderByOrder[$z] != "") {
            if ($_POST[$vOrderByOrder[$z]] != "") {
              $cOrderBy .= $_POST[$vOrderByOrder[$z]];
            }
          }
        }
        if (strlen($cOrderBy)>0) {
        	$cOrderBy = substr($cOrderBy,0,strlen($cOrderBy)-1);
        	$cOrderBy = "ORDER BY ".$cOrderBy;
        } else {
          $cOrderBy = "ORDER BY $cAlfa.SIAI0003.REGFECXX DESC,  $cAlfa.SIAI0003.REGHORXX DESC";
        }
        //// FIN CODIGO NUEVO PARA ORDER BY
        $qUsrDat .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
				$cIdCountRow = mt_rand(1000000000, 9999999999);
				$xUsrDat = mysql_query($qUsrDat, $xConexion01, true, $cIdCountRow);

			  //f_Mensaje(__FILE__,__LINE__,$qUsrDat);
				
				$xNumRows = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD", $xConexion01, false);
				$xRNR     = mysql_fetch_array($xNumRows);
				$xRNR     = $xRNR['CANTIDAD'];

			?>
      <center>
       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Registros Seleccionados (<?php echo $xRNR ?>)</legend>
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
    				      												  	if (document.forms['frgrm']['vPaginas'].id == 'ON') {
    				      												  	  document.forms['frgrm']['vPaginas'].id = 'OFF'
    				      												  	} else {
    				      												  	  document.forms['frgrm']['vPaginas'].value='1';
    				      												  	};
    					      												  document.forms['frgrm'].submit()">
            	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:hand" title="Mostrar Todo"
    					      		onClick ="javascript:document.forms['frgrm']['vSearch'].value='';
    					      												 document.forms['frgrm']['vLimInf'].value='00';
    					      												 document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
    					      												 document.forms['frgrm']['vPaginas'].value='1';
    					      												 document.forms['frgrm']['vSortField'].value='';
    					      												 document.forms['frgrm']['vSortType'].value='';
    					      												 document.forms['frgrm']['vTimes'].value='';
    					      												 document.forms['frgrm']['cOrderByOrder'].value='';
    					      												 document.forms['frgrm'].submit()">
                  	  </td>
       	       				<td class="name" width="03%" align="left">Filas&nbsp;
       	       					<input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
      					      		onblur = "javascript:f_FixFloat(this);
    					      												 	 document.forms['frgrm']['vLimInf'].value='00'; ">
       	       				</td>
       	       				<td class="name" width="06%" align="center">
       	       					<?php if (ceil($xRNR/$vLimSup) > 1) { ?>
       	       						<?php if ($vPaginas == "1") { ?>
      											<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
           	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
           	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.frgrm.vPaginas.value++;
      					      												 			document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
      					      												 			document.frgrm.submit()">
           	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.frgrm.vPaginas.value='<?php echo ceil($xRNR/$vLimSup) ?>';
      					      				    						 			document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
      					      												 			document.frgrm.submit()">
       	       						<?php } ?>
       	       						<?php if ($vPaginas > "1" && $vPaginas < ceil($xRNR/$vLimSup)) { ?>
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
       	       								onClick = "javascript:document.frgrm.vPaginas.value='<?php echo ceil($xRNR/$vLimSup) ?>';
      					      				    						 			document.frgrm.vLimInf.value=('<?php echo $vLimSup ?>'*(document.frgrm.vPaginas.value-1));
      					      												 			document.frgrm.submit()">
         	       					<?php } ?>
       	       						<?php if ($vPaginas == ceil($xRNR/$vLimSup)) { ?>
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
      					      												 document.forms['frgrm'].submit();">
      										<?php for ($i=0;$i<ceil($xRNR/$vLimSup);$i++) {
      											if ($i+1 == $vPaginas) { ?>
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
  															case "EDITAR": ?>
  																<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_edit.png" onClick = "javascript:f_Editar('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
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
           	         		<td class="name" width="20%">
             	         		<a href = "javascript:f_Order_By('onclick','USRIDXXX');" title="Ordenar">C&oacute;digo</a>&nbsp;
             	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "USRIDXXX">
             	         		<input type = "hidden" name = "USRIDXXX" value = "<?php echo $_POST['USRIDXXX'] ?>" id = "ABS(<?php echo $cAlfa ?>.SIAI0003.USRIDXXX)">
             	         		<script language="javascript">f_Order_By('','USRIDXXX')</script>
             	         	</td>
             	         	<td class="name" width="18%">
             	         		<a href = "javascript:f_Order_By('onclick','USRID2XX');" title="Ordenar">Director</a>&nbsp;
             	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "USRID2XX">
             	         		<input type = "hidden" name = "USRID2XX" value = "<?php echo $_POST['USRID2XX'] ?>" id = "<?php echo $cAlfa ?>.SIAI0003.USRID2XX">
             	         		<script language="javascript">f_Order_By('','USRID2XX')</script>
             	         	</td>
             	         	<td class="name" width="30%">
             	         		<a href = "javascript:f_Order_By('onclick','USRNOMXX');" title="Ordenar">Descripcion</a>&nbsp;
             	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "USRNOMXX">
             	         		<input type = "hidden" name = "USRNOMXX" value = "<?php echo $_POST['USRNOMXX'] ?>" id = "<?php echo $cAlfa ?>.SIAI0003.USRNOMXX">
             	         		<script language="javascript">f_Order_By('','USRNOMXX')</script>
             	         	</td>
             	         	<td class="name" width="10%">
             	         		<a href = "javascript:f_Order_By('onclick','REGFECXX');" title="Ordenar">Fecha</a>&nbsp;
             	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "REGFECXX">
             	         		<input type = "hidden" name = "REGFECXX" value = "<?php echo $_POST['REGFECXX'] ?>" id = "<?php echo $cAlfa ?>.SIAI0003.REGFECXX">
             	         		<script language="javascript">f_Order_By('','REGFECXX')</script>
             	         	</td>
             	         	<td class="name" width="10%">
             	         		<a href = "javascript:f_Order_By('onclick','REGHORXX');" title="Ordenar">Hora</a>&nbsp;
             	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "REGHORXX">
             	         		<input type = "hidden" name = "REGHORXX" value = "<?php echo $_POST['REGHORXX'] ?>" id = "<?php echo $cAlfa ?>.SIAI0003..REGHORXX">
             	         		<script language="javascript">f_Order_By('','REGHORXX')</script>
             	         	</td>
             	         	<td class="name" width="10%">
             	         		<a href = "javascript:f_Order_By('onclick','REGESTXX');" title="Ordenar">Estado</a>&nbsp;
             	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "REGESTXX">
             	         		<input type = "hidden" name = "REGESTXX" value = "<?php echo $_POST['REGESTXX'] ?>" id = "<?php echo $cAlfa ?>.SIAI0003.REGESTXX">
             	         		<script language="javascript">f_Order_By('','REGESTXX')</script>
             	         	</td>
                   	    <td Class='name' width="02%" align="right">
                   	    	<input type="checkbox" name="oChkComAll" onClick = 'javascript:f_Marca()'>
                   	    </td>
                   		</tr>
  								      <script languaje="javascript">
  												document.forms['frgrm']['vRecords'].value = "<?php echo mysql_num_rows($xUsrDat) ?>";
  											</script>
   	                    <?php

   	                     $y = 0;

   	                    while ($xRCC = mysql_fetch_array($xUsrDat)) {

   	                    	if ($y <= mysql_num_rows($xUsrDat)) { // Para Controlar el Error
  	 	                    	$zColor = "{$vSysStr['system_row_impar_color_ini']}";
  	                   	    if($y % 2 == 0) {
  	                   	    	$zColor = "{$vSysStr['system_row_par_color_ini']}";
  													} ?>
  													<!--<tr bgcolor = "<?php echo $zColor ?>">-->
  													<tr id="<?php echo $xRCC['USRIDXXX'] ?>" bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')"
  													  onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')"  >
  													  <td class="letra7"><a href = javascript:f_Ver('<?php echo $xRCC['USRIDXXX']?>')><?php echo $xRCC['USRIDXXX'] ?> </a></td>
  	       	                	<td class="letra7"><?php echo $xRCC['USRID2XX'] ?></td>
  		                      	<td class="letra7"><?php echo $xRCC['USRNOMXX'] ?></td>
                              <td class="letra7"><?php echo substr($xRCC['REGFECXX'],0,28) ?></td>
                              <td class="letra7"><?php echo substr($xRCC['REGHORXX'],0,28) ?></td>
  	          	              <td class="letra7"><?php echo $xRCC['REGESTXX'] ?></td>
  	            	            <td Class="letra7" align="right">
  	            	              <input type="checkbox" name="oCheck" value = "<?php echo mysql_num_rows($xUsrDat) ?>"
  	                   	    		id="<?php echo $xRCC['USRIDXXX'] ?>"
  	                   	    		onclick="javascript:document.forms['frgrm']['vRecords'].value='<?php echo mysql_num_rows($xUsrDat) ?>'">
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
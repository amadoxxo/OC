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
  $qUsrMen .= "sys00005.menimgon <> \"\" ";
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
     	function fnLink(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
       	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes="+xMenDes+";path="+"/";
       	document.cookie="kModo="+xOpcion;
       	//document.cookie="kModo="+xOpcion+";path="+"/";
       	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
       	document.location = xForm; // Invoco el menu.
      }

		 	function fnAnular() {
		 	  var nSel = 0; var cMsj = "";
		 	  
		 	  document.forms['frestado']['cComMemo'].value = "";
				switch (document.forms['frgrm']['nRecords'].value) {
					case "1":
						if (document.forms['frgrm']['oCheck'].checked == true) {
							var zMatriz = document.forms['frgrm']['oCheck'].id.split('~');
							if (zMatriz[3] == "SI") {
  								document.forms['frestado']['cComMemo'].value  = zMatriz[0] + '~' + zMatriz[1] + '~' + zMatriz[2] + '|';
  								nSel++;
  						}
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
							if (document.forms['frgrm']['oCheck'][i].checked == true ) {
								// Solo Deja Legalizar el Primero Seleccionado
								zSw_Prv++;
								var zMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
								if (zMatriz[3] == "SI") {
  									document.forms['frestado']['cComMemo'].value  += zMatriz[0] + '~' + zMatriz[1] + '~' + zMatriz[2] + '|';
  									nSel++;
  							} 
							}
						}
					break;
				}
				if (document.forms['frestado']['cComMemo'].value != "" && nSel > 0) {
					if (confirm("Esta Seguro de Anular la Autorizacion de Facturacion Anticipos y DOs Informativos sin Legalizar Do Seleccionados?")) {
					  document.cookie="kModo=ANULAR";
						document.forms['frestado'].submit();
					}
				} else {
				  if (nSel == "") {
				    alert("Debe Seleccionar un Do.");
				  }
				}
		 	}

     	function fnCargaVariable(xRecords) {
	  	  var zSw_Prv=0;
	  	  document.forms['frgrm']['cDo'].value = "";
	  		switch (xRecords) {
					case "1":
			  		if (document.forms['frgrm']['oCheck'].checked == true) {
							document.forms['frgrm']['cDo'].value = document.forms['frgrm']['oCheck'].value;
		  			}
	  			break;
	  			default:
			  		for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
			  			if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv==0) {
				  			document.forms['frgrm']['cDo'].value += document.forms['frgrm']['oCheck'][i].value;
				  			zSw_Prv=1;
		  				}
		  			}
	  			break;
	  		}
	  	}

	  	function fnMarca() {
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

	    function fnButtonsAscDes(xEvent,xSortField,xSortType) {
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
  	<style type="text/css">
      SELECT{ font-family: verdana; font-size: 10px; color:#2B547D; background-color:#D8E4F1;}
    </style>

  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
		<form name = "frestado" action = "fraslgra.php" method = "post" target="fmpro">
			<textarea  name = "cComMemo" id = "cComMemo" ></textarea>
			<script languaje = "javascript">
        document.getElementById("cComMemo").style.display="none";
      </script>
		</form>

    <form name = "frgrm" action = "fraslini.php" method = "post" target="fmwork">
   		<input type = "hidden" name = "nRecords"   value = "">
   		<input type = "hidden" name = "nLimInf"    value = "<?php echo $nLimInf ?>">
   		<input type = "hidden" name = "cSortField" value = "<?php echo $cSortField ?>">
   		<input type = "hidden" name = "cSortType"  value = "<?php echo $cSortType ?>">
   		<textarea name="cDo" id="cDo"></textarea>
   		<script languaje = "javascript">
        document.getElementById("cDo").style.display="none";
      </script>
   		
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
													  <td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory ?>/<?php echo $xRUM['menimgon'] ?>" style = "cursor:pointer" onClick ="javascript:fnLink('<?php echo $xRUM['modidxxx'] ?>','<?php echo $xRUM['proidxxx'] ?>','<?php echo $xRUM['menidxxx'] ?>','<?php echo $xRUM['menformx']?>','<?php echo $xRUM['menopcxx']?>','<?php echo $xRUM['mendesxx']?>')"><br>
				                    <a href = "javascript:fnLink('<?php echo $xRUM['modidxxx'] ?>','<?php echo $xRUM['proidxxx'] ?>','<?php echo $xRUM['menidxxx'] ?>','<?php echo $xRUM['menformx']?>','<?php echo $xRUM['menopcxx']?>','<?php echo $xRUM['mendesxx']?>')"
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
				if ($nLimInf == "" && $nLimSup == "") {
					$nLimInf = "00";
          $nLimSup = $vSysStr['system_rows_page_ini'];
				}

				if ($nPaginas == "") {
        	$nPaginas = "1";
				}

				/**
				 * Realizo la consulta de los formularios legalizados o no.
				 */
		   	$y=0;
				$qDoAut  = "SELECT ";
				$qDoAut .= "docidxxx, ";
				$qDoAut .= "docsufxx, ";
				$qDoAut .= "sucidxxx, ";
				$qDoAut .= "doctipxx, ";
				$qDoAut .= "docafasl, ";
				$qDoAut .= "cliidxxx, ";
				$qDoAut .= "regfcrex, ";
				$qDoAut .= "reghcrex, ";
				$qDoAut .= "regfmodx, ";
				$qDoAut .= "reghmodx, ";
				$qDoAut .= "regestxx ";
			  $qDoAut .= "FROM $cAlfa.sys00121 ";
				$qDoAut .= "WHERE ";
				$qDoAut .= "docafasl = \"SI\" AND ";
				$qDoAut .= "regestxx = \"ACTIVO\" ";
				$qDoAut .= "ORDER BY sucidxxx, docidxxx, docsufxx ";
				//wMenssage(__FILE__,__LINE__,$qDoAut);
				$xDoAut = mysql_query($qDoAut,$xConexion01);
				//f_Mensaje(__FILE__,__LINE__,$qDoAut." ~ ".mysql_num_rows($xDoAut));
				//wMenssage(__FILE__,__LINE__,mysql_num_rows($xDoAut));
				/* Cargo la Matriz con los ROWS del Cursor */
				$i=0;
				while ($xRDA = mysql_fetch_array($xDoAut)) {
				  /* Traigo el Nombre del Cliente del Do */
          $qCliente = "SELECT ";
          $qCliente .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) AS CLINOMXX ";
          $qCliente .= "FROM $cAlfa.SIAI0150 ";
          $qCliente .= "WHERE ";
          $qCliente .= "CLIIDXXX = \"{$xRDA['cliidxxx']}\" LIMIT 0,1";
          $xCliente = mysql_query($qCliente,$xConexion01);
          if (mysql_num_rows($xCliente) > 0) {
            $vRCli = mysql_fetch_array($xCliente);
            $xRDA['clinomxx'] = $vRCli['CLINOMXX'];
          } else {
            $xRDA['clinomxx'] = "SIN NOMBRE";
          }
          
					$zMatrizTmp[$i] = $xRDA;
					$i++;
				}
				/* Fin de Cargo la Matriz con los ROWS del Cursor */
				/* Recorro la Matriz para Traer Datos Externos */

				/***** Si el $cSearch Viene Cargado Busco el Patron en Todos los Campos de la Matriz *****/
				if ($cSearch != "") {
					$mMatrizTra = array();
					for ($i=0,$j=0;$i<count($zMatrizTmp);$i++) {
						$zArray = array_values($zMatrizTmp[$i]);
						for ($k=0;$k<count($zArray);$k++) {
							if (substr_count($zArray[$k],strtoupper($cSearch)) > 0) {
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

				if ($cSortField != "" && $cSortType != "") {
					$mMatrizTra = f_Sort_Array_By_Field($mMatrizTra,$cSortField,$cSortType);
				}
				/* Fin de Recorro la Matriz para Traer Datos Externos */
			?>
      <center>
        <script languaje="javascript">
					document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra) ?>";
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
            	        	<input type="text" class="letra" name = "cSearch" maxlength="20" value = "<?php echo $cSearch ?>" style= "width:80"
            	        		onblur="javascript:this.value=this.value.toUpperCase();
																						 document.forms['frgrm']['nLimInf'].value='00';
								      											 document.forms['frgrm']['nLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      											 document.forms['frgrm']['nPaginas'].value='1'
								      											 document.forms['frgrm'].submit()">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:hand" title="Buscar"
								      		onClick = "javascript:document.forms['frgrm']['cSearch'].value=document.forms['frgrm']['cSearch'].value.toUpperCase();
								      											 		document.forms['frgrm']['nLimInf'].value='00';
								      												  document.forms['frgrm']['nLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												  document.forms['frgrm']['nPaginas'].value='1'
								      												  document.forms['frgrm'].submit()">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:hand" title="Mostrar Todo"
								      		onClick ="javascript:document.forms['frgrm']['cSearch'].value='';
								      												 document.forms['frgrm']['nLimInf'].value='00';
								      												 document.forms['frgrm']['nLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												 document.forms['frgrm']['nPaginas'].value='1';
								      												 document.forms['frgrm']['cSortField'].value='';
								      												 document.forms['frgrm']['cSortType'].value='';
								      												 document.forms['frgrm'].submit()">
   	              	  </td>
       	       				<td class="name" width="08%" align="left">Filas&nbsp;
       	       					<input type="text" class="letra" name = "nLimSup" value = "<?php echo $nLimSup ?>" style="width:30;text-align:right"
								      		onfocus = "javascript:document.forms['frgrm']['nPaginas'].value='1'"
       	       						onblur = "javascript:uFixFloat(this);
								      												 document.forms['frgrm']['nLimInf'].value='00';
								      												 document.forms['frgrm'].submit()">
       	       				</td>
       	       				<td class="name" width="08%">
       	       					<?php if (ceil(count($mMatrizTra)/$nLimSup) > 1) { ?>
       	       						<?php if ($nPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:hand" title="Primera Pagina">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:hand" title="Pagina Anterior">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:hand" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value++;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:hand" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='<?php echo ceil(count($mMatrizTra)/$nLimSup) ?>';
								      				    						 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
       	       						<?php } ?>
       	       						<?php if ($nPaginas > "1" && $nPaginas < ceil(count($mMatrizTra)/$nLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='1';
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value--;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:hand" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value++;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:hand" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='<?php echo ceil(count($mMatrizTra)/$nLimSup) ?>';
								      				    						 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
	       	       					<?php } ?>
       	       						<?php if ($nPaginas == ceil(count($mMatrizTra)/$nLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='1';
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value--;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
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
												<select Class = "letrase" name = "nPaginas" value = "<?php echo $nPaginas ?>" style = "width:60%"
       	       						onchange="javascript:document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(this.value-1));
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
                              case "ANULAR": ?>
                                <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_cambest.gif" onClick = "javascript:fnCargaVariable(document.forms['frgrm']['nRecords'].value);fnAnular('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="<?php echo $mBotAcc['mendesxx'] ?>">
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
           	          <td class="name" width="15%">
           	         		<a href = "javascript:fnButtonsAscDes('onclick','docidxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:fnButtonsAscDes('onmouseover','docidxxx','')"
       	       					onmouseout="javascript:fnButtonsAscDes('onmouseout','docidxxx','')">DO</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docidxxx">
           	         		<script language="javascript">fnButtonsAscDes('','docidxxx','')</script>
          	          </td>
          	          <td class="name" width="10%">
           	         		<a href = "javascript:fnButtonsAscDes('onclick','doctipxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:fnButtonsAscDes('onmouseover','doctipxx','')"
       	       					onmouseout="javascript:fnButtonsAscDes('onmouseout','doctipxx','')">Tipo Operacion</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "doctipxx">
           	         		<script language="javascript">fnButtonsAscDes('','doctipxx','')</script>
          	          </td>
         	            <td class="name" width="12%">
           	         		<a href = "javascript:fnButtonsAscDes('onclick','cliidxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:fnButtonsAscDes('onmouseover','cliidxxx','')"
       	       					onmouseout="javascript:fnButtonsAscDes('onmouseout','cliidxxx','')">Nit</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "cliidxxx">
           	         		<script language="javascript">fnButtonsAscDes('','cliidxxx','')</script>
           	         	</td>
           	         	<td class="name" width="56%">
           	         		<a href = "javascript:fnButtonsAscDes('onclick','clinomxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:fnButtonsAscDes('onmouseover','clinomxx','')"
       	       					onmouseout="javascript:fnButtonsAscDes('onmouseout','clinomxx','')">Cliente</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "clinomxx">
           	         		<script language="javascript">fnButtonsAscDes('','clinomxx','')</script>
          	         	</td>
          	          <td class="name" width="05%">
           	         		<a href = "javascript:fnButtonsAscDes('onclick','regestxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:fnButtonsAscDes('onmouseover','regestxx','')"
       	       					onmouseout="javascript:fnButtonsAscDes('onmouseout','regestxx','')">Estado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
           	         		<script language="javascript">fnButtonsAscDes('','regestxx','')</script>
               	      </td>
               	      <td class="name" width="02%" align="right">
           	         		<input type="checkbox" name="oCheckAll" onClick = 'javascript:fnMarca()'>
           	         	</td>
             	     </tr>
					         <script languaje="javascript">
						          document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra) ?>";
						       </script>
 	                    <?php for($i=intval($nLimInf);$i<intval($nLimInf+$nLimSup);$i++) {
 	                    	if ($i < count($mMatrizTra)) { // Para Controlar el Error
	 	                    	$zColor = "{$vSysStr['system_row_impar_color_ini']}";
	                   	    if($y % 2 == 0) {
	                   	    	$zColor = "{$vSysStr['system_row_par_color_ini']}";
													} ?>
								        <!--<tr bgcolor = "<?php echo $zColor ?>">-->
								        <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
		                      	<td class="letra7"><?php echo $mMatrizTra[$i]['docidxxx'] ?></td>
		                      	<td class="letra7"><?php echo $mMatrizTra[$i]['doctipxx'] ?></td>
								            <td class="letra7"><?php echo substr($mMatrizTra[$i]['cliidxxx'],0,14);?></td>
	       	                	<td class="letra7"><?php echo $mMatrizTra[$i]['clinomxx'] ?></td>
	       	                	<td class="letra7"><?php echo $mMatrizTra[$i]['regestxx'] ?></td>
	        	              	<td class="letra7" align="right">
	        	              	  <input type="checkbox" name="oCheck"
													       value = "<?php echo count($mMatrizTra) ?>"
													       id = "<?php echo $mMatrizTra[$i]['sucidxxx'].'~'.$mMatrizTra[$i]['docidxxx'].'~'.$mMatrizTra[$i]['docsufxx'].'~'.$mMatrizTra[$i]['docafasl'].'~'.$mMatrizTra[$i]['regestxx'] ?>">
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
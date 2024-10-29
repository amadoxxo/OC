<?php
  namespace openComex;
	/**
	 * Control de formularios.
	 * --- Descripcion: Permite Legalizar, Anular y Liberar Formularios. .
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

	  	function f_Carga_Variable(xRecords) {
	  		var zSwitch = "0";
	  		document.forms['frgrm']['cComMemo'].value = "|";
	  		switch (xRecords) {
					case "1":
			  		if (document.forms['frgrm']['cCheck'].checked == true) {
							document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'].value;
							document.forms['frgrm']['cComMemo'].value += "|";
							document.forms['frgrm']['cChekeados'].value="1";
		  			}
	  			break;
	  			default:
	  				document.forms['frgrm']['cComMemo'].value = "|";
			  		for (i=0;i<document.forms['frgrm']['cCheck'].length;i++) {
			  			if (document.forms['frgrm']['cCheck'][i].checked == true) {
				  			document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'][i].value;
				  			document.forms['frgrm']['cComMemo'].value += "|";
				  			document.forms['frgrm']['cChekeados'].value="1";
		  				}
		  			}
		  		break;
		  	}
	  	}

	  	function f_Verificar_Check() {
  		  if(document.forms['frgrm']['cCheckAll'].checked == true)
	 			   document.forms['frgrm']['cChekeados'].value=1;
     	  if (document.forms['frgrm']['nRecords'].value == 1){
     	    if(document.forms['frgrm']['cCheck'].checked == true)
     	      document.forms['frgrm']['cChekeados'].value=1;
      	}else {
	      	if (document.forms['frgrm']['nRecords'].value > 1){
			     	for (i=0;i<document.forms['frgrm']['cCheck'].length;i++){
   	   	     	if(document.forms['frgrm']['cCheck'][i].checked == true){
   	   	     	  document.forms['frgrm']['cChekeados'].value=1;
   	   	     	  $i=document.forms['frgrm']['cCheck'].length;
   	   	     	}
			     	}
			    }
      	}
	 		}

	 		function f_Marca() {
       	if (document.forms['frgrm']['cCheckAll'].checked == true){
      	  if (document.forms['frgrm']['nRecords'].value == 1){
      	  	document.forms['frgrm']['cCheck'].checked=true;
      	  } else {
	      		if (document.forms['frgrm']['nRecords'].value > 1){
			      	for (i=0;i<document.forms['frgrm']['cCheck'].length;i++){
   	   	      	document.forms['frgrm']['cCheck'][i].checked = true;
			      	}
			      }
      	  }
      	} else {
	      	if (document.forms['frgrm']['nRecords'].value == 1){
      	  	document.forms['frgrm']['cCheck'].checked=false;
      	  } else {
      	  	if (document.forms['frgrm']['nRecords'].value > 1){
				      for (i=0;i<document.forms['frgrm']['cCheck'].length;i++){
				      	document.forms['frgrm']['cCheck'][i].checked = false;
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
							document.forms['frgrm'].submit();;
						} else {
							document.forms['frgrm'].submit();;
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

		<!--<form name = "frgrm" action = "frforcon.php" method = "post" target="fmwork">-->
		<form name = "frgrm" method="POST">
   		<input type = "hidden" name = "cChekeados" value = "">
  	  <input type = "hidden" name = "cComMemo"   value = "<?php echo $cComMemo ?>">
  	  <input type = "hidden" name = "gTipSav"    value = "">
  	  <input type = "hidden" name = "cEstado"    value = "">
   		<input type = "hidden" name = "nRecords"   value = "">
   		<input type = "hidden" name = "nLimInf"    value = "<?php echo $nLimInf ?>">
   		<input type = "hidden" name = "cSortField" value = "<?php echo $cSortField ?>">
   		<input type = "hidden" name = "cSortType"  value = "<?php echo $cSortType ?>">
   		<input type = "hidden" name = "cTimes"     value = "<?php echo $cTimes ?>">
      <?php
				if ($nLimInf == "" && $nLimSup == "") {
					$nLimInf = "00";
          $nLimSup = $vSysStr['system_rows_page_ini'];
				}

				if ($cPaginas == "") {
        	$cPaginas = "1";
				}

				/***** Si Viene Vacio el $cCcoId lo Cargo con la Cookie del Centro de Costo *****/
				/***** Si no Hago el SELECT con el Centro de Costo que me Entrega el Combo del INI *****/
				if ($cCcoId == "") {
        	$cCcoId  = $_COOKIE['kUsrCco'];
				} else {
					/***** Si el $cCcoId Viene Cargado del Combo con "ALL" es porque Debo Mostrar Todos los Centros de Costos *****/
					/***** Si no Dejo la Sucursal que Viene Cargada *****/
					if ($cCcoId == "ALL") {
						$cCcoId = "";
					}
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

				$qDatDir  = "SELECT * ";
				$qDatDir .= "FROM $cAlfa.SIAI0003 ";
				$qDatDir .= "WHERE ";
				$qDatDir .= "USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
				$xDatDir  = f_MySql("SELECT","",$qDatDir,$xConexion01,"");
				$xRDD = mysql_fetch_array($xDatDir);

				$qSysDoi   = "SELECT * FROM $cAlfa.sys00121  ";
				$qSysDoi .= "WHERE ";
  			if (($_POST['dDesde'] != "" && $_POST['dHasta'] != "") && ($_POST['dDesde'] <= $_POST['dHasta'])) {
					$qSysDoi .= "regfcrex BETWEEN \"{$_POST['dDesde']}\" AND \"{$_POST['dHasta']}\" AND ";
				} else {
					$_POST['dDesde'] = ""; $_POST['dHasta'] = "";
					$qSysDoi .= "regfcrex LIKE \"%%\" AND ";
				}
				$qSysDoi .= "sucidxxx LIKE \"%{$xRDD['sucidxxx']}%\" AND docforms <> \"\" AND regestxx = \"ACTIVO\" ";
				$qSysDoi .= "ORDER BY regfcrex DESC,reghcrex DESC ";
				$xSysDoi  = f_MySql("SELECT","",$qSysDoi,$xConexion01,"");

				/* Cargo la Matriz con los ROWS del Cursor */
				$i=0;
				while ($xRSD = mysql_fetch_array($xSysDoi)) {
					$mMatrizTmp[$i] = $xRSD;
					$i++;
				}

				for ($i=0;$i<count($mMatrizTmp);$i++) {
					/* Traigo el Nombre del Cliente */
					$qDatCli = "SELECT CLINOMXX FROM $cAlfa.SIAI0150 WHERE CLIIDXXX = \"{$mMatrizTmp[$i]['cliidxxx']}\" LIMIT 0,1";
					$xDatCli  = f_MySql("SELECT","",$qDatCli,$xConexion01,"");
					if (mysql_num_rows($xDatCli) > 0) {
						while ($xRDC = mysql_fetch_array($xDatCli)) {
							$mMatrizTmp[$i]['clinomxx'] = $xRDC['CLINOMXX'];
						}
					} else {
						$mMatrizTmp[$i]['clinomxx'] = "CLIENTE SIN NOMBRE";
					}
					/* Fin Traigo el Nombre del Cliente */
				}

				/* Fin de Cargo la Matriz con los ROWS del Cursor */
				/* Recorro la Matriz para Traer Datos Externos */
				/***** Si el $cSearch Viene Cargado Busco el Patron en Todos los Campos de la Matriz *****/

				if ($cSearch != "") {
					$mMatrizTra = array();
					for ($i=0,$j=0;$i<count($mMatrizTmp);$i++) {
						$vArray = array_values($mMatrizTmp[$i]);
						for ($k=0;$k<count($vArray);$k++) {
							if (substr_count($vArray[$k],strtoupper($cSearch)) > 0) {
								$k = count($vArray)+1;
								$mMatrizTra[$j] = $mMatrizTmp[$i];
								$j++;
							}
						}
					}
				} else {
					$mMatrizTra = $mMatrizTmp;
				}
				/***** Fin de Buscar Patron en la Matriz *****/

				if ($cSortField != "" && $cSortType != "") {
					$mMatrizTra = f_Sort_Array_By_Field($mMatrizTra,$cSortField,$cSortType);
				}
				/* Fin de Recorro la Matriz para Traer Datos Externos */
				/***** Extraigo el nombre del usuario *****/
				$qUsrNom = "SELECT USRNOMXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
				$xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
				$xRUN   = mysql_fetch_array($xUsrNom);
				$cNomUsr = $xRUN['USRNOMXX'];
				/***** Fin Extracción nombre del usuario General. *****/
			?>
      <center>
       <script languaje="javascript">
					document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra) ?>";
				 </script>
       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Registros en la Consulta (<?php echo count($mMatrizTra)?>) </legend>
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
								      											 document.forms['frgrm']['cPaginas'].value='1';
								      											 document.forms['frgrm'].submit();">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:pointer" title="Buscar"
								      		onClick = "javascript:document.forms['frgrm']['cSearch'].value=document.forms['frgrm']['cSearch'].value.toUpperCase();
								      											 		document.forms['frgrm']['nLimInf'].value='00';
								      												  document.forms['frgrm']['nLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												  document.forms['frgrm']['cPaginas'].value='1';
								      												  document.forms['frgrm'].submit();">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:pointer" title="Mostrar Todo"
								      		onClick ="javascript:document.forms['frgrm']['cSearch'].value='';
								      												 document.forms['frgrm']['nLimInf'].value='00';
								      												 document.forms['frgrm']['nLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												 document.forms['frgrm']['cPaginas'].value='1';
								      												 document.forms['frgrm']['cSortField'].value='';
								      												 document.forms['frgrm']['cSortType'].value='';
								      												 document.forms['frgrm']['dDesde'].value='';
								      												 document.forms['frgrm']['dHasta'].value='';
								      												 document.forms['frgrm'].submit();">
   	              	  </td>
       	       				<td class="name" width="08%" align="left">Filas&nbsp;
       	       					<input type="text" class="letra" name = "nLimSup" value = "<?php echo $nLimSup ?>" style="width:30;text-align:right"
								      		onfocus = "javascript:document.forms['frgrm']['cPaginas'].value='1'"
       	       						onblur = "javascript:f_FixFloat(this);
								      												 document.forms['frgrm']['nLimInf'].value='00';
								      												 document.forms['frgrm'].submit();">
       	       				</td>
       	       				<td class="name" width="08%">
       	       					<?php if (ceil(count($mMatrizTra)/$nLimSup) > 1) { ?>
       	       						<?php if ($cPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value++;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit();">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value='<?php echo ceil(count($mMatrizTra)/$nLimSup) ?>';
								      				    						 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit();">
       	       						<?php } ?>
       	       						<?php if ($cPaginas > "1" && $cPaginas < ceil(count($mMatrizTra)/$nLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value='1';
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit();">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value--;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit();">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value++;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit();">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value='<?php echo ceil(count($mMatrizTra)/$nLimSup) ?>';
								      				    						 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit();">
	       	       					<?php } ?>
       	       						<?php if ($cPaginas == ceil(count($mMatrizTra)/$nLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value='1';
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit();">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value--;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit();">
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
       	       						onchange="javascript:document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(this.value-1));
       	       						                     document.forms['frgrm'].submit();">
													<?php for ($i=0;$i<ceil(count($mMatrizTra)/$nLimSup);$i++) {
														if ($i+1 == $cPaginas) { ?>
															<option value = "<?php echo $i+1 ?>" selected><?php echo $i+1 ?></option>
														<?php } else { ?>
															<option value = "<?php echo $i+1 ?>"><?php echo $i+1 ?></option>
														<?php } ?>
													<?php } ?>
												</select>
       	       				</td>
       	       				<td class="name" width="10%">
       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_calendar.png" style = "cursor:pointer" title="Fecha Desde"
													onClick = "javascript:document.forms['frgrm']['cPaginas'].value='1';
																								show_calendar('frgrm.dDesde')">
       	       					<input type = "text" Class = "letra" style = "width:70%" name = "dDesde" value = "<?php echo $_POST['dDesde'] ?>" readonly
       	       						onfocus = "javascript:document.forms['frgrm']['cPaginas'].value='1'";>
       	       				</td>

       	       				<td class="name" width="10%" align="left">
       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_calendar.png" style = "cursor:pointer" title="Fecha Hasta"
													onClick = "javascript:document.forms['frgrm']['cPaginas'].value='1';
																								show_calendar('frgrm.dHasta')">
       	       					<input type = "text" Class = "letra" style = "width:70%" name = "dHasta" value = "<?php echo $_POST['dHasta'] ?>" readonly
       	       						onfocus = "javascript:document.forms['frgrm']['cPaginas'].value='1'";>
       	       				</td>

          	        	 <td Class="name" width="41%" align="right">
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
															case "ANULARTRA": ?>
															<!--
															<img src = "<?php echo $cPlesk_Skin_Directory ?>/page_go.png"
															  onClick = "javascript:document.cookie='kModo=ANULARTRA';
															                        document.forms['frgrm']['cComMemo'].value='';
															                        f_Carga_Variable(document.forms['frgrm']['nRecords'].value);
															                        document.forms['frgrm'].action='frforagr.php';
															                        document.forms['frgrm'].target='fmpro';
															                        document.forms['frgrm'].submit();" style = "cursor:pointer" title="Anula Asignacion" id="IdImg">
															-->
                              <input class="name" name="IdImg" type="button" value="Anular" style="cursor:pointer"
															  onClick = "javascript:/*document.cookie='kModo=ANULARTRA';
															                        document.forms['frgrm']['cComMemo'].value='';
															                        f_Carga_Variable(document.forms['frgrm']['nRecords'].value);*/
															                        f_Verificar_Check();
															                        f_Carga_Variable(document.forms['frgrm']['nRecords'].value);
															                        document.forms['frgrm'].action='frforagr.php';
															                        document.forms['frgrm'].target='fmpro';
															                        document.forms['frgrm'].submit();
															                        document.forms['frgrm'].action='';
															                        document.forms['frgrm'].target='';" style = "cursor:pointer" title="Anula Asignacion" id="IdImg">
                              <script languaje="javascript">
				                            if(document.forms['frgrm']['nRecords'].value ==0)
				                            {
				                              document.getElementById("IdImg").onclick="";
				                            }
			                         </script>
															<?php
															break;
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
         	         		<td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','sucidxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','sucidxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','sucidxxx','')">Suc</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "sucidxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','sucidxxx','')</script>
           	         	</td>
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
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','doctipxx','')">Tipo</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "doctipxx">
           	         		<script language="javascript">f_ButtonsAscDes('','doctipxx ','')</script>
           	         	</td>
           	         	<td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','cliidxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','cliidxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','cliidxxx','')">Nit</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "cliidxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','cliidxxx','')</script>
           	         	</td>
           	         	<td class="name" width="30%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','clinomxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','clinomxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','clinomxx','')">Cliente</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "clinomxx">
           	         		<script language="javascript">f_ButtonsAscDes('','clinomxx','')</script>
           	         	</td>
           	         	<td class="name" width="5%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','docforms','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','docforms','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','docforms','')">Asignados</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docforms">
           	         		<script language="javascript">f_ButtonsAscDes('','docforms ','')</script>
           	         	</td>
             	        <td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','regfcrex','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','regfcrex','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','regfcrex','')">Fecha</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfcrex">
           	         		<script language="javascript">f_ButtonsAscDes('','regfcrex','')</script>
             	        </td>
             	        <td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','reghcrex','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','reghcrex','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','reghcrex','')">Hora</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghcrex">
           	         		<script language="javascript">f_ButtonsAscDes('','reghcrex','')</script>
             	        </td>
             	        <td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','regestxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','regestxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','regestxx','')">Estado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
           	         		<script language="javascript">f_ButtonsAscDes('','regestxx','')</script>
               	      </td>
                 	    <td Class='name' width="5%" align="right">
                 	    	<input type="checkbox" name="cCheckAll" onClick = "javascript:f_Marca();f_Verificar_Check();f_Carga_Variable(document.forms['frgrm']['nRecords'].value);">
                 	    </td>
                 		</tr>
								      <script languaje="javascript">
												document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra) ?>";
											</script>
 	                    <?php for ($i=intval($nLimInf);$i<intval($nLimInf+$nLimSup);$i++) {
 	                    	if ($i < count($mMatrizTra)) { // Para Controlar el Error
	 	                    	$zColor = "{$vSysStr['system_row_impar_color_ini']}";
	                   	    if($y % 2 == 0) {
	                   	    	$zColor = "{$vSysStr['system_row_par_color_ini']}";
													} ?>
													<!--<tr bgcolor = "<?php echo $zColor ?>">-->
													<tr height="20" bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
		                      	<td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['sucidxxx'] ?></td>
		                      	<td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['docidxxx'] ?></td>
		                      	<td class="letra7" width="10%"><?php echo substr($mMatrizTra[$i]['doctipxx'],0,18) ?></td>
	        	              	<td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['cliidxxx'] ?></td>
	       	                	<td class="letra7" width="30%"><?php echo substr($mMatrizTra[$i]['clinomxx'],0,36) ?></td>
		                      	<td class="letra7" width="5%"><?php echo $mMatrizTra[$i]['docforms'] ?></td>
	       	                	<td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['regfcrex'] ?></td>
	        	              	<td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['reghcrex'] ?></td>
	        	              	<td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['regestxx'] ?></td>
	        	              	<td class="letra7" width="5%" align="right">
	        	              	  <input type="checkbox" name="cCheck"  value = "<?php echo $mMatrizTra[$i]['docidxxx'] ?>"
	        	              	    onclick="javascript:f_Verificar_Check();f_Carga_Variable(<?php echo count($mMatrizTra) ?>);">
	              	        </tr>
	                	    	<?php $y++;
 	                    	}
 	                    }
 	                    ?>
 	                    <?php switch ($_COOKIE['kModo']) {
			                  case "NUEVO":
				                  $qUsrNom = "SELECT USRNOMXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
				                  $xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
				                  $cUsrNom = "USUARIO SIN NOMBRE";
				                  while ($xRUN = mysql_fetch_array($xUsrNom)) {
					                  $cUsrNom = trim($xRUN['USRNOMXX']);
					                  ?>
					                  <script languaje = "javascript">
						                  document.forms['frgrm']['cUsrId'].value  = "<?php echo $_COOKIE['kUsrId'] ?>";
						                  document.forms['frgrm']['cUsrNom'].value = "<?php echo $cUsrNom ?>";
					                  </script>
				                    <?php
				                  }
			                  break;
			                  case "ANULARTRA":?>
				                  <script languaje = "javascript">
					                  document.forms['frgrm']['gTipSav'].value    = "ANULARTRA";
				                  </script>
			                    <?php
			                  break;
                      } ?>
                  </table>
                </center>
   	          </fieldset>
           	</td>
          </tr>
        </table>
      </center>
    </form>

    <?php
    // Inicia Codigo para Mantener los Check Prendidos sin importar lo que pase con el INI
    if (strlen($cComMemo) > 1) {
      // Cuando La Consulta genera mas de un registro
      if (count($mMatrizTra) > 1) {
        for ($i=0;$i<count($mMatrizTra);$i++) {
          $cValor = '|'.$mMatrizTra[$i]['docidxxx'].'|';
          if (strlen($cValor) > 1) {
            if (strstr($cComMemo,$cValor) == true) {
              //f_Mensaje(__FILE__,__LINE__,$cValor."   --   ".$i);
              ?>
              <script languaje="javascript">
                document.forms['frgrm']['cCheck']['<?php echo $i ?>'].checked = true;
              </script>
              <?php
            }
          }
        }
      // Cuando La Consulta genera solo uno (1) registro
      } elseif (count($mMatrizTra) == 1) {
        for ($i=0;$i<count($mMatrizTra);$i++) {
          $cValor = '|'.$mMatrizTra[$i]['docidxxx'].'|';
          if (strlen($cValor) > 1) {
            if (strstr($cComMemo,$cValor) == true) {
              //f_Mensaje(__FILE__,__LINE__,$cValor."   --   ".$i);
              ?>
              <script languaje="javascript">
                document.forms['frgrm']['cCheck'].checked = true;
              </script>
              <?php
            }
          }
        }
      }
    }
    // Fin Codigo para Mantener los Check Prendidos sin importar lo que pase con el INI
    ?>

	</body>
</html>
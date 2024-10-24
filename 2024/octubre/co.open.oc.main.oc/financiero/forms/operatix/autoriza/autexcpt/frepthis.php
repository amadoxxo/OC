<?php
  namespace openComex;
	/**
	 * Historico Autorizacion Excluir Conceptos de Pagos a Terceros
	 * --- Descripcion: Ver el Historico de Autorizacion para Excluir conceptos de Pagos a Terceros para Facturacion.
	 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
	 * @version 001
	 */
	include("../../../../libs/php/utility.php");
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


   	function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			}
		
		function f_Links(xLink,xSwitch,xIteration) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink){
					case "cDocNro":
						if(document.frgrm['cDocNro'].value == "") {
							xSwitch = "WINDOW";
						}
						if (xSwitch == "VALID") {
							var cRuta  = "frept121.php?gWhat=VALID&gFunction="+xLink+
							                          "&gDocNro="+document.frgrm['cDocNro'].value.toUpperCase();
							parent.fmpro.location = cRuta;
						} else {
							var nNx     = (nX-600)/2;
							var nNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
							var cRuta   = "frept121.php?gWhat=WINDOW&gFunction="+xLink+
							              "&gDocNro="+document.frgrm['cDocNro'].value.toUpperCase();
							cWindow = window.open(cRuta,"cWindow",zWinPro);
					  	cWindow.focus();
						}
			    break;
				}
			}
		
		function f_Limpiar(){
			  document.forms['frgrm']['cSucId'].value='';
			  document.forms['frgrm']['cDocNro'].value='';
			  document.forms['frgrm']['cDocSuf'].value='';
			  document.forms['frgrm']['dDesde'].value='<?php echo substr(date('Y-m-d'),0,8)."01";  ?>';
				document.forms['frgrm']['dHasta'].value='<?php echo date('Y-m-d');  ?>';
			  document.forms['frgrm']['cPeriodos'].value='20';
			  document.forms['frgrm'].submit();
		}
		
		function f_Buscar(){
			//Validando Fecha
			var nSwicht  = 0;
		  var msj = "\n";
		
		  var dDesde  = document.forms['frgrm']['dDesde'].value.replace('-','');
			var dHasta  = document.forms['frgrm']['dHasta'].value.replace('-','');
			var cSucId  = document.forms['frgrm']['cSucId'].value.toUpperCase();
      var cDocNro = document.forms['frgrm']['cDocNro'].value.toUpperCase();
      var cDocSuf = document.forms['frgrm']['cDocSuf'].value.toUpperCase();
		
			if (dDesde > dHasta) {
				nSwicht = 1;
				msj += "La Fecha Inicial Debe ser Menor a la Final.\n";
			}

			if(cDocNro != "" && cDocSuf == "") {
				nSwicht = 1;
		    msj += "Debe Seleccionar el Sufijo del DO.\n";
			}
		
		  if (nSwicht == 0) {
			 document.forms['frgrm'].submit();
		  } else {
			  alert(msj + "Verifique.")
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
  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
		<form name = "frgrm" action = "frepthis.php" method = "post" target="fmwork">
   		<input type = "hidden" name = "vRecords"   value = "">
   		<input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
   		<input type = "hidden" name = "vSortField" value = "<?php echo $vSortField ?>">
   		<input type = "hidden" name = "vSortType"  value = "<?php echo $vSortType ?>">
   		<input type = "hidden" name = "vBuscar"    value = "<?php echo $_POST['vBuscar'] ?>">
      <?php

				if ($vLimInf == "" && $vLimSup == "") {
					$vLimInf = "00";
          $vLimSup = $vSysStr['system_rows_page_ini'];
				}

				if ($vPaginas == "") {
        	$vPaginas = "1";
				}

			#Consulta de los do que ya fueron aperturados o cerrados
 	    $_POST['dDesde']    = ($_POST['dDesde'] <> "")?$_POST['dDesde']:substr(date('Y-m-d'),0,8)."01";
	 		$_POST['dHasta']    = ($_POST['dHasta'] <> "")?$_POST['dHasta']:date('Y-m-d');
	 		$_POST['cPeriodos'] = ($_POST['cPeriodos'] <> "")?$_POST['cPeriodos']:20;
 	    
 	    $cAnoIni = substr($_POST['dDesde'],0,4);
 	    $cAnoFin = substr($_POST['dHasta'],0,4);

 	    $cMeses = "";
 	    if($cAnoIni == $cAnoFin) {
 	    	for($i = substr($_POST['dDesde'],5,2); $i <= substr($_POST['dHasta'],5,2); $i++) {
 	    		$nMes = str_pad($i,2,"0",STR_PAD_LEFT);
 	    		$cMeses .= "docobept LIKE \"%$cAnoIni-$nMes%\" OR ";
 	    	}
 	    }else{
 	    	for($i = substr($_POST['dDesde'],5,2); $i <= 12; $i++) {
 	    		$nMes = str_pad($i,2,"0",STR_PAD_LEFT);
 	    		$cMeses .= "docobept LIKE \"%$cAnoIni-$nMes%\" OR ";
 	    	}
 	    	for($i = 1; $i <= substr($_POST['dHasta'],5,2); $i++) {
 	    		$nMes = str_pad($i,2,"0",STR_PAD_LEFT);
 	    		$cMeses .= "docobept LIKE \"%$cAnoFin-$nMes%\" OR ";
 	    	}
 	    }
 	    
 	    $cMeses = substr($cMeses,0,strlen($cMeses)-4);
 	    
 	    $qObsDo  = "SELECT  ";
	    $qObsDo .= "cliidxxx,";
	    $qObsDo .= "docobept,";
	    $qObsDo .= "sucidxxx,";
	    $qObsDo .= "docidxxx,";
	    $qObsDo .= "docsufxx ";
	    $qObsDo .= "FROM $cAlfa.sys00121 ";
	    $qObsDo .= "WHERE " ;
	    $qObsDo .= "docobept <> \"\" AND ";
	    if ($_POST['cDocNro'] !="") {
		    $qObsDo .= "sucidxxx = \"{$_POST['cSucId']}\" AND ";
		    $qObsDo .= "docidxxx = \"{$_POST['cDocNro']}\" AND ";
		    $qObsDo .= "docsufxx = \"{$_POST['cDocSuf']}\" ";
	    } else {
	     $qObsDo .= "$cMeses " ;
	    }
	    $qObsDo .= "ORDER BY sucidxxx, docidxxx, docsufxx";
	    $xObsDo  = f_MySql("SELECT","",$qObsDo,$xConexion01,"");
	    //f_Mensaje(__FILE__,__LINE__,$qObsDo."~".mysql_num_rows($xObsDo));
			
	    $mMatrizTmp = array();
	    # Cargo la Matriz con los ROWS del Cursor 
			while ($xRF = mysql_fetch_array($xObsDo)) {
				
				#Busco nombre del cliente
		    $qCliNom  = "SELECT ";
		    $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) <> \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
		    $qCliNom .= "FROM $cAlfa.SIAI0150 ";
		    $qCliNom .= "WHERE ";
		    $qCliNom .= "CLIIDXXX = \"{$xRF['cliidxxx']}\" LIMIT 0,1";
		    $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
		    
		    if (mysql_num_rows($xCliNom) > 0) {
		     $xRCN = mysql_fetch_array($xCliNom);
		     $xRF['clinomxx'] = $xRCN['clinomxx'];
		    } else {
		      $xRF['clinomxx'] = "CLIENTE SIN NOMBRE";
		    }
				
		    $mDatos = explode("|___",$xRF['docobept']);
				for($j=0;$j<count($mDatos);$j++) {
					if($mDatos[$j] <> "") {
					  //Explode para los datos internos de la observacion
					  $mAuxDat = explode("__",$mDatos[$j]);
					  
					  $nSw_Incluir = 0;
					  if ($_POST['cDocNro'] !="") {
					   $nSw_Incluir = 1;
					  } else {
					   //verificando que la fecha de la observacion este en el rango de busqueda
					   if (mktime(0,0,0,substr($mAuxDat[1],5,2),substr($mAuxDat[1],8,2),substr($mAuxDat[1],0,4)) >= mktime(0,0,0,substr($_POST['dDesde'],5,2),substr($_POST['dDesde'],8,2),substr($_POST['dDesde'],0,4)) &&
                 mktime(0,0,0,substr($mAuxDat[1],5,2),substr($mAuxDat[1],8,2),substr($mAuxDat[1],0,4)) <= mktime(0,0,0,substr($_POST['dHasta'],5,2),substr($_POST['dHasta'],8,2),substr($_POST['dHasta'],0,4))) {
               $nSw_Incluir = 1;
             }
					  }
					  
					 	if ($nSw_Incluir == 1) {							
						    #Busco el nombre
						    $qUsrNom  = "SELECT USRNOMXX ";
                $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
                $qUsrNom .= "WHERE ";
                $qUsrNom .= "USRIDXXX = \"{$mAuxDat[0]}\" AND ";
                $qUsrNom .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
                $xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
                //f_Mensaje(__FILE__,__LINE__,$qUsrNom."~".mysql_num_rows($xUsrNom));
                
                if (mysql_num_rows($xUsrNom) > 0) {
			            $xRUN = mysql_fetch_array($xUsrNom);
			            $cUsrNom= $xRUN['USRNOMXX'];
			          } else {
			            $cUsrNom = "USUARIO SIN NOMBRE";
			          }
			          
			      		$i=count($mMatrizTmp);
								$mMatrizTmp[$i] = $xRF;    
			          $mMatrizTmp[$i]['usrnomxx'] = $cUsrNom; 
			          $mMatrizTmp[$i]['fechaxxx'] = $mAuxDat[1]; 
			          $mMatrizTmp[$i]['horaxxxx'] = $mAuxDat[2]; 
			          $mMatrizTmp[$i]['observax'] = (substr_count($mAuxDat[3], ",") > 0) ? str_replace(",", ",  ", $mAuxDat[3]) : $mAuxDat[3];
						}
					}
				}
			}
			$mMatrizTra = $mMatrizTmp;
			/***** Fin de Buscar Patron en la Matriz *****/

			if ($vSortField != "" && $vSortType != "") {
				$mMatrizTra = f_Sort_Array_By_Field($mMatrizTra,$vSortField,$vSortType);
			} ?>
			<center>
        <table width="95%" cellspacing="0" cellpadding="0" border="0">
          <tr height="21">
            <td height="21">&nbsp;</td>
            <td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
          </tr>
        </table>
      </center>
      <center>
       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Registros del Periodo Seleccionado (<?php echo count($mMatrizTra)?>)</legend>
     	         	<center>
       	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
           	        	<td class="clase08" width="38%">
           	        		Seleccione el Do:&nbsp;&nbsp;
													<input type="text"   class="letra" name="cSucId"  style="width:30" value="<?php echo $_POST['cSucId']; ?>" readonly>
													<input type="text"   class="letra" name="cDocNro" style="width:80" value="<?php echo $_POST['cDocNro']; ?>"
													       onblur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';
													                            f_Links('cDocNro','VALID');
													                            this.value=this.value.toUpperCase();"
													       onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';
													                           document.forms['frgrm']['cSucId'].value='';
													                           document.forms['frgrm']['cDocNro'].value='';
													                           document.forms['frgrm']['cDocSuf'].value='';">
												<input type="text" class="letra"   name="cDocSuf" style="width:40" value="<?php echo $_POST['cDocSuf']; ?>" readonly>
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:pointer" title="Buscar"
								      		onClick = "javascript:document.forms['frgrm']['vLimInf'].value='00';
	                                              document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
                                                document.forms['frgrm']['vPaginas'].value='1';
                                                document.forms['frgrm']['vSortField'].value='';
                                                document.forms['frgrm']['vSortType'].value='';
									      		                    document.forms['frgrm']['vBuscar'].value = 'ON';
								      													f_Buscar();">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:pointer" title="Mostrar Todo"
								      		onClick ="javascript:document.forms['frgrm']['vLimInf'].value='00';
								      												 document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												 document.forms['frgrm']['vPaginas'].value='1';
								      												 document.forms['frgrm']['vSortField'].value='';
								      												 document.forms['frgrm']['vSortType'].value='';
								      												 document.forms['frgrm']['vBuscar'].value='';
								      												 f_Limpiar();">
   	              	  </td>
       	       				<td class="name" width="06%" align="left">Filas&nbsp;
       	       					<input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
       	       						onblur = "javascript:f_FixFloat(this);
								      												 document.forms['frgrm']['vLimInf'].value='00';">
       	       				</td>
       	       				<td class="name" width="08%">
       	       					<?php if (ceil(count($mMatrizTra)/$vLimSup) > 1) { ?>
       	       						<?php if ($vPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil(count($mMatrizTra)/$vLimSup) ?>';
								      				    						 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
       	       						<?php } ?>
       	       						<?php if ($vPaginas > "1" && $vPaginas < ceil(count($mMatrizTra)/$vLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='1';
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value--;
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil(count($mMatrizTra)/$vLimSup) ?>';
								      				    						 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
	       	       					<?php } ?>
       	       						<?php if ($vPaginas == ceil(count($mMatrizTra)/$vLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='1';
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value--;
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
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
												<select Class = "letrase" name = "vPaginas" value = "<?php echo $vPaginas ?>" style = "width:60%"
       	       						onchange="javascript:this.id = 'ON';
								      												 document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(this.value-1));
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
   	         	        <td class="clase08" style = "width:20%">
									  		<select class="letrase" size="1" name="cPeriodos" id="cPeriodos" style = "width:100%"
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
									  	</td>
									  	<td class="name" width="10%" align="center">
       	       					<input type = "text" Class = "letra" style = "width:70%;text-align:center" name = "dDesde" value = "<?php echo $_POST['dDesde']; ?>" readonly>
       	       				</td>
       	       				<td class="name" width="10%" align="center">
       	       					<input type = "text" Class = "letra" style = "width:70%;text-align:center" name = "dHasta" value = "<?php echo $_POST['dHasta']; ?>" readonly>
       	       				</td>
       	       			</tr>
 	     	         	</table>
 	   	         	</center>
   	         		<hr></hr>
     	       		<center>
       	     			<table cellspacing="0" width="100%">
         	         	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
         	         		<td class="name" width="12%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','docidxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','docidxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','docidxxx','')">DO</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docidxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','docidxxx','')</script>
           	         	</td>
           	         	<td class="name" width="15%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','usrnomxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','usrnomxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','usrnomxx','')">Usuario</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "usrnomxx">
           	         		<script language="javascript">f_ButtonsAscDes('','usrnomxx','')</script>
           	         	</td>
             	        <td class="name" width="08%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','fechaxxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','fechaxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','fechaxxx','')">Fecha</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "fechaxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','fechaxxx','')</script>
             	        </td>
             	        <td class="name" width="05%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','horaxxxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','horaxxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','horaxxxx','')">Hora</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "horaxxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','horaxxxx','')</script>
             	        </td>
             	        <td class="name" width="20%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','clinomxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','clinomxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','clinomxx','')">Cliente</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "clinomxx">
           	         		<script language="javascript">f_ButtonsAscDes('','clinomxx','')</script>
             	        </td>
             	        <td class="name" width="40%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','observax','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','observax','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','observax','')">Documentos Excluidos</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "observax">
           	         		<script language="javascript">f_ButtonsAscDes('','observax','')</script>
             	        </td>
                 		</tr> 	
                 		<?php 
                 			for ($i=intval($vLimInf);$i<intval($vLimInf+$vLimSup);$i++) {
 	                    	if ($i < count($mMatrizTra)) { // Para Controlar el Error
	 	                    	$cColor = "{$vSysStr['system_row_impar_color_ini']}";
	                   	    if($y % 2 == 0) {
	                   	    	$cColor = "{$vSysStr['system_row_par_color_ini']}";
													} ?>
													<tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
		                      	<td class="letra7"><?php echo $mMatrizTra[$i]['sucidxxx']."-".$mMatrizTra[$i]['docidxxx']."-".$mMatrizTra[$i]['docsufxx'] ?></td>
                          	<td class="letra7"><?php echo $mMatrizTra[$i]['usrnomxx'] ?></td>
	                          <td class="letra7"><?php echo $mMatrizTra[$i]['fechaxxx'] ?></td>
	                          <td class="letra7"><?php echo $mMatrizTra[$i]['horaxxxx'] ?></td>
	                          <td class="letra7"><?php echo $mMatrizTra[$i]['clinomxx'] ?></td>
	                          <td class="letra7"><?php echo utf8_encode($mMatrizTra[$i]['observax']) ?></td>
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
			<script language="javascript">
				document.forms['frgrm']['cPeriodos'].value= "<?php echo $_POST['cPeriodos'] ?>";
				if ("<?php echo $_POST['cPeriodos'] ?>" == '99') {
					document.forms['frgrm']['dDesde'].readOnly = false;
					document.forms['frgrm']['dHasta'].readOnly = false;
				} else {
					document.forms['frgrm']['dDesde'].readOnly = true;
					document.forms['frgrm']['dHasta'].readOnly = true;
				}
			</script>
    </form>
	</body>
</html>
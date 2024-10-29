<?php
  namespace openComex;
   /**
	 * Imprimir Autorizacion Formulario al Gasto
	 * --- Descripcion: Me lista todas observaciones digitadas para autorizacion Formulario al Gasto.
	 * @author Johana Arboleda Ramos <dp1@opentecnologia.com.co>
	 * @version 002
	 */
	include("../../../../libs/php/utility.php");

?>
<html>
	<head>
		<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
		<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
		<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
   	<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
		<script language="javascript">
			function f_Imprimir_Autorizacion_Gasto() {
				if (document.forms['frgrm']['nRecords'].value!="0"){
					var zX      = screen.width;
					var zY      = screen.height;
					var alto = zY-80;
					var ancho = zX-100;
					var zNx     = (zX-ancho)/2;
					var zNy     = (zY-alto)/2;
					var zWinPro = 'width='+ancho+',height='+alto+',left='+zNx+',top='+zNy;
					
					switch (document.forms['frgrm']['nRecords'].value) {
						case "1":
							if (document.forms['frgrm']['cCheck'].checked == true) {
								var zMatriz = document.forms['frgrm']['cCheck'].id.split('~');
								var zRuta = 'frforgpr.php?cObsCsc='+zMatriz[0];
								zWindow = window.open(zRuta,'zWindow',zWinPro);
							}
						break;
						default:
							var zSw_Prv = 0;
							for (i=0;i<document.forms['frgrm']['cCheck'].length;i++) {
								if (document.forms['frgrm']['cCheck'][i].checked == true && zSw_Prv == 0) {
									zSw_Prv = 1;
									var zMatriz = document.forms['frgrm']['cCheck'][i].id.split('~');
									var zRuta = 'frforgpr.php?cObsCsc='+zMatriz[0];
									zWindow = window.open(zRuta,'zWindow',zWinPro);
								}
							}
						break;
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
		<form name = "frgrm" action = "frgasafg.php" method = "post" target="fmwork">
   		<input type = "hidden" name = "cChekeados" value = "">
  	  <input type = "hidden" name = "cComMemo"   value = "<?php echo $cComMemo ?>" style="width:800">
  	  <input type = "hidden" name = "gTipSav"    value = "">
  	  <input type = "hidden" name = "cEstado"    value = "">
   		<input type = "hidden" name = "nRecords"   value = "">
   		<input type = "hidden" name = "nLimInf"    value = "<?php echo $nLimInf ?>">
   		<input type = "hidden" name = "cSortField" value = "<?php echo $cSortField ?>">
   		<input type = "hidden" name = "cSortType"  value = "<?php echo $cSortType ?>">
   		<input type = "hidden" name = "cTimes"     value = "<?php echo $cTimes ?>">
   		<input type = "hidden" name = "cObserv"    value = "">
 	    <?php

				if ($nLimInf == "" && $nLimSup == "")
				 {
					$nLimInf = "00";
          $nLimSup = $vSysStr['system_rows_page_ini'];
				}

				if ($vPaginas == "")
				{
        	$vPaginas = "1";
				}

				/***** Si Viene Vacio el $vUsrId lo Cargo con la Cookie del Usuario *****/
				/***** Si no Hago el SELECT con el Usuario que me Entrega el Combo de INI *****/
				if ($vUsrId == "")
				{
        	$vUsrId = $_COOKIE['kUsrId'];
				} else {
					/***** Si el $vUsrId Viene Cargado del Combo con "ALL" es porque Debo Mostrar Todos los Usuarios *****/
					/***** Si no Dejo el Usuario que Viene Cargado *****/
					if ($vUsrId == "ALL") {
						$vUsrId = "";
					}
				}
				/**
				 * Realizo la consulta de los formularios que solo tengan estado PRVGASTO.
				 */

				if ($cSearch !="") {
			   	$y=0;
					$qFoiDat  = "SELECT DISTINCT  ";
					$qFoiDat .= "$cAlfa.ffob0000.obscscxx, ";
					$qFoiDat .= "$cAlfa.ffob0000.gofidxxx, ";
					$qFoiDat .= "$cAlfa.ffob0000.obsobsxx, ";
					$qFoiDat .= "$cAlfa.ffob0000.regfcrex, ";
					$qFoiDat .= "$cAlfa.ffob0000.reghcrex, ";
					$qFoiDat .= "$cAlfa.ffob0000.regfmodx, ";
					$qFoiDat .= "$cAlfa.ffob0000.reghmodx, ";
					$qFoiDat .= "$cAlfa.ffob0000.regestxx, ";					
					$qFoiDat .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"DIRECTOR SIN NOMBRE\") AS usrnomxx, ";
					$qFoiDat .= "IF($cAlfa.fpar0123.gofdesxx <> \"\",$cAlfa.fpar0123.gofdesxx,\"SIN DESCRIPCION\") AS gofdesxx ";
				  $qFoiDat .= "FROM $cAlfa.ffob0000 ";
				  $qFoiDat .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.ffob0000.diridxxx = $cAlfa.SIAI0003.USRIDXXX ";
				  $qFoiDat .= "LEFT JOIN $cAlfa.fpar0123 ON $cAlfa.ffob0000.gofidxxx = $cAlfa.fpar0123.gofidxxx AND $cAlfa.fpar0123.goftipxx = \"FORMULARIOS\" ";
					$qFoiDat .= "WHERE " ;
					$qFoiDat .= "$cAlfa.ffob0000.obstipxx = \"AUTPRVGASTO\" AND ";
					$qFoiDat .= "(" ;
					//El usuario puede buscar por producto, serial, consecutivo, grupo observacion, observacion, director, sucursal y do
					$qFoiDat .= "$cAlfa.ffob0000.ptoidxxx LIKE \"%$cSearch%\" OR ";
	  			$qFoiDat .= "$cAlfa.ffob0000.seridxxx LIKE \"%$cSearch%\" OR ";
				  $qFoiDat .= "$cAlfa.ffob0000.obscscxx LIKE \"%$cSearch%\" OR ";
				  $qFoiDat .= "IF($cAlfa.fpar0123.gofdesxx <> \"\",$cAlfa.fpar0123.gofdesxx,\"SIN DESCRIPCION\") LIKE \"%$cSearch%\" OR ";
				  $qFoiDat .= "$cAlfa.ffob0000.obsobsxx LIKE \"%$cSearch%\" OR ";
				  $qFoiDat .= "$cAlfa.ffob0000.diridxxx LIKE \"%$cSearch%\" OR ";
				  $qFoiDat .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"DIRECTOR SIN NOMBRE\") LIKE \"%$cSearch%\" OR ";
				  $qFoiDat .= "$cAlfa.ffob0000.docsucxx LIKE \"%$cSearch%\" OR ";
				  $qFoiDat .= "$cAlfa.ffob0000.docnroxx LIKE \"%$cSearch%\" OR ";
				  $qFoiDat .= "$cAlfa.ffob0000.regfcrex LIKE \"%$cSearch%\" OR ";
					$qFoiDat .= "$cAlfa.ffob0000.reghcrex LIKE \"%$cSearch%\" OR ";
					$qFoiDat .= "$cAlfa.ffob0000.regfmodx LIKE \"%$cSearch%\" OR ";
					$qFoiDat .= "$cAlfa.ffob0000.reghmodx LIKE \"%$cSearch%\" OR ";
					$qFoiDat .= "$cAlfa.ffob0000.regestxx LIKE \"%$cSearch%\" ";
					$qFoiDat .= ")" ;
					$qFoiDat .= "ORDER BY ABS($cAlfa.ffob0000.obscscxx) DESC ";
					$xFoiDat  = f_MySql("SELECT","",$qFoiDat,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qFoiDat."~".mysql_num_rows($xFoiDat));
					
					/* Cargo la Matriz con los ROWS del Cursor */
					$i=0;
					while ($xRFD = mysql_fetch_array($xFoiDat)) {
						$mMatrizTmp[$i] = $xRFD;
						//f_Mensaje(__FILE__,__LINE__,$mMatrizTmp[$i]['ptoidxxx']." ~ ".$mMatrizTmp[$i]['seridxxx']);
						$i++;
					}
					/* Fin de Cargo la Matriz con los ROWS del Cursor */
					/* Recorro la Matriz para Traer Datos Externos */
				}

				$mMatrizTra = $mMatrizTmp;
				if ($cSortField !="" && $cSortType !="") {
					$mMatrizTra = f_Sort_Array_By_Field($mMatrizTra,$cSortField,$cSortType);
				}
				/* Fin de Recorro la Matriz para Traer Datos Externos */
			?>
        <script language="javascript">
					document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra) ?>";
				</script>
				<center>

				<br></br>


       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Autorizaciones de Formularios al Gasto (<?php echo count($mMatrizTra) ?>)</legend>
     	       		<center>
     	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
         	      			<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
           	        	<td class="clase08" width="20%">
            	        	<input type="text" class="letra" name = "cSearch" maxlength="20" value = "<?php echo $cSearch ?>" style= "width:80"
            	        		onblur="javascript:this.value=this.value.toUpperCase();
																						 /*document.forms['frgrm']['nLimInf'].value='00';
								      											 document.forms['frgrm']['nLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      											 document.forms['frgrm']['cPaginas'].value='1'*/
								      											 document.forms['frgrm'].submit()">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:hand" title="Buscar"
								      		onClick = "javascript:document.forms['frgrm']['cSearch'].value=document.forms['frgrm']['cSearch'].value.toUpperCase();
								      											 		/*document.forms['frgrm']['nLimInf'].value='00';
								      												  document.forms['frgrm']['nLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												  document.forms['frgrm']['cPaginas'].value='1'*/
								      												  document.forms['frgrm'].submit()">

              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/spamfilter_on.gif" style = "cursor:hand" title="Nueva Busqueda"
								      		onClick ="javascript:document.forms['frgrm']['cSearch'].value='';
								      												 document.forms['frgrm']['nLimInf'].value='00';
								      												 document.forms['frgrm']['nLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												 document.forms['frgrm']['cPaginas'].value='1';
								      												 document.forms['frgrm']['cSortField'].value='';
								      												 document.forms['frgrm']['cSortType'].value='';
								      												 document.forms['frgrm'].submit()">
   	              	  </td>

       	       				<td class="name" width="08%" align="left">Filas&nbsp;
       	       					<input type="text" class="letra" name = "nLimSup" value = "<?php echo $nLimSup ?>" style="width:30;text-align:right"
								      		onfocus = "javascript:document.forms['frgrm']['cPaginas'].value='1'"
       	       						onblur = "javascript:f_FixFloat(this);
								      												 document.forms['frgrm']['nLimInf'].value='00';
								      												 document.forms['frgrm'].submit()">
       	       				</td>
       	       				<td class="name" width="08%">
       	       					<?php if (ceil(count($mMatrizTra)/$nLimSup) > 1) { ?>
       	       						<?php if ($cPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:hand" title="Primera Pagina">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:hand" title="Pagina Anterior">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:hand" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value++;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:hand" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value='<?php echo ceil(count($mMatrizTra)/$nLimSup) ?>';
								      				    						 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
       	       						<?php } ?>
       	       						<?php if ($cPaginas > "1" && $cPaginas < ceil(count($mMatrizTra)/$nLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value='1';
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value--;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:hand" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value++;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:hand" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value='<?php echo ceil(count($mMatrizTra)/$nLimSup) ?>';
								      				    						 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
	       	       					<?php } ?>
       	       						<?php if ($cPaginas == ceil(count($mMatrizTra)/$nLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:hand" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value='1';
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:hand" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['cPaginas'].value--;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['cPaginas'].value-1));
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
												<select Class = "letrase" name = "cPaginas" value = "<?php echo $cPaginas ?>" style = "width:60%"
       	       						onchange="javascript:document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(this.value-1));
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
 											<td Class="name" width="15%" align="right">
 												<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_print.png" onClick = "javascript:f_Imprimir_Autorizacion_Gasto();"
 													style = "cursor:pointer" title="Imprimir Autorizacion Formulario al Gasto">
 											</td>
             	      </tr>
         	      	</table>
         	      	</br>
       	     			<table cellspacing="0" width="100%">
         	         <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
         	         		<td class="name" width="05%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','obscscxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','obscscxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','obscscxx','')">Csc.</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "obscscxx">
           	         		<script language="javascript">f_ButtonsAscDes('','obscscxx','')</script>
          	          </td>
          	          <td class="name" width="16%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','usrnomxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','usrnomxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','usrnomxx','')">Director</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "usrnomxx">
           	         		<script language="javascript">f_ButtonsAscDes('','usrnomxx','')</script>
           	         	</td>
           	         	<td class="name" width="26%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','gofdesxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','gofdesxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','gofdesxx','')">Grupo</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "gofdesxx">
           	         		<script language="javascript">f_ButtonsAscDes('','gofdesxx','')</script>
          	         	</td>
          	          <td class="name" width="26%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','obsobsxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','obsobsxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','obsobsxx','')">Observaciones</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "obsobsxx">
           	         		<script language="javascript">f_ButtonsAscDes('','obsobsxx','')</script>
          	          </td>
          	         	<td class="name" width="05%">
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
          	         	<td class="name" width="02%" align="right">
           	         		<input type="checkbox" name="cCheckAll" onClick = 'javascript:f_Marca();'>
           	         	</td>
             	        </tr>
             	        <tr bgcolor = 'white'>
             	        <td class="name" width="05%" colspan="10">&nbsp;</td>
										</tr>
					         <script language="javascript">
						          document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra); ?>";
						       </script>
						       
 	                 <?php 
 	                    for($i=intval($nLimInf);$i<count($mMatrizTra);$i++) {
 	                    	if ($i < count($mMatrizTra)) { // Para Controlar el Error
	 	                    	$zColor = "{$vSysStr['system_row_impar_color_ini']}";
	                   	    if($y % 2 == 0) {
	                   	    	$zColor = "{$vSysStr['system_row_par_color_ini']}";
													}
													?>
								        <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
										        <td class="letra7"><?php echo $mMatrizTra[$i]['obscscxx'] ?></td>
										        <td class="letra7"><?php echo $mMatrizTra[$i]['usrnomxx'];?></td>
										        <td class="letra7"><?php echo substr($mMatrizTra[$i]['gofdesxx'],0,40) ?></td>
										        <td class="letra7"><?php echo substr($mMatrizTra[$i]['obsobsxx'],0,40) ?></td>
	       	                	<td class="letra7"><?php echo $mMatrizTra[$i]['regfcrex'] ?></td>
	          	              <td class="letra7"><?php echo $mMatrizTra[$i]['reghcrex'] ?></td>
	          	              <td class="letra7"><?php echo $mMatrizTra[$i]['regfmodx'] ?></td>
	          	              <td class="letra7"><?php echo $mMatrizTra[$i]['reghmodx'] ?></td>
	          	              <td class="letra7"><?php echo $mMatrizTra[$i]['regestxx'] ?></td>
	        	              	<td class="letra7" align="right">
	        	              	  <input type="checkbox" name="cCheck"
	        	              	  	value = "<?php echo $mMatrizTra[$i]['obscscxx'].'~'.$mMatrizTra[$i]['regestxx'] ?>"
	        	              	  	id = "<?php echo $mMatrizTra[$i]['obscscxx'].'~'.$mMatrizTra[$i]['regestxx'] ?>"
	        	              	    onclick="javascript:document.forms['frgrm']['nRecords'].value='<?php echo count($mMatrizTra) ?>'">
	        	              	</td>
	        	        		</tr>
	                	    	<?php $y++;
 	                    	}
 	                    }
 	                    //mysql_close($zConnect);

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
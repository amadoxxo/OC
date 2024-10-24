<?php
  namespace openComex;
   /**
	 * Legaliazar Formularios al Gasto .
	 * --- Descripcion: Me lista los formularios con estado PRVGASTO. de todos los Directores de Cuenta de Toda Colombia.
	 * @author Paola Garay <dp3@opentecnologia.com.co>
	 * @version 001
	 */
	include("../../../../libs/php/utility.php");

	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlDb = $kDf[3];

  /* Busco en la 05 que Tiene Permiso el Usuario*/
  $qUsrMen  = "SELECT * ";
  $qUsrMen .= "FROM $cAlfa.sys00005 ";
  $qUsrMen .= "WHERE ";
  $qUsrMer .= "sys00005.usridxxx = \"{$_COOKIE['kUsrId']}\" AND ";
  $qUsrMen .= "sys00005.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
  $qUsrMen .= "sys00005.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
  $qUsrPer .= "sys00005.menidxxx = \"{$mUsrMen['menidxxx']}\"  LIMIT 0,1";
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
   	<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
   	<script language = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
   	<script language="javascript">



    	function f_Link(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
          	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
            document.cookie="kMenDes="+xMenDes+";path="+"/";
          	document.cookie="kModo="+xOpcion+";path="+"/";
          	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
          	document.location = xForm; // Invoco el menu.
          }




	  	function f_Carga_Variable(xRecords) {
	  		var zSwitch = "0";
        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      	document.cookie="kModo=LEGALIZAR;path="+"/";
	  		document.forms['frgrm']['cComMemo'].value = "|";
	  		switch (xRecords) {
					case "1":
			  		if (document.forms['frgrm']['cCheck'].checked == true) {
							document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'].value;
							document.forms['frgrm']['cComMemo'].value += "|";
		  			}
	  			break;
	  			default:
	  				document.forms['frgrm']['cComMemo'].value = "|";
			  		for (i=0;i<document.forms['frgrm']['cCheck'].length;i++) {
			  			if (document.forms['frgrm']['cCheck'][i].checked == true) {
				  			document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'][i].value;
				  			document.forms['frgrm']['cComMemo'].value += "|";
		  				}
		  			}
	  			break;
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
   	   	      	i=document.forms['frgrm']['cCheck'].length;
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
		<form name = "frestado" action = "frgasgra.php" method = "post" target="fmpro">
			<input type = "hidden" name = "cSerId" value = "">
			<input type = "hidden" name = "cCliId" value = "">
		</form>





		<form name = "frgrm" method = "post">
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


					<!-- Inicia Nivel de Procesos -->
   		<?php if (mysql_num_rows($xUsrMen) > 0) {
   		?>

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

												/*para pintar solo 5 modulos por fila*/
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
		   	$y=0;
				$qFoiDat  = "SELECT * ";
			  $qFoiDat .= "FROM $cAlfa.ffoi0000 ";
				// $qFoiDat .= "WHERE " ;
				// $qFoiDat .= "$cAlfa.ffoi0000.regestxx = \"PRVGASTO\" AND ";
				// $qFoiDat .= "$cAlfa.ffoi0000.doccomex <> \"\" ";
				if($_POST['cTipPro']!="") {
			  	// $qFoiDat .= "AND $cAlfa.ffoi0000.ptoidxxx=\"{$_POST['cTipPro']}\" ";
			  }
				$qFoiDat .= "ORDER BY ABS($cAlfa.ffoi0000.seridxxx) ASC ";
				//f_Mensaje(__FILE__,__LINE__,$qFoiDat);
				$xFoiDat  = f_MySql("SELECT","",$qFoiDat,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,mysql_num_rows($xFoiDat));
				/* Cargo la Matriz con los ROWS del Cursor */
				$i=0;
				while ($xRFD = mysql_fetch_array($xFoiDat)) {
					$mMatrizTmp[$i] = $xRFD;
					//f_Mensaje(__FILE__,__LINE__,$mMatrizTmp[$i]['ptoidxxx']." ~ ".$mMatrizTmp[$i]['seridxxx']);
					$i++;
				}
				/* Fin de Cargo la Matriz con los ROWS del Cursor */
				/* Recorro la Matriz para Traer Datos Externos */

				/* Traigo el Nombre del Usuario de la matriz ya cargada, es decir que si no hay registros en la consulta
				anterior no trae descripcion de usuario, ni ninguna de las consultas siguientes tendran valores*/
				for ($i=0;$i<count($mMatrizTmp);$i++) {
					## Traigo el Nombre del Usuario ##
					$qUsrNom  = "SELECT ";
					$qUsrNom .= "$cAlfa.SIAI0003.USRNOMXX ";
					$qUsrNom .= "FROM $cAlfa.SIAI0003 ";
					$qUsrNom .= "WHERE ";
					$qUsrNom .= "$cAlfa.SIAI0003.USRIDXXX = \"{$mMatrizTmp[$i]['diridxxx']}\" LIMIT 0,1";
					$xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qUsrNom);
					if (mysql_num_rows($xUsrNom) > 0) {
						while ($xRUN = mysql_fetch_array($xUsrNom)) {
							$mMatrizTmp[$i]['usrnomxx'] = $xRUN['USRNOMXX'];
						}
					} else {
						$mMatrizTmp[$i]['usrnomxx'] = "USUARIO SIN NOMBRE";
					}
					##Fin Traigo el Nombre del Usuario ##

					## Traigo la descripcion del tipo Productos Formulario ##
					$qPtoDes  = "SELECT ";
					$qPtoDes .= "$cAlfa.fpar0132.ptodesxx ";
					$qPtoDes .= "FROM $cAlfa.fpar0132 ";
					$qPtoDes .= "WHERE ";
					$qPtoDes .= "$cAlfa.fpar0132.ptoidxxx = \"{$mMatrizTmp[$i]['ptoidxxx']}\" LIMIT 0,1";
					$xPtoDes  = f_MySql("SELECT","",$qPtoDes,$xConexion01,"");
					if (mysql_num_rows($xPtoDes) > 0) {
						while ($xRFD = mysql_fetch_array($xPtoDes)) {
							$mMatrizTmp[$i]['ptodesxx'] = $xRFD['ptodesxx'];
						}
					} else {
						$mMatrizTmp[$i]['ptodesxx'] = "TIPO FORMULARIO SIN NOMBRE";
					}
					## Fin Traigo la descripcion del tipo de formulario ##
				}
				/***** Extraigo el nombre del usuario *****/
				$qUsrNom = "SELECT USRNOMXX ";
				$qUsrNom .= "FROM $cAlfa.SIAI0003 ";
				$qUsrNom .= "WHERE ";
				$qUsrNom .= "USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
				$xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
				$xRUN   = mysql_fetch_array($xUsrNom);
				$zNomUsr = $xRUN['USRNOMXX'];
				/***** Fin Extracci�n nombre del usuario General. *****/


				/***** Si el $cSearch Viene Cargado Busco el Patron en Todos los Campos de la Matriz *****/
				if ($cSearch !="") {
					$mMatrizTra = array();
					for ($i=0,$j=0;$i<count($mMatrizTmp);$i++) {
						$zArray = array_values($mMatrizTmp[$i]);
						for ($k=0;$k<count($zArray);$k++) {
							if (substr_count($zArray[$k],strtoupper($cSearch)) > 0) {
								$k = count($zArray)+1;
								$mMatrizTra[$j] = $mMatrizTmp[$i];
								$j++;
							}
						}
					}
				} else {
					$mMatrizTra = $mMatrizTmp;

				}
				/***** Fin de Buscar Patron en la Matriz *****/

				if ($cSortField !="" && $cSortType !="") {
					$mMatrizTra = f_Sort_Array_By_Field($mMatrizTra,$cSortField,$cSortType);
				}
				/* Fin de Recorro la Matriz para Traer Datos Externos */
			?>
        <script language="javascript">
					document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra) ?>";
				</script>
				<center>
       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Formularios Legalizados al Gasto (<?php echo count($mMatrizTra) ?>)</legend>
     	       		<center>
     	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
         	      			<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
           	        	<td class="clase08" width="20%">
            	        	<input type="text" class="letra" name = "cSearch" maxlength="20" value = "<?php echo $cSearch ?>" style= "width:80"
            	        		onblur="javascript:this.value=this.value.toUpperCase();
																						 document.forms['frgrm']['nLimInf'].value='00';
								      											 document.forms['frgrm']['nLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      											 document.forms['frgrm']['cPaginas'].value='1'
								      											 document.forms['frgrm'].submit()">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:hand" title="Buscar"
								      		onClick = "javascript:document.forms['frgrm']['cSearch'].value=document.forms['frgrm']['cSearch'].value.toUpperCase();
								      											 		document.forms['frgrm']['nLimInf'].value='00';
								      												  document.forms['frgrm']['nLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												  document.forms['frgrm']['cPaginas'].value='1'
								      												  document.forms['frgrm'].submit()">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:hand" title="Mostrar Todo"
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
       	       				<td class="name" width="32%" align="left">
       	       				  &nbsp;&nbsp;&nbsp; Tipo Producto: &nbsp;&nbsp;&nbsp;
            	       	  <select name="cTipPro" size=1 onchange="javascript:document.forms['frgrm'].submit()">
            	       	  <option value="">TODOS</option>
            	       	  <?php
            	       	  $qFoiDat  = "SELECT * ";
            	       	  $qFoiDat .= "FROM $cAlfa.ffoi0000 ";
            	       	  $qFoiDat .= "WHERE ";
            	       	  $qFoiDat .= "REGESTXX=\"PRVGASTO\" AND ";
            	       	  $qFoiDat .= "doccomex<>\"\" ";
            	       	  $qFoiDat .= "GROUP BY ptoidxxx ";
            	       	  $xFoiDat  = f_MySql("SELECT","",$qFoiDat,$xConexion01,"");
											  while ($xRFD = mysql_fetch_array($xFoiDat)) {
											    $qPtoDat  = "SELECT * ";
											    $qPtoDat .= "FROM $cAlfa.fpar0132 ";
											    $qPtoDat .= "WHERE ptoidxxx=\"{$xRFD['ptoidxxx']}\" LIMIT 0,1";
											   	//f_Mensaje(__FILE__,__LINE__,$xPtoDat);
											    $xPtoDat  = f_MySql("SELECT","",$qPtoDat,$xConexion01,"");
											    $xRFD = mysql_fetch_array($xPtoDat);
											  ?>
                        <option value="<?php echo $xRFD['ptoidxxx'];?>"><?php  echo $xRFD['ptodesxx']; ?></option>
				                <?php
											  }
                        ?>
            	       	  </select>
            	       	  <?php
            	       	  if($_POST['cTipPro']!=""){?>
            	       	  <script>
            	       	    document.forms['frgrm']['cTipPro'].value="<?php echo $_POST['cTipPro']; ?>";
            	       	  </script>
            	       	  <?php
            	       	  }
                        ?>
       	       				</td>

             	      </tr>
         	      	</table>
         	      	</br>
       	     			<table cellspacing="0" width="100%">
         	         <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
         	         		<td class="name" width="03%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','sucidxxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','sucidxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','sucidxxx','')">Suc</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "sucidxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','sucidxxx','')</script>
          	          </td>
          	          <td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','doccomex','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','docidxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','seridxxx','')">Do</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docidxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','doccomex','')</script>
          	          </td>
          	          <td class="name" width="04%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','docsufxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','docsufxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','docsufxx','')">Suf</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docsufxx">
           	         		<script language="javascript">f_ButtonsAscDes('','docsufxx','')</script>
          	          </td>
           	          <td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','seridxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','seridxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','seridxxx','')">Serial</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "seridxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','seridxxx','')</script>
          	           </td>
         	            <td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','ptoidxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','ptoidxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','ptoidxxx','')">Pto.</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ptoidxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','ptoidxxx','')</script>
           	         	</td>
           	         	<td class="name" width="20%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','ptodesxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','ptodesxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','ptodesxx','')">Descripcion</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ptodesxx">
           	         		<script language="javascript">f_ButtonsAscDes('','ptodesxx','')</script>
          	         	</td>
           	         	<td class="name" width="15%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','diridxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','diridxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','diridxxx','')">C.C.</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "diridxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','diridxxx','')</script>
          	          </td>
          	         	<td class="name" width="18%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','usrnomxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','usrnomxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','usrnomxx','')">Director de Cuenta</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "usrnomxx">
           	         		<script language="javascript">f_ButtonsAscDes('','usrnomxx','')</script>
          	         	</td>

          	         	<td class="name" width="1%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','regestxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','regestxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','regestxx','')">Estado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
           	         		<script language="javascript">f_ButtonsAscDes('','regestxx','')</script>
          	         	</td>
          	         	<td class="name" width="5%" align="right">
           	         	</td>
             	        </tr>
             	        <tr bgcolor = 'white'>
             	        <td class="name" width="03%">&nbsp;

          	         	</td>
          	         	<td class="name" width="10%">&nbsp;

          	         	</td>
          	         	<td class="name" width="04%">&nbsp;

          	         	</td>
           	         	<td class="name" width="10%">&nbsp;

          	         	</td>
             	        <td class="name" width="10%">&nbsp;
           	         	</td>
           	         	<td class="name" width="20%">&nbsp;

          	         	</td>
          	         	<td class="name" width="15%">&nbsp;

          	         	</td>
           	         	<td class="name" width="18%">&nbsp;

          	         	</td>
           	         	<td class="name" width="5%">&nbsp;

          	         	</td>
          	         		<td class="name" width="5%">&nbsp;

          	         	</td>
             	     </tr>
					         <script language="javascript">
						          document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra) ?>";
						       </script>
 	                    <?php

 	                    for($i=intval($nLimInf);$i<intval($nLimInf+$nLimSup);$i++) {
 	                    	if ($i < count($mMatrizTra)) { // Para Controlar el Error
	 	                    	$zColor = "{$vSysStr['system_row_impar_color_ini']}";
	                   	    if($y % 2 == 0) {
	                   	    	$zColor = "{$vSysStr['system_row_par_color_ini']}";
													}


													?>
								        <!--<tr bgcolor = "<?php echo $zColor ?>">-->
								        <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
										        <td class="letra7" width="03%"><?php echo $mMatrizTra[$i]['sucidxxx'] ?></td>
										        <td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['doccomex'] ?></td>
										        <td class="letra7" width="04%"><?php echo $mMatrizTra[$i]['docsufxx'] ?></td>
										        <td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['seridxxx'] ?></td>
								            <td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['ptoidxxx'];?></td>
	       	                	<td class="letra7" width="20%"><?php echo $mMatrizTra[$i]['ptodesxx'] ?></td>
	       	                	<td class="letra7" width="15%"><?php echo $mMatrizTra[$i]['diridxxx'] ?></td>
	       	                	<td class="letra7" width="18%"><?php echo $mMatrizTra[$i]['usrnomxx'] ?></td>
	       	                	<td class="letra7" width="5%"><?php echo $mMatrizTra[$i]['regestxx'] ?></td>
	        	              	<td class="letra7" width="5%" align="right"></td>
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
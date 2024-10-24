<?php
  namespace openComex;
   /**
	 * Legaliazar Formularios al Gasto .
	 * --- Descripcion: Me lista los formularios con estado PRVGASTO. de todos los Directores de Cuenta de Toda Colombia.
	 * @author Paola Garay <dp3@opentecnologia.com.co>
	 * @version 001
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

	   	function f_Retorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
				document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
				parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			}

    	function f_Link(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
        document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes="+xMenDes+";path="+"/";
        document.cookie="kModo="+xOpcion+";path="+"/";
        parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
        document.location = xForm; // Invoco el menu.
      }

    	function f_Links(xLink,xSwitch,xIteration) {
				var nX    = screen.width;
				var nY    = screen.height;
				switch (xLink){
					case "cSerId":
						if (xSwitch == "VALID") {
							var zRuta  = "frfoi000.php?gWhat=VALID&gFunction=cSerId&cSerId="+
							document.frgrm.cSerId.value.toUpperCase()+"";
							parent.fmpro.location = zRuta;
						} else {
		  				var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var zRuta   = "frfoi000.php?gWhat=WINDOW&gFunction=cSerId&cSerId="+
							document.frgrm.cSerId.value.toUpperCase()+"";
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
				  break;
					case "cDirId":
					case "cDirNom":
						if (xSwitch == "VALID") {
							var zRuta  = "frgas003.php?gWhat=VALID&gFunction="+xLink+
							                          "&gDirId="+document.frgrm['cDirId'].value.toUpperCase()+
							                          "&gDirNom="+document.frgrm['cDirNom'].value.toUpperCase();
							parent.fmpro.location = zRuta;
						} else {
	  					var nNx     = (nX-600)/2;
							var nNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
							var zRuta   = "frgas003.php?gWhat=WINDOW&gFunction="+xLink+
							                           "&gDirId="+document.frgrm['cDirId'].value.toUpperCase()+
							                           "&gDirNom="+document.frgrm['cDirNom'].value.toUpperCase();
							zWindow = window.open(zRuta,"zWindow",zWinPro);
					  	zWindow.focus();
						}
			    break;
				}
			}

     	function f_Enviar_Form(){
     	  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
 					if(document.forms['frgrm']['nRecords'].value >= 1){
 	          if(document.forms['frgrm']['cChekeados'].value >= 1){
 	            w=500;
 	        	  h=230;
 	        	  LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
 	            TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
 	            settings ='height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable'
 	    			 	zWin = window.open('frforg35.php','cObserv',settings);
 	    			 	zWin.focus();
         		}else{
         	  	alert("Usted no ha Checkeado Ningun Formulario. Verifique.");
         		}
          }else{
            alert("No hay Registros Disponibles, Verifique.");
        }
      }

 		  function f_Enviar_Form_Ya(){
  			document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
 	      document.forms['frgrm'].action='frforggr.php';
 	      document.forms['frgrm'].target='fmpro';
 	      document.forms['frgrm'].submit();
 	    }

  	  function f_Imp_Soporte(xObsCsc){
  	    var zX      = screen.width;
  			var zY      = screen.height;
  			var alto = zY-80;
  			var ancho = zX-100;
  			var zNx     = (zX-ancho)/2;
  			var zNy     = (zY-alto)/2;
  			var zWinPro = 'width='+ancho+',height='+alto+',left='+zNx+',top='+zNy;
  			var zRuta = 'frforgpr.php?cObsCsc='+xObsCsc;
  			zWindow = window.open(zRuta,'zWindow',zWinPro);
  		}

    	function f_Carga_Variable(xRecords) {
  	  	//alert(xRecords);
    		var zSwitch = "0";
          document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kModo=LEGALIZAR;path="+"/";
    		document.forms['frgrm']['cComMemo'].value = "|";
    		switch (xRecords) {
  	  		case 0:
  	  			//alert("Entre 0");
    			break;
  				case 1:
  					//alert("Entre 1");
  		  		if (document.forms['frgrm']['cCheck'].checked == true) {
  						document.forms['frgrm']['cComMemo'].value += document.forms['frgrm']['cCheck'].value;
  						document.forms['frgrm']['cComMemo'].value += "|";
  	  			}
    			break;
    			default:
    				//alert("Entre Varios");
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
        	  if(document.forms['frgrm']['cCheckAll'].checked == true){
        		  if (document.forms['frgrm']['nRecords'].value == 1){
    				    if(document.forms['frgrm']['cCheck'].checked == true){
    				    	document.forms['frgrm']['cChekeados'].value=1;
    				    }else{
    				    	document.forms['frgrm']['cChekeados'].value=0;
    				    }
    				  }else{
    						if (document.forms['frgrm']['nRecords'].value > 1){
    							document.forms['frgrm']['cChekeados'].value=0;
    			      	for (i=0;i<document.forms['frgrm']['cCheck'].length;i++){
    			   	      if(document.forms['frgrm']['cCheck'][i].checked == true){
    			   	      	document.forms['frgrm']['cChekeados'].value=1;
    			   	      	i=document.forms['frgrm']['cCheck'].length;
    			   	      }
    				      }
    				    }
    					}
        	  }else{
        		  if (document.forms['frgrm']['nRecords'].value == 1){
    				    if(document.forms['frgrm']['cCheck'].checked == true){
    				    	document.forms['frgrm']['cChekeados'].value=1;
    				    }else{
    				    	document.forms['frgrm']['cChekeados'].value=0;
    				    }
    				  }else{
    						if (document.forms['frgrm']['nRecords'].value > 1){
    							document.forms['frgrm']['cChekeados'].value=0;
    			      	for (i=0;i<document.forms['frgrm']['cCheck'].length;i++){
    			   	      if(document.forms['frgrm']['cCheck'][i].checked == true){
    			   	      	document.forms['frgrm']['cChekeados'].value=1;
    			   	      	i=document.forms['frgrm']['cCheck'].length;
    			   	      }
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
   		<input type = "hidden" name = "cGofId"    value = "">


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

				if ($cDirId !="") {


		   	$y=0;
				$qFoiDat  = "SELECT * ";
			  $qFoiDat .= "FROM $cAlfa.ffoi0000 ";
				// $qFoiDat .= "WHERE " ;
				// $qFoiDat .= "ffoi0000.diridxxx = \"$cDirId\" AND ";
				// $qFoiDat .= "ffoi0000.regestxx = \"CONDO\" AND ";
				// $qFoiDat .= "ffoi0000.doccomex <> \"\" ";
				if($_POST['cTipPro']!="") {
			  	// $qFoiDat .= "AND ffoi0000.ptoidxxx=\"{$_POST['cTipPro']}\" ";
			  }
				$qFoiDat .= "ORDER BY ABS(ffoi0000.seridxxx) ASC ";
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
					/*
					## Traigo el Nombre del Usuario ##
					$qUsrNom  = "SELECT ";
					$qUsrNom .= "SIAI0003.USRNOMXX ";
					$qUsrNom .= "FROM SIAI0003 ";
					$qUsrNom .= "WHERE ";
					$qUsrNom .= "SIAI0003.USRIDXXX = \"{$mMatrizTmp[$i]['diridxxx']}\" LIMIT 0,1";
					$xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
					if (mysql_num_rows($xUsrNom) > 0) {
						while ($xRUN = mysql_fetch_array($xUsrNom)) {
							$mMatrizTmp[$i]['usrnomxx'] = $xRUN['USRNOMXX'];
						}
					} else {
						$mMatrizTmp[$i]['usrnomxx'] = "USUARIO SIN NOMBRE";
					}
					##Fin Traigo el Nombre del Usuario ##
					*/

					## Traigo la descripcion del tipo Productos Formulario ##
					$qPtoDes  = "SELECT ";
					$qPtoDes .= "$cAlfa.fpar0132.ptodesxx ";
					$qPtoDes .= "FROM $cAlfa.fpar0132 ";
					$qPtoDes .= "WHERE ";
					$qPtoDes .= "$cAlfa.fpar0132.ptoidxxx = \"{$mMatrizTmp[$i]['ptoidxxx']}\" LIMIT 0,1";
					$xPtoDes  = f_MySql("SELECT","",$qPtoDes,$xConexion01,"");
					/*
					$xRFD = mysql_fetch_array($xPtoDes);
					$mMatrizTmp[$i]['ptodesxx'] = $xRFD['ptodesxx'];
					*/
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
				/*
				$qUsrNom = "SELECT USRNOMXX ";
				$qUsrNom .= "FROM SIAI0003 ";
				$qUsrNom .= "WHERE ";
				$qUsrNom .= "USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
				$xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
				$xRUN   = mysql_fetch_array($xUsrNom);
				$zNomUsr = $xRUN['USRNOMXX'];
				/***** Fin Extracci�n nombre del usuario General. *****/

				}

				/***** Si el $cDirId Viene Cargado Busco el Patron en Todos los Campos de la Matriz *****/
				if ($cDirId !="") {
					$mMatrizTra = array();
					for ($i=0,$j=0;$i<count($mMatrizTmp);$i++) {
						$zArray = array_values($mMatrizTmp[$i]);
						for ($k=0;$k<count($zArray);$k++) {
							if (substr_count($zArray[$k],strtoupper($cDirId)) > 0) {
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

				<br></br>

				<center>
					<table border="0" cellpadding="0" cellspacing="0" width="780">
						<tr height="21">
							<td width="598" height="21"></td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:f_Enviar_Form();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Legalizar</td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						</tr>
					</table>
				</center>

       	<table width="780" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Formularios para Legalizar al Gasto (<?php echo count($mMatrizTra) ?>)</legend>
     	       		<center>
     	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
         	      			<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
         	      			<td class="clase08" width="80%">Seleccione el Director:&nbsp;<a href = "javascript:document.frgrm.cDirId.value='';
																                      document.frgrm.cDirNom.value='';
																											f_Links('cDirId','VALID')" id="vDir"></a>&nbsp;&nbsp;&nbsp;
	          	          <input type="text" name="cDirId" style = "width:100" value = "<?php echo $cDirId ?>"
													onfocus="javascript:document.forms['frgrm']['cDirId'].value  = '';
	              					  									document.forms['frgrm']['cDirNom'].value = '';
	  												                  this.style.background='#00FFFF'"
	  									   	onBlur = "javascript:this.value=this.value.toUpperCase();
	  																	         f_Links('cDirId','VALID');
	  																	         this.style.background='#FFFFFF'">

	          	          <input type="text" name="cDirDv" style = "width:2">

	          	          <input type="text" name="cDirNom" style = "width:300" value = "<?php echo $cDirNom ?>"
	  									   	onfocus="javascript:document.forms['frgrm']['cDirId'].value  = '';
	            					  									document.forms['frgrm']['cDirNom'].value = '';
													                  this.style.background='#00FFFF'"
										   	  onBlur = "javascript:this.value=this.value.toUpperCase();
													                   f_Links('cDirNom','VALID');
													                   this.style.background='#FFFFFF'">

												<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:hand" title="Buscar"
								      		onClick = "javascript:document.forms['frgrm']['cDirId'].value=document.forms['frgrm']['cDirId'].value.toUpperCase();
								      												  document.forms['frgrm'].submit();">

												<img src = "<?php echo $cPlesk_Skin_Directory ?>/spamfilter_on.gif" style = "cursor:hand" title="Nueva Busqueda"
								      		onClick = "javascript:document.forms['frgrm']['cDirId'].value='';
									      												document.forms['frgrm']['cDirNom'].value='';
								      												  document.forms['frgrm'].submit();">

	          	        </td>
   	              	  </td>
             	      </tr>
         	      	</table>
         	      	</br>
       	     			<table cellspacing="0" width="100%">
         	         <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
         	         		<td class="name" width="05%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','sucidxxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','sucidxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','sucidxxx','')">Suc</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "sucidxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','sucidxxx','')</script>
          	          </td>
          	          <td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','doccomex','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','doccomex','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','doccomex','')">Do</a>&nbsp;
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
           	         	<td class="name" width="40%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','ptodesxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','ptodesxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','ptodesxx','')">Descripcion</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ptodesxx">
           	         		<script language="javascript">f_ButtonsAscDes('','ptodesxx','')</script>
          	         	</td>

          	         	<td class="name" width="5%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','regestxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','regestxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','regestxx','')">Estado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
           	         		<script language="javascript">f_ButtonsAscDes('','regestxx','')</script>
          	         	</td>
          	         	<td class="name" width="5%" align="right">
           	         		<input type="checkbox" name="cCheckAll" onClick = 'javascript:f_Marca();f_Verificar_Check();f_Carga_Variable(<?php echo count($mMatrizTra) ?>);'>
           	         	</td>
             	        </tr>

             	        <tr bgcolor = 'white'>
             	        <td class="name" width="05%">&nbsp;

          	         	</td>
          	         	<td class="name" width="10%">&nbsp;

          	         	</td>
          	         	<td class="name" width="05%">&nbsp;

          	         	</td>
           	         	<td class="name" width="10%">&nbsp;

          	         	</td>
             	        <td class="name" width="10%">&nbsp;
           	         	</td>
           	         	<td class="name" width="40%">&nbsp;

          	         	</td>
           	         	<td class="name" width="5%">&nbsp;

          	         	</td>
          	         	<td class="name" width="5%" align="right">&nbsp;

           	         	</td>
										</tr>
					         <script language="javascript">
						          document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra) ?>";
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
										        <td class="letra7" width="05%"><?php echo $mMatrizTra[$i]['sucidxxx'] ?></td>
										        <td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['doccomex'] ?></td>
										        <td class="letra7" width="05%"><?php echo $mMatrizTra[$i]['docsufxx'] ?></td>
										        <td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['seridxxx'] ?></td>
								            <td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['ptoidxxx'];?></td>
	       	                	<td class="letra7" width="20%"><?php echo $mMatrizTra[$i]['ptodesxx'] ?></td>
	       	                	<td class="letra7" width="5%"><?php echo $mMatrizTra[$i]['regestxx'] ?></td>
	        	              	<td class="letra7" width="5%" align="right">
	        	              	  <input type="checkbox" name="cCheck"  value = "<?php echo $mMatrizTra[$i]['seridxxx'].'~'.$mMatrizTra[$i]['ptoidxxx'].'~'.$mMatrizTra[$i]['diridxxx'].'~'.$mMatrizTra[$i]['doccomex'].'~'.$mMatrizTra[$i]['sucidxxx'].'~'.$mMatrizTra[$i]['docsufxx'] ?>"
	        	              	    onclick="javascript:f_Verificar_Check();f_Carga_Variable(<?php echo count($mMatrizTra) ?>);">
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
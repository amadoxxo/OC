<?php
  namespace openComex;
   /**
	 * Anular Formularios.
	 * --- Descripcion: Me lista los formularios con estado ASIGNADO. de todos los Directores de Cuenta de Toda Colombia.
	 * @author Pedro Leon Burbano Suarez <pedrob@repremundo.com.co>
	 * @version 001
	 */
	include("../../../../libs/php/utility.php");

	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlDb = $kDf[3];

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

     	function f_Enviar_Form(){
     	  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        if(document.forms['frgrm']['nRecords'].value >= 1){
          if(document.forms['frgrm']['cChekeados'].value!=""){
            w=500;
        	  h=230;
        	  LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
            TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
            settings ='height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable'
    			 	zWin = window.open('frforg35.php','cObserv',settings);
    			 	zWin.focus();
        	}else{
        	  alert("Usted no ha Checkeado Ningun Formulario. Verifique.")
        	}
        }else{
            alert("No hay Resgistros Disponibles, Verifique.")
        }
      }

      function f_Enviar_Form_Ya(){
        document.forms['frgrm'].action='frforggr.php';
        document.forms['frgrm'].target='fmpro';
        document.forms['frgrm'].submit();
      }

      function f_Imp_Soporte(gMemo,gObservaciones,gSopId){
        var gkMysqlDb = '<?php echo $kMysqlDb ?>';
        var zX      = screen.width;
				var zY      = screen.height;
				var alto = zY-80;
				var ancho = zX-100;
				var zNx     = (zX-ancho)/2;
				var zNy     = (zY-alto)/2;
				var zWinPro = 'width='+ancho+',height='+alto+',left='+zNx+',top='+zNy;
				var zRuta = 'frforgpr.php?cMemo='+gMemo+'&cObserv='+gObservaciones+'&cSopId='+gSopId+''+'&ckMysqlDb='+gkMysqlDb;
				zWindow = window.open(zRuta,'zWindow',zWinPro);
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
		<form name = "frgrm" method = "post" >
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
				if ($nLimInf =="" && $nLimSup =="") {
					$nLimInf = "00";
          $nLimSup = $vSysStr['system_rows_page_ini'];
				}

				if ($cPaginas =="") {
        	$cPaginas = "1";
				}

				/***** Si Viene Vacio el $cCcoId lo Cargo con la Cookie del Centro de Costo *****/
				/***** Si no Hago el SELECT con el Centro de Costo que me Entrega el Combo del INI *****/
				if ($cCcoId =="") {
        	$cCcoId = $_COOKIE['kUsrCco'];
				} else {
					/***** Si el $cCcoId Viene Cargado del Combo con "ALL" es porque Debo Mostrar Todos los Centros de Costos *****/
					/***** Si no Dejo la Sucursal que Viene Cargada *****/
					if ($cCcoId == "ALL") {
						$cCcoId = "";
					}
				}

				/***** Si Viene Vacio el $cUsrId lo Cargo con la Cookie del Usuario *****/
				/***** Si no Hago el SELECT con el Usuario que me Entrega el Combo de INI *****/
				if ($cUsrId =="") {
        	$cUsrId = $_COOKIE['kUsrId'];
				} else {
					/***** Si el $cUsrId Viene Cargado del Combo con "ALL" es porque Debo Mostrar Todos los Usuarios *****/
					/***** Si no Dejo el Usuario que Viene Cargado *****/
					if ($cUsrId == "ALL") {
						$cUsrId = "";
					}
				}
				/**
				 * Realizo la consulta de los formularios legalizados o no.
				 */
		   	$y=0;
				$qFoiDat  = "SELECT ";
				$qFoiDat  = "$cAlfa.ffoi0000.ptoidxxx, ";
				$qFoiDat  = "$cAlfa.ffoi0000.seridxxx, ";
				$qFoiDat  = "$cAlfa.ffoi0000.diridxxx, ";
				$qFoiDat  = "$cAlfa.ffoi0000.sucidxxx, ";
				$qFoiDat  = "$cAlfa.ffoi0000.doccomex, ";
				$qFoiDat  = "$cAlfa.ffoi0000.docsufxx, ";
				$qFoiDat  = "$cAlfa.ffoi0000.regestxx ";
			  $qFoiDat .= "FROM $cAlfa.ffoi0000 ";
				$qFoiDat .= "WHERE ";
				$qFoiDat .= "$cAlfa.ffoi0000.regestxx = \"CONDO\" AND ";
				$qFoiDat .= "$cAlfa.ffoi0000.doccomex <> \"\" ";
				if($_POST['cTipPro']!=""){
			  $qFoiDat .= "AND $cAlfa.ffoi0000.ptoidxxx=\"{$_POST['cTipPro']}\" ";
			  }
				$qFoiDat .= "ORDER BY CONVERT($cAlfa.ffoi0000.seridxxx,signed) ASC ";
				//f_Mensaje(__FILE__,__LINE__,$qFoiDat);
				$xFoiDat  = f_MySql("SELECT","",$qFoiDat,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,mysql_num_rows($xFoiDat));
				/* Cargo la Matriz con los ROWS del Cursor */
				$i=0;
				while ($xRFD = mysql_fetch_array($xFoiDat)) {
					$mMatrizTmp[$i] = $xRFD;
					$i++;
				}
				/* Fin de Cargo la Matriz con los ROWS del Cursor */
				/* Recorro la Matriz para Traer Datos Externos */


				/* Traigo el Nombre del Usuario de la matriz ya cargada, es decir que si no hay registros en la consulta
				anterior no trae descripcion de usuario, ni ninguna de las consultas siguientes tendran valores*/
				for ($i=0;$i<count($mMatrizTmp);$i++) {
				  //$mMatrizTmp[$i]['seridxxx'] = intval($mMatrizTmp[$i]['seridxxx']);

				  $qDoiDat = "SELECT * FROM $cAlfa.sys00121 WHERE docidxxx = \"{$mMatrizTmp[$i]['doccomex']}\" AND REGESTXX=\"ACTIVO\" LIMIT 0,1";
					$xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
					$xRDD = mysql_fetch_array($xDoiDat);

					$mMatrizTmp[$i]['sucidxxx']=$xRDD['sucidxxx'];
					$mMatrizTmp[$i]['docidxxx']=$xRDD['docidxxx'];

					/* Traigo el Nombre del Usuario */
					$qUsrNom = "SELECT USRNOMXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$mMatrizTmp[$i]['diridxxx']}\" LIMIT 0,1";
					$xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
					if (mysql_num_rows($xUsrNom) > 0) {
						while ($xRUN = mysql_fetch_array($xUsrNom)) {
							$mMatrizTmp[$i]['usrnomxx'] = $xRUN['USRNOMXX'];
						}
					} else {
						$mMatrizTmp[$i]['usrnomxx'] = "USUARIO SIN NOMBRE";
					}

					/* Fin Traigo el Nombre del Usuario */



					/* Traigo la descripcion del tipo de formulario */
					$qPtoDes = "SELECT ptodesxx FROM $cAlfa.fpar0132 WHERE ptoidxxx = \"{$mMatrizTmp[$i]['ptoidxxx']}\" LIMIT 0,1";
					$xPtoDes  = f_MySql("SELECT","",$qPtoDes,$xConexion01,"");
					if (mysql_num_rows($xPtoDes) > 0) {
						while ($xRPD = mysql_fetch_array($xPtoDes)) {
							$mMatrizTmp[$i]['ptodesxx'] = $xRPD['ptodesxx'];
						}
					} else {
						$mMatrizTmp[$i]['ptodesxx'] = "TIPO FORMULARIO SIN NOMBRE";
					}
					/* Fin Traigo la descripcion del tipo de formulario */
				}
				/***** Extraigo el nombre del usuario *****/
				$qUsrNom = "SELECT USRNOMXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
				$xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
				$xRUN   = mysql_fetch_array($xUsrNom);
				$zNomUsr = $xRUN['USRNOMXX'];
				/***** Fin Extracción nombre del usuario General. *****/


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
      <center>
        <script languaje="javascript">
					document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra) ?>";
				</script>
       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Formularios Para Legalizar al Gasto: <?php echo count($mMatrizTra) ?></legend>
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
            	       	  $qFoiDat  = "SELECT ";
            	       	  $qFoiDat .= "$cAlfa.ffoi0000.ptoidxxx, ";
            	       	  $qFoiDat .= "$cAlfa.ffoi0000.regestxx ";
            	       	  $qFoiDat .= "FROM $cAlfa.ffoi0000 ";
            	       	  $qFoiDat .= "WHERE ";
            	       	  $qFoiDat .= "$cAlfa.ffoi0000.regestxx =\"CONDO\" AND  ";
            	       	  $qFoiDat .= "$cAlfa.ffoi0000.doccomex<>\"\" ";
            	       	  $qFoiDat .= "GROUP BY ptoidxxx ";
											  $xFoiDat  = f_MySql("SELECT","",$qFoiDat,$xConexion01,"");
											  while ($xRFD = mysql_fetch_array($xFoiDat)) {
											    $qPtoDat  = "SELECT * FROM $cAlfa.fpar0132 WHERE ptoidxxx=\"{$xRFD['ptoidxxx']}\" LIMIT 0,1";
											    $xPtoDat  = f_MySql("SELECT","",$qPtoDat,$xConexion01,"");
											    $xRPD = mysql_fetch_array($xPtoDat);
											  ?>
                        <option value="<?php echo $xRFD['ptoidxxx'];?>"><?php  echo $xRPD['ptodesxx']; ?></option>
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
			        		    <td Class="name" width="30%" align="right">
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
															case "LEGALIZAR": ?>
															<!--
															<img src = "<?php echo $cPlesk_Skin_Directory ?>/page_delete.png"
															  onClick = "javascript:f_Verificar_Check();
															                        f_Carga_Variable(document.forms['frgrm']['nRecords'].value);
															                        document.forms['frgrm'].action='frforgpr.php';
															                        document.forms['frgrm'].target='fmpro';
															                        document.forms['frgrm'].submit();"
															                        style = "cursor:hand" title="Anular Formulario" id="IdImg">
															-->
                              <input class="name" name="IdImg" type="button" value="Legalizar" style="cursor:pointer"
															  onClick = "javascript:f_Verificar_Check();
															                        f_Carga_Variable(document.forms['frgrm']['nRecords'].value);
															                        f_Enviar_Form();"
															                        style = "cursor:hand" title="Legalizar Formulario" id="IdImg">
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
         	      	<br><br>
       	     			<table cellspacing="0" width="100%">
         	         <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
         	         		<td class="name" width="03%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','sucidxxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','sucidxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','sucidxxx','')">Suc</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "sucidxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','sucidxxx','')</script>
          	          </td>
          	          <td class="name" width="07%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','docidxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','docidxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','seridxxx','')">Do</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docidxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','docidxxx','')</script>
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
           	         	<td class="name" width="16%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','ptodesxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','ptodesxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','ptodesxx','')">Descripcion</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ptodesxx">
           	         		<script language="javascript">f_ButtonsAscDes('','ptodesxx','')</script>
          	         	</td>
           	         	<td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','diridxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','diridxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','diridxxx','')">C.C.</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "diridxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','diridxxx','')</script>
          	          </td>
          	         	<td class="name" width="30%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','usrnomxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','usrnomxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','usrnomxx','')">Director de Cuenta</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "usrnomxx">
           	         		<script language="javascript">f_ButtonsAscDes('','usrnomxx','')</script>
          	         	</td>

          	         	<td class="name" width="5%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','regestxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','regestxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','regestxx','')">Estado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
           	         		<script language="javascript">f_ButtonsAscDes('','regestxx','')</script>
          	         	</td>
             	        <td class="name" width="5%" align="right">
           	         		<input type="checkbox" name="cCheckAll" onClick = "javascript:f_Marca();f_Verificar_Check();f_Carga_Variable(document.forms['frgrm']['nRecords'].value);">
           	         	</td>
             	        </tr>
             	        <tr bgcolor = 'white'>
             	        <td class="name" width="03%">&nbsp;

          	         	</td>
          	         	<td class="name" width="07%">&nbsp;

          	         	</td>
          	         	<td class="name" width="04%">&nbsp;

          	         	</td>
           	         	<td class="name" width="10%">&nbsp;

          	         	</td>
             	        <td class="name" width="10%">&nbsp;
           	         	</td>
           	         	<td class="name" width="16%">&nbsp;

          	         	</td>
          	         	<td class="name" width="10%">&nbsp;

          	         	</td>
           	         	<td class="name" width="30%">&nbsp;

          	         	</td>
           	         	<td class="name" width="5%">&nbsp;

          	         	</td>
             	        <td class="name" width="5%" align="right">&nbsp;

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
										        <td class="letra7" width="03%"><?php echo $mMatrizTra[$i]['sucidxxx'] ?></td>
										        <td class="letra7" width="07%"><?php echo $mMatrizTra[$i]['docidxxx'] ?></td>
										        <td class="letra7" width="04%"><?php echo $mMatrizTra[$i]['docsufxx'] ?></td>
		                      	<td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['seridxxx'] ?></td>
								            <td class="letra7" width="10%"><?php echo $mMatrizTra[$i]['ptoidxxx'];?></td>
	       	                	<td class="letra7" width="16%"><?php echo $mMatrizTra[$i]['ptodesxx'] ?></td>
	       	                	<td class="letra7" width="10%" align="left"><?php echo $mMatrizTra[$i]['diridxxx'] ?></td>
	       	                	<td class="letra7" width="30%"><?php echo $mMatrizTra[$i]['usrnomxx'] ?></td>
	       	                	<td class="letra7" width="5%"><?php echo $mMatrizTra[$i]['regestxx'] ?></td>
	        	              	<td class="letra7" width="5%" align="right">
	        	              	  <input type="checkbox" name="cCheck"  value = "<?php echo $mMatrizTra[$i]['seridxxx'].'~'.$mMatrizTra[$i]['ptoidxxx'].'~'.$mMatrizTra[$i]['diridxxx'].'~'.$mMatrizTra[$i]['docidxxx'].'~'.$mMatrizTra[$i]['sucidxxx'].'~'.$mMatrizTra[$i]['docsufxx'] ?>"
	        	              	    onclick="javascript:f_Verificar_Check();f_Carga_Variable(<?php echo count($mMatrizTra) ?>);">
	        	              	</td>
	        	        		</tr>
	                	    	<?php $y++;
 	                    	}
 	                    }
 	                    //mysql_close($zConnect);

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
			                 case "EDITAR":?>
				                  <script languaje = "javascript">
  					               document.forms['frgrm']['gTipSav'].value    = "UPDATE";
				                  </script>
			                 <?php break;

			                 case "ANULAR":?>
				                 <script languaje = "javascript">
					                 document.forms['frgrm']['gTipSav'].value    = "ANULAR";
				                 </script>
			                 <?php break;

			                 case "LEGALIZAR":?>
				                 <script languaje = "javascript">
					                 document.forms['frgrm']['gTipSav'].value    = "LEGALIZAR";
				                 </script>
			                 <?php break;

			                 case "VER":
				                 wCargaData($gGruId); ?>
				                 <script languaje = "javascript">
					                 document.forms['frgrm']['cGruDes'].disabled  = true;
				                 </script>
			                 <?php break;
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
          //f_Mensaje(__FILE__,__LINE__,"$cComMemo == {$mMatrizTra[$i]['seridxxx']}~{$mMatrizTra[$i]['ptoidxxx']}");
          $cValor = '|'.$mMatrizTra[$i]['seridxxx'].'~'.$mMatrizTra[$i]['ptoidxxx'].'~'.$mMatrizTra[$i]['diridxxx'].'~'.$mMatrizTra[$i]['docidxxx'].'|';
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
          $cValor = '|'.$mMatrizTra[$i]['seridxxx'].'~'.$mMatrizTra[$i]['ptoidxxx'].'~'.$mMatrizTra[$i]['diridxxx'].'~'.$mMatrizTra[$i]['docidxxx'].'|';
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
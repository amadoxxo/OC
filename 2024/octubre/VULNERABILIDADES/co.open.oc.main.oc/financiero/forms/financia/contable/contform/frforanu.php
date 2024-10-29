<?php
  namespace openComex;
   /**
	 * Anular Formularios.
	 * --- Descripcion: me Lista los formularios legalizados y  no legalizados que hayan sido asignados a ese Director
	 * de cuenta. y me da la opcion de chekear los que quiera anular.
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
   	   	      	$i=document.forms['frgrm']['cCheck'].length;
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
  	<style type="text/css">
      SELECT{ font-family: verdana; font-size: 10px; color:#2B547D; background-color:#D8E4F1;}
    </style>
  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
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
			if ($cUsrId == "") {
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

		  $cDirector=0;
     	$qUsrDat  = "SELECT * ";
		  $qUsrDat .= "FROM $cAlfa.SIAI0003 ";
		  $qUsrDat .= "WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" AND ";
	  	$qUsrDat .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
	  	$xUsrDat  = f_MySql("SELECT","",$qUsrDat,$xConexion01,"");
	  	$xRUD = mysql_fetch_array($xUsrDat);
	  	if($xRUD['USRIDXXX']==$xRUD['USRID2XX']){
	  	 $cDirector=$xRUD['USRIDXXX'];
	  	}else{
  	    $qUsrDat2  = "SELECT * ";
		  	$qUsrDat2 .= "FROM $cAlfa.SIAI0003 ";
			  $qUsrDat2 .= "WHERE USRIDXXX = \"{$xRUD['USRID2XX']}\" AND ";
		  	$qUsrDat2 .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
		  	$xUsrDat2  = f_MySql("SELECT","",$qUsrDat2,$xConexion01,"");
		  	$xRUD2 = mysql_fetch_array($xUsrDat2);
		  	if($xRUD2['USRIDXXX']==$xRUD2['USRID2XX']){
		  	  if($xRUD2['USRIDXXX']!=""){
		  		  $cDirector=$xRUD2['USRIDXXX'];
		  		}
		  	}
		  }

		  $y=0;
			$qFoiDat  = "SELECT * ";
			$qFoiDat .= "FROM $cAlfa.ffoi0000 ";
			$qFoiDat .= "WHERE diridxxx = \"$cDirector\" AND regestxx = \"ASIGNADO\" AND doccomex = \"\"";
	    if($_POST['cTipPro']!=""){
			  $qFoiDat .= "AND ptoidxxx=\"{$_POST['cTipPro']}\" ";
	    }
			$qFoiDat .= "ORDER BY CONVERT(seridxxx,signed) ASC ";
			//wMenssage(__FILE__,__LINE__,$qFoiDat);
			$xFoiDat  = f_MySql("SELECT","",$qFoiDat,$xConexion01,"");
			//wMenssage(__FILE__,__LINE__,mysql_num_rows($xFoiDat));
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
				/* Traigo el Nombre del Usuario */
				$qUsrDat = "SELECT USRNOMXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$mMatrizTmp[$i]['diridxxx']}\" LIMIT 0,1";
				$xUsrDat  = f_MySql("SELECT","",$qUsrDat,$xConexion01,"");
				if (mysql_num_rows($xUsrDat) > 0) {
					while ($xRUD = mysql_fetch_array($xUsrDat)) {
						$mMatrizTmp[$i]['usrnomxx'] = $xRUD['USRNOMXX'];
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
			$zRNom   = mysql_fetch_array($xUsrNom);
			$cNomUsr = $zRNom['USRNOMXX'];
			/***** Fin Extracci�n nombre del usuario General. *****/
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
			?>
      <center>
        <script languaje="javascript">
					document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra) ?>";
				</script>
       	<table width="60%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Formularios Asiganos al Usuario: <font color=red><b><?php echo $cNomUsr; ?></legend>
     	       		<center>
     	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
         	      			<td class="name" width="70%">
			             		</td>
         	      		</tr>
         	      	</table>
         	      	<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      	<tr>
           	        <td class="clase08" width="80%">
            	      	<input type="text" class="letra" name = "cSearch" maxlength="20" value = "<?php echo $cSearch ?>" style= "width:80"
            	        	onblur="javascript:this.value=this.value.toUpperCase();
								      											 document.forms['frgrm'].submit();">
              	      <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:hand" title="Buscar"
								      	onClick = "javascript:document.forms['frgrm']['cSearch'].value=document.forms['frgrm']['cSearch'].value.toUpperCase();
								      												  document.forms['frgrm'].submit();">
              	      <img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:hand" title="Mostrar Todo"
								      	onClick ="javascript:document.forms['frgrm']['cSearch'].value='';
								      												 document.forms['frgrm'].submit();">
   	             	     &nbsp;&nbsp;&nbsp; Tipo Producto: &nbsp;&nbsp;&nbsp;
            	       	 <select name="cTipPro" size=1 onchange="javascript:document.forms['frgrm'].submit();">
            	       	 <option value="">TODOS</option>
            	       	 <?php
            	       	 $qPtoDat  = "SELECT * FROM $cAlfa.ffoi0000 WHERE diridxxx=\"{$_COOKIE['kUsrId']}\" AND ";
            	       	 $qPtoDat  .="REGESTXX=\"ASIGNADO\" AND  DOCCOMEX=\"\" GROUP BY ptoidxxx ";
											 $xPtoDat = f_MySql("SELECT","",$qPtoDat,$xConexion01,"");
											 while ($xRPD = mysql_fetch_array($xPtoDat)) {
											   $qPtoDes  = "SELECT * FROM $cAlfa.fpar0132 WHERE ptoidxxx=\"{$xRPD['ptoidxxx']}\" LIMIT 0,1";
											   $xPtoDes  = f_MySql("SELECT","",$qPtoDes,$xConexion01,"");
											   $xRPN = mysql_fetch_array($xPtoDes);
											 ?>
                         <option value="<?php echo $xRPD['ptoidxxx'];?>"><?php echo $xRPN['ptodesxx']; ?></option>
				               <?php }
                       ?>
            	       	 </select>
            	       	 <?php
            	       	 if($_POST['cTipPro']!=""){?>
            	       	 <script>
            	       	   document.forms['frgrm']['cTipPro'].value="<?php echo $_POST['cTipPro']; ?>";
            	       	 </script>
            	       	 <?php }
                       ?>
   	                 </td>
   	                 <td Class="name" width="20%" align="right">
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
															case "ANULAR": ?>
															<!--
															<img src = "<?php echo $cPlesk_Skin_Directory ?>/page_delete.png"
															  onClick = "javascript:f_Verificar_Check();
															                        f_Carga_Variable(document.forms['frgrm']['nRecords'].value);
															                        document.forms['frgrm'].action='frforagr.php';
															                        document.forms['frgrm'].target='fmpro';
															                        document.forms['frgrm'].submit();"
															                        style = "cursor:hand" title="Anular Formulario" id="IdImg">
															-->
                              <input class="name" name="IdImg" type="button" value="Anular" style="cursor:pointer"
															  onClick = "javascript:f_Verificar_Check();
															                        f_Carga_Variable(document.forms['frgrm']['nRecords'].value);
															                        document.forms['frgrm'].action='frforagr.php';
															                        document.forms['frgrm'].target='fmpro';
															                        document.forms['frgrm'].submit();
															                        document.forms['frgrm'].action='';
															                        document.forms['frgrm'].target='';"
															                        style = "cursor:hand" title="Anular Formulario" id="IdImg">
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
   	               <tr bgcolor='white'>
   	                 <td width='100%'>
   	                   &nbsp;
   	                 </td>
   	               </tr>
   	             </table>
       	     		 <table cellspacing="0" width="100%">
         	         <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
           	         <td class="name" width="20%">
           	         	 <a href = "javascript:f_ButtonsAscDes('onclick','ptoidxxx','ASC_NUM')" title="Ordenar"
       	       				 onmouseover="javascript:f_ButtonsAscDes('onmouseover','ptoidxxx','')"
       	       			   onmouseout="javascript:f_ButtonsAscDes('onmouseout','ptoidxxx','')">Pto.</a>&nbsp;
           	         	 <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ptoidxxx">
           	         	 <script language="javascript">f_ButtonsAscDes('','ptoidxxx','')</script>
           	         </td>
           	         <td class="name" width="50%">
           	           <a href = "javascript:f_ButtonsAscDes('onclick','ptodesxx','ASC_AZ')" title="Ordenar"
       	       				 onmouseover="javascript:f_ButtonsAscDes('onmouseover','ptodesxx','')"
       	       				 onmouseout="javascript:f_ButtonsAscDes('onmouseout','ptodesxx','')">Descripcion</a>&nbsp;
           	         	 <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ptodesxx">
           	         	 <script language="javascript">f_ButtonsAscDes('','ptodesxx','')</script>
          	         </td>
           	         <td class="name" width="20%">
           	         	 <a href = "javascript:f_ButtonsAscDes('onclick','seridxxx','ASC_NUM')" title="Ordenar"
       	       				 onmouseover="javascript:f_ButtonsAscDes('onmouseover','seridxxx','')"
       	       				 onmouseout="javascript:f_ButtonsAscDes('onmouseout','seridxxx','')">Serial</a>&nbsp;
           	         	 <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "seridxxx">
           	         	 <script language="javascript">f_ButtonsAscDes('','seridxxx','')</script>
          	         </td>
             	       <td class="name" width="10%" align="right">
           	         	 <input type="checkbox" name="cCheckAll" onClick = "javascript:f_Marca();f_Verificar_Check();f_Carga_Variable(document.forms['frgrm']['nRecords'].value);">
           	         </td>
             	       </tr>
             	       <tr bgcolor = 'white'>
           	           <td class="name" width="20%">&nbsp;
           	           </td>
           	         	 <td class="name" width="50%">&nbsp;
          	         	 </td>
           	         	 <td class="name" width="20%">&nbsp;
          	         	 </td>
             	         <td class="name" width="10%" align="right">&nbsp;
           	         	 </td>
             	       </tr>
					           <script languaje="javascript">
						           document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra) ?>";
						         </script>
 	                   <?php for ($i=0;$i<count($mMatrizTra);$i++) {
 	                   	 if ($i < count($mMatrizTra)) { // Para Controlar el Error
	 	                     $zColor = "{$vSysStr['system_row_impar_color_ini']}";
	                   	   if($y % 2 == 0) {
	                   	   	 $zColor = "{$vSysStr['system_row_par_color_ini']}";
												 } ?>
								        <!--<tr bgcolor = "<?php echo $zColor ?>">-->
								        <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
		                      	<td class="letra7" width="20%"><?php echo $mMatrizTra[$i]['ptoidxxx'];?></td>
	       	                	<td class="letra7" width="50%"><?php echo $mMatrizTra[$i]['ptodesxx'] ?></td>
	       	                	<td class="letra7" width="20%"><?php echo $mMatrizTra[$i]['seridxxx'] ?></td>
	        	              	<td class="letra7" width="10%" align="right">
	        	              	  <input type="checkbox" name="cCheck"  value = "<?php echo $mMatrizTra[$i]['seridxxx'].'~'.$mMatrizTra[$i]['ptoidxxx'] ?>"
	        	              	    onclick="javascript:f_Verificar_Check();f_Carga_Variable(<?php echo count($mMatrizTra) ?>);">
	        	              	</td>
	        	        		</tr>
	                	    	<?php $y++;
 	                    	}
 	                    }
 	                    ?>
 	                    <?php switch ($_COOKIE['kModo']) {
                        case "NUEVO":
				                  $qUsrDat = "SELECT USRNOMXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
				                  $xUsrDat  = f_MySql("SELECT","",$qUsrDat,$xConexion01,"");
				                  $zUsrNom = "USUARIO SIN NOMBRE";
				                  while ($xRUD = mysql_fetch_array($xUsrDat)) {
					                  $zUsrNom = trim($xRUD['USRNOMXX']);
					                  ?>
					                  <script languaje = "javascript">
						                  document.forms['frgrm']['cUsrId'].value  = "<?php echo $_COOKIE['kUsrId'] ?>";
						                  document.forms['frgrm']['cUsrNom'].value = "<?php echo $zUsrNom ?>";
					                  </script>
				                    <?php
				                  }
			                  break;
			                  case "EDITAR":?>
				                  <script languaje = "javascript">
  					               document.forms['frgrm']['gTipSav'].value    = "UPDATE";
				                  </script>
			                    <?php
			                  break;
                        case "ANULAR":?>
				                  <script languaje = "javascript">
					                  document.forms['frgrm']['gTipSav'].value    = "ANULAR";
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
          //f_Mensaje(__FILE__,__LINE__,"$cComMemo == {$mMatrizTra[$i]['seridxxx']}~{$mMatrizTra[$i]['ptoidxxx']}");
          $cValor = '|'.$mMatrizTra[$i]['seridxxx'].'~'.$mMatrizTra[$i]['ptoidxxx'].'|';
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
          $cValor = '|'.$mMatrizTra[$i]['seridxxx'].'~'.$mMatrizTra[$i]['ptoidxxx'].'|';
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
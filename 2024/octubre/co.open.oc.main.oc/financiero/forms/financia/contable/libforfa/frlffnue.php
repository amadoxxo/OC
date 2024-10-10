<?php
  namespace openComex;
   /**
	 * Liberacion de Formularios Facturados.
	 * --- Descripcion: Me lista los formularios con estado FACTURADO y que estan disponibles para ser liberados.
	 * @author Johana Arboleda <dp5@opentecnologia.com.co>
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

	   function f_Links(xLink,xSwitch,xIteration) {
	        var nX    = screen.width;
	        var nY    = screen.height;
	        switch (xLink){
	          case "cSerId":
	            if(document.frgrm['cSerId'].value == "") {
	                xSwitch = "WINDOW";
	            }
	            if (xSwitch == "VALID") {
	              var cRuta  = "frlffdos.php?gWhat=VALID&gFunction="+xLink+
	                            "&gSerId="+document.frgrm['cSerId'].value.toUpperCase();
	              parent.fmpro.location = cRuta;
	            } else {
	              var zNx     = (nX-600)/2;
	              var zNy     = (nY-250)/2;
	              var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
	              var cRuta   = "frlffdos.php?gWhat=WINDOW&gFunction="+xLink+
	                            "&gSerId="+document.frgrm['cSerId'].value.toUpperCase();
	              cWindow = window.open(cRuta,"cWindow",zWinPro);
	              cWindow.focus();
	            }
	          break;
	          case "cDocNro":
	            if(document.frgrm['cDocNro'].value == "") {
	              xSwitch = "WINDOW";
	            }
	            if (xSwitch == "VALID") {
	              var cRuta  = "frlffdos.php?gWhat=VALID&gFunction="+xLink+
	                                        "&gDocNro="+document.frgrm['cDocNro'].value.toUpperCase();
	              parent.fmpro.location = cRuta;
	            } else {
	              var nNx     = (nX-600)/2;
	              var nNy     = (nY-250)/2;
	              var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
	              var cRuta   = "frlffdos.php?gWhat=WINDOW&gFunction="+xLink+
	                            "&gDocNro="+document.frgrm['cDocNro'].value.toUpperCase();
	              cWindow = window.open(cRuta,"cWindow",zWinPro);
	              cWindow.focus();
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
 	    			 	zWin = window.open('frlffobs.php','cObserv',settings);
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
 	      document.forms['frgrm'].action='frlffgra.php';
 	      document.forms['frgrm'].target='fmpro';
 	      document.forms['frgrm'].submit();
 	    }

     	function f_Carga_Variable(xRecords) {
  	  	//alert(xRecords);
    		var zSwitch = "0";
          document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kModo=LIBERAR;path="+"/";
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

	     function f_Mostrar_Busqueda(xValor) {
	    	  if(xValor =="DO") {
	    		  document.getElementById('tblDo').style.display="block";
            document.getElementById('tblFormu').style.display="none";
          } else {
        	  document.getElementById('tblDo').style.display="none";
            document.getElementById('tblFormu').style.display="block";
          }
	     }
  	 	    	 
	     function f_Limpiar(){
        document.forms['frgrm']['cSucId'].value='';
        document.forms['frgrm']['cDocNro'].value='';
        document.forms['frgrm']['cDocSuf'].value='';
        document.forms['frgrm']['cSerId'].value='';
        f_Mostrar_Busqueda(document.forms['frgrm']['cBusPor'].value);
        document.forms['frgrm'].submit();
      }
		</script>

		<style type="text/css">
      SELECT{ font-family: verdana; font-size: 10px; color:#2B547D; background-color:#D8E4F1;}
    </style>

  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
		<form name = "frgrm" action = "frlffnue.php" method = "post">
   		<input type = "hidden" name = "cChekeados" value = "0">
   		<input type = "hidden" name = "nRecords"   value = "0">
   		<input type = "hidden" name = "cComMemo"   value = "">
   		<input type = "hidden" name = "cSortField" value = "<?php echo $cSortField ?>">
      <input type = "hidden" name = "cSortType"  value = "<?php echo $cSortType ?>">
      <input type = "hidden" name = "cGofId"     value = "">
      <textarea style="width:400" rows=10 name="cObserv" id="cObserv"></textarea>
      <script type="text/javascript">document.getElementById('cObserv').style.display="none";</script>
 	    <?php

 	    #Realizo la consulta de los formularios del DO seleccionado que estan disponibles para liberacion.
       $qFoiDat = "";
       switch ($_POST['cBusPor']) {
        case "DO":
          if ($_POST['cDocNro'] !="") {
            $qFoiDat  = "SELECT *, ";
		        $qFoiDat .= "CONCAT(comidsxx,\"-\",comcodsx,\"-\",comcscsx) AS comidsxx ";
		        $qFoiDat .= "FROM $cAlfa.ffoi0000 ";
		        $qFoiDat .= "WHERE " ;
		        $qFoiDat .= "sucidxxx = \"$cSucId\"  AND ";
		        $qFoiDat .= "doccomex = \"$cDocNro\" AND ";
		        $qFoiDat .= "docsufxx = \"$cDocSuf\" AND ";
		        $qFoiDat .= "ffoi0000.regestxx = \"FACTURADO\" ";
		        $qFoiDat .= "ORDER BY ABS(seridxxx) ASC ";
          }
        break;
        case "FORMULARIO":
          if ($_POST['cSerId'] !="") {
            $qFoiDat  = "SELECT *, ";
		        $qFoiDat .= "CONCAT(comidsxx,\"-\",comcodsx,\"-\",comcscsx) AS comidsxx ";
		        $qFoiDat .= "FROM $cAlfa.ffoi0000 ";
		        $qFoiDat .= "WHERE " ;
		        $qFoiDat .= "seridxxx = \"{$_POST['cSerId']}\" AND ";
		        $qFoiDat .= "ffoi0000.regestxx = \"FACTURADO\" ";
		        $qFoiDat .= "ORDER BY ABS(seridxxx) ASC ";
          }
        break;
        default:
          //NO HACE NADA
        break;
       } 
      
       if ($qFoiDat !="") {
        $y=0;
        $xFoiDat  = f_MySql("SELECT","",$qFoiDat,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qFoiDat."~".mysql_num_rows($xFoiDat));
        # Cargo la Matriz con los ROWS del Cursor 
        $i=0;
        while ($xRF = mysql_fetch_array($xFoiDat)) {
          
          ## Traigo la descripcion del tipo Productos Formulario ##
          $qPtoDes  = "SELECT ";
          $qPtoDes .= "$cAlfa.fpar0132.ptodesxx ";
          $qPtoDes .= "FROM $cAlfa.fpar0132 ";
          $qPtoDes .= "WHERE ";
          $qPtoDes .= "$cAlfa.fpar0132.ptoidxxx = \"{$xRF['ptoidxxx']}\" LIMIT 0,1";
          $xPtoDes  = f_MySql("SELECT","",$qPtoDes,$xConexion01,"");
          if (mysql_num_rows($xPtoDes) > 0) {
            while ($xRPD = mysql_fetch_array($xPtoDes)) {
              $xRF['ptodesxx'] = $xRPD['ptodesxx'];
            }
          } else {
            $xRF['ptodesxx'] = "TIPO FORMULARIO SIN NOMBRE";
          }
          $mMatrizTmp[$i] = $xRF;
          $i++;
        }
        $mMatrizTra = $mMatrizTmp;
        
        if ($vSortField != "" && $vSortType != "") {
          $mMatrizTra = f_Sort_Array_By_Field($mMatrizTra,$vSortField,$vSortType);
        }
       } 
       # Fin de Cargo la Matriz con los ROWS del Cursor
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
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_ok_bg.gif" style="cursor:pointer" onClick = "javascript:f_Enviar_Form();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Liberar</td>
							<td width="91" height="21" Class="name" background="<?php echo $cPlesk_Skin_Directory ?>/btn_cancel_bg.gif" style="cursor:pointer" onClick = 'javascript:f_Retorna()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>
						</tr>
					</table>
				</center>

       	<table width="780" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Formularios para Liberar (<?php echo count($mMatrizTra) ?>)</legend>
     	       		<center>
     	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td class="clase08" width="170">Buscar por:<br>
                        <select Class = "letrase" style = "width:160" name = "cBusPor" id = "cBusPor" onchange="javascript:f_Mostrar_Busqueda(this.value);f_Limpiar()">
                          <option value="DO">DO</option>
                          <option value="FORMULARIO">FORMULARIO</option>
                        </select>                        
                      </td>
                      <td class="clase08" style = "width:160">
                        <div id="tblDo">
                          Seleccione el Do:<br>
                          <input type="text"   class="letra" name="cSucId"  style="width:30" value="<?php echo $_POST['cSucId']; ?>" readonly>
                          <input type="text"   class="letra" name="cDocNro" style="width:80" value="<?php echo $_POST['cDocNro']; ?>"
                                 onblur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';
                                                      f_Links('cDocNro','VALID');"
                                 onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';
                                                     document.forms['frgrm']['cSucId'].value='';
                                                     document.forms['frgrm']['cDocNro'].value='';
                                                     document.forms['frgrm']['cDocSuf'].value='';
                                                     document.forms['frgrm']['cSerId'].value='';">
                          <input type="text" class="letra"   name="cDocSuf" style="width:40" value="<?php echo $_POST['cDocSuf']; ?>" readonly>
                        </div>
                        <div id="tblFormu">
                          Seleccione el Formulario:<br>
                          <input type="text"   class="letra" name="cSerId" style="width:150" value="<?php echo $_POST['cSerId']; ?>"
                                 onblur = "javascript:this.style.background='<?php echo $vSysStr['system_imput_onblur_color'] ?>';
                                                      f_Links('cSerId','VALID');"
                                 onFocus="javascript:this.style.background='<?php echo $vSysStr['system_imput_onfocus_color'] ?>';
                                                     document.forms['frgrm']['cSerId'].value='';
                                                     document.forms['frgrm']['cSucId'].value='';
                                                     document.forms['frgrm']['cDocNro'].value='';
                                                     document.forms['frgrm']['cDocSuf'].value='';">
                        </div>
                      </td>
                      <td>
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:hand" title="Buscar"
                             onClick = "javascript:document.forms['frgrm']['cSucId'].value=document.forms['frgrm']['cSucId'].value.toUpperCase();
                                                   document.forms['frgrm']['cDocNro'].value=document.forms['frgrm']['cDocNro'].value.toUpperCase();
                                                   document.forms['frgrm']['cDocSuf'].value=document.forms['frgrm']['cDocSuf'].value.toUpperCase();
                                                   document.forms['frgrm']['cSerId'].value=document.forms['frgrm']['cSerId'].value.toUpperCase();
                                                   document.forms['frgrm'].submit();">
                        <img src = "<?php echo $cPlesk_Skin_Directory ?>/spamfilter_on.gif" style = "cursor:hand" title="Nueva Busqueda"
                             onClick = "javascript:f_Limpiar();">
                      </td>
                    </tr>
                  </table>
         	      	<script type="text/javascript">
                   if('<?php echo $_POST['cBusPor'] ?>' != '') {
                     document.forms['frgrm']['cBusPor'].value = '<?php echo $_POST['cBusPor'] ?>';
                   }
                   f_Mostrar_Busqueda(document.forms['frgrm']['cBusPor'].value);
                  </script>
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
          	          <td class="name" width="14%">
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
           	          <td class="name" width="15%">
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
           	         	<td class="name" width="15%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','ptodesxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','ptodesxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','ptodesxx','')">Descripcion</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ptodesxx">
           	         		<script language="javascript">f_ButtonsAscDes('','ptodesxx','')</script>
          	         	</td>
          	         	<td class="name" width="20%">
                        <a href = "javascript:f_ButtonsAscDes('onclick','comidsxx','ASC_AZ')" title="Ordenar"
                        onmouseover="javascript:f_ButtonsAscDes('onmouseover','comidsxx','')"
                        onmouseout="javascript:f_ButtonsAscDes('onmouseout','comidsxx','')">Comprobante</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "comidsxx">
                        <script language="javascript">f_ButtonsAscDes('','comidsxx','')</script>
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
                      for($i=0;$i<count($mMatrizTra);$i++) {
 	                    	if ($i < count($mMatrizTra)) { // Para Controlar el Error
	 	                    	$zColor = "{$vSysStr['system_row_impar_color_ini']}";
	                   	    if($y % 2 == 0) {
	                   	    	$zColor = "{$vSysStr['system_row_par_color_ini']}";
													}


													?>
								        <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
										        <td class="letra7"><?php echo $mMatrizTra[$i]['sucidxxx'] ?></td>
										        <td class="letra7"><?php echo $mMatrizTra[$i]['doccomex'] ?></td>
										        <td class="letra7"><?php echo $mMatrizTra[$i]['docsufxx'] ?></td>
										        <td class="letra7"><?php echo $mMatrizTra[$i]['seridxxx'] ?></td>
								            <td class="letra7"><?php echo $mMatrizTra[$i]['ptoidxxx'];?></td>
	       	                	<td class="letra7"><?php echo $mMatrizTra[$i]['ptodesxx'] ?></td>
	       	                	<td class="letra7"><?php echo $mMatrizTra[$i]['comidsxx'] ?></td>
	       	                	<td class="letra7"><?php echo $mMatrizTra[$i]['regestxx'] ?></td>
	        	              	<td class="letra7" align="right">
	        	              	  <input type="checkbox" name="cCheck"  value = "<?php echo $mMatrizTra[$i]['seridxxx'].'~'.$mMatrizTra[$i]['ptoidxxx'].'~'.$mMatrizTra[$i]['doccomex'].'~'.$mMatrizTra[$i]['sucidxxx'].'~'.$mMatrizTra[$i]['docsufxx'] ?>"
	        	              	    onclick="javascript:f_Verificar_Check();f_Carga_Variable(<?php echo count($mMatrizTra) ?>);">
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
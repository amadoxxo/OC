<?php
  namespace openComex;
   /**
	 * Liberacion de Formularios Facturados.
	 * --- Descripcion: Me lista los formularios con estado FACTURADO y que estan disponibles para ser liberados.
	 * @author Johana Arboleda <dp5@opentecnologia.com.co>
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
							var cRuta  = "frsegobs.php?gWhat=VALID&gFunction="+xLink+
                            "&gSerId="+document.frgrm['cSerId'].value.toUpperCase();
							parent.fmpro.location = cRuta;
						} else {
		  				var zNx     = (nX-600)/2;
							var zNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+zNx+',top='+zNy;
							var cRuta   = "frsegobs.php?gWhat=WINDOW&gFunction="+xLink+
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
							var cRuta  = "frsegobs.php?gWhat=VALID&gFunction="+xLink+
							                          "&gDocNro="+document.frgrm['cDocNro'].value.toUpperCase();
							parent.fmpro.location = cRuta;
						} else {
	  					var nNx     = (nX-600)/2;
							var nNy     = (nY-250)/2;
							var zWinPro = 'width=600,scrollbars=1,height=250,left='+nNx+',top='+nNy;
							var cRuta   = "frsegobs.php?gWhat=WINDOW&gFunction="+xLink+
							              "&gDocNro="+document.frgrm['cDocNro'].value.toUpperCase();
							cWindow = window.open(cRuta,"cWindow",zWinPro);
					  	cWindow.focus();
						}
			    break;
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
		<form name = "frgrm" action = "frlffprn.php" method = "post">
 	    <?php

			 #Realizo la consulta de los formularios del DO seleccionado que estan disponibles para liberacion.
			
 	     $qFoiDat = "";
 	     switch ($_POST['cBusPor']) {
 	     	case "DO":
	 	     	if ($_POST['cDocNro'] !="") {
	 	     	
		 	     	$qFoiDat  = "SELECT ";
						$qFoiDat .= "$cAlfa.ffob0000.*, ";
						$qFoiDat .= "IF($cAlfa.A.USRNOMXX <> \"\",$cAlfa.A.USRNOMXX,\"USUARIO SIN NOMBRE\") AS usrnomxx, ";
						$qFoiDat .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"DIRECTOR SIN NOMBRE\") AS dirnomxx, ";
						$qFoiDat .= "IF($cAlfa.fpar0123.gofdesxx <> \"\",$cAlfa.fpar0123.gofdesxx,\"SIN DESCRIPCION\") AS gofdesxx ";
					  $qFoiDat .= "FROM $cAlfa.ffob0000 ";
					  $qFoiDat .= "LEFT JOIN $cAlfa.SIAI0003 AS A ON $cAlfa.ffob0000.regusrxx = $cAlfa.A.USRIDXXX ";
					  $qFoiDat .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.ffob0000.diridxxx = $cAlfa.SIAI0003.USRIDXXX ";
					  $qFoiDat .= "LEFT JOIN $cAlfa.fpar0123 ON $cAlfa.ffob0000.gofidxxx = $cAlfa.fpar0123.gofidxxx AND $cAlfa.fpar0123.goftipxx = \"FORMULARIOS\" ";
						$qFoiDat .= "WHERE " ;
						$qFoiDat .= "$cAlfa.ffob0000.obstipxx = \"LIBERARFAC\" AND ";
					  $qFoiDat .= "$cAlfa.ffob0000.docsucxx = \"{$_POST['cSucId']}\"  AND ";
					  $qFoiDat .= "$cAlfa.ffob0000.docnroxx = \"{$_POST['cDocNro']}\" AND ";
					  $qFoiDat .= "$cAlfa.ffob0000.docsufxx = \"{$_POST['cDocSuf']}\" ";
						$qFoiDat .= "ORDER BY ABS($cAlfa.ffob0000.seridxxx) ";
	 	     	}
 	     	break;
 	     	case "FORMULARIO":
 	        if ($_POST['cSerId'] !="") {
 	        
 	        	$qFoiDat  = "SELECT ";
						$qFoiDat .= "$cAlfa.ffob0000.*, ";
						$qFoiDat .= "IF($cAlfa.A.USRNOMXX <> \"\",$cAlfa.A.USRNOMXX,\"USUARIO SIN NOMBRE\") AS usrnomxx, ";
						$qFoiDat .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"DIRECTOR SIN NOMBRE\") AS dirnomxx, ";
						$qFoiDat .= "IF($cAlfa.fpar0123.gofdesxx <> \"\",$cAlfa.fpar0123.gofdesxx,\"SIN DESCRIPCION\") AS gofdesxx ";
					  $qFoiDat .= "FROM $cAlfa.ffob0000 ";
					  $qFoiDat .= "LEFT JOIN $cAlfa.SIAI0003 AS A ON $cAlfa.ffob0000.regusrxx = $cAlfa.A.USRIDXXX ";
					  $qFoiDat .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.ffob0000.diridxxx = $cAlfa.SIAI0003.USRIDXXX ";
					  $qFoiDat .= "LEFT JOIN $cAlfa.fpar0123 ON $cAlfa.ffob0000.gofidxxx = $cAlfa.fpar0123.gofidxxx AND $cAlfa.fpar0123.goftipxx = \"FORMULARIOS\" ";
						$qFoiDat .= "WHERE " ;
						$qFoiDat .= "$cAlfa.ffob0000.obstipxx = \"LIBERARFAC\" AND ";
						$qFoiDat .= "$cAlfa.ffob0000.seridxxx = \"{$_POST['cSerId']}\" ";
					  $qFoiDat .= "ORDER BY $cAlfa.ffob0000.docsucxx, $cAlfa.ffob0000.docnroxx, $cAlfa.ffob0000.docsufxx ";
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
		   } 
				# Fin de Cargo la Matriz con los ROWS del Cursor

		?>
		<center>
			<br></br>
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
								<legend>Formularios para Liberar (<?php echo count($mMatrizTra) ?>)</legend>
								<center><br>
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
									<table cellspacing="0" width="100%" id="tblDatos">
									  <tr bgcolor = "<?php echo $vSysStr['system_row_title_color_ini']; ?>">
										  <td class="name" width = "06%">Csc.</td>
										  <td class="name" width = "10%">Formulario</td>
										  <td class="name" width = "10%">Do</td>
										  <td class="name" width = "12%">Director</td>
										  <td class="name" width = "12%">Grupo</td>
										  <td class="name" width = "25%">Observaciones</td>
										  <td class="name" width = "12%">Usuario</td>
										  <td class="name" width = "08%">Fecha</td>
										  <td class="name" width = "05%">Hora</td>										  
									  </tr>
									  <tr bgcolor = "white">
									   <td class="name" colspan = "9">&nbsp;</td>
									  </tr>
										<?php
										echo $cDatCab;
										$y=0;
										for($i=0;$i<count($mMatrizTra);$i++) {
											if ($i < count($mMatrizTra)) { // Para Controlar el Error
                        ?> 
                        <tr bgcolor = "<?php echo $zColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $zColor ?>')">
                          <td class="letra7"><?php echo $mMatrizTra[$i]['obscscxx'] ?></td>                          
                          <td class="letra7"><?php echo $mMatrizTra[$i]['seridxxx'] ?></td>                          
                          <td class="letra7"><?php echo $mMatrizTra[$i]['docsucxx']."-".$mMatrizTra[$i]['docnroxx']."-".$mMatrizTra[$i]['docsufxx'] ?></td>
                          <td class="letra7"><?php echo $mMatrizTra[$i]['dirnomxx'] ?></td>                          
                          <td class="letra7"><?php echo $mMatrizTra[$i]['gofdesxx'] ?></td>                          
                          <td class="letra7"><?php echo $mMatrizTra[$i]['obsobsxx'] ?></td>                          
                          <td class="letra7"><?php echo $mMatrizTra[$i]['usrnomxx'] ?></td>
                          <td class="letra7"><?php echo $mMatrizTra[$i]['regfcrex'] ?></td>
                          <td class="letra7"><?php echo $mMatrizTra[$i]['reghcrex'] ?></td>
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
		</form>
	</body>
</html>
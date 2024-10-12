<?php
  namespace openComex;
/**
 * Tracking Reporte de Estado de Cartera
 * Este programa permite realizar consultas rapidas al estado de cartera
 * @author Johana Arboleda <johana.arboleda@opentecnologia.com.co>
 * @package Opencomex
 */

  // ini_set('error_reporting', E_ERROR);
	// ini_set("display_errors","1");

	ini_set("memory_limit","4096M");
	set_time_limit(0);

	include("../../../../../config/config.php");
	include("../../../../libs/php/utility.php");
	include("../../../../libs/php/utiescar.php");

	/**
    *  Cookie fija
    */
   $kDf = explode("~",$_COOKIE["kDatosFijos"]);
   $kMysqlHost = $kDf[0];
   $kMysqlUser = $kDf[1];
   $kMysqlPass = $kDf[2];
   $kMysqlDb   = $kDf[3];
   $kUser      = $kDf[4];
   $kLicencia  = $kDf[5];
   $swidth     = $kDf[6];

	/* Busco en la 05 que Tiene Permiso el Usuario*/
  $qUsrMen  = "SELECT * ";
  $qUsrMen .= "FROM $cAlfa.sys00005 ";
  $qUsrMen .= "WHERE ";
  $qUsrMen .= "sys00005.modidxxx = \"{$_COOKIE['kModId']}\" AND ";
  $qUsrMen .= "sys00005.proidxxx = \"{$_COOKIE['kProId']}\" AND ";
  $qUsrMen .= "sys00005.menimgon <> '' ";
  $qUsrMen .= "ORDER BY sys00005.menordxx";
  $xUsrMen  = f_MySql("SELECT","",$qUsrMen,$xConexion01,"");

 ?>

<html>
	<head>
  	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
   	<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
   	<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/date_picker.js'></script>
		<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/utility.js'></script>
		<!-- <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/ajax.js'></script>
		<link rel="stylesheet" type="text/css" href="../../../../../programs/gwtext/resources/css/ext-all.css">
  	<script type="text/javascript" src="../../../../../programs/gwtext/adapter/ext/ext-base.js"></script>
  	<script type="text/javascript" src="../../../../../programs/gwtext/ext-all.js"></script>
  	<script language="JavaScript" src="../../../../../programs/gwtext/conexijs/loading/loading.js"></script> -->
  	<style type="text/css">
  		fieldset {
  			margin: 5px 0px 0px !important;
				padding: 0px 5px 5px !important;
  		}
  		td {
  			margin: 2px;
				padding: 2px;
  		}
  	</style>

   	<script language="javascript">

     	function f_Link(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
      	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes="+xMenDes+";path="+"/";
      	document.cookie="kModo="+xOpcion+";path="+"/";
      	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	document.location = xForm; // Invoco el menu.
      }

      function fnRetorna() { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
        document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      }

      function fnAgendarReporte() {
				document.forms['frgrm']['nTimesSave'].value++;
				document.getElementById('bntProcesar').disabled = true;
        document.forms['frgrm'].action="frescgra.php";
        document.forms['frgrm'].target="fmpro";
        document.forms['frgrm'].submit();
        document.forms['frgrm'].action="frescini.php";
        document.forms['frgrm'].target="fmwork";
      }

      function fnGenerarReporte(xTerId, xTipo) {
      	/**
      	 * xTipo:
      	 * 1->Pantalla
      	 * 2->Excel
      	 * 3->Pdf
      	 */
      	var cRuta = "frescprn.php?dHasta=<? echo date('Y-m-d') ?>&cTerId="+xTerId+"&cTipo="+xTipo+"&cColPed="+document.forms['frgrm']['cColPed'].value;

      	if (xTerId != "") {
      		if(xTipo == 2) {
      			document.location = cRuta; // Invoco el menu.
          }else{
        	  var zX      = screen.width;
            var zY      = screen.height;
            var zNx     = (zX-30)/2;
            var zNy     = (zY-100)/2;
            var zNy2    = (zY-100);
            var zWinPro = "width="+zX+",scrollbars=1,height="+zNy2+",left="+1+",top="+50;
            var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
            zWindow = window.open(cRuta,cNomVen,zWinPro);
            zWindow.focus();
          }
      	} else {
      		alert("Debe Seleccionar un Cliente, Verifique.");
      	}
      }

			function f_Imprimir(xModo) {
				if (document.forms['frgrm']['nRecords'].value != "0"){
					switch (document.forms['frgrm']['nRecords'].value) {
  					case "1":
  						if (document.forms['frgrm']['oChkCom'].checked == true) {
   						  var mComDat = document.forms['frgrm']['oChkCom'].id.split("~");
   						  fnGenerarReporte(mComDat[0], '3');
  						}
  					break;
  					default:
  						var nSw_Prv = 0;
  						for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++) {
  							if (document.forms['frgrm']['oChkCom'][i].checked == true && nSw_Prv == 0) {
  								nSw_Prv = 1;
   							  var mComDat = document.forms['frgrm']['oChkCom'][i].id.split("~");
   							  fnGenerarReporte(mComDat[0], '3');
  							}
  						}
  					break;
  				}
	      }
	    }

      function f_Marca() {
      	if (document.forms['frgrm']['oChkComAll'].checked == true){
      	  if (document.forms['frgrm']['nRecords'].value == 1){
      	  	document.forms['frgrm']['oChkCom'].checked=true;
      	  } else {
	      		if (document.forms['frgrm']['nRecords'].value > 1){
			      	for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
   	   	      	document.forms['frgrm']['oChkCom'][i].checked = true;
			      	}
			      }
      	  }
      	} else {
	      	if (document.forms['frgrm']['nRecords'].value == 1){
      	  	document.forms['frgrm']['oChkCom'].checked=false;
      	  } else {
      	  	if (document.forms['frgrm']['nRecords'].value > 1){
				      for (i=0;i<document.forms['frgrm']['oChkCom'].length;i++){
				      	document.forms['frgrm']['oChkCom'][i].checked = false;
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
  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
		<form name = "frgrm" action = "frescini.php" method = "post" target = "fmwork">
   		<input type = "hidden" name = "nRecords"   value = "">
   		<input type = "hidden" name = "nLimInf"    value = "<?php echo $nLimInf ?>">
   		<input type = "hidden" name = "cSortField" value = "<?php echo $cSortField ?>">
   		<input type = "hidden" name = "cSortType"  value = "<?php echo $cSortType ?>">
   		<input type = "hidden" name = "cBuscar"    value = "<?php echo $_POST['cBuscar'] ?>">
			<input type = "hidden" name = "nTimesSave" value = "0">

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
				if ($nLimInf == "" && $nLimSup == "") {
					$nLimInf = "00";
					$nLimSup = $vSysStr['system_rows_page_ini'];
				}

				if ($nPaginas == "") {
					$nPaginas = "1";
				}

        $nSwitch = 0;

        if ($vSysStr['system_reporte_estado_cartera_background'] != "SI") {
          #Instancionado Objetos de Estructuras Reporte Estado Cartera
          $objEstructurasEstadoCartera = new cEstructurasEstadoCartera();

          ##Instanciando Objeto para Creacion de Estructuras##
          $vParametros['TIPOESTU'] = "ERRORES";
          $mReturnTablaE  = $objEstructurasEstadoCartera->fnCrearEstructurasEstadoCartera($vParametros);

          $vParametros = array();
          $vParametros['TIPOESTU'] = "ESTADOCARTERA";
          $mReturnTablaR  = $objEstructurasEstadoCartera->fnCrearEstructurasEstadoCartera($vParametros);

          if($mReturnTablaR[0] == "true" && $mReturnTablaE[0] == "true") {
            //No hace nada
          }else{
            $nSwitch = 1;
            for($nR=1;$nR<count($mReturnTablaR);$nR++){
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= $mReturnTablaR[$nR]."\n";
            }

            for($nR=1;$nR<count($mReturnTablaE);$nR++){
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= $mReturnTablaE[$nR]."\n";
            }
          }

          if ($nSwitch == 0) {
            /* Enviando Datos recibidos */
            $objEstadoCartera = new cEstadoCartera(); // se instancia la clase cTOE
            $vDatos['FECCORTE'] = date('Y-m-d');     //FECHA DE CORTE
            $vDatos['BUSCARXX'] = $_POST['cSearch']; //BUSQUEDA
            $vDatos['TABLAXXX'] = $mReturnTablaR[1]; //TABLA PRINCIPAL
            $vDatos['TABLAERR'] = $mReturnTablaE[1]; //TABLA DE ERRORES

            $mReturnReporte = $objEstadoCartera->fnReporteEstadoCartera($vDatos);

            if($mReturnReporte[0] == "true") {
              //Se ejecuto con Exito
            } else {
              $nSwitch = 1;
              for($nR=1;$nR<count($mReturnReporte);$nR++){
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= $mReturnReporte[$nR]."\n";
              }
            }
          }
        }

        if ($vSysStr['system_reporte_estado_cartera_background'] == "SI") {
          /**
           * Solo puede ejecutarse un proceso en background a la vez
           */
          $qProBg  = "SELECT * ";
          $qProBg .= "FROM $cBeta.sysprobg ";
          $qProBg .= "WHERE ";
          $qProBg .= "pbadbxxx = \"$cAlfa\" AND ";
          $qProBg .= "pbamodxx = \"MODFACTURACION\" AND ";
          $qProBg .= "pbatinxx = \"ESTADOCARTERA\" AND ";
          $qProBg .= "regusrxx = \"$kUser\" ";
          $qProBg .= "ORDER BY pbaidxxx DESC LIMIT 0,1 ";
          $xProBg  = f_MySql("SELECT","",$qProBg,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qProBg."~".mysql_num_rows($xProBg));
          $cTextProBg = "";
          if (mysql_num_rows($xProBg) == 0) {
            $cTextProBg = "No Se Encontro Proceso en para la Generaci&oacute; del Reporte. <b>Por Favor de Click en Procesar</b>.&nbsp;&nbsp;&nbsp;";
          } else {
            $xRB = mysql_fetch_array($xProBg);

            /**
             * Armando parametros para enviar al utiescar
             */
            $mTablas = explode("~",$xRB['pbatabxx']);

            /**
             * Vectore de tablas temporales
             */
            $mReturnTablaR[1] = $mTablas[0];
            $mReturnTablaE[1] = $mTablas[1];

						$cTextProBg = "<b>&Uacute;ltimo Proceso: {$xRB['regdcrex']}</b><br>";

            switch ($xRB['regestxx']) {
              case "PROCESADO":
                if ($xRB['pbarespr'] == "EXITOSO") {
                  //Verificando si la tabla aun existe
                  $qExiTab = "SHOW TABLES FROM $cAlfa LIKE \"{$mReturnTablaR[1]}\"";
                  $xExiTab  = f_MySql("SELECT","",$qExiTab,$xConexion01,"");
      				    //f_Mensaje(__FILE__,__LINE__,$qExiTab." ~ ".mysql_num_rows($xExiTab));
      				    if (mysql_num_rows($xExiTab) == 0) {
                    $nSwitch = 1;
                    $cTextProBg .= "La Consulta Ya No Se Encuentra Disponible. <b>Por favor de Click en Procesar</b>.&nbsp;&nbsp;&nbsp;";
                  }
                } else {
                  $nSwitch = 1;
                  $cTextProBg .= "La Generacion del Reporte de Cartera Presento Errores.&nbsp;&nbsp;&nbsp;".

                  $qErrores = "SELECT * FROM $mReturnTablaE[1]";
									$xErrores  = f_MySql("SELECT","",$qErrores,$xConexion01,"");
									// f_Mensaje(__FILE__,__LINE__,$qErrores."~".mysql_num_rows($xErrores));
                  while($xRE = mysql_fetch_array($xErrores)){
                    $cTextProBg .= "{$xRE['DESERROR']}&nbsp;&nbsp;&nbsp;";
                  }
                }
              break;
              case "ACTIVO":
                $nSwitch = 1;
                $cTextProBg .= "Existe un Proceso en Curso para Generar el Reporte.&nbsp;&nbsp;&nbsp;";
              break;
              default:
                $nSwitch = 1;
                $cTextProBg .= "No Se Encontro Proceso para la Generaci&oacute;n del Reporte. <b>Por favor de Click en Procesar</b>.&nbsp;&nbsp;&nbsp;";
              break;
            }
          }
        }

        if ($nSwitch == 0) {

					//Trayendo totales
					$qTotales  = "SELECT ";
          $qTotales .= "SUM(saldotot) AS saldotot, ";
          $qTotales .= "SUM(saldopro) AS saldopro  ";
          $qTotales .= "FROM $cAlfa.{$mReturnTablaR[1]} ";
          $xTotales = mysql_query($qTotales,$xConexion01);
          // echo $qTotales."~".mysql_num_rows($xTotales)."<br><br>";
          $vTotales = mysql_fetch_array($xTotales);

					$nTotCar = $vTotales['saldotot']+0;
					$nTotPro = $vTotales['saldopro']+0;

          $qDatMov  = "SELECT * ";
          $qDatMov .= "FROM $cAlfa.{$mReturnTablaR[1]} ";
          $xDatMov = mysql_query($qDatMov,$xConexion01);
          // echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";
          $i=0;
          while ($xRDM = mysql_fetch_array($xDatMov)) {
            $zMatrizTmp[$i] = $xRDM;
            $i++;
          }

  				$zMatrizTmp = f_Sort_Array_By_Field($zMatrizTmp,"clinomxx","ASC_AZ");
  				/* Fin de Cargo la Matriz con los ROWS del Cursor */

  				/***** Si el $_POST['cSearch'] Viene Cargado Busco el Patron en Todos los Campos de la Matriz *****/
  				if ($_POST['cSearch'] != "") {
  					$mMatrizTra = array();
  					for ($i=0,$j=0;$i<count($zMatrizTmp);$i++) {

  						if (substr_count(strtoupper($_POST['cSearch']),"=") > 0) {
  							$mPatron = explode("=",$_POST['cSearch']); $cCriterio = "EQUAL";
  						} else {
  							if (substr_count(strtoupper($_POST['cSearch']),".") > 0) {
  								$mPatron = explode(".",$_POST['cSearch']); $cCriterio = "AND";
  							} else {
  								if (substr_count(strtoupper($_POST['cSearch']),",") > 0) {
  									$mPatron = explode(",",$_POST['cSearch']); $cCriterio = "OR";
  								} else {
  									$mPatron = array("{$_POST['cSearch']}"); $cCriterio = "NOTHING";
  								}
  							}
  						}

  						$cCadena  = $zMatrizTmp[$i]['teridxxx']."~";
  						$cCadena .= $zMatrizTmp[$i]['clinomxx']."~";
  						$cCadena .= $zMatrizTmp[$i]['saldonvx']."~";
  						$cCadena .= $zMatrizTmp[$i]['saldoven']."~";
  						$cCadena .= $zMatrizTmp[$i]['saldoafx']."~";
  						$cCadena .= $zMatrizTmp[$i]['saldotot']."~";
  						$cCadena .= $zMatrizTmp[$i]['saldopro'];

  						$nCont_Find = 0;
  						switch ($cCriterio) {
  							case "EQUAL":
  								for ($k=0;$k<count($mPatron);$k++) {
  									if (in_array(strtoupper($mPatron[$k]),$zMatrizTmp[$i])) {
  										$nCont_Find++;
  									}
  								}
  							break;
  							case "AND":
  								$nContador = 0;
  								for ($k=0;$k<count($mPatron);$k++) {
  									if (substr_count(strtoupper($cCadena),trim($mPatron[$k])) > 0) {
  										$nContador++;
  									}
  								}
  								if (count($mPatron) == $nContador) { $nCont_Find++; }
  							break;
  							case "OR":
  							case "NOTHING";
  								for ($k=0;$k<count($mPatron);$k++) {
  									if (substr_count(strtoupper($cCadena),trim($mPatron[$k])) > 0) {
  										$nCont_Find++;
  									}
  								}
  							break;
  						}
  						if ($nCont_Find > 0) {
  							$mMatrizTra[$j] = $zMatrizTmp[$i]; $j++;
  						}
  					}
  				} else {
  					$mMatrizTra = $zMatrizTmp;
  				}
  				/***** Fin de Buscar Patron en la Matriz *****/

  				if ($cSortField != "" && $cSortType != "") {
  					$mMatrizTra = f_Sort_Array_By_Field($mMatrizTra,$cSortField,$cSortType);
  				}
        }
			?>
      <center>
       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Registros Seleccionados (<?php echo count($mMatrizTra)?>)</legend>
     	         	<center>
       	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
           	        	<td class="clase08" width="14%">
            	        	<input type="text" class="letra" name = "cSearch" maxlength="50" value = "<?php echo $_POST['cSearch'] ?>" style= "width:80"
            	        		onblur="javascript:this.value=this.value.toUpperCase();
																						 document.forms['frgrm']['nLimInf'].value='00';
								      											 document.forms['frgrm']['nPaginas'].value='1'">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:pointer" title="Buscar"
								      		onClick = "javascript:document.forms['frgrm']['cBuscar'].value = 'ON'
								      											    document.forms['frgrm']['cSearch'].value=document.forms['frgrm']['cSearch'].value.toUpperCase();
								      												  document.forms['frgrm'].submit()">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:pointer" title="Mostrar Todo"
								      		onClick ="javascript:document.forms['frgrm']['cSearch'].value='';
								      												 document.forms['frgrm']['nLimInf'].value='00';
								      												 document.forms['frgrm']['nLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												 document.forms['frgrm']['nPaginas'].value='1';
								      												 document.forms['frgrm']['cSortField'].value='';
								      												 document.forms['frgrm']['cSortType'].value='';
								      												 document.forms['frgrm']['cBuscar'].value='';
								      												 document.forms['frgrm'].submit()">
   	              	  </td>
       	       				<td class="name" width="06%" align="left">Filas&nbsp;
       	       					<input type="text" class="letra" name = "nLimSup" value = "<?php echo $nLimSup ?>" style="width:30;text-align:right"
       	       						onblur = "javascript:f_FixFloat(this);
								      												 document.forms['frgrm']['nLimInf'].value='00';">
       	       				</td>
       	       				<td class="name" width="08%">
       	       					<?php if (ceil(count($mMatrizTra)/$nLimSup) > 1) { ?>
       	       						<?php if ($nPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value++;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='<?php echo ceil(count($mMatrizTra)/$nLimSup) ?>';
								      				    						 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
       	       						<?php } ?>
       	       						<?php if ($nPaginas > "1" && $nPaginas < ceil(count($mMatrizTra)/$nLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='1';
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value--;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value++;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='<?php echo ceil(count($mMatrizTra)/$nLimSup) ?>';
								      				    						 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
	       	       					<?php } ?>
       	       						<?php if ($nPaginas == ceil(count($mMatrizTra)/$nLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='1';
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value--;
								      												 			document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
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
												<select Class = "letrase" name = "nPaginas" value = "<?php echo $nPaginas ?>" style = "width:60%"
       	       						onchange="javascript:this.id = 'ON';
								      												 document.forms['frgrm']['nLimInf'].value=('<?php echo $nLimSup ?>'*(this.value-1));
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
       	       				<td class="name" align="left">Total Cartera&nbsp;&nbsp;
       	       					<input type="text" class="letra" name = "nTotCar" value = "<?php echo ($nTotCar != 0) ? number_format($nTotCar,2,",",".") : $nTotCar ?>" style="width:150;text-align:right;font-weight: bold;" readonly>
       	       				</td>
       	       				<td class="name" align="left">Total Provisionales&nbsp;&nbsp;
       	       					<input type="text" class="letra" name = "nTotPro" value = "<?php echo ($nTotPro != 0) ? number_format($nTotPro,2,",",".") : $nTotPro ?>" style="width:150;text-align:right;font-weight: bold;" readonly>
       	       				</td>
       	       				<td class="name" align="left">
       	       					<?php if ($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") { ?>
       	       						<label><input type="checkbox" name = "cColPed" value = "NO" onclick="javascript:if(this.checked == true) { this.value = 'SI'; } else { this.value = 'NO'; }">Incluir Pedido</label>
       	       					<?php } else { ?>
       	       						<input type="hidden" class="letra" name = "cColPed" value = "NO" readonly>
       	       					<?php } ?>
       	       				</td>
   	         	        <td Class="name" align="right">&nbsp;
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

													while ($mBotAcc = mysql_fetch_array($xBotAcc)) {
														switch ($mBotAcc['menopcxx']) {
															case "IMPRIMIR": ?>
																<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_print.png" onClick = "javascript:f_Imprimir('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="Imprmir, Solo Uno">
															<?php break;
														}
												  }
												  /***** Fin Botones de Acceso Rapido *****/
  	         	        	?>
             	        </td>
	       	         	</tr>
 	     	         	</table>
 	   	         	</center>
   	         		<hr></hr>
                <?php if ($vSysStr['system_reporte_estado_cartera_background'] == "SI") { ?>
                  <table border="0" cellpadding="0" cellspacing="0" width="95%">
                    <tr>
                      <td width = "91">
                         <input type="button" id="bntProcesar" value="Procesar" style="text-aling:center;border:0;width:91;height:21;background-image:url('<?php echo $cPlesk_Skin_Directory ?>/btn_cambiaestado_bg.gif')" Class="name" onClick = "javascript:fnAgendarReporte()">
                      </td>
                      <td><?php echo trim($cTextProBg); ?> </td>
                    </tr>
                  </table>
                  <hr></hr>
                <?php } ?>
     	       		<center>
       	     			<table cellspacing="0" width="100%">
         	         	<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
         	         		<td class="name" width="10%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','teridxxx','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','teridxxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','teridxxx','')">Nit</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "teridxxx">
           	         		<script language="javascript">f_ButtonsAscDes('','teridxxx','')</script>
           	         	</td>
           	         	<td class="name" width="3%">&nbsp;</td>
           	         	<td class="name" width="25%">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','clinomxx','ASC_AZ')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','clinomxx','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','clinomxx','')">Cliente</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "clinomxx">
           	         		<script language="javascript">f_ButtonsAscDes('','clinomxx','')</script>
           	         	</td>
           	         	<td class="name" width="12%" align="right">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','saldoven','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','saldoven','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','saldoven','')">Vencida</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "saldoven">
           	         		<script language="javascript">f_ButtonsAscDes('','saldoven','')</script>
           	         	</td>
           	         	<td class="name" width="12%" align="right">
                        <a href = "javascript:f_ButtonsAscDes('onclick','saldonvx','ASC_NUM')" title="Ordenar"
                        onmouseover="javascript:f_ButtonsAscDes('onmouseover','saldonvx','')"
                        onmouseout="javascript:f_ButtonsAscDes('onmouseout','saldonvx','')">Sin Vencer</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "saldonvx">
                        <script language="javascript">f_ButtonsAscDes('','saldonvx','')</script>
                      </td>
           	         	<td class="name" width="12%" align="right">
                        <a href = "javascript:f_ButtonsAscDes('onclick','saldoafx','ASC_NUM')" title="Ordenar"
                        onmouseover="javascript:f_ButtonsAscDes('onmouseover','saldoafx','')"
                        onmouseout="javascript:f_ButtonsAscDes('onmouseout','saldoafx','')">A Favor</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "saldoafx">
                        <script language="javascript">f_ButtonsAscDes('','saldoafx','')</script>
                      </td>
               	      <td class="name" width="12%" align="right">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','saldotot','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','saldotot','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','saldotot','')">Total</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "saldotot">
           	         		<script language="javascript">f_ButtonsAscDes('','saldotot','')</script>
               	      </td>
               	      <td class="name" width="12%" align="right">
           	         		<a href = "javascript:f_ButtonsAscDes('onclick','saldopro','ASC_NUM')" title="Ordenar"
       	       					onmouseover="javascript:f_ButtonsAscDes('onmouseover','saldopro','')"
       	       					onmouseout="javascript:f_ButtonsAscDes('onmouseout','saldopro','')">Provisionales x Descontar</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "saldopro">
           	         		<script language="javascript">f_ButtonsAscDes('','saldopro','')</script>
               	      </td>
                 	    <td Class='name' width="02%" align="right">
                 	    	<input type="checkbox" name="oChkComAll" onClick = 'javascript:f_Marca()'>
                 	    </td>
                 		</tr>
								      <script languaje="javascript">
												document.forms['frgrm']['nRecords'].value = "<?php echo count($mMatrizTra) ?>";
											</script>
 	                    <?php $y=0;for ($i=intval($nLimInf);$i<intval($nLimInf+$nLimSup);$i++) {
 	                    	if ($i < count($mMatrizTra)) { // Para Controlar el Error
	 	                    	$cColor = "{$vSysStr['system_row_impar_color_ini']}";
	                   	    if($y % 2 == 0) {
	                   	    	$cColor = "{$vSysStr['system_row_par_color_ini']}";
													} ?>
													<tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
		                      	<td class="letra7">
		                      			<a href = "javascript:fnGenerarReporte('<?php echo $mMatrizTra[$i]['teridxxx'] ?>','1')"><?php echo $mMatrizTra[$i]['teridxxx'] ?></a>
		                      	</td>
	       	                	<td class="letra7">
	       	                		<a href = "javascript:fnGenerarReporte('<?php echo $mMatrizTra[$i]['teridxxx'] ?>','2')">Excel</a>
	       	                	</td>
	       	                	<td class="letra7"><?php echo $mMatrizTra[$i]['clinomxx'] ?></td>
	       	                	<td class="letra7" align="right"><?php echo ($mMatrizTra[$i]['saldoven'] != 0) ? number_format($mMatrizTra[$i]['saldoven'],2,",",".") : $mMatrizTra[$i]['saldoven'] ?></td>
	       	                	<td class="letra7" align="right"><?php echo ($mMatrizTra[$i]['saldonvx'] != 0) ? number_format($mMatrizTra[$i]['saldonvx'],2,",",".") : $mMatrizTra[$i]['saldonvx'] ?></td>
	       	                	<td class="letra7" align="right"><?php echo ($mMatrizTra[$i]['saldoafx'] != 0) ? number_format($mMatrizTra[$i]['saldoafx'],2,",",".") : $mMatrizTra[$i]['saldoafx'] ?></td>
	       	                	<td class="letra7" align="right"><?php echo ($mMatrizTra[$i]['saldotot'] != 0) ? number_format($mMatrizTra[$i]['saldotot'],2,",",".") : $mMatrizTra[$i]['saldotot'] ?></td>
	       	                	<td class="letra7" align="right"><?php echo ($mMatrizTra[$i]['saldopro'] != 0) ? number_format($mMatrizTra[$i]['saldopro'],2,",",".") : $mMatrizTra[$i]['saldopro'] ?></td>
	            	            <td Class="letra7" align="right"><input type="checkbox" name="oChkCom"  value = "<?php echo count($mMatrizTra) ?>"
	                   	    		id="<?php echo $mMatrizTra[$i]['teridxxx'] ?>"
	                   	    		onclick="javascript:document.forms['frgrm']['nRecords'].value='<?php echo count($mMatrizTra) ?>'">
	            	            </td>
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
	<br><br>
</html>

<?php
if ($nSwitch == 1 && $vSysStr['system_reporte_estado_cartera_background'] != "SI") {
  f_Mensaje(__FILE__,__LINE__,$cMsj);
}
?>

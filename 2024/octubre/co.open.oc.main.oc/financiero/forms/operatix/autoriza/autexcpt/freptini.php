<?php
  namespace openComex;
/**
	 * Tracking Autorizacion Excluir Conceptos Pagos a Terceros.
	 * --- Descripcion: Parametrica para autorizar la exclusion de Conceptos Pagos a Terceros.
	 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
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

    	function f_Ver(xSucId,xDocId,xDocSuf) {
      	var ruta = "freptnue.php?gSucId="+xSucId+'&gDocId='+xDocId+'&gDocSuf='+xDocSuf;
      	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      	document.cookie="kMenDes=Ver Autorizacion Excluir Concepto Pagos a Terceros;path="+"/";
      	document.cookie="kModo=VER;path="+"/";
      	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	document.location = ruta; // Invoco el menu.
	    }

	  	function f_Editar(xModo) {
				switch (document.forms['frgrm']['vRecords'].value) {
					case "1":
						if (document.forms['frgrm']['oCheck'].checked == true) {
							var zMatriz = document.forms['frgrm']['oCheck'].id.split('~');
							if (zMatriz[3] == "ACTIVO") {
								var ruta = "freptnue.php?gSucId="+zMatriz[0]+"&gDocId="+zMatriz[1]+"&gDocSuf="+zMatriz[2];
	      	      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
	      	      document.cookie="kMenDes=Editar Autorizacion Excluir Concepto Pagos a Terceros;path="+"/";
	      	      document.cookie="kModo="+xModo+";path="+"/";
	      	      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
	      	      document.location = ruta; // Invoco el menu.
							} else {
								alert("El Estado del DO debe ser ACTIVO, Verifique.");
							}
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
							if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
								// Solo Deja Legalizar el Primero Seleccionado
								zSw_Prv = 1;
								var zMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
								if (zMatriz[3] == "ACTIVO") {
									var ruta = "freptnue.php?gSucId="+zMatriz[0]+"&gDocId="+zMatriz[1]+"&gDocSuf="+zMatriz[2];
		      	      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
		      	      document.cookie="kMenDes=Editar Autorizacion Excluir Concepto Pagos a Terceros;path="+"/";
		      	      document.cookie="kModo="+xModo+";path="+"/";
		      	      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
	      	      	document.location = ruta; // Invoco el menu.
								} else {
									alert("El Estado del DO debe ser ACTIVO, Verifique.");
								}
							}
						}
					break;
				}
	    }


	    function f_Anular(xModo) {
				switch (document.forms['frgrm']['vRecords'].value) {
					case "1":
						if (document.forms['frgrm']['oCheck'].checked == true) {
							var zMatriz = document.forms['frgrm']['oCheck'].id.split('~');
							if (confirm("Esta Seguro de Anular la Autorizacion Excluir Conceptos Pagos a Terceros para el Do "+zMatriz[0]+"-"+zMatriz[1]+"-"+zMatriz[2]+" ")) {
							  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
							  document.cookie="kModo="+xModo+";path="+"/";
								document.forms['frestado']['cSucId'].value   = zMatriz[0];
								document.forms['frestado']['cDocId'].value   = zMatriz[1];
								document.forms['frestado']['cDocSuf'].value  = zMatriz[2];
								document.forms['frestado']['cEstado'].value  = zMatriz[3];
								document.forms['frestado'].submit();
							}
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
							if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
								// Solo Deja Legalizar el Primero Seleccionado
								zSw_Prv = 1;
								var zMatriz = document.forms['frgrm']['oCheck'][i].id.split('~');
								if (confirm("Esta Seguro de Anular la Autorizacion Excluir Conceptos Pagos a Terceros para el Do "+zMatriz[0]+"-"+zMatriz[1]+"-"+zMatriz[2]+" ")) {
								  document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
								  document.cookie="kModo="+xModo+";path="+"/";
								  document.forms['frestado']['cSucId'].value   = zMatriz[0];
									document.forms['frestado']['cDocId'].value   = zMatriz[1];
									document.forms['frestado']['cDocSuf'].value  = zMatriz[2];
									document.forms['frestado']['cEstado'].value  = zMatriz[3];
									document.forms['frestado'].submit();
								}
							}
						}
					break;
				}
		 	}

     	function f_Link(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
      	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes="+xMenDes+";path="+"/";
      	document.cookie="kModo="+xOpcion+";path="+"/";
      	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	document.location = xForm; // Invoco el menu.
      }

       function f_Marca() {
      	if (document.forms['frgrm']['oCheckAll'].checked == true){
      	  if (document.forms['frgrm']['vRecords'].value == 1){
      	  	document.forms['frgrm']['oCheck'].checked=true;
      	  } else {
	      		if (document.forms['frgrm']['vRecords'].value > 1){
			      	for (i=0;i<document.forms['frgrm']['oCheck'].length;i++){
   	   	      	document.forms['frgrm']['oCheck'][i].checked = true;
			      	}
			      }
      	  }
      	} else {
	      	if (document.forms['frgrm']['vRecords'].value == 1){
      	  	document.forms['frgrm']['oCheck'].checked=false;
      	  } else {
      	  	if (document.forms['frgrm']['vRecords'].value > 1){
				      for (i=0;i<document.forms['frgrm']['oCheck'].length;i++){
				      	document.forms['frgrm']['oCheck'][i].checked = false;
				      }
      	  	}
 	  	   	}
	      }
	 		}

      /************************ FUNCION PARA GUARDAR EL ORDEN DEL ORDER BY DEL SQL ***********************/
	 		function f_Order_By(xEvento,xCampo) {
  	 		//alert(document.forms['frgrm'][xCampo].value);
  			if (document.forms['frgrm'][xCampo].value != '') {
  				var vSwitch = document.forms['frgrm'][xCampo].value.split(' ');
  				var cSwitch = vSwitch[1];
  			} else {
  				var cSwitch = '';
  			}
  			//alert(cSwitch);
  			if (xEvento == 'onclick') {
    			switch (cSwitch) {
    				case '':
    					document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id+' ASC,';
    					document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
    					if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
   					    document.forms['frgrm']['cOrderByOrder'].value += xCampo+"~";
    					}
    				break;
    				case 'ASC,':
    					document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id+' DESC,';
    					document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
    					if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
    					  document.forms['frgrm']['cOrderByOrder'].value += xCampo+"~";
    					}
    				break;
    				case 'DESC,':
    					document.forms['frgrm'][xCampo].value = '';
    					document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
    					if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) >= 0) {
    					  document.forms['frgrm']['cOrderByOrder'].value = document.forms['frgrm']['cOrderByOrder'].value.replace(xCampo,"");
    					}
    				break;
    			}
  			} else {
  			  switch (cSwitch) {
    				case '':
    				  document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
    				break;
    				case 'ASC,':
    				  document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
    				break;
    				case 'DESC,':
    				  document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
    				break;
    			}
  			}
	 		}

  	</script>
  </head>
  <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
		<form name = "frestado" action = "freptgra.php" method = "post" target="fmpro">
			<input type = "hidden" name = "cSucId" value = "">
			<input type = "hidden" name = "cDocId" value = "">
			<input type = "hidden" name = "cDocSuf" value = "">
			<input type = "hidden" name = "cEstado" value = "">
		</form>

		<form name = "frgrm" action = "freptini.php" method = "post" target="fmwork">
   		<input type = "hidden" name = "vRecords"   value = "">
   		<input type = "hidden" name = "vLimInf"    value = "<?php echo $vLimInf ?>">
   		<input type = "hidden" name = "vSortField" value = "<?php echo $vSortField ?>">
   		<input type = "hidden" name = "vSortType"  value = "<?php echo $vSortType ?>">
   		<input type = "hidden" name = "vBuscar"    value = "<?php echo $_POST['vBuscar'] ?>">
   		<input type = "hidden" name = "cOrderByOrder"  value = "<?php echo $_POST['cOrderByOrder'] ?>" style = "width:1000">

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

				if ($vLimInf == "" && $vLimSup == "") {
					$vLimInf = "00";
          $vLimSup = $vSysStr['system_rows_page_ini'];
				}elseif ($vLimInf == "") {
				  $vLimInf = "00";
				}

				if ($vPaginas == "") {
        	$vPaginas = "1";
				}

        if ($_POST['vSearch'] != "") {

          /**
           * Buscando los id que corresponden a las busquedas de los leftjoin
           */
           $qCliNom  = "SELECT ";
           $qCliNom .= "CLIIDXXX ";
           $qCliNom .= "FROM $cAlfa.SIAI0150 ";
           $qCliNom .= "WHERE IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) <> \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) LIKE \"%{$_POST['vSearch']}%\" ";
           $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
           $cCliIdSearch = "";
           while ($xRCN = mysql_fetch_array($xCliNom)) {
             $cCliIdSearch .= "\"{$xRCN['CLIIDXXX']}\",";
           }
           $cCliIdSearch = substr($cCliIdSearch,0,strlen($cCliIdSearch)-1);
        }

        $y=0;
        $mDatDce = array();
        $qDatDce  = "SELECT DISTINCT SQL_CALC_FOUND_ROWS ";
        $qDatDce .= "CONCAT($cAlfa.sys00121.sucidxxx,\"-\",$cAlfa.sys00121.docidxxx,\"-\",$cAlfa.sys00121.docsufxx) AS docidcom, ";
        $qDatDce .= "$cAlfa.sys00121.sucidxxx, "; // Sucursal
        $qDatDce .= "$cAlfa.sys00121.docidxxx, "; // Do
        $qDatDce .= "$cAlfa.sys00121.docsufxx, "; // Sufijo
        $qDatDce .= "$cAlfa.sys00121.doctipxx, "; // Tipo de Operacion
        $qDatDce .= "$cAlfa.sys00121.cliidxxx, "; // Cliente
        $qDatDce .= "$cAlfa.sys00121.regestxx ";  // Estado
        if (substr_count($_POST['cOrderByOrder'],"clinomxx") > 0) {
          $qDatDce .= ", IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS clinomxx ";
        }
        $qDatDce .= "FROM $cAlfa.sys00121 ";
        if (substr_count($_POST['cOrderByOrder'],"clinomxx") > 0) {
          $qDatDce .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
        }
				$qDatDce .= "WHERE ";
				$qDatDce .= "$cAlfa.sys00121.doctexpt <> \"\" AND ";
        if ($_POST['vSearch'] != "") {
					$qDatDce .= "(";
					$qDatDce .= "$cAlfa.sys00121.sucidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qDatDce .= "$cAlfa.sys00121.docidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qDatDce .= "$cAlfa.sys00121.docsufxx LIKE \"%{$_POST['vSearch']}%\" OR ";
					$qDatDce .= "$cAlfa.sys00121.doctipxx LIKE \"%{$_POST['vSearch']}%\" OR ";
          $qDatDce .= "$cAlfa.sys00121.cliidxxx LIKE \"%{$_POST['vSearch']}%\" OR ";

          if ($cCliIdSearch != "") {
            $qDatDce .= "$cAlfa.sys00121.cliidxxx IN ($cCliIdSearch) OR ";
          }
          $qDatDce .= "$cAlfa.sys00121.regestxx LIKE \"%{$_POST['vSearch']}%\") AND ";
				}
				$qDatDce .= "$cAlfa.sys00121.regestxx =  \"ACTIVO\" ";

        //// CODIGO NUEVO PARA ORDER BY
        $cOrderBy = "";
        $vOrderByOrder = explode("~",$_POST['cOrderByOrder']);
        for ($z=0;$z<count($vOrderByOrder);$z++) {
          if ($vOrderByOrder[$z] != "") {
            if ($_POST[$vOrderByOrder[$z]] != "") {

              if (substr_count($_POST[$vOrderByOrder[$z]], "docidcom") > 0) {
                //Ordena por sucidxxx, docidxxx, docsufxx
                $cOrdComId = str_replace("docidcom", "CONCAT(sucidxxx,\"-\",docidxxx,\"-\",docsufxx)", $_POST[$vOrderByOrder[$z]]);
                $cOrderBy .= $cOrdComId;
              } else {
                $cOrderBy .= $_POST[$vOrderByOrder[$z]];
              }

            }
          }
        }
        if (strlen($cOrderBy)>0) {
          $cOrderBy = substr($cOrderBy,0,strlen($cOrderBy)-1);
          $cOrderBy = "ORDER BY ".$cOrderBy;
        } else {
          $cOrderBy = "ORDER BY regfmodx DESC,regfmodx DESC";
        }
        //// FIN CODIGO NUEVO PARA ORDER BY

        $qDatDce .= "$cOrderBy LIMIT $vLimInf,$vLimSup ";
				$cIdCountRow = mt_rand(1000000000, 9999999999);
				$xDatDce = mysql_query($qDatDce, $xConexion01, true, $cIdCountRow);
        // f_Mensaje(__FILE__, __LINE__, $qDatDce."~".mysql_num_rows($xDatDce));
        /***** FIN SQL *****/

        $xNumRows = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD", $xConexion01, false);
				$xRNR     = mysql_fetch_array($xNumRows);
				$nRNR     = $xRNR['CANTIDAD'];

        while ($xRDC = mysql_fetch_array($xDatDce)) {

          //Buscando nombre del cliente
          if (substr_count($_POST['cOrderByOrder'],"clinomxx") == 0) {
          	$qCliNom  = "SELECT ";
            $qCliNom .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) <> \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX ";
            $qCliNom .= "FROM $cAlfa.SIAI0150 ";
            $qCliNom .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$xRDC['cliidxxx']}\" LIMIT 0,1 ";
            $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
            if (mysql_num_rows($xCliNom) > 0) {
            	$xRCN = mysql_fetch_array($xCliNom);
              $xRDC['clinomxx'] = $xRCN['CLINOMXX'];
            } else {
              $xRDC['clinomxx'] = "SIN NOMBRE";
            }
          }
					$mDatDce[count($mDatDce)] = $xRDC;
				}
			?>
      <center>
       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Registros en la Consulta (<?php echo $nRNR?>)</legend>
     	         	<center>
       	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
           	        	<td class="clase08" width="14%">
            	        	<input type="text" class="letra" name = "vSearch" maxlength="50" value = "<?php echo $vSearch ?>" style= "width:80"
            	        		onblur="javascript:this.value=this.value.toUpperCase();
																						 document.forms['frgrm']['vLimInf'].value='00';
								      											 document.forms['frgrm']['vPaginas'].value='1'">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:pointer" title="Buscar"
								      		onClick = "javascript:document.forms['frgrm']['vBuscar'].value = 'ON'
								      											    document.forms['frgrm']['vSearch'].value=document.forms['frgrm']['vSearch'].value.toUpperCase();
								      												  document.forms['frgrm'].submit()">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:pointer" title="Mostrar Todo"
								      		onClick ="javascript:document.forms['frgrm']['vSearch'].value='';
								      												 document.forms['frgrm']['vLimInf'].value='00';
								      												 document.forms['frgrm']['vLimSup'].value='<?php echo $vSysStr['system_rows_page_ini'] ?>';
								      												 document.forms['frgrm']['vPaginas'].value='1';
								      												 document.forms['frgrm']['vSortField'].value='';
								      												 document.forms['frgrm']['vSortType'].value='';
								      												 document.forms['frgrm']['vBuscar'].value='';
								      												 document.forms['frgrm'].submit()">
   	              	  </td>
       	       				<td class="name" width="06%" align="left">Filas&nbsp;
       	       					<input type="text" class="letra" name = "vLimSup" value = "<?php echo $vLimSup ?>" style="width:30;text-align:right"
       	       						onblur = "javascript:f_FixFloat(this);
								      												 document.forms['frgrm']['vLimInf'].value='00';">
       	       				</td>
       	       				<td class="name" width="08%">
       	       					<?php if (ceil($nRNR/$vLimSup) > 1) { ?>
       	       						<?php if ($vPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value++;
								      												 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil($nRNR/$vLimSup) ?>';
								      				    						 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
       	       						<?php } ?>
       	       						<?php if ($vPaginas > "1" && $vPaginas < ceil($nRNR/$vLimSup)) { ?>
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
       	       								onClick = "javascript:document.forms['frgrm']['vPaginas'].value='<?php echo ceil($nRNR/$vLimSup) ?>';
								      				    						 			document.forms['frgrm']['vLimInf'].value=('<?php echo $vLimSup ?>'*(document.forms['frgrm']['vPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
	       	       					<?php } ?>
       	       						<?php if ($vPaginas == ceil($nRNR/$vLimSup)) { ?>
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
													<?php for ($i=0;$i<ceil($nRNR/$vLimSup);$i++) {
														if ($i+1 == $vPaginas) { ?>
															<option value = "<?php echo $i+1 ?>" selected><?php echo $i+1 ?></option>
														<?php } else { ?>
															<option value = "<?php echo $i+1 ?>"><?php echo $i+1 ?></option>
														<?php } ?>
													<?php } ?>
												</select>
       	       				</td>
   	         	        <td Class="name" width="15%" align="right">&nbsp;
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
															case "EDITAR": ?>
																<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_edit.png" onClick = "javascript:f_Editar('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="Editar, Solo Uno">
															<?php break;
															case "ANULAR": ?>
																<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_drop.png" onClick = "javascript:f_Anular('<?php echo $mBotAcc['menopcxx'] ?>')" style = "cursor:pointer" title="Cambiar Estado, Solo Uno">
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
     	       		<center>
                  <table cellspacing="0" width="100%">
                    <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                      <td class="name" width="15%">
                        <a href = "javascript:f_Order_By('onclick','docidcom');" title="Ordenar">Do</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "docidcom">
                        <input type = "hidden" name = "docidcom" value = "<?php echo $_POST['docidcom'] ?>" id = "docidcom">
                        <script language="javascript">f_Order_By('','docidcom')</script>
           	         	</td>
           	         	<td class="name" width="10%">
                        <a href = "javascript:f_Order_By('onclick','doctipxx');" title="Ordenar">Tipo Operacion</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "doctipxx">
                        <input type = "hidden" name = "doctipxx" value = "<?php echo $_POST['doctipxx'] ?>" id = "doctipxx">
                        <script language="javascript">f_Order_By('','doctipxx')</script>
           	         	</td>
           	         	<td class="name" width="12%">
                        <a href = "javascript:f_Order_By('onclick','cliidxxx');" title="Ordenar">Nit</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "cliidxxx">
                        <input type = "hidden" name = "cliidxxx" value = "<?php echo $_POST['cliidxxx'] ?>" id = "cliidxxx">
                        <script language="javascript">f_Order_By('','cliidxxx')</script>
           	         	</td>
           	         	<td class="name" width="56%">
                        <a href = "javascript:f_Order_By('onclick','clinomxx');" title="Ordenar">Cliente</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "clinomxx">
                        <input type = "hidden" name = "clinomxx" value = "<?php echo $_POST['clinomxx'] ?>" id = "clinomxx">
                        <script language="javascript">f_Order_By('','clinomxx')</script>
           	         	</td>
               	      <td class="name" width="05%">
                        <a href = "javascript:f_Order_By('onclick','regestxx');" title="Ordenar">Estado</a>&nbsp;
                        <img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regestxx">
                        <input type = "hidden" name = "regestxx" value = "<?php echo $_POST['regestxx'] ?>" id = "regestxx">
                        <script language="javascript">f_Order_By('','regestxx')</script>
               	      </td>
                      <td Class='name' width="02%" align="right">
                        <input type="checkbox" name="oCheckAll" onClick = 'javascript:f_Marca()'>
                      </td>
                    </tr>
                      <script languaje="javascript">
                        document.forms['frgrm']['vRecords'].value = "<?php echo count($mDatDce) ?>";
                      </script>
                      <?php
                      $y=0;

                      for ($i=0;$i<count($mDatDce);$i++) {
                        if ($i < count($mDatDce)) { // Para Controlar el Error
                          $cColor = "{$vSysStr['system_row_impar_color_ini']}";
                          if($y % 2 == 0) {
                            $cColor = "{$vSysStr['system_row_par_color_ini']}";
                          }
                          ?>
                          <!--<tr bgcolor = "<?php echo $cColor ?>">-->
                          <tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
                            <td class="letra7"><a href = javascript:f_Ver('<?php echo $mDatDce[$i]['sucidxxx']?>','<?php echo $mDatDce[$i]['docidxxx']?>','<?php echo $mDatDce[$i]['docsufxx']?>')><?php echo $mDatDce[$i]['docidcom'] ?></a></td>
                            <td class="letra7"><?php echo $mDatDce[$i]['doctipxx'] ?></td>
                            <td class="letra7"><?php echo $mDatDce[$i]['cliidxxx'] ?></td>
                            <td class="letra7"><?php echo utf8_decode($mDatDce[$i]['clinomxx']) ?></td>
                            <td class="letra7"><?php echo $mDatDce[$i]['regestxx'] ?></td>
                            <td Class="letra7" align="right"><input type="checkbox" name="oCheck"  value = "<?php echo count($mDatDce) ?>"
                              id="<?php echo $mDatDce[$i]['sucidxxx'].'~'.$mDatDce[$i]['docidxxx'].'~'.$mDatDce[$i]['docsufxx'].'~'.$mDatDce[$i]['regestxx']?>"
                              onclick="javascript:document.forms['frgrm']['vRecords'].value='<?php echo count($mDatDce) ?>'">
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
</html>

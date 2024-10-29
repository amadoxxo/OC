<?php
  namespace openComex;
/**
 * Tracking Conceptos Contables Causaciones Automaticas
 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
 * @package opencomex
 */

	include("../../../../libs/php/utility.php");

	/* Busco en la 05 que Tiene Permiso el Usuario*/
  $qUsrMen  = "SELECT * ";
  $qUsrMen .= "FROM $cAlfa.sys00005 ";
  $qUsrMen .= "WHERE ";
  $qUsrMen .= "sys00005.modidxxx  = \"{$_COOKIE['kModId']}\" AND ";
  $qUsrMen .= "sys00005.proidxxx  = \"{$_COOKIE['kProId']}\" AND ";
  $qUsrMen .= "sys00005.menimgon != '' ";
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

    	function f_Ver(xCtoId,xPucId) {
      	var ruta = "frctonue.php?cCtoId="+xCtoId+"&cPucId="+xPucId;
      	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      	document.cookie="kMenDes=Ver Concepto Contable;path="+"/";
      	document.cookie="kModo=VER;path="+"/";
      	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	document.location = ruta; // Invoco el menu.
	    }

	  	function f_Editar(xModo) {
				switch (document.forms['frgrm']['cRecords'].value) {
					case "1":
						if (document.forms['frgrm']['oCheck'].checked == true) {
							var zMatriz = document.forms['frgrm']['oCheck'].id.split('-');
							var ruta = "frctonue.php?cCtoId="+zMatriz[0]+"&cPucId="+zMatriz[1];
      	      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
      	      document.cookie="kMenDes=Editar Concepto Contable;path="+"/";
      	      document.cookie="kModo="+xModo+";path="+"/";
      	      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	      document.location = ruta; // Invoco el menu.
						}
					break;
					default:
						var zSw_Prv = 0;
						for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
							if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
								// Solo Deja Legalizar el Primero Seleccionado
								zSw_Prv = 1;
								var zMatriz = document.forms['frgrm']['oCheck'][i].id.split('-');
								var ruta = "frctonue.php?cCtoId="+zMatriz[0]+"&cPucId="+zMatriz[1];
        	      document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        	      document.cookie="kMenDes=Editar Concepto Contable;path="+"/";
        	      document.cookie="kModo="+xModo+";path="+"/";
        	      parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
        	      document.location = ruta; // Invoco el menu.
							}
						}
					break;
				}
	    }

	    function f_Anular(xModo) {
				 if (document.forms['frgrm']['cRecords'].value!="0"){
  				switch (document.forms['frgrm']['cRecords'].value) {
  					case "1":
  						if (document.forms['frgrm']['oCheck'].checked == true) {
   						  var zMatriz = document.forms['frgrm']['oCheck'].id.split('-');
   						  if (confirm("Esta Seguro de Cambiar el Estado del Concepto Contable No. "+zMatriz[1]+" ?")) {
									document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
    							document.forms['frestado']['cCtoId'].value=zMatriz[0];
    							document.forms['frestado']['cPucId'].value=zMatriz[1];
    							document.forms['frestado']['cEstado'].value=zMatriz[2];
          	      document.cookie="kModo="+xModo+";path="+"/";
    				      document.forms['frestado'].submit();
  						  }
  						}
  					break;
  					default:
  						var zSw_Prv = 0;
  						for (i=0;i<document.forms['frgrm']['oCheck'].length;i++) {
  							if (document.forms['frgrm']['oCheck'][i].checked == true && zSw_Prv == 0) {
   							  var zMatriz = document.forms['frgrm']['oCheck'][i].id.split('-');
   							  if (confirm("Esta Seguro de Cambiar el Estado del Concepto Contable No. "+zMatriz[1]+" ?")) {
     								zSw_Prv = 1;
     								var zMatriz = document.forms['frgrm']['oCheck'][i].id.split('-');
  									document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
    							  document.forms['frestado']['cCtoId'].value=zMatriz[0];
    							  document.forms['frestado']['cPucId'].value=zMatriz[1];
    							  document.forms['frestado']['cEstado'].value=zMatriz[2];
            	      document.cookie="kModo="+xModo+";path="+"/";
    					      document.forms['frestado'].submit();
  							  }
  							}
  						}
  					break;
  				}
	      }
	    }

     	function f_Ctok(xModId,xProId,xMenId,xForm,xOpcion,xMenDes){
      	document.cookie="kIniAnt=<?php echo substr($_SERVER['PHP_SELF'],(strrpos($_SERVER['PHP_SELF'],"/")+1),strlen($_SERVER['PHP_SELF'])) ?>;path="+"/";
        document.cookie="kMenDes="+xMenDes+";path="+"/";
      	document.cookie="kModo="+xOpcion+";path="+"/";
      	parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory ?>/frnivel4.php";
      	document.location = xForm; // Invoco el menu.
      }

       function f_Marca() {
      	if (document.forms['frgrm']['oCheckAll'].checked == true){
      	  if (document.forms['frgrm']['cRecords'].value == 1){
      	  	document.forms['frgrm']['oCheck'].checked=true;
      	  } else {
	      		if (document.forms['frgrm']['cRecords'].value > 1){
			      	for (i=0;i<document.forms['frgrm']['oCheck'].length;i++){
   	   	      	document.forms['frgrm']['oCheck'][i].checked = true;
			      	}
			      }
      	  }
      	} else {
	      	if (document.forms['frgrm']['cRecords'].value == 1){
      	  	document.forms['frgrm']['oCheck'].checked=false;
      	  } else {
      	  	if (document.forms['frgrm']['cRecords'].value > 1){
				      for (i=0;i<document.forms['frgrm']['oCheck'].length;i++){
				      	document.forms['frgrm']['oCheck'][i].checked = false;
				      }
      	  	}
 	  	   	}
	      }
	 		}

	    /************************ FUNCION PARA GUARDAR EL ORDEN DEL ORDER BY DEL SQL ***********************/
      function f_Order_By(xEvento, xCampo) {
        // alert(document.forms['frgrm'][xCampo].value);
        // alert(xCampo + " inicio ");
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
              document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id + ' ASC,';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_asc.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
                document.forms['frgrm']['cOrderByOrder'].value += xCampo + "~";
              }
            break;
            case 'ASC,':
              document.forms['frgrm'][xCampo].value = document.forms['frgrm'][xCampo].id + ' DESC,';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/s_desc.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) < 0) {
                document.forms['frgrm']['cOrderByOrder'].value += xCampo + "~";
              }
            break;
            case 'DESC,':
              document.forms['frgrm'][xCampo].value = '';
              document.getElementById(xCampo).src = '<?php echo $cPlesk_Skin_Directory ?>/spacer.png';
              if (document.forms['frgrm']['cOrderByOrder'].value.lastIndexOf(xCampo) >= 0) {
                document.forms['frgrm']['cOrderByOrder'].value = document.forms['frgrm']['cOrderByOrder'].value.replace(xCampo, "");
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
		<form name = "frestado" action = "frcatgra.php" method = "post" target="fmpro">
			<input type = "hidden" name = "cCtoId" value = "">
			<input type = "hidden" name = "cPucId" value = "">
			<input type = "hidden" name = "cEstado" value = "">
		</form>

		<form name = "frgrm" action="frctoini.php" method="post" target="fmwork">
   		<input type = "hidden" name = "cRecords"   value = "">
   		<input type = "hidden" name = "cLimInf"    value = "<?php echo $cLimInf ?>">
   		<input type = "hidden" name = "cSortField" value = "<?php echo $cSortField ?>">
   		<input type = "hidden" name = "cSortType"  value = "<?php echo $cSortType ?>">
   		<input type = "hidden" name = "cBuscar"    value = "<?php echo $_POST['cBuscar'] ?>">
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
													  <td Class="clase08" width="20%"><center><img src = "<?php echo $cPlesk_Skin_Directory ?>/<?php echo $mUsrMen['menimgon'] ?>" style = "cursor:pointer" onClick ="javascript:f_Ctok('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')"><br>
				                    <a href = "javascript:f_Ctok('<?php echo $mUsrMen['modidxxx'] ?>','<?php echo $mUsrMen['proidxxx'] ?>','<?php echo $mUsrMen['menidxxx'] ?>','<?php echo $mUsrMen['menformx']?>','<?php echo $mUsrMen['menopcxx']?>','<?php echo $mUsrMen['mendesxx']?>')"
															style="color:<?php echo $vSysStr['system_ctok_menu_color'] ?>"><?php echo $mUsrMen['mendesxx'] ?></a></center></td>
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

				if (empty($cLimInf) && empty($cLimSup)) {
					$cLimInf = "00";
          $cLimSup = "30";
				}

				if ($nPaginas == "") {
        	$nPaginas = "1";
				}

				if ($_POST['cSearch'] != "") {
					/**
				   * Buscando los id que corresponden a las busquedas de los lefjoin
				   */
					$qCodUsuaio  = "SELECT ";
					$qCodUsuaio .= "USRIDXXX, USRNOMXX ";
					$qCodUsuaio .= "FROM $cAlfa.SIAI0003 ";
					$qCodUsuaio .= "WHERE ";
          $qCodUsuaio .= "USRNOMXX LIKE \"%{$_POST['cSearch']}%\" ";
				  $xCodUsuaio = f_MySql("SELECT","",$qCodUsuaio,$xConexion01,"");
				  $cNombreSearch = "";
				  while ($xRCN = mysql_fetch_array($xCodUsuaio)) {
				 	 $cNombreSearch .= "\"{$xRCN['USRIDXXX']}\",";
				  }
				  $cNombreSearch = substr($cNombreSearch,0,strlen($cNombreSearch)-1);
        }

				$y=0;
				$mCtoCau = array();
				$qCatInv  = "SELECT DISTINCT SQL_CALC_FOUND_ROWS ";
        $qCatInv .= "$cAlfa.fpar0121.pucidxxx, ";
				$qCatInv .= "$cAlfa.fpar0121.ctoidxxx, ";
        $qCatInv .= "$cAlfa.fpar0121.ctodesxx, ";
        $qCatInv .= "$cAlfa.fpar0121.regfcrex, ";
				$qCatInv .= "$cAlfa.fpar0121.reghcrex, ";
        $qCatInv .= "$cAlfa.fpar0121.regfmodx, ";
				$qCatInv .= "$cAlfa.fpar0121.reghmodx, ";
				$qCatInv .= "$cAlfa.fpar0121.regestxx, ";
				if (substr_count($_POST['cOrderByOrder'],"usrnomxx") > 0) {
					$qCatInv .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,TRIM(CONCAT($cAlfa.SIAI0003.USRPAPEX,\" \",$cAlfa.SIAI0003.USRSAPEX,\" \",$cAlfa.SIAI0003.USRPNOMX,\" \",$cAlfa.SIAI0003.USRSNOMX))) AS usrnomxx, ";
				}
        $qCatInv .= "$cAlfa.fpar0121.regusrxx ";
				$qCatInv .= "FROM $cAlfa.fpar0121 ";
				if (substr_count($_POST['cOrderByOrder'],"usrnomxx") > 0) {
          $qCatInv .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.fpar0121.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
				}

				if ($_POST['cSearch'] != "") {
					$qCatInv .= "WHERE ";
					$qCatInv .= "$cAlfa.fpar0121.pucidxxx LIKE \"%{$_POST['cSearch']}%\" OR ";
					$qCatInv .= "$cAlfa.fpar0121.ctoidxxx LIKE \"%{$_POST['cSearch']}%\" OR ";
					$qCatInv .= "$cAlfa.fpar0121.ctodesxx LIKE \"%{$_POST['cSearch']}%\" OR ";
					$qCatInv .= "$cAlfa.fpar0121.regfcrex LIKE \"%{$_POST['cSearch']}%\" OR ";
					$qCatInv .= "$cAlfa.fpar0121.reghcrex LIKE \"%{$_POST['cSearch']}%\" OR ";
					$qCatInv .= "$cAlfa.fpar0121.regfmodx LIKE \"%{$_POST['cSearch']}%\" OR ";
					$qCatInv .= "$cAlfa.fpar0121.reghmodx LIKE \"%{$_POST['cSearch']}%\" OR ";
					if ($cNombreSearch != "") {
						$qCatInv .= "$cAlfa.fpar0121.regusrxx IN ($cNombreSearch) OR ";
					}
					$qCatInv .= "$cAlfa.fpar0121.regestxx LIKE \"%{$_POST['cSearch']}%\" ";
				}

				//// CODIGO NUEVO PARA ORDER BY
				$cOrderBy = "";
				$vOrderByOrder = explode("~",$_POST['cOrderByOrder']);
				for ($z=0;$z<count($vOrderByOrder);$z++) {
					if ($vOrderByOrder[$z] != "") {
						if ($_POST[$vOrderByOrder[$z]] != "") {
							$cOrderBy .= $_POST[$vOrderByOrder[$z]];
						}
					}
				}
				if (strlen($cOrderBy)>0) {
					$cOrderBy = substr($cOrderBy,0,strlen($cOrderBy)-1);
					$cOrderBy = "ORDER BY ".$cOrderBy;
				} else {
					$cOrderBy = "ORDER BY ctoidxxx, pucidxxx ";
				}
				//// FIN CODIGO NUEVO PARA ORDER BY

				$qCatInv .= "$cOrderBy LIMIT $cLimInf,$cLimSup ";
				$cIdCountRow = mt_rand(1000000000, 9999999999);
				$xCatInv = mysql_query($qCatInv, $xConexion01, true, $cIdCountRow);

				$xNumRows = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD", $xConexion01, false);
				$xRNR     = mysql_fetch_array($xNumRows);
				$nRNR     = $xRNR['CANTIDAD'];

				/* Cargo la Matriz con los ROWS del Cursor */
				while ($xRCI = mysql_fetch_array($xCatInv)) {
					/* Traigo el Nombre del Usuario */
					$qNomUser  = "SELECT ";
          $qNomUser .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,TRIM(CONCAT($cAlfa.SIAI0003.USRPAPEX,\" \",$cAlfa.SIAI0003.USRSAPEX,\" \",$cAlfa.SIAI0003.USRPNOMX,\" \",$cAlfa.SIAI0003.USRSNOMX))) AS USRNOMXX ";
          $qNomUser .= "FROM $cAlfa.SIAI0003 ";
          $qNomUser .= "WHERE ";
          $qNomUser .= "USRIDXXX = \"{$xRCI['regusrxx']}\" LIMIT 0,1 ";
          $xNomUser  = f_MySql("SELECT","",$qNomUser,$xConexion01,"");
          if (mysql_num_rows($xNomUser) > 0) {
						$vCodUsuaio = mysql_fetch_array($xNomUser);
            $xRCI['usrnomxx'] = $vCodUsuaio['USRNOMXX'];
          } else {
            $xRCI['usrnomxx'] = "SIN NOMBRE";
          }
					/* Fin Traigo el Nombre del Usuario */

					$mCtoCau[count($mCtoCau)] = $xRCI;
				}
				/* Fin de Cargo la Matriz con los ROWS del Cursor */
			?>
      <center>
       	<table width="95%" cellspacing="0" cellpadding="0" border="0">
         	<tr>
	       	  <td>
				      <fieldset>
   			        <legend>Registros Seleccionados(<?php echo $nRNR ?>)</legend>
     	         	<center>
       	       		<table border="0" cellspacing="0" cellpadding="0" width="100%">
         	      		<tr>
           	        	<td class="clase08" width="14%">
            	        	<input type="text" class="letra" name = "cSearch" maxlength="50" value = "<?php echo $cSearch ?>" style= "width:80"
            	        		onblur="javascript:this.value=this.value.toUpperCase();
																						 document.forms['frgrm']['cLimInf'].value='00';
																						 document.forms['frgrm']['cLimSup'].value='30';
								      											 document.forms['frgrm']['nPaginas'].value='1'">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_search.png" style = "cursor:pointer" title="Buscar"
								      		onClick = "javascript:document.forms['frgrm']['cBuscar'].value = 'ON'
								      											    document.forms['frgrm']['cSearch'].value=document.forms['frgrm']['cSearch'].value.toUpperCase();
																								document.forms['frgrm']['cLimInf'].value='00';
								      												  document.forms['frgrm']['cLimSup'].value='30';
								      												  document.forms['frgrm']['nPaginas'].value='1'
								      												  document.forms['frgrm'].submit()">
              	      	<img src = "<?php echo $cPlesk_Skin_Directory ?>/btn_show-all_bg.gif" style = "cursor:pointer" title="Mostrar Todo"
								      		onClick ="javascript:document.forms['frgrm']['cSearch'].value='';
								      												 document.forms['frgrm']['cLimInf'].value='00';
								      												 document.forms['frgrm']['cLimSup'].value='30';
								      												 document.forms['frgrm']['nPaginas'].value='1';
								      												 document.forms['frgrm']['cSortField'].value='';
								      												 document.forms['frgrm']['cSortType'].value='';
								      												 document.forms['frgrm']['cBuscar'].value='';
								      												 document.forms['frgrm'].submit()">
   	              	  </td>
       	       				<td class="name" width="06%" align="left">Filas&nbsp;
       	       					<input type="text" class="letra" name = "cLimSup" value = "<?php echo $cLimSup ?>" style="width:30;text-align:right"
       	       						onblur = "javascript:f_FixFloat(this);
								      												 document.forms['frgrm']['cLimInf'].value='00';">
       	       				</td>
       	       				<td class="name" width="08%">
       	       					<?php if (ceil($nRNR/$cLimSup) > 1) { ?>
       	       						<?php if ($nPaginas == "1") { ?>
														<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_firstpage.png" style = "cursor:pointer" title="Primera Pagina">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/bd_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  	style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value++;
								      												 			document.forms['frgrm']['cLimInf'].value=('<?php echo $cLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  	style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='<?php echo ceil($nRNR/$cLimSup) ?>';
								      				    						 			document.forms['frgrm']['cLimInf'].value=('<?php echo $cLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
       	       						<?php } ?>
       	       						<?php if ($nPaginas > "1" && $nPaginas < ceil($nRNR/$cLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='1';
								      												 			document.forms['frgrm']['cLimInf'].value=('<?php echo $cLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value--;
								      												 			document.forms['frgrm']['cLimInf'].value=('<?php echo $cLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_nextpage.png"  style = "cursor:pointer" title="Pagina Siguiente"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value++;
								      												 			document.forms['frgrm']['cLimInf'].value=('<?php echo $cLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_lastpage.png"  style = "cursor:pointer" title="Ultima Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='<?php echo ceil($nRNR/$cLimSup) ?>';
								      				    						 			document.forms['frgrm']['cLimInf'].value=('<?php echo $cLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
	       	       					<?php } ?>
       	       						<?php if ($nPaginas == ceil($nRNR/$cLimSup)) { ?>
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_firstpage.png" style = "cursor:pointer" title="Primera Pagina"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value='1';
								      												 			document.forms['frgrm']['cLimInf'].value=('<?php echo $cLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
								      												 			document.forms['frgrm'].submit()">
		       	       					<img src = "<?php echo $cPlesk_Skin_Directory ?>/b_prevpage.png"  style = "cursor:pointer" title="Pagina Anterior"
       	       								onClick = "javascript:document.forms['frgrm']['nPaginas'].value--;
								      												 			document.forms['frgrm']['cLimInf'].value=('<?php echo $cLimSup ?>'*(document.forms['frgrm']['nPaginas'].value-1));
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
								      												 document.forms['frgrm']['cLimInf'].value=('<?php echo $cLimSup ?>'*(this.value-1));
       	       						                     document.forms['frgrm'].submit()">
													<?php for ($i=0;$i<ceil($nRNR/$cLimSup);$i++) {
														if ($i+1 == $nPaginas) { ?>
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
         	         		<td class="name" width="10%">
           	         		<a href = "javascript:f_Order_By('onclick','ctoidxxx');" title="Ordenar">Concepto</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ctoidxxx">
												<input type = "hidden" name = "ctoidxxx" value = "<?php echo $_POST['ctoidxxx'] ?>" id = "ctoidxxx">
           	         		<script language="javascript">f_Order_By('','ctoidxxx')</script>
           	         	</td>
           	         	<td class="name" width="10%">
           	         		<a href = "javascript:f_Order_By('onclick','pucidxxx');" title="Ordenar">Cuenta</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "pucidxxx">
												<input type = "hidden" name = "pucidxxx" value = "<?php echo $_POST['pucidxxx'] ?>" id = "pucidxxx">
           	         		<script language="javascript">f_Order_By('','pucidxxx')</script>
           	         	</td>
         	         		<td class="name" width="32%">
           	         		<a href = "javascript:f_Order_By('onclick','ctodesxx');" title="Ordenar">Descripcion Concepto</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "ctodesxx">
												<input type = "hidden" name = "ctodesxx" value = "<?php echo $_POST['ctodesxx'] ?>" id = "ctodesxx">
           	         		<script language="javascript">f_Order_By('','ctodesxx')</script>
           	         	</td>
           	         	<td class="name" width="17%">
           	         		<a href = "javascript:f_Order_By('onclick','usrnomxx');" title="Ordenar">Usuario</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "usrnomxx">
												<input type = "hidden" name = "usrnomxx" value = "<?php echo $_POST['usrnomxx'] ?>" id = "usrnomxx">
           	         		<script language="javascript">f_Order_By('','usrnomxx')</script>
           	         	</td>
             	        <td class="name" width="07%">
           	         		<a href = "javascript:f_Order_By('onclick','regfcrex');" title="Ordenar">Creado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfcrex">
												<input type = "hidden" name = "regfcrex" value = "<?php echo $_POST['regfcrex'] ?>" id = "regfcrex">
           	         		<script language="javascript">f_Order_By('','regfcrex')</script>
             	        </td>
             	        <td class="name" width="05%">
           	         		<a href = "javascript:f_Order_By('onclick','reghcrex');" title="Ordenar">Hora</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghcrex">
												<input type = "hidden" name = "reghcrex" value = "<?php echo $_POST['reghcrex'] ?>" id = "reghcrex">
           	         		<script language="javascript">f_Order_By('','reghcrex')</script>
             	        </td>
             	        <td class="name" width="05%">
           	         		<a href = "javascript:f_Order_By('onclick','regfmodx');" title="Ordenar">Modificado</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "regfmodx">
												<input type = "hidden" name = "regfmodx" value = "<?php echo $_POST['regfmodx'] ?>" id = "regfmodx">
           	         		<script language="javascript">f_Order_By('','regfmodx')</script>
             	        </td>
             	        <td class="name" width="05%">
           	         		<a href = "javascript:f_Order_By('onclick','reghmodx');" title="Ordenar">Hora</a>&nbsp;
           	         		<img src="<?php echo $cPlesk_Skin_Directory ?>/spacer.png" border="0" width="11" height="9" title = "" id = "reghmodx">
												<input type = "hidden" name = "reghmodx" value = "<?php echo $_POST['reghmodx'] ?>" id = "reghmodx">
           	         		<script language="javascript">f_Order_By('','reghmodx')</script>
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
												document.forms['frgrm']['cRecords'].value = "<?php echo count($mCtoCau) ?>";
											</script>
 	                    <?php for ($i=0;$i<count($mCtoCau);$i++) {
 	                    	if ($i < count($mCtoCau)) { // Para Controlar el Error
	 	                    	$cColor = "{$vSysStr['system_row_impar_color_ini']}";
	                   	    if($y % 2 == 0) {
	                   	    	$cColor = "{$vSysStr['system_row_par_color_ini']}";
													} ?>
													<!--<tr bgcolor = "<?php echo $cColor ?>">-->
													<tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
													  <td class="letra7"><a href = javascript:f_Ver('<?php echo $mCtoCau[$i]['ctoidxxx']?>','<?php echo $mCtoCau[$i]['pucidxxx']?>')><?php echo $mCtoCau[$i]['ctoidxxx'] ?></a></td>
														<td class="letra7"><?php echo $mCtoCau[$i]['pucidxxx'] ?></a></td>
														<td class="letra7"><?php echo trim(substr($mCtoCau[$i]['ctodesxx'],0,60)) ?></a></td>
	       	                	<td class="letra7"><?php echo substr($mCtoCau[$i]['usrnomxx'],0,20) ?></td>
	        	              	<td class="letra7"><?php echo $mCtoCau[$i]['regfcrex'] ?></td>
	          	              <td class="letra7"><?php echo $mCtoCau[$i]['reghcrex'] ?></td>
	          	              <td class="letra7"><?php echo $mCtoCau[$i]['regfmodx'] ?></td>
	          	              <td class="letra7"><?php echo $mCtoCau[$i]['reghmodx'] ?></td>
	          	              <td class="letra7"><?php echo $mCtoCau[$i]['regestxx'] ?></td>
	            	            <td Class="letra7" align="right"><input type="checkbox" name="oCheck"  value = "<?php echo count($mCtoCau) ?>"
	                   	    		id="<?php echo $mCtoCau[$i]['ctoidxxx'].'-'.$mCtoCau[$i]['pucidxxx'].'-'.$mCtoCau[$i]['regestxx']?>"
	                   	    		onclick="javascript:document.forms['frgrm']['cRecords'].value='<?php echo count($mCtoCau) ?>'">
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
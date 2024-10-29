<?php
  namespace openComex;
/**
 * Graba Conceptos Contables Causaciones Automaticas
 * @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
 * @package opencomex
 */
	include("../../../../libs/php/utility.php");

	$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
	$cMsj = "";

	switch ($_COOKIE['kModo']) {
  	case "NUEVO":
  	case "EDITAR":

  		if($_POST['cPucId']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Seleccionar el No de Cuenta.\n";
			}

  		$qSqlCta  = "SELECT  * ";
			$qSqlCta .= "FROM $cAlfa.fpar0115 ";
			$qSqlCta .= "WHERE CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) = \"{$_POST['cPucId']}\" AND ";
			$qSqlCta .= "regestxx = \"ACTIVO\" ";
			$xSqlCta  = f_MySql("SELECT","",$qSqlCta,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qSqlCta."~".mysql_num_rows($xSqlCta));
			if (mysql_num_rows($xSqlCta) == 0) {
				$nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "La Cuenta Seleccionada No Existe.\n";
			}

			switch ($_COOKIE['kModo']) {
  			case "NUEVO":

					$cApar=substr($_POST['cPucId'],0,6);
					$qSqlCsc  = "SELECT ctocscxx ";
		      $qSqlCsc .= "FROM $cAlfa.fpar0119 ";
		      $qSqlCsc .= "WHERE SUBSTRING(pucidxxx,1,6) = \"{$cApar}\" AND  ";
		      $qSqlCsc .= "regestxx = \"ACTIVO\" ORDER BY ctoidxxx DESC ";
		      $xSqlCsc  = f_MySql("SELECT","",$qSqlCsc,$xConexion01,"");
		      //f_Mensaje(__FILE__,__LINE__,$qSqlCsc."~".mysql_num_rows($xSqlCsc));
		      $cCsc119 = 0;
		      if(mysql_num_rows($xSqlCsc) > 0){
		        $xRCsc = mysql_fetch_array($xSqlCsc);
		        $cCsc119=intval($xRCsc['ctocscxx']);
		      }

		      $qSqlCsc  = "SELECT ctocscxx ";
		      $qSqlCsc .= "FROM $cAlfa.fpar0121 ";
		      $qSqlCsc .= "WHERE SUBSTRING(pucidxxx,1,6) = \"{$cApar}\" AND  ";
		      $qSqlCsc .= "regestxx = \"ACTIVO\" ORDER BY ctoidxxx DESC ";
		      $xSqlCsc  = f_MySql("SELECT","",$qSqlCsc,$xConexion01,"");
		      //f_Mensaje(__FILE__,__LINE__,$qSqlCsc."~".mysql_num_rows($xSqlCsc));
		      $cCsc121= 0;
		      if(mysql_num_rows($xSqlCsc) > 0){
		        $xRCsc = mysql_fetch_array($xSqlCsc);
		        $cCsc121=intval($xRCsc['ctocscxx']);
		      }

		      //f_Mensaje(__FILE__,__LINE__,$cCsc119." ~ ".$cCsc121);
		      if($cCsc119 > $cCsc121){
		        $_POST['cCtoId'] = str_pad((intval($cCsc119)+1),4,"0",STR_PAD_LEFT);;
		      } else {
		        $_POST['cCtoId'] = str_pad((intval($cCsc121)+1),4,"0",STR_PAD_LEFT);;
		      }

					$qSqlCon  = "SELECT  * ";
					$qSqlCon .= "FROM $cAlfa.fpar0119 ";
					$qSqlCon .= "WHERE ";
					$qSqlCon .= "pucidxxx = \"{$_POST['cPucId']}\" AND ";
					$qSqlCon .= "ctoidxxx = \"{$_POST['cCtoId']}\" AND ";
					$qSqlCon .= "regestxx = \"ACTIVO\" ";
					$xSqlCon  = f_MySql("SELECT","",$qSqlCon,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qSqlCon."~".mysql_num_rows($xSqlCon));
					if(mysql_num_rows($xSqlCon) > 0){
					  $nSwitch = 1;
					  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					  $cMsj .= "El No de Concepto ya Existe para esa Cuenta [fpar0119], \n";
					}

			    $qSqlCon  = "SELECT  * ";
          $qSqlCon .= "FROM $cAlfa.fpar0121 ";
          $qSqlCon .= "WHERE ";
          $qSqlCon .= "pucidxxx = \"{$_POST['cPucId']}\" AND ";
          $qSqlCon .= "ctoidxxx = \"{$_POST['cCtoId']}\" AND ";
          $qSqlCon .= "regestxx = \"ACTIVO\" ";
          $xSqlCon  = f_MySql("SELECT","",$qSqlCon,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qSqlCon."~".mysql_num_rows($xSqlCon));
          if(mysql_num_rows($xSqlCon) > 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El No de Concepto ya Existe para esa Cuenta [fpar0121], \n";
          }
				break;
			}

			if($_POST['cCtoId']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "El No de Concepto no Puede Ser Vacio.\n";
			}

			if($_POST['cCtoDes']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Digitar la Descripcion del Concepto.\n";
			}

			if($_POST['cCtoNit']==""){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Seleccionar el Tipo de Nit para Busqueda de Documeto Cruce.\n ";
			}

			if($_POST['cCtoTip']=="TERCEROS" && $_POST['cCtoNit']!="CLIENTE"){
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Si el Tipo de Concepto es TERCERO, el Nit Busqueda Documento Cruce debe ser CLIENTE.\n ";
			}

			$mSiEgr=explode("|",$_POST['cComMemo']);
			$nComp = 0;
			$cSqlCom = "";
			for ($i=0;$i<count($mSiEgr);$i++) {
				if($mSiEgr[$i] != "") {
					$nComp++;

					$mAux = array();
					$mAux = explode("~",$mSiEgr[$i]);
					$cSqlCom .= "$cAlfa.fpar0119.ctocomxx LIKE \"%|{$mAux[0]}~{$mAux[1]}~%\" OR ";
				}
			}
			$cSqlCom = substr($cSqlCom,0,strlen($cSqlCom)-4);

			if ($nComp == 0) {
			  $nSwitch = 1;
			  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			  $cMsj .= "Debe Escoger al menos un Comprobante.\n";
			}

			switch ($_POST['cCtoTip']) {
				case "PROPIOS":
					$_POST['cCtoCtori'] = "";
					$_POST['cCtoCtoRf'] = "";
					$_POST['cCtoCtrFs'] = "";
					$_POST['cCtoCtoRv'] = "";
					$_POST['cCtoCtoRc'] = "";
					$_POST['cCtoCtoCp'] = "";
					$_POST['cCtoNit2p'] = "";
					$_POST['cCtoNit2p'] = "";
				break;
				case "TERCEROS":

					/**
					 * Validando que exista cada uno de los conceptos de retencion
					 */
					//Conceptos de Retencion ICA
					if ($_POST['cCtoCtori'] != "") {
						$mAuxCtori = explode("|",$_POST['cCtoCtori']);
						
						$vCtaRtIca = explode(",",$vSysStr['financiero_cuentas_reteica']);
						$cCtaRtIca = "\"".implode("\",\"", $vCtaRtIca)."\"";

						for ($nA=0; $nA < count($mAuxCtori); $nA++) {
							if ($mAuxCtori[$nA] != "") {
								$qDatIca  = "SELECT $cAlfa.fpar0119.ctoidxxx ";
								$qDatIca .= "FROM $cAlfa.fpar0119 ";
								$qDatIca .= "WHERE ";
								$qDatIca .= "SUBSTRING($cAlfa.fpar0119.pucidxxx,1,4) IN ($cCtaRtIca) AND ";
								$qDatIca .= "$cAlfa.fpar0119.ctoidxxx = \"{$mAuxCtori[$nA]}\" AND ";
								$qDatIca .= "$cAlfa.fpar0119.ctosucri != \"\" AND ";
								$qDatIca .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
								$qDatIca .= "ORDER BY ABS($cAlfa.fpar0119.ctoidxxx) ";
								$xDatIca  = f_MySql("SELECT","",$qDatIca,$xConexion01,"");
								//f_Mensaje(__FILE__,__LINE__,$qDatIca."~".mysql_num_rows($xDatIca));
								if (mysql_num_rows($xDatIca) == 0) {
									$nSwitch = 1;
									$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
									$cMsj .= "El Concepto [{$mAuxCtori[$nA]}] de Retencion ICA no Existe o no es un Concepto de Retencion ICA.\n";
								}
							}
						}
					}

					//Conceptos de Retencion en la Fuente
					if ($_POST['cCtoCtoRf'] != "") {
						$qCtoId  = "SELECT $cAlfa.fpar0119.ctoidxxx ";
						$qCtoId .= "FROM $cAlfa.fpar0119 ";
						$qCtoId .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
						$qCtoId .= "WHERE ";
						$qCtoId .= "$cAlfa.fpar0119.ctoidxxx = \"{$_POST['cCtoCtoRf']}\" AND ";
						$qCtoId .= "$cAlfa.fpar0115.pucterxx = \"R\" AND ";
						$qCtoId .= "($cSqlCom) AND ";
						$qCtoId .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
						$qCtoId .= "ORDER BY ABS($cAlfa.fpar0119.ctoidxxx) ";
						$xCtoId  = f_MySql("SELECT","",$qCtoId,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qCtoId."~".mysql_num_rows($xCtoId));
						if (mysql_num_rows($xCtoId) == 0) {
							$nSwitch = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "El Concepto [{$_POST['cCtoCtoRf']}] de Retencion en la Fuente Regimen Comun no Existe o no es un Concepto de Retencion o no esta parametrizado para los Comprobantes seleccionados.\n";
						}
					}

					//Conceptos Retefuente - Regimen Simplificado
					if ($_POST['cCtoCtrFs'] != "") {
						$qCtoId  = "SELECT $cAlfa.fpar0119.ctoidxxx ";
						$qCtoId .= "FROM $cAlfa.fpar0119 ";
						$qCtoId .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
						$qCtoId .= "WHERE ";
						$qCtoId .= "$cAlfa.fpar0119.ctoidxxx = \"{$_POST['cCtoCtrFs']}\" AND ";
						$qCtoId .= "$cAlfa.fpar0115.pucterxx = \"R\" AND ";
						$qCtoId .= "($cSqlCom) AND ";
						$qCtoId .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
						$qCtoId .= "ORDER BY ABS($cAlfa.fpar0119.ctoidxxx) ";
						$xCtoId  = f_MySql("SELECT","",$qCtoId,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qCtoId."~".mysql_num_rows($xCtoId));
						if (mysql_num_rows($xCtoId) == 0) {
							$nSwitch = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "El Concepto [{$_POST['cCtoCtrFs']}] de Retencion en la Fuente Regimen Simplificado no Existe o no es un Concepto de Retencion o no esta parametrizado para los Comprobantes seleccionados.\n";
						}
					}

					//Conceptos de Retencion IVA - Gran Contribuyente
					if ($_POST['cCtoCtoRv'] != "") {
						$qCtoId  = "SELECT $cAlfa.fpar0119.ctoidxxx ";
						$qCtoId .= "FROM $cAlfa.fpar0119 ";
						$qCtoId .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
						$qCtoId .= "WHERE ";
						$qCtoId .= "$cAlfa.fpar0119.ctoidxxx = \"{$_POST['cCtoCtoRv']}\" AND ";
						$qCtoId .= "$cAlfa.fpar0115.pucterxx = \"R\" AND ";
						$qCtoId .= "($cSqlCom) AND ";
						$qCtoId .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
						$qCtoId .= "ORDER BY ABS($cAlfa.fpar0119.ctoidxxx) ";
						$xCtoId  = f_MySql("SELECT","",$qCtoId,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qCtoId."~".mysql_num_rows($xCtoId));
						if (mysql_num_rows($xCtoId) == 0) {
							$nSwitch = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "El Concepto [{$_POST['cCtoCtoRv']}] de Retencion IVA - Gran Contribuyente no Existe o no es un Concepto de Retencion o no esta parametrizado para los Comprobantes seleccionados.\n";
						}
					}

					//Conceptos de Retencion IVA - Regimen Comun
					if ($_POST['cCtoCtoRc'] != "") {
						$qCtoId  = "SELECT $cAlfa.fpar0119.ctoidxxx ";
						$qCtoId .= "FROM $cAlfa.fpar0119 ";
						$qCtoId .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
						$qCtoId .= "WHERE ";
						$qCtoId .= "$cAlfa.fpar0119.ctoidxxx = \"{$_POST['cCtoCtoRc']}\" AND ";
						$qCtoId .= "$cAlfa.fpar0115.pucterxx = \"R\" AND ";
						$qCtoId .= "($cSqlCom) AND ";
						$qCtoId .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
						$qCtoId .= "ORDER BY ABS($cAlfa.fpar0119.ctoidxxx) ";
						$xCtoId  = f_MySql("SELECT","",$qCtoId,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qCtoId."~".mysql_num_rows($xCtoId));
						if (mysql_num_rows($xCtoId) == 0) {
							$nSwitch = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "El Concepto [{$_POST['cCtoCtoRc']}] de Retencion IVA - Regimen Comun no Existe o no es un Concepto de Retencion o no esta parametrizado para los Comprobantes seleccionados.\n";
						}
					}

					//Validando que haya seleccinado concepto de cuenta por pagar
					if($_POST['cCtoCtoCp'] == ""){
  					$nSwitch = 1;
  					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			  	$cMsj .= "Debe Seleccionar En Datos Adicionales un Concepto de Cuenta por Pagar.\n";
  				} else {
  					//Validando que el concepto seleccionado sea un concepto de cuenta por pagar
  					$qCtoId  = "SELECT $cAlfa.fpar0119.ctoidxxx ";
  					$qCtoId .= "FROM $cAlfa.fpar0119 ";
  					$qCtoId .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
  					$qCtoId .= "WHERE ";
  					$qCtoId .= "$cAlfa.fpar0119.ctoidxxx = \"{$_POST['cCtoCtoCp']}\" AND ";
  					$qCtoId .= "$cAlfa.fpar0115.pucdetxx = \"P\" AND ";
  					$qCtoId .= "($cSqlCom) AND ";
  					$qCtoId .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
  					$qCtoId .= "ORDER BY ABS($cAlfa.fpar0119.ctoidxxx) ";
  					$xCtoId  = f_MySql("SELECT","",$qCtoId,$xConexion01,"");
  					//f_Mensaje(__FILE__,__LINE__,$qCtoId."~".mysql_num_rows($xCtoId));
  					if (mysql_num_rows($xCtoId) == 0) {
  						$nSwitch = 1;
  						$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  						$cMsj .= "El Concepto [{$_POST['cCtoCtoCp']}] de Cuenta por Pagar no Existe o no es un Concepto de Cuenta por Pagar o no esta parametrizado para los Comprobantes seleccionados.\n";
  					}
  				}

					if($vSysStr['financiero_aplica_nit1_nit2_integracion_sistema_contable'] == "SI"){
		  			if($_POST['cCtoNit1p'] == ""){
		  				$nSwitch = 1;
		  				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
		  		  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo P \n";
		  			}

		  			if($_POST['cCtoNit2p'] == ""){
		  				$nSwitch = 1;
		  				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
		  		  	$cMsj .= "Debe Digitar En Datos Adicionales Orden de Nits para Integracion con SAPHIENS, el Nit N codigo, Comprobante Tipo P \n";
		  			}
		  		}

				break;
				default:
					$nSwitch = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= "Debe Seleccionar el Tipo de Concepto.";
				break;
			}

	    /**
       * Codigo de Integracion con E2K
       */
      if ($cAlfa == "UPSXXXXX" || $cAlfa == "TEUPSXXXXX" || $cAlfa == "TEUPSXXXXP" || $cAlfa == "DEUPSXXXXX") {

        /**
         * Si la cuenta detalla por DO y la base de datos es de UPS se obliga a digitar el codigo E2K
         */
        if ($_POST['cCtoE2k'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Codigo de Integracion para E2K no Puede Ser Vacio.\n";
        }

       /**
        * Valido que sean solo alfanumericos
        */

        if ($_POST['cCtoE2k'] != "" && !preg_match("/^[[:alnum:]]+$/", $_POST['cCtoE2k'])) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Codigo de Integracion para E2K debe ser Alfanumerico.\n";
        }
      }
      if ($_POST['cNuAsBel'] != ""){ // Valida si es n�mero la asignacion para la Integracion de Belcorp 2013-06-12
      	if (!is_numeric($_POST['cNuAsBel'])) {
      		$nSwitch  = 1;
      		$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      		$cMsj .= "El N&uacute;mero de Asignaci&oacute;n debe ser N&uacute;merico. \n";
      	}else{
      		if ($_POST['cNuAsBel'] == 0) {
      			$nSwitch  = 1;
      			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      			$cMsj .= "El N&uacute;mero de Asignaci&oacute;n debe ser Mayor a Cero. \n";
      		}
      	}
      }

      /*** Si la variable Categoriacion Conceptos Facturacion esta encendida se habilita el menu de Categoria Conceptos***/
			if($vSysStr['system_habilitar_categorizacion_conceptos_facturacion'] == 'SI'){
				/*** Valido que la categoria concepto exista en el sistema.***/
				$qCatCon  = "SELECT * ";
				$qCatCon .= "FROM $cAlfa.fpar0144 ";
				$qCatCon .= "WHERE ";
				$qCatCon .= "cacidxxx = \"{$_POST['cCacId']}\" LIMIT 0,1 ";
				$xCatCon  = f_MySql("SELECT","",$qCatCon,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qCatCon."~".mysql_num_rows($xCatCon));
				if(mysql_num_rows($xCatCon) == 0 && $_POST['cCacId'] != ""){
					$nSwitch  = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= "La Categoria Concepto es invalida. \n";
				}
			}

			/*** Valido que el Codigo de Compra Eficiente Exista en la Base de Datos y no se Encuentre Inactivo ***/
			if($_POST['cCtoClapr'] == "001" && $_POST['cCceId'] == ""){
				$nSwitch  = 1;
				$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Campo Colombia Compra Eficiente no puede ser vacio. \n";
			}

			if($_POST['cCceId'] != ""){
				$qComEfi  = "SELECT regestxx ";
				$qComEfi .= "FROM $cAlfa.fpar0156 ";
				$qComEfi .= "WHERE ";
				$qComEfi .= "cceidxxx = \"{$_POST['cCceId']}\" LIMIT 0,1 ";
				$xComEfi  = f_MySql("SELECT","",$qComEfi,$xConexion01,"");

				if(mysql_num_rows($xComEfi) == 0){
					$nSwitch  = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= " El codigo Colombia Compra Eficiente[".$_POST['cCceId']."], No Existe en la Base de Datos. \n";
				}else{
					$vComEfi = mysql_fetch_array($xComEfi);
					if($vComEfi['regestxx'] == "INACTIVO"){
						$nSwitch  = 1;
						$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= " El codigo Colombia Compra Eficiente[".$_POST['cCceId']."], Se Encuentra INACTIVO. \n";
					}
				}
			}

			/*** Valido que la Unidad de Medida Exista en la Base de Datos y no se Encuentre Inactiva ***/
			if($_POST['cUmeId'] != ""){
				$qUniMed  = "SELECT regestxx ";
				$qUniMed .= "FROM $cAlfa.fpar0157 ";
				$qUniMed .= "WHERE ";
				$qUniMed .= "umeidxxx = \"{$_POST['cUmeId']}\" LIMIT 0,1 ";
				$xUniMed  = f_MySql("SELECT","",$qUniMed,$xConexion01,"");

				if(mysql_num_rows($xUniMed) == 0){
					$nSwitch  = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= " La Unidad de Medida[".$_POST['cUmeId']."], No Existe en la Base de Datos. \n";
				}else{
					$vUniMed = mysql_fetch_array($xUniMed);
					if($vUniMed['regestxx'] == "INACTIVO"){
						$nSwitch  = 1;
						$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= " La Unidad de Medida[".$_POST['cUmeId']."], se Encuentra INACTIVA. \n";
					}
				}
			}

  	break;
  	default:
  		$nSwitch = 1;
	    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  	  $cMsj .= "El Modo de Grabado es Vacio.\n";
  	break;
	}

  //f_Mensaje(__FILE__,__LINE__,"{$_POST['cCtoId']}");

	switch ($_COOKIE['kModo']) {
  	case "NUEVO":

	    if ($nSwitch == 0 ) {
		    $zInsertCsc = array(array('NAME'=>'pucidxxx','VALUE'=>$_POST['cPucId']                              ,'CHECK'=>'SI'),  //Id de la Cuenta PUC
														array('NAME'=>'ctocscxx','VALUE'=>$_POST['cCtoId']                              ,'CHECK'=>'SI'),  //Consecutivo del Concepto por Cuenta
														array('NAME'=>'ctoidxxx','VALUE'=>substr($_POST['cPucId'],0,6).$_POST['cCtoId']	,'CHECK'=>'SI'),  //Id del Concepto
														array('NAME'=>'ctodesxx','VALUE'=>trim(strtoupper($_POST['cCtoDes']))           ,'CHECK'=>'SI'),  //Descripci�n del Concepto
														array('NAME'=>'ctocomxx','VALUE'=>$_POST['cComMemo']                            ,'CHECK'=>'SI'),  //Comprobantes y Movimiento por Comprobante
														array('NAME'=>'ctovlr01','VALUE'=>$_POST['cCtoVlr01']                           ,'CHECK'=>'NO'),  //Calculo Autom�tico de la Base
														array('NAME'=>'ctovlr02','VALUE'=>$_POST['cCtoVlr02']                           ,'CHECK'=>'NO'),  //Calculo Autom�tico del Iva
														array('NAME'=>'ctotipxx','VALUE'=>$_POST['cCtoTip']                             ,'CHECK'=>'SI'),  //Tipo de Concepto [PROPIOS/TERCEROS]
														array('NAME'=>'ctonitxx','VALUE'=>$_POST['cCtoNit']                             ,'CHECK'=>'SI'),  //Nit para Transmisi�n a SIIGO
														array('NAME'=>'ctoctori','VALUE'=>$_POST['cCtoCtori']                           ,'CHECK'=>'NO'),  //Conceptos de Retenci�n Ica por Sucursal
														array('NAME'=>'ctoctorf','VALUE'=>$_POST['cCtoCtoRf']                           ,'CHECK'=>'NO'),  //Concepto de Rete Fuente
														array('NAME'=>'ctoctrfs','VALUE'=>$_POST['cCtoCtrFs']                           ,'CHECK'=>'NO'),  //Concepto de Rete Fuente SIMPLIFICADO
														array('NAME'=>'ctoctorv','VALUE'=>$_POST['cCtoCtoRv']                           ,'CHECK'=>'NO'),  //Concepto de Rete IVA - Gran Contribuyente
														array('NAME'=>'ctoctorc','VALUE'=>$_POST['cCtoCtoRc']                           ,'CHECK'=>'NO'),  //Concepto de Rete IVA
														array('NAME'=>'ctoctocp','VALUE'=>$_POST['cCtoCtoCp']                           ,'CHECK'=>'SI'),  //Concepto de Cuenta por Pagar
														array('NAME'=>'ctonit1p','VALUE'=>$_POST['cCtoNit1p']                           ,'CHECK'=>'NO'),  //Nit NCodigo Orden de Nits para Integracion con SAPHIENS Comprobantes de Tipo P
														array('NAME'=>'ctonit2p','VALUE'=>$_POST['cCtoNit2p']                           ,'CHECK'=>'NO'),  //Nit NPCodigo Orden de Nits para Integracion con SAPHIENS Comprobantes de Tipo P
														array('NAME'=>'ctoe2kxx','VALUE'=>$_POST['cCtoE2k']                             ,'CHECK'=>'NO'),  //Codigo de Integracion con E2K
														/***  Codigo Homologacion Aladuanas ***/
														array('NAME'=>'ctochald','VALUE'=>$_POST['cCtoChAld']                           ,'CHECK'=>'NO'),
														/***  Codigo Integracion con Belcorp***/
														array('NAME'=>'pucadbel','VALUE'=>trim($_POST['cPucBel'])                       ,'CHECK'=>'NO'), //C�digo Integraci�n Belcorp
														array('NAME'=>'pucadnas','VALUE'=>trim($_POST['cNuAsBel'])                      ,'CHECK'=>'NO'), //N�mero Asignaci�n Belcorp
																/*** Categoria concepto ***/
														array('NAME'=>'cacidxxx','VALUE'=>trim($_POST['cCacId'])                        ,'CHECK'=>'NO'),
                            /*** Integracion SAP ***/
														array('NAME'=>'ctosapid','VALUE'=>trim($_POST['cCtoSapId'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapcx','VALUE'=>trim($_POST['cCtoSapC'])                      ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapix','VALUE'=>trim($_POST['cCtoSapI'])                      ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapic','VALUE'=>trim($_POST['cCtoSapIc'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapiv','VALUE'=>trim($_POST['cCtoSapIv'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapca','VALUE'=>trim($_POST['cCtoSapCA'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapli','VALUE'=>trim($_POST['cCtoSapLI'])                     ,'CHECK'=>'NO'),
														array('NAME'=>'ctosaple','VALUE'=>trim($_POST['cCtoSapLE'])                     ,'CHECK'=>'NO'),
														/*** Clasificacion Producto ***/
														array('NAME'=>'ctoclapr','VALUE'=>trim($_POST['cCtoClapr'])                     ,'CHECK'=>'NO'),
														/*** Codigo Colombia Compra Eficiente ***/
														array('NAME'=>'cceidxxx','VALUE'=>trim($_POST['cCceId'])                        ,'CHECK'=>'NO'),
														/*** Unidad de Medida ***/
														array('NAME'=>'umeidxxx','VALUE'=>trim($_POST['cUmeId'])                        ,'CHECK'=>'NO'),
														array('NAME'=>'ctopuc85','VALUE'=>trim($_POST['cCtoPuc85'])                     ,'CHECK'=>'NO'),
														array('NAME'=>'ctocwccx','VALUE'=>trim($_POST['cCtocWccX'])        							,'CHECK'=>'NO'),
														array('NAME'=>'regusrxx','VALUE'=>$_COOKIE['kUsrId']                            ,'CHECK'=>'SI'),  //Usuario que Creo el Registro
														array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')                                 ,'CHECK'=>'SI'),  //Fecha de Creacion del Registro
														array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')                              		,'CHECK'=>'SI'),  //Hora de Creacion del Registro
														array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                              		,'CHECK'=>'SI'),  //Fecha de Modificacion del Registro
														array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                              		,'CHECK'=>'SI'),  //Hora de Modificacion del Registro
														array('NAME'=>'regestxx','VALUE'=>'ACTIVO'                             					,'CHECK'=>'SI'));  //Estado del Registro
    		if (f_MySql("INSERT","fpar0121",$zInsertCsc,$xConexion01,$cAlfa)){
    		}else{
    			$cMsj .= "Error al Guardar los Datos [fpar0121], Verifique ";
			    $nSwitch = 1;
    		}
	    }
  	break;
  	case "EDITAR":
	    if ($nSwitch == 0 ) {

		    $zInsertCsc = array(array('NAME'=>'ctodesxx','VALUE'=>trim(strtoupper($_POST['cCtoDes']))           ,'CHECK'=>'SI'),  //Descripci�n del Concepto
														array('NAME'=>'ctocomxx','VALUE'=>$_POST['cComMemo']                            ,'CHECK'=>'SI'),  //Comprobantes y Movimiento por Comprobante
														array('NAME'=>'ctovlr01','VALUE'=>$_POST['cCtoVlr01']                           ,'CHECK'=>'NO'),  //Calculo Autom�tico de la Base
														array('NAME'=>'ctovlr02','VALUE'=>$_POST['cCtoVlr02']                           ,'CHECK'=>'NO'),  //Calculo Autom�tico del Iva
														array('NAME'=>'ctotipxx','VALUE'=>$_POST['cCtoTip']                             ,'CHECK'=>'SI'),  //Tipo de Concepto [PROPIOS/TERCEROS]
														array('NAME'=>'ctonitxx','VALUE'=>$_POST['cCtoNit']                             ,'CHECK'=>'SI'),  //Nit para Transmisi�n a SIIGO
														array('NAME'=>'ctoctori','VALUE'=>$_POST['cCtoCtori']                           ,'CHECK'=>'NO'),  //Conceptos de Retenci�n Ica por Sucursal
														array('NAME'=>'ctoctorf','VALUE'=>$_POST['cCtoCtoRf']                           ,'CHECK'=>'NO'),  //Concepto de Rete Fuente
														array('NAME'=>'ctoctrfs','VALUE'=>$_POST['cCtoCtrFs']                           ,'CHECK'=>'NO'),  //Concepto de Rete Fuente SIMPLIFICADO
		    										array('NAME'=>'ctoctorv','VALUE'=>$_POST['cCtoCtoRv']                           ,'CHECK'=>'NO'),  //Concepto de Rete IVA - Gran Contribuyente
														array('NAME'=>'ctoctorc','VALUE'=>$_POST['cCtoCtoRc']                           ,'CHECK'=>'NO'),  //Concepto de Rete IVA - Regimen Comun
														array('NAME'=>'ctoctocp','VALUE'=>$_POST['cCtoCtoCp']                           ,'CHECK'=>'SI'),  //Concepto de Cuenta por Pagar
														array('NAME'=>'ctonit1p','VALUE'=>$_POST['cCtoNit1p']                           ,'CHECK'=>'NO'),  //Nit NCodigo Orden de Nits para Integracion con SAPHIENS Comprobantes de Tipo P
														array('NAME'=>'ctonit2p','VALUE'=>$_POST['cCtoNit2p']                           ,'CHECK'=>'NO'),  //Nit NPCodigo Orden de Nits para Integracion con SAPHIENS Comprobantes de Tipo P
														array('NAME'=>'ctoe2kxx','VALUE'=>$_POST['cCtoE2k']                             ,'CHECK'=>'NO'),  //Codigo de Integracion con E2K
														/***  Codigo Homologacion Aladuanas ***/
														array('NAME'=>'ctochald','VALUE'=>$_POST['cCtoChAld']                           ,'CHECK'=>'NO'),
										    		/***  Codigo Integracion con Belcorp***/
										    		array('NAME'=>'pucadbel','VALUE'=>trim($_POST['cPucBel'])                       ,'CHECK'=>'NO'), //C�digo Integraci�n Belcorp
										    		array('NAME'=>'pucadnas','VALUE'=>trim($_POST['cNuAsBel'])                      ,'CHECK'=>'NO'), //N�mero Asignaci�n Belcorp
										    		/*** Categoria concepto ***/
  								    	    array('NAME'=>'cacidxxx','VALUE'=>trim($_POST['cCacId'])                        ,'CHECK'=>'NO'),
                            /*** Integracion SAP ***/
														array('NAME'=>'ctosapid','VALUE'=>trim($_POST['cCtoSapId'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapcx','VALUE'=>trim($_POST['cCtoSapC'])                      ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapix','VALUE'=>trim($_POST['cCtoSapI'])                      ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapic','VALUE'=>trim($_POST['cCtoSapIc'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapiv','VALUE'=>trim($_POST['cCtoSapIv'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapca','VALUE'=>trim($_POST['cCtoSapCA'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosapli','VALUE'=>trim($_POST['cCtoSapLI'])                     ,'CHECK'=>'NO'),
                            array('NAME'=>'ctosaple','VALUE'=>trim($_POST['cCtoSapLE'])                     ,'CHECK'=>'NO'),
                            /*** Siempre que se edita un registro se limpia el campo ctosapxx = 0000-00-00 00:00:00 ***/
														array('NAME'=>'ctosapxx','VALUE'=>"0000-00-00 00:00:00"                         ,'CHECK'=>'NO'),
														/*** Clasificacion Producto ***/
														array('NAME'=>'ctoclapr','VALUE'=>trim($_POST['cCtoClapr'])                     ,'CHECK'=>'NO'),
														/*** Codigo Colombia Compra Eficiente ***/
														array('NAME'=>'cceidxxx','VALUE'=>trim($_POST['cCceId'])                        ,'CHECK'=>'NO'),
														/*** Unidad de Medida ***/
														array('NAME'=>'umeidxxx','VALUE'=>trim($_POST['cUmeId'])                        ,'CHECK'=>'NO'),
														array('NAME'=>'ctopuc85','VALUE'=>trim($_POST['cCtoPuc85'])                     ,'CHECK'=>'NO'),
														array('NAME'=>'ctocwccx','VALUE'=>trim($_POST['cCtocWccX'])        							,'CHECK'=>'NO'),
														array('NAME'=>'regusrxx','VALUE'=>$_COOKIE['kUsrId']                            ,'CHECK'=>'SI'),  //Usuario que Creo el Registro
														array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                              		,'CHECK'=>'SI'),  //Fecha de Modificacion del Registro
														array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                              		,'CHECK'=>'SI'),  //Hora de Modificacion del Registro
														array('NAME'=>'pucidxxx','VALUE'=>$_POST['cPucId']                              ,'CHECK'=>'WH'),
		                        array('NAME'=>'ctoidxxx','VALUE'=>$_POST['cCtoId']                              ,'CHECK'=>'WH'));
    		if (f_MySql("UPDATE","fpar0121",$zInsertCsc,$xConexion01,$cAlfa)){
    		  //Grabo bien
    		}else{
    		  $cMsj .= "Error al Actualizar los Datos [fpar0121], Verifique ";
			    $nSwitch = 1;
    		}
	    }
  	break;
	}

	if($nSwitch==0){
	  switch ($_COOKIE['kModo']) {
		 case "NUEVO":
			 f_Mensaje(__FILE__,__LINE__,"El Concepto ha Sido Creado Con Exito, Verifique");
		 break;
		 case "EDITAR":
		  f_Mensaje(__FILE__,__LINE__,"El Concepto ha Sido Modificado Con Exito, Verifique");
		 break;
	  } ?>
		 <html><body>
		 <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt']; ?>" method = "post" target = "fmwork"></form>
		 <script languaje = "javascript">
  		 parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			 document.frgrm.submit();
		 </script>
		 </body>
		 </html>
	<?php } else {
  	f_Mensaje(__FILE__,__LINE__,"$cMsj Verifique");
  }
?>

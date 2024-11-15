<?php
/**
 * Graba Concepto de Cobro.
 * Este programa permite Guardar en la tabla Concepto de Cobro.
 * @author
 * @package emisioncero
 */
	include("../../../../libs/php/utility.php");

	$nSwitch = 0; // Switch para Vericar la Validacion de Datos
	$cMsj = "\n";

	switch ($_COOKIE['kModo']) {
	  //case "NUEVO":
	  case "EDITAR":

	    $_POST['cSerId'] = trim(strtoupper($_POST['cSerId']));

	    /***** Validando Descripcion *****/
  		if ($_POST['cSerDes'] == "") {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  		  $cMsj .= "Descripcion Concepto de Cobro no puede ser vacia.\n";
  		}

  		if ($_POST['cSerDesP'] == "") {
  		  $_POST['cSerDesP'] = $_POST['cSerDes'];
  		}

  		/***** Validando Orden *****/
  		if ($_POST['cSerOrd'] == "") {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  		  $cMsj .= "El Orden del Concepto de Cobro no puede ser vacio.\n";
			}
			
  		if ($_POST['cSerChsa'] != $_POST['cSerId'] && $_POST['cSerChsa'] != "" &&
					($cAlfa == "ALPOPULX" || $cAlfa == "TEALPOPULP" || $cAlfa == "TEALPOPULX" || $cAlfa == "DEALPOPULX")) {

				$qConDat  = "SELECT * ";
				$qConDat .= "FROM $cAlfa.fpar0129 ";
				$qConDat .= "WHERE ";
				$qConDat .= "seridxxx = \"{$_POST['cSerChsa']}\"  " ;
	  		$xConDat  = f_MySql("SELECT","",$qConDat,$xConexion01,"");
	    	// f_Mensaje(__FILE__,__LINE__,$qConDat."~".mysql_num_rows($xConDat));
	    	if (mysql_num_rows($xConDat) > 0) {
	    	  $vConDat = mysql_fetch_array($xConDat);
	    	  #Datos Clientes Nacionales
	    	  if($vConDat['pucidxxx'] != ""){
	    	  	$_POST['cPucId'] = $vConDat['pucidxxx'];
	    	  }else{
	    	  	$nSwitch = 1;
	    	  	$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  		      $cMsj .= "El Concepto de Cobro ". $vConDat['seridxxx']." No tiene Parametrizada Cuenta Puc para Clientes Nacionales\n";
	    	  }

	    	  if($vConDat['pucmovxx'] != ""){
	    	  	$_POST['cPucMov'] = $vConDat['pucmovxx'];
	    	  }else{
	    	  	$nSwitch = 1;
	    	  	$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  		      $cMsj .= "El Concepto de Cobro ". $vConDat['seridxxx']." No tiene Parametrizado Movimiento para Clientes Nacionales\n";
	    	  }

	    		#Datos Clientes Exterior
	    	  if($vConDat['pucidexx'] != ""){
	    	  	$_POST['cPucIdExt'] = $vConDat['pucidexx'];
	    	  }else{
	    	  	$nSwitch = 1;
	    	  	$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  		      $cMsj .= "El Concepto de Cobro ". $vConDat['seridxxx']." No tiene Parametrizada Cuenta Puc para Clientes del Exterior\n";
	    	  }

	    	  if($vConDat['pucmovex'] != ""){
	    	  	$_POST['cPucMovExt'] = $vConDat['pucmovex'];
	    	  }else{
	    	  	$nSwitch = 1;
	    	  	$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  		      $cMsj .= "El Concepto de Cobro ". $vConDat['seridxxx']." No tiene Parametrizado Movimiento para Clientes del Exterior\n";
	    	  }

	    	  #Validando centro de costo
	    	  if($vConDat['sersgxxx'] != ""){
	    	  	$_POST['cSerSg'] = $vConDat['sersgxxx'];
	    	  }else{
	    	  	$nSwitch = 1;
	    	  	$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  		      $cMsj .= "El Concepto de Cobro ". $vConDat['seridxxx']." No tiene Parametrizado Centro de Costo\n";
	    	  }

	    	  $_POST['cCtoId']    = $_POST['cSerChsa'];
	    	  $_POST['cCtoIdExt'] = $_POST['cSerChsa'];

	    	}else{
	    		$nSwitch = 1;
	    		$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  		    $cMsj .= "El Codigo de Servicio ".$_POST['cSerChsa']." debe existir como Concepto de Cobro.\n";
	    	}
  		} else {
  		  /***** Validando que digite datos en la Cuenta PUC *****/
  		  if ($_POST['cPucId']    != "" || $_POST['cCtoId']     != "" ||
				    $_POST['cPucMov']   != "" || $_POST['cPucMovExt'] != "" ||
						$_POST['cPucIdExt'] != "" || $_POST['cCtoIdExt']  != "") {
            /***** Validando Cuenta PUC *****/
          if ($_POST['cPucId'] == "" ) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "La Cuenta PUC para el Concepto de Cobro no puede ser vacio.\n";
          }

          /***** Validando Movimiento *****/
          if ($_POST['cPucMov'] == "") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Movimiento no puede ser vacio.\n";
          }

          /***** Validando Cuenta PUC Cliente Exterior*****/
          if ($_POST['cPucIdExt'] == "") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "La Cuenta PUC para Clientes del Exterior para el Concepto de Cobro no puede ser vacio.\n";
          }

          /***** Validando Movimiento Cliente Exterior*****/
          if ($_POST['cPucMovExt'] == "") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Movimiento para Clientes del Exterior no puede ser vacio.\n";
					}
					
          if ($_POST['cSerChsa'] != "" && ($cAlfa == "ALPOPULX" || $cAlfa == "TEALPOPULP" || $cAlfa == "TEALPOPULX" || $cAlfa == "DEALPOPULX")) {
            $_POST['cCtoId']    = $_POST['cSerChsa'];
            $_POST['cCtoIdExt'] = $_POST['cSerChsa'];
          }else {
            /***** Validando Concepto *****/
            if (strlen($_POST['cCtoId']) != 10) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Concepto debe ser Numerico y su Longitud es de Diez Caracteres.\n";
            }

            /***** Validando Concepto Cliente Exterior*****/
            if (strlen($_POST['cCtoIdExt']) != 10) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Concepto para Clientes del Exterior debe ser Numerico y su Longitud es de Diez Caracteres.\n";
            }
					}
					
					/**
					 * Validacion para almavia, el codigo de servicio debe ser obligatorio
					 */
					if($cAlfa == "ALMAVIVA" || $cAlfa == "TEALMAVIVA" || $cAlfa == "DEALMAVIVA") {
						if ($_POST['cSerSg'] == "") {
							$nSwitch = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "El Centro de Costo de Seven No Puede Ser Vacio.\n";
						}

						if ($_POST['cSerChsa'] == "") {
							$nSwitch = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "El Codigo de Servicio de Seven No Puede Ser Vacio.\n";
						}
					}

        /**
         * Validaciones para ver si el concepto esta repetido
         * para ALPOPULAR solo puede haber dos conceptos con el mismo cCtoId el creado desde el ws y el de opencomex
         * para los demas clientes solo puede existir el concepto una sola vez
         */
        if ($_POST['cSerChsa'] != "" && ($cAlfa == "ALPOPULX" || $cAlfa == "TEALPOPULP" || $cAlfa == "TEALPOPULX" || $cAlfa == "DEALPOPULX")) {
          ##Validacion para verificar que no se repita el concepto de clientes nacionales
          $qConDat  = "SELECT ctoidxxx,ctoidexx,seridxxx,serdespx,serchsax ";
          $qConDat .= "FROM $cAlfa.fpar0129 ";
          $qConDat .= "WHERE ";
          $qConDat .= "(ctoidxxx = \"{$_POST['cCtoId']}\" OR ctoidexx = \"{$_POST['cCtoId']}\") AND " ;
          $qConDat .= "seridxxx != \"{$_POST['cSerId']}\" " ;
          $xConDat  = f_MySql("SELECT","",$qConDat,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qConDat."~".mysql_num_rows($xConDat));
          if (mysql_num_rows($xConDat) > 0) {
            $cConceptos = "";
            while ($xRCD = mysql_fetch_array($xConDat)) {
              /**
               * Si el concepto que se esta guardando es de logitud 8 es un concepto ALPOPULAR
               * si el concepto que se esta guardando es de logitud diferente a 8 es un concepto de opencomex
               */
              if (strlen($_POST['cSerId']) == 8) {
                /**
                 * Si el servicio encontrado tiene longitud 8 es un concepto de ALPOPULAR
                 * lo que significa el codigo de servicio de alpopular ya esta asignado a otro servicio de ALPOPULAR
                 * y se muestra el error
                 *
                 * Si la logitud es diferente de 8 significa que ese concepto esta asigando a un concepto de opencomex
                 * y se permite guardar el concepto
                 */
                if (strlen($xRCD['seridxxx']) == 8) {
                  $cConceptos .= "{$xRCD['serdespx']} [{$xRCD['seridxxx']}], ";
                }
              } else {
                /**
                 * Si el servicio encontrado tiene longitud 8 es un concepto de ALPOPULAR
                 * y se permite guardar el concepto porque se asume que es el concepto de donde se toman los datos
                 *
                 * Si la logitud es diferente de 8 significa que ese concepto esta asigando a un concepto de opencomex
                 * lo que significa el codigo de servicio de alpopular ya esta asignado a otro concepto de opencomex
                 * y se muestra el error
                 */
                if (strlen($xRCD['seridxxx']) != 8) {
                  $cConceptos .= "{$xRCD['serdespx']} [{$xRCD['seridxxx']}], ";
                }
              }
            }
            $cConceptos = substr($cConceptos,0,strlen($cConceptos)-2);
            if ($cConceptos != "") {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Concepto {$_POST['cCtoId']} de Clientes Nacionales se Encuentra Asignado al Concepto $cConceptos.\n";
            }
          }
          ##Validacion para verificar que no se repita el concepto de clientes nacionales

          ##Validacion para verificar que no se repita el concepto de clientes nacionales
          $qConDat  = "SELECT ctoidxxx,ctoidexx,seridxxx,serdespx,serchsax ";
          $qConDat .= "FROM $cAlfa.fpar0129 ";
          $qConDat .= "WHERE ";
          $qConDat .= "(ctoidxxx = \"{$_POST['cCtoIdExt']}\" OR ctoidexx = \"{$_POST['cCtoIdExt']}\") AND " ;
          $qConDat .= "seridxxx != \"{$_POST['cSerId']}\" " ;
          $xConDat  = f_MySql("SELECT","",$qConDat,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qConDat."~".mysql_num_rows($xConDat));
          if (mysql_num_rows($xConDat) > 0) {
            $cConceptos = "";
            while ($xRCD = mysql_fetch_array($xConDat)) {
              /**
               * Si el concepto que se esta guardando es de logitud 8 es un concepto ALPOPULAR
               * si el concepto que se esta guardando es de logitud diferente a 8 es un concepto de opencomex
               */
              if (strlen($_POST['cSerId']) == 8) {
                /**
                 * Si el servicio encontrado tiene longitud 8 es un concepto de ALPOPULAR
                 * lo que significa el codigo de servicio de alpopular ya esta asignado a otro servicio de ALPOPULAR
                 * y se muestra el error
                 *
                 * Si la logitud es diferente de 8 significa que ese concepto esta asigando a un concepto de opencomex
                 * y se permite guardar el concepto
                 */
                if (strlen($xRCD['seridxxx']) == 8) {
                  $cConceptos .= "{$xRCD['serdespx']} [{$xRCD['seridxxx']}], ";
                }
              } else {
                /**
                 * Si el servicio encontrado tiene longitud 8 es un concepto de ALPOPULAR
                 * y se permite guardar el concepto porque se asume que es el concepto de donde se toman los datos
                 *
                 * Si la logitud es diferente de 8 significa que ese concepto esta asigando a un concepto de opencomex
                 * lo que significa el codigo de servicio de alpopular ya esta asignado a otro concepto de opencomex
                 * y se muestra el error
                 */
                if (strlen($xRCD['seridxxx']) != 8) {
                  $cConceptos .= "{$xRCD['serdespx']} [{$xRCD['seridxxx']}], ";
                }
              }
            }
            $cConceptos = substr($cConceptos,0,strlen($cConceptos)-2);
            if ($cConceptos != "") {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Concepto {$_POST['cCtoIdExt']} de Clientes Exterior se Encuentra Asignado al Concepto $cConceptos.\n";
            }
          }
          ##Validacion para verificar que no se repita el concepto de clientes nacionales
        } else {
          ##Validacion para verificar que no se repita el concepto de clientes nacionales
          $qConDat  = "SELECT ctoidxxx,ctoidexx,seridxxx,serdespx ";
          $qConDat .= "FROM $cAlfa.fpar0129 ";
          $qConDat .= "WHERE ";
          $qConDat .= "(ctoidxxx = \"{$_POST['cCtoId']}\" OR ctoidexx = \"{$_POST['cCtoId']}\") AND " ;
          $qConDat .= "seridxxx != \"{$_POST['cSerId']}\" LIMIT 0,1 " ;
          $xConDat  = f_MySql("SELECT","",$qConDat,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qConDat."~".mysql_num_rows($xConDat));
          if (mysql_num_rows($xConDat) > 0) {
            $xRCD = mysql_fetch_array($xConDat);
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Concepto {$_POST['cCtoId']} de Clientes Nacionales se Encuentra Asignado al Concepto [{$xRCD['seridxxx']}] {$xRCD['serdespx']}.\n";
          }
          ##Fin Validacion para verificar que no se repita el concepto de clientes nacionales

          ##Validacion para verificar que no se repita el concepto de clientes del exterior
          $qConDat  = "SELECT ctoidxxx,ctoidexx,seridxxx,serdespx ";
          $qConDat .= "FROM $cAlfa.fpar0129 ";
          $qConDat .= "WHERE ";
          $qConDat .= "(ctoidxxx = \"{$_POST['cCtoIdExt']}\" OR ctoidexx = \"{$_POST['cCtoIdExt']}\") AND " ;
          $qConDat .= "seridxxx != \"{$_POST['cSerId']}\" LIMIT 0,1 " ;
          $xConDat  = f_MySql("SELECT","",$qConDat,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qConDat."~".mysql_num_rows($xConDat));
          if (mysql_num_rows($xConDat) > 0) {
            $xRCD = mysql_fetch_array($xConDat);
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Concepto {$_POST['cCtoIdExt']} de Clientes del Exterior se Encuentra Asignado al Concepto [{$xRCD['seridxxx']}] {$xRCD['serdespx']}.\n";
          }
          ##Fin Validacion para verificar que no se repita el concepto de clientes del exterior
        }
  		  /***** Fin Validando Cuenta PUC *****/
  		 /***** Validando Cuenta PUC *****/
	  		/*if ($_POST['cPucId'] == "" ) {
	  		  $nSwitch = 1;
	  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  		  $cMsj .= "La Cuenta PUC para el Concepto de Cobro no puede ser vacio.\n";
	  		}*/

	  		/***** Validando Cuenta PUC Cliente Exterior*****/
	  		/*if ($_POST['cPucIdExt'] == "") {
	  		  $nSwitch = 1;
	  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	  		  $cMsj .= "La Cuenta PUC para Clientes del Exterior para el Concepto de Cobro no puede ser vacio.\n";
	  		}*/
	  		}//fin si deja guardar sin Cuneta PUC y concep
  		}

  		##Valido si se parametriza cuenta de Autoretencion en la Fuente se debe parametrizar obligatoriamente Cuenta para Retencion en la Fuente##
  		if ($_POST['cPucARfte'] != "" && $_POST['cPucRfte'] == "" ) {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  		  $cMsj .= "Usted ha parametrizado Cuenta de Autorentencion en la Fuente, debe tambien Parametrizar Cuenta de Retencion en la Fuente\n";
  		}
  		##Valido si se parametriza cuenta de Autoretencion en la Fuente se debe parametrizar obligatoriamente Cuenta para Retencion en la Fuente##

			##Valido si se parametriza cuenta de Autoretencion en la Fuente se debe parametrizar obligatoriamente Cuenta para Retencion en la Fuente##
  		if ($_POST['cPucRfteT'] != "" && $_POST['cPucARfteT'] == "") {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  		  $cMsj .= "Usted ha parametrizado Cuenta de Rentencion en la Fuente Regimen Simple Tributacion, debe tambien Parametrizar Cuenta de Autoretencion en la Fuente Regimen Simple Tributacion.\n";
  		}
  		##Valido si se parametriza cuenta de Autoretencion en la Fuente se debe parametrizar obligatoriamente Cuenta para Retencion en la Fuente##

  		##Valido si se parametriza cuenta de Autoretencion en la Fuente se debe parametrizar obligatoriamente Cuenta para Retencion en la Fuente##
  		if ($_POST['cPucARfteT'] != "" && $_POST['cPucRfteT'] == "" ) {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  		  $cMsj .= "Usted ha parametrizado Cuenta de Autorentencion en la Fuente Regimen Simple Tributacion, debe tambien Parametrizar Cuenta de Retencion en la Fuente Regimen Simple Tributacion.\n";
  		}
  		##Valido si se parametriza cuenta de Autoretencion en la Fuente se debe parametrizar obligatoriamente Cuenta para Retencion en la Fuente##


  		##Valido si se parametriza cuenta de Autoretencion CREE se debe parametrizar obligatoriamente Cuenta para Retencion CREE##
  		if ($_POST['cPucARcr'] != "" && $_POST['cPucRcr'] == "" ) {
  			$nSwitch = 1;
  			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  			$cMsj .= "Usted ha parametrizado Cuenta de Autorentencion CREE, debe tambien Parametrizar Cuenta de Retencion CREE\n";
  		}
  		##Valido si se parametriza cuenta de Autoretencion CREE se debe parametrizar obligatoriamente Cuenta para Retencion CREE##


  		##Valido si se parametriza cuenta de Autoretencion en la Fuente se debe parametrizar obligatoriamente Cuenta para Retencion en la Fuente##
  		if ($_POST['cPucARica'] != "" && $_POST['cPucRica'] == "" ) {
  		  $nSwitch = 1;
  		  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
  		  $cMsj .= "Usted ha parametrizado Cuenta de Autorentencion de ICA, debe tambien Parametrizar Cuenta de Retencion de ICA\n";
  		}
  		##Valido si se parametriza cuenta de Autoretencion en la Fuente se debe parametrizar obligatoriamente Cuenta para Retencion en la Fuente##


			/**
			 * Codigo de Integracion con E2K
			 */
			if ($cAlfa == "UPSXXXXX" || $cAlfa == "TEUPSXXXXX" || $cAlfa == "TEUPSXXXXP" || $cAlfa == "DEUPSXXXXX") {
			 /**
			  * Valido codigo E2K
			  */
			 if ($_POST['cCtoE2k'] == "") {
			   $nSwitch = 1;
         $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
         $cMsj .= "El Codigo de Integracion para E2K no puede ser vacio.\n";
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

			/* Validado El Estado del Registro */
			if ($_POST['cEstado'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Estado del Registro no puede ser vacio.\n";
			}

			// Valida si es n�mero la asignacion para la Integracion de Belcorp 2013-06-12
			if ($_POST['cNuAsBel'] != ""){
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
			## Validaciones respectivas al Material SAP ##
			switch ($cAlfa) {
				case "TEALMACAFE":
				case "DEALMACAFE":
        case "ALMACAFE":
        case "DEALPOPULX":
        case "TEALPOPULP":
        case "ALPOPULX":
        case "DEALMAVIVA":
        case "TEALMAVIVA":
        case "ALMAVIVA":
					if (trim($_POST['cSerMaSap']) == "") {
						$nSwitch = 1;
						$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= "El Material SAP no puede ser vac&iacute;o.\n";
					}
				break;
				default:
					$_POST['cSerMaSap'] = "";
				break;
			}
			## FIN validaciones al Material SAP ##

			/*** Valido que el Codigo de Compra Eficiente Exista en la Base de Datos y no se Encuentre Inactivo ***/
			if($_POST['cSerClapr'] == "001" && $_POST['cCceId'] == ""){
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
      
      /**** Validando centro y subcentro de costo ****/
      if($_POST['cCcoId'] != ""){
				$qDesCco  = "SELECT regestxx ";
				$qDesCco .= "FROM $cAlfa.fpar0116 ";
				$qDesCco .= "WHERE ccoidxxx = \"{$_POST['cCcoId']}\" LIMIT 0,1 ";
				$xDesCco  = f_MySql("SELECT","",$qDesCco,$xConexion01,"");

				if(mysql_num_rows($xDesCco) == 0){
					$nSwitch  = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= " El Centro de Costo[".$_POST['cCcoId']."], No Existe en la Base de Datos. \n";
				}else{
					$vDesCco = mysql_fetch_array($xDesCco);
					if($vDesCco['regestxx'] == "INACTIVO"){
						$nSwitch  = 1;
						$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= " El Centro de Costo[".$_POST['cCcoId']."], se Encuentra INACTIVO. \n";
					}
				}
      }
      
      if($_POST['cSccId'] != ""){
				$qDesScc  = "SELECT regestxx ";
				$qDesScc .= "FROM $cAlfa.fpar0120 ";
				$qDesScc .= "WHERE ccoidxxx = \"{$_POST['cCcoId']}\" AND ";
				$qDesScc .= "sccidxxx = \"{$_POST['cSccId']}\" LIMIT 0,1 ";
				$xDesScc  = f_MySql("SELECT","",$qDesScc,$xConexion01,"");

				if(mysql_num_rows($xDesScc) == 0){
					$nSwitch  = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= " El Subcentro de Costo[".$_POST['cSccId']."], No Existe en la Base de Datos. \n";
				}else{
					$vDesScc = mysql_fetch_array($xDesScc);
					if($vDesScc['regestxx'] == "INACTIVO"){
						$nSwitch  = 1;
						$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						$cMsj .= " El Subcentro de Costo[".$_POST['cSccId']."], se Encuentra INACTIVO. \n";
					}
				}
			}

			$_POST['cSerAplCu'] = ($_POST['cSerAplCu'] == "on") ? "SI" : "NO";

			$cLineaNeg = "";
			$vBDIntegracionColmasSap = explode("~",$vSysStr['system_integracion_colmas_sap']);
			if (in_array($cAlfa, $vBDIntegracionColmasSap) == true) {
				// Validando las lineas de ingreso
				for ($i=0;$i<$_POST['nSecuencia_Grid_LineaNegocio'];$i++) {
					if ($_POST['cCodLineaNeg' . ($i+1)] != "") {
						$cLineaNeg .= $_POST['cCodLineaNeg' . ($i+1)]."~".$_POST['cCtaIngreso' . ($i+1)]."~".$_POST['cCtaCosto' . ($i+1)]."|";

						// Validando la linea de negocio
						$qLineaNeg  = "SELECT ";
						$qLineaNeg .= "regestxx ";
						$qLineaNeg .= "FROM $cAlfa.zcol0003 ";
						$qLineaNeg .= "WHERE ";
						$qLineaNeg .= "lnecodxx = \"{$_POST['cCodLineaNeg' . ($i+1)]}\" LIMIT 0,1";
						$xLineaNeg  = f_MySql("SELECT","",$qLineaNeg,$xConexion01,"");
						if (mysql_num_rows($xLineaNeg) > 0) {
							$vLineaNeg = mysql_fetch_array($xLineaNeg);
							if ($vLineaNeg['regestxx'] == "INACTIVO") {
								$nSwitch  = 1;
								$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
								$cMsj .= " La Linea de Negocio[".$_POST['cCodLineaNeg' . ($i+1)]."], se Encuentra INACTIVA, Secuencia [".($i+1)."]. \n";
							}
						} else {
							$nSwitch  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= " La Linea de Negocio[".$_POST['cCodLineaNeg' . ($i+1)]."], No Existe, Secuencia [".($i+1)."]. \n";
						}

						if ($_POST['cCtaIngreso' . ($i+1)] == "" && $_POST['cCtaCosto' . ($i+1)] == "") {
							$nSwitch  = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= " Debe Ingresar la Cuenta de Ingreso o la Cuenta de Costo, Secuencia [".($i+1)."]. \n";
						}
					}
				}
			}
  	break;
	}	/***** Fin de la Validacion *****/

	/***** Ahora Empiezo a Grabar *****/
	/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
			  $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Para [INSERTAR] Un Concepto de Cobro, por favor Comunicarse con openTecnologia.";
			break;
			/*****************************   UPDATE    ***********************************************/
			case "EDITAR":
				if ($nSwitch == 0) {
					$cInsertTab	 = array(array('NAME'=>'serdespx','VALUE'=>trim(strtoupper($_POST['cSerDesP']))  ,'CHECK'=>'SI'),
					                     array('NAME'=>'serordxx','VALUE'=>trim(strtoupper($_POST['cSerOrd']))   ,'CHECK'=>'SI'),
				                       array('NAME'=>'pucidxxx','VALUE'=>trim(strtoupper($_POST['cPucId']))    ,'CHECK'=>'NO'),
				                       array('NAME'=>'pucmovxx','VALUE'=>trim(strtoupper($_POST['cPucMov']))   ,'CHECK'=>'NO'),
				                       array('NAME'=>'ctoidxxx','VALUE'=>trim(strtoupper($_POST['cCtoId']))    ,'CHECK'=>'NO'),
				                       array('NAME'=>'pucidexx','VALUE'=>trim(strtoupper($_POST['cPucIdExt'])) ,'CHECK'=>'NO'),
				                       array('NAME'=>'pucmovex','VALUE'=>trim(strtoupper($_POST['cPucMovExt'])),'CHECK'=>'NO'),
				                       array('NAME'=>'ctoidexx','VALUE'=>trim(strtoupper($_POST['cCtoIdExt'])) ,'CHECK'=>'NO'),
				                       array('NAME'=>'pucivaxx','VALUE'=>trim(strtoupper($_POST['cPucIva']))   ,'CHECK'=>'NO'),
				                       array('NAME'=>'pucrftex','VALUE'=>trim(strtoupper($_POST['cPucRfte']))  ,'CHECK'=>'NO'),
				                       array('NAME'=>'pucaftex','VALUE'=>trim(strtoupper($_POST['cPucARfte'])) ,'CHECK'=>'NO'),
				                       array('NAME'=>'pucrftet','VALUE'=>trim(strtoupper($_POST['cPucRfteT'])) ,'CHECK'=>'NO'),
				                       array('NAME'=>'pucaftet','VALUE'=>trim(strtoupper($_POST['cPucARfteT'])),'CHECK'=>'NO'),
															 array('NAME'=>'pucrcrxx','VALUE'=>trim(strtoupper($_POST['cPucRcr']))   ,'CHECK'=>'NO'),
															 array('NAME'=>'pucacrxx','VALUE'=>trim(strtoupper($_POST['cPucARcr']))  ,'CHECK'=>'NO'),
				                       array('NAME'=>'pucricax','VALUE'=>trim(strtoupper($_POST['cPucRica']))  ,'CHECK'=>'NO'),
				                       array('NAME'=>'pucaicax','VALUE'=>trim(strtoupper($_POST['cPucARica'])) ,'CHECK'=>'NO'),
				                       array('NAME'=>'pucrivax','VALUE'=>trim(strtoupper($_POST['cPucRiva']))  ,'CHECK'=>'NO'),
															 array('NAME'=>'pucriva1','VALUE'=>trim(strtoupper($_POST['cPucRiva01'])),'CHECK'=>'NO'),
															 array('NAME'=>'sercxcip','VALUE'=>trim(strtoupper($_POST['cSerCxcIp'])) ,'CHECK'=>'NO'),
				                       array('NAME'=>'sersgxxx','VALUE'=>trim(strtoupper($_POST['cSerSg']))    ,'CHECK'=>'NO'),
                               array('NAME'=>'serchsax','VALUE'=>trim(strtoupper($_POST['cSerChsa']))  ,'CHECK'=>'NO'),
                               /*** Centro y subcentro de costo ****/
                               array('NAME'=>'ccoidxxx','VALUE'=>trim(strtoupper($_POST['cCcoId']))    ,'CHECK'=>'NO'),
				                       array('NAME'=>'sccidxxx','VALUE'=>trim(strtoupper($_POST['cSccId']))    ,'CHECK'=>'NO'),
                               /***  Codigo Integracion con Belcorp***/
                               array('NAME'=>'pucadbel','VALUE'=>trim($_POST['cPucBel'])               ,'CHECK'=>'NO'), //Codigo Integracion Belcorp
                               array('NAME'=>'pucadnas','VALUE'=>trim($_POST['cNuAsBel'])              ,'CHECK'=>'NO'), //Numero Asignacion Belcorp
				                       array('NAME'=>'ctoe2kxx','VALUE'=>trim(strtoupper($_POST['cCtoE2k']))   ,'CHECK'=>'NO'),
															 array('NAME'=>'sermasap','VALUE'=>trim(strtoupper($_POST['cSerMaSap'])) ,'CHECK'=>'NO'),
															 /***  Codigo Homologacion Aladuanas ***/
															 array('NAME'=>'serchald','VALUE'=>trim(strtoupper($_POST['cSerChAld'])) ,'CHECK'=>'NO'),
                               /***  Codigo Integracion SAP ***/
															 array('NAME'=>'sersapid','VALUE'=>trim(strtoupper($_POST['cSerSapId'])) ,'CHECK'=>'NO'),
                               array('NAME'=>'sersapcx','VALUE'=>trim(strtoupper($_POST['cSerSapC']))  ,'CHECK'=>'NO'),
                               array('NAME'=>'sersapix','VALUE'=>trim(strtoupper($_POST['cSerSapI']))  ,'CHECK'=>'NO'),
                               array('NAME'=>'sersapic','VALUE'=>trim(strtoupper($_POST['cSerSapIc'])) ,'CHECK'=>'NO'),
                               array('NAME'=>'sersapiv','VALUE'=>trim(strtoupper($_POST['cSerSapIv'])) ,'CHECK'=>'NO'),
                               array('NAME'=>'sersapca','VALUE'=>trim(strtoupper($_POST['cSerSapCA'])) ,'CHECK'=>'NO'),
                               array('NAME'=>'sersapcl','VALUE'=>trim(strtoupper($_POST['cSerSapCL'])) ,'CHECK'=>'NO'),
                               /*** Siempre que se edita un registro se limpia el campo sersapxx = 0000-00-00 00:00:00 ***/
                               array('NAME'=>'sersapxx','VALUE'=>"0000-00-00 00:00:00"                 ,'CHECK'=>'NO'),
															 array('NAME'=>'sercipal','VALUE'=>trim(strtoupper($_POST['cSerCipAl'])) ,'CHECK'=>'NO'),
															 array('NAME'=>'seraplcu','VALUE'=>$_POST['cSerAplCu']									 ,'CHECK'=>'NO'),
															 array('NAME'=>'sercodse','VALUE'=>$_POST['cSerCodSe']									 ,'CHECK'=>'NO'),
															 /*** Clasificacion Producto ***/
															 array('NAME'=>'serclapr','VALUE'=>trim($_POST['cSerClapr'])             ,'CHECK'=>'NO'),
														   /*** Codigo Colombia Compra Eficiente ***/
														   array('NAME'=>'cceidxxx','VALUE'=>trim($_POST['cCceId'])                ,'CHECK'=>'NO'),
														   /*** Unidad de Medida ***/
															 array('NAME'=>'umeidxxx','VALUE'=>trim($_POST['cUmeId'])                ,'CHECK'=>'NO'),
															 array('NAME'=>'sercwccx','VALUE'=>trim($_POST['cSercWccX'])						 ,'CHECK'=>'NO'),
															 /*** Cantidad enviada a FE ***/
															 array('NAME'=>'serlineg','VALUE'=>trim($cLineaNeg)						 			  	 ,'CHECK'=>'NO'), // Linea de Negocio
										    		   array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK'=>'SI'),
					 									   array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')    										 ,'CHECK'=>'SI'),
					 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')    		                 ,'CHECK'=>'SI'),
														   array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))   ,'CHECK'=>'SI'),
                               array('NAME'=>'seridxxx','VALUE'=>trim(strtoupper($_POST['cSerId']))    ,'CHECK'=>'WH'));

						if (f_MySql("UPDATE","fpar0129",$cInsertTab,$xConexion01,$cAlfa)) {
							/***** Grabo Bien *****/
							$nSwitch = 0;
						} else {
							$nSwitch = 1;
							$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "Error al Actualizar el Registro.\n";
						}
				}
      break;
      /*****************************   UPDATE    ***********************************************/
      case "ANULAR":
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Para [ANULAR] Un Concepto de Cobro, por favor Comunicarse con openTecnologia.";
      break;
    }
  }

  if ($nSwitch == 1) {
  	f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique");
  }

 	if ($nSwitch == 0) {
 	  if($_COOKIE['kModo']!="ANULAR"){
 		  f_Mensaje(__FILE__,__LINE__,"El Registro se cargo con Exito");
 	  }
 		?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
			<script languaje = "javascript">
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
				document.forms['frgrm'].submit()
			</script>
  	<?php
 	}
?>

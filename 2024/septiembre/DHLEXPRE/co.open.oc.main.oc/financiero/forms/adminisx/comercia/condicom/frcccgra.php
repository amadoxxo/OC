<?php
/**
 * Graba Cuentas Corrientes.
 * Este programa permite Guardar en la tabla Cuentas Corrientes.
 * @author
 * @package emisioncero
 */
	include("../../../../libs/php/utility.php");
	include("../../../../../libs/php/utiguops.php");

	$nSwitch = 0; // Switch para Vericar la Validacion de Datos
	$cCadErr = "";

	switch ($_COOKIE['kModo']) {
	  case "NUEVO":
	  case "EDITAR":
	  	/***** Validando Cliente *****/
  		if ($_POST['cCliId'] == "") {
  		  $nSwitch = 1;
  		  $cCadErr .= " El Nit del Cliente no puede ser vacio, \n";
  		}

  		/***** Validando que el Cliente Exista *****/
	  	$qCccCli  = "SELECT * ";
      $qCccCli .= "FROM $cAlfa.SIAI0150 WHERE CLIIDXXX = \"{$_POST['cCliId']}\" AND REGESTXX = \"ACTIVO\" LIMIT 0,1";
  	  $xCccCli = f_MySql("SELECT","",$qCccCli,$xConexion01,"");
      //$vSucCod  = mysql_fetch_array($xSucCod);
      $nCccCli  = mysql_num_rows($xCccCli);
      if ($nCccCli == 0) {
  		  $nSwitch = 1;
  		  $cCadErr .= " El Cliente ({$_POST['cCliId']}-{$_POST['cCliDV']} | {$_POST['cCliNom']}) no existe o se encuentra en estado [INACTIVO], \n";
  		}

	  	if ($_COOKIE['kModo'] == "NUEVO") {
	  	  /***** Validando Condicion Comercial Para el Cliente no exista *****/
		  	$qConCom  = "SELECT cliidxxx ";
	      $qConCom .= "FROM $cAlfa.fpar0151 WHERE cliidxxx = \"{$_POST['cCliId']}\"  LIMIT 0,1";
	  	  $xConCom = f_MySql("SELECT","",$qConCom,$xConexion01,"");
	      //$vSucCod  = mysql_fetch_array($xSucCod);
	      $nConCom  = mysql_num_rows($xConCom);
	      if ($nConCom > 0) {
	  		  $nSwitch = 1;
	  		  $cCadErr .= " La Condicion Comercial Para el Cliente ({$_POST['cCliId']}-{$_POST['cCliDV']} | {$_POST['cCliNom']}) ya existe, \n";
	  		}
	  	}

	  	//Validando si escogio grupo de tarifas que el cliente no tenga tarifas en el sistema
	  	$_POST['cGtaId'] = trim($_POST['cGtaId']);
	  	if ($_POST['cGtaId'] != "") {
	  	  $qTarCli  = "SELECT cliidxxx ";
        $qTarCli .= "FROM $cAlfa.fpar0131 WHERE cliidxxx = \"{$_POST['cCliId']}\" AND regestxx = \"ACTIVO\" LIMIT 0,1";
        $xTarCli = f_MySql("SELECT","",$qTarCli,$xConexion01,"");
        if (mysql_num_rows($xTarCli) > 0) {
          $nSwitch = 1;
          $cCadErr .= " No Puede Seleccionar Grupo de Tarifas, el Cliente Tiene Tarifas Activas en el Sistema, \n";
        }
	  	}

	  	##Si se escoge SI en Cobro Formularios Virtuales##
	  	if($_POST['cCccCfv'] == "SI"){
	  		if($_POST['cCccCfvv'] == ""){
	  			$nSwitch = 1;
	  		  $cCadErr .= " Usted escogio SI Cobro Forms Virtuales, debe digitar el valor del Formulario, Verifique \n";
	  		}elseif($_POST['cCccCfvv'] <= 0){
	  			$nSwitch = 1;
	  		  $cCadErr .= "El Valor del Formulario Virtual, debe ser mayor a cero, Verifique \n";
	  		}
	  	}else{
	  		$_POST['cCccCfvv'] = "";
	  	}
	  	##Fin Si se escoge SI en Cobro Formularios Virtuales##

	  	##Si se escoge SI en Cobro Formularios DAV Magneticas, se debe obligar a digitar el valor del formulario por Cliente##
	  	if($_POST['cCccFdm'] == "SI"){
	  		if($_POST['cCccFdmv'] == ""){
	  			$nSwitch = 1;
	  		  $cCadErr .= " Usted escogio SI Cobro Forms DAV Magneticas, debe digitar el valor del Formulario, Verifique \n";
	  		}elseif($_POST['cCccFdmv'] <= 0){
	  			$nSwitch = 1;
	  		  $cCadErr .= "El Valor del Formulario DAV Magneticas, debe ser mayor a cero, Verifique \n";
	  		}
	  	}else{
	  		$_POST['cCccFdmv'] = "";
	  	}
	  	##Fin Si se escoge SI en Cobro Formularios DAV Magneticas, se debe obligar a digitar el valor del formulario por Cliente##

	  	##Si se escoge SI en Cobro Formularios Virtuales Exportacion, se debe obligar a digitar el valor del formulario por Cliente##
	  	if($_POST['cCccFve'] == "SI"){
	  		if($_POST['cCccFvev'] == ""){
	  			$nSwitch = 1;
	  		  $cCadErr .= " Usted escogio SI Cobro Forms Virtuales Exportacion, debe digitar el valor del Formulario, Verifique \n";
	  		}elseif($_POST['cCccFvev'] <= 0){
	  			$nSwitch = 1;
	  		  $cCadErr .= "El valor del Formulario Virtual Exportacion debe ser mayor a cero, Verifique \n";
	  		}
	  	}else{
	  		$_POST['cCccFvev'] = "";
	  	}
	  	##Fin Si se escoge SI en Cobro Virtuales Exportacion, se debe obligar a digitar el valor del formulario por Cliente##

	  	##Si se escoge SI en Cobro Formularios Virtuales Exportacion Hoja Adicional, se debe obligar a digitar el valor del formulario por Cliente##
	  	if($_POST['cCccFvha'] == "SI"){
	  		if($_POST['cCccFvhav'] == ""){
	  			$nSwitch = 1;
	  		  $cCadErr .= " Usted escogio SI Cobro Forms Virtuales Exportacion Hoja Adicional, debe digitar el valor del Formulario, Verifique \n";
	  		}elseif($_POST['cCccFvhav'] <= 0){
	  			$nSwitch = 1;
	  		  $cCadErr .= " El Valor del Formulario Virtual Exportacion Hoja Adicional debe ser mayor a cero, Verifique \n";
	  		}
	  	}else{
	  		$_POST['cCccFvhav'] = "";
	  	}
	  	##Fin Si se escoge SI en Cobro Virtuales Exportacion, se debe obligar a digitar el valor del formulario por Cliente##

	  	// Si el campo vienen informado lo valido sino lo guardo vacio.
	  	if($_POST['cCccNmfmd'] != ""){
	  		if (!ctype_digit($_POST['cCccNmfmd'])) {
	  			$nSwitch = 1;
	  		  $cCadErr .= " Numero Mensual De Facturas Cobro Manejo Documental debe ser numerico, Verifique \n";
				}
			}

			//Impuestos sobre IF
			if ($_POST['cCccIfa'] == "NO") {
				$_POST['cCccIfv']   = "";
				$_POST['cCccRfIf']  = "NO";
				$_POST['cCccArfIf'] = "NO";
				$_POST['cCccRiIf']  = "NO";
				$_POST['cCccAriIf'] = "NO";
			}

      //Do Schenker, aplica para SIACO
      $cSchenker = "";
      if ($_POST['oChkSheImp'] != "") {
        $cSchenker .= "{$_POST['oChkSheImp']}~";
      }
      if ($_POST['oChkSheExp'] != "") {
        $cSchenker .= "{$_POST['oChkSheExp']}~";
      }
      if ($_POST['oChkSheDta'] != "") {
        $cSchenker .= "{$_POST['oChkSheDta']}~";
      }
      if ($_POST['oChkSheOtr'] != "") {
        $cSchenker .= "{$_POST['oChkSheOtr']}~";
      }
      $cSchenker = trim($cSchenker,"~");

      //Si aplica facturacion automatica
			if($_POST['cAplica'] == "SI") {
				if($_POST['cGenFacP'] == ""){
					$nSwitch = 1;
					$cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cCadErr .= "Debe seleccionar Generar Factura PCC.\n";
				}
				if($_POST['cTipFacT'] == ""){
					$nSwitch = 1;
					$cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cCadErr .= "Debe seleccionar el Tipo de Factura.\n";
				}
        if ($cAlfa=="SIACOSIA" || $cAlfa=="DESIACOSIP" || $cAlfa=="TESIACOSIP") {
          if($_POST['cForImp'] == ""){
            $nSwitch = 1;
            $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cCadErr .= "Debe seleccionar el Formato de Impresion.\n";
          }
        }
				if($_POST['cFacPor'] == ""){
					$nSwitch = 1;
					$cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cCadErr .= "Debe seleccionar Facturar Por.\n";
				}
        //Validando el nit facturar a
        if ($_POST['cCliId2'] != "") {
          $mNitsFacA = explode("~", $_POST['cFacA']);
          if(!in_array($_POST['cCliId2'], $mNitsFacA)){
            $nSwitch = 1;
            $cCadErr .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
            $cCadErr .= "El Nit {$_POST['cCliId2']} de Facturar A, debe Coincidir con el Nit de A Quien Va Dirigida la Factura.\n";
          }
        }
				if($_POST['cComFpag'] == ""){
					$nSwitch = 1;
					$cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cCadErr .= "Debe seleccionar la Forma de Pago.\n";
				}

        if ($_POST['cComFpag'] == "1") {
          if ($_POST['cMePagId'] == "") {
            $nSwitch = 1;
            $cCadErr .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
            $cCadErr .= "Debe Selecionar el Medio de Pago.\n";
          }
        }
        if ($_POST['cMePagId'] != "") {
          //Validando que el medio de pago exista
          $qMedPag  = "SELECT ";
          $qMedPag .= "mpaidxxx, ";
          $qMedPag .= "mpadesxx, ";
          $qMedPag .= "regestxx ";
          $qMedPag .= "FROM $cAlfa.fpar0155 ";
          $qMedPag .= "WHERE ";
          $qMedPag .= "mpaidxxx = \"{$_POST['cMePagId']}\" LIMIT 0,1";
          $xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qMedPag."~ ".mysql_num_rows($xMedPag));
          if (mysql_num_rows($xMedPag) == 0) {
            $nSwitch = 1;
            $cCadErr .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
            $cCadErr .= "El Medio de Pago seleccionado no Existe.\n";
          }
        }
				//Validando los correos
        $_POST['cCorNotI'] = trim($_POST['cCorNotI'],",");
        $vCorreos = explode(",", $_POST['cCorNotI']);
        for ($i=0; $i < count($vCorreos); $i++) { 
          $vCorreos[$i] = trim($vCorreos[$i]);
          if($vCorreos[$i] != ""){
            if (!filter_var($vCorreos[$i], FILTER_VALIDATE_EMAIL)) {
              $nSwitch = 1;
              $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cCadErr .= " El Correo Notificacion para Facturacion Automatica [".$vCorreos[$i]."], No es Valido.\n";
            }
          }
        }

        if($_POST['cFacPor'] == "TRAMITES_APROBADOS"){
          // Si la opcion es TRAMITES_APROBADOS se debe obligar a seleccionar la frecuencia de ejecucion
          if (count($_POST['cHoras']) == 0 || count($_POST['cDias']) == 0 || count($_POST['cMes']) == 0 || count($_POST['cDiaSemA']) == 0) {
            $nSwitch = 1;
            $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cCadErr .= "Debe seleccionar La Frecuencia de Ejecucion (Horas, Dias, Meses y Dias de la Semana).\n";
          }
        }

        // Validando las ciudades de facturacion
        $vSucursales = array(); $cComDes = "";
        for ($i=0;$i<$_POST['nSecuencia_Grid_CiuFac'];$i++) {
          if ($_POST['cSucId'.($i+1)]  == "" || $_POST['cUsrId'.($i+1)]     == "" || $_POST['cComDes'.($i+1)] == "" ||
              $_POST['cUsrNom'.($i+1)] == "" || $_POST['cComDesNom'.($i+1)] == "") {
            $nSwitch = 1;
            $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cCadErr .= "En la Secuencia [".($i+1)."] Debe Seleccionar Sucursal, Usuario Facturador y Ciudad de Facturacion.\n";
          } else {
            // Ciudades de Facturacion
            $cComDes .= "{$_POST['cSucId'.($i+1)]}^";
            $cComDes .= "{$_POST['cUsrId'.($i+1)]}^";
            $cComDes .= "{$_POST['cComDes'.($i+1)]}^";
            $cComDes .= "{$_POST['cComDesNom'.($i+1)]}|";
          }
          $vSucursales[$_POST['cSucId'.($i+1)]]++;
        }
        foreach ($vSucursales as $cKey => $cValue) {
          if ($vSucursales[$cKey] > 1) {
            $nSwitch = 1;
            $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cCadErr .= "En la Seccion de Ciudad Facturacion por Sucural la Sucursal [$cKey] Se Encuentra Repetida.\n";
          }
        }
        //Limpiando el ultimo pipeline (|)
        $cComDes = trim($cComDes,"|");
        if ($cComDes == "") {
          $nSwitch = 1;
          $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cCadErr .= "Debe Seleccionar la Ciudad Facturacion por Sucursal.\n";
        }
			} else {
        $_POST['cAplica']  = "";
        $_POST['cGenFacP'] = "";
				$_POST['cComDes']  = "";
        $_POST['cTipFacT'] = "";
				$_POST['cForImp']  = "";
				$_POST['cFacPor']  = "";
				$_POST['cCliId2']  = "";
				$_POST['cComFpag'] = "";
				$_POST['cMePagId'] = "";
        $_POST['cCorNotI'] = "";
				$_POST['cHoras']   = "";
				$_POST['cDias']    = "";
				$_POST['cMes']     = "";
				$_POST['cDiaSemA'] = "";
      }

			$cDescuen = "";
			// Valida que solo pueda seleccionar un codigo de descuento por servicio y forma de cobro
			if ($cAlfa == "DHLEXPRE" || $cAlfa == "DEDHLEXPRE" || $cAlfa == "TEDHLEXPRE") {
				$mCodigos = explode("|",$_POST['cDescuen']);
      	$mDescRep = array();
      	for($i=0;$i<count($mCodigos);$i++){
					if ($mCodigos[$i] != "") {
						$vServicio = explode("~", $mCodigos[$i]);
						if (!in_array($vServicio[0]."~".$vServicio[1], $mDescRep)) {
							$cDescuen .= $mCodigos[$i]."|";
							$mDescRep[count($mDescRep)] = $vServicio[0]."~".$vServicio[1];
						} else {
							$nSwitch = 1;
							$cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
							$cCadErr .= "Solo Puede Seleccionar un Codigo de Descuento por Servicio [".$vServicio[0]."] y Forma de Cobro [".$vServicio[1]."].\n";
						}
					}
      	}
			}

      $cConceptos = "";
			if ($cAlfa == "ALADUANA" || $cAlfa == "DEALADUANA" || $cAlfa == "TEALADUANA") {
        // Validando los conceptos pagos a terceros
        $vConceptos = array();
        for ($i=0;$i<$_POST['nSecuencia_Grid_ValorConcepto'];$i++) {
          if ($_POST['cCtoId'.($i+1)] == "" && $_POST['cUmeId'.($i+1)] == "" && $_POST['cVlrUnit'.($i+1)] == "") {
            continue;
          } else if ($_POST['cCtoId'.($i+1)] != "" && ($_POST['cUmeId'.($i+1)] == "" || $_POST['cVlrUnit'.($i+1)] == "")) {
            $nSwitch = 1;
            $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cCadErr .= "En la Secuencia [".($i+1)."] Debe Seleccionar Unidad Medida y Valor Unitario.\n";
          } else if ($_POST['cCtoId'.($i+1)] == "" && ($_POST['cUmeId'.($i+1)] != "" || $_POST['cVlrUnit'.($i+1)] != "")) {
            $nSwitch = 1;
            $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cCadErr .= "En la Secuencia [".($i+1)."] Debe Seleccionar El Concepto.\n";
          } else if($_POST['cCtoId'.($i+1)] != "" && $_POST['cUmeId'.($i+1)] != "" && $_POST['cVlrUnit'.($i+1)] != "") {
            // Concepto,unidad medida y valor unitario
            $cConceptos .= "{$_POST['cCtoId'.($i+1)]}~";
            $cConceptos .= "{$_POST['cUmeId'.($i+1)]}~";
            $cConceptos .= "{$_POST['cVlrUnit'.($i+1)]}|";
          }
          $vConceptos[$_POST['cCtoId'.($i+1)]]++;
        }
        foreach ($vConceptos as $cKey => $cValue) {
          if ($vConceptos[$cKey] > 1) {
            $nSwitch = 1;
            $cCadErr .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cCadErr .= "En la Seccion de Valores Unitarios Conceptos Pagos a Terceros el Concepto [$cKey] Se Encuentra Repetido.\n";
          }
        }
        //Limpiando el ultimo pipeline (|)
        $cConceptos = trim($cConceptos,"|");
      }
    break;
	  case "ANULAR":
  		$qDatCcc  = "SELECT $cAlfa.fpar0151.cccggesx, regestxx ";
  		$qDatCcc .= "FROM $cAlfa.fpar0151 ";
  		$qDatCcc .= "WHERE $cAlfa.fpar0151.cliidxxx = \"{$_POST['cCliId']}\" LIMIT 0,1";
  		$xDatCcc = f_MySql("SELECT","",$qDatCcc,$xConexion01,"");
  		if (mysql_num_rows($xDatCcc) == 1) {
  			$xRDC = mysql_fetch_array($xDatCcc);
  		} else {
  			$nSwitch = 1;
  			$cCadErr .= " El Cliente No Existe, Verifique \n";
  		}
  		//f_Mensaje(__FILE__,__LINE__,$qDatCcc);
	  break;
	}	/***** Fin de la Validacion *****/

	/***** Ahora Empiezo a Grabar *****/
	/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
	if ($nSwitch == 0) {
		switch ($_COOKIE['kModo']) {
			case "NUEVO":
  	    /**
  	     * Insert en la Tabla.
  	     */
        $cInsertTab	 = array(array('NAME'=>'cliidxxx','VALUE'=>trim(strtoupper($_POST['cCliId']))   ,'CHECK'=>'SI'),
				                     array('NAME'=>'cccplaxx','VALUE'=>trim(strtoupper($_POST['cCccPla']))  ,'CHECK'=>'NO'),
				                     array('NAME'=>'cccplaip','VALUE'=>trim(strtoupper($_POST['cCccPlaIp'])),'CHECK'=>'NO'),
				                     array('NAME'=>'cccantxx','VALUE'=>trim(strtoupper($_POST['cCccAnt']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'cccifaxx','VALUE'=>trim(strtoupper($_POST['cCccIfa']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'cccifvxx','VALUE'=>trim(strtoupper($_POST['cCccIfv']))	,'CHECK'=>'NO'),
														 array('NAME'=>'cccrfifx','VALUE'=>trim(strtoupper($_POST['cCccRfIf']))	,'CHECK'=>'NO'),
														 array('NAME'=>'cccarfif','VALUE'=>trim(strtoupper($_POST['cCccArfIf'])),'CHECK'=>'NO'),
														 array('NAME'=>'cccriifx','VALUE'=>trim(strtoupper($_POST['cCccRiIf']))	,'CHECK'=>'NO'),
														 array('NAME'=>'cccariif','VALUE'=>trim(strtoupper($_POST['cCccAriIf'])),'CHECK'=>'NO'),
				                     array('NAME'=>'cccnmfmd','VALUE'=>trim(strtoupper($_POST['cCccNmfmd'])),'CHECK'=>'NO'),
				                     array('NAME'=>'cccfdcxx','VALUE'=>trim(strtoupper($_POST['dCccFdc']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfhcxx','VALUE'=>trim(strtoupper($_POST['dCccFhc']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'ccccotxx','VALUE'=>trim(strtoupper($_POST['cCccCot']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'ccccopfa','VALUE'=>trim(strtoupper($_POST['cCccCop']))	,'CHECK'=>'NO'),// para num facturas
				                     array('NAME'=>'cccaplfa','VALUE'=>trim(strtoupper($_POST['cAplFacA'])) ,'CHECK'=>'NO'),
                             array('NAME'=>'cccplafa','VALUE'=>trim(strtoupper($_POST['cPlaFacA'])) ,'CHECK'=>'NO'),
				                     array('NAME'=>'cccdfaxx','VALUE'=>trim(strtoupper($_POST['cCccDfa']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'cccsurxx','VALUE'=>trim(strtoupper($_POST['cCccSur']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'cccdetxx','VALUE'=>trim(strtoupper($_POST['cCccDet']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'cccconxx','VALUE'=>trim(strtoupper($_POST['cCccCon']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'cccdirxx','VALUE'=>trim(strtoupper($_POST['cCccDir']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'cccimpxx','VALUE'=>trim(strtoupper($_POST['cCccImp']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'ccccfvxx','VALUE'=>trim(strtoupper($_POST['cCccCfv']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'ccccfvvx','VALUE'=>trim(strtoupper($_POST['cCccCfvv']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfdmxx','VALUE'=>trim(strtoupper($_POST['cCccFdm']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfdmvx','VALUE'=>trim(strtoupper($_POST['cCccFdmv']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfvexx','VALUE'=>trim(strtoupper($_POST['cCccFve']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfvevx','VALUE'=>trim(strtoupper($_POST['cCccFvev']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfvhax','VALUE'=>trim(strtoupper($_POST['cCccFvha']))	,'CHECK'=>'NO'),
				                     array('NAME'=>'gtaidxxx','VALUE'=>trim(strtoupper($_POST['cGtaId']))   ,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfvhav','VALUE'=>trim(strtoupper($_POST['cCccFvhav'])),'CHECK'=>'NO'),
				                     array('NAME'=>'cccintxx','VALUE'=>trim(strtoupper($_POST['cFacA']))    ,'CHECK'=>'NO'),
				                     array('NAME'=>'cccexcpt','VALUE'=>trim(strtoupper($_POST['cExcPt']))   ,'CHECK'=>'NO'), // Pagos a Terceros Exluidos en Facturacion
				                     array('NAME'=>'cccggesx','VALUE'=>trim(strtoupper($_POST['cGruGesId'])),'CHECK'=>'NO'), //Grupos de Gestion
				                     array('NAME'=>'cccimpro','VALUE'=>trim(strtoupper($_POST['cCccImpRo'])),'CHECK'=>'NO'),
				                     array('NAME'=>'ccccdant','VALUE'=>trim(strtoupper($_POST['cCccCdAnt'])),'CHECK'=>'NO'),
				                     array('NAME'=>'cccschek','VALUE'=>$cSchenker														,'CHECK'=>'NO'),
				                     array('NAME'=>'cccimpus','VALUE'=>trim(strtoupper($_POST['oChkImpUS'])),'CHECK'=>'NO'),
				                     array('NAME'=>'ccctrans','VALUE'=>trim(strtoupper($_POST['oChkTrans'])),'CHECK'=>'NO'),
				                     array('NAME'=>'cccafaxx','VALUE'=>trim(strtoupper($_POST['cAplica']))  ,'CHECK'=>'NO'),
				                     array('NAME'=>'cccgfacx','VALUE'=>trim(strtoupper($_POST['cGenFacP'])) ,'CHECK'=>'NO'),
				                     array('NAME'=>'ccccfacx','VALUE'=>$cComDes                             ,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfacax','VALUE'=>trim(strtoupper($_POST['cCliId2']))  ,'CHECK'=>'NO'),
                             array('NAME'=>'cccuftfe','VALUE'=>trim(strtoupper($_POST['cComTdoc'])) ,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfpagx','VALUE'=>trim(strtoupper($_POST['cComFpag'])) ,'CHECK'=>'NO'),
				                     array('NAME'=>'cccmpagx','VALUE'=>trim(strtoupper($_POST['cMePagId'])) ,'CHECK'=>'NO'),
				                     array('NAME'=>'ccctfacx','VALUE'=>trim(strtoupper($_POST['cTipFacT'])) ,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfimpx','VALUE'=>trim(strtoupper($_POST['cForImp']))  ,'CHECK'=>'NO'),
                             array('NAME'=>'cccmonfa','VALUE'=>trim(strtoupper($_POST['cMonId']))   ,'CHECK'=>'NO'),
                             array('NAME'=>'cccobsfa','VALUE'=>trim(strtoupper($_POST['cComObs']))  ,'CHECK'=>'NO'),
				                     array('NAME'=>'ccccnotx','VALUE'=>trim($_POST['cCorNotI'])             ,'CHECK'=>'NO','CS'=>'NONE'),
				                     array('NAME'=>'cccfacpo','VALUE'=>trim(strtoupper($_POST['cFacPor']))  ,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfehor','VALUE'=>implode(',',$_POST['cHoras'])        ,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfedmx','VALUE'=>implode(',',$_POST['cDias'])         ,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfemes','VALUE'=>implode(',',$_POST['cMes'])          ,'CHECK'=>'NO'),
				                     array('NAME'=>'cccfedsx','VALUE'=>implode(',',$_POST['cDiaSemA'])      ,'CHECK'=>'NO'),
														 array('NAME'=>'cccdescu','VALUE'=>trim(strtoupper($cDescuen))					,'CHECK'=>'NO'),
														 array('NAME'=>'cccagrta','VALUE'=>trim(strtoupper($_POST['oCccAgrTa'])),'CHECK'=>'NO'),
                             array('NAME'=>'cccvlcto','VALUE'=>$cConceptos                          ,'CHECK'=>'NO'), // Valores Unitarios Conceptos Pagos a Terceros
										    		 array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
					 									 array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									 array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
														 array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))  ,'CHECK'=>'SI'));

				if (f_MySql("INSERT","fpar0151",$cInsertTab,$xConexion01,$cAlfa)) {
					/**
					 * Si adiciono grupos de gestion se debe actualizar la tabla de usuarios de openSMART para ese Cliente
					 */
					if ($vSysStr['opensmart_activar_modulo'] == "SI") {
						//Actualizando usuarios de openSMART en los clientes que tienen asociado el grupo
						$vDatos = array();
						$vDatos['cliidxxx'] = $_POST['cCliId'];			//Nit Cliente
						$vDatos['grugesid'] = "";										//Codigo Grupo de Gestion, si viene vacio se asume que son todos los grupos asosicados a un cliente
						$vDatos['regusrxx'] = $_COOKIE['kUsrId']; 	//Usuaro que Crea el registro
						$vDatos['conexion'] = $xConexion01;					//Conexion BD
						$vDatos['datebase'] = $cAlfa;								//Base de Datos

						#Creando Objeto Usuario OpenSMART
						$ObjUserOpenSmart = new cUsuariosOpenSmat();
						$mRetorna = $ObjUserOpenSmart->fnAsociarGrupoGesaClientes($vDatos);
						if ($mRetorna[0] == "false") {
							for ($nR=1; $nR<count($mRetorna); $nR++) {
								$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
								$cMsj .= $mRetorna[$nR]."\n";
							}
							f_Mensaje(__FILE__,__LINE__,$cMsj);
						}
					}
				} else {
					$nSwitch = 1;
					f_Mensaje(__FILE__,__LINE__,"Error Guardando Datos de la Cuenta Corriente, Verifique");
				}
			break;
			/*****************************   UPDATE    ***********************************************/
			case "EDITAR":

				/***** Validaciones Particulares *****/
				/* Validado El Estado del Registro */
				if (!f_InList($_POST['cEstado'],"ACTIVO","INACTIVO")) {
					$nSwitch = 1;
					f_Mensaje(__FILE__,__LINE__,"El Estado del Registro No es Correcto, Verifique");
				}
				/***** Fin de Validaciones Particulares *****/
				if ($nSwitch == 0) {
					$cInsertTab	 = array(array('NAME'=>'cccplaxx','VALUE'=>trim(strtoupper($_POST['cCccPla']))  ,'CHECK'=>'NO'),
					                     array('NAME'=>'cccplaip','VALUE'=>trim(strtoupper($_POST['cCccPlaIp'])),'CHECK'=>'NO'),
  				                     array('NAME'=>'cccantxx','VALUE'=>trim(strtoupper($_POST['cCccAnt']))	,'CHECK'=>'NO'),
  				                     array('NAME'=>'cccifaxx','VALUE'=>trim(strtoupper($_POST['cCccIfa']))	,'CHECK'=>'NO'),
  				                     array('NAME'=>'cccifvxx','VALUE'=>trim(strtoupper($_POST['cCccIfv']))	,'CHECK'=>'NO'),
															 array('NAME'=>'cccrfifx','VALUE'=>trim(strtoupper($_POST['cCccRfIf']))	,'CHECK'=>'NO'),
															 array('NAME'=>'cccarfif','VALUE'=>trim(strtoupper($_POST['cCccArfIf'])),'CHECK'=>'NO'),
															 array('NAME'=>'cccriifx','VALUE'=>trim(strtoupper($_POST['cCccRiIf']))	,'CHECK'=>'NO'),
															 array('NAME'=>'cccariif','VALUE'=>trim(strtoupper($_POST['cCccAriIf'])),'CHECK'=>'NO'),
  				                     array('NAME'=>'cccnmfmd','VALUE'=>trim(strtoupper($_POST['cCccNmfmd'])),'CHECK'=>'NO'),
  				                     array('NAME'=>'cccfdcxx','VALUE'=>trim(strtoupper($_POST['dCccFdc']))	,'CHECK'=>'NO'),
  				                     array('NAME'=>'cccfhcxx','VALUE'=>trim(strtoupper($_POST['dCccFhc']))	,'CHECK'=>'NO'),
  				                     array('NAME'=>'ccccotxx','VALUE'=>trim(strtoupper($_POST['cCccCot']))	,'CHECK'=>'NO'),
  				                     array('NAME'=>'ccccopfa','VALUE'=>trim(strtoupper($_POST['cCccCop']))	,'CHECK'=>'NO'),// campo num facturas
  				                     array('NAME'=>'cccaplfa','VALUE'=>trim(strtoupper($_POST['cAplFacA'])) ,'CHECK'=>'NO'),
                               array('NAME'=>'cccplafa','VALUE'=>trim(strtoupper($_POST['cPlaFacA'])) ,'CHECK'=>'NO'),
  				                     array('NAME'=>'cccdfaxx','VALUE'=>trim(strtoupper($_POST['cCccDfa']))	,'CHECK'=>'NO'),
  				                     array('NAME'=>'cccsurxx','VALUE'=>trim(strtoupper($_POST['cCccSur']))	,'CHECK'=>'NO'),
  				                     array('NAME'=>'cccdetxx','VALUE'=>trim(strtoupper($_POST['cCccDet']))	,'CHECK'=>'NO'),
  				                     array('NAME'=>'cccconxx','VALUE'=>trim(strtoupper($_POST['cCccCon']))	,'CHECK'=>'NO'),
  				                     array('NAME'=>'cccdirxx','VALUE'=>trim(strtoupper($_POST['cCccDir']))	,'CHECK'=>'NO'),
  				                     array('NAME'=>'cccimpxx','VALUE'=>trim(strtoupper($_POST['cCccImp']))	,'CHECK'=>'NO'),
  				                     array('NAME'=>'ccccfvxx','VALUE'=>trim(strtoupper($_POST['cCccCfv']))	,'CHECK'=>'NO'),
  				                     array('NAME'=>'ccccfvvx','VALUE'=>trim(strtoupper($_POST['cCccCfvv']))	,'CHECK'=>'NO'),
  				                     array('NAME'=>'cccfdmxx','VALUE'=>trim(strtoupper($_POST['cCccFdm']))	,'CHECK'=>'NO'),
				                     	 array('NAME'=>'cccfdmvx','VALUE'=>trim(strtoupper($_POST['cCccFdmv']))	,'CHECK'=>'NO'),
				                     	 array('NAME'=>'cccfvexx','VALUE'=>trim(strtoupper($_POST['cCccFve']))	,'CHECK'=>'NO'),
				                     	 array('NAME'=>'cccfvevx','VALUE'=>trim(strtoupper($_POST['cCccFvev']))	,'CHECK'=>'NO'),
				                     	 array('NAME'=>'cccfvhax','VALUE'=>trim(strtoupper($_POST['cCccFvha']))	,'CHECK'=>'NO'),
				                     	 array('NAME'=>'cccfvhav','VALUE'=>trim(strtoupper($_POST['cCccFvhav'])),'CHECK'=>'NO'),
				                       array('NAME'=>'gtaidxxx','VALUE'=>trim(strtoupper($_POST['cGtaId']))   ,'CHECK'=>'NO'),
				                       array('NAME'=>'cccintxx','VALUE'=>trim(strtoupper($_POST['cFacA']))    ,'CHECK'=>'NO'),
				                       array('NAME'=>'cccexcpt','VALUE'=>trim(strtoupper($_POST['cExcPt']))   ,'CHECK'=>'NO'), // Pagos a Terceros Exluidos en Facturacion
															 array('NAME'=>'cccggesx','VALUE'=>trim(strtoupper($_POST['cGruGesId'])),'CHECK'=>'NO'), //Grupos de Gestion
							                 array('NAME'=>'cccimpro','VALUE'=>trim(strtoupper($_POST['cCccImpRo'])),'CHECK'=>'NO'),
							                 array('NAME'=>'ccccdant','VALUE'=>trim(strtoupper($_POST['cCccCdAnt'])),'CHECK'=>'NO'),
							                 array('NAME'=>'cccschek','VALUE'=>$cSchenker														,'CHECK'=>'NO'),
							                 array('NAME'=>'cccimpus','VALUE'=>trim(strtoupper($_POST['oChkImpUS'])),'CHECK'=>'NO'),
							                 array('NAME'=>'ccctrans','VALUE'=>trim(strtoupper($_POST['oChkTrans'])),'CHECK'=>'NO'),
                               array('NAME'=>'cccafaxx','VALUE'=>trim(strtoupper($_POST['cAplica']))  ,'CHECK'=>'NO'),
                               array('NAME'=>'cccgfacx','VALUE'=>trim(strtoupper($_POST['cGenFacP'])) ,'CHECK'=>'NO'),
                               array('NAME'=>'ccccfacx','VALUE'=>$cComDes                             ,'CHECK'=>'NO'),
                               array('NAME'=>'cccfacax','VALUE'=>trim(strtoupper($_POST['cCliId2']))  ,'CHECK'=>'NO'),
                               array('NAME'=>'cccuftfe','VALUE'=>trim(strtoupper($_POST['cComTdoc'])) ,'CHECK'=>'NO'),
                               array('NAME'=>'cccfpagx','VALUE'=>trim(strtoupper($_POST['cComFpag'])) ,'CHECK'=>'NO'),
                               array('NAME'=>'cccmpagx','VALUE'=>trim(strtoupper($_POST['cMePagId'])) ,'CHECK'=>'NO'),
                               array('NAME'=>'ccctfacx','VALUE'=>trim(strtoupper($_POST['cTipFacT'])) ,'CHECK'=>'NO'),
                               array('NAME'=>'cccfimpx','VALUE'=>trim(strtoupper($_POST['cForImp']))  ,'CHECK'=>'NO'),
                               array('NAME'=>'cccmonfa','VALUE'=>trim(strtoupper($_POST['cMonId']))   ,'CHECK'=>'NO'),
                               array('NAME'=>'cccobsfa','VALUE'=>trim(strtoupper($_POST['cComObs']))  ,'CHECK'=>'NO'),
                               array('NAME'=>'ccccnotx','VALUE'=>trim($_POST['cCorNotI'])             ,'CHECK'=>'NO','CS'=>'NONE'),
                               array('NAME'=>'cccfacpo','VALUE'=>trim(strtoupper($_POST['cFacPor']))  ,'CHECK'=>'NO'),
                               array('NAME'=>'cccfehor','VALUE'=>implode(',',$_POST['cHoras'])        ,'CHECK'=>'NO'),
                               array('NAME'=>'cccfedmx','VALUE'=>implode(',',$_POST['cDias'])         ,'CHECK'=>'NO'),
                               array('NAME'=>'cccfemes','VALUE'=>implode(',',$_POST['cMes'])          ,'CHECK'=>'NO'),
                               array('NAME'=>'cccfedsx','VALUE'=>implode(',',$_POST['cDiaSemA'])      ,'CHECK'=>'NO'),
                               array('NAME'=>'cccdescu','VALUE'=>trim(strtoupper($cDescuen)) 					,'CHECK'=>'NO'),
                               array('NAME'=>'cccagrta','VALUE'=>trim(strtoupper($_POST['oCccAgrTa'])),'CHECK'=>'NO'),
                               array('NAME'=>'cccvlcto','VALUE'=>$cConceptos                          ,'CHECK'=>'NO'), // Valores Unitarios Conceptos Pagos a Terceros
							                 array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									   array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
														   array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado']))  ,'CHECK'=>'SI'),
                               array('NAME'=>'cliidxxx','VALUE'=>trim(strtoupper($_POST['cCliId']))   ,'CHECK'=>'WH'));

						if (f_MySql("UPDATE","fpar0151",$cInsertTab,$xConexion01,$cAlfa)) {
							/***** Grabo Bien *****/
							/**
							 * Si adiciono grupos de gestion se debe actualizar la tabla de usuarios de openSMART para ese Cliente
							 */
							if ($vSysStr['opensmart_activar_modulo'] == "SI") {
								//Actualizando usuarios de openSMART en los clientes que tienen asociado el grupo
								$vDatos = array();
								$vDatos['cliidxxx'] = $_POST['cCliId'];			//Nit Cliente
								$vDatos['grugesid'] = "";										//Codigo Grupo de Gestion, si viene vacio se asume que son todos los grupos asosicados a un cliente
								$vDatos['regusrxx'] = $_COOKIE['kUsrId']; 	//Usuaro que Crea el registro
								$vDatos['conexion'] = $xConexion01;					//Conexion BD
								$vDatos['datebase'] = $cAlfa;								//Base de Datos

								#Creando Objeto Usuario OpenSMART
								$ObjUserOpenSmart = new cUsuariosOpenSmat();
								$mRetorna = $ObjUserOpenSmart->fnAsociarGrupoGesaClientes($vDatos);
								if ($mRetorna[0] == "false") {
									for ($nR=1; $nR<count($mRetorna); $nR++) {
										$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
										$cMsj .= $mRetorna[$nR]."\n";
									}
									f_Mensaje(__FILE__,__LINE__,$cMsj);
								}
							}
							$nSwitch = 0;
						} else {
							$nSwitch = 1;
							f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro");
						}
				}
      break;
      /*****************************   UPDATE    ***********************************************/
      case "ANULAR":
         if($_POST['cCliEst']=="ACTIVO"){
           $cEstado="INACTIVO";
         }
         if($_POST['cCliEst']=="INACTIVO"){
           $cEstado="ACTIVO";
         }

         $zInsertCab	 = array(array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])) ,'CHECK'=>'SI'),
					 									   array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
					 									   array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')												,'CHECK'=>'SI'),
														   array('NAME'=>'regestxx','VALUE'=>$cEstado                             ,'CHECK'=>'SI'),
                               array('NAME'=>'cliidxxx','VALUE'=>trim(strtoupper($_POST['cCliId']))   ,'CHECK'=>'WH'));

				 if (f_MySql("UPDATE","fpar0151",$zInsertCab,$xConexion01,$cAlfa)) {
					 /***** Grabo Bien *****/
					 	/**
					 	 * Si adiciono grupos de gestion se debe actualizar la tabla de usuarios de openSMART para ese Cliente
					 	 */
				 		if ($vSysStr['opensmart_activar_modulo'] == "SI") {
					 		//Actualizando usuarios de openSMART en los clientes que tienen asociado el grupo
					 		$vDatos = array();
					 		$vDatos['cliidxxx'] = trim(strtoupper($_POST['cCliId']));	//Nit Cliente
					 		$vDatos['grugesid'] = "";																	//Codigo Grupo de Gestion, si viene vacio se asume que son todos los grupos asosicados a un cliente
					 		$vDatos['regusrxx'] = $_COOKIE['kUsrId']; 								//Usuaro que Crea el registro
					 		$vDatos['conexion'] = $xConexion01;												//Conexion BD
					 		$vDatos['datebase'] = $cAlfa;															//Base de Datos

					 		#Creando Objeto Usuario OpenSMART
					 		$ObjUserOpenSmart = new cUsuariosOpenSmat();
					 		$mRetorna = $ObjUserOpenSmart->fnAsociarGrupoGesaClientes($vDatos);
					 		if ($mRetorna[0] == "false") {
					 			for ($nR=1; $nR<count($mRetorna); $nR++) {
						 			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
						 			$cMsj .= $mRetorna[$nR]."\n";
					 			}
					 			f_Mensaje(__FILE__,__LINE__,$cMsj);
					 		}
					 	}
				 } else {
					 $nSwitch = 1;
					 f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro");
				 }
      break;
    }
  } else {
  	f_Mensaje(__FILE__,__LINE__,"$cCadErr Verifique");
  }

 	if ($nSwitch == 0) {
 	  if($_COOKIE['kModo']!="ANULAR"){
 		  f_Mensaje(__FILE__,__LINE__,"El Registro se cargo con Exito");
 	  }
 	  if($_COOKIE['kModo']=="ANULAR"){
 	    f_Mensaje(__FILE__,__LINE__,"El Registro Cambio de Estado Con Exito");
 	  }
 		?>
		<form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
			<script languaje = "javascript">
  			parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
				document.forms['frgrm'].submit()
			</script>
  	<?php }
?>

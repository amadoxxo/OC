<?php
  namespace openComex;
/**
 * Traslado Movimiento DO
 * Este programa permite Trasladar los Movimiento de un Do a otro.
 * @author Julio Lopez <julio.lopez@opentecnologia.com.co>
 * @package openComex
 */

  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");

  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");
  include("../../../../libs/php/utimovdo.php");
  include("../../../../libs/php/uticonta.php");
  include("../../../../libs/php/utiajuxx.php");

  $nSwitch = 0; // Switch para Vericar la Validacion de Datos
  $cMsj    = "";
  $cMsjAdi = "";
  
  // Validando que el usuario haya dado un solo click en el boton guardar.
	if ($_POST['nTimesSave'] != 1) {
		$nSwitch = 1;
		$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
		$cMsj .= "El Sistema Detecto mas de un Click en el Boton Guardar.\n";
	}

	/**
	 * Primero valido los datos que llegan por metodo POST.
	 */
	switch ($_COOKIE['kModo']) {
    case "TRASLADAR":

      // Validado que exista el comprobante.
      $qValCom  = "SELECT * ";
      $qValCom .= "FROM $cAlfa.fpar0117 ";
      $qValCom .= "WHERE ";
      $qValCom .= "comidxxx = \"{$_POST['cComId']}\" AND ";
      $qValCom .= "comcodxx = \"{$_POST['cComCod']}\" AND ";
      $qValCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
      $xValCom  = f_MySql("SELECT","",$qValCom,$xConexion01,"");
      if (mysql_num_rows($xValCom) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Comprobante [{$_POST['cComId']}-{$_POST['cComCod']}] no esta Parametrizado.\n";
      } else {
        $vValCom  = mysql_fetch_assoc($xValCom);
      }

      //Validando observacion
      if ($_POST['cComObs'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Observacion Traslado No Puede Ser Vacia.\n";
      }
      
      // Validando que la Fecha del Comprobante no este Vacia.
      if ($_POST['dComFec'] == "" or $_POST['dComFec'] == "0000-00-00") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Fecha del Comprobante no puede ser vacia el formato es [AAAA-MM-DD].\n";
      }

      // Validado la Fecha de Vencimiento del Comprobante.
      if ($_POST['dComVen'] < $_POST['dComFec']) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Fecha de Vencimiento del Comprobante [{$_POST['dComVen']}] no Puede ser Menor a la Fecha del Comprobante [{$_POST['dComFec']}].\n";
      }

      // Valido que haya tasa de cambio si la calidad del "facturar a" es "NO RESIDE EN EL PAIS"
      if ($_POST['nTasaCambio'] == "" || $_POST['nTasaCambio'] <= 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Sistema no Detecto tasa de Cambio para el Comprobante.\n";
      }
      // Fin de Valido que haya tasa de cambio si la calidad del "facturar a" es "NO RESIDE EN EL PAIS"

	    /**
	     * Validando DO ORIGEN.
	     */
	    if (empty($_POST['cSucIdOri'])) {
	      $nSwitch = 1;
	      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      $cMsj .= "La Sucursal DO Origen No Puede Ser Vacia.\n";
	    }

			if (empty($_POST['cDocNroOri'])) {
	      $nSwitch = 1;
	      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      $cMsj .= "El DO Origen No Puede Ser Vacio.\n";
	    }

			if (empty($_POST['cDocSufOri'])) {
	      $nSwitch = 1;
	      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      $cMsj .= "El Sufijo DO Origen No Puede Ser Vacio.\n";
	    }

			/**
	     * Validando DO DESTINO.
	     */
	    if (empty($_POST['cSucIdDes'])) {
	      $nSwitch = 1;
	      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      $cMsj .= "La Sucursal DO Destino No Puede Ser Vacia.\n";
	    }

			if (empty($_POST['cDocNroDes'])) {
	      $nSwitch = 1;
	      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      $cMsj .= "El DO Destino No Puede Ser Vacio.\n";
	    }

			if (empty($_POST['cDocSufDes'])) {
	      $nSwitch = 1;
	      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	      $cMsj .= "El Sufijo DO Destino No Puede Ser Vacio.\n";
	    }

      if ($_POST['cSucIdOri']  == $_POST['cSucIdDes'] &&
          $_POST['cDocNroOri'] == $_POST['cDocNroDes'] &&
          $_POST['cDocSufOri'] == $_POST['cDocSufDes']) {
				$nSwitch = 1;
	      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	    	$cMsj .= "El DO Origen No puede ser igual al Numero DO Destino.\n";
      }
      
      /**
       * Array con los nombres de los terceros 
       */
      $vNomTer = array();
      $vNitTer = array();

      /**
        * I)
        * Validando que el número de DO Origen sea valido.
        * Buscar en la sys00121 la fecha de creación del DO y el nit del cliente del DO origen,
        * y desde el año anterior al año actual buscar en la fcod por los campos sucidxxx, docidxxx, docsufxx.
        * $vDocOri["cliidxxx"] => Nit del Cliente
        * $vDocOri["regfcrex"] => Fecha de Creación del DO
        */
			if(!empty($_POST['cDocNroOri'])){
      	$qDocOri  = "SELECT ";
	  		$qDocOri .= "sucidxxx, ";
				$qDocOri .= "docidxxx, ";
				$qDocOri .= "docsufxx, ";
        $qDocOri .= "cliidxxx, ";
        $qDocOri .= "doctipxx, ";
        $qDocOri .= "docobsac, ";        
      	$qDocOri .= "regestxx, ";
				$qDocOri .= "regfcrex	";
	  		$qDocOri .= "FROM $cAlfa.sys00121 ";
      	$qDocOri .= "WHERE ";
      	$qDocOri .= "sucidxxx = \"{$_POST['cSucIdOri']}\"  AND ";
      	$qDocOri .= "docidxxx = \"{$_POST['cDocNroOri']}\" AND ";
      	$qDocOri .= "docsufxx = \"{$_POST['cDocSufOri']}\" AND ";
	  		$qDocOri .= "regestxx = \"ACTIVO\" ORDER BY docidxxx";
				$xDocOri  = f_MySql("SELECT","",$qDocOri,$xConexion01,"");
				if(mysql_num_rows($xDocOri) > 0){
          $vDocOri = mysql_fetch_array($xDocOri);
          
          //Trayendo el nombre del cliente
          $qCliNom  = "SELECT ";
          $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
          $qCliNom .= "FROM $cAlfa.SIAI0150 ";
          $qCliNom .= "WHERE ";
          $qCliNom .= "CLIIDXXX = \"{$vDocOri['cliidxxx']}\" LIMIT 0,1";
          $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
          if (mysql_num_rows($xCliNom) > 0) {
            $vCliNom = mysql_fetch_array($xCliNom);
            $vDocOri['clinomxx'] = $vCliNom['clinomxx'];
            $vNomTer["{$vDocOri['cliidxxx']}"] = $vCliNom['clinomxx'];
            $vNitTer[count($vNitTer)] = "{$vDocOri['cliidxxx']}";
          }
				}else{
					$nSwitch = 1;
	    	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	    	  $cMsj .= "El DO Origen [".$_POST['cSucIdOri']."-".$_POST['cDocNroOri']."-".$_POST['cDocSufOri']."] No Existe o NO Se Encuentra en Estado ACTIVO .\n";
				}
			}

     /**
			* II)
	    * Validando que el número de DO Destino sea valido.
      * Buscar en la sys00121 el do destino y traer el cliente, el centro de costo y el subcentro de costo.
			* Campos a Seleccionar:
			* $vDocDes["cliidxxx"] => Cliente
			* $vDocDes["ccoidxxx"] => Centro de Costo
			* $vDocDes["sccidxxx"] => Sub Centro de Costo
			* $vDocDes["regfcrex"] => Sub Centro de Costo
	    */
			if(!empty($_POST['cDocNroDes'])){
      	$qDocDes  = "SELECT ";
      	$qDocDes .= "sucidxxx, ";
				$qDocDes .= "docidxxx, ";
        $qDocDes .= "docsufxx, ";
        $qDocDes .= "ccoidxxx, ";
				$qDocDes .= "cliidxxx, ";
      	$qDocDes .= "regestxx, ";
				$qDocDes .= "regfcrex	";
	  		$qDocDes .= "FROM $cAlfa.sys00121 ";
      	$qDocDes .= "WHERE ";
      	$qDocDes .= "sucidxxx = \"{$_POST['cSucIdDes']}\" AND ";
      	$qDocDes .= "docidxxx = \"{$_POST['cDocNroDes']}\" AND ";
      	$qDocDes .= "docsufxx = \"{$_POST['cDocSufDes']}\" AND ";
	  		$qDocDes .= "regestxx = \"ACTIVO\" ORDER BY docidxxx";
				$xDocDes  = f_MySql("SELECT","",$qDocDes,$xConexion01,"");
				if(mysql_num_rows($xDocDes) > 0){
          $vDocDes = mysql_fetch_array($xDocDes);
          
          //Trayendo el nombre del cliente
          $qCliNom  = "SELECT ";
          $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
          $qCliNom .= "FROM $cAlfa.SIAI0150 ";
          $qCliNom .= "WHERE ";
          $qCliNom .= "CLIIDXXX = \"{$vDocDes['cliidxxx']}\" LIMIT 0,1";
          $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
          if (mysql_num_rows($xCliNom) > 0) {
            $vCliNom = mysql_fetch_array($xCliNom);
            $vDocDes['clinomxx'] = $vCliNom['clinomxx'];
            $vNomTer["{$vDocDes['cliidxxx']}"] = $vCliNom['clinomxx'];
            $vNitTer[count($vNitTer)] = "{$vDocOri['cliidxxx']}";
          }

          if ($vDocDes['ccoidxxx'] == "") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El DO Destino [".$_POST['cSucIdDes']. "-".$_POST['cDocNroDes']."-".$_POST['cDocSufDes']."] No Tiene Centro de Costo Asignado.\n";
          }
				}else{
					$nSwitch = 1;
	    	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	    	  $cMsj .= "El DO Destino [".$_POST['cSucIdDes']. "-".$_POST['cDocNroDes']."-".$_POST['cDocSufDes']."] No Existe o NO Se Encuentra en Estado ACTIVO .\n";
      	}
      }
      
      //Verificando que haya selecionado al menos un pago a tercero o anticipo
      $nSel = 0;
      $mRegSel = f_Explode_Array($_POST['cComMemo'],"|","~");
      for ($i=0; $i<count($mRegSel); $i++) {
        if ($mRegSel[$i][0] != "") {
          $nSel++; 

          //Validando que se selecciono sucursal si no es un anticipo
          if ($mRegSel[$i][12] != "SI" && $mRegSel[$i][13] == "") { 
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Debe Seleccionar Sucursal ICA para el Comprobante [{$mRegSel[$i][0]}-{$mRegSel[$i][1]}-{$mRegSel[$i][2]}], Concepto [{$mRegSel[$i][10]}-{$mRegSel[$i][11]}].\n";
          }
        }
      }

      if ($nSel == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Debe Seleccionar Por lo Menos Un Concepto de Pagos a Terceros o Anticipo.\n";
      }

      //Buscando las cuentas de retencion CREE
    	$qRetCree  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
    	$qRetCree .= "FROM $cAlfa.fpar0115 ";
    	$qRetCree .= "WHERE ";
    	$qRetCree .= "pucgruxx LIKE \"23\" AND ";
    	$qRetCree .= "pucterxx LIKE \"R\"  AND ";
    	$qRetCree .= "pucdesxx LIKE \"%CREE%\"";
    	$xRetCree  = f_MySql("SELECT","",$qRetCree,$xConexion01,"");
    	//f_Mensaje(__FILE__,__LINE__,$qRetCree."~".mysql_num_rows($xRetCree));
    	$mRetCree = array();
    	while ($xRRC = mysql_fetch_array($xRetCree)){
    		$mRetCree[count($mRetCree)] = $xRRC['pucidxxx'];
      }
      
      //Buscando las cuentas de retencion CREE
    	$qPucCon  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
    	$qPucCon .= "FROM $cAlfa.fpar0115 ";
    	$qPucCon .= "WHERE ";
    	$qPucCon .= "pucdetxx IN (\"C\",\"P\")";
    	$xPucCon  = f_MySql("SELECT","",$qPucCon,$xConexion01,"");
    	// f_Mensaje(__FILE__,__LINE__,$qPucCon."~".mysql_num_rows($xPucCon));
    	$mPucCon = array();
    	while ($xRRC = mysql_fetch_array($xPucCon)){
    		$mPucCon[count($mPucCon)] = $xRRC['pucidxxx'];
    	}

      //Array PCC
      $vPCC = array();
      //Array Anticipos
      $vAnticipos = array();

      //Trae el movimiento del DO
      //Buscano conceptos de causaciones automaticas
      $qCAyP121  = "SELECT DISTINCT ";
      $qCAyP121 .= "pucidxxx, ";
      $qCAyP121 .= "ctoidxxx ";
      $qCAyP121 .= "FROM $cAlfa.fpar0121 ";
      $qCAyP121 .= "WHERE ";
      $qCAyP121 .= "regestxx = \"ACTIVO\"";
      $xCAyP121 = f_MySql("SELECT","",$qCAyP121,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qCAyP121."~".mysql_num_rows($xCAyP121));
      while($xRCP121 = mysql_fetch_array($xCAyP121)) {
        $vPCC[count($vPCC)] = "{$xRCP121['pucidxxx']}~{$xRCP121['ctoidxxx']}";
      }

      //Buscando conceptos
      $qCtoAntyPCC  = "SELECT DISTINCT ";
      $qCtoAntyPCC .= "ctoantxx, ";
      $qCtoAntyPCC .= "ctopccxx, ";
      $qCtoAntyPCC .= "pucidxxx, ";
      $qCtoAntyPCC .= "ctoidxxx ";
      $qCtoAntyPCC .= "FROM $cAlfa.fpar0119 ";
      $qCtoAntyPCC .= "WHERE ";
      $qCtoAntyPCC .= "(ctoantxx = \"SI\" OR ctopccxx = \"SI\") AND ";
      $qCtoAntyPCC .= "regestxx = \"ACTIVO\"";
      $xCtoAntyPCC = f_MySql("SELECT","",$qCtoAntyPCC,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCtoAntyPCC."~".mysql_num_rows($xCtoAntyPCC));
      while($xRCAP = mysql_fetch_array($xCtoAntyPCC)) {
        if($xRCAP['ctopccxx'] == "SI"){
          $vPCC[count($vPCC)] = "{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}";
        } else {
          $vAnticipos[count($vPCC)] = "{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}";
        }
      }

      //Validando que los registros seleccionados no esten facturados
      //Las poisiciones del array $mRegSel
      //[0]  Id Combropante         -> comidxxx 
      //[1]  Codigo Combprobante    -> comcodxx 
      //[2]  Consecutivo Uno        -> comcscxx 
      //[3]  Consecutivo Dos        -> comcsc2x 
      //[4]  Secuencia Comprobante  -> comseqxx 
      //[5]  Fecha Comprobante      -> comfecxx 
      //[6]  Base                   -> comvlr01 
      //[7]  Iva                    -> comvlr02 
      //[8]  Valor                  -> comvlrxx 
      //[9]  Movimiento Concepto    -> commovxx
      //[10] Codigo Concepto        -> ctoidxxx 
      //[11] Descripcion concepto   -> ctodesxx 
      //[12] Concepto de anticipo   -> ctoantxx 
      //[13] Sucursal Ica           -> sucicaxx 

      //Registros cabecera documentos seleccionados
      $mCabecera      = array();
      //Id comprobantes seleccionados, usado para buscarlos una sola vez 
      $vCabecera      = array();
      //Detalle PCC y Anticipos
      $mDetalle       = array();
      //Registros de retencion por comprobante;
      $mRetenciones   = array();
      //CxP o CxC de la causacion
      $mContrapartida = array();
      //Documentos cruce dos del comporobante, se guarda el primero del ajuste
      $mDocCurceDos   = array();

      for ($i=0; $i<count($mRegSel); $i++) {
        if (in_array("{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}",$vCabecera) == false) {
          //vector usado para validar si ya se buscaron los datos del comprobante
          $vCabecera[count($vCabecera)] = "{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}";

          $nAnio = substr($mRegSel[$i][5],0,4);
          //Trayendo datos de cabecera
          $qFcoc  = "SELECT * ";
          $qFcoc .= "FROM $cAlfa.fcoc$nAnio ";
          $qFcoc .= "WHERE ";
          $qFcoc .= "comidxxx = \"{$mRegSel[$i][0]}\" AND ";
          $qFcoc .= "comcodxx = \"{$mRegSel[$i][1]}\" AND ";
          $qFcoc .= "comcscxx = \"{$mRegSel[$i][2]}\" AND ";
          $qFcoc .= "comcsc2x = \"{$mRegSel[$i][3]}\" LIMIT 0,1";
          $xFcoc  = f_MySql("SELECT","",$qFcoc,$xConexion01,"");
          // echo $qFcoc."~".mysql_num_rows($xFcoc)."<br><br>";

          if(!$xFcoc) {
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "No Se Encotraron Datos de Cabecera del Comprobante [{$mRegSel[$i][0]}-{$mRegSel[$i][1]}-{$mRegSel[$i][2]}-{$mRegSel[$i][3]}].";
          } else {
            $xRFC = mysql_fetch_assoc($xFcoc);

            if(in_array("{$xRFC['teridxxx']}",$vNitTer) == false) {
              $vNitTer[count($vNitTer)] = "{$xRFC['teridxxx']}";
              //Trayendo el nombre del proveedor
              $qCliPro  = "SELECT ";
              $qCliPro .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
              $qCliPro .= "FROM $cAlfa.SIAI0150 ";
              $qCliPro .= "WHERE ";
              $qCliPro .= "CLIIDXXX = \"{$xRFC['teridxxx']}\" LIMIT 0,1";
              $xCliPro = f_MySql("SELECT","",$qCliPro,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qCliPro."~".mysql_num_rows($xCliPro));
              if (mysql_num_rows($xCliPro) > 0) {
                $vCliPro = mysql_fetch_array($xCliPro);
                $xRFC['clinomxx'] = $vCliPro['clinomxx'];
                $vNomTer["{$xRFC['teridxxx']}"] = $vCliPro['clinomxx'];
              }
            }

            if(in_array("{$xRFC['terid2xx']}",$vNitTer) == false) {
              $vNitTer[count($vNitTer)] = "{$xRFC['terid2xx']}";
              //Trayendo el nombre del proveedor
              $qCliPro  = "SELECT ";
              $qCliPro .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
              $qCliPro .= "FROM $cAlfa.SIAI0150 ";
              $qCliPro .= "WHERE ";
              $qCliPro .= "CLIIDXXX = \"{$xRFC['terid2xx']}\" LIMIT 0,1";
              $xCliPro = f_MySql("SELECT","",$qCliPro,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qCliPro."~".mysql_num_rows($xCliPro));
              if (mysql_num_rows($xCliPro) > 0) {
                $vCliPro = mysql_fetch_array($xCliPro);
                $xRFC['clinomxx'] = $vCliPro['clinomxx'];
                $vNomTer["{$xRFC['terid2xx']}"] = $vCliPro['clinomxx'];
              }
            }

            //Datos cabecera
            $mCabecera["{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}"]    = $xRFC;

            $qFcod  = "SELECT * ";
            $qFcod .= "FROM $cAlfa.fcod$nAnio ";
            $qFcod .= "WHERE ";
            $qFcod .= "comidxxx = \"{$mRegSel[$i][0]}\" AND ";
            $qFcod .= "comcodxx = \"{$mRegSel[$i][1]}\" AND ";
            $qFcod .= "comcscxx = \"{$mRegSel[$i][2]}\" AND ";
            $qFcod .= "comcsc2x = \"{$mRegSel[$i][3]}\" ORDER BY ABS(comseqxx)";
            $xFcod  = f_MySql("SELECT","",$qFcod,$xConexion01,"");

            while ($xRFD = mysql_fetch_assoc($xFcod)) {
              if(in_array("{$xRFD['teridxxx']}",$vNitTer) == false) {
                $vNitTer[count($vNitTer)] = "{$xRFD['teridxxx']}";
                //Trayendo el nombre del proveedor
                $qCliPro  = "SELECT ";
                $qCliPro .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
                $qCliPro .= "FROM $cAlfa.SIAI0150 ";
                $qCliPro .= "WHERE ";
                $qCliPro .= "CLIIDXXX = \"{$xRFD['teridxxx']}\" LIMIT 0,1";
                $xCliPro = f_MySql("SELECT","",$qCliPro,$xConexion01,"");
                // f_Mensaje(__FILE__,__LINE__,$qCliPro."~".mysql_num_rows($xCliPro));
                if (mysql_num_rows($xCliPro) > 0) {
                  $vCliPro = mysql_fetch_array($xCliPro);
                  $xRFD['clinomxx'] = $vCliPro['clinomxx'];
                  $vNomTer["{$xRFD['teridxxx']}"] = $vCliPro['clinomxx'];
                }
              }
  
              if(in_array("{$xRFD['terid2xx']}",$vNitTer) == false) {
                $vNitTer[count($vNitTer)] = "{$xRFD['terid2xx']}";
                //Trayendo el nombre del proveedor
                $qCliPro  = "SELECT ";
                $qCliPro .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
                $qCliPro .= "FROM $cAlfa.SIAI0150 ";
                $qCliPro .= "WHERE ";
                $qCliPro .= "CLIIDXXX = \"{$xRFD['terid2xx']}\" LIMIT 0,1";
                $xCliPro = f_MySql("SELECT","",$qCliPro,$xConexion01,"");
                // f_Mensaje(__FILE__,__LINE__,$qCliPro."~".mysql_num_rows($xCliPro));
                if (mysql_num_rows($xCliPro) > 0) {
                  $vCliPro = mysql_fetch_array($xCliPro);
                  $xRFD['clinomxx'] = $vCliPro['clinomxx'];
                  $vNomTer["{$xRFD['terid2xx']}"] = $vCliPro['clinomxx'];
                }
              }

              $nRetencion = 0;
              if(in_array($xRFD['pucidxxx'],$mRetCree) == true) { //ReteCree
                $nRetencion = 1;
                $xRFD['retenxxx']  = 'ReteCree';
              } elseif(substr($xRFD['pucidxxx'],0,4) == '2365') { //Retefuente
                $nRetencion = 1;
                $xRFD['retenxxx']  = 'Retefuente';
              }elseif(substr($xRFD['pucidxxx'],0,4) == '2367') { //ReteIva
                $nRetencion = 1;
                $xRFD['retenxxx']  = 'ReteIva';
              }elseif(substr($xRFD['pucidxxx'],0,4) == '2368') { //ReteIca
                $nRetencion = 1;
                $xRFD['retenxxx']  = 'ReteIca';
              } else {
                $xRFD['retencre'] = ""; //ReteCree
                $xRFD['retenrft'] = ""; //Retefuente
                $xRFD['retenriv'] = ""; //ReteIva
                $xRFD['retenric'] = ""; //ReteIca

      					//Solo se guardan los pagos a terceros y anticipos
                if (in_array("{$xRFD['pucidxxx']}~{$xRFD['ctoidxxx']}",$vPCC) == true || in_array("{$xRFD['pucidxxx']}~{$xRFD['ctoidxxx']}",$vAnticipos) == true) {
                  $xRFD['tipcomxx'] = (in_array("{$xRFD['pucidxxx']}~{$xRFD['ctoidxxx']}",$vPCC) == true) ? "PCC" : "ANTICIPO";
                  $nInd_mDetalle = count($mDetalle["{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}"]);
                  $mDetalle["{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}"][$nInd_mDetalle] = $xRFD;
                }

                //Guardando la contrapartida cxc o cxp del comprobante
                if (in_array("{$xRFD['pucidxxx']}",$mPucCon) == true) {
                  $xRFD['tipcomxx'] = "CONTRAPARTIDA";
                  $nInd_mContrapartida = count($mContrapartida["{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}"]);
                  $mContrapartida["{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}"][$nInd_mContrapartida] = $xRFD;
                }
              }

              //retenciones por concepto en la causacion
              if ($nRetencion == 1) {
                $nInd_mRetenciones = count($mRetenciones["{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}"]);
                $mRetenciones["{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}"][$nInd_mRetenciones] = $xRFD;
              }

              if ($mRegSel[$i][0] == $xRFD['comidxxx'] && 
                  $mRegSel[$i][1] == $xRFD['comcodxx'] && 
                  $mRegSel[$i][2] == $xRFD['comcscxx'] && 
                  $mRegSel[$i][3] == $xRFD['comcsc2x'] && 
                  $mRegSel[$i][4] == $xRFD['comseqxx']) {
                if ($xRFD['comfacxx'] != "") {
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "El Comprobante [{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}~{$mRegSel[$i][4]}] Ya Se Encuentra Facturado con el Documento [{$xRFD['comfacxx']}].\n";
                }
              }
            }
          }
        } else {
          //Validando que el pago no este facturado
          $vRegAux = $mDetalle["{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}"];
          for ($nS=0; $nS<count($vRegAux);$nS++) {
            
            if ($mRegSel[$i][0] == $vRegAux[$nS]['comidxxx'] && 
                $mRegSel[$i][1] == $vRegAux[$nS]['comcodxx'] && 
                $mRegSel[$i][2] == $vRegAux[$nS]['comcscxx'] && 
                $mRegSel[$i][3] == $vRegAux[$nS]['comcsc2x'] && 
                $mRegSel[$i][4] == $vRegAux[$nS]['comseqxx']) {
              if ($vRegAux['comfacxx'] != "") {
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Comprobante [{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}~{$mRegSel[$i][4]}] Ya Se Encuentra Facturado con el Documento [{$vRegAux[$nS]['comfacxx']}].\n";
              }
              $nS=count($vRegAux);
            }
          }
        } 
      }
	  break;
	  default:
	    $nSwitch = 1;
	    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	    $cMsj .= "El Modo de Grabado No Es Correcto.\n";
	  break;
  }

	if ($nSwitch == 0) {
	  switch ($_COOKIE['kModo']) {
      case "TRASLADAR":

        //Inicializando Clases para generacion de causacion automatica y guardar el ajuste
        $oCauAut = new cCausacionAutomaticaTerceros();
        $oAjuste = new cAjustes();

        // echo "<pre>";
        // print_r($mRetenciones);
        // echo "</pre>";
        
        //Identificando las retenciones encontradas a que pago pertenecen
        foreach ($mRetenciones as $cKey => $cValue) {
          //Buscando a que servicio aplica la retencion
          for($nA=0; $nA<count($mRetenciones[$cKey]); $nA++) {
            //Buscando en todos los servicios
            for ($nB=0; $nB<count($mDetalle[$cKey]); $nB++) {
              //si es un pago a tercero
              if ($mDetalle[$cKey][$nB]['tipcomxx'] == "PCC") {
                //Si el pago a tercero ya tiene asignada ese tipo de retencion
                //esta no se analiza para ese pago
                //Adicional se tiene en cuenta la secuencia de la retencion, si esta es menor a la del pago 
                //tampoco se analiza para ese pago
                if (in_array($mRetenciones[$cKey][$nA]['retenxxx'], $mDetalle[$cKey][$nB]['tipreten']) == false &&
                    ($mRetenciones[$cKey][$nA]['comseqxx']+0) > ($mDetalle[$cKey][$nB]['comseqxx']+0)) {
                  //si la base del servicio es igual a la base de retencion del impuesto se asocia ese impuesto a ese servicio
                  //Tambien se asigna la retencion al servicio segun el DO
                  $nIncRet = 0;

                  if ($mRetenciones[$cKey][$nA]['retenxxx'] == "ReteIva") {
                    //Si el pago a tercero ya tiene reteiva no se tiene en cuenta para este registro
                    if ($mDetalle[$cKey][$nB]['retenriv'] == "") {
                      //Para el ReteIva primero se verifica tomando como base el IVA
                      $nBase = $mDetalle[$cKey][$nB]['comvlr02'];
                      if (($mRetenciones[$cKey][$nA]['docidxxx'] == "" || $mRetenciones[$cKey][$nA]['docidxxx'] == $mDetalle[$cKey][$nB]['docidxxx']) &&
                          $mRetenciones[$cKey][$nA]['comvlr01'] == $nBase) {
                        $nIncRet = 1;
                        $mRetenciones[$cKey][$nA]['baseretx'] = "comvlr02";
                      }

                      //Despues se verifica tomando como base la base del concepto
                      if ($nIncRet == 0) {
                        $nBase = $mDetalle[$cKey][$nB]['comvlr01'];
                        if (($mRetenciones[$cKey][$nA]['docidxxx'] == "" || $mRetenciones[$cKey][$nA]['docidxxx'] == $mDetalle[$cKey][$nB]['docidxxx']) &&
                            $mRetenciones[$cKey][$nA]['comvlr01'] == $nBase) {
                          $nIncRet = 1;
                          $mRetenciones[$cKey][$nA]['baseretx'] = "comvlr01";
                        }
                      }

                      //Despues se verifica tomando como base el valor del concepto
                      if ($nIncRet == 0) {
                        $nBase = $mDetalle[$cKey][$nB]['comvlrxx'];
                        if (($mRetenciones[$cKey][$nA]['docidxxx'] == "" || $mRetenciones[$cKey][$nA]['docidxxx'] == $mDetalle[$cKey][$nB]['docidxxx']) &&
                            $mRetenciones[$cKey][$nA]['comvlr01'] == $nBase) {
                          $nIncRet = 1;
                          $mRetenciones[$cKey][$nA]['baseretx'] = "comvlrxx";
                        }
                      }
                    }
                  } else {

                    $nAnalizar = 0;
                    //Si el pago a tercero ya tiene ReteIva no se tiene en cuenta para este registro
                    if ($mRetenciones[$cKey][$nA]['retenxxx'] == "ReteIva" && $mDetalle[$cKey][$nB]['retenriv'] != "") {
                      $nAnalizar = 1;
                    }

                    //Si el pago a tercero ya tiene ReteCree no se tiene en cuenta para este registro
                    if ($mRetenciones[$cKey][$nA]['retenxxx'] == "ReteCree" && $mDetalle[$cKey][$nB]['retencre'] != "") {
                      $nAnalizar = 1;
                    }

                    //Si el pago a tercero ya tiene Retefuente no se tiene en cuenta para este registro
                    if ($mRetenciones[$cKey][$nA]['retenxxx'] == "Retefuente" && $mDetalle[$cKey][$nB]['retenrft'] != "") {
                      $nAnalizar = 1;
                    }

                    //Si el pago a tercero ya tiene ReteIca no se tiene en cuenta para este registro
                    if ($mRetenciones[$cKey][$nA]['retenxxx'] == "ReteIca" && $mDetalle[$cKey][$nB]['retenric'] != "") {
                      $nAnalizar = 1;
                    }

                    if ($nAnalizar == 0) {
                      //Buscando retenciones, comparando base uno a uno
                      $nBase = ($mDetalle[$cKey][$nB]['comvlr01'] > 0) ? $mDetalle[$cKey][$nB]['comvlr01'] : $mDetalle[$cKey][$nB]['comvlrxx'];
                      if (($mRetenciones[$cKey][$nA]['docidxxx'] == "" || $mRetenciones[$cKey][$nA]['docidxxx'] == $mDetalle[$cKey][$nB]['docidxxx']) &&
                          $mRetenciones[$cKey][$nA]['comvlr01'] == $nBase) {
                        $nIncRet = 1;
                      }
                    }
                  }

                  if ($nIncRet == 1) {
                    $mDetalle[$cKey][$nB]['tipreten'][] = $mRetenciones[$cKey][$nA]['retenxxx'];
                    $mDetalle[$cKey][$nB]['retenxxx'][] = $mRetenciones[$cKey][$nA];
                    $nB = count($mDetalle[$cKey]);
                  }
                }
              }
            }
          }
        }

        // echo "<pre>";
        // print_r($mDetalle);
        // echo "</pre>";

        //Validando que los registros seleccionados no esten facturados
        //Las poisiciones del array $mRegSel
        //[0]  Id Combropante         -> comidxxx 
        //[1]  Codigo Combprobante    -> comcodxx 
        //[2]  Consecutivo Uno        -> comcscxx 
        //[3]  Consecutivo Dos        -> comcsc2x 
        //[4]  Secuencia Comprobante  -> comseqxx 
        //[5]  Fecha Comprobante      -> comfecxx 
        //[6]  Base                   -> comvlr01 
        //[7]  Iva                    -> comvlr02 
        //[8]  Valor                  -> comvlrxx 
        //[9]  Movimiento Concepto    -> commovxx
        //[10] Codigo Concepto        -> ctoidxxx 
        //[11] Descripcion concepto   -> ctodesxx 
        //[12] Concepto de anticipo   -> ctoantxx
        //[13] Sucursal Ica           -> sucicaxx 
        //Trayendo datos de detalle y retenciones a los registros seleccionados
        for ($i=0; $i<count($mRegSel); $i++) {
          $cKey = "{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}";
          for ($nB=0; $nB<count($mDetalle[$cKey]); $nB++) {
            if ($mRegSel[$i][0] == $mDetalle[$cKey][$nB]['comidxxx'] && 
                $mRegSel[$i][1] == $mDetalle[$cKey][$nB]['comcodxx'] && 
                $mRegSel[$i][2] == $mDetalle[$cKey][$nB]['comcscxx'] && 
                $mRegSel[$i][3] == $mDetalle[$cKey][$nB]['comcsc2x'] && 
                $mRegSel[$i][4] == $mDetalle[$cKey][$nB]['comseqxx']) {

              //Si el valor es igual, las retenciones y datos del comprobante se guardan en el registro seleccionado
              if (round($mRegSel[$i][8],5) == round($mDetalle[$cKey][$nB]['comvlrxx'],5)) {
                foreach ($mDetalle[$cKey][$nB] as $cKey01 => $cValue01) {
                  $mRegSel[$i][$cKey01] = $cValue01;
                }
              } else {
                foreach ($mDetalle[$cKey][$nB] as $cKey01 => $cValue01) {
                  switch($cKey01) {
                    case "comvlr01":
                      $cValue01 = $mRegSel[$i][6];
                    break;
                    case "comvlr02":
                      $cValue01 = $mRegSel[$i][7];
                    break;
                    case "comvlrxx":
                      $cValue01 = $mRegSel[$i][8];
                    break;
                    case "retenxxx":
                      // Los valores de las retenciones se calculan nuevamente
                      for ($nR=0; $nR<count($cValue01); $nR++) {
                        if ($cValue01[$nR]['retenxxx'] == "ReteIva") {
                          //Difiniendo base de retencion
                          if ($cValue01[$nR]['baseretx'] == "comvlr02"){
                            $nBasRet = $mRegSel[$i][7];
                          } elseif ($cValue01[$nR]['baseretx'] == "comvlr01"){
                            $nBasRet = $mRegSel[$i][6];
                          }else { //comvlrxx
                            $nBasRet = $mRegSel[$i][8];
                          }
                          $cValue01[$nR]['comvlr01'] = $nBasRet;
                          $cValue01[$nR]['comvlrxx'] = round($nBasRet * ($cValue01[$nR]['pucretxx']/100));
                        } else {
                          $cValue01[$nR]['comvlr01'] = $mRegSel[$i][6];
                          $cValue01[$nR]['comvlrxx'] = round($mRegSel[$i][6] * ($cValue01[$nR]['pucretxx']/100));
                        }
                      }
                    break;
                    default:
                      //No hace nada
                    break;
                  }
                  $mRegSel[$i][$cKey01] = $cValue01;
                }
              }
              $nB=count($mDetalle[$cKey]);
            }
          }
          //Trayendo la observacion de cabecera para asignarla al comprobante
          $mRegSel[$i]['comobsxx'] = $mCabecera["{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}"]['comobsxx'];
        }

        //Calculando para el concepto de cobro impuestos con el nuevo cliente
        for ($i=0; $i<count($mRegSel); $i++) {
          if ($mRegSel[$i][14] == "") {
            //Identificador del documento
            $cKey = "{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}";

            //Incluyendo en el comprobante por el que se debe agrupar el ajuste
            $mRegSel[$i][15] = $cKey;

            //Marca que indica que ya fue procesado
            $mRegSel[$i][14] = "SI";

            //Si es un pago a tercro se realiza la causacion automatica
            $nIncluir = 0;
            if ($mRegSel[$i]['tipcomxx'] == "PCC" && $mRegSel[$i][12] != "SI") {
              $nIncluir = 1;

              //Matriz parametrica para calcular 
              $mCauAut = array();

              /**
               * Indice de la matriz para ingresar nuevos registros.
               * 
               * @var numeric
               */
              $nInd_mCauAut = count($mCauAut);
              
              /**
               * Vector para explotar el numero del DO. $mCabecera[$cKey]
               * 
               * @var array
               */
              $mCauAut[$nInd_mCauAut]['origenxx'] = "TRASLADO";                                                             // Origen, desde este proyecto especial se envia TRASLADO
              $mCauAut[$nInd_mCauAut]['sucidxxx'] = $vDocDes['sucidxxx'];                                                   // Sucursal
              $mCauAut[$nInd_mCauAut]['docidxxx'] = $vDocDes['docidxxx'];                                                   // DO
              $mCauAut[$nInd_mCauAut]['docsufxx'] = $vDocDes['docsufxx'];                                                   // Sufijo
              $mCauAut[$nInd_mCauAut]['tertipxx'] = $mRegSel[$i]['tertipxx'];                                                             // Tipo de Tercero Dueño del DO
              $mCauAut[$nInd_mCauAut]['teridxxx'] = $vDocDes['cliidxxx'];                                                   // Id Cliente Dueño del DO
              $mCauAut[$nInd_mCauAut]['ternomxx'] = $vDocDes['clinomxx'];                                                   // Nombre Cliente Dueño del DO
              $mCauAut[$nInd_mCauAut]['tertipbx'] = $mRegSel[$i]['tertip2x'];                                                        // Tipo de Tercero del Proveedor
              $mCauAut[$nInd_mCauAut]['teridbxx'] = $mRegSel[$i]['terid2xx'];                                               // Id Proveedor
              $mCauAut[$nInd_mCauAut]['ternombx'] = $vNomTer["{$mRegSel[$i]['terid2xx']}"];                                 // Nombre Proveedor
              $mCauAut[$nInd_mCauAut]['sucicapr'] = $mRegSel[$i][13];                                                       // Sucursal ICA del Proveedor
              $mCauAut[$nInd_mCauAut]['combasex'] = $mRegSel[$i][6];                                                        // Base del Comprobante
              $mCauAut[$nInd_mCauAut]['comivaxx'] = $mRegSel[$i][7];                                                        // IVA del Comprobante
              $mCauAut[$nInd_mCauAut]['comobsxx'] = $mRegSel[$i]['comobsxx'];                                               // Observacion del Comprobante
              $mCauAut[$nInd_mCauAut]['docproxx'] = "VALOR";                                                                // Tipo de Prorrateo del DO (VALOR/PORCENTAJE)
              $mCauAut[$nInd_mCauAut]['docprovl'] = $mRegSel[$i][8];                                                        // Valor del Prorrateo del DO 
              $mCauAut[$nInd_mCauAut]['ctoidxxx'] = $mRegSel[$i]['ctoidxxx'];                                               // Concepto PAPA
              $mCauAut[$nInd_mCauAut]['ctodocid'] = $vDocDes['sucidxxx']."-".$vDocDes['docidxxx']."-".$vDocDes['docsufxx']; // DO del Concepto
              $mCauAut[$nInd_mCauAut]['ctodesxx'] = $mRegSel[$i][11];                                                       // Descripcion Concepto PAPA
              $mCauAut[$nInd_mCauAut]['aiuaplxx'] = "NO"; 					 	                                                      // Aplica Base AIU
              $mCauAut[$nInd_mCauAut]['ctobaaiu'] = 0;                                                                      // Valor Base AIU
              $mCauAut[$nInd_mCauAut]['ctobasex'] = $mRegSel[$i][6];                                                        // Valor Base del Concepto
              $mCauAut[$nInd_mCauAut]['ctoivaxx'] = $mRegSel[$i][7];                                                        // Valor IVA del Concepto
              $mCauAut[$nInd_mCauAut]['ctototxx'] = $mRegSel[$i][8];                                                        // Valor Total del Concepto
              $mCauAut[$nInd_mCauAut]['comidxxx'] = $mRegSel[$i]['comidxxx'];                                                                // Id del Comprobante
              $mCauAut[$nInd_mCauAut]['comcodxx'] = $mRegSel[$i]['comcodxx'];                                                               // Codigo del Comprobante
              $mCauAut[$nInd_mCauAut]['ccoidxxx'] = $mCabecera[$cKey]['ccoidxxx'];                                          // Centro Costo Comprobante
              $mCauAut[$nInd_mCauAut]['sccidxxx'] = $mCabecera[$cKey]['sccidxxx'];                                          // Subcentro de Costo Comprobante
              $mCauAut[$nInd_mCauAut]['comcscxx'] = $mCabecera[$cKey]['comcscxx'];                                          // Factura del Comprobante

              //No importa si el comprobante de casuación automatica es por porcenje o por valor
              //si es por porcentaje se envia el 100% porque es un solo concepto y solo DO
              $mCauAut[$nInd_mCauAut]['docbasex'] = $mRegSel[$i][6];                                                        // Valor Base del DO
              $mCauAut[$nInd_mCauAut]['docbaaiu'] = 0;                                                                      // Valor Base A.I.U del DO
              $mCauAut[$nInd_mCauAut]['docivaxx'] = $mRegSel[$i][7];                                                        // Valor IVA del DO
              $mCauAut[$nInd_mCauAut]['doctotxx'] = $mRegSel[$i][8];                                                        // Valor Total del DO	
              
              //Campos Tasa Pactada
              $mCauAut[$nInd_mCauAut]['tasadiax'] = $_POST['nTasaCambio']; 				                                          // Tasa de Cambio del dia
              $mCauAut[$nInd_mCauAut]['comintpa'] = $_POST['cComIntPa']; 					                                          // Intermediacion de Pago

              /**
               * Hago el llamado al metodo que elabora la causacion automaticamente.
               */
              $mReturn_CauAut = $oCauAut->fnCausacionAutomaticaTerceros($mCauAut);

              if (count($mReturn_CauAut) > 0) {
                //El primer item retornado corresponde al pago a terceros, los demas corresponden a las cuentas de retencion y cuenta por pagar
                //adicionando el nuevo concepto de cobro y cambiando el movimiento del do origen
                $nInd_mRegSel = count($mRegSel);
                $mRegSel[$nInd_mRegSel][0]  = $_POST['cComId'];
                $mRegSel[$nInd_mRegSel][1]  = $_POST['cComCod'];
                $mRegSel[$nInd_mRegSel][2]  = "{$mRegSel[$i][2]}";
                $mRegSel[$nInd_mRegSel][3]  = "";
                $mRegSel[$nInd_mRegSel][4]  = "";
                $mRegSel[$nInd_mRegSel][5]  = $_POST['dComFec'];
                $mRegSel[$nInd_mRegSel][6]  = $mReturn_CauAut[0]['nComBIva'];
                $mRegSel[$nInd_mRegSel][7]  = $mReturn_CauAut[0]['nComIva'];
                $mRegSel[$nInd_mRegSel][8]  = $mReturn_CauAut[0]['nComVlr'];
                $mRegSel[$nInd_mRegSel][10] = $mReturn_CauAut[0]['cCtoId'];
                $mRegSel[$nInd_mRegSel][11] = $mReturn_CauAut[0]['cCtoDes'];
                $mRegSel[$nInd_mRegSel][15] = $cKey; 
                $mRegSel[$nInd_mRegSel][14] = "SI";
                $mRegSel[$nInd_mRegSel]['comidxxx'] = $_POST['cComId'];
                $mRegSel[$nInd_mRegSel]['comcodxx'] = $_POST['cComCod'];
                $mRegSel[$nInd_mRegSel]['comcscxx'] = "{$mRegSel[$i][2]}";
                $mRegSel[$nInd_mRegSel]['comcsc2x'] = "";
                $mRegSel[$nInd_mRegSel]['comcsc3x'] = "";
                $mRegSel[$nInd_mRegSel]['comseqxx'] = "";
                $mRegSel[$nInd_mRegSel]['comfecxx'] = $_POST['dComFec'];
                $mRegSel[$nInd_mRegSel]['ccoidxxx'] = $mReturn_CauAut[0]['cCcoId'];
                $mRegSel[$nInd_mRegSel]['sccidxxx'] = $mReturn_CauAut[0]['cSccId'];
                $mRegSel[$nInd_mRegSel]['pucidxxx'] = $mReturn_CauAut[0]['cPucId'];
                $mRegSel[$nInd_mRegSel]['ctoidxxx'] = $mReturn_CauAut[0]['cCtoId'];
                $mRegSel[$nInd_mRegSel]['commovxx'] = $mReturn_CauAut[0]['cComMov'];
                $mRegSel[$nInd_mRegSel]['comperxx'] = str_replace("-","",substr($_POST['dComFec'],0,7));
                $mRegSel[$nInd_mRegSel]['tertipxx'] = $mReturn_CauAut[0]['cTerTip'];
                $mRegSel[$nInd_mRegSel]['teridxxx'] = $mReturn_CauAut[0]['cTerId'];
                $mRegSel[$nInd_mRegSel]['tertip2x'] = $mReturn_CauAut[0]['cTerTipB'];
                $mRegSel[$nInd_mRegSel]['terid2xx'] = $mReturn_CauAut[0]['cTerIdB'];
                $mRegSel[$nInd_mRegSel]['puctipej'] = $mReturn_CauAut[0]['cPucTipEj'];
                $mRegSel[$nInd_mRegSel]['comvlrxx'] = $mReturn_CauAut[0]['nComVlr'];
                $mRegSel[$nInd_mRegSel]['comvlr01'] = $mReturn_CauAut[0]['nComBIva'];
                $mRegSel[$nInd_mRegSel]['comvlr02'] = $mReturn_CauAut[0]['nComIva'];
                $mRegSel[$nInd_mRegSel]['comidcxx'] = $mReturn_CauAut[0]['cComIdC'];
                $mRegSel[$nInd_mRegSel]['comcodcx'] = $mReturn_CauAut[0]['cComCodC'];
                $mRegSel[$nInd_mRegSel]['comcsccx'] = $mReturn_CauAut[0]['cComCscC'];
                $mRegSel[$nInd_mRegSel]['comseqcx'] = $mReturn_CauAut[0]['cComSeqC'];
                $mRegSel[$nInd_mRegSel]['comctocx'] = "{$mRegSel[$i][0]}";
                $mRegSel[$nInd_mRegSel]['comidc2x'] = "";
                $mRegSel[$nInd_mRegSel]['comcodc2'] = "";
                $mRegSel[$nInd_mRegSel]['comcscc2'] = "";
                $mRegSel[$nInd_mRegSel]['comseqc2'] = "";
                $mRegSel[$nInd_mRegSel]['comfecve'] = $_POST['dComVen'];
                $mRegSel[$nInd_mRegSel]['sucidxxx'] = $mReturn_CauAut[0]['cSucId'];
                $mRegSel[$nInd_mRegSel]['docidxxx'] = $mReturn_CauAut[0]['cDocId'];
                $mRegSel[$nInd_mRegSel]['docsufxx'] = $mReturn_CauAut[0]['cDocSuf'];
                $mRegSel[$nInd_mRegSel]['tipcomxx'] = "PCC";
                $mRegSel[$nInd_mRegSel]['sucicaxx'] = $mRegSel[$i][13];

                $mAuxRet = array();
                $mAuxCon = array();
                //Incluyendo las retenciones del concepto
                for($nR=1;$nR<count($mReturn_CauAut);$nR++) {
                  if ($mReturn_CauAut[$nR]['cPucTer'] == "R") {
                    //Es una cuenta de retencion
                    $nInd_mAuxRet = count($mAuxRet);
                    $mAuxRet[$nInd_mAuxRet]['comidxxx'] = $_POST['cComId'];
                    $mAuxRet[$nInd_mAuxRet]['comcodxx'] = $_POST['cComCod'];
                    $mAuxRet[$nInd_mAuxRet]['comcscxx'] = "{$mRegSel[$i][2]}";
                    $mAuxRet[$nInd_mAuxRet]['comcsc2x'] = "";
                    $mAuxRet[$nInd_mAuxRet]['comcsc3x'] = "";
                    $mAuxRet[$nInd_mAuxRet]['comseqxx'] = "";
                    $mAuxRet[$nInd_mAuxRet]['comfecxx'] = $_POST['dComFec'];
                    $mAuxRet[$nInd_mAuxRet]['ccoidxxx'] = $mReturn_CauAut[$nR]['cCcoId'];
                    $mAuxRet[$nInd_mAuxRet]['sccidxxx'] = $mReturn_CauAut[$nR]['cSccId'];
                    $mAuxRet[$nInd_mAuxRet]['pucidxxx'] = $mReturn_CauAut[$nR]['cPucId'];
                    $mAuxRet[$nInd_mAuxRet]['ctoidxxx'] = $mReturn_CauAut[$nR]['cCtoId'];
                    $mAuxRet[$nInd_mAuxRet]['commovxx'] = $mReturn_CauAut[$nR]['cComMov'];
                    $mAuxRet[$nInd_mAuxRet]['comperxx'] = str_replace("-","",substr($_POST['dComFec'],0,7));
                    $mAuxRet[$nInd_mAuxRet]['tertipxx'] = $mReturn_CauAut[$nR]['cTerTip'];
                    $mAuxRet[$nInd_mAuxRet]['teridxxx'] = $mReturn_CauAut[$nR]['cTerId'];
                    $mAuxRet[$nInd_mAuxRet]['tertip2x'] = $mReturn_CauAut[$nR]['cTerTipB'];
                    $mAuxRet[$nInd_mAuxRet]['terid2xx'] = $mReturn_CauAut[$nR]['cTerIdB'];
                    $mAuxRet[$nInd_mAuxRet]['puctipej'] = $mReturn_CauAut[$nR]['cPucTipEj'];
                    $mAuxRet[$nInd_mAuxRet]['comvlrxx'] = $mReturn_CauAut[$nR]['nComVlr'];
                    $mAuxRet[$nInd_mAuxRet]['comvlr01'] = $mReturn_CauAut[$nR]['nComBRet'];
                    $mAuxRet[$nInd_mAuxRet]['comvlr02'] = $mReturn_CauAut[$nR]['nComIva'];
                    $mAuxRet[$nInd_mAuxRet]['pucretxx'] = $mReturn_CauAut[$nR]['nPucRet'];
                    $mAuxRet[$nInd_mAuxRet]['comidcxx'] = "";
                    $mAuxRet[$nInd_mAuxRet]['comcodcx'] = "";
                    $mAuxRet[$nInd_mAuxRet]['comcsccx'] = "";
                    $mAuxRet[$nInd_mAuxRet]['comseqcx'] = "";
                    $mAuxRet[$nInd_mAuxRet]['comctocx'] = "{$mRegSel[$i][0]}";
                    $mAuxRet[$nInd_mAuxRet]['comidc2x'] = "";
                    $mAuxRet[$nInd_mAuxRet]['comcodc2'] = "";
                    $mAuxRet[$nInd_mAuxRet]['comcscc2'] = "";
                    $mAuxRet[$nInd_mAuxRet]['comseqc2'] = "";
                    $mAuxRet[$nInd_mAuxRet]['comfecve'] = $_POST['dComVen'];
                    $mAuxRet[$nInd_mAuxRet]['sucidxxx'] = $mReturn_CauAut[$nR]['cSucId'];
                    $mAuxRet[$nInd_mAuxRet]['docidxxx'] = $mReturn_CauAut[$nR]['cDocId'];
                    $mAuxRet[$nInd_mAuxRet]['docsufxx'] = $mReturn_CauAut[$nR]['cDocSuf'];
                    $mAuxRet[$nInd_mAuxRet]['retenxxx'] = $mReturn_CauAut[$nR]['cPucTer'];
                  }

                  if ($mReturn_CauAut[$nR]['cPucDet'] == "P" || $mReturn_CauAut[$nR]['cPucDet'] == "C") {
                    //Es una cuenta CxP o CxC
                    $nInd_mAuxCon = count($mAuxCon);
                    $mAuxCon[$nInd_mAuxCon]['comidxxx'] = $_POST['cComId'];
                    $mAuxCon[$nInd_mAuxCon]['comcodxx'] = $_POST['cComCod'];
                    $mAuxCon[$nInd_mAuxCon]['comcscxx'] = "{$mRegSel[$i][2]}";
                    $mAuxCon[$nInd_mAuxCon]['comcsc2x'] = "";
                    $mAuxCon[$nInd_mAuxCon]['comcsc3x'] = "";
                    $mAuxCon[$nInd_mAuxCon]['comseqxx'] = "";
                    $mAuxCon[$nInd_mAuxCon]['comfecxx'] = $_POST['dComFec'];
                    $mAuxCon[$nInd_mAuxCon]['ccoidxxx'] = $mReturn_CauAut[$nR]['cCcoId'];
                    $mAuxCon[$nInd_mAuxCon]['sccidxxx'] = $mReturn_CauAut[$nR]['cSccId'];
                    $mAuxCon[$nInd_mAuxCon]['pucidxxx'] = $mReturn_CauAut[$nR]['cPucId'];
                    $mAuxCon[$nInd_mAuxCon]['ctoidxxx'] = $mReturn_CauAut[$nR]['cCtoId'];
                    $mAuxCon[$nInd_mAuxCon]['commovxx'] = $mReturn_CauAut[$nR]['cComMov'];
                    $mAuxCon[$nInd_mAuxCon]['comperxx'] = str_replace("-","",substr($_POST['dComFec'],0,7));
                    $mAuxCon[$nInd_mAuxCon]['tertipxx'] = $mReturn_CauAut[$nR]['cTerTip'];
                    $mAuxCon[$nInd_mAuxCon]['teridxxx'] = $mReturn_CauAut[$nR]['cTerId'];
                    $mAuxCon[$nInd_mAuxCon]['tertip2x'] = $mReturn_CauAut[$nR]['cTerTipB'];
                    $mAuxCon[$nInd_mAuxCon]['terid2xx'] = $mReturn_CauAut[$nR]['cTerIdB'];
                    $mAuxCon[$nInd_mAuxCon]['puctipej'] = $mReturn_CauAut[$nR]['cPucTipEj'];
                    $mAuxCon[$nInd_mAuxCon]['comvlrxx'] = $mReturn_CauAut[$nR]['nComVlr'];
                    $mAuxCon[$nInd_mAuxCon]['comvlr01'] = $mReturn_CauAut[$nR]['nComBRet'];
                    $mAuxCon[$nInd_mAuxCon]['comvlr02'] = $mReturn_CauAut[$nR]['nComIva'];
                    $mAuxCon[$nInd_mAuxCon]['pucretxx'] = $mReturn_CauAut[$nR]['nPucRet'];
                    $mAuxCon[$nInd_mAuxCon]['comidcxx'] = "";
                    $mAuxCon[$nInd_mAuxCon]['comcodcx'] = "";
                    $mAuxCon[$nInd_mAuxCon]['comcsccx'] = "";
                    $mAuxCon[$nInd_mAuxCon]['comseqcx'] = "";
                    $mAuxCon[$nInd_mAuxCon]['comctocx'] = "{$mRegSel[$i][0]}";
                    $mAuxCon[$nInd_mAuxCon]['comidc2x'] = "";
                    $mAuxCon[$nInd_mAuxCon]['comcodc2'] = "";
                    $mAuxCon[$nInd_mAuxCon]['comcscc2'] = "";
                    $mAuxCon[$nInd_mAuxCon]['comseqc2'] = "";
                    $mAuxCon[$nInd_mAuxCon]['comfecve'] = $_POST['dComVen'];
                    $mAuxCon[$nInd_mAuxCon]['sucidxxx'] = $mReturn_CauAut[$nR]['cSucId'];
                    $mAuxCon[$nInd_mAuxCon]['docidxxx'] = $mReturn_CauAut[$nR]['cDocId'];
                    $mAuxCon[$nInd_mAuxCon]['docsufxx'] = $mReturn_CauAut[$nR]['cDocSuf'];
                  }
                }

                //Validando diferencias en los tribuos para llevar la diferencia a la cuenta por cobrar o por pagar del comprobante origen
                for ($nR=0; $nR<count($mRegSel[$i]['retenxxx']); $nR++) {
                  for ($nA=0;$nA<count($mAuxRet);$nA++) {
                    if ($mRegSel[$i]['retenxxx'][$nR]['pucidxxx'] == $mAuxRet[$nA]['pucidxxx'] &&
                        $mRegSel[$i]['retenxxx'][$nR]['ctoidxxx'] == $mAuxRet[$nA]['ctoidxxx'] &&
                        $mRegSel[$i]['retenxxx'][$nR]['commovxx'] == $mAuxRet[$nA]['commovxx'] &&
                        $mRegSel[$i]['retenxxx'][$nR]['teridxxx'] == $mAuxRet[$nA]['teridxxx'] &&
                        $mRegSel[$i]['retenxxx'][$nR]['comvlrxx'] == $mAuxRet[$nA]['comvlrxx'] &&
                        $mRegSel[$i]['retenxxx'][$nR]['comvlr01'] == $mAuxRet[$nA]['comvlr01'] &&
                        $mRegSel[$i]['retenxxx'][$nR]['pucretxx'] == $mAuxRet[$nA]['pucretxx']){
                      //Indica que esta contidicion tributaria no cambio
                      $mRegSel[$i]['retenxxx'][$nR]['aplicare'] = "SI";
                      $mAuxRet[$nA]['aplicare'] = "SI";
                    }
                  }
                }

                //Retenciones al nuevo concepto del do destino
                $mRegSel[$nInd_mRegSel]['retenxxx'] = $mAuxRet;
                //Contrapartida 
                $mRegSel[$nInd_mRegSel]['contrapa'] = $mAuxCon;
              } else {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "No Fue Posible Generar la Causacion Automatica para el Concepto [{$mRegSel[$i][9]}-{$mRegSel[$i][10]}] del Comprobante [{$mRegSel[$i][0]}~{$mRegSel[$i][1]}~{$mRegSel[$i][2]}~{$mRegSel[$i][3]}~{$mRegSel[$i][4]}].\n";
              }
            }

            if ($nIncluir == 0) {
              //Se crea la contrapatida igual al registro actual, se reemplaza el nombre del cliente
              //Si de quien se recibe el pago es diferente al cliente se mantiene el de quien se recibe el pago
              //y se reemplaza solo el cliente
              //En los anticipos para los recibos de caja en el teridxxx va el dueño del DO 
              //y en el terid2x va de quien se recibio el pago
              $nInd_mRegSel = count($mRegSel);
              $mRegSel[$nInd_mRegSel][0]  = $_POST['cComId'];
              $mRegSel[$nInd_mRegSel][1]  = $_POST['cComCod'];
              $mRegSel[$nInd_mRegSel][2]  = "{$mRegSel[$i][2]}";
              $mRegSel[$nInd_mRegSel][3]  = "";
              $mRegSel[$nInd_mRegSel][4]  = "";
              $mRegSel[$nInd_mRegSel][5]  = $_POST['dComFec'];
              $mRegSel[$nInd_mRegSel][6]  = $mRegSel[$i][6];
              $mRegSel[$nInd_mRegSel][7]  = $mRegSel[$i][7];
              $mRegSel[$nInd_mRegSel][8]  = $mRegSel[$i][8];
              $mRegSel[$nInd_mRegSel][9]  = $mRegSel[$i][9];
              $mRegSel[$nInd_mRegSel][10] = $mRegSel[$i][10];
              $mRegSel[$nInd_mRegSel][15] = $cKey; 
              $mRegSel[$nInd_mRegSel][14] = "SI";
              $mRegSel[$nInd_mRegSel]['comidxxx'] = $_POST['cComId'];
              $mRegSel[$nInd_mRegSel]['comcodxx'] = $_POST['cComCod'];
              $mRegSel[$nInd_mRegSel]['comcscxx'] = "{$mRegSel[$i][2]}";
              $mRegSel[$nInd_mRegSel]['comcsc2x'] = "";
              $mRegSel[$nInd_mRegSel]['comcsc3x'] = "";
              $mRegSel[$nInd_mRegSel]['comseqxx'] = "";
              $mRegSel[$nInd_mRegSel]['comfecxx'] = $_POST['dComFec'];
              $mRegSel[$nInd_mRegSel]['ccoidxxx'] = $vDocDes['ccoidxxx'];
              $mRegSel[$nInd_mRegSel]['sccidxxx'] = $vDocDes['docidxxx'];
              $mRegSel[$nInd_mRegSel]['pucidxxx'] = $mRegSel[$i]['pucidxxx'];
              $mRegSel[$nInd_mRegSel]['ctoidxxx'] = $mRegSel[$i]['ctoidxxx'];
              $mRegSel[$nInd_mRegSel]['commovxx'] = $mRegSel[$i]['commovxx'];
              $mRegSel[$nInd_mRegSel]['comperxx'] = str_replace("-","",substr($_POST['dComFec'],0,7));
              $mRegSel[$nInd_mRegSel]['tertipxx'] = $mRegSel[$i]['tertipxx'];
              $mRegSel[$nInd_mRegSel]['teridxxx'] = $mRegSel[$i]['teridxxx'];
              $mRegSel[$nInd_mRegSel]['tertip2x'] = $mRegSel[$i]['tertip2x'];
              $mRegSel[$nInd_mRegSel]['terid2xx'] = $mRegSel[$i]['terid2xx'];
              $mRegSel[$nInd_mRegSel]['puctipej'] = $mRegSel[$i]['puctipej'];
              $mRegSel[$nInd_mRegSel]['comvlrxx'] = $mRegSel[$i]['comvlrxx'];
              $mRegSel[$nInd_mRegSel]['comvlr01'] = $mRegSel[$i]['comvlr01'];
              $mRegSel[$nInd_mRegSel]['comvlr02'] = $mRegSel[$i]['comvlr02'];
              $mRegSel[$nInd_mRegSel]['comidcxx'] = $mRegSel[$i]['comidcxx'];
              $mRegSel[$nInd_mRegSel]['comcodcx'] = $mRegSel[$i]['comcodcx'];
              $mRegSel[$nInd_mRegSel]['comcsccx'] = $vDocDes['docidxxx'];
              $mRegSel[$nInd_mRegSel]['comseqcx'] = $vDocDes['docsufxx'];
              $mRegSel[$nInd_mRegSel]['comctocx'] = "{$mRegSel[$i][0]}";
              $mRegSel[$nInd_mRegSel]['comidc2x'] = "";
              $mRegSel[$nInd_mRegSel]['comcodc2'] = "";
              $mRegSel[$nInd_mRegSel]['comcscc2'] = "";
              $mRegSel[$nInd_mRegSel]['comseqc2'] = "";
              $mRegSel[$nInd_mRegSel]['comfecve'] = $_POST['dComVen'];
              $mRegSel[$nInd_mRegSel]['sucidxxx'] = $vDocDes['sucidxxx'];
              $mRegSel[$nInd_mRegSel]['docidxxx'] = $vDocDes['docidxxx'];
              $mRegSel[$nInd_mRegSel]['docsufxx'] = $vDocDes['docsufxx'];
              $mRegSel[$nInd_mRegSel]['tipcomxx'] = $mRegSel[$i]['tipcomxx'];
              $mRegSel[$nInd_mRegSel]['sucicaxx'] = $mRegSel[$i][13];

              if ($mRegSel[$i]['tipcomxx'] == "ANTICIPO") {
                if ($mRegSel[$i]['teridxxx'] == $mRegSel[$i]['terid2xx']) {
                  $mRegSel[$nInd_mRegSel]['teridxxx'] = $vDocDes['cliidxxx'];
                  $mRegSel[$nInd_mRegSel]['terid2xx'] = $vDocDes['cliidxxx'];
                } else {
                  $mRegSel[$nInd_mRegSel]['teridxxx'] = $vDocDes['cliidxxx'];
                  $mRegSel[$nInd_mRegSel]['terid2xx'] = $mRegSel[$i]['teridxxx'];
                }
                //Se deben intercambiar los nits en el registro del anticipo de origen, ya que en los ajustes
                //en el teridxxx va el dueño del DO y en el terid2x va de quien se recibio el pago
                //contrario a como se guarda en el recibo de caja
                $cAuxTerTip = $mRegSel[$i]['tertipxx'];
                $cAuxTerId  = $mRegSel[$i]['teridxxx'];
                $mRegSel[$i]['tertipxx'] = $mRegSel[$i]['tertip2x'];
                $mRegSel[$i]['teridxxx'] = $mRegSel[$i]['terid2xx'];
                $mRegSel[$i]['tertip2x'] = $cAuxTerTip;
                $mRegSel[$i]['terid2xx'] = $cAuxTerId;
              } else { 
                //Pagos por cuenta del cliente
                $mRegSel[$nInd_mRegSel]['teridxxx'] = $vDocDes['cliidxxx'];
              }
            }

            //si es una L de ajuste y el documento cruce dos es diferente de vacio, se debe dejar el mismo documento cruce dos
            if ($mRegSel[$i]['comidxxx'] == "L" && $mRegSel[$i]['comidc2x'] != "") {
              //No se hace nada
            } else {
              //Se asigna como documento cruce dos el documento origen
              $mRegSel[$i]['comctocx'] = "{$mRegSel[$i][0]}";
              $mRegSel[$i]['comidc2x'] = "{$mRegSel[$i][0]}";
              $mRegSel[$i]['comcodc2'] = "{$mRegSel[$i][1]}";
              $mRegSel[$i]['comcscc2'] = "{$mRegSel[$i][2]}";
              $mRegSel[$i]['comseqc2'] = "{$mRegSel[$i][4]}";
            }   

            //Cambiando el movimiento contable del pago origen
            //Adiciono en el documento cruce DOS el pago original para que el sistema lo cruce
            $mRegSel[$i]['commovxx'] = ($mRegSel[$i]['commovxx'] == "D") ? "C" : "D";
            
            //Sucursal ICA de la causacion
            $mRegSel[$i]['sucicaxx'] = $mRegSel[$i][13];

            for ($nR=0; $nR<count($mRegSel[$i]['retenxxx']); $nR++) {
              //Cambiando el movimiento contable del pago origen
              $mRegSel[$i]['retenxxx'][$nR]['commovxx'] = ($mRegSel[$i]['retenxxx'][$nR]['commovxx'] == "D") ? "C" : "D";
            }
          }
        }

        //Agrupando registros por factura, para crear un solo comprobante de ajuste por factura
        $mMovCon = array();
        for ($i=0; $i<count($mRegSel); $i++) {
          $nInd_mMovCon = count($mMovCon[$mRegSel[$i][15]]);
          $mMovCon[$mRegSel[$i][15]][$nInd_mMovCon] = $mRegSel[$i];
        }

        // echo "<pre>";
        // print_r($mMovCon);
        // echo "</pre>";

        $mDatos    = array(); // Vector con los datos para enviar al método fnGuardarAjuste
        $mDatosGen = array(); // Vector con los datos generales
        $mDatosDet = array(); // Vector con los datos de detalle

        $nCanCre = 0;

        foreach ($mMovCon as $cKey => $mDatCom) {

          //Inicializando varibales por cada comprobante
          $nDebitos    = 0;
          $nCreditos   = 0;
          $nDiferencia = 0;
          $nBasIva     = 0;
          $nValIva     = 0;
          $nSecuencia  = 1;

          $cSccId_SucId  = "";	// Sucursal del subcentro de costo de la cabecera
          $cSccId_DocId  = "";	// Do del subcentro de costo de la cabecera
          $cSccId_DocSuf = "";	// Sufijo del subcentro de costo de la cabecera
          /**
           * Datos contrapartida, se toma el del primer concepto
           */
          $mDatCon = array();

          /**
           * Array con la informacion del log
           */
          $vLog = array();

          //Detalle del documento
          for($i=0; $i<count($mDatCom);$i++) {

            //Buscando información del concepto de cobro
            // Validando que el concepto exista y que pertenezca al comprobante que estoy utilizando.
            $qCtoCon  = "SELECT fpar0119.*,fpar0115.* ";
            $qCtoCon .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
            $qCtoCon .= "WHERE ";
            $qCtoCon .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
            $qCtoCon .= "$cAlfa.fpar0119.ctoidxxx = \"{$mDatCom[$i]['ctoidxxx']}\" AND ";
            $qCtoCon .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
            $qCtoCon .= "ORDER BY $cAlfa.fpar0119.pucidxxx,$cAlfa.fpar0119.ctoidxxx LIMIT 0,1";
            $xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));

            if (mysql_num_rows($xCtoCon) == 0) {
              $qCtoCon  = "SELECT fpar0121.*,fpar0115.* ";
              $qCtoCon .= "FROM $cAlfa.fpar0121,$cAlfa.fpar0115 ";
              $qCtoCon .= "WHERE ";
              $qCtoCon .= "$cAlfa.fpar0121.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
              $qCtoCon .= "$cAlfa.fpar0121.ctoidxxx = \"{$mDatCom[$i]['ctoidxxx']}\" AND ";
              $qCtoCon .= "$cAlfa.fpar0121.regestxx = \"ACTIVO\" ";
              $qCtoCon .= "ORDER BY $cAlfa.fpar0121.pucidxxx,$cAlfa.fpar0121.ctoidxxx LIMIT 0,1";
              $xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
            }
            $vCtoCon = array();
            $vCtoCon = mysql_fetch_array($xCtoCon);

            $mDatosDet['cComSeq'  .$nSecuencia] = str_pad($nSecuencia, 3, "0", STR_PAD_LEFT);	// Secuencia
            $mDatosDet['cCtoId'   .$nSecuencia] = $mDatCom[$i]['ctoidxxx'];										// Id del Concepto
            $mDatosDet['cCtoDes'  .$nSecuencia] = $mDatCom[$i][10];												    // Descripcion del Concepto
            $mDatosDet['cCtoAnt'  .$nSecuencia] = $vCtoCon['ctoantxx'];												// Control Anticipado
            $mDatosDet['cComObs'  .$nSecuencia] = "";                                         // Observacion del Comprobante
            $mDatosDet['cComIdC'  .$nSecuencia] = $mDatCom[$i]['comidcxx'];	                  // Id Comprobante Cruce
            $mDatosDet['cComCodC' .$nSecuencia] = $mDatCom[$i]['comcodcx'];	                  // Codigo Comprobante Cruce
            $mDatosDet['cComCscC' .$nSecuencia] = $mDatCom[$i]['comcsccx'];	                  // Consecutivo Comprobante Cruce
            $mDatosDet['cComSeqC' .$nSecuencia] = $mDatCom[$i]['comseqcx'];	                  // Secuencia Comprobante Cruce
            $mDatosDet['cCcoId'   .$nSecuencia] = $mDatCom[$i]['ccoidxxx'];	                  // Centro de Costos
            $mDatosDet['cSccId'   .$nSecuencia] = $mDatCom[$i]['sccidxxx'];	                  // Sub Centro de Costos
            $mDatosDet['cComCtoC' .$nSecuencia] = $mDatCom[$i][0];	                          // Concepto Comprobante Cruce
            //Documento cruce dos
            $mDatosDet['cComIdCB' .$nSecuencia] = $mDatCom[$i]['comidc2x'];	                  // Id del Comprobante B
            $mDatosDet['cComCodCB'.$nSecuencia] = $mDatCom[$i]['comcodc2'];	                  // Código del Comprobante B
            $mDatosDet['cComCscCB'.$nSecuencia] = $mDatCom[$i]['comcscc2'];	                  // Consecutivo Uno B del Comprobante
            $mDatosDet['cComSeqCB'.$nSecuencia] = $mDatCom[$i]['comseqc2'];	                  // Secuencia B del Comprobante
            $mDatosDet['cComFecCB'.$nSecuencia] = $mDatCom[$i][5];	                          // Hidden (Fecha B del Comprobante)
            //Iva
            $mDatosDet['nComBRet'.$nSecuencia] = "";                                          // Base de Retencion
            $mDatosDet['nComBIva'.$nSecuencia] = ($mDatCom[$i]['comvlr01'] > 0) ? $mDatCom[$i]['comvlr01']+0 : "";	// Base Iva
            $mDatosDet['nComIva' .$nSecuencia] = ($mDatCom[$i]['comvlr02'] > 0) ? $mDatCom[$i]['comvlr02']+0 : "";	// Valor Iva
            //Valor
            $mDatosDet['nComVlr'  .$nSecuencia] = ($mDatCom[$i]['comvlrxx'] > 0) ? $mDatCom[$i]['comvlrxx']+0 : "";	// Valor del Comprobante Ejecucion Local
            $mDatosDet['nComVlrNF'.$nSecuencia] = ($mDatCom[$i]['comvlrnf'] > 0) ? $mDatCom[$i]['comvlrnf']+0 : "";	// Valor Movimiento en Ejecución NIF
            $mDatosDet['cComMov'  .$nSecuencia] = $mDatCom[$i]['commovxx'];			              // Movimiento Debito o Credito
            $mDatosDet['cComNit'  .$nSecuencia] = $vCtoCon['ctonitxx'];	                      // Hidden (Nit que va para SIIGO)
            //Terceros
            //Si el tipo de cuenta es de TERCERO se deben invertir los nits
            switch ($vCtoCon['ctonitxx']) {
              case "CLIENTE":
                $mDatosDet['cTerTip'  .$nSecuencia] = $mDatCom[$i]['tertipxx'];               // Hidden (Tipo de Tercero)
                $mDatosDet['cTerId'   .$nSecuencia] = $mDatCom[$i]['teridxxx'];               // Hidden (Id del Tercero)
                $mDatosDet['cTerNom'  .$nSecuencia] = $vNomTer["{$mDatCom[$i]['teridxxx']}"]; // Nombre del Tercero
                $mDatosDet['cTerTipB' .$nSecuencia] = $mDatCom[$i]['tertip2x'];               // Hidden (Tipo de Tercero Dos)
                $mDatosDet['cTerIdB'  .$nSecuencia] = $mDatCom[$i]['terid2xx'];               // Hidden (Id del Tercero Dos)
                $mDatosDet['cTerNomB' .$nSecuencia] = $vNomTer["{$mDatCom[$i]['terid2xx']}"]; // Nombre del Tercero B
              break;
              case "TERCERO":
                $mDatosDet['cTerTip'  .$nSecuencia] = $mDatCom[$i]['tertip2x'];               // Hidden (Tipo de Tercero Dos)
                $mDatosDet['cTerId'   .$nSecuencia] = $mDatCom[$i]['terid2xx'];               // Hidden (Id del Tercero Dos)
                $mDatosDet['cTerNom'  .$nSecuencia] = $vNomTer["{$mDatCom[$i]['terid2xx']}"]; // Nombre del Tercero B
                $mDatosDet['cTerTipB' .$nSecuencia] = $mDatCom[$i]['tertipxx'];               // Hidden (Tipo de Tercero)
                $mDatosDet['cTerIdB'  .$nSecuencia] = $mDatCom[$i]['teridxxx'];               // Hidden (Id del Tercero)
                $mDatosDet['cTerNomB' .$nSecuencia] = $vNomTer["{$mDatCom[$i]['teridxxx']}"]; // Nombre del Tercero
              break;
						}            
            //Cuenta
            $mDatosDet['cPucId'   .$nSecuencia] = $mDatCom[$i]['pucidxxx'];			              // Hidden (La Cuenta Contable)
            $mDatosDet['cPucDet'  .$nSecuencia] = $vCtoCon['pucdetxx'];	                      // Hidden (Detalle de la Cuenta)
            $mDatosDet['cPucTer'  .$nSecuencia] = $vCtoCon['pucterxx'];	                      // Hidden (Cuenta de Terceros)
            $mDatosDet['nPucBRet' .$nSecuencia] = $vCtoCon['pucbaret'];	                      // Hidden (Cuenta Retención)
            $mDatosDet['nPucRet'  .$nSecuencia] = $vCtoCon['pucretxx'];	                      // Hidden (Porcentaje de Retencion de la Cuenta)
            $mDatosDet['cPucNat'  .$nSecuencia] = $vCtoCon['pucnatxx'];	                      // Hidden (Naturaleza de la Cuenta)
            $mDatosDet['cPucInv'  .$nSecuencia] = $vCtoCon['pucinvxx'];	                      // Hidden (Cuenta de Inventarios)
            $mDatosDet['cPucCco'  .$nSecuencia] = $vCtoCon['puccccxx'];	                      // Hidden (Aplica para la Cuenta Centro de Costos)
            $mDatosDet['cPucDoSc' .$nSecuencia] = $vCtoCon['pucdoscc'];	                      // Hidden (Aplica DO para Subcentro de Costo)
            $mDatosDet['cPucTipEj'.$nSecuencia] = $mDatCom[$i]['puctipej'];			              // Hidden (Tipo de Ejecucion(L-Local,N-Niif,vacio-Ambas))
            $mDatosDet['cComVlr1' .$nSecuencia] = $vCtoCon['ctovlr01'];	                      // Hidden (Valor Uno)
            $mDatosDet['cComVlr2' .$nSecuencia] = $vCtoCon['ctovlr02'];	                      // Hidden (Valor Dos)
            $mDatosDet['cComFac'  .$nSecuencia] = $mDatCom[$i]['comfacxx'];			              // Hidden (Comfac)
            $mDatosDet['cComComLi'.$nSecuencia] = $mDatCom[$i]['comcomli'];			              // Hidden (Comprobante de Liquidación Do)
            $mDatosDet['cSucId'   .$nSecuencia] = $mDatCom[$i]['sucidxxx'];			              // Hidden (Sucursal)
            $mDatosDet['cDocId'   .$nSecuencia] = $mDatCom[$i]['docidxxx'];			              // Hidden (Do)
            $mDatosDet['cDocSuf'  .$nSecuencia] = $mDatCom[$i]['docsufxx'];			              // Hidden (Sufijo)
            $mDatosDet['cComEst'  .$nSecuencia] = "NO";									                      // Hidden (Estado Recibo de Caja)
            $mDatosDet['cSucIca'  .$nSecuencia] = $mDatCom[$i]['sucicaxx'];			              // Hidden (Sucursal Ica)
            //Centro y Subcentro de costo del comprobante
            if ($cSccId_SucId == "") {
              $cSccId_SucId  = $mDatCom[$i]['sucidxxx'];	                                    // Sucursal del subcentro de costo de la cabecera
              $cSccId_DocId  = $mDatCom[$i]['docidxxx'];	                                    // Do del subcentro de costo de la cabecera
              $cSccId_DocSuf = $mDatCom[$i]['docsufxx'];	                                    // Sufijo del subcentro de costo de la cabecera
            }
            $nSecuencia++;

            $nDebitos  += ($mDatCom[$i]['commovxx'] == "D") ? $mDatCom[$i]['comvlrxx'] : 0;
            $nCreditos += ($mDatCom[$i]['commovxx'] == "C") ? $mDatCom[$i]['comvlrxx'] : 0;

            $nBasIva   += ($mDatCom[$i]['comvlr01'] > 0) ? $mDatCom[$i]['comvlr01']+0 : 0;
            $nValIva   += ($mDatCom[$i]['comvlr02'] > 0) ? $mDatCom[$i]['comvlr02']+0 : 0;

            //Insertando registro de Log
            if ($cKey == $mDatCom[$i]['comidxxx']."~".$mDatCom[$i]['comcodxx']."~".$mDatCom[$i]['comcscxx']."~".$mDatCom[$i]['comcsc2x']) {
              $vLog['sucidorx'] = $_POST['cSucIdOri'];                                        //Id de la Sucursal Origen',
              $vLog['docidorx'] = $_POST['cDocNroOri'];                                       //Id del DO Origen',
              $vLog['docsufor'] = $_POST['cDocSufOri'];                                       //Sufijo del DO Origen',
              $vLog['suciddex'] = $_POST['cSucIdDes'];                                        //Id de la Sucursal Destino',
              $vLog['dociddex'] = $_POST['cDocNroDes'];                                       //Id del DO Destino',
              $vLog['docsufde'] = $_POST['cDocSufDes'];                                       //Sufijo del DO Destino',
              $vLog['comidorx'] = $mDatCom[$i]['comidxxx'];                                   //Id del Comprobante Origen',
              $vLog['comcodor'] = $mDatCom[$i]['comcodxx'];                                   //Codigo del Comprobante Origen',
              $vLog['comcscor'] = $mDatCom[$i]['comcscxx'];                                   //Consecutivo Uno del Comprobante Origen',
              $vLog['comcsc2o'] = $mDatCom[$i]['comcsc2x'];                                   //Consecutivo Dos del Comprobante Origen',
              $vLog['comcsc3o'] = $mDatCom[$i]['comcsc3x'];                                   //Consecutivo Tres Origen',
              $vLog['comfecor'] = $mDatCom[$i]['comfecxx'];                                   //Fecha del Comprobante Origen',
              $vLog['comhoror'] = $mDatCom[$i]['reghcrex'];                                   //Hora del Comprobante Origen',
              $vLog['cliidorx'] = $vDocOri['cliidxxx'];                                       //Nit Cliente Origen',
              $vLog['cliiddex'] = $vDocDes['cliidxxx'];                                       //Nit Cliente Destino',
              $vLog['comvlrxx'] = ($mDatCom[$i]['commovxx'] == "D") ? $mDatCom[$i]['comvlrxx'] : ($mDatCom[$i]['comvlrxx']*-1); //Valor del Comprobante',
            }

            //Retenciones
            for ($nR=0; $nR<count($mDatCom[$i]['retenxxx']); $nR++) {
              //Buscando información del concepto de cobro
              // Validando que el concepto exista y que pertenezca al comprobante que estoy utilizando.
              $qCtoCon  = "SELECT fpar0119.*,fpar0115.* ";
              $qCtoCon .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
              $qCtoCon .= "WHERE ";
              $qCtoCon .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
              $qCtoCon .= "$cAlfa.fpar0119.ctoidxxx = \"{$mDatCom[$i]['retenxxx'][$nR]['ctoidxxx']}\" AND ";
              $qCtoCon .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
              $qCtoCon .= "ORDER BY $cAlfa.fpar0119.pucidxxx,$cAlfa.fpar0119.ctoidxxx LIMIT 0,1";
              $xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
              $vCtoCon = array();
              $vCtoCon = mysql_fetch_array($xCtoCon);

              $mDatosDet['cComSeq'  .$nSecuencia] = str_pad($nSecuencia, 3, "0", STR_PAD_LEFT);	// Secuencia
              $mDatosDet['cCtoId'   .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['ctoidxxx'];	// Id del Concepto
              $mDatosDet['cCtoDes'  .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['ctodesxx'];	// Descripcion del Concepto
              $mDatosDet['cCtoAnt'  .$nSecuencia] = $vCtoCon['ctoantxx'];												// Control Anticipado
              $mDatosDet['cComObs'  .$nSecuencia] = "";	                                        // Observacion del Comprobante
              $mDatosDet['cComIdC'  .$nSecuencia] = "";	                                        // Id Comprobante Cruce
              $mDatosDet['cComCodC' .$nSecuencia] = "";	                                        // Codigo Comprobante Cruce
              $mDatosDet['cComCscC' .$nSecuencia] = "";	                                        // Consecutivo Comprobante Cruce
              $mDatosDet['cComSeqC' .$nSecuencia] = "";	                                        // Secuencia Comprobante Cruce
              $mDatosDet['cCcoId'   .$nSecuencia] = "";	                                        // Centro de Costos
              $mDatosDet['cSccId'   .$nSecuencia] = "";	                                        // Sub Centro de Costos
              $mDatosDet['cComCtoC' .$nSecuencia] = $mDatCom[$i][0];	                          // Concepto Comprobante Cruce
              //Documento cruce dos
              $mDatosDet['cComIdCB' .$nSecuencia] = "";	                                        // Id del Comprobante B
              $mDatosDet['cComCodCB'.$nSecuencia] = "";	                                        // Código del Comprobante B
              $mDatosDet['cComCscCB'.$nSecuencia] = "";	                                        // Consecutivo Uno B del Comprobante
              $mDatosDet['cComSeqCB'.$nSecuencia] = "";	                                        // Secuencia B del Comprobante
              $mDatosDet['cComFecCB'.$nSecuencia] = "";	                                        // Hidden (Fecha B del Comprobante)
              //Base de retencion
              $mDatosDet['nComBRet'.$nSecuencia] = ($mDatCom[$i]['retenxxx'][$nR]['comvlr01'] > 0) ? $mDatCom[$i]['retenxxx'][$nR]['comvlr01']+0 : ""; // Base Retención
              $mDatosDet['nComBIva'.$nSecuencia] = "";	                                        // Base Iva
              $mDatosDet['nComIva' .$nSecuencia] = "";	                                        // Valor Iva
              //Valor
              $mDatosDet['nComVlr'  .$nSecuencia] = ($mDatCom[$i]['retenxxx'][$nR]['comvlrxx'] > 0) ? $mDatCom[$i]['retenxxx'][$nR]['comvlrxx']+0 : "";	// Valor del Comprobante Ejecucion Local
              $mDatosDet['nComVlrNF'.$nSecuencia] = ($mDatCom[$i]['retenxxx'][$nR]['comvlrnf'] > 0) ? $mDatCom[$i]['retenxxx'][$nR]['comvlrnf']+0 : "";	// Valor Movimiento en Ejecución NIF
              $mDatosDet['cComMov'  .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['commovxx'];	// Movimiento Debito o Credito
              $mDatosDet['cComNit'  .$nSecuencia] = $vCtoCon['ctonitxx'];	                      // Hidden (Nit que va para SIIGO)
              //Terceros
              //Si el tipo de cuenta es de TERCERO se deben invertir los nits
              switch ($vCtoCon['ctonitxx']) {
                case "CLIENTE":
                  $mDatosDet['cTerTip'  .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['tertipxx'];                // Hidden (Tipo de Tercero)
                  $mDatosDet['cTerId'   .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['teridxxx'];                // Hidden (Id del Tercero)
                  $mDatosDet['cTerNom'  .$nSecuencia] = $vNomTer["{$mDatCom[$i]['retenxxx'][$nR]['teridxxx']}"];  // Nombre del Tercero
                  $mDatosDet['cTerTipB' .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['tertip2x'];                // Hidden (Tipo de Tercero Dos)
                  $mDatosDet['cTerIdB'  .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['terid2xx'];                // Hidden (Id del Tercero Dos)
                  $mDatosDet['cTerNomB' .$nSecuencia] = $vNomTer["{$mDatCom[$i]['retenxxx'][$nR]['terid2xx']}"];  // Nombre del Tercero B
                break;
                case "TERCERO":
                  $mDatosDet['cTerTip'  .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['tertip2x'];                // Hidden (Tipo de Tercero Dos)
                  $mDatosDet['cTerId'   .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['terid2xx'];                // Hidden (Id del Tercero Dos)
                  $mDatosDet['cTerNom'  .$nSecuencia] = $vNomTer["{$mDatCom[$i]['retenxxx'][$nR]['terid2xx']}"];  // Nombre del Tercero B
                  $mDatosDet['cTerTipB' .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['tertipxx'];                // Hidden (Tipo de Tercero)
                  $mDatosDet['cTerIdB'  .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['teridxxx'];                // Hidden (Id del Tercero)
                  $mDatosDet['cTerNomB' .$nSecuencia] = $vNomTer["{$mDatCom[$i]['retenxxx'][$nR]['teridxxx']}"];  // Nombre del Tercero
                break;
              }
              //Cuenta
              $mDatosDet['cPucId'   .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['pucidxxx'];			              // Hidden (La Cuenta Contable)
              $mDatosDet['cPucDet'  .$nSecuencia] = $vCtoCon['pucdetxx'];	                                        // Hidden (Detalle de la Cuenta)
              $mDatosDet['cPucTer'  .$nSecuencia] = $vCtoCon['pucterxx'];	                                        // Hidden (Cuenta de Terceros)
              $mDatosDet['nPucBRet' .$nSecuencia] = $vCtoCon['pucbaret'];	                                        // Hidden (Cuenta Retención)
              $mDatosDet['nPucRet'  .$nSecuencia] = $vCtoCon['pucretxx'];	                                        // Hidden (Porcentaje de Retencion de la Cuenta)
              $mDatosDet['cPucNat'  .$nSecuencia] = $vCtoCon['pucnatxx'];	                                        // Hidden (Naturaleza de la Cuenta)
              $mDatosDet['cPucInv'  .$nSecuencia] = $vCtoCon['pucinvxx'];	                                        // Hidden (Cuenta de Inventarios)
              $mDatosDet['cPucCco'  .$nSecuencia] = $vCtoCon['puccccxx'];	                                        // Hidden (Aplica para la Cuenta Centro de Costos)
              $mDatosDet['cPucDoSc' .$nSecuencia] = $vCtoCon['pucdoscc'];	                                        // Hidden (Aplica DO para Subcentro de Costo)
              $mDatosDet['cPucTipEj'.$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['puctipej'];			              // Hidden (Tipo de Ejecucion(L-Local,N-Niif,vacio-Ambas))
              $mDatosDet['cComVlr1' .$nSecuencia] = $vCtoCon['ctovlr01'];	                                        // Hidden (Valor Uno)
              $mDatosDet['cComVlr2' .$nSecuencia] = $vCtoCon['ctovlr02'];	                                        // Hidden (Valor Dos)
              $mDatosDet['cComFac'  .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['comfacxx'];			              // Hidden (Comfac)
              $mDatosDet['cComComLi'.$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['comcomli'];			              // Hidden (Comprobante de Liquidación Do)
              $mDatosDet['cSucId'   .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['sucidxxx'];			              // Hidden (Sucursal)
              $mDatosDet['cDocId'   .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['docidxxx'];			              // Hidden (Do)
              $mDatosDet['cDocSuf'  .$nSecuencia] = $mDatCom[$i]['retenxxx'][$nR]['docsufxx'];			              // Hidden (Sufijo)
              $mDatosDet['cComEst'  .$nSecuencia] = "NO";									                                        // Hidden (Estado Recibo de Caja)
              $nSecuencia++;

              $nDebitos  += ($mDatCom[$i]['retenxxx'][$nR]['commovxx'] == "D") ? $mDatCom[$i]['retenxxx'][$nR]['comvlrxx'] : 0;
              $nCreditos += ($mDatCom[$i]['retenxxx'][$nR]['commovxx'] == "C") ? $mDatCom[$i]['retenxxx'][$nR]['comvlrxx'] : 0;
            }

            //Datos contrapartida
            if (count($mDatCon) == 0 && count($mDatCom[$i]['contrapa']) > 0) {
              $mDatCon[count($mDatCon)] = $mDatCom[$i]['contrapa'][0];
            }
          }

          //Si hay diferencias entre debitos y creditos se debe crear la cuenta por cobrar o por pagar correspondiente
          if (round($nDebitos,5) != round($nCreditos,5)) {

            //Diferencia 
            $nDiferencia = round(($nDebitos - $nCreditos),2);

            //Buscando si el comprobante inicial aun tiene saldo en la cxc o cxp creada, 
            //si es asi la diferencia se suma a esta
            //sino se crea una cxc o cxp con el comprobante que se esta creando
            $nEncCon = 0;
            if (count($mContrapartida[$cKey]) > 0) {
              //Buscando información del concepto de cobro
              // Validando que el concepto exista y que pertenezca al comprobante que estoy utilizando.
              $qCtoCon  = "SELECT fpar0119.*,fpar0115.* ";
              $qCtoCon .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
              $qCtoCon .= "WHERE ";
              $qCtoCon .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
              $qCtoCon .= "$cAlfa.fpar0119.ctoidxxx = \"{$mContrapartida[$cKey][0]['ctoidxxx']}\" AND ";
              $qCtoCon .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
              $qCtoCon .= "ORDER BY $cAlfa.fpar0119.pucidxxx,$cAlfa.fpar0119.ctoidxxx LIMIT 0,1";
              $xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
              $vCtoCon = array();
              $vCtoCon = mysql_fetch_array($xCtoCon);
              switch($vCtoCon['pucdetxx']) {
                case "C":
                  $qComCxC  = "SELECT * ";
                  $qComCxC .= "FROM $cAlfa.fcxc0000 ";
                  $qComCxC .= "WHERE ";
                  $qComCxC .= "comidxxx = \"{$mContrapartida[$cKey][0]['comidcxx']}\" AND ";
                  $qComCxC .= "comcodxx = \"{$mContrapartida[$cKey][0]['comcodcx']}\" AND ";
                  $qComCxC .= "comcscxx = \"{$mContrapartida[$cKey][0]['comcsccx']}\" AND ";
                  $qComCxC .= "comseqxx = \"{$mContrapartida[$cKey][0]['comseqcx']}\" AND ";
                  $qComCxC .= "teridxxx = \"{$mContrapartida[$cKey][0]['teridxxx']}\" AND ";
                  $qComCxC .= "pucidxxx = \"{$mContrapartida[$cKey][0]['pucidxxx']}\" AND ";
                  $qComCxC .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xComCxC  = f_MySql("SELECT","",$qComCxC,$xConexion01,"");
                  // f_Mensaje(__FILE__,__LINE__,$qComCxC." ~ ".mysql_num_rows($xComCxC));
                  if(mysql_num_rows($xComCxC) > 0) {
                    $nEncCon = 1;
                  }
                break;
                case "P":
                  $qComCxP  = "SELECT * ";
                  $qComCxP .= "FROM $cAlfa.fcxp0000 ";
                  $qComCxP .= "WHERE ";
                  $qComCxP .= "comidxxx = \"{$mContrapartida[$cKey][0]['comidcxx']}\" AND ";
                  $qComCxP .= "comcodxx = \"{$mContrapartida[$cKey][0]['comcodcx']}\" AND ";
                  $qComCxP .= "comcscxx = \"{$mContrapartida[$cKey][0]['comcsccx']}\" AND ";
                  $qComCxP .= "comseqxx = \"{$mContrapartida[$cKey][0]['comseqcx']}\" AND ";
                  $qComCxP .= "teridxxx = \"{$mContrapartida[$cKey][0]['teridxxx']}\" AND ";
                  $qComCxP .= "pucidxxx = \"{$mContrapartida[$cKey][0]['pucidxxx']}\" AND ";
                  $qComCxP .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xComCxP  = f_MySql("SELECT","",$qComCxP,$xConexion01,"");
                  // f_Mensaje(__FILE__,__LINE__,$qComCxP." ~ ".mysql_num_rows($xComCxP));
                  if(mysql_num_rows($xComCxP) > 0) {
                    $nEncCon = 1;
                  }
                break;
                default:
                  //No hace nada
                break;
              }
            }

            if ($nEncCon == 1) {
              //El comprobante origen todavia tiene saldo, debe afectarse la cxc o cxp del comprobante original
              $cCtoId   = $mContrapartida[$cKey][0]['ctoidxxx'];      // Id del Concepto
              $cComIdC  = $mContrapartida[$cKey][0]['comidcxx'];	    // Id Comprobante Cruce
              $cComCodC = $mContrapartida[$cKey][0]['comcodcx'];	    // Codigo Comprobante Cruce
              $cComCscC = $mContrapartida[$cKey][0]['comcsccx'];	    // Consecutivo Comprobante Cruce
              $cComSeqC = $mContrapartida[$cKey][0]['comseqcx'];	    // Secuencia Comprobante Cruce
              $cCcoId   = $mContrapartida[$cKey][0]['ccoidxxx'];	    // Centro de Costos
              $cSccId   = $mContrapartida[$cKey][0]['sccidxxx'];	    // Sub Centro de Costos
              $nComVlr  = abs($nDiferencia);	                        // Valor del Comprobante Ejecucion Local
              $nComVlrNF= 0;	                                        // Valor Movimiento en Ejecución NIF
              $cComMov  = ($nDiferencia > 0) ? "C" : "D";			        // Movimiento Debito o Credito
              $cTerTip  = $mContrapartida[$cKey][0]['tertipxx'];      // Hidden (Tipo de Tercero)
              $cTerId   = $mContrapartida[$cKey][0]['teridxxx'];      // Hidden (Id del Tercero)
              $cTerTipB = $mContrapartida[$cKey][0]['tertip2x'];      // Hidden (Tipo de Tercero Dos)
              $cTerIdB  = $mContrapartida[$cKey][0]['terid2xx'];      // Hidden (Id del Tercero Dos)
              $cPucId   = $mContrapartida[$cKey][0]['pucidxxx'];			// Hidden (La Cuenta Contable)
              $cPucTipEj= $mContrapartida[$cKey][0]['puctipej'];			// Hidden (Tipo de Ejecucion(L-Local,N-Niif,vacio-Ambas))
              $cComFac  = $mContrapartida[$cKey][0]['comfacxx'];			// Hidden (Comfac)
              $cComComLi= $mContrapartida[$cKey][0]['comcomli'];			// Hidden (Comprobante de Liquidación Do)
              $cSucId   = $mContrapartida[$cKey][0]['sucidxxx'];			// Hidden (Sucursal)
              $cDocId   = $mContrapartida[$cKey][0]['docidxxx'];			// Hidden (Do)
              $cDocSuf  = $mContrapartida[$cKey][0]['docsufxx'];			// Hidden (Sufijo)
            } else {
              //Se crea la cxc o cxp al comprobante origen
              $cCtoId   = $mDatCon[0]['ctoidxxx'];            // Id del Concepto
              $cComIdC  = $mDatCon[0]['comidcxx'];	          // Id Comprobante Cruce
              $cComCodC = $mDatCon[0]['comcodcx'];	          // Codigo Comprobante Cruce
              $cComCscC = $mDatCon[0]['comcsccx'];	          // Consecutivo Comprobante Cruce
              $cComSeqC = $mDatCon[0]['comseqcx'];	          // Secuencia Comprobante Cruce
              $cCcoId   = $mDatCon[0]['ccoidxxx'];	          // Centro de Costos
              $cSccId   = $mDatCon[0]['sccidxxx'];	          // Sub Centro de Costos
              $cComCtoC = $mDatCon[0]['comidxxx'];	          // Concepto Comprobante Cruce
              $nComVlr  = abs($nDiferencia);	                // Valor del Comprobante Ejecucion Local
              $nComVlrNF= 0;	                                // Valor Movimiento en Ejecución NIF
              $cComMov  = ($nDiferencia > 0) ? "C" : "D";			// Movimiento Debito o Credito
              $cTerTip  = $mDatCon[0]['tertipxx'];            // Hidden (Tipo de Tercero)
              $cTerId   = $mDatCon[0]['teridxxx'];            // Hidden (Id del Tercero)
              $cTerTipB = $mDatCon[0]['tertip2x'];            // Hidden (Tipo de Tercero Dos)
              $cTerIdB  = $mDatCon[0]['terid2xx'];            // Hidden (Id del Tercero Dos)
              $cPucId   = $mDatCon[0]['pucidxxx'];			      // Hidden (La Cuenta Contable)
              $cPucTipEj= $mDatCon[0]['puctipej'];			      // Hidden (Tipo de Ejecucion(L-Local,N-Niif,vacio-Ambas))
              $cComFac  = $mDatCon[0]['comfacxx'];			      // Hidden (Comfac)
              $cComComLi= $mDatCon[0]['comcomli'];			      // Hidden (Comprobante de Liquidación Do)
              $cSucId   = $mDatCon[0]['sucidxxx'];			      // Hidden (Sucursal)
              $cDocId   = $mDatCon[0]['docidxxx'];			      // Hidden (Do)
              $cDocSuf  = $mDatCon[0]['docsufxx'];			      // Hidden (Sufijo)

              //buscando los datos del concepto de cobro de la contrapatidad
              $qCtoCon  = "SELECT fpar0119.*,fpar0115.* ";
              $qCtoCon .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
              $qCtoCon .= "WHERE ";
              $qCtoCon .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
              $qCtoCon .= "$cAlfa.fpar0119.ctoidxxx = \"{$mDatCon[0]['ctoidxxx']}\" AND ";
              $qCtoCon .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
              $qCtoCon .= "ORDER BY $cAlfa.fpar0119.pucidxxx,$cAlfa.fpar0119.ctoidxxx LIMIT 0,1";
              $xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
              $vCtoCon = array();
              $vCtoCon = mysql_fetch_array($xCtoCon);
            }

            $mDatosDet['cComSeq'  .$nSecuencia] = str_pad($nSecuencia, 3, "0", STR_PAD_LEFT);	// Secuencia
            $mDatosDet['cCtoId'   .$nSecuencia] = $cCtoId;														        // Id del Concepto
            $mDatosDet['cCtoDes'  .$nSecuencia] = "";												                  // Descripcion del Concepto
            $mDatosDet['cCtoAnt'  .$nSecuencia] = $vCtoCon['ctoantxx'];			                  // Control Anticipado
            $mDatosDet['cComObs'  .$nSecuencia] = "";	                                        // Observacion del Comprobante
            $mDatosDet['cComIdC'  .$nSecuencia] = $cComIdC;	                                  // Id Comprobante Cruce
            $mDatosDet['cComCodC' .$nSecuencia] = $cComCodC;	                                // Codigo Comprobante Cruce
            $mDatosDet['cComCscC' .$nSecuencia] = $cComCscC;	                                // Consecutivo Comprobante Cruce
            $mDatosDet['cComSeqC' .$nSecuencia] = $cComSeqC;	                                // Secuencia Comprobante Cruce
            $mDatosDet['cCcoId'   .$nSecuencia] = $cCcoId;	                                  // Centro de Costos
            $mDatosDet['cSccId'   .$nSecuencia] = $cSccId;	                                  // Sub Centro de Costos
            $mDatosDet['cComCtoC' .$nSecuencia] = $mDatCom[$i][0];	                          // Concepto Comprobante Cruce
            //Documento cruce dos
            $mDatosDet['cComIdCB' .$nSecuencia] = "";	                                        // Id del Comprobante B
            $mDatosDet['cComCodCB'.$nSecuencia] = "";	                                        // Código del Comprobante B
            $mDatosDet['cComCscCB'.$nSecuencia] = "";	                                        // Consecutivo Uno B del Comprobante
            $mDatosDet['cComSeqCB'.$nSecuencia] = "";	                                        // Secuencia B del Comprobante
            $mDatosDet['cComFecCB'.$nSecuencia] = "";	                                        // Hidden (Fecha B del Comprobante)
            //Iva
            $mDatosDet['nComBRet'.$nSecuencia]  = "";                                         // Base de Retencion
            $mDatosDet['nComBIva'.$nSecuencia]  = "";	                                        // Base Iva
            $mDatosDet['nComIva' .$nSecuencia]  = "";	                                        // Valor Iva
            $mDatosDet['nComVlr'  .$nSecuencia] = $nComVlr;	                                  // Valor del Comprobante Ejecucion Local
            $mDatosDet['nComVlrNF'.$nSecuencia] = $nComVlrNF;	                                // Valor Movimiento en Ejecución NIF
            $mDatosDet['cComMov'  .$nSecuencia] = $cComMov;			                              // Movimiento Debito o Credito
            $mDatosDet['cComNit'  .$nSecuencia] = $vCtoCon['ctonitxx'];	                      // Hidden (Nit que va para SIIGO)
            //Si el tipo de cuenta es de TERCERO se deben invertir los nits
            switch ($vCtoCon['ctonitxx']) {
              case "CLIENTE":
                $mDatosDet['cTerTip'  .$nSecuencia] = $cTerTip;                               // Hidden (Tipo de Tercero)
                $mDatosDet['cTerId'   .$nSecuencia] = $cTerId;                                // Hidden (Id del Tercero)
                $mDatosDet['cTerNom'  .$nSecuencia] = $vNomTer["$cTerId"];                    // Nombre del Tercero
                $mDatosDet['cTerTipB' .$nSecuencia] = $cTerTipB;                              // Hidden (Tipo de Tercero Dos)
                $mDatosDet['cTerIdB'  .$nSecuencia] = $cTerIdB;                               // Hidden (Id del Tercero Dos)
                $mDatosDet['cTerNomB' .$nSecuencia] = $vNomTer["$cTerIdB"];                   // Nombre del Tercero B              
              break;
              case "TERCERO":
                $mDatosDet['cTerTip'  .$nSecuencia] = $cTerTipB;                              // Hidden (Tipo de Tercero Dos)
                $mDatosDet['cTerId'   .$nSecuencia] = $cTerIdB;                               // Hidden (Id del Tercero Dos)
                $mDatosDet['cTerNom'  .$nSecuencia] = $vNomTer["$cTerIdB"];                   // Nombre del Tercero B 
                $mDatosDet['cTerTipB' .$nSecuencia] = $cTerTip;                               // Hidden (Tipo de Tercero)
                $mDatosDet['cTerIdB'  .$nSecuencia] = $cTerId;                                // Hidden (Id del Tercero)
                $mDatosDet['cTerNomB' .$nSecuencia] = $vNomTer["$cTerId"];                    // Nombre del Tercero
              break;
            }
            
            $mDatosDet['cPucId'   .$nSecuencia] = $cPucId;			                              // Hidden (La Cuenta Contable)
            $mDatosDet['cPucDet'  .$nSecuencia] = $vCtoCon['pucdetxx'];	                      // Hidden (Detalle de la Cuenta)
            $mDatosDet['cPucTer'  .$nSecuencia] = $vCtoCon['pucterxx'];	                      // Hidden (Cuenta de Terceros)
            $mDatosDet['nPucBRet' .$nSecuencia] = $vCtoCon['pucbaret'];	                      // Hidden (Cuenta Retención)
            $mDatosDet['nPucRet'  .$nSecuencia] = $vCtoCon['pucretxx'];	                      // Hidden (Porcentaje de Retencion de la Cuenta)
            $mDatosDet['cPucNat'  .$nSecuencia] = $vCtoCon['pucnatxx'];	                      // Hidden (Naturaleza de la Cuenta)
            $mDatosDet['cPucInv'  .$nSecuencia] = $vCtoCon['pucinvxx'];	                      // Hidden (Cuenta de Inventarios)
            $mDatosDet['cPucCco'  .$nSecuencia] = $vCtoCon['puccccxx'];	                      // Hidden (Aplica para la Cuenta Centro de Costos)
            $mDatosDet['cPucDoSc' .$nSecuencia] = $vCtoCon['pucdoscc'];	                      // Hidden (Aplica DO para Subcentro de Costo)
            $mDatosDet['cPucTipEj'.$nSecuencia] = $cPucTipEj;			                            // Hidden (Tipo de Ejecucion(L-Local,N-Niif,vacio-Ambas))
            $mDatosDet['cComVlr1' .$nSecuencia] = $vCtoCon['ctovlr01'];	                      // Hidden (Valor Uno)
            $mDatosDet['cComVlr2' .$nSecuencia] = $vCtoCon['ctovlr02'];	                      // Hidden (Valor Dos)
            $mDatosDet['cComFac'  .$nSecuencia] = $cComFac;			                              // Hidden (Comfac)
            $mDatosDet['cComComLi'.$nSecuencia] = $cComComLi;			                            // Hidden (Comprobante de Liquidación Do)
            $mDatosDet['cSucId'   .$nSecuencia] = $cSucId;			                              // Hidden (Sucursal)
            $mDatosDet['cDocId'   .$nSecuencia] = $cDocId;			                              // Hidden (Do)
            $mDatosDet['cDocSuf'  .$nSecuencia] = $cDocSuf;			                              // Hidden (Sufijo)
            $mDatosDet['cComEst'  .$nSecuencia] = "SI";									                      // Hidden (Estado Recibo de Caja)
            $nSecuencia++;

            $nDebitos  += ($cComMov == "D") ? $nComVlr : 0;
            $nCreditos += ($cComMov == "C") ? $nComVlr : 0;
          }

          //Diferencia 
          $nDiferencia = round(($nDebitos - $nCreditos),2);

          //Datos generales comprobante
          $mDatosGen['cModo']	        =	"NUEVO";				                      // Modo de grabado (NUEVO,ANTERIOR,EDITAR,BORRAR,LEGALIZAR)
          $mDatosGen['cModo2']	      =	"NUEVO";				                      // Modo liquidado o Facturado
          $mDatosGen['cComTras']	    =	"SI";				                          // Indica que es un comprobante de traslado
          $mDatosGen['nSecuencia']	  =	($nSecuencia-1);			                // Secuencia de la grilla, se resta 1, porque inicio en 1
          $mDatosGen['nTimesSave']	  =	$_POST['nTimesSave'];			            // cantidad de click
          $mDatosGen['cComFac'] 	    =	"";				                            // comfacxx de la cabecera - Comprobante con el que fue Facturado
          $mDatosGen['cComTco']	      =	$vValCom['comtcoxx'];				          // Tipo de Consecutivo para el comprobante (MANUAL/AUTOMATICO)
          $mDatosGen['cComCco']	      =	$vValCom['comccoxx'];				          // Control Consecutivo para el comprobante (MENSUAL/ANUAL/INDEFINIDO)
          $mDatosGen['dComFec_Ant'] 	=	$_POST['dComFec'];		                // Fecha Periodo Anterior
          $mDatosGen['cComId']	      =	$vValCom['comidxxx'];					        // Id comprobante
          $mDatosGen['cComCod']	      =	$vValCom['comcodxx'];				          // Codigo comprobante
          $mDatosGen['cComDes']	      =	$vValCom['comdesxx'];				          // Descripcion Comprobante
          $mDatosGen['cCcoId']	      =	$mCabecera[$cKey]['ccoidxxx'];				// Centro de Costo cabecera
          $mDatosGen['cSccId']	      =	$mCabecera[$cKey]['sccidxxx'];				// Subcentro de Costo cabecera
          $mDatosGen['cSccId_SucId']  =	$cSccId_SucId;	                      // Sucursal del subcentro de costo de la cabecera
          $mDatosGen['cSccId_DocId']  =	$cSccId_DocId;	                      // Do del subcentro de costo de la cabecera
          $mDatosGen['cSccId_DocSuf'] =	$cSccId_DocSuf;	                      // Sufijo del subcentro de costo de la cabecera
          $mDatosGen['dComFec']	      =	$_POST['dComFec'];				            // Fecha del comprobante
          $mDatosGen['tRegHCre']	    =	$_POST['tRegHCre'];				            // hora del comprobante
          $mDatosGen['cComCsc']	      =	"{$mDatCom[0][2]}";                   // consecutivo uno del comprobante
          $mDatosGen['cComCsc2']  	  =	"";				                            // consecutivo dos del comprobante
          $mDatosGen['cComCsc3']  	  =	"";				                            // consecutivo tres del comprobante
          $mDatosGen['dComVen']  	    =	$_POST['dComVen'];				            // Fecha de vencimiento del comprobante
          $mDatosGen['cTerTip']	      =	$mDatCom[0]['tertipxx'];				      // tipo de tercero cabecera
          $mDatosGen['cTerId']	      =	$vDocDes['cliidxxx'];					        // id tercero cabecera
          $mDatosGen['cTerTipB']	    =	$mCabecera[$cKey]['tertip2x'];				// tipo de tercero B cabecera
          $mDatosGen['cTerIdB']	      =	$mCabecera[$cKey]['terid2xx'];				// id tercero B cabecera
          $mDatosGen['cComObs']	      =	$mCabecera[$cKey]['comobsxx'];				// Observacion cabecera
          $mDatosGen['nTasaCambio']	  =	$_POST['nTasaCambio'];		            // Tasa de Cambio
          $mDatosGen['cRegEst']	      =	"ACTIVO";				                      // Estado del comprobange
          $mDatosGen['dRegFMod']	    =	$_POST['tRegHCre'];				            // Fecha de Modificacion del comprobante
          $mDatosGen['nComVlr01']	    =	$nBasIva; 			                      // Base total
          $mDatosGen['nComVlr02']	    =	$nValIva; 			                      // Iva de la Base total
          $mDatosGen['nDebitos']	    =	$nDebitos;				                    // Debitos
          $mDatosGen['nCreditos']	    =	$nCreditos;			                      // Creditos
          $mDatosGen['nDiferencia'] 	=	$nDiferencia;		                      // Diferencia debitos - creditos

          $mDatos  = array_merge($mDatosGen, $mDatosDet);

          //OC-14564
          //Buscando el nuevo consecutivo, para guardar ajustes con traslados parciales
          //Buscando secuencia actual del consecutivo

          //Si el consecutivo empieza con T, se asume que es un consecutivo de un ajuste de traslado
          //Se eliminan los tres primeros caracteres que corresponden a la letra T y la secuencia del ajuste
          $cConsecutivo = $mDatos['cComCsc'];
          if (substr($mDatos['cComCsc'], 0, 1) == "T") {
            $cConsecutivo = substr($mDatos['cComCsc'], 3, strlen($mDatos['cComCsc']));
          }

          $nAnio = date('Y');
          $qFcoc  = "SELECT ";
          $qFcoc .= "SUBSTRING(comcscxx,2,2) as cscajuxx ";
          $qFcoc .= "FROM $cAlfa.fcoc$nAnio ";
          $qFcoc .= "WHERE ";
          $qFcoc .= "comidxxx = \"{$mDatos['cComId']}\" AND ";
          $qFcoc .= "comcodxx = \"{$mDatos['cComCod']}\" AND ";
          $qFcoc .= "SUBSTRING(comcscxx,4) = \"$cConsecutivo\" ";
          $qFcoc .= "ORDER BY ABS(cscajuxx) DESC LIMIT 0,1";
          $xFcoc  = f_MySql("SELECT","",$qFcoc,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qFcoc."~".mysql_num_rows($xFcoc));

          $cCscNue = 1;
          if (mysql_num_rows($xFcoc) > 0) {
            $vFcoc   = mysql_fetch_array($xFcoc);
            $cCscNue = $vFcoc['cscajuxx'] + 1;
          } else {
            $nAnioAnt = date('Y')-1;
            $qFcoc = str_replace("fcoc$nAnio","fcoc$nAnioAnt",$qFcoc);
            $xFcoc  = f_MySql("SELECT","",$qFcoc,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qFcoc."~".mysql_num_rows($xFcoc));
            if (mysql_num_rows($xFcoc) > 0) {
              $vFcoc   = mysql_fetch_array($xFcoc);
              $cCscNue = $vFcoc['cscajuxx'] + 1;
            }
          }
          $cCscNue = "T".str_pad($cCscNue,2,"0",STR_PAD_LEFT).$cConsecutivo;

          //Reemplazando el consecutivo nuevo en el comprobante en detalle
          for ($i=0;$i<$mDatosGen['nSecuencia'];$i++) {
            if ($mDatos['cComIdC' .($i+1)] == $mDatos['cComId']  && 
                $mDatos['cComCodC'.($i+1)] == $mDatos['cComCod'] &&
                $mDatos['cComCscC'.($i+1)] == $mDatos['cComCsc']) {
              $mDatos['cComCscC'.($i+1)] = $cCscNue;
            }
          }

          //Reemplazando el consecutivo nuevo en el comprobante en cabecera
          $mDatos['cComCsc'] = $cCscNue;

          if ($nSwitch == 0) {
            # Guardando Ajuste
            $mRetorna = $oAjuste->fnGuardarAjuste($mDatos);

            if($mRetorna[0] == "false") {
              $nSwitch = 1;
            } else {
              $nCanCre++;

              //Insertando registro en en log del sistema
              $qInsLog =  array(array('NAME'=>'sucidorx','VALUE'=>$vLog['sucidorx']     ,'CHECK'=>'SI'), //Id de la Sucursal Origen',
                                array('NAME'=>'docidorx','VALUE'=>$vLog['docidorx']     ,'CHECK'=>'SI'), //Id del DO Origen',
                                array('NAME'=>'docsufor','VALUE'=>$vLog['docsufor']     ,'CHECK'=>'SI'), //Sufijo del DO Origen',
                                array('NAME'=>'suciddex','VALUE'=>$vLog['suciddex']     ,'CHECK'=>'SI'), //Id de la Sucursal Destino',
                                array('NAME'=>'dociddex','VALUE'=>$vLog['dociddex']     ,'CHECK'=>'SI'), //Id del DO Destino',
                                array('NAME'=>'docsufde','VALUE'=>$vLog['docsufde']     ,'CHECK'=>'SI'), //Sufijo del DO Destino',
                                array('NAME'=>'comidorx','VALUE'=>$vLog['comidorx']     ,'CHECK'=>'SI'), //Id del Comprobante Origen',
                                array('NAME'=>'comcodor','VALUE'=>$vLog['comcodor']     ,'CHECK'=>'SI'), //Codigo del Comprobante Origen',
                                array('NAME'=>'comcscor','VALUE'=>$vLog['comcscor']     ,'CHECK'=>'SI'), //Consecutivo Uno del Comprobante Origen',
                                array('NAME'=>'comcsc2o','VALUE'=>$vLog['comcsc2o']     ,'CHECK'=>'SI'), //Consecutivo Dos del Comprobante Origen',
                                array('NAME'=>'comcsc3o','VALUE'=>$vLog['comcsc3o']     ,'CHECK'=>'NO'), //Consecutivo Tres Origen',
                                array('NAME'=>'comfecor','VALUE'=>$vLog['comfecor']     ,'CHECK'=>'SI'), //Fecha del Comprobante Origen',
                                array('NAME'=>'comhoror','VALUE'=>$vLog['comhoror']     ,'CHECK'=>'SI'), //Hora del Comprobante Origen',
                                array('NAME'=>'cliidorx','VALUE'=>$vLog['cliidorx']     ,'CHECK'=>'SI'), //Nit Cliente Origen',
                                array('NAME'=>'comiddex','VALUE'=>$mRetorna[1]          ,'CHECK'=>'SI'), //Id del Comprobante Destino',
                                array('NAME'=>'comcodde','VALUE'=>$mRetorna[2]          ,'CHECK'=>'SI'), //Codigo del Comprobante Destino',
                                array('NAME'=>'comcscde','VALUE'=>$mRetorna[3]          ,'CHECK'=>'SI'), //Consecutivo Uno del Comprobante Destino',
                                array('NAME'=>'comcsc2d','VALUE'=>$mRetorna[4]          ,'CHECK'=>'SI'), //Consecutivo Dos del Comprobante Destino',
                                array('NAME'=>'comcsc3d','VALUE'=>""                    ,'CHECK'=>'NO'), //Consecutivo Tres Destino',
                                array('NAME'=>'comfecde','VALUE'=>$mRetorna[5]          ,'CHECK'=>'SI'), //Fecha del Comprobante Destino',
                                array('NAME'=>'comhorde','VALUE'=>date('H:i:s')         ,'CHECK'=>'SI'), //Hora del Comprobante Destino',
                                array('NAME'=>'cliiddex','VALUE'=>$vDocDes['cliidxxx']  ,'CHECK'=>'SI'), //Nit Cliente Destino',
                                array('NAME'=>'comvlrxx','VALUE'=>abs($vLog['comvlrxx']),'CHECK'=>'SI'), //Valor del Comprobante',
                                array('NAME'=>'comobsxx','VALUE'=>$_POST['cComObs']     ,'CHECK'=>'SI'), //Observacion Traslado',            
                                array('NAME'=>'regusrxx','VALUE'=>$_COOKIE['kUsrId']    ,'CHECK'=>'SI'), //Usuario que Creo el Registro',
                                array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')         ,'CHECK'=>'SI'), //Fecha de Creacion del Registro',
                                array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')         ,'CHECK'=>'SI'), //Hora de Creacion del Registro',
                                array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')         ,'CHECK'=>'SI'), //Fecha de Modificacion del Registro',
                                array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')         ,'CHECK'=>'SI'), //Hora de Modificacion del Registro',
                                array('NAME'=>'regestxx','VALUE'=>"ACTIVO"              ,'CHECK'=>'SI')); //Estado del Registro',
              if (!f_MySql("INSERT","fpar0158",$qInsLog,$xConexion01,$cAlfa)) {
                // //control error si no se puede actualizar
                $cMsjAdi .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsjAdi .= "Error al Insertar en el Log de Traslado DO a DO.\n";
              }
            }
    
            for ($i=6; $i<count($mRetorna); $i++) {
              $mAuxText = explode("~",$mRetorna[$i]);
              $cMsj  .= ($mAuxText[0] != "") ? "Linea ".str_pad($mAuxText[0],4,"0",STR_PAD_LEFT).": " : "";
              $cMsj  .= $mAuxText[1]."\n";
            }
          }
        }
			break;
	  }
  }

  if ($nCanCre > 0) {
    //Cerrando DO automaticamente si la opcion fue marcada
    if ($_POST['chkCerrarDo'] == "SI") {
      //Validando si el DO tiene movimiento contable, No se permita Cerrar
      //Metodo que trae el movimiento del DO
      $objMovDo = new cMovimientoDo();
      $vDatos['sucidxxx'] =  $_POST['cSucIdOri']; //sucusal
      $vDatos['docidxxx'] =  $_POST['cDocNroOri']; //Do
      $vDatos['docsufxx'] =  $_POST['cDocSufOri']; //sufijo
      $vDatos['imppygdo'] =  ""; //imprimir PyG del DO

      $mRetMovDo = $objMovDo->fnDatosMovimientoDo($vDatos);
      $mDatos = array();
      $mDatos = $mRetMovDo[1];

      $nSinFactura = 0;
      $cDoc = "";
      //Validando que todos los anticipos esten facturados
      for ($nA=0; $nA<count($mDatos['anticipo']); $nA++) {
        if ($mDatos['anticipo'][$nA]['comfacxx'] == "") {
          $cDoc .= "ANTICIPO: ";
          $cDoc .= $mDatos['anticipo'][$nA]['comidxxx']."-";
          $cDoc .= $mDatos['anticipo'][$nA]['comcodxx']."-";
          $cDoc .= $mDatos['anticipo'][$nA]['comcscxx']."-";
          $cDoc .= $mDatos['anticipo'][$nA]['comcsc2x']."\n";
          $nSinFactura++;
        }
      }
      //Validando que todos los pcc esten facturados
      for ($nA=0; $nA<count($mDatos['pccxxxxx']); $nA++) {
        if ($mDatos['pccxxxxx'][$nA]['comfacxx'] == "") {
          $cDoc .= "PCC: ";
          $cDoc .= $mDatos['pccxxxxx'][$nA]['comidxxx']."-";
          $cDoc .= $mDatos['pccxxxxx'][$nA]['comcodxx']."-";
          $cDoc .= $mDatos['pccxxxxx'][$nA]['comcscxx']."-";
          $cDoc .= $mDatos['pccxxxxx'][$nA]['comcsc2x']."\n";
          $nSinFactura++;
        }
      }
      //Anticipos sin legalizar
      for ($nA=0; $nA<count($mDatos['doinfslx']); $nA++) {
        if ($mDatos['doinfslx'][$nA]['comfacxx'] == "") {
          $cDoc .= "ANTICIPO SIN LEGALIZAR: ";
          $cDoc .= $mDatos['doinfslx'][$nA]['comidxxx']."-";
          $cDoc .= $mDatos['doinfslx'][$nA]['comcodxx']."-";
          $cDoc .= $mDatos['doinfslx'][$nA]['comcscxx']."-";
          $cDoc .= $mDatos['doinfslx'][$nA]['comcsc2x']."\n";
          $nSinFactura++;
        }
      }
      //Formularios
      if ($mDatos['movfordo'] != "") {
        $cDoc .= "FORMULARIOS CON DO: ".$mDatos['movfordo'];
        $nSinFactura++;
      }

      if($nSinFactura > 0){
        //Advertencia
        $cMsjAdi .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsjAdi .= "No se puede Cerrar el Do [{$_POST['cSucIdOri']}-{$_POST['cDocNroOri']}-{$_POST['cDocSufOri']}], tiene el siguiente Movimiento Contable Asociado:\n\n".$cDoc."\n";
      } else {
        $nSwActDo = 0;
        //Insertando observacion de cierre automatico, y cambiando de estado el DO
        #Observacion
        $dFecha = date('Y-m-d');
        $tHora  = date('H:i:s');

        $cObs  = "|___";
        $cObs .= trim(strtoupper("Cerrado"))."__";
        $cObs .= trim(strtoupper($_COOKIE['kUsrId']))."__";
        $cObs .= $dFecha."__";
        $cObs .= $tHora."__";
        $cObs .= trim("CIERRE AUTOMATICO DE DO DESDE EL PROCESO TRASLADO DO A DO. ".strtoupper($_POST['cComObs']));
        $cObs .= "___|";
        $cObs .= $vDocOri['docobsac'];

        $qUpdate =  array(array('NAME'=>'regfmodx','VALUE'=>$dFecha             ,'CHECK'=>'SI'),
                          array('NAME'=>'reghmodx','VALUE'=>$tHora              ,'CHECK'=>'SI'),
                          array('NAME'=>'docapexx','VALUE'=>'SI'                ,'CHECK'=>'SI'),
                          array('NAME'=>'regestxx','VALUE'=>'FACTURADO'         ,'CHECK'=>'SI'),
                          array('NAME'=>'docobsac','VALUE'=>$cObs               ,'CHECK'=>'SI'),
                          array('NAME'=>'sucidxxx','VALUE'=>$_POST['cSucIdOri'] ,'CHECK'=>'WH'),
                          array('NAME'=>'docidxxx','VALUE'=>$_POST['cDocNroOri'],'CHECK'=>'WH'),
                          array('NAME'=>'docsufxx','VALUE'=>$_POST['cDocSufOri'],'CHECK'=>'WH'));

        if (f_MySql("UPDATE","sys00121",$qUpdate,$xConexion01,$cAlfa)) {
          /***** Busco si el DO como SubCentro de Costo  *****/
          $qSqlScc  = "SELECT * ";
          $qSqlScc .= "FROM $cAlfa.fpar0120 ";
          $qSqlScc .= "WHERE ";
          $qSqlScc .= "sccidxxx = \"{$_POST['cDocNroOri']}\" LIMIT 0,1";
          $xSqlScc  = f_MySql("SELECT","",$qSqlScc,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qSqlScc."~".mysql_num_rows($xSqlScc));
          $DoScc = "";
          if (mysql_num_rows($xSqlScc) > 0) {
            $vSqlScc  = mysql_fetch_array($xSqlScc);
            $DoScc = $vSqlScc['sccidxxx'];
            $mUpScc = array(array('NAME'=>'sccestdo','VALUE'=>$cNewEst   ,'CHECK'=>'SI'),
                            array('NAME'=>'sccidxxx','VALUE'=>$DoScc     ,'CHECK'=>'WH'));

            if (!f_MySql("UPDATE","fpar0120",$mUpScc,$xConexion01,$cAlfa)) {
              $cMsjAdi .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsjAdi .= "No se Pudo Actualizar el DO [".$Do['cSucId_DOS']."-".$_POST['cDocNroOri']."-".$_POST['cDocSufOri']."] en la Tabla de Subcentros de Costo.\n";
            }
          }
          // Fin Nuevo metodo de actualizacion en la tabla de Subcentros de Costo

          //Actualizando fecha de facturacion en modulo de aduana
          switch ($vDocOri['doctipxx']) { //dependiendo del tipo de operacion actualizo la tabla en Impo o Expo
            case "IMPORTACION":
            case "TRANSITO":
              $mUpd200 =  array(array('NAME'=>'DOIFENTR','VALUE'=>$dFecha             ,'CHECK'=>'SI'),
                                array('NAME'=>'DOIHENTR','VALUE'=>$tHora              ,'CHECK'=>'SI'),
                                array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDocNroOri'],'CHECK'=>'WH'),
                                array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDocSufOri'],'CHECK'=>'WH'),
                                array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cSucIdOri'] ,'CHECK'=>'WH'));

              if (!f_MySql("UPDATE","SIAI0200",$mUpd200,$xConexion01,$cAlfa)) {
                $cMsjAdi .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsjAdi .= "El Sistema no Pudo Actualizar la Fecha de la Factura en el Modulo de Control Fechas.Para el DO [".$Do['cSucId_DOS']."-".$_POST['cDocNroOri']."-".$_POST['cDocSufOri']."].\n";
              }
            break;
            case "EXPORTACION":
              $mUpd199 =  array(array('NAME'=>'dexfefac','VALUE'=>$dFecha             ,'CHECK'=>'SI'),
                                array('NAME'=>'dexidxxx','VALUE'=>$_POST['cDocNroOri'],'CHECK'=>'WH'),
                                array('NAME'=>'admidxxx','VALUE'=>$_POST['cSucIdOri'] ,'CHECK'=>'WH'));

              if (!f_MySql("UPDATE","siae0199",$mUpd199,$xConexion01,$cAlfa)) {
                $cMsjAdi .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsjAdi .= "El Sistema no Pudo Actualizar la Fecha de la Factura en el Modulo de Control Fechas. Para el DO [".$Do['cSucId_DOS']."-".$_POST['cDocNroOri']."-".$_POST['cDocSufOri']."].\n";
              }
            break;
            default:
              //No hace nada para los tipo de operacion OTROS y REGISTRO
            break;
          }## switch ($_POST['cDosTip_DOS'.($i+1)])
        } else {
          $nSwActDo = 1;
          $cMsjAdi .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsjAdi .= "Error al Actualizar el Registro la Tabla sys00121 para el Do [{$_POST['cSucIdOri']}-{$_POST['cDocNroOri']}-{$_POST['cDocSufOri']}].\n";
        }

        if ($nSwActDo == 0){
          $cMsjAdi .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsjAdi .= "El DO [{$_POST['cSucIdOri']}-{$_POST['cDocNroOri']}-{$_POST['cDocSufOri']}] Se Cerro Automaticamente con Exito.\n";
        }
      }
    }
  }

  if ($cMsjAdi != "") {
    $cMsj .= $cMsjAdi;
  }

  if ($nSwitch == 1) {
		f_Mensaje(__FILE__,__LINE__,"\n".$cMsj."Verifique."); ?>
    <script languaje = "javascript">
			parent.fmwork.document.forms['frnav']['nTimesSave'].value = 0;
		</script>
	<?php }

  if ($nSwitch == 0) {
    f_Mensaje(__FILE__, __LINE__, "\n".$cMsj);
  }
    
  if ($nCanCre > 0) { ?>
		<form name = "frnav" action = "frtmdini.php" method = "post" target = "fmwork"></form>
		<script languaje = "javascript">
      document.location="<?php echo $_COOKIE['kIniAnt'] ?>";
    	parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
			document.forms['frnav'].submit();
		</script>
		<?php
	}
?>

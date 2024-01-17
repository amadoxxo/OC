<?php
  //set_time_limit(0);
	include("../../../../libs/php/utility.php");
	$vBuscar = array(chr(13),chr(10),chr(27),chr(9));
	$vReempl = array(" "," "," "," ");
	## Actualizaciones
	## 2010-07-13 / 10:09:00 : Se incluyo los saldos a favor del cliente, que que cambiara el NIT que envia a SIIGO en las compañias vinculadas.
	##											 : En la cuenta contable se esta asignando el valor de la variable del sistema para el caso de las compañias vinculadas.
	
	/**
   * Cantidad de Registros para reiniciar conexion
   */
  define("_NUMREG_",100);
?>
<html>
	<head>
		<title>Archivo Plano para Sistema SIIGO</title>
  	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/general.css">
   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/layout.css">
   	<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/custom.css">
  </head>
  <body>
		<?php
		  /**
       * Buscando la informacion de la tabla fpar0115  DETALLES DE LA CTA PUC...
       */
      $qFpar115  = "SELECT *, ";
      $qFpar115 .= "CONCAT(pucgruxx, pucctaxx, pucsctax, pucauxxx, pucsauxx) AS pucid36x ";
      $qFpar115 .= "FROM $cAlfa.fpar0115 ";
      $xFpar115 = f_MySql("SELECT","",$qFpar115,$xConexion01,"");
      // echo $qFpar115."~".mysql_num_rows($xFpar115)."<br><br>";
      $mCuenta = array();
      while($xR115 = mysql_fetch_array($xFpar115)) {
        $mCuenta["{$xR115['pucid36x']}"] = $xR115;
      }
      mysql_free_result($xFpar115);
      
    	$dos = "SIIGO_".$_COOKIE['kUsrId']."_".date("YmdHis").".TXT";
    	$fedi = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$dos;
			if (file_exists($fedi)){
				unlink($fedi);
			}

			$fp = fopen($fedi,'a+');

			$cAno = substr($dDesde,0,4);
		  $qCodDat  = "SELECT * ";
      $qCodDat .= "FROM $cAlfa.fcod$cAno ";
      $qCodDat .= "WHERE ";

      if($gComId <> ""){
      	$qCodDat .= "comidxxx = \"$gComId\" AND ";
      }

      if($gComCod <> ""){
      	$qCodDat .= "comcodxx = \"$gComCod\" AND ";
      }

      if($gUsrId <> ""){
      	$qCodDat .= "regusrxx = \"$gUsrId\" AND ";
      }

      /*$qCodDat .= "comidxxx = \"G\" AND ";
      $qCodDat .= "comcodxx = \"001\" AND ";
      $qCodDat .= "comcscxx = \"874105\" AND ";*/

      $qCodDat .= "regestxx IN (\"ACTIVO\",\"INACTIVO\") AND ";
      $qCodDat .= "comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" ";
      $qCodDat .= "ORDER BY comidxxx,comcodxx,comcscxx,ABS(comcsc2x),ABS(comseqxx) ";
		  $xCodDat =  f_Mysql("SELECT","",$qCodDat,$xConexion01,"");
		  //f_Mensaje(__FILE__,__LINE__,$gUsrId);
		  //f_Mensaje(__FILE__,__LINE__,$qCodDat." ~ ".mysql_num_rows($xCodDat));

		  $j = 0; $nSwitchFile = 0; $cMsj = ""; $xRCD_Ant = array();
      $nCanReg = 0;
		  while ($xRCD = mysql_fetch_array($xCodDat)){
        $nCanReg++;
        if (($nCanReg % _NUMREG_) == 0) {
          $xConexion01 = fnReiniciarConexion(); 
        }
		  	//Eliminando caracteres de tabulacion, interlineado de los campos
				foreach ($xRCD as $ckey => $cValue) {
        	$xRCD[$ckey] = str_replace($vBuscar,$vReempl,$xRCD[$ckey]);
        }
        
        $cCad01 = "";                   // TIPO DE COMPROBANTE
        $cCad02 = "";                   // CODIGO COMPROBANTE
        $cCad03 = "";                   // NUMERO DE DOCUMENTO
        $cCad04 = "";                   // SECUENCIA  (HASTA 250)
        $cCad05 = "";                   // NIT
        $cCad06 = "000";                // SUCURSAL
        $cCad07 = "";                   // CUENTA CONTABLE
        $cCad08 = "0000000000000";      // CODIGO DE PRODUCTO
        $cCad09 = "";                   // FECHA DEL DOCUMENTO (AAAAMMDD)
        $cCad10 = "";                   // CENTRO DE COSTO
        $cCad11 = "";                   // SUBCENTRO DE COSTO
        $cCad12 = "";                   // DESCRIPCION DEL MOVIMIENTO
        $cCad13 = "";                   // DEBITO O CREDITO
        $cCad14 = "";                   // VALOR DEL MOVIMIENTO
        $cCad15 = "";                   // BASE DE RETENCION
        $cCad16 = "0001";               // CODIGO DEL VENDEDOR
        $cCad17 = "0000";               // CODIGO DE LA CIUDAD
        $cCad18 = "000";                // CODIGO DE LA ZONA
        $cCad19 = "0000";               // CODIGO DE LA BODEGA
        $cCad20 = "000";                // CODIGO DE LA UBICACION
        $cCad21 = "000000000000000";    // CANTIDAD
        $cCad22 = "";                   // TIPO DE DOCUMENTO CRUCE
        $cCad23 = "";                   // CODIGO COMPROBANTE CRUCE
        $cCad24 = "";                   // NUMERO DE DOCUMENTO CRUCE
        $cCad25 = "";                   // SECUENCIA DEL DOCUMENTO CRUCE
        $cCad26 = "";                   // FECHA VENCIMIENTO DOC CRUCE
        $cCad27 = "0000";               // CODIGO FORMA DE PAGO
        $cCad28 = "00";                 // CODIGO DEL BANCO
        $cCad29 = " ";                  // TIPO DOCUMENTO DE PEDIDO
        $cCad30 = "000";                // CODIGO COMPROBANTE DE PEDIDO
        $cCad31 = "00000000000";        // NUMERO DE COMPROBANTE DE PEDIDO
        $cCad32 = "000";                // SECUENCIA DE PEDIDO
        $cCad33 = "00";                 // CODIGO DE LA MONEDA
        $cCad34 = "000000000000000";    // TASA DE CAMBIO
        $cCad35 = "000000000000000";    // VALOR DEL MOVIMIENTO EN EXTRANJERA
        $cCad36 = "000";                // CONCEPTO DE NOMINA
        $cCad37 = "00000000000";        // CANTIDAD DE PAGO
        $cCad38 = "0000";               // PORCENTAJE DEL DESCUENTO DE MOVIMIENTO
        $cCad39 = "0000000000000";      // VALOR DE DESCUENTO DEL MOVIMIENTO
        $cCad40 = "0000";               // PORCENTAJE DE CARGO DEL MOVIMIENTO
        $cCad41 = "0000000000000";      // VALOR DE CARGO DEL MOVIMIENTO
        $cCad42 = "0000";               // PORCENTAJE DEL IVA DE MOVIMIENTO
        $cCad43 = "0000000000000";      // VALOR DE IVA DEL MOVIMIENTO
        $cCad44 = "N";                  // INDICADOR DE NOMINA
        $cCad45 = "0";                  // NUMERO DE PAGO
        $cCad46 = "00000000000";        // NUMERO DE CHEQUE
        $cCad47 = "N";                  // INDICADOR TIPO MOVIMIENTO
        $cCad48 = "OPEN";               // NOMBRE DEL COMPUTADOR
        $cCad49 = "";                   // ESTADO DEL COMPROBANTE
        $cCad50 = "  ";                 // ECUADOR
        $cCad51 = "00";                 // ECUADOR
        $cCad52 = "    ";               // PERU NUMERO DE COMPROBANTE DEL PROVEEDOR
        $cCad53 = "00000000000";        // NUMERO DEL DOCUMENTO DEL PROVEEDOR
        $cCad54 = "          ";         // PREFIJO DEL DOCUMENTO DEL PROVEEDOR
        $cCad55 = "00000000";           // FECHA DE DOCUMENTO DE PROVEEDOR
        $cCad56 = "000000000000000000"; // PRECIO UNITARIO EN MONEDA LOCAL
        $cCad57 = "000000000000000000"; // PRECIO UNITARIO EN MONEDA EXTRANJERA
        $cCad58 = " ";                  // INDICAR TIPO DE MOVIMIENTO
        $cCad59 = "000";                // VECES A DEPRECIAR EL ACTIVO
        $cCad60 = "00";                 // ECUADOR SECUENCIA DE TRANSACCION
        $cCad61 = "0000000000";         // ECUADOR AUTORIZACION IMPRENTA
        $cCad62 = "A";                  // ECUADOR SECUENCIA MARCADA COMO IVA PARA EL COA
        $cCad63 = "000";                // NUMERO DE CAJA
        $cCad64 = "000000000000000";    // -- SIGNO -- NUMERO DE PUNTOS OBTENIDOS
        $cCad65 = "000000000000000";    // CANTIDAD DOS
        $cCad66 = "000000000000000";    // CANTIDAD ALTERNA DOS
        $cCad67 = "L";                  // METODO DE DEPRECIACION
        $cCad68 = "000000000000000000"; // CANTIDAD DE FACTOR DE CONVERSION
        $cCad69 = "1";                  // OPERADOR DE FACTOR DE CONVERSION
        $cCad70 = "0000000000";         // FACTOR DE CONVERSION
        $cCad71 = "00000000";           // FECHA DE CADUCIDAD
        $cCad72 = "00";                 // CODIGO ICE
        $cCad73 = "     ";              // CODIGO RETENCION
        $cCad74 = " ";                  // CLASE RETENCION
        $cCad75 = "0000";               // Codigo del motivo de devolucion
        $cCad76 = "                                           "; // DATOS M/CIA CONSIGNACION
        $cCad77 = "                   "; // NUMERO COMPROBANTE FISCAL PROPIO (REP.DOM)
        $cCad78 = "                   "; // NUMERO COMPROBANTE FISCAL PROVEEDOR (REP.DOM)
        $cCad79 = " ";                   // INDICADOR TIPO DE LETRA:  1
        $cCad80 = " ";                   // ESTADO DE LA LETRA:  1
        
        				
		  	// Busco los datos de cabecera.
		  	$qCabFac  = "SELECT * ";
  			$qCabFac .= "FROM $cAlfa.fcoc$cAno ";
  			$qCabFac .= "WHERE ";
  			$qCabFac .= "comidxxx = \"{$xRCD['comidxxx']}\" AND ";
  			$qCabFac .= "comcodxx = \"{$xRCD['comcodxx']}\" AND ";
  			$qCabFac .= "comcscxx = \"{$xRCD['comcscxx']}\" AND ";
  			$qCabFac .= "comcsc2x = \"{$xRCD['comcsc2x']}\" AND ";
  			$qCabFac .= "regestxx = \"{$xRCD['regestxx']}\" LIMIT 0,1";
  			$xCabFac  = f_MySql("SELECT","",$qCabFac,$xConexion01,"");
  			//f_Mensaje(__FILE__,__LINE__,$qCabFac." ~ ".mysql_num_rows($xCabFac));
				$vCabFac  = mysql_fetch_array($xCabFac);
		  	// Fin Busco los datos de cabecera.

		  	// Busco las caracteristicas de la cuenta.
        $vPucId  = $mCuenta["{$xRCD['pucidxxx']}"];
        // Fin Busco las caracteristicas de la cuenta.

		    $j++;
		    $supercadena = '';

		    $cCad01 = str_pad($xRCD['comidxxx'],1,chr(32),STR_PAD_RIGHT);								 /* Tipo de Comprobante */
		    switch ($xRCD['comidxxx']) { // Trampa para Alcomex para enviar todos los comprobantes de facturacion como F-001.
		    	case "F": $cCad02 = "001"; break;																					 /* Codigo de Comprobante */
		    	default:  $cCad02 = str_pad($xRCD['comcodxx'],3,"0",STR_PAD_LEFT); break;	 /* Codigo de Comprobante */
		    }
		    $cCad03 = str_pad($xRCD['comcsc2x'],11,"0",STR_PAD_LEFT); 									 /* Numero del Documento */
		    $cCad04 = str_pad($xRCD['comseqxx'],5,"0",STR_PAD_LEFT); 										 /* Secuencia */
		    switch($xRCD['comidxxx']){
		   		case "P":
		   			## Trampa para enviar el Nit del Proveedor en todos los registros de Comprobante Tipo P de Reembolso Caja Menor. 2010-10-27 ##
        	  ## Se identifico en la interfaz de Transmision a SIIGO, que en los comprobantes P de Reembolso de Caja Menor se envia el nit Proveedor - Cliente ##
        	  ## y siempre se debe enviar el nit del proveedor ##
        	  //Busco Marca que determina cuales son los comprobantes de tipo P parametrizados como Reembolso de Caja Menor
		    		$qDatCom  = "SELECT $cAlfa.fpar0117.comtipxx ";
	        	$qDatCom .= "FROM $cAlfa.fpar0117 ";
	         	$qDatCom .= "WHERE ";
	          $qDatCom .= "$cAlfa.fpar0117.comidxxx = \"P\" AND ";
	          $qDatCom .= "$cAlfa.fpar0117.comcodxx = \"{$xRCD['comcodxx']}\" AND ";
	          $qDatCom .= "$cAlfa.fpar0117.comtipxx = \"RCM\" AND ";
	          $qDatCom .= "$cAlfa.fpar0117.regestxx = \"ACTIVO\" LIMIT 0,1 ";
	          $xDatCom  = f_MySql("SELECT","",$qDatCom,$xConexion01,"");
	        	$vDatCom  = mysql_fetch_array($xDatCom);
	        	//f_Mensaje(__FILE__,__LINE__,$qDatCom." ~ ".mysql_num_rows($xDatCom));
	        	if (mysql_num_rows($xDatCom) == 1) {
	        		if((substr($xRCD['pucidxxx'],0,1) == "2") && $xRCD['tertipxx'] == "CLICLIXX"){
	        			$cCad05 = str_pad($xRCD['terid2xx'],13,"0",STR_PAD_LEFT);
	        		} else {
	        			$cCad05 = str_pad($xRCD['teridxxx'],13,"0",STR_PAD_LEFT);
	        		}
	        	} else {
	        		$cCad05 = str_pad($xRCD['teridxxx'],13,"0",STR_PAD_LEFT);
	        	}
		      break;
		   	  case "F":
		   	  		if(($xRCD['comctocx'] == "SS" || $xRCD['comctocx'] == "SC") && f_InList($vCabFac['terid2xx'],$vSysStr['alcomex_nits_companias_vinculadas'])) {
		   	  			$cCad05 = str_pad($vCabFac['terid2xx'],13,"0",STR_PAD_LEFT); // Trampa para Alcomex - Compañias Vinculadas.
		    			}elseif(substr($xRCD['pucidxxx'],0,4) == "2408"){
		    				$cCad05 = str_pad($xRCD['terid2xx'],13,"0",STR_PAD_LEFT); 							 	 /* Nit */
		    			}elseif(substr($xRCD['pucidxxx'],0,4) == "2366" || substr($xRCD['pucidxxx'],0,6) == "135519"){
		    				//Para el caso de la retencion CREE y la autoretencion CREE este impuesto siempre se aplica al cliente
		    				$cCad05 = str_pad($xRCD['terid2xx'],13,"0",STR_PAD_LEFT); 							 	 /* Nit */
		    			}else{
		    				$cCad05 = str_pad($xRCD['teridxxx'],13,"0",STR_PAD_LEFT); 							 	 /* Nit */
		    			}
		   	  break;
		   	  default:
		   	   $cCad05 = str_pad($xRCD['teridxxx'],13,"0",STR_PAD_LEFT);
		   	  break;
		    }//switch($xRCD['comidxxx']){

		    /*if ($xRCD['comidxxx'] == "F" && ($xRCD['comctocx'] == "SS" || $xRCD['comctocx'] == "SC") && f_InList($vCabFac['terid2xx'],$vSysStr['alcomex_nits_companias_vinculadas'])) {
					$cCad05 = str_pad($vCabFac['terid2xx'],13,"0",STR_PAD_LEFT); // Trampa para Alcomex - Compañias Vinculadas.
		    } else {
		    	$cCad05 = str_pad($xRCD['teridxxx'],13,"0",STR_PAD_LEFT); 							 	 /* Nit */
		    //}//Prueba 20101030
		    $cCad07 = str_pad($xRCD['pucidxxx'],10,"0",STR_PAD_LEFT); 								 	 /* Cuenta */
		    /*
		    if ($xRCD['comidxxx'] == "F" && $xRCD['comctocx'] == "SS" && f_InList($vCabFac['terid2xx'],"800188557","830506117","800006786","830063139","900148614")) {
		    	$cCad07 = "1310100500"; // Trampa para Alcomex - Compañias Vinculadas.
		    } else {
		    	$cCad07 = str_pad($xRCD['pucidxxx'],10,"0",STR_PAD_LEFT); 								 // Cuenta
		    }
		    */
		    $cCad09 = str_pad(str_replace('-','',$xRCD['comfecxx']),8,"0",STR_PAD_LEFT); /* Fecha del Comprobante */
		    switch (strlen($xRCD['sccidxxx'])) {
		    	case "7": // Cuando la longitud del sub centro de costo sea de 7 digitos.
		    		$cCad10 = str_pad(substr($xRCD['sccidxxx'],0,4),4,"0",STR_PAD_LEFT); 		 // Centro de Costos
		    		$cCad11 = str_pad(substr($xRCD['sccidxxx'],-3),3,"0",STR_PAD_LEFT); 		 // Sub Centro de Costos
		    	break;
		    	case "6": // Cuando la longitud del sub centro de costo sea de 6 digitos (porque el DO no permite cero a la izquierda).
		    		$cCad10 = str_pad(substr($xRCD['sccidxxx'],0,3),4,"0",STR_PAD_LEFT); 		 // Centro de Costos
		    		$cCad11 = str_pad(substr($xRCD['sccidxxx'],-3),3,"0",STR_PAD_LEFT); 		 // Sub Centro de Costos
		    	break;
		    	case "0": // Cuando la longitud del sub centro de costo sea de 0 digitos (viene vacio, en este caso uso el subcentro del registro anterior).
		    		$xRCD['sccidxxx'] = str_pad($xRCD_Ant['sccidxxx'],7,"0",STR_PAD_LEFT);
		    		$cCad10 = str_pad(substr($xRCD['sccidxxx'],0,4),4,"0",STR_PAD_LEFT); 		 // Centro de Costos
		    		$cCad11 = str_pad(substr($xRCD['sccidxxx'],-3),3,"0",STR_PAD_LEFT); 		 // Sub Centro de Costos
		    	break;
		    	default: // Cuando la longitud del sub centro de costo sea diferente 7,6,0 digitos.
		    		$xRCD['sccidxxx'] = str_pad($xRCD['sccidxxx'],7,"0",STR_PAD_LEFT);
		    		$cCad10 = str_pad(substr($xRCD['sccidxxx'],0,4),4,"0",STR_PAD_LEFT); 		 // Centro de Costos
		    		$cCad11 = str_pad(substr($xRCD['sccidxxx'],-3),3,"0",STR_PAD_LEFT); 		 // Sub Centro de Costos
		    	break;
		    }

		    $cCad12 = str_pad(substr($xRCD['comobsxx'],0,50),50,chr(32),STR_PAD_RIGHT);  /* Observacion del Item */
		    $cCad13 = str_pad($xRCD['commovxx'],1,chr(32),STR_PAD_RIGHT); 							 /* Tipo de Movimiento Debito o Credito */
		    /* Valor del Movimiento */
		    $decex = explode('.',$xRCD['comvlrxx']);
			  $nint = $decex[0];
				$ndec = $decex[1];
				$cCad1 = str_pad($nint,13,"0",STR_PAD_LEFT);
				$cCad2 = str_pad($ndec,2,"0",STR_PAD_RIGHT);
				$cCad14 = $cCad1.$cCad2;																											 /* Valor del Movimiento */
				/* Fin Valor del Movimiento */
		    /* Base Retencion */
		    $decex = explode('.',$xRCD['comvlr01']); //??????
			  $nint = $decex[0];
				$ndec = $decex[1];
				$cCad1 = str_pad($nint,13,"0",STR_PAD_LEFT);
				$cCad2 = str_pad($ndec,2,"0",STR_PAD_RIGHT);
				$cCad15 = $cCad1.$cCad2;																											 /* Base de la Retencion */
				/* Fin Base Retencion */
				$cCad22 = str_pad($xRCD['comidcxx'],1,"0",STR_PAD_LEFT);										 /* Tipo Documento Cruce */
		  	switch ($xRCD['comidcxx']) { // Trampa para Alcomex para enviar todos los comprobantes de facturacion como F-001.
		    	case "F": $cCad23 = "001"; break;																					 /* Codigo Documento Cruce */
		    	default:  $cCad23 = str_pad($xRCD['comcodcx'],3,"0",STR_PAD_LEFT); break;	 /* Codigo Documento Cruce */
		    }
				$cCad24 = str_pad($xRCD['comcsccx'],11,"0",STR_PAD_LEFT); 									 /* Numero Documento Cruce */
				$cCad25 = str_pad($xRCD['comseqcx'],3,"0",STR_PAD_LEFT);   									 /* Secuencia Documento Cruce */
 				$cCad26 = str_pad(str_replace('-','',$xRCD['comfecve']),8,"0",STR_PAD_LEFT); /* Fecha Vencimiento Doc. Cruce */
				/* Estado del Comprobante */
				if ($xRCD['regestxx'] == "ACTIVO") {
					$cCad49 = " ";																														 /* Estado del Comprobante */
				} else {
					$cCad49 = "A";
				}																																					 	 /* Estado del Comprobante */
				/* Fin Estado del Comprobante */

			  $supercadena  = $cCad01.$cCad02.$cCad03.$cCad04.$cCad05.$cCad06.$cCad07.$cCad08.$cCad09.$cCad10;
			  $supercadena .= $cCad11.$cCad12.$cCad13.$cCad14.$cCad15.$cCad16.$cCad17.$cCad18.$cCad19.$cCad20;
			  $supercadena .= $cCad21.$cCad22.$cCad23.$cCad24.$cCad25.$cCad26.$cCad27.$cCad28.$cCad29.$cCad30;
			  $supercadena .= $cCad31.$cCad32.$cCad33.$cCad34.$cCad35.$cCad36.$cCad37.$cCad38.$cCad39.$cCad40;
			  $supercadena .= $cCad41.$cCad42.$cCad43.$cCad44.$cCad45.$cCad46.$cCad47.$cCad48.$cCad49.$cCad50;
			  $supercadena .= $cCad51.$cCad52.$cCad53.$cCad54.$cCad55.$cCad56.$cCad57.$cCad58.$cCad59.$cCad60;
			  $supercadena .= $cCad61.$cCad62.$cCad63.$cCad64.$cCad65.$cCad66.$cCad67.$cCad68.$cCad69.$cCad70;
			  $supercadena .= $cCad71.$cCad72.$cCad73.$cCad74.$cCad75.$cCad76.$cCad77.$cCad78.$cCad79.$cCad80;

			  //if (strlen($supercadena) == 220) {
			  if (strlen($supercadena) == 625) {
			    fwrite($fp,trim($supercadena));
			    if ($j < mysql_num_rows($xCodDat)){
			      fwrite($fp,chr(13).chr(10));
			    }
			  } else {
			   $nSwitchFile = strlen($supercadena);
			   $cMsj .= "$cCad01-$cCad02-$cCad03-$cCad04, Error la Longitud del Registro es Diferente a [$nSwitchFile], Verifique.<br>";
			  }

			  $xRCD_Ant = $xRCD; // Guardo un backup del registro que lei.
			}

		  fclose($fp);

      if (file_exists($fedi)){
        chmod($fedi,intval($vSysStr['system_permisos_archivos'],8));
      } else {
        f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $fedi, Favor Comunicar este Error a openTecnologia S.A.");
      }

  	 if ($j == 0) { $nSwitchFile = 1; $cMsj .= "No se Generaron Registros, Verifique.<br>"; }

		 if ($nSwitchFile == 0){
			echo "<center><a href ='$fedi'>SIIGO.TXT</a></center>";
		 } else {
       echo $cMsj;
  	 } ?>
	</body>
</html>
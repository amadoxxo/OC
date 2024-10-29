<?php
  namespace openComex;
  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/uticonta.php");

  /**
   * Matriz para recibir la causacion automatica.
   * 
   * @var array
   */
  $mReturn_CauAut = array();

	/**
	 * Armo la matriz para enviar a la clase que hace la causacion automatica.
	 * 
	 * Por cada DO y por cada CONCEPTO PAPA debo crear un registro en la matriz para enviar a la clase.
	 */
  
  /**
   * Tasa Pactada:
   * Se incluyo la posibilidad de calcular la causacion automatica con una tasa pactada, el proceso que se debe hacer es:
   * 
   * Precondiciones:
   * 1. El Cliente debe tener tasa pactada y concepto de diferencia al cambio.
   * 2. La opcion de aplica tasa pactada debe estar checkeada.
   * 3. Muy importante TODOS los valores de cabecera del comprobante deben digitarse en dolares: la base total, el iva y 
   *    en los conceptos papa la Base Iva, Valor Iva y Valor.
   * 
   * Calculo:
   * 1. Se debe enviar al metodo si aplica o no tasa pactada, el valor de la tasa de cambio del dia y el valor de la tasa pactada.
   * 2. En cada concepto papa el valor de la base iva y del iva se calcula con la tasa de cambio del dia, 
   *    y el valor del servicio se calcula con la tasa pactada.
   * 3. Las retenciones del concepto papa se calculan con la tasa de cambio del dia.
   * 4. La CxP se calcula con el valor del servicio calculado con la tasa del cambio del dia. 
   *    Es decir, se calcula con el valor del servicio calculado con la tasa del cambio del dia y las retenciones.
   * 5. Al calcular la CxP con la tasa del dia el comprobante queda descuadrado, esto porque el valor del servicio calculado 
   *    con la tasa pactada puede ser mayor o menor al valor del servicio con la tasa del dia, 
   *    la diferencia debe ir al concepto de diferencia al cambio.
   * 6. Para determinar el movimiento contable del concepto de diferencia al cambio, si los debitos son mayores que los creditos
   *    el movimiento debe ser credito, si los debitos son menores que los creditos el movimiento debe ser debido.  
   */
  
  /**
   * Matriz de Causaciones Automaticas.
   * $mCauAut[$nInd_mCauAut]['sucidxxx'] - Sucursal
   * $mCauAut[$nInd_mCauAut]['docidxxx'] - DO
   * $mCauAut[$nInd_mCauAut]['docsufxx'] - Sufijo
   * $mCauAut[$nInd_mCauAut]['tertipxx'] - Tipo de Tercero Dueño del DO
   * $mCauAut[$nInd_mCauAut]['teridxxx'] - Id Cliente Dueño del DO
   * $mCauAut[$nInd_mCauAut]['ternomxx'] - Nombre Cliente Dueño del DO
   * $mCauAut[$nInd_mCauAut]['tertipbx'] - Tipo de Tercero del Proveedor
   * $mCauAut[$nInd_mCauAut]['teridbxx'] - Id Proveedor
   * $mCauAut[$nInd_mCauAut]['ternombx'] - Nombre Proveedor
   * $mCauAut[$nInd_mCauAut]['sucicapr'] - Sucursal ICA del Proveedor
   * $mCauAut[$nInd_mCauAut]['combasex'] - Base del Comprobante
   * $mCauAut[$nInd_mCauAut]['comivaxx'] - IVA del Comprobante
   * $mCauAut[$nInd_mCauAut]['comobsxx'] - Observacion del Comprobante
   * $mCauAut[$nInd_mCauAut]['docproxx'] - Tipo de Prorrateo del DO (VALOR/PORCENTAJE)
   * $mCauAut[$nInd_mCauAut]['docprovl'] - Valor del Prorrateo del DO
   * $mCauAut[$nInd_mCauAut]['ctoidxxx'] - Concepto PAPA
   * $mCauAut[$nInd_mCauAut]['ctodocid'] - DO del Concepto
   * $mCauAut[$nInd_mCauAut]['ctodesxx'] - Descripcion Concepto PAPA
   * $mCauAut[$nInd_mCauAut]['aiuaplxx'] - Aplica Base AIU
   * $mCauAut[$nInd_mCauAut]['ctobaaiu'] - Valor Base AIU
   * $mCauAut[$nInd_mCauAut]['ctobasex'] - Valor Base del Concepto
   * $mCauAut[$nInd_mCauAut]['ctoivaxx'] - Valor IVA del Concepto
   * $mCauAut[$nInd_mCauAut]['ctototxx'] - Valor Total del Concepto
   * $mCauAut[$nInd_mCauAut]['comidxxx'] - Id del Comprobante
   * $mCauAut[$nInd_mCauAut]['comcodxx'] - Codigo del Comprobante
   * $mCauAut[$nInd_mCauAut]['ccoidxxx'] - Centro Costo Comprobante
   * $mCauAut[$nInd_mCauAut]['sccidxxx'] - Subcentro de Costo Comprobante
   * $mCauAut[$nInd_mCauAut]['comcscxx'] - Factura del Comprobante
   * $mCauAut[$nInd_mCauAut]['docbasex'] - Valor Base del DO
   * $mCauAut[$nInd_mCauAut]['docbaaiu'] - Valor Base A.I.U del DO
   * $mCauAut[$nInd_mCauAut]['docivaxx'] - Valor IVA del DO
   * $mCauAut[$nInd_mCauAut]['doctotxx'] - Valor Total del DO
   * -Campos Tasa Pactada
   * $mCauAut[$nInd_mCauAut]['tasadiax'] - Tasa de Cambio del dia
   * $mCauAut[$nInd_mCauAut]['clitpxxx'] - Tasa Pactada
   * $mCauAut[$nInd_mCauAut]['clitpapl'] - Aplica Tasa Pactada
   * $mCauAut[$nInd_mCauAut]['clitpaga'] - Aplica Tasa de Pago
   * $mCauAut[$nInd_mCauAut]['clitpagv'] - Valor Tasa de Pago
   * $mCauAut[$nInd_mCauAut]['combaaiu'] - Valor Base AIU
	 * $mCauAut[$nInd_mCauAut]['comivaiu'] - Valor IVA AIU
	 * $mCauAut[$nInd_mCauAut]['comintpa'] - Intermediacion de Pago
   * 
   * @var array
   * @access public
   */
  $mCauAut = array();
  
  $nTotalBase = array();
  $nTotalAiu  = array();
  $nTotalIva  = array();
  
  for ($i=0;$i<$_POST['nSecuencia_DO'];$i++) { // Recorro DO x DO de la grilla para cargar la matriz.
  	for ($j=0;$j<$_POST['nSecuencia_CCO'];$j++) { // Recorro CONCEPTO x CONCEPTO para cargar la matriz.
  		
  		$nSw_Find = 0;
  		switch ($_POST['cTipPro']) {
  			case "VALOR":
		  		if ($_POST['cSucId_DO'  .($i+1)]."-".$_POST['cDocId_DO'  .($i+1)]."-".$_POST['cDocSuf_DO' .($i+1)] != 
		  				$_POST['cSucId_CCO' .($j+1)]."-".$_POST['cDocId_CCO' .($j+1)]."-".$_POST['cDocSuf_CCO'.($j+1)]) {
			  		$nSw_Find = 1;
			  	}
  			break;
  			case "PORCENTAJE":
  				$nSw_Find = 0;
  			break;
  		}		 
  		
  		if ($nSw_Find == 0) {
  			
  			/**
  			 * Validando que si aplica base A.I.U
  			 * La base A.I.U. debe ser menor o igual a la base del iva
  			 */
  			if ($_POST['cAiuApl'] == "SI") {
  				if ($_POST['nVlrBaiu_CCO'.($j+1)] > $_POST['nVlrBase_CCO'.($j+1)]) {
  					$nSwicht = 1;
  					$cMsj .= "Para el Concepto [".$_POST['cCcoId_CCO'  .($j+1)]."] La Base A.I.U [".$_POST['nVlrBaiu_CCO'.($j+1)]."] es Mayor a la Base de Iva [".$_POST['nVlrBase_CCO'.($j+1)]."].\n";
  				}
  			}
  			
  			
	  		/**
		  	 * Indice de la matriz para ingresar nuevos registros.
		  	 * 
		  	 * @var numeric
		  	 */
		  	$nInd_mCauAut = count($mCauAut);
	  		
		  	/**
		  	 * Vector para explotar el numero del DO.
		  	 * 
		  	 * @var array
		  	 */
	  		$mCauAut[$nInd_mCauAut]['sucidxxx'] = $_POST['cSucId_DO'   .($i+1)]; // Sucursal
			  $mCauAut[$nInd_mCauAut]['docidxxx'] = $_POST['cDocId_DO'   .($i+1)]; // DO
			  $mCauAut[$nInd_mCauAut]['docsufxx'] = $_POST['cDocSuf_DO'  .($i+1)]; // Sufijo
			  $mCauAut[$nInd_mCauAut]['tertipxx'] = $_POST['cTerTip_DO'  .($i+1)]; // Tipo de Tercero Dueño del DO
			  $mCauAut[$nInd_mCauAut]['teridxxx'] = $_POST['cTerId_DO'   .($i+1)]; // Id Cliente Dueño del DO
			  $mCauAut[$nInd_mCauAut]['ternomxx'] = $_POST['cTerNom_DO'  .($i+1)]; // Nombre Cliente Dueño del DO
			  $mCauAut[$nInd_mCauAut]['tertipbx'] = $_POST['cTerTipB']; 					 // Tipo de Tercero del Proveedor
			  $mCauAut[$nInd_mCauAut]['teridbxx'] = $_POST['cTerIdB']; 						 // Id Proveedor
			  $mCauAut[$nInd_mCauAut]['ternombx'] = $_POST['cTerNomB']; 					 // Nombre Proveedor
			  $mCauAut[$nInd_mCauAut]['sucicapr'] = $_POST['cProSucIca']; 				 // Sucursal ICA del Proveedor
			  $mCauAut[$nInd_mCauAut]['combasex'] = $_POST['nComVlr01']; 					 // Base del Comprobante
			  $mCauAut[$nInd_mCauAut]['comivaxx'] = $_POST['nComVlr02']; 					 // IVA del Comprobante
			  $mCauAut[$nInd_mCauAut]['comobsxx'] = $_POST['cComObs']; 					 	 // Observacion del Comprobante
			  $mCauAut[$nInd_mCauAut]['docproxx'] = $_POST['cTipPro']; 						 // Tipo de Prorrateo del DO (VALOR/PORCENTAJE)
			  $mCauAut[$nInd_mCauAut]['docprovl'] = $_POST['nVlrPro_DO'  .($i+1)]; // Valor del Prorrateo del DO 
			  $mCauAut[$nInd_mCauAut]['ctoidxxx'] = $_POST['cCcoId_CCO'  .($j+1)]; // Concepto PAPA
			  $mCauAut[$nInd_mCauAut]['ctodocid'] = $_POST['cSucId_CCO'  .($j+1)]."-".$_POST['cDocId_CCO'.($j+1)]."-".$_POST['cDocSuf_CCO'.($j+1)]; 	 // DO del Concepto
			  $mCauAut[$nInd_mCauAut]['ctodesxx'] = $_POST['cCcoDes_CCO' .($j+1)]; // Descripcion Concepto PAPA
			  $mCauAut[$nInd_mCauAut]['aiuaplxx'] = $_POST['cAiuApl']; 					 	 // Aplica Base AIU
			  $mCauAut[$nInd_mCauAut]['ctobaaiu'] = $_POST['nVlrBaiu_CCO'.($j+1)]; // Valor Base AIU
			  $mCauAut[$nInd_mCauAut]['ctobasex'] = $_POST['nVlrBase_CCO'.($j+1)]; // Valor Base del Concepto
			  $mCauAut[$nInd_mCauAut]['ctoivaxx'] = $_POST['nVlrIva_CCO' .($j+1)]; // Valor IVA del Concepto
			  $mCauAut[$nInd_mCauAut]['ctototxx'] = $_POST['nVlr_CCO'    .($j+1)]; // Valor Total del Concepto
			  $mCauAut[$nInd_mCauAut]['comidxxx'] = $_POST['cComId']; 						 // Id del Comprobante
			  $mCauAut[$nInd_mCauAut]['comcodxx'] = $_POST['cComCod']; 						 // Codigo del Comprobante
			  $mCauAut[$nInd_mCauAut]['ccoidxxx'] = $_POST['cCcoId']; 						 // Centro Costo Comprobante
			  $mCauAut[$nInd_mCauAut]['sccidxxx'] = $_POST['cSccId']; 						 // Subcentro de Costo Comprobante
			  $mCauAut[$nInd_mCauAut]['comcscxx'] = $_POST['cComCsc']; 						 // Factura del Comprobante
        
        $nDec = 0;
        if ($_POST['cCliTpApl'] == "SI" || $_POST['cCliTpagApl'] == "SI") {
          $nDec = 3;
        } elseif ($vSysStr['financiero_permitir_decimales_causaciones_automaticas'] == "SI") {
          $nDec = 2;
        }

        switch ($mCauAut[$nInd_mCauAut]['docproxx']) {
          case "PORCENTAJE":
            if (($i+1) == $_POST['nSecuencia_DO']) {
              $mCauAut[$nInd_mCauAut]['docbasex'] = number_format(($_POST['nVlrBase_CCO'.($j+1)] - $nTotalBase[($j+1)]),$nDec,'.',''); // Valor Base del DO
              $mCauAut[$nInd_mCauAut]['docbaaiu'] = number_format(($_POST['nVlrBaiu_CCO'.($j+1)] - $nTotalAiu[($j+1)]),$nDec,'.',''); // Valor Base A.I.U del DO
              $mCauAut[$nInd_mCauAut]['docivaxx'] = number_format(($_POST['nVlrIva_CCO' .($j+1)] - $nTotalIva[($j+1)]),$nDec,'.',''); // Valor IVA del DO
              $mCauAut[$nInd_mCauAut]['doctotxx'] = number_format(($mCauAut[$nInd_mCauAut]['docbasex'] + $mCauAut[$nInd_mCauAut]['docivaxx']),$nDec,'.',''); 		 	// Valor Total del DO
            }	else {
              $mCauAut[$nInd_mCauAut]['docbasex'] = number_format(($mCauAut[$nInd_mCauAut]['ctobasex'] * ($mCauAut[$nInd_mCauAut]['docprovl']/100)),$nDec,'.',''); // Valor Base del DO
              $mCauAut[$nInd_mCauAut]['docbaaiu'] = number_format(($mCauAut[$nInd_mCauAut]['ctobaaiu'] * ($mCauAut[$nInd_mCauAut]['docprovl']/100)),$nDec,'.',''); // Valor Base A.I.U del DO
              $mCauAut[$nInd_mCauAut]['docivaxx'] = number_format(($mCauAut[$nInd_mCauAut]['ctoivaxx'] * ($mCauAut[$nInd_mCauAut]['docprovl']/100)),$nDec,'.',''); // Valor IVA del DO
              $mCauAut[$nInd_mCauAut]['doctotxx'] = number_format(($mCauAut[$nInd_mCauAut]['docbasex'] + $mCauAut[$nInd_mCauAut]['docivaxx']),$nDec,'.',''); 		 	// Valor Total del DO
                
              $nTotalBase[($j+1)] += $mCauAut[$nInd_mCauAut]['docbasex'];
              $nTotalAiu[($j+1)]  += $mCauAut[$nInd_mCauAut]['docbaaiu'];
              $nTotalIva[($j+1)]  += $mCauAut[$nInd_mCauAut]['docivaxx'];
            }		  		
          break;
          case "VALOR":
			  		$mCauAut[$nInd_mCauAut]['docbasex'] = number_format($mCauAut[$nInd_mCauAut]['ctobasex'],$nDec,'.',''); // Valor Base del DO
			  		$mCauAut[$nInd_mCauAut]['docbaaiu'] = number_format($mCauAut[$nInd_mCauAut]['ctobaaiu'],$nDec,'.',''); // Valor Base A.I.U del DO
						$mCauAut[$nInd_mCauAut]['docivaxx'] = number_format($mCauAut[$nInd_mCauAut]['ctoivaxx'],$nDec,'.',''); // Valor IVA del DO
		  			$mCauAut[$nInd_mCauAut]['doctotxx'] = number_format(($mCauAut[$nInd_mCauAut]['docbasex'] + $mCauAut[$nInd_mCauAut]['docivaxx']),$nDec,'.',''); // Valor Total del DO	
          break;
        }
        
			  //Campos Tasa Pactada
			  $mCauAut[$nInd_mCauAut]['tasadiax'] = $_POST['nTasaCambio']; 				 // Tasa de Cambio del dia
			  $mCauAut[$nInd_mCauAut]['clitpxxx'] = $_POST['cCliTp']; 						 // Tasa Pactada
			  $mCauAut[$nInd_mCauAut]['clitpapl'] = $_POST['cCliTpApl']; 					 // Aplica Tasa Pactada
			  $mCauAut[$nInd_mCauAut]['clitpaga'] = $_POST['cCliTpagApl']; 				 // Aplica Tasa de Pago
			  $mCauAut[$nInd_mCauAut]['clitpagv'] = $_POST['cCliTpag']; 					 // Valor Tasa de Pago
			  $mCauAut[$nInd_mCauAut]['combaaiu'] = $_POST['nAiuVlr01']; 					 // Valor Base AIU
			  $mCauAut[$nInd_mCauAut]['comivaiu'] = $_POST['nAiuVlr02']; 					 // Valor IVA AIU
			  $mCauAut[$nInd_mCauAut]['comintpa'] = $_POST['cComIntPa']; 					 // Intermediacion de Pago
	  	}
  	} ## for ($j=0;$j<$_POST['nSecuencia_CCO'];$j++) { ##
  } ## for ($i=0;$i<$_POST['nSecuencia_DO'];$i++) { ##

  //f_Mensaje(__FILE__,__LINE__,"Registros Totales en la Matriz: ".count($mCauAut));
  //f_Mensaje(__FILE__,__LINE__,"Posicion 0: ".$mCauAut[0]['tramitex']." - ".$mCauAut[0]['ctoidxxx']." - ".$mCauAut[0]['docbasex']." - ".$mCauAut[0]['docivaxx']." - ".$mCauAut[0]['doctotxx']);
  //f_Mensaje(__FILE__,__LINE__,"Posicion 1: ".$mCauAut[1]['tramitex']." - ".$mCauAut[1]['ctoidxxx']." - ".$mCauAut[1]['docbasex']." - ".$mCauAut[1]['docivaxx']." - ".$mCauAut[1]['doctotxx']);
  //f_Mensaje(__FILE__,__LINE__,"Posicion 2: ".$mCauAut[2]['tramitex']." - ".$mCauAut[2]['ctoidxxx']." - ".$mCauAut[2]['docbasex']." - ".$mCauAut[2]['docivaxx']." - ".$mCauAut[2]['doctotxx']);
  //f_Mensaje(__FILE__,__LINE__,"Posicion 3: ".$mCauAut[3]['tramitex']." - ".$mCauAut[3]['ctoidxxx']." - ".$mCauAut[3]['docbasex']." - ".$mCauAut[3]['docivaxx']." - ".$mCauAut[3]['doctotxx']);
  
  if ($nSwicht == 0) {  
    /**
     * Hago el llamado al metodo que elabora la causacion automaticamente.
     */
    $oCauAut = new cCausacionAutomaticaTerceros();
    $mReturn_CauAut = $oCauAut->fnCausacionAutomaticaTerceros($mCauAut);

    $cMsj = ""; $nSwicht = 0;
    for ($i=0;$i<count($mReturn_CauAut);$i++) {

      //Validando las retenciones que su base no sobrepasa la base de retencion de la cuenta PUC 
      if  ($mReturn_CauAut[$i]['cPucTipEj'] == "L" || $mReturn_CauAut[$i]['cPucTipEj'] == "") {
        if ($mReturn_CauAut[$i]['cComVlr1'] == "SI" || $mReturn_CauAut[$i]['cComVlr2'] == "SI") {
          if ($mReturn_CauAut[$i]['nPucRet'] > 0) { // Es una retencion
            if ($mReturn_CauAut[$i]['nComBRet'] < $mReturn_CauAut[$i]['nPucBRet']) {
              $nSwicht = 1;
              $cMsj .= "La Base de Retencion [".$mReturn_CauAut[$i]['nComBRet']."] es Menor a la Base de Retencion [".$mReturn_CauAut[$i]['nPucBRet']."] Parametrizada en la Cuenta PUC [".$mReturn_CauAut[$i]['cPucId']."].\n";
            }
          } else { // Es un IVA.
            if ($mReturn_CauAut[$i]['nComBIva'] < $mReturn_CauAut[$i]['nPucBRet']) {
              $nSwicht = 1;
              $cMsj .= "La Base de Retencion [".$mReturn_CauAut[$i]['nComBIva']."] es Menor a la Base de Retencion [".$mReturn_CauAut[$i]['nPucBRet']."] Parametrizada en la Cuenta PUC [".$mReturn_CauAut[$i]['cPucId']."].\n";
            }
          }
        }
      } ?>
      <script languaje = "javascript">
    
        parent.fmwork.document.forms['frgrm']['cComSeq'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComSeq']  ?>";
        parent.fmwork.document.forms['frgrm']['cCtoId'   +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].id    = "<?php echo $mReturn_CauAut[$i]['cCtoId']   ?>";
        parent.fmwork.document.forms['frgrm']['cCtoId'   +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cCtoId']   ?>";
        parent.fmwork.document.forms['frgrm']['cCtoDes'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cCtoDes']  ?>";
        parent.fmwork.document.forms['frgrm']['cComObs'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComObs']  ?>";
        parent.fmwork.document.forms['frgrm']['cComIdC'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComIdC']  ?>";
        parent.fmwork.document.forms['frgrm']['cComCodC' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComCodC'] ?>";
        parent.fmwork.document.forms['frgrm']['cComCscC' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComCscC'] ?>";
        parent.fmwork.document.forms['frgrm']['cComSeqC' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComSeqC'] ?>";
        parent.fmwork.document.forms['frgrm']['cCcoId'   +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cCcoId']   ?>";
        parent.fmwork.document.forms['frgrm']['cSccId'   +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cSccId']   ?>";
        
        parent.fmwork.document.forms['frgrm']['cSucId'   +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cSucId']  ?>";
        parent.fmwork.document.forms['frgrm']['cDocId'   +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cDocId']  ?>";
        parent.fmwork.document.forms['frgrm']['cDocSuf'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cDocSuf'] ?>";
        
        parent.fmwork.document.forms['frgrm']['cComCtoC' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComCtoC'] ?>";
        parent.fmwork.document.forms['frgrm']['nComBRet' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['nComBRet']+0 ?>";
        parent.fmwork.document.forms['frgrm']['nComBIva' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['nComBIva']+0 ?>";
        parent.fmwork.document.forms['frgrm']['nComIva'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['nComIva']+0  ?>";

        switch ("<?php echo $mReturn_CauAut[$i]['cPucTipEj'] ?>") {
          case "L": //Tipo ejecucion Local
            parent.fmwork.document.forms['frgrm']['nComVlr'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['nComVlr']+0 ?>";
            parent.fmwork.document.forms['frgrm']['nComVlrNF'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "";
            
            parent.fmwork.document.forms['frgrm']['nComVlr'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = false;
            parent.fmwork.document.forms['frgrm']['nComVlrNF'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = true;
          break;
          case "N": //Ejecucion NIIF
            parent.fmwork.document.forms['frgrm']['nComVlr'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "";
            parent.fmwork.document.forms['frgrm']['nComVlrNF'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['nComVlrNF']+0 ?>";
              
            parent.fmwork.document.forms['frgrm']['nComVlr'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = true;
            parent.fmwork.document.forms['frgrm']['nComVlrNF'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = false;
          break;
          default: //Ambas
            parent.fmwork.document.forms['frgrm']['nComVlr'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['nComVlr']+0 ?>";
            parent.fmwork.document.forms['frgrm']['nComVlrNF'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['nComVlrNF']+0 ?>";
            
            parent.fmwork.document.forms['frgrm']['nComVlr'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = false;
            parent.fmwork.document.forms['frgrm']['nComVlrNF'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = false;
          break;
        }
        
        parent.fmwork.document.forms['frgrm']['cComMov'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComMov']  ?>";
        parent.fmwork.document.forms['frgrm']['cComNit'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComNit']  ?>";
        parent.fmwork.document.forms['frgrm']['cTerTip'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cTerTip']  ?>";
        parent.fmwork.document.forms['frgrm']['cTerId'   +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cTerId']   ?>";
        parent.fmwork.document.forms['frgrm']['cTerTipB' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cTerTipB'] ?>";

        parent.fmwork.document.forms['frgrm']['cTerIdB'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cTerIdB']   ?>";
        parent.fmwork.document.forms['frgrm']['cPucId'   +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucId']    ?>";
        parent.fmwork.document.forms['frgrm']['cPucDet'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucDet']   ?>";
        parent.fmwork.document.forms['frgrm']['cPucTer'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucTer']   ?>";
        parent.fmwork.document.forms['frgrm']['nPucBRet' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['nPucBRet']  ?>";
        parent.fmwork.document.forms['frgrm']['nPucRet'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['nPucRet']   ?>";
        parent.fmwork.document.forms['frgrm']['cPucNat'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucNat']   ?>";
        parent.fmwork.document.forms['frgrm']['cPucInv'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucInv']   ?>";
        parent.fmwork.document.forms['frgrm']['cPucCco'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucCco']   ?>";
        parent.fmwork.document.forms['frgrm']['cPucDoSc' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucDoSc']  ?>";
        parent.fmwork.document.forms['frgrm']['cPucTipEj'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucTipEj'] ?>";
        parent.fmwork.document.forms['frgrm']['cComVlr1' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComVlr1']  ?>";
        parent.fmwork.document.forms['frgrm']['cComVlr2' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComVlr2']  ?>";
        parent.fmwork.document.forms['frgrm']['cComFac'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComFac']   ?>";
        //Campos de intermediacion de pago
        parent.fmwork.document.forms['frgrm']['cComIdCB'    +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComIdCB']     ?>";
        parent.fmwork.document.forms['frgrm']['cComCodCB'   +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComCodCB']    ?>";
        parent.fmwork.document.forms['frgrm']['cComCscCB'   +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComCscCB']    ?>";
        parent.fmwork.document.forms['frgrm']['cComSeqCB'   +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComSeqCB']    ?>";
        parent.fmwork.document.forms['frgrm']['cCtoIdInp'   +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cCtoIdInp']    ?>";
        parent.fmwork.document.forms['frgrm']['cPucIdInp'   +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucIdInp']    ?>";
        parent.fmwork.document.forms['frgrm']['cPucDetInp'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucDetInp']   ?>";
        parent.fmwork.document.forms['frgrm']['cPucTerInp'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucTerInp']   ?>";
        parent.fmwork.document.forms['frgrm']['nPucBRetInp' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['nPucBRetInp']  ?>";
        parent.fmwork.document.forms['frgrm']['nPucRetInp'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['nPucRetInp']   ?>";
        parent.fmwork.document.forms['frgrm']['cPucNatInp'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucNatInp']   ?>";
        parent.fmwork.document.forms['frgrm']['cPucInvInp'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucInvInp']   ?>";
        parent.fmwork.document.forms['frgrm']['cPucCcoInp'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucCcoInp']   ?>";
        parent.fmwork.document.forms['frgrm']['cPucDoScInp' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucDoScInp']  ?>";
        parent.fmwork.document.forms['frgrm']['cPucTipEjInp'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cPucTipEjInp'] ?>";
        parent.fmwork.document.forms['frgrm']['cComVlr1Inp' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComVlr1Inp']  ?>";
        parent.fmwork.document.forms['frgrm']['cComVlr2Inp' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "<?php echo $mReturn_CauAut[$i]['cComVlr2Inp']  ?>";
          
        if  (parent.fmwork.document.forms['frgrm']['cPucTipEj'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value == "L" || parent.fmwork.document.forms['frgrm']['cPucTipEj'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value == "") {
          if (parent.fmwork.document.forms['frgrm']['cComVlr1' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value == "SI" || parent.fmwork.document.forms['frgrm']['cComVlr2' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value == "SI") {
            if (parent.fmwork.document.forms['frgrm']['nPucRet'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value > 0) { // Es una retencion
              parent.fmwork.document.forms['frgrm']['nComBIva' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = true;
              parent.fmwork.document.forms['frgrm']['nComBIva' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "";
              
              parent.fmwork.document.forms['frgrm']['nComIva'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled  = true;
              parent.fmwork.document.forms['frgrm']['nComIva'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value  = "";
              
              parent.fmwork.document.forms['frgrm']['nComBRet' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = false;
            } else { // Es un IVA.
              parent.fmwork.document.forms['frgrm']['nComBRet' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = true;
              parent.fmwork.document.forms['frgrm']['nComBRet' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "";
              
              parent.fmwork.document.forms['frgrm']['nComBIva' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = false;
              parent.fmwork.document.forms['frgrm']['nComIva'  +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled  = false;
            }
          }
        } else if (parent.fmwork.document.forms['frgrm']['cPucTipEj'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value == "N") {
          //Para la ejecucion NIIF no aplican retenciones, ni IVA
          parent.fmwork.document.forms['frgrm']['nComBRet'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = true; 
          parent.fmwork.document.forms['frgrm']['nComBRet'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "";
          parent.fmwork.document.forms['frgrm']['nComBIva'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = true; 
          parent.fmwork.document.forms['frgrm']['nComBIva'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "";
          parent.fmwork.document.forms['frgrm']['nComIva' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = true; 
          parent.fmwork.document.forms['frgrm']['nComIva' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "";
          parent.fmwork.document.forms['frgrm']['nComVlr' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value = "";	

          if (parent.fmwork.document.forms['frgrm']['cComVlr1' +parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value == "SI" || parent.fmwork.document.forms['frgrm']['cComVlr2'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value == "SI") {
            if (parent.fmwork.document.forms['frgrm']['nPucRet'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value > 0) { // Es una retencion
            //No Hace Nada
            } else { // Es un IVA, se debe digitar base Iva, no se calcula Iva
              parent.fmwork.document.forms['frgrm']['nComBIva'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].disabled = false;
              parent.fmwork.document.forms['frgrm']['nComBIva'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value    = parent.fmwork.document.forms['frgrm']['nComVlrNF'+parent.fmwork.document.forms['frgrm']['nSecuencia'].value].value+0;
            }					  	
          }
        }
        
        //Color para indicar si la secuencia es una retencion de intemediacion de pago
        parent.fmwork.document.getElementById('cComSeq'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "<?php echo ($mReturn_CauAut[$i]['cColor'] != "") ? $mReturn_CauAut[$i]['cColor'] : "#000000" ?>";
        parent.fmwork.document.getElementById('cCtoDes'+ parent.fmwork.document.forms['frgrm']['nSecuencia'].value).style.color = "<?php echo ($mReturn_CauAut[$i]['cColor'] != "") ? $mReturn_CauAut[$i]['cColor'] : "#000000" ?>";
      </script>
      <?php 
      if (($i+1) < count($mReturn_CauAut)) { ?>
        <script languaje = "javascript">
          parent.fmwork.f_Add_New_Row_Comprobante();
        </script>
      <?php  }
    } ?>  
    <script languaje = "javascript">
      parent.fmwork.f_Cuadre_Debitos_Creditos();
    </script>
  <?php }
	
	if ($nSwicht == 1) {
		f_Mensaje(__FILE__,__LINE__,$cMsj);
	} ?>
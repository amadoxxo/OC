<?php
  namespace openComex;
  /**
   * Borrar Comprobante
   * Descripcion: Si el comprobante tiene registros donde alguna de sus cuentas detalla por DO y el comfacxx es diferente de vacio
   *              No Permite Borrar el Comprobante 
   */
  
	include("../../../../libs/php/utility.php");
	include("../../../../libs/php/uticonta.php");
  include("../../../../libs/php/utitrans.php");

	$cMsj = "\n"; $nSwitch = 0;
	
	$cAno = substr($_POST['dComFec'],0,4);

  // Validando si el documento es origen de un documento soporte
  if ($vSysStr['financiero_bloqueo_documentos_ds'] != "SI") {
    $qFcoc  = "SELECT comdsxxx ";
    $qFcoc .= "FROM $cAlfa.fcoc$cAno ";
    $qFcoc .= "WHERE ";
    $qFcoc .= "comidxxx = \"{$_POST['cComId']}\"  AND ";
    $qFcoc .= "comcodxx = \"{$_POST['cComCod']}\" AND ";
    $qFcoc .= "comcscxx = \"{$_POST['cComCsc']}\" AND ";
    $qFcoc .= "comcsc2x = \"{$_POST['cComCsc2']}\"LIMIT 0,1";
    $xFcoc  = f_MySql("SELECT","",$qFcoc,$xConexion01,"");
    //f_Mensaje(__FILE__,__LINE__,$qFcoc."~".mysql_num_rows($xFcoc));
    if (mysql_num_rows($xFcoc) > 0) {
      $vFcoc = mysql_fetch_array($xFcoc);
      if ($vFcoc['comdsxxx'] != "") {
        $nSwitch = 1;    
        $cMsj .= "El Comprobante [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}], es documento Origen del Documento Soporte [{$vFcoc['comdsxxx']}].\n";
      }
    }
  }
	
	//Buscando los datos del comprobante en detalle
  $qFcod  = "SELECT $cAlfa.fcod$cAno.comfacxx  ";
  $qFcod .= "FROM $cAlfa.fcod$cAno ";
  $qFcod .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fcod$cAno.pucidxxx ";
  $qFcod .= "WHERE ";
  $qFcod .= "$cAlfa.fpar0115.pucdetxx  = \"D\" AND ";
  $qFcod .= "$cAlfa.fcod$cAno.comfacxx <> \"\" AND ";
  $qFcod .= "$cAlfa.fcod$cAno.comidxxx = \"{$_POST['cComId']}\"  AND ";
  $qFcod .= "$cAlfa.fcod$cAno.comcodxx = \"{$_POST['cComCod']}\" AND ";
  $qFcod .= "$cAlfa.fcod$cAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";
  $qFcod .= "$cAlfa.fcod$cAno.comcsc2x = \"{$_POST['cComCsc2']}\" ";
  $qFcod .= "ORDER BY $cAlfa.fcod$cAno.comfacxx";
  $xFcod  = f_MySql("SELECT","",$qFcod,$xConexion01,"");
  //f_Mensaje(__FILE__,__LINE__,$qFcod."~".mysql_num_rows($xFcod));
  if (mysql_num_rows($xFcod) > 0) {
    $mFacturas = array();
    while ($xRF = mysql_fetch_array($xFcod)) {
      if (in_array($xRF['comfacxx'],$mFacturas) == false) {
        $mFacturas[count($mFacturas)] = $xRF['comfacxx'];
      }
    }    
    $cFacturas = implode(", ",$mFacturas);
    
    $nSwitch = 1;
    $cMsj .= "El Comprobante [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}] esta Facturado con la(s) Factura(s): $cFacturas.\n";
  }
	
	if ($vSysStr['financiero_permitir_borrar_documento_cruce'] != "SI") {
		//Buscando las cuentas por cobrar o por pagar donde el comprobante sea documento cruce
		$mDocCru = array();
		$qFcod  = "SELECT ";
		$qFcod .= "$cAlfa.fcod$cAno.comidxxx,";
		$qFcod .= "$cAlfa.fcod$cAno.comcodxx,";
		$qFcod .= "$cAlfa.fcod$cAno.comcscxx,";
		$qFcod .= "$cAlfa.fcod$cAno.comcsc2x,";
		$qFcod .= "$cAlfa.fcod$cAno.comseqxx,";
		$qFcod .= "$cAlfa.fcod$cAno.teridxxx,";
		$qFcod .= "$cAlfa.fcod$cAno.terid2xx,";
		$qFcod .= "$cAlfa.fcod$cAno.pucidxxx ";
    $qFcod .= "FROM $cAlfa.fcod$cAno ";
    $qFcod .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fcod$cAno.pucidxxx ";
    $qFcod .= "WHERE ";
    $qFcod .= "$cAlfa.fpar0115.pucdetxx IN (\"P\",\"C\") AND ";
    $qFcod .= "$cAlfa.fcod$cAno.comidxxx = \"{$_POST['cComId']}\"  AND ";
    $qFcod .= "$cAlfa.fcod$cAno.comcodxx = \"{$_POST['cComCod']}\" AND ";
    $qFcod .= "$cAlfa.fcod$cAno.comcscxx = \"{$_POST['cComCsc']}\" AND ";
    $qFcod .= "$cAlfa.fcod$cAno.comcsc2x = \"{$_POST['cComCsc2']}\" ";
		$xFcod  = f_MySql("SELECT","",$qFcod,$xConexion01,"");							
		//f_Mensaje(__FILE__,__LINE__,$qFcod."~".mysql_num_rows($xFcod));
		while ($xRF = mysql_fetch_array($xFcod)) {
			for ($nAnoPer = $cAno; $nAnoPer <= date('Y'); $nAnoPer++) {
				//Se busca en el año anterior y año siguiente donde es documento cruce
				$qDocCru  = "SELECT ";
				$qDocCru .= "$cAlfa.fcod$nAnoPer.comidxxx,";
				$qDocCru .= "$cAlfa.fcod$nAnoPer.comcodxx,";
				$qDocCru .= "$cAlfa.fcod$nAnoPer.comcscxx,";
				$qDocCru .= "$cAlfa.fcod$nAnoPer.comcsc2x,";
				$qDocCru .= "$cAlfa.fcod$nAnoPer.comseqxx,";
				$qDocCru .= "$cAlfa.fcod$nAnoPer.teridxxx,";
				$qDocCru .= "$cAlfa.fcod$nAnoPer.terid2xx,";
				$qDocCru .= "$cAlfa.fcod$nAnoPer.pucidxxx ";
        $qDocCru .= "FROM $cAlfa.fcod$nAnoPer ";
        $qDocCru .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fcod$nAnoPer.pucidxxx ";
        $qDocCru .= "WHERE ";
        $qDocCru .= "$cAlfa.fcod$nAnoPer.comidcxx  = \"{$xRF['comidxxx']}\" AND ";
        $qDocCru .= "$cAlfa.fcod$nAnoPer.comcodcx  = \"{$xRF['comcodxx']}\" AND ";
        $qDocCru .= "$cAlfa.fcod$nAnoPer.comcsccx  = \"{$xRF['comcscxx']}\" AND ";
        $qDocCru .= "$cAlfa.fcod$nAnoPer.teridxxx  = \"{$xRF['teridxxx']}\" AND ";
        $qDocCru .= "$cAlfa.fcod$nAnoPer.terid2xx  IN (\"{$xRF['teridxxx']}\",\"{$xRF['terid2xx']}\") AND ";
        $qDocCru .= "$cAlfa.fcod$nAnoPer.pucidxxx  = \"{$xRF['pucidxxx']}\" AND ";
        $qDocCru .= "$cAlfa.fcod$nAnoPer.regestxx  = \"ACTIVO\" ";
				$xDocCru  = f_MySql("SELECT","",$qDocCru,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qDocCru."~".mysql_num_rows($xDocCru));
				while ($xRDC = mysql_fetch_array($xDocCru)) {
					if ("{$xRDC['comidxxx']}-{$xRDC['comcodxx']}-{$xRDC['comcscxx']}-{$xRDC['comcsc2x']}" != "{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}-{$_POST['cComCsc2']}") {
						$mDocCru[count($mDocCru)] = "{$xRDC['comidxxx']}-{$xRDC['comcodxx']}-{$xRDC['comcscxx']}-{$xRDC['comcsc2x']}";	
					}
				}
			}
		}

		if (count($mDocCru) > 0) {
    	$cDocCru = implode(", ",$mDocCru);
    	$nSwitch = 1;
    	$cMsj .= "El Comprobante [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}] es Documento Cruce en: $cDocCru.\n\n";
		}
	}	
  if($nSwitch == 0){
    //Transmision ERP
    $vDatosIntegracion['comidxxx'] = $_POST['cComId'];      //Comprobante
    $vDatosIntegracion['comcodxx'] = $_POST['cComCod'];     //Codigo Comprobante
    $vDatosIntegracion['comcscxx'] = $_POST['cComCsc'];     //Consecutivo Uno
    $vDatosIntegracion['comcsc2x'] = $_POST['cComCsc2'];    //Consecutivo Dos
    $vDatosIntegracion['comfecxx'] = $_POST['dComFec'];     //Fecha Comprobante
    $vDatosIntegracion['modoxxxx'] = "CAMBIAESTADO-BORRAR"; //Modo

    $ObjWsTransmisionxIntegracion = new cTransmisionxIntegracion();
    $mRetorna = $ObjWsTransmisionxIntegracion->fnTransmitirComprobante($vDatosIntegracion);
    $cMsjAdv = "";
    for ($nR=6; $nR< count($mRetorna); $nR++) {
      $vAuxText = explode("~",$mRetorna[$nR]);
      $cMsjAdv .= ($vAuxText[0] != "") ? "Linea ".str_pad($vAuxText[0],4,"0",STR_PAD_LEFT).": " : "";
      $cMsjAdv .= $vAuxText[1]."\n";
    }
    if ($cMsjAdv != "") {
      f_Mensaje(__FILE__,__LINE__,$cMsjAdv);
    }
  }
	
	if ($nSwitch == 0) { 
    if (!f_Borra_Saldos_Cuentas($_POST['cComId'],$_POST['cComCod'],$_POST['cComCsc'],$_POST['cComCsc2'],$_POST['dComFec'],"BORRAR")) {
			$nSwitch = 1;
			$cMsj  = "EL PROCESO DE BORRAR EL COMPROBANTE [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}] SE EJECUTO CON ERRORES\n";
			$cMsj .= "Se Presentaron Inconvenientes al Borrar el Comprobante de los Modulos de Contabilidad, ";
			$cMsj .= "CxC, CxP y DoS, Revise el Comprobante para Verificar que Haya Quedado Bien Borrado.";
			f_Mensaje(__FILE__,__LINE__,$cMsj); ?>
		<?php }
	}else{
		$cMsj  = "EL COMPROBANTE [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}] NO PUEDE SER BORRADO\n".$cMsj;
		f_Mensaje(__FILE__,__LINE__,$cMsj);?>
		<script languaje = "javascript">
			parent.fmwork.f_Retorna();
		</script>
	<?php }
	
	if ($nSwitch == 0) {
		$cMsj = "EL COMPROBANTE [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}] SE BORRO CON EXITO\n";
		f_Mensaje(__FILE__,__LINE__,$cMsj); ?>
			<!-- Limpio los Documentos Cruce -->
      <script languaje = "javascript">
        for (i=0;i<parent.fmwork.document.forms['frgrm']['nSecuencia'].value;i++) {
          if (parent.fmwork.document.forms['frgrm']['cComIdC' +(i+1)].value == parent.fmwork.document.forms['frgrm']['cComId'].value  &&
              parent.fmwork.document.forms['frgrm']['cComCodC'+(i+1)].value == parent.fmwork.document.forms['frgrm']['cComCod'].value &&
              parent.fmwork.document.forms['frgrm']['cComCscC'+(i+1)].value == parent.fmwork.document.forms['frgrm']['cComCsc'].value &&
              parent.fmwork.document.forms['frgrm']['cComSeqC'+(i+1)].value == "001") {
              //parent.fmwork.document.forms['frgrm']['cCcoId'  +(i+1)].value == parent.fmwork.document.forms['frgrm']['cCcoId'].value) {
            parent.fmwork.document.forms['frgrm']['cComIdC' +(i+1)].value = "";
            parent.fmwork.document.forms['frgrm']['cComCodC'+(i+1)].value = "";
            parent.fmwork.document.forms['frgrm']['cComCscC'+(i+1)].value = "";
            parent.fmwork.document.forms['frgrm']['cComSeqC'+(i+1)].value = "";
            //parent.fmwork.document.forms['frgrm']['cCcoId'  +(i+1)].value = "";
          }
        }
        parent.fmwork.document.forms['frgrm']['Btn_Guardar'].disabled = false;
      </script>
      <!-- Fin de Limpio los Documentos Cruce -->
		<?php }  ?>
<?php
  namespace openComex;
  use FPDF;

  /**
	 * Imprime Anexo de Factura Aduana -Pagos a Terceros Servicios y Anticipos .
	 * --- Descripcion: Permite Imprimir Anexo de Factura Aduana Pagos a Terceros Servicios y Anticipos.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 * @version 002
	 */
  include("../../../../libs/php/utility.php");

  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
  $pdf = new FPDF('P','mm','Letter');
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->SetFont('verdana','',8);
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);


  global $xConexion01; global $cAlfa;
  $mPrn = explode("|",$prints);
  /*for ($nn=0;$nn<count($mPrn);$nn++) {
    if (strlen($mPrn[$nn]) > 0) {
      $vComp = explode("~",$mPrn[$nn]);
  		$cComId   = $vComp[0];
  		$cComCod  = $vComp[1];
  		$cComCsc  = $vComp[3];
  		$cComCsc2 = $vComp[3];
  		$cRegFCre = $vComp[4];
  		$cNewYear = date("Y");
    }
  }*/

  /***** CABECERA 1001 *****/
	$qCocDat  = "SELECT ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.*, ";

	$qCocDat .= "IF($cAlfa.fpar0008.sucidxxx <> \"\",$cAlfa.fpar0008.sucidxxx,\"SUCURSAL SIN ID\") AS sucidxxx, ";
	$qCocDat .= "IF($cAlfa.fpar0008.sucdesxx <> \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS CLINOMXX, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX <> \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX <> \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.CLIFAXXX <> \"\",$cAlfa.SIAI0150.CLIFAXXX,\"SIN TELEFONO\") AS CLIFAXXX, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.CLICONTX <> \"\",$cAlfa.SIAI0150.CLICONTX,\"SIN RESPONSABLE\") AS CLICONTX, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.PAIIDXXX <> \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX <> \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
	$qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX <> \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX ";
	$qCocDat .= "FROM $cAlfa.fcoc$cNewYear ";
	$qCocDat .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcoc$cNewYear.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
  $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cNewYear.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
	$qCocDat .= "WHERE $cAlfa.fcoc$cNewYear.comidxxx = \"F\" AND ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.teridxxx LIKE \"%$gTerId%\" AND ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.ccoidxxx LIKE \"%$gSucId%\" AND ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.comcscxx LIKE \"%$cComCsc%\" AND ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.regfcrex BETWEEN  \"$gDesde\" AND \"$gHasta\" AND ";
	$qCocDat .= "$cAlfa.fcoc$cNewYear.regestxx =\"ACTIVO\" ";
	//f_Mensaje(__FILE__,__LINE__,$qCocDat);
	$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
	$nFilCoc  = mysql_num_rows($xCocDat);
	if ($nFilCoc > 0) {
    while($xRCD = mysql_fetch_array($xCocDat)){
	 /*****CONSULTA A LA 1002 *****/
	   $nInd_mCocDat = count($mCocDat);
		 $mCocDat[$nInd_mCocDat]= $xRCD;
		 $qCodDat  = "SELECT DISTINCT ";
     $qCodDat .= "$cAlfa.fcod$cNewYear.*, ";
     $qCodDat .= "IF($cAlfa.fpar0008.sucdesxx <> \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
     $qCodDat .= "(TRIM(CONCAT($cAlfa.SIAI0150.CLINOMXX,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X))) AS CLINOMXX ";
     $qCodDat .= "FROM $cAlfa.fcod$cNewYear ";
     $qCodDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$cNewYear.terid2xx = $cAlfa.SIAI0150.CLIIDXXX ";
     $qCodDat .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcod$cNewYear.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
  	 $qCodDat .= "WHERE $cAlfa.fcod$cNewYear.comidxxx = \"{$xRCD['comidxxx']}\" AND ";
  	 $qCodDat .= "$cAlfa.fcod$cNewYear.comcodxx = \"{$xRCD['comcodxx']}\" AND ";
  	 $qCodDat .= "$cAlfa.fcod$cNewYear.comcscxx = \"{$xRCD['comcscxx']}\" AND ";
  	 $qCodDat .= "$cAlfa.fcod$cNewYear.comcsc2x = \"{$xRCD['comcsc2x']}\" ORDER BY ABS($cAlfa.fcod$cNewYear.comseqxx) ASC ";
  	 $xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
  	 $nFilCod  = mysql_num_rows($xCodDat);
  	 if ($nFilCod > 0) {
  	   while ($xRCDo = mysql_fetch_array($xCodDat)) {
  		  $nInd_mCodDat = count($mCodDat);
			  $mCodDat[$nInd_mCodDat]= $xRCDo;
  		 }
  	  }

    }
  }
  $mDoiId    = array();
  $mConcepto = array();
  $mFacturas = array();
  for($a=0;$a<count($mCocDat);$a++){
    $mDo = f_Explode_Array($mCocDat[$i]['commemod'],"|","~");
    $cFactura = $mCocDat[$i]['comidxxx']."-".$mCocDat[$i]['comcodxx']."-".$mCocDat[$i]['comcscxx']."-".$mCocDat[$i]['comcsc2x'];
    for ($i=0;$i<count($mDo);$i++) {
      if ($mDo[$i][14] != "") {
          $nSwitch_Encontre_Do = 0;
          for ($j=0;$j<count($mDoiId);$j++) {
            if ($mDoiId[$j][14] == $mDo[$i][14]) {
              if($mDoiId[$j][1] == $mDo[$i][1]){
              $nSwitch_Encontre_Do = 1;
              $mDoiId[$j][7] += $mDo[$i][7]; // Acumulo el valor de ingreso para tercero.
              $mDoiId[$j][100] = ((strlen($mDoiId[$j][100]) + strlen($mDo[$i][5]) + 1) < 16) ? $mDoiId[$j][100]."/".$mDo[$i][5] : $mDoiId[$j][100];
              $mDoiId[$j][100] = (substr($mDoiId[$j][100],0,1) == "/") ? substr($mDoiId[$j][100],1,strlen($mDoiId[$j][100])) : $mDoiId[$j][100];
              $j = count($mDoiId); // Me salgo del FOR cuando encuentro el concepto.
            }
           }
          }
          if ($nSwitch_Encontre_Do == 0) { // No encontre el ingreso para tercero en la matrix $mDoiId
            $nInd_mDoiId = count($mDoiId);
            $mDoiId[$nInd_mDoiId] = $mDo[$i]; // Ingreso el registro como nuevo.
            $mDoiId[$nInd_mDoiId][100] = ((strlen($mDoiId[$nInd_mDoiId][100]) + strlen($mDo[$i][5]) + 1) < 16) ? $mDoiId[$nInd_mDoiId][100]."/".$mDo[$i][5] : $mDoiId[$nInd_mDoiId][100];
            $mDoiId[$nInd_mDoiId][100] = (substr($mDoiId[$nInd_mDoiId][100],0,1) == "/") ? substr($mDoiId[$nInd_mDoiId][100],1,strlen($mDoiId[$nInd_mDoiId][100])) : $mDoiId[$nInd_mDoiId][100];
            $mDoiId[$nInd_mDoiId][101] = $cFactura;
            $nInd_mConcepto = count($mConcepto);
            $mConcepto[$nInd_mConcepto]['cCtoId']  = $mDoiId[$i][1];
            $mConcepto[$nInd_mConcepto]['cCtoDes'] = $mDoiId[$i][2];
          }
        }
      }
  }

   /***** Cargo Matriz con Do's*****/
  $mMatrizDo = array();
  for($a=0;$a<count($mCocDat);$a++){
    $mDo = f_Explode_Array($mCocDat[$a]['commemod'],"|","~");
    //$cFac = $mCocDat[$i]['comidxxx']."-".$mCocDat[$i]['comcodxx']."-".$mCocDat[$i]['comcscxx']."-".$mCocDat[$i]['comcsc2x'];
    for ($k=0;$k<count($mDo);$k++) {
     if ($mDo[$k][14] != "") {
  	   $nSwitch_Encontre_Doi = 0;
  	   for ($l=0;$l<count($mMatrizDo);$l++) {
  	     if ($mMatrizDo[$l][0] == $mDo[$k][14]) {
  	       $nSwitch_Encontre_Doi = 1;
  	     }
  	   }
  	   if ($nSwitch_Encontre_Doi == 0) { // No encontre el ingreso para tercero en la matrix $mDoiId
  	     $nInd_mMatrizDo = count($mMatrizDo);
  	     $mMatrizDo[$nInd_mMatrizDo][0]  = $mDo[$k][14];
  	     $mMatrizDo[$nInd_mMatrizDo][1]  = $mCocDat[$a]['CLINOMXX'];
  	     $mMatrizDo[$nInd_mMatrizDo][2]  = $mCocDat[$a]['terid2xx'];
  	     $mMatrizDo[$nInd_mMatrizDo][3]  = $mCocDat[$a]['sucdesxx'];
  	     $mMatrizDo[$nInd_mMatrizDo][4]  = $mCocDat[$a]['comcscxx'];
  	     $mMatrizDo[$nInd_mMatrizDo][5]  = $mCocDat[$a]['comfpxxx'];
  	   }
  	 }
    }
  }
  /*****Fin de Carga Matriz de Do's *****/


  /***** Cargo Matriz Con Facturas *****/
  for($i=0;$i<count($mCocDat);$i++){
    $cFactu = $mCocDat[$i]['comidxxx']."-".$mCocDat[$i]['comcodxx']."-".$mCocDat[$i]['comcscxx']."-".$mCocDat[$i]['comcsc2x'];
    $nInd_mFacturas = count($mFacturas);
    $mFacturas[$nInd_mFacturas] = $cFactu;
  }
  /*****Fin Carga de Facturas *****/

  /***** Carga Matriz con Conceptos Ingresos Propios *****/
  $mMatrizIP = array();
  for($i=0;$i<count($mCodDat);$i++){
    if($mCodDat[$i]['comctocx'] == "IP"){
      $nSwitch_Encontre_IP = 0;
      for($k=0;$k<count($mMatrizIP);$k++){
        if ($mMatrizIP[$k] == $mCodDat[$i]['ctoidxxx']) {
          $nSwitch_Encontre_IP = 1;
        }
      }
      if($nSwitch_Encontre_IP == 0) { // No encontre el Ingreso para Tercero en la Matrix $mDoiId
        $nInd_mMatrizIP = count($mMatrizIP);
        $mMatrizIP[$nInd_mMatrizIP]  = $mCodDat[$i]['ctoidxxx'];
      }
    }
  }
  /***** Fin Matriz Conceptos Ingresos Propios *****/

  /***** Cargo Matriz Concepto PCC de la 1002 *****/
  $mMatrizPCC = array();
    for($i=0;$i<count($mCodDat);$i++){
      if($mCodDat[$i]['comctocx'] == "PCC" && $mCodDat[$i]['comtraxx'] != ""){
        $nSwitch_Encontre_PCC = 0;
        for($k=0;$k<count($mMatrizPCC);$k++){
          if($mMatrizPCC[$k] == $mCodDat[$i]['ctoidxxx']) {
            $nSwitch_Encontre_PCC = 1;
           }
          }
          if($nSwitch_Encontre_PCC == 0) { // No encontre el ingreso para tercero en la matrix $mDoiId
            $nInd_mMatrizPCC = count($mMatrizPCC);
            $mMatrizPCC[$nInd_mMatrizPCC]  = $mCodDat[$i]['ctoidxxx'];
          }
      }
    }
    /***** Fin Matriz Concepto PCC de la 1002 *****/

  /***** Busco si en la 1002 hay Do�s diferentes a los del campo commemod de la 1001 *****/
  for($i=0;$i<count($mCodDat);$i++){
    if($mCodDat[$i]['comctocx'] == "PCC" && $mCodDat[$i]['comtraxx'] != ""){
      $nSwitch_Encontre_Doi = 0;
      for($k=0;$k<count($mMatrizDo);$k++){
        if($mMatrizDo[$k][0] == $mCodDat[$i]['comtraxx']) {
          $nSwitch_Encontre_Doi = 1;
         }
      }
      if($nSwitch_Encontre_Doi == 0) { // No encontre el Do en la Matriz $mMatrizDo
        $nInd_mMatrizDo = count($mMatrizDo);
        $mMatrizDo[$nInd_mMatrizDo][0]  = $mCodDat[$i]['comtraxx'];
        $mMatrizDo[$nInd_mMatrizDo][1]  = $mCodDat[$i]['CLINOMXX'];
        $mMatrizDo[$nInd_mMatrizDo][2]  = $mCodDat[$i]['terid2xx'];
        $mMatrizDo[$nInd_mMatrizDo][3]  = $mCodDat[$i]['sucdesxx'];
        $mMatrizDo[$nInd_mMatrizDo][4]  = $mCodDat[$i]['comcscxx'];
        $mMatrizDo[$nInd_mMatrizDo][5]  = "";

      }
    }
  }
  /***** Fin busqueda de Do's *****/

  /*****Cargo conceptos de ingresos propios de 1002  *****/
  for($i=0;$i<count($mCodDat);$i++){
    if($mCodDat[$i]['comctocx'] == "IP" && $mCodDat[$i]['comtraxx'] != ""){
      $nSwitch_Encontre_Doi = 0;
      for($k=0;$k<count($mMatrizDo);$k++){
        if ($mMatrizDo[$k][0] == $mCodDat[$i]['comtraxx']) {
          $nSwitch_Encontre_Doi = 1;
        }
      }
      if($nSwitch_Encontre_Doi == 0) { // No encontre el ingreso para tercero en la matriz $mMatrizDo
        $nInd_mMatrizDo = count($mMatrizDo);
        $mMatrizDo[$nInd_mMatrizDo][0]  = $mCodDat[$i]['comtraxx'];
        $mMatrizDo[$nInd_mMatrizDo][1]  = $mCodDat[$i]['CLINOMXX'];
        $mMatrizDo[$nInd_mMatrizDo][2]  = $mCodDat[$i]['terid2xx'];
        $mMatrizDo[$nInd_mMatrizDo][3]  = $mCodDat[$i]['sucdesxx'];
        $mMatrizDo[$nInd_mMatrizDo][4]  = $mCodDat[$i]['comcscxx'];
        $mMatrizDo[$nInd_mMatrizDo][5]  = "";

      }
    }
  }
  /***** Fin conceptos de 1002 *****/




  /***** Comienzo a pintar Anexo de Factura *****/
  //$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/alpopul1.jpg',30,8,80,7);
  $posy	= 15;  /** PRIMERA POSICION DE Y **/
  $posx	= 20;
  $nPag = 1;
  /* Siguiente Pagina */
  $pdf->AddPage();
  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/alpopul1.jpg',15,8,80,11);
  $pdf->SetFont('verdanab','',9);
  $pdf->setXY(80,$posy);
  $pdf->Cell(50,10,"ANEXO DE FACTURA ADUANA - PAGOS A TERCEROS SERVICIOS Y ANTICIPOS",0,0,'C');
    for($h=0;$h<count($mFacturas);$h++){
      for($k=0;$k<count($mMatrizDo);$k++){
        $posyFin = 250;
        $posy += 10;
        $pdf->SetFont('verdanab','',7);
        $pdf->setXY(15,$posy);
        $pdf->Cell(10,10,"Cliente:",0,0,'L');
        $pyy = $posy;
        $alinea = explode("~",f_Words($mMatrizDo[$k][1],80));
        $cCliNom = $mMatrizDo[$k][1];
        for ($n=0;$n<count($alinea);$n++) {
      		$pdf->SetFont('verdana','',7);
        	$pdf->setXY(45,$pyy+3);
          $pdf->Cell(10,3,$alinea[$n]);
          $pyy += 3;
        }

        $pdf->setXY(130,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(10,10,"NIT:",0,0,'L');
        $pdf->setXY(170,$posy);
        $pdf->SetFont('verdana','',7);
        $pdf->Cell(10,10,substr($mMatrizDo[$k][2]."-".f_Digito_Verificacion($mMatrizDo[$k][2]),0,45),0,0,'L');

        $posy += 4;
        $pdf->setXY(15,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(10,10,"Sucursal:",0,0,'L');
        $pdf->setXY(45,$posy);
        $pdf->SetFont('verdana','',7);
        $pdf->Cell(10,10,$mMatrizDo[$k][3],0,0,'L');
        $pdf->setXY(130,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(10,10,"Fra ALPOPULAR:",0,0,'L');
        $pdf->setXY(170,$posy);
        $pdf->SetFont('verdana','',7);
        $pdf->Cell(10,10,$mMatrizDo[$k][4],0,0,'L');

        $posy += 4;
        $pdf->setXY(15,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(10,10,"D.O:",0,0,'L');
        $pdf->setXY(45,$posy);
        $pdf->SetFont('verdana','',7);
        $pdf->Cell(10,10,$mMatrizDo[$k][0],0,0,'L');
        $pdf->setXY(130,$posy);

        /***** Consulto en la SIAI0200 Datos para encabezado *****/
        $mDoiDat = explode("-",$mMatrizDo[$k][0]);
        $qDoiDat  = "SELECT * ";
        $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
        $qDoiDat .= "WHERE $cAlfa.SIAI0200.DOIIDXXX = \"{$mDoiDat[1]}\" AND ";
        $qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"{$mDoiDat[2]}\" AND ";
        $qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$mDoiDat[0]}\" ";
        $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");

        //f_Mensaje(__FILE__,__LINE__,$qDoiDat);
      	$nFilDoi  = mysql_num_rows($xDoiDat);
      	if ($nFilDoi > 0) {
      	  $vDoiDat  = mysql_fetch_array($xDoiDat);
      	}
        /***** Fin Consulta a la SIAI0200 *****/
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(10,10,"Documento de Transporte:",0,0,'L');
        $pdf->setXY(170,$posy);
        $pdf->SetFont('verdana','',7);
        $pdf->Cell(10,10,$vDoiDat['DGEDTXXX'],0,0,'L');

        $posy += 4;
        $pdf->setXY(15,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(10,10,"Factura Comercial:",0,0,'L');
        $qFacDat  = "SELECT * ";
        $qFacDat .= "FROM $cAlfa.SIAI0204 ";
        $qFacDat .= "WHERE ";
        $qFacDat .= "$cAlfa.SIAI0204.DOIIDXXX = \"{$vDoiDat['DOIIDXXX']}\" AND ";
        $qFacDat .= "$cAlfa.SIAI0204.DOISFIDX = \"{$vDoiDat['DOISFIDX']}\" AND ";
        $qFacDat .= "$cAlfa.SIAI0204.ADMIDXXX = \"{$vDoiDat['ADMIDXXX']}\" ";
        $qFacDat .= "ORDER BY $cAlfa.SIAI0204.FACIDXXX ";
        $xFacDat  = f_MySql("SELECT","",$qFacDat,$xConexion01,"");
        $x = 0;
        while($xRFD = mysql_fetch_array($xFacDat)){
         if($x == 0){
           $cFactura = $xRFD['FACIDXXX'];
           $x++;
          }else{
            $cFactura .= "-".$xRFD['FACIDXXX'];
           }
        }

        $pyy1 = $posy;
        $alinea1 = explode("~",f_Words($cFactura,145));
        for ($n=0;$n<count($alinea1);$n++) {
      		$pdf->SetFont('verdana','',7);
        	$pdf->setXY(45,$pyy1+3);
          $pdf->Cell(30,3,$alinea1[$n]);
          $pyy1+=3;
        }

        $posy += 4;
        $pdf->setXY(15,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(10,10,"Pedido:",0,0,'L');
        $pdf->setXY(45,$posy);
        $pdf->SetFont('verdana','',7);
        $pdf->Cell(10,10,$vDoiDat['DOIPEDXX'],0,0,'L');
        $pdf->setXY(130,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(10,10,"Valor C.I.F:",0,0,'L');

        /***** Traigo Valor CIF de la SIAI0206 *****/
        $qDecDat  = "SELECT ";
      	$qDecDat .= "SUM($cAlfa.SIAI0206.LIMCIFXX) AS LIMCIFXX ";
      	$qDecDat .= "FROM $cAlfa.SIAI0206 ";
      	$qDecDat .= "WHERE ";
      	$qDecDat .= "$cAlfa.SIAI0206.DOIIDXXX = \"{$vDoiDat['DOIIDXXX']}\" AND ";
      	$qDecDat .= "$cAlfa.SIAI0206.DOISFIDX = \"{$vDoiDat['DOISFIDX']}\" AND ";
      	$qDecDat .= "$cAlfa.SIAI0206.ADMIDXXX = \"{$vDoiDat['ADMIDXXX']}\" ";
        $qDecDat .= "GROUP BY $cAlfa.SIAI0206.DOIIDXXX ";
        $xDecDat  = f_MySql("SELECT","",$qDecDat,$xConexion01,"");
      	$nFilDec  = mysql_num_rows($xDecDat);
      	if ($nFilDec > 0) {
      	  $vDecDat  = mysql_fetch_array($xDecDat);
      	}


        $pdf->setXY(170,$posy);
        $pdf->SetFont('verdana','',7);
        $pdf->Cell(10,10,number_format($vDecDat['LIMCIFXX'],0,',','.'),0,0,'L');

        $posy +=7;
        $pdf->setXY(65,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(10,10,"DETALLE DE COBRO",0,0,'L');

        $posy +=5;
        $pdf->setXY(20,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(15,10,"Cod. Servicio",0,0,'L');
        $pdf->setXY(70,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(30,10,"Descripcion",0,0,'L');
        $pdf->setXY(160,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(20,10,"Valor",0,0,'C');

        $posy +=4;
        $pdf->setXY(60,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(20,10,"Pagos a Terceros",0,0,'L');


        $posy += 4;
        $nTotPCC = 0;

        for($l=0;$l<count($mDoiId);$l++){
          if($mFacturas[$h] == $mDoiId[$l][101]){
            if($mMatrizDo[$k][0] == $mDoiId[$l][14]){
              for($j=0;$j<count($mConcepto);$j++){
                if($mConcepto[$j]['cCtoId'] == $mDoiId[$l][1]){
                  $pdf->setXY(20,$posy);
                  $pdf->SetFont('verdana','',7);
                  $pdf->Cell(15,10,$mConcepto[$j]['cCtoId'],0,0,'L');
                  $pdf->setXY(60,$posy);
                  $pdf->SetFont('verdana','',7);
                  $cComObs  = explode("^",$mConcepto[$j]['cCtoDes']);
                  $pdf->Cell(30,10,$cComObs[0],0,0,'L');
                  $pdf->setXY(170,$posy);
                  $pdf->SetFont('verdana','',7);
                  $pdf->Cell(20,10,number_format($mDoiId[$l][7],0,',','.'),0,0,'R');
                  $posy +=3;
                  $nTotPCC += $mDoiId[$l][7];
                }
              }
            }
          }
        }



      $nTotPCC2 = 0;
        for($m=0;$m<count($mCodDat);$m++){
          if($mCodDat[$m]['comctocx'] == "PCC" && $mCodDat[$m]['comtraxx'] != ""){
            $cFacPCC = $mCodDat[$m]['comidxxx']."-".$mCodDat[$m]['comcodxx']."-".$mCodDat[$m]['comcscxx']."-".$mCodDat[$m]['comcsc2x'];
            if($cFacPCC == $mFacturas[$h]){
              if($mCodDat[$m]['comtraxx'] == $mMatrizDo[$k][0]){
                for($n=0;$n<count($mMatrizPCC);$n++){
                  if($mMatrizPCC[$n] == $mCodDat[$m]['ctoidxxx']){
                    $pdf->setXY(20,$posy);
                    $pdf->SetFont('verdana','',7);
                    $pdf->Cell(15,10,$mMatrizPCC[$n],0,0,'L');
                    $pdf->setXY(60,$posy);
                    $pdf->SetFont('verdana','',7);
                    $nComObs_IP = stripos($mCodDat[$m]['comobsxx'], "[");
                    if($nComObs_IP > 0){
                      $pdf->Cell(30,10,substr($mCodDat[$m]['comobsxx'],0,$nComObs_IP),0,0,'L');
                    }else{
                      $pdf->Cell(30,10,substr($mCodDat[$m]['comobsxx'],0,40),0,0,'L');
                    }
                    $pdf->setXY(170,$posy);
                    $pdf->SetFont('verdana','',7);
                    $pdf->Cell(20,10,number_format($mCodDat[$m]['comvlrxx'],0,',','.'),0,0,'R');
                    $posy +=3;
                    $nTotPCC2 += $mCodDat[$m]['comvlrxx'];
                  }
                }
              }
            }
          }
        }

         $mComFp = f_Explode_Array($mMatrizDo[5]['comfpxxx'],"|","~");
         $ToComFp = 0;
    	   $qCtoDat  = "SELECT * ";
    	   $qCtoDat .= "FROM $cAlfa.fpar0119 ";
    	   $qCtoDat .= "WHERE ";
    	   $qCtoDat .= "$cAlfa.fpar0119.ctoclaxf = \"IMPUESTOFINANCIERO\" AND ";
    	   $qCtoDat .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
    	   $xCtoDat  = f_MySql("SELECT","",$qCtoDat,$xConexion01,"");
    	   $nFilCto  = mysql_num_rows($xCtoDat);
    		 if ($nFilCto > 0) {
    		  $vCtoDat = mysql_fetch_array($xCtoDat);
    		  }
         for($y=0;$y<count($mComFp);$y++){
         	$mMDo = explode("-",$mMatrizDo[$k][0]);
        	$mCompDo = $mMDo[1]."-".$mMDo[2];
        	if($mCompDo == $mComFp[$y][2]."-".$mComFp[$y][3]){
        		$ToComFp += $mComFp[$y][18];
        		$pdf->setXY(20,$posy);
            $pdf->SetFont('verdana','',7);
            $pdf->Cell(15,10,$vCtoDat['ctoidxxx'],0,0,'L');
            $pdf->setXY(60,$posy);
            $pdf->SetFont('verdana','',7);
            $pdf->Cell(30,10,substr($vCtoDat['ctodesxf'],0,40),0,0,'L');
            $pdf->setXY(170,$posy);
            $pdf->SetFont('verdana','',7);
            $pdf->Cell(20,10,number_format($mComFp[$y][18],0,',','.'),0,0,'R');
            $posy +=3;
        	}
         }

        $posy += 4;
        $pdf->setXY(60,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(20,10,"Servicios ALPOPULAR S.A.",0,0,'L');

        $posy += 4;

        $nTotIP = 0;
        for($m=0;$m<count($mCodDat);$m++){
          if($mCodDat[$m]['comctocx'] == "IP"){
            $cFacIP = $mCodDat[$i]['comidxxx']."-".$mCodDat[$i]['comcodxx']."-".$mCodDat[$i]['comcscxx']."-".$mCodDat[$i]['comcsc2x'];
            if($cFacIP == $mFacturas[$h]){
              if($mCodDat[$m]['comtraxx'] == $mMatrizDo[$k][0]){
                for($n=0;$n<count($mMatrizIP);$n++){
                  if($mMatrizIP[$n] == $mCodDat[$m]['ctoidxxx']){
                    $pdf->setXY(20,$posy);
                    $pdf->SetFont('verdana','',7);
                    $pdf->Cell(15,10,$mMatrizIP[$n],0,0,'L');
                    $pdf->setXY(60,$posy);
                    $pdf->SetFont('verdana','',7);
                    $mComObs_IP = f_Explode_Array($mCodDat[$m]['comobsxx'],"|","~");
            				$nIP = 0;
            				if(count($mComObs_IP) > 0){
    	      				 for($i=0;$i<count($mComObs_IP);$i++){
    			     			   if($mComObs_IP[$i][2] != ""){
    			               $nComObs_IP = stripos($mComObs_IP[$i][2], "[");
    			               if($nComObs_IP > 0){
    			                 $pdf->Cell(30,10,substr($mComObs_IP[$i][2],0,$nComObs_IP),0,0,'L');
    			               } else {
    			                   $pdf->Cell(30,10,substr($mComObs_IP[$i][2],0,40),0,0,'L');
    			                 }
    			                 $nIP++;
    			     				 }
    	      				 }
            			  }
                    if($nIP == 0){
    					      	$nComObs_IP = stripos($mCodDat[$m]['comobsxx'], "[");
    						      if($nComObs_IP > 0){
    						        $pdf->Cell(20,10,substr($mCodDat[$m]['comobsxx'],0,$nComObs_IP),0,0,'L');
    						      }else{
    						        $pdf->Cell(20,10,substr($mCodDat[$m]['comobsxx'],0,40),0,0,'L');
    						      }
    					      }
                    $pdf->setXY(170,$posy);
                    $pdf->SetFont('verdana','',7);
                    $pdf->Cell(20,10,number_format($mCodDat[$m]['comvlrxx'],0,',','.'),0,0,'R');
                    $posy +=3;
                    $nTotIP += $mCodDat[$m]['comvlrxx'];
                  }
                }
               }
              }
             }
            }

        	$mComFp = f_Explode_Array($mMatrizDo['comfpxxx'],"|","~");
         	$ToComFpI = 0;
        	$qCtoDatI  = "SELECT * ";
        	$qCtoDatI .= "FROM $cAlfa.fpar0119 ";
        	$qCtoDatI .= "WHERE ";
        	$qCtoDatI .= "$cAlfa.fpar0119.ctoclaxf = \"IVAIP\" AND ";
        	$qCtoDatI .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
        	$xCtoDatI  = f_MySql("SELECT","",$qCtoDatI,$xConexion01,"");
        	//f_Mensaje(__FILE__,__LINE__,$qCtoDatI);
        	$nFilCtoI  = mysql_num_rows($xCtoDatI);
    	  	if ($nFilCtoI > 0) {
    	    	$vCtoDatI = mysql_fetch_array($xCtoDatI);
    	  	}
         	for($y=0;$y<count($mComFp);$y++){
         	$mMDo = explode("-",$mMatrizDo[$k][0]);
        	$mCompDo = $mMDo[1]."-".$mMDo[2];
        	if($mCompDo == $mComFp[$y][2]."-".$mComFp[$y][3]){
        		$ToComFpI += $mComFp[$y][20];
        		$pdf->setXY(20,$posy);
            $pdf->SetFont('verdana','',7);
            $pdf->Cell(15,10,$vCtoDatI['ctoidxxx'],0,0,'L');
            $pdf->setXY(60,$posy);
            $pdf->SetFont('verdana','',7);
            $pdf->Cell(30,10,substr($vCtoDatI['ctodesxf'],0,40),0,0,'L');
            $pdf->setXY(170,$posy);
            $pdf->SetFont('verdana','',7);
            $pdf->Cell(20,10,number_format($mComFp[$y][20],0,',','.'),0,0,'R');
            $posy +=3;
        	}
         }

        $posy += 4;

        $pdf->setXY(60,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(20,10,"Anticipos",0,0,'L');


        /***** Traigo Anticipo de Do *****/
        $mAntDo = explode("-",$mMatrizDo[$k][0]);
        $qAntDat  = "SELECT * ";
        $qAntDat .= "FROM $cAlfa.fcod$cNewYear ";
        $qAntDat .= "WHERE ";
        $qAntDat .= "$cAlfa.fcod$cNewYear.comidxxx = \"R\" AND ";
        $qAntDat .= "$cAlfa.fcod$cNewYear.comcsccx = \"{$mAntDo[1]}\" AND ";
        $qAntDat .= "$cAlfa.fcod$cNewYear.comseqcx = \"{$mAntDo[2]}\" AND ";
        $qAntDat .= "$cAlfa.fcod$cNewYear.commovxx = \"C\" ";
        $xAntDat  = f_MySql("SELECT","",$qAntDat,$xConexion01,"");
      	$nFilAnt  = mysql_num_rows($xAntDat);
      	if ($nFilAnt > 0) {
      	  $vAntDat  = mysql_fetch_array($xAntDat);
      	}

      	//f_Mensaje(__FILE__,__LINE__,$qAntDat);

      	$qStrDat  = "SELECT * ";
       	$qStrDat .= "FROM $cAlfa.sys00002 ";
       	$qStrDat .= "WHERE ";
       	$qStrDat .= "$cAlfa.sys00002.stridxxx = \"alpopular_cuenta_anticipos_factura\" AND ";
       	$qStrDat .= "$cAlfa.sys00002.regestxx = \"ACTIVO\" ";
        $xStrDat  = f_MySql("SELECT","",$qStrDat,$xConexion01,"");
    	  $nFilStr  = mysql_num_rows($xStrDat);
    		if ($nFilStr > 0) {
    		  $vStrDat  = mysql_fetch_array($xStrDat);
    		}

        $posy += 4;
      	$pdf->setXY(20,$posy);
        $pdf->SetFont('verdana','',7);
        $cAnticipo = explode(" ",$vStrDat['strvlrxx']);
        $pdf->Cell(15,10,$cAnticipo[0],0,0,'L');
        $pdf->setXY(60,$posy);
        $pdf->SetFont('verdana','',7);
        $pdf->Cell(30,10,trim(substr($vStrDat['strvlrxx'],strlen($cAnticipo[0]),strlen($vStrDat['strvlrxx']))),0,0,'L');
        $pdf->setXY(170,$posy);
        $pdf->SetFont('verdana','',7);
        if($vAntDat['comvlrxx'] != 0){
        	$pdf->Cell(20,10,number_format($vAntDat['comvlrxx'],0,',','.'),0,0,'R');
        }
        $posy +=3;
        $nTotAnt = $vAntDat['comvlrxx'];


        /***** Fin Consulta de Anticipo de Do*****/


        $posy += 4;
        $nTotDo = 0;
        $nTotDo = ($nTotPCC + $nTotPCC2 + $nTotIP + $ToComFp + $ToComFpI) - $nTotAnt;
        $pdf->setXY(60,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(20,10,"Total D.O. ".$mMatrizDo[$k][0],0,0,'L');
        $pdf->setXY(170,$posy);
        $pdf->SetFont('verdanab','',7);
        $pdf->Cell(20,10,number_format($nTotDo,0,',','.'),0,0,'R');
        $pdf->Line(60,$posy+7,190,$posy+7);




        if(($posyFin - $posy) <= 0){
           $pdf->AddPage();
           $posy = 15;
           $nPag++;
        }
       $nTotal += $nTotDo;
      }
     }

   /* for($i=0;$i<count($mCodDat);$i++){
    	if($mCodDat[$i]['comctocx'] == "PCC" && $mCodDat[$i]['comtraxx'] == ""){
    		$cConPcc  = $mCodDat[$i]['comobsxx'];
    		$nComVPcc = $mCodDat[$i]['comvlrxx'];
    	}
    }*/
    if($cConPcc != ""){
  	  $posy += 5;
  	  $pdf->setXY(60,$posy);
  	  $pdf->SetFont('verdanab','',7);
  	  $pdf->Cell(20,10,"TOTAL ".$cConPcc,0,0,'L');
  	  $pdf->setXY(170,$posy);
  	  $pdf->SetFont('verdanab','',7);
  	  $pdf->Cell(20,10,number_format($nComVPcc,0,',','.'),0,0,'R');
    }
    $posy += 5;
    $pdf->setXY(60,$posy);
    $pdf->SetFont('verdanab','',7);
    $pdf->Cell(20,10,"Total Cliente ".$cCliNom,0,0,'L');
    $pdf->setXY(170,$posy);
    $pdf->SetFont('verdanab','',7);
    $pdf->Cell(20,10,number_format($nTotal+$nComVPcc,0,',','.'),0,0,'R');
    $pdf->setXY(90,270);
    $pdf->SetFont('verdanab','',9);
    $pdf->Cell(20,10,"PAGINA ".$nPag,0,0,'C');


  $cFile = "../../../../../..".$cSystem_Root_Directory."/downloads/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	$pdf->Output($cFile);
	echo "<html><script>document.location='$cFile';</script></html>";
  //$pdf->Output();

?>

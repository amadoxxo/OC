<?php
  /**
	 * Imprime Comprobante Personalizado para BMA.
	 * --- Descripcion: Permite Imprimir Comprobante.
	 * @author Hair Zabala <hair.zabala@opentecnologia.com.co>
	 */
	 
	//ini_set('error_reporting', E_ERROR);
  //ini_set("display_errors","1");
	
  include("../../../../libs/php/utility.php");
  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

  $pdf = new FPDF('P','mm','Letter');
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->SetFont('verdana','',8);
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);
  
  /*** Cargo en una Matriz El/Los Comprobantes Seleccionados Para Imprimir ***/
  $mPrn = explode("|",$prints);

  /**
   * Matriz Auxiliar para las cuentas
   * @var array
   */
  $mPucIds = array();
  $qPucIds  = "SELECT *, CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
  $qPucIds .= "FROM $cAlfa.fpar0115 ";
  $xPucIds  = mysql_query($qPucIds,$xConexion01);
  // echo "<br>".$qPucIds."~".mysql_num_rows($xPucIds);
  while($xRCAP = mysql_fetch_array($xPucIds)) {
      $mPucIds["{$xRCAP['pucidxxx']}"] = $xRCAP;
  }
  
  for ($nn=0;$nn<count($mPrn);$nn++) {
    if (strlen($mPrn[$nn]) > 0) {
      $vComp = explode("~",$mPrn[$nn]);
  		$cComId   = $vComp[0];
  		$cComCod  = $vComp[1];
  		$cComCsc  = $vComp[2];
  		$cComCsc2 = $vComp[3];
  		$cComFec  = $vComp[4];
	    $cAno     = substr($cComFec,0,4);
      
      // $mPccIp =  array(); 
      // $nIva = 0; 
      // $nTotal = 0;

      /**
       * Variable para indicar el total de la Nota Credito. 
       * @var number
       */
      $nTotNce = 0;

      /**
       * Variable para almacenar los registros de detalle de la Nota Credito.
       * @var array
       */
      $mCodDat = array();
      
  		/*** CABECERA 1001 ***/
  		$qCocDat  = "SELECT ";
  		$qCocDat .= "$cAlfa.fcoc$cAno.*, ";
  		$qCocDat .= "IF($cAlfa.fpar0116.ccodesxx <> \"\",$cAlfa.fpar0116.ccodesxx,\"CENTRO DE COSTO SIN DESCRIPCION\") AS ccodesxx, ";
  		$qCocDat .= "IF($cAlfa.fpar0117.comdesxx <> \"\",$cAlfa.fpar0117.comdesxx,\"COMPROBANTE SIN DESCRIPCION\") AS comdesxx, ";
			$qCocDat .= "$cAlfa.fpar0117.comtcoxx  AS comtcoxx, ";
  		$qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX, ";
  		$qCocDat .= "IF($cAlfa.A.CLINOMXX <> \"\",$cAlfa.A.CLINOMXX,CONCAT($cAlfa.A.CLINOM1X,\" \",$cAlfa.A.CLINOM2X,\" \",$cAlfa.A.CLIAPE1X,\" \",$cAlfa.A.CLIAPE2X)) AS PRONOMXX, ";
  		$qCocDat .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
  		$qCocDat .= "FROM $cAlfa.fcoc$cAno ";
  		$qCocDat .= "LEFT JOIN $cAlfa.fpar0116 ON $cAlfa.fcoc$cAno.ccoidxxx = $cAlfa.fpar0116.ccoidxxx ";
  		$qCocDat .= "LEFT JOIN $cAlfa.fpar0117 ON $cAlfa.fcoc$cAno.comidxxx = $cAlfa.fpar0117.comidxxx AND $cAlfa.fcoc$cAno.comcodxx = $cAlfa.fpar0117.comcodxx ";
      $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
      $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.fcoc$cAno.terid2xx = $cAlfa.A.CLIIDXXX ";
      $qCocDat .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.fcoc$cAno.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
  		$qCocDat .= "WHERE $cAlfa.fcoc$cAno.comidxxx = \"$cComId\" AND ";
  		$qCocDat .= "$cAlfa.fcoc$cAno.comcodxx = \"$cComCod\" AND ";
  		$qCocDat .= "$cAlfa.fcoc$cAno.comcscxx = \"$cComCsc\" AND ";
  		$qCocDat .= "$cAlfa.fcoc$cAno.comcsc2x = \"$cComCsc2\" LIMIT 0,1";
  		//f_Mensaje(__FILE__,__LINE__,$qCocDat);

  		$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
  		$nFilCoc  = mysql_num_rows($xCocDat);
  		if ($nFilCoc > 0) {
  		  $vCocDat  = mysql_fetch_array($xCocDat);
  		}
  		
  		/*** Datos del Cliente ***/
		  $qCliente  = "SELECT ";
      $qCliente .= "$cAlfa.SIAI0150.*, ";
      $qCliente .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X,\" \",$cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX ";
      $qCliente .= "FROM $cAlfa.SIAI0150 ";
      $qCliente .= "WHERE ";
      $qCliente .= "$cAlfa.SIAI0150.CLIIDXXX = \"{$vCocDat['teridxxx']}\" LIMIT 0,1";
      $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCliente." ~ ".mysql_num_rows($xCliente));
      $vCliente  = mysql_fetch_array($xCliente);
      
      /*** Pais ***/
      $qPaises  = "SELECT PAIDESXX ";
      $qPaises .= "FROM $cAlfa.SIAI0052 ";
      $qPaises .= "WHERE ";
      $qPaises .= "PAIIDXXX = \"{$vCliente['PAIIDXXX']}\" LIMIT 0,1";
      $xPaises  = f_MySql("SELECT","",$qPaises,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qPaises." ~ ".mysql_num_rows($xPaises));
      $vPaises  = mysql_fetch_array($xPaises);
      
      /*** Ciudad ***/
      $qCiudad  = "SELECT CIUDESXX ";
      $qCiudad .= "FROM $cAlfa.SIAI0055 ";
      $qCiudad .= "WHERE ";
      $qCiudad .= "PAIIDXXX = \"{$vCliente['PAIIDXXX']}\" AND ";
      $qCiudad .= "DEPIDXXX = \"{$vCliente['DEPIDXXX']}\" AND ";
      $qCiudad .= "CIUIDXXX = \"{$vCliente['CIUIDXXX']}\" LIMIT 0,1";
      $xCiudad  = f_MySql("SELECT","",$qCiudad,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCiudad." ~ ".mysql_num_rows($xCiudad));
      $vCiudad  = mysql_fetch_array($xCiudad);

      $vCtoPcc = array(); 
 
      /*** Buscando conceptos de causaciones automaticas ***/
      $qCAyP121 = "SELECT DISTINCT $cAlfa.fpar0121.pucidxxx, $cAlfa.fpar0121.ctoidxxx FROM $cAlfa.fpar0121 WHERE $cAlfa.fpar0121.regestxx = \"ACTIVO\"";
      $xCAyP121 = f_MySql("SELECT","",$qCAyP121,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCAyP121."~".mysql_num_rows($xCAyP121));
      while($xRCP121 = mysql_fetch_array($xCAyP121)) {
        $vCtoPcc[count($vCtoPcc)] = "{$xRCP121['pucidxxx']}~{$xRCP121['ctoidxxx']}";
      }
      
      /*** Buscando conceptos ***/
      $qCtoAntyPCC = "SELECT DISTINCT $cAlfa.fpar0119.ctoantxx, $cAlfa.fpar0119.ctopccxx, $cAlfa.fpar0119.pucidxxx, $cAlfa.fpar0119.ctoidxxx FROM $cAlfa.fpar0119 WHERE ($cAlfa.fpar0119.ctoantxx = \"SI\" OR $cAlfa.fpar0119.ctopccxx = \"SI\") AND $cAlfa.fpar0119.regestxx = \"ACTIVO\"";
      $xCtoAntyPCC = f_MySql("SELECT","",$qCtoAntyPCC,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCtoAntyPCC."~".mysql_num_rows($xCtoAntyPCC));
      while($xRCAP = mysql_fetch_array($xCtoAntyPCC)) {
        if ($xRCAP['ctopccxx'] == "SI") {
          $vCtoPcc[count($vCtoPcc)] = "{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}";
        }
      }

  		/*** DETALLE 1002 ***/
  		$qCodDat  = "SELECT DISTINCT ";
      $qCodDat .= "$cAlfa.fcod$cAno.*, ";
      $qCodDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
      $qCodDat .= "FROM $cAlfa.fcod$cAno ";
      $qCodDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
  		$qCodDat .= "WHERE $cAlfa.fcod$cAno.comidxxx = \"$cComId\" AND ";
  		$qCodDat .= "$cAlfa.fcod$cAno.comcodxx = \"$cComCod\" AND ";
  		$qCodDat .= "$cAlfa.fcod$cAno.comcscxx = \"$cComCsc\" AND ";
   		$qCodDat .= "$cAlfa.fcod$cAno.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cAno.comseqxx) ASC";
  		$xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
  		$nFilCod  = mysql_num_rows($xCodDat);
  		if ($nFilCod > 0) {
  		  
    		/*** Cargo la Matriz con los ROWS del Cursor ***/
        while ($xRCD = mysql_fetch_array($xCodDat)) {
  			  
  			  $vCtoCon = array(); //Inicializando vector con informacion del concepto y la cuenta
					/*** Busco la descripcion del concepto ***/
					$qCtoCon  = "SELECT $cAlfa.fpar0119.*,$cAlfa.fpar0115.* ";
					$qCtoCon .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
					$qCtoCon .= "WHERE ";
					$qCtoCon .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
					$qCtoCon .= "$cAlfa.fpar0119.ctoidxxx = \"{$xRCD['ctoidxxx']}\" AND ";
					$qCtoCon .= "$cAlfa.fpar0119.pucidxxx = \"{$xRCD['pucidxxx']}\" LIMIT 0,1";
					$xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
					//f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
					if (mysql_num_rows($xCtoCon) > 0) {
						$vCtoCon = mysql_fetch_array($xCtoCon);
					} else {
						/*** Busco en la parametrica de Conceptos Contables Causaciones Automaticas ***/
						$qCtoCon  = "SELECT $cAlfa.fpar0121.*,$cAlfa.fpar0115.* ";
						$qCtoCon .= "FROM $cAlfa.fpar0121,$cAlfa.fpar0115 ";
						$qCtoCon .= "WHERE ";
						$qCtoCon .= "$cAlfa.fpar0121.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
						$qCtoCon .= "$cAlfa.fpar0121.ctoidxxx = \"{$xRCD['ctoidxxx']}\" AND ";
						$qCtoCon .= "$cAlfa.fpar0121.pucidxxx = \"{$xRCD['pucidxxx']}\" LIMIT 0,1";
						$xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
						//f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
						if (mysql_num_rows($xCtoCon) > 0) {
							$vCtoCon = mysql_fetch_array($xCtoCon);
						} else {
							/*** Busco por la cuenta, si es una cuenta de ingresos busco la descripcion del concepto de cobro ***/
							if (substr($xRCD['pucidxxx'],0,1) == "4") {
								$qCtoCon  = "SELECT $cAlfa.fpar0129.*,$cAlfa.fpar0115.* ";
								$qCtoCon .= "FROM $cAlfa.fpar0129,$cAlfa.fpar0115 ";
								$qCtoCon .= "WHERE ";
								$qCtoCon .= "$cAlfa.fpar0129.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
								$qCtoCon .= "$cAlfa.fpar0129.ctoidxxx = \"{$xRCD['ctoidxxx']}\" AND ";
								$qCtoCon .= "$cAlfa.fpar0129.pucidxxx = \"{$xRCD['pucidxxx']}\" LIMIT 0,1";
								$xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
								//f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
								if (mysql_num_rows($xCtoCon) > 0) {
									$vCtoCon = mysql_fetch_array($xCtoCon);
									$vCtoCon['ctodesxx'] = $vCtoCon['serdesxx'];
									$xRCD['ctonitxx'] = "CLIENTE";
								}
							} else {
								if ($xRCD['ctoidxxx'] == $xRCD['pucidxxx']) {
									/*** Busco la descripcion de la cuenta contable, para los impuestos ***/
									$qCtoCon  = "SELECT $cAlfa.fpar0115.* ";
									$qCtoCon .= "FROM $cAlfa.fpar0115 ";
									$qCtoCon .= "WHERE ";
									$qCtoCon .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$xRCD['pucidxxx']}\" LIMIT 0,1";
									$xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
									//f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
									if (mysql_num_rows($xCtoCon) > 0) {
										$vCtoCon = mysql_fetch_array($xCtoCon);
										$vCtoCon['ctodesxx'] = $vCtoCon['pucdesxx'];
										$xRCD['ctonitxx'] = "CLIENTE";
											
										if ($vCtoCon['pucretxx'] > 0) { //Si es una retencion aplica calculo automatico de base
											$xRCD['ctovlr01'] = "SI";
										}
									}
								}
							}
						}
					}
					
					$cComAux = ($vCtoCon['ctodesxx'] == "" && $xRCD['comidc2x'] == "") ? "F" : $xRCD['comidc2x'];
					$cCtoDesAux = ($vCtoCon['ctodesx'.strtolower($cComAux)] <> "") ? (($vCtoCon['ctodesx'.strtolower($cComAux)] <> "") ? $vCtoCon['ctodesx'.strtolower($cComAux)] : $vCtoCon['ctodesxx']) : (($vCtoCon['ctodesx'.strtolower($xRCD['comidxxx'])] <> "") ? $vCtoCon['ctodesx'.strtolower($xRCD['comidxxx'])] : $vCtoCon['ctodesxx']);
          
          if (substr_count($xRCD['ctodesxx'],"^") > 0){
            $vCtoDesAux = explode("^",$xRCD['ctodesxx']);
            if(trim($vCtoDesAux[0]) != ""){
              $xRCD['ctodesxx'] = trim($vCtoDesAux[0]);
            }
          }

          if (substr_count($xRCD['ctodesxx'],"~") > 0){
            $vCtoDesAux = explode("~",$xRCD['ctodesxx']);
            if(trim($vCtoDesAux[2]) != ""){
              $vAux = explode("[",trim($vCtoDesAux[2]));
              $xRCD['ctodesxx'] = $vAux[0];
            }
          }
          
          $xRCD['ctodesxx'] = (trim($xRCD['ctodesxx']) != "") ? $xRCD['ctodesxx'] : $cCtoDesAux;
					
				  /*if(in_array("{$xRCD['pucidxxx']}~{$xRCD['ctoidxxx']}", $vCtoPcc) == true){
            $mPccIp[count($mPccIp)] = $xRCD;
            $nTotal += $xRCD['comvlrxx'];
          }else if(substr($xRCD['pucidxxx'], 0,1) == "4"){
            $mPccIp[count($mPccIp)] = $xRCD;
            $nTotal += $xRCD['comvlrxx'];
          }else if(substr($xRCD['pucidxxx'], 0,4) == "2408"){
            $nIva += $xRCD['comvlrxx'];
          }*/

          /**
           * Condicion para obtener El codigo de la cuenta por pagar
           */
          if( $mPucIds["{$xRCD['pucidxxx']}"]['pucdetxx'] == "P" || $mPucIds["{$xRCD['pucidxxx']}"]['pucdetxx'] == "C" ){
            if($xRCD['commovxx'] == "D"){
              $nTotNce += $xRCD['comvlrxx'];
            }else{
              $nTotNce -= $xRCD['comvlrxx'];
            }
          }else{
            $mCodDat[count($mCodDat)] = $xRCD;
          }

  			}
  			// Fin de Cargo la Matriz con los ROWS del Cursor
  		}

  		if ($nFilCoc > 0 && $nFilCod > 0) {
  		  
				$j=0; // lineas del detalle permitido para cada comprobante
  			for ($k=0;$k<count($mCodDat);$k++) {
					$j++;
					if ($j == 1 || (($j % 30) == 0))	{

						/*** Nueva Pagina ***/
						$pdf->AddPage();
            
            $nPosX = 13;
            $nPosY = 17;
            
			      /*** Impresion de Logos Agencias de Aduanas Financiero Contable ***/
            switch($cAlfa){
							// case "DEADUANAMO":
              case "TRLXXXXX": 
              case "DETRLXXXXX": 
              case "TETRLXXXXX":
                $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma.jpg',$nPosX+135,$nPosY,55,19);
              break;                
							default:
							break;
						}
            
            /*** Nota de Credito. ***/
            $pdf->setXY($nPosX,$nPosY);
            $pdf->SetFont('verdanab','',12);
			      $pdf->Cell(46,5,"NOTA DE CREDITO",0,0,'C');
            
            $nPosY += 5;
            $pdf->Rect($nPosX,$nPosY,50,5);
            $pdf->SetFont('verdanab','',10);
            $pdf->setXY($nPosX,$nPosY);
            $pdf->Cell(50,5,substr($vCocDat['comcsc3x'],3,strlen($vCocDat['comcsc3x'])),0,0,'C'); // consecutivo 3
            
            /*** Rectangulo fecha del comprobante ***/
            $nPosY += 8;
			      $pdf->Rect($nPosX,$nPosY,50,9);
            $pdf->Line($nPosX+15,$nPosY,$nPosX+15,$nPosY+9);
            $pdf->Line($nPosX+15,$nPosY+4,$nPosX+50,$nPosY+4);
            $pdf->Line($nPosX+25,$nPosY+4,$nPosX+25,$nPosY+9);
            $pdf->Line($nPosX+35,$nPosY+4,$nPosX+35,$nPosY+9);
            
            // Fecha del Comprobante
            $pdf->setXY($nPosX,$nPosY);
            $pdf->SetFont('verdanab','',8);
            $pdf->Cell(15,9,"FECHA:",0,0,'C');
            
            $pdf->setXY($nPosX+15,$nPosY);
            $pdf->Cell(10,5,"DIA",0,0,'C');
            $pdf->Cell(10,5,"MES",0,0,'C');
            $pdf->Cell(15,5,utf8_decode('AÑO'),0,0,'C');
            
            $pdf->setXY($nPosX+15,$nPosY+4);
            $pdf->SetFont('verdana','',8);
            $pdf->Cell(10,5,substr($vCocDat['comfecxx'], 8,2),0,0,'C');
            $pdf->Cell(10,5,substr($vCocDat['comfecxx'], 5,2),0,0,'C');
            $pdf->Cell(15,5,substr($vCocDat['comfecxx'], 0,4),0,0,'C');
            
            /*** Rectangulo Cabecera del documento ***/
            $nPosY = $pdf->getY()+8;
            
            $pdf->Rect($nPosX,$nPosY,190,38);
            $pdf->setXY($nPosX,$nPosY+2);
            $pdf->SetFont('verdanab','',8);
            $pdf->Cell(17,5,utf8_decode(Señores).":",0,0,'L');
            $pdf->SetFont('verdana','',8); 
            $pdf->Cell(170,5,$vCliente['CLINOMXX'],0,0,'L');                              //Nombre.
            
            $pdf->Ln(5);
            $pdf->setX($nPosX+17);
            $pdf->Cell(170,5,$vCliente['CLIDIRXX'],0,0,'L');                              //Dirección
            $pdf->Ln(5);
            $pdf->setX($nPosX+17);
            $pdf->Cell(170,5,$vCiudad['CIUDESXX']." (".$vPaises['PAIDESXX'].")",0,0,'L'); //Ciudad
            
            $pdf->Ln(5);
            $pdf->setX($nPosX);
            $pdf->SetFont('verdanab','',8);
            $pdf->Cell(10,5,"Tel: ",0,0,'L'); 
            $pdf->SetFont('verdana','',8);        
            $pdf->Cell(40,5,$vCliente['CLITELXX'],0,0,'L');                               //Nro de telefonos.
            $pdf->SetFont('verdanab','',8);
            $pdf->Cell(10,5,"Fax: ",0,0,'L'); 
            $pdf->SetFont('verdana','',8);        
            $pdf->Cell(127,5,$vCliente['CLIFAXXX'],0,0,'L');                              //Nro de Fax.
            
            $pdf->Ln(5);
            $pdf->setX($nPosX);
            $pdf->SetFont('verdanab','',8);
            $pdf->Cell(10,3,"Attn: ",0,0,'L'); 
            $pdf->SetFont('verdana','',8);       
            $pdf->Cell(117,3,"",0,0,'L');                                                 //Atentamente
            $pdf->Cell(60,3,number_format($vCliente['CLIIDXXX'],0,"",".")."-".f_Digito_Verificacion($vCliente['CLIIDXXX']),0,0,'L');                               // Nit.
            
            $pdf->Ln(5);
            $pdf->setX($nPosX);
            $pdf->MultiCell(180,5,$vCocDat['comobsxx'],0,"J");
            
            //Datos de detalle,
            $nPosY = $pdf->getY()+8;
            $pdf->setXY($nPosX,$nPosY);
            
            $pdf->SetFont('verdanab','',9);
            $pdf->Cell(124,5,"CONCEPTO",1,0,'C'); //titulo concepto
            $pdf->Cell(33,5,"U$",1,0,'C');        //titulo U$
            $pdf->Cell(33,5,"COL.$",1,0,'C');     //titulo Col.$
            
            $nPosY    = $pdf->getY();
            $nPosConY = $pdf->getY()+8;
            
            /*** Rectangulo detalle del comprobante***/
            $pdf->SetFillColor(228,228,228);
            $pdf->Rect($nPosX+157,$nPosY+138,33,8,"F");   //Cuadro Relleno gris Total Pesos
            $pdf->Rect($nPosX+124,$nPosY+146,33,8,"F");   //Cuadro Relleno gris Total US
            $pdf->SetFillColor(255,255,255);
            
            $pdf->Rect($nPosX,$nPosY,190,174);
            $pdf->Line($nPosX+124,$nPosY,$nPosX+124,$nPosY+154);
            $pdf->Line($nPosX+157,$nPosY,$nPosX+157,$nPosY+154);
            
            $pdf->Line($nPosX,$nPosY+130,$nPosX+124,$nPosY+130);
            $pdf->Line($nPosX,$nPosY+138,$nPosX+190,$nPosY+138);
            $pdf->Line($nPosX+94,$nPosY+146,$nPosX+190,$nPosY+146);
            $pdf->Line($nPosX+94,$nPosY+138,$nPosX+94,$nPosY+154);
            
            $pdf->Line($nPosX,$nPosY+154,$nPosX+190,$nPosY+154);
            $pdf->Line($nPosX+47,$nPosY+154,$nPosX+47,$nPosY+174);
            $pdf->Line($nPosX+94,$nPosY+154,$nPosX+94,$nPosY+174);
            $pdf->Line($nPosX+142,$nPosY+154,$nPosX+142,$nPosY+174);
            
            $nPosTotX = $nPosX;
            $nPosTotY = $nPosY;
            
            $pdf->setXY($nPosX,$nPosY+130);
            $pdf->SetFont('verdanab','',9);
            $pdf->Cell(37,8,"TASA DE CAMBIO:",0,0,'L');
            
            $pdf->setXY($nPosX,$nPosY+138);
            $pdf->Cell(30,8,"SON:",0,0,'L');
            
            $pdf->setXY($nPosX+94,$nPosY+138);
            $pdf->Cell(30,8,"TOTAL U$",0,0,'L');
            
            $pdf->setXY($nPosX+94,$nPosY+146);
            $pdf->Cell(30,8,"TOTAL PESOS",0,0,'L');
            
            $pdf->setXY($nPosX,$nPosY+154);
            $pdf->Cell(47,5," ELABORADA:",0,0,'L');   //Elaborada
            $pdf->Cell(47,5," REVISADA:",0,0,'L');    //Revisada 
            $pdf->Cell(48,5," AUTORIZADA:",0,0,'L');   //Autorizada
            $pdf->Cell(48,5," CONTABILIZADA:",0,0,'L');//Contabliizada
					}

          $pdf->setXY($nPosX,$nPosConY);
          $pdf->SetFont('verdana','',8);
          $pdf->Cell(124,3,substr($mCodDat[$k]['ctodesxx'],0,65),0,0,'L');       //Concepto.
          $pdf->Cell(33,3,"",0,0,'R');
          $cSigno = ($mCodDat[$k]['commovxx'] == "C") ? "-" : "" ; 
          $pdf->Cell(33,3,$cSigno.((strpos(($mCodDat[$k]['comvlrxx']+0),'.') > 0) ? number_format(($mCodDat[$k]['comvlrxx']+0),2,',','.') : number_format(($mCodDat[$k]['comvlrxx']+0),0,',','.')),0,0,'R');
          $nPosConY+=4;
				}

        $pdf->setXY($nPosTotX+37,$nPosTotY+130);
        $pdf->SetFont('verdana','',9);
        $pdf->Cell(37,8,number_format($vCocDat['tcatasax']+0,2,",","."),0,0,'L');
        
        $pdf->setXY($nPosTotX,$nPosTotY+144);
        $pdf->SetFont('verdana','',7);
        $pdf->MultiCell(94,4,trim(f_Cifra_Php(abs($nTotNce),'PESO')),0,"J");
            
        $pdf->setXY($nPosTotX+157,$nPosTotY+146);
        $pdf->SetFont('verdana','',9);
        $pdf->Cell(33,8,'$'.((abs($nTotNce+0) > 0 ) ? number_format(abs($nTotNce+0),2,',','.') : number_format(abs($nTotNce+0),0,',','.')),1,0,'R');

  		} else {
  		  $pdf->AddPage();
				$pdf->SetXY(40,40);
				$pdf->Cell(200,200,"recibo incompleto verifique (1) ");
  		}
    }
  }//final recorrido matriz documentos seleccionados para imprimir

  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	$pdf->Output($cFile);

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }
	echo "<html><script>document.location='$cFile';</script></html>";
?>

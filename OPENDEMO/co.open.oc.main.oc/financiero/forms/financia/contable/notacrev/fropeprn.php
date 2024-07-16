<?php
  /**
	 * Imprime Comprobante Nota Credito.
	 * --- Descripcion: Permite Imprimir Comprobante Nota Credito (OPENTECNOLOGIA).
	 * @author juan Jose Trujillo <juan.trujillo@open-eb.co>
	 */
	//ini_set('error_reporting', E_ERROR);
  //ini_set("display_errors","1");

	include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");
	
	//Generacion del codigo QR
	require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/phpqrcode/qrlib.php');

  /*** Incluir fuente y clase pdf segun base de datos ***/
	define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
	require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

  //Estilo de letra usado en el documento
  $cEstiloLetra = 'arial';

	function fnDatosCabecera($cPrn){
		global $vSysStr, $cAlfa, $xConexion01, $vCocDat;

		$mDatos = array();

  	$vComp = explode("~",$cPrn);
		$cComId   = $vComp[0];
		$cComCod  = $vComp[1];
		$cComCsc  = $vComp[2];
		$cComCsc2 = $vComp[3];
		$cComFec  = $vComp[4];
	  $cAno     = substr($cComFec,0,4);

		////// CABECERA 1001 /////
		$qCocDat  = "SELECT ";
		$qCocDat .= "$cAlfa.fcoc$cAno.*, ";
		$qCocDat .= "IF($cAlfa.fpar0116.ccodesxx <> \"\",$cAlfa.fpar0116.ccodesxx,\"CENTRO DE COSTO SIN DESCRIPCION\") AS ccodesxx, ";
		$qCocDat .= "IF($cAlfa.fpar0117.comdesxx <> \"\",$cAlfa.fpar0117.comdesxx,\"COMPROBANTE SIN DESCRIPCION\") AS comdesxx, ";
		$qCocDat .= "$cAlfa.fpar0117.comtcoxx  AS comtcoxx, ";
		$qCocDat .= "IF($cAlfa.A.CLINOMXX <> \"\",$cAlfa.A.CLINOMXX,CONCAT($cAlfa.A.CLINOM1X,\" \",$cAlfa.A.CLINOM2X,\" \",$cAlfa.A.CLIAPE1X,\" \",$cAlfa.A.CLIAPE2X)) AS PRONOMXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX, ";
		$qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX <> \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.PAIIDXXX <> \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX <> \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX <> \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.CLINRPXX <> \"\",$cAlfa.SIAI0150.CLINRPXX,\"\") AS CLINRPXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX <> \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
    $qCocDat .= "IF($cAlfa.SIAI0150.CLIDIR3X <> \"\",$cAlfa.SIAI0150.CLIDIR3X,\"SIN DIRECCION\") AS CLIDIR3X, ";
  	$qCocDat .= "IF($cAlfa.SIAI0150.CLIPLAXX <> \"\",$cAlfa.SIAI0150.CLIPLAXX,\"\") AS CLIPLAXX ";
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

		$xCocDat  = mysql_query($qCocDat,$xConexion01);
		$nFilCoc  = mysql_num_rows($xCocDat);
		if ($nFilCoc > 0) {
			$vCocDat  = mysql_fetch_array($xCocDat);
			
			##Traigo Pais del Cliente ##
			$qPaiCfa  = "SELECT PAIDESXX ";
			$qPaiCfa .= "FROM $cAlfa.SIAI0052 ";
			$qPaiCfa .= "WHERE ";
			$qPaiCfa .= "PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
			$qPaiCfa .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
			$xPaiCfa  = f_MySql("SELECT","",$qPaiCfa,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qPaiCfa."~".mysql_num_rows($xPaiCfa));
			if (mysql_num_rows($xPaiCfa) > 0) {
				$vPaiCfa = mysql_fetch_array($xPaiCfa);
			}
			##Fin Traigo Pais del Cliente ##
	    $vCocDat['PAIDESXX']  = $vPaiCfa['PAIDESXX'];

			##Traigo Departamento del Cliente ##
			$qDepCfa  = "SELECT DEPDESXX ";
			$qDepCfa .= "FROM $cAlfa.SIAI0054 ";
			$qDepCfa .= "WHERE ";
			$qDepCfa .= "PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
			$qDepCfa .= "DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
			$qDepCfa .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
			$xDepCfa  = f_MySql("SELECT","",$qDepCfa,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qDepCfa."~".mysql_num_rows($xDepCfa));
			if (mysql_num_rows($xDepCfa) > 0) {
				$vDepCfa = mysql_fetch_array($xDepCfa);
			}
			##Traigo Departamento del Cliente ##
	    $vCocDat['DEPDESXX']  = $vDepCfa['DEPDESXX'];

	    ##Traigo Ciudad del Cliente ##
	    $qCiuDat  = "SELECT * ";
	    $qCiuDat .= "FROM $cAlfa.SIAI0055 ";
	    $qCiuDat .= "WHERE ";
	    $qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCocDat['PAIIDXXX']}\" AND ";
	    $qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCocDat['DEPIDXXX']}\" AND ";
	    $qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCocDat['CIUIDXXX']}\" AND ";
	    $qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
	    $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
	    // f_Mensaje(__FILE__,__LINE__,$qCiuDat."~".mysql_num_rows($xCiuDat));
	    $nFilCiu  = mysql_num_rows($xCiuDat);
	    if ($nFilCiu > 0) {
	      $vCiuDat = mysql_fetch_array($xCiuDat);
	    }
	    ##Fin Traigo Ciudad del Cliente ##
	    $vCocDat['CIUDESXX']  = $vCiuDat['CIUDESXX'];

  		$qDocDat  = "SELECT fcod$cAno.sucidxxx, fcod$cAno.docidxxx, fcod$cAno.docsufxx ";
      $qDocDat .= "FROM $cAlfa.fcod$cAno ";
  		$qDocDat .= "WHERE ";
  		$qDocDat .= "$cAlfa.fcod$cAno.docidxxx <> \"\" AND ";
  		$qDocDat .= "$cAlfa.fcod$cAno.comidxxx = \"$cComId\" AND ";
  		$qDocDat .= "$cAlfa.fcod$cAno.comcodxx = \"$cComCod\" AND ";
  		$qDocDat .= "$cAlfa.fcod$cAno.comcscxx = \"$cComCsc\" AND ";
   		$qDocDat .= "$cAlfa.fcod$cAno.comcsc2x = \"$cComCsc2\" ";
	    $xDocDat  = f_MySql("SELECT","",$qDocDat,$xConexion01,"");
	    //echo $qDocDat;die();
	    // f_Mensaje(__FILE__,__LINE__,$qDocDat."~".mysql_num_rows($xDocDat));
	    $nDocDat  = mysql_num_rows($xDocDat);

	    if ($nDocDat > 0) {
	      $vRCD = mysql_fetch_array($xDocDat);
    		$cSucId = $vRCD['sucidxxx'];
    		$cDocId = $vRCD['docidxxx'];
    		$cDocSuf = $vRCD['docsufxx'];
				$vCocDat['docidxxx'] = $cDocId; // Guardo el DO
		    $vCocDat['sucidxxx'] = $cSucId; // Guardo el Sucursal
		    $vCocDat['docsufxx'] = $cDocSuf; // Guardo el Sufijo

		    ##Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##
		    $qDceDat  = "SELECT doctipxx ";
		    $qDceDat .= "FROM $cAlfa.sys00121 ";
		    $qDceDat .= "WHERE ";
		    $qDceDat .= "$cAlfa.sys00121.sucidxxx = \"$cSucId\" AND ";
		    $qDceDat .= "$cAlfa.sys00121.docidxxx = \"$cDocId\" AND ";
		    $qDceDat .= "$cAlfa.sys00121.docsufxx = \"$cDocSuf\" ";
		    $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
		    $nFilDce  = mysql_num_rows($xDceDat);
		    if ($nFilDce > 0) {
		      $vDceDat = mysql_fetch_array($xDceDat);
					$vCocDat['doctipxx'] = $vDceDat['doctipxx'];
		    }
		    ##Fin Busco Do en la sys00121 Tabla de Do's Financieros y traigo Tipo de Operacion ##

	      ##Traigo Datos de la SIAI0200 DATOS DEL DO ##
	      $qDoiDat  = "SELECT DOIPEDXX ";
	      $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
	      $qDoiDat .= "WHERE $cAlfa.SIAI0200.DOIIDXXX = \"$cDocId\" AND ";
	      $qDoiDat .= "$cAlfa.SIAI0200.DOISFIDX = \"$cDocSuf\" AND ";
	      $qDoiDat .= "$cAlfa.SIAI0200.ADMIDXXX = \"$cSucId\" ";
	      //f_Mensaje(__FILE__,__LINE__,$qDoiDat);
	      $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
	      $nFilDoi  = mysql_num_rows($xDoiDat);
	      if ($nFilDoi > 0) {
	        $vDoiDat  = mysql_fetch_array($xDoiDat);
	      	$vCocDat['DOIPEDXX'] = $vDoiDat['DOIPEDXX'];
	      }
	      ##Fin Traigo Datos de la SIAI0200 DATOS DEL DO ##
	    }

	  	$vCocDat['comcscxx'] = ($vCocDat['regestxx'] == "PROVISIONAL") ?  "XXXXX" : $vCocDat['comcscxx']; //Factura de venta

			$mDatos = $vCocDat;

			$comobs2x = explode('~', $vCocDat['comobs2x']);
			$cAnoB = $comobs2x[0];
			$cComIdB = $comobs2x[1];
			$cComCodB = $comobs2x[2];
			$cComCscB = $comobs2x[3];
			$cComCsc2B = $comobs2x[4];

			$qFacAnu = "SELECT resprexx, comcscxx, compcecu, comfecxx  ";
			$qFacAnu .= "FROM $cAlfa.fcoc$cAnoB ";
			$qFacAnu .= "WHERE ";
			$qFacAnu .= "comidxxx = \"$cComIdB\" AND ";
			$qFacAnu .= "comcodxx = \"$cComCodB\" AND ";
			$qFacAnu .= "comcscxx = \"$cComCscB\" AND ";
			$qFacAnu .= "comcsc2x = \"$cComCsc2B\" LIMIT 0,1";
			$xFacAnu = f_MySql("SELECT", "", $qFacAnu, $xConexion01, "");

			$vFacAnu = mysql_fetch_array($xFacAnu);

			$mDatos['comfac2x'] = $cComCscB;
			$mDatos['comfacfe'] = $vFacAnu['comfecxx'];
			$mDatos['comfaccu'] = ($comobs2x[6] == "22") ? $vFacAnu['resprexx'].$vFacAnu['comcscxx'] :  $vFacAnu['compcecu'];
		}
		return $mDatos;
	}

	
  class PDF extends FPDF {

		function Header() {
			global $cAlfa, $cPlesk_Skin_Directory, $cEstiloLetra, $mDatCab, $vSysStr, $vCccDat;
      /*** Impresion Datos Generales Nota ***/
			
			//Inicializo Posicion X,Y
			$posx = 11;
			$posy = 15;
			//Hoja Membrete
			$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/hoja_membrete_open.jpg', $posx-12, $posy-10, 212);
			//Logo Texto laterar
			$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/camaracomercio_openeb.jpg', $posx-9, $posy+43, 2);

			## Datos Fecha Factura ##
			$this->setXY($posx,$posy+40);
			$this->SetFont($cEstiloLetra,'',7.5);
			$this->Cell(30,5, utf8_decode("Fecha de la Nota Crèdito: ") . substr($mDatCab['comfecxx'], 8, 2) . "-" . substr($mDatCab['comfecxx'], 5, 2) . "-" . substr($mDatCab['comfecxx'], 0, 4) ,0,0,'L');
			$this->Ln(4.5);
			$this->setX($posx);
			$this->Cell(30,5, "Fecha de vencimiento: " . substr($mDatCab['comfecve'], 8, 2) . "-" . substr($mDatCab['comfecve'], 5, 2) . "-" . substr($mDatCab['comfecve'], 0, 4),0,0,'L');
			$this->Ln(4.5);
			$this->setX($posx);
			$this->Cell(30,5, "Forma de pago: A " . (isset($vCccDat['cccplaxx']) ? $vCccDat['cccplaxx']: 0)." ".($vCccDat['cccplaxx'] == 1 ? "DIA": "DIAS"),0,0,'L');
			$this->Ln(8);

			## Datos Adquiriente ##
			$this->setX($posx);
			$this->Cell(30,5, "Facturado a",0,0,'L');
			$this->Ln(5);
			//Cliente
			$this->setX($posx);
      $this->MultiCell(105,3.5,$mDatCab['PRONOMXX'],0,'L');
			$this->Ln(0.5);
			//Nit Cliente
			$this->setX($posx);
			$this->Cell(30,5, "N.I.T: " . $mDatCab['terid2xx']." - ".f_Digito_Verificacion($mDatCab['terid2xx']),0,0,'L');				
			$this->Ln(4.5);
			//Telefono Cliente
			$this->setX($posx);
			$this->Cell(30,5, "Tel: " . $mDatCab['CLITELXX'],0,0,'L');
			$this->Ln(4.5);
			//Direccion Cliente
			$this->setX($posx);
			$this->Cell(30,5, utf8_decode("Dir: " . $mDatCab['CLIDIRXX']),0,0,'L');
			$this->Ln(4.5);
			$this->setX($posx);
			$this->Cell(30,5, utf8_decode($mDatCab['CIUDESXX'] . " - " . $mDatCab['DEPDESXX']),0,0,'L');
			$this->Ln(4.5);
			$this->setX($posx);
			$this->Cell(30,5, "Pais: ".utf8_decode($mDatCab['PAIDESXX']),0,0,'L');
			$this->Ln(4.5);
			$nPosyFin = $this->gety();

			## Datos OFE ##
			$this->setXY($posx+165,$posy+40);
			$this->SetFont($cEstiloLetra,'B',7.5);
			$this->Cell(30,5,utf8_decode("NOTA CRÉDITO ELECTRÓNICA No. ").$vResDat['resprexx'] . " " . $mDatCab['comcscxx'],0,0,'R');
			$this->Ln(4.5);

			$this->SetFont($cEstiloLetra,'',7.5);
			$this->setX($posx+165);
			$this->Cell(30,5,utf8_decode("OPENTECNOLOGIA S.A"),0,0,'R');
			$this->Ln(4.5);
			$this->setX($posx+165);
			$this->Cell(30,5,utf8_decode("N.I.T Nº: ") . number_format($vSysStr['financiero_nit_agencia_aduanas'], 0, '', '.') . "-" . f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']),0,0,'R');
			$this->Ln(4.5);
			$this->setX($posx+165);
			$this->Cell(30,5,utf8_decode("Regimen Común de IVA"),0,0,'R');
			$this->Ln(4.5);
			$this->setX($posx+165);
			$this->Cell(30,5,utf8_decode("Actividad Económica No. 6201 ICA 9,66"),0,0,'R');
			$this->Ln(4.5);
			$this->setX($posx+165);
			$this->Cell(30,5,"Dir.: ".utf8_decode("Carrera 70C No. 49-68"),0,0,'R');
			$this->Ln(4.5);
			$this->setX($posx+165);
			$this->Cell(30,5,"Tel.: "."5800820",0,0,'R');
			$this->Ln(4.5);
			$this->setX($posx+165);
			$this->Cell(30,5,utf8_decode("BOGOTA - BOGOTA D.C"),0,0,'R');
			$this->Ln(4.5);

    	/** Cabecera de la tabla **/
      $nPosY = $nPosyFin > $this->GetY() ? $nPosyFin : $this->GetY() ;

			$this->SetFillColor(150);
			$this->SetTextColor(255);
      $this->setXY($posx,$nPosY+5);
      $this->SetFont($cEstiloLetra,'B',7);
		  $this->Cell(20,6,utf8_decode("CANTIDAD"),1,0,'C', true);
      $this->Cell(114,6,utf8_decode("DESCRIPCIÓN"),1,0,'C', true);
      $this->Cell(30,6,utf8_decode("VALOR UNITARIO"),1,0,'C', true);
      $this->Cell(30,6,utf8_decode("VALOR TOTAL"),1,0,'C', true);
			$this->SetTextColor(0);
			$this->Ln(6);
			$this->nPosY = $this->getY();

		}//Function Header

		function Footer() {
			global $mDatCab; global $cEstiloLetra; global $cPlesk_Skin_Directory; global $vSysStr;

			// echo "<pre>";
			// print_r($mDatCab);
			// die();

      $posx	= 11;
      $posy = 195;

			//Factura referencia
			$this->Rect($posx, $posy, 194, 5);
			$this->SetFillColor(150);
			$this->SetTextColor(255);
			$this->setXY($posx, $posy);
			$this->SetFont('arial','B',7);
			$this->Cell(194,6,"Documento referencia: ",1,0,'L', true);
			$this->SetTextColor(0);

			$this->setXY($posx, $posy + 6);
			$this->SetFont('arial','B',7);
			$this->Cell(20,5,"Factura: ", 0,0,'L');
			$this->SetFont('arial','',7);
			$this->Cell(80,5, $mDatCab['comfac2x'], 0,0,'L');
			$this->Ln(5);
			$this->setX($posx);
			$this->SetFont('arial','B',7);
			$this->Cell(20,5,"Fecha Factura: ", 0,0,'L');
			$this->SetFont('arial','',7);
			$this->Cell(80,5, $mDatCab['comfacfe'], 0,0,'L');
			$this->Ln(5);
			$this->setX($posx);
			$this->SetFont('arial','B',7);
			$this->Cell(20,5,"CUFE: ", 0,0,'L');
			$this->SetFont('arial','',7);
			$this->Cell(80,5, $mDatCab['comfaccu'], 0,0,'L'); 

			$this->Rect($posx, $posy + 6, 194, 15);

      $posy += 30;

			if ($mDatCab['compceqr'] != "") {
				$cFileQR = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/QR_".$_COOKIE['kUsrId']."_".date("YmdHis").".png";
				QRcode::png($mDatCab['compceqr'], $cFileQR, "H", 10, 1);
				$this->Image($cFileQR,$posx+160,$posy+18,30,30);
			}

			$this->setXY($posx+140,$posy-3);
			$this->SetFont('arial','B',7);
			$this->Cell(30,5,"CUDE: ",0,0,'L');
			$this->Ln(5);
			$this->setX($posx+140);
			$this->SetFont('arial','',6);
			$this->MultiCell(53, 3,$mDatCab['compcecu'],0,'L');

			$this->setXY($posx,$posy+10);
			$this->SetFont('arial','',7);
			$this->Cell(30,5, utf8_decode("Representación Impresa de la Nota Crédito Electrónica"),0,0,'L');
			$this->Ln(5);
			$this->setX($posx);
			$this->SetFont('arial','',6);
			$this->MultiCell(140, 3,$mDatCab['compcesv'],0,'L');

		}

		// rota la celda
		function RotatedText($x,$y,$txt,$angle){
			//Text rotated around its origin
			$this->Rotate($angle,$x,$y);
			$this->Text($x,$y,$txt);
			$this->Rotate(0);
		}

		// rota la celda
		var $angle=0;
		function Rotate($angle,$x=-1,$y=-1){
			if($x==-1)
				$x=$this->x;
			if($y==-1)
				$y=$this->y;
			if($this->angle!=0)
				$this->_out('Q');
			$this->angle=$angle;
			if($angle!=0) {
				$angle*=M_PI/170;
				$c=cos($angle);
				$s=sin($angle);
				$cx=$x*$this->k;
				$cy=($this->h-$y)*$this->k;
				$this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
			}
		}

		function Setwidths($w) {
			//Set the array of column widths
			$this->widths=$w;
		}

		function SetAligns($a){
			//Set the array of column alignments
			$this->aligns=$a;
		}

		function Row($data){
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data);$i++)
			    $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
			$h=4*$nb;
			//Issue a page break first if needed
			$this->CheckPageBreak($h);
			//Draw the cells of the row
			for($i=0;$i<count($data);$i++) {
				$w=$this->widths[$i];
				$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
				//Save the current position
				$x=$this->GetX();
				$y=$this->GetY();
				//Draw the border
				//$this->Rect($x,$y,$w,$h);
				//Print the text
				$this->MultiCell($w,4,$data[$i],0,$a);
				//Put the position to the right of the cell
				$this->SetXY($x+$w,$y);
			}
			//Go to the next line
			$this->Ln($h);
		}

		function CheckPageBreak($h){
			//If the height h would cause an overflow, add a new page immediately
			if($this->GetY()+$h>$this->PageBreakTrigger)
			$this->AddPage($this->CurOrientation);
		}

		function NbLines($w,$txt){
			//Computes the number of lines a MultiCell of width w will take
			$cw=&$this->CurrentFont['cw'];
			if($w==0)
			    $w=$this->w-$this->rMargin-$this->x;
			$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			$s=str_replace("\r",'',$txt);
			$nb=strlen($s);
			if($nb>0 and $s[$nb-1]=="\n")
			    $nb--;
			$sep=-1;
			$i=0;
			$j=0;
			$l=0;
			$nl=1;
			while($i<$nb){
			  $c=$s[$i];
			  if($c=="\n"){
			    $i++;
			    $sep=-1;
			    $j=$i;
			    $l=0;
			    $nl++;
			    continue;
			  }
			  if($c==' ')
			        $sep=$i;
			    $l+=$cw[$c];
			    if($l>$wmax){
			      if($sep==-1){
			        if($i==$j)
			            $i++;
			      }
			      else
			          $i=$sep+1;
			      $sep=-1;
			      $j=$i;
			      $l=0;
			      $nl++;
			    }
			    else
			        $i++;
			  }
			  return $nl;
		}

  }//class PDF extends FPDF {

  /**
   * Matriz Auxiliar para las cuentas
   * @var array
   */
  $mPucIds = array();
  $qPucIds  = "SELECT *, CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
  $qPucIds .= "FROM $cAlfa.fpar0115 ";
  $xPucIds  = mysql_query($qPucIds,$xConexion01);
  // echo "<br>".$qPucIds."~".mysql_num_rows($xPucIds);
  while($xRPI = mysql_fetch_array($xPucIds)) {
    $mPucIds["{$xRPI['pucidxxx']}"] = $xRPI;
  }

  /**
   * Matriz Auxiliar para los conceptos contables.
   * @var array
   */
  $mCtoDes = array();
  $qCtoPCC  = "SELECT * ";
  $qCtoPCC .= "FROM $cAlfa.fpar0119 ";
  $xCtoPCC  = mysql_query($qCtoPCC,$xConexion01);
  // echo "<br>".$qCtoPCC."~".mysql_num_rows($xCtoPCC);
  while($xRCC = mysql_fetch_array($xCtoPCC)) {
     $mCtoDes["{$xRCC['pucidxxx']}-{$xRCC['ctoidxxx']}"] = $xRCC;
  }

	$mPrn = explode("|",$prints);

  ## Fin Switch para incluir fuente y clase pdf segun base de datos ##
  $pdf = new PDF('P','mm','Letter');
  $pdf->AddFont($cEstiloLetra,'','arial.php');
  $pdf->SetFont('arial','',8);
  $pdf->SetMargins(0,0,0);
	$pdf->SetAutoPageBreak(0,20);
  $pdf->SetWidths(array(20,114,30,30));
	$pdf->SetAligns(array("C","L","R","R"));

	/*** Se imprime el detalle. ***/
	for ($i=0; $i < count($mPrn); $i++) {

    /*** Consulto data de cabecera.  ***/
		$mDatCab = fnDatosCabecera($mPrn[$i]);
		
		// Condicones comerciales del facturar a
		$qCccDat  = "SELECT cccplaxx ";
		$qCccDat .= "FROM $cAlfa.fpar0151 ";
		$qCccDat .= "WHERE ";
		$qCccDat .= "$cAlfa.fpar0151.cliidxxx = \"{$mDatCab['terid2xx']}\" AND ";
		$qCccDat .= "$cAlfa.fpar0151.regestxx = \"ACTIVO\" LIMIT 0,1";
		$xCccDat  = mysql_query($qCccDat, $xConexion01);
	  // echo "<br>".$qCccDat."~".mysql_num_rows($xCccDat);
		if (mysql_num_rows($xCccDat) > 0) {
			$vCccDat = mysql_fetch_array($xCccDat);
		}
	
		//Agregar pagina
		$pdf->AddPage();
		
		$nPyIni  = $pdf->nPosY; // Posicion Y inicial para imprimir el detalle
		$nPosY   = $nPyIni + 3;
		$nPosX   = 11;
		$pyy     = $nPosY;
		$nPosFin = 177;

		$nSubTotal = 0;
		$nTotalIva = 0;

  	$vComp = explode("~",$mPrn[$i]);
		$cComId   = $vComp[0];
		$cComCod  = $vComp[1];
		$cComCsc  = $vComp[2];
		$cComCsc2 = $vComp[3];
		$cComFec  = $vComp[4];
	  $cAno     = substr($cComFec,0,4);

		////// DETALLE 1002 /////
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
			// Cargo la Matriz con los ROWS del Cursor
			$iA=0;
			while ($xRCD = mysql_fetch_array($xCodDat)) {
			  $vCtoCon = array(); //Inicializando vector con informacion del concepto y la cuenta

				// Busco la descripcion del concepto
				$qCtoCon  = "SELECT $cAlfa.fpar0119.*,$cAlfa.fpar0115.* ";
				$qCtoCon .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
				$qCtoCon .= "WHERE ";
				$qCtoCon .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
				$qCtoCon .= "$cAlfa.fpar0119.ctoidxxx = \"{$xRCD['ctoidxxx']}\" AND ";
				$qCtoCon .= "$cAlfa.fpar0119.pucidxxx = \"{$xRCD['pucidxxx']}\" LIMIT 0,1";
				$xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
				if (mysql_num_rows($xCtoCon) > 0) {
					$vCtoCon = mysql_fetch_array($xCtoCon);
				} else {
					//Busco en la parametrica de Conceptos Contables Causaciones Automaticas
					$qCtoCon  = "SELECT $cAlfa.fpar0121.*,$cAlfa.fpar0115.* ";
					$qCtoCon .= "FROM $cAlfa.fpar0121,$cAlfa.fpar0115 ";
					$qCtoCon .= "WHERE ";
					$qCtoCon .= "$cAlfa.fpar0121.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
					$qCtoCon .= "$cAlfa.fpar0121.ctoidxxx = \"{$xRCD['ctoidxxx']}\" AND ";
					$qCtoCon .= "$cAlfa.fpar0121.pucidxxx = \"{$xRCD['pucidxxx']}\" LIMIT 0,1";
					$xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
					if (mysql_num_rows($xCtoCon) > 0) {
						$vCtoCon = mysql_fetch_array($xCtoCon);
					} else {
						//Busco por la cuenta, si es una cuenta de ingresos busco la descripcion del concepto de cobro
						if (substr($xRCD['pucidxxx'],0,1) == "4") {
							$qCtoCon  = "SELECT $cAlfa.fpar0129.*,$cAlfa.fpar0115.* ";
							$qCtoCon .= "FROM $cAlfa.fpar0129,$cAlfa.fpar0115 ";
							$qCtoCon .= "WHERE ";
							$qCtoCon .= "$cAlfa.fpar0129.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
							$qCtoCon .= "$cAlfa.fpar0129.ctoidxxx = \"{$xRCD['ctoidxxx']}\" AND ";
							$qCtoCon .= "$cAlfa.fpar0129.pucidxxx = \"{$xRCD['pucidxxx']}\" LIMIT 0,1";
							$xCtoCon  = f_MySql("SELECT","",$qCtoCon,$xConexion01,"");
							// f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
							if (mysql_num_rows($xCtoCon) > 0) {
								$vCtoCon = mysql_fetch_array($xCtoCon);
								$vCtoCon['ctodesxx'] = $vCtoCon['serdesxx'];
								$vCtoCon['seridxxx'] = $vCtoCon['seridxxx'];
								$xRCD['ctonitxx'] = "CLIENTE";
							}
						} else {
							if ($xRCD['ctoidxxx'] == $xRCD['pucidxxx']) {
								//Busco la descripcion de la cuenta contable, para los impuestos
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
				$xRCD['ctodesxx'] = (trim($xRCD['ctodesxx']) != "") ? $xRCD['ctodesxx'] : $cCtoDesAux;

				//Trayendo descripcion concepto, cantidad y unidad //Metodo del utiliqdo.php
				$vDatosIp = array();
				$vDatosIp = f_Cantidad_Ingreso_Propio($xRCD['ctodesxx'], '', $vCocDat['sucidxxx'], $vCocDat['docidxxx'], $vCocDat['docsufxx']);

				$nSwitch_Encontre_Concepto = 0;
				//Los IP se agrupan por Sevicio
				for($j=0;$j<count($mCodDat);$j++) {
					if ($mCodDat[$j]['ctoidxxx'] == $xRCD['ctoidxxx'] && $mCodDat[$j]['seridxxx'] == $xRCD['seridxxx']) {
						$nSwitch_Encontre_Concepto = 1;

						$mCodDat[$j]['comvlrxx'] += $xRCD['comvlrxx'];
						$mCodDat[$j]['comvlrme'] += $xRCD['comvlrme'];

						$mCodDat[$j]['compivax']  = $xRCD['compivax']; // Porcentaje IVA
						$mCodDat[$j]['comvlr01'] += $xRCD['comvlr01']; // Valor Iva
						//Cantidad de veces que se encuentra el servicio
						$mCodDat[$j]['agrupaip'] += 1;
						//Cantidad FE
						$mCodDat[$j]['canfexxx'] += 1;
					}
				}

				if ($nSwitch_Encontre_Concepto == 0) {
					$nInd_mConData = count($mCodDat);
					$mCodDat[$nInd_mConData] = $xRCD;
					$mCodDat[$nInd_mConData]['comobsxx'] = $vDatosIp[0];
					$mCodDat[$nInd_mConData]['canfexxx'] = 1;
					$mCodDat[$nInd_mConData]['unidadfe'] = $vDatosIp[2];
				}
			}
			// Fin de Cargo la Matriz con los ROWS del Cursor

      /*** Seteando anchos y alineacion para la impresión del Row. ***/
      $pdf->setXY($nPosX,$nPosY);
			// $mCodDat = array_merge($mCodDat,$mCodDat,$mCodDat,$mCodDat,$mCodDat,$mCodDat,$mCodDat); // ESTE ARRAY ES SOLO PARA PRUEBAS
			for ($k=0;$k<count($mCodDat);$k++) {

        if($mCodDat[$k]['comctocx'] != "IVAIP"){

					if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
						$pdf->Rect($nPosX, $nPyIni, 194, ($nPosFin-$nPyIni));
						$pdf->Line($nPosX + 20, $nPyIni, $nPosX + 20, $nPosFin);
						$pdf->Line($nPosX + 134, $nPyIni, $nPosX + 134, $nPosFin);
						$pdf->Line($nPosX + 164, $nPyIni, $nPosX + 164, $nPosFin);

            $pdf->AddPage();
						$nPosY = $pdf->nPosY + 2;
            $pyy   = $nPosY;
            $pdf->setXY($nPosX,$nPosY);
          }

          /*** Orden en la impresión de las columnas de la nota credito. ***/
          if( $mPucIds[$mCodDat[$k]['pucidxxx']]['pucterxx'] == "R" || $mPucIds[$mCodDat[$k]['pucidxxx']]['pucdetxx'] == "C" ||
              $mPucIds[$mCodDat[$k]['pucidxxx']]['pucdetxx'] == "P" || $mCtoDes["{$mCodDat[$k]['pucidxxx']}-{$mCodDat[$k]['ctoidxxx']}"]['ctoantxx'] == "SI" ||
              ($mCodDat[$k]['comvlr01'] == 0 && $mCodDat[$k]['comvlr02'] == 0) ){
            $mCodDat[$k]['comvlr01'] = $mCodDat[$k]['comvlrxx'];
            $mCodDat[$k]['comvlr02'] = 0;
          }

          if($mCodDat[$k]['comctocx'] == "IP"){
						if(trim($mCodDat[$k]['comfacxx']) == ""){
							$vComObs2 = explode("~",$vCocDat['comobs2x']);
							$mCodDat[$k]['comfacxx'] = $vComObs2[1]."-".$vComObs2[2]."-".$vComObs2[3];
            	$vComFac  = explode("-",$mCodDat[$k]['comfacxx']);
						}else{
            	$vComFac  = explode("-",$mCodDat[$k]['comfacxx']);
						}
						
            for($cAnoP = $cAno; $cAnoP >= $vSysStr['financiero_ano_instalacion_modulo']; $cAnoP--){
              $qComFac  = "SELECT ";
              $qComFac .= "comvlr01,";
              $qComFac .= "compivax ";
              $qComFac .= "FROM $cAlfa.fcod$cAnoP ";
              $qComFac .= "WHERE ";
              $qComFac .= "comidxxx = \"$vComFac[0]\" AND ";
              $qComFac .= "comcodxx = \"$vComFac[1]\" AND ";
              $qComFac .= "comcscxx = \"$vComFac[2]\" AND ";
              $qComFac .= "comcsc2x = \"$vComFac[2]\" AND ";
              $qComFac .= "pucidxxx = \"{$mCodDat[$k]['pucidxxx']}\" AND ";
              $qComFac .= "ctoidxxx = \"{$mCodDat[$k]['ctoidxxx']}\" AND ";
              $qComFac .= "teridxxx = \"{$mCodDat[$k]['teridxxx']}\" AND ";
              $qComFac .= "terid2xx = \"{$mCodDat[$k]['terid2xx']}\" AND ";
              $qComFac .= "sucidxxx = \"{$mCodDat[$k]['sucidxxx']}\" AND ";
              $qComFac .= "docidxxx = \"{$mCodDat[$k]['docidxxx']}\" AND ";
              $qComFac .= "docsufxx = \"{$mCodDat[$k]['docsufxx']}\" LIMIT 0,1 ";
              $xComFac  = f_MySql("SELECT","",$qComFac,$xConexion01,"");
              //f_Mensaje(__FILE__,__LINE__,$qComFac."~".mysql_num_rows($xComFac));
              if(mysql_num_rows($xComFac) > 0){
                $vComFac = mysql_fetch_array($xComFac);
                if(($vComFac['comvlr01']+0) > 0){
                  $mCodDat[$k]['comvlr01'] = $mCodDat[$k]['comvlrxx'];
                  $mCodDat[$k]['comvlr02'] = ($mCodDat[$k]['comvlrxx'] * ($vComFac['compivax']/100));
                  $mCodDat[$k]['comvlrxx'] = ($mCodDat[$k]['comvlrxx'] + $mCodDat[$k]['comvlr02']);
                }
                break;
              }
            }
					}

					if ($mCodDat[$k]['commovxx'] == 'D') {
						$nSubTotal += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
						$SumDebito += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : "0";
						$nTotalIva += ($mCodDat[$k]['comvlr02'] > 0) ? $mCodDat[$k]['comvlr02'] : "0";
					}

          /*** Detalle Nota Credito ***/
          $pdf->setX($nPosX);
					$pdf->Row(array(number_format((($mCodDat[$k]['unidadfe'] == "A9") ? 1 : $mCodDat[$k]['canfexxx']),0,'.',','),
													$mCodDat[$k]['comobsxx'],
													number_format((($mCodDat[$k]['unidadfe'] != "A9" && $mCodDat[$k]['canfexxx'] > 0) ? ($mCodDat[$k]['comvlrxx']/$mCodDat[$k]['canfexxx']) : $mCodDat[$k]['comvlrxx']),2,'.',','),
													number_format($mCodDat[$k]['comvlrxx'],2,'.',',')
               					));
          $pyy = $pdf->getY();
        }
			}

			$posyFin = $pyy;

			if($py > $nPosFin + 5){
				$pdf->Rect($nPosX, $nPyIni, 194, ($nPosFin-$nPyIni));
				$pdf->Line($nPosX + 20, $nPyIni, $nPosX + 20, $nPosFin);
				$pdf->Line($nPosX + 134, $nPyIni, $nPosX + 134, $nPosFin);
				$pdf->Line($nPosX + 164, $nPyIni, $nPosX + 164, $nPosFin);
				$pdf->AddPage(); 
				$pyy = $posy;
			}

			$pdf->Rect($nPosX, $nPyIni, 194, ($posyFin-$nPyIni));
			$pdf->Line($nPosX + 20, $nPyIni, $nPosX + 20, $posyFin);
			$pdf->Line($nPosX + 134, $nPyIni, $nPosX + 134, $posyFin);
			$pdf->Line($nPosX + 164, $nPyIni, $nPosX + 164, $posyFin);

			### Totaes
			$posy = $pdf->getY();
			$posyIni = $posy;

			$nTotPag   = $nSubTotal + $nTotalIva;

			$pdf->SetFont('arial', 'b', 8);
			$pdf->setXY($nPosX + 134, $posyIni + 1);
			$pdf->Cell(30, 5, "SUBTOTAL", 0, 0, 'R');
			$pdf->Cell(30, 5, number_format($nSubTotal, 2,',','.'), 0, 0, 'R');
			$pdf->Ln(6);
    
			$pdf->setX($nPosX + 134);
			$pdf->Cell(30, 5, "IVA", 0, 0, 'R');
			$pdf->Cell(30, 5, number_format($nTotalIva, 2,',','.'), 0, 0, 'R');
			$pdf->Ln(6);

			$pdf->setX($nPosX + 134);
			$pdf->Cell(30, 5, "TOTAL", 0, 0, 'R');
			$pdf->Cell(30, 5, number_format($nTotPag, 2,',','.'), 0, 0, 'R');
			$pdf->Ln(6);

			### Recuadro de los totales
			$pdf->Line($nPosX + 164, $posy, $nPosX + 164, $posy + 18);
			$pdf->Line($nPosX + 134, $posy + 6, $nPosX + 194, $posy + 6);
			$pdf->Line($nPosX + 134, $posy + 12, $nPosX + 194, $posy + 12);
			$pdf->Rect($nPosX + 134, $posy, 60, 18);

			### Observaciones
			$pdf->setXY($nPosX, $posyIni + 1);
			$pdf->SetFont('arial', 'b', 8);
			$pdf->Cell(30, 5, "OBSERVACIONES:", 0, 0, 'L');
			$pdf->Ln(5);
			$pdf->setX($nPosX);
			$pdf->SetFont('arial', '', 7);
			$pdf->MultiCell(128, 3.3, utf8_decode($mDatCab['comobsxx']), 0, 'L');

			### Inicializo posicion Y
			$posy = 225;
			$nTotPag1 = f_Cifra_Php($nTotPag,'PESO');
			$pdf->setXY($nPosX, $posy);
			$pdf->SetFont('arial', 'b', 8);
			$pdf->MultiCell(130, 4, "SON: " . $nTotPag1 , 0, 'L');
		}
	}

  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	$pdf->Output($cFile);
	//$pdf->Output();

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }

	echo "<html><script>document.location='$cFile';</script></html>";
?>

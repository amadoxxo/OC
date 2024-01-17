<?php
  /**
	 * Imprime Comprobante Nota Credito.
	 * --- Descripcion: Permite Imprimir Comprobante Nota Credito (GRUMALCO).
	 * @author Shamaru Primera <shamaru001@gmail.com>
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

	$nSwitch = 0;

	//Se recorren los documentos seleccionados para validar que todos sean de 2242 o VP
	$mPrn  = explode("|",$prints);
	$nVP   = 0;
	$n2242 = 0;
	for ($i=0; $i < count($mPrn); $i++) {
		$vComp = explode("~",$mPrn[$i]);
		$cComId   = $vComp[0];
		$cComCod  = $vComp[1];
		$cComCsc  = $vComp[2];
		$cComCsc2 = $vComp[3];
		$cComFec  = $vComp[4];
		$cAno     = substr($cComFec,0,4);
		
		$qCocDat  = "SELECT compcevx ";
		$qCocDat .= "FROM $cAlfa.fcoc$cAno ";
		$qCocDat .= "WHERE ";
		$qCocDat .= "comidxxx = \"$cComId\" AND ";
		$qCocDat .= "comcodxx = \"$cComCod\" AND ";
		$qCocDat .= "comcscxx = \"$cComCsc\" AND ";
		$qCocDat .= "comcsc2x = \"$cComCsc2\" LIMIT 0,1";
		$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
		$vCocDat = mysql_fetch_array($xCocDat);

		if ($vCocDat['compcevx'] == "VP") {
			$nVP++;
		} else {
			$n2242++;
		}
	}

	if ($nVP > 0 && $n2242 > 0) {
		$nSwitch = 1;
		f_Mensaje(__FILE__,__LINE__,"No Puede Seleccionar Documentos con Versiones de Facturacion Electronica DIAN Diferentes."); ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
        document.forms['frgrm'].submit();
      </script>
    <?php
	}

	if ($nSwitch == 0){

		//Nuevo formato validacion previa
		if ($nVP > 0) {
			//Estilo de letra usado en el documento
			$cEstiloLetra = 'arial';
		
			class PDF extends FPDF {
		
				public $headers = [
					['w' => 20, 'title' => 'COD. REF'],
					['w' => 66, 'title' => 'DETALLE'],
					['w' => 15, 'title' => 'CANT'],
					['w' => 24, 'title' => 'VALOR UNITARIO'],
					['w' => 11, 'title' => '% IVA'],
					['w' => 13, 'title' => 'IVA'],
					['w' => 17, 'title' => 'VALOR USD'],
					['w' => 39, 'title' => 'VALOR COP'],
				];
		
				function Header() {
					global $cAlfa;   global $cPlesk_Skin_Directory;    global $vAgeDat; global $vCocDat;
          global $vResDat; global $cDocTra; global $cTasCam; global $cBultos; global $cPesBru;
          global $cDocId;  global $vCiuDat; global $vDceDat; global $cOpera;  global $nValAdu;
          global $cCscFac; global $cPaiOri; global $cPedido; global $vSysStr; global $cEstiloLetra; 
          global $cAduana; global $_COOKIE; global $vPaiDat; global $cNomVen; global $cOrdCom; 
					global $cLugIngDes; global $vDatDep; global $vMedPag; global $cPlazo;  global $cFormaPago;
					global $cDocRef; global $dFecDocRef; global $cCufeDocRef;
					
					// Impresion Datos Generales
					$nPosX = 5;
					$nPosY = 10;

					$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',$nPosX,$nPosY,32,21);

					$this->SetFont('Arial', 'B', 10);
					$this->setXY($nPosX + 3, $nPosY);
					$this->Cell(200, 4, utf8_decode("AGENCIA DE ADUANAS MARIO LONDOÑO S.A NIVEL 1"), 0, 0, 'C');
					$this->Ln(5);
					$this->setX($nPosX + 3);
					$this->Cell(200, 4, utf8_decode("NIT. {$vSysStr['financiero_nit_agencia_aduanas']}-".f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas'])), 0, 0, 'C');
					$nPosY = $this->GetY() + 5;
					$this->setXY($nPosX + 32, $nPosY);
					$this->SetFont('Arial', 'B', 7);
					$this->Cell(50, 4, utf8_decode("REPRESENTACIÓN GRÁFICA DE NOTA CRÉDITO"), 0, 0, 'L');
					$this->Ln(4);
					$this->setX($nPosX + 32);
					$this->Cell(50, 4, "CUFE:", 0, 0, 'L');

					$this->Ln(4);
					$this->setX($nPosX + 32);
					$this->SetFont('Arial', '', 7);
					$this->MultiCell(65, 3, $vCocDat['compcecu'], 0, 'L');
					$nPosYfin = $this->GetY();

					$this->setXY($nPosX + 110, $nPosY);
					$this->SetFont('Arial', 'B', 7);
					$this->Cell(50, 4, "SEDE PPAL:", 0, 0, 'L');
					$this->setX($nPosX + 126);
					$this->SetFont('Arial', '', 7);
					$this->MultiCell(47, 4, utf8_decode("MEDELLÍN Cll 8B Nº 65-191 OF.511 Edificio Puerto Seco"), 0, 'L');

					$this->Ln(0.5);
					$this->setX($nPosX + 110);
					$this->SetFont('Arial', 'B', 7);
					$this->Cell(50, 4, "CIUDAD QUE GENERA EL INGRESO:", 0, 0, 'L');
					$this->setX($nPosX + 155);
					$this->SetFont('Arial', '', 7);
					$this->MultiCell(50, 4, utf8_decode($cLugIngDes), 0, 'L');
					
					//Codigo QR
					if ($vCocDat['compceqr'] != "") {
						$cFileQR = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/QR_".$_COOKIE['kUsrId']."_".date("YmdHis").".png";
						QRcode::png($vCocDat['compceqr'], $cFileQR, "H", 10, 1);
						$this->Image($cFileQR,$nPosX+173, $nPosY-15,25,25);
					}

					if($nPosYfin < $this->GetY()){
						$nPosYfin = $this->GetY() + 1;
					}

					$nPosY = $nPosYfin;
					$this->setXY($nPosX, $nPosY);
					$this->SetFillColor(230, 230, 230);
					$this->SetFont('Arial', 'B', 12);
					$this->Cell(205, 7, utf8_decode("NOTA CRÉDITO No. ".$cCscFac), 0, 0, 'C', TRUE);

					$nPosYfin = $this->GetY() + 7;
					if ($this->PageNo() == 1) {
						$nFontSizeHeaderSumarize = 7;
						$nFontSizeHeader = 8;

						// Columna 1
						$nPosY = $this->GetY() + 10;
						$nPosYIni = $nPosY;

						$this->setXY($nPosX, $nPosY);
						$this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
						$this->Cell(20, 4, utf8_decode("SEÑOR(ES):"), 0, 0, 'L');
						$this->SetFont('Arial', '', $nFontSizeHeader);
						$this->MultiCell(67, 3, utf8_decode($vCocDat['CLINOMXX']), 0, 'L');
						$this->Ln(1);

						$this->setX($nPosX);
						$this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
						$this->Cell(20, 4, "NIT/CC:", 0, 0, 'L');
						$this->SetFont('Arial', '', $nFontSizeHeader);
						$this->Cell(67, 4, $vCocDat['terid2xx']."-".f_Digito_Verificacion($vCocDat['terid2xx']), 0, 0, 'L');
						$this->Ln(4);

						$this->setX($nPosX);
						$this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
						$this->Cell(20, 4, utf8_decode("DIRECCIÓN:"), 0, 0, 'L');
						$this->SetFont('Arial', '', $nFontSizeHeader);
						$this->MultiCell(67, 3, utf8_decode($vCocDat['CLIDIR3X']), 0, 'L');
						$this->Ln(0.5);

						$this->setX($nPosX);
						$this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
						$this->Cell(38, 4, utf8_decode("CIUDAD Y DEPARTAMENTO:"), 0, 0, 'L');
						$this->SetFont('Arial', '', $nFontSizeHeader);
						$this->MultiCell(49, 4, utf8_decode($vCiuDat['CIUDESXX'] . " - " . $vDatDep['DEPDESXX']), 0, 'L');
						$this->Ln(0.5);
					
						$nPosYfin = $this->GetY();

						// Columna 2
						$offsetX = 88;
						$this->setXY($nPosX + $offsetX, $nPosY);
						$this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
						$this->Cell(20, 4, utf8_decode("TELÉFONO:"), 0, 0, 'L');
						$this->SetFont('Arial', '', $nFontSizeHeader);
						$this->Cell(56, 4, $vCocDat['CLITELXX'], 0, 0, 'L');
						$this->Ln(4);

						$this->setX($nPosX + $offsetX);
						$this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
						$this->Cell(45, 4, utf8_decode("FECHA Y HORA DE GENERACIÓN:"), 0, 0, 'L');
						$this->SetFont('Arial', '', $nFontSizeHeader);
						$this->Cell(31, 4, $vCocDat['comfecxx']." ".$vCocDat['reghcrex'], 0, 0, 'L');
						$this->Ln(4);

						$this->setX($nPosX + $offsetX);
						$this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
						$this->Cell(49, 4, utf8_decode("FECHA Y HORA DE VALIDACION DIAN:"), 0, 0, 'L');
						$this->SetFont('Arial', '', $nFontSizeHeader);
						$this->Cell(27, 4, substr($vCocDat['compcevd'],0,16), 0, 0, 'L');
						$this->Ln(4);

						$this->setX($nPosX + $offsetX);
						$this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
						$this->Cell(30, 4, "FECHA VENCIMIENTO:", 0, 0, 'L');
						$this->SetFont('Arial', '', $nFontSizeHeader);
						$this->Cell(46, 4, $vCocDat['comfecve'], 0, 0, 'L');
						$this->Ln(4);
						if ($nPosYfin < $this->GetY())
								$nPosYfin = $this->GetY();

						
						// Columna 3
						$offsetX = 165;

						$this->setXY($nPosX + $offsetX, $nPosY);
						$this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
						$this->Cell(8, 4, "DO:", 0, 0, 'L');
						$this->SetFont('Arial', '', $nFontSizeHeader);
						$this->Cell(32, 4, $cDocId, 0, 0, 'L');
						$this->Ln(4);

						$this->setX($nPosX + $offsetX);
						$this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
						$this->Cell(13, 4, "PEDIDO:", 0, 0, 'L');
						$this->SetFont('Arial', '', $nFontSizeHeader);
						$this->Cell(27, 4, utf8_decode($cPedido), 0, 0, 'L');
						$this->Ln(4.5);

						$this->setX($nPosX + $offsetX);
						$this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
						$this->Cell(8, 4, "OC:", 0, 0, 'L');
						$this->SetFont('Arial', '', $nFontSizeHeader);
						$this->Cell(32, 4, utf8_decode($cOrdCom), 0, 0, 'L');
						$this->Ln(4);

						$this->setX($nPosX + $offsetX);
						$this->SetFont('Arial', 'B', $nFontSizeHeaderSumarize);
						$this->Cell(13, 4, "PLAZO:", 0, 0, 'L');
						$this->SetFont('Arial', '', $nFontSizeHeader);
						$this->Cell(27, 4, utf8_decode($cPlazo), 0, 0, 'L');
						$this->Ln(4);
						if ($nPosYfin < $this->GetY())
							$nPosYfin = $this->GetY();
						
						//Documento referencia
						$this->Line(5, $nPosYfin, 210, $nPosYfin);
						$fontSizeHeaderReference = 7;
						$this->setXY($nPosX, $nPosYfin + 1);
						$this->SetFont('Arial', 'B', $fontSizeHeaderReference);
						$this->Cell(48, 4, utf8_decode("FACTURA ELECTRÓNICA DE VENTA: "), 0, 0, 'L');
						$this->SetFont('Arial', '', $fontSizeHeader);
						$this->Cell(142, 4, $cDocRef, 0, 'L');
						$this->Ln(4);

						$this->setX($nPosX);
						$this->SetFont('Arial', 'B', $fontSizeHeaderReference);
						$this->Cell(73, 4, utf8_decode("FECHA GENERACIÓN FACTURA ELECTRÓNICA DE VENTA: "), 0, 0, 'L');
						$this->SetFont('Arial', '', $fontSizeHeader);
						$this->Cell(117, 4, $dFecDocRef, 0, 'L');
						$this->Ln(4);

						$this->setX($nPosX);
						$this->SetFont('Arial', 'B', $fontSizeHeaderReference);
						$this->Cell(55, 4, utf8_decode("CUFE FACTURA ELECTRÓNICA DE VENTA:"), 0, 0, 'L');
						$this->SetFont('Arial', '', $fontSizeHeaderReference);
						$this->Cell(135, 4, $cCufeDocRef, 0, 'L');
						$this->Ln(4);

						$nPosYfin = $this->GetY();

						$this->Rect($nPosX, $nPosY - 1, 205, $nPosYfin - ($nPosYIni-2));
					}

					$nPosY = $nPosYfin+3;
					$this->setXY($nPosX, $nPosY);
					$this->SetFillColor(220, 220, 220);
					$this->SetFont('Arial', 'B', 7.5);
					foreach ($this->headers as $head)
							$this->Cell($head['w'], 5, $head['title'], 0, 0, 'C', TRUE);

					$this->Rect($nPosX, $nPosY, 205, 5);
					$this->posYIniLines = $nPosY;
					$this->setXY($nPosX,$nPosY);
		
				}//Function Header
		
				function Footer() {
		
					global $vCocDat; global $vUsrNom;

          //$nPosY = 217;
          $nPosX = 5;
          //Defino posicion inicial Y para pintar la firma
          $nPosY = 250;

          if ($vCocDat['compceqr'] != "" && $vCocDat['compcesv'] != "") {
              $this->setXY($nPosX, $nPosY + 1);
              $this->SetFont('Arial', '', 8);
              $this->Cell(130, 3, utf8_decode("Firma Electrónica:"), 0, 0, 'L');
              $this->Ln(4);
              $this->setX($nPosX);
              $this->SetFont('Arial', '', 6.5);
              $this->MultiCell(135, 3, $vCocDat['compcesv'], 0, 'J');
          }

          $this->setXY($nPosX + 137, $nPosY + 4);
          $this->SetFont('Arial', '', 8.5);
          $this->MultiCell(70, 3, utf8_decode($vUsrNom['USRNOMXX']), 0, 'C');

          $this->setXY($nPosX + 137, $nPosY + 13);
          $this->SetFont('Arial', 'B', 8);
          $this->MultiCell(70, 3, utf8_decode("ELABORÓ"), 0, 'C');

          $this->Rect($nPosX, $nPosY, 205, 17);
          $this->Line($nPosX + 136, $nPosY, $nPosX + 136, $nPosY + 17);
          $this->Line($nPosX + 136, $nPosY + 11, $nPosX + 205, $nPosY + 11);

          //Paginacion
          $this->setXY($nPosX, $nPosY + 17);
          $this->SetFont('Arial', 'B', 7);
          $this->Cell(205, 4, utf8_decode('Pág. ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
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
		
			$mPrn = explode("|",$prints);
		
			## Fin Switch para incluir fuente y clase pdf segun base de datos ##
			$pdf = new PDF('P','mm','Letter');
			$pdf->AddFont($cEstiloLetra,'','arial.php');
			$pdf->SetFont('arial','',8);
			$pdf->AliasNbPages();
			$pdf->SetMargins(0,0,0);
			$pdf->SetAutoPageBreak(0,20);
			$pdf->SetFillColor(209,209,209);

			//Buscando conceptos de anticipo
			$qCtoAnt  = "SELECT ctoidxxx, ctoantxx ";
			$qCtoAnt .= "FROM $cAlfa.fpar0119 ";
			$qCtoAnt .= "WHERE ";
			$qCtoAnt .= "ctoantxx = \"SI\" ";
			$xCtoAnt  = mysql_query($qCtoAnt, $xConexion01);
			//f_Mensaje(__FILE__,__LINE__,$qCtoAnt." ~ ".mysql_num_rows($xCtoAnt));
			$vCtoAnt = array();
			while ($xRC = mysql_fetch_assoc($xCtoAnt)) {
				$vCtoAnt["{$xRC['ctoidxxx']}"] = $xRC;
			}
		
			/*** Se imprime el detalle. ***/
			for ($i=0; $i < count($mPrn); $i++) {

				$mDatGmf   = array();
				$mCodDat   = array();
				$mValores  = array();
				$vTramites = array();

				$nIva      = 0;
				$nTotRfte  = 0;
				$nTotARfte = 0;
				$nTotCree  = 0;
				$nTotACree = 0;
				$nTotIva   = 0;
				$nTotIca   = 0;
				$nTotAIca  = 0;
				$nTotAnt   = 0;

				$cDocId  = ""; 
				$cDocSuc = ""; 
				$cDocSuf = "";

				$vComp = explode("~",$mPrn[$i]);
				$cComId   = $vComp[0];
				$cComCod  = $vComp[1];
				$cComCsc  = $vComp[2];
				$cComCsc2 = $vComp[3];
				$cComFec  = $vComp[4];
				$cAno     = substr($cComFec,0,4);

				/*** Consulto data de cabecera.  ***/
				////// CABECERA 1001 /////
				$qCocDat  = "SELECT ";
				$qCocDat .= "$cAlfa.fcoc$cAno.*, ";
				$qCocDat .= "IF($cAlfa.fpar0116.ccodesxx != \"\",$cAlfa.fpar0116.ccodesxx,\"CENTRO DE COSTO SIN DESCRIPCION\") AS ccodesxx, ";
				$qCocDat .= "IF($cAlfa.fpar0117.comdesxx != \"\",$cAlfa.fpar0117.comdesxx,\"COMPROBANTE SIN DESCRIPCION\") AS comdesxx, ";
				$qCocDat .= "$cAlfa.fpar0117.comtcoxx  AS comtcoxx, ";
				$qCocDat .= "IF($cAlfa.A.CLINOMXX != \"\",$cAlfa.A.CLINOMXX,CONCAT($cAlfa.A.CLINOM1X,\" \",$cAlfa.A.CLINOM2X,\" \",$cAlfa.A.CLIAPE1X,\" \",$cAlfa.A.CLIAPE2X)) AS PRONOMXX, ";
				$qCocDat .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX, ";
				$qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX, ";
				$qCocDat .= "IF($cAlfa.SIAI0150.CLITELXX != \"\",$cAlfa.SIAI0150.CLITELXX,\"SIN TELEFONO\") AS CLITELXX, ";
				$qCocDat .= "IF($cAlfa.SIAI0150.PAIIDXXX != \"\",$cAlfa.SIAI0150.PAIIDXXX,\"\") AS PAIIDXXX, ";
				$qCocDat .= "IF($cAlfa.SIAI0150.DEPIDXXX != \"\",$cAlfa.SIAI0150.DEPIDXXX,\"\") AS DEPIDXXX, ";
				$qCocDat .= "IF($cAlfa.SIAI0150.CIUIDXXX != \"\",$cAlfa.SIAI0150.CIUIDXXX,\"\") AS CIUIDXXX, ";
				$qCocDat .= "IF($cAlfa.SIAI0150.CLINRPXX != \"\",$cAlfa.SIAI0150.CLINRPXX,\"\") AS CLINRPXX, ";
				$qCocDat .= "IF($cAlfa.SIAI0150.CLIDIRXX != \"\",$cAlfa.SIAI0150.CLIDIRXX,\"SIN DIRECCION\") AS CLIDIRXX, ";
				$qCocDat .= "IF($cAlfa.SIAI0150.CLIDIR3X != \"\",$cAlfa.SIAI0150.CLIDIR3X,\"SIN DIRECCION\") AS CLIDIR3X, ";
				$qCocDat .= "IF($cAlfa.SIAI0150.CLIPLAXX != \"\",$cAlfa.SIAI0150.CLIPLAXX,\"\") AS CLIPLAXX ";
				$qCocDat .= "FROM $cAlfa.fcoc$cAno ";
				$qCocDat .= "LEFT JOIN $cAlfa.fpar0116 ON $cAlfa.fcoc$cAno.ccoidxxx = $cAlfa.fpar0116.ccoidxxx ";
				$qCocDat .= "LEFT JOIN $cAlfa.fpar0117 ON $cAlfa.fcoc$cAno.comidxxx = $cAlfa.fpar0117.comidxxx AND $cAlfa.fcoc$cAno.comcodxx = $cAlfa.fpar0117.comcodxx ";
				$qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
				$qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.fcoc$cAno.terid2xx = $cAlfa.A.CLIIDXXX ";
				$qCocDat .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.fcoc$cAno.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
				$qCocDat .= "WHERE ";
				$qCocDat .= "$cAlfa.fcoc$cAno.comidxxx = \"$cComId\"  AND ";
				$qCocDat .= "$cAlfa.fcoc$cAno.comcodxx = \"$cComCod\" AND ";
				$qCocDat .= "$cAlfa.fcoc$cAno.comcscxx = \"$cComCsc\" AND ";
				$qCocDat .= "$cAlfa.fcoc$cAno.comcsc2x = \"$cComCsc2\" LIMIT 0,1";
				//f_Mensaje(__FILE__,__LINE__,$qCocDat);
				$xCocDat  = mysql_query($qCocDat,$xConexion01);

				if (mysql_num_rows($xCocDat) > 0) {
					$vCocDat  = mysql_fetch_array($xCocDat);

					$cCscFac = $vCocDat['resprexx'].$vCocDat['comcscxx'];

					//Factura seleccionada en cabecera
					$vDatFac = explode("~",$vCocDat['comobs2x']);

					//Forma de pago
					$cFormaPago = "";
					if ($vDatFac[7] != "") {
						//Buscando descripcion
						$cFormaPago = ($vDatFac[7] == 1) ? "CONTADO" : "CREDITO";
					}
					//Buscando descripcion Medio de Pago
					$vMedPag['mpadesxx'] = "";
					if ($vDatFac[8] != "") {
						$qMedPag  = "SELECT ";
						$qMedPag .= "mpaidxxx, ";
						$qMedPag .= "mpadesxx, ";
						$qMedPag .= "regestxx ";
						$qMedPag .= "FROM $cAlfa.fpar0155 ";
						$qMedPag .= "WHERE ";
						$qMedPag .= "mpaidxxx = \"{$vDatFac[8]}\" LIMIT 0,1";
						$xMedPag  = f_MySql("SELECT","",$qMedPag,$xConexion01,"");
						// f_Mensaje(__FILE__,__LINE__,$qMedPag."~ ".mysql_num_rows($xMedPag));
						$vMedPag = mysql_fetch_array($xMedPag);
					}

					##Traigo las condiciones comerciales (Dias Credito) para el Facturar a:##
          $qPlazo  = "SELECT cccplaxx ";
          $qPlazo .= "FROM $cAlfa.fpar0151 ";
          $qPlazo .= "WHERE ";
          $qPlazo .= "cliidxxx = \"{$vCocDat['terid2xx']}\" LIMIT 0,1 ";
          $xPlazo  = f_MySql("SELECT","",$qPlazo,$xConexion01,"");
          //f_Mensaje(__FILE__,__LINE__,$qPlazo);
          $vPlazo  = mysql_fetch_array($xPlazo);
          ##Fin Traigo las condiciones comerciales (Dias Credito) para el Facturar a:##		

					$cPlazo = $vPlazo['cccplaxx'].(($vComObs[10] == 1) ? " DIA": " DIAS");

					//Trayendo datos de la factura referencia
					//Buscando datos de cabecera de la factura anulada
					$qFacAnu  = "SELECT resprexx, comcscxx, compcecu, comfecxx ";
					$qFacAnu .= "FROM $cAlfa.fcoc{$vDatFac[0]} ";
					$qFacAnu .= "WHERE ";
					$qFacAnu .= "comidxxx = \"{$vDatFac[1]}\" AND ";
					$qFacAnu .= "comcodxx = \"{$vDatFac[2]}\" AND ";
					$qFacAnu .= "comcscxx = \"{$vDatFac[3]}\" AND ";
					$qFacAnu .= "comcsc2x = \"{$vDatFac[4]}\" LIMIT 0,1 ";
					$xFacAnu  = mysql_query($qFacAnu,$xConexion01);
					// f_Mensaje(__FILE__,__LINE__,$qFacAnu."~".mysql_num_rows($xFacAnu));
					$vFacAnu     = mysql_fetch_array($xFacAnu);
					$cDocRef     = $vFacAnu['resprexx'].$vFacAnu['comcscxx'];
					$dFecDocRef  = $vFacAnu['comfecxx'];
					$cCufeDocRef = $vFacAnu['compcecu'];

					// Nombre del usuario logueado.
					$qUsrNom  = "SELECT USRNOMXX ";
					$qUsrNom .= "FROM $cAlfa.SIAI0003 ";
					$qUsrNom .= "WHERE ";
					$qUsrNom .= "USRIDXXX = \"{$vCocDat['regusrxx']}\" LIMIT 0,1 ";
					$xUsrNom  = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qUsrNom."~".mysql_num_rows($xUsrNom));
					$vUsrNom  = mysql_fetch_array($xUsrNom);

					##Traigo Departamento del Cliente ##
					$qDatDep  = "SELECT DEPDESXX  ";
					$qDatDep .= "FROM $cAlfa.SIAI0054 ";
					$qDatDep .= "WHERE ";
					$qDatDep .= "PAIIDXXX =\"{$vCocDat['PAIIDXXX']}\" AND ";
					$qDatDep .= "DEPIDXXX =\"{$vCocDat['DEPIDXXX']}\" LIMIT 0,1";
					$xDatDep  = f_MySql("SELECT","",$qDatDep,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qDatDep."~".mysql_num_rows($xDatDep));
					if (mysql_num_rows($xDatDep) > 0) {
						$vDatDep = mysql_fetch_array($xDatDep);
					}
					##Fin Traigo Departamento del Cliente ##

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
					if (mysql_num_rows($xCiuDat) > 0) {
						$vCiuDat = mysql_fetch_array($xCiuDat);
					}
					##Fin Traigo Ciudad del Cliente ##

					////// DETALLE 1002 /////
					$qCodDat  = "SELECT DISTINCT ";
					$qCodDat .= "$cAlfa.fcod$cAno.* ";
					$qCodDat .= "FROM $cAlfa.fcod$cAno ";
					$qCodDat .= "WHERE ";
					$qCodDat .= "$cAlfa.fcod$cAno.comidxxx = \"$cComId\" AND ";
					$qCodDat .= "$cAlfa.fcod$cAno.comcodxx = \"$cComCod\" AND ";
					$qCodDat .= "$cAlfa.fcod$cAno.comcscxx = \"$cComCsc\" AND ";
					$qCodDat .= "$cAlfa.fcod$cAno.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cAno.comseqxx) ASC ";
					$xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qCodDat);
					// Matriz para pagos de 4xmil GMF
					if (mysql_num_rows($xCodDat) > 0) {
						// Cargo la Matriz con los ROWS del Cursor
						$iA=0;
						while ($xRCD = mysql_fetch_array($xCodDat)) {

							//La base e iva de los cuadre de DO son cero (0) y los ajustes al peso
							if ($xRCD['comctocx'] == "CD" || $xRCD['comctocx'] == "ADCI" || $xRCD['comctocx'] == "ADCG") {
								$xRCD['comvlr01'] = 0;
								$xRCD['comvlr02'] = 0;
							}

							if ($xRCD['sucidxxx'] != "" && $xRCD['docidxxx'] != "" && $xRCD['docsufxx'] != "") {
								if ($cDocId == "") {
									$cDocId  = $xRCD['docidxxx'];
									$cDocSuf = $xRCD['docsufxx'];
									$cSucId  = $xRCD['sucidxxx'];
								}
							}
							
							if($xRCD['comctocx'] == "PCC" && $xRCD['comidc2x'] == "") {
								//Impuesto financiero
								$nInd_mDatGmf = count($mDatGmf);
								$mDatGmf[$nInd_mDatGmf]['ctoidxxx'] = $xRCD['ctoidxxx'];
								$mDatGmf[$nInd_mDatGmf]['comobsxx'] = $xRCD['comobsxx'];
								$mDatGmf[$nInd_mDatGmf]['comvlrxx'] = $xRCD['comvlrxx'];
								$mDatGmf[$nInd_mDatGmf]['comvlrme'] = $xRCD['comvlrme'];
								$mDatGmf[$nInd_mDatGmf]['puctipej'] = $xRCD['puctipej'];
								$mDatGmf[$nInd_mDatGmf]['ctoidxxx'] = $xRCD['ctoidxxx'];
								$mDatGmf[$nInd_mDatGmf]['comvlr01'] = $xRCD['comvlr01'];
							}else if ($xRCD['comctocx'] == "PCC") {
								//pagos por cuenta del cliente
								$nInd_mValores = count($mValores);
								$mValores[$nInd_mValores]['ctoidxxx'] = $xRCD['ctoidxxx'];
								$mValores[$nInd_mValores]['comobsxx'] = $xRCD['comobsxx'];
								$mValores[$nInd_mValores]['comvlrxx'] = $xRCD['comvlrxx'];
								$mValores[$nInd_mValores]['comvlrme'] = $xRCD['comvlrme'];
								$mValores[$nInd_mValores]['puctipej'] = $xRCD['puctipej'];
								$mValores[$nInd_mValores]['ctoidxxx'] = $xRCD['ctoidxxx'];
								$mValores[$nInd_mValores]['comvlr01'] = $xRCD['comvlr01'];
							} else if ($vCtoAnt["{$xRCD['ctoidxxx']}"]['ctoantxx'] == "SI") {
								//Anticipos
								$nTotAnt += $xRCD['comvlrxx'];
							} else {
								switch ($xRCD['comctocx']) {
									case "IP":
										$nSwitch_Encontre_Concepto = 0;
										//Trayendo descripcion concepto, cantidad y unidad
										$vDatosIp = array();
										$vDatosIp = f_Cantidad_Ingreso_Propio($xRCD['comobsxx'],'',$xRCD['sucidxxx'],$xRCD['docidxxx'],$xRCD['docsufxx']);
										
										//Los IP se agrupan por Sevicio
										for($j=0;$j<count($mCodDat);$j++){
											if($mCodDat[$j]['ctoidxxx'] == $xRCD['ctoidxxx'] && $mCodDat[$j]['seridxxx'] == $xRCD['seridxxx']){
												$nSwitch_Encontre_Concepto = 1;

												$mCodDat[$j]['comvlrxx'] += $xRCD['comvlrxx'];
												$mCodDat[$j]['comvlrme'] += $xRCD['comvlrme'];
												
												$mCodDat[$j]['compivax']  = $xRCD['compivax']; // Porcentaje IVA
												$mCodDat[$j]['comvlr01'] += $xRCD['comvlr01']; // Valor Iva

												//Cantidad FE
												$mCodDat[$j]['canfexxx'] += $vDatosIp[1];
												//Cantidad por condicion especial
												for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
													$mCodDat[$j]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] += $vDatosIp[3][$nP]['valpdfxx'];
												}
											}
										}

										if ($nSwitch_Encontre_Concepto == 0) {
											$nInd_mConData = count($mCodDat);
											$mCodDat[$nInd_mConData] = $xRCD;
											if ($xRCD['comctocx'] == "IP") {
												$mCodDat[$nInd_mConData]['comobsxx'] = $vDatosIp[0];
												$mCodDat[$nInd_mConData]['canfexxx'] = $vDatosIp[1];
												$mCodDat[$nInd_mConData]['unidadfe'] = $vDatosIp[2];
		
												for ($nP=0; $nP<count($vDatosIp[3]); $nP++) {
													$mCodDat[$nInd_mConData]['itemcanx'][str_replace(" ","_","{$vDatosIp[3][$nP]['despdfxx']}")] = $vDatosIp[3][$nP]['valpdfxx'];
												}
											}
										} 
									break;
									case "RETCRE":
										// Rete CREE
										$nTotCree += $xRCD['comvlrxx'];
									break;
									case "ARETCRE":
										// Auto RCree
										$nTotACree += $xRCD['comvlrxx'];
									break;
									case "RETFTE":
										// Rete Fuente
										$nTotRfte += $xRCD['comvlrxx'];
									break;
									case "ARETFTE":
										// Auto RFte
										$nTotARfte += $xRCD['comvlrxx'];
									break;
									case "RETICA":
										// Rete ICA
										$nTotIca += $xRCD['comvlrxx'];
									break;
									case "ARETICA":
										// Auto RICA
										$nTotAIca += $xRCD['comvlrxx'];
									break;
									case "RETIVA":
										// Rete Iva
										$nTotIva += $xRCD['comvlrxx'];
									break;
									case "IVAIP":
										// Iva
										$nIva += $xRCD['comvlrxx'];
									break;
									default:
										//No hace Nada
									break;
								}
							}
						}
						// Fin de Cargo la Matriz con los ROWS del Cursor
					}

					##Trayendo Datos de Do Dependiendo del Tipo de Operacion ##
					$vDatDo 		= f_Datos_Operativos_Do($cSucId, $cDocId, $cDocSuf);
					$vDceDat 		= $vDatDo['decdatxx'];
					$cTasCam 		= $vDatDo['tascamxx']; //Tasa de Cambio
					$cDocTra 		= $vDatDo['doctraxx']; //Documento de Transporte
					$cBultos 		= $vDatDo['bultosxx']; //Bultos
					$cPesBru		= $vDatDo['pesbruxx']; //Peso Bruto
					$nValAdu 		= $vDatDo['valaduxx']; //Valor en aduana
					$cOpera  		= $vDatDo['operaxxx']; //texto valor CIF: o FOB: segun el tipo operacion del tramite
					$cPedido 		= $vDatDo['pedidoxx']; //Pedido
					$cAduana 		= $vDatDo['aduanaxx']; //Descripcion Aduana
					$cNomVen 		= $vDatDo['nomvenxx']; //Nombre Vendedor
					$cOrdCom 		= $vDatDo['ordcomxx']; //Orden de Compra
					$cPaiOri 		= $vDatDo['paiorixx']; //Pais de Origen
					$cLugIngDes = $vDatDo['lindesxx']; //Lugar de Ingreso Descripcion
					###Fin Trayendo Datos de Do Dependiendo del Tipo de Operacion ##
				}

				$pdf->AddPage();

				$nPosY = $pdf->GetY()+6;
        $nPosX = 5;
        $nPosFin = 240;
        $nPosYVl = 185;
        $nb = 1;
        $pyy = $nPosY;
        // Imprimo Detalle de Pagos a Terceros e Ingresos Propios
        // Imprimo Pagos a Terceros
        if (count($mValores) > 0) { //Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
          $nTotPcc    = 0; 
          $cCodigoPCC = "";
          
          // Recorro la matriz de la 1002 para imprimir Registros de PCC
          for ($i=0;$i<count($mValores);$i++) {
            $cCodigoPCC = ($cCodigoPCC == "") ? $mValores[$i]['ctoidxxx'] : $cCodigoPCC;
            $nTotPcc   += $mValores[$i]['comvlrxx'];
          }//for ($i=0;$i<count($mValores);$i++) {

          $pdf->setXY($nPosX,$pyy);
          $pdf->SetWidths(array(20, 66, 15, 24, 11, 13, 17, 39));
          $pdf->SetAligns(array("L", "L", "C", "R", "C", "R", "R", "R"));
          $pdf->SetFont($cEstiloLetra,'B',8);

          $pdf->Row(array(
                  utf8_decode($cCodigoPCC),
                  utf8_decode("TOTAL PAGOS EFECTUADOS POR SU CUENTA"),
                  number_format(1, 0, ',', '.'),
                  number_format($nTotPcc, 0, ',', '.'),
                  "0%",
                  "0",
                  number_format(0, 2, ',', '.'),
                  number_format($nTotPcc, 0, ',', '.'),
              ));
          $pyy = $pdf->GetY() + 6;
        }//if(count($mIngTer) > 0 || $nBandPcc == 1){//Si la matriz de Pcc o Bandera de PCC de Detalle viene en 1
        // Fin Imprimo Pagos a Terceros
				
				// Imprimo Ingresos Propios
        $nSubToIP = 0;    // Subtotal pagos propios
        $nSubToIPIva = 0; // Iva 19%

        $nSubToIPGra   = 0; // Total Ingresos Gravados
				$nSubToIPNoGra = 0; // Total Ingresos No Gravados
				
				if(count($mCodDat) > 0 || $nBandIP == 1 || count($mDatGmf)) {
					$pdf->setXY($nPosX,$pyy);
          $pdf->SetFont($cEstiloLetra,'B',8);
          $pdf->Cell(20,6,"",0,0,'L');
					$pdf->Cell(66,6,utf8_decode("SERVICIOS PRESTADOS"),0,0,'L');
					$pyy += 6;
				}

        if(count($mCodDat) > 0 || $nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
          $pdf->setXY($nPosX,$pyy);
          $pdf->SetWidths(array(20, 66, 15, 24, 11, 13, 17, 39));
          $pdf->SetAligns(array("L", "L", "C", "R", "C", "R", "R", "R"));
          $pdf->SetFont($cEstiloLetra,'',8);

          // hago dos veces el mismo for para separar los ingresos GRAVADOS y luego los NO GRAVADOS
          for($k=0;$k<(count($mCodDat));$k++) {
            $pyy = $pdf->GetY();

            if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
              $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,($nPosFin+10));
              $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,($nPosFin+10));
              $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,($nPosFin+10));
              $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,($nPosFin+10));
              $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,($nPosFin+10));
              $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,($nPosFin+10));
              $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,($nPosFin+10));
              $pdf->Rect($nPosX, $nPosY-6, 205, ($nPosFin+10) - ($nPosY - 6));
              $pdf->AddPage();
              $nb++;
              $nPosY = $pdf->GetY()+6;
              $nPosX = 5;
              $pyy = $nPosY;
              $pdf->SetFont($cEstiloLetra,'',8);
              $pdf->setXY($nPosX,$pyy);
            }

            if($mCodDat[$k]['comctocx'] == "IP" && $mCodDat[$k]['comvlr01'] != 0 ) {
              $nSubToIP    += $mCodDat[$k]['comvlrxx'];
              $nSubToIPIva += $mCodDat[$k]['comvlr01'];
              $nSubToIPGra += $mCodDat[$k]['comvlrxx'];

              $cValor = ""; $cValCon = "";
              //Mostrando cantidades por tipo de cantidad
              foreach ($mCodDat[$k]['itemcanx'] as $cKey => $cValue) {
                // Personalizacion de la descripcion por base de datos e informacion adicional
                if($cKey == "FOB" && $cValue > 0) {
                  $cValor  = " FOB: ($".$cValue;
                  $cValor .= ($mCodDat[$k]['itemcanx']['TRM'] > 0) ? " TRM: $".$mCodDat[$k]['itemcanx']['TRM'] : "";
                  $cValor .= ")";
                } elseif ($cKey == "CIF") {
                  $cValor = "CIF: ($".$cValue.")";
                } elseif ($cKey == "CONTENEDORES_DE_20") {
                  $cValCon .= " CONTENEDORES DE 20: (".$cValue.")";
                } elseif ($cKey == "CONTENEDORES_DE_40") {
                  $cValCon .= " CONTENEDORES DE 40: (".$cValue.")";
                }elseif ($cKey == "UNIDADES_DE_CARGA_SUELTA") {
                  $cValCon .= " UNIDADES DE CARGA SUELTA: (".$cValue.")";
                }
                $cValor = ($cValCon != "") ? $cValCon : $cValor;
              }

              $nCantidad = number_format((($mCodDat[$k]['unidadfe'] == "A9") ? 1 : $mCodDat[$k]['canfexxx']),2,'.','');
              $nValUni   = number_format((($mCodDat[$k]['unidadfe'] != "A9" && $mCodDat[$k]['canfexxx'] > 0) ? ($mCodDat[$k]['comvlrxx']/$mCodDat[$k]['canfexxx']) : $mCodDat[$k]['comvlrxx']),2,'.','');
              $nValIva   = number_format($mCodDat[$k]['comvlr01'], 0, ',', '.');

              $pdf->setX($nPosX);
              $pdf->Row(array(
                  utf8_decode($mCodDat[$k]['ctoidxxx']),
                  utf8_decode("* ".trim($mCodDat[$k]['comobsxx'].$cValor)),
                  number_format($nCantidad, 0, ',', '.'),
                  number_format($nValUni, 0, ',', '.'),
                  ($mCodDat[$k]['compivax']+0)."%",
                  $nValIva,
                  number_format(0, 2, ',', '.'),
                  number_format($mCodDat[$k]['comvlrxx'], 0, ',', '.'),
              ));
            }//if($mCodDat[$k]['comctocx'] == 'IP'){
          }## for($k=$nPosIP;$k<(count($mCodDat));$k++) { ##

          for($k=0;$k<(count($mCodDat));$k++) {
            $pyy = $pdf->GetY();
            if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
              $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,($nPosFin+10));
              $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,($nPosFin+10));
              $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,($nPosFin+10));
              $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,($nPosFin+10));
              $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,($nPosFin+10));
              $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,($nPosFin+10));
              $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,($nPosFin+10));
              $pdf->Rect($nPosX, $nPosY-6, 205, ($nPosFin+10) - ($nPosY - 6));
              $pdf->AddPage();
              $nb++;
              $nPosY = $pdf->GetY()+6;
              $nPosX = 5;
              $pyy = $nPosY;
              $pdf->SetFont($cEstiloLetra,'',8);
              $pdf->setXY($nPosX,$nPosY);
            }

            if($mCodDat[$k]['comctocx'] == "IP" && $mCodDat[$k]['comvlr01'] == 0 ) {
              $nSubToIP      += $mCodDat[$k]['comvlrxx'];
              $nSubToIPIva   += $mCodDat[$k]['comvlr01'];
              $nSubToIPNoGra += $mCodDat[$k]['comvlrxx'];

              $cValor = ""; $cValCon = "";
              //Mostrando cantidades por tipo de cantidad
              foreach ($mCodDat[$k]['itemcanx'] as $cKey => $cValue) {
                // Personalizacion de la descripcion por base de datos e informacion adicional
                if($cKey == "FOB" && $cValue > 0) {
                  $cValor  = " FOB: ($".$cValue;
                  $cValor .= ($mCodDat[$k]['itemcanx']['TRM'] > 0) ? " TRM: $".$mCodDat[$k]['itemcanx']['TRM'] : "";
                  $cValor .= ")";
                } elseif ($cKey == "CIF") {
                  $cValor = "CIF: ($".$cValue.")";
                } elseif ($cKey == "CONTENEDORES_DE_20") {
                  $cValCon .= " CONTENEDORES DE 20: (".$cValue.")";
                } elseif ($cKey == "CONTENEDORES_DE_40") {
                  $cValCon .= " CONTENEDORES DE 40: (".$cValue.")";
                }elseif ($cKey == "UNIDADES_DE_CARGA_SUELTA") {
                  $cValCon .= " UNIDADES DE CARGA SUELTA: (".$cValue.")";
                }
                $cValor = ($cValCon != "") ? $cValCon : $cValor;
              }

              $nCantidad = number_format((($mCodDat[$k]['unidadfe'] == "A9") ? 1 : $mCodDat[$k]['canfexxx']),2,'.','');
              $nValUni   = number_format((($mCodDat[$k]['unidadfe'] != "A9" && $mCodDat[$k]['canfexxx'] > 0) ? ($mCodDat[$k]['comvlrxx']/$mCodDat[$k]['canfexxx']) : $mCodDat[$k]['comvlrxx']),2,'.','');
              $nValIva   = number_format($mCodDat[$k]['comvlr01'], 0, ',', '.');

              $pdf->setX($nPosX);
              $pdf->Row(array(
                  utf8_decode($mCodDat[$k]['ctoidxxx']),
                  utf8_decode(trim($mCodDat[$k]['comobsxx'].$cValor)),
                  number_format($nCantidad, 0, ',', '.'),
                  number_format($nValUni, 0, ',', '.'),
                  ($mCodDat[$k]['compivax']+0)."%",
                  $nValIva,
                  number_format(0, 2, ',', '.'),
                  number_format($mCodDat[$k]['comvlrxx'], 0, ',', '.'),
              ));
            }//if($mCodDat[$k]['comctocx'] == 'IP'){
          }## for($k=$nPosIP;$k<(count($mCodDat));$k++) { ##

        }//if(count($mCodDat) > 0 || $nBandIP == 1){//Valido si la Bandera de IP viene en 1 para imprimir bloque de INGRESOS PROPIOS
        // Fin Imprimo Ingresos Propios
        // Fin Imprimo Detalle de Pagos a Terceros e Ingresos Propios

				// Impresion GMF
        if ( count($mDatGmf) > 0 ){

          $pyy = ($pdf->GetY() > $pyy) ? $pdf->GetY(): $pyy;

          if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
            $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,($nPosFin+10));
            $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,($nPosFin+10));
            $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,($nPosFin+10));
            $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,($nPosFin+10));
            $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,($nPosFin+10));
            $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,($nPosFin+10));
            $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,($nPosFin+10));
            $pdf->Rect($nPosX, $nPosY-6, 205, ($nPosFin+10) - ($nPosY - 6));
            $pdf->AddPage();
            $nb++;
            $nPosY = $pdf->GetY()+6;
            $nPosX = 5;
            $pyy = $nPosY;
            $pdf->SetFont($cEstiloLetra,'',8);
            $pdf->setXY($nPosX,$pyy);
          }

					$nSubTotGmf = 0; 
					$cCodigoGMF = "";
          for ($i=0;$i<count($mDatGmf);$i++) {
            $cCodigoGMF    = ($cCodigoGMF == "") ? $mDatGmf[$i]['ctoidxxx'] : $cCodigoGMF;
            $nSubTotGmf   += $mDatGmf[$i]['comvlrxx'];
          }//for ($i=0;$i<count($mDatGmf);$i++) {

          $pdf->setXY($nPosX,$pyy);
          $pdf->SetWidths(array(20, 66, 15, 24, 11, 13, 17, 39));
          $pdf->SetAligns(array("L", "L", "C", "R", "C", "R", "R", "R"));
          $pdf->SetFont($cEstiloLetra,'',8);

          $pdf->Row(array(
              utf8_decode($cCodigoGMF),
              utf8_decode("RECUPERACIÓN GASTOS BANCARIOS (GMF)"),
              number_format(1, 0, ',', '.'),
              number_format($nSubTotGmf, 0, ',', '.'),
              "0%",
              "0",
              number_format(0, 2, ',', '.'),
              number_format($nSubTotGmf, 0, ',', '.'),
          ));
          $pyy += 6;
        }
				// Fin Impresion GMF
				
				//Total a Pagar
				$nTotalconAnticipos = round(($nTotAnt*-1) + $nTotPcc + $nSubTotGmf + $nSubToIP + $nSubToIPIva - ($nTotCree + $nTotRfte + $nTotIva + $nTotIca) + ($nTotACree + $nTotARfte + $nTotAIca),2);
				$nTotPag = ($nTotalconAnticipos > 0) ? $nTotalconAnticipos : 0;
				$nTotAntCruce = abs($nTotAnt);
				if ($nTotalconAnticipos < 0 || $nTotPag == 0) {
					$nTotAntCruce = abs($nTotAnt + $nTotalconAnticipos);
				}

				//Valor en letras
				$nTotPag1 = f_Cifra_Php(str_replace("-","",abs($nTotPag)),"PESO");

				if($pyy > $nPosYVl){//Validacion para siguiente pagina si se excede espacio de impresion
          $pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,($nPosFin+10));
          $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,($nPosFin+10));
          $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,($nPosFin+10));
          $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,($nPosFin+10));
          $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,($nPosFin+10));
          $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,($nPosFin+10));
          $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,($nPosFin+10));
          $pdf->Rect($nPosX, $nPosY-6, 205, ($nPosFin+10) - ($nPosY - 6));
          $pdf->AddPage();
          $nb++;
          $nPosY = $pdf->GetY()+6;
          $nPosX = 5;
          $pyy = $nPosYVl;
          $pdf->SetFont($cEstiloLetra,'',8);
          $pdf->setXY($nPosX,$pyy);
        } else {
          $pyy = $nPosYVl;
          $pdf->SetFont($cEstiloLetra,'',8);
          $pdf->setXY($nPosX,$pyy);
				}
				
				$pdf->Line($nPosX+20,$nPosY-6,$nPosX+20,$pyy);
        $pdf->Line($nPosX+86,$nPosY-6,$nPosX+86,$pyy);
        $pdf->Line($nPosX+101,$nPosY-6,$nPosX+101,$pyy);
        $pdf->Line($nPosX+125,$nPosY-6,$nPosX+125,$pyy);
        $pdf->Line($nPosX+136,$nPosY-6,$nPosX+136,$pyy);
        $pdf->Line($nPosX+149,$nPosY-6,$nPosX+149,$pyy);
        $pdf->Line($nPosX+166,$nPosY-6,$nPosX+166,$pyy);
        $pdf->Rect($nPosX, $nPosY-6, 205, $pyy - ($nPosY - 6));

        $pdf->setXY($nPosX, $pyy + 1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(135, 3.2, "OBSERVACIONES: \n" . utf8_decode($vCocDat['comobsxx']).". ".substr($cObsPedido, 0, -2), 0, 'L');

        $pdf->setXY($nPosX, $pyy + 23);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(135, 3.2, utf8_decode("AGENTES RETENEDORES DE IVA - NO SOMOS GRANDES CONTRIBUYENTES - SOMOS AUTORRETENEDORES EN RENTA RESOLUCIÓN DIAN 005315 DE JUNIO 26 DE 2013 RESPONSABLE DE IVA. - ACTIVIDAD ECONÓMICA PRINCIPAL (CIIU): 5229 \nSOMOS AUTORRETENEDORES DE ICA EN: CARTAGENA, BARRANQUILLA, SANTA MARTA Y RIOHACHA"), 0, 'L');

        $pdf->setXY($nPosX, $pyy + 54);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(135, 3.2, utf8_decode("Consignar en Banco de Occidente 400058467 Cta. cte, Bancolombia 60400010197 Cta. cte, a nombre de Agencia de Aduanas Mario Londoño."), 0, 'L');

        $pdf->setXY($nPosX + 137, $pyy + 50);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(68, 4, "TOTAL EN LETRAS", 0, 0, 'C');
        $pdf->Ln(4);
        $pdf->setX($nPosX + 137);
        $pdf->SetFont('Arial', '', 7);
        $pdf->MultiCell(68, 3, utf8_decode($nTotPag1), 0, 'C');

        ### Columna de Subtotales ##
        //Para la nota debito el campo de ip gravados se trae del subtotal de ingresos porpios gravados
				$nTotIca = ($nTotAIca == 0) ? $nTotIca : 0;
				$nTotFac = floatval($nTotPcc + $nSubTotGmf + $nSubToIPGra + $nSubToIPNoGra + $nSubToIPIva) - ($nTotIva + $nTotIca);
				$nTotSalFav = $nTotFac - $nTotAntCruce;
        $pdf->setXY($nPosX + 136, $pyy + 1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(35, 4, "SUBTOTAL", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format(floatval($nSubToIPGra), 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "IVA", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format($nSubToIPIva, 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "RETE IVA", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format($nTotIva, 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "RETE ICA", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format($nTotIca, 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, utf8_decode("TOTAL NOTA CRÉDITO"), 0, 0, 'L');
        $pdf->Cell(34, 4, number_format($nTotFac, 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "ANTICIPOS", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format($nTotAntCruce, 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "TOTAL A PAGAR", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format($nTotPag, 0), 0, 0, 'R');
        $pdf->Ln(4.5);
        $pdf->setX($nPosX + 136);
        $pdf->Cell(35, 4, "TOTAL SALDO A FAVOR", 0, 0, 'L');
        $pdf->Cell(34, 4, number_format(($nTotSalFav > 0) ? 0 : abs($nTotSalFav), 0), 0, 0, 'R');

        $pdf->Line($nPosX, $pyy + 20, $nPosX + 136, $pyy + 20);
        $pdf->Line($nPosX, $pyy + 50, $nPosX + 205, $pyy + 50);
        $pdf->Line($nPosX + 136, $pyy, $nPosX + 136, $pyy + 65);
        $pdf->Rect($nPosX, $pyy, 205, 65);		
			}
		} else {
			//Se imprime formato anterior
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

					$qCocDat2 = "SELECT $cAlfa.fcoc$cAnoB.comfecxx ";
					$qCocDat2 .= "FROM $cAlfa.fcoc$cAnoB ";
					$qCocDat2 .= "WHERE ";
					$qCocDat2 .= "$cAlfa.fcoc$cAnoB.comidxxx = \"$cComIdB\" AND ";
					$qCocDat2 .= "$cAlfa.fcoc$cAnoB.comcodxx = \"$cComCodB\" AND ";
					$qCocDat2 .= "$cAlfa.fcoc$cAnoB.comcscxx = \"$cComCscB\" AND ";
					$qCocDat2 .= "$cAlfa.fcoc$cAnoB.comcsc2x = \"$cComCsc2B\" ";

					$xCocDat2 = f_MySql("SELECT", "", $qCocDat2, $xConexion01, "");

					$vRCC2 = mysql_fetch_array($xCocDat2);

					$mDatos['comfac2x'] = $cComCscB;
					$mDatos['comfacfe'] = $vRCC2['comfecxx'];
				}
				return $mDatos;
			}

			class PDF extends FPDF {

				function Header() {
					global $cAlfa, $cPlesk_Skin_Directory, $cEstiloLetra, $mDatCab, $vSysStr;
					/*** Impresion Datos Generales Factura ***/
					$nPosX = 5;
					$nPosY = 20;

					/*** Logo Malco. ***/
					$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco_gris.jpg',5,$nPosY,37,19);

					//echo "<pre>";
					//print_r($mDatCab);die();

					$this->SetFont($cEstiloLetra,'B',14);
					$this->setXY($nPosX+42,$nPosY+3);
					$this->Cell(70,5,utf8_decode("AGENCIA DE ADUANAS MARIO LONDOÑO S.A. NIVEL 1"),0,0,'L');
					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					$nPosY = $this->GetY();
					$this->setXY($nPosX+42,$nPosY+5);
					$this->SetFont($cEstiloLetra,'B',12);
					$this->Cell(70,4,utf8_decode("NIT: {$vSysStr['financiero_nit_agencia_aduanas']}-".f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas'])),0,0,'L');
					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					$this->SetFont($cEstiloLetra,'B',8);
					$this->setXY($nPosX+42,$nPosY+10);
					$this->Cell(70,4,utf8_decode("SEDE PRINCIPAL: Calle 8B Nº 65-191 Of 511, Medellín - Colombia"),0,0,'L');
					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					$this->SetFont($cEstiloLetra,'B',8);
					$this->setXY($nPosX+42,$nPosY+15);
					$this->Cell(70,4,utf8_decode("CUFE: "),0,0,'L');

					/*** Titulo Factura Venta No. ***/
					$this->setXY($nPosX,$nPosY+20);
					$this->SetFont($cEstiloLetra,'B',14);
					$this->Cell(205,6,utf8_decode("NOTA DE CREDITO Nro. ".$mDatCab['comcscxx']),0,0,'C', true);

					$nPosY = $this->GetY()+7;
					/*** Ciudad ***/
					$this->setXY($nPosX+2,$nPosY);
					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(20,5,utf8_decode("CIUDAD:"),0,0,'');
					$this->SetFont($cEstiloLetra,'',8);
					$this->Cell(70,5,$mDatCab['CIUDESXX'],0,0,'');

					$nPosY = $this->GetY()+2;
					$nRecPosY = $nPosY;
					/*** Bloque rectangulo de cabecera. ***/
					/*** Señores. ***/
					$this->setXY($nPosX+2,$nPosY+5);
					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(20,5,utf8_decode("SEÑOR(ES):"),0,0,'');
					$this->SetFont($cEstiloLetra,'',8);
					$this->MultiCell(72,5,$mDatCab['PRONOMXX'],0,'L');

					$nPosY = $this->GetY();
					/*** Nit. ***/
					$this->setXY($nPosX+2,$nPosY);
					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(20,5,utf8_decode("NIT:"),0,0,'');
					$this->SetFont($cEstiloLetra,'',8);
					$this->Cell(72,5,$mDatCab['terid2xx']." - ".f_Digito_Verificacion($mDatCab['terid2xx']),0,0,'');

					$nPosY = $this->GetY();
					/*** Direccion ***/
					$this->setXY($nPosX+2,$nPosY+5);
					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(20,5,utf8_decode("DIRECCIÓN:"),0,0,'');
					$this->SetFont($cEstiloLetra,'',8);
					$this->MultiCell(72,5,$mDatCab['CLIDIRXX'],0,'L');
					$nPosY = $this->GetY();
					/*** Telefono ***/
					$this->setXY($nPosX+2,$nPosY);
					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(20,5,utf8_decode("TELÉFONO:"),0,0,'');
					$this->SetFont($cEstiloLetra,'',8);
					$this->Cell(72,5,$mDatCab['CLITELXX'],0,0,'');

					$nPosY = $this->GetY();
					$this->nPyIni = $nPosY+6;
					$this->Rect($nPosX, $nRecPosY+4, $nPosX + 90 , ($nPosY-$nRecPosY)+2);

					$nPosY = $this->GetY();

					/*** FECHA N. CREDITO ***/
					$this->setXY($nPosX+97,$nPosY-22);
					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(29,5,utf8_decode("FECHA N. CREDITO:"),0,0,'');
					$this->SetFont($cEstiloLetra,'',8);
					$this->Cell(20,5,$mDatCab['comfecxx'],0,0,'');

					/*** FECHA DE VENTA ***/
					$this->setXY($nPosX+160,$nPosY-22);
					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(27,5,utf8_decode("FECHA DE VENTA:"),0,0,'');
					$this->SetFont($cEstiloLetra,'',8);
					$this->Cell(20,5,$mDatCab['comfecve'],0,0,'');
					//////////////////////////////////////////////////////////////////////////////
					$this->Line($nPosX+97,$nPosY-17,$nPosX+205,$nPosY-17);
					$this->Line($nPosX+160,$nPosY-22,$nPosX+160,$nPosY-7);

					/*** DO ***/
					$this->setXY($nPosX+97,$nPosY-17);
					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(7,5,utf8_decode("DO:"),0,0,'');
					$this->SetFont($cEstiloLetra,'',8);
					$this->Cell(28,5,$mDatCab['docidxxx']." ".utf8_decode($mDatCab['doctipxx']),0,0,'');

					/*** Pedido ***/
					$this->setXY($nPosX+160,$nPosY-17);
					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(11,5,utf8_decode("Pedido:"),0,0,'');
					$this->SetFont($cEstiloLetra,'',8);
					$this->Cell(35,5,$mDatCab['DOIPEDXX'],0,0,'');

					$this->Line($nPosX+97,$nPosY-12,$nPosX+205,$nPosY-12);

					/*** No. FACTURA ***/
					$this->setXY($nPosX + 97, $nPosY - 12);
					$this->SetFont($cEstiloLetra, 'B', 8);
					$this->Cell(20, 5, utf8_decode("FACTURA No:"), 0, 0, '');
					$this->SetFont($cEstiloLetra, '', 8);
					$this->Cell(35, 5, $mDatCab['comfac2x'], 0, 0, '');

					/*** FECHA FACTURA ***/
					$this->setXY($nPosX + 160, $nPosY - 12);
					$this->SetFont($cEstiloLetra, 'B', 8);
					$this->Cell(26, 5, utf8_decode("FECHA FACTURA:"), 0, 0, '');
					$this->SetFont($cEstiloLetra, '', 8);
					$this->Cell(28, 5, $mDatCab['comfacfe'], 0, 0, '');

					//////////////////////////////////////////////////////////////////////////////
					$this->Line($nPosX+97,$nPosY-7,$nPosX+205,$nPosY-7);

					/*** SON ***/
					$nTotPag1 = f_Cifra_Php($mDatCab['comvlrxx']);
					$this->setXY($nPosX+97,$nPosY-7);
					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(10,5,utf8_decode("SON:"),0,0,'');
					$this->setXY($nPosX+97,$nPosY-2);
					$this->SetFont($cEstiloLetra,'',8);
					$this->MultiCell(108,5,trim($nTotPag1),0,'');


					/** Cabecera de la tabla
					** Cuenta **/

					$nPosY = $this->nPyIni;

					$this->setXY($nPosX,$nPosY+5);

					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(25,6,utf8_decode("CUENTA"),1,0,'C', true);
					$this->Cell(87,6,utf8_decode("CONCEPTO"),1,0,'C', true);  /*** Concepto ***/
					$this->Cell(26,6,utf8_decode("VALOR"),1,0,'C', true);     /*** Valor ***/
					$this->Cell(26,6,utf8_decode("IVA"),1,0,'C', true);       /*** IVA ***/
					$this->Cell(26,6,utf8_decode("TOTAL"),1,0,'C', true);     /*** Total ***/
					$this->Cell(15,6,utf8_decode("NAT."),1,0,'C', true);      /*** NAT. ***/

					/** Linea de fin de la tabla del contenido en forma horizontal **/
					$this->Line($nPosX,$nPosY+155,$nPosX+205,$nPosY+155);

					/* Linea que da formato a la tabla
					** Linea inicial de forma vertical **/
					$this->Line($nPosX,$nPosY+5,$nPosX,$nPosY+155);
					/** Linea donde inicia el Concepto **/
					$this->Line($nPosX+25,$nPosY+5,$nPosX+25,$nPosY+155);
					/** Linea donde finaliza el Concepto e inicia Valor**/
					$this->Line($nPosX+112,$nPosY+5,$nPosX+112,$nPosY+155);
					/** Linea donde finaliza el Valor e inicia IVA**/
					$this->Line($nPosX+138,$nPosY+5,$nPosX+138,$nPosY+155);
					/** Linea donde finaliza el IVA e inicia Total**/
					$this->Line($nPosX+164,$nPosY+5,$nPosX+164,$nPosY+155);
					/** Linea donde finaliza el TOTAL e inicia NAT.**/
					$this->Line($nPosX+190,$nPosY+5,$nPosX+190,$nPosY+155);
					/** Linea final de forma vertical **/
					$this->Line($nPosX+205,$nPosY+5,$nPosX+205,$nPosY+155);

				}//Function Header

				function Footer() {

					global $mDatCab;

					$nPosX = 5;
					$nPosY = 250;

					$this->Line($nPosX+5,$nPosY+10,$nPosX+100,$nPosY+10);

					$this->setXY($nPosX,$nPosY+10);
					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(50,6,utf8_decode("FIRMA, CC/NIT y SELLO"),0,0,'C');
					$this->Cell(15,6,utf8_decode("RECIBÍ"),0,0,'C');
					$this->Cell(10,6,utf8_decode("DIA"),0,0,'L');
					$this->Cell(10,6,utf8_decode("MES"),0,0,'L');
					$this->Cell(10,6,utf8_decode("AÑO"),0,0,'L');

					/* ELABORADO POR: */
					$this->setXY($nPosX+120,$nPosY+5);
					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(17,6,utf8_decode("ELABORÓ:"),0,0,'L');
					$this->SetFont($cEstiloLetra,'',8);
					$this->Cell(68,6,utf8_decode($mDatCab['USRNOMXX']),0,0,'L');

					/* WEB SITE */
					$this->setXY($nPosX+110,$nPosY+10);
					$this->SetFont($cEstiloLetra,'B',8);
					$this->Cell(44,6,utf8_decode("Visite nuestro sitio en internet:"),0,0,'L');
					$this->SetFont($cEstiloLetra,'U',8);
					$this->Cell(40,6 ,'www.grupomalco.com',0,0,'L');

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
			$pdf->AliasNbPages();
			$pdf->SetMargins(0,0,0);
			$pdf->SetAutoPageBreak(0,20);
			$pdf->SetFillColor(209,209,209);

			$pdf->SetWidths(array(25,87,26,26,26,15));
			$pdf->SetAligns(array("L","L","R","R","R","C"));

			/*** Se imprime el detalle. ***/
			for ($i=0; $i < count($mPrn); $i++) {

				/*** Consulto data de cabecera.  ***/
				$mDatCab = fnDatosCabecera($mPrn[$i]);
				$pdf->AddPage();

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
						//f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
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
							//f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
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
									//f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
									if (mysql_num_rows($xCtoCon) > 0) {
										$vCtoCon = mysql_fetch_array($xCtoCon);
										$vCtoCon['ctodesxx'] = $vCtoCon['serdesxx'];
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

						$mCodDat[$iA] = $xRCD;
						$iA++;
					}
					// Fin de Cargo la Matriz con los ROWS del Cursor
					$nPyIni  = $pdf->GetY(); // Posicion Y inicial para imprimir el detalle
					$nPosX   = 5;
					$nPosY   = $pdf->nPyIni+12;
					$pyy     = $nPosY;
					$nPosFin = 224;
					//f_Mensaje(__FILE__,__LINE__,$$this->nPyIni);

					/*** Seteando anchos y alineacion para la impresión del Row. ***/
					$pdf->setXY($nPosX,$nPosY);

					// $mCodDat = array_merge($mCodDat,$mCodDat); // ESTE ARRAY ES SOLO PARA PRUEBAS

					for ($k=0;$k<count($mCodDat);$k++) {

						if($mCodDat[$k]['comctocx'] != "IVAIP"){

							if($pyy > $nPosFin){//Validacion para siguiente pagina si se excede espacio de impresion
								$pdf->AddPage();
								$nPosY = $pdf->nPyIni+12;
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

							/*** Detalle Nota Credito ***/
							$pdf->setX($nPosX);
							$pdf->Row(array($mCodDat[$k]['pucidxxx'],
															$mCodDat[$k]['ctodesxx'],
															number_format($mCodDat[$k]['comvlr01'],0,',','.'),
															number_format($mCodDat[$k]['comvlr02'],0,',','.'),
															number_format($mCodDat[$k]['comvlrxx'],0,',','.'),
															$mCodDat[$k]['commovxx']));
							$pyy = $pdf->getY();

							if($mCodDat[$k]['comctocx'] == "PCC" && $mCodDat[$k]['comidc2x'] == "P"){

								for($cAnoP = $cAno; $cAnoP >= $vSysStr['financiero_ano_instalacion_modulo']; $cAnoP--){
									$qPagTer  = "SELECT ";
									$qPagTer .= "pucretxx,";
									$qPagTer .= "ctoidxxx,";
									$qPagTer .= "pucidxxx,";
									$qPagTer .= "comvlr01 ";
									$qPagTer .= "FROM $cAlfa.fcod$cAnoP ";
									$qPagTer .= "WHERE ";
									$qPagTer .= "comidxxx = \"{$mCodDat[$k]['comidc2x']}\" AND ";
									$qPagTer .= "comcodxx = \"{$mCodDat[$k]['comcodc2']}\" AND ";
									$qPagTer .= "comcscxx = \"{$mCodDat[$k]['comcscc2']}\" AND ";
									$qPagTer .= "teridxxx = \"{$mCodDat[$k]['teridxxx']}\" ";
									$qPagTer .= "ORDER BY ABS(comseqxx) ";
									$xPagTer  = f_MySql("SELECT","",$qPagTer,$xConexion01,"");
									//f_Mensaje(__FILE__,__LINE__,$qPagTer."~".mysql_num_rows($xPagTer));

									if(mysql_num_rows($xPagTer) > 0){
										$mRetPag = array();
										$cRetPag = "NO";
										while($xRPT = mysql_fetch_array($xPagTer)){

											/*** Si ya localizo el concepto de pago a tercero se Agregan las Retenciones***/
											if($cRetPag == "SI"){
												if($mPucIds[$xRPT['pucidxxx']]['pucterxx'] == "R"){
													$nInd_mRetPag = count($mRetPag);
													$mRetPag[$nInd_mRetPag]['ctoidxxx'] = $xRPT['ctoidxxx'];
													$mRetPag[$nInd_mRetPag]['ctodesxx'] = $mCtoDes["{$xRPT['pucidxxx']}-{$xRPT['ctoidxxx']}"]['ctodesxp'];
													$mRetPag[$nInd_mRetPag]['pucidxxx'] = $xRPT['pucidxxx'];
													$mRetPag[$nInd_mRetPag]['pucretxx'] = $xRPT['pucretxx'];
													$mRetPag[$nInd_mRetPag]['comvlr01'] = ($mCodDat[$k]['comvlr01']*($xRPT['pucretxx']/100));
													$mRetPag[$nInd_mRetPag]['comvlr02'] = 0;
													$mRetPag[$nInd_mRetPag]['comvlrxx'] = ($mCodDat[$k]['comvlr01']*($xRPT['pucretxx']/100));
													$mRetPag[$nInd_mRetPag]['commovxx'] = $mCodDat[$k]['commovxx'];
												}else{
													break;
												}
											}

											/*** Se localiza el concepto del pago a tercero y se marca para que en el proximo recorrido entre a las retenciones si las tiene. ***/
											if($xRPT['ctoidxxx'] == $mCodDat[$k]['ctoidxxx']){
												$cRetPag = "SI";
											}
										}
										break;
									}
								}

								if(count($mRetPag) > 0){
									for($nRP = 0; $nRP < count($mRetPag); $nRP++){
										/*** Retenciones Pago a Tercero ***/
										$pdf->setX($nPosX);
										$pdf->Row(array($mRetPag[$nRP]['pucidxxx'],
																		$mRetPag[$nRP]['ctodesxx'],
																		number_format($mRetPag[$nRP]['comvlr01'],0,',','.'),
																		number_format($mRetPag[$nRP]['comvlr02'],0,',','.'),
																		number_format($mRetPag[$nRP]['comvlrxx'],0,',','.'),
																		$mRetPag[$nRP]['commovxx']));
										$pyy = $pdf->getY();
									}
								}

							}//if($mCodDat[$k]['comctocx'] == "PCC" && $mCodDat[$k]['comidc2x'] == "P"){
							// $total += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
						}
					}

					$pdf->setXY($nPosX, $nPosFin+9);
					$pdf->MultiCell(205, 10, utf8_decode("OBSERVACIONES:  "). $mDatCab['comobsxx'], 1, 'L');
					// $pdf->Cell(25, 6, utf8_decode("CUENTA"), 1, 0, 'C', true);
				}
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
	}
?>

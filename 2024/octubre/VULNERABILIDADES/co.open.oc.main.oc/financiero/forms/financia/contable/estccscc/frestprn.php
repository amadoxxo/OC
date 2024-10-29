<?php
  namespace openComex;
  use FPDF;

  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");

  /**
   * Usuario
   */
  $qUsrNom  = "SELECT USRNOMXX FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" AND regestxx = \"ACTIVO\" LIMIT 0,1";
	$xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
	$xRUN = mysql_fetch_array($xUsrNom);
	$cUsrNom = $xRUN['USRNOMXX'];
  /**
   * Centro de costo
   */
  $qCenCos  = "SELECT ccodesxx FROM $cAlfa.fpar0116 WHERE ccoidxxx LIKE \"%$gCcoId%\" AND regestxx = \"ACTIVO\" LIMIT 0,1";
	$xCenCos = f_MySql("SELECT","",$qCenCos,$xConexion01,"");
	$xRBD = mysql_fetch_array($xCenCos);
	$cCcoDes = $xRBD['ccodesxx'];

  /**
   * Subcentro de costo
   */
  $qSubCen  = "SELECT sccdesxx FROM $cAlfa.fpar0120 WHERE ccoidxxx = \"$gCcoId\" AND sccidxxx LIKE \"%$gSccId%\" AND regestxx = \"ACTIVO\" LIMIT 0,1";
	$xSubCens = f_MySql("SELECT","",$qSubCen,$xConexion01,"");
	$xRSC = mysql_fetch_array($xCenCos);
	$cScoDes = $xRSC['sccdesxx'];
	
	$qDatDoi  = "SELECT ";
	$qDatDoi .= "$cAlfa.sys00121.regfcrex, ";
	$qDatDoi .= "$cAlfa.sys00121.sucidxxx, ";
	$qDatDoi .= "$cAlfa.sys00121.docidxxx, ";
	$qDatDoi .= "$cAlfa.sys00121.docsufxx  ";
	$qDatDoi .= "FROM $cAlfa.sys00121 ";
	$qDatDoi .= "WHERE ";
	$qDatDoi .= "$cAlfa.sys00121.ccoidxxx = \"$gCcoId\" AND ";
	$qDatDoi .= "$cAlfa.sys00121.docidxxx = \"$gSccId\" LIMIT 0,1";
	//f_Mensaje(__FILE__,__LINE__,$qDatDoi);
  $xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
  $xRDD = mysql_fetch_array($xDatDoi);

	$mCocDat1 = f_pyg_Do(substr($xRDD['regfcrex'],0,4),$gAnoHas,$gCcoId,$xRDD['sucidxxx'],$xRDD['docidxxx'],$xRDD['docsufxx']);
	$mCocDat  = f_Sort_Array_By_Field($mCocDat1,"DIGCUEXX","ASC_AZ");

	$mMovCom = array(); $i=0;
  $nBan = 0;
  $cMesIni = $gMesDes;
  $qCocDat = "";
 

  for($cAno=$gAnoDes;$cAno<=$gAnoHas;$cAno++){
  	if($cAno == ($gAnoDes+1)){
  		$cMesIni = "01";
	 	}
  	if($cAno < $gAnoHas){
  		$cMesFin = "12";
  		$cDiaFin = "31";
  	}else{
  		$cMesFin = $gMesHas;
  		$cDiaFin = date ('d', mktime (0, 0, 0, $cMesFin + 1, 0, $cAno));
  	}
		
  	/**Validacion en el Subcentro de Costo, en el caso que el primer digito sea cero [El primer digito del Subcentro indica el anio de creacion del Do, operativamente los Do's, no los crean con este cero],
  	 * se debe enviar para el select sin este cero para que genere la consulta del DO,
  	 * en el caso de los Comprobantes de Tipo F.
  	 */
  	$nSccId = 0;//Switch para incluir la busqueda por subcentro de costo con 0 y sin 0.
  	if(substr($gSccId,0,1) == '0'){
  		$gSccId = $gSccId;
  		$nSccId = 1;
  		$gSccId1 = substr($gSccId,1,strlen($gSccId));
  	}

  }
  $cRoot = $_SERVER['DOCUMENT_ROOT'];

  ##Switch para incluir fuente y clase pdf segun base de datos ##
  switch($cAlfa){
  	case "COLMASXX":
  		define('FPDF_FONTPATH',"../../../../../fonts/");
  		require("../../../../../forms/fpdf.php");
  	break;
  	default:
  		define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  		require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
  	break;
  }
  ##Fin Switch para incluir fuente y clase pdf segun base de datos ##

  class PDF extends FPDF {
		function Header() {
			global $cAlfa; global $cRoot; global $cPlesk_Skin_Directory;
			global $gCcoId; global $gSccId; global $gMesDes; global $gAnoDes; global $gMesHas; global $gAnoHas;
			global $cUsrNom; global $cCcoDes; global $cScoDes; global $nPag;
			/**
		   * Impirmiendo Encabezado
		   */
			$cNomRep="ESTADO DE RESULTADOS POR CENTRO DE COSTO Y SUBCENTRO DE COSTO";


			 	$this->SetXY(7,5);
		  	$this->Cell(32,18,'',1,0,'C');
		  	$this->Cell(168,18,'',1,0,'C');
		  	switch($cAlfa){
		  	 case "INTERLOG":
		  	 case "DESARROL":
		  	 case "PRUEBASX":
		  	 	$this->Image($cRoot.$cPlesk_Skin_Directory.'/MaryAire.jpg',8,6,30,15);
		  	 break;
				 case "ROLDANLO"://ROLDAN
	       case "TEROLDANLO"://ROLDAN
	       case "DEROLDANLO"://ROLDAN
        	$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',8,8,30,13);
         break;
		  	 case "COLMASXX":
		  	 	$this->Image($cRoot.$cPlesk_Skin_Directory.'/colmas.jpg',8,8,30,13);
		  	 break;
		  	}

				$this->SetFont('verdana','B',9);
			  $this->setXY(39,5);
			  $this->Cell(168,7,$cNomRep,0,0,'C'); // TITULO DEL FORMULARIO //
				$this->SetFont('verdana','B',7);
			  $this->Ln(5);
			  $this->setX(39);
			  $this->Cell(168,5,"CENTRO DE COSTO: [$gCcoId] {$cCcoDes}",0,0,'C');
			  if($gSccId <> ""){
				  $this->Ln(4);
				  $this->setX(39);
				  $this->Cell(168,5,"SUBCENTRO DE COSTO: [$gSccId] {$cScoDes}",0,0,'C');
			  }
			  $this->SetFont('verdana','',6);
				$this->Ln(5);
				$this->setX(39);
				$cDiaFin = date ('d', mktime (0, 0, 0, $gMesHas + 1, 0, $gAnoHas));
				$this->Cell(64,3,"DESDE: 01-$gMesDes-$gAnoDes HASTA: $cDiaFin-$gMesHas-$gAnoHas",0,0,'L');
				$this->Cell(64,3,"GENERADO POR: {$cUsrNom}",0,0,'L');
				$this->Cell(40,3,"PAGINA: ".$this->PageNo()." DE {nb}",0,0,'R');
		  	$this->setXY(7,25);

		  	if($nPag == 1){
			  	$this->SetFont('verdana','B',5);
			  	$this->Cell(20,3,"Comprobante",0,0,'L');
			  	$this->Cell(15,3,"Csc Dos",0,0,'L');
			    $this->Cell(13,3,"Fecha",0,0,'L');
			    $this->Cell(12,3,"Hora",0,0,'L');
			    $this->Cell(14,3,"Concepto",0,0,'L');
			    $this->Cell(27,3,"Descripcion",0,0,'L');
			    $this->Cell(30,3,"Tercero",0,0,'L');
			    $this->Cell(33,3,"Cliente",0,0,'L');
			    $this->Cell(20,3,"Debito",0,0,'R');
			    $this->Cell(20,3,"Credito",0,0,'R');
			    $this->Ln(3);
			    $this->setX(7);
		  	}

		}
		function Footer() {
		}

		function SetWidths($w) {
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
	    $h=2*$nb;
	    //Issue a page break first if needed
	    $this->CheckPageBreak($h);
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++){
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	        //Save the current position
	        $x=$this->GetX();
	        $y=$this->GetY();
	        //Draw the border
	        //$this->Rect($x,$y,$w,$h);
	        //Print the text
	        $this->MultiCell($w,2,$data[$i],0,$a);
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
  }

  $pdf = new PDF('P','mm','Letter');
	$pdf->AddFont('verdana','','');
	$pdf->AddFont('verdana','B','');
	$pdf->AliasNbPages();
	$pdf->SetMargins(0,0,0);
	$pdf->SetAutoPageBreak(true,10);
  $pdf->AddPage();

  /**
   * Imprimiendo datos
   */

  if(count($mCocDat)>0){
  	$pdf->SetFont('verdana','B',5);
  	$pdf->Cell(20,3,"Comprobante",0,0,'L');
  	$pdf->Cell(15,3,"Csc Dos",0,0,'L');
    $pdf->Cell(13,3,"Fecha",0,0,'L');
    $pdf->Cell(12,3,"Hora",0,0,'L');
    $pdf->Cell(14,3,"Concepto",0,0,'L');
    $pdf->Cell(27,3,"Descripcion",0,0,'L');
    $pdf->Cell(30,3,"Tercero",0,0,'L');
    $pdf->Cell(30,3,"Cliente",0,0,'L');
    $pdf->Cell(20,3,"Debito",0,0,'R');
    $pdf->Cell(20,3,"Credito",0,0,'R');

    $pdf->Ln(3);

    $pdf->SetFont('verdana','',5);
    $pdf->SetWidths(array(20,15,13,12,14,27,30,30,20,20));
		$pdf->SetAligns(array("L","L","L","L","L","L","L","L","R","R"));
		$nPag = 0;
		$nSumCueDeb = 0;
		$nSumCueCre = 0;
		$nDigCue = 0;
		$nBan = 0;
		
  	for($j=0;$j<count($mCocDat);$j++){
  		$nPag = 1;
  		
  		if ($mCocDat[$j]['commovxx']=='D') {
				$nTotDeb += $mCocDat[$j]['comvlrxx'];
				$nValDeb = $mCocDat[$j]['comvlrxx'];
				$nValCre = "";
			} else {
				$nTotCre += $mCocDat[$j]['comvlrxx'];
				$nValDeb = "";
				$nValCre = $mCocDat[$j]['comvlrxx'];
			}
		
			if($nDigCue == ""){
				$nDigCue = $mCocDat[$j]['DIGCUEXX'];
			}
			
  		switch($nDigCue){
				case "4":
					$cTilSub = "SUBTOTAL PARA LAS CUENTAS DE INGRESOS";
				break;
				case "5":
					$cTilSub = "SUBTOTAL PARA LAS CUENTAS DE GASTOS";
				break;
				case "6":
					$cTilSub = "SUBTOTAL PARA LAS CUENTAS DE COSTO DE VENTAS";
				break;
				case "7":
					$cTilSub = "SUBTOTAL PARA LAS CUENTAS DE COSTO DE PRODUCCION";
				break;
			}

			
			if($nDigCue == $mCocDat[$j]['DIGCUEXX']){
			
				$nSumCueDeb += $nValDeb;
				$nSumCueCre += $nValCre;
				
			}else{
				$pdf->SetTextColor(0);
				$pdf->Ln(3);
				$pdf->setX(7);
				$pdf->SetFont('verdana','B',6);
				$pdf->Cell(160,0,$cTilSub,0,0,'R');
				$pdf->Cell(20,0,number_format($nSumCueDeb,0,',','.'),0,0,'R');
				$pdf->Cell(20,0,number_format($nSumCueCre,0,',','.'),0,0,'R');
				$pdf->Ln(3);
		    $nDigCue = $mCocDat[$j]['DIGCUEXX'];
		    
				$nSumCueDeb = $nValDeb;
				$nSumCueCre = $nValCre;
				//$nBan = 1;
				$pdf->SetFont('verdana','',5);
			}

			$cNomPro = ""; $cNomCli = "";
			if($mCocDat[$j]['comidxxx'] == "D" || $mCocDat[$j]['comidxxx'] == "C" || $mCocDat[$j]['comidxxx'] == "N"){
				$cNomPro = $mCocDat[$j]['PRONOMXX']; $cNomCli = $mCocDat[$j]['CLINOMXX'];
				$mCocDat[$j]['PRONOMXX'] = $cNomCli;
				$mCocDat[$j]['CLINOMXX'] = $cNomPro;
				if($mCocDat[$j]['comidcxx'] == "F" && $mCocDat[$j]['comcodcx'] <>  "" && $mCocDat[$j]['comcsccx'] <> "" && $mCocDat[$j]['comseqcx'] <> ""){
					if ($mCocDat[$j]['DIGCUEXX']=='4') {
						if ($mCocDat[$j]['commovxx']=='D') {
							$pdf->SetTextColor(255,0,0);
						}else{
							$pdf->SetTextColor(180,82,205);
						}
					}else{
						if ($mCocDat[$j]['commovxx']=='D') {
							$pdf->SetTextColor(180,82,205);
						}else{
							$pdf->SetTextColor(255,0,0);
						}
					}
				}
			}else{
				$pdf->SetTextColor(0);
			}

		  $pdf->setX(7);
		  $Descrip = "";
		  
  	 	if (substr($mCocDat[$j]['ctodesx'.strtolower($mCocDat[$j]['comidxxx'])],0,30) == $mCocDat[$j]['ctodesxf'] && $mCocDat[$j]['ctodesxf'] == ""){
		    $Descrip = substr($mCocDat[$j]['serdesxx'],0,30);
		  }else{
		  	$Descrip = substr($mCocDat[$j]['ctodesx'.strtolower($mCocDat[$j]['comidxxx'])],0,30);
		  }
		  if ($Descrip == ""){
		  $Descrip = "COMPROBANTE SIN NOMBRE";
		  }
		  
		
			 $pdf->Row(array($mCocDat[$j]['comidxxx']."-".$mCocDat[$j]['comcodxx']."-".$mCocDat[$j]['comcscxx'],
							 				$mCocDat[$j]['comcsc2x'],
			 								$mCocDat[$j]['comfecxx'],
							 				$mCocDat[$j]['reghcrex'],
							 				$mCocDat[$j]['ctoidxxx'],
											substr($Descrip,0,20),
							 				substr($mCocDat[$j]['PRONOMXX'],0,20),
							 				substr($mCocDat[$j]['CLINOMXX'],0,20),
							 				number_format($nValDeb,0,',','.'),
							 				number_format($nValCre,0,',','.')));
		
  		//}
  	}//For
  	
  	$nPag = 0;
  	$pdf->SetTextColor(0);
  	
  	switch($nDigCue){
			case "4":
				$cTilSub = "SUBTOTAL PARA LAS CUENTAS DE INGRESOS";
			break;
			case "5":
				$cTilSub = "SUBTOTAL PARA LAS CUENTAS DE GASTOS";
			break;
			case "6":
				$cTilSub = "SUBTOTAL PARA LAS CUENTAS DE COSTOS DE VENTAS";
			break;
			case "6":
				$cTilSub = "SUBTOTAL PARA LAS CUENTAS DE COSTOS DE PRODUCCION";
			break;
		}
  	if($nSumCueDeb > 0 || $nSumCueCre > 0){
	  	$pdf->Ln(3);
			$pdf->setX(7);
			$pdf->SetFont('verdana','B',6);
			$pdf->Cell(160,0,$cTilSub,0,0,'R');
	    $pdf->Cell(20,0,number_format($nSumCueDeb,0,',','.'),0,0,'R');
	    $pdf->Cell(20,0,number_format($nSumCueCre,0,',','.'),0,0,'R');
	    $pdf->Ln(3);
  	}

		$nTotal = $nTotCre - $nTotDeb;
		if($nTotal > 0){
			$cTitulo = "UTILIDAD";
		}else{
			$cTitulo = "PERDIDA";
		}
		$pdf->Ln(3);
		$pdf->setX(7);
		$pdf->SetFont('verdana','B',6);
		$pdf->Cell(180,0,$cTitulo,0,0,'R');
    $pdf->Cell(20,0,number_format($nTotal,0,',','.'),0,0,'R');

  }else{
  	$pdf->setX(7);
		$pdf->Cell(200,3,"No se generaron registros, verifique los parametros de la consulta.",0,0,'L');
  }

  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";

	$pdf->Output($cFile);

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }

	echo "<html><script>document.location='$cFile';</script></html>";

?>
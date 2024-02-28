<?php

  include("../../../../libs/php/utility.php");
  include("../../../../libs/php/utiliqdo.php");
  include("../../../../libs/php/utimovdo.php");

	// ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb   = $kDf[3];
  $kUser      = $kDf[4];
  $kLicencia  = $kDf[5];
  $swidth     = $kDf[6];

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
			global $posy;

		}//Function Header

		function Footer() {

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
			$h=3*$nb;
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
				$this->MultiCell($w,3,$data[$i],0,$a);
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

  $pdf = new PDF('P','mm','Letter');  //Error al invocar la clase
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->SetFont('verdana','',8);
  $pdf->SetMargins(0,30,0);
  $pdf->SetAutoPageBreak(true,20);

  $cFec = date('Y-m-d');
  $cAno = substr($cFec,0,4);

  $pdf->AddPage();

  ##Impresión de Logo de ADIMPEX en la parte superior derecha##
  switch($cAlfa){
    case "TEADIMPEXX":
    case "DEADIMPEXX":
    case "ADIMPEXX":
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex5.jpg',192,00,25,20);
    break;
    case "ANDINOSX": //ANDINOSX
    case "TEANDINOSX": //ANDINOSX
    case "DEANDINOSX": //ANDINOSX
      $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoandinos.jpg', 12, $py + 4, 36, 15);
    break;
    default:
      // No hace nada
    break;
  }
  ##Fin Impresión de Logo de ADIMPEX en la parte superior derecha##

  //  Variable que Trae el # del $gDocId //
  /***** Tamaño de la Hoja **/
  $nAncho = 210;
  $nLargo = 280;
  $nCol   = 0;
  $nLetf  = 0;
  $nRight = 0;
  /**************************/

  /***** Defino Tamaños de Letra *****/
  $cFs_Titulo    = 10;
  $cFs_SubTitulo = 8;
  $cFs_Normal    = 5;
  /***** Fin Defino Tamaños de Letra *****/

  /***** Acumulador *****/
  $nSumTot = 0;
  /**********************/

  /***** Acumulador *****/
  $nInd_Com = 0;
  /**********************/

  //$zConnect = mysql_connect("localhost","root","fedoracore3");

  /***** Logo de Siaco *****/
  //$pdf->Image('../../../../plesk/graphics/standar/SIACO1.jpg',$nRow,0,$nAncho,35);
  /***** Fin de Logo de Siaco *****/

  /***** Imprime Titulo *****/

  /** Instanciando Objetos para buscar el movimiento del DO
      */
  $objMovDo = new cMovimientoDo();
  $vDatos['sucidxxx'] =  $gSucId; //sucusal
  $vDatos['docidxxx'] =  $gDocId; //Do
  $vDatos['docsufxx'] =  $gDocSuf; //sufijo
  $vDatos['imppygdo'] =  $gPyG; //imprimir PyG del DO

  $mRetorna = $objMovDo->fnDatosMovimientoDo($vDatos);
  $mDatos = array();
  $mDatos = $mRetorna[1];
  
  switch($cAlfa){
		case "ROLDANLO"://ROLDAN
    case "TEROLDANLO"://ROLDAN
    case "DEROLDANLO"://ROLDAN
	  	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/LogoRoldan.png',80,8,37,22);
      $posy = 30;
	  break;
    case "ADUANAMO": //ADUANAMO
    case "DEADUANAMO": //ADUANAMO
    case "TEADUANAMO": //ADUANAMO
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamo.jpg',92,6,30,19);
      $posy = 30;
	  break;
    case "CASTANOX":
    case "TECASTANOX":
    case "DECASTANOX":
	  	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomartcam.jpg',90,8,38,23);
      $posy = 30;
	  break;
    case "ALMACAFE": //ALMACAFE
    case "TEALMACAFE": //ALMACAFE
    case "DEALMACAFE": //ALMACAFE
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalmacafe.jpg',90,8,35,15);
      $posy = 30;
    break;
    case "TEADIMPEXX": // ADIMPEX
    case "DEADIMPEXX": // ADIMPEX
    case "ADIMPEXX": // ADIMPEX
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex4.jpg',83,10,50,11);
      $posy = 30;
    break;
    case "GRUMALCO": //GRUMALCO
    case "TEGRUMALCO": //GRUMALCO
    case "DEGRUMALCO": //GRUMALCO
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',90,8,35,19);
      $posy = 30;
    break;
    case "ALADUANA": //ALADUANA
    case "TEALADUANA": //ALADUANA
    case "DEALADUANA": //ALADUANA
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',90,9,35,19);
      $posy = 30;
    break;
    case "GRUPOALC": //GRUPOALC
    case "TEGRUPOALC": //GRUPOALC
    case "DEGRUPOALC": //GRUPOALC
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalc.jpg',90,9,40,19);
      $posy = 30;
    break;
    case "AAINTERX": //AAINTERX
    case "TEAAINTERX": //AAINTERX
    case "DEAAINTERX": //AAINTERX
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointernacional.jpg',90,9,35,19);
      $posy = 30;
    break;
    case "AALOPEZX":
    case "TEAALOPEZX":
    case "DEAALOPEZX":
    	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaalopez.png',90,3,25);
      $posy = 18;
    break;
    case "ADUAMARX": //ADUAMARX
    case "TEADUAMARX": //ADUAMARX
    case "DEADUAMARX": //ADUAMARX
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg',95,8,22);
      $posy = 30;
		break;
		case "SOLUCION": //SOLUCION
		case "TESOLUCION": //SOLUCION
		case "DESOLUCION": //SOLUCION
			$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',88,9,45);
			$posy = 30;
		break;
		case "FENIXSAS": //FENIXSAS
		case "TEFENIXSAS": //FENIXSAS
		case "DEFENIXSAS": //FENIXSAS
			$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg', 85, 10, 45);
			$posy = 30;
    break;
    case "COLVANXX": //COLVANXX
    case "TECOLVANXX": //COLVANXX
    case "DECOLVANXX": //COLVANXX
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logocolvan.jpg', 85, 7, 45);
      $posy = 30;
    break;
    case "INTERLAC": //INTERLAC
    case "TEINTERLAC": //INTERLAC
    case "DEINTERLAC": //INTERLAC
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointerlace.jpg', 85, 6, 45);
      $posy = 30;
    break;
		case "DHLEXPRE": //DHLEXPRE
		case "TEDHLEXPRE": //DHLEXPRE
		case "DEDHLEXPRE": //DHLEXPRE
			$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',90,8,35,19);
			$posy = 30;
		break;
    case "KARGORUX": //KARGORUX
    case "TEKARGORUX": //KARGORUX
    case "DEKARGORUX": //KARGORUX
      $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logokargoru.jpg', 90, 8, 35, 19);
      $posy = 30;
    break;
    case "ALOGISAS": //LOGISTICA
    case "TEALOGISAS": //LOGISTICA
    case "DEALOGISAS": //LOGISTICA
      $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 80, 8, 55);
      $posy = 30;
    break;
    case "PROSERCO": //PROSERCO
    case "TEPROSERCO": //PROSERCO
    case "DEPROSERCO": //PROSERCO
      $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoproserco.png', 90, 6, 40);
      $posy = 30;
    break;
    case "MANATIAL": //MANATIAL
    case "TEMANATIAL": //MANATIAL
    case "DEMANATIAL": //MANATIAL
      $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomanantial.jpg', 90, 6, 40);
      $posy = 30;
    break;
    case "DSVSASXX":    //DSVSAS
    case "DEDSVSASXX":  //DSVSAS
    case "TEDSVSASXX":  //DSVSAS
      $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logodsv.jpg', 90, 6, 40);
      $posy = 30;
    break;
    case "MELYAKXX":    //MELYAK
    case "DEMELYAKXX":  //MELYAK
    case "TEMELYAKXX":  //MELYAK
      $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomelyak.jpg', 90, 6, 40);
      $posy = 30;
    break;
    case "FEDEXEXP":    //FEDEX
    case "DEFEDEXEXP":  //FEDEX
    case "TEFEDEXEXP":  //FEDEX
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofedexexp.jpg',90,6,40);
      $posy = 30;
    break;
    case "EXPORCOM":    //EXPORCOMEX
    case "DEEXPORCOM":  //EXPORCOMEX
    case "TEEXPORCOM":  //EXPORCOMEX
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoexporcomex.jpg',90,6,40);
      $posy = 30;
    break;
		case "HAYDEARX":   //HAYDEARX
		case "DEHAYDEARX": //HAYDEARX
    case "TEHAYDEARX": //HAYDEARX
      $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logohaydear.jpeg',70,3,65,25);
      $posy = 30;
    break;
    case "CONNECTA":   //CONNECTA
    case "DECONNECTA": //CONNECTA
    case "TECONNECTA": //CONNECTA
      $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoconnecta.jpg', 12,8,35,18);
      $posy = 30;
    break;
		default:
      $posy = 15;
		break;
	}

	$cImprime = "MOVIMIENTO DE DO NUMERO ".trim($mDatos['sucidxxx'])."-".trim($mDatos['doctipxx'])."-".trim($mDatos['docidxxx'])."-".$mDatos['docsufxx']." DEL ".$mDatos['regfcrex'];
  $pdf->setXY(10,$posy);
  $pdf->SetFont('verdanab','',$cFs_Titulo);
  $pdf->Cell(200,8,$cImprime,0,0,'C');
  /***** Fin de Imprime Titulo *****/



  /***** Usuario *****/

  $pdf->Ln(6);
  $pdf->setX(10);
  $pdf->SetFont('verdana','',$cFs_Normal);
  $cImprime = "FECHA : ".date("Y-m-d")." - HORA : ".date("H:i")." - USUARIO : ".substr($mDatos['usrnomxx'],0,30);
  $pdf->Cell(200,4,$cImprime,0,0,'C');
  /***** Fin Usuario *****/
  /**** Documento de transpote ****/

  $pdf->Ln(6);
  $pdf->setX(10);
  $pdf->SetFont('verdanab','',$cFs_SubTitulo);
  $pdf->Cell(17,4,"PEDIDO: ",0,0,'L');
  $pdf->SetFont('verdana','',$cFs_SubTitulo);
  $pdf->Cell(183,4,substr($mDatos['docpedxx'],0,86),0,0,'L');

  if ($mDatos['dgedtxxx'] != "") {
	  $pdf->Ln(4);
	  $pdf->setX(10);
	  $pdf->SetFont('verdanab','',$cFs_SubTitulo);
	  $pdf->Cell(51,4,"DOCUMENTO DE TRANSPORTE: ",0,0,'L');
	  $pdf->SetFont('verdana','',$cFs_SubTitulo);
	  $pdf->Cell(169,4,$mDatos['dgedtxxx'],0,0,'L');
  }
  /***** Cliente *****/

  $nRound = 0; //Redondeo de los valores
  $pdf->Ln(4);
  $pdf->setX(10);
  $pdf->SetFont('verdanab','',$cFs_SubTitulo);
  $pdf->Cell(17,4,"CLIENTE:",0,0,'L');
  $pdf->SetFont('verdana','',$cFs_SubTitulo);
  $pdf->Cell(183,4,substr(trim($mDatos['clinomxx']),0,80)." (".$mDatos['cliidxxx']."-".f_Digito_Verificacion($mDatos['cliidxxx']).")",0,0,'L');

	/*SUCURSAL COMERCIAL, CENTRO DE COSTO, SUBCENTRO DE COSTO, MODO DE TRANSPORTE Y ESTADO*/
	$pdf->Ln(4);
	$pdf->setX(10);

	$pdf->SetFont('verdanab','',$cFs_SubTitulo);
	$pdf->Cell(40,4,"MODO DE TRANSPORTE:",0,0,'L');
	$pdf->SetFont('verdana','',$cFs_SubTitulo);
	$pdf->Cell(30,4,$mDatos['docmtrxx'],0,0,'L');

	$pdf->Ln(4);
	$pdf->setX(10);

	/*Estado*/
	$pdf->SetFont('verdanab','',$cFs_SubTitulo);
	$pdf->Cell(15,4,"ESTADO:",0,0,'L');
	$pdf->SetFont('verdana','',$cFs_SubTitulo);
	$pdf->Cell(30,4,$mDatos['regestxx'],0,0,'L');

	if ($mDatos['succomxx']) {
		$pdf->SetFont('verdanab','',$cFs_SubTitulo);
		$pdf->Cell(40,4,"SUCURSAL COMERCIAL:",0,0,'L');
		$pdf->SetFont('verdana','',$cFs_SubTitulo);
		$pdf->Cell(20,4,$mDatos['succomxx'],0,0,'L');
	}

	$pdf->SetFont('verdanab','',$cFs_SubTitulo);
	$pdf->Cell(8,4,"CC:",0,0,'L');
	$pdf->SetFont('verdana','',$cFs_SubTitulo);
	$pdf->Cell(30,4,$mDatos['ccoidxxx'],0,0,'L');

	if ($mDatos['sccidxxx']) {
		$pdf->SetFont('verdanab','',$cFs_SubTitulo);
		$pdf->Cell(8,4,"SC:",0,0,'L');
		$pdf->SetFont('verdana','',$cFs_SubTitulo);
		$pdf->Cell(30,4,$mDatos['sccidxxx'],0,0,'L');
	}

	/*TIPO DE FACTURACION*/
	$pdf->Ln(4);
	$pdf->setX(10);
	$pdf->SetFont('verdanab','',$cFs_SubTitulo);
	$pdf->Cell(40,4,"TIPO DE FACTURACION:",0,0,'L');
	$pdf->SetFont('verdana','',$cFs_SubTitulo);
	$pdf->Cell(166,4,$mDatos['docfmaxx'],0,0,'L');

	/*  DOs DE REGISTRO ASOCIADOS*/
	if ($mDatos['docdosre'] != "" ) {
		$pdf->Ln(4);
		$pdf->setX(10);
		$pdf->SetFont('verdanab','',$cFs_SubTitulo);
		$pdf->Cell(52,4,"DOs DE REGISTRO ASOCIADOS:",0,0,'L');
		$pdf->SetFont('verdana','',$cFs_SubTitulo);
		$pdf->Cell(166,4,str_replace("~",", ",$mDatos['docdosre']),0,0,'L');
	}

  /****** Grupo Tarifa ****/
	$pdf->Ln(4);
	$pdf->setX(10);
	$pdf->SetFont('verdanab','',$cFs_SubTitulo);
	$pdf->Cell(34,4,"GRUPO DE TARIFA:",0,0,'L');
	$pdf->SetFont('verdana','',$cFs_SubTitulo);
	$pdf->Cell(166,4,$mDatos['gtaidxxx'],0,0,'L');

	$pdf->Ln(4);
	$pdf->setX(10);
	$pdf->SetFont('verdanab','',$cFs_SubTitulo);
	$pdf->Cell(58,4,"TIPO DE TARIFA ESPECIFICA POR: ",0,0,'L');
	$pdf->SetFont('verdana','',$cFs_SubTitulo);
	$pdf->Cell(142,4,$mDatos['doctepid'],0,0,'L');

  /***** Director de Cuenta *****/
  $pdf->Ln(4);
  $cImprime = "DIRECTOR DE CUENTA : ".$mDatos['diridxxx']." - ".substr(trim($mDatos['dirnomxx']),0,60);
  $pdf->setX(10);
  $pdf->SetFont('verdana','',$cFs_Normal);
  $pdf->Cell(200,3,$cImprime,0,0,'L');
  /***** Fin de Director de Cuenta *****/

  $pdf->Ln(4);
  $pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());

  /***** Recibos de Caja *****/
  $pdf->Ln(7);
  $cImprime = "ANTICIPOS RECIBIDOS";
  $pdf->setX($nLetf+10);
  $pdf->SetFont('verdanab','',$cFs_SubTitulo);
  //$pdf->SetTextColor(255,0,0);  //Rojo
  $pdf->Cell(strlen($cImprime),0,$cImprime,0,0,'L');
  //$pdf->SetTextColor(0,0,0);   //Negro

  $pdf->Ln(4);
  $pdf->SetFont('verdanab','',$cFs_Normal);
  $pdf->setX(10);
  $pdf->Cell(24,0,"Comprobante",0,0,'L');
  $pdf->Cell(12,0,($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') ? "Csc Tres" : "Csc Dos",0,0,'L');
  $pdf->Cell(12,0,"Fecha",0,0,'L');
  $pdf->Cell(10,0,"Hora",0,0,'L');
  	if ($gMov == 'CUENTA') {
      $pdf->Cell(13,0,"Cuenta",0,0,'L');
    } else {
      $pdf->Cell(13,0,"Concepto",0,0,'L');
    }
  $pdf->Cell(30,0,"Descripcion",0,0,'L');
  $pdf->Cell(30,0,"Tercero",0,0,'L');
  $pdf->Cell(26,0,"cliente",0,0,'L');
  $pdf->Cell(20,0,"Valor",0,0,'C');
  $pdf->Cell(20,0,"Factura",0,0,'C');
  $pdf->Ln(2);

  $pdf->SetFont('verdana','',$cFs_Normal);
  ///////////////////////////////////////////////////////////
  /// contador de registros Anticipos Recibos de Caja
  $nCont_ARC = 0;
  ///////////////////////////////////////////////////////////
  for($e=0; $e<count($mDatos['anticipo']); $e++){

    $nCont_ARC++;
    $pdf->Ln(2);
    $pdf->setX(10);

    $pdf->Cell(24,0,str_pad(trim($mDatos['anticipo'][$e]['secmovxx']),3,0,STR_PAD_LEFT).") ".trim($mDatos['anticipo'][$e]['comidxxx'])."-".str_pad(trim($mDatos['anticipo'][$e]['comcodxx']),3,0,STR_PAD_LEFT)."-".str_pad(trim($mDatos['anticipo'][$e]['comcscxx']),10,0,STR_PAD_LEFT),0,0,'L');
    $pdf->Cell(12,0,trim($mDatos['anticipo'][$e]['comcsc2x']),0,0,'L');
    $pdf->Cell(12,0,trim($mDatos['anticipo'][$e]['comfecxx']),0,0,'L');
    $pdf->Cell(10,0,trim($mDatos['anticipo'][$e]['reghcrex']),0,0,'L');
    if ($gMov == 'CUENTA') {
      $pdf->Cell(13,0,trim($mDatos['anticipo'][$e]['pucidxxx']),0,0,'L');
	  } else {
	    $pdf->Cell(13,0,trim($mDatos['anticipo'][$e]['ctoidxxx']),0,0,'L');
	  }
    $pdf->Cell(30,0,substr($mDatos['anticipo'][$e]['comdesxx'],0,25),0,0,'L');
    $pdf->Cell(30,0,substr($mDatos['anticipo'][$e]['clinomxx'],0,25),0,0,'L');
    $pdf->Cell(26,0,substr($mDatos['anticipo'][$e]['pronomxx'],0,25),0,0,'L');
    $pdf->Cell(18,0,((strpos((abs($mDatos['anticipo'][$e]['comvlrxx'])+0),'.') > 0) ? number_format(($mDatos['anticipo'][$e]['comvlrxx']+0),2,',','.') : number_format(($mDatos['anticipo'][$e]['comvlrxx']+0),0,',','.')),0,0,'R');
    $pdf->Cell(2,0,$mDatos['anticipo'][$e]['commovxx'],0,0,'C');
    $pdf->Cell(20,0,$mDatos['anticipo'][$e]['comfacxx'],0,0,'L');
  }
  if ($nCont_ARC == 0) {
    $nLetf = 0;
    $pdf->Ln(5);
    $cImprime = "NO HAY ANTICIPOS PARA ESTE DO";
    $pdf->setX($nLetf+10);
    $pdf->SetFont('verdana','',$cFs_Normal);
    $pdf->Cell(50,0,$cImprime,0,0,'L');
  }

  ////////////////////////////////////////////////
  /***** Fin de Recibos de Caja *****/

  /***** Pagos por Cuenta del Cliente *****/
  $nLetf = 0;
  $pdf->Ln(7);
  $cImprime = "PAGOS POR CUENTA DEL CLIENTE";
  $pdf->setX($nLetf+10);
  $pdf->SetFont('verdanab','',$cFs_SubTitulo);
  $pdf->Cell(strlen($cImprime),0,$cImprime,0,0,'L');

 	$pdf->Ln(5);
 	$pdf->SetFont('verdanab','',$cFs_Normal);
  $pdf->setX(10);
  $pdf->Cell(24,0,"Comprobante",0,0,'L');
  $pdf->Cell(12,0,($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') ? "Csc Tres" : "Csc Dos",0,0,'L');
  $pdf->Cell(12,0,"Fecha",0,0,'L');
  $pdf->Cell(10,0,"Hora",0,0,'L');
  if ($gMov == 'CUENTA') {
    $pdf->Cell(13,0,"Cuenta",0,0,'L');
  } else{
    $pdf->Cell(13,0,"Concepto",0,0,'L');
  }
  $pdf->Cell(30,0,"Descripcion",0,0,'L');
  $pdf->Cell(30,0,"Tercero",0,0,'L');
  $pdf->Cell(26,0,"Cliente",0,0,'L');
  $pdf->Cell(20,0,"Valor",0,0,'C');
  $pdf->Cell(20,0,"Factura",0,0,'C');

  $pdf->Ln(2);
  $pdf->SetFont('verdana','',$cFs_Normal);

  $zSw_Reg = 0;
  for($e=0; $e<count($mDatos['pccxxxxx']); $e++){
    $zSw_Reg++;

    $pdf->Ln(2);
    $pdf->setX(10);

    $pdf->Cell(24,0,str_pad(trim($mDatos['pccxxxxx'][$e]['secmovxx']),3,0,STR_PAD_LEFT).") ".trim($mDatos['pccxxxxx'][$e]['comidxxx'])."-".str_pad(trim($mDatos['pccxxxxx'][$e]['comcodxx']),3,0,STR_PAD_LEFT)."-".str_pad(trim($mDatos['pccxxxxx'][$e]['comcscxx']),10,0,STR_PAD_LEFT),0,0,'L');
    $pdf->Cell(12,0,trim($mDatos['pccxxxxx'][$e]['comcsc2x']),0,0,'L');
    $pdf->Cell(12,0,trim($mDatos['pccxxxxx'][$e]['comfecxx']),0,0,'L');
    $pdf->Cell(10,0,trim($mDatos['pccxxxxx'][$e]['reghcrex']),0,0,'L');
  	if ($gMov == 'CUENTA') {
      $pdf->Cell(13,0,trim($mDatos['pccxxxxx'][$e]['pucidxxx']),0,0,'L');
    } else {
      $pdf->Cell(13,0,trim($mDatos['pccxxxxx'][$e]['ctoidxxx']),0,0,'L');
    }
    $pdf->Cell(30,0,substr($mDatos['pccxxxxx'][$e]['comdesxx'],0,25),0,0,'L');
    $pdf->Cell(30,0,substr($mDatos['pccxxxxx'][$e]['pronomxx'],0,25),0,0,'L');
    $pdf->Cell(26,0,substr($mDatos['pccxxxxx'][$e]['clinomxx'],0,25),0,0,'L');
    $pdf->Cell(18,0,((strpos((abs($mDatos['pccxxxxx'][$e]['comvlrxx'])+0),'.') > 0) ? number_format(($mDatos['pccxxxxx'][$e]['comvlrxx']+0),2,',','.') : number_format(($mDatos['pccxxxxx'][$e]['comvlrxx']+0),0,',','.')),0,0,'R');
    $pdf->Cell(2,0,$mDatos['pccxxxxx'][$e]['commovxx'],0,0,'C');
    $pdf->Cell(20,0,$mDatos['pccxxxxx'][$e]['comfacxx'],0,0,'L');
  }
  if ($zSw_Reg  == 0) {
  	$nLetf = 0;
  	$pdf->Ln(5);
  	$cImprime = "NO HAY PAGOS POR CUENTA DEL CLIENTE PARA ESTE DO";
  	$pdf->setX($nLetf+10);
  	$pdf->SetFont('verdana','',$cFs_Normal);
  	$pdf->Cell(70,0,$cImprime,0,0,'L');

  }
  /***** Fin de Pagos por Cuenta del Cliente *****/

  /***** Anticipos sin Legalizar y Recibos Provicionales de Caja Nenor *****/
  $nLetf = 0;
  $pdf->Ln(7);
  $cImprime = "ANTICIPOS SIN LEGALIZAR Y RECIBOS PROVISIONALES DE CAJA MENOR";
  $pdf->setX($nLetf+10);
  $pdf->SetFont('verdanab','',$cFs_SubTitulo);
  //$pdf->SetTextColor(255,0,0);  //Rojo
  $pdf->Cell(strlen($cImprime),0,$cImprime,0,0,'L');
  //$pdf->SetTextColor(0,0,0);   //Negro

  //$zSw_ImpTit = 0;
  $zSw_Prv = 0;
  $zSw_Reg = 0;

  for($e=0; $e<count($mDatos['antycmsl']); $e++){
    $zSw_Reg++;

    /**
     * Son anticipos sin legalizar unicamente las G-XXX que tienen saldo en la CxP, porque despues de que
     * se hace la P-28 y se cruza contra la G-XXX el saldo de esta desaparece de la CxP.
     */

    if ($zSw_Prv == 0) {
      $zSw_Prv++;

      $pdf->Ln(4);
      $pdf->SetFont('verdanab','',$cFs_Normal);
      $pdf->setX(10);
      $pdf->Cell(24,0,"Comprobante",0,0,'L');
      $pdf->Cell(12,0,($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') ? "Csc Tres" : "Csc Dos",0,0,'L');
      $pdf->Cell(12,0,"Fecha",0,0,'L');
      $pdf->Cell(10,0,"Hora",0,0,'L');
      if ($gMov == 'CUENTA') {
        $pdf->Cell(13,0,"Cuenta",0,0,'L');
      } else {
        $pdf->Cell(13,0,"Concepto",0,0,'L');
      }
      $pdf->Cell(30,0,"Descripcion",0,0,'L');
      $pdf->Cell(30,0,"Tercero",0,0,'L');
      $pdf->Cell(26,0,"Cliente",0,0,'L');
      $pdf->Cell(20,0,"Valor",0,0,'C');
      $pdf->Cell(20,0,"Factura",0,0,'C');
      $pdf->Ln(2);
  	}

  	$pdf->SetFont('verdana','',$cFs_Normal);

  	$pdf->Ln(2);
  	$pdf->setX(10);

  	$pdf->Cell(24,0,str_pad(trim($mDatos['antycmsl'][$e]['secmovxx']),3,0,STR_PAD_LEFT).") ".trim($mDatos['antycmsl'][$e]['comidxxx'])."-".str_pad(trim($mDatos['antycmsl'][$e]['comcodxx']),3,0,STR_PAD_LEFT)."-".str_pad(trim($mDatos['antycmsl'][$e]['comcscxx']),10,0,STR_PAD_LEFT),0,0,'L');
    $pdf->Cell(12,0,trim($mDatos['antycmsl'][$e]['comcsc2x']),0,0,'L');
    $pdf->Cell(12,0,trim($mDatos['antycmsl'][$e]['comfecxx']),0,0,'L');
    $pdf->Cell(10,0,trim($mDatos['antycmsl'][$e]['reghcrex']),0,0,'L');
  	if ($gMov == 'CUENTA') {
      $pdf->Cell(13,0,trim($mDatos['antycmsl'][$e]['pucidxxx']),0,0,'L');
    } else {
      $pdf->Cell(13,0,trim($mDatos['antycmsl'][$e]['ctoidxxx']),0,0,'L');
    }
    $pdf->Cell(30,0,substr($mDatos['antycmsl'][$e]['comdesxx'],0,25),0,0,'L');
    $pdf->Cell(30,0,substr($mDatos['antycmsl'][$e]['pronomxx'],0,25),0,0,'L');
    $pdf->Cell(26,0,substr($mDatos['antycmsl'][$e]['clinomxx'],0,25),0,0,'L');
    $pdf->Cell(18,0,((strpos((abs($mDatos['antycmsl'][$e]['comvlrxx'])+0),'.') > 0) ? number_format(($mDatos['antycmsl'][$e]['comvlrxx']+0),2,',','.') : number_format(($mDatos['antycmsl'][$e]['comvlrxx']+0),0,',','.')),0,0,'R');
    $pdf->Cell(2,0,$mDatos['antycmsl'][$e]['commovxx'],0,0,'C');
    $pdf->Cell(20,0,$mDatos['antycmsl'][$e]['comfacxx'],0,0,'L');
  }
  if($zSw_Reg  == 0) {
    $nLetf = 0;
    $pdf->Ln(5);
    $cImprime = "NO HAY ANTICIPOS SIN LEGALIZAR NI RECIBOS PROVISIONALES DE CAJA MENOR";
    $pdf->setX($nLetf+10);
    $pdf->SetFont('verdana','',$cFs_Normal);
    $pdf->Cell(100,0,$cImprime,0,0,'L');
  }
  /***** Fin de Anticipos sin Legalizar y Recibos Provicionales de Caja Nenor *****/

	//Sandra Guerrero. Bloque para DO Informativo 27-09-2011
  /***** Anticipos sin Legalizar y Recibos Provicionales de Caja Nenor *****/
  $nLetf = 0;
  $pdf->Ln(7);
  $cImprime = "DO INFORMATIVO SIN LEGALIZAR";
  $pdf->setX($nLetf+10);
  $pdf->SetFont('verdanab','',$cFs_SubTitulo);
  //$pdf->SetTextColor(255,0,0);  //Rojo
  $pdf->Cell(strlen($cImprime),0,$cImprime,0,0,'L');
  //$pdf->SetTextColor(0,0,0);   //Negro

  //$zSw_ImpTit = 0;
  $zSw_Prv = 0;
  $zSw_Reg = 0;

  for($e=0; $e<count($mDatos['doinfslx']); $e++){
    $zSw_Reg++;

    /**
    * Son anticipos sin legalizar unicamente las G-XXX que tienen saldo en la CxP, porque despues de que
    * se hace la P-28 y se cruza contra la G-XXX el saldo de esta desaparece de la CxP.
    */
    if ($zSw_Prv == 0) {
      $zSw_Prv++;

      $pdf->Ln(4);
      $pdf->SetFont('verdanab','',$cFs_Normal);
      $pdf->setX(10);
      $pdf->Cell(24,0,"Comprobante",0,0,'L');
      $pdf->Cell(12,0,($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') ? "Csc Tres" :"Csc Dos",0,0,'L');
      $pdf->Cell(12,0,"Fecha",0,0,'L');
      $pdf->Cell(10,0,"Hora",0,0,'L');
  		if ($gMov == 'CUENTA') {
        $pdf->Cell(13,0,"Cuenta",0,0,'L');
      } else {
        $pdf->Cell(13,0,"Concepto",0,0,'L');
      }
      $pdf->Cell(30,0,"Descripcion",0,0,'L');
      $pdf->Cell(30,0,"Tercero",0,0,'L');
      $pdf->Cell(26,0,"Cliente",0,0,'L');
      $pdf->Cell(20,0,"Valor",0,0,'C');
      $pdf->Cell(20,0,"CxP o CxC",0,0,'C');
      $pdf->Ln(2);
    }

    $pdf->SetFont('verdana','',$cFs_Normal);
    $pdf->Ln(2);
    $pdf->setX(10);

    $pdf->Cell(24,0,str_pad(trim($mDatos['doinfslx'][$e]['secmovxx']),3,0,STR_PAD_LEFT).") ".trim($mDatos['doinfslx'][$e]['comidxxx'])."-".str_pad(trim($mDatos['doinfslx'][$e]['comcodxx']),3,0,STR_PAD_LEFT)."-".str_pad(trim($mDatos['doinfslx'][$e]['comcscxx']),10,0,STR_PAD_LEFT),0,0,'L');
    $pdf->Cell(12,0,trim($mDatos['doinfslx'][$e]['comcsc2x']),0,0,'L');
    $pdf->Cell(12,0,trim($mDatos['doinfslx'][$e]['comfecxx']),0,0,'L');
    $pdf->Cell(10,0,trim($mDatos['doinfslx'][$e]['reghcrex']),0,0,'L');
  	if ($gMov == 'CUENTA') {
      $pdf->Cell(13,0,trim($mDatos['doinfslx'][$e]['pucidxxx']),0,0,'L');
    } else {
      $pdf->Cell(13,0,trim($mDatos['doinfslx'][$e]['ctoidxxx']),0,0,'L');
    }
    $pdf->Cell(30,0,substr($mDatos['doinfslx'][$e]['comdesxx'],0,25),0,0,'L');
    $pdf->Cell(30,0,substr($mDatos['doinfslx'][$e]['pronomxx'],0,25),0,0,'L');
    $pdf->Cell(26,0,substr($mDatos['doinfslx'][$e]['clinomxx'],0,25),0,0,'L');
    $pdf->Cell(18,0,((strpos((abs($mDatos['doinfslx'][$e]['comvlrxx'])+0),'.') > 0) ? number_format(($mDatos['doinfslx'][$e]['comvlrxx']+0),2,',','.') : number_format(($mDatos['doinfslx'][$e]['comvlrxx']+0),0,',','.')),0,0,'R');
    $pdf->Cell(2,0,$mDatos['doinfslx'][$e]['commovxx'],0,0,'C');
		$pdf->Cell(18,0,((strpos((abs($mDatos['doinfslx'][$e]['cxcocxpx'])+0),'.') > 0) ? number_format(($mDatos['doinfslx'][$e]['cxcocxpx']+0),2,',','.') : number_format(($mDatos['doinfslx'][$e]['cxcocxpx']+0),0,',','.')),0,0,'R');
  }
  if ($zSw_Reg  == 0) {
  	$nLetf = 0;
  	$pdf->Ln(5);
  	$cImprime = "NO HAY DOS INFORMATIVO SIN LEGALIZAR";
  	$pdf->setX($nLetf+10);
  	$pdf->SetFont('verdana','',$cFs_Normal);
  	$pdf->Cell(100,0,$cImprime,0,0,'L');
  }
  /***** Fin de Anticipos sin Legalizar y Recibos Provicionales de Caja Nenor *****/
  //Sandra Guerrero. Fin Bloque para DO Informativo 27-09-2011

  /***** Facturas del DO *****/
  //Si es vacio, es porque el reporte se genero desde el movimiento del DO
  if($gMosIng == 1 || $gMosIng == ""){
    $nLetf = 0;
    $pdf->Ln(7);
    $cImprime = "FACTURAS DEL DO";
    $pdf->setX($nLetf+10);
    $pdf->SetFont('verdanab','',$cFs_SubTitulo);
    $pdf->Cell(strlen($cImprime),0,$cImprime,0,0,'L');

  	$pdf->Ln(4);
  	$pdf->SetFont('verdanab','',$cFs_Normal);
    $pdf->setX(10);
    $pdf->Cell(24,0,"Comprobante",0,0,'L');
    $pdf->Cell(12,0,($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') ? "Csc Tres" : "Csc Dos",0,0,'L');
    $pdf->Cell(12,0,"Fecha",0,0,'L');
    $pdf->Cell(10,0,"Hora",0,0,'L');
    if ($gMov == 'CUENTA') {
      $pdf->Cell(13,0,"Cuenta",0,0,'L');
    } else {
      $pdf->Cell(13,0,"Concepto",0,0,'L');
    }
    $pdf->Cell(30,0,"Descripcion",0,0,'L');
    $pdf->Cell(30,0,"Tercero",0,0,'L');
    $pdf->Cell(26,0,"Cliente",0,0,'L');
    $pdf->Cell(20,0,"Valor",0,0,'C');
    $pdf->Cell(20,0,"Factura",0,0,'C');
    $pdf->Ln(2);

    $nFactu = 0;

    $pdf->SetFont('verdana','',$cFs_Normal);

    for($e=0; $e<count($mDatos['facdoxxx']); $e++){
      $nFactu++;
  	  $nLetf = 0;

  	  $pdf->Ln(2);
  	  $pdf->setX(10);

  	  $pdf->Cell(24,0,str_pad(trim($mDatos['facdoxxx'][$e]['secmovxx']),3,0,STR_PAD_LEFT).") ".trim($mDatos['facdoxxx'][$e]['comidxxx'])."-".str_pad(trim($mDatos['facdoxxx'][$e]['comcodxx']),3,0,STR_PAD_LEFT)."-".str_pad(trim($mDatos['facdoxxx'][$e]['comcscxx']),10,0,STR_PAD_LEFT),0,0,'L');
  	  $pdf->Cell(12,0,trim($mDatos['facdoxxx'][$e]['comcsc2x']),0,0,'L');
  	  $pdf->Cell(12,0,trim($mDatos['facdoxxx'][$e]['comfecxx']),0,0,'L');
  	  $pdf->Cell(10,0,trim($mDatos['facdoxxx'][$e]['reghcrex']),0,0,'L');
  		if ($gMov == 'CUENTA') {
  	    $pdf->Cell(13,0,trim($mDatos['facdoxxx'][$e]['pucidxxx']),0,0,'L');
  	  } else {
  	    $pdf->Cell(13,0,trim($mDatos['facdoxxx'][$e]['ctoidxxx']),0,0,'L');
  	  }
  	  $pdf->Cell(30,0,substr($mDatos['facdoxxx'][$e]['comdesxx'],0,25),0,0,'L');
  	  $pdf->Cell(30,0,substr($mDatos['facdoxxx'][$e]['clinomxx'],0,25),0,0,'L');
  	  $pdf->Cell(26,0,substr($mDatos['facdoxxx'][$e]['pronomxx'],0,25),0,0,'L');
  	  $pdf->Cell(18,0,((strpos((abs($mDatos['facdoxxx'][$e]['comvlrxx'])+0),'.') > 0) ? number_format(($mDatos['facdoxxx'][$e]['comvlrxx']+0),2,',','.') : number_format(($mDatos['facdoxxx'][$e]['comvlrxx']+0),0,',','.')),0,0,'R');
  	  $pdf->Cell(2,0,$mDatos['facdoxxx'][$e]['commovxx'],0,0,'C');
  	  $pdf->Cell(20,0,$mDatos['facdoxxx'][$e]['comfacxx'],0,0,'L');
    }
    if ($nFactu == 0){
    	$pdf->Ln(2);
      $nFactu = 0;

      $pdf->SetFont('verdana','',$cFs_Normal);
      for($e=0; $e<count($mDatos['facdoxxx']); $e++){
    	  $nFactu++;
       	$nLetf = 0;
        $nInd_Com ++;
      	$pdf->Ln(2);
        $pdf->setX(10);

        $pdf->Cell(24,0,str_pad(trim($mDatos['facdoxxx'][$e]['secmovxx']),3,0,STR_PAD_LEFT).") ".trim($mDatos['facdoxxx'][$e]['comidxxx'])."-".str_pad(trim($mDatos['facdoxxx'][$e]['comcodxx']),3,0,STR_PAD_LEFT)."-".str_pad(trim($mDatos['facdoxxx'][$e]['comcscxx']),10,0,STR_PAD_LEFT),0,0,'L');
        $pdf->Cell(12,0,trim($mDatos['facdoxxx'][$e]['comcsc2x']),0,0,'L');
        $pdf->Cell(12,0,trim($mDatos['facdoxxx'][$e]['comfecxx']),0,0,'L');
        $pdf->Cell(10,0,trim($mDatos['facdoxxx'][$e]['reghcrex']),0,0,'L');
        $pdf->Cell(13,0,trim($mDatos['facdoxxx'][$e]['pucidxxx']),0,0,'L');
        $pdf->Cell(30,0,trim($mDatos['facdoxxx'][$e]['ctoidxxx']),0,0,'L');
        $pdf->Cell(30,0,substr($mDatos['facdoxxx'][$e]['clinomxx'],0,25),0,0,'L');
        $pdf->Cell(26,0,substr($mDatos['facdoxxx'][$e]['pronomxx'],0,25),0,0,'L');
      	$pdf->Cell(18,0,((strpos((abs($mDatos['facdoxxx'][$e]['comvlrxx'])+0),'.') > 0) ? number_format(($mDatos['facdoxxx'][$e]['comvlrxx']+0),2,',','.') : number_format(($mDatos['facdoxxx'][$e]['comvlrxx']+0),0,',','.')),0,0,'R');
        $pdf->Cell(2,0,$mDatos['facdoxxx'][$e]['commovxx'],0,0,'C');
        $pdf->Cell(20,0,$mDatos['facdoxxx'][$e]['comfacxx'],0,0,'L');
    	}
    }
    if ($nFactu == 0){
      $nLetf = 0;
    	$pdf->Ln(5);
    	$cImprime = "NO HAY FACTURAS PARA EL DO";
    	$pdf->setX($nLetf+10);
    	$pdf->SetFont('verdana','',$cFs_Normal);
    	$pdf->Cell(strlen($cImprime),0,$cImprime,0,0,'L');
    }
    /***** Fin de Facturas del DO *****/

    /***** Formularios asignados al DO *****/

    $nLetf = 0;
    $pdf->Ln(7);
    $cImprime = "MOVIMIENTO DE FORMULARIOS";
    $pdf->setX($nLetf+10);
    $pdf->SetFont('verdanab','',$cFs_SubTitulo);
    $pdf->Cell(strlen($cImprime),0,$cImprime,0,0,'L');

    /*$pdf->MultiCell(200,3,$cImprime,0,'L');*/

    /////////////////////////////////////////////////////////////////////////////////////////////
    $nLetf = 0;
    $CadenaConDo="";
    $VlrForConDo=0;
    $CadenaPrvGasto="";
    $VlrForPrvGasto=0;
    $pdf->Ln(7);

    if ($mDatos['movformu'] != "" ) {
      $pdf->SetFont('verdana','',$cFs_Normal);
      $pdf->setX($nLetf+10);
      $pdf->MultiCell(200,3,$mDatos['movformu1'],0);
    }else{
      $pdf->SetFont('verdana','',$cFs_Normal);
      $pdf->SetX(10);
      $pdf->MultiCell(260,3,$mDatos['movformu2'],0,'L');
    }
    if ($mDatos['movformus'] != "" ) {
      $pdf->SetFont('verdana','',$cFs_Normal);
      $pdf->SetX(10);
      $pdf->SetFont('verdana','',$cFs_Normal);
      $pdf->MultiCell(200,3,$mDatos['movformu3'],0,'J');
    }else{
      $pdf->SetFont('verdana','',$cFs_Normal);
      $pdf->SetX(10);
      $pdf->MultiCell(260,3,$mDatos['movformu4'],0,'L');
    }
    /////////////////////////////////////////////////////////////////////////////////////////////
    /***** Fin de Formularios asignados al DO *****/
  }


  /***** Imprime Cuadre del DO *****/
  $pdf->Ln(3);
  $cImprime = "CUADRE DEL DO: ";
  $pdf->SetFont('verdanab','',$cFs_SubTitulo);
  $pdf->SetX(150);
  $pdf->Cell(50,3,$cImprime,0,'L');
  $cImprime = $nSumTot;
  $pdf->SetFont('verdanab','',$cFs_SubTitulo);
  $pdf->SetX(185);
	$pdf->Cell(50,3,((strpos((abs($mDatos['cuadredo'])+0),'.') > 0) ? number_format(($mDatos['cuadredo']+0),2,',','.') : number_format(($mDatos['cuadredo']+0),0,',','.')));
  /***** Fin de Imprime Cuadre del DO *****/

  $pdf->Ln(5);
  ##INGRESOS ESTIMADOS##
  #El valor de Ingresos Estimados se muestra solo si el DO no ha sido Facturado
  //Si es vacio, es porque el reporte se genero desde el movimiento del DO
  if($gMosIng == 1 || $gMosIng == ""){

    if ($mDatos['regestxx'] == "ACTIVO") {
      $pdf->Ln(5);
  	  $pdf->SetFont('verdanab','',$cFs_SubTitulo);
  	  $pdf->setX(10);
  	  $pdf->Cell(168,5,"INGRESOS ESTIMADOS",0,0,'l'); // TITULO DEL FORMULARIO //
  	  $pdf->Ln(5);
      $pdf->SetFont('verdanab','',$cFs_Normal);

  	  if (count($mDatos['ingestxx']) > 0) {

    	  $pdf->setX(10);
    	  $pdf->SetFont('verdanab','',5);
    	  $pdf->Cell(15,3,"Concepto",0,0,'L');
    	  $pdf->Cell(155,3,"Descripcion",0,0,'L');
    	  $pdf->Cell(20,3,"Valor",0,0,'R');

    	  $pdf->Ln(3);
    	  $pdf->SetFont('verdana','',$cFs_Normal);
    		$pdf->SetWidths(array(15,155,20));
    		$pdf->SetAligns(array("L","L","R"));


    	  for ($nIP=0; $nIP<count($mDatos['ingestxx']); $nIP++) {
    	    $pdf->setX(10);
    	    $pdf->Row(array(trim($mDatos['ingestxx'][$nIP]['ctoidxxx']),
    										 trim($mDatos['ingestxx'][$nIP]['ctodesxx']),
    										 ((strpos((abs($mDatos['ingestxx'][$nIP]['comvalor'])+0),'.') > 0) ? number_format(($mDatos['ingestxx'][$nIP]['comvalor']+0),2,',','.') : number_format(($mDatos['ingestxx'][$nIP]['comvalor']+0),0,',','.'))));

    	  }

        $pdf->SetFont('verdanab','',$cFs_SubTitulo);
        $pdf->Ln(6);
        $pdf->setX(10);
        $pdf->Cell(90,0,"Total Ingresos Estimados: ",0,0,'L'); // TITULO DEL FORMULARIO //
        $pdf->Cell(100,0,((strpos((abs($mDatos['subtotxx'])+0),'.') > 0) ? number_format(($mDatos['subtotxx']+0),2,',','.') : number_format(($mDatos['subtotxx']+0),0,',','.')),0,0,'R'); // TITULO DEL FORMULARIO //
        $pdf->Ln(2);
      }

    	$pdf->SetFont('verdana','',$cFs_Normal);
    	//Mensajes adicionales de advertencia
    	$pdf->setX(10);
    	$pdf->MultiCell(200,3,str_replace("~", "\n", $mDatos['mensajex']),0);
    	$pdf->SetFont('verdana','',$cFs_SubTitulo);
      $pdf->Ln(5);
    }
  }
  #########################################################################################################################################################################################

  #########################################################################################################################################################################################

  #########################################################################################################################################################################################
  if ($gPyG == 1) {
    /**
  	 * Imprimiendo Encabezado
  	 */
  	$pdf->Ln(1);
  	$cNomRep="P & G DEL DO";
  	$pdf->SetFont('verdanab','',$cFs_SubTitulo);
  	$pdf->setX(10);
  	$pdf->Cell(168,0,$cNomRep,0,0,'l'); // TITULO DEL FORMULARIO //

  	$nSumCueDeb = 0;
  	$nSumCueCre = 0;
  	$nDigCue = 0;
  	$nBan = 0;

    if (count($mDatos['pygdoxxx']) > 0) {

      $pdf->Ln(10);
      $pdf->SetFont('verdanab','',$cFs_Normal);
      $pdf->setX($nLetf+10);
      $pdf->SetFont('verdanab','',5);
      $pdf->Cell(24,0,"Comprobante",0,0,'L');
      $pdf->Cell(12,0,($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') ? "Csc Tres" : "Csc Dos",0,0,'L');
      $pdf->Cell(12,0,"Fecha",0,0,'L');
      $pdf->Cell(10,0,"Hora",0,0,'L');
      $pdf->Cell(13,0,"Concepto",0,0,'L');
      $pdf->Cell(30,0,"Descripcion",0,0,'L');
      $pdf->Cell(30,0,"Tercero",0,0,'L');
      $pdf->Cell(26,0,"Cliente",0,0,'L');
      $pdf->Cell(20,0,"Debito",0,0,'C');
      $pdf->Cell(20,0,"Credito",0,0,'C');
      $pdf->Ln(3);

      for($j=0;$j<count($mDatos['pygdoxxx']);$j++){

        if($mDatos['pygdoxxx'][$j]['ndingexx'] != $mDatos['pygdoxxx'][$j]['digcuexx']){
          $pdf->SetTextColor(0);
    		  $pdf->Ln(3);
    		  $pdf->setX(7);
    		  $pdf->SetFont('verdanab','',$cFs_SubTitulo);
    		  $pdf->Cell(160,0,$mDatos['pygdoxxx'][$j]['ctilsubx'],0,0,'R');
    		  $pdf->Cell(20,0,((strpos((abs($mDatos['pygdoxxx'][$j]['sumdebxx'])+0),'.') > 0) ? number_format(($mDatos['pygdoxxx'][$j]['sumdebxx']+0),2,',','.') : number_format(($mDatos['pygdoxxx'][$j]['sumdebxx']+0),0,',','.')),0,0,'R');
    		  $pdf->Cell(20,0,((strpos((abs($mDatos['pygdoxxx'][$j]['sumcrexx'])+0),'.') > 0) ? number_format(($mDatos['pygdoxxx'][$j]['sumcrexx']+0),2,',','.') : number_format(($mDatos['pygdoxxx'][$j]['sumcrexx']+0),0,',','.')),0,0,'R');
    		  $pdf->Ln(3);
        }

    		$pdf->SetFont('verdana','',5);
    	  $pdf->SetTextColor($mDatos['pygdoxxx'][$j]['colorfon'][0],$mDatos['pygdoxxx'][$j]['colorfon'][1],$mDatos['pygdoxxx'][$j]['colorfon'][2]);

        $pdf->setX(7);
        $pdf->Ln(2);

      	$pdf->SetFont('verdana','',$cFs_Normal);
    	  $pdf->setX(10);
        $pdf->Cell(24,0,$mDatos['pygdoxxx'][$j]['comidxxx']."-".$mDatos['pygdoxxx'][$j]['comcodxx']."-".$mDatos['pygdoxxx'][$j]['comcscxx'],0,0,'L');
      	$pdf->Cell(12,0,$mDatos['pygdoxxx'][$j]['comcsc2x'],0,0,'L');
      	$pdf->Cell(12,0,$mDatos['pygdoxxx'][$j]['comfecxx'],0,0,'L');
      	$pdf->Cell(10,0,$mDatos['pygdoxxx'][$j]['reghcrex'],0,0,'L');
      	$pdf->Cell(13,0,$mDatos['pygdoxxx'][$j]['ctoidxxx'],0,0,'L');
      	$pdf->Cell(30,0,substr($mDatos['pygdoxxx'][$j]['descripx'],0,25),0,0,'L');
      	$pdf->Cell(30,0,substr($mDatos['pygdoxxx'][$j]['pronomxx'],0,25),0,0,'L');
      	$pdf->Cell(26,0,substr($mDatos['pygdoxxx'][$j]['clinomxx'],0,25),0,0,'L');
      	$pdf->Cell(20,0,((strpos((abs($mDatos['pygdoxxx'][$j]['valdebxx'])+0),'.') > 0) ? number_format(($mDatos['pygdoxxx'][$j]['valdebxx']+0),2,',','.') : number_format(($mDatos['pygdoxxx'][$j]['valdebxx']+0),0,',','.')),0,0,'R');
      	$pdf->Cell(20,0,((strpos((abs($mDatos['pygdoxxx'][$j]['valcrexx'])+0),'.') > 0) ? number_format(($mDatos['pygdoxxx'][$j]['valcrexx']+0),2,',','.') : number_format(($mDatos['pygdoxxx'][$j]['valcrexx']+0),0,',','.')),0,0,'R');
        $pdf->SetTextColor(0);
     }

     $pdf->Ln(3);
     $pdf->setX(7);
     $pdf->SetFont('verdanab','',$cFs_SubTitulo);
     $pdf->Cell(160,0,$mDatos['ctilsub'],0,0,'R');
     $pdf->Cell(20,0,((strpos((abs($mDatos['nsumdxxx'])+0),'.') > 0) ? number_format(($mDatos['nsumdxxx']+0),2,',','.') : number_format(($mDatos['nsumdxxx']+0),0,',','.')),0,0,'R');
     $pdf->Cell(20,0,((strpos((abs($mDatos['nsumcxxx'])+0),'.') > 0) ? number_format(($mDatos['nsumcxxx']+0),2,',','.') : number_format(($mDatos['nsumcxxx']+0),0,',','.')),0,0,'R');
     $pdf->Ln(3);

     $pdf->Ln(3);
     $pdf->setX(7);
     $pdf->SetFont('verdanab','',$cFs_SubTitulo);
     $pdf->Cell(180,0,$mDatos['ctitulox'],0,0,'R');
     $pdf->Cell(20,0,((strpos((abs($mDatos['ntotalxx'])+0),'.') > 0) ? number_format(($mDatos['ntotalxx']+0),2,',','.') : number_format(($mDatos['ntotalxx']+0),0,',','.')),0,0,'R');
   }

   $pdf->Ln(3);
   $pdf->SetFont('verdana','',$cFs_Normal);
   $pdf->setX(10);
   $pdf->Cell(200,3,$mDatos['mensalix'],0,0,'L');
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

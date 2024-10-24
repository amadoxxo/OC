<?php
  namespace openComex;
  use FPDF;

  include("../../../../libs/php/utility.php");

  $cPerAno = date('Y');
  $cPerMes = date('m');

	global $cUsrNom;

  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

	class PDF extends FPDF {
    //Cabecera de p�gina
    function Header() {
      global $cSopId;
      global $cUsrNom;
      global $cAlfa;
      global $xConexion01;
      global $cPlesk_Skin_Directory;

      $qUsrDat  = "SELECT * ";
      $qUsrDat .= "FROM $cAlfa.SIAI0003 ";
      $qUsrDat .= "WHERE ";
      $qUsrDat .= "USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
      $xUsrDat  = f_MySql("SELECT","",$qUsrDat,$xConexion01,"");
      $filasPro = mysql_num_rows($xUsrDat);
      if ($filasPro > 0) {
        while ($xRUD = mysql_fetch_array($xUsrDat))	{
          $cUsrNom = trim($xRUD['USRNOMXX']);
          break;
        }
      }
  		//$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/SIACO1.jpg',10,0,190,34);
      $this->SetFont('arial','B',11);
      $this->setY(30);
      $this->MultiCell(187,5,'SOPORTE FORMULARIOS-GASTO',0,'C',0);
      $this->SetFont('arial','B',8);
      $this->SetXY(147,35);
      //$this->MultiCell(50,5,'Memorando No.'.$cSopId.'',0,'R',0);
      $this->MultiCell(50,5,'Memorando No.'.$cSopId.'',0,'R',0);
      $this->SetFont('arial','',8);
      $this->setY(45);
      $this->Cell(140,5,'De : '.$cUsrNom." - ".$_COOKIE['kUsrId']);
      $this->SetXY(171,45);
      $this->Cell(30,5,'Fecha: '.date('Y-m-d'));
      //$this->Cell(30,5,'Hora: '.date('H:i:s'));
      $this->setY(50);
      $this->Cell(140,5,'Para : Departamento de Contabilidad');
      $this->SetFont('arial','B',8);
      $this->setY(60);
      $this->Cell(140,5,'Asunto:');
      $this->SetFont('arial','',8);
      $this->setY(65);
      $this->Cell(50,5,'Autorizacion por reclacificacion de formularios asumidos por la empresa por:');
      $this->Ln(6);
    }
  }

  $pdf=new PDF();
  $pdf->AliasNbPages();
  $pdf->AddPage();
  $qAgfDat = "SELECT * FROM $cAlfa.fpar0135 WHERE AFGIDXXX=\"$cSopId\" LIMIT 0,1";
  $xAgfDat  = f_MySql("SELECT","",$qAgfDat,$xConexion01,"");
  $xRAD = mysql_fetch_array($xAgfDat);

  $qUsrDat  = "SELECT * ";
  $qUsrDat .= "FROM $cAlfa.SIAI0003 ";
  $qUsrDat .= "WHERE ";
  $qUsrDat .= "USRIDXXX = \"{$xRAD['REGUSRXX']}\" LIMIT 0,1";
  $xUsrDat  = f_MySql("SELECT","",$qUsrDat,$xConexion01,"");
  $xRUD = mysql_fetch_array($xUsrDat);

  $cForm="";
  $mMatriz01 = explode("|",$xRAD['afgforms']);
	$j=0;
	for ($i=0;$i<count($mMatriz01);$i++) {
	  if ($mMatriz01[$i] !="") {
	    $zMatriz02 = explode("~", $mMatriz01[$i]);
	    $cForm=$cForm.$zMatriz02[0].', ';
	  }
	}
	$nLong=strlen($cForm);
	$cForm[($nLong-2)]=" ";
	$cObserv = str_replace(array(chr(27),chr(9),chr(13),chr(10),chr(96),chr(92),chr(180)),' ',$xRAD['afgobsxx']);
	$pdf->SetFont('arial','',8);
	$pdf->Ln(2);
  $pdf->SetFont('arial','B',8);
	$pdf->MultiCell(185,5,'Concepto: ',0,'L',0);
	$pdf->SetFont('arial','',8);
	$pdf->MultiCell(191,4,$cObserv,0,'L',0);
	$pdf->Ln(2);
	$pdf->SetFont('arial','B',8);
	$pdf->MultiCell(185,5,'Relacion de Formularios',0,'L',0);
	$pdf->SetFont('arial','B',7);
	$pdf->Ln(2);
	$pdf->SetFillColor(200);
	$pdf->Cell(30,4,'No. Serial',1,0,"C",1);
  $pdf->Cell(50,4,'Producto',1,0,"C",1);
  $pdf->Cell(50,4,'Director de Cuenta',1,0,"C",1);
  $pdf->Cell(20,4,'Fecha',1,0,"C",1);
	$pdf->Cell(20,4,'Valor',1,0,"C",1);
	$mMatriz01 = explode("|",$xRAD['afgforms']);
  $j=0;
  $nAcum=0;
  $pdf->SetFont('arial','',7);
	for ($i=0;$i<count($mMatriz01);$i++) {
	  if ($mMatriz01[$i] != "") {
	    $zMatriz02 = explode("~", $mMatriz01[$i]);
	    $pdf->Ln(4);
      $pdf->Cell(30,4,$zMatriz02[0],1,0,"L");

      $qFoiSer  = "SELECT * ";
      $qFoiSer .= "FROM $cAlfa.ffoi0000 ";
      $qFoiSer .= "WHERE ";
      $qFoiSer .= "seridxxx = \"{$zMatriz02[0]}\" LIMIT 0,1";
      $xFoiSer  = f_MySql("SELECT","",$qFoiSer,$xConexion01,"");
      $xRFS = mysql_fetch_array($xFoiSer);

      $qPtoDat  = "SELECT * ";
      $qPtoDat .= "FROM $cAlfa.fpar0132 ";
      $qPtoDat .= "WHERE ";
      $qPtoDat .= "ptoidxxx = \"{$xRFS['ptoidxxx']}\" LIMIT 0,1";
      $xPtoDat  = f_MySql("SELECT","",$qPtoDat,$xConexion01,"");
      $xRPD = mysql_fetch_array($xPtoDat);

      $qUsrDir  = "SELECT * ";
      $qUsrDir .= "FROM $cAlfa.SIAI0003 ";
      $qUsrDir .= "WHERE ";
      $qUsrDir .= "USRIDXXX = \"{$xRFS['diridxxx']}\" LIMIT 0,1";
      $xUsrDir  = f_MySql("SELECT","",$qUsrDir,$xConexion01,"");
      $xRUD = mysql_fetch_array($xUsrDir);

      $pdf->Cell(50,4,substr($xRPD['ptodesxx'],0,30),1,0,"C");
      $pdf->Cell(50,4,substr($xRUD['USRNOMXX'],0,30),1,0,"C");
      $pdf->Cell(20,4,$xRFS['comfecax'],1,0,"C");
      $pdf->Cell(20,4,number_format($xRPD['ptovlrxx'],0,'','.'),1,0,"C");
      $nAcum=$nAcum+$xRPD['ptovlrxx'];
	  }
	}
	$pdf->SetFillColor(200);
  $pdf->Ln(7);
  $pdf->Cell(30,4,'',0,0,"L");
  $pdf->Cell(50,4,'',0,0,"C");
  $pdf->Cell(50,4,'',0,0,"C");
  $pdf->Cell(20,4,'Total:',0,0,"C",1);
	$pdf->Cell(20,4,number_format($nAcum,0,'','.'),0,0,"C",1);
	$pdf->Ln(22);

	$pdf->Cell(60,4,'',0,0,"C");
  $pdf->Cell(60,4,'_____________________________',0,0,"C");
  $pdf->Cell(60,4,'_____________________________',0,0,"C");
  $pdf->Ln(4);
	$pdf->Cell(60,4,'Autoriz�: '.$xRUD['USRNOMXX'],0,0,"C");
  $pdf->Cell(60,4,'Revisado Compras',0,0,"C");
  $pdf->Cell(60,4,'Revisado Contabilidad',0,0,"C");

	$pdf->Output();
?>
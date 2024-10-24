<?php
  namespace openComex;
  use FPDF;

/**
	 * Imprime Soporte de Formularios Gasto.
	 * --- Descripcion: Permite Imprimir Reporte.
	 * @author Johana Arboleda Ramos <dp1@opentecnologia.com.co>
	 * @version 002
	 */
	include("../../../../libs/php/utility.php");

	$kDf = explode("~",$_COOKIE["kDatosFijos"]);
	$kMysqlDb = $kDf[3];
    
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

	#Busco los datos de quien imprime el documento
	$qUsrDat  = "SELECT USRNOMXX ";
	$qUsrDat .= "FROM $cAlfa.SIAI0003 ";
	$qUsrDat .= "WHERE ";
	$qUsrDat .= "USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
	$xUsrDat  = f_MySql("SELECT","",$qUsrDat,$xConexion01,"");
	if (mysql_num_rows($xUsrDat) > 0) {
		$xRUD = mysql_fetch_array($xUsrDat);
		$cUsrNom = $xRUD['USRNOMXX'];
	} else {
		$cUsrNom = "USUARIO SIN NOMBRE";
	}

	class PDF extends FPDF {
	  /** Cabecera de p�gina */
	  function Header(){
	   	/** Variables */
	  	global $cObsCsc;
	  	global $cUsrNom;
	    global $cAlfa;
	    global $xConexion01;
	    global $kMysqlDb;
	    global $cPlesk_Skin_Directory;
	
			switch($kMysqlDb){
			  case 'ALPOPULX':
			  case 'TEALPOPULX':
			  case 'DEALPOPULX':
			  case 'TEALPOPULP':
			    $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/alpopul1.jpg',70,5,70,20);
			  break;
			}
	
	    $this->SetFont('arial','B',11);
	    $this->setY(30);
	    $this->MultiCell(187,5,'SOPORTE FORMULARIOS-GASTO',0,'C',0);
	    $this->SetFont('arial','B',8);
	    $this->SetXY(147,35);
	    $this->MultiCell(50,5,'Memorando No.'.$cObsCsc.'',0,'R',0);
	    $this->SetFont('arial','',8);
	    $this->setY(45);
	    $this->Cell(140,5,'De : '.$cUsrNom." - ".$_COOKIE['kUsrId']);
	    $this->SetXY(171,45);
	    $this->Cell(30,5,'Fecha: '.date('Y-m-d'));
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
  
  #Busco las observaciones por producto y serial
	$qFoiDat  = "SELECT DISTINCT  ";
	$qFoiDat .= "$cAlfa.ffob0000.*, ";
	$qFoiDat .= "IF($cAlfa.fpar0123.gofdesxx <> \"\",$cAlfa.fpar0123.gofdesxx,\"GRUPO SIN DESCRIPCION\") AS gofdesxx, ";
	$qFoiDat .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"DIRECTOR SIN NOMBRE\") AS usrnomxx ";
  $qFoiDat .= "FROM $cAlfa.ffob0000 ";
  $qFoiDat .= "LEFT JOIN $cAlfa.fpar0123 ON $cAlfa.fpar0123.goftipxx = \"FORMULARIOS\" AND $cAlfa.fpar0123.gofidxxx = $cAlfa.ffob0000.gofidxxx ";
  $qFoiDat .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.ffob0000.diridxxx = $cAlfa.SIAI0003.USRIDXXX ";
	$qFoiDat .= "WHERE " ;
	$qFoiDat .= "$cAlfa.ffob0000.obscscxx = \"$cObsCsc\" ";
	$qFoiDat .=" AND obstipxx =\"AUTPRVGASTO\" ";
	//f_Mensaje(__FILE__,__LINE__,$qFoiDat);
	$xFoiDat  = f_MySql("SELECT","",$qFoiDat,$xConexion01,"");

	$cObs = "";
	while ($xRFD = mysql_fetch_array($xFoiDat)) {
		if ($cObs == "") {
			$cObs = $xRFD['gofdesxx'].":  ".$xRFD['obsobsxx'];
		}
		$mDatos[] = $xRFD;		 
	}
	$cObs = str_replace(array(chr(27),chr(9),chr(13),chr(10),chr(96),chr(92),chr(180)),' ',$cObs);
	$pdf->SetFont('arial','',8);
	$pdf->Ln(2);
  $pdf->SetFont('arial','B',8);
	$pdf->MultiCell(185,5,'Concepto: ',0,'L',0);
	$pdf->SetFont('arial','',8);
	$pdf->MultiCell(191,4,$cObs,0,'L',0);
	
	$pdf->Ln(2);
	$pdf->SetFont('arial','B',8);
	$pdf->MultiCell(185,5,'Relacion de Formularios',0,'L',0);
	$pdf->SetFont('arial','B',7);
	
	$pdf->Ln(2);
	$pdf->SetFillColor(200);
	$pdf->Cell(30,4,'No. Serial',1,0,"C",1);
  $pdf->Cell(42,4,'Producto',1,0,"C",1);
  $pdf->Cell(40,4,'DO',1,0,"C",1);
  $pdf->Cell(40,4,'Director de Cuenta',1,0,"C",1);
  $pdf->Cell(18,4,'Fecha',1,0,"C",1);
	$pdf->Cell(20,4,'Valor',1,0,"C",1);

  $pdf->SetFont('arial','',7);  
	$nAcum = 0;    
	for ($i=0;$i<count($mDatos);$i++) {
    #Busco los datos del formulario
    $qFoiDat  = "SELECT ptoidxxx,comfecax ";
    $qFoiDat .= "FROM $cAlfa.ffoi0000 ";
    $qFoiDat .= "WHERE ";
    $qFoiDat .= "ptoidxxx = \"{$mDatos[$i]['ptoidxxx']}\" AND ";
    $qFoiDat .= "seridxxx = \"{$mDatos[$i]['seridxxx']}\" LIMIT 0,1";
    
    //f_Mensaje(__FILE__,__LINE__,$qFoiDat);
    $xFoiDat  = f_MySql("SELECT","",$qFoiDat,$xConexion01,"");
    $xRFD = mysql_fetch_array($xFoiDat);
		
    #Busco los datos del producto
    $qPtoDat  = "SELECT ptodesxx, ptovlrxx ";
    $qPtoDat .= "FROM $cAlfa.fpar0132 ";
    $qPtoDat .= "WHERE ";
    $qPtoDat .= "ptoidxxx = \"{$xRFD['ptoidxxx']}\" LIMIT 0,1";
    $xPtoDat  = f_MySql("SELECT","",$qPtoDat,$xConexion01,"");
    $xRPD = mysql_fetch_array($xPtoDat);

    $pdf->Ln(4);
    $pdf->Cell(30,4,$mDatos[$i]['seridxxx'],1,0,"L");
    $pdf->Cell(42,4,substr($xRPD['ptodesxx'],0,26),1,0,"L");
    $pdf->Cell(40,4,$mDatos[$i]['docsucxx']."-".$mDatos[$i]['docnroxx']."-".$mDatos[$i]['docsufxx'],1,0,"C");
    $pdf->Cell(40,4,substr($mDatos[$i]['usrnomxx'],0,27),1,0,"C");
    $pdf->Cell(18,4,$xRFD['comfecax'],1,0,"C");
    $pdf->Cell(20,4,number_format($xRPD['ptovlrxx'],0,'','.'),1,0,"C");
    
    $nAcum = $nAcum + $xRPD['ptovlrxx'];
	}
	$pdf->SetFillColor(200);
  $pdf->Ln(7);
  $pdf->Cell(30,4,'',0,0,"L");
  $pdf->Cell(40,4,'',0,0,"C");
  $pdf->Cell(40,4,'',0,0,"C");
  $pdf->Cell(40,4,'',0,0,"C");
  $pdf->Cell(20,4,'Total:',0,0,"C",1);
	$pdf->Cell(20,4,number_format($nAcum,0,'','.'),0,0,"C",1);
	$pdf->Ln(22);

	$pdf->Cell(60,4,'',0,0,"C");
  $pdf->Cell(60,4,'_____________________________',0,0,"C");
  $pdf->Cell(60,4,'_____________________________',0,0,"C");
  $pdf->Ln(4);
	$pdf->Cell(60,4,'Autorizo: '.$cUsrNom,0,0,"C");
  $pdf->Cell(60,4,'Revisado Compras',0,0,"C");
  $pdf->Cell(60,4,'Revisado Contabilidad',0,0,"C");

	$pdf->Output();

?>
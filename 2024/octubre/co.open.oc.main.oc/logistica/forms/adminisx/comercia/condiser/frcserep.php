<?php
  namespace openComex;
  use FPDF;
  
  include("../../../../../financiero/libs/php/utility.php");

  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

  class PDF extends FPDF {
    function Header(){
      global $mDatos;

      $posx = 10;
      $posy = 10;

      $posy += 20;
      $this->SetFont('arial','B',10);
      $this->setXY($posx, $posy);
      $this->MultiCell(190,5,"CONDICIONES DE SERVICIO\n REPORTE CONDICIONES DE SERVICIO",'','C');

      $posy += 15;
      $this->line($posx, $posy-2, $posx+190, $posy-2);
      $this->setXY($posx, $posy);
      $this->SetFont('arial','B',8);
      $this->Cell(110, 3, "CLIENTE: " . $mDatos[0]['clinomxx']);
      $this->Cell(40,3, "COD SAP: " . $mDatos[0]['clisapxx'],0,0,'R');
      $this->Cell(40,3, "NIT: " . $mDatos[0]['cliidxxx'],0,0,'R');
      $this->line($posx, $posy+5, $posx+190, $posy+5);
      $this->Ln(8);
		}

    function Footer(){
      global $cAlfa; global $xConexion01;

      $qSqlUsr = "SELECT * FROM $cAlfa.SIAI0003 WHERE USRIDXXX = \"{$_COOKIE['kUsrId']}\" LIMIT 0,1";
      $xSqlUsr = f_MySql("SELECT","",$qSqlUsr,$xConexion01,"");
      $xRUsr = mysql_fetch_array($xSqlUsr);
      //Posicion: a 1,5 cm del final
      $this->SetY(-25);
	    //Arial italic 8
      $this->SetFont('Arial','I',6);

			//Numero de pagina
      $this->MultiCell(0,10,$xRUsr['USRNOMXX'],0,'R',0);

      //Posicion: a 1,5 cm del final
      $this->SetY(-15);
      //Arial italic 8
      $this->SetFont('Arial','I',6);
      $this->SetFont('Arial','I',8);
      $this->MultiCell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,'C',0);
    }
  }

  $mDatos = array();
  // Consulta principal condicion de servicio
  $qCondiSer  = "SELECT lpar0152.*, ";
  $qCondiSer .= "lpar0150.cliidxxx, ";
  $qCondiSer .= "lpar0150.clisapxx, ";
  $qCondiSer .= "IF(lpar0150.clinomxx != \"\",lpar0150.clinomxx,(TRIM(CONCAT(lpar0150.clinomxx,\" \",lpar0150.clinom1x,\" \",lpar0150.clinom2x,\" \",lpar0150.cliape1x,\" \",lpar0150.cliape2x)))) AS clinomxx, ";
  $qCondiSer .= "lpar0151.ccoidocx, ";
  $qCondiSer .= "lpar0151.ccofvdxx, ";
  $qCondiSer .= "lpar0151.ccofvhxx, ";
  $qCondiSer .= "lpar0006.ufadesxx, ";
  $qCondiSer .= "lpar0004.obfdesxx ";
  $qCondiSer .= "FROM $cAlfa.lpar0152 ";
  $qCondiSer .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lpar0152.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
  $qCondiSer .= "LEFT JOIN $cAlfa.lpar0006 ON $cAlfa.lpar0152.ufaidxxx = $cAlfa.lpar0006.ufaidxxx ";
  $qCondiSer .= "LEFT JOIN $cAlfa.lpar0004 ON $cAlfa.lpar0152.obfidxxx = $cAlfa.lpar0004.obfidxxx ";
  $qCondiSer .= "LEFT JOIN $cAlfa.lpar0151 ON $cAlfa.lpar0152.ccoidocx = $cAlfa.lpar0151.ccoidocx  ";
  $qCondiSer .= "WHERE ";
  if ($cCcoIdOc != "") {
    $qCondiSer .= "lpar0152.ccoidocx = \"$cCcoIdOc\" AND ";
  }
  $qCondiSer .= "lpar0152.cliidxxx = \"$cCliId\" AND ";
  if ($cEstado == "TODOS") {
    $qCondiSer .= "lpar0152.regestxx IN(\"ACTIVO\",\"INACTIVO\")";
  } else {
    $qCondiSer .= "lpar0152.regestxx = \"$cEstado\"";
  }
  $xCondiSer  = f_MySql("SELECT","",$qCondiSer,$xConexion01,"");

  // f_Mensaje(__FILE__, __LINE__,$qCondiSer."~".mysql_num_rows($xCondiSer));
  if (mysql_num_rows($xCondiSer) > 0) {
    while($xRCS = mysql_fetch_array($xCondiSer)) {

      // Descripcion del servicio
      $vServicio  = array();
      $qServicio  = "SELECT ";
      $qServicio .= "sersapxx, ";
      $qServicio .= "serdesxx, ";
      $qServicio .= "regestxx ";
      $qServicio .= "FROM $cAlfa.lpar0011 ";
      $qServicio .= "WHERE ";
      $qServicio .= "sersapxx = \"{$xRCS['sersapxx']}\" AND ";
      $qServicio .= "regestxx = \"ACTIVO\" ";
      $xServicio  = f_MySql("SELECT","",$qServicio,$xConexion01,"");
      if (mysql_num_rows($xServicio) > 0) {
        $vServicio = mysql_fetch_array($xServicio);
      }

      // Consulta Condiciones de Servicio - Subservicios
      $cCondSubser = "";
      $qCondSubser  = "SELECT ";
      $qCondSubser .= "cseidxxx, ";
      $qCondSubser .= "sersapxx, ";
      $qCondSubser .= "subidxxx, ";
      $qCondSubser .= "regestxx ";
      $qCondSubser .= "FROM $cAlfa.lpar0153 ";
      $qCondSubser .= "WHERE ";
      $qCondSubser .= "cseidxxx = \"{$xRCS['cseidxxx']}\" ";
      $xCondSubser  = f_MySql("SELECT","",$qCondSubser,$xConexion01,"");
      if (mysql_num_rows($xCondSubser) > 0) {
        while($xRSS = mysql_fetch_array($xCondSubser)) {
          // Consulta subservicios
          $qSubServ  = "SELECT ";
          $qSubServ .= "subdesxx ";
          $qSubServ .= "FROM $cAlfa.lpar0012 ";
          $qSubServ .= "WHERE ";
          $qSubServ .= "sersapxx = \"{$xRSS['sersapxx']}\" AND ";
          $qSubServ .= "subidxxx = \"{$xRSS['subidxxx']}\" AND ";
          $qSubServ .= "regestxx = \"ACTIVO\" LIMIT 0,1";
          $xSubServ  = f_MySql("SELECT","",$qSubServ,$xConexion01,"");
          if (mysql_num_rows($xSubServ) > 0) {
            $vSubServ = mysql_fetch_array($xSubServ);
            $cCondSubser .= $vSubServ['subdesxx'] . ", ";
          }
        }
      }

      // Consulta Condiciones de Servicio - Oficinas y Organizacion de Ventas
      $cCondOfiVenta = "";
      $qCondOfiVenta  = "SELECT ";
      $qCondOfiVenta .= "cseidxxx, ";
      $qCondOfiVenta .= "orvsapxx, ";
      $qCondOfiVenta .= "ofvsapxx, ";
      $qCondOfiVenta .= "regestxx ";
      $qCondOfiVenta .= "FROM $cAlfa.lpar0154 ";
      $qCondOfiVenta .= "WHERE ";
      $qCondOfiVenta .= "cseidxxx = \"{$xRCS['cseidxxx']}\" ";
      $xCondOfiVenta  = f_MySql("SELECT","",$qCondOfiVenta,$xConexion01,"");
      if (mysql_num_rows($xCondOfiVenta) > 0) {
        while($xRSS = mysql_fetch_array($xCondOfiVenta)) {
          // Consulta oficina
          $qOficina  = "SELECT ";
          $qOficina .= "ofvdesxx ";
          $qOficina .= "FROM $cAlfa.lpar0002 ";
          $qOficina .= "WHERE ";
          $qOficina .= "orvsapxx = \"{$xRSS['orvsapxx']}\" AND ";
          $qOficina .= "ofvsapxx = \"{$xRSS['ofvsapxx']}\" AND ";
          $qOficina .= "regestxx = \"ACTIVO\" LIMIT 0,1";
          $xOficina  = f_MySql("SELECT","",$qOficina,$xConexion01,"");
          if (mysql_num_rows($xOficina) > 0) {
            $vOficina = mysql_fetch_array($xOficina);
            $cCondOfiVenta .= $vOficina['ofvdesxx'] . ", ";
          }
        }
      }

      $nInt_mDatos = count($mDatos);
      $mDatos[$nInt_mDatos]['serdesxx'] = $vServicio['sersapxx'] . " - " .$vServicio['serdesxx'];

      $mDatos[$nInt_mDatos]['cliidxxx'] = $xRCS['cliidxxx'];
      $mDatos[$nInt_mDatos]['clisapxx'] = $xRCS['clisapxx'];
      $mDatos[$nInt_mDatos]['clinomxx'] = $xRCS['clinomxx'];

      $mDatos[$nInt_mDatos]['ccoidocx'] = $xRCS['ccoidocx'];
      $mDatos[$nInt_mDatos]['ccofvdxx'] = $xRCS['ccofvdxx'];
      $mDatos[$nInt_mDatos]['ccofvhxx'] = $xRCS['ccofvhxx'];

      $mDatos[$nInt_mDatos]['regestxx'] = $xRCS['regestxx'];
      $mDatos[$nInt_mDatos]['subdesxx'] = rtrim($cCondSubser, ", ");
      $mDatos[$nInt_mDatos]['ofvdesxx'] = rtrim($cCondOfiVenta, ", ");
      $mDatos[$nInt_mDatos]['ufadesxx'] = $xRCS['ufadesxx'];
      $mDatos[$nInt_mDatos]['obfdesxx'] = $xRCS['obfdesxx'];
    }
  }

  $pdf=new PDF();
  $pdf->AliasNbPages();
  $pdf->AddPage('P');   // L para poner la Hoja Horizontal, por default es P

  for ($i=0; $i < count($mDatos); $i++) { 

    $posx = $pdf->getX();
    $posy = $pdf->getY();

    $pdf->SetFont('arial','B',8);
    $pdf->SetFillColor(190);
    $pdf->setXY($posx, $posy);
    $pdf->Cell(95,5,utf8_decode("CONDICIÓN COMERCIAL: ") .$mDatos[$i]['ccoidocx'],0,0,'L', true);
    $pdf->SetFont('arial','',8);
    $pdf->Cell(95,5,"VIGENCIA DESDE: ".$mDatos[$i]['ccofvdxx']." HASTA: ".$mDatos[$i]['ccofvhxx'],0,0,'R', true);
    $pdf->Ln(6);
    $pdf->SetFillColor(225);
    
    $posy = $pdf->getY();
    // Fila 1
    $pdf->setXY($posx+5, $posy);
    $pdf->SetFont('arial','B',8);
    $pdf->Cell(18,5,"SERVICIO:",0,0,'L', true);
    $pdf->SetFont('arial','',8);
    $pdf->Cell(147,5,$mDatos[$i]['serdesxx'],0,0,'L', true);
    $pdf->SetFont('arial','B',8);
    $pdf->Cell(5,5,"ESTADO: ",0,0,'R', true);
    $pdf->SetFont('arial','',8);
    $pdf->Cell(15,5,$mDatos[$i]['regestxx'],0,0,'L', true);
    $pdf->Ln(6);

    // Fila 2
    $pdf->setX($posx+10);
    $pdf->SetFont('arial','B',8);
    $pdf->Cell(25,5,"SUB-SERVICIO:",0,0,'L', true);
    $pdf->SetFont('arial','',8);
    $pdf->MultiCell(155,5,$mDatos[$i]['subdesxx'],0,'L', true);
    $pdf->Ln(1);
 
    // Fila 3
    $pdf->setX($posx+15);
    $pdf->SetFont('arial','B',8);
    $pdf->Cell(33,5,"OFICINAS DE VENTA:",0,0,'L', true);
    $pdf->SetFont('arial','',8);
    $pdf->MultiCell(142,5,$mDatos[$i]['ofvdesxx'],0,'L', true);
    $pdf->Ln(1);

    // Fila 4
    $pdf->setX($posx+20);
    $pdf->SetFont('arial','B',8);
    $pdf->Cell(35,5,"UNIDAD FACTURABLE:",0,0,'L', true);
    $pdf->SetFont('arial','',8);
    $pdf->MultiCell(135,5,$mDatos[$i]['ufadesxx'],0,'L', true);
    $pdf->Ln(1);

    // Fila 5
    $pdf->setX($posx+25);
    $pdf->SetFont('arial','B',8);
    $pdf->Cell(35,5,"OBJETO FACTURABLE:",0,0,'L', true);
    $pdf->SetFont('arial','',8);
    $pdf->MultiCell(130,5,$mDatos[$i]['obfdesxx'],0,'L', true);

    $pdf->Ln(10);
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

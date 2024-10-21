<?php
namespace openComex;
  /**
   * Generar Impreso Vista Definitiva del Pedido.
   * --- Descripcion: Permite Imprimir la vista definitiva del Pedido.
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package opencomex
   * @version 001
   */

  include("../../../../../financiero/libs/php/utility.php");

  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
	require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

  $mPrints = explode("~", $prints);
  $cPedId  = $mPrints[0];
  $cAno    = substr($mPrints[1], 0, 4);

  // Consultando informacion de Cabecera del Pedido
  $qCabPedido  = "SELECT ";
  $qCabPedido .= "$cAlfa.lpca$cAno.*, ";
  $qCabPedido .= "IF($cAlfa.lpar0150.clinomxx != \"\",$cAlfa.lpar0150.clinomxx,\"CLIENTE SIN NOMBRE\") AS clinomxx, ";
  $qCabPedido .= "IF($cAlfa.lpar0150.clidirxx != \"\",$cAlfa.lpar0150.clidirxx,\"SIN DIRECCION\") AS clidirxx, ";
  $qCabPedido .= "IF($cAlfa.lpar0150.clitelxx != \"\",$cAlfa.lpar0150.clitelxx,\"SIN TELEFONO\") AS clitelxx, ";
  $qCabPedido .= "IF($cAlfa.lpar0150.paiidxxx != \"\",$cAlfa.lpar0150.paiidxxx,\"\") AS paiidxxx, ";
  $qCabPedido .= "IF($cAlfa.lpar0150.depidxxx != \"\",$cAlfa.lpar0150.depidxxx,\"\") AS depidxxx, ";
  $qCabPedido .= "IF($cAlfa.lpar0150.ciuidxxx != \"\",$cAlfa.lpar0150.ciuidxxx,\"\") AS ciuidxxx ";
  $qCabPedido .= "FROM $cAlfa.lpca$cAno ";
  $qCabPedido .= "LEFT JOIN $cAlfa.lpar0150 ON $cAlfa.lpca$cAno.cliidxxx = $cAlfa.lpar0150.cliidxxx ";
  $qCabPedido .= "WHERE ";
  $qCabPedido .= "$cAlfa.lpca$cAno.pedidxxx = \"$cPedId\" LIMIT 0,1";
  $xCabPedido  = f_MySql("SELECT","",$qCabPedido,$xConexion01,"");
  $vCabPedido = array();
  if (mysql_num_rows($xCabPedido) > 0) {
    $vCabPedido = mysql_fetch_array($xCabPedido);
  }

  $mDetPedido  = array();
  $cDeposito   = "";
  $dFechaDesde = "";
  $dFechaHasta = "";
  // Consultando el detalle del Pedido
  $qDetPedido  = "SELECT ";
  $qDetPedido .= "$cAlfa.lpde$cAno.* ";
  $qDetPedido .= "FROM $cAlfa.lpde$cAno ";
  $qDetPedido .= "WHERE ";
  $qDetPedido .= "$cAlfa.lpde$cAno.pedidxxx = \"$cPedId\" ";
  $xDetPedido  = f_MySql("SELECT","",$qDetPedido,$xConexion01,"");
  if (mysql_num_rows($xDetPedido) > 0) {
    while ($xRDP = mysql_fetch_array($xDetPedido)) {
      // Obtiene la informacion del deposito
      $cDeposito .= ($xRDP['depnumxx'] != "") ? $xRDP['depnumxx'].", " : $xRDP['depnum2x'] . ", ";

      // Consultando descripcion del servicio
      $qSercicio  = "SELECT ";
      $qSercicio .= "sersapxx, ";
      $qSercicio .= "serdesxx, ";
      $qSercicio .= "regestxx ";
      $qSercicio .= "FROM $cAlfa.lpar0011 ";
      $qSercicio .= "WHERE ";
      $qSercicio .= "sersapxx = \"{$xRDP['sersapxx']}\" ";
      $xServicio  = f_MySql("SELECT","",$qSercicio,$xConexion01,"");
      $vServicio  = array();
      if (mysql_num_rows($xServicio) > 0) {
        $vServicio = mysql_fetch_array($xServicio);
      }

      // Consultando descripcion del CEBE
      $qCodCebe  = "SELECT ";
      $qCodCebe .= "cebidxxx, ";
      $qCodCebe .= "cebcodxx, ";
      $qCodCebe .= "cebdesxx, ";
      $qCodCebe .= "regestxx ";
      $qCodCebe .= "FROM $cAlfa.lpar0010 ";
      $qCodCebe .= "WHERE ";
      $qCodCebe .= "cebidxxx = \"{$xRDP['cebidxxx']}\" ";
      $xCodCebe  = f_MySql("SELECT","",$qCodCebe,$xConexion01,"");
      $vCodCebe  = array();
      if (mysql_num_rows($xCodCebe) > 0) {
        $vCodCebe = mysql_fetch_array($xCodCebe);
      }

      $nInd_mDetPedido = count($mDetPedido);
      $mDetPedido[$nInd_mDetPedido]['serdesxx'] = $vServicio['serdesxx'];
      $mDetPedido[$nInd_mDetPedido]['cebdesxx'] = $vCodCebe['cebdesxx'];
      $mDetPedido[$nInd_mDetPedido]['cseidxxx'] = $xRDP['cseidxxx'];
      $mDetPedido[$nInd_mDetPedido]['pedbasex'] = $xRDP['pedbasex'];
      $mDetPedido[$nInd_mDetPedido]['pedtarix'] = $xRDP['pedtarix'];
      $mDetPedido[$nInd_mDetPedido]['pedcalcu'] = $xRDP['pedcalcu'];
      $mDetPedido[$nInd_mDetPedido]['pedminix'] = $xRDP['pedminix'];
      $mDetPedido[$nInd_mDetPedido]['pedvlrxx'] = $xRDP['pedvlrxx'];

      if ($dFechaDesde == "" && $dFechaHasta == "") {
        $dFechaDesde = ($vCabPedido['pedtipxx'] == "AUTOMATICA") ? $xRDP['cerfdexx'] : $xRDP['pedfdexx'];
        $dFechaHasta = ($vCabPedido['pedtipxx'] == "AUTOMATICA") ? $xRDP['cerfhaxx'] : $xRDP['pedfhaxx'];
      }
    }
  }

  // Consulto la oficina de venta
  $qOfiVenta  = "SELECT ";
  $qOfiVenta .= "orvsapxx, ";
  $qOfiVenta .= "ofvsapxx, ";
  $qOfiVenta .= "ofvdesxx, ";
  $qOfiVenta .= "regestxx ";
  $qOfiVenta .= "FROM $cAlfa.lpar0002 ";
  $qOfiVenta .= "WHERE ";
  $qOfiVenta .= "ofvsapxx = \"{$vCabPedido['ofvsapxx']}\" AND ";
  $qOfiVenta .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  $xOfiVenta  = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");
  $vOfiVenta = array();
  if (mysql_num_rows($xOfiVenta) > 0) {
    $vOfiVenta = mysql_fetch_array($xOfiVenta);
  }

  // Consulta la ciudad del cliente
  $qCiuDat  = "SELECT * ";
  $qCiuDat .= "FROM $cAlfa.SIAI0055 ";
  $qCiuDat .= "WHERE ";
  $qCiuDat .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$vCabPedido['paiidxxx']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$vCabPedido['depidxxx']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.CIUIDXXX = \"{$vCabPedido['ciuidxxx']}\" AND ";
  $qCiuDat .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
  $xCiuDat  = f_MySql("SELECT","",$qCiuDat,$xConexion01,"");
  // f_Mensaje(__FILE__,__LINE__,$qCiuDat."~".mysql_num_rows($xCiuDat));
  if (mysql_num_rows($xCiuDat) > 0) {
    $vCiuDat = mysql_fetch_array($xCiuDat);
  }

  $dFechaDesde = fnFechaLetras($dFechaDesde);
  $dFechaHasta = fnFechaLetras($dFechaHasta);
  $vFechCrea   = fnFechaLetras($vCabPedido['comfecxx']);

  class PDF extends FPDF {

    function Header() {
      global $vSysStr; global $cPlesk_Skin_Directory_Logistic; global $vCabPedido; global $dFechaDesde; global $dFechaHasta; 
      global $vFechCrea; global $vOfiVenta;  global $vCiuDat;  global $cDeposito;
  
      $posx    = 10;
      $posy    = 10;
      $posyIni = $posy;

      // Logo
      $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory_Logistic.'/logoalpopular.jpg', $posx+30, $posy+2, 45);

      // Columna 1
      $this->setXY($posx, $posy+20);
      $this->SetFont('verdanab','',8);
      $this->Cell(105,4,"Almacen General de Depositos Alpopular S.A",0,0,'C');
      $this->Ln(4);
      $this->setX($posx);
      $this->Cell(105,5,"Nit. " . number_format($vSysStr['financiero_nit_agencia_aduanas'],0,'','.')."-".f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']),0,0,'C');
      $this->Rect($posx, $posy, 110, 30);

      $this->setXY($posx, $posy+35);
      $this->SetFillColor(227, 227, 227);
      $this->Cell(110,6,utf8_decode("PERIODO DE FACTURACION"),1,0,'C', true);
      $this->Ln(6);
      $this->setX($posx);
      $this->SetFont('verdanab','',8);
      $this->Cell(35,5,"Desde",1,0,'C');
      $this->SetFont('verdana','',8);
      $this->Cell(25,5,$dFechaDesde[0],1,0,'C');
      $this->Cell(25,5,$dFechaDesde[1],1,0,'C');
      $this->Cell(25,5,$dFechaDesde[2],1,0,'C');
      $this->Ln(5);
      $this->setX($posx);
      $this->SetFont('verdanab','',8);
      $this->Cell(35,5,"Hasta",1,0,'C');
      $this->SetFont('verdana','',8);
      $this->Cell(25,5,$dFechaHasta[0],1,0,'C');
      $this->Cell(25,5,$dFechaHasta[1],1,0,'C');
      $this->Cell(25,5,$dFechaHasta[2],1,0,'C');
      $this->Ln(8);

      $this->setX($posx);
      $this->SetFont('verdanab','',8);
      $this->Cell(35,5,"Oficina de Ventas",1,0,'C', true);
      $this->SetFont('verdana','',8);
      $this->Cell(75,5,$vOfiVenta['ofvdesxx'],1,0,'C');
      $this->Ln(5);
      $this->setX($posx);
      $this->SetFont('verdanab','',8);
      $this->Cell(35,18,"No. Deposito",1,0,'C', true);
      $posy = $this->getY();
      $this->setXY($posx+37, $posy+3);
      $this->SetFont('verdana','',8);
      $this->MultiCell(70,4,str_replace("~", ", ", $vCabPedido['pedmemde']),0,'C');
      $posyFin1 = $this->getY() > ($posy+18) ? $this->getY() : ($posy+18);
      $this->Rect($posx+35, $posy, 75, ($posyFin1-$posy));

      // Columna 2
      $posy = $posyIni;
      $this->setXY($posx+130, $posy+8);
      $this->SetFont('verdanab','',8);
      $this->Cell(50,4,"No. PEDIDO",0,0,'C');
      $this->Rect($posx+130, $posy, 50, 20);

      $this->SetFillColor(227, 227, 227);
      $this->setXY($posx+180, $posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(77,20,$vCabPedido['comidxxx'] ."-". $vCabPedido['comprexx'] ."-". $vCabPedido['comcscxx'],1,0,'C', true);
      $this->Ln(22);
      $this->setX($posx+130);
      $this->Cell(32,5,"Fecha Pedido",1,0,'L', true);
      $this->SetFont('verdana','',8);
      $this->Cell(30,5,$vFechCrea[0],1,0,'C');
      $this->Cell(35,5,$vFechCrea[1],1,0,'C');
      $this->Cell(30,5,$vFechCrea[2],1,0,'C');
      $this->Ln(8);
      $this->setX($posx+130);
      $this->SetFont('verdanab','',8);
      $this->Cell(127,6,"CLIENTE",1,0,'C', true);
      $posy = $this->getY()+8;
      $this->SetFont('verdanab','',8);
      $this->setXY($posx+130,$posy);
      $this->Cell(10,4,"RAZON SOCIAL",0,0,'L');
      $this->setX($posx+160);
      $this->SetFont('verdana','',8);
      $this->MultiCell(90,4, $vCabPedido['clinomxx'],0,'L');
      $this->Ln(1);
      $this->setX($posx+130);
      $this->SetFont('verdanab','',8);
      $this->Cell(10,4,"NIT",0,0,'L');
      $this->setX($posx+160);
      $this->SetFont('verdana','',8);
      $this->MultiCell(90,4,utf8_decode($vCabPedido['cliidxxx']),0,'L');
      $this->Ln(1);
      $this->setX($posx+130);
      $this->SetFont('verdanab','',8);
      $this->Cell(10,4,"CIUDAD",0,0,'L');
      $this->setX($posx+160);
      $this->SetFont('verdana','',8);
      $this->MultiCell(90,4,$vCiuDat['CIUDESXX'],0,'L');
      $this->Ln(1);
      $this->setX($posx+130);
      $this->SetFont('verdanab','',8);
      $this->Cell(10,4,utf8_decode("DIRECCIÓN"),0,0,'L');
      $this->setX($posx+160);
      $this->SetFont('verdana','',8);
      $this->MultiCell(90,4,utf8_decode($vCabPedido['clidirxx']),0,'L');
      $this->Ln(1);
      $this->setX($posx+130);
      $this->SetFont('verdanab','',8);
      $this->Cell(10,4,"TELEFONO",0,0,'L');
      $this->setX($posx+160);
      $this->SetFont('verdana','',8);
      $this->MultiCell(90,4,utf8_decode($vCabPedido['clitelxx']),0,'L');
      $this->Ln(1);
      $posyFin2 = $this->getY();
      $this->Rect($posx+130, $posy, 127, $posyFin2-$posy);

      $posy = ($posyFin1 > $posyFin2) ? $posyFin1+4 : $posyFin2+4;

      $this->setXY($posx,$posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(85,5,utf8_decode("DESCRIPCIÓN SERVICIO"),1,0,'C', true);
      $this->Cell(40,5,utf8_decode("DESCRIPCIÓN CEBE"),1,0,'C', true);
      $this->Cell(30,5,"BASE",1,0,'C', true);
      $this->Cell(25,5,"TARIFA",1,0,'C', true);
      $this->Cell(25,5,utf8_decode("CÁLCULO"),1,0,'C', true);
      $this->Cell(25,5,utf8_decode("MÍNIMA"),1,0,'C', true);
      $this->Cell(27,5,"VALOR PEDIDO",1,0,'C', true);
      $this->Ln(7);
      $this->nPosYIni = $this->getY();
    }//Function Header

    function Footer(){
      global $vCabPedido;

      $posx = 10;
      $posy = 190;

      $this->SetFillColor(227, 227, 227);
      $this->setXY($posx,$posy);
      $this->SetFont('verdanab','',8);
      $this->Cell(257,5,utf8_decode("OBSERVACIONES PEDIDO"),1,0,'C', true);
      $this->setXY($posx,$posy+5);
      $this->SetFont('verdana','',8);
      $this->MultiCell(257,5,$vCabPedido['pedobsxx'],0,'L');
      $this->Rect($posx, $posy+5, 257, 14);

      $this->setXY($posx,$posy+20);
      $this->SetFont('verdanab','',8);
      $this->Cell(257,5,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
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

    function TextWithDirection($x, $y, $txt, $direction='U') {
      if ($direction=='R')
          $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
      elseif ($direction=='L')
          $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
      elseif ($direction=='U')
          $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
      elseif ($direction=='D')
          $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
      else
          $s=sprintf('BT %.2F %.2F Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
      if ($this->ColorFlag)
          $s='q '.$this->TextColor.' '.$s.' Q';
      $this->_out($s);
    }
  }
    
  $pdf = new PDF('L','mm','Letter');
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->AddFont('otfon1','','otfon1.php');
  $pdf->AliasNbPages();
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);
  $pdf->AddPage();

  $posy    = $pdf->nPosYIni;
  $posyIni = $posy-2;
  $posx    = 10;
  $posfin  = 180;
  $nVlrPedido = 0;

  // $mDetPedido = array_merge($mDetPedido, $mDetPedido, $mDetPedido, $mDetPedido);
  // $mDetPedido = array_merge($mDetPedido, $mDetPedido, $mDetPedido, $mDetPedido);

  if (count($mDetPedido) > 0){
    $pdf->SetWidths(array(85,40,30,25,25,25,27));
    $pdf->SetAligns(array("L","C","R","R","R","R","R"));
    $pdf->SetFont('verdana','',7);
    $pdf->setXY($posx,$posy);

    for($i=0;$i<count($mDetPedido);$i++){
      if($posy > $posfin){
        $pdf->Rect($posx,$posyIni,257,($posfin+10)-$posyIni);
        $pdf->AddPage();
        $posy = $pdf->nPosYIni;
        $posx = 10;
        $pdf->setXY($posx,$posy);
      }
      $nVlrPedido += $mDetPedido[$i]['pedvlrxx'];

      // Consulto la tarifa asociada a la Condicion de Servicio para identificar si es Tarifa Porcentual para pintar el signo ( % )
      $qTarifa  = "SELECT ";
      $qTarifa .= "taridxxx, ";
      $qTarifa .= "cseidxxx, ";
      $qTarifa .= "fcoidxxx, ";
      $qTarifa .= "regestxx ";
      $qTarifa .= "FROM $cAlfa.lpar0131 ";
      $qTarifa .= "WHERE ";
      $qTarifa .= "cseidxxx = \"{$mDetPedido[$i]['cseidxxx']}\" AND ";
      $qTarifa .= "regestxx = \"ACTIVO\" limit 0,1";
      $xTarifa  = f_MySql("SELECT","",$qTarifa,$xConexion01,"");
      $cSigno   = "";
      if (mysql_num_rows($xTarifa) > 0) {
        $vTarifa = mysql_fetch_array($xTarifa);
        if ($vTarifa['fcoidxxx'] == "009" || $vTarifa['fcoidxxx'] == "010") {
          $cSigno = " %";
        }
      }

      $pdf->SetFont('verdana','',7);
      $pdf->setX($posx);
      $pdf->Row(array(
        $mDetPedido[$i]['serdesxx'],
        $mDetPedido[$i]['cebdesxx'],
        number_format($mDetPedido[$i]['pedbasex'],2,'.',','),
        number_format($mDetPedido[$i]['pedtarix'],2,'.',',') . $cSigno,
        number_format($mDetPedido[$i]['pedcalcu'],2,'.',','),
        number_format($mDetPedido[$i]['pedminix'],2,'.',','),
        number_format($mDetPedido[$i]['pedvlrxx'],2,'.',',')
      ));
      $posy += 4;
    }
  }

  if($posy > $posfin){
    $pdf->Rect($posx,$posyIni,257,($posfin+10)-$posyIni);
    $pdf->AddPage();
    $posx	= 10;
    $posy = $pdf->nPosYIni;
    $pdf->setXY($posx,$posy);
  }

  $pdf->Rect($posx,$posyIni,257,180-$posyIni);
  $cVlrLetras = trim(f_Cifra_Php(number_format($nVlrPedido,2,'.',''),"PESO"));

  $posy = 180;
  $pdf->setXY($posx,$posy);
  $pdf->SetFont('verdanab','',8);
  $pdf->MultiCell(190,4,"VALOR EN LETRAS: " . $cVlrLetras ,0,'L');
  $pdf->setXY($posx+190,$posy);
  $pdf->Cell(30,5,"TOTAL PEDIDO",0,0,'L');
  $pdf->Cell(37,5,number_format($nVlrPedido, 2, '.', ','),0,0,'R');
  $pdf->Rect($posx, $posy, 190, 10);
  $pdf->Rect($posx+190, $posy, 30, 10);
  $pdf->Rect($posx+220, $posy, 37, 10);

  $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
  $pdf->Output($cFile);

  if (file_exists($cFile)){
    chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
  } else {
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }

  echo "<html><script>document.location='$cFile';</script></html>";

  function fnFechaLetras($xFecha){
    if ($xFecha==''){
      $vFecfor = array();
    }else{
      $fano = substr ($xFecha, 0, 4);
      $fdia = substr ($xFecha, 8, 2);
      $fmes_antes = substr ($xFecha, 5, 2);
      if($fmes_antes=='01')
        $fmes="Enero";
      if($fmes_antes=='02')
        $fmes="Febrero";
      if($fmes_antes=='03')
        $fmes="Marzo";
      if($fmes_antes=='04')
        $fmes="Abril";
      if($fmes_antes=='05')
        $fmes="Mayo";
      if($fmes_antes=='06')
        $fmes="Junio";
      if($fmes_antes=='07')
        $fmes="Julio";
      if($fmes_antes=='08')
        $fmes="Agosto";
      if($fmes_antes=='09')
        $fmes="Septiembre";
      if($fmes_antes=='10')
        $fmes="Octubre";
      if($fmes_antes=='11')
        $fmes="Noviembre";
      if($fmes_antes=='12')
        $fmes="Diciembre";

      $vFecFor[0] = $fdia;
      $vFecFor[1] = $fmes;
      $vFecFor[2] = $fano;
    }

    return ($vFecFor);
  }
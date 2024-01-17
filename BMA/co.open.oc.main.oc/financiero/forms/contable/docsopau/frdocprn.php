<?php
  /**
	 * Imprime Comprobante.
	 * --- Descripcion: Permite Imprimir Comprobante.
	 * @author Johana Arboleda Ramos <johana.arboleda@open-eb.co>
	 * @version 002
	 */

  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");

  include("../../../../libs/php/utility.php");

 	##Switch para incluir fuente y clase pdf segun base de datos ##
  define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
  ##Fin Switch para incluir fuente y clase pdf segun base de datos ##

  class PDF extends FPDF {
    function Header() {
      global $cAlfa; global $cPlesk_Skin_Directory; global $OPENINIT; global $_COOKIE; global $_SERVER;
      global $cNombreAduana; global $cNitAduana; global $vCocDat; global $nPosX; global $nPosY; global $nBan;

      ##Impresion de Logos Agencias de Aduanas Financiero Contable ##
      switch($cAlfa){
        case "TEADIMPEXX": // ADIMPEX
        case "DEADIMPEXX": // ADIMPEX
        case "ADIMPEXX": // ADIMPEX
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex5.jpg',192,00,25,20);
        break;
        case "INTERLOG"://MAR Y AIRE - ALCOMEX
        case "TEINTERLOG"://MAR Y AIRE - ALCOMEX
        case "DEINTERLOG"://MAR Y AIRE - ALCOMEX
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/MaryAire.jpg',17,9,25,15);
        break;
        case "ADUACARX": //ADUACARX
        case "DEADUACARX":
        case "TEADUACARX":
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/aduacarga1.png',13,9,35,15);
        break;
        case "ALPOPULX"://ALPOPULAR
        case "TEALPOPULP"://ALPOPULAR PRUEBAS
        case "DEALPOPULX"://ALPOPULAR PRUEBAS
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/alpopul1.jpg',17,9,25,15);
        break;
        case "ETRANSPT"://DIETRICH
        case "TEETRANSPT"://DIETRICH
        case "DEETRANSPT"://DIETRICH
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/dli.jpg',17,9,25,15);
        break;
        case "COLMASXX"://COLMAS
        case "TECOLMASXX"://COLMAS
        case "DECOLMASXX"://COLMAS
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/colmas.jpg',13,9,33,15);
        break;
        case "ADUANAMI"://ADUANAMIENTOS
        case "TEADUANAMI"://ADUANAMIENTOS
        case "DEADUANAMI"://ADUANAMIENTOS
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamientos.jpg',13,9,35,15);
        break;
        case "ADUANERA"://ADUANERA GRANCOLOMBIANA
        case "TEADUANERA"://ADUANERA GRANCOLOMBIANA
        case "DEADUANERA"://ADUANERA GRANCOLOMBIANA
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/aduanera.jpg',13,7,35,17);
        break;
        case "INTERLO2"://INTERLOGISTICA
        case "TEINTERLO2"://INTERLOGISTICA
        case "DEINTERLO2"://INTERLOGISTICA
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/interlogistica.jpg',17,9,25,15);
        break;
        case "GRUPOGLA"://GRUPO GLA
        case "TEGRUPOGLA"://GRUPO GLA
        case "DEGRUPOGLA"://GRUPO GLA
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_grupogla.jpg',19,6,20,19);
        break;
        case "LOGISTSA"://LOGISTSA
        case "TELOGISTSA"://LOGISTSA
        case "DELOGISTSA"://LOGISTSA
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logistica.jpg',11,8,38,14);
        break;
        case "SIACOSIA"://SIACO
        case "TESIACOSIP"://SIACO
        case "DESIACOSIP"://SIACO
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_siaco.jpg',15,6,30,19);
        break;
        case "UPSXXXXX": //UPS
        case "DEUPSXXXXX": //UPS
        case "TEUPSXXXXX": //UPS
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_ups.jpeg',19,6,20,19);
        break;
        case "ADUANAMO": //ADUANAMO
        case "DEADUANAMO": //ADUANAMO
        case "TEADUANAMO": //ADUANAMO
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamo.jpg',15,6,30,19);
        break;
        case "MIRCANAX": //MIRCANAX
        case "DEMIRCANAX": //MIRCANAX
        case "TEMIRCANAX": //MIRCANAX
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_mircana.jpg',11,8,38,15);
        break;
        case "LIDERESX":
        case "DELIDERESX":
        case "TELIDERESX":
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/Logo_Lideres.jpg',19,6,20,19);
        break;
        case "ACODEXXX":
        case "DEACODEXXX":
        case "TEACODEXXX":
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_acodex.jpg',12,8,36,15);
        break;
        case "LOGINCAR":
        case "DELOGINCAR":
        case "TELOGINCAR":
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/Logo_Login_Cargo_Ltda_2.jpg',11,9,38,12);
        break;
        case "TRLXXXXX"://TRLXXXXX
        case "DETRLXXXXX"://TRLXXXXX
        case "TETRLXXXXX"://TRLXXXXX
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma.jpg',9,10,45,15);
        break;
        case "TEADIMPEXX": // ADIMPEX
        case "DEADIMPEXX": // ADIMPEX
        case "ADIMPEXX": // ADIMPEX
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex4.jpg',12,11,36,8);
        break;
        case "ROLDANLO"://ROLDAN
        case "TEROLDANLO"://ROLDAN
        case "DEROLDANLO"://ROLDAN
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',12,6,37,19);
        break;
        case "CASTANOX":
        case "TECASTANOX":
        case "DECASTANOX":
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomartcam.jpg',13,6,35,19);
        break;
        case "ALMACAFE": //ALMACAFE
        case "TEALMACAFE": //ALMACAFE
        case "DEALMACAFE": //ALMACAFE
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalmacafe.jpg',12,8,35,15);
        break;
        case "CARGOADU": //CARGOADU
        case "TECARGOADU": //CARGOADU
        case "DECARGOADU": //CARGOADU
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoCargoAduana.png',12,8,37,17);
        break;
        case "GRUMALCO": //GRUMALCO
        case "TEGRUMALCO": //GRUMALCO
        case "DEGRUMALCO": //GRUMALCO
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',12,8,35,15);
        break;
        case "ALADUANA": //ALADUANA
        case "TEALADUANA": //ALADUANA
        case "DEALADUANA": //ALADUANA
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',16,8,30,15);
        break;
        case "ANDINOSX": //ANDINOSX
        case "TEANDINOSX": //ANDINOSX
        case "DEANDINOSX": //ANDINOSX
          $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoandinos.jpg', 13, $py + 9, 30, 15);
        break;
        case "GRUPOALC": //GRUPOALC
        case "TEGRUPOALC": //GRUPOALC
        case "DEGRUPOALC": //GRUPOALC
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalc.jpg',15,8,30,15);
        break;
        case "AAINTERX": //AAINTERX
        case "TEAAINTERX": //AAINTERX
        case "DEAAINTERX": //AAINTERX
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointernacional.jpg', 14,$py+7, 33, 18);
        break;
        case "AALOPEZX":
        case "TEAALOPEZX":
        case "DEAALOPEZX":
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaalopez.png',17,9,25);
        break;
        case "ADUAMARX": //ADUAMARX
        case "TEADUAMARX": //ADUAMARX
        case "DEADUAMARX": //ADUAMARX
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg',21,6.5,19);
        break;
        case "SOLUCION": //SOLUCION
        case "TESOLUCION": //SOLUCION
        case "DESOLUCION": //SOLUCION
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',14,9,32);
        break;
        case "FENIXSAS": //FENIXSAS
        case "TEFENIXSAS": //FENIXSAS
        case "DEFENIXSAS": //FENIXSAS
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg', 12, 11, 36);
        break;
        case "COLVANXX": //COLVANXX
        case "TECOLVANXX": //COLVANXX
        case "DECOLVANXX": //COLVANXX
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logocolvan.jpg', 12, 8, 36);
        break;
        case "INTERLAC": //INTERLAC
        case "TEINTERLAC": //INTERLAC
        case "DEINTERLAC": //INTERLAC
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointerlace.jpg', 12, 7, 36);
        break;
        case "KARGORUX": //KARGORUX
        case "TEKARGORUX": //KARGORUX
        case "DEKARGORUX": //KARGORUX
          $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logokargoru.jpg', 12, 7, 36);
        break;
        case "ALOGISAS": //LOGISTICA
        case "TEALOGISAS": //LOGISTICA
        case "DEALOGISAS": //LOGISTICA
          $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 11, 7, 47);
        break;
        case "PROSERCO": //PROSERCO
        case "TEPROSERCO": //PROSERCO
        case "DEPROSERCO": // PROSERCO
          $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoproserco.png', 12, 7, 36);
        break;
        case "MANATIAL":
        case "TEMANATIAL":
        case "DEMANATIAL":
          $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomanantial.jpg', 12, 9, 33);
        break;
        case "DSVSASXX":
        case "TEDSVSASXX":
        case "DEDSVSASXX":
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logodsv.jpg',17,9,25,15);
        break;
        case "MELYAKXX":
        case "TEMELYAKXX":
        case "DEMELYAKXX":
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomelyak.jpg',13,9,33,12);
        break;
        case "FEDEXEXP":
        case "DEFEDEXEXP":
        case "TEFEDEXEXP":
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofedexexp.jpg',13,9,33,15);
        break;
        case "EXPORCOM":
        case "DEEXPORCOM":
        case "TEEXPORCOM":
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoexporcomex.jpg',14,9,32,15);
        break;
        default://Logo open
          $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/opentecnologia.JPG',17,9,25,15);
        break;
      }
      ##Impresion de Logos Agencias de Aduanas Financiero Contable ##
      $this->SetFont('verdana','B',8);
      $this->setXY($nPosX+40,$nPosY);
      if ( $cNombreAduana != '' ) {
        if ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") {
          $this->MultiCell(150, 4, $cNombreAduana, 0, 'C');
          $this->Ln(1);
          $this->setX($nPosX+40);
          $this->MultiCell(150, 4, "NIT. " . $cNitAduana, 0, 'C');
        } else {
          $this->MultiCell(150, 4, utf8_decode($cNombreAduana), 0, 'C');
          $this->Ln(1);
          $this->setX($nPosX+40);
          $this->MultiCell(150, 4, "NIT. " . $cNitAduana, 0, 'C');
        }
        $this->Ln(3);
      }
      $this->setX($nPosX+40);
      $this->MultiCell(150, 4, "Documento Soporte en Adquisiciones Efectuadas a No Obligados a Facturar", 0, 'C');

      $this->Ln(5);
      $this->setXY($nPosX,$nPosY+20);
      $this->SetFont('verdana','B',8);
      $this->Cell(190,4,"No. {$vCocDat['resprexx']}-{$vCocDat['comcscxx']} ",0,0,"R");

      $this->Ln(6);
      $this->setX($nPosX);
      $this->SetFont('verdana','B',7);
      $this->Cell(24,4,'PROVEEDOR :',0,0,"L");
      $this->SetFont('verdana','',7);
      $this->Cell(94,4,utf8_decode($vCocDat['PRONOMXX']),0,0,"L");
      $this->SetFont('verdana','B',7);
      $this->Cell(8,4,'NIT :',0,0,"L");
      $this->SetFont('verdana','',7);
      $this->Cell(32,4,"{$vCocDat['terid2xx']}-".f_Digito_Verificacion($vCocDat['terid2xx']),0,0,"L");
      $this->SetFont('verdana','B',7);
      $this->Cell(10,4,'FECHA :',0,0,"L");
      $this->SetFont('verdana','',7);
      $this->Cell(22,4,"{$vCocDat['comfecxx']}",0,0,'R');
      $this->Ln(6);

      if ($nBan == 0) {
        $this->setX($nPosX);
        $this->SetFont('verdana','B',6);
        $this->SetWidths(array('9','17','13','20','69','10','22','22','9'));
        $this->SetAligns(array('L','L','L','L','L','R','R','R','R'));
        $this->Row(array("ITEM",
                        "CUENTA",
                        "CENTRO",
                        "SUBCENTRO",
                        utf8_decode("DESCRIPCIÓN"),
                        "CANT.",
                        "VLR. UNITARIO",
                        "VLR. TOTAL",
                        "MOV"));
        
        $this->SetFont('verdana','',6);
        $this->SetAligns(array('L','L','L','L','L','R','R','R','C'));
      }
      $this->setX($nPosX);
    }//Function Header

    function Footer() {
      global $cPlesk_Skin_Directory; global $vSysStr; global $nPosX; global $nPosY; global $nPosFin; global $vCocDat; global $vResFac;

      $this->setXY($nPosX,$nPosFin);
      $this->Rect($nPosX,$this->GetY(),190,15);
      $this->Line($nPosX+95,$this->GetY(),$nPosX+95, $this->GetY()+15);
      $this->SetFont('verdana','',6);

      $this->setXY($nPosX,$this->GetY()+10);
      $this->Cell(25,3,$vCocDat['USRNOMXX']);
      $this->Cell(25,3,"Elaboro",0,0,'C');
      $this->Cell(20,3,"Revisado",0,0,'C');
      $this->Cell(20,3,"Aprobado",0,0,'C');
      $this->Cell(95,3,"Firma y Sello.",0,0,'C');

      //Calculo el numero de Meses entre Desde y Hasta
      $dFechaInicial 	= date_create($vResFac['resfdexx']);
      $dFechaFinal 		= date_create($vResFac['resfhaxx']);
      $nDiferencia 		= date_diff($dFechaInicial, $dFechaFinal);
      $nMesesVigencia = ( $nDiferencia->y * 12 ) + $nDiferencia->m;
      
      $this->Ln(7);
      $this->setX($nPosX+40);
      $this->MultiCell(110, 3, utf8_decode("Resolución de documento soporte, en adquisiciones efectuadas a no obligados a facturar ".$vResFac['residxxx']." del ".substr($vResFac['resfdexx'], 0, 4).", rango de numeración ".$vResFac['resprexx'].$vResFac['resdesxx']." a ".$vResFac['resprexx'].$vResFac['reshasxx'].", vigencia de $nMesesVigencia meses."), 0, 'C');

    }//function Footer() {

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

  $pdf = new PDF('P','mm','Letter'); 
  $pdf->AddFont('verdana','','');
  $pdf->AddFont('verdana','B','');
  $pdf->AliasNbPages();
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(true,35);

  // Cargo en una Matriz El/Los Comprobantes Seleccionados Para Imprimir
  $mPrn = explode("|",$prints);

  for ($nn=0;$nn<count($mPrn);$nn++) {
    if (strlen($mPrn[$nn]) > 0) {
      $vComp = explode("~",$mPrn[$nn]);
  		$cComId   = $vComp[0];
  		$cComCod  = $vComp[1];
  		$cComCsc  = $vComp[2];
  		$cComCsc2 = $vComp[3];
  		$cComFec  = $vComp[4];
  		$cAno     = substr($cComFec,0,4);

  		$qCocDat  = "SELECT ";
  		$qCocDat .= "$cAlfa.fdsc$cAno.*, ";
  		$qCocDat .= "IF($cAlfa.fpar0116.ccodesxx != \"\",$cAlfa.fpar0116.ccodesxx,\"CENTRO DE COSTO SIN DESCRIPCION\") AS ccodesxx, ";
  		$qCocDat .= "IF($cAlfa.fpar0117.comdesxx != \"\",$cAlfa.fpar0117.comdesxx,\"COMPROBANTE SIN DESCRIPCION\") AS comdesxx, ";
			$qCocDat .= "$cAlfa.fpar0117.comtcoxx  AS comtcoxx, ";
  		$qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX, ";
  		$qCocDat .= "IF($cAlfa.A.CLINOMXX != \"\",$cAlfa.A.CLINOMXX,CONCAT($cAlfa.A.CLINOM1X,\" \",$cAlfa.A.CLINOM2X,\" \",$cAlfa.A.CLIAPE1X,\" \",$cAlfa.A.CLIAPE2X)) AS PRONOMXX, ";
  		$qCocDat .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
  		$qCocDat .= "FROM $cAlfa.fdsc$cAno ";
  		$qCocDat .= "LEFT JOIN $cAlfa.fpar0116 ON $cAlfa.fdsc$cAno.ccoidxxx = $cAlfa.fpar0116.ccoidxxx ";
  		$qCocDat .= "LEFT JOIN $cAlfa.fpar0117 ON $cAlfa.fdsc$cAno.comidxxx = $cAlfa.fpar0117.comidxxx AND $cAlfa.fdsc$cAno.comcodxx = $cAlfa.fpar0117.comcodxx ";
      $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fdsc$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
      $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.fdsc$cAno.terid2xx = $cAlfa.A.CLIIDXXX ";
      $qCocDat .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.fdsc$cAno.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
  		$qCocDat .= "WHERE $cAlfa.fdsc$cAno.comidxxx = \"$cComId\" AND ";
  		$qCocDat .= "$cAlfa.fdsc$cAno.comcodxx = \"$cComCod\" AND ";
  		$qCocDat .= "$cAlfa.fdsc$cAno.comcscxx = \"$cComCsc\" AND ";
  		$qCocDat .= "$cAlfa.fdsc$cAno.comcsc2x = \"$cComCsc2\" LIMIT 0,1";
      //f_Mensaje(__FILE__,__LINE__,$qCocDat);
      $xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
  		if (mysql_num_rows($xCocDat) > 0) {
  		  $vCocDat  = mysql_fetch_assoc($xCocDat);
  		}

  		$qCodDat  = "SELECT DISTINCT ";
      $qCodDat .= "$cAlfa.fdsd$cAno.*, ";
      $qCodDat .= "IF($cAlfa.fpar0119.ctodesxl != \"\",$cAlfa.fpar0119.ctodesxl,IF($cAlfa.fpar0119.ctodesxx != \"\",$cAlfa.fpar0119.ctodesxx,\"CONCEPTO SIN DESCRIPCION\")) AS ctodesxl, ";
      $qCodDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
      $qCodDat .= "FROM $cAlfa.fdsd$cAno ";
      $qCodDat .= "LEFT JOIN $cAlfa.fpar0119 ON $cAlfa.fdsd$cAno.pucidxxx = $cAlfa.fpar0119.pucidxxx AND $cAlfa.fdsd$cAno.ctoidxxx = $cAlfa.fpar0119.ctoidxxx ";
      $qCodDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fdsd$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
  		$qCodDat .= "WHERE $cAlfa.fdsd$cAno.comidxxx = \"$cComId\" AND ";
  		$qCodDat .= "$cAlfa.fdsd$cAno.comcodxx = \"$cComCod\" AND ";
  		$qCodDat .= "$cAlfa.fdsd$cAno.comcscxx = \"$cComCsc\" AND ";
   		$qCodDat .= "$cAlfa.fdsd$cAno.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fdsd$cAno.comseqxx) ASC";
      $xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCodDat."~".mysql_num_rows($xCodDat));
  		if (mysql_num_rows($xCodDat) > 0) {
        //Datos Resolucion de facturacion
        $qResFac  = "SELECT * ";
        $qResFac .= "FROM $cAlfa.fpar0161 ";
        $qResFac .= "WHERE ";
        $qResFac .= "residxxx = \"{$vCocDat['residxxx']}\" AND ";
        $qResFac .= "resprexx = \"{$vCocDat['resprexx']}\" AND ";
        $qResFac .= "restipxx = \"{$vCocDat['restipxx']}\"";
        $xResFac  = f_MySql("SELECT","",$qResFac,$xConexion01,"");
        $vResFac  = mysql_fetch_assoc($xResFac);
        //f_Mensaje(__FILE__,__LINE__,$qResFac." ~ ".mysql_num_rows($xResFac));

        // Cargo la Matriz con los ROWS del Cursor
        $mItems     = array();
        $mOtros     = array();
        $nSubTotal  = 0;
        $nTotal     = 0;
        $nTotIva    = 0;
        $nTotRteFte = 0;
        $nTotRteIva = 0;
        $nTotRteIca = 0;
        $nDec = 0;
        while ($xRCD = mysql_fetch_assoc($xCodDat)) {
          //Total
          $nTotal += ($xRCD['commovxx'] == "C") ? $xRCD['comvlrxx'] : ($xRCD['comvlrxx']*-1);

          //Verificando la cantidad de decimales que se deben dejar
          $nDec = ($nDec > 0) ? $nDec  : ((strpos(($xRCD['comvlrxx']+0),'.') > 0) ? 2 : 0);

          //Totales por tipo
          switch ($xRCD['comctocx']) {
            case 'ITEM':
              $nInd_mItems = count($mItems);
              $mItems[$nInd_mItems] = $xRCD;
              $nSubTotal += ($xRCD['commovxx'] == "C") ? $xRCD['comvlrxx'] : ($xRCD['comvlrxx']*-1);
            break;
            case 'OTROS':
              $nInd_mOtros = count($mOtros);
              $mOtros[$nInd_mOtros] = $xRCD;
              $nSubTotal += ($xRCD['commovxx'] == "C") ? $xRCD['comvlrxx'] : ($xRCD['comvlrxx']*-1);
            break;
            case 'IVA':
              $nTotIva += ($xRCD['commovxx'] == "C") ? $xRCD['comvlrxx'] : ($xRCD['comvlrxx']*-1);
            break;
            case 'RETEFTE':
              $nTotRteFte += ($xRCD['commovxx'] == "D") ? $xRCD['comvlrxx'] : ($xRCD['comvlrxx']*-1);
            break;
            case 'RETEIVA':
              $nTotRteIva += ($xRCD['commovxx'] == "D") ? $xRCD['comvlrxx'] : ($xRCD['comvlrxx']*-1);
            break;
            case 'RETEICA':
              $nTotRteIca += ($xRCD['commovxx'] == "D") ? $xRCD['comvlrxx'] : ($xRCD['comvlrxx']*-1);
            break;
            default:
              //No hace nada
            break;
          }
  			}
  			// Fin de Cargo la Matriz con los ROWS del Cursor
      }
      //BUSCO EL NOMBRE DE LA ADUANA SEGUN LA VARIABLE DEL SISTEMA. SI ESTA ES VACIA NO BUSCO NADA.
      $cNombreAduana = '';
			$cNitAduana    = '';
			if ( $vSysStr['financiero_nit_agencia_aduanas'] != '' ) {
				$qAduana  = "SELECT CLINOMXX ";
				$qAduana .= "FROM $cAlfa.SIAI0150 ";
				$qAduana .= "WHERE ";
				$qAduana .= "CLIIDXXX = \"{$vSysStr['financiero_nit_agencia_aduanas']}\" ";
				$xAduana  = f_MySql("SELECT","",$qAduana,$xConexion01,"");
				if ( mysql_num_rows($xAduana) > 0 ) {
					$xRAD = mysql_fetch_array($xAduana);
					$cNombreAduana = $xRAD['CLINOMXX'];
					$cNitAduana    = $vSysStr['financiero_nit_agencia_aduanas'];
				}
      }
      
      //Posicion X Y inicial
      $nPosX   = 10;
      $nPosY   = 10;
      $nPosFin = 250;

      if (mysql_num_rows($xCocDat) > 0 && mysql_num_rows($xCodDat) > 0) {

        // Siguiente Pagina //
        $pdf->AddPage();

        //Items
        $nItems = 0;
        $nBan   = 0;
        for ($k=0;$k<count($mItems);$k++) {

          $pdf->setX($nPosX);
          $pdf->Row(array(str_pad(($nItems+1),3,"0",STR_PAD_LEFT),
                          $mItems[$k]['pucidxxx'],
                          $mItems[$k]['ccoidxxx'],
                          $mItems[$k]['sccidxxx'],
                          $mItems[$k]['ctodesxl'],
                          number_format($mItems[$k]['comcanxx'],$nDec,',','.'),
                          number_format($mItems[$k]['comvlrun'],$nDec,',','.'),
                          number_format($mItems[$k]['comvlrxx'],$nDec,',','.'),
                          $mItems[$k]['commovxx']));
          $nItems++;
        }
        //Otros
        for ($k=0;$k<count($mOtros);$k++) {
          $pdf->setX($nPosX);
          $pdf->Row(array(str_pad(($nItems+1),3,"0",STR_PAD_LEFT),
                          $mOtros[$k]['pucidxxx'],
                          $mOtros[$k]['ccoidxxx'],
                          $mOtros[$k]['sccidxxx'],
                          $mOtros[$k]['ctodesxl'],
                          "",
                          "",
                          number_format($mOtros[$k]['comvlrxx'],$nDec,',','.'),
                          $mOtros[$k]['commovxx']));
          $nItems++;
        }
        $nBan = 1;

        if ($pdf->GetY() > ($nPosFin-40)) {
          $pdf->AddPage();
        }

        $pdf->Ln(3);
        $pdf->setX($nPosX);
        $pdf->SetFont('verdana','B',6);
        $pdf->Cell(140,4,"",0,0,"R");
        $pdf->Cell(25,4,"SUBTOTAL",0,0,"L");
        $pdf->SetFont('verdana','',6);
        $pdf->Cell(25,4,number_format($nSubTotal,$nDec,',','.'),0,0,"R");

        $pdf->Ln(3);
        $pdf->setX($nPosX);
        $pdf->SetFont('verdana','B',6);
        $pdf->Cell(140,4,"",0,0,"R");
        $pdf->Cell(25,4,"IVA",0,0,"L");
        $pdf->SetFont('verdana','',6);
        $pdf->Cell(25,4,number_format($nTotIva,$nDec,',','.'),0,0,"R");

        $pdf->Ln(3);
        $pdf->setX($nPosX);
        $pdf->SetFont('verdana','B',6);
        $pdf->Cell(140,4,"",0,0,"R");
        $pdf->Cell(25,4,"RETE FTE",0,0,"L");
        $pdf->SetFont('verdana','',6);
        $pdf->Cell(25,4,number_format($nTotRteFte,$nDec,',','.'),0,0,"R");
        
        $pdf->Ln(3);
        $pdf->setX($nPosX);
        $pdf->SetFont('verdana','B',6);
        $pdf->Cell(140,4,"",0,0,"R");
        $pdf->Cell(25,4,"RETE IVA",0,0,"L");
        $pdf->SetFont('verdana','',6);
        $pdf->Cell(25,4,number_format($nTotRteIva,$nDec,',','.'),0,0,"R");

        $pdf->Ln(3);
        $pdf->setX($nPosX);
        $pdf->SetFont('verdana','B',6);
        $pdf->Cell(140,4,"",0,0,"R");
        $pdf->Cell(25,4,"RETE ICA",0,0,"L");
        $pdf->SetFont('verdana','',6);
        $pdf->Cell(25,4,number_format($nTotRteIca,$nDec,',','.'),0,0,"R");

        $pdf->Ln(3);
        $pdf->setX($nPosX);
        $pdf->SetFont('verdana','B',6);
        $pdf->Cell(140,4,"",0,0,"R");
        $pdf->Cell(25,4,"TOTAL",0,0,"L");
        $pdf->SetFont('verdana','',6);
        $pdf->Cell(25,4,number_format($nTotal,$nDec,',','.'),0,0,"R");

        $pdf->Ln(3);
        $pdf->setX($nPosX);
        $pdf->SetFont('verdana','B',6);
        $pdf->Cell(190,4,"OBSERVACIONES GENERALES:",0,0,"L");
        $pdf->SetFont('verdana','',6);
        $pdf->Ln(4);
        $pdf->setX($nPosX);
        $pdf->MultiCell(190, 3, $vCocDat['comobsxx'], 0, 'J');

      } else {
        $pdf->AddPage();
        $pdf->SetXY(40,40);
        $pdf->Cell(200,200,"recibo incompleto verifique (1) ");
      }
    }
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

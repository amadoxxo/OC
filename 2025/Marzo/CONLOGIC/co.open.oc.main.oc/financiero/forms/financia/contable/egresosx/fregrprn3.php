<?php
  /**
  * Imprimir Chequera
  * Este programa permite Imprimir el Comprobante el cheque de Aduanera
  * @author  openTecnologia - Desarrollo
  * @package openComex
  * @version 3.0.0
  */

  include("../../../../libs/php/utility.php");

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

  $pdf = new FPDF('P','mm','Letter');
  $pdf->AddFont('verdana','','verdana.php');
  $pdf->AddFont('verdanab','','verdanab.php');
  $pdf->SetFont('verdana','',8);
  $pdf->SetMargins(0,0,0);
  $pdf->SetAutoPageBreak(0,0);

  // Cargo en una Matriz El/Los Comprobantes Seleccionados Para Imprimir
  $mPrn = explode("|",$prints);
  // Variable para la impresion de los dos comprobantes por hoja

  // Defino las globales de la conexion y de la base de datos
  global $xConexion01; global $cAlfa;
  for ($nn=0;$nn<count($mPrn);$nn++) {
    if (strlen($mPrn[$nn]) > 0) {
      $vComp    = explode("~",$mPrn[$nn]);
      $cComId   = $vComp[0];
      $cComCod  = $vComp[1];
      $cComCsc  = $vComp[2];
      $cComCsc2 = $vComp[3];
      $cComFec  = $vComp[4];
      $cAno     = substr($cComFec,0,4);
      $total    = 0;
      $totalcr  = 0; //variable para mostrar y sumar los creditos ///
      $totaldb  = 0; //variable para mostrar y sumar los creditos ///

      ////// CABECERA 1001 /////
      $qCocDat  = "SELECT ";
      $qCocDat .= "$cAlfa.fcoc$cAno.*, ";

      $qCocDat .= "IF($cAlfa.fpar0117.comdesxx <> \"\",$cAlfa.fpar0117.comdesxx,\"COMPROBANTE SIN DESCRIPCION\") AS comdesxx, ";
      $qCocDat .= "IF($cAlfa.fpar0117.pucidxxx <> \"\",$cAlfa.fpar0117.pucidxxx,\"COMPROBANTE SIN DESCRIPCION\") AS pucidxxx, ";
      $qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX, ";
      $qCocDat .= "IF($cAlfa.A.CLINOMXX <> \"\",$cAlfa.A.CLINOMXX,CONCAT($cAlfa.A.CLINOM1X,' ',$cAlfa.A.CLINOM2X,' ',$cAlfa.A.CLIAPE1X,' ',$cAlfa.A.CLIAPE2X)) AS PRONOMXX, ";
      $qCocDat .= "IF($cAlfa.B.CLINOMXX <> \"\",$cAlfa.B.CLINOMXX,CONCAT($cAlfa.B.CLINOM1X,' ',$cAlfa.B.CLINOM2X,' ',$cAlfa.B.CLIAPE1X,' ',$cAlfa.B.CLIAPE2X)) AS GIRNOMXX, ";
      $qCocDat .= "IF($cAlfa.B.CLIAPE1X <> \"\",$cAlfa.B.CLIAPE1X,\"\") AS CLIAPE1X, ";
      $qCocDat .= "IF($cAlfa.B.CLIAPE2X <> \"\",$cAlfa.B.CLIAPE2X,\"\") AS CLIAPE2X, ";
      $qCocDat .= "IF($cAlfa.B.CLINOM1X <> \"\",$cAlfa.B.CLINOM1X,\"\") AS CLINOM1X, ";
      $qCocDat .= "IF($cAlfa.B.CLINOM2X <> \"\",$cAlfa.B.CLINOM2X,\"\") AS CLINOM2X, ";
      $qCocDat .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
      $qCocDat .= "FROM $cAlfa.fcoc$cAno ";
      $qCocDat .= "LEFT JOIN $cAlfa.fpar0117 ON $cAlfa.fcoc$cAno.comidxxx = $cAlfa.fpar0117.comidxxx AND $cAlfa.fcoc$cAno.comcodxx = $cAlfa.fpar0117.comcodxx ";
      $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
      $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.fcoc$cAno.terid2xx = $cAlfa.A.CLIIDXXX ";
      $qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 AS B ON $cAlfa.fcoc$cAno.terid3xx = $cAlfa.B.CLIIDXXX ";
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
      //////////////////////////////////////////////////////////////////////////

      ////// DETALLE 1002 /////
      $qCodDat  = "SELECT DISTINCT ";
      $qCodDat .= "$cAlfa.fcod$cAno.*, ";
      $qCodDat .= "IF($cAlfa.fpar0119.ctodesxg <> \"\",$cAlfa.fpar0119.ctodesxg,\"CONCEPTO SIN DESCRIPCION\") AS ctodesxg, ";
      $qCodDat .= "IF($cAlfa.fpar0115.pucdesxx <> \"\",$cAlfa.fpar0115.pucdesxx,\"CUENTA SIN DESCRIPCION\") AS pucdesxx, ";
      $qCodDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
      $qCodDat .= "FROM $cAlfa.fcod$cAno ";
      $qCodDat .= "LEFT JOIN $cAlfa.fpar0119 ON $cAlfa.fcod$cAno.pucidxxx = $cAlfa.fpar0119.pucidxxx AND $cAlfa.fcod$cAno.ctoidxxx = $cAlfa.fpar0119.ctoidxxx ";
      $qCodDat .= "LEFT JOIN $cAlfa.fpar0115 ON $cAlfa.fcod$cAno.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) ";
      $qCodDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
      $qCodDat .= "WHERE $cAlfa.fcod$cAno.comidxxx = \"$cComId\" AND ";
      $qCodDat .= "$cAlfa.fcod$cAno.comcodxx = \"$cComCod\" AND ";
      $qCodDat .= "$cAlfa.fcod$cAno.comcscxx = \"$cComCsc\" AND ";
      $qCodDat .= "$cAlfa.fcod$cAno.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cAno.comseqxx) ASC";
      $xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");

      $nFilCod  = mysql_num_rows($xCodDat);

      if ($nFilCod > 0) {
        // Cargo la Matriz con los ROWS del Cursor
        $mCodDat = array ();
        while ($xRCD = mysql_fetch_array($xCodDat)) {
        $nInd_mCodDat = count($mCodDat);
          $mCodDat[$nInd_mCodDat] = $xRCD;
        }
      }
      //f_Mensaje(__FILE__,__LINE__,$qCodDat);

      //////////////////////////////////////////////////////////////////////////
      $cude = 0;
      // Primera Causacion //

      if ($nFilCoc > 0 && $nFilCod > 0) {
        // # DE PAGINAS //
        $cuantas = 1;
        $cude = $nFilCod-1;
        $m12 = $cude - 12;

        if($m12 > 0) {
          $resta = intval($m12 / 12);
          $cuantas+=$resta;
          $resta2 = ($resta*12)-$m12;
          if ($resta2 > 0){
            $cuantas++;
          }
        }

        $arranca = 1;
        $posx1   = 10; //// X /////
        $posxcr  = 150;
        $posxdb  = 180;
        $j=0; // lineas del detalle permitido para cada comprobante de media pagina

        if (count($mCodDat) > 0) {

          // Siguiente Pagina //
          $pdf->AddPage();
          $py    = 87;
          $posy  = 130;

          $pdf->Rect(10,92,198,21);
          $pdf->Line(50,92,50,113);
          $pdf->Line(150,92,150,113);
          $pdf->Line(150,99,208,99);
          $pdf->Line(150,106,208,106);
          $pdf->Line(175,106,175,113);

          switch($cAlfa){
            case "INTERLOG"://MAR Y AIRE - ALCOMEX
            case "TEINTERLOG":
            case "DEINTERLOG":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/MaryAire.jpg',17,96,25,15);
            break;
            case "ADUACARX"://ADUACARGA
            case "TEADUACARX":
            case "DEADUACARX":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/aduacarga1.png',17,96,25,15);
            break;
            case "ALPOPULX"://ALPOPULAR
            case "TEALPOPULP"://ALPOPULAR PRUEBAS
            case "DEALPOPULX"://ALPOPULAR PRUEBAS
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/alpopul1.jpg',17,96,25,15);
            break;
            case "ETRANSPT"://DIETRICH
            case "TEETRANSPT":
            case "DEETRANSPT":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logodli.jpg',11,96,37,12);
            break;
            case "COLMASXX"://COLMAS
            case "TECOLMASXX":
            case "DECOLMASXX":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/colmas.jpg',17,96,25,15);
            break;
            case "ADUANAMI"://ADUANAMIENTOS
            case "TEADUANAMI":
            case "DEADUANAMI":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamientos.jpg',17,96,25,15);
            break;
            case "ADUANERA"://ADUANERA GRANCOLOMBIANA
            case "TEADUANERA":
            case "DEADUANERA":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/aduanera.jpg',17,96,25,15);
            break;
            case "INTERLO2"://INTERLOGISTICA
            case "TEINTERLO2":
            case "DEINTERLO2":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/interlogistica.jpg',17,96,25,15);
            break;
            case "GRUPOGLA"://GRUPO GLA
            case "TEGRUPOGLA"://GRUPO GLA
            case "DEGRUPOGLA"://GRUPO GLA
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_grupogla.jpg',19,93,20,19);
            break;
            case "SIACOSIA"://SIACO
            case "DESIACOSIP"://SIACO
            case "TESIACOSIP"://SIACO
            	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_siaco.jpg',15,93,30,19);
            break;
            case "UPSXXXXX": //UPS
            case "DEUPSXXXXX": //UPS
            case "TEUPSXXXXX": //UPS
            	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_ups.jpeg',19,93,20,19);
            break;
            case "UPSXXXXX": //UPS
            case "DEUPSXXXXX": //UPS
            case "TEUPSXXXXX": //UPS
            	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_ups.jpeg',19,93,20,19);
            break;
						case "ADUANAMO": //ADUANAMO
						case "DEADUANAMO": //ADUANAMO
						case "TEADUANAMO": //ADUANAMO
							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamo.jpg',15,93,30,19);
						break;
						case "MIRCANAX": //MIRCANAX
            case "DEMIRCANAX": //MIRCANAX
            case "TEMIRCANAX": //MIRCANAX
            $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_mircana.jpg',11,95,38,15);
            break;
						case "LIDERESX":
						case "DELIDERESX":
						case "TELIDERESX":
							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/Logo_Lideres.jpg',19,93,20,19);
						break;
						case "ACODEXXX":
						case "DEACODEXXX":
						case "TEACODEXXX":
							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_acodex.jpg',12,8,36,15);
						break;
            case "LOGINCAR":
            case "DELOGINCAR":
            case "TELOGINCAR":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/Logo_Login_Cargo_Ltda_2.jpg',11,9,38,12);
            break;
            case "TRLXXXXX"://TRLXXXXX
            case "DETRLXXXXX"://TRLXXXXX
            case "TETRLXXXXX"://TRLXXXXX
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma1.jpg',11,97,35,10);
            break;
            case "ADIMPEXX":
            case "DEADIMPEXX":
            case "TEADIMPEXX":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex4.jpg',12,99,36,8);
    		    break;
    		    case "ROLDANLO"://ROLDAN
    		    case "DEROLDANLO"://ROLDAN
    		    case "TEROLDANLO"://ROLDAN
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',11,96,37,15);
            break;
            case "CASTANOX":
    		    case "DECASTANOX":
    		    case "TECASTANOX":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomartcam.jpg',11,96,37,15);
            break;
            case "ANDINOSX":
            case "DEANDINOSX":
            case "TEANDINOSX":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoAndinos2.jpeg', 20, 94, 18, 18);
            break;
            case "GRUPOALC": //GRUPOALC
            case "DEGRUPOALC": //GRUPOALC
            case "TEGRUPOALC": //GRUPOALC
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalc.jpg',11,95,37,15);
            break;
            case "ADUAMARX": //ADUAMARX
            case "DEADUAMARX": //ADUAMARX
            case "TEADUAMARX": //ADUAMARX
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg',20,93.5,19);
            break;
            case "SOLUCION": //SOLUCION
            case "DESOLUCION": //SOLUCION
            case "TESOLUCION": //SOLUCION
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',14,96,32);
						break;
						case "FENIXSAS": //FENIXSAS
						case "TEFENIXSAS": //FENIXSAS
						case "DEFENIXSAS": //FENIXSAS
							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg', 12, 11, 36);
						break;
						case "COLVANXX": //COLVANXX
						case "TECOLVANXX": //COLVANXX
						case "DECOLVANXX": //COLVANXX
							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logocolvan.jpg', 12, 4, 36);
						break;
						case "INTERLAC": //INTERLAC
						case "TEINTERLAC": //INTERLAC
						case "DEINTERLAC": //INTERLAC
							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointerlace.jpg', 12, 2, 36);
						break;
						case "DHLEXPRE": //DHLEXPRE
						case "TEDHLEXPRE": //DHLEXPRE
						case "DEDHLEXPRE": //DHLEXPRE
							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg', 12, 2, 36);
						break;
						case "KARGORUX": //KARGORUX
						case "TEKARGORUX": //KARGORUX
						case "DEKARGORUX": //KARGORUX
							$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logokargoru.jpg', 12, 2, 36);
						break;
            case "ALOGISAS": //LOGISTICA
            case "TEALOGISAS": //LOGISTICA
            case "DEALOGISAS": //LOGISTICA
							$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 10.5, 95, 38);
						break;
            case "PROSERCO":
            case "TEPROSERCO":
            case "DEPROSERCO":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoproserco.png', 14, 93, 33);
            break;
            case "MANATIAL":
            case "TEMANATIAL":
            case "DEMANATIAL":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomanantial.jpg', 14, 98, 33);
            break;
            case "DSVSASXX":
            case "TEDSVSASXX":
            case "DEDSVSASXX":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logodsv.jpg',17,96,25,15);
            break;
            case "MELYAKXX":
            case "TEMELYAKXX":
            case "DEMELYAKXX":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomelyak.jpg',13,96,33,12);
            break;
            case "FEDEXEXP":
            case "DEFEDEXEXP":
            case "TEFEDEXEXP":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofedexexp.jpg',13,96,33,15);
            break;
            case "EXPORCOM":
            case "DEEXPORCOM":
            case "TEEXPORCOM":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoexporcomex.jpg',14,95,32,15);
            break;
            case "HAYDEARX":
            case "DEHAYDEARX":
            case "TEHAYDEARX":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logohaydear.jpeg',14,95,35,15);
            break;
            case "CONNECTA":
            case "DECONNECTA":
            case "TECONNECTA":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoconnecta.jpg',17,95,25,15);
            break;
            case "CONLOGIC":
            case "DECONLOGIC":
            case "TECONLOGIC":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoconlogic.jpg',20,96,18,14);
            break;
            default://Logo open
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/opentecnologia.JPG',11,96,35,15);
            break;
          }

          ##Impresion de Logos Agencias de Aduanas Financiero Contable ##

          // DATOS CABECERA //

          $pdf->SetFont('verdanab','',10);
          $pdf->setXY(50,100);

          if(strlen($vCocDat['comdesxx']) > 35){
            $pdf->SetFont('verdanab','',8);
          }

          $pdf->Cell(100,3,$vCocDat['comdesxx'],0,0,'C'); // TITULO DEL FORMULARIO //
          $pdf->SetFont('verdanab','',8);
          $pdf->setXY(50,104);
          $pdf->Cell(100,3,"$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT),0,0,'C');

          $pdf->SetFont('verdana','',7);
          $pdf->setXY(10,116);
          $pdf->Cell(17,3,'CLIENTE');
          $pdf->Cell(2,3,":" );
          $pdf->Cell(25,3,substr($vCocDat['CLINOMXX'],0,55));

          // Nit del Cliente //
          $pdf->setXY(120,116);
          $pdf->Cell(60,3,"NIT: {$vCocDat['teridxxx']}-".f_Digito_Verificacion($vCocDat['teridxxx']));

          $pdf->setXY(170,116);
          $pdf->SetFont('verdanab','',9);
          $pdf->Cell(39,3,"No".(($vCocDat['comcsc3x'] != "") ? $vCocDat['comcsc3x'] : str_pad($vCocDat['comcsc2x'],10,'0',STR_PAD_LEFT)),0,0,"R");

          $pdf->SetFont('verdana','',7);
          $pdf->setXY(10,120);
          $pdf->Cell(17,3,'GIRADO A');
          $pdf->Cell(2,3,":" );
          $vCocDat['GIRNOMAX'] = ($vCocDat['GIRNOMXX'] == "") ? $vCocDat['CLIAPE1X']." ".$vCocDat['CLIAPE2X']." ".$vCocDat['CLINOM1X']." ".$vCocDat['CLINOM2X'] : $vCocDat['GIRNOMXX'];
          $pdf->Cell(25,3,substr($vCocDat['GIRNOMAX'],0,55));

          // Nit del Proveedor //
          $pdf->setXY(120,120);
          $pdf->Cell(60,3,"NIT: {$vCocDat['terid3xx']}-".f_Digito_Verificacion($vCocDat['terid3xx']));

          $pdf->SetFont('verdana','',7);
          $pdf->setXY(178,120);
          $pdf->Cell(31,3,"FECHA:  {$vCocDat['comfecve']}",0,0,'R');

          // DATOS DE DETALLE //
          $pdf->SetFont('verdanab','',7);
          $pdf->setXY($posx1,$py+39);
          $pdf->Cell(8,3,'ITEM',0,0,'C');
          $pdf->Cell(16,3,'CUENTA',0,0,'C');
          $pdf->Cell(13,3,'CENTRO',0,0,'C');
          $pdf->Cell(18,3,'SUBCENTRO',0,0,'C');
          $pdf->Cell(36,3,'DOC.CRUCE',0,0,'C');
          $pdf->Cell(38,3,'DO INFORMATIVO',0,0,'C');
          $pdf->Cell(29,3,'OBSERVACIONES',0,0,'C');
          $pdf->Cell(20,3,'DEBITO',0,0,'R');
          $pdf->Cell(20,3,'CREDITO',0,0,'R');


          for ($k=0;$k<count($mCodDat);$k++) {
            //If para mostrar en la grilla el valor de credito y debito ///
            if ($mCodDat[$k]['commovxx'] == 'C') {
              if ($mCodDat[$k]['pucidxxx'] == $vCocDat['pucidxxx']) {
                $total += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
              }
            }

            if ($mCodDat[$k]['commovxx']=='D') {
              $totaldb += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
            } else {
              $totalcr += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
            }
          }

          $nCan = (count($mCodDat) < 20) ? count($mCodDat) : 20;
          for ($k=0;$k<$nCan;$k++) {
	          $j++;
	          ///////////////// PINTO EL DETALLE /////////////////////////////
	          $pdf->SetFont('verdana','',7);
	          $pdf->setXY($posx1,$posy);
	          $pdf->Cell(8,3,str_pad($j,3,'0',STR_PAD_LEFT),0,0,'C');
	          $pdf->Cell(16,3,$mCodDat[$k]['pucidxxx'],0,0,'L');
	          $pdf->Cell(13,3,$mCodDat[$k]['ccoidxxx'],0,0,'C');
	          $pdf->Cell(18,3,$mCodDat[$k]['sccidxxx'],0,0,'L');
	          $pdf->Cell(36,3,($mCodDat[$k]['comidcxx']."-".$mCodDat[$k]['comcodcx']."-".$mCodDat[$k]['comcsccx']."-".$mCodDat[$k]['comseqcx']),0,0,'L');

	          $pdf->cell(38,3,substr($mCodDat[$k]['comcscc2'],0,22),0,0,'L');

	          $pdf->cell(29,3,substr($mCodDat[$k]['comobsxx'],0,20),0,0,'L');

	          // If para mostrar en la grilla el valor de credito y debito ///
	          $nComVlr = ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
	          if ($mCodDat[$k]['commovxx'] == 'C') {
	            $pdf->Cell(20,3,'');
	            $pdf->Cell(20,3,((strpos(($nComVlr+0),'.') > 0) ? number_format(($nComVlr+0),2,',','.') : number_format(($nComVlr+0),0,',','.')),0,0,'R');
	          } else {
	            $pdf->Cell(20,3,((strpos(($nComVlr+0),'.') > 0) ? number_format(($nComVlr+0),2,',','.') : number_format(($nComVlr+0),0,',','.')),0,0,'R');
	            $pdf->Cell(20,3,'');
	          }
	          ///////////////// PINTO EL DETALLE /////////////////////////////
	          $posy+=3;
	        }
        }

        $pdf->SetFont('verdanab','',7);
        $pdf->SetXY(10,200);
        $pdf->Cell(15,3,'OBSERVACIONES GENERALES :');
        $pdf->SetFont('verdana','',7);
        $pdf->SetXY(10,203);
        $pdf->Cell(200,3,$vCocDat['comobsxx']);
          $py=120;

          $posy+=3;
          $pdf->SetFont('verdanab','',7);
          $pdf->setXY(138,195);
          $pdf->Cell(40,3,'TOTAL : ',0,0,'R');
					$pdf->Cell(30,3,'$'.((strpos(($totalcr+0),'.') > 0) ? number_format(($totalcr+0),2,',','.') : number_format(($totalcr+0),0,',','.')),0,0,'R');

          $py+=1;
          $pdf->Rect(10,212,198,10);
          $pdf->Line(85,212,85,222);
          $pdf->SetFont('verdana','',7);
          $py+=3;
          $pdf->setXY(10, 215);
          $pdf->Cell(25,3,substr($vCocDat['USRNOMXX'],0,20));
          $py+=3;
          $pdf->setXY(10,218);
          $pdf->Cell(25,3,"Elaboro",0,0,'C');
          $pdf->Cell(20,3,"Revisado",0,0,'C');
          $pdf->Cell(20,3,"Aprobado",0,0,'C');
          $pdf->setXY(95,218);
          $pdf->Cell(95,3,"Firma y Sello.",0,0,'C');
          $py+=4;

		    $pdf->setXY(116,13);
				$pdf->SetFont('verdana','',10);
				$vMtzTar = explode("-",$vCocDat['comfecve']);
				$pdf->Cell(05,3,$vMtzTar[0],0,0,"R");

				$pdf->setXY(126,13);
				$pdf->Cell(05,3,$vMtzTar[1],0,0,"R");

				$pdf->setXY(135,13);
				$pdf->Cell(05,3,$vMtzTar[2],0,0,"R");

				$pdf->setXY(142,13);
				$pdf->Cell(30,3,'**'.((strpos(($total+0),'.') > 0) ? number_format(($total+0),2,',','.') : number_format(($total+0),0,',','.')),0,0,'R');

        $pdf->setXY(27,20);
        $pdf->Cell(90,3,"{$vCocDat['GIRNOMXX']} - {$vCocDat['terid3xx']}-$zProDV ");

				$pdf->setXY(140,105);
				$pdf->SetFont('verdana','',10);
		    $vObs = trim(f_Cifra_Php($total,'PESO'));
	      $alinea = explode("~",f_Words($vObs,145));
        $py=30;

        if (count($alinea) > 3) {
          $pdf->SetTextColor(255,0,0);
          $pdf->setXY(15,$py-2);
          $pdf->Cell(150,3,"Resultado superara las tres lineas,verifique");
          $pdf->SetTextColor(0,0,0);
        }	else	{
          for ($n=0;$n<count($alinea);$n++)	{
            $pdf->setXY(15,$py-2);
            $pdf->Cell(127,3,$alinea[$n]);
            $py +=8;
          }
        }

        if (count($mCodDat) > 20) {
          $pdf->AddPage();
          $posy = 20;
          // DATOS DE DETALLE //
          $pdf->SetFont('verdanab','',7);
          $pdf->setXY($posx1,$posy);
          $pdf->Cell(8,3,'ITEM',0,0,'C');
          $pdf->Cell(16,3,'CUENTA',0,0,'C');
          $pdf->Cell(13,3,'CENTRO',0,0,'C');
          $pdf->Cell(18,3,'SUBCENTRO',0,0,'C');
          $pdf->Cell(36,3,'DOC.CRUCE',0,0,'C');
          $pdf->Cell(38,3,'DO INFORMATIVO',0,0,'C');
          $pdf->Cell(29,3,'OBSERVACIONES',0,0,'C');
          $pdf->Cell(20,3,'DEBITO',0,0,'R');
          $pdf->Cell(20,3,'CREDITO',0,0,'R');
          $posy+=3;
          for ($k=20;$k<count($mCodDat);$k++) {
	            $j++;
	            ///////////////// PINTO EL DETALLE /////////////////////////////
	            $pdf->SetFont('verdana','',7);
	            $pdf->setXY($posx1,$posy);
	            $pdf->Cell(8,3,str_pad($j,3,'0',STR_PAD_LEFT),0,0,'C');
	            $pdf->Cell(16,3,$mCodDat[$k]['pucidxxx'],0,0,'L');
	            $pdf->Cell(13,3,$mCodDat[$k]['ccoidxxx'],0,0,'C');
	            $pdf->Cell(18,3,$mCodDat[$k]['sccidxxx'],0,0,'L');
	            $pdf->Cell(36,3,($mCodDat[$k]['comidcxx']."-".$mCodDat[$k]['comcodcx']."-".$mCodDat[$k]['comcsccx']."-".$mCodDat[$k]['comseqcx']),0,0,'L');

	            $pdf->cell(38,3,substr($mCodDat[$k]['comcscc2'],0,22),0,0,'L');

	            $pdf->cell(29,3,substr($mCodDat[$k]['comobsxx'],0,20),0,0,'L');

	            // If para mostrar en la grilla el valor de credito y debito ///
	            $nComVlr = ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
	            if ($mCodDat[$k]['commovxx'] == 'C') {
	              $pdf->Cell(20,3,'');
	              $pdf->Cell(20,3,((strpos(($nComVlr+0),'.') > 0) ? number_format(($nComVlr+0),2,',','.') : number_format(($nComVlr+0),0,',','.')),0,0,'R');
	            } else {
	              $pdf->Cell(20,3,((strpos(($nComVlr+0),'.') > 0) ? number_format(($nComVlr+0),2,',','.') : number_format(($nComVlr+0),0,',','.')),0,0,'R');
	              $pdf->Cell(20,3,'');
	            }
	            ///////////////// PINTO EL DETALLE /////////////////////////////
	            $posy+=3;
	        }
        }
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
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicarse con openTecnologia S.A.");
  }

	echo "<html><script>document.location='$cFile';</script></html>";
?>

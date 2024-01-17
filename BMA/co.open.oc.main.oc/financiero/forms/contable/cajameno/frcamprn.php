<?php
  /**
  * Caja Menor.
  * Este programa permite Visualizar la Informacion de Caja Menor para un periodos especifico.
  * @author  openTecnologia - Desarrollo
  * @package openComex
  * @version 001
  **/

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
  $nMedias = 0;
  // Defino las globales de la conexion y de la base de datos
	global $xConexion01; global $cAlfa;

  for ($nn=0;$nn<count($mPrn);$nn++) {
    if (strlen($mPrn[$nn]) > 0) {
      $vComp = explode("~",$mPrn[$nn]);
  		$cComId   = $vComp[0];
  		$cComCod  = $vComp[1];
  		$cComCsc  = $vComp[2];
  		$cComCsc2 = $vComp[3];
  		$dRegFCre = $vComp[4];
	    $cAno     = substr($dRegFCre,0,4);
  		$nMedias++;

  		$qCmeDat  = "SELECT ";
  		$qCmeDat .= "$cAlfa.fcme$cAno.*, ";
      $qCmeDat .= "IF($cAlfa.fpar0008.sucdesxx <> \"\",$cAlfa.fpar0008.sucdesxx,\"SUCURSAL SIN DESCRIPCION\") AS sucdesxx, ";
      $qCmeDat .= "IF($cAlfa.fpar0116.ccodesxx <> \"\",$cAlfa.fpar0116.ccodesxx,\"CENTRO DE COSTO SIN DESCRIPCION\") AS ccodesxx, ";
  		$qCmeDat .= "IF($cAlfa.fpar0117.comdesxx <> \"\",$cAlfa.fpar0117.comdesxx,\"COMPROBANTE SIN DESCRIPCION\") AS comdesxx, ";
  		$qCmeDat .= "IF($cAlfa.fpar0119.ctodesxm <> \"\",$cAlfa.fpar0119.ctodesxm,\"COMPROBANTE SIN DESCRIPCION\") AS ctodesxm, ";
  		$qCmeDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X,' ',$cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X)) AS CLINOMXX, ";
  		$qCmeDat .= "IF($cAlfa.A.CLINOMXX <> \"\",$cAlfa.A.CLINOMXX,CONCAT($cAlfa.A.CLIAPE1X,' ',$cAlfa.A.CLIAPE2X,' ',$cAlfa.A.CLINOM1X,' ',$cAlfa.A.CLINOM2X)) AS PRONOMXX, ";
  		$qCmeDat .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
  		$qCmeDat .= "FROM $cAlfa.fcme$cAno ";
  		$qCmeDat .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fcme$cAno.ccoidxxx = $cAlfa.fpar0008.ccoidxxx ";
  		$qCmeDat .= "LEFT JOIN $cAlfa.fpar0116 ON $cAlfa.fcme$cAno.ccoidxxx = $cAlfa.fpar0116.ccoidxxx ";
  		$qCmeDat .= "LEFT JOIN $cAlfa.fpar0117 ON $cAlfa.fcme$cAno.comidxxx = $cAlfa.fpar0117.comidxxx AND $cAlfa.fcme$cAno.comcodxx = $cAlfa.fpar0117.comcodxx ";
  		$qCmeDat .= "LEFT JOIN $cAlfa.fpar0119 ON $cAlfa.fcme$cAno.ctoidxxx = $cAlfa.fpar0119.ctoidxxx ";
      $qCmeDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcme$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
      $qCmeDat .= "LEFT JOIN $cAlfa.SIAI0150 AS A ON $cAlfa.fcme$cAno.terid2xx = $cAlfa.A.CLIIDXXX ";
      $qCmeDat .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.fcme$cAno.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
  		$qCmeDat .= "WHERE $cAlfa.fcme$cAno.comidxxx = \"$cComId\" AND ";
  		$qCmeDat .= "$cAlfa.fpar0119.ctocomxx LIKE \"%M~%\" AND ";
  		$qCmeDat .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" AND ";
  		$qCmeDat .= "$cAlfa.fcme$cAno.comcodxx = \"$cComCod\" AND ";
  		$qCmeDat .= "$cAlfa.fcme$cAno.comcscxx = \"$cComCsc\" AND ";
  		$qCmeDat .= "$cAlfa.fcme$cAno.comcsc2x = \"$cComCsc2\" ";
  		$qCmeDat .= "GROUP BY ctodesxm ORDER BY comseqxx ";
  		//f_Mensaje(__FILE__,__LINE__,$qCmeDat);

  		$xCmeDat  = f_MySql("SELECT","",$qCmeDat,$xConexion01,"");
  		$nFilCme  = mysql_num_rows($xCmeDat);

  		$mCodDat = array();
  		if ($nFilCme > 0) {
    		// Cargo la Matriz con los ROWS del Cursor
  			$iA=0;
  			while ($xRCMD = mysql_fetch_array($xCmeDat)) {
  				$mCodDat[$iA] = $xRCMD;
  				$iA++;
  			}
  			// Fin de Cargo la Matriz con los ROWS del Cursor
  		}

			//BUSCO EL NOMBRE DE LA ADUANA SEGUN LA VARIABLE DEL SISTEMA. SI ESTA ES VACIA NO BUSCO NADA.
			if ( $vSysStr['financiero_nit_agencia_aduanas'] != '' ) {
				$qAduana  = "SELECT * ";
				$qAduana .= "FROM $cAlfa.SIAI0150 ";
				$qAduana .= "WHERE ";
				$qAduana .= "CLIIDXXX = \"{$vSysStr['financiero_nit_agencia_aduanas']}\" ";

				$xAduana  = f_MySql("SELECT","",$qAduana,$xConexion01,"");

				if ( mysql_num_rows($xAduana) > 0 ) {
					$xRAD = mysql_fetch_array($xAduana);
					$cNombreAduana = $xRAD['CLINOMXX'];
					$cNitAduana = $vSysStr['financiero_nit_agencia_aduanas'];
				} else {
					$cNombreAduana = '';
					$cNitAduana = '';
				}
			} else {
				$cNombreAduana = '';
				$cNitAduana = '';
			}

  		$cude = 0;
  		// Primera Causacion //
		  if($nMedias % 2) { ///  CABECERA ///
    		if ($nFilCme > 0) {
          // # DE PAGINAS //
  				$cuantas = 1;
  				$cude = $nFilCme-1;
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
  				$posx1 = 10; //// X /////
  				$posxcr = 150;
  				$posxdb = 180;
  				$total = 0;
  				$totalcr = 0; //variable para mostrar y sumar los creditos ///
  				$totaldb = 0; //variable para mostrar y sumar los creditos ///
  				$j=0; // lineas del detalle permitido para cada comprobante de media pagina

    			for ($k=0;$k<count($mCodDat);$k++) {
  					$j++;
  					if ($mCodDat[$k]['commovxx']=='D') {
  						$totaldb += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
  					} else {
  						$totalcr += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
  					}
  					$Usr  = $mCodDat[$k]['USRNOMXX'];
  					$Prov = $mCodDat[$k]['PRONOMXX'];
  					$PNit = $mCodDat[$k]['terid2xx'];

  					if ($j == 1 || (($j % 50) == 0))	{
  				  	 if ($j > 1) {
  							$arranca++;
  						}
  						// Siguiente Pagina //
  						$pdf->AddPage();
  						$py    = 0;
  						$posy  = 44;
  						$posy2 = 44;
  						$posy3 = 70;
  						$posy4 = 271;
  						$posy5 = 273;
  						$posy6 = 275;
  						$posy7 = 277;

  						$pdf->Rect(10,5,198,21);
				      $pdf->Line(50,5,50,26);
				      $pdf->Line(150,5,150,26);
     					$pdf->Line(150,12,208,12);
     					$pdf->Line(150,19,208,19);
     					$pdf->Line(175,19,175,26);

							##Impresion de Logos Agencias de Aduanas Financiero Contable ##
  					 	switch($cAlfa){
  							case "INTERLOG"://MAR Y AIRE - ALCOMEX
  							case "TEINTERLOG"://MAR Y AIRE - ALCOMEX
  							case "DEINTERLOG"://MAR Y AIRE - ALCOMEX
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/MaryAire.jpg',17,9,25,15);
  							break;
  							case "ADUACARX"://ADUACARGA
  							case "TEADUACARX"://ADUACARGA
  							case "DEADUACARX"://ADUACARGA
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/aduacarga.jpg',13,9,35,15);
  							break;
  							case "ALPOPULX"://ALPOPULAR
  							case "TEALPOPULP"://ALPOPULAR PRUEBAS
  							case "DEALPOPULX"://ALPOPULAR PRUEBAS
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/alpopul1.jpg',17,9,25,15);
  							break;
  							case "ETRANSPT"://DIETRICH
  							case "TEETRANSPT"://DIETRICH
  							case "DEETRANSPT"://DIETRICH
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logodli.jpg',11,8,37,12);
  							break;
  							case "COLMASXX"://COLMAS
  							case "TECOLMASXX"://COLMAS
  							case "DECOLMASXX"://COLMAS
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/colmas.jpg',13,9,33,15);
  							break;
  							case "ADUANAMI"://ADUANAMIENTOS
  							case "TEADUANAMI"://ADUANAMIENTOS
  							case "DEADUANAMI"://ADUANAMIENTOS
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamientos.jpg',13,9,35,15);
  							break;
  							case "ADUANERA"://ADUANERA GRANCOLOMBIANA
  							case "TEADUANERA"://ADUANERA GRANCOLOMBIANA
  							case "DEADUANERA"://ADUANERA GRANCOLOMBIANA
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/aduanera.jpg',13,7,35,17);
  							break;
  							case "INTERLO2"://INTERLOGISTICA
  							case "TEINTERLO2"://INTERLOGISTICA
  							case "DEINTERLO2"://INTERLOGISTICA
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/interlogistica.jpg',17,9,25,15);
  							break;
  							case "GRUPOGLA"://GRUPO GLA
                case "TEGRUPOGLA"://GRUPO GLA
                case "DEGRUPOGLA"://GRUPO GLA
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_grupogla.jpg',24,6,20,19);
                break;
                case "LOGISTSA"://LOGISTSA
                case "TELOGISTSA"://LOGISTSA
                case "DELOGISTSA"://LOGISTSA
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logistica.jpg',11,8,38,14);
                break;
                case "SIACOSIA"://SIACO
                case "DESIACOSIP"://SIACO
                case "TESIACOSIP"://SIACO
                	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_siaco.jpg',15,6,30,19);
                break;
                case "UPSXXXXX": //UPS
                case "DEUPSXXXXX": //UPS
                case "TEUPSXXXXX": //UPS
                	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_ups.jpeg',19,6,20,19);
                break;
								case "ADUANAMO": //ADUANAMO
								case "DEADUANAMO": //ADUANAMO
								case "TEADUANAMO": //ADUANAMO
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamo.jpg',15,6,30,19);
								break;
								case "MIRCANAX": //MIRCANAX
	              case "DEMIRCANAX": //MIRCANAX
	              case "TEMIRCANAX": //MIRCANAX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_mircana.jpg',11,10,38,11);
								break;
								case "LIDERESX":
								case "DELIDERESX":
								case "TELIDERESX":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/Logo_Lideres.jpg',19,6,20,19);
								break;
								case "ACODEXXX":
								case "DEACODEXXX":
								case "TEACODEXXX":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_acodex.jpg',12,6,36,19);
								break;
								case "LOGINCAR":
								case "DELOGINCAR":
								case "TELOGINCAR":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/Logo_Login_Cargo_Ltda_2.jpg',11,9,38,12);
								break;
                case "TRLXXXXX": //TRLXXXXX
                case "DETRLXXXXX": //TRLXXXXX
                case "TETRLXXXXX": //TRLXXXXX
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma.jpg',11,11,35,10);
                break;
								case "TEADIMPEXX":
								case "DEADIMPEXX":
								case "ADIMPEXX":
								// case "DEGRUPOGLA":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoAdimpex.jpg',19,6,20,19);
								break;
								case "ROLDANLO"://ROLDAN
	              case "TEROLDANLO"://ROLDAN
	              case "DEROLDANLO"://ROLDAN
                	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',12,6,37,19);
	              break;
								case "CARGOADU"://CARGOADU
	              case "TECARGOADU"://CARGOADU
	              case "DECARGOADU"://CARGOADU
                	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoCargoAduana.png',12,8,37,17);
	              break;
                case "GRUMALCO"://GRUMALCO
	              case "TEGRUMALCO"://GRUMALCO
	              case "DEGRUMALCO"://GRUMALCO
                	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',12,6,37,19);
	              break;
	              case "ALADUANA"://ALADUANA
	              case "TEALADUANA"://ALADUANA
	              case "DEALADUANA"://ALADUANA
	              	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',16,8,30,15);
								break;
								case "ANDINOSX": //ANDINOSX
								case "TEANDINOSX": //ANDINOSX
								case "DEANDINOSX": //ANDINOSX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoandinos.jpg', 13, $py + 9, 36, 15);
								break;
								case "GRUPOALC": //GRUPOALC
                case "TEGRUPOALC": //GRUPOALC
                case "DEGRUPOALC": //GRUPOALC
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalc.jpg',15,8,30,15);
                break;
								case "AAINTERX": //AAINTERX
								case "TEAAINTERX": //AAINTERX
								case "DEAAINTERX": //AAINTERX
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointernacional.jpg', 14,$py+7, 33, 18);
								break;
								case "AALOPEZX":
								case "TEAALOPEZX":
								case "DEAALOPEZX":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaalopez.png',17,$py+8,25);
								break;
								case "ADUAMARX": //ADUAMARX
                case "TEADUAMARX": //ADUAMARX
                case "DEADUAMARX": //ADUAMARX
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg',21,6.5,19);
								break;
								case "SOLUCION": //SOLUCION
								case "TESOLUCION": //SOLUCION
								case "DESOLUCION": //SOLUCION
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',14,9,32);
								break;
								case "FENIXSAS": //FENIXSAS
								case "TEFENIXSAS": //FENIXSAS
								case "DEFENIXSAS": //FENIXSAS
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg',12,11,36);
								break;
								case "COLVANXX": //COLVANXX
								case "TECOLVANXX": //COLVANXX
								case "DECOLVANXX": //COLVANXX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logocolvan.jpg', 12, 8, 36);
								break;
								case "INTERLAC": //INTERLAC
								case "TEINTERLAC": //INTERLAC
								case "DEINTERLAC": //INTERLAC
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointerlace.jpg', 12, 7, 36);
								break;
								case "DHLEXPRE": //DHLEXPRE
								case "TEDHLEXPRE": //DHLEXPRE
								case "DEDHLEXPRE": //DHLEXPRE
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',12,7,35,17);
								break;
								case "KARGORUX": //KARGORUX
								case "TEKARGORUX": //KARGORUX
								case "DEKARGORUX": //KARGORUX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logokargoru.jpg', 12, 7, 35, 17);
								break;
								case "ALOGISAS": //LOGISTICA
								case "TEALOGISAS": //LOGISTICA
								case "DEALOGISAS": //LOGISTICA
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 10.5, 8, 38);
								break;
								case "PROSERCO":
								case "TEPROSERCO":
								case "DEPROSERCO":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoproserco.png', 14, 6, 33);
								break;
                case "MANATIAL":
                case "TEMANATIAL":
                case "DEMANATIAL":
                    $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomanantial.jpg', 14, 9, 33, 10);
                break;
                case "DSVSASXX":
                case "TEDSVSASXX":
                case "DEDSVSASXX":
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logodsv.jpg',17,9,25,15);
                break;
                case "MELYAKXX":
                case "TEMELYAKXX":
                case "DEMELYAKXX":
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomelyak.jpg',13,9,33,12);
                break;
                case "FEDEXEXP":
                case "DEFEDEXEXP":
                case "TEFEDEXEXP":
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofedexexp.jpg',13,7,33,18);
                break;
								case "EXPORCOM":
								case "DEEXPORCOM":
								case "TEEXPORCOM":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoexporcomex.jpg',14,8,32,15);
								break;
  							default://Logo open
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/opentecnologia.JPG',17,9,25,15);
  							break;
  						}
  						##Impresion de Logos Agencias de Aduanas Financiero Contable ##

  						// DATOS CABECERA //
              if ($mCodDat[$k]['regestxx'] == "ACTIVO")	{
                $zEstado = "DEFINITIVO";
              } else {
                $zEstado = "PROVISIONAL";
              }

              if ($mCodDat[$k]['comtpaxx'] == 'PROPIOS')	{
                $zTitDos = 'PAGOS PROPIOS EMPRESA';
              }	elseif ($mCodDat[$k]['comtpaxx'] == 'TERCEROS')	{
                $zTitDos = 'PAGOS X CTA DE TERCEROS';
              }
							if ( $cNombreAduana != '' ) {
								$pdf->SetFont('verdanab','',8);
								$pdf->setXY(50,6);
								if ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") {
                  $pdf->MultiCell(100, 3, $cNombreAduana . "\n NIT. " . $cNitAduana, 0, 'C'); // TITULO DEL FORMULARIO //
                } else {
                  $pdf->MultiCell(100, 3, utf8_decode($cNombreAduana . "\n NIT. " . $cNitAduana), 0, 'C'); // TITULO DEL FORMULARIO //
                }

								$pdf->SetFont('verdanab','',8);
								$pdf->setXY(50,17);
								$pdf->Cell(100,3,$mCodDat[$k]['comdesxx'],0,0,'C'); // TITULO DEL FORMULARIO //
								$pdf->setXY(50,21);
								$pdf->Cell(100,3,$zEstado,0,0,'C'); // TITULO DEL FORMULARIO //

								$pdf->setXY(150,8);
								$pdf->Cell(58,3,$zTitDos,0,0,'C'); // TITULO DEL FORMULARIO //
								$pdf->SetFont('verdanab','',8);
								$pdf->setXY(150,14);
								$pdf->Cell(58,3,"{$mCodDat[$k]['sucdesxx']}-$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT),0,0,'C');
							} else {
								$pdf->SetFont('verdanab','',10);
								$pdf->setXY(50,8);
								$pdf->Cell(100,3,$mCodDat[$k]['comdesxx'],0,0,'C'); // TITULO DEL FORMULARIO //
								$pdf->setXY(50,12);
								$pdf->Cell(100,3,$zEstado,0,0,'C'); // TITULO DEL FORMULARIO //
								$pdf->SetFont('verdanab','',10);
								$pdf->setXY(50,16);
								$pdf->Cell(100,3,$zTitDos,0,0,'C'); // TITULO DEL FORMULARIO //
								$pdf->SetFont('verdanab','',8);
								$pdf->setXY(50,20);
								$pdf->Cell(100,3,"{$mCodDat[$k]['sucdesxx']}-$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT),0,0,'C');
							}

							$pdf->setXY(170,28);
							$pdf->SetFont('verdanab','',9);
							$pdf->Cell(39,3,"{$mCodDat[$k]['sucidxxx']} No ".str_pad($mCodDat[$k]['comcsc2x'],10,'0',STR_PAD_LEFT),0,0,"R");

							$pdf->SetFont('verdana','',7);
       				$pdf->setXY(20,32);

       				$pdf->SetFont('verdana','',7);
							$pdf->setXY(178,32);
							$pdf->Cell(31,3,"FECHA:  {$mCodDat[$k]['comfecxx']}",0,0,'R');

       				if($zEstado == "DEFINITIVO"){
       					//Fecha Vencimiento
       					switch($cAlfa){
       						case "SIACOSIA":
       						case "DESIACOSIP":
       						case "TESIACOSIP":
       							$pdf->SetFont('verdanab','',7);
       							$pdf->setXY(172,12);
       							$pdf->Cell(31,3,"FECHA VENCIMIENTO:  {$mCodDat[$k]['comfecve']}",0,0,'R');
       							break;
       						default:
       							break;
       					}

                // Nit del Cliente //
                $pdf->SetFont('verdana','',7);
                $pdf->setXY(140,28);
                $pdf->Cell(40,3,"NIT: {$mCodDat[$k]['teridxxx']}-".f_Digito_Verificacion($mCodDat[$k]['teridxxx']));
                $pdf->SetFont('verdana','',7);
                $pdf->setXY(10,28);
                $pdf->Cell(17,3,'CLIENTE');
                $pdf->Cell(2,3,":" );
                $pdf->Cell(100,3,substr($mCodDat[$k]['CLINOMXX'],0,65));
                // DATOS DE DETALLE //

                switch ($cAlfa) {
                  case 'TRLXXXXX':
                  case 'DETRLXXXXX':
                  case 'TETRLXXXXX':
                  // case 'DEGRUPOGLA':
                    $nPosPucId  = 17;
                    $nPosCcoId  = 17;
                    $nPosDocCr  = 34;
                    $nPosPucDes = 36;
                    $nPosComObs = 27;
                  break;
                  default:
                    $nPosPucId  = 16;
                    $nPosCcoId  = 13;
                    $nPosDocCr  = 36;
                    $nPosPucDes = 38;
                    $nPosComObs = 29;
                  break;
                }
                $pdf->SetFont('verdanab','',7);
                $pdf->setXY($posx1,$py+39);
                $pdf->Cell(8,3,'ITEM',0,0,'C');
                $pdf->Cell($nPosPucId,3,'CUENTA',0,0,'C');
                $pdf->Cell($nPosCcoId,3,'CENTRO',0,0,'C');
                $pdf->Cell(18,3,'SUBCENTRO',0,0,'C');
                $pdf->Cell($nPosDocCr,3,'DOC.CRUCE',0,0,'C');
                $pdf->Cell($nPosPucDes,3,'DESCRIPCION',0,0,'C');
                $pdf->Cell($nPosComObs,3,'OBSERVACIONES',0,0,'C');
                $pdf->Cell(20,3,'DEBITO',0,0,'R');
                $pdf->Cell(20,3,'CREDITO',0,0,'R');

      					if ($j == $nFilCme+1) { /// INCREMENTA CADA REGISTRO QUE TENGA LA GRILLA  y Pinto la grilla/////
        					break;
      					}
              } elseif($zEstado == "PROVISIONAL") {
                $pdf->SetFont('verdanab','',7);
                $pdf->setXY($posx1,40);
                $pdf->Cell(168,3,'',0,0,'R');
                $pdf->Cell(30,3,'VALOR',0,0,'R');

                ////////////////////////// PINTO EL DETALLE ///////////////////////
                $pdf->SetFont('verdana','',7);
                $pdf->setXY($posx1,$posy);
                $pdf->Cell(30,3,'MOTIVO DEL PAGO');
                $pdf->Cell(2,3,":");
                $pdf->Cell(136,3,substr(trim($mCodDat[$k]['ctoidxxx']).' - '.trim($mCodDat[$k]['ctodesxm']),0,60));
                $pdf->Cell(30,3,number_format((($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']),0,',','.'),0,0,'R');
                $total = 0+(($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']);
              }
            }

            if($zEstado == "DEFINITIVO"){
              ///////////////// PINTO EL DETALLE /////////////////////////////
              $pdf->SetFont('verdana','',7);
              $pdf->setXY($posx1,$posy);
              $pdf->Cell(8,3,str_pad($j,3,'0',STR_PAD_LEFT),0,0,'C');
              $pdf->Cell($nPosPucId,3,$mCodDat[$k]['pucidxxx'],0,0,'L');
              $pdf->Cell($nPosCcoId,3,$mCodDat[$k]['ccoidxxx'],'0',0,'C');
              $pdf->Cell(18,3,$mCodDat[$k]['sccidxxx'],0,0,'L');
              $pdf->Cell($nPosDocCr,3,substr($mCodDat[$k]['comidcxx']."-".$mCodDat[$k]['comcodcx']."-".$mCodDat[$k]['comcsccx']."-".$mCodDat[$k]['comseqcx'],0,21),0,0,'L');
              $pdf->cell($nPosPucDes,3,substr($mCodDat[$k]['ctodesxm'],0,22),0,0,'L');
              $pdf->cell($nPosComObs,3,substr($mCodDat[$k]['comdesxx'],0,16),0,0,'L');

              // If para mostrar en la grilla el valor de credito y debito ///
              if ($mCodDat[$k]['commovxx'] == 'C') {
                $pdf->Cell(20,3,'');
                $pdf->Cell(20,3,number_format((($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']),0,',','.'),0,0,'R');
              } else {
                $pdf->Cell(20,3,number_format((($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']),0,',','.'),0,0,'R');
                $pdf->Cell(20,3,'');
                $total = ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
              }
              $posy+=3;
            }
          }

          $py=120;
          $pdf->SetFont('verdanab','',7);
          $pdf->setXY(134,$py-20);
          $pdf->Cell(40,3,'TOTALES : ',0,0,'R');
          $pdf->Cell(15,3,'$'.number_format($totaldb,0,',','.'),0,0,'R');
          $pdf->Cell(15,3,'$'.number_format($totalcr,0,',','.'),0,0,'R');

  				// Termino de leer el cursor del la $Mat2 //
  				if($j < 20) {// CUANDO SON POCOS REGISTROS  " pregunto se la grilla es menor que 20 para pintar "
  					$pdf->SetFont('verdanab','',7);
  					$pdf->SetXY(10,110);
  					$pdf->Cell(15,3,'OBSERVACIONES GENERALES :');
  					$pdf->SetFont('verdana','',7);
  					$pdf->SetXY(10,113);
  					$pdf->Cell(200,3,$mCodDat[$k]['comobsxx']);
  					$py=120;
  					$posy+=3;
  					$py+=1;
  					$pdf->Rect(10,$py,198,10);
  					$pdf->Line(85,$py,85, $py+10);
  					$pdf->SetFont('verdana','',7);
  					$py+=3;
  					$pdf->setXY(10, $py);
  					$pdf->Cell(25,3,$Usr);
  					$py+=3;
  					$pdf->setXY(10,$py);
  					$pdf->Cell(25,3,"Elaboro",0,0,'C');
  					$pdf->Cell(20,3,"Revisado",0,0,'C');
  					$pdf->Cell(20,3,"Aprobado",0,0,'C');
  					$pdf->setXY(95,$py);
  					$pdf->setXY(85,123);
					  $pdf->Cell(160,3,substr("BENEFICIARIO: _________________________________________________________",0,300));
					  $pdf->setXY(85,126);
					  $pdf->Cell(160,3,substr("                       ".$Prov.'   NIT: '.number_format($PNit,0,',','.').' - '.f_Digito_Verificacion($PNit),0,300));
  					$py+=4;
  					$py+=6;
  					$pdf->Line(10, $py,208, $py);
  				}	else	{
  					$pdf->SetFont('verdanab','',7);
  					$posy+=2;
  					$pdf->SetXY(10,$posy);
  					$pdf->Cell(15,3,'OBSERVACIONES GENERALES :');
  					$posy+=3;
  					$pdf->SetFont('verdana','',7);
  					$pdf->SetXY(10,$posy);
  					$pdf->Cell(200,3,substr($mCodDat[$k]['comobsxx'],0,60));
  					$posy+=3;
  					$posy+=8;
  					$pdf->Rect(10,$posy,198,10);
  					$pdf->Line(85,$posy,85,$posy+10);
  					$pdf->SetFont('verdana','',7);
  					$posy+=3;
  					$pdf->setXY(10, $posy);
  					$pdf->Cell(25,3,$Usr);
  					$posy+=3;
  					$pdf->setXY(10,$posy);
  					$pdf->Cell(25,3,"Elaboro",0,0,'C');
  					$pdf->Cell(20,3,"Revisado",0,0,'C');
  					$pdf->Cell(20,3,"Aprobado",0,0,'C');
  					$pdf->setXY(95,$posy);
  					$pdf->setXY(85,123);
  				}
  				// Segunda Causacion //
    		} else {
    		  $pdf->AddPage();
  				$pdf->SetXY(40,40);
  				$pdf->Cell(200,200,"recibo incompleto verifique (1) ");
    		}
  		} else { ///  CABECERA ////
  		  if ($nFilCme > 0) {
  				//// Nueva Causacion ////
  				$P=0; //
  				$total1 = 0;
  				$totalcr1 = 0; //variable para mostrar y sumar los creditos ///
  				$totaldb1 = 0; //variable para mostrar y sumar los creditos ///

  				for ($k=0;$k<count($mCodDat);$k++) {
	          $P++;
	          if ($mCodDat[$k]['commovxx']=='D') {
	            $totaldb1 += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
	          } else {
	            $totalcr1 += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
	          }

	          $Usr  = $mCodDat[$k]['USRNOMXX'];
	          $Prov = $mCodDat[$k]['PRONOMXX'];
	          $PNit = $mCodDat[$k]['terid2xx'];

	          if ($P == 1 || (($P % 25) == 0))	{
	            if ($P > 1) {
	              $arranca++;
	            }
	            $py+=6;
	            $posyi=178;
	            $posyi1=183;
	            $posy=192;

	            $pdf->Rect(10,$py,198,21);
	            $pdf->Line(50,$py,50,164);
	            $pdf->Line(150,$py,150,164);
	            $pdf->Line(150,$py+7,208,$py+7);
	            $pdf->Line(150,$py+14,208,$py+14);
	            $pdf->Line(175,$py+14,175,$py+21);

				      ##Impresion de Logos Agencias de Aduanas Financiero Contable ##
						 	switch($cAlfa){
								case "INTERLOG"://MAR Y AIRE - ALCOMEX
								case "TEINTERLOG"://MAR Y AIRE - ALCOMEX
								case "DEINTERLOG"://MAR Y AIRE - ALCOMEX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/MaryAire.jpg',17,$py+4,25,15);
								break;
								case "ADUACARX"://ADUACARGA
								case "TEADUACARX"://ADUACARGA
								case "DEADUACARX"://ADUACARGA
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/aduacarga.jpg',13,$py+4,35,15);
								break;
								case "ALPOPULX"://ALPOPULAR
								case "TEALPOPULP"://ALPOPULAR PRUEBAS
								case "DEALPOPULX"://ALPOPULAR PRUEBAS
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/alpopul1.jpg',17,$py+4,25,15);
								break;
								case "ETRANSPT"://DIETRICH
								case "TEETRANSPT"://DIETRICH
								case "DEETRANSPT"://DIETRICH
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logodli.jpg',11,$py+8,37,12);
								break;
								case "COLMASXX"://COLMAS
								case "TECOLMASXX"://COLMAS
								case "DECOLMASXX"://COLMAS
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/colmas.jpg',13,$py+4,33,15);
								break;
								case "ADUANAMI"://ADUANAMIENTOS
								case "TEADUANAMI"://ADUANAMIENTOS
								case "DEADUANAMI"://ADUANAMIENTOS
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamientos.jpg',13,$py+4,35,15);
								break;
								case "ADUANERA"://ADUANERA GRANCOLOMBIANA
								case "TEADUANERA"://ADUANERA GRANCOLOMBIANA
								case "DEADUANERA"://ADUANERA GRANCOLOMBIANA
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/aduanera.jpg',13,$py+1,35,17);
								break;
								case "INTERLO2"://INTERLOGISTICA
								case "TEINTERLO2"://INTERLOGISTICA
								case "DEINTERLO2"://INTERLOGISTICA
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/interlogistica.jpg',17,$py+4,25,15);
								break;
								case "GRUPOGLA"://GRUPO GLA
                case "TEGRUPOGLA"://GRUPO GLA
                case "DEGRUPOGLA"://GRUPO GLA
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_grupogla.jpg',24,$py+1,20,19);
                break;
                case "LOGISTSA"://LOGISTSA
                case "TELOGISTSA"://LOGISTSA
                case "DELOGISTSA"://LOGISTSA
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logistica.jpg',11,$py+2,38,14);
                break;
                case "SIACOSIA"://SIACO
                case "DESIACOSIP"://SIACO
                case "TESIACOSIP"://SIACO
                	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_siaco.jpg',15,$py+1,30,19);
                break;
                case "UPSXXXXX": //UPS
                case "DEUPSXXXXX": //UPS
                case "TEUPSXXXXX": //UPS
                	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_ups.jpeg',19,$py+1,20,19);
                break;
								case "ADUANAMO": //ADUANAMO
								case "DEADUANAMO": //ADUANAMO
								case "TEADUANAMO": //ADUANAMO
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamo.jpg',15,$py+1,30,19);
								break;
								case "MIRCANAX": //MIRCANAX
	              case "DEMIRCANAX": //MIRCANAX
	              case "TEMIRCANAX": //MIRCANAX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_mircana.jpg',11,$py+5,38,11);
								break;
								case "LIDERESX":
								case "DELIDERESX":
								case "TELIDERESX":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/Logo_Lideres.jpg',24,$py+1,20,19);
								break;
								case "ACODEXXX":
								case "DEACODEXXX":
								case "TEACODEXXX":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_acodex.jpg',12,$py+3,36,15);
								break;
								case "LOGINCAR":
								case "DELOGINCAR":
								case "TELOGINCAR":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/Logo_Login_Cargo_Ltda_2.jpg',11,$py+4,38,12);
								break;
								case "TRLXXXXX": //TRLXXXXX
                case "DETRLXXXXX": //TRLXXXXX
                case "TETRLXXXXX": //TRLXXXXX
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma.jpg',11,$py+1,35,10);
                break;
								case "TEADIMPEXX":
								case "DEADIMPEXX":
								case "ADIMPEXX":
								// case "DEGRUPOGLA":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoAdimpex.jpg',19,$py+1,20,19);
								break;
								case "ROLDANLO"://ROLDAN
	              case "TEROLDANLO"://ROLDAN
	              case "DEROLDANLO"://ROLDAN
                	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',12,$py+1,37,19);
	              break;
								case "CARGOADU"://CARGOADU
	              case "TECARGOADU"://CARGOADU
	              case "DECARGOADU"://CARGOADU
                	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoCargoAduana.png',12,$py+3,37,17);
	              break;
                case "GRUMALCO"://GRUMALCO
                case "TEGRUMALCO"://GRUMALCO
                case "DEGRUMALCO"://GRUMALCO
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',12,$py+1,37,19);
                break;
								case "ALADUANA"://ALADUANA
								case "TEALADUANA"://ALADUANA
								case "DEALADUANA"://ALADUANA
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',16,$py+4,30,15);
								break;
								case "ANDINOSX": //ANDINOSX
								case "TEANDINOSX": //ANDINOSX
								case "DEANDINOSX": //ANDINOSX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoandinos.jpg', 13, $py + 9, 36, 15);
								break;
								case "GRUPOALC": //GRUPOALC
                case "TEGRUPOALC": //GRUPOALC
                case "DEGRUPOALC": //GRUPOALC
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalc.jpg',15,$py+3,30,15);
                break;
								case "AAINTERX": //AAINTERX
								case "TEAAINTERX": //AAINTERX
								case "DEAAINTERX": //AAINTERX
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointernacional.jpg', 14,$py+7, 33, 18);
								break;
								case "AALOPEZX":
								case "TEAALOPEZX":
								case "DEAALOPEZX":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaalopez.png',17,$py+3,25);
								break;
								case "SOLUCION": //SOLUCION
								case "TESOLUCION": //SOLUCION
								case "DESOLUCION": //SOLUCION
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',14,$py+4,32);
								break;
								case "FENIXSAS": //FENIXSAS
								case "TEFENIXSAS": //FENIXSAS
								case "DEFENIXSAS": //FENIXSAS
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg',12,$py+4,36);
								break;
								case "COLVANXX": //COLVANXX
								case "TECOLVANXX": //COLVANXX
								case "DECOLVANXX": //COLVANXX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logocolvan.jpg', 12, $py+1, 36);
								break;
								case "INTERLAC": //INTERLAC
								case "TEINTERLAC": //INTERLAC
								case "DEINTERLAC": //INTERLAC
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointerlace.jpg', 12, $py+1, 36);
								break;
								case "DHLEXPRE": //DHLEXPRE
								case "TEDHLEXPRE": //DHLEXPRE
								case "DEDHLEXPRE": //DHLEXPRE
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',12,$py+2,35,17);
								break;
								case "KARGORUX": //KARGORUX
								case "TEKARGORUX": //KARGORUX
								case "DEKARGORUX": //KARGORUX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logokargoru.jpg', 12, $py + 2, 35, 17);
								break;
								case "ALOGISAS": //LOGISTICA
								case "TEALOGISAS": //LOGISTICA
								case "DEALOGISAS": //LOGISTICA
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logologisticasas.jpg', 12, $py + 2, 35, 17);
								break;
								case "PROSERCO":
								case "TEPROSERCO":
								case "DEPROSERCO":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoproserco.png', 14, $py + 1, 33);
								break;
                case "MANATIAL":
                case "TEMANATIAL":
                case "DEMANATIAL":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomanantial.jpg', 14, $py + 4, 32, 10);
								break;
                case "DSVSASXX":
                case "TEDSVSASXX":
                case "DEDSVSASXX":
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logodsv.jpg',17,$py+4,25,15);
                break;
                case "MELYAKXX":
                case "TEMELYAKXX":
                case "DEMELYAKXX":
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomelyak.jpg',13,$py+4,33,12);
                break;
                case "FEDEXEXP":
                case "DEFEDEXEXP":
                case "TEFEDEXEXP":
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofedexexp.jpg',13,$py+4,33,15);
                break;
								case "EXPORCOM":
								case "DEEXPORCOM":
								case "TEEXPORCOM":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoexporcomex.jpg',14,$py+4,32,15);
								break;
								default://Logo open
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/opentecnologia.JPG',17,$py+4,25,15);
								break;
							}
							##Impresion de Logos Agencias de Aduanas Financiero Contable ##

							// DATOS CABECERA //
							if ($mCodDat[$k]['regestxx'] == "ACTIVO")	{
								$zEstado = "DEFINITIVO";
							} else {
								$zEstado = "PROVISIONAL";
							}

							if ($mCodDat[$k]['comtpaxx'] == 'PROPIOS')	{
								$zTitDos = 'PAGOS PROPIOS EMPRESA';
							}	elseif ($mCodDat[$k]['comtpaxx'] == 'TERCEROS')	{
								$zTitDos = 'PAGOS X CTA DE TERCEROS';
							}

							if ( $cNombreAduana != '' ) {
								$pdf->SetFont('verdanab','',8);
								$pdf->setXY(50,$py+2);
								if ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") {
                  $pdf->MultiCell(100, 3, $cNombreAduana . "\n NIT. " . $cNitAduana, 0, 'C'); // TITULO DEL FORMULARIO //
                } else {
                  $pdf->MultiCell(100, 3, utf8_decode($cNombreAduana . "\n NIT. " . $cNitAduana), 0, 'C'); // TITULO DEL FORMULARIO //
                }

								$pdf->SetFont('verdanab','',8);
								$pdf->setXY(50,$py+13);
								$pdf->Cell(100,3,$mCodDat[$k]['comdesxx'],0,0,'C'); // TITULO DEL FORMULARIO //
								$pdf->setXY(50,$py+16);
								$pdf->Cell(100,3,$zEstado,0,0,'C'); // TITULO DEL FORMULARIO //

								$pdf->setXY(150,$py+2);
								$pdf->Cell(58,3,$zTitDos,0,0,'C'); // TITULO DEL FORMULARIO //
								$pdf->SetFont('verdanab','',8);
								$pdf->setXY(150, $py+9);
								$pdf->Cell(58,3,"{$mCodDat[$k]['sucdesxx']}-$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT),0,0,'C');
							} else {
					      $pdf->SetFont('verdanab','',10);
		   					$pdf->setXY(50,$py+6);
		   					$pdf->Cell(100,3,$mCodDat[$k]['comdesxx']." ".$zEstado,0,0,'C'); // TITULO DEL FORMULARIO //
		   					$pdf->SetFont('verdanab','',10);
		   					$pdf->setXY(50,$py+10);
					      $pdf->Cell(100,3,$zTitDos,0,0,'C'); // TITULO DEL FORMULARIO //
		   					$pdf->SetFont('verdanab','',8);
		   					$pdf->setXY(50,$py+14);
		   					$pdf->Cell(100,3,"{$mCodDat[$k]['sucdesxx']}-$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT),0,0,'C'); // TITULO DEL FORMULARIO //
							}

	   					$pdf->setXY(170,$py+23);
							$pdf->SetFont('verdanab','',9);
							$pdf->Cell(39,3,"{$mCodDat[$k]['sucidxxx']} No ".str_pad($mCodDat[$k]['comcsc2x'],10,'0',STR_PAD_LEFT),0,0,"R");

	            $pdf->SetFont('verdana','',7);
							$pdf->setXY(178,$py+27);
							$pdf->Cell(31,3,"FECHA:  {$mCodDat[$k]['comfecxx']}",0,0,'R');

	     				// DATOS DE DETALLE //
	     				if($zEstado == "DEFINITIVO"){
	     					//Fecha Vencimiento
	     					switch($cAlfa){
	     						case "SIACOSIA":
	     						case "DESIACOSIP":
	     						case "TESIACOSIP":
	     							$pdf->SetFont('verdanab','',7);
	     							$pdf->setXY(174,$py+10);
	     							$pdf->Cell(30,3,"FECHA VENCIMIENTO:  {$mCodDat[$k]['comfecve']}",0,0,'R');
	     							break;
	     						default:
	     							break;
	     					}

	       				// Cliente //
	       				$pdf->SetFont('verdana','',7);
	       				$pdf->setXY(10,$py+23);
	       				$pdf->Cell(17,3,'CLIENTE');
	       				$pdf->Cell(2,3,":" );
	       				$pdf->Cell(100,3,substr($mCodDat[$k]['CLINOMXX'],0,55));
	       				// Nit del Cliente //
	       				$pdf->setXY(140,$py+23);
	       				$pdf->Cell(40,3,"NIT: {$mCodDat[$k]['teridxxx']}-".f_Digito_Verificacion($mCodDat[$k]['teridxxx']));
	  						$pdf->SetFont('verdanab','',7);
	  						$pdf->setXY($posx1,$py+33);
                switch ($cAlfa) {
                  case 'TRLXXXXX':
                  case 'DETRLXXXXX':
                  case 'TETRLXXXXX':
                  // case 'DEGRUPOGLA':
                    $nPosPucId  = 17;
                    $nPosCcoId  = 17;
                    $nPosDocCr  = 34;
                    $nPosPucDes = 36;
                    $nPosComObs = 27;
                  break;
                  default:
                    $nPosPucId  = 16;
                    $nPosCcoId  = 13;
                    $nPosDocCr  = 36;
                    $nPosPucDes = 38;
                    $nPosComObs = 29;
                  break;
                }
	  						$pdf->Cell(8,3,'ITEM',0,0,'C');
	  						$pdf->Cell($nPosPucId,3,'CUENTA',0,0,'C');
	  						$pdf->Cell($nPosCcoId,3,'CENTRO',0,0,'C');
	  						$pdf->Cell(18,3,'SUBCENTRO',0,0,'C');
	  						$pdf->Cell($nPosDocCr,3,'DOC.CRUCE',0,0,'C');
	  						$pdf->Cell($nPosPucDes,3,'DESCRIPCION',0,0,'C');
	  						$pdf->Cell($nPosComObs,3,'OBSERVACIONES',0,0,'C');
	  						$pdf->Cell(20,3,'DEBITO',0,0,'R');
	  						$pdf->Cell(20,3,'CREDITO',0,0,'R');

	              if ($P == $nFilCme+1) { /// INCREMENTA CADA REGISTRO QUE TENGA LA GRILLA  y Pinto la grilla/////
	                break;
	              }
	     				} elseif ($zEstado == "PROVISIONAL") {
    				  	// Cliente //
         				$pdf->SetFont('verdana','',7);
         				$pdf->setXY(10,$py+23);
         				$pdf->Cell(25,3,'MOTIVO DEL PAGO');
         				$pdf->Cell(2,3,":" );
         				$pdf->Cell(25,3,substr(trim($mCodDat[$k]['ctoidxxx']).' - '.trim($mCodDat[$k]['ctodesxm']),0,25));
    						$pdf->SetFont('verdanab','',7);
    						$pdf->setXY($posx1,$py+33);
    						$pdf->Cell(8,3,'',0,0,'C');
    						$pdf->Cell(16,3,'',0,0,'C');
    						$pdf->Cell(13,3,'',0,0,'C');
    						$pdf->Cell(18,3,'',0,0,'C');
    						$pdf->Cell(36,3,'',0,0,'C');
    						$pdf->Cell(38,3,'',0,0,'C');
    						$pdf->Cell(29,3,'OBSERVACIONES',0,0,'C');
    						$pdf->Cell(20,3,'',0,0,'R');
    						$pdf->Cell(20,3,'VALOR',0,0,'R');

    						/////////PINTO EL DETALLE//////////////////////////////////////
    						$pdf->SetFont('verdana','',7);
      					$pdf->setXY($posx1,$posy-11);
      					$pdf->Cell(8,3,'',0,0,'C');
      					$pdf->Cell(16,3,'',0,0,'L');
      					$pdf->Cell(13,3,'',0,0,'L');
      					$pdf->Cell(18,3,'',0,0,'L');
      					$pdf->Cell(36,3,'',0,0,'L');
      					$pdf->cell(38,3,'',0,0,'L');
      					$pdf->cell(29,3,substr($mCodDat[$k]['comobsxx'],0,20),0,0,'L');
      				  $pdf->Cell(20,3,'');
      					$pdf->Cell(20,3,number_format((($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']),0,',','.'),0,0,'R');
      					$tot =0+(($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']);
      					$posy+=3;
      					$py1=260;
      					$pdf->SetFont('verdanab','',7);
      					$pdf->setXY(134,$py1-20);
      					$pdf->Cell(40,3,'TOTAL : ',0,0,'R');
      					$pdf->Cell(15,3,'',0,0,'R');
      					$pdf->Cell(15,3,'$'.number_format($tot,0,',','.'),0,0,'R');
      					$total1 = 0+(($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']);
    					}
						}

						if($zEstado == "DEFINITIVO"){
							///////////////// PINTO EL DETALLE /////////////////////////////
							$pdf->SetFont('verdana','',7);
							$pdf->setXY($posx1,$posy-11);
							$pdf->Cell(8,3,str_pad($P,3,'0',STR_PAD_LEFT),0,0,'C');
							$pdf->Cell($nPosPucId,3,$mCodDat[$k]['pucidxxx'],0,0,'L');
							$pdf->Cell($nPosCcoId,3,$mCodDat[$k]['ccoidxxx'],0,0,'C');
							$pdf->Cell(18,3,$mCodDat[$k]['sccidxxx'],0,0,'L');
							$pdf->Cell($nPosDocCr,3,($mCodDat[$k]['comidcxx']."-".$mCodDat[$k]['comcodcx']."-".$mCodDat[$k]['comcsccx']."-".$mCodDat[$k]['comseqcx']),0,0,'L');
							$pdf->cell($nPosPucDes,3,substr($mCodDat[$k]['ctodesxm'],0,22),0,0,'L');
							$pdf->cell($nPosComObs,3,substr($mCodDat[$k]['comobsxx'],0,20),0,0,'L');

							// If para mostrar en la grilla el valor de credito y debito ///
							if ($mCodDat[$k]['commovxx'] == 'C') {
								$pdf->Cell(20,3,'');
								$pdf->Cell(20,3,number_format((($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']),0,',','.'),0,0,'R');
							} else {
								$pdf->Cell(20,3,number_format((($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']),0,',','.'),0,0,'R');
								$pdf->Cell(20,3,'');
								$total1 = ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
							}
							$posy+=3;
						}
	        }

					$posy+=3;
					$py1=260;
					$pdf->SetFont('verdanab','',7);
					$pdf->setXY(134,$py1-20);
					$pdf->Cell(40,3,'TOTALES : ',0,0,'R');
					$pdf->Cell(15,3,'$'.number_format($totaldb1,0,',','.'),0,0,'R');
					$pdf->Cell(15,3,'$'.number_format($totalcr1,0,',','.'),0,0,'R');
					$pdf->setXY(95,$py1-3);

					// Termino de leer el cursor del la $Mat2 //
					if($P < 20) {// CUANDO SON POCOS REGISTROS  " pregunto si la grilla es menor que 20 para pintar "
						$pdf->SetFont('verdanab','',7);
						$pdf->SetXY(10,250);
						$pdf->Cell(15,3,'OBSERVACIONES GENERALES :');
						$pdf->SetFont('verdana','',7);
						$pdf->SetXY(10,255);
						$pdf->Cell(50,3,$mCodDat[$k]['comobsxx']);
						$py1=260;

						$posy+=3;
						$py1+=1;
						$pdf->Rect(10,$py1,198,10);
						$pdf->Line(85,$py1,85,$py1+10);
						$pdf->SetFont('verdana','',7);
						$py1+=3;
						$pdf->setXY(10, $py1);
						$pdf->Cell(25,3,$Usr);
						$py1+=3;
						$pdf->setXY(10,$py1);
						$pdf->Cell(25,3,"Elaboro",0,0,'C');
						$pdf->Cell(20,3,"Revisado",0,0,'C');
						$pdf->Cell(20,3,"Aprobado",0,0,'C');
						$pdf->setXY(95,$py1-4);
						$pdf->Cell(95,3,substr("BENEFICIARIO: _________________________________________________________",0,300),0,0,'C');
						$pdf->SetFont('verdana','',7);
						$pdf->setXY(95,$py1-1);
						$pdf->Cell(160,3,substr("                       ".$Prov.'   NIT: '.number_format($PNit,0,',','.').' - '.f_Digito_Verificacion($PNit),0,300));
						$py1+=4;
					} // Queda pendiente $j > 20
  			} else {
  				$pdf->AddPage();
  				$pdf->Cell(100,20,"recibo incompleto verifique (2)");
  			}
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
  //$pdf->Output();
?>

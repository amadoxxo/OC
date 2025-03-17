<?php
  /**
	 * Imprime Comprobante.
	 * --- Descripcion: Permite Imprimir Comprobante.
	 * @author Johana Arboleda Ramos <johana.arboleda@opentecnologia.com.co>
	 * @version 001
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
  $nMedias=1;
  // Defino las globales de la conexion y de la base de datos
  global $xConexion01; global $cAlfa;

  for ($nn=0;$nn<count($mPrn);$nn++) {
    if (strlen($mPrn[$nn]) > 0) {
      $vComp = explode("~",$mPrn[$nn]);
  		$cComId   = $vComp[0];
  		$cComCod  = $vComp[1];
  		$cComCsc  = $vComp[2];
  		$cComCsc2 = $vComp[3];
  		$cComFec = $vComp[4];
	    $cAno     = substr($cComFec,0,4);

  		////// CABECERA 1001 /////
  		$qCocDat  = "SELECT ";
  		$qCocDat .= "$cAlfa.fcoc$cAno.*, ";
  		$qCocDat .= "IF($cAlfa.fpar0116.ccodesxx <> \"\",$cAlfa.fpar0116.ccodesxx,\"CENTRO DE COSTO SIN DESCRIPCION\") AS ccodesxx, ";
  		$qCocDat .= "IF($cAlfa.fpar0117.comdesxx <> \"\",$cAlfa.fpar0117.comdesxx,\"COMPROBANTE SIN DESCRIPCION\") AS comdesxx, ";
			$qCocDat .= "$cAlfa.fpar0117.comtcoxx  AS comtcoxx, ";
  		$qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX, ";
  		$qCocDat .= "IF($cAlfa.A.CLINOMXX <> \"\",$cAlfa.A.CLINOMXX,CONCAT($cAlfa.A.CLINOM1X,\" \",$cAlfa.A.CLINOM2X,\" \",$cAlfa.A.CLIAPE1X,\" \",$cAlfa.A.CLIAPE2X)) AS PRONOMXX, ";
  		$qCocDat .= "IF($cAlfa.SIAI0003.USRNOMXX <> \"\",$cAlfa.SIAI0003.USRNOMXX,\"USUARIO SIN NOMBRE\") AS USRNOMXX ";
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
  		$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
  		//f_Mensaje(__FILE__,__LINE__,$qCocDat."~".mysql_num_rows($xCocDat));
  		$nFilCoc  = mysql_num_rows($xCocDat);
  		$vCocDat = array();
  		if ($nFilCoc > 0) {
  		  $vCocDat  = mysql_fetch_array($xCocDat);
  		}
  		//////////////////////////////////////////////////////////////////////////
  		////// DETALLE 1002 /////
  		$qCodDat  = "SELECT DISTINCT ";
      $qCodDat .= "$cAlfa.fcod$cAno.*, ";
      $qCodDat .= "IF($cAlfa.fpar0119.ctodesxp <> \"\",$cAlfa.fpar0119.ctodesxp,\"CONCEPTO SIN DESCRIPCION\") AS ctodesxp, ";
      $qCodDat .= "IF($cAlfa.fpar0115.pucdesxx <> \"\",$cAlfa.fpar0115.pucdesxx,\"CUENTA SIN DESCRIPCION\") AS pucdesxx, ";
      $qCodDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
      $qCodDat .= "FROM $cAlfa.fcod$cAno ";
      $qCodDat .= "LEFT JOIN $cAlfa.fpar0119 ON $cAlfa.fcod$cAno.pucidxxx = $cAlfa.fpar0119.pucidxxx AND $cAlfa.fcod$cAno.ctoidxxx = $cAlfa.fpar0119.ctoidxxx ";
      $qCodDat .= "LEFT JOIN $cAlfa.fpar0115 ON $cAlfa.fcod$cAno.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) ";
      $qCodDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
  		$qCodDat .= "WHERE $cAlfa.fcod$cAno.comidxxx = \"$cComId\" AND ";
  		$qCodDat .= "$cAlfa.fcod$cAno.comcodxx = \"$cComCod\" AND ";
  		$qCodDat .= "$cAlfa.fcod$cAno.comcscxx = \"$cComCsc\" AND ";
   		$qCodDat .= "$cAlfa.fcod$cAno.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cAno.comseqxx) ASC";
  		$xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
  		//f_Mensaje(__FILE__,__LINE__,$qCocDat."~".mysql_num_rows($xCocDat));
  		$nFilCod  = mysql_num_rows($xCodDat);
  		$mCodDat = array();
  		if ($nFilCod > 0) {
    		// Cargo la Matriz con los ROWS del Cursor
  			$iA=0;
  			while ($xRCD = mysql_fetch_array($xCodDat)) {
  				$mCodDat[$iA] = $xRCD;
  				$iA++;
  			}
  			// Fin de Cargo la Matriz con los ROWS del Cursor
  		}

  		//f_Mensaje(__FILE__,__LINE__,$nMedias);

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
  		//////////////////////////////////////////////////////////////////////////
			$total =0;
			$SumCredito =0;
			$SumDebito = 0;
  		$cude =0;
  		$posx1 = 10; //// X /////
  		$j=0; // lineas del detalle permitido para cada comprobante de media pagina
      $d=0; // control para impresion del logo de ADIMPEX en la parte superior derecha
  		// Primera Causacion //
		  if ($nFilCoc > 0 || $nFilCod > 0) {
		  	if (count($mCodDat) == 0 && count($zMatrizAce) > 0) {
  				$mCodDat[0] = "";
  			}

    		for ($k=0;$k<count($mCodDat);$k++) {

  				$j++;
  				if ($j == 1 || $pdf->getY() > 260)	{
  					// Siguiente Pagina //
  					if($nMedias == 1 || $pdf->getY() > 260) {
  						$pdf->AddPage();
	  					$py = 8;
	  					$nPosObs = 110;
              $d = 1;
  					} else {
  						$py = 138;
  						$nPosObs = 250;
              $d = 0;
  					}

  					if ($j == 1) {

              ##Impresión de Logo de ADIMPEX en la parte superior derecha##
              if ($d == 1) {
                switch($cAlfa){
                  case "TEADIMPEXX": // ADIMPEX
                  case "DEADIMPEXX": // ADIMPEX
                  case "ADIMPEXX": // ADIMPEX
                    $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex5.jpg',192,00,28,22);
                  break;
                  default:
                    // No hace nada
                  break;
                }
              }
              ##Fin Impresión de Logo de ADIMPEX en la parte superior derecha##

	  					$pdf->Line(10,$py,208,$py);
	  					$pdf->Line(10,$py+21,208,$py+21);
	  					$pdf->Line(10,$py,10,$py+21);
	  					$pdf->Line(208,$py,208,$py+21);
					    $pdf->Line(50,$py,50,$py+21);
					    $pdf->Line(150,$py,150,$py+21);
					    $pdf->Line(150,$py+7,208,$py+7);
					    $pdf->Line(150,$py+14,208,$py+14);
					    $pdf->Line(175,$py+14,175,$py+21);

	  					// Pinto el logo //
							##Impresion de Logos Agencias de Aduanas Financiero Contable 2011-05-27 Yulieth Campos##
	  					switch($cAlfa){
	  						case "INTERLOG"://MAR Y AIRE - ALCOMEX
	  						case "TEINTERLOG"://MAR Y AIRE - ALCOMEX
	  						case "DEINTERLOG"://MAR Y AIRE - ALCOMEX
	  							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/MaryAire.jpg',17,$py+4,25,15);
	  						break;
	  						case "ADUACARX"://ADUACARGA
	  						case "TEADUACARX"://ADUACARGA
	  						case "DEADUACARX"://ADUACARGA
	  							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/aduacarga.jpg',17,$py+4,25,15);
	  						break;
	  						case "ALPOPULX"://ALPOPULAR
	  						case "TEALPOPULX"://ALPOPULAR
	  						case "DEALPOPULX"://ALPOPULAR
	  						case "TEALPOPULP"://ALPOPULAR PRUEBAS
	  							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/alpopul1.jpg',17,$py+4,25,15);
	  						break;
	  						case "ETRANSPT"://DIETRICH
	  						case "TEETRANSPT"://DIETRICH
	  						case "DEETRANSPT"://DIETRICH
	  							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logodli.jpg',11,$py+3,37,12);
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
	  							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/interlogistica.jpg',13,$py+4,25,15);
	  						break;
	  						case "GRUPOGLA"://GRUPO GLA
  							case "TEGRUPOGLA"://GRUPO GLA
  							case "DEGRUPOGLA"://GRUPO GLA
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_grupogla.jpg',19,$py+1,20,19);
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
								case "ADUANAMO": //ADUANAMO
								case "DEADUANAMO": //ADUANAMO
								case "TEADUANAMO": //ADUANAMO
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamo.jpg',15,$py+1,30,19);
								break;
								case "MIRCANAX": //MIRCANAX
	              case "DEMIRCANAX": //MIRCANAX
                case "TEMIRCANAX": //MIRCANAX
	              	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_mircana.jpg',11,$py+3,38,15);
	              break;
	              case "LIDERESX":
								case "DELIDERESX":
								case "TELIDERESX":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/Logo_Lideres.jpg',19,$py+1,20,19);
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
                case "TRLXXXXX"://TRLXXXXX
                case "DETRLXXXXX"://TRLXXXXX
                case "TETRLXXXXX"://TRLXXXXX
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma1.jpg',11,$py+5,35,10);
                break;
								case "TEADIMPEXX": // ADIMPEX
								case "DEADIMPEXX": // ADIMPEX
								case "ADIMPEXX": // ADIMPEX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex4.jpg',12,$py+5,36,8);
								break;
								case "ROLDANLO"://ROLDAN
	              case "TEROLDANLO"://ROLDAN
	              case "DEROLDANLO"://ROLDAN
                	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',12,$py+1,37,19);
	              break;
                case "CASTANOX":
  							case "TECASTANOX":
  							case "DECASTANOX":
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomartcam.jpg',13,$py+1,35,19);
  							break;
                case "ALMACAFE": //ALMACAFE
	  						case "TEALMACAFE": //ALMACAFE
	  						case "DEALMACAFE": //ALMACAFE
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalmacafe.jpg',12,$py+3,35,15);
	  						break;
                case "CARGOADU": //CARGOADU
	  						case "TECARGOADU": //CARGOADU
	  						case "DECARGOADU": //CARGOADU
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoCargoAduana.png',12,$py+3,37,17);
	  						break;
                case "GRUMALCO": //GRUMALCO
	  						case "TEGRUMALCO": //GRUMALCO
	  						case "DEGRUMALCO": //GRUMALCO
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',13,6,34,18);
	  						break;
	  						case "ALADUANA"://ALADUANA
	  						case "TEALADUANA"://ALADUANA
	  						case "DEALADUANA"://ALADUANA
	  							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',16,$py+4,30,15);
								break;
								case "ANDINOSX": //ANDINOSX
								case "TEANDINOSX": //ANDINOSX
								case "DEANDINOSX": //ANDINOSX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoAndinos2.jpeg', 20, $py + 4, 16, 15);
								break;
								case "GRUPOALC": //GRUPOALC
                case "TEGRUPOALC": //GRUPOALC
                case "DEGRUPOALC": //GRUPOALC
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalc.jpg',15,$py+3,30,15);
                break;
                case "AAINTERX": //AAINTERX
								case "TEAAINTERX": //AAINTERX
								case "DEAAINTERX": //AAINTERX
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointernacional.jpg', 14,$py+2, 33, 18);
								break;
								case "AALOPEZX":
								case "TEAALOPEZX":
								case "DEAALOPEZX":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaalopez.png',17,$py+3,25);
								break;
								case "ADUAMARX": //ADUAMARX
                case "TEADUAMARX": //ADUAMARX
                case "DEADUAMARX": //ADUAMARX
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg',21,$py+1.5,19);
								break;
								case "SOLUCION": //SOLUCION
								case "TESOLUCION": //SOLUCION
								case "DESOLUCION": //SOLUCION
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',14,$py+3,32);
								break;
								case "FENIXSAS": //FENIXSAS
								case "TEFENIXSAS": //FENIXSAS
								case "DEFENIXSAS": //FENIXSAS
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg',12,$py+6,36);
								break;
								case "COLVANXX": //COLVANXX
								case "TECOLVANXX": //COLVANXX
								case "DECOLVANXX": //COLVANXX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logocolvan.jpg', 12, $py+3, 36);
								break;
								case "INTERLAC": //INTERLAC
								case "TEINTERLAC": //INTERLAC
								case "DEINTERLAC": //INTERLAC
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointerlace.jpg', 12, $py+2, 36);
								break;
								case "DHLEXPRE": //DHLEXPRE
								case "TEDHLEXPRE": //DHLEXPRE
								case "DEDHLEXPRE": //DHLEXPRE
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',12,$py+3,35,15);
								break;
								case "KARGORUX": //KARGORUX
								case "TEKARGORUX": //KARGORUX
								case "DEKARGORUX": //KARGORUX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logokargoru.jpg', 12, $py + 3, 35, 15);
								break;
								case "ALOGISAS": //LOGISTICA
								case "TEALOGISAS": //LOGISTICA
								case "DEALOGISAS": //LOGISTICA
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 10.5, $py + 3, 38);
								break;
								case "PROSERCO":
								case "TEPROSERCO":
								case "DEPROSERCO":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoproserco.png', 14, $py + 1, 33);
								break;
								case "MANATIAL":
								case "TEMANATIAL":
								case "DEMANATIAL":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomanantial.jpg', 12, $py + 5, 35);
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
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoexporcomex.jpg',14,$py+3,32,15);
								break;
								case "HAYDEARX":
								case "DEHAYDEARX":
								case "TEHAYDEARX":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logohaydear.jpeg',12,$py+3,37,17);
								break;
								case "CONNECTA":
								case "DECONNECTA":
								case "TECONNECTA":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoconnecta.jpg',17,$py+3,25,15);
                break;
                case "CONLOGIC":
                case "DECONLOGIC":
                case "TECONLOGIC":
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoconlogic.jpg',20,$py+4,18,14);
                break;
	  						default://Logo open
	  							$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/opentecnologia.JPG',11,$py+4,35,15);
	  						break;
	  					}
	  					##Impresion de Logos Agencias de Aduanas Financiero Contable 2011-05-27 Yulieth Campos##

	  					if ( $cNombreAduana != '' ) {
								$pdf->SetFont('verdanab','',8);
								$py+=2;
								$pdf->setXY(50,$py);
								if ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") {
                  $pdf->MultiCell(100, 3, $cNombreAduana . "\n NIT. " . $cNitAduana, 0, 'C'); // TITULO DEL FORMULARIO //
                } else {
                  $pdf->MultiCell(100, 3, utf8_decode($cNombreAduana . "\n NIT. " . $cNitAduana), 0, 'C'); // TITULO DEL FORMULARIO //
                }
								$py+=6;
								$py+=5;
								$pdf->setXY(50,$py);
								$pdf->Cell(100,3,utf8_decode($vCocDat['comdesxx']),0,0,'C'); // TITULO DEL FORMULARIO //
								$py+=4;
					      $pdf->setXY(50,$py);
								if(($cAlfa == "ACODEXXX" || $cAlfa == "DEACODEXXX" || $cAlfa == "TEACODEXXX") && $vCocDat['comtcoxx'] == "AUTOMATICO"){
									$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($vCocDat['comcsc3x'],0,'0',STR_PAD_LEFT)),0,0,'C');
								} else if(($cAlfa == "LOGINCAR" || $cAlfa == "DELOGINCAR" || $cAlfa == "TELOGINCAR")){
									$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($vCocDat['comcsc3x'],0,'0',STR_PAD_LEFT)),0,0,'C');
									if ($vCocDat['comtcoxx'] == "MANUAL") {
										$pdf->setXY(175,24);
										$pdf->Cell(30,3,utf8_decode($vCocDat['comcscxx']),0,0,'C');
									}
								} else {
										$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT)),0,0,'C');
								}
					     // $pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT)),0,0,'C');

							} else {
		  					$py+=8;
						    $pdf->SetFont('verdanab','',10);
		     				$pdf->setXY(50,$py);
		     				$pdf->Cell(100,3,$vCocDat['comdesxx'],0,0,'C'); // TITULO DEL FORMULARIO //
		     				$pdf->SetFont('verdanab','',8);
		     				$py+=4;
		     				$pdf->setXY(50,$py);
								if(($cAlfa == "ACODEXXX" || $cAlfa == "DEACODEXXX" || $cAlfa == "TEACODEXXX") && $vCocDat['comtcoxx'] == "AUTOMATICO"){
									$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($vCocDat['comcsc3x'],0,'0',STR_PAD_LEFT)),0,0,'C');
								} else if(($cAlfa == "LOGINCAR" || $cAlfa == "DELOGINCAR" || $cAlfa == "TELOGINCAR")){
									$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($vCocDat['comcsc3x'],0,'0',STR_PAD_LEFT)),0,0,'C');
									if ($vCocDat['comtcoxx'] == "MANUAL") {
										$pdf->setXY(175,24);
										$pdf->Cell(30,3,utf8_decode($vCocDat['comcscxx']),0,0,'C');
									}
								} else {
										$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT)),0,0,'C');
								}
		     				//$pdf->Cell(100,3,"$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT),0,0,'C'); // TITULO DEL FORMULARIO //
		     			}

							$py+=10;
	            //Numero comprobante
	     				$pdf->setXY(170,$py);
							$pdf->SetFont('verdanab','',9);
							$pdf->Cell(38,3,"{$vCocDat['sucidxxx']} No. ".(($vCocDat['comcsc3x'] != "") ? $vCocDat['comcsc3x'] : str_pad($vCocDat['comcsc2x'],10,'0',STR_PAD_LEFT)),0,0,"R");

							//Fecha Vencimiento
							switch($cAlfa){
								case "SIACOSIA":
                case "DESIACOSIP":
								case "TESIACOSIP":
									$py+=5;
									$pdf->SetFont('verdanab','',7);
									$pdf->setXY(172,$py-25);
									$pdf->Cell(30,3,"FECHA VENCIMIENTO:  {$vCocDat['comfecve']}",0,0,'R');
									break;
								default:
									$py+=5;
									$pdf->SetFont('verdana','',7);
									$pdf->setXY(178,$py);
									$pdf->Cell(30,3,"FECHA:  {$vCocDat['comfecve']}",0,0,'R');
									break;
							}
						}
  					$py+=5;
       			// DATOS DE DETALLE //

       			switch ($cAlfa) {
              case 'TRLXXXXX':
              case 'DETRLXXXXX':
              case 'TETRLXXXXX':
              // case 'TEGRUPOGLA':
                $nPosPucId  = 17;
                $nPosCcoId  = 17;
                $nPosDocCr  = 34;
                $nPosPucDes = 35;
                $nPosComObs = 28;
              break;
              default:
                $nPosPucId  = 16;
                $nPosCcoId  = 13;
                $nPosDocCr  = 36;
                $nPosPucDes = 37;
                $nPosComObs = 30;
              break;
            }

  					$pdf->SetFont('verdanab','',7);
  					$pdf->setXY($posx1,$py);
  					$pdf->Cell(8,3,'ITEM',0,0,'C');
  					$pdf->Cell($nPosPucId,3,'CUENTA',0,0,'C');
  					$pdf->Cell($nPosCcoId,3,'CENTRO',0,0,'C');
  					$pdf->Cell(18,3,'SUBCENTRO',0,0,'C');
  					$pdf->Cell($nPosDocCr,3,'DOC.CRUCE',0,0,'C');
  					$pdf->Cell($nPosPucDes,3,'DESCRIPCION',0,0,'C');
  					$pdf->Cell($nPosComObs,3,'OBSERVACIONES',0,0,'C');
  					$pdf->Cell(20,3,'DEBITO',0,0,'R');
  					$pdf->Cell(20,3,'CREDITO',0,0,'R');
  					$posy = $py+3;
  				}

  				///////////////// PINTO EL DETALLE /////////////////////////////
  				if ($mCodDat[$k]['pucidxxx'] != "") {
	  				$pdf->SetFont('verdana','',7);
	  				$pdf->setXY($posx1,$posy);
	  				$pdf->Cell(8,3,str_pad($j,3,'0',STR_PAD_LEFT),0,0,'C');
	  				$pdf->Cell($nPosPucId,3,$mCodDat[$k]['pucidxxx'],0,0,'L');
	  				$pdf->Cell($nPosCcoId,3,$mCodDat[$k]['ccoidxxx'],0,0,'C');
	  				$pdf->Cell(18,3,$mCodDat[$k]['sccidxxx'],0,0,'L');
	  				$pdf->Cell($nPosDocCr,3,substr($mCodDat[$k]['comidcxx']."-".$mCodDat[$k]['comcodcx']."-".$mCodDat[$k]['comcsccx']."-".$mCodDat[$k]['comseqcx'],0,21),0,0,'L');
	  				if($mCodDat[$k]['ctodesxp'] != 'CONCEPTO SIN DESCRIPCION') {
	  					$pdf->cell($nPosPucDes,3,substr($mCodDat[$k]['ctodesxp'],0,21),0,0,'L');
	  				}else{
	  					$pdf->cell($nPosPucDes,3,substr($mCodDat[$k]['pucdesxx'],0,21),0,0,'L');
	  				}
	  				$pdf->cell($nPosComObs,3,substr($mCodDat[$k]['comobsxx'],0,18),0,0,'L');
	  				// If para mostrar en la grilla el valor de credito y debito ///
	  				if ($mCodDat[$k]['commovxx'] == 'C') {
	  					$pdf->Cell(20,3,'');
	  					$pdf->Cell(20,3,number_format((($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']),0,',','.'),0,0,'R');
	  					$SumCredito += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : "0";
	  				} else {
	  					$pdf->Cell(20,3,number_format((($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']),0,',','.'),0,0,'R');
	  					$pdf->Cell(20,3,'');
	  					$total += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
	  					$SumDebito += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : "0";
	  				}
	    			///////////////// PINTO EL DETALLE /////////////////////////////
	  				$posy+=3;
  				}
  			}
  			// Para q muestre la suma del debito y credito
  			$pdf->SetFont('verdanab','',7);
  			$pdf->setXY($posx1+160,$posy+2);
  			$pdf->Cell(20,3,'$'.number_format($SumDebito,0,',','.'),0,0,'R');
  			$pdf->Cell(20,3,'$'.number_format($SumCredito,0,',','.'),0,0,'R');
  			//Fin mostras sumatorias

  			if($pdf->getY() < 250) {

  				$nP = ($pdf->getY() < $nPosObs) ? $nPosObs : 250;
  				$py = ($pdf->getY() < $nPosObs) ? $nPosObs+10 : 260;

  				switch($cAlfa){
  					case "ADUANERA"://ADUANERA GRANCOLOMBIANA
  					case "TEADUANERA"://ADUANERA GRANCOLOMBIANA
  					case "DEADUANERA"://ADUANERA GRANCOLOMBIANA
  						$nMedias=1;
  					break;
  					default:
  						$nMedias = ($pdf->getY() <= 110) ? 2 : 1;
  					break;
  				}

  			} else {
  				$pdf->AddPage();
  				$nP = 44;
  				$py = $nP+10;

  				switch($cAlfa) {
  					case "ADUANERA"://ADUANERA GRANCOLOMBIANA
  					case "TEADUANERA"://ADUANERA GRANCOLOMBIANA
  					case "DEADUANERA"://ADUANERA GRANCOLOMBIANA
  						$nMedias=1;
  					break;
  					default:
  						$nMedias=2;
  					break;
  				}
  			}

  			$pdf->SetFont('verdanab','',7);
  			$pdf->SetXY(10,$nP);
  			$pdf->Cell(15,3,'OBSERVACIONES GENERALES :');
  			$pdf->SetFont('verdana','',7);
  			$pdf->SetXY(10,$nP+3);
  			$pdf->Cell(200,3,$vCocDat['comobsxx']);

  			/*$posy+=3;
  			$pdf->SetFont('verdanab','',7);
  			$pdf->setXY(138,$py-13);
  			$pdf->Cell(40,3,'TOTAL : ',0,0,'R');
  			$pdf->Cell(30,3,'$'.number_format($total,0,',','.'),0,0,'R');*/

  			$py+=1;
  			$pdf->Rect(10,$py,198,10);
  			$pdf->Line(85,$py,85, $py+10);
  			$pdf->SetFont('verdana','',7);
  			$py+=3;
  			$pdf->setXY(10, $py);
  			$pdf->Cell(25,3,$vCocDat['USRNOMXX']);
  			$py+=3;
  			$pdf->setXY(10,$py);
  			$pdf->Cell(25,3,"Elaboro",0,0,'C');
  			$pdf->Cell(20,3,"Revisado",0,0,'C');
  			$pdf->Cell(20,3,"Aprobado",0,0,'C');
  			$pdf->setXY(95,$py);
  			$pdf->Cell(95,3,"Firma y Sello.",0,0,'C');
  			$py+=8;
  			if ($py < 250) {
  				$pdf->Line(10, $py,208, $py);
  			}
  			// Segunda Causacion //
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

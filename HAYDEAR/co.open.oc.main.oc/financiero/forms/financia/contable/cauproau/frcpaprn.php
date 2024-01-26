<?php
  /**
	 * Imprime Comprobante.
	 * --- Descripcion: Permite Imprimir Comprobante.
	 * @author Sandra Guerrero <sguerrero@opentecnologia.com.co>
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
  		$cComFec  = $vComp[4];
  		$cAno     = substr($cComFec,0,4);
  		$nMedias++;

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

  		//f_Mensaje(__FILE__,__LINE__,$qCocDat);

  		$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
  		$nFilCoc  = mysql_num_rows($xCocDat);
  		$vCocDat = array();
  		if ($nFilCoc > 0) {
  		  $vCocDat  = mysql_fetch_array($xCocDat);
  		}
  		//////////////////////////////////////////////////////////////////////////

  		////// DETALLE 1002 /////
  		$qCodDat  = "SELECT DISTINCT ";
      $qCodDat .= "$cAlfa.fcod$cAno.*, ";
      $qCodDat .= "IF($cAlfa.fpar0119.ctodesxp <> \"\",$cAlfa.fpar0119.ctodesxp,IF($cAlfa.fpar0121.ctodesxx <> \"\",$cAlfa.fpar0121.ctodesxx,\"CONCEPTO SIN DESCRIPCION\")) AS ctodesxp,";
      $qCodDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
      $qCodDat .= "FROM $cAlfa.fcod$cAno ";
      $qCodDat .= "LEFT JOIN $cAlfa.fpar0119 ON $cAlfa.fcod$cAno.pucidxxx = $cAlfa.fpar0119.pucidxxx AND $cAlfa.fcod$cAno.ctoidxxx = $cAlfa.fpar0119.ctoidxxx ";
      $qCodDat .= "LEFT JOIN $cAlfa.fpar0121 ON $cAlfa.fcod$cAno.pucidxxx = $cAlfa.fpar0121.pucidxxx AND $cAlfa.fcod$cAno.ctoidxxx = $cAlfa.fpar0121.ctoidxxx ";
      $qCodDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
  		$qCodDat .= "WHERE $cAlfa.fcod$cAno.comidxxx = \"$cComId\" AND ";
  		$qCodDat .= "$cAlfa.fcod$cAno.comcodxx = \"$cComCod\" AND ";
  		$qCodDat .= "$cAlfa.fcod$cAno.comcscxx = \"$cComCsc\" AND ";
   		$qCodDat .= "$cAlfa.fcod$cAno.comcsc2x = \"$cComCsc2\" ORDER BY ABS($cAlfa.fcod$cAno.comseqxx) ASC";
  		$xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
  		$nFilCod  = mysql_num_rows($xCodDat);
  		if ($nFilCod > 0) {
    		// Cargo la Matriz con los ROWS del Cursor
  			$iA=0;
  			while ($xRCD = mysql_fetch_array($xCodDat)) {
  				//Buscando el detalle de la cuenta
  				$qPucId  = "SELECT pucdetxx ";
  				$qPucId .= "FROM $cAlfa.fpar0115 ";
  				$qPucId .= "WHERE ";
  				$qPucId .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = \"{$xRCD['pucidxxx']}\" AND ";
  				$qPucId .= "regestxx = \"ACTIVO\" LIMIT 0,1";
  				$xPucId  = f_MySql("SELECT","",$qPucId,$xConexion01,"");
  				$vPucId  = mysql_fetch_array($xPucId);
  				$xRCD['pucdetxx'] = $vPucId['pucdetxx'];

  				$mCodDat[$iA] = $xRCD;
  				$iA++;
  			}
  			// Fin de Cargo la Matriz con los ROWS del Cursor
  		}

  		//f_Mensaje(__FILE__,__LINE__,$qCodDat);

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

  		$cude = 0;
  		// Primera Causacion //
		  if($nMedias % 2) { ///  CABECERA ///

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
  				$posx1 = 10; //// X /////
  				$posxcr = 150;
  				$posxdb = 180;
  				$total = 0;
  				$SumCredito =0;
  				$SumDebito = 0;
  				$BaseIva = 0; // Variable para sumar los valores del campo comvlr01
  				$TotalIva= 0; // Variable para sumar los valores del campo comvlr02
  				$totalcr = 0; //variable para mostrar y sumar los creditos ///
  				$totaldb = 0; //variable para mostrar y sumar los creditos ///
  				$j=0; // lineas del detalle permitido para cada comprobante de media pagina

    			for ($k=0;$k<count($mCodDat);$k++) {

  					$j++;
  					if ($j == 1 || (($j % 50) == 0))	{
  						if ($j > 1) {
  							$arranca++;
  						}

  						// Siguiente Pagina //
  						$pdf->AddPage();
  						$py = 0;
  						$posy = 44;
  						$posy2 = 44;
  						$posy3 = 70;
  						$posy4 = 271;
  						$posy5 = 273;
  						$posy6 = 275;
  						$posy7 = 277;

              ##Impresión de Logo de ADIMPEX en la parte superior derecha##
              switch($cAlfa){
                case "TEADIMPEXX": // ADIMPEX
                case "DEADIMPEXX": // ADIMPEX
                case "ADIMPEXX": // ADIMPEX
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex5.jpg',194,00,25,20);
                break;
                default:
                  // No hace nada
                break;
              }
              ##Fin Impresión de Logo de ADIMPEX en la parte superior derecha##

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
  							case "ADUACARX": //ADUACARX
  							case "DEADUACARX":
  							case "TEADUACARX":
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/aduacarga1.png',13,9,35,15);
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
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_grupogla.jpg',19,6,20,19);
  							break;
  							case "LOGISTSA"://LOGISTSA
  							case "TELOGISTSA"://LOGISTSA
  							case "DELOGISTSA"://LOGISTSA
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logistica.jpg',11,8,38,14);
  							break;
  							case "SIACOSIA"://SIACO
  							case "DESIACOSIA"://SIACO
  							case "TESIACOSIP"://SIACO
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_siaco.jpg',15,6,30,19);
  							break;
  							case "UPSXXXXX": //UPS
  							case "DEUPSXXXXX": //UPS
  							case "TEUPSXXXXX": //UPS
                 					$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_ups.jpeg',19,6,20,19);
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
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma.jpg',19,6,20,19);
                break;
								case "TEADIMPEXX": // ADIMPEX
								case "DEADIMPEXX": // ADIMPEX
								case "ADIMPEXX": // ADIMPEX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex4.jpg',12,11,36,8);
								break;
								case "ROLDANLO"://ROLDAN
	              case "TEROLDANLO"://ROLDAN
	              case "DEROLDANLO"://ROLDAN
                	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',12,6,37,19);
	              break;
                case "CASTANOX":
  							case "TECASTANOX":
  							case "DECASTANOX":
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomartcam.jpg',13,6,35,19);
  							break;
                case "ALMACAFE": //ALMACAFE
	  						case "TEALMACAFE": //ALMACAFE
	  						case "DEALMACAFE": //ALMACAFE
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalmacafe.jpg',12,8,35,15);
	  						break;
                case "CARGOADU": //CARGOADU
	  						case "TECARGOADU": //CARGOADU
	  						case "DECARGOADU": //CARGOADU
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoCargoAduana.png',12,8,37,17);
	  						break;
                case "GRUMALCO": //GRUMALCO
	  						case "TEGRUMALCO": //GRUMALCO
	  						case "DEGRUMALCO": //GRUMALCO
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',12,8,35,15);
	  						break;
                case "ALADUANA": //ALADUANA
                case "TEALADUANA": //ALADUANA
                case "DEALADUANA": //ALADUANA
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',16,8,30,15);
								break;
								case "ANDINOSX": //ANDINOSX
								case "TEANDINOSX": //ANDINOSX
								case "DEANDINOSX": //ANDINOSX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoandinos.jpg', 12, $py + 9, 36, 15);
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
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg', 12, 11, 36);
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
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',12,8,35,15);
								break;
								case "KARGORUX": //KARGORUX
								case "TEKARGORUX": //KARGORUX
								case "DEKARGORUX": //KARGORUX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logokargoru.jpg', 12, 8, 35, 15);
								break;
								case "ALOGISAS": //LOGISTICA
								case "TEALOGISAS": //LOGISTICA
								case "DEALOGISAS": //LOGISTICA
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 10.5, 8, 38);
								break;
								case "PROSERCO":
								case "TEPROSERCO":
								case "DEPROSERCO":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoproserco.png', 14, 6, 34);
								break;
                case "MANATIAL":
                case "TEMANATIAL":
                case "DEMANATIAL":
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomanantial.jpg', 12, 10, 36);
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
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofedexexp.jpg',13,9,33,15);
                break;
								case "EXPORCOM":
								case "DEEXPORCOM":
								case "TEEXPORCOM":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoexporcomex.jpg',14,8,32,15);
								break;
								case "HAYDEARX":
								case "DEHAYDEARX":
								case "TEHAYDEARX":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logohaydear.jpeg',14,8,35,15);
								break;
  							default://Logo open
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/opentecnologia.JPG',17,9,25,15);
  							break;
  						}
  						##Impresion de Logos Agencias de Aduanas Financiero Contable ##
  						// DATOS CABECERA //
  						//$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/emision_cero.jpg',10,0,200,30);

							if ( $cNombreAduana != '' ) {
								$pdf->SetFont('verdanab','',8);
								$pdf->setXY(50,7);
                if ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") {
                  $pdf->MultiCell(100, 3, $cNombreAduana . "\n NIT. " . $cNitAduana, 0, 'C'); // TITULO DEL FORMULARIO //
                } else {
                  $pdf->MultiCell(100, 3, utf8_decode($cNombreAduana . "\n NIT. " . $cNitAduana), 0, 'C'); // TITULO DEL FORMULARIO //
                }

								$pdf->setXY(50,18);
								$pdf->Cell(100,3,utf8_decode($vCocDat['comdesxx']),0,0,'C'); // TITULO DEL FORMULARIO //
					      $pdf->setXY(50,22);
								if(($cAlfa == "ACODEXXX" || $cAlfa == "DEACODEXXX" || $cAlfa == "TEACODEXXX") && $vCocDat['comtcoxx'] == "AUTOMATICO"){
									$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($vCocDat['comcsc3x'],0,'0',STR_PAD_LEFT)),0,0,'C');
								} else if(($cAlfa == "LOGISTSA" || $cAlfa == "DELOGISTSA" || $cAlfa == "TELOGISTSA") && $vCocDat['comtcoxx'] == "MANUAL"){
										$pdf->Cell(100,3,"");
								} else if(($cAlfa == "LOGINCAR" || $cAlfa == "DELOGINCAR" || $cAlfa == "TELOGINCAR")){
									$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($vCocDat['comcsc3x'],0,'0',STR_PAD_LEFT)),0,0,'C');
									if ($vCocDat['comtcoxx'] == "MANUAL") {
										$pdf->setXY(175,22);
										$pdf->Cell(30,3,utf8_decode($vCocDat['comcscxx']),0,0,'C');
									}
								}else {
										$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT)),0,0,'C');
								}
							} else {
					      $pdf->SetFont('verdanab','',10);
					      $pdf->setXY(50,12);
					      $pdf->Cell(100,3,$vCocDat['comdesxx'],0,0,'C'); // TITULO DEL FORMULARIO //
					      $pdf->SetFont('verdanab','',8);
					      $pdf->setXY(50,16);
								if(($cAlfa == "ACODEXXX" || $cAlfa == "DEACODEXXX" || $cAlfa == "TEACODEXXX") && $vCocDat['comtcoxx'] == "AUTOMATICO"){
									$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($vCocDat['comcsc3x'],0,'0',STR_PAD_LEFT)),0,0,'C');
								} else if(($cAlfa == "LOGISTSA" || $cAlfa == "DELOGISTSA" || $cAlfa == "TELOGISTSA") && $vCocDat['comtcoxx'] == "MANUAL"){
										$pdf->Cell(100,3,"");
								} else if(($cAlfa == "LOGINCAR" || $cAlfa == "DELOGINCAR" || $cAlfa == "TELOGINCAR")){
									$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($vCocDat['comcsc3x'],0,'0',STR_PAD_LEFT)),0,0,'C');
									if ($vCocDat['comtcoxx'] == "MANUAL") {
										$pdf->setXY(175,22);
										$pdf->Cell(30,3,utf8_decode($vCocDat['comcscxx']),0,0,'C');
									}
								} else {
										$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT)),0,0,'C');
								}
							}

				      $pdf->SetFont('verdana','',7);
				      $pdf->setXY(10,28);
       				$pdf->Cell(17,3,'CLIENTE');
       				$pdf->Cell(2,3,":" );
       				$pdf->Cell(100,3,substr($vCocDat['CLINOMXX'],0,65));
       				// Nit del Cliente //
       				$pdf->setXY(140,28);
       				$pdf->Cell(40,3,"NIT: {$vCocDat['teridxxx']}-".f_Digito_Verificacion($vCocDat['teridxxx']));

							$pdf->setXY(170,28);
							$pdf->SetFont('verdanab','',9);
							if(($cAlfa == "LOGISTSA" || $cAlfa == "DELOGISTSA" || $cAlfa == "TELOGISTSA") && $vCocDat['comtcoxx'] == "MANUAL"){
								$vComCsc = explode("-",$vCocDat['comcsc3x']);
								$pdf->Cell(39,3,"{$vCocDat['sucidxxx']} No. ".(($vCocDat['comcsc3x'] != "") ? (($vComCsc['0']))."-".(($cComCsc)): str_pad($vCocDat['comcsc2x'],10,'0',STR_PAD_LEFT)),0,0,"R");
							} else {
								$pdf->Cell(39,3,"{$vCocDat['sucidxxx']} No. ".(($vCocDat['comcsc3x'] != "") ? $vCocDat['comcsc3x'] : str_pad($vCocDat['comcsc2x'],10,'0',STR_PAD_LEFT)),0,0,"R");
							}
							$pdf->SetFont('verdana','',7);
       				$pdf->setXY(10,32);
       				$pdf->Cell(17,3,'PROVEEDOR');
       				$pdf->Cell(2,3,":" );
       				$pdf->Cell(100,3,substr($vCocDat['PRONOMXX'],0,65));
       				// Nit del Proveedor //
       				$pdf->setXY(140,32);
       				$pdf->Cell(40,3,"NIT: {$vCocDat['terid2xx']}-".f_Digito_Verificacion($vCocDat['terid2xx']));

       				$pdf->SetFont('verdana','',7);
							$pdf->setXY(178,32);
							$pdf->Cell(31,3,"FECHA:  {$vCocDat['comfecxx']}",0,0,'R');

							//Fecha Vencimiento
							switch($cAlfa){
								case "SIACOSIA":
								case "DESIACOSIP":
								case "TESIACOSIP":
									$pdf->SetFont('verdanab','',7);
									$pdf->setXY(174,$py+15);
									$pdf->Cell(30,3,"FECHA VENCIMIENTO:  {$vCocDat['comfecve']}",0,0,'R');
									break;
								default:
									break;
							}

     					// DATOS DE DETALLE //
     					switch ($cAlfa) {
                case 'TRLXXXXX':
                case 'DETRLXXXXX':
                case 'TETRLXXXXX':
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
  					}

  					if ($j == $nFilCod+1) { /// INCREMENTA CADA REGISTRO QUE TENGA LA GRILLA  y Pinto la grilla/////
  						break;
  					}

  					///////////////// PINTO EL DETALLE /////////////////////////////
						$pdf->SetFont('verdana','',7);
						$pdf->setXY($posx1,$posy);
						$pdf->Cell(8,3,str_pad($j,3,'0',STR_PAD_LEFT),0,0,'C');
						$pdf->Cell($nPosPucId,3,$mCodDat[$k]['pucidxxx'],0,0,'L');
						$pdf->Cell($nPosCcoId,3,$mCodDat[$k]['ccoidxxx'],0,0,'C');
  					$pdf->Cell(18,3,$mCodDat[$k]['sccidxxx'],0,0,'L');
						$pdf->Cell($nPosDocCr,3,substr($mCodDat[$k]['comidcxx']."-".$mCodDat[$k]['comcodcx']."-".$mCodDat[$k]['comcsccx']."-".$mCodDat[$k]['comseqcx'],0,21),0,0,'L');
						$pdf->cell($nPosPucDes,3,substr($mCodDat[$k]['ctodesxp'],0,22),0,0,'L');
						$pdf->cell($nPosComObs,3,substr($mCodDat[$k]['comobsxx'],0,17),0,0,'L');
						// If para mostrar en la grilla el valor de credito y debito ///
						if ($mCodDat[$k]['commovxx'] == 'C') {
							//$pdf->setXY($posxdb,$posy); //Debito //Credito
							$pdf->Cell(20,3,'');
							$pdf->Cell(20,3,number_format((($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'] ),0,',','.'),0,0,'R');
							$SumCredito += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : "0";
						} else {
							//$pdf->setXY($posxcr,$posy);  //Credito //Debito
							$pdf->Cell(20,3,number_format((($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']),0,',','.'),0,0,'R');
							$pdf->Cell(20,3,'');
							$total += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
							$SumDebito += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : "0";
						}
  					///////////////// PINTO EL DETALLE /////////////////////////////
						$BaseIva  += ($mCodDat[$k]['pucdetxx'] == "D") ? $mCodDat[$k]['comvlr01'] : 0;
						$TotalIva += ($mCodDat[$k]['pucdetxx'] == "D") ? $mCodDat[$k]['comvlr02'] : 0;
  					$posy+=3;
  				}

  				// Para q muestre la suma del debito y credito
  				$pdf->SetFont('verdanab','',7);
  				$pdf->setXY($posx1+160,$posy+2);
  				$pdf->Cell(20,3,'$'.number_format($SumDebito,0,',','.'),0,0,'R');
  				$pdf->Cell(20,3,'$'.number_format($SumCredito,0,',','.'),0,0,'R');
  				//Fin mostras sumatorias

  				// Pinta la tabla que contiene Basa iva y total
  				$posy+=3;
  				$pdf->SetFont('verdanab','',7);
  				$pdf->setXY($posx1+60,$posy+5);
  				//$pdf->SetTextColor(255,150,90); Color de la letra
  				$pdf->Cell(22,5,'Base IVA',1,0,'C');
  				$pdf->Cell(22,5,'$'.number_format($BaseIva,0,',','.'),1,0);
  				$pdf->Cell(22,5,'Valor IVA',1,0,'C');
  				$pdf->Cell(22,5,'$'.number_format($TotalIva,0,',','.'),1,0);
  				// Fin pintar tabla

  				// Termino de leer el cursor del la $Mat2 //
  				if($j < 20) {// CUANDO SON POCOS REGISTROS  " pregunto se la grilla es menor que 20 para pintar "
  					$pdf->SetFont('verdanab','',7);
  					$pdf->SetXY(10,110);
  					$pdf->Cell(15,3,'OBSERVACIONES GENERALES :');
  					$pdf->SetFont('verdana','',7);
  					$pdf->SetXY(10,113);
  					$pdf->Cell(200,3,$vCocDat['comobsxx']);
  					$py=120;

  					$posy+=3;
  					$pdf->SetFont('verdanab','',7);
  					$pdf->setXY(138,$py-20);
  					$pdf->Cell(40,3,'TOTAL : ',0,0,'R');
  					//$pdf->Cell(30,3,'$'.number_format($totaldb,0,',','.'),0,0,'R');
  					//$pdf->Cell(30,3,'$'.number_format($totalcr,0,',','.'),0,0,'R');
  					$pdf->Cell(30,3,'$'.number_format($total,0,',','.'),0,0,'R');

  					//$pdf->Line(20, $py,200, $py);
  					$py+=1;
  					$pdf->Rect(10,$py,198,10);
  					$pdf->Line(85,$py,85, $py+10);
  					$pdf->SetFont('verdana','',7);
  					$py+=3;
  					$pdf->setXY(10, $py);
  					$pdf->Cell(25,3,$vCocDat['USRNOMXX']);
  					//$pdf->Cell(25,3,'Denis Montealegre');
  					$py+=3;
  					$pdf->setXY(10,$py);
  					$pdf->Cell(25,3,"Elaboro",0,0,'C');
  					$pdf->Cell(20,3,"Revisado",0,0,'C');
  					$pdf->Cell(20,3,"Aprobado",0,0,'C');
  					$pdf->setXY(95,$py);
  					$pdf->Cell(95,3,"Firma y Sello.",0,0,'C');
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
  					$pdf->Cell(200,3,substr($vCocDat['comobsxx'],0,60));
  					///////////////////////////////////////////////////////
  					$posy+=3;
  					$pdf->SetFont('verdanab','',7);
  					$pdf->setXY(138,$py-20);
  					$pdf->Cell(40,3,'TOTAL : ',0,0,'R');
  					$pdf->Cell(30,3,'$'.number_format($total,0,',','.'),0,0,'R');
  					///////////////////////////////////////////////////////
  					$posy+=8;
  					$pdf->Rect(10,$posy,198,10);
  					$pdf->Line(85,$posy,85,$posy+10);
  					$pdf->SetFont('verdana','',7);
  					$posy+=3;
  					$pdf->setXY(10, $posy);
  					$pdf->Cell(25,3,$vCocDat['USRNOMXX']);
  					$posy+=3;
  					$pdf->setXY(10,$posy);
  					$pdf->Cell(25,3,"Elaboro",0,0,'C');
  					$pdf->Cell(20,3,"Revisado",0,0,'C');
  					$pdf->Cell(20,3,"Aprobado",0,0,'C');
  					$pdf->setXY(95,$posy);
  					$pdf->Cell(95,3,"Firma y Sello.",0,0,'C');
  				}
  				// Segunda Causacion //
    		} else {
    		  $pdf->AddPage();
  				$pdf->SetXY(40,40);
  				$pdf->Cell(200,200,"recibo incompleto verifique (1) ");
    		}

  		} else { ///  CABECERA ////

  		  if ($nFilCoc > 0 && $nFilCod > 0) {
  				//// Nueva Causacion ////
  				$P=0; //
  			  $total1=0;
  			  $SumCredito1 =0;
  			  $SumDebito1 = 0;
  			  $BaseIva1= 0; // Variable para sumar los valores del campo comvlr01
  			  $TotalIva1= 0; // Variable para sumar los valores del campo comvlr02
  				$totalcr = 0; //variable para mostrar y sumar los creditos ///
  				$totaldb = 0; //variable para mostrar y sumar los creditos ///
  				//while ($mCodDat[$k] = mysql_fetch_array($xCodDat)) 	{ //CURSOR PARA RECORRER LA GRILLA //

  				for ($k=0;$k<count($mCodDat);$k++) {
   					$P++;
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

  						// Pinto el dibujo //
  						//$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/emision_cero.jpg',10,$py,200,30);
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
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/interlogistica.jpg',17,$py+4,25,15);
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
  							case "DESIACOSIA"://SIACO
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
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma.jpg',19,$py+1,20,19);
                break;
								case "TEADIMPEXX": // ADIMPEX
								case "DEADIMPEXX": // ADIMPEX
								case "ADIMPEXX": // ADIMPEX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex4.jpg',12,$py+6,36,8);
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
                case "GRUMALCO"://GRUMALCO
	              case "TEGRUMALCO"://GRUMALCO
	              case "DEGRUMALCO"://GRUMALCO
                	$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',12,$py+1,37,19);
	              break;
                case "ALADUANA": //ALADUANA
                case "TEALADUANA": //ALADUANA
                case "DEALADUANA": //ALADUANA
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',16,$py+4,30,15);
								break;
								case "ANDINOSX": //ANDINOSX
								case "TEANDINOSX": //ANDINOSX
								case "DEANDINOSX": //ANDINOSX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoandinos.jpg', 12, $py + 9, 36, 15);
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
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaalopez.png',17,$py+4,25);
								break;
								case "SOLUCION": //SOLUCION
								case "TESOLUCION": //SOLUCION
								case "DESOLUCION": //SOLUCION
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',14,$py+4,32);
								break;
								case "FENIXSAS": //FENIXSAS
								case "TEFENIXSAS": //FENIXSAS
								case "DEFENIXSAS": //FENIXSAS
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg', 12, $py+6, 36);
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
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',12,$py+1,37,19);
								break;
								case "KARGORUX": //KARGORUX
								case "TEKARGORUX": //KARGORUX
								case "DEKARGORUX": //KARGORUX
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logokargoru.jpg', 12, $py + 1, 37, 19);
								break;
								case "ALOGISAS": //LOGISTICA
								case "TEALOGISAS": //LOGISTICA
								case "DEALOGISAS": //LOGISTICA
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 12, $py + 1, 37, 19);
								break;
								case "PROSERCO":
								case "TEPROSERCO":
								case "DEPROSERCO":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoproserco.png', 12, $py + 1, 37, 19);
								break;
                case "MANATIAL":
                case "TEMANATIAL":
                case "DEMANATIAL":
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomanantial.jpg', 12, $py + 5, 35, 9);
                break;
                case "DSVSASXX":
                case "TEDSVSASXX":
                case "DEDSVSASXX":
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logodsv.jpg',17,9,25,15);
                break;
                case "MELYAKXX":
                case "TEMELYAKXX":
                case "DEMELYAKXX":
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomelyak.jpg',13,$py+4,33,12);
                break;
                case "FEDEXEXP":
                case "DEFEDEXEXP":
                case "TEFEDEXEXP":
                  $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofedexexp.jpg',13,$py+9,33,15);
                break;
								case "EXPORCOM":
								case "DEEXPORCOM":
								case "TEEXPORCOM":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoexporcomex.jpg',14,$py+4,32,15);
								break;
								case "HAYDEARX":
								case "DEHAYDEARX":
								case "TEHAYDEARX":
									$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logohaydear.jpeg',14,8,35,15);
								break;
  							default://Logo open
  								$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/opentecnologia.JPG',17,$py+4,25,15);
  							break;
  						}
  						##Impresion de Logos Agencias de Aduanas Financiero Contable ##

  						if ( $cNombreAduana != '' ) {
								$pdf->SetFont('verdanab','',8);
								$pdf->setXY(50,$py+2);
                if ($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO") {
                  $pdf->MultiCell(100, 3, $cNombreAduana . "\n NIT. " . $cNitAduana, 0, 'C'); // TITULO DEL FORMULARIO //
                } else {
                  $pdf->MultiCell(100, 3, utf8_decode($cNombreAduana . "\n NIT. " . $cNitAduana), 0, 'C'); // TITULO DEL FORMULARIO //
                }

								$pdf->setXY(50,$py+13);
								$pdf->Cell(100,3,utf8_decode($vCocDat['comdesxx']),0,0,'C'); // TITULO DEL FORMULARIO //
					      $pdf->setXY(50,$py+17);

								if(($cAlfa == "ACODEXXX" || $cAlfa == "DEACODEXXX" || $cAlfa == "TEACODEXXX") && $vCocDat['comtcoxx'] == "AUTOMATICO"){
									$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($vCocDat['comcsc3x'],0,'0',STR_PAD_LEFT)),0,0,'C');
								} else if(($cAlfa == "LOGISTSA" || $cAlfa == "DELOGISTSA" || $cAlfa == "TELOGISTSA") && $vCocDat['comtcoxx'] == "MANUAL"){
										$pdf->Cell(100,3,"");
								} else {
										$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT)),0,0,'C');
								}
					      //$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT)),0,0,'C');

							} else {
	  						$pdf->SetFont('verdanab','',10);
	     					$pdf->setXY(50,$py+8);
	     					$pdf->Cell(100,3,$vCocDat['comdesxx'],0,0,'C'); // TITULO DEL FORMULARIO //
	     					$pdf->SetFont('verdanab','',8);
	     					$pdf->setXY(50,$py+12);

								if(($cAlfa == "ACODEXXX" || $cAlfa == "DEACODEXXX" || $cAlfa == "TEACODEXXX") && $vCocDat['comtcoxx'] == "AUTOMATICO"){
									$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($vCocDat['comcsc3x'],0,'0',STR_PAD_LEFT)),0,0,'C');
								} else if(($cAlfa == "LOGISTSA" || $cAlfa == "DELOGISTSA" || $cAlfa == "TELOGISTSA") && $vCocDat['comtcoxx'] == "MANUAL"){
										$pdf->Cell(100,3,"");
								} else {
										$pdf->Cell(100,3,utf8_decode("$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT)),0,0,'C');
								}
	     					//$pdf->Cell(100,3,"$cComId-".str_pad($cComCod,3,'0',STR_PAD_LEFT)."-".str_pad($cComCsc,0,'0',STR_PAD_LEFT),0,0,'C'); // TITULO DEL FORMULARIO //
							}
              // Cliente //
       				$pdf->SetFont('verdana','',7);
       				$pdf->setXY(10,$py+23);
       				$pdf->Cell(17,3,'CLIENTE');
       				$pdf->Cell(2,3,":" );
       				$pdf->Cell(100,3,substr($vCocDat['CLINOMXX'],0,65));
       				// Nit del Cliente //
       				$pdf->setXY(140,$py+23);
       				$pdf->Cell(40,3,"NIT: {$vCocDat['teridxxx']}-".f_Digito_Verificacion($vCocDat['teridxxx']));


     					$pdf->setXY(170,$py+23);
							$pdf->SetFont('verdanab','',9);
							if(($cAlfa == "LOGISTSA" || $cAlfa == "DELOGISTSA" || $cAlfa == "TELOGISTSA") && $vCocDat['comtcoxx'] == "MANUAL"){
								$vComCsc = explode("-",$vCocDat['comcsc3x']);
								$pdf->Cell(39,3,"{$vCocDat['sucidxxx']} No. ".(($vCocDat['comcsc3x'] != "") ? (($vComCsc['0']))."-".(($cComCsc)): str_pad($vCocDat['comcsc2x'],10,'0',STR_PAD_LEFT)),0,0,"R");
							} else {
								$pdf->Cell(39,3,"{$vCocDat['sucidxxx']} No. ".(($vCocDat['comcsc3x'] != "") ? $vCocDat['comcsc3x'] : str_pad($vCocDat['comcsc2x'],10,'0',STR_PAD_LEFT)),0,0,"R");
							}

							$pdf->SetFont('verdana','',7);
       				$pdf->setXY(10,$py+27);
       				$pdf->Cell(17,3,'PROVEEDOR');
       				$pdf->Cell(2,3,":" );
       				$pdf->Cell(100,3,substr($vCocDat['PRONOMXX'],0,65));
       				// Nit del Proveedor //
       				$pdf->setXY(140,$py+27);
       				$pdf->Cell(40,3,"NIT: {$vCocDat['terid2xx']}-".f_Digito_Verificacion($vCocDat['terid2xx']));

              $pdf->SetFont('verdana','',7);
							$pdf->setXY(178,$py+27);
							$pdf->Cell(31,3,"FECHA:  {$vCocDat['comfecxx']}",0,0,'R');

							//Fecha Vencimiento
							switch($cAlfa){
								case "SIACOSIA":
								case "DESIACOSIP":
								case "TESIACOSIP":
									$pdf->SetFont('verdanab','',7);
									$pdf->setXY(174,$py+10);
									$pdf->Cell(30,3,"FECHA VENCIMIENTO:  {$vCocDat['comfecve']}",0,0,'R');
									break;
								default:
									break;
							}

       				// DATOS DE DETALLE //
       				switch ($cAlfa) {
                case 'TRLXXXXX':
                case 'DETRLXXXXX':
                case 'TETRLXXXXX':
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
  						$pdf->setXY($posx1,$py+33);
  						$pdf->Cell(8,3,'ITEM',0,0,'C');
  						$pdf->Cell($nPosPucId,3,'CUENTA',0,0,'C');
  						$pdf->Cell($nPosCcoId,3,'CENTRO',0,0,'C');
  						$pdf->Cell(18,3,'SUBCENTRO',0,0,'C');
  						$pdf->Cell($nPosDocCr,3,'DOC.CRUCE',0,0,'C');
  						$pdf->Cell($nPosPucDes,3,'DESCRIPCION',0,0,'C');
  						$pdf->Cell($nPosComObs,3,'OBSERVACIONES',0,0,'C');
  						$pdf->Cell(20,3,'DEBITO',0,0,'R');
  						$pdf->Cell(20,3,'CREDITO',0,0,'R');
  					}

  					if ($P == $nFilCod+1) { /// INCREMENTA CADA REGISTRO QUE TENGA LA GRILLA  y Pinto la grilla/////
  						break;
  					}
  					///////////////// PINTO EL DETALLE /////////////////////////////
						$pdf->SetFont('verdana','',7);
						$pdf->setXY($posx1,$posy-11);
						$pdf->Cell(8,3,str_pad($P,3,'0',STR_PAD_LEFT),0,0,'C');
						$pdf->Cell($nPosPucId,3,$mCodDat[$k]['pucidxxx'],0,0,'L');
						$pdf->Cell($nPosCcoId,3,$mCodDat[$k]['ccoidxxx'],0,0,'C');
  					$pdf->Cell(18,3,$mCodDat[$k]['sccidxxx'],0,0,'L');
						$pdf->Cell($nPosDocCr,3,($mCodDat[$k]['comidcxx']."-".$mCodDat[$k]['comcodcx']."-".$mCodDat[$k]['comcsccx']."-".$mCodDat[$k]['comseqcx']),0,0,'L');
						//$pdf->cell(70,3,substr($cComDes,0,40),0,0,'L');
						$pdf->cell($nPosPucDes,3,substr($mCodDat[$k]['ctodesxp'],0,22),0,0,'L');
						$pdf->cell($nPosComObs,3,substr($mCodDat[$k]['comobsxx'],0,20),0,0,'L');
						// If para mostrar en la grilla el valor de credito y debito ///
						if ($mCodDat[$k]['commovxx'] == 'C') {
							//$pdf->setXY($posxdb,$posy); //Debito //Credito
							$pdf->Cell(20,3,'');
							$pdf->Cell(20,3,number_format((($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']),0,',','.'),0,0,'R');
							$SumCredito1 += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : "0";
						} else {
							//$pdf->setXY($posxcr,$posy);  //Credito //Debito
							$pdf->Cell(20,3,number_format((($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf']),0,',','.'),0,0,'R');
							$pdf->Cell(20,3,'');
							$total1 += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : $mCodDat[$k]['comvlrnf'];
							$SumDebito1 += ($mCodDat[$k]['comvlrxx'] > 0) ? $mCodDat[$k]['comvlrxx'] : "0";
						}
						///////////////// PINTO EL DETALLE /////////////////////////////
						$BaseIva1 += ($mCodDat[$k]['comvlr01'] > 0) ? $mCodDat[$k]['comvlr01'] : "0";
						$TotalIva1 += ($mCodDat[$k]['comvlr02'] > 0) ? $mCodDat[$k]['comvlr02'] : "0";
  					$posy+=3;
  				}

  				// Para q muestre la suma del debito y credito
  				$pdf->SetFont('verdanab','',7);
  				$pdf->setXY($posx1+160,$posy-5);
  				$pdf->Cell(20,3,'$'.number_format($SumDebito1,0,',','.'),0,0,'R');
  				$pdf->Cell(20,3,'$'.number_format($SumCredito1,0,',','.'),0,0,'R');
  				//Fin mostras sumatorias

  				// Pinta la tabla que contiene Basa iva y total
  				$posy+=3;
  				$pdf->SetFont('verdanab','',7);
  				$pdf->setXY($posx1+60,$posy+1);
  				//$pdf->SetTextColor(255,150,90); Color de la letra
  				$pdf->Cell(22,5,'Base IVA',1,0,'C');
  				$pdf->Cell(22,5,'$'.number_format($BaseIva1,0,',','.'),1,0);
  				$pdf->Cell(22,5,'Valor IVA',1,0,'C');
  				$pdf->Cell(22,5,'$'.number_format($TotalIva1,0,',','.'),1,0);
  				// Fin pintar tabla

  				// Termino de leer el cursor del la $Mat2 //
  				if($P < 20) {// CUANDO SON POCOS REGISTROS  " pregunto si la grilla es menor que 20 para pintar "
  					$pdf->SetFont('verdanab','',7);
  					//$posy+=2;
  					$pdf->SetXY(10,250);
  					$pdf->Cell(15,3,'OBSERVACIONES GENERALES :');
  					//$posy+=3;
  					$pdf->SetFont('verdana','',7);
  					$pdf->SetXY(10,255);
  					$pdf->Cell(50,3,$vCocDat['comobsxx']);
  					$py1=260;

  					$posy+=3;
  					$pdf->SetFont('verdanab','',7);
  					$pdf->setXY(138,$py1-20);
  					$pdf->Cell(40,3,'TOTAL : ',0,0,'R');
  					$pdf->Cell(30,3,'$'.number_format($total1,0,',','.'),0,0,'R');
  					$py1+=1;
  					$pdf->Rect(10,$py1,198,10);
  					$pdf->Line(85,$py1,85,$py1+10);
  					$pdf->SetFont('verdana','',7);
  					$py1+=3;
  					$pdf->setXY(10, $py1);
  					$pdf->Cell(25,3,$vCocDat['USRNOMXX']);
  					$py1+=3;
  					$pdf->setXY(10,$py1);
  					$pdf->Cell(25,3,"Elaboro",0,0,'C');
  					$pdf->Cell(20,3,"Revisado",0,0,'C');
  					$pdf->Cell(20,3,"Aprobado",0,0,'C');
  					$pdf->setXY(95,$py1);
  					$pdf->Cell(95,3,"Firma y Sello.",0,0,'C');
  					$pdf->SetFont('verdana','',5);
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
    f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
  }

	echo "<html><script>document.location='$cFile';</script></html>";
?>

<?php
  /**
	 * Imprime Certificado Mensual de Retenciones x Pagos a Terceros.
	 * --- Descripcion: Permite Imprimir Certificado Mensual de Retenciones x Pagos a Terceros.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 * @version 002
	 */

	// ini_set('error_reporting', E_ERROR);
  // ini_set('display_errors','1');

	ini_set("memory_limit","512M");
	set_time_limit(0);
	
	$nSwitch = 0;

	/**
   * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales
   * @var Number
   */
  $cEjePro = 0;

  /**
   * Nombre(s) de los archivos en excel generados
   */
  $cNomArc = "";

  /**
   * Cuando se ejecuta desde el cron debe armarse la cookie para incluir los utilitys
   */
  if ($_SERVER["SERVER_PORT"] == "") {
    $vArg = explode(",", $argv[1]);

    if ($vArg[0] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El parametro Id del Proceso no puede ser vacio.\n";
    }

    if ($vArg[1] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El parametro de la Cookie no puede ser vacio.\n";
    }

    if ($nSwitch == 0) {
      $_COOKIE["kDatosFijos"] = $vArg[1];

      # Librerias
      include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
      include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utility.php");
			include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php");
			include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utimovdo.php");

      /**
       * Buscando el ID del proceso
       */
      $qProBg = "SELECT * ";
      $qProBg .= "FROM $cBeta.sysprobg ";
      $qProBg .= "WHERE ";
      $qProBg .= "pbaidxxx= \"{$vArg[0]}\" AND ";
      $qProBg .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
      $xProBg = f_MySql("SELECT", "", $qProBg, $xConexion01, "");
      if (mysql_num_rows($xProBg) == 0) {
        $xRPB = mysql_fetch_array($xProBg);
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "El Proceso en Background [{$vArg[0]}] No Existe o ya fue Procesado.\n";
      } else {
        $xRB = mysql_fetch_array($xProBg);

        /**
         * Reconstruyendo Post
         */
        $mPost = f_Explode_Array($xRB['pbapostx'], "|", "~");
        for ($nP = 0; $nP < count($mPost); $nP++) {
          if ($mPost[$nP][0] != "") {
            $_POST[$mPost[$nP][0]] = $mPost[$nP][1];
          }
        }
      }
    }
  }
	
	/**
   * Subiendo el archivo al sistema
   */
  if ($_SERVER["SERVER_PORT"] != "") {
    # Librerias
    include("../../../../config/config.php");
    include("../../../../libs/php/utility.php");
		include("../../../../../libs/php/utiprobg.php");
		include("../../../../../financiero/libs/php/utimovdo.php");
  }

  if ($_SERVER["SERVER_PORT"] != "") {
    $cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
	}

	if ($_SERVER["SERVER_PORT"] == "") {
		$gTerId   = $_POST['gTerId'];
		$gAnioD   = $_POST['gAnioD'];
		$gMesD    = $_POST['gMesD'];
		$gAnioH   = $_POST['gAnioH'];
		$gMesH    = $_POST['gMesH'];
		$gGenerar = $_POST['gGenerar'];
		$gIntPag  = $_POST['gIntPag'];
		$cTipo    = $_POST['cTipo'];	
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

  $vNitCertificados = explode(",", $vSysStr['aduanera_nit_mostrar_pedido_certificado_pagos_terceros']);//Nit a los que se le imprimie el pedido en aduanera

  //Fecha desde
  $dFecDes = $gAnioD."-".$gMesD."-01";

  //Fecha hasta
  $dDia    = date("d",(mktime(0,0,0,$gMesH+1,1,$gAnioH)-1));
	$dFecHas = $gAnioH."-".$gMesH."-".str_pad($dDia, 2, "0", STR_PAD_LEFT);

  // f_Mensaje(__FILE__,__LINE__,$dFecDes." ~ ".$dFecHas);

  // array para el envío de datos al método
  $vDatos = array();
  $vDatos['cTipo']    = $cTipo;    // Tipo de impresión, por pdf o excel
  $vDatos['cGenerar'] = $cGenerar; // opción para impresión: facturado y/o no facturado
  $vDatos['cIntPag']  = $cIntPag;  // Intermediación de Pagos
  $vDatos['cTerId']   = $gTerId;   // Tercero
  $vDatos['dFecDes']  = $dFecDes;  // Fecha desde
	$vDatos['dFecHas']  = $dFecHas;  // Fecha Hasta
	
	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro  = 1;
		$nRegistros = 0;

		$strPost  = "gTerId~".$gTerId."|";
		$strPost .= "gTerNom~".$gTerNom."|";
		$strPost .= "gAnioD~".$gAnioD."|";
		$strPost .= "gMesD~".$gMesD."|";
		$strPost .= "gAnioH~".$gAnioH."|";
		$strPost .= "gMesH~".$gMesH."|";
		$strPost .= "cGenerar~".$cGenerar."|";
		$strPost .= "cIntPag~".$cIntPag."|";
		$strPost .= "cTipo~".$cTipo;
  
    $vParBg['pbadbxxx'] = $cAlfa;                                         	//Base de Datos
    $vParBg['pbamodxx'] = "FACTURACION";                                  	//Modulo
    $vParBg['pbatinxx'] = "CERTIFICADOXPAGOSATERCEROS";                	    //Tipo Interface
    $vParBg['pbatinde'] = "CERTIFICADO X PAGOS A TERCEROS";                 //Descripcion Tipo de Interfaz
    $vParBg['admidxxx'] = "";                                             	//Sucursal
    $vParBg['doiidxxx'] = "";                                             	//Do
    $vParBg['doisfidx'] = "";                                             	//Sufijo
    $vParBg['cliidxxx'] = "";                                             	//Nit
    $vParBg['clinomxx'] = "";                                             	//Nombre Importador
    $vParBg['pbapostx'] = $strPost;																					//Parametros para reconstruir Post
    $vParBg['pbatabxx'] = "";                                             	//Tablas Temporales
    $vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];                    	//Script
    $vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];                        	//cookie
    $vParBg['pbacrexx'] = $nRegistros;                                    	//Cantidad Registros
    $vParBg['pbatxixx'] = 1;                                              	//Tiempo Ejecucion x Item en Segundos
    $vParBg['pbaopcxx'] = "";                                             	//Opciones
    $vParBg['regusrxx'] = $kUser;                                         	//Usuario que Creo Registro
  
    #Incluyendo la clase de procesos en background
    $ObjProBg = new cProcesosBackground();
    $mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);
  
    #Imprimiendo resumen de todo ok.
    if ($mReturnProBg[0] == "true") {
      f_Mensaje(__FILE__, __LINE__, "Proceso en Background Agendado con Exito."); ?>
      <script languaje = "javascript">
          parent.fmwork.fnRecargar();
      </script>
    <?php } else {
      $nSwitch = 1;
      for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= $mReturnProBg[$nR] . "\n";
      }
      f_Mensaje(__FILE__, __LINE__, $cMsj."Verifique.");
    }
	}

	if ($cEjePro == 0) {
		// Se instancia la clase cMovimientoDo del utility utimovdo.php
		$ObjMovimiento = new cMovimientoDo();
		// se envían todos los datos necesarios al método fnPagosaTerceros
		$mReturn = $ObjMovimiento->fnPagosaTerceros($vDatos);
		$mDatos  = $mReturn[1];
		$vCocDat = $mReturn[4];
		
		switch ($cTipo) {
			case "1": //Impirmir como Pdf

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
						global $cPlesk_Skin_Directory; global $nBan; global $posy;
						global $dFecDes; global $dFecHas;global $mCocDat; global $vResDat;
						global $cAlfa; global $cIntPag;
						if($this->PageNo() >1 && $nBan == 1) {
							switch($cAlfa){
								case "DEINTERLOG":
								case "TEINTERLOG":
								case "INTERLOG":
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/MaryAire.jpg',10,10,45,15);
								break;
								case "TEADIMPEXX": // ADIMPEX
								case "DEADIMPEXX": // ADIMPEX
								case "ADIMPEXX": // ADIMPEX
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex4.jpg',12,11,36,8);
								break;
								case "DECOLMASXX":
								case "TECOLMASXX":
								case "COLMASXX":
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/colmas.jpg',10,10,40,15);
									$this->SetFont('verdanab','',6);
									$this->SetTextColor(129,129,133);
									$this->setXY(10,21);
									$this->Cell(28,10,$vSysStr['financiero_nit_agencia_aduanas'].'-'.f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']),0,0,'L');
									$this->SetTextColor(0,0,0);
								break;
								case "ROLDANLO"://ROLDAN
								case "TEROLDANLO"://ROLDAN
								case "DEROLDANLO"://ROLDAN
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',10,10,37,19);
								break;
								case "ADUANAMO": //ADUANAMO
								case "DEADUANAMO": //ADUANAMO
								case "TEADUANAMO": //ADUANAMO
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamo.jpg',10,10,30,19);
								break;
								case "CASTANOX":
								case "TECASTANOX":
								case "DECASTANOX":
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomartcam.jpg',10,10,35,19);
								break;
								case "ALMACAFE": //ALMACAFE
								case "TEALMACAFE": //ALMACAFE
								case "DEALMACAFE": //ALMACAFE
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalmacafe.jpg',12,10,35,15);
								break;
								case "CARGOADU": //CARGOADU
								case "TECARGOADU": //CARGOADU
								case "DECARGOADU": //CARGOADU
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoCargoAduana.png',12,8,37,17);
								break;
								case "GRUMALCO"://GRUMALCO
								case "TEGRUMALCO"://GRUMALCO
								case "DEGRUMALCO"://GRUMALCO
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',10,10,37,19);
								break;
								case "ALADUANA": //ALADUANA
								case "TEALADUANA": //ALADUANA
								case "DEALADUANA": //ALADUANA
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',10,8,30,15);
								break;
								case "ANDINOSX": //ANDINOSX
								case "TEANDINOSX": //ANDINOSX
								case "DEANDINOSX": //ANDINOSX
									$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoandinos.jpg', 10, 8, 35, 15);
								break;
								case "GRUPOALC": //GRUPOALC
								case "TEGRUPOALC": //GRUPOALC
								case "DEGRUPOALC": //GRUPOALC
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalc.jpg',10,8,30,15);
								break;
								case "AAINTERX": //AAINTERX
								case "TEAAINTERX": //AAINTERX
								case "DEAAINTERX": //AAINTERX
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointernacional.jpg',10,8,30,15);
								break;	
								case "AALOPEZX":
								case "TEAALOPEZX":
								case "DEAALOPEZX":
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaalopez.png',10,8,25);
								break;
								case "ADUAMARX": //ADUAMARX
								case "TEADUAMARX": //ADUAMARX
								case "DEADUAMARX": //ADUAMARX
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg',7,7,20);
								break;
								case "SOLUCION": //SOLUCION
								case "TESOLUCION": //SOLUCION
								case "DESOLUCION": //SOLUCION
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',7,10,35);
								break;
								case "FENIXSAS": //FENIXSAS
								case "TEFENIXSAS": //FENIXSAS
								case "DEFENIXSAS": //FENIXSAS
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg',7,10,38);
								break;
								case "COLVANXX": //COLVANXX
								case "TECOLVANXX": //COLVANXX
								case "DECOLVANXX": //COLVANXX
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logocolvan.jpg',7,7,44);
								break;
								case "INTERLAC": //INTERLAC
								case "TEINTERLAC": //INTERLAC
								case "DEINTERLAC": //INTERLAC
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointerlace.jpg',7,6,44);
								break;
								
								case "DHLEXPRE": //DHLEXPRE
								case "TEDHLEXPRE": //DHLEXPRE
								case "DEDHLEXPRE": //DHLEXPRE
									$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',10,10,37,17);
								break;
								case "KARGORUX": //KARGORUX
								case "TEKARGORUX": //KARGORUX
								case "DEKARGORUX": //KARGORUX
									$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logokargoru.jpg', 10, 10, 37, 17);
								break;
								case "ALOGISAS": //LOGISTICA
								case "TEALOGISAS": //LOGISTICA
								case "DEALOGISAS": //LOGISTICA
									$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 6, 5, 60);
								break;
								case "PROSERCO":
								case "TEPROSERCO":
								case "DEPROSERCO":
									$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoproserco.png', 6, 3, 45);
								break;
                case "MANATIAL":
                case "TEMANATIAL":
                case "DEMANATIAL":
                  $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomanantial.jpg', 6, 12, 34, 12);
                break;
                case "DSVSASXX":
                case "DEDSVSASXX":
                case "TEDSVSASXX":
                  $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logodsv.jpg', 6, 12, 34, 12);
                break;
                case "MELYAKXX":    //MELYAK
                case "DEMELYAKXX":  //MELYAK
                case "TEMELYAKXX":  //MELYAK
                  $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomelyak.jpg', 6, 12, 34, 12);
                break;
                case "FEDEXEXP":    //FEDEX
                case "DEFEDEXEXP":  //FEDEX
                case "TEFEDEXEXP":  //FEDEX
                  $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logofedexexp.jpg', 6, 10, 33);
                break;
								case "EXPORCOM":    //EXPORCOMEX
								case "DEEXPORCOM":  //EXPORCOMEX
								case "TEEXPORCOM":  //EXPORCOMEX
									$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoexporcomex.jpg', 6, 8, 35);
								break;
								case "HAYDEARX":   //HAYDEARX
								case "DEHAYDEARX": //HAYDEARX
								case "TEHAYDEARX": //HAYDEARX
									$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logohaydear.jpeg', 6, 12, 40, 15);
								break;
							}
							##Fin Switch para imprimir LOGO##
							$posy = 15;
							$this->SetFont('verdanab','',9);
							$this->setXY(5,$posy);

							switch($cAlfa){
								case "ROLDANLO":
								case "DEROLDANLO":
								case "TEROLDANLO":
									if ($cIntPag == "SI") {
										$this->Cell(270,10,"CERTIFICADO MENSUAL DE INTERMEDIACION DE PAGO",0,0,'C');
									} else {
										$this->Cell(270,10,"CERTIFICADO MENSUAL DE MANDATO",0,0,'C');
									}
								break;
								default:
									if ($cIntPag == "SI") {
										$this->Cell(270,10,"CERTIFICADO MENSUAL DE INTERMEDIACION DE PAGO",0,0,'C');
									} else {
										$this->Cell(270,10,"CERTIFICADO MENSUAL DE RETENCIONES POR PAGOS A TERCEROS",0,0,'C');
									}
								break;
							}

							$this->Ln(5);
							$this->Cell(270,10,"PERIODO: DEL $dFecDes AL $dFecHas ",0,0,'C');

							$posy += 15;
							$this->setXY(5,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(28,10,"DO",0,0,'C');
							$this->Rect(5,$posy+2,28,5);
							$this->setXY(33,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(15,10,"FACTURA",0,0,'C');
							$this->Rect(33,$posy+2,15,5);
							$this->setXY(48,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(15,10,"TERCERO",0,0,'C');
							$this->Rect(48,$posy+2,15,5);
							$this->setXY(63,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(30,10,"NOMBRE",0,0,'C');
							$this->Rect(63,$posy+2,30,5);
							$this->setXY(93,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(20,10,"DOCUMENTO",0,0,'C');
							$this->Rect(93,$posy+2,20,5);
							$this->setXY(113,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(20,10,"FECHA",0,0,'C');
							$this->Rect(113,$posy+2,20,5);
							$this->setXY(133,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(27,10,"CONCEPTO",0,0,'C');
							$this->Rect(133,$posy+2,27,5);
							$this->setXY(160,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(15,10,"COSTO",0,0,'C');
							$this->Rect(160,$posy+2,15,5);
							$this->setXY(175,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(15,10,"IVA",0,0,'C');
							$this->Rect(175,$posy+2,15,5);
							$this->setXY(190,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(20,10,"TOTAL",0,0,'C');
							$this->Rect(190,$posy+2,20,5);
							$this->setXY(210,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(15,10,"TIPO",0,0,'C');
							$this->Rect(210,$posy+2,15,5);
							$this->setXY(225,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(20,10,"VALOR BASE",0,0,'C');
							$this->Rect(225,$posy+2,20,5);
							$this->setXY(245,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(10,10,"%",0,0,'C');
							$this->Rect(245,$posy+2,10,5);
							$this->setXY(255,$posy);
							$this->SetFont('verdanab','',6);
							$this->Cell(20,10,"VALOR",0,0,'C');
							$this->Rect(255,$posy+2,20,5);
						}
					}

					function Footer() {
						$this->SetY(-10);
						$this->SetFont('verdana','',6);
						$this->Cell(0,5,'PAGINA '.$this->PageNo().' DE {nb}',0,0,'C');
					}
				}

				$pdf = new PDF('L','mm','Letter');
				$pdf->AddFont('verdana','','verdana.php');
				$pdf->AddFont('verdanab','','verdanab.php');
				$pdf->SetFont('verdana','',8);
				$pdf->AliasNbPages();
				$pdf->SetMargins(0,0,0);
				$pdf->SetAutoPageBreak(false);

				/* Siguiente Pagina */
				$pdf->AddPage();

				$posyFin = 185;
				$posy	= 15;  /** PRIMERA POSICION DE Y **/
				$nBan = 1;

				##Switch para imprimir LOGO##
				switch($cAlfa){
					case "DEINTERLOG":
					case "TEINTERLOG":
					case "INTERLOG":
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/MaryAire.jpg',10,10,45,15);
					break;
					case "DECOLMASXX":
					case "TECOLMASXX":
					case "COLMASXX":
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/colmas.jpg',10,10,40,15);
						$pdf->SetFont('verdanab','',6);
						$pdf->SetTextColor(129,129,133);
						$pdf->setXY(10,21);
						$pdf->Cell(28,10,$vSysStr['financiero_nit_agencia_aduanas'].'-'.f_Digito_Verificacion($vSysStr['financiero_nit_agencia_aduanas']),0,0,'L');
						$pdf->SetTextColor(0,0,0);
					break;
					case "LOGINCAR":
					case "DELOGINCAR":
					case "TELOGINCAR":
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/Logo_Login_Cargo_Ltda_2.jpg',10,10,45,12);
					break;
					case "TRLXXXXX":
					case "DETRLXXXXX":
					case "TETRLXXXXX":
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma.jpg',10,10,24,15);
					break;
					case "TEADIMPEXX": // ADIMPEX
					case "DEADIMPEXX": // ADIMPEX
					case "ADIMPEXX": // ADIMPEX
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex4.jpg',12,11,45,10);
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex5.jpg',255,00,25,20);
					break;
					case "ROLDANLO"://ROLDAN
					case "TEROLDANLO"://ROLDAN
					case "DEROLDANLO"://ROLDAN
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',10,10,37,19);
					break;
					case "ADUANAMO": //ADUANAMO
					case "DEADUANAMO": //ADUANAMO
					case "TEADUANAMO": //ADUANAMO
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamo.jpg',10,10,30,19);
					break;
					case "CASTANOX":
					case "TECASTANOX":
					case "DECASTANOX":
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomartcam.jpg',13,10,35,19);
					break;
					case "ALMACAFE": //ALMACAFE
					case "TEALMACAFE": //ALMACAFE
					case "DEALMACAFE": //ALMACAFE
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalmacafe.jpg',12,10,35,15);
					break;
					case "CARGOADU": //CARGOADU
					case "TECARGOADU": //CARGOADU
					case "DECARGOADU": //CARGOADU
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoCargoAduana.png',12,8,37,17);
					break;
					case "GRUMALCO"://GRUMALCO
					case "TEGRUMALCO"://GRUMALCO
					case "DEGRUMALCO"://GRUMALCO
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',10,10,37,19);
					break;
					case "ALADUANA"://ALADUANA
					case "TEALADUANA"://TEALADUANA
					case "DEALADUANA"://DEALADUANAfrcpa
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',4,7,27,18);
					break;
					case "ANDINOSX"://ANDINOSX
					case "TEANDINOSX"://TEANDINOSX
					case "DEANDINOSX"://DEANDINOSX
						$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoandinos.jpg', 4, 7, 34, 18);
					break;
					case "GRUPOALC"://GRUPOALC
					case "TEGRUPOALC"://TEGRUPOALC
					case "DEGRUPOALC"://DEGRUPOALC
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalc.jpg',5,10,35,16);
					break;
					case "AAINTERX"://AAINTERX
					case "TEAAINTERX"://TEAAINTERX
					case "DEAAINTERX"://DEAAINTERX
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointernacional.jpg',4,7,27,18);
					break;
					case "AALOPEZX":
					case "TEAALOPEZX":
					case "DEAALOPEZX":
						$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoaalopez.png', 6, 7, 27);
					break;
					case "ADUAMARX"://ADUAMARX
					case "TEADUAMARX"://TEADUAMARX
					case "DEADUAMARX"://DEADUAMARX
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg',7,7,20);
					break;
					case "SOLUCION"://SOLUCION
					case "TESOLUCION"://TESOLUCION
					case "DESOLUCION"://DESOLUCION
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',7,10,35);
					break;
					case "FENIXSAS"://FENIXSAS
					case "TEFENIXSAS"://TEFENIXSAS
					case "DEFENIXSAS"://DEFENIXSAS
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg',7,10,38);
					break;
					case "COLVANXX"://COLVANXX
					case "TECOLVANXX"://TECOLVANXX
					case "DECOLVANXX"://DECOLVANXX
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logocolvan.jpg',7,7,44);
					break;
					case "INTERLAC"://INTERLAC
					case "TEINTERLAC"://TEINTERLAC
					case "DEINTERLAC"://DEINTERLAC
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointerlace.jpg',7,6,44);
					break;
					case "DHLEXPRE": //DHLEXPRE
					case "TEDHLEXPRE": //DHLEXPRE
					case "DEDHLEXPRE": //DHLEXPRE
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',10,10,37,17);
					break;
					case "KARGORUX": //KARGORUX
					case "TEKARGORUX": //KARGORUX
					case "DEKARGORUX": //KARGORUX
						$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logokargoru.jpg', 10, 10, 37, 17);
					break;
					case "ALOGISAS": //LOGISTICA
					case "TEALOGISAS": //LOGISTICA
					case "DEALOGISAS": //LOGISTICA
						$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 6, 5, 60);
					break;
					case "PROSERCO":
					case "TEPROSERCO":
					case "DEPROSERCO":
						$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoproserco.png', 6, 3, 45);
					break;
          case "MANATIAL":
          case "TEMANATIAL":
          case "DEMANATIAL":
            $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomanantial.jpg', 6, 12, 45, 12);
          break;
          case "DSVSASXX":
          case "DEDSVSASXX":
          case "TEDSVSASXX":
            $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logodsv.jpg', 6, 12, 34, 12);
          break;
          case "MELYAKXX":    //MELYAK
          case "DEMELYAKXX":  //MELYAK
          case "TEMELYAKXX":  //MELYAK
            $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomelyak.jpg', 6, 12, 34, 12);
          break;
          case "FEDEXEXP":    //FEDEX
          case "DEFEDEXEXP":  //FEDEX
          case "TEFEDEXEXP":  //FEDEX
            $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logofedexexp.jpg', 6, 10, 33);
          break;
					case "EXPORCOM":    //EXPORCOMEX
					case "DEEXPORCOM":  //EXPORCOMEX
					case "TEEXPORCOM":  //EXPORCOMEX
						$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoexporcomex.jpg', 6, 8, 35);
					break;
					case "HAYDEARX":   //HAYDEARX
					case "DEHAYDEARX": //HAYDEARX
					case "TEHAYDEARX": //HAYDEARX
						$pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logohaydear.jpeg', 6, 12, 40, 15);
					break;
				}
				##Switch para imprimir LOGO##

				$pdf->SetFont('verdanab','',9);
				$pdf->setXY(120,$posy);
				$pdf->Cell(50,10,"CERTIFICADO MENSUAL DE RETENCIONES POR PAGOS A TERCEROS",0,0,'C');

				switch($cAlfa){
					case "SIACOSIP":
					case "SIACOSIA":
					case "TESIACOSIP":
					case "DESIACOSIP":
						//Introduccion para siaco
						$cLeyenda  = "El suscrito Revisor Fiscal de la Agencia de Aduanas Siaco S.A.S Nivel 1 certifica que los valores relacionados ";
						$cLeyenda .= "a continuacion hacen parte de los costos o gastos y de los impuestos descontables de nuestro cliente {$mDatos[0]['clinomxx']} ";
						$cLeyenda .= "Nit. ".$gTerId."-".f_Digito_Verificacion($gTerId)." correspondiente a lo facturado el PERIODO: DEL $dFecDes AL $dFecHas, ";
						$cLeyenda .= "por lo tanto no se han tomado como deducciones o impuestos descontables por parte de la Agencia de Aduanas, ";
						$cLeyenda .= "igualmente certificamos que se han practicado y pagado las retenciones de acuerdo a la normatividad vigente.";
						$posy += 15;
						$pdf->SetFont('verdana','',10);
						$pdf->setXY(10,$posy);
						$pdf->MultiCell(260,5,$cLeyenda,0,'J');
						//fin
						$posy += 25;
					break;
					case "DECOLMASXX":
					case "TECOLMASXX":
					case "COLMASXX":
						//Introduccion para colmas
						$cLeyenda  = "El suscrito Revisor Fiscal y/o Contador Publico de la Agencia de Aduanas Colmas S.A.S. Nivel 1  certifica que ";
						$cLeyenda .= "los valores relacionados a continuacion hacen parte de los costos o gastos y de los impuestos descontables de ";
						$cLeyenda .= "nuestro cliente {$mDatos[0]['clinomxx']} Nit. ".$gTerId."-".f_Digito_Verificacion($gTerId)." correspondiente a lo facturado el ";
						$cLeyenda .= "PERIODO: DEL $dFecDes AL $dFecHas, por lo tanto no se han tomado como deducciones o impuestos descontables por ";
						$cLeyenda .= "parte de la Agencia de Aduanas, igualmente certificamos que se han practicado y pagado las retenciones de acuerdo ";
						$cLeyenda .= "a la normatividad vigente.";
						$posy += 15;
						$pdf->SetFont('verdana','',10);
						$pdf->setXY(10,$posy);
						$pdf->MultiCell(260,5,$cLeyenda,0,'J');
						//fin
						$posy += 25;
					break;
					case "DEADIMPEXX":
					case "TEADIMPEXX":
					case "ADIMPEXX":
						$posy += 5;
						//Introduccion para Adimpex
						$cLeyenda  = "EL área de contabilidad de la compañía AGENCIA DE ADUANAS ADUANAMIENTOS IMPORTACIONES Y EXPORTACIONES S.A.S NIVEL 2 ";
						$cLeyenda .= "con Nit 830.032.263-9 de acuerdo con el artículo 3 del decreto 1514 de 1998, la Agencia a efectuado pagos a terceros en ";
						$cLeyenda .= "calidad de mandatario por cuenta del cliente {$mDatos[0]['clinomxx']} con Nit ".$gTerId."-".f_Digito_Verificacion($gTerId).", Así: ";
						$posy += 15;
						$pdf->SetFont('verdana','',10);
						$pdf->setXY($posx+5,$posy);
						$pdf->MultiCell(260,5,utf8_decode($cLeyenda),0,'J');
						//fin
						$posy += 25;
					break;
				  case "DEFEDEXEXP":
					case "TEFEDEXEXP":
					case "FEDEXEXP":
						//Introduccion para Fedex
						$cLeyenda1  = "Con el fin de acreditar el cumplimiento de lo dispuesto en el artículo 1.6.1.4.9 del decreto único reglamentario del 2016, bajo la gravedad de juramento.";
						$cLeyenda2  = "Que, para efectos de soportar los respectivos costos, deducciones o impuestos descontables o devoluciones a que tenga ";
						$cLeyenda2 .= "derecho el mandante, se relaciona a continuación el concepto y la cuantía en los que se incurrieron en la celebración del contrato de mandato.";
					
						$posy += 20;
						$pdf->SetFont('verdana','',8);
						$pdf->setXY($nPosX+5,$posy);
						$pdf->MultiCell(260,4,utf8_decode($cLeyenda1),0,'C');
						$posy += 6;
						$pdf->SetFont('verdanab','',8);
						$pdf->setXY($nPosX+5,$posy);
						$pdf->Cell(260,4,"CERTIFICO",0,0,'C');
						$posy += 6;
						$pdf->SetFont('verdana','',8);
						$pdf->setXY($nPosX+5,$posy);
						$pdf->MultiCell(260,4,utf8_decode($cLeyenda2),0,'J');
						//fin
						$posy += 10;
					break;
					default:
						$pdf->Ln(5);
						$pdf->setX(5);
						$pdf->Cell(270,10,"PERIODO: DEL $dFecDes AL $dFecHas ",0,0,'C');
						$posy += 12;
						break;
				}

				$pdf->setXY(5,$posy);
				$pdf->SetFont('verdanab','',7);
				$pdf->Cell(12,10,"Cliente:",0,0,'L');
				$pdf->SetFont('verdana','',7);
				$pdf->Cell(168,10,$mDatos[0]['clinomxx'],0,0,'L');
				$pdf->SetFont('verdanab','',7);
				$pdf->Cell(10,10,"NIT: ",0,0,'L');
				$pdf->SetFont('verdana','',7);
				$pdf->Cell(30,10,substr($gTerId."-".f_Digito_Verificacion($gTerId),0,45),0,0,'L');
				$pdf->SetFont('verdanab','',7);
				$pdf->Cell(30,10,"FECHA IMPRESION : ",0,0,'L');
				$pdf->SetFont('verdana','',7);
				$pdf->Cell(20,10,date('Y-m-d'),0,0,'R');
				$posy += 8;

				$pdf->setXY(5,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(28,10,"DO",0,0,'C');
				$pdf->Rect(5,$posy+2,28,5);
				$pdf->setXY(33,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(15,10,"FACTURA",0,0,'C');
				$pdf->Rect(33,$posy+2,15,5);
				$pdf->setXY(48,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(15,10,"TERCERO",0,0,'C');
				$pdf->Rect(48,$posy+2,15,5);
				$pdf->setXY(63,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(30,10,"NOMBRE",0,0,'C');
				$pdf->Rect(63,$posy+2,30,5);
				$pdf->setXY(93,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(20,10,"DOCUMENTO",0,0,'C');
				$pdf->Rect(93,$posy+2,20,5);
				$pdf->setXY(113,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(20,10,"FECHA",0,0,'C');
				$pdf->Rect(113,$posy+2,20,5);
				$pdf->setXY(133,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(27,10,"CONCEPTO",0,0,'C');
				$pdf->Rect(133,$posy+2,27,5);
				$pdf->setXY(160,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(15,10,"COSTO",0,0,'C');
				$pdf->Rect(160,$posy+2,15,5);
				$pdf->setXY(175,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(15,10,"IVA",0,0,'C');
				$pdf->Rect(175,$posy+2,15,5);
				$pdf->setXY(190,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(20,10,"TOTAL",0,0,'C');
				$pdf->Rect(190,$posy+2,20,5);
				$pdf->setXY(210,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(15,10,"TIPO",0,0,'C');
				$pdf->Rect(210,$posy+2,15,5);
				$pdf->setXY(225,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(20,10,"VALOR BASE",0,0,'C');
				$pdf->Rect(225,$posy+2,20,5);
				$pdf->setXY(245,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(10,10,"%",0,0,'C');
				$pdf->Rect(245,$posy+2,10,5);
				$pdf->setXY(255,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(20,10,"VALOR",0,0,'C');
				$pdf->Rect(255,$posy+2,20,5);
				$posy += 7;

				for($i=0; $i<count($mDatos); $i++) {

					if(($posyFin - $posy) <= 0){
						$pdf->AddPage();
						$posy =37;
					}

					$n = 0;

					$mRetenciones = array();
					$mRetenciones = $mDatos[$i]['retencio'];

					if (count($mRetenciones) > 0) {
						$n += (4*count($mRetenciones));
					}

					$n = ($n != 0) ? $n : 4;

					/**
					 * GPOS-1792
					 * Para aduanera se debe mostrar el pedido en la columna del DO
					 */
					if(in_array($mDatos[$i]['cliidxxx'], $vNitCertificados) == true) {
						$mDatos[$i]['docidxxx'] = substr(trim($mDatos[$i]['docidxxx']." ".$mDatos[$i]['docpedxx']),0,20);
					}

					if (($cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "ROLDANLO") && $mDatos[$i]['docidxxx'] != "") {
						$mDatos[$i]['docidxxx'] = $mDatos[$i]['sucidxxx']."-".$mDatos[$i]['docidxxx']."-".$mDatos[$i]['docsufxx'];
					}

					$pdf->SetFont('verdana','',6);
					$pdf->setXY(5,$posy);
					$pdf->Cell(28,4,$mDatos[$i]['docidxxx'],0,0,'L');
					$pdf->Rect(5,$posy,28,$n);

					$vNitsCli = explode(",",$vSysStr['siacosia_incluir_prefijo_certificado_pcc']);
					if (($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") && in_array($mDatos[$i]['cliidxxx'], $vNitsCli)) {
						// Se obtiene el prefijo de la factura
						$vResPref = explode("~", $mDatos[$i]['residxxx']);

						$pdf->setXY(33,$posy);
						$pdf->Cell(15,4,substr($vResPref[1] . "-" . $mDatos[$i]['facturax'],0,10),0,0,'C');
						$pdf->Rect(33,$posy,15,$n);	
					} else {
						$pdf->setXY(33,$posy);
						$pdf->Cell(15,4,substr($mDatos[$i]['facturax'],0,10),0,0,'C');
						$pdf->Rect(33,$posy,15,$n);
					}
          
					$pdf->setXY(48,$posy);
					$pdf->Cell(15,4,trim($mDatos[$i]['teridxxx']),0,0,'L');
					$pdf->Rect(48,$posy,15,$n);

					$pdf->setXY(63,$posy);
					$pdf->Cell(30,4,substr(trim($mDatos[$i]['ternomxx']),0,20),0,0,'L');
					$pdf->Rect(63,$posy,30,$n);

					$pdf->setXY(93,$posy);
					$pdf->Cell(20,4,$mDatos[$i]['document'],0,0,'C');
					$pdf->Rect(93,$posy,20,$n);

					$pdf->setXY(113,$posy);
					$pdf->Cell(20,4,$mDatos[$i]['comfecxx'],0,0,'C');
					$pdf->Rect(113,$posy,20,$n);

					$pdf->setXY(133,$posy);
					$pdf->Cell(27,4,substr(trim($mDatos[$i]['concepto']),0,18),0,0,'L');
					$pdf->Rect(133,$posy,27,$n);

					$pdf->setXY(160,$posy);
					$pdf->Cell(15,4,number_format($mDatos[$i]['costoxxx'],0,',','.'),0,0,'R');
					$pdf->Rect(160,$posy,15,$n);

					$pdf->setXY(175,$posy);
					$pdf->Cell(15,4,number_format($mDatos[$i]['ivaxxxxx'],0,',','.'),0,0,'R');
					$pdf->Rect(175,$posy,15,$n);

					$pdf->setXY(190,$posy);
					$pdf->Cell(20,4,number_format($mDatos[$i]['totalxxx'],0,',','.'),0,0,'R');
					$pdf->Rect(190,$posy,20,$n);


					if (count($mRetenciones) > 0) {
						$posy2 = $posy;

						for ($y = 0; $y < count($mRetenciones); $y++){
							$pdf->setXY(210,$posy2);
							$pdf->Cell(15,4,$mRetenciones[$y]['retenxxx'],0,0,'L');
							$pdf->Rect(210,$posy2,15,4);

							$pdf->setXY(225,$posy2);
							$pdf->Cell(20,4,number_format($mRetenciones[$y]['comvlr01'],0,',','.'),0,0,'R');
							$pdf->Rect(225,$posy2,20,4);

							$pdf->setXY(245,$posy2);
							$pdf->Cell(10,4,number_format($mRetenciones[$y]['pucretxx'],3,',','.'),0,0,'C');
							$pdf->Rect(245,$posy2,10,4);

							$nRetencion = round($mRetenciones[$y]['comvlrxx']);
							if($mRetenciones[$y]['retenxxx'] == 'ReteCree'){
								$nTotRCre += $nRetencion;
							}
							if($mRetenciones[$y]['retenxxx'] == 'Retefuente'){
								$nTotRfte += $nRetencion;
							}
							if($mRetenciones[$y]['retenxxx'] == 'ReteIva'){
								$nTotRIva += $nRetencion;
							}
							if($mRetenciones[$y]['retenxxx'] == 'ReteIca'){
								$nTotRIca += $nRetencion;
							}

							$pdf->setXY(255,$posy2);
							$pdf->Cell(20,4,number_format($nRetencion,0,',','.'),0,0,'R');
							$pdf->Rect(255,$posy2,20,4);
							$posy2 += 4;
						}
						$posy = $posy2-4;
					}else{
						$pdf->setXY(210,$posy);
						$pdf->Cell(15,4,"",0,0,'L');
						$pdf->Rect(210,$posy,15,4);

						$pdf->setXY(225,$posy);
						$pdf->Cell(20,4,"",0,0,'R');
						$pdf->Rect(225,$posy,20,4);

						$pdf->setXY(245,$posy);
						$pdf->Cell(10,4,"",0,0,'C');
						$pdf->Rect(245,$posy,10,4);

						$pdf->setXY(255,$posy);
						$pdf->Cell(20,4,"",0,0,'R');
						$pdf->Rect(255,$posy,20,4);
					}

					$posy += 4;

					$nTotCos += $mDatos[$i]['costoxxx'];
					$nTotIva += $mDatos[$i]['ivaxxxxx'];
					$nTotFac += $mDatos[$i]['totalxxx'];
				}

				$nBan = 0;
				$posy -= 2;
				$pdf->setXY(5,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(155,10,"TOTAL PAGOS A TERCEROS",0,0,'C');
				$pdf->Rect(5,$posy+2,155,6);
				$pdf->setXY(160,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(15,10,number_format($nTotCos,0,',','.'),0,0,'R');
				$pdf->Rect(160,$posy+2,15,6);
				$pdf->setXY(175,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(15,10,number_format($nTotIva,0,',','.'),0,0,'R');
				$pdf->Rect(175,$posy+2,15,6);
				$pdf->setXY(190,$posy);
				$pdf->SetFont('verdanab','',6);
				$pdf->Cell(20,10,number_format($nTotFac,0,',','.'),0,0,'R');
				$pdf->Rect(190,$posy+2,20,6);
				$pdf->Rect(210,$posy+2,65,6);
				$posy += 10;

				if(($posyFin - $posy) <= 0){
					$pdf->AddPage();
					$posy = 30;
				}
				if($nTotRfte != 0){
					$pdf->setXY(5,$posy);
					$pdf->SetFont('verdanab','',6);
					$pdf->Cell(240,10,utf8_decode("TOTAL RETENCIÓN EN LA FUENTE"),0,0,'L');
					$pdf->Rect(5,$posy+2,240,6);
					$pdf->setXY(245,$posy);
					$pdf->SetFont('verdanab','',6);
					$pdf->Cell(30,10,number_format($nTotRfte,0,',','.'),0,0,'R');
					$pdf->Rect(245,$posy+2,30,6);
					$posy += 6;
				}
				if(($posyFin - $posy) <= 0){
					$pdf->AddPage();
					$posy = 30;
				}
				if($nTotRCre != 0){
					$pdf->setXY(5,$posy);
					$pdf->SetFont('verdanab','',6);
					$pdf->Cell(240,10,utf8_decode("TOTAL RETENCIÓN CREE"),0,0,'L');
					$pdf->Rect(5,$posy+2,240,6);
					$pdf->setXY(245,$posy);
					$pdf->SetFont('verdanab','',6);
					$pdf->Cell(30,10,number_format($nTotRCre,0,',','.'),0,0,'R');
					$pdf->Rect(245,$posy+2,30,6);
					$posy += 6;
				}
				if(($posyFin - $posy) <= 0){
					$pdf->AddPage();
					$posy = 30;
				}
				if($nTotRIva != 0){
					$pdf->setXY(5,$posy);
					$pdf->SetFont('verdanab','',6);
					$pdf->Cell(240,10,utf8_decode("TOTAL RETENCIÓN IVA"),0,0,'L');
					$pdf->Rect(5,$posy+2,240,6);
					$pdf->setXY(245,$posy);
					$pdf->SetFont('verdanab','',6);
					$pdf->Cell(30,10,number_format($nTotRIva,0,',','.'),0,0,'R');
					$pdf->Rect(245,$posy+2,30,6);
					$posy += 6;
				}
				if(($posyFin - $posy) <= 0){
					$pdf->AddPage();
					$posy = 30;
				}
				if($nTotRIca != 0){
					$pdf->setXY(5,$posy);
					$pdf->SetFont('verdanab','',6);
					$pdf->Cell(240,10,utf8_decode("TOTAL RETENCIÓN ICA"),0,0,'L');
					$pdf->Rect(5,$posy+2,240,6);
					$pdf->setXY(245,$posy);
					$pdf->SetFont('verdanab','',6);
					$pdf->Cell(30,10,number_format($nTotRIca,0,',','.'),0,0,'R');
					$pdf->Rect(245,$posy+2,30,6);
					$posy += 6;
				}
				$posy += 15;
				if(($posyFin - $posy) <= 0){
					$pdf->AddPage();
					$posy = 40;
				}

				if ($cAlfa == "FEDEXEXP" || $cAlfa == "DEFEDEXEXP" || $cAlfa == "TEFEDEXEXP") {
					$cLeyenda3  = "Actuando en mi calidad de Contador Público, Rafael Ricardo Buitrago Naranjo con Cédula de ciudadanía 80.196.154 de Bogotá, Tarjeta Profesional 179422-T, ";
					$cLeyenda3 .= "en representación de la empresa AGENCIA DE ADUANAS FEDEX EXPRESS COLOMBIA S.A.S. NIVEL 2 con NIT: 901.106.968-9 quien actúa como mandatario, suscribo la presente certificación.";

					$pdf->setXY(5,$posy-10);
					$pdf->SetFont('verdana','',8);
					$pdf->MultiCell(265,4,utf8_decode($cLeyenda3),0,'J');
					$posy += 12;
				}

				//PAra imprimir la leyenda de ciaco
				switch($cAlfa){
					case "SIACOSIA":
					case "TESIACOSIP":
					case "DESIACOSIP":

						//22 lineas para Firma
						if($posy > 163){
							$pdf->AddPage();
							$posy = 30;
						}

						//Introduccion para siaco
						$cLeyenda= utf8_decode("La presente certificacion se expide a los "). f_FormatFecActa(date('Y-m-d'));
						$pdf->SetFont('verdana','',10);
						$pdf->setXY(10,$posy);
						$pdf->MultiCell(260,5,$cLeyenda,0,'J');

						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/firma_revisor_fiscal_certificados_siaco.jpg',21,$posy+4,48,28);

						$cLeyenda1="Jose Wilson Gonzales Marin \n";
						$cLeyenda1.="S. Revisor Fiscal \n";
						$cLeyenda1.="TP-42796-T";
						$pdf->SetFont('verdanab','',10);
						$pdf->Line(10,$posy+29,80,$posy+29);
						$pdf->setXY(10,$posy+30);
						$pdf->MultiCell(260,5,$cLeyenda1,0,'J');
						//fin
					break;
					case "GRUMALCO":
					case "DEGRUMALCO":
					case "TEGRUMALCO":
						$nPosYCon = (trim($vSysStr['financiero_contador_certificado_de_pagos_a_terceros'])       == "") ? $posy : $posy+4;
						$nPosYRev = (trim($vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros']) == "") ? $posy : $posy+4;
						$pdf->SetFont('verdana','',6);
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/firma_revisor_fiscal_eva_malco.jpg',2,$nPosYCon+2,55);
            $pdf->setXY(4,$nPosYCon+16);
            $pdf->Cell(30,10,"Eva Sandrid Mercado Alfaro",0,0,'L');
            $pdf->setXY(4,$nPosYCon+19);
            $pdf->Cell(30,10,"Revisor Fiscal Principal",0,0,'L');
            $pdf->setXY(4,$nPosYCon+22);
            $pdf->Cell(30,10,"Tarjeta Profesional No. 278074-T",0,0,'L');
            $pdf->setXY(4,$nPosYCon+25);
            $pdf->Cell(30,10,"Designado por Deloitte & Touche S.A.S",0,0,'L');
					break;
					case "COLMASXX":
					case "DECOLMASXX":
					case "TECOLMASXX":
						$nPosYCon = (trim($vSysStr['financiero_contador_certificado_de_pagos_a_terceros'])       == "") ? $posy : $posy+4;

						$pdf->Line(5,$posy+2,80,$posy+2);
						$pdf->setXY(5,$posy+1);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,$vSysStr['financiero_contador_certificado_de_pagos_a_terceros'],0,0,'L');
						$pdf->setXY(5,$nPosYCon);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,"CONTADOR",0,0,'L');
						$pdf->Line(180,$posy+2,260,$posy+2);
						$pdf->setXY(180,$posy);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,"REVISOR FISCAL",0,0,'L');
					break;
					case "FEDEXEXP":
					case "DEFEDEXEXP":
					case "TEFEDEXEXP":
						if($posy > 169){
							$pdf->AddPage();
							$posy = 30;
						}

						$nPosYCon = $posy+5;
						$nPosYRev = $posy+5;

						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/firma_contador_fedex.jpg',5,$nPosYCon-14,45);
						$pdf->Line(5,$nPosYCon+2,80,$nPosYCon+2);
						$pdf->setXY(5,$nPosYCon);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,"CONTADOR",0,0,'L');
						$pdf->setXY(5,$nPosYCon+4);
						$pdf->SetFont('verdana','',6);
						$pdf->Cell(240,10,"Rafael Ricardo Buitrago Naranjo",0,0,'L');
						$pdf->setXY(5,$nPosYCon+7);
						$pdf->Cell(240,10,"CC. 80196154",0,0,'L');
						$pdf->setXY(5,$nPosYCon+10);
						$pdf->Cell(240,10,"Tarjeta Profesional 179422-T",0,0,'L');

						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/firma_representante_legal_fedex.jpg',178,$nPosYRev-17,35);
						$pdf->Line(180,$nPosYRev+2,260,$nPosYRev+2);
						$pdf->setXY(180,$nPosYRev);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,"REPRESENTANTE LEGAL",0,0,'L');
						$pdf->setXY(180,$nPosYRev+4);
						$pdf->SetFont('verdana','',6);
						$pdf->Cell(240,10,"Eduardo Alfonso Garrido",0,0,'L');
						$pdf->setXY(180,$nPosYRev+7);
						$pdf->Cell(240,10,"CC. 8532627",0,0,'L');
					break;
					case "FENIXSAS":
					case "DEFENIXSAS":
					case "TEFENIXSAS":
						$posy += 5;

						$nPosYCon = (trim($vSysStr['financiero_contador_certificado_de_pagos_a_terceros'])       == "") ? $posy : $posy+4;
						$nPosYRev = (trim($vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros']) == "") ? $posy : $posy+4;

						$pdf->Line(5,$posy+2,80,$posy+2);
						$pdf->setXY(5,$posy+1);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,$vSysStr['financiero_contador_certificado_de_pagos_a_terceros'],0,0,'L');
						$pdf->setXY(5,$nPosYCon);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,"CONTADOR",0,0,'L');
						$pdf->Line(180,$posy+2,260,$posy+2);
						$pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/firma_revisor_fiscal_fenix.jpg',205,$posy-15,35);
						$pdf->setXY(180,$posy+1);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,$vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros'],0,0,'L');
						$pdf->setXY(180,$nPosYRev);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,"REVISOR FISCAL",0,0,'L');
					break;
					case "MIRCANAX":
					case "DEMIRCANAX":
					case "TEMIRCANAX":
		
						$posy += 3;

						$pdf->Line(180,$posy+2,260,$posy+2);
						$pdf->setXY(180,$posy+1);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,"NELSON A TRIANA CERVERA ",0,0,'L');
						$pdf->setXY(180,$posy+4);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,"REVISOR FISCAL",0,0,'L');
            $pdf->setXY(180,$posy+7);
            $pdf->Cell(240,10,"CC 14.228.600",0,0,'L');
            $pdf->setXY(180,$posy+10);
            $pdf->Cell(240,10,"TP 40590-T",0,0,'L');
					break;
					default:

						$nPosYCon = (trim($vSysStr['financiero_contador_certificado_de_pagos_a_terceros'])       == "") ? $posy : $posy+4;
						$nPosYRev = (trim($vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros']) == "") ? $posy : $posy+4;

						$pdf->Line(5,$posy+2,80,$posy+2);
						$pdf->setXY(5,$posy+1);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,$vSysStr['financiero_contador_certificado_de_pagos_a_terceros'],0,0,'L');
						$pdf->setXY(5,$nPosYCon);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,"CONTADOR",0,0,'L');
						$pdf->Line(180,$posy+2,260,$posy+2);
						$pdf->setXY(180,$posy+1);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,$vSysStr['financiero_revisor_fiscal_certificado_de_pagos_a_terceros'],0,0,'L');
						$pdf->setXY(180,$nPosYRev);
						$pdf->SetFont('verdanab','',6);
						$pdf->Cell(240,10,"REVISOR FISCAL",0,0,'L');
					break;
				}

				$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";

				$pdf->Output($cFile);

				if (file_exists($cFile)){
					chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
				} else {
					f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
				}

				echo "<html><script>document.location='$cFile';</script></html>";
			break;
			case "2": //Imprimir como Excel
				$nNumCol = ($gTerId != "") ? 14 : 16;
				
				// se suma una columna mas para estas base de datos asi se completa bien los datos que ya estaban para su impresion
				if($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO"){
					$nNumCol += 1;
				}

				/**
				 * GPOS-1792
				 * Para aduanera se debe mostrar el pedido en la columna del DO
				 */
				if(in_array($gTerId, $vNitCertificados) == true) {
					$nNumCol += 1;
				}

				$cNomFile = "CERTIFICADO_MENSUAL_DE_RETENCIONES_POR_PAGOS_A_TERCEROS_".$_COOKIE['kUsrId'].date("YmdHis").".xls";
				
				if ($_SERVER["SERVER_PORT"] != "") {
					$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;
				} else {
					$cFile = "{$OPENINIT['pathdr']}/opencomex/".$vSysStr['system_download_directory']."/".$cNomFile;
				}

				$fOp = fopen($cFile,'a');

				if ($gTerId == "") {

					//Encabezado
					$cData  = "CERTIFICADO MENSUAL DE RETENCIONES POR PAGOS A TERCEROS"."\n";
					$cData .= "PERIODO: DEL ".$dFecDes." AL ".$dFecHas."\n";
					$cData .= "FECHA IMPRESION: ". date("Y-m-d")."\n";
					//Columnas
					$cData .= "NIT\t";
					$cData .= "CLIENTE\t";
					$cData .= "DO\t";
					$cData .= "FACTURA\t";
					$cData .= "TERCERO\t";
					$cData .= "NOMBRE\t";
					$cData .= "DOCUMENTO\t";
					$cData .= "FECHA\t";
					$cData .= "CONCEPTO\t";
					$cData .= "COSTO\t";
					$cData .= "IVA\t";
					$cData .= "TOTAL\t";
					$cData .= "TIPO\t";
					$cData .= "VALOR BASE\t";
					$cData .= "%\t";
					$cData .= "VALOR\t";
					$cData .= "\n";

					fwrite($fOp,$cData);

					for($i=0; $i<count($mDatos); $i++) {

						if (($cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "ROLDANLO") && $mDatos[$i]['docidxxx'] != "") {
							$mDatos[$i]['docidxxx'] = $mDatos[$i]['sucidxxx']."-".$mDatos[$i]['docidxxx']."-".$mDatos[$i]['docsufxx'];
						}

						$mRetenciones = array();
						$mRetenciones = $mDatos[$i]['retencio'];

						$cData  = $mDatos[$i]['cliidxxx']."\t";
						$cData .= $mDatos[$i]['clinomxx']."\t";
						$cData .= $mDatos[$i]['docidxxx']."\t";

						$vNitsCli = explode(",",$vSysStr['siacosia_incluir_prefijo_certificado_pcc']);
						if (($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") && in_array($mDatos[$i]['cliidxxx'], $vNitsCli)) {
							// Se obtiene el prefijo de la factura
							$vResPref = explode("~", $mDatos[$i]['residxxx']);
							$cData .= $vResPref[1] . "-" . $mDatos[$i]['facturax']."\t";
						} else {
							$cData .= $mDatos[$i]['facturax']."\t";
						}

						$cData .= $mDatos[$i]['teridxxx']."\t";
						$cData .= $mDatos[$i]['ternomxx']."\t";
						$cData .= $mDatos[$i]['document']."\t";
						$cData .= $mDatos[$i]['comfecxx']."\t";
						$cData .= $mDatos[$i]['concepto']."\t";
						$cData .= number_format($mDatos[$i]['costoxxx'],0,',','')."\t";
						$cData .= number_format($mDatos[$i]['ivaxxxxx'],0,',','')."\t";
						$cData .= number_format($mDatos[$i]['totalxxx'],0,',','')."\t";

						if (count($mRetenciones) > 0) {
							for ($y = 0; $y < count($mRetenciones); $y++){
								if ($y > 0) {
									$cData  = "\t";
									$cData .= "\t";
									$cData .= "\t";
									$cData .= "\t";
									$cData .= "\t";
									$cData .= "\t";
									$cData .= "\t";
									$cData .= "\t";
									$cData .= "\t";
									$cData .= number_format($mDatos[$i]['costoxxx'],0,',','')."\t";
									$cData .= number_format($mDatos[$i]['ivaxxxxx'],0,',','')."\t";
									$cData .= number_format($mDatos[$i]['totalxxx'],0,',','')."\t";
								}
								$nRetencion = round($mRetenciones[$y]['comvlrxx']);
								if($mRetenciones[$y]['retenxxx'] == 'ReteCree'){
									$nTotRCre += $nRetencion;
								}
								if($mRetenciones[$y]['retenxxx'] == 'Retefuente'){
									$nTotRfte += $nRetencion;
								}
								if($mRetenciones[$y]['retenxxx'] == 'ReteIva'){
									$nTotRIva += $nRetencion;
								}
								if($mRetenciones[$y]['retenxxx'] == 'ReteIca'){
									$nTotRIca += $nRetencion;
								}

								$cData .= $mRetenciones[$y]['retenxxx']."\t";
								$cData .= number_format($mRetenciones[$y]['comvlr01'],0,',','')."\t";
								$cData .= number_format($mRetenciones[$y]['pucretxx'],3,',','')."\t";
								$cData .= number_format($nRetencion,0,',','')."\t";
								$cData .= "\n";
							}
						}else{
							$cData .= "\t";
							$cData .= "\t";
							$cData .= "\t";
							$cData .= "\t";
							$cData .= "\n";
						}

						fwrite($fOp,$cData);

						$nTotCos += $mDatos[$i]['costoxxx'];
						$nTotIva += $mDatos[$i]['ivaxxxxx'];
						$nTotFac += $mDatos[$i]['totalxxx'];
					}

					for($nT=0;$nT<($nNumCol-7);$nT++){
						$cData .= "\t";
					}
					$cData  = 'TOTAL PAGOS A TERCEROS'."\t";
					$cData .= number_format($nTotCos,0,',','')."\t";
					$cData .= number_format($nTotIva,0,',','')."\t";
					$cData .= number_format($nTotFac,0,',','')."\t";
					$cData .= "\t";
					$cData .= "\n";
					$cData .= "\n";

					if($nTotRfte != 0){
						$cData .= utf8_decode('TOTAL RETENCIÓN EN LA FUENTE')."\t";
						$cData .= number_format($nTotRfte,0,',','')."\t";
						$cData .= "\n";
					}
					if($nTotRCre != 0){
						$cData .= utf8_decode('TOTAL RETENCIÓN CREE')."\t";
						$cData .= number_format($nTotRCre,0,',','')."\t";
						$cData .= "\n";
					}
					if($nTotRIva != 0){
						$cData .= utf8_decode('TOTAL RETENCIÓN IVA')."\t";
						$cData .= number_format($nTotRIva,0,',','')."\t";
						$cData .= "\n";
					}
					if($nTotRIca != 0){
						$cData .= utf8_decode('TOTAL RETENCIÓN ICA')."\t";
						$cData .= number_format($nTotRIca,0,',','')."\t";
						$cData .= "\n";
					}
					fwrite($fOp,$cData);
					fclose($fOp);
				} else {
					//Cuando selecciono Cliente
					$cData = '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px">';
						//Encabezado
						$cData .= '<tr bgcolor = "white" height="30" style="padding-left:5px;padding-top:5px">';
							$cData .= '<td class="name" colspan="'.$nNumCol.'" align="left">';
								$cData .= '<font size="3">';
									$cData .= '<b>CERTIFICADO MENSUAL DE RETENCIONES POR PAGOS A TERCEROS<br>';
									$cData .= 'PERIODO: DEL '.$dFecDes.' AL '.$dFecHas.'</b>';
								$cData .= '</font>';
							$cData .= '</td>';
						$cData .= '</tr>';
						if ($gTerId != "") {
							$cData .= '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
								$cData .= '<td class="name" colspan="'.($nNumCol - 4).'" align="left">';
									$cData .= '<font size="2">';
										$cData .= '<b>Cliente:  </b>'. $mDatos[0]['clinomxx'];
									$cData .= '</font>';
								$cData .= '</td>';
								$cData .= '<td class="name" colspan="2" align="left">';
									$cData .= '<font size="2">';
										$cData .= '<b>NIT:  </b>'. $gTerId."-".f_Digito_Verificacion($gTerId);
									$cData .= '</font>';
								$cData .= '</td>';
								$cData .= '<td class="name" colspan="2" align="left">';
									$cData .= '<font size="2">';
										$cData .= '<b>FECHA IMPRESION:  </b>'. date('Y-m-d');
									$cData .= '</font>';
								$cData .= '</td>';
							$cData .= '</tr>';
						} else {
							$cData .= '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
								$cData .= '<td class="name" colspan="'.$nNumCol.'" align="left">';
									$cData .= '<font size="2">';
										$cData .= '<b>FECHA IMPRESION:  </b>'. date('Y-m-d');
									$cData .= '</font>';
								$cData .= '</td>';
							$cData .= '</tr>';
						}
						//Columnas
						$cData .= '<tr height="20">';
							if ($gTerId == "") {
								$cData .= '<td style="background-color:#0B610B" width="80px" align="center"><b><font color=white>NIT</font></b></td>';
								$cData .= '<td style="background-color:#0B610B" width="200px" align="center"><b><font color=white>CLIENTE</font></b></td>';
							}
							$cData .= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>DO</font></b></td>';
							if(in_array($gTerId, $vNitCertificados) == true) {
								$cData .= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>PEDIDO</font></b></td>';
							}
							$cData .= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>FACTURA</font></b></td>';
							$cData .= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>TERCERO</font></b></td>';
							$cData .= '<td style="background-color:#0B610B" width="450px" align="center"><b><font color=white>NOMBRE</font></b></td>';
							$cData .= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>DOCUMENTO</font></b></td>';
							$cData .= '<td style="background-color:#0B610B" width="100px" align="center"><b><font color=white>FECHA</font></b></td>';
							if($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO"){
								$cData .= '<td style="background-color:#0B610B" width="100px" align="center"><b><font color=white>FECHA FACTURA</font></b></td>';
							}
							$cData .= '<td style="background-color:#0B610B" width="400px" align="center"><b><font color=white>CONCEPTO</font></b></td>';
							$cData .= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>COSTO</font></b></td>';
							$cData .= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>IVA</font></b></td>';
							$cData .= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TOTAL</font></b></td>';
							$cData .= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TIPO</font></b></td>';
							$cData .= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR BASE</font></b></td>';
							$cData .= '<td style="background-color:#0B610B" width="80px" align="center"><b><font color=white>%</font></b></td>';
							$cData .= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR</font></b></td>';
						$cData .= '</tr>';

						fwrite($fOp,$cData);

						for($i=0; $i<count($mDatos); $i++) {

							$n = 0;

							$mRetenciones = array();
							$mRetenciones = $mDatos[$i]['retencio'];

							if (count($mRetenciones) > 0) {
								$n += count($mRetenciones);
							}

							$cColorPro = "#000000";
							$cColor = "#FFFFFF";

							if (($cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "ROLDANLO") && $mDatos[$i]['docidxxx'] != "") {
								$mDatos[$i]['docidxxx'] = $mDatos[$i]['sucidxxx']."-".$mDatos[$i]['docidxxx']."-".$mDatos[$i]['docsufxx'];
							}

							$cData  = '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
								if ($gTerId == "") {
									$cData .= '<td align="left"   '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.$mDatos[$i]['cliidxxx'].'</td>';
									$cData .= '<td align="left"   '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.$mDatos[$i]['clinomxx'].'</td>';
								}
								$cData .= '<td align="left"   '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.$mDatos[$i]['docidxxx'].'</td>';
								if(in_array($gTerId, $vNitCertificados) == true) {
									$cData .= '<td align="left"   '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.$mDatos[$i]['docpedxx'].'</td>';
								}

								$vNitsCli = explode(",",$vSysStr['siacosia_incluir_prefijo_certificado_pcc']);
								if (($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") && in_array($mDatos[$i]['cliidxxx'], $vNitsCli)) {
									// Se obtiene el prefijo de la factura
									$vResPref = explode("~", $mDatos[$i]['residxxx']);
									$cData .= '<td align="center" '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.$vResPref[1] . "-" . $mDatos[$i]['facturax'].'</td>';
								} else {
									$cData .= '<td align="center" '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.$mDatos[$i]['facturax'].'</td>';
								}

								$cData .= '<td align="left"   '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.$mDatos[$i]['teridxxx'].'</td>';
								$cData .= '<td align="left"   '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.$mDatos[$i]['ternomxx'].'</td>';
								$cData .= '<td align="center" '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.$mDatos[$i]['document'].'</td>';
								$cData .= '<td align="center" '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.$mDatos[$i]['comfecxx'].'</td>';
								if($cAlfa == "DEGRUMALCO" || $cAlfa == "TEGRUMALCO" || $cAlfa == "GRUMALCO"){
									$cData .= '<td align="center" '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.((($mDatos[$i]['comfecfa']) != "") ? ($mDatos[$i]['comfecfa']) : "00/00/0000") .'</td>';
								}
								$cData .= '<td align="left"   '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.$mDatos[$i]['concepto'].'</td>';
								$cData .= '<td align="right"  '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.number_format($mDatos[$i]['costoxxx'],0,',','').'</td>';
								$cData .= '<td align="right"  '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.number_format($mDatos[$i]['ivaxxxxx'],0,',','').'</td>';
								$cData .= '<td align="right"  '.(($n > 0) ? "rowspan=\"$n\" " : "").'style = "color:'.$cColorPro.'">'.number_format($mDatos[$i]['totalxxx'],0,',','').'</td>';

								if (count($mRetenciones) > 0) {
									for ($y = 0; $y < count($mRetenciones); $y++){
										$cData .= '<td align="left"   style = "color:'.$cColorPro.'">'.$mRetenciones[$y]['retenxxx'].'</td>';
										$cData .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($mRetenciones[$y]['comvlr01'],0,',','').'</td>';
										$cData .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($mRetenciones[$y]['pucretxx'],3,',','').'</td>';

										$nRetencion = round($mRetenciones[$y]['comvlrxx']);
										if($mRetenciones[$y]['retenxxx'] == 'ReteCree'){
											$nTotRCre += $nRetencion;
										}
										if($mRetenciones[$y]['retenxxx'] == 'Retefuente'){
											$nTotRfte += $nRetencion;
										}
										if($mRetenciones[$y]['retenxxx'] == 'ReteIva'){
											$nTotRIva += $nRetencion;
										}
										if($mRetenciones[$y]['retenxxx'] == 'ReteIca'){
											$nTotRIca += $nRetencion;
										}
										$cData .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($nRetencion,0,',','').'</td>';

										$cData .= '</tr>';
								}
						}else{
							$cData .= '<td align="left"   style = "color:'.$cColorPro.'"></td>';
							$cData .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
							$cData .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
							$cData .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
							$cData .= '</tr>';
						}

						fwrite($fOp,$cData);

						$nTotCos += $mDatos[$i]['costoxxx'];
						$nTotIva += $mDatos[$i]['ivaxxxxx'];
						$nTotFac += $mDatos[$i]['totalxxx'];
					}

					$cData  = '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
						$cData .= '<td align="center" colspan="'.($nNumCol-7).'" style="background-color:#0B610B"><b><font color=white>TOTAL PAGOS A TERCEROS</td>';
						$cData .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotCos,0,',','').'</font></b></td>';
						$cData .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotIva,0,',','').'</font></b></td>';
						$cData .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotFac,0,',','').'</font></b></td>';
						$cData .= '<td align="right"  colspan="4" style="background-color:#0B610B"></td>';
					$cData .= '</tr>';

					$cData .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
						$cData .= '<td align="center" colspan="'.$nNumCol.'" style = "color:'.$cColorPro.'"><b><font color=white></td>';
					$cData .= '</tr>';

					if($nTotRfte != 0){
						$cData .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
							$cData .= '<td align="left" colspan="'.($nNumCol-1).'"  style = "color:'.$cColorPro.'"><b>'.utf8_decode('TOTAL RETENCIÓN EN LA FUENTE').'</b></td>';
							$cData .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRfte,0,',','').'</b></td>';
						$cData .= '</tr>';
					}
					if($nTotRCre != 0){
						$cData .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
						$cData .= '<td align="left" colspan="'.($nNumCol-1).'"  style = "color:'.$cColorPro.'"><b>'.utf8_decode('TOTAL RETENCIÓN CREE').'</b></td>';
						$cData .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRCre,0,',','').'</b></td>';
						$cData .= '</tr>';
					}
					if($nTotRIva != 0){
						$cData .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
							$cData .= '<td align="left" colspan="'.($nNumCol-1).'" style = "color:'.$cColorPro.'"><b>'.utf8_decode('TOTAL RETENCIÓN IVA').'</b></td>';
							$cData .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRIva,0,',','').'</b></td>';
						$cData .= '</tr>';
					}
					if($nTotRIca != 0){
						$cData .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
							$cData .= '<td align="left" colspan="'.($nNumCol-1).'" style = "color:'.$cColorPro.'"><b>'.utf8_decode('TOTAL RETENCIÓN ICA').'</b></td>';
							$cData .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRIca,0,',','').'</b></td>';
						$cData .= '</tr>';
					}
					$cData .= '</table>';

					fwrite($fOp,$cData);
					fclose($fOp);
				}

				if (file_exists($cFile)){
					if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "NO") {
						chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
						$cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFile);
						header('Content-Description: File Transfer');
						header('Content-Type: application/octet-stream');
						header('Content-Disposition: attachment; filename=' . $cDownLoadFilename);
						header('Content-Transfer-Encoding: binary');
						header('Expires: 0');
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						header('Pragma: public');
						header('Content-Length: ' . filesize($cFile));

						ob_clean();
						flush();
						readfile($cFile);
						
						exit;
					} else {
						$cNomArc = $cNomFile;
					}
				} else {
					if ($_SERVER["SERVER_PORT"] != "") {
            f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
          } else {
            $cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
          }
				}
			break;
		}
	}## if ($cEjePro == 0) {

	function fnCadenaAleatoria($pLength = 8) {
    $cCaracteres = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ";
    $nCaracteres = strlen($cCaracteres);
    $cResult = "";
    for ($x=0;$x< $pLength;$x++) {
      $nIndex = mt_rand(0,$nCaracteres - 1);
      $cResult .= $cCaracteres[$nIndex];
    }
    return $cResult;
	}
	
	if ($_SERVER["SERVER_PORT"] == "") {
		/**
		 * Se ejecuto por el proceso en background
		 * Actualizo el campo de resultado y nombre del archivo
		 */
		$vParBg['pbarespr'] = ($nSwitch == 0) ? "EXITOSO" : "FALLIDO";  //Resultado Proceso
		$vParBg['pbaexcxx'] = $cNomArc;                                 //Nombre Archivos Excel
		$vParBg['pbaerrxx'] = $cMsj;                                    //Errores al ejecutar el Proceso
		$vParBg['regdfinx'] = date('Y-m-d H:i:s');                      //Fecha y Hora Fin Ejecucion Proceso
		$vParBg['pbaidxxx'] = $vArg[0];                                 //id Proceso
	
		#Incluyendo la clase de procesos en background
		$ObjProBg = new cProcesosBackground();
		$mReturnProBg = $ObjProBg->fnFinalizarProcesoBackground($vParBg);
	
		#Imprimiendo resumen de todo ok.
		if ($mReturnProBg[0] == "false") {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
				$cMsj .= $mReturnProBg[$nR] . "\n";
			}
		}
	} // fin del if ($_SERVER["SERVER_PORT"] == "")
?>

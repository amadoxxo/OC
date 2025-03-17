<?php
  namespace openComex;
  use FPDF;
  
  // ini_set('display_errors', 1);
  // ini_set('display_startup_errors', 1);
  // error_reporting(E_ERROR);

	set_time_limit(0);
	ini_set("memory_limit","512M");

  date_default_timezone_set("America/Bogota");
  
  /**
   * Cantidad de Registros para reiniciar conexion
   */
  define("_NUMREG_",50);

	/**
	 * Variables de control de errores
	 * @var number
	 */
	$nSwitch = 0;

	/**
	 * Variable para almacenar los mensajes de error
	 * @var string
	 */
	$cMsj = "\n";

	/**
	 * Variables para reemplazar caracteres especiales
	 * @var array
	 */
	$cBuscar = array('"', "'", chr(13), chr(10), chr(27), chr(9));
	$cReempl = array('\"', "\'", " ", " ", " ", " ");

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

      /*** Nueva Columna anticipos a proveedor solo para SIACO. ***/
      $vBasSia = array('DESIACOSIP', 'TESIACOSIP', 'SIACOSIA');
      $bBasSia = in_array($cAlfa, $vBasSia);
      if ($bBasSia) {
        include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiliqdo.php");
      }
      
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
		include("../../../../../config/config.php");
		include("../../../../libs/php/utility.php");
		include("../../../../../libs/php/utiprobg.php");

		/*** Nueva Columna anticipos a proveedor solo para SIACO. ***/
		$vBasSia = array('DESIACOSIP', 'TESIACOSIP', 'SIACOSIA');
		$bBasSia = in_array($cAlfa, $vBasSia);
		if($bBasSia){
			include("../../../../libs/php/utiliqdo.php");
		}
	}

	/**
	 *  Cookie fija
	 */
	$kDf = explode("~", $_COOKIE["kDatosFijos"]);
	$kUser = $kDf[4];

	$cSystemPath = OC_DOCUMENTROOT;
	
	if ($_SERVER["SERVER_PORT"] != "") {
		/*** Ejecutar proceso en Background ***/
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;

		if($gCcoId != "") {
			$mAux    = explode("~",$gCcoId);
			$gCcoId  = $mAux[0];
			$gSucCco = $mAux[1];
			$gCcoNom = $mAux[2];
		}
	}
	
	if ($_SERVER["SERVER_PORT"] == "") {
		$gCcoId    = $_POST['gCcoId'];
		$gTerId    = $_POST['gTerId'];
		$gDirId    = $_POST['gDirId'];
		$gDesde    = $_POST['gDesde'];
		$gHasta    = $_POST['gHasta'];
		$gSucId    = $_POST['gSucId'];
		$gDocNro   = $_POST['gDocNro'];
		$gDocSuf   = $_POST['gDocSuf'];
		$gSucCco   = $_POST['gSucCco'];
		$gCcoNom   = $_POST['gCcoNom'];
		$cTipo     = $_POST['cTipo'];
    $gEstado   = $_POST['gEstado'];
    $gFecCorte = $_POST['gFecCorte'];
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

	if($gTerId != ""){
		#Busco el nombre del cliente
		$qCliNom  = "SELECT ";
		$qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
		$qCliNom .= "FROM $cAlfa.SIAI0150 ";
		$qCliNom .= "WHERE ";
		$qCliNom .= "CLIIDXXX = \"{$gTerId}\" LIMIT 0,1";
		$xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
		if (mysql_num_rows($xCliNom) > 0) {
			$xDDE = mysql_fetch_array($xCliNom);
		} else {
			$xDDE['clinomxx'] = "CLIENTE SIN NOMBRE";
		}
	}

	if($gDirId != ""){
		#Busco el nombre del director de cuenta
		$qNomDir  = "SELECT ";
		$qNomDir .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"DIRECTOR SIN NOMBRE\") AS USRNOMXX ";
		$qNomDir .= "FROM $cAlfa.SIAI0003 ";
		$qNomDir .= "WHERE ";
		$qNomDir .= "USRIDXXX = \"{$gDirId}\" LIMIT 0,1";
		$xNomDir = f_MySql("SELECT","",$qNomDir,$xConexion01,"");
		if (mysql_num_rows($xNomDir) > 0) {
			$xRU = mysql_fetch_array($xNomDir);
		} else {
			$xRU['USRNOMXX'] = "VENDEDOR SIN NOMBRE";
		}
	}

  $cTitulo = "";
	switch ($gEstado) {
		case "ACTIVO":
			$cTitulo .= "REPORTE DE TRAMITES ABIERTOS SIN FACTURAR ";
		break;
		case "FACTURADO":
			$cTitulo .= "REPORTE DE TRAMITES FACTURADOS ";
		break;
  }

	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;
	
    $strPost  = "gTerId~"    . $gTerId;
    $strPost .= "|gCcoId~"   . $gCcoId;
    $strPost .= "|gDirId~"   . $gDirId;
    $strPost .= "|gDesde~"   . $gDesde;
    $strPost .= "|gHasta~"   . $gHasta;
    $strPost .= "|gSucId~"   . $gSucId;
    $strPost .= "|gDocNro~"  . $gDocNro;
    $strPost .= "|gDocSuf~"  . $gDocSuf;
    $strPost .= "|gSucCco~"  . $gSucCco;
    $strPost .= "|gCcoNom~"  . $gCcoNom;
    $strPost .= "|cTipo~"    . $cTipo;
    $strPost .= "|gEstado~"  . $gEstado;
    $strPost .= "|gFecCorte~". $gFecCorte;
    
		$vParBg['pbadbxxx'] = $cAlfa;                           // Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                    // Modulo
		$vParBg['pbatinxx'] = "ESTADOCUENTATRAMITES";           // Tipo Interface
		$vParBg['pbatinde'] = "ESTADO DE CUENTA TRAMITES";      // Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = trim($gSucId);                    // Sucursal
		$vParBg['doiidxxx'] = trim($gDocNro);                   // Do
		$vParBg['doisfidx'] = trim($gDocSuf);                   // Sufijo
		$vParBg['cliidxxx'] = $gTerId;                          // Nit
		$vParBg['clinomxx'] = $xDDE['clinomxx'];                // Nombre Importador
		$vParBg['pbapostx'] = $strPost;													// Parametros para reconstruir Post
		$vParBg['pbatabxx'] = "";                               // Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];      // Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];          // cookie
		$vParBg['pbacrexx'] = 0;                                // Cantidad Registros
		$vParBg['pbatxixx'] = 1;                                // Tiempo Ejecucion x Item en Segundos
		$vParBg['pbaopcxx'] = "";                               // Opciones
		$vParBg['regusrxx'] = $kUser;                           // Usuario que Creo Registro
	
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
  } // fin del if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0)

	if ($cEjePro == 0) {
		if ($nSwitch == 0) {

			$nColspan = ($bBasSia ? 16 : 15);

			switch($cAlfa){
				case "GRUMALCO"://GRUMALCO
				case "TEGRUMALCO"://GRUMALCO
				case "DEGRUMALCO"://GRUMALCO
					$nColspan += 1;
				break;
				case "FENIXSAS"://FENIXSAS
				case "TEFENIXSAS"://FENIXSAS
				case "DEFENIXSAS"://FENIXSAS
					$nColspan += 2;
				break;
			}

			switch ($cTipo) {
				case 1:
					if ($_SERVER["SERVER_PORT"] != "") {
							
						// PINTA POR PANTALLA// ?>
						<html>
							<head>
								<title>Reporte de Estado de Cuenta Tramites</title>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
								<script type="text/javascript">
									function f_Imprimir(xComId, cComCod, xSucId, xDocTip, xDocId, xDocSuf, xPucId, xCcoId, xCliId, xRegFCre) { // Devuelvo al Formulario que Me Llama los Datos de la Aplicacion
										if(xSucId.length   > 0 ||
											xDocNro.length  > 0 ||
											xDocSuf.length  > 0 ||
											xRegFCre.length > 0 ){

											var cRuta  = "../movimido/frmdoprn.php?"+
																	"gComId="+xComId+
																	"&gComCod="+cComCod+
																	"&gSucId="+xSucId+
																	"&gDocTip="+xDocTip+
																	"&gDocId="+xDocId+
																	"&gDocSuf="+xDocSuf+
																	"&gPucId="+xPucId+
																	"&gCcoId="+xCcoId+
																	"&gCliId="+xCliId+
																	"&gRegFCre="+xRegFCre+
																	"&gMov=CONCEPTO"+
																	"&gPyG=1";

											var nX      = screen.width;
											var nY      = screen.height;
											var nNx     = 0;
											var nNy     = 0;
											var cWinOpt = "width="+nX+",scrollbars=1,resizable=YES,height="+nY+",left="+nNx+",top="+nNy;
											var cNomVen = 'zWinTrp'+Math.ceil(Math.random()*1000);
											cWindow = window.open(cRuta,cNomVen,cWinOpt);
											cWindow.focus();

										} else {
											alert("El Numero del DO esta Vacio, Verifique");
										}

									}
								</script>
							</head>
							<body>
							<form name = 'frgrm' action='frinpgrf.php' method="POST">
								<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">
									<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
										<td class="name" colspan="<?php echo $nColspan; ?>" align="left">
											<?php
											switch($cAlfa){

												case "ROLDANLO"://ROLDAN
												case "TEROLDANLO"://ROLDAN
												case "DEROLDANLO"://ROLDAN
													?>
													<center>
														<img width="150" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoroldan.png">
														<br/>
													</center>
											<?php
												break;
												case "ADUANAMO": //ADUANAMO
												case "DEADUANAMO": //ADUANAMO
												case "TEADUANAMO": //ADUANAMO
													?>
													<center>
														<img width="150" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logo_aduanamo.jpg">
														<br/>
													</center>
											<?php
												break;
												case "CASTANOX":
												case "TECASTANOX":
												case "DECASTANOX":
													?>
														<center>
																	<img width="150" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomartcam.jpg">
																	<br/>
														</center>
													<?php
												break;
												case "ALMACAFE": //ALMACAFE
												case "TEALMACAFE": //ALMACAFE
												case "DEALMACAFE": //ALMACAFE
													?>
														<center>
																	<img width="150" height="70" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoalmacafe.jpg">
																	<br/>
														</center>
													<?php
												break;
												case "TEADIMPEXX": // ADIMPEX
												case "DEADIMPEXX": // ADIMPEX
												case "ADIMPEXX": // ADIMPEX
													?>
														<center>
																	<img width="279" height="62" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoadimpex4.jpg">
																	<br/>
														</center>
													<?php
												break;
												case "GRUMALCO"://GRUMALCO
												case "TEGRUMALCO"://GRUMALCO
												case "DEGRUMALCO"://GRUMALCO
													?>
													<center>
														<img width="120" height="70" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomalco.jpg">
														<br/>
													</center>
													<?php
												break;
												case "ALADUANA": //ALADUANA
												case "TEALADUANA": //ALADUANA
												case "DEALADUANA": //ALADUANA
													?>
													<center>
														<img width="150" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaladuana.jpg">
														<br/>
													</center>
													<?php
												break;
												case "ANDINOSX": //ANDINOSX
												case "TEANDINOSX": //ANDINOSX
												case "DEANDINOSX": //ANDINOSX
													?>
													<center>
														<img width="55" height="60" style="left: 20px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoAndinos2.jpeg">
														<br/>
													</center>
													<?php
												break;
												case "GRUPOALC": //GRUPOALC
												case "TEGRUPOALC": //GRUPOALC
												case "DEGRUPOALC": //GRUPOALC
													?>
													<center>
														<img width="150" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoalc.jpg">
														<br/>
													</center>
													<?php
												break;
												case "AAINTERX": //AAINTERX
												case "TEAAINTERX": //AAINTERX
												case "DEAAINTERX": //AAINTERX
													?>
													<center>
														<img width="150" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointernacional.jpg">
														<br/>
													</center>
													<?php
												break;
												case "AALOPEZX":
												case "TEAALOPEZX":
												case "DEAALOPEZX":
													?>
													<center>
														<img width="130" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaalopez.png">
														<br/>
													</center>
													<?php
												break;
												case "ADUAMARX": //ADUAMARX
												case "TEADUAMARX": //ADUAMARX
												case "DEADUAMARX": //ADUAMARX
													?>
													<center>
														<img width="90" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaduamar.jpg">
														<br/>
													</center>
													<?php
												break;
												case "SOLUCION": //SOLUCION
												case "TESOLUCION": //SOLUCION
												case "DESOLUCION": //SOLUCION
													?>
													<center>
														<img width="150" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logosoluciones.jpg">
														<br/>
													</center>
													<?php
												break;
												case "FENIXSAS": //FENIXSAS
												case "TEFENIXSAS": //FENIXSAS
												case "DEFENIXSAS": //FENIXSAS
													?>
													<center>
														<img width="150" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logofenix.jpg">
														<br/>
													</center>
													<?php
												break;
												case "COLVANXX": //COLVANXX
												case "TECOLVANXX": //COLVANXX
												case "DECOLVANXX": //COLVANXX
													?>
													<center>
														<img width="150" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logocolvan.jpg">
														<br/>
													</center>
													<?php
												break;
												case "INTERLAC": //INTERLAC
												case "TEINTERLAC": //INTERLAC
												case "DEINTERLAC": //INTERLAC
													?>
													<center>
														<img width="150" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointerlace.jpg">
														<br/>
													</center>
													<?php
												break;
												case "DHLEXPRE": //DHLEXPRE
												case "TEDHLEXPRE": //DHLEXPRE
												case "DEDHLEXPRE": //DHLEXPRE
													?>
													<center>
														<img width="140" height="80" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logo_dhl_express.jpg">
														<br/>
													</center>
													<?php
												break;
												case "KARGORUX": //KARGORUX
												case "TEKARGORUX": //KARGORUX
												case "DEKARGORUX": //KARGORUX
													?>
													<center>
														<img width="140" height="80" style="left: 15px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logokargoru.jpg">
														<br />
													</center>
													<?php
												break;
												case "ALOGISAS": //LOGISTICA
												case "TEALOGISAS": //LOGISTICA
												case "DEALOGISAS": //LOGISTICA
													?>
													<center>
														<img width="210" style="left: 10px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logologisticasas.jpg">
														<br />
													</center>
													<?php
												break;
												case "PROSERCO":
												case "TEPROSERCO":
												case "DEPROSERCO":
													?>
													<center>
														<img width="200" style="left: 10px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoproserco.png">
														<br />
													</center>
													<?php
												break;
                        case "MANATIAL":
                        case "TEMANATIAL":
                        case "DEMANATIAL":
                          ?>
                          <center>
                            <img width="200" style="left: 10px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logomanantial.jpg">
                            <br />
                          </center>
                          <?php
                        break;
                        case "DSVSASXX":
                        case "DEDSVSASXX":
                        case "TEDSVSASXX":
                          ?>
                          <center>
                            <img width="200" style="left: 10px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logodsv.jpg">
                            <br />
                          </center>
                          <?php
                        break;
                        case "MELYAKXX":    //MELYAK
                        case "DEMELYAKXX":  //MELYAK
                        case "TEMELYAKXX":  //MELYAK
                          ?>
                          <center>
                            <img width="200" style="left: 10px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logomelyak.jpg">
                            <br />
                          </center>
                          <?php
                        break;
                        case "FEDEXEXP":    //FEDEX
                        case "DEFEDEXEXP":  //FEDEX
                        case "TEFEDEXEXP":  //FEDEX
                          ?>
                          <center>
                            <img width="200" style="left: 10px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logofedexexp.jpg">
                            <br />
                          </center>
                          <?php
                        break;
												case "EXPORCOM":    //EXPORCOMEX
												case "DEEXPORCOM":  //EXPORCOMEX
												case "TEEXPORCOM":  //EXPORCOMEX
													?>
													<center>
														<img width="180" style="left: 10px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoexporcomex.jpg">
														<br />
													</center>
													<?php
												break;
												case "HAYDEARX":   //EXPORCOMEX
												case "DEHAYDEARX": //EXPORCOMEX
												case "TEHAYDEARX": //EXPORCOMEX
													?>
													<center>
														<img width="200" style="left: 10px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logohaydear.jpeg">
														<br />
													</center>
													<?php
												break;
												case "CONNECTA":   //CONNECTA
												case "DECONNECTA": //CONNECTA
												case "TECONNECTA": //CONNECTA
													?>
													<center>
														<img width="120" style="left: 10px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoconnecta.jpg">
														<br />
													</center>
													<?php
												break;
                        case "CONLOGIC":   //CONLOGIC
                        case "DECONLOGIC": //CONLOGIC
                        case "TECONLOGIC": //CONLOGIC
                          ?>
                          <center>
                            <img width="120" style="left: 10px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoconlogic.jpg">
                            <br />
                          </center>
                          <?php
                        break;
												case "OPENEBCO":   //OPENEBCO
												case "DEOPENEBCO": //OPENEBCO
												case "TEOPENEBCO": //OPENEBCO
													?>
													<center>
														<img width="120" style="left: 10px;margin-top:5px;margin-bottom:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/opentecnologia.JPG">
														<br />
													</center>
													<?php
												break;
											}?>
											<center>
												<font size="3"><b>
													<?php echo $cTitulo ?><br>
													<?php echo "DESDE ".$gDesde." HASTA ".$gHasta.(($gFecCorte != "") ? " / FECHA DE CORTE ".$gFecCorte : "") ?><br>
													<?php if($gCcoId!=""){ ?>
														SURCURSAL: <?php echo "[".$gCcoId."] ".$gCcoNom ?><br>
													<?php }
													if($gTerId!=""){ ?>
														CLIENTE: <?php echo "[".$gTerId."] ".$xDDE['clinomxx'] ?><br>
													<?php }
													if($gDirId!=""){ ?>
														DIRECTOR: <?php echo "[".$gDirId."] ".$xRU['USRNOMXX'] ?><br>
													<?php } ?>
												</b></font>
											</center>
										</td>
									</tr>
									<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
										<td class="name" colspan="<?php echo $nColspan; ?>" align="left">
											<center>
												<font size="3">
													<b>TOTAL TRAMITES EN ESTA CONSULTA <input type="text" name="nCanReg" style="width:80px" readonly><br>
												</font>
											</center>
										</td>
									</tr>
									<tr height="20">
										<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Tramite</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Sucursal</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Pedido</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Fecha</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Operaci&oacute;n</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Mayor Levante</font></b></td>
										<?php 
											switch($cAlfa){ 
												case "GRUMALCO"://GRUMALCO
												case "TEGRUMALCO"://GRUMALCO
												case "DEGRUMALCO"://GRUMALCO
													?>
														<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>C.E</font></b></td>
													<?php
												break;
											}
										?>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Entrega Carpeta</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nit</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Cliente</font></b></td>
										<?php 
											switch($cAlfa){ 
												case "FENIXSAS"://FENIXSAS
												case "TEFENIXSAS"://FENIXSAS
												case "DEFENIXSAS"://FENIXSAS
													?>
														<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nit</font></b></td>
														<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Facturar A</font></b></td>
													<?php
												break;
											}
										?>
										<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Estado</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Cierre</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Director</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Anticipo</font></b></td>
										<?php if($bBasSia){ ?>
										<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Ant. PCC</font></b></td>
										<?php } ?>
										<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Pagos</font></b></td>
										<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Saldo</font></b></td>
									</tr>
						<?php 
					}
				break;
				case 2:
					// PINTA POR EXCEL //Reporte de Estado de Cuenta Tramites
					$header .= 'REPORTE DE ESTADO DE CUENTA TRAMITES'."\n";
					$header .= "\n";
					$data = '';
					$cNomFile = "REPORTE_ESTADO_DE_CUENTA_TRAMITES_" . $kUser . "_" . date("YmdHis") . ".xls";

					if ($_SERVER["SERVER_PORT"] != "") {
						$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;

						if (file_exists($cFile)) {
							unlink($cFile);
						}
					} else {

						/**
						 * Ruta archivo
						 * @var string
						 */
						$cRuta = "{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa/estado_cuenta";
						if (!is_dir("{$OPENINIT['pathdr']}/opencomex/propios")) {
							mkdir("{$OPENINIT['pathdr']}/opencomex/propios");
							chmod("{$OPENINIT['pathdr']}/opencomex/propios", intval($vSysStr['system_permisos_directorios'], 8));
						}

						if (!is_dir("{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa")) {
							mkdir("{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa");
							chmod("{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa", intval($vSysStr['system_permisos_directorios'], 8));
						}

						if (!is_dir($cRuta)) {
							mkdir($cRuta);
							chmod($cRuta, intval($vSysStr['system_permisos_directorios'], 8));
						}

						$cFile = $cRuta . "/" . $cNomFile;

						/*** Eliminar los archivos creados por el Usuario Logueado que corresponden a dias diferentes de HOY ***/
						$vArchivos = array_slice(scandir($cRuta),2);
						$cArcUsu = "REPORTE_ESTADO_DE_CUENTA_TRAMITES_" . $kUser;
						$cArcHoy = "REPORTE_ESTADO_DE_CUENTA_TRAMITES_" . $kUser . "_" . date("Ymd");
						// echo "Archivo de Hoy: ".$cArcHoy;
						for($nA = 0; $nA < count($vArchivos); $nA++){
							if(substr_count($vArchivos[$nA],$cArcUsu) > 0){
								if(substr_count($vArchivos[$nA],$cArcHoy) == 0){
									$cFileDel = $cRuta . "/" . $vArchivos[$nA];
									if (file_exists($cFileDel)) {
										unlink($cFileDel);
									}
								}
							}
						}
						/*** Fin Eliminar los archivos creados por el Usuario Logueado que corresponden a dias diferentes de HOY ***/
					}
	
					$fOp = fopen($cFile, 'a');

					$data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">';
						$data .= '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
							$data .= '<td class="name" colspan="'.$nColspan.'" align="left">';
								$data .= '<center>';
									$data .= '<font size="3">';
									$data .= '<b>'.$cTitulo.'<br>';
                  $data .= 'DESDE '.$gDesde.' HASTA '.$gHasta.(($gFecCorte != '') ? ' / FECHA DE CORTE '.$gFecCorte : '').'<br>';  
                  if ($gFecCorte != "") {
                    $data .= 'FECHA DE CORTE '.$gFecCorte.'<br>';
                  }
									if($gCcoId!=""){
										$data .= 'SURCURSAL: '."[".$gCcoId."] ".$gCcoNom.'<br>';
									}
									if($gTerId!=""){
										$data .= 'CLIENTE: '."[".$gTerId."] ".$xDDE['clinomxx'].'<br>';
									}
									if($gDirId!=""){
										$data .= 'DIRECTOR: '."[".$gDirId."] ".$xRU['USRNOMXX'].'<br>';
									}
									$data .= '</b>';
									$data .= '</font>';
								$data .= '</center>';
							$data .= '</td>';
						$data .= '</tr>';
						$data .= '<tr height="20">';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Tramite</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Sucursal</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Pedido</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Fecha</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Operaci&oacute;n</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Mayor Levante</font></b></td>';
							switch($cAlfa){ 
								case "GRUMALCO"://GRUMALCO
								case "TEGRUMALCO"://GRUMALCO
								case "DEGRUMALCO"://GRUMALCO
									$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>C.E</font></b></td>';
								break;
							}
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Entrega Carpeta</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nit</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Cliente</font></b></td>';
							switch($cAlfa){ 
								case "FENIXSAS"://FENIXSAS
								case "TEFENIXSAS"://FENIXSAS
								case "DEFENIXSAS"://FENIXSAS
									$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nit</font></b></td>';
									$data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Facturar A</font></b></td>';
								break;
							}
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Estado</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Cierre</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Director</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Anticipo</font></b></td>';
							if($bBasSia){
								$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Ant. PCC</font></b></td>';
							}
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Pagos</font></b></td>';
							$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Saldo</font></b></td>';
						$data .= '</tr>';

						fwrite($fOp, $data);
				break;
				case 3 :
					if ($_SERVER["SERVER_PORT"] != "") {
						
						// PINTA POR PDF
						$cRoot = $_SERVER['DOCUMENT_ROOT'];
						$gTerNom = $xDDE['clinomxx'];
						$gDirNom = $xRU['USRNOMXX'];
	
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
						switch($cAlfa){ 
							case "GRUMALCO"://GRUMALCO
							case "TEGRUMALCO"://GRUMALCO
							case "DEGRUMALCO"://GRUMALCO
								$nPosXAnt = ($bBasSia ? 5 : 10);
								$nLarXAnt = ($bBasSia ? 226 : 217);
							break;
							default:
								$nPosXAnt = ($bBasSia ? 5 : 13);
								$nLarXAnt = ($bBasSia ? 226 : 213);
							break;
						}	
	
						class PDF extends FPDF {
							function Header() {
								global $cRoot; global $cPlesk_Skin_Directory;
								global $cAlfa; global $cTitulo; global $gDesde; global $gHasta; global $gCcoId; global $gCcoNom;
								global $gTerId; global $gTerNom; global $gDirId; global $gDirNom; global $gCount; global $nPag;
								global $nPosXAnt; global $nLarXAnt; global $bBasSia; global $gFecCorte;
								switch ($cAlfa) {
									case "INTERLOG":
									case "TEINTERLOG":
									case "DEINTERLOG":
										$this->SetXY($nPosXAnt,7);
										$this->Cell(42,28,'',1,0,'C');
										$this->Cell($nLarXAnt,28,'',1,0,'C');
	
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/MaryAire.jpg',$nPosXAnt+1,8,40,25);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(55,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(55);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "ROLDANLO"://ROLDAN
									case "TEROLDANLO"://ROLDAN
									case "DEROLDANLO"://ROLDAN
										$this->SetXY($nPosXAnt,7);
										$this->Cell(42,28,'',1,0,'C');
										$this->Cell($nLarXAnt,28,'',1,0,'C');
	
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoroldan.png',$nPosXAnt+1,8,40,25);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(55,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(55);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "ADUANAMO": //ADUANAMO
									case "DEADUANAMO": //ADUANAMO
									case "TEADUANAMO": //ADUANAMO
										$this->SetXY($nPosXAnt,7);
										$this->Cell(42,28,'',1,0,'C');
										$this->Cell($nLarXAnt,28,'',1,0,'C');
	
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logo_aduanamo.jpg',$nPosXAnt+3,10,30,19);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(55,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(55);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
												$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "CASTANOX":
									case "DECASTANOX":
									case "TECASTANOX":
										$this->SetXY($nPosXAnt,7);
										$this->Cell(42,28,'',1,0,'C');
										$this->Cell($nLarXAnt,28,'',1,0,'C');
	
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logomartcam.jpg',$nPosXAnt+1,8,40,23);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(55,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(55);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
												$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "ALMACAFE": //ALMACAFE
									case "TEALMACAFE": //ALMACAFE
									case "DEALMACAFE": //ALMACAFE
										$this->SetXY($nPosXAnt,7);
										$this->Cell(42,28,'',1,0,'C');
										$this->Cell($nLarXAnt,28,'',1,0,'C');
	
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoalmacafe.jpg',$nPosXAnt+3,12,35,15);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(55,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(55);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
												$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "TEADIMPEXX": // ADIMPEX
									case "DEADIMPEXX": // ADIMPEX
									case "ADIMPEXX": // ADIMPEX
										// logo en la parte superior derecha
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoadimpex5.jpg',255,00,25,20);
										$this->SetXY($nPosXAnt,7);
										$this->Cell(42,28,'',1,0,'C');
										$this->Cell($nLarXAnt,28,'',1,0,'C');
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoadimpex4.jpg',$nPosXAnt+3,17,36,8);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(55,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(55);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "GRUMALCO"://GRUMALCO
									case "TEGRUMALCO"://GRUMALCO
									case "DEGRUMALCO"://GRUMALCO
										$this->SetXY($nPosXAnt,7);
										$this->Cell(42,28,'',1,0,'C');
										$this->Cell($nLarXAnt,28,'',1,0,'C');
	
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logomalco.jpg',$nPosXAnt+3,10,35,22);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(55,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(55);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "DHLEXPRE": //DHLEXPRE
									case "TEDHLEXPRE": //DHLEXPRE
									case "DEDHLEXPRE": //DHLEXPRE
										$this->SetXY($nPosXAnt,7);
										$this->Cell(42,28,'',1,0,'C');
										$this->Cell($nLarXAnt,28,'',1,0,'C');
	
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',$nPosXAnt+1,8,40,25);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(55,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(55);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "ALADUANA"://ALADUANA
									case "TEALADUANA"://ALADUANA
									case "DEALADUANA"://ALADUANA
										$this->SetXY(13,7);
										$this->Cell(42,28,'',1,0,'C');
										$this->Cell(213,28,'',1,0,'C');
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoaladuana.jpg',14,8,40,25);
										$this->SetXY(13,6);
										$this->Cell(255,28,'',1,0,'C');
										$this->SetFont('verdana','',12);
										$this->SetXY(13,7);
										$this->Cell(255,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(55);
										$this->SetFont('verdana','B',8);
										$this->Cell(213,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(13);
											$this->Cell(255,6,'SURCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(13);
											$this->Cell(255,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(13);
											$this->Cell(255,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}


										$this->Ln($n);
										$this->SetX(13);
									break;
									case "ANDINOSX"://ANDINOSX
									case "TEANDINOSX"://ANDINOSX
									case "DEANDINOSX"://ANDINOSX
										$this->SetXY(13, 7);
										$this->Cell(42, 28, '', 1, 0, 'C');
										$this->Cell(213, 28, '', 1, 0, 'C');
											// Dibujo //
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoAndinos2.jpeg', 24, 8, 20, 25);
										$this->SetXY(13, 6);
										$this->Cell(255, 28, '', 1, 0, 'C');
										$this->SetFont('verdana', '', 12);
										$this->SetXY(13, 7);
										$this->Cell(255, 8, $cTitulo, 0, 0, 'C');
										$this->Ln(6);
										$this->SetX(55);
										$this->SetFont('verdana', 'B', 8);
										$this->Cell(213, 8, "DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""), 0, 0, 'C');
										$this->Ln(5);
										$n = 20;
										if ($gCcoId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(13);
											$this->Cell(255, 6, 'SURCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}
										if ($gTerId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(13);
											$this->Cell(255, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}
										if ($gDirId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(13);
											$this->Cell(255, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}
										$this->Ln($n);
										$this->SetX(13);
									break;
									case "GRUPOALC"://GRUPOALC
									case "TEGRUPOALC"://GRUPOALC
									case "DEGRUPOALC"://GRUPOALC
										$this->SetXY(13, 7);
										$this->Cell(42, 28, '', 1, 0, 'C');
										$this->Cell(213, 28, '', 1, 0, 'C');
											// Dibujo //
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoalc.jpg',16,13,35,16);
										$this->SetXY(13, 6);
										$this->Cell(255, 28, '', 1, 0, 'C');
										$this->SetFont('verdana', '', 12);
										$this->SetXY(13, 7);
										$this->Cell(255, 8, $cTitulo, 0, 0, 'C');
										$this->Ln(6);
										$this->SetX(55);
										$this->SetFont('verdana', 'B', 8);
										$this->Cell(213, 8, "DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""), 0, 0, 'C');
										$this->Ln(5);
										$n = 20;
										if ($gCcoId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(13);
											$this->Cell(255, 6, 'SURCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}
										if ($gTerId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(13);
											$this->Cell(255, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}
										if ($gDirId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(13);
											$this->Cell(255, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}
										$this->Ln($n);
										$this->SetX(13);
									break;
									case "AAINTERX"://AAINTERX
									case "TEAAINTERX"://AAINTERX
									case "DEAAINTERX"://AAINTERX
										$this->SetXY(13,7);
										$this->Cell(42,28,'',1,0,'C');
										$this->Cell(213,28,'',1,0,'C');
										// Dibujo //
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logointernacional.jpg',14,8,40,25);
										$this->SetXY(13,6);
										$this->Cell(255,28,'',1,0,'C');
										$this->SetFont('verdana','',12);
										$this->SetXY(13,7);
										$this->Cell(255,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(55);
										$this->SetFont('verdana','B',8);
										$this->Cell(213,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(13);
											$this->Cell(255,6,'SURCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(13);
											$this->Cell(255,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(13);
											$this->Cell(255,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
	
	
										$this->Ln($n);
										$this->SetX(13);
									break;
									case "AALOPEZX":
									case "TEAALOPEZX":
									case "DEAALOPEZX":

										$nLarXAnt = ($bBasSia ? 268 : 255);
	
										$this->SetXY($nPosXAnt,6);
										$this->Cell($nLarXAnt,28,'',1,0,'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoaalopez.png', 16, 8, 30);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(13,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "ADUAMARX":
									case "TEADUAMARX":
									case "DEADUAMARX":

										$nLarXAnt = ($bBasSia ? 268 : 255);
	
										$this->SetXY($nPosXAnt,6);
										$this->Cell($nLarXAnt,28,'',1,0,'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoaduamar.jpg', 16, 9, 22);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(13,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(10);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "SOLUCION":
									case "TESOLUCION":
									case "DESOLUCION":

										$nLarXAnt = ($bBasSia ? 268 : 255);
	
										$this->SetXY($nPosXAnt,6);
										$this->Cell($nLarXAnt,28,'',1,0,'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logosoluciones.jpg', 16, 10, 45);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(13,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(10);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "FENIXSAS":
									case "TEFENIXSAS":
									case "DEFENIXSAS":

										$nLarXAnt = ($bBasSia ? 268 : 255);
	
										$this->SetXY($nPosXAnt,6);
										$this->Cell($nLarXAnt,28,'',1,0,'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logofenix.jpg', 16, 12, 50);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(13,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(10);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "COLVANXX":
									case "TECOLVANXX":
									case "DECOLVANXX":

										$nLarXAnt = ($bBasSia ? 268 : 255);
	
										$this->SetXY($nPosXAnt,6);
										$this->Cell($nLarXAnt,28,'',1,0,'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logocolvan.jpg', 16, 9, 50);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(13,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(10);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "INTERLAC":
									case "TEINTERLAC":
									case "DEINTERLAC":

										$nLarXAnt = ($bBasSia ? 268 : 255);
	
										$this->SetXY($nPosXAnt,6);
										$this->Cell($nLarXAnt,28,'',1,0,'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logointerlace.jpg', 16, 8, 50);
	
										$this->SetFont('verdana','',12);
										$this->SetXY(13,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetX(10);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(10);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "KARGORUX": //KARGORUX
									case "TEKARGORUX": //KARGORUX
									case "DEKARGORUX": //KARGORUX

										$nLarXAnt = ($bBasSia ? 268 : 255);

										$this->SetXY($nPosXAnt, 6);
										$this->Cell($nLarXAnt, 28, '', 1, 0, 'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logokargoru.jpg', 16, 9, 50);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(13, 7);
										$this->Cell($nLarXAnt, 8, $cTitulo, 0, 0, 'C');
										$this->Ln(6);
										$this->SetX(10);
										$this->SetFont('verdana', 'B', 8);
										$this->Cell($nLarXAnt, 8, "DESDE $gDesde HASTA $gHasta" . (($gFecCorte != '') ? " / FECHA DE CORTE " . $gFecCorte : ""), 0, 0, 'C');
										$this->Ln(5);
										$n = 20;
										if ($gCcoId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(10);
											$this->Cell($nLarXAnt, 6, 'SUCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}
										if ($gTerId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(10);
											$this->Cell($nLarXAnt, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}
										if ($gDirId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(10);
											$this->Cell($nLarXAnt, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}

										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "ALOGISAS": //LOGISTICA
									case "TEALOGISAS": //LOGISTICA
									case "DEALOGISAS": //LOGISTICA

										$nLarXAnt = ($bBasSia ? 268 : 255);

										$this->SetXY($nPosXAnt, 6);
										$this->Cell($nLarXAnt, 28, '', 1, 0, 'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 13, 9, 55);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(13, 7);
										$this->Cell($nLarXAnt, 8, $cTitulo, 0, 0, 'C');
										$this->Ln(6);
										$this->SetX(10);
										$this->SetFont('verdana', 'B', 8);
										$this->Cell($nLarXAnt, 8, "DESDE $gDesde HASTA $gHasta" . (($gFecCorte != '') ? " / FECHA DE CORTE " . $gFecCorte : ""), 0, 0, 'C');
										$this->Ln(5);
										$n = 20;
										if ($gCcoId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(10);
											$this->Cell($nLarXAnt, 6, 'SUCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}
										if ($gTerId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(10);
											$this->Cell($nLarXAnt, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}
										if ($gDirId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(10);
											$this->Cell($nLarXAnt, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}

										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "PROSERCO":
									case "TEPROSERCO":
									case "DEPROSERCO":
										$nLarXAnt = ($bBasSia ? 268 : 255);

										$this->SetXY($nPosXAnt, 6);
										$this->Cell($nLarXAnt, 28, '', 1, 0, 'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoproserco.png', 13, 7, 45);

										$this->SetFont('verdana', '', 12);
										$this->SetXY(13, 7);
										$this->Cell($nLarXAnt, 8, $cTitulo, 0, 0, 'C');
										$this->Ln(6);
										$this->SetX(10);
										$this->SetFont('verdana', 'B', 8);
										$this->Cell($nLarXAnt, 8, "DESDE $gDesde HASTA $gHasta" . (($gFecCorte != '') ? " / FECHA DE CORTE " . $gFecCorte : ""), 0, 0, 'C');
										$this->Ln(5);
										$n = 20;
										if ($gCcoId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(10);
											$this->Cell($nLarXAnt, 6, 'SUCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}
										if ($gTerId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(10);
											$this->Cell($nLarXAnt, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}
										if ($gDirId != "") {
											$this->SetFont('verdana', '', 8);
											$this->SetX(10);
											$this->Cell($nLarXAnt, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
											$this->Ln(5);
											$n -= 5;
										}

										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
                  case "MANATIAL":
                  case "TEMANATIAL":
                  case "DEMANATIAL":
                    $nLarXAnt = ($bBasSia ? 268 : 255);

                    $this->SetXY($nPosXAnt, 6);
                    $this->Cell($nLarXAnt, 28, '', 1, 0, 'C');
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logomanantial.jpg', 15, 7, 45);

                    $this->SetFont('verdana', '', 12);
                    $this->SetXY(13, 7);
                    $this->Cell($nLarXAnt, 8, $cTitulo, 0, 0, 'C');
                    $this->Ln(6);
                    $this->SetX(10);
                    $this->SetFont('verdana', 'B', 8);
                    $this->Cell($nLarXAnt, 8, "DESDE $gDesde HASTA $gHasta" . (($gFecCorte != '') ? " / FECHA DE CORTE " . $gFecCorte : ""), 0, 0, 'C');
                    $this->Ln(5);
                    $n = 20;
                    if ($gCcoId != "") {
                      $this->SetFont('verdana', '', 8);
                      $this->SetX(10);
                      $this->Cell($nLarXAnt, 6, 'SUCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
                      $this->Ln(5);
                      $n -= 5;
                    }
                    if ($gTerId != "") {
                      $this->SetFont('verdana', '', 8);
                      $this->SetX(10);
                      $this->Cell($nLarXAnt, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
                      $this->Ln(5);
                      $n -= 5;
                    }
                    if ($gDirId != "") {
                      $this->SetFont('verdana', '', 8);
                      $this->SetX(10);
                      $this->Cell($nLarXAnt, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
                      $this->Ln(5);
                      $n -= 5;
                    }

                    $this->Ln($n);
                    $this->SetX($nPosXAnt);
                  break;
                  case "DSVSASXX":
                  case "DEDSVSASXX":
                  case "TEDSVSASXX":
                    $nLarXAnt = ($bBasSia ? 268 : 255);

                    $this->SetXY($nPosXAnt, 6);
                    $this->Cell($nLarXAnt, 28, '', 1, 0, 'C');
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logodsv.jpg', 15, 9, 45);

                    $this->SetFont('verdana', '', 12);
                    $this->SetXY(13, 7);
                    $this->Cell($nLarXAnt, 8, $cTitulo, 0, 0, 'C');
                    $this->Ln(6);
                    $this->SetX(10);
                    $this->SetFont('verdana', 'B', 8);
                    $this->Cell($nLarXAnt, 8, "DESDE $gDesde HASTA $gHasta" . (($gFecCorte != '') ? " / FECHA DE CORTE " . $gFecCorte : ""), 0, 0, 'C');
                    $this->Ln(5);
                    $n = 20;
                    if ($gCcoId != "") {
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(10);
                    $this->Cell($nLarXAnt, 6, 'SUCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
                    $this->Ln(5);
                    $n -= 5;
                    }
                    if ($gTerId != "") {
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(10);
                    $this->Cell($nLarXAnt, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
                    $this->Ln(5);
                    $n -= 5;
                    }
                    if ($gDirId != "") {
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(10);
                    $this->Cell($nLarXAnt, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
                    $this->Ln(5);
                    $n -= 5;
                    }

                    $this->Ln($n);
                    $this->SetX($nPosXAnt);
                  break;
                  case "MELYAKXX":    //MELYAK
                  case "DEMELYAKXX":  //MELYAK
                  case "TEMELYAKXX":  //MELYAK
                    $nLarXAnt = ($bBasSia ? 268 : 255);
    
                    $this->SetXY($nPosXAnt, 6);
                    $this->Cell($nLarXAnt, 28, '', 1, 0, 'C');
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logomelyak.jpg', 15, 11.5, 45);
    
                    $this->SetFont('verdana', '', 12);
                    $this->SetXY(13, 7);
                    $this->Cell($nLarXAnt, 8, $cTitulo, 0, 0, 'C');
                    $this->Ln(6);
                    $this->SetX(10);
                    $this->SetFont('verdana', 'B', 8);
                    $this->Cell($nLarXAnt, 8, "DESDE $gDesde HASTA $gHasta" . (($gFecCorte != '') ? " / FECHA DE CORTE " . $gFecCorte : ""), 0, 0, 'C');
                    $this->Ln(5);
                    $n = 20;
                    if ($gCcoId != "") {
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(10);
                    $this->Cell($nLarXAnt, 6, 'SUCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
                    $this->Ln(5);
                    $n -= 5;
                    }
                    if ($gTerId != "") {
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(10);
                    $this->Cell($nLarXAnt, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
                    $this->Ln(5);
                    $n -= 5;
                    }
                    if ($gDirId != "") {
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(10);
                    $this->Cell($nLarXAnt, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
                    $this->Ln(5);
                    $n -= 5;
                    }

                    $this->Ln($n);
                    $this->SetX($nPosXAnt);
                  break;
                  case "FEDEXEXP":    //FEDEX
                  case "DEFEDEXEXP":  //FEDEX
                  case "TEFEDEXEXP":  //FEDEX
                    $nLarXAnt = ($bBasSia ? 268 : 255);
    
                    $this->SetXY($nPosXAnt, 6);
                    $this->Cell($nLarXAnt, 28, '', 1, 0, 'C');
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logofedexexp.jpg', 15, 11.5, 35);
    
                    $this->SetFont('verdana', '', 12);
                    $this->SetXY(13, 7);
                    $this->Cell($nLarXAnt, 8, $cTitulo, 0, 0, 'C');
                    $this->Ln(6);
                    $this->SetX(10);
                    $this->SetFont('verdana', 'B', 8);
                    $this->Cell($nLarXAnt, 8, "DESDE $gDesde HASTA $gHasta" . (($gFecCorte != '') ? " / FECHA DE CORTE " . $gFecCorte : ""), 0, 0, 'C');
                    $this->Ln(5);
                    $n = 20;
                    if ($gCcoId != "") {
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(10);
                    $this->Cell($nLarXAnt, 6, 'SUCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
                    $this->Ln(5);
                    $n -= 5;
                    }
                    if ($gTerId != "") {
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(10);
                    $this->Cell($nLarXAnt, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
                    $this->Ln(5);
                    $n -= 5;
                    }
                    if ($gDirId != "") {
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(10);
                    $this->Cell($nLarXAnt, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
                    $this->Ln(5);
                    $n -= 5;
                    }

                    $this->Ln($n);
                    $this->SetX($nPosXAnt);
                  break;
									case "EXPORCOM":    //EXPORCOMEX
									case "DEEXPORCOM":  //EXPORCOMEX
									case "TEEXPORCOM":  //EXPORCOMEX
										$nLarXAnt = ($bBasSia ? 268 : 255);
		
										$this->SetXY($nPosXAnt, 6);
										$this->Cell($nLarXAnt, 28, '', 1, 0, 'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoexporcomex.jpg', 15, 11.5, 35);
		
										$this->SetFont('verdana', '', 12);
										$this->SetXY(13, 7);
										$this->Cell($nLarXAnt, 8, $cTitulo, 0, 0, 'C');
										$this->Ln(6);
										$this->SetX(10);
										$this->SetFont('verdana', 'B', 8);
										$this->Cell($nLarXAnt, 8, "DESDE $gDesde HASTA $gHasta" . (($gFecCorte != '') ? " / FECHA DE CORTE " . $gFecCorte : ""), 0, 0, 'C');
										$this->Ln(5);
										$n = 20;
										if ($gCcoId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'SUCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}
										if ($gTerId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}
										if ($gDirId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}

										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "HAYDEARX":   //HAYDEARX
									case "DEHAYDEARX": //HAYDEARX
									case "TEHAYDEARX": //HAYDEARX
										$nLarXAnt = ($bBasSia ? 268 : 255);
		
										$this->SetXY($nPosXAnt, 6);
										$this->Cell($nLarXAnt, 28, '', 1, 0, 'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logohaydear.jpeg', 15, 6.5, 53, 20);
		
										$this->SetFont('verdana', '', 12);
										$this->SetXY(13, 7);
										$this->Cell($nLarXAnt, 8, $cTitulo, 0, 0, 'C');
										$this->Ln(6);
										$this->SetX(10);
										$this->SetFont('verdana', 'B', 8);
										$this->Cell($nLarXAnt, 8, "DESDE $gDesde HASTA $gHasta" . (($gFecCorte != '') ? " / FECHA DE CORTE " . $gFecCorte : ""), 0, 0, 'C');
										$this->Ln(5);
										$n = 20;
										if ($gCcoId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'SUCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}
										if ($gTerId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}
										if ($gDirId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}

										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "CONNECTA":   //CONNECTA
									case "DECONNECTA": //CONNECTA
									case "TECONNECTA": //CONNECTA
										$nLarXAnt = ($bBasSia ? 268 : 255);
		
										$this->SetXY($nPosXAnt, 6);
										$this->Cell($nLarXAnt, 28, '', 1, 0, 'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoconnecta.jpg', 15, 6.5, 35, 20);
		
										$this->SetFont('verdana', '', 12);
										$this->SetXY(13, 7);
										$this->Cell($nLarXAnt, 8, $cTitulo, 0, 0, 'C');
										$this->Ln(6);
										$this->SetX(10);
										$this->SetFont('verdana', 'B', 8);
										$this->Cell($nLarXAnt, 8, "DESDE $gDesde HASTA $gHasta" . (($gFecCorte != '') ? " / FECHA DE CORTE " . $gFecCorte : ""), 0, 0, 'C');
										$this->Ln(5);
										$n = 20;
										if ($gCcoId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'SUCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}
										if ($gTerId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}
										if ($gDirId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}
                    
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "CONLOGIC":   //CONLOGIC
									case "DECONLOGIC": //CONLOGIC
									case "TECONLOGIC": //CONLOGIC
										$nLarXAnt = ($bBasSia ? 268 : 255);
		
										$this->SetXY($nPosXAnt, 6);
										$this->Cell($nLarXAnt, 28, '', 1, 0, 'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logoconlogic.jpg', 15, 6.5, 30, 20);
		
										$this->SetFont('verdana', '', 12);
										$this->SetXY(13, 7);
										$this->Cell($nLarXAnt, 8, $cTitulo, 0, 0, 'C');
										$this->Ln(6);
										$this->SetX(10);
										$this->SetFont('verdana', 'B', 8);
										$this->Cell($nLarXAnt, 8, "DESDE $gDesde HASTA $gHasta" . (($gFecCorte != '') ? " / FECHA DE CORTE " . $gFecCorte : ""), 0, 0, 'C');
										$this->Ln(5);
										$n = 20;
										if ($gCcoId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'SUCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}
										if ($gTerId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}
										if ($gDirId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}

										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									case "OPENEBCO":   //OPENEBCO
									case "DEOPENEBCO": //OPENEBCO
									case "TEOPENEBCO": //OPENEBCO
										$nLarXAnt = ($bBasSia ? 268 : 255);
		
										$this->SetXY($nPosXAnt, 6);
										$this->Cell($nLarXAnt, 28, '', 1, 0, 'C');
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/opentecnologia.JPG', 15, 6.5, 40, 20);
		
										$this->SetFont('verdana', '', 12);
										$this->SetXY(13, 7);
										$this->Cell($nLarXAnt, 8, $cTitulo, 0, 0, 'C');
										$this->Ln(6);
										$this->SetX(10);
										$this->SetFont('verdana', 'B', 8);
										$this->Cell($nLarXAnt, 8, "DESDE $gDesde HASTA $gHasta" . (($gFecCorte != '') ? " / FECHA DE CORTE " . $gFecCorte : ""), 0, 0, 'C');
										$this->Ln(5);
										$n = 20;
										if ($gCcoId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'SUCURSAL: [' . $gCcoId . '] ' . $gCcoNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}
										if ($gTerId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'CLIENTE: [' . $gTerId . '] ' . $gTerNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}
										if ($gDirId != "") {
										$this->SetFont('verdana', '', 8);
										$this->SetX(10);
										$this->Cell($nLarXAnt, 6, 'DIRECTOR: [' . $gDirId . '] ' . $gDirNom, 0, 0, 'C');
										$this->Ln(5);
										$n -= 5;
										}

										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
									default:
										$nLarXAnt = ($bBasSia ? 268 : 255);
	
										$this->SetXY($nPosXAnt,6);
										$this->Cell($nLarXAnt,28,'',1,0,'C');
	
										$this->SetFont('verdana','',12);
										$this->SetXY(13,7);
										$this->Cell($nLarXAnt,8,$cTitulo,0,0,'C');
										$this->Ln(6);
										$this->SetFont('verdana','B',8);
										$this->Cell($nLarXAnt,8,"DESDE $gDesde HASTA $gHasta".(($gFecCorte != '') ? " / FECHA DE CORTE ".$gFecCorte : ""),0,0,'C');
										$this->Ln(5);
										$n = 20;
										if($gCcoId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'SUCURSAL: ['.$gCcoId.'] '.$gCcoNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gTerId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'CLIENTE: ['.$gTerId.'] '.$gTerNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
										if($gDirId!=""){
											$this->SetFont('verdana','',8);
											$this->SetX(55);
											$this->Cell($nLarXAnt,6,'DIRECTOR: ['.$gDirId.'] '.$gDirNom,0,0,'C');
											$this->Ln(5);
											$n -= 5;
										}
	
										$this->Ln($n);
										$this->SetX($nPosXAnt);
									break;
								}
	
								if($this->PageNo() > 1 && $nPag ==1){
									$this->SetFillColor(11,97,11);
									$this->SetTextColor(255);
									$this->SetFont('verdana','B',6);
									$this->SetX($nPosXAnt);
	
									$this->Cell(20,5,"Tramite",1,0,'C',1);
									$this->Cell(8,5,"Suc",1,0,'C',1);
									$this->Cell(20,5,"Pedido",1,0,'C',1);
									$this->Cell(15,5,"Fecha",1,0,'C',1);
									$this->Cell(18,5,"Operacion",1,0,'C',1);
									$this->Cell(15,5,"M. Levante",1,0,'C',1);
									switch($cAlfa){ 
										case "GRUMALCO"://GRUMALCO
										case "TEGRUMALCO"://GRUMALCO
										case "DEGRUMALCO"://GRUMALCO
											$this->Cell(6,5,"C.E",1,0,'C',1);
										break;
									}
									$this->Cell(15,5,"Ent. Carpeta",1,0,'C',1);
									$this->Cell(15,5,"Nit",1,0,'C',1);
									$this->Cell(30,5,"Cliente",1,0,'C',1);
									$this->Cell(12,5,"Estado",1,0,'C',1);
									$this->Cell(15,5,"Cierre",1,0,'C',1);
									$this->Cell(25,5,"Director",1,0,'C',1);
									$this->Cell(15,5,"Anticipo",1,0,'C',1);
									if($bBasSia){
										$this->Cell(15,5,"Ant. PCC",1,0,'C',1);
									}
									$this->Cell(15,5,"Pagos",1,0,'C',1);
									$this->Cell(15,5,"Saldo",1,0,'C',1);
	
									$this->SetFillColor(255);
									$this->SetTextColor(0);
	
									$this->Ln(5);
									$this->SetX($nPosXAnt);
									$this->SetFont('verdana','',6);
	
									if($bBasSia){
										$this->SetWidths(array('20','8','20','15','18','15','15','15','30','12','15','25','15','15','15','15'));
										$this->SetAligns(array('L','C','L','C','C','C','C','L','L','C','C','L','R','R','R','R'));
	
									} else {
										switch($cAlfa){
											case "GRUMALCO"://GRUMALCO
											case "TEGRUMALCO"://GRUMALCO
											case "DEGRUMALCO"://GRUMALCO
												$this->SetWidths(array('20','8','20','15','18','15','6','15','15','30','12','15','25','15','15','15'));
												$this->SetAligns(array('L','C','L','C','C','C','C','C','L','L','C','C','L','R','R','R'));
											break;
											default:
												$this->SetWidths(array('20','8','20','15','18','15','15','15','30','12','15','25','15','15','15'));
												$this->SetAligns(array('L','C','L','C','C','C','C','L','L','C','C','L','R','R','R'));
											break;
										}
									}
								}
							}
							function Footer() {
								$this->SetY(-10);
								$this->SetFont('verdana','',6);
								$this->Cell(0,5,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
							}
	
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
									$this->Rect($x,$y,$w,$h);
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
							}
	
						$pdf = new PDF('L','mm','Letter');
						$pdf->AddFont('verdana','','');
						$pdf->AddFont('verdana','B','');
						$pdf->AliasNbPages();
						$pdf->SetMargins(0,0,0);
	
						$pdf->AddPage();
						$pdf->SetFillColor(11,97,11);
						$pdf->SetTextColor(255);
						$pdf->SetX($nPosXAnt);
	
						$pdf->SetFont('verdana','B',6);
	
						$pdf->Cell(20,5,"Tramite",1,0,'C',1);
						$pdf->Cell(8,5,"Suc",1,0,'C',1);
						$pdf->Cell(20,5,"Pedido",1,0,'C',1);
						$pdf->Cell(15,5,"Fecha",1,0,'C',1);
						$pdf->Cell(18,5,"Operacion",1,0,'C',1);
						$pdf->Cell(15,5,"M. Levante",1,0,'C',1);
						switch($cAlfa){ 
							case "GRUMALCO"://GRUMALCO
							case "TEGRUMALCO"://GRUMALCO
							case "DEGRUMALCO"://GRUMALCO
								$pdf->Cell(6,5,"C.E",1,0,'C',1);
							break;
						}
						$pdf->Cell(15,5,"Ent. Carpeta",1,0,'C',1);
						$pdf->Cell(15,5,"Nit",1,0,'C',1);
						$pdf->Cell(30,5,"Cliente",1,0,'C',1);
						$pdf->Cell(12,5,"Estado",1,0,'C',1);
						$pdf->Cell(15,5,"Cierre",1,0,'C',1);
						$pdf->Cell(25,5,"Director",1,0,'C',1);
						$pdf->Cell(15,5,"Anticipo",1,0,'C',1);
						if($bBasSia){
							$pdf->Cell(15,5,"Ant. PCC",1,0,'C',1);
						}
						$pdf->Cell(15,5,"Pagos",1,0,'C',1);
						$pdf->Cell(15,5,"Saldo",1,0,'C',1);
	
						$pdf->SetFillColor(255);
						$pdf->SetTextColor(0);
	
						$pdf->Ln(5);
						$pdf->SetX($nPosXAnt);
						$pdf->SetFont('verdana','',6);
	
						if($bBasSia){
							$pdf->SetWidths(array('20','8','20','15','18','15','15','15','30','12','15','25','15','15','15','15'));
							$pdf->SetAligns(array('L','C','L','C','C','C','C','L','L','C','C','L','R','R','R','R'));
	
						} else {
							switch($cAlfa){ 
								case "GRUMALCO"://GRUMALCO
								case "TEGRUMALCO"://GRUMALCO
								case "DEGRUMALCO"://GRUMALCO
									$pdf->SetWidths(array('20','8','20','15','18','15','6','15','15','30','12','15','25','15','15','15'));
									$pdf->SetAligns(array('L','C','L','C','C','C','C','C','L','L','C','C','L','R','R','R'));
								break;
								default:
									$pdf->SetWidths(array('20','8','20','15','18','15','15','15','30','12','15','25','15','15','15'));
									$pdf->SetAligns(array('L','C','L','C','C','C','C','L','L','C','C','L','R','R','R'));
								break;
							}
						}
	
						$nPag = 0;
					}
				break;
			}

			#Trayendo comprobantes
			$mTabFac = array(); //Array con nombre de las tablas temporales para las cabecera de facturas
			$mTabDet = array(); //Array con nombre de las tablas temporales para los ajustes, anticipos y pagos a terceros en la tabla de detalle
			$mTabRec = array(); //Array con nombre de las tablas temporales para los recibos de caja en la tabla de detalle

			$mCtoAnt = array(); //Array con la marca de ancipos por cuenta-concepto
			$mCtoPCC = array(); //Array con la marca de pcc por cuenta-concepto

			//Buscano conceptos de causaciones automaticas pcc
			$qCAyP121 = "SELECT DISTINCT $cAlfa.fpar0121.pucidxxx, $cAlfa.fpar0121.ctoidxxx FROM $cAlfa.fpar0121 WHERE $cAlfa.fpar0121.regestxx = \"ACTIVO\"";
			$xCAyP121 = f_MySql("SELECT","",$qCAyP121,$xConexion01,"");
			// echo "\n".$qCAyP121."~".mysql_num_rows($xCAyP121);

			$cCAyP121 = "";
			while($xRCP121 = mysql_fetch_array($xCAyP121)) {
				$cCAyP121 .= "\"{$xRCP121['pucidxxx']}~{$xRCP121['ctoidxxx']}\",";
				$mCtoPCC[count($mCtoPCC)] = "{$xRCP121['pucidxxx']}~{$xRCP121['ctoidxxx']}";

				if($bBasSia){
					if(in_array("{$xRCP121['pucidxxx']}", $vPucId) == false) {
						$cPucId .= "\"{$xRCP121['pucidxxx']}\",";
						$vPucId[count($vPucId)] = "{$xRCP121['pucidxxx']}";
					}
				}
			}

			//Buscando conceptos pcc y anticipos
			$qCtoAntyPCC = "SELECT DISTINCT $cAlfa.fpar0119.ctoantxx,$cAlfa.fpar0119.ctopccxx,$cAlfa.fpar0119.pucidxxx, $cAlfa.fpar0119.ctoidxxx FROM $cAlfa.fpar0119 WHERE ($cAlfa.fpar0119.ctoantxx = \"SI\" OR $cAlfa.fpar0119.ctopccxx = \"SI\") AND $cAlfa.fpar0119.regestxx = \"ACTIVO\"";
			$xCtoAntyPCC = f_MySql("SELECT","",$qCtoAntyPCC,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qCtoAntyPCC."~".mysql_num_rows($xCtoAntyPCC));
			$cCtoAntyPCC = "";
			while($xRCAP = mysql_fetch_array($xCtoAntyPCC)) {
				$cCtoAntyPCC .= "\"{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}\",";
				if ($xRCAP['ctoantxx'] == "SI") {
					$mCtoAnt[count($mCtoAnt)] = "{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}";
				}
				if ($xRCAP['ctopccxx'] == "SI") {
					$mCtoPCC[count($mCtoPCC)] = "{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}";
				}
			}
      $cCtoAntyPCC = $cCAyP121.substr($cCtoAntyPCC,0,strlen($cCtoAntyPCC)-1);
      
      // echo $cCtoAntyPCC."<br><br><br>";

			/*** Creando objeto del movimiento del DO.***/
			if($bBasSia){
				/**
				* Buscando los centros de costo que aplican para cada resolucion de facturacion
				*/
				$qResFac  = "SELECT residxxx,restipxx,rescomxx ";
				$qResFac .= "FROM $cAlfa.fpar0138 ";
				$xResTip  = f_MySql("SELECT","",$qResFac,$xConexion01,"");
				//f_Mensaje(__FILE__,__LINE__,$qResFac." ~ ".mysql_num_rows($xResTip));
				$mCcoRes = array();
				$mSucRes = array();
				while ($xRRF = mysql_fetch_array($xResTip)) {
					$cCcoId = "";	$vCcoId = array();
					$cSucId = "";	$vSucId = array();
					$vComprobantes = f_Explode_Array($xRRF['rescomxx'],"|","~");
					for ($i=0;$i<count($vComprobantes);$i++) {
						if($vComprobantes[$i][0] <> "" && $vComprobantes[$i][1] <> "") {
							// Por cada comprobante del campo vector busco los datos de las sucursal.
							$qCiudades  = "SELECT ";
							$qCiudades .= "fpar0117.comidxxx,";
							$qCiudades .= "fpar0117.comcodxx,";
							$qCiudades .= "fpar0008.sucidxxx,";
							$qCiudades .= "fpar0117.ccoidxxx,";
							$qCiudades .= "fpar0117.comdesxx ";
							$qCiudades .= "FROM $cAlfa.fpar0117,$cAlfa.fpar0008 ";
							$qCiudades .= "WHERE ";
							$qCiudades .= "fpar0117.sucidxxx = fpar0008.sucidxxx AND ";
							$qCiudades .= "fpar0117.ccoidxxx = fpar0008.ccoidxxx AND ";
							$qCiudades .= "fpar0117.comidxxx = \"{$vComprobantes[$i][0]}\" AND ";
							$qCiudades .= "fpar0117.comcodxx = \"{$vComprobantes[$i][1]}\" AND ";
							$qCiudades .= "fpar0117.regestxx = \"ACTIVO\" LIMIT 0,1";
							$xCiudades  = f_MySql("SELECT","",$qCiudades,$xConexion01,"");
							//f_Mensaje(__FILE__,__LINE__,$qCiudades." ~ ".mysql_num_rows($xCiudades));
							if (mysql_num_rows($xCiudades) > 0) {
								$vCiudades  = mysql_fetch_array($xCiudades);

								if(!isset($mCcoRes["{$xRRF['residxxx']}"])){
									$mCcoRes["{$xRRF['residxxx']}"][] = "\"{$vCiudades['ccoidxxx']}\"";
								} else {
									if (in_array("\"{$vCiudades['ccoidxxx']}\"",$mCcoRes["{$xRRF['residxxx']}"]) == false) {
										$mCcoRes["{$xRRF['residxxx']}"][] = "\"{$vCiudades['ccoidxxx']}\"";
									}
								}

								if(!isset($mSucRes["{$xRRF['residxxx']}"])){
									$mSucRes["{$xRRF['residxxx']}"][] = "\"{$vCiudades['sucidxxx']}\"";
								} else {
									if (in_array("\"{$vCiudades['sucidxxx']}\"",$mSucRes["{$xRRF['residxxx']}"]) == false) {
										$mSucRes["{$xRRF['residxxx']}"][] = "\"{$vCiudades['sucidxxx']}\"";
									}
								}
							}
						}
					}
				}

				//Cuentas Puc
				$cPucId = "";
				$vPucId = array();

				$cCtoAntyPCCDo = "";
				//Buscando conceptos para procesar Anticipo PCC
				$qCtoAntyPCCDo  = "SELECT DISTINCT ";
				$qCtoAntyPCCDo .= "$cAlfa.fpar0119.pucidxxx, ";
				$qCtoAntyPCCDo .= "$cAlfa.fpar0119.ctoidxxx ";
				$qCtoAntyPCCDo .= "FROM $cAlfa.fpar0119 ";
				$qCtoAntyPCCDo .= "WHERE ";
				$qCtoAntyPCCDo .= "($cAlfa.fpar0119.ctoantxx = \"SI\" OR $cAlfa.fpar0119.ctopccxx = \"SI\" OR $cAlfa.fpar0119.ctodocxg = \"SI\" OR $cAlfa.fpar0119.ctodocxl = \"SI\") AND ";
				$qCtoAntyPCCDo .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\"";
				$xCtoAntyPCCDo = f_MySql("SELECT","",$qCtoAntyPCCDo,$xConexion01,"");

				while($xRCAPDo = mysql_fetch_array($xCtoAntyPCCDo)) {
					$cCtoAntyPCCDo .= "\"{$xRCAPDo['pucidxxx']}~{$xRCAPDo['ctoidxxx']}\",";
					if(in_array("{$xRCAPDo['pucidxxx']}", $vPucId) == false) {
						$cPucId .= "\"{$xRCAPDo['pucidxxx']}\",";
						$vPucId[count($vPucId)] = "{$xRCAPDo['pucidxxx']}";
					}
				}
				$cCtoAntyPCCDo = $cCAyP121.substr($cCtoAntyPCCDo,0,strlen($cCtoAntyPCCDo)-1);
				$cPucId = substr($cPucId,0,strlen($cPucId)-1);

				$qDoInf = "SELECT $cAlfa.fpar0119.pucidxxx, $cAlfa.fpar0119.ctoidxxx FROM $cAlfa.fpar0119 WHERE ($cAlfa.fpar0119.ctodocxg = \"SI\" OR $cAlfa.fpar0119.ctodocxl = \"SI\") AND $cAlfa.fpar0119.regestxx = \"ACTIVO\"";
				$xDoInf = f_MySql("SELECT","",$qDoInf,$xConexion01,"");
				$cDoInf = "";
				while($xRDI = mysql_fetch_array($xDoInf)) {
					$cDoInf .= "\"{$xRDI['pucidxxx']}~{$xRDI['ctoidxxx']}\",";
				}
				$cDoInf = $cCAyP121.substr($cDoInf,0,strlen($cDoInf)-1);
			}

      // f_Mensaje(__FILE__,__LINE__,$qCtoAntyPCC."~".mysql_num_rows($xCtoAntyPCC));

      #Rango de los Años en donde debo buscar los datos
			$nAnoI = ((substr($gDesde,0,4)-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (substr($gDesde,0,4)-1);
			$nAnoF = date('Y');
      
      if ($gFecCorte != "") {
				$nAnoI = $vSysStr['financiero_ano_instalacion_modulo'];
				$nAnoF = substr($gFecCorte,0,4);
      }

			$objEstructuras = new cEstructurasEstadoCuentaTramite();
			
			for($nPerAno=$nAnoI;$nPerAno<=$nAnoF;$nPerAno++) {
				#Creando Tabla temporal de Facturas
				$cFcoc      = "fcoc".$nPerAno;
				$mReturnTab = $objEstructuras->fnCrearEstructurasEstadoCuentaTramite($cFcoc);
				$cTabFac    = $mReturnTab[1];

				$qCabFac  = "SELECT * ";
				$qCabFac .= "FROM $cAlfa.fcoc$nPerAno ";
				$qCabFac .= "WHERE ";
				$qCabFac .= "comidxxx = \"F\" AND ";
				if ($gFecCorte != "") {
					$qCabFac .= "comfecxx <= \"$gFecCorte\" AND ";
				}
				$qCabFac .= "regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";

				$qInsert = "INSERT INTO $cAlfa.$cTabFac $qCabFac";
				// echo "<br>".$qInsert."<br>";
				$xInsert = mysql_query($qInsert,$xConexion01);

				//echo "Ano ".$nPerAno."Tabla Facturas ".$cTabFac."\n\n";
				$mTabFac[$nPerAno] = $cTabFac;
				#Fin Creando Tabla temporal de Facturas


        #Creando Tabla temporal de PCC, anticipos
        $cFcod      = "fcod".$nPerAno;
        $mReturnTab = $objEstructuras->fnCrearEstructurasEstadoCuentaTramite($cFcod);
        $cTabFac    = $mReturnTab[1];

				$qMovDO  = "SELECT  * ";
				$qMovDO .= "FROM $cAlfa.fcod$nPerAno ";
				$qMovDO .= "WHERE ";
				if($gDocNro != "") {
					$qMovDO .= "comcsccx =  \"$gDocNro\" AND ";
					$qMovDO .= "comseqcx =  \"$gDocSuf\" AND ";
				}
				$qMovDO .= "CONCAT(pucidxxx,\"~\",ctoidxxx) IN ($cCtoAntyPCC) AND "; //PCC Y ANTICPOS
				$qMovDO .= "comidxxx != \"F\"  AND ";
				if ($gFecCorte == ""){
					$qMovDO .= "(comfacxx =  \"\" OR (comfacxx != \"\" AND comfacxx LIKE \"%-P%\")) AND ";
				}
				$qMovDO .= "regestxx = \"ACTIVO\" ";
        // echo "<br>".$qMovDO."<br><br>";
        
        $qInsert = "INSERT INTO $cAlfa.$cTabFac $qMovDO";
				// echo "<br>".$qInsert."<br>";
				$xInsert = mysql_query($qInsert,$xConexion01);
				$mTabDet[$nPerAno] = $cTabFac;
				#Fin Creando Tabla temporal de PCC, anticipos y ajustes

				#Creando tabla temporal de recibos de caja
        $cFcod      = "fcme".$nPerAno;
        $mReturnTab = $objEstructuras->fnCrearEstructurasEstadoCuentaTramite($cFcod);
        $cTabFac    = $mReturnTab[1];

				$qRecCaj  = "SELECT * ";
				$qRecCaj .= "FROM $cAlfa.fcme$nPerAno ";
				$qRecCaj .= "WHERE ";
				if($gDocNro != "") {
					$qRecCaj .= "comcsccx = \"$gDocNro\" AND ";
					$qRecCaj .= "comseqcx = \"$gDocSuf\" AND ";
        }
        $qRecCaj .= "comidc2x = \"\" AND "; //No tenga reembolso
				$qRecCaj .= "CONCAT(pucidxxx,\"~\",ctoidxxx) IN ($cCtoAntyPCC) AND "; //PCC Y ANTICPOS
				if ($gFecCorte == ""){
					$qRecCaj .= "(comfacxx =  \"\" OR (comfacxx != \"\" AND comfacxx LIKE \"%-P%\")) AND ";
				}
				$qRecCaj .= "regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
        // echo $qRecCaj."<br><br>";

				$qInsert = "INSERT INTO $cAlfa.$cTabFac $qMovDO";
				$xInsert = mysql_query($qInsert,$xConexion01);
				$mTabRec[$nPerAno] = $cTabFac;
				#Fin Creando tabla temporal de recibos de caja
			}
      #Fin Trayendo comprobantes
    
			$qDatDoi  = "SELECT ";
			$qDatDoi .= "$cAlfa.sys00121.sucidxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.docidxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.docsufxx, ";
			$qDatDoi .= "$cAlfa.sys00121.comidxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.comcodxx, ";
			$qDatDoi .= "$cAlfa.sys00121.ccoidxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.pucidxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.succomxx, ";
			$qDatDoi .= "$cAlfa.sys00121.doctipxx, ";
			$qDatDoi .= "$cAlfa.sys00121.docpedxx, ";
			$qDatDoi .= "$cAlfa.sys00121.cliidxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.diridxxx, ";
			$qDatDoi .= "$cAlfa.sys00121.docfacxx, ";
			$qDatDoi .= "$cAlfa.sys00121.docffecx, ";
			$qDatDoi .= "$cAlfa.sys00121.regfcrex, ";
			$qDatDoi .= "$cAlfa.sys00121.docusrce, ";
			$qDatDoi .= "$cAlfa.sys00121.docfecce, ";
			$qDatDoi .= "$cAlfa.sys00121.regestxx ";
			$qDatDoi .= "FROM $cAlfa.sys00121 ";
      $qDatDoi .= "WHERE ";
      $nExacto = 0;
			if($gDocNro!=""){
        $nExacto = 1;
        $qDatDoi .= "$cAlfa.sys00121.sucidxxx = \"$gSucId\" AND ";
        $qDatDoi .= "$cAlfa.sys00121.docidxxx = \"$gDocNro\" AND ";
        $qDatDoi .= "$cAlfa.sys00121.docsufxx = \"$gDocSuf\" AND ";
			}
			if($gCcoId!=""){
        $qDatDoi .= "$cAlfa.sys00121.sucidxxx = \"$gSucCco\" AND ";
			}
			if($gTerId!=""){
				$qDatDoi .= "$cAlfa.sys00121.cliidxxx = \"$gTerId\" AND ";
			}
			if($gDirId!=""){
				$qDatDoi .= "$cAlfa.sys00121.diridxxx = \"$gDirId\" AND ";
      }
      if ($nExacto == 0) {
        if ($gFecCorte != "") {
          $qDatDoi .= "$cAlfa.sys00121.regfcrex <= \"$gFecCorte\" AND ";
        } else {
          $qDatDoi .= "$cAlfa.sys00121.regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
        }
      }
      switch ($cAlfa) {
        case "TEROLDANLO":
        case "DEROLDANLO":
        case "ROLDANLO":
          /*	Cuando el filtro sea ACTIVO, también se traen las facturadas para ADEMÁS pintar SOLO las provisionales de dichas facturadas.
          *	Cuando el filtro sea FACTURADO, las provisionales NO deben pintarse.
          */
          if ($gEstado == "ACTIVO") {
            $qDatDoi .= "($cAlfa.sys00121.regestxx = \"ACTIVO\" OR ($cAlfa.sys00121.regestxx = \"FACTURADO\" AND $cAlfa.sys00121.docfacxx LIKE \"%-P%\")) ";
          } else {
            $qDatDoi .= "$cAlfa.sys00121.regestxx = \"$gEstado\" ";
          }
        break;
        default:
          if ($gFecCorte == "") {
            $qDatDoi .= "$cAlfa.sys00121.regestxx = \"$gEstado\" ";
          } else {
            $qDatDoi = substr($qDatDoi, 0, -4);
          }
        break;
      }
			$qDatDoi .= "ORDER BY $cAlfa.sys00121.regfcrex";
			$xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qDatDoi."~".mysql_num_rows($xDatDoi));
      // echo $qDatDoi."~".mysql_num_rows($xDatDoi)."<br><br>";
      
			#Recorro Do's
			$nCReg = 0;
			$nCanReg = 0; //Contador de registros
			
			while ($xDD = mysql_fetch_array($xDatDoi)) {
				$nCanReg++;
        if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objEstructuras->fnReiniciarConexionDBEstadoCuentaTramite($xConexion01); } 

				#Busco el nombre del cliente
				$qCliNom  = "SELECT ";
				$qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,' ',CLINOM2X,' ',CLIAPE1X,' ',CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,' ',CLINOM2X,' ',CLIAPE1X,' ',CLIAPE2X)), CLINOMXX) AS clinomxx ";
				$qCliNom .= "FROM $cAlfa.SIAI0150 ";
				$qCliNom .= "WHERE ";
				$qCliNom .= "CLIIDXXX = \"{$xDD['cliidxxx']}\" LIMIT 0,1";
				$xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
				if (mysql_num_rows($xCliNom) > 0) {
					$xRCN = mysql_fetch_array($xCliNom);
					$xDD['clinomxx'] = $xRCN['clinomxx'];
				} else {
					$xDD['clinomxx'] = "CLIENTE SIN NOMBRE";
				}

				switch($cAlfa) {
					case "FENIXSAS"://FENIXSAS
					case "TEFENIXSAS"://FENIXSAS
					case "DEFENIXSAS"://FENIXSAS
						#Busco la primera identifiacion para quien va dirigida la factura en las condiciones comerciales del cliente
						$xDD['cccintxx'] = "";
						$qNitFacA = "SELECT ";
						$qNitFacA .= "cccintxx ";
						$qNitFacA .= "FROM $cAlfa.fpar0151 ";
						$qNitFacA .= "WHERE ";
						$qNitFacA .= "CLIIDXXX = \"{$xDD['cliidxxx']}\" LIMIT 0,1";
						$xNitFacA = f_MySql("SELECT","",$qNitFacA,$xConexion01,"");
						if (mysql_num_rows($xNitFacA) > 0) {
							$vNitFacA = mysql_fetch_array($xNitFacA);
							if ($vNitFacA['cccintxx'] != "") {
								$vNitFacA = explode('~', $vNitFacA['cccintxx']);
								$xDD['cccintxx'] = $vNitFacA[0];
							}
						}

						#Busco el nombre del cliente para quien va dirigida la factura
						$xDD['nomfacta'] = "";
						if($xDD['cccintxx'] != "") {
							$qNomFacA  = "SELECT ";
							$qNomFacA .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
							$qNomFacA .= "FROM $cAlfa.SIAI0150 ";
							$qNomFacA .= "WHERE ";
							$qNomFacA .= "CLIIDXXX = \"{$xDD['cccintxx']}\" LIMIT 0,1";
							$xNomFacA = f_MySql("SELECT","",$qNomFacA,$xConexion01,"");
							if (mysql_num_rows($xNomFacA) > 0) {
								$vNomFacA = mysql_fetch_array($xNomFacA);
								$xDD['nomfacta'] = $vNomFacA['clinomxx'];
							}
						}
					break;
				}
				#Busco el nombre del director de cuenta
				$qNomDir  = "SELECT ";
				$qNomDir .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"DIRECTOR SIN NOMBRE\") AS dirnomxx ";
				$qNomDir .= "FROM $cAlfa.SIAI0003 ";
				$qNomDir .= "WHERE ";
				$qNomDir .= "USRIDXXX = \"{$xDD['diridxxx']}\" LIMIT 0,1";
				$xNomDir = f_MySql("SELECT","",$qNomDir,$xConexion01,"");
				if (mysql_num_rows($xNomDir) > 0) {
					$xRND = mysql_fetch_array($xNomDir);
					$xDD['dirnomxx'] = $xRND['dirnomxx'];
				} else {
					$xDD['dirnomxx'] = "DIRECTOR SIN NOMBRE";
        }

				$nPintar = 0; // bandera para pintar registros según el filtro
				$cEstado = ""; // estado de factura provisional cuando se selecciona el filtro ACTIVO
				switch ($cAlfa) {
					case "TEROLDANLO":
					case "DEROLDANLO":
					case "ROLDANLO":
						if ($xDD['regestxx'] == "FACTURADO") { // si el estado es FACTURADO, pueden ser provisionales, lo cual es lo que interesa aquí
							$cNumFactura = explode("-",$xDD['docfacxx']); // se explota por guión (-) el número de la factura
							if ($gEstado == "ACTIVO") { // si el filtro se seleccionó ACTIVO
								if (substr($cNumFactura[2],0,1) != "P") { // si la posición 2 no inicia en "P", indica que NO es PROVISIONAL, no se debe pintar para filtro ACTIVO
									$nPintar = 1; // no se pinta
								} else {
									$cEstado = "PROFORMA"; // solo cuando el filtro es ACTIVO y la factura es PROVISIONAL
								}
							} elseif ($gEstado == "FACTURADO") { // si el filtro se seleccionó FACTURADO
								if (substr($cNumFactura[2],0,1) == "P") { // si la posición 2 inicia en "P", indica que es PROVISIONAL, no se debe  pintar para filtro FACTURADO
									$nPintar = 1; // no se pinta
								}
							}
						}
					break;
					default:
						if ($gFecCorte != "" && $gEstado == "ACTIVO" && $xDD['regestxx'] == "FACTURADO") {
							$cEstado = "ACTIVO"; // COLOCA EL ESTADO ACTIVO  a la fecha de corte
						}
						// no hace nada
					break;
				}
				// f_Mensaje(__FILE__,__LINE__,$xDD['regestxx']);
				if ($nPintar == 0) { // Inicia pintado de registros
					$nAnticipo = 0;
					$nPagosTer = 0;
					$nSaldo = 0;

					$nAno01I = ((substr($xDD['regfcrex'],0,4)-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (substr($xDD['regfcrex'],0,4)-1);
          $nAno01F = date('Y');

          /*** Buscando Anticipos y PCC ***/
          $nCanReg01 = 0;
					for($nAnoBus = $nAno01I; $nAnoBus <= $nAno01F; $nAnoBus++){

						$nCanReg01++;
            if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = $objEstructuras->fnReiniciarConexionDBEstadoCuentaTramite($xConexion01); } 

            $cTabFac = $mTabDet[$nAnoBus];
						$qMovDO  = "SELECT ";
						$qMovDO .= "$cAlfa.$cTabFac.comidxxx, ";
						$qMovDO .= "$cAlfa.$cTabFac.comcodxx, ";
						$qMovDO .= "$cAlfa.$cTabFac.comcscxx, ";
						$qMovDO .= "$cAlfa.$cTabFac.comseqxx, ";
						$qMovDO .= "$cAlfa.$cTabFac.ccoidxxx, ";
						$qMovDO .= "$cAlfa.$cTabFac.pucidxxx, ";
						$qMovDO .= "$cAlfa.$cTabFac.ctoidxxx, ";
						$qMovDO .= "$cAlfa.$cTabFac.sucidxxx, ";
						$qMovDO .= "$cAlfa.$cTabFac.docidxxx, ";
						$qMovDO .= "$cAlfa.$cTabFac.docsufxx, ";
						$qMovDO .= "$cAlfa.$cTabFac.commovxx, ";
						$qMovDO .= "$cAlfa.$cTabFac.comfacxx, ";
						$qMovDO .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx*-1,$cAlfa.$cTabFac.comvlrxx) AS anticipo, ";
						$qMovDO .= "IF($cAlfa.$cTabFac.commovxx=\"C\",$cAlfa.$cTabFac.comvlrxx*-1,$cAlfa.$cTabFac.comvlrxx) AS pagoster  ";
						$qMovDO .= "FROM $cAlfa.$cTabFac ";
						$qMovDO .= "WHERE ";
						$qMovDO .= "$cAlfa.$cTabFac.comcsccx  =  \"{$xDD['docidxxx']}\" AND ";
            $qMovDO .= "$cAlfa.$cTabFac.comseqcx  =  \"{$xDD['docsufxx']}\" AND ";
            if ($gFecCorte != "") {
              $qMovDO .= "$cAlfa.$cTabFac.comfecxx <=  \"$gFecCorte\" AND ";
            }
						$qMovDO .= "$cAlfa.$cTabFac.regestxx = \"ACTIVO\" ";
            $xMovDO  = mysql_query($qMovDO,$xConexion01);
						// echo "<br>fcod".$nAnoBus."~".$qMovDO."~".mysql_num_rows($xMovDO)."<br><br>";
						while ($xRMD = mysql_fetch_array($xMovDO)) {
							$cDoFac = 0;
							if($xRMD['comfacxx'] != '' && $gFecCorte != "") {
								//En el caso de Aduanera existe pagos marcados con "opencomex" en el campo comfac
								if(trim(strtoupper($xRMD['comfacxx'])) == "OPENCOMEX"){
									$cDoFac = 1;  
									break;
								}else{
									$CscFac = explode("-", $xRMD['comfacxx']);							
									// Si el AÑO factura no pertenece al AÑO que fue causado el gasto
									// Busco desde el AÑO de la fecha de corte, en descenso hasta el AÑO del gasto
									for($nAno = $nAno01I; $nAno <= substr($gFecCorte,0,4); $nAno++){
										$cTabFac = $mTabFac[$nAno];
										//Buscar si el pago a tercero o anticipo tiene factura asociada antes la fecha de corte
										$qCabFac  = "SELECT ";
										$qCabFac .= "comfecxx ";
										$qCabFac .= "FROM $cAlfa.$cTabFac ";
										$qCabFac .= "WHERE ";
										$qCabFac .= "comidxxx = \"$CscFac[0]\" AND ";
										$qCabFac .= "comcodxx = \"$CscFac[1]\" AND ";
										$qCabFac .= "comcscxx = \"$CscFac[2]\" AND ";
										$qCabFac .= "comcsc2x = \"$CscFac[3]\" AND ";
										$qCabFac .= "regestxx IN (\"ACTIVO\",\"PROVISIONAL\")  ";
										$xCabFac  = mysql_query($qCabFac,$xConexion01);
										// if($xDD['docidxxx'] == "IMP-17112"){	
										// 	echo $qCabFac."~".mysql_num_rows($xCabFac)."\n\n";
										// }	
										if (mysql_num_rows($xCabFac) == 1) {
											$cDoFac = 1;  // El Pago a tercer o anticipo se encuentra FACTURADO
											break;
										}
									}
								}

							}

							if ($cDoFac == 0) {
								//Anticipos
								if (in_array("{$xRMD['pucidxxx']}~{$xRMD['ctoidxxx']}",$mCtoAnt) == true) {
									//echo "ANT ~~> ".$xRMD['comidxxx']."~".$xRMD['comcodxx']."~".$xRMD['comcscxx']."~".$xRMD['comseqxx']." ~~ ".$xRMD['commovxx']." ~~ ".$xRMD['anticipo']."<br>";
									$nSw_Incluir = 0;
									if ($xRMD['sucidxxx'] != "" && $xRMD['docidxxx'] != "" && $xRMD['docsufxx'] != "") {
										//si tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
										if ($xRMD['sucidxxx'] == $xDD['sucidxxx'] && $xRMD['docidxxx'] == $xDD['docidxxx'] && $xRMD['docsufxx'] == $xDD['docsufxx']) {
											$nSw_Incluir = 1;
										}
									} else {
										//Comparando por el centro de costo
										if ($xRMD['ccoidxxx'] == $xDD['ccoidxxx']) {
											$nSw_Incluir = 1;
										}
									}

									if ($nSw_Incluir == 1) {
										$nAnticipo += $xRMD['anticipo'];
									}
								}

								//PCC
								if (in_array("{$xRMD['pucidxxx']}~{$xRMD['ctoidxxx']}",$mCtoPCC) == true) {
									//echo "PCC ~~> ".$xRMD['comidxxx']."~".$xRMD['comcodxx']."~".$xRMD['comcscxx']."~".$xRMD['comseqxx']." ~~ ".$xRMD['commovxx']." ~~ ".$xRMD['pagoster']."<br>";
									$nSw_Incluir = 0;
									if ($xRMD['sucidxxx'] != "" && $xRMD['docidxxx'] != "" && $xRMD['docsufxx'] != "") {
										//si tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
										if ($xRMD['sucidxxx'] == $xDD['sucidxxx'] && $xRMD['docidxxx'] == $xDD['docidxxx'] && $xRMD['docsufxx'] == $xDD['docsufxx']) {
											$nSw_Incluir = 1;
										}
									} else {
										//Comparando por el centro de costo
										if ($xRMD['ccoidxxx'] == $xDD['ccoidxxx']) {
											$nSw_Incluir = 1;
										}
									}

									if ($nSw_Incluir == 1) {
										$nPagosTer += $xRMD['pagoster'];
									}
								}
							}
						} ## while ($xRMD = mysql_fetch_array($xMovDO)) {##
						//echo "Total Anticipos {$xDD['docidxxx']}-{$xDD['docsufxx']}: ".$nAnticipo."<br><br>";
						//echo "Total PCC {$xDD['docidxxx']}-{$xDD['docsufxx']}: ".$nPagosTer."<br><br>";
					} //for($nAnoBus = $nAno01I; $nAnoBus <= $nAno01F; $nAnoBus++){
					/*** Fin Buscando Anticipos y PCC ***/
				}

				$nAplica = 0;
				$nSaldo = (round($nAnticipo,5) + round($nPagosTer,5));
				if ($gFecCorte != "" && $xDD['regestxx'] != "INACTIVO"){
					if ($gEstado == "ACTIVO" &&  ($nSaldo != 0 || $xDD['regestxx'] == "ACTIVO") ) {
						$nAplica = 1;
					}
					if ($gEstado == "FACTURADO" && $nSaldo == 0 && $xDD['regestxx'] == "FACTURADO") {
						$nAplica = 1;
					}					
				}

				if (($nPintar == 0 && $gFecCorte == "") || ($nPintar == 0 && $nAplica == 1)) { // Inicia pintado de registros
					$nCReg++; 
					$nAno01I = ((substr($xDD['regfcrex'],0,4)-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (substr($xDD['regfcrex'],0,4)-1);
					$nAno01F = date('Y');

					/*** Recibos de caja ***/
					for($nAnoBus = $nAno01I; $nAnoBus <= $nAno01F; $nAnoBus++){
						$cTabFac = $mTabRec[$nAnoBus];
						$qRecCaj  = "SELECT ";
						$qRecCaj .= "$cAlfa.$cTabFac.sucidxxx, ";
						$qRecCaj .= "$cAlfa.$cTabFac.docidxxx, ";
						$qRecCaj .= "$cAlfa.$cTabFac.docsufxx, ";
						$qRecCaj .= "$cAlfa.$cTabFac.ccoidxxx, ";
						$qRecCaj .= "IF($cAlfa.$cTabFac.commovxx=\"D\",$cAlfa.$cTabFac.comvlrxx,0) AS pagoster ";
						$qRecCaj .= "FROM $cAlfa.$cTabFac ";
						$qRecCaj .= "WHERE ";
						$qRecCaj .= "$cAlfa.$cTabFac.comcsccx = \"{$xDD['docidxxx']}\" AND ";
						$qRecCaj .= "$cAlfa.$cTabFac.comseqcx = \"{$xDD['docsufxx']}\" AND ";
						$qRecCaj .= "$cAlfa.$cTabFac.regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
						$xRecCaj  = mysql_query($qRecCaj,$xConexion01);
						//echo "Recibos de caja:<br><br>";
						// echo "<br>fcme".$nAnoBus."~".$qRecCaj."~".mysql_num_rows($xRecCaj)."<br><br>";
						while ($xRRC = mysql_fetch_array($xRecCaj)) {

							$nSw_Incluir = 0;
							if ($xRRC['sucidxxx'] != "" && $xRRC['docidxxx'] != "" && $xRRC['docsufxx'] != "") {
								if ($xRRC['sucidxxx'] == $xDD['sucidxxx'] && $xRRC['docidxxx'] == $xDD['docidxxx'] && $xRRC['docsufxx'] == $xDD['docsufxx']) {
									$nSw_Incluir = 1;
								}
							} else {
								if ($xRRC['ccoidxxx'] == "" && $xRRC['ccoidxxx']) {
									$nSw_Incluir = 1;
								}
							}

							if ($nSw_Incluir == 1) {
								$nPagosTer += $xRRC['pagoster'];
							}
						}
					}
					/*** Fin Recibos de caja ***/

					//echo "Total Pagos a terceros: ".$nPagosTer."<br><br>";
					#Fin Buscando pagos a terceros

					#Buscando valor formualios
					$nValFor = 0;

					$qDatFor  = "SELECT ";
					$qDatFor .= "$cAlfa.ffoi0000.comvlrxx AS formuxx ";
					$qDatFor .= "FROM $cAlfa.ffoi0000 ";
					$qDatFor .= "WHERE ";
					$qDatFor .= "$cAlfa.ffoi0000.sucidxxx = \"{$xDD['sucidxxx']}\" AND ";
					$qDatFor .= "$cAlfa.ffoi0000.doccomex = \"{$xDD['docidxxx']}\" AND ";
					$qDatFor .= "$cAlfa.ffoi0000.docsufxx = \"{$xDD['docsufxx']}\" AND ";
					$qDatFor .= "$cAlfa.ffoi0000.regestxx = \"CONDO\" ";
					$xDatFor = f_MySql("SELECT","",$qDatFor,$xConexion01,"");
					//echo "Formularios:<br><br>";
					// echo "ffoi0000~".$qDatFor."~".mysql_num_rows($xDatFor)."<br><br>";
					while ($xDDF = mysql_fetch_array($xDatFor)) {
						$nValFor += $xDDF['formuxx'];
					}
					//echo "Total Formularios: ".$nValFor."<br><br>";
					#Fin Buscando valor formualios

					switch($xDD['doctipxx']){
						case "IMPORTACION":
						case "TRANSITO":
							## Buscando Fecha Mayor De Levante y Fecha de Entrega a Facturacion ##
							$qDat200  = "SELECT ";
							$qDat200 .= "$cAlfa.SIAI0200.DOIMYLEV, ";
							$qDat200 .= "$cAlfa.SIAI0200.DOIFENCA ";
							$qDat200 .= "FROM $cAlfa.SIAI0200 ";
							$qDat200 .= "WHERE ";
							$qDat200 .= "$cAlfa.SIAI0200.DOIIDXXX = \"{$xDD['docidxxx']}\" AND ";
							$qDat200 .= "$cAlfa.SIAI0200.DOISFIDX = \"{$xDD['docsufxx']}\" AND ";
							$qDat200 .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$xDD['sucidxxx']}\" AND ";
							$qDat200 .= "$cAlfa.SIAI0200.regestxx = \"ACTIVO\" LIMIT 0,1";
							$xDat200 = f_MySql("SELECT","",$qDat200,$xConexion01,"");
							if(mysql_num_rows($xDat200) > 0 ) {
								$xDDO = mysql_fetch_array($xDat200);
								$xDD['doimylev'] = $xDDO['DOIMYLEV'];
								$xDD['doifenca'] = $xDDO['DOIFENCA'];
							}
							break;
						case "EXPORTACION":
							/*** Buscando Fecha Entrega de Carpeta - Control Fechas Exportaciones. ***/
							$qDatExp  = "SELECT dexfenfa ";
							$qDatExp .= "FROM $cAlfa.siae0199 ";
							$qDatExp .= "WHERE ";
							$qDatExp .= "dexidxxx = \"{$xDD['docidxxx']}\" AND ";
							$qDatExp .= "admidxxx = \"{$xDD['sucidxxx']}\" LIMIT 0,1 ";
							$xDatExp  = f_MySql("SELECT","",$qDatExp,$xConexion01,"");
							// echo $qDatExp."~".mysql_num_rows($xDatExp)."<br>";
							if(mysql_num_rows($xDatExp) > 0 ) {
								$vDatExp = mysql_fetch_array($xDatExp);
								$xDD['doifenca'] = $vDatExp['dexfenfa'];
							}
						break;
					}

					if($bBasSia){
						$qAntPCC = "SELECT * ";
						$qAntPCC .= "FROM $cAlfa.sys00121 ";
						$qAntPCC .= "WHERE ";
						$qAntPCC .= "sucidxxx = \"{$xDD['sucidxxx']}\" AND ";
						$qAntPCC .= "docidxxx = \"{$xDD['docidxxx']}\" AND ";
						$qAntPCC .= "docsufxx = \"{$xDD['docsufxx']}\" LIMIT 0,1";
						$xAntPCC = f_MySql("SELECT","",$qAntPCC,$xConexion01,"");

						if(mysql_num_rows($xAntPCC) > 0){
							$vAntPCC = mysql_fetch_array($xAntPCC);

							if (substr($vAntPCC['regfcrex'],0,4) != "") {
								$nAnticiPro = 0;
								//Se busca desde el año anterior a la creacion del DO
								$mCrsMdo = array();

								$nAno02I = ((substr($vAntPCC['regfcrex'],0,4)-1) <  $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (substr($vAntPCC['regfcrex'],0,4)-1);
								$nAno02F = date('Y');

								for($iAno02 = $nAno02I; $iAno02 <= $nAno02F; $iAno02++){
									$qSqlMdo  = "SELECT DISTINCT ";
									$qSqlMdo .= "$cAlfa.fcod$iAno02.* ";
									$qSqlMdo .= "FROM $cAlfa.fcod$iAno02 ";
									$qSqlMdo .= "WHERE ";
									$qSqlMdo .= "(CONCAT($cAlfa.fcod$iAno02.pucidxxx,\"~\",$cAlfa.fcod$iAno02.ctoidxxx) IN ($cCtoAntyPCCDo) OR  "; // combinacion cuenta-concepto para anticipos y pcc
									$qSqlMdo .= "($cAlfa.fcod$iAno02.pucidxxx IN ($cPucId) AND $cAlfa.fcod$iAno02.comctocx = \"CD\")) AND "; //cuenta puc para los cuadre de DO de anticipos y pcc
									$qSqlMdo .= "$cAlfa.fcod$iAno02.comcsccx  = \"{$xDD['docidxxx']}\" AND ";
									$qSqlMdo .= "$cAlfa.fcod$iAno02.comseqcx  = \"{$xDD['docsufxx']}\" AND ";
									$qSqlMdo .= "$cAlfa.fcod$iAno02.regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
									$qSqlMdo .= "ORDER BY $cAlfa.fcod$iAno02.comidxxx,$cAlfa.fcod$iAno02.comcodxx,$cAlfa.fcod$iAno02.comcscxx ";

									$xCrsMdo = f_MySql("SELECT","",$qSqlMdo,$xConexion01,"");

									// echo "<pre>";
									// echo $qSqlMdo."<br> Results~".mysql_num_rows($xCrsMdo)."<br>";

									while ($xRMdo = mysql_fetch_array($xCrsMdo)) {
										$nIncluir = 0;

										if ($xRMdo['sucidxxx'] != "" && $xRMdo['docidxxx'] != "" && $xRMdo['docsufxx'] != "") {
											//si el pago tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
											if ($xRMdo['sucidxxx'] == $xDD['sucidxxx'] && $xRMdo['docidxxx'] == $xDD['docidxxx'] && $xRMdo['docsufxx'] == $xDD['docsufxx']) {
												$nIncluir = 1;
											}
										} else {
											//Comparando por el centro de costo
											if ($xRMdo['ccoidxxx'] == $vAntPCC['ccoidxxx']) {
												$nIncluir = 1;
											}
										}

										$cResId = "";
										if ($nIncluir == 1 || ($nIncluir == 0 && $xRMdo['comidxxx'] == "F")) {
												// Datos Cabecera
												$qFcoc  = "SELECT ";
												$qFcoc .= "$cAlfa.fcoc$iAno02.comfpxxx, ";
												$qFcoc .= "$cAlfa.fcoc$iAno02.residxxx, ";
												$qFcoc .= "$cAlfa.fcoc$iAno02.regestxx AS ESTADO ";
												$qFcoc .= "FROM $cAlfa.fcoc$iAno02 ";
												$qFcoc .= "WHERE ";
												$qFcoc .= "$cAlfa.fcoc$iAno02.comidxxx = \"{$xRMdo['comidxxx']}\" AND ";
												$qFcoc .= "$cAlfa.fcoc$iAno02.comcodxx = \"{$xRMdo['comcodxx']}\" AND ";
												$qFcoc .= "$cAlfa.fcoc$iAno02.comcscxx = \"{$xRMdo['comcscxx']}\" AND ";
												$qFcoc .= "$cAlfa.fcoc$iAno02.comcsc2x = \"{$xRMdo['comcsc2x']}\" LIMIT 0,1";
												$xFcoc  = f_MySql("SELECT","",$qFcoc,$xConexion01,"");
												// f_Mensaje(__FILE__,__LINE__,$qFcoc." ~ ".mysql_num_rows($xFcoc));
												if (mysql_num_rows($xFcoc) > 0) {
													$xRFcoc = mysql_fetch_array($xFcoc);
													$xRMdo['comfpxxx'] = $xRFcoc['comfpxxx'];
													$xRMdo['ESTADO']   = $xRFcoc['ESTADO'];
													$cResId            = "{$xRFcoc['residxxx']}";
												}
										}

										if ($nIncluir == 0 && $xRMdo['comidxxx'] == "F") {
											//Si es una factura se compara contra el campo comtraxx
											//el centro de costo del DO debe pertencer a los centro de costo permitidos para la resolucion de facturacion
											$nCco = "NO";
											switch ($cAlfa){
												case "SIACOSIA":
												case "TESIACOSIP":
												case "DESIACOSIP":
													//Para SICACO se busca en la resolucion de facturacion a nivel de sucursal y no de centro de costo
													//Para ellos el centro de costo es independiente de la sucursal del DO
													$nCco = (in_array("\"{$xDD['sucidxxx']}\"", $mSucRes["$cResId"]) == true) ? "SI" : "NO";
												break;
												default:
													$nCco = (in_array("\"{$vAntPCC['ccoidxxx']}\"", $mCcoRes["$cResId"]) == true) ? "SI" : "NO";
												break;
											}

											if(substr_count($xRMdo['comtraxx'], "-{$xDD['docidxxx']}-{$xDD['docsufxx']}") > 0 && $nCco == "SI") {
												$nIncluir = 1;
											}
										}

										//Buscando si el CD pertenece a las facturas del DO
										if ($xRMdo['comctocx'] == "CD" && $xRMdo['comidxxx'] == "F") {
											$nIncluir = 0; //Varible que indica si el registro es del DO o NO y si se debe tener en cuenta

											$mFacCD = array();
											$mFacCD = f_explode_array($xRMdo['comfpxxx'],"|","~");
											for($nCD=0;$nCD<count($mFacCD);$nCD++){
												//comparo sucursal, do y sufijo
												if($mFacCD[$nCD][15] == $xDD['sucidxxx'] && $mFacCD[$nCD][2] == $xDD['docidxxx'] && $mFacCD[$nCD][3] == $xDD['docsufxx']){
													$nIncluir = 1;
													$nCD=count($mFacCD);
												}
											}
										}

										if ($nIncluir == 1) {
											// Buscando datos de la fpar0119
											$qFpar119  = "SELECT ";
											$qFpar119 .= "$cAlfa.fpar0119.ctodocxg, ";
											$qFpar119 .= "$cAlfa.fpar0119.ctodocxl ";
											$qFpar119 .= "FROM $cAlfa.fpar0119 ";
											$qFpar119 .= "WHERE ";
											$qFpar119 .= "$cAlfa.fpar0119.pucidxxx = \"{$xRMdo['pucidxxx']}\" AND ";
											$qFpar119 .= "$cAlfa.fpar0119.ctoidxxx = \"{$xRMdo['ctoidxxx']}\" LIMIT 0,1";
											$xFpar119  = f_MySql("SELECT","",$qFpar119,$xConexion01,"");
											// echo $qFpar119." ~ ".mysql_num_rows($xFpar119)." ~ <br>";
											// f_Mensaje(__FILE__,__LINE__,$qFpar119." ~ ".mysql_num_rows($xFpar119));
											if (mysql_num_rows($xFpar119) > 0) {
												$xRFp119 = mysql_fetch_array($xFpar119);
												$xRMdo['ctodocxg'] = $xRFp119['ctodocxg'];
												$xRMdo['ctodocxl'] = $xRFp119['ctodocxl'];
											}

											$mCrsMdo[count($mCrsMdo)] = $xRMdo;
										}
									}

									$qSqlDoI  = "SELECT DISTINCT ";
									$qSqlDoI .= "$cAlfa.fcod$iAno02.* ";
									$qSqlDoI .= "FROM $cAlfa.fcod$iAno02 ";
									$qSqlDoI .= "WHERE ";
									$qSqlDoI .= "CONCAT($cAlfa.fcod$iAno02.pucidxxx,\"~\",$cAlfa.fcod$iAno02.ctoidxxx) IN ($cDoInf) AND "; // Igual a la cuenta con la que se creo el DO.
									$qSqlDoI .= "$cAlfa.fcod$iAno02.comcscc2 = \"{$xDD['docidxxx']}\" AND ";
									$qSqlDoI .= "$cAlfa.fcod$iAno02.comseqc2 = \"{$xDD['docsufxx']}\" AND ";
									$qSqlDoI .= "$cAlfa.fcod$iAno02.regestxx = \"ACTIVO\" ";
									$qSqlDoI .= "ORDER BY $cAlfa.fcod$iAno02.comidxxx,$cAlfa.fcod$iAno02.comcodxx,$cAlfa.fcod$iAno02.comcscxx ";
									$xCrsDoI = f_MySql("SELECT","",$qSqlDoI,$xConexion01,"");

									while ($xRAD = mysql_fetch_array($xCrsDoI)) {
										/**
										* Buscando datos de la fpar0119
										*/
										$qFpar119  = "SELECT ";
										$qFpar119 .= "$cAlfa.fpar0119.ctodocxg, ";
										$qFpar119 .= "$cAlfa.fpar0119.ctodocxl ";
										$qFpar119 .= "FROM $cAlfa.fpar0119 ";
										$qFpar119 .= "WHERE ";
										$qFpar119 .= "$cAlfa.fpar0119.pucidxxx = \"{$xRAD['pucidxxx']}\" AND ";
										$qFpar119 .= "$cAlfa.fpar0119.ctoidxxx = \"{$xRAD['ctoidxxx']}\" LIMIT 0,1";
										$xFpar119  = f_MySql("SELECT","",$qFpar119,$xConexion01,"");
										//f_Mensaje(__FILE__,__LINE__,$qFpar119." ~ ".mysql_num_rows($xFpar119));

										if (mysql_num_rows($xFpar119) > 0) {
											$xRFp119 = mysql_fetch_array($xFpar119);
											$xRAD['ctodocxg'] = $xRFp119['ctodocxg'];
											$xRAD['ctodocxl'] = $xRFp119['ctodocxl'];
										}

										/**
										* Datos Cabecera
										*/
										$qFcoc  = "SELECT ";
										$qFcoc .= "$cAlfa.fcoc$iAno02.regestxx AS ESTADO ";
										$qFcoc .= "FROM $cAlfa.fcoc$iAno02 ";
										$qFcoc .= "WHERE ";
										$qFcoc .= "$cAlfa.fcoc$iAno02.comidxxx = \"{$xRAD['comidxxx']}\" AND ";
										$qFcoc .= "$cAlfa.fcoc$iAno02.comcodxx = \"{$xRAD['comcodxx']}\" AND ";
										$qFcoc .= "$cAlfa.fcoc$iAno02.comcscxx = \"{$xRAD['comcscxx']}\" AND ";
										$qFcoc .= "$cAlfa.fcoc$iAno02.comcsc2x = \"{$xRAD['comcsc2x']}\" LIMIT 0,1";
										$xFcoc  = f_MySql("SELECT","",$qFcoc,$xConexion01,"");
										//f_Mensaje(__FILE__,__LINE__,$qFcoc." ~ ".mysql_num_rows($xFcoc));
										if (mysql_num_rows($xFcoc) > 0) {
											$xRFcoc = mysql_fetch_array($xFcoc);
											$xRAD['ESTADO'] = $xRFcoc['ESTADO'];
										}


										// Son anticipos sin legalizar unicamente las G-XXX que tienen saldo en la CxP, porque despues de que
										// se hace la P-28 y se cruza contra la G-XXX el saldo de esta desaparece de la CxP.
										$qCxPAnt  = "SELECT * ";
										$qCxPAnt .= "FROM $cAlfa.fcxp0000 ";
										$qCxPAnt .= "WHERE ";
										$qCxPAnt .= "comidxxx = \"{$xRAD['comidcxx']}\" AND ";
										$qCxPAnt .= "comcodxx = \"{$xRAD['comcodcx']}\" AND ";
										$qCxPAnt .= "comcscxx = \"{$xRAD['comcsccx']}\" AND ";
										$qCxPAnt .= "comseqxx = \"{$xRAD['comseqcx']}\" AND ";
										$qCxPAnt .= "pucidxxx = \"{$xRAD['pucidxxx']}\" AND ";
										$qCxPAnt .= "regestxx = \"ACTIVO\" LIMIT 0,1";
										$xCxPAnt  = f_MySql("SELECT","",$qCxPAnt,$xConexion01,"");

										// Son anticipos sin legalizar unicamente las G-XXX que tienen saldo en la CxC, porque despues de que
										// se hace la P-28 y se cruza contra la G-XXX el saldo de esta desaparece de la CxC.
										$qCxCAnt  = "SELECT * ";
										$qCxCAnt .= "FROM $cAlfa.fcxc0000 ";
										$qCxCAnt .= "WHERE ";
										$qCxCAnt .= "comidxxx = \"{$xRAD['comidcxx']}\" AND ";
										$qCxCAnt .= "comcodxx = \"{$xRAD['comcodcx']}\" AND ";
										$qCxCAnt .= "comcscxx = \"{$xRAD['comcsccx']}\" AND ";
										$qCxCAnt .= "comseqxx = \"{$xRAD['comseqcx']}\" AND ";
										$qCxCAnt .= "pucidxxx = \"{$xRAD['pucidxxx']}\" AND ";
										$qCxCAnt .= "regestxx = \"ACTIVO\" LIMIT 0,1";
										$xCxCAnt  = f_MySql("SELECT","",$qCxCAnt,$xConexion01,"");

										if (mysql_num_rows($xCxPAnt) > 0 || mysql_num_rows($xCxCAnt)> 0) {
											/**
											* Trayendo el saldo del anticipo
											*/
											$nSalAnt = 0;
											$nAnoAnt = substr($xRAD['regfcrex'], 0,4);
											for ($nA=$nAnoAnt; $nA<=date('Y');$nA++) {
												$qDatMov  = "SELECT ";
												$qDatMov .= "SUM(if(commovxx = \"D\", comvlrxx, comvlrxx*-1)) AS comvlrxx ";
												$qDatMov .= "FROM $cAlfa.fcod$nA ";
												$qDatMov .= "WHERE ";
												$qDatMov .= "comidcxx = \"{$xRAD['comidcxx']}\" AND ";
												$qDatMov .= "comcodcx = \"{$xRAD['comcodcx']}\" AND ";
												$qDatMov .= "comcsccx = \"{$xRAD['comcsccx']}\" AND ";
												$qDatMov .= "comseqcx = \"{$xRAD['comseqcx']}\" AND ";
												$qDatMov .= "pucidxxx = \"{$xRAD['pucidxxx']}\" AND ";
												$qDatMov .= "regestxx = \"ACTIVO\" ";
												$qDatMov .= "GROUP BY comidcxx,comcodcx,comcsccx,pucidxxx ";
												$xDatMov  = f_MySql("SELECT","",$qDatMov,$xConexion01,"");
												//f_Mensaje(__FILE__,__LINE__,$qDatMov." ~ ".mysql_num_rows($xDatMov));
												while ($xRDM = mysql_fetch_array($xDatMov)){
													$nSalAnt += $xRDM['comvlrxx'];
												}
											}
											$xRAD['comvlrcx'] = $nSalAnt;

											$mCrsMdo[count($mCrsMdo)] = $xRAD;
										}
									}
								}

								for ($e=0; $e < count($mCrsMdo); $e++) {
									if ((($mCrsMdo[$e]['comidxxx'] == "G" && $mCrsMdo[$e]['ctodocxg'] == "SI") || ($mCrsMdo[$e]['comidxxx'] == "L" && $mCrsMdo[$e]['ctodocxl'] == "SI"))) {
										if ($mCrsMdo[$e]['commovxx'] == "C") {
											$cImprime = $mCrsMdo[$e]['comvlrxx']*-1;
										} elseif ($mCrsMdo[$e]['commovxx'] == "D") {
											$cImprime = $mCrsMdo[$e]['comvlrxx'];
										}else{
											if($mCrsMdo[$e]['comidxxx'] == "M"){
												$qMovCto  = "SELECT ";
												$qMovCto .= "$cAlfa.fpar0119.ctocomxx ";
												$qMovCto .= "FROM $cAlfa.fpar0119 ";
												$qMovCto .= "WHERE ";
												$qMovCto .= "$cAlfa.fpar0119.ctoidxxx = \"{$mCrsMdo[$e]['ctoidxxx']}\" AND ";
												$qMovCto .= "$cAlfa.fpar0119.ctocomxx LIKE '%|{$mCrsMdo[$e]['comidxxx']}~{$mCrsMdo[$e]['comcodxx']}~%' AND ";
												$qMovCto .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" LIMIT 0,1 ";
												$xMovCto  = f_MySql("SELECT","",$qMovCto,$xConexion01,"");
												$vMovCto  = mysql_fetch_array($xMovCto);
												$mMovCto  = f_Explode_Array($vMovCto['ctocomxx'],"|","~");
												for($a=0;$a<count($mMovCto);$a++){
													if(($mMovCto[$a][0] == $mCrsMdo[$e]['comidxxx']) && ($mMovCto[$a][1] == $mCrsMdo[$e]['comcodxx']) ){
														$mCrsMdo[$e]['commovxx'] = $mMovCto[$a][2];
														if ($mMovCto[$a][2] == "C") {
															$cImprime = $mCrsMdo[$e]['comvlrxx']*-1;
														} elseif ($mMovCto[$a][2] == "D") {
															$cImprime = $mCrsMdo[$e]['comvlrxx'];
														}
													}
												}
											}
										}
										$nAnticiPro += $cImprime;
									}
								}
							}
						}
          }

					## Fin Buscando Fecha Mayor De Levante y Fecha de Entrega a Facturacion##
					switch ($cTipo) {
						case 1:  // PINTA POR PANTALLA//
							if ($_SERVER["SERVER_PORT"] != "") {
								$zColorPro = "#000000";

								$nPagTer = $nPagosTer + $nValFor;
								$nSaldo  = $nAnticipo  + $nPagTer; ?>
								<tr bgcolor = "white" height="20"   style="padding-left:4px;padding-right:4px">
									<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>">
										<a href ="javascript:f_Imprimir('<?php echo $xDD['comidxxx'] ?>', '<?php echo $xDD['comcodxx'] ?>', '<?php echo $xDD['sucidxxx'] ?>', '<?php echo $xDD['doctipxx'] ?>', '<?php echo $xDD['docidxxx'] ?>', '<?php echo $xDD['docsufxx'] ?>', '<?php echo $xDD['pucidxxx'] ?>', '<?php echo $xDD['ccoidxxx'] ?>', '<?php echo $xDD['cliidxxx'] ?>', '<?php echo $xDD['regfcrex'] ?>')">
											<?php echo $xDD['docidxxx']."-".$xDD['docsufxx'] ?>
										</a>
									</td>
									<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $xDD['sucidxxx'] ?></td>
									<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['docpedxx'] != "") ? $xDD['docpedxx'] : "&nbsp;"; ?></td>
									<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['regfcrex'] != '0000-00-00' && $xDD['regfcrex'] != "") ? $xDD['regfcrex']: "&nbsp;"; ?></td>
									<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $xDD['doctipxx'] ?></td>
									<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['doimylev'] != '0000-00-00' && $xDD['doimylev'] != "") ? $xDD['doimylev']: "&nbsp;"; ?></td>
									<?php 
										switch($cAlfa){ 
											case "GRUMALCO"://GRUMALCO
											case "TEGRUMALCO"://GRUMALCO
											case "DEGRUMALCO"://GRUMALCO
												?>
													<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['docusrce'] != "" && $xDD['docfecce'] != "0000-00-00 00:00:00")? "SI": ""; ?></font></b></td>
												<?php
											break;
										}
									?>
									<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['doifenca'] != '0000-00-00' && $xDD['doifenca'] != "") ? $xDD['doifenca']: "&nbsp;"; ?></td>
									<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xDD['cliidxxx'] ?></td>
									<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xDD['clinomxx'] ?></td>
									<?php 
										switch($cAlfa){ 
											case "FENIXSAS"://FENIXSAS
											case "TEFENIXSAS"://FENIXSAS
											case "DEFENIXSAS"://FENIXSAS
												?>
													<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xDD['cccintxx'] ?></td>
													<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xDD['nomfacta'] ?></td>
												<?php
											break;
										}
									?>
									<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($cEstado != "") ? $cEstado : (($xDD['regestxx'] != "") ? $xDD['regestxx'] : "&nbsp;"); ?></td>
									<td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['docffecx'] != '0000-00-00' && $xDD['docffecx'] != "") ? $xDD['docffecx']: "&nbsp;"; ?></td>
									<td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo ($xDD['dirnomxx'] != "") ? $xDD['dirnomxx'] : "&nbsp;"; ?></td>
									<td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($nAnticipo != "") ? number_format($nAnticipo,0,',','.') : "&nbsp;" ?></td>
									<?php if($bBasSia){ ?>
										<td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo number_format($nAnticiPro,0,',','.') ?></td>
									<?php } ?>
									<td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo number_format($nPagTer,0,',','.') ?></td>
									<td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo number_format($nSaldo,0,',','.')  ?></td>
								</tr>
								<?php 
							}
						break;
						case 2:
								$zColorPro = "#000000";

								$nPagTer = $nPagosTer + $nValFor;
								$nSaldo = $nAnticipo  + $nPagTer;

								$nValor01 = ($xDD['docpedxx'] != "") ? $xDD['docpedxx'] : "";
								$nValor02 = ($cEstado != "") ? $cEstado : (($xDD['regestxx'] != "") ? $xDD['regestxx'] : "");
								$nValor03 = ($xDD['dirnomxx'] != "") ? $xDD['dirnomxx'] : "";
								$nValor04 = ($nAnticipo != "") ? number_format($nAnticipo,0,',','') : "";
								$nValor05 = ($nPagTer != "") ? number_format($nPagTer,0,',','') : "";
								$nValor06 = ($nSaldo  != "") ? number_format($nSaldo,0,',','')  : "";
								$nValor07 = ($xDD['docffecx'] != '0000-00-00' && $xDD['docffecx'] != "") ? $xDD['docffecx']: "";
								$nValor08 = ($xDD['regfcrex'] != '0000-00-00' && $xDD['regfcrex'] != "") ? $xDD['regfcrex']: "";
								$nValor09 = ($xDD['doimylev'] != '0000-00-00' && $xDD['doimylev'] != "") ? $xDD['doimylev']: "";
								$nValor10 = ($xDD['doifenca'] != '0000-00-00' && $xDD['doifenca'] != "") ? $xDD['doifenca']: "";
								$nValor11 = ($nAnticiPro != "") ? number_format($nAnticiPro,0,',','') : "";

								$data  = '<tr bgcolor = "white" height="20"   style="padding-left:4px;padding-right:4px">';
									$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$xDD['docidxxx']."-".$xDD['docsufxx'].'</td>';
									$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$xDD['sucidxxx'].'</td>';
									$data .= '<td class="letra7" align="left"   style = "mso-number-format:\'\@\';color:'.$zColorPro.'">'.$nValor01.'</td>';
									$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$nValor08.'</td>';
									$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$xDD['doctipxx'].'</td>';
									$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$nValor09.'</td>';
									switch($cAlfa){
										case "GRUMALCO"://GRUMALCO
										case "TEGRUMALCO"://GRUMALCO
										case "DEGRUMALCO"://GRUMALCO
											$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.(($xDD['docusrce'] != "" && $xDD['docfecce'] != "0000-00-00 00:00:00")? "SI": "") .'</td>';
										break;
									}
									$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$nValor10.'</td>';
									$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$xDD['cliidxxx'].'</td>';
									$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$xDD['clinomxx'].'</td>';
									switch($cAlfa){
										case "FENIXSAS"://FENIXSAS
										case "TEFENIXSAS"://FENIXSAS
										case "DEFENIXSAS"://FENIXSAS
											$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$xDD['cccintxx'].'</td>';
											$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$xDD['nomfacta'].'</td>';
										break;
									}
									$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$nValor02.'</td>';
									$data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$nValor07.'</td>';
									$data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$nValor03.'</td>';
									$data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'">'.$nValor04.'</td>';
									if($bBasSia){
										$data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'">'.$nValor11.'</td>';
									}
									$data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'">'.$nValor05.'</td>';
									$data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'">'.$nValor06.'</td>';
								$data .= '</tr>';

								fwrite($fOp, $data);
						break;
						case 3 :
							if ($_SERVER["SERVER_PORT"] != "") {
								$nPag = 1;
								$zColorPro = "#000000";
	
								$nPagTer = $nPagosTer;
								$nSaldo = $nAnticipo  + $nPagTer;
	
								$nValor01 = ($xDD['docpedxx'] != "") ? $xDD['docpedxx'] : "";
								$nValor02 = ($cEstado != "") ? $cEstado : (($xDD['regestxx'] != "") ? $xDD['regestxx'] : "");
								$nValor03 = ($xDD['dirnomxx'] != "") ? $xDD['dirnomxx'] : "";
								$nValor04 = ($nAnticipo != "") ? number_format($nAnticipo,0,',','') : "";
								$nValor05 = ($nPagTer != "") ? number_format($nPagTer,0,',','') : "";
								$nValor06 = ($nSaldo  != "") ? number_format($nSaldo,0,',','')  : "";
								$nValor07 = ($xDD['docffecx'] != '0000-00-00' && $xDD['docffecx'] != "") ? $xDD['docffecx']: "";
								$nValor08 = ($xDD['regfcrex'] != '0000-00-00' && $xDD['regfcrex'] != "") ? $xDD['regfcrex']: "";
								$nValor09 = ($xDD['doimylev'] != '0000-00-00' && $xDD['doimylev'] != "") ? $xDD['doimylev']: "";
								$nValor10 = ($xDD['doifenca'] != '0000-00-00' && $xDD['doifenca'] != "") ? $xDD['doifenca']: "";
								$nValor11 = ($nAnticiPro != "") ? number_format($nAnticiPro,0,',','') : "";
	
								$pdf->SetX($nPosXAnt);
	
								if($bBasSia){
									$pdf->Row(
										array(
											$xDD['docidxxx']."-".$xDD['docsufxx'],
											$xDD['sucidxxx'],
											$nValor01,
											$nValor08,
											$xDD['doctipxx'],
											$nValor09,
											$nValor10,
											$xDD['cliidxxx'],
											$xDD['clinomxx'],
											$nValor02,
											$nValor07,
											$nValor03,
											$nValor04,
											$nValor11,
											$nValor05,
											$nValor06
										)
									);
								} else {
									switch($cAlfa){ 
										case "GRUMALCO"://GRUMALCO
										case "TEGRUMALCO"://GRUMALCO
										case "DEGRUMALCO"://GRUMALCO
											$cCodEsp = ($xDD['docusrce'] != "" && $xDD['docfecce'] != "0000-00-00 00:00:00")? "SI": "";
											$pdf->Row(
												array(
													$xDD['docidxxx']."-".$xDD['docsufxx'],
													$xDD['sucidxxx'],
													$nValor01,
													$nValor08,
													$xDD['doctipxx'],
													$nValor09,
													$cCodEsp,
													$nValor10,
													$xDD['cliidxxx'],
													$xDD['clinomxx'],
													$nValor02,
													$nValor07,
													$nValor03,
													$nValor04,
													$nValor05,
													$nValor06
												)
											);
										break;
										default:
											$pdf->Row(
												array(
													$xDD['docidxxx']."-".$xDD['docsufxx'],
													$xDD['sucidxxx'],
													$nValor01,
													$nValor08,
													$xDD['doctipxx'],
													$nValor09,
													$nValor10,
													$xDD['cliidxxx'],
													$xDD['clinomxx'],
													$nValor02,
													$nValor07,
													$nValor03,
													$nValor04,
													$nValor05,
													$nValor06
												)
											);
										break;
									}
								}
							}
						break;
					}//Fin Switch
				} // FIN pintado de registros
			} ## while ($xDD = mysql_fetch_array($xDatDoi)) { ##

			switch ($cTipo) {
				case 1:
					if ($_SERVER["SERVER_PORT"] != "") {
						// PINTA POR PANTALLA// ?>
										</table>
									</form>
								</body>
							</html>
							<script type="text/javascript">
								document.forms['frgrm']['nCanReg'].value = "<?php echo $nCReg ?>";
							</script>
						<?php 
					}
				break;
				case 2:
						/* Colspan una ultima fila */
						$nExcCsp = 15;
						switch($cAlfa){ 
							case "GRUMALCO"://GRUMALCO
							case "TEGRUMALCO"://GRUMALCO
							case "DEGRUMALCO"://GRUMALCO
								$nExcCsp += 1;
							break;
						}
						$data  = '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
							$data .= '<td class="name" colspan="'.$nExcCsp.'" align="center">';
								$data .= '<center>';
									$data .= '<font size="3">';
										$data   .= '<b>TOTAL TRAMITES EN ESTA CONSULTA ['.$nCReg.']<br>';
									$data .= '</font>';
								$data .= '</center>';
							$data .= '</td>';
						$data .= '</tr>';
					$data .= '</table>';
					
					fwrite($fOp, $data);
					fclose($fOp);
						
					if (file_exists($cFile)) {

						if ($data == "") {
							$data = "\n(0) REGISTROS!\n";
						}

						// Obtener la ruta absoluta del archivo
						$cAbsolutePath = realpath($cFile);
						$cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

						if ($data == "") {
							$data = "\n(0) REGISTROS!\n";
						}

						if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
							chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
							$cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFile);
	
							if ($_SERVER["SERVER_PORT"] != "") {
	
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
							}else{
								$cNomArc = $cNomFile;
							}
						}

					}else {
						$nSwitch = 1;
						if ($_SERVER["SERVER_PORT"] != "") {
							f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
						} else {
							$cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
						}
					}

				break;
				case 3 :

					if ($_SERVER["SERVER_PORT"] != "") {
						$nPag = 0;
	
						$pdf->Ln(5);
						$pdf->SetFont('verdana','',8);
						$pdf->SetX(0);
						$pdf->Cell(280,6,'TOTAL TRAMITES EN ESTA CONSULTA ['.$nCReg.']',0,0,'C');
	
						$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";
	
						$pdf->Output($cFile);
	
						if (file_exists($cFile)){
							chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
						} else {
							f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
						}
	
						echo "<html><script>document.location='$cFile';</script></html>";
					}
				break;
			}//Fin Switch

		}
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
  
  /**
   * Clase para crear Tabla Temporal
   */
  class cEstructurasEstadoCuentaTramite {

    /**
     * Metodo que se encarga de Crear las Estructuras de las Tablas
     */
    function fnCrearEstructurasEstadoCuentaTramite($pcTabla) {
      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var number
       */
      $nSwitch = 0;

      /**
       * Matriz para Retornar Valores
       */
      $mReturn = array();

      /**
       * Reservando Primera Posición para retorna true o false
       */
      $mReturn[0] = "";

      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionTM = $this->fnConectarDBEstadoCuentaTramite();
      if ($mReturnConexionTM[0] == "true") {
        $xConexionTM = $mReturnConexionTM[1];
      } else {
        $nSwitch = 1;
        for ($nR = 1; $nR < count($mReturnConexionTM); $nR++) {
          $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
        }
      }

      /**
       * Random para Nombre de la Tabla
       */
      $cTabCar  = mt_rand(1000000000, 9999999999);

      $cTabla = "memesctr" . $cTabCar;

      $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla LIKE $cAlfa.$pcTabla";
      $xNewTab  = mysql_query($qNewTab, $xConexionTM);
      if (!$xNewTab) {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "(" . __LINE__ . ") Error al Crear Tabla Temporal para Reporte Estado Cuenta de Tramite." . mysql_error($xConexionTM);
      }

      if($nSwitch == 0){
        $mReturn[0] = "true"; 
        $mReturn[1] = $cTabla;
      }else{
        $mReturn[0] = "false";
      }
      return $mReturn;
    } ## function fnCrearEstructurasTOE($pArrayParametros){ ##

    /**
    * Metodo que realiza la conexion
    */
    function fnConectarDBEstadoCuentaTramite() {
      global $cAlfa;

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var number
       */
      $nSwitch = 0;

      /**
       * Matriz para Retornar Valores
       */
      $mReturn = array();

      /**
       * Reservo Primera Posicion para retorna true o false
       */
      $mReturn[0] = "";

      $xConexion99 = mysql_connect(OC_SERVER, OC_USERROBOT, OC_PASSROBOT) or die("El Sistema no Logro Conexion con " . OC_SERVER);
      
      if ($xConexion99) {
        $nSwitch = 0;
      } else {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "El Sistema no Logro Conexion con " . OC_SERVER;
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
        $mReturn[1] = $xConexion99;
      } else {
        $mReturn[0] = "false";
      }
      return $mReturn;
    } ##function fnConectarDBEstadoCuentaTramite(){##

    /**
    * Metodo que reinicia la conexion
    */
    function fnReiniciarConexionDBEstadoCuentaTramite($pConexion){
      global $cHost;  global $cUserHost;  global $cPassHost;

      mysql_close($pConexion);
      $xConexion01 = mysql_connect($cHost,$cUserHost,$cPassHost,TRUE);

      return $xConexion01;
    }##function fnReiniciarConexionDBEstadoCuentaTramite(){##

  }
  ?>

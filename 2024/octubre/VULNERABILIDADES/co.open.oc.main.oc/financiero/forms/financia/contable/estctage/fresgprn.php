<?php
  /**
   * Imprime Reporte Estado Cuenta General.
   * --- Descripcion: Permite Imprimir Reporte Estado Cuenta General.
   * @author Juan Jose Trujillo Chimbaco <juan.trujillo@open-eb.co>
   */

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
	}

  /**
   *  Cookie fija
   */
  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb   = $kDf[3];
  $kUser      = $kDf[4];
  $kLicencia  = $kDf[5];
  $swidth     = $kDf[6];
  $kModId     = $_COOKIE["kModId"];
  $kProId     = $_COOKIE["kProId"];

  $cSystemPath = OC_DOCUMENTROOT;


  if ($_SERVER["SERVER_PORT"] != "") {
		/*** Ejecutar proceso en Background ***/
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;

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
  }

  if ($_SERVER["SERVER_PORT"] == "") {
    $gTerId   = $_POST['gTerId'];
    $gSucId   = $_POST['gSucId'];
    $gDocTip  = $_POST['gDocTip'];
    $gDocNro  = $_POST['gDocNro'];
    $gDocSuf  = $_POST['gDocSuf'];
    $gCcoId   = $_POST['gCcoId'];
    $gPedido  = $_POST['gPedido'];
    $gDesde   = $_POST['gDesde'];
    $gHasta   = $_POST['gHasta'];
    $cTipo    = $_POST['cTipo'];
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;
	
    $strPost = "gTerId~"  . $gTerId . 
              "|gSucId~"  . $gSucId . 
              "|gDocTip~" . $gDocTip . 
              "|gDocNro~" . $gDocNro . 
              "|gDocSuf~" . $gDocSuf .  
              "|gCcoId~"  . $gCcoId . 
              "|gPedido~" . $gPedido .
              "|gDesde~"  . $gDesde . 
              "|gHasta~"  . $gHasta . 
              "|cTipo~"   . $cTipo;

		$vParBg['pbadbxxx'] = $cAlfa;                           // Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                    // Modulo
		$vParBg['pbatinxx'] = "ESTADOCUENTAGENERAL";            // Tipo Interface
		$vParBg['pbatinde'] = "ESTADO DE CUENTA GENERAL";       // Descripcion Tipo de Interfaz
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

      // Generar Reporte
      // Generar Reporte
      // Generar Reporte

      $cFecha = fechaCastellano($gDesde)." AL ".fechaCastellano($gHasta);
      $gTerId = trim($gTerId);

      $AnoIni = $vSysStr['financiero_ano_instalacion_modulo'];
      $AnoFin = substr($gHasta,0,4);
      $cFechaActual = date('Y-m-d');

      # Traigo informacion de las cuentas contables
      $qDatExt  = "SELECT comidxxx,comcodxx,comtipxx ";
      $qDatExt .= "FROM $cAlfa.fpar0117 ";
      $qDatExt .= "WHERE ";
      $qDatExt .= "(comidxxx = \"P\" OR comidxxx = \"L\" OR comidxxx = \"C\") AND ";
      $qDatExt .= "regestxx = \"ACTIVO\" ";
      $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
      $mComP = array();
      while ($xRDE = mysql_fetch_array($xDatExt)){
        $mComP[$xRDE['comidxxx']][$xRDE['comcodxx']] = $xRDE['comtipxx'];
      }

      # Traigo el Nombre del Cliente
      $qNomCli  = "SELECT ";
      $qNomCli .= "if($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF($cAlfa.SIAI0150.CLIAPE1X  != \"\",CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X),\"TERCERO SIN NOMBRE\")) AS clinomxx, ";
      $qNomCli .= "$cAlfa.SIAI0150.CLITELXX ";
      $qNomCli .= "FROM $cAlfa.SIAI0150 ";
      $qNomCli .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"$gTerId\" LIMIT 0,1";
      $xNomCli = f_MySql("SELECT","",$qNomCli,$xConexion01,"");
      $vNomCli = mysql_fetch_array($xNomCli);
      //f_Mensaje(__FILE__,__LINE__,$qNomCli."~".mysql_num_rows($xNomCli));
      # Fin Traigo el Nombre del Cliente

      #Buscando solo los saldos de cartera
      $qSaldos  = "SELECT pucidxxx ";
      $qSaldos .= "FROM $cAlfa.fpar0119 ";
      $qSaldos .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
      $qSaldos .= "WHERE ";
      $qSaldos .= "$cAlfa.fpar0119.ctoclaxf IN (\"SCLIENTE\",\"SCLIENTEUSD\",\"SAGENCIA\",\"SAGENCIAIP\",\"SAGENCIAPCC\",\"SAGENCIAUSD\",\"SAGENCIAUSDIP\",\"SAGENCIAUSDPCC\") AND ";
      $qSaldos .= "$cAlfa.fpar0115.pucdetxx IN (\"C\",\"P\") AND ";
      $qSaldos .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
      $xSaldos  = f_MySql("SELECT","",$qSaldos,$xConexion01,"");
      $cPucSal  = "";
      while ($xRDS = mysql_fetch_array($xSaldos)){
        $cPucSal .= "\"{$xRDS['pucidxxx']}\",";
      }
      mysql_free_result($xSaldos);
      $cPucSal = substr($cPucSal, 0, -1);
      #Fin Buscando solo los saldos de cartera

      $cCuentas  = ""; $vCuentas = array();
      if ($cPucSal != "") {
        #Buscando cuentas por cobrar o por pagar
        $qCuentas  = "SELECT *, CONCAT(pucgruxx, pucctaxx, pucsctax, pucauxxx, pucsauxx) AS pucidxxx ";
        $qCuentas .= "FROM $cAlfa.fpar0115 ";
        $qCuentas .= "WHERE ";
        $qCuentas .= "CONCAT(pucgruxx, pucctaxx, pucsctax, pucauxxx, pucsauxx) IN ($cPucSal) AND ";
        $qCuentas .= "regestxx = \"ACTIVO\" ";
        $xCuentas  = f_MySql("SELECT","",$qCuentas,$xConexion01,"");
        while ($xRDS = mysql_fetch_array($xCuentas)){
          $cCuentas .= "\"{$xRDS['pucidxxx']}\",";
          $vCuentas["{$xRDS['pucidxxx']}"] = $xRDS;
        }
        mysql_free_result($xCuentas);
        $cCuentas = substr($cCuentas, 0, -1);
        #Fin Buscando cuentas por cobrar o por pagar
      }

      if ($cCuentas != "") {
        $mDatMov = array();
        for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {

          // Se realiza la consulta principal
          $qDatMov  = "SELECT ";
          $qDatMov .= "$cAlfa.fcod$nAno.comidcxx, ";
          $qDatMov .= "$cAlfa.fcod$nAno.comcodcx, ";
          $qDatMov .= "$cAlfa.fcod$nAno.comcsccx, ";
          $qDatMov .= "$cAlfa.fcod$nAno.teridxxx, ";
          $qDatMov .= "$cAlfa.fcod$nAno.pucidxxx, ";
          $qDatMov .= "GROUP_CONCAT(CONCAT($cAlfa.fcod$nAno.comidxxx,\"-\",$cAlfa.fcod$nAno.comcodxx,\"-\",$cAlfa.fcod$nAno.comcscxx,\"~\",$cAlfa.fcod$nAno.comfecxx,\"~\",$cAlfa.fcod$nAno.comfecve,\"~\",$cAlfa.fcoc$nAno.comfefac) ORDER BY $cAlfa.fcod$nAno.comfecxx) AS fechasxx, ";
          $qDatMov .= "$cAlfa.fcod$nAno.commovxx, ";
          $qDatMov .= "SUM(if ($cAlfa.fcod$nAno.commovxx = \"D\", $cAlfa.fcod$nAno.comvlrxx, $cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx, ";
          $qDatMov .= "$cAlfa.fcod$nAno.regestxx,";
          $qDatMov .= "$cAlfa.fcoc$nAno.comidxxx,";
          $qDatMov .= "$cAlfa.fcoc$nAno.comcodxx,";
          $qDatMov .= "$cAlfa.fcoc$nAno.comcscxx,";
          $qDatMov .= "$cAlfa.fcoc$nAno.comcscxx,";
          $qDatMov .= "GROUP_CONCAT($cAlfa.fcoc$nAno.comfpxxx) AS comfpxxx, ";
          $qDatMov .= "$cAlfa.fcoc$nAno.comobsxx, ";
          $qDatMov .= "$cAlfa.fcoc$nAno.comobsxx, ";
  	      $qDatMov .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS clinomxx ";
          $qDatMov .= "FROM $cAlfa.fcod$nAno ";
          $qDatMov .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$nAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
          $qDatMov .= "LEFT JOIN  $cAlfa.fcoc$nAno ON ";
          $qDatMov .= "$cAlfa.fcod$nAno.comidxxx = $cAlfa.fcoc$nAno.comidxxx AND ";
          $qDatMov .= "$cAlfa.fcod$nAno.comcodxx = $cAlfa.fcoc$nAno.comcodxx AND ";
          $qDatMov .= "$cAlfa.fcod$nAno.comcscxx = $cAlfa.fcoc$nAno.comcscxx AND ";
          $qDatMov .= "$cAlfa.fcod$nAno.comcsc2x = $cAlfa.fcoc$nAno.comcsc2x ";
          $qDatMov .= "WHERE ";
          if ($gTerId != "") {
            $qDatMov .= "$cAlfa.fcod$nAno.teridxxx = \"$gTerId\" AND ";
          }
          $qDatMov .= "$cAlfa.fcod$nAno.comfecxx <= \"$cFechaActual\" AND ";
          $qDatMov .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" AND ";
          $qDatMov .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuentas)  ";
          $qDatMov .= "GROUP BY $cAlfa.fcod$nAno.comidcxx,$cAlfa.fcod$nAno.comcodcx,$cAlfa.fcod$nAno.comcsccx,$cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.pucidxxx ";
          $qDatMov .= "ORDER BY $cAlfa.fcod$nAno.comidcxx,$cAlfa.fcod$nAno.comcodcx,$cAlfa.fcod$nAno.comcsccx,$cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.pucidxxx ";
          $xDatMov  = f_MySql("SELECT","",$qDatMov,$xConexion01,"");
          // echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";
          // die();

          while ($xRDM = mysql_fetch_array($xDatMov)) {

            $xRDM['pucdetxx'] = $vCuentas["{$xRDM['pucidxxx']}"]['pucdetxx'];
            $xRDM['pucdesxx'] = $vCuentas["{$xRDM['pucidxxx']}"]['pucdesxx'];
  
            //Buscando la fecha del comprobante
            $mAuxFec = explode(",", $xRDM['fechasxx']);
            $dFecCre = ""; $dFecVen    = "";
  
            $nEncFec = 0;
            for ($nF=0; $nF<count($mAuxFec); $nF++) {
              if ($mAuxFec[$nF] != "") {
                $mAuxCom = array();
                $mAuxCom = explode("~", $mAuxFec[$nF]);
                $dFecCre = $mAuxCom[1];
                $dFecVen = $mAuxCom[2];
                $vAuxId = explode("-",$mAuxCom[0]);
  
                if ($vAuxId[0] == "S" || $mAuxCom[0] == $xRDM['comidcxx']."-".$xRDM['comcodcx']."-".$xRDM['comcsccx']) {
                  $nEncFec = 1;
                  //Encontro fecha comprobante
                  $xRDM['comfecxx'] = $mAuxCom[1];
                  $xRDM['comfecve'] = $mAuxCom[2];
                  $xRDM['comfefac'] = $mAuxCom[3];
                  $nF = count($mAuxFec);
                }
              }
            }
    
            $nDias = 0;
            $cKey = $xRDM['comidcxx']."-".$xRDM['comcodcx']."-".$xRDM['comcsccx']."-".$xRDM['teridxxx']."-".$xRDM['pucidxxx'];
            if($mDatMov[$cKey]['comidcxx'] == '') {
  
              $mDatMov[$cKey]['comidcxx']  = $xRDM['comidcxx'];
              $mDatMov[$cKey]['comcodcx']  = $xRDM['comcodcx'];
              $mDatMov[$cKey]['comcsccx']  = $xRDM['comcsccx'];
              $mDatMov[$cKey]['teridxxx']  = $xRDM['teridxxx'];
              $mDatMov[$cKey]['clinomxx']  = $xRDM['clinomxx'];
              $mDatMov[$cKey]['pucidxxx']  = $xRDM['pucidxxx'];
              $mDatMov[$cKey]['pucdetxx']  = $xRDM['pucdetxx'];
              $mDatMov[$cKey]['pucdesxx']  = $xRDM['pucdesxx'];
              $mDatMov[$cKey]['commovxx']  = $xRDM['commovxx'];
              $mDatMov[$cKey]['comfpxxx']  = trim($xRDM['comfpxxx'],",");
              $mDatMov[$cKey]['comobs2x']  = $xRDM['comobs2x'];
              $mDatMov[$cKey]['comobsxx']  = $xRDM['comobsxx'];
              $mDatMov[$cKey]['regestxx']  = $xRDM['regestxx'];
            }
  
            if ($nEncFec == 1) {
              $mDatMov[$cKey]['comfecin']  = $xRDM['comfecxx'];
              $mDatMov[$cKey]['comfefac']  = ($xRDM['comfefac'] != "") ? $xRDM['comfefac'] : "0000-00-00";
              $mDatMov[$cKey]['comfecxx']  = ($xRDM['comfecxx'] != "") ? $xRDM['comfecxx'] : $dFecCre;
              $mDatMov[$cKey]['comfecve']  = ($xRDM['comfecve'] != "") ? $xRDM['comfecve'] : $dFecVen;
            }
            $mDatMov[$cKey]['saldoxxx'] += $xRDM['comvlrxx'];
          }
        }

        /**
         * Instanciando Objetos para Guardar Regristros en la Tabla Temporal.
         * 
         */
        $objEstructurasEstadoCuentaGeneral = new cEstructurasEstadoCuentaGeneral();
        $mReturnCrearReporte 			         = $objEstructurasEstadoCuentaGeneral->fnCrearEstructurasEstadoCuentaGeneral();

        if($mReturnCrearReporte[0] == "false"){
          $nSwitch = 1;
          for($nR=2;$nR<count($mReturnCrearReporte);$nR++){
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .=  $mReturnCrearReporte[$nR]."\n";
          }
        }

        $qInsDet = '';
        $nCanReg01 = 0;
        $nSwitch_Reporte = 0;

        // Declaro el INSERT para la tabal temporal
        $qInsCab  = "INSERT INTO $cAlfa.$mReturnCrearReporte[1] (";
        $qInsCab .= "comidxxx, ";
        $qInsCab .= "comcodxx, ";
        $qInsCab .= "comcscxx, ";
        $qInsCab .= "document, ";
        $qInsCab .= "comfecin, ";
        $qInsCab .= "comfecxx, ";
        $qInsCab .= "comfecve, ";
        $qInsCab .= "comfecnx, ";
        $qInsCab .= "comfecvn, ";
        $qInsCab .= "diascart, ";
        $qInsCab .= "diasvenc, ";
        $qInsCab .= "teridxxx, ";
        $qInsCab .= "clinomxx, ";
        $qInsCab .= "pucidxxx, ";
        $qInsCab .= "pucdesxx, ";
        $qInsCab .= "commovxx, ";
        $qInsCab .= "saldoxxx, ";
        $qInsCab .= "regestxx, ";
        $qInsCab .= "pedidoxx, ";
        $qInsCab .= "docidxxx, ";
        $qInsCab .= "sucidxxx, ";
        $qInsCab .= "ctipoxxx, ";
        $qInsCab .= "comobsxx) VALUES ";
        // FIN Declaro el INSERT para la tabla temporal

        //// Empiezo a Recorrer la Matriz de Creditos Vs Debitos para Dejar las Cuentas que a la fecha de Corte tienen Saldo ////
        $mCarteraVencida   = array(); //Cartera que tiene uno o mas dias de vencimiento
        $mCarteraSinVencer = array(); //Cartera que no se ha vencido
        $mSaldosaFavor     = array(); //Saldos a Favor del Cliente, valores negativos
        foreach ($mDatMov as $key => $cValue) {
          if ($mDatMov[$key]['saldoxxx'] != 0) {
    
            //Fechas de vencimeinto de SIACO, se calcula con la fecha de entrega de factura al cliente
            if (($cAlfa == "SIACOSIA" || $cAlfa == "TESIACOSIP" || $cAlfa == "DESIACOSIP") && $mDatMov[$key]['comidcxx'] == "F") {
              //Buscando Pedido
              if ($mDatMov[$key]['comobs2x'] != "") {
                $vAuxPed = explode("~",$mDatMov[$key]['comobs2x']);
                $mDatMov[$key]['pedidoxx'] = $vAuxPed[8];
              }
              //Calculando cuantos dias son para el vencimiento
              $dComFec  = str_replace("-","",$mDatMov[$key]['comfecxx']);
              $dConFeVe = str_replace("-","",$mDatMov[$key]['comfecve']);
              $nDias    = round((mktime(0,0,0,substr($dConFeVe,4,2),substr($dConFeVe,6,2), substr($dConFeVe,0,4))  - mktime(0,0,0,substr($dComFec,4,2), substr($dComFec,6,2),  substr($dComFec,0,4))) / (60 * 60 * 24));
  
              if ($mDatMov[$key]['comfefac'] == "0000-00-00") {
                $mDatMov[$key]['comfecnx'] = $mDatMov[$key]['comfecxx'];
                $mDatMov[$key]['comfecvn'] = $mDatMov[$key]['comfecve'];
              } else {
                $mDatMov[$key]['comfecnx'] = $mDatMov[$key]['comfefac'];
                $dConFeVe = str_replace("-","",$mDatMov[$key]['comfefac']);
                $mDatMov[$key]['comfecvn'] = date("Y-m-d",mktime(0,0,0,substr($dConFeVe,4,2),substr($dConFeVe,6,2)+$nDias, substr($dConFeVe,0,4)));
              }

              $dComFecVe = $mDatMov[$key]['comfecvn'];
              $dComFec   = $mDatMov[$key]['comfecnx'];

            } else {
              $mDatMov[$key]['comfecvn'] = $mDatMov[$key]['comfecxx'];
              $mDatMov[$key]['comfecvn'] = $mDatMov[$key]['comfecve'];
              $dComFecVe = $mDatMov[$key]['comfecvn'];
              $dComFec   = $mDatMov[$key]['comfecxx'];
            }

            $valorVen = 0;
            $valorCar = '';
            $valorVen = '';

            if ($dComFecVe != "0000-00-00" && $dComFec != "0000-00-00") {
              $dFecCor = date('Ymd');
              $dFecCar = str_replace("-","",$dComFec);
              $dFecVen = str_replace("-","",$dComFecVe);
              $dateVen = mktime(0,0,0,substr($dFecVen,4,2), substr($dFecVen,6,2), substr($dFecVen,0,4));
  
              $dateCar = mktime(0,0,0,substr($dFecCar,4,2), substr($dFecCar,6,2), substr($dFecCar,0,4));
              $valorCar= round(($dateVen  - $dateCar) / (60 * 60 * 24));
  
              $dateCor = mktime(0,0,0,substr($dFecCor,4,2), substr($dFecCor,6,2), substr($dFecCor,0,4));
              $valorVen= round(($dateCor  - $dateVen) / (60 * 60 * 24));
            }

            $mDatMov[$key]['commovxx'] = ($mDatMov[$key]['saldoxxx'] > 0) ? "D" : "C";
            $cDocument = $mDatMov[$key]['comidcxx']."-".$mDatMov[$key]['comcodcx']."-".$mDatMov[$key]['comcsccx'];

            if ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') {
              // si aplica el tercer consecutivo busco el comcsc3x
              for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {
                $qDatMov  = "SELECT ";
                $qDatMov .= "$cAlfa.fcoc$nAno.comcsc3x, ";
                $qDatMov .= "$cAlfa.fcoc$nAno.comcsc2x ";
                $qDatMov .= "FROM $cAlfa.fcoc$nAno ";
                $qDatMov .= "WHERE ";
                $qDatMov .= "($cAlfa.fcoc$nAno.comidxxx = \"{$mDatMov[$key]['comidcxx']}\" OR $cAlfa.fcoc$nAno.comidxxx =\"S\" ) AND ";
                $qDatMov .= "$cAlfa.fcoc$nAno.comidcxx = \"{$mDatMov[$key]['comidcxx']}\" AND ";
                $qDatMov .= "$cAlfa.fcoc$nAno.comcodcx = \"{$mDatMov[$key]['comcodcx']}\" AND ";
                $qDatMov .= "$cAlfa.fcoc$nAno.comcsccx = \"{$mDatMov[$key]['comcsccx']}\" AND ";
                $qDatMov .= "$cAlfa.fcoc$nAno.teridxxx = \"{$mDatMov[$key]['teridxxx']}\" AND ";
                $qDatMov .= "$cAlfa.fcoc$nAno.pucidxxx = \"{$mDatMov[$key]['pucidxxx']}\" LIMIT 0,1";
                $xDatMov  = f_MySql("SELECT","",$qDatMov,$xConexion01,"");
                // echo $qDatMov."~".mysql_num_rows($xDatMov)."<br><br>";
                // die();

                if (mysql_num_rows($xDatMov) > 0 ) {
                  $vCre = mysql_fetch_array($xDatMov);
                  $vCre['comcsc3x'] = ($vCre['comcsc3x'] != '') ? $vCre['comcsc3x'] : $vCre['comcsc2x'];
                  $cDocument = $mDatMov[$key]['comidcxx']."-".$mDatMov[$key]['comcodcx']."-".$mDatMov[$key]['comcsccx']."-".$vCre['comcsc3x'];
                  $nAno = $AnoFin + 1;
                }
              }
            }

            $cDocId  = "";  $cDocSuf = "";  $cDocSuc = "";
            $cSucIdxx= "";  $cDocIdxx= "";
            if ($mDatMov[$key]['comidcxx'] == "F") {
              $mDoiId = explode("|",$mDatMov[$key]['comfpxxx']);
              for ($i=0;$i<count($mDoiId);$i++) {
                if($mDoiId[$i] != ""){
                  $vDoiId  = explode("~",$mDoiId[$i]);
                  if($cDocId == "") {
                    $cDocId  = $vDoiId[2];
                    $cDocSuf = $vDoiId[3];
                    $cDocSuc = $vDoiId[15];
                  }
                }
              }

              $vDceDat = array();
              $qDceDat  = "SELECT ";
              $qDceDat .= "$cAlfa.sys00121.docidxxx, ";
              $qDceDat .= "$cAlfa.sys00121.cliidxxx, ";
              $qDceDat .= "$cAlfa.sys00121.sucidxxx, ";
              $qDceDat .= "$cAlfa.sys00121.docpedxx, ";
              $qDceDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS CLINOMXX ";
              $qDceDat .= "FROM $cAlfa.sys00121 ";
              $qDceDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.sys00121.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
              $qDceDat .= "WHERE ";
              $qDceDat .= "$cAlfa.sys00121.sucidxxx = \"$cDocSuc\" AND ";
              $qDceDat .= "$cAlfa.sys00121.docidxxx = \"$cDocId\" AND ";
              $qDceDat .= "$cAlfa.sys00121.docsufxx = \"$cDocSuf\" ";
              $xDceDat  = f_MySql("SELECT","",$qDceDat,$xConexion01,"");
              if (mysql_num_rows($xDceDat) > 0) {
                $vDceDat = mysql_fetch_array($xDceDat);

                $cDocIdxx = $vDceDat['docidxxx'];
                $cSucIdxx = $vDceDat['sucidxxx'];
                $mDatMov[$key]['pedidoxx'] = $vDceDat['docpedxx'];
                $mDatMov[$key]['clinomxx'] = $vDceDat['CLINOMXX'];
              }
            }

            if ($mDatMov[$key]['saldoxxx'] < 0 || $mDatMov[$key]['pucdetxx'] == "P") { //es un saldo a favor del cliente
              $cTipoxxx = "SALDOS A FAVOR";
              $valorVen = 0;
            } else if ($valorVen > 0) { //Cartera vencida
              $cTipoxxx = "CARTERA VENCIDA";
            } else { //Cartera no vencida
              $cTipoxxx = "CARTERA NO VENCIDA";
            }
            $nSaldoxx = abs($mDatMov[$key]['saldoxxx']);

            //Almaceno los VALUES del INSERT
            $qInsDet .= "(\"{$mDatMov[$key]['comidcxx']}\",";
            $qInsDet .= "\"{$mDatMov[$key]['comcodcx']}\",";
            $qInsDet .= "\"{$mDatMov[$key]['comcsccx']}\",";
            $qInsDet .= "\"$cDocument\",";
            $qInsDet .= "\"{$mDatMov[$key]['comfecin']}\",";
            $qInsDet .= "\"{$mDatMov[$key]['comfecxx']}\",";
            $qInsDet .= "\"{$mDatMov[$key]['comfecve']}\",";
            $qInsDet .= "\"{$mDatMov[$key]['comfecnx']}\",";
            $qInsDet .= "\"{$mDatMov[$key]['comfecvn']}\",";
            $qInsDet .= "\"$valorCar\",";
            $qInsDet .= "\"$valorVen\",";
            $qInsDet .= "\"{$mDatMov[$key]['teridxxx']}\",";
            $qInsDet .= "\"{$mDatMov[$key]['clinomxx']}\",";
            $qInsDet .= "\"{$mDatMov[$key]['pucidxxx']}\",";
            $qInsDet .= "\"{$mDatMov[$key]['pucdesxx']}\",";
            $qInsDet .= "\"{$mDatMov[$key]['commovxx']}\",";
            $qInsDet .= "\"$nSaldoxx\",";
            $qInsDet .= "\"{$mDatMov[$key]['regestxx']}\",";
            $qInsDet .= "\"{$mDatMov[$key]['pedidoxx']}\",";
            $qInsDet .= "\"$cDocIdxx\",";
            $qInsDet .= "\"$cSucIdxx\",";
            $qInsDet .= "\"$cTipoxxx\",";
            $qInsDet .= "\"{$mDatMov[$key]['comobsxx']}\"),";

            //Realizo el INSERT a la tabla temporal - Acumulo la cantidad de registros para reiniciar la conexion
            $nCanReg01++;
            if (($nCanReg01 % _NUMREG_) == 0) {

              $xConexion01 = $objEstructurasEstadoCuentaGeneral->fnReiniciarConexionDBEstadoCuentaGeneral($xConexion01);

              /**
               * Insertando Registros
               */
              $qInsDet = substr($qInsDet, 0, -1);
              $qInsDet = $qInsCab.$qInsDet;				
              if(!mysql_query($qInsDet,$xConexion01)) {
                $nSwitch_Reporte = 1;
                f_Mensaje(__FILE__,__LINE__, "Error Al Insertar Registro a la Tabla Temporal");
              }
              $qInsDet = "";        
            }
          }
        }

        if($nSwitch_Reporte == 0 && $qInsDet != ""){

          $xConexion01 = $objEstructurasEstadoCuentaGeneral->fnReiniciarConexionDBEstadoCuentaGeneral($xConexion01);
  
          $qInsDet = substr($qInsDet, 0, -1);
          $qInsDet = $qInsCab.$qInsDet;
          // f_Mensaje(__FILE__,__LINE__,$qInsDet);
          if(!mysql_query($qInsDet,$xConexion01)) {
            $nSwitch_Reporte = 1;
          }
        }
        
        if($nSwitch_Reporte == 1){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
          $cMsj .= "Error Guardando en la Tabla Temporal 1.\n";
        }

        //// Fin Recorrer la Matriz de Creditos-Debitos para Dejar las Cuentas que a la fecha de Corte tienen Saldo ////
        /////FIN DE CALCULOS PARA ARMAR EL ARCHIVO /////
      }

      $qTabTemp  = "SELECT * ";
      $qTabTemp .= "FROM $cAlfa.$mReturnCrearReporte[1] ";
      $qTabTemp .= "WHERE ";
      if ($gTerId != "") {
        $qTabTemp .= "teridxxx = \"$gTerId\" AND ";
      }
      if ($gDocNro != "") {
        $qTabTemp .= "docidxxx = \"$gDocNro\" AND ";
      }
      if ($gCcoId != "") {
        $qTabTemp .= "sucidxxx = \"$gCcoId\" AND ";
      }
      if ($gPedido != "") {
        $qTabTemp .= "pedidoxx = \"$gPedido\" AND ";
      }
      if ($gDesde != "" && $gHasta != ""){
        $qTabTemp .= "comfecxx BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
      }
      $qTabTemp .= "regestxx = \"ACTIVO\" ";
      $xTabTemp = f_MySql("SELECT","",$qTabTemp,$xConexion01,"");
      // echo $qTabTemp." ~ ". mysql_num_rows($xTabTemp);
      // die();

      while ($xRTP = mysql_fetch_array($xTabTemp)) {
        if ($xRTP['ctipoxxx'] == "SALDOS A FAVOR") { //es un saldo a favor del cliente
          $nInd_mSaldosaFavor = count($mSaldosaFavor);
          $mSaldosaFavor[$nInd_mSaldosaFavor]['teridxxx'] = $xRTP['teridxxx'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['clinomxx'] = $xRTP['clinomxx'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['docidxxx'] = $xRTP['docidxxx'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['pedidoxx'] = $xRTP['pedidoxx'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['comobsxx'] = $xRTP['comobsxx'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['comidxxx'] = $xRTP['comidxxx'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['comcodxx'] = $xRTP['comcodxx'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['comcscxx'] = $xRTP['comcscxx'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['comfecin'] = $xRTP['comfecin'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['regestxx'] = $xRTP['regestxx'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['document'] = $xRTP['document'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['pucidxxx'] = $xRTP['pucidxxx'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['comfecxx'] = $xRTP['comfecxx'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['comfecnx'] = $xRTP['comfecnx'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['comfecvn'] = $xRTP['comfecvn'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['diascart'] = number_format($xRTP['diascart'], 0);
          $mSaldosaFavor[$nInd_mSaldosaFavor]['diasvenc'] = number_format($xRTP['diasvenc'], 0);
          $mSaldosaFavor[$nInd_mSaldosaFavor]['commovxx'] = $xRTP['commovxx'];
          $mSaldosaFavor[$nInd_mSaldosaFavor]['saldoxxx'] = $xRTP['saldoxxx'];
        } else if ($xRTP['ctipoxxx'] == "CARTERA VENCIDA") { //Cartera vencida
          $nInd_mCarteraVencida = count($mCarteraVencida);
          $mCarteraVencida[$nInd_mCarteraVencida]['teridxxx'] = $xRTP['teridxxx'];
          $mCarteraVencida[$nInd_mCarteraVencida]['clinomxx'] = $xRTP['clinomxx'];
          $mCarteraVencida[$nInd_mCarteraVencida]['docidxxx'] = $xRTP['docidxxx'];
          $mCarteraVencida[$nInd_mCarteraVencida]['pedidoxx'] = $xRTP['pedidoxx'];
          $mCarteraVencida[$nInd_mCarteraVencida]['comobsxx'] = $xRTP['comobsxx'];
          $mCarteraVencida[$nInd_mCarteraVencida]['comidxxx'] = $xRTP['comidxxx'];
          $mCarteraVencida[$nInd_mCarteraVencida]['comcodxx'] = $xRTP['comcodxx'];
          $mCarteraVencida[$nInd_mCarteraVencida]['comcscxx'] = $xRTP['comcscxx'];
          $mCarteraVencida[$nInd_mCarteraVencida]['comfecin'] = $xRTP['comfecin'];
          $mCarteraVencida[$nInd_mCarteraVencida]['regestxx'] = $xRTP['regestxx'];
          $mCarteraVencida[$nInd_mCarteraVencida]['document'] = $xRTP['document'];
          $mCarteraVencida[$nInd_mCarteraVencida]['pucidxxx'] = $xRTP['pucidxxx'];
          $mCarteraVencida[$nInd_mCarteraVencida]['comfecxx'] = $xRTP['comfecxx'];
          $mCarteraVencida[$nInd_mCarteraVencida]['comfecnx'] = $xRTP['comfecnx'];
          $mCarteraVencida[$nInd_mCarteraVencida]['comfecvn'] = $xRTP['comfecvn'];
          $mCarteraVencida[$nInd_mCarteraVencida]['diascart'] = number_format($xRTP['diascart'], 0);
          $mCarteraVencida[$nInd_mCarteraVencida]['diasvenc'] = number_format($xRTP['diasvenc'], 0);
          $mCarteraVencida[$nInd_mCarteraVencida]['commovxx'] = $xRTP['commovxx'];
          $mCarteraVencida[$nInd_mCarteraVencida]['saldoxxx'] = $xRTP['saldoxxx'];
        } else if ($xRTP['ctipoxxx'] == "CARTERA NO VENCIDA") { //Cartera no vencida
          $nInd_mCarteraSinVencer = count($mCarteraSinVencer);
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['teridxxx'] = $xRTP['teridxxx'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['clinomxx'] = $xRTP['clinomxx'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['docidxxx'] = $xRTP['docidxxx'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['pedidoxx'] = $xRTP['pedidoxx'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comobsxx'] = $xRTP['comobsxx'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comidxxx'] = $xRTP['comidxxx'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comcodxx'] = $xRTP['comcodxx'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comcscxx'] = $xRTP['comcscxx'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comfecin'] = $xRTP['comfecin'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['regestxx'] = $xRTP['regestxx'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['document'] = $xRTP['document'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['pucidxxx'] = $xRTP['pucidxxx'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comfecxx'] = $xRTP['comfecxx'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comfecnx'] = $xRTP['comfecnx'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['comfecvn'] = $xRTP['comfecvn'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['diascart'] = number_format($xRTP['diascart'], 0);
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['diasvenc'] = number_format($xRTP['diasvenc'], 0);
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['commovxx'] = $xRTP['commovxx'];
          $mCarteraSinVencer[$nInd_mCarteraSinVencer]['saldoxxx'] = $xRTP['saldoxxx'];
        }
      }

      ///Recibos Provisionales
      $mRecProv = array();
      if ($gDocNro == "" && $gCcoId == "" && $gPedido == "") {
        for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {
          $qProvCab  = "SELECT ";
          $qProvCab .= "$cAlfa.fcoc$nAno.comidxxx, ";
          $qProvCab .= "$cAlfa.fcoc$nAno.comcodxx, ";
          $qProvCab .= "$cAlfa.fcoc$nAno.comcscxx, ";
          $qProvCab .= "$cAlfa.fcoc$nAno.comcsc2x, ";
          $qProvCab .= "$cAlfa.fcoc$nAno.comcsc3x, ";
          $qProvCab .= "$cAlfa.fcoc$nAno.teridxxx, ";
          $qProvCab .= "$cAlfa.fcoc$nAno.comfecxx, ";
          $qProvCab .= "$cAlfa.fcoc$nAno.comfecve, ";
          $qProvCab .= "$cAlfa.fcoc$nAno.comvlr01,  ";
          $qProvCab .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,\"CLIENTE SIN NOMBRE\") AS clinomxx ";
          $qProvCab .= "FROM $cAlfa.fcoc$nAno ";
          $qProvCab .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$nAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
          $qProvCab .= "WHERE ";
          $qProvCab .= "$cAlfa.fcoc$nAno.comidxxx != \"F\" AND ";
          if ($gTerId != "") {
            $qProvCab .= "$cAlfa.fcoc$nAno.teridxxx = \"$gTerId\" AND ";
          }
          if ($gDesde != "" && $gHasta != ""){
            $qProvCab .= "comfecxx BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
          }
          $qProvCab .= "$cAlfa.fcoc$nAno.regestxx = \"PROVISIONAL\" ";
          $xProvCab = f_MySql("SELECT","",$qProvCab,$xConexion01,"");
    
          while ($xRPC = mysql_fetch_array($xProvCab)) {
            if ($xRPC['comvlr01'] > 0) {
              if ( $vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $xRPC['comcsc3x'] == '' ) {
                $xRPC['comcsc3x'] = $xRPC['comcsc2x'];
              }
              $nInd_mRecProv = count($mRecProv);
              $mRecProv[$nInd_mRecProv]['comidxxx']=$xRPC['comidxxx'];
              $mRecProv[$nInd_mRecProv]['comcodxx']=$xRPC['comcodxx'];
              $mRecProv[$nInd_mRecProv]['comcscxx']=$xRPC['comcscxx'];
              $mRecProv[$nInd_mRecProv]['comfecin']=$xRPC['comfecxx'];
              $mRecProv[$nInd_mRecProv]['comfecxx']=$xRPC['comfecxx'];
              $mRecProv[$nInd_mRecProv]['document']= ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') ? $xRPC['comidxxx']."-".$xRPC['comcodxx']."-".$xRPC['comcscxx']."-".$xRPC['comcsc3x'] : $xRPC['comidxxx']."-".$xRPC['comcodxx']."-".$xRPC['comcscxx'];
              $mRecProv[$nInd_mRecProv]['comfecvn']=$xRPC['comfecve'];
              $mRecProv[$nInd_mRecProv]['diascart']="";
              $mRecProv[$nInd_mRecProv]['diasvenc']="";
              $mRecProv[$nInd_mRecProv]['teridxxx']=$xRPC['teridxxx'];
              $mRecProv[$nInd_mRecProv]['clinomxx']=$xRPC['clinomxx'];
              $mRecProv[$nInd_mRecProv]['commovxx']=($xRPC['comvlr01'] > 0) ? "D" : "C";
              $mRecProv[$nInd_mRecProv]['saldoxxx']=abs($xRPC['comvlr01']);
            }
          }
        }
      }
      //Recibos Provisionales

      $mSaldosaFavor     = f_ordenar_array_bidimensional($mSaldosaFavor,'diasvenc',SORT_DESC,'comfecxx',SORT_ASC,'document',SORT_ASC);
      $mCarteraVencida   = f_ordenar_array_bidimensional($mCarteraVencida,'diasvenc',SORT_DESC,'comfecxx',SORT_ASC,'document',SORT_ASC);
      $mCarteraSinVencer = f_ordenar_array_bidimensional($mCarteraSinVencer,'diasvenc',SORT_DESC,'comfecxx',SORT_ASC,'document',SORT_ASC);
      $mRecProv          = f_ordenar_array_bidimensional($mRecProv,'comfecxx',SORT_ASC,'document',SORT_ASC);

      switch ($cTipo) {
        case 1: // PINTA POR PANTALLA//
          ?>
          <script language="javascript">
            function f_Ver(xComId,xComCod,xComCsc,xComFec,xRegEst,xTipCom) {
  
              var xComId  = xComId;
              var xComCod = xComCod;
              var xComCsc = xComCsc;
              var xComFec = xComFec;
              var xRegEst = xRegEst;
              var xTipCom = xTipCom;
  
              var ruta  = "frvercom.php?xComId="+xComId+"&xComCod="+xComCod+"&xComCsc="+xComCsc+"&xComFec="+xComFec+"&xRegEst="+xRegEst+"&xTipCom="+xTipCom;
  
              //document.location = ruta; // Invoco el menu.
              var zX    = screen.width;
              var zY    = screen.height;
              var zNx     = (zX-550)/2;
              var zNy     = (zY-350)/2;
              var zWinPro = 'width=550,scrollbars=1,height=350,left='+zNx+',top='+zNy;
              //var cNomVen = 'zWindowcom';
              var cNomVen = 'zWindow'+Math.ceil(Math.random()*1000);
              zWindow = window.open(ruta,cNomVen,zWinPro);
              zWindow.focus();
            }
          </script>
          <html>
            <title>Reporte Estado de Cuenta General</title>
            <head>
              <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
              <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
              <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
              <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
              <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
              <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
              <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
            </head>
            <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
              <?php
              $nCol = 13;
              ?>
              <center>
                <table border = "1" cellpadding = "0" cellspacing = "0" width = "98%">
                  <tr>
                    <?php
                    switch ($cAlfa) {
                      case 'ADUANAMO':
                      case 'DEADUANAMO':
                      case 'TEADUANAMO': ?>
                        <td class="name" style="font-size:14px;width:120px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logo_aduanamo.png">
                        </td>
                      <?php break;
                      case 'SIACOSIA':
                      case 'TESIACOSIP':
                      case 'DESIACOSIP': ?>
                        <td class="name" style="font-size:14px;width:70px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logo_repcar.jpg" style="height: 50px;width:70">
                        </td>
                      <?php break;
                      case 'LOGINCAR':
                      case 'TELOGINCAR':
                      case 'DELOGINCAR': ?>
                        <td class="name" style="font-size:14px;width:70px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/Logo_Login_Cargo_Ltda_2.jpg" style="height: 41px;width:156">
                        </td>
                      <?php break;
                      case "ROLDANLO"://ROLDAN
                      case "TEROLDANLO"://ROLDAN
                      case "DEROLDANLO"://ROLDAN ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoroldan.png" style="height: 41px;width:156">
                        </td>
                      <?php break;
                      case "CASTANOX":
                      case "DECASTANOX":
                      case "TECASTANOX": ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logomartcam.jpg.png" style="height: 41px;width:156">
                        </td>
                      <?php break;
                      case "ALMACAFE": //ALMACAFE
                      case "TEALMACAFE": //ALMACAFE
                      case "DEALMACAFE": //ALMACAFE ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoalmacafe.jpg.png" style="height: 41px;width:156">
                        </td>
                      <?php break;
                      case "GRUMALCO"://GRUMALCO
                      case "TEGRUMALCO"://GRUMALCO
                      case "DEGRUMALCO"://GRUMALCO?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logomalco.jpg" style="height: 55px;width:120">
                        </td>
                      <?php break;
                      case "ALADUANA"://ALADUANA
                      case "TEALADUANA"://ALADUANA
                      case "DEALADUANA":  ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoaladuana.jpg" style="height: 60px;width:120">
                        </td>
                      <?php break;
                      case "ANDINOSX"://ANDINOSX
                      case "TEANDINOSX"://ANDINOSX
                      case "DEANDINOSX"://ANDINOSX ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoAndinos2.jpeg" style="height: 60px;width:70; margin-left: 14px; ">
                        </td>
                      <?php break;
                      case "GRUPOALC"://GRUPOALC
                      case "TEGRUPOALC"://GRUPOALC
                      case "DEGRUPOALC"://GRUPOALC ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoalc.jpg" style="height: 60px;width:120">
                        </td>
                      <?php break;
                      case "AAINTERX"://AAINTERX
                      case "TEAAINTERX"://AAINTERX
                      case "DEAAINTERX":  ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logointernacional.jpg" style="height: 60px;width:120">
                        </td>
                      <?php break;
                      case "AALOPEZX":
                      case "TEAALOPEZX":
                      case "DEAALOPEZX": ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoaalopez.png" style="width:120">
                        </td>
                      <?php break;
                      case "ADUAMARX"://ADUAMARX
                      case "TEADUAMARX"://ADUAMARX
                      case "DEADUAMARX"://ADUAMARX ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logoaduamar.jpg" style="width:70">
                        </td>
                      <?php break;
                      case "SOLUCION"://SOLUCION
                      case "TESOLUCION"://SOLUCION
                      case "DESOLUCION"://SOLUCION ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logosoluciones.jpg" style="width:120">
                        </td>
                      <?php break;
                      case "FENIXSAS"://FENIXSAS
                      case "TEFENIXSAS"://FENIXSAS
                      case "DEFENIXSAS"://FENIXSAS ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logofenix.jpg" style="width:130">
                        </td>
                      <?php break;
                      case "COLVANXX"://COLVANXX
                      case "TECOLVANXX"://COLVANXX
                      case "DECOLVANXX"://COLVANXX ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logocolvan.jpg" style="width:130">
                        </td>
                      <?php break;
                      case "INTERLAC"://INTERLAC
                      case "TEINTERLAC"://INTERLAC
                      case "DEINTERLAC"://INTERLAC ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src = "<?php echo $cPlesk_Skin_Directory ?>/logointerlace.jpg" style="width:130">
                        </td>
                      <?php break;
                      case "KARGORUX": //KARGORUX
                      case "TEKARGORUX": //KARGORUX
                      case "DEKARGORUX": //KARGORUX ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/logokargoru.jpg" style="width:130;margin:10px;">
                        </td>
                      <?php break;
                      case "ALOGISAS": //LOGISTICA
                      case "TEALOGISAS": //LOGISTICA
                      case "DEALOGISAS": //LOGISTICA ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/logologisticasas.jpg" style="width:140;margin:5px;">
                        </td>
                      <?php break;
                      case "PROSERCO": //PROSERCO
                      case "TEPROSERCO": //PROSERCO
                      case "DEPROSERCO": //PROSERCO ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/logoproserco.png" style="width:140;margin:5px;">
                        </td>
                      <?php break;
                      case "MANATIAL": //MANATIAL
                      case "TEMANATIAL": //MANATIAL
                      case "DEMANATIAL": //MANATIAL?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/logomanantial.jpg" style="width:140;margin:5px;">
                        </td>
                      <?php break;
                      case "DSVSASXX":  //DSVSAS
                      case "DEDSVSASXX"://DSVSAS
                      case "TEDSVSASXX"://DSVSAS ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/logodsv.jpg" style="width:140;margin:5px;">
                        </td>
                      <?php break;
                      case "MELYAKXX":    //MELYAK
                      case "DEMELYAKXX":  //MELYAK
                      case "TEMELYAKXX":  //MELYAK ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/logomelyak.jpg" style="width:140;margin:5px;">
                        </td>
                      <?php	break;
                      case "FEDEXEXP":    //FEDEX
                      case "DEFEDEXEXP":  //FEDEX
                      case "TEFEDEXEXP":  //FEDEX ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/logofedexexp.jpg" style="width:140;margin:5px;">
                        </td>
                      <?php	break;
                      case "EXPORCOM":    //EXPORCOMEX
                      case "DEEXPORCOM":  //EXPORCOMEX
                      case "TEEXPORCOM":  //EXPORCOMEX ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/logoexporcomex.jpg" style="width:140;margin:5px;">
                        </td>
                      <?php	break;
                      case "HAYDEARX":    //HAYDEARX
                      case "DEHAYDEARX":  //HAYDEARX
                      case "TEHAYDEARX":  //HAYDEARX ?>
                        <td class="name" style="font-size:14px;width:100px">
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/logohaydear.jpeg" style="width:200;margin:5px;">
                        </td>
                      <?php	break;
                      case "CONNECTA":    //CONNECTA
                      case "DECONNECTA":  //CONNECTA
                      case "TECONNECTA":  //CONNECTA ?>
                        <td class="name" style="font-size:14px;width:80px">
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/logoconnecta.jpg" style="width:200;margin:5px;">
                        </td>
                      <?php	break;
                      case "OPENEBCO":    //OPENEBCO
                      case "DEOPENEBCO":  //OPENEBCO
                      case "TEOPENEBCO":  //OPENEBCO ?>
                        <td class="name" style="font-size:14px;width:80px">
                          <img src="<?php echo $cPlesk_Skin_Directory ?>/opentecnologia.JPG" style="width:200;margin:5px;">
                        </td>
                      <?php	break;
                    }?>
                    <td class="name" style="font-size:14px">
                      <center><br><span style="font-size:18px"> <?php echo "REPORTE DE ESTADO DE CUENTA GENERAL DEL ". $cFecha ?></span></center><br>
                    </td>
                  </tr>
                </table>
                <table border = "1" cellpadding = "0" cellspacing = "0" width = "98%">
                  <tr>
                    <td align="left" colspan = "8" bgcolor = "#96ADEB" style="font-size:14px">
                      <b>CONSULTA POR CLIENTE</b>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" bgcolor = "#D6DFF7"><b>NIT</b></td>
                    <td align="center"><b><?php echo $gTerId."-".f_Digito_Verificacion($gTerId)?></b></td>
                    <td align="center" bgcolor = "#D6DFF7"><b>CLIENTE</b></td>
                    <td align="center"><b><?php echo $vNomCli['clinomxx']?></b></td>
                    <td align="center" bgcolor = "#D6DFF7"><b>TEL&Eacute;FONO</b></td>
                    <td align="center"><b><?php echo $vNomCli['CLITELXX']?></b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:200px"><b>FECHA Y HORA DE CONSULTA</b></td>
                    <td align="center"><b><?php echo date("Y-m-d H:i:s");?></b></td>
                  </tr>
                </table>
                <br>
                <table border = "1" cellpadding = "0" cellspacing = "0" width = "98%">
                  <tr>
                    <td align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>CARTERA VENCIDA</b></td>
                  </tr>
                  <tr>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Importador</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>DO</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Pedido</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Observaci&oacute;n</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Comprobante</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Cuenta</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Entrega Cliente</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>
                  </tr>
                  <?php for($i=0;$i<count($mCarteraVencida);$i++){ 
                    if($mRecProv[$i]['comidxxx'] == 'P' || $mRecProv[$i]['comidxxx'] == 'L' || $mRecProv[$i]['comidxxx'] == 'C'){
                      $cTipCom = $mComP[$mRecProv[$i]['comidxxx']][$mRecProv[$i]['comcodxx']];
  
                      if (in_array("{$mRecProv[$i]['comidxxx']}~{$mRecProv[$i]['comcodxx']}", $mRCM) == true) {
                        $cTipCom = "RCM";
                      }
                    }else{
                      $cTipCom = "";
                    } ?>
                    <tr>
                      <td align="left"><?php echo ($mCarteraVencida[$i]['clinomxx'] != "") ? $mCarteraVencida[$i]['clinomxx'] : "&nbsp;" ?></td>
                      <td align="Center"><?php echo ($mCarteraVencida[$i]['docidxxx'] != "") ? $mCarteraVencida[$i]['docidxxx'] : "&nbsp;" ?></td>
                      <td align="Center"><?php echo ($mCarteraVencida[$i]['pedidoxx'] != "") ? $mCarteraVencida[$i]['pedidoxx'] : "&nbsp;" ?></td>
                      <td align="left"><?php echo ($mCarteraVencida[$i]['comobsxx'] != "") ? $mCarteraVencida[$i]['comobsxx'] : "&nbsp;" ?></td>
                      <td align="left"><?php echo ($mCarteraVencida[$i]['document'] != "") ? "<a href=\"javascript:f_Ver('{$mCarteraVencida[$i]['comidxxx']}','{$mCarteraVencida[$i]['comcodxx']}','{$mCarteraVencida[$i]['comcscxx']}','{$mCarteraVencida[$i]['comfecin']}','{$mCarteraVencida[$i]['regestxx']}','$cTipCom');\">{$mCarteraVencida[$i]['document']}</a>": "&nbsp;"; ?></td>
                      <td align="center"><?php echo ($mCarteraVencida[$i]['pucidxxx'] != "") ? $mCarteraVencida[$i]['pucidxxx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mCarteraVencida[$i]['comfecxx'] != "") ? $mCarteraVencida[$i]['comfecxx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mCarteraVencida[$i]['comfecnx'] != "") ? $mCarteraVencida[$i]['comfecnx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mCarteraVencida[$i]['comfecvn'] != "") ? $mCarteraVencida[$i]['comfecvn'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mCarteraVencida[$i]['diascart'] != "") ? $mCarteraVencida[$i]['diascart'] : "&nbsp;" ?></td>
                      <td align="center"><font color="red"><?php echo ($mCarteraVencida[$i]['diasvenc'] != "") ? $mCarteraVencida[$i]['diasvenc'] : "&nbsp;" ?></font></td>
                      <td align="center"><?php echo ($mCarteraVencida[$i]['commovxx'] != "") ? $mCarteraVencida[$i]['commovxx'] : "&nbsp;" ?></td>
                      <td align="right"><?php echo number_format($mCarteraVencida[$i]['saldoxxx'],2,",",".")?></td>
                    </tr>
                    <?php $nTotCarVencida += ($mCarteraVencida[$i]['commovxx'] == "D") ? $mCarteraVencida[$i]['saldoxxx'] : ($mCarteraVencida[$i]['saldoxxx'] * -1);
                  } ?>
                  <tr>
                    <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA VENCIDA: </b></td>
                    <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotCarVencida,2,",",".")?></b></td>
                  </tr>
                  <tr><td colspan = "<?php echo $nCol ?>">&nbsp;</td></tr>
                  <tr>
                    <td align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>CARTERA SIN VENCER</b></td>
                  </tr>
                  <tr>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Importador</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>DO</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Pedido</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:140px"><b>Observaci&oacute;n</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Comprobante</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Cuenta</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Entrega Cliente</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>
                  </tr>
                  <?php for($i=0;$i<count($mCarteraSinVencer);$i++){ 
                    if($mRecProv[$i]['comidxxx'] == 'P' || $mRecProv[$i]['comidxxx'] == 'L' || $mRecProv[$i]['comidxxx'] == 'C'){
                      $cTipCom = $mComP[$mRecProv[$i]['comidxxx']][$mRecProv[$i]['comcodxx']];
  
                      if (in_array("{$mRecProv[$i]['comidxxx']}~{$mRecProv[$i]['comcodxx']}", $mRCM) == true) {
                        $cTipCom = "RCM";
                      }
                    }else{
                      $cTipCom = "";
                    } ?>
                    <tr>
                      <td align="left"><?php echo ($mCarteraSinVencer[$i]['clinomxx'] != "") ? $mCarteraSinVencer[$i]['clinomxx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mCarteraSinVencer[$i]['docidxxx'] != "") ? $mCarteraSinVencer[$i]['docidxxx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mCarteraSinVencer[$i]['pedidoxx'] != "") ? $mCarteraSinVencer[$i]['pedidoxx'] : "&nbsp;" ?></td>
                      <td align="left"><?php echo ($mCarteraSinVencer[$i]['comobsxx'] != "") ? $mCarteraSinVencer[$i]['comobsxx'] : "&nbsp;" ?></td>
                      <td align="left"><?php echo ($mCarteraSinVencer[$i]['document'] != "") ? "<a href=\"javascript:f_Ver('{$mCarteraSinVencer[$i]['comidxxx']}','{$mCarteraSinVencer[$i]['comcodxx']}','{$mCarteraSinVencer[$i]['comcscxx']}','{$mCarteraSinVencer[$i]['comfecin']}','{$mCarteraSinVencer[$i]['regestxx']}','$cTipCom')\">{$mCarteraSinVencer[$i]['document']}</a>": "&nbsp;"; ?></td>
                      <td align="center"><?php echo ($mCarteraSinVencer[$i]['pucidxxx'] != "") ? $mCarteraSinVencer[$i]['pucidxxx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mCarteraSinVencer[$i]['comfecxx'] != "") ? $mCarteraSinVencer[$i]['comfecxx'] : "&nbsp;"; ?></td>
                      <td align="center"><?php echo ($mCarteraSinVencer[$i]['comfecnx'] != "") ? $mCarteraSinVencer[$i]['comfecnx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mCarteraSinVencer[$i]['comfecvn'] != "") ? $mCarteraSinVencer[$i]['comfecvn'] : "&nbsp;"; ?></td>
                      <td align="center"><?php echo ($mCarteraSinVencer[$i]['diascart'] != "") ? $mCarteraSinVencer[$i]['diascart'] : "&nbsp;"; ?></td>
                      <td align="center"><?php echo ($mCarteraSinVencer[$i]['diasvenc'] != "") ? $mCarteraSinVencer[$i]['diasvenc'] : "&nbsp;"; ?></td>
                      <td align="center"><?php echo ($mCarteraSinVencer[$i]['commovxx'] != "") ? $mCarteraSinVencer[$i]['commovxx'] : "&nbsp;"; ?></td>
                      <td align="right"><?php echo number_format($mCarteraSinVencer[$i]['saldoxxx'],2,",",".")?></td>
                    </tr>
                    <?php $nTotCartera += ($mCarteraSinVencer[$i]['commovxx'] == "D") ? $mCarteraSinVencer[$i]['saldoxxx'] : ($mCarteraSinVencer[$i]['saldoxxx'] * -1);
                  } ?>
                  <tr>
                    <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA SIN VENCER: </b></td>
                    <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotCartera,2,",",".")?></b></td>
                  </tr>
                  <tr><td colspan = "<?php echo $nCol ?>">&nbsp;</td></tr>
                  <tr>
                    <td align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>SALDOS A FAVOR</b></td>
                  </tr>
                  <tr>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Importador</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>DO</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Pedido</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:140px"><b>Observaci&oacute;n</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Comprobante</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Cuenta</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Entrega Cliente</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>
                  </tr>
                  <?php for($i=0;$i<count($mSaldosaFavor);$i++){ 
                    if($mRecProv[$i]['comidxxx'] == 'P' || $mRecProv[$i]['comidxxx'] == 'L' || $mRecProv[$i]['comidxxx'] == 'C'){
                      $cTipCom = $mComP[$mRecProv[$i]['comidxxx']][$mRecProv[$i]['comcodxx']];
  
                      if (in_array("{$mRecProv[$i]['comidxxx']}~{$mRecProv[$i]['comcodxx']}", $mRCM) == true) {
                        $cTipCom = "RCM";
                      }
                    }else{
                      $cTipCom = "";
                    } ?>
                    <tr>
                      <td align="left"><?php echo ($mSaldosaFavor[$i]['clinomxx'] != "") ? $mSaldosaFavor[$i]['clinomxx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mSaldosaFavor[$i]['docidxxx'] != "") ? $mSaldosaFavor[$i]['docidxxx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mSaldosaFavor[$i]['pedidoxx'] != "") ? $mSaldosaFavor[$i]['pedidoxx'] : "&nbsp;" ?></td>
                      <td align="left"><?php echo ($mSaldosaFavor[$i]['comobsxx'] != "") ? $mSaldosaFavor[$i]['comobsxx'] : "&nbsp;" ?></td>
                      <td align="left"><?php echo ($mSaldosaFavor[$i]['document'] != "") ? "<a href=\"javascript:f_Ver('{$mSaldosaFavor[$i]['comidxxx']}','{$mSaldosaFavor[$i]['comcodxx']}','{$mSaldosaFavor[$i]['comcscxx']}','{$mSaldosaFavor[$i]['comfecin']}','{$mSaldosaFavor[$i]['regestxx']}','$cTipCom')\">{$mSaldosaFavor[$i]['document']}</a>": "&nbsp;"; ?></td>
                      <td align="center"><?php echo ($mSaldosaFavor[$i]['pucidxxx'] != "") ? $mSaldosaFavor[$i]['pucidxxx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mSaldosaFavor[$i]['comfecxx'] != "") ? $mSaldosaFavor[$i]['comfecxx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mSaldosaFavor[$i]['comfecnx'] != "") ? $mSaldosaFavor[$i]['comfecnx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mSaldosaFavor[$i]['comfecvn'] != "") ? $mSaldosaFavor[$i]['comfecvn'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mSaldosaFavor[$i]['diascart'] != "") ? $mSaldosaFavor[$i]['diascart'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mSaldosaFavor[$i]['diasvenc'] != "") ? $mSaldosaFavor[$i]['diasvenc'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mSaldosaFavor[$i]['commovxx'] != "") ? $mSaldosaFavor[$i]['commovxx'] : "&nbsp;" ?></td>
                      <td align="right"><?php echo number_format($mSaldosaFavor[$i]['saldoxxx'],2,",",".")?></td>
                    </tr>
                    <?php $nTotSaldos += ($mSaldosaFavor[$i]['commovxx'] == "D") ? $mSaldosaFavor[$i]['saldoxxx'] : ($mSaldosaFavor[$i]['saldoxxx'] * -1);
                  } ?>
                  <tr>
                    <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS A FAVOR: </b></td>
                    <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotSaldos,2,",",".")?></b></td>
                  </tr>
                  <tr><td colspan = "<?php echo $nCol ?>">&nbsp;</td></tr>
                  <tr>
                    <td align="left" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB" style="font-size:14px"><b>RECIBOS PROVISIONALES</b></td>
                  </tr>
                  <tr>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Importador</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>DO</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Pedido</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:140px"><b>Observaci&oacute;n</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Comprobante</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Cuenta</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Entrega Cliente</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Vencimiento</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>
                    <td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>
                  </tr>
                  <?php for($i=0;$i<count($mRecProv);$i++){
                    if($mRecProv[$i]['comidxxx'] == 'P' || $mRecProv[$i]['comidxxx'] == 'L' || $mRecProv[$i]['comidxxx'] == 'C'){
                      $cTipCom = $mComP[$mRecProv[$i]['comidxxx']][$mRecProv[$i]['comcodxx']];
  
                      if (in_array("{$mRecProv[$i]['comidxxx']}~{$mRecProv[$i]['comcodxx']}", $mRCM) == true) {
                        $cTipCom = "RCM";
                      }
                    }else{
                      $cTipCom = "";
                    } ?>
                    <tr>
                      <td align="left"><?php echo ($mRecProv[$i]['clinomxx'] != "") ? $mRecProv[$i]['clinomxx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mRecProv[$i]['docidxxx'] != "") ? $mRecProv[$i]['docidxxx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mRecProv[$i]['pedidoxx'] != "") ? $mRecProv[$i]['pedidoxx'] : "&nbsp;" ?></td>
                      <td align="left"><?php echo ($mRecProv[$i]['comobsxx'] != "") ? $mRecProv[$i]['comobsxx'] : "&nbsp;" ?></td>
                      <td align="left"><?php echo ($mRecProv[$i]['document'] != "")?"<a href=\"javascript:f_Ver('{$mRecProv[$i]['comidxxx']}','{$mRecProv[$i]['comcodxx']}','{$mRecProv[$i]['comcscxx']}','{$mRecProv[$i]['comfecin']}','PROVISIONAL','$cTipCom')\">{$mRecProv[$i]['document']}</a>": "&nbsp;"; ?></td>
                      <td align="center"><?php echo ($mRecProv[$i]['pucidxxx'] != "") ? $mRecProv[$i]['pucidxxx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mRecProv[$i]['comfecxx'] != "") ? $mRecProv[$i]['comfecxx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mRecProv[$i]['comfecnx'] != "") ? $mRecProv[$i]['comfecnx'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mRecProv[$i]['comfecvn'] != "") ? $mRecProv[$i]['comfecvn'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mRecProv[$i]['diascart'] != "") ? $mRecProv[$i]['diascart'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mRecProv[$i]['diasvenc'] != "") ? $mRecProv[$i]['diasvenc'] : "&nbsp;" ?></td>
                      <td align="center"><?php echo ($mRecProv[$i]['commovxx'] != "") ? $mRecProv[$i]['commovxx'] : "&nbsp;" ?></td>
                      <td align="right"><?php echo number_format($mRecProv[$i]['saldoxxx'],2,",",".")?></td>
                    </tr>
                    <?php $nTotProvicionales += ($mRecProv[$i]['commovxx'] == "D") ? $mRecProv[$i]['saldoxxx'] : ($mRecProv[$i]['saldoxxx'] * -1);
                  } ?>
                  <tr>
                    <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS PROVISIONALES: </b></td>
                    <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotProvicionales,2,",",".")?></b></td>
                  </tr>
                  <tr><td colspan = "<?php echo $nCol ?>">&nbsp;</td></tr>
                  <tr>
                    <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS A FAVOR: </b></td>
                    <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format($nTotSaldos,2,",",".")?></b></td>
                  </tr>
                  <tr>
                    <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA: </b></td>
                    <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format(($nTotCarVencida+$nTotCartera),2,",",".")?></b></td>
                  </tr>
                  <?php $mNomTotales = array();
                  (abs($nTotCarVencida+$nTotCartera)>0) ? $mNomTotales[count($mNomTotales)] ="TOTAL CARTERA" :"";
                  (abs($nTotSaldos)>0) ? $mNomTotales[count($mNomTotales)] ="SALDOS A FAVOR" :"";
                  (abs($nTotProvicionales)>0) ? $mNomTotales[count($mNomTotales)]="RECIBOS PROVISIONALES" :"";
  
                  $mTitulo="";
  
                  for($j=0;$j <= (count($mNomTotales)-1);$j++){
                    $mTitulo .= $mNomTotales[$j];
                    ($j==(count($mNomTotales)-1)) ? "" : $mTitulo .=" - ";
                  } ?>
                  <tr>
                    <td align="right" colspan = "<?php echo $nCol-1 ?>" bgcolor = "#A6DEEE" style="font-size:12px"><b><?php echo $mTitulo.":" ?></b></td>
                    <td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b><?php echo number_format((($nTotCarVencida+$nTotCartera) - ($nTotProvicionales) + ($nTotSaldos)),2,",",".")?></b></td>
                  </tr>
                </table>
              </center>
            </body>
          </html>
        <?php break;
        case 2: // PINTA POR EXCEL//
          /**
           * Variable para armar la cadena de texto que se envia al excel
           * @var Text
           */
          $header  .= 'Reporte Estado de Cuenta General'."\n";
          $header  .= "\n";
          $cData    = '';
          $cNomFile = "ESTADO_DE_CUENTA_GENERAL_".$kUser."_".date('YmdHis').".xls";

          if ($_SERVER["SERVER_PORT"] != "") {
            $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
          } else {
            $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
          }
      
          if (file_exists($cFile)) {
            unlink($cFile);
          }
  
          $fOp = fopen($cFile, 'a');

          $nCol     = 13;  
          $cData .= '<table border = "1" cellpadding = "0" cellspacing = "0" width = "1200px">';
            $cData .= '<tr>';
              $cData .= '<td class="name" colspan = "'.$nCol.'" style="font-size:14px">';
                $cData .= '<center><span style="font-size:18px">REPORTE DE ESTADO DE CUENTA GENERAL DEL '.$cFecha.'</span></center>';
              $cData .= '</td>';
            $cData .= '</tr>';
            $cData .= '<tr>';
              $cData .= '<td align="left" colspan = "'.$nCol.'" bgcolor = "#96ADEB" style="font-size:14px"><b>CONSULTA POR CLIENTE</b></td>';
            $cData .= '</tr>';
            $cData .= '<tr>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:50px"><b>NIT</b></td>';
              $cData .= '<td align="center" style="width:120px"><b>'.$gTerId."-".f_Digito_Verificacion($gTerId).'</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Cliente</b></td>';
              $cData .= '<td align="center"><b>'.($vNomCli['clinomxx']).'</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Tel&eacute;fono</b></td>';
              $cData .= '<td align="center"><b>'.($vNomCli['CLITELXX']).'</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7"><b>Fecha y Hora de Consulta</b></td>';
              $cData .= '<td align="center" style="width:140px"><b>'.date("Y-m-d H:i:s").'</b></td>';
            $cData .= '</tr>';
            $cData .= '<tr><td colspan = "'.$nCol.'"></td></tr>';
            $cData .= '<tr>';
              $cData .= '<td align="left" colspan = "'.$nCol.'" bgcolor = "#96ADEB" style="font-size:14px"><b>CARTERA VENCIDA</b></td>';
            $cData .= '</tr>';
            $cData .= '<tr>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:130px"><b>Importador</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Pedido</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:140px"><b>Observacion</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Comprobante</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Cuenta</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:140px"><b>Entrega Cliente</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Vencimiento</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>';
            $cData .= '</tr>';
            for($i=0;$i<count($mCarteraVencida);$i++) {
              if($mCarteraVencida[$i]['comidxxx'] == 'P' || $mCarteraVencida[$i]['comidxxx'] == 'L' || $mCarteraVencida[$i]['comidxxx'] == 'C'){
                $cTipCom = $mComP[$mCarteraVencida[$i]['comidxxx']][$mCarteraVencida[$i]['comcodxx']];
  
                if (in_array("{$mCarteraVencida[$i]['comidxxx']}~{$mCarteraVencida[$i]['comcodxx']}", $mRCM) == true) {
                  $cTipCom = "RCM";
                }
              }else{
                $cTipCom = "";
              }
              $cData .= '<tr>';
                $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['clinomxx'] != "") ? $mCarteraVencida[$i]['clinomxx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['docidxxx'] != "") ? $mCarteraVencida[$i]['docidxxx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['pedidoxx'] != "") ? $mCarteraVencida[$i]['pedidoxx'] : "").'</td>';
                $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['comobsxx'] != "") ? $mCarteraVencida[$i]['comobsxx'] : "").'</td>';
                $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['document'] != "") ? $mCarteraVencida[$i]['document'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['pucidxxx'] != "") ? $mCarteraVencida[$i]['pucidxxx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraVencida[$i]['comfecxx'] != "") ? $mCarteraVencida[$i]['comfecxx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraVencida[$i]['comfecnx'] != "") ? $mCarteraVencida[$i]['comfecnx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraVencida[$i]['comfecvn'] != "") ? $mCarteraVencida[$i]['comfecvn'] : "").'</td>';
                $cData .= '<td align="center">'.(($mCarteraVencida[$i]['diascart'] != "") ? $mCarteraVencida[$i]['diascart'] : "").'</td>';
                $cData .= '<td align="center"><font color="red">'.(($mCarteraVencida[$i]['diasvenc'] != "") ? $mCarteraVencida[$i]['diasvenc'] : "").'</font></td>';
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraVencida[$i]['commovxx'] != "") ? $mCarteraVencida[$i]['commovxx'] : "").'</td>';
                $cData .= '<td align="right">'.(number_format($mCarteraVencida[$i]['saldoxxx'],2,",",".")).'</td>';
              $cData .= '</tr>';
              $nTotCarVencida += ($mCarteraVencida[$i]['commovxx'] == "D") ? $mCarteraVencida[$i]['saldoxxx'] : ($mCarteraVencida[$i]['saldoxxx'] * -1);
            }
            $cData .= '<tr>';
              $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA VENCIDA: </b></td>';
              $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotCarVencida,2,",",".")).'</b></td>';
            $cData .= '</tr>';
            $cData .= '<tr><td colspan = "'.$nCol.'"></td></tr>';
            $cData .= '<tr>';
              $cData .= '<td align="left" colspan = "'.$nCol.'" bgcolor = "#96ADEB" style="font-size:14px"><b>CARTERA SIN VENCER</b></td>';
            $cData .= '</tr>';
            $cData .= '<tr>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:130px"><b>Importador</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Pedido</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:140px"><b>Observacion</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Comprobante</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Cuenta</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:140px"><b>Entrega Cliente</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Vencimiento</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>';
            $cData .= '</tr>';
            for($i=0;$i<count($mCarteraSinVencer);$i++){
              if($mCarteraSinVencer[$i]['comidxxx'] == 'P' || $mCarteraSinVencer[$i]['comidxxx'] == 'L' || $mCarteraSinVencer[$i]['comidxxx'] == 'C'){
                $cTipCom = $mComP[$mCarteraSinVencer[$i]['comidxxx']][$mCarteraSinVencer[$i]['comcodxx']];
  
                if (in_array("{$mCarteraSinVencer[$i]['comidxxx']}~{$mCarteraSinVencer[$i]['comcodxx']}", $mRCM) == true) {
                  $cTipCom = "RCM";
                }
              }else{
                $cTipCom = "";
              }
              $cData .= '<tr>';
                $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['clinomxx'] != "") ? $mCarteraSinVencer[$i]['clinomxx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['docidxxx'] != "") ? $mCarteraSinVencer[$i]['docidxxx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['pedidoxx'] != "") ? $mCarteraSinVencer[$i]['pedidoxx'] : "").'</td>';
                $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['comobsxx'] != "") ? $mCarteraSinVencer[$i]['comobsxx'] : "").'</td>';
                $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['document'] != "") ? $mCarteraSinVencer[$i]['document'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['pucidxxx'] != "") ? $mCarteraSinVencer[$i]['pucidxxx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraSinVencer[$i]['comfecxx'] != "") ? $mCarteraSinVencer[$i]['comfecxx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraSinVencer[$i]['comfecnx'] != "") ? $mCarteraSinVencer[$i]['comfecnx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mCarteraSinVencer[$i]['comfecvn'] != "") ? $mCarteraSinVencer[$i]['comfecvn'] : "").'</td>';
                $cData .= '<td align="center">'.(($mCarteraSinVencer[$i]['diascart'] != "") ? $mCarteraSinVencer[$i]['diascart'] : "").'</td>';
                $cData .= '<td align="center"><font color="red">'.(($mCarteraSinVencer[$i]['diasvenc'] != "") ? $mCarteraSinVencer[$i]['diasvenc'] : "").'</font></td>';
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mCarteraSinVencer[$i]['commovxx'] != "") ? $mCarteraSinVencer[$i]['commovxx'] : "").'</td>';
                $cData .= '<td align="right">'.(number_format($mCarteraSinVencer[$i]['saldoxxx'],2,",",".")).'</td>';
              $cData .= '</tr>';
              $nTotCartera += ($mCarteraSinVencer[$i]['commovxx'] == "D") ? $mCarteraSinVencer[$i]['saldoxxx'] : ($mCarteraSinVencer[$i]['saldoxxx'] * -1);
            }
            $cData .= '<tr>';
              $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA SIN VENCER: </b></td>';
              $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotCartera,2,",",".")).'</b></td>';
            $cData .= '</tr>';
            $cData .= '<tr><td colspan = "'.$nCol.'"></td></tr>';
            $cData .= '<tr>';
              $cData .= '<td align="left" colspan = "'.$nCol.'" bgcolor = "#96ADEB" style="font-size:14px"><b>SALDOS A FAVOR</b></td>';
            $cData .= '</tr>';
            $cData .= '<tr>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:130px"><b>Importador</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Pedido</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:140px"><b>Observacion</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Comprobante</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Cuenta</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:140px"><b>Entrega Cliente</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Vencimiento</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>';
            $cData .= '</tr>';
            for($i=0;$i<count($mSaldosaFavor);$i++) {
              if($mSaldosaFavor[$i]['comidxxx'] == 'P' || $mSaldosaFavor[$i]['comidxxx'] == 'L' || $mSaldosaFavor[$i]['comidxxx'] == 'C'){
                $cTipCom = $mComP[$mSaldosaFavor[$i]['comidxxx']][$mSaldosaFavor[$i]['comcodxx']];
  
                if (in_array("{$mSaldosaFavor[$i]['comidxxx']}~{$mSaldosaFavor[$i]['comcodxx']}", $mRCM) == true) {
                  $cTipCom = "RCM";
                }
              }else{
                $cTipCom = "";
              }
              $cData .= '<tr>';
                $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['clinomxx'] != "") ? $mSaldosaFavor[$i]['clinomxx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['docidxxx'] != "") ? $mSaldosaFavor[$i]['docidxxx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['pedidoxx'] != "") ? $mSaldosaFavor[$i]['pedidoxx'] : "").'</td>';
                $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['comobsxx'] != "") ? $mSaldosaFavor[$i]['comobsxx'] : "").'</td>';
                $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['document'] != "") ? $mSaldosaFavor[$i]['document'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['pucidxxx'] != "") ? $mSaldosaFavor[$i]['pucidxxx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mSaldosaFavor[$i]['comfecxx'] != "") ? $mSaldosaFavor[$i]['comfecxx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mSaldosaFavor[$i]['comfecnx'] != "") ? $mSaldosaFavor[$i]['comfecnx'] : "").'</td>';
                $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mSaldosaFavor[$i]['comfecvn'] != "") ? $mSaldosaFavor[$i]['comfecvn'] : "").'</td>';
                $cData .= '<td align="center">'.(($mSaldosaFavor[$i]['diascart'] != "") ? $mSaldosaFavor[$i]['diascart'] : "").'</td>';
                $cData .= '<td align="center"><font color="red">'.(($mSaldosaFavor[$i]['diasvenc'] != "") ? $mSaldosaFavor[$i]['diasvenc'] : "").'</font></td>';
                $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mSaldosaFavor[$i]['commovxx'] != "") ? $mSaldosaFavor[$i]['commovxx'] : "").'</td>';
                $cData .= '<td align="right">'.(number_format($mSaldosaFavor[$i]['saldoxxx'],2,",",".")).'</td>';
              $cData .= '</tr>';
              $nTotSaldos += ($mSaldosaFavor[$i]['commovxx'] == "D") ? $mSaldosaFavor[$i]['saldoxxx'] : ($mSaldosaFavor[$i]['saldoxxx'] * -1);
            }
            $cData .= '<tr>';
              $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS A FAVOR: </b></td>';
              $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotSaldos,2,",",".")).'</b></td>';
            $cData .= '</tr>';
            $cData .= '<tr><td colspan = "'.$nCol.'"></td></tr>';
            $cData .= '<tr>';
              $cData .= '<td align="left" colspan = "'.$nCol.'" bgcolor = "#96ADEB" style="font-size:14px"><b>RECIBOS PROVISIONALES</b></td>';
            $cData .= '</tr>';
            $cData .= '<tr>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:130px"><b>Importador</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>DO</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Pedido</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:140px"><b>Observacion</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Comprobante</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Cuenta</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Fecha</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:140px"><b>Entrega Cliente</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Vencimiento</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Cartera</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>D&iacute;as Vencidos</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:100px"><b>Movimiento</b></td>';
              $cData .= '<td align="center" bgcolor = "#D6DFF7" style="width:120px"><b>Saldo</b></td>';
            $cData .= '</tr>';
            for($i=0;$i<count($mRecProv);$i++) {
              if($mRecProv[$i]['comidxxx'] == 'P' || $mRecProv[$i]['comidxxx'] == 'L' || $mRecProv[$i]['comidxxx'] == 'C'){
                $cTipCom = $mComP[$mRecProv[$i]['comidxxx']][$mRecProv[$i]['comcodxx']];
  
                if (in_array("{$mRecProv[$i]['comidxxx']}~{$mRecProv[$i]['comcodxx']}", $mRCM) == true) {
                  $cTipCom = "RCM";
                }
              }else{
                $cTipCom = "";
              }
            $cData .= '<tr>';
              $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mRecProv[$i]['clinomxx'] != "") ? $mRecProv[$i]['clinomxx'] : "").'</td>';
              $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mRecProv[$i]['docidxxx'] != "") ? $mRecProv[$i]['docidxxx'] : "").'</td>';
              $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mRecProv[$i]['pedidoxx'] != "") ? $mRecProv[$i]['pedidoxx'] : "").'</td>';
              $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mRecProv[$i]['comobsxx'] != "") ? $mRecProv[$i]['comobsxx'] : "").'</td>';
              $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mRecProv[$i]['document'] != "") ? $mRecProv[$i]['document'] : "").'</td>';
              $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mRecProv[$i]['pucidxxx'] != "") ? $mRecProv[$i]['pucidxxx'] : "").'</td>';
              $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mRecProv[$i]['comfecxx'] != "") ? $mRecProv[$i]['comfecxx'] : "").'</td>';
              $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mRecProv[$i]['comfecnx'] != "") ? $mRecProv[$i]['comfecnx'] : "").'</td>';
              $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mRecProv[$i]['comfecvn'] != "") ? $mRecProv[$i]['comfecvn'] : "").'</td>';
              $cData .= '<td align="center">'.(($mRecProv[$i]['diascart'] != "") ? $mRecProv[$i]['diascart'] : "").'</td>';
              $cData .= '<td align="center"><font color="red">'.(($mRecProv[$i]['diasvenc'] != "") ? $mRecProv[$i]['diasvenc'] : "").'</font></td>';
              $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mRecProv[$i]['commovxx'] != "") ? $mRecProv[$i]['commovxx'] : "").'</td>';
              $cData .= '<td align="right">'.(number_format($mRecProv[$i]['saldoxxx'],2,",",".")).'</td>';
            $cData .= '</tr>';
            $nTotProvicionales += ($mRecProv[$i]['commovxx'] == "D") ? $mRecProv[$i]['saldoxxx'] : ($mRecProv[$i]['saldoxxx'] * -1);
            }
            $cData .= '<tr>';
              $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS PROVISIONALES: </b></td>';
              $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotProvicionales,2,",",".")).'</b></td>';
            $cData .= '</tr>';
            $cData .= '<tr><td colspan = "'.$nCol.'"></td></tr>';
            $cData .= '<tr>';
              $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL SALDOS A FAVOR: </b></td>';
              $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format($nTotSaldos,2,",",".")).'</b></td>';
            $cData .= '</tr>';
            $cData .= '<tr>';
              $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>TOTAL CARTERA: </b></td>';
              $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format(($nTotCarVencida+$nTotCartera),2,",",".")).'</b></td>';
            $cData .= '</tr>';
            $mNomTotales = array();
            (abs($nTotCarVencida+$nTotCartera)>0) ? $mNomTotales[count($mNomTotales)] ="TOTAL CARTERA" :"";
            (abs($nTotSaldos)>0) ? $mNomTotales[count($mNomTotales)] ="SALDOS A FAVOR" :"";
            (abs($nTotProvicionales)>0) ? $mNomTotales[count($mNomTotales)]="RECIBOS PROVISIONALES" :"";
  
            $mTitulo="";
  
            for($j=0;$j <= (count($mNomTotales)-1);$j++){
              $mTitulo .= $mNomTotales[$j];
              ($j==(count($mNomTotales)-1)) ? "" : $mTitulo .=" - ";
              }
            $cData .= '<tr>';
              $cData .= '<td align="right" colspan = "'.($nCol-1).'" bgcolor = "#A6DEEE" style="font-size:12px"><b>'.($mTitulo.":" ).'</b></td>';
              $cData .= '<td align="right" bgcolor = "#A6DEEE" style="font-size:12px;width=120px"><b>'.(number_format((($nTotCarVencida+$nTotCartera) - ($nTotProvicionales) + ($nTotSaldos)),2,",",".")).'</b></td>';
            $cData .= '</tr>';
          $cData .= '</table>';

          fwrite($fOp, $cData);
          fclose($fOp);

          if (file_exists($cFile)) {

            if ($cData == "") {
              $cData = "\n(0) REGISTROS!\n";
            }
    
            chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
            $cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFile);
    
            if ($_SERVER["SERVER_PORT"] != "") {
               // Obtener la ruta absoluta del archivo
						  $cAbsolutePath = realpath($cFile);
						  $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

              if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
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
              }
            }else{
              $cNomArc = $cNomFile;
              echo "\n".$cNomArc;
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
        default:

          define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
          require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

          class PDF extends FPDF {
            function Header() {
            }

            function Footer(){
              $this->SetY(-10);
              $this->SetFont('Arial','',6);
              $this->Cell(0,5,'PAGINA '.$this->PageNo().' DE {nb}',0,0,'C');
            }

            // rota la celda
            function RotatedText($x,$y,$txt,$angle){
              //Text rotated around its origin
              $this->Rotate($angle,$x,$y);
              $this->Text($x,$y,$txt);
              $this->Rotate(0);
            }

            // rota la celda
            var $angle=0;
            function Rotate($angle,$x=-1,$y=-1){
              if($x==-1)
                $x=$this->x;
              if($y==-1)
                $y=$this->y;
              if($this->angle!=0)
                $this->_out('Q');
              $this->angle=$angle;
              if($angle!=0) {
                $angle*=M_PI/180;
                $c=cos($angle);
                $s=sin($angle);
                $cx=$x*$this->k;
                $cy=($this->h-$y)*$this->k;
                $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
              }
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
          }

          $pdf = new PDF('L','mm','Letter');
          $pdf->AliasNbPages();
          $pdf->SetMargins(5,5,5);
          $pdf->SetAutoPageBreak(true,10);
          $pdf->AddPage();
          $pdf->AddFont('otfon1','','otfon1.php');

          $pdf->SetFont('Arial','B',12);
          $pdf->setXY(10,12);
          $pdf->MultiCell(250,6,"REPORTE DE ESTADO DE CUENTA GENERAL \nDEL ". $cFecha,0,'C');
          $pdf->Rect(5,10,268,15);
          $nPosy = 30;

          switch ($cAlfa) {
            case 'SIACOSIA':
            case 'TESIACOSIP':
            case 'DESIACOSIP':
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_repcar.jpg',10,11,32,13);
            break;
            case 'ADUANAMO':
            case 'DEADUANAMO':
            case 'TEADUANAMO':
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_aduanamo.jpg',10,11,32,13);
            break;
            case "LOGINCAR":
            case "DELOGINCAR":
            case "TELOGINCAR":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/Logo_Login_Cargo_Ltda_2.jpg',10,11,39,13);
            break;
            case "TRLXXXXX":
            case "DETRLXXXXX":
            case "TETRLXXXXX":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logobma1.jpg',10,11,40,13);
            break;
            case "TEADIMPEXX": // ADIMPEX
            case "DEADIMPEXX": // ADIMPEX
            case "ADIMPEXX": // ADIMPEX
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex4.jpg',10,13,36,8);
            break;
            case "ROLDANLO"://ROLDAN
            case "TEROLDANLO"://ROLDAN
            case "DEROLDANLO"://ROLDAN
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',10,11,40,13);
            break;
            case "CASTANOX":
            case "DECASTANOX":
            case "TECASTANOX":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomartcam.jpg',10,11,29,13);
            break;
            case "ALMACAFE": //ALMACAFE
            case "TEALMACAFE": //ALMACAFE
            case "DEALMACAFE": //ALMACAFE
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalmacafe.jpg',10,12,26,11);
            break;
            case "GRUMALCO"://GRUMALCO
            case "TEGRUMALCO"://GRUMALCO
            case "DEGRUMALCO"://GRUMALCO
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',10,11,26,13);
            break;
            case "ALADUANA"://ALADUANA
            case "TEALADUANA"://ALADUANA
            case "DEALADUANA"://ALADUANA
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',16,11,29,13);
            break;
            case "ANDINOSX"://ANDINOSX
            case "TEANDINOSX"://ANDINOSX
            case "DEANDINOSX"://ANDINOSX
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoAndinos2.jpeg', 6, 11, 20, 13);
            break;
            case "GRUPOALC"://GRUPOALC
            case "TEGRUPOALC"://GRUPOALC
            case "DEGRUPOALC"://GRUPOALC
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalc.jpg',6,11,29,13);
            break;
            case "AAINTERX"://AAINTERX
            case "TEAAINTERX"://AAINTERX
            case "DEAAINTERX"://AAINTERX
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointernacional.jpg',16,11,29,13);
            break;
            case "AALOPEZX":
            case "TEAALOPEZX":
            case "DEAALOPEZX":
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoaalopez.png', 6, 11, 27);
            break;
            case "ADUAMARX"://ADUAMARX
            case "TEADUAMARX"://ADUAMARX
            case "DEADUAMARX"://ADUAMARX
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg',10,11,13);
            break;
            case "SOLUCION"://SOLUCION
            case "TESOLUCION"://SOLUCION
            case "DESOLUCION"://SOLUCION
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',10,11,30);
            break;
            case "FENIXSAS"://FENIXSAS
            case "TEFENIXSAS"://FENIXSAS
            case "DEFENIXSAS"://FENIXSAS
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg',10,11,34);
            break;
            case "COLVANXX"://COLVANXX
            case "TECOLVANXX"://COLVANXX
            case "DECOLVANXX"://COLVANXX
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logocolvan.jpg',10,9,34);
            break;
            case "INTERLAC"://INTERLAC
            case "TEINTERLAC"://INTERLAC
            case "DEINTERLAC"://INTERLAC
              $pdf->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointerlace.jpg',10,8,34);
            break;
            case "KARGORUX": //KARGORUX
            case "TEKARGORUX": //KARGORUX
            case "DEKARGORUX": //KARGORUX
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logokargoru.jpg', 10, 11.5, 28);
            break;
            case "ALOGISAS": //LOGISTICA
            case "TEALOGISAS": //LOGISTICA
            case "DEALOGISAS": //LOGISTICA
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 10, 11, 32);
            break;
            case "PROSERCO": //PROSERCO
            case "TEPROSERCO": //PROSERCO
            case "DEPROSERCO": //PROSERCO
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoproserco.png', 10, 11, 22);
            break;
            case "MANATIAL": //MANATIAL
            case "TEMANATIAL": //MANATIAL
            case "DEMANATIAL": //MANATIAL
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomanantial.jpg', 10, 12, 50, 11);
            break;
            case "DSVSASXX":  //DSVSAS
            case "DEDSVSASXX"://DSVSAS
            case "TEDSVSASXX"://DSVSAS
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logodsv.jpg', 10, 12, 40, 11);
            break;
            case "MELYAKXX":    //MELYAK
            case "DEMELYAKXX":  //MELYAK
            case "TEMELYAKXX":  //MELYAK
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomelyak.jpg', 10, 12, 35, 11);
            break;
            case "FEDEXEXP":    //FEDEX
            case "DEFEDEXEXP":  //FEDEX
            case "TEFEDEXEXP":  //FEDEX  
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logofedexexp.jpg', 10, 11, 30, 13);
            break;
            case "EXPORCOM":    //EXPORCOMEX
            case "DEEXPORCOM":  //EXPORCOMEX
            case "TEEXPORCOM":  //EXPORCOMEX  
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoexporcomex.jpg', 10, 11, 28, 13);
            break;
            case "HAYDEARX":    //HAYDEARX
            case "DEHAYDEARX":  //HAYDEARX
            case "TEHAYDEARX":  //HAYDEARX  
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logohaydear.jpeg', 10, 11, 40, 13);
            break;
            case "CONNECTA":    //CONNECTA
            case "DECONNECTA":  //CONNECTA
            case "TECONNECTA":  //CONNECTA  
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoconnecta.jpg', 10, 11, 20, 13);
            break;
            case "OPENEBCO":    //OPENEBCO
            case "DEOPENEBCO":  //OPENEBCO
            case "TEOPENEBCO":  //OPENEBCO  
              $pdf->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/opentecnologia.JPG', 6, 11, 35, 13);
            break;
          }

          $nPosx = 5;
          $pdf->SetFont('Arial','B',10);
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFillColor(150,173,235);
          $pdf->Cell(268,4,"CONSULTA POR CLIENTE",1,0,'L',1);
          $nPosy += 4;
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFillColor(255,255,255);
          $pdf->SetFont('Arial','B',7);
          $pdf->Cell(8,4,"NIT",1,0,'L',1);
          $pdf->SetFont('Arial','',7);
          $pdf->Cell(25,4,$gTerId."-".f_Digito_Verificacion($gTerId),1,0,'L',1);
          $pdf->SetFont('Arial','B',7);
          $pdf->Cell(15,4,"CLIENTE",1,0,'L',1);
          $pdf->SetFont('Arial','',7);
          $pdf->Cell(105,4,$vNomCli['clinomxx'],1,0,'L',1);
          $pdf->SetFont('Arial','B',7);
          $pdf->Cell(15,4,"TELEFONO",1,0,'C',1);
          $pdf->SetFont('Arial','',7);
          $pdf->Cell(30,4,$vNomCli['CLITELXX'],1,0,'L',1);
          $pdf->SetFont('Arial','B',7);
          $pdf->Cell(40,4,"FECHA Y HORA DE CONSULTA",1,0,'L',1);
          $pdf->SetFont('Arial','',7);
          $pdf->Cell(30,4,date("Y-m-d H:i:s"),1,0,'L',1);
          $nPosy += 8;

          $pdf->SetFont('Arial','B',10);
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFillColor(150,173,235);
          $pdf->Cell(268,4,"CARTERA VENCIDA",1,0,'L',1);
          $nPosy += 4;
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFont('Arial','B',7);
          $pdf->SetFillColor(150,173,235);
 
          $pdf->Cell(18,4,"Importador",1,0,'C',1);
          $pdf->Cell(18,4,"DO",1,0,'C',1);
          $pdf->Cell(20,4,"Pedido",1,0,'C',1);
          $pdf->Cell(30,4,utf8_decode("Observación"),1,0,'C',1);
          $pdf->Cell(20,4,"Comprobante",1,0,'C',1);
          $pdf->Cell(20,4,"Cuenta",1,0,'C',1);
          $pdf->Cell(20,4,"Fecha",1,0,'C',1);
          $pdf->Cell(22,4,"Entrega Cliente",1,0,'C',1);
          $pdf->Cell(20,4,"Vencimiento",1,0,'C',1);
          $pdf->Cell(20,4,"Dias Cartera",1,0,'C',1);
          $pdf->Cell(20,4,"Dias Vencidos",1,0,'C',1);
          $pdf->Cell(20,4,"Movimiento",1,0,'C',1);
          $pdf->Cell(20,4,"Saldo",1,0,'C',1);
          $nPosy += 4;
          $pdf->setXY($nPosx,$nPosy);

          $pdf->SetWidths(array(18, 18, 20, 30, 20, 20, 20, 22, 20, 20, 20, 20, 20));
          $pdf->SetAligns(array("L", "L", "L", "C", "C", "C", "C", "C", "C", "C", "C", "C", "R"));

          for($i=0;$i<count($mCarteraVencida);$i++) {

            if($nPosy > 196){
              $pdf->AddPage();
              $nPosx = 5;
              $nPosy = 25;
              $pdf->setXY($nPosx,$nPosy);
              $pdf->Line($nPosx, $nPosy, $nPosx+268, $nPosy);
            }

            $pdf->SetFont('Arial','',6);
            $pdf->Row(array(
                $mCarteraVencida[$i]['clinomxx'],
                $mCarteraVencida[$i]['docidxxx'],
                substr($mCarteraVencida[$i]['pedidoxx'], 0, 60),
                substr($mCarteraVencida[$i]['comobsxx'], 0, 60),
                $mCarteraVencida[$i]['document'],
                $mCarteraVencida[$i]['pucidxxx'],
                $mCarteraVencida[$i]['comfecxx'],
                $mCarteraVencida[$i]['comfecnx'],
                $mCarteraVencida[$i]['comfecvn'],
                $mCarteraVencida[$i]['diascart'],
                $mCarteraVencida[$i]['diasvenc'],
                $mCarteraVencida[$i]['commovxx'],
                number_format($mCarteraVencida[$i]['saldoxxx'],2,",",".")
            ));

            $nTotCarVencida += ($mCarteraVencida[$i]['commovxx'] == "D") ? $mCarteraVencida[$i]['saldoxxx'] : ($mCarteraVencida[$i]['saldoxxx'] * -1);

            $pdf->Line($nPosx, $nPosy, $nPosx, $pdf->getY());
            $pdf->Line($nPosx+18, $nPosy, $nPosx+18, $pdf->getY());
            $pdf->Line($nPosx+36, $nPosy, $nPosx+36, $pdf->getY());
            $pdf->Line($nPosx+56, $nPosy, $nPosx+56, $pdf->getY());
            $pdf->Line($nPosx+86, $nPosy, $nPosx+86, $pdf->getY());
            $pdf->Line($nPosx+106, $nPosy, $nPosx+106, $pdf->getY());
            $pdf->Line($nPosx+126, $nPosy, $nPosx+126, $pdf->getY());
            $pdf->Line($nPosx+146, $nPosy, $nPosx+146, $pdf->getY());
            $pdf->Line($nPosx+168, $nPosy, $nPosx+168, $pdf->getY());
            $pdf->Line($nPosx+188, $nPosy, $nPosx+188, $pdf->getY());
            $pdf->Line($nPosx+208, $nPosy, $nPosx+208, $pdf->getY());
            $pdf->Line($nPosx+228, $nPosy, $nPosx+228, $pdf->getY());
            $pdf->Line($nPosx+248, $nPosy, $nPosx+248, $pdf->getY());
            $pdf->Line($nPosx+268, $nPosy, $nPosx+268, $pdf->getY());

            $pdf->Line($nPosx, $pdf->getY(), $nPosx+268, $pdf->getY());
            $nPosy = $pdf->getY();
          }

          if($nPosy > 196){
            $pdf->AddPage();
            $nPosx = 5;
            $nPosy = 25;
          }
          $pdf->SetFont('Arial','B',7);
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFillColor(150,173,235);
          $pdf->Cell(228,4,"TOTAL CARTERA VENCIDA:",1,0,'R',1);
          $pdf->Cell(40,4,number_format($nTotCarVencida,2,",","."),1,0,'R',1);

          $nPosy += 8;

          if($nPosy > 196){
            $pdf->AddPage();
            $nPosx = 5;
            $nPosy = 25;
          }

          $pdf->SetFont('Arial','B',10);
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFillColor(150,173,235);
          $pdf->Cell(268,4,"CARTERA SIN VENCER",1,0,'L',1);
          $nPosy += 4;
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFont('Arial','B',7);
          $pdf->SetFillColor(150,173,235);

          $pdf->Cell(18,4,"Importador",1,0,'C',1);
          $pdf->Cell(18,4,"DO",1,0,'C',1);
          $pdf->Cell(20,4,"Pedido",1,0,'C',1);
          $pdf->Cell(30,4,utf8_decode("Observación"),1,0,'C',1);
          $pdf->Cell(20,4,"Comprobante",1,0,'C',1);
          $pdf->Cell(20,4,"Cuenta",1,0,'C',1);
          $pdf->Cell(20,4,"Fecha",1,0,'C',1);
          $pdf->Cell(22,4,"Entrega Cliente",1,0,'C',1);
          $pdf->Cell(20,4,"Vencimiento",1,0,'C',1);
          $pdf->Cell(20,4,"Dias Cartera",1,0,'C',1);
          $pdf->Cell(20,4,"Dias Vencidos",1,0,'C',1);
          $pdf->Cell(20,4,"Movimiento",1,0,'C',1);
          $pdf->Cell(20,4,"Saldo",1,0,'C',1);
          $nPosy += 4;
          $pdf->setXY($nPosx,$nPosy);

          $pdf->SetWidths(array(18, 18, 20, 30, 20, 20, 20, 22, 20, 20, 20, 20, 20));
          $pdf->SetAligns(array("L", "L", "L", "C", "C", "C", "C", "C", "C", "C", "C", "C", "R"));

          for($i=0;$i<count($mCarteraSinVencer);$i++){

            if($nPosy > 196){
              $pdf->AddPage();
              $nPosx = 5;
              $nPosy = 25;
              $pdf->setXY($nPosx,$nPosy);
              $pdf->Line($nPosx, $nPosy, $nPosx+268, $nPosy);
            }            
           
            $pdf->SetFont('Arial','',6);
            $pdf->Row(array(
              $mCarteraSinVencer[$i]['clinomxx'],
              $mCarteraSinVencer[$i]['docidxxx'],
              substr($mCarteraSinVencer[$i]['pedidoxx'], 0, 60),
              substr($mCarteraSinVencer[$i]['comobsxx'], 0, 60),
              $mCarteraSinVencer[$i]['document'],
              $mCarteraSinVencer[$i]['pucidxxx'],
              $mCarteraSinVencer[$i]['comfecxx'],
              $mCarteraSinVencer[$i]['comfecnx'],
              $mCarteraSinVencer[$i]['comfecvn'],
              $mCarteraSinVencer[$i]['diascart'],
              $mCarteraSinVencer[$i]['diasvenc'],
              $mCarteraSinVencer[$i]['commovxx'],
              number_format($mCarteraSinVencer[$i]['saldoxxx'],2,",",".")
            ));

            $nTotCartera += ($mCarteraSinVencer[$i]['commovxx'] == "D") ? $mCarteraSinVencer[$i]['saldoxxx'] : ($mCarteraSinVencer[$i]['saldoxxx'] * -1);

            $pdf->Line($nPosx, $nPosy, $nPosx, $pdf->getY());
            $pdf->Line($nPosx+18, $nPosy, $nPosx+18, $pdf->getY());
            $pdf->Line($nPosx+36, $nPosy, $nPosx+36, $pdf->getY());
            $pdf->Line($nPosx+56, $nPosy, $nPosx+56, $pdf->getY());
            $pdf->Line($nPosx+86, $nPosy, $nPosx+86, $pdf->getY());
            $pdf->Line($nPosx+106, $nPosy, $nPosx+106, $pdf->getY());
            $pdf->Line($nPosx+126, $nPosy, $nPosx+126, $pdf->getY());
            $pdf->Line($nPosx+146, $nPosy, $nPosx+146, $pdf->getY());
            $pdf->Line($nPosx+168, $nPosy, $nPosx+168, $pdf->getY());
            $pdf->Line($nPosx+188, $nPosy, $nPosx+188, $pdf->getY());
            $pdf->Line($nPosx+208, $nPosy, $nPosx+208, $pdf->getY());
            $pdf->Line($nPosx+228, $nPosy, $nPosx+228, $pdf->getY());
            $pdf->Line($nPosx+248, $nPosy, $nPosx+248, $pdf->getY());
            $pdf->Line($nPosx+268, $nPosy, $nPosx+268, $pdf->getY());

            $pdf->Line($nPosx, $pdf->getY(), $nPosx+268, $pdf->getY());
            $nPosy = $pdf->getY();
          }

          if($nPosy > 196){
            $pdf->AddPage();
            $nPosx = 5;
            $nPosy = 25;
          }
          $pdf->SetFont('Arial','B',7);
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFillColor(150,173,235);
          $pdf->Cell(228,4,"TOTAL CARTERA SIN VENCER:",1,0,'R',1);
          $pdf->Cell(40,4,number_format($nTotCartera,2,",","."),1,0,'R',1);

          $nPosy += 8;

          if($nPosy > 196){
            $pdf->AddPage();
            $nPosx = 5;
            $nPosy = 25;
          }

          $pdf->SetFont('Arial','B',10);
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFillColor(150,173,235);
          $pdf->Cell(268,4,"SALDOS A FAVOR",1,0,'L',1);
          $nPosy += 4;
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFont('Arial','B',7);
          $pdf->SetFillColor(150,173,235);

          $pdf->Cell(18,4,"Importador",1,0,'C',1);
          $pdf->Cell(18,4,"DO",1,0,'C',1);
          $pdf->Cell(20,4,"Pedido",1,0,'C',1);
          $pdf->Cell(30,4,utf8_decode("Observación"),1,0,'C',1);
          $pdf->Cell(20,4,"Comprobante",1,0,'C',1);
          $pdf->Cell(20,4,"Cuenta",1,0,'C',1);
          $pdf->Cell(20,4,"Fecha",1,0,'C',1);
          $pdf->Cell(22,4,"Entrega Cliente",1,0,'C',1);
          $pdf->Cell(20,4,"Vencimiento",1,0,'C',1);
          $pdf->Cell(20,4,"Dias Cartera",1,0,'C',1);
          $pdf->Cell(20,4,"Dias Vencidos",1,0,'C',1);
          $pdf->Cell(20,4,"Movimiento",1,0,'C',1);
          $pdf->Cell(20,4,"Saldo",1,0,'C',1);
          $nPosy += 4;
          $pdf->setXY($nPosx,$nPosy);

          $pdf->SetWidths(array(18, 18, 20, 30, 20, 20, 20, 22, 20, 20, 20, 20, 20));
          $pdf->SetAligns(array("L", "L", "L", "C", "C", "C", "C", "C", "C", "C", "C", "C", "R"));

          for($i=0;$i<count($mSaldosaFavor);$i++){

            if($nPosy > 196){
              $pdf->AddPage();
              $nPosx = 5;
              $nPosy = 25;
              $pdf->setXY($nPosx,$nPosy);
              $pdf->Line($nPosx, $nPosy, $nPosx+268, $nPosy);
            }

            $pdf->setXY($nPosx,$nPosy);
            $pdf->SetFont('Arial','',6);
            $pdf->Row(array(
                $mSaldosaFavor[$i]['clinomxx'],
                $mSaldosaFavor[$i]['docidxxx'],
                substr($mSaldosaFavor[$i]['pedidoxx'], 0, 60),
                substr($mSaldosaFavor[$i]['comobsxx'], 0, 60),
                $mSaldosaFavor[$i]['document'],
                $mSaldosaFavor[$i]['pucidxxx'],
                $mSaldosaFavor[$i]['comfecxx'],
                $mSaldosaFavor[$i]['comfecnx'],
                $mSaldosaFavor[$i]['comfecvn'],
                $mSaldosaFavor[$i]['diascart'],
                $mSaldosaFavor[$i]['diasvenc'],
                $mSaldosaFavor[$i]['commovxx'],
                number_format($mSaldosaFavor[$i]['saldoxxx'],2,",",".")
            ));

            $nTotSaldos += ($mSaldosaFavor[$i]['commovxx'] == "D") ? $mSaldosaFavor[$i]['saldoxxx'] : ($mSaldosaFavor[$i]['saldoxxx'] * -1);

            $pdf->Line($nPosx, $nPosy, $nPosx, $pdf->getY());
            $pdf->Line($nPosx+18, $nPosy, $nPosx+18, $pdf->getY());
            $pdf->Line($nPosx+36, $nPosy, $nPosx+36, $pdf->getY());
            $pdf->Line($nPosx+56, $nPosy, $nPosx+56, $pdf->getY());
            $pdf->Line($nPosx+86, $nPosy, $nPosx+86, $pdf->getY());
            $pdf->Line($nPosx+106, $nPosy, $nPosx+106, $pdf->getY());
            $pdf->Line($nPosx+126, $nPosy, $nPosx+126, $pdf->getY());
            $pdf->Line($nPosx+146, $nPosy, $nPosx+146, $pdf->getY());
            $pdf->Line($nPosx+168, $nPosy, $nPosx+168, $pdf->getY());
            $pdf->Line($nPosx+188, $nPosy, $nPosx+188, $pdf->getY());
            $pdf->Line($nPosx+208, $nPosy, $nPosx+208, $pdf->getY());
            $pdf->Line($nPosx+228, $nPosy, $nPosx+228, $pdf->getY());
            $pdf->Line($nPosx+248, $nPosy, $nPosx+248, $pdf->getY());
            $pdf->Line($nPosx+268, $nPosy, $nPosx+268, $pdf->getY());

            $pdf->Line($nPosx, $pdf->getY(), $nPosx+268, $pdf->getY());
            $nPosy = $pdf->getY();
          }

          if($nPosy > 196){
            $pdf->AddPage();
            $nPosx = 5;
            $nPosy = 25;
          }
          $pdf->SetFont('Arial','B',7);
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFillColor(150,173,235);
          $pdf->Cell(228,4,"TOTAL SALDOS A FAVOR:",1,0,'R',1);
          $pdf->Cell(40,4,number_format($nTotSaldos,2,",","."),1,0,'R',1);

          $nPosy += 8;

          if($nPosy > 196){
            $pdf->AddPage();
            $nPosx = 5;
            $nPosy = 25;
          }

          $pdf->SetFont('Arial','B',10);
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFillColor(150,173,235);
          $pdf->Cell(268,4,"RECIBOS PROVISIONALES",1,0,'L',1);
          $nPosy += 4;
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFont('Arial','B',7);
          $pdf->SetFillColor(150,173,235);

          $pdf->Cell(18,4,"Importador",1,0,'C',1);
          $pdf->Cell(18,4,"DO",1,0,'C',1);
          $pdf->Cell(20,4,"Pedido",1,0,'C',1);
          $pdf->Cell(30,4,utf8_decode("Observación"),1,0,'C',1);
          $pdf->Cell(20,4,"Comprobante",1,0,'C',1);
          $pdf->Cell(20,4,"Cuenta",1,0,'C',1);
          $pdf->Cell(20,4,"Fecha",1,0,'C',1);
          $pdf->Cell(22,4,"Entrega Cliente",1,0,'C',1);
          $pdf->Cell(20,4,"Vencimiento",1,0,'C',1);
          $pdf->Cell(20,4,"Dias Cartera",1,0,'C',1);
          $pdf->Cell(20,4,"Dias Vencidos",1,0,'C',1);
          $pdf->Cell(20,4,"Movimiento",1,0,'C',1);
          $pdf->Cell(20,4,"Saldo",1,0,'C',1);
          $nPosy += 4;
          $pdf->setXY($nPosx,$nPosy);

          $pdf->SetWidths(array(18, 18, 20, 30, 20, 20, 20, 22, 20, 20, 20, 20, 20));
          $pdf->SetAligns(array("L", "L", "L", "C", "C", "C", "C", "C", "C", "C", "C", "C", "R"));

          for($i=0;$i<count($mRecProv);$i++){

            if($nPosy > 196){
              $pdf->AddPage();
              $nPosx = 5;
              $nPosy = 25;
              $pdf->setXY($nPosx,$nPosy);
              $pdf->Line($nPosx, $nPosy, $nPosx+268, $nPosy);
            }

            $pdf->setXY($nPosx,$nPosy);
            $pdf->SetFont('Arial','',6);
            $pdf->Row(array(
                $mRecProv[$i]['clinomxx'],
                $mRecProv[$i]['docidxxx'],
                substr($mRecProv[$i]['pedidoxx'], 0, 60),
                substr($mRecProv[$i]['comobsxx'], 0, 60),
                $mRecProv[$i]['document'],
                $mRecProv[$i]['pucidxxx'],
                $mRecProv[$i]['comfecxx'],
                $mRecProv[$i]['comfecnx'],
                $mRecProv[$i]['comfecvn'],
                $mRecProv[$i]['diascart'],
                $mRecProv[$i]['diasvenc'],
                $mRecProv[$i]['commovxx'],
                number_format($mRecProv[$i]['saldoxxx'],2,",",".")
            ));

            $nTotProvicionales += ($mRecProv[$i]['commovxx'] == "D") ? $mRecProv[$i]['saldoxxx'] : ($mRecProv[$i]['saldoxxx'] * -1);

            $pdf->Line($nPosx, $nPosy, $nPosx, $pdf->getY());
            $pdf->Line($nPosx+18, $nPosy, $nPosx+18, $pdf->getY());
            $pdf->Line($nPosx+36, $nPosy, $nPosx+36, $pdf->getY());
            $pdf->Line($nPosx+56, $nPosy, $nPosx+56, $pdf->getY());
            $pdf->Line($nPosx+86, $nPosy, $nPosx+86, $pdf->getY());
            $pdf->Line($nPosx+106, $nPosy, $nPosx+106, $pdf->getY());
            $pdf->Line($nPosx+126, $nPosy, $nPosx+126, $pdf->getY());
            $pdf->Line($nPosx+146, $nPosy, $nPosx+146, $pdf->getY());
            $pdf->Line($nPosx+168, $nPosy, $nPosx+168, $pdf->getY());
            $pdf->Line($nPosx+188, $nPosy, $nPosx+188, $pdf->getY());
            $pdf->Line($nPosx+208, $nPosy, $nPosx+208, $pdf->getY());
            $pdf->Line($nPosx+228, $nPosy, $nPosx+228, $pdf->getY());
            $pdf->Line($nPosx+248, $nPosy, $nPosx+248, $pdf->getY());
            $pdf->Line($nPosx+268, $nPosy, $nPosx+268, $pdf->getY());

            $pdf->Line($nPosx, $pdf->getY(), $nPosx+268, $pdf->getY());
            $nPosy = $pdf->getY();
          }

          if($nPosy > 196){
            $pdf->AddPage();
            $nPosx = 5;
            $nPosy = 25;
          }
          $pdf->SetFont('Arial','B',7);
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFillColor(150,173,235);
          $pdf->Cell(228,4,"TOTAL SALDOS PROVISIONALES: ",1,0,'R',1);
          $pdf->Cell(40,4,number_format($nTotProvicionales,2,",","."),1,0,'R',1);
          $nPosy += 4;
          $pdf->SetFont('Arial','B',9);
          $pdf->setXY($nPosx,$nPosy);
          $pdf->SetFillColor(166,222,238);
          $pdf->Cell(228,4,"TOTAL SALDOS A FAVOR: ",1,0,'R',1);
          $pdf->Cell(40,4,number_format($nTotSaldos,2,",","."),1,0,'R',1);
          $nPosy += 4;
          $pdf->setXY($nPosx,$nPosy);
          $pdf->Cell(228,4,"TOTAL CARTERA: ",1,0,'R',1);
          $pdf->Cell(40,4,number_format(($nTotCarVencida+$nTotCartera),2,",","."),1,0,'R',1);
          $nPosy += 4;

          $mNomTotales = array();
          (abs($nTotCarVencida+$nTotCartera)>0) ? $mNomTotales[count($mNomTotales)]="TOTAL CARTERA" :"";
          (abs($nTotSaldos)>0) ? $mNomTotales[count($mNomTotales)]="SALDOS A FAVOR" :"";
          (abs($nTotProvicionales)>0) ? $mNomTotales[count($mNomTotales)]="RECIBOS PROVISIONALES" :"";

          $mTitulo="";

          for($j=0;$j <= (count($mNomTotales)-1);$j++){
          $mTitulo .= $mNomTotales[$j];
          ($j==(count($mNomTotales)-1)) ? "" : $mTitulo .=" - ";
          }

          $pdf->setXY($nPosx,$nPosy);
          $pdf->Cell(228,4,$mTitulo,1,0,'R',1);
          $pdf->Cell(40,4,number_format((($nTotCarVencida+$nTotCartera) - ($nTotProvicionales) + ($nTotSaldos)),2,",","."),1,0,'R',1);

          $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";

          $pdf->Output($cFile);

          if (file_exists($cFile)){
            chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
          } else {
            f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
          }

          echo "<html><script>document.location='$cFile';</script></html>";
        break;
      }
    }
  }

  if ($nSwitch == 0) {
  } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.\n");

    switch ($cTipo) {
    case 2:
      /** Excel
       * No hace nada porque se ejecuta en el fmpro
      **/
    break;
    default: ?>
      <script languaje = "javascript">
        window.close();
      </script>
    <?php break;
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

  function fechaCastellano($dFecha) {
    $nombreCompletoMeses = [
        1 => 'ENERO',
        2 => 'FEBRERO',
        3 => 'MARZO',
        4 => 'ABRIL',
        5 => 'MAYO',
        6 => 'JUNIO',
        7 => 'JULIO',
        8 => 'AGOSTO',
        9 => 'SEPTIEMBRE',
        10 => 'OCTUBRE',
        11 => 'NOVIEMBRE',
        12 => 'DICIEMBRE'
    ];

    $dia = date("d", strtotime($dFecha));
    $anio = date("Y", strtotime($dFecha));
  
    $mes = $nombreCompletoMeses[date("n", strtotime($dFecha))];
    return $dia . " DE ". $mes . " DE " . $anio;
  }
  
  /**
   * Clase para crear Tabla Temporal
   */
  class cEstructurasEstadoCuentaGeneral {

    /**
     * Metodo que se encarga de Crear las Estructuras de las Tablas
     */
    function fnCrearEstructurasEstadoCuentaGeneral() {
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
      $mReturnConexionTM = $this->fnConectarDBEstadoCuentaGeneral();
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

      $cTabla = "memreesg" . $cTabCar;

      $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
      $qNewTab .= "lineaidx INT(11)      	NOT NULL AUTO_INCREMENT, ";  //autoincremental
      $qNewTab .= "comidxxx varchar(1)   	NOT NULL, ";
      $qNewTab .= "comcodxx varchar(4)  	NOT NULL, ";
      $qNewTab .= "comcscxx varchar(20) 	NOT NULL, ";
      $qNewTab .= "document varchar(50)   NOT NULL, ";
      $qNewTab .= "comfecin DATE          NOT NULL, ";
      $qNewTab .= "comfecxx DATE          NOT NULL, ";
      $qNewTab .= "comfecve DATE          NOT NULL, ";
      $qNewTab .= "comfecnx DATE          NOT NULL, ";
      $qNewTab .= "comfecvn DATE          NOT NULL, ";
      $qNewTab .= "diascart decimal(15,2) NOT NULL, ";
      $qNewTab .= "diasvenc decimal(15,2) NOT NULL, ";
      $qNewTab .= "teridxxx varchar(20)   NOT NULL, ";
      $qNewTab .= "clinomxx varchar(250)  NOT NULL, ";
      $qNewTab .= "pucidxxx varchar(10)  	NOT NULL, ";
      $qNewTab .= "pucdesxx varchar(50)   NOT NULL, ";
      $qNewTab .= "commovxx varchar(10)   NOT NULL, ";
      $qNewTab .= "saldoxxx decimal(15,2) NOT NULL, ";
      $qNewTab .= "regestxx varchar(20)   NOT NULL, ";
      $qNewTab .= "pedidoxx varchar(250)  NOT NULL, ";
      $qNewTab .= "docidxxx varchar(20)   NOT NULL, ";
      $qNewTab .= "sucidxxx varchar(3)    NOT NULL, ";
      $qNewTab .= "ctipoxxx varchar(20)   NOT NULL, ";
      $qNewTab .= "comobsxx varchar(200)  NOT NULL, ";
      $qNewTab .= "PRIMARY KEY (lineaidx)) ENGINE=MyISAM "; //MyISAM
      $xNewTab  = mysql_query($qNewTab, $xConexionTM);

      if (!$xNewTab) {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "(" . __LINE__ . ") Error al Crear Tabla Temporal para Reporte Estado Cuenta General." . mysql_error($xConexionTM);
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
    function fnConectarDBEstadoCuentaGeneral() {
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
    } ##function fnConectarDBEstadoCuentaGeneral(){##

    /**
    * Metodo que reinicia la conexion
    */
    function fnReiniciarConexionDBEstadoCuentaGeneral($pConexion){
      global $cHost;  global $cUserHost;  global $cPassHost;

      mysql_close($pConexion);
      $xConexion01 = mysql_connect($cHost,$cUserHost,$cPassHost,TRUE);

      return $xConexion01;
    }##function fnReiniciarConexionDBEstadoCuentaGeneral(){##

  }

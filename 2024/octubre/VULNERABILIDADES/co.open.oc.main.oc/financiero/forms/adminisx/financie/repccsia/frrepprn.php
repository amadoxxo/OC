<?php
  namespace openComex;
	##Estableciendo que el tiempo de ejecucion no se limite

	ini_set('error_reporting', E_ERROR);
	ini_set("display_errors", "1");
	date_default_timezone_set('America/Bogota');

	set_time_limit(0);
	ini_set("memory_limit", "512M");

	/**
	 * Cantidad de Registros para reiniciar conexion
	 */
	define("_NUMREG_",100);
	
	/**
	 * Variable para limitar la cantidad de registros en la busqueda.
	 */
	$nNumReg01 = 50;

	/**
	 * Variables para reemplazar caracteres especiales
	 * @var array
	 */
	$cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
	$cReempl = array('\"',"\'"," "," "," "," ");
	
	/**
	 * Variable para saber si hay o no errores de validacion.
	 * @var number
	 */
	$nSwitch = 0;

	/**
	 * Variable para concatenar los errores de validacion
	 * @var string
	 */
	$cMsj = "";
	
	/**
	 * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales
	 * @var Number
	 */
	$cEjePro = 0;

	/**
	 * Nombre(s) de los archivos en excel generados
	 */
	$cNomArc = "";

	if ($_SERVER["SERVER_PORT"] == "") {
		$vArg = explode(",", $argv[1]);

		if ($vArg[0] == "") {
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "El parametro Id del Proceso no puede ser vacio.\n";
		}

		if ($vArg[1] == "") {
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "El parametro de la Cookie no puede ser vacio.\n";
		}

		if ($nSwitch == 0) {
			$_COOKIE["kDatosFijos"] = $vArg[1];

			include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
			include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utility.php");
			include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php");

			/**
			 * Buscando el ID del proceso
			 */
			$qProBg  = "SELECT * ";
			$qProBg .= "FROM $cBeta.sysprobg ";
			$qProBg .= "WHERE ";
			$qProBg .= "pbaidxxx= \"{$vArg[0]}\" AND ";
			$qProBg .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
			$xProBg = f_MySql("SELECT","",$qProBg,$xConexion01, "");
			// echo $qProBg."~".mysql_num_rows($xProBg);
			if (mysql_num_rows($xProBg) == 0) {
				$xRPB = mysql_fetch_array($xProBg);
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Proceso en Background [{$vArg[0]}] No Existe o ya fue Procesado.\n".$qProBg;
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
		include("../../../../libs/php/utility.php");
		include("../../../../../config/config.php");
		include("../../../../../libs/php/utiprobg.php"); 
	}

	/**
	 *  Cookie fija
	 */
	$kDf = explode("~", $_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb = $kDf[3];
	$kUser = $kDf[4];
	$kLicencia = $kDf[5];
	$swidth = $kDf[6];

	$cSystemPath = OC_DOCUMENTROOT;

	if ($_SERVER["SERVER_PORT"] != "") {
		/**
		 * Validando Licencia
		 */
		$nLic = f_Licencia();
		if ($nLic == 0){
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "Error grave de Seguridad otro usuario ingreso con su clave.\n";
		}
		#Ejecutar proceso en Background
		$_POST['cEjProBg'] = ($_POST['cEjProBg'] != "SI") ? "NO" : $_POST['cEjProBg'];
	}
	
	if ($nSwitch == 0) {
		if ($_SERVER["SERVER_PORT"] != "") {
			if($_POST['cCliId'] != ""){
				$qDatExt  = "SELECT CLIIDXXX ";
				$qDatExt .= "FROM $cAlfa.SIAI0150 ";
				$qDatExt .= "WHERE ";
				$qDatExt .= "CLIIDXXX = \"{$_POST['cCliId']}\" AND ";
				$qDatExt .= "CLICLIXX = \"SI\" AND ";
				$qDatExt .= "REGESTXX = \"ACTIVO\" ";
				$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
				// f_Mensaje(__FILE__, __LINE__,$qDatExt."~".mysql_num_rows($xDatExt));
				if(mysql_num_rows($xDatExt) == 0){
					$nSwitch = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= "El Cliente[{$_POST['cCliId']}] No Existe.\n";
				}
			}
		}
	}
	
	if ($nSwitch == 0) {
		//Cargando datos
		$rTipo    = $_POST['rTipo'];
		$cCliId   = $_POST['cCliId'];
		$cTipCup  = $_POST['cTipCup'];
	}
	
	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;
		
		/**
		 * Trayendo cantidad de registros de la interface
		 */
		$qLoad  = "SELECT ";
		$qLoad .= "SQL_CALC_FOUND_ROWS cliidxxx ";
		$qLoad .= "FROM $cAlfa.SIAI0150 ";
		$qLoad .= "WHERE ";
		$qLoad .= "CLICLIXX = \"SI\" AND ";
		if($_POST['cCliId'] != ""){
			$qLoad .= "CLIIDXXX = \"{$_POST['cCliId']}\" AND ";
		}
		if($_POST['cTipCup'] != ""){
			$cValTipCup = ($_POST['cTipCup'] == "SINCUPO" ? "\"SINCUPO\",\"\"" : "\"{$_POST['cTipCup']}\"");
			$qLoad .= "CLICUPTI = \"$cValTipCup\" AND ";
		}
		$qLoad .= "REGESTXX = \"ACTIVO\" ";
		$qLoad .= "LIMIT 0,1";
    $cIdCountRow = mt_rand(1000000000, 9999999999);
		$xLoad = mysql_query($qLoad, $xConexion01, true, $cIdCountRow);
		// f_Mensaje(__FILE__, __LINE__,$qLoad."~".mysql_num_rows($xLoad));

		mysql_free_result($xLoad);

    $xNumRows   = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD", $xConexion01, false);
		$xRNR       = mysql_fetch_array($xNumRows);
		$nRegistros = $xRNR['CANTIDAD'];
		mysql_free_result($xNumRows);

		$cPost  = "cCliId~".$cCliId."|";
		$cPost .= "cTipCup~".$cTipCup."|";
		$cPost .= "rTipo~".$rTipo;

		$cTablas = "";
	
		$vParBg['pbadbxxx'] = $cAlfa;                                         	//Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                                  	//Modulo
		$vParBg['pbatinxx'] = "REPORTECONTROLCUPOSSIACO";                       //Tipo Interface
		$vParBg['pbatinde'] = "REPORTE CONTROL CUPOS - SIACO";                  //Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                                             	//Sucursal
		$vParBg['doiidxxx'] = "";                                             	//Do
		$vParBg['doisfidx'] = "";                                             	//Sufijo
		$vParBg['cliidxxx'] = "";                                             	//Nit
		$vParBg['clinomxx'] = "";                                             	//Nombre Importador
		$vParBg['pbapostx'] = $cPost;																					  //Parametros para reconstruir Post
		$vParBg['pbatabxx'] = $cTablas;                                         //Tablas Temporales
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
			f_Mensaje(__FILE__, __LINE__, "Proceso en Background Agendado con Exito.");
			?>
			<script language = "javascript">
				parent.fmwork.fnRecargar();
			</script>
			<?php
		} else {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}
		}
	} // fin del if ($_SERVER["SERVER_PORT"] != "" && $_POST['cEjProBg'] == "SI" && $nSwitch == 0)

	if ($cEjePro == 0) {
    $vDatos['tipotabl'] = "FACTURAS";
    $mReturnFacturas = fnCrearTablaFacturas($vDatos);
    if($mReturnFacturas[0] == "false"){
      $nSwitch = 1;
      for($nR=1;$nR<count($mReturnFacturas);$nR++){
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .=  $mReturnFacturas[$nR]."\n";
      }
    }

    $vDatos['tipotabl'] = "CXC";
    $mReturnCxC = fnCrearTablaFacturas($vDatos);
    if($mReturnCxC[0] == "false"){
      $nSwitch = 1;
      for($nR=1;$nR<count($mReturnCxC);$nR++){
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .=  $mReturnCxC[$nR]."\n";
      }
    }

		if ($nSwitch == 0) {
			/**
			 * Inicializo array para despues guardarlos en la tabla temporal
			 */
			$mCupos  = array();

			// Busco todos los clientes del sistema con estado ACTIVO
			$qClientes  = "SELECT ";
			$qClientes .= "CLIIDXXX AS cliidxxx,";
			$qClientes .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))  AS clinomxx,";
			$qClientes .= "\"IMPORTADOR\" AS desdexxx,";
			$qClientes .= "CLICLIXX AS cliclixx,";
			$qClientes .= "CLICUPTI AS clicupxx,";
			$qClientes .= "CLICUPCL AS clicupcl,";
			$qClientes .= "CLICUPOP AS clicupop ";
			$qClientes .= "FROM $cAlfa.SIAI0150 ";
			$qClientes .= "WHERE ";
			$qClientes .= "CLICLIXX = \"SI\" AND ";
			if($_POST['cCliId'] != ""){
				$qClientes .= "CLIIDXXX = \"{$_POST['cCliId']}\" AND ";
			}
			if($_POST['cTipCup'] != ""){
				$cValTipCup = ($_POST['cTipCup'] == "SINCUPO" ? "\"SINCUPO\",\"\"" : "\"{$_POST['cTipCup']}\"");
				$qClientes .= "CLICUPTI IN ($cValTipCup) AND ";
			}
			$qClientes .= "REGESTXX = \"ACTIVO\" ";
			$qClientes .= "ORDER BY ABS(CLIIDXXX)";
			$xClientes  = f_MySql("SELECT","",$qClientes,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qClientes." ~ ".mysql_num_rows($xClientes));
			
			$nCanReg = 0;
			$cClientes = "";
			while ($xRCLI = mysql_fetch_array($xClientes)) {
				$nCanReg++;
				if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }
				$cClientes .= "\"".$xRCLI['cliidxxx']."\",";
				$mCupos["{$xRCLI['cliidxxx']}"] = $xRCLI;
			}
			mysql_free_result($xClientes);
			$cClientes = substr($cClientes, 0, -1);
			// FIN Busco todos los clientes del sistema sin importar el estado del cliente

			// Busco los cupos autortizados por operacion en el sistema y los agrupo por cliente en la matriz
			$qCupAut  = "SELECT ";
			$qCupAut .= "cliidxxx,";
			$qCupAut .= "doccupxx,";
			$qCupAut .= "doccupaf ";
			$qCupAut .= "FROM $cAlfa.sys00121 ";
			$qCupAut .= "WHERE ";
			if($_POST['cCliId'] != ""){
				$qCupAut .= "cliidxxx = \"{$_POST['cCliId']}\" AND ";
			} else { 
        $qCupAut .= "cliidxxx IN ($cClientes) AND ";
      }
			$qCupAut .= "regestxx = \"ACTIVO\" ";
			$qCupAut .= "ORDER BY cliidxxx";
			$xCupAut  = f_MySql("SELECT","",$qCupAut,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qCupAut." ~ ".mysql_num_rows($xCupAut));

			$nCanReg = 0;
			while ($xRCUP = mysql_fetch_array($xCupAut)) {

				$nCanReg++;
				if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }

				$mCupos["{$xRCUP['cliidxxx']}"]['clicupsf'] += $xRCUP['doccupxx'];
			}
			mysql_free_result($xCupAut);
			// Fin Busco los cupos autortizados por operacion en el sistema y los agrupo por cliente en la matriz

      // Buscando cuentas solo los saldos de cartera
      $qSaldos  = "SELECT pucidxxx, ctoclaxf ";
      $qSaldos .= "FROM $cAlfa.fpar0119 ";
      $qSaldos .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fpar0119.pucidxxx ";
      $qSaldos .= "WHERE ";
      $qSaldos .= "$cAlfa.fpar0119.ctoclaxf IN (\"SCLIENTE\",\"SCLIENTEUSD\",\"SAGENCIA\",\"SAGENCIAIP\",\"SAGENCIAPCC\",\"SAGENCIAUSD\",\"SAGENCIAUSDIP\",\"SAGENCIAUSDPCC\") AND ";
      $qSaldos .= "$cAlfa.fpar0115.pucdetxx IN (\"C\",\"P\") AND ";
      $qSaldos .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
      $xSaldos  = f_MySql("SELECT","",$qSaldos,$xConexion01,"");
      $cCuentas = "";
      $vCueCli  = array();
      $vCueIng  = array();
      $vCuePcc  = array();
      $cCuePCC  = "";
      while ($xRDS = mysql_fetch_array($xSaldos)){
        $cCuentas .= "\"{$xRDS['pucidxxx']}\",";
        switch($xRDS['ctoclaxf']) {
          case "SCLIENTE":
          case "SCLIENTEUSD":
            //Saldo a favor del cliente
            $vCueCli[] = "{$xRDS['pucidxxx']}";
          break;
          case "SAGENCIA":
          case "SAGENCIAUSD":
          case "SAGENCIAIP":
          case "SAGENCIAUSDIP":
            //Ingresos propios
            $vCueIng[] = "{$xRDS['pucidxxx']}";
          break;
          case "SAGENCIAPCC":
          case "SAGENCIAUSDPCC";
            //Pagos por cuenta del cliente
            $vCuePcc[] = "{$xRDS['pucidxxx']}";
            $cCuePCC .= "\"{$xRDS['pucidxxx']}\",";
          break;
          default:
            //Ingresos Propios
            $vCueIng[] = "{$xRDS['pucidxxx']}";
          break;
        }
      }
      mysql_free_result($xSaldos);
      $cCuentas = substr($cCuentas, 0, -1);
      $cCuePCC  = substr($cCuePCC, 0, -1);

			// Busco pagos a terceros y anticipos de los tramites no facturados
			$cCtoPCC = "";
      $cCtoAnt = "";
			$cCuePag = "";

      //Buscando conceptos de cuenta por pagar
			$qCuePag  = "SELECT DISTINCT ";
			$qCuePag .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AS pucidxxx ";
			$qCuePag .= "FROM $cAlfa.fpar0115 ";
			$qCuePag .= "WHERE ";
			$qCuePag .= "$cAlfa.fpar0115.pucdetxx = \"P\" AND ";
			$qCuePag .= "$cAlfa.fpar0115.regestxx = \"ACTIVO\"";
			$xCuePag = f_MySql("SELECT","",$qCuePag,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qCuePag."~".mysql_num_rows($xCuePag));
			$nCanReg = 0;
			while($xRCP = mysql_fetch_array($xCuePag)) {
				$nCanReg++;
				if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }

				$cCuePag .= "\"{$xRCP['pucidxxx']}\",";
			}
			$cCuePag = substr($cCuePag,0,strlen($cCuePag)-1);
			mysql_free_result($xCuePag);

			//Buscano conceptos de causaciones automaticas
			$qCAyP121  = "SELECT DISTINCT ";
			$qCAyP121 .= "$cAlfa.fpar0121.pucidxxx,";
			$qCAyP121 .= "$cAlfa.fpar0121.ctoidxxx ";
			$qCAyP121 .= "FROM $cAlfa.fpar0121 ";
			$qCAyP121 .= "WHERE ";
			$qCAyP121 .= "$cAlfa.fpar0121.regestxx = \"ACTIVO\"";
			$xCAyP121 = f_MySql("SELECT","",$qCAyP121,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qCAyP121."~".mysql_num_rows($xCAyP121));
			$cCAyP121 = "";
			$nCanReg = 0;
			while($xRCP121 = mysql_fetch_array($xCAyP121)) {
				$nCanReg++;
				if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }

        if ($xRCP121['pucidxxx'] == "2805050000") {
          $cCtoPCC .= "\"{$xRCP121['pucidxxx']}~{$xRCP121['ctoidxxx']}\",";
        }				
			}
			mysql_free_result($xCAyP121);

			//Buscando conceptos
			$qCtoPCC  = "SELECT DISTINCT ";
			$qCtoPCC .= "$cAlfa.fpar0119.pucidxxx,";
			$qCtoPCC .= "$cAlfa.fpar0119.ctoidxxx,";
			$qCtoPCC .= "$cAlfa.fpar0119.ctopccxx,";
			$qCtoPCC .= "$cAlfa.fpar0119.ctoantxx ";
			$qCtoPCC .= "FROM $cAlfa.fpar0119 ";
			$qCtoPCC .= "WHERE ";
			$qCtoPCC .= "($cAlfa.fpar0119.ctopccxx = \"SI\" OR $cAlfa.fpar0119.ctoantxx = \"SI\") AND ";
			$qCtoPCC .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\"";
			$xCtoPCC = f_MySql("SELECT","",$qCtoPCC,$xConexion01,"");
			//f_Mensaje(__FILE__,__LINE__,$qCtoPCC."~".mysql_num_rows($xCtoPCC));
			$nCanReg = 0;
			while($xRCAP = mysql_fetch_array($xCtoPCC)) {
				$nCanReg++;
				if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }

				if ($xRCAP['ctopccxx'] == "SI" && $xRCAP['pucidxxx'] == "2805050000") {
					$cCtoPCC .= "\"{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}\",";
				} elseif ($xRCAP['ctoantxx'] == "SI" && $xRCAP['pucidxxx'] == "2805050000") {
          $cCtoAnt .= "\"{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}\",";
				}
			}
			$cCtoPCC = substr($cCtoPCC,0,strlen($cCtoPCC)-1);
      $cCtoAnt = substr($cCtoAnt,0,strlen($cCtoAnt)-1);
			mysql_free_result($xCtoPCC);

      //Se consultan los ultimos 8 años
      $nAnoIni = ((date('Y')-8) <  $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (date('Y')-8);

      //Tabla temporal con las CxC de los pagos a terceros
      for ($nAno=$nAnoIni; $nAno<=date('Y'); $nAno++) {
        $qFactura  = "SELECT ";
        $qFactura .= "comidxxx,";
        $qFactura .= "comcodxx,";
        $qFactura .= "comcscxx,";
        $qFactura .= "comcsc2x,";
        $qFactura .= "commemod,";
        $qFactura .= "comfecxx ";
        $qFactura .= "FROM $cAlfa.fcoc$nAno ";
        $qFactura .= "WHERE ";
        $qFactura .= "comidxxx  = \"F\" AND ";
        $qFactura .= "commemod != \"\" AND ";
        $qFactura .= "regestxx = \"ACTIVO\"";
        // echo $qFactura."<br>";

        $qInsert = "INSERT INTO $cAlfa.$mReturnFacturas[1] $qFactura";
        $xInsert = mysql_query($qInsert,$xConexion01);
        // echo $qInsert."~".mysql_error($xInsert)."<br><br>";
        $xFree = mysql_free_result($xInsert);

        $qFactura  = "SELECT ";
        $qFactura .= "comidxxx,";
        $qFactura .= "comcodxx,";
        $qFactura .= "comcscxx,";
        $qFactura .= "comcsc2x,";
        $qFactura .= "teridxxx,";
        $qFactura .= "pucidxxx,";
        $qFactura .= "comfecxx ";
        $qFactura .= "FROM $cAlfa.fcod$nAno ";
        $qFactura .= "WHERE ";
        $qFactura .= "comidxxx = \"F\" AND ";
        $qFactura .= "pucidxxx IN ($cCuePCC) AND ";
        $qFactura .= "regestxx = \"ACTIVO\"";
        // echo $qFactura."<br>";
        
        $qInsert = "INSERT INTO $cAlfa.$mReturnCxC[1] $qFactura";
        $xInsert = mysql_query($qInsert,$xConexion01);
        // echo $qInsert."~".mysql_error($xInsert)."<br><br>";
        $xFree = mysql_free_result($xInsert);
      }

			for ($nAno=$nAnoIni; $nAno<=date('Y'); $nAno++) {
        // Busco la cartera total, cartera de PCC, cartera de IP y cartera a favor del cliente
        // Se busca desde el movimiento contable
        $qDatMov  = "SELECT ";
        $qDatMov .= "$cAlfa.fcod$nAno.teridxxx, ";
        $qDatMov .= "$cAlfa.fcod$nAno.pucidxxx, ";
        $qDatMov .= "SUM(if ($cAlfa.fcod$nAno.commovxx = \"D\", $cAlfa.fcod$nAno.comvlrxx, $cAlfa.fcod$nAno.comvlrxx*-1)) AS comvlrxx ";
        $qDatMov .= "FROM $cAlfa.fcod$nAno ";
        $qDatMov .= "WHERE  ";
        if($_POST['cCliId'] != ""){
          $qDatMov .= "$cAlfa.fcod$nAno.teridxxx = \"{$_POST['cCliId']}\" AND ";
        }
        $qDatMov .= "$cAlfa.fcod$nAno.pucidxxx IN ($cCuentas) AND ";
        $qDatMov .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
        $qDatMov .= "GROUP BY $cAlfa.fcod$nAno.teridxxx,$cAlfa.fcod$nAno.pucidxxx ";
        $xDatMov = mysql_query($qDatMov,$xConexion01);
        // if ($_COOKIE['kUsrId'] == "ADMIN") {
        //   echo $qDatMov."~".mysql_num_rows($xDatMov)."<br>";
        // }
        while ($xRDM = mysql_fetch_array($xDatMov)) {
          // Pagos por cuenta del cliente
          if (in_array("{$xRDM['pucidxxx']}", $vCuePcc)) {
            $mCupos["{$xRDM['teridxxx']}"]['clicxcpt'] += $xRDM['comvlrxx'];
            $mCupos["{$xRDM['teridxxx']}"]['cxctotal'] += $xRDM['comvlrxx'];
          }
          // Ingresos Propios
          if (in_array("{$xRDM['pucidxxx']}", $vCueIng)) {
            $mCupos["{$xRDM['teridxxx']}"]['clicxcip'] += $xRDM['comvlrxx'];
            $mCupos["{$xRDM['teridxxx']}"]['cxctotal'] += $xRDM['comvlrxx'];
          }
          // Saldos a Favor
          if (in_array("{$xRDM['pucidxxx']}", $vCueCli)) {
            $mCupos["{$xRDM['teridxxx']}"]['clicxcsa'] += $xRDM['comvlrxx'];
          }
        }
        mysql_free_result($xDatMov);

				## Pagos por Cuenta de Cliente Cancelados Facturados y no Facturados ##
				$qPCC  = "SELECT ";
				$qPCC .= "comidxxx,";
				$qPCC .= "comcodxx,";
				$qPCC .= "comcscxx,";
				$qPCC .= "comcsc2x,";
				$qPCC .= "comseqxx,";
        $qPCC .= "comfecxx,";
				$qPCC .= "ctoidxxx,";
				$qPCC .= "pucidxxx,";
				$qPCC .= "teridxxx,";
				$qPCC .= "terid2xx,";
				$qPCC .= "sucidxxx,";
				$qPCC .= "docidxxx,";
				$qPCC .= "docsufxx,";
				$qPCC .= "comvlrxx,";
        $qPCC .= "comfacxx ";
				$qPCC .= "FROM $cAlfa.fcod$nAno ";
				$qPCC .= "WHERE ";
				$qPCC .= "comidxxx NOT IN (\"F\") AND ";
				$qPCC .= "CONCAT(pucidxxx,\"~\",ctoidxxx) IN ($cCtoPCC) AND ";
				if($_POST['cCliId'] != ""){
					$qPCC .= "teridxxx = \"{$_POST['cCliId']}\" AND ";
				} else {
          $qPCC .= "teridxxx IN ($cClientes) AND ";
        }
				if ($cTerExc != "") {
					$qPCC .= "terid2xx NOT IN ($cTerExc) AND ";
				}
				$qPCC .= "regestxx = \"ACTIVO\" ";
        $qPCC .= "ORDER BY  comidxxx, comcodxx, comcscxx, comcsc2x, ABS(comseqxx)";
				$xPCC  = f_MySql("SELECT","",$qPCC,$xConexion01,"");
        // if ($_COOKIE['kUsrId'] == "ADMIN") {
        //   echo $qPCC." ~ ".mysql_num_rows($xPCC)."<br><br>";
        // }
				//f_Mensaje(__FILE__,__LINE__,$qPCC." ~ ".mysql_num_rows($xPCC));
				
				$vPccCanCxP = array(); //CxP Pagos a terceros ya buscados
        $mPccEst    = array(); //Estado pago a tercero
        $cComIni    = "";
        $vCuePag    = array();
				$nCanReg    = 0; 
        while ($xRPCC = mysql_fetch_array($xPCC)) {
					$nCanReg++;
					if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }

          if ($xRPCC['comfacxx'] != "") {
            //Factura del comprobante
            $vFac = explode("-",$xRPCC['comfacxx']);

            //Varible que indica que la factura del pcc aun no ha sido cancelada
            $nFacCan = 0;

            // echo "{$xRPCC['comidxxx']}~{$xRPCC['comcodxx']}~{$xRPCC['comcscxx']}~{$xRPCC['comcsc2x']}~{$xRPCC['comseqxx']}<br>";

            // Si el pago ya fue facturado y no se ha pagado la factura se incluye el valor 
            // en la columna
            // Se busca en las facturas todas aquellas que en el campo de pagos a terceros
            // commemod contenga la causacion y se verifica si esas facturas ya fueron pagas
            $qFactura  = "SELECT ";
            $qFactura .= "comidxxx, ";
            $qFactura .= "comcodxx, ";
            $qFactura .= "comcscxx, ";
            $qFactura .= "comcsc2x, ";
            $qFactura .= "comfecxx, ";
            $qFactura .= "commemod  ";
            $qFactura .= "FROM $cAlfa.$mReturnFacturas[1] ";
            $qFactura .= "WHERE ";
            $qFactura .= "comidxxx = \"{$vFac[0]}\" AND ";
            $qFactura .= "comcodxx = \"{$vFac[1]}\" AND ";
            $qFactura .= "comcscxx = \"{$vFac[2]}\" AND ";
            $qFactura .= "comcsc2x = \"{$vFac[2]}\" AND ";
            $qFactura .= "commemod LIKE \"%{$xRPCC['comcscxx']}%\"";
            $xFactura = mysql_query($qFactura,$xConexion01);
            // echo $qFactura." ~ ".mysql_num_rows($xFactura)."<br><br>";
            while ($xRF = mysql_fetch_array($xFactura)) {
              $mPcc      = f_Explode_Array($xRF['commemod'],"|","~");
              $nEncontro = 0;
              for($nP=0; $nP<count($mPcc); $nP++) {
                //Extrayendo el DO
                $vAuxTramite = explode("-",$mPcc[$nP][14]);
                $cSucIdAux = $vAuxTramite[0];
                $cDocIdAux = "";
                for($nT=1; $nT<count($vAuxTramite)-1;$nT++){
                  $cDocIdAux .= $vAuxTramite[$nT]."-";
                }
                $cDocIdAux  = substr($cDocIdAux, 0, strlen($cDocIdAux)-1);
                $cDocSufAux = $vAuxTramite[count($vAuxTramite)-1];

                //Identifico si el pago existe en la factura
                if ("{$xRPCC['comidxxx']}" == "{$mPcc[$nP][3]}"  && 
                    "{$xRPCC['comcodxx']}" == "{$mPcc[$nP][4]}"  && 
                    "{$xRPCC['comcscxx']}" == "{$mPcc[$nP][5]}"  && 
                    "{$xRPCC['comseqxx']}" == "{$mPcc[$nP][6]}"  && 
                    "{$xRPCC['ctoidxxx']}" == "{$mPcc[$nP][1]}"  && 
                    "{$xRPCC['pucidxxx']}" == "{$mPcc[$nP][9]}"  && 
                    "{$xRPCC['teridxxx']}" == "{$mPcc[$nP][11]}" && 
                    "{$xRPCC['terid2xx']}" == "{$mPcc[$nP][12]}" && 
                    "{$xRPCC['sucidxxx']}" == "$cSucIdAux" && 
                    "{$xRPCC['docidxxx']}" == "$cDocIdAux" && 
                    "{$xRPCC['docsufxx']}" == "$cDocSufAux") {
                  $nEncontro = 1;
                  $nP = count($mPcc);
                }
              }

              if ($nEncontro == 1) {
                // Se busca si la factura ya fue cancelada
                $qFacCxC  = "SELECT * ";
                $qFacCxC .= "FROM $cAlfa.$mReturnCxC[1] ";
                $qFacCxC .= "WHERE ";
                $qFacCxC .= "comidxxx = \"{$xRF['comidxxx']}\" AND ";
                $qFacCxC .= "comcodxx = \"{$xRF['comcodxx']}\" AND ";
                $qFacCxC .= "comcscxx = \"{$xRF['comcscxx']}\" AND ";
                $qFacCxC .= "comcsc2x = \"{$xRF['comcsc2x']}\" AND ";
                $qFacCxC .= "comfecxx = \"{$xRF['comfecxx']}\"";
                $xFacCxC = mysql_query($qFacCxC,$xConexion01);
                // echo $qFacCxC." ~ ".mysql_num_rows($xFacCxC)."<br><br>";
                while ($xRFCxC = mysql_fetch_array($xFacCxC)) {
                  //Buscando en CxC
                  $qCartera  = "SELECT ";
                  $qCartera .= "comidxxx,";
                  $qCartera .= "comcodxx,";
                  $qCartera .= "comcscxx,";
                  $qCartera .= "terid2xx,";
                  $qCartera .= "comsaldo,";
                  $qCartera .= "regfcrex ";
                  $qCartera .= "FROM $cAlfa.fcxc0000 ";
                  $qCartera .= "WHERE ";
                  $qCartera .= "comidxxx = \"{$xRFCxC['comidxxx']}\" AND ";
                  $qCartera .= "comcodxx = \"{$xRFCxC['comcodxx']}\" AND ";
                  $qCartera .= "comcscxx = \"{$xRFCxC['comcscxx']}\" AND ";
                  $qCartera .= "comseqxx = \"001\" AND ";
                  $qCartera .= "teridxxx = \"{$xRFCxC['teridxxx']}\" AND ";
                  $qCartera .= "pucidxxx = \"{$xRFCxC['pucidxxx']}\" LIMIT 0,1 ";
                  $xCartera  = f_MySql("SELECT","",$qCartera,$xConexion01,"");
                  // echo $qCartera." ~ ".mysql_num_rows($xCartera)."<br>";
                  if(mysql_num_rows($xCartera) > 0) {
                    $vCartera = mysql_fetch_array($xCartera);
                    $nFacCan++;
                  }
                }
              }
            }

            if($nFacCan > 0) {
              //Todavia no ha sido cancelada por lo que debe incluirse
              $mCupos["{$xRPCC['teridxxx']}"]['clipcccx'] += $xRPCC['comvlrxx'];
              // if ($_COOKIE['kUsrId'] == "ADMIN") {
              //   echo "{$xRPCC['comidxxx']}~{$xRPCC['comcodxx']}~{$xRPCC['comcscxx']}~{$xRPCC['comcsc2x']}~{$xRPCC['comseqxx']}~{$xRPCC['comfecxx']}~{$xRPCC['comvlrxx']}<br>";
              // }
            }
          } else {
            //Pago no facturado
            //Cuando cambia el comprobante se busca si este tiene cxc
            if ($cComIni != "{$xRPCC['comidxxx']}~{$xRPCC['comcodxx']}~{$xRPCC['comcscxx']}~{$xRPCC['comcsc2x']}") {
              $cComIni = "{$xRPCC['comidxxx']}~{$xRPCC['comcodxx']}~{$xRPCC['comcscxx']}~{$xRPCC['comcsc2x']}";
              $vCuePag = array();

              $qCuePag  = "SELECT ";
              $qCuePag .= "comidxxx,";
              $qCuePag .= "comcodxx,";
              $qCuePag .= "comcscxx,";
              $qCuePag .= "comseqxx,";
              $qCuePag .= "teridxxx,";
              $qCuePag .= "terid2xx,";
              $qCuePag .= "pucidxxx,";
              $qCuePag .= "comvlrxx ";
              $qCuePag .= "FROM $cAlfa.fcod$nAno ";
              $qCuePag .= "WHERE ";
              $qCuePag .= "comidxxx = \"{$xRPCC['comidxxx']}\" AND ";
              $qCuePag .= "comcodxx = \"{$xRPCC['comcodxx']}\" AND ";
              $qCuePag .= "comcscxx = \"{$xRPCC['comcscxx']}\" AND ";
              $qCuePag .= "comcsc2x = \"{$xRPCC['comcsc2x']}\" AND ";
              $qCuePag .= "pucidxxx IN ($cCuePag) LIMIT 0,1 ";
              $xCuePag  = f_MySql("SELECT","",$qCuePag,$xConexion01,"");
              // echo $qCuePag." ~ ".mysql_num_rows($xCuePag)."<br>";
              // f_Mensaje(__FILE__,__LINE__,$qCuePag." ~ ".mysql_num_rows($xCuePag));
              if(mysql_num_rows($xCuePag) > 0){
                $vCuePag = mysql_fetch_array($xCuePag);

                if (!in_array("{$vCuePag['comidxxx']}~{$vCuePag['comcodxx']}~{$vCuePag['comcscxx']}~001~{$vCuePag['teridxxx']}~{$vCuePag['pucidxxx']}", $vPccCanCxP)) {
                  $vPccCanCxP[] = "{$vCuePag['comidxxx']}~{$vCuePag['comcodxx']}~{$vCuePag['comcscxx']}~001~{$vCuePag['teridxxx']}~{$vCuePag['pucidxxx']}";

                  //Buscando en CxP
                  $qCartera  = "SELECT ";
                  $qCartera .= "comidxxx,";
                  $qCartera .= "comcodxx,";
                  $qCartera .= "comcscxx,";
                  $qCartera .= "terid2xx,";
                  $qCartera .= "comsaldo,";
                  $qCartera .= "regfcrex ";
                  $qCartera .= "FROM $cAlfa.fcxp0000 ";
                  $qCartera .= "WHERE ";
                  $qCartera .= "comidxxx = \"{$vCuePag['comidxxx']}\" AND ";
                  $qCartera .= "comcodxx = \"{$vCuePag['comcodxx']}\" AND ";
                  $qCartera .= "comcscxx = \"{$vCuePag['comcscxx']}\" AND ";
                  $qCartera .= "comseqxx = \"001\" AND ";
                  $qCartera .= "teridxxx = \"{$vCuePag['teridxxx']}\" AND ";
                  $qCartera .= "pucidxxx = \"{$vCuePag['pucidxxx']}\" LIMIT 0,1 ";
                  $xCartera  = f_MySql("SELECT","",$qCartera,$xConexion01,"");
                  $mPccEst["{$vCuePag['comidxxx']}~{$vCuePag['comcodxx']}~{$vCuePag['comcscxx']}~001~{$vCuePag['teridxxx']}~{$vCuePag['pucidxxx']}"] = "SI"; //si existe la CxP
                  if(mysql_num_rows($xCartera) == 0) {
                    $mPccEst["{$vCuePag['comidxxx']}~{$vCuePag['comcodxx']}~{$vCuePag['comcscxx']}~001~{$vCuePag['teridxxx']}~{$vCuePag['pucidxxx']}"] = "NO";
                  }
                }
              }
            }
            if ($mPccEst["{$vCuePag['comidxxx']}~{$vCuePag['comcodxx']}~{$vCuePag['comcscxx']}~001~{$vCuePag['teridxxx']}~{$vCuePag['pucidxxx']}"] == "NO") {
              $mCupos["{$xRPCC['teridxxx']}"]['clipcccx'] += $xRPCC['comvlrxx'];
              // if ($_COOKIE['kUsrId'] == "ADMIN") {
              //   echo "{$vCuePag['comidxxx']}~{$vCuePag['comcodxx']}~{$vCuePag['comcscxx']}~001~{$vCuePag['teridxxx']}~{$vCuePag['pucidxxx']}<br>";
              // }
            }
          }
				}
				mysql_free_result($xPCC);
				## Fin Pagos por Cuenta de Cliente Cancelados Facturados y no Facturados ##

				//Buscando Anticipos no facturados
				## Movimiento Contable ##
				$qAnticipos  = "SELECT ";
				$qAnticipos .= "teridxxx,";
				$qAnticipos .= "SUM(IF(commovxx=\"D\",comvlrxx,comvlrxx*-1)) AS comvlrxx ";
				$qAnticipos .= "FROM $cAlfa.fcod$nAno ";
				$qAnticipos .= "WHERE ";
        $qAnticipos .= "CONCAT(pucidxxx,\"~\",ctoidxxx) IN ($cCtoAnt) AND ";
				if($_POST['cCliId'] != ""){
					$qAnticipos .= "teridxxx = \"{$_POST['cCliId']}\" AND ";
				} else {
          $qAnticipos .= "teridxxx IN ($cClientes) AND ";
        }
        $qAnticipos .= "comfacxx = \"\" AND ";
				$qAnticipos .= "regestxx = \"ACTIVO\" ";
				$qAnticipos .= "GROUP BY teridxxx";
				$xAnticipos  = f_MySql("SELECT","",$qAnticipos,$xConexion01,"");
				// echo "Anticipos: ".$qAnticipos." ~ ".mysql_num_rows($xAnticipos)."<br>";
				//f_Mensaje(__FILE__,__LINE__,$qAnticipos." ~ ".mysql_num_rows($xAnticipos));

				$nCanReg = 0;
				while ($xRA = mysql_fetch_array($xAnticipos)) {
					$nCanReg++;
					if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }

					$mCupos["{$xRA['teridxxx']}"]['cliantxx'] += $xRA['comvlrxx'];
				}
				mysql_free_result($xRA);
				## Fin de Movimiento Contable ##

				## Movimiento Caja Menor ##
				$qRecAnt  = "SELECT ";
				$qRecAnt .= "teridxxx,";
				$qRecAnt .= "SUM(IF(commovxx=\"D\",comvlrxx,comvlrxx*-1)) AS comvlrxx ";
				$qRecAnt .= "FROM $cAlfa.fcme$nAno ";
				$qRecAnt .= "WHERE ";
				$qRecAnt .= "CONCAT(pucidxxx,\"~\",ctoidxxx) IN ($cCtoAnt) AND ";
				if($_POST['cCliId'] != ""){
					$qRecAnt .= "teridxxx = \"{$_POST['cCliId']}\" AND ";
				} else {
          $qRecAnt .= "teridxxx IN ($cClientes) AND ";
        }
        $qRecAnt .= "comfacxx = \"\" AND ";
				$qRecAnt .= "regestxx IN (\"PROVISIONAL\",\"ACTIVO\") ";
				$qRecAnt .= "GROUP BY teridxxx";
				$xRecAnt = f_MySql("SELECT","",$qRecAnt,$xConexion01,"");
				// echo "Anticipos: ".$qRecAnt." ~ ".mysql_num_rows($xRecAnt)."<br>";
				//f_Mensaje(__FILE__,__LINE__,$qRecAnt." ~ ".mysql_num_rows($xRecAnt));

				$nCanReg = 0;
				while ($xRCA = mysql_fetch_array($xRecAnt)) {
					$nCanReg++;
					if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }

					$mCupos["{$xRCA['teridxxx']}"]['cliantxx'] += $xRCA['comvlrxx'];
				}
				mysql_free_result($xRecAnt);
				## Fin de Movimiento Caja Menor ##
				//Fin Buscando Anticipos no facturados

				## Recibos de caja Provisionales ##
				$qRecCaP  = "SELECT ";
				$qRecCaP .= "comidxxx,";
				$qRecCaP .= "comcodxx,";
				$qRecCaP .= "comcscxx,";
				$qRecCaP .= "comcsc2x,";
				$qRecCaP .= "teridxxx,";
				$qRecCaP .= "comvlr01 ";
				$qRecCaP .= "FROM $cAlfa.fcoc$nAno ";
				$qRecCaP .= "WHERE ";
				$qRecCaP .= "comidxxx NOT IN (\"F\") AND ";
				if($_POST['cCliId'] != ""){
					$qRecCaP .= "teridxxx = \"{$_POST['cCliId']}\" AND ";
				} else {
          $qRecCaP .= "teridxxx IN ($cClientes) AND ";
        }
				$qRecCaP .= "regestxx = \"PROVISIONAL\" ";
				$qRecAnt .= "GROUP BY teridxxx";
				$xRecCaP  = f_MySql("SELECT","",$qRecCaP,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qRecCaP." ~ ".mysql_num_rows($xRecCaP));

				$nCanReg = 0;
				while ($xRRC = mysql_fetch_array($xRecCaP)) {
					$nCanReg++;
					if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }

					$mCupos["{$xRRC['teridxxx']}"]['clirecxx'] += $xRRC['comvlr01'];
				}
				mysql_free_result($xRRC);
				## Fin de Recibos de caja Provisionales ##
			}
			##Fin Busco pagos a terceros de los tramites no facturados
			
			// Calculo la financiacion y el saldo del cupo
			$nCupCli = 0;
			$nTotCar = 0;
			$nTotFin = 0;
			$nTotRca = 0;
			$nTotCcc = 0;
			$nTotSal = 0;
			$nCupSF  = 0;
			$nCarPCC = 0;
			$nCarIP  = 0;
			$nSalAC  = 0;

			$mDatos = array();

			// echo "<pre>";
			// print_r($mCupos);
			// echo "</pre>";

			foreach ($mCupos as $i => $cValue) {

				$nCanReg01++;
				if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }

				$nMostrar = ""; //Indica si debe mostrarse el registro

        //Financiacion = PCC NF Cancelados - Anticipos NF - Recibos de caja provisionales - saldos a favor
        //El aniticipo y los saldos a favor vienen negativos, por eso se suma
				$mCupos[$i]['clifinan'] = $mCupos[$i]['clipcccx'] + $mCupos[$i]['cliantxx'] - $mCupos[$i]['clirecxx'] + $mCupos[$i]['clicxcsa'];

				switch ($mCupos[$i]['clicupxx']) {
					case "LIMITADO":
						$mCupos[$i]['clicupcl'] = $mCupos[$i]['clicupcl'];
						// Saldo = Cupo Cliente + Cupo Autorizado - Financiacion
						$mCupos[$i]['clisaldo'] = $mCupos[$i]['clicupcl'] + $mCupos[$i]['clicupsf'] - $mCupos[$i]['clifinan'];
						$nMostrar = "SI";
					break;
					case "ILIMITADO":
						$mCupos[$i]['clicupcl'] = "ILIMITADO";
						$mCupos[$i]['clisaldo'] = "ILIMITADO";
						$nMostrar = "SI";
					break;
					case "LIMITADO/ILIMITADO":
						$mCupos[$i]['clicupcl'] = $mCupos[$i]['clicupcl'];
						// Saldo = Cupo Cliente + Cupo Autorizado - Financiacion
						$mCupos[$i]['clisaldo'] = $mCupos[$i]['clicupcl'] + $mCupos[$i]['clicupsf'] - $mCupos[$i]['clifinan'];
						$nMostrar = "SI";
					break;
					case "ILIMITADO/LIMITADO":
						$mCupos[$i]['clicupcl'] = "ILIMITADO";
						$mCupos[$i]['clisaldo'] = "ILIMITADO";
						$nMostrar = "SI";
					break;
					case "SINCUPO": default:
						$mCupos[$i]['clicupcl'] = "SINCUPO";
						// Saldo = Cupo Cliente + Cupo Autorizado - Financiacion
						$mCupos[$i]['clisaldo'] = $mCupos[$i]['clicupsf'] - $mCupos[$i]['clifinan'];
					break;
				}

				if ($nMostrar == "") {
					//Verifico que no tenga algun valor en las columnas o sea un cliente
					if (
              (
                ($mCupos[$i]['cxctotal']+0) != 0 || ($mCupos[$i]['cliantxx']+0) != 0 ||
                ($mCupos[$i]['clicupsf']+0) != 0 || ($mCupos[$i]['clicxcpt']+0) != 0 || 
                ($mCupos[$i]['clipcccx']+0) != 0 || ($mCupos[$i]['clirecxx']+0) != 0 || 
                ($mCupos[$i]['clicxcip']+0) != 0 || ($mCupos[$i]['clicxcsa']+0) != 0
              ) 
              && $mCupos[$i]['cliclixx'] == "SI"
            ) {
						$nMostrar = "SI";
					}
				}

        if ($_POST['cCliId'] != "") {
          //Si solo se esta buscando un cliente
          $nMostrar = "SI";
        }

				if($nMostrar == "SI"){
					//Sumatorias
					$nCupCli += $mCupos[$i]['clicupcl'];
					$nTotCar += $mCupos[$i]['cxctotal'];
					$nTotAnt += $mCupos[$i]['cliantxx'];
					$nTotFin += $mCupos[$i]['clifinan'];
					$nTotRca += $mCupos[$i]['clirecxx'];
					$nTotCcc += $mCupos[$i]['clipcccx'];
					if ($mCupos[$i]['clisaldo'] != "ILIMITADO") {
						$nTotSal += $mCupos[$i]['clisaldo'];
					}
					$nCupSF  += $mCupos[$i]['clicupsf'];
					$nCarPCC += $mCupos[$i]['clicxcpt'];
					$nCarIP  += $mCupos[$i]['clicxcip'];
					$nSalAC  += $mCupos[$i]['clicxcsa'];

					$mDatos[count($mDatos)] = $mCupos[$i];
				}
			}
			// Fin Caludo la financiacion y el saldo del cupo
			$mDatos = f_Sort_Array_By_Field($mDatos,"clifinan","DESC_NUM");
				
			if($nSwitch == 0){
				switch ($rTipo) {
					case 1: ?>
						<html>
							<head>
								<title>Reporte Control Cupos - Siaco</title>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
								<link rel="stylesheet" type="text/css" href="<?php echo $cSystem_Libs_JS_Directory ?>/gwtext/resources/css/ext-all.css" />
								<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/utility.js'></script>
								<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/ajax.js'></script>
								<script type="text/javascript"  src = "<?php echo $cSystem_Libs_JS_Directory ?>/gwtext/adapter/ext/ext-base.js"></script>
								<script type="text/javascript"  src = "<?php echo $cSystem_Libs_JS_Directory ?>/gwtext/ext-all.js"></script>
								<script language = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/gwtext/conexijs/loading/loading.js"></script>
								<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
								<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
							</head>
							<body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0" onLoad="init();">
								<script>
									uLoad();
									var ld=(document.all);
									var ns4=document.layers;
									var ns6=document.getElementById&&!document.all;
									var ie4=document.all;
			
									function init() {
										if(ns4){ld.visibility="hidden";}
										else if (ns6||ie4) {
											Ext.MessageBox.updateProgress(1,'100% completed');
											Ext.MessageBox.hide();
										}
									}
								</script>
								<?php
								ob_flush();
								flush();?>
								<form name = 'frgrm' action='frrmmprn.php' method="post">
									<center>
										<table border="1" width="1900x" cellspacing="0" cellpadding="0" align=center>
											<tr bgcolor = "white" height="30">
												<td class="name" align="left" colspan="34"><font size="4"><b>Reporte Control Cupos - Siaco</font></b>
												</td>
											</tr>
											<tr bgcolor = "white" height="30">
												<td class="name" align="left" colspan="34"><font size="2">Registros Analizados : <?php echo count($mDatos)?></font></td>
											</tr>
											<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
												<td class="name" width="080px">Nit</td>
												<td class="name" width="300px">Cliente</td>
												<td class="name" width="150px">Tipo de Cupo</td>
												<td class="name" width="100px">Cupo por Cliente</td>
												<td class="name" width="110px">Cupo Autorizado SF</td>
												<td class="name" width="100px">Total Cupo</td>
												<td class="name" width="100px">Cartera Total</td>
												<td class="name" width="100px">Cartera Pagos por Cuenta de Cliente</td>
												<td class="name" width="100px">Cartera IP</td>
												<td class="name" width="100px">Saldos A Favor</td>
												<td class="name" width="100px">Pagos por Cuenta de Cliente Cancelados y No Facturados</td>
												<td class="name" width="100px">Anticipos No Facturados</td>
												<td class="name" width="100px">Recibos de Caja Provisionales</td>
												<td class="name" width="100px">Financiaci&oacute;n</td>
												<td class="name" width="100px">Saldo</td>
											</tr>
											<tr>
												<td class="name" colspan="3" align="right">Totales&nbsp;&nbsp;&nbsp;</td>
												<td class="name" style="padding:2px;text-align:right"><input type="text" class="letra" name = "cTotCup"  style= "width:100px;text-align:right;color:blue;font-weight:bold" value="<?php echo number_format($nCupCli,2,',','.'); ?>"></td>
												<td class="name" style="padding:2px;text-align:left"><input type="text" class="letra" name = "cTotCupSF" style= "width:110px;text-align:right;color:blue;font-weight:bold" value="<?php echo number_format($nCupSF,2,',','.');  ?>"></td>
												<td class="name" style="padding:2px;text-align:left"><input type="text" class="letra" name = "cTotCupCF" style= "width:100px;text-align:right;color:blue;font-weight:bold" value="<?php echo number_format(($nCupCli + $nCupSF),2,',','.');  ?>"></td>
												<td class="name" style="padding:2px;text-align:left"><input type="text" class="letra" name = "cTotCar"   style= "width:100px;text-align:right;color:blue;font-weight:bold" value="<?php echo number_format($nTotCar,2,',','.'); ?>"></td>
												<td class="name" style="padding:2px;text-align:left"><input type="text" class="letra" name = "cCarPCC"   style= "width:100px;text-align:right;color:blue;font-weight:bold" value="<?php echo number_format($nCarPCC,2,',','.'); ?>"></td>
												<td class="name" style="padding:2px;text-align:left"><input type="text" class="letra" name = "cCarIP"    style= "width:100px;text-align:right;color:blue;font-weight:bold" value="<?php echo number_format($nCarIP,2,',','.');  ?>"></td>
												<td class="name" style="padding:2px;text-align:left"><input type="text" class="letra" name = "cSalAC"    style= "width:100px;text-align:right;color:blue;font-weight:bold" value="<?php echo number_format($nSalAC,2,',','.');  ?>"></td>
												<td class="name" style="padding:2px;text-align:left"><input type="text" class="letra" name = "cTotCcc"   style= "width:100px;text-align:right;color:blue;font-weight:bold" value="<?php echo number_format($nTotCcc,2,',','.'); ?>"></td>
												<td class="name" style="padding:2px;text-align:left"><input type="text" class="letra" name = "cTotAnt"   style= "width:100px;text-align:right;color:blue;font-weight:bold" value="<?php echo number_format($nTotAnt,2,',','.'); ?>"></td>
												<td class="name" style="padding:2px;text-align:left"><input type="text" class="letra" name = "cTotRca"   style= "width:100px;text-align:right;color:blue;font-weight:bold" value="<?php echo number_format($nTotRca,2,',','.'); ?>"></td>
												<td class="name" style="padding:2px;text-align:left"><input type="text" class="letra" name = "cTotFin"   style= "width:100px;text-align:right;color:blue;font-weight:bold" value="<?php echo number_format($nTotFin,2,',','.'); ?>"></td>
												<td class="name" style="padding:2px;text-align:left"><input type="text" class="letra" name = "cTotSal"   style= "width:100px;text-align:right;color:blue;font-weight:bold" value="<?php echo number_format($nTotSal,2,',','.'); ?>"></td>
											</tr>
											<?php
											$nCanReg01 = 0;
											$y = 0;
											for($i=0; $i<count($mDatos); $i++){

												$cColor = "{$vSysStr['system_row_impar_color_ini']}";
												if($y % 2 == 0) {
													$cColor = "{$vSysStr['system_row_par_color_ini']}";
												}

												$nCanReg01++;
												if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }
												
												$nToTCup  = $mDatos[$i]['clicupcl'] + $mDatos[$i]['clicupsf'] + 0;
												?>
												<tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
													<td class="letra7" style="padding:2px;text-align:left"><?php  echo ($mDatos[$i]['cliidxxx'] != "") ? $mDatos[$i]['cliidxxx'] : "&nbsp" ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php  echo ($mDatos[$i]['clinomxx'] != "") ? $mDatos[$i]['clinomxx'] : "&nbsp" ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php  echo ($mDatos[$i]['clicupxx'] != "") ? $mDatos[$i]['clicupxx'] : "SINCUPO" ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php  echo ($mDatos[$i]['clicupcl'] != "") ? ((is_numeric($mDatos[$i]['clicupcl']))?number_format($mDatos[$i]['clicupcl'],2,',','.'):$mDatos[$i]['clicupcl']) : "&nbsp" ?></td>
													<td class="letra7" style="padding:2px;text-align:right"><?php echo ($mDatos[$i]['clicupsf'] != "") ? number_format($mDatos[$i]['clicupsf'],2,',','.') : "&nbsp" ?></td>
													<td class="letra7" style="padding:2px;text-align:right"><?php echo ($nToTCup != 0) ? number_format($nToTCup,2,',','.') : "&nbsp" ?></td>
													<td class="letra7" style="padding:2px;text-align:right"><?php echo ($mDatos[$i]['cxctotal'] != "") ? number_format($mDatos[$i]['cxctotal'],2,',','.') : "&nbsp" ?></td>
													<td class="letra7" style="padding:2px;text-align:right"><?php echo ($mDatos[$i]['clicxcpt'] != "") ? number_format($mDatos[$i]['clicxcpt'],2,',','.') : "&nbsp" ?></td>
													<td class="letra7" style="padding:2px;text-align:right"><?php echo ($mDatos[$i]['clicxcip'] != "") ? number_format($mDatos[$i]['clicxcip'],2,',','.') : "&nbsp" ?></td>
													<td class="letra7" style="padding:2px;text-align:right"><?php echo ($mDatos[$i]['clicxcsa'] != "") ? number_format($mDatos[$i]['clicxcsa'],2,',','.') : "&nbsp" ?></td>
													<td class="letra7" style="padding:2px;text-align:right"><?php echo ($mDatos[$i]['clipcccx'] != "") ? number_format($mDatos[$i]['clipcccx'],2,',','.') : "&nbsp" ?></td>
													<td class="letra7" style="padding:2px;text-align:right"><?php echo ($mDatos[$i]['cliantxx'] != "") ? number_format($mDatos[$i]['cliantxx'],2,',','.') : "&nbsp" ?></td>
													<td class="letra7" style="padding:2px;text-align:right"><?php echo ($mDatos[$i]['clirecxx'] != "") ? number_format($mDatos[$i]['clirecxx'],2,',','.') : "&nbsp" ?></td>
													<td class="letra7" style="padding:2px;text-align:right"><?php echo ($mDatos[$i]['clifinan'] != "") ? number_format($mDatos[$i]['clifinan'],2,',','.') : "&nbsp" ?></td>
													<td class="letra7" style="padding:2px;text-align:right"><?php echo ($mDatos[$i]['clisaldo'] == "ILIMITADO") ? $mDatos[$i]['clisaldo'] : number_format($mDatos[$i]['clisaldo'],2,',','.') ?></td>
												</tr>
												<?php
												$y++;
											} //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
										</table><br>
									</center>
								</form>
							</body>
						</html>
					<?php
					break;
					case 2:
						// PINTA POR EXCEL
						if(count($mDatos) > 0){
			
							$cNomFile = "REPORTECONTROLCUPOSSIACO_".$kUser."_".date('YmdHis').".xls";
							// $cFile = "{$OPENINIT['pathdr']}/opencomex/".$vSysStr['system_download_directory']."/".$cNomFile;
							
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
								$cRuta = "{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa/control_cupos";
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
								$cArcUsu = "REPORTECONTROLCUPOSSIACO_" . $kUser;
								$cArcHoy = "REPORTECONTROLCUPOSSIACO_" . $kUser . "_" . date("Ymd");
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
							
							$cF01 = fopen($cFile,"a");
							$cData = "<table cellspacing=\"0\" border=\"1\">";
								$cData .= "<tr>";
									$cData .= "<td colspan=\"15\"><b><font size=\"4\">Reporte Control Cupos - Siaco</font></b></td>";
								$cData .= "</tr>";
								$cData .= "<tr>";
								$cData .= "<td colspan=\"15\">Generado: ".date("Y-m-d")."  ".date("H:i:s")."</td>";
								$cData .= "</tr>";
								$cData .= "<tr>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"080px\"><b>Nit</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"250px\"><b>Cliente</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>Tipo de Cupo</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>Cupo por Cliente</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>Cupo Autorizado SF</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>Total Cupo</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>Cartera Total</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>Cartera Pagos por Cuenta de Cliente</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>Cartera IP</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>Saldos A Favor</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>Pagos por Cuenta de Cliente Cancelados</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>Anticipos No Facturados</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>Recibos de Caja Provisionales</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>Financiaci&oacute;n</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>Saldo</b></td>";
								$cData .= "</tr>";
								$cData .= "<tr>";
									$cData .= "<td colspan=\"3\"><b>Totales</td>";
									$cData .= "<td><b>".number_format($nCupCli,2,',','.')."</b></td>";
									$cData .= "<td><b>".number_format($nCupSF,2,',','.')."</b></td>";
									$cData .= "<td><b>".number_format(($nCupCli + $nCupSF),2,',','.')."</b></td>";
									$cData .= "<td><b>".number_format($nTotCar,2,',','.')."</b></td>";
									$cData .= "<td><b>".number_format($nCarPCC,2,',','.')."</b></td>";
									$cData .= "<td><b>".number_format($nCarIP,2,',','.')."</b></td>";
									$cData .= "<td><b>".number_format($nSalAC,2,',','.')."</b></td>";
									$cData .= "<td><b>".number_format($nTotCcc,2,',','.')."</b></td>";
									$cData .= "<td><b>".number_format($nTotAnt,2,',','.')."</b></td>";
									$cData .= "<td><b>".number_format($nTotRca,2,',','.')."</b></td>";
									$cData .= "<td><b>".number_format($nTotFin,2,',','.')."</b></td>";
									$cData .= "<td><b>".number_format($nTotSal,2,',','.')."</b></td>";
								$cData .= "</tr>";
								fwrite($cF01,$cData);
							
								$nCanReg01 = 0;
								for($i=0; $i<count($mDatos); $i++){
									$nCanReg01++;
									if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }

									$nToTCup  = $mDatos[$i]['clicupcl'] + $mDatos[$i]['clicupsf'] + 0;

									$cData = "<tr>";
										$cData .= "<td style=\"width:150px;mso-number-format:'\@'\">".(($mDatos[$i]['cliidxxx'] != "") ? $mDatos[$i]['cliidxxx'] : "")."</td>";
										$cData .= "<td style=\"width:200px;mso-number-format:'\@'\">".(($mDatos[$i]['clinomxx'] != "") ? $mDatos[$i]['clinomxx'] : "")."</td>";
										$cData .= "<td style=\"width:150px;mso-number-format:'\@'\">".(($mDatos[$i]['clicupxx'] != "") ? $mDatos[$i]['clicupxx'] : "SINCUPO")."</td>";
										$cData .= "<td>".(($mDatos[$i]['clicupcl'] != "") ? ((is_numeric($mDatos[$i]['clicupcl']))?number_format($mDatos[$i]['clicupcl'],2,',','.'):$mDatos[$i]['clicupcl']) : "")."</td>";
										$cData .= "<td>".(($mDatos[$i]['clicupsf'] != "") ? number_format($mDatos[$i]['clicupsf'],2,',','.') : "")."</td>";
										$cData .= "<td>".(($nToTCup != 0) ? number_format($nToTCup,2,',','.') : "")."</td>";
										$cData .= "<td>".(($mDatos[$i]['cxctotal'] != "") ? number_format($mDatos[$i]['cxctotal'],2,',','.') : "")."</td>";
										$cData .= "<td>".(($mDatos[$i]['clicxcpt'] != "") ? number_format($mDatos[$i]['clicxcpt'],2,',','.') : "")."</td>";
										$cData .= "<td>".(($mDatos[$i]['clicxcip'] != "") ? number_format($mDatos[$i]['clicxcip'],2,',','.') : "")."</td>";
										$cData .= "<td>".(($mDatos[$i]['clicxcsa'] != "") ? number_format($mDatos[$i]['clicxcsa'],2,',','.') : "")."</td>";
										$cData .= "<td>".(($mDatos[$i]['clipcccx'] != "") ? number_format($mDatos[$i]['clipcccx'],2,',','.') : "")."</td>";
										$cData .= "<td>".(($mDatos[$i]['cliantxx'] != "") ? number_format($mDatos[$i]['cliantxx'],2,',','.') : "")."</td>";
										$cData .= "<td>".(($mDatos[$i]['clirecxx'] != "") ? number_format($mDatos[$i]['clirecxx'],2,',','.') : "")."</td>";
										$cData .= "<td>".(($mDatos[$i]['clifinan'] != "") ? number_format($mDatos[$i]['clifinan'],2,',','.') : "")."</td>";
										$cData .= "<td>".(($mDatos[$i]['clisaldo'] == "ILIMITADO") ? $mDatos[$i]['clisaldo'] : number_format($mDatos[$i]['clisaldo'],2,',','.'))."</td>";
									$cData .= "</tr>";
									fwrite($cF01,$cData);
								} //Fin While que recorre el cursor de la matriz generada por la consulta.
							$cData = '</table>';
							fwrite($cF01,$cData);
							fclose($cF01);

							if (file_exists($cFile)) {

								if ($_SERVER["SERVER_PORT"] != "") {?>
										<script languaje = "javascript">
											parent.fmpro2.location = 'frrepdoc.php?cRuta=<?php echo $cNomFile ?>';
										</script>
										<?php 
								}else{
									$cNomArc = $cNomFile;
								}

							}else {
								$nSwitch = 1;
								if ($_SERVER["SERVER_PORT"] != "") {
									f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
								} else {
									$cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
								}
							}

							if ($_SERVER["SERVER_PORT"] != "") {
								$cMsj = "Proceso Realizado con Exito.\n";
								f_Mensaje(__FILE__,__LINE__,$cMsj);
							}
						}else{
							$nSwitch = 1;
							$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "No Se Encontraron Registros.\n";
						}
					break;
				}//Fin Switch
			}
		}## if ($cEjePro == 0) {
	}## if ($nSwitch == 0) {

	if($nSwitch == 1){
		$cMsj = "Se Presentaron Errores en el Proceso.\n".$cMsj;
	}
	
	if ($nSwitch == 1){
		if ($_SERVER["SERVER_PORT"] != "") {
			f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
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
				$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}
		}
	} // fin del if ($_SERVER["SERVER_PORT"] == "")

	/**
		* Metodo que realiza la conexion
		*/
	function fnConectarDBReporte(){
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

		$xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);
		if($xConexion99){
			$nSwitch = 0;
		}else{
			$nSwitch = 1;
			$mReturn[count($mReturn)] = "El Sistema no Logro Conexion con ".OC_SERVER;
		}

		if($nSwitch == 0){
			$mReturn[0] = "true"; $mReturn[1] = $xConexion99;
		}else{
			$mReturn[0] = "false";
		}
		return $mReturn;
	}##function fnConectarDBReporte(){##

	/**
		* Metodo que realiza el reinicio de la conexion
		*/
	function fnReiniciarConexionDBReporte($pConexion){
		global $cHost;  global $cUserHost;  global $cPassHost;

		mysql_close($pConexion);
		$xConexion01 = mysql_connect($cHost,$cUserHost,$cPassHost,TRUE);

		return $xConexion01;
	}##function fnReiniciarConexionDBReporte(){##

  function fnCrearTablaFacturas($pParametros){
    global $cAlfa;

    $xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);
    if($xConexion99){
      $nSwitch = 0;
    }else{
      $nSwitch = 1;
      f_Mensaje(__FILE__,__LINE__,"El Sistema no Logro Conexion con ".OC_SERVER);
    }

    if($nSwitch == 0){

      /**
       * Random para Nombre de la Tabla
       */
      $cTabCar  = mt_rand(1000000000, 9999999999);

      switch($pParametros['tipotabl']){
        case "FACTURAS":
          $cTabla   = "mempccfa".$cTabCar;
          $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
          $qNewTab .= "comidxxx varchar(1)  NOT NULL COMMENT \"Id del Comprobante\", ";
          $qNewTab .= "comcodxx varchar(4)  NOT NULL COMMENT \"Codigo del Comprobante\", ";
          $qNewTab .= "comcscxx varchar(20) NOT NULL COMMENT \"Consecutivo Uno del Comprobante\", ";
          $qNewTab .= "comcsc2x varchar(20) NOT NULL COMMENT \"Consecutivo Dos del Comprobante\", ";
          $qNewTab .= "commemod longtext NOT NULL COMMENT \"Campo Memo con los PCC\", ";
          $qNewTab .= "comfecxx date NOT NULL COMMENT \"Fecha del Comprobante'\", ";
          $qNewTab .= "KEY (comidxxx,comcodxx,comcscxx,comcsc2x)) ENGINE=MyISAM ";
        break;
        case "CXC":
          $cTabla   = "mempcccp".$cTabCar;
          $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
          $qNewTab .= "comidxxx varchar(1)  NOT NULL COMMENT \"Id del Comprobante\", ";
          $qNewTab .= "comcodxx varchar(4)  NOT NULL COMMENT \"Codigo del Comprobante\", ";
          $qNewTab .= "comcscxx varchar(20) NOT NULL COMMENT \"Consecutivo Uno del Comprobante\", ";
          $qNewTab .= "comcsc2x varchar(20) NOT NULL COMMENT \"Consecutivo Dos del Comprobante\", ";
          $qNewTab .= "teridxxx varchar(20) NOT NULL COMMENT \"Id del Tercero\", ";
          $qNewTab .= "pucidxxx varchar(10) NOT NULL COMMENT \"Cuenta Contable PUC\", ";
          $qNewTab .= "comfecxx date NOT NULL COMMENT \"Fecha del Comprobante'\", ";
          $qNewTab .= "KEY (comidxxx,comcodxx,comcscxx,comcsc2x)) ENGINE=MyISAM ";
        break;
      }
      
      $xNewTab  = mysql_query($qNewTab,$xConexion99);
      //f_Mensaje(__FILE__,__LINE__,$qNewTab);
      if(!$xNewTab) {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal.".mysql_error($xConexion99);
      }
    }

    if($nSwitch == 0){
      $mReturn[0] = "true";
      $mReturn[1] = $cTabla;
    }else{
      $mReturn[0] = "false";
    }
    return $mReturn;
  }
?>
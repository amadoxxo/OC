<?php

  ##Estableciendo que el tiempo de ejecucion no se limite

// ini_set('error_reporting', E_ERROR);
// ini_set("display_errors", "1");

set_time_limit(0);
ini_set("memory_limit", "512M");

/**
 * Cantidad de Registros para reiniciar conexion
 */
define("_NUMREG_",100);

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
    include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/uticonta.php");
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
  include("../../../../libs/php/uticonta.php");
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
  /*** Ejecutar proceso en Background ***/
  $cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
} // fin if ($_SERVER["SERVER_PORT"] != "")

if ($_SERVER["SERVER_PORT"] == "") {
  $gPucIdIni = $_POST['gPucIdIni'];
  $gPucIdFin = $_POST['gPucIdFin'];
  $gDesde = $_POST['gDesde'];
  $gHasta = $_POST['gHasta'];
  $cTipo = $_POST['cTipo'];
}  // fin del if ($_SERVER["SERVER_PORT"] == "")

$cPerAno2 = '0';
$cFecha = explode("-", $gDesde);
$cPerAno = $cFecha[0];

  # Rango de Fechas para traer el saldo anterior
$dFecIni = $cPerAno . "-01-01";
	# Restando un dia a la fecha de incio
list($nAno, $nMes, $nDia) = split("-", $gDesde);
$nFecFin = mktime(0, 0, 0, $nMes, $nDia, $nAno) - (24 * 60 * 60);
$dFecFin = date("Y-m-d", $nFecFin);

if ($dFecFin < $cPerAno) {
  $cPerAno2 = $cPerAno;
  $cPerAno2 = $cPerAno2 - 1;
  $dFecIni = $cPerAno2 . "-01-01";
}

$qLoad = "SELECT SQL_CALC_FOUND_ROWS comidxxx ";
$qLoad .= "FROM $cAlfa.fcod$cPerAno ";
$qLoad .= "WHERE ";
$qLoad .= "$cAlfa.fcod$cPerAno.comfecxx BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
$qLoad .= "$cAlfa.fcod$cPerAno.pucidxxx BETWEEN \"$gPucIdIni\" AND \"$gPucIdFin\" AND ";
$qLoad .= "$cAlfa.fcod$cPerAno.regestxx = \"ACTIVO\" ";
$qLoad .= "LIMIT 0,1";
$xLoad = f_Mysql("SELECT", "", $qLoad, $xConexion01, "");

mysql_free_result($xLoad);

$xNumRows = mysql_query("SELECT FOUND_ROWS();");
$xRNR = mysql_fetch_array($xNumRows);
$nRegistros = $xRNR['FOUND_ROWS()'];
mysql_free_result($xNumRows);

if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
  $cEjePro = 1;

  $strPost = "gPucIdIni~" . $gPucIdIni . "|gPucIdFin~" . $gPucIdFin . "|gDesde~" . $gDesde . "|gHasta~" . $gHasta . "|cTipo~" . $cTipo . "";

  $vParBg['pbadbxxx'] = $cAlfa;                                         	//Base de Datos
  $vParBg['pbamodxx'] = "FACTURACION";                                  	//Modulo
  $vParBg['pbatinxx'] = "ESTADODECUENTA";                             	  //Tipo Interface
  $vParBg['pbatinde'] = "ESTADO DE CUENTA";                               //Descripcion Tipo de Interfaz
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
} // fin del if ($_SERVER["SERVER_PORT"] != "" && $_POST['cEjProBg'] == "SI" && $nSwitch == 0)

if ($cEjePro == 0) {
  if ($nSwitch == 0) {
    if($cTipo == 1){
      if ($_SERVER["SERVER_PORT"] != "") {
        ?>
        <html>
          <head>
            <title>Reporte de Auxiliar por Cuenta</title>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
          </head>
          <body>
            <div id="loading" style="background: white;position: absolute;left: 45%;top: 45%;padding: 2px;height: auto;border: 1px solid #ccc;">
              <div style="background: white;color: #444;font: bold 13px tahoma, arial, helvetica;padding: 10px;margin: 0;height: auto;">
                <img src="<?php echo $cPlesk_Skin_Directory ?>/loading.gif" width="32" height="32" style="margin-right:8px;float:left;vertical-align:top;"/>
                openComex<br>
                <span style="font: normal 10px arial, tahoma, sans-serif;">Cargando...</span>
              </div>
      	    </div>
        <?php
      }
    }

    // Busco la descripcion del concepto
    $qCtoCon = "SELECT $cAlfa.fpar0119.*,$cAlfa.fpar0115.* ";
    $qCtoCon .= "FROM $cAlfa.fpar0119,$cAlfa.fpar0115 ";
    $qCtoCon .= "WHERE ";
    $qCtoCon .= "$cAlfa.fpar0119.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) ";
    $xCtoCon = f_MySql("SELECT", "", $qCtoCon, $xConexion01, "");
  
    //f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
    $mConceptos = array();
    while ($xRCC = mysql_fetch_array($xCtoCon)) {
      $mConceptos["{$xRCC['ctoidxxx']}~{$xRCC['pucidxxx']}"] = $xRCC;
    }

    //Busco en la parametrica de Conceptos Contables Causaciones Automaticas
    $qCtoCon = "SELECT $cAlfa.fpar0121.*,$cAlfa.fpar0115.* ";
    $qCtoCon .= "FROM $cAlfa.fpar0121,$cAlfa.fpar0115 ";
    $qCtoCon .= "WHERE ";
    $qCtoCon .= "$cAlfa.fpar0121.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) ";
    $xCtoCon = f_MySql("SELECT", "", $qCtoCon, $xConexion01, "");
  
    //f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
    while ($xRCC = mysql_fetch_array($xCtoCon)) {
      $mConceptos["{$xRCC['ctoidxxx']}~{$xRCC['pucidxxx']}"] = $xRCC;
    }

    $qCtoCon = "SELECT $cAlfa.fpar0129.*,$cAlfa.fpar0115.* ";
    $qCtoCon .= "FROM $cAlfa.fpar0129,$cAlfa.fpar0115 ";
    $qCtoCon .= "WHERE ";
    $qCtoCon .= "$cAlfa.fpar0129.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) ";
    $xCtoCon = f_MySql("SELECT", "", $qCtoCon, $xConexion01, "");
  
    //f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
    $mConIP = array();
    while ($xRCC = mysql_fetch_array($xCtoCon)) {
      $xRCC['ctodesxx'] = $xRCC['serdesxx'];
      $xRCC['ctonitxx'] = "CLIENTE";
      $mConIP["{$xRCC['ctoidxxx']}~{$xRCC['pucidxxx']}"] = $xRCC;
    }

    $qCtoCon = "SELECT $cAlfa.fpar0115.*, ";
    $qCtoCon .= "CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AS pucidxxx ";
    $qCtoCon .= "FROM $cAlfa.fpar0115 ";
    $xCtoCon = f_MySql("SELECT", "", $qCtoCon, $xConexion01, "");
  
    // f_Mensaje(__FILE__,__LINE__,$qCtoCon." ~ ".mysql_num_rows($xCtoCon));
    $mPUC = array();
    while ($xRCC = mysql_fetch_array($xCtoCon)) {
      $xRCC['ctodesxx'] = $xRCC['pucdesxx'];
      $xRCC['ctonitxx'] = "CLIENTE";

      if ($xRCC['pucretxx'] > 0) { //Si es una retencion aplica calculo automatico de base
        $xRCC['ctovlr01'] = "SI";
      }

      $mPUC["{$xRCC['pucidxxx']}"] = $xRCC;
    }

    // Se obtiene el movimiento de las cuentas por cobrar y por pagar para guardar en la tabla temporal
    $AnoFin  = substr($gHasta,0,4);		
    $AnoIni  = $vSysStr['financiero_ano_instalacion_modulo'];
		$mTabMov = array(); //Nombre de las tablas temporales para el movimiento

    // Instanciando la clase para guardar regristros en la tabla temporal
    $objEstructurasAuxiliarPorCuenta = new cEstructurasAuxiliarPorCuenta();

    for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {
      // Se crea la tabla temporal por cada anio
      $mReturnCrearReporte = $objEstructurasAuxiliarPorCuenta->fnCrearEstructurasAuxiliarPorCuenta($nAno);
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
      if ($nSwitch == 0) {
        $mTabMov[$nAno] = $mReturnCrearReporte[1];
        // Declaro el INSERT para la tabal temporal
        $qInsCab  = "INSERT INTO $cAlfa.$mReturnCrearReporte[1] (";
        $qInsCab .= "comidxxx, ";
        $qInsCab .= "comcodxx, ";
        $qInsCab .= "comcscxx, ";
        $qInsCab .= "comcsc2x, ";
        $qInsCab .= "teridxxx, ";
        $qInsCab .= "pucidxxx, ";
        $qInsCab .= "comfecxx, ";
        $qInsCab .= "comfecve) VALUES ";
        // FIN Declaro el INSERT para la tabla temporal

        $qCxcCxp  = "SELECT ";
        $qCxcCxp .= "$cAlfa.fcod$nAno.comidxxx, ";
        $qCxcCxp .= "$cAlfa.fcod$nAno.comcodxx, ";
        $qCxcCxp .= "$cAlfa.fcod$nAno.comcscxx, ";
        $qCxcCxp .= "$cAlfa.fcod$nAno.comcsc2x, ";
        $qCxcCxp .= "$cAlfa.fcod$nAno.teridxxx, ";
        $qCxcCxp .= "$cAlfa.fcod$nAno.pucidxxx, ";
        $qCxcCxp .= "$cAlfa.fcod$nAno.comfecxx, ";
        $qCxcCxp .= "$cAlfa.fcod$nAno.comfecve ";
        $qCxcCxp .= "FROM $cAlfa.fcod$nAno ";
        $qCxcCxp .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fcod$nAno.pucidxxx ";
        $qCxcCxp .= "WHERE ";
        $qCxcCxp .= "$cAlfa.fpar0115.pucdetxx IN (\"C\",\"P\") AND ";
        $qCxcCxp .= "$cAlfa.fcod$cPerAno.pucidxxx BETWEEN \"$gPucIdIni\" AND \"$gPucIdFin\" AND ";
        $qCxcCxp .= "$cAlfa.fcod$nAno.regestxx = \"ACTIVO\" ";
        $xCxcCxp = f_MySql("SELECT", "", $qCxcCxp, $xConexion01, "");

			  while ($xRCC = mysql_fetch_array($xCxcCxp)) {
          //Almaceno los VALUES del INSERT
          $qInsDet .= "(\"{$xRCC['comidxxx']}\","; 
          $qInsDet .= "\"{$xRCC['comcodxx']}\",";
          $qInsDet .= "\"{$xRCC['comcscxx']}\",";
          $qInsDet .= "\"{$xRCC['comcsc2x']}\",";
          $qInsDet .= "\"{$xRCC['teridxxx']}\",";
          $qInsDet .= "\"{$xRCC['pucidxxx']}\",";
          $qInsDet .= "\"{$xRCC['comfecxx']}\",";
          $qInsDet .= "\"{$xRCC['comfecve']}\"),";

          //Realizo el INSERT a la tabla temporal - Acumulo la cantidad de registros para reiniciar la conexion
          $nCanReg01++;
          if (($nCanReg01 % _NUMREG_) == 0) {
            $xConexion01 = $objEstructurasAuxiliarPorCuenta->fnReiniciarConexionDBAuxiliarPorCuenta($xConexion01);

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

        if($nSwitch_Reporte == 0 && $qInsDet != ""){
          $xConexion01 = $objEstructurasAuxiliarPorCuenta->fnReiniciarConexionDBAuxiliarPorCuenta($xConexion01);
  
          $qInsDet = substr($qInsDet, 0, -1);
          $qInsDet = $qInsCab.$qInsDet;
          if(!mysql_query($qInsDet,$xConexion01)) {
            $nSwitch_Reporte = 1;
          }
        }
      }
    }

    $qDatMov = "SELECT ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comidxxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comcodxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comcscxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comcsc3x,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.pucidxxx,$cAlfa.fcod$cPerAno.commovxx, ";
    if ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI') {
      $qDatMov .= "CONCAT($cAlfa.fcod$cPerAno.comidxxx,\"-\",$cAlfa.fcod$cPerAno.comcodxx,\"-\",$cAlfa.fcod$cPerAno.comcscxx,\"-\",$cAlfa.fcod$cPerAno.comcsc3x) AS consecux,";
    } else {
      $qDatMov .= "CONCAT($cAlfa.fcod$cPerAno.comidxxx,\"-\",$cAlfa.fcod$cPerAno.comcodxx,\"-\",$cAlfa.fcod$cPerAno.comcscxx) AS consecux,";
    }
    $qDatMov .= "CONCAT($cAlfa.fcod$cPerAno.comidcxx,\"-\",$cAlfa.fcod$cPerAno.comcodcx,\"-\",$cAlfa.fcod$cPerAno.comcsccx) AS cruce,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comfecxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comfecve,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.pucidxxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comobsxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.ctoidxxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.teridxxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.terid2xx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comvlr01,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comctocx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comctoc2,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comidcxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comcodcx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comcsccx,";
    $qDatMov .= "$cAlfa.fpar0115.pucterxx, ";
    $qDatMov .= "$cAlfa.fpar0115.pucdetxx, ";
    /** Fin Cruce-Base **/
    $qDatMov .= "$cAlfa.fcod$cPerAno.ccoidxxx,";
    $qDatMov .= "$cAlfa.fcod$cPerAno.sccidxxx,";
    $qDatMov .= "IF($cAlfa.fcod$cPerAno.commovxx=\"D\",$cAlfa.fcod$cPerAno.comvlrxx,\"\") AS debitoxx,";
    $qDatMov .= "IF($cAlfa.fcod$cPerAno.commovxx=\"C\",$cAlfa.fcod$cPerAno.comvlrxx,\"\") AS creditox ";
    $qDatMov .= "FROM $cAlfa.fcod$cPerAno ";
    $qDatMov .= "LEFT JOIN $cAlfa.fpar0115 ON $cAlfa.fcod$cPerAno.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) ";
    $qDatMov .= "WHERE ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comfecxx BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.pucidxxx BETWEEN \"$gPucIdIni\" AND \"$gPucIdFin\" AND ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.regestxx = \"ACTIVO\" ";
    $qDatMov .= "ORDER BY $cAlfa.fcod$cPerAno.pucidxxx,$cAlfa.fcod$cPerAno.comfecxx ";
    $xDatMov  = f_MySql("SELECT", "", $qDatMov, $xConexion01, "");
    // echo $qDatMov."~".mysql_num_rows($xDatMov);

    //Trayendo cuentas contables de la busqueda
    $qCuentas  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
    $qCuentas .= "FROM $cAlfa.fpar0115 ";
    $qCuentas .= "WHERE ";
    $qCuentas .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) BETWEEN \"$gPucIdIni\" AND \"$gPucIdFin\" AND ";
    $qCuentas .= "regestxx = \"ACTIVO\" LIMIT 0,1";
    $xCuentas  = f_MySql("SELECT","",$qCuentas,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qCuentas."~".mysql_num_rows($xCuentas));
    $mCuentas = array();
    while ($xRC = mysql_fetch_array($xCuentas)) {
      $mCuentas["{$xRC['pucidxxx']}"] = 0;
    }

    switch ($cTipo) {
      case 1: /* PINTA POR PANTALLA */
        if ($_SERVER["SERVER_PORT"] != "") {
          ?>
              <form name = 'frgrm' action='frinpgrf.php' method="POST">
                <center>
                  <table border="1" cellspacing="0" cellpadding="0" width="1500" align=center style="margin:5px">
                  <tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px;">
                      <?php
                      switch ($cAlfa) {
                        case "GRUMALCO"://GRUMALCO
                        case "TEGRUMALCO"://GRUMALCO
                        case "DEGRUMALCO"://GRUMALCO
                          ?>
                          <td class="name" colspan="1" align="left">
                            <img width="120" height="70" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomalco.jpg">
                          </td>
                          <td class="name" colspan="17" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                              RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                              PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                          <?php
                          break;
                        case "ALADUANA"://ALADUANA
                        case "TEALADUANA"://ALADUANA
                        case "DEALADUANA"://ALADUANA
                          ?>
                          <td class="name" colspan="1" align="left">
                            <img width="150" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaladuana.jpg">
                          </td>
                          <td class="name" colspan="17" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                              RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                              PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                          <?php
                        break;
                        case "ANDINOSX"://ANDINOSX
                        case "TEANDINOSX"://ANDINOSX
                        case "DEANDINOSX"://ANDINOSX
                          ?>
                          <td class="name" colspan="1" align="left">
                            <img width="90" height="90" style="left: 35;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoAndinos2.jpeg">
                          </td>
                          <td class="name" colspan="17" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                              RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                              PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                          <?php
                        break;
                        case "GRUPOALC"://GRUPOALC
                        case "TEGRUPOALC"://GRUPOALC
                        case "DEGRUPOALC"://GRUPOALC
                          ?>
                          <td class="name" colspan="1" align="left">
                            <img width="150" height="70" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoalc.jpg">
                          </td>
                          <td class="name" colspan="17" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                              RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                              PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                          <?php
                        break;
                        case "AAINTERX"://AAINTERX
                        case "TEAAINTERX"://AAINTERX
                        case "DEAAINTERX"://AAINTERX
                          ?>
                          <td class="name" colspan="1" align="left">
                            <img width="150" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointernacional.jpg">
                          </td>
                          <td class="name" colspan="17" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                              RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                              PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                          <?php
                        break;
                        case "AALOPEZX":
                        case "TEAALOPEZX":
                        case "DEAALOPEZX":
                          ?>
                          <td class="name" colspan="1" align="left">
                            <img width="120" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaalopez.png">
                          </td>
                          <td class="name" colspan="17" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                              RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                              PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                          <?php
                        break;
                        case "ADUAMARX"://ADUAMARX
                        case "TEADUAMARX"://ADUAMARX
                        case "DEADUAMARX"://ADUAMARX
                          ?>
                          <td class="name" colspan="1" align="left">
                            <img width="90" height="90" style="left: 25px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaduamar.jpg">
                          </td>
                          <td class="name" colspan="17" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                              RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                              PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                          <?php
                        break;
                        case "SOLUCION"://SOLUCION
                        case "TESOLUCION"://SOLUCION
                        case "DESOLUCION"://SOLUCION
                          ?>
                          <td class="name" colspan="1" align="left">
                            <img width="120" style="left: 25px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logosoluciones.jpg">
                          </td>
                          <td class="name" colspan="17" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                              RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                              PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                          <?php
												break;
												case "FENIXSAS"://FENIXSAS
												case "TEFENIXSAS"://FENIXSAS
												case "DEFENIXSAS"://FENIXSAS
													?>
													<td class="name" colspan="1" align="left">
														<img width="130" style="left: 25px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logofenix.jpg">
													</td>
													<td class="name" colspan="17" align="left">
														<font size="3">
															<b>REPORTE AUXILIAR POR CUENTA<BR>
															RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
															PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
															</b>
														</font>
													</td>
													<?php
												break;
												case "COLVANXX"://COLVANXX
												case "TECOLVANXX"://COLVANXX
												case "DECOLVANXX"://COLVANXX
													?>
													<td class="name" colspan="1" align="left">
														<img width="130" style="left: 25px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logocolvan.jpg">
													</td>
													<td class="name" colspan="17" align="left">
														<font size="3">
															<b>REPORTE AUXILIAR POR CUENTA<BR>
															RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
															PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
															</b>
														</font>
													</td>
													<?php
												break;
												case "INTERLAC"://INTERLAC
												case "TEINTERLAC"://INTERLAC
												case "DEINTERLAC"://INTERLAC
													?>
													<td class="name" colspan="1" align="left">
														<img width="130" style="left: 25px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointerlace.jpg">
													</td>
													<td class="name" colspan="17" align="left">
														<font size="3">
															<b>REPORTE AUXILIAR POR CUENTA<BR>
															RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
															PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
															</b>
														</font>
													</td>
													<?php
												break;
												case "DHLEXPRE": //DHLEXPRE
												case "TEDHLEXPRE": //DHLEXPRE
												case "DEDHLEXPRE": //DHLEXPRE
													?>
													<td class="name" colspan="1" align="left">
														<img width="140" height="80" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logo_dhl_express.jpg">
													</td>
													<td class="name" colspan="17" align="left">
														<font size="3">
															<b>REPORTE AUXILIAR POR CUENTA<BR>
															RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
															PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
															</b>
														</font>
													</td>
													<?php
												break;
                        case "KARGORUX": //KARGORUX
                        case "TEKARGORUX": //KARGORUX
                        case "DEKARGORUX": //KARGORUX
                        ?>
                          <td class="name" colspan="1" align="left">
                            <img width="130" height="70" style="left: 15px;margin-top:5px;margin-bottom:5px;margin-left:5px;margin-right:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logokargoru.jpg">
                          </td>
                          <td class="name" colspan="17" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                                RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                                PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                        <?php
												break;
                        case "ALOGISAS": //LOGISTICA
                        case "TEALOGISAS": //LOGISTICA
                        case "DEALOGISAS": //LOGISTICA
                        ?>
                          <td class="name" colspan="1" align="left">
                            <img width="130" style="left: 15px;margin-top:6px;margin-bottom:5px;margin-left:5px;margin-right:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logologisticasas.jpg">
                          </td>
                          <td class="name" colspan="17" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                                RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                                PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                        <?php
												break;
                        case "PROSERCO":
                        case "TEPROSERCO":
                        case "DEPROSERCO":
                        ?>
                          <td class="name" colspan="1" align="left">
                            <img width="130" style="left: 15px;margin-top:6px;margin-bottom:5px;margin-left:5px;margin-right:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoproserco.png">
                          </td>
                          <td class="name" colspan="17" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                                RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                                PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                        <?php
                        break;
                        case "MANATIAL":
                        case "TEMANATIAL":
                        case "DEMANATIAL":
                        ?>
                          <td class="name" colspan="1" align="left">
                            <img width="130" style="left: 15px;margin-top:6px;margin-bottom:5px;margin-left:5px;margin-right:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logomanantial.jpg">
                          </td>
                          <td class="name" colspan="17" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                                RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                                PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                        <?php
                        break;
                        case "DSVSASXX":
                        case "DEDSVSASXX":
                        case "TEDSVSASXX":
                        ?>
                          <td class="name" colspan="1" align="left">
                            <img width="130" style="left: 15px;margin-top:6px;margin-bottom:5px;margin-left:5px;margin-right:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logodsv.jpg">
                          </td>
                          <td class="name" colspan="17" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                                RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                                PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                        <?php
                        break;
                        case "MELYAKXX":    //MELYAK
                        case "DEMELYAKXX":  //MELYAK
                        case "TEMELYAKXX":  //MELYAK
                          ?>
                            <td class="name" colspan="1" align="left">
                              <img width="130" style="left: 15px;margin-top:6px;margin-bottom:5px;margin-left:5px;margin-right:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logomelyak.jpg">
                            </td>
                            <td class="name" colspan="17" align="left">
                              <font size="3">
                                <b>REPORTE AUXILIAR POR CUENTA<BR>
                                  RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                                  PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                                </b>
                              </font>
                            </td>
                          <?php
                        break;
                        case "FEDEXEXP":    //FEDEX
                        case "DEFEDEXEXP":  //FEDEX
                        case "TEFEDEXEXP":  //FEDEX
                          ?>
                            <td class="name" colspan="1" align="left">
                              <img width="130" style="left: 15px;margin-top:6px;margin-bottom:5px;margin-left:5px;margin-right:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logofedexexp.jpg">
                            </td>
                            <td class="name" colspan="17" align="left">
                              <font size="3">
                                <b>REPORTE AUXILIAR POR CUENTA<BR>
                                  RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                                  PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                                </b>
                              </font>
                            </td>
                          <?php
                        break;
                        case "EXPORCOM":    //EXPORCOMEX
                        case "DEEXPORCOM":  //EXPORCOMEX
                        case "TEEXPORCOM":  //EXPORCOMEX
                          ?>
                            <td class="name" colspan="1" align="left">
                              <img width="130" style="left: 15px;margin-top:6px;margin-bottom:5px;margin-left:5px;margin-right:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoexporcomex.jpg">
                            </td>
                            <td class="name" colspan="17" align="left">
                              <font size="3">
                                <b>REPORTE AUXILIAR POR CUENTA<BR>
                                  RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                                  PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                                </b>
                              </font>
                            </td>
                          <?php
                        break;
                        case "HAYDEARX":   //HAYDEARX
                        case "DEHAYDEARX": //HAYDEARX
                        case "TEHAYDEARX": //HAYDEARX
                          ?>
                            <td class="name" colspan="1" align="left">
                              <img width="180" height="70" style="left: 15px;margin-top:6px;margin-bottom:5px;margin-left:5px;margin-right:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logohaydear.jpeg">
                            </td>
                            <td class="name" colspan="17" align="left">
                              <font size="3">
                                <b>REPORTE AUXILIAR POR CUENTA<BR>
                                  RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                                  PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                                </b>
                              </font>
                            </td>
                          <?php
                        break;
                        case "CONNECTA":   //CONNECTA
                        case "DECONNECTA": //CONNECTA
                        case "TECONNECTA": //CONNECTA
                          ?>
                            <td class="name" colspan="1" align="left">
                              <img width="120" height="80" style="left: 15px;margin-top:6px;margin-bottom:5px;margin-left:5px;margin-right:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoconnecta.jpg">
                            </td>
                            <td class="name" colspan="17" align="left">
                              <font size="3">
                                <b>REPORTE AUXILIAR POR CUENTA<BR>
                                  RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                                  PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                                </b>
                              </font>
                            </td>
                          <?php
                        break;
                        case "CONLOGIC":   //CONLOGIC
                        case "DECONLOGIC": //CONLOGIC
                        case "TECONLOGIC": //CONLOGIC
                          ?>
                            <td class="name" colspan="1" align="left">
                              <img width="120" height="80" style="left: 15px;margin-top:6px;margin-bottom:5px;margin-left:5px;margin-right:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/logoconlogic.jpg">
                            </td>
                            <td class="name" colspan="17" align="left">
                              <font size="3">
                                <b>REPORTE AUXILIAR POR CUENTA<BR>
                                  RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                                  PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                                </b>
                              </font>
                            </td>
                          <?php
                        break;
                        case "OPENEBCO":   //OPENEBCO
                        case "DEOPENEBCO": //OPENEBCO
                        case "TEOPENEBCO": //OPENEBCO
                          ?>
                            <td class="name" colspan="1" align="left">
                              <img width="200" height="80" style="left: 15px;margin-top:6px;margin-bottom:5px;margin-left:5px;margin-right:5px;" src="<?php echo $cPlesk_Skin_Directory ?>/opentecnologia.JPG">
                            </td>
                            <td class="name" colspan="17" align="left">
                              <font size="3">
                                <b>REPORTE AUXILIAR POR CUENTA<BR>
                                  RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                                  PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                                </b>
                              </font>
                            </td>
                          <?php
                        break;
                        default:
                          ?>
                          <td class="name" colspan="16" align="left">
                            <font size="3">
                              <b>REPORTE AUXILIAR POR CUENTA<BR>
                              RANGO CUENTAS: <?php echo " " . $gPucIdIni . " - " . $gPucIdFin ?><br>
                              PERIODO: <?php echo " " . $gDesde . " - " . $gHasta ?><br>
                              </b>
                            </font>
                          </td>
                          <?php
                          break;
                      }
                      ?>
                    </tr>
                    <tr height="20">
                      <td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>Cuenta</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="150px"><b><font color=white>Comprobante</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha Comprobante</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="150px"><b><font color=white>Fecha Vencimiento Comprobante</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Descripci&oacute;n</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="140px"><b><font color=white>Detalle</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>Nit</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Tercero</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>Nit</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Tercero</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="150px"><b><font color=white>Cruce-Base</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha Cruce-Base</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha Vencimiento Cruce-Base</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>CC</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>SC</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Debitos</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Creditos</font></b></td>
                      <td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Saldo Movimiento</font></b></td>
                    </tr>
                  <?php
                  $cCueAux = "";
                  $nCanReg = 0;

                  while ($xRDM = mysql_fetch_array($xDatMov)) {

                    $nCanReg++;
                    if (($nCanReg % _NUMREG_) == 0) {
                      $xConexion01 = fnReiniciarConexion();
                    }

                    //Trayendo nombre del cliente
                    $qCliente = "SELECT CLIIDXXX, ";
                    $qCliente .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
                    $qCliente .= "FROM $cAlfa.SIAI0150 ";
                    $qCliente .= "WHERE ";
                    $qCliente .= "CLIIDXXX = \"{$xRDM['teridxxx']}\" LIMIT 0,1 ";
                    $xCliente = f_MySql("SELECT", "", $qCliente, $xConexion01, "");
                    $vCliente = mysql_fetch_array($xCliente);
                    $xRDM['clinomxx'] = $vCliente['CLINOMXX'];

                    //Trayendo nombre del proveedor
                    $qProveedor = "SELECT CLIIDXXX, ";
                    $qProveedor .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
                    $qProveedor .= "FROM $cAlfa.SIAI0150 ";
                    $qProveedor .= "WHERE ";
                    $qProveedor .= "CLIIDXXX = \"{$xRDM['terid2xx']}\" LIMIT 0,1 ";
                    $xProveedor = f_MySql("SELECT", "", $qProveedor, $xConexion01, "");
                    $vProveedor = mysql_fetch_array($xProveedor);
                    $xRDM['clinom2x'] = $vProveedor['CLINOMXX'];

                    if ($mConceptos["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"] != "") {
                      $vCtoCon = $mConceptos["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"];
                    } else {
                      if ($xRDM['comctocx'] == "IP") {
                        if ($mConIP["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"] != "") {
                          $vCtoCon = $mConIP["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"];
                        }
                      } else {
                        if ($xRDM['ctoidxxx'] == $xRDM['pucidxxx']) {
                          $vCtoCon = $mPUC["{$xRDM['pucidxxx']}"];
                        }
                      }
                    }

                    $xRDM['ctodesxx'] = ($vCtoCon['ctodesx' . strtolower($xRDM['comctoc2'])] != "") ? (($vCtoCon['ctodesx' . strtolower($xRDM['comctoc2'])] != "") ? $vCtoCon['ctodesx' . strtolower($xRDM['comctoc2'])] : $vCtoCon['ctodesxx']) : (($vCtoCon['ctodesx' . strtolower($xRDM['comidxxx'])] != "") ? $vCtoCon['ctodesx' . strtolower($xRDM['comidxxx'])] : $vCtoCon['ctodesxx']);
                    $xRDM['ctodesxx'] = ($xRDM['ctodesxx'] != "") ? $xRDM['ctodesxx'] : "CONCEPTO SIN DESCRIPCION";

                    $zColorPro = "#000000";
                    if ($xRDM['pucterxx'] == "R") {
                      $xRDM['crubasxx'] = (strpos($xRDM['comvlr01'] + 0, '.') > 0) ? number_format($xRDM['comvlr01'], 2, ',', '.') : number_format($xRDM['comvlr01'], 0, ',', '.');
                    } elseif ($xRDM['pucdetxx'] == "C" || $xRDM['pucdetxx'] == "P" || $xRDM['pucdetxx'] == "D") {
                      $xRDM['crubasxx'] = $xRDM['cruce'];
                    } else {
                      $xRDM['crubasxx'] = "";
                    }

                    // Para las cuentas por cobrar o por pagar se obtiene la fecha del documento cruce y fecha de vencimiento del documento cruce
                    $nEncontro = 0;
                    $xRDM['feccruce'] = "";
                    $xRDM['fevencru'] = "";
                    if (($xRDM['pucdetxx'] == "C" || $xRDM['pucdetxx'] == "P") && 
                      !($xRDM['comidxxx'] == $xRDM['comidcxx'] && $xRDM['comcodxx'] == $xRDM['comcodcx'] && $xRDM['comcscxx'] == $xRDM['comcsccx'])) 
                    {
                      for ($nA=$AnoFin; $nA>=$vSysStr['financiero_ano_instalacion_modulo']; $nA--) {
                        $qDocCru  = "SELECT ";
                        $qDocCru .= "comfecxx, ";
                        $qDocCru .= "comfecve ";
                        $qDocCru .= "FROM $cAlfa.{$mTabMov[$nA]} ";
                        $qDocCru .= "WHERE ";
                        $qDocCru .= "comidxxx = \"{$xRDM['comidcxx']}\" AND ";
                        $qDocCru .= "comcodxx = \"{$xRDM['comcodcx']}\" AND ";
                        $qDocCru .= "comcscxx = \"{$xRDM['comcsccx']}\" AND ";
                        $qDocCru .= "pucidxxx = \"{$xRDM['pucidxxx']}\" AND ";
                        $qDocCru .= "teridxxx = \"{$xRDM['teridxxx']}\" LIMIT 0,1";
                        $xDocCru  = f_MySql("SELECT","",$qDocCru,$xConexion01,"");
                        if (mysql_num_rows($xDocCru) > 0) {
                          $nA = $vSysStr['financiero_ano_instalacion_modulo']; $nEncontro = 1;
                          $vDocCru = mysql_fetch_array($xDocCru);
                          $xRDM['feccruce'] = $vDocCru['comfecxx'];
                          $xRDM['fevencru'] = $vDocCru['comfecve'];
                        }
                      }
                    }

                    if ($nEncontro == 0 && ($xRDM['pucdetxx'] == "C" || $xRDM['pucdetxx'] == "P")) {
                      $xRDM['feccruce'] = $xRDM['comfecxx'];
                      $xRDM['fevencru'] = $xRDM['comfecve'];
                    }

                    if ($cCueAux != $xRDM['pucidxxx']) {
                      $mCuentas["{$xRDM['pucidxxx']}"] += 1; //Indica si la cuenta contable tuvo movimiento
                      $cCueAux = $xRDM['pucidxxx'];
                      $mSalAnt = explode("~", f_Saldo_X_Cuenta($xRDM['pucidxxx'], $dFecIni, $dFecFin));
                      $nSalAnt = $mSalAnt[1];
                        //nuevo calculo de saldo anterior por cuenta ?>
                        <tr height="20" style="padding-left:4px;padding-right:4px">
                        <td style="background-color:#084B8A" class="letra7" align="right" colspan="17"><b><font color=white>Saldo Anterior</font></b></td>
                        <td style="background-color:#084B8A" class="letra7" align="right"><b><font color=white><?php echo ($mSalAnt[1] != "") ? ((strpos($mSalAnt[1] + 0, '.') > 0) ? number_format($mSalAnt[1], 2, ',', '.') : number_format($mSalAnt[1], 0, ',', '.')) : "0" ?></font></b></td>
                      </tr>
                      <?php
                    }

                    $nSalAnt = $nSalAnt + (($xRDM['debitoxx']) ? $xRDM['debitoxx'] : 0) - (($xRDM['creditox']) ? $xRDM['creditox'] : 0);
                    ?>
                      <tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">
                        <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $xRDM['pucidxxx'] ?></td>
                        <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xRDM['consecux'] ?></td>
                        <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $xRDM['comfecxx'] ?></td>
                        <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($xRDM['comfecve'] != "") ? $xRDM['comfecve'] : $xRDM['comfecxx'] ?></td>
                        <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo ($xRDM['ctodesxx'] != "") ? $xRDM['ctodesxx'] : "&nbsp;" ?></td>
                        <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo ($xRDM['comobsxx'] != "") ? $xRDM['comobsxx'] : "&nbsp;" ?></td>
                        <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xRDM['teridxxx'] ?></td>
                        <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo (trim($xRDM['clinomxx']) != "") ? trim($xRDM['clinomxx']) : "CLIENTE SIN NOMBRE" ?></td>
                        <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $xRDM['terid2xx'] ?></td>
                        <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo (trim($xRDM['clinom2x']) != "") ? trim($xRDM['clinom2x']) : "CLIENTE SIN NOMBRE" ?></td>
                        <td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($xRDM['crubasxx'] != "") ? $xRDM['crubasxx'] : "&nbsp;" ?></td>
                        <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $xRDM['feccruce'] ?></td>
                        <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $xRDM['fevencru'] ?></td>
                        <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($xRDM['ccoidxxx'] != "") ? $xRDM['ccoidxxx'] : "&nbsp;" ?></td>
                        <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($xRDM['sccidxxx'] != "") ? $xRDM['sccidxxx'] : "&nbsp;" ?></td>
                        <td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($xRDM['debitoxx'] != "") ? ((strpos($xRDM['debitoxx'] + 0, '.') > 0) ? number_format($xRDM['debitoxx'], 2, ',', '.') : number_format($xRDM['debitoxx'], 0, ',', '.')) : "&nbsp;" ?></td>
                        <td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($xRDM['creditox'] != "") ? ((strpos($xRDM['creditox'] + 0, '.') > 0) ? number_format($xRDM['creditox'], 2, ',', '.') : number_format($xRDM['creditox'], 0, ',', '.')) : "&nbsp;" ?></td>
                        <td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($nSalAnt != "") ? ((strpos($nSalAnt + 0, '.') > 0) ? number_format($nSalAnt, 2, ',', '.') : number_format($nSalAnt, 0, ',', '.')) : "0" ?></td>
                      </tr>
                  <?php 
                } 
                
                //Se debe traer el saldo de las cuentas contables de las que no encontro movimiento
                foreach ($mCuentas as $cKey => $cValue) {
                  if ($mCuentas[$cKey] == 0) {
                    $mSalAnt = explode("~", f_Saldo_X_Cuenta($cKey, $dFecIni, $dFecFin));
                    $nSalAnt = $mSalAnt[1];
                    //nuevo calculo de saldo anterior por cuenta ?>
                    <tr height="20" style="padding-left:4px;padding-right:4px">
                      <td style="background-color:#084B8A;padding-right:4px" class="letra7" align="right" colspan="17"><b><font color=white>[Cuenta <?php echo $cKey ?>] Saldo Anterior</font></b></td>
                      <td style="background-color:#084B8A" class="letra7" align="right"><b><font color=white><?php echo ($mSalAnt[1] != "") ? ((strpos($mSalAnt[1] + 0, '.') > 0) ? number_format($mSalAnt[1], 2, ',', '.') : number_format($mSalAnt[1], 0, ',', '.')) : "0" ?></font></b></td>
                    </tr>
                    <?php
                  }
                }              
                ?>
                  </table>
                </center>
              </form>
              <script type="text/javascript">document.getElementById('loading').style.display="none";</script>
            </body>
          </html>
          <?php
        }
      break;

      case 2: /* PINTA POR EXCEL */
        $cNomFile = "REPORTE_AUXILIAR_POR_CUENTA_" . $kUser . "_" . date("YmdHis") . ".xls";

        if ($_SERVER["SERVER_PORT"] != "") {
          $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
        } else {
          $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
        }

        if (file_exists($cFile)) {
          unlink($cFile);
        }

        $fOp = fopen($cFile, 'a');

        $data = '<table border="1" cellspacing="0" cellpadding="0" width="1600" align=center style="margin:5px">';
        $data .= '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
        $data .= '<td class="name" colspan="18" align="left">';
        $data .= '<font size="3">';
        $data .= '<b>REPORTE AUXILIAR POR CUENTA<BR>';
        $data .= 'RANGO CUENTAS: ' . $gPucIdIni . ' - ' . $gPucIdFin . '<br>';
        $data .= 'PERIODO: ' . $gDesde . ' - ' . $gHasta . '<br>';
        $data .= '</b>';
        $data .= '</font>';
        $data .= '</td>';
        $data .= '</tr>';
        $data .= '<tr height="20">';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>Cuenta</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="150px"><b><font color=white>Comprobante</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha Comprobante</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha Vencimiento Comprobante</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Descripci&oacute;n</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Detalle</font></b></td>';				
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>Nit</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Tercero</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>Nit</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Tercero</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="150px"><b><font color=white>Cruce-Base</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha Cruce-Base</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha Vencimiento Cruce-Base</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>CC</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="80px"><b><font color=white>SC</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Debitos</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Creditos</font></b></td>';
        $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Saldo Movimiento</font></b></td>';
        $data .= '</tr>';

        fwrite($fOp, $data);

        $cCueAux = "";
        $nCanReg = 0;
        while ($xRDM = mysql_fetch_array($xDatMov)) {

          $nCanReg++;
          if (($nCanReg % _NUMREG_) == 0) {
            $xConexion01 = fnReiniciarConexion();
          }

          //Trayendo nombre del cliente
          $qCliente = "SELECT CLIIDXXX, ";
          $qCliente .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
          $qCliente .= "FROM $cAlfa.SIAI0150 ";
          $qCliente .= "WHERE ";
          $qCliente .= "CLIIDXXX = \"{$xRDM['teridxxx']}\" LIMIT 0,1 ";
          $xCliente = f_MySql("SELECT", "", $qCliente, $xConexion01, "");
          $vCliente = mysql_fetch_array($xCliente);
          $xRDM['clinomxx'] = $vCliente['CLINOMXX'];

          //Trayendo nombre del proveedor
          $qProveedor = "SELECT CLIIDXXX, ";
          $qProveedor .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
          $qProveedor .= "FROM $cAlfa.SIAI0150 ";
          $qProveedor .= "WHERE ";
          $qProveedor .= "CLIIDXXX = \"{$xRDM['terid2xx']}\" LIMIT 0,1 ";
          $xProveedor = f_MySql("SELECT", "", $qProveedor, $xConexion01, "");
          $vProveedor = mysql_fetch_array($xProveedor);
          $xRDM['clinom2x'] = $vProveedor['CLINOMXX'];

          if ($mConceptos["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"] != "") {
            $vCtoCon = $mConceptos["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"];
          } else {
            if ($xRDM['comctocx'] == "IP") {
              if ($mConIP["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"] != "") {
                $vCtoCon = $mConIP["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"];
              }
            } else {
              if ($xRDM['ctoidxxx'] == $xRDM['pucidxxx']) {
                $vCtoCon = $mPUC["{$xRDM['pucidxxx']}"];
              }
            }
          }

          $xRDM['ctodesxx'] = ($vCtoCon['ctodesx' . strtolower($xRDM['comctoc2'])] != "") ? (($vCtoCon['ctodesx' . strtolower($xRDM['comctoc2'])] != "") ? $vCtoCon['ctodesx' . strtolower($xRDM['comctoc2'])] : $vCtoCon['ctodesxx']) : (($vCtoCon['ctodesx' . strtolower($xRDM['comidxxx'])] != "") ? $vCtoCon['ctodesx' . strtolower($xRDM['comidxxx'])] : $vCtoCon['ctodesxx']);
          $xRDM['ctodesxx'] = ($xRDM['ctodesxx'] != "") ? $xRDM['ctodesxx'] : "CONCEPTO SIN DESCRIPCION";

          $zColorPro = "#000000";
          if ($xRDM['pucterxx'] == "R") {
            $xRDM['crubasxx'] = (strpos($xRDM['comvlr01'] + 0, '.') > 0) ? number_format($xRDM['comvlr01'], 2, ',', '') : number_format($xRDM['comvlr01'], 0, ',', '');
          } elseif ($xRDM['pucdetxx'] == "C" || $xRDM['pucdetxx'] == "P" || $xRDM['pucdetxx'] == "D") {
            $xRDM['crubasxx'] = $xRDM['cruce'];
          } else {
            $xRDM['crubasxx'] = "";
          }

          // Para las cuentas por cobrar o por pagar se obtiene la fecha del documento cruce y fecha de vencimiento del documento cruce
          $nEncontro = 0;
          $xRDM['feccruce'] = "";
          $xRDM['fevencru'] = "";
          if (($xRDM['pucdetxx'] == "C" || $xRDM['pucdetxx'] == "P") && 
            !($xRDM['comidxxx'] == $xRDM['comidcxx'] && $xRDM['comcodxx'] == $xRDM['comcodcx'] && $xRDM['comcscxx'] == $xRDM['comcsccx'])) 
          {
            for ($nA=$AnoFin; $nA>=$vSysStr['financiero_ano_instalacion_modulo']; $nA--) {
              $qDocCru  = "SELECT ";
              $qDocCru .= "comfecxx, ";
              $qDocCru .= "comfecve ";
              $qDocCru .= "FROM $cAlfa.{$mTabMov[$nA]} ";
              $qDocCru .= "WHERE ";
              $qDocCru .= "comidxxx = \"{$xRDM['comidcxx']}\" AND ";
              $qDocCru .= "comcodxx = \"{$xRDM['comcodcx']}\" AND ";
              $qDocCru .= "comcscxx = \"{$xRDM['comcsccx']}\" AND ";
              $qDocCru .= "pucidxxx = \"{$xRDM['pucidxxx']}\" AND ";
              $qDocCru .= "teridxxx = \"{$xRDM['teridxxx']}\" LIMIT 0,1";
              $xDocCru  = f_MySql("SELECT","",$qDocCru,$xConexion01,"");
              if (mysql_num_rows($xDocCru) > 0) {
                $nA = $vSysStr['financiero_ano_instalacion_modulo']; $nEncontro = 1;
                $vDocCru = mysql_fetch_array($xDocCru);
                $xRDM['feccruce'] = $vDocCru['comfecxx'];
                $xRDM['fevencru'] = $vDocCru['comfecve'];
              }
            }
          }

          if ($nEncontro == 0 && ($xRDM['pucdetxx'] == "C" || $xRDM['pucdetxx'] == "P")) {
            $xRDM['feccruce'] = $xRDM['comfecxx'];
            $xRDM['fevencru'] = $xRDM['comfecve'];
          }

          if ($cCueAux != $xRDM['pucidxxx']) {
            $mCuentas["{$xRDM['pucidxxx']}"] += 1; //Indica si la cuenta contable tuvo movimiento
            $cCueAux = $xRDM['pucidxxx'];
            $mSalAnt = explode("~", f_Saldo_X_Cuenta($xRDM['pucidxxx'], $dFecIni, $dFecFin));
            $nSalAnt = $mSalAnt[1];
            //nuevo calculo de saldo anterior por cuenta
            $data = '<tr height="20" style="padding-left:4px;padding-right:4px">';
            $data .= '<td style="background-color:#084B8A" class="letra7" align="right" colspan="17"><b><font color=white>Saldo Anterior</font></b></td>';
            $data .= '<td style="background-color:#084B8A" class="letra7" align="right"><b><font color=white>' . (($mSalAnt[1] != "") ? ((strpos($mSalAnt[1] + 0, '.') > 0) ? number_format($mSalAnt[1], 2, ',', '') : number_format($mSalAnt[1], 0, ',', '')) : "0") . '</font></b></td>';
            $data .= '</tr>';
            fwrite($fOp, $data);
          }

          $nSalAnt = $nSalAnt + (($xRDM['debitoxx']) ? $xRDM['debitoxx'] : 0) - (($xRDM['creditox']) ? $xRDM['creditox'] : 0);

          $nValor01 = ($xRDM['ctodesxx'] != "") ? $xRDM['ctodesxx'] : "&nbsp;";
          $nValor02 = ($xRDM['crubasxx'] != "") ? $xRDM['crubasxx'] : "&nbsp;";
          $nValor03 = ($xRDM['ccoidxxx'] != "") ? $xRDM['ccoidxxx'] : "&nbsp;";
          $nValor04 = ($xRDM['sccidxxx'] != "") ? $xRDM['sccidxxx'] : "&nbsp;";
          $nValor05 = ($xRDM['debitoxx'] != "") ? ((strpos($xRDM['debitoxx'] + 0, '.') > 0) ? number_format($xRDM['debitoxx'], 2, ',', '') : number_format($xRDM['debitoxx'], 0, ',', '')) : "&nbsp;";
          $nValor06 = ($xRDM['creditox'] != "") ? ((strpos($xRDM['creditox'] + 0, '.') > 0) ? number_format($xRDM['creditox'], 2, ',', '') : number_format($xRDM['creditox'], 0, ',', '')) : "&nbsp;";
          $nValor07 = ($nSalAnt != "") ? ((strpos($nSalAnt + 0, '.') > 0) ? number_format($nSalAnt, 2, ',', '') : number_format($nSalAnt, 0, ',', '')) : "0";
          $nValor08 = ($xRDM['comobsxx'] != "") ? $xRDM['comobsxx'] : "&nbsp;";

          $data = '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
          $data .= '<td class="letra7" align="center" style = "color:' . $zColorPro . '">' . $xRDM['pucidxxx'] . '</td>';
          $data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . $xRDM['consecux'] . '</td>';
          $data .= '<td class="letra7" align="center" style = "color:' . $zColorPro . ';mso-number-format:yyyy-mm-dd">' . $xRDM['comfecxx'] . '</td>';
          $data .= '<td class="letra7" align="center" style = "color:' . $zColorPro . ';mso-number-format:yyyy-mm-dd">' . (($xRDM['comfecve'] != "") ? $xRDM['comfecve'] : $xRDM['comfecxx']) . '</td>';
          $data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . $nValor01 . '</td>';
          $data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . $nValor08 . '</td>';
          $data .= '<td class="letra7" align="left"   style = "mso-number-format:\'\@\'; color:' . $zColorPro . '">' . $xRDM['teridxxx'] . '</td>';
          $data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . ((trim($xRDM['clinomxx']) != "") ? trim($xRDM['clinomxx']) : "CLIENTE SIN NOMBRE") . '</td>';
          $data .= '<td class="letra7" align="left"   style = "mso-number-format:\'\@\'; color:' . $zColorPro . '">' . $xRDM['terid2xx'] . '</td>';
          $data .= '<td class="letra7" align="left"   style = "color:' . $zColorPro . '">' . ((trim($xRDM['clinom2x']) != "") ? trim($xRDM['clinom2x']) : "CLIENTE SIN NOMBRE") . '</td>';
          $data .= '<td class="letra7" align="right"  style = "color:' . $zColorPro . '">' . $nValor02 . '</td>';
          $data .= '<td class="letra7" align="right"  style = "color:' . $zColorPro . ';mso-number-format:yyyy-mm-dd">' . $xRDM['feccruce'] . '</td>';
          $data .= '<td class="letra7" align="right"  style = "color:' . $zColorPro . ';mso-number-format:yyyy-mm-dd">' . $xRDM['fevencru'] . '</td>';
          $data .= '<td class="letra7" align="center" style = "mso-number-format:\'\@\'; color:' . $zColorPro . '">' . $nValor03 . '</td>';
          $data .= '<td class="letra7" align="center" style = "mso-number-format:\'\@\'; color:' . $zColorPro . '">' . $nValor04 . '</td>';
          $data .= '<td class="letra7" align="right"  style = "color:' . $zColorPro . '">' . $nValor05 . '</td>';
          $data .= '<td class="letra7" align="right"  style = "color:' . $zColorPro . '">' . $nValor06 . '</td>';
          $data .= '<td class="letra7" align="right"  style = "color:' . $zColorPro . '">' . $nValor07 . '</td>';
          $data .= '</tr>';
          fwrite($fOp, $data);
        }

        //Se debe traer el saldo de las cuentas contables de las que no encontro movimiento
        foreach ($mCuentas as $cKey => $cValue) {
          if ($mCuentas[$cKey] == 0) {
            $mSalAnt = explode("~", f_Saldo_X_Cuenta($cKey, $dFecIni, $dFecFin));
            $nSalAnt = $mSalAnt[1];
            $data  = '<tr height="20" style="padding-left:4px;padding-right:4px">';
            $data .= '<td style="background-color:#084B8A" class="letra7" align="right" colspan="17"><b><font color=white>[Cuenta '.$cKey.'] Saldo Anterior</font></b></td>';
            $data .= '<td style="background-color:#084B8A" class="letra7" align="right"><b><font color=white>' . (($mSalAnt[1] != "") ? ((strpos($mSalAnt[1] + 0, '.') > 0) ? number_format($mSalAnt[1], 2, ',', '') : number_format($mSalAnt[1], 0, ',', '')) : "0") . '</font></b></td>';
            $data .= '</tr>';
            fwrite($fOp, $data);
          }
        } 
        $data = '</table>';
        fwrite($fOp, $data);
        fclose($fOp);

        if (file_exists($cFile)) {
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
            exit;
          } else {
            $cNomArc = $cNomFile;
          }
        } else {
          $nSwitch = 1;
          if ($_SERVER["SERVER_PORT"] != "") {
            f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
          } else {
            $cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
          }
        }
      break;

      case 3: /* EXCEL SIN FORMATO */
        $cNomFile = "REPORTE_AUXILIAR_POR_CUENTA_" . $kUser . "_" . date("YmdHis") . ".xls";
        
        if ($_SERVER["SERVER_PORT"] != "") {
          $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
        } else {
          $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
        }

        if (file_exists($cFile)) {
          unlink($cFile);
        }

        $fOp = fopen($cFile, 'a');

        $data = 'REPORTE AUXILIAR POR CUENTA' . chr(13);
        $data .= 'RANGO CUENTAS: ' . $gPucIdIni . ' - ' . $gPucIdFin . chr(13);
        $data .= 'PERIODO: ' . $gDesde . ' - ' . $gHasta . chr(13);

        $data .= 'Cuenta' . chr(9);
        $data .= 'Comprobante' . chr(9);
        $data .= 'Fecha Comprobante' . chr(9);
        $data .= 'Fecha Vencimiento Comprobante' . chr(9);
        $data .= 'Descripcion' . chr(9);
        $data .= 'Detalle' . chr(9);
        $data .= 'Nit' . chr(9);
        $data .= 'Tercero' . chr(9);
        $data .= 'Nit' . chr(9);
        $data .= 'Tercero' . chr(9);
        $data .= 'Cruce-Base' . chr(9);
        $data .= 'Fecha Cruce-Base' . chr(9);
        $data .= 'Fecha Vencimiento Cruce-Base' . chr(9);
        $data .= 'CC' . chr(9);
        $data .= 'SC' . chr(9);
        $data .= 'Debitos' . chr(9);
        $data .= 'Creditos' . chr(9);
        $data .= 'Saldo Movimiento' . chr(13);

        fwrite($fOp, $data);

        $cCueAux = "";
        $nCanReg = 0;

        while ($xRDM = mysql_fetch_array($xDatMov)) {

          $nCanReg++;
          if (($nCanReg % _NUMREG_) == 0) {
            $xConexion01 = fnReiniciarConexion();
          }

          //Trayendo nombre del cliente
          $qCliente = "SELECT CLIIDXXX, ";
          $qCliente .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
          $qCliente .= "FROM $cAlfa.SIAI0150 ";
          $qCliente .= "WHERE ";
          $qCliente .= "CLIIDXXX = \"{$xRDM['teridxxx']}\" LIMIT 0,1 ";
          $xCliente = f_MySql("SELECT", "", $qCliente, $xConexion01, "");
          $vCliente = mysql_fetch_array($xCliente);
          $xRDM['clinomxx'] = $vCliente['CLINOMXX'];

          //Trayendo nombre del proveedor
          $qProveedor = "SELECT CLIIDXXX, ";
          $qProveedor .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
          $qProveedor .= "FROM $cAlfa.SIAI0150 ";
          $qProveedor .= "WHERE ";
          $qProveedor .= "CLIIDXXX = \"{$xRDM['terid2xx']}\" LIMIT 0,1 ";
          $xProveedor = f_MySql("SELECT", "", $qProveedor, $xConexion01, "");
          $vProveedor = mysql_fetch_array($xProveedor);
          $xRDM['clinom2x'] = $vProveedor['CLINOMXX'];

          if ($mConceptos["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"] != "") {
            $vCtoCon = $mConceptos["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"];
          } else {
            if ($xRDM['comctocx'] == "IP") {
              if ($mConIP["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"] != "") {
                $vCtoCon = $mConIP["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"];
              }
            } else {
              if ($xRDM['ctoidxxx'] == $xRDM['pucidxxx']) {
                $vCtoCon = $mPUC["{$xRDM['pucidxxx']}"];
              }
            }
          }

          $xRDM['ctodesxx'] = ($vCtoCon['ctodesx' . strtolower($xRDM['comctoc2'])] != "") ? (($vCtoCon['ctodesx' . strtolower($xRDM['comctoc2'])] != "") ? $vCtoCon['ctodesx' . strtolower($xRDM['comctoc2'])] : $vCtoCon['ctodesxx']) : (($vCtoCon['ctodesx' . strtolower($xRDM['comidxxx'])] != "") ? $vCtoCon['ctodesx' . strtolower($xRDM['comidxxx'])] : $vCtoCon['ctodesxx']);
          $xRDM['ctodesxx'] = ($xRDM['ctodesxx'] != "") ? $xRDM['ctodesxx'] : "CONCEPTO SIN DESCRIPCION";

          $zColorPro = "#000000";
          if ($xRDM['pucterxx'] == "R") {
            $xRDM['crubasxx'] = (strpos($xRDM['comvlr01'] + 0, '.') > 0) ? number_format($xRDM['comvlr01'], 2, ',', '.') : number_format($xRDM['comvlr01'], 0, ',', '.');
          } elseif ($xRDM['pucdetxx'] == "C" || $xRDM['pucdetxx'] == "P" || $xRDM['pucdetxx'] == "D") {
            $xRDM['crubasxx'] = $xRDM['cruce'];
          } else {
            $xRDM['crubasxx'] = "";
          }

          // Para las cuentas por cobrar o por pagar se obtiene la fecha del documento cruce y fecha de vencimiento del documento cruce
          $nEncontro = 0;
          $xRDM['feccruce'] = "";
          $xRDM['fevencru'] = "";
          if (($xRDM['pucdetxx'] == "C" || $xRDM['pucdetxx'] == "P") && 
            !($xRDM['comidxxx'] == $xRDM['comidcxx'] && $xRDM['comcodxx'] == $xRDM['comcodcx'] && $xRDM['comcscxx'] == $xRDM['comcsccx'])) 
          {
            for ($nA=$AnoFin; $nA>=$vSysStr['financiero_ano_instalacion_modulo']; $nA--) {
              $qDocCru  = "SELECT ";
              $qDocCru .= "comfecxx, ";
              $qDocCru .= "comfecve ";
              $qDocCru .= "FROM $cAlfa.{$mTabMov[$nA]} ";
              $qDocCru .= "WHERE ";
              $qDocCru .= "comidxxx = \"{$xRDM['comidcxx']}\" AND ";
              $qDocCru .= "comcodxx = \"{$xRDM['comcodcx']}\" AND ";
              $qDocCru .= "comcscxx = \"{$xRDM['comcsccx']}\" AND ";
              $qDocCru .= "pucidxxx = \"{$xRDM['pucidxxx']}\" AND ";
              $qDocCru .= "teridxxx = \"{$xRDM['teridxxx']}\" LIMIT 0,1";
              $xDocCru  = f_MySql("SELECT","",$qDocCru,$xConexion01,"");
              if (mysql_num_rows($xDocCru) > 0) {
                $nA = $vSysStr['financiero_ano_instalacion_modulo']; $nEncontro = 1;
                $vDocCru = mysql_fetch_array($xDocCru);
                $xRDM['feccruce'] = $vDocCru['comfecxx'];
                $xRDM['fevencru'] = $vDocCru['comfecve'];
              }
            }
          }

          if ($nEncontro == 0 && ($xRDM['pucdetxx'] == "C" || $xRDM['pucdetxx'] == "P")) {
            $xRDM['feccruce'] = $xRDM['comfecxx'];
            $xRDM['fevencru'] = $xRDM['comfecve'];
          }

          if ($cCueAux != $xRDM['pucidxxx']) {
            $mCuentas["{$xRDM['pucidxxx']}"] += 1; //Indica si la cuenta contable tuvo movimiento
            $cCueAux = $xRDM['pucidxxx'];
            $mSalAnt = explode("~", f_Saldo_X_Cuenta($xRDM['pucidxxx'], $dFecIni, $dFecFin));
            $nSalAnt = $mSalAnt[1];
            //nuevo calculo de saldo anterior por cuenta
            $data = chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= 'Saldo Anterior' . chr(9);
            $data .= (($mSalAnt[1] != "") ? ((strpos($mSalAnt[1] + 0, '.') > 0) ? number_format($mSalAnt[1], 2, ',', '') : number_format($mSalAnt[1], 0, ',', '')) : "0") . chr(13);

            fwrite($fOp, $data);
          }

          $nSalAnt = $nSalAnt + (($xRDM['debitoxx']) ? $xRDM['debitoxx'] : 0) - (($xRDM['creditox']) ? $xRDM['creditox'] : 0);

          $nValor01 = ($xRDM['ctodesxx'] != "") ? $xRDM['ctodesxx'] : "";
          $nValor02 = ($xRDM['crubasxx'] != "") ? $xRDM['crubasxx'] : "";
          $nValor03 = ($xRDM['ccoidxxx'] != "") ? "'" . $xRDM['ccoidxxx'] : "";
          $nValor04 = ($xRDM['sccidxxx'] != "") ? "'" . $xRDM['sccidxxx'] : "";
          $nValor05 = ($xRDM['debitoxx'] != "") ? ((strpos($xRDM['debitoxx'] + 0, '.') > 0) ? number_format($xRDM['debitoxx'], 2, ',', '') : number_format($xRDM['debitoxx'], 0, ',', '')) : "";
          $nValor06 = ($xRDM['creditox'] != "") ? ((strpos($xRDM['creditox'] + 0, '.') > 0) ? number_format($xRDM['creditox'], 2, ',', '') : number_format($xRDM['creditox'], 0, ',', '')) : "";
          $nValor07 = ($nSalAnt != "") ? ((strpos($nSalAnt + 0, '.') > 0) ? number_format($nSalAnt, 2, ',', '') : number_format($nSalAnt, 0, ',', '')) : "0";
          $nValor08 = ($xRDM['comobsxx'] != "") ? $xRDM['comobsxx'] : "";	

          $data  = $xRDM['pucidxxx'] . chr(9);
          $data .= $xRDM['consecux'] . chr(9);
          $data .= $xRDM['comfecxx'] . chr(9);
          $data .= (($xRDM['comfecve'] != "") ? $xRDM['comfecve'] : $xRDM['comfecxx']) . chr(9);
          $data .= $nValor01 . chr(9);
          $data .= $nValor08 . chr(9);
          $data .= $xRDM['teridxxx'] . chr(9);
          $data .= (((trim($xRDM['clinomxx']) != "") ? trim($xRDM['clinomxx']) : "CLIENTE SIN NOMBRE")) . chr(9);
          $data .= $xRDM['terid2xx'] . chr(9);
          $data .= (((trim($xRDM['clinom2x']) != "") ? trim($xRDM['clinom2x']) : "CLIENTE SIN NOMBRE")) . chr(9);
          $data .= $nValor02 . chr(9);
          $data .= $xRDM['feccruce'] . chr(9);
          $data .= $xRDM['fevencru'] . chr(9);
          $data .= $nValor03 . chr(9);
          $data .= $nValor04 . chr(9);
          $data .= $nValor05 . chr(9);
          $data .= $nValor06 . chr(9);
          $data .= $nValor07 . chr(13);
          fwrite($fOp, $data);
        }

        //Se debe traer el saldo de las cuentas contables de las que no encontro movimiento
        foreach ($mCuentas as $cKey => $cValue) {
          if ($mCuentas[$cKey] == 0) {
            $mSalAnt = explode("~", f_Saldo_X_Cuenta($cKey, $dFecIni, $dFecFin));
            $nSalAnt = $mSalAnt[1];
            //nuevo calculo de saldo anterior por cuenta
            $data = chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
            $data .= chr(9);
						$data .= chr(9);
            $data .= chr(9);						
            $data .= '[Cuenta '.$cKey.'] Saldo Anterior' . chr(9);
            $data .= (($mSalAnt[1] != "") ? ((strpos($mSalAnt[1] + 0, '.') > 0) ? number_format($mSalAnt[1], 2, ',', '') : number_format($mSalAnt[1], 0, ',', '')) : "0") . chr(13);
            fwrite($fOp, $data);
          }
        }  
        fclose($fOp);

        if (file_exists($cFile)) {
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
            exit;
          } else {
            $cNomArc = $cNomFile;
          }
        } else {
          $nSwitch = 1;

          if ($_SERVER["SERVER_PORT"] != "") {
            f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
          } else {
            $cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
          }
        }
      break;

      case 4: /* PINTA POR PDF */
        if ($_SERVER["SERVER_PORT"] != "") {
          $cRoot = $_SERVER['DOCUMENT_ROOT'];

          define('FPDF_FONTPATH', $_SERVER['DOCUMENT_ROOT'] . $cSystem_Fonts_Directory . '/');
          require($_SERVER['DOCUMENT_ROOT'] . $cSystem_Class_Directory . '/fpdf/fpdf.php');

          class PDF extends FPDF {
            function Header(){
              global $cRoot;
              global $cPlesk_Skin_Directory;
              global $cAlfa;
              global $gPucIdIni;
              global $gPucIdFin;
              global $gDesde;
              global $gHasta;
              global $nPag;

              if ($cAlfa == "INTERLOG" || $cAlfa == "DESARROL" || $cAlfa == "PRUEBASX") {

                $this->SetXY(5, 7);
                $this->Cell(47, 28, '', 1, 0, 'C');
                $this->Cell(223, 28, '', 1, 0, 'C');

              // Dibujo //
              $this->Image($cRoot . $cPlesk_Skin_Directory . '/MaryAire.jpg', 8, 8, 40, 25);

                $this->SetFont('verdana', '', 12);
                $this->SetXY(40, 9);
                $this->Cell(213, 8, "REPORTE AUXILIAR POR CUENTA", 0, 0, 'C');
                $this->Ln(8);
                $this->SetFont('verdana', '', 8);
                $this->SetX(40);
                $this->Cell(213, 6, 'RANGO CUENTAS: ' . $gPucIdIni . ' - ' . $gPucIdFin, 0, 0, 'C');
                $this->Ln(6);
                $this->SetFont('verdana', '', 8);
                $this->SetX(40);
                $this->Cell(213, 6, "DE: " . $gDesde . "  A:   " . $gHasta, 0, 0, 'C');
                $this->Ln(10);
                $this->SetX(5);
              } else {
                $this->SetXY(5, 7);
                $this->Cell(270, 24, '', 1, 0, 'C');

                switch ($cAlfa) {
                  case "TRLXXXXX":
                  case "DETRLXXXXX":
                  case "TETRLXXXXX":
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logobma1.jpg', 6, 8, 50, 22);
                  break;
                  case "ADIMPEXX":
                  case "TEADIMPEXX":
                  case "DEADIMPEXX":
                    // case "DEGRUPOGLA":
                    // case "TEGRUPOGLA":
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logoAdimpex.jpg', 7, 8, 25, 22);
                  break;
                  case "GRUMALCO":
                  case "TEGRUMALCO":
                  case "DEGRUMALCO":
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logomalco.jpg', 7, 10, 35, 18);                
                  break;
                  case "ALADUANA":
                  case "TEALADUANA":
                  case "DEALADUANA":
                  $this->Image($cRoot . $cPlesk_Skin_Directory . '/logoaladuana.jpg', 6, 9, 32, 21);
                    break;
                  case "ANDINOSX":
                  case "TEANDINOSX":
                  case "DEANDINOSX":
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logoAndinos2.jpeg', 10, 9, 18, 20);
                  break;
                  case "GRUPOALC":
                  case "TEGRUPOALC":
                  case "DEGRUPOALC":
                    $this->Image($cRoot.$cPlesk_Skin_Directory.'/logoalc.jpg', 6, 9, 35, 20);
                  break;
                  case "AAINTERX":
                  case "TEAAINTERX":
                  case "DEAAINTERX":
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logointernacional.jpg', 14, 9, 32, 21);
                  break;
                  case "AALOPEZX":
                  case "TEAALOPEZX":
                  case "DEAALOPEZX":
                  	$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaalopez.png',14,9,30);
                  break;
                  case "ADUAMARX":
                  case "TEADUAMARX":
                  case "DEADUAMARX":
                    $this->Image($cRoot.$cPlesk_Skin_Directory.'/logoaduamar.jpg', 8, 9, 20);
                  break;
                  case "SOLUCION":
                  case "TESOLUCION":
                  case "DESOLUCION":
                    $this->Image($cRoot.$cPlesk_Skin_Directory.'/logosoluciones.jpg', 8, 10, 40);
									break;
									case "FENIXSAS":
									case "TEFENIXSAS":
									case "DEFENIXSAS":
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logofenix.jpg', 8, 12, 43);
									break;
									case "COLVANXX":
									case "TECOLVANXX":
									case "DECOLVANXX":
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logocolvan.jpg', 8, 9, 43);
									break;
									case "INTERLAC":
									case "TEINTERLAC":
									case "DEINTERLAC":
										$this->Image($cRoot.$cPlesk_Skin_Directory.'/logointerlace.jpg', 8, 9, 43);
									break;
									case "DHLEXPRE": //DHLEXPRE
									case "TEDHLEXPRE": //DHLEXPRE
									case "DEDHLEXPRE": //DHLEXPRE
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logo_dhl_express.jpg', 7, 8, 40, 22);                  
									break;
                  case "KARGORUX": //KARGORUX
									case "TEKARGORUX": //KARGORUX
									case "DEKARGORUX": //KARGORUX
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logokargoru.jpg', 7, 8, 40, 22);
									break;
                  case "ALOGISAS": //LOGISTICA
                  case "TEALOGISAS": //LOGISTICA
                  case "DEALOGISAS": //LOGISTICA
										$this->Image($cRoot . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 7, 8, 53);
									break;
                  case "PROSERCO":
                  case "TEPROSERCO":
                  case "DEPROSERCO":
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logoproserco.png', 7, 8, 38);
                  break;
                  case "MANATIAL":
                  case "TEMANATIAL":
                  case "DEMANATIAL":
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logomanantial.jpg', 7, 12, 50, 12);
                  break;
                  case "DSVSASXX":
                  case "DEDSVSASXX":
                  case "TEDSVSASXX":
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logodsv.jpg', 7, 8.3, 40, 22);
                  break;
                  case "MELYAKXX":    //MELYAK
                  case "DEMELYAKXX":  //MELYAK
                  case "TEMELYAKXX":  //MELYAK
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logomelyak.jpg', 7,10,40,18);
                  break;
                  case "FEDEXEXP":    //FEDEX
                  case "DEFEDEXEXP":  //FEDEX
                  case "TEFEDEXEXP":  //FEDEX
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logofedexexp.jpg', 7,10,40,20);
                  break;
                  case "EXPORCOM":    //EXPORCOMEX
                  case "DEEXPORCOM":  //EXPORCOMEX
                  case "TEEXPORCOM":  //EXPORCOMEX
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logoexporcomex.jpg', 7,9,40,20);
                  break;
                  case "HAYDEARX":   //HAYDEARX
                  case "DEHAYDEARX": //HAYDEARX
                  case "TEHAYDEARX": //HAYDEARX
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logohaydear.jpeg', 7,9,60,20);
                  break;
                  case "CONNECTA":   //CONNECTA
                  case "DECONNECTA": //CONNECTA
                  case "TECONNECTA": //CONNECTA
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logoconnecta.jpg', 7,9,35,20);
                  break;
                  case "CONLOGIC":   //CONLOGIC
                  case "DECONLOGIC": //CONLOGIC
                  case "TECONLOGIC": //CONLOGIC
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/logoconlogic.jpg', 7,9,31,20);
                  break;
                  case "OPENEBCO":   //OPENEBCO
                  case "DEOPENEBCO": //OPENEBCO
                  case "TEOPENEBCO": //OPENEBCO
                    $this->Image($cRoot . $cPlesk_Skin_Directory . '/opentecnologia.JPG', 7,9,40,20);
                  break;
                }

                $this->SetFont('verdana', '', 12);
                $this->SetXY(5, 8);
                $this->Cell(285, 8, "REPORTE AUXILIAR POR CUENTA", 0, 0, 'C');
                $this->Ln(7);
                $this->SetFont('verdana', '', 8);
                $this->SetX(5);
                $this->Cell(285, 6, 'RANGO CUENTAS: ' . $gPucIdIni . ' - ' . $gPucIdFin, 0, 0, 'C');
                $this->Ln(5);
                $this->SetFont('verdana', '', 8);
                $this->SetX(5);
                $this->Cell(285, 6, "DE: " . $gDesde . "  A:   " . $gHasta, 0, 0, 'C');
                $this->Ln(10);
                $this->SetX(5);
              }

              if ($this->PageNo() > 1 && $nPag == 1) {
                $this->Ln(5);
                $this->SetFillColor(11, 97, 11);
                $this->SetTextColor(255);
                $this->SetFont('verdana', 'B', 6);
                $this->SetX(5);
                $this->Cell(16, 5, "Cuenta", 1, 0, 'C', 1);
                $this->Cell(25, 5, "Comprobante", 1, 0, 'C', 1);
                $this->Cell(15, 5, "Fecha", 1, 0, 'C', 1);
                $this->Cell(20, 5, "Descripcion", 1, 0, 'C', 1);
                $this->Cell(15, 5, "Detalle", 1, 0, 'C', 1);								
                $this->Cell(15, 5, "Nit", 1, 0, 'C', 1);
                $this->Cell(20, 5, "Tercero", 1, 0, 'C', 1);
                $this->Cell(15, 5, "Nit", 1, 0, 'C', 1);
                $this->Cell(20, 5, "Tercero", 1, 0, 'C', 1);
                $this->Cell(25, 5, "Cruce-Base", 1, 0, 'C', 1);
                $this->Cell(12, 5, "CC", 1, 0, 'C', 1);
                $this->Cell(12, 5, "SC", 1, 0, 'C', 1);
                $this->Cell(20, 5, "Debitos", 1, 0, 'C', 1);
                $this->Cell(20, 5, "Creditos", 1, 0, 'C', 1);
                $this->Cell(20, 5, "Saldo Mov.", 1, 0, 'C', 1);

                $this->SetFillColor(255);
                $this->SetTextColor(0);

                $this->Ln(5);
                $this->SetX(5);
                $this->SetFont('verdana', '', 6);
                $this->SetWidths(array('16', '25', '15', '20', '15', '15', '20', '15', '20', '25', '12', '12', '20', '20', '20'));
                $this->SetAligns(array('C', 'L', 'C', 'L', 'L', 'C', 'L', 'C', 'L', 'R', 'C', 'C', 'R', 'R', 'R'));
                $this->SetX(5);
              }

            }

            function Footer() {
              $this->SetY(-10);
              $this->SetFont('verdana', '', 6);
              $this->Cell(0, 5, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
            }

            function SetWidths($w) {
              //Set the array of column widths
              $this->widths = $w;
            }

            function SetAligns($a) {
              //Set the array of column alignments
              $this->aligns = $a;
            }

            function Row($data) {
              //Calculate the height of the row
              $nb = 0;
              for ($i = 0; $i < count($data); $i++)
                $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
              $h = 4 * $nb;
              //Issue a page break first if needed
              $this->CheckPageBreak($h);
              //Draw the cells of the row
              for ($i = 0; $i < count($data); $i++) {
                $w = $this->widths[$i];
                $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                //Save the current position
                $x = $this->GetX();
                $y = $this->GetY();
                //Draw the border
                $this->Rect($x, $y, $w, $h);
                //Print the text
                $this->MultiCell($w, 4, $data[$i], 0, $a);
                //Put the position to the right of the cell
                $this->SetXY($x + $w, $y);
              }
              //Go to the next line
              $this->Ln($h);
            }

            function CheckPageBreak($h){
              //If the height h would cause an overflow, add a new page immediately
              if ($this->GetY() + $h > $this->PageBreakTrigger){
                $this->AddPage($this->CurOrientation);
              }
            }

            function NbLines($w, $txt){
              //Computes the number of lines a MultiCell of width w will take
              $cw = &$this->CurrentFont['cw'];
              if ($w == 0)
                $w = $this->w - $this->rMargin - $this->x;
              $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
              $s = str_replace("\r", '', $txt);
              $nb = strlen($s);
              if ($nb > 0 and $s[$nb - 1] == "\n")
                $nb--;
              $sep = -1;
              $i = 0;
              $j = 0;
              $l = 0;
              $nl = 1;
              while ($i < $nb) {
                $c = $s[$i];
                if ($c == "\n") {
                  $i++;
                  $sep = -1;
                  $j = $i;
                  $l = 0;
                  $nl++;
                  continue;
                }
                if ($c == ' ')
                  $sep = $i;
                $l += $cw[$c];
                if ($l > $wmax) {
                  if ($sep == -1) {
                    if ($i == $j)
                      $i++;
                  } else
                    $i = $sep + 1;
                  $sep = -1;
                  $j = $i;
                  $l = 0;
                  $nl++;
                } else
                  $i++;
              }
              return $nl;
            }

          }

          $pdf = new PDF('L', 'mm', 'Letter');
          $pdf->AddFont('verdana', '', '');
          $pdf->AddFont('verdana', 'B', '');
          $pdf->AliasNbPages();
          $pdf->SetMargins(0, 0, 0);

          $pdf->AddPage();
          $pdf->Ln(5);
          $pdf->SetFillColor(11, 97, 11);
          $pdf->SetTextColor(255);
          $pdf->SetFont('verdana', 'B', 6);
          $pdf->SetX(5);
          $pdf->Cell(16, 5, "Cuenta", 1, 0, 'C', 1);
          $pdf->Cell(25, 5, "Comprobante", 1, 0, 'C', 1);
          $pdf->Cell(15, 5, "Fecha", 1, 0, 'C', 1);
          $pdf->Cell(20, 5, "Descripcion", 1, 0, 'C', 1);
          $pdf->Cell(15, 5, "Detalle", 1, 0, 'C', 1);
          $pdf->Cell(15, 5, "Nit", 1, 0, 'C', 1);
          $pdf->Cell(20, 5, "Tercero", 1, 0, 'C', 1);
          $pdf->Cell(15, 5, "Nit", 1, 0, 'C', 1);
          $pdf->Cell(20, 5, "Tercero", 1, 0, 'C', 1);
          $pdf->Cell(25, 5, "Cruce-Base", 1, 0, 'C', 1);
          $pdf->Cell(12, 5, "CC", 1, 0, 'C', 1);
          $pdf->Cell(12, 5, "SC", 1, 0, 'C', 1);
          $pdf->Cell(20, 5, "Debitos", 1, 0, 'C', 1);
          $pdf->Cell(20, 5, "Creditos", 1, 0, 'C', 1);
          $pdf->Cell(20, 5, "Saldo Mov.", 1, 0, 'C', 1);

          $pdf->SetFillColor(255);
          $pdf->SetTextColor(0);

          $pdf->Ln(5);
          $pdf->SetX(5);
          $pdf->SetFont('verdana', '', 6);
          $pdf->SetWidths(array('16', '25', '15', '20', '15', '15', '20', '15', '20', '25', '12', '12', '20', '20', '20'));
          $pdf->SetAligns(array('C', 'L', 'C', 'L', 'L', 'C', 'L', 'C', 'L', 'R', 'C', 'C', 'R', 'R', 'R'));

          $nPag = 0;

          $cCueAux = "";
          $nCanReg = 0;
          while ($xRDM = mysql_fetch_array($xDatMov)) {

            $nCanReg++;
            if (($nCanReg % _NUMREG_) == 0) {
              $xConexion01 = fnReiniciarConexion();
            }

            $nPag = 1;

            //Trayendo nombre del cliente
            $qCliente = "SELECT CLIIDXXX, ";
            $qCliente .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
            $qCliente .= "FROM $cAlfa.SIAI0150 ";
            $qCliente .= "WHERE ";
            $qCliente .= "CLIIDXXX = \"{$xRDM['teridxxx']}\" LIMIT 0,1 ";
            $xCliente = f_MySql("SELECT", "", $qCliente, $xConexion01, "");
            $vCliente = mysql_fetch_array($xCliente);
            $xRDM['clinomxx'] = $vCliente['CLINOMXX'];

            //Trayendo nombre del proveedor
            $qProveedor = "SELECT CLIIDXXX, ";
            $qProveedor .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLIAPE1X,\" \",CLIAPE2X,\" \",CLINOM1X,\" \",CLINOM2X)) AS CLINOMXX ";
            $qProveedor .= "FROM $cAlfa.SIAI0150 ";
            $qProveedor .= "WHERE ";
            $qProveedor .= "CLIIDXXX = \"{$xRDM['teridxxx']}\" LIMIT 0,1 ";
            $xProveedor = f_MySql("SELECT", "", $qProveedor, $xConexion01, "");
            $vProveedor = mysql_fetch_array($xProveedor);
            $xRDM['clinom2x'] = $vProveedor['CLINOMXX'];

            if ($mConceptos["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"] != "") {
              $vCtoCon = $mConceptos["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"];
            } else {
              if ($xRDM['comctocx'] == "IP") {
                if ($mConIP["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"] != "") {
                  $vCtoCon = $mConIP["{$xRDM['ctoidxxx']}~{$xRDM['pucidxxx']}"];
                }
              } else {
                if ($xRDM['ctoidxxx'] == $xRDM['pucidxxx']) {
                  $vCtoCon = $mPUC["{$xRDM['pucidxxx']}"];
                }
              }
            }

            $xRDM['ctodesxx'] = ($vCtoCon['ctodesx' . strtolower($xRDM['comctoc2'])] != "") ? (($vCtoCon['ctodesx' . strtolower($xRDM['comctoc2'])] != "") ? $vCtoCon['ctodesx' . strtolower($xRDM['comctoc2'])] : $vCtoCon['ctodesxx']) : (($vCtoCon['ctodesx' . strtolower($xRDM['comidxxx'])] != "") ? $vCtoCon['ctodesx' . strtolower($xRDM['comidxxx'])] : $vCtoCon['ctodesxx']);
            $xRDM['ctodesxx'] = ($xRDM['ctodesxx'] != "") ? $xRDM['ctodesxx'] : "CONCEPTO SIN DESCRIPCION";

            $zColorPro = "#000000";
            if ($xRDM['pucterxx'] == "R") {
              $xRDM['crubasxx'] = (strpos($xRDM['comvlr01'] + 0, '.') > 0) ? number_format($xRDM['comvlr01'], 2, ',', '.') : number_format($xRDM['comvlr01'], 0, ',', '.');
            } elseif ($xRDM['pucdetxx'] == "C" || $xRDM['pucdetxx'] == "P" || $xRDM['pucdetxx'] == "D") {
              $xRDM['crubasxx'] = $xRDM['cruce'];
            } else {
              $xRDM['crubasxx'] = "";
            }
            if ($cCueAux != $xRDM['pucidxxx']) {
              $mCuentas["{$xRDM['pucidxxx']}"] += 1; //Indica si la cuenta contable tuvo movimiento
              $cCueAux = $xRDM['pucidxxx'];
              $mSalAnt = explode("~", f_Saldo_X_Cuenta($xRDM['pucidxxx'], $dFecIni, $dFecFin));
              $nSalAnt = $mSalAnt[1];

              $pdf->SetFillColor(8, 75, 138);
              $pdf->SetTextColor(255);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->SetX(5);
              $pdf->Cell(250, 5, "Saldo Anterior", 1, 0, 'R', 1);
              $pdf->Cell(20, 5, (($mSalAnt[1] != "") ? ((strpos($mSalAnt[1] + 0, '.') > 0) ? number_format($mSalAnt[1], 2, ',', '.') : number_format($mSalAnt[1], 0, ',', '.')) : "0"), 1, 0, 'C', 1);

              $pdf->SetFillColor(255);
              $pdf->SetTextColor(0);

              $pdf->Ln(5);
              $pdf->SetX(5);
              $pdf->SetFont('verdana', '', 6);
            }
            $nSalAnt = $nSalAnt + (($xRDM['debitoxx']) ? $xRDM['debitoxx'] : 0) - (($xRDM['creditox']) ? $xRDM['creditox'] : 0);

            $nValor01 = ($xRDM['ctodesxx'] != "") ? $xRDM['ctodesxx'] : "";
            $nValor08 = ($xRDM['comobsxx'] != "") ? $xRDM['comobsxx'] : "";	
            $nValor02 = ($xRDM['crubasxx'] != "") ? $xRDM['crubasxx'] : "";
            $nValor03 = ($xRDM['ccoidxxx'] != "") ? $xRDM['ccoidxxx'] : "";
            $nValor04 = ($xRDM['sccidxxx'] != "") ? $xRDM['sccidxxx'] : "";
            $nValor05 = ($xRDM['debitoxx'] != "") ? ((strpos($xRDM['debitoxx'] + 0, '.') > 0) ? number_format($xRDM['debitoxx'], 2, ',', '.') : number_format($xRDM['debitoxx'], 0, ',', '.')) : "";
            $nValor06 = ($xRDM['creditox'] != "") ? ((strpos($xRDM['creditox'] + 0, '.') > 0) ? number_format($xRDM['creditox'], 2, ',', '.') : number_format($xRDM['creditox'], 0, ',', '.')) : "";
            $nValor07 = ($nSalAnt != "") ? (strpos($nSalAnt + 0, '.') > 0) ? number_format($nSalAnt, 2, ',', '.') : number_format($nSalAnt, 0, ',', '.') : "0";

            $pdf->SetX(5);
            $pdf->Row(array(
              $xRDM['pucidxxx'],
              $xRDM['consecux'],
              $xRDM['comfecxx'],
              $nValor01,
              $nValor08,
              $xRDM['teridxxx'],
              ((trim($xRDM['clinomxx']) != "") ? trim($xRDM['clinomxx']) : "CLIENTE SIN NOMBRE"),
              $xRDM['terid2xx'],
              ((trim($xRDM['clinom2x']) != "") ? trim($xRDM['clinom2x']) : "CLIENTE SIN NOMBRE"),
              $nValor02,
              $nValor03,
              $nValor04,
              $nValor05,
              $nValor06,
              $nValor07
            ));
          }

          //Se debe traer el saldo de las cuentas contables de las que no encontro movimiento
          foreach ($mCuentas as $cKey => $cValue) {
            if ($mCuentas[$cKey] == 0) {
              $mSalAnt = explode("~", f_Saldo_X_Cuenta($cKey, $dFecIni, $dFecFin));
              $nSalAnt = $mSalAnt[1];

              $pdf->SetFillColor(8, 75, 138);
              $pdf->SetTextColor(255);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->SetX(5);
              $pdf->Cell(250, 5, "[Cuenta $cKey] Saldo Anterior", 1, 0, 'R', 1);
              $pdf->Cell(20, 5, (($mSalAnt[1] != "") ? ((strpos($mSalAnt[1] + 0, '.') > 0) ? number_format($mSalAnt[1], 2, ',', '.') : number_format($mSalAnt[1], 0, ',', '.')) : "0"), 1, 0, 'C', 1);

              $pdf->SetFillColor(255);
              $pdf->SetTextColor(0);

              $pdf->Ln(5);
              $pdf->SetX(5);
              $pdf->SetFont('verdana', '', 6);
            }
          }

          $nPag = 0;
          $cNomFile = "pdf_" . $kUser . "_" . date("YmdHis") . ".pdf";

          $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;

          $pdf->Output($cFile);

          if (file_exists($cFile)) {
            chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
          } else {
            $nSwitch = 1;
            f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
          }

          echo "<html><script>document.location='$cFile';</script></html>";
        }
      break;
    }
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


class cEstructurasAuxiliarPorCuenta {
  /**
   * Metodo que se encarga de Crear las Estructuras de las Tablas.
   */
  function fnCrearEstructurasAuxiliarPorCuenta($dAnio) {
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
    $mReturnConexionTM = $this->fnConectarDBAuxiliarPorCuenta();
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
    $cTabCar = mt_rand(1000000000, 9999999999);
    $cTabla  = "memauxcu" . $cTabCar;

    $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
    $qNewTab .= "lineaidx INT(11)      	NOT NULL AUTO_INCREMENT, ";  //autoincremental
    $qNewTab .= "comidxxx varchar(1)   	NOT NULL, ";
    $qNewTab .= "comcodxx varchar(4)  	NOT NULL, ";
    $qNewTab .= "comcscxx varchar(20) 	NOT NULL, ";
    $qNewTab .= "comcsc2x varchar(20) 	NOT NULL, ";
    $qNewTab .= "teridxxx varchar(12) 	NOT NULL, ";
    $qNewTab .= "pucidxxx varchar(10) 	NOT NULL, ";
    $qNewTab .= "comfecxx DATE          NOT NULL, ";
    $qNewTab .= "comfecve DATE          NOT NULL, ";
    $qNewTab .= "PRIMARY KEY (lineaidx)) ENGINE=MyISAM "; //MyISAM
    $xNewTab  = mysql_query($qNewTab, $xConexionTM);

    if (!$xNewTab) {
      $nSwitch = 1;
      $mReturn[count($mReturn)] = "(" . __LINE__ . ") Error al Crear Tabla Temporal para Reporte Auxiliar por Cuenta." . mysql_error($xConexionTM);
    }

    if($nSwitch == 0){
      $mReturn[0] = "true"; 
      $mReturn[1] = $cTabla;
    }else{
      $mReturn[0] = "false";
    }
    return $mReturn;
  } ## function fnCrearEstructurasAuxiliarPorCuenta(){ ##

  /**
   * Metodo que realiza la conexion.
   */
  function fnConectarDBAuxiliarPorCuenta() {
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
  } ##function fnConectarDBAuxiliarPorCuenta(){##

  /**
   * Metodo que reinicia la conexion.
   */
  function fnReiniciarConexionDBAuxiliarPorCuenta($pConexion){
    global $cHost;  global $cUserHost;  global $cPassHost;

    mysql_close($pConexion);
    $xConexion01 = mysql_connect($cHost,$cUserHost,$cPassHost,TRUE);

    return $xConexion01;
  }##function fnReiniciarConexionDBAuxiliarPorCuenta(){##
}
?>

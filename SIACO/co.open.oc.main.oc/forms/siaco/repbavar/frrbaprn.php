<?php
  /**
	 * Imprime Reporte Proformas.
	 * --- Descripcion: Permite Imprimir Reporte Proformas de BAVARIA
   * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package openComex
   * @version 001
   */

  set_time_limit (0);
  ini_set("memory_limit","512M");
  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");

  /**
   * Variable de control de errores.
   * 
   * @var int
   */
  $nSwitch = 0;

  /**
   * Variable para almacenar los errores.
   * 
   * @var string
   */
  $cMsj = "\n";

  /**
   * Variables para reemplazar caracteres especiales.
   * 
   * @var array
   */
  $cBuscar = array('"',"'","<",">","«","»","%",chr(13),chr(10),chr(27),chr(9));
	$cReempl = array('\"', "\'", " ", " ", " ", " ");

  /**
   * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales.
   * 
   * @var int
   */
  $cEjePro = 0;

  /**
   * Nombre(s) de los archivos en excel generados.
   * 
   * @var string
   */
  $cNomArc = "";

  /**
   * Cantidad de Registros para reiniciar conexion.
   */
  define("_NUMREG_",100);

  /**
   * Cuando se ejecuta desde el cron debe armarse la cookie para incluir los utilitys
   */
  if ($_SERVER["SERVER_PORT"] == "") {
    $vArg = explode(",", $argv[1]);

    if ($vArg[0] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El parametro Id del Proceso no puede ser vacio.\n";
    }

    if ($vArg[1] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El parametro de la Cookie no puede ser vacio.\n";
    }

    if ($nSwitch == 0) {
      $_COOKIE["kDatosFijos"] = $vArg[1];

      include("{$OPENINIT['pathdr']}/opencomex/libs/php/utility.php");
      include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
      include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php");
      include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utirebav.php");

      /**
       * Buscando el ID del proceso
       */
      $qProBg  = "SELECT * ";
      $qProBg .= "FROM $cBeta.sysprobg ";
      $qProBg .= "WHERE ";
      $qProBg .= "pbaidxxx= \"{$vArg[0]}\" AND ";
      $qProBg .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
      $xProBg  = f_MySql("SELECT","",$qProBg,$xConexion01,"");
      if (mysql_num_rows($xProBg) == 0) {
        $xRPB = mysql_fetch_array($xProBg);
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Proceso en Background [{$vArg[0]}] No Existe o ya fue Procesado.\n";
      } else {
        $xRB = mysql_fetch_array($xProBg);
        /**
         * Reconstruyendo Post
         */
        $mPost = f_Explode_Array($xRB['pbapostx'],"|","~");
        for ($nP=0; $nP<count($mPost); $nP++) {
          if ($mPost[$nP][0] != "") {
            $_POST[$mPost[$nP][0]] = $mPost[$nP][1];
          }
        }
      }
    }
  }

  /**
   * Subiendo el archivo al sistema y cargando los datos en las tablas temporales
   */
  if ($_SERVER["SERVER_PORT"] != "") {
    include("../../../libs/php/utility.php");
    include("../../../config/config.php");
    include("../../../libs/php/utiprobg.php");
    include("../../../financiero/libs/php/utirebav.php");
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

  if ($_SERVER["SERVER_PORT"] != "") {
    /*** Ejecutar proceso en Background ***/
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;

    /**
     * Validando Licencia
     */
    $nLic = f_Licencia();
    if ($nLic == 0) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "Error grave de Seguridad otro usuario ingreso con su clave.\n";
    }

    if ($_POST['cTerId'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
      $cMsj .= "Debe Seleccionar el Cliente.\n";
    }

    if (($_POST['dDesde'] == "0000-00-00" || $_POST['dDesde'] == "") && ($_POST['dHasta'] == "0000-00-00" && $_POST['dHasta'] == "")) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Debe Seleccionar un Rango de Fechas.\n";
    } else {
      // Valida que el rango de fechas sea maximo de 6 meses 
      $dFecLimi = date("d-m-Y",strtotime($_POST['dHasta']."- 6 month"));
      $dFecLimi = strtotime($dFecLimi);
      $dDesde   = strtotime($_POST['dDesde']);
      if($dDesde < $dFecLimi) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Rango de Fechas no Puede ser Mayor a 6 Meses.\n";
      }
    }

    /**
     * Validando que El Cliente escogido exista
     */
    if ($_POST['cTerId'] != "") {
      $qUser  = "SELECT ";
      $qUser .= "USRTIPXX,";
      $qUser .= "USRCLIXX ";
      $qUser .= "FROM $cAlfa.SIAI0003 ";
      $qUser .= "WHERE ";
      $qUser .= "USRIDXXX = \"$kUser\" AND ";
      $qUser .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
      $xUser  = f_MySql("SELECT","",$qUser,$xConexion01,"");
      $vUser  = mysql_fetch_array($xUser);
      $cClientes = "";
      $vClientes = array();
      if ($vUser['USRTIPXX'] == "CLIENTE") {
        if($vUser['USRCLIXX'] != ""){
          $vClientes = explode("~",$vUser['USRCLIXX']);
        }

        if(!in_array($_POST['cTerId'],$vClientes)){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
          $cMsj .= "El Cliente {$_POST['cTerId']} no se Encuentra Asignado a su Usuario.\n";
        }
      }

      if(in_array($_POST['cTerId'],$vClientes) || $vUser['USRTIPXX'] != "CLIENTE"){
        $qCliente  = "SELECT ";
        $qCliente .= "CLIIDXXX,";
        $qCliente .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) AS CLINOMXX ";
        $qCliente .= "FROM $cAlfa.SIAI0150 ";
        $qCliente .= "WHERE ";
        $qCliente .= "CLICLIXX = \"SI\" AND ";
        $qCliente .= "CLIIDXXX = \"{$_POST['cTerId']}\" AND ";
        $qCliente .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
        $xCliente  = f_MySql("SELECT", "", $qCliente, $xConexion01, "");
        $vCliente  = mysql_fetch_array($xCliente);
        $_POST['cCliNom'] = $vCliente['CLINOMXX'];
        if (mysql_num_rows($xCliente) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
          $cMsj .= "El Cliente {$_POST['cTerId']} No Existe en la Base de Datos.\n";
        }
      }
    }

    if ($nSwitch == 0) {
      /**
       * Instanciando objeto para generar el reporte proformas
       */
      $objEstructurasReporteProformasBavaria = new cEstructurasReporteProformasBavaria();

      //Creando Tabla Temporal de Movimiento
      $vParametros = array();
      $vParametros['TIPOTABL'] = "TEMPORAL";
      $mReturnTablaT = $objEstructurasReporteProformasBavaria->fnCrearEstructurasReporteProformasBavaria($vParametros);

      //Creando Tabla Temporal de Errores
      $vParametros = array();
      $vParametros['TIPOTABL'] = "ERRORES";
      $mReturnTablaE = $objEstructurasReporteProformasBavaria->fnCrearEstructurasReporteProformasBavaria($vParametros);

      //Imprimir parametros de la tabla creada (temporal)
      if($mReturnTablaT[0] == "false") {
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnTablaT);$nR++){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "{$mReturnTablaT[$nR]}\n";
        }
      }

      if($mReturnTablaE[0] == "false") {
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnTablaE);$nR++){
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "{$mReturnTablaE[$nR]}\n";
        }
      }
    }
  }

  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;
    $cTablas = $mReturnTablaT[1]."~".$mReturnTablaE[1];
		$strPost = "gTerId~" 	 . $gTerId.
							"|gTerNom~"  . $gTerNom.
							"|gSucId~" 	 . $gSucId.
							"|gDexId~"   . $gDexId.
							"|gComId~" 	 . $gComId.
							"|gComCod~"  . $gComCod.
							"|gComCsc~"  . $gComCsc.
							"|gComCsc2~" . $gComCsc2.
							"|gNumGui~"  . $gNumGui.
							"|gDesde~" 	 . $gDesde.
							"|gHasta~"   . $gHasta.
							"|gPerAno~"  . $gPerAno.
							"|cEjProBg~" . $cEjProBg;

		$vParBg['pbadbxxx'] = $cAlfa;                       // Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                // Modulo
		$vParBg['pbatinxx'] = "REPORTEPROFORMASBAVARIA";    // Tipo Interface
		$vParBg['pbatinde'] = "REPORTE PROFORMAS BAVARIA";  // Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = $gSucId;                      // Sucursal
		$vParBg['doiidxxx'] = $gDexId;                      // Dex
		$vParBg['doisfidx'] = "";                           // Sufijo
		$vParBg['cliidxxx'] = $gTerId;                      // Nit
		$vParBg['clinomxx'] = $gTerNom;                     // Nombre Importador
		$vParBg['pbapostx'] = $strPost;                     // Parametros para reconstruir Post
		$vParBg['pbatabxx'] = $cTablas;                     // Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];	// Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];      // cookie
		$vParBg['pbacrexx'] = 0;                            // Cantidad Registros
		$vParBg['pbatxixx'] = 1;                            // Tiempo Ejecucion x Item en Segundos
		$vParBg['pbaopcxx'] = "";                           // Opciones
		$vParBg['regusrxx'] = $kUser;                       // Usuario que Creo Registro

		## Incluyendo la clase de procesos en background
		$ObjProBg = new cProcesosBackground();
		$mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);

		## Imprimiendo resumen de todo ok.
		if ($mReturnProBg[0] == "true") {
			f_Mensaje(__FILE__, __LINE__, "Proceso en Background Agendado con Exito."); ?>
			<script languaje = "javascript">
					parent.fmwork.fnRecargar();
			</script>
		<?php } else {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}			
		}
	}

  if ($_SERVER["SERVER_PORT"] == "") {
		$gTerId   = $_POST['gTerId'];
		$gTerNom  = $_POST['gTerNom'];
		$gSucId   = $_POST['gSucId'];
		$gDexId   = $_POST['gDexId'];
		$gComId   = $_POST['gComId'];
		$gComCod  = $_POST['gComCod'];
		$gComCsc  = $_POST['gComCsc'];
		$gComCsc2 = $_POST['gComCsc2'];
		$gDesde   = $_POST['gDesde'];
		$gHasta   = $_POST['gHasta'];
		$gPerAno  = $_POST['gPerAno'];

    /**
     * Armando parametros de las tablas
     */
    $mTablas = explode("~",$xRB['pbatabxx']);
    
    /**
     * Vectore de tablas temporales
     */
    $mReturnTablaT[1] = $mTablas[0];
    $mReturnTablaE[1] = $mTablas[1];
	}

  // Inicia el proceso para generar el Excel
  if ($cEjePro == 0) {
		if ($nSwitch == 0) {
      // se instancia la clase que genera el Reporte
			$objReporteProformasBavaria = new cRoporteProformasBavaria();

      $vDatos = array();
      $vDatos['TABLAXXX'] = $mReturnTablaT[1];
      $vDatos['TABLAERR'] = $mReturnTablaE[1];
			$vDatos['TERIDXXX'] = $gTerId;           //ID CLIENTE
			$vDatos['TERNOMXX'] = $gTerNom;          //NOMBRE DEL CLIENTE
			$vDatos['SUCIDXXX'] = $gSucId;           //ID DE LA SUCURSAL
			$vDatos['DOCIDXXX'] = $gDexId;           //ID DEL DO
			$vDatos['PERANOXX'] = $gPerAno;          //AÑO DE LA FACTURA
			$vDatos['COMIDXXX'] = $gComId;           //ID DE LA FACTURA
			$vDatos['COMCODXX'] = $gComCod;          //CODIGO DE LA FACTURA
			$vDatos['COMCSCXX'] = $gComCsc;          //CONSECUTIVO DE LAFACTURA
			$vDatos['COMCSC2X'] = $gComCsc2;         //CONSECUTIVO 2 DE LA FACTURA
			$vDatos['DDESDEXX'] = $gDesde;           //FECHA DESDE
			$vDatos['DHASTAXX'] = $gHasta;           //FECHA HASTA
			$mReturnReporte = $objReporteProformasBavaria->fnReporteProformasBavaria($vDatos);

      if($mReturnReporte[0] == "true"){
        $cNomArc = $mReturnReporte[1];
      }
		}
	}

  if ($_SERVER["SERVER_PORT"] == "") {
    // Consulta la tabla errores
    $qTabErro  = "SELECT * ";
    $qTabErro .= "FROM $cAlfa.{$mReturnTablaE[1]}";
    $xTabErro  = f_MySql("SELECT", "", $qTabErro, $xConexion01, "");
    if (mysql_num_rows($xTabErro) > 0) {
      $nSwitch = 1;
      while($xRTE = mysql_fetch_array($xTabErro)) {
        $cMsj .= $xRTE['DESERROR']."\n";
      }
    }

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
    $ObjProBg     = new cProcesosBackground();
    $mReturnProBg = $ObjProBg->fnFinalizarProcesoBackground($vParBg);

    #Imprimiendo resumen de todo ok.
    if ($mReturnProBg[0] == "false") {
      $nSwitch = 1;
      for($nR=1;$nR<count($mReturnProBg);$nR++){
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= $mReturnProBg[$nR]."\n";
      }
    }
  }//if ($_SERVER["SERVER_PORT"] == "") {

  if ($nSwitch == 1){
    if ($_SERVER["SERVER_PORT"] != "") {
      f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
    }
  }
?>

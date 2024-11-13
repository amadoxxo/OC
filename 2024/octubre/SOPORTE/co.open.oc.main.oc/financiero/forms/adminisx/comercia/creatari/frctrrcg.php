<?php
  /**
   * Reporte Tarifas Consolidado.
   * --- Descripcion: Permite generar el Excel del Reporte de Tarifas Consolidado 
   * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package openComex
   * @version 001
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");
  set_time_limit(0);
  ini_set("memory_limit","4096M");

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
   * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales.
   * 
   * @var int
   */
  $cEjePro = 0;

  /**
   * Nombre(s) de los archivos en excel generados.
   * 
   * @var array
   */
  $vNomArc = array();

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
      include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utictari.php");

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
    include("../../../../libs/php/utility.php");
    include("../../../../../config/config.php");
    include("../../../../../libs/php/utiprobg.php");
    include("../../../../libs/php/utictari.php");
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
    
    $cNomCliGru = "";
    if($gApliTar == "CLIENTE" && $gCliId != ""){
      $vCliIds = explode(',', $gCliId);
      for ($i=0;$i<count($vCliIds);$i++) {
        // Busco el nombre del cliente
        $qCliNom  = "SELECT ";
        $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS CLINOMXX ";
        $qCliNom .= "FROM $cAlfa.SIAI0150 ";
        $qCliNom .= "WHERE ";
        $qCliNom .= "CLIIDXXX = \"{$vCliIds[$i]}\" LIMIT 0,1;";
        $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
        if (mysql_num_rows($xCliNom) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Cliente [\"{$vCliIds[$i]}\"] No Existe.\n";
        } else {
          $vCliNom = mysql_fetch_array($xCliNom);
          $cNomCliGru = $vCliNom['CLINOMXX'];
        }
      }
    }

    if($gApliTar == "GRUPO" && $gCliId != ""){
      // Valido que Exista el Grupo de tarifas
      $qGruTar = "SELECT gtadesxx FROM $cAlfa.fpar0111 WHERE gtaidxxx = \"$gCliId\" LIMIT 0,1";
      $xGruTar  = f_MySql("SELECT","",$qGruTar,$xConexion01,"");
      if (mysql_num_rows($xGruTar) != 1) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Grupo de Tarifas [$gCliId] No Existe..\n";
      } else {
        $vGruTar = mysql_fetch_array($xGruTar);
        $cNomCliGru = $vGruTar['gtadesxx'];
      }
    }

    $objTablasTemporales = new cEstructurasTarfiasFacturacion();

    //Creando Tabla Temporal de Tarifas
    $vParametros = array();
    $vParametros['TIPOTABL'] = "REPORTE";
    $mReturnTablaT  = $objTablasTemporales->fnCrearEstructurasTarifasFacturacion($vParametros);
    if($mReturnTablaT[0] == "false"){
      $nSwitch = 1;
      for($nR=1;$nR<count($mReturnTablaT);$nR++){
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= $mReturnTablaT[$nR]."\n";
      }
    }

    //Creando Tabla Temporal de Errores
    $vParametros = array();
    $vParametros['TIPOTABL'] = "ERRORES";
    $mReturnTablaE  = $objTablasTemporales->fnCrearEstructurasTarifasFacturacion($vParametros);
    if($mReturnTablaE[0] == "false"){
      $nSwitch = 1;
      for($nR=1;$nR<count($mReturnTablaE);$nR++){
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= $mReturnTablaE[$nR]."\n";
      }
    }
  }

  //Valido que se envien los parametros necesarios
  if (!($gCliId != "" || $gDesde != "" || $gDesde != "0000-00-00" || $gHasta != "" || $gHasta != "0000-00-00")) {
    $nSwitch = 1;
    $cMsj .= "Debe seleccionar Cliente/Grupo o un Rango de Fechas.\n";
  }

  if (($gDesde != "" && $gDesde != "0000-00-00") || ($gHasta != "" && $gHasta != "0000-00-00")) {
    // Valida que el rango de fechas sea maximo de 6 meses 
    // Valida que el rango de fechas sea maximo de 6 meses 
    $dFecLimi = date("d-m-Y",strtotime($_POST['dHasta']."- 36 month"));
    $dFecLimi = strtotime($dFecLimi);
    $dDesde   = strtotime($_POST['dDesde']);
    if($dDesde < $dFecLimi) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El Rango de Fechas no Puede ser Mayor a 36 Meses.\n";
    }
  }

  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {


    $cEjePro = 1;
    $cTablas = $mReturnTablaT[1]."~".$mReturnTablaE[1];
    $strPost = "gEstTari~" . $gEstTari.
              "|gTipoOpe~" . $gTipoOpe.
              "|gApliTar~" . $gApliTar.
              "|gCliId~"   . $gCliId.
              "|gEstCli~"  . $gEstCli.
              "|gSerId~"   . $gSerId.
              "|gFcoId~"   . $gFcoId.
              "|gTipoFec~" . $gTipoFec.
              "|gHasta~"   . $gHasta.
              "|gDesde~" 	 . $gDesde.
              "|cEjProBg~" . $cEjProBg;

    $vParBg['pbadbxxx'] = $cAlfa;                       // Base de Datos
    $vParBg['pbamodxx'] = "FACTURACION";                // Modulo
    $vParBg['pbatinxx'] = "REPTARIFASCON";              // Tipo Interface
    $vParBg['pbatinde'] = "REPORTE TARIFAS CONSOLIDADO";// Descripcion Tipo de Interfaz
    $vParBg['admidxxx'] = "";                           // Sucursal
    $vParBg['doiidxxx'] = "";                           // Dex
    $vParBg['doisfidx'] = "";                           // Sufijo
    if (count($vCliIds) > 1) {
      $vParBg['cliidxxx'] = "VARIOS";
    } else {
      $vParBg['cliidxxx'] = $gCliId;                    // Nit
    }
    $vParBg['clinomxx'] = $cNomCliGru;                  // Nombre Importador
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
    $gEstTari = $_POST['gEstTari'];
    $gApliTar = $_POST['gApliTar'];
    $gCliId   = $_POST['gCliId'];
    $gEstCli  = $_POST['gEstCli'];
    $gSerId   = $_POST['gSerId'];
    $gFcoId   = $_POST['gFcoId'];
    $gTipoFec = $_POST['gTipoFec'];
    $gHasta   = $_POST['gHasta'];
    $gDesde   = $_POST['gDesde'];

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
      $objReporteTarifasConsolidado = new cTarifasFacturacion();

      $vDatos = array();
      $vDatos['TABLAXXX'] = $mReturnTablaT[1];
      $vDatos['TABLAERR'] = $mReturnTablaE[1];
      $vDatos['ORIGENXX'] = "REPORTE"; //ORIGEN
      $vDatos['ESTTARIX'] = $gEstTari; //ESTADO TARIFA
      $vDatos['TIPOPEXX'] = $gTipoOpe; //TIPO OPERACION
      $vDatos['APLITARX'] = $gApliTar; //APLICA TARIFAS
      $vDatos['CLIIDXXX'] = $gCliId;   //ID DEL CLIENTE
      $vDatos['ESTCLIXX'] = $gEstCli;  //ESTADO CLIENTE
      $vDatos['SERIDXXX'] = $gSerId;   //ID DEL CONCEPTO DE COBRO
      $vDatos['FCOIDXXX'] = $gFcoId;   //ID DE LA FORMA DE COBRO
      $vDatos['TIPOFECX'] = $gTipoFec; //TIPO FECHA
      $vDatos['FECDESDE'] = $gDesde;   //FECHA DESDE
      $vDatos['FECHASTA'] = $gHasta;   //FECHA HASTA
      $mReturnReporte = $objReporteTarifasConsolidado->fnReporteTarifasConsolidado($vDatos);

      if($mReturnReporte[0] == "true"){
        $vNomArc = $mReturnReporte[1];
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
    $vParBg['pbaexcxx'] = implode("~", $vNomArc);                   //Nombre Archivos Excel
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

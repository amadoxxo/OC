<?php
  namespace openComex;
 /**
	* Generar Data para reporte
	* @author Johana Arboleda Ramos <johana.arboleda@opentecnologia.com.co>
	* @package openComex
	*/

  ini_set("memory_limit","1024M");
  set_time_limit(0);

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  /**
   * Variables de control de errores
   */
  $nSwitch = 0; // Switch para Vericar la Validacion de Datos
	$cMsj = ""; // Mensaje a mostrar si hay errores

  /**
   * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales
   * @var Number
   */
  $cEjePro = 0;

  /**
   * Para este proceso por defecto se ejecutara por background
   */
  $_POST['cEjProBg'] = "SI";

  /**
   * Cantidad de registos
   */
  $nCanReg = 0;

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

      include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
      include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utility.php");
      include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utiescar.php");
      include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php");

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
        $cMsj .= "El Proceso [{$vArg[0]}] No Existe o ya fue Procesado.\n";
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
    include("../../../../../config/config.php");
    include("../../../../libs/php/utility.php");
    include("../../../../libs/php/utiescar.php");
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

  if ($_SERVER["SERVER_PORT"] != "") {

    // Validando que el usuario haya dado un solo click en el boton guardar.
    if ($_POST['nTimesSave'] != 1) {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El Sistema Detecto mas de un Click en el Boton Guardar.\n";
    }

    /**
     * Solo puede ejecutarse un proceso en background a la vez
     */
    $qProBg  = "SELECT * ";
    $qProBg .= "FROM $cBeta.sysprobg ";
    $qProBg .= "WHERE ";
    $qProBg .= "pbamodxx = \"MODFACTURACION\" AND ";
    $qProBg .= "pbatinxx = \"ESTADOCARTERA\" AND ";
    $qProBg .= "regusrxx = \"$kUser\" AND ";
    $qProBg .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
    $xProBg  = f_MySql("SELECT","",$qProBg,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qProBg."~".mysql_num_rows($xProBg));
    if (mysql_num_rows($xProBg) == 1) {
      $xRPB = mysql_fetch_array($xProBg);
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Existe un Proceso en Curso para Generar Datos del Reporte de Cartera.\n";
    }

    #Ejecutar proceso en Background
    $_POST['cEjProBg'] = ($_POST['cEjProBg'] != "SI") ? "NO" : $_POST['cEjProBg'];
  }

  if ($_SERVER["SERVER_PORT"] != "") {
    #Instancionado Objetos de Estructuras Reporte Estado Cartera
    $objEstructurasEstadoCartera = new cEstructurasEstadoCartera();

    ##Instanciando Objeto para Creacion de Estructuras##
    $vParametros['TIPOESTU'] = "ERRORES";
    $mReturnTablaE  = $objEstructurasEstadoCartera->fnCrearEstructurasEstadoCartera($vParametros);

    $vParametros = array();
    $vParametros['TIPOESTU'] = "ESTADOCARTERA";
    $mReturnTablaR  = $objEstructurasEstadoCartera->fnCrearEstructurasEstadoCartera($vParametros);

    if($mReturnTablaR[0] == "true" && $mReturnTablaE[0] == "true") {
      //No hace nada
    }else{
      $nSwitch = 1;

      for($nR=1;$nR<count($mReturnTablaR);$nR++){
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= $mReturnTablaR[$nR]."\n";
      }

      for($nR=1;$nR<count($mReturnTablaE);$nR++){
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= $mReturnTablaE[$nR]."\n";
      }
    }
  }

  if ($_SERVER["SERVER_PORT"] != "" && $_POST['cEjProBg'] == "SI" && $nSwitch == 0) {
    $cEjePro = 1;

    $cPost   = "";
    $cTablas = $mReturnTablaR[1]."~".$mReturnTablaE[1];

    $vParBg['pbadbxxx'] = $cAlfa;                              //Base de Datos
    $vParBg['pbamodxx'] = "MODFACTURACION";                    //Modulo
    $vParBg['pbatinxx'] = "ESTADOCARTERA";                     //Liberar Consecutivo Explosión
    $vParBg['pbatinde'] = "REPORTE ESTADO DE CARTERA";         //Descripcion Liberar Consecutivo Explosión
    $vParBg['admidxxx'] = "";                                  //Sucursal
    $vParBg['doiidxxx'] = "";                                  //Do
    $vParBg['doisfidx'] = "";                                  //Sufijo
    $vParBg['cliidxxx'] = "";                                  //Nit
    $vParBg['clinomxx'] = "";                                  //Nombre Importador
    $vParBg['pbapostx'] = $cPost;                              //Parametros para reconstruir Post
    $vParBg['pbatabxx'] = $cTablas;                            //Tabla Temporal (vacia)
    $vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];         //Script
    $vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];             //cookie
    $vParBg['pbacrexx'] = 0;                                   //Cantidad Registros (vacio)
    $vParBg['pbatxixx'] = 1;                                   //Tiempo Ejecucion x Item en Segundos
    $vParBg['pbaopcxx'] = "";                                  //Opciones
    $vParBg['regusrxx'] = $kUser;                              //Usuario que Creo Registro

    // Instanceando la clase de procesos en background
    $ObjProBg     = new cProcesosBackground();
    $mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);

    // Imprimiendo resumen de todo ok.
    if ($mReturnProBg[0] == "true") {
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Proceso para la Generacion del Reporte Creado Con Exito.";
    } else {
      $nSwitch = 1;
      for($nR=1;$nR<count($mReturnProBg);$nR++){
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= $mReturnProBg[$nR]."\n";
      }
    }
  }

  if ($_SERVER["SERVER_PORT"] == "") {
    /**
     * Armando parametros para enviar al utiescar
     */
    $mTablas = explode("~",$xRB['pbatabxx']);

    /**
     * Vectore de tablas temporales
     */
    $mReturnTablaR[1] = $mTablas[0];
    $mReturnTablaE[1] = $mTablas[1];
  }

  if ($cEjePro == 0) {
    if ($nSwitch == 0) {

      /* Enviando Datos recibidos */
      $objEstadoCartera = new cEstadoCartera(); // se instancia la clase cTOE
      $vDatos['FECCORTE'] = date('Y-m-d');     //FECHA DE CORTE
      $vDatos['TABLAXXX'] = $mReturnTablaR[1]; //TABLA PRINCIPAL
      $vDatos['TABLAERR'] = $mReturnTablaE[1]; //TABLA DE ERRORES


      $mReturnReporte = $objEstadoCartera->fnReporteEstadoCartera($vDatos);

      if($mReturnReporte[0] == "true") {
        //Se ejecuto con Exito
      } else {
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnReporte);$nR++){
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= $mReturnReporte[$nR]."\n";
        }
      }
    }
  }

  if ($_SERVER["SERVER_PORT"] == "") {
    /**
     * Se ejecuto por el proceso en background
     * Actualizo el campo de resultado y nombre del archivo
     */
    $vParBg['pbarespr'] = ($nSwitch == 0) ? "EXITOSO" : "FALLIDO";  //Resultado Proceso
    $vParBg['pbaexcxx'] = "";                                       //Nombre Archivos Excel
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
  }

  if ($_SERVER["SERVER_PORT"] != "") {
    if ($nSwitch == 1) {
      f_Mensaje(__FILE__,__LINE__,$cMsj); ?>
      <script languaje = "javascript">
        parent.fmwork.document.forms['frgrm']['nTimesSave'].value    = 0;
        parent.fmwork.document.getElementById('bntProcesar').disabled= false;
      </script>
    <?php }

    if($nSwitch == 0) {
      f_Mensaje(__FILE__,__LINE__,$cMsj); ?>
      <form name = "frgrm" action = "frescini.php" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">document.forms['frgrm'].submit();</script>
    <?php }
  }
?>

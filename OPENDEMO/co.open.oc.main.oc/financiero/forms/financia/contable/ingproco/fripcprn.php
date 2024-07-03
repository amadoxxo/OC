<?php

  /**
   * Genera archivo excel de Ingresos Propios Detallado por Concepto
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors", "1");

  set_time_limit(0);
  ini_set("memory_limit", "512M");

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
    include("../../../../config/config.php");
    include("../../../../libs/php/utility.php");
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
    $gPeriodo = $_POST['gPeriodo'];
    $gMes     = $_POST['gMes'];
    $gDirId   = $_POST['gDirId'];
    $gTerId   = $_POST['gTerId'];
    $gSerId   = $_POST['gSerId'];
    $cTipo    = $_POST['cTipo'];
    $cEjProBg = $_POST['cEjProBg'];
  } 
 
  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
    $cEjePro = 1;
  
    $strPost = "gPeriodo~" . $gPeriodo . 
              "|gMes~" . $gMes . 
              "|gDirId~" . $gDirId . 
              "|gTerId~" . $gTerId . 
              "|gSerId~" . $gSerId . 
              "|cTipo~" . $cTipo . 
              "|cEjProBg~" . $cEjProBg;

    $vParBg['pbadbxxx'] = $cAlfa;                                         	//Base de Datos
    $vParBg['pbamodxx'] = "FACTURACION";                                  	//Modulo
    $vParBg['pbatinxx'] = "IPDETALLADOXCONCEPTO";                           //Tipo Interface
    $vParBg['pbatinde'] = "INGRESOS PROPIOS DETALLADO POR CONCEPTO";        //Descripcion Tipo de Interfaz
    $vParBg['admidxxx'] = "";                                             	//Sucursal
    $vParBg['doiidxxx'] = "";                                             	//Do
    $vParBg['doisfidx'] = "";                                             	//Sufijo
    $vParBg['cliidxxx'] = $gTerId;                                          //Nit
    $vParBg['clinomxx'] = $xDDE['clinomxx'];                                //Nombre Importador
    $vParBg['pbapostx'] = $strPost;																					//Parametros para reconstruir Post
    $vParBg['pbatabxx'] = "";                                             	//Tablas Temporales
    $vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];                    	//Script
    $vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];                        	//cookie
    $vParBg['pbacrexx'] = 0;                                    	          //Cantidad Registros
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

  //###############################################
  //Switch segun el tipo
  if ($cEjePro == 0) {
    //Inicializa cuando es visualizacion por pantalla
    switch ($cTipo) {
      case 1:
        // PINTA POR PANTALLA// ?>
        <html>
          <head>
            <title>Reporte de Ingresos Propios Detallado por Concepto</title>
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
      <?php break;
    }
    //Fin inicio visualizacion por pantalla

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

    //Hago el Select para pintar por Pantalla o en Excel
    $qDatSuc  = "SELECT ";
    $qDatSuc .= "$cAlfa.fpar0008.sucidxxx, ";
    $qDatSuc .= "$cAlfa.fpar0008.ccoidxxx, ";
    $qDatSuc .= "$cAlfa.fpar0008.sucdesxx ";
    $qDatSuc .= "FROM $cAlfa.fpar0008 ";
    $qDatSuc .= "WHERE ";
    $qDatSuc .= "$cAlfa.fpar0008.regestxx = \"ACTIVO\" ";
    $qDatSuc .= "ORDER BY $cAlfa.fpar0008.sucidxxx ";
    $xDatSuc  = f_MySql("SELECT","",$qDatSuc,$xConexion01,"");
    $mDatSuc  = array();
    //echo $qDatSuc."~".mysql_num_rows($xDatSuc);
    while ($xRDS = mysql_fetch_array($xDatSuc)){
      $mDatSuc[$xRDS['sucidxxx']] = $xRDS['sucdesxx'];
    }

    //Periodo
    $cPerAno = $gPeriodo;

    //Fecha Inicial
    $dFecIni = $gPeriodo."-".$gMes."-01";
    $dFecFin = $gPeriodo."-".$gMes."-".date ('d', mktime (0, 0, 0, $gMes + 1, 0, $cPerAno));

    //Posiciones de consulta
    $nPosObs01 = "LOCATE(\"|\",$cAlfa.fcod$cPerAno.comobsxx)";
    $nPosObs02 = "LOCATE(\"~\",$cAlfa.fcod$cPerAno.comobsxx)";
    $nPosObs03 = "LOCATE(\"~\",$cAlfa.fcod$cPerAno.comobsxx,$nPosObs02+1)";

    //Variables de sentencia SQL
    $qSelSuc = "SELECT COUNT(sucidxxx) FROM $cAlfa.fpar0008 WHERE $cAlfa.fpar0008.ccoidxxx = $cAlfa.fcod$cPerAno.ccoidxxx ";
    $qComDes = "SUBSTRING($cAlfa.fcod$cPerAno.comobsxx,($nPosObs03+1),(LENGTH($cAlfa.fcod$cPerAno.comobsxx)-($nPosObs03+1)))";
    $nPosCor = "LOCATE(\"[\",$qComDes)";
    $qComDes = "IF($nPosCor > 0,TRIM(SUBSTRING($qComDes,1,$nPosCor-1)),$qComDes)";

    /**
     * SElect para traer comvlrxx por cliente, centro de costo y sucursal por cliente
     */
    $cNomCli = "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)))";

    $nPos01 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx)";
    $nPos02 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos01+1)";
    $nPos03 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos02+1)";
    $nPos04 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos03+1)";
    $nPos05 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos04+1)";
    $nPos06 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos05+1)";
    $nPos07 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos06+1)";
    $nPos08 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos07+1)";
    $nPos09 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos08+1)";
    $nPos10 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos09+1)";
    $nPos11 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos10+1)";
    $nPos12 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos11+1)";
    $nPos13 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos12+1)";
    $nPos14 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos13+1)";
    $nPos15 = "LOCATE(\"~\",$cAlfa.fcoc$cPerAno.comfpxxx,$nPos14+1)";


   ##Cargo una matriz con todos los PUCID'S par utilizarla despues ##   
   $mPucIds = array();
   $qPucIds  = "SELECT ";
   $qPucIds .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) as pucidxxx,";
   $qPucIds .= "pucdoscc,";
   $qPucIds .= "pucdetxx ";
   $qPucIds .= "FROM $cAlfa.fpar0115 ";
   $qPucIds .= "WHERE ";    
   $qPucIds .= "$cAlfa.fpar0115.regestxx = \"ACTIVO\" ";
   $xPucIds  = f_MySql("SELECT","",$qPucIds,$xConexion01,"");
   while ($xRPI = mysql_fetch_array($xPucIds)){
     $nindice = count($mPucIds);
     $mPucIds[$nindice]['pucidxxx'] = $xRPI['pucidxxx'] ;
     $mPucIds[$nindice]['pucdoscc'] = $xRPI['pucdoscc'] ;
     $mPucIds[$nindice]['pucdetxx'] = $xRPI['pucdetxx'] ;
   }


   ##Cargo una matriz con todos los CCOID'S par utilizarla despues ## 
   $mParConC = array();
   $qParConC  = "SELECT ";
   $qParConC .= "ctoidxxx, ";
   $qParConC .= "ctodocxg, ";
   $qParConC .= "ctodocxl, ";
   $qParConC .= "ctoclaxf ";
   $qParConC .= "FROM $cAlfa.fpar0119 ";
   $qParConC .= "WHERE ";
   $qParConC .= "($cAlfa.fpar0119.ctodocxg = \"SI\" OR ";
   $qParConC .= "$cAlfa.fpar0119.ctodocxl = \"SI\" OR ";
   $qParConC .= "$cAlfa.fpar0119.ctoclaxf != \"\") AND ";
   $qParConC .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\" ";
   $xParConC  = f_MySql("SELECT","",$qParConC,$xConexion01,"");
   while ($xRPC = mysql_fetch_array($xParConC)){
     $nindice = count($mParConC);
     $mParConC[$nindice]['ctoidxxx'] = $xRPC['ctoidxxx'] ;
     $mParConC[$nindice]['ctodocxg'] = $xRPC['ctodocxg'] ;
     $mParConC[$nindice]['ctodocxl'] = $xRPC['ctodocxl'] ;
     $mParConC[$nindice]['ctoclaxf'] = $xRPC['ctoclaxf'] ;
   }

    /**
     * Select para traer comvlr por centro de costo y sucursal
     */
    $qTotMov  = "SELECT ";
    $qTotMov .= "$cAlfa.fcod$cPerAno.comidxxx, ";
    $qTotMov .= "$cAlfa.fcod$cPerAno.comcodxx, ";
    $qTotMov .= "$cAlfa.fcod$cPerAno.comcscxx, ";
    $qTotMov .= "$cAlfa.fcod$cPerAno.comseqcx, ";
    $qTotMov .= "$cAlfa.fcod$cPerAno.docidxxx, ";
    $qTotMov .= "$cAlfa.fcod$cPerAno.docsufxx, ";
    $qTotMov .= "$cAlfa.fcod$cPerAno.ccoidxxx, ";
    $qTotMov .= "$cAlfa.fcod$cPerAno.ctoidxxx, ";
    $qTotMov .= "$cAlfa.fcod$cPerAno.sucidxxx as suciddox, ";
    $qTotMov .= "IF($cAlfa.fcod$cPerAno.commovxx=\"D\",($cAlfa.fcod$cPerAno.comvlrxx * -1),$cAlfa.fcod$cPerAno.comvlrxx) as comvlrxx, ";
    $qTotMov .= "IF(($qSelSuc)= 1, $cAlfa.fpar0008.sucidxxx,SUBSTRING($cAlfa.fcoc$cPerAno.comfpxxx,$nPos15+1,3)) AS sucidxxx, ";
    $qTotMov .= "$cAlfa.fcoc$cPerAno.diridxxx,";
    $qTotMov .= "IF($cAlfa.fpar0117.comtipxx = \"AJUSTES\", $cAlfa.fpar0129.serdespx, ($qComDes)) AS serdesxx,";     
    $qTotMov .= "$cAlfa.fpar0117.comtipxx, ";
    $qTotMov .= "$cAlfa.fcod$cPerAno.comdocfa ";
    $qTotMov .= "FROM $cAlfa.fcod$cPerAno ";
    $qTotMov .= "LEFT JOIN $cAlfa.fcoc$cPerAno ON $cAlfa.fcod$cPerAno.comidxxx = $cAlfa.fcoc$cPerAno.comidxxx AND $cAlfa.fcod$cPerAno.comcodxx = $cAlfa.fcoc$cPerAno.comcodxx AND $cAlfa.fcod$cPerAno.comcscxx = $cAlfa.fcoc$cPerAno.comcscxx AND $cAlfa.fcod$cPerAno.comcsc2x = $cAlfa.fcoc$cPerAno.comcsc2x ";
    $qTotMov .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fpar0008.ccoidxxx = $cAlfa.fcod$cPerAno.ccoidxxx ";
    $qTotMov .= "LEFT JOIN $cAlfa.fpar0117 ON $cAlfa.fcoc$cPerAno.comidxxx = $cAlfa.fpar0117.comidxxx AND $cAlfa.fcoc$cPerAno.comcodxx = $cAlfa.fpar0117.comcodxx ";
    $qTotMov .= "LEFT JOIN $cAlfa.fpar0129 ON $cAlfa.fcod$cPerAno.ctoidxxx = $cAlfa.fpar0129.ctoidxxx ";    
    if($vSysStr['financiero_grupos_contables_reportes_ip'] != ""){
      $qTotMov .= "LEFT JOIN $cAlfa.fpar0115 ON $cAlfa.fcod$cPerAno.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) ";
    }
    $qTotMov .= "WHERE ";
    if($gDirId != "") {
      $qTotMov .= "(($cAlfa.fcoc$cPerAno.comidxxx = \"F\" AND $cAlfa.fcoc$cPerAno.diridxxx = \"$gDirId\" AND ";
    }else{
      $qTotMov .= "(($cAlfa.fcoc$cPerAno.comidxxx = \"F\" AND ";
    }   
    $qTotMov .= "$cAlfa.fcod$cPerAno.comctocx = \"IP\") OR ";
    $qTotMov .= "($cAlfa.fpar0117.comtipxx = \"AJUSTES\" AND $cAlfa.fcod$cPerAno.comdocfa)  OR ";
    $qTotMov .= "(($cAlfa.fcoc$cPerAno.comidxxx = \"C\" OR $cAlfa.fcoc$cPerAno.comidxxx = \"D\") AND $cAlfa.fpar0117.comtipxx != \"AJUSTES\" AND $cAlfa.fcod$cPerAno.comctocx = \"IP\") ) AND ";
    $qTotMov .= "$cAlfa.fcod$cPerAno.comfecxx BETWEEN \"$dFecIni\" AND \"$dFecFin\" ";
    if($gTerId != ""){
      $qTotMov .= "AND $cAlfa.fcod$cPerAno.teridxxx = \"{$gTerId}\" ";
    }
    if($gSerId != ""){
      $qTotMov .= "AND SUBSTRING($cAlfa.fcod$cPerAno.comobsxx,($nPosObs01+1),($nPosObs02-2)) = \"{$gSerId}\" ";
    }
    if($vSysStr['financiero_grupos_contables_reportes_ip'] != ""){
      $qTotMov .= "AND $cAlfa.fpar0115.pucgruxx IN ({$vSysStr['financiero_grupos_contables_reportes_ip']}) ";
    }
    $qTotMov .= "AND $cAlfa.fcod$cPerAno.regestxx = \"ACTIVO\" ";
    $qTotMov .= "GROUP BY $cAlfa.fcod$cPerAno.teridxxx,$cAlfa.fcod$cPerAno.comidxxx,$cAlfa.fcod$cPerAno.comcodxx,$cAlfa.fcod$cPerAno.comcscxx,$cAlfa.fcod$cPerAno.ccoidxxx,sucidxxx,$cAlfa.fcod$cPerAno.ctoidxxx,serdesxx ";

    $xTotMov  = f_MySql("SELECT","",$qTotMov,$xConexion01,"");
    $mTotMov = array();
    //echo $qTotMov."~".mysql_num_rows($xTotMov);
    $nTotal = 0;
    while ($xRTM = mysql_fetch_array($xTotMov)){
      $nAplica   = 0;
      $mDoiId    = array();
      $cDocId    = "";
      $cctodocxg = "";
      $cctodocxl = "";
      $cctoclaxf = "";
      $cpucdoscc = "";
      $cpucdetxx = "";
      $cDirId    = $xRTM['diridxxx'];

      if (($xRTM['comidxxx'] == "C" || $xRTM['comidxxx'] == "D") && $xRTM['sucidxxx'] == "" ){
        $xRTM['sucidxxx'] = $xRTM['suciddox'];
      }
      
      if($gDirId != "") {
        /// Comprobante tipo C ó D 
        if(($xRTM['comidxxx'] == "C" || $xRTM['comidxxx'] == "D") && $xRTM['comtipxx'] != "AJUSTES" ){
          //Busca en matriz $mParConC (comprobantes contables)
          for ($nP=0; $nP < count($mParConC); $nP++) { 
            if ($xRTM['ctoidxxx'] == $mParConC[$nP]['ctoidxxx']){
              $cctodocxg = $mParConC[$nP]['ctodocxg'];
              $cctodocxl = $mParConC[$nP]['ctodocxl'];
              $cctoclaxf = $mParConC[$nP]['ctoclaxf'];
              $nP = count($mParConC);
            }
          }

          //Busca en matriz $mPucIds (Cuentas contables)
          for ($nC=0; $nC < count($mPucIds); $nC++) { 
            if ($xRTM['pucidxxx'] == $mPucIds[$nC]['pucidxxx']){
              $cpucdoscc = $mPucIds[$nC]['pucdoscc'];
              $cpucdetxx = $mPucIds[$nC]['pucdetxx'];
              $nC = count($mPucIds);
            }
          }
          #Do's
          if($xRTM['docidxxx'] != ""){
            if (in_array($xRTM['docidxxx'],$mDoiId) == false) {
              $mDoiId[] = $xRTM['docidxxx'];
              $cDocId = $xRTM['docidxxx'];
            }
          }else{
            if(($cctodocxg == "SI" || $cctodocxl == "SI") && $xRTM['comcscc2'] != ""){
              if (in_array($xRTM['comcscc2'],$mDoiId) == false) {
                $mDoiId[] = $xRTM['comcscc2'];
                $cDocId = $xRTM['comcscc2'];
              }
            }elseif (($cpucdoscc == "S" || $cpucdetxx == "D") && $xRTM['comcsccx'] != ""){
                if (in_array($xRTM['comcsccx'],$mDoiId) == false) {
                  $mDoiId[] = $xRTM['comcsccx'];
                  $cDocId = $xRTM['comcsccx'];
                }
            }else{
              #Do's
              if (in_array($xRTM['sccidxxx'],$mDoiId) == false) {
                $mDoiId[] = $xRTM['sccidxxx'];
              }
            }
          }
          if ($cDocId != "") {
            //Buscando si el DO existe traigo el director de cuenta y la sucursal
            $qDo = "SELECT diridxxx, sucidxxx FROM $cAlfa.sys00121 WHERE $cAlfa.sys00121.docidxxx = \"{$cDocId}\" LIMIT 0,1";
            $xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
            if (mysql_num_rows($xDo) > 0) {
              $xRDO = mysql_fetch_array($xDo);
              $cDirId = $xRDO['diridxxx'];   
            } 
          }
        }

        /// Comprobantes tipo AJUSTE
        if($xRTM['comtipxx'] == "AJUSTES" && $xRTM['comdocfa'] != ""){
          $vDocFa = explode('~',$xRTM['comdocfa']);
          $vCscFa = explode('-',$vDocFa[1]);

          if($vDocFa[0] !=""){
            $qCabMov  = "SELECT diridxxx ";
            $qCabMov .= "FROM $cAlfa.fcoc$vDocFa[0] ";
            $qCabMov .= "WHERE ";
            $qCabMov .= "comidxxx = \"$vCscFa[0]\"  AND ";
            $qCabMov .= "comcodxx = \"$vCscFa[1]\" AND ";
            $qCabMov .= "comcscxx = \"$vCscFa[2]\" LIMIT 0,1";
            $xCabMov  = f_MySql("SELECT","",$qCabMov,$xConexion01,"");
            $vCabMov  = mysql_fetch_array($xCabMov);
            if (mysql_num_rows($xCabMov) == 1) {
              $cDirId = $vCabMov['diridxxx'];
            }  
          } 
        } //if($xRTM['comtipxx'] == "AJUSTES" && $xRTM['comdocfa'] != ""){   

        // Si el código del director es diferente al filtro seleccionado
        if(trim($cDirId) != trim($gDirId)){
          $nAplica = 1;
        }        
      } //if($gDirId != "") {

      if ($nAplica == 0){
        $mTotMov[$xRTM['sucidxxx']]['comvlrxx'] += $xRTM['comvlrxx'];
        $mTotMov[$xRTM['sucidxxx']]['comvlrpo']  = 0;
        $nTotal += $xRTM['comvlrxx'];
      }
    }


    ////Detalle por vendedor
    $qDatMov  = "SELECT ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.teridxxx, ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comidxxx, ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comcodxx, ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comcscxx, ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comseqcx, ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.docidxxx, ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.docsufxx, ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.ccoidxxx, ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.ctoidxxx, ";    
    $qDatMov .= "$cAlfa.fcod$cPerAno.sucidxxx as suciddox, ";
    $qDatMov .= "IF($cAlfa.fcod$cPerAno.commovxx=\"D\",($cAlfa.fcod$cPerAno.comvlrxx * -1),$cAlfa.fcod$cPerAno.comvlrxx) as comvlrxx, ";
    $qDatMov .= "$cAlfa.fcoc$cPerAno.diridxxx,";
    $qDatMov .= "IF(($qSelSuc)= 1, $cAlfa.fpar0008.sucidxxx,SUBSTRING($cAlfa.fcoc$cPerAno.comfpxxx,$nPos15+1,3)) AS sucidxxx, ";
    $qDatMov .= "IF($cAlfa.fpar0117.comtipxx = \"AJUSTES\", $cAlfa.fpar0129.serdespx, ($qComDes)) AS serdesxx,"; 
    $qDatMov .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",IF($cNomCli != \"\",$cNomCli,$cAlfa.SIAI0150.CLINOMXX),\"CLIENTE SIN NOMBRE\") AS clinomxx, ";
    $qDatMov .= "$cAlfa.SIAI0150.CLIVENXX AS clivenxx, ";
    $qDatMov .= "$cAlfa.fpar0117.comtipxx, ";
    $qDatMov .= "$cAlfa.fcod$cPerAno.comdocfa ";
    $qDatMov .= "FROM $cAlfa.fcod$cPerAno ";
    $qDatMov .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$cPerAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
    $qDatMov .= "LEFT JOIN $cAlfa.fcoc$cPerAno ON $cAlfa.fcod$cPerAno.comidxxx = $cAlfa.fcoc$cPerAno.comidxxx AND $cAlfa.fcod$cPerAno.comcodxx = $cAlfa.fcoc$cPerAno.comcodxx AND $cAlfa.fcod$cPerAno.comcscxx = $cAlfa.fcoc$cPerAno.comcscxx AND $cAlfa.fcod$cPerAno.comcsc2x = $cAlfa.fcoc$cPerAno.comcsc2x ";
    $qDatMov .= "LEFT JOIN $cAlfa.fpar0008 ON $cAlfa.fpar0008.ccoidxxx = $cAlfa.fcod$cPerAno.ccoidxxx ";
    $qDatMov .= "LEFT JOIN $cAlfa.fpar0117 ON $cAlfa.fcoc$cPerAno.comidxxx = $cAlfa.fpar0117.comidxxx AND $cAlfa.fcoc$cPerAno.comcodxx = $cAlfa.fpar0117.comcodxx ";
    $qDatMov .= "LEFT JOIN $cAlfa.fpar0129 ON $cAlfa.fcod$cPerAno.ctoidxxx = $cAlfa.fpar0129.ctoidxxx ";    
    if($vSysStr['financiero_grupos_contables_reportes_ip'] != ""){
      $qDatMov .= "LEFT JOIN $cAlfa.fpar0115 ON $cAlfa.fcod$cPerAno.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) ";
    }
    $qDatMov .= "WHERE ";
    if($gDirId != "") {
      $qDatMov .= "(($cAlfa.fcoc$cPerAno.comidxxx = \"F\" AND $cAlfa.fcoc$cPerAno.diridxxx = \"$gDirId\" AND ";
    }else{
      $qDatMov .= "(($cAlfa.fcoc$cPerAno.comidxxx = \"F\" AND ";
    } 
    $qDatMov .= "$cAlfa.fcod$cPerAno.comctocx = \"IP\") OR ";
    $qDatMov .= "($cAlfa.fpar0117.comtipxx = \"AJUSTES\" AND $cAlfa.fcod$cPerAno.comdocfa) OR ";
    $qDatMov .= "(($cAlfa.fcoc$cPerAno.comidxxx = \"C\" OR $cAlfa.fcoc$cPerAno.comidxxx = \"D\") AND $cAlfa.fpar0117.comtipxx != \"AJUSTES\" AND $cAlfa.fcod$cPerAno.comctocx = \"IP\") ) ";
    $qDatMov .= "AND $cAlfa.fcod$cPerAno.comfecxx BETWEEN \"$dFecIni\" AND \"$dFecFin\" ";
    if($gTerId != ""){
      $qDatMov .= "AND $cAlfa.fcod$cPerAno.teridxxx = \"{$gTerId}\" ";
    }
    if($gSerId != ""){
      $qDatMov .= "AND SUBSTRING($cAlfa.fcod$cPerAno.comobsxx,($nPosObs01+1),($nPosObs02-2)) = \"{$gSerId}\" ";
    }
    if($vSysStr['financiero_grupos_contables_reportes_ip'] != ""){
      $qDatMov .= "AND $cAlfa.fpar0115.pucgruxx IN ({$vSysStr['financiero_grupos_contables_reportes_ip']}) ";
    }  
    $qDatMov .= "AND $cAlfa.fcod$cPerAno.regestxx = \"ACTIVO\" ";
    $qDatMov .= "GROUP BY $cAlfa.fcod$cPerAno.teridxxx,$cAlfa.fcod$cPerAno.comidxxx,$cAlfa.fcod$cPerAno.comcodxx,$cAlfa.fcod$cPerAno.comcscxx,$cAlfa.fcod$cPerAno.ccoidxxx,sucidxxx,$cAlfa.fcod$cPerAno.ctoidxxx,serdesxx ";
    $xDatMov  = f_MySql("SELECT","",$qDatMov,$xConexion01,"");
    $mDatMov = array();
    //echo $qDatMov."~".mysql_num_rows($xDatMov);
    
    $mNumDos = array();
    $nTotalDos = 0;
    $nTotqalPor= 0;
    while ($xRDM = mysql_fetch_array($xDatMov)){
      $nAplica    = 0;
      $mDoiId     = array();
      $cDocId     = "";
      $cctodocxg = "";
      $cctodocxl = "";
      $cctoclaxf = "";
      $cpucdoscc = "";
      $cpucdetxx = "";
      $cDirId    = $xRDM['diridxxx'];

      if (($xRDM['comidxxx'] == "C" || $xRDM['comidxxx'] == "D") && $xRDM['sucidxxx'] == "" ){
        $xRDM['sucidxxx'] = $xRDM['suciddox'];
      }

      if($gDirId != "") {
        // Comprobante tipo C ó D
        // Buscar DO y Director
        if(($xRDM['comidxxx'] == "C" || $xRDM['comidxxx'] == "D") && $xRDM['comtipxx'] != "AJUSTES" ){
          //Busca en matriz $mParConC (comprobantes contables)
          for ($nP=0; $nP < count($mParConC); $nP++) { 
            if ($xRDM['ctoidxxx'] == $mParConC[$nP]['ctoidxxx']){
              $cctodocxg = $mParConC[$nP]['ctodocxg'];
              $cctodocxl = $mParConC[$nP]['ctodocxl'];
              $cctoclaxf = $mParConC[$nP]['ctoclaxf'];
              $nP = count($mParConC);
            }
          }

          //Busca en matriz $mPucIds (Cuentas contables)
          for ($nC=0; $nC < count($mPucIds); $nC++) { 
            if ($xRDM['pucidxxx'] == $mPucIds[$nC]['pucidxxx']){
              $cpucdoscc = $mPucIds[$nC]['pucdoscc'];
              $cpucdetxx = $mPucIds[$nC]['pucdetxx'];
              $nC = count($mPucIds);
            }
          }
          #Do's
          if($xRDM['docidxxx'] != ""){
            if (in_array($xRDM['docidxxx'],$mDoiId) == false) {
              $mDoiId[] = $xRDM['docidxxx'];
              $cDocId = $xRDM['docidxxx'];
            }
          }else{
            if(($cctodocxg == "SI" || $cctodocxl == "SI") && $xRDM['comcscc2'] != ""){
              if (in_array($xRDM['comcscc2'],$mDoiId) == false) {
                $mDoiId[] = $xRDM['comcscc2'];
                $cDocId = $xRDM['comcscc2'];
              }
            }elseif (($cpucdoscc == "S" || $cpucdetxx == "D") && $xRDM['comcsccx'] != ""){
                if (in_array($xRDM['comcsccx'],$mDoiId) == false) {
                  $mDoiId[] = $xRDM['comcsccx'];
                  $cDocId = $xRDM['comcsccx'];
                }
            }else{
              #Do's
              if (in_array($xRDM['sccidxxx'],$mDoiId) == false) {
                $mDoiId[] = $xRDM['sccidxxx'];
              }
            }
          }
          if ($cDocId != "") {
            //Buscando si el DO existe traigo el director de cuenta y la sucursal
            $qDo = "SELECT diridxxx, sucidxxx FROM $cAlfa.sys00121 WHERE $cAlfa.sys00121.docidxxx = \"{$cDocId}\" LIMIT 0,1";
            $xDo  = f_MySql("SELECT","",$qDo,$xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$qDo." ~ ".mysql_num_rows($xDo));
            if (mysql_num_rows($xDo) > 0) {
              $xRDO = mysql_fetch_array($xDo);
              $cDirId = $xRDO['diridxxx'];   
            } 
          }

        }

        // Comprobantes tipo AJUSTE
        // Extrae Factura para buscar el director
        if($xRDM['comtipxx'] == "AJUSTES" && $xRDM['comdocfa'] != ""){
          $vDocFa = explode('~',$xRDM['comdocfa']);
          $vCscFa = explode('-',$vDocFa[1]);

          if($vDocFa[0] !=""){
            $qCabMov  = "SELECT diridxxx ";
            $qCabMov .= "FROM $cAlfa.fcoc$vDocFa[0] ";
            $qCabMov .= "WHERE ";
            $qCabMov .= "comidxxx = \"$vCscFa[0]\"  AND ";
            $qCabMov .= "comcodxx = \"$vCscFa[1]\" AND ";
            $qCabMov .= "comcscxx = \"$vCscFa[2]\" LIMIT 0,1";
            $xCabMov  = f_MySql("SELECT","",$qCabMov,$xConexion01,"");
            $vCabMov  = mysql_fetch_array($xCabMov);
            if (mysql_num_rows($xCabMov) == 1) {
              $cDirId = $vCabMov['diridxxx'];
            }  
          }           
        }      
        // Si el código del director es diferente al filtro seleccionado
        if(trim($cDirId) != trim($gDirId)){
          $nAplica = 1;
        }
      }  
    
      if ($nAplica == 0){    
        if($xRDM['comtipxx'] == "AJUSTES"){
          /**
          * Buscando descripcion en la fpar0119
          */
          $qfpar119  = "SELECT ";
          $qfpar119 .= "ctodesxp ";
          $qfpar119 .= "FROM $cAlfa.fpar0119 ";
          $qfpar119 .= "WHERE ";
          $qfpar119 .= "ctoidxxx = \"{$xRDM['ctoidxxx']}\" LIMIT 0,1";
          $xfpar119  = f_MySql("SELECT","",$qfpar119,$xConexion01,"");
          if (mysql_num_rows($xfpar119) >0) {
            $xRpar119 = mysql_fetch_array($xfpar119);
            $xRDM['serdesxx'] = $xRpar119['ctodesxp'];  
          }                   
        }//if($xRDM['comtipxx'] == "AJUSTES"){

        #Buscando Nombre vendedor
        if($xRDM['clivenxx'] != ""){
          $mCliVen = explode("~",$xRDM['clivenxx']);
          $cNomVen = "";
          for($i=0;$i<count($mCliVen);$i++){
            if($mCliVen[$i] != ""){
              $qNomVen  = "SELECT IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clivenxx ";
              $qNomVen .= "FROM $cAlfa.SIAI0150 ";
              $qNomVen .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$mCliVen[$i]}\" LIMIT 0,1 ";
              $xNomVen  = f_MySql("SELECT","",$qNomVen,$xConexion01,"");
              if (mysql_num_rows($xNomVen) > 0) {
                $xRNV = mysql_fetch_array($xNomVen);
                $cNomVen = $xRNV['clivenxx']." [{$mCliVen[$i]}]";
                $i = count($mCliVen);
              }
            }
          }
          if ($cNomVen != "") {
            $xRDM['clivenxx'] = $cNomVen;
          } else {
            $xRDM['clivenxx'] = "VENDEDOR SIN NOMBRE";
          }
        }else{
          $xRDM['clivenxx'] = "SIN ASIGNAR";
        }

        $mDatMov[$xRDM['teridxxx']][$xRDM['serdesxx']]['clivenxx'] = ($xRDM['clivenxx']!="")?$xRDM['clivenxx']:"&nbsp;";
        $mDatMov[$xRDM['teridxxx']][$xRDM['serdesxx']]['clinomxx'] = ($xRDM['clinomxx']!="")?$xRDM['clinomxx']:"&nbsp;";
        $mDatMov[$xRDM['teridxxx']][$xRDM['serdesxx']]['teridxxx'] = ($xRDM['teridxxx']!="")?$xRDM['teridxxx']:"&nbsp;";
        $mDatMov[$xRDM['teridxxx']][$xRDM['serdesxx']]['serdesxx'] = ($xRDM['serdesxx']!="")?$xRDM['serdesxx']:"&nbsp;";

        $mDatMov[$xRDM['teridxxx']][$xRDM['serdesxx']][$xRDM['sucidxxx']]['sucidxxx'] = $xRDM['sucidxxx'];
        $mDatMov[$xRDM['teridxxx']][$xRDM['serdesxx']][$xRDM['sucidxxx']]['comvlrxx'] += ($xRDM['comvlrxx']!="")?$xRDM['comvlrxx']:"0";
        $nPorMov = ($mDatMov[$xRDM['teridxxx']][$xRDM['serdesxx']][$xRDM['sucidxxx']]['comvlrxx']*100)/$mTotMov[$xRDM['sucidxxx']]['comvlrxx'];
        $nPorMov = round($nPorMov * 100) / 100;
        $mDatMov[$xRDM['teridxxx']][$xRDM['serdesxx']][$xRDM['sucidxxx']]['comvlrpo'] = ($nPorMov!="")?$nPorMov:"0";

        //sumando el comvlrxx del cliente
        $mDatMov[$xRDM['teridxxx']][$xRDM['serdesxx']]['comvlrto'] += ($xRDM['comvlrxx']!="")?$xRDM['comvlrxx']:"0";

        $nPorMov = ($mDatMov[$xRDM['teridxxx']][$xRDM['serdesxx']]['comvlrto']*100)/$nTotal;
        $nPorMov = round($nPorMov * 100) / 100;
        $mDatMov[$xRDM['teridxxx']][$xRDM['serdesxx']]['comvlrpo'] = ($nPorMov!="")?$nPorMov:"0";
      }
    }

    if($gTerId==""){
      $nCol = 6;
    }else{
      $nCol = 4;
    }
    //Fin Proceso

    switch ($cTipo) {
      case 1:
        // PINTA POR PANTALLA//
        ?>
          <form name = 'frgrm' action='frinpgrf.php' method="POST">
            <center>
              <table border="1" cellspacing="0" cellpadding="0" width="3200" align=center style="margin-top:5px;margin-left: 5px; margin-right: 5px">
                <tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
                  <?php
                  switch($cAlfa){
                    case "TEADIMPEXX":
                    case "DEADIMPEXX":
                    case "ADIMPEXX":
                    // case "DEGRUPOGLA":
                    // case "TEGRUPOGLA":
                      ?>
                      <td class = "name" style="width: 100px;">
                        <img width="100" height="90" style="left: 15px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoAdimpex.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;

                    case "ROLDANLO"://ROLDAN
                    case "TEROLDANLO"://ROLDAN
                    case "DEROLDANLO"://ROLDAN
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="150" height="100" style="left: 17px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoroldan.png">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "GRUMALCO"://GRUMALCO
                    case "TEGRUMALCO"://GRUMALCO
                    case "DEGRUMALCO"://GRUMALCO
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="150" height="100" style="left: 17px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomalco.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "ALADUANA": //ALADUANA
                    case "TEALADUANA": //ALADUANA
                    case "DEALADUANA": //ALADUANA
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="150" height="100" style="left: 17px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaladuana.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "ANDINOSX": //ANDINOSX
                    case "TEANDINOSX": //ANDINOSX
                    case "DEANDINOSX": //ANDINOSX
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="150" height="60" style="left: 17px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoandinos.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "GRUPOALC": //GRUPOALC
                    case "TEGRUPOALC": //GRUPOALC
                    case "DEGRUPOALC": //GRUPOALC
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="150" height="70" style="left: 17px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoalc.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "AAINTERX": //AAINTERX
                    case "TEAAINTERX": //AAINTERX
                    case "DEAAINTERX": //AAINTERX
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="150" height="100" style="left: 17px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointernacional.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "AALOPEZX":
                    case "TEAALOPEZX":
                    case "DEAALOPEZX":
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="130" style="left: 17px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaalopez.png">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "ADUAMARX": //ADUAMARX
                    case "TEADUAMARX": //ADUAMARX
                    case "DEADUAMARX": //ADUAMARX
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="90" height="90" style="left: 17px;margin-top:2px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaduamar.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "SOLUCION": //SOLUCION
                    case "TESOLUCION": //SOLUCION
                    case "DESOLUCION": //SOLUCION
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="150" style="left: 17px;margin-top:2px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logosoluciones.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "FENIXSAS": //FENIXSAS
                    case "TEFENIXSAS": //FENIXSAS
                    case "DEFENIXSAS": //FENIXSAS
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="150" style="left: 17px;margin-top:2px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logofenix.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
										case "COLVANXX": //COLVANXX
										case "TECOLVANXX": //COLVANXX
										case "DECOLVANXX": //COLVANXX
											?>
											<td class = "name" style="width: 150px;"><center>
												<img width="150" style="left: 17px;margin-top:2px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logocolvan.jpg">
											</td>
											<?php
											$nColRes = 1;
                    break;
										case "INTERLAC": //INTERLAC
										case "TEINTERLAC": //INTERLAC
										case "DEINTERLAC": //INTERLAC
											?>
											<td class = "name" style="width: 150px;"><center>
												<img width="150" style="left: 17px;margin-top:2px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointerlace.jpg">
											</td>
											<?php
											$nColRes = 1;
                    break;
										case "DHLEXPRE": //DHLEXPRE
										case "TEDHLEXPRE": //DHLEXPRE
										case "DEDHLEXPRE": //DHLEXPRE
											?>
											<td class = "name" style="width: 150px;"><center>
												<img width="140" height="90" style="left: 17px;margin-top:1px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logo_dhl_express.jpg">
											</td>
											<?php
											$nColRes = 1;
										break;
                    case "KARGORUX": //KARGORUX
                    case "TEKARGORUX": //KARGORUX
                    case "DEKARGORUX": //KARGORUX
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="130" height="70" style="left: 17px;margin-top:5px;margin-bottom:5px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logokargoru.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "ALOGISAS": //LOGISTICA
                    case "TEALOGISAS": //LOGISTICA
                    case "DEALOGISAS": //LOGISTICA
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="140" style="left: 15px;margin-top:6px;margin-bottom:5px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logologisticasas.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "PROSERCO": //PROSERCO
                    case "TEPROSERCO": //PROSERCO
                    case "DEPROSERCO": //PROSERCO
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="140" style="left: 15px;margin-top:6px;margin-bottom:5px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoproserco.png">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "MANATIAL": //MANATIAL
                    case "TEMANATIAL": //MANATIAL
                    case "DEMANATIAL": //MANATIAL
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="140" style="left: 15px;margin-top:6px;margin-bottom:5px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomanantial.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "DSVSASXX":
                    case "DEDSVSASXX":
                    case "TEDSVSASXX":
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="140" style="left: 15px;margin-top:6px;margin-bottom:5px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logodsv.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "MELYAKXX":    //MELYAK
                    case "DEMELYAKXX":  //MELYAK
                    case "TEMELYAKXX":  //MELYAK
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="140" style="left: 15px;margin-top:6px;margin-bottom:5px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomelyak.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "FEDEXEXP":    //FEDEX
                    case "DEFEDEXEXP":  //FEDEX
                    case "TEFEDEXEXP":  //FEDEX
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="140" style="left: 15px;margin-top:6px;margin-bottom:5px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logofedexexp.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "EXPORCOM":    //EXPORCOMEX
                    case "DEEXPORCOM":  //EXPORCOMEX
                    case "TEEXPORCOM":  //EXPORCOMEX
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="140" style="left: 15px;margin-top:6px;margin-bottom:5px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoexporcomex.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "HAYDEARX":   //HAYDEARX
                    case "DEHAYDEARX": //HAYDEARX
                    case "TEHAYDEARX": //HAYDEARX
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="180" height="70" style="left: 15px;margin-top:6px;margin-bottom:5px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logohaydear.jpeg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "CONNECTA":   //CONNECTA
                    case "DECONNECTA": //CONNECTA
                    case "TECONNECTA": //CONNECTA
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="120" height="80" style="left: 15px;margin-top:6px;margin-bottom:5px;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoconnecta.jpg">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    case "OPENEBCO":   //OPENEBCO
                    case "DEOPENEBCO": //OPENEBCO
                    case "TEOPENEBCO": //OPENEBCO
                      ?>
                      <td class = "name" style="width: 150px;"><center>
                        <img width="200" height="80" style="left: 15px;margin-top:6px;margin-bottom:5px;" src = "<?php echo $cPlesk_Skin_Directory ?>/opentecnologia.JPG">
                      </td>
                      <?php
                      $nColRes = 1;
                    break;
                    default:
                      $nColRes = 0;
                    break;
                  }
                  ?>
                  <td class="name" colspan="<?php echo (count($mDatSuc)*2)+$nCol-$nColRes ?>" align="left">
                    <font size="3">
                      <b>REPORTE DE INGRESOS PROPIOS DETALLADO POR CONCEPTO<br>
                        PERIODO: <?php echo " ".$gPeriodo."-".$gMes ?>
                        <?php if($gTerId != ""){
                          //Busco en la base de datos el nombre del cliente
                          $qDatExt  = "SELECT IF($cAlfa.SIAI0150.CLINOMXX != \"\",IF($cNomCli != \"\",$cNomCli,$cAlfa.SIAI0150.CLINOMXX),\"CLIENTE SIN NOMBRE\") AS clinomxx ";
                          $qDatExt .= "FROM $cAlfa.SIAI0150 ";
                          $qDatExt .= "WHERE ";
                          $qDatExt .= "CLIIDXXX = \"$gTerId\" LIMIT 0,1";
                          $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
                          $xRDE = mysql_fetch_array($xDatExt);
                          //f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
                        ?>
                        <br>CLIENTE: <?php echo "[".$gTerId."] ".$xRDE['clinomxx'] ?>
                        <?php } ?>
                        <?php if($gSerId != ""){
                          //Busco el nombre del concepto contable

                          $qConCob  = "SELECT * ";
                          $qConCob .= "FROM $cAlfa.fpar0129 ";
                          $qConCob .= "WHERE ";
                          $qConCob .= "seridxxx = \"$gSerId\" AND ";
                          $qConCob .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                          $xConCob = f_MySql("SELECT","",$qConCob,$xConexion01,"");
                          $xRCC = mysql_fetch_array($xConCob);
                          //f_Mensaje(__FILE__,__LINE__,$qConCob." ~ ".mysql_num_rows($xConCob));
                        ?>
                        <br>CONCEPTO: <?php echo "[".$gSerId."] ".$xRCC['serdesxx'] ?>
                        <?php } ?>

                        <?php if($gDirId != ""){
                          //Busco en la base de datos el nombre del director
                          $qSqlUsr  = "SELECT USRNOMXX ";
                          $qSqlUsr .= "FROM $cAlfa.SIAI0003 ";
                          $qSqlUsr .= "WHERE ";
                          $qSqlUsr .= "USRIDXXX = \"$gDirId\" AND ";
                          $qSqlUsr .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
                          $xSqlUsr = f_MySql("SELECT","",$qSqlUsr,$xConexion01,"");
                          $xRU = mysql_fetch_array($xSqlUsr);
                        ?>
                        <br>DIRECTOR: <?php echo "[".$gDirId."] ".$xRU['USRNOMXX'] ?>
                        <?php } ?>
                        </b>
                      </font>
                    </td>
                </tr>
              </table>
              <table border="1" cellspacing="0" cellpadding="0" width="3200" align=center style="margin-left:5px; margin-right: 5px">
                <tr height="20">
                  <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>VENDEDOR</font></b></td>
                  <?php if($gTerId==""){ ?>
                    <td style="background-color:#0B610B" class="letra8" width="100px" align="center"><b><font color=white>NIT</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>CLIENTE</font></b></td>
                  <?php } ?>
                    <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>CONCEPTO</font></b></td>
                  <?php  foreach ($mDatSuc as $cKey => $cValue) { ?>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white><?php echo $cValue ?></font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>%</font></b></td>
                  <?php } ?>
                  <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>TOTAL</font></b></td>
                  <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>%</font></b></td>
                </tr>
                  <?php
                  foreach ($mDatMov as $cKey => $cValue) {
                    foreach ($cValue as $cKey2 => $cValue2) {
                      $zColorPro = "#000000";
                      $cColor = "#FFFFFF";
                      ?>
                      <tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">
                        <td class="letra7" align="left" style = "color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2]['clivenxx'] ?></td>
                        <?php if($gTerId==""){ ?>
                          <td class="letra7" align="left" style = "color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2]['teridxxx'] ?></td>
                          <td class="letra7" align="left" style = "color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2]['clinomxx'] ?></td>
                        <?php } ?>
                          <td class="letra7" align="left" style = "color:<?php echo $zColorPro ?>"><?php echo $mDatMov[$cKey][$cKey2]['serdesxx'] ?></td>
                        <?php  foreach ($mDatSuc as $cKeySuc => $cValueSuc) {
                          if($cColor == "#FFFFFF"){
                            $cColor = "#F2F2F2";
                          }else{
                            $cColor = "#FFFFFF";
                          }
                        ?>
                          <td class="letra7" align="right" style = "background-color:<?php echo $cColor ?>;color:<?php echo $zColorPro ?>"><?php  echo (number_format($mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrxx'],0,',','.') != "")?number_format($mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrxx'],0,',','.'):"&nbsp;" ?></td>
                          <td class="letra7" align="right" style = "background-color:<?php echo $cColor ?>;color:<?php echo $zColorPro ?>"><?php  echo (number_format($mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrpo'],2,',','.') != "")?number_format($mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrpo'],2,',','.'):"&nbsp;" ?></td>
                        <?php
                          $mTotMov[$cKeySuc]['comvlrpo'] += $mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrpo'];
                        } ?>
                        <td class="letra7" align="right"  style = "background-color:#E3F6CE;color:<?php echo $zColorPro ?>"><?php echo (number_format($mDatMov[$cKey][$cKey2]['comvlrto'],0,',','.') != "")?number_format($mDatMov[$cKey][$cKey2]['comvlrto'],0,',','.'):"&nbsp;" ?></td>
                        <td class="letra7" align="right"  style = "background-color:#E3F6CE;color:<?php echo $zColorPro ?>"><?php echo (number_format($mDatMov[$cKey][$cKey2]['comvlrpo'],2,',','.') != "")?number_format($mDatMov[$cKey][$cKey2]['comvlrpo'],2,',','.'):"&nbsp;" ?></td>
                      </tr>
                    <?php
                    $nTotqalPor += $mDatMov[$cKey][$cKey2]['comvlrpo'];
                    }
                  } ?>
                  <tr style="background-color:#0B610B" height="20" style="padding-left:4px;padding-right:4px">
                    <td class="letra8" align="right" colspan="<?php echo ($nCol-2) ?>"><b><font color=white>TOTALES</font></b></td>
                    <?php  foreach ($mDatSuc as $cKey => $cValue) { ?>
                      <td class="letra8" align="right" width="100px"><b><font color=white><?php echo (number_format($mTotMov[$cKey]['comvlrxx'],0,',','.') != "")?number_format($mTotMov[$cKey]['comvlrxx'],0,',','.'):0 ?></font></b></td>
                      <td class="letra8" align="right" width="100px"><b><font color=white><?php echo (number_format(round($mTotMov[$cKey]['comvlrpo']),2,',','.') != "")?number_format(round($mTotMov[$cKey]['comvlrpo']),2,',','.'):0 ?></font></b></td>
                    <?php
                  } ?>
                    <td class="letra8" align="right" width="100px"><b><font color=white><?php echo (number_format($nTotal,0,',','.') != "")?number_format($nTotal,0,',','.'):0 ?></font></b></td>
                    <td class="letra8" align="right" width="100px"><b><font color=white><?php echo (number_format(round($nTotqalPor),2,',','.') != "")?number_format(round($nTotqalPor),2,',','.'):0 ?></font></b></td>
                  </tr>
                </table>
              </center>
            </form>
            <script type="text/javascript">document.getElementById('loading').style.display="none";</script>
          </body>
        </html>
        <?php
      break;
      case 2:
        
        $cNomFile = "REPORTE_DE_INGRESOS_PROPIOS_DETALLADO_POR_CONCEPTO_" . $kUser . "_" . date("YmdHis") . ".xls";

        if ($_SERVER["SERVER_PORT"] != "") {
          $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
        } else {
          $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
        }

        if (file_exists($cFile)) {
          unlink($cFile);
        }

        $fOp = fopen($cFile, 'a');

        // PINTA POR EXCEL //
        $data = '';
        $nNumCol = (count($mDatSuc)*2)+$nCol;
        $nNumCol01 = ($nCol-2);
        $data .= '<table border="1" cellspacing="0" cellpadding="0" width="3500" align=center style="margin:5px">';
          $data .= '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
            $data .= '<td class="name" colspan="'.$nNumCol.'" align="left">';
              $data .= '<font size="3">';
                $data .= '<b>REPORTE DE INGRESOS PROPIOS DETALLADO POR CONCEPTO<BR>';
                  $data .= 'PERIODO: '." ".$gPeriodo."-".$gMes.'<br>';
                  if($gTerId != ""){
                    //Busco en la base de datos el nombre del cliente
                    $qDatExt  = "SELECT IF($cAlfa.SIAI0150.CLINOMXX != \"\",IF($cNomCli != \"\",$cNomCli,$cAlfa.SIAI0150.CLINOMXX),\"CLIENTE SIN NOMBRE\") AS clinomxx ";
                    $qDatExt .= "FROM $cAlfa.SIAI0150 ";
                    $qDatExt .= "WHERE ";
                    $qDatExt .= "CLIIDXXX = \"$gTerId\" LIMIT 0,1";
                    $xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
                    $xRDE = mysql_fetch_array($xDatExt);
                    //f_Mensaje(__FILE__,__LINE__,$qDatExt." ~ ".mysql_num_rows($xDatExt));
                    $data .= 'CLIENTE: '."[".$gTerId."] ".$xRDE['clinomxx'].'<br>';
                  }
                  if($gSerId != ""){
                    //Busco el nombre del concepto contable
                    $qConCob  = "SELECT * ";
                    $qConCob .= "FROM $cAlfa.fpar0129 ";
                    $qConCob .= "WHERE ";
                    $qConCob .= "seridxxx = \"$gSerId\" AND ";
                    $qConCob .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                    $xConCob = f_MySql("SELECT","",$qConCob,$xConexion01,"");
                    $xRCC = mysql_fetch_array($xConCob);
                    //f_Mensaje(__FILE__,__LINE__,$qConCob." ~ ".mysql_num_rows($xConCob));
                    $data .= 'CONCEPTO: '."[".$gSerId."] ".$xRCC['serdesxx'].'<br>';
                  }
                  if($gDirId != ""){
                    //Busco en la base de datos el nombre del director
                    $qSqlUsr  = "SELECT USRNOMXX ";
                    $qSqlUsr .= "FROM $cAlfa.SIAI0003 ";
                    $qSqlUsr .= "WHERE ";
                    $qSqlUsr .= "USRIDXXX = \"$gDirId\" AND ";
                    $qSqlUsr .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
                    $xSqlUsr = f_MySql("SELECT","",$qSqlUsr,$xConexion01,"");
                    $xRU = mysql_fetch_array($xSqlUsr);
                    $data .= 'DIRECTOR: '."[".$gDirId."] ".$xRU['USRNOMXX'].'<br>';
                  }
                $data .= '</b>';
              $data .= '</font>';
            $data .= '</td>';
          $data .= '</tr>';
          $data .= '<tr height="20">';
          $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>VENDEDOR</font></b></td>';
            if($gTerId==""){
              $data .= '<td style="background-color:#0B610B" class="letra8" width="100px" align="center"><b><font color=white>NIT</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>CLIENTE</font></b></td>';
            }
            $data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>CONCEPTO</font></b></td>';
            foreach ($mDatSuc as $cKey => $cValue) {
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>'.$cValue.'</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>%</font></b></td>';
            }
            $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>TOTAL</font></b></td>';
            $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>%</font></b></td>';
          $data .= '</tr>';
          foreach ($mDatMov as $cKey => $cValue) {
            foreach ($cValue as $cKey2 => $cValue2) {
              $zColorPro = "#000000";
              $cColor = "#FFFFFF";
              $data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
              $data .= '<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2]['clivenxx'].'</td>';
              if($gTerId==""){
                $data .= '<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2]['teridxxx'].'</td>';
                $data .= '<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2]['clinomxx'].'</td>';
              }
              $data .= '<td class="letra7" align="left" style = "color:'.$zColorPro.'">'.$mDatMov[$cKey][$cKey2]['serdesxx'].'</td>';
              foreach ($mDatSuc as $cKeySuc => $cValueSuc) {
                if($cColor == "#FFFFFF"){
                $cColor = "#F2F2F2";
                }else{
                $cColor = "#FFFFFF";
                }
                $nValor01 = (number_format($mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrxx'],0,',','') != "")?number_format($mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrxx'],0,',',''):"&nbsp;";
                $nValor02 = (number_format($mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrpo'],2,',','') != "")?number_format($mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrpo'],2,',',''):"&nbsp;";
                $data .= '<td class="letra7" align="right" style = "background-color:'.$cColor.';color:'.$zColorPro.'">'.$nValor01.'</td>';
                $data .= '<td class="letra7" align="right" style = "background-color:'.$cColor.';color:'.$zColorPro.'">'.$nValor02.'</td>';
                $mTotMov[$cKeySuc]['comvlrpo'] += $mDatMov[$cKey][$cKey2][$cKeySuc]['comvlrpo'];
              }
              $nValor01 = (number_format($mDatMov[$cKey][$cKey2]['comvlrto'],0,',','') != "")?number_format($mDatMov[$cKey][$cKey2]['comvlrto'],0,',',''):"&nbsp;";
              $nValor02 = (number_format($mDatMov[$cKey][$cKey2]['comvlrpo'],2,',','') != "")?number_format($mDatMov[$cKey][$cKey2]['comvlrpo'],2,',',''):"&nbsp;";
              $data .= '<td class="letra7" align="right"  style = "background-color:#E3F6CE;color:'.$zColorPro.'">'.$nValor01.'</td>';
              $data .= '<td class="letra7" align="right"  style = "background-color:#E3F6CE;color:'.$zColorPro.'">'.$nValor02.'</td>';
              $data .= '</tr>';
              $nTotqalPor += $mDatMov[$cKey][$cKey2]['comvlrpo'];
            }
          }
          $data .= '<tr style="background-color:#0B610B" height="20" style="padding-left:4px;padding-right:4px">';
            $data .= '<td class="letra8" align="right" colspan="'.$nNumCol01.'"><b><font color=white>TOTALES</font></b></td>';
            foreach ($mDatSuc as $cKey => $cValue) {
              $nValor01 = (number_format($mTotMov[$cKey]['comvlrxx'],0,',','') != "")?number_format($mTotMov[$cKey]['comvlrxx'],0,',',''):0;
              $nValor02 = (number_format(round($mTotMov[$cKey]['comvlrpo']),2,',','') != "")?number_format(round($mTotMov[$cKey]['comvlrpo']),2,',',''):0;
              $data .= '<td class="letra8" align="right" width="100px"><b><font color=white>'.$nValor01.'</font></b></td>';
              $data .= '<td class="letra8" align="right" width="100px"><b><font color=white>'.$nValor02.'</font></b></td>';
            }
            $nValor01 = (number_format($nTotal,0,',','') != "")?number_format($nTotal,0,',',''):0;
            $nValor02 = (number_format(round($nTotqalPor),2,',','') != "")?number_format(round($nTotqalPor),2,',',''):0;
            $data .= '<td class="letra8" align="right" width="100px"><b><font color=white>'.$nValor01.'</font></b></td>';
            $data .= '<td class="letra8" align="right" width="100px"><b><font color=white>'.$nValor02.'</font></b></td>';
          $data .= '</tr>';
        $data .= '</table>';

        if ($data == "") {
          $data = "\n(0) REGISTROS!\n";
        }

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
    }
  }
//Fin Switch

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
} 
?>

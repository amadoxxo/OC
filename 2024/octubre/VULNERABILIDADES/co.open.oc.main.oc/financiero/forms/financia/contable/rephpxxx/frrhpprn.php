<?php
  /**
   * Genera archivo excel de Reporte HP Colombia
   * @package opencomex
   * @author Juan Jose Trujillo <juan.trujillo@open-eb.co>
	 * @version 001
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors", "1");

  ini_set("memory_limit","512M");
  set_time_limit(0);

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
      include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utirephp.php");
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
    include("../../../../libs/php/utirephp.php");
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

    if($gCliId != ""){
			#Busco el nombre del cliente
			$qCliNom  = "SELECT ";
			$qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
			$qCliNom .= "FROM $cAlfa.SIAI0150 ";
			$qCliNom .= "WHERE ";
			$qCliNom .= "CLIIDXXX = \"{$gCliId}\" LIMIT 0,1";
			$xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
			if (mysql_num_rows($xCliNom) > 0) {
				$xDDE = mysql_fetch_array($xCliNom);
			} else {
				$xDDE['clinomxx'] = "CLIENTE SIN NOMBRE";
			}
		}
  } // fin if ($_SERVER["SERVER_PORT"] != "")

  $nSwitch = 0;
  $cMsj = "";

  if ($_SERVER["SERVER_PORT"] == "") {
    $gCliId   = $_POST['gCliId'];
    $gComId   = $_POST['gComId'];
		$gComCod  = $_POST['gComCod'];
    $gComCsc  = $_POST['gComCsc'];
		$gComCsc2 = $_POST['gComCsc2'];
		$gFecIni  = $_POST['gFecIni'];
    $gFecFin  = $_POST['gFecFin'];
    $gPerAno  = $_POST['gPerAno'];
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

  /**
	 * Validaciones
	 */
	//Validando que seleccionen el Importador
	if($gCliId == ""){
		$nSwitch = 1;
    $cMsj .= "Debe Seleccionar el Importador.\n";
  }
  
  //Validando que exista el importador
	if($gCliId != ""){
		$qDatImp  = "SELECT CLIIDXXX, ";
		$qDatImp .= "IF(CLINOMCX != \"\",CLINOMCX,IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))) AS CLINOMXX ";
		$qDatImp .= "FROM $cAlfa.SIAI0150 ";
		$qDatImp .= "WHERE ";
		$qDatImp .= "CLIIDXXX = \"{$gCliId}\" AND ";
		$qDatImp .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
		$xDatImp  = f_MySql("SELECT","",$qDatImp,$xConexion01,"");
		$vDatImp = mysql_fetch_array($xDatImp);

		if (mysql_num_rows($xDatImp) == 0) {
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "No Existe el Importador[{$gCliId}] en la Base de Datos. ";
		} 
  }

  if ($gComCsc == ""){
    //Valido que Digiten el Rango de Fecha Inicial
    if($gFecIni == "" || $gFecIni == "0000-00-00"){
      $nSwitch = 1;
      $cMsj .= "Debe Ingresar el Rango de Fecha Inicial.\n";
    }

    //Valido que Digiten el Rango de Fecha Final
    if($gFecFin == "" || $gFecFin == "0000-00-00"){
      $nSwitch = 1;
      $cMsj .= "Debe Ingresar el Rango de Fecha Final.\n";
    }

    //Valido que la Fecha Hasta no sea menor a la Fecha Desde
    if($gFecIni != "0000-00-00" && $gFecFin != "0000-00-00"){
      $date1  = strtotime($gFecIni);
      $date2  = strtotime($gFecFin);
      $diff   = $date2 - $date1;
      $meses  = (string) round($diff / (60 * 60 * 24 * 30.5));

      if($meses > 6){
        $nSwitch = 1;
        $cMsj .= "El Rango de Fechas debe ser Menor a Seis Meses.\n";
      }
    }
  }
  //Fin Validaciones

  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;

    $strPost = "gCliId~"  . $gCliId . 
              "|gComId~"  . $gComId . 
              "|gComCod~" . $gComCod .
              "|gComCsc~" . $gComCsc .
              "|gComCsc2~". $gComCsc2 .
              "|gFecIni~" . $gFecIni . 
              "|gFecFin~" . $gFecFin .
              "|gPerAno~" . $gPerAno;

		$vParBg['pbadbxxx'] = $cAlfa;                           // Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                    // Modulo
		$vParBg['pbatinxx'] = "REPORTEHPCOLOMBIA";          		// Tipo Interface
		$vParBg['pbatinde'] = "REPORTE HP COLOMBIA";         		// Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                               // Sucursal
		$vParBg['doiidxxx'] = "";                               // Do
		$vParBg['doisfidx'] = "";                               // Sufijo
		$vParBg['cliidxxx'] = $gCliId;                          // Nit
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
			f_Mensaje(__FILE__, __LINE__, $cMsj);
		}
	} // fin del if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0)

  //CONSULTAS
  if ($cEjePro == 0) {
    if($nSwitch == 0){

      //Ano desde - hasta
      $nAnioD = ($gComCsc == "") ? $gFecIni : $gPerAno;
      $nAnioH = ($gComCsc == "") ? $gFecFin : $gPerAno;

      $vFiltros = array();
      $vFiltros['teridxxx'] = $gCliId;
      $vFiltros['comidxxx'] = $gComId;
      $vFiltros['comcodxx'] = $gComCod;
      $vFiltros['comcscxx'] = $gComCsc;
      $vFiltros['comcsc2x'] = $gComCsc2;
      $vFiltros['regfcini'] = $nAnioD;
      $vFiltros['regfcfin'] = $nAnioH;

      $oReportesHP = new cFacturacionHP();
      $vReturnHP   = $oReportesHP->fnReportesFacturacionHP($vFiltros);

      // echo "<pre>";
      // print_r($vReturnHP);
      // die();

      if($vReturnHP[0] == "true"){
        if (file_exists($vReturnHP[1]['ruta'])) {
          // Obtener la ruta absoluta del archivo
          $cAbsolutePath = realpath($vReturnHP[1]['ruta']);
          $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

          if ($cData == "") {
            $cData = "\n(0) REGISTROS!\n";
          }
  
          if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
            chmod($vReturnHP[1]['ruta'], intval($vSysStr['system_permisos_archivos'], 8));
            $cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($vReturnHP[1]['ruta']);
    
            if ($_SERVER["SERVER_PORT"] != "") {
    
              header('Content-Description: File Transfer');
              header('Content-Type: application/octet-stream');
              header('Content-Disposition: attachment; filename=' . $cDownLoadFilename);
              header('Content-Transfer-Encoding: binary');
              header('Expires: 0');
              header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
              header('Pragma: public');
              header('Content-Length: ' . filesize($vReturnHP[1]['ruta']));
              
              ob_clean();
              flush();
              readfile($vReturnHP[1]['ruta']);
            }else{
              $cNomArc = $vReturnHP[1]['archivo'];
              echo "\n".$cNomArc;
            }
          }
        }else {
          $nSwitch = 1;
          if ($_SERVER["SERVER_PORT"] != "") {
            f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo {$vReturnHP[1]['ruta']}, Favor Comunicar este Error a openTecnologia S.A.");
          } else {
            $cMsj .= "No se encontro el archivo {$vReturnHP[1]['ruta']}, Favor Comunicar este Error a openTecnologia S.A.";
          }
        }

        // echo "<html><script>document.location='{$vReturnHP[1]['ruta']}';</script></html>";
      }else if($vReturnHP[0] == "false"){
        $nSwitch = 1;
        for($nCM = 1; $nCM < count($vReturnHP); $nCM++){
          $cMsj .= $vReturnHP[$nCM]."\n";
        }
        f_Mensaje(__FILE__,__LINE__,$cMsj."\nVerifique.");
      }
     
    } else {
      f_Mensaje(__FILE__,__LINE__,$cMsj."\nVerifique.");
    }
  } // if cEjePro 

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
  
  function f_Fecha_Letras($xFecha, $meses = 'SI'){
		if ($xFecha==''){
			$xFecfor='';
		}else{
			$fano = substr ($xFecha, 0, 4);
			$fdia = substr ($xFecha, 8, 2);
			$fmes_antes = substr ($xFecha, 5, 2);
			if($fmes_antes=='01')
			$fmes="Enero";
			if($fmes_antes=='02')
			$fmes="Febrero";
			if($fmes_antes=='03')
			$fmes="Marzo";
			if($fmes_antes=='04')
			$fmes="Abril";
			if($fmes_antes=='05')
			$fmes="Mayo";
			if($fmes_antes=='06')
			$fmes="Junio";
			if($fmes_antes=='07')
			$fmes="Julio";
			if($fmes_antes=='08')
			$fmes="Agosto";
			if($fmes_antes=='09')
			$fmes="Septiembre";
			if($fmes_antes=='10')
			$fmes="Octubre";
			if($fmes_antes=='11')
			$fmes="Noviembre";
			if($fmes_antes=='12')
      $fmes="Diciembre";
      
      if($meses == "SI"){
        $xFecFor = $fmes;
      } else {
			  $xFecFor = $fmes." ".$fdia." de ".$fano;
      }
		}
		return ($xFecFor);
	}
?>

<?php
	//Estableciendo que el tiempo de ejecucion no se limite
	set_time_limit (0);

	/**
	* Graba Creacion Cargar Facturas Provisionales desde un txt delimitado por tabulaciones.
	* --- Descripcion: Permite Subir y Creacion Cargar Facturas Provisionales desde un txt delimitado por tabulaciones.
	* @author Camilo Dulce <camilo.dulce@open-eb.co>
	* @version 001
	*/
  
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

      include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utility.php");
      include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
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
   * Incluir los utilitys cuando viene por el navegador
   */
  if ($_SERVER["SERVER_PORT"] != "") {
    include("../../../../libs/php/utility.php");
    include("../../../../../config/config.php");
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

	$cSystemPath= OC_DOCUMENTROOT;

	$nSwitch  = 0;   // Switch para Vericar la Validacion de Datos
	$nError   = 0;   // Errores para las actualziaciones
	$cMsj     = "\n";

  $nCanIns = 0;
  $nCanAct = 0;

	#Numero de registros por recorrido
	$nNumReg = 2000;

	#Cadenas para reemplazar caracteres espciales
	$vBuscar = array(chr(13),chr(10),chr(27),chr(9));
	$vReempl = array(" "," "," "," ");

	$cBuscar01 = array('"',chr(13),chr(10),chr(27),chr(9));
  $cReempl01 = array('\"'," "," "," "," ");

  $cBuscarEmail = array(" ",'"',chr(13),chr(10),chr(27),chr(9));
  $cReemplEmail = array("",'\"',"","","","");

  /**
   * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales
   * @var Number
   */
  $cEjePro = 0;

  /**
   * Nombre(s) de los archivos en excel generados
   */
  $cNomArc = "";

  $kModo = ($_POST['kModo'] != "" ? $_POST['kModo'] : $_COOKIE['kModo']);
  
	switch ($kModo) {
    case "SUBIR":
      if ($_SERVER["SERVER_PORT"] != "") {
        ## Validando que haya seleccionado un archivo
        if ($_FILES['cArcPla']['name'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Debe Digitar un Archivo.\n";
        } else {
          #Copiando el archivo a la carpeta de downloads
          $cNomFile = "/CargueFacturasProvisionales_".$kUser."_".date("YmdHis").".txt";
          switch (PHP_OS) {
            case "Linux" :
              $cFile = "$cSystemPath/opencomex/".$vSysStr['system_download_directory'].$cNomFile;
              break;
            case "WINNT":
              $cFile = "$cSystemPath/opencomex/".$vSysStr['system_download_directory'].$cNomFile;
              break;
          }

          if(!copy($_FILES['cArcPla']['tmp_name'],$cFile)){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al Copiar Archivo.\n";
          }
        }

        #Verificando columnas de la tabla
        if ($nSwitch == 0) {
          $mGestor = fopen($cFile,'r');
          $nCanCol  = 0;
          while (!feof($mGestor)) {
            $cDatos = fgets($mGestor,10000);
            $mBuffer = explode("\t",$cDatos);
            $nCanCol = (count($mBuffer) > $nCanCol) ? count($mBuffer) : $nCanCol;
            break;
          }

          $nTotCol = 4;
          if ($nCanCol != $nTotCol) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "La Cantidad de Columnas es Diferente a $nTotCol.\n";
          }
        }

        #Creando tabla temporal
        if ($nSwitch == 0) {
          $mReturnCrearTabla = fnCrearTablaTem();
          if($mReturnCrearTabla[0] == "false"){
            $nSwitch = 1;
            for($nD = 1; $nD < count($mReturnCrearTabla); $nD++){
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "$mReturnCrearTabla[$nD].\n";
            }
          }else{
            $cTabCar = $mReturnCrearTabla[1];
          }
        }
        #Fin Creando tabla temporal

        #Cargando Archivo a tabla temporal
        if ($nSwitch == 0) {
          $xDescTabla = mysql_query("DESCRIBE $cAlfa.$cTabCar",$xConexion01);

          $vFieldsExcluidos = array();

          while ($xRD = mysql_fetch_array($xDescTabla)) {
            if (!in_array($xRD['Field'],$vFieldsExcluidos)) {
              $vFields[count($vFields)] = $xRD['Field'];
            }
          }
          array_shift($vFields); $cFields = implode(",",$vFields);

          $qLoad  = "LOAD DATA LOCAL INFILE '$cFile' INTO TABLE $cAlfa.$cTabCar ";
          $qLoad .= "FIELDS TERMINATED BY '\\t' LINES TERMINATED BY '\\n' ";
          $qLoad .= "IGNORE 1 LINES ";
          $qLoad .= "($cFields) ";
          $xLoad = mysql_query($qLoad,$xConexion01);
          // echo $qLoad;
          if(!$xLoad) {
            //die(mysql_error());
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al Cargar los Datos ".mysql_errno($xConexion01)." - ".mysql_error($xLoad);
          }
        }
        #Fin Cargando Archivo a tabla temporal
      }

      /**
       * Cuando selecciono el check de ejecutar proceso en background
       * Inserto LOCK en la tabla de agendamiento procesos en background el registro 
       */
      if ($_SERVER["SERVER_PORT"] != "" && $_POST['cEjProBg'] == "SI" && $nSwitch == 0) {
        $cEjePro = 1;
        
        /**
         * Trayendo cantidad de registros de la interface
         * @var Number
         */
        $qItems  = "SELECT * "; 
        $qItems .= "FROM $cAlfa.$cTabCar ";  
        $xItems  = f_MySql("SELECT","",$qItems,$xConexion01,"");
        $nCanReg = mysql_num_rows($xItems);
        
        $cTablas = $cTabCar;
        $cPost   = "kModo~".$kModo."|";
        
        $vParBg['pbadbxxx'] = $cAlfa;                                   //Base de Datos
        $vParBg['pbamodxx'] = "FACTURACION";                            //Modulo
        $vParBg['pbatinxx'] = "CARGARFACTURASPROVISIONALES";            //Tipo Interface
        $vParBg['pbatinde'] = "CARGA DE LEGALIZACIoN MASIVA DE FACTURAS PROVISIONALES"; //Descripcion Tipo de Interfaz
        $vParBg['admidxxx'] = "";                                       //Sucursal
        $vParBg['doiidxxx'] = "";                                       //Do
        $vParBg['doisfidx'] = "";                                       //Sufijo
        $vParBg['cliidxxx'] = "";                                       //Nit
        $vParBg['clinomxx'] = "";                                       //Nombre Importador
        $vParBg['pbapostx'] = $cPost;                                   //Parametros para reconstruir Post
        $vParBg['pbatabxx'] = $cTablas;                                 //Tablas Temporales
        $vParBg['pbascrxx'] = str_replace("frfaccfg.php", "frfacpro.php", $_SERVER['SCRIPT_FILENAME']); //Script
        $vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];                  //cookie
        $vParBg['pbacrexx'] = $nCanReg;                                 //Cantidad Registros
        $vParBg['pbatxixx'] = 1;                                        //Tiempo Ejecucion x Item en Segundos
        $vParBg['pbaopcxx'] = "";                                       //Opciones
        $vParBg['regusrxx'] = $kUser;                                   //Usuario que Creo Registro
        
        #Incluyendo la clase de procesos en background
        $ObjProBg     = new cProcesosBackground();
        $mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);
        
        #Imprimiendo resumen de todo ok.
        if ($mReturnProBg[0] == "true") {
          f_Mensaje(__FILE__,__LINE__,"Proceso en Background Agendado con Exito.");
        } else {
          $nSwitch = 1;
          for($nR=1;$nR<count($mReturnProBg);$nR++){
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= $mReturnProBg[$nR]."\n";
          }
        }
      }
    break;
    case "EXCEL":
      $cNomFile = "CargueFacturasProvisionales_".$_COOKIE['kUsrId']."_".date("YmdHis").".xls";
      $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;
      if (file_exists($cFile)){
        unlink($cFile);
      }
      
      $fOp = fopen($cFile,'a');
      
      $cCad01  = "NUMERO PREFACTURA\t";
      $cCad01 .= "CLIENTE\t";
      $cCad01 .= "FACTURAR A\t";
      $cCad01 .= "FECHA\n";
      
      fwrite($fOp,$cCad01);
      fclose($fOp);
      
      if (file_exists($cFile)){
        chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
      
        // Obtener la ruta absoluta del archivo
        $cAbsolutePath = realpath($cFile);
        $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

        if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
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
        }
      } else {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A..\n";
      }
    break;
		default:
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "Modo de Grabado Viene Vacio.\n";
		break;
  }

  if ($_SERVER["SERVER_PORT"] != "") {
    if ($nSwitch == 1){
      f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
    }else {
      ?>
      <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
        document.forms['frgrm'].submit()
      </script>
      <?php
    }
  }
  
  function fnCrearTablaTem(){
    global $vSysStr; global $cAlfa;

    /**
     * Variable para saber si hay o no errores de validacion.
     *
     * @var number
     */
    $nSwitch = 0;

    /**
     * Matriz para Retornar Errores
     */
    $mReturn = array();

    /**
     * Variable para hacer el retorno.
     * @var array
     */
    $mReturn[0] = "";

    /**
     * Hacer la conexion a la base de datos
     */
    $xConexionTM = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);

    /**
     * Random para Nombre de la Tabla
     */
    $cTabCar  = mt_rand(1000000000, 9999999999);
    
    $cTabla = "memcafpr".$cTabCar;
    $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
    $qNewTab .= "Lineaidx INT(11) NOT NULL AUTO_INCREMENT,";
    $qNewTab .= "comnprex varchar(255)  NOT NULL COMMENT \"Numero Prefactura\",";
    $qNewTab .= "teridxxx varchar(12)   NOT NULL COMMENT \"Cliente\",";
    $qNewTab .= "terid2xx varchar(12)  NOT NULL COMMENT \"Facturar A\",";
    $qNewTab .= "comfecxx	date NOT NULL COMMENT \"Fecha\",";
    $qNewTab .= " PRIMARY KEY (Lineaidx)) ENGINE=MyISAM ";
    // f_Mensaje(__FILE__, __LINE__, $qNewTab);
    $xNewTab = mysql_query($qNewTab,$xConexionTM);
   
    if (!$xNewTab) {
      $nSwitch = 1;
      $mReturn[count($mReturn)] = "Error al Crear la Tabla Temporal, Comuniquese con openTecnologia.";
    }

    if ($nSwitch == 0) {
      $mReturn[0] = "true"; $mReturn[1] = $cTabla;
      return $mReturn;
    } else {
      $mReturn[0] = "false";
      return $mReturn;
    }
  }
  ##function fnCrearTablaTem##
  
  ?>

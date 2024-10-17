<?php
  namespace openComex;
  /**
   * Graba para Cargar Depositos.
   * --- Descripcion: Permite la Creacion y/o Actualizacion de Depositos desde un txt delimintado por tabulaciones.
   * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
   */

  //Estableciendo que el tiempo de ejecucion no se limite
  set_time_limit (0);

  define(_NUMREG_,100);

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
    include("../../../../../financiero/libs/php/utility.php");
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

  $cSystemPath = OC_DOCUMENTROOT;
  $nSwitch     = 0;   // Switch para Vericar la Validacion de Datos
  $cMsj        = "\n";
  $nCanIns     = 0;
  $nCanAct     = 0;

  #Cadenas para reemplazar caracteres espciales
  $vBuscar = array('"',chr(13),chr(10),chr(27),chr(9));
  $vReempl = array('\"'," "," "," "," ");

  /**
   * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales.
   * 
   * @var int
   */
  $cEjePro = 0;

  /**
   * Nombre(s) de los archivos en excel generados
   */
  $cNomArc = "";

  $kModo = ($_SERVER["SERVER_PORT"] == "" ? $_POST['kModo'] : $_COOKIE['kModo']);

  switch ($kModo) {
    case "CARGAR":
      if ($_SERVER["SERVER_PORT"] != "") {
        ## Validando que haya seleccionado un archivo
        if ($_FILES['cArcPla']['name'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Debe Seleccionar un Archivo.\n";
        } else {
          #Copiando el archivo a la carpeta de downloads
          $cNomFile = "/carguedeposito_".$kUser."_".date("YmdHis").".txt";
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
          $mColumnas = array('FECHA DE CREACION','ESTADO');
          while (!feof($mGestor)) {
            $cDatos = fgets($mGestor,10000);
            $mBuffer = explode("\t",$cDatos);
            $nCanCol = (count($mBuffer) > $nCanCol) ? count($mBuffer) : $nCanCol;

            /**
             * Validando que se hayan eliminado las columnas de:
             * FECHA DE CREACION y ESTADO
            */
            for($y=0;$y<$nCanCol;$y++){
              if (in_array(trim($mBuffer[$y]),$mColumnas)) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Debe Eliminar la Columna de ".trim($mBuffer[$y]).".\n";
              }
            }
            break;
          }

          $nTotCol = 9;
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

          /**
           * Campos a excluir en el LOAD DATA INFILE
           */
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
          if(!$xLoad) {
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
         * Trayendo cantidad de registros de la interface.
         * 
         * @var int
         */
        $qItems  = "SELECT * "; 
        $qItems .= "FROM $cAlfa.$cTabCar ";  
        $xItems  = f_MySql("SELECT","",$qItems,$xConexion01,"");
        $nCanReg = mysql_num_rows($xItems);
        
        $cTablas = $cTabCar;
        $cPost   = "kModo~".$_COOKIE['kModo']."|";
        
        $vParBg['pbadbxxx'] = $cAlfa;                      //Base de Datos
        $vParBg['pbamodxx'] = "LOGISTICA";                 //Modulo
        $vParBg['pbatinxx'] = "CARGARDEPOSITO";            //Tipo Interface
        $vParBg['pbatinde'] = "CARGA DE DEPOSITOS";        //Descripcion Tipo de Interfaz
        $vParBg['admidxxx'] = "";                          //Sucursal
        $vParBg['doiidxxx'] = "";                          //Do
        $vParBg['doisfidx'] = "";                          //Sufijo
        $vParBg['cliidxxx'] = "";                          //Nit
        $vParBg['clinomxx'] = "";                          //Nombre Importador
        $vParBg['pbapostx'] = $cPost;                      //Parametros para reconstruir Post
        $vParBg['pbatabxx'] = $cTablas;                    //Tablas Temporales
        $vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME']; //Script
        $vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];     //cookie
        $vParBg['pbacrexx'] = $nCanReg;                    //Cantidad Registros
        $vParBg['pbatxixx'] = 1;                           //Tiempo Ejecucion x Item en Segundos
        $vParBg['pbaopcxx'] = "";                          //Opciones
        $vParBg['regusrxx'] = $kUser;                      //Usuario que Creo Registro
        
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

      if ($_SERVER["SERVER_PORT"] == "") {
        /**
         * Armando parametros para enviar al uticimpo
         */
        $mTablas = explode("~",$xRB['pbatabxx']);
        
        /**
         * Vectore de tablas temporales
         */
        $cTabCar = $mTablas[0];
      }

      if ($cEjePro == 0) {
        if ($nSwitch == 0) {
          //Calculando cantidad de registros en la tabla
          $qDatos  = "SELECT SQL_CALC_FOUND_ROWS * ";
          $qDatos .= "FROM $cAlfa.$cTabCar LIMIT 0,1";
          $cIdCountRow = mt_rand(1000000000, 9999999999);
          $xDatos = mysql_query($qDatos, $xConexion01, true, $cIdCountRow);
          //f_Mensaje(__FILE__,__LINE__,$qDatos."~".mysql_num_rows($xDatos));
          mysql_free_result($xDatos);

          $xNumRows = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD",$xConexion01);
          $xRNR     = mysql_fetch_array($xNumRows);
          $nCanReg  = $xRNR['CANTIDAD'];
          mysql_free_result($xNumRows);
          //f_Mensaje(__FILE__,__LINE__,"tabla temporal -> ".$nCanReg);

          if ($nCanReg == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "No Se Encontraron Registros.\n";
          }
          
          if ($nSwitch == 0) {
            // Contador Creacion y/o Actualizacion
            $nCanIns = 0;
            $nCanAct = 0;

            $qDatos = "SELECT * FROM $cAlfa.$cTabCar";
            $xDatos = mysql_query($qDatos,$xConexion01);
            // f_Mensaje(__FILE__,__LINE__,$qDatos."~".mysql_num_rows($xDatos));
            $nCanReg = 0;

            while ($xRDE = mysql_fetch_array($xDatos)) {
              $nCanReg++;
              if (($nCanReg % _NUMREG_) == 0) {
                $xConexion01 = fnReiniciarConexion();
              }

              if ($xRDE['depnumxx'] != "") {
                //Eliminando caracteres de tabulacion, intelieado de los campos
                foreach ($xRDE as $ckey => $cValue) {
                  $xRDE[$ckey] = trim(str_replace($vBuscar,$vReempl,$xRDE[$ckey]));
                }

                // Identificando si se debe crear o editar el deposito
                $qDeposito  = "SELECT depnumxx ";
                $qDeposito .= "FROM $cAlfa.lpar0155 ";
                $qDeposito .= "WHERE ";
                $qDeposito .= "depnumxx = \"{$xRDE['depnumxx']}\" LIMIT 0,1";
                $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
                // f_Mensaje(__FILE__,__LINE__,$qDeposito."~".mysql_num_rows($xDeposito));

                $cModo = (mysql_num_rows($xDeposito) > 0) ? "EDITAR": "NUEVO"; // Modo de grabado

                //INICIO DE VALIDACIONES
                //Validando que oferta comercial sea alfanumerico
                if (!preg_match("/^[A-Za-z0-9]+$/", $xRDE['depnumxx'])) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "El Numero de Deposito solo puede contener valores alfanumericos.\n";
                }

                // Validando el tipo de deposito
                if ($xRDE['tdeidxxx'] == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "El Tipo de Deposito no puede ser vacio.\n";
                } else {
                  // Validando que el tipo de deposito exista
                  $qTipoDep  = "SELECT tdeidxxx ";
                  $qTipoDep .= "FROM $cAlfa.lpar0007 ";
                  $qTipoDep .= "WHERE ";
                  $qTipoDep .= "tdeidxxx = \"{$xRDE['tdeidxxx']}\" AND ";
                  $qTipoDep .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xTipoDep  = f_MySql("SELECT","",$qTipoDep,$xConexion01,"");
                  if (mysql_num_rows($xTipoDep) == 0) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "El Tipo de Deposito [".$xRDE['tdeidxxx']."] no existe.\n";
                  }
                }

                // Validando el Nit del Cliente
                if ($xRDE['cliidxxx'] == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "El Nit del Cliente no puede ser vacio.\n";
                } else {
                  $qCliente  = "SELECT cliidxxx ";
                  $qCliente .= "FROM $cAlfa.lpar0150 ";
                  $qCliente .= "WHERE ";
                  $qCliente .= "cliidxxx = \"{$xRDE['cliidxxx']}\" AND ";
                  $qCliente .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
                  if (mysql_num_rows($xCliente) == 0) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "El Nit [".$xRDE['cliidxxx']."] del Cliente no existe.\n";
                  }
                }

                // Validando la oferta comercial
                if ($xRDE['ccoidocx'] == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "La Oferta Comercial no puede ser vacia.\n";
                } else {
                  // Validando que la oferta comercial exista
                  $qCondiCom  = "SELECT ccoidocx ";
                  $qCondiCom .= "FROM $cAlfa.lpar0151 ";
                  $qCondiCom .= "WHERE ";
                  $qCondiCom .= "ccoidocx = \"{$xRDE['ccoidocx']}\" AND ";
                  $qCondiCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xCondiCom  = f_MySql("SELECT","",$qCondiCom,$xConexion01,"");
                  if (mysql_num_rows($xCondiCom) == 0) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "La Oferta Comercial [".$xRDE['ccoidocx']."] no existe.\n";
                  }
                }

                // Validando la Periodicidad
                if ($xRDE['pfaidxxx'] == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "La Periodicidad no puede ser vacia.\n";
                } else {
                  // Validando que la Periodicidad exista
                  $qPeriocidad  = "SELECT pfaidxxx ";
                  $qPeriocidad .= "FROM $cAlfa.lpar0005 ";
                  $qPeriocidad .= "WHERE ";
                  $qPeriocidad .= "pfadesxx = \"{$xRDE['pfaidxxx']}\" AND ";
                  $qPeriocidad .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xPeriocidad  = f_MySql("SELECT","",$qPeriocidad,$xConexion01,"");
                  // f_Mensaje(__FILE__,__LINE__,$qPeriocidad."~".mysql_num_rows($xPeriocidad));
                  if (mysql_num_rows($xPeriocidad) == 0) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "La Periodicidad [".$xRDE['pfaidxxx']."] no existe.\n";
                  } else {
                    $vPeriocidad = mysql_fetch_array($xPeriocidad);
                    $xRDE['pfaidxxx'] = $vPeriocidad['pfaidxxx'];
                  }
                }

                // Validando la organizacion de venta
                if ($xRDE['orvsapxx'] == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "La Organizacion de Venta no puede ser vacia.\n";
                } else {
                  // Validando que la organizacion de venta exista
                  $qOrgVenta  = "SELECT orvsapxx ";
                  $qOrgVenta .= "FROM $cAlfa.lpar0001 ";
                  $qOrgVenta .= "WHERE ";
                  $qOrgVenta .= "orvsapxx = \"{$xRDE['orvsapxx']}\" AND ";
                  $qOrgVenta .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xOrgVenta  = f_MySql("SELECT","",$qOrgVenta,$xConexion01,"");
                  if (mysql_num_rows($xOrgVenta) == 0) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "La Organizacion de Venta [".$xRDE['orvsapxx']."] no existe.\n";
                  }
                }

                // Validando la oficina de venta
                if ($xRDE['ofvsapxx'] == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "La Oficina de Venta no puede ser vacia.\n";
                } else {
                  // Validando que la oficina de venta exista
                  $qOfiVenta  = "SELECT ofvsapxx ";
                  $qOfiVenta .= "FROM $cAlfa.lpar0002 ";
                  $qOfiVenta .= "WHERE ";
                  $qOfiVenta .= "orvsapxx = \"{$xRDE['orvsapxx']}\" AND ";
                  $qOfiVenta .= "ofvsapxx = \"{$xRDE['ofvsapxx']}\" AND ";
                  $qOfiVenta .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xOfiVenta  = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");
                  if (mysql_num_rows($xOfiVenta) == 0) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "La Oficina de Venta [".$xRDE['ofvsapxx']."] no existe o no pertenece a la Organizacion de Venta [".$xRDE['orvsapxx']."].\n";
                  }
                }

                // Validando el centro logistico
                if ($xRDE['closapxx'] == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "El Centro Logistico no puede ser vacio.\n";
                } else {
                  // Validando que el centro logistico exista
                  $qCentroLog  = "SELECT closapxx ";
                  $qCentroLog .= "FROM $cAlfa.lpar0003 ";
                  $qCentroLog .= "WHERE ";
                  $qCentroLog .= "orvsapxx = \"{$xRDE['orvsapxx']}\" AND ";
                  $qCentroLog .= "ofvsapxx = \"{$xRDE['ofvsapxx']}\" AND ";
                  $qCentroLog .= "closapxx = \"{$xRDE['closapxx']}\" AND ";
                  $qCentroLog .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xCentroLog  = f_MySql("SELECT","",$qCentroLog,$xConexion01,"");
                  if (mysql_num_rows($xCentroLog) == 0) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "El Centro Logistico [".$xRDE['closapxx']."] no existe o no pertenece a la Organizacion de Venta [".$xRDE['orvsapxx']."] y La Oficina de Venta [".$xRDE['ofvsapxx']."].\n";
                  }
                }

                // Validando el sector
                if ($xRDE['secsapxx'] == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "El Sector no puede ser vacia.\n";
                } else {
                  // Validando que el sector
                  $qOrgVenta  = "SELECT secsapxx ";
                  $qOrgVenta .= "FROM $cAlfa.lpar0009 ";
                  $qOrgVenta .= "WHERE ";
                  $qOrgVenta .= "secsapxx = \"{$xRDE['secsapxx']}\" AND ";
                  $qOrgVenta .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xOrgVenta  = f_MySql("SELECT","",$qOrgVenta,$xConexion01,"");
                  if (mysql_num_rows($xOrgVenta) == 0) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "El Sector [".$xRDE['secsapxx']."] no existe.\n";
                  }
                }

                if ($nSwitch == 0) {
                  $qDeposito  = "SELECT depnumxx ";
                  $qDeposito .= "FROM $cAlfa.lpar0155 ";
                  $qDeposito .= "WHERE ";
                  $qDeposito .= "depnumxx = \"{$xRDE['depnumxx']}\" LIMIT 0,1";
                  $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");

                  switch ($cModo) {
                    case "NUEVO":
                      /***** Validando No. oferta no exista *****/
                      if (mysql_num_rows($xDeposito) > 0) {
                        $nSwitch = 1;
                        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                        $cMsj .= "El Numero de Deposito ya existe.\n";
                      }
                    break;
                    default:
                      /***** Validando No. oferta exista *****/
                      if (mysql_num_rows($xDeposito) == 0) {
                        $nSwitch = 1;
                        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                        $cMsj .= "El Numero de Deposito NO existe.\n";
                      }
                    break;
                  }
                }

                if ($nSwitch == 0) {
                  switch ($cModo) {
                    case "NUEVO":                          
                      $qInsert	= array(array('NAME'=>'depnumxx','VALUE'=>trim($xRDE['depnumxx'])              ,'CHECK'=>'SI'),  //Numero de Deposito
                                        array('NAME'=>'tdeidxxx','VALUE'=>trim($xRDE['tdeidxxx'])              ,'CHECK'=>'SI'),  //Tipo de Deposito
                                        array('NAME'=>'cliidxxx','VALUE'=>trim($xRDE['cliidxxx'])              ,'CHECK'=>'SI'),  //Id Cliente
                                        array('NAME'=>'ccoidocx','VALUE'=>trim($xRDE['ccoidocx'])              ,'CHECK'=>'SI'),  //Id Oferta Comercial
                                        array('NAME'=>'pfaidxxx','VALUE'=>trim($xRDE['pfaidxxx'])              ,'CHECK'=>'SI'),  //Id Periodicidad
                                        array('NAME'=>'orvsapxx','VALUE'=>trim($xRDE['orvsapxx'])              ,'CHECK'=>'SI'),  //Cod SAP Organizacion de Ventas
                                        array('NAME'=>'ofvsapxx','VALUE'=>trim($xRDE['ofvsapxx'])              ,'CHECK'=>'SI'),  //Cod SAP Oficina de Ventas
                                        array('NAME'=>'closapxx','VALUE'=>trim($xRDE['closapxx'])              ,'CHECK'=>'SI'),  //Cod SAP Centro Logistico
                                        array('NAME'=>'secsapxx','VALUE'=>trim($xRDE['secsapxx'])              ,'CHECK'=>'SI'),  //Cod SAP Sector
                                        array('NAME'=>'regusrxx','VALUE'=>$kUser                               ,'CHECK'=>'SI'),  //Usuario que Creo el Registro
                                        array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')                        ,'CHECK'=>'SI'),  //Fecha de Creacion del Registro
                                        array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')                        ,'CHECK'=>'SI'),  //Hora de Creacion del Registro
                                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                        ,'CHECK'=>'SI'),  //Fecha de Modificacion del Registro
                                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                        ,'CHECK'=>'SI'),  //Hora de Modificacion del Registro
                                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                             ,'CHECK'=>'SI')); //Estado del Registro

                      if (!f_MySql("INSERT","lpar0155",$qInsert,$xConexion01,$cAlfa)) {
                        $nSwitch = 1;
                        $cMsj = "Error al Crear los Registros.";
                        $mReturn[count($mReturn)] = (($kModo == "") ? str_pad(__LINE__,4,"0",STR_PAD_LEFT)."~" : "").$cMsj;
                      }
                      $nCanIns++;
                    break;
                    case "EDITAR":
                      $qUpdate	= array(array('NAME'=>'tdeidxxx','VALUE'=>trim($xRDE['tdeidxxx'])              ,'CHECK'=>'SI'),  //Tipo de Deposito
                                        array('NAME'=>'cliidxxx','VALUE'=>trim($xRDE['cliidxxx'])              ,'CHECK'=>'SI'),  //Id Cliente
                                        array('NAME'=>'ccoidocx','VALUE'=>trim($xRDE['ccoidocx'])              ,'CHECK'=>'SI'),  //Id Oferta Comercial
                                        array('NAME'=>'pfaidxxx','VALUE'=>trim($xRDE['pfaidxxx'])              ,'CHECK'=>'SI'),  //Id Periodicidad
                                        array('NAME'=>'orvsapxx','VALUE'=>trim($xRDE['orvsapxx'])              ,'CHECK'=>'SI'),  //Cod SAP Organizacion de Ventas
                                        array('NAME'=>'ofvsapxx','VALUE'=>trim($xRDE['ofvsapxx'])              ,'CHECK'=>'SI'),  //Cod SAP Oficina de Ventas
                                        array('NAME'=>'closapxx','VALUE'=>trim($xRDE['closapxx'])              ,'CHECK'=>'SI'),  //Cod SAP Centro Logistico
                                        array('NAME'=>'secsapxx','VALUE'=>trim($xRDE['secsapxx'])              ,'CHECK'=>'SI'),  //Cod SAP Sector
                                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                        ,'CHECK'=>'SI'),  //Fecha de Modificacion del Registro
                                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                        ,'CHECK'=>'SI'),  //Hora de Modificacion del Registro
                                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                             ,'CHECK'=>'SI'),  //Estado del Registro
                                        array('NAME'=>'depnumxx','VALUE'=>trim($xRDE['depnumxx'])              ,'CHECK'=>'WH')); //Numero de Deposito

                        if (!f_MySql("UPDATE","lpar0155",$qUpdate,$xConexion01,$cAlfa)) {
                          $nSwitch = 1;
                          $cMsj = "Error al Actualizar los Registros.";
                          $mReturn[count($mReturn)] = (($kModo == "") ? str_pad(__LINE__,4,"0",STR_PAD_LEFT)."~" : "").$cMsj;
                        }
                      $nCanAct++;
                    break;
                    default:
                      $nSwitch = 1;
                      $cMsj = "Modo de Grabado Vacio.";
                      $mReturn[count($mReturn)] = (($kModo == "") ? str_pad(__LINE__,4,"0",STR_PAD_LEFT)."~" : "").$cMsj;
                    break;
                  }
                }
              }
            }
          }
          $cMsj = (($nCanIns > 0 || $nCanAct > 0) ? "Creados: $nCanIns. Actualizados: $nCanAct.\n\n" : "").(($nSwitch == 1) ? "Se presentaron los siguientes errores en la ejecucion del proceso: ".$cMsj : "");  
        }
      }

    break;
    default:
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Modo de Grabado Viene Vacio.\n";
    break;
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
    if ($nSwitch == 1){
      f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
    } else {
      if($_POST['cEjProBg'] != "SI"){
        f_Mensaje(__FILE__,__LINE__,$cMsj);
      } ?>
      <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
        document.forms['frgrm'].submit()
      </script>
      <?php
    }
  }

  function fnCrearTablaTem() {
    global $vSysStr; global $cAlfa;

    /**
     * Variable para saber si hay o no errores de validacion.
     *
     * @var int
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
    
    $cTabla   = "memdepos".$cTabCar;
    $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
    $qNewTab .= "lineaidx INT(11) NOT NULL AUTO_INCREMENT,";                                //LINEA
    $qNewTab .= "depnumxx varchar(15) NOT NULL COMMENT \"Numero Deposito\",";               //NO. DEPOSITO
    $qNewTab .= "tdeidxxx varchar(2) NOT NULL COMMENT \"Id Tipo de Deposito\",";            //ID TIPO DEPOSITO
    $qNewTab .= "cliidxxx varchar(20) NOT NULL COMMENT \"Id Cliente\",";                    //NIT
    $qNewTab .= "ccoidocx varchar(20) NOT NULL COMMENT \"Id Oferta Comercial\",";           //ID OFERTA COMERCIAL
    $qNewTab .= "pfaidxxx varchar(20) NOT NULL COMMENT \"Id Periodicidad\",";                //ID PERIODICIDAD
    $qNewTab .= "orvsapxx varchar(4) NOT NULL COMMENT \"Cod SAP Organizacion de Ventas\","; //COD SAP ORGANIZACION DE VENTAS
    $qNewTab .= "ofvsapxx varchar(4) NOT NULL COMMENT \"Cod SAP Oficina de Ventas\",";      //COD SAP OFICINA DE VENTAS
    $qNewTab .= "closapxx varchar(4) NOT NULL COMMENT \"Cod SAP Centro Logistico\",";       //COD SAP CENTRO LOGISTICO
    $qNewTab .= "secsapxx varchar(2)NOT NULL COMMENT \"Cod SAP Sector\",";                  //COD SAP SECTOR
    $qNewTab .= " PRIMARY KEY (lineaidx)) ENGINE=MyISAM ";
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

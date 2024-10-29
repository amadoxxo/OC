<?php
  namespace openComex;
  /**
   * Graba Creacion y/o Actualizacion de Condiciones Comerciales desde un txt delimintado por tabulaciones.
   * --- Descripcion: Permite Subir y Creacion y/o Actualizacion de Condiciones Comerciales desde un txt delimintado por tabulaciones.
   * @author Diego Fernando Cortes Rojas <diego.cortes@openits.co>
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

  $cSystemPath= OC_DOCUMENTROOT;

  $nSwitch  = 0;   // Switch para Vericar la Validacion de Datos
  $cMsj     = "\n";

  $nCanIns = 0;
  $nCanAct = 0;

  #Numero de registros por recorrido
  $nNumReg = 2000;

  #Cadenas para reemplazar caracteres espciales
  $vBuscar = array('"',chr(13),chr(10),chr(27),chr(9));
  $vReempl = array('\"'," "," "," "," ");

  /**
   * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales
   * @var Number
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
          $cMsj .= "Debe Digitar un Archivo.\n";
        } else {
          #Copiando el archivo a la carpeta de downloads
          $cNomFile = "/carcondicom_".$kUser."_".date("YmdHis").".txt";
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
        $cPost   = "kModo~".$_COOKIE['kModo']."|";
        
        $vParBg['pbadbxxx'] = $cAlfa;                                   //Base de Datos
        $vParBg['pbamodxx'] = "LOGISTICA";                              //Modulo
        $vParBg['pbatinxx'] = "CARGARCONDICIONESCOMERCIALES";           //Tipo Interface
        $vParBg['pbatinde'] = "CARGA DE CONDICIONES COMERCIALES";       //Descripcion Tipo de Interfaz
        $vParBg['admidxxx'] = "";                                       //Sucursal
        $vParBg['doiidxxx'] = "";                                       //Do
        $vParBg['doisfidx'] = "";                                       //Sufijo
        $vParBg['cliidxxx'] = "";                                       //Nit
        $vParBg['clinomxx'] = "";                                       //Nombre Importador
        $vParBg['pbapostx'] = $cPost;                                   //Parametros para reconstruir Post
        $vParBg['pbatabxx'] = $cTablas;                                 //Tablas Temporales
        $vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];              //Script
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

            while ($xRD = mysql_fetch_array($xDatos)) {
              $nCanReg++;
              if (($nCanReg % _NUMREG_) == 0) {
                $xConexion01 = fnReiniciarConexion();
              }

              if ($xRD['ccoidocx'] != "") {
                //Eliminando caracteres de tabulacion, intelieado de los campos
                foreach ($xRD as $ckey => $cValue) {
                  $xRD[$ckey] = trim(str_replace($vBuscar,$vReempl,$xRD[$ckey]));
                }

                // Identificando si se debe crear o editar la Condicion Comercial
                $qCondiCom  = "SELECT ccoidocx ";
                $qCondiCom .= "FROM $cAlfa.lpar0151 ";
                $qCondiCom .= "WHERE ";
                $qCondiCom .= "ccoidocx = \"{$xRD['ccoidocx']}\" LIMIT 0,1";
                $xCondiCom  = f_MySql("SELECT","",$qCondiCom,$xConexion01,"");
                // f_Mensaje(__FILE__,__LINE__,$qCondiCom."~".mysql_num_rows($xCondiCom));

                $cModo = (mysql_num_rows($xCondiCom) > 0) ? "EDITAR": "NUEVO"; // Modo de grabado

                //INICIO DE VALIDACIONES

                //Validando que oferta comercial sea alfanumerico y tenga guion
                if (!preg_match('/^[a-zA-Z0-9\-]+$/', $xRD['ccoidocx'])) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                  $cMsj .= "El No. Oferta Comercial debe contener valores alfanumericos y guiones.\n";
                } else {
                  //Validando que contenga hasta 15 caracteres de id Oferta Comercial.
                  if (strlen($xRD['ccoidocx']) < 1 || strlen($xRD['ccoidocx']) > 15){
                    $nSwitch = 1;
                    $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                    $cMsj .= "El No. Oferta Comercial admite hasta 15 caracteres.\n";
                  }
                }

                //Validando que no sea vacio el NIT del cliente.
                if ($xRD['ccotipxx'] == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                  $cMsj .= "El Nit no puede ser vacio.\n";
                } else {
                  $qClientes  = "SELECT cliidxxx ";
                  $qClientes .= "FROM $cAlfa.lpar0150 ";
                  $qClientes .= "WHERE ";
                  $qClientes .= "cliidxxx = \"{$xRD['cliidxxx']}\" LIMIT 0,1";
                  $xClientes  = f_MySql("SELECT","",$qClientes,$xConexion01,"");

                  // Validando Nit Cliente exista
                  if (mysql_num_rows($xClientes) == 0) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                    $cMsj .= "El Nit ".$xRD['cliidxxx']." del cliente no existe.\n";
                  }
                }

                //Validando que el Tipo sea diferente a SELECCIONE.
                if ($xRD['ccotipxx'] == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                  $cMsj .= "Debe seleccionar un Tipo.\n";
                }

                //Reemplazando los espacios entre palabras para Tipo.
                $xRD['ccotipxx'] = trim(str_replace(" ","_",$xRD['ccotipxx']));

                //Validando que el Tipo tenga los valores del combo.
                if ($xRD['ccotipxx'] != "OFERTA_COMERCIAL" && $xRD['ccotipxx'] != "CONTRATO" && $xRD['ccotipxx'] != "ALCANCE" && $xRD['ccotipxx'] != "OTRO_SI" && $xRD['ccotipxx'] != "OTRO") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                  $cMsj .= "Debe seleccionar un Tipo valido.\n";
                }

                //Validando que el Cierre de Facturación sea diferente a SELECCIONE.
                if ($xRD['ccociexx'] == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                  $cMsj .= "Debe seleccionar un Cierre de Facturacion.\n";
                }

                //Validando que el Cierre de Facturación tenga valores correspondientes.
                if ($xRD['ccociexx'] < 1 && $xRD['ccociexx'] > 31) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                  $cMsj .= "Debe seleccionar un Cierre de Facturacion valido.\n";
                }

                //Validando que la Fecha Vigencia Desde no sea vacia.
                if ($xRD['ccofvdxx'] == "" || $xRD['ccofvdxx'] == "0000-00-00") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                  $cMsj .= "Debe seleccionar una Fecha Vigencia Desde valida [YYYY-MM-DD].\n";
                }

                //Validando que la Fecha Vigencia Hasta no sea vacia.
                if ($xRD['ccofvhxx'] == "" || $xRD['ccofvhxx'] == "0000-00-00") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                  $cMsj .= "Debe seleccionar una Fecha Vigencia Hasta Valida [YYYY-MM-DD].\n";
                }

                //Validando que la Fecha Vigencia Hasta sea mayor que la Fecha Vigencia Desde.
                if ($xRD['ccofvhxx'] < $xRD['ccofvdxx']) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                  $cMsj .= "La Fecha de Vigencia Hasta debe ser mayor que la Fecha Vigencia Desde.\n";
                }

                //Validando que el Tipo Incremento sea diferente a SELECCIONE.
                if ($xRD['ccoincxx'] == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                  $cMsj .= "Debe seleccionar un Tipo Incremento.\n";
                }

                //Validando que el Tipo Incremento sea igual a los que aparecen en su opciones.
                if ($xRD['ccoincxx'] != "SMMLV" && $xRD['ccoincxx'] != "IPC" && $xRD['ccoincxx'] != "IPC+1" && $xRD['ccoincxx'] != "IPC+2" && $xRD['ccoincxx'] != "OTRO") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                  $cMsj .= "Debe seleccionar un Tipo Incremento Valido.\n";
                }

                //Validando que el campo Especifique sea vacio en caso de que NO se seleccione OTRO.
                if ($xRD['ccoincxx'] !="OTRO" && $xRD['ccoincox'] != "") {
                  $xRD['ccoincox'] =="";
                }

                //Validando que el campo Especifique no sea vacio cuando se ha seleccionado OTRO.
                if ($xRD['ccoincxx'] =="OTRO" && $xRD['ccoincox'] == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                  $cMsj .= "Debe Especificar el Tipo Incremento.\n";
                }

                if ($nSwitch == 0) {
                  $qCondiCom  = "SELECT ccoidocx ";
                  $qCondiCom .= "FROM $cAlfa.lpar0151 ";
                  $qCondiCom .= "WHERE ";
                  $qCondiCom .= "ccoidocx = \"{$xRD['ccoidocx']}\" LIMIT 0,1";
                  $xCondiCom  = f_MySql("SELECT","",$qCondiCom,$xConexion01,"");

                  switch ($cModo) {
                    case "NUEVO":
                      /***** Validando No. oferta no exista *****/
                      if (mysql_num_rows($xCondiCom) > 0) {
                        $nSwitch = 1;
                        $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                        $cMsj .= "El No. Oferta Comercial ya existe en las Condiciones Comerciales.\n";
                      }
                    break;
                    default:
                      /***** Validando No. oferta exista *****/
                      if (mysql_num_rows($xCondiCom) == 0) {
                        $nSwitch = 1;
                        $cMsj .= "Linea ".$xRD['lineaidx'].": ";
                        $cMsj .= "El No. Oferta Comercial NO existe en las Condiciones Comerciales.\n";
                      }
                    break;
                  }
                }
                if ($nSwitch == 0) {
                  switch ($cModo) {
                    case "NUEVO":
                      $qInsert	= array(array('NAME'=>'ccoidocx','VALUE'=>trim($xRD['ccoidocx'])                        ,'CHECK'=>'SI'),         //Id Oferta Comercial
                                        array('NAME'=>'cliidxxx','VALUE'=>trim($xRD['cliidxxx'])                        ,'CHECK'=>'SI'),         //Id Cliente
                                        array('NAME'=>'ccotipxx','VALUE'=>trim($xRD['ccotipxx'])                        ,'CHECK'=>'SI'),         //Tipo
                                        array('NAME'=>'ccociexx','VALUE'=>trim($xRD['ccociexx'])                        ,'CHECK'=>'SI'),         //Dia Cierre
                                        array('NAME'=>'ccofvdxx','VALUE'=>trim($xRD['ccofvdxx'])                        ,'CHECK'=>'SI'),         //Fecha Vigencia Desde
                                        array('NAME'=>'ccofvhxx','VALUE'=>trim($xRD['ccofvhxx'])                        ,'CHECK'=>'SI'),         //Fecha Vigencia Hasta
                                        array('NAME'=>'ccoincxx','VALUE'=>trim($xRD['ccoincxx'])                        ,'CHECK'=>'SI'),         //Incremento
                                        array('NAME'=>'ccoincox','VALUE'=>trim($xRD['ccoincox'])                        ,'CHECK'=>'NO'),         //Incremento Otros
                                        array('NAME'=>'ccoobsxx','VALUE'=>trim($xRD['ccoobsxx'])                        ,'CHECK'=>'NO'),         //Observacion
                                        array('NAME'=>'regusrxx','VALUE'=>$kUser                                        ,'CHECK'=>'SI'),         //Usuario que Creo el Registro
                                        array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')                                 ,'CHECK'=>'SI'),         //Fecha de Creacion del Registro
                                        array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')                                 ,'CHECK'=>'SI'),         //Hora de Creacion del Registro
                                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                                 ,'CHECK'=>'SI'),         //Fecha de Modificacion del Registro
                                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                                 ,'CHECK'=>'SI'),         //Hora de Modificacion del Registro
                                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                                      ,'CHECK'=>'SI'));        //Estado del Registro

                      if (!f_MySql("INSERT","lpar0151",$qInsert,$xConexion01,$cAlfa)) {
                        $nSwitch = 1;
                        $cMsj = "Error Guardando Datos.";
                        $mReturn[count($mReturn)] = (($kModo == "") ? str_pad(__LINE__,4,"0",STR_PAD_LEFT)."~" : "").$cMsj;
                      }
                      $nCanIns++;
                    break;
                    case "EDITAR":
                      $qUpdate	= array(array('NAME'=>'cliidxxx','VALUE'=>trim($xRD['cliidxxx'])                        ,'CHECK'=>'SI'),         //Id Cliente
                                        array('NAME'=>'ccotipxx','VALUE'=>trim($xRD['ccotipxx'])                        ,'CHECK'=>'SI'),         //Tipo
                                        array('NAME'=>'ccociexx','VALUE'=>trim($xRD['ccociexx'])                        ,'CHECK'=>'SI'),         //Dia Cierre
                                        array('NAME'=>'ccofvdxx','VALUE'=>trim($xRD['ccofvdxx'])                        ,'CHECK'=>'SI'),         //Fecha Vigencia Desde
                                        array('NAME'=>'ccofvhxx','VALUE'=>trim($xRD['ccofvhxx'])                        ,'CHECK'=>'SI'),         //Fecha Vigencia Hasta
                                        array('NAME'=>'ccoincxx','VALUE'=>trim($xRD['ccoincxx'])                        ,'CHECK'=>'SI'),         //Incremento
                                        array('NAME'=>'ccoincox','VALUE'=>trim($xRD['ccoincox'])                        ,'CHECK'=>'NO'),         //Incremento Otros
                                        array('NAME'=>'ccoobsxx','VALUE'=>trim($xRD['ccoobsxx'])                        ,'CHECK'=>'NO'),         //Observacion
                                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                                 ,'CHECK'=>'SI'),         //Fecha de Modificacion del Registro
                                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                                 ,'CHECK'=>'SI'),         //Hora de Modificacion del Registro
                                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                                      ,'CHECK'=>'SI'),         //Estado del Registro
                                        array('NAME'=>'ccoidocx','VALUE'=>trim($xRD['ccoidocx'])                        ,'CHECK'=>'WH'));        //Id Oferta Comercial

                        if (!f_MySql("UPDATE","lpar0151",$qUpdate,$xConexion01,$cAlfa)) {
                          $nSwitch = 1;
                          $cMsj = "Error Actualizando Datos.";
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
    }else {
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
    
    $cTabla   = "memcacco".$cTabCar;
    $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
    $qNewTab .= "lineaidx INT(11) NOT NULL AUTO_INCREMENT,";                       //LINEA
    $qNewTab .= "ccoidocx varchar(20) NOT NULL COMMENT \"Id Oferta Comercial\",";  //ID OFERTA COMERCIAL
    $qNewTab .= "cliidxxx varchar(20) NOT NULL COMMENT \"Id Cliente\",";           //ID CLIENTE
    $qNewTab .= "ccotipxx varchar(20) NOT NULL COMMENT \"Tipo\",";                 //TIPO
    $qNewTab .= "ccociexx varchar(2) NOT NULL COMMENT \"Dia Cierre\",";            //DIA CIERRE
    $qNewTab .= "ccofvdxx date NOT NULL COMMENT \"Fecha Vigencia Desde\",";        //FECHA VIGENCIA DESDE
    $qNewTab .= "ccofvhxx date NOT NULL COMMENT \"Fecha Vigencia Hasta\",";        //FECHA VIGENCIA HASTA
    $qNewTab .= "ccoincxx varchar(10) NOT NULL COMMENT \"Incremento\",";           //INCREMENTO
    $qNewTab .= "ccoincox text NULL COMMENT \"Incremento Otros\",";                //ICREMENTO OTROS
    $qNewTab .= "ccoobsxx text NULL COMMENT \"Observacion\",";                     //OBSERVACIONES
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

<?php
	//Estableciendo que el tiempo de ejecucion no se limite
	set_time_limit (0);

	/**
	* Graba Creacion y/o Actualizacion de terceros desde un txt delimintado por tabulaciones.
	* --- Descripcion: Permite Subir y Creacion y/o Actualizacion de terceros desde un txt delimintado por tabulaciones.
	* @author Johana Arboleda Ramos <jarboleda@opentecnologia.com.co>
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

  $kModo = ($_SERVER["SERVER_PORT"] == "" ? $_POST['kModo'] : $_COOKIE['kModo']);
  
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
          $cNomFile = "/carbcoaut_".$kUser."_".date("YmdHis").".txt";
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
          $mColumnas = array('CLIENTE (SI - NO)','ESTADO');
          while (!feof($mGestor)) {
            $cDatos = fgets($mGestor,10000);
            $mBuffer = explode("\t",$cDatos);
            $nCanCol = (count($mBuffer) > $nCanCol) ? count($mBuffer) : $nCanCol;

            /**
             * Validando que se hayan eliminado las columnas de:
             * CLIENTE (SI - NO) y ESTADO
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

          switch ($cAlfa) {
            case "DHLEXPRE": case "TEDHLEXPRE": case "DEDHLEXPRE":
              $nTotCol = 66;
            break;
            default:
              $nTotCol = 57;
            break;
          }
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
          $vFieldsExcluidos = array("CLIREIVA","CLIARXXX");

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
          echo $qLoad;
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
        $vParBg['pbatinxx'] = "CARGARTERCEROS";                         //Tipo Interface
        $vParBg['pbatinde'] = "CARGA DE TERCEROS";                      //Descripcion Tipo de Interfaz
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
          $xDatos = mysql_query($qDatos,$xConexion01);
          //f_Mensaje(__FILE__,__LINE__,$qDatos."~".mysql_num_rows($xDatos));
          mysql_free_result($xDatos);

          $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
          $xRNR = mysql_fetch_array($xNumRows);
          $nCanReg = $xRNR['FOUND_ROWS()'];
          mysql_free_result($xNumRows);
          //f_Mensaje(__FILE__,__LINE__,"tabla temporal -> ".$nCanReg);

          if ($nCanReg == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "No Se Encontraron Registros.\n";
          }

          if ($nSwitch == 0) {
            $qDatos = "SELECT * FROM $cAlfa.$cTabCar";
            $xDatos = mysql_query($qDatos,$xConexion01);
            //f_Mensaje(__FILE__,__LINE__,$qDatos."~".mysql_num_rows($xDatos));

            while ($xRD = mysql_fetch_assoc($xDatos)) {

              //Eliminando caracteres de tabulacion, intelieado de los campos
              foreach ($xRD as $ckey => $cValue) {
                $xRD[$ckey] = trim(strtoupper(str_replace($vBuscar,$vReempl,$xRD[$ckey])));
              }

              $xRTC = array();

              //Validaciones del graba de Terceros
              /***** Validando Codigo *****/
              $nExiste = 0;
              if ($xRD['CLIIDXXX'] == "") {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Codigo del Tercero no puede ser vacio.\n";
              }else{
                $qTerCod  = "SELECT CLIIDXXX,REGESTXX ";
                $qTerCod .= "FROM $cAlfa.SIAI0150 WHERE CLIIDXXX = \"{$xRD['CLIIDXXX']}\" LIMIT 0,1";
                $xTerCod  = f_MySql("SELECT","",$qTerCod,$xConexion01,"");
                if (mysql_num_rows($xTerCod) > 0) {
                  $nExiste = 1;
                  $xRTC = mysql_fetch_array($xTerCod);

                  if($xRTC['REGESTXX'] != "ACTIVO") {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "No Puede Actualizarse el Tercero [{$xRD['CLIIDXXX']}] porque se Existe en Estado [{$xRTC['REGESTXX']}].\n";
                  }
                }
              }

              if ($xRD['TDIIDXXX'] == "") {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Codigo del Tipo de Documento No Puede Ser Vacio.\n";
              } else {
                $qDatTdi  = "SELECT * FROM $cAlfa.fpar0109 WHERE tdiidxxx = \"{$xRD['TDIIDXXX']}\" AND regestxx = \"ACTIVO\" ";
                $xDatTdi = f_MySql("SELECT","",$qDatTdi,$xConexion01,"");
                if (mysql_num_rows($xDatTdi) == 0) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "El Codigo del Tipo de Documento {$xRD['TDIIDXXX']} No Existe o esta Inactivo.\n";
                }
              }

              //Para los NIT y Cedula de ciudadania el Numero de Identificacion debe ser Numerico
              //Para los demas debe ser alfanumerico, puede contener guion
              switch ($xRD['TDIIDXXX']){
                case "31":
                case "13":
                  /**
                   * Validando que sea numerico
                   */
                  if (!preg_match("/^[[:digit:]]+$/", $xRD['CLIIDXXX'])) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "Codigo del Tercero Debe Ser Numerico.\n";
                  }
                break;
                default:
                  //validando que sea alfanumerico y/o tenga un guion
                  if (!preg_match("/^[a-zA-Z0-9-]+$/", $xRD['CLIIDXXX'])) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "Codigo del Tercero Debe Contener Letras, Numeros y/o Guiones .\n";
                  }
                break;
              }

              /* Validado  Tipo de Persona  */
              if ($xRD['CLITPERX'] == "NATURAL") {
                $xRD['CLINOMXX'] = "";
                $xRD['CLINOMCX'] = "";
              } else {
                $xRD['CLIAPE1X'] = "";
                $xRD['CLIAPE2X'] = "";
                $xRD['CLINOM1X'] = "";
                $xRD['CLINOM2X'] = "";
              }

              /* Validado  Tipo de Persona	*/
              if (!f_InList($xRD['CLITPERX'],"PUBLICA","JURIDICA","NATURAL")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Tipo de Persona Debe ser PUBLICA o JURIDICA o NATURAL.\n";
              }

              if ($xRD['CLITPERX'] == "") {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Tipo de Persona No Pude Ser Vacio.\n";
              }	elseif ($xRD['CLITPERX'] == "NATURAL" &&
                  $xRD['CLIAPE1X'] == "" &&
                  $xRD['CLIAPE2X'] == "" &&
                  $xRD['CLINOM1X'] == "" &&
                  $xRD['CLINOM2X'] == "")	{
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Para el Tipo de Persona \"NATURAL\" Debe Digitar Primer Apellido, Segundo Apellido, Primer Nombre y Segundo Nombre.\n";
              }

              if($xRD['CLITPERX'] == "NATURAL" &&
                ($xRD['CLIAPE1X'] != "" ||
                $xRD['CLIAPE2X'] != "" ||
                $xRD['CLINOM1X'] != "" ||
                $xRD['CLINOM2X'] != "")) {
                $cNombre = "";
                $cNombre .= $xRD['CLIAPE1X'] != "" ? $xRD['CLIAPE1X']." " : "";
                $cNombre .= $xRD['CLIAPE2X'] != "" ? $xRD['CLIAPE2X']." " : "";
                $cNombre .= $xRD['CLINOM1X'] != "" ? $xRD['CLINOM1X']." " : "";
                $cNombre .= $xRD['CLINOM2X'] != "" ? $xRD['CLINOM2X']." " : "";
                $xRD['CLINOMXX'] = trim($cNombre);
              }

              if($xRD['CLITPERX'] == "JURIDICA" || $xRD['CLITPERX'] == "PUBLICA" ){
                if($xRD['CLINOMXX'] == "" || $xRD['CLINOMCX'] == ""){
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Debe Digitar Razon Social Y Nombre Comercial.\n";
                }//if($xRD['CLINOMXX'] == "" || $xRD['CLINOMCX'] == ""){
              }//if($xRD['CLITPERX'] == "JURIDICA" || $xRD['CLITPERX'] == "PUBLICA" ){

              /*validando Pais*/
              if($xRD['PAIIDXXX'] == ""){
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Debe Digitar Pais de Domicilio Fiscal.\n";
              } else {
                //Validando que el pais digitado exista
                $qPais  = "SELECT PAIIDXXX ";
                $qPais .= "FROM $cAlfa.SIAI0052 ";
                $qPais .= "WHERE ";
                $qPais .= "PAIIDXXX = \"{$xRD['PAIIDXXX']}\" AND ";
                $qPais .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
                $xPais  = f_MySql("SELECT","",$qPais,$xConexion01,"");
                if (mysql_num_rows($xPais) == 0) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "El Pais {$xRD['PAIIDXXX']} de Domicilio Fiscal No Existe o esta Inactivo.\n";
                }
              }

              if($xRD['PAIIDXXX'] == "CO") {
                if($xRD['DEPIDXXX'] == "" ){
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Debe Digitar Departamento de Domicilio Fiscal.\n";
                }

                if($xRD['CIUIDXXX'] == "" ){
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Debe Digitar Ciudad de Domicilio Fiscal.\n";
                }
              }

              if($xRD['DEPIDXXX'] != ""){
                //Validando Departamento
                $qDpto  = "SELECT PAIIDXXX,DEPIDXXX ";
                $qDpto .= "FROM $cAlfa.SIAI0054 ";
                $qDpto .= "WHERE ";
                $qDpto .= "PAIIDXXX = \"{$xRD['PAIIDXXX']}\" AND ";
                $qDpto .= "DEPIDXXX = \"{$xRD['DEPIDXXX']}\" AND ";
                $qDpto .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
                $xDpto  = f_MySql("SELECT","",$qDpto,$xConexion01,"");
                if (mysql_num_rows($xDpto) == 0) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "El Departamento {$xRD['DEPIDXXX']} de Domicilio Fiscal No Existe o esta Inactivo.\n";
                }
              }

              if($xRD['CIUIDXXX'] != ""){
                //Validando Ciudad
                $qCiudad  = "SELECT PAIIDXXX,CIUIDXXX ";
                $qCiudad .= "FROM $cAlfa.SIAI0055 ";
                $qCiudad .= "WHERE ";
                $qCiudad .= "PAIIDXXX = \"{$xRD['PAIIDXXX']}\" AND ";
                $qCiudad .= "CIUIDXXX = \"{$xRD['CIUIDXXX']}\" AND ";
                $qCiudad .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
                $xCiudad  = f_MySql("SELECT","",$qCiudad,$xConexion01,"");
                if (mysql_num_rows($xCiudad) == 0) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "La Ciudad {$xRD['CIUIDXXX']} de Domicilio Fiscal No Existe o esta Inactivo.\n";
                }
              }
              
              /*validando Domicilio Fiscal*/
              if($xRD['CLIDIRXX'] == ""){
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Debe Digitar la Direccion del Domicilio Fiscal.\n";
              }

              /*Validando Telefono del Tercero*/
              if($xRD['CLITELXX'] == ""){
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Debe Digitar Un Numero de Telefono.\n";
              }

              /*validando Pais Direccion Correspondencia*/
              if($xRD['PAIID3XX'] == ""){
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Debe Digitar Pais de Correspondencia.\n";
              } else {
                //Validando que el pais digitado exista
                $qPais  = "SELECT PAIIDXXX ";
                $qPais .= "FROM $cAlfa.SIAI0052 ";
                $qPais .= "WHERE ";
                $qPais .= "PAIIDXXX = \"{$xRD['PAIID3XX']}\" AND ";
                $qPais .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
                $xPais  = f_MySql("SELECT","",$qPais,$xConexion01,"");
                if (mysql_num_rows($xPais) == 0) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "El Pais {$xRD['PAIID3XX']} de Correspondencia No Existe o esta Inactivo.\n";
                }
              }

              if($xRD['PAIID3XX'] == "CO" ){
                if($xRD['DEPID3XX'] == "" ){
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Debe Digitar Departamento de Correspondencia.\n";
                }

                if($xRD['CIUID3XX'] == "" ){
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Debe Digitar Ciudad de Correspondencia.\n";
                }
              }

              if($xRD['DEPID3XX'] != "" ){
                //Validando Departamento
                $qDpto  = "SELECT PAIIDXXX,DEPIDXXX ";
                $qDpto .= "FROM $cAlfa.SIAI0054 ";
                $qDpto .= "WHERE ";
                $qDpto .= "PAIIDXXX = \"{$xRD['PAIID3XX']}\" AND ";
                $qDpto .= "DEPIDXXX = \"{$xRD['DEPID3XX']}\" AND ";
                $qDpto .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
                $xDpto  = f_MySql("SELECT","",$qDpto,$xConexion01,"");
                if (mysql_num_rows($xDpto) == 0) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "El Departamento {$xRD['DEPID3XX']} de Correspondencia No Existe o esta Inactivo.\n";
                }
              }

              if($xRD['CIUID3XX'] != "" ){
                //Validando Ciudad
                $qCiudad  = "SELECT PAIIDXXX,DEPIDXXX,CIUIDXXX ";
                $qCiudad .= "FROM $cAlfa.SIAI0055 ";
                $qCiudad .= "WHERE ";
                $qCiudad .= "PAIIDXXX = \"{$xRD['PAIID3XX']}\" AND ";
                $qCiudad .= "DEPIDXXX = \"{$xRD['DEPID3XX']}\" AND ";
                $qCiudad .= "CIUIDXXX = \"{$xRD['CIUID3XX']}\" AND ";
                $qCiudad .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
                $xCiudad  = f_MySql("SELECT","",$qCiudad,$xConexion01,"");
                if (mysql_num_rows($xCiudad) == 0) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "La Ciudad {$xRD['CIUID3XX']} de Correspondencia No Existe o esta Inactivo.\n";
                }
              }

              /*validando Direccion Correspondencia*/
              if($xRD['CLIDIR3X'] == ""){
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Debe Digitar la Direccion de Correspondencia.\n";
              }

              $cCliClie = ""; 
              if ($xRD['CLIPROCX'] == "SI" || $xRD['CLIPROEX'] == "SI" || $xRD['CLISOCXX'] == "SI" ||
                  $xRD['CLIEFIXX'] == "SI" || $xRD['CLIOTRXX'] == "SI" || $xRD['CLIEMPXX'] == "SI" || $xRD['CLIVENCO'] == "SI") {
                //No hace nada
              } else {
                $qTerCod  = "SELECT CLIIDXXX,REGESTXX,CLICLIXX ";
                $qTerCod .= "FROM $cAlfa.SIAI0150 WHERE CLIIDXXX = \"{$xRD['CLIIDXXX']}\" LIMIT 0,1";
                $xTerCod  = f_MySql("SELECT","",$qTerCod,$xConexion01,"");
                if (mysql_num_rows($xTerCod) > 0) {
                  $xRTC = mysql_fetch_array($xTerCod);
                  $cCliClie = $xRTC['CLICLIXX'];
                  if($xRTC['CLICLIXX'] != "SI") {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "Falta Clasificacion del Tercero.\n";
                  }
                }else{
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Falta Clasificacion del Tercero.\n";
                }
              }
              /**
               * Valido que si se asignan vendedores, eston existan y esten marcados como vendedores
               */
              if( $xRD['CLIVENXX'] != "" ){
                $vVendedores = explode(",",$xRD['CLIVENXX']);
                $xRD['CLIVENXX'] = "";
                for($nI = 0; $nI < count($vVendedores) ; $nI++){
                  $qSelectVendedor  = "SELECT ";
                  $qSelectVendedor .= "CLIVENCO ";
                  $qSelectVendedor .= "FROM ";
                  $qSelectVendedor .= "$cAlfa.SIAI0150 ";
                  $qSelectVendedor .= "WHERE ";
                  $qSelectVendedor .= "CLIIDXXX = \"{$vVendedores[$nI]}\" AND ";
                  $qSelectVendedor .= "CLIVENCO = \"SI\" AND ";
                  $qSelectVendedor .= "REGESTXX = \"ACTIVO\" ";
                  $qSelectVendedor .= "LIMIT 0,1";
                  $xSelectVendedor  = mysql_query($qSelectVendedor,$xConexion01);
                  if(mysql_num_rows($xSelectVendedor) == 0){
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "El Vendedor[{$vVendedores[$nI]}] no Existe, Esta Inactivo o no Esta Parametrizado como Vendedor.\n";
                  }else{
                    if($vVendedores[$nI] != $xRD['CLIIDXXX'] ){
                      $xRD['CLIVENXX'] .= $vVendedores[$nI]."~";
                    }
                  }
                }
                $xRD['CLIVENXX'] = substr($xRD['CLIVENXX'], 0, -1);
              }

              /**
               * Valida que se digite al menos un vendedor.
               */ 
              switch ($cAlfa) {
                case 'INTERLO2':
                case 'DEINTERLO2':
                case 'TEINTERLO2':
                  if ($xRD['CLIVENXX'] == "") {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= " Debe Digitar al Menos un Vendedor.\n";
                  }
                  break;
              }

              /**
               * Si la Marca de Vendedor Viene Vacio, Verifico si existe el Tercero, si tiene la marca de Vendedor y esta asignado no lo Permito Editar
               */
              if( $xRD['CLIVENCO'] != "SI" ){
                $qTerCod  = "SELECT CLIIDXXX,REGESTXX,CLICLIXX ";
                $qTerCod .= "FROM $cAlfa.SIAI0150 WHERE CLIIDXXX = \"{$xRD['CLIIDXXX']}\" LIMIT 0,1";
                $xTerCod  = mysql_query($qTerCod,$xConexion01);
                if (mysql_num_rows($xTerCod) > 0) {
                  $qCliDat  = "SELECT CLIIDXXX,CLIVENXX, IF(CLINOMXX != \"\",CLINOMXX,TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))) AS CLINOMXX, REGESTXX ";
                  $qCliDat .= "FROM $cAlfa.SIAI0150 ";
                  $qCliDat .= "WHERE CLIVENXX LIKE \"%{$xRD['CLIIDXXX']}%\" AND ";
                  $qCliDat .= "REGESTXX = \"ACTIVO\" ORDER BY CLINOMXX";
                  $xCliDat  = mysql_query($qCliDat,$xConexion01);
                  $cClientes = "";
                  while( $xRCD = mysql_fetch_array($xCliDat) ){
                    $cClientes .= "[{$xRCD['CLIIDXXX']}] {$xRCD['CLINOMXX']}.\n";
                  }
                  if ($cClientes != "") {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "No Puede Desactivar la Clasificacion VENDEDOR, el Tercero esta asignado como vendedor al (los) Tercero(s): \n$cClientes\n";
                  }
                }
              }

              $xRD['CLIREIVA'] = ($xRD['CLIREIVA'] == "") ? "NO" : $xRD['CLIREIVA'];
              $xRD['CLIRECOM'] = ($xRD['CLIRECOM'] == "") ? "NO" : $xRD['CLIRECOM'];
              $xRD['CLIRESIM'] = ($xRD['CLIRESIM'] == "") ? "NO" : $xRD['CLIRESIM'];
              $xRD['CLIGCXXX'] = ($xRD['CLIGCXXX'] == "") ? "NO" : $xRD['CLIGCXXX'];
              $xRD['CLIREGST'] = ($xRD['CLIREGST'] == "") ? "NO" : $xRD['CLIREGST'];
              $xRD['CLINRPXX'] = ($xRD['CLINRPXX'] == "") ? "NO" : $xRD['CLINRPXX'];
              $xRD['CLINRPAI'] = ($xRD['CLINRPAI'] == "") ? "NO" : $xRD['CLINRPAI'];
              $xRD['CLINRPIF'] = ($xRD['CLINRPIF'] == "") ? "NO" : $xRD['CLINRPIF'];
              $xRD['CLINRNSR'] = ($xRD['CLINRNSR'] == "") ? "NO" : $xRD['CLINRNSR'];
              $xRD['CLIARXXX'] = ($xRD['CLIARXXX'] == "") ? "NO" : $xRD['CLIARXXX'];
              $xRD['CLIARARE'] = ($xRD['CLIARARE'] == "") ? "NO" : $xRD['CLIARARE'];
              $xRD['CLIARAIV'] = ($xRD['CLIARAIV'] == "") ? "NO" : $xRD['CLIARAIV'];
              $xRD['CLIARAIC'] = ($xRD['CLIARAIC'] == "") ? "NO" : $xRD['CLIARAIC'];
              $xRD['CLIARACR'] = ($xRD['CLIARACR'] == "") ? "NO" : $xRD['CLIARACR'];
              $xRD['CLINSRRX'] = ($xRD['CLINSRRX'] == "") ? "NO" : $xRD['CLINSRRX'];
              $xRD['CLINSRIV'] = ($xRD['CLINSRIV'] == "") ? "NO" : $xRD['CLINSRIV'];
              $xRD['CLINSRCR'] = ($xRD['CLINSRCR'] == "") ? "NO" : $xRD['CLINSRCR'];
              $xRD['CLINSRRI'] = ($xRD['CLINSRRI'] == "") ? "NO" : $xRD['CLINSRRI'];
              $xRD['CLIARRXX'] = ($xRD['CLIARRXX'] == "") ? "NO" : $xRD['CLIARRXX'];
              $xRD['CLIARIVA'] = ($xRD['CLIARIVA'] == "") ? "NO" : $xRD['CLIARIVA'];
              $xRD['CLIARCRX'] = ($xRD['CLIARCRX'] == "") ? "NO" : $xRD['CLIARCRX'];
              $xRD['CLIARRIX'] = ($xRD['CLIARRIX'] == "") ? "NO" : $xRD['CLIARRIX'];
              $xRD['CLIPCIXX'] = ($xRD['CLIPCIXX'] == "") ? "NO" : $xRD['CLIPCIXX'];
              $xRD['CLINSOFE'] = ($xRD['CLINSOFE'] == "") ? "NO" : $xRD['CLINSOFE'];

              #Validaciones Condiciones Tributarias
              if (!f_InList($xRD['CLIPROCX'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Tercero de Tipo Proveedor-Cliente Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIPROEX'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Tercero de Tipo Proveedor-Empresa Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLISOCXX'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Tercero de Tipo Proveedor-Socio Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIEFIXX'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Tercero de Tipo Entidad Financiera Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIOTRXX'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Tercero de Tipo Proveedor-Otros Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIEMPXX'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Tercero de Tipo Empleado Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIVENCO'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Tercero de Tipo Vendedor Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIRECOM'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria Responsable Iva Regimen Comun Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIRESIM'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria Responsable Iva Regimen Simplificado Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIGCXXX'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria Gran Contribuyente Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIREGST'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria Regimen Simple Tributario Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLINRPXX'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria No Residente en el Pais Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLINRPAI'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria No Residente en el Pais - Aplica IVA Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLINRPIF'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria No Residente en el Pais - No Sujeto RETEFTE por Renta Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLINRNSR'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria No Residente en el Pais - Aplica GMF Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIARARE'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria Autoretenedor en Renta Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIARAIV'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria Autoretenedor de IVA Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIARAIC'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria Autoretenedor de ICA Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIARACR'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria Autoretenedor de CREE Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIARACR'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria No sujeto RETEFTE por Renta Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLINSRRX'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria No sujeto RETEFTE por Renta Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLINSRIV'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria No sujeto RETEFTE por IVA Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLINSRCR'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria No sujeto Retencion CREE Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLINSRRI'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria No sujeto a Retencion ICA Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIARRXX'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria Agente Retenedor en Renta Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIARIVA'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria Agente Retenedor en IVA Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIARCRX'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria Agente Retenedor CREE Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIARRIX'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Tributaria Agente Retenedor ICA Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLIPCIXX'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion Proveedor Comercializadora Internacional Debe ser SI o NO o Vacio.\n";
              }

              if (!f_InList($xRD['CLINSOFE'],"SI","NO","")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Condicion No Sujeto a Expedir Factura de Venta o Documento Equivalente Debe ser SI o NO o Vacio.\n";
              }

              $xRD['CLIRECOM'] = ($xRD['CLIGCXXX'] == "SI") ? "SI" : $xRD['CLIRECOM'];

              if ($xRD['CLIRECOM'] == "SI" && $xRD['CLIRESIM'] == "SI") {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Responsable del IVA Debe Ser Regimen Comun o Simplificado, pero no Ambos.\n";
              }

              if ($xRD['CLIGCXXX'] == "SI" && $xRD['CLIRESIM'] == "SI") {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Responsable del IVA Debe Ser Regimen Simplificado o Gran Contribuyente, pero no Ambos.\n";
              }

              $xRD['CLIREIVA'] = ($xRD['CLIRECOM'] == "SI" || $xRD['CLIRESIM'] == "SI" || $xRD['CLIGCXXX'] == "SI") ? "SI" : "";
              $cCliReg = ($xRD['CLIRECOM'] == "SI" || $xRD['CLIGCXXX'] == "SI") ? "COMUN" : (($xRD['CLIRESIM'] == "SI") ?  "SIMPLIFICADO" : "");

              #Validando Responsable de IVA
              if($xRD['CLIREIVA'] == "SI") {
                if($cCliReg == "") {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Debe Digitar Si el Responsable del IVA es Regimen Comun o Simplificado.\n";
                } else {
                  if($cCliReg == "COMUN") {
                    $xRD['CLIREIVA'] = "SI";
                    $xRD['CLIRECOM'] = "SI";
                    $xRD['CLIRESIM'] = "NO";
                  } elseif($cCliReg == "SIMPLIFICADO") {
                    $xRD['CLIREIVA'] = "SI";
                    $xRD['CLIRECOM'] = "NO";
                    $xRD['CLIRESIM'] = "SI";
                  } else {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "Debe Digitar Si el Responsable del IVA es Regimen Comun o Simplificado.\n";
                  }
                }
              } else {
                $xRD['CLIREIVA'] = "NO";
                $xRD['CLIRECOM'] = "NO";
                $xRD['CLIRESIM'] = "NO";
              }

              #Validando Gran Contribuyebte
              if ($xRD['CLIGCXXX'] == "SI") {
                $xRD['CLIGCXXX'] = "SI";
              } else {
                $xRD['CLIGCXXX'] = "NO";
              }

              #Validando No Residente en el Pais
              if ($xRD['CLINRPXX'] == "SI") {
                $xRD['CLINRPXX'] = "SI";
                $xRD['CLINRPAI'] = ($xRD['CLINRPAI'] == "SI") ? "SI" : "NO";
                $xRD['CLINRPIF'] = ($xRD['CLINRPIF'] == "SI") ? "SI" : "NO";
                $xRD['CLINRNSR'] = ($xRD['CLINRNSR'] == "SI") ? "SI" : "NO";
              } else {
                $xRD['CLINRPXX'] = "NO";
                $xRD['CLINRPAI'] = "NO";
                $xRD['CLINRPIF'] = "NO";
                $xRD['CLINRNSR'] = "NO";
              }

              $xRD['CLIARXXX'] = ($xRD['CLIARARE'] == "SI" || $xRD['CLIARAIV'] == "SI" || $xRD['CLIARAIC'] == "SI" || $xRD['CLIARACR'] == "SI") ? "SI" : "NO";

              #Validando Autorretenedor
              if ($xRD['CLIARXXX'] == "SI") {
                $xRD['CLIARXXX'] = "SI";
                $xRD['CLIARARE'] = ($xRD['CLIARARE'] == "SI") ? "SI" : "NO";
                $xRD['CLIARAIV'] = ($xRD['CLIARAIV'] == "SI") ? "SI" : "NO";
                $xRD['CLIARAIC'] = ($xRD['CLIARAIC'] == "SI") ? "SI" : "NO";
                $xRD['CLIARACR'] = ($xRD['CLIARACR'] == "SI") ? "SI" : "NO";
                $xRD['CLIARAIS'] = ($xRD['CLIARAIC'] == "SI") ? $xRD['CLIARAIS'] : "";
                #Validando que selecciono al menos una sucursal
                if($xRD['CLIARAIC'] == "SI"){
                  $vCliArAis = explode(",",$xRD['CLIARAIS']);
                  $nCon = 0;
                  $cCliArAis = "";
                  for($i=0; $i<count($vCliArAis);$i++) {
                    $vCliArAis[$i] = trim($vCliArAis[$i]);
                    if ($vCliArAis[$i] != "") {
                      $cCliArAis .= $vCliArAis[$i]."~";
                      $nCon++;

                      //Validando que la sucursal existe
                      $qSucIca  = "SELECT * ";
                      $qSucIca .= "FROM $cAlfa.fpar0008 ";
                      $qSucIca .= "WHERE ";
                      $qSucIca .= "sucidxxx = \"{$vCliArAis[$i]}\" AND ";
                      $qSucIca .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
                      $xSucIca  = f_MySql("SELECT","",$qSucIca,$xConexion01,"");
                      if (mysql_num_rows($xSucIca) == 0) {
                        $nSwitch = 1;
                        $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                        $cMsj .= "La Sucursal {$vCliArAis[$i]} ICA x Sucursares del Autorretenedor no Existe o esta Inactiva.\n";
                      }
                    }
                  }

                  $cCliArAis = substr($cCliArAis,0,strlen($cCliArAis)-1);
                  $xRD['CLIARAIS'] = $cCliArAis;

                  if($nCon == 0) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "Debe Digitar las ICA x Sucursares del Autorretenedor.\n";
                  }
                }
              } else {
                $xRD['CLIARXXX'] = "NO";
                $xRD['CLIARARE'] = "NO";
                $xRD['CLIARAIV'] = "NO";
                $xRD['CLIARAIC'] = "NO";
                $xRD['CLIARACR'] = "NO";
                $xRD['CLIARAIS'] = "";
              }

              #Validando No Sujeto RETEFTE Renta
              if ($xRD['CLINSRRX'] == "SI") {
                $xRD['CLINSRRX'] = "SI";
              } else {
                $xRD['CLINSRRX'] = "NO";
              }

              #Validando No Sujeto RETEFTE por IVA
              if ($xRD['CLINSRIV'] == "SI") {
                $xRD['CLINSRIV'] = "SI";
              } else {
                $xRD['CLINSRIV'] = "NO";
              }

              #Validando No Sujeto de Retencion ICA
              if ($xRD['CLINSRRI'] == "SI") {
                $xRD['CLINSRRI'] = "SI";
              } else {
                $xRD['CLINSRRI'] = "NO";
              }

              #Validando No Sujeto Retencion CREE
              if ($xRD['CLINSRCR'] == "SI") {
                $xRD['CLINSRCR'] = "SI";
              } else {
                $xRD['CLINSRCR'] = "NO";
              }

              #Validando Agente Retenedor Renta
              if ($xRD['CLIARRXX'] == "SI") {
                $xRD['CLIARRXX'] = "SI";
              } else {
                $xRD['CLIARRXX'] = "NO";
              }

              #Validando Agente Retenedor en IVA
              if ($xRD['CLIARIVA'] == "SI") {
                $xRD['CLIARIVA'] = "SI";
              } else {
                $xRD['CLIARIVA'] = "NO";
              }

              #Validando Agente Retenedor CREE
              if ($xRD['CLIARCRX'] == "SI") {
                $xRD['CLIARCRX'] = "SI";
              } else {
                $xRD['CLIARCRX'] = "NO";
              }

              #Agente Retenedor ICA en
              if ($xRD['CLIARRIX'] == "SI") {
                #Validando que selecciono al menos una sucursal
                $vCliArrIs = explode(",",$xRD['CLIARRIS']);
                $nCon = 0;
                $cCliArrIs = "";
                for($i=0; $i<count($vCliArrIs);$i++) {
                  if ($vCliArrIs[$i] != "") {
                    $cCliArrIs .= $vCliArrIs[$i]."~";
                    $nCon++;

                    //Validando que la sucursal existe
                    $qSucIca  = "SELECT * ";
                    $qSucIca .= "FROM $cAlfa.fpar0008 ";
                    $qSucIca .= "WHERE ";
                    $qSucIca .= "sucidxxx = \"{$vCliArrIs[$i]}\" AND ";
                    $qSucIca .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
                    $xSucIca  = f_MySql("SELECT","",$qSucIca,$xConexion01,"");
                    if (mysql_num_rows($xSucIca) == 0) {
                      $nSwitch = 1;
                      $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                      $cMsj .= "La Sucursal {$vCliArrIs[$i]} ICA x Sucursares del Agente Retenedor ICA no Existe o esta Inactiva.\n";
                    }
                  }
                }
                $cCliArrIs = substr($cCliArrIs,0,strlen($cCliArrIs)-1);
                $xRD['CLIARRIS'] = $cCliArrIs;

                if($nCon == 0) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Debe Digitar las ICA x Sucursares del Agente Retenedor ICA en.\n";
                }
              }else {
                $xRD['CLIARRIX'] = "NO";
                $xRD['CLIARRIS'] = "";
              }

              #Validando Proveedor Comercializadora Internacional
              if ($xRD['CLIPCIXX'] == "SI") {
                $xRD['CLIPCIXX'] = "SI";
              } else {
                $xRD['CLIPCIXX'] = "NO";
              }

              #Validando No Sujeto a Expedir Factura de Venta o Documento Equivalente
              if ($xRD['CLINSOFE'] == "SI") {
                $xRD['CLINSOFE'] = "SI";
              } else {
                $xRD['CLINSOFE'] = "NO";
              }

              /***
              * Valiaciones de agrupamiento de las Condiciones Tributarias
              */

              /**
              * Si se Activa Responsable IVA - Regimen Simplificado no debe permitir que se active el Check de
              * Autorretenedor,
              * No Residente en el Pas,
              * Agente Retenedor de Renta,
              * Agente Retenedor de IVA,
              * Agente Retenedor CREE,
              * Agente Retenedor de Ica y
              * Proveedor Comercializadora Internacional.
              */

              if($cCliReg   == "SIMPLIFICADO" &&
                ($xRD['CLIARXXX'] == "SI" ||
                  $xRD['CLINRPXX'] == "SI" ||
                  $xRD['CLIARRXX'] == "SI" ||
                  $xRD['CLIARIVA'] == "SI" ||
                  $xRD['CLIARRIX'] == "SI"||
                  $xRD['CLIPCIXX'] == "SI")) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Si Marco que Aplica Responsable IVA - Regimen Simplificado, no Debe Digitar que Aplica Como Auterretenedor, ";
                $cMsj .= "No Residente en el Pais, ";
                $cMsj .= "Agente Retenedor en Renta, ";
                $cMsj .= "Agente Retenedor en IVA, ";
                $cMsj .= "Agente Retenedor CREE, ";
                $cMsj .= "Agente Retenedor de Ica en o ";
                $cMsj .= "Proveedor Comercializadora Internacional.\n";
              }

              /**
              * Si se Activa Gran Contribuyente debe obligar a marcar el Campo Responsable IVA - Regimen Comun
              */
              if($xRD['CLIGCXXX'] == "SI" && $cCliReg != "COMUN") {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Si Marco que Aplica Como Gran Contribuyente Debe Digitar que Aplica como Responsable IVA - Regimen Comun.\n";
              }

              /**
              * Si se Activa - Regimen Simple Tributario no debe permitir que se active el Check de
              * Gran Contribuyente,
              * No Sujeto RETEFTE por Renta y
              * Agente Retenedor de Renta
              */

              if($xRD['CLIREGST'] == "SI" &&
                  ($xRD['CLIGCXXX'] == 'SI' ||
                  $xRD['CLINSRRX']  == 'SI' ||
                  $xRD['CLIARRXX']  == 'SI' )) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Si Marco que Regimen Simple Tributario, no Debe Seleccionar que Aplica Como ";
                $cMsj .= "Gran Contribuyente, ";
                $cMsj .= "No Sujeto RETEFTE por Renta, ";
                $cMsj .= "Agente Retenedor en Renta.\n";
                
              }
              
              /**
              * Si Activo No Residente en el Pais no puede estar marcado
              * Responsable IVA,
              * Gran Contribuyente,
              * Autorretenedor,
              * No Sujeto RETEFUENTE por Renta,
              * No Sujeto RETEFUENTE por IVA,
              * No Sujeto Retencion CREE,
              * Agente Retenedor en Renta,
              * Agente Retenedor en IVA,
              * Agente Retenedor CREE,
              * Agente Retenedor ICA en y
              * Proveedor Comercializadora Internacional.
              */
              if($xRD['CLINRPXX']  == "SI" &&
                ($xRD['CLIREIVA'] == "SI" ||
                  $xRD['CLIGCXXX'] == "SI" ||
                  $xRD['CLIARXXX'] == "SI" ||
                  $xRD['CLINSRRX'] == "SI" ||
                  $xRD['CLINSRIV'] == "SI" ||
                  $xRD['CLINSRRI'] == "SI" ||
                  $xRD['CLINSRCR'] == "SI" ||
                  $xRD['CLIARRXX'] == "SI" ||
                  $xRD['CLIARIVA'] == "SI" ||
                  $xRD['CLIARCRX'] == "SI" ||
                  $xRD['CLIARRIX'] == "SI" ||
                  $xRD['CLIPCIXX'] == "SI")) {
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Si Marco que Aplica No Residente en el Pais, no Debe Digitar que Aplica Como ";
                $cMsj .= "Gran Contribuyente, ";
                $cMsj .= "Auterretenedor, ";
                $cMsj .= "No Sujeto RETEFTE por Renta, ";
                $cMsj .= "No Sujeto RETEFTE por IVA, ";
                $cMsj .= "No Sujeto Retencion CREE, ";
                $cMsj .= "Agente Retenedor en Renta, ";
                $cMsj .= "Agente Retenedor en IVA, ";
                $cMsj .= "Agente Retenedor CREE, ";
                $cMsj .= "Agente Retenedor de Ica, ";
                $cMsj .= "No Sujeto Retencion Ica o ";
                $cMsj .= "Proveedor Comercializadora Internacional.\n";
              }

              /**
              * Si se Activa Proveedor Comercializadora Internacional debe estar activo Responsable IVA - Regimen Comon.
              */
              if($xRD['CLIPCIXX'] == "SI" && $cCliReg != "COMUN") {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Si Marco que Aplica Como Proveedor Comercializadora Internacional Debe Digitar que Aplica como Responsable IVA - Regimen Comun.\n";
              }
              
              /**
              * Validando que la Responsabilidad Fiscal exista en la Base de Datos y no este Inactiva
              */
              if(trim($xRD['CLIRESFI']) != ""){
                $cCliResFis = "";
                $vCliResFis = explode(",", $xRD['CLIRESFI']);
                for($i=0; $i<count($vCliResFis); $i++){
                  if($vCliResFis[$i] != ""){
                    $cCliResFis .= $vCliResFis[$i]."~";
                    $qResFis  = "SELECT ";
                    $qResFis .= "rfiidxxx,";
                    $qResFis .= "regestxx ";
                    $qResFis .= "FROM $cAlfa.fpar0152 ";
                    $qResFis .= "WHERE ";
                    $qResFis .= "rfiidxxx = \"{$vCliResFis[$i]}\" LIMIT 0,1";
                    $xResFis  = f_MySql("SELECT","",$qResFis,$xConexion01,"");
                    $vResFis  = mysql_fetch_array($xResFis);
                    if(mysql_num_rows($xResFis) == 0){
                      $nSwitch = 1;
                      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                      $cMsj .= "La Responsabilidad Fiscal[".$vCliResFis[$i]."], No Existe en la Base de Datos.\n";
                    }elseif($vResFis['regestxx'] == "INACTIVO"){
                      $nSwitch = 1;
                      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                      $cMsj .= "La Responsabilidad Fiscal[".$vCliResFis[$i]."], se Encuentra INACTIVA.\n";
                    }
                  }
                }
                $cCliResFis = substr($cCliResFis, 0, -1);
                $xRD['CLIRESFI'] = $cCliResFis;
              }

              ## Validando que el Tributo exista en la Base de Datos y no este Inactivo ##
              if(trim($xRD['CLITRIBU']) != ""){
                $cCliTribu = "";
                $vCliTribu = explode(",", $xRD['CLITRIBU']);
                for($i=0; $i<count($vCliTribu); $i++){
                  if($vCliTribu[$i] != ""){
                    $cCliTribu .= $vCliTribu[$i]."~";
                    $qTributo  = "SELECT ";
                    $qTributo .= "triidxxx,";
                    $qTributo .= "regestxx ";
                    $qTributo .= "FROM $cAlfa.fpar0153 ";
                    $qTributo .= "WHERE ";
                    $qTributo .= "triidxxx = \"{$vCliTribu[$i]}\" LIMIT 0,1";
                    $xTributo  = f_MySql("SELECT","",$qTributo,$xConexion01,"");
                    $vTributo  = mysql_fetch_array($xTributo);
                    if(mysql_num_rows($xTributo) == 0){
                      $nSwitch = 1;
                      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                      $cMsj .= "El Tributo[".$vCliTribu[$i]."], No Existe en la Base de Datos.\n";
                    }elseif($vTributo['regestxx'] == "INACTIVO"){
                      $nSwitch = 1;
                      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                      $cMsj .= "El Tributo[".$vCliTribu[$i]."], se Encuentra INACTIVO.\n";
                    }
                  }
                }
                $cCliTribu = substr($cCliTribu, 0, -1);
                $xRD['CLITRIBU'] = $cCliTribu;
              }

              if($vSysStr['system_activar_openetl'] == "SI") {
                if(trim($xRD['CLIPCECN']) == ""){
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Correos Notificacion No Puede Ser Vacio.\n";
                }
              }

              if(trim($xRD['CLIPCECN']) != "") {
                $vCorreos = explode(",", $xRD['CLIPCECN']);
                for ($i=0; $i < count($vCorreos); $i++) {
                  $vCorreos[$i] = trim($vCorreos[$i]);
                  if($vCorreos[$i] != ""){
                    if (!filter_var($vCorreos[$i], FILTER_VALIDATE_EMAIL)) {
                      $nSwitch = 1;
                      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                      $cMsj .= "El Correo Notificacion[".$vCorreos[$i]."], No es Valido.\n";
                    }
                  }
                }
              }

              if ($cAlfa == "DHLEXPRE" || $cAlfa == "DEDHLEXPRE" || $cAlfa == "TEDHLEXPRE") {
                $cImpCash = "";
                $cImpCred = "";
                if(trim($xRD['CLIIMPCS']) != "") {
                  // Para los terceros marcados como clientes se valida que se digite cuenta IMP Cash
                  if ($cCliClie == "SI" && $xRD['CLIIMPCS'] == "") {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "La Cuenta IMP Cash No Puede Ser Vacia.\n";
                  }

                  // Si se digita cuenta IMP Cash esta debe ser DUTYCOADA
                  if ($xRD['CLIIMPCS'] != "" && $xRD['CLIIMPCS'] != "DUTYCOADA") {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "Solo se Permite la Cuenta IMP Cash DUTYCOADA.\n";
                  }

                  $vCtaCash = explode(',', $xRD['CLIIMPCS']);
                  $vEstCash = explode(',', $xRD['CLICASES']);

                  for ($i=0; $i < count($vCtaCash); $i++) { 
                    $cEstado   = ($vEstCash[$i] != "") ? $vEstCash[$i] : "ACTIVO";
                    $cImpCash .= $vCtaCash[$i] . "~" . $cEstado . "|";
                  }
                }

                if(trim($xRD['CLIIMPCR']) != "") {
                  $vCtaCred = explode(',', $xRD['CLIIMPCR']);
                  $vEstCred = explode(',', $xRD['CLICREES']);

                  for ($i=0; $i < count($vCtaCred); $i++) { 
                    $cEstado   = ($vEstCred[$i] != "") ? $vEstCred[$i] : "ACTIVO";
                    $cImpCred .= $vCtaCred[$i] . "~" . $cEstado . "|";
                  }
                }
              }

              if ($nSwitch == 0) {
                //Actualizando los datos del cliente
                $qUpdate  = "UPDATE $cAlfa.$cTabCar SET ";
                $qUpdate .= "CLIIDXXX = \"{$xRD['CLIIDXXX']}\", ";
                $qUpdate .= "CLINOMXX = \"{$xRD['CLINOMXX']}\", ";
                $qUpdate .= "CLIREIVA = \"{$xRD['CLIREIVA']}\", ";
                $qUpdate .= "CLIRECOM = \"{$xRD['CLIRECOM']}\", ";
                $qUpdate .= "CLIRESIM = \"{$xRD['CLIRESIM']}\", ";
                $qUpdate .= "CLIGCXXX = \"{$xRD['CLIGCXXX']}\", ";
                $qUpdate .= "CLIREGST = \"{$xRD['CLIREGST']}\", ";
                $qUpdate .= "CLINRPXX = \"{$xRD['CLINRPXX']}\", ";
                $qUpdate .= "CLINRPAI = \"{$xRD['CLINRPAI']}\", ";
                $qUpdate .= "CLINRPIF = \"{$xRD['CLINRPIF']}\", ";
                $qUpdate .= "CLINRNSR = \"{$xRD['CLINRNSR']}\", ";
                $qUpdate .= "CLIARXXX = \"{$xRD['CLIARXXX']}\", ";
                $qUpdate .= "CLIARARE = \"{$xRD['CLIARARE']}\", ";
                $qUpdate .= "CLIARAIV = \"{$xRD['CLIARAIV']}\", ";
                $qUpdate .= "CLIARAIC = \"{$xRD['CLIARAIC']}\", ";
                $qUpdate .= "CLIARACR = \"{$xRD['CLIARACR']}\", ";
                $qUpdate .= "CLIARAIS = \"{$xRD['CLIARAIS']}\", ";
                $qUpdate .= "CLINSRRX = \"{$xRD['CLINSRRX']}\", ";
                $qUpdate .= "CLINSRIV = \"{$xRD['CLINSRIV']}\", ";
                $qUpdate .= "CLINSRRI = \"{$xRD['CLINSRRI']}\", ";
                $qUpdate .= "CLINSRCR = \"{$xRD['CLINSRCR']}\", ";
                $qUpdate .= "CLIARRXX = \"{$xRD['CLIARRXX']}\", ";
                $qUpdate .= "CLIARIVA = \"{$xRD['CLIARIVA']}\", ";
                $qUpdate .= "CLIARCRX = \"{$xRD['CLIARCRX']}\", ";
                $qUpdate .= "CLIARRIX = \"{$xRD['CLIARRIX']}\", ";
                $qUpdate .= "CLIARRIS = \"{$xRD['CLIARRIS']}\", ";
                $qUpdate .= "CLIPCIXX = \"{$xRD['CLIPCIXX']}\", ";
                $qUpdate .= "CLINSOFE = \"{$xRD['CLINSOFE']}\", ";
                $qUpdate .= "CLIRESFI = \"{$xRD['CLIRESFI']}\", ";
                $qUpdate .= "CLITRIBU = \"{$xRD['CLITRIBU']}\", ";
                $qUpdate .= "CLIVENXX = \"{$xRD['CLIVENXX']}\"  ";
                $qUpdate .= "WHERE LINEAIDX = \"{$xRD['LINEAIDX']}\"";
                $xUpdate = mysql_query($qUpdate,$xConexion01);

                if (!$xUpdate) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Error al Actualizar tabla temporal de Terceros.\n";
                }
              }
            }
          }

          if($nSwitch == 0){
            $qDatos = "SELECT * FROM $cAlfa.$cTabCar";
            $xDatos = mysql_query($qDatos,$xConexion01);
            //f_Mensaje(__FILE__,__LINE__,$qDatos."~".mysql_num_rows($xDatos));

            while ($xRD = mysql_fetch_assoc($xDatos)) {

              $qTerCod  = "SELECT CLIIDXXX ";
              $qTerCod .= "FROM $cAlfa.SIAI0150 WHERE CLIIDXXX = \"{$xRD['CLIIDXXX']}\" LIMIT 0,1";
              $xTerCod  = f_MySql("SELECT","",$qTerCod,$xConexion01,"");
              if (mysql_num_rows($xTerCod) == 0) { //Insertar

                $qInsert =  array(array('NAME'=>'CLIIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIIDXXX']),'"')),'CHECK'=>'SI'),
                                  array('NAME'=>'TDIIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['TDIIDXXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLITPERX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLITPERX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINOMXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINOMXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINOMCX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINOMCX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIAPE1X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIAPE1X']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIAPE2X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIAPE2X']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINOM1X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINOM1X']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINOM2X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINOM2X']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'PAIIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['PAIIDXXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'DEPIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['DEPIDXXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CIUIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CIUIDXXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLITELXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLITELXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIDIRXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIDIRXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIEMAXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIEMAXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'PAIID3XX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['PAIID3XX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'DEPID3XX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['DEPID3XX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CIUID3XX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CIUID3XX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIDIR3X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIDIR3X']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIPROCX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIPROCX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIPROEX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIPROEX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLISOCXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLISOCXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIEFIXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIEFIXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIOTRXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIOTRXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIEMPXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIEMPXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIVENCO','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIVENCO']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIVENXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIVENXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIREIVA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIREIVA']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIRECOM','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIRECOM']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIRESIM','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIRESIM']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIGCXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIGCXXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIREGST','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIREGST']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINRPXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINRPXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINRPAI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINRPAI']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINRPIF','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINRPIF']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINRNSR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINRNSR']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARXXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARARE','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARARE']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARAIV','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARAIV']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARAIC','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARAIC']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARAIS','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARAIS']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARACR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARACR']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINSRRX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINSRRX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINSRIV','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINSRIV']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINSRCR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINSRCR']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINSRRI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINSRRI']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARRXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARRXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARIVA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARIVA']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARCRX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARCRX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARRIX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARRIX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARRIS','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARRIS']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIPCIXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIPCIXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINSOFE','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINSOFE']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLICPOSX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLICPOSX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLICPOS3','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLICPOS3']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIRESFI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIRESFI']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLITRIBU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLITRIBU']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIPCECN','VALUE'=>str_replace($cBuscarEmail,$cReemplEmail,trim(trim($xRD['CLIPCECN']),'"')),'CHECK'=>'NO','CS'=>'NONE'),
                                  array('NAME'=>'CLIMMERX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIMMERX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'REGUSRXX','VALUE'=>$kUser                                                                   ,'CHECK'=>'SI'),
                                  array('NAME'=>'REGFECXX','VALUE'=>date('Y-m-d')		                                                         ,'CHECK'=>'SI'),
                                  array('NAME'=>'REGHORXX','VALUE'=>date('H:i')	                                                             ,'CHECK'=>'SI'),
                                  array('NAME'=>'REGMODXX','VALUE'=>date('Y-m-d')		                                                         ,'CHECK'=>'SI'),
                                  array('NAME'=>'REGESTXX','VALUE'=>"ACTIVO"  			                                                         ,'CHECK'=>'SI'));

                switch ($cAlfa) {
                  case "DHLEXPRE": case "TEDHLEXPRE": case "DEDHLEXPRE":
                    $qInsertDhl = array(array('NAME'=>'CLIIMPCS','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($cImpCash),'"')),'CHECK'=>'NO'),
                                        array('NAME'=>'CLIIMPCR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($cImpCred),'"')),'CHECK'=>'NO'),
                                        array('NAME'=>'CLIBANID','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIBANID']),'"')),'CHECK'=>'NO'),
                                        array('NAME'=>'CLITIPCU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLITIPCU']),'"')),'CHECK'=>'NO'),
                                        array('NAME'=>'CLINUMCU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINUMCU']),'"')),'CHECK'=>'NO'),
                                        array('NAME'=>'CLIESTCU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIESTCU']),'"')),'CHECK'=>'NO'));

                    $qInsert = array_merge($qInsert, $qInsertDhl);
                  break;
                }

                if (f_MySql("INSERT","SIAI0150",$qInsert,$xConexion01,$cAlfa)) {
                  $nCanIns++;
                } else {
                  $nError = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Error al Insertar Nit [{$xRD['CLIIDXXX']}].\n";
                }
              } else { //Actualizar

                $qUpdate =  array(array('NAME'=>'TDIIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['TDIIDXXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLITPERX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLITPERX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINOMXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINOMXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINOMCX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINOMCX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIAPE1X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIAPE1X']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIAPE2X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIAPE2X']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINOM1X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINOM1X']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINOM2X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINOM2X']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'PAIIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['PAIIDXXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'DEPIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['DEPIDXXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CIUIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CIUIDXXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLITELXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLITELXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIDIRXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIDIRXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIEMAXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIEMAXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'PAIID3XX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['PAIID3XX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'DEPID3XX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['DEPID3XX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CIUID3XX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CIUID3XX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIDIR3X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIDIR3X']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIPROCX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIPROCX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIPROEX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIPROEX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLISOCXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLISOCXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIEFIXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIEFIXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIOTRXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIOTRXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIEMPXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIEMPXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIVENCO','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIVENCO']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIVENXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIVENXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIREIVA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIREIVA']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIRECOM','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIRECOM']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIRESIM','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIRESIM']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIGCXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIGCXXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIREGST','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIREGST']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINRPXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINRPXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINRPAI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINRPAI']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINRPIF','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINRPIF']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINRNSR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINRNSR']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARXXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARARE','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARARE']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARAIV','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARAIV']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARAIC','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARAIC']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARAIS','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARAIS']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARACR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARACR']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINSRRX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINSRRX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINSRIV','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINSRIV']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINSRCR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINSRCR']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINSRRI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINSRRI']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARRXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARRXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARIVA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARIVA']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARCRX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARCRX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARRIX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARRIX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIARRIS','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIARRIS']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIPCIXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIPCIXX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLINSOFE','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINSOFE']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLICPOSX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLICPOSX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLICPOS3','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLICPOS3']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIRESFI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIRESFI']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLITRIBU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLITRIBU']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'CLIPCESN','VALUE'=>"0000-00-00 00:00:00"                                                    ,'CHECK'=>'NO'),
                                  array('NAME'=>'CLIPCECN','VALUE'=>str_replace($cBuscarEmail,$cReemplEmail,trim(trim($xRD['CLIPCECN']),'"')),'CHECK'=>'NO','CS'=>'NONE'),
                                  array('NAME'=>'CLIMMERX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIMMERX']),'"')),'CHECK'=>'NO'),
                                  array('NAME'=>'REGMODXX','VALUE'=>date('Y-m-d')		                                                         ,'CHECK'=>'SI'),
                                  array('NAME'=>'REGHORXX','VALUE'=>date('H:i')	                                                             ,'CHECK'=>'SI'),
                                  array('NAME'=>'CLIIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim($xRD['CLIIDXXX'],'"'))            ,'CHECK'=>'WH'));

                switch ($cAlfa) {
                  case "DHLEXPRE": case "TEDHLEXPRE": case "DEDHLEXPRE":
                    $qUpdateDhl = array(array('NAME'=>'CLIIMPCS','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($cImpCash),'"')),'CHECK'=>'NO'),
                                        array('NAME'=>'CLIIMPCR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($cImpCred),'"')),'CHECK'=>'NO'),
                                        array('NAME'=>'CLIBANID','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIBANID']),'"')),'CHECK'=>'NO'),
                                        array('NAME'=>'CLITIPCU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLITIPCU']),'"')),'CHECK'=>'NO'),
                                        array('NAME'=>'CLINUMCU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLINUMCU']),'"')),'CHECK'=>'NO'),
                                        array('NAME'=>'CLIESTCU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(strtoupper($xRD['CLIESTCU']),'"')),'CHECK'=>'NO'));

                    $qUpdate = array_merge($qUpdate, $qUpdateDhl);
                  break;
                }

                if (f_MySql("UPDATE","SIAI0150",$qUpdate,$xConexion01,$cAlfa)) {
                  $nCanAct++;
                } else {
                  $nError = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['LINEAIDX']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Error al Actualizar Nit [{$xRD['CLIIDXXX']}].\n";
                }
              }
            }
          }

          if ($nSwitch == 0) {
            $cMsj = "Se Insertaron $nCanIns y Se Actualizaron $nCanAct Terceros.".(($nError == 1) ? "Se presentaron los siguientes errores en la ejecucion del proceso: ".$cMsj : "");  
          }
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
     * Se ejecuto por el proceso en backgroun
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
    
    $cTabla = "memcater".$cTabCar;
    $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
    $qNewTab .= "LINEAIDX INT(11) NOT NULL AUTO_INCREMENT,"; 																									//LINEA
    $qNewTab .= "CLIIDXXX varchar(20)  NOT NULL COMMENT \"NIT CC\","; 																					//NIT
    $qNewTab .= "TDIIDXXX varchar(2)   NOT NULL COMMENT \"Tipo Documento\","; 																	//CODIGO TIPO DE DOCUMENTO
    $qNewTab .= "CLITPERX varchar(20)  NOT NULL COMMENT \"Tipo de Persona\","; 																	//TIPO DE PERSONA (PUBLICA - JURIDICA - NATURAL)
    $qNewTab .= "CLINOMXX varchar(100) NOT NULL COMMENT \"Importador\","; 																			//RAZON SOCIAL
    $qNewTab .= "CLINOMCX varchar(200) NOT NULL COMMENT \"Nombre Comercial\","; 																//NOMBRE COMERCIAL
    $qNewTab .= "CLIAPE1X varchar(30)  NOT NULL COMMENT \"Primer Apellido\","; 																	//PRIMER APELLIDO
    $qNewTab .= "CLIAPE2X varchar(30)  NOT NULL COMMENT \"Segundo Apellido\","; 																//SEGUNDO APELLIDO
    $qNewTab .= "CLINOM1X varchar(30)  NOT NULL COMMENT \"Primer Nombre\","; 																		//PRIMER NOMBRE
    $qNewTab .= "CLINOM2X varchar(30)  NOT NULL COMMENT \"Otros Nombres\","; 																		//OTROS NOMBRES
    $qNewTab .= "PAIIDXXX varchar(10)  NOT NULL COMMENT \"Cod Pais\","; 																				//CODIGO PAIS DOMICILIO FISCAL
    $qNewTab .= "DEPIDXXX varchar(10)  NOT NULL COMMENT \"Cod Depto\","; 																				//CODIGO DEPARTAMENTO DOMICILIO FISCAL
    $qNewTab .= "CIUIDXXX varchar(10)  NOT NULL COMMENT \"Cod Ciudad\","; 																			//CODIGO CIUDAD DOMICILIO FISCAL
    $qNewTab .= "CLITELXX varchar(20)  NOT NULL COMMENT \"Telefono\","; 																				//TELEFONO DOMICILIO FISCAL
    $qNewTab .= "CLIDIRXX varchar(100) NOT NULL COMMENT \"Direccion\","; 																				//DIRECCION DOMICILIO FISCAL
    $qNewTab .= "CLIEMAXX varchar(150) NOT NULL COMMENT \"Email\","; 																						//CORREO ELECTRONICO
    $qNewTab .= "PAIID3XX varchar(10)  NOT NULL COMMENT \"Cod Pais Correspondencia\","; 												//CODIGO PAIS CORRESPONDENCIA
    $qNewTab .= "DEPID3XX varchar(10)  NOT NULL COMMENT \"Cod Depto Correspondencia\","; 												//CODIGO DEPARTAMENTO CORRESPONDENCIA
    $qNewTab .= "CIUID3XX varchar(10)  NOT NULL COMMENT \"Cod Ciudad Correspondencia\","; 											//CODIGO CIUDAD CORRESPONDENCIA
    $qNewTab .= "CLIDIR3X varchar(50)  NOT NULL COMMENT \"Direccion Correspondencia\","; 												//DIRECCION CORRESPONDENCIA
    $qNewTab .= "CLIPROCX varchar(2)   NOT NULL COMMENT \"Tercero de Tipo Proveedor Cliente\","; 								//PROVEEDOR-CLIENTE (SI - NO)
    $qNewTab .= "CLIPROEX varchar(2)   NOT NULL COMMENT \"Tercero de Tipo Proveedor de la Empresa\","; 					//PROVEEDOR-EMPRESA (SI - NO)
    $qNewTab .= "CLISOCXX varchar(2)   NOT NULL COMMENT \"Tercero de Tipo Proveedor Socio\",";									//PROVEEDOR-SOCIO (SI - NO)
    $qNewTab .= "CLIEFIXX varchar(2)   NOT NULL COMMENT \"Tercero de Tipo Proveedor de Entidad Financiera\","; 	//ENTIDAD FINANCIERA (SI - NO)
    $qNewTab .= "CLIOTRXX varchar(2)   NOT NULL COMMENT \"Tercero de Tipo Proveedor Otros\","; 									//PROVEEDOR-OTROS (SI - NO)
    $qNewTab .= "CLIEMPXX varchar(2)   NOT NULL COMMENT \"Tercero de Tipo Proveedor Empleado\","; 							//EMPLEADO (SI - NO)
    $qNewTab .= "CLIVENCO varchar(2)   NOT NULL COMMENT \"Tercero de Tipo Proveedor Vendedor\","; 							//VENDEDOR (SI - NO)
    $qNewTab .= "CLIVENXX varchar(200) NOT NULL COMMENT \"Vendedores Asignados\","; 	              						//VENDEDOR_ASIGNADO
    $qNewTab .= "CLIREIVA varchar(2)   NOT NULL COMMENT \"Responsable de IVA\",";
    $qNewTab .= "CLIRECOM varchar(2)   NOT NULL COMMENT \"Regimen Comun\","; 																		//RESPONSABLE IVA REGIMEN COMUN (SI - NO)
    $qNewTab .= "CLIRESIM varchar(2)   NOT NULL COMMENT \"Regimen Simplificado\","; 														//RESPONSABLE IVA REGIMEN SIMPLIFICADO (SI - NO)
    $qNewTab .= "CLIGCXXX varchar(2)   NOT NULL COMMENT \"Gran Contribuyente\","; 															//GRAN CONTRIBUYENTE (SI - NO)
    $qNewTab .= "CLIREGST varchar(2)   NOT NULL COMMENT \"Regimen Simple Tributario\","; 												//REGIMEN SIMPLE TRIBUTARIO (SI - NO)
    $qNewTab .= "CLINRPXX varchar(2)   NOT NULL COMMENT \"No Residente en el Pais\","; 													//NO RESIDENTE EN EL PAIS  (SI - NO)
    $qNewTab .= "CLINRPAI varchar(2)   NOT NULL COMMENT \"Aplica IVA No Residentes\","; 												//NO RESIDENTE EN EL PAIS - APLICA IVA (SI - NO)
    $qNewTab .= "CLINRPIF varchar(2)   NOT NULL COMMENT \"Aplica Gravamen Financiero No Residentes\","; 				//NO RESIDENTE EN EL PAIS - APLICA GMF (SI - NO)
    $qNewTab .= "CLINRNSR varchar(2)   NOT NULL COMMENT \"No Sujeto RETEFTE por Renta No Residentes\","; 				//NO RESIDENTE EN EL PAIS - NO SUJETO RETEFTE POR RENTA (SI - NO)
    $qNewTab .= "CLIARXXX varchar(2)   NOT NULL COMMENT \"Autoretenedor\",";
    $qNewTab .= "CLIARARE varchar(2)   NOT NULL COMMENT \"Autoretenedor de Renta\","; 													//AUTORETENEDOR EN RENTA (SI - NO)
    $qNewTab .= "CLIARAIV varchar(2)   NOT NULL COMMENT \"Autoretenedor de IVA\","; 														//AUTORETENEDOR DE IVA (SI - NO)
    $qNewTab .= "CLIARAIC varchar(2)   NOT NULL COMMENT \"Autoretenedor de ICA\","; 														//AUTORETENEDOR DE ICA (SI - NO)
    $qNewTab .= "CLIARAIS mediumtext   NOT NULL COMMENT \"Autoretenedor de ICA Sucrusales\","; 									//CODIGO SUCURSALES AUTORETENEDOR DE ICA (SEPARADAS POR COMA)
    $qNewTab .= "CLIARACR varchar(2)   NOT NULL COMMENT \"Autoretenedor de CREE\","; 														//AUTORETENEDOR DE CREE (SI - NO)
    $qNewTab .= "CLINSRRX varchar(2)   NOT NULL COMMENT \"No sujeto RETEFTE Renta\","; 													//NO SUJETO RETEFTE POR RENTA (SI - NO)
    $qNewTab .= "CLINSRIV varchar(2)   NOT NULL COMMENT \"No Sujeto RETEFTE por IVA\","; 												//NO SUJETO RETEFTE POR IVA (SI - NO)
    $qNewTab .= "CLINSRCR varchar(2)   NOT NULL COMMENT \"No Sujeto Retencion CREE\","; 												//NO SUJETO RETENCION CREE (SI - NO)
    $qNewTab .= "CLINSRRI varchar(2)   NOT NULL COMMENT \"No Sujeto de Retencion ICA\","; 											//NO SUJETO A RETENCION ICA (SI - NO)
    $qNewTab .= "CLIARRXX varchar(2)   NOT NULL COMMENT \"Agente Retenedor Renta\","; 													//AGENTE RETENEDOR EN RENTA (SI - NO)
    $qNewTab .= "CLIARIVA varchar(2)   NOT NULL COMMENT \"Agente Retenedor en IVA\","; 													//AGENTE RETENEDOR EN IVA (SI - NO)
    $qNewTab .= "CLIARCRX varchar(2)   NOT NULL COMMENT \"Agente Retenedor CREE\","; 														//AGENTE RETENEDOR CREE (SI - NO)
    $qNewTab .= "CLIARRIX varchar(2)   NOT NULL COMMENT \"Agente Retenedor ICA\","; 														//AGENTE RETENEDOR ICA (SI - NO)
    $qNewTab .= "CLIARRIS mediumtext   NOT NULL COMMENT \"Agente Retenedor ICA Sucrusales\","; 									//CODIGO SUCURSALES AGENTE RETENEDOR ICA (SEPARADAS POR COMA)
    $qNewTab .= "CLIPCIXX varchar(2)   NOT NULL COMMENT \"Proveedor Comercializadora Internacional\","; 				//PROVEEDOR COMERCIALIZADORA INTERNACIONAL(SI - NO)
    $qNewTab .= "CLINSOFE varchar(2)   NOT NULL COMMENT \"No Sujeto a Expedir Factura de Venta o Documento Equivalente\","; //NO RESIDENTE EN EL PAIS - NO SUJETO RETEFTE POR RENTA (SI - NO)
    $qNewTab .= "CLICPOSX varchar(10)  NOT NULL COMMENT \"Codigo Postal Domicilio Fiscal\","; 				          //CODIGO POSTAL DOMICILIO FISCAL
    $qNewTab .= "CLICPOS3 varchar(10)  NOT NULL COMMENT \"Codigo Postal Correspondencia\","; 				            //CODIGO POSTAL CORRESPONDENCIA
    $qNewTab .= "CLIRESFI text         NOT NULL COMMENT \"Responsabilidad Fiscal\","; 				                  //RESPONSABILIDAD FISCAL (SEPARADAS POR COMA)
    $qNewTab .= "CLITRIBU text         NOT NULL COMMENT \"Responsabilidad Tributo\","; 				                  //RESPONSABILIDAD TRIBUTO (SEPARADAS POR COMA)
    $qNewTab .= "CLIPCECN text         NOT NULL COMMENT \"Correos Notificacion\","; 				                    //CORREOS NOTIFICACION (SEPARADOS POR COMA)
    $qNewTab .= "CLIMMERX varchar(100) NOT NULL COMMENT \"Matricula Mercantil\","; 				                      //MATRICULA MERCANTIL
    if ((f_InList($cAlfa,"DHLEXPRE","TEDHLEXPRE","DEDHLEXPRE"))) {
      $qNewTab .= "CLIIMPCS text NOT NULL COMMENT \"Cuenta IMP Cash\","; 				                                //CUENTA IMP CASH
      $qNewTab .= "CLICASES text NOT NULL COMMENT \"Estado Cuenta IMP Cash\","; 				                        //CUENTA IMP CASH
      $qNewTab .= "CLIIMPCR text NOT NULL COMMENT \"Cuenta IMP Credito\","; 				                            //CUENTA IMP CREDITO
      $qNewTab .= "CLICREES text NOT NULL COMMENT \"Estado Cuenta IMP Credito\","; 				                      //CUENTA IMP CREDITO
      $qNewTab .= "CLIBANID varchar(3) NOT NULL COMMENT \"Id del Banco\","; 				                            //ID BANCO
      $qNewTab .= "CLIBANDE varchar(100) NOT NULL COMMENT \"Descripcion del Banco\","; 				                  //NOMBRE BANCO
      $qNewTab .= "CLITIPCU varchar(20) NOT NULL COMMENT \"Tipo de Cuenta\","; 				                          //TIPO CUENTA
      $qNewTab .= "CLINUMCU varchar(30) NOT NULL COMMENT \"Numero de Cuenta\","; 				                        //NUMERO DE CUENTA
      $qNewTab .= "CLIESTCU varchar(10) NOT NULL COMMENT \"Estado Cuenta\","; 				                          //ESTADO
    }

    $qNewTab .= " PRIMARY KEY (LINEAIDX)) ENGINE=MyISAM ";
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

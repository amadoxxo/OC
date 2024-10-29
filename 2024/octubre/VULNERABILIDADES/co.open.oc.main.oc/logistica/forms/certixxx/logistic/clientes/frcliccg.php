<?php
  namespace openComex;
//Estableciendo que el tiempo de ejecucion no se limite
set_time_limit (0);

define(_NUMREG_,1);

/**
* Graba Creacion y/o Actualizacion de clientes desde un txt delimintado por tabulaciones.
* --- Descripcion: Permite Subir y Creacion y/o Actualizacion de clientes desde un txt delimintado por tabulaciones.
* @author Daniel Monsalve <daniel.monsalve@openits.co>
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

    include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
    include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utility.php");
    include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php");
    include("{$OPENINIT['pathdr']}/opencomex/logistica/libs/php/uticclix.php");

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
  include("../../../../libs/php/uticclix.php");
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
  case "CARGACLIENTES":
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

        $nTotCol = 27;
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
      $cPost   = "kModo~".$_COOKIE['kModo']."|";
      
      $vParBg['pbadbxxx'] = $cAlfa;                                   //Base de Datos
      $vParBg['pbamodxx'] = "LOGISTICA";                              //Modulo
      $vParBg['pbatinxx'] = "CARGARCLIENTES";                         //Tipo Interface
      $vParBg['pbatinde'] = "CARGA DE CLIENTES";                      //Descripcion Tipo de Interfaz
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

          # Creando la instancia para procesar clientes
          $ObjcClientes = new cClientes();

          $qDatos = "SELECT * FROM $cAlfa.$cTabCar";
          $xDatos = mysql_query($qDatos,$xConexion01);
          // f_Mensaje(__FILE__,__LINE__,$qDatos."~".mysql_num_rows($xDatos));
          $nCanReg = 0;
          while ($xRD = mysql_fetch_assoc($xDatos)) {

            // Variable de control de errores por cliente
            $nError = 0;

            $nCanReg++;
            if (($nCanReg % _NUMREG_) == 0) {
              $xConexion01 = fnReiniciarConexion();
            }

            if ($xRD['cliidxxx'] != "") {
              //Eliminando caracteres de tabulacion, intelieado de los campos
              foreach ($xRD as $ckey => $cValue) {
                $xRD[$ckey] = trim(strtoupper(str_replace($vBuscar,$vReempl,$xRD[$ckey])));
              }

              // Identificando si se debe crear o editar el cliente
              $qCliCod  = "SELECT cliidxxx ";
              $qCliCod .= "FROM $cAlfa.lpar0150 ";
              $qCliCod .= "WHERE ";
              $qCliCod .= "cliidxxx = \"{$xRD['cliidxxx']}\" LIMIT 0,1";
              $xCliCod  = f_MySql("SELECT","",$qCliCod,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qCliCod."~".mysql_num_rows($xCliCod));

              // Armando Matriz de datos requeridos
              $pArrayDatos             = array();
              $pArrayDatos['cModo']    = (mysql_num_rows($xCliCod) > 0) ? "EDITAR" : "NUEVO"; // Modo de grabado
              $pArrayDatos['cOrigen']  = $kModo; //Origen
              $pArrayDatos['cTpeId']   = $xRD['clitperx'];  // Tipo de Persona
              $pArrayDatos['cTdiId']   = $xRD['tdiidxxx'];  // Id Tipo de Documento
              $pArrayDatos['cCliId']   = $xRD['cliidxxx'];  // No. Identificacion
              $pArrayDatos['cCliNom']  = $xRD['clinomxx'];  // Razon Social
              $pArrayDatos['cCliNomC'] = $xRD['clinomcx'];  // Nombre Comercial
              $pArrayDatos['cCliPApe'] = $xRD['cliape1x'];  // Primer Apellido
              $pArrayDatos['cCliSApe'] = $xRD['cliape2x'];  // Segundo Apellido
              $pArrayDatos['cCliPNom'] = $xRD['clinom1x'];  // Primer Nombre
              $pArrayDatos['cCliSNom'] = $xRD['clinom2x'];  // Segundo Nombre
              $pArrayDatos['vChCli']   = $xRD['cliclixx'];  // Tipo Cliente
              $pArrayDatos['vChUsu']   = $xRD['cliusuxx'];  // Tipo Usuario
              $pArrayDatos['vChDian']  = $xRD['clidianx'];  // Tipo Usuario DIAN
              $pArrayDatos['vChEmp']   = $xRD['cliempxx'];  // Tipo Empleado
              $pArrayDatos['vChCon']   = $xRD['cliconxx'];  // Tipo Contacto
              $pArrayDatos['vChOtr']   = $xRD['cliotrxx'];  // Tipo Otros
              $pArrayDatos['cPaiId']   = $xRD['paiidxxx'];  // Id Pais
              $pArrayDatos['cDepId']   = $xRD['depidxxx'];  // Id Departamento
              $pArrayDatos['cCiuId']   = $xRD['ciuidxxx'];  // Id Ciudad
              $pArrayDatos['cCliDir']  = $xRD['clidirxx'];  // Direccion Domicilio Fiscal
              $pArrayDatos['cCliCPos'] = $xRD['clicposx'];  // Codigo Postal
              $pArrayDatos['cCliTel']  = $xRD['clitelxx'];  // Telefono
              $pArrayDatos['cCliMov']  = $xRD['climovxx'];  // Telefono Movil
              $pArrayDatos['cCliEma']  = $xRD['cliemaxx'];  // Correo Facturacion Electronica
              $pArrayDatos['cCliApa']  = $xRD['cliapaxx'];  // Apartado Aereo
              $pArrayDatos['cCliSap']  = $xRD['clisapxx'];  // Codigo SAP
              $pArrayDatos['rCheckF']  = $xRD['cliprefa'];  // Requiere Prefactura
              $pArrayDatos['cCliObs']  = $xRD['cliobsxx'];  // Observacion

              # Validar Cliente
              $mRetVal  = $ObjcClientes->fnValidarCliente($pArrayDatos); //Se envian todos los datos que llegan por POST
              if ($mRetVal[0] == "false") {
                $nSwitch = 1;
                $nError  = 1;
                for ($nR=1; $nR<count($mRetVal); $nR++) {
                  $cMsj .= "Linea ".str_pad($xRD['lineaidx'],4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= $mRetVal[$nR]."\n";
                }
              }

              if ($nError == 0) {
                $mRetVal = $ObjcClientes->fnGuardarCliente($pArrayDatos); //Se envian todos los datos que llegan por POST
                if ($mRetVal[0] == "false") {
                  $nSwitch = 1;
                  $nError  = 1;
                  for ($nR=1; $nR<count($mRetVal); $nR++) {
                    $cMsj .= "Linea ".str_pad($xRD['lineaidx'],4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= $mRetVal[$nR]."\n";
                  }
                } else {
                  if ($pArrayDatos['cModo'] == "NUEVO" ) {
                    $nCanIns++;
                  }
                  if ($pArrayDatos['cModo'] == "EDITAR" ) {
                    $nCanAct++;
                  }
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
  
  $cTabla = "memcacli".$cTabCar;
  $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
  $qNewTab .= "lineaidx INT(11)      NOT NULL AUTO_INCREMENT,";                             //LINEA
  $qNewTab .= "cliidxxx varchar(20)  NOT NULL COMMENT \"Id Cliente\",";                     //NIT
  $qNewTab .= "tdiidxxx varchar(2)   NOT NULL COMMENT \"Tipo Documento\",";                 //CODIGO TIPO DE DOCUMENTO
  $qNewTab .= "clitperx varchar(20)  NOT NULL COMMENT \"Tipo de Persona\",";                //TIPO DE PERSONA (PUBLICA - JURIDICA - NATURAL)
  $qNewTab .= "clinomxx varchar(100) NOT NULL COMMENT \"Razon Social\",";                   //RAZON SOCIAL
  $qNewTab .= "clinomcx varchar(200) NOT NULL COMMENT \"Nombre Comercial\",";               //NOMBRE COMERCIAL
  $qNewTab .= "cliape1x varchar(30)  NOT NULL COMMENT \"Primer Apellido\",";                //PRIMER APELLIDO
  $qNewTab .= "cliape2x varchar(30)  NOT NULL COMMENT \"Segundo Apellido\",";               //SEGUNDO APELLIDO
  $qNewTab .= "clinom1x varchar(30)  NOT NULL COMMENT \"Primer Nombre\",";                  //PRIMER NOMBRE
  $qNewTab .= "clinom2x varchar(30)  NOT NULL COMMENT \"Otros Nombres\",";                  //OTROS NOMBRES
  $qNewTab .= "cliprefa varchar(2)   NOT NULL COMMENT \"Prefactura (SI/NO)\",";             //REQUIERE PREFACTURA (SI - NO)
  $qNewTab .= "paiidxxx varchar(10)  NOT NULL COMMENT \"Cod Pais\",";                       //CODIGO PAIS DOMICILIO FISCAL
  $qNewTab .= "depidxxx varchar(10)  NOT NULL COMMENT \"Cod Depto\",";                      //CODIGO DEPARTAMENTO DOMICILIO FISCAL
  $qNewTab .= "ciuidxxx varchar(10)  NOT NULL COMMENT \"Cod Ciudad\",";                     //CODIGO CIUDAD DOMICILIO FISCAL
  $qNewTab .= "clidirxx varchar(100) NOT NULL COMMENT \"Direccion Domicilio Fiscal\",";     //DIRECCION DOMICILIO FISCAL
  $qNewTab .= "clicposx varchar(10)  NOT NULL COMMENT\"Codigo Postal Domicilio Fiscal\",";  //CODIGO POSTAL
  $qNewTab .= "clitelxx varchar(20)  NOT NULL COMMENT \"Telefono\",";                       //TELEFONO
  $qNewTab .= "climovxx varchar(20)  NOT NULL COMMENT \"Movil\",";                          //TELEFONO MOVIL
  $qNewTab .= "cliemaxx varchar(150) NOT NULL COMMENT \"Correo Factura Electronica\",";     //CORREO FACTURACION ELECTRONICA
  $qNewTab .= "cliapaxx varchar(30)  NOT NULL COMMENT \"Apartado Aereo\",";                 //APARTADO AEREO
  $qNewTab .= "cliclixx varchar(2)   NOT NULL COMMENT \"Cliente (SI/NO)\",";                //CLIENTE (SI - NO)
  $qNewTab .= "cliusuxx varchar(2)   NOT NULL COMMENT \"Usuario (SI/NO)\",";                //USUARIO (SI - NO)
  $qNewTab .= "clidianx varchar(2)   NOT NULL COMMENT \"Usuario DIAN (SI/NO)\",";           //USUARIO DIAN (SI - NO)
  $qNewTab .= "cliempxx varchar(2)   NOT NULL COMMENT \"'Empleado (SI/NO)\",";              //EMPLEADO (SI - NO)
  $qNewTab .= "cliconxx varchar(2)   NOT NULL COMMENT \"Contacto (SI/NO)\",";               //CONTACTO (SI - NO)
  $qNewTab .= "cliotrxx varchar(2)   NOT NULL COMMENT \"Otro (SI/NO)\",";                   //OTRO (SI - NO)
  $qNewTab .= "clisapxx varchar(10)  NOT NULL COMMENT \"Codigo SAP\",";                     //CODIGO SAP
  $qNewTab .= "cliobsxx text         NOT NULL COMMENT \"Observaciones del Cliente\",";      //OBSERVACIONES
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

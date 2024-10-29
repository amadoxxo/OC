<?php
	//Estableciendo que el tiempo de ejecucion no se limite
	set_time_limit (0);

	/**
	* Graba Cargue Tarifas Condiciones desde un txt delimitado por tabulaciones.
	* --- Descripcion: Permite Subir y Cargue Tarifas Condiciones desde un txt delimitado por tabulaciones.
	* @author Camilo Dulce <camilo.dulce@open-eb.co>
	* @version 001
  */
  
  /**
   * Cantidad de Registros para reiniciar conexion
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
	$cMsj     = "\n";

	#Cadenas para reemplazar caracteres espciales
	$vBuscar = array(chr(13),chr(10),chr(27),chr(9));
	$vReempl = array(" "," "," "," ");

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

        if($_POST['cSerId'] == ""){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Concepto de Cobro, No Puede Ser Vacio.\n";
        }else{
          $qConCob  = "SELECT ";
          $qConCob .= "fcoidxxx,";
          $qConCob .= "regestxx ";
          $qConCob .= "FROM $cAlfa.fpar0129 ";
          $qConCob .= "WHERE ";
          $qConCob .= "seridxxx = \"{$_POST['cSerId']}\" LIMIT 0,1";
          $xConCob = f_MySql("SELECT", "", $qConCob, $xConexion01, "");
          if(mysql_num_rows($xConCob) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Concepto de Cobro[{$_POST['cSerId']}], No Existe En La Base de Datos.\n";
          }else{
            $vConCob = mysql_fetch_array($xConCob);
            if($vConCob['regestxx'] != "ACTIVO"){
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Concepto de Cobro[{$_POST['cSerId']}], Se Encuentra INACTIVO.\n";
            }
          }
        }

        if($_POST['cFcoId'] == ""){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La Forma de Cobro, No Puede Ser Vacia.\n";
        }else{
          // Validado que la Forma de Pago Aplique para el Concepto de Cobro
          $mMtzCon = explode("~",$vConCob['fcoidxxx']);
          $nEncontro = 0;
          if(in_array($_POST['cFcoId'], $mMtzCon)){
            $nEncontro = 1;
          }
          if ($nEncontro == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "La Forma de Cobro no Aplica para el Concepto de Cobro.\n";
          }

          // Valido que Exista la Forma de Cobro
          $qForCob  = "SELECT ";
          $qForCob .= "regestxx ";
          $qForCob .= "FROM $cAlfa.fpar0130 ";
          $qForCob .= "WHERE ";
          $qForCob .= "fcoidxxx = \"{$_POST['cFcoId']}\" LIMIT 0,1";
          $xForCob = f_MySql("SELECT", "", $qForCob, $xConexion01, "");
          if(mysql_num_rows($xForCob) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "La Forma de Cobro[{$_POST['cFcoId']}], No Existe En La Base de Datos.\n";
          }else{
            $vForCob = mysql_fetch_array($xForCob);
            if($vForCob['regestxx'] != "ACTIVO"){
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "La Forma de Cobro[{$_POST['cFcoId']}], Se Encuentra INACTIVA.\n";
            }
          }
        }

        if($_POST['cConEsp'] == ""){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La Condicion Especial, No Puede Ser Vacia.\n";
        }

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

          $nTotCol = 3;
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
        $cPost  .= "cSerId~".$_POST['cSerId']."|";
        $cPost  .= "cSerTop~".$_POST['cSerTop']."|";
        $cPost  .= "cFcoId~".$_POST['cFcoId']."|";
        $cPost  .= "cConEsp~".$_POST['cConEsp']."|";
        
        $vParBg['pbadbxxx'] = $cAlfa;                                   //Base de Datos
        $vParBg['pbamodxx'] = "FACTURACION";                            //Modulo
        $vParBg['pbatinxx'] = "CARGARCONDICIONESESPECIALES";                //Tipo Interface
        $vParBg['pbatinde'] = "CARGA DE CONDICIONES ESPECIALES DO";           //Descripcion Tipo de Interfaz
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

      if($cEjePro == 0){
        if($nSwitch == 0){
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

          if($nSwitch == 0) {
            $qDatos = "SELECT * FROM $cAlfa.$cTabCar";
            $xDatos = f_MySql("SELECT", "", $qDatos, $xConexion01, "");
            //f_Mensaje(__FILE__,__LINE__,$qDatos."~".mysql_num_rows($xDatos));
            $nCanReg = 0;
            while ($xRD = mysql_fetch_array($xDatos)) {
              $nSwitchErr = 0;
              $cMsj = "";
              
              //Actualizo el nombre del cliente
              $qCliDat  = "SELECT ";
              $qCliDat .= "IF(CLINOMXX != \"\",CLINOMXX,(TRIM(CONCAT(CLINOMXX,' ',CLINOM1X,' ',CLINOM2X,' ',CLIAPE1X,' ',CLIAPE2X)))) AS CLINOMXX ";
              $qCliDat .= "FROM $cAlfa.SIAI0150 ";
              $qCliDat .= "WHERE ";
              $qCliDat .= "CLIIDXXX = \"{$xRD['cliidxxx']}\" LIMIT 0,1";
              $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
              $vCliDat  = mysql_fetch_array($xCliDat);

              $qUpdate =  array(array('NAME'=>'clinomxx','VALUE'=>$vCliDat['CLINOMXX']  ,'CHECK'=>'NO'),
                                array('NAME'=>'lineaidx','VALUE'=>$xRD['lineaidx']      ,'CHECK'=>'WH'));
                    
              if (!f_MySql("UPDATE",$cTabCar,$qUpdate,$xConexion01,$cAlfa)) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "No se Pudo Actualizar El Nombre de la Agencia en la Tabla Temporal.  Comuniquese con openTecnologia S.A. \n";
              }

              // El nit debe ser obligatorio, que exista y que este en estado ACTIVO
              if($xRD['cliidxxx'] == ""){
                $nSwitchErr = 1;
                $cMsj .= "Linea ".str_pad(($xRD['lineaidx']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Nit, No Puede Ser Vacio.\n";
              }else{
                $qDatCli  = "SELECT ";
                $qDatCli .= "REGESTXX ";
                $qDatCli .= "FROM $cAlfa.SIAI0150 ";
                $qDatCli .= "WHERE ";
                $qDatCli .= "CLIIDXXX = \"{$xRD['cliidxxx']}\" LIMIT 0,1";
                $xDatCli  = f_MySql("SELECT", "", $qDatCli, $xConexion01, "");
                $vDatCli  = mysql_fetch_array($xDatCli);
                if(mysql_num_rows($xDatCli) == 0) {
                  $nSwitchErr = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['lineaidx']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "El Nit[{$xRD['cliidxxx']}], no Existe en la Base de Datos.\n";
                }else{
                  if($vDatCli['REGESTXX'] != "ACTIVO"){
                    $nSwitchErr = 1;
                    $cMsj .= "Linea ".str_pad(($xRD['lineaidx']+1),4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "El Nit[{$xRD['cliidxxx']}], se Encuentra INACTIVO.\n";
                  }
                }
              }

              //Consulto el Do del cliente y que este en estado ACTIVO
              $qDatDo  = "SELECT ";
              $qDatDo .= "sucidxxx,";
              $qDatDo .= "docidxxx,";
              $qDatDo .= "docsufxx,";
              $qDatDo .= "regestxx ";
              $qDatDo .= "FROM $cAlfa.sys00121 ";
              $qDatDo .= "WHERE ";
              $qDatDo .= "cliidxxx = \"{$xRD['cliidxxx']}\" LIMIT 0,1";
              $xDatDo  = f_MySql("SELECT", "", $qDatDo, $xConexion01, "");
              if(mysql_num_rows($xDatDo) == 0){
                $nSwitchErr = 1;
                $cMsj .= "Linea ".str_pad(($xRD['lineaidx']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "No Se Encontraron DOs Para el Nit[{$xRD['cliidxxx']}].\n";
              }else{
                $vDatDo  = mysql_fetch_array($xDatDo);
                if($vDatDo['regestxx'] != "ACTIVO"){
                  $nSwitchErr = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['lineaidx']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "El DO[{$vDatDo['sucidxxx']}-{$vDatDo['docidxxx']}-{$vDatDo['docsufxx']}], Para el Nit[{$xRD['cliidxxx']}] Debe Estar en Estado ACTIVO.\n";
                }
              }

              // La cantidad debe ser numerico y mayor a cero
              if(!is_numeric($xRD['cantidad']+0)){
                $nSwitchErr  = 1;
                $cMsj .= "Linea ".str_pad(($xRD['lineaidx']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "La Cantidad, Debe Ser Un Campo Numerico.\n";
              }else{
                if(($xRD['cantidad']+0) <= 0){
                  $nSwitchErr  = 1;
                  $cMsj .= "Linea ".str_pad(($xRD['lineaidx']+1),4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "La Cantidad, Debe Ser Mayor a Cero.\n";
                }
              }

              // Validando que no Exista el Mismo Cliente, Servicio, Forma de Cobro,  Tarifa y Sucursal en la Base de Datos en Estado ACTIVO
              $qTarifa  = "SELECT * ";
              $qTarifa .= "FROM $cAlfa.fpar0131 ";
              $qTarifa .= "WHERE ";
              $qTarifa .= "cliidxxx = \"{$xRD['cliidxxx']}\" AND ";
              $qTarifa .= "seridxxx = \"{$_POST['cSerId']}\" AND ";
              $qTarifa .=	"fcoidxxx = \"{$_POST['cFcoId']}\" AND ";
              $qTarifa .=	"fcotopxx = \"{$_POST['cSerTop']}\" AND ";
              $qTarifa .= "regestxx = \"ACTIVO\" LIMIT 0,1";
              $xTarifa  = f_MySql("SELECT","",$qTarifa,$xConexion01,"");
              //f_Mensaje(__FILE__,__LINE__,$qTarifa."~".mysql_num_rows($xTarifa));
              if (mysql_num_rows($xTarifa) == 0) {
                $nSwitchErr = 1;
                $cMsj .= "Linea ".str_pad(($xRD['lineaidx']+1),4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "No se Encuentra Parametrizada una Tarifa ACTIVA para el Cliente[{$xRD['cliidxxx']}], Concepto de Cobro[{$_POST['cSerId']}], Forma de Cobro[{$_POST['cFcoId']}].\n";
              }

              $nCanReg++;
              if($nSwitchErr == 0){
                if (($nCanReg % _NUMREG_) == 0) {$xConexion01 = fnReiniciarConexion($xConexion01);}

                // Actualizo Campo
                $qUpdate =  array(array('NAME'=>$_POST['cConEsp'],'VALUE'=>$xRD['cantidad']                                    ,'CHECK'=>'NO'),
                                  array('NAME'=>'sucidxxx','VALUE'=>$vDatDo['sucidxxx']                                        ,'CHECK'=>'WH'),
                                  array('NAME'=>'docidxxx','VALUE'=>$vDatDo['docidxxx']                                        ,'CHECK'=>'WH'),
                                  array('NAME'=>'docsufxx','VALUE'=>$vDatDo['docsufxx']                                        ,'CHECK'=>'WH'));
                      
                if (!f_MySql("UPDATE","sys00121",$qUpdate,$xConexion01,$cAlfa)) {
                  $nSwitchErr = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "No Se Pudo Actualizar El Do en Facturacion.  Comuniquese con openTecnologia S.A.\n";
                }else{
                  $vError['LINEAIDX'] = $xRD['lineaidx']; //Id
                  $vError['TABLAERR'] = $cTabCar; //Tabla errores
                  $vError['TIPOERRX'] = "EXITOSO"; //ERROR o EXITOSO
                  $vError['DESERROR'] = "Se Actualizo La Tarifa Especial Con Concepto de Cobro[{$_POST['cSerId']}], Forma de Cobro[{$_POST['cFcoId']}], para el DO[{$vDatDo['sucidxxx']}-{$vDatDo['docidxxx']}-{$vDatDo['docsufxx']}], Con el Nit[{$xRD['cliidxxx']}]";
                  fnGuardarError($vError);
                }
              }

              if($nSwitchErr == 1){
                $vError['LINEAIDX'] = $xRD['lineaidx']; //Id
                $vError['TABLAERR'] = $cTabCar; //Tabla errores
                $vError['TIPOERRX'] = "ERROR"; //ERROR o EXITOSO
                $vError['DESERROR'] = $cMsj;
                fnGuardarError($vError);
              }
            } ## while ($xRD = mysql_fetch_array($xDatos)) {

            // Genero el archivo excel con los errores o exitosos
            $vParametros['INTERFAZ'] = "EXCELRESULTADOS";
            $vParametros['TABLAERR'] = $cTabCar;
            $mReturnGenerarExcelResultados = fnGenerarExcelResultados($vParametros);
            if( $mReturnGenerarExcelResultados[0] == "true"){
              if($mReturnGenerarExcelResultados[1] != ""){
                $cNomArc .= $mReturnGenerarExcelResultados[1]."~";
              }
            }

            if ($_SERVER["SERVER_PORT"] != "") {
    					if($mReturnGenerarExcelResultados[0] == "true"){
    						if($mReturnGenerarExcelResultados[1] != ""){
                  ?>
    					    <script languaje = "javascript">
    					    	parent.fmpro2.location = 'frgendoc.php?cRuta=<?php echo $mReturnGenerarExcelResultados[1] ?>';
    					    </script>
    							<?php 
    						}
    			  	}
            }
          }
        }
      }
    break;
    case "EXCEL":
      $cNomFile = "CargueCondicionesEspeciales_".$_COOKIE['kUsrId']."_".date("YmdHis").".xls";
      $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;
      if (file_exists($cFile)){
        unlink($cFile);
      }
      
      $fOp = fopen($cFile,'a');
      
      $cCad01  = "NIT\t";
      $cCad01 .= "AGENCIA\t";
      $cCad01 .= "CANTIDAD\n";
      
      fwrite($fOp,$cCad01);
      fclose($fOp);
      
      if (file_exists($cFile)){
        // Obtener la ruta absoluta del archivo
        $cAbsolutePath = realpath($cFile);
        $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));
        
        if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
          chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));

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
  }//if ($_SERVER["SERVER_PORT"] == "") {

  if ($_SERVER["SERVER_PORT"] != "") {
    if ($nSwitch == 1){
      f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
    }else {
      if($_POST['cEjProBg'] != "SI"){
        f_Mensaje(__FILE__,__LINE__,"Se Realizo el Proceso con Exito, Por Favor Verificar Los Resultados en el Archivo Excel.");
      }
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
    
    $cTabla = "memcatex".$cTabCar;
    $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
    $qNewTab .= "lineaidx INT(11) NOT NULL AUTO_INCREMENT,";
    $qNewTab .= "cliidxxx varchar(12)  NOT NULL COMMENT \"Nit\",";
    $qNewTab .= "clinomxx varchar(100) NOT NULL COMMENT \"Agencia\",";
    $qNewTab .= "cantidad varchar(15)  NOT NULL COMMENT \"Cantidad\",";
    $qNewTab .= "tipoerrx varchar(20)  NOT NULL COMMENT \"Tipo Error\",";
    $qNewTab .= "deserror text         NOT NULL COMMENT \"Observacion\",";
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


  function fnGuardarError($pArrayParametros){

    global $cAlfa;
    /**
     * Hacer la conexion a la base de datos
     */
    $xConexion01 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);
    /**
     * Recibe como parametro un vector con los siguientes campos
     * $pArrayParametros['BASEDATO']  //BASE DE DATOS
     * $pArrayParametros['TABLAERR']  //TABLA ERROR
     * $pArrayParametros['TIPOERRX']  //TIPO DE ERROR
     * $pArrayParametros['DESERROR']  //DESCRIPCION DEL ERROR
     * $pArrayParametros['MOSTRARX']  //INDICA SI SE DEBE PINTAR O NO EL ERROR.  EN SI O VACIO SE PINTA.
     */
     
     /**
       * Variables para reemplazar caracteres especiales
       * @var array
       */
    $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
    $cReempl = array('\"',"\'"," "," "," "," ");
    if($pArrayParametros['TABLAERR'] != ""){
      // $qInsertError   = "INSERT INTO {$pArrayParametros['BASEDATO']}.{$pArrayParametros['TABLAERR']} ";
      // $qInsertError  .= "(TIPOERRX,DESERROR) ";
      // $qInsertError  .= "VALUES ";
      // $qInsertError  .= "(\"{$pArrayParametros['TIPOERRX']}\", ";
      // $qInsertError  .= "\"{$pArrayParametros['DESERROR']}\") ";

      $qInsertError   = "UPDATE $cAlfa.{$pArrayParametros['TABLAERR']} SET ";
      $qInsertError  .= "tipoerrx = \"{$pArrayParametros['TIPOERRX']}\",";
      $qInsertError  .= "deserror = \"{$pArrayParametros['DESERROR']}\" ";
      $qInsertError  .= "WHERE ";
      $qInsertError  .= "lineaidx = \"{$pArrayParametros['LINEAIDX']}\" ";
      $nQueryTimeStart = microtime(true); $xInsertError  = mysql_query($qInsertError,$xConexion01);
      $nQueryTime = (microtime(true) - $nQueryTimeStart); fnMysqlQueryInfo($xConexion01,$nQueryTime);
    }
  } ## function fnGuardarError($pParametros){##

  ## Metodo para capturar la informacion del motor de DB asosciada al query
  function fnMysqlQueryInfo($xConexion,$xQueryTime) {
      
    global $cSystemPath;
    
    $xMysqlInfo = mysql_info($xConexion);
  
    ereg("Changed: ([0-9]*)",$xMysqlInfo,$vChanged);
    ereg("Deleted: ([0-9]*)",$xMysqlInfo,$vDeleted);
    ereg("Duplicates: ([0-9]*)",$xMysqlInfo,$vDuplicates);
    ereg("Records: ([0-9]*)",$xMysqlInfo,$vRecords);
    ereg("Rows matched: ([0-9]*)",$xMysqlInfo,$vRows_matched);
    ereg("Skipped: ([0-9]*)",$xMysqlInfo,$vSkipped);
    ereg("Warnings: ([0-9]*)",$xMysqlInfo,$vWarnings);
  
    $cQueryInfo  = "|";
    $cQueryInfo .= "Changed~{$vChanged[1]}|";
    $cQueryInfo .= "Deleted~{$vDeleted[1]}|";
    $cQueryInfo .= "Duplicates~{$vDuplicates[1]}|";
    $cQueryInfo .= "Records~{$vRecords[1]}|";
    $cQueryInfo .= "Rows matched~{$vRows_matched[1]}|";
    $cQueryInfo .= "Skipped~{$vSkipped[1]}|";
    $cQueryInfo .= "Warnings~{$vWarnings[1]}|";
    $cQueryInfo .= "Affected Rows~".mysql_affected_rows($xConexion)."|";
    $cQueryInfo .= "Query Time~".number_format($xQueryTime,2)."|";
    $cQueryInfo .= "Error Number~".mysql_errno($xConexion)."|";
    $cQueryInfo .= "Error Description~".mysql_error($xConexion)."|";
    
    $copenComex  = "|";
    $copenComex .= "SIACOSIA~";
    $copenComex .= "$cSystemPath/opencomex/alerts/frtardma.php~";
    $copenComex .= "localhost~";
    $copenComex .= "localhost~";
    $copenComex .= "SIACOSIA~";
    $copenComex .= date("Y-m-d")."~";
    $copenComex .= date("H:i:s");
    $copenComex .= "|";
    $xopenComex = mysql_query("SET @opencomex = \"$copenComex\"",$xConexion);
    $xQueryInfo = mysql_query("SET @mysqlinfo = \"$cQueryInfo\"",$xConexion);
  } ## function f_Mysql_Query_Info($xConexion,$xQueryTime) {	


  /**
   * Metodo para Generar Archivo Excel de Resultados
   */	
  function fnGenerarExcelResultados($pParametros){
    
    global $xConexion01; global $cAlfa; global $cBeta; global $vSysStr; global $kUser; global $cSystemPath;
    
    /**
      * Recibe como parametro un vector que contiene las siguientes posiciones
      * $pParametros['INTERFAZ']		//INTERFACE PARA GENERAR EXCEL
      * $pParametros['TABLAERR']		//TABLA A PARTIR DE LA CUAL SE GENERA EL ARCHIVO EXCEL
      */
  
    /**
      * Variable para saber si hay o no errores de validacion.
      *
      * @var number
      */
    $nSwitch = 0;
    
    /**
      * Matriz para Retornar Valores
      */
    $mReturn = array();
      
    /**
      * Reservando Primera Posición para retorna true o false
      */
    $mReturn[0] = "";
    
    /**
     * Variables para reemplazar caracteres especiales
     * @var array
     */
    // $cBuscar = array("<",">","«","»","%",chr(13),chr(10),chr(27),chr(9));
    // $cReempl = array("&#60;","&#62;","&#171;","&#187;","&#37;"," "," "," "," ");

    $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
    $cReempl = array('\"',"\'"," "," "," "," ");
    
    /**
     * Validando que el Tipo de Interface no sea vacia
     */
    if($pParametros['INTERFAZ'] == ""){
      $nSwitch = 1;
      $mReturn[count($mReturn)] = "El Tipo de Interfaz para Generar Excel no Puede ser Vacia.";
    }
    
    if($nSwitch == 0){
      /**
       * Segun la Interface se genera el Archivo Excel
       */
      switch($pParametros['INTERFAZ']){
        case "EXCELRESULTADOS":
          $cFile01 = "RESULTADOSPROCESOS_".$kUser."_".date('YmdHis').".xls";
          $cFileDownload = "$cSystemPath/opencomex/".$vSysStr['system_download_directory']."/".$cFile01;
          $cF01 = fopen($cFileDownload,"a");
          
          /**
           * Cantidad de Registros en el excel de resultados
           * @var Number
           */
          $nRegistros = 0;
          
          /**
           * Consultando Tabla Temporal para Generar Excel con los Errores:	 
           */
          $qErrores  = "SELECT * ";
          $qErrores .= "FROM $cAlfa.{$pParametros['TABLAERR']} ";
          $xErrores  = f_MySql("SELECT","",$qErrores,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qErrores."~".mysql_num_rows($xErrores));
          if(mysql_num_rows($xErrores) > 0){
            $nRegistros++;
            $cData  = '<table border="1" cellpadding="0" cellspacing="0" style="width:800px"><tr>';
            $cData .= '<td colspan="6"><b>Se Realizaron Las Siguientes Acciones al Realizar el Proceso:</b></td>';
            $cData .= '</tr>';
            $cData .= '<tr>';
            $cData .= '<td bgcolor = "#D6DFF7" style="width:50px"><center>Linea</center></td>'; 
            $cData .= '<td bgcolor = "#D6DFF7"><center>Nit</center></td>'; 
            $cData .= '<td bgcolor = "#D6DFF7"><center>Agencia</center></td>'; 
            $cData .= '<td bgcolor = "#D6DFF7"><center>Cantidad</center></td>'; 
            $cData .= '<td bgcolor = "#D6DFF7"><center>Accion</center></td>'; 
            $cData .= '<td bgcolor = "#D6DFF7"><center>Observacion</center></td>'; 
            $cData .= '</tr>';
            fwrite($cF01,$cData);
            
            while($xRE = mysql_fetch_array($xErrores)){
              $xRE['deserror'] = str_replace("\n", "<br>",$xRE['deserror']);

              $cData  = '<tr>';
              $cData .= '<td><center>'.($xRE['lineaidx']+1).'</center></td>';
              $cData .= '<td style="mso-number-format:\'\@\'">'.$xRE['cliidxxx'].'</td>';
              $cData .= '<td style="mso-number-format:\'\@\'">'.$xRE['clinomxx'].'</td>';
              $cData .= '<td style="mso-number-format:\'\@\'"><center>'.$xRE['cantidad'].'</center></td>';
              $cData .= '<td style="mso-number-format:\'\@\'">'.$xRE['tipoerrx'].'</td>';
              $cData .= '<td style="mso-number-format:\'\@\'">'.$xRE['deserror'].'</td>';
              $cData .= '</tr>';
              fwrite($cF01,$cData);
            }
            $cData  = '</table><br>';
            fwrite($cF01,$cData);
          }
          
          fclose($cF01);
          
          if ($nRegistros == 0) {
            /**
             * No se e encontro ningun registro para crear el excel
             */
            $cFile01 = "";
            $cFile02 = "";
          }						
        break;
      }
    }

    if($nSwitch == 0){
      $mReturn[0] = "true";	$mReturn[1] = $cFile01;	$mReturn[2] = $cFile02;
    }else{
      $mReturn[0] = "false";
    }
    
    return $mReturn;
  } ##function fnGenerarExcelResultados($pParametros){##
  
  ?>

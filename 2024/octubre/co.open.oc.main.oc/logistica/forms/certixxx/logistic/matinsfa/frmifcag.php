<?php
  namespace openComex;
  /**
   * Graba para Cargue Masivo de Movimiento.
   * --- Descripcion: Permite la Creacion y/o Actualizacion de los Movimientos de la M.I.F desde un txt delimintado por tabulaciones.
	 * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
	 * @package openComex
   * @version 001
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");
  // error_reporting(E_ALL);

  //Estableciendo que el tiempo de ejecucion no se limite
  set_time_limit(0);
  ini_set("memory_limit","512M");
	date_default_timezone_set('America/Bogota');
 
  define(_NUMREG_,100);

  include("../../../../../financiero/libs/php/utility.php");
  include("../../../../../config/config.php");

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

  $cSystemPath   = OC_DOCUMENTROOT;
  $nSwitch       = 0;   // Switch para Verificar la Validacion de Datos
  $nSwitchManual = 0;   // Switch para Verificar la Validacion del Origen de la MIF
  $cMsj          = "\n";
  $cAnio         = $_POST['cAnio'];

  // Cadenas para reemplazar caracteres espciales
  $vBuscar = array('"',chr(13),chr(10),chr(27),chr(9));
  $vReempl = array('\"'," "," "," "," ");

  // Validando que haya seleccionado un archivo
  if ($_FILES['cArcPla']['name'] == "") {
    $nSwitch = 1;
    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
    $cMsj .= "Debe Seleccionar un Archivo.\n";
  } else {
    // Copiando el archivo a la carpeta de downloads
    $cNomFile = "/carguemovimientomif_".$kUser."_".date("YmdHis").".txt";
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

  switch ($_COOKIE['kModo']) {
    case "CARGAR":
      // Validando los estados de la MIF
      $vMatriz  = array();
      $qMatriz  = "SELECT ";
      $qMatriz .= "mifidxxx, ";
      $qMatriz .= "miforixx, ";
      $qMatriz .= "regestxx ";
      $qMatriz .= "FROM $cAlfa.lmca$cAnio ";
      $qMatriz .= "WHERE ";
      $qMatriz .= "mifidxxx = \"{$_POST['cMifId']}\" LIMIT 0,1";
      $xMatriz  = f_MySql("SELECT","",$qMatriz,$xConexion01,"");
      if (mysql_num_rows($xMatriz) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La M.I.F seleccionada no Existe.\n";
      } else {
        $vMatriz = mysql_fetch_array($xMatriz);

        if ($vMatriz['regestxx'] != "ENPROCESO") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El estado de la M.I.F debe ser ENPROCESO.\n";
        }
      }

      // Creando tabla temporal
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
      // Fin Creando tabla temporal

      // Cargando Archivo a la tabla temporal
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

        // Se lee el archivo TXT y se organiza la data en un array para posterior hacer el Insert
        $archivo    = fopen($cFile, 'r');
        $mRegistros = array();
        $nCount     = 0;
        while ($linea = fgets($archivo)) {
          $mRegistros[$nCount] = explode("\t", $linea);
          $nCount++;
        }

        // Se obtinen los subservicios
        $mDatos = array();
        for ($i=0; $i < count($mRegistros[0]) ; $i++) {
          if ($i != 0) {
            $vSubId = explode("-", $mRegistros[0][$i]);
            $mDatos[$i]['subid'] = $vSubId[0];
          }
        }

        // Se organiza la data por Subservicios de la siguiente manera:
        // [0] => Array
        // (
        //    [subid] => 001 
        //    [fecha] => 2023-07-11
        //    [cantidad] => 400
        // )
        //
        $mDataFinal = array();
        for ($j=1; $j <= count($mDatos); $j++) { 
          for ($i=0; $i < count($mRegistros); $i++) {
            if ($i != 0) {
              $nInd_mDataFinal = count($mDataFinal);
              $mDataFinal[$nInd_mDataFinal]['subid']    = $mDatos[$j]['subid'];
              $mDataFinal[$nInd_mDataFinal]['fecha']    = $mRegistros[$i][0];
              $mDataFinal[$nInd_mDataFinal]['cantidad'] = $mRegistros[$i][$j];
            }
          }
        }

        // Cargando Informacion del Archivo a tabla temporal
        $qInsCab  = "INSERT INTO $cAlfa.$cTabCar (".$cFields.") VALUES ";
        $qInsert  = "";
        $nCantReg = 0;

        for ($i=0; $i < count($mDataFinal); $i++) { 
          $cFecha    = date("Y-m-d", strtotime(str_replace("/", "-", $mDataFinal[$i]['fecha'])));
          // $nCantidad = ($mDataFinal[$i]['cantidad'] != "") ? str_replace(",", ".", $mDataFinal[$i]['cantidad']) : NULL;
          $nCantidad = rtrim(ltrim($mDataFinal[$i]['cantidad'], '"'), '"');
          $nCantidad = (double)filter_var(str_replace(",",".",$nCantidad), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

          $qInsert .= "(";
          $qInsert .= "\"{$_POST['cMifId']}\",";
          $qInsert .= "\"{$mDataFinal[$i]['subid']}\",";
          $qInsert .= "\"$cFecha\",";
          $qInsert .= "\"{$nCantidad}\"";
          $qInsert .= "),";

          $nCantReg++;
          if (($nCantReg % _NUMREG_) == 0) { 
            $xConexion01 = fnReiniciarConexion();

            // Organizo el Insert e ejecuto el script
            $qInsert = substr($qInsert, 0, -1);
            $qInsert = $qInsCab.$qInsert;
            $xInsert = mysql_query($qInsert,$xConexion01);
            if (!$xInsert) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Error al Cargar los Datos en la Tabla Temporal. Favor Comunicarse con OpenTecnologia S.A.\n";
            }
            $qInsert = "";
          }
        }

        if ($qInsert != "") {
          $xConexion01 = fnReiniciarConexion();

          // Organizo el Insert e ejecuto el script
          $qInsert = substr($qInsert, 0, -1);
          $qInsert = $qInsCab.$qInsert;
          $xInsert = mysql_query($qInsert,$xConexion01);
          if (!$xInsert) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al Cargar los Datos en la Tabla Temporal. Favor Comunicarse con OpenTecnologia S.A.\n";
          }
          $qInsert = "";
        }
        // Fin Cargando Informacion del Archivo a tabla temporal
      }

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
          // Consulta a la tabla temporal
          $qDatos = "SELECT * FROM $cAlfa.$cTabCar";
          $xDatos = mysql_query($qDatos,$xConexion01);
          // f_Mensaje(__FILE__,__LINE__,$qDatos."~".mysql_num_rows($xDatos));
          $nCanReg = 0;

          while ($xRDE = mysql_fetch_array($xDatos)) {
            $nCanReg++;
            if (($nCanReg % _NUMREG_) == 0) {
              $xConexion01 = fnReiniciarConexion();
            }

            $xRDE['mifdcanx'] = ($xRDE['mifdcanx'] == 0) ? "" : $xRDE['mifdcanx'];
            if ($xRDE['mifidxxx'] != "" && $xRDE['subidxxx'] != "") {
              //Eliminando caracteres de tabulacion, intelieado de los campos
              foreach ($xRDE as $ckey => $cValue) {
                $xRDE[$ckey] = trim(str_replace($vBuscar,$vReempl,$xRDE[$ckey]));
              }

              // Cionsulta el subservicio en la MIF
              $qMifSubser  = "SELECT  ";
              $qMifSubser .= "mifdidxx, ";
              $qMifSubser .= "IF(mifdcanx > 0, mifdcanx, \"\") AS mifdcanx, ";
              $qMifSubser .= "mifdmodx ";
              $qMifSubser .= "FROM $cAlfa.lmsu$cAnio ";
              $qMifSubser .= "WHERE ";
              $qMifSubser .= "mifidxxx = \"{$xRDE['mifidxxx']}\" AND ";
              $qMifSubser .= "subidxxx = \"{$xRDE['subidxxx']}\" AND ";
              $qMifSubser .= "mifdfecx = \"{$xRDE['mifdfecx']}\" LIMIT 0,1";
              $xMifSubser  = f_MySql("SELECT","",$qMifSubser,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qMifSubser."~".mysql_num_rows($xMifSubser));
              if (mysql_num_rows($xMifSubser) == 0) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "No existe el registro para la MIF con Subservicio [".$xRDE['subidxxx']."] y Fecha [".$xRDE['mifdfecx']."].\n";
              } else {
                $vMifSubser = mysql_fetch_array($xMifSubser);

                // Valida si se puede modificar la cantidad
                if ($vMifSubser['mifdmodx'] != "SI" && $vMifSubser['mifdcanx'] != "" && $vMifSubser['mifdcanx'] != $xRDE['mifdcanx']) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Para la Fecha [".$xRDE['mifdfecx']."], Subservicio [".$xRDE['subidxxx']."] ya se encuentra una cantidad digitada.\n";
                }

                if ($vMifSubser['mifdmodx'] != "SI" && $xRDE['mifdcanx'] != "" && $vMifSubser['mifdcanx'] == "") {
                  $dFecInicial = strtotime($xRDE['mifdfecx']);
                  $dFecFinal   = strtotime(date("Y-m-d"));
                  $nDiffDias   = floor(($dFecFinal - $dFecInicial) / (60 * 60 * 24));
      
                  if ($nDiffDias > 7) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "La Fecha [".$xRDE['mifdfecx']."], para el Subservicio [".$xRDE['subidxxx']."] excede en 7 dias a la fecha actual del sistema. Por favor verifique o contacte al administrador.\n";
                  }
                }

                if ($xRDE['mifdcanx'] != "" && $xRDE['mifdcanx'] <= 0) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "La cantidad debe ser mayor a cero para la Fecha [".$xRDE['mifdfecx']."] y Subservicio [".$xRDE['subidxxx']."].\n";
                }
              }

              if ($nSwitch == 0) {
                if (($vMifSubser['mifdcanx'] == "" && $xRDE['mifdcanx'] != "") || ($vMifSubser['mifdmodx'] == "SI" && $xRDE['mifdcanx'] != "")) {
                  // Actualiza la informacion de detalle de los subservicios
                  $qUpdate = array(array('NAME' => 'mifdcanx','VALUE' => $xRDE['mifdcanx']       ,'CHECK' => 'SI'),
                                   array('NAME' => 'mifdmodx','VALUE' => NULL                    ,'CHECK' => 'NO'),
                                   array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')           ,'CHECK' => 'SI'),
                                   array('NAME' => 'reghmodx','VALUE' => date('H:i:s')           ,'CHECK' => 'SI'),
                                   array('NAME' => 'mifdidxx','VALUE' => $vMifSubser['mifdidxx'] ,'CHECK' => 'WH'));

                  if (!f_MySql("UPDATE","lmsu$cAnio",$qUpdate,$xConexion01,$cAlfa)) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "Error al Actualizar Datos de Detalle de los Subservicios.\n";
                  }
                }
              }
            }
          }

          if ($nSwitch == 0) {
            if ($vMatriz['miforixx'] == "") {
              // Actualiza la informacion de cabecera de la MIF
              $qUpdate = array(array('NAME' => 'miforixx','VALUE' => "MANUAL"               ,'CHECK' => 'SI'),
                               array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')          ,'CHECK' => 'SI'),
                               array('NAME' => 'reghmodx','VALUE' => date('H:i:s')          ,'CHECK' => 'SI'),
                               array('NAME' => 'mifidxxx','VALUE' => trim($_POST['cMifId']) ,'CHECK' => 'WH'));
    
              if (!f_MySql("UPDATE","lmca$cAnio",$qUpdate,$xConexion01,$cAlfa)) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Error al Actualizar Datos de Cabecera de la MIF.\n";
              }
            }
          }
        }
      }
    break;
    case "CARGARREP":
      // Validando que haya seleccionado el subervicio
      if($_POST['cSubservicio'] == ""){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Debe Seleccionar un Subservicio.\n";
      }

      // Validando los estados de la MIF
      $vMatriz  = array();
      $qMatriz  = "SELECT ";
      $qMatriz .= "mifidxxx, ";
      $qMatriz .= "miforixx, ";
      $qMatriz .= "miffdexx, ";
      $qMatriz .= "miffhaxx, ";
      $qMatriz .= "regestxx ";
      $qMatriz .= "FROM $cAlfa.lmca$cAnio ";
      $qMatriz .= "WHERE ";
      $qMatriz .= "mifidxxx = \"{$_POST['cMifId']}\" LIMIT 0,1";
      $xMatriz  = f_MySql("SELECT","",$qMatriz,$xConexion01,"");
      if (mysql_num_rows($xMatriz) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La M.I.F seleccionada no Existe.\n";
      } else {
        $vMatriz = mysql_fetch_array($xMatriz);

        if ($vMatriz['regestxx'] != "ENPROCESO") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El estado de la M.I.F debe ser ENPROCESO.\n";
        }
      }

      // Creando tabla temporal
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
      // Fin Creando tabla temporal

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

        // Se lee el archivo TXT y se organiza la data en un array para posterior hacer el Insert
        $archivo    = fopen($cFile, 'r');
        $mRegistros = array();
        $nCount     = 0;
        while ($linea = fgets($archivo)) {
          $mRegistros[$nCount] = explode("\t", $linea);
          $nCount++;
        }

        $mDataFinal  = array();
        $dFechaDesde = "";
        $dFechaHasta = "";
        for ($i=0; $i < count($mRegistros); $i++) {
          // Se obtiene el renago de fechas
          if ($mRegistros[9][25]) {
            $dFechaDesde = date("Y-m-d", strtotime(str_replace("/", "-", $mRegistros[9][25])));
          }
          if ($mRegistros[11][25]) {
            $dFechaHasta = date("Y-m-d", strtotime(str_replace("/", "-", $mRegistros[11][25])));
          }

          // Se obtienen los valores por fecha
          if ($i >= 19 && $mRegistros[$i][2] != "" && $mRegistros[$i][2] != "Totales") {
            $nInd_mDataFinal = count($mDataFinal);
            $mDataFinal[$nInd_mDataFinal] = $mRegistros[$i];
          }
        }

        // Cargando Informacion del Archivo a la tabla temporal
        $qInsCab  = "INSERT INTO $cAlfa.$cTabCar (".$cFields.") VALUES ";
        $qInsert  = "";
        $nCantReg = 0;

        for ($i=0; $i < count($mDataFinal); $i++) { 
          $cFecha    = date("Y-m-d", strtotime(str_replace("/", "-", $mDataFinal[$i][2])));
          $nCantidad = rtrim(ltrim($mDataFinal[$i][18], '"'), '"');
          $nCantidad = (double)filter_var(str_replace(",",".",$nCantidad), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

          $qInsert .= "(";
          $qInsert .= "\"{$_POST['cMifId']}\",";
          $qInsert .= "\"{$_POST['cSubservicio']}\",";
          $qInsert .= "\"$cFecha\",";
          $qInsert .= "\"{$nCantidad}\"";
          $qInsert .= "),";

          $nCantReg++;
          if (($nCantReg % _NUMREG_) == 0) { 
            $xConexion01 = fnReiniciarConexion();

            // Organizo el Insert e ejecuto el script
            $qInsert = substr($qInsert, 0, -1);
            $qInsert = $qInsCab.$qInsert;
            $xInsert = mysql_query($qInsert,$xConexion01);
            if (!$xInsert) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Error al Cargar los Datos en la Tabla Temporal. Favor Comunicarse con OpenTecnologia S.A.\n";
            }
            $qInsert = "";
          }
        }

        if ($qInsert != "") {
          $xConexion01 = fnReiniciarConexion();

          // Organizo el Insert e ejecuto el script
          $qInsert = substr($qInsert, 0, -1);
          $qInsert = $qInsCab.$qInsert;
          $xInsert = mysql_query($qInsert,$xConexion01);
          if (!$xInsert) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al Cargar los Datos en la Tabla Temporal. Favor Comunicarse con OpenTecnologia S.A.\n";
          }
          $qInsert = "";
        }
        // Fin Cargando Informacion del Archivo a tabla temporal

        // Valida que el rango de fechas corresponda con el de la MIF
        if ($dFechaDesde != $vMatriz['miffdexx'] || $dFechaHasta != $vMatriz['miffhaxx']) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Las fechas del reporte no coinciden con la vigencia de la matriz de Insumos Facturables.\n";
        }
      }

      if ($nSwitch == 0) {
        // Calculando cantidad de registros en la tabla
        $qDatos  = "SELECT SQL_CALC_FOUND_ROWS * ";
        $qDatos .= "FROM $cAlfa.$cTabCar LIMIT 0,1";
        $cIdCountRow = mt_rand(1000000000, 9999999999);
        $xDatos = mysql_query($qDatos, $xConexion01, true, $cIdCountRow);
        mysql_free_result($xDatos);

        $xNumRows = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD",$xConexion01);
        $xRNR     = mysql_fetch_array($xNumRows);
        $nCanReg  = $xRNR['CANTIDAD'];
        mysql_free_result($xNumRows);

        if ($nCanReg == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "No Se Encontraron Registros.\n";
        }

        // Consulta a la tabla temporal
        $qDatos = "SELECT * FROM $cAlfa.$cTabCar";
        $xDatos = mysql_query($qDatos,$xConexion01);
        $nCanReg = 0;

        while ($xRDE = mysql_fetch_array($xDatos)) {
          $nCanReg++;
          if (($nCanReg % _NUMREG_) == 0) {
            $xConexion01 = fnReiniciarConexion();
          }

          if ($xRDE['mifidxxx'] != "" && $xRDE['subidxxx'] != "") {
            //Eliminando caracteres de tabulacion, intelieado de los campos
            foreach ($xRDE as $ckey => $cValue) {
              $xRDE[$ckey] = trim(str_replace($vBuscar,$vReempl,$xRDE[$ckey]));
            }
            
            if ($xRDE['mifdcanx'] <= 0 || $xRDE['mifdcanx'] == "") {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "La Cantidad debe ser mayor a cero para la Fecha [".$xRDE['mifdfecx']."].\n";
            }

            // Consulta el subservicio en la MIF
            $qMifSubser  = "SELECT  ";
            $qMifSubser .= "mifdidxx, ";
            $qMifSubser .= "IF(mifdcanx > 0, mifdcanx, \"\") AS mifdcanx, ";
            $qMifSubser .= "mifdmodx ";
            $qMifSubser .= "FROM $cAlfa.lmsu$cAnio ";
            $qMifSubser .= "WHERE ";
            $qMifSubser .= "mifidxxx = \"{$xRDE['mifidxxx']}\" AND ";
            $qMifSubser .= "subidxxx = \"{$xRDE['subidxxx']}\" AND ";
            $qMifSubser .= "mifdfecx = \"{$xRDE['mifdfecx']}\" LIMIT 0,1";
            $xMifSubser  = f_MySql("SELECT","",$qMifSubser,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qMifSubser."~".mysql_num_rows($xMifSubser));
            if (mysql_num_rows($xMifSubser) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "No existe el registro para la MIF con Subservicio [".$xRDE['subidxxx']."] y Fecha [".$xRDE['mifdfecx']."].\n";
            } else {
              $vMifSubser = mysql_fetch_array($xMifSubser);

              if ($vMifSubser['mifdcanx'] != "") {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "El Movimiento de la M.I.F ya fue cargado.\n";
                break;
              }
            }
          }
        }
      }

      // Actualiza las cantidades en la MIF - Subservicios
      if ($nSwitch == 0) {
        // Consulta a la tabla temporal
        $qDatos = "SELECT * FROM $cAlfa.$cTabCar";
        $xDatos = mysql_query($qDatos,$xConexion01);
        $nCanReg = 0;

        while ($xRDE = mysql_fetch_array($xDatos)) {
          $nCanReg++;
          if (($nCanReg % _NUMREG_) == 0) {
            $xConexion01 = fnReiniciarConexion();
          }

          if ($xRDE['mifidxxx'] != "" && $xRDE['subidxxx'] != "") {
            //Eliminando caracteres de tabulacion, intelieado de los campos
            foreach ($xRDE as $ckey => $cValue) {
              $xRDE[$ckey] = trim(str_replace($vBuscar,$vReempl,$xRDE[$ckey]));
            }

            // Actualiza la informacion de detalle de los subservicios
            $qUpdate = array(array('NAME' => 'mifdcanx','VALUE' => $xRDE['mifdcanx']       ,'CHECK' => 'SI'),
                             array('NAME' => 'mifdmodx','VALUE' => NULL                    ,'CHECK' => 'NO'),
                             array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')           ,'CHECK' => 'SI'),
                             array('NAME' => 'reghmodx','VALUE' => date('H:i:s')           ,'CHECK' => 'SI'),
                             array('NAME' => 'mifidxxx','VALUE' => $xRDE['mifidxxx']       ,'CHECK' => 'WH'),
                             array('NAME' => 'subidxxx','VALUE' => $xRDE['subidxxx']       ,'CHECK' => 'WH'),
                             array('NAME' => 'mifdfecx','VALUE' => $xRDE['mifdfecx']       ,'CHECK' => 'WH'));

            if (!f_MySql("UPDATE","lmsu$cAnio",$qUpdate,$xConexion01,$cAlfa)) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Error al Actualizar Datos de Detalle de los Subservicios.\n";
            }
          }
        }

        // Actualiza el origen en la MIF
        $qUpdate = array(array('NAME' => 'miforixx','VALUE' => "REPORTE"              ,'CHECK' => 'SI'),
                         array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')          ,'CHECK' => 'SI'),
                         array('NAME' => 'reghmodx','VALUE' => date('H:i:s')          ,'CHECK' => 'SI'),
                         array('NAME' => 'mifidxxx','VALUE' => trim($_POST['cMifId']) ,'CHECK' => 'WH'));

        if (!f_MySql("UPDATE","lmca$cAnio",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Actualizar Datos de Cabecera de la MIF.\n";
        }
      }
    break;
    default:
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Modo de Grabado Viene Vacio.\n";
    break;
  }

  if ($nSwitch == 1){
    if (strlen($cMsj) > 300) {
      f_Mensaje(__FILE__,__LINE__,"Se presentaron errores en la ejecucion del proceso, \nverifique el Excel.");
      fnDescargarLogErrores($cMsj);
    } else {
      f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
    }
  } else { 
    f_Mensaje(__FILE__,__LINE__,"Se Actualizaron los Registros con Exito.");
    ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      document.forms['frgrm'].submit()
    </script>
    <?php
  }

  /**
   * Permite crear la tabla temporal.
   */
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
    
    $cTabla   = "memmomif".$cTabCar;
    $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
    $qNewTab .= "lineaidx INT(11) NOT NULL AUTO_INCREMENT,";                  //LINEA
    $qNewTab .= "mifidxxx INT(10) NOT NULL COMMENT \"Id MIF\",";              //ID DE LA MIF
    $qNewTab .= "subidxxx varchar(10) NOT NULL COMMENT \"Id Subservicio\",";  //ID DEL SUBSERVICIO
    $qNewTab .= "mifdfecx date NOT NULL COMMENT \"Fecha\",";                  //FECHA
    $qNewTab .= "mifdcanx decimal (18,5) NULL COMMENT \"Cantidad\",";         //CANTIDAD
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

  /**
   * Permite generear el Excel con el Log de errores.
   */
  function fnDescargarLogErrores($cErrores) {
    global $vSysStr; global $cAlfa;

    $cNomFile = "RESULTADOS_CARGUE_REPORTE_".$_COOKIE['kUsrId']."_".date("YmdHis").".xls";
    $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/".$cNomFile;
    if (file_exists($cFile)){
      unlink($cFile);
    }

    $fOp = fopen($cFile,'a');

    $cDataCab  = "Se presentaron los siguientes errores: \t";
    $cDataCab .= "\n";

    $cDataDet = "";
    $mErrores = explode("\n", $cErrores);
    for ($i=0; $i < count($mErrores); $i++) { 
      if ($mErrores[$i] != "") {
        $cDataDet .= $mErrores[$i]."\t";
        $cDataDet .= "\n";
      }
    }

    fwrite($fOp,$cDataCab);
    fwrite($fOp,$cDataDet);
    fclose($fOp);

    if (file_exists($cFile)){
      ?>
      <script languaje = "javascript">
        parent.fmpro.location = 'frgendoc.php?cRuta=<?php echo $cNomFile ?>';
      </script>
      <?php
    } else {
      f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
    }
  }


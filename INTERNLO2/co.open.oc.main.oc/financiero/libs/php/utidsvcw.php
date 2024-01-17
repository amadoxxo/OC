<?php
  /**
   * utidsvcw.php : Utility de Clases de Integracion con Cargo Wise DSV.
   *
   * Este script contiene la colecciones de clases para la Integracion Cargo Wise  DSV
   *
   * @package openComex
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  define("_NUMREG_",100);

  class cIntegracionCargoWiseDsv {
    /**
     * Metodo para la transmision de los comprobantes de Causaciones a Cargo Wise por FTP.
     */
    function fnCausaciones($pvDatos){
      /**
       * Recibe una Matriz con las siguientes posiciones:
       * $pvDatos['basedato'] //Base de datos
       * $pvDatos['tablaerr'] //Tabla Error
       * $pvDatos['tablamov'] //Tabla Movimiento
       * $pvDatos['origenxx'] //Origen (FTP o Vacio), FTP indica que se ejecuta desde la tarea, vacio que se ejecuta desde el modulo de transmision
       *
       * Se puede enviar un comprobante de manera opcional, si se envia se debe buscarse es comprobante exacto
       * $pvDatos['comidxxx'] //Comprobante
       * $pvDatos['comcodxx'] //Codigo Comprobante
       * $pvDatos['comcscxx'] //Consecutivo Uno
       * $pvDatos['comcsc2x'] //Consecutivo Dos
       * $pvDatos['comfecde'] //Fecha Comprobante desde
       * $pvDatos['comfecha'] //Fecha Comprobante hasta
       * $pvDatos['usuariox'] //Id usuario
       */

      /**
       * Variables para reemplazar caracteres especiales
       * @var array
       */
      $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
      $cReempl = array('\"',"\'"," "," "," "," ");

      /**
       * Variable para saber si hay o no errores de validacion.
       * @var number
       */
      $nSwitch = 0;

      /**
       * Cantidad de registros para reinicio de conexion.
       * @var integer
       */
      $nCanReg = 0;

      /**
       * Hacer la conexion a la base de datos
       */
      $xConexion01 = mysql_connect(OC_SERVER, OC_USERROBOT, OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);

      /**
       * Variable para hacer el retorno.
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = ""; // true - false

      /**
       * Base de datos
       * @var string
       */
      $cAlfa = $pvDatos['basedato'];

      /**
       * Instanciando Objeto para la creacion de las tablas temporales.
       */
      $objTablasTemporales = new cEstructurasCargoWiseDsv();

      /**
       * Vector de Errores
       * @var array
       */
      $vError = array();
      $vError['TABLAERR'] = $pvDatos['tablaerr'];
      
      if($nSwitch == 0){
        /**
         * Vector con las variables del sistea requeridas.
         */
        $vSysStr = $this->fnVariablesSistema($cAlfa);

        /**
         * Buscando informacion del concepto contable
         */
        $mCtoCon = array();
        $qFpar119  = "SELECT ";
        $qFpar119 .= "$cAlfa.fpar0119.pucidxxx, ";
        $qFpar119 .= "$cAlfa.fpar0119.ctoidxxx, ";
        $qFpar119 .= "$cAlfa.fpar0119.ctodesxp, ";
        $qFpar119 .= "$cAlfa.fpar0119.ctodesxl, ";
        $qFpar119 .= "$cAlfa.fpar0119.ctoantxx, ";
        $qFpar119 .= "$cAlfa.fpar0119.ctocwccx  ";
        $qFpar119 .= "FROM $cAlfa.fpar0119 ";
        $qFpar119 .= "WHERE ";
        $qFpar119 .= "$cAlfa.fpar0119.ctopccxx = \"SI\"";
        $nQueryTimeStart = microtime(true); $xFpar119 = mysql_query($qFpar119,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
        $cCtoCon = "";
        while($xRF119 = mysql_fetch_assoc($xFpar119)) {
          $cCtoCon .= "\"{$xRF119['pucidxxx']}~{$xRF119['ctoidxxx']}\", ";
          $mCtoCon["{$xRF119['pucidxxx']}~{$xRF119['ctoidxxx']}"]['ctoantxx'] = $xRF119['ctoantxx'];
          $mCtoCon["{$xRF119['pucidxxx']}~{$xRF119['ctoidxxx']}"]['ctodesxp'] = $xRF119['ctodesxp'];
          $mCtoCon["{$xRF119['pucidxxx']}~{$xRF119['ctoidxxx']}"]['ctodesxl'] = $xRF119['ctodesxl'];
          $mCtoCon["{$xRF119['pucidxxx']}~{$xRF119['ctoidxxx']}"]['ctocwccx'] = $xRF119['ctocwccx'];
        }

        /**
         * Buscando informacion del concepto de cobro causacion automatica
         */
        $qFpar121  = "SELECT ";
        $qFpar121 .= "pucidxxx, ";
        $qFpar121 .= "ctoidxxx, ";
        $qFpar121 .= "\"NO\" AS ctoantxx, ";
        $qFpar121 .= "ctodesxx, ";
        $qFpar121 .= "ctocwccx ";
        $qFpar121 .= "FROM $cAlfa.fpar0121 ";
        $nQueryTimeStart = microtime(true); $xFpar121 = mysql_query($qFpar121,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
        while($xRF121 = mysql_fetch_assoc($xFpar121)) {
          $cCtoCon .= "\"{$xRF121['pucidxxx']}~{$xRF121['ctoidxxx']}\", ";
          $mCtoCon["{$xRF121['pucidxxx']}~{$xRF121['ctoidxxx']}"]['ctoantxx'] = $xRF121['ctoantxx'];
          $mCtoCon["{$xRF121['pucidxxx']}~{$xRF121['ctoidxxx']}"]['ctodesxp'] = $xRF121['ctodesxx'];
          $mCtoCon["{$xRF121['pucidxxx']}~{$xRF121['ctoidxxx']}"]['ctodesxl'] = $xRF121['ctodesxx'];
          $mCtoCon["{$xRF121['pucidxxx']}~{$xRF121['ctoidxxx']}"]['ctocwccx'] = $xRF121['ctocwccx'];
        }
        $cCtoCon = substr($cCtoCon, 0, -2);

        /**
         * Buscando los comprobantes de cartas bancarias y causaciones
         */
        $cComCont  = "";
        $vCarBan   = array();
				$qFpar117  = "SELECT ";
        $qFpar117 .= "comtipxx, ";
				$qFpar117 .= "comidxxx, ";
				$qFpar117 .= "comcodxx  ";
				$qFpar117 .= "FROM $cAlfa.fpar0117 ";
				$qFpar117 .= "WHERE ";
				$qFpar117 .= "(comtipxx = \"CPC\" OR comtipxx = \"PAGOIMPUESTOS\") AND ";
				$qFpar117 .= "regestxx = \"ACTIVO\"";
        $nQueryTimeStart = microtime(true); $xFpar117 = mysql_query($qFpar117,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
				while ($xRF = mysql_fetch_assoc($xFpar117)) {
          $cComCont .= "\"{$xRF['comidxxx']}-{$xRF['comcodxx']}\",";
          if ($xRF['comidxxx'] == "PAGOIMPUESTOS") {
            $vCarBan[] = "{$xRF['comidxxx']}-{$xRF['comcodxx']}";
          }
				}
        $cComCont = substr($cComCont,0,strlen($cComCont)-1);

        //El año de inicio depende de donde se ejecuta el procesados
        //Si es desde las tareas es desde el año anterior hasta el actualizar
        //Si es desde el proceso de transmision solo en el periodo seleccionado
        if ($pvDatos['origenxx'] == "FTP") {
          $nAnoDesde = ((date('Y')-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : date('Y')-1;
          $nAnoHasta = date('Y');
        } else {
          $nAnoDesde = (substr($pvDatos['comfecde'], 0, 4) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : substr($pvDatos['comfecde'], 0, 4);
          $nAnoHasta = (substr($pvDatos['comfecha'], 0, 4) > date('Y')) ? date('Y') : substr($pvDatos['comfecha'], 0, 4);
        }

        $nCanReg   = 0; //Contador de la cantidad de registros
        $nIdArc    = 0; // Indicador del archivo
        $mClientes = array(); //Nombre clientes
        $vClientes = array(); //Nits clientes

        $qInsCab  = "INSERT INTO $cAlfa.{$pvDatos['tablamov']} (";
        $qInsCab .= "interfaz, ";  // Interfaz
        $qInsCab .= "comidxxx, ";  // Id del Comprobante
        $qInsCab .= "comcodxx, ";  // Codigo del Comprobante
        $qInsCab .= "comcscxx, ";  // Consecutivo Uno del Comprobante
        $qInsCab .= "comcsc2x, ";  // Consecutivo Dos del Comprobante
        $qInsCab .= "comfecxx, ";  // Fecha del Comprobante
        $qInsCab .= "reghcrex, ";  // Hora de Creacion del Registro
        $qInsCab .= "ccoidxxx, ";  // Centro de costo de cabecera
        $qInsCab .= "sccidxxx, ";  // Sub centro de costo de cabecera
        $qInsCab .= "sucidxxx, ";  // Id de la Sucursal Operativa
        $qInsCab .= "docidxxx, ";  // Id del DO
        $qInsCab .= "docsufxx, ";  // Sufijo del DO
        $qInsCab .= "ctodesxx, ";  // Observacion del Comprobante
        $qInsCab .= "ctocwccx, ";  // Charge Codes Cargowise
        $qInsCab .= "clicwccx, ";  // Charge Codes Cargowise
        $qInsCab .= "clicwccc, ";  // Charge Codes Cargowise Dueño DO
        $qInsCab .= "comvlrxx, ";  // Valor del Comprobante
        $qInsCab .= "comvlr01, ";  // Valor de IVA del Comprobante
        $qInsCab .= "anioxxxx, ";  // Año del Comprobante
        $qInsCab .= "regestxx, ";  // Estado del Comprobante
        $qInsCab .= "archivox) VALUES ";  // Nombre del archivo

        $qInsDet   = "";
        /*** Consulto los comprobantes del año actual y anterior que no han sido transmitidos a Cargo Wise DSV ***/
        for($nAno = $nAnoDesde; $nAno <= $nAnoHasta; $nAno++ ) {
          $qCabMov  = "SELECT ";
          $qCabMov .= "$cAlfa.fcoc$nAno.comidxxx, "; // Id del Comprobante
          $qCabMov .= "$cAlfa.fcoc$nAno.comcodxx, "; // Codigo del Comprobante
          $qCabMov .= "$cAlfa.fcoc$nAno.comcscxx, "; // Consecutivo Uno del Comprobante
          $qCabMov .= "$cAlfa.fcoc$nAno.comcsc2x, "; // Consecutivo Dos del Comprobante
          $qCabMov .= "$cAlfa.fcoc$nAno.comfecxx, "; // Fecha del Comprobante
          $qCabMov .= "$cAlfa.fcoc$nAno.reghcrex, "; // Hora de Creacion del Registro
          $qCabMov .= "$cAlfa.fcoc$nAno.teridxxx, "; // Tercero
          $qCabMov .= "$cAlfa.fcoc$nAno.terid2xx, "; // Proveedor
          $qCabMov .= "$cAlfa.fcoc$nAno.comobsxx, "; // Observacion
          $qCabMov .= "$cAlfa.fcoc$nAno.comfpxxx, "; // Forma de Pago
          $qCabMov .= "$cAlfa.fcoc$nAno.ccoidxxx, "; // Centro de costo
          $qCabMov .= "$cAlfa.fcoc$nAno.sccidxxx, "; // Sub centro de costo
          $qCabMov .= "$cAlfa.fcoc$nAno.regestxx  "; // Estado del Comprobante
          $qCabMov .= "FROM $cAlfa.fcoc$nAno ";
          $qCabMov .= "WHERE ";
          $qCabMov .= "CONCAT($cAlfa.fcoc$nAno.comidxxx,\"-\",$cAlfa.fcoc$nAno.comcodxx) IN ($cComCont) AND ";
          if ($pvDatos['origenxx'] == "FTP") {
            $qCabMov .= "$cAlfa.fcoc$nAno.comcwxxx = \"0000-00-00 00:00:00\" AND ";
          } else {
            //Se genera el archivo en el rango 
            if ($pvDatos['comidxxx'] != '') {
              $qCabMov .= "$cAlfa.fcoc$nAno.comidxxx = \"{$pvDatos['comidxxx']}\" AND ";
            }
            if ($pvDatos['comcodxx'] != '') {
              $qCabMov .= "$cAlfa.fcoc$nAno.comcodxx = \"{$pvDatos['comcodxx']}\" AND ";
            }
            if ($pvDatos['comcscxx'] != '') {
              $qCabMov .= "$cAlfa.fcoc$nAno.comcscxx = \"{$pvDatos['comcscxx']}\" AND ";
            }
            if ($pvDatos['comcsc2x'] != '') {
              $qCabMov .= "$cAlfa.fcoc$nAno.comcsc2x = \"{$pvDatos['comcsc2x']}\" AND ";
            }
            if ($pvDatos['usuariox'] != '') {
              $qCabMov .= "$cAlfa.fcoc$nAno.regusrxx = \"{$pvDatos['usuariox']}\" AND ";
            }
            $qCabMov .= "$cAlfa.fcoc$nAno.comfecxx BETWEEN \"{$pvDatos['comfecde']}\" AND \"{$pvDatos['comfecha']}\" AND ";
          }
          $qCabMov .= "$cAlfa.fcoc$nAno.regestxx = \"ACTIVO\" ";
          $qCabMov .= "ORDER BY $cAlfa.fcoc$nAno.comidxxx,$cAlfa.fcoc$nAno.comcodxx,$cAlfa.fcoc$nAno.comcscxx,ABS($cAlfa.fcoc$nAno.comcsc2x) ";
          $nQueryTimeStart = microtime(true); $xCabMov = mysql_query($qCabMov,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
          // echo "\n\n".$qCabMov."~".mysql_num_rows($xCabMov)."\n\n";
          
          while($xRCM = mysql_fetch_assoc($xCabMov)){
            $nIdArc++;
            
            /**
             * Variable para saber si hay o no errores de validacion por comprobante.
             * @var number
             */
            $nSwitchAux = 0;

            $nCanReg++;
            if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDbCargoWiseDsv($xConexion01); }

            //Detalle del documento
            $qDetMov  = "SELECT ";
            $qDetMov .= "$cAlfa.fcod$nAno.comidxxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comcodxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comcscxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comcsc2x, ";
            $qDetMov .= "$cAlfa.fcod$nAno.teridxxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.terid2xx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.pucidxxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.ctoidxxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comvlrxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comvlr01, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comvlr02, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comobsxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.sucidxxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.docidxxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.docsufxx  ";
            $qDetMov .= "FROM $cAlfa.fcod$nAno ";
            $qDetMov .= "WHERE ";
            $qDetMov .= "$cAlfa.fcod$nAno.comidxxx = \"{$xRCM['comidxxx']}\" AND ";
            $qDetMov .= "$cAlfa.fcod$nAno.comcodxx = \"{$xRCM['comcodxx']}\" AND ";
            $qDetMov .= "$cAlfa.fcod$nAno.comcscxx = \"{$xRCM['comcscxx']}\" AND ";
            $qDetMov .= "$cAlfa.fcod$nAno.comcsc2x = \"{$xRCM['comcsc2x']}\" AND ";
            $qDetMov .= "CONCAT($cAlfa.fcod$nAno.pucidxxx,\"~\",$cAlfa.fcod$nAno.ctoidxxx) IN ($cCtoCon) ";
            $qDetMov .= "ORDER BY ABS(comseqxx) ";
            $nQueryTimeStart = microtime(true); $xDetMov = mysql_query($qDetMov,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
            // echo "\n\n".$qDetMov."~".mysql_num_rows($xDetMov);

            if (mysql_num_rows($xDetMov) == 0) {
              $nSwitchAux = 1;
              $vError['LINEAERR'] = __LINE__;
              $vError['TIPOERRX'] = "FALLIDO";
              $vError['DESERROR'] = "El Comprobante [{$xRCM['comidxxx']}-{$xRCM['comcodxx']}-{$xRCM['comcscxx']}-{$xRCM['comcsc2x']}], Fecha [{$xRCM['comfecxx']}] No Existe en Detalle.";
              $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
            }

            if ($nSwitchAux == 0) {
              // Se debe cargar primero el comprobante en una matriz para identificar el primer DO
              $mDetMov = array(); 
              $cSucId  = ""; 
              $cDocId  = ""; 
              $cDocSuf = "";
              while ($xRDM = mysql_fetch_assoc($xDetMov)) {
                $nCanReg++;
                if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDbCargoWiseDsv($xConexion01); }

                //Descripcion
                $cCtoDes = ($mCtoCon["{$xRDM['pucidxxx']}~{$xRDM['ctoidxxx']}"]['ctodesxp'] != "") ? $mCtoCon["{$xRDM['pucidxxx']}~{$xRDM['ctoidxxx']}"]['ctodesxp'] : $mCtoCon["{$xRDM['pucidxxx']}~{$xRDM['ctoidxxx']}"]['ctodesxl']; // Descripcion

                //Se obtiene el Charge Codes del Cliente
                $cCliCod = "";
                if (!in_array("{$xRDM['teridxxx']}", $vClientes)) {
                  //Buscando nombre del cliente
                  $qDatCli  = "SELECT ";
                  $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) != \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX, ";
                  $qDatCli .= "$cAlfa.SIAI0150.CLICWCCX ";
                  $qDatCli .= "FROM $cAlfa.SIAI0150 ";
                  $qDatCli .= "WHERE CLIIDXXX = \"{$xRDM['teridxxx']}\" LIMIT 0,1 ";
                  $nQueryTimeStart = microtime(true); $xDatCli = mysql_query($qDatCli,$xConexion01);
                  $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                  $vDatCli = mysql_fetch_assoc($xDatCli);
                  $vClientes[] = "{$xRDM['teridxxx']}";
                  $mClientes["{$xRDM['teridxxx']}"] = $vDatCli['CLICWCCX'];
                }
                $cCliCod = $mClientes["{$xRDM['teridxxx']}"];

                //Para las cartas bancarias se envia como proveedor al Beneficiario (terid2xx de cabecera)
                if (!in_array("{$xRCM['comidxxx']}-{$xRCM['comcodxx']}", $vCarBan)) {
                  $xRDM['terid2xx'] = $xRCM['terid2xx'];
                }

                //Se obtiene el Charge Codes del Proveedor
                $cTerCod = "";
                if (!in_array("{$xRDM['terid2xx']}", $vClientes)) {
                  //Buscando nombre del cliente
                  $qDatCli  = "SELECT ";
                  $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) != \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX, ";
                  $qDatCli .= "$cAlfa.SIAI0150.CLICWCCX ";
                  $qDatCli .= "FROM $cAlfa.SIAI0150 ";
                  $qDatCli .= "WHERE CLIIDXXX = \"{$xRDM['terid2xx']}\" LIMIT 0,1 ";
                  $nQueryTimeStart = microtime(true); $xDatCli = mysql_query($qDatCli,$xConexion01);
                  $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                  $vDatCli = mysql_fetch_assoc($xDatCli);
                  $vClientes[] = "{$xRDM['terid2xx']}";
                  $mClientes["{$xRDM['terid2xx']}"] = $vDatCli['CLICWCCX'];
                }
                $cTerCod = $mClientes["{$xRDM['terid2xx']}"];

                //Primer DO
                if ($cDocId == "" && $xRDM['docidxxx'] != "") {
                  $cSucId  = $xRDM['sucidxxx']; 
                  $cDocId  = $xRDM['docidxxx']; 
                  $cDocSuf = $xRDM['docsufxx'];
                }

                //Datos comprobante
                $nInd_mDetMov = count($mDetMov);
                $mDetMov[$nInd_mDetMov]['interfaz'] = "CAUSACIONES_TERCEROS";          // Interfaz
                $mDetMov[$nInd_mDetMov]['comidxxx'] = $xRDM['comidxxx'];               // Id del Comprobante
                $mDetMov[$nInd_mDetMov]['comcodxx'] = $xRDM['comcodxx'];               // Codigo del Comprobante
                $mDetMov[$nInd_mDetMov]['comcscxx'] = $xRDM['comcscxx'];               // Consecutivo Uno del Comprobante
                $mDetMov[$nInd_mDetMov]['comcsc2x'] = $xRDM['comcsc2x'];               // Consecutivo Dos del Comprobante
                $mDetMov[$nInd_mDetMov]['comfecxx'] = $xRCM['comfecxx'];               // Fecha del Comprobante
                $mDetMov[$nInd_mDetMov]['reghcrex'] = $xRCM['reghcrex'];               // Hora de Creacion del Registro
                $mDetMov[$nInd_mDetMov]['ccoidxxx'] = $xRCM['ccoidxxx'];               // Centro de Costo
                $mDetMov[$nInd_mDetMov]['sccidxxx'] = $xRCM['sccidxxx'];               // Sub Centro de Costo
                $mDetMov[$nInd_mDetMov]['sucidxxx'] = $xRDM['sucidxxx'];               // Sucursal DO
                $mDetMov[$nInd_mDetMov]['docidxxx'] = $xRDM['docidxxx'];               // Numero DO
                $mDetMov[$nInd_mDetMov]['docsufxx'] = $xRDM['docsufxx'];               // Sufijo DO
                $mDetMov[$nInd_mDetMov]['ctodesxx'] = $cCtoDes;                        // Descripcion
                $mDetMov[$nInd_mDetMov]['ctocwccx'] = $mCtoCon["{$xRDM['pucidxxx']}~{$xRDM['ctoidxxx']}"]['ctocwccx']; // Charge Codes Cargowise
                $mDetMov[$nInd_mDetMov]['clicwccx'] = $cTerCod;                        // Proveedor Charge Codes Cargowise
                $mDetMov[$nInd_mDetMov]['clicwccc'] = $cCliCod;                        // Cliente Codes Cargowise Dueño DO
                $mDetMov[$nInd_mDetMov]['comvlrxx'] = $xRDM['comvlrxx'];               // Valor del Comprobante
                $mDetMov[$nInd_mDetMov]['comvlr01'] = $xRDM['comvlr02'];               // Valor del IVA
                $mDetMov[$nInd_mDetMov]['anioxxxx'] = substr($xRCM['comfecxx'], 0, 4); // Año del Comprobante
                $mDetMov[$nInd_mDetMov]['regestxx'] = $xRCM['regestxx'];               // Estado del Comprobante
                $mDetMov[$nInd_mDetMov]['archivox'] = $nIdArc;                         // Archivo
              }

              //Insertando en la tabla temporal
              for($nD=0; $nD<count($mDetMov); $nD++) {
                if ($mDetMov[$nD]['docidxxx'] == "") {
                  $mDetMov[$nD]['sucidxxx'] = $cSucId;
                  $mDetMov[$nD]['docidxxx'] = $cDocId;
                  $mDetMov[$nD]['docsufxx'] = $cDocSuf;
                }

                //Datos comprobante
                $qInsDet .= "(";
                $qInsDet .= "\"{$mDetMov[$nD]['interfaz']}\","; // Interfaz
                $qInsDet .= "\"{$mDetMov[$nD]['comidxxx']}\","; // Id del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['comcodxx']}\","; // Codigo del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['comcscxx']}\","; // Consecutivo Uno del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['comcsc2x']}\","; // Consecutivo Dos del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['comfecxx']}\","; // Fecha del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['reghcrex']}\","; // Hora de Creacion del Registro
                $qInsDet .= "\"{$mDetMov[$nD]['ccoidxxx']}\","; // Centro de costo de cabecera
                $qInsDet .= "\"{$mDetMov[$nD]['sccidxxx']}\","; // Sub centro de costo de cabecera
                $qInsDet .= "\"{$mDetMov[$nD]['sucidxxx']}\","; // Sucursal DO
                $qInsDet .= "\"{$mDetMov[$nD]['docidxxx']}\","; // Numero DO
                $qInsDet .= "\"{$mDetMov[$nD]['docsufxx']}\","; // Sufijo DO
                $qInsDet .= "\"{$mDetMov[$nD]['ctodesxx']}\","; // Descripcion
                $qInsDet .= "\"{$mDetMov[$nD]['ctocwccx']}\","; // Charge Codes Cargowise
                $qInsDet .= "\"{$mDetMov[$nD]['clicwccx']}\","; // Charge Codes Cargowise
                $qInsDet .= "\"{$mDetMov[$nD]['clicwccc']}\","; // Charge Codes Cargowise Dueño Do
                $qInsDet .= "\"{$mDetMov[$nD]['comvlrxx']}\","; // Valor del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['comvlr01']}\","; // Valor del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['anioxxxx']}\","; // Año del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['regestxx']}\","; // Estado del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['archivox']}\"), "; // Archivo

                $nCanReg++;
                if (($nCanReg % _NUMREG_) == 0) {
                  $xConexion01 = $objTablasTemporales->fnReiniciarConexionDbCargoWiseDsv($xConexion01); 

                  $qInsert = $qInsCab.substr($qInsDet, 0, -2);
                  $nQueryTimeStart = microtime(true); $xInsert = mysql_query($qInsert,$xConexion01);
                  $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                  if (!$xInsert) {
                    $nSwitchAux = 1;
                    $vError['LINEAERR'] = __LINE__;
                    $vError['TIPOERRX'] = "FALLIDO";
                    $vError['DESERROR'] = "Error al insertar el Comprobante en la tabla temporal.";
                    $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                  }
                  $qInsDet = "";
                }
              }
            }
            if ($nSwitchAux == 1) {
              $nSwitch = 1;
            }
          } //while($xRCM = mysql_fetch_assoc($xCabMov)){
        }

        if ($qInsDet != "") {
          $xConexion01 = $objTablasTemporales->fnReiniciarConexionDbCargoWiseDsv($xConexion01); 

          $qInsert = $qInsCab.substr($qInsDet, 0, -2);
          $nQueryTimeStart = microtime(true); $xInsert = mysql_query($qInsert,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
          if (!$xInsert) {
            $nSwitchAux = 1;
            $vError['LINEAERR'] = __LINE__;
            $vError['TIPOERRX'] = "FALLIDO";
            $vError['DESERROR'] = "Error al insertar el Comprobante en la tabla temporal.";
            $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
          }
        }
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    } ## function fnCausaciones($cAlfa){

    /**
     * Metodo para la transmision de los comprobantes de facturas.
     */
    function fnFacturas($pvDatos){
      /**
       * Recibe una Matriz con las siguientes posiciones:
       * $pvDatos['basedato'] //Base de datos
       * $pvDatos['tablaerr'] //Tabla Error
       * $pvDatos['tablamov'] //Tabla Movimiento
       * $pvDatos['origenxx'] //Origen (FTP o Vacio), FTP indica que se ejecuta desde la tarea, vacio que se ejecuta desde el modulo de transmision
       *
       * Se puede enviar un comprobante de manera opcional, si se envia se debe buscarse es comprobante exacto
       * $pvDatos['comidxxx'] //Comprobante
       * $pvDatos['comcodxx'] //Codigo Comprobante
       * $pvDatos['comcscxx'] //Consecutivo Uno
       * $pvDatos['comcsc2x'] //Consecutivo Dos
       * $pvDatos['comfecde'] //Fecha Comprobante desde
       * $pvDatos['comfecha'] //Fecha Comprobante hasta
       * $pvDatos['usuariox'] //Id usuario
       */

      /**
       * Variables para reemplazar caracteres especiales
       * @var array
       */
      $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
      $cReempl = array('\"',"\'"," "," "," "," ");

      /**
       * Variable para saber si hay o no errores de validacion.
       * @var number
       */
      $nSwitch = 0;

      /**
       * Cantidad de registros para reinicio de conexion.
       * @var integer
       */
      $nCanReg = 0;

      /**
       * Hacer la conexion a la base de datos
       */
      $xConexion01 = mysql_connect(OC_SERVER, OC_USERROBOT, OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);

      /**
       * Variable para hacer el retorno.
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = ""; // true - false

      /**
       * Base de datos
       * @var string
       */
      $cAlfa = $pvDatos['basedato'];

      /**
       * Instanciando Objeto para la creacion de las tablas temporales.
       */
      $objTablasTemporales = new cEstructurasCargoWiseDsv();

      /**
       * Vector de Errores
       * @var array
       */
      $vError = array();
      $vError['TABLAERR'] = $pvDatos['tablaerr'];

      if($nSwitch == 0){
        /**
         * Vector con las variables del sistea requeridas.
         */
        $vSysStr = $this->fnVariablesSistema($cAlfa);

        /**
         * Buscando informacion del concepto contable
         */
        $mCtoCon = array();
        $qFpar119  = "SELECT ";
        $qFpar119 .= "$cAlfa.fpar0119.pucidxxx, ";
        $qFpar119 .= "$cAlfa.fpar0119.ctoidxxx, ";
        $qFpar119 .= "$cAlfa.fpar0119.ctodesxp, ";
        $qFpar119 .= "$cAlfa.fpar0119.ctodesxl, ";
        $qFpar119 .= "$cAlfa.fpar0119.ctoantxx, ";
        $qFpar119 .= "$cAlfa.fpar0119.ctocwccx  ";
        $qFpar119 .= "FROM $cAlfa.fpar0119 ";
        $qFpar119 .= "WHERE ";
        $qFpar119 .= "($cAlfa.fpar0119.ctopccxx = \"SI\" OR $cAlfa.fpar0119.ctoclaxf != \"\")";
        $nQueryTimeStart = microtime(true); $xFpar119 = mysql_query($qFpar119,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
        $cCtoCon = "";
        while($xRF119 = mysql_fetch_assoc($xFpar119)) {
          $cCtoCon .= "\"{$xRF119['pucidxxx']}~{$xRF119['ctoidxxx']}\", ";
          $mCtoCon["{$xRF119['pucidxxx']}~{$xRF119['ctoidxxx']}"]['ctoantxx'] = $xRF119['ctoantxx'];
          $mCtoCon["{$xRF119['pucidxxx']}~{$xRF119['ctoidxxx']}"]['ctodesxp'] = $xRF119['ctodesxp'];
          $mCtoCon["{$xRF119['pucidxxx']}~{$xRF119['ctoidxxx']}"]['ctodesxl'] = $xRF119['ctodesxl'];
          $mCtoCon["{$xRF119['pucidxxx']}~{$xRF119['ctoidxxx']}"]['ctocwccx'] = $xRF119['ctocwccx'];
        }

        /**
         * Buscando informacion del concepto de cobro causacion automatica
         */
        $qFpar121  = "SELECT ";
        $qFpar121 .= "pucidxxx, ";
        $qFpar121 .= "ctoidxxx, ";
        $qFpar121 .= "\"NO\" AS ctoantxx, ";
        $qFpar121 .= "ctodesxx, ";
        $qFpar121 .= "ctocwccx ";
        $qFpar121 .= "FROM $cAlfa.fpar0121 ";
        $nQueryTimeStart = microtime(true); $xFpar121 = mysql_query($qFpar121,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
        while($xRF121 = mysql_fetch_assoc($xFpar121)) {
          $cCtoCon .= "\"{$xRF121['pucidxxx']}~{$xRF121['ctoidxxx']}\", ";
          $mCtoCon["{$xRF121['pucidxxx']}~{$xRF121['ctoidxxx']}"]['ctoantxx'] = $xRF121['ctoantxx'];
          $mCtoCon["{$xRF121['pucidxxx']}~{$xRF121['ctoidxxx']}"]['ctodesxp'] = $xRF121['ctodesxx'];
          $mCtoCon["{$xRF121['pucidxxx']}~{$xRF121['ctoidxxx']}"]['ctodesxl'] = $xRF121['ctodesxx'];
          $mCtoCon["{$xRF121['pucidxxx']}~{$xRF121['ctoidxxx']}"]['ctocwccx'] = $xRF121['ctocwccx'];
        }

        /**
         * Buscando informacion del concepto de cobro
         */
        $qFpar129  = "SELECT ";
        $qFpar129 .= "pucidxxx, ";
        $qFpar129 .= "ctoidxxx, ";
        $qFpar129 .= "pucidexx, ";
        $qFpar129 .= "ctoidexx, \"NO\" AS serantxx, ";
        $qFpar129 .= "serdespx, ";
        $qFpar129 .= "sercwccx ";
        $qFpar129 .= "FROM $cAlfa.fpar0129 ";
        $nQueryTimeStart = microtime(true); $xFpar129 = mysql_query($qFpar129,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
        while($xRF129 = mysql_fetch_assoc($xFpar129)) {
          $cCtoCon .= "\"{$xRF129['pucidxxx']}~{$xRF129['ctoidxxx']}\", ";
          $mCtoCon["{$xRF129['pucidxxx']}~{$xRF129['ctoidxxx']}"]['ctoantxx'] = $xRF129['serantxx'];
          $mCtoCon["{$xRF129['pucidxxx']}~{$xRF129['ctoidxxx']}"]['ctodesxp'] = $xRF129['serdespx'];
          $mCtoCon["{$xRF129['pucidxxx']}~{$xRF129['ctoidxxx']}"]['ctodesxl'] = $xRF129['serdespx'];
          $mCtoCon["{$xRF129['pucidxxx']}~{$xRF129['ctoidxxx']}"]['ctocwccx'] = $xRF129['sercwccx'];
          if ($xRF129['pucidexx'] != '' && $xRF129['ctoidexx'] != '') {
            $mCtoCon["{$xRF129['pucidexx']}~{$xRF129['ctoidexx']}"]['ctoantxx'] = $xRF129['serantxx'];
            $mCtoCon["{$xRF129['pucidexx']}~{$xRF129['ctoidexx']}"]['ctodesxp'] = $xRF129['serdespx'];
            $mCtoCon["{$xRF129['pucidexx']}~{$xRF129['ctoidexx']}"]['ctodesxl'] = $xRF129['serdespx'];
            $mCtoCon["{$xRF129['pucidexx']}~{$xRF129['ctoidexx']}"]['ctocwccx'] = $xRF129['sercwccx'];
          }
        }
        $cCtoCon = substr($cCtoCon, 0, -2);

        /**
         * Buscando comprobantes marcados de Nota Credito
         */
        $qNotCre  = "SELECT ";
        $qNotCre .= "CONCAT(comidxxx,\"-\",comcodxx) AS comidxxx ";
        $qNotCre .= "FROM $cAlfa.fpar0117 ";
        $qNotCre .= "WHERE ";
        $qNotCre .= "comidxxx = \"C\" AND ";
        $qNotCre .= "comtipxx != \"AJUSTES\"";
        $xNotCre  = mysql_query($qNotCre,$xConexion01);
        // echo $qNotCre."~".mysql_num_rows($xNotCre);
        $cNotCre = "";
        $vNotCre = array();
        while ($xRNC = mysql_fetch_assoc($xNotCre)) {
          $cNotCre  .= "\"{$xRNC['comidxxx']}\",";
          $vNotCre[] = "{$xRNC['comidxxx']}";
        }
        $cNotCre = substr($cNotCre,0,strlen($cNotCre)-1);

        /**
         * Buscando Ajustes
         */
        $qAjustes  = "SELECT ";
        $qAjustes .= "CONCAT(comidxxx,\"-\",comcodxx) AS comidxxx ";
        $qAjustes .= "FROM $cAlfa.fpar0117 ";
        $qAjustes .= "WHERE ";
        $qAjustes .= "comidxxx = \"C\" AND ";
        $qAjustes .= "comtipxx = \"AJUSTES\"";
        $xAjustes  = mysql_query($qAjustes,$xConexion01);
        // echo $qAjustes."~".mysql_num_rows($xAjustes);
        $cAjustes = "";
        $vAjustes = array();
        while ($xRNA = mysql_fetch_assoc($xAjustes)) {
          $cAjustes  .= "\"{$xRNA['comidxxx']}\",";
          $vAjustes[] = "{$xRNA['comidxxx']}";
        }
        $cAjustes = substr($cAjustes,0,strlen($cAjustes)-1);

        // Conceptos de tributos
        $qDatCom  = "SELECT ";
        $qDatCom .= "ctoidxxx, ";
        $qDatCom .= "pucidxxx ";
        $qDatCom .= "FROM $cAlfa.fpar0119 ";
        $qDatCom .= "WHERE ";
        $qDatCom .= "(ctoptaxg = \"SI\" OR ctoptaxl = \"SI\") AND ";
        $qDatCom .= "regestxx = \"ACTIVO\" ";
        $nQueryTimeStart = microtime(true); $xDatCom = mysql_query($qDatCom,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
        // echo "\n\n".$qDatCom."~".mysql_num_rows($xDatCom)."\n\n";
        $vComImp = array();
        //f_Mensaje(__FILE__,__LINE__,"$qDatCom~".mysql_num_rows($xDatCom));
        while($xRDC = mysql_fetch_assoc($xDatCom)){
          $vComImp[] = $xRDC['pucidxxx']."~".$xRDC['ctoidxxx'];
        }

        //El año de inicio depende de donde se ejecuta el procesados
        //Si es desde las tareas es desde el año anterior hasta el actualizar
        //Si es desde el proceso de transmision solo en el periodo seleccionado
        if ($pvDatos['origenxx'] == "FTP") {
          $nAnoDesde = ((date('Y')-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : date('Y')-1;
          $nAnoHasta = date('Y');
        } else {
          $nAnoDesde = (substr($pvDatos['comfecde'], 0, 4) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : substr($pvDatos['comfecde'], 0, 4);
          $nAnoHasta = (substr($pvDatos['comfecha'], 0, 4) > date('Y')) ? date('Y') : substr($pvDatos['comfecha'], 0, 4);
        }
        
        $nCanReg   = 0; //Contador de la cantidad de registros
        $nIdArc    = 0; // Indicador del archivo
        $mClientes = array(); //Nombre clientes
        $vClientes = array(); //Nits clientes

        $qInsCab  = "INSERT INTO $cAlfa.{$pvDatos['tablamov']} (";
        $qInsCab .= "interfaz, ";  // Interfaz
        $qInsCab .= "comidxxx, ";  // Id del Comprobante
        $qInsCab .= "comcodxx, ";  // Codigo del Comprobante
        $qInsCab .= "comcscxx, ";  // Consecutivo Uno del Comprobante
        $qInsCab .= "comcsc2x, ";  // Consecutivo Dos del Comprobante
        $qInsCab .= "comfecxx, ";  // Fecha del Comprobante
        $qInsCab .= "reghcrex, ";  // Hora de Creacion del Registro
        $qInsCab .= "ccoidxxx, ";  // Centro de costo de cabecera
        $qInsCab .= "sccidxxx, ";  // Sub centro de costo de cabecera
        $qInsCab .= "sucidxxx, ";  // Id de la Sucursal Operativa
        $qInsCab .= "docidxxx, ";  // Id del DO
        $qInsCab .= "docsufxx, ";  // Sufijo del DO
        $qInsCab .= "ctopccxx, ";  // Concepto PCC
        $qInsCab .= "ctotrixx, ";  // Concepto tributos
        $qInsCab .= "ctodesxx, ";  // Observacion del Comprobante
        $qInsCab .= "ctocwccx, ";  // Charge Codes Cargowise
        $qInsCab .= "clicwccx, ";  // Charge Codes Cargowise
        $qInsCab .= "comvlrxx, ";  // Valor del Comprobante
        $qInsCab .= "comvlr01, ";  // Valor de IVA del Comprobante
        $qInsCab .= "anioxxxx, ";  // Año del Comprobante
        $qInsCab .= "regestxx, ";  // Estado del Comprobante
        $qInsCab .= "archivox) VALUES ";  // Nombre del archivo complemento

        $qInsDet   = "";
        /*** Consulto los comprobantes del año actual y anterior que no han sido transmitidos a Cargo Wise DSV ***/
        for($nAno = $nAnoDesde; $nAno <= $nAnoHasta; $nAno++ ) {
          $qCabMov  = "SELECT ";
          $qCabMov .= "$cAlfa.fcoc$nAno.comidxxx, "; // Id del Comprobante
          $qCabMov .= "$cAlfa.fcoc$nAno.comcodxx, "; // Codigo del Comprobante
          $qCabMov .= "$cAlfa.fcoc$nAno.comcscxx, "; // Consecutivo Uno del Comprobante
          $qCabMov .= "$cAlfa.fcoc$nAno.comcsc2x, "; // Consecutivo Dos del Comprobante
          $qCabMov .= "$cAlfa.fcoc$nAno.comfecxx, "; // Fecha del Comprobante
          $qCabMov .= "$cAlfa.fcoc$nAno.reghcrex, "; // Hora de Creacion del Registro
          $qCabMov .= "$cAlfa.fcoc$nAno.teridxxx, "; // Cliente
          $qCabMov .= "$cAlfa.fcoc$nAno.terid2xx, "; // Facturar a
          $qCabMov .= "$cAlfa.fcoc$nAno.comobsxx, "; // Observacion
          $qCabMov .= "$cAlfa.fcoc$nAno.comfpxxx, "; // Tramites
          $qCabMov .= "$cAlfa.fcoc$nAno.ccoidxxx, "; // Centro de costo
          $qCabMov .= "$cAlfa.fcoc$nAno.sccidxxx, "; // Sub centro de costo
          $qCabMov .= "$cAlfa.fcoc$nAno.comfpxxx, "; // Campo memo de Tramites
          $qCabMov .= "$cAlfa.fcoc$nAno.commemod, "; // Campo memo de Pagos a Terceros
          $qCabMov .= "$cAlfa.fcoc$nAno.regestxx  "; // Estado del Comprobante
          $qCabMov .= "FROM $cAlfa.fcoc$nAno ";
          $qCabMov .= "WHERE ";
          if ($pvDatos['origenxx'] == "FTP") {
            $qCabMov .= "(";
            $qCabMov .= "$cAlfa.fcoc$nAno.comidxxx = \"F\" OR ";
            if ($cNotCre != "") {
              $qCabMov .= "CONCAT(comidxxx,\"-\",comcodxx) IN ($cNotCre) OR ";
            }
            if ($cAjustes != "") {
              $qCabMov .= "CONCAT(comidxxx,\"-\",comcodxx) IN ($cAjustes) OR ";
            }
            $qCabMov = substr($qCabMov, 0, -4);         
            $qCabMov .= ") AND ";
            $qCabMov .= "$cAlfa.fcoc$nAno.comcwxxx = \"0000-00-00 00:00:00\" AND ";
          } else {
            if ($pvDatos['interfaz'] == 'FACTURAS') {
              $qCabMov .= "$cAlfa.fcoc$nAno.comidxxx = \"F\" AND ";
            } elseif ($pvDatos['interfaz'] == 'NOTAS_CREDITO') {
              $qCabMov .= "(";
              if ($cNotCre != "") {
                $qCabMov .= "CONCAT(comidxxx,\"-\",comcodxx) IN ($cNotCre) OR ";
              }
              if ($cAjustes != "") {
                $qCabMov .= "CONCAT(comidxxx,\"-\",comcodxx) IN ($cAjustes) OR ";
              }
              $qCabMov = substr($qCabMov, 0, -4);        
              $qCabMov .= ") AND "; 
            }
            //Se genera el archivo en el rango 
            if ($pvDatos['comidxxx'] != '') {
              $qCabMov .= "$cAlfa.fcoc$nAno.comidxxx = \"{$pvDatos['comidxxx']}\" AND ";
            }
            if ($pvDatos['comcodxx'] != '') {
              $qCabMov .= "$cAlfa.fcoc$nAno.comcodxx = \"{$pvDatos['comcodxx']}\" AND ";
            }
            if ($pvDatos['comcscxx'] != '') {
              $qCabMov .= "$cAlfa.fcoc$nAno.comcscxx = \"{$pvDatos['comcscxx']}\" AND ";
            }
            if ($pvDatos['comcsc2x'] != '') {
              $qCabMov .= "$cAlfa.fcoc$nAno.comcsc2x = \"{$pvDatos['comcsc2x']}\" AND ";
            }
            if ($pvDatos['usuariox'] != '') {
              $qCabMov .= "$cAlfa.fcoc$nAno.regusrxx = \"{$pvDatos['usuariox']}\" AND ";
            }
            $qCabMov .= "$cAlfa.fcoc$nAno.comfecxx BETWEEN \"{$pvDatos['comfecde']}\" AND \"{$pvDatos['comfecha']}\" AND ";
          }
          $qCabMov .= "$cAlfa.fcoc$nAno.regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
          $qCabMov .= "ORDER BY $cAlfa.fcoc$nAno.comidxxx,$cAlfa.fcoc$nAno.comcodxx,$cAlfa.fcoc$nAno.comcscxx,ABS($cAlfa.fcoc$nAno.comcsc2x) ";
          $nQueryTimeStart = microtime(true); $xCabMov = mysql_query($qCabMov,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
          // echo "\n\n".$qCabMov."~".mysql_num_rows($xCabMov)."\n\n";

          while($xRCM = mysql_fetch_assoc($xCabMov)){
            $nIdArc++;
            
            /**
             * Variable para saber si hay o no errores de validacion por comprobante.
             * @var number
             */
            $nSwitchAux = 0;

            $nCanReg++;
            if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDbCargoWiseDsv($xConexion01); }

            // Se debe cargar primero el comprobante en una matriz para identificar el primer DO
            $mDetMov = array(); 
            $cSucId  = ""; 
            $cDocId  = "";
            $cDocSuf = "";
            $cSccId  = "";

            //Se obtiene el Charge de a quien se le va a facturar
            $cTerCod = "";
            if (!in_array("{$xRCM['terid2xx']}", $vClientes)) {
              //Buscando nombre del cliente
              $qDatCli  = "SELECT ";
              $qDatCli .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF((TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))) != \"\",(TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X))),\"SIN NOMBRE\")) AS CLINOMXX, ";
              $qDatCli .= "$cAlfa.SIAI0150.CLICWCCX ";
              $qDatCli .= "FROM $cAlfa.SIAI0150 ";
              $qDatCli .= "WHERE CLIIDXXX = \"{$xRCM['terid2xx']}\" LIMIT 0,1 ";
              $nQueryTimeStart = microtime(true); $xDatCli = mysql_query($qDatCli,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
              // echo "\n\n".$qDatCli."~".mysql_num_rows($xDatCli)."\n\n";
              $vDatCli = mysql_fetch_assoc($xDatCli);
              $vClientes[] = "{$xRCM['terid2xx']}";
              $mClientes["{$xRCM['terid2xx']}"] = $vDatCli['CLICWCCX'];
            }
            $cTerCod = $mClientes["{$xRCM['terid2xx']}"];

            if ($xRCM['comidxxx'] == "F") {
              //Primer DO
              $mTraAux = $this->fnExplodeArray($xRCM['comfpxxx'],"|","~");
              for($nTA = 0; $nTA < count($mTraAux); $nTA++) {
                if ($cDocId == "" && $mTraAux[$nTA][2] != "") {
                    $cSucId  = $mTraAux[$nTA][15]; 
                    $cDocId  = $mTraAux[$nTA][2]; 
                    $cDocSuf = $mTraAux[$nTA][3];
                }
              }

              // Se obtienen los pagos a terceros de la factura
              $mPagTerAux = $this->fnExplodeArray($xRCM['commemod'],"|","~");
              for($nPT = 0; $nPT < count($mPagTerAux); $nPT++){
                if ($mPagTerAux[$nPT][0] != "") {
                  $vDescPcc = explode('^', $mPagTerAux[$nPT][2]);

                  $vTramite = array();
                  if ($mPagTerAux[$nPT][14] != "") {
                    //Primer DO
                    $vTramite = explode("-",$mPagTerAux[$nPT][14]);
                    if ($cDocId == "" && $vTramite[1] != "") {
                      $cSucId  = $vTramite[0]; 
                      $cDocId  = $vTramite[1]; 
                      $cDocSuf = $vTramite[2];
                    }
                  }

                  // Si es un concepto de tributos se modifica la descripcion y se envia la marca de tributos
                  $cCtoTri = "";
                  if (in_array("{$mPagTerAux[$nPT][9]}~{$mPagTerAux[$nPT][1]}", $vComImp)) {
                    $vDescPcc[0] = "TRIBUTOS ADUANEROS";
                    $cCtoTri = "SI";
                  }

                  //Datos comprobante
                  $nInd_mDetMov = count($mDetMov);
                  $mDetMov[$nInd_mDetMov]['interfaz'] = ($xRCM['comidxxx'] == "F") ? "FACTURAS" : "NOTAS_CREDITO"; // Interfaz
                  $mDetMov[$nInd_mDetMov]['comidxxx'] = $xRCM['comidxxx'];               // Id del Comprobante
                  $mDetMov[$nInd_mDetMov]['comcodxx'] = $xRCM['comcodxx'];               // Codigo del Comprobante
                  $mDetMov[$nInd_mDetMov]['comcscxx'] = $xRCM['comcscxx'];               // Consecutivo Uno del Comprobante
                  $mDetMov[$nInd_mDetMov]['comcsc2x'] = $xRCM['comcsc2x'];               // Consecutivo Dos del Comprobante
                  $mDetMov[$nInd_mDetMov]['comfecxx'] = $xRCM['comfecxx'];               // Fecha del Comprobante
                  $mDetMov[$nInd_mDetMov]['reghcrex'] = $xRCM['reghcrex'];               // Hora de Creacion del Registro
                  $mDetMov[$nInd_mDetMov]['ccoidxxx'] = $xRCM['ccoidxxx'];               // Centro de Costo
                  $mDetMov[$nInd_mDetMov]['sccidxxx'] = $xRCM['sccidxxx'];               // Sub Centro de Costo
                  $mDetMov[$nInd_mDetMov]['sucidxxx'] = $vTramite[0];                    // Sucursal DO
                  $mDetMov[$nInd_mDetMov]['docidxxx'] = $vTramite[1];                    // Numero DO
                  $mDetMov[$nInd_mDetMov]['docsufxx'] = $vTramite[2];                    // Sufijo DO
                  $mDetMov[$nInd_mDetMov]['ctopccxx'] = "SI";                            // Concepto PCC
                  $mDetMov[$nInd_mDetMov]['ctotrixx'] = $cCtoTri;                        // Concepto tributos
                  $mDetMov[$nInd_mDetMov]['ctodesxx'] = trim($vDescPcc[0]);              // Descripcion
                  $mDetMov[$nInd_mDetMov]['ctocwccx'] = $mCtoCon["{$mPagTerAux[$nPT][9]}~{$mPagTerAux[$nPT][1]}"]['ctocwccx']; // Charge Codes Cargowise
                  $mDetMov[$nInd_mDetMov]['clicwccx'] = $cTerCod;                        // Proveedor Charge Codes Cargowise
                  $mDetMov[$nInd_mDetMov]['comvlrxx'] = $mPagTerAux[$nPT][7];            // Valor del Comprobante
                  $mDetMov[$nInd_mDetMov]['comvlr01'] = $mPagTerAux[$nPT][16];           // Valor del IVA
                  $mDetMov[$nInd_mDetMov]['anioxxxx'] = substr($xRCM['comfecxx'], 0, 4); // Año del Comprobante
                  $mDetMov[$nInd_mDetMov]['regestxx'] = $xRCM['regestxx'];               // Estado del Comprobante
                  $mDetMov[$nInd_mDetMov]['archivox'] = $nIdArc;                         // Archivo
                }
              }
            }

            //Detalle del documento
            $qDetMov  = "SELECT ";
            $qDetMov .= "$cAlfa.fcod$nAno.comidxxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comcodxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comcscxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comcsc2x, ";
            $qDetMov .= "$cAlfa.fcod$nAno.teridxxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.terid2xx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.pucidxxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.ctoidxxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comctocx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comvlrxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comvlr01, ";
            $qDetMov .= "$cAlfa.fcod$nAno.comobsxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.sucidxxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.docidxxx, ";
            $qDetMov .= "$cAlfa.fcod$nAno.docsufxx  ";
            $qDetMov .= "FROM $cAlfa.fcod$nAno ";
            $qDetMov .= "WHERE ";
            $qDetMov .= "$cAlfa.fcod$nAno.comidxxx = \"{$xRCM['comidxxx']}\" AND ";
            $qDetMov .= "$cAlfa.fcod$nAno.comcodxx = \"{$xRCM['comcodxx']}\" AND ";
            $qDetMov .= "$cAlfa.fcod$nAno.comcscxx = \"{$xRCM['comcscxx']}\" AND ";
            $qDetMov .= "$cAlfa.fcod$nAno.comcsc2x = \"{$xRCM['comcsc2x']}\" AND ";
            if ($xRCM['comidxxx'] == "F" || (in_array("{$xRCM['comidxxx']}~{$xRCM['comcodxx']}",$vNotCre) == true)) {
              // Facturas y Notas Credito
              $qDetMov .= "$cAlfa.fcod$nAno.comctocx IN (\"IP\",\"PCC\") ";
            } else {
              // Ajustes contables
              $qDetMov .= "CONCAT($cAlfa.fcod$nAno.pucidxxx,\"~\",$cAlfa.fcod$nAno.ctoidxxx) IN ($cCtoCon) ";
            }
            $qDetMov .= "ORDER BY ABS(comseqxx) ";
            $nQueryTimeStart = microtime(true); $xDetMov = mysql_query($qDetMov,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
            // echo "\n\n".$qDetMov."~".mysql_num_rows($xDetMov);

            // Trayendo Detalle
            while ($xRDM = mysql_fetch_assoc($xDetMov)) {
              $nCanReg++;
              if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDbCargoWiseDsv($xConexion01); }

              //Descripcion
              $cCtoDes = ($mCtoCon["{$xRDM['pucidxxx']}~{$xRDM['ctoidxxx']}"]['ctodesxp'] != "") ? $mCtoCon["{$xRDM['pucidxxx']}~{$xRDM['ctoidxxx']}"]['ctodesxp'] : $mCtoCon["{$xRDM['pucidxxx']}~{$xRDM['ctoidxxx']}"]['ctodesxl']; // Descripcion
              $cCtoDes = ($cCtoDes == "") ? utf8_decode($xRDM['comobsxx']) : $cCtoDes;

              //Primer DO
              if ($cDocId == "" && $xRDM['docidxxx'] != "") {
                $cSucId  = $xRDM['sucidxxx']; 
                $cDocId  = $xRDM['docidxxx']; 
                $cDocSuf = $xRDM['docsufxx'];
              }

              //Datos comprobante
              $nInd_mDetMov = count($mDetMov);
              $mDetMov[$nInd_mDetMov]['interfaz'] = ($xRDM['comidxxx'] == "F") ? "FACTURAS" : "NOTAS_CREDITO"; // Interfaz
              $mDetMov[$nInd_mDetMov]['comidxxx'] = $xRDM['comidxxx'];               // Id del Comprobante
              $mDetMov[$nInd_mDetMov]['comcodxx'] = $xRDM['comcodxx'];               // Codigo del Comprobante
              $mDetMov[$nInd_mDetMov]['comcscxx'] = $xRDM['comcscxx'];               // Consecutivo Uno del Comprobante
              $mDetMov[$nInd_mDetMov]['comcsc2x'] = $xRDM['comcsc2x'];               // Consecutivo Dos del Comprobante
              $mDetMov[$nInd_mDetMov]['comfecxx'] = $xRCM['comfecxx'];               // Fecha del Comprobante
              $mDetMov[$nInd_mDetMov]['reghcrex'] = $xRCM['reghcrex'];               // Hora de Creacion del Registro
              $mDetMov[$nInd_mDetMov]['ccoidxxx'] = $xRCM['ccoidxxx'];               // Centro de Costo
              $mDetMov[$nInd_mDetMov]['sccidxxx'] = $xRCM['sccidxxx'];               // Sub Centro de Costo
              $mDetMov[$nInd_mDetMov]['sucidxxx'] = $xRDM['sucidxxx'];               // Sucursal DO
              $mDetMov[$nInd_mDetMov]['docidxxx'] = $xRDM['docidxxx'];               // Numero DO
              $mDetMov[$nInd_mDetMov]['docsufxx'] = $xRDM['docsufxx'];               // Sufijo DO
              $mDetMov[$nInd_mDetMov]['ctopccxx'] = "";                              // Concepto PCC
              $mDetMov[$nInd_mDetMov]['ctotrixx'] = "";                              // Concepto tributos
              $mDetMov[$nInd_mDetMov]['ctodesxx'] = trim($cCtoDes);                  // Descripcion
              $mDetMov[$nInd_mDetMov]['ctocwccx'] = $mCtoCon["{$xRDM['pucidxxx']}~{$xRDM['ctoidxxx']}"]['ctocwccx']; // Charge Codes Cargowise
              $mDetMov[$nInd_mDetMov]['clicwccx'] = $cTerCod;                        // Proveedor Charge Codes Cargowise
              $mDetMov[$nInd_mDetMov]['comvlrxx'] = $xRDM['comvlrxx'];               // Valor del Comprobante
              $mDetMov[$nInd_mDetMov]['comvlr01'] = $xRDM['comvlr01'];               // Valor del IVA
              $mDetMov[$nInd_mDetMov]['anioxxxx'] = substr($xRCM['comfecxx'], 0, 4); // Año del Comprobante
              $mDetMov[$nInd_mDetMov]['regestxx'] = $xRCM['regestxx'];               // Estado del Comprobante
              $mDetMov[$nInd_mDetMov]['archivox'] = $nIdArc;                         // Archivo
            }

            if (count($mDetMov) == 0) {
              $nSwitchAux = 1;
              $vError['LINEAERR'] = __LINE__;
              $vError['TIPOERRX'] = "FALLIDO";
              $vError['DESERROR'] = "El Comprobante [{$xRCM['comidxxx']}-{$xRCM['comcodxx']}-{$xRCM['comcscxx']}-{$xRCM['comcsc2x']}], Fecha [{$xRCM['comfecxx']}] No Existe en Detalle.";
              $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
            }

            if ($nSwitchAux == 0) {
              //Trayendo el tipo de operacion y el modo de transporte
              $qTramite  = "SELECT ";
              $qTramite .= "sucidxxx, ";
              $qTramite .= "docidxxx, ";
              $qTramite .= "docsufxx, ";
              $qTramite .= "doctipxx, ";
              $qTramite .= "docmtrxx ";
              $qTramite .= "FROM $cAlfa.sys00121 ";
              $qTramite .= "WHERE ";
              $qTramite .= "sucidxxx = \"$cSucId\" AND ";
              $qTramite .= "docidxxx = \"$cDocId\"  AND ";
              $qTramite .= "docsufxx = \"$cDocSuf\" ";
              $nQueryTimeStart = microtime(true); $xTramite = mysql_query($qTramite,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);

              if (mysql_num_rows($xTramite) == 0) {
                $nSwitchAux = 1;
                $vError['LINEAERR'] = __LINE__;
                $vError['TIPOERRX'] = "FALLIDO";
                $vError['DESERROR'] = "El Tramite [$cSucId-$cDocId-$cDocSuf] no Existe.";
                $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
              } else {
                $vTramite = mysql_fetch_assoc($xTramite);

                //Homologacion subcentros de costo
                //AEREO - IMPORTACION
                if ($vTramite['docmtrxx'] == "AEREO" && ($vTramite['doctipxx'] == "IMPORTACION" || $vTramite['doctipxx'] == "TRANSITO")) {
                  $cSccId  = "CIA";
                }
                //AEREO - EXPORTACION
                if ($vTramite['docmtrxx'] == "AEREO" && $vTramite['doctipxx'] == "EXPORTACION") {
                  $cSccId  = "CEA";
                }
                //TERRESTRE - IMPORTACION
                if ($vTramite['docmtrxx'] == "TERRESTRE" && ($vTramite['doctipxx'] == "IMPORTACION" || $vTramite['doctipxx'] == "TRANSITO")) {
                  $cSccId  = "CIT";
                }
                //TERRESTRE - EXPORTACION
                if ($vTramite['docmtrxx'] == "TERRESTRE" && $vTramite['doctipxx'] == "EXPORTACION") {
                  $cSccId  = "COT";
                }
                //MARITIMO - IMPORTACION
                if ($vTramite['docmtrxx'] == "MARITIMO" && ($vTramite['doctipxx'] == "IMPORTACION" || $vTramite['doctipxx'] == "TRANSITO")) {
                  $cSccId  = "CIS";
                }
                //MARITIMO - EXPORTACION
                if ($vTramite['docmtrxx'] == "MARITIMO" && $vTramite['doctipxx'] == "EXPORTACION") {
                  $cSccId  = "CES";
                }
              }
            }

            if ($nSwitchAux == 0) {
              // Insertando en la tabla temporal
              for($nD=0; $nD<count($mDetMov); $nD++) {
                if ($mDetMov[$nD]['docidxxx'] == "") {
                  $mDetMov[$nD]['sucidxxx'] = $cSucId;
                  $mDetMov[$nD]['docidxxx'] = $cDocId;
                  $mDetMov[$nD]['docsufxx'] = $cDocSuf;
                }

                // Datos comprobante
                $qInsDet .= "(";
                $qInsDet .= "\"{$mDetMov[$nD]['interfaz']}\","; // Interfaz
                $qInsDet .= "\"{$mDetMov[$nD]['comidxxx']}\","; // Id del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['comcodxx']}\","; // Codigo del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['comcscxx']}\","; // Consecutivo Uno del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['comcsc2x']}\","; // Consecutivo Dos del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['comfecxx']}\","; // Fecha del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['reghcrex']}\","; // Hora de Creacion del Registro
                $qInsDet .= "\"{$mDetMov[$nD]['ccoidxxx']}\","; // Centro de costo de cabecera
                $qInsDet .= "\"$cSccId\",";                     // Sub centro de costo de cabecera
                $qInsDet .= "\"{$mDetMov[$nD]['sucidxxx']}\","; // Sucursal DO
                $qInsDet .= "\"{$mDetMov[$nD]['docidxxx']}\","; // Numero DO
                $qInsDet .= "\"{$mDetMov[$nD]['docsufxx']}\","; // Sufijo DO
                $qInsDet .= "\"{$mDetMov[$nD]['ctopccxx']}\","; // Concepto PCC
                $qInsDet .= "\"{$mDetMov[$nD]['ctotrixx']}\","; // Concepto tributos
                $qInsDet .= "\"{$mDetMov[$nD]['ctodesxx']}\","; // Descripcion
                $qInsDet .= "\"{$mDetMov[$nD]['ctocwccx']}\","; // Charge Codes Cargowise
                $qInsDet .= "\"{$mDetMov[$nD]['clicwccx']}\","; // Charge Codes Cargowise
                $qInsDet .= "\"{$mDetMov[$nD]['comvlrxx']}\","; // Valor del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['comvlr01']}\","; // Valor del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['anioxxxx']}\","; // Año del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['regestxx']}\","; // Estado del Comprobante
                $qInsDet .= "\"{$mDetMov[$nD]['archivox']}\"), "; // Archivo complemento

                $nCanReg++;
                if (($nCanReg % _NUMREG_) == 0) {
                  $xConexion01 = $objTablasTemporales->fnReiniciarConexionDbCargoWiseDsv($xConexion01); 

                  $qInsert = $qInsCab.substr($qInsDet, 0, -2);
                  $nQueryTimeStart = microtime(true); $xInsert = mysql_query($qInsert,$xConexion01);
                  $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                  if (!$xInsert) {
                    $nSwitchAux = 1;
                    $vError['LINEAERR'] = __LINE__;
                    $vError['TIPOERRX'] = "FALLIDO";
                    $vError['DESERROR'] = "Error al insertar el Comprobante en la tabla temporal.";
                    $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                  }
                  $qInsDet = "";
                }
              }
            }

            if ($nSwitchAux == 1) {
              $nSwitch = 1;
            }
          }
        }

        if ($qInsDet != "") {
          $xConexion01 = $objTablasTemporales->fnReiniciarConexionDbCargoWiseDsv($xConexion01); 

          $qInsert = $qInsCab.substr($qInsDet, 0, -2);
          $nQueryTimeStart = microtime(true); $xInsert = mysql_query($qInsert,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
          if (!$xInsert) {
            $nSwitchAux = 1;
            $vError['LINEAERR'] = __LINE__;
            $vError['TIPOERRX'] = "FALLIDO";
            $vError['DESERROR'] = "Error al insertar el Comprobante en la tabla temporal.";
            $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
          }
        }
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    } ## function fnFacturas($cAlfa){

    /**
     * Metodo para Generar Archivo XML de las Causaciones.
     */
    function fnXmlComprobantes($pvDatos) {
      /**
       * Recibe una Matriz con las siguientes posiciones:
       * $pvDatos['basedato'] //Base de datos
       * $pvDatos['tablaerr'] //Tabla Error
       * $pvDatos['tablamov'] //Tabla Movimiento
       * $pvDatos['ejeprobg'] //Indica si el proceso es en background
       * $pvDatos['origenxx'] //Origen (FTP o Vacio), FTP indica que se ejecuta desde la tarea, vacio que se ejecuta desde el modulo de transmision
       * $pvDatos['interfaz'] //Indica el tipo de comprobante a generar
       */

      global $OPENINIT;

      /**
       * Variable para saber si hay o no errores de validacion.
       * 
       * @var integer
       */
      $nSwitch = 0;

      /**
       * Cantidad de registros para reinicio de conexion.
       * 
       * @var integer
       */
      $nCanReg = 0;

      /**
       * Hacer la conexion a la base de datos.
       */
      $xConexion01 = mysql_connect(OC_SERVER, OC_USERROBOT, OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);

      /**
       * Variable para hacer el retorno.
       * 
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = ""; // true - false

      /**
       * Variable con los nombres de los archivos generados.
       * 
       * @var array
       */
      $vFile = array();

      /**
       * Variable con los nombres de los archivos generados para el complemento de las facturas.
       * 
       * @var array
       */
      $vFileComplemento = array();

      /**
       * Base de datos.
       * 
       * @var string
       */
      $cAlfa = $pvDatos['basedato'];

      /**
       * Instanciando Objeto para la creacion de las tablas temporales.
       */
      $objTablasTemporales = new cEstructurasCargoWiseDsv();

      /**
       * Vector de Errores.
       * 
       * @var array
       */
      $vError = array();
      $vError['TABLAERR'] = $pvDatos['tablaerr'];

      /**
       * Vector con las variables del sistea requeridas.
       */
      $vSysStr = $this->fnVariablesSistema($cAlfa);

      //Se debe agrupar por el campo archivox, que corresponde al agrupamiento de los registros por archivo
      $qArchivos = "SELECT DISTINCT interfaz,archivox ";
      $qArchivos .= "FROM $cAlfa.{$pvDatos['tablamov']} ";
      $nQueryTimeStart = microtime(true); $xArchivos = mysql_query($qArchivos,$xConexion01);
      $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
      // echo "\n\n".$qArchivos."~".mysql_num_rows($xArchivos)."\n\n";

      while ($xRA = mysql_fetch_assoc($xArchivos)) {
        $qCabMov  = "SELECT * ";
        $qCabMov .= "FROM $cAlfa.{$pvDatos['tablamov']} ";
        $qCabMov .= "WHERE ";
        $qCabMov .= "archivox = \"{$xRA['archivox']}\" ";
        $qCabMov .= "ORDER BY comidxxx,ABS(comcodxx),ABS(comcscxx),ABS(comcsc2x)";
        $nQueryTimeStart = microtime(true); $xCabMov = mysql_query($qCabMov,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
        // echo "\n\n".$qCabMov."~".mysql_num_rows($xCabMov)."\n\n";
        
        // Variable de control para indicar cambio de comprobante en el detalle
        $cArchivo = $xRA['archivox'];

        if(mysql_num_rows($xCabMov) > 0) {
          //Se espera 1 segundo para que el nombre del archivo sea diferente por cada archivo
          sleep(1);

          //Creando Archivo
          if ($pvDatos['origenxx'] == "FTP") {
            //Se crea en propios
            $cDirectorio = "{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa/comprobantes_cargowise";

            if (!is_dir("{$OPENINIT['pathdr']}/opencomex/propios")) {
              mkdir("{$OPENINIT['pathdr']}/opencomex/propios");
              chmod("{$OPENINIT['pathdr']}/opencomex/propios", intval($vSysStr['system_permisos_directorios'], 8));
            }

            if (!is_dir("{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa")) {
              mkdir("{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa");
              chmod("{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa", intval($vSysStr['system_permisos_directorios'], 8));
            }

            if (!is_dir($cDirectorio)) {
              mkdir($cDirectorio);
              chmod($cDirectorio, intval($vSysStr['system_permisos_directorios'], 8));
            }

            //Borrando documentos del directorio  que no son del dia
            $vArchivos = array_slice(scandir($cDirectorio),2);
            for($nA = 0; $nA < count($vArchivos); $nA++){
              //Se borran todos los archivos que no hayan sido generados el dia actual
              if(substr_count($vArchivos[$nA],date('Ymd')) == 0) {
                $cFileDel = $cDirectorio . "/" . $vArchivos[$nA];
                if (file_exists($cFileDel)) {
                  unlink($cFileDel);
                }
              }
            }
          } else {
            //Se crea en Downloads
            if ($pvDatos['ejeprobg'] == "SI") {
              $cDirectorio = "{$OPENINIT['pathdr']}/opencomex/".$vSysStr['system_download_directory'];
            } else {
              $cDirectorio = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory'];
            }
          }

          $cFile = "Charges_".date('YmdHis').".xml";
          $cFileDownload = $cDirectorio."/".$cFile;
          if (file_exists($cFileDownload)) {
            unlink($cFileDownload);
          }
          $fpFac = fopen($cFileDownload,'a+');

          //Archivos generados
          $vFile[count($vFile)] = $cFile;

          $mDatos = array();
          while($xRCM = mysql_fetch_assoc($xCabMov)){
            $nCanReg++;
            if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDbCargoWiseDsv($xConexion01); }
            
            // Para las FACTURAS
            // Agrupando los PCC por por concepto y proveedor
            if ($xRCM['interfaz'] == "FACTURAS") {
              $nSwitch_Encontre_Concepto = 0;
              if ($xRCM['ctopccxx'] == "SI") {
                for ($nD=0; $nD<count($mDatos); $nD++) {
                  if ($mDatos[$nD]['ctocwccx'] == $xRCM['ctocwccx'] && $mDatos[$nD]['clicwccx'] == $xRCM['clicwccx']) {
                    $nSwitch_Encontre_Concepto = 1;
                    $mDatos[$nD]['comvlrxx'] += $xRCM['comvlrxx'];
                    $mDatos[$nD]['comvlr01'] += $xRCM['comvlr01'];
                  }
                }
              }

              if ($nSwitch_Encontre_Concepto == 0) {
                $nInd_mDatos = count($mDatos);
                $mDatos[$nInd_mDatos] = $xRCM;
              }
            } else {
              $nInd_mDatos = count($mDatos);
              $mDatos[$nInd_mDatos] = $xRCM;
            }
          }
          
          $cXml = "";
          $cFileComplemento = "";
          $cXmlComplemento  = "";
          for ($nD=0; $nD<count($mDatos); $nD++) {
            if ($cArchivo != "") {
              //Armando del XML informacion de cabecera
              $cXml .= "<ChargesMessage xmlns='http://OpenComex/ChargesMessage'>";
                $cXml .= "<Header>";
                  $cXml .= "<MessageId>".$vSysStr['dsvsasxx_contable_cw_messageid']."</MessageId>";
                  $cXml .= "<TimeStamp>".$mDatos[$nD]['comfecxx']."T".$mDatos[$nD]['reghcrex']."</TimeStamp>";
                  $cXml .= "<Target>";
                    $cXml .= "<Application>".$vSysStr['dsvsasxx_contable_cw_application']."</Application>";
                    $cXml .= "<Country>".$vSysStr['dsvsasxx_contable_cw_country']."</Country>";
                    $cXml .= "<Company>".$vSysStr['dsvsasxx_contable_cw_company']."</Company>";
                  $cXml .= "</Target>";
                $cXml .= "</Header>";
                $cXml .= "<Charges>";
                  $cXml .= "<JobReference>".$mDatos[$nD]['docidxxx']."</JobReference>";
                  $cXml .= "<OCReference>".$mDatos[$nD]['sucidxxx']."-".$mDatos[$nD]['docidxxx']."-".$mDatos[$nD]['docsufxx']."</OCReference>";

              // Se genera el XML de complemento solo con la información de cabecera
              if ($mDatos[$nD]['interfaz'] == "FACTURAS") {
                $cFileComplemento = "ReferenceNumbers_".date('YmdHis').".xml";
                $cFileDownloadComplemento = $cDirectorio."/".$cFileComplemento;
                if (file_exists($cFileDownloadComplemento)) {
                  unlink($cFileDownloadComplemento);
                }
                $fpComplemento = fopen($cFileDownloadComplemento,'a+');

                $cXmlComplemento = "";
                $cXmlComplemento .= "<ReferenceNumbersMessage xmlns='http://OpenComex/ManifestNumbersMessage'>";
                  $cXmlComplemento .= "<Header>";
                    $cXmlComplemento .= "<MessageId>".$vSysStr['dsvsasxx_contable_cw_messageid']."</MessageId>";
                    $cXmlComplemento .= "<TimeStamp>".$mDatos[$nD]['comfecxx']."T".$mDatos[$nD]['reghcrex']."</TimeStamp>";
                    $cXmlComplemento .= "<Target>";
                      $cXmlComplemento .= "<Application>".$vSysStr['dsvsasxx_contable_cw_application']."</Application>";
                      $cXmlComplemento .= "<Country>".$vSysStr['dsvsasxx_contable_cw_country']."</Country>";
                      $cXmlComplemento .= "<Company>".$vSysStr['dsvsasxx_contable_cw_company']."</Company>";
                    $cXmlComplemento .= "</Target>";
                  $cXmlComplemento .= "</Header>";
                  $cXmlComplemento .= "<ReferenceNumbers>";
                    $cXmlComplemento .= "<JobReference>".$mDatos[$nD]['docidxxx']."</JobReference>";
                    $cXmlComplemento .= "<OCReference>".$mDatos[$nD]['sucidxxx']."-".$mDatos[$nD]['docidxxx']."-".$mDatos[$nD]['docsufxx']."</OCReference>";
                    $cXmlComplemento .= "<ReferenceNumbers>";
                      $cXmlComplemento .= "<ReferenceNumber>";
                        $cXmlComplemento .= "<Type>INO</Type>";
                        $cXmlComplemento .= "<Value>".$mDatos[$nD]['comcodxx']."-".$mDatos[$nD]['comcscxx']."</Value>";
                      $cXmlComplemento .= "</ReferenceNumber>";
                    $cXmlComplemento .= "</ReferenceNumbers>";
                  $cXmlComplemento .= "</ReferenceNumbers>";
                $cXmlComplemento .= "</ReferenceNumbersMessage>";
              }
            }

            //Armando del XML informacion de detalle
            $cXml .= "<ChargeLines>";
              $cXml .= "<ChargeLine>";
                $cXml .= "<ChargeCode>".$mDatos[$nD]['ctocwccx']."</ChargeCode>";
                $cXml .= "<Description>".$mDatos[$nD]['ctodesxx']."</Description>";

                if ($mDatos[$nD]['interfaz'] == "FACTURAS") {
                  $cXml .= "<RevenueAmount Currency='COP'>".number_format($mDatos[$nD]['comvlrxx'], 3, '.', '')."</RevenueAmount>";
                  $cXml .= "<RevenueTaxAmount Currency='COP'>".number_format($mDatos[$nD]['comvlr01'], 3, '.', '')."</RevenueTaxAmount>";
                  $cXml .= "<DebtorCode>".$mDatos[$nD]['clicwccx']."</DebtorCode>";
                } else {
                  $cXml .= "<CostAmount Currency='COP'>".number_format($mDatos[$nD]['comvlrxx'], 3, '.', '')."</CostAmount>";
                  $cXml .= "<CostTaxAmount Currency='COP'>".number_format($mDatos[$nD]['comvlr01'], 3, '.', '')."</CostTaxAmount>";
                  $cXml .= "<CreditorCode>".$mDatos[$nD]['clicwccx']."</CreditorCode>";
                  $cXml .= "<DebtorCode>".$mDatos[$nD]['clicwccc']."</DebtorCode>";
                }

                $cXml .= "<BranchCode>".$mDatos[$nD]['ccoidxxx']."</BranchCode>";
                $cXml .= "<DepartmentCode>".$mDatos[$nD]['sccidxxx']."</DepartmentCode>";
              $cXml .= "</ChargeLine>";
            $cXml .= "</ChargeLines>";
            $cArchivo = "";
          } // for ($nD=0; $nD<count($mDatos); $nD++) {
          
            $cXml .= "</Charges>";
          $cXml .= "</ChargesMessage>";

          fwrite($fpFac,$cXml.chr(13).chr(10));

          //Se creo un archivo y hay que cerrarlo
          fclose($fpFac);
          if (file_exists($cFileDownload)){
            chmod($cFileDownload,intval($vSysStr['system_permisos_archivos'],8));
          }

          //Se escibe y crea el archivo de complemento
          if ($xRA['interfaz'] == "FACTURAS" && $cXmlComplemento != "") {
            //Archivos generados
            $vFileComplemento[count($vFileComplemento)] = $cFileComplemento;

            fwrite($fpComplemento,$cXmlComplemento.chr(13).chr(10));

            //Se creo un archivo y hay que cerrarlo
            fclose($fpComplemento);
            if (file_exists($cFileDownloadComplemento)){
              chmod($cFileDownload,intval($vSysStr['system_permisos_archivos'],8));
            }
          }

          //Actualizando todos los registros de la tabla con el nombre del archivo
          $qUpdCom  = "UPDATE $cAlfa.{$pvDatos['tablamov']} SET ";
          //Se actualiza el nombre del archivo complemento
          if ($xRA['interfaz'] == "FACTURAS" && $cXmlComplemento != "") {
            $qUpdCom .= "archicom = \"$cFileComplemento\", ";
          }
          $qUpdCom .= "archivox = \"$cFile\" ";
          $qUpdCom .= "WHERE ";
          $qUpdCom .= "archivox = \"{$xRA['archivox']}\" ";
          $nQueryTimeStart = microtime(true); $xUpdCom = mysql_query($qUpdCom,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
          if(!$xUpdCom){
            // echo "\nActualizando Cabecera: ".$qUpdCom;
            $nSwitchAux = 1;
            $vError['LINEAERR'] = __LINE__;
            $vError['TIPOERRX'] = "FALLIDO";
            $vError['DESERROR'] = "Error al Actualizar el nombre del Archivo en la tabla temporal.";
            $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
          }
        } else {
          $nSwitch = 1;
          $vError['LINEAERR'] = __LINE__;
          $vError['TIPOERRX'] = "FALLIDO";
          $vError['DESERROR'] = "No se encontraron Registros.";
          $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
        }
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
        $mReturn[1] = $cDirectorio;
        $mReturn[2] = $vFile;
        $mReturn[3] = $vFileComplemento;
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    } ## function fnXmlComprobantes($cAlfa){

    /**
     * Metodo para Enviar el Archivo XML al FTP.
     */
    function fnEnviarFtpCargoWiseDsv($pvDatos) {
      /**
       * Recibe una Matriz con las siguientes posiciones:
       * $pvDatos['basedato'] //Base de datos
       * $pvDatos['tablaerr'] //Tabla Error
       * $pvDatos['tablamov'] //Tabla Movimiento
       * $pvDatos['dirorixx'] //Directorio Origen
       * $pvDatos['dirdesxx'] //Directorio Destino
       * $pvDatos['hostxxxx'] //Host FTP
       * $pvDatos['puertoxx'] //Puerto FTP
       * $pvDatos['usuariox'] //Usuario FTP
       * $pvDatos['password'] //Password FTP
       * $pvDatos['tipoenvi'] //Password FTP
       */

      global $OPENINIT;

      /**
       * Variables para reemplazar caracteres especiales
       * @var array
       */
      $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
      $cReempl = array('\"',"\'"," "," "," "," ");

      /**
       * Variable para saber si hay o no errores de validacion.
       * @var number
       */
      $nSwitch    = 0;
      $nSwitchAux = 0;

      /**
       * Base de datos.
       * 
       * @var string
       */
      $cAlfa = $pvDatos['basedato'];

      /**
       * Vector con las variables del sistea requeridas.
       */
      $vSysStr = $this->fnVariablesSistema($cAlfa);

      /**
       * Hacer la conexion a la base de datos
       */
      $xConexion01 = mysql_connect(OC_SERVER, OC_USERROBOT, OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);

      /**
       * Variable para hacer el retorno.
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = ""; // true - false

      /**
       * Instanciando Objeto para la creacion de las tablas temporales.
       */
      $objTablasTemporales = new cEstructurasCargoWiseDsv();

      /**
       * Vector de Errores
       * @var array
       */
      $vError = array();
      $vError['TABLAERR'] = $pvDatos['tablaerr'];

      $cCampArc = ($pvDatos['tipoenvi'] == "COMPLEMENTO") ? 'archicom' : 'archivox';
      $cCamEftp = ($pvDatos['tipoenvi'] == "COMPLEMENTO") ? 'envftpco' : 'envftpxx';

      //Estableciendo conexion con el FTP
      $id_ftp = ftp_connect($pvDatos['hostxxxx'],$pvDatos['puertoxx']) or $nSwitch=1; //Obtiene un manejador del Servidor FTP

      //Reiniciar conexion
      $xConexion01 = $objTablasTemporales->fnReiniciarConexionDbCargoWiseDsv($xConexion01);

      if ($nSwitch == 0) {
        $id_ftp_login = ftp_login($id_ftp,$pvDatos['usuariox'],$pvDatos['password']) or $nSwitch=1; //Se loguea al Servidor FTP

        //Reiniciar conexion
        $xConexion01 = $objTablasTemporales->fnReiniciarConexionDbCargoWiseDsv($xConexion01);
          
        if ($nSwitch == 0) {
          ftp_pasv($id_ftp,true); //Establece el modo de conexion
          
          /**
           * Creando carpeta destino por si no existe
           */
          if (ftp_mkdir($id_ftp, $pvDatos['dirdesxx'])) {
            //Creando Carpeta
          }

          $qArchivos  = "SELECT comidxxx, comcodxx, comcscxx, comcsc2x, comfecxx, $cCampArc ";
          $qArchivos .= "FROM $cAlfa.{$pvDatos['tablamov']} ";
          $qArchivos .= "GROUP BY $cCampArc ";
          $nQueryTimeStart = microtime(true); $xArchivos = mysql_query($qArchivos,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
          // echo "\n\n".$qArchivos."~".mysql_num_rows($xArchivos)."\n\n";

          while ($xRA = mysql_fetch_assoc($xArchivos)) {
            $nSwitchAux = 0;

            //validando que el archivo origen existe
            if (!file_exists($pvDatos['dirorixx']."/".$xRA[$cCampArc])) {
              $nSwitchAux = 1;
              $vError['LINEAERR'] = __LINE__;
              $vError['TIPOERRX'] = "FALLIDO";
              $vError['DESERROR'] = "El archivo origen [{$pvDatos[$cCampArc]}] no existe.";
              $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
            }
    
            if ($nSwitchAux == 0) {
              // echo "Destino: ".$pvDatos['dirdesxx']."/".$xRA[$cCampArc]." Origen: ".$pvDatos['dirorixx']."/".$xRA[$cCampArc]."\n";
              //Si el archivo destino ya existe, se borra
              $cFtpD = ftp_delete($id_ftp, $pvDatos['dirdesxx']."/".$xRA[$cCampArc]); //Entro Borrando, me aseguro que el archivo no exista.
              if (ftp_put($id_ftp,$pvDatos['dirdesxx']."/".$xRA[$cCampArc],$pvDatos['dirorixx']."/".$xRA[$cCampArc],FTP_BINARY)){    //FTP_ASCII //FTP_BINARY
                //Subio
                $vError['LINEAERR'] = __LINE__;
                $vError['TIPOERRX'] = "EXITOSO";
                $vError['DESERROR'] = "Documento: [{$xRA['comidxxx']}-{$xRA['comcodxx']}-{$xRA['comcscxx']}-{$xRA['comcsc2x']}], Fecha: [{$xRA['comfecxx']}], Archivo: [{$xRA[$cCampArc]}].";
                $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
              
                // Actualizando marca de enviado a FTP
                $qUpdCom  = "UPDATE $cAlfa.{$pvDatos['tablamov']} SET ";
                $qUpdCom .= "$cCamEftp = \"FTP\" ";
                $qUpdCom .= "WHERE ";
                $qUpdCom .= "$cCampArc = \"{$xRA[$cCampArc]}\" ";
                $nQueryTimeStart = microtime(true); $xUpdCom = mysql_query($qUpdCom,$xConexion01);
                $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                if(!$xUpdCom){
                  // echo "\nActualizando Cabecera: ".$qUpdCom;
                  $nSwitchAux = 1;
                  $vError['LINEAERR'] = __LINE__;
                  $vError['TIPOERRX'] = "FALLIDO";
                  $vError['DESERROR'] = "Error al Actualizar el archivo [{$xRA[$cCampArc]}] con enviado a FTP.";
                  $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                }
              } else {
                $nSwitchAux = 1;
                $vError['LINEAERR'] = __LINE__;
                $vError['TIPOERRX'] = "FALLIDO";
                $vError['DESERROR'] = "Error al copiar el archivo [{$xRA[$cCampArc]}] al FTP.";
                $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
              }
            }
    
            if ($nSwitchAux == 1) {
              $nSwitch = 1;
            }
          }
        } else {
          $nSwitch = 1;
          $vError['LINEAERR'] = __LINE__;
          $vError['TIPOERRX'] = "FALLIDO";
          $vError['DESERROR'] = "Error al Iniciar Sesion en el Servidor FTP.";
          $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
        }
      } else {
        $nSwitch = 1;
        $vError['LINEAERR'] = __LINE__;
        $vError['TIPOERRX'] = "FALLIDO";
        $vError['DESERROR'] = "Error de Conexion con el Servidor FTP.";
        $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
      }
      ftp_quit($id_ftp);

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    } ## function fnEnviarFtpCargoWiseDsv($cAlfa){

    /**
     * Metodo para legalizar las facturas alojadas en el FTP.
     */
    function fnLegalizarFacturas($pvDatos) {
      /**
       * Recibe una Matriz con las siguientes posiciones:
       * $pvDatos['basedato'] //Base de datos
       * $pvDatos['tablaerr'] //Tabla Error
       * $pvDatos['dirinxxx'] //Directorio In
       * $pvDatos['hostxxxx'] //Host FTP
       * $pvDatos['puertoxx'] //Puerto FTP
       * $pvDatos['usuariox'] //Usuario FTP
       * $pvDatos['password'] //Password FTP
       */

      global $OPENINIT;

      /**
       * Variables para reemplazar caracteres especiales.
       * 
       * @var array
       */
      $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
      $cReempl = array('\"',"\'"," "," "," "," ");

      /**
       * Variable para saber si hay o no errores de validacion.
       * 
       * @var number
       */
      $nSwitch = 0;

      /**
       * Base de datos.
       * 
       * @var string
       */
      $cAlfa = $pvDatos['basedato'];

      /**
       * Vector con las variables del sistea requeridas.
       */
      $vSysStr = $this->fnVariablesSistema($cAlfa);

      /**
       * Hacer la conexion a la base de datos
       */
      $xConexion01 = mysql_connect(OC_SERVER, OC_USERROBOT, OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);

      /**
       * Variable para hacer el retorno.
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = ""; // true - false

      /**
       * Instanciando Objeto para la creacion de las tablas temporales.
       */
      $objTablasTemporales = new cEstructurasCargoWiseDsv();

      /**
       * Vector de Errores
       * @var array
       */
      $vError = array();
      $vError['TABLAERR'] = $pvDatos['tablaerr'];

      // Estableciendo conexion con el FTP
      $id_ftp = ftp_connect($pvDatos['hostxxxx'],$pvDatos['puertoxx']) or $nSwitch=1; //Obtiene un manejador del Servidor FTP

      if ($nSwitch == 0) {
        $id_ftp_login = ftp_login($id_ftp,$pvDatos['usuariox'],$pvDatos['password']) or $nSwitch=1; //Se loguea al Servidor FTP
          
        if ($nSwitch == 0) {
          ftp_pasv($id_ftp,true); //Establece el modo de conexion
          $arrFiles = ftp_nlist($id_ftp, $pvDatos['dirinxxx']);

          if(count($arrFiles) > 0) {
            foreach ($arrFiles as $cFile) {
              if ($cFile != "." && $cFile != "..") {
                $vDatArc = array();
                $vDatArc = explode("/", $cFile);
                $cFileExt = strtolower(substr(strrchr($vDatArc[count($vDatArc)-1], "."), 1));
                // echo "Archivo: ".$vDatArc[count($vDatArc)-1]." Extension: ".$cFileExt."\n";
                if ($cFileExt == "xml") {
                  // Error x cada Documento
                  $nError = 0;

                  // Archivos procesados
                  $vError['LINEAERR'] = __LINE__;
                  $vError['TIPOERRX'] = "ARCHIVO";
                  $vError['DESERROR'] = $vDatArc[count($vDatArc)-1];
                  $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                  
                  $cFileDown = "{$OPENINIT['pathdr']}/opencomex/downloads/".$vDatArc[count($vDatArc)-1];
									if (file_exists($cFileDown)){ 
											unlink($cFileDown);
									}

                  if (!ftp_get($id_ftp, $cFileDown, $cFile, FTP_BINARY)) {
                    $nError = 1;
                    $vError['LINEAERR'] = __LINE__;
                    $vError['TIPOERRX'] = "FALLIDO";
                    $vError['DESERROR'] = "Error al Copiar Archivo al Local $cFileDown.";
                    $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
									} else {
                    if (file_exists($cFileDown)) {
                      // Se obtiene la informacion del XML
                      $oXml = simplexml_load_file($cFileDown);

                      if (!isset($oXml->InvoiceCompliance->InvoiceReference)) {
                        $nError = 1;
                        $vError['LINEAERR'] = __LINE__;
                        $vError['TIPOERRX'] = "FALLIDO";
                        $vError['DESERROR'] = "En el Archivo [{$vDatArc[count($vDatArc)-1]}] no existe el elemento InvoiceCompliance->InvoiceReference.";
                        $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                      } else {
                        if (empty($oXml->InvoiceCompliance->InvoiceReference)) {
                          $nError = 1;
                          $vError['LINEAERR'] = __LINE__;
                          $vError['TIPOERRX'] = "FALLIDO";
                          $vError['DESERROR'] = "Para el Archivo [{$vDatArc[count($vDatArc)-1]}] elemento InvoiceCompliance->InvoiceReference no puede ser vacio.";
                          $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                        }
                      }

                      if (!isset($oXml->InvoiceCompliance->ComplianceNumber)) {
                        $nError = 1;
                        $vError['LINEAERR'] = __LINE__;
                        $vError['TIPOERRX'] = "FALLIDO";
                        $vError['DESERROR'] = "En el Archivo [{$vDatArc[count($vDatArc)-1]}] no existe el elemento InvoiceCompliance->ComplianceNumber.";
                        $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                      } else {
                        if (empty($oXml->InvoiceCompliance->ComplianceNumber)) {
                          $nError = 1;
                          $vError['LINEAERR'] = __LINE__;
                          $vError['TIPOERRX'] = "FALLIDO";
                          $vError['DESERROR'] = "Para el Archivo [{$vDatArc[count($vDatArc)-1]}] elemento InvoiceCompliance->ComplianceNumber no puede ser vacio.";
                          $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                        }
                      }

                      if (!isset($oXml->InvoiceCompliance->InvoiceDate)) {
                        $nError = 1;
                        $vError['LINEAERR'] = __LINE__;
                        $vError['TIPOERRX'] = "FALLIDO";
                        $vError['DESERROR'] = "En el Archivo [{$vDatArc[count($vDatArc)-1]}] no existe el elemento InvoiceCompliance->InvoiceDate.";
                        $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                      } else {
                        if (empty($oXml->InvoiceCompliance->InvoiceDate)) {
                          $nError = 1;
                          $vError['LINEAERR'] = __LINE__;
                          $vError['TIPOERRX'] = "FALLIDO";
                          $vError['DESERROR'] = "Para el Archivo [{$vDatArc[count($vDatArc)-1]}] elemento InvoiceCompliance->InvoiceDate no puede ser vacio.";
                          $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                        }
                      }
                      
                      if ($nError == 0) {
                        $vInvRef = explode("-", str_replace($cBuscar,$cReempl,$oXml->InvoiceCompliance->InvoiceReference));
                        $cComCod = $vInvRef[0];
                        $cComCsc = $vInvRef[1];
                        $cNewCsc = str_replace($cBuscar,$cReempl,$oXml->InvoiceCompliance->ComplianceNumber);
              
                        // El documento (Factura) debe existir en la base de datos, se busca los 2 ultimos años
                        $nAnioActual = date('Y');
                        $nAnioAnterior = (($nAnioActual - 1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($nAnioActual - 1);
              
                        // Array de tramites
                        $mTramites = array();
            
                        $nCanReg   = 0; 
                        $nEncontro = 0;
                        $mDatos    = array();
                        for ($nAnio = $nAnioActual; $nAnio >= $nAnioAnterior; $nAnio--) {
                          $nCanReg++;
                          if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDbCargoWiseDsv($xConexion01); }
              
                          $qCabFac  = "SELECT ";
                          $qCabFac .= "comidxxx, ";
                          $qCabFac .= "comcodxx, ";
                          $qCabFac .= "comcscxx, ";
                          $qCabFac .= "comcsc2x, ";
                          $qCabFac .= "comfecxx, ";
                          $qCabFac .= "comfecve, ";
                          $qCabFac .= "comobs2x, ";
                          $qCabFac .= "comfpxxx, ";
                          $qCabFac .= "comealpo, ";
                          $qCabFac .= "regestxx ";
                          $qCabFac .= "FROM $cAlfa.fcoc$nAnio ";
                          $qCabFac .= "WHERE ";
                          $qCabFac .= "comidxxx = \"F\" AND ";
                          $qCabFac .= "comcodxx = \"$cComCod\" AND ";
                          $qCabFac .= "comcscxx = \"$cComCsc\" AND ";
                          $qCabFac .= "comcsc2x = \"$cComCsc\" AND ";
                          $qCabFac .= "comealpo = \"PENDIENTE\" AND ";
                          $qCabFac .= "regestxx = \"PROVISIONAL\" LIMIT 0,1";
                          $nQueryTimeStart = microtime(true); $xCabFac = mysql_query($qCabFac,$xConexion01);
                          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                          // echo $qCabFac."~".mysql_num_rows($xCabFac)."\n\n";
                          if (mysql_num_rows($xCabFac) > 0) {
                            $nEncontro++;
                            $vCabFac   = mysql_fetch_assoc($xCabFac);
                            $mDatos[]  = $vCabFac;
            
                            //Buscando el DO de la factura
                            $mDosFac = $this->fnExplodeArray($vCabFac['comfpxxx'],"|","~");
                            for ($n=0; $n<count($mDosFac); $n++) {
                              if ($mDosFac[$n][0] != "") {
                                //Trayendo sucursal y centro de costo del primer DO
                                $qTramite  = "SELECT ";
                                $qTramite .= "sucidxxx, ";
                                $qTramite .= "docidxxx, ";
                                $qTramite .= "docsufxx, ";
                                $qTramite .= "doctipxx, ";
                                $qTramite .= "docpedxx, ";
                                $qTramite .= "ccoidxxx, ";
                                $qTramite .= "regfcrex ";
                                $qTramite .= "FROM $cAlfa.sys00121 ";
                                $qTramite .= "WHERE ";
                                $qTramite .= "sucidxxx = \"{$mDosFac[0][15]}\" AND ";
                                $qTramite .= "docidxxx = \"{$mDosFac[0][2]}\"  AND ";
                                $qTramite .= "docsufxx = \"{$mDosFac[0][3]}\" ";
                                $nQueryTimeStart = microtime(true); $xTramite = mysql_query($qTramite,$xConexion01);
                                $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
            
                                if (mysql_num_rows($xTramite) == 0) {
                                  $nError = 1;
                                  $vError['LINEAERR'] = __LINE__;
                                  $vError['TIPOERRX'] = "FALLIDO";
                                  $vError['DESERROR'] = "El Tramite [{$mDosFac[0][15]}-{$mDosFac[0][2]}-{$mDosFac[0][3]}] no Existe.";
                                  $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                                } else {
                                  $vTramite = mysql_fetch_assoc($xTramite);
                                  $nInd_mTramites = count($mTramites);
                                  $mTramites[$nInd_mTramites] = $vTramite;
                                }
                              }
                            }
                          }
                        }
            
                        // Actualiza consecutivos del documento al consecutivo definitivo
                        // El estado lo cambia ACTIVO
                        // Crea CxC o CxP
                        if ($nEncontro == 1 && $nError == 0) {
                          foreach ($mDatos as $data) {
            
                            $nAnio = substr($data['comfecxx'],0,4);
            
                            // Calculando la nueva fecha de vencimiento
                            // calculo timestam de las dos fechas
                            $vFecha    = explode("-",$data['comfecxx']);
                            $dComFec   = mktime(0,0,0,$vFecha[1],$vFecha[2],$vFecha[0]);
                            $vFechaVe  = explode("-",$data['comfecve']);
                            $dComFecVe = mktime(0,0,0,$vFechaVe[1],$vFechaVe[2],$vFechaVe[0]);
              
                            // resto a una fecha la otra
                            $nDiferencia = $dComFecVe - $dComFec;
              
                            // convierto segundos en dias
                            $nDias = round($nDiferencia / (60 * 60 * 24));
              
                            // Nueva fecha de vencimiento
                            $dFechaXml = str_replace($cBuscar,$cReempl,$oXml->InvoiceCompliance->InvoiceDate);
                            $dFechaXml = str_replace('T', ' ', $dFechaXml);
            
                            $vFecha    = explode(" ", $dFechaXml);
                            $vNueFec   = explode("-", $vFecha[0]);
                            $nNueFecVe = date('Y-m-d',mktime(0,0,0,$vNueFe[1],$vNueFec[2]+$nDias,$vNueFec[0]));
              
                            // Actualiza el comprobante en Cabecera
                            $qUpdate  = "UPDATE $cAlfa.fcoc$nAnio SET ";
                            $qUpdate .= "comcscxx = \"$cNewCsc\", ";
                            $qUpdate .= "comcsc2x = \"$cNewCsc\", ";
                            $qUpdate .= "comfecxx = \"{$vFecha[0]}\", ";
                            $qUpdate .= "comfecve = \"$nNueFecVe\", ";
                            $qUpdate .= "comfacpr = \"{$data['comidxxx']}-{$data['comcodxx']}-{$data['comcscxx']}-{$data['comcsc2x']}\", ";
                            $qUpdate .= "comfprfe = \"{$data['comfecxx']}\", ";
                            $qUpdate .= "regestxx = \"ACTIVO\" ";
                            $qUpdate .= "WHERE ";
                            $qUpdate .= "comidxxx = \"{$data['comidxxx']}\" AND ";
                            $qUpdate .= "comcodxx = \"{$data['comcodxx']}\" AND ";
                            $qUpdate .= "comcscxx = \"{$data['comcscxx']}\" AND ";
                            $qUpdate .= "comcsc2x = \"{$data['comcsc2x']}\"";
                            $nQueryTimeStart = microtime(true); $xUpdate = mysql_query($qUpdate,$xConexion01);
                            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
            
                            if (!$xUpdate) {
                              $nError = 1;
                              $vError['LINEAERR'] = __LINE__;
                              $vError['TIPOERRX'] = "FALLIDO";
                              $vError['DESERROR'] = "Error al actualizar el Consecutivo Definitivo [$cNewCsc], Consecutivo Inicial [$cComCod-$cComCsc] en la tabla de Cabecera.";
                              $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                            }
            
                            // Actualiza el comprobante en Detalle
                            $qUpdate  = "UPDATE $cAlfa.fcod$nAnio SET ";
                            $qUpdate .= "comcscxx = \"$cNewCsc\", ";
                            $qUpdate .= "comcsc2x = \"$cNewCsc\", ";
                            $qUpdate .= "comfecxx = \"{$vFecha[0]}\", ";
                            $qUpdate .= "comfecve = \"$nNueFecVe\", ";
                            $qUpdate .= "regestxx = \"ACTIVO\" ";
                            $qUpdate .= "WHERE ";
                            $qUpdate .= "comidxxx = \"{$data['comidxxx']}\" AND ";
                            $qUpdate .= "comcodxx = \"{$data['comcodxx']}\" AND ";
                            $qUpdate .= "comcscxx = \"{$data['comcscxx']}\" AND ";
                            $qUpdate .= "comcsc2x = \"{$data['comcsc2x']}\"";
                            $nQueryTimeStart = microtime(true); $xUpdate = mysql_query($qUpdate,$xConexion01);
                            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                            if (!$xUpdate) {
                              $nError = 1;
                              $vError['LINEAERR'] = __LINE__;
                              $vError['TIPOERRX'] = "FALLIDO";
                              $vError['DESERROR'] = "Error al actualizar el Consecutivo Definitivo [$cNewCsc], Consecutivo Inicial [$cComCod-$cComCsc] en la tabla de Detalle.";
                              $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                            }
            
                            // Actualiza el campo de la factura en Cabecera
                            $qUpdate  = "UPDATE $cAlfa.fcoc$nAnio SET ";
                            $qUpdate .= "comfacxx = \"{$data['comidxxx']}-{$data['comcodxx']}-$cNewCsc-$cNewCsc\" ";
                            $qUpdate .= "WHERE ";
                            $qUpdate .= "comfacxx = \"{$data['comidxxx']}-{$data['comcodxx']}-{$data['comcscxx']}-{$data['comcsc2x']}\"";
                            $nQueryTimeStart = microtime(true); $xUpdate = mysql_query($qUpdate,$xConexion01);
                            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                            if (!$xUpdate) {
                              $nError = 1;
                              $vError['LINEAERR'] = __LINE__;
                              $vError['TIPOERRX'] = "FALLIDO";
                              $vError['DESERROR'] = "Error al actualizar el Consecutivo Definitivo [{$data['comidxxx']}-{$data['comcodxx']}-$cNewCsc-$cNewCsc].";
                              $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                            }
            
                            // Actualiza el campo de la factura en Detalle
                            $qUpdate  = "UPDATE $cAlfa.fcod$nAnio SET ";
                            $qUpdate .= "comfacxx = \"{$data['comidxxx']}-{$data['comcodxx']}-$cNewCsc-$cNewCsc\" ";
                            $qUpdate .= "WHERE ";
                            $qUpdate .= "comfacxx = \"{$data['comidxxx']}-{$data['comcodxx']}-{$data['comcscxx']}-{$data['comcsc2x']}\"";
                            $nQueryTimeStart = microtime(true); $xUpdate = mysql_query($qUpdate,$xConexion01);
                            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                            if (!$xUpdate) {
                              $nError = 1;
                              $vError['LINEAERR'] = __LINE__;
                              $vError['TIPOERRX'] = "FALLIDO";
                              $vError['DESERROR'] = "Error al actualizar el Consecutivo Definitivo [{$data['comidxxx']}-{$data['comcodxx']}-$cNewCsc-$cNewCsc].";
                              $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                            }
            
                            // Actualiza el consecutivo cruce en Detalle
                            $qUpdate  = "UPDATE $cAlfa.fcod$nAnio SET ";
                            $qUpdate .= "comcsccx = \"$cNewCsc\" ";
                            $qUpdate .= "WHERE ";
                            $qUpdate .= "comidcxx = \"{$data['comidxxx']}\" AND ";
                            $qUpdate .= "comcodcx = \"{$data['comcodxx']}\" AND ";
                            $qUpdate .= "comcsccx = \"{$data['comcscxx']}\"";
                            $nQueryTimeStart = microtime(true); $xUpdate = mysql_query($qUpdate,$xConexion01);
                            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                            if (!$xUpdate) {
                              $nError = 1;
                              $vError['LINEAERR'] = __LINE__;
                              $vError['TIPOERRX'] = "FALLIDO";
                              $vError['DESERROR'] = "Error al actualizar el Consecutivo Cruece Definitivo [$cNewCsc], Consecutivo Inicial [$cComCod-$cComCsc].";
                              $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                            }
            
                            // Actualiza el consecutivo cruce dos en Detalle
                            $qUpdate  = "UPDATE $cAlfa.fcod$nAnio SET ";
                            $qUpdate .= "comcscc2 = \"$cNewCsc\" ";
                            $qUpdate .= "WHERE ";
                            $qUpdate .= "comidc2x = \"{$data['comidxxx']}\" AND ";
                            $qUpdate .= "comcodc2 = \"{$data['comcodxx']}\" AND ";
                            $qUpdate .= "comcscc2 = \"{$data['comcscxx']}\"";
                            $nQueryTimeStart = microtime(true); $xUpdate = mysql_query($qUpdate,$xConexion01);
                            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                            if (!$xUpdate) {
                              $nError = 1;
                              $vError['LINEAERR'] = __LINE__;
                              $vError['TIPOERRX'] = "FALLIDO";
                              $vError['DESERROR'] = "Error al actualizar el Consecutivo Cruece Dos Definitivo [$cNewCsc], Consecutivo Inicial [$cComCod-$cComCsc].";
                              $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                            }
            
                            // Actualiza el numero de la factura definitiva para el DO
                            $qUpdate  = "UPDATE $cAlfa.sys00121 SET ";
                            $qUpdate .= "docfacxx = \"{$data['comidxxx']}-{$data['comcodxx']}-$cNewCsc-$cNewCsc\" ";
                            $qUpdate .= "WHERE ";
                            $qUpdate .= "docfacxx = \"{$data['comidxxx']}-{$data['comcodxx']}-{$data['comcscxx']}-{$data['comcsc2x']}\"";
                            $nQueryTimeStart = microtime(true); $xUpdate = mysql_query($qUpdate,$xConexion01);
                            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                            if (!$xUpdate) {
                              $nError = 1;
                              $vError['LINEAERR'] = __LINE__;
                              $vError['TIPOERRX'] = "FALLIDO";
                              $vError['DESERROR'] = "Error al actualizar el Numero de la Factura en el DO.";
                              $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                            }
            
                            if ($nSwitch == 0) {
                              // Consulta principal para traer las cuentas por Cobrar o por Pagar
                              $qFcod  = "SELECT ";
                              $qFcod .= "$cAlfa.fcod$nAnio.comidcxx, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.comcodcx, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.comcsccx, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.comseqcx, ";
                              $qFcod .= "$cAlfa.fpar0115.pucdetxx, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.puctipej, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.commovxx, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.pucidxxx, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.teridxxx, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.terid2xx, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.comvlrxx, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.comvlrnf, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.comfecxx, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.comfecve, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.ccoidxxx, ";
                              $qFcod .= "$cAlfa.fcod$nAnio.sccidxxx, ";
                              $qFcod .= "$cAlfa.fpar0115.pucdetxx ";
                              $qFcod .= "FROM $cAlfa.fcod$nAnio ";
                              $qFcod .= "LEFT JOIN $cAlfa.fpar0115 ON CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) = $cAlfa.fcod$nAnio.pucidxxx ";
                              $qFcod .= "WHERE ";
                              $qFcod .= "$cAlfa.fpar0115.pucdetxx IN (\"P\",\"C\") AND ";
                              $qFcod .= "$cAlfa.fcod$nAnio.comidxxx = \"{$data['comidxxx']}\" AND ";
                              $qFcod .= "$cAlfa.fcod$nAnio.comcodxx = \"{$data['comcodxx']}\" AND ";
                              $qFcod .= "$cAlfa.fcod$nAnio.comcscxx = \"$cNewCsc\" AND ";
                              $qFcod .= "$cAlfa.fcod$nAnio.comcsc2x = \"$cNewCsc\"";
                              $nQueryTimeStart = microtime(true); $xFcod = mysql_query($qFcod,$xConexion01);
                              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                              while ($xRDC = mysql_fetch_assoc($xFcod)) {
                                // Si la CxP o CxC no existe se crea
                                // Si ya existe, se actualiza
                                if ($xRDC['pucdetxx'] == "C") {
                                  $cTable  = "fcxc0000";
                                  $cModulo = "CxC";
                                } else {
                                  $cTable  = "fcxp0000";
                                  $cModulo = "CxP";
                                }
            
                                $nSaldo = 0; $nSaldoNF = 0;
                                // Primero pregunto si existe la $cModulo en la fcxc0000.
                                $qModulo  = "SELECT * ";
                                $qModulo .= "FROM $cAlfa.$cTable ";
                                $qModulo .= "WHERE ";
                                $qModulo .= "comidxxx = \"{$xRDC['comidcxx']}\" AND ";
                                $qModulo .= "comcodxx = \"{$xRDC['comcodcx']}\" AND ";
                                $qModulo .= "comcscxx = \"{$xRDC['comcsccx']}\" AND ";
                                $qModulo .= "comseqxx = \"{$xRDC['comseqcx']}\" AND ";
                                $qModulo .= "teridxxx = \"{$xRDC['teridxxx']}\" AND ";
                                $qModulo .= "pucidxxx = \"{$xRDC['pucidxxx']}\" ";
                                $qModulo .= "ORDER BY comidxxx,comcodxx,comcscxx,comseqxx LIMIT 0,1";
                                $nQueryTimeStart = microtime(true); $xModulo = mysql_query($qModulo,$xConexion01);
                                $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                                if (mysql_num_rows($xModulo) > 0) {
                                  // Cuando Encuentro el Registro Actualizo el Saldo.
                                  $vModulo = mysql_fetch_assoc($xModulo);
            
                                  $nCrear = 0;
                                  if ($xRDC['puctipej'] == "N" && ($vModulo['puctipej'] == "L" || $vModulo['puctipej'] == "")){
                                    // No permite crear si el concepto tiene ejecucion Niif y la cuenta [CxC o CxP] es ejecucion LOCAL o AMBAS
                                    $nCrear = 1;
                                  }
            
                                  if ($vModulo['puctipej'] == "N" && ($xRDC['puctipej'] == "L" || $xRDC['puctipej'] == "")){
                                    // No permite crear si el concepto tiene ejecucion LOCAL o AMBAS y la cuenta [CxC o CxP] es ejecucion Niff
                                    $nCrear = 1;
                                  }
            
                                  // Valido que el tipo de ejecucion de la [CxC o CxP] del comprobante sea el mismo que tiene la [CxC o CxP]
                                  // Si la ejecucion es LOCAL, se permite que la otra sea AMBAS y viceversa
                                  // No se permite LOCAL con NIIF, ni AMBAS con NIIF, porque para LOCAL y AMBAS predomina el valor Local
                                  if ($nCrear == 0) {
                                    // Pregunto la Naturaleza del Comprobante, los Debitos son Positivos y los Creditos con Negativos
                                    // En las CxC los Debitos Aumentan la CxC y los Creditos Disminuyen la CxC
                                    // La CxC es de Naturaleza Debito por tal Motivo los Valores son Positivos
                                    // En las CxP los Debitos Disminuyen la CxP y los Creditos Aumentan la CxP
                                    // La CxP es de Naturaleza Credito por tal Motivo los Valores son Negativos
                                    switch ($xRDC['commovxx']) {
                                      case "D":
                                        // Para las CxC los debitos aumentan.
                                        // Para las CxP los debitos disminuyen.
                                        // Se incluye el valor NIIF
                                        $nSaldo   = ($vModulo['comsaldo'] + $xRDC['comvlrxx']);
                                        $nSaldoNF = ($vModulo['comsalnf'] + $xRDC['comvlrnf']);
                                      break;
                                      case "C":
                                        // Para las CxC los creditos disminuyen.
                                        // Para las CxP los creditos aumentan.
                                        // Se incluye el valor NIIF
                                        $nSaldo   = ($vModulo['comsaldo'] - $xRDC['comvlrxx']);
                                        $nSaldoNF = ($vModulo['comsalnf'] - $xRDC['comvlrnf']);
                                      break;
                                    }
            
                                    // Valido que tipo de movimiento debo mandar a la $cModulo, este depende del tipo de ejecuion.
                                    if ($vModulo['puctipej'] == "L" || $vModulo['puctipej'] == "") { //se realizo con ejecucion LOCAL o AMBAS
                                      // Si el tipo de ejecuion es LOCAL o AMBAS, y el saldo del valor local queda en cero,
                                      // automaticamente el del valor NIIF debe quedar en cero, porque se debe borrar esa [CxC o CxP]
                                      $nSaldoNF = ($nSaldo == 0) ? 0 : $nSaldoNF;
                                      //Si el LOCAL o AMBAS el movimiento se valida con respecto al saldo local (comsaldo)
                                      if ($nSaldo > 0) { $xRDC['commovxx'] = "D"; } else { $xRDC['commovxx'] = "C"; }
                                    } else { //Se realizo con ejecucion NIIF
                                      //Si el LOCAL o AMBAS el movimiento se valida con respecto al saldo saldo niif (comsalnf)
                                      if ($nSaldoNF > 0) { $xRDC['commovxx'] = "D"; } else { $xRDC['commovxx'] = "C"; }
                                    }
            
                                    // Actualizo el saldo en la $cModulo.
                                    $qUpdMod  = "UPDATE $cAlfa.$cTable SET ";
                                    $qUpdMod .= "commovxx = \"".$xRDC['commovxx']."\", ";
                                    $qUpdMod .= "comsaldo = \"".$nSaldo."\", ";
                                    $qUpdMod .= "comsalnf = \"".$nSaldoNF."\", ";
                                    $qUpdMod .= "regusrxx = \"INTCW\", ";
                                    $qUpdMod .= "regfmodx = \"".date("Y-m-d")."\", ";
                                    $qUpdMod .= "reghmodx = \"".date("H:i:s")."\", ";
                                    $qUpdMod .= "comidxxx = \"".$xRDC['comidcxx']."\", ";
                                    $qUpdMod .= "comcodxx = \"".$xRDC['comcodcx']."\" ";
                                    $qUpdMod .= "WHERE ";
                                    $qUpdMod .= "comidxxx = \"{$xRDC['comidcxx']}\" AND ";
                                    $qUpdMod .= "comcodxx = \"{$xRDC['comcodcx']}\" AND ";
                                    $qUpdMod .= "comcscxx = \"{$xRDC['comcsccx']}\" AND ";
                                    $qUpdMod .= "comseqxx = \"{$xRDC['comseqcx']}\" AND ";
                                    $qUpdMod .= "teridxxx = \"{$xRDC['teridxxx']}\" AND ";
                                    $qUpdMod .= "pucidxxx = \"{$xRDC['pucidxxx']}\" ";
                                    $nQueryTimeStart = microtime(true); $xUpdMod = mysql_query($qUpdMod,$xConexion01);
                                    $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                                    if (!$xUpdMod) {
                                      $nError = 1;
                                      $vError['LINEAERR'] = __LINE__;
                                      $vError['TIPOERRX'] = "FALLIDO";
                                      $vError['DESERROR'] = "Error al Actualizar el Saldo en la $cModulo.";
                                      $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                                    }
                                  } else {
                                    $nError = 1;
                                    $vError['LINEAERR'] = __LINE__;
                                    $vError['TIPOERRX'] = "FALLIDO";
                                    $vError['DESERROR'] = "Error al Actualizar el Saldo en la $cModulo, el tipo de ejecucion de la $cModulo del comprobante no es el mismo que tiene la $cModulo Existente.";
                                    $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                                  }
                                } else {
                                  // Si el registro no existe en la $cModulo lo creo como nuevo.
                                  switch ($xRDC['commovxx']) {
                                    case "D":
                                      // Para las CxC los debitos aumentan.
                                      // Para las CxP los debitos disminuyen.
                                      // Se incluye el valor NIIF
                                      $nSaldo   = ($xRDC['comvlrxx']*1);
                                      $nSaldoNF = ($xRDC['comvlrnf']*1);
                                    break;
                                    case "C":
                                      // Para las CxC los creditos disminuyen.
                                      // Para las CxP los creditos aumentan.
                                      // Se incluye el valor NIIF
                                      $nSaldo   = ($xRDC['comvlrxx']*-1);
                                      $nSaldoNF = ($xRDC['comvlrnf']*-1);
                                    break;
                                  }

                                  // Valido que tipo de movimiento debo mandar a la $cModulo, este depende del tipo de ejecuion.
                                  if ($xRDC['puctipej'] == "L" || $xRDC['puctipej'] == "") { //se realizo con ejecucion LOCAL o AMBAS
                                    //Si el tipo de ejecuion es LOCAL o AMBAS, y el saldo del valor local queda en cero,
                                    //automaticamente el del valor NIIF debe quedar en cero, porque se debe borrar esa [CxC o CxP]
                                    $nSaldoNF = ($nSaldo == 0) ? 0 : $nSaldoNF;
                                    //Si el LOCAL o AMBAS el movimiento se valida con respecto al saldo local (comsaldo)
                                    if ($nSaldo > 0) { $xRDC['commovxx'] = "D"; } else { $xRDC['commovxx'] = "C"; }
                                  } else { //Se realizo con ejecucion NIIF
                                    //Si el LOCAL o AMBAS el movimiento se valida con respecto al saldo saldo niif (comsalnf)
                                    if ($nSaldoNF > 0) { $xRDC['commovxx'] = "D"; } else { $xRDC['commovxx'] = "C"; }
                                  }
            
                                  // Crea la cuenta por Cobrar o por Pagar 
                                  $qInsMod  = "INSERT INTO $cAlfa.$cTable (";
                                  $qInsMod .= "comidxxx,";
                                  $qInsMod .= "comcodxx,";
                                  $qInsMod .= "comcscxx,";
                                  $qInsMod .= "comseqxx,";
                                  $qInsMod .= "regfcrex,";
                                  $qInsMod .= "comfecve,";
                                  $qInsMod .= "teridxxx,";
                                  $qInsMod .= "terid2xx,";
                                  $qInsMod .= "pucidxxx,";
                                  $qInsMod .= "commovxx,";
                                  $qInsMod .= "puctipej,";
                                  $qInsMod .= "comsaldo,";
                                  $qInsMod .= "comsalnf,";
                                  $qInsMod .= "ccoidxxx,";
                                  $qInsMod .= "sccidxxx,";
                                  $qInsMod .= "regusrxx,";
                                  $qInsMod .= "reghcrex,";
                                  $qInsMod .= "regfmodx,";
                                  $qInsMod .= "reghmodx,";
                                  $qInsMod .= "regestxx) VALUES (";
                                  $qInsMod .= "\"".$xRDC['comidcxx']."\",";
                                  $qInsMod .= "\"".$xRDC['comcodcx']."\",";
                                  $qInsMod .= "\"".$xRDC['comcsccx']."\",";
                                  $qInsMod .= "\"".$xRDC['comseqcx']."\",";
                                  $qInsMod .= "\"".$xRDC['comfecxx']."\",";
                                  $qInsMod .= "\"".$xRDC['comfecve']."\",";
                                  $qInsMod .= "\"".$xRDC['teridxxx']."\",";
                                  $qInsMod .= "\"".$xRDC['terid2xx']."\",";
                                  $qInsMod .= "\"".$xRDC['pucidxxx']."\",";
                                  $qInsMod .= "\"".$xRDC['commovxx']."\",";
                                  $qInsMod .= "\"".$xRDC['puctipej']."\",";
                                  $qInsMod .= "\"".$nSaldo."\",";
                                  $qInsMod .= "\"".$nSaldoNF."\",";
                                  $qInsMod .= "\"".$xRDC['ccoidxxx']."\",";
                                  $qInsMod .= "\"".$xRDC['sccidxxx']."\",";
                                  $qInsMod .= "\"INTCW\",";
                                  $qInsMod .= "\"".date("H:i:s")."\",";
                                  $qInsMod .= "\"".date("Y-m-d")."\",";
                                  $qInsMod .= "\"".date("H:i:s")."\",";
                                  $qInsMod .= "\"ACTIVO\")";
                                  $nQueryTimeStart = microtime(true); $xInsMod = mysql_query($qInsMod,$xConexion01);
                                  $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                                  if (!$xInsMod) {
                                    $nError = 1;
                                    $vError['LINEAERR'] = __LINE__;
                                    $vError['TIPOERRX'] = "FALLIDO";
                                    $vError['DESERROR'] = "Error al Guardar los Datos Antes de Insertar en el Modulo de $cModulo.";
                                    $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                                  }
                                }
                              }
                            }
            
                            if ($nError == 0) {
                              // Actualizando fecha en control fechas modulo impo y expo
                              for ($n=0;$n<count($mTramites);$n++) {
                                switch ($mTramites[$n]['doctipxx']) { //dependiendo del tipo de operacion actualizo la tabla en Impo o Expo
                                  case "IMPORTACION":
                                  case "TRANSITO":
                                  case "OTROS":
                                    //Verificando que el DO no tenga fecha de factrura, solo se debe guardar en control fechas
                                    //la fecha de la primera factura
                                    $qFecDo  = "SELECT DOIFENTR,DOIHENTR ";
                                    $qFecDo .= "FROM $cAlfa.SIAI0200 ";
                                    $qFecDo .= "WHERE ";
                                    $qFecDo .= "DOIIDXXX =  \"{$mTramites[$n]['docidxxx']}\" AND ";
                                    $qFecDo .= "DOISFIDX =  \"{$mTramites[$n]['docsufxx']}\" AND ";
                                    $qFecDo .= "ADMIDXXX =  \"{$mTramites[$n]['sucidxxx']}\" AND ";
                                    $qFecDo .= "DOIFENTR != \"0000-00-00\" AND ";
                                    $qFecDo .= "DOIHENTR != \"00:00:00\" LIMIT 0,1 ";
                                    $nQueryTimeStart = microtime(true); $xFecDo = mysql_query($qFecDo,$xConexion01);
                                    $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                                    if (mysql_num_rows($xFecDo) == 0) {
                                      // Actualizo la Fecha de Facturacion en Control de Fechas
                                      $qUpd200  = "UPDATE $cAlfa.SIAI0200 SET ";
                                      $qUpd200 .= "DOIFENTR = \"".$vFecha[0]."\", ";
                                      $qUpd200 .= "DOIHENTR = \"".date('H:i:s')."\" ";
                                      $qUpd200 .= "WHERE ";
                                      $qUpd200 .= "DOIIDXXX = \"{$mTramites[$n]['docidxxx']}\" AND ";
                                      $qUpd200 .= "DOISFIDX = \"{$mTramites[$n]['docsufxx']}\" AND ";
                                      $qUpd200 .= "ADMIDXXX = \"{$mTramites[$n]['sucidxxx']}\"";
                                      $nQueryTimeStart = microtime(true); $xUpd200 = mysql_query($qUpd200,$xConexion01);
                                      $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                                      if (!$xUpd200) {
                                        $nError = 1;
                                        $vError['LINEAERR'] = __LINE__;
                                        $vError['TIPOERRX'] = "FALLIDO";
                                        $vError['DESERROR'] = "El Sistema no Pudo Actualizar la Fecha de la Factura en el Modulo de Control Fechas.";
                                        $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                                      }
                                    }
                                    // Fin de Actualizo la Fecha de Facturacion en Control de Fechas.
                                  break;
                                  case "EXPORTACION":
                                    //Verificando que el DO no tenga fecha de factrura, solo se debe guardar en control fechas
                                    //la fecha de la primera factura
                                    $qFecDo  = "SELECT dexfefac ";
                                    $qFecDo .= "FROM $cAlfa.siae0199 ";
                                    $qFecDo .= "WHERE ";
                                    $qFecDo .= "dexidxxx =  \"{$mTramites[$n]['docidxxx']}\" AND ";
                                    $qFecDo .= "admidxxx =  \"{$mTramites[$n]['sucidxxx']}\" AND ";
                                    $qFecDo .= "dexfefac != \"0000-00-00\" LIMIT 0,1 ";
                                    $nQueryTimeStart = microtime(true); $xFecDo = mysql_query($qFecDo,$xConexion01);
                                    $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                                    if (mysql_num_rows($xFecDo) == 0) {
                                      // Actualizo la Fecha de Facturacion en Exportacion.
                                      $qUpd199  = "UPDATE $cAlfa.siae0199 SET ";
                                      $qUpd199 .= "dexfefac = \"".$vFecha[0]."\" ";
                                      $qUpd199 .= "WHERE ";
                                      $qUpd199 .= "dexidxxx = \"{$mTramites[$n]['docidxxx']}\" AND ";
                                      $qUpd199 .= "admidxxx = \"{$mTramites[$n]['sucidxxx']}\"";
                                      $nQueryTimeStart = microtime(true); $xUpd199 = mysql_query($qUpd199,$xConexion01);
                                      $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                                      if (!$xUpd199) {
                                        $nError = 1;
                                        $vError['LINEAERR'] = __LINE__;
                                        $vError['TIPOERRX'] = "FALLIDO";
                                        $vError['DESERROR'] = "El Sistema no Pudo Actualizar la Fecha de la Factura en el Modulo de Control Fechas.";
                                        $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                                      }
                                    }
                                    // Fin de Actualizo la Fecha de Facturacion en Exportacion.
                                  break;
                                  default:
                                    //Verificando que el DO no tenga fecha de factrura, solo se debe guardar en control fechas
                                    //la fecha de la primera factura
                                    $qFecDo  = "SELECT DOIFENTR,DOIHENTR ";
                                    $qFecDo .= "FROM $cAlfa.SIAI0200 ";
                                    $qFecDo .= "WHERE ";
                                    $qFecDo .= "DOIIDXXX =  \"{$mTramites[$n]['docidxxx']}\" AND ";
                                    $qFecDo .= "DOISFIDX =  \"{$mTramites[$n]['docsufxx']}\" AND ";
                                    $qFecDo .= "ADMIDXXX =  \"{$mTramites[$n]['sucidxxx']}\" AND ";
                                    $qFecDo .= "DOIFENTR != \"0000-00-00\" AND ";
                                    $qFecDo .= "DOIHENTR != \"00:00:00\" LIMIT 0,1 ";
                                    $nQueryTimeStart = microtime(true); $xFecDo = mysql_query($qFecDo,$xConexion01);
                                    $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                                    if (mysql_num_rows($xFecDo) == 0) {
                                      // Actualizo la Fecha de Facturacion en Control de Fechas.
                                      $qUpd200  = "UPDATE $cAlfa.SIAI0200 SET ";
                                      $qUpd200 .= "DOIFENTR = \"".$vFecha[0]."\", ";
                                      $qUpd200 .= "DOIHENTR = \"".date('H:i:s')."\" ";
                                      $qUpd200 .= "WHERE ";
                                      $qUpd200 .= "DOIIDXXX = \"{$mTramites[$n]['docidxxx']}\" AND ";
                                      $qUpd200 .= "DOISFIDX = \"{$mTramites[$n]['docsufxx']}\" AND ";
                                      $qUpd200 .= "ADMIDXXX = \"{$mTramites[$n]['sucidxxx']}\"";
                                      $nQueryTimeStart = microtime(true); $xUpd200 = mysql_query($qUpd200,$xConexion01);
                                      $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                                      if (!$xUpd200) {
                                        $nError = 1;
                                        $vError['LINEAERR'] = __LINE__;
                                        $vError['TIPOERRX'] = "FALLIDO";
                                        $vError['DESERROR'] = "El Sistema no Pudo Actualizar la Fecha de la Factura en el Modulo de Control Fechas.";
                                        $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                                      }
                                    }
                                    // Fin de Actualizo la Fecha de Facturacion en Control de Fechas.
                                  break;
                                }## switch ($mTramites[$n]['doctipxx']) {
                              } ## for ($n=0;$n<count($mTramites);$n++) { ##
                            }
            
                            if ($nError == 0) {
                              $qUpdate  = "UPDATE $cAlfa.fcoc$nAnio SET ";
                              $qUpdate .= "comealpo = \"CONTABILIZADO\" ";
                              $qUpdate .= "WHERE ";
                              $qUpdate .= "comidxxx = \"{$data['comidxxx']}\" AND ";
                              $qUpdate .= "comcodxx = \"{$data['comcodxx']}\" AND ";
                              $qUpdate .= "comcscxx = \"$cNewCsc\" AND ";
                              $qUpdate .= "comcsc2x = \"$cNewCsc\"";
                              $nQueryTimeStart = microtime(true); $xUpdate = mysql_query($qUpdate,$xConexion01);
                              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
                              if (!$xUpdate) {
                                $nError = 1;
                                $vError['LINEAERR'] = __LINE__;
                                $vError['TIPOERRX'] = "FALLIDO";
                                $vError['DESERROR'] = "Error al actualizar el Estado CONTABILIZADO al Consecutivo Definitivo [$cNewCsc].";
                                $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                              } else {
                                $vError['LINEAERR'] = __LINE__;
                                $vError['TIPOERRX'] = "EXITOSO";
                                $vError['DESERROR'] = "La Factura [F-$cComCod-$cComCsc] se legalizo con exito con el consecutivo [F-$cComCod-$cNewCsc].";
                                $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                              }
                            }
                          }
                        } else {
                          $nError = 1;
                          $vError['LINEAERR'] = __LINE__;
                          $vError['TIPOERRX'] = "FALLIDO";
                          $vError['DESERROR'] = "La factura con consecutivo [$cComCod-$cComCsc] no existe o no tiene estado [PENDIENTE - PROVISIONAL].";
                          $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                        }
                      }
                    } else {
                      $nError = 1;
                      $vError['LINEAERR'] = __LINE__;
                      $vError['TIPOERRX'] = "FALLIDO";
                      $vError['DESERROR'] = "El archivo XML [$cFileDown] no existe en el directorio temporal.";
                      $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
                    }

                    if ($nError == 1) {
                      $nSwitch = 1;
                    }

                    //Borrando el archivo del FTP
										ftp_delete($id_ftp, $cFile);
									}
                }
              }
            }
          } else {
            $nSwitch = 1;
            $vError['LINEAERR'] = __LINE__;
            $vError['TIPOERRX'] = "FALLIDO";
            $vError['DESERROR'] = "No se encontraron archivos XML en el directorio [{$pvDatos['dirinxxx']}] del FTP.";
            $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
          }
        } else {
          $nSwitch = 1;
          $vError['LINEAERR'] = __LINE__;
          $vError['TIPOERRX'] = "FALLIDO";
          $vError['DESERROR'] = "Error al Iniciar Sesion en el Servidor FTP.";
          $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
        }
      } else {
        $nSwitch = 1;
        $vError['LINEAERR'] = __LINE__;
        $vError['TIPOERRX'] = "FALLIDO";
        $vError['DESERROR'] = "Error de Conexion con el Servidor FTP.";
        $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
      }

      ftp_quit($id_ftp);

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    } ## function fnLegalizarFacturas($pvDatos) {

    /**
     * Metodo para Marcar los comprobantes transmitidos en el XML.
     */
    function fnMarcarComprobantes($pvDatos) {
      /**
       * Recibe una Matriz con las siguientes posiciones:
       * $pvDatos['basedato'] //Base de datos
       * $pvDatos['tablaerr'] //Tabla Error
       * $pvDatos['tablamov'] //Tabla Movimiento
       * $pvDatos['archivox'] //Nombre del archivo
       * $pvDatos['fecfinxx'] //Fecha fin proceso
       */

      global $OPENINIT;

      /**
       * Variable para saber si hay o no errores de validacion.
       * @var number
       */
      $nSwitch = 0;

      /**
       * Cantidad de registros para reinicio de conexion.
       * @var integer
       */
      $nCanReg = 0;

      /**
       * Hacer la conexion a la base de datos
       */
      $xConexion01 = mysql_connect(OC_SERVER, OC_USERROBOT, OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);

      /**
       * Variable para hacer el retorno.
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = ""; // true - false

      /**
       * Base de datos
       * @var string
       */
      $cAlfa = $pvDatos['basedato'];

      /**
       * Instanciando Objeto para la creacion de las tablas temporales.
       */
      $objTablasTemporales = new cEstructurasCargoWiseDsv();

      /**
       * Vector de Errores
       * @var array
       */
      $vError = array();
      $vError['TABLAERR'] = $pvDatos['tablaerr'];

      $qCabMov  = "SELECT ";
      $qCabMov .= "comidxxx, ";
      $qCabMov .= "comcodxx, ";
      $qCabMov .= "comcscxx, ";
      $qCabMov .= "comcsc2x, ";
      $qCabMov .= "anioxxxx, ";
      $qCabMov .= "archivox, ";
      $qCabMov .= "archicom, ";
      $qCabMov .= "regestxx ";
      $qCabMov .= "FROM $cAlfa.{$pvDatos['tablamov']} ";
      $qCabMov .= "GROUP BY comidxxx,comcodxx,comcscxx,comcsc2x";
      $nQueryTimeStart = microtime(true); $xCabMov = mysql_query($qCabMov,$xConexion01);
      $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
      // echo "\n\n".$qCabMov."~".mysql_num_rows($xCabMov)."\n\n";
      if(mysql_num_rows($xCabMov) > 0) {
        while($xRCM = mysql_fetch_assoc($xCabMov)){
          $nCanReg++;
          if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDbCargoWiseDsv($xConexion01); }

          //Actualizo campo de transmision integracion Cargo Wise en la fcocAAAA
          $qUpdCom  = "UPDATE $cAlfa.fcoc{$xRCM['anioxxxx']} SET ";
          if ($xRCM['regestxx'] == "ACTIVO" || $xRCM['regestxx'] == "PROVISIONAL") {
            $qUpdCom .= "comcwxxx = \"{$pvDatos['fecfinxx']}\", ";
          }
          if ($xRCM['archicom'] != "") {
            $qUpdCom .= "comcwaco = \"{$xRCM['archicom']}\", ";
          }
          $qUpdCom .= "comcwarc = \"{$xRCM['archivox']}\" ";
          $qUpdCom .= "WHERE ";
          $qUpdCom .= "comidxxx = \"{$xRCM['comidxxx']}\" AND ";
          $qUpdCom .= "comcodxx = \"{$xRCM['comcodxx']}\" AND ";
          $qUpdCom .= "comcscxx = \"{$xRCM['comcscxx']}\" AND ";
          $qUpdCom .= "comcsc2x = \"{$xRCM['comcsc2x']}\" ";
          $nQueryTimeStart = microtime(true); $xUpdCom = mysql_query($qUpdCom,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
          if(!$xUpdCom){
            // echo "\nActualizando Cabecera: ".$qUpdCom;
            $nSwitchAux = 1;
            $vError['LINEAERR'] = __LINE__;
            $vError['TIPOERRX'] = "FALLIDO";
            $vError['DESERROR'] = "Error al Actualizar Fecha Transmision Cargo Wise DSV para el Comprobante [{$xRCM['comidxxx']}-{$xRCM['comcodxx']}-{$xRCM['comcscxx']}-{$xRCM['comcsc2x']}].";
            $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
          }
        } //while($xRCM = mysql_fetch_assoc($xCabMov)){
      } else {
        $nSwitch = 1;
        $vError['LINEAERR'] = __LINE__;
        $vError['TIPOERRX'] = "FALLIDO";
        $vError['DESERROR'] = "No se encontraron Registros.";
        $objTablasTemporales->fnGuardarErrorCargoWiseDsv($vError,$cAlfa);
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    } ## function fnMarcarComprobantes($cAlfa){

    /**
     * Metodo para Crear el vector con las variables del sistema requeridas.
     */
    function fnVariablesSistema($cAlfa) {
      /**
       * Parametros:
       * $pcVariables //Cadena con las variables del sistema requeridas
       * $cAlfa       //Base de Datos
       */

      /**
       * Hacer la conexion a la base de datos
       */
      $xConexion01 = mysql_connect(OC_SERVER, OC_USERROBOT, OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);

      /**
       * Instanciando Objeto para la creacion de las tablas temporales.
       */
      $objTablasTemporales = new cEstructurasCargoWiseDsv();

      $qConsulta  = "SELECT stridxxx, strvlrxx ";
      $qConsulta .= "FROM $cAlfa.sys00002 ";
      $qConsulta .= "WHERE ";
      $qConsulta .= "regestxx = \"ACTIVO\"";
      $nQueryTimeStart = microtime(true); $xConsulta = mysql_query($qConsulta,$xConexion01);
      $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfoDsv($xConexion01,$nQueryTime,$cAlfa);
      // echo "\n".$qConsulta."~".mysql_num_rows($xConsulta);

      while($xRC = mysql_fetch_assoc($xConsulta)) {
        $vSysStr["{$xRC['stridxxx']}"] = $xRC['strvlrxx'];
      }
      mysql_free_result($xConsulta);

      return $vSysStr;
    } ## function fnVariablesSistema($pArrayParametros) { ##

    /**
     * Funcion para extraer una matriz de un string con separadores.
     */
    function fnExplodeArray($xString, $xRecords_Separator, $xFields_Separator) {
      $mMatriz = array();
      $mMatriz01 = array();
      $mMatriz02 = array();
      $nInd = 0;
      $mMatriz01 = explode($xRecords_Separator, $xString);
      for ($i = 0; $i < count($mMatriz01); $i++) {
        if ($mMatriz01[$i] != "") {
          $mMatriz02 = explode($xFields_Separator, $mMatriz01[$i]);
          for ($j = 0; $j < count($mMatriz02); $j++) {
            $mMatriz[$nInd][$j] = $mMatriz02[$j];
          }
          $nInd++;
        }
      }
      return $mMatriz;
    }
  } ## class cIntegracionCargoWiseDsv { ##

  class cEstructurasCargoWiseDsv {

    /**
     * Metodo que se encarga de Crear las Estructuras de las Tablas
     */
    function fnCrearEstructurasCargoWiseDsv($pParametros,$cAlfa){

      /**
       * Recibe como Parametro un vector con las siguientes posiciones:
       * $pArrayParametros['TIPOESTU] //TIPO DE ESTRUCTURA
       * $cAlfa //Base de datos
       */

      /**
       * Variable para saber si hay o no errores de validacion.
       * @var number
       */
      $nSwitch = 0;

      /**
       * Matriz para Retornar Valores
       * @var array
       */
      $mReturn = array();

      /**
       * Campos Excluidos al momento del LOAD DATA
       * @var array
       */
      $vFieldsExcluidos = array();

      /**
       * Reservando Primera Posición para retorna true o false
       */
      $mReturn[0] = "";
      $mReturn[1] = ""; // Nombre de tabla
      $mReturn[2] = ""; // Campos excluidos

      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionCargoWiseDsv = $this->fnConectarDbCargoWiseDsv();
      if($mReturnConexionCargoWiseDsv[0] == "true"){
        $xConexionDsv = $mReturnConexionCargoWiseDsv[1];
      }else{
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnConexionCargoWiseDsv);$nR++){
          $mReturn[count($mReturn)] = $mReturnConexionCargoWiseDsv[$nR];
        }
      }

      /**
       * Random para Nombre de la Tabla
       */
      $cTabCar  = mt_rand(1000000000, 9999999999);

      switch($pParametros['TIPOESTU']){
        case "MOVCONTABLE":
          $cTabla = "memmovca".$cTabCar;

          $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
          $qNewTab .= "lineaidx INT(11) NOT NULL AUTO_INCREMENT, "; //LINEA
          $qNewTab .= "interfaz VARCHAR(255)  NOT NULL, ";  // Interfaz
          $qNewTab .= "comidxxx VARCHAR(255)  NOT NULL, ";  // Id del Comprobante
          $qNewTab .= "comcodxx VARCHAR(255)  NOT NULL, ";  // Codigo del Comprobante
          $qNewTab .= "comcscxx VARCHAR(255)  NOT NULL, ";  // Consecutivo Uno del Comprobante
          $qNewTab .= "comcsc2x VARCHAR(255)  NOT NULL, ";  // Consecutivo Dos del Comprobante
          $qNewTab .= "comfecxx date          NOT NULL, ";  // Fecha del Comprobante
          $qNewTab .= "reghcrex time          NOT NULL, ";  // Hora de Creacion del Registro
          $qNewTab .= "ccoidxxx varchar(10)   NOT NULL, ";  // Centro de costo de cabecera
          $qNewTab .= "sccidxxx varchar(20)   NOT NULL, ";  // Sub centro de costo de cabecera
          $qNewTab .= "sucidxxx varchar(3)    NOT NULL, ";  // Id de la Sucursal Operativa
          $qNewTab .= "docidxxx varchar(20)   NOT NULL, ";  // Id del DO
          $qNewTab .= "docsufxx varchar(3)    NOT NULL, ";  // Sufijo del DO
          $qNewTab .= "ctopccxx varchar(2)   NOT NULL, ";   // Concepto PCC
          $qNewTab .= "ctotrixx varchar(2)   NOT NULL, ";   // Concepto tributos
          $qNewTab .= "ctodesxx varchar(50)   NOT NULL, ";  // Descripcion del concepto
          $qNewTab .= "ctocwccx varchar(20)   NOT NULL, ";  // Charge Codes Cargowise
          $qNewTab .= "clicwccx varchar(20)   NOT NULL, ";  // Charge Codes Cargowise
          $qNewTab .= "clicwccc varchar(20)   NOT NULL, ";  // Charge Codes Cargowise Dueño DO
          $qNewTab .= "comvlrxx decimal(15,2) NOT NULL, ";  // Valor del Comprobante
          $qNewTab .= "comvlr01 decimal(15,2) NOT NULL, ";  // Valor de IVA del Comprobante
          $qNewTab .= "anioxxxx VARCHAR(255)  NOT NULL, ";  // Año del Comprobante
          $qNewTab .= "regestxx VARCHAR(12)   NOT NULL, ";  // Estado del Comprobante
          $qNewTab .= "archivox VARCHAR(255)  NOT NULL, ";  // Nombre del archivo
          $qNewTab .= "archicom VARCHAR(255)  NULL, ";      // Nombre del archivo complemento
          $qNewTab .= "envftpxx VARCHAR(255)  NULL, ";      // Enviado al FTP
          $qNewTab .= "envftpco VARCHAR(255)  NULL, ";      // Enviado al FTP complemento
          $qNewTab .= "PRIMARY KEY (lineaidx)) ENGINE=MyISAM ";
          $xNewTab  = mysql_query($qNewTab,$xConexionDsv);

          if(!$xNewTab) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal de Errores. ".mysql_error($xConexionDsv);
          }
        break;
        case "ERRORES":
          $cTabla = "memerror".$cTabCar;

          $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
          $qNewTab .= "LINEAIDX INT(11) NOT NULL AUTO_INCREMENT, "; //LINEA
          $qNewTab .= "LINEAERR VARCHAR(10) NOT NULL, ";            //LINEA DEL ARCHIVO
          $qNewTab .= "TIPOERRX VARCHAR(20) NOT NULL, ";            //TIPO DE ERROR
          $qNewTab .= "DESERROR TEXT NOT NULL, ";                   //DESCRIPCION DEL ERROR
          $qNewTab .= "PRIMARY KEY (LINEAIDX)) ENGINE=MyISAM ";
          $xNewTab  = mysql_query($qNewTab,$xConexionDsv);

          if(!$xNewTab) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal de Errores. ".mysql_error($xConexionDsv);
          }
        break;
        default:
          $nSwitch = 1;
          $mReturn[count($mReturn)] = "No se Recibio Tipo de Estructura a Crear";
        break;
      }

      if($nSwitch == 0){
        $mReturn[0] = "true";
        $mReturn[1] = $cTabla;
        $mReturn[2] = $vFieldsExcluidos;
      }else{
        $mReturn[0] = "false";
      }
      return $mReturn;
    } ## function fnCrearEstructurasCargoWiseDsv($pParametros,$cAlfa){ ##
    
    /**
     * Metodo que realiza la conexion.
     */
    function fnConectarDbCargoWiseDsv() {
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
       * Reservo Primera Posicion para retorna true o false
       */
      $mReturn[0] = "";

      $xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);
      if($xConexion99){
        $nSwitch = 0;
      }else{
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "El Sistema no Logro Conexion con ".OC_SERVER;
      }

      if($nSwitch == 0){
        $mReturn[0] = "true";
        $mReturn[1] = $xConexion99;
      }else{
        $mReturn[0] = "false";
      }
      return $mReturn;
    }##function fnConectarDbCargoWiseDsv(){##

    /**
     * Metodo que realiza el reinicio de la conexion.
     */
    function fnReiniciarConexionDbCargoWiseDsv($pConexion){

      mysql_close($pConexion);

      $xConexion01 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT,TRUE);

      return $xConexion01;
    }##function fnReiniciarConexionDbCargoWiseDsv(){##

    /**
     * Metodo que se encarga de Guardar los Errores Generados por los Metodos de Interfaces.
     */
    function fnGuardarErrorCargoWiseDsv($pArrayParametros,$cAlfa){
      /**
       * Recibe como Parametro un vector con las siguientes posiciones:
       * $pArrayParametros['TABLAERR] //Nombre tabla errores
       * $pArrayParametros['TABLAERR] //Nombre tabla errores
       * $cAlfa //Base de datos
       */

      /**
       * Variables para reemplazar caracteres especiales
       * @var array
       */
      $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
      $cReempl = array('\"',"\'"," "," "," "," ");

      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionCargoWiseDsv = $this->fnConectarDbCargoWiseDsv();
      if($mReturnConexionCargoWiseDsv[0] == "true"){
        $xConexionDsv = $mReturnConexionCargoWiseDsv[1];
      }

      if($pArrayParametros['TABLAERR'] != ""){

        $qInsert  = "INSERT INTO $cAlfa.{$pArrayParametros['TABLAERR']} (LINEAERR, TIPOERRX, DESERROR) VALUES ";
        $qInsert .= "(\"{$pArrayParametros['LINEAERR']}\", ";
        $qInsert .= " \"{$pArrayParametros['TIPOERRX']}\", ";
        $qInsert .= " \"".str_replace($cBuscar,$cReempl,$pArrayParametros['DESERROR'])."\") ";
        $xInsert = mysql_query($qInsert,$xConexionDsv);
        // echo "\n".$qInsert;
      }
    }##function fnGuardarErrorCargoWiseDsv($pParametros){##

    /**
     * Metodo que Crea la Tabla Log en caso de no existir.
     */
    function fnCrearTablaLogCargoWiseDsv($cAlfa) {

      /**
       * Variable para saber si hay o no errores de validacion.
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
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionCargoWiseDsv = $this->fnConectarDbCargoWiseDsv();
      if($mReturnConexionCargoWiseDsv[0] == "true"){
        $xConexionDsv = $mReturnConexionCargoWiseDsv[1];
      }

      /**
       * Variable para saber el Año de la tabla que se va a crear.
       *
       * @var number
       */
      $nAnio = date('Y');
      $nAnioAnterior = date('Y')-1;

      $qTabAct = "SHOW TABLES FROM $cAlfa LIKE \"sysf$nAnio\" ";
      $nQueryTimeStart = microtime(true); $xTabAct = mysql_query($qTabAct,$xConexionDsv);
      $nQueryTime = (microtime(true) - $nQueryTimeStart); $this->fnMysqlQueryInfoDsv($xConexionDsv,$nQueryTime,$cAlfa);
      if(mysql_num_rows($xTabAct) == 0){
        $qTabAnt = "SHOW TABLES FROM $cAlfa LIKE \"sysf$nAnioAnterior\" ";
        $nQueryTimeStart = microtime(true); $xTabAnt = mysql_query($qTabAnt,$xConexionDsv);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $this->fnMysqlQueryInfoDsv($xConexionDsv,$nQueryTime,$cAlfa);
        if(mysql_num_rows($xTabAnt) == 0){
          $nSwitch = 1;
          $mReturn[count($mReturn)] = "Error al Crear Tabla sysf$nAnio, No Existe la Tabla sysf$nAnioAnterior, Comuniquese con openTecnologia.";
        } else {
          $qCreate = "CREATE TABLE IF NOT EXISTS $cAlfa.sysf$nAnio LIKE $cAlfa.sysf$nAnioAnterior ";
          $nQueryTimeStart = microtime(true); $xCreate = mysql_query($qCreate,$xConexionDsv);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $this->fnMysqlQueryInfoDsv($xConexionDsv,$nQueryTime,$cAlfa);
          if (!$xCreate) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "Error al crear Tabla sysf$nAnio para Log, Comuniquese con openTecnologia.";
          }
        }
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;

    }## fnCrearTablaLogCargoWiseDsv

    /**
     * function para guardar el Log.
     */
    function fnLogEnvioFTP($pvDatos) {
      /**
       * Recibe una Matriz con las siguientes posiciones:
       * $pvDatos['basedato'] //Base de datos
       * $pvDatos['tablaerr'] //Tabla Error
       * $pvDatos['ftpaixxx'] //Id Log
       * $pvDatos['ftptipxx'] //tipo Interfaz
       * $pvDatos['ftpfinix'] //Fecha Inicio proceso
       * $pvDatos['ftpffinx'] //Fecha fin proceso
       * $pvDatos['ftparcxx'] //Nombre del archivo
       * $pvDatos['ftpmsjxx'] //Mensajes de error
       * $pvDatos['ftpesxxx'] //Estado
       * $pvDatos['regusrxx'] //Usuario
       * $pvDatos['accionxx'] //Accion a ejecutar
       */

      /**
       * Variable para saber si hay o no errores de validacion.
       * @var number
       */
      $nSwitch = 0;

      /**
       * Base de datos
       * @var string
       */
      $cAlfa = $pvDatos['basedato'];

      /**
       * Variables para reemplazar caracteres especiales
       * @var array
       */
      $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
      $cReempl = array('\"',"\'"," "," "," "," ");

      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionCargoWiseDsv = $this->fnConectarDbCargoWiseDsv();
      if($mReturnConexionCargoWiseDsv[0] == "true"){
        $xConexionDsv = $mReturnConexionCargoWiseDsv[1];
      }

      /**
       * Variable para hacer el retorno.
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = ""; //true o false
      $mReturn[1] = ""; //Autoincremental tabla log

      //Año actual
      $nAnio = date("Y");

      switch ($pvDatos['accionxx']) {
        case "INSERTAR":
          $mReturnTablaL  = $this->fnCrearTablaLogCargoWiseDsv($cAlfa);
          if($mReturnTablaL[0] == "false"){
            $nSwitch = 1;
            for($nRTE=3;$nRTE<count($mReturnTablaL);$nRTE++){
              $mReturn[count($mReturn)] = $mReturnTablaL[$nRTE];
            }
          }

          if ($nSwitch == 0) {
            /*** Insertar en tabla de log. ***/
            $qInsert  = "INSERT INTO $cAlfa.sysf$nAnio (";
            $qInsert .= "ftptipxx, ";
            $qInsert .= "ftpfinix, ";
            $qInsert .= "ftpesxxx, ";
            $qInsert .= "regusrxx, ";
            $qInsert .= "regfcrex, ";
            $qInsert .= "reghcrex, ";
            $qInsert .= "regfmodx, ";
            $qInsert .= "reghmodx, ";
            $qInsert .= "regestxx) ";
            $qInsert .= "VALUES (";
            $qInsert .= "\"{$pvDatos['ftptipxx']}\",";
            $qInsert .= "\"{$pvDatos['ftpfinix']}\",";
            $qInsert .= "\"{$pvDatos['ftpesxxx']}\",";
            $qInsert .= "\"{$pvDatos['regusrxx']}\",";
            $qInsert .= "NOW(),";
            $qInsert .= "NOW(),";
            $qInsert .= "NOW(),";
            $qInsert .= "NOW(),";
            $qInsert .= "\"ACTIVO\")";
            $nQueryTimeStart = microtime(true); $xInsert = mysql_query($qInsert,$xConexionDsv);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $this->fnMysqlQueryInfoDsv($xConexionDsv,$nQueryTime,$cAlfa);
            if (!$xInsert) {
              $nSwitch = 1;
              $mReturn[count($mReturn)] = "Error Al Insertar LOG en la Tabla sysf$nAnio. ";
            } else {
              $qIdIns  = "SELECT MAX(ftpaixxx) AS ftpaixxx ";
              $qIdIns .= "FROM $cAlfa.sysf$nAnio ";
              $nQueryTimeStart = microtime(true); $xIdIns = mysql_query($qIdIns,$xConexionDsv);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $this->fnMysqlQueryInfoDsv($xConexionDsv,$nQueryTime,$cAlfa);
              $vIdIns = mysql_fetch_assoc($xIdIns);

              $mReturn[1] = $vIdIns['ftpaixxx'];
            }
          }
        break;
        case "ACTUALIZAR":
          $qUpdate  = "UPDATE $cAlfa.sysf$nAnio SET ";
          $qUpdate .= "ftpmsjxx = \"{$pvDatos['ftpmsjxx']}\", ";
          $qUpdate .= "ftpffinx = \"{$pvDatos['ftpffinx']}\", ";
          $qUpdate .= "ftparcxx = \"{$pvDatos['ftparcxx']}\", ";
          $qUpdate .= "ftpesxxx = \"{$pvDatos['ftpesxxx']}\", ";
          $qUpdate .= "regfmodx = NOW(), ";
          $qUpdate .= "reghmodx = NOW() ";
          $qUpdate .= "WHERE ";
          $qUpdate .= "ftpaixxx = \"{$pvDatos['ftpaixxx']}\" ";
          $nQueryTimeStart = microtime(true); $xUpdate = mysql_query($qUpdate,$xConexionDsv);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $this->fnMysqlQueryInfoDsv($xConexionDsv,$nQueryTime,$cAlfa);
          // echo $qUpdate;
          if (!$xUpdate) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "Error Al Actualizar LOG en la Tabla sysf$nAnio. ";
          }
        break;
        default:
          $nSwitch = 1;
          $mReturn[count($mReturn)] = "Accion sobre el LOG Vacia. ";
        break;
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }

      return $mReturn;
    }

    /**
     * function para capturar la informacion del motor de DB asosciada al query.
     */
    function fnMysqlQueryInfoDsv($xConexion,$xQueryTime,$cAlfa) {

      global $cSystemPath; global $_SERVER;

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

      $cIP = "";
      $cHost = "";
      if ($_SERVER['HTTP_CLIENT_IP'] != "") {
        $cIP   = $_SERVER['HTTP_CLIENT_IP'];
        $cHost = $_SERVER['HTTP_VIA'];
      }elseif ($_SERVER['HTTP_X_FORWARDED_FOR'] != "") {
        $cIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $cHost = $_SERVER['HTTP_VIA'];
      }else{
        $cIP = $_SERVER['REMOTE_ADDR'];
        $cHost = $_SERVER['HTTP_VIA'];
      }

      if ($cHost == "") {
        $cHost = $cIP;
      }

      $copenComex  = "|";
      $copenComex .= "OPENCOMEX~";
      $copenComex .= "{$_SERVER['PHP_SELF']}~";
      $copenComex .= "$cIP~";
      $copenComex .= "$cHost~";
      $copenComex .= "$cAlfa~";
      $copenComex .= date("Y-m-d")."~";
      $copenComex .= date("H:i:s");
      $copenComex .= "|";
      $xopenComex = mysql_query("SET @opencomex = \"$copenComex\"",$xConexion);
      $xQueryInfo = mysql_query("SET @mysqlinfo = \"$cQueryInfo\"",$xConexion);

    } ## function fnMysqlQueryInfoDsv($xConexion,$xQueryTime,$cAlfa) {
    ## Metodo para capturar la informacion del motor de DB asosciada al query
  }

<?php

  /**
   * utirebav.php : Utility Reporte Proformas BAVARIA del cliente SIACO.
   *
   * Este script contiene la colecciones de clases para generar el Reporte Proformas BAVARIA del cliente SIACO
   *
   * @package openComex
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  define("_NUMREG_",100);

  class cRoporteProformasBavaria {

    /**
     * Metodo para obtener la data del reporte proformas.
     */
    function fnReporteProformasBavaria($pArrayParametros) {

      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      /**
       * Variable para hacer el retorno.
       * 
       * @var array
       */
      $mReturn    = array();
      $mReturn[0] = "";

      /**
       * Variable para alamacenar errores.
       * 
       * @var array
       */
      $vError = array();
      $vError['TABLAERR'] = $pArrayParametros['TABLAERR'];

      /**
       * Instanciando Objeto para el Guardado de Errores.
       */
      $objEstructuraReporteProformasBavaria = new cEstructurasReporteProformasBavaria();

      // Declaro el INSERT para la tabal temporal
      $qInsCab  = "INSERT INTO $cAlfa.{$pArrayParametros['TABLAXXX']} (";
      $qInsCab .= "resprexx, ";
      $qInsCab .= "comcscxx, ";
      $qInsCab .= "sucidxxx, ";
      $qInsCab .= "doiidxxx, ";
      $qInsCab .= "docpedxx, ";
      $qInsCab .= "cliidxxx, ";
      $qInsCab .= "clinomxx, ";
      $qInsCab .= "desctoxx, ";
      $qInsCab .= "tipopago, ";
      $qInsCab .= "numfactx, ";
      $qInsCab .= "vlrctoxx, ";
      $qInsCab .= "vlrivaip, ";
      $qInsCab .= "vlrtotal) VALUES ";
      // FIN Declaro el INSERT para la tabla temporal
      
      if ($pArrayParametros['DDESDEXX'] != '' && $pArrayParametros['DHASTAXX'] != '') {
        //Año inicial del filtro
        $vDesde   = explode('-', $pArrayParametros['DDESDEXX']);
        $nAnioIni = $vDesde[0];
        //Año final del filtro
        $vHasta   = explode('-', $pArrayParametros['DHASTAXX']);
        $nAnioFin = $vHasta[0];
      }

      // Nits a los cuales se genera el reporte
      $vNitCli = explode(",", $vSysStr['siacosia_reporte_proformas']);
      $cNitCli = "\"".implode("\",\"", $vNitCli)."\"";

      for ($nAno=$nAnioIni;$nAno<=$nAnioFin;$nAno++) {
        // Contador de Registros para reinicio de conexion
        $nCanReg = 0; $nCanReg01 = 0;

        // Consulta principal para optener el numero de registros
        $qFcoc  = "SELECT ";        
        $qFcoc .= "SQL_CALC_FOUND_ROWS comidxxx ";
        $qFcoc .= "FROM $cAlfa.fcoc$nAno ";
        $qFcoc .= "WHERE ";
        $qFcoc .= "$cAlfa.fcoc$nAno.comidxxx = \"F\" AND ";
        $qFcoc .= "$cAlfa.fcoc$nAno.comfpxxx LIKE \"%EXPORTACION%\" OR $cAlfa.fcoc$nAno.comfpxxx LIKE \"%OTROS%\" AND ";
        if ($pArrayParametros['TERIDXXX'] != '') {
          $qFcoc .= "$cAlfa.fcoc$nAno.teridxxx = \"{$pArrayParametros['TERIDXXX']}\" OR $cAlfa.fcoc$nAno.terid2xx = \"{$pArrayParametros['TERIDXXX']}\" AND ";
        } else {
          $qFcoc .= "($cAlfa.fcoc$nAno.teridxxx IN($cNitCli) OR $cAlfa.fcoc$nAno.terid2xx IN($cNitCli)) AND ";
        }
        if ($pArrayParametros['SUCIDXXX'] != '' && $pArrayParametros['DOCIDXXX'] != '' && $pArrayParametros['DOCSUFXX'] != '') {
          $qFcoc .= "$cAlfa.fcoc$nAno.comfpxxx LIKE \"%{$pArrayParametros['DOCIDXXX']}%\" AND ";
        }
        if ($pArrayParametros['COMIDXXX'] != '' && $pArrayParametros['COMCODXX'] != '' && $pArrayParametros['COMCSCXX'] != ''  && $pArrayParametros['COMCSC2X'] != '') {
          $qFcoc .= "$cAlfa.fcoc$nAno.comidxxx LIKE \"%{$pArrayParametros['COMIDXXX']}%\" AND ";
          $qFcoc .= "$cAlfa.fcoc$nAno.comcodxx LIKE \"%{$pArrayParametros['COMCODXX']}%\" AND ";
          $qFcoc .= "$cAlfa.fcoc$nAno.comcscxx LIKE \"%{$pArrayParametros['COMCSCXX']}%\" AND ";
          $qFcoc .= "$cAlfa.fcoc$nAno.comcsc2x LIKE \"%{$pArrayParametros['COMCSC2X']}%\" AND ";
        }
        if ($pArrayParametros['DDESDEXX'] != '' && $pArrayParametros['DHASTAXX'] != '') {
          $qFcoc .= "$cAlfa.fcoc$nAno.comfecxx BETWEEN \"{$pArrayParametros['DDESDEXX']}\" AND \"{$pArrayParametros['DHASTAXX']}\" AND ";
        }
        $qFcoc .= "$cAlfa.fcoc$nAno.regestxx = \"PROVISIONAL\" LIMIT 0,1";
        // echo $qFcoc;
        $xFcoc  = f_MySql("SELECT","",$qFcoc,$xConexion01,"");

        $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
        $xRNR     = mysql_fetch_array($xNumRows);
        $nCanReg  = $xRNR['FOUND_ROWS()'];
        mysql_free_result($xNumRows);
        mysql_free_result($xLoad);

        if ($nCanReg > 0) {
          for($k=0;$k<=$nCanReg ;$k+=_NUMREG_) {
            $xConexion01 = $objEstructuraReporteProformasBavaria->fnReiniciarConexionDBReporteProformasBavaria($xConexion01);

            //Matriz de datos
            $mDatos = array();

            // Variables para el insert de datos
            $nSwitch_Detalle = 0; 
            $qInsReg = "";

            // Consulta principal para optener la informacion de las facturas
            $qFcoc  = "SELECT ";        
            $qFcoc .= "comidxxx, ";
            $qFcoc .= "comcodxx, ";
            $qFcoc .= "comcscxx, ";
            $qFcoc .= "comcsc2x, ";
            $qFcoc .= "teridxxx, ";
            $qFcoc .= "resprexx, ";
            $qFcoc .= "comfecxx, ";
            $qFcoc .= "comfpxxx, ";
            $qFcoc .= "commemod, ";
            $qFcoc .= "comvlrxx, ";
            $qFcoc .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS clinomxx ";
            $qFcoc .= "FROM $cAlfa.fcoc$nAno ";
            $qFcoc .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcoc$nAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
            $qFcoc .= "WHERE ";
            $qFcoc .= "$cAlfa.fcoc$nAno.comidxxx = \"F\" AND ";
            $qFcoc .= "($cAlfa.fcoc$nAno.comfpxxx LIKE \"%EXPORTACION%\" OR $cAlfa.fcoc$nAno.comfpxxx LIKE \"%OTROS%\") AND ";
            if ($pArrayParametros['TERIDXXX'] != '') {
              $qFcoc .= "($cAlfa.fcoc$nAno.teridxxx = \"{$pArrayParametros['TERIDXXX']}\" OR $cAlfa.fcoc$nAno.terid2xx = \"{$pArrayParametros['TERIDXXX']}\") AND ";
            } else {
              $qFcoc .= "($cAlfa.fcoc$nAno.teridxxx IN($cNitCli) OR $cAlfa.fcoc$nAno.terid2xx IN($cNitCli)) AND ";
            }
            if ($pArrayParametros['SUCIDXXX'] != '' && $pArrayParametros['DOCIDXXX'] != '' && $pArrayParametros['DOCSUFXX'] != '') {
              $qFcoc .= "$cAlfa.fcoc$nAno.comfpxxx LIKE \"%{$pArrayParametros['DOCIDXXX']}%\" AND ";
            }
            if ($pArrayParametros['COMIDXXX'] != '' && $pArrayParametros['COMCODXX'] != '' && $pArrayParametros['COMCSCXX'] != ''  && $pArrayParametros['COMCSC2X'] != '') {
              $qFcoc .= "$cAlfa.fcoc$nAno.comidxxx LIKE \"%{$pArrayParametros['COMIDXXX']}%\" AND ";
              $qFcoc .= "$cAlfa.fcoc$nAno.comcodxx LIKE \"%{$pArrayParametros['COMCODXX']}%\" AND ";
              $qFcoc .= "$cAlfa.fcoc$nAno.comcscxx LIKE \"%{$pArrayParametros['COMCSCXX']}%\" AND ";
              $qFcoc .= "$cAlfa.fcoc$nAno.comcsc2x LIKE \"%{$pArrayParametros['COMCSC2X']}%\" AND ";
            }
            if ($pArrayParametros['DDESDEXX'] != '' && $pArrayParametros['DHASTAXX'] != '') {
              $qFcoc .= "$cAlfa.fcoc$nAno.comfecxx BETWEEN \"{$pArrayParametros['DDESDEXX']}\" AND \"{$pArrayParametros['DHASTAXX']}\" AND ";
            }
            $qFcoc .= "$cAlfa.fcoc$nAno.regestxx = \"PROVISIONAL\" LIMIT $k,"._NUMREG_;
            $xFcoc  = f_MySql("SELECT","",$qFcoc,$xConexion01,"");

            while ($xRCD = mysql_fetch_array($xFcoc)) {
              if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objEstructuraReporteProformasBavaria->fnReiniciarConexionDBReporteProformasBavaria($xConexion01); }

              // // Se obtiene los datos del Dex
              $cSucId = "";
              $cDexId = "";
              $mComfp = f_Explode_Array($xRCD['comfpxxx'],"|","~");
              for ($j=0; $j < count($mComfp); $j++) {
                if ($mComfp[$j][15] != "") {
                  $cSucId = $mComfp[$j][15];
                  $cDexId = $mComfp[$j][2];
                }
              }

              // Se obtiene el pedido del Dex
              $vDexPed  = array();
              $qDexPed  = "SELECT ";
              $qDexPed .= "docpedxx ";
              $qDexPed .= "FROM $cAlfa.sys00121 ";
              $qDexPed .= "WHERE ";
              $qDexPed .= "sucidxxx = \"$cSucId\" AND ";
              $qDexPed .= "docidxxx = \"$cDexId\" LIMIT 0,1 ";
              $xDexPed  = f_MySql("SELECT","",$qDexPed,$xConexion01,"");
              $vDexPed = mysql_fetch_array($xDexPed);

              $mPagosFac = array();
              $mPCC = array();
              $mPCC = f_Explode_Array($xRCD['commemod'],"|","~");
              for ($i=0; $i < count($mPCC); $i++) {
                $vConcepto = explode('^', $mPCC[$i][2]);

                // Se agrupan los pagos por cliente y numero de factura
                $nInd_mPagosFac = count($mPagosFac["{$mPCC[$i][11]}~{$mPCC[$i][5]}"]);
                $mPagosFac["{$mPCC[$i][11]}~{$mPCC[$i][5]}"][$nInd_mPagosFac]['ctodesxx'] = $vConcepto[0];
                $mPagosFac["{$mPCC[$i][11]}~{$mPCC[$i][5]}"][$nInd_mPagosFac]['numfactx'] = $mPCC[$i][5];
                $mPagosFac["{$mPCC[$i][11]}~{$mPCC[$i][5]}"][$nInd_mPagosFac]['vlrpccxx'] = ($mPCC[$i][15] + $mPCC[$i][16]);
              }

              // Informacion de los Pagos a Terceros
              foreach ($mPagosFac as $key => $mPagos) {
                $vIndice = explode("~", $key);
                $cCtoDes = '';
                $nCtoVlr = 0;

                // Se obtiene la descripcion de los pagos para concatenarlas y el valor de los pagos para sumarlos
                foreach($mPagos as $vPago) {
                  $cCtoDes .= trim($vPago['ctodesxx']) . " / ";
                  $nCtoVlr += $vPago['vlrpccxx'];
                }
                
                $nInd_mDatos = count($mDatos);
                $mDatos[$nInd_mDatos]['resprexx'] = $xRCD['resprexx'];
                $mDatos[$nInd_mDatos]['comcscxx'] = $xRCD['comcscxx'];
                $mDatos[$nInd_mDatos]['sucidxxx'] = $cSucId;
                $mDatos[$nInd_mDatos]['doiidxxx'] = $cDexId;
                $mDatos[$nInd_mDatos]['docpedxx'] = $vDexPed['docpedxx'];
                $mDatos[$nInd_mDatos]['cliidxxx'] = $xRCD['teridxxx'];
                $mDatos[$nInd_mDatos]['clinomxx'] = $xRCD['clinomxx'];
                $mDatos[$nInd_mDatos]['desctoxx'] = rtrim($cCtoDes, " / ");
                $mDatos[$nInd_mDatos]['tipopago'] = "REEMBOLSO";
                $mDatos[$nInd_mDatos]['numfactx'] = $vIndice[1];
                $mDatos[$nInd_mDatos]['vlrctoxx'] = $nCtoVlr;
                $mDatos[$nInd_mDatos]['vlrivaip'] = 0;
                $mDatos[$nInd_mDatos]['vlrtotal'] = $nCtoVlr;
              }

              // Información de los Ingresos Propios y valor del 4 x mil
              $nTotalIp = 0;
              $nIvaIp   = 0;
              $nVlrGmf  = 0;
              for ($cAno=substr($pArrayParametros['DDESDEXX'],0,4); $cAno <= substr($pArrayParametros['DHASTAXX'],0,4); $cAno++) {
                $qFcod  = "SELECT ";
                $qFcod .= "comctocx, ";
                $qFcod .= "comidc2x, ";
                $qFcod .= "comvlrxx, ";
                $qFcod .= "comvlr01 ";
                $qFcod .= "FROM $cAlfa.fcod$cAno ";
                $qFcod .= "WHERE ";
                $qFcod .= "$cAlfa.fcod$cAno.comidxxx = \"{$xRCD['comidxxx']}\" AND ";
                $qFcod .= "$cAlfa.fcod$cAno.comcodxx = \"{$xRCD['comcodxx']}\" AND ";
                $qFcod .= "$cAlfa.fcod$cAno.comcscxx = \"{$xRCD['comcscxx']}\" AND ";
                $qFcod .= "$cAlfa.fcod$cAno.comcsc2x = \"{$xRCD['comcsc2x']}\" AND ";
                $qFcod .= "$cAlfa.fcod$cAno.comctocx IN(\"IP\",\"PCC\") AND ";
                $qFcod .= "$cAlfa.fcod$cAno.regestxx = \"PROVISIONAL\" ";
                $xFcod  = f_MySql("SELECT","",$qFcod,$xConexion01,"");

                while($xRFD = mysql_fetch_assoc($xFcod)){
                  if ($xRFD['comctocx'] == "IP") {
                    // Obtiene el valor de los IP y el iva
                    $nIvaIp   += $xRFD['comvlr01'];
                    $nTotalIp += $xRFD['comvlrxx'];
                  } else if($xRFD['comctocx'] == "PCC" && $xRFD['comidc2x'] != "X") {
                    // Obtiene el valor del GMF
                    $nVlrGmf += $xRFD['comvlrxx'];
                  }
                }
              }

              if ($nTotalIp > 0) {
                $nInd_mDatos = count($mDatos);
                $mDatos[$nInd_mDatos]['resprexx'] = $xRCD['resprexx'];
                $mDatos[$nInd_mDatos]['comcscxx'] = $xRCD['comcscxx'];
                $mDatos[$nInd_mDatos]['sucidxxx'] = $cSucId;
                $mDatos[$nInd_mDatos]['doiidxxx'] = $cDexId;
                $mDatos[$nInd_mDatos]['docpedxx'] = $vDexPed['docpedxx'];
                $mDatos[$nInd_mDatos]['cliidxxx'] = $xRCD['teridxxx'];
                $mDatos[$nInd_mDatos]['clinomxx'] = $xRCD['clinomxx'];
                $mDatos[$nInd_mDatos]['desctoxx'] = "INGRESOS PROPIOS";
                $mDatos[$nInd_mDatos]['tipopago'] = "HONORARIOS";
                $mDatos[$nInd_mDatos]['numfactx'] = $xRCD['resprexx']."-".$xRCD['comcscxx'];
                $mDatos[$nInd_mDatos]['vlrctoxx'] = $nTotalIp;
                $mDatos[$nInd_mDatos]['vlrivaip'] = $nIvaIp;
                $mDatos[$nInd_mDatos]['vlrtotal'] = ($nTotalIp + $nIvaIp);
              }

              if ($nVlrGmf > 0) {
                $nInd_mDatos = count($mDatos);
                $mDatos[$nInd_mDatos]['resprexx'] = $xRCD['resprexx'];
                $mDatos[$nInd_mDatos]['comcscxx'] = $xRCD['comcscxx'];
                $mDatos[$nInd_mDatos]['sucidxxx'] = $cSucId;
                $mDatos[$nInd_mDatos]['doiidxxx'] = $cDexId;
                $mDatos[$nInd_mDatos]['docpedxx'] = $vDexPed['docpedxx'];
                $mDatos[$nInd_mDatos]['cliidxxx'] = $xRCD['teridxxx'];
                $mDatos[$nInd_mDatos]['clinomxx'] = $xRCD['clinomxx'];
                $mDatos[$nInd_mDatos]['desctoxx'] = "IMPUESTO PAGOS A TERCEROS 4XMIL";
                $mDatos[$nInd_mDatos]['tipopago'] = "REEMBOLSO";
                $mDatos[$nInd_mDatos]['numfactx'] = "";
                $mDatos[$nInd_mDatos]['vlrctoxx'] = $nVlrGmf;
                $mDatos[$nInd_mDatos]['vlrivaip'] = 0;
                $mDatos[$nInd_mDatos]['vlrtotal'] = $nVlrGmf;
              }
            }

            // Se recorre el array de mDatos para almacenar los registros en la tabla temporal
            for($j = 0; $j < count($mDatos); $j++) {
              $qInsReg .= "(\"{$mDatos[$j]['resprexx']}\",";
              $qInsReg .= "\"{$mDatos[$j]['comcscxx']}\",";
              $qInsReg .= "\"{$mDatos[$j]['sucidxxx']}\",";
              $qInsReg .= "\"{$mDatos[$j]['doiidxxx']}\",";
              $qInsReg .= "\"{$mDatos[$j]['docpedxx']}\",";
              $qInsReg .= "\"{$mDatos[$j]['cliidxxx']}\",";
              $qInsReg .= "\"{$mDatos[$j]['clinomxx']}\",";
              $qInsReg .= "\"{$mDatos[$j]['desctoxx']}\",";
              $qInsReg .= "\"{$mDatos[$j]['tipopago']}\",";
              $qInsReg .= "\"{$mDatos[$j]['numfactx']}\",";
              $qInsReg .= "\"{$mDatos[$j]['vlrctoxx']}\",";
              $qInsReg .= "\"{$mDatos[$j]['vlrivaip']}\",";
              $qInsReg .= "\"{$mDatos[$j]['vlrtotal']}\"),";

              $nCanReg01++;
              if (($nCanReg01 % _NUMREG_) == 0) {
                $xConexion01 = $objEstructuraReporteProformasBavaria->fnReiniciarConexionDBReporteProformasBavaria($xConexion01);

                // Insertando en bloque el inventario
                $qInsReg = substr($qInsReg, 0, -1);
                $qInsReg = $qInsCab.$qInsReg;
                // f_Mensaje(__FILE__,__LINE__,$qInsReg);
                
                if(!mysql_query($qInsReg,$xConexion01)) {
                  $nSwitch = 1;
                  $nSwitch_Detalle = 1;
                  $vError['LINEAERR'] = __LINE__;
                  $vError['TIPOERRX'] = "ERROR";
                  $vError['DESERROR'] = "Error al cargar datos en la tabla temporal";
                  $objEstructuraReporteProformasBavaria->fnGuardarErrorReporteProformasBavaria($vError);
                }
                $qInsReg = "";
              }
            }//for($j = 0; $j < count($mDatos); $j++) {

            if($nSwitch_Detalle == 0 && $qInsReg != ""){
              $xConexion01 = $objEstructuraReporteProformasBavaria->fnReiniciarConexionDBReporteProformasBavaria($xConexion01);

              $qInsReg = substr($qInsReg, 0, -1);
              $qInsReg = $qInsCab.$qInsReg;
              if(!mysql_query($qInsReg,$xConexion01)) {
                $nSwitch = 1;
                $vError['LINEAERR'] = __LINE__;
                $vError['TIPOERRX'] = "ERROR";
                $vError['DESERROR'] = "Error al cargar datos en la tabla temporal";
                $objEstructuraReporteProformasBavaria->fnGuardarErrorReporteProformasBavaria($vError);
              }
            }
          } ## for($k=0,$nReg=0;$k<=$nCanReg ;$k+=_NUMREG_) { ##
        } ## if ($nCanReg > 0) { ##
      } ## for ($nAno=$nAnioIni;$nAno<=$nAnioFin;$nAno++) { ##      

      if ($nSwitch == 0) {
        $vParametros = array();
        $vParametros['TABLAXXX'] = $pArrayParametros['TABLAXXX'];
        $vParametros['TABLAERR'] = $pArrayParametros['TABLAERR'];
        $vParametros['ORIGENXX'] = $pArrayParametros['ORIGENXX'];
        $mRespuesta = $this->fnGenerarReporteProformasBavaria($vParametros);

        if ($mRespuesta[0] == "true") {
          if ($vParametros['ORIGENXX'] == "TAREA") {
            // Se envia correo
            $vParametros = array();
            $vParametros['ARCHIVO'] = $mRespuesta[1];
            $vParametros['RUTAXXX'] = $mRespuesta[2];
            $mRespuesta = $this->fnNotificarReporteProformasBavaria($vParametros);

            if ($mRespuesta[0] == "false") {
              $nSwitch = 1;
              for ($nR=2; $nR<count($mRespuesta); $nR++) {
                $vError['LINEAERR'] = __LINE__;
                $vError['TIPOERRX'] = "ERROR";
                $vError['DESERROR'] = $mRespuesta[$nR];
                $objEstructuraReporteProformasBavaria->fnGuardarErrorReporteProformasBavaria($vError);
              }
            } else {
              $vError['LINEAERR'] = __LINE__;
              $vError['TIPOERRX'] = "EXITOSO";
              $vError['DESERROR'] = $mRespuesta[1];
              $objEstructuraReporteProformasBavaria->fnGuardarErrorReporteProformasBavaria($vError);
            }
          } else {
            $mReturn[1] = $mRespuesta[1];
          }
        } else {
          $nSwitch = 1;
          $vError['LINEAERR'] = __LINE__;
          $vError['TIPOERRX'] = "ERROR";
          $vError['DESERROR'] = "Error al generar los archivos";
          $objEstructuraReporteProformasBavaria->fnGuardarErrorReporteProformasBavaria($vError);
        }
      }

      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }
      return $mReturn;
    } ## function fnReporteProformasBavaria($pArrayParametros) { ##

    /**
     * Metodo para generar el archivo Excel del Reporte Proformas de Bavaria.
     */
    function fnGenerarReporteProformasBavaria($vpParametros){
      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser; global $OPENINIT;

      /**
       * Recibe como Parametro un Vector con las siguientes posiciones:
       * $vpParametros['TABLAXXX'] // Nombre Tabla Temporal
       * $vpParametros['TABLAERR'] // Nombre Tabla Error
      */

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;
  
      /**
       * Variable para hacer el retorno.
       * 
       * @var array
       */
      $mReturn[0] = "";

      /**
       * Variables para reemplazar caracteres especiales.
       * 
       * @var array
       */
      $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
      $cReempl = array('\"',"\'"," "," "," "," ");

      /**
       * Variable para alamacenar errores.
       * 
       * @var array
       */
      $vError = array();
      $vError['TABLAERR'] = $vpParametros['TABLAERR'];

      /**
       * Instanciando Objeto para el Guardado de Errores.
       */
      $objEstructuraReporteProformasBavaria = new cEstructurasReporteProformasBavaria();
  
      //validando que el nombre de la tabla Temporal no sea vacia
      if($vpParametros['TABLAXXX'] == ""){
        $nSwitch = 1;
        $vError['LINEAERR'] = __LINE__;
        $vError['TIPOERRX'] = "ERROR";
        $vError['DESERROR'] = "La Tabla Temporal del Reporte Proformas No puede ser Vacia.".mysql_error($xConexion01);
        $objEstructuraReporteProformasBavaria->fnGuardarErrorReporteProformasBavaria($vError);
      }

      if($nSwitch == 0){
        // Variable para armar la cadena de texto que se envia al excel.
        $cData = "";

        //Creando Archivo
        if ($vpParametros['ORIGENXX'] == "TAREA") {
          //Se crea en propios
          $cDirectorio = "{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa/reporte_bavaria";

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
          $cDirectorio = "{$OPENINIT['pathdr']}/opencomex/".$vSysStr['system_download_directory'];
        }

        $cFile = "REPORTE_PROFORMAS_EXPORTACION_BAVARIA_".$kUser."_".date('YmdHis').".xls";
        $cFileDownload = $cDirectorio."/".$cFile;
        $cF01 = fopen($cFileDownload,"a");

        $cData .= '<table width = "1600" border = 1 cellpadding = 0 cellspacing = 0 style = "padding-left:3px;padding-right:3px">';
        $cData .= '<tr>';
        $cData .= '<td align ="center" bgcolor = "'.$vSysStr['system_row_title_color_ini'].'" style = "width=120px;font-weight: bold;">Referencia Interna SIACO</td>';
        $cData .= '<td align ="center" bgcolor = "'.$vSysStr['system_row_title_color_ini'].'" style = "width=120px;font-weight: bold;">Proformas</td>';
        $cData .= '<td align ="center" bgcolor = "'.$vSysStr['system_row_title_color_ini'].'" style = "width=160px;font-weight: bold;">Booking</td>';
        $cData .= '<td align ="center" bgcolor = "'.$vSysStr['system_row_title_color_ini'].'" style = "width=300px;font-weight: bold;">REFERENCIA</td>';
        $cData .= '<td align ="center" bgcolor = "'.$vSysStr['system_row_title_color_ini'].'" style = "width=300px;font-weight: bold;">Descripci&oacute;n de Costos</td>';
        $cData .= '<td align ="center" bgcolor = "'.$vSysStr['system_row_title_color_ini'].'" style = "width=120px;font-weight: bold;">Tipo de gesti&oacute;n de pago</td>';
        $cData .= '<td align ="center" bgcolor = "'.$vSysStr['system_row_title_color_ini'].'" style = "width=120px;font-weight: bold;">FX TERCEROS</td>';;
        $cData .= '<td align ="center" bgcolor = "'.$vSysStr['system_row_title_color_ini'].'" style = "width=120px;font-weight: bold;">Sub total COPS</td>';
        $cData .= '<td align ="center" bgcolor = "'.$vSysStr['system_row_title_color_ini'].'" style = "width=120px;font-weight: bold;">IVA</td>';
        $cData .= '<td align ="center" bgcolor = "'.$vSysStr['system_row_title_color_ini'].'" style = "width=120px;font-weight: bold;">Total</td>';
        $cData .= '</tr>';
        fwrite($cF01,$cData);

        // Consultando los registros de la tabla temporal para generar el Excel
        $nCanReg01 = 0;
        $qTabTem  = "SELECT * ";
        $qTabTem .= "FROM $cAlfa.{$vpParametros['TABLAXXX']}";
        $xTabTem  = f_MySql("SELECT", "", $qTabTem, $xConexion01, "");

        if (mysql_num_rows($xTabTem) > 0) {
          while($xRTT = mysql_fetch_array($xTabTem)){
            $nCanReg01++;
            if (($nCanReg01 % _NUMREG_) == 0) {  $xConexion01 = fnReiniciarConexion(); }           

            $cData  = '<tr>';
            $cData .= '<td style="text-align:center;mso-number-format:\'@\';">'.(($xRTT['doiidxxx'] != "") ? $xRTT['doiidxxx'] : "&nbsp;").'</td>';
            $cData .= '<td style="text-align:center;mso-number-format:\'@\';">'.(($xRTT['comcscxx'] != "") ? $xRTT['resprexx'] . "-" . $xRTT['comcscxx'] : "&nbsp;").'</td>';
            $cData .= '<td style="text-align:left;mso-number-format:\'@\';">'.(($xRTT['docpedxx'] != "") ? $xRTT['docpedxx'] : "&nbsp;").'</td>';
            $cData .= '<td style="text-align:left;mso-number-format:\'@\';">'.(($xRTT['clinomxx'] != "") ? $xRTT['clinomxx'] : "&nbsp;").'</td>';
            $cData .= '<td style="text-align:left;mso-number-format:\'@\';">'.(($xRTT['desctoxx'] != "") ? $xRTT['desctoxx'] : "&nbsp;").'</td>';
            $cData .= '<td style="text-align:center;mso-number-format:\'@\';">'.(($xRTT['tipopago'] != "") ? $xRTT['tipopago'] : "&nbsp;").'</td>';
            $cData .= '<td style="text-center:left;mso-number-format:\'@\';">'.(($xRTT['numfactx'] != "") ? $xRTT['numfactx'] : "&nbsp;").'</td>';
            $cData .= '<td style="text-align:right;">'.(($xRTT['vlrctoxx'] != 0) ? number_format($xRTT['vlrctoxx'], 2, ',', '') : "0,00").'</td>';
            $cData .= '<td style="text-align:right;">'.(($xRTT['vlrivaip'] != 0) ? number_format($xRTT['vlrivaip'], 2, ',', '') : "0,00").'</td>';
            $cData .= '<td style="text-align:right;">'.(($xRTT['vlrtotal'] != 0) ? number_format($xRTT['vlrtotal'], 2, ',', '') : "0,00").'</td>';
            $cData .= '</tr>';
            fwrite($cF01,$cData);
          }

          $cData = '</table>';
          fwrite($cF01,$cData);
          fclose($cF01);
        } else {
          $nSwitch = 1;
          $vError['LINEAERR'] = __LINE__;
          $vError['TIPOERRX'] = "ERROR";
          $vError['DESERROR'] = "No se encontraron resgitros.<br>".mysql_error($xConexion01);
          $objEstructuraReporteProformasBavaria->fnGuardarErrorReporteProformasBavaria($vError);
        }

        if ($cData == "") {
          $nSwitch = 1;
          $vError['LINEAERR'] = __LINE__;
          $vError['TIPOERRX'] = "ERROR";
          $vError['DESERROR'] = "Error al Generar el Archivo Excel.<br>".mysql_error($xConexion01);
          $objEstructuraReporteProformasBavaria->fnGuardarErrorReporteProformasBavaria($vError);
        }

        if($nSwitch == 0){
          $mReturn[0] = "true";
          $mReturn[1] = $cFile;
          $mReturn[2] = $cFileDownload;
        }else{
          $mReturn[0] = "false";
        }

        return $mReturn;
      }
    }##function fnGenerarReporteProformasBavaria($vpParametros){##  

    /**
     * Envia el Correo Electronico a los clientes parametrizados en la variable siacosia_reporte_proformas
     * 
     * @param array  Archivos $pvParametros
     */
    function fnNotificarReporteProformasBavaria($pvParametros){
      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser; global $OPENINIT;

      $nSwitch = 0; 
      // Matriz de errores
      $vReturn    = array();
      $vReturn[0] = ""; // Se reserva la primera posicion para enviar true o false
      $vReturn[1] = ""; // Correos a los que se notifico

      $vNitCli =  explode(",", $vSysStr['siacosia_reporte_proformas']);
      $cNitCli = "\"".implode("\",\"", $vNitCli)."\"";

      // Correos para enviar la Notificacion
      $qCliEmai  = "SELECT ";
      $qCliEmai .= "CLIBAVCN ";
      $qCliEmai .= "FROM $cAlfa.SIAI0150 ";
      $qCliEmai .= "WHERE ";
      $qCliEmai .= "cliidxxx IN($cNitCli) AND ";
      $qCliEmai .= "regestxx = \"ACTIVO\" ";
      $xCliEmai  = f_MySql("SELECT","",$qCliEmai,$xConexion01,"");
      // echo $qCliEmai."~".mysql_num_rows($xCliEmai)."\n\n";
      $vCorreos = array();
      while($xRCE = mysql_fetch_assoc($xCliEmai)){
        if($xRCE['CLIBAVCN'] != ""){
          $vUsrEma = explode(",",$xRCE['CLIBAVCN']);
          for($nE=0; $nE<count($vUsrEma); $nE++) {
            if ($vUsrEma[$nE] != "" && in_array($vUsrEma[$nE],$vCorreos) == false) {
              $vCorreos[] = $vUsrEma[$nE];
            }
          }
        }
      }

      /**
       * Cadena con las variables de sistema que se necesitan contenadas
       */
      $cVariables .= "\"siacosia_phpmailer_usuario\""; 

      $qConsulta  = "SELECT stridxxx, strvlrxx ";
      $qConsulta .= "FROM $pcAlfa.sys00002 ";
      $qConsulta .= "WHERE stridxxx IN ($cVariables) AND ";
      $qConsulta .= "regestxx = \"ACTIVO\"";
      $xConsulta  = mysql_query($qConsulta, $xConexion01);
      // echo $qConsulta."~".mysql_num_rows($xConsulta);

      while($xRC = mysql_fetch_array($xConsulta)) {
        $vSysStr["{$xRC['stridxxx']}"] = $xRC['strvlrxx'];
      }
      mysql_free_result($xConsulta);

      /**
      * Vector con los archivos para adjuntar en el correo
      * @var array
      */
      $nInd_vFile = count($vFile);
      $vFile[$nInd_vFile]['ruta']    = $pvParametros['RUTAXXX'];
      $vFile[$nInd_vFile]['archivo'] = $pvParametros['ARCHIVO'];
      
      if (count($vCorreos) > 0) {
        $cDominio = $vSysStr['siacosia_phpmailer_usuario'];
        $cFrom    = "REPORTE PROFORMAS <$cDominio>";

        $cSubject = "REPORTE PROFORMAS";

        $cMessage  = "Buen d&iacute;a,<br><br>";
        $cMessage .= "A continuación adjunto Reporte Proformas Bavaria, <br><br>";
        $cMessage .= "Cordial Saludo.<br>";

        // Se envia el correo por la funcion mail de php
        //Modelo actual
        # segim OS Windows o Mac o Linux
        switch (strtoupper(substr(PHP_OS,0,3))) {
          case "MAC":
            $cFin = "\r" ;
          break;
          case "WIN":
            $cFin = "\r\n" ;
          break;
          default:
            $cFin = "\n" ;
          break;
        }

        $cHeaders = "From: $cFrom";
        
        // boundary 
        $semi_rand = md5(time()); 
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
          
        // headers for attachment 
        $cHeaders .= "\nMIME-Version: 1.0$cFin" . "Content-Type: multipart/mixed;$cFin" . " boundary=\"{$mime_boundary}\"$cFin"; 
          
        // multipart boundary 
        $cMessage = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $cMessage . "\n\n"; 
        $cMessage .= "--{$mime_boundary}\n";
        
        // Preparo el archivo adjunto
        for ($nA=0; $nA<count($vFile); $nA++) {
          if(filesize($vFile[$nA]['ruta']) <= (1024*1024)){
            $file = fopen($vFile[$nA]['ruta'],"rb");
            $data = fread($file,filesize($vFile[$nA]['ruta']));
            fclose($file);
            $data = chunk_split(base64_encode($data));
            $name = $vFile[$nA]['archivo'];
            $cMessage .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$name\"\n" . 
            "Content-Disposition: attachment;\n" . " filename=\"$name\"\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
            $cMessage .= "--{$mime_boundary}\n";
          } else {
            $nSwitch = 1;
            $vReturn[count($vReturn)] = "Para el Reporte de Proformas el Archivo Adjunto Supera Limite de Tamano [1024]. Favor Comunicar este Error a openTecnologia S.A.\n";
          }
        }

        // send
        if ($nSwitch == 0) {
          $cCorreos = "";
          for ($nC=0; $nC<count($vCorreos); $nC++) {
            if ($vCorreos[$nC] != "") {
              //Enviando correos a los contactos y director(es) de Cuenta del o los Do
              $xMail = mail($vCorreos[$nC], $cSubject, $cMessage, $cHeaders);
              if(!$xMail){
                $nSwitch = 1;
                $vReturn[count($vReturn)] = "Para el Reporte de Proformas Error al Enviar Correo al destinatario [{$vCorreos[$nC]}]. Favor Comunicar este Error a openTecnologia S.A.\n";
              }
              $cCorreos .= "{$vCorreos[$nC]}, ";
            }
          }
          $cCorreos = substr($cCorreos, 0, strlen($cCorreos)-2);
        }
        
      } else {
        $nSwitch = 1;
        $vReturn[count($vReturn)] = "No Se Encontraron Correos Parametrizados.";
      }

      if ($nSwitch == 0) {
        $vReturn[0] = "true";
        $vReturn[1] = "Se Envio el Reporte a los siguientes correos: ".$cCorreos;
      } else {
        $vReturn[0] = "false";
      }

      return $vReturn;
    }//function fnNotificarReporteProformasBavaria($pcCorreos,$pcAsunto,$pcMsj) {
  

  } ## class cRoporteProformasBavaria { ##


  class cEstructurasReporteProformasBavaria{
    /**
     * Metodo que se encarga de Crear las Estructuras de las Tablas
     */
    function fnCrearEstructurasReporteProformasBavaria($pParametros){
      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

      /**
       *Recibe como Parametro un vector con las siguientes posiciones:
       *$pArrayParametros['TIPOTABL] //TIPO DE ESTRUCTURA
       */

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
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
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionTM = $this->fnConectarDBReporteProformasBavaria();
      if($mReturnConexionTM[0] == "true"){
        $xConexionTM = $mReturnConexionTM[1];
      }else{
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnConexionTM);$nR++){
          $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
        }
      }

      /**
       * Random para Nombre de la Tabla
       */
      $cTabCar  = mt_rand(1000000000, 9999999999);

      switch($pParametros['TIPOTABL']){
        case "TEMPORAL":
          $cTabla = "memrepba".$cTabCar;

          $cTabCar  = mt_rand();
          $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
        	$qNewTab .= "lineaidx int(11)       NOT NULL AUTO_INCREMENT,"; //Id Autoincremantal
          $qNewTab .= "resprexx varchar(6)    NOT NULL,"; //Prefijo del Comprobante
          $qNewTab .= "comcscxx varchar(20)   NOT NULL,"; //Consecutivo Uno del Comprobante
          $qNewTab .= "sucidxxx varchar(3)    NOT NULL,"; //Sucursal del Dex
          $qNewTab .= "doiidxxx varchar(20)   NOT NULL,"; //Dex
          $qNewTab .= "docpedxx varchar(250)  NOT NULL,"; //Pedido del Dex
          $qNewTab .= "cliidxxx varchar(12)   NOT NULL,"; //Nit del Cliente 
          $qNewTab .= "clinomxx varchar(250)  NOT NULL,"; // Nombre del Cliente
          $qNewTab .= "desctoxx text          NOT NULL,"; //Descripcion del Concepto
          $qNewTab .= "tipopago varchar(20)   NOT NULL,"; //Tipo de Pago
          $qNewTab .= "numfactx varchar(20)   NOT NULL,"; //Numero de Factura
          $qNewTab .= "vlrctoxx decimal(15,2) NOT NULL,"; //Tipo de Pago
          $qNewTab .= "vlrivaip decimal(15,2) NOT NULL,"; //Tipo de Pago
          $qNewTab .= "vlrtotal decimal(15,2) NOT NULL,"; //Tipo de Pago
          $qNewTab .= " PRIMARY KEY (lineaidx)) ENGINE=MyISAM ";
          $xNewTab = mysql_query($qNewTab,$xConexionTM);

          if(!$xNewTab) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "(".__LINE__.") Error al Crear Tabla Temporal .".mysql_error($xConexionTM);
          }

        break;
        case "ERRORES":
          $cTabla = "memerror".$cTabCar;

          $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
          $qNewTab .= "LINEAIDX INT(11) NOT NULL AUTO_INCREMENT,";//LINEA
          $qNewTab .= "LINEAERR VARCHAR(10) NOT NULL,";           //LINEA DEL ARCHIVO
          $qNewTab .= "TIPOERRX VARCHAR(20) NOT NULL,";           //TIPO DE ERROR
          $qNewTab .= "DESERROR TEXT NOT NULL,";                  //DESCRIPCION DEL ERROR
          $qNewTab .= " PRIMARY KEY (LINEAIDX), ";
          $qNewTab .= " KEY (TIPOERRX)) ENGINE=MyISAM ";
          $xNewTab  = mysql_query($qNewTab,$xConexionTM);
          //f_Mensaje(__FILE__,__LINE__,$qNewTab);

          if(!$xNewTab) {
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal de Errores.".mysql_error($xConexionTM);
          }
        break;
        default:
          $nSwitch = 1;
          $mReturn[count($mReturn)] = "No se Recibio Tipo de Estructura a Crear";
        break;
      }

      if($nSwitch == 0){
        $mReturn[0] = "true"; $mReturn[1] = $cTabla;
      }else{
        $mReturn[0] = "false";
      }
      return $mReturn;
    } ## function fnCrearEstructurasReporteProformasBavaria(){ ##

    /**
     * Metodo que se encarga de Borrar las Estructuras de las Tablas
     */
    function fnBorrarEstructurasReporteProformasBavaria() {
      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      /**
       * Matriz para Retornar Valores.
       * 
       * @var array
       */
      $mReturn = array();

      /**
       * Reservando Primera Posición para retorna true o false.
       */
      $mReturn[0] = "";

      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionTM = $this->fnConectarDBReporteProformasBavaria();
      if($mReturnConexionTM[0] == "true"){
        $xConexionTM = $mReturnConexionTM[1];
      }else{
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnConexionTM);$nR++){
          $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
        }
      }

      $qDroTab  = "SELECT table_schema,table_name ";
      $qDroTab .= "FROM information_schema.TABLES ";
      $qDroTab .= "WHERE ";
      $qDroTab .= "table_schema = \"$cAlfa\" AND ";
      $qDroTab .= "table_name LIKE 'mem_______________' AND (UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(create_time)) > (2*60*60)";
      $xDroTab  = mysql_query($qDroTab,$xConexionTM);
      while($xRDT = mysql_fetch_array($xDroTab)){
        $qDrop  = "DROP TABLE IF EXISTS $cAlfa.{$xRDT['table_name']} ";
        $xDrop  = mysql_query($qDrop,$xConexionTM);
      }
      mysql_free_result($xDroTab);

    }##function fnBorrarEstructurasReporteProformasBavaria() {##

    /**
     * Metodo que realiza la conexion.
     */
    function fnConectarDBReporteProformasBavaria(){
      global $cAlfa;

      /**
       * Variable para saber si hay o no errores de validacion.
       *
       * @var int
       */
      $nSwitch = 0;

      /**
       * Matriz para Retornar Valores.
       * 
       * @var array
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
        $mReturn[0] = "true"; $mReturn[1] = $xConexion99;
      }else{
        $mReturn[0] = "false";
      }
      return $mReturn;
    }##function fnConectarDBReporteProformasBavaria(){##

    /**
     * Metodo que realiza el reinicio de la conexion.
     */
    function fnReiniciarConexionDBReporteProformasBavaria($pConexion){
      global $cHost;  global $cUserHost;  global $cPassHost;

      mysql_close($pConexion);
      $xConexion01 = mysql_connect($cHost,$cUserHost,$cPassHost,TRUE);

      return $xConexion01;
    }##function fnReiniciarConexionDBReporteProformasBavaria(){##

    /**
     * Metodo que se encarga de Guardar los Errores Generados por los Metodos de Interfaces.
     */
    function fnGuardarErrorReporteProformasBavaria($pArrayParametros){
      global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

      /**
       * Recibe como parametro un vector con los siguientes campos
       * $pArrayParametros['TABLAERR']  //TABLA ERROR
       * $pArrayParametros['LINEAERR']  //LINEA ERROR
       * $pArrayParametros['TIPOERRX']  //TIPO DE ERROR
       * $pArrayParametros['DESERROR']  //DESCRIPCION DEL ERROR
       * $pArrayParametros['MOSTRARX']  //INDICA SI SE DEBE PINTAR O NO EL ERROR.  EN SI O VACIO SE PINTA.
       */

      /**
       * Variables para reemplazar caracteres especiales.
       * 
       * @var array
       */
      $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
      $cReempl = array('\"',"\'"," "," "," "," ");

      if($pArrayParametros['TABLAERR'] != ""){

         $qInsert = array(array('NAME'=>'LINEAERR','VALUE'=>$pArrayParametros['LINEAERR']                                 ,'CHECK'=>'NO'),
                          array('NAME'=>'TIPOERRX','VALUE'=>$pArrayParametros['TIPOERRX']                                 ,'CHECK'=>'NO'),
                          array('NAME'=>'DESERROR','VALUE'=>str_replace($cBuscar,$cReempl,$pArrayParametros['DESERROR'])  ,'CHECK'=>'NO'));

        f_MySql("INSERT",$pArrayParametros['TABLAERR'],$qInsert,$xConexion01,$cAlfa);
      }
    }##function fnGuardarErrorReporteProformasBavaria($pParametros){##

    /*+ 
     * Metodo para capturar la informacion del motor de DB asosciada al query.
     */
    function fnMysqlQueryInfo($xConexion,$xQueryTime) {

      global $cSystemPath; global $cAlfa; global $_SERVER; global $kDf;

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
      $copenComex .= "{$kDf[4]}~";
      $copenComex .= "{$_SERVER['PHP_SELF']}~";
      $copenComex .= "$cIP~";
      $copenComex .= "$cHost~";
      $copenComex .= "{$kDf[3]}~";
      $copenComex .= date("Y-m-d")."~";
      $copenComex .= date("H:i:s");
      $copenComex .= "|";
      $xopenComex = mysql_query("SET @opencomex = \"$copenComex\"",$xConexion);
      $xQueryInfo = mysql_query("SET @mysqlinfo = \"$cQueryInfo\"",$xConexion);
    } ## function f_Mysql_Query_Info($xConexion,$xQueryTime) {
    ## Metodo para capturar la informacion del motor de DB asosciada al query
  }

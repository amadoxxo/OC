<?php

/**
 * uticupro.php : Utility de Clases del Modulo de Financiacion Cliente (cupos Roldan)
 *
 * Este script contiene la colecciones de clases para el Modulo de Financiacion Cliente (cupos Roldan)
 * @author Johana Arboleda <johana.arboleda@opentecnologia.com.co>
 * @version 001
 * @package openComex
 */

// ini_set('error_reporting', E_ALL);
// ini_set("display_errors","1");

set_time_limit(0);
ini_set("memory_limit","4096M");

define("_NUMREG_",50);

class cFinanciacionCliente {

  /**
  * Metodo para el Control de Cupos por el Modulo de Financiacion Cliente
  * Se debe tener mucho cuidado con este metodo, porque aunque genera el REPORTE
  * esta funcion realiza el CONTROL DE CUPOS
  */
  function fnCupoFinanciacionCliente($pvParametros) {
    global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

    /**
    * Recibe como Parametro un vector con las siguientes posiciones:
    * $pvParametros['origenxx'], CONTROL o REPORTE o SALDO
    * $pvParametros['clientes'][$i], array con los nit clientes
    * $pvParametros['tramites'][$i]['cliidxxx'], Cliente
    * $pvParametros['tramites'][$i]['sucidxxx'], sucursal
    * $pvParametros['tramites'][$i]['docidxxx'], DO
    * $pvParametros['tramites'][$i]['docsufxx'], Sufijo
    * $pvParametros['tramites'][$i]['ctoidxxx'], Concepto
    * $pvParametros['tramites'][$i]['comvlrxx'], Valor Trámite
    *
    * Desde la alerta de control vencimiento financiacion cliente se envia la base de datos y el usuarios
    * $pvParametros['database']
    * $pvParametros['usridxxx']
    * $pvParametros['conexion']
    * $pvParametros['varsisxx']
    */

    if ($pvParametros['database'] != "") {
      $cAlfa       = $pvParametros['database'];
      $kUser       = $pvParametros['usridxxx'];
      $xConexion01 = $pvParametros['conexion'];
      $vSysStr     = $pvParametros['varsisxx'];
    }

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
    * Variable para hacer el retorno.
    * En la posicion 0 se retorna true o false
    * En las demas posiciones se envian los mensajes de error
    * @var array
    */

    $mReturn = array();
    $mReturn[0] = "";

    /**
    * Instanciando Objeto para la creacion de las tablas temporales.
    */
    $objTablasTemporales = new cEstructurasFinanciacionCliente();

    /**
    * Clientes en el filtro de busqueda
    */
    $cCliId = "";
    for ($i = 0; $i<count($pvParametros['clientes']); $i++) {
      $cCliId .= "\"{$pvParametros['clientes'][$i]}\",";
    }
    $cCliId = substr($cCliId, 0, -1);

    //  $tInicio = microtime(true);

    /**
      * Condiciones para el calculo del cupo del Cliente:
      * - Se aplica si la variable del sistema system_activar_control_financiacion_clientes esta activa
      *
      * Se deben buscar:
      * - Todos los pagos a terceros no facturados del cliente y sus anticipos
      * - Los tributos aduaneros
      *   se calcula con el control de cupos para pagos de tributos
      * - Todos los anticipos a proveedor que no hayan sido cancelados
      * - Todos las financiaciones operativas asociadas al cliente
      * - Todas las financiaciones manuales de los pagos a terceros
      * - Cartera pendiente del cliente que no tenga Recibo de Caja que cruce el pago de facturas del cliente en PCC.
      *
      * Considerciones generales:
      * Si en la parametricacion de cupo por cleinte Opción Aplica control de Financiación para TRIBUTOS ADUANEROS:  
      * - Si esta opción es seleccionada, al momento de guardar una carta de tributos aduaneros, módulo de cartas bancarias o documento de causación a terceros, 
      *   el sistema debe realizar el control de cupo sobre los conceptos de tributos aduaneros para carta bancaria y en causación sobre la categoría 
      *   con la lógica de control de cupos de cliente (estándar para Roldan).
      * - Si esta opción no se encuentra seleccionada, el sistema no debe aplicar la lógica de control de cupos del cliente, sino que debe realizar la logica 
      *   especial para contro de tributos.
      *
      * La formula para el calculo del cupo es:
      * + Financiación Asignada al cliente (General (financiero_cupo_autorizado_para_clientes_sin_cupo) o Específica (fpar0147))
      * + Anticipos
      * + Fondos Operativos (roldanlo_conceptos_fondos_operativos_control_financiacion_clientes)
      * + Financiación manual (fpar0148)
      * - Cartera pendiente del cliente que no tenga Recibo de Caja que cruce el pago de facturas del cliente en PCC.
      * - Menos todos los PCC no facturados (los TRIBUTOS se incluyen si en la financiacion del cliente esta que aplican)
      * - anticipos a proveedores no pagados (roldanlo_conceptos_anticipos_a_proveedores_control_financiacion_clientes)
      
      * La formaula para el control de cupos para conceptos de pagos de tributos es:
      * Aplica para los comprobantes de cartas bancarias generadas por el sistema y para los conceptos marcados como pago a tributos,
      * o los conceptos asociados a la categoria parametrizada en a la variable del sistema roldanlo_categoria_tributos_control_financiacion_clientes 
      * La carta bancaria y/o la causación solo puede guardarse si el DO tiene anticipo disponible. El sistema debe verificar si los anticipos 
      * ya fueron usado en otras operaciones, y solo tener en cuenta el saldo disponible de dichos anticipos.
      *
      * Los anticipos y financiacion manual, debe aplicar para su tramite correspondiente,
      * Si un anticipo excede el valor de los pagos a terceros del DO, el sobrante no suma en el cupo, porque estaria afectando tramites que no tienen anticipos
      * Igual para la financiacion manual por DO y Concepto, solo debe afectar al DO y concepto correspondiente,
      * si hay excedentes no suman en el control de cupos
    */

    // echo "<pre>";
    // print_r($pvParametros);
    // echo "</pre>";

    // roldanlo_cuentas_cxc_facturas_cliente_financiacion_clientes        => CxC Clientes y Saldos a favor del cliente: 1305100100, 1305050100,1305050200
    // roldanlo_cuentas_cxp_saldos_a_favor_cliente_financiacion_clientes  => Saldos a favor CxP: 2305050100, 2305100100, 2305050200
    // roldanlo_cuentas_pcc_financiacion_clientes                         => PCC: 13102000100
    // roldanlo_cuentas_anticipos_operativos_financiacion_clientes        => Anticipos operativos: 2805050100
    // roldanlo_cuentas_fondos_operativos_financiacion_clientes           => Fondos operativos: 2805050300
    // roldanlo_cuentas_anticipos_proveedor_financiacion_clientes         => Anticipos a proveedor: 1330050100
    
    //Si el metodo es llamado desde con la opcion CONTROL, es porque se va a analizar
    //unos tramites en particular, por lo que el listado de clientes y tramites es obligatorio

    if ($pvParametros['origenxx'] == "CONTROL") {
      if (count($pvParametros['clientes']) == 0 || count($pvParametros['tramites']) == 0) {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "Parametros Incompletos para el Control de Cupos, Por Favor Comuniquese con openTecnologia S.A.";
      }
    }

    if ($nSwitch == 0) {

      //Buscando Tipo financiacion cliente y cartera vencida del cliente
      $mClientes   = array(); //Vector con la informacion de financiacion de lo clientes enviados en la matriz
      $vIdClientes = array(); //Nits de lo clientes enviados en la matriz, para buscar su informacion solo una vez
      $mFinManual  = array(); //Vector con la informacion de la financiacicion manual
      $nTotalCausar= 0;     //Valor total de los pagos que se quieren causar
      $vTramCausar = array(); //Tramites que se quieren causar

      $qClientes  = "SELECT ";
      $qClientes .= "CLIIDXXX, ";
      $qClientes .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) AS CLINOMXX ";
      $qClientes .= "FROM $cAlfa.SIAI0150 ";
      $qClientes .= "WHERE ";
      $qClientes .= "CLICLIXX = \"SI\" AND ";
      if ($cCliId != "") {
        $qClientes .= "CLIIDXXX IN ($cCliId) AND ";
      }
      $qClientes .= "REGESTXX = \"ACTIVO\"";
      $nQueryTimeStart = microtime(true); $xClientes  = mysql_query($qClientes,$xConexion01);
      $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
      // echo $qClientes."~".mysql_num_rows($xClientes)."<br>";
      
      $nCanReg = 0;
      while ($xRC = mysql_fetch_array($xClientes)) {
        $nCanReg++;
        if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01); }

        $vIdClientes[count($vIdClientes)] = $xRC['CLIIDXXX'];
        $mClientes["{$xRC['CLIIDXXX']}"]['cliidxxx'] = $xRC['CLIIDXXX'];
        $mClientes["{$xRC['CLIIDXXX']}"]['clinomxx'] = $xRC['CLINOMXX'];

        //Inicializando Valor de pagos a terceros pendientes de pagos
        $mClientes["{$xRC['CLIIDXXX']}"]['carterap'] = 0; //Cartera Pagos a Terceros Pendiente de pago
        $mClientes["{$xRC['CLIIDXXX']}"]['carterai'] = 0; //Cartera Pagos a Ingresos Propios

        //Busacando el cupo de financiacion del cliente
        $qCupFin  = "SELECT * ";
        $qCupFin .= "FROM $cAlfa.fpar0147 ";
        $qCupFin .= "WHERE ";
        $qCupFin .= "cliidxxx = \"{$xRC['CLIIDXXX']}\" AND ";
        $qCupFin .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $nQueryTimeStart = microtime(true); $xCupFin  = mysql_query($qCupFin,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
        // f_Mensaje(__FILE__,__LINE__,$qCupFin."~".mysql_num_rows($xCupFin));
        //  echo $qCupFin."~".mysql_num_rows($xCupFin)."<br>";
        if (mysql_num_rows($xCupFin) == 0) {
          $mClientes["{$xRC['CLIIDXXX']}"]['fpccupox'] = $vSysStr['financiero_cupo_autorizado_para_clientes_sin_cupo'];
          $mClientes["{$xRC['CLIIDXXX']}"]['fpcfecxx'] = "0000-00-00";
          $mClientes["{$xRC['CLIIDXXX']}"]['tipofina'] = "MANUAL";
          $mClientes["{$xRC['CLIIDXXX']}"]['fpcatria'] = "NO"; //Aplica control de Financiación para TRIBUTOS ADUANEROS
          $mClientes["{$xRC['CLIIDXXX']}"]['anticipo'] = 0; //Anticipos No facturados
          $mClientes["{$xRC['CLIIDXXX']}"]['pagoster'] = 0; //Pagos a terceros No facturados y los que se quieren causar
          $mClientes["{$xRC['CLIIDXXX']}"]['fondoope'] = 0; //Fondos Operativos
          $mClientes["{$xRC['CLIIDXXX']}"]['antprove'] = 0; //Anticipos a Proveedor
          $mClientes["{$xRC['CLIIDXXX']}"]['carterap'] = 0; //Cartera Pagos a Terceros Pendiente de pago
          $mClientes["{$xRC['CLIIDXXX']}"]['finmanxx'] = 0; //Financiacion Manual
          $mClientes["{$xRC['CLIIDXXX']}"]['tramites'] = array(); //Tramites
        } else {
          $vCupFin = mysql_fetch_assoc($xCupFin);
          //Si el tipo de cupo es SIN-CUPO se asigna el cupo de la variable del sistema
          $mClientes["{$xRC['CLIIDXXX']}"]['fpccupox'] = (($vCupFin['fpctipxx'] == "SIN-CUPO") ? $vSysStr['financiero_cupo_autorizado_para_clientes_sin_cupo'] : $vCupFin['fpccupox']) + $vCupFin['fpcsobgi'];
          $mClientes["{$xRC['CLIIDXXX']}"]['fpcfecxx'] = $vCupFin['fpcfecxx'];
          $mClientes["{$xRC['CLIIDXXX']}"]['tipofina'] = "AUTOMATICA";
          $mClientes["{$xRC['CLIIDXXX']}"]['fpcatria'] = $vCupFin['fpcatria']; //Aplica control de Financiación para TRIBUTOS ADUANEROS
          $mClientes["{$xRC['CLIIDXXX']}"]['anticipo'] = 0; //Anticipos No facturados
          $mClientes["{$xRC['CLIIDXXX']}"]['pagoster'] = 0; //Pagos a terceros No facturados y los que se quieren causar
          $mClientes["{$xRC['CLIIDXXX']}"]['fondoope'] = 0; //Fondos Operativos
          $mClientes["{$xRC['CLIIDXXX']}"]['antprove'] = 0; //Anticipos a Proveedor
          $mClientes["{$xRC['CLIIDXXX']}"]['carterap'] = 0; //Cartera Pagos a Terceros Pendiente de pago
          $mClientes["{$xRC['CLIIDXXX']}"]['finmanxx'] = 0; //Financiacion Manual
          $mClientes["{$xRC['CLIIDXXX']}"]['tramites'] = array(); //Tramites
        }
        $xFree = mysql_free_result($xCupFin);
      }

      //CxC y CxP de Documentos vencidos
      $cDocumentos = "";

      if (count($vIdClientes) > 0) {

        $vPucIdCxC = explode(",",$vSysStr['roldanlo_cuentas_cxc_facturas_cliente_financiacion_clientes']);
        $cPucIdCxC = "\"".implode("\",\"", $vPucIdCxC)."\"";

        //Buscando si tiene Cartera Vencida
        $qCxC  = "SELECT * ";
        $qCxC .= "FROM $cAlfa.fcxc0000 ";
        $qCxC .= "WHERE ";
        $qCxC .= "comidxxx = \"F\" AND ";
        $qCxC .= "pucidxxx IN ($cPucIdCxC) AND ";
        if ($cCliId != "") {
          $qCxC .= "teridxxx IN ($cCliId) AND ";
        }
        $qCxC .= "comsaldo > 0 ";
        $nQueryTimeStart = microtime(true); $xCxC  = mysql_query($qCxC,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
        // f_Mensaje(__FILE__,__LINE__,$qCxC."~".mysql_num_rows($xCxC));
        // echo $qCxC."~".mysql_num_rows($xCxC)."<br><br>";
        $nCanReg01=0;
        while ($xRCxC = mysql_fetch_assoc($xCxC)) {
          $nCanReg01++;
          if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01); }

          if ($pvParametros['origenxx'] != "REPORTE") {
            $dActual = date_create(date('Y-m-d'));
            $dVencimiento = date_create($xRCxC['comfecve']);
            if ($dVencimiento < $dActual) {
              $cDocumentos .= "{$xRCxC['comidxxx']}-{$xRCxC['comcodxx']}-{$xRCxC['comcscxx']}, Fecha Vencimiento: {$xRCxC['comfecve']}.\n";
            }
          }

          //Buscando si el registro existe en cabecera
          $nAno = substr($xRCxC['regfcrex'],0,4);

          $qFcoc  = "SELECT ";
          $qFcoc .= "comvlrxx,";
          $qFcoc .= "comvlr01,";
          $qFcoc .= "comvlr02,";
          $qFcoc .= "comvlr03,";
          $qFcoc .= "comifxxx,";
          $qFcoc .= "comipxxx,";
          $qFcoc .= "comivaxx,";
          $qFcoc .= "comrftex,";
          $qFcoc .= "comrcrex,";
          $qFcoc .= "comrivax,";
          $qFcoc .= "comricax,";
          $qFcoc .= "comarfte,";
          $qFcoc .= "comarcre,";
          $qFcoc .= "comarica ";
          $qFcoc .= "FROM $cAlfa.fcoc$nAno ";
          $qFcoc .= "WHERE ";
          $qFcoc .= "comidxxx = \"{$xRCxC['comidxxx']}\" AND ";
          $qFcoc .= "comcodxx = \"{$xRCxC['comcodxx']}\" AND ";
          $qFcoc .= "comcscxx = \"{$xRCxC['comcscxx']}\" LIMIT 0,1";
          $nQueryTimeStart = microtime(true); $xFcoc  = mysql_query($qFcoc,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
          //f_Mensaje(__FILE__,__LINE__,$qFcoc." ~ ".mysql_num_rows($xFcoc));
          // echo $qFcoc." ~ ".mysql_num_rows($xFcoc)."<br><br>";
          if (mysql_num_rows($xFcoc) > 0) {
            $vFcoc = mysql_fetch_assoc($xFcoc);
            $xRCxC['comvlrxx'] = $vFcoc['comvlrxx'];
            $xRCxC['comvlr01'] = $vFcoc['comvlr01'];
            $xRCxC['comvlr02'] = $vFcoc['comvlr02'];
            $xRCxC['comvlr03'] = $vFcoc['comvlr03'];
            $xRCxC['comifxxx'] = $vFcoc['comifxxx'];
            $xRCxC['comipxxx'] = $vFcoc['comipxxx'];
            $xRCxC['comivaxx'] = $vFcoc['comivaxx'];
            $xRCxC['comrftex'] = $vFcoc['comrftex'];
            $xRCxC['comrcrex'] = $vFcoc['comrcrex'];
            $xRCxC['comrivax'] = $vFcoc['comrivax'];
            $xRCxC['comricax'] = $vFcoc['comricax'];
            $xRCxC['comarfte'] = $vFcoc['comarfte'];
            $xRCxC['comarcre'] = $vFcoc['comarcre'];
            $xRCxC['comarica'] = $vFcoc['comarica'];

            $nCaretra_Original    = ($xRCxC['comvlr01'] * -1) + $xRCxC['comvlr02'] + $xRCxC['comvlr03'] + $xRCxC['comifxxx'] + $xRCxC['comipxxx'] + $xRCxC['comivaxx'] - ($xRCxC['comrftex'] + $xRCxC['comrcrex'] + $xRCxC['comrivax'] + $xRCxC['comricax']) + ($xRCxC['comarfte'] + $xRCxC['comarcre'] + $xRCxC['comarica']);
            $nSaldo_Actual_Cartera = $xRCxC['comsaldo'];
            $nCartera_Valor_Pagado = ($nCaretra_Original - $nSaldo_Actual_Cartera);

            //  echo "Caretra_Original: ".$nCaretra_Original."~Saldo_Actual_Cartera: ".$nSaldo_Actual_Cartera."~Cartera_Valor_Pagado: ".$nCartera_Valor_Pagado."<br>";

            $nAnticipos        = ($xRCxC['comvlr01'] * -1);
            $nPagos_Terceros   = ($xRCxC['comvlr02'] + $xRCxC['comvlr03'] + $xRCxC['comifxxx']);
            $nIngresos_Propios = ($xRCxC['comipxxx'] + $xRCxC['comivaxx'] - ($xRCxC['comrftex'] + $xRCxC['comrcrex'] + $xRCxC['comrivax'] + $xRCxC['comricax']) + ($xRCxC['comarfte'] + $xRCxC['comarcre'] + $xRCxC['comarica']));

            //  echo "Anticipos: ".$nAnticipos."~Pagos_Terceros: ".$nPagos_Terceros."~Ingresos_Propios: ".$nIngresos_Propios."<br>";
            // Verifico si el anticipo alcanzo para pagar los pagos a terceros
            if ($xRCxC['comvlr01'] < $nPagos_Terceros) { // El anticipo no alcanzo para cubrir los pagos a terceros
              $nCartera_PCC_FORMS_IF     = ($nAnticipos + $nPagos_Terceros);
              $nCartera_IP_IVA_RETENCIONES = $nIngresos_Propios;
            } else { // El anticipo si alcanzo para cubrir los pagos a terceros
              $nCartera_PCC_FORMS_IF     = 0;
              $nCartera_IP_IVA_RETENCIONES = (($nAnticipos + $nPagos_Terceros) + $nIngresos_Propios);
            }
            // Fin de Verifico si el anticipo alcanzo para pagar los pagos a terceros

            if ($nSaldo_Actual_Cartera <= $nCaretra_Original) { // Pregunto si hubo abonos a la cartera

              if ($nCartera_Valor_Pagado <= $nCartera_PCC_FORMS_IF) { // Es porque el valor abonado a la cartera cubre parcial o total el valor de los PCC
                $mClientes["{$xRCxC['teridxxx']}"]['carterap'] += ($nCartera_PCC_FORMS_IF - $nCartera_Valor_Pagado);
                $mClientes["{$xRCxC['teridxxx']}"]['carterai'] += $nCartera_IP_IVA_RETENCIONES;
              } else { // Es porque el valor abonado a la cartera cubre el total de los PCC y alcanza para los ingresos propios parcial o total
                $mClientes["{$xRCxC['teridxxx']}"]['carterap'] += 0; // Sumo cero a la acrtera de PCC porque el abono cubrio toda la cartera de PCC
                $mClientes["{$xRCxC['teridxxx']}"]['carterai'] += ($nCartera_IP_IVA_RETENCIONES - ($nCartera_Valor_Pagado - $nCartera_PCC_FORMS_IF));
              }
            } else { // Entra por aqui si no hubo abonos
              $mClientes["{$xRCxC['teridxxx']}"]['carterap'] += ($nSaldo_Actual_Cartera - $nIngresos_Propios);
              $mClientes["{$xRCxC['teridxxx']}"]['carterai'] += $nIngresos_Propios;
            }
          } else {
            //Si no se encontro la factura en el movimiento contable, se busca en saldos iniciales y se lleva el valor
            //a los PCC
            for($nAnoC=$vSysStr['financiero_ano_instalacion_modulo'];$nAnoC<=date('Y');$nAnoC++) {
              $qFcod  = "SELECT ";
              $qFcod .= "comvlrxx ";
              $qFcod .= "FROM $cAlfa.fcod$nAnoC ";
              $qFcod .= "WHERE ";
              $qFcod .= "comidxxx = \"S\" AND ";
              $qFcod .= "comcodxx = \"999\"  AND ";
              $qFcod .= "comidcxx = \"{$xRCxC['comidxxx']}\"  AND ";
              $qFcod .= "comcodcx = \"{$xRCxC['comcodxx']}\"  AND ";
              $qFcod .= "comcsccx = \"{$xRCxC['comcscxx']}\"  AND ";
              $qFcod .= "pucidxxx = \"{$xRCxC['pucidxxx']}\"  AND ";                 
              $qFcod .= "teridxxx = \"{$xRCxC['teridxxx']}\" LIMIT 0,1 ";
              $nQueryTimeStart = microtime(true); $xFcod  = mysql_query($qFcod,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
              //f_Mensaje(__FILE__,__LINE__,$qFcod." ~ ".mysql_num_rows($xFcod));
              // echo $qFcod." ~ ".mysql_num_rows($xFcod)."<br><br>";
              if (mysql_num_rows($xFcod) > 0) {
                $vFcod = mysql_fetch_assoc($xFcod);
                $mClientes["{$xRCxC['teridxxx']}"]['carterap'] += $xRCxC['comsaldo']; //Saldo en cartera
                $mClientes["{$xRCxC['teridxxx']}"]['carterai'] += 0;
                $nAnoC = date('Y') + 1;
              }
            }
          }
          $xFree = mysql_free_result($xFcoc);
        } ## while ($xRCxC = mysql_fetch_assoc($xCxC)) { ##
        $xFree = mysql_free_result($xRCxC);
      }

      // echo "<pre>";
      // print_r($mClientes);
      // echo "</pre>";

      /*if ($pvParametros['origenxx'] == "CONTROL") {
        if ($cDocumentos != "") {
          $nSwitch = 1;
          $mReturn[count($mReturn)] = "Los Siguientes Documntos tiene Cartera Vencida:\n".$cDocumentos;
        }
      }*/

      if ($nSwitch == 0) {

        $vCatCon    = array(); //Categoria Concepto
        $vConceptos = array(); //Conceptos Contables
        $vTributos  = array(); //Conceptos Tributos

        //Categorias de los conceptos de tributos parametrizados en la varibale 
        //roldanlo_categoria_tributos_control_financiacion_clientes 
        $vConceptosTributos = array();
        if ($vSysStr['roldanlo_categoria_tributos_control_financiacion_clientes'] != "") {
          $vCacId = explode(",", $vSysStr['roldanlo_categoria_tributos_control_financiacion_clientes']);
          for($nCI=0; $nCI<count($vCacId);$nCI++){
            if (trim($vCacId[$nCI]) != "") {
              $vConceptosTributos[] = trim($vCacId[$nCI]);
            }
          }          
        }

        //Buscano conceptos de causaciones automaticas
        $qCto121  = "SELECT DISTINCT ";
        $qCto121 .= "pucidxxx, ";
        $qCto121 .= "ctoidxxx, ";
        $qCto121 .= "cacidxxx, "; //Categoria Concepto
        $qCto121 .= "\"NO\" AS ctoantxx, ";
        $qCto121 .= "\"SI\" AS ctopccxx, ";
        $qCto121 .= "\"NO\" AS ctoptaxg, "; //Tributos aduaneros en los egresos
        $qCto121 .= "\"NO\" AS ctoptaxl, "; //Tributos aduaneros en las cartas bancarias
        $qCto121 .= "\"NO\" AS fondoope  ";
        $qCto121 .= "FROM $cAlfa.fpar0121 ";
        $qCto121 .= "WHERE ";
        $qCto121 .= "regestxx = \"ACTIVO\"";
        $nQueryTimeStart = microtime(true); $xCto121  = mysql_query($qCto121,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
        //  f_Mensaje(__FILE__,__LINE__,$qCto121."~".mysql_num_rows($xCto121));
        // echo  $qCto121."~".mysql_num_rows($xCto121)."<br><br>";
        while($xRC121 = mysql_fetch_assoc($xCto121)) {
          $vConceptos["{$xRC121['ctoidxxx']}"] = $xRC121;
          $vCatCon["{$xRC121['ctoidxxx']}"] = $xRC121['cacidxxx'];

          if (in_array($xRC121['cacidxxx'],$vConceptosTributos) == true) {
            $vTributos[count($vTributos)] = "{$xRC121['ctoidxxx']}";
          }
        }

        //Cuentas de pagos a terceros
        $vPucIdPcc = explode(",",$vSysStr['roldanlo_cuentas_pcc_financiacion_clientes']);
        $cPucIdPcc = "\"".implode("\",\"", $vPucIdPcc)."\"";

        //Cuentas de anticipos operativos
        $vPucIdAnt = explode(",",$vSysStr['roldanlo_cuentas_anticipos_operativos_financiacion_clientes']);
        $cPucIdAnt = "\"".implode("\",\"", $vPucIdAnt)."\"";


        //Buscando conceptos de anticipos y pagos a terceros normales
        //Si en las condiciones de financicion Cliente NO  Aplica control de Financiación para TRIBUTOS ADUANEROS,
        //estos deben excluirse de esta busqueda
        $qCto119  = "SELECT DISTINCT ";
        $qCto119 .= "pucidxxx, ";
        $qCto119 .= "ctoidxxx, ";
        $qCto119 .= "cacidxxx, "; //Categoria Concepto
        $qCto119 .= "ctoantxx, ";
        $qCto119 .= "ctopccxx, ";
        $qCto119 .= "ctoptaxg, "; //Tributos aduaneros en los egresos
        $qCto119 .= "ctoptaxl, "; //Tributos aduaneros en las cartas bancarias
        $qCto119 .= "\"NO\" AS fondoope ";
        $qCto119 .= "FROM $cAlfa.fpar0119 ";
        $qCto119 .= "WHERE ";
        //Se traen los conceptos de las cuentas de pcc y anticipos operativos 
        $qCto119 .= "($cAlfa.fpar0119.pucidxxx IN ($cPucIdPcc) OR $cAlfa.fpar0119.pucidxxx IN ($cPucIdAnt)) AND ";
        $qCto119 .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\"";
        $nQueryTimeStart = microtime(true); $xCto119  = mysql_query($qCto119,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
        //  f_Mensaje(__FILE__,__LINE__,$qCto119."~".mysql_num_rows($xCto119));
        // echo  $qCto119."~".mysql_num_rows($xCto119)."<br><br>";
        while($xRC119 = mysql_fetch_assoc($xCto119)) {
          if (in_array($xRC119['cacidxxx'],$vConceptosTributos) == true) {
            $vTributos[count($vTributos)] = "{$xRC119['ctoidxxx']}";
          }
          $vConceptos["{$xRC119['ctoidxxx']}"] = $xRC119;
          $vCatCon["{$xRC119['ctoidxxx']}"] = $xRC119['cacidxxx'];
        }

        //Buscando cuentas de Anticipos a Proveedor y trayendo los anticipos a proveedor pendientes de pago
        if ($vSysStr['roldanlo_cuentas_anticipos_proveedor_financiacion_clientes'] != "") {
          $vPucIdAp = explode(",",$vSysStr['roldanlo_cuentas_anticipos_proveedor_financiacion_clientes']);
          $cPucIdAp = "\"".implode("\",\"", $vPucIdAp)."\"";

          //Buscando las CxP de los anticipos a proveedor sin pagar
          $qCxP  = "SELECT * ";
          $qCxP .= "FROM $cAlfa.fcxp0000 ";
          $qCxP .= "WHERE ";
          $qCxP .= "pucidxxx IN ($cPucIdAp) ";
          $nQueryTimeStart = microtime(true); $xCxP  = mysql_query($qCxP,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
          //f_Mensaje(__FILE__,__LINE__,$qCxP."~".mysql_num_rows($xCxP));
          $vAntProxCli    = array();
          $cAntProxCli    = "";
          $vSaldoAntProxCli = array();
          $nAnoIni       = date('Y');
          $nCanReg01      = 0;
          while ($xRCxP = mysql_fetch_assoc($xCxP)) {
            $nCanReg01++;
            if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01); }

            if ($xRCxP['comsaldo'] > 0) {
              //Buscando el DO en el moviento contable
              //Buscando si el registro existe en cabecera
              if ($nAnoIni > substr($xRCxP['regfcrex'],0,4)) {
                $nAnoIni = substr($xRCxP['regfcrex'],0,4);
              }
              $vSaldoAntProxCli["{$xRCxP['comidxxx']}~{$xRCxP['comcodxx']}~{$xRCxP['comcscxx']}~{$xRCxP['comseqxx']}~{$xRCxP['teridxxx']}~{$xRCxP['pucidxxx']}"] = $xRCxP['comsaldo'];
              $cAntProxCli .= "\"{$xRCxP['comidxxx']}~{$xRCxP['comcodxx']}~{$xRCxP['comcscxx']}~{$xRCxP['comseqxx']}~{$xRCxP['teridxxx']}~{$xRCxP['pucidxxx']}\", ";
            } ## if ($xRCxP['comsaldo'] > 0) { ##
          } ## while ($xRCxP = mysql_fetch_assoc($xCxP)) { ##

          $cAntProxCli = substr($cAntProxCli, 0, -2);

          $vAntProxDO = array(); //Para sumar los pagos, si estos se hicieron en años diferentes
          for ($nAno = $nAnoIni; $nAno<=date('Y'); $nAno++) {
            $qFcod  = "SELECT ";
            $qFcod .= "comidcxx,comcodcx,comcsccx,comseqcx,teridxxx,pucidxxx,sucidxxx, docidxxx, docsufxx, terid3xx, ";
            $qFcod .= "SUM(IF(commovxx=\"D\",IF(puctipej=\"L\" OR puctipej=\"\", comvlrxx, comvlrnf),IF(puctipej=\"L\" OR puctipej=\"\", comvlrxx*-1, comvlrnf*-1))) AS comvlrxx ";
            $qFcod .= "FROM $cAlfa.fcod$nAno ";
            $qFcod .= "WHERE ";
            $qFcod .= "CONCAT(comidcxx,\"~\",comcodcx,\"~\",comcsccx,\"~\",comseqcx,\"~\",teridxxx,\"~\",pucidxxx) IN ($cAntProxCli) AND ";
            $qFcod .= "pucidxxx IN ($cPucIdAp) AND ";
            $qFcod .= "regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
            $qFcod .= "GROUP BY comidcxx,comcodcx,comcsccx,comseqcx,teridxxx,pucidxxx,sucidxxx, docidxxx, docsufxx, terid3xx ";
            $nQueryTimeStart = microtime(true); $xFcod  = mysql_query($qFcod,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
            //  f_Mensaje(__FILE__,__LINE__,$qFcod." ~ ".mysql_num_rows($xFcod));
            // echo $qFcod." ~ ".mysql_num_rows($xFcod)."<br><br>";
            while ($xRF = mysql_fetch_assoc($xFcod)) {
              if ($xRF['sucidxxx'] != "" && $xRF['docidxxx'] != "" && $xRF['docsufxx'] != "") {
                $vAntProxDO["{$xRF['comidcxx']}~{$xRF['comcodcx']}~{$xRF['comcsccx']}~{$xRF['comseqcx']}~{$xRF['teridxxx']}~{$xRF['pucidxxx']}~{$xRF['sucidxxx']}~{$xRF['docidxxx']}~{$xRF['docsufxx']}"] += $xRF['comvlrxx'];
              } else {
                //Solo se incluyen los registros de los clientes que se estan buscando
                $nIncluir = 0;
                if (count($pvParametros['clientes']) > 0) {
                  if (in_array("{$xRF['terid3xx']}", $pvParametros['clientes']) == false) {
                    $nIncluir = 1;
                  }
                }
                if ($xRF['terid3xx'] != "" && $nIncluir == 0) {
                  $vAntProxCli["{$xRF['terid3xx']}"] += $xRF['comvlrxx'];
                }
              }
            } ## while ($xRF = mysql_fetch_assoc($xFcod)) { ##
          } ## for ($nAnoIni = 0; $nAno<=date('Y'); $nAno++) { ##

          // echo "<pre>";
          // print_r($vAntProxDO);
          // echo "</pre>";

          foreach ($vAntProxDO as $ckeyDO => $cValueDO) {
            //El key viene armado asi:
            //[0] comidcxx
            //[1] comcodcx
            //[2] comcsccx
            //[3] comseqcx
            //[4] teridxxx
            //[5] pucidxxx
            //[6] sucidxxx
            //[7] docidxxx
            //[8] docsufxx
            $vDatosCom = explode("~", $ckeyDO);

            //Buscando el cliente del DO
            $qTramite = "SELECT ";
            $qTramite.= "cliidxxx ";
            $qTramite.= "FROM $cAlfa.sys00121 ";
            $qTramite.= "WHERE ";
            $qTramite.= "sucidxxx = \"{$vDatosCom[6]}\" AND ";
            $qTramite.= "docidxxx = \"{$vDatosCom[7]}\" AND ";
            $qTramite.= "docsufxx = \"{$vDatosCom[8]}\" LIMIT 0,1 ";
            $nQueryTimeStart = microtime(true); $xTramite  = mysql_query($qTramite,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
            //  f_Mensaje(__FILE__,__LINE__,$qTramite."~".mysql_num_rows($xTramite));
            $nCanReg01 = 0;
            if (mysql_num_rows($xTramite) > 0) {
              $nCanReg01++;
              if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01); }

                $vTramite = mysql_fetch_assoc($xTramite);

                if (($vSaldoAntProxCli["{$vDatosCom[0]}~{$vDatosCom[1]}~{$vDatosCom[2]}~{$vDatosCom[3]}~{$vDatosCom[4]}~{$vDatosCom[5]}"]+0) >= ($vAntProxDO[$ckeyDO]+0)) {
                  //Solo se incluyen los registros de los clientes que se estan buscando
                  $nIncluir = 0;
                  if (count($pvParametros['clientes']) > 0) {
                    if (in_array("{$vTramite['cliidxxx']}", $pvParametros['clientes']) == false) {
                      $nIncluir = 1;
                    }
                  }

                  if ($nIncluir == 0) {
                    $vAntProxCli["{$vTramite['cliidxxx']}"] += $vAntProxDO[$ckeyDO];
                    $vSaldoAntProxCli["{$vDatosCom[0]}~{$vDatosCom[1]}~{$vDatosCom[2]}~{$vDatosCom[3]}~{$vDatosCom[4]}~{$vDatosCom[5]}"] -= $vAntProxDO[$ckeyDO];
                    //Tramites
                    // f_Mensaje(__FILE__,__LINE__,"{$vDatosCom[6]}~{$vDatosCom[7]}~{$vDatosCom[8]}");
                    if (in_array("{$vDatosCom[6]}~{$vDatosCom[7]}~{$vDatosCom[8]}", $mClientes["{$vTramite['cliidxxx']}"]['tramites']) == false){
                      $mClientes["{$vTramite['cliidxxx']}"]['tramites'][] = "{$vDatosCom[6]}~{$vDatosCom[7]}~{$vDatosCom[8]}";
                    }
                  }
                } else {
                  //Solo se incluyen los registros de los clientes que se estan buscando
                  $nIncluir = 0;
                  if (count($pvParametros['clientes']) > 0) {
                    if (in_array("{$vTramite['cliidxxx']}", $pvParametros['clientes']) == false) {
                      $nIncluir = 1;
                    }
                  }

                  if ($nIncluir == 0) {
                    $vAntProxCli["{$vTramite['cliidxxx']}"] += $vSaldoAntProxCli["{$vDatosCom[0]}~{$vDatosCom[1]}~{$vDatosCom[2]}~{$vDatosCom[3]}~{$vDatosCom[4]}~{$vDatosCom[5]}"];
                    $vSaldoAntProxCli["{$vDatosCom[0]}~{$vDatosCom[1]}~{$vDatosCom[2]}~{$vDatosCom[3]}~{$vDatosCom[4]}~{$vDatosCom[5]}"] = 0;
                    //Tramites
                    // f_Mensaje(__FILE__,__LINE__,"{$vDatosCom[6]}~{$vDatosCom[7]}~{$vDatosCom[8]}");
                    if (in_array("{$vDatosCom[6]}~{$vDatosCom[7]}~{$vDatosCom[8]}", $mClientes["{$vTramite['cliidxxx']}"]['tramites']) == false){
                    $mClientes["{$vTramite['cliidxxx']}"]['tramites'][] = "{$vDatosCom[6]}~{$vDatosCom[7]}~{$vDatosCom[8]}";
                    }
                  }
                }
              }
            } ## foreach ($vAntProxDO as $ckeyDO => $cValueDO) { ##
          }

          //  $tFin = microtime(true);
          //  echo "Tiempo de ejecucion: ".bcsub($tFin, $tInicio, 4)."<br>";

          //Array para el control de las financiaciones manuales
          $vConCau = array();
          $cConCau = "";

          $vPCC = array(); //Pagos a terceros

          //Buscando todos los pagos a terceros y anticipos no facturados del cliente
          //Inicializando vector de pagos a terceros x Concepto
          //vecto de pagos a terceros por do y concepto
          $vPCCxCto = array();

          //Vector anticipos por do
          $vAntxDO = array();

          //Valor de los concepto de tributos que se deben cubrir los anticipos
          $nTotalTributos = 0;

          //Cuentas de pagos a terceros
          $vPucIdPcc = explode(",",$vSysStr['roldanlo_cuentas_pcc_financiacion_clientes']);
          $cPucIdPcc = "\"".implode("\",\"", $vPucIdPcc)."\"";

          //Cuentas de anticipos operativos
          $vPucIdAnt = explode(",",$vSysStr['roldanlo_cuentas_anticipos_operativos_financiacion_clientes']);
          $cPucIdAnt = "\"".implode("\",\"", $vPucIdAnt)."\"";

          //Cuentas de fondos operativos
          $vPucIdFon = explode(",",$vSysStr['roldanlo_cuentas_fondos_operativos_financiacion_clientes']);
          $cPucIdFon = "\"".implode("\",\"", $vPucIdFon)."\"";

          //Se busca desde el año anterior a la creacion del DO, hasta el año actual
          for ($iAno = $vSysStr['financiero_ano_instalacion_modulo']; $iAno<=date('Y'); $iAno++) {

            $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01);

            //Buscando pagos a terceros, aplica la fecha de fincaciación
            //Debito suma, credito resta
            $qMovCon  = "SELECT DISTINCT ";
            $qMovCon .= "teridxxx, regfcrex, reghcrex, pucidxxx, ctoidxxx, sucidxxx, docidxxx, docsufxx, comfacxx, ";
            $qMovCon .= "SUM(IF(commovxx=\"D\",IF(puctipej=\"L\" OR puctipej=\"\", comvlrxx, comvlrnf),IF(puctipej=\"L\" OR puctipej=\"\", comvlrxx*-1, comvlrnf*-1))) AS comvlrxx ";
            $qMovCon .= "FROM $cAlfa.fcod$iAno ";
            $qMovCon .= "WHERE ";
            if ($cCliId != "") {
              $qMovCon .= "teridxxx IN ($cCliId) AND ";
            }
            $qMovCon .= "comidxxx != \"F\" AND ";
            $qMovCon .= "(comfacxx = \"\" OR comfacxx LIKE \"%-P%\") AND ";
            $qMovCon .= "pucidxxx IN ($cPucIdPcc) AND ";
            $qMovCon .= "regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
            $qMovCon .= "GROUP BY teridxxx, regfcrex, reghcrex, pucidxxx, ctoidxxx, sucidxxx, docidxxx, docsufxx, comfacxx ";
            $qMovCon .= "ORDER BY ABS(regfcrex) ASC ";
            $nQueryTimeStart = microtime(true); $xMovCon  = mysql_query($qMovCon,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
            // if (mysql_num_rows($xMovCon) > 0){
              // echo "<br>PCC:<br>".mysql_num_rows($xMovCon)."~".$qMovCon."<br>";
            // }
            while ($xRMC = mysql_fetch_assoc($xMovCon)) {
              $nIncluir = 0;

              //Si el campo comfacxx es diferente de vacio se debe verificar que efectivamente corresponda a una proforma
              //Los pagos a terceros facturados como proforma deben tenerse en cuenta
              if ($xRMC['comfacxx'] != "") {
                $vComfac = explode("-", $xRMC['comfacxx']);
                if (substr($vComfac[2], 0, 1) != "P") {
                  $nIncluir = 1;
                }
              }

              if ($nIncluir == 0) {
                //Acumulando pagos a terceros x concepto, para buscar la financiacion manual y descontarla
                $vPCCxCto["{$xRMC['teridxxx']}"]["{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}~{$xRMC['ctoidxxx']}"]['totalxxx']  += $xRMC['comvlrxx'];
                $vPCCxCto["{$xRMC['teridxxx']}"]["{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}~{$xRMC['ctoidxxx']}"]['controlx']  += $xRMC['comvlrxx'];
                $vPCCxCto["{$xRMC['teridxxx']}"]["{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}~{$xRMC['ctoidxxx']}"]['anticipo']   = 0;
                $vPCCxCto["{$xRMC['teridxxx']}"]["{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}~{$xRMC['ctoidxxx']}"]['finmanxx']   = 0;
                $vPCCxCto["{$xRMC['teridxxx']}"]["{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}~{$xRMC['ctoidxxx']}"]['contribu']   = "NO";
                $vPCCxCto["{$xRMC['teridxxx']}"]["{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}~{$xRMC['ctoidxxx']}"]['ctoidxxx']   = "{$xRMC['ctoidxxx']}";

                if ($mClientes["{$xRMC['teridxxx']}"]['fpcatria'] != "SI") {
                  //Marcando si el concepto debe ser cubierto por los antipos
                  if (in_array("{$xRMC['ctoidxxx']}", $vTributos) == true) {
                    $vPCCxCto["{$xRMC['teridxxx']}"]["{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}~".$vCatCon["{$xRMC['ctoidxxx']}"]]['contribu'] = "SI";
                  }
                }                  
                
                //Tramites
                // f_Mensaje(__FILE__,__LINE__,"{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}");
                if (in_array("{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}", $mClientes["{$xRMC['teridxxx']}"]['tramites']) == false){
                  $mClientes["{$xRMC['teridxxx']}"]['tramites'][] = "{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}";
                }
              }
            }

            //Buscando anticipos operativos
            //Credito suma, debito resta
            $qMovCon  = "SELECT DISTINCT ";
            $qMovCon .= "teridxxx, terid2xx, pucidxxx, ctoidxxx, sucidxxx, docidxxx, docsufxx, comfacxx, ";
            $qMovCon .= "SUM(IF(commovxx=\"C\",IF(puctipej=\"L\" OR puctipej=\"\", comvlrxx, comvlrnf),IF(puctipej=\"L\" OR puctipej=\"\", comvlrxx*-1, comvlrnf*-1))) AS comvlrxx ";
            $qMovCon .= "FROM $cAlfa.fcod$iAno ";
            $qMovCon .= "WHERE ";
            if ($cCliId != "") {
              $qMovCon .= "(teridxxx IN ($cCliId) OR terid2xx IN ($cCliId)) AND ";
            }
            $qMovCon .= "comidxxx != \"F\" AND ";
            $qMovCon .= "(comfacxx = \"\" OR comfacxx LIKE \"%-P%\") AND ";
            $qMovCon .= "pucidxxx IN ($cPucIdAnt) AND ";
            $qMovCon .= "regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
            $qMovCon .= "GROUP BY teridxxx, terid2xx, pucidxxx, ctoidxxx, sucidxxx, docidxxx, docsufxx, comfacxx ";
            $nQueryTimeStart = microtime(true); $xMovCon  = mysql_query($qMovCon,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
            // if ($kUser == "ADMIN") {
            //   echo "<br>Anticipo: <br>".mysql_num_rows($xMovCon)."~".$qMovCon."<br>";
            // }
            while ($xRMC = mysql_fetch_assoc($xMovCon)) {
              $nIncluir = 0;

              //Si el campo comfacxx es diferente de vacio se debe verificar que efectivamente corresponda a una proforma
              //Los pagos a terceros facturados como proforma deben tenerse en cuenta
              if ($xRMC['comfacxx'] != "") {
                $vComfac = explode("-", $xRMC['comfacxx']);
                if (substr($vComfac[2], 0, 1) != "P") {
                  $nIncluir = 1;
                }
              }

              if ($nIncluir == 0) {
                //Si el teridxxx y el terid2xx son diferentes, el anticipo fue realizado por un tercero diferente al dueño del DO
                //y debe cargarse es al dueño del DO
                $cTerId = ($xRMC['teridxxx'] != $xRMC['terid2xx']) ? $xRMC['terid2xx'] : $xRMC['teridxxx'];

                //Anticipos x DO
                $vAntxDO["$cTerId"]["{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}"] += $xRMC['comvlrxx'];
                //Tramites
                // f_Mensaje(__FILE__,__LINE__,"{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}");
                if (in_array("{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}", $mClientes["$cTerId"]['tramites']) == false){
                  $mClientes["$cTerId"]['tramites'][] = "{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}";
                }
              }
            }

            //Buscando fondos operativos
            //Credito suma, debito resta
            $qMovCon  = "SELECT DISTINCT ";
            $qMovCon .= "teridxxx, pucidxxx, ctoidxxx, sucidxxx, docidxxx, docsufxx, comfacxx, ";
            $qMovCon .= "SUM(IF(commovxx=\"C\",IF(puctipej=\"L\" OR puctipej=\"\", comvlrxx, comvlrnf),IF(puctipej=\"L\" OR puctipej=\"\", comvlrxx*-1, comvlrnf*-1))) AS comvlrxx ";
            $qMovCon .= "FROM $cAlfa.fcod$iAno ";
            $qMovCon .= "WHERE ";
            if ($cCliId != "") {
              $qMovCon .= "teridxxx IN ($cCliId) AND ";
            }
            $qMovCon .= "comidxxx != \"F\" AND ";
            $qMovCon .= "(comfacxx = \"\" OR comfacxx LIKE \"%-P%\") AND ";
            $qMovCon .= "pucidxxx IN ($cPucIdFon) AND ";
            $qMovCon .= "regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
            $qMovCon .= "GROUP BY teridxxx, pucidxxx, ctoidxxx, sucidxxx, docidxxx, docsufxx, comfacxx ";
            $nQueryTimeStart = microtime(true); $xMovCon  = mysql_query($qMovCon,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
            //  if (mysql_num_rows($xMovCon) > 0) {
              // echo "<br>Fondos operativos: <br>".mysql_num_rows($xMovCon)."~".$qMovCon."<br>";
            //  }
            while ($xRMC = mysql_fetch_assoc($xMovCon)) {
              $nIncluir = 0;

              //Si el campo comfacxx es diferente de vacio se debe verificar que efectivamente corresponda a una proforma
              //Los pagos a terceros facturados como proforma deben tenerse en cuenta
              if ($xRMC['comfacxx'] != "") {
                $vComfac = explode("-", $xRMC['comfacxx']);
                if (substr($vComfac[2], 0, 1) != "P") {
                  $nIncluir = 1;
                }
              }

              if ($nIncluir == 0) {
                //Fondos operativos
                $mClientes["{$xRMC['teridxxx']}"]['fondoope'] += $xRMC['comvlrxx'];
              }
            }
          } //for ($iAno = $vDatos[$i]['iniciomc']; $iAno<=date('Y'); $iAno++) {

          //Incluyendo los pagos a terceros que se quieren causar
          if ( $pvParametros['origenxx'] == "CONTROL") {
            $dFechaActual = date('Y-m-d~H:i:s');
            for ($i=0; $i<count($pvParametros['tramites']); $i++) {

              //Si es un concepto cuya cuenta es de pagos a terceros
              if (in_array($vConceptos["{$pvParametros['tramites'][$i]['ctoidxxx']}"]['pucidxxx'], $vPucIdPcc) == true) {
                
                //Tramites que se estan causando
                $cTramite = "{$pvParametros['tramites'][$i]['sucidxxx']}~{$pvParametros['tramites'][$i]['docidxxx']}~{$pvParametros['tramites'][$i]['docsufxx']}";
                if (in_array($cTramite, $vTramCausar) == false) {
                  $vTramCausar[count($vTramCausar)] = $cTramite;
                  $mClientes["{$pvParametros['tramites'][$i]['cliidxxx']}"]['tramites'][] = $cTramite;
                }

                //Se le adiciona la palabra SI para indicar que es un pago nuevo y no los sume con los pagos ya causados
                $cDoCto = "{$pvParametros['tramites'][$i]['sucidxxx']}~{$pvParametros['tramites'][$i]['docidxxx']}~{$pvParametros['tramites'][$i]['docsufxx']}~{$pvParametros['tramites'][$i]['ctoidxxx']}~SI";
                $vPCCxCto["{$pvParametros['tramites'][$i]['cliidxxx']}"][$cDoCto]['totalxxx']  += $pvParametros['tramites'][$i]['comvlrxx'];
                $vPCCxCto["{$pvParametros['tramites'][$i]['cliidxxx']}"][$cDoCto]['controlx']  += $pvParametros['tramites'][$i]['comvlrxx'];
                $vPCCxCto["{$pvParametros['tramites'][$i]['cliidxxx']}"][$cDoCto]['anticipo']   = 0;
                $vPCCxCto["{$pvParametros['tramites'][$i]['cliidxxx']}"][$cDoCto]['finmanxx']   = 0;
                $vPCCxCto["{$pvParametros['tramites'][$i]['cliidxxx']}"][$cDoCto]['contribu']   = "NO";
                $vPCCxCto["{$pvParametros['tramites'][$i]['cliidxxx']}"][$cDoCto]['nuevopcc']   = "SI";
                $vPCCxCto["{$pvParametros['tramites'][$i]['cliidxxx']}"][$cDoCto]['ctoidxxx']   = "{$pvParametros['tramites'][$i]['ctoidxxx']}";

                if ($mClientes["{$pvParametros['tramites'][$i]['cliidxxx']}"]['fpcatria'] != "SI") {
                  //Aplica control de cupos sobre tributos si los anticipos del DO cubren el valor del pago
                  //No se tiene en cuenta en la logica normal
                  if (in_array("{$pvParametros['tramites'][$i]['ctoidxxx']}", $vTributos) == true) {
                    $vPCCxCto["{$pvParametros['tramites'][$i]['cliidxxx']}"][$cDoCto]['contribu'] = "SI";
                  }
                }
                
                $nTotalCausar += $pvParametros['tramites'][$i]['comvlrxx'];

                //Tramites
                if (in_array($vCatCon["{$pvParametros['tramites'][$i]['ctoidxxx']}"], $mClientes["{$pvParametros['tramites'][$i]['cliidxxx']}"]['catidxxx']) == false) {
                  $mClientes["{$pvParametros['tramites'][$i]['cliidxxx']}"]['catidxxx'][] = $vCatCon["{$pvParametros['tramites'][$i]['ctoidxxx']}"];
                }
              }
            }
          }

          // echo "<pre>";
          // print_r($vAntxDO);
          // echo "</pre>";

          // echo "<pre>";
          // print_r($vPCCxCto);
          // echo "</pre>";

          foreach ($mClientes as $cKey => $cValue) {
            //Anticipos a Proveedor Cliente
            $mClientes[$cKey]['antprove'] += $vAntProxCli[$cKey];

            //Los pagos a terceros estan agrupados por cliente y fecha
            //Ordenando por fecha de creacion de los pagos a terceros para asigar anticipo
            //del pago mas antiguo al mas reciente
            ksort($vPCCxCto[$cKey]);

            if ( $pvParametros['origenxx'] == "CONTROL") {
              //Totalizando los anticipos, solo se suma el valor que cubre los pagos a terceros del DO
              //Si hay sobrantes, estos no se suman, porque si se hiciera estarian favoreciendo el cupo de un
              //DO que no tiene anticipos
              //Se parte de los pagos a terceros porque estos estan discriminados por concepto y es necesario
              //decontar el anticipo por cada concepto, para facilitar luego la asignación de cupo manual
              $nAnt = 0;

              //Se analizan primero los ya causados
              foreach ($vPCCxCto[$cKey] as $cKeyPCC => $cValuePcc) {
                if ($vPCCxCto[$cKey][$cKeyPCC]['nuevopcc'] != "SI") {
                  //Extrayendo el key para los anticpos, que es sucursal~do~sufijo
                  $cKeyAux = explode("~", $cKeyPCC);
                  $cKeyAnt = $cKeyAux[0]."~".$cKeyAux[1]."~".$cKeyAux[2];

                  //Si la sumatoria de los pagos a terceros esta dando menor a cero
                  //Se asume que son ajustes a pagos a terceros anteriores a la fecha de inicio de contro de cupo
                  //Por lo que se lleva el valor de los pagos a terceros a cero
                  $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'] = ($vPCCxCto[$cKey][$cKeyPCC]['totalxxx'] > 0) ? $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'] : 0;
                  
                  //pagos a terceros del cliente
                  $mClientes[$cKey]['pagoster'] += $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];

                  //Calculando Anitcipo utilizado para cada pago a tercero
                  if (($vAntxDO[$cKey][$cKeyAnt]+0) > 0) {
                    if (($vAntxDO[$cKey][$cKeyAnt]+0) >= ($vPCCxCto[$cKey][$cKeyPCC]['totalxxx']+0)) {
                      //El anticipo cubre el pago a tercero
                      $nAnt += $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];
                      //Resto al anticipo el pago a tercero
                      $vAntxDO[$cKey][$cKeyAnt] -= $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];
                      //En el vector de control llevo a cero el pago, porque fue cobijado por el anticipo
                      $vPCCxCto[$cKey][$cKeyPCC]['anticipo'] = $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];
                      $vPCCxCto[$cKey][$cKeyPCC]['controlx'] = 0;
                    } else {
                      //El anticipo cubre parte de pago al tercero
                      $nAnt += $vAntxDO[$cKey][$cKeyAnt];
                      //Descuento el valor del anticipo al pago al tercero, y dejo lo que no alcanzo a cubrir
                      $vPCCxCto[$cKey][$cKeyPCC]['controlx'] -= $vAntxDO[$cKey][$cKeyAnt];
                      //El anticipo queda en cero
                      $vPCCxCto[$cKey][$cKeyPCC]['anticipo'] = $vAntxDO[$cKey][$cKeyAnt];
                      $vAntxDO[$cKey][$cKeyAnt] = 0;
                    }
                  }
                }
              }

              //se analizan los que se quieren causar
              foreach ($vPCCxCto[$cKey] as $cKeyPCC => $cValuePcc) {
                if ($vPCCxCto[$cKey][$cKeyPCC]['nuevopcc'] == "SI") {
                  //Extrayendo el key para los anticpos, que es sucursal~do~sufijo
                  $cKeyAux = explode("~", $cKeyPCC);
                  $cKeyAnt = $cKeyAux[0]."~".$cKeyAux[1]."~".$cKeyAux[2];

                  //Si la sumatoria de los pagos a terceros esta dando menor a cero
                  //Se asume que son ajustes a pagos a terceros anteriores a la fecha de inicio de contro de cupo
                  //Por lo que se lleva el valor de los pagos a terceros a cero
                  $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'] = ($vPCCxCto[$cKey][$cKeyPCC]['totalxxx'] > 0) ? $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'] : 0;
                  
                  //pagos a terceros del cliente
                  $mClientes[$cKey]['pagoster'] += $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];

                  //Calculando Anitcipo utilizado para cada pago a tercero
                  if (($vAntxDO[$cKey][$cKeyAnt]+0) > 0) {
                    if (($vAntxDO[$cKey][$cKeyAnt]+0) >= ($vPCCxCto[$cKey][$cKeyPCC]['totalxxx']+0)) {
                      //El anticipo cubre el pago a tercero
                      $nAnt += $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];
                      //Resto al anticipo el pago a tercero
                      $vAntxDO[$cKey][$cKeyAnt] -= $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];
                      //En el vector de control llevo a cero el pago, porque fue cobijado por el anticipo
                      $vPCCxCto[$cKey][$cKeyPCC]['anticipo'] = $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];
                      $vPCCxCto[$cKey][$cKeyPCC]['controlx'] = 0;
                    } else {
                      //El anticipo cubre parte de pago al tercero
                      $nAnt += $vAntxDO[$cKey][$cKeyAnt];
                      //Descuento el valor del anticipo al pago al tercero, y dejo lo que no alcanzo a cubrir
                      $vPCCxCto[$cKey][$cKeyPCC]['controlx'] -= $vAntxDO[$cKey][$cKeyAnt];
                      //El anticipo queda en cero
                      $vPCCxCto[$cKey][$cKeyPCC]['anticipo'] = $vAntxDO[$cKey][$cKeyAnt];
                      $vAntxDO[$cKey][$cKeyAnt] = 0;
                    }
                  }
                }
              }

              $mClientes[$cKey]['anticipo'] += $nAnt;
            } else {
              //para el reporte y el saldo se envia el total de los anticipos
              foreach ($vAntxDO[$cKey] as $cKeyAnt => $cValueAnt) {
                $mClientes[$cKey]['anticipo'] += $vAntxDO[$cKey][$cKeyAnt];
              }
              //Llevando el valor de los pagos a terceros
              foreach ($vPCCxCto[$cKey] as $cKeyPCC => $cValuePcc) {
                  //Si la sumatoria de los pagos a terceros esta dando menor a cero
                  //Se asume que son ajustes a pagos a terceros anteriores a la fecha de inicio de contro de cupo
                  //Por lo que se lleva el valor de los pagos a terceros a cero
                  $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'] = ($vPCCxCto[$cKey][$cKeyPCC]['totalxxx'] > 0) ? $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'] : 0;

                  $mClientes[$cKey]['pagoster'] += $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];
              }              
            }
          } ## foreach ($mClientes as $cKey => $cValue) { ##


          // echo "<pre>";
          // print_r($vAntxDO);
          // echo "</pre>";

          // echo "<pre>";
          // print_r($vPCCxCto);
          // echo "</pre>";

          // echo "<pre>";
          // print_r($mClientes);
          // echo "</pre>";

          //Buscando el cupo de financiacion manual de los tramites utilizados en el proceso
          foreach ($mClientes as $cKey => $cValue) {
            $cTramitesFM = "";
            for($i=0; $i<count($mClientes[$cKey]['tramites']); $i++) {
              $cTramitesFM .= "\"{$mClientes[$cKey]['tramites'][$i]}\", ";
            }
            $cTramitesFM = substr($cTramitesFM, 0, -2);

            //Buscando Financiacion Manual por DO y Concepto
            $qFinMan  = "SELECT ";
            $qFinMan .= "$cAlfa.sys00121.cliidxxx AS cliidxxx, ";
            $qFinMan .= "$cAlfa.fpar0148.sucidxxx, ";
            $qFinMan .= "$cAlfa.fpar0148.docidxxx, ";
            $qFinMan .= "$cAlfa.fpar0148.docsufxx, ";
            $qFinMan .= "$cAlfa.fpar0148.cacidxxx, ";
            $qFinMan .= "$cAlfa.fpar0148.regfcrex, ";
            $qFinMan .= "$cAlfa.fpar0148.reghcrex, ";
            $qFinMan .= "SUM($cAlfa.fpar0148.sfmvalxx) AS sfmvalxx ";
            $qFinMan .= "FROM $cAlfa.fpar0148 ";
            $qFinMan .= "LEFT JOIN $cAlfa.sys00121 ON ";
            $qFinMan .= "$cAlfa.sys00121.sucidxxx = $cAlfa.fpar0148.sucidxxx AND ";
            $qFinMan .= "$cAlfa.sys00121.docidxxx = $cAlfa.fpar0148.docidxxx AND ";
            $qFinMan .= "$cAlfa.sys00121.docsufxx = $cAlfa.fpar0148.docsufxx ";
            $qFinMan .= "WHERE ";
            switch ($pvParametros['origenxx']) {
              case "REPORTE":
              case "SALDO":
                //no hace nada, se buscan todos los tramites que tengan financiacion manual aprobada para el cliente
                if ($cCliId != "") {
                  $qFinMan .= "$cAlfa.sys00121.cliidxxx = \"$cKey\" AND ";
                }
              break;
              default:
                $qFinMan .= "CONCAT($cAlfa.fpar0148.sucidxxx,\"~\",$cAlfa.fpar0148.docidxxx,\"~\",$cAlfa.fpar0148.docsufxx) IN ($cTramitesFM)AND ";
            }
            $qFinMan .= "$cAlfa.fpar0148.sfmestxx = \"APROBADO\" AND ";
            $qFinMan .= "$cAlfa.fpar0148.regestxx = \"ACTIVO\" ";
            $qFinMan .= "GROUP BY $cAlfa.fpar0148.sucidxxx,$cAlfa.fpar0148.docidxxx,$cAlfa.fpar0148.docsufxx,$cAlfa.fpar0148.cacidxxx,$cAlfa.fpar0148.regfcrex,$cAlfa.fpar0148.reghcrex ";

            $nQueryTimeStart = microtime(true); $xFinMan  = mysql_query($qFinMan,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
            // f_Mensaje(__FILE__,__LINE__,$qFinMan."~".mysql_num_rows($xFinMan));
            // echo $qFinMan."~".mysql_num_rows($xFinMan)."<br><br>";
            while ($xRFM = mysql_fetch_assoc($xFinMan)) {
              $Ind_mFinManual = count($mFinManual);
              $mFinManual[$Ind_mFinManual]['cliidxxx']  = $xRFM['cliidxxx'];
              $mFinManual[$Ind_mFinManual]['sucidxxx']  = $xRFM['sucidxxx'];
              $mFinManual[$Ind_mFinManual]['docidxxx']  = $xRFM['docidxxx'];
              $mFinManual[$Ind_mFinManual]['docsufxx']  = $xRFM['docsufxx'];
              $mFinManual[$Ind_mFinManual]['cacidxxx']  = $xRFM['cacidxxx'];
              $mFinManual[$Ind_mFinManual]['regfcrex']  = $xRFM['regfcrex'];
              $mFinManual[$Ind_mFinManual]['reghcrex']  = $xRFM['reghcrex'];
              $mFinManual[$Ind_mFinManual]['inicialx'] += $xRFM['sfmvalxx'];
              $mFinManual[$Ind_mFinManual]['saldoxxx'] += $xRFM['sfmvalxx'];
            }
          }

          if ( $pvParametros['origenxx'] == "CONTROL") {

            //Para los pagos ya causados, se verifica si se asigno financiacion manual para desconatarla
            foreach ($mClientes as  $cKey => $cValue) {
              $nFinMan = 0; //Valor Financiacion Manual Para el cliente
              
              foreach ($vPCCxCto[$cKey] as $cKeyPCC => $cValuePCC) {
                //Si el pago a terceros aun tiene valor y es un pago ya guardado en el sistema
                if (($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0) > 0 && $vPCCxCto[$cKey][$cKeyPCC]['nuevopcc'] != "SI") {
                  $vDatosDo = explode("~",$cKeyPCC);
                  $vDatosDo[3] = $vCatCon["{$vDatosDo[3]}"]; //Se trae la categoria papa del concepto
                  for ($i=0; $i<count($mFinManual); $i++) {
                    // $cTexto  = $mFinManual[$i]['sucidxxx']." == ".$vDatosDo[0]." && ";
                    // $cTexto .= $mFinManual[$i]['docidxxx']." == ".$vDatosDo[1]." && ";
                    // $cTexto .= $mFinManual[$i]['docsufxx']." == ".$vDatosDo[2]." && ";
                    // $cTexto .= $mFinManual[$i]['cacidxxx']." == ".$vDatosDo[3]."<br><br>";
                    // echo $cTexto;
                    if ($mFinManual[$i]['sucidxxx'] == $vDatosDo[0] &&
                        $mFinManual[$i]['docidxxx'] == $vDatosDo[1] &&
                        $mFinManual[$i]['docsufxx'] == $vDatosDo[2] &&
                        $mFinManual[$i]['cacidxxx'] == $vDatosDo[3]) {
                      //La Financiacion Manual Afecto el Pago
                      if (($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0) >= ($mFinManual[$i]['saldoxxx']+0)) {
                        // echo "Mayor: ".$cKeyPCC."~Financia: ".($mFinManual[$i]['saldoxxx']+0)."~Pago:".($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0)."<br>";
                        $nFinMan += $mFinManual[$i]['saldoxxx'];
                        $vPCCxCto[$cKey][$cKeyPCC]['finmanxx'] = $mFinManual[$i]['saldoxxx'];
                        $mFinManual[$i]['saldoxxx'] = 0;
                      } else {
                        // echo "Menor".$cKeyPCC."~Financia: ".($mFinManual[$cKeyPCC]['saldoxxx']+0)."~Pago:".($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0)."<br>";
                        $nFinMan += ($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0);
                        $vPCCxCto[$cKey][$cKeyPCC]['finmanxx'] = ($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0);
                        $mFinManual[$i]['saldoxxx'] -= ($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0);
                      }                      
                    }
                  } ## for ($i=0; $i<count($mFinManual); $i++) { ##
                } ## if (($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0) > 0 && $vPCCxCto[$cKey][$cKeyPCC]['nuevopcc'] != "SI") { ##
              } ## foreach ($vPCCxCto[$cKey] as $cKeyPCC => $cValuePCC) { ##
              
              $mClientes[$cKey]['finmanxx'] += $nFinMan;
            } ## foreach ($mClientes as  $cKey => $cValue) { ##
              
            //Para los pagos que se van a causar, se verifica si se asigno financiacion manual para desconatarla
            foreach ($mClientes as  $cKey => $cValue) {
              $nFinMan = 0; //Valor Financiacion Manual Para el cliente
              foreach ($vPCCxCto[$cKey] as $cKeyPCC => $cValuePCC) {
                //Si el pago a terceros aun tiene valor y es un pago que no se ha guardado en el sistema
                if (($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0) > 0 && $vPCCxCto[$cKey][$cKeyPCC]['nuevopcc'] == "SI") {
                  $vDatosDo = explode("~",$cKeyPCC);
                  $vDatosDo[3] = $vCatCon["{$vDatosDo[3]}"]; //Se trae la categoria papa del concepto
                  for ($i=0; $i<count($mFinManual); $i++) {
                    $cTexto  = $mFinManual[$i]['sucidxxx']." == ".$vDatosDo[0]." && <br>";
                    // $cTexto .= $mFinManual[$i]['docidxxx']." == ".$vDatosDo[1]." && <br>";
                    // $cTexto .= $mFinManual[$i]['docsufxx']." == ".$vDatosDo[2]." && <br>";
                    // $cTexto .= $mFinManual[$i]['cacidxxx']." == ".$vDatosDo[3]."<br><br><br>";
                    // echo $cTexto;
                    if ($mFinManual[$i]['sucidxxx'] == $vDatosDo[0] &&
                        $mFinManual[$i]['docidxxx'] == $vDatosDo[1] &&
                        $mFinManual[$i]['docsufxx'] == $vDatosDo[2] &&
                        $mFinManual[$i]['cacidxxx'] == $vDatosDo[3]) {
                      //La Financiacion Manual Afecto el Pago
                      // echo ($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0)."~".($mFinManual[$i]['saldoxxx']+0)."<br>";
                      if (($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0) >= ($mFinManual[$i]['saldoxxx']+0)) {
                        //  echo "Mayor: ".$cKeyPCC."~Financia: ".($mFinManual[$i]['saldoxxx']+0)."~Pago:".($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0)."<br>";
                        $nFinMan += $mFinManual[$i]['saldoxxx'];
                        $vPCCxCto[$cKey][$cKeyPCC]['finmanxx'] = $mFinManual[$i]['saldoxxx'];
                        $mFinManual[$i]['saldoxxx'] = 0;                          
                      } else {
                        //  echo "Menor".$cKeyPCC."~Financia: ".($mFinManual[$cKeyPCC]['saldoxxx']+0)."~Pago:".($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0)."<br>";
                        $nFinMan += ($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0);
                        $vPCCxCto[$cKey][$cKeyPCC]['finmanxx'] = ($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0);
                        $mFinManual[$i]['saldoxxx'] -= ($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0);
                      }
                    }
                  } ## for ($i=0; $i<count($mFinManual); $i++) { ##
                } ## if (($vPCCxCto[$cKey][$cKeyPCC]['controlx']+0) > 0 && $vPCCxCto[$cKey][$cKeyPCC]['nuevopcc'] == "SI") { ##
              } ## foreach ($vPCCxCto[$cKey] as $cKeyPCC => $cValuePCC) {
              $mClientes[$cKey]['finmanxx'] += $nFinMan;
            } ## foreach ($mClientes as  $cKey => $cValue) { ##
          } ## if ( $pvParametros['origenxx'] == "CONTROL") { ##

          // echo "<pre>";
          // print_r($vPCCxCto);
          // echo "</pre>";

          if ( $pvParametros['origenxx'] == "REPORTE" || $pvParametros['origenxx'] == "SALDO") {
            //Si no se ha usado debe mostrarse en el repote
            for ($n=0; $n<count($mFinManual); $n++) {
              $mClientes[$mFinManual[$n]['cliidxxx']]['finmanxx'] += $mFinManual[$n]['saldoxxx'];
            }
          }

          if ( $pvParametros['origenxx'] == "CONTROL") {
            $vFinManAct = array();
            for ($i=0; $i<count($pvParametros['tramites']); $i++) {
              $cKey = "{$pvParametros['tramites'][$i]['sucidxxx']}~{$pvParametros['tramites'][$i]['docidxxx']}~{$pvParametros['tramites'][$i]['docsufxx']}~".$vCatCon["{$pvParametros['tramites'][$i]['ctoidxxx']}"];
              for ($n=0; $n<count($mFinManual); $n++) {
                if ($mFinManual[$n]['sucidxxx'] == $pvParametros['tramites'][$i]['sucidxxx'] &&
                    $mFinManual[$n]['docidxxx'] == $pvParametros['tramites'][$i]['docidxxx'] &&
                    $mFinManual[$n]['docsufxx'] == $pvParametros['tramites'][$i]['docsufxx'] &&
                    $mFinManual[$n]['cacidxxx'] == $vCatCon["{$pvParametros['tramites'][$i]['ctoidxxx']}"]) {
                  $vFinManAct[$cKey]['inicialx'] = $mFinManual[$n]['inicialx'];
                  $vFinManAct[$cKey]['saldoxxx'] = $mFinManual[$n]['saldoxxx'];
                }
              }
            }
          }

          //  $tFin = microtime(true);
          //  echo "Tiempo de ejecucion: ".bcsub($tFin, $tInicio, 4)."<br>";

          if ($pvParametros['origenxx'] == "REPORTE") {
            /**
              * Creando Tabla temporal para el movimiento de Inventario
              */
            $vParametros['TIPOESTU'] = "SALDOFINANCIACION";
            $mReturnTabla = $objTablasTemporales->fnCrearEstructurasFinanciacionCliente($vParametros);

            if($mReturnTabla[0] == "false"){
              $nSwitch = 1;
              for($nR=1;$nR<count($mReturnTabla);$nR++){
                $cMsj = $mReturnTabla[$nR]."\n";
              }
            }

            $qInsCab  = "INSERT INTO $cAlfa.{$mReturnTabla[1]} (";
            $qInsCab .= "cliidxxx, "; // Cliente
            $qInsCab .= "clinomxx, "; // Nombre cliente
            $qInsCab .= "tipfincl, "; // Tipo Financiacion
            $qInsCab .= "salfincl,"; // Saldo Financiacion
            $qInsCab .= "tramites) VALUES "; // Saldo Financiacion
          }

          // echo "<pre>";
          // print_r($mClientes);
          // echo "</pre>";

          $nError = 0; $qInsert = ""; $nCanReg = 0;
          foreach ($mClientes as  $cKey => $cValue) {
            //Formula general del control de cupos:
            //    (Financiación Asignada al cliente (General o Específica)
            //  + Anticipos
            //  + Fondos Operativos (Cuentas en Variable)
            //  + Financiación manual (esta se desconto del pago a tercero cuando se busco el movimiento contable))
            //  - (PCC, Incluyendo los que se estan causando (se incluyen Tributos dependiendo de la parametrizacion del cliente)
            //     + Cartera pendiente del cliente que no tenga Recibo de Caja que cruce el pago de facturas del cliente en PCC.
            //     + anticipos a proveedores)

            //Se incluyen los fondos operativos
            $nCupoCliente  = ($mClientes[$cKey]['fpccupox'] + $mClientes[$cKey]['anticipo'] + $mClientes[$cKey]['fondoope'] + $mClientes[$cKey]['finmanxx']);
            $nCupoCliente -= ($mClientes[$cKey]['pagoster'] + $mClientes[$cKey]['antprove'] + $mClientes[$cKey]['carterap']);

            $nSobregiro  = ($mClientes[$cKey]['fpccupox'] + $mClientes[$cKey]['fondoope']);
            $nSobregiro -= ($mClientes[$cKey]['antprove'] + $mClientes[$cKey]['carterap']);
            $nSobregiro  = ($nSobregiro > 0) ? $nSobregiro : 0;
            
            $cResultado  = "$nCupoCliente  = ";
            $cResultado .= "(fpccupox {$mClientes[$cKey]['fpccupox']} ";
            $cResultado .= "+ anticipo {$mClientes[$cKey]['anticipo']} ";
            $cResultado .= "+ fondoope {$mClientes[$cKey]['fondoope']} ";
            $cResultado .= "+ finmanxx {$mClientes[$cKey]['finmanxx']}) ";
            $cResultado .= "- (pagoster {$mClientes[$cKey]['pagoster']} ";
            $cResultado .= "+ antprove {$mClientes[$cKey]['antprove']} ";
            $cResultado .= "+ carterap {$mClientes[$cKey]['carterap']}) ";

            if ($pvParametros['origenxx'] == "SALDO" && $kUser == "ADMIN") {
              echo $cResultado."<br><br>";
              echo "origen: ".$pvParametros['origenxx']."<br><br>";
              echo "Sobregiro: ".$nSobregiro."<br><br>";
            }
            // $nSwitch = 1;

            $mClientes[$cKey]['saldofin'] = $nCupoCliente;

            switch ($pvParametros['origenxx']) {
              case "REPORTE":
                if ($mClientes[$cKey]['cliidxxx'] != "") {
                  $qInsert .= "(\"".$mClientes[$cKey]['cliidxxx']."\", "; // Cliente
                  $qInsert .= "\"".$mClientes[$cKey]['clinomxx']."\", "; // Nombre cliente
                  $qInsert .= "\"".$mClientes[$cKey]['tipofina']."\", "; // Tipo Financiacion
                  $qInsert .= "\"".$mClientes[$cKey]['saldofin']."\","; // Saldo Financiacion
                  $qInsert .= "\"".implode("|",$mClientes[$cKey]['tramites'])."\"),"; // Tramites
                }

                $nCanReg++;
                if (($nCanReg % _NUMREG_) == 0) {
                  $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01);
                  if ($qInsert != "") {
                    $qInsert = $qInsCab.substr($qInsert, 0, -1);
                    $nQueryTimeStart = microtime(true); $xInsDet = mysql_query($qInsert,$xConexion01);
                    $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);

                    if(!$xInsDet) {
                      $nError = 1;
                    }
                  }
                  $qInsert = "";
                }
              break;
              case "CONTROL":
                $nControl = 0;  $cCupoFaltantexDo = "";
                if (($nCupoCliente+0) < 0) {
                  //Se valida si el DO tiene cupo
                  //Anticipos + Financiacion Manual - PCC No Facturados

                  // echo "<pre>";
                  // print_r($vPCCxCto);
                  // echo "</pre>";

                  $vCupoxDo = array();
                  foreach ($vPCCxCto as $cKey => $cValue) {
                    foreach ($vPCCxCto[$cKey] as $cKeyPCC => $cValuePcc) {
                      
                      //Logica para saber si el sobregiro cubre los pagos a terceros ya causados y los que se quieren causar.
                      $nTotalxPago = $vPCCxCto[$cKey][$cKeyPCC]['anticipo'] + $vPCCxCto[$cKey][$cKeyPCC]['finmanxx'] - $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];

                      //Si es negativo y el sobregiro es mayor a cero se descuenta el valor del sobregiro
                      //Para que sea cubierto
                      if ($nSobregiro > 0 && $nTotalxPago < 0) {
                        //Si el sobre giro cubre el pago 
                        if ($nSobregiro >= abs($nTotalxPago)) {
                          $nSobregiro += $nTotalxPago;
                          $nTotalxPago = 0;
                        } else {
                          //si el sobre giro solo cubre parte del pago
                          $nTotalxPago += $nSobregiro;
                          $nSobregiro = 0;
                        }
                      }

                      //Extrayendo el DO, que es sucursal~do~sufijo
                      $cKeyAux = explode("~", $cKeyPCC);
                      $cKeyCupoxDo = $cKeyAux[0]."~".$cKeyAux[1]."~".$cKeyAux[2];
                      if (in_array ("$cKeyCupoxDo",$vTramCausar) == true) {
                        $vCupoxDo["{$cKeyCupoxDo}"]['valorxxx'] += $nTotalxPago;
                        $vCupoxDo["{$cKeyCupoxDo}"]['anticipo'] += $vPCCxCto[$cKey][$cKeyPCC]['anticipo'];
                        $vCupoxDo["{$cKeyCupoxDo}"]['finmanxx'] += $vPCCxCto[$cKey][$cKeyPCC]['finmanxx'];
                        if ($vPCCxCto[$cKey][$cKeyPCC]['nuevopcc'] == "SI") {
                            $vCupoxDo["{$cKeyCupoxDo}"]['totalxxx'] += $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];
                        } else {
                          $vCupoxDo["{$cKeyCupoxDo}"]['totalpcc'] += $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];
                        }
                      }  
                    }
                  }

                  // echo "<pre>";
                  // print_r($vCupoxDo);
                  // echo "</pre>";

                  $nCupoxDo = 0; $cCupoFaltantexDo = "";
                  foreach ($vCupoxDo as $cKey01 => $cValue01) {
                    if ($vCupoxDo[$cKey01]['valorxxx'] < 0) {
                      $nCupoxDo = 1;
                      $cCupoFaltantexDo  = "DO: ".str_replace("~","-",$cKey01).", ";
                      $cCupoFaltantexDo .= "Anticipo: ".((strpos(($vCupoxDo[$cKey01]['anticipo']+0),'.') > 0) ? number_format(($vCupoxDo[$cKey01]['anticipo']),2,',','.') : number_format(($vCupoxDo[$cKey01]['anticipo']),0,',','.')).", ";
                      $cCupoFaltantexDo .= "Financiacion Manual: ".((strpos(($vCupoxDo[$cKey01]['finmanxx']+0),'.') > 0) ? number_format(($vCupoxDo[$cKey01]['finmanxx']),2,',','.') : number_format(($vCupoxDo[$cKey01]['finmanxx']),0,',','.')).", ";
                      $cCupoFaltantexDo .= "Pagos a Terceros: ".((strpos(($vCupoxDo[$cKey01]['totalpcc']+0),'.') > 0) ? number_format(($vCupoxDo[$cKey01]['totalpcc']),2,',','.') : number_format(($vCupoxDo[$cKey01]['totalpcc']),0,',','.')).", ";
                      $cCupoFaltantexDo .= "Valor a Causar: ".((strpos(($vCupoxDo[$cKey01]['totalxxx']+0),'.') > 0) ? number_format(($vCupoxDo[$cKey01]['totalxxx']),2,',','.') : number_format(($vCupoxDo[$cKey01]['totalxxx']),0,',','.')).".\n\n";
                    }
                  }

                  //Si el cupo del DO es mayor o igual a cero se debe dejar causar
                  if ($nCupoxDo == 0) {
                    $nControl = 1;
                  }
                } else {
                  $nControl = 1;
                }

                // echo "<pre>";
                // print_r($vPCCxCto);
                // echo "</pre>";

                // Validando que si hay conceptos de tributos y para el cliente no aplica la opcion de control de tributos aduaneros
                // Si estos no fueron cubiertos por un anticipo, genera error
                foreach ($vPCCxCto as $cKey => $cValue) {
                  foreach ($vPCCxCto[$cKey] as $cKeyPCC => $cValuePcc) {
                    if ($vPCCxCto[$cKey][$cKeyPCC]['contribu'] == "SI") {
                        // echo ($vPCCxCto[$cKey][$cKeyPCC]['totalxxx']+0)." != ".($vPCCxCto[$cKey][$cKeyPCC]['anticipo']+0);
                        if (($vPCCxCto[$cKey][$cKeyPCC]['totalxxx']+0) != ($vPCCxCto[$cKey][$cKeyPCC]['anticipo']+0)) {
                            $nSwitch = 1;
                            $cMsj  = "El Valor del Anticipo No Cubre el Valor de los Pagos a Tributos del ";
                            $cMsj .= "Concepto [{$vPCCxCto[$cKey][$cKeyPCC]['ctoidxxx']}] por Valor de [".((strpos(abs($vPCCxCto[$cKey][$cKeyPCC]['totalxxx'])+0, '.') > 0) ? number_format(abs($vPCCxCto[$cKey][$cKeyPCC]['totalxxx']), 2, ',', '.') : number_format(abs($vPCCxCto[$cKey][$cKeyPCC]['totalxxx']), 0, ',', '.'))."].\n";
                            $mReturn[count($mReturn)] = $cMsj;
                        }
                    }
                  }
                }
                
                if ($nControl == 0) {
                  $nSwitch = 1;
                  $nCupoDisponible = (($nCupoCliente+0) > 0) ? ($nCupoCliente+0) : 0;
                  $nCupoFaltante = abs($nCupoCliente);
                  
                  $cCat  = (count($vConCau) > 1) ? "Categorias Conceptos que esta causando" : "Categoria Concepto que esta causando";
                  $cMsj  = "El Cliente [$cKey - {$mClientes[$cKey]['clinomxx']}] No Tiene Cupo Suficiente, Solicite Financiacion Manual.\n";
                  $cMsj .= "$cCat: ".trim(implode(" - ",$mClientes[$cKey]['catidxxx']))."\n";
                  $cMsj .= "Cupo Disponible: ".((strpos(($nCupoDisponible),'.') > 0) ? number_format(($nCupoDisponible),2,',','.') : number_format(($nCupoDisponible),0,',','.'))."\n";
                  $cMsj .= "Cupo Faltante: ".((strpos(abs($nCupoFaltante)+0,'.') > 0) ? number_format(abs($nCupoFaltante),2,',','.') : number_format(abs($nCupoFaltante),0,',','.'))."\n";

                  if ($kUser == "ADMIN") {
                    $cResultado  = "\n\nTotal a Causar: $nTotalCausar\n";
                    $cResultado .= "Cupo: $nCupoCliente = ";
                    $cResultado .= "(Cupo+Sobregiro: {$mClientes[$cKey]['fpccupox']} ";
                    $cResultado .= "+ Anticipo: {$mClientes[$cKey]['anticipo']} ";
                    $cResultado .= "+ Fondos Operativos: {$mClientes[$cKey]['fondoope']} ";
                    $cResultado .= "+ Financiacion Manual: {$mClientes[$cKey]['finmanxx']}) ";
                    $cResultado .= "- (PCC: {$mClientes[$cKey]['pagoster']} ";
                    $cResultado .= "+ Anticipos Proveedor: {$mClientes[$cKey]['antprove']} ";
                    $cResultado .= "+ Cartera PCC: {$mClientes[$cKey]['carterap']}) ";
                    $cMsj .= $cResultado."\n\n";
                  }

                  if ($cCupoFaltantexDo != "") {
                    $cMsj .= "\n";
                    $cMsj .= "Detalle DO: \n\n";
                    $cMsj .= $cCupoFaltantexDo;
                  }
                  $mReturn[count($mReturn)] = $cMsj;
                } 

                if ($nSwitch == 0) {
                  $mReturn[1] = $vFinManAct;
                }
              break;
              case "SALDO":
                $mReturn[1] = $nCupoCliente;
              break;
              default:
                // No hace nada
              break;
            }
          } ## foreach ($mClientes as  $cKey => $cValue) { ##

          if ($pvParametros['origenxx'] == "REPORTE") {
            if ($nError == 0 && $qInsert != "") {
              $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01);

              $qInsert = $qInsCab.substr($qInsert, 0, -1);
              $nQueryTimeStart = microtime(true); $xInsDet = mysql_query($qInsert,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);

              if(!$xInsDet) {
                // echo $qInsert."<br><br>";
                $nError = 1;
              }
            }

            if ($nError == 1) {
              $nSwitch = 1;
              $mReturn[count($mReturn)] = "Error al Procesar Movimiento Contable para Obtener Saldo de Financiacion.";
            } else {
              $mReturn[1] = $mReturnTabla[1];
            }
          }
          //  echo "<pre>";
          //  print_r($mClientes);
          //  echo "</pre>";
          //  die();
        } ## if ($nSwitch == 0) { ##
      }

      //  $tFin = microtime(true);
      //  echo "Tiempo de ejecucion: ".bcsub($tFin, $tInicio, 4)."<br>";
      if ($nSwitch == 0) {
        $mReturn[0] = "true";
      } else {
        $mReturn[0] = "false";
      }
      return $mReturn;
    } ## function fnCupoFinanciacionCliente($vDatos) { ##

  /**
   * Metodo que retorna el saldo Financiacion del cliente
   */
  function fnSaldoFinanciacionCliente($pvParametros) {
    global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;
    /**
    * Recibe como Parametro un vector con las siguientes posiciones:
    * $pvParametros['cliidxxx'], nit clinte
    */

    $vParametros['origenxx'] = "SALDO";
    $vParametros['clientes'][0] = $pvParametros['cliidxxx'];

    $mReturnSaldos = $this->fnCupoFinanciacionCliente($vParametros);
    return $mReturnSaldos[1];
  }

  /**
   * Metodo que retorna el saldo Financiacion del cliente
   */
  function fnActualizarCupoManualAutorizado($pvParametros) {
    global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

    /**
    * Recibe como Parametro un vector con las siguientes posiciones:
    * $pvParametros[SUC~DO~SUFIJO~CATEGORIA]['inicialx'], valor inicial
    * $pvParametros[SUC~DO~SUFIJO~CATEGORIA]['saldoxxx'], valor sobrante
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
    * Variable para hacer el retorno.
    * En la posicion 0 se retorna true o false
    * En las demas posiciones se envian los mensajes de error
    * @var array
    */

    $mReturn = array();
    $mReturn[0] = "";

    /**
    * Instanciando Objeto para la creacion de las tablas temporales.
    */
    $objTablasTemporales = new cEstructurasFinanciacionCliente();

    foreach ($pvParametros as $cKey => $cValue) {

      $pvParametros[$cKey]['utilizax'] = $pvParametros[$cKey]['inicialx'] - $pvParametros[$cKey]['saldoxxx'];
      // echo $pvParametros[$cKey]['inicialx']."-".$pvParametros[$cKey]['saldoxxx']." = ".$pvParametros[$cKey]['utilizax']."<br>";

      $vDatos = explode("~", $cKey);
      $qFinMan  = "SELECT * ";
      $qFinMan .= "FROM $cAlfa.fpar0148 ";
      $qFinMan .= "WHERE ";
      $qFinMan .= "sucidxxx = \"{$vDatos[0]}\" AND ";
      $qFinMan .= "docidxxx = \"{$vDatos[1]}\" AND ";
      $qFinMan .= "docsufxx = \"{$vDatos[2]}\" AND ";
      $qFinMan .= "cacidxxx = \"{$vDatos[3]}\" AND ";
      $qFinMan .= "sfmestxx = \"APROBADO\" AND ";
      $qFinMan .= "regestxx = \"ACTIVO\" ";
      $nQueryTimeStart = microtime(true); $xFinMan  = mysql_query($qFinMan,$xConexion01);
      $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
      // f_Mensaje(__FILE__,__LINE__,$qFinMan."~".mysql_num_rows($xFinMan));
      // echo $qFinMan."~".mysql_num_rows($xFinMan)."<br>";
      while ($xRFM = mysql_fetch_assoc($xFinMan)) {
        // echo "utiliza: ".($pvParametros[$cKey]['utilizax']+0)."~".($xRFM['sfmvalxx']+0)."<br><br>";

        if (($pvParametros[$cKey]['utilizax']+0) > 0) {
          if (($pvParametros[$cKey]['utilizax']+0) > ($xRFM['sfmvalxx']+0)) {
            $nValUti = $xRFM['sfmvalxx'];
            $pvParametros[$cKey]['utilizax'] -= $xRFM['sfmvalxx'];
          } else {
            $nValUti = $pvParametros[$cKey]['utilizax'];
            $pvParametros[$cKey]['utilizax'] = 0;
          }
        } else {
          $nValUti = 0;
        }

        $qUpdate  = "UPDATE $cAlfa.fpar0148 SET ";
        $qUpdate .= "sfmvalut = \"$nValUti\" ";
        $qUpdate .= "WHERE ";
        $qUpdate .= "sfmidxxx = \"{$xRFM['sfmidxxx']}\" ";
        $nQueryTimeStart = microtime(true); $xUpdate  = mysql_query($qUpdate,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
        if (!$xUpdate){
          $nSwitch = 1;
          $mReturn[count($mReturn)] = "Erro al Actualizar la Solicitud Financiacion Manual asociada al DO [{$vDatos[0]}-{$vDatos[1]}-{$vDatos[2]}] y Categoria [{$vDatos[3]}].";
        }
      }
    }

    if ($nSwitch == 0) {
      $mReturn[0] = "true";
    } else {
      $mReturn[0] = "false";
    }
    return $mReturn;
  }

  /**
   * Metodo que retorna la data del reporte de la financiacion cliente
   */
  function fnRepoteFinanciacionCliente($pvParametros) {
    global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;
    /**
    * Recibe como Parametro un vector con las siguientes posiciones:
    * $pvParametros['feccorte'] // Fecha de Corte
    * $pvParametros['cliidxxx'] // Cliente
    * $pvParametros['tablaxxx'] // Tabla de Data
    * $pvParametros['tablaerr'] // Tabla de Errores.
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
    * Variable para hacer el retorno.
    * En la posicion 0 se retorna true o false
    * En las demas posiciones se envian los mensajes de error
    * @var array
    */
    $mReturn = array();
    $mReturn[0] = "";

    /**
     * Indica la cantidad de registros que se debe consultar por bloques en la base de datos.
     *
     * @var int
     */
    $nNumReg = 1000;

    /**
     * Se define hasta que año se busca en las tablas anualizadas, desde el año actual menos 4.
     *
     * @var int
     */
    $dAnioInicio = ((date('Y')-4) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (date('Y')-4);

    /*
    * Vector de Errores
    */
    $vError = array();
    $vError['TABLAERR'] = $pvParametros['tablaerr'];

    /**
    * Instanciando Objetos para el Guardado de Errores
    */
    $objTablasTemporales = new cEstructurasFinanciacionCliente();

    /**
     * Inicializando variables del sistema, mientras hace el script para crearlas
     */

    // roldanlo_cuentas_cxc_facturas_cliente_financiacion_clientes        => CxC Clientes y Saldos a favor del cliente: 1305100100, 1305050100,1305050200
    // roldanlo_cuentas_cxp_saldos_a_favor_cliente_financiacion_clientes  => Saldos a favor CxP: 2305050100, 2305100100, 2305050200
    // roldanlo_cuentas_pcc_financiacion_clientes                         => PCC: 13102000100
    // roldanlo_cuentas_anticipos_operativos_financiacion_clientes        => Anticipos operativos: 2805050100
    // roldanlo_cuentas_fondos_operativos_financiacion_clientes           => Fondos operativos: 2805050300
    // roldanlo_cuentas_anticipos_proveedor_financiacion_clientes         => Anticipos a proveedor: 1330050100
    
    try {
      //Buscando Tipo financiacion cliente y cartera vencida del cliente
      $mClientes  = array(); //Vector con la informacion de financiacion de lo clientes enviados en la matriz
      $vIdClientes = array(); //Nits de lo clientes enviados en la matriz, para buscar su informacion solo una vez
      $mFinManual  = array();

      $qClientes  = "SELECT ";
      $qClientes .= "CLIIDXXX, ";
      $qClientes .= "IF(CLINOMXX != \"\",CLINOMXX,CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) AS CLINOMXX, ";
      $qClientes .= "REGESTXX ";
      $qClientes .= "FROM $cAlfa.SIAI0150 ";
      $qClientes .= "WHERE ";
      $qClientes .= "CLICLIXX = \"SI\" ";
      if ($pvParametros['cliidxxx'] != "") {
        $qClientes .= "AND CLIIDXXX = \"{$pvParametros['cliidxxx']}\" ";
      }
      // $qClientes .= "REGESTXX = \"ACTIVO\" ";
      $qClientes .= "ORDER BY ABS(CLIIDXXX) ";
      $nQueryTimeStart = microtime(true); $xClientes  = mysql_query($qClientes,$xConexion01);
      $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
      // echo $qClientes."~".mysql_num_rows($xClientes)."<br>";
      // f_Mensaje(__FILE__,__LINE__,$qClientes."~".mysql_num_rows($xClientes));

      $vError['LINEAERR'] = __LINE__;
      $vError['TIPOERRX'] = "ADVERTENCIA";
      $vError['DESERROR'] = mysql_num_rows($xClientes)."~".$qClientes;
      $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

      $nCanReg = 0; $nError = 0; $mFinManxDO = array();
      while ($xRC = mysql_fetch_array($xClientes)) {

        //Busacando el cupo de financiacion del cliente
        $qCupFin  = "SELECT * ";
        $qCupFin .= "FROM $cAlfa.fpar0147 ";
        $qCupFin .= "WHERE ";
        $qCupFin .= "cliidxxx = \"{$xRC['CLIIDXXX']}\" AND ";
        $qCupFin .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $nQueryTimeStart = microtime(true); $xCupFin  = mysql_query($qCupFin,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
        //  f_Mensaje(__FILE__,__LINE__,$qCupFin."~".mysql_num_rows($xCupFin));
        $vError['LINEAERR'] = __LINE__;
        $vError['TIPOERRX'] = "ADVERTENCIA";
        $vError['DESERROR'] = mysql_num_rows($xCupFin)."~".$qCupFin;
        $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

        if (mysql_num_rows($xCupFin) == 0) {
          $mClientes["{$xRC['CLIIDXXX']}"]['fpccupox'] = $vSysStr['financiero_cupo_autorizado_para_clientes_sin_cupo'];
          $mClientes["{$xRC['CLIIDXXX']}"]['fpcsobgi'] = 0;
          $mClientes["{$xRC['CLIIDXXX']}"]['fpcfecxx'] = "0000-00-00";
          $mClientes["{$xRC['CLIIDXXX']}"]['tipofina'] = "MANUAL";
          $mClientes["{$xRC['CLIIDXXX']}"]['fpcatria'] = "NO"; //Aplica control de Financiación para TRIBUTOS ADUANEROS
        } else {
          $vCupFin = mysql_fetch_assoc($xCupFin);
          //Si el tipo de cupo es SIN-CUPO se asigna el cupo de la variable del sistema
          $mClientes["{$xRC['CLIIDXXX']}"]['fpccupox'] = ($vCupFin['fpctipxx'] == "SIN-CUPO") ? $vSysStr['financiero_cupo_autorizado_para_clientes_sin_cupo'] : $vCupFin['fpccupox'];
          $mClientes["{$xRC['CLIIDXXX']}"]['fpcsobgi'] = $vCupFin['fpcsobgi'];
          $mClientes["{$xRC['CLIIDXXX']}"]['fpcfecxx'] = $vCupFin['fpcfecxx'];
          $mClientes["{$xRC['CLIIDXXX']}"]['tipofina'] = "AUTOMATICA";
          $mClientes["{$xRC['CLIIDXXX']}"]['fpcatria'] = $vCupFin['fpcatria']; //Aplica control de Financiación para TRIBUTOS ADUANEROS
        }

        $qFinMan  = "SELECT ";
        $qFinMan .= "$cAlfa.fpar0148.sucidxxx AS sucidxxx, ";
        $qFinMan .= "$cAlfa.fpar0148.docidxxx AS docidxxx, ";
        $qFinMan .= "$cAlfa.fpar0148.docsufxx AS docsufxx, ";
        $qFinMan .= "$cAlfa.sys00121.cliidxxx AS cliidxxx, ";
        $qFinMan .= "SUM($cAlfa.fpar0148.sfmvalxx) AS sfmvalxx, ";
        $qFinMan .= "SUM(IF($cAlfa.sys00121.regestxx = \"ACTIVO\",$cAlfa.fpar0148.sfmvalxx, 0)) AS sfmvalac ";
        $qFinMan .= "FROM $cAlfa.fpar0148 ";
        $qFinMan .= "LEFT JOIN $cAlfa.sys00121 ON ";
        $qFinMan .= "$cAlfa.sys00121.sucidxxx = $cAlfa.fpar0148.sucidxxx AND ";
        $qFinMan .= "$cAlfa.sys00121.docidxxx = $cAlfa.fpar0148.docidxxx AND ";
        $qFinMan .= "$cAlfa.sys00121.docsufxx = $cAlfa.fpar0148.docsufxx ";
        $qFinMan .= "WHERE ";
        $qFinMan .= "$cAlfa.sys00121.cliidxxx = \"{$xRC['CLIIDXXX']}\" AND ";
        $qFinMan .= "$cAlfa.fpar0148.sfmestxx = \"APROBADO\" AND ";
        $qFinMan .= "$cAlfa.fpar0148.regestxx = \"ACTIVO\" ";
        $qFinMan .= "GROUP BY $cAlfa.fpar0148.sucidxxx,$cAlfa.fpar0148.docidxxx,$cAlfa.fpar0148.docsufxx";
        $nQueryTimeStart = microtime(true); $xFinMan  = mysql_query($qFinMan,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
        //  echo $qFinMan."~".mysql_num_rows($xFinMan)."<br>";
        $vError['LINEAERR'] = __LINE__;
        $vError['TIPOERRX'] = "ADVERTENCIA";
        $vError['DESERROR'] = mysql_num_rows($xFinMan)."~".$qFinMan;
        $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

        $nSfmVal = 0; $nSfmValAc = 0;
        while($xRFM = mysql_fetch_array($xFinMan)) {
          $nSfmVal   = $xRFM['sfmvalxx'];
          $nSfmValAc = $xRFM['sfmvalac'];
          $cKeyDo = $xRFM['sucidxxx']."~".$xRFM['docidxxx']."~".$xRFM['docsufxx'];
          $mFinManxDO["{$xRFM['cliidxxx']}"][$cKeyDo] += $xRFM['sfmvalxx'];

          if (in_array("{$xRFM['sucidxxx']}~{$xRFM['docidxxx']}~{$xRFM['docsufxx']}", $mClientes["{$xRFM['cliidxxx']}"]['tramites']) == false){
            $mClientes["{$xRFM['cliidxxx']}"]['tramites'][] = "{$xRFM['sucidxxx']}~{$xRFM['docidxxx']}~{$xRFM['docsufxx']}";
          }
        }

        $vIdClientes[count($vIdClientes)] = $xRC['CLIIDXXX'];

        $mClientes["{$xRC['CLIIDXXX']}"]['cliidxxx'] = $xRC['CLIIDXXX'];
        $mClientes["{$xRC['CLIIDXXX']}"]['clinomxx'] = $xRC['CLINOMXX'];
        $mClientes["{$xRC['CLIIDXXX']}"]['regestxx'] = $xRC['REGESTXX'];
        $mClientes["{$xRC['CLIIDXXX']}"]['sfmvalxx'] = $nSfmVal;
        $mClientes["{$xRC['CLIIDXXX']}"]['sfmvalac'] = $nSfmValAc;
      }
      $xFree = mysql_free_result($xClientes);

      if (count($vIdClientes) > 0) {

        //Buscando Cartera
        $vPucIdCxC = explode(",",$vSysStr['roldanlo_cuentas_cxc_facturas_cliente_financiacion_clientes']);
        $cPucIdCxC = "\"".implode("\",\"", $vPucIdCxC)."\"";

        $mSumatoria = array(); $mCxC = array(); $mSaldosFavor = array();
        if ($cPucIdCxC != "") {
          for ($iAno = $dAnioInicio; $iAno<=date('Y'); $iAno++) {

            $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01);

            // Consulta la cantidad de registros
            $qLoad  = "SELECT SQL_CALC_FOUND_ROWS ";
            $qLoad .= "teridxxx, pucidxxx, comidcxx, comcodcx, comcsccx, comseqcx, ";
            $qLoad .= "SUM(IF($cAlfa.fcod$iAno.commovxx=\"D\",IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx, $cAlfa.fcod$iAno.comvlrnf),IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx*-1, $cAlfa.fcod$iAno.comvlrnf*-1))) as saldoxxx ";
            $qLoad .= "FROM $cAlfa.fcod$iAno ";
            $qLoad .= "WHERE ";
            if ($pvParametros['cliidxxx'] != "") {
              $qLoad .= "teridxxx = \"{$pvParametros['cliidxxx']}\" AND ";
            }
            $qLoad .= "$cAlfa.fcod$iAno.pucidxxx IN ($cPucIdCxC) AND ";
            $qLoad .= "$cAlfa.fcod$iAno.comfecxx BETWEEN \"{$vSysStr['financiero_ano_instalacion_modulo']}-01-01\" AND \"{$pvParametros['feccorte']}\" AND ";
            $qLoad .= "$cAlfa.fcod$iAno.regestxx = \"ACTIVO\" ";
            $qLoad .= "GROUP BY teridxxx, pucidxxx, comidcxx, comcodcx, comcsccx, comseqcx ";
            $nQueryTimeStart = microtime(true); $xLoad  = mysql_query($qLoad,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);

            $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
            $xRNR = mysql_fetch_array($xNumRows);
            $nRegistros = $xRNR['FOUND_ROWS()'];
            mysql_free_result($xNumRows);

            // Se consultan los registros por bloques
            $nCanReg01 = 0;
            for ($i=0;$i<=$nRegistros;$i+=$nNumReg) {

              $qMovCon  = "SELECT ";
              $qMovCon .= "teridxxx, pucidxxx, comidcxx, comcodcx, comcsccx, comseqcx, ";
              $qMovCon .= "SUM(IF($cAlfa.fcod$iAno.commovxx=\"D\",IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx, $cAlfa.fcod$iAno.comvlrnf),IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx*-1, $cAlfa.fcod$iAno.comvlrnf*-1))) as saldoxxx ";
              $qMovCon .= "FROM $cAlfa.fcod$iAno ";
              $qMovCon .= "WHERE ";
              if ($pvParametros['cliidxxx'] != "") {
                $qMovCon .= "teridxxx = \"{$pvParametros['cliidxxx']}\" AND ";
              }
              $qMovCon .= "$cAlfa.fcod$iAno.pucidxxx IN ($cPucIdCxC) AND ";
              $qMovCon .= "$cAlfa.fcod$iAno.comfecxx BETWEEN \"{$vSysStr['financiero_ano_instalacion_modulo']}-01-01\" AND \"{$pvParametros['feccorte']}\" AND ";
              $qMovCon .= "$cAlfa.fcod$iAno.regestxx = \"ACTIVO\" ";
              $qMovCon .= "GROUP BY teridxxx, pucidxxx, comidcxx, comcodcx, comcsccx, comseqcx ";
              $qMovCon .= "LIMIT $i,$nNumReg; ";
              $nQueryTimeStart = microtime(true); $xMovCon  = mysql_query($qMovCon,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
              // if (mysql_num_rows($xMovCon) > 0) {
                // echo "<br>CxC: <br>".mysql_num_rows($xMovCon)."~".$qMovCon."<br>";
              //  f_Mensaje(__FILE__,__LINE__,mysql_num_rows($xMovCon)."~".$qMovCon);
              // }
              $vError['LINEAERR'] = __LINE__;
              $vError['TIPOERRX'] = "ADVERTENCIA";
              $vError['DESERROR'] = mysql_num_rows($xMovCon)."~".$qMovCon;
              $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

              while ($xRMC = mysql_fetch_assoc($xMovCon)) {
                $nCanReg01++;
                if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01); }
                $cDocCru = "{$xRMC['teridxxx']}~{$xRMC['pucidxxx']}~{$xRMC['comidcxx']}~{$xRMC['comcodcx']}~{$xRMC['comcsccx']}~{$xRMC['comseqcx']}";
                $mSumatoria["$cDocCru"]['comvlrxx'] += $xRMC['saldoxxx'];
                $mSumatoria["$cDocCru"]['comproba'][] = $xRMC;
              }
              $xFree = mysql_free_result($xMovCon);
            }
          }

          // echo number_format($nSumCxC,2,',','.')."<br><br>";
          foreach ($mSumatoria as $cKey => $nValue) {
            if (round($mSumatoria[$cKey]['comvlrxx'],2) > 0) {
              $mCxC[$cKey] += $mSumatoria[$cKey]['comvlrxx'];
            }

            if (round($mSumatoria[$cKey]['comvlrxx'],2) < 0) {
              $mSaldosFavor[$cKey] += $mSumatoria[$cKey]['comvlrxx'];
            }
          }

          foreach ($mCxC as $cKey => $cValue) {

            $vDocumento = explode("~",$cKey);

            $vRCxC = array();
            $vRCxC['teridxxx'] = $vDocumento[0];
            $vRCxC['pucidxxx'] = $vDocumento[1];
            $vRCxC['comidxxx'] = $vDocumento[2];
            $vRCxC['comcodxx'] = $vDocumento[3];
            $vRCxC['comcscxx'] = $vDocumento[4];
            $vRCxC['comsaldo'] = $mCxC[$cKey];

            for ($nAno = date('Y'); $nAno>=$dAnioInicio; $nAno--) {
              //Buscando si el registro existe en cabecera
              $qFcoc  = "SELECT ";
              $qFcoc .= "comfecxx,";
              $qFcoc .= "comfecve,";
              $qFcoc .= "comvlrxx,";
              $qFcoc .= "comvlr01,";
              $qFcoc .= "comvlr02,";
              $qFcoc .= "comvlr03,";
              $qFcoc .= "comifxxx,";
              $qFcoc .= "comipxxx,";
              $qFcoc .= "comivaxx,";
              $qFcoc .= "comrftex,";
              $qFcoc .= "comrcrex,";
              $qFcoc .= "comrivax,";
              $qFcoc .= "comricax,";
              $qFcoc .= "comarfte,";
              $qFcoc .= "comarcre,";
              $qFcoc .= "comarica ";
              $qFcoc .= "FROM $cAlfa.fcoc$nAno ";
              $qFcoc .= "WHERE ";
              $qFcoc .= "comidxxx = \"{$vRCxC['comidxxx']}\" AND ";
              $qFcoc .= "comcodxx = \"{$vRCxC['comcodxx']}\" AND ";
              $qFcoc .= "comcscxx = \"{$vRCxC['comcscxx']}\" LIMIT 0,1";
              $nQueryTimeStart = microtime(true); $xFcoc  = mysql_query($qFcoc,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
              //f_Mensaje(__FILE__,__LINE__,$qFcoc." ~ ".mysql_num_rows($xFcoc));
              // echo $qFcoc." ~ ".mysql_num_rows($xFcoc)."<br>";

              $vError['LINEAERR'] = __LINE__;
              $vError['TIPOERRX'] = "ADVERTENCIA";
              $vError['DESERROR'] = mysql_num_rows($xFcoc)."~".$qFcoc;
              $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

              if (mysql_num_rows($xFcoc) > 0) {

                //Total Cartera
                $mClientes["{$vRCxC['teridxxx']}"]['comsaldo'] += $vRCxC['comsaldo'];

                $vFcoc = mysql_fetch_assoc($xFcoc);

                $vRCxC['comvlrxx'] = $vFcoc['comvlrxx'];
                $vRCxC['comvlr01'] = $vFcoc['comvlr01'];
                $vRCxC['comvlr02'] = $vFcoc['comvlr02'];
                $vRCxC['comvlr03'] = $vFcoc['comvlr03'];
                $vRCxC['comifxxx'] = $vFcoc['comifxxx'];
                $vRCxC['comipxxx'] = $vFcoc['comipxxx'];
                $vRCxC['comivaxx'] = $vFcoc['comivaxx'];
                $vRCxC['comrftex'] = $vFcoc['comrftex'];
                $vRCxC['comrcrex'] = $vFcoc['comrcrex'];
                $vRCxC['comrivax'] = $vFcoc['comrivax'];
                $vRCxC['comricax'] = $vFcoc['comricax'];
                $vRCxC['comarfte'] = $vFcoc['comarfte'];
                $vRCxC['comarcre'] = $vFcoc['comarcre'];
                $vRCxC['comarica'] = $vFcoc['comarica'];

                $nCaretra_Original     = ($vRCxC['comvlr01'] * -1) + $vRCxC['comvlr02'] + $vRCxC['comvlr03'] + $vRCxC['comifxxx'] + $vRCxC['comipxxx'] + $vRCxC['comivaxx'] - ($vRCxC['comrftex'] + $vRCxC['comrcrex'] + $vRCxC['comrivax'] + $vRCxC['comricax']) + ($vRCxC['comarfte'] + $vRCxC['comarcre'] + $vRCxC['comarica']);
                $nSaldo_Actual_Cartera = $vRCxC['comsaldo'];
                $nCartera_Valor_Pagado = ($nCaretra_Original - $nSaldo_Actual_Cartera);

                // echo "Caretra_Original: ".$nCaretra_Original."~Saldo_Actual_Cartera: ".$nSaldo_Actual_Cartera."~Cartera_Valor_Pagado: ".$nCartera_Valor_Pagado."<br>";

                $nAnticipos        = ($vRCxC['comvlr01'] * -1);
                $nPagos_Terceros   = ($vRCxC['comvlr02'] + $vRCxC['comvlr03'] + $vRCxC['comifxxx']);
                $nIngresos_Propios = ($vRCxC['comipxxx'] + $vRCxC['comivaxx'] - ($vRCxC['comrftex'] + $vRCxC['comrcrex'] + $vRCxC['comrivax'] + $vRCxC['comricax']) + ($vRCxC['comarfte'] + $vRCxC['comarcre'] + $vRCxC['comarica']));

                // echo "Anticipos: ".$nAnticipos."~Pagos_Terceros: ".$nPagos_Terceros."~Ingresos_Propios: ".$nIngresos_Propios."<br>";
                // Verifico si el anticipo alcanzo para pagar los pagos a terceros
                if ($vRCxC['comvlr01'] < $nPagos_Terceros) { // El anticipo no alcanzo para cubrir los pagos a terceros
                  $nCartera_PCC_FORMS_IF     = ($nAnticipos + $nPagos_Terceros);
                  $nCartera_IP_IVA_RETENCIONES = $nIngresos_Propios;
                } else { // El anticipo si alcanzo para cubrir los pagos a terceros
                  $nCartera_PCC_FORMS_IF     = 0;
                  $nCartera_IP_IVA_RETENCIONES = (($nAnticipos + $nPagos_Terceros) + $nIngresos_Propios);
                }
                // Fin de Verifico si el anticipo alcanzo para pagar los pagos a terceros

                if ($nSaldo_Actual_Cartera <= $nCaretra_Original) { // Pregunto si hubo abonos a la cartera
                  if ($nCartera_Valor_Pagado <= $nCartera_PCC_FORMS_IF) { // Es porque el valor abonado a la cartera cubre parcial o total el valor de los PCC
                    $mClientes["{$vRCxC['teridxxx']}"]['carpccxx'] += ($nCartera_PCC_FORMS_IF - $nCartera_Valor_Pagado);
                    $mClientes["{$vRCxC['teridxxx']}"]['caripxxx'] += $nCartera_IP_IVA_RETENCIONES;
                  } else { // Es porque el valor abonado a la cartera cubre el total de los PCC y alcanza para los ingresos propios parcial o total
                    $mClientes["{$vRCxC['teridxxx']}"]['carpccxx'] += 0; // Sumo cero a la acrtera de PCC porque el abono cubrio toda la cartera de PCC
                    $mClientes["{$vRCxC['teridxxx']}"]['caripxxx'] += ($nCartera_IP_IVA_RETENCIONES - ($nCartera_Valor_Pagado - $nCartera_PCC_FORMS_IF));
                  }
                } else { // Entra por aqui si no hubo abonos
                  $mClientes["{$vRCxC['teridxxx']}"]['carpccxx'] += ($nSaldo_Actual_Cartera - $nIngresos_Propios);
                  $mClientes["{$vRCxC['teridxxx']}"]['caripxxx'] += $nIngresos_Propios;
                }

                //Saldo Cartera Vencida 
                $dCorte  = date_create($pvParametros['feccorte']);
                $dFecVen = date_create($vFcoc['comfecve']);
                if ($dFecVen < $dCorte) {
                  $mClientes["{$vRCxC['teridxxx']}"]['carvenxx'] = $mClientes["{$vRCxC['teridxxx']}"]['carvenxx'] + $nSaldo_Actual_Cartera;
                  //$mClientes["{$vRCxC['teridxxx']}"]['carpccxx'] + $mClientes["{$vRCxC['teridxxx']}"]['caripxxx'];
                }
                //Fin Saldo Cartera Vencida 

                $nAno = $dAnioInicio - 1;
              } else {
                //Si no se encontro la factura en el movimiento contable, se busca en saldos iniciales y se lleva el valor
                //a los PCC
                for($nAnoC=$dAnioInicio;$nAnoC<=date('Y');$nAnoC++) {
                  $qFcod  = "SELECT ";
                  $qFcod .= "comfecve, ";
                  $qFcod .= "comvlrxx ";
                  $qFcod .= "FROM $cAlfa.fcod$nAnoC ";
                  $qFcod .= "WHERE ";
                  $qFcod .= "comidxxx = \"S\" AND ";
                  $qFcod .= "comcodxx = \"999\"  AND ";
                  $qFcod .= "comidcxx = \"{$vRCxC['comidxxx']}\"  AND ";
                  $qFcod .= "comcodcx = \"{$vRCxC['comcodxx']}\"  AND ";
                  $qFcod .= "comcsccx = \"{$vRCxC['comcscxx']}\"  AND ";
                  $qFcod .= "pucidxxx = \"{$vRCxC['pucidxxx']}\"  AND ";                 
                  $qFcod .= "teridxxx = \"{$vRCxC['teridxxx']}\" LIMIT 0,1 ";
                  $nQueryTimeStart = microtime(true); $xFcod  = mysql_query($qFcod,$xConexion01);
                  $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
                  //f_Mensaje(__FILE__,__LINE__,$qFcod." ~ ".mysql_num_rows($xFcod));
                  // echo $qFcod." ~ ".mysql_num_rows($xFcod)."<br><br>";
                  $vError['LINEAERR'] = __LINE__;
                  $vError['TIPOERRX'] = "ADVERTENCIA";
                  $vError['DESERROR'] = mysql_num_rows($xFcod)."~".$qFcod;
                  $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

                  if (mysql_num_rows($xFcod) > 0) {
                    $vFcod = mysql_fetch_assoc($xFcod);
                    $mClientes["{$vRCxC['teridxxx']}"]['carpccxx'] += $vRCxC['comsaldo']; //Saldo en cartera
                    $mClientes["{$vRCxC['teridxxx']}"]['caripxxx'] += 0;

                    //Saldo Cartera Vencida 
                    $dCorte  = date_create($pvParametros['feccorte']);
                    $dFecVen = date_create($vFcod['comfecve']);
                    if ($dFecVen < $dCorte) {
                      $mClientes["{$vRCxC['teridxxx']}"]['carvenxx'] = 
                      $mClientes["{$vRCxC['teridxxx']}"]['carpccxx'] + $mClientes["{$vRCxC['teridxxx']}"]['caripxxx'];
                    }
                    //Fin Saldo Cartera Vencida 

                    $nAnoC = date('Y') + 1;
                    $nAno = $dAnioInicio - 1;
                  }
                  $xFree = mysql_free_result($xFcod);
                }
              }
              $xFree = mysql_free_result($xFcoc);
            }
          }
        }

        //Buscando Saldos a Favor en la CxP
        $vPucIdSC = explode(",",$vSysStr['roldanlo_cuentas_cxp_saldos_a_favor_cliente_financiacion_clientes']);
        $cPucIdSC = "\"".implode("\",\"", $vPucIdSC)."\"";

        if ($cPucIdSC != "") {
          $mSumatoria = array(); $vCuenta = array(); 
          for ($iAno = $dAnioInicio; $iAno<=date('Y'); $iAno++) {

            $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01);

            // Consulta la cantidad de registros
            $qLoad  = "SELECT SQL_CALC_FOUND_ROWS ";
            $qLoad .= "teridxxx, pucidxxx, comidcxx, comcodcx, comcsccx, comseqcx, ";
            $qLoad .= "SUM(IF($cAlfa.fcod$iAno.commovxx=\"C\",IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx, $cAlfa.fcod$iAno.comvlrnf),IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx*-1, $cAlfa.fcod$iAno.comvlrnf*-1))) as saldoxxx ";
            $qLoad .= "FROM $cAlfa.fcod$iAno ";
            $qLoad .= "WHERE ";
            if ($pvParametros['cliidxxx'] != "") {
              $qLoad .= "teridxxx = \"{$pvParametros['cliidxxx']}\" AND ";
            }
            $qLoad .= "$cAlfa.fcod$iAno.pucidxxx IN ($cPucIdSC) AND ";
            $qLoad .= "$cAlfa.fcod$iAno.comfecxx BETWEEN \"{$vSysStr['financiero_ano_instalacion_modulo']}-01-01\" AND \"{$pvParametros['feccorte']}\" AND ";
            $qLoad .= "$cAlfa.fcod$iAno.regestxx = \"ACTIVO\" ";
            $qLoad .= "GROUP BY teridxxx, pucidxxx, comidcxx, comcodcx, comcsccx, comseqcx ";
            $nQueryTimeStart = microtime(true); $xLoad = mysql_query($qLoad,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);

            $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
            $xRNR = mysql_fetch_array($xNumRows);
            $nRegistros = $xRNR['FOUND_ROWS()'];
            mysql_free_result($xNumRows);

            // Se consultan los registros por bloques
            $nCanReg01 = 0;
            for ($i=0;$i<=$nRegistros;$i+=$nNumReg) {

              $qMovCon  = "SELECT ";
              $qMovCon .= "teridxxx, pucidxxx, comidcxx, comcodcx, comcsccx, comseqcx, ";
              $qMovCon .= "SUM(IF($cAlfa.fcod$iAno.commovxx=\"C\",IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx, $cAlfa.fcod$iAno.comvlrnf),IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx*-1, $cAlfa.fcod$iAno.comvlrnf*-1))) as saldoxxx ";
              $qMovCon .= "FROM $cAlfa.fcod$iAno ";
              $qMovCon .= "WHERE ";
              if ($pvParametros['cliidxxx'] != "") {
                $qMovCon .= "teridxxx = \"{$pvParametros['cliidxxx']}\" AND ";
              }
              $qMovCon .= "$cAlfa.fcod$iAno.pucidxxx IN ($cPucIdSC) AND ";
              $qMovCon .= "$cAlfa.fcod$iAno.comfecxx BETWEEN \"{$vSysStr['financiero_ano_instalacion_modulo']}-01-01\" AND \"{$pvParametros['feccorte']}\" AND ";
              $qMovCon .= "$cAlfa.fcod$iAno.regestxx = \"ACTIVO\" ";
              $qMovCon .= "GROUP BY teridxxx, pucidxxx, comidcxx, comcodcx, comcsccx, comseqcx ";
              $qMovCon .= "LIMIT $i,$nNumReg; ";
              $nQueryTimeStart = microtime(true); $xMovCon  = mysql_query($qMovCon,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
              // if (mysql_num_rows($xMovCon) > 0) {
                // echo "<br>Saldos a Favor: <br>".mysql_num_rows($xMovCon)."~".$qMovCon."<br>";
              // }

              $vError['LINEAERR'] = __LINE__;
              $vError['TIPOERRX'] = "ADVERTENCIA";
              $vError['DESERROR'] = mysql_num_rows($xMovCon)."~".$qMovCon;
              $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

              while ($xRMC = mysql_fetch_assoc($xMovCon)) {
                $nCanReg01++;
                if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01); }
                $cDocCru = "{$xRMC['teridxxx']}~{$xRMC['pucidxxx']}~{$xRMC['comidcxx']}~{$xRMC['comcodcx']}~{$xRMC['comcsccx']}~{$xRMC['comseqcx']}";
                $mSumatoria["$cDocCru"]['comvlrxx'] += $xRMC['saldoxxx'];
                $mSumatoria["$cDocCru"]['comproba'][] = $xRMC;     
              }
              $xFree = mysql_free_result($xMovCon);
            }
          }

          
          foreach ($mSumatoria as $cKey => $nValue) {
            if (round($mSumatoria[$cKey]['comvlrxx'],2) != 0) {
              $mSaldosFavor[$cKey] += $mSumatoria[$cKey]['comvlrxx'];
            }
          }

          foreach ($mSaldosFavor as $cKey => $cValue) {
            $vDocumento = explode("~", $cKey);

            $mClientes[$vDocumento[0]]['cliidxxx']  = $vDocumento[0];
            $mClientes[$vDocumento[0]]['salafavx'] += $mSaldosFavor[$cKey];
          }
        }

        if ($nSwitch == 0) {
          //Buscando movimiento contable por DO
          $vCatCon    = array(); //Categoria Concepto

          //Buscano conceptos de causaciones automaticas
          $qCto121  = "SELECT DISTINCT ";
          $qCto121 .= "pucidxxx, ";
          $qCto121 .= "ctoidxxx, ";
          $qCto121 .= "cacidxxx, "; //Categoria Concepto
          $qCto121 .= "\"NO\" AS ctoantxx, ";
          $qCto121 .= "\"SI\" AS ctopccxx, ";
          $qCto121 .= "\"NO\" AS ctoptaxg, "; //Tributos aduaneros en los egresos
          $qCto121 .= "\"NO\" AS ctoptaxl, "; //Tributos aduaneros en las cartas bancarias
          $qCto121 .= "\"NO\" AS fondoope  ";
          $qCto121 .= "FROM $cAlfa.fpar0121 ";
          $qCto121 .= "WHERE ";
          $qCto121 .= "regestxx = \"ACTIVO\"";
          $nQueryTimeStart = microtime(true); $xCto121  = mysql_query($qCto121,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
          // echo $qCto121."~".mysql_num_rows($xCto121)."<br>";
          //  f_Mensaje(__FILE__,__LINE__,$qCto121."~".mysql_num_rows($xCto121));

          $vError['LINEAERR'] = __LINE__;
          $vError['TIPOERRX'] = "ADVERTENCIA";
          $vError['DESERROR'] = mysql_num_rows($xCto121)."~".$qCto121;
          $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

          while($xRC121 = mysql_fetch_assoc($xCto121)) {
            $vCatCon["{$xRC121['ctoidxxx']}"] = $xRC121['cacidxxx'];
          }
          $xFree = mysql_free_result($xCto121);

          //Buscando conceptos de anticipos y pagos a terceros normales
          //Si en las condiciones de financicion Cliente NO  Aplica control de Financiación para TRIBUTOS ADUANEROS,
          //estos deben excluirse de esta busqueda
          $qCto119  = "SELECT DISTINCT ";
          $qCto119 .= "pucidxxx, ";
          $qCto119 .= "ctoidxxx, ";
          $qCto119 .= "cacidxxx, "; //Categoria Concepto
          $qCto119 .= "ctoantxx, ";
          $qCto119 .= "ctopccxx, ";
          $qCto119 .= "ctoptaxg, "; //Tributos aduaneros en los egresos
          $qCto119 .= "ctoptaxl, "; //Tributos aduaneros en las cartas bancarias
          $qCto119 .= "\"NO\" AS fondoope ";
          $qCto119 .= "FROM $cAlfa.fpar0119 ";
          $qCto119 .= "WHERE ";
          $qCto119 .= "($cAlfa.fpar0119.ctoantxx = \"SI\" OR $cAlfa.fpar0119.ctopccxx = \"SI\") AND ";
          $qCto119 .= "$cAlfa.fpar0119.regestxx = \"ACTIVO\"";
          $nQueryTimeStart = microtime(true); $xCto119  = mysql_query($qCto119,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
          //  f_Mensaje(__FILE__,__LINE__,$qCto119."~".mysql_num_rows($xCto119));
          // echo $qCto119."~".mysql_num_rows($xCto119)."<br>";

          $vError['LINEAERR'] = __LINE__;
          $vError['TIPOERRX'] = "ADVERTENCIA";
          $vError['DESERROR'] = mysql_num_rows($xCto119)."~".$qCto119;
          $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

          while($xRC119 = mysql_fetch_assoc($xCto119)) {
            $vCatCon["{$xRC119['ctoidxxx']}"] = $xRC119['cacidxxx'];
          }
          $xFree = mysql_free_result($xCto119);

          //Buscando cuentas de Anticipos a Proveedor y trayendo los anticipos a proveedor pendientes de pago
          if ($vSysStr['roldanlo_cuentas_anticipos_proveedor_financiacion_clientes'] != "") {
            $vPucIdAp = explode(",",$vSysStr['roldanlo_cuentas_anticipos_proveedor_financiacion_clientes']);
            $cPucIdAp = "\"".implode("\",\"", $vPucIdAp)."\"";

            $mSumatoria = array(); $vCuentasCxP = array();
            for ($iAno = $dAnioInicio; $iAno<=date('Y'); $iAno++) {

              $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01);

              // Consulta la cantidad de registros
              $qLoad  = "SELECT SQL_CALC_FOUND_ROWS ";
              $qLoad .= "teridxxx, sucidxxx, docidxxx, docsufxx, terid3xx, ";
              $qLoad .= "SUM(IF($cAlfa.fcod$iAno.commovxx=\"D\",IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx, $cAlfa.fcod$iAno.comvlrnf),IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx*-1, $cAlfa.fcod$iAno.comvlrnf*-1))) as saldoxxx ";
              $qLoad .= "FROM $cAlfa.fcod$iAno ";
              $qLoad .= "WHERE ";
              $qLoad .= "$cAlfa.fcod$iAno.pucidxxx IN ($cPucIdAp) AND ";
              $qLoad .= "$cAlfa.fcod$iAno.comfecxx BETWEEN \"{$vSysStr['financiero_ano_instalacion_modulo']}-01-01\" AND \"{$pvParametros['feccorte']}\" AND ";
              $qLoad .= "$cAlfa.fcod$iAno.regestxx = \"ACTIVO\" ";
              $qLoad .= "GROUP BY teridxxx, sucidxxx, docidxxx, docsufxx, terid3xx ";
              $nQueryTimeStart = microtime(true); $xLoad = mysql_query($qLoad,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);

              $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
              $xRNR = mysql_fetch_array($xNumRows);
              $nRegistros = $xRNR['FOUND_ROWS()'];
              mysql_free_result($xNumRows);

              // Se consultan los registros por bloques
              $nCanReg01 = 0;
              for ($i=0;$i<=$nRegistros;$i+=$nNumReg) {

                $qMovCon  = "SELECT ";
                $qMovCon .= "teridxxx, sucidxxx, docidxxx, docsufxx, terid3xx, ";
                $qMovCon .= "SUM(IF($cAlfa.fcod$iAno.commovxx=\"D\",IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx, $cAlfa.fcod$iAno.comvlrnf),IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx*-1, $cAlfa.fcod$iAno.comvlrnf*-1))) as saldoxxx ";
                $qMovCon .= "FROM $cAlfa.fcod$iAno ";
                $qMovCon .= "WHERE ";
                $qMovCon .= "$cAlfa.fcod$iAno.pucidxxx IN ($cPucIdAp) AND ";
                $qMovCon .= "$cAlfa.fcod$iAno.comfecxx BETWEEN \"{$vSysStr['financiero_ano_instalacion_modulo']}-01-01\" AND \"{$pvParametros['feccorte']}\" AND ";
                $qMovCon .= "$cAlfa.fcod$iAno.regestxx = \"ACTIVO\" ";
                $qMovCon .= "GROUP BY teridxxx, sucidxxx, docidxxx, docsufxx, terid3xx ";
                $qMovCon .= "LIMIT $i,$nNumReg; ";
                $nQueryTimeStart = microtime(true); $xMovCon  = mysql_query($qMovCon,$xConexion01);
                $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
                // if (mysql_num_rows($xMovCon) > 0) {
                //   echo "<br>CxP Anticipo Proveedores: <br>".mysql_num_rows($xMovCon)."~".$qMovCon."<br>";
                // }

                $vError['LINEAERR'] = __LINE__;
                $vError['TIPOERRX'] = "ADVERTENCIA";
                $vError['DESERROR'] = mysql_num_rows($xMovCon)."~".$qMovCon;
                $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

                while ($xRMC = mysql_fetch_assoc($xMovCon)) {
                  $nCanReg01++;
                  if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01); }
                  $cDocCru = "{$xRMC['teridxxx']}~{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}~{$xRMC['terid3xx']}";
                  $mSumatoria["$cDocCru"]['comvlrxx'] += $xRMC['saldoxxx'];
                  $mSumatoria["$cDocCru"]['comproba'][] = $xRMC;
                }
                $xFree = mysql_free_result($xMovCon);
              }
            }

            $vAntProxCli = array();

            foreach ($mSumatoria as $cKey => $cValue) {
              $vDocCru = explode("~",$cKey);
              $nEncontro = 0;
              if ($vDocCru[1] != "" && $vDocCru[2] != "" && $vDocCru[3] != "") {
                //Se asocio a un DO
                //Buscando el cliente del DO
                $qTramite = "SELECT ";
                $qTramite.= "cliidxxx ";
                $qTramite.= "FROM $cAlfa.sys00121 ";
                $qTramite.= "WHERE ";
                $qTramite.= "sucidxxx = \"{$vDocCru[1]}\" AND ";
                $qTramite.= "docidxxx = \"{$vDocCru[2]}\" AND ";
                $qTramite.= "docsufxx = \"{$vDocCru[3]}\" LIMIT 0,1 ";
                $nQueryTimeStart = microtime(true); $xTramite  = mysql_query($qTramite,$xConexion01);
                $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
                //  f_Mensaje(__FILE__,__LINE__,$qTramite."~".mysql_num_rows($xTramite));

                $vError['LINEAERR'] = __LINE__;
                $vError['TIPOERRX'] = "ADVERTENCIA";
                $vError['DESERROR'] = mysql_num_rows($xTramite)."~".$qTramite;
                $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

                $nCanReg01 = 0;
                if (mysql_num_rows($xTramite) > 0) {
                  $nEncontro = 1;
                  $vTramite = mysql_fetch_assoc($xTramite);

                  $nCanReg01++;
                  if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01); }

                  $nIncluirCliente = 0;
                  if ($pvParametros['cliidxxx'] != "") {
                    $nIncluirCliente = 1;
                    if ($pvParametros['cliidxxx'] == $vTramite['cliidxxx']) {
                      $nIncluirCliente = 0;
                    }
                  }

                  if ($nIncluirCliente == 0) {
                    $vAntProxCli["{$vTramite['cliidxxx']}"] += $mSumatoria[$cKey]['comvlrxx'];

                    if (in_array("{$vDocCru[1]}~{$vDocCru[2]}~{$vDocCru[3]}", $mClientes["{$vTramite['cliidxxx']}"]['tramites']) == false) {
                      $mClientes["{$vTramite['cliidxxx']}"]['tramites'][] = "{$vDocCru[1]}~{$vDocCru[2]}~{$vDocCru[3]}";
                    }
                  }
                }
                $xFree = mysql_free_result($xTramite); 
              }

              if ($nEncontro == 0) {
                if ($vDocCru[4] != "") {
                  //Se asocio a un cliente
                  $nIncluirCliente = 0;
                  if ($pvParametros['cliidxxx'] != "") {
                    $nIncluirCliente = 1;
                    if ($pvParametros['cliidxxx'] == $vDocCru[4]) {
                      $nIncluirCliente = 0;
                    }
                  }
                  if ($nIncluirCliente == 0) {
                    $vAntProxCli["{$vDocCru[4]}"] += $mSumatoria[$cKey]['comvlrxx'];
                  }
                } else {
                  //No se asocio, ni a un cliente, ni a un do, se carga al proveedor
                  $nIncluirCliente = 0;
                  if ($pvParametros['cliidxxx'] != "") {
                    $nIncluirCliente = 1;
                    if ($pvParametros['cliidxxx'] == $vDocCru[0]) {
                      $nIncluirCliente = 0;
                    }
                  }
                  if ($nIncluirCliente == 0) {
                    $vAntProxCli["{$vDocCru[0]}"] += $mSumatoria[$cKey]['comvlrxx'];
                  }
                }
              }
            }
          }

          //Incluyendo los clientes que no existen en el array de clientes
          foreach ($vAntProxCli as $cKey => $cValue) {
            if ($vAntProxCli[$cKey] != 0) {
              $mClientes["$cKey"]['antpronf'] = 0;
            }
          }

          //  $tFin = microtime(true);
          //  echo "Tiempo de ejecucion: ".bcsub($tFin, $tInicio, 4)."<br>";

          //Array para el control de las financiaciones manuales
          $vConCau = array();
          $cConCau = "";

          $vPCC = array(); //Pagos a terceros

          //Buscando todos los pagos a terceros y anticipos no facturados del cliente
          //Inicializando vector de pagos a terceros x Concepto
          //vecto de pagos a terceros por do y concepto
          $vPCCxCto = array();
          $mSumatoriaPCC = array();

          //Vector anticipos por do
          $vAntxDO = array();
          $vAntxDOAux = array();
          $mSumatoria = array();

          //Cuentas de pagos a terceros
          $vPucIdPcc = explode(",",$vSysStr['roldanlo_cuentas_pcc_financiacion_clientes']);
          $cPucIdPcc = "\"".implode("\",\"", $vPucIdPcc)."\"";

          //Cuentas de anticipos operativos
          $vPucIdAnt = explode(",",$vSysStr['roldanlo_cuentas_anticipos_operativos_financiacion_clientes']);
          $cPucIdAnt = "\"".implode("\",\"", $vPucIdAnt)."\"";

          //Cuentas de fondos operativos
          $vPucIdFon = explode(",",$vSysStr['roldanlo_cuentas_fondos_operativos_financiacion_clientes']);
          $cPucIdFon = "\"".implode("\",\"", $vPucIdFon)."\"";

          //Se busca desde el año anterior a la creacion del DO, hasta el año actual
          for ($iAno = $dAnioInicio; $iAno<=date('Y'); $iAno++) {

            $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01);

            //Buscando pagos a terceros, aplica la fecha de fincaciación
            //Debito suma, credito resta
            $qLoad  = "SELECT SQL_CALC_FOUND_ROWS ";
            $qLoad .= "teridxxx, sucidxxx, docidxxx, docsufxx, ctoidxxx, comfacxx, ";
            $qLoad .= "SUM(IF($cAlfa.fcod$iAno.commovxx=\"D\",IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx, $cAlfa.fcod$iAno.comvlrnf),IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx*-1, $cAlfa.fcod$iAno.comvlrnf*-1))) as comvlrxx ";
            $qLoad .= "FROM $cAlfa.fcod$iAno ";
            $qLoad .= "WHERE ";
            $qLoad .= "$cAlfa.fcod$iAno.pucidxxx IN ($cPucIdPcc) AND ";
            $qLoad .= "$cAlfa.fcod$iAno.comfecxx BETWEEN \"{$vSysStr['financiero_ano_instalacion_modulo']}-01-01\" AND \"{$pvParametros['feccorte']}\" AND ";
            $qLoad .= "(($cAlfa.fcod$iAno.comidxxx != \"F\" && $cAlfa.fcod$iAno.regestxx IN (\"ACTIVO\",\"PROVISIONAL\")) OR ($cAlfa.fcod$iAno.comidxxx = \"F\" && $cAlfa.fcod$iAno.regestxx = \"ACTIVO\")) ";
            $qLoad .= "GROUP BY teridxxx, sucidxxx, docidxxx, docsufxx, ctoidxxx, comfacxx ";
            $nQueryTimeStart = microtime(true); $xLoad  = mysql_query($qLoad,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);

            $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
            $xRNR = mysql_fetch_array($xNumRows);
            $nRegistros = $xRNR['FOUND_ROWS()'];
            mysql_free_result($xNumRows);

            // Se consultan los registros por bloques
            $nCanReg01 = 0;
            for ($i=0;$i<=$nRegistros;$i+=$nNumReg) {

              $qMovCon  = "SELECT ";
              $qMovCon .= "teridxxx, sucidxxx, docidxxx, docsufxx, ctoidxxx, comfacxx, ";
              $qMovCon .= "SUM(IF($cAlfa.fcod$iAno.commovxx=\"D\",IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx, $cAlfa.fcod$iAno.comvlrnf),IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx*-1, $cAlfa.fcod$iAno.comvlrnf*-1))) as comvlrxx ";
              $qMovCon .= "FROM $cAlfa.fcod$iAno ";
              $qMovCon .= "WHERE ";
              $qMovCon .= "$cAlfa.fcod$iAno.pucidxxx IN ($cPucIdPcc) AND ";
              $qMovCon .= "$cAlfa.fcod$iAno.comfecxx BETWEEN \"{$vSysStr['financiero_ano_instalacion_modulo']}-01-01\" AND \"{$pvParametros['feccorte']}\" AND ";
              $qMovCon .= "(($cAlfa.fcod$iAno.comidxxx != \"F\" && $cAlfa.fcod$iAno.regestxx IN (\"ACTIVO\",\"PROVISIONAL\")) OR ($cAlfa.fcod$iAno.comidxxx = \"F\" && $cAlfa.fcod$iAno.regestxx = \"ACTIVO\")) ";
              $qMovCon .= "GROUP BY teridxxx, sucidxxx, docidxxx, docsufxx, ctoidxxx, comfacxx ";
              $qMovCon .= "LIMIT $i,$nNumReg; ";
              $nQueryTimeStart = microtime(true); $xMovCon  = mysql_query($qMovCon,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
              // if (mysql_num_rows($xMovCon) > 0) {
                // echo "<br>PCC: <br>".mysql_num_rows($xMovCon)."~".$qMovCon."<br>";
              // }

              $vError['LINEAERR'] = __LINE__;
              $vError['TIPOERRX'] = "ADVERTENCIA";
              $vError['DESERROR'] = mysql_num_rows($xMovCon)."~".$qMovCon;
              $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

              while ($xRMC = mysql_fetch_assoc($xMovCon)) {
                $nCanReg01++;
                if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01); }
                $mSumatoriaPCC["{$xRMC['comfacxx']}"]['comvlrxx'] += $xRMC['comvlrxx'];
                $mSumatoriaPCC["{$xRMC['comfacxx']}"]['comproba']["{$xRMC['teridxxx']}"]["{$xRMC['sucidxxx']}~{$xRMC['docidxxx']}~{$xRMC['docsufxx']}~{$xRMC['ctoidxxx']}"][] = $xRMC;         
              }
              $xFree = mysql_free_result($xMovCon);
            }

            //Buscando anticipos operativos
            //Credito suma, debito resta
            $qLoad  = "SELECT SQL_CALC_FOUND_ROWS ";
            $qLoad .= "teridxxx, terid2xx, pucidxxx, ctoidxxx, sucidxxx, docidxxx, docsufxx, comfacxx, ";
            $qLoad .= "IF($cAlfa.fcod$iAno.commovxx=\"C\",IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx, $cAlfa.fcod$iAno.comvlrnf),IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx*-1, $cAlfa.fcod$iAno.comvlrnf*-1)) as comvlrxx ";
            $qLoad .= "FROM $cAlfa.fcod$iAno ";
            $qLoad .= "WHERE ";
            $qLoad .= "$cAlfa.fcod$iAno.pucidxxx IN ($cPucIdAnt) AND ";
            $qLoad .= "$cAlfa.fcod$iAno.comfecxx BETWEEN \"{$vSysStr['financiero_ano_instalacion_modulo']}-01-01\" AND \"{$pvParametros['feccorte']}\" AND ";
            $qLoad .= "(($cAlfa.fcod$iAno.comidxxx != \"F\" && $cAlfa.fcod$iAno.regestxx IN (\"ACTIVO\",\"PROVISIONAL\")) OR ($cAlfa.fcod$iAno.comidxxx = \"F\" && $cAlfa.fcod$iAno.regestxx = \"ACTIVO\")) ";
            $nQueryTimeStart = microtime(true); $xLoad  = mysql_query($qLoad,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);

            $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
            $xRNR = mysql_fetch_array($xNumRows);
            $nRegistros = $xRNR['FOUND_ROWS()'];
            mysql_free_result($xNumRows);

            // Se consultan los registros por bloques
            $nCanReg01 = 0;
            for ($i=0;$i<=$nRegistros;$i+=$nNumReg) {

              $qMovCon  = "SELECT ";
              $qMovCon .= "teridxxx, terid2xx, pucidxxx, ctoidxxx, sucidxxx, docidxxx, docsufxx, comfacxx, ";
              $qMovCon .= "IF($cAlfa.fcod$iAno.commovxx=\"C\",IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx, $cAlfa.fcod$iAno.comvlrnf),IF($cAlfa.fcod$iAno.puctipej=\"L\" OR $cAlfa.fcod$iAno.puctipej=\"\", $cAlfa.fcod$iAno.comvlrxx*-1, $cAlfa.fcod$iAno.comvlrnf*-1)) as comvlrxx ";
              $qMovCon .= "FROM $cAlfa.fcod$iAno ";
              $qMovCon .= "WHERE ";
              $qMovCon .= "$cAlfa.fcod$iAno.pucidxxx IN ($cPucIdAnt) AND ";
              $qMovCon .= "$cAlfa.fcod$iAno.comfecxx BETWEEN \"{$vSysStr['financiero_ano_instalacion_modulo']}-01-01\" AND \"{$pvParametros['feccorte']}\" AND ";
              $qMovCon .= "(($cAlfa.fcod$iAno.comidxxx != \"F\" && $cAlfa.fcod$iAno.regestxx IN (\"ACTIVO\",\"PROVISIONAL\")) OR ($cAlfa.fcod$iAno.comidxxx = \"F\" && $cAlfa.fcod$iAno.regestxx = \"ACTIVO\")) ";
              $qMovCon .= "LIMIT $i,$nNumReg; ";
              $nQueryTimeStart = microtime(true); $xMovCon  = mysql_query($qMovCon,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
              // if (mysql_num_rows($xMovCon) > 0) {
                // echo "<br>Anticipo: <br>".mysql_num_rows($xMovCon)."~".$qMovCon."<br>";
              // }

              $vError['LINEAERR'] = __LINE__;
              $vError['TIPOERRX'] = "ADVERTENCIA";
              $vError['DESERROR'] = mysql_num_rows($xMovCon)."~".$qMovCon;
              $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

              while ($xRMC = mysql_fetch_assoc($xMovCon)) {
                $nCanReg01++;
                if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01); }
                $mSumatoria["{$xRMC['comfacxx']}"]['comvlrxx'] += $xRMC['comvlrxx'];
                $mSumatoria["{$xRMC['comfacxx']}"]['comproba'][] = $xRMC;         
              }
              $xFree = mysql_free_result($xMovCon);
            }

            //Buscando fondos operativos
            //Credito suma, debito resta
            $qLoad  = "SELECT DISTINCT SQL_CALC_FOUND_ROWS ";
            $qLoad .= "teridxxx, pucidxxx, ctoidxxx, sucidxxx, docidxxx, docsufxx, comfacxx, ";
            $qLoad .= "SUM(IF(commovxx=\"C\",IF(puctipej=\"L\" OR puctipej=\"\", comvlrxx, comvlrnf),IF(puctipej=\"L\" OR puctipej=\"\", comvlrxx*-1, comvlrnf*-1))) AS comvlrxx ";
            $qLoad .= "FROM $cAlfa.fcod$iAno ";
            $qLoad .= "WHERE ";
            if ($pvParametros['cliidxxx'] != "") {
              $qLoad .= "teridxxx = \"{$pvParametros['cliidxxx']}\" AND ";
            }
            $qLoad .= "comidxxx != \"F\" AND ";
            $qLoad .= "(comfacxx = \"\" OR comfacxx LIKE \"%-P%\") AND ";
            $qLoad .= "comfecxx <= \"{$pvParametros['feccorte']}\" AND ";
            $qLoad .= "pucidxxx IN ($cPucIdFon) AND ";
            $qLoad .= "regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
            $qLoad .= "GROUP BY teridxxx, pucidxxx, ctoidxxx, sucidxxx, docidxxx, docsufxx, comfacxx ";
            $nQueryTimeStart = microtime(true); $xLoad  = mysql_query($qLoad,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);

            $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
            $xRNR = mysql_fetch_array($xNumRows);
            $nRegistros = $xRNR['FOUND_ROWS()'];
            mysql_free_result($xNumRows);

            // Se consultan los registros por bloques
            for ($i=0;$i<=$nRegistros;$i+=$nNumReg) {

              //Buscando fondos operativos
              //Credito suma, debito resta
              $qMovCon  = "SELECT DISTINCT ";
              $qMovCon .= "teridxxx, pucidxxx, ctoidxxx, sucidxxx, docidxxx, docsufxx, comfacxx, ";
              $qMovCon .= "SUM(IF(commovxx=\"C\",IF(puctipej=\"L\" OR puctipej=\"\", comvlrxx, comvlrnf),IF(puctipej=\"L\" OR puctipej=\"\", comvlrxx*-1, comvlrnf*-1))) AS comvlrxx ";
              $qMovCon .= "FROM $cAlfa.fcod$iAno ";
              $qMovCon .= "WHERE ";
              if ($pvParametros['cliidxxx'] != "") {
                $qMovCon .= "teridxxx = \"{$pvParametros['cliidxxx']}\" AND ";
              }
              $qMovCon .= "comidxxx != \"F\" AND ";
              $qMovCon .= "(comfacxx = \"\" OR comfacxx LIKE \"%-P%\") AND ";
              $qMovCon .= "comfecxx <= \"{$pvParametros['feccorte']}\" AND ";
              $qMovCon .= "pucidxxx IN ($cPucIdFon) AND ";
              $qMovCon .= "regestxx IN (\"ACTIVO\",\"PROVISIONAL\") ";
              $qMovCon .= "GROUP BY teridxxx, pucidxxx, ctoidxxx, sucidxxx, docidxxx, docsufxx, comfacxx ";
              $qMovCon .= "LIMIT $i,$nNumReg; ";
              $nQueryTimeStart = microtime(true); $xMovCon  = mysql_query($qMovCon,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);
              // if (mysql_num_rows($xMovCon) > 0) {
                // echo "<br>Fondos operativos: <br>".mysql_num_rows($xMovCon)."~".$qMovCon."<br>";
              // }

              $vError['LINEAERR'] = __LINE__;
              $vError['TIPOERRX'] = "ADVERTENCIA";
              $vError['DESERROR'] = mysql_num_rows($xMovCon)."~".$qMovCon;
              $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

              while ($xRMC = mysql_fetch_assoc($xMovCon)) {
                $nIncluir = 0;

                //Si el campo comfacxx es diferente de vacio se debe verificar que efectivamente corresponda a una proforma
                //Los pagos a terceros facturados como proforma deben tenerse en cuenta
                if ($xRMC['comfacxx'] != "") {
                  $vComfac = explode("-", $xRMC['comfacxx']);
                  if (substr($vComfac[2], 0, 1) != "P") {
                    $nIncluir = 1;
                  }
                }

                if ($nIncluir == 0) {
                  //Fondos operativos
                  $mClientes["{$xRMC['teridxxx']}"]['fonopexx'] += $xRMC['comvlrxx'];
                }          
              }
              $xFree = mysql_free_result($xMovCon);
            }
          } //for ($iAno = $vDatos[$i]['iniciomc']; $iAno<=date('Y'); $iAno++) {

          //Ordenando PCC por cliente y Tramite
          foreach ($mSumatoriaPCC as $cKey => $cValue) {
            if (round($mSumatoriaPCC[$cKey]['comvlrxx'], 2)  != 0) {
              foreach ($mSumatoriaPCC[$cKey]['comproba'] as $cKey01 => $cValue01) {
                foreach ($mSumatoriaPCC[$cKey]['comproba'][$cKey01] as $cKey02 => $cValue02) {
                  foreach ($mSumatoriaPCC[$cKey]['comproba'][$cKey01][$cKey02] as $cKey03 => $cValue03) {
                    $nIncluir = 0;
                    
                    if ($pvParametros['cliidxxx'] != "" && $pvParametros['cliidxxx'] != $cKey01) {
                      $nIncluir = 1;
                    }

                    if ($nIncluir == 0) {
                      if ($mSumatoriaPCC[$cKey]['comproba'][$cKey01][$cKey02][$cKey03]['comvlrxx'] != 0){
                        //Acumulando pagos a terceros x concepto, para buscar la financiacion manual y descontarla
                        $vPCCxCto[$cKey01][$cKey02]['totalxxx'] += $mSumatoriaPCC[$cKey]['comproba'][$cKey01][$cKey02][$cKey03]['comvlrxx'];
                        $vPCCxCto[$cKey01][$cKey02]['controlx'] += $mSumatoriaPCC[$cKey]['comproba'][$cKey01][$cKey02][$cKey03]['comvlrxx'];

                        //Inicializando en la matriz de clientes el valor de los pagos a terceros
                        $mClientes[$cKey01]['pccnfxxx'] = 0;

                        //Tramites
                        if (in_array("{$mSumatoriaPCC[$cKey]['comproba'][$cKey01][$cKey02][$cKey03]['sucidxxx']}~{$mSumatoriaPCC[$cKey]['comproba'][$cKey01][$cKey02][$cKey03]['docidxxx']}~{$mSumatoriaPCC[$cKey]['comproba'][$cKey01][$cKey02][$cKey03]['docsufxx']}", $mClientes[$cKey01]['tramites']) == false){
                        $mClientes[$cKey01]['tramites'][] = "{$mSumatoriaPCC[$cKey]['comproba'][$cKey01][$cKey02][$cKey03]['sucidxxx']}~{$mSumatoriaPCC[$cKey]['comproba'][$cKey01][$cKey02][$cKey03]['docidxxx']}~{$mSumatoriaPCC[$cKey]['comproba'][$cKey01][$cKey02][$cKey03]['docsufxx']}";
                        }
                      }
                    }
                  }
                }
              }
            }
          }

          //Ordenando Anticipos Operativos por cliente y Tramite
          foreach ($mSumatoria as $cKey => $cValue) {
            if (round($mSumatoria[$cKey]['comvlrxx'],2)  != 0) {
              foreach ($mSumatoria[$cKey]['comproba'] as $cKey01 => $cValue01) {
                $nIncluir = 0;
                //Si el teridxxx y el terid2xx son diferentes, el anticipo fue realizado por un tercero diferente al dueño del DO
                //y debe cargarse es al dueño del DO
                $cTerId = ($mSumatoria[$cKey]['comproba'][$cKey01]['teridxxx'] != $mSumatoria[$cKey]['comproba'][$cKey01]['terid2xx']) ? $mSumatoria[$cKey]['comproba'][$cKey01]['terid2xx'] : $mSumatoria[$cKey]['comproba'][$cKey01]['teridxxx'];

                if ($pvParametros['cliidxxx'] != "" && $pvParametros['cliidxxx'] != $cTerId) {
                  $nIncluir = 1;
                }
                //Anticipos x DO
                if ($nIncluir == 0) {

                    //Inicializando en la matriz de clientes el valor de los anticipos
                    $mClientes["$cTerId"]['anticipo'] = 0;

                    $vAntxDOAux["$cTerId"]["{$mSumatoria[$cKey]['comproba'][$cKey01]['sucidxxx']}~{$mSumatoria[$cKey]['comproba'][$cKey01]['docidxxx']}~{$mSumatoria[$cKey]['comproba'][$cKey01]['docsufxx']}"] += $mSumatoria[$cKey]['comproba'][$cKey01]['comvlrxx'];
                    $nSumAnt += $mSumatoria[$cKey]['comproba'][$cKey01]['comvlrxx'];
                }
              }
            }
          }

          foreach ($vAntxDOAux as $cTerId => $cValue) {
            foreach ($vAntxDOAux[$cTerId] as $cDo => $cValue01) {
              if (round($vAntxDOAux[$cTerId][$cDo], 2) != 0) {
                //Anticipos x DO
                $mClientes["$cTerId"]['antxxxxx'] += round($vAntxDOAux[$cTerId][$cDo], 2);
                $vAntxDO["$cTerId"]["$cDo"] += round($vAntxDOAux[$cTerId][$cDo], 2);
                //Tramites
                if (in_array("$cDo", $mClientes["$cTerId"]['tramites']) == false){
                  $mClientes["$cTerId"]['tramites'][] = "$cDo";
                }
              }
            }
          }
          //Fin Ordenando Anticipos Operativos por cliente y Tramite

          // echo "<pre>";
          // print_r($vAntxDO);
          // echo "</pre>";

          // echo "<pre>";
          // print_r($vPCCxCto);
          // echo "</pre>";
          foreach ($mClientes as $cKey => $cValue) {
            //Anticipos a Proveedor Cliente
            $mClientes[$cKey]['antpronf'] += $vAntProxCli[$cKey];

            //Los pagos a terceros estan agrupados por cliente y fecha
            //Ordenando por fecha de creacion de los pagos a terceros para asigar anticipo
            //del pago mas antiguo al mas reciente
            ksort($vPCCxCto[$cKey]);

            //Totalizando los anticipos, solo se suma el valor que cubre los pagos a terceros del DO
            //Si hay sobrantes, estos no se suman, porque si se hiciera estarian favoreciendo el cupo de un
            //DO que no tiene anticipos
            //Se parte de los pagos a terceros porque estos estan discriminados por concepto y es necesario
            //decontar el anticipo por cada concepto, para facilitar luego la asignación de cupo manual
            $nAnt = 0;
            foreach ($vPCCxCto[$cKey] as $cKeyPCC => $cValuePcc) {
              //pagos a terceros del cliente
              $mClientes[$cKey]['pccnfxxx'] += $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];
            }        
          } ## foreach ($mClientes as $cKey => $cValue) { ##
        }
      }

      // echo "<pre>";
      // print_r($vPCCxCto);
      // echo "</pre>";

      // echo "<pre>";
      // print_r($vAntxDO);
      // echo "</pre>";

      // echo "<pre>";
      // print_r($mFinManxDO);
      // echo "</pre>";

      $mPccyAntxDo = array();
      foreach ($mClientes as $cKey => $cValue) {
          if ($mClientes[$cKey]['cliidxxx'] != "") {
            foreach ($vPCCxCto[$cKey] as $cKeyPCC => $cValuePcc) {
              //Extrayendo el key para los anticpos, que es sucursal~do~sufijo
              $cKeyAux = explode("~", $cKeyPCC);
              $cKeyDo  = $cKeyAux[0]."~".$cKeyAux[1]."~".$cKeyAux[2];
              $mPccyAntxDo[$cKeyDo]['pagoster'] += $vPCCxCto[$cKey][$cKeyPCC]['totalxxx'];
            }

            foreach ($vAntxDO[$cKey] as $cKeyAnt => $cValueAnt) {
              $mPccyAntxDo[$cKeyAnt]['anticipo'] += $vAntxDO[$cKey][$cKeyAnt];
            }
            
            foreach ($mFinManxDO[$cKey] as $cKeyFm => $cValueFm) {
              $mPccyAntxDo[$cKeyFm]['finmanua'] += $mFinManxDO[$cKey][$cKeyFm];
            }
          }
      }

      //completando datos de pagos a terceros y anticipos por DO
      foreach ($mClientes as $cKey => $cValue) {
        if ($mClientes[$cKey]['cliidxxx'] != "") {
          for($nD=0; $nD<count($mClientes[$cKey]['tramites']);$nD++) {
            $cKeyDo = $mClientes[$cKey]['tramites'][$nD];
            $mClientes[$cKey]['tramites'][$nD] = $mClientes[$cKey]['tramites'][$nD]."~".($mPccyAntxDo[$cKeyDo]['pagoster']+0)."~".($mPccyAntxDo[$cKeyDo]['anticipo']+0)."~".($mPccyAntxDo[$cKeyDo]['finmanua']+0);
          } 
        }
      }

      // echo "<pre>";
      // print_r($mClientes);
      // echo "</pre>";

      if ($nSwitch == 0) {

        $qInsCab  = "INSERT INTO $cAlfa.{$pvParametros['tablaxxx']} ";
        $qInsCab .= "(cliidxxx,";
        $qInsCab .= "clinomxx,";
        $qInsCab .= "regestxx,";
        $qInsCab .= "fpccupox,";
        $qInsCab .= "fpcsobgi,";        
        $qInsCab .= "sfmvalxx,";
        $qInsCab .= "sfmvalac,";
        $qInsCab .= "carpccxx,";
        $qInsCab .= "caripxxx,";
        $qInsCab .= "comsaldo,";
        $qInsCab .= "carvenxx,";
        $qInsCab .= "salafavx,";
        $qInsCab .= "pccnfxxx,";
        $qInsCab .= "antpronf,";
        $qInsCab .= "antxxxxx,";
        $qInsCab .= "fonopexx,";
        $qInsCab .= "tramites) VALUE ";

        $vError['LINEAERR'] = __LINE__;
        $vError['TIPOERRX'] = "ADVERTENCIA";
        $vError['DESERROR'] = $qInsCab;
        $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

        foreach ($mClientes as $cKey => $cValue) {
          if ($mClientes[$cKey]['cliidxxx'] != "") {
            $qInsert .= "(\"".$mClientes[$cKey]['cliidxxx']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['clinomxx']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['regestxx']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['fpccupox']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['fpcsobgi']."\",";            
            $qInsert .= "\"".$mClientes[$cKey]['sfmvalxx']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['sfmvalac']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['carpccxx']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['caripxxx']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['comsaldo']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['carvenxx']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['salafavx']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['pccnfxxx']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['antpronf']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['antxxxxx']."\",";
            $qInsert .= "\"".$mClientes[$cKey]['fonopexx']."\",";
            $qInsert .= "\"".implode("|",$mClientes[$cKey]['tramites'])."\"),";

            $nCanReg++;
            if (($nCanReg % _NUMREG_) == 0) {
              $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01);

              $qInsert = $qInsCab.substr($qInsert, 0, -1);
              $nQueryTimeStart = microtime(true); $xInsDet = mysql_query($qInsert,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);

              if(!$xInsDet) {
                $nError = 1;
              }
              $vError['LINEAERR'] = __LINE__;
              $vError['TIPOERRX'] = "ADVERTENCIA";
              $vError['DESERROR'] = mysql_error($xConexion01)."~".mysql_affected_rows($xConexion01)."~".str_replace('\"','"',$qInsert);
              $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

              $qInsert = "";
              $xFree = mysql_free_result($xInsDet); 
            }
          }
        }

        if ($nError == 0 && $qInsert != "") {
          $xConexion01 = $objTablasTemporales->fnReiniciarConexionDBFinanciacionCliente($xConexion01);

          $qInsert = $qInsCab.substr($qInsert, 0, -1);
          $nQueryTimeStart = microtime(true); $xInsDet = mysql_query($qInsert,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objTablasTemporales->fnMysqlQueryInfo($xConexion01,$nQueryTime);

          $vError['LINEAERR'] = __LINE__;
          $vError['TIPOERRX'] = "ADVERTENCIA";
          $vError['DESERROR'] = mysql_error($xConexion01)."~".mysql_affected_rows($xConexion01)."~".str_replace('\"','"',$qInsert);
          $objTablasTemporales->fnGuardarErrorFinanciacionCliente($vError);

          if(!$xInsDet) {
            $nError = 1;
          }
          $xFree = mysql_free_result($xInsDet); 
        }

        if ($nError == 1) {
          $nSwitch = 1;
          $mReturn[count($mReturn)] = "Error al Procesar Clientes.";
        }
      }
    } catch (\Exception $e) {
      $nSwitch = 1;
      $mReturn[count($mReturn)] = $e->getMessage() . "\n\nTrace:\n" . $e->getTraceAsString();
    }

    // echo "<pre>";
    // print_r($mReturn);
    // echo "</pre>";

    if( $nSwitch == 0 ){
      $mReturn[0] = "true";
    }else{
      $mReturn[0] = "false";
    }
    return $mReturn;
  } ## function fnRepoteFinanciacionCliente($pvParametros) { ##
} ## class cFinanciacionCliente { ##

class cEstructurasFinanciacionCliente {

  /**
  * Metodo que se encarga de Crear las Estructuras de las Tablas
  */
  function fnCrearEstructurasFinanciacionCliente($pParametros){
    global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

   /**
    * Recibe como Parametro un vector con las siguientes posiciones:
    * $pParametros['TIPOESTU'] //TIPO DE ESTRUCTURA
    */

   /**
    * Variable para saber si hay o no errores de validacion.
    * @var number
    */
    $nSwitch = 0;

   /**
    * Matriz para Retornar Valores
    */
    $mReturn = array();

   /**
    * Reservando Primera Posición para retorna true o false
    * Reservando Segunda Posición para el nombre de la tabla
    */
    $mReturn[0] = "";
    $mReturn[1] = "";

   /**
    * Llamando Metodo que hace conexion
    */
    $mReturnConexionTM = $this->fnConectarDBFinanciacionCliente();

    if($mReturnConexionTM[0] == "true"){
      $xConexionTM = $mReturnConexionTM[1];
    }else{
      $nSwitch = 1;
      for($nR=1;$nR<count($mReturnConexionTM);$nR++){
        $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
      }
    }

   /**
    * Borrando tablas antiguas
    */
    //$this->fnBorrarEstructurasFinanciacionCliente();

   /**
    * Random para Nombre de la Tabla
    */
    $cTabCar  = mt_rand(1000000000, 9999999999);

    switch($pParametros['TIPOESTU']){
      case "SALDOFINANCIACION":
        $cTabla = "memsalfn".$cTabCar;

        $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
        $qNewTab .= "lineaidx INT(11)     NOT NULL AUTO_INCREMENT, "; // Id Autoincremental
        $qNewTab .= "cliidxxx VARCHAR(20)  NOT NULL, "; // Cliente
        $qNewTab .= "clinomxx VARCHAR(255)  NOT NULL, "; // Nombre cliente
        $qNewTab .= "tipfincl VARCHAR(20)  NOT NULL, "; // Tipo Financiacion
        $qNewTab .= "salfincl DECIMAL(15,2) NOT NULL, "; // Saldo Financiacion
        $qNewTab .= "tramites TEXT NOT NULL, "; // Saldo Financiacion
        $qNewTab .= "PRIMARY KEY (lineaidx)) ENGINE=MyISAM ";
        $xNewTab = mysql_query($qNewTab,$xConexionTM);
        // f_Mensaje(__FILE__,__LINE__,$qNewTab);
        if(!$xNewTab) {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal para Generar Reporte Financiaciones Clientes, por Favor Informar a OpenTecnologia S.A. ";
        }
      break;
      case "FINANCIACIONCLIENTES":
      case "FINANCIACIONCLIENTESREPORTE":
        if ($pParametros['TIPOESTU'] == "FINANCIACIONCLIENTESREPORTE"){
          $cTabla = "memfincl".$cTabCar;
        }else{
          $cTabla = "memfincl";
        }
        $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
        $qNewTab .= "lineaidx INT(11)       NOT NULL AUTO_INCREMENT, "; // Id Autoincremental
        $qNewTab .= "cliidxxx VARCHAR(100)  NOT NULL, "; // Cliente
        $qNewTab .= "clinomxx TEXT          NOT NULL, "; // Nombre cliente
        $qNewTab .= "regestxx VARCHAR(20)   NOT NULL, "; // Cliente
        $qNewTab .= "docidxxx TEXT          NOT NULL, "; // DO's
        $qNewTab .= "fpccupox DECIMAL(15,2) NOT NULL, "; // Cupo por cliente
        $qNewTab .= "fpcsobgi DECIMAL(15,2) NOT NULL, "; // Sobregiro por cliente
        $qNewTab .= "sfmvalxx DECIMAL(15,2) NOT NULL, "; // Historico Solicitud Manual
        $qNewTab .= "sfmvalac DECIMAL(15,2) NOT NULL, "; // Cupo Autorizado Manual
        $qNewTab .= "carpccxx DECIMAL(15,2) NOT NULL, "; // Cartera PCC
        $qNewTab .= "caripxxx DECIMAL(15,2) NOT NULL, "; // Cartera IP
        $qNewTab .= "comsaldo DECIMAL(15,2) NOT NULL, "; // Cartera total vencida y no vencida
        $qNewTab .= "carvenxx DECIMAL(15,2) NOT NULL, "; // Cartera Vencida Total
        $qNewTab .= "salafavx DECIMAL(15,2) NOT NULL, "; // Saldo a favor
        $qNewTab .= "pccnfxxx DECIMAL(15,2) NOT NULL, "; // PCC NF
        $qNewTab .= "antpronf DECIMAL(15,2) NOT NULL, "; // Anticipos a Proveedores no facturados
        $qNewTab .= "antxxxxx DECIMAL(15,2) NOT NULL, "; // Anticipos
        $qNewTab .= "fonopexx DECIMAL(15,2) NOT NULL, "; // Fondos Operativos
        $qNewTab .= "tramites TEXT NOT NULL, ";          // Saldo Financiacion
        $qNewTab .= "PRIMARY KEY (lineaidx)) ENGINE=MyISAM ";
        $xNewTab = mysql_query($qNewTab,$xConexionTM);
        // f_Mensaje(__FILE__,__LINE__,$qNewTab);
        if(!$xNewTab) {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal para Generar Reporte Financiaciones Clientes, por Favor Informar a OpenTecnologia S.A. ";
        }
      break;
      case "ERRORES":
        $cTabla = "memerror".$cTabCar;

        $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
        $qNewTab .= "LINEAIDX INT(11)     NOT NULL AUTO_INCREMENT, "; //LINEA
        $qNewTab .= "LINEAERR VARCHAR(10) NOT NULL, ";                //LINEA DEL ARCHIVO
        $qNewTab .= "TIPOERRX VARCHAR(20) NOT NULL, ";                //TIPO DE ERROR
        $qNewTab .= "DESERROR LONGTEXT    NOT NULL, ";                //DESCRIPCION DEL ERROR
        $qNewTab .= "PRIMARY KEY (LINEAIDX)) ENGINE=MyISAM ";
        $xNewTab  = mysql_query($qNewTab,$xConexionTM);
        //f_Mensaje(__FILE__,__LINE__,$qNewTab);

        if(!$xNewTab) {
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal de Errores, por Favor Informar a OpenTecnologia S.A. ";
        }
      break;
      default:
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "No se Recibio Tipo de Estructura a Crear, por Favor Informar a OpenTecnologia S.A.";
      break;
    }

    if($nSwitch == 0){
      $mReturn[0] = "true";
      $mReturn[1] = $cTabla;
    }else{
      $mReturn[0] = "false";
    }
    return $mReturn;
  } ## function fnCrearEstructurasFinanciacionCliente($pParametros){ ##

  function fnBorrarEstructurasFinanciacionCliente() {
    global $xConexion01; global $cAlfa; global $vSysStr; global $kUser;

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
    * Llamando Metodo que hace conexion
    */
    $mReturnConexionTM = $this->fnConectarDBFinanciacionCliente();
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
  }##function fnBorrarEstructurasFinanciacionCliente() {##

  /**
  * Metodo que realiza la conexion
  */
  function fnConectarDBFinanciacionCliente(){
    global $cAlfa;

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

    $xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion.");
    if($xConexion99){
      $nSwitch = 0;
    }else{
      $nSwitch = 1;
      $mReturn[count($mReturn)] = "El Sistema no Logro Conexion.";
    }

    if($nSwitch == 0){
      $mReturn[0] = "true";
      $mReturn[1] = $xConexion99;
    }else{
      $mReturn[0] = "false";
    }
    return $mReturn;
  }##function fnConectarDB(){##

  /**
  * Metodo que realiza el reinicio de la conexion
  */
  function fnReiniciarConexionDBFinanciacionCliente($pConexion){
    global $cHost;  global $cUserHost;  global $cPassHost;

    // echo "<br>Reconectando...";
    mysql_close($pConexion);
    if($cHost != "" && $cUserHost != "" && $cPassHost != ""){
      $xConexion01 = mysql_connect($cHost,$cUserHost,$cPassHost,TRUE);
    }else{
      $xConexion01 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT,TRUE);
    }
    return $xConexion01;
  }##function fnReiniciarConexionDBFinanciacionCliente(){##

  /**
  * Metodo que se encarga de Guardar los Errores Generados por los Metodos de Interfaces
  */

  function fnGuardarErrorFinanciacionCliente($pArrayParametros){
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
     * Variables para reemplazar caracteres especiales
     * @var array
     */
    $cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
    $cReempl = array('\"',"\'"," "," "," "," ");

    if($pArrayParametros['TABLAERR'] != ""){

      $qInsert  = "INSERT INTO $cAlfa.{$pArrayParametros['TABLAERR']} (LINEAERR, TIPOERRX, DESERROR) VALUES ";
      $qInsert .= "(\"{$pArrayParametros['LINEAERR']}\",";
      $qInsert .= "\"{$pArrayParametros['TIPOERRX'] }\",";
      $qInsert .= "\"".str_replace($cBuscar,$cReempl,$pArrayParametros['DESERROR'])."\")";
      mysql_query($qInsert,$xConexion01);
      // echo "<br>".$qInsert;
    }
  }##function fnGuardarErrorFinanciacionCliente($pParametros){##

  ## Metodo para capturar la informacion del motor de DB asosciada al query
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
      $cIP  = $_SERVER['HTTP_CLIENT_IP'];
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

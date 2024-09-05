<?php
  /**
   * Graba Matriz de Insumos Facturable.
   * --- Descripcion: Permite Guardar en la tabla Matriz de Insumos Facturable un nuevo registro.
   * @author Diego Fernando Cortes Rojas <diego.cortes@openits.co>
   * @package opencomex
   * @version 001
   */
  include('../../../../../financiero/libs/php/utility.php');
  include("../../../../libs/php/utimifxx.php");
  include("../../../../../config/config.php");

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  /**
   * Switch para Vericar la Validacion de Datos.
   * 
   * @var int
   */
  $nSwitch = 0;

  /**
   * Almacena los errores generados en el proceso.
   * 
   * @var string
   */
  $cMsj = "\n";

  /**
   * Año actual del sistema.
   * 
   * @var string
   */
  $cPerAno = date('Y');

  /**
   * Mes actual del sistema.
   * 
   * @var string
   */
  $cPerMes = date('m');

  /**
   * Se instancia la clase de Matriz de Insumos Facturables
   */
  $ObjcMatrizInsumosFacturables = new cMatrizInsumosFacturables();

  /**
   * Instanciando Objeto para la creacion de las tablas temporales.
   */
  $objTablasAnualizadas          = new cEstructurasTablasAnualizadasMIF();
  $mReturnCrearTablasAnualizadas = $objTablasAnualizadas->fnCrearTablasAnualizadas();
  if($mReturnCrearTablasAnualizadas[0] == "false"){
    $nSwitch = 1;
    for($nR=1;$nR<count($mReturnCrearTablasAnualizadas);$nR++){
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= $mReturnCrearTablasAnualizadas[$nR] . "\n";
    }
  }

  $cPerAno = ($_COOKIE['kModo'] == "NUEVO") ? $cPerAno : substr($_POST['dFecCre'],0,4);
  $cPerAno = ($_COOKIE['kModo'] == "ADDMOV") ? $cAnio : $cPerAno;
  // Inicio de la Validacion
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
    case "EDITAR":
      // Validando que el Prefijo no sea vacio.
      if ($_POST['cComPre'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Prefijo no puede ser vacio.\n";
      } else {
        // Validando que el prefijo exista.
        if ($_COOKIE['kModo'] == "NUEVO") {
          $qComprobante  = "SELECT ";
          $qComprobante .= "comidxxx, ";
          $qComprobante .= "comtcoxx ";
          $qComprobante .= "FROM $cAlfa.lpar0117 ";
          $qComprobante .= "WHERE ";
          $qComprobante .= "$cAlfa.lpar0117.comidxxx = \"{$_POST['cComId']}\" AND ";
          $qComprobante .= "$cAlfa.lpar0117.comprexx = \"{$_POST['cComPre']}\" LIMIT 0,1 ";
          $xComprobante  = f_MySql("SELECT","",$qComprobante,$xConexion01,"");
          if(mysql_num_rows($xComprobante) < 1){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Prefijo [{$_POST['cComPre']}] no existe.\n";
          }
        }
      }

      // Validando el No M.I.
      if ($_COOKIE['kModo'] == "NUEVO") {
        if ($_POST['cComCsc'] != "") {
          if (!preg_match('/^[0-9]+$/', $_POST['cComCsc'])) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El No M.I.F [{$_POST['cComCsc']}] debe ser numerico.\n";
          } else {
            if (strlen($_POST['cComCsc']) > 6) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El No M.I.F [{$_POST['cComCsc']}] no puede ser mayor a 6 caracteres.\n";
            }
          }
        } elseif ($_POST['cComCsc'] == "" && $_POST['cComTCo'] == "MANUAL") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El No M.I.F no puede ser vacio.\n";
        }
      }

      // Validando que el Nit no sea vacio.
      if ($_POST['cCliId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Nit no puede ser vacio.\n";
      } else {
        // Validando que el Nit exista.
        if ($_COOKIE['kModo'] == "NUEVO") {
          $qCliDat  = "SELECT * ";
          $qCliDat .= "FROM $cAlfa.lpar0150 ";
          $qCliDat .= "WHERE ";
          $qCliDat .= "$cAlfa.lpar0150.cliidxxx = \"{$_POST['cCliId']}\" LIMIT 0,1 ";
          $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
          if(mysql_num_rows($xCliDat) < 1){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Nit del cliente [{$_POST['cCliId']}] no existe.\n";
          }
        }
      }

      // Validando el Deposito
      // Validando que el Deposito no sea vacio.
      if ($_POST['cDepNum'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Deposito no puede ser vacio.\n";
      } else {
        // Validando que el Deposito exista.
        if ($_COOKIE['kModo'] == "NUEVO") {
          $qDepDat  = "SELECT * ";
          $qDepDat .= "FROM $cAlfa.lpar0155 ";
          $qDepDat .= "WHERE ";
          $qDepDat .= "$cAlfa.lpar0155.depnumxx = \"{$_POST['cDepNum']}\" LIMIT 0,1 ";
          $xDepDat  = f_MySql("SELECT","",$qDepDat,$xConexion01,"");
          if(mysql_num_rows($xDepDat) < 1){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Deposito [{$_POST['cDepNum']}] no existe.\n";
          }
        }
      }

      // Validando Fechas
      // Validando que la Fecha Desde no sea vacia.
      if ($_POST['dDesde'] == "" || $_POST['dDesde'] == "0000-00-00") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Se debe seleccionar una Fecha Desde.\n";
      }
      // Validando que la Fecha Hasta no sea vacia.
      if ($_POST['dHasta'] == "" || $_POST['dHasta'] == "0000-00-00") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Se debe seleccionar una Fecha Hasta.\n";
      }
      // Validando la fecha desde, que no sea mayor que la fecha hasta.
      if ($_POST['dDesde'] > $_POST['dHasta']) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La fecha Desde no puede ser mayor a la fecha hasta.\n";
      }

      // Validando que el rango de fechas seleccionado corresponda con el periodo del Deposito
      $dFecHasta = strtotime($_POST['dHasta']);
      $dFecDesde = strtotime($_POST['dDesde']);
      $nDiffDias = floor((($dFecHasta - $dFecDesde) / (60 * 60 * 24)) + 1); // Se le suma 1 para incluir el dia de la fecha desde

      $nErrorPeriodo = 0;
      switch ($_POST['cPerFacDes']) {
        case 'DIARIA':
          if ($nDiffDias < 1 || $nDiffDias > 31) {
            $nErrorPeriodo = 1;
          }
        break;
        case 'DECADAL':
          if (10 != $nDiffDias) {
            $nErrorPeriodo = 1;
          }
        break;
        case 'QUINCENAL':
          if ($nDiffDias < 13 || $nDiffDias > 16) {
            $nErrorPeriodo = 1;
          }
        break;
        case 'MENSUAL':
          if ($nDiffDias < 28 || $nDiffDias > 31) {
            $nErrorPeriodo = 1;
          }
        break;
      }
      
      if ($nErrorPeriodo == 1) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El rango de fechas seleccionado no corresponde a la Periodicidad asignada al Deposito.\n";
      }

      // Validaciones nuevo Periodo
      // Consultado Periodo para aperturar uno nuevo.
      $qCscDat  = "SELECT ";
      $qCscDat .= "comidxxx, ";
      $qCscDat .= "comcodxx, ";
      $qCscDat .= "comcscxx, ";
      $qCscDat .= "peranoxx, ";
      $qCscDat .= "permesxx ";
      $qCscDat .= "FROM $cAlfa.lpar0122 ";
      $qCscDat .= "WHERE ";
      $qCscDat .= "comidxxx = \"{$_POST['cComId']}\" AND ";
      $qCscDat .= "comcodxx = \"{$_POST['cComCod']}\" ";
      $qCscDat .= "ORDER BY ABS(peranoxx) DESC, ";
      $qCscDat .= "ABS(permesxx) DESC LIMIT 0,1 ";
      $xCscDat = f_MySql("SELECT","",$qCscDat,$xConexion01,"");
      $vCscDat = mysql_fetch_array($xCscDat);

      // Apertura nuevo periodo si el año y mes son distintos al actual.
      if ($vCscDat['peranoxx'] != date('Y') || $vCscDat['permesxx'] != date('m')) {

        $pArrayDatos = array();
        $pArrayDatos['comidxxx'] = $_POST['cComId'];
        $pArrayDatos['comcodxx'] = $_POST['cComCod'];
        $pArrayDatos['comprexx'] = $_POST['cComPre'];
        $pArrayDatos['peranoxx'] = $vCscDat['peranoxx'];
        $pArrayDatos['permesxx'] = $vCscDat['permesxx'];

        $respuesta = $ObjcMatrizInsumosFacturables->fnAperturaCierrePeriodoAutomatico($pArrayDatos);
        if ($respuesta[0] == "false") {
          for ($i=1; $i <= count($respuesta); $i++) { 
            if ($respuesta[$i] != "") {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= $respuesta[$i]."\n";
            }
          }
        }
       
      }
      // Fin Validaciones nuevo Periodo

      // Inicio Control de Consecutivos
      if ($_COOKIE['kModo'] == "NUEVO") {
        $cComCsc  = "";
        $cComCsc2 = "";

        $qComCsc  = "SELECT ";
        $qComCsc .= "peranoxx, ";
        $qComCsc .= "permesxx, ";
        $qComCsc .= "comcscxx, ";
        $qComCsc .= "comcsc2x ";
        $qComCsc .= "FROM $cAlfa.lpar0122 ";
        $qComCsc .= "WHERE ";
        $qComCsc .= "comidxxx = \"{$_POST['cComId']}\"  AND ";
        $qComCsc .= "comcodxx = \"{$_POST['cComCod']}\" AND ";
        $qComCsc .= "peranoxx = \"$cPerAno\" AND ";
        $qComCsc .= "permesxx = \"$cPerMes\" AND ";
        $qComCsc .= "regestxx = \"ABIERTO\" LIMIT 0,1";
        $xComCsc  = f_MySql("SELECT","",$qComCsc,$xConexion01,"");
        //f_Mensaje(__FILE__,__LINE__,$qComCsc." ~ ".mysql_num_rows($xComCsc));

        if ($_POST['cComCod'] != "") {
          if (mysql_num_rows($xComCsc) > 0) {
            $vComCsc  = mysql_fetch_array($xComCsc);
            $cComCsc  = $vComCsc['comcscxx'];
            $cComCsc2 = $vComCsc['comcsc2x'];

            // Se define consecutivo uno para la tabla lmcaYYYY
            $nComCscMif = "";
            switch ($_POST['cComTCo']) {
              case 'MANUAL':
                $nComCscMif = date('y').str_pad($_POST['cComCsc'],6,'0',STR_PAD_LEFT);
              break;
              case 'AUTOMATICO':
                switch ($_POST['cComCco']) {
                  case 'INDEFINIDO':
                    $nComCscMif = date('y').str_pad($vComCsc['comcscxx'],6,'0',STR_PAD_LEFT);
                  break;
                  case 'MENSUAL':
                    $nComCscMif = date('y').date('m').str_pad($vComCsc['comcscxx'],4,'0',STR_PAD_LEFT);
                  break;
                  case 'ANUAL':
                    $nComCscMif = date('y').str_pad($vComCsc['comcscxx'],6,'0',STR_PAD_LEFT);
                  break;
                }
              break;
            }
            $_POST['cComCsc'] = $nComCscMif;

            // Valida el tipo de consecutivo dos del comprobante
            switch ($_POST['cComTCo']) {
              case "MANUAL":
                $_POST['cComCsc2'] = ($vComCsc['peranoxx'].$vComCsc['permesxx'].str_pad($vComCsc['comcsc2x'],4,"0",STR_PAD_LEFT));
              break;
              case "AUTOMATICO": 
                switch ($_POST['cComCco']) {
                  case "MENSUAL":
                    $_POST['cComCsc2'] = ($vComCsc['peranoxx'].$vComCsc['permesxx'].str_pad($vComCsc['comcsc2x'],4,"0",STR_PAD_LEFT));
                  break;
                  case "ANUAL":
                    $_POST['cComCsc2'] = ($vComCsc['peranoxx'].$vComCsc['permesxx'].str_pad($vComCsc['comcsc2x'],4,"0",STR_PAD_LEFT));
                  break;
                  case "INDEFINIDO":
                    $_POST['cComCsc2'] = ($vComCsc['peranoxx'].$vComCsc['permesxx'].str_pad($vComCsc['comcsc2x'],4,"0",STR_PAD_LEFT));
                  break;
                }
              break;
            }
          } else {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "No se Encontro Consecutivo para el Comprobante.\n";
          }
        }

        // Valida si el documento ya existe
        $qValCom  = "SELECT * ";
        $qValCom .= "FROM $cAlfa.lmca$cPerAno ";
        $qValCom .= "WHERE ";
        $qValCom .= "comidxxx = \"{$_POST['cComId']}\"  AND ";
        $qValCom .= "comcodxx = \"{$_POST['cComCod']}\" AND ";
        $qValCom .= "comcscxx = \"{$_POST['cComCsc']}\" AND ";
        $qValCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xValCom  = f_MySql("SELECT","",$qValCom,$xConexion01,"");
        if (mysql_num_rows($xValCom) > 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}] no se Puede Grabar porque ya Existe.\n";
        } else {
          $qValCom  = "SELECT * ";
          $qValCom .= "FROM $cAlfa.lmca$cPerAno ";
          $qValCom .= "WHERE ";
          $qValCom .= "comidxxx = \"{$_POST['cComId']}\"  AND ";
          $qValCom .= "comprexx = \"{$_POST['cComPre']}\" AND ";
          $qValCom .= "comcscxx = \"{$_POST['cComCsc']}\" AND ";
          $qValCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
          $xValCom  = f_MySql("SELECT","",$qValCom,$xConexion01,"");
          if (mysql_num_rows($xValCom) > 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComPre']}-{$_POST['cComCsc']}] no se Puede Grabar porque ya Existe.\n";
          } else {
            $qValCom  = "SELECT * ";
            $qValCom .= "FROM $cAlfa.lmca$cPerAno ";
            $qValCom .= "WHERE ";
            $qValCom .= "comidxxx = \"{$_POST['cComId']}\"  AND ";
            $qValCom .= "comcodxx = \"{$_POST['cComCod']}\" AND ";
            $qValCom .= "comcsc2x = \"{$_POST['cComCsc2']}\" LIMIT 0,1";
            $xValCom  = f_MySql("SELECT","",$qValCom,$xConexion01,"");
            if (mysql_num_rows($xValCom) > 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc2']}] no se Puede Grabar porque ya Existe.\n";
            }
          }
        }
      }
      // Fin Valida si el documento ya existe
    break;
    case "ADDMOV":
      // Consulto la matriz de insumo facturable
      $vMatriz  = array();
      $qMatriz  = "SELECT ";
      $qMatriz .= "mifidxxx, ";
      $qMatriz .= "miforixx, ";
      $qMatriz .= "regestxx ";
      $qMatriz .= "FROM $cAlfa.lmca$cPerAno ";
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

        if ($vMatriz['miforixx'] != "MANUAL" && $vMatriz['miforixx'] != "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La actualizacion del Movimiento de la M.I.F no fue realizado de manera manual, no es posible continuar con el proceso.\n";
        }

        // Validando la cantidad por fechas
        for ($i=0; $i < $_POST['nCantSub']; $i++) { 
          // Valida si se permite cambiar la cantidad
          if ($_POST['cMifdMod'.$i] != "SI" && $_POST['nCantOcul'.$i] != "" && $_POST['nCantOcul'.$i] != $_POST['nCant'.$i]) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Para la fecha [".$_POST['cMifdFec'.$i]."] ya se encuentra una cantidad digitada.\n";
          }

          // Valida que no exceda en 7 dias la fecha actual del sistema
          if ($_POST['cMifdMod'.$i] != "SI" && $_POST['nCant'.$i] != "" && $_POST['nCantOcul'.$i] == "") {
            $dFecInicial = strtotime($_POST['cMifdFec'.$i]);
            $dFecFinal   = strtotime(date("Y-m-d"));
            $nDiffDias   = floor(($dFecFinal - $dFecInicial) / (60 * 60 * 24));

            if ($nDiffDias > 7) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "La fecha que intenta diligenciar [".$_POST['cMifdFec'.$i]."] excede en 7 dias a la fecha actual del sistema. Por favor verifique o contacte al administrador.\n";
            }
          }

          if ($_POST['nCant'.$i] != "" && $_POST['nCant'.$i] <= 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "La cantidad debe ser mayor a cero.\n";
          }
        }
      }
    break;
    case "DESBLOQUEO":

      // Valida que el rango de fecha seleccionado este dentro del rango de fechas de la MIF
      if ($_POST['dDesde'] == "" || $_POST['dDesde'] == "0000-00-00" || $_POST['dHasta'] == "" || $_POST['dHasta'] == "0000-00-00") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Debe seleccionar un rango de fechas.\n";
      } else {
        $dFecDesde = strtotime(date($_POST['dDesde']));
        $dFecHasta = strtotime(date($_POST['dHasta']));
        $dMifDesde = strtotime(date($_POST['dMifDesde']));
        $dMifHasta = strtotime(date($_POST['dMifHasta']));

        if ($dFecDesde < $dMifDesde || $dFecHasta > $dMifHasta) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Las fechas seleccionadas no se encuentran dentro de la vigencia de la matriz de Insumos Facturables.\n";
        }
      }

      $cPerAno = $_POST['cAnio'];
      // Valida que la MIF seleccionada exista
      $qDataMif  = "SELECT ";
      $qDataMif .= "mifidxxx, ";
      $qDataMif .= "regestxx ";
      $qDataMif .= "FROM $cAlfa.lmca$cPerAno ";
      $qDataMif .= "WHERE ";
      $qDataMif .= "$cAlfa.lmca$cPerAno.mifidxxx = \"{$_POST['cMifId']}\" LIMIT 0,1";
      $xDataMif  = f_MySql("SELECT","",$qDataMif,$xConexion01,"");
      if (mysql_num_rows($xDataMif) > 0) {
        $vDataMif = mysql_fetch_array($xDataMif);
        if ($vDataMif['regestxx'] != "ENPROCESO") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El estado de la M.I.F debe ser ENPROCESO.\n";
        }
      } else {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La M.I.F [".$_POST['cComPre'].$_POST['cComCsc']."] no existe.\n";
      }
    break;
    case "ACTIVAR":
    case "ANULAR":
      // Valida que la MIF seleccionada exista
      $qDataMif  = "SELECT ";
      $qDataMif .= "mifidxxx, ";
      $qDataMif .= "mifanexx, ";
      $qDataMif .= "regestxx ";
      $qDataMif .= "FROM $cAlfa.lmca$cPerAno ";
      $qDataMif .= "WHERE ";
      $qDataMif .= "$cAlfa.lmca$cPerAno.mifidxxx = \"{$_POST['cMifId']}\" LIMIT 0,1";
      $xDataMif  = f_MySql("SELECT","",$qDataMif,$xConexion01,"");
      if (mysql_num_rows($xDataMif) > 0) {
        $vDataMif = mysql_fetch_array($xDataMif);
        if ($vDataMif['regestxx'] != "ENPROCESO") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El estado de la M.I.F debe ser ENPROCESO.\n";
        }

        if ($vDataMif['mifanexx'] == "NO") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "No es posible ACTIVAR la MIF, no cuenta con anexos. \n";
        }
      } else {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La M.I.F [".$_POST['cComPre'].$_POST['cComCsc']."] no existe.\n";
      }
    break;
    default:
      // No hace nada
    break;
  }
  // Fin de la Validacion

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        // Insertando en la Tabla lmcaYYYY.
        $qInsert  = array(array('NAME' => 'comidxxx','VALUE' => trim(strtoupper($_POST['cComId']))          ,'CHECK' => 'SI'),     //Id del comprobante  
                          array('NAME' => 'comcodxx','VALUE' => trim(strtoupper($_POST['cComCod']))         ,'CHECK' => 'SI'),     //Codigo del comprobante  
                          array('NAME' => 'comprexx','VALUE' => trim(strtoupper($_POST['cComPre']))         ,'CHECK' => 'SI'),     //Prefijo 
                          array('NAME' => 'comcscxx','VALUE' => trim($_POST['cComCsc'])                     ,'CHECK' => 'SI'),     //Consecutivo uno  
                          array('NAME' => 'comcsc2x','VALUE' => trim($_POST['cComCsc2'])                    ,'CHECK' => 'SI'),     //Consecutivo dos
                          array('NAME' => 'cliidxxx','VALUE' => trim(strtoupper($_POST['cCliId']))          ,'CHECK' => 'SI'),     //Id Cliente  
                          array('NAME' => 'depnumxx','VALUE' => trim(strtoupper($_POST['cDepNum']))         ,'CHECK' => 'SI'),     //Numero Deposito  
                          array('NAME' => 'miffdexx','VALUE' => trim($_POST['dDesde'])                      ,'CHECK' => 'SI'),     //Fecha Desde 
                          array('NAME' => 'miffhaxx','VALUE' => trim($_POST['dHasta'])                      ,'CHECK' => 'SI'),     //Fecha Hasta  
                          array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))        ,'CHECK' => 'SI'),     //Usuario que creo el registro  
                          array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                               ,'CHECK' => 'SI'),     //Fecha de creacion  
                          array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                               ,'CHECK' => 'SI'),     //Hora de creacion  
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                               ,'CHECK' => 'SI'),     //Fecha de modificacion  
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                               ,'CHECK' => 'SI'),     //Hora de modificacion  
                          array('NAME' => 'regestxx','VALUE' => trim(strtoupper($_POST['cEstado']))         ,'CHECK' => 'SI'));    //Estado  

        if (!f_MySql("INSERT","lmca$cPerAno",$qInsert,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error Guardando Datos de la Matriz de Insumos Facturables.\n";
        }

        //Actualizando consecutivo uno y dos
        switch ($_POST['cComTCo']) {
          case "MANUAL":
            $cComCsc2++;
            $qUpdCsc = array(array('NAME'=>'comcsc2x','VALUE'=>$cComCsc2	  	  			 ,'CHECK'=>'SI'),
                             array('NAME'=>'comidxxx','VALUE'=>$_POST['cComId']      	 ,'CHECK'=>'WH'),
                             array('NAME'=>'comcodxx','VALUE'=>$_POST['cComCod']     	 ,'CHECK'=>'WH'),
                             array('NAME'=>'peranoxx','VALUE'=>$cPerAno	 							 ,'CHECK'=>'WH'),
                             array('NAME'=>'permesxx','VALUE'=>$cPerMes								 ,'CHECK'=>'WH'));
          break;
          case "AUTOMATICO": default:
            $cComCsc++; $cComCsc2++;
            $qUpdCsc = array(array('NAME'=>'comcscxx','VALUE'=>$cComCsc		  	  			,'CHECK'=>'SI'),
                             array('NAME'=>'comcsc2x','VALUE'=>$cComCsc2	  	  			,'CHECK'=>'SI'),
                             array('NAME'=>'comidxxx','VALUE'=>$_POST['cComId']      	,'CHECK'=>'WH'),
                             array('NAME'=>'comcodxx','VALUE'=>$_POST['cComCod']     	,'CHECK'=>'WH'),
                             array('NAME'=>'peranoxx','VALUE'=>$cPerAno	 							,'CHECK'=>'WH'),
                             array('NAME'=>'permesxx','VALUE'=>$cPerMes								,'CHECK'=>'WH'));
          break;
        }

        if (!f_MySql("UPDATE","lpar0122",$qUpdCsc,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "No se Pudo Actualizar el Consecutivo.\n";
        }
      break;
      case "ADDMOV":
        if ($vMatriz['miforixx'] == "") {
          // Actualiza la informacion de cabecera de la MIF
          $qUpdate = array(array('NAME' => 'miforixx','VALUE' => "MANUAL"               ,'CHECK' => 'SI'),
                           array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')          ,'CHECK' => 'SI'),
                           array('NAME' => 'reghmodx','VALUE' => date('H:i:s')          ,'CHECK' => 'SI'),
                           array('NAME' => 'mifidxxx','VALUE' => trim($_POST['cMifId']) ,'CHECK' => 'WH'));

          if (!f_MySql("UPDATE","lmca$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al Actualizar Datos de Cabecera de la MIF.\n";
          }
        }

        for ($i=0; $i < $_POST['nCantSub']; $i++) { 
          if (($_POST['nCantOcul'.$i] != "" && $_POST['nCant'.$i] == "") || ($_POST['nCantOcul'.$i] == "" && $_POST['nCant'.$i] != "") || ($_POST['cMifdMod'.$i] == "SI" && $_POST['nCant'.$i] != "")) {
            // Actualiza la informacion de detalle de los subservicios
            $qUpdate = array(array('NAME' => 'mifdcanx','VALUE' => $_POST['nCant'.$i]         ,'CHECK' => 'NO'),
                             array('NAME' => 'mifdmodx','VALUE' => NULL                       ,'CHECK' => 'NO'),
                             array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')              ,'CHECK' => 'SI'),
                             array('NAME' => 'reghmodx','VALUE' => date('H:i:s')              ,'CHECK' => 'SI'),
                             array('NAME' => 'mifdidxx','VALUE' => trim($_POST['cMifdId'.$i]) ,'CHECK' => 'WH'));

            if (!f_MySql("UPDATE","lmsu$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Error al Actualizar Datos de Detalle de los Subservicios.\n";
            }
          }
        }
      break;
      case "DESBLOQUEO":
        // Consulta los subservicios por el rango de fechas seleccionado
        $vMifSubservi  = array();
        $qMifSubservi  = "SELECT ";
        $qMifSubservi .= "lmsu$cPerAno.* ";
        $qMifSubservi .= "FROM $cAlfa.lmsu$cPerAno ";
        $qMifSubservi .= "WHERE ";
        $qMifSubservi .= "$cAlfa.lmsu$cPerAno.mifidxxx = \"{$_POST['cMifId']}\" AND ";
        $qMifSubservi .= "$cAlfa.lmsu$cPerAno.mifdfecx BETWEEN \"{$_POST['dDesde']}\" AND \"{$_POST['dHasta']}\"";
        $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
        if (mysql_num_rows($xMifSubservi) > 0) {
          while($xRMS = mysql_fetch_array($xMifSubservi)) {
            $cObservacion = $xRMS['mifdoamx'].trim(strtoupper($_COOKIE['kUsrId']))."~".date('Y-m-d')."|";

            // Actualiza la informacion de detalle de los subservicios
            $qUpdate = array(array('NAME' => 'mifdmodx','VALUE' => "SI"                 ,'CHECK' => 'SI'),
                             array('NAME' => 'mifdoamx','VALUE' => $cObservacion        ,'CHECK' => 'SI'),
                             array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')        ,'CHECK' => 'SI'),
                             array('NAME' => 'reghmodx','VALUE' => date('H:i:s')        ,'CHECK' => 'SI'),
                             array('NAME' => 'mifdidxx','VALUE' => $xRMS['mifdidxx']    ,'CHECK' => 'WH'));

            if (!f_MySql("UPDATE","lmsu$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Error al Actualizar Datos de Detalle de los Subservicios.\n";
            }
          }
        }
      break;
      case "ACTIVAR":
      case "ANULAR":
        $cEstado = ($_COOKIE['kModo'] == "ACTIVAR") ? "ACTIVO" : "ANULADO";

        // Actualiza la informacion de cabecera de la MIF
        $qUpdate = array(array('NAME' => 'regestxx','VALUE' => $cEstado               ,'CHECK' => 'SI'),
                         array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')          ,'CHECK' => 'SI'),
                         array('NAME' => 'reghmodx','VALUE' => date('H:i:s')          ,'CHECK' => 'SI'),
                         array('NAME' => 'mifidxxx','VALUE' => trim($_POST['cMifId']) ,'CHECK' => 'WH'));

        if (!f_MySql("UPDATE","lmca$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Actualizar el Estado en la Cabecera de la MIF.\n";
        }
      break;
      default:
        // No hace nada
      break;
    }

    // Guarda los subservicios en la tabla lmsuYYYY
    if ($nSwitch == 0 && ($_COOKIE['kModo'] == "NUEVO" || $_COOKIE['kModo'] == "EDITAR")) {
      $qMifId  = "SELECT ";
      $qMifId .= "MAX(mifidxxx) AS ultimoId ";
      $qMifId .= "FROM $cAlfa.lmca$cPerAno";
      $xMifId  = f_MySql("SELECT","",$qMifId,$xConexion01,"");
      $vMifId  = mysql_fetch_array($xMifId);
      $cUltimoId = ($_COOKIE['kModo'] == "NUEVO") ? $vMifId['ultimoId'] : $_POST['cMifId'];
      
      $mSubservicios = f_Explode_Array($_POST['cSubservicios'], "|", "~");
      for ($i=0; $i < count($mSubservicios); $i++) { 
        if ($mSubservicios[$i][1] != "") {
          $qMatrizSub  = "SELECT ";
          $qMatrizSub .= "mifdidxx, ";
          $qMatrizSub .= "regestxx ";
          $qMatrizSub .= "FROM $cAlfa.lmsu$cPerAno ";
          $qMatrizSub .= "WHERE ";
          $qMatrizSub .= "mifidxxx = \"$cUltimoId\" AND ";
          $qMatrizSub .= "subidxxx = \"{$mSubservicios[$i][1]}\" LIMIT 0,1 ";
          $xMatrizSub = f_MySql("SELECT","",$qMatrizSub,$xConexion01,"");
          if (mysql_num_rows($xMatrizSub) > 0) {
            $vMatrizSub = mysql_fetch_array($xMatrizSub);

            if ($vMatrizSub['regestxx'] == "INACTIVO") {
              // Actualiza el Estado en la Tabla lmsuYYYY.
              $qUpdate = array(array('NAME' => 'regestxx','VALUE' => "ACTIVO"              ,'CHECK' => 'SI'),
                               array('NAME' => 'mifidxxx','VALUE' => $cUltimoId            ,'CHECK' => 'WH'),
                               array('NAME' => 'subidxxx','VALUE' => $mSubservicios[$i][1] ,'CHECK' => 'WH'));

              if (!f_MySql("UPDATE","lmsu$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Error al Actualizar el Estado de los Subservicios.\n";
              }
            }
          } else {
            $fechaInicio = strtotime(date($_POST['dDesde']));
            $fechaFin    = strtotime(date($_POST['dHasta']));
            for($nDia=$fechaInicio; $nDia<=$fechaFin; $nDia+=86400){
              $cFecha = date("Y-m-d", $nDia);

              // Insertando en la Tabla lmsuYYYY.
              $qInsert  = array(array('NAME' => 'mifidxxx','VALUE' => $cUltimoId                            ,'CHECK' => 'SI'),     //Id MIF
                                array('NAME' => 'sersapxx','VALUE' => $mSubservicios[$i][0]                 ,'CHECK' => 'SI'),     //Codigo SAP Servicio
                                array('NAME' => 'subidxxx','VALUE' => $mSubservicios[$i][1]                 ,'CHECK' => 'SI'),     //Id Subservicio
                                array('NAME' => 'mifdfecx','VALUE' => $cFecha                               ,'CHECK' => 'SI'),     //Fecha
                                array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK' => 'SI'),     //Usuario que creo el registro
                                array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),     //Fecha de creacion
                                array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),     //Hora de creacion 
                                array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),     //Fecha de modificacion
                                array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),     //Hora de modificacion
                                array('NAME' => 'regestxx','VALUE' => "ACTIVO"                              ,'CHECK' => 'SI'));    //Estado
  
              if (!f_MySql("INSERT","lmsu$cPerAno",$qInsert,$xConexion01,$cAlfa)) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Error Guardando los Subservicios de la Matriz de Insumos Facturables.\n";
              }
            }
          }
        }
      }

      $mSubserNoMarcados = f_Explode_Array($_POST['cSubserNoMarcados'], "|", "~");
      for ($i=0; $i < count($mSubserNoMarcados); $i++) { 
        if ($mSubserNoMarcados[$i][1] != "") {
          $qMatrizSub  = "SELECT ";
          $qMatrizSub .= "mifdidxx, ";
          $qMatrizSub .= "mifdcanx, ";
          $qMatrizSub .= "regestxx ";
          $qMatrizSub .= "FROM $cAlfa.lmsu$cPerAno ";
          $qMatrizSub .= "WHERE ";
          $qMatrizSub .= "mifidxxx = \"$cUltimoId\" AND ";
          $qMatrizSub .= "subidxxx = \"{$mSubserNoMarcados[$i][1]}\" ";
          $xMatrizSub = f_MySql("SELECT","",$qMatrizSub,$xConexion01,"");
          $nTieneMovimiento = 0;
          if (mysql_num_rows($xMatrizSub) > 0) {
            while ($xRMS = mysql_fetch_array($xMatrizSub)) {
              if ($xRMS['mifdcanx'] != "") {
                $nTieneMovimiento++;
              }
            }
          }

          if ($nTieneMovimiento > 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Subservicio [".$mSubserNoMarcados[$i][1]."] ya cuenta con movimiento diligenciado. No es posible cambiar el estado del Subservicio seleccionado.\n";
          }
        }
      }

      if ($nSwitch == 0) {
        for ($i=0; $i < count($mSubserNoMarcados); $i++) { 
          // Actualiza el Estado en la Tabla lmsuYYYY.
          $qUpdate = array(array('NAME' => 'regestxx','VALUE' => "INACTIVO"                ,'CHECK' => 'SI'),
                           array('NAME' => 'mifidxxx','VALUE' => $cUltimoId                ,'CHECK' => 'WH'),
                           array('NAME' => 'subidxxx','VALUE' => $mSubserNoMarcados[$i][1] ,'CHECK' => 'WH'));

          if (!f_MySql("UPDATE","lmsu$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al Actualizar el Estado de los Subservicios.\n";
          }
        }
      }
    }
  }

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        f_Mensaje(__FILE__,__LINE__,"Se Creo la Martiz de Insumos Facturables con Exito.");
      break;
      case "EDITAR":
        f_Mensaje(__FILE__,__LINE__,"Se Actualizo la Martiz de Insumos Facturables con Exito.");
      break;
      case "ADDMOV":
        f_Mensaje(__FILE__,__LINE__,"Se Adiciono el Movimiento con Exito.");
      break;
      case "DESBLOQUEO":
        f_Mensaje(__FILE__,__LINE__,"Se Desbloqueo la Martiz de Insumos Facturables con Exito.");
      break;
      case "ACTIVAR":
      case "ANULAR":
        f_Mensaje(__FILE__,__LINE__,"Se Actualizo el Estado de la Martiz de Insumos Facturables con Exito.");
      break;
      default:
        // no hace nada
      break;
    }
    ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      document.forms['frgrm'].submit()
    </script>
  <?php } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
  }

  /**
   * Clase que permite crear la tabla aualizada de cabecera y detalle en caso de estas no existan.
   */
  class cEstructurasTablasAnualizadasMIF{

    /**
     * Permite crear las tablas anualizadas de la MIF.
     */
    function fnCrearTablasAnualizadas() {
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

      /**
       * Año actual del sistema.
       * 
       * @var string
       */
      $nAnio = date('Y');
      $nAnioAnterior = date('Y')-1;

      /**
       * Llamando Metodo que hace conexion
       */
      $mReturnConexionTM = $this->fnConectarDBMIF();
      if ($mReturnConexionTM[0] == "true") {
        $xConexionTM = $mReturnConexionTM[1];
      } else {
        $nSwitch = 1;
        for ($nR=1;$nR<count($mReturnConexionTM);$nR++) {
          $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
        }
      }

      if ($nSwitch == 0) {
        // Creación de tabla anualizada en caso de que no exista
        $qTabExis = "SHOW TABLES FROM $cAlfa LIKE \"lmca$nAnio\"";
        $xTabExis = f_MySql("SELECT","",$qTabExis,$xConexionTM,"");
        if (mysql_num_rows($xTabExis) == 0) {
          $qCreate = "CREATE TABLE IF NOT EXISTS $cAlfa.lmca$nAnio LIKE $cAlfa.lmca$nAnioAnterior ";
          $xCreate = mysql_query($qCreate,$xConexionTM);
          if (!$xCreate) {
            $nSwitch = 1;
            $vReturn[count($vReturn)] = __LINE__."~Error al crear Tabla Anualizada [lmca$nAnio].~".mysql_error($xConexion01);
          } else {
            /**
             * NOTA: Siempre que se cree una llave foránea en la tabla anualizada de cabecera [lmcaxxxx] se debe agregar la sentencia sql ALTER TABLE
             */
            $qAlter  = "ALTER TABLE $cAlfa.lmca$nAnio  ";
            $qAlter .= "ADD CONSTRAINT lmca{$nAnio}_ibfk_1 FOREIGN KEY (cliidxxx) REFERENCES $cAlfa.lpar0150(cliidxxx),";
            $qAlter .= "ADD CONSTRAINT lmca{$nAnio}_ibfk_2 FOREIGN KEY (depnumxx) REFERENCES $cAlfa.lpar0155(depnumxx)";
            $xAlter  = mysql_query($qAlter,$xConexionTM);
            if (!$xAlter) {
              $nSwitch = 1;
              $vReturn[count($vReturn)] = __LINE__."~Error al crear FK Anualizada [lmca$nAnio].~".mysql_error($xConexion01);
            }

            // Valida si NO existe la tabla de detalle para crearla
            $qTabExis = "SHOW TABLES FROM $cAlfa LIKE \"lmsu$nAnio\"";
            $xTabExis = f_MySql("SELECT","",$qTabExis,$xConexionTM,"");
            if( mysql_num_rows($xTabExis) == 0 ){
              $qCreate = "CREATE TABLE IF NOT EXISTS $cAlfa.lmsu$nAnio LIKE $cAlfa.lmsu$nAnioAnterior ";
              $xCreate = mysql_query($qCreate,$xConexionTM);
              if(!$xCreate) {
                $nSwitch = 1;
                $vReturn[count($vReturn)] = __LINE__."~Error al crear Tabla Anualizada [lmsu$nAnio].~".mysql_error($xConexion01);
              } else {
                /**
                 * NOTA: Siempre que se cree una llave foránea en la tabla anualizada de detalle [lmsuxxxx] se debe agregar la sentencia sql ALTER TABLE
                 */
                $qAlter  = "ALTER TABLE $cAlfa.lmsu$nAnio ";
                $qAlter .= "ADD CONSTRAINT lmsu{$nAnio}_ibfk_1 FOREIGN KEY (mifidxxx) REFERENCES $cAlfa.lmca$nAnio(mifidxxx),";
                $qAlter .= "ADD CONSTRAINT lmsu{$nAnio}_ibfk_2 FOREIGN KEY (sersapxx) REFERENCES $cAlfa.lpar0011(sersapxx),";
                $qAlter .= "ADD CONSTRAINT lmsu{$nAnio}_ibfk_3 FOREIGN KEY (sersapxx,subidxxx) REFERENCES $cAlfa.lpar0012(sersapxx,subidxxx)";
                $xAlter  = mysql_query($qAlter,$xConexionTM);
                if (!$xAlter) {
                  $nSwitch = 1;
                  $vReturn[count($vReturn)] = __LINE__."~Error al crear FK Anualizada [lmsu$nAnio].~".mysql_error($xConexion01);
                }
              }
            } 
          }
        }
      }

      if($nSwitch == 0){
        $mReturn[0] = "true";
      }else{
        $mReturn[0] = "false";
      }
      return $mReturn;
    }

    /**
     * Metodo que realiza la conexion
     */
    function fnConectarDBMIF(){

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
    }##function fnConectarDBMIF(){##
  }
?>

<?php
  /**
   * Graba Certificacion.
   * --- Descripcion: Permite Guardar en la tabla Certificacion un nuevo registro.
   * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @package opencomex
   * @version 001
   */
  include('../../../../../financiero/libs/php/utility.php');
  include("../../../../libs/php/utimifxx.php");
  include("../../../../../config/config.php");

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
  $objTablasAnualizadas          = new cEstructurasTablasAnualizadasCertificaciones();
  $mReturnCrearTablasAnualizadas = $objTablasAnualizadas->fnCrearTablasAnualizadas();
  if($mReturnCrearTablasAnualizadas[0] == "false"){
    $nSwitch = 1;
    for($nR=1;$nR<count($mReturnCrearTablasAnualizadas);$nR++){
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= $mReturnCrearTablasAnualizadas[$nR] . "\n";
    }
  }

  $cPerAno = ($_COOKIE['kModo'] == "NUEVO") ? $cPerAno : $_POST['cAnio'];
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
          if(mysql_num_rows($xComprobante) == 0){
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
            $cMsj .= "El Consecutivo [{$_POST['cComCsc']}] debe ser numerico.\n";
          } else {
            if (strlen($_POST['cComCsc']) > 6) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Consecutivo [{$_POST['cComCsc']}] no puede ser mayor a 6 caracteres.\n";
            }
          }
        } elseif ($_POST['cComCsc'] == "" && $_POST['cComTCo'] == "MANUAL") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Consecutivo no puede ser vacio.\n";
        }
      }

      // Valida que el estado de la Certificacion este ENPROCESO al momento de Editar
      if ($_COOKIE['kModo'] == "EDITAR" && $_POST['cRegEst'] != "ENPROCESO") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Certificacion debe tener estado ENPROCESO.\n";
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
          if(mysql_num_rows($xCliDat) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Nit del Cliente [{$_POST['cCliId']}] no existe.\n";
          }
        }
      }

      if ($_POST['cCerTip'] == "AUTOMATICA") {
        // Validando la MIF
        // Validando que la MIF no sea vacia
        if ($_POST['cMifComId'] == "" && $_POST['cMifComCod'] == "" && $_POST['cMifComCsc'] == "" && $_POST['cMifComCsc2'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La M.I.F no puede ser vacia.\n";
        } else {
          // Validando que la MIF exista
          if ($_COOKIE['kModo'] == "NUEVO") {
            $qMatrizIns  = "SELECT mifidxxx ";
            $qMatrizIns .= "FROM $cAlfa.lmca{$_POST['cPerAno']} ";
            $qMatrizIns .= "WHERE ";
            $qMatrizIns .= "comidxxx = \"{$_POST['cMifComId']}\" AND ";
            $qMatrizIns .= "comcodxx = \"{$_POST['cMifComCod']}\" AND ";
            $qMatrizIns .= "comcscxx = \"{$_POST['cMifComCsc']}\" AND ";
            $qMatrizIns .= "comcsc2x = \"{$_POST['cMifComCsc2']}\" AND ";
            $qMatrizIns .= "regestxx IN (\"ACTIVO\",\"CERTIFICADO_PARCIAL\") LIMIT 0,1 ";
            $xMatrizIns  = f_MySql("SELECT","",$qMatrizIns,$xConexion01,"");
            if(mysql_num_rows($xMatrizIns) == 0){
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "La M.I.F [{$_POST['cMifComId']}-{$_POST['cMifComCod']}-{$_POST['cMifComCsc']}-{$_POST['cMifComCsc2']}] no existe.\n";
            }
          }
        }
      } else {
        // Validando el Deposito
        // Validando que el Deposito no sea vacio.
        if ($_POST['cDepNum_hidd'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Deposito no puede ser vacio.\n";
        } else {
          // Validando que el Deposito exista.
          if ($_COOKIE['kModo'] == "NUEVO") {
            $qDeposito  = "SELECT depnumxx ";
            $qDeposito .= "FROM $cAlfa.lpar0155 ";
            $qDeposito .= "WHERE ";
            $qDeposito .= "$cAlfa.lpar0155.depnumxx = \"{$_POST['cDepNum_hidd']}\" AND ";
            $qDeposito .= "$cAlfa.lpar0155.regestxx = \"ACTIVO\" LIMIT 0,1 ";
            $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
            if(mysql_num_rows($xDeposito) == 0){
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Deposito [{$_POST['cDepNum_hidd']}] no existe.\n";
            }
          }
        }

        // Validando Fechas
        // Validando que la Fecha Desde no sea vacia.
        if ($_POST['dVigDesde'] == "" || $_POST['dVigDesde'] == "0000-00-00") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Se bebe deleccionar una Fecha de Vigencia desde.\n";
        }
        // Validando que la Fecha Hasta no sea vacia.
        if ($_POST['dVigHasta'] == "" || $_POST['dVigHasta'] == "0000-00-00") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Se debe seleccionar una Fecha de Viegnecia hasta.\n";
        }
        // Validando la fecha desde, que no sea mayor que la fecha hasta.
        if ($_POST['dDesde'] > $_POST['dVigHasta']) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La Fecha Vigencia desde no puede ser mayor a la Fecha Vigencia hasta.\n";
        }
      }

      // Validando el Canal de Distribucion
      // Validando que el Canal de Distribucion no sea vacio.
      if ($_POST['cCdiSap'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Canal de Distribucion no puede ser vacio.\n";
      } else {
        // Validando que el Canal de Distribucion exista.
        if ($_COOKIE['kModo'] == "NUEVO") {
          $qCanalDis  = "SELECT cdisapxx ";
          $qCanalDis .= "FROM $cAlfa.lpar0008 ";
          $qCanalDis .= "WHERE ";
          $qCanalDis .= "$cAlfa.lpar0008.cdisapxx = \"{$_POST['cCdiSap']}\" AND ";
          $qCanalDis .= "$cAlfa.lpar0008.regestxx = \"ACTIVO\" LIMIT 0,1 ";
          $xCanalDis  = f_MySql("SELECT","",$qCanalDis,$xConexion01,"");
          if(mysql_num_rows($xCanalDis) == 0){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Canal de Distribucion [{$_POST['cCdiSap']}] no existe.\n";
          }
        }
      }

      // Validando que el Tipo de Mercancia no sea vacio.
      if ($_POST['cCerTipMe'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Tipo de Mercancia no puede ser vacio.\n";
      }

      // // Validaciones nuevo Periodo
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

            // Se define consecutivo uno para la tabla lccaYYYY
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
            $cMsj .= "No se encontro Consecutivo para el comprobante.\n";
          }
        }

        // Valida si el documento ya existe
        $qValCom  = "SELECT * ";
        $qValCom .= "FROM $cAlfa.lcca$cPerAno ";
        $qValCom .= "WHERE ";
        $qValCom .= "comidxxx = \"{$_POST['cComId']}\"  AND ";
        $qValCom .= "comcodxx = \"{$_POST['cComCod']}\" AND ";
        $qValCom .= "comcscxx = \"{$_POST['cComCsc']}\" AND ";
        $qValCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xValCom  = f_MySql("SELECT","",$qValCom,$xConexion01,"");
        if (mysql_num_rows($xValCom) > 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc']}] no se puede grabar porque ya existe.\n";
        } else {
          $qValCom  = "SELECT * ";
          $qValCom .= "FROM $cAlfa.lcca$cPerAno ";
          $qValCom .= "WHERE ";
          $qValCom .= "comidxxx = \"{$_POST['cComId']}\"  AND ";
          $qValCom .= "comprexx = \"{$_POST['cComPre']}\" AND ";
          $qValCom .= "comcscxx = \"{$_POST['cComCsc']}\" AND ";
          $qValCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
          $xValCom  = f_MySql("SELECT","",$qValCom,$xConexion01,"");
          if (mysql_num_rows($xValCom) > 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComPre']}-{$_POST['cComCsc']}] no se puede grabar porque ya existe.\n";
          } else {
            $qValCom  = "SELECT * ";
            $qValCom .= "FROM $cAlfa.lcca$cPerAno ";
            $qValCom .= "WHERE ";
            $qValCom .= "comidxxx = \"{$_POST['cComId']}\"  AND ";
            $qValCom .= "comcodxx = \"{$_POST['cComCod']}\" AND ";
            $qValCom .= "comcsc2x = \"{$_POST['cComCsc2']}\" LIMIT 0,1";
            $xValCom  = f_MySql("SELECT","",$qValCom,$xConexion01,"");
            if (mysql_num_rows($xValCom) > 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Documento [{$_POST['cComId']}-{$_POST['cComCod']}-{$_POST['cComCsc2']}] no se puede grabar porque ya existe.\n";
            }
          }
        }
      }
      // Fin Valida si el documento ya existe

      $nObsObligatoria = 0;
      // Valida las grillas de certificacion (Detalle)
      for ($i=1; $i <= $_POST['nSecuencia_Certificacion']; $i++) {
        if ($_POST['cDesMaterial' . $i] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La Descripcion del Material no puede ser vacia para la secuencia [".$i."].\n";
        } else {
          // Si el servicio es automatico o transaccional se valida que exista en el sistema
          if ($_POST['cTipoCerti' . $i] != "MANUAL") {
            $qServicio  = "SELECT ";
            $qServicio .= "sersapxx ";
            $qServicio .= "FROM $cAlfa.lpar0011 ";
            $qServicio .= "WHERE ";
            $qServicio .= "lpar0011.sersapxx = \"{$_POST['cCodSapSer' . $i]}\" AND ";
            $qServicio .= "lpar0011.regestxx = \"ACTIVO\" ";
            $xServicio  = f_MySql("SELECT","",$qServicio,$xConexion01,"");
            if (mysql_num_rows($xServicio) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Servicio con codigo [".$_POST['cCodSapSer' . $i]."] no existe para la secuencia [".$i."].\n";
            }
          }

          // Valida que el Objeto Facturable no sea vacio
          if ($_POST['cObfId_Certi' . $i] == "") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Objeto Facturable no puede ser vacio para la secuencia [".$i."].\n";
          } else {
            // Valida que el Objeto Facturable exista
            $qObjFact  = "SELECT ";
            $qObjFact .= "obfidxxx ";
            $qObjFact .= "FROM $cAlfa.lpar0004 ";
            $qObjFact .= "WHERE ";
            $qObjFact .= "lpar0004.obfidxxx = \"{$_POST['cObfId_Certi' . $i]}\" AND ";
            $qObjFact .= "lpar0004.regestxx = \"ACTIVO\" ";
            $xObjFact  = f_MySql("SELECT","",$qObjFact,$xConexion01,"");
            if (mysql_num_rows($xObjFact) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Objeto Facturable con codigo [".$_POST['cObfId_Certi' . $i]."] no existe para la secuencia [".$i."].\n";
            }
          }

          // Valida que la Unidad Facturable no sea vacia
          if ($_POST['cUfaId_Certi' . $i] == "") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "La Unidad Facturable no puede ser vacia para la secuencia [".$i."].\n";
          } else {
            // Valida que la Unidad Facturable exista
            $qUniFact  = "SELECT ";
            $qUniFact .= "ufaidxxx ";
            $qUniFact .= "FROM $cAlfa.lpar0006 ";
            $qUniFact .= "WHERE ";
            $qUniFact .= "lpar0006.ufaidxxx = \"{$_POST['cUfaId_Certi' . $i]}\" AND ";
            $qUniFact .= "lpar0006.regestxx = \"ACTIVO\" ";
            $xUniFact  = f_MySql("SELECT","",$qUniFact,$xConexion01,"");
            if (mysql_num_rows($xUniFact) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "La Unidad Facturable con codigo [".$_POST['cUfaId_Certi' . $i]."] no existe para la secuencia [".$i."].\n";
            }
          }

          // Valida que el Codigo Cebe no sea vacio
          if ($_POST['cCebId' . $i] == "") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Codigo Cebe no puede ser vacio para la secuencia [".$i."].\n";
          } else {
            // Valida que el Codigo Cebe exista
            $qCodCebe  = "SELECT ";
            $qCodCebe .= "cebidxxx ";
            $qCodCebe .= "FROM $cAlfa.lpar0010 ";
            $qCodCebe .= "WHERE ";
            $qCodCebe .= "lpar0010.cebidxxx = \"{$_POST['cCebId' . $i]}\" AND ";
            $qCodCebe .= "lpar0010.regestxx = \"ACTIVO\" ";
            $xCodCebe  = f_MySql("SELECT","",$qCodCebe,$xConexion01,"");
            if (mysql_num_rows($xCodCebe) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "El Codigo Cebe [".$_POST['cCebCod' . $i]."] no existe para la secuencia [".$i."].\n";
            }
          }

          // Valida que la Base no sea vacia
          if ($_POST['cBase' . $i] == "") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "La Base no puede ser vacia para la secuencia [".$i."].\n";
          }
        }

        // Valida si la observacion es obligatoria
        if ($_POST['cCondicion' . $i] == "DESHABILITADO") {
          $nObsObligatoria += 1;
        }
      }
      // Fin Valida las grillas de certificacion (Detalle)

      // Valida que la observacion no sea vacia solo si existen subservicios deshabilitado
      if ($nObsObligatoria > 0 && $_POST['cCerObs'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Observacion no puede ser vacia.\n";
      }

    break;
    case "CERTIFICAFINANCIERO":
    case "APROBADOFINANCIERO":
    case 'RECHAZOFINANCIERO':
    case 'ANULAR':

      // Se obtiene el estado a validar
      $cEstValidar = "";
      switch ($_COOKIE['kModo']) {
        case 'CERTIFICAFINANCIERO':
        case 'ANULAR':
          $cEstValidar = "ENPROCESO";
        break;
        case 'APROBADOFINANCIERO':
        case 'RECHAZOFINANCIERO':
          $cEstValidar = "VALIDACION_FINANCIERA";
        break;
        default:
          // No hace nada
        break;
      }

      // Consulta la informacion del comprobante seleccionado
      $qCertificacion  = "SELECT ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.ceridxxx, ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.comidxxx, ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.comcodxx, ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.comcscxx, ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.comcsc2x, ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.mifidxxx, ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.mifidano, ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.certipxx, ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.ceranexx, ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.regestxx ";
      $qCertificacion .= "FROM $cAlfa.lcca$cPerAno ";
      $qCertificacion .= "WHERE ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.comidxxx = \"{$_POST['gComId']}\" AND ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.comcodxx = \"{$_POST['gComCod']}\" AND ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.comcscxx = \"{$_POST['gComCsc']}\" AND ";
      $qCertificacion .= "$cAlfa.lcca$cPerAno.comcsc2x = \"{$_POST['gComCsc2']}\" LIMIT 0,1 ";
      $xCertificacion  = f_MySql("SELECT","",$qCertificacion,$xConexion01,"");
      $vCertificacion  = array();
      if (mysql_num_rows($xCertificacion) > 0) {
        $vCertificacion = mysql_fetch_array($xCertificacion);
        if ($vCertificacion['regestxx'] != $cEstValidar) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La Certificacion [".$vCertificacion['comidxxx']."-".$vCertificacion['comcodxx']."-".$vCertificacion['comcscxx']."-".$vCertificacion['comcsc2x']."] debe tener el estado [".$cEstValidar."].\n";
        }

        if ($vCertifiCab['ceranexx'] == "NO" && $_COOKIE['kModo'] == "CERTIFICAFINANCIERO") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "No es posible asignar el estado Certificar para Financiero, la certificacion es MANUAL y no cuenta con anexos.\n";
        }

        if ($vCertifiCab['ceranexx'] == "NO" && $_COOKIE['kModo'] == "CERTIFICAFACTURA") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "No es posible asignar el estado Certificar para Facturación, la certificacion es MANUAL y no cuenta con anexos. \n";
        }
      } else {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Certificacion [{$_POST['gComId']}-{$_POST['gComCod']}-{$_POST['gComCsc']}-{$_POST['gComCsc2']}] no existe.\n";
      }

      if (($_COOKIE['kModo'] == "RECHAZOFINANCIERO" || $_COOKIE['kModo'] == "ANULAR") && $_POST['gObservacion'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Observacion no puede ser vacia.\n";
      }
    break;
    default:
      // No hace nada
    break;
  }
  // Fin de la Validacion

  // echo "<br>";
  // echo $nSwitch;
  // echo "<br>";
  // echo $cMsj;
  // echo "<br>";

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        $cAnioMif = $_POST['cPerAno'];

        // Insertando en la Tabla lccaYYYY (Cabecera)
        $qInsert  = array(array('NAME' => 'comidxxx','VALUE' => trim(strtoupper($_POST['cComId']))          ,'CHECK' => 'SI'),  //Id del comprobante
                          array('NAME' => 'comcodxx','VALUE' => trim(strtoupper($_POST['cComCod']))         ,'CHECK' => 'SI'),  //Codigo del comprobante
                          array('NAME' => 'comprexx','VALUE' => trim(strtoupper($_POST['cComPre']))         ,'CHECK' => 'SI'),  //Prefijo
                          array('NAME' => 'comcscxx','VALUE' => trim($_POST['cComCsc'])                     ,'CHECK' => 'SI'),  //Consecutivo uno
                          array('NAME' => 'comcsc2x','VALUE' => trim($_POST['cComCsc2'])                    ,'CHECK' => 'SI'),  //Consecutivo dos
                          array('NAME' => 'comfecxx','VALUE' => trim($_POST['dComFec'])                     ,'CHECK' => 'SI'),  //Fecha del comprobante
                          array('NAME' => 'comhorxx','VALUE' => trim($_POST['tComHCre'])                    ,'CHECK' => 'SI'),  //Hora del comprobante
                          array('NAME' => 'certipxx','VALUE' => trim($_POST['cCerTip'])                     ,'CHECK' => 'SI'),  //Tipo de certificacion
                          array('NAME' => 'cliidxxx','VALUE' => trim($_POST['cCliId'])                      ,'CHECK' => 'SI'),  //Id Cliente
                          array('NAME' => 'mifidxxx','VALUE' => trim($_POST['cMifId'])                      ,'CHECK' => 'NO'),  //Id MIF
                          array('NAME' => 'mifidano','VALUE' => trim($cAnioMif)                             ,'CHECK' => 'NO'),  //Año de la MIF
                          array('NAME' => 'cerfdexx','VALUE' => trim($_POST['dVigDesde'])                   ,'CHECK' => 'SI'),  //Fecha Vigencia Desde
                          array('NAME' => 'cerfhaxx','VALUE' => trim($_POST['dVigHasta'])                   ,'CHECK' => 'SI'),  //Fecha Vigencia Hasta
                          array('NAME' => 'depnumxx','VALUE' => trim($_POST['cDepNum_hidd'])                ,'CHECK' => 'SI'),  //Numero Deposito
                          array('NAME' => 'cdisapxx','VALUE' => trim($_POST['cCdiSap'])                     ,'CHECK' => 'SI'),  //Codigo SAP canal distribucion
                          array('NAME' => 'certipme','VALUE' => trim($_POST['cCerTipMe'])                   ,'CHECK' => 'SI'),  //Tipo de mercancia
                          array('NAME' => 'cerobsxx','VALUE' => trim($_POST['cCerObs'])                     ,'CHECK' => 'NO'),  //Observacion
                          array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))        ,'CHECK' => 'SI'),  //Usuario que creo el registro
                          array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                               ,'CHECK' => 'SI'),  //Fecha de creacion
                          array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                               ,'CHECK' => 'SI'),  //Hora de creacion
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                               ,'CHECK' => 'SI'),  //Fecha de modificacion
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                               ,'CHECK' => 'SI'),  //Hora de modificacion
                          array('NAME' => 'regestxx','VALUE' => trim("ENPROCESO")                           ,'CHECK' => 'SI')); //Estado

        if (!f_MySql("INSERT","lcca$cPerAno",$qInsert,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error guardando datos de la Certificacion.\n";
        } else {
          // Insertando en la tabla lcdeYYYY (Detalle)
          $qCertifiCab  = "SELECT ";
          $qCertifiCab .= "MAX(ceridxxx) AS ultimoId ";
          $qCertifiCab .= "FROM $cAlfa.lcca$cPerAno";
          $xCertifiCab  = f_MySql("SELECT","",$qCertifiCab,$xConexion01,"");
          $vCertifiCab  = mysql_fetch_array($xCertifiCab);

          $nErrorDetalle = 0;
          // Recorre la grilla de Certificacion
          for ($i=1; $i <= $_POST['nSecuencia_Certificacion']; $i++) {
            $qInsert = array(array('NAME' => 'ceridxxx','VALUE' => $vCertifiCab['ultimoId']              ,'CHECK' => 'SI'),  //Id Certificacion Cabecera
                             array('NAME' => 'sersapxx','VALUE' => trim($_POST['cCodSapSer' . $i])       ,'CHECK' => 'NO'),  //Codigo SAP Servicio
                             array('NAME' => 'subidxxx','VALUE' => trim($_POST['cSubId_Certi' . $i])     ,'CHECK' => 'NO'),  //Id Subservicio
                             array('NAME' => 'subdesxx','VALUE' => trim($_POST['cDesMaterial' . $i])     ,'CHECK' => 'SI'),  //Descripcion Subservicio
                             array('NAME' => 'obfidxxx','VALUE' => trim($_POST['cObfId_Certi' . $i])     ,'CHECK' => 'SI'),  //Id Objeto Facturable
                             array('NAME' => 'ufaidxxx','VALUE' => trim($_POST['cUfaId_Certi' . $i])     ,'CHECK' => 'SI'),  //Id Unidad Facturable
                             array('NAME' => 'cebidxxx','VALUE' => trim($_POST['cCebId' . $i])           ,'CHECK' => 'SI'),  //Id Codigo Cebe
                             array('NAME' => 'basexxxx','VALUE' => trim($_POST['cBase' . $i])            ,'CHECK' => 'SI'),  //Base
                             array('NAME' => 'cerdconx','VALUE' => trim($_POST['cCondicion' . $i])       ,'CHECK' => 'SI'),  //Condicion
                             array('NAME' => 'cerdestx','VALUE' => trim($_POST['cStatus' . $i])          ,'CHECK' => 'SI'),  //Status
                             array('NAME' => 'cerdorix','VALUE' => trim($_POST['cTipoCerti' . $i])       ,'CHECK' => 'SI'),  //Origen
                             array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK' => 'SI'),  //Usuario que creo el registro
                             array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),  //Fecha de creacion
                             array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),  //Hora de creacion 
                             array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),  //Fecha de modificacion
                             array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),  //Hora de modificacion
                             array('NAME' => 'regestxx','VALUE' => "ENPROCESO"                           ,'CHECK' => 'SI'));  //Estado

            if (!f_MySql("INSERT","lcde$cPerAno",$qInsert,$xConexion01,$cAlfa)) {
              $nErrorDetalle = 1;
            } else {
              // Actualiza el Subservicio de la MIF con el Consecutivo de la Certificacion
              if ($_POST['cMifComCsc'] != "" && $_POST['cMifId'] != "") {
                $qMifSubservi  = "SELECT ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.mifdidxx ";
                $qMifSubservi .= "FROM $cAlfa.lmsu$cAnioMif ";
                $qMifSubservi .= "WHERE ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.mifidxxx = \"{$_POST['cMifId']}\" AND ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.sersapxx = \"{$_POST['cCodSapSer' . $i]}\" AND ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.subidxxx = \"{$_POST['cSubId_Certi' . $i]}\"";
                $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
                $nError = 0;
                if (mysql_num_rows($xMifSubservi) > 0) {
                  while ($xRMS = mysql_fetch_array($xMifSubservi)) {
                    $cConsecutivo = trim(strtoupper($_POST['cComId']))."-".trim(strtoupper($_POST['cComPre']))."-".trim(strtoupper($_POST['cComCsc']));
                    $qUpdateMif   = array(array('NAME'=>'cercscxx','VALUE' => $cConsecutivo      ,'CHECK'=>'SI'),
                                          array('NAME'=>'regestxx','VALUE' => "CERTIFICADO"      ,'CHECK'=>'SI'),
                                          array('NAME'=>'mifdidxx','VALUE' => $xRMS['mifdidxx']  ,'CHECK'=>'WH'));
                    
                    if (!f_MySql("UPDATE","lmsu$cAnioMif",$qUpdateMif,$xConexion01,$cAlfa)) {
                      $nError = 1;
                    }
                  }

                  if ($nError == 1) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "No se pudo actualizar el consecutivo de Certificacion en el detalle de la MIF.\n";
                  }
                }
              }
            }
          }

          if ($nErrorDetalle == 1) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error guardando datos de la Certificacion en el detalle.\n";
          }

          if ($nSwitch == 0 && $_POST['cMifComCsc'] != "" && $_POST['cMifId'] != "") {
            fnActualizaEstadosMif($cAnioMif, $_POST['cMifId']);
          }

          //Actualizando consecutivo uno y dos
          switch ($_POST['cComTCo']) {
            case "MANUAL":
              $cComCsc2++;
              $qUpdCsc = array(array('NAME' => 'comcsc2x', 'VALUE' => $cComCsc2            ,'CHECK'=>'SI'),
                               array('NAME' => 'comidxxx', 'VALUE' => $_POST['cComId']     ,'CHECK'=>'WH'),
                               array('NAME' => 'comcodxx', 'VALUE' => $_POST['cComCod']    ,'CHECK'=>'WH'),
                               array('NAME' => 'peranoxx', 'VALUE' => $cPerAno             ,'CHECK'=>'WH'),
                               array('NAME' => 'permesxx', 'VALUE' => $cPerMes             ,'CHECK'=>'WH'));
            break;
            case "AUTOMATICO": default:
              $cComCsc++; $cComCsc2++;
              $qUpdCsc = array(array('NAME' => 'comcscxx', 'VALUE' => $cComCsc             ,'CHECK'=>'SI'),
                               array('NAME' => 'comcsc2x', 'VALUE' => $cComCsc2            ,'CHECK'=>'SI'),
                               array('NAME' => 'comidxxx', 'VALUE' => $_POST['cComId']     ,'CHECK'=>'WH'),
                               array('NAME' => 'comcodxx', 'VALUE' => $_POST['cComCod']    ,'CHECK'=>'WH'),
                               array('NAME' => 'peranoxx', 'VALUE' => $cPerAno             ,'CHECK'=>'WH'),
                               array('NAME' => 'permesxx', 'VALUE' => $cPerMes             ,'CHECK'=>'WH'));
            break;
          }

          if (!f_MySql("UPDATE","lpar0122",$qUpdCsc,$xConexion01,$cAlfa)) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "No se pudo actualizar el consecutivo.\n";
          }
        }
      break;
      case "EDITAR":
        $cAnioMif = $_POST['cPerAno'];

        // Actualiza la observacion de cabecera
        $qUpdate = array(array('NAME' => 'cerobsxx', 'VALUE' => $_POST['cCerObs']    ,'CHECK'=>'NO'),
                         array('NAME' => 'ceridxxx', 'VALUE' => $_POST['cCerId']     ,'CHECK'=>'WH'));
                        
        if (!f_MySql("UPDATE","lcca$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error actualizando la observacion de la Certificacion.\n";
        }

        // Válida si los subservicios guardados en el sistema son los mismos que se están editando, sino se eliminan de la Base de Datos
        $qCertifiDet  = "SELECT ";
        $qCertifiDet .= "$cAlfa.lcde$cPerAno.* ";
        $qCertifiDet .= "FROM $cAlfa.lcde$cPerAno ";
        $qCertifiDet .= "WHERE ";
        $qCertifiDet .= "$cAlfa.lcde$cPerAno.ceridxxx = \"{$_POST['cCerId']}\" ";
        $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
        if (mysql_num_rows($xCertifiDet) > 0) {
          while ($xRCD = mysql_fetch_array($xCertifiDet)) {
            $nExiste = 0;
            // Recorre la grilla de certificacion
            for ($i=1; $i <= $_POST['nSecuencia_Certificacion']; $i++) { 
              if ($xRCD['cerdidxx'] == $_POST['cCerdId' . $i ]) {
                $nExiste = 1;
              }
            }

            // Si no existe el subservicio se elimina de la Base de Datos
            if ($nExiste == 0) {
              $qDelete = array(array('NAME' => 'cerdidxx','VALUE' => trim(strtoupper($xRCD['cerdidxx']))   ,'CHECK'=>'WH'));
              if (f_MySql("DELETE","lcde$cPerAno",$qDelete,$xConexion01,$cAlfa) && $_POST['cMifId'] != "") {
                // Se libera el consecutivo de la certificacion en la MIF
                $qMifSubservi  = "SELECT ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.mifdidxx ";
                $qMifSubservi .= "FROM $cAlfa.lmsu$cAnioMif ";
                $qMifSubservi .= "WHERE ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.mifidxxx = \"{$_POST['cMifId']}\" AND ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.sersapxx = \"{$xRCD['sersapxx']}\" AND ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.subidxxx = \"{$xRCD['subidxxx']}\"";
                $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
                $nError = 0;
                if (mysql_num_rows($xMifSubservi) > 0) {
                  while ($xRMS = mysql_fetch_array($xMifSubservi)) {
                    $qUpdateMif = array(array('NAME'=>'cercscxx','VALUE' => ""                 ,'CHECK'=>'NO'),
                                        array('NAME'=>'regestxx','VALUE' => "ACTIVO"           ,'CHECK'=>'SI'),
                                        array('NAME'=>'mifdidxx','VALUE' => $xRMS['mifdidxx']  ,'CHECK'=>'WH'));
                    
                    if (!f_MySql("UPDATE","lmsu$cAnioMif",$qUpdateMif,$xConexion01,$cAlfa)) {
                      $nError = 1;
                    }
                  }

                  if ($nError == 1) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "No se pudo actualizar el consecutivo de Certificacion en el detalle de la MIF.\n";
                  }
                }
              }
            }
          }
        }

        $nErrorDetalle = 0;
        // Actualiza o crea los nuevos subservicios de la grilla de certificacion
        for ($i=1; $i <= $_POST['nSecuencia_Certificacion']; $i++) {
          $qCertifiDet  = "SELECT ";
          $qCertifiDet .= "$cAlfa.lcde$cPerAno.* ";
          $qCertifiDet .= "FROM $cAlfa.lcde$cPerAno ";
          $qCertifiDet .= "WHERE ";
          $qCertifiDet .= "$cAlfa.lcde$cPerAno.cerdidxx = \"{$_POST['cCerdId' . $i]}\" ";
          $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
          if (mysql_num_rows($xCertifiDet) > 0) {
            $qUpdate = array(array('NAME' => 'sersapxx','VALUE' => trim($_POST['cCodSapSer' . $i])       ,'CHECK' => 'NO'),  //Codigo SAP Servicio
                             array('NAME' => 'subidxxx','VALUE' => trim($_POST['cSubId_Certi' . $i])     ,'CHECK' => 'NO'),  //Id Subservicio
                             array('NAME' => 'subdesxx','VALUE' => trim($_POST['cDesMaterial' . $i])     ,'CHECK' => 'SI'),  //Descripcion Subservicio
                             array('NAME' => 'obfidxxx','VALUE' => trim($_POST['cObfId_Certi' . $i])     ,'CHECK' => 'SI'),  //Id Objeto Facturable
                             array('NAME' => 'ufaidxxx','VALUE' => trim($_POST['cUfaId_Certi' . $i])     ,'CHECK' => 'SI'),  //Id Unidad Facturable
                             array('NAME' => 'cebidxxx','VALUE' => trim($_POST['cCebId' . $i])           ,'CHECK' => 'SI'),  //Id Codigo Cebe
                             array('NAME' => 'basexxxx','VALUE' => trim($_POST['cBase' . $i])            ,'CHECK' => 'SI'),  //Base
                             array('NAME' => 'cerdconx','VALUE' => trim($_POST['cCondicion' . $i])       ,'CHECK' => 'SI'),  //Condicion
                             array('NAME' => 'cerdestx','VALUE' => trim($_POST['cStatus' . $i])          ,'CHECK' => 'SI'),  //Status
                             array('NAME' => 'cerdorix','VALUE' => trim($_POST['cTipoCerti' . $i])       ,'CHECK' => 'SI'),  //Origen
                             array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK' => 'SI'),  //Usuario que creo el registro
                             array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),  //Fecha de creacion
                             array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),  //Hora de creacion 
                             array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),  //Fecha de modificacion
                             array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),  //Hora de modificacion
                             array('NAME' => 'cerdidxx','VALUE' => $_POST['cCerdId' . $i]                ,'CHECK' => 'WH'));

            if (!f_MySql("UPDATE","lcde$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
              $nErrorDetalle = 1;
            }
          } else {
            $qInsert = array(array('NAME' => 'ceridxxx','VALUE' => trim($_POST['cCerId'])                ,'CHECK' => 'SI'),  //Id Certificacion Cabecera
                             array('NAME' => 'sersapxx','VALUE' => trim($_POST['cCodSapSer' . $i])       ,'CHECK' => 'NO'),  //Codigo SAP Servicio
                             array('NAME' => 'subidxxx','VALUE' => trim($_POST['cSubId_Certi' . $i])     ,'CHECK' => 'NO'),  //Id Subservicio
                             array('NAME' => 'subdesxx','VALUE' => trim($_POST['cDesMaterial' . $i])     ,'CHECK' => 'SI'),  //Descripcion Subservicio
                             array('NAME' => 'obfidxxx','VALUE' => trim($_POST['cObfId_Certi' . $i])     ,'CHECK' => 'SI'),  //Id Objeto Facturable
                             array('NAME' => 'ufaidxxx','VALUE' => trim($_POST['cUfaId_Certi' . $i])     ,'CHECK' => 'SI'),  //Id Unidad Facturable
                             array('NAME' => 'cebidxxx','VALUE' => trim($_POST['cCebId' . $i])           ,'CHECK' => 'SI'),  //Id Codigo Cebe
                             array('NAME' => 'basexxxx','VALUE' => trim($_POST['cBase' . $i])            ,'CHECK' => 'SI'),  //Base
                             array('NAME' => 'cerdconx','VALUE' => trim($_POST['cCondicion' . $i])       ,'CHECK' => 'SI'),  //Condicion
                             array('NAME' => 'cerdestx','VALUE' => trim($_POST['cStatus' . $i])          ,'CHECK' => 'SI'),  //Status
                             array('NAME' => 'cerdorix','VALUE' => trim($_POST['cTipoCerti' . $i])       ,'CHECK' => 'SI'),  //Origen
                             array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK' => 'SI'),  //Usuario que creo el registro
                             array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),  //Fecha de creacion
                             array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),  //Hora de creacion 
                             array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),  //Fecha de modificacion
                             array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),  //Hora de modificacion
                             array('NAME' => 'regestxx','VALUE' => "ENPROCESO"                           ,'CHECK' => 'SI')); //Estado

            if (!f_MySql("INSERT","lcde$cPerAno",$qInsert,$xConexion01,$cAlfa)) {
              $nErrorDetalle = 1;
            } else {
              // Actualiza el Subservicio de la MIF con el Consecutivo de la Certificacion
              if ($_POST['cMifComCsc'] != "" && $_POST['cMifId'] != "") {
                $cAnioMif = $_POST['cPerAno'];
                $qMifSubservi  = "SELECT ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.mifdidxx ";
                $qMifSubservi .= "FROM $cAlfa.lmsu$cAnioMif ";
                $qMifSubservi .= "WHERE ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.mifidxxx = \"{$_POST['cMifId']}\" AND ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.sersapxx = \"{$_POST['cCodSapSer' . $i]}\" AND ";
                $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.subidxxx = \"{$_POST['cSubId_Certi' . $i]}\"";
                $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
                $nError = 0;
                if (mysql_num_rows($xMifSubservi) > 0) {
                  while ($xRMS = mysql_fetch_array($xMifSubservi)) {
                    $cConsecutivo = trim(strtoupper($_POST['cComId']))."-".trim(strtoupper($_POST['cComPre']))."-".trim(strtoupper($_POST['cComCsc']));
                    $qUpdateMif   = array(array('NAME'=>'cercscxx','VALUE' => $cConsecutivo      ,'CHECK'=>'SI'),
                                          array('NAME'=>'regestxx','VALUE' => "CERTIFICADO"      ,'CHECK'=>'SI'),
                                          array('NAME'=>'mifdidxx','VALUE' => $xRMS['mifdidxx']  ,'CHECK'=>'WH'));
                    
                    if (!f_MySql("UPDATE","lmsu$cAnioMif",$qUpdateMif,$xConexion01,$cAlfa)) {
                      $nError = 1;
                    }
                  }

                  if ($nError == 1) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "No se pudo actualizar el consecutivo de Certificacion en el detalle de la MIF.\n";
                  }
                }
              }
            }
          }
        }

        // Se actualiza el esatdo de la MIF
        if ($nSwitch == 0 && $_POST['cMifComCsc'] != "" && $_POST['cMifId'] != "") {
          fnActualizaEstadosMif($cAnioMif, $_POST['cMifId']);
        }

        if ($nErrorDetalle == 1) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error actualizando datos en el detalle de la Certificacion.\n";
        }

      break;
      case "CERTIFICAFINANCIERO":
        // Actualiza el registro de cabecera
        $qUpdate = array(array('NAME' => 'cerusufi','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))   ,'CHECK' => 'NO'),
                         array('NAME' => 'cerfecfi','VALUE' => date('Y-m-d H:i:s')                    ,'CHECK' => 'SI'),
                         array('NAME' => 'cerobsfi','VALUE' => $_POST['gObservacion']                 ,'CHECK' => 'NO'),
                         array('NAME' => 'regestxx','VALUE' => "VALIDACION_FINANCIERA"                ,'CHECK' => 'SI'),
                         array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                          ,'CHECK' => 'SI'),
                         array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                          ,'CHECK' => 'SI'),
                         array('NAME' => 'comidxxx','VALUE' => $_POST['gComId']                       ,'CHECK' => 'WH'),
                         array('NAME' => 'comcodxx','VALUE' => $_POST['gComCod']                      ,'CHECK' => 'WH'),
                         array('NAME' => 'comcscxx','VALUE' => $_POST['gComCsc']                      ,'CHECK' => 'WH'),
                         array('NAME' => 'comcsc2x','VALUE' => $_POST['gComCsc2']                     ,'CHECK' => 'WH'));

        if (!f_MySql("UPDATE","lcca$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al actualizar el estado de Validacion Financiera en la cabecera de la Certificacion.\n";
        } else {
          // Actualiza los registros de detalle
          $qCertifiDet  = "SELECT ";
          $qCertifiDet .= "cerdidxx ";
          $qCertifiDet .= "FROM $cAlfa.lcde$cPerAno ";
          $qCertifiDet .= "WHERE ";
          $qCertifiDet .= "$cAlfa.lcde$cPerAno.ceridxxx = \"{$vCertificacion['ceridxxx']}\" ";
          $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
          if (mysql_num_rows($xCertifiDet) > 0) {
            while ($xRCD = mysql_fetch_array($xCertifiDet)) {
              $qUpdate = array(array('NAME' => 'regestxx','VALUE' => "VALIDACION_FINANCIERA"       ,'CHECK' => 'SI'),
                               array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                 ,'CHECK' => 'SI'),
                               array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                 ,'CHECK' => 'SI'),
                               array('NAME' => 'cerdidxx','VALUE' => $xRCD['cerdidxx']             ,'CHECK' => 'WH'));

              if (!f_MySql("UPDATE","lcde$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Error al actualizar el estado de Validacion Financiera en el detalle de la Certificacion.\n";
              }
            }
          }
        }
      break;
      case "APROBADOFINANCIERO":
        // Actualiza el registro de cabecera
        $qUpdate = array(array('NAME' => 'cerusuar','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))   ,'CHECK' => 'NO'),
                         array('NAME' => 'cerfecar','VALUE' => date('Y-m-d H:i:s')                    ,'CHECK' => 'SI'),
                         array('NAME' => 'cerobsar','VALUE' => $_POST['gObservacion']                 ,'CHECK' => 'NO'),
                         array('NAME' => 'regestxx','VALUE' => "CERTIFICADO"                          ,'CHECK' => 'SI'),
                         array('NAME' => 'comidxxx','VALUE' => $_POST['gComId']                       ,'CHECK' => 'WH'),
                         array('NAME' => 'comcodxx','VALUE' => $_POST['gComCod']                      ,'CHECK' => 'WH'),
                         array('NAME' => 'comcscxx','VALUE' => $_POST['gComCsc']                      ,'CHECK' => 'WH'),
                         array('NAME' => 'comcsc2x','VALUE' => $_POST['gComCsc2']                     ,'CHECK' => 'WH'));

        if (!f_MySql("UPDATE","lcca$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al actualizar el estado de Aprobado Financiero en la cabecera de la Certificacion.\n";
        } else {
          // Actualiza los registros de detalle
          $qCertifiDet  = "SELECT ";
          $qCertifiDet .= "cerdidxx ";
          $qCertifiDet .= "FROM $cAlfa.lcde$cPerAno ";
          $qCertifiDet .= "WHERE ";
          $qCertifiDet .= "$cAlfa.lcde$cPerAno.ceridxxx = \"{$vCertificacion['ceridxxx']}\" ";
          $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
          if (mysql_num_rows($xCertifiDet) > 0) {
            while ($xRCD = mysql_fetch_array($xCertifiDet)) {
              $qUpdate = array(array('NAME' => 'regestxx','VALUE' => "CERTIFICADO"        ,'CHECK' => 'SI'),
                               array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')        ,'CHECK' => 'SI'),
                               array('NAME' => 'reghmodx','VALUE' => date('H:i:s')        ,'CHECK' => 'SI'),
                               array('NAME' => 'cerdidxx','VALUE' => $xRCD['cerdidxx']    ,'CHECK' => 'WH'));

              if (!f_MySql("UPDATE","lcde$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Error al actualizar el estado de Aprobado Financiero en el detalle de la Certificacion.\n";
              }
            }
          }
        }
      break;
      case "RECHAZOFINANCIERO":
        // Actualiza el registro de cabecera
        $qUpdate = array(array('NAME' => 'cerusuar','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))   ,'CHECK' => 'SI'),
                         array('NAME' => 'cerfecar','VALUE' => date('Y-m-d H:i:s')                    ,'CHECK' => 'SI'),
                         array('NAME' => 'cerobsar','VALUE' => $_POST['gObservacion']                 ,'CHECK' => 'SI'),
                         array('NAME' => 'regestxx','VALUE' => "ENPROCESO"                            ,'CHECK' => 'SI'),
                         array('NAME' => 'comidxxx','VALUE' => $_POST['gComId']                       ,'CHECK' => 'WH'),
                         array('NAME' => 'comcodxx','VALUE' => $_POST['gComCod']                      ,'CHECK' => 'WH'),
                         array('NAME' => 'comcscxx','VALUE' => $_POST['gComCsc']                      ,'CHECK' => 'WH'),
                         array('NAME' => 'comcsc2x','VALUE' => $_POST['gComCsc2']                     ,'CHECK' => 'WH'));

        if (!f_MySql("UPDATE","lcca$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al actualizar el estado de Rechazo Financiero en la cabecera de la Certificacion.\n";
        } else {
          // Actualiza los registros de detalle
          $qCertifiDet  = "SELECT ";
          $qCertifiDet .= "cerdidxx ";
          $qCertifiDet .= "FROM $cAlfa.lcde$cPerAno ";
          $qCertifiDet .= "WHERE ";
          $qCertifiDet .= "$cAlfa.lcde$cPerAno.ceridxxx = \"{$vCertificacion['ceridxxx']}\" ";
          $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
          if (mysql_num_rows($xCertifiDet) > 0) {
            while ($xRCD = mysql_fetch_array($xCertifiDet)) {
              $qUpdate = array(array('NAME' => 'regestxx','VALUE' => "ENPROCESO"          ,'CHECK' => 'SI'),
                               array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')        ,'CHECK' => 'SI'),
                               array('NAME' => 'reghmodx','VALUE' => date('H:i:s')        ,'CHECK' => 'SI'),
                               array('NAME' => 'cerdidxx','VALUE' => $xRCD['cerdidxx']    ,'CHECK' => 'WH'));

              if (!f_MySql("UPDATE","lcde$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Error al actualizar el estado de Rechazo Financiero en el detalle de la Certificacion.\n";
              }
            }
          }
        }
      break;
      case "CERTIFICAFACTURA":
        // Actualiza el registro de cabecera
        $qUpdate = array(array('NAME' => 'cerusufa','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))   ,'CHECK' => 'SI'),
                         array('NAME' => 'cerfecfa','VALUE' => date('Y-m-d H:i:s')                    ,'CHECK' => 'SI'),
                         array('NAME' => 'cerobsfa','VALUE' => $_POST['gObservacion']                 ,'CHECK' => 'NO'),
                         array('NAME' => 'regestxx','VALUE' => "CERTIFICADO"                          ,'CHECK' => 'SI'),
                         array('NAME' => 'comidxxx','VALUE' => $_POST['gComId']                       ,'CHECK' => 'WH'),
                         array('NAME' => 'comcodxx','VALUE' => $_POST['gComCod']                      ,'CHECK' => 'WH'),
                         array('NAME' => 'comcscxx','VALUE' => $_POST['gComCsc']                      ,'CHECK' => 'WH'),
                         array('NAME' => 'comcsc2x','VALUE' => $_POST['gComCsc2']                     ,'CHECK' => 'WH'));

        if (!f_MySql("UPDATE","lcca$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al actualizar el estado de Certifica para Facturacion en la cabecera de la Certificacion.\n";
        } else {
          // Consulta el Id de la certificacion
          $qCertificacion  = "SELECT ";
          $qCertificacion .= "$cAlfa.lcca$cPerAno.ceridxxx ";
          $qCertificacion .= "FROM $cAlfa.lcca$cPerAno ";
          $qCertificacion .= "WHERE ";
          $qCertificacion .= "$cAlfa.lcca$cPerAno.comidxxx = \"{$_POST['gComId']}\" AND ";
          $qCertificacion .= "$cAlfa.lcca$cPerAno.comcodxx = \"{$_POST['gComCod']}\" AND ";
          $qCertificacion .= "$cAlfa.lcca$cPerAno.comcscxx = \"{$_POST['gComCsc']}\" AND ";
          $qCertificacion .= "$cAlfa.lcca$cPerAno.comcsc2x = \"{$_POST['gComCsc2']}\" LIMIT 0,1 ";
          $xCertificacion  = f_MySql("SELECT","",$qCertificacion,$xConexion01,"");
          $vCertificacion  = array();
          if (mysql_num_rows($xCertificacion) > 0) {
            $vCertificacion = mysql_fetch_array($xCertificacion);
          }

          // Actualiza los registros de detalle
          $qCertifiDet  = "SELECT ";
          $qCertifiDet .= "cerdidxx ";
          $qCertifiDet .= "FROM $cAlfa.lcde$cPerAno ";
          $qCertifiDet .= "WHERE ";
          $qCertifiDet .= "$cAlfa.lcde$cPerAno.ceridxxx = \"{$vCertificacion['ceridxxx']}\" ";
          $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
          if (mysql_num_rows($xCertifiDet) > 0) {
            while ($xRCD = mysql_fetch_array($xCertifiDet)) {
              $qUpdate = array(array('NAME' => 'regestxx','VALUE' => "CERTIFICADO"        ,'CHECK' => 'SI'),
                               array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')        ,'CHECK' => 'SI'),
                               array('NAME' => 'reghmodx','VALUE' => date('H:i:s')        ,'CHECK' => 'SI'),
                               array('NAME' => 'cerdidxx','VALUE' => $xRCD['cerdidxx']    ,'CHECK' => 'WH'));

              if (!f_MySql("UPDATE","lcde$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Error al actualizar el estado de Certifica para Facturacion en el detalle de la Certificacion.\n";
              }
            }
          }
        }
      break;
      case "ANULAR":
        // Actualiza el registro de cabecera
        $qUpdate = array(array('NAME' => 'cerusuan','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))   ,'CHECK' => 'SI'),
                         array('NAME' => 'cerfecan','VALUE' => date('Y-m-d H:i:s')                    ,'CHECK' => 'SI'),
                         array('NAME' => 'cerobsan','VALUE' => $_POST['gObservacion']                 ,'CHECK' => 'SI'),
                         array('NAME' => 'regestxx','VALUE' => "ANULADO"                              ,'CHECK' => 'SI'),
                         array('NAME' => 'comidxxx','VALUE' => $_POST['gComId']                       ,'CHECK' => 'WH'),
                         array('NAME' => 'comcodxx','VALUE' => $_POST['gComCod']                      ,'CHECK' => 'WH'),
                         array('NAME' => 'comcscxx','VALUE' => $_POST['gComCsc']                      ,'CHECK' => 'WH'),
                         array('NAME' => 'comcsc2x','VALUE' => $_POST['gComCsc2']                     ,'CHECK' => 'WH'));

        if (!f_MySql("UPDATE","lcca$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al actualizar el estado de Anulacion en la cabecera de la Certificacion.\n";
        } else {
          // Actualiza los registros de detalle
          $qCertifiDet  = "SELECT ";
          $qCertifiDet .= "lcde$cPerAno.cerdidxx, ";
          $qCertifiDet .= "lcde$cPerAno.sersapxx, ";
          $qCertifiDet .= "lcde$cPerAno.subidxxx, ";
          $qCertifiDet .= "lcde$cPerAno.cerdorix ";
          $qCertifiDet .= "FROM $cAlfa.lcde$cPerAno ";
          $qCertifiDet .= "WHERE ";
          $qCertifiDet .= "$cAlfa.lcde$cPerAno.ceridxxx = \"{$vCertificacion['ceridxxx']}\" ";
          $xCertifiDet  = f_MySql("SELECT","",$qCertifiDet,$xConexion01,"");
          if (mysql_num_rows($xCertifiDet) > 0) {
            while ($xRCD = mysql_fetch_array($xCertifiDet)) {
              $qUpdate = array(array('NAME' => 'regestxx','VALUE' => "ANULADO"            ,'CHECK' => 'SI'),
                               array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')        ,'CHECK' => 'SI'),
                               array('NAME' => 'reghmodx','VALUE' => date('H:i:s')        ,'CHECK' => 'SI'),
                               array('NAME' => 'cerdidxx','VALUE' => $xRCD['cerdidxx']    ,'CHECK' => 'WH'));

              if (!f_MySql("UPDATE","lcde$cPerAno",$qUpdate,$xConexion01,$cAlfa)) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Error al actualizar el estado de Anulacion en el detalle de la Certificacion.\n";
              } else {
                if ($xRCD['sersapxx'] != "" && $xRCD['subidxxx'] != "" && $xRCD['cerdorix'] == "MIF") {
                  fnActualizaEstadoSubserviciosMif($vCertificacion['mifidano'], $vCertificacion['mifidxxx'], $xRCD['sersapxx'], $xRCD['subidxxx'], "ACTIVO");
                }
              }
            }
          }
        }

        if ($vCertificacion['certipxx'] == "AUTOMATICA") {
          $cMifId   = $vCertificacion['mifidxxx'];
          $cAnioMif = $vCertificacion['mifidano'];

          // Valida si tiene subservicios certificados en otro comprobante de Certificacion
          $cConsecutivo = $_POST['gComId']."-".$_POST['$gComPre']."-".$_POST['cComCsc'];
          $qMifSubservi  = "SELECT ";
          $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.* ";
          $qMifSubservi .= "FROM $cAlfa.lmsu$cAnioMif ";
          $qMifSubservi .= "WHERE ";
          $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.mifidxxx = \"$cMifId\" AND ";
          $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.cercscxx != \"$cConsecutivo\" AND ";
          $qMifSubservi .= "$cAlfa.lmsu$cAnioMif.regestxx = \"CERTIFICADO\" ";
          $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
          if (mysql_num_rows($xMifSubservi) == 0) {
            $qUpdate = array(array('NAME' => 'regestxx','VALUE' => "ENPROCESO"       ,'CHECK' => 'SI'),
                             array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')     ,'CHECK' => 'SI'),
                             array('NAME' => 'reghmodx','VALUE' => date('H:i:s')     ,'CHECK' => 'SI'),
                             array('NAME' => 'mifidxxx','VALUE' => $cMifId           ,'CHECK' => 'WH'));

            if (!f_MySql("UPDATE","lmca$cAnioMif",$qUpdate,$xConexion01,$cAlfa)) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Error al actualizar el estado de los Subservicios en la MIF.\n";
            }
          }
        }
       
      break;
      default:
        // No hace nada
      break;
    }
  }

  // echo "<br>";
  // echo $nSwitch;
  // echo "<br>";
  // echo $cMsj;
  // echo "<br>";
  // die();

  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        f_Mensaje(__FILE__,__LINE__,"Se creo la Certificacion con exito.");
      break;
      case "EDITAR":
        f_Mensaje(__FILE__,__LINE__,"Se actualizo la Certificacion con exito.");
      break;
      case "CERTIFICAFINANCIERO":
        f_Mensaje(__FILE__,__LINE__,"Se realizo la Certificacion Financiera con exito.");
      break;
      case "APROBADOFINANCIERO":
        f_Mensaje(__FILE__,__LINE__,"Se realizo la Aprobacion Financiera con exito.");
      break;
      case "RECHAZOFINANCIERO":
        f_Mensaje(__FILE__,__LINE__,"Se realizo el Rechazo Financiero con exito.");
      break;
      case "CERTIFICAFACTURA":
        f_Mensaje(__FILE__,__LINE__,"Se realizo la Certificacion para Facturacion con exito.");
      break;
      case "ANULAR":
        f_Mensaje(__FILE__,__LINE__,"Se Anulo la Certificacion con exito.");
      break;
      default:
        // no hace nada
      break;
    }

    if ($_COOKIE['kModo'] == "NUEVO" || $_COOKIE['kModo'] == "EDITAR") {
      ?>
      <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
      <script languaje = "javascript">
        parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
        document.forms['frgrm'].submit();
      </script> 
      <?php
    } else {
      ?>
        <script type="text/javascript">
          parent.window.fmwork.location="<?php echo $_COOKIE['kIniAnt'] ?>";
        </script>
      <?php
    }
  } else {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
  }

  /**
   * Permite actualizar el estado de los subservicios de la MIF.
   */
  function fnActualizaEstadoSubserviciosMif($cAnio, $cMifId, $cSerSap, $cSubId, $cEstado) {
    global $cAlfa; global $xConexion01;

    // Consulta el subservicio para actualizar el Estado
    $qMifSubservi  = "SELECT ";
    $qMifSubservi .= "$cAlfa.lmsu$cAnio.* ";
    $qMifSubservi .= "FROM $cAlfa.lmsu$cAnio ";
    $qMifSubservi .= "WHERE ";
    $qMifSubservi .= "$cAlfa.lmsu$cAnio.mifidxxx = \"$cMifId\" AND ";
    $qMifSubservi .= "$cAlfa.lmsu$cAnio.sersapxx = \"$cSerSap\" AND ";
    $qMifSubservi .= "$cAlfa.lmsu$cAnio.subidxxx = \"$cSubId\" ";
    $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
    if (mysql_num_rows($xMifSubservi) > 0) {
      while ($xRMS = mysql_fetch_array($xMifSubservi)) {
        $qUpdateMif = array(array('NAME'=>'regestxx','VALUE' => $cEstado           ,'CHECK'=>'SI'),
                            array('NAME'=>'mifdidxx','VALUE' => $xRMS['mifdidxx']  ,'CHECK'=>'WH'));
        f_MySql("UPDATE","lmsu$cAnio",$qUpdateMif,$xConexion01,$cAlfa);
      }
    }
  }

  /**
   * Permite actualizar el estado de la MIF.
   */
  function fnActualizaEstadosMif($cAnio, $cMifId) {
    global $cAlfa; global $xConexion01;

    $nCertiActivo  = 0;
    $nCertiTotal   = 0;

    // Consulta los estados de los subservicios de la MIF
    $qMifSubservi  = "SELECT ";
    $qMifSubservi .= "$cAlfa.lmsu$cAnio.* ";
    $qMifSubservi .= "FROM $cAlfa.lmsu$cAnio ";
    $qMifSubservi .= "WHERE ";
    $qMifSubservi .= "$cAlfa.lmsu$cAnio.mifidxxx = \"$cMifId\" AND ";
    $qMifSubservi .= "$cAlfa.lmsu$cAnio.regestxx != \"INACTIVO\" ";
    $qMifSubservi .= "GROUP BY lmsu$cAnio.sersapxx, lmsu$cAnio.subidxxx";
    $xMifSubservi  = f_MySql("SELECT","",$qMifSubservi,$xConexion01,"");
    // echo $qMifSubservi;
    if (mysql_num_rows($xMifSubservi) > 0) {
      while ($xRMS = mysql_fetch_array($xMifSubservi)) {
        if ($xRMS['regestxx'] == "ACTIVO") {
          $nCertiActivo += 1;
        } elseif ($xRMS['regestxx'] == "CERTIFICADO") {
          $nCertiTotal += 1;
        }
      }

      $cEstado = "";
      if ($nCertiActivo == mysql_num_rows($xMifSubservi)) {
        $cEstado = "ACTIVO";
      } elseif ($nCertiTotal == mysql_num_rows($xMifSubservi)) {
        $cEstado = "CERTIFICADO_TOTAL";
      } else {
        $cEstado = "CERTIFICADO_PARCIAL";
      }

      $qUpdateMif = array(array('NAME'=>'regestxx','VALUE' => $cEstado ,'CHECK'=>'SI'),
                          array('NAME'=>'mifidxxx','VALUE' => $cMifId  ,'CHECK'=>'WH'));
      f_MySql("UPDATE","lmca$cAnio",$qUpdateMif,$xConexion01,$cAlfa);
    }
  }

  /**
   * Clase que permite crear la tabla aualizada de cabecera y detalle en caso de estas no existan.
   */
  class cEstructurasTablasAnualizadasCertificaciones{

    /**
     * Permite crear las tablas anualizadas de la Certificacion.
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
      $mReturnConexionTM = $this->fnConectarDBCertificacion();
      if ($mReturnConexionTM[0] == "true") {
        $xConexionTM = $mReturnConexionTM[1];
      } else {
        $nSwitch = 1;
        for ($nR=1;$nR<count($mReturnConexionTM);$nR++) {
          $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
        }
      }

      if($nSwitch == 0){
        // Creación de tabla anualizada en caso de que no exista
        $qTabExis = "SHOW TABLES FROM $cAlfa LIKE \"lcca$nAnio\"";
        $xTabExis = f_MySql("SELECT","",$qTabExis,$xConexionTM,"");
        if (mysql_num_rows($xTabExis) == 0) {
          $qCreate = "CREATE TABLE IF NOT EXISTS $cAlfa.lcca$nAnio LIKE $cAlfa.lcca$nAnioAnterior ";
          $xCreate = mysql_query($qCreate,$xConexionTM);
          if (!$xCreate) {
            $nSwitch = 1;
            $vReturn[count($vReturn)] = __LINE__."~Error al crear Tabla Anualizada [lcca$nAnio].~".mysql_error($xConexion01);
          } else {
            /**
             * NOTA: Siempre que se cree una llave foránea en la tabla anualizada de cabecera [lccaxxxx] se debe agregar la sentencia sql ALTER TABLE
             */
            $qAlter  = "ALTER TABLE $cAlfa.lcca$nAnio ";
            $qAlter .= "ADD CONSTRAINT lcca{$nAnio}_ibfk_1 FOREIGN KEY (cliidxxx) REFERENCES $cAlfa.lpar0150(cliidxxx),";
            $qAlter .= "ADD CONSTRAINT lcca{$nAnio}_ibfk_2 FOREIGN KEY (depnumxx) REFERENCES $cAlfa.lpar0155(depnumxx),";
            $qAlter .= "ADD CONSTRAINT lcca{$nAnio}_ibfk_3 FOREIGN KEY (cdisapxx) REFERENCES $cAlfa.lpar0008(cdisapxx)";
            $xAlter  = mysql_query($qAlter,$xConexionTM);
            if (!$xAlter) {
              $nSwitch = 1;
              $vReturn[count($vReturn)] = __LINE__."~Error al crear FK Anualizada [lcca$nAnio].~".mysql_error($xConexion01);
            }

            // Valida si NO existe la tabla de detalle para crearla
            $qTabExis = "SHOW TABLES FROM $cAlfa LIKE \"lcde$nAnio\"";
            $xTabExis = f_MySql("SELECT","",$qTabExis,$xConexionTM,"");
            if( mysql_num_rows($xTabExis) == 0 ){
              $qCreate = "CREATE TABLE IF NOT EXISTS $cAlfa.lcde$nAnio LIKE $cAlfa.lcde$nAnioAnterior ";
              $xCreate = mysql_query($qCreate,$xConexionTM);
              if(!$xCreate) {
                $nSwitch = 1;
                $vReturn[count($vReturn)] = __LINE__."~Error al crear Tabla Anualizada [lcde$nAnio].~".mysql_error($xConexion01);
              } else {
                /**
                 * NOTA: Siempre que se cree una llave foránea en la tabla anualizada de detalle [lcdexxxx] se debe agregar la sentencia sql ALTER TABLE
                 */
                $qAlter  = "ALTER TABLE $cAlfa.lcde$nAnio ";
                $qAlter .= "ADD CONSTRAINT lcde{$nAnio}_ibfk_1 FOREIGN KEY (ceridxxx) REFERENCES $cAlfa.lcca$nAnio(ceridxxx),";
                $qAlter .= "ADD CONSTRAINT lcde{$nAnio}_ibfk_2 FOREIGN KEY (sersapxx) REFERENCES $cAlfa.lpar0011(sersapxx),";
                $qAlter .= "ADD CONSTRAINT lcde{$nAnio}_ibfk_3 FOREIGN KEY (sersapxx,subidxxx) REFERENCES $cAlfa.lpar0012(sersapxx,subidxxx),";
                $qAlter .= "ADD CONSTRAINT lcde{$nAnio}_ibfk_4 FOREIGN KEY (obfidxxx) REFERENCES $cAlfa.lpar0004(obfidxxx),";
                $qAlter .= "ADD CONSTRAINT lcde{$nAnio}_ibfk_5 FOREIGN KEY (ufaidxxx) REFERENCES $cAlfa.lpar0006(ufaidxxx),";
                $qAlter .= "ADD CONSTRAINT lcde{$nAnio}_ibfk_6 FOREIGN KEY (cebidxxx) REFERENCES $cAlfa.lpar0010(cebidxxx)";
                $xAlter = mysql_query($qAlter,$xConexionTM);
                if (!$xAlter) {
                  $nSwitch = 1;
                  $vReturn[count($vReturn)] = __LINE__."~Error al crear FK Anualizada [lcde$nAnio].~".mysql_error($xConexion01);
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
    function fnConectarDBCertificacion(){

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
    }##function fnConectarDBCertificacion(){##
  }
?>

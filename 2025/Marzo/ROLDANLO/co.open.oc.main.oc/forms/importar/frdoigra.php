<?php
  /**
   * Graba Nuevo DO/Imp
   * Este programa permite Grabar DO/Imp.
   * @author Cesar Muñoz <opencomex@opencomex.com>
   * @package openComex
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  include("../utility.php");
  include("../class.mysql.php");
  include("../../libs/php/uticcdav.php");
  include("../../ws/opensycx/utiwsout.php");
  include("../../libs/php/uticimpo.php");
  include("../../libs/php/uticcrec.php");
  include("../../config/config.php");
  include("../../libs/php/utiindi.php");
  include("../../libs/php/uticlogp.php");

  switch($cAlfa){
    case "ALMAVIVA":
    case "DEALMAVIVA":
    case "TEALMAVIVA":
      include ("../../ws/almaviva/utiwssap.php");
      include("../../libs/php/utinotal.php");
      include("../../libs/php/uticmcpv.php");
      $oPlanVallejo = new cPlanVallejo();

      /*** Defino la clase para llamar el metodo fnCrearDoSap() ***/
      $oWSOutsap = new cWSOutputPedidoSap();
    break;
    case "DHLEXPRE":
    case "DEDHLEXPRE":
    case "TEDHLEXPRE":
      // Utility para notificación CheckPoints.
      include("../../libs/php/uticdexp.php");
      $oCheckpointsExpress = new cCheckpointsExpress();
      $oProcesosExpress    = new cProcesosExpress();
    break;
    case "ROLDANLO":
    case "DEROLDANLO":
    case "TEROLDANLO":
      if(!in_array($OPENINIT['pathdr']."/opencomex/libs/php/uticrold.php",get_included_files(),true)) { 
        include ($OPENINIT['pathdr']."/opencomex/libs/php/uticrold.php"); 
      }
      $oProcesosRoldan = new cProcesosRoldan();
    break;
  }
  switch($cAlfa){
    case "DHLXXXXX":
    case "DEDHLXXXXX":
    case "TEDHLXXXXX":
      include("../../libs/php/uticdhl.php");
      $oProcesosCargoWise = new cProcesosCargoWise();
      $objProcesosIntegracioneWmsSap = new cProcesosIntegracioneWmsSap();
    break;
  }
  switch($cAlfa){
    case "SIACOSIA":
    case "TESIACOSIP":
    case "DESIACOSIP":
      if (!in_array($OPENINIT['pathdr']."/opencomex/libs/php/uticsiac.php",get_included_files(),true)) {
        include ($OPENINIT['pathdr']."/opencomex/libs/php/uticsiac.php");
      }
      $oProcesosSiaco = new cProcesosSiaco();
    break;
  }
  if($vSysStr['system_activar_modulo_toe'] == "SI"){
    include("../../libs/php/uticmtoe.php");

    #Instancionado Objetos de Inventario TOE
    $objInventarioTOE = new cTOE();
    $objEstructurasTOE = new cEstructurasTOE();
  }
  if($vSysStr['system_activar_modulo_vehiculos'] == "SI"){
    include("../../libs/php/uticmveh.php");

    #Instancionado Objetos de Inventario TOE
    $objSaldoVehiculos       = new cVehiculos();
    $objEstructurasVehiculos = new cEstructurasVehiculos();
  }
  $vBDActivarWsAlmaviva = explode("~",$vSysStr['system_activar_consumo_ws_seven_erp_almaviva']);
  if ( in_array($cAlfa, $vBDActivarWsAlmaviva) == true ) {
    include('../../ws/almaviva/utiwsout.php');
  }

  $oImpo = new cInterfacesDeclaraciones();
  $oVuce = new cInterfacesVUCE();

  /**
   *  Cookie fija
   */
  $kDf = explode("~", $_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb = $kDf[3];
  $kUser = $kDf[4];
  $kLicencia = $kDf[5];
  $swidth = $kDf[6];
  $mysql = new c_Mysql();
  $mysql->cServer = $kMysqlHost;
  $mysql->cUser = $kMysqlUser;
  $mysql->cPass = $kMysqlPass;
  $mysql->cDatab = $kMysqlDb;
  $mysql->f_Conectar();
  $mysql->f_SelecDb();

  $cTipoDo = "IMPORTACION";

  $nAno = date('Y');

  $kModo = $_COOKIE['kModo'];
  $isclonlg = 0;
  if ($kModo == "CLONAR") {
    $kModo = "NUEVO";
    $isclonlg = 1;
  }

  // Buscar y Reemplazar Enter, Tabulador, Doble espacio
  $cBuscar = array('"',"'",chr(9),chr(11),chr(13),chr(32).chr(32),"\r\n","\n","\t");
  $cReempl = array('\"',"\'"," "," "," "," "," "," "," ");

  $zSwitch = 0; // Switch para Vericar la Validacion de Datos
  $cMsj = "";   //Variable para concatenar Errores de Validacion

  if ($kModo == "BORRAR" || $kModo == "ANULAR") {
    if (f_DoiPert($kUser, $_POST['cDoiId'], $_POST['cDoiSfId'], $_POST['cAdmId']) == 0) {
      $zSwitch = 1;
      f_Mensaje(__FILE__, __LINE__, "DO no le pertenece, verifique");
    }
  }

  $vchapry = '';
  $vchapro = '';
  $vcpryid = '';
  $vcpryid2 = '';
  $vclprid = '';
  $vclprid2 = '';

  $zCsc = "";
  $zCsc2 = "";

  /**
   * Primero valido los datos que llegan por metodo POST.
   */
  switch ($kModo) {
    case "NUEVO":
    case "EDITAR":

      $vchapry = trim(strtoupper($_POST['oDoProy']));
      $vchapro = trim(strtoupper($_POST['oLprId']));
      $vcpryid = trim(strtoupper($_POST['cPryId']));
      $vcpryid2 = trim(strtoupper($_POST['cPryId2']));
      $vclprid = trim(strtoupper($_POST['cLprId']));
      $vclprid2 = trim(strtoupper($_POST['cLprId2']));

      /**
       * Validando el Importador.
       */
      if (empty($_POST['cCliId'])) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "El Importador no puede ser vacio, verifique");
      }

      /**
       * Validando Licencia
       */
      $nLic = f_Licencia();
      if ($nLic == 0) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "Error grave de Seguridad otro usuario ingreso con su clave");
      }

      /**
       * Validando el Director.
       */
      if (empty($_POST['cUsrId2'])) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "El Director no puede ser vacio, verifique");
      }


      /**
       * Validando la Sucursal.
       */
      if (empty($_POST['cAdmId'])) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "La Sucursal no puede ser vacia, verifique");
      }

      switch($cAlfa){
        case "ALMAVIVA":
        case "DEALMAVIVA":
        case "TEALMAVIVA":
          /**
           * Validando Ejecutivo de Cuenta solo para ALMAVIVA
           */
          if(empty($_POST['cUsrId4'])){
            $zSwitch = 1;
            f_Mensaje(__FILE__, __LINE__, "El Ejecutivo de Cuenta No Puede Ser Vacio, Verifique.");
          }

          /**
           * Valido Modalidad de Importacion ALMAVIVA
           */
          if(empty($_POST['cMimId'])){
            $zSwitch = 1;
            f_Mensaje(__FILE__, __LINE__, "Modalidad de Importacion No Puede Ser Vacia, Verifique.");
          }

          if($_POST['cMimId'] != ""){
            $qModImp  = "SELECT mimdesxx ";
            $qModImp .= "FROM $cAlfa.zalma012 ";
            $qModImp .= "WHERE ";
            $qModImp .= "mimidxxx = \"{$_POST['cMimId']}\" AND ";
            $qModImp .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
            $xModImp  = f_MySql("SELECT", "", $qModImp, $xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$qModImp."~".mysql_num_rows($xModImp));
            if(mysql_num_rows($xModImp) == 0){
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Modalidad de Importacion Invalida, Verifique.");
            }
          }

          if($_POST['cEenNum'] != "" && $_POST['cEenNum'] != "NOAPLICA"){
            $qModImp  = "SELECT EENNUMXX ";
            $qModImp .= "FROM $cAlfa.SIAI0250 ";
            $qModImp .= "WHERE ";
            $qModImp .= "EENNUMXX = \"{$_POST['cEenNum']}\" AND ";
            $qModImp .= "CLIIDXXX = \"{$_POST['cCliId']}\" AND ";
            $qModImp .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
            $xModImp  = f_MySql("SELECT", "", $qModImp, $xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qModImp."~".mysql_num_rows($xModImp));
            if(mysql_num_rows($xModImp) == 0){
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Entrega Entrante Invalida, Verifique.");
            }
          }

          /**
           * Validando Diplomatico para Porsche
           */
          if (trim(strtoupper($_POST["cDoiDipPo"])) == "ON") {
            $qDatCli  = "SELECT CLIDIPPO ";
            $qDatCli .= "FROM $cAlfa.SIAI0150 ";
            $qDatCli .= "WHERE ";
            $qDatCli .= "CLIIDXXX = \"{$_POST['cCliId']}\" LIMIT 0,1 ";
            $xDatCli  = f_MySql("SELECT", "", $qDatCli, $xConexion01,"");
            $vDatCli  = mysql_fetch_array($xDatCli);
            // f_Mensaje(__FILE__,__LINE__,$qDatCli."~".mysql_num_rows($xDatCli));
            if($vDatCli['CLIDIPPO'] != "SI"){
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "El Importador[{$_POST['cCliId']}], No se Encuentra marcado como Diplomatico para Porsche, Verifique.");
            }
          }
        break;
      }

      /**
       * Validando el Departamento.
       */
      if (empty($_POST['cDepId'])) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "El Departamento no puede ser vacio, verifique");
      }

      /**
       * Validando la Ciudad.
       */
      if (empty($_POST['cCiuId'])) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "La Ciudad no puede ser vacia, verifique");
      }

      /**
       * Validando el Digitador.
       */
      if (empty($_POST['cUsrId'])) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "El Digitador no puede ser vacio, verifique");
      }

      /**
       * Validando el Declarante.
       */
      if (empty($_POST['cDauId'])) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "El Declarante no puede ser vacio, verifique");
      }

      /**
       * Validando el tipo de tramite.
       */
      if (empty($_POST['cDoiTitra'])) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "El Tipo de Tramite no Puede Ser Vacio, verifique");
      } else {
        if ($_POST['cDoiTitra'] == 'DDIRECTO') {
          $_POST['cDaaId'] = '99900';
        }
      }

      $nSim = 0;
      $nSid = 0;
      $nTrd = 0;
      $nPse = 0;
      $nAfl = 0;
      $nAgv = 0;
      $nLva = 0;
      $nLiqPE  = 0;
      $nLiqPV  = 0;
      $nNlfob  = 0;
      $nDgeAci = 0;

      if ($_POST['cDgcPerte'] == "" && $_POST['cDgcPerte2'] == "SI") {
        if ($kUser != "ADMIN") {
          $_POST['cDgcPerte'] = "SI";
          f_Mensaje(__FILE__, __LINE__, "Debe comunicarse con digiComex para para que no sea digitado");
        }
      }

      if ($_POST['cDgcEst'] == "" && $_POST['cDgcEst2'] == "FINALIZADO") {
        $_POST['cDgcEst'] = "FINALIZADO";
        f_Mensaje(__FILE__, __LINE__, "Estado de digitacion es finalizado no se puede cambiar");
      } else {
        if ($_POST['cDgcEst'] == "FINALIZADO" && $_POST['cDgcEst2'] == "") {
          if (strlen($_POST['cDgcUsr']) == 0) {
            $_POST['cDgcEst'] = "";
            f_Mensaje(__FILE__, __LINE__, "Estado de digitacion es finalizado pero no ha asignado digitador");
          } else {
            $_POST['dDgcFec'] = f_Fecha();
            $_POST['cDgcHor'] = f_Hora();
          }
        }
      }

      /**
       * Validacion Parametricas
       */
      $cErrorParametrica = "";
      if (strlen($_POST['cCliId']) > 0) {
        $qDatCli  = "SELECT ";
        $qDatCli .= "CLIIDXXX,";
        $qDatCli .= "CLIFTSXM,";
        $qDatCli .= "CLINOEAX,";
        $qDatCli .= "CLICUARB,";
        $qDatCli .= "CLICUAPT ";
        $qDatCli .= "FROM $cAlfa.SIAI0150 ";
        $qDatCli .= "WHERE ";
        $qDatCli .= "CLIIDXXX = \"{$_POST['cCliId']}\" ";
        $xDatCli  = f_MySql("SELECT", "", $qDatCli, $xConexion01, "");
        //f_Mensaje(__FILE__,__LINE__,$qDatCli,"~",mysql_num_rows($xDatCli));
        $vRDC = mysql_fetch_array($xDatCli);
        //$cValidaParametrica = fldesc("SIAI0150", "CLIIDXXX", "CLIIDXXX", $_POST['cCliId']);
        if ($vRDC['CLIFTSXM'] != "0000-00-00" && $vRDC['CLIFTSXM'] < date("Y-m-d")) {
          f_Mensaje(__FILE__,__LINE__,"Se Encuentra Vencida la Fecha de Tasa Seguro x Mil en el Importador. Verifique");
        }

        if (strlen($vRDC['CLIIDXXX']) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Importador {$_POST['cCliId']} no es valido\n";
        }
      }

      if (strlen($_POST['cCliId2']) > 0) {
        $cValidaParametrica = fldesc("SIAI0150", "CLIIDXXX", "CLIIDXXX", $_POST['cCliId2']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Referido {$_POST['cCliId2']} no es valido\n";
        }
      }

      if (strlen($_POST['cUsrId2']) > 0) {
        $cValidaParametrica = fldesc("SIAI0003", "USRIDXXX", "USRIDXXX", $_POST['cUsrId2']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Director {$_POST['cUsrId2']} no es valido\n";
        }
      }

      switch ($cAlfa) {
        case 'DHLEXPRE':
        case 'TEDHLEXPRE':
        case 'DEDHLEXPRE':
          /**
           * Se valida Ejecutivo de cuenta.
           */
          if ($_POST['cUsrId4'] == "") {
            $zSwitch = 1;
            f_Mensaje(__FILE__, __LINE__, "Debe Asignar Ejecutivo de Cuenta, Verifique");
          }else{
            $qCliId  = "SELECT ";
            $qCliId .= "CLIIDXXX  ";
            $qCliId .= "FROM $cAlfa.SIAI0150 ";
            $qCliId .= "WHERE ";
            $qCliId .= "CLIIDXXX = \"{$_POST['cUsrId4']}\" LIMIT 0,1";
            $xCliId  = f_MySql("SELECT", "", $qCliId, $xConexion01, "");
            if (mysql_num_rows($xCliId) == 0) {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Ejecutivo de Cuenta[{$_POST['cUsrId4']}] no Existe, Verifique");
            }
          }

          //Validando que no se seleccione los dos Checks de FULL DDP y DDP Generico
          if($_POST['cDoiFullD'] == "SI" && $_POST['cDoiDdpGe'] == "SI"){
            $zSwitch = 1;
            f_Mensaje(__FILE__, __LINE__, "Solo se permite seleccionar una opcion Full DDP o DDP Generico.");
          }

          //Validando que no se seleccione los dos Checks DO Parcial y DO Parcial DHL
          if($_POST['cDoiPar'] == "SI" && $_POST['cDoiPaDhl'] == "SI"){
            $zSwitch = 1;
            f_Mensaje(__FILE__, __LINE__, "Solo se permite seleccionar una opcion DO Parcial o DO Parcial DHL.");
          }
        break;
        default:
          /**
           * Si se Escogio Ejecutivo de Cuenta se debe Validar que el Ejecutivo Escogido Exista en openComex y que sea Hijo del
           * Director Escogido
           */
          if (strlen($_POST['cUsrId4']) > 0) {
            $qDatEje  = "SELECT USRIDXXX,USRPROXX ";
            $qDatEje .= "FROM $cAlfa.SIAI0003 ";
            $qDatEje .= "WHERE ";
            $qDatEje .= "$cAlfa.SIAI0003.USRIDXXX = \"{$_POST['cUsrId4']}\" AND ";
            $qDatEje .= "$cAlfa.SIAI0003.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
            $xDatEje  = f_MySql("SELECT", "", $qDatEje, $xConexion01, "");
            //f_Mensaje(__FILE__,__LINE__,$qDatEje."~".mysql_num_rows($xDatEje));
            if (mysql_num_rows($xDatEje) == 0) {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Ejecutivo de Cuenta no Existe, Verifique");
            }else{
              $vDatEje = mysql_fetch_array($xDatEje);
              if (substr_count($vDatEje['USRPROXX'],$_POST['cAdmId']."~105~103") == 0) {
                $zSwitch = 1;
                f_Mensaje(__FILE__, __LINE__, " El Ejecutivo de Cuenta Escogido No se Encuentra Parametrizado con el Perfil Especial, Verifique");
              }
            }
          }
        break;
      }

      /**
       * Si se Escogio Analista de Arancel se debe Validar que el Analista de Arancel Exista en openComex
       */
      if (strlen($_POST['cUsrId5']) > 0) {
        $qDatAar = "SELECT USRIDXXX,USRPROXX ";
        $qDatAar .= "FROM $cAlfa.SIAI0003 ";
        $qDatAar .= "WHERE ";
        $qDatAar .= "$cAlfa.SIAI0003.USRIDXXX = \"{$_POST['cUsrId5']}\" AND ";
        $qDatAar .= "$cAlfa.SIAI0003.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
        $xDatAar = f_MySql("SELECT", "", $qDatAar, $xConexion01, "");
        if (mysql_num_rows($xDatAar) == 0) {
          $zSwitch = 1;
          f_Mensaje(__FILE__, __LINE__, "Analista de Arancel no Existe, Verifique");
        }else{
          $vDatAar = mysql_fetch_array($xDatAar);
          if (substr_count($vDatAar['USRPROXX'],$_POST['cAdmId']."~105~104") == 0) {
            $zSwitch = 1;
            f_Mensaje(__FILE__, __LINE__, "El Analista de Arancel No se Encuentra Parametrizado Con el Perfil Especial, Verifique");
          }
        }
      }

      /**
       * Si se Escogio Analista de Registro se debe Validar que el Analista de Registro Exista en openComex
       */
      if (strlen($_POST['cUsrId6']) > 0) {
        $qDatAre = "SELECT USRIDXXX,USRPROXX ";
        $qDatAre .= "FROM $cAlfa.SIAI0003 ";
        $qDatAre .= "WHERE ";
        $qDatAre .= "$cAlfa.SIAI0003.USRIDXXX = \"{$_POST['cUsrId6']}\" AND ";
        $qDatAre .= "$cAlfa.SIAI0003.REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
        $xDatAre = f_MySql("SELECT", "", $qDatAre, $xConexion01, "");
        if (mysql_num_rows($xDatAre) == 0) {
          $zSwitch = 1;
          f_Mensaje(__FILE__, __LINE__, "Analista de Registro no Existe, Verifique");
        }else{
          $vDatAre = mysql_fetch_array($xDatAre);
          if (substr_count($vDatAre['USRPROXX'],$_POST['cAdmId']."~105~105") == 0) {
            $zSwitch = 1;
            f_Mensaje(__FILE__, __LINE__, "El Analista de Registro No se Encuentra Parametrizado Con el Perfil Especial, Verifique");
          }
        }
      }

      if (strlen($_POST['cAdmId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0119", "LINIDXXX", "LINIDXXX", $_POST['cAdmId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Administracion {$_POST['cAdmId']} no es valida\n";
        }
      }

      if (strlen($_POST['cDepId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0054", "DEPIDXXX", "DEPIDXXX", $_POST['cDepId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Departamento {$_POST['cDepId']} no es valido\n";
        }
      }

      if (strlen($_POST['cCiuId']) > 0) {
        $qCiudad = mysql_query("SELECT CIUIDXXX FROM SIAI0055 WHERE CIUIDXXX =\"{$_POST['cCiuId']}\" AND DEPIDXXX=\"{$_POST['cDepId']}\"");
        $nCiud = mysql_num_rows($qCiudad);
        if ($nCiud <= 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Ciudad/mpio {$_POST['cCiuId']} no es valido\n";
        }
      }

      if (strlen($_POST['cUsrId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0003", "USRIDXXX", "USRIDXXX", $_POST['cUsrId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Digitador {$_POST['cUsrId']} no es valido\n";
        }
      }

      if (strlen($_POST['cDauId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0109", "DAUIDXXX", "DAUIDXXX", $_POST['cDauId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Declarante {$_POST['cDauId']} no es valido\n";
        }
      }

      if (strlen($_POST['cUsrId3']) > 0) {
        $cValidaParametrica = fldesc("SIAI0003", "USRIDXXX", "USRIDXXX", $_POST['cUsrId3']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Comparte con {$_POST['cUsrId3']} no es valido\n";
        }
      }

      if (strlen($_POST['cTdeId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0127", "TDEIDXXX", "TDEIDXXX", $_POST['cTdeId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Tipo declaracion {$_POST['cTdeId']} no es valido\n";
        }
      }

      if (strlen($_POST['cTdtId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0129", "TDTIDXXX", "TDTIDXXX", $_POST['cTdtId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Tipo documento {$_POST['cTdtId']} no es valido\n";
        }
      }

      if (strlen($_POST['cLinId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0119", "LINIDXXX", "LINIDXXX", $_POST['cLinId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Lugar Ingreso {$_POST['cLinId']} no es valido\n";
        }
      }

      if (strlen($_POST['cOdiId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0103", "ODIIDXXX", "ODIIDXXX", $_POST['cOdiId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Oficina Dian {$_POST['cOdiId']} no es valida\n";
        }
      }

      if (strlen($_POST['cMtrId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0120", "MTRIDXXX", "MTRIDXXX", $_POST['cMtrId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Medio transporte {$_POST['cMtrId']} no es valido\n";
        }
      }

      if (strlen($_POST['cMonIdGa']) > 0) {
        $cValidaParametrica = fldesc("SIAI0111", "MONIDXXX", "MONIDXXX", $_POST['cMonIdGa']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Moneda gastos {$_POST['cMonIdGa']} no es valido\n";
        }
      }

      if (strlen($_POST['cDepId2']) > 0) {
        $cValidaParametrica = fldesc("SIAI0054", "DEPIDXXX", "DEPIDXXX", $_POST['cDepId2']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Departamento destino {$_POST['cDepId2']} no es valido\n";
        }
      }

      if (strlen($_POST['cMonIdSe']) > 0) {
        $cValidaParametrica = fldesc("SIAI0111", "MONIDXXX", "MONIDXXX", $_POST['cMonIdSe']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Moneda seguro {$_POST['cMonIdSe']} no es valido\n";
        }
      }

      if (strlen($_POST['cGmoId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0064", "GMOIDXXX", "GMOIDXXX", $_POST['cGmoId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Grupo modalidad {$_POST['cGmoId']} no es valido\n";
        }
      }

      if (strlen($_POST['cTraId']) > 0) {
        $qTraDes  = "SELECT ";
        $qTraDes .= "TRADESXX ";
        $qTraDes .= "FROM $cAlfa.SIAI0133 ";
        $qTraDes .= "WHERE ";
        $qTraDes .= "TRAIDXXX = \"{$_POST['cTraId']}\" AND ";
        $qTraDes .= "TRAODIXX = \"{$_POST['cTraOdi']}\" AND ";
        $qTraDes .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
        $xTraDes  = f_MySql("SELECT","",$qTraDes,$xConexion01,"");
        if (mysql_num_rows($xTraDes) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "El Transportador[{$_POST['cTraId']}] con Administraci&#243;n[{$_POST['cTraOdi']}], No Existe en openComex o se Encuentra Inactivo\n";
        }
      }

      if (strlen($_POST['cDaaId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0110", "DAAIDXXX", "DAAIDXXX", $_POST['cDaaId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Deposito {$_POST['cDaaId']} no es valido\n";
        }
      }

      if (strlen($_POST['cAuxId']) > 0) {
        $vAuxiliares = explode(",", $_POST['cAuxId']);
        for ($i = 0; $i < count($vAuxiliares); $i++) {
          if ($vAuxiliares[$i] != "") {
            $cValidaParametrica = fldesc("SIAI0141", "AUXIDXXX", "AUXIDXXX", $vAuxiliares[$i]);
            if (strlen($cValidaParametrica) == 0) {
              $zSwitch = 1;
              $cErrorParametrica .= "- Auxiliar/Tramitador {$vAuxiliares[$i]} No Existe en la Base de Datos\n";
            }
          }
        }
        $_POST['cAuxId'] = (substr($_POST['cAuxId'], -1) == ",") ? substr($_POST['cAuxId'], 0, strlen($_POST['cAuxId']) - 1) : $_POST['cAuxId'];
      }

      if (strlen($_POST['cAgcId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0154", "AGCIDXXX", "AGCIDXXX", $_POST['cAgcId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Agente de carga {$_POST['cAgcId']} no es valido\n";
        }
      }

      if (strlen($_POST['cPaiId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0052", "PAIIDXXX", "PAIIDXXX", $_POST['cPaiId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Pais procedencia {$_POST['cPaiId']} no es valido\n";
        }
      }

      if (strlen($_POST['cPaiBanId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0052", "PAIIDXXX", "PAIIDXXX", $_POST['cPaiBanId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Bandera {$_POST['cPaiBanId']} no es valida\n";
        }
      }

      if (strlen($_POST['cLprId']) > 0) {
        $cValidaParametrica = fldesc("zalpo003", "lpridxxx", "lpridxxx", $_POST['cLprId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Producto {$_POST['cLprId']} no es valido\n";
        }
      }

      if (strlen($_POST['cLprId2']) > 0) {
        $cValidaParametrica = fldesc("fpar0143", "proidxxx", "prodesxx", $_POST['cLprId2']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Producto {$_POST['cLprId2']} no es valido\n";
        }
      }

      if (strlen($_POST['cPryId']) > 0) {
        $cValidaParametrica = fldesc("siai1101", "pryidxxx", "prydesxx", $_POST['cPryId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Proyecto {$_POST['cPryId']} no es valido\n";
        }
      }

      if (strlen($_POST['cDivId']) > 0) {
        $cValidaParametrica = fldesc("siai1102", "dividxxx", "divdesxx", $_POST['cDivId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Division {$_POST['cDivId']} no es valido\n";
        }
      }

      if (strlen($_POST['cVenId']) > 0) {
        $cValidaParametrica = fldesc("SIAI0150", "CLIIDXXX", "CLIIDXXX", $_POST['cVenId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Vendedor {$_POST['cVenId']} no es valido\n";
        }
      }

      if (strlen($_POST['cCcoAlId']) > 0) {
        $cValidaParametrica = fldesc("zalma002", "ccoidxxx", "ccoidxxx", $_POST['cCcoAlId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Centro de Costo {$_POST['cCcoAlId']} no es valido\n";
        }
      }

      if (strlen($_POST['cTcaId']) > 0) {
        $cValidaParametrica = fldesc("zrol0001", "tcaidxxx", "tcaidxxx", $_POST['cTcaId']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Tipo de Carga {$_POST['cTcaId']} no es valido\n";
        }
      }

      switch($kMysqlDb) {
        case "TEALADUANA":
        case "DEALADUANA":
        case "ALADUANA":
          if (strlen($_POST['cDoiDmeR']) > 100) {
            $zSwitch = 1;
            $cErrorParametrica .= "- Descripcion Mercancia debe ser menor a 100 caracteres\n";
          }

          if (strlen($_POST['cDoiNumVa']) > 50) {
            $zSwitch = 1;
            $cErrorParametrica .= "- Nro. Vapor debe ser menor a 50 caracteres\n";
          }
        break;
      }

      switch($kMysqlDb) {
        case "ROLDANLO":
        case "TEROLDANLO":
        case "DEROLDANLO":
          if($kModo == 'EDITAR') {
            /**
             * Validacion Tipo de Proceso
             */
            $vImportadoresHalli = explode(",",$vSysStr['roldanlo_nit_importador_halliburton']);
            if(in_array($_POST['cCliId'],$vImportadoresHalli)){
              if($_POST['cTipId'] != ""){
                $qLinPro  = "SELECT tipidxxx ";
                $qLinPro .= "FROM $cAlfa.zrol0007 ";
                $qLinPro .= "WHERE ";
                $qLinPro .= "tipidxxx = \"{$_POST['cTipId']}\" AND ";
                $qLinPro .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
                $xLinPro = f_MySql("SELECT","",$qLinPro,$xConexion01,"");
                // f_Mensaje(__FILE__,__LINE__,$qLinPro."~".mysql_num_rows($xLinPro));
                if(mysql_num_rows($xLinPro) == 0){
                  $zSwitch = 1;
                  $cErrorParametrica .= "El Tipo de Proceso [{$_POST['cTipId']}] No existe.\n";
                }
              }
            }
          }
        break;
      }

      /**
       * Validacion Linea Negocio para Interfaz Repuestos Porsche
       */
      $cSysStrBaseDatos = (strlen($cAlfa) == 10) ? strtolower(substr($cAlfa, 2)) : strtolower($cAlfa);
      $mImpRepPor = explode(",",$vSysStr[$cSysStrBaseDatos.'_nit_interfaz_repuestos_porsche']);
      if(in_array($_POST['cCliId'],$mImpRepPor)){
        if($_POST['cLneCod'] != ""){
          $qLinNeg  = "SELECT ";
          $qLinNeg .= "lnecodxx ";
          $qLinNeg .= "FROM $cAlfa.zalma015 ";
          $qLinNeg .= "WHERE ";
          $qLinNeg .= "lnecodxx = \"{$_POST['cLneCod']}\" AND ";
          $qLinNeg .= "cliidxxx = \"{$_POST['cCliId']}\" LIMIT 0,1 ";
          $xLinNeg = f_MySql("SELECT", "", $qLinNeg, $xConexion01, "");
          if(mysql_num_rows($xLinNeg) == 0){
            $zSwitch = 1;
            $cErrorParametrica .= "La Linea de Negocio[{$_POST['cLneCod']}] No Pertenece al Cliente[{$_POST['cCliId']}].\n";
          }
        }
      }

      switch($kMysqlDb) {
        case "DHLXXXXX":
        case "TEDHLXXXXX":
        case "DEDHLXXXXX":
          if ($kModo == 'EDITAR') {
            // Consulto el Importador.
            $qCli	 = "SELECT * ";
            $qCli .= "FROM SIAI0150 ";
            $qCli .= "WHERE ";
            $qCli .= "CLIIDXXX = \"{$_POST['cCliId']}\" AND ";
            $qCli .= "REGESTXX = \"ACTIVO\"";
            $xCli = f_MySql("SELECT","",$qCli,$xConexion01,"");

            $RC = mysql_fetch_assoc($xCli);

            $cCCOIds= array_filter(explode("~", trim($RC['CLICCOXX'], '~')));
            $cDivIds= array_filter(explode("~", trim($RC['CLIDIVXX'], '~')));

            if ( $_POST['cCosDHLId'] == "" && count($cCCOIds) > 0 ) {
              // Si el Id es vacío y el Importador tiene CCOs asignados, mostrar que es requerido.
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Centro de Costos DHL no Puede ser Vacio, Verifique");
            } else if ( $_POST['cCosDHLId'] != "" && count($cCCOIds) > 0 ) {
              // Si el Id no es vacío y el Importador tiene CCOs asignados, comprobar que ese Id esté asignado.
              if (!in_array($_POST['cCosDHLId'], $cCCOIds) ) {
                $zSwitch = 1;
                $cErrorParametrica .= "- Centro de Costos DHL {$_POST['cCosDHLId']} no es valido\n";
              }
            } else if ( $_POST['cCosDHLId'] != "" && count($cCCOIds) == 0 ) {
              // Si el Id no es vacío y el Importador no tiene CCOs asignados, mostrar que no es válido.
              $zSwitch = 1;
              $cErrorParametrica .= "- Centro de Costos DHL {$_POST['cCosDHLId']} no es valido\n";
            }

            if ( $_POST['cDivDHLId'] == "" && count($cDivIds) > 0 ) {
              // Si el Id es vacío y el Importador tiene Divisiones DHL asignadas, mostrar que es requerido.
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Division DHL no Puede ser Vaio, Verifique");
            } else if ( $_POST['cDivDHLId'] != "" && count($cDivIds) > 0 ) {
              // Si el Id no es vacío y el Importador tiene Divisiones DHL asignadas, comprobar que ese Id esté asignado.
              if ( !in_array($_POST['cDivDHLId'], $cDivIds) ) {
                $zSwitch = 1;
                $cErrorParametrica .= "- Division DHL {$_POST['cDivDHLId']} no es valido\n";
              }
            } else if ( $_POST['cDivDHLId'] != "" && count($cDivIds) == 0 ) {
              // Si el Id no es vacío y el Importador no tiene Divisiones DHL asignadas, mostrar que no es válida.
              $zSwitch = 1;
              $cErrorParametrica .= "- Division DHL {$_POST['cDivDHLId']} no es valido\n";
            }

            //Validando que se haya Escogido Pais de Origen.
            if ($_POST['cPaiId'] == "") {
              $zSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
              $cMsj .= "Debe Escoger Pais de Procedencia.\n";
            } else {
              $qDatPaiO = "SELECT ";
              $qDatPaiO .= "$cAlfa.SIAI0052.PAIIDXXX ";
              $qDatPaiO .= "FROM $cAlfa.SIAI0052 ";
              $qDatPaiO .= "WHERE ";
              $qDatPaiO .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$_POST['cPaiId']}\" AND ";
              $qDatPaiO .= "$cAlfa.SIAI0052.REGESTXX = \"ACTIVO\" ";
              $xDatPaiO = f_MySql("SELECT", "", $qDatPaiO, $xConexion01, "");
              if (mysql_num_rows($xDatPaiO) == 0) {
                $zSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                $cMsj .= "El Pais de Procedencia [{$_POST['cPaiId']}] No Existe.\n";
              }
            }

            //Validando que se haya Escogido Pais de Procedencia o Embarque.
            if ($_POST['cPapOe'] == "") {
              $zSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
              $cMsj .= "Debe Escoger Pais de Procedencia o Embarque.\n";
            } else {
              $qDatPaiP = "SELECT ";
              $qDatPaiP .= "$cAlfa.SIAI0052.PAIIDXXX ";
              $qDatPaiP .= "FROM $cAlfa.SIAI0052 ";
              $qDatPaiP .= "WHERE ";
              $qDatPaiP .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$_POST['cPapOe']}\" AND ";
              $qDatPaiP .= "$cAlfa.SIAI0052.REGESTXX = \"ACTIVO\" ";
              $xDatPaiP = f_MySql("SELECT", "", $qDatPaiP, $xConexion01, "");
              if (mysql_num_rows($xDatPaiP) == 0) {
                $zSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                $cMsj .= "El Pais de Procedencia o Embarque [{$_POST['cPapOe']}] No Existe.\n";
              }
            }

            //Validando que se haya Escogido Ciudad de Origen.
            if ($_POST['cCiuIdO'] == "") {
              $zSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
              $cMsj .= "Debe Escoger Ciudad de Procedencia o Embarque.\n";
            } else {
              $qDatCiuO = "SELECT ";
              $qDatCiuO .= "$cAlfa.SIAI0055.CIUIDXXX ";
              $qDatCiuO .= "FROM $cAlfa.SIAI0055 ";
              $qDatCiuO .= "WHERE ";
              $qDatCiuO .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$_POST['cPapOe']}\" AND ";
              $qDatCiuO .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$_POST['cDepIdO']}\" AND ";
              $qDatCiuO .= "$cAlfa.SIAI0055.CIUISOXX = \"{$_POST['cCiuIdO']}\" AND ";
              $qDatCiuO .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
              $xDatCiuO = f_MySql("SELECT", "", $qDatCiuO, $xConexion01, "");
              if (mysql_num_rows($xDatCiuO) == 0) {
                $zSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                $cMsj .= "La Ciudad de Procedencia o Embarque [{$_POST['cCiuIdO']}] No Existe ";
                $cMsj .= "para el Pais de Procedencia o Embarque [{$_POST['cPapOe']}].\n";
              }
            }

            //Validando que se haya Escogido Pais Destino.
            if ($_POST['cPaiIdD'] == "") {
              $zSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
              $cMsj .= "Debe Escoger Pais Destino.\n";
            } else {
              $qDatPaiD = "SELECT ";
              $qDatPaiD .= "$cAlfa.SIAI0052.PAIIDXXX ";
              $qDatPaiD .= "FROM $cAlfa.SIAI0052 ";
              $qDatPaiD .= "WHERE ";
              $qDatPaiD .= "$cAlfa.SIAI0052.PAIIDXXX = \"{$_POST['cPaiIdD']}\" AND ";
              $qDatPaiD .= "$cAlfa.SIAI0052.REGESTXX = \"ACTIVO\" ";
              $xDatPaiD = f_MySql("SELECT", "", $qDatPaiD, $xConexion01, "");
              if (mysql_num_rows($xDatPaiD) == 0) {
                $zSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                $cMsj .= "El Pais Destino [{$_POST['cPaiIdD']}] No Existe.\n";
              }
            }

            //Validando que se haya Escogido Ciudad de Origen.
            if ($_POST['cCiuIdD'] == "") {
              $zSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
              $cMsj .= "Debe Escoger Ciudad Destino.\n";
            } else {
              $qDatCiuD = "SELECT ";
              $qDatCiuD .= "$cAlfa.SIAI0055.CIUIDXXX ";
              $qDatCiuD .= "FROM $cAlfa.SIAI0055 ";
              $qDatCiuD .= "WHERE ";
              $qDatCiuD .= "$cAlfa.SIAI0055.PAIIDXXX = \"{$_POST['cPaiIdD']}\" AND ";
              $qDatCiuD .= "$cAlfa.SIAI0055.DEPIDXXX = \"{$_POST['cDepIdD']}\" AND ";
              $qDatCiuD .= "$cAlfa.SIAI0055.CIUISOXX = \"{$_POST['cCiuIdD']}\" AND ";
              $qDatCiuD .= "$cAlfa.SIAI0055.REGESTXX = \"ACTIVO\" ";
              $xDatCiuD = f_MySql("SELECT", "", $qDatCiuD, $xConexion01, "");
              if (mysql_num_rows($xDatCiuD) == 0) {
                $zSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                $cMsj .= "La Ciudad Destino [{$_POST['cCiuIdD']}] No Existe ";
                $cMsj .= "para el Pais Destino [{$_POST['cPaiIdD']}].\n";
              }
            }
            if($cMsj != ""){
              f_Mensaje(__FILE__, __LINE__, $cMsj);
            }
          }
        break;
      }

      if (strlen($_POST['cSucCom']) > 0) {
        $cValidaParametrica = fldesc("SIAI0119", "LINIDXXX", "LINIDXXX", $_POST['cSucCom']);
        if (strlen($cValidaParametrica) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- Sucursal comercial {$_POST['cSucCom']} no es valida\n";
        }
      }

      //Codigo para obligar vendedor y sucursal comercial si el modulo de comisiones esta activo
      if ($vSysStr['financiero_fecha_instalacion_modulo_comisiones_vendedores'] != "") {
        if (strlen($_POST['cSucCom']) == 0 || strlen($_POST['cVenId']) == 0) {
          $zSwitch = 1;
          $cErrorParametrica .= "- El Modulo de Comisiones esta Activo, debe Seleccionar Vendedor y Sucursal Comercial\n";
        }
      }

      if (strlen($cErrorParametrica) > 0) {
        f_Mensaje(__FILE__, __LINE__, $cErrorParametrica);
      }

      /**
       * fin Validacion Parametricas
       */
      if (trim(strtoupper($_POST["cDoiApcri"])) == "ON") {
        $cDoiApcri = "SI";
        $cDoiFpcri = date("Y-m-d");
        $cDoiHpcri = date("H:i:s");
      } else {
        $cDoiApcri = "NO";
        $cDoiFpcri = "";
        $cDoiHpcri = "";
      }

      if (trim(strtoupper($_POST["cDgeSim"])) == "ON") {
        $nSim = 1;
      }

      if (trim(strtoupper($_POST["cDgeSid"])) == "ON") {
        $nSid = 1;
      }

      if (trim(strtoupper($_POST["cDgeTrd"])) == "ON") {
        $nTrd = 1;
      }

      if (trim(strtoupper($_POST["cDoiPse"])) == "ON") {
        $nPse = 1;
      }

      if (trim(strtoupper($_POST["cDgeAfl"])) == "ON") {
        $nAfl = 1;
      }

      if (trim(strtoupper($_POST["cDgeAga"])) == "ON") {
        $nAgv = 1;
      }

      if (trim(strtoupper($_POST["cDgeLva"])) == "ON") {
        $nLva = 1;
      }

      if (trim(strtoupper($_POST["cDgeLiqPE"])) == "ON") {
        $nLiqPE = 1;
      }

      if (trim(strtoupper($_POST["cDgeLiqPV"])) == "ON") {
        $nLiqPV = 1;
      }

      if (trim(strtoupper($_POST["cDgeNlFob"])) == "ON") {
        $nNlfob = 1;
      }

      if (trim(strtoupper($_POST["cDgeAci"])) == "ON") {
        $nDgeAci = 1;
      }

      if ($nTrd == 1 && $nPse == 1) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "No puede seleccionar al mismo tiempo, tributos diferidos y pago electronico , verifique");
      }

      if ($nTrd == 0 && $vRDC['CLINOEAX'] != "") {
        f_Mensaje(__FILE__,__LINE__,"El Importador tiene Asignado Codigo OEA / Resolucion OEA, pero usted No seleccion&oacute; que al tr&aacute;mite aplica Tributos Diferidos. \nPor favor Verifique.");
      }

      if ($nTrd == 0 && $vRDC['CLICUARB'] != "") {
        f_Mensaje(__FILE__,__LINE__,"El Importador tiene Asignado Codigo Usuario Aduanero Bajo Riesgo, pero usted No seleccion&oacute; que al tr&aacute;mite aplica Tributos Diferidos. \nPor favor verifique.");
      }

      if ($nTrd == 0 && $vRDC['CLICUAPT'] != "") {
        f_Mensaje(__FILE__,__LINE__,"El Importador tiene Asignado Codigo Usuario UTS, pero usted No seleccion&oacute; que al tr&aacute;mite aplica Tributos Diferidos. \nPor favor verifique.");
      }

      if ($_POST['nCliPts'] > 0 && $_POST['nDgeSge'] > 0) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "No se debe digitar Tasa Seguro X Mil y Seguro Especifico simultaneamente, verifique");
      }

      if($_POST['cPaiId'] != '' && (isset($_POST['cDoiCpro']) && $_POST['cDoiCpro'] == '')){
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "Si Existe Pais de Procedencia Debe Seleccionar una Ciudad de Procedencia, verifique.");
      }

      if($_POST['cPaiId'] != '' && (isset($_POST['cDoiDdes']) && $_POST['cDoiDdes'] == '')){
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "Si Existe Pais de Procedencia Debe Seleccionar una Ciudad de Destino, verifique.");
      }

      if($_POST['cDoiCdes'] != ''){
        $qCiuP  = "SELECT ";
        $qCiuP .= "SIAI0055.CIUIDXXX ";
        $qCiuP .= "FROM SIAI0055 ";
        $qCiuP .= "WHERE ";
        $qCiuP .= "SIAI0055.PAIIDXXX = \"{$_POST['cPaiId']}\" AND ";
        $qCiuP .= "SIAI0055.DEPIDXXX = \"{$_POST['cDoiDpro']}\" AND ";
        $qCiuP .= "SIAI0055.CIUIDXXX = \"{$_POST['cDoiCpro']}\" ";
        $xCiuP = $mysql->f_Ejecutar($qCiuP);

        if (mysql_num_rows($xCiuP) == 0) {
          $zSwitch = 1;
          f_Mensaje(__FILE__, __LINE__, "La Ciudad de Procedencia no Existe, verifique.");
        }
      }

      if($_POST['cCiuDesdes'] != ''){
        $qSqlCiuD = "SELECT DISTINCT SIAI0055.CIUDESXX ";
        $qSqlCiuD .= "FROM SIAI0055 ";
        $qSqlCiuD .= "LEFT JOIN SIAI0052 ON SIAI0055.PAIIDXXX = SIAI0052.PAIIDXXX ";
        $qSqlCiuD .= "WHERE (SIAI0055.PAIIDXXX = \"CO\" OR SIAI0052.PAIDESXX LIKE \"%ZONA FRANCA%\") ";
        $qSqlCiuD .= "AND SIAI0055.DEPIDXXX = \"{$zRDtg['dexdidpr']}\" AND  SIAI0055.CIUIDXXX = \"{$zRDtg['dexcidpr']}\" LIMIT 0,1";
        $cSqlCiuD = mysql_query($qSqlCiuP);
        $zSqlCiuD = mysql_fetch_array($cSqlCiuP);
        $xCiuD = $mysql->f_Ejecutar($qCiuD);

        if (mysql_num_rows($xCiuD) == 0) {
          $zSwitch = 1;
          f_Mensaje(__FILE__, __LINE__, "La Ciudad de Destino no Existe, verifique.");
        }
      }

      if (empty($_POST['cUsrId3'])) {
        $_POST['cUsrId3'] = $_POST['cUsrId2'];
      }

      if ($nTrd == 1 && ($_POST["cCliUap"] == "" && $vRDC['CLINOEAX'] == "" && $vRDC['CLICUARB'] == "" && $vRDC['CLICUAPT'] == "")) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "Solo puede Seleccionar Tributos Diferidos si el importador posee Nro. UAP, o Codigo OEA / Resolucion OEA o Codigo Usuario Aduanero Bajo Riesgo o Codigo Usuario UTS. Verifique.");
      }

      # Validando si es una legalizacion y si las fecha de doc.transporte es mayor a la fecha de manifiesto
      if(isset($_POST['cLegal'])){
        if($_POST['cLegal'] == "1"){
          if ($_POST['cTdeId'] == "2"){
              if( $_POST['dDgeFmc']>$_POST['dDgeFdt'] ){
                f_Mensaje(__FILE__, __LINE__, "La fecha de  Doc. Transporte no puede ser menor que la fecha de manifiesto");
                $zSwitch = 1;
              }
          } else{
            f_Mensaje(__FILE__, __LINE__, "Para aplicar acta de legalizacion debe seleccionar tipo de declaracion LEGALIZACION.");
            $zSwitch = 1;
          }
        }
      } else {
        if (1 * (str_replace("-", "", $_POST["dDgeFdt"])) > 0 && 1 * (str_replace("-", "", $_POST["dDgeFmc"])) > 0) {
          if (1 * (str_replace("-", "", $_POST["dDgeFdt"])) > 1 * (str_replace("-", "", $_POST["dDgeFmc"]))) {
            if (trim(strtoupper($_POST["cDoiDta"])) != "ON" && $_POST["cTdtId"] !="3") {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Fecha de Guia no puede ser Mayor a Fecha de Manifiesto, verifique");
            }
          }
        }
      }

      if (0 + $_POST["nDgePnt"] > 0 + $_POST["nDgePbr"]) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "Peso Neto no puede ser Mayor a Peso Bruto, verifique");
      }

      if (substr_count($vSysStr['alpopular_db_aplica'], $kMysqlDb) > 0) {
        if (strlen($_POST['cDivId']) == 0) {
          $zSwitch = 1;
          f_Mensaje(__FILE__, __LINE__, "Debe Digitar Division, verifique");
        }
        if (strlen($_POST['cPryId']) == 0) {
          $zSwitch = 1;
          f_Mensaje(__FILE__, __LINE__, "Debe Digitar Proyecto, verifique");
        }
      }

      /**
       * Validando si se Escogio Aplica trayecto, se haya diligenciado los dias que aplican
       */
      if ($_POST['cDoiAtrA'] != "" && $_POST['cDoiDatRa'] == "") {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "Debe Digitar Dias de Trayecto, verifique");
      }

      /**
       * Validando que si se diligenciaron los dias, el dato sea un numero
       */
      if ($_POST['cDoiAtrA'] != "" && $_POST['cDoiDatRa'] != "" && (is_numeric($_POST['cDoiDatRa'])) == false) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "Debe Digitar un Numero en Dias de Trayecto, Verifique");
      }

      if ($vchapry == "ON" && $vchapro == "ON") {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "Aplica Proyecto y aplica Producto al mismo tiempo, verifique");
      }

      if ($vSysStr['system_aplica_financiero'] == "SI") {
        if ($vchapry == "ON") {
          if (substr_count($vSysStr['alpopular_db_aplica'], $kMysqlDb) > 0) {
            if (strlen($vcpryid) == 0) {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Aplica Proyecto, pero esta vacio, verifique");
            }
          } else {
            if (strlen($vcpryid2) == 0) {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Aplica Proyecto, pero esta vacio, verifique");
            }
          }
        }

        if ($vchapry == "OFF" || $vchapry == '') {
          if (substr_count($vSysStr['alpopular_db_aplica'], $kMysqlDb) > 0) {

          } else {
            if (strlen($vcpryid2) > 0) {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "No Aplica Proyecto, pero esta digitado, verifique");
            }
          }
        }
        if ($vchapro == "ON") {
          if (substr_count($vSysStr['alpopular_db_aplica'], $kMysqlDb) > 0) {
            if (strlen($vclprid) == 0) {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Aplica Producto, pero esta vacio, verifique");
            }
          } else {
            if (strlen($vclprid2) == 0) {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Aplica Producto, pero esta vacio, verifique");
            }
          }
        }

        if ($vchapro == "OFF" || $vchapro == '') {
          if (substr_count($vSysStr['alpopular_db_aplica'], $kMysqlDb) > 0) {
            if (strlen($vclprid) > 0) {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "No Aplica Producto, pero esta digitado, verifique");
            }
          } else {
            if (strlen($vclprid2) > 0) {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "No Aplica Producto, pero esta digitado, verifique");
            }
          }
        }
      }

      if (!empty($_POST['cLprId3'])) {
        //valido que exista la linea de producto
        $zSql = mysql_query("SELECT LPRIDXXX from SIAI0238 WHERE LPRIDXXX = \"{$_POST['cLprId3']}\" ");
        if (mysql_num_rows($zSql) == 0 ) {
          $zSwitch = 1;
          f_Mensaje(__FILE__, __LINE__, "La Linea de Producto no existe, verifique");
        }
      }

      if ($vSysStr['system_aplica_financiero'] == "SI") {
        //Validacion Vendedor y Sucursal Comercial
        $zSq002 = mysql_query("SELECT strvlrxx from sys00002 WHERE stridxxx = \"financiero_asignar_centro_de_costo_de_sucursal_comercial_a_do\" LIMIT 0,1");
        $cAsiCos = "";
        while (($zRAC = mysql_fetch_array($zSq002)) != false) {
          $cAsiCos = $zRAC['strvlrxx'];
        }

        if ($cAsiCos == "SI") {
          if (strlen($_POST['cSucCom']) == 0 || strlen($_POST['cVenId']) == 0) {
            $zSwitch = 1;
            f_Mensaje(__FILE__, __LINE__, "Debe tener Vendedor y Sucursal Comercial, Verifique");
          } else {
            $issuc1 = fldesc("fpar0008", "sucidxxx", "sucidxxx", $_POST['cSucCom']);
            $issuc2 = fldesc("fpar0008", "sucidxxx", "sucidxxx", $_POST['cAdmId']);
            if (strlen($issuc1) == 0 || strlen($issuc2) == 0) {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Debe Tener Sucursal DO y Comercial, Verifique");
            } else {
              //Validacion movimiento contable
              if ($kModo == "EDITAR" && $zSwitch == 0) {
                if ($_POST['cSucCom'] != $_POST['cSucCom2'] && strlen($_POST['cSucCom2']) > 0) {
                  $aact = substr(f_Fecha(), 0, 4);
                  $aant = $aact - 1;
                  $zsMC = mysql_query("SELECT comidxxx,comcodxx,comcscxx,comseqxx FROM fcod$aact WHERE comcsccx = \"{$_POST['cDoiId']}\" and comseqcx = \"{$_POST['cDoiSfId']}\" and regestxx = \"ACTIVO\") UNION (SELECT comidxxx,comcodxx,comcscxx,comseqxx FROM fcme$aact WHERE comcsccx = \"{$_POST['cDoiId']}\" and comseqcx = \"{$_POST['cDoiSfId']}\" and regestxx = \"ACTIVO\")");
                  $nCCom = mysql_num_rows($zsMC);
                  $zsMC2 = mysql_query("SELECT comidxxx,comcodxx,comcscxx,comseqxx FROM fcod$aant WHERE comcsccx = \"{$_POST['cDoiId']}\" and comseqcx = \"{$_POST['cDoiSfId']}\" and regestxx = \"ACTIVO\") UNION (SELECT comidxxx,comcodxx,comcscxx,comseqxx FROM fcme$aant WHERE comcsccx = \"{$_POST['cDoiId']}\" and comseqcx = \"{$_POST['cDoiSfId']}\" and regestxx = \"ACTIVO\")");
                  $nCCom2 = mysql_num_rows($zsMC2);

                  if ($nCCom > 0 || $nCCom2 > 0) {
                    $zSwitch = 1;
                    f_Mensaje(__FILE__, __LINE__, "No puede modificar la Sucursal Comercial por que el tramite registra movimiento contable, Por favor revisar el movimiento del DO");
                  }
                }
              }
              //Fin Validacion movimiento contable
              //VALIDO QUE EL VENDEDOR PERTENEZCA AL IMPORTADOR
              $zVends = mysql_query("SELECT CLIVENXX,CLIVENCO,SUCCOMXX FROM SIAI0150 WHERE CLIIDXXX = \"{$_POST['cCliId']}\" LIMIT 0,1");
              while (($zRVS = mysql_fetch_array($zVends)) != false) {
                $nEncontro = 0;
                if (strlen($zRVS['CLIVENXX']) > 0) {
                  $aCVen = explode("~", $zRVS['CLIVENXX']);
                  for ($jj = 0; $jj < count($aCVen); $jj++) {
                    if ($_POST['cVenId'] == $aCVen[$jj]) {
                      $nEncontro = 1;
                      $jj = count($aCVen);
                    }
                  }
                }
                if ($nEncontro == 0) {
                  $zSwitch = 1;
                  f_Mensaje(__FILE__, __LINE__, "El Vendedor [{$_POST['cVenId']}-{$_POST['cVenNom']}] no pertenece al Importador [{$_POST['cCliId']}], Verifique");
                }
              }
            }
          }
        }
        //Fin Validacion Vendedor y Sucursal Comercial
      }

      if ($kModo == "NUEVO" && $zSwitch == 0) {
        /**
         * Traer el siguiente Consecutivo
         */
        $zSqlCsc = "SELECT LINCSCXX FROM SIAI0119 WHERE LINIDXXX = \"{$_POST["cAdmId"]}\" LIMIT 0,1";
        $zResCsc = $mysql->f_Ejecutar($zSqlCsc);
        if ($mysql->f_ContarFilas($zResCsc) > 0) {
          while (($zRCsc = mysql_fetch_array($zResCsc)) != false) {
            $zCsc = $zRCsc["LINCSCXX"];
            $zCsc2 = 1 + $zCsc;
          }
        } else {
          $zSwitch = 1;
          f_Mensaje(__FILE__, __LINE__, "No se obtuvo el Consecutivo para DO /Imp, verifique");
        }
      }

      switch ($cAlfa) {
        case 'ROLDANLO':
        case 'TEROLDANLO':
        case 'DEROLDANLO':
          /**
           * Validando Oficina Operadora
           */
          if ($_POST['cSccId'] == "") {
            $zSwitch = 1;
            f_Mensaje(__FILE__,__LINE__,"La Oficina Operadora / Subcentro de Costo No Puede Ser Vacia, Verifique");
          }

          if ($_POST['cCcoId'] == "") {
            $zSwitch = 1;
            f_Mensaje(__FILE__,__LINE__,"El Centro de Costo de la Oficina Operadora / Subcentro de Costo No Puede Ser Vacio, Verifique");
          }

          if($_POST['cCcoId'] != "" && $_POST['cSccId'] != ""){
            $qOfiOpe = "SELECT ccoidxxx, sccidxxx ";
            $qOfiOpe .= "FROM $cAlfa.fpar0120 ";
            $qOfiOpe .= "WHERE ";
            $qOfiOpe .= "ccoidxxx = \"{$_POST['cCcoId']}\" AND ";
            $qOfiOpe .= "sccidxxx = \"{$_POST['cSccId']}\" AND ";
            $qOfiOpe .= "sccestdo = \"\" AND ";
            $qOfiOpe .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
            $xOfiOpe = f_MySql("SELECT", "", $qOfiOpe, $xConexion01, "");
            // f_Mensaje(__FILE__,__LINE__,$qOfiOpe."~".mysql_num_rows($xOfiOpe));
            if(mysql_num_rows($xOfiOpe) == 0){
              $zSwitch = 0;
              f_Mensaje(__FILE__,__LINE__,"La Oficina Operadora / Subcentro {$_POST['cSccId']} No Existe o se Encuentra Inactiva, Verifique");
            }
          }

          /*** Validando que el centro de costos este activo. ***/
          $qCenCos  = "SELECT ccoidxxx ";
          $qCenCos .= "FROM $cAlfa.fpar0116 ";
          $qCenCos .= "WHERE ";
          $qCenCos .= "ccoidxxx = \"{$_POST['cCcoId']}\" AND ";
          $qCenCos .= "regestxx = \"ACTIVO\" LIMIT 0,1";
          $xCenCos  = f_MySql("SELECT","",$qCenCos,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qCenCos."~".mysql_num_rows($xCenCos));
          if(mysql_num_rows($xCenCos) == 0){
            $zSwitch = 1;
            f_Mensaje(__FILE__,__LINE__,"El Centro de Costos [{$_POST['cCcoId']}] No existe o se Encuentra Inactivo.");
          }

          // Validación de caracteres prohibidos en el campo de Pedido
          if($_POST['cDoiPed'] != ""){
            if (preg_match("/[\\\\'\"\$\°\<\>\*\{\}\[\]\^\|]/", $_POST['cDoiPed'])) {
              $zSwitch = 1;
              f_Mensaje(__FILE__,__LINE__,"No se permite diligenciar los caracteres \\ , &#39; , \" , \$ , &deg; , < , > , * , { , } , [ , ] , ^ , | en el Campo de Pedido");
            }
          }
        break;
      }

      /**
       * Validacion ALMAVIVA
       * Si el documeto de transporte se repite para otro DO diferente,
       * y no se ha digitado observacion se debe mostrar el DO
       */
      switch ($cAlfa) {
        case 'DEALMAVIVA':
        case 'TEALMAVIVA':
        case 'ALMAVIVA':
          if($kModo == "EDITAR"){

            /**
             * Si se digita Documento Transporte, Validar que se digite Fecha Doc. Transporte
             */
            if(trim($_POST['cDgeDt']) != "" && ($_POST['dDgeFdt'] == "0000-00-00" || $_POST['dDgeFdt'] == "")){
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Se Digito Documento de Transporte [{$_POST['cDgeDt']}], Debe Ingresar Fecha Doc. Transporte.");
            }

            if (trim($_POST['cDgeDt']) != "" && trim($_POST['cDgeDt']) != trim($_POST['cDgeDt_ori']) ) {

              /**
               * Consulto si el documento de transporte existe en otro DO del sistema
               */
              $qDocTraRe  = "SELECT ";
              $qDocTraRe .= "DOIIDXXX,";
              $qDocTraRe .= "DOISFIDX,";
              $qDocTraRe .= "ADMIDXXX,";
              $qDocTraRe .= "CLIIDXXX ";
              $qDocTraRe .= "FROM $cAlfa.SIAI0200 ";
              $qDocTraRe .= "WHERE ";
              $qDocTraRe .= "DGEDTXXX = \"{$_POST['cDgeDt']}\" AND ";
              $qDocTraRe .= "REGESTXX = \"ACTIVO\" ";
              $xDocTraRe  = f_MySql("SELECT","",$qDocTraRe,$xConexion01,"");
              if(mysql_num_rows($xDocTraRe) > 0) {
                $nRepetido = 0;
                $cRepetido = "";
                while ($xRDT = mysql_fetch_array($xDocTraRe)) {
                  if (!($xRDT['DOIIDXXX'] == $_POST['cDoiId'] &&
                    $xRDT['DOISFIDX'] == $_POST['cDoiSfId'] &&
                    $xRDT['ADMIDXXX'] == $_POST['cAdmId'])) {

                    $qNomImp  = "SELECT IF( CLINOMXX = \"\", CONCAT( CLINOM1X, if(CLINOM2X != \"\", \" \", \"\")), CLINOMXX) AS CLINOMXX ";
                    $qNomImp .= "FROM $cAlfa.SIAI0150 ";
                    $qNomImp .= "WHERE ";
                    $qNomImp .= "CLIIDXXX = \"{$xRDT['CLIIDXXX']}\" ";
                    $xNomImp  = f_MySql("SELECT","",$qNomImp,$xConexion01,"");
                    // f_Mensaje(__FILE__,__LINE__,$qNomImp." ~ ".mysql_num_rows($xNomImp));
                    $vNomImp = mysql_fetch_array($xNomImp);

                    $cRepetido .= "DO: {$xRDT['ADMIDXXX']}-{$xRDT['DOIIDXXX']}-{$xRDT['DOISFIDX']}, Importador: {$vNomImp['CLINOMXX']} ({$xRDT['CLIIDXXX']}).\n";
                    $nRepetido++;
                  }
                }
                if ($nRepetido > 0) {
                  if (trim($_POST['cObsDtRep']) == ""){
                    $zSwitch = 1;
                    $cMsjDtRep  = "El Documento de Transporte Se Encuentra Asignado al:\n";
                    $cMsjDtRep .= "$cRepetido";
                    $cMsjDtRep .= "Debe Digitar la Justificacion.";
                    f_Mensaje(__FILE__, __LINE__,$cMsjDtRep);
                  }
                }
              } ## if(mysql_num_rows($xDocTraRe) > 0) { ##

              /**
               * Consulto si el documento de transporte existe en otro DO del sistema
               */
              $qDocTraCms  = "SELECT cliidxxx ";
              $qDocTraCms .= "FROM $cAlfa.zalma016 ";
              $qDocTraCms .= "WHERE ";
              $qDocTraCms .= "dtcmsdtx = \"{$_POST['cDgeDt']}\" AND ";
              $qDocTraCms .= "regestxx = \"ACTIVO\" ";
              $xDocTraCms  = f_MySql("SELECT","",$qDocTraCms,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qDocTraCms."~".mysql_num_rows($xDocTraCms));
              if(mysql_num_rows($xDocTraCms) > 0) {
                $vCliId    = array();
                $cRepetido = "";
                while ($xRDTC = mysql_fetch_array($xDocTraCms)) {
                  if(!(in_array("{$xRDTC['cliidxxx']}", $vCliId))){
                    $vCliId[count($vCliId)] = "{$xRDTC['cliidxxx']}";

                    $qNomImp  = "SELECT IF( CLINOMXX = \"\", CONCAT( CLINOM1X, if(CLINOM2X != \"\", \" \", \"\")), CLINOMXX) AS CLINOMXX ";
                    $qNomImp .= "FROM $cAlfa.SIAI0150 ";
                    $qNomImp .= "WHERE ";
                    $qNomImp .= "CLIIDXXX = \"{$xRDTC['cliidxxx']}\" ";
                    $xNomImp  = f_MySql("SELECT","",$qNomImp,$xConexion01,"");
                    // f_Mensaje(__FILE__,__LINE__,$qNomImp." ~ ".mysql_num_rows($xNomImp));
                    $vNomImp = mysql_fetch_array($xNomImp);

                    $cRepetido .= "{$vNomImp['CLINOMXX']} ({$xRDTC['cliidxxx']}).\n";
                  }

                }
                if(count($vCliId) > 0){
                  if (trim($_POST['cObsDtRep']) == ""){
                    $zSwitch = 1;
                    $cMsjDtCms  = "El Documento de Transporte Se Encuentra Asignado en CMS Para el(los) Importador(es):\n";
                    $cMsjDtCms .= "$cRepetido";
                    $cMsjDtCms .= "Debe Digitar la Justificacion.";
                    f_Mensaje(__FILE__, __LINE__,$cMsjDtCms);
                  }
                }
              }
            } ## if ($_POST['cDgeDt'] != "") { ##
          }

        break;
        default:
          //No hace nada
        break;
      }

      switch($kMysqlDb) {
        case "ALMACAFE":
        case "TEALMACAFE":
        case "DEALMACAFE":
          if($kModo == 'EDITAR') {
            if ($_POST['cDoiAcaFe'] == "") {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Debe Seleccionar SI o NO en Cafe, verifique");
            }
          }
        break;
      }

      /**
      * Validación ControlyManejodeEmbalajesRacks
      */
      switch($kMysqlDb) {
        case "HINOMOTO":
        case "TEHINOMOTO":
        case "DEHINOMOTO":
          if ($_POST['cDgeDt'] != "" && $_POST['cDoiReeXp'] == true) {
            $qConDo  = "SELECT ";
            $qConDo .= "ADMIDXXX, ";
            $qConDo .= "DOIIDXXX, ";
            $qConDo .= "DOISFIDX  ";
            $qConDo .= "FROM $cAlfa.SIAI0200 ";
            $qConDo .= "WHERE ";
            $qConDo .= "DGEDTXXX  = \"{$_POST['cDgeDt']}\" AND ";
            $qConDo .= "DOIREEXP  = \"SI\" AND ";
            $qConDo .= "CONCAT(DOIIDXXX,'~',DOISFIDX,'~',ADMIDXXX) !=  \"{$_POST['cDoiId']}\"'~'\"{$_POST['cDoiSfId']}\"'~'\"{$_POST['cAdmId']}\" LIMIT 0,1";
            $xConDo  = f_MySql("SELECT","",$qConDo,$xConexion01,"");
            if (mysql_num_rows($xConDo) == 1) {
              $vConDo  = mysql_fetch_array($xConDo);
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "El Documento de Transporte[".$_POST['cDgeDt']."], Ya Fue Asignado a otra Reexportacion para el Tramite[".$vConDo['ADMIDXXX']."-".$vConDo['DOIIDXXX']."-".$vConDo['DOISFIDX']."]. Verifique");
            }
          }
        break;
      }

      /**
       * Validación CEVA
       * En el graba de Datos Generales del Do, se debe validar que si en la tabla SIAI0253, 
       * existen registros por el campo DOIIDXXX, DOISFIDX, ADMIDXXX, DGEDTXXX 
       * y se está cambiando el numero de documento de transporte, 
       * se genere un error y se muestre el siguiente mensaje: 
       * "No se Permite el Cambio del Número del Documento de Transporte, ya que se encontraron contenedores asociados con este número para el Do."  
       * @lastmoddt: 2018-08-01
       */
      switch($kMysqlDb) {
        case "TECEVAXXXX":
        case "DECEVAXXXX":
        case "CEVAXXXX":
          if($kModo == 'EDITAR') {
            if ($_POST['cDgeDt'] != $_POST['cDgeDt_ori']) {
              $qContenedores  = "SELECT ";
              $qContenedores .= "ADMIDXXX, ";
              $qContenedores .= "DOIIDXXX, ";
              $qContenedores .= "DOISFIDX, ";
              $qContenedores .= "SERSECXX, ";
              $qContenedores .= "CONNUMXX ";
              $qContenedores .= "FROM $cAlfa.SIAI0253 ";
              $qContenedores .= "WHERE ";
              $qContenedores .= "DOIIDXXX = \"{$_POST['cDoiId']}\" AND ";
              $qContenedores .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
              $qContenedores .= "ADMIDXXX = \"{$_POST['cAdmId']}\" AND ";
              $qContenedores .= "DGEDTXXX = \"{$_POST['cDgeDt_ori']}\" AND ";
              $qContenedores .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
              $xContenedores  = f_MySql("SELECT","",$qContenedores,$xConexion01,"");
              if (mysql_num_rows($xContenedores) > 0){  
                $zSwitch = 1;
                f_Mensaje(__FILE__, __LINE__, "No se Permite el Cambio del ".utf8_decode("Número")." del Documento de Transporte, ya que se encontraron contenedores asociados con este ".utf8_decode("número")." para el Do.");
              }
            }
          }
        break;
      }

      //Validando si Aplica Trayecto
      if ($_POST['cDoiAtrA'] != ""){
        if ($_POST['cTdeId'] != "3" ){
          $zSwitch = 1;
          f_Mensaje(__FILE__, __LINE__, "Si Aplica Trayecto Debe Seleccionar Tipo Declaracion Anticipada. Verifique.");
        }else{
          if ($_POST['cMtrId'] == "" ){
            $zSwitch = 1;
            f_Mensaje(__FILE__, __LINE__, "Si Aplica Trayecto, el Medio de Transporte no Puede ser Vacio. Verifique.");
          }else{
            switch ($_POST['cMtrId']) {
              case '1':
              case '4':
                if($_POST['cDoiAtrA'] != "T. LARGO" && $_POST['cDoiAtrA'] != "T. CORTO" && $_POST['cDoiAtrA'] != "OTROS"){
                  $zSwitch = 1;
                  f_Mensaje(__FILE__, __LINE__, "El Medio de Transporte no Aplica al Tipo de Trayecto seleccionado. Verifique");
                }

                if ($_POST['cDoiAtrA'] == "T. LARGO") {
                  if ($_POST['cDoiDatRa'] != '5') {
                    $zSwitch = 1;
                    f_Mensaje(__FILE__, __LINE__, "El valor de los D&iacute;as de Trayecto, para la opci&oacute;n Aplica Trayecto[".$_POST['cDoiAtrA']."], debe ser igual a [5].");
                  }
                }

                if ($_POST['cDoiAtrA'] == "T. CORTO") {
                  if ($_POST['cDoiDatRa'] != '1') {
                    $zSwitch = 1;
                    f_Mensaje(__FILE__, __LINE__, "El valor de los D&iacute;as de Trayecto, para la opci&oacute;n Aplica Trayecto[".$_POST['cDoiAtrA']."], debe ser igual a [1].");
                  }
                }
              break;
              case '3':
                if($_POST['cDoiAtrA'] != "CUALQUIER T." && $_POST['cDoiAtrA'] != "OTROS"){
                  $zSwitch = 1;
                  f_Mensaje(__FILE__, __LINE__, "El Medio de Transporte no Aplica al Tipo de Trayecto seleccionado. Verifique");
                }

                if ($_POST['cDoiAtrA'] == "CUALQUIER T.") {
                  if ($_POST['cDoiDatRa'] != '1') {
                    $zSwitch = 1;
                    f_Mensaje(__FILE__, __LINE__, "El valor de los D&iacute;as de Trayecto, para la opci&oacute;n Aplica Trayecto[".$_POST['cDoiAtrA']."], debe ser igual a [1].");
                  }
                }
              break;
              default:
                if($_POST['cDoiAtrA'] != "OTROS"){
                  $zSwitch = 1;
                  f_Mensaje(__FILE__, __LINE__, "El Medio de Transporte no Aplica al Tipo de Trayecto seleccionado. Verifique");
                }
              break;
            }
          }
        }
      }//if ($_POST['cDoiAtrA'] != ""){

      /**
       * Validando la Fecha de Creacion.
       */
      if (empty($_POST['dRegFec'])) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "La Fecha de Creacion no puede ser vacia, Verifique");
      }

      /**
       * Validando la Hora de Modificacion.
       */
      if (empty($_POST['cRegHor'])) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "La Hora de Modificacion No Puede Ser Vacia, Verifique");
      }

      /**
       * Validando la Fecha de Modificacion.
       */
      if (empty($_POST['dRegMod'])) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "La Fecha de Modificacion No Puede Ser Vacia, Verifique");
      }

      if ($_POST['dRegMod'] != f_Fecha()) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "La Fecha de Modificacion Debe Ser la Actual, Verifique");
      }

      /**
       * Validando el Estado .
       */
      if (empty($_POST['cRegEst'])) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "El Campo Estado No Puede Estar Vacio, Verifique");
      }

      if ($_POST['cRegEst'] != "ACTIVO") {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "El Estado Debe Estar ACTIVO, Verifique");
      }

      /**
       * Consulto si se debe excluir el Do de la Alerta Terminos en Deposito
       */
      $cAler = "";
      if (trim(strtoupper($_POST["cExcAler"])) == "ON") {
        $cAler = "SI";
      } else {
        $cAler = "NO";
      }

      /**
       * Validacion de Documento de Transporte - Agente de Carga
       */
      switch ($cAlfa) {
        case "DEDHLXXXXX":
        case "TEDHLXXXXX":
        case "DHLXXXXX":
          $nActualizarIntEst = 0;
          if($kModo == "EDITAR"){

            $vAgentesCarga = explode(",",$vSysStr['dhlxxxxx_agente_carga_handover_cargowise']);
            if(in_array($_POST['cAgcId'], $vAgentesCarga)){
              if($_POST['cDoiDtm'] == "" && $_POST['cDoiEei20'] != "on"){
                $zSwitch = 1;
                f_Mensaje(__FILE__, __LINE__,"El Documento de Transporte Master, No Puede Ser Vacio");  
              }
              if($_POST['cDoiDtm'] != "" && ($_POST['cDoiRefCw'] == "" || $_POST['cIntId'] == "")){
                $zSwitch = 1;
                f_Mensaje(__FILE__, __LINE__,"La Referencia CargoWise y/o Identificador del Documento de Transporte Master, No Puede Ser Vacia.");
              }
              if($_POST['cDgeDt'] == ""){
                $zSwitch = 1;
                f_Mensaje(__FILE__, __LINE__,"El Documento de Transporte, No Puede Ser Vacio");
              }
            }

            if($_POST['cDgeDt'] != "" && $_POST['cAgcId'] == ""){
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Debe Indicar Agente de Carga Internacional, para Diligenciar Documento de Transporte, Verifique");
            }

            if($_POST['cIntId'] != "" && $_POST['cIntId'] != $_POST['cIntIdO'] && $_POST['cDoiDtm'] != "" && $_POST['cDoiDtm'] != $_POST['cDoiDtmO']){

              $cNits = "\"".implode("\",\"", $vAgentesCarga)."\"";
              $qDatDo  = "SELECT INTIDXXX ";
              $qDatDo .= "FROM $cAlfa.SIAI0200 ";
              $qDatDo .= "WHERE ";
              $qDatDo .= "CONCAT(DOIIDXXX,\"~\",DOISFIDX,\"~\",ADMIDXXX) != \"{$_POST['cDoiId']}~{$_POST['cDoiSfId']}~{$_POST['cAdmId']}\" AND ";
              $qDatDo .= "DGEDTXXX = \"{$_POST['cDgeDt']}\" AND ";
              $qDatDo .= "AGCIDXXX IN ($cNits) AND ";
              $qDatDo .= "DOIREFCW != \"\" LIMIT 0,1";
              $xDatDo  = f_MySql("SELECT","",$qDatDo,$xConexion01,"");
              // f_Mensaje(__FILE__, __LINE__,$qDatDo."~".mysql_num_rows($xDatDo));
              $vDatDo = mysql_fetch_array($xDatDo);

              $qIntCdz123  = "SELECT intidxxx ";
              $qIntCdz123 .= "FROM $cAlfa.zdhl0017 ";
              $qIntCdz123 .= "WHERE ";
              if(mysql_num_rows($xDatDo) > 0){
                $qIntCdz123 .= "intidxxx = \"{$vDatDo['INTIDXXX']}\" ";
              }else{
                $qIntCdz123 .= "intidxxx = \"{$_POST['cIntId']}\" AND ";
                $qIntCdz123 .= "intdtrma = \"{$_POST['cDoiDtm']}\" AND ";
                $qIntCdz123 .= "inttipxx = \"HANDOVER\" AND ";
                $qIntCdz123 .= "intestxx = \"SINASIGNAR\" ";
              }
              $xIntCdz123  = f_MySql("SELECT","",$qIntCdz123,$xConexion01,"");  
              if (mysql_num_rows($xIntCdz123) == 0) {
                $zSwitch = 1;
                f_Mensaje(__FILE__, __LINE__,"No se encontraron Registros para Asignar al Do con Documento de Transporte Master");  
              } else {
                $nActualizarIntEst = 1;
              }
            }

            /*if($_POST['cDgeDt_ori'] != "" && $_POST['cDgeDt_ori'] != $_POST['cDgeDt']){
              if($_POST['cDoiRefCw'] != ""){
                $zSwitch = 1;
                f_Mensaje(__FILE__, __LINE__, "No esta autorizado para actualizar Documento de Transporte conforme a politicas de calidad de Cargo Wise One - Por favor comuniquese con el Administrador del Sistema de Opencomex, Verifique");
              }
            }*/
          }
        break;
      }

      switch ($kMysqlDb) {
        case "DHLEXPRE":
        case "TEDHLEXPRE":
        case "DEDHLEXPRE":
          if($_POST['cDoiCorI'] != ""){
            $qCodOri  = "SELECT ";
            $qCodOri .= "ciacodxx,";
            $qCodOri .= "ciadesxx,";
            $qCodOri .= "regestxx ";
            $qCodOri .= "FROM $cAlfa.zdex0001 ";
            $qCodOri .= "WHERE ";
            $qCodOri .= "ciacodxx = \"{$_POST['cDoiCorI']}\" LIMIT 0,1";
            $xCodOri  = f_MySql("SELECT","",$qCodOri,$xConexion01,"");
            $vCodOri  = mysql_fetch_array($xCodOri);
            if(mysql_num_rows($xCodOri) == 0){
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__,"La Ciudad Origen[{$_POST['cDoiCorI']}], No Existe en la Base de Datos.");
            }else{
              if($vCodOri['regestxx'] == "INACTIVO"){
                $zSwitch = 1;
                f_Mensaje(__FILE__, __LINE__,"La Ciudad Origen[{$_POST['cDoiCorI']}], se Encuentra INACTIVA.");
              }
            }
          }else{
            $zSwitch = 1;
            f_Mensaje(__FILE__, __LINE__,"La Ciudad Origen, No Puede ser vacia.");
          }

          if($_POST['cDoiCidEs'] != ""){
            $qCodDes  = "SELECT ";
            $qCodDes .= "ciacodxx,";
            $qCodDes .= "ciadesxx,";
            $qCodDes .= "regestxx ";
            $qCodDes .= "FROM $cAlfa.zdex0001 ";
            $qCodDes .= "WHERE ";
            $qCodDes .= "ciacodxx = \"{$_POST['cDoiCidEs']}\" LIMIT 0,1";
            $xCodDes  = f_MySql("SELECT","",$qCodDes,$xConexion01,"");
            $vCodDes  = mysql_fetch_array($xCodDes);
            if(mysql_num_rows($xCodDes) == 0){
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__,"La Ciudad Destino[{$_POST['cDoiCidEs']}], No Existe en la Base de Datos.");
            }else{
              if($vCodDes['regestxx'] == "INACTIVO"){
                $zSwitch = 1;
                f_Mensaje(__FILE__, __LINE__,"La Ciudad Destino[{$_POST['cDoiCidEs']}], se Encuentra INACTIVA.");
              }
            }
          }else{
            $zSwitch = 1;
            f_Mensaje(__FILE__, __LINE__,"La Ciudad Destino, No Puede ser Vacia.");
          }

          // Almacena el texto PreAlerta
          if($kModo == "NUEVO") {
            $cTextoPrealerta =  "";
            if($_POST['cCliId'] != "" && $nSwitch == 0){
              $vDatos = array();
              $vDatos['CLIIDXXX'] = $_POST['cCliId'];
              $vDatos['TEXTOPRE'] = 'CLITAIMP';
              $mReturnTextosPrealertas = $oProcesosExpress->fnTextosPrealertas($vDatos);
              $cTextoPrealerta = $mReturnTextosPrealertas[1];
              if($mReturnTextosPrealertas[0] == "false"){
                $cMsjAlerta = "";
                $zSwitch = 1;
                for ($nE=2; $nE < count($mReturnTextosPrealertas); $nE++){
                  $cMsjAlerta .= $mReturnTextosPrealertas[$nE] ."\n";
                }
                f_Mensaje(__FILE__, __LINE__,$cMsjAlerta."\n.Verifique.");
              }else if($cTextoPrealerta != "" && $_POST['cLeido'] != "SI"){
                $zSwitch = 1;
                f_Mensaje(__FILE__, __LINE__,"Debe Aceptar que ha Le&iacute;do las Condiciones, de lo contrario no se permite Crear el Tr&aacute;mite.");
              }
            }
          }

        break;
      }

      switch ($cAlfa) {
        case "SIACOSIA":
        case "TESIACOSIP":
        case "DESIACOSIP":
          if ($_POST['cPemId'] != "") {
            $qPueEmb  = "SELECT ";
            $qPueEmb .= "REGESTXX ";
            $qPueEmb .= "FROM $cAlfa.RIM00121 ";
            $qPueEmb .= "WHERE ";
            $qPueEmb .= "PEMIDXXX = \"{$_POST['cPemId']}\" AND ";
            $qPueEmb .= "PEMPAIID = \"{$_POST['cPaiId']}\" LIMIT 0,1 ";
            $xPueEmb  = f_MySql("SELECT","",$qPueEmb,$xConexion01,"");
            $vPueEmb  = mysql_fetch_array($xPueEmb);
            if (mysql_num_rows($xPueEmb) == 0) {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__,"El Puerto de Embarque[{$_POST['cPemId']}], con Pais Procedencia[{$_POST['cPaiId']}] No Existe en la Base de Datos");
            }else{
              if($vPueEmb['REGESTXX'] == "INACTIVO"){
                $zSwitch = 1;
                f_Mensaje(__FILE__, __LINE__,"El Puerto de Embarque[{$_POST['cPemId']}], on Pais Procedencia[{$_POST['cPaiId']}] Se Encuentra Inactivo");
              }
            }
          }

          if($_POST['cTdeId'] == "3"){
            $vDatAnt = array();
            $vDatAnt['ADMIDXXX'] = $_POST['cAdmId'];
            $vDatAnt['DOIIDXXX'] = $_POST['cDoiId'];
            $vDatAnt['DOISFIDX'] = $_POST['cDoiSfId'];
            $vDatAnt['TDEIDXXX'] = $_POST['cTdeId'];
            $vDatAnt['MTRIDXXX'] = $_POST['cMtrId'];
            $vDatAnt['PAIIDXXX'] = $_POST['cPaiId'];
            $vDatAnt['PEMIDXXX'] = $_POST['cPemId'];
            $vDatAnt['LINIDXXX'] = $_POST['cLinId'];
            $vDatAnt['CLIIDXXX'] = $_POST['cCliId'];
						$vDatAnt['DAAIDXXX'] = $_POST['cDaaId'];
            $mReturnControlAnticipadas = $oProcesosSiaco->fnControlAnticipadas($vDatAnt);
            if($mReturnControlAnticipadas[0] == "false"){
              $zSwitch = 1;
              $cMsjAnt = "";
              for($nE=2; $nE<count($mReturnControlAnticipadas); $nE++) {
                $cMsjAnt = $mReturnControlAnticipadas[$nE]."\n";
              }
              f_Mensaje(__FILE__, __LINE__, $cMsjAnt);
            }else{
              $vDatAplTra = $mReturnControlAnticipadas[1];
              if($vDatAplTra[2] != "EXCLUIDO"){
                if($vDatAplTra[0] == "" || $vDatAplTra[1] == ""){
                  f_Mensaje(__FILE__, __LINE__,"El Tipo de Declaracion Seleccionado es ANTICIPADA y No se Encontr&oacute; parametrizaci&oacute;n de Aplica Trayecto, para realizar control de trayecto y dias de trayecto");
                }

                if($_POST['cDoiAtrA'] != $vDatAplTra[0] && $zSwitch == 0){
                  $zSwitch = 1;
                  f_Mensaje(__FILE__, __LINE__,"Para el Modo de Transporte, el Trayecto que Aplica es {$vDatAplTra[0]}.  Tabular o seleccionar el Tipo de declaracion para cargar el valor.");
                }

                if($_POST['cDoiDatRa'] != $vDatAplTra[1] && $zSwitch == 0){
                  $zSwitch = 1;
                  f_Mensaje(__FILE__, __LINE__,"Para el Modo de Transporte, los d&iacute;as de trayecto deben ser {$vDatAplTra[1]}.  Tabular o seleccionar el Tipo de declaracion para cargar el valor.");
                }
              }
            }
          }
        break;
      }
    break;
    case "ANULAR":
      /**
       * Validando el Estado del Producto.
       */
      if ($_POST['cRegEst'] == "INACTIVO" || $_POST['cRegEst'] == "ACTIVO") {

      } else {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "El Estado Debe Estar Inactivo o Activo, Verifique");
      }

      /**
       * Validando que el DO no tenga movimiento contable.
       */
      $cSqlPro = "SELECT REGFECXX ";
      $cSqlPro .= "FROM SIAI0200 ";
      $cSqlPro .= "WHERE ";
      $cSqlPro .= "DOIIDXXX = \"{$_POST['cDoiId']}\" AND ";
      $cSqlPro .= "DOISFIDX = \"{$_POST['cDoiSfId']}\"  AND ";
      $cSqlPro .= "ADMIDXXX = \"{$_POST['cAdmId']}\" LIMIT 0,1";
      $zCrsPro  = mysql_query($cSqlPro);

      if (mysql_num_rows($zCrsPro) == 0) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "EL DO no Existe, Verifique");
      } else {
        if ($_POST['cRegEst'] == "INACTIVO") {
          $zRPro = mysql_fetch_array($zCrsPro);
          $rRet = f_Bloquear_Importador($_POST['cDoiId'], $_POST['cDoiSfId'], substr($zRPro['REGFECXX'], 0, 4),"",$_POST['cAdmId']);
          $mysql->f_Conectar();
          $mysql->f_SelecDb();
          if ($rRet == 0) {
            $zSwitch = 1;
            f_Mensaje(__FILE__, __LINE__, "EL DO [{$_POST['cAdmId']}-{$_POST['cDoiId']}-{$_POST['cDoiSfId']}] no Puede Ser Anulado Porque Tiene Movimiento Contable, Verifique");
          }
        }
      }

      if ($_POST['cRegEst'] == "ACTIVO") {
        $qItems  = "SELECT ";
        $qItems .= "SIAI0205.ITEIDXXX,";
        $qItems .= "SIAI0205.ITENUEXX,";
        $qItems .= "SIAI0205.FACIDXXX,";
        $qItems .= "SIAI0205.PROIDXXX,";
        $qItems .= "SIAI0205.SFDIDXXX,";
        $qItems .= "SIAI0205.SFDTIPXX,";
        $qItems .= "SIAI0205.SFDID2XX,";
        $qItems .= "SIAI0205.SFDTIP2X,";
        $qItems .= "SIAI0205.ITECANDV,";
        $qItems .= "SIAI0205.ITECMSPV,"; // Cantidad de saldos migrado desde Plan Vallejo
        $qItems .= "SIAI0205.RESIDXXX,"; // Id de reserva plan vallejo.
        $qItems .= "SIAI0205.ITECSHXX,"; // Consecutivo Accesorios.
        $qItems .= "SIAI0152.NEGIDXXX ";
        $qItems .= "FROM SIAI0205 ";
        $qItems .= "LEFT JOIN SIAI0152 ON SIAI0205.SFDIDXXX = SIAI0152.SFDIDXXX AND ";
        $qItems .= "SIAI0205.SFDTIPXX = SIAI0152.SFDTIPXX ";
        $qItems .= "WHERE ";
        $qItems .= "SIAI0205.DOIIDXXX = \"{$_POST['cDoiId']}\" AND ";
        $qItems .= "SIAI0205.DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
        $qItems .= "SIAI0205.ADMIDXXX = \"{$_POST['cAdmId']}\" AND SIAI0205.SFDTIPXX != \"\" ";
        $xItems = $mysql->f_Ejecutar($qItems);
        // f_Mensaje(__FILE__,__LINE__,$qItems."~".mysql_num_rows($xItems));
        while ($vItems = mysql_fetch_array($xItems)) {
          $vItems['SFDTIPXX'] = ($vItems['NEGIDXXX'] != "0" && $vItems['SFDTIPXX'] == "FACTURA") ? "OPENSMART" : $vItems['SFDTIPXX'];
          switch ($vItems['SFDTIPXX']) {
            case "ORDENC":
            case "FACTURA":
            case "OPENSMART":
            case "SALDOSDELL":
            case "DANFOSS":
            case "ASTARA":
            case "PFIZER":
              $vFiltros['SFDIDXXX'] = $vItems['SFDIDXXX'];
              $vFiltros['SFDTIPXX'] = $vItems['SFDTIPXX'];
              $mResultados = $oImpo->fnCalculoSaldosItemsFactura($vFiltros);
              $mValidacionSaldos = $mResultados[1];
              $mValidacionSaldos[0]['PVVITEXX'] = ($mValidacionSaldos[0]['PVVITEXX'] != "") ? $mValidacionSaldos[0]['PVVITEXX'] : 0;
              if ($mValidacionSaldos[0]['PVVITEXX'] < $vItems['ITECANDV']) {
                // f_Mensaje(__FILE__,__LINE__,$vItems['SFDIDXXX']."~".$mValidacionSaldos[0]['PVITEXXX']."<".$vItems['ITECANDV']);
                $zSwitch = 1;
                $sError .= "el saldo del consecutivo [".$vItems['SFDIDXXX']."] asignado al item [".$vItems['ITEIDXXX']."] es menor a la cantidad del item \n";
              }

              //Liberando Saldos de Accesorios
              switch($cAlfa){
                case "SIACOSIA":
                case "TESIACOSIP":
                case "DESIACOSIP":
                  if($vItems['ITECSHXX'] != ""){
                    $vLiberarAccesorios['SFDIDXXX'] = $vItems['ITECSHXX'];
                    $mReturnLiberarAccesorios = $oImpo->fnLiberarSaldosAccesorios($vLiberarAccesorios);
                    if($mReturnLiberarAccesorios[0] == "false"){
                      $zSwitch = 1;
                      for($nTLA=1;$nTLA<count($mReturnLiberarAccesorios);$nTLA){
                        $sError .= $mReturnLiberarAccesorios[$nTLA]."\n";
                      }
                    }
                  }
                break;
              }
            break;
            case "REGISTRO":
              $vFiltros['SIVIDXXX'] = $vItems['SFDIDXXX'];
              $vFiltros['SFDTIPXX'] = $vItems['SFDTIPXX'];
              $mResultados = $oVuce->fnCalculoSaldosItemsVUCE($vFiltros);
              $mValidacionSaldos = $mResultados[1];
              $mValidacionSaldos[0]['PVVITEXX'] = ($mValidacionSaldos[0]['PVVITEXX'] != "") ? $mValidacionSaldos[0]['PVVITEXX'] : 0;
              if ($mValidacionSaldos[0]['PVVITEXX'] < $vItems['ITECANDV']) {
                $zSwitch = 1;
                $sError .= "el saldo del consecutivo [".$vItems['SFDIDXXX']."] asignado al item [".$vItems['ITEIDXXX']."] es menor a la cantidad del item \n";
              }
            break;
            case "PVALLEJO":
              $vData['RESIDXXX'] = $vItems['RESIDXXX'];
              $nValidacionSaldo  = $oPlanVallejo->fnCalcularSaldosReserva($vData);
              if ( $nValidacionSaldo < $vItems['ITECMSPV']) {

                $qUsuRes  = "SELECT ";
                $qUsuRes .= "resusrxx ";
                $qUsuRes .= "FROM $cAlfa.cspv1003 ";
                $qUsuRes .= "WHERE ";
                $qUsuRes .= "residxxx = \"{$vItems['RESIDXXX']}\" LIMIT 0,1 ";
                $xUsuRes  = f_MySql("SELECT","",$qUsuRes,$xConexion01,"");
                // f_Mensaje(__FILE__,__LINE__,$qUsuRes."~".mysql_num_rows($xUsuRes));
                $vUsuRes  = mysql_fetch_array($xUsuRes);

                $zSwitch = 1;
                $sError .= "Para el Item [".$vItems['ITEIDXXX']."], No Existe Saldo de Reserva Disponible para el Usuario [".$vUsuRes['resusrxx']."] y Producto [".$vItems['PROIDXXX']."].\n";
              }
            break;
          }

          switch ($vItems['SFDTIP2X']) {
            case "REGISTRO":
              $vFiltros['SIVIDXXX'] = $vItems['SFDID2XX'];
              $vFiltros['SFDTIPXX'] = $vItems['SFDTIP2X'];
              $mResultados = $oVuce->fnCalculoSaldosItemsVUCE($vFiltros);
              $mValidacionSaldos = $mResultados[1];
              $mValidacionSaldos[0]['PVVITEXX'] = ($mValidacionSaldos[0]['PVVITEXX'] != "") ? $mValidacionSaldos[0]['PVVITEXX'] : 0;
              if ($mValidacionSaldos[0]['PVVITEXX'] < $vItems['ITECANDV']) {
                $zSwitch = 1;
                $sError .= "el saldo del consecutivo [".$vItems['SFDID2XX']."] asignado al item [".$vItems['ITEIDXXX']."] es menor a la cantidad del item \n";
              }
            break;
          }
        }
      }
      if ($sError != "") {
        f_Mensaje(__FILE__, __LINE__, $sError."Verifique");
      }

      /**
       * Validando justificacion cierre de DO
       */
      if ($vSysStr['importaciones_exportaciones_justificar_cierre_DO'] == "SI" && $_POST['cObsObs'] == "") {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "La Justificacion Cierre DO no Puede Ser Vacia, Verifique.");
      }

      /**
       * Consultando si el Do tiene Items creados a partir de Saldos de Factura para desasociar el Do de la Cabecera de Saldos.
       */
      $nCreaTabla = 0;
      $qItems  = "SELECT SFDIDXXX,SFDTIPXX,FACIDXXX,CLIIDXXX,PIEIDXXX ";
      $qItems .= "FROM $cAlfa.SIAI0205 ";
      $qItems .= "WHERE ";
      $qItems .= "DOIIDXXX = \"{$_POST['cDoiId']}\" AND ";
      $qItems .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
      $qItems .= "ADMIDXXX = \"{$_POST['cAdmId']}\" AND ";
      $qItems .= "SFDIDXXX != \"\" AND ";
      $qItems .= "SFDTIPXX = \"FACTURA\" ";
      $qItems .= "ORDER BY ABS(ITEIDXXX) ASC ";
      $xItems  = f_MySql("SELECT","",$qItems,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qItems."~".mysql_num_rows($xItems));
      if(mysql_num_rows($xItems) > 0){

        if($nCreaTabla == 0){
          /**
            * Creando Tabla Temporal para Cargar Saldos de Factura
            */
          $objTablaMemory = new cEstructuras();
          $mReturnTablaM  = $objTablaMemory->fnCrearEstructuras(array('TIPOESTU'=>'PROCESOSSALDOS'));

          if($mReturnTablaM[0] == "true"){
            $nCreaTabla = 1;
            $vParametros['TABLAXXX'] = $mReturnTablaM[1];
          }else{
            $zSwitch = 1;
          }
        }

        if($zSwitch == 0){
          while($xRI = mysql_fetch_array($xItems)){
            $qInsert =	array(array('NAME'=>'SFDIDXXX','VALUE'=>$xRI['SFDIDXXX']	,'CHECK'=>'SI'),
                              array('NAME'=>'SFDTIPXX','VALUE'=>$xRI['SFDTIPXX']	,'CHECK'=>'SI'),
                              array('NAME'=>'FACIDXXX','VALUE'=>$xRI['FACIDXXX']	,'CHECK'=>'SI'),
                              array('NAME'=>'CLIIDXXX','VALUE'=>$xRI['CLIIDXXX']	,'CHECK'=>'SI'),
                              array('NAME'=>'PIEIDXXX','VALUE'=>$xRI['PIEIDXXX']	,'CHECK'=>'SI'));

            if (f_MySql("INSERT",$mReturnTablaM[1],$qInsert,$xConexion01,$cAlfa)) {
            }else{
              $zSwitch = 1;
              f_Mensaje(__FILE__,__LINE__,"Error al Guardar Saldos para Eliminar Do de Cabecera de Saldos.");
            }
          }
        }
      }

      if($nCreaTabla == 1){
        $mysql->f_Conectar();
        $mysql->f_SelecDb();
      }

      /**
       * Control de seriales repetidos en Items Activos del sistema.
       */
      if ($_POST['cRegEst'] == "ACTIVO") {
        
        /**
         * Bandera para indicar si mostar el texto: No se Permite Activar el DO:
         */
        $cMsjVal = "NO";

        /**
         * Control de seriales Repetidos en Items Activos
         * verifico si el item aplica descriptor tipo serial y tiene seriales,
         * de ser consumo el metodo fnValidarSerialesItemsActivos para validar que el serial no se encuentre en otro Item Activo del sistema.
         */
        if($vSysStr['importaciones_control_seriales_repetidos'] == "SI") {

          $qSerRep  = "SELECT ";
          $qSerRep .= "DOIIDXXX, ";
          $qSerRep .= "DOISFIDX, ";
          $qSerRep .= "ADMIDXXX, ";
          $qSerRep .= "ITEIDXXX, ";
          $qSerRep .= "PROIDXXX, ";
          $qSerRep .= "ITEDESXX, ";
          $qSerRep .= "CLIIDXXX  ";
          $qSerRep .= "FROM $cAlfa.SIAI0205 ";
          $qSerRep .= "WHERE ";
          $qSerRep .= "DOIIDXXX = \"{$_POST['cDoiId']}\" AND " ;
          $qSerRep .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
          $qSerRep .= "ADMIDXXX = \"{$_POST['cAdmId']}\" AND ";
          $qSerRep .= "ITEDESXX LIKE \"%SERIAL%\" ";
          $xSerRep  = f_MySql("SELECT","",$qSerRep,$xConexion01,"");
          // f_Mensaje(__FILE__, __LINE__, $qSerRep."~".mysql_num_rows($xSerRep));
          $mDatosSeriales = array();
          if(mysql_num_rows($xSerRep) > 0){
            while($xSR = mysql_fetch_array($xSerRep)) {
              $mDescriptores = f_Explode_Array($xSR['ITEDESXX'],"|","~");
              for($mD = 0; $mD < count($mDescriptores); $mD++){
                if($mDescriptores[$mD][2] == "SERIAL" && $mDescriptores[$mD][4] == "SI"){
                  $nInd_mDatosSeriales = count($mDatosSeriales);
                  $mDatosSeriales[$nInd_mDatosSeriales]['ADMIDXXX'] = $xSR['ADMIDXXX']; //Sucursal
                  $mDatosSeriales[$nInd_mDatosSeriales]['DOIIDXXX'] = $xSR['DOIIDXXX']; //Do
                  $mDatosSeriales[$nInd_mDatosSeriales]['DOISFIDX'] = $xSR['DOISFIDX']; //Sufijo
                  $mDatosSeriales[$nInd_mDatosSeriales]['CLIIDXXX'] = $xSR['CLIIDXXX']; //Cliente
                  $mDatosSeriales[$nInd_mDatosSeriales]['PROIDXXX'] = $xSR['PROIDXXX']; //Producto
                  $mDatosSeriales[$nInd_mDatosSeriales]['ITEIDXXX'] = $xSR['ITEIDXXX']; //Item
                  $mDatosSeriales[$nInd_mDatosSeriales]['DESIDXXX'] = $mDescriptores[$mD][0]; //Id Descriptor
                  $mDatosSeriales[$nInd_mDatosSeriales]['ORIGENXX'] = "DO"; //Id Descriptor
                }
              }
            }
          }

          if(count($mDatosSeriales) > 0){

            /*** Valido que los seriales del item no se encuentran incluido en otro items activo del sistema. ***/
            $objSerialesItemsActivos = new cInterfacesDeclaraciones();
            $mRetornaSerialesItemsActivos = $objSerialesItemsActivos->fnValidarSerialesItemsActivos($mDatosSeriales);
            // echo "<pre>";
            // print_r($mRetornaSerialesItemsActivos);
            
            if($mRetornaSerialesItemsActivos[0] == "false"){
              $zSwitch = 1;
              $cMsjVal = "SI";
              $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
              $cMsj .= "No se Permite Activar el DO:\n";
              for($nR=1; $nR<count($mRetornaSerialesItemsActivos); $nR++) {
                $vSerErr = explode("~", $mRetornaSerialesItemsActivos[$nR]);
                $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                $cMsj .= $vSerErr[1]."\n";
              }
            }

            $mysql->f_Conectar();
            $mysql->f_SelecDb();
          }
        }

        /**
         * Control de seriales Repetidos Adicionales en Items Activos
         * Consulto los seriales adicionales del DO y si tiene seriales relacionados se consume el 
         * metodo fnValidarSerialesAdicionalesItemsActivos para validar que el serial no se encuentre en otro Item Activo del sistema.
         */
        if($vSysStr['importaciones_control_seriales_adicionales_repetidos'] == "SI") {

          $qSerRepAdi  = "SELECT * ";
          $qSerRepAdi .= "FROM $cAlfa.SIAI0234 ";
          $qSerRepAdi .= "WHERE ";
          $qSerRepAdi .= "DOIIDXXX = \"{$_POST['cDoiId']}\" AND " ;
          $qSerRepAdi .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
          $qSerRepAdi .= "ADMIDXXX = \"{$_POST['cAdmId']}\" ";
          $qSerRepAdi .= "GROUP BY ITEIDXXX ";
          $xSerRepAdi  = f_MySql("SELECT","",$qSerRepAdi,$xConexion01,"");
          // f_Mensaje(__FILE__, __LINE__, $qSerRepAdi."~".mysql_num_rows($xSerRepAdi));
          if(mysql_num_rows($xSerRepAdi) > 0){
            $mDatSerAdi = array();
            while($xSRA = mysql_fetch_array($xSerRepAdi)){
              $nInd_DatSerAdi = count($mDatSerAdi);
              $mDatSerAdi[$nInd_DatSerAdi]['ADMIDXXX'] = $_POST['cAdmId'];     //Sucursal
              $mDatSerAdi[$nInd_DatSerAdi]['DOIIDXXX'] = $_POST['cDoiId'];     //Do
              $mDatSerAdi[$nInd_DatSerAdi]['DOISFIDX'] = $_POST['cDoiSfId'];   //Sufijo
              $mDatSerAdi[$nInd_DatSerAdi]['ITEIDXXX'] = $xSRA['ITEIDXXX'];    //Item
              $mDatSerAdi[$nInd_DatSerAdi]['CLIIDXXX'] = $xSRA['CLIIDXXX'];    //Cliente
              $mDatSerAdi[$nInd_DatSerAdi]['PROIDXXX'] = $xSRA['PROIDXXX'];    //Producto
              $mDatSerAdi[$nInd_DatSerAdi]['ORIGENXX'] = "DO";    //Producto
            }
            
            $objSerialesAdicionalesItemsActivos = new cInterfacesDeclaraciones();
            $mRetornaSerialesAdicionalesItemsActivos = $objSerialesAdicionalesItemsActivos->fnValidarSerialesAdicionalesItemsActivos($mDatSerAdi);
            // echo "<pre>fnValidarSerialesAdicionalesItemsActivos";
            // print_r($mRetornaSerialesAdicionalesItemsActivos);

            if($mRetornaSerialesAdicionalesItemsActivos[0] == "false"){
              $zSwitch  = 1;
              if($cMsjVal == "NO"){
                $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                $cMsj .= "No se Permite Activar el DO:\n";
              }
              for($nR=1; $nR<count($mRetornaSerialesAdicionalesItemsActivos); $nR++) {
                $vSerErr = explode("~", $mRetornaSerialesAdicionalesItemsActivos[$nR]);
                $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                $cMsj .= $vSerErr[1]."\n";
              }
            }

            $mysql->f_Conectar();
            $mysql->f_SelecDb();
          }
        }

        if($mRetornaSerialesItemsActivos[0] == "false" || $mRetornaSerialesAdicionalesItemsActivos[0] == "false"){
          f_Mensaje(__FILE__,__LINE__,$cMsj);
        }
      }

    break;
    case "BORRAR":
      /**
       * Validando que el DO no tenga movimiento contable.
       */
      $cSqlPro = "SELECT REGFECXX ";
      $cSqlPro .= "FROM SIAI0200 ";
      $cSqlPro .= "WHERE ";
      $cSqlPro .= "DOIIDXXX = \"{$_POST['cDoiId']}\" AND ";
      $cSqlPro .= "DOISFIDX = \"{$_POST['cDoiSfId']}\"  AND ";
      $cSqlPro .= "ADMIDXXX = \"{$_POST['cAdmId']}\" LIMIT 0,1";
      $zCrsPro  = mysql_query($cSqlPro);

      if (mysql_num_rows($zCrsPro) == 0) {
        $zSwitch = 1;
        f_Mensaje(__FILE__, __LINE__, "EL DO no Existe, Verifique");
      } else {
        $zRPro = mysql_fetch_array($zCrsPro);
        $rRet = f_Bloquear_Importador($_POST['cDoiId'], $_POST['cDoiSfId'], substr($zRPro['REGFECXX'], 0, 4),"",$_POST['cAdmId']);
        $mysql->f_Conectar();
        $mysql->f_SelecDb();
        if ($rRet != 1) {
          $zSwitch = 1;
          f_Mensaje(__FILE__, __LINE__, "EL DO [{$_POST['cAdmId']}-{$_POST['cDoiId']}-{$_POST['cDoiSfId']}] no Puede Ser Borrado Porque Tiene Movimiento Contable, Verifique");
        }
      }

      /**
       * Validando si el Tramite a Eliminar tiene:
       * Consecutivos DAV Asignados.
       * Autorizacion de Eliminar Tramite con consecutivos DAV Asignados, de acuerdo al valor de la Variable "importaciones_permitir_eliminar_consecutivo_DAV_asignado".
       * o Autorizacion de Eliminar Do por Opcion Autorizaciones Do, osea si el campo DOIDAVEL = "ELIMINAR".
       *
       */
      $vFiltros = array();
      $vFiltros['DOIIDXXX'] = $_POST['cDoiId'];
      $vFiltros['DOISFIDX'] = $_POST['cDoiSfId'];
      $vFiltros['ADMIDXXX'] = $_POST['cAdmId'];
      $vFiltros['PROCESOX'] = "BORRARDO";
      $oVerificarBorrarReliquidarTramiteConsecutivosDAV = new cConsecutivosDAV();
      $mReturn = $oVerificarBorrarReliquidarTramiteConsecutivosDAV->fnVerificarBorrarReliquidarTramiteConsecutivosDAV($vFiltros);

      /**
       * Si el Retorno es True, se debe validar que en la posicion 1 vienen los consecutivos DAV
       * Si el Retorno es false, no se permite Eliminar el Tramite y se muestra Mensaje a Usuario.
       */
      if ($mReturn[0] == "true") {
        $mCscDav = array();
        $mCscDav = $mReturn[1];
      } else {
        $zSwitch = 1;
        for ($y = 1; $y < count($mReturn); $y++) {
          $cMsj = $mReturn[$y]."\n";
        }

        if ($cMsj != "") {
          f_Mensaje(__FILE__, __LINE__, $cMsj);
        }
      }

      /**
       * Validando si el Tramite a Eliminar tiene:
       * Consecutivo Acta Reconocimiento Asignado.
       * Autorizacion de Eliminar Tramite con consecutivo Acta Reconocimiento Asignado, de acuerdo al valor de la Variable "importaciones_permitir_eliminar_consecutivo_reconocimiento".
       * o Autorizacion de Eliminar Do por Opcion Autorizaciones Do, si el campo DOICSCAR = "ELIMINAR".
       */
      $cMsj03 = "";
      $pvTramite = array();
      $pvTramite['DOIIDXXX'] = $_POST['cDoiId'];
      $pvTramite['DOISFIDX'] = $_POST['cDoiSfId'];
      $pvTramite['ADMIDXXX'] = $_POST['cAdmId'];
      $pvTramite['PROCESOX'] = "BORRARDO";
      $oVerificarBorrarActaReconocimiento = new cConsecutivoActaReconocimiento();
      $mReturnActaRec = $oVerificarBorrarActaReconocimiento->fnVerificarCambioConsecutivoActaReconocimiento($pvTramite);

      /**
       * Si el Retorno es True, se debe validar que en la posicion 1 vienen los consecutivos DAV
       * Si el Retorno es false, no se permite Eliminar el Tramite y se muestra Mensaje a Usuario.
      */
      if ($mReturnActaRec[0] == "true") {
        $mCscRec = array();
        $mCscRec[1] = $mReturnActaRec[1];
        $mCscRec[2] = $mReturnActaRec[2];
        $mCscRec[3] = $mReturnActaRec[3];
      } else {
        $zSwitch = 1;
        for ($y = 1; $y < count($mReturnActaRec); $y++) {
          $cMsj03 = $mReturnActaRec[$y]."\n";
        }

        if ($cMsj03 != "") {
          f_Mensaje(__FILE__, __LINE__, $cMsj03);
        }
      }

      /**
       * Consultando si el Do tiene Items creados a partir de Saldos de Factura para desasociar el Do de la Cabecera de Saldos.
       */
      $nCreaTabla = 0;
      $mSfdId = array();
      $qItems  = "SELECT SFDIDXXX,SFDTIPXX,FACIDXXX,CLIIDXXX,PIEIDXXX ";
      $qItems .= "FROM $cAlfa.SIAI0205 ";
      $qItems .= "WHERE ";
      $qItems .= "DOIIDXXX = \"{$_POST['cDoiId']}\" AND ";
      $qItems .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
      $qItems .= "ADMIDXXX = \"{$_POST['cAdmId']}\" AND ";
      $qItems .= "SFDIDXXX != \"\" AND ";
      $qItems .= "SFDTIPXX = \"FACTURA\" ";
      $qItems .= "ORDER BY ABS(ITEIDXXX) ASC ";
      $xItems  = f_MySql("SELECT","",$qItems,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qItems."~".mysql_num_rows($xItems));
      if(mysql_num_rows($xItems) > 0){

        if($nCreaTabla == 0){
          /**
            * Creando Tabla Temporal para Cargar Saldos de Factura
            */
          $objTablaMemory = new cEstructuras();
          $mReturnTablaM  = $objTablaMemory->fnCrearEstructuras(array('TIPOESTU'=>'PROCESOSSALDOS'));

          if($mReturnTablaM[0] == "true"){
            $nCreaTabla = 1;
            $vParametros['TABLAXXX'] = $mReturnTablaM[1];
          }else{
            $zSwitch = 1;
          }
        }

        if($zSwitch == 0){
          while($xRI = mysql_fetch_array($xItems)){

            // Matriz para marcar Seriales BPO
            $nInd_mSfdId = count($mSfdId);
            $mSfdId[$nInd_mSfdId]['SFDIDXXX'] = $xRI['SFDIDXXX'];
            $mSfdId[$nInd_mSfdId]['SFDTIPXX'] = $xRI['SFDTIPXX'];

            $qInsert =	array(array('NAME'=>'SFDIDXXX','VALUE'=>$xRI['SFDIDXXX']	,'CHECK'=>'SI'),
                              array('NAME'=>'SFDTIPXX','VALUE'=>$xRI['SFDTIPXX']	,'CHECK'=>'SI'),
                              array('NAME'=>'FACIDXXX','VALUE'=>$xRI['FACIDXXX']	,'CHECK'=>'SI'),
                              array('NAME'=>'CLIIDXXX','VALUE'=>$xRI['CLIIDXXX']	,'CHECK'=>'SI'),
                              array('NAME'=>'PIEIDXXX','VALUE'=>$xRI['PIEIDXXX']	,'CHECK'=>'SI'));

            if (f_MySql("INSERT",$mReturnTablaM[1],$qInsert,$xConexion01,$cAlfa)) {
            }else{
              $zSwitch = 1;
              f_Mensaje(__FILE__,__LINE__,"Error al Guardar Saldos para Eliminar Do de Cabecera de Saldos.");
            }
          }
        }
      }

      if($nCreaTabla == 1){
        $mysql->f_Conectar();
        $mysql->f_SelecDb();
      }
    break;
    default:
      $zSwitch = 1;
      f_Mensaje(__FILE__, __LINE__, "El Modo de Grabado $kModo No Es Correcto, Verifique");
    break;
  }

  // Validaciones Control Casillas Obligatorias
  switch ($cAlfa) {
    case 'DEROLDANLO':
    case 'TEROLDANLO':
    case 'ROLDANLO':
      if($kModo == "EDITAR"){
        $cCampos   = array();
        $vDatos    = array();

        // Control Casilla Fecha Real de Arribo - Fecha Manifiesto
        if($_POST['dDgeFmc'] != "0000-00-00" && $_POST['dDgeFmc'] != ""){
          $cCampos[] = "REAMAN"; 
        }

        // Control Casilla Aplica Trayecto - Tipo Declaracion Anticipada
        if($_POST['cTdeId'] == "3" && $_POST['cDoiAtrA'] == ""){
          $vDatos['TDEIDXXX'] = $_POST['cTdeId'];
          $vDatos['DAAIDXXX'] = $_POST['cDaaId'];
          $cCampos[] = "APTTDE";
        }

        // Control Casilla Número de Manifiesto - Fecha de Manifiesto
        if(($_POST['dDgeFmc'] != "0000-00-00" && $_POST['dDgeFmc'] != "") && $_POST['cDgeMc'] == ""){
          $cCampos[] = "NMAFMA"; 
        }

        if (!empty($cCampos)) {

          $vDatos['ORIGENXX'] = "IMPORTACION";
          $vDatos['ADMIDXXX'] = $_POST['cAdmId'];
          $vDatos['DOIIDXXX'] = $_POST['cDoiId'];
          $vDatos['DOISFIDX'] = $_POST['cDoiSfId'];

          foreach ($cCampos as $cCampo) {
            $vDatos['CAMPOXXX'] = $cCampo;
            $mReturnControlDiligenciamientoCasillasObligatorias = $oProcesosRoldan->fnControlDiligenciamientoCasillasObligatorias($vDatos);
            if($mReturnControlDiligenciamientoCasillasObligatorias[0] == "false"){
              $cMsjError = "";
              $zSwitch   = 1;
              for($nE=1;$nE<count($mReturnControlDiligenciamientoCasillasObligatorias);$nE++){
                $cMsjError .= $mReturnControlDiligenciamientoCasillasObligatorias[$nE] ."\n";
              }
              f_Mensaje(__FILE__, __LINE__, $cMsjError);
            }
          }
        }
      }
    break;
  }

  if ($zSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "ANULAR":
      case "BORRAR":
        /**
         * Consultando si el Item fue creado desde el modulo de TOE, para modificar inventario
         */
        if($vSysStr['system_activar_modulo_toe'] == "SI"){
          $cMsjTOE = "";
          /**
            * Creando Tabla Temporal para guardar los errores
            */
          $vParametrosTOE['TIPOESTU'] = "ERRORES";
          $mReturnTablaET  = $objEstructurasTOE->fnCrearEstructurasTOE($vParametrosTOE);

          if($mReturnTablaET[0] == "false"){
            $zSwitch = 1;
            $cMsjTOE .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
            $cMsjTOE .= "Error al Crear Tabla Temporal Para Errores Inventario TOE.";
          } else {

            //Buscando los items TOE
            $vItems = array();
            $qItemsTOE  = "SELECT ITEIDXXX ";
            $qItemsTOE .= "FROM $cAlfa.SIAI0205 ";
            $qItemsTOE .= "WHERE ";
            $qItemsTOE .= "DOIIDXXX = \"{$_POST['cDoiId']}\"   AND ";
            $qItemsTOE .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
            $qItemsTOE .= "ADMIDXXX = \"{$_POST['cAdmId']}\"   AND ";
            $qItemsTOE .= "ITETOEIN != \"\"";
            $xItemsTOE  = f_MySql("SELECT","",$qItemsTOE,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qItemsTOE."~".mysql_num_rows($xItemsTOE));
            if (mysql_num_rows($xItemsTOE) > 0) {
              $vParametrosTOE['TABLAERR'] = $mReturnTablaET[1]; //TABLA DE ERRORES
              $vParametrosTOE['ADMIDXXX'] = $_POST['cAdmId'];   //SUCURSAL
              $vParametrosTOE['DOIIDXXX'] = $_POST['cDoiId'];   //DO
              $vParametrosTOE['DOISFIDX'] = $_POST['cDoiSfId']; //SUFIJO
              $vParametrosTOE['REGESTXX'] = ($_COOKIE['kModo'] == "BORRAR") ? "BORRAR" : $_POST['cRegEst']; //ESTADO AL QUE VA CAMBIAR EL ITEM
              $vItems = array();

              $mReturnTablaTOE  = $objInventarioTOE->fnReversarInventarioTOE($vParametrosTOE,$vItems);

              if($mReturnTablaTOE[0] == "false"){
                $zSwitch = 1;
                $qErrores  = "SELECT LINEAERR, DESERROR ";
                $qErrores .= "FROM $cAlfa.$mReturnTablaET[1] ";
                $xErrores  = f_MySql("SELECT","",$qErrores,$xConexion01,"");
                // f_Mensaje(__FILE__,__LINE__,$qErrores."~".mysql_num_rows($xErrores));
                if(mysql_num_rows($xErrores) > 0){
                  $cAccion  = ($_COOKIE['kModo'] == "BORRAR") ? "Eliminar" : (($_POST['cRegEst'] == "ACTIVO") ? "Activar" : "Inactivar");
                  $zSwitch  = 1;
                  $cMsjTOE .= "No se Permiten $cAccion los Items porque:\n\n";
                  while($xRE = mysql_fetch_array($xErrores)){
                    $cMsjTOE .= "Linea ".str_pad($xRE['LINEAERR'], 4, "0", STR_PAD_LEFT).": ";
                    $cMsjTOE .= $xRE['DESERROR']."\n";
                  }
                }
              }
            }
          }

          if ($cMsjTOE != "") {
            f_Mensaje(__FILE__, __LINE__, $cMsjTOE);
          }

          $mysql->f_Conectar();
          $mysql->f_SelecDb();
        } ## if($vSysStr['system_activar_modulo_toe'] == "SI"){ ##

        if($vSysStr['system_activar_modulo_vehiculos'] == "SI"){

          $cMsjVeh = "";

          /**
            * Creando Tabla Temporal para guardar los errores
            */
          $vParametrosVehiculos['TIPOESTU'] = "ERRORES";
          $mReturnTablaEV  = $objEstructurasVehiculos->fnCrearEstructurasVehiculos($vParametrosVehiculos);

          $vParametrosVehiculos['TIPOESTU'] = "SALDOSMARCADOS";
          $mReturnTablaIV  = $objEstructurasVehiculos->fnCrearEstructurasVehiculos($vParametrosVehiculos);

          if($mReturnTablaEV[0] == "false" || $mReturnTablaIV[0] == "false"){
            $zSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
            $cMsj .= "Error al Crear Tabla Temporal Para Vehiculos.";
          } else {
            //Buscando los items Vehiculos
            $nErrorVeh = 0; $nCanIteVeh = 0;
            $qItemsVeh  = "SELECT DOIIDXXX,DOISFIDX,ADMIDXXX,ITEIDXXX,SFDTIPXX,SFDIDXXX ";
            $qItemsVeh .= "FROM $cAlfa.SIAI0205 ";
            $qItemsVeh .= "WHERE ";
            $qItemsVeh .= "DOIIDXXX  = \"{$_POST['cDoiId']}\"   AND ";
            $qItemsVeh .= "DOISFIDX  = \"{$_POST['cDoiSfId']}\" AND ";
            $qItemsVeh .= "ADMIDXXX  = \"{$_POST['cAdmId']}\"   AND ";
            $qItemsVeh .= "SFDTIPXX  = \"VEHICULO\"             AND ";
            $qItemsVeh .= "SFDIDXXX != \"\"";
            $xItemsVeh  = f_MySql("SELECT","",$qItemsVeh,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qItemsVeh."~".mysql_num_rows($xItemsVeh));
            while ($xRIV = mysql_fetch_array($xItemsVeh)) {
              $qInsert =	array(array('NAME'=>'vsaidxxx','VALUE'=>$xRIV['SFDIDXXX'],'CHECK'=>'SI'),
                                array('NAME'=>'doiidxxx','VALUE'=>$xRIV['DOIIDXXX'],'CHECK'=>'SI'),
                                array('NAME'=>'doisfidx','VALUE'=>$xRIV['DOISFIDX'],'CHECK'=>'SI'),
                                array('NAME'=>'admidxxx','VALUE'=>$xRIV['ADMIDXXX'],'CHECK'=>'SI'),
                                array('NAME'=>'iteidxxx','VALUE'=>$xRIV['ITEIDXXX'],'CHECK'=>'SI'));

              if (f_MySql("INSERT",$mReturnTablaIV[1],$qInsert,$xConexion01,$cAlfa)) {
                $nCanIteVeh++;
              } else {
                $nErrorVeh = 1;
              }
            }

            if ($nErrorVeh == 1) {
              $zSwitch = 1;
              $cMsjVeh .= "Linea ".str_pad($xRE['LINEAERR'], 4, "0", STR_PAD_LEFT).": ";
              $cMsjVeh .= "Error al Guardar en la Tabla Temporal de Saldos Vehiculos.\n";
            }

            // f_Mensaje(__FILE__,__LINE__,count($vItems));
            if ($zSwitch == 0 && $nCanIteVeh > 0) {

              $vParametrosVeh['TABLAXXX'] = $mReturnTablaIV[1]; //TABLA DE ITEMS
              $vParametrosVeh['TABLAERR'] = $mReturnTablaEV[1]; //TABLA DE ERRORES
              $vParametrosVeh['ACTIONXX'] = ($_COOKIE['kModo'] == "BORRAR") ? "BORRAR" : $_POST['cRegEst']; //ESTADO AL QUE VA CAMBIAR EL ITEM

              $mReturnTablaVeh  = $objSaldoVehiculos->fnMarcarDesmarcarSaldo($vParametrosVeh);

              if($mReturnTablaVeh[0] == "false"){
                $zSwitch = 1;
                $qErrores  = "SELECT LINEAERR, DESERROR ";
                $qErrores .= "FROM $cAlfa.$mReturnTablaEV[1] ";
                $xErrores  = f_MySql("SELECT","",$qErrores,$xConexion01,"");
                // f_Mensaje(__FILE__,__LINE__,$qErrores."~".mysql_num_rows($xErrores));
                if(mysql_num_rows($xErrores) > 0){
                  $cAccion  = ($_COOKIE['kModo'] == "BORRAR") ? "Eliminar" : (($_POST['cRegEst'] == "ACTIVO") ? "Activar" : "Inactivar");
                  $cMsjVeh .= "No se Permiten $cAccion los Items:\n\n";
                  while($xRE = mysql_fetch_array($xErrores)){
                    $cMsjVeh .= "Linea ".str_pad($xRE['LINEAERR'], 4, "0", STR_PAD_LEFT).": ";
                    $cMsjVeh .= $xRE['DESERROR']."\n";
                  }
                }
              }
              if ($cMsjVeh != "") {
                f_Mensaje(__FILE__, __LINE__, $cMsjVeh);
              }
            }
          }

          $mysql->f_Conectar();
          $mysql->f_SelecDb();
        }
      break;
      default:
        //No hace nada
      break;
    }
  }

  /**
   * Fin de Primero valido los datos que llegan por metodo POST.
   */
  /**
   * Actualizacion en la Tabla.
   */
  if ($zSwitch == 0) {
    $vfchpry = 0;
    $vfchpro = 0;

    if ($vchapry == "ON") {
      $vfchpry = 1;
    }

    if ($vchapro == "ON") {
      $vfchpro = 1;
    }

    $vCliImp3 = explode(",",$vSysStr['roldanlo_nit_proyecto_3m']);
    if(!in_array($_POST['cCliId'],$vCliImp3) || $_POST['cMtrId'] != "5"){
      $_POST['nDoiHanD'] = 0;
      $_POST['nDoiIvaH'] = 0;
      $_POST['nDoiComI'] = 0;
    }

    switch ($kModo) {
      case "NUEVO":
        $csc = "DOI";
        $cs = '';
        $cero = 0;
        $tempo = 1;
        $fl = 1;
        if ($_POST["cDoiSfId"] == '001') {
          $cs = $zCsc;
					$tempo = 0;
          //Do Raro Mar y Aire
          if ($kMysqlDb == "INTERLOG") {
            if ($_POST['cAdmId'] == "CLO" || $_POST['cAdmId'] == "SMR") {
              $zSqd = mysql_query("SELECT docidxxx,sucidxxx FROM sys00121 WHERE (sucidxxx = \"CLO\" OR sucidxxx = \"SMR\") AND docidxxx = \"$cs\" LIMIT 0,1");
              $zRcr = mysql_num_rows($zSqd);
              if ($zRcr == 1) {
                $yador = 1;
                while ($yador == 1) {
                  $cs++;
                  $zCsc2++;
                  $zSqd = mysql_query("SELECT docidxxx,sucidxxx FROM sys00121 WHERE (sucidxxx = \"CLO\" OR sucidxxx = \"SMR\") AND docidxxx = \"$cs\" LIMIT 0,1");
                  $zRcr = mysql_num_rows($zSqd);
                  if ($zRcr <= 0) {
                    $yador = 0;
                  }
                }
              }
            }
          }
          //fin Do Raro Mar y Aire
        } else {
          if ($_POST["cDoiSfId"] == 'TTT') {
            $cs = $_POST["cDoiId"];
            $_POST["cDoiSfId"] = "001";

            //Cambio ticket 9433 Cristian Cardona
            $qDocId  = "SELECT ";
            $qDocId .= "$cAlfa.SIAI0200.DOIIDXXX ";
            $qDocId .= "FROM $cAlfa.SIAI0200 ";
            $qDocId .= "WHERE ";
            $qDocId .= "$cAlfa.SIAI0200.DOIIDXXX = \"$cs\" AND ";
            $qDocId .= "$cAlfa.SIAI0200.DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
            $qDocId .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$_POST['cAdmId']}\" ";
            $qDocId .= "LIMIT 0,1";
            $xDocId  = f_MySql("SELECT","",$qDocId,$xConexion01,"");

            if (mysql_num_rows($xDocId)==1){
              $fl = 0;
              f_Mensaje(__FILE__,__LINE__,"DO/Imp ya existe, Verifique");
            } //Fin

            switch($kMysqlDb) {
              case "DESIACOSIP":
              case "TESIACOSIP":
              case "SIACOSIA":
                //Como el numero de DO es unico en todo el sistema se valida que no exista en
                //el modulo de exportaciones y en el modulo de facturacion como tipo otros

                //Validando que el DO/Exp no existe
                $qSqlDtg  = "SELECT dexidxxx ";
                $qSqlDtg .= "FROM siae0199 ";
                $qSqlDtg .= "WHERE ";
                $qSqlDtg .= "dexidxxx = \"$cs\" LIMIT 0,1";
                $xCrsDtg  = f_MySql("SELECT","",$qSqlDtg,$xConexion01,"");
                //f_Mensaje(__FILE__,__LINE__,$qSqlDtg."~".mysql_num_rows($xCrsDtg));
                if (mysql_num_rows($xCrsDtg) == 1){
                  $fl = 0;
                  f_Mensaje(__FILE__,__LINE__,"El DO ya existe en el Modulo de Exportaciones, Verifique");
                } //Fin
              break;
              default: //No hace nada
              break;
            }
          } else {
            ////////////////////////////////////////////////////////////////////////
            if ($_POST["cDoiSfId"] == 'SUB') {
              $cs = $_POST["cDoiId"];
              $sqlsub = "SELECT DOIIDXXX,";
              $sqlsub .= "CLIIDXXX,";
              $sqlsub .= "DOIPEDXX,";
              $sqlsub .= "DAUIDXXX,";
              $sqlsub .= "ADMIDXXX,";
              $sqlsub .= "USRIDXXX,";
              $sqlsub .= "USRID2XX,";
              $sqlsub .= "USRID3XX,";
              $sqlsub .= "CLIUAPXX ";
              $sqlsub .= "FROM SIAI0200 ";
              $sqlsub .= "WHERE ";
              $sqlsub .= "DOIIDXXX = \"$cs\" AND ";
              $sqlsub .= "ADMIDXXX = \"{$_POST["cAdmId"]}\" ";
              //f_Mensaje(__FILE__,__LINE__,"Hijo ".$sqlsub);
              $ressub = $mysql->f_Ejecutar($sqlsub);
              $filsub = $mysql->f_ContarFilas($ressub);
              $subf = '';
              if ($filsub > 0) {
                $row = $mysql->f_FilaX($ressub, 0);
                $_POST["cCliId"] = $row['CLIIDXXX'];
                $_POST["cAdmId"] = $row['ADMIDXXX'];
                $_POST["cCliUap"] = $row['CLIUAPXX'];
                $sufi = $filsub + 1;
                if ($sufi < 10) {
                  $subf = '00'.trim($sufi);
                }

                if ($sufi > 9 && $sufi < 100) {
                  $subf = '0'.trim($sufi);
                }

                if ($sufi > 100) {
                  $subf = trim($sufi);
                }

                $sqlpr = "SELECT * FROM SIAI0200 WHERE DOIIDXXX = \"$cs\" AND ADMIDXXX = \"{$_POST['cAdmId']}\" LIMIT 0,1";
                $respr = $mysql->f_Ejecutar($sqlpr);
                $filpr = $mysql->f_ContarFilas($respr);
                if ($filpr > 0) {
                  $cero = 1;
                  $_POST["cDoiSfId"] = $subf;
                } else {
                  $fl = 0;
                }
              } else {
                $fl = 0;
                ?>
                <script language = 'javascript'>
                  alert('El Do Origen no Existe, No se pudo Crear Sub DO');
                </script>
                <?php
              }
            } else {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "Debe Seleccionar Si es un Nuevo D.O./Imp o un D.O./Imp Manual o un D.O /Imp. Hijo");
            }

            $ver200 = "SELECT DOIIDXXX ";
            $ver200 .= "FROM SIAI0200 ";
            $ver200 .= "WHERE ";
            $ver200 .= "DOIIDXXX = \"$cs\" AND ";
            $ver200 .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
            $ver200 .= "ADMIDXXX = \"{$_POST['cAdmId']}\" ";
            $re200 = $mysql->f_Ejecutar($ver200);
            $f200 = $mysql->f_ContarFilas($re200);
            if ($f200 > 0) {
              $fl = 0;
              ?>
              <script language = 'javascript'>
                alert('DO/Imp Ya existe');
              </script>
              <?php
            }

            $caletra = 0;
            $aletra = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', '_');
            for ($i = 0; $i < strlen($cs); $i++) {
              if (in_array(substr($cs, $i, 1), $aletra)) {
                $caletra++;
              } else {
                $fl = 0;
                ?>
                <script language = 'javascript'>
                  alert('Nro DO Contiene caracter no permitido , verifique que no tenga espacios ni guiones y que los caracteres esten en los rangos [A-Z][0-9][._/]');
                </script>
                <?php
                break;
              }
              ///////////////////////////////////////////////////////////////////////
            }
          }
        }

        if ($fl == 1) {
          $_POST['cObs1'] = str_replace(array(chr(27), chr(9), chr(13), chr(10), chr(96), chr(92), chr(180)), ' ', $_POST['cObs1']);
          for ($xn = 0; $xn < 10; $xn++) {
            $_POST['cObs1'] = str_replace(array("      ", "     ", "    ", "   ", "  "), " ", $_POST['cObs1']);
          }

          $_POST['cObs2'] = str_replace(array(chr(27), chr(9), chr(13), chr(10), chr(96), chr(92), chr(180)), ' ', $_POST['cObs2']);
          for ($xn = 0; $xn < 10; $xn++) {
            $_POST['cObs2'] = str_replace(array("      ", "     ", "    ", "   ", "  "), " ", $_POST['cObs2']);
          }

          $isnvaob = 0;
          $isnvaob2 = 0;
          if (strlen($_POST['cObs1']) > 0) {
            $isnvaob = 1;
            $cadwh = f_Fecha().'_'.f_Hora();
            $usrs = fldesc("SIAI0003", "USRIDXXX", "USRNOMXX", $kUser);
            $cadwh .= " $usrs - ";
            //$_POST['cDoiObs'] = '|___'.$cadwh.strtoupper($_POST['cObs1']).'___|';
            $_POST['cObs1'] = '|___'.$cadwh.strtoupper($_POST['cObs1']).'___|';
          }

          $_POST['cDoiObs2'] = str_replace(array(chr(27), chr(9), chr(13), chr(10), chr(96), chr(92), chr(180)), ' ', $_POST['cDoiObs2']);
          $cObsf2 = $_POST['cDoiObs2'];
          for ($xn = 0; $xn < 10; $xn++) {
            $cObsf2 = str_replace(array("      ", "     ", "    ", "   ", "  "), " ", $cObsf2);
          }

          if (strlen($_POST['cDoiObs2']) > 0) {
            $isnvaob2 = 1;
            $cadwh = f_Fecha().'_'.f_Hora();
            $usrs = fldesc("SIAI0003", "USRIDXXX", "USRNOMXX", $kUser);
            $cadwh .= " $usrs - ";
            $cObsf2 = '|___'.$cadwh.strtoupper($_POST['cObs2']).'___|'.$cObsf2;
          }

          $isDta = 0;
          $nDta = 0;
          if (trim(strtoupper($_POST["cDoiDta"])) == "ON") {
            $nDta = 1;
            $cTipoDo = "TRANSITO";
            $sqlDta = "SELECT doiidxxx,admidxxx from dta00200 WHERE doiidxxx = \"$cs\" AND admidxxx = \"{$_POST["cAdmId"]}\" LIMIT 0,1";
            $zCrsDta = $mysql->f_Ejecutar($sqlDta);
            $nFilDta = $mysql->f_ContarFilas($zCrsDta);
            if ($nFilDta == 0) {
              $isDta = 1;
            }
          }

          $isEqu = 0;
          $nEqu = 0;
          if (trim(strtoupper($_POST["cDoiEqu"])) == "ON") {
            $nEqu = 1;
            $cTipoDo = "OTROS";
            $sqlEqu = "SELECT DOIIDXX,ADMIDXXX,DOISFIDX FROM SIAI0142 WHERE DOIIDXXX = \"$cs\" AND ADMIDXXX = \"{$_POST["cAdmId"]}\" AND DOISFIDX = \"{$_POST["cDoiSfId"]}\" LIMIT 0,1";
            $zCrsEqu = $mysql->f_Ejecutar($sqlEqu);
            $nFilEqu = $mysql->f_ContarFilas($zCrsEqu);
            if ($nFilEqu == 0) {
              $isEqu = 1;
            }
          }

          if ($nEqu == 1 && $nDta == 1) {
            $zSwitch = 1;
            f_Mensaje(__FILE__, __LINE__, "El tramite puede ser DTA o Equipaje, no ambos, Verifique");
          }
          //$muevecsc = 0;
          //if($kMysqlDb == "INTERLOG"){
          if ($vSysStr['system_creacion_do_consecutivo_excepcion'] == "SI") {
            $csdointerlog = f_Numeros($cs);
            if (strlen($cs) != strlen($csdointerlog)) {
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__, "El sistema no permite crear DO con caracteres alfabeticos, Verifique");
            } else {
              if (substr($cs, strlen($cs) - 3, 3) == $vSysStr['creacion_do_no_termina']) {
                f_Mensaje(__FILE__, __LINE__, "El Consecutivo $cs termina en ceros ({$vSysStr['creacion_do_no_termina']}), se procede a incrementar en uno (1), de acuerdo a la politica de la Organizacion.");
                $cs = $cs + 1;
                $zCsc2++;
                //$muevecsc =1;
              }
            }
          }

          //Vendedor Sucursal
          $cVenId = $_POST['cVenId'];
          $cSucCom = $_POST['cSucCom'];

          /**
           * Cargo el valor de la Hora de Manifiesto con la hora actual del sistema solo si no se ha digitado y si ya se digito
           * Numero de Manifiesto y Fecha de Manifiesto.
           */
          if (($_POST['cDgeMc'] != "" || ($_POST['dDgeFmc'] != "" && $_POST['dDgeFmc'] != "0000-00-00")) && ($_POST['hDgeHmc'] == "" || $_POST['hDgeHmc'] == "00:00:00")) {
            $_POST['hDgeHmc'] = date('H:i:s');
          }

          // si la variable del sistema esta activa valido en facturación
          if ($vSysStr['system_aplica_financiero'] == "SI") {

            switch($kMysqlDb) {
              case "DESIACOSIP":
              case "TESIACOSIP":
              case "SIACOSIA":

                //Validando que no exista como tipo otros
                $qDocId  = "SELECT ";
                $qDocId .= "doctipxx ";
                $qDocId .= "FROM $cAlfa.sys00121 ";
                $qDocId .= "WHERE ";
                $qDocId .= "$cAlfa.sys00121.docidxxx = \"{$cs}\" AND ";
                $qDocId .= "$cAlfa.sys00121.doctipxx NOT IN (\"IMPORTACION\",\"TRANSITO\") ";
                $qDocId .= "LIMIT 0,1";
                //f_Mensaje(__FILE__,__LINE__,$qDocId."~".mysql_num_rows($xDocId));
                $xDocId  = f_MySql("SELECT","",$qDocId,$xConexion01,"");

                if (mysql_num_rows($xDocId)==1){
                  $vDocId = mysql_fetch_array($xDocId);
                  $zSwitch = 1;
                  f_Mensaje(__FILE__,__LINE__,"DO/Imp ya existe en el Modulo Facturacion como DO de tipo [{$vDocId['doctipxx']}], Verifique");
                } //Fin

                $qDocId  = "SELECT ";
                $qDocId .= "sucidxxx,docidxxx,docsufxx ";
                $qDocId .= "FROM $cAlfa.sys00121 ";
                $qDocId .= "WHERE ";
                $qDocId .= "$cAlfa.sys00121.docidxxx = \"{$cs}\" ";
                $qDocId .= "LIMIT 0,1";
                //f_Mensaje(__FILE__,__LINE__,$qDocId."~".mysql_num_rows($xDocId));
                $xDocId  = f_MySql("SELECT","",$qDocId,$xConexion01,"");

                if (mysql_num_rows($xDocId)==0){
                  $zSwitch = 1;
                  f_Mensaje(__FILE__,__LINE__,"DO/Imp NO existe en el Modulo Facturacion, Verifique");
                } else {
                  //Se verifica que la sucursal y el sufijo sean el mismo en el modulo de facturacion para que no se cree el DO varias veces
                  $vDocId = mysql_fetch_array($xDocId);

                  if ($vDocId['sucidxxx'] != $_POST['cAdmId']) {
                    $zSwitch = 1;
                    f_Mensaje(__FILE__,__LINE__,"El Do Existe en el Modulo Contable con la Sucursal {$vDocId['sucidxxx']}, por favor Comuniquese con Help Desk.");
                  }

                  if ($vDocId['docsufxx'] != $_POST['cDoiSfId']) {
                    $zSwitch = 1;
                    f_Mensaje(__FILE__,__LINE__,"El Do Existe en el Modulo Contable con el Sufijo {$vDocId['docsufxx']}, por favor Comuniquese con Help Desk.");
                  }
                }//Fin
              break;
              default:

                //Validando que no exista como tipo otros
                $qDocId  = "SELECT ";
                $qDocId .= "doctipxx ";
                $qDocId .= "FROM $cAlfa.sys00121 ";
                $qDocId .= "WHERE ";
                $qDocId .= "$cAlfa.sys00121.sucidxxx = \"{$_POST['cAdmId']}\" AND ";
                $qDocId .= "$cAlfa.sys00121.docidxxx = \"{$cs}\" AND ";
                $qDocId .= "$cAlfa.sys00121.docsufxx = \"{$_POST['cDoiSfId']}\" AND ";
                $qDocId .= "$cAlfa.sys00121.doctipxx NOT IN (\"IMPORTACION\",\"TRANSITO\") LIMIT 0,1";
                //f_Mensaje(__FILE__,__LINE__,$qDocId."~".mysql_num_rows($xDocId));
                $xDocId  = f_MySql("SELECT","",$qDocId,$xConexion01,"");

                if (mysql_num_rows($xDocId)==1){
                  $vDocId = mysql_fetch_array($xDocId);
                  $zSwitch = 1;
                  f_Mensaje(__FILE__,__LINE__,"DO/Imp ya existe en el Modulo Facturacion como DO de tipo [{$vDocId['doctipxx']}], Verifique");
                } //Fin

                $qDocId  = "SELECT ";
                $qDocId .= "docidxxx ";
                $qDocId .= "FROM $cAlfa.sys00121 ";
                $qDocId .= "WHERE ";
                $qDocId .= "$cAlfa.sys00121.sucidxxx = \"{$_POST['cAdmId']}\" AND ";
                $qDocId .= "$cAlfa.sys00121.docidxxx = \"{$cs}\" AND ";
                $qDocId .= "$cAlfa.sys00121.docsufxx = \"{$_POST['cDoiSfId']}\" ";
                $qDocId .= "LIMIT 0,1";
                // f_Mensaje(__FILE__,__LINE__,"frdoigra - ".$qDocId);
                $xDocId  = f_MySql("SELECT","",$qDocId,$xConexion01,"");

                if (mysql_num_rows($xDocId)==1){
                  $zSwitch = 1;
                  f_Mensaje(__FILE__,__LINE__,"DO/Imp ya existe en el Modulo Facturacion, Verifique");
                } //Fin
              break;
            }
            //$_POST['cDoiSfId']
          }

          if ($cs == "" || $_POST['cDoiSfId'] == "" || $_POST['cAdmId'] == "") {
            $zSwitch = 1;
            f_Mensaje(__FILE__,__LINE__,"El Numero de DO/Imp, el Sufijo o la Sucursal No Pueden Ser Vacio, Verifique");
          }

          /**
          * Validando el Consecutivo Numerico si la variable de activar consecutivo del sistema esta en SI
          */
          if($vSysStr['importaciones_activar_control_consecutivo_do_numerico'] == "SI"){
            if(!is_numeric($cs)){
              $zSwitch = 1;
              f_Mensaje(__FILE__, __LINE__,"El Numero del Do, Debe ser Numerico, Verifique");
            }
          }

          $cDoiApeU = "";
          if ($kMysqlDb == "DEDHLEXPRE" || $kMysqlDb == "TEDHLEXPRE" || $kMysqlDb == "DHLEXPRE"){
            $cDoiApeU = $kUser;
          }

          if ($zSwitch == 0) {
            $cs = str_replace(array('"', "'", '&', '#', ' ', '/', '=', '*', '+', '.', ':', ',', ';', '%', '@', '$', '!'), '', trim(strtoupper($cs)));
            $cs = str_replace(array(chr(27), chr(9), chr(13), chr(10), chr(96), chr(92), chr(180)), '', $cs);
            $zInsert = array('DOIIDXXX'=>'"'.$cs.'"',
              'DOISFIDX'=>'"'.$_POST['cDoiSfId'].'"',
              'ADMIDXXX'=>'"'.$_POST['cAdmId'].'"',
              'DOITIPXX'=>'"'."IMPORTACION".'"',
              'DOITITRA'=>'"'.$_POST['cDoiTitra'].'"',
              'CLIIDXXX'=>'"'.$_POST['cCliId'].'"',
              'CLIUAPXX'=>'"'.$_POST['cCliUap'].'"',
              'CLIID2XX'=>'"'.$_POST['cCliId2'].'"',
              'USRIDXXX'=>'"'.$_POST['cUsrId'].'"',
              'USRID2XX'=>'"'.$_POST['cUsrId2'].'"',
              'USRID3XX'=>'"'.$_POST['cUsrId3'].'"',
              'USRID4XX'=>'"'.$_POST['cUsrId4'].'"',
              'USRID5XX'=>'"'.$_POST['cUsrId5'].'"',
              'USRID6XX'=>'"'.$_POST['cUsrId6'].'"',
              'DOIDTGXX'=>1,
              'DAUIDXXX'=>'"'.$_POST['cDauId'].'"',
              'DEPIDXXX'=>'"'.$_POST['cDepId'].'"',
              'CIUIDXXX'=>'"'.$_POST['cCiuId'].'"',
              'TDEIDXXX'=>'"'.$_POST['cTdeId'].'"',
              'TDTIDXXX'=>'"'.$_POST['cTdtId'].'"',
              'LINIDXXX'=>'"'.$_POST['cLinId'].'"',
              'ODIIDXXX'=>'"'.$_POST['cOdiId'].'"',
              'TRAIDXXX'=>'"'.$_POST['cTraId'].'"',
              'TRAODIXX'=>'"'.$_POST['cTraOdi'].'"',
              'MONIDXXX'=>'"'.$_POST['cMonIdGa'].'"',
              'MONIDSGX'=>'"'.$_POST['cMonIdSe'].'"',
              'PAIIDXXX'=>'"'.$_POST['cPaiId'].'"',
              'DAAIDXXX'=>'"'.$_POST['cDaaId'].'"',
              'AGCIDXXX'=>'"'.$_POST['cAgcId'].'"',
              'DOINECAP'=>'"'.($_POST["cDoiNecAp"] == "on" ? "SI" : "NO").'"',
              'DOITOPMX'=>'"'.$_POST["cDoiTopM"].'"',
              'CSEIDXXX'=>'"'.$_POST['cCseId'].'"',
              'MTRIDXXX'=>'"'.$_POST['cMtrId'].'"',
              'AUXIDXXX'=>'"'.$_POST['cAuxId'].'"',
              'PAIBANID'=>'"'.$_POST['cPaiBanId'].'"',
              'DEPID2XX'=>'"'.$_POST['cDepId2'].'"',
              'DOIPEDXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiPed']))).'"',
              'DOIFEPED'=>'"'.$_POST['dDoiFePed'].'"',
              'CLINOMAD'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cCliNomAd']))).'"',
              'DOICLICO'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiCliCo']))).'"',
              'DOICLICI'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiCliCi']))).'"',
              //'DOIOBSXX'=>'"'.str_replace(array('"',"'"),array('\"',"\'"),trim(strtoupper($_POST['cDoiObs']))).'"',
              'DOIOBS2X'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($cObsf2))).'"',
              'DOIEUNRO'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuNro']))).'"',
              'DOIEUFEC'=>'"'.$_POST['dDoiEuFec'].'"',
              'DOIEUENC'=>'"'.str_replace($cBuscar,$cReempl, trim(strtoupper($_POST['cDoiEuEnc']))).'"',
              'DOIEUTIP'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuNro']))).'"',
              'DOIEUPOL'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuPol']))).'"',
              'DOIEUFPO'=>'"'.$_POST['dDoiEuFPo'].'"',
              'DOIEUTIP'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuTip']))).'"',
              'DOIEUDIL'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuDil']))).'"',
              'DOIEUFDI'=>'"'.$_POST['dDoiEuFDi'].'"',
              'DOIEUACT'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuAct']))).'"',
              'DOIEUFAC'=>'"'.$_POST['dDoiEuFAc'].'"',
              'DOIEULEV'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuLev']))).'"',
              'DOIEUFLE'=>'"'.$_POST['dDoiEuFLe'].'"',
              'DOIEUCIU'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuCiu']))).'"',
              'DOIEUPEN'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuPen']))).'"',
              'DOIEUCAR'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuCar']))).'"',
              'DOIORIXX'=>'"'."CREACIONDOIMPO".'"',
              'DOIAPEXX'=>'"'.f_Fecha().'"',
              'DOIAPEHO'=>'"'.f_Hora().'"',
              'REGFECXX'=>'"'.f_Fecha().'"',
              'REGMODXX'=>'"'.f_Fecha().'"',
              'REGHORXX'=>'"'.f_Hora().'"',
              'REGESTXX'=>'"'."ACTIVO".'"',
              'TCATASAX'=>0 + $_POST['nTcaT1'],
              'TCAFECXX'=>'"'.$_POST['dTcaFec1'].'"',
              'DGESIMXX'=>$nSim,
              'DGESIDUN'=>$nSid,
              'DGETRDIF'=>$nTrd,
              'DOIRBPSE'=>$nPse,
              'DOIAPDTA'=>$nDta,
              'DOIAPEQU'=>$nEqu,
              'DGEMCXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDgeMc']))).'"',
              'DGEFMCXX'=>'"'.$_POST['dDgeFmc'].'"',
              'DGEHMCXX'=>'"'.$_POST['hDgeHmc'].'"',
              'DGEDTXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim($_POST['cDgeDt'])).'"',
              'DGEFDTXX'=>'"'.$_POST['dDgeFdt'].'"',
              'DGEAFLXX'=>$nAfl,
              'DGEAGVXX'=>$nAgv,
              'DGELIQVL'=>$nLva,
              'DGELIQPE'=>$nLiqPE,
              'DGELIQPV'=>$nLiqPV,
              'DGENLFOB'=>$nNlfob,
              'DGEACIXX'=>$nDgeAci,
              'DOIREEXP'=>'"'.($_POST['cDoiReeXp'] == true ? "SI" : "NO").'"',
              'CLIPTSXX'=>0 + $_POST['nCliPts'],
              'DGEBULXX'=>0 + $_POST['nDgeBul'],
              'DGEPBRXX'=>0 + $_POST['nDgePbr'],
              'DGEPNTXX'=>0 + $_POST['nDgePnt'],
              'DGEVOLXX'=>0 + $_POST['nDgeVol'],
              'DGEFOBXX'=>0 + $_POST['nDgeFob'],
              'DGEFLEXX'=>0 + $_POST['nDgeFle'],
              'DGECONXX'=>0 + $_POST['nDgeCon'],
              'DGEVARXX'=>0 + $_POST['nDgeVar'],
              'DGESGEXX'=>0 + $_POST['nDgeSge'],
              'GRMCPIXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cGrmCpi']))).'"',
              'TIITENOC'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteNoc']))).'"',
              'TIITEMAC'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteMac']))).'"',
              'TIITETIP'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteTip']))).'"',
              'TIITECLA'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteCla']))).'"',
              'TIITEMOD'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteMod']))).'"',
              'TIITEREF'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteRef']))).'"',
              'TIITEANO'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteAno']))).'"',
              'TIITEOTC'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteOtc']))).'"',
              'PRYIDXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cPryId']))).'"',
              'TERIDXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTerId']))).'"',
              'DGCPERTE'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDgcPerte']))).'"',
              'GMOIDXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cGmoId']))).'"',
              'DOCVENXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($cVenId))).'"',
              'SUCCOMXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($cSucCom))).'"',
              'PRYID2XX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($vcpryid2))).'"',
              'LPRIDXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($vclprid))).'"',
              'LPRID2XX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($vclprid2))).'"',
              'LPRID3XX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cLprId3']))).'"',
              'DOIAVPRX'=>'"'.($_POST['cDoiAvpR'] == "on" ? "SI" : "NO").'"',
              'DOIMOIMA'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cMimId']))).'"',
              'LNECODXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cLneCod']))).'"',
              'DOIAIMPA'=>'"'.($_POST['cDoiAimPa'] == "on" ? "SI" : "NO").'"',
              'DOIAPPRY'=>0 + $vfchpry,
              'DOIAPPRO'=>0 + $vfchpro,
              'DIVIDXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDivId']))).'"',
              'DOIAPCRI'=>'"'.$cDoiApcri.'"',
              'DOIFPCRI'=>'"'.$cDoiFpcri.'"',
              'DOIHPCRI'=>'"'.$cDoiHpcri.'"',
              'DOIATRAX'=>'"'.trim(strtoupper($_POST['cDoiAtrA'])).'"',
              'DOIDATRA'=>'"'.trim(strtoupper($_POST['cDoiDatRa'])).'"',
              'DOINODXX'=>'"'.trim(strtoupper($_POST['cDoiNumOpeDoc'])).'"',
              'DOISAPXX'=>'"'.trim(strtoupper($_POST['cCodSap'])).'"', //Codigo Sap Malco 2014-05-22
              'DOIEAVTD'=>'"'.$cAler.'"',
              'DOIDPROX'=>'"'.$_POST['cDoiDpro'].'"',
              'DOICPROX'=>'"'.$_POST['cDoiCpro'].'"',
              'DOIFINGX'=>'"'.$_POST['cDoiFin'].'"',
              'DOIGADES'=>'"'.$_POST['nDoiGaDes'].'"',
              'DOITRANA'=>'"'.$_POST['nDoiTraNa'].'"',
              'DOIFULLD'=>'"'.($_POST['cDoiFullD']== "on" ? "SI" : "NO").'"',
              'DOIDDPGE'=>'"'.($_POST['cDoiDdpGe']== "on" ? "SI" : "NO").'"',
              'DOIPADHL'=>'"'.($_POST['cDoiPaDhl']== "on" ? "SI" : "NO").'"',
              'DOIPDESX'=>'"CO"',
              'DOIDDESX'=>'"'.$_POST['cDoiDdes'].'"',
              'DOICDESX'=>'"'.$_POST['cDoiCdesc'].'"',
              'DOIDTACE'=>'"'.(($_POST['cDoiDtaCe'] == "on") ? "SI" : "NO").'"',
              'DOIDIGITA'=>'"'.(($_POST['cDoiDigita'] == "on") ? "SI" : "NO").'"',
              'DOILICHQ'=>'"'.(($_POST['cDoiLiChq'] == "on") ? "SI" : "NO").'"',
              'DOIEEI20'=>'"'.(($_POST['cDoiEei20'] == "on") ? "SI" : "").'"',
              'CCOIDXXX'=>'"'.$_POST['cCcoId'].'"',
              'SCCIDXXX'=>'"'.$_POST['cSccId'].'"',
              'DOIDMERX'=>'"'.$_POST['cDoiDmeR'].'"',
              'DOINUMVA'=>'"'.$_POST['cDoiNumVa'].'"',
              'DOIOBSAL'=>'"'.$_POST['cDoiObsAl'].'"',
              'EENNUMXX'=>'"'.$_POST['cEenNum'].'"',
              'DOIDIPPO'=>'"'.($_POST['cDoiDipPo'] == "on" ? "SI" : "NO").'"',
              'DOIHANDL'=>0 + $_POST['nDoiHanD'],
              'DOIIVAHA'=>0 + $_POST['nDoiIvaH'],
              'ORDIDINX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cOrdIdIn']))).'"',
              'ORDSERIN'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cOrdSerIn']))).'"',
              'DOIHENIN'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiHenIn']))).'"',
              'BANIDINX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cBanIdIn']))).'"',
              'BANCTAIN'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cBanCtaIn']))).'"',
              'DOICORIX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiCorI']))).'"',
              'DOICIDES'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiCidEs']))).'"',
              'PEMIDXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cPemId']))).'"',
              'DOINFEU4'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiNFeU4']))).'"',
              'DOIAPEUX'=>'"'.$cDoiApeU.'"',
              'DOICOMIX'=>0 + $_POST['nDoiComI']);

            $mysql->f_Insertar("SIAI0200", $zInsert);

            if ($kMysqlDb == "DEDHLEXPRE" || $kMysqlDb == "TEDHLEXPRE" || $kMysqlDb == "DHLEXPRE"){
              //Envío Notificación CheckPoints
              $vDatos = array();
              $vDatos['DOIIDXXX'] = $cs;
              $vDatos['DOISFIDX'] = $_POST['cDoiSfId'];
              $vDatos['ADMIDXXX'] = $_POST['cAdmId'];
              $mReturnCheckPointEmailCreacionDo = $oCheckpointsExpress->fnCheckPointEmailCreacionDo($vDatos);
              $mysql->f_Conectar();
              $mysql->f_SelecDb();
              $cMsjCheP = "";
              if($mReturnCheckPointEmailCreacionDo[0] == "false"){
                for($nR=1;$nR<count($mReturnCheckPointEmailCreacionDo);$nR++){
                  $cMsjCheP  = "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsjCheP .= $mReturnCheckPointEmailCreacionDo[$nR]."\n";
                }
                f_Mensaje(__FILE__,__LINE__,$cMsjCheP);
              }
            }

            /// nuevo logger
            $arLog1 = array();
            $arLog2 = array();
            $cnueclon = "INSERT";
            $cValo2 = "";
            if ($isclonlg == 1) {
              $cnueclon = "CLONE";
              $cValo2 = trim(strtoupper($_POST['cClonlg']));
            }
            $cLlavelg = $cs."=>".$_POST['cDoiSfId']."=>".$_POST['cAdmId'];
            f_DatosCambio($arLog1, $arLog2, "SIAI0200", "|DOIIDXXX=>DOISFIDX=>ADMIDXXX|", $cLlavelg, $cValo2, $cnueclon, $kUser, f_Fecha(), f_Hora2(), ipCheck(), $_SERVER['PHP_SELF']);
            // Fin nuevo logger
            /// Nueva observacion
            if ($isnvaob == 1) {
              $cTiOb = "IMPORTACION";
              $cFecOb = f_Fecha();
              $cHorOb = f_Hora2();
              $zInsert2 = array('docidxxx'=>'"'.$cs.'"',
                'docsufxx'=>'"'.$_POST['cDoiSfId'].'"',
                'sucidxxx'=>'"'.$_POST['cAdmId'].'"',
                'doctipxx'=>'"'.$cTiOb.'"',
                'obsobsxx'=>'"'.$_POST['cObs1'].'"',
                'obsobs2x'=>'"'."NO".'"',
                //'obstipxx'=>'"'."AGENCIA".'"',
                'regusrxx'=>'"'.$kUser.'"',
                'regfcrex'=>'"'.$cFecOb.'"',
                'reghcrex'=>'"'.$cHorOb.'"',
                'regfmodx'=>'"'.$cFecOb.'"',
                'reghmodx'=>'"'.$cHorOb.'"',
                'regestxx'=>'"'."ACTIVO".'"');
              $mysql->f_Insertar("sys00017", $zInsert2);
            }
            //fin Nueva observacion operativa

            if ($isnvaob2 == 1) {
              $cTiOb = "IMPORTACION";
              $cFecOb = f_Fecha();
              $cHorOb = f_Hora2();
              $zInsert2 = array('docidxxx'=>'"'.$cs.'"',
                'docsufxx'=>'"'.$_POST['cDoiSfId'].'"',
                'sucidxxx'=>'"'.$_POST['cAdmId'].'"',
                'doctipxx'=>'"'.$cTiOb.'"',
                'obsobsxx'=>'"'.$cObsf2.'"',
                'obsobs2x'=>'"'."SI".'"',
                //'obstipxx'=>'"'."AGENCIA".'"',
                'regusrxx'=>'"'.$kUser.'"',
                'regfcrex'=>'"'.$cFecOb.'"',
                'reghcrex'=>'"'.$cHorOb.'"',
                'regfmodx'=>'"'.$cFecOb.'"',
                'reghmodx'=>'"'.$cHorOb.'"',
                'regestxx'=>'"'."ACTIVO".'"');
              $mysql->f_Insertar("sys00017", $zInsert2);
            }

            if ($_POST['cDoiSfId'] == '001' && $tempo == 0) {
              $aCampos = array('LINCSCXX'=>'"'.$zCsc2.'"');
              $aLlave = array('LINIDXXX'=>'"'.$_POST['cAdmId'].'"');
              $mysql->f_Actualizar("SIAI0119", $aCampos, $aLlave);
            }

            if ($isDta == 1) {
              $zInsert = array('DOIIDXXX'=>'"'.$cs.'"',
                'ADMIDXXX'=>'"'.$_POST['cAdmId'].'"',
                'REGFECXX'=>'"'.f_Fecha().'"',
                'REGMODXX'=>'"'.f_Fecha().'"',
                'REGHORXX'=>'"'.f_Hora().'"',
                'REGESTXX'=>'"'."ACTIVO".'"');
              $mysql->f_Insertar("dta00200", $zInsert);
            }

            if ($isEqu == 1) {
              $zInsert = array('DOIIDXXX'=>'"'.$cs.'"',
                'ADMIDXXX'=>'"'.$_POST['cAdmId'].'"',
                'DOISFIDX'=>'"'.$_POST['cDoiSfId'].'"',
                'CLIIDXXX'=>'"'.$_POST['cCliId'].'"',
                'EQUDTRXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim($_POST['cDgeDt'])).'"',
                'REGFECXX'=>'"'.f_Fecha().'"',
                'REGMODXX'=>'"'.f_Fecha().'"',
                'REGHORXX'=>'"'.f_Hora().'"',
                'REGESTXX'=>'"'."ACTIVO".'"');
              $mysql->f_Insertar("SIAI0142", $zInsert);
            }

            /////INTERNAC
            if ($kMysqlDb == "INTERNAC") {
              internacp1($cs, $cDoiSfId, $cAdmId, $cCliId, $cDoiPed);
            } else {
              //Do Financiero
              if ($vSysStr['system_aplica_financiero'] == "SI") {
                $cMedio = "MARITIMO";
                if (strlen($_POST['cMtrId']) > 0) {
                  switch ($_POST['cMtrId']) {
                    case '3': $cMedio = "TERRESTRE"; break;
                    case '4': $cMedio = "AEREO";     break;
                  }
                }
                $cAdmId2 = $cAdmId;

                if (substr_count($vSysStr['alpopular_db_aplica'], $kMysqlDb) > 0) {
                  $cAdmId2 = fldesc("siai1100", "admidxxx", "sucidxxx", $cAdmId);
                }

                $cDocTep = "GENERAL";
                $cDocTepId = "100";
                if ($vfchpry == 1) {
                  $cDocTep = "PROYECTO";

                  if (substr_count($vSysStr['alpopular_db_aplica'], $kMysqlDb) > 0) {
                    $cDocTepId = $vcpryid;
                  } else {
                    $cDocTepId = $vcpryid2;
                  }
                }

                if ($vfchpro == 1) {
                  $cDocTep = "PRODUCTO";

                  if (substr_count($vSysStr['alpopular_db_aplica'], $kMysqlDb) > 0) {
                    $cDocTepId = $vclprid;
                  } else {
                    $cDocTepId = $vclprid2;
                  }
                }

                switch($kMysqlDb) {
                  case "DESIACOSIP":
                  case "TESIACOSIP":
                  case "SIACOSIA":
                    $qDoiFin = "SELECT ccoidxxx,sccidxxx ";
                    $qDoiFin .= "FROM $cAlfa.sys00121 ";
                    $qDoiFin .= "WHERE ";
                    $qDoiFin .= "$cAlfa.sys00121.docidxxx = \"$cs\" LIMIT 0,1 ";
                    $xDoiFin = f_MySql("SELECT", "", $qDoiFin, $xConexion01, "");
                    $vSDoiFin= mysql_fetch_array($xDoiFin);
                    $cCcoId  = $vSDoiFin["ccoidxxx"];
                    $cSccId  = $vSDoiFin["sccidxxx"];
                    $cComId  = "P";
                    $cComCod = "028";
                  break;
                  default:

                    // Consultar si existe el centro de costo 0201 para IMPORTACION
                    $qCcoImp  = "SELECT ccoidxxx ";
                    $qCcoImp .= "FROM $cAlfa.fpar0008 ";
                    $qCcoImp .= "WHERE ";
                    $qCcoImp .= "sucidxxx = \"$cAdmId2\" AND ";
                    $qCcoImp .= "sucopexx = \"IMPORTACION\" AND ";
                    $qCcoImp .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
                    $xCcoImp  = f_MySql("SELECT", "", $qCcoImp, $xConexion01, "");
                    // f_Mensaje(__FILE__,__LINE__,$qCcoImp."~".mysql_num_rows($xCcoImp));
                    if(mysql_num_rows($xCcoImp) == 1){
                      $vCcoImp = mysql_fetch_array($xCcoImp);
                      $cCcoId  = $vCcoImp['ccoidxxx'];
                      $cComId  = "P";
                      $cComCod = "001";
                      $cSccId  = "";
                    }else{
                      //Consulto el Centro de Costos normalmente
                      $qCcoId  = "SELECT ccoidxxx ";
                      $qCcoId .= "FROM $cAlfa.fpar0008 ";
                      $qCcoId .= "WHERE ";
                      $qCcoId .= "sucidxxx = \"$cAdmId2\" AND ";
                      $qCcoId .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
                      $xCcoId  = f_MySql("SELECT", "", $qCcoId, $xConexion01, "");
                      // f_Mensaje(__FILE__,__LINE__,$qCcoId."~".mysql_num_rows($xCcoId));
                      $vCcoId  = mysql_fetch_array($xCcoId);
                      $cCcoId  = $vCcoId['ccoidxxx'];
                      $cComId  = "P";
                      $cComCod = "001";
                      $cSccId  = "";
                    }

                  break;
                }

                $cPucId = "2805050000";
                $cDocFma = "NO";
                $cDocFrs = "PRINCIPAL";
                f_DO_Financiero($cs, $_POST['cDoiSfId'], $cAdmId, $cTipoDo, $cCliId, $cDoiPed, $_POST['cUsrId2'], $_POST['cUsrId'], $cMedio, $cCcoId, $cComId, $cComCod, $cPucId, $cDocTep, $cDocTepId, $cDocFma, $cDocFrs, $cVenId, $cSucCom,'','',$cSccId);
              }
              // Fin Do Financiero

              if (substr_count($vSysStr['alpopular_db_aplica'], $kMysqlDb) > 0 && $vSysStr['alpopular_activar_seven_creacion_do'] == "SI") {
                if (strlen($cs) == 13) {
                  $fFecha = f_Fecha();
                  $fHora = f_Hora();
                  $cDoiAg = $cs."~".$_POST['cDoiSfId']."~".$_POST['cAdmId']."~".$fFecha."~".$fHora;
                  $cWsId = "100";
                  $cWsDes = "CREACION DO / IMP ALPOPULAR";
                  $cWsTip = "OUT";
                  $cWsPhp = "utiwsout.php";
                  $cWsMet = "f_Crear_RArea_Tercero";
                  $cWsPar = $cDoiAg;
                  $cWsDo = $cDoiAg;
                  $cWsUsr = $kUser;
                  $cWsFec = $fFecha;
                  $cWsHor = $fHora;
                  //$cWsFecm = $fFecha;
                  //$cWsHorm = $fHora;
                  $cWsEst = "ACTIVO";

                  $zInsert = "INSERT INTO sysagews (ageidxxx,agesucxx,agedesxx,agetipxx,agephpxx,agemetxx,ageparxx,agedoxxx,regusrxx,regfecxx,reghorxx,regfmodx,reghmodx,regestxx)";
                  $zInsert .= " VALUES (";
                  $zInsert .= "\"$cWsId\",";
                  $zInsert .= "\"{$_POST['cAdmId']}\",";
                  $zInsert .= "\"$cWsDes\",";
                  $zInsert .= "\"$cWsTip\",";
                  $zInsert .= "\"$cWsPhp\",";
                  $zInsert .= "\"$cWsMet\",";
                  $zInsert .= "\"$cDoiAg\",";
                  $zInsert .= "\"$cDoiAg\",";
                  $zInsert .= "\"$kUser\",";
                  $zInsert .= "\"$cWsFec\",";
                  $zInsert .= "\"$cWsHor\",";
                  $zInsert .= "\"$cWsFec\",";
                  $zInsert .= "\"$cWsHor\",";
                  $zInsert .= "\"$cWsEst\"";
                  $zInsert .= ")";

                  $cAgeWs = mysql_query($zInsert);
                }
              }
            }

            if($_POST['cEenNum'] != "" && $_POST['cEenNum'] != "NOAPLICA"){
              $qUpEntEnt  = array(array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']           ,'CHECK'=>'NO'),
                                  array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']         ,'CHECK'=>'NO'),
                                  array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']           ,'CHECK'=>'NO'),
                                  array('NAME'=>'EENNUMXX','VALUE'=>$_POST['cEenNum']          ,'CHECK'=>'WH'));
              f_MySql("UPDATE","SIAI0250",$qUpEntEnt,$xConexion01,$cAlfa);
            }
            /////

            switch ($kMysqlDb) {
              case "DHLEXPRE":
              case "TEDHLEXPRE":
              case "DEDHLEXPRE":
                if($cTextoPrealerta != "" && $_POST['cLeido'] == "SI"){
                  $vDatos = array();
                  $vDatos['ADMIDXXX'] = $_POST['cAdmId'];
                  $vDatos['DOIIDXXX'] = $cs;
                  $vDatos['DOISFIDX'] = $_POST['cDoiSfId'];
                  $vDatos['LPIMTEXT'] = "CLITAIMP"; 
                  $vDatos['ANIOXXXX'] = date("Y");
                  $mReturnCrearRegistroLecturaPrealertaImportador = $oProcesosExpress->fnCrearRegistroLecturaPrealertaImportador($vDatos);
                  if($mReturnCrearRegistroLecturaPrealertaImportador[0] == "false"){
                    $cMsjAlerta = "";
                    for ($nE=1; $nE < count($mReturnCrearRegistroLecturaPrealertaImportador); $nE++){
                      $cMsjAlerta .= $mReturnCrearRegistroLecturaPrealertaImportador[$nE] ."\n";
                    }
                    f_Mensaje(__FILE__, __LINE__,$cMsjAlerta."\nVerifique.");
                  }
                }
              break;
            }

            switch ($cAlfa) {
              case "SIACOSIA":
              case "TESIACOSIP":
              case "DESIACOSIP":
                $vDatos = array();
                $vDatos['admidxxx'] = $_POST['cAdmId'];
                $vDatos['doiidxxx'] = $cs;
                $vDatos['doisfidx'] = $_POST['cDoiSfId'];
                $vDatos['tdeidxxx'] = $_POST['cTdeId'];
                $vDatos['mtridxxx'] = $_POST['cMtrId'];
                $vDatos['paiidxxx'] = $_POST['cPaiId'];
                $vDatos['pemidxxx'] = $_POST['cPemId'];
                $vDatos['linidxxx'] = $_POST['cLinId'];
                $vDatos['cliidxxx'] = $_POST['cCliId'];
                $vDatos['daaidxxx'] = $_POST['cDaaId'];
                $vDatos['doiatrax'] = $_POST['cDoiAtrA'];
                $vDatos['doidatra'] = $_POST['cDoiDatRa'];
                $vDatos['dgemcxxx'] = $_POST['cDgeMc'];
                $vDatos['dgefmcxx'] = $_POST['dDgeFmc'];

                $mReturnTrazabilidadDatosControlAnticipadas = $oProcesosSiaco->fnTrazabilidadDatosControlAnticipadas($vDatos);
                if($mReturnTrazabilidadDatosControlAnticipadas[0] == "false"){
                  $cMsjAnt = "";
                  for($nE=1; $nE<count($mReturnTrazabilidadDatosControlAnticipadas); $nE++) {
                    $cMsjAnt = $mReturnTrazabilidadDatosControlAnticipadas[$nE]."\n";
                  }
                  f_Mensaje(__FILE__, __LINE__,$cMsjAnt."Verifique.");
                }
              break;
            }

            f_Mensaje(__FILE__, __LINE__, "Datos Guardados con Exito");
            $docook = $cs."~".$_POST['cDoiSfId']."~".$_POST['cAdmId'];

            /**
             * Asignando Do a Numero de operacion documental
             */
            if ($vSysStr['opensmart_activar_modulo'] == "SI") {
              if ($_POST['cDoiNumOpeDoc'] != "") {
                //Solo se envian los Numero de operacion Nuevos
                $mDatIni = explode("~", $_POST['cDoiNumOpeDocIni']);
                $mDat = explode("~", $_POST['cDoiNumOpeDoc']);
                $nError = 0;
                $nEncontro = 0;
                $cMsjAsig = "";
                for ($nD = 0; $nD < count($mDat); $nD++) {
                  if ($mDat[$nD] != "") {
                    if (in_array($mDat[$nD], $mDatIni) == false) {
                      $nEncontro = 1;
                      //Llamado al WS de SyC para Asignar Do a Numeros de operacion
                      //Llamado al WS de SyC para Asignar Do a Numeros de operacion
                      $oWSOutput = new cWSOutput;
                      $vDatos['numopexx'] = $mDat[$nD];
                      $vDatos['doiidxxx'] = $cs;          //-> Do
                      $vDatos['doisfidx'] = $_POST['cDoiSfId'];  //-> Sufijo
                      $vDatos['admidxxx'] = $_POST['cAdmId'];   //-> Sucursal
                      $vDatos['doitipxx'] = "IMPORTACION";     //-> Tipo de operacion
                      $vDatos['cliidxxx'] = $_POST['cCliId'];   //-> Cliente
                      $vDatos['usridxxx'] = $kUser;        //-> Usuario que Asocia el DO al numero de operacion.
                      $vReturn = $oWSOutput->fnAsingarOperacionDocumental($vDatos);

                      if ($vReturn[0] == "false") {
                        $nError = 1;
                        for ($nR = 1; $nR < count($vReturn); $nR++) {
                          $cMsjAsig .= "- ".$vReturn[$nR]."\n";
                        }
                      }
                    }
                  }
                }

                if ($nEncontro == 1) {
                  ?>
                  <script language="javascript">
                    var cRuta = "frgesdoc.php?gDocId=<?php echo $cs ?>"
                            + "&gDocSuf=<?php echo $_POST['cDoiSfId'] ?>"
                            + "&gSucId=<?php echo $_POST['cAdmId'] ?>"
                            + "&gCliId=<?php echo $_POST['cCliId'] ?>"
                            + "&gOpcion=EDITAR";
                    parent.fmpro2.location = cRuta;
                  </script>
                  <?php
                } else {
                  if ($nError == 1) {
                    f_Mensaje(__FILE__, __LINE__, $cMsjAsig);
                  }
                }
              }
            }
            ?>
            <form name = "frnav" action = "frdoiini.php" method = "post" target = "fmwork"></form>
            <script language = "javascript">
              parent.fmwork.f_CreaCookie('kDoImp', '<?php echo $docook ?>');
              parent.fmnav.location = '../nivel3.php';
              document.forms['frnav'].submit();
            </script>
            <?php
          }
        }
      break;
      case "EDITAR":
        $_POST['cObs1'] = str_replace(array(chr(27), chr(9), chr(13), chr(10), chr(96), chr(92), chr(180)), ' ', $_POST['cObs1']);
        $cObsf = $_POST['cDoiObs'];
        $_POST['cObs2'] = str_replace(array(chr(27), chr(9), chr(13), chr(10), chr(96), chr(92), chr(180)), ' ', $_POST['cObs2']);
        $cObsf2 = $_POST['cDoiObs2'];

        $isnvaob = 0;
        $isnvaob2 = 0;
        if (strlen($_POST['cObs1']) > 0) {
          $isnvaob = 1;
          $cadwh = f_Fecha().'_'.f_Hora();
          $usrs = fldesc("SIAI0003", "USRIDXXX", "USRNOMXX", $kUser);
          $cadwh .= " $usrs - ";
          //$_POST['cDoiObs'] = '|___'.$cadwh.strtoupper($_POST['cObs1']).'___|'.$cObsf;
          $_POST['cObs1'] = '|___'.$cadwh.strtoupper($_POST['cObs1']).'___|';
        }

        for ($xn = 0; $xn < 10; $xn++) {
          $cObsf = str_replace(array("      ", "     ", "    ", "   ", "  "), " ", $cObsf);
        }

        $_POST['cDoiObs2'] = str_replace(array(chr(27), chr(9), chr(13), chr(10), chr(96), chr(92), chr(180)), ' ', $_POST['cDoiObs2']);
        $cObsf = $_POST['cDoiObs'];
        $cObsf2 = $_POST['cDoiObs2'];
        for ($xn = 0; $xn < 10; $xn++) {
          $cObsf2 = str_replace(array("      ", "     ", "    ", "   ", "  "), " ", $cObsf2);
        }

        if (strlen($_POST['cDoiObs2']) > 0) {
          $isnvaob2 = 1;
          $cadwh = f_Fecha().'_'.f_Hora();
          $usrs = fldesc("SIAI0003", "USRIDXXX", "USRNOMXX", $kUser);
          $cadwh .= " $usrs - ";
          $cObsf2 = '|___'.$cadwh.$cObsf2.'___|';
        }

        $isDta = 0;
        $nDta = 0;
        if (trim(strtoupper($_POST["cDoiDta"])) == "ON") {
          $nDta = 1;
          $cTipoDo = "TRANSITO";
          $sqlDta = "SELECT doiidxxx,admidxxx from dta00200 where doiidxxx = \"{$_POST['cDoiId']}\" AND admidxxx = \"{$_POST["cAdmId"]}\" LIMIT 0,1";
          $zCrsDta = $mysql->f_Ejecutar($sqlDta);
          $nFilDta = $mysql->f_ContarFilas($zCrsDta);
          if ($nFilDta <= 0) {
            $isDta = 1;
          }
        }
        $isEqu = 0;
        $nEqu = 0;
        if (trim(strtoupper($_POST["cDoiEqu"])) == "ON") {
          $nEqu = 1;
          $cTipoDo = "OTROS";
          $sqlEqu = "SELECT DOIIDXX,ADMIDXXX FROM SIAI0142 WHERE DOIIDXXX = \"{$_POST['cDoiId']}\" AND ADMIDXXX = \"{$_POST["cAdmId"]}\" AND DOISFIDX = \"{$_POST["cDoiSfId"]}\" LIMIT 0,1";
          $zCrsEqu = $mysql->f_Ejecutar($sqlEqu);
          $nFilEqu = $mysql->f_ContarFilas($zCrsEqu);
          if ($nFilEqu == 0) {
            $isEqu = 1;
          }
        }

        if ($nEqu == 1 && $nDta == 1) {
          $zSwitch = 1;
          f_Mensaje(__FILE__, __LINE__, "El tramite puede ser DTA o Equipaje, no ambos, Verifique");
        }

        /**
         * Validacion deposito versus modalidades
         */
        if (strlen($_POST['cDaaId']) > 0) {
          $qSelDo = "SELECT DOIOVDMZ  FROM SIAI0200 ";
          $qSelDo .= "WHERE  DOIIDXXX = \"{$_POST['cDoiId']}\" AND ";
          $qSelDo .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
          $qSelDo .= "ADMIDXXX = \"{$_POST['cAdmId']}\" AND ";
          $qSelDo .= "REGESTXX = \"ACTIVO\"";
          $xSelDo = f_MySql("SELECT", "", $qSelDo, $xConexion01, "");
          $vSelDo = mysql_fetch_array($xSelDo);
          //f_Mensaje(__FILE__,__LINE__,$vSelDo['DOIOVDMZ']);

          if ($vSelDo['DOIOVDMZ'] != "SI") {
            $cValidaParametrica = fldesc("SIAI0110", "DAAIDXXX", "DAAAPLZF", $_POST['cDaaId']);
            $xSelect = mysql_query("SELECT MODIDXXX,SUBIDXXX FROM SIAI0203 WHERE
                                    DOIIDXXX = \"{$_POST['cDoiId']}\" AND
                                    DOISFIDX = \"{$_POST['cDoiSfId']}\" AND
                                    ADMIDXXX = \"{$_POST['cAdmId']}\" AND
                                    REGESTXX = \"ACTIVO\"");
            if ($cValidaParametrica == "SI") {
              $cErrorModalidad = "";
              while (($zRMod = mysql_fetch_array($xSelect)) != false) {
                if (substr($zRMod['MODIDXXX'], 1, 1) != "2" && $zRMod['MODIDXXX'] != "C602" && $zRMod['MODIDXXX'] != "C603") {
                  $zSwitch = 1;
                  $cErrorModalidad .= "- Modalidad {$zRMod['MODIDXXX']} en subpartida {$zRMod['SUBIDXXX']} no es valida\n";
                }
              }
              if (strlen($cErrorModalidad) > 0) {
                f_Mensaje(__FILE__, __LINE__, $cErrorModalidad);
              }
            } else {
              $cErrorModalidad = "";
              while (($zRMod = mysql_fetch_array($xSelect)) != false) {
                if (substr($zRMod['MODIDXXX'], 1, 1) == "2" || $zRMod['MODIDXXX'] == "C602" || $zRMod['MODIDXXX'] == "C603") {
                  $zSwitch = 1;
                  $cErrorModalidad .= "- Modalidad {$zRMod['MODIDXXX']} en subpartida {$zRMod['SUBIDXXX']} no es valida\n";
                }
              }
              if (strlen($cErrorModalidad) > 0) {
                f_Mensaje(__FILE__, __LINE__, $cErrorModalidad);
              }
            }
          }
        }
        /**
         * Fin Validacion deposito versus modalidad
         */
        /**
         * Cargo el valor de la Hora de Manifiesto con la hora actual del sistema solo si no se ha digitado y si ya se digito
         * Numero de Manifiesto y Fecha de Manifiesto.
         */
        if ($_POST['cDgeMc'] != "" || ($_POST['dDgeFmc'] != "" || $_POST['dDgeFmc'] != "0000-00-00") && ($_POST['hDgeHmc'] == "" || $_POST['hDgeHmc'] == "00:00:00")) {
          $_POST['hDgeHmc'] = date('H:i:s');
        }

        /**
         * Se debe limpiar la Hora de Manifiesto si el Tipo de Declaracion es Anticipada y si se borro Manifiesto o Fecha
         */
        if ($_POST['cDgeMc'] == "" && $_POST['dDgeFmc'] == "" || $_POST['dDgeFmc'] == "0000-00-00") {
          $_POST['hDgeHmc'] = "00:00:00";
        }

        if ($kModo == "EDITAR" ) {
          //f_Mensaje(__FILE__,__LINE__,"frdoigra - EDISTAR ");
          // si la variable del sistema esta activa valido en facturación
          if ($vSysStr['system_aplica_financiero'] == "SI") {
            switch($kMysqlDb) {
              case "DESIACOSIP":
              case "TESIACOSIP":
              case "SIACOSIA":
                $qDocId  = "SELECT ";
                $qDocId .= "sucidxxx, ";
                $qDocId .= "docidxxx, ";
                $qDocId .= "docsufxx  ";
                $qDocId .= "FROM $cAlfa.sys00121 ";
                $qDocId .= "WHERE ";
                $qDocId .= "docidxxx = \"{$_POST['cDoiId']}\" LIMIT 0,1";
                //f_Mensaje(__FILE__,__LINE__,$qDocId."~".mysql_num_rows($xDocId));
                $xDocId  = f_MySql("SELECT","",$qDocId,$xConexion01,"");

                if (mysql_num_rows($xDocId) == 0){
                  $zSwitch = 1;
                  f_Mensaje(__FILE__,__LINE__,"El Do No Existe en el Modulo Contable, por favor Comuniquese con Help Desk.");
                } else {
                  //Se verifica que la sucursal y el sufijo sean el mismo en el modulo de facturacion para que no se cree el DO varias veces
                  $vDocId = mysql_fetch_array($xDocId);

                  if ($vDocId['sucidxxx'] != $_POST['cAdmId']) {
                    $zSwitch = 1;
                    f_Mensaje(__FILE__,__LINE__,"El Do Existe en el Modulo Contable con la Sucursal {$vDocId['sucidxxx']}, por favor Comuniquese con Help Desk.");
                  }

                  if ($vDocId['docsufxx'] != $_POST['cDoiSfId']) {
                    $zSwitch = 1;
                    f_Mensaje(__FILE__,__LINE__,"El Do Existe en el Modulo Contable con el Sufijo {$vDocId['docsufxx']}, por favor Comuniquese con Help Desk.");
                  }
                } //Fin
              break;
            }
          }
          /**
           * Si es roldan valido que se agrego la fecha de Levante
           */
          switch($kMysqlDb) {
            case "DEROLDANLO":
            case "TEROLDANLO":
            case "ROLDANLO":
              $qSelMan  = "SELECT ";
              $qSelMan .= "DOICWSCI,DGEFMCXX ";
              $qSelMan .= "FROM SIAI0200 ";
              $qSelMan .= "WHERE ";
              $qSelMan .= "DOIIDXXX = \"{$_POST['cDoiId']}\" AND ";
              $qSelMan .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
              $qSelMan .= "ADMIDXXX = \"{$_POST['cAdmId']}\" ";
              $xSelMan = f_MySql("SELECT", "", $qSelMan, $xConexion01, "");
              while($xRDM = mysql_fetch_array($xSelMan)){
                if($xRDM['DOICWSCI'] == "NO" && $xRDM['DGEFMCXX'] == "0000-00-00" && $_POST['dDgeFmc'] != "0000-00-00"){
                  $qUpDO  = array(array('NAME'=>'DOICWSCI','VALUE'=>""                         ,'CHECK'=>'NO'),
                                  array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']           ,'CHECK'=>'WH'),
                                  array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']         ,'CHECK'=>'WH'),
                                  array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']           ,'CHECK'=>'WH'));
                  f_MySql("UPDATE","SIAI0200",$qUpDO,$xConexion01,$cAlfa);
                }
              }

              //Valido si Aplica el Consecutivo del Pedido
              $vImportadoresHalli = explode(",",$vSysStr['roldanlo_nit_importador_halliburton']);
              if(in_array($_POST['cCliId'],$vImportadoresHalli)){
                if($_POST['cCamPed'] == "SI" && $_POST['cTipId'] != ""){
                  $_POST['cDoiPed'] = substr($_POST['cDoiPed'], 0, 5).$_POST['cTipId'].substr($_POST['cDoiPed'], 7, 9);
                }
              }
            break;
          }

          switch($kMysqlDb) {
            case "DEROLDANLO":
            case "TEROLDANLO":
            case "ROLDANLO":
              if($_POST['dDgeFmc'] != "0000-00-00" && $_POST['dDgeFmc'] != $_POST['dDgeFmc_ori']){
                /**
                 * Consultando Fecha de Apertura del Do, Fecha y Hora de Localizacion en SYGA
                 */
                $qFecApe  = "SELECT ";
                $qFecApe .= "DOIAPEXX,DOIFLOSI,DOIHLOSI ";
                $qFecApe .= "FROM SIAI0200 ";
                $qFecApe .= "WHERE ";
                $qFecApe .= "DOIIDXXX = \"{$_POST['cDoiId']}\" AND ";
                $qFecApe .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
                $qFecApe .= "ADMIDXXX = \"{$_POST['cAdmId']}\" LIMIT 0,1 ";
                $xFecApe = f_MySql("SELECT", "", $qFecApe, $xConexion01, "");
                $vFecApe = mysql_fetch_array($xFecApe);

                if($vFecApe['DOIFLOSI'] == "0000-00-00"){
                  if ($vSysStr['indicadores_activa_modulo'] == "SI" && $vFecApe['DOIAPEXX'] >= $vSysStr['indicadores_fecha_instalacion_modulo']) {
                    $qEtapasTramites = "SELECT ";
                    $qEtapasTramites .= "$cBeta.sys00027.etacampx ";
                    $qEtapasTramites .= "FROM $cBeta.sys00027 ";
                    $qEtapasTramites .= "WHERE ";
                    $qEtapasTramites .= "$cBeta.sys00027.etaidxxx = \"30000\" AND ";
                    $qEtapasTramites .= "$cBeta.sys00027.regestxx = \"ACTIVO\" ";
                    $xEtapasTramites = f_MySql("SELECT", "", $qEtapasTramites, $xConexion01, "");
                    $vEtapasTramites = mysql_fetch_array($xEtapasTramites);
                    //f_Mensaje(__FILE__,__LINE__,$qEtapasTramites."~".mysql_num_rows($xEtapasTramites));
                    //Armo vector de parametros para invocar el Metodo fnActualizaSaldosIndicadoresxDOxEtapa de la Clase cIndicadoresGestionImportaciones
                    $vDatos = array();
                    $vDatos['TIPOACTX'] = "ETAPA";     // Tipo de Actualizacion (ETAPA O AUTORIZACION)
                    $vDatos['ADMIDXXX'] = $_POST['cAdmId'];       // Sucursal del DO
                    $vDatos['DOIIDXXX'] = $_POST['cDoiId'];       // Numero del DO
                    $vDatos['DOISFIDX'] = $_POST['cDoiSfId'];       // Sucursal del DO
                    $vDatos['DATOSIND'] = $vEtapasTramites['etacampx']; // Campos del Indicador, son los campos de fecha y hora en la 200
                    $vDatos['FECHALSX'] = $_POST['dDgeFmc'];   // Fecha del Limite Superior
                    $vDatos['HORALSXX'] = date('H:i:s');    // Hora del Limite Superior
                    $vDatos['ETAPAEXC'] = ""; // Tapa Excluida
                    //f_Mensaje(__FILE__,__LINE__,$vDatos[TIPOACTX]."~".$vDatos['ADMIDXXX']."~".$vDatos['DOIIDXXX']."~".$vDatos['DOISFIDX']."~".$vDatos['DATOSIND']."~".$vDatos['FECHALSX']."~".$vDatos['HORALSXX']."~".$vDatos['ETAPAEXC']);
                    $oSaldosIndicadoresxDoxEtapa = new cIndicadoresGestionImportaciones();
                    $mReturnSaldoIndicadorxDo = $oSaldosIndicadoresxDoxEtapa->fnActualizaSaldosIndicadoresxDOxEtapa($vDatos);
                    //f_Mensaje(__FILE__,__LINE__,"mReturnSaldoIndicadorxDo[0]: ".$mReturnSaldoIndicadorxDo[0]);
                    $cMsjRetorno = "";
                    $cMsjObservacion = "";
                    if ($mReturnSaldoIndicadorxDo[0] == "true") {
                      //Se hace update de los campos SIAI0200.DOIFLOSI y SIAI0200.DOIHLOSI con los mismos valores de Fecha de Manifiesto y Hora(Hora actual del sistema).
                      if ($_POST['dDgeFmc'] != "0000-00-00" && $_POST['dDgeFmc'] != $_POST['dDgeFmc_ori']){
                        $qUpDO  = array(array('NAME'=>'DOIFLOSI','VALUE'=>$_POST['dDgeFmc']          ,'CHECK'=>'NO'),
                                        array('NAME'=>'DOIHLOSI','VALUE'=>date('H:i:s')              ,'CHECK'=>'NO'),
                                        array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']           ,'CHECK'=>'WH'),
                                        array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']         ,'CHECK'=>'WH'),
                                        array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']           ,'CHECK'=>'WH'));
                        f_MySql("UPDATE","SIAI0200",$qUpDO,$xConexion01,$cAlfa);
                      }
                    }else{
                      //Muestro los errores que me devolvio el Metodo
                      for ($i = 1; $i < count($mReturnSaldoIndicadorxDo); $i++) {
                        $cMsjRetorno .= $mReturnSaldoIndicadorxDo[$i]."\n";
                        $cMsjObservacion .= $mReturnSaldoIndicadorxDo[$i];
                      }//for($i=1;$i<count($mReturnSaldoIndicadorxDo);$i++){

                      if($cMsjObservacion != ""){
                        $cMsjObservacion = "No se Pudo Realizar la Actualizacion Automatica para Fecha y Hora de Localizacion en Syga.  ". $cMsjObservacion;
                      }

                      //observación automatica al Do.

                      $qObs = "INSERT INTO sys00017 (docidxxx, sucidxxx, docsufxx, obgidxxx, obsidxxx, doctipxx, obsobsxx, obsobs2x, obstipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx) ";
                      $qObs .= "VALUES ";
                      $qObs .= "(\"{$_POST['cDoiId']}\", \"{$_POST['cAdmId']}\", \"{$_POST['cDoiSfId']}\", \"\", \"\", \"IMPORTACION\", \"$cMsjObservacion \", \"NO\", \"AGENCIA\", \"$kUser\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\") ";
                      $xObs = f_MySql("SELECT", "", $qObs, $xConexion01, "");
                      //Se muestra Mensaje de alerta
                      if($cMsjRetorno != ""){
                        f_Mensaje(__FILE__,__LINE__, "No se Pudo Realizar la Actualizacion Automatica para Fecha y Hora de Localizacion en Syga. ".$cMsjRetorno);
                      }
                    }
                  }else{
                    //Se hace update de los campos SIAI0200.DOIFLOSI y SIAI0200.DOIHLOSI con los mismos valores de Fecha de Manifiesto y Hora(Hora actual del sistema).
                    if ($_POST['dDgeFmc'] != "0000-00-00" && $_POST['dDgeFmc'] != $_POST['dDgeFmc_ori']){
                      $qUpDO  = array(array('NAME'=>'DOIFLOSI','VALUE'=>$_POST['dDgeFmc']          ,'CHECK'=>'NO'),
                                      array('NAME'=>'DOIHLOSI','VALUE'=>date('H:i:s')              ,'CHECK'=>'NO'),
                                      array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']           ,'CHECK'=>'WH'),
                                      array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']         ,'CHECK'=>'WH'),
                                      array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']           ,'CHECK'=>'WH'));
                      f_MySql("UPDATE","SIAI0200",$qUpDO,$xConexion01,$cAlfa);
                    }
                  }
                }else{
                  $cMsjObservacion = "No se Actualiza Fecha y Hora de Localizacion en SYGA, Ya se Encuentra Diligenciada[{$vFecApe['DOIFLOSI']} {$vFecApe['DOIHLOSI']}]";
                  $qObs = "INSERT INTO sys00017 (docidxxx, sucidxxx, docsufxx, obgidxxx, obsidxxx, doctipxx, obsobsxx, obsobs2x, obstipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx) ";
                  $qObs .= "VALUES ";
                  $qObs .= "(\"{$_POST['cDoiId']}\", \"{$_POST['cAdmId']}\", \"{$_POST['cDoiSfId']}\", \"\", \"\", \"IMPORTACION\", \"$cMsjObservacion \", \"NO\", \"AGENCIA\", \"$kUser\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\") ";
                  $xObs = f_MySql("SELECT", "", $qObs, $xConexion01, "");
                  f_Mensaje(__FILE__,__LINE__,$cMsjObservacion);
                }
              }
            break;
          }
        }

        if ($_POST['cDoiId'] == "" || $_POST['cDoiSfId'] == "" || $_POST['cAdmId'] == "") {
          $zSwitch = 1;
          f_Mensaje(__FILE__,__LINE__,"El Numero de DO/Imp, el Sufijo o la Sucursal No Pueden Ser Vacio, Verifique");
        }

        if ($zSwitch == 0) {

          switch ($cAlfa) {
            case "SIACOSIA":
            case "TESIACOSIP":
            case "DESIACOSIP":
              $qDatTra  = "SELECT ";
              $qDatTra .= "TDEIDXXX,";
              $qDatTra .= "MTRIDXXX,";
              $qDatTra .= "PAIIDXXX,";
              $qDatTra .= "PEMIDXXX,";
              $qDatTra .= "LINIDXXX,";
              $qDatTra .= "CLIIDXXX,";
              $qDatTra .= "DAAIDXXX,";
              $qDatTra .= "DOIATRAX,";
              $qDatTra .= "DGEMCXXX,";
              $qDatTra .= "DGEFMCXX,";
              $qDatTra .= "DOIDATRA ";
              $qDatTra .= "FROM $cAlfa.SIAI0200 ";
              $qDatTra .= "WHERE ";
              $qDatTra .= "DOIIDXXX = \"{$_POST['cDoiId']}\" AND ";
              $qDatTra .= "DOISFIDX = \"{$_POST['cDoiSfId']}\"  AND ";
              $qDatTra .= "ADMIDXXX = \"{$_POST['cAdmId']}\" LIMIT 0,1";
              $xDatTra  = f_MySql("SELECT","",$qDatTra,$xConexion01,"");
              $vDatTra  = mysql_fetch_array($xDatTra);

              if($_POST['cTdeId'] != $vDatTra['TDEIDXXX'] || $_POST['cMtrId'] != $vDatTra['MTRIDXXX'] ||
                $_POST['cPaiId'] != $vDatTra['PAIIDXXX'] || $_POST['cPemId'] != $vDatTra['PEMIDXXX'] ||
                $_POST['cLinId'] != $vDatTra['LINIDXXX'] || $_POST['cCliId'] != $vDatTra['CLIIDXXX'] ||
                $_POST['cDaaId'] != $vDatTra['DAAIDXXX'] || $_POST['cDoiAtrA'] != $vDatTra['DOIATRAX'] ||
                $_POST['cDgeMc'] != $vDatTra['DGEMCXXX'] || $_POST['dDgeFmc'] != $vDatTra['DGEFMCXX'] ||
                $_POST['cDoiDatRa'] != $vDatTra['DOIDATRA']
              ){
                $vDatos = array();
                $vDatos['admidxxx'] = $_POST['cAdmId'];
                $vDatos['doiidxxx'] = $_POST['cDoiId'];
                $vDatos['doisfidx'] = $_POST['cDoiSfId'];
                $vDatos['tdeidxxx'] = $_POST['cTdeId'];
                $vDatos['mtridxxx'] = $_POST['cMtrId'];
                $vDatos['paiidxxx'] = $_POST['cPaiId'];
                $vDatos['pemidxxx'] = $_POST['cPemId'];
                $vDatos['linidxxx'] = $_POST['cLinId'];
                $vDatos['cliidxxx'] = $_POST['cCliId'];
                $vDatos['daaidxxx'] = $_POST['cDaaId'];
                $vDatos['doiatrax'] = $_POST['cDoiAtrA'];
                $vDatos['doidatra'] = $_POST['cDoiDatRa'];
                $vDatos['dgemcxxx'] = $_POST['cDgeMc'];
                $vDatos['dgefmcxx'] = $_POST['dDgeFmc'];

                $mReturnTrazabilidadDatosControlAnticipadas = $oProcesosSiaco->fnTrazabilidadDatosControlAnticipadas($vDatos);
                if($mReturnTrazabilidadDatosControlAnticipadas[0] == "false"){
                  $cMsjAnt = "";
                  for($nE=1; $nE<count($mReturnTrazabilidadDatosControlAnticipadas); $nE++) {
                    $cMsjAnt = $mReturnTrazabilidadDatosControlAnticipadas[$nE]."\n";
                  }
                  f_Mensaje(__FILE__, __LINE__,$cMsjAnt."Verifique.");
                }
              }
            break;
          }

          $aCampos  = array('DOITITRA'=>'"'.$_POST['cDoiTitra'].'"',
                            'CLIIDXXX'=>'"'.$_POST['cCliId'].'"',
                            'CLIUAPXX'=>'"'.$_POST['cCliUap'].'"',
                            'CLIID2XX'=>'"'.$_POST['cCliId2'].'"',
                            'USRIDXXX'=>'"'.$_POST['cUsrId'].'"',
                            'USRID2XX'=>'"'.$_POST['cUsrId2'].'"',
                            'USRID3XX'=>'"'.$_POST['cUsrId3'].'"',
                            'USRID4XX'=>'"'.$_POST['cUsrId4'].'"',
                            'USRID5XX'=>'"'.$_POST['cUsrId5'].'"',
                            'USRID6XX'=>'"'.$_POST['cUsrId6'].'"',
                            'DOIDTGXX'=>1,
                            'DAUIDXXX'=>'"'.$_POST['cDauId'].'"',
                            'DEPIDXXX'=>'"'.$_POST['cDepId'].'"',
                            'CIUIDXXX'=>'"'.$_POST['cCiuId'].'"',
                            'TDEIDXXX'=>'"'.$_POST['cTdeId'].'"',
                            'TDTIDXXX'=>'"'.$_POST['cTdtId'].'"',
                            'LINIDXXX'=>'"'.$_POST['cLinId'].'"',
                            'ODIIDXXX'=>'"'.$_POST['cOdiId'].'"',
                            'TRAIDXXX'=>'"'.$_POST['cTraId'].'"',
                            'TRAODIXX'=>'"'.$_POST['cTraOdi'].'"',
                            'MONIDXXX'=>'"'.$_POST['cMonIdGa'].'"',
                            'MONIDSGX'=>'"'.$_POST['cMonIdSe'].'"',
                            'PAIIDXXX'=>'"'.$_POST['cPaiId'].'"',
                            'DAAIDXXX'=>'"'.$_POST['cDaaId'].'"',
                            'AGCIDXXX'=>'"'.$_POST['cAgcId'].'"',
                            'DOINECAP'=>'"'.($_POST["cDoiNecAp"] == "on" ? "SI" : "NO").'"',
                            'DOITOPMX'=>'"'.$_POST["cDoiTopM"].'"',
                            'CSEIDXXX'=>'"'.$_POST['cCseId'].'"',
                            'MTRIDXXX'=>'"'.$_POST['cMtrId'].'"',
                            'AUXIDXXX'=>'"'.$_POST['cAuxId'].'"',
                            'PAIBANID'=>'"'.$_POST['cPaiBanId'].'"',
                            'DEPID2XX'=>'"'.$_POST['cDepId2'].'"',
                            'DOIPEDXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiPed']))).'"',
                            'DOIFEPED'=>'"'.$_POST['dDoiFePed'].'"',
                            'CLINOMAD'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cCliNomAd']))).'"',
                            'DOICLICO'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiCliCo']))).'"',
                            'DOICLICI'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiCliCi']))).'"',
                            //'DOIOBSXX'=>'"'.str_replace(array('"',"'"),array('\"',"\'"),trim(strtoupper($cObsf))).'"',
                            'DOIOBS2X'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($cObsf2))).'"',
                            'DOIEUNRO'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuNro']))).'"',
                            'DOIEUFEC'=>'"'.$_POST['dDoiEuFec'].'"',
                            'DOIEUENC'=>'"'.str_replace($cBuscar,$cReempl, trim(strtoupper($_POST['cDoiEuEnc']))).'"',
                            'DOIEUTIP'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuNro']))).'"',
                            'DOIEUPOL'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuPol']))).'"',
                            'DOIEUFPO'=>'"'.$_POST['dDoiEuFPo'].'"',
                            'DOIEUTIP'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuTip']))).'"',
                            'DOIEUDIL'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuDil']))).'"',
                            'DOIEUFDI'=>'"'.$_POST['dDoiEuFDi'].'"',
                            'DOIEUACT'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuAct']))).'"',
                            'DOIEUFAC'=>'"'.$_POST['dDoiEuFAc'].'"',
                            'DOIEULEV'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuLev']))).'"',
                            'DOIEUFLE'=>'"'.$_POST['dDoiEuFLe'].'"',
                            'DOIEUCIU'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuCiu']))).'"',
                            'DOIEUPEN'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuPen']))).'"',
                            'DOIEUCAR'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiEuCar']))).'"',
                            'REGMODXX'=>'"'.f_Fecha().'"',
                            'REGHORXX'=>'"'.f_Hora().'"',
                            'REGESTXX'=>'"'.$_POST['cRegEst'].'"',
                            'TCATASAX'=>0 + $_POST['nTcaT1'],
                            'TCAFECXX'=>'"'.$_POST['dTcaFec1'].'"',
                            'DGESIMXX'=>$nSim,
                            'DGESIDUN'=>$nSid,
                            'DGETRDIF'=>$nTrd,
                            'DOIRBPSE'=>$nPse,
                            'DOIAPDTA'=>$nDta,
                            'DOIAPEQU'=>$nEqu,
                            'DGEMCXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDgeMc']))).'"',
                            'DGEFMCXX'=>'"'.$_POST['dDgeFmc'].'"',
                            'DGEHMCXX'=>'"'.$_POST['hDgeHmc'].'"',
                            'DGEDTXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim($_POST['cDgeDt'])).'"',
                            'DGEFDTXX'=>'"'.$_POST['dDgeFdt'].'"',
                            'DGEAFLXX'=>$nAfl,
                            'DGEAGVXX'=>$nAgv,
                            'DGELIQVL'=>$nLva,
                            'DGELIQPE'=>$nLiqPE,
                            'DGELIQPV'=>$nLiqPV,
                            'DGENLFOB'=>$nNlfob,
                            'DGEACIXX'=>$nDgeAci,
                            'DOIREEXP'=>'"'.($_POST['cDoiReeXp'] == true ? "SI" : "NO").'"',
                            'CLIPTSXX'=>0 + $_POST['nCliPts'],
                            'DGEBULXX'=>0 + $_POST['nDgeBul'],
                            'DGEPBRXX'=>0 + $_POST['nDgePbr'],
                            'DGEPNTXX'=>0 + $_POST['nDgePnt'],
                            'DGEVOLXX'=>0 + $_POST['nDgeVol'],
                            'DGEFOBXX'=>0 + $_POST['nDgeFob'],
                            'DGEFLEXX'=>0 + $_POST['nDgeFle'],
                            'DGECONXX'=>0 + $_POST['nDgeCon'],
                            'DGEVARXX'=>0 + $_POST['nDgeVar'],
                            'DGESGEXX'=>0 + $_POST['nDgeSge'],
                            'GRMCPIXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cGrmCpi']))).'"',
                            'TIITENOC'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteNoc']))).'"',
                            'TIITEMAC'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteMac']))).'"',
                            'TIITETIP'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteTip']))).'"',
                            'TIITECLA'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteCla']))).'"',
                            'TIITEMOD'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteMod']))).'"',
                            'TIITEREF'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteRef']))).'"',
                            'TIITEANO'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteAno']))).'"',
                            'TIITEOTC'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTiIteOtc']))).'"',
                            'DOICCOXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cCosDHLId']))).'"',
                            'DOIDIVXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDivDHLId']))).'"',
                            'PRYIDXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cPryId']))).'"',
                            'TERIDXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cTerId']))).'"',
                            'DGCPERTE'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDgcPerte']))).'"',
                            'GMOIDXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cGmoId']))).'"',
                            'DGCUSRXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDgcUsr']))).'"',
                            'DGCESTXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDgcEst']))).'"',
                            'DGCFECXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['dDgcFec']))).'"',
                            'DGCHORXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDgcHor']))).'"',
                            'DOCVENXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cVenId']))).'"',
                            'SUCCOMXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cSucCom']))).'"',
                            'PRYID2XX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($vcpryid2))).'"',
                            'LPRIDXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($vclprid))).'"',
                            'LPRID2XX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($vclprid2))).'"',
                            'LPRID3XX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cLprId3']))).'"',
                            'DOIAVPRX'=>'"'.($_POST['cDoiAvpR'] == "on" ? "SI" : "NO").'"',
                            'DOIMOIMA'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cMimId']))).'"',
                            'LNECODXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cLneCod']))).'"',
                            'DOIAIMPA'=>'"'.($_POST['cDoiAimPa'] == "on" ? "SI" : "NO").'"',
                            'DOIAPPRY'=>0 + $vfchpry,
                            'DOIAPPRO'=>0 + $vfchpro,
                            'DIVIDXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDivId']))).'"',
                            'DOIAPCRI'=>'"'.$cDoiApcri.'"',
                            'DOIFPCRI'=>'"'.$cDoiFpcri.'"',
                            'DOIHPCRI'=>'"'.$cDoiHpcri.'"',
                            'DOIATRAX'=>'"'.trim(strtoupper($_POST['cDoiAtrA'])).'"',
                            'DOIDATRA'=>'"'.trim(strtoupper($_POST['cDoiDatRa'])).'"',
                            'DOINODXX'=>'"'.trim(strtoupper($_POST['cDoiNumOpeDoc'])).'"',
                            'DOISAPXX'=>'"'.trim(strtoupper($_POST['cCodSap'])).'"', //Codigo Sap Malco 2014-05-22
                            'DOIEAVTD'=>'"'.$cAler.'"',
                            'DOIDPROX'=>'"'.$_POST['cDoiDpro'].'"',
                            'DOICPROX'=>'"'.$_POST['cDoiCpro'].'"',
                            'DOIPDESX'=>'"CO"',
                            'DOIDDESX'=>'"'.$_POST['cDoiDdes'].'"',
                            'DOILEGXX'=>'"'.$_POST['cLegal'].'"',
                            'DOICDESX'=>'"'.$_POST['cDoiCdesc'].'"',
                            'CCOIDXXX'=>'"'.$_POST['cCcoId'].'"',
                            'SCCIDXXX'=>'"'.$_POST['cSccId'].'"',
                            'CCOIDALX'=>'"'.$_POST['cCcoAlId'].'"',
                            'DOIFINGX'=>'"'.$_POST['cDoiFin'].'"',
                            'DOIGADES'=>'"'.$_POST['nDoiGaDes'].'"',
                            'DOITRANA'=>'"'.$_POST['nDoiTraNa'].'"',
                            'DOIBMSXX'=>'"'.$_POST['cDoiBms'].'"',
                            'DOIPEDAN'=>'"'.$_POST['cDoiPedAn'].'"',
                            'DOIATRIY'=>'"'.($_POST['cDoiAtriY'] == true ? "SI" : "NO").'"',
                            'DOIIEMBY'=>'"'.$_POST['cDoiIembY'].'"',
                            'TCAIDXXX'=>'"'.$_POST['cTcaId'].'"',
                            'DOIEMSTX'=>'"'.$_POST['cDoiEmst'].'"',
                            'DOIATMSY'=>'"'.($_POST['cDoiAtmsY'] == true ? "SI" : "NO").'"',
                            'DOIDTACE'=>'"'.($_POST['cDoiDtaCe'] == "on" ? "SI" : "NO").'"',                        
                            'DOIDIGITA'=>'"'.(($_POST['cDoiDigita'] == "on") ? "SI" : "NO").'"',
                            'DOILICHQ'=>'"'.(($_POST['cDoiLiChq'] == "on") ? "SI" : "NO").'"',
                            'DOIEEI20'=>'"'.(($_POST['cDoiEei20'] == "on") ? "SI" : "").'"',
                            'CCOIDCEX'=>'"'.str_replace(array('"', "'", "~"), array('\"', "\'", ","), trim(strtoupper($_POST['cCcoIds']))).'"',
                            'DOICSAPX'=>'"'.$_POST['cDoiCsaP'].'"',
                            'DOIACAFE'=>'"'.$_POST['cDoiAcaFe'].'"',
                            'DOILOTEX'=>'"'.str_replace($cBuscar,$cReempl,$_POST['cDoiLotE']).'"',
                            'DOINSEMX'=>'"'.$_POST['cInsEmb'].'"',
                            'DOIDMERX'=>'"'.$_POST['cDoiDmeR'].'"',
                            'DOINUMVA'=>'"'.$_POST['cDoiNumVa'].'"',
                            'DOIOBSAL'=>'"'.$_POST['cDoiObsAl'].'"',
                            'DEPIDORI'=>'"'.$_POST['cDepIdO'].'"',
                            'DOIPAPOE'=>'"'.$_POST['cPapOe'].'"',
                            'CIUIDORI'=>'"'.$_POST['cCiuIdOri'].'"',
                            'PAIIDDES'=>'"'.$_POST['cPaiIdD'].'"',
                            'DEPIDDES'=>'"'.$_POST['cDepIdD'].'"',
                            'EENNUMXX'=>'"'.$_POST['cEenNum'].'"',
                            'CIUIDDES'=>'"'.$_POST['cCiuIdDes'].'"',
                            'DOIDIPPO'=>'"'.($_POST['cDoiDipPo'] == "on" ? "SI" : "NO").'"',
                            'DOIHANDL'=>0 + $_POST['nDoiHanD'],
                            'DOIIVAHA'=>0 + $_POST['nDoiIvaH'],
                            'ORDIDINX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cOrdIdIn']))).'"',
                            'ORDSERIN'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cOrdSerIn']))).'"',
                            'DOIHENIN'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiHenIn']))).'"',
                            'BANIDINX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cBanIdIn']))).'"',
                            'BANCTAIN'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cBanCtaIn']))).'"',
                            'DOICORIX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiCorI']))).'"',
                            'DOICIDES'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiCidEs']))).'"',
                            'PEMIDXXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cPemId']))).'"',
                            'DOIPARXX'=>'"'.($_POST['cDoiPar']== "on" ? "SI" : "NO").'"',
                            'DOIFULLD'=>'"'.($_POST['cDoiFullD']== "on" ? "SI" : "NO").'"',
                            'DOIDDPGE'=>'"'.($_POST['cDoiDdpGe']== "on" ? "SI" : "NO").'"',
                            'DOIPADHL'=>'"'.($_POST['cDoiPaDhl']== "on" ? "SI" : "NO").'"',
                            'DOINFEU4'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim(strtoupper($_POST['cDoiNFeU4']))).'"',
                            'DOICOMIX'=>0 + $_POST['nDoiComI'],
                            'DOIDTMXX'=>'"'.$_POST['cDoiDtm'].'"',
                            'DOIREFCW'=>'"'.$_POST['cDoiRefCw'].'"',
                            'INTIDXXX'=>'"'.$_POST['cIntId'].'"');

          $aLlave = array('DOIIDXXX'=>'"'.trim(strtoupper($_POST['cDoiId'])).'"',
                          'DOISFIDX'=>'"'.trim(strtoupper($_POST['cDoiSfId'])).'"',
                          'ADMIDXXX'=>'"'.trim(strtoupper($_POST['cAdmId'])).'"');

          // nuevo logger FASE 1
          $arLog1 = array();
          $arLog2 = array();
          $cLlavelg = $_POST['cDoiId']."=>".$_POST['cDoiSfId']."=>".$_POST['cAdmId'];
          $cSqUplog = mysql_query("SELECT * FROM SIAI0200 WHERE DOIIDXXX = \"{$_POST['cDoiId']}\" AND DOISFIDX = \"{$_POST['cDoiSfId']}\"  AND  ADMIDXXX = \"{$_POST['cAdmId']}\"  LIMIT 0,1");
          $arLog1 = mysql_fetch_array($cSqUplog);
          // Fin nuevo logger FASE 1
          //DO Bloqueado
          $flblo = f_BloqDoi($_POST['cDoiId'], $_POST['cDoiSfId'], $_POST['cAdmId']);
          //Fin DO Bloqueado
          if ($flblo == 0) {
            $mysql->f_Actualizar("SIAI0200", $aCampos, $aLlave);

						if($cAlfa == "DHLEXPRE" || $cAlfa == "DEDHLEXPRE" || $cAlfa == "TEDHLEXPRE") {
							//Actualiza la condicion especial de Aplica DO Parcial
							$zInsertCab = array(array('NAME'=>'docdopar','VALUE'=>($_POST['cDoiPar']== "on" ? "SI" : "NO")  ,'CHECK'=>'NO'),
                                  array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')											 	      ,'CHECK'=>'SI'),
                                  array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                          ,'CHECK'=>'SI'),
                                  array('NAME'=>'docidxxx','VALUE'=>trim(strtoupper($_POST['cDoiId']))        ,'CHECK'=>'WH'),
                                  array('NAME'=>'sucidxxx','VALUE'=>trim(strtoupper($_POST['cAdmId']))        ,'CHECK'=>'WH'),
                                  array('NAME'=>'docsufxx','VALUE'=>trim(strtoupper($_POST['cDoiSfId']))      ,'CHECK'=>'WH'));

							f_MySql("UPDATE","sys00121",$zInsertCab,$xConexion01,$cAlfa);
						}
          }

          //Consulto si debo llevar Pedido a Declaraciones
          if($vSysStr['importaciones_actualizar_pedido_datos_generales_en_declaraciones'] == "SI"){

            //Consulto si ingresaron pedido
            if($_POST["cDoiPed"] != ""){

              $qEstDO  = "SELECT ";
              $qEstDO .= "DOIESTXX ";//Estado operativo
              $qEstDO .= "FROM $cAlfa.SIAI0200 ";
              $qEstDO .= "WHERE ";
              $qEstDO .= "DOIIDXXX = \"{$_POST['cDoiId']}\"   AND ";
              $qEstDO .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
              $qEstDO .= "ADMIDXXX = \"{$_POST['cAdmId']}\" ";
              $xEstDO  = f_MySql("SELECT","",$qEstDO,$xConexion01,"");
              $vRED    = mysql_fetch_array($xEstDO);
              //f_Mensaje(__FILE__,__LINE__,$qEstDO."-".mysql_num_rows($xEstDO));

              //Consulto que tenga declaraciones por su estado operativo
              if($vRED["DOIESTXX"] != ""){

                //Actualizando declaraciones con el Pedido del DO
                $qUpPed	= array(array('NAME'=>'DOIPEDXX','VALUE'=>$_POST["cDoiPed"]					,'CHECK'=>'NO'),
                                array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId'] 					,'CHECK'=>'WH'),
                                array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId'] 				,'CHECK'=>'WH'),
                                array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId'] 					,'CHECK'=>'WH'));

                f_MySql("UPDATE","SIAI0206",$qUpPed,$xConexion01,$cAlfa);

              }
            }
          }

          /// nuevo logger FASE 2
          $cSqUplog = mysql_query("SELECT * FROM SIAI0200 WHERE DOIIDXXX = \"{$_POST['cDoiId']}\" AND DOISFIDX = \"{$_POST['cDoiSfId']}\"  AND  ADMIDXXX = \"{$_POST['cAdmId']}\"  LIMIT 0,1");
          $arLog2 = mysql_fetch_array($cSqUplog);
          f_DatosCambio($arLog1, $arLog2, "SIAI0200", "|DOIIDXXX=>DOISFIDX=>ADMIDXXX|", $cLlavelg, "", "UPDATE", $kUser, f_Fecha(), f_Hora2(), ipCheck(), $_SERVER['PHP_SELF']);
          // Fin nuevo logger FASE 2
          /// Nueva observacion
          if ($isnvaob == 1) {
            $cTiOb = "IMPORTACION";
            $cFecOb = f_Fecha();
            $cHorOb = f_Hora2();
            $zInsert =  array('docidxxx'=>'"'.$_POST['cDoiId'].'"',
                              'docsufxx'=>'"'.$_POST['cDoiSfId'].'"',
                              'sucidxxx'=>'"'.$_POST['cAdmId'].'"',
                              'doctipxx'=>'"'.$cTiOb.'"',
                              'obsobsxx'=>'"'.$_POST['cObs1'].'"',
                              'obsobs2x'=>'"'."NO".'"',
                              //'obstipxx'=>'"'."AGENCIA".'"',
                              'regusrxx'=>'"'.$kUser.'"',
                              'regfcrex'=>'"'.$cFecOb.'"',
                              'reghcrex'=>'"'.$cHorOb.'"',
                              'regfmodx'=>'"'.$cFecOb.'"',
                              'reghmodx'=>'"'.$cHorOb.'"',
                              'regestxx'=>'"'."ACTIVO".'"');
            $mysql->f_Insertar("sys00017", $zInsert);
          }

          if ($isnvaob2 == 1) {
            $cTiOb = "IMPORTACION";
            $cFecOb = f_Fecha();
            $cHorOb = f_Hora2();
            $zInsert =  array('docidxxx'=>'"'.$_POST['cDoiId'].'"',
                              'docsufxx'=>'"'.$_POST['cDoiSfId'].'"',
                              'sucidxxx'=>'"'.$_POST['cAdmId'].'"',
                              'doctipxx'=>'"'.$cTiOb.'"',
                              'obsobsxx'=>'"'.$cObsf2.'"',
                              'obsobs2x'=>'"'."SI".'"',
                              //'obstipxx'=>'"'."AGENCIA".'"',
                              'regusrxx'=>'"'.$kUser.'"',
                              'regfcrex'=>'"'.$cFecOb.'"',
                              'reghcrex'=>'"'.$cHorOb.'"',
                              'regfmodx'=>'"'.$cFecOb.'"',
                              'reghmodx'=>'"'.$cHorOb.'"',
                              'regestxx'=>'"'."ACTIVO".'"');
            $mysql->f_Insertar("sys00017", $zInsert);
          }
          //fin Nueva observacion

          if ($isDta == 1) {
            $zInsert =  array('DOIIDXXX'=>'"'.$_POST['cDoiId'].'"',
                              'ADMIDXXX'=>'"'.$_POST['cAdmId'].'"',
                              'REGFECXX'=>'"'.f_Fecha().'"',
                              'REGMODXX'=>'"'.f_Fecha().'"',
                              'REGHORXX'=>'"'.f_Hora().'"',
                              'REGESTXX'=>'"'."ACTIVO".'"');
            $mysql->f_Insertar("dta00200", $zInsert);
          }

          if ($isEqu == 1) {
            $zInsert =  array('DOIIDXXX'=>'"'.$_POST['cDoiId'].'"',
                              'ADMIDXXX'=>'"'.$_POST['cAdmId'].'"',
                              'DOISFIDX'=>'"'.$_POST['cDoiSfId'].'"',
                              'CLIIDXXX'=>'"'.$_POST['cCliId'].'"',
                              'EQUDTRXX'=>'"'.str_replace(array('"', "'"), array('\"', "\'"), trim($_POST['cDgeDt'])).'"',
                              'REGFECXX'=>'"'.f_Fecha().'"',
                              'REGMODXX'=>'"'.f_Fecha().'"',
                              'REGHORXX'=>'"'.f_Hora().'"',
                              'REGESTXX'=>'"'."ACTIVO".'"');
            $mysql->f_Insertar("SIAI0142", $zInsert);
          }

          /**
           * Consumo Web Service para actualizar Tercero en Almaviva
           */
          if ( in_array($cAlfa, $vBDActivarWsAlmaviva) == true ) {
            if($_POST['cCliId'] != $_POST['cCliIdAct']){
              $oWSOutput = new cWSOutputAlmaviva();
              $vDatosFacturacion['DOIIDXXX'] = $_POST['cDoiId'];
              $vDatosFacturacion['ADMIDXXX'] = $_POST['cAdmId'];
              $vDatosFacturacion['DOISFIDX'] = $_POST['cDoiSfId'];
              $vDatosFacturacion['DOITIPXX'] = "IMPORTACION";
              $mReturnWs = $oWSOutput->fnActualizarRAreaTercero($vDatosFacturacion);
              if($mReturnWs[0] == "false"){
                $cMsjAlm .= "Se Presentaron Errores al Actualizar Tercero: \n";
                for($nI = 1; $nI < count($mReturnWs) ; $nI++){
                  $cMsjAlm .= $mReturnWs[$nI]."\n";
                }
                f_Mensaje(__FILE__,__LINE__,$cMsjAlm);
              }
            }
          }

          /////INTERNAC
          if ($kMysqlDb == "INTERNAC") {
            $sqant = "SELECT CLIIDXXX ";
            $sqant .= "FROM SIAI0200 ";
            $sqant .= "WHERE ";
            $sqant .= "DOIIDXXX = \"{$_POST['cDoiId']}\" AND ";
            $sqant .= "DOISFIDX =\"{$_POST['cDoiSfId']}\" AND ";
            $sqant .= "ADMIDXXX =\"{$_POST['cAdmId']}\" LIMIT 0,1 ";
            $rant = myqry($sqant);
            $fant = mynro($rant);
            if ($fant > 0) {
              $roa = myfar($rant, 0);
              $cliant = $roa['CLIIDXXX'];
              if ($cliant != $cCliId && strlen($cliant) > 0 && strlen($cCliId) > 0) {
                internacp1($cDoiId, $cDoiSfId, $cAdmId, $cCliId, $cDoiPed);
              }
            }
          } else {
            //Do Financiero
            if ($vSysStr['system_aplica_financiero'] == "SI") {
              $cMedio = "MARITIMO";
              if (strlen($_POST['cMtrId']) > 0) {
                switch ($_POST['cMtrId']) {
                  case '3': $cMedio = "TERRESTRE"; break;
                  case '4': $cMedio = "AEREO";     break;
                }
              }

              $cAdmId2 = $cAdmId;

              $cDocTep = "GENERAL";
              $cDocTepId = "100";
              if ($vfchpry == 1) {
                $cDocTep = "PROYECTO";
                if (substr_count($vSysStr['alpopular_db_aplica'], $kMysqlDb) > 0) {
                  $cDocTepId = $vcpryid;
                } else {
                  $cDocTepId = $vcpryid2;
                }
              }

              if ($vfchpro == 1) {
                $cDocTep = "PRODUCTO";
                if (substr_count($vSysStr['alpopular_db_aplica'], $kMysqlDb) > 0) {
                  $cDocTepId = $vclprid;
                } else {
                  $cDocTepId = $vclprid2;
                }
              }

              switch($kMysqlDb) {
                case "DESIACOSIP":
                case "TESIACOSIP":
                case "SIACOSIA":
                  $qDoiFin = "SELECT ccoidxxx,sccidxxx ";
                  $qDoiFin .= "FROM $cAlfa.sys00121 ";
                  $qDoiFin .= "WHERE ";
                  $qDoiFin .= "$cAlfa.sys00121.docidxxx = \"{$_POST['cDoiId']}\" LIMIT 0,1";
                  $xDoiFin = f_MySql("SELECT", "", $qDoiFin, $xConexion01, "");
                  $vSDoiFin= mysql_fetch_array($xDoiFin);
                  $cCcoId  = $vSDoiFin["ccoidxxx"];
                  $cSccId  = $vSDoiFin["sccidxxx"];
                  $cComId  = "P";
                  $cComCod = "028";
                break;
                default:

                  // Consultar si existe el centro de costo 0201 para IMPORTACION
                  $qCcoImp  = "SELECT ccoidxxx ";
                  $qCcoImp .= "FROM $cAlfa.fpar0008 ";
                  $qCcoImp .= "WHERE ";
                  $qCcoImp .= "sucidxxx = \"$cAdmId2\" AND ";
                  $qCcoImp .= "sucopexx = \"IMPORTACION\" AND ";
                  $qCcoImp .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
                  $xCcoImp  = f_MySql("SELECT", "", $qCcoImp, $xConexion01, "");
                  // f_Mensaje(__FILE__,__LINE__,$qCcoImp."~".mysql_num_rows($xCcoImp));
                  if(mysql_num_rows($xCcoImp) == 1){
                    $vCcoImp = mysql_fetch_array($xCcoImp);
                    $cCcoId  = $vCcoImp['ccoidxxx'];
                    $cComId  = "P";
                    $cComCod = "001";
                    $cSccId  = "";
                  }else{
                    //Consulto el Cecntro de Costos normalmente
                    $qCcoId  = "SELECT ccoidxxx ";
                    $qCcoId .= "FROM $cAlfa.fpar0008 ";
                    $qCcoId .= "WHERE ";
                    $qCcoId .= "sucidxxx = \"$cAdmId2\" AND ";
                    $qCcoId .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
                    $xCcoId  = f_MySql("SELECT", "", $qCcoId, $xConexion01, "");
                    // f_Mensaje(__FILE__,__LINE__,$qCcoId."~".mysql_num_rows($xCcoId));
                    $vCcoId  = mysql_fetch_array($xCcoId);
                    $cCcoId  = $vCcoId['ccoidxxx'];
                    $cComId  = "P";
                    $cComCod = "001";
                    $cSccId  = "";
                  }

                break;
              }
              $cPucId = "2805050000";
              //$cDocTep   = "GENERAL";
              //$cDocTepId = "100";
              $cDocFma = "NO";
              $cDocFrs = "PRINCIPAL";
              $cSucCom = $_POST['cSucCom'];
              $cVenId = $_POST['cVenId'];

              f_DO_Financiero($_POST['cDoiId'], $_POST['cDoiSfId'], $cAdmId, $cTipoDo, $cCliId, $cDoiPed, $_POST['cUsrId2'], $_POST['cUsrId'], $cMedio, $cCcoId, $cComId, $cComCod, $cPucId, $cDocTep, $cDocTepId, $cDocFma, $cDocFrs, $cVenId, $cSucCom,'','',$cSccId);
            }

            // Fin DO Financiero
            if (substr_count($vSysStr['alpopular_db_aplica'], $kMysqlDb) > 0 && $vSysStr['alpopular_activar_seven_creacion_do'] == "SI") {
              if (strlen($_POST['cDoiId']) == 13) {
                $fFecha = f_Fecha();
                $fHora = f_Hora();
                //$cCog = mycong2();
                //$cDbg = mydbg();
                $cDoiAg = $_POST['cDoiId']."~".$_POST['cDoiSfId']."~".$_POST['cAdmId']."~".$fFecha."~".$fHora;
                $cDocId = $_POST['cDoiId']."~".$_POST['cDoiSfId']."~".$_POST['cAdmId']."~";
                $cWsId = "100";
                $cWsDes = "CREACION DO / IMP ALPOPULAR";
                $cWsTip = "OUT";
                $cWsPhp = "utiwsout.php";
                $cWsMet = "f_Crear_RArea_Tercero";
                $cWsPar = $cDoiAg;
                $cWsDo = $cDoiAg;
                $cWsUsr = $kUser;
                $cWsFec = $fFecha;
                $cWsHor = $fHora;
                //$cWsFecm = $fFecha;
                //$cWsHorm = $fHora;
                $cWsEst = "ACTIVO";


                $nWsVer = 0;
                $cWsVer = mysql_query("SELECT ageidxxx,ageparxx FROM sysagews WHERE ageparxx LIKE \"%$cDocId%\" AND ageidxxx = \"$cWsId\" AND regestxx = \"ACTIVO\" LIMIT 0,1");
                while ($xRWsVer = mysql_fetch_array($cWsVer)) {
                  $vTramite = explode("~", $xRWsVer['ageparxx']);
                  if ($vTramite[0] == $_POST['cDoiId'] && $vTramite[1] == $_POST['cDoiSfId'] && $vTramite[2] == $_POST['cAdmId']) {
                    $nWsVer = 1;
                  }
                }
                //$nWsVer = mysql_num_rows($cWsVer);

                if ($nWsVer <= 0) {
                  $zInsert = "INSERT INTO sysagews (ageidxxx,agesucxx,agedesxx,agetipxx,agephpxx,agemetxx,ageparxx,agedoxxx,regusrxx,regfecxx,reghorxx,regfmodx,reghmodx,regestxx)";
                  $zInsert .= " VALUES (";
                  $zInsert .= "\"$cWsId\",";
                  $zInsert .= "\"{$_POST['cAdmId']}\",";
                  $zInsert .= "\"$cWsDes\",";
                  $zInsert .= "\"$cWsTip\",";
                  $zInsert .= "\"$cWsPhp\",";
                  $zInsert .= "\"$cWsMet\",";
                  $zInsert .= "\"$cDoiAg\",";
                  $zInsert .= "\"$cDoiAg\",";
                  $zInsert .= "\"$kUser\",";
                  $zInsert .= "\"$cWsFec\",";
                  $zInsert .= "\"$cWsHor\",";
                  $zInsert .= "\"$cWsFec\",";
                  $zInsert .= "\"$cWsHor\",";
                  $zInsert .= "\"$cWsEst\"";
                  $zInsert .= ")";
                  $cAgeWs = mysql_query($zInsert);
                }
              }
            }
          }
          /////

          switch($cAlfa) {
            case "DEDHLXXXXX":
            case "TEDHLXXXXX":
            case "DHLXXXXX":
              $qDatDo  = "SELECT ";
              $qDatDo .= "DOIEICDZ,";
              $qDatDo .= "DOIE1789,";
              $qDatDo .= "DOIEP789,";
              $qDatDo .= "DOIEEI20,";
              $qDatDo .= "DOIEDWSY ";
              $qDatDo .= "FROM $cAlfa.SIAI0200 ";
              $qDatDo .= "WHERE ";
              $qDatDo .= "DOIIDXXX = \"{$_POST['cDoiId']}\" AND ";
              $qDatDo .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
              $qDatDo .= "ADMIDXXX = \"{$_POST['cAdmId']}\" ";
              $xDatDo  = f_MySql("SELECT", "", $qDatDo, $xConexion01, "");
              $vDatDo  = mysql_fetch_array($xDatDo);

              if($_POST['cDoiRefCw'] != "" && $vDatDo['DOIEDWSY'] == "0000-00-00 00:00:00"){
                if (!in_array($OPENINIT['pathdr']."/opencomex/ws/opensycx/utiwsdel.php", get_included_files(), true)) {
                  include($OPENINIT['pathdr']."/opencomex/ws/opensycx/utiwsdel.php");
                }

                $oWSOutputSycEdm = new cWSOutputSycEdm();
                $vParametros = array();
                $vParametros["ADMIDXXX"] = $_POST['cAdmId'];    // Sucursal del Do
                $vParametros["DOIIDXXX"] = $_POST['cDoiId'];    // Numero de Do
                $vParametros["DOISFIDX"] = $_POST['cDoiSfId'];  // Sufijo del Do
                $mReturnWsDatosRequeridosEDM = $oWSOutputSycEdm->fnEnviarDatosRequeridosEDM($vParametros);
                if($mReturnWsDatosRequeridosEDM[0] == "false"){
                  $cMsjWs  = "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsjWs .= "Error al consumir WS Imagenes Levante EDM.\n";
                  for($nE=1;$nE<count($mReturnWsDatosRequeridosEDM);$nE++){
                    $cMsjWs .= $mReturnWsDatosRequeridosEDM[$nE].".\n";
                  }
                  f_Mensaje(__FILE__, __LINE__, $cMsjWs . "Verifique.");
                }else{
                  $qUpdate  = array(array('NAME'=>'DOIEDWSY','VALUE'=>date("Y-m-d H:i:s") ,'CHECK'=>'SI'),
                                    array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']    ,'CHECK'=>'WH'),
                                    array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']  ,'CHECK'=>'WH'),
                                    array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']    ,'CHECK'=>'WH'));
                  if (!f_MySql("UPDATE","SIAI0200",$qUpdate,$xConexion01,$cAlfa)) {
                    f_Mensaje(__FILE__,__LINE__,"Error al Actualizar Envio Datos x WS SYC");
                  }
                }
              }

              // Enviar Interfaz CDZ020
              $vAgentesCarga = explode(",",$vSysStr['dhlxxxxx_agente_carga_handover_cargowise']);
              if(!in_array($_POST['cAgcId'], $vAgentesCarga) && $_POST['cDgeDt'] != ""){

                // Busco si hay do's parciales con el mismo documento de transporte
                $qDoPar  = "SELECT DOIIDXXX ";
                $qDoPar .= "FROM $cAlfa.SIAI0200 ";
                $qDoPar .= "WHERE ";
                $qDoPar .= "DGEDTXXX = \"{$_POST['cDgeDt']}\" AND ";
                $qDoPar .= "DOIEICDZ != \"0000-00-00 00:00:00\" LIMIT 0,1";
                $xDoPar  = f_MySql("SELECT", "", $qDoPar, $xConexion01, "");
                $nAplicaEnvio = 1;
                if(mysql_num_rows($xDoPar) == 0){
                  $nAplicaEnvio = 0;
                }
                // Busco si hay do's parciales con el mismo documento de transporte

                if($vDatDo['DOIEICDZ'] == "0000-00-00 00:00:00" && $vDatDo['DOIEEI20'] != "SI" && $nAplicaEnvio == 0){
                  $vDatos = array();
                  $vDatos["ADMIDXXX"] = $_POST['cAdmId']; // Sucursal del Do
                  $vDatos["DOIIDXXX"] = $_POST['cDoiId']; // Numero de Do
                  $vDatos["DOISFIDX"] = $_POST['cDoiSfId']; // Sufijo del Do
                  $vDatos["AGCIDXXX"] = $_POST['cAgcId']; //Agente de Carga
                  
                  $mReturnEnvioInterfazCDZ020 = $oProcesosCargoWise->fnEnvioInterfazCDZ020($vDatos);
                  if($mReturnEnvioInterfazCDZ020[0] == "false"){
                    $cMsjEnvioInterfazCDZ020 = "Se Presentaron los Siguientes errores en el Envio de la Interfaz CDZ020:\n";
                    for($nR=1;$nR<count($mReturnEnvioInterfazCDZ020);$nR++){
                      $cMsjEnvioInterfazCDZ020 .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                      $cMsjEnvioInterfazCDZ020 .= $mReturnEnvioInterfazCDZ020[$nR]."\n";
                    }
                    f_Mensaje(__FILE__, __LINE__,$cMsjEnvioInterfazCDZ020."Verifique");
                  }else{
                    //Actualiza Envio Interfaz CDZ020 y Id Mensaje de Envio Interfaz CDZ020
                    $qUpdate   =  array(array('NAME'=>'DOIEICDZ','VALUE'=>date("Y-m-d H:i:s")               ,'CHECK'=>'SI'),
                                        array('NAME'=>'DOIIDMEN','VALUE'=>$mReturnEnvioInterfazCDZ020[1]    ,'CHECK'=>'SI'),
                                        array('NAME'=>'DOIFMACW','VALUE'=>date("Y-m-d")                     ,'CHECK'=>'SI'),
                                        array('NAME'=>'DOIUMACW','VALUE'=>$kUser                            ,'CHECK'=>'SI'),
                                        array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']                  ,'CHECK'=>'WH'),
                                        array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']                ,'CHECK'=>'WH'),
                                        array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']                  ,'CHECK'=>'WH'));
                    if (!f_MySql("UPDATE","SIAI0200",$qUpdate,$xConexion01,$cAlfa)) {
                      f_Mensaje(__FILE__,__LINE__,"Error al Actualizar Envio Interfaz CDZ020");
                    }
                  }
                }
              }
              // Fin Enviar Interfaz CDZ020
              
              if($nActualizarIntEst == 1){

                $qIntCdz123  = "SELECT intdopar ";
                $qIntCdz123 .= "FROM $cAlfa.zdhl0017 ";
                $qIntCdz123 .= "WHERE ";
                $qIntCdz123 .= "intidxxx = \"{$_POST['cIntId']}\" AND ";
                $qIntCdz123 .= "doiidxxx != \"\" AND ";
                $qIntCdz123 .= "doisfidx != \"\" LIMIT 0,1";
                $xIntCdz123  = f_MySql("SELECT", "", $qIntCdz123, $xConexion01, "");
                if(mysql_num_rows($xIntCdz123) > 0){
                  $vIntCdz123  = mysql_fetch_array($xIntCdz123);
                  $cDoParcial = $vIntCdz123['intdopar']."{$_POST['cAdmId']}~{$_POST['cDoiId']}~{$_POST['cDoiSfId']}|";

                  //Actualiza zdhl0017
                  $qUpdate   =  array(array('NAME'=>'intdopar','VALUE'=>$cDoParcial                       ,'CHECK'=>'SI'),
                                      array('NAME'=>'intidxxx','VALUE'=>$_POST['cIntId']                  ,'CHECK'=>'WH'));
                  if (!f_MySql("UPDATE","zdhl0017",$qUpdate,$xConexion01,$cAlfa)) {
                    f_Mensaje(__FILE__,__LINE__,"Error al Actualizar Do Parcial de la Interfaz");
                  }
                }else{
                  //Actualiza zdhl0017
                  $qUpdate   =  array(array('NAME'=>'doiidxxx','VALUE'=>$_POST['cDoiId']                  ,'CHECK'=>'SI'),
                                      array('NAME'=>'doisfidx','VALUE'=>$_POST['cDoiSfId']                ,'CHECK'=>'SI'),
                                      array('NAME'=>'intestxx','VALUE'=>"ASIGNADO"                        ,'CHECK'=>'SI'),
                                      array('NAME'=>'intidxxx','VALUE'=>$_POST['cIntId']                  ,'CHECK'=>'WH'));
                  if (!f_MySql("UPDATE","zdhl0017",$qUpdate,$xConexion01,$cAlfa)) {
                    f_Mensaje(__FILE__,__LINE__,"Error al Actualizar Estado de la Interfaz");
                  }
                }
              }

              // Enviar Interfaz CDZ789
              if($_POST['cDoiDtm'] != "" && $_POST['cIntId'] != "" && ($vDatDo['DOIE1789'] == "0000-00-00 00:00:00" || 
                ($_POST['cDoiPed'] != "" && $vDatDo['DOIEP789'] == "0000-00-00 00:00:00")) ){

                $vParametros = array();
                $vParametros["ADMIDXXX"] = $_POST['cAdmId']; // Sucursal del Do
                $vParametros["DOIIDXXX"] = $_POST['cDoiId']; // Numero de Do
                $vParametros["DOISFIDX"] = $_POST['cDoiSfId']; // Sufijo del Do
                
                $mReturnEnvioInterfazCDZ789 = $oProcesosCargoWise->fnGenerarInterfaceCDZ789($vParametros);
                if($mReturnEnvioInterfazCDZ789[0] == "false"){
                  $cMsjEnvioInterfazCDZ789 = "Se Presentaron los Siguientes errores en el Envio de la Interfaz CDZ789:\n";
                  for($nR=1;$nR<count($mReturnEnvioInterfazCDZ789);$nR++){
                    $cMsjEnvioInterfazCDZ789 .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsjEnvioInterfazCDZ789 .= $mReturnEnvioInterfazCDZ789[$nR]."\n";
                  }
                  f_Mensaje(__FILE__, __LINE__,$cMsjEnvioInterfazCDZ789."Verifique");
                }
              }
              // Fin Enviar Interfaz CDZ789
            break;
          }
          // Fin Envio Interfaz CDZ020

          if($_POST['cDoiDtm'] != "" && $_POST['cDoiDtm'] != $_POST['cDoiDtmO']){

            $qUpdate  = array(array('NAME'=>'DOIFMACW','VALUE'=>date("Y-m-d")					,'CHECK'=>'SI'),
                              array('NAME'=>'DOIUMACW','VALUE'=>$kUser					      ,'CHECK'=>'SI'),
                              array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId'] 		  ,'CHECK'=>'WH'),
                              array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId'] 	  ,'CHECK'=>'WH'),
                              array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId'] 		  ,'CHECK'=>'WH'));
            f_MySql("UPDATE","SIAI0200",$qUpdate,$xConexion01,$cAlfa);

          }

          //Actualiza Cliente en Item
          $qUpItem	 =  array(array('NAME'=>'CLIIDXXX','VALUE'=>$_POST['cCliId']					,'CHECK'=>'NO'),
                              array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId'] 					,'CHECK'=>'WH'),
                              array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId'] 				,'CHECK'=>'WH'),
                              array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId'] 					,'CHECK'=>'WH'));

          f_MySql("UPDATE","SIAI0205",$qUpItem,$xConexion01,$cAlfa);

          //Actualiza Cliente en Seriales
          $qUpItem	 =  array(array('NAME'=>'CLIIDXXX','VALUE'=>$_POST['cCliId']					,'CHECK'=>'NO'),
                              array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId'] 					,'CHECK'=>'WH'),
                              array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId'] 				,'CHECK'=>'WH'),
                              array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId'] 					,'CHECK'=>'WH'));

          f_MySql("UPDATE","SIAI0221",$qUpItem,$xConexion01,$cAlfa);

          //Actualiza Cliente en Lotes
          $qUpItem   =  array(array('NAME'=>'CLIIDXXX','VALUE'=>$_POST['cCliId']          ,'CHECK'=>'NO'),
                              array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']          ,'CHECK'=>'WH'),
                              array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']        ,'CHECK'=>'WH'),
                              array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']          ,'CHECK'=>'WH'));

          f_MySql("UPDATE","SIAI0222",$qUpItem,$xConexion01,$cAlfa);

          switch ($cAlfa) {
            case 'ROLDANLO':
            case 'TEROLDANLO':
            case 'DEROLDANLO':
              //Actualiza Do Financiero
              $qUpFinanciero   =  array(array('NAME'=>'sccidxxx','VALUE'=>$_POST['cSccId']          ,'CHECK'=>'NO'),
                                        array('NAME'=>'docidxxx','VALUE'=>$_POST['cDoiId']          ,'CHECK'=>'WH'),
                                        array('NAME'=>'docsufxx','VALUE'=>$_POST['cDoiSfId']        ,'CHECK'=>'WH'),
                                        array('NAME'=>'sucidxxx','VALUE'=>$_POST['cAdmId']          ,'CHECK'=>'WH'));

              f_MySql("UPDATE","sys00121",$qUpFinanciero,$xConexion01,$cAlfa);
              break;
          }

          if(!empty($_POST['cObsObs'])){
            $_POST['cObsObs'] = "CAMBIO NIT IMPORTADOR ".$_POST['cCliIdAct']." POR ".$_POST['cCliId']." - ".$_POST['cObsObs'];
            $dFecha = date('Y-m-d');
            $tHora  = date("H:i:s");
            $zInsertCab = array(array('NAME'=>'sucidxxx','VALUE'=>trim($_POST['cAdmId'])                ,'CHECK'=>'NO'),
                                array('NAME'=>'docidxxx','VALUE'=>trim($_POST['cDoiId'])                ,'CHECK'=>'SI'),
                                array('NAME'=>'docsufxx','VALUE'=>trim($_POST['cDoiSfId'])              ,'CHECK'=>'NO'),
                                array('NAME'=>'obgidxxx','VALUE'=>trim($_POST['cObgId'])                ,'CHECK'=>'NO'),
                                array('NAME'=>'obsidxxx','VALUE'=>trim($_POST['cObsId'])                ,'CHECK'=>'NO'),
                                array('NAME'=>'doctipxx','VALUE'=>"IMPORTACION"                         ,'CHECK'=>'NO'),
                                array('NAME'=>'obsobsxx','VALUE'=>trim($_POST['cObsObs'])               ,'CHECK'=>'SI'),
                                array('NAME'=>'obsobs2x','VALUE'=>"NO"                                  ,'CHECK'=>'NO'),
                                array('NAME'=>'obstipxx','VALUE'=>'AGENCIA'                             ,'CHECK'=>'SI'),
                                array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($kUser))              ,'CHECK'=>'SI'),
                                array('NAME'=>'regfcrex','VALUE'=>$dFecha                               ,'CHECK'=>'SI'),
                                array('NAME'=>'reghcrex','VALUE'=>$tHora                                ,'CHECK'=>'SI'),
                                array('NAME'=>'regfmodx','VALUE'=>$dFecha                               ,'CHECK'=>'SI'),
                                array('NAME'=>'reghmodx','VALUE'=>$tHora                                ,'CHECK'=>'SI'),
                                array('NAME'=>'regestxx','VALUE'=>'ACTIVO'                              ,'CHECK'=>'SI'));

            $xretora = f_MySql("INSERT","sys00017",$zInsertCab,$xConexion01,$cAlfa);
            //f_Mensaje(__FILE__, __LINE__, $_POST['cObgId']." + ".$_POST['cObsId']);
          }

          switch ($cAlfa) {
            case 'DEALMAVIVA':
            case 'TEALMAVIVA':
            case 'ALMAVIVA':
              if ($_POST['cDgeDt'] != "" && trim($_POST['cObsDtRep']) != "") {
                $_POST['cObsDtRep'] = "Justificacion Asignacion Documento de Transporte Repetido: {$_POST['cDgeDt']}. {$_POST['cObsDtRep']}";
                $qInsertObs = array(array('NAME'=>'sucidxxx','VALUE'=>trim($_POST['cAdmId'])  	,'CHECK'=>'NO'),
                                    array('NAME'=>'docidxxx','VALUE'=>trim($_POST['cDoiId'])  	,'CHECK'=>'SI'),
                                    array('NAME'=>'docsufxx','VALUE'=>trim($_POST['cDoiSfId'])	,'CHECK'=>'NO'),
                                    array('NAME'=>'obgidxxx','VALUE'=>trim($_POST['cObgId'])  	,'CHECK'=>'NO'),
                                    array('NAME'=>'obsidxxx','VALUE'=>trim($_POST['cObsId'])  	,'CHECK'=>'NO'),
                                    array('NAME'=>'doctipxx','VALUE'=>"IMPORTACION"           	,'CHECK'=>'NO'),
                                    array('NAME'=>'obsobsxx','VALUE'=>trim($_POST['cObsDtRep'])	,'CHECK'=>'SI'),
                                    array('NAME'=>'obsobs2x','VALUE'=>"NO"                      ,'CHECK'=>'NO'),
                                    array('NAME'=>'obstipxx','VALUE'=>'AGENCIA'                 ,'CHECK'=>'SI'),
                                    array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($kUser))  ,'CHECK'=>'SI'),
                                    array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')             ,'CHECK'=>'SI'),
                                    array('NAME'=>'reghcrex','VALUE'=>date("H:i:s")             ,'CHECK'=>'SI'),
                                    array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')             ,'CHECK'=>'SI'),
                                    array('NAME'=>'reghmodx','VALUE'=>date("H:i:s")             ,'CHECK'=>'SI'),
                                    array('NAME'=>'regestxx','VALUE'=>'ACTIVO'                  ,'CHECK'=>'SI'));

                $xInsertObs = f_MySql("INSERT","sys00017",$qInsertObs,$xConexion01,$cAlfa);
              }
            break;
            default:
              //No hace nada
            break;
          }

          if( $cAlfa == "ALMAVIVA" || $cAlfa == "DEALMAVIVA" || $cAlfa == "TEALMAVIVA" ){
            if($_POST['cEenNum'] != "" && $_POST['cEenNum'] != "NOAPLICA"){
              if($_POST['cEenNum'] != $_POST['cEenNumAnt']){
                $qUpEntEntAnt = array(array('NAME'=>'DOIIDXXX','VALUE'=>""                       ,'CHECK'=>'NO'),
                                      array('NAME'=>'DOISFIDX','VALUE'=>""                       ,'CHECK'=>'NO'),
                                      array('NAME'=>'ADMIDXXX','VALUE'=>""                       ,'CHECK'=>'NO'),
                                      array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']         ,'CHECK'=>'WH'),
                                      array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']       ,'CHECK'=>'WH'),
                                      array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']         ,'CHECK'=>'WH'));
                f_MySql("UPDATE","SIAI0250",$qUpEntEntAnt,$xConexion01,$cAlfa);

                $qUpdFac  = array(array('NAME'=>'EENFACEQ','VALUE'=>""                       ,'CHECK'=>'NO'),
                                  array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']         ,'CHECK'=>'WH'),
                                  array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']       ,'CHECK'=>'WH'),
                                  array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']         ,'CHECK'=>'WH'));
                f_MySql("UPDATE","SIAI0204",$qUpdFac,$xConexion01,$cAlfa);

                $qUpDatCompNueRem = array(array('NAME'=>'DOIDCNRX','VALUE'=>""                       ,'CHECK'=>'NO'),
                                          array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']         ,'CHECK'=>'WH'),
                                          array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']       ,'CHECK'=>'WH'),
                                          array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']         ,'CHECK'=>'WH'));
                f_MySql("UPDATE","SIAI0200",$qUpDatCompNueRem,$xConexion01,$cAlfa);

              }
              $qUpEntEnt  = array(array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']           ,'CHECK'=>'NO'),
                                  array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']         ,'CHECK'=>'NO'),
                                  array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']           ,'CHECK'=>'NO'),
                                  array('NAME'=>'EENNUMXX','VALUE'=>$_POST['cEenNum']          ,'CHECK'=>'WH'));
              f_MySql("UPDATE","SIAI0250",$qUpEntEnt,$xConexion01,$cAlfa);
            } else{
              $qUpDatCompNueRem = array(array('NAME'=>'DOIDCNRX','VALUE'=>""                       ,'CHECK'=>'NO'),
                                        array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']         ,'CHECK'=>'WH'),
                                        array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']       ,'CHECK'=>'WH'),
                                        array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']         ,'CHECK'=>'WH'));
              f_MySql("UPDATE","SIAI0200",$qUpDatCompNueRem,$xConexion01,$cAlfa);

              $qUpEntEntAnt = array(array('NAME'=>'DOIIDXXX','VALUE'=>""                       ,'CHECK'=>'NO'),
                                    array('NAME'=>'DOISFIDX','VALUE'=>""                       ,'CHECK'=>'NO'),
                                    array('NAME'=>'ADMIDXXX','VALUE'=>""                       ,'CHECK'=>'NO'),
                                    array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']         ,'CHECK'=>'WH'),
                                    array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']       ,'CHECK'=>'WH'),
                                    array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']         ,'CHECK'=>'WH'));
              f_MySql("UPDATE","SIAI0250",$qUpEntEntAnt,$xConexion01,$cAlfa);
            }

            /**
             * Consumo Web Service para Insertar DO en SAP
             */
            //Ticket OC-19434 - Modalidades excluidas de la transmision a SAP
            $vModExc = explode(",",str_replace(" ","",$vSysStr['almaviva_creacion_do_sap_modalidades_excluidas']));

            //Si la modalidad de importacion selecciona es diferente a una de las modalidades excluidas, 
            //y antes tenia asignada una modalidad excluida
            //debe crearse el DO en SAP
            if (in_array($_POST['cMimId'],$vModExc) == false && in_array($_POST['cMimId_ori'],$vModExc) == true ) {
              $vDatosERP['DOIIDXXX'] = $_POST['cDoiId'];
              $vDatosERP['ADMIDXXX'] = $_POST['cAdmId'];
              $vDatosERP['DOISFIDX'] = $_POST['cDoiSfId'];
              $vDatosERP['NITCLIEX'] = $vSysStr['financiero_nit_agencia_aduanas'];
              $vDatosERP['CLIIDXXX'] = $_POST['cCliId'];
              $vDatosERP['LINNEGDO'] = $vSysStr['almaviva_linea_negocio_sap'];
              $mReturnWs = $oWSOutsap->fnCrearDoSap($vDatosERP);
              if($mReturnWs[0] == "false"){
                $nSwitch = 0;
                $cMsjInsertDO .= "Se Presentaron Errores al Transmitir DO a SAP: \n";
                for($nI = 1; $nI < count($mReturnWs) ; $nI++){
                  $cMsjInsertDO .= $mReturnWs[$nI]."\n";
                }
                f_Mensaje(__FILE__,__LINE__,$cMsjInsertDO);
              }else{
                f_Mensaje(__FILE__,__LINE__,"Se Creo Con Exito el DO[{$vDatosERP['ADMIDXXX']}{$vDatosERP['DOIIDXXX']}{$vDatosERP['DOISFIDX']}] en SAP");
              }
            }
          }

          if ($flblo == 0) {
            f_Mensaje(__FILE__, __LINE__, "Datos Actualizados con Exito");
          } else {
            f_Mensaje(__FILE__, __LINE__, "DO con Levante o Finalizado, No se puede modificar");
          }
          ?>
          <form name = "frnav" action = "frdoiini.php" method = "post" target = "fmwork"></form>
          <script language = "javascript">
            parent.fmnav.location = '../nivel3.php';
            document.forms['frnav'].submit();
          </script>
          <?php
        }
      break;
      case "ANULAR":
        /**
         * Buscando Datos Items DO
         * Se deben buscar primero si los items fueron Creados por Res.0025 o son Items Antiguos para el Cambio de Estado
        */
        $qTipIte  = "SELECT ITEIDXXX, ITENUEXX ";
        $qTipIte .= "FROM SIAI0205 ";
        $qTipIte .= "WHERE ";
        $qTipIte .= "DOIIDXXX = \"{$_POST['cDoiId']}\"   AND ";
        $qTipIte .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
        $qTipIte .= "ADMIDXXX = \"{$_POST['cAdmId']}\" LIMIT 0,1 ";
        $xTipIte  = $mysql->f_Ejecutar($qTipIte);
        $vTipIte  = mysql_fetch_array($xTipIte);
        //f_Mensaje(__FILE__,__LINE__,$qTipIte."~".mysql_num_rows($xTipIte));

        $mSfdId = array();
        switch ($_POST['cRegEst']) {
          case "ACTIVO":
            $qItems  = "SELECT ";
            $qItems .= "ITEIDXXX, ";
            $qItems .= "ITENUEXX, ";
            $qItems .= "FACIDXXX, ";
            $qItems .= "PROIDXXX, ";
            $qItems .= "SFDIDXXX, ";
            $qItems .= "SFDTIPXX, ";
            $qItems .= "SFDID2XX, ";
            $qItems .= "SFDTIP2X, ";
            $qItems .= "ITECANDV, ";
            $qItems .= "ITECMSPV, ";
            $qItems .= "RESIDXXX  ";
            $qItems .= "FROM SIAI0205 ";
            $qItems .= "WHERE ";
            $qItems .= "DOIIDXXX = \"{$_POST['cDoiId']}\" AND ";
            $qItems .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
            $qItems .= "ADMIDXXX = \"{$_POST['cAdmId']}\" ";
            $xItems = $mysql->f_Ejecutar($qItems);
            // f_Mensaje(__FILE__,__LINE__,"ACTIVO: ".$qItems."~".mysql_num_rows($xItems));
            while ($vItems = mysql_fetch_array($xItems)) {
              $vFiltros = array();
              $mResultados = array();
              $bError = false;
              switch ($vItems['SFDTIPXX']) {
                case "ORDENC":
                case "FACTURA":
                case "SALDOSDELL":
                case "DANFOSS":
                case "ASTARA":
                case "PFIZER":

                  /**
                   * Consultando el Tipo de Saldo para cambiar la descripción del Tipo de Saldo
                   */
                  $qTipSal  = "SELECT NEGIDXXX ";
                  $qTipSal .= "FROM SIAI0152 ";
                  $qTipSal .= "WHERE ";
                  $qTipSal .= "SFDIDXXX = \"{$vItems['SFDIDXXX']}\" AND ";
                  $qTipSal .= "SFDTIPXX = \"{$vItems['SFDTIPXX']}\" LIMIT 0,1 ";
                  $xTipSal  = $mysql->f_Ejecutar($qTipSal);
                  $vTipSal  = mysql_fetch_array($xTipSal);

                  $vFiltros['SFDIDXXX'] = $vItems['SFDIDXXX'];
                  $vFiltros['SFDTIPXX'] = ($vTipSal['NEGIDXXX'] != "0" && $vItems['SFDTIPXX'] == "FACTURA") ? "OPENSMART" : $vItems['SFDTIPXX'];
                  $mResultados = $oImpo->fnCalculoSaldosItemsFactura($vFiltros);
                  $mValidacionSaldos = $mResultados[1];
                  $mValidacionSaldos[0]['PVVITEXX'] = ($mValidacionSaldos[0]['PVVITEXX'] != "") ? $mValidacionSaldos[0]['PVVITEXX'] : 0;
                  if ($mValidacionSaldos[0]['PVVITEXX'] == round($vItems['ITECANDV'],5)) {
                    $aCampos1 = array('SFDSALDO'=>'"'."SI".'"');
                    $aLlaves1 = array('SFDIDXXX'=>'"'.$vItems['SFDIDXXX'].'"');
                    $mysql->f_Actualizar("SIAI0152", $aCampos1, $aLlaves1);
                  }
                  
                  // Matriz para marcar Seriales BPO
                  if($vItems['SFDIDXXX'] != "" && $vItems['SFDTIPXX'] == "FACTURA"){
                    $nInd_mSfdId = count($mSfdId);
                    $mSfdId[$nInd_mSfdId]['SFDIDXXX'] = $vItems['SFDIDXXX'];
                    $mSfdId[$nInd_mSfdId]['SFDTIPXX'] = $vItems['SFDTIPXX'];
                  }
                break;
                case "REGISTRO":
                  $vFiltros['SIVIDXXX'] = $vItems['SFDIDXXX'];
                  $vFiltros['SFDTIPXX'] = $vItems['SFDTIPXX'];
                  $mResultados = $oVuce->fnCalculoSaldosItemsVUCE($vFiltros);
                  $mValidacionSaldos = $mResultados[1];
                  $mValidacionSaldos[0]['PVITEXXX'] = ($mValidacionSaldos[0]['PVITEXXX'] != "") ? $mValidacionSaldos[0]['PVITEXXX'] : 0;
                  if ($mValidacionSaldos[0]['PVITEXXX'] == $vItems['ITECANDV']) {
                    $aCampos1 = array('ITESALDO'=>'"'."SI".'"');
                    $aLlaves1 = array('ITEIDXXX'=>'"'.$vItems['ITEIDXXX'].'"',
                                      'SIVIDXXX'=>'"'.$vItems['SFDIDXXX'].'"');
                    $mysql->f_Actualizar("RIM00152", $aCampos1, $aLlaves1);
                  }
                break;
                case "PVALLEJO":
                  /*** Se llama el metodo de fnCalcularSaldosReserva, para marcar el campo de reservas en cero en SI si ya no hay saldo reserva. ***/
                  $vData['RESIDXXX'] = $vItems['RESIDXXX'];
                  $oPlanVallejo->fnCalcularSaldosReserva($vData);
                break;
              }

              //Liberando Saldos x El Consecutivo 2
              switch ($vItems['SFDTIP2X']) {
                case "REGISTRO":
                  $vFiltros['SIVIDXXX'] = $vItems['SFDID2XX'];
                  $vFiltros['SFDTIPXX'] = $vItems['SFDTIP2X'];
                  $mResultados = $oVuce->fnCalculoSaldosItemsVUCE($vFiltros);
                  $mValidacionSaldos = $mResultados[1];
                  $mValidacionSaldos[0]['PVITEXXX'] = ($mValidacionSaldos[0]['PVITEXXX'] != "") ? $mValidacionSaldos[0]['PVITEXXX'] : 0;
                  if ($mValidacionSaldos[0]['PVITEXXX'] == $vItems['ITECANDV']) {
                    $aCampos1 = array('ITESALDO'=>'"'."SI".'"');
                    $aLlaves1 = array('ITEIDXXX'=>'"'.$vItems['ITEIDXXX'].'"',
                                      'SIVIDXXX'=>'"'.$vItems['SFDIDXXX'].'"');
                    $mysql->f_Actualizar("RIM00152", $aCampos1, $aLlaves1);
                  }
                break;
              }
            }

            /*
            * Creacion de array para actualizar el estado del do
            */
            $aCampos = array('REGESTXX'=>'"'.trim(strtoupper($_POST['cRegEst'])).'"');
            $aLlave = array('DOIIDXXX'=>'"'.$_POST['cDoiId'].'"',
                            'DOISFIDX'=>'"'.$_POST['cDoiSfId'].'"',
                            'ADMIDXXX'=>'"'.$_POST['cAdmId'].'"');
            //DO Bloqueado
            $flblo = f_BloqDoi($_POST['cDoiId'], $_POST['cDoiSfId'], $_POST['cAdmId']);
            //Fin DO Bloqueado
            if ($flblo == 0) {
              $mysql->f_Actualizar("SIAI0200",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0202",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0203",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0204",$aCampos,$aLlave);
              if($vTipIte['ITENUEXX'] == "SI"){
                $qUpdateItem  = array(array('NAME'=>'REGESTXX','VALUE'=>"PROVISIONAL"              ,'CHECK'=>'SI'),
                                      array('NAME'=>'DOIIDXXX','VALUE'=>$_POST['cDoiId']           ,'CHECK'=>'WH'),
                                      array('NAME'=>'DOISFIDX','VALUE'=>$_POST['cDoiSfId']         ,'CHECK'=>'WH'),
                                      array('NAME'=>'ADMIDXXX','VALUE'=>$_POST['cAdmId']           ,'CHECK'=>'WH'));
                f_MySql("UPDATE","SIAI0205",$qUpdateItem,$xConexion01,$cAlfa);
                $oImpo->fnValidarEstadoItem($_POST['cAdmId'],$_POST['cDoiId'],$_POST['cDoiSfId']);
              }else{
                $mysql->f_Actualizar("SIAI0205",$aCampos,$aLlave);
              }
              $mysql->f_Actualizar("SIAI0206",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0207",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0208",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0209",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0210",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0211",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0212",$aCampos,$aLlave);
              
              // Proceso para Marcar Seriales BPO y Marcar Seriales Adicionales BPO.
              if(count($mSfdId) > 0){
                $oSerialesBpo = new cInterfacesDeclaraciones();
                $mReturnSerialesBpo = $oSerialesBpo->fnMarcarSerialesBPOxItems($mSfdId);
                if($mReturnSerialesBpo[0] == "false"){
                  $cMsjErr .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                  for($nS=1;$nS<count($mReturnSerialesBpo);$nS++){
                    $cMsjErr .= $mReturnSerialesBpo[$nS].".\n";
                  }
                  f_Mensaje(__FILE__, __LINE__, $cMsjErr."Verifique");            
                }
              }

              //Cambiando estado DO en financiero
              if ($vSysStr['system_aplica_financiero'] == "SI") {
                $aCampos = array('regestxx'=>'"'.trim(strtoupper($_POST['cRegEst'])).'"');
                $aLlave  = array('docidxxx'=>'"'.$_POST['cDoiId'].'"',
                                  'docsufxx'=>'"'.$_POST['cDoiSfId'].'"',
                                  'sucidxxx'=>'"'.$_POST['cAdmId'].'"');
                $mysql->f_Actualizar("sys00121",$aCampos,$aLlave);
              }
            }
            $titest = "Activo";

            /**
            * Invocando Metodo para Eliminar Tramite de la Cabecera de Saldos de Factura si se eliminaron todos los items de la Factura
            */

            switch($_POST['cRegEst']){
              case "ACTIVO":
                $vParametros['ACCIONXX'] = "ASOCIAR";
              break;
              case "INACTIVO":
                $vParametros['ACCIONXX'] = "ELIMINAR";
              break;
            }
            $cMsj01 = "";
            $vParametros['ADMIDXXX'] = $_POST['cAdmId'];
            $vParametros['DOIIDXXX'] = $_POST['cDoiId'];
            $vParametros['DOISFIDX'] = $_POST['cDoiSfId'];
            $objAsociarEliminarDoFactura = new cInterfacesDeclaraciones();
            $mReturnAsociarEliminarDoFactura = $objAsociarEliminarDoFactura->fnAsociarEliminarDoFactura($vParametros);
            if($mReturnAsociarEliminarDoFactura[0] == "false"){
              for($nR=1;$nR<count($mReturnAsociarEliminarDoFactura);$nR++){
                $cMsj01 .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                $cMsj01 .= $mReturnAsociarEliminarDoFactura[$nR]."\n";
              }
              f_Mensaje(__FILE__,__LINE__,$cMsj01);
            }

            /**
             * Realizando conexion y Seleccion de Base de datos ya que al llamar los Metodos del uticimpo se pierde la conexion
             */
            $mysql->f_Conectar();
            $mysql->f_SelecDb();

            // nuevo logger
            $arLog1 = array();
            $arLog2 = array();
            $actina = "ENABLE";
            $cLlavelg = $_POST['cDoiId']."=>".$_POST['cDoiSfId']."=>".$_POST['cAdmId'];
            f_DatosCambio($arLog1, $arLog2, "SIAI0200", "|DOIIDXXX=>DOISFIDX=>ADMIDXXX|", $cLlavelg, "", $actina, $kUser, f_Fecha(), f_Hora2(), ipCheck(), $_SERVER['PHP_SELF']);
            if ($flblo == 0) {
              f_Mensaje(__FILE__, __LINE__, "El DO/Imp $cAdmId-$cDoiId-$cDoiSfId se $titest con Exito");
            } else {
              f_Mensaje(__FILE__, __LINE__, "DO con Levante o Finalizado, No se puede modificar");
            }

            /**
             * Se guarda la Justificacion Cierre DO, cuando la variable del sistema esta encendida.
             */
            if($vSysStr['importaciones_exportaciones_justificar_cierre_DO'] == "SI"){
              $cObs = "CIERRE DE DO {$_POST['cAdmId']}-{$_POST['cDoiId']}-{$_POST['cDoiSfId']}. ".$_POST['cObsObs'].".";

              $qInsert  = array(array('NAME'=>'docidxxx','VALUE'=>trim($_POST['cDoiId'])    ,'CHECK'=>'SI'),
                                array('NAME'=>'sucidxxx','VALUE'=>trim($_POST['cAdmId'])    ,'CHECK'=>'NO'),
                                array('NAME'=>'docsufxx','VALUE'=>trim($_POST['cDoiSfId'])  ,'CHECK'=>'NO'),
                                array('NAME'=>'obgidxxx','VALUE'=>''                        ,'CHECK'=>'NO'),
                                array('NAME'=>'obsidxxx','VALUE'=>''                        ,'CHECK'=>'NO'),
                                array('NAME'=>'doctipxx','VALUE'=>'IMPORTACION'             ,'CHECK'=>'SI'),
                                array('NAME'=>'obsobsxx','VALUE'=>$cObs                     ,'CHECK'=>'SI'),
                                array('NAME'=>'obsobs2x','VALUE'=>'NO'                      ,'CHECK'=>'NO'),
                                array('NAME'=>'obstipxx','VALUE'=>'AGENCIA'                 ,'CHECK'=>'SI'),
                                array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($kUser))  ,'CHECK'=>'SI'),
                                array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')             ,'CHECK'=>'SI'),
                                array('NAME'=>'reghcrex','VALUE'=>date("H:i:s")             ,'CHECK'=>'SI'),
                                array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')             ,'CHECK'=>'SI'),
                                array('NAME'=>'reghmodx','VALUE'=>date("H:i:s")             ,'CHECK'=>'SI'),
                                array('NAME'=>'regestxx','VALUE'=>'ACTIVO'                  ,'CHECK'=>'SI'));

              if (!f_MySql("INSERT","sys00017",$qInsert,$xConexion01,$cAlfa)) {
                f_Mensaje(__FILE__, __LINE__, "Error al Guardar Observacion [sys00017].");
              }
            }

            if($vSysStr['importaciones_exportaciones_justificar_cierre_DO'] == "SI"){
              ?>
              <form name = "frnav" action = "frdoiini.php" method = "post" target = "fmwork"></form>
              <script language = "javascript">
                parent.window.close();
                document.forms['frnav'].submit();
              </script>
              <?php
            }else{
              ?>
              <form name = "frnav" action = "frdoiini.php" method = "post" target = "fmwork"></form>
              <script language = "javascript">
                document.forms['frnav'].submit();
              </script>
              <?php
            }
          break;
          case "INACTIVO":
            /*
            * Inactivando registros saldo de factura
            */
            $qItems  = "SELECT ";
            $qItems .= "ITEIDXXX, ";
            $qItems .= "ITENUEXX, ";
            $qItems .= "FACIDXXX, ";
            $qItems .= "PROIDXXX, ";
            $qItems .= "SFDIDXXX, ";
            $qItems .= "SFDTIPXX, ";
            $qItems .= "RESIDXXX ";
            $qItems .= "FROM SIAI0205 ";
            $qItems .= "WHERE ";
            $qItems .= "DOIIDXXX = \"{$_POST['cDoiId']}\"   AND ";
            $qItems .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
            $qItems .= "ADMIDXXX = \"{$_POST['cAdmId']}\"";
            $xItems = $mysql->f_Ejecutar($qItems);
            // f_Mensaje(__FILE__,__LINE__,"INACTIVO: ".$qItems."~".mysql_num_rows($xItems));
            while ($xRI = mysql_fetch_array($xItems)) {
              switch ($xRI['SFDTIPXX']) {
                case "ORDENC":
                case "FACTURA":
                case "SALDOSDELL":
                case "DANFOSS":
                case "ASTARA":
                case "PFIZER":
                  $qSalFac  = "SELECT ";
                  $qSalFac .= "SIAI0152.SFDIDXXX, ";
                  $qSalFac .= "SIAI0152.ITECSXXX, ";
                  $qSalFac .= "SIAI0152.ITEIDXXX, ";
                  $qSalFac .= "SIAI0152.FACIDXXX, ";
                  $qSalFac .= "SIAI0152.CLIIDXXX, ";
                  $qSalFac .= "SIAI0152.PIEIDXXX  ";
                  $qSalFac .= "FROM SIAI0152 ";
                  $qSalFac .= "WHERE ";
                  $qSalFac .= "SIAI0152.SFDIDXXX = \"{$xRI['SFDIDXXX']}\" AND ";
                  $qSalFac .= "SIAI0152.REGESTXX = \"ACTIVO\" ";
                  $xSalFac = $mysql->f_Ejecutar($qSalFac);
                  $mSalFac = array();
                  while ($xRSF = mysql_fetch_array($xSalFac)) {

                    $mSalFac[count($mSalFac)] = $xRSF;
                    #Busco si ese item tiene hijos
                    if (substr($xRSF['ITECSXXX'], 0, 1) == 'P') {
                      $cHijo = str_replace("P", "H", $xRSF['ITECSXXX']);
                      $qSalHijo = "SELECT ";
                      $qSalHijo .= "SIAI0152.SFDIDXXX, ";
                      $qSalHijo .= "SIAI0152.ITECSXXX, ";
                      $qSalHijo .= "SIAI0152.ITEIDXXX, ";
                      $qSalHijo .= "SIAI0152.FACIDXXX, ";
                      $qSalHijo .= "SIAI0152.CLIIDXXX, ";
                      $qSalHijo .= "SIAI0152.PIEIDXXX  ";
                      $qSalHijo .= "FROM SIAI0152 ";
                      $qSalHijo .= "WHERE ";
                      $qSalHijo .= "SIAI0152.FACIDXXX = \"{$xRSF['FACIDXXX']}\" AND ";
                      $qSalHijo .= "SIAI0152.ITECSXXX = \"$cHijo\" AND ";
                      $qSalHijo .= "SIAI0152.CLIIDXXX = \"{$xRSF['CLIIDXXX']}\" AND ";
                      $qSalHijo .= "SIAI0152.PIEIDXXX = \"{$xRSF['PIEIDXXX']}\" AND ";
                      $qSalHijo .= "SIAI0152.REGESTXX = \"ACTIVO\" ";
                      $xSalHijo = $mysql->f_Ejecutar($qSalHijo);
                      //f_Mensaje(__FILE__,__LINE__,$qSalHijo."~".mysql_num_rows($xSalHijo));
                      while ($xRSH = mysql_fetch_array($xSalHijo)) {
                        $mSalFac[count($mSalFac)] = $xRSH;
                      }
                    }
                    #Borrando Saldos de Factura
                    for ($nS = 0; $nS < count($mSalFac); $nS++) {
                      $aCampos = array('SFDSALDO'=>'""');
                      $aLlaveS = array('SFDIDXXX'=>'"'.$mSalFac[$nS]['SFDIDXXX'].'"');
                      $mysql->f_Actualizar("SIAI0152", $aCampos, $aLlaveS);
                    }
                  }
                  
                  // Matriz para marcar Seriales BPO
                  if($xRI['SFDIDXXX'] != "" && $xRI['SFDTIPXX'] == "FACTURA"){
                    $nInd_mSfdId = count($mSfdId);
                    $mSfdId[$nInd_mSfdId]['SFDIDXXX'] = $xRI['SFDIDXXX'];
                    $mSfdId[$nInd_mSfdId]['SFDTIPXX'] = $xRI['SFDTIPXX'];
                  }
                break;
                case "REGISTRO":
                  $qSalFac = "SELECT SIVIDXXX ";
                  $qSalFac .= "FROM RIM00152 ";
                  $qSalFac .= "WHERE SIVIDXXX = \"{$xRI['SFDIDXXX']}\" ";
                  $qSalFac .= "AND REGESTXX = \"ACTIVO\" ";
                  $xSalFac = $mysql->f_Ejecutar($qSalFac);
                  $mSalFac = array();
                  while ($xRSF = mysql_fetch_array($xSalFac)) {
                    $mSalFac[count($mSalFac)] = $xRSF;
                  }
                  #Borrando Saldos de Factrua
                  for ($nS = 0; $nS < count($mSalFac); $nS++) {
                    $aCampos = array('ITESALDO'=>'""');
                    $aLlaveS = array('SIVIDXXX'=>'"'.$mSalFac[$nS]['SIVIDXXX'].'"');
                    $mysql->f_Actualizar("RIM00152", $aCampos, $aLlaveS);
                  }
                break;
                case "PVALLEJO":
                  $qUpdRes  = array(array('NAME'=>'ressaldo','VALUE'=>""		            ,'CHECK'=>'NO'),
                                    array('NAME'=>'residxxx','VALUE'=>$xRI['RESIDXXX'] ,'CHECK'=>'WH'));

                  if (f_MySql("UPDATE","cspv1003",$qUpdRes,$xConexion01,$cAlfa)) {
                    /*** Se actualizo con exito reserva con saldo cero ***/
                  }else {
                    $zSwitch = 1;
                    f_Mensaje(__FILE__, __LINE__, "Error al Desmarcar Reserva con Saldo Cero.\nVerifique.");
                  }
                break;
              }
            }

            /*
            * Creacion de array para actualizar el estado del do
            */
            $aCampos  = array('REGESTXX'=>'"'.trim(strtoupper($_POST['cRegEst'])).'"');
            $aLlave   = array('DOIIDXXX'=>'"'.$_POST['cDoiId'].'"',
                              'DOISFIDX'=>'"'.$_POST['cDoiSfId'].'"',
                              'ADMIDXXX'=>'"'.$_POST['cAdmId'].'"');
            //DO Bloqueado
            $flblo = f_BloqDoi($_POST['cDoiId'], $_POST['cDoiSfId'], $_POST['cAdmId']);
            //Fin DO Bloqueado
            if ($flblo == 0) {
              $mysql->f_Actualizar("SIAI0200",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0202",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0203",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0204",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0205",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0206",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0207",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0208",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0209",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0210",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0211",$aCampos,$aLlave);
              $mysql->f_Actualizar("SIAI0212",$aCampos,$aLlave);

              //Cambiando estado DO en financiero
              if ($vSysStr['system_aplica_financiero'] == "SI") {
                $cEstado = ($_POST['cRegEst'] == "INACTIVO" && $rRet == "2") ? "FACTURADO" : $_POST['cRegEst'];
                $aCampos =  array('regestxx'=>'"'.trim(strtoupper($cEstado)).'"');
                $aLlave  =  array('docidxxx'=>'"'.$_POST['cDoiId'].'"',
                                  'docsufxx'=>'"'.$_POST['cDoiSfId'].'"',
                                  'sucidxxx'=>'"'.$_POST['cAdmId'].'"');
                $mysql->f_Actualizar("sys00121",$aCampos,$aLlave);
              }
              
              // Proceso para Marcar Seriales BPO y Marcar Seriales Adicionales BPO.
              if(count($mSfdId) > 0){
                $oSerialesBpo = new cInterfacesDeclaraciones();
                $mReturnSerialesBpo = $oSerialesBpo->fnMarcarSerialesBPOxItems($mSfdId);
                if($mReturnSerialesBpo[0] == "false"){
                  $cMsjErr .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                  for($nS=1;$nS<count($mReturnSerialesBpo);$nS++){
                    $cMsjErr .= $mReturnSerialesBpo[$nS].".\n";
                  }
                  f_Mensaje(__FILE__, __LINE__, $cMsjErr."Verifique");            
                }
              }
            }

            $titest = "Inactivo";

            /**
             * Invocando Metodo para Eliminar Tramite de la Cabecera de Saldos de Factura si se eliminaron todos los items de la Factura
             */
            $cMsj01 = "";
            $vParametros['ADMIDXXX'] = $_POST['cAdmId'];
            $vParametros['DOIIDXXX'] = $_POST['cDoiId'];
            $vParametros['DOISFIDX'] = $_POST['cDoiSfId'];
            $vParametros['ACCIONXX'] = "ELIMINAR";
            $objAsociarEliminarDoFactura = new cInterfacesDeclaraciones();
            $mReturnAsociarEliminarDoFactura = $objAsociarEliminarDoFactura->fnAsociarEliminarDoFactura($vParametros);
            if($mReturnAsociarEliminarDoFactura[0] == "false"){
              for($nR=1;$nR<count($mReturnAsociarEliminarDoFactura);$nR++){
                $cMsj01 .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
                $cMsj01 .= $mReturnAsociarEliminarDoFactura[$nR]."\n";
              }
              f_Mensaje(__FILE__,__LINE__,$cMsj01);
            }

            /**
             * Realizando conexion y Seleccion de Base de datos ya que al llamar los Metodos del uticimpo se pierde la conexion
             */
            $mysql->f_Conectar();
            $mysql->f_SelecDb();

            // nuevo logger
            $arLog1 = array();
            $arLog2 = array();
            $actina = "DISABLE";
            $cLlavelg = $_POST['cDoiId']."=>".$_POST['cDoiSfId']."=>".$_POST['cAdmId'];
            f_DatosCambio($arLog1, $arLog2, "SIAI0200", "|DOIIDXXX=>DOISFIDX=>ADMIDXXX|", $cLlavelg, "", $actina, $kUser, f_Fecha(), f_Hora2(), ipCheck(), $_SERVER['PHP_SELF']);
            if ($flblo == 0) {
              f_Mensaje(__FILE__, __LINE__, "El DO/Imp $cAdmId-$cDoiId-$cDoiSfId se $titest con Exito");
            } else {
              f_Mensaje(__FILE__, __LINE__, "DO con Levante o Finalizado, No se puede modificar");
            }

            /**
             * Se guarda la Justificacion Cierre DO cuando la variable del sistema esta encendida
             */
            if($vSysStr['importaciones_exportaciones_justificar_cierre_DO'] == "SI"){
              $cObs = "CIERRE DE DO {$_POST['cAdmId']}-{$_POST['cDoiId']}-{$_POST['cDoiSfId']}. ".$_POST['cObsObs'].".";

              $qInsert  = array(array('NAME'=>'docidxxx','VALUE'=>trim($_POST['cDoiId'])    ,'CHECK'=>'SI'),
                                array('NAME'=>'sucidxxx','VALUE'=>trim($_POST['cAdmId'])    ,'CHECK'=>'NO'),
                                array('NAME'=>'docsufxx','VALUE'=>trim($_POST['cDoiSfId'])  ,'CHECK'=>'NO'),
                                array('NAME'=>'obgidxxx','VALUE'=>''                        ,'CHECK'=>'NO'),
                                array('NAME'=>'obsidxxx','VALUE'=>''                        ,'CHECK'=>'NO'),
                                array('NAME'=>'doctipxx','VALUE'=>'IMPORTACION'             ,'CHECK'=>'SI'),
                                array('NAME'=>'obsobsxx','VALUE'=>$cObs                     ,'CHECK'=>'SI'),
                                array('NAME'=>'obsobs2x','VALUE'=>'NO'                      ,'CHECK'=>'NO'),
                                array('NAME'=>'obstipxx','VALUE'=>'AGENCIA'                 ,'CHECK'=>'SI'),
                                array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($kUser))  ,'CHECK'=>'SI'),
                                array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')             ,'CHECK'=>'SI'),
                                array('NAME'=>'reghcrex','VALUE'=>date("H:i:s")             ,'CHECK'=>'SI'),
                                array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')             ,'CHECK'=>'SI'),
                                array('NAME'=>'reghmodx','VALUE'=>date("H:i:s")             ,'CHECK'=>'SI'),
                                array('NAME'=>'regestxx','VALUE'=>'ACTIVO'                  ,'CHECK'=>'SI'));

              if (!f_MySql("INSERT","sys00017",$qInsert,$xConexion01,$cAlfa)) {
                f_Mensaje(__FILE__, __LINE__, "Error al Guardar Observacion [sys00017].");
              }

              switch($cAlfa){
                case "ALMAVIVA":
                case "DEALMAVIVA":
                case "TEALMAVIVA":

                  $vData = array();
                  $vData['DOIIDXXX'] = trim($_POST['cDoiId']);
                  $vData['DOISFIDX'] = trim($_POST['cDoiSfId']);
                  $vData['ADMIDXXX'] = trim($_POST['cAdmId']);
                  $vData['USRIDXXX'] = trim(strtoupper($kUser));
                  $vData['OBSOBSXX'] = $cObs;
                  $ObjNotificacionesAlmaviva = new cNotificacionesAlmaviva();
                  $ObjNotificacionesAlmaviva->fnNotificacionCierreDo($vData,$cAlfa);
                break;
              }
            }

            if($vSysStr['importaciones_exportaciones_justificar_cierre_DO'] == "SI"){
              ?>
              <form name = "frnav" action = "frdoiini.php" method = "post" target = "fmwork"></form>
              <script language = "javascript">
                parent.window.close();
                document.forms['frnav'].submit();
              </script>
              <?php
            }else{
              ?>
              <form name = "frnav" action = "frdoiini.php" method = "post" target = "fmwork"></form>
              <script language = "javascript">
                document.forms['frnav'].submit();
              </script>
              <?php
            }
          break;
        }
      break;
      case "BORRAR":
        $aLlave = array('DOIIDXXX'=>'"'.$_POST['cDoiId'].'"',
                        'DOISFIDX'=>'"'.$_POST['cDoiSfId'].'"',
                        'ADMIDXXX'=>'"'.$_POST['cAdmId'].'"');

        //DO Bloqueado
        $flblo = f_BloqDoi($_POST['cDoiId'], $_POST['cDoiSfId'], $_POST['cAdmId']);
        $nFilr = 0;
        /* if ($kMysqlDb == 'ALPOPULX' || $kMysqlDb == 'FACTURAX' || $kMysqlDb == 'ALPOPULP'){
        $zsRim = mysql_query("SELECT REGREFXX FROM RIM00150 WHERE REGREFXX = \"{$_POST['cDoiId']}\" LIMIT 0,1");
        $nFilr = mysql_num_rows($zsRim);
        } */
        //Fin DO Bloqueado

        /**
         * Borrando Asiganacion de DO a numeros de operacion
         */
        if ($vSysStr['opensmart_activar_modulo'] == "SI") {
          /**
           * Busco datos del DO
           */
          $qDatDoi  = "SELECT ";
          $qDatDoi .= "DOIIDXXX, DOISFIDX, ADMIDXXX, DOINODXX, CLIIDXXX ";
          $qDatDoi .= "FROM $cAlfa.SIAI0200 ";
          $qDatDoi .= "WHERE ";
          $qDatDoi .= "DOIIDXXX = \"{$_POST['cDoiId']}\"  AND ";
          $qDatDoi .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
          $qDatDoi .= "ADMIDXXX = \"{$_POST['cAdmId']}\"  LIMIT 0,1 ";
          $xDatDoi = f_MySql("SELECT", "", $qDatDoi, $xConexion01, "");
          //f_Mensaje(__FILE__,__LINE__,$qDatDoi."~".mysql_num_rows($xDatDoi));
          $xRDD = mysql_fetch_array($xDatDoi);

          if ($xRDD['DOINODXX'] != "") {
            //Solo se envian los Numero de operacion Nuevos
            $mDat = explode("~", $xRDD['DOINODXX']);
            $nError = 0;
            $cMsjAsig = "";
            for ($nD = 0; $nD < count($mDat); $nD++) {
              //f_Mensaje(__FILE__,__LINE__,$mDat[$nD]);
              if ($mDat[$nD] != "") {
                //Llamado al WS de SyC para Asignar Do a Numeros de operacion
                $oWSOutput = new cWSOutput;
                $vDatos['numopexx'] = $mDat[$nD];
                $vDatos['doiidxxx'] = $_POST['cDoiId'];      //-> Do
                $vDatos['doisfidx'] = $_POST['cDoiSfId'];     //-> Sufijo
                $vDatos['admidxxx'] = $_POST['cAdmId'];      //-> Sucursal
                $vDatos['doitipxx'] = "IMPORTACION";   //-> Tipo de operacion
                $vDatos['cliidxxx'] = $xRDD['CLIIDXXX'];      //-> Cliente
                $vDatos['usridxxx'] = $kUser;      //-> Usuario que Asocia el DO al numero de operacion.
                $vReturn = $oWSOutput->fnEliminarDoaOperacionDocumental($vDatos);

                if ($vReturn[0] == "false") {
                  $nError = 1;
                  for ($nR = 1; $nR < count($vReturn); $nR++) {
                    $cMsjAsig .= "- ".$vReturn[$nR]."\n";
                  }
                }
              }
            }
            if ($nError == 1) {
              f_Mensaje(__FILE__, __LINE__, "Errores en la Ejecucion del Web Service [eliminarAsignacionDO]:\n\n".$cMsjAsig);
            }
          }
        }

        if ($flblo == 0 && $nFilr <= 0) {
          /**
           * Buscando Datos Items DO
           * Se deben buscar primero si los items fueron Creados por Res.0178
           * porque si son por la nueva opcion hay que borrar los seriales y saldos de factura asociados
           */
          $qItems  = "SELECT ";
          $qItems .= "ITEIDXXX, ";
          $qItems .= "ITENUEXX, ";
          $qItems .= "FACIDXXX, ";
          $qItems .= "PROIDXXX, ";
          $qItems .= "SFDIDXXX, ";
          $qItems .= "SFDTIPXX, ";
          $qItems .= "RESIDXXX, ";
          $qItems .= "ITECSHXX ";
          $qItems .= "FROM SIAI0205 ";
          $qItems .= "WHERE ";
          $qItems .= "DOIIDXXX = \"{$_POST['cDoiId']}\"   AND ";
          $qItems .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
          $qItems .= "ADMIDXXX = \"{$_POST['cAdmId']}\" ";
          $xItems = $mysql->f_Ejecutar($qItems);
          // f_Mensaje(__FILE__,__LINE__,"BORRAR: ".$qItems."~".mysql_num_rows($xItems));

          $mSalFac = array();
          while ($xRI = mysql_fetch_array($xItems)) {

            #Borrando los seriales
            if ($xRI['ITENUEXX'] == "SI") {
              $aLlaveI  = array('DOIIDXXX'=>'"'.$_POST['cDoiId'].'"',
                                'DOISFIDX'=>'"'.$_POST['cDoiSfId'].'"',
                                'ADMIDXXX'=>'"'.$_POST['cAdmId'].'"',
                                'ITEIDXXX'=>'"'.$xRI['ITEIDXXX'].'"');
              $mysql->f_Eliminar("SIAI0221", $aLlaveI);
            }

            #Liberando los seriales eWms Sap
            if ($xRI['ITENUEXX'] == "SI") {
              switch ($cAlfa) {
                case "DHLXXXXX":
                case "DEDHLXXXXX":
                case "TEDHLXXXXX":
                  $vDatos = array();
                  $vDatos['DOIIDXXX'] = $_POST['cDoiId'];   //DO Impo 
                  $vDatos['DOISFIDX'] = $_POST['cDoiSfId']; //Sufijo 
                  $vDatos['ADMIDXXX'] = $_POST['cAdmId'];   //Sucursal 
                  $vDatos['ITEIDXXX'] = $xRI['ITEIDXXX'];   //Item 
                  $objProcesosIntegracioneWmsSap->fnLiberarSerialeseWMSSap($vDatos);
                break;
              }
            }

            //Liberando Saldos de Accesorios
            switch($cAlfa){
              case "SIACOSIA":
              case "TESIACOSIP":
              case "DESIACOSIP":
                if($xRI['ITECSHXX'] != ""){
                  $vLiberarAccesorios['SFDIDXXX'] = $xRI['ITECSHXX'];
                  $mReturnLiberarAccesorios = $oImpo->fnLiberarSaldosAccesorios($vLiberarAccesorios);
                }
              break;
            }

            #Borrando los LOTES
            if ($xRI['ITENUEXX'] == "SI") {
              $aLlaveI  = array('DOIIDXXX'=>'"'.$_POST['cDoiId'].'"',
                                'DOISFIDX'=>'"'.$_POST['cDoiSfId'].'"',
                                'ADMIDXXX'=>'"'.$_POST['cAdmId'].'"',
                                'ITEIDXXX'=>'"'.$xRI['ITEIDXXX'].'"');
              $mysql->f_Eliminar("SIAI0222", $aLlaveI);
            }

            #Borrando los seriales adicionales
            if ($xRI['ITENUEXX'] == "SI") {
              $aLlaveI  = array('DOIIDXXX'=>'"'.$_POST['cDoiId'].'"',
                                'DOISFIDX'=>'"'.$_POST['cDoiSfId'].'"',
                                'ADMIDXXX'=>'"'.$_POST['cAdmId'].'"',
                                'ITEIDXXX'=>'"'.$xRI['ITEIDXXX'].'"');
              $mysql->f_Eliminar("SIAI0234", $aLlaveI);
            }

            #Borrando los Cabecera Seriales Componentes
            if ($xRI['ITENUEXX'] == "SI") {
              $aLlaveI  = array('DOIIDXXX'=>'"'.$_POST['cDoiId'].'"',
                                'DOISFIDX'=>'"'.$_POST['cDoiSfId'].'"',
                                'ADMIDXXX'=>'"'.$_POST['cAdmId'].'"',
                                'ITEIDXXX'=>'"'.$xRI['ITEIDXXX'].'"');
              $mysql->f_Eliminar("SIAI0246", $aLlaveI);
            }

            #Borrando los seriales Componentes
            if ($xRI['ITENUEXX'] == "SI") {
              $aLlaveI  = array('DOIIDXXX'=>'"'.$_POST['cDoiId'].'"',
                                'DOISFIDX'=>'"'.$_POST['cDoiSfId'].'"',
                                'ADMIDXXX'=>'"'.$_POST['cAdmId'].'"',
                                'ITEIDXXX'=>'"'.$xRI['ITEIDXXX'].'"');
              $mysql->f_Eliminar("SIAI0247", $aLlaveI);
            }

            switch ($xRI['SFDTIPXX']) {
              case "ORDENC":
              case "FACTURA":
              case "SALDOSDELL":
              case "DANFOSS":
              case "ASTARA":
              case "PFIZER":
                #Busco saldos de factura
                $qSalFac = "SELECT ";
                $qSalFac .= "SIAI0152.SFDIDXXX, ";
                $qSalFac .= "SIAI0152.ITECSXXX, ";
                $qSalFac .= "SIAI0152.ITEIDXXX, ";
                $qSalFac .= "SIAI0152.FACIDXXX, ";
                $qSalFac .= "SIAI0152.CLIIDXXX, ";
                $qSalFac .= "SIAI0152.PIEIDXXX  ";
                $qSalFac .= "FROM SIAI0152 ";
                $qSalFac .= "WHERE ";
                $qSalFac .= "SIAI0152.SFDIDXXX = \"{$xRI['SFDIDXXX']}\" AND ";
                $qSalFac .= "SIAI0152.REGESTXX = \"ACTIVO\" ";
                $xSalFac  = $mysql->f_Ejecutar($qSalFac);
                $mSalFac = array();
                while ($xRSF = mysql_fetch_array($xSalFac)) {
                  $mSalFac[count($mSalFac)] = $xRSF;
                  #Busco si ese item tiene hijos
                  if (substr($xRSF['ITECSXXX'], 0, 1) == 'P') {
                    $cHijo = str_replace("P", "H", $xRSF['ITECSXXX']);

                    $qSalHijo = "SELECT ";
                    $qSalHijo .= "SIAI0152.SFDIDXXX, ";
                    $qSalHijo .= "SIAI0152.ITECSXXX, ";
                    $qSalHijo .= "SIAI0152.ITEIDXXX, ";
                    $qSalHijo .= "SIAI0152.FACIDXXX, ";
                    $qSalHijo .= "SIAI0152.CLIIDXXX, ";
                    $qSalHijo .= "SIAI0152.PIEIDXXX  ";
                    $qSalHijo .= "FROM SIAI0152 ";
                    $qSalHijo .= "WHERE ";
                    $qSalHijo .= "SIAI0152.FACIDXXX = \"{$xRSF['FACIDXXX']}\" AND ";
                    $qSalHijo .= "SIAI0152.ITECSXXX = \"$cHijo\" AND ";
                    $qSalHijo .= "SIAI0152.CLIIDXXX = \"{$xRSF['CLIIDXXX']}\" AND ";
                    $qSalHijo .= "SIAI0152.PIEIDXXX = \"{$xRSF['PIEIDXXX']}\" AND ";
                    $qSalHijo .= "SIAI0152.REGESTXX = \"ACTIVO\" ";
                    $xSalHijo  = $mysql->f_Ejecutar($qSalHijo);
                    //f_Mensaje(__FILE__,__LINE__,$qSalHijo."~".mysql_num_rows($xSalHijo));
                    while ($xRSH = mysql_fetch_array($xSalHijo)) {
                      $mSalFac[count($mSalFac)] = $xRSH;
                    }
                  }
                  #Liberando Saldos de Factura
                  for ($nS = 0; $nS < count($mSalFac); $nS++) {
                    $aCampos = array('SFDSALDO'=>'""');
                    $aLlaveS = array('SFDIDXXX'=>'"'.$mSalFac[$nS]['SFDIDXXX'].'"');
                    $mysql->f_Actualizar("SIAI0152", $aCampos, $aLlaveS);
                  }
                }
              break;
              case "REGISTRO":
                #Busco Saldos de Registro
                $qSalReg  = "SELECT SIVIDXXX ";
                $qSalReg .= "FROM RIM00152 ";
                $qSalReg .= "WHERE SIVIDXXX = \"{$xRI['SFDIDXXX']}\" ";
                $qSalReg .= "AND REGESTXX = \"ACTIVO\" ";
                $xSalReg  = $mysql->f_Ejecutar($qSalReg);
                $mSalReg  = array();
                while ($xRSF = mysql_fetch_array($xSalReg)) {
                  $mSalReg[count($mSalReg)] = $xRSF;
                }
                #Liberacion Saldos de Registro
                for ($nS = 0; $nS < count($mSalReg); $nS++) {
                  $aCampos = array('ITESALDO'=>'""');
                  $aLlaveS = array('SIVIDXXX'=>'"'.$mSalReg[$nS]['SIVIDXXX'].'"');
                  $mysql->f_Actualizar("RIM00152", $aCampos, $aLlaveS);
                }
              break;
              case "PVALLEJO":
                $qUpdRes  = array(array('NAME'=>'ressaldo','VALUE'=>""		            ,'CHECK'=>'NO'),
                                  array('NAME'=>'residxxx','VALUE'=>$xRI['RESIDXXX'] ,'CHECK'=>'WH'));

                if (f_MySql("UPDATE","cspv1003",$qUpdRes,$xConexion01,$cAlfa)) {
                  /*** Se actualizo con exito reserva con saldo cero ***/
                }else {
                  $zSwitch = 1;
                  f_Mensaje(__FILE__, __LINE__, "Error al Desmarcar Reserva con Saldo Cero.\nVerifique.");
                }
              break;
            }
          }

          //Trazabilidad al Borrar DO
          $qConDo  = "SELECT ";
          $qConDo .= "ADMIDXXX,";
          $qConDo .= "DOIIDXXX,";
          $qConDo .= "DOISFIDX,";
          $qConDo .= "CLIIDXXX,"; 
          $qConDo .= "REGFECXX ";
          $qConDo .= "FROM $cAlfa.SIAI0200 ";
          $qConDo .= "WHERE ";
          $qConDo .= "DOIIDXXX = \"{$_POST['cDoiId']}\"   AND ";
          $qConDo .= "DOISFIDX = \"{$_POST['cDoiSfId']}\" AND ";
          $qConDo .= "ADMIDXXX = \"{$_POST['cAdmId']}\"  LIMIT 0,1";
          $xConDo  = f_MySql("SELECT","",$qConDo,$xConexion01,"");
          $vConDo  = mysql_fetch_array($xConDo);
          
          $vTramite = array();
          $vTramite['ADMIDXXX'] = $vConDo['ADMIDXXX'];
          $vTramite['DOIIDXXX'] = $vConDo['DOIIDXXX'];
          $vTramite['DOISFIDX'] = $vConDo['DOISFIDX'];
          $vTramite['CLIIDXXX'] = $vConDo['CLIIDXXX'];
          $vTramite['PROCESOX'] = 'BORRAR_DO';
          $vTramite['DETALLEX'] = 'ELIMINADO COMPLETO DEL DO EN OPENCOMEX';
          
          $oLogPro = new cLogProcesos();
          $mReturnLogBorrarDatosDo = $oLogPro->fnLogBorrarDatosDo($vTramite);
          if($mReturnLogBorrarDatosDo[0] == "false") {
            $cMsjError = "";
            for ($i=1; $i <= count($mReturnLogBorrarDatosDo) ; $i++) {
              $cMsjError .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsjError .= $mReturnLogBorrarDatosDo[$i]."\n";
            }
            if ($cMsjError != "") {
              f_Mensaje(__FILE__,__LINE__, $cMsjError."Verifique");
            } 
          }

          $mysql->f_Conectar();
          $mysql->f_SelecDb();

          $mysql->f_Eliminar("SIAI0200", $aLlave);
          $mysql->f_Eliminar("SIAI0202", $aLlave);
          $mysql->f_Eliminar("SIAI0203", $aLlave);
          $mysql->f_Eliminar("SIAI0204", $aLlave);
          $mysql->f_Eliminar("SIAI0205", $aLlave);
          $mysql->f_Eliminar("SIAI0206", $aLlave);
          $mysql->f_Eliminar("SIAI0207", $aLlave);
          $mysql->f_Eliminar("SIAI0208", $aLlave);
          $mysql->f_Eliminar("SIAI0209", $aLlave);
          $mysql->f_Eliminar("SIAI0210", $aLlave);
          $mysql->f_Eliminar("SIAI0211", $aLlave);
          $mysql->f_Eliminar("SIAI0212", $aLlave);
          $mysql->f_Eliminar("SIAI0257", $aLlave);

          switch($kMysqlDb) {
            case "DHLEXPRE":
            case "TEDHLEXPRE":
            case "DEDHLEXPRE":
              // Obtiene el año de la fecha de registro del DO
              $nAnio = substr($vConDo['REGFECXX'], 0, 4);

              $qTabAct = "SHOW TABLES FROM $cAlfa LIKE \"zpim$nAnio\" ";
              $xTabAct = f_MySql("SELECT","",$qTabAct,$xConexion01,"");
              if(mysql_num_rows($xTabAct) > 0){
                // Consulta la Tabla Lectura Prealertas
                $qLecPreI  = "SELECT ";
                $qLecPreI .= "lpiidxxx ";
                $qLecPreI .= "FROM zpim$nAnio ";
                $qLecPreI .= "WHERE ";
                $qLecPreI .= "doiidxxx = \"{$_POST['cDoiId']}\" AND ";
                $qLecPreI .= "doisfidx = \"{$_POST['cDoiSfId']}\" AND ";
                $qLecPreI .= "admidxxx = \"{$_POST['cAdmId']}\" LIMIT 0,1 ";
                $xLecPreI  = f_MySql("SELECT","",$qLecPreI,$xConexion01,"");
                // Determina si debe válidar la Lectura del texto Prealertas
                // f_Mensaje(__FILE__,__LINE__,$qLecPreI."-".mysql_num_rows($xLecPreI));
                if(mysql_num_rows($xLecPreI) == 1) {
                  $qDelete  = array(array('NAME'=>'doiidxxx', 'VALUE'=>$_POST['cDoiId']		  , 'CHECK'=>'WH'),
                                    array('NAME'=>'doisfidx', 'VALUE'=>$_POST['cDoiSfId']		, 'CHECK'=>'WH'),
                                    array('NAME'=>'admidxxx', 'VALUE'=>$_POST['cAdmId']			, 'CHECK'=>'WH'));
                  if(!f_MySql("DELETE", "zpim$nAnio", $qDelete, $xConexion01, $cAlfa)){
                    $nSwitch = 1;
                    f_Mensaje(__FILE__,__LINE__,"Error al Eliminar Registros en la Tabla[zpim$nAnio], Verifique.");
                  }
                }
              }
            break;
          }

          // Proceso para Marcar Seriales BPO y Marcar Seriales Adicionales BPO.
          if(count($mSfdId) > 0){
            $oSerialesBpo = new cInterfacesDeclaraciones();
            $mReturnSerialesBpo = $oSerialesBpo->fnMarcarSerialesBPOxItems($mSfdId);
            if($mReturnSerialesBpo[0] == "false"){
              $cMsjErr .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
              for($nS=1;$nS<count($mReturnSerialesBpo);$nS++){
                $cMsjErr .= $mReturnSerialesBpo[$nS].".\n";
              }
              f_Mensaje(__FILE__, __LINE__, $cMsjErr."Verifique");            
            }
          }

          $aLlave2  = array('docidxxx'=>'"'.$_POST['cDoiId'].'"',
                            'docsufxx'=>'"'.$_POST['cDoiSfId'].'"',
                            'sucidxxx'=>'"'.$_POST['cAdmId'].'"',
                            'doctipxx'=>'"'."IMPORTACION".'"');
          // $mysql->f_Eliminar("sys00017", $aLlave2);

          if ($vSysStr['system_aplica_financiero'] == "SI") {
            switch($kMysqlDb) {
              case "DESIACOSIP":
              case "TESIACOSIP":
              case "SIACOSIA":
                //Para Siaco no se elimina porque hay datos que se envian desde GRM que no estan en opencomex
                //En SIACO borrar un DO para despues clonar los datos de ADUANA
              break;
              default:
                $aLlave2  = array('docidxxx'=>'"'.$_POST['cDoiId'].'"',
                                  'docsufxx'=>'"'.$_POST['cDoiSfId'].'"',
                                  'sucidxxx'=>'"'.$_POST['cAdmId'].'"',
                                  'doctipxx'=>'"'."IMPORTACION".'"');
                $mysql->f_Eliminar("sys00121", $aLlave2);
              break;
            }
          }

          /**
           * Haciendo Observacion a cada uno de los Consecutivos DAV asignados al Do, si el Tramite ya se le habian asignado Consecutivos DAV.
           */
          if (count($mCscDav) > 0) {

            $cMsj02 = "";
            $oActualizaConsecutivosDAV = new cConsecutivosDAV();

            for ($x = 0; $x < count($mCscDav); $x++) {
              $vFiltros = array();
              $bApliObs2 = false;
              switch ($mCscDav[$x]['AUTORIZA']) {
                case "VARIABLE":
                  $mCscDav[$x]['OBSERVAX'] = "|__{$kUser}__".date('Y-m-d')."__".date('H:i:s')."__EL CONSECUTIVO FUE BORRADO POR EL PROCESO BORRAR DO.__|";
                  $mCscDav[$x]['OBSERVA2'] = "|__{$kUser}__".date('Y-m-d')."__".date('H:i:s')."__EL PDF FUE BORRADO POR EL PROCESO BORRAR DO.__|";
                break;
                case "ELIMINAR":
                  $mCscDav[$x]['OBSERVAX'] = "|__{$kUser}__".date('Y-m-d')."__".date('H:i:s')."__EL CONSECUTIVO FUE BORRADO POR EL PROCESO BORRAR DO A PARTIR DE AUTORIZACION.__|";
                  $mCscDav[$x]['OBSERVA2'] = "|__{$kUser}__".date('Y-m-d')."__".date('H:i:s')."__EL PDF FUE BORRADO POR EL PROCESO BORRAR DO A PARTIR DE AUTORIZACION.__|";
                break;
              }

              /**
               * Verificando si existen pdf dav generados
               */

              //En caso de estar en siaco
              if (substr_count($kMysqlDb, "SIACOSIA") > 0) {
                //directorio de pdfs
                $cRuta_Siaco = "../../../conexiongrm/archivogestor/1/1";
                $qSql = "SELECT REGFECXX FROM $cEta.MDOS$nAno WHERE DOCNROXX = \"{$doid}\" AND SUCIDXXX =\"{$admi}\" LIMIT 0,1";
                $xSql = mysql_query($qSql,$xConexionGRM);
                while (($resmdos = mysql_fetch_array($xSql)) != false) {
                  $cArrRuta = explode("-", $resmdos['REGFECXX']);
                }
                $fdav_siaco = $cRuta_Siaco."/".$zRPro['CLIIDXXX']."/".$cArrRuta[0]."/".$cArrRuta[1]."/".$doid."/declaraciones/".$mCscDav[$x]['LIMACEXX']."/dav";
                if (is_dir($fdav_siaco)) {
                  $archivos = scandir($fdav_siaco); //hace una lista de archivos del directorio
                  $num = count($archivos); //los cuenta
                  for ($i = 0; $i <= $num; $i++) {
                    if ($archivos[$i] != "." and $archivos[$i] != ".." and $archivos[$i] != "") {
                      $cFileDel = $fdav_siaco."/".$archivos[$i];
                      if (substr_count($cFileDel, $mCscDav[$x]['DAVIDXXX']) > 0) {
                        $bApliObs2 = true;
                        break;
                      }
                    }
                  }
                }
              } else {
                //entorno normal
                //directorio de pdfs
                $cRuta = "../../propios/$kMysqlDb/documentos/impo/{$mCscDav[$x]['ADMIDXXX']}-{$mCscDav[$x]['DOIIDXXX']}-{$mCscDav[$x]['DOISFIDX']}/dav/".trim($mCscDav[$x]['DAVIDXXX']).".pdf";
                if (is_file("$cRuta")) {
                  $bApliObs2 = true;
                }
              }
              /**
               * Guardando Observacion por Consecutivo Dav
               */
              $vFiltros['CAMPOACT'] = "OBSERVACION";
              $vFiltros['DOIIDXXX'] = $mCscDav[$x]['DOIIDXXX'];
              $vFiltros['DOISFIDX'] = $mCscDav[$x]['DOISFIDX'];
              $vFiltros['ADMIDXXX'] = $mCscDav[$x]['ADMIDXXX'];
              $vFiltros['DAVIDXXX'] = $mCscDav[$x]['DAVIDXXX'];
              $vFiltros['DAVOBSXX'] = $mCscDav[$x]['OBSERVAX'];
              if ($bApliObs2) {
                $vFiltros['BDAVOBS2'] = "SI";
                $vFiltros['DAVOBS2X'] = $mCscDav[$x]['OBSERVA2'];
              }
              $mActualizaConsecutivosDAV = $oActualizaConsecutivosDAV->fnActualizaConsecutivosDAV($vFiltros);
              if ($mActualizaConsecutivosDAV[0] == "false") {
                for ($xm = 1; $xm < count($mActualizaConsecutivosDAV); $xm++) {
                  $cMsj02 .= $mActualizaConsecutivosDAV[$i]."\n";
                }
              }
            }
            if ($cMsj02 != "") {
              f_Mensaje(__FILE__, __LINE__, $cMsj02."Verifique.");
            }
          }

          /**
           * Haciendo Observacion al Consecutivo Acta Reconocimiento asignado al Do, si el Tramite ya se le habian asignado Consecutivo Acta Reconocimiento.
           */
          if ($mCscRec[1] != "") {

            //Variable que concatena errores
            $cMsj03 = "";

            //Valido si esta habilitado para Eliminar o si tiene Autorizacion
            switch ($mCscRec[2]) {
              case "VARIABLE":
                $mCscRec[4] = "|__{$kUser}__".date('Y-m-d')."__".date('H:i:s')."__EL CONSECUTIVO FUE BORRADO POR EL PROCESO BORRAR DO.__|";
              break;
              case "ELIMINAR":
                $mCscRec[4] = "|__{$kUser}__".date('Y-m-d')."__".date('H:i:s')."__EL CONSECUTIVO FUE BORRADO POR EL PROCESO BORRAR DO A PARTIR DE AUTORIZACION.__|";
              break;
            }

            //Guardando Observacion Consecutivo Acta Reconocimiento
            $vFiltros = array();
            $vFiltros['campoact'] = "BORRARDO";
            $vFiltros['doiidxxx'] = $_POST['cDoiId'];   //Do
            $vFiltros['admidxxx'] = $_POST['cAdmId'];   //Sucursal
            $vFiltros['docsufxx'] = $_POST['cDoiSfId']; //Sufijo
            $vFiltros['recidxxx'] = $mCscRec[1];        //CSC Reconocimiento
            $vFiltros['cliidxxx'] = $mCscRec[3];        //Id Cliente
            $vFiltros['recobsxx'] = $mCscRec[4]; 				//Observaciones
            $vFiltros['limpiara'] = "SI";

            $oActualizaConsecutivosActaRec = new cConsecutivoActaReconocimiento();
            $mActualizaConsecutivosActaRec = $oActualizaConsecutivosActaRec->fnActualizaConsecutivosActaReconocimiento($vFiltros);
            if ($mActualizaConsecutivosActaRec[0] == "false") {
              for ($xm = 1; $xm < count($mActualizaConsecutivosActaRec); $xm++) {
                $cMsj03 .= $mActualizaConsecutivosActaRec[$i]."\n";
              }
            }

            if($cMsj03 != "") {
              f_Mensaje(__FILE__, __LINE__, $cMsj03."Verifique.");
            }
          }
          //Fin Observacion Consecutivo Acta de Reconocimiento

          /**
           * Invocando Metodo para Eliminar Tramite de la Cabecera de Saldos de Factura si se eliminaron todos los items de la Factura
           */
          $cMsj01 = "";
          $vParametros['ADMIDXXX'] = $_POST['cAdmId'];
          $vParametros['DOIIDXXX'] = $_POST['cDoiId'];
          $vParametros['DOISFIDX'] = $_POST['cDoiSfId'];
          $vParametros['PROCESOX'] = "BORRARDO";
          $vParametros['ACCIONXX'] = "ELIMINAR";
          $objAsociarEliminarDoFactura = new cInterfacesDeclaraciones();
          $mReturnAsociarEliminarDoFactura = $objAsociarEliminarDoFactura->fnAsociarEliminarDoFactura($vParametros);
          if($mReturnAsociarEliminarDoFactura[0] == "false"){
            for($nR=1;$nR<count($mReturnAsociarEliminarDoFactura);$nR++){
              $cMsj01 .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
              $cMsj01 .= $mReturnAsociarEliminarDoFactura[$nR]."\n";
            }
            f_Mensaje(__FILE__,__LINE__,$cMsj01);
          }

          /**
           * Realizando conexion y Seleccion de Base de datos ya que al llamar los Metodos del uticimpo se pierde la conexion
           */
          $mysql->f_Conectar();
          $mysql->f_SelecDb();

          // nuevo logger
          $arLog1 = array();
          $arLog2 = array();

          $cLlavelg = $_POST['cDoiId']."=>".$_POST['cDoiSfId']."=>".$_POST['cAdmId'];
          f_DatosCambio($arLog1, $arLog2, "SIAI0200", "|DOIIDXXX=>DOISFIDX=>ADMIDXXX|", $cLlavelg, "", "DELETE", $kUser, f_Fecha(), f_Hora2(), ipCheck(), $_SERVER['PHP_SELF']);
          // Fin nuevo logger

          f_Mensaje(__FILE__, __LINE__, "El DO se Borro con Exito");
        }else {
          f_Mensaje(__FILE__, __LINE__, "DO con Levante o Finalizado o Figura en Registro VUCE, No se puede modificar");
        }
        ?>
        <form name = "frnav" action = "frdoiini.php" method = "post" target = "fmwork"></form>
        <script language = "javascript">
          document.forms['frnav'].submit();
        </script>
        <?php
      break;
    }
  }
?>

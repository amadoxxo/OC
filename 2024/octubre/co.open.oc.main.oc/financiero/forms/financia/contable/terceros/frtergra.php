<?php
  namespace openComex;
/**
 * Graba Terceros.
 * @author
 * @package opencomex
 */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors","1");

  include("../../../../libs/php/utility.php");
  include("../../../../../config/config.php");

  switch ($cAlfa) {
    case "DEROLDANLO":
    case "TEROLDANLO":
    case "ROLDANLO":
      include("../../../../../ws/roldanlo/utiwsrol.php");
      include("../../../../../ws/roldanlo/utiwsout.php");
    break;
  }

  $nSwitch = 0; // Switch para Vericar la Validacion de Datos
  $cMsj = "";
  $nCheck = "0";
  $cClivenCo = ""; //Variable que indica si el tercero tenia parametrizado la bandera de vendedor

  $cBuscar01 = array('"',chr(13),chr(10),chr(27),chr(9));
  $cReempl01 = array('\"'," "," "," "," ");

  $cBuscarEmail = array(" ",'"',chr(13),chr(10),chr(27),chr(9));
  $cReemplEmail = array("",'\"',"","","","");

  /* Validando los Checks de Aplica */
  if ($_POST['vChCliCli'] == "1") {
    $nCheck = "1";
    $cChCli = "SI";
  }else {
    $cChCli = "";
  }

  if ($_POST['vChEmp'] == "on") {
    $cChEmp = "SI";
    $nCheck = "1";
  } else {
    $cChEmp = "";
  }

  if ($_POST['vChCliVenCo'] == "on") {
    $cChCliVenCo  = "SI";
    $nCheck = "1";
  } else {
    $cChCliVenCo  = "";
  }

  if ($_POST['vChProC'] == "on") {
    $cChProC  = "SI";
    $nCheck = "1";
  } else {
    $cChProC  = "";
  }

  if ($_POST['vChProE'] == "on") {
    $cChProE  = "SI";
    $nCheck = "1";
  } else {
    $cChProE  = "";
  }

  if ($_POST['vChSoc'] == "on") {
    $cChSoc = "SI";
    $nCheck = "1";
  } else {
    $cChSoc = "";
  }

  if ($_POST['vChEfi'] == "on") {
    $cChEfi = "SI";
    $nCheck = "1";
  } else {
    $cChEfi = "";
  }

  if ($_POST['vChOtr'] == "on") {
    $cChOtr = "SI";
    $nCheck = "1";
  } else {
    $cChOtr = "";
  }

  if ($_POST['vChCon'] == "on") {
    $cChCon = "SI";
    $nCheck = "1";
  } else {
    $cChCon = "";
  }

  if ($cChProC == "" && $cChProE == "") {
   $_POST['cCliCto'] = "";
  }

  switch ($_COOKIE['kModo']) {
    case "NUEVO":
    case "EDITAR":
    case "APLICAR":
      /***** Validando Codigo *****/
      if ($_POST['cTerId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " Codigo del Tercero no puede ser vacio.\n";
      }else{
        $cTerId = trim(strtoupper($_POST['cTerId']));

        $qTerCod  = "SELECT CLIIDXXX,CLIVENCO ";
        $qTerCod .= "FROM $cAlfa.SIAI0150 WHERE CLIIDXXX = \"$cTerId\" LIMIT 0,1";
        $xTerCod  = f_MySql("SELECT","",$qTerCod,$xConexion01,"");

        switch ($_COOKIE['kModo']) {
          case "NUEVO":
            /***** Validando Codigo no exista *****/
            if (mysql_num_rows($xTerCod) > 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= " Codigo de Tercero ya existe.\n";
            }
          break;
          default:
            /***** Validando Codigo exista *****/
            if (mysql_num_rows($xTerCod) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= " Codigo de Tercero no existe.\n";
            } else {
              $xRTC = mysql_fetch_array($xTerCod);
              $cClivenCo = $xRTC['CLIVENCO'];
            }
          break;
        }
      }

      if ($_POST['cTdiId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " El Codigo del Tipo de Documento No Puede Ser Vacio.\n";
      } else {
        $qDatTdi  = "SELECT * FROM $cAlfa.fpar0109 WHERE tdiidxxx = \"{$_POST['cTdiId']}\" AND regestxx = \"ACTIVO\" ";
        $xDatTdi = f_MySql("SELECT","",$qDatTdi,$xConexion01,"");
        if (mysql_num_rows($xDatTdi) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " El Codigo del Tipo de Documento {$_POST['cTdiId']} No Existe o esta Inactivo.\n";
        }
      }

      //Para los NIT y Cedula de ciudadania el Numero de Identificacion debe ser Numerico
      //Para los demas debe ser alfanumerico, puede contener guion
      switch ($_POST['cTdiId']){
        case "31":
        case "13":
          /**
           * Validando que sea numerico
           */
          if (!preg_match("/^[[:digit:]]+$/", $_POST['cTerId'])) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " Codigo del Tercero Debe Ser Numerico.\n";
          }
          break;
        default:
          //validando que sea alfanumerico y/o tenga un guion
          if (!preg_match("/^[a-zA-Z0-9-]+$/", $_POST['cTerId'])) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " Codigo del Tercero Debe Contener Letras, Numeros y/o Guiones .\n";
          }
          break;
      }

      /* Validado  Tipo de Persona  */
      if($_POST['cTpeId'] == "NATURAL"){
        $_POST['cTerNom']  = "";
        $_POST['cTerNomC'] = "";
      } else {
        $_POST['cTerPApe'] = "";
        $_POST['cTerSApe'] = "";
        $_POST['cTerPNom'] = "";
        $_POST['cTerSNom'] = "";
      }

      /* Validado  Tipo de Persona	*/
      if (!f_InList($_POST['cTpeId'],"PUBLICA","JURIDICA","NATURAL")) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " El Tipo de Persona Debe ser PUBLICA o JURIDICA o NATURAL.\n";
      }

      if ($_POST['cTpeId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " Debe Seleccionar Tipo de Persona.\n";
      } elseif ($_POST['cTpeId'] == "NATURAL" &&
          $_POST['cTerPApe'] == "" &&
          $_POST['cTerSApe'] == "" &&
          $_POST['cTerPNom'] == "" &&
          $_POST['cTerSNom'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " Selecciono Tipo de Persona \"NATURAL\" Debe Digitar Primer Apellido, Segundo Apellido, Primer Nombre y Segundo Nombre.\n";
      }

      if($_POST['cTpeId'] == "NATURAL" &&
        ($_POST['cTerPApe'] != "" ||
        $_POST['cTerSApe'] != "" ||
        $_POST['cTerPNom'] != "" ||
        $_POST['cTerSNom'] != "")) {
        $cNombre = "";
        $cNombre .= $_POST['cTerPApe'] != "" ? $_POST['cTerPApe']." " : "";
        $cNombre .= $_POST['cTerSApe'] != "" ? $_POST['cTerSApe']." " : "";
        $cNombre .= $_POST['cTerPNom'] != "" ? $_POST['cTerPNom']." " : "";
        $cNombre .= $_POST['cTerSNom'] != "" ? $_POST['cTerSNom']." " : "";
        $cNombre  = trim($cNombre);
      }

      /* Validando que si tenia el check de vendedor activo y se deselecciono el vendedor no este asignado a otro cliente*/
      switch ($_COOKIE['kModo']) {
        case "NUEVO":
          /***** No hace nada *****/
        break;
        default:
          if ($cClivenCo == "SI" && $cChCliVenCo != "SI") {
            /* Lo esta inactivando como vendedor */
            $qCliDat  = "SELECT CLIIDXXX,CLIVENXX, IF(CLINOMXX != \"\",CLINOMXX,TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))) AS CLINOMXX, REGESTXX ";
            $qCliDat .= "FROM $cAlfa.SIAI0150 ";
            $qCliDat .= "WHERE CLIVENXX LIKE \"%$cTerId%\" AND ";
            $qCliDat .= "REGESTXX = \"ACTIVO\" ORDER BY CLINOMXX";
            $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
            $cClientes = "";
            while ($xRCD = mysql_fetch_array($xCliDat)){
              $mVendedores = array();
              $mVendedores = explode("~",$xRCD['CLIVENXX']);
              for ($i=0; $i<count($mVendedores); $i++) {
                if ($mVendedores[$i] == $cTerId) {
                  $cClientes .= "[{$xRCD['CLIIDXXX']}] {$xRCD['CLINOMXX']}.\n";
                }
              }
            }
            if ($cClientes != "") {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= " No Puede Desactivar la Clasificacion VENDEDOR, el Tercero esta asignado como vendedor al (los) Tercero(s): \n$cClientes";
            }
          }
        break;
      }

      if($_POST['cTpeId'] == "JURIDICA" || $_POST['cTpeId'] == "PUBLICA" ){
        if($_POST['cTerNom'] == "" ||
          $_POST['cTerNomC'] == ""){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " Debe Digitar Razon Social Y Nombre Comercial.\n";
        }//if($_POST['cTerNom'] == "" || $_POST['cTerNomC'] == ""){
      }//if($_POST['cTpeId'] == "JURIDICA" || $_POST['cTpeId'] == "PUBLICA" ){

      /*validando Pais*/
      if($_POST['cPaiId'] == ""){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " Debe Seleccionar Pais para el Domicilio Fiscal.\n";
      } else {
        //Validando que el pais digitado exista
        $qPais  = "SELECT PAIIDXXX ";
        $qPais .= "FROM $cAlfa.SIAI0052 ";
        $qPais .= "WHERE ";
        $qPais .= "PAIIDXXX = \"{$_POST['cPaiId']}\" AND ";
        $qPais .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
        $xPais  = f_MySql("SELECT","",$qPais,$xConexion01,"");
        if (mysql_num_rows($xPais) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " El Pais {$_POST['cPaiId']} de Domicilio Fiscal No Existe o esta Inactivo.\n";
        }
      }

      if($_POST['cPaiId'] == "CO"){
        if($_POST['cDepId'] == ""){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " Debe Seleccionar Departamento para el Domicilio Fiscal.\n";
        }

        if($_POST['cCiuId'] == ""){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " Debe Seleccionar La Ciudad para el Domicilio Fiscal.\n";
        }
      }

      if($_POST['cDepId'] != ""){
        $qDpto  = "SELECT PAIIDXXX,DEPIDXXX ";
        $qDpto .= "FROM $cAlfa.SIAI0054 ";
        $qDpto .= "WHERE ";
        $qDpto .= "PAIIDXXX = \"{$_POST['cPaiId']}\" AND ";
        $qDpto .= "DEPIDXXX = \"{$_POST['cDepId']}\" AND ";
        $qDpto .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
        $xDpto  = f_MySql("SELECT","",$qDpto,$xConexion01,"");
        if (mysql_num_rows($xDpto) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " El Departamento {$_POST['cDepId']} de Domicilio Fiscal No Existe o esta Inactivo.\n";
        }
      }

      if($_POST['cCiuId'] != ""){
        //Validando Ciudad
        $qCiudad  = "SELECT PAIIDXXX,DEPIDXXX,CIUIDXXX ";
        $qCiudad .= "FROM $cAlfa.SIAI0055 ";
        $qCiudad .= "WHERE ";
        $qCiudad .= "PAIIDXXX = \"{$_POST['cPaiId']}\" AND ";
        $qCiudad .= "DEPIDXXX = \"{$_POST['cDepId']}\" AND ";
        $qCiudad .= "CIUIDXXX = \"{$_POST['cCiuId']}\" AND ";
        $qCiudad .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
        $xCiudad  = f_MySql("SELECT","",$qCiudad,$xConexion01,"");
        if (mysql_num_rows($xCiudad) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " La Ciudad {$_POST['cCiuId']} de Domicilio Fiscal No Existe o esta Inactivo.\n";
        }
      }

      /*validando Domicilio Fiscal*/
      if($_POST['cTerDir'] == ""){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " Debe Digitar la Direccion del Domicilio Fiscal.\n";
      }

      /*Validando Telefono del Tercero*/
      if($_POST['cTerTel'] == ""){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " Debe Digitar Un Numero de Telefono.\n";
      }

      /*validando Pais Direccion Correspondencia*/
      if($_POST['cPaiId1'] == ""){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " Debe Seleccionar Pais para Correspondencia.\n";
      } else {
        //Validando que el pais digitado exista
        $qPais  = "SELECT PAIIDXXX ";
        $qPais .= "FROM $cAlfa.SIAI0052 ";
        $qPais .= "WHERE ";
        $qPais .= "PAIIDXXX = \"{$_POST['cPaiId1']}\" AND ";
        $qPais .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
        $xPais  = f_MySql("SELECT","",$qPais,$xConexion01,"");
        if (mysql_num_rows($xPais) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " El Pais {$_POST['cPaiId']} de Correspondencia No Existe o esta Inactivo.\n";
        }
      }

      if($_POST['cPaiId1'] == "CO"){
        if($_POST['cDepId1'] == ""){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " Debe Seleccionar Departamento para la Correspondencia.\n";
        }

        if($_POST['cCiuId1'] == ""){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " Debe Seleccionar Ciudad para la Correspondencia.\n";
        }
      }

      if($_POST['cDepId1'] != ""){
        $qDpto  = "SELECT PAIIDXXX,DEPIDXXX ";
        $qDpto .= "FROM $cAlfa.SIAI0054 ";
        $qDpto .= "WHERE ";
        $qDpto .= "PAIIDXXX = \"{$_POST['cPaiId1']}\" AND ";
        $qDpto .= "DEPIDXXX = \"{$_POST['cDepId1']}\" AND ";
        $qDpto .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
        $xDpto  = f_MySql("SELECT","",$qDpto,$xConexion01,"");
        if (mysql_num_rows($xDpto) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " El Departamento {$_POST['cDepId1']} de Correspondencia No Existe o esta Inactivo.\n";
        }
      }

      if($_POST['cCiuId1'] != ""){
        //Validando Ciudad
        $qCiudad  = "SELECT PAIIDXXX,DEPIDXXX,CIUIDXXX ";
        $qCiudad .= "FROM $cAlfa.SIAI0055 ";
        $qCiudad .= "WHERE ";
        $qCiudad .= "PAIIDXXX = \"{$_POST['cPaiId1']}\" AND ";
        $qCiudad .= "DEPIDXXX = \"{$_POST['cDepId1']}\" AND ";
        $qCiudad .= "CIUIDXXX = \"{$_POST['cCiuId1']}\" AND ";
        $qCiudad .= "REGESTXX = \"ACTIVO\" LIMIT 0,1 ";
        $xCiudad  = f_MySql("SELECT","",$qCiudad,$xConexion01,"");
        if (mysql_num_rows($xCiudad) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " La Ciudad {$_POST['cCiuId1']} de Correspondencia No Existe o esta Inactivo.\n";
        }
      }

      /*validando Direccion Correspondencia*/
      if($_POST['cTerDirC'] == ""){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " Debe Digitar la Direccion de Correspondencia.\n";
      }

      if($nCheck == "0"){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " Falta Clasificacion del Tercero.\n";
      }

      for ($i=0;$i<$_POST['nSecuencia'];$i++) {
        // f_Mensaje(__FILE__,__LINE__,$_POST['cReqNro'.(2)]);
        /***** Validando que no Hayan DOs en Vacio *****/
        if (empty($_POST['cReqNro'.($i+1)])) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " En la Relacion de Documentos Requisitos Legales.\n";

          if (($_POST['cReqNro'.($i+1)] == '001' ||
            $_POST['cReqNro'.($i+1)] == '002' ||
            $_POST['cReqNro'.($i+1)] == '003')&& (($_POST['cReqFec'.($i+1)]) == '0000-00-00')) {
            //if (empty($_POST['cReqFec'.($i+1)])) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " En la Relacion de Documentos Requisitos Legales La Fecha en la Secuencia ".($i+1)." no puede ser vacia.\n";
          }
        }
      }
      #Validaciones Condiciones Tributarias

      #Validando Responsable de IVA
      if($_POST['oCliReIva'] == 'SI') {
        if($_POST['oCliReg'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " Debe Seleccionar Si el Responsable del IVA es Regimen Comun o Simplificado.\n";
        } else {
          if($_POST['oCliReg'] == "COMUN") {
            $_POST['oCliReIva'] = "SI";
            $cCliReCom = "SI";
            $cCliReSim = "NO";
          } elseif($_POST['oCliReg'] == "SIMPLIFICADO") {
            $_POST['oCliReIva'] = "SI";
            $cCliReCom = "NO";
            $cCliReSim = "SI";
          } else {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " Debe Seleccionar Si el Responsable del IVA es Regimen Comun o Simplificado.\n";
          }
        }
      } else {
        $_POST['oCliReIva'] = "NO";
        $cCliReCom = "NO";
        $cCliReSim = "NO";
      }

      #Validando Gran Contribuyebte
      if ($_POST['oCliGc'] == 'SI') {
        $_POST['oCliGc'] = "SI";
      } else {
        $_POST['oCliGc'] = "NO";
      }

      #Validando No Residente en el Pais
      if ($_POST['oCliNrp'] == 'SI') {
        $_POST['oCliNrp']    = "SI";
        $_POST['oCliNrpai']  = ($_POST['oCliNrpai'] == 'SI') ? "SI" : "NO";
        $_POST['oCliNrpif']  = ($_POST['oCliNrpif'] == 'SI') ? "SI" : "NO";
        $_POST['oCliNrpNsr'] = ($_POST['oCliNrpNsr'] == 'SI')? "SI" : "NO";
      } else {
        $_POST['oCliNrp']    = "NO";
        $_POST['oCliNrpai']  = "NO";
        $_POST['oCliNrpif']  = "NO";
        $_POST['oCliNrpNsr'] = "NO";
      }

      #Validando Autorretenedor
      if ($_POST['oCliAr'] == 'SI') {
        $_POST['oCliAr'] = "SI";
        $_POST['oCliArAre'] = ($_POST['oCliArAre'] == 'SI')?"SI":"NO";
        $_POST['oCliArAiv'] = ($_POST['oCliArAiv'] == 'SI')?"SI":"NO";
        $_POST['oCliArAic'] = ($_POST['oCliArAic'] == 'SI')?"SI":"NO";
        $_POST['oCliArAcr'] = ($_POST['oCliArAcr'] == 'SI')?"SI":"NO";
        $_POST['cCliArAis'] = ($_POST['oCliArAic'] == 'SI')?$_POST['cCliArAis']:"";
        #Validando que selecciono al menos una sucursal
        if($_POST['oCliArAic'] == "SI"){
          $mCliArAis = explode("~",$_POST['cCliArAis']);
          $nCon = 0;
          $cCliArAis = "";
          for($i=0; $i<count($mCliArAis);$i++) {
            if ($mCliArAis[$i] != "") {
              $cCliArAis .= $mCliArAis[$i]."~";
              $nCon++;
            }
          }
          $cCliArAis = substr($cCliArAis,0,strlen($cCliArAis)-1);
          if($nCon == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " Debe Seleccionar las ICA x Sucursales del Autorretenedor.\n";
          }
        }
      } else {
        $_POST['oCliAr']    = "NO";
        $_POST['oCliArAre'] = "NO";
        $_POST['oCliArAiv'] = "NO";
        $_POST['oCliArAic'] = "NO";
        $_POST['oCliArAcr'] = "NO";
        $cCliArAis = "";
      }

      #Validando No Sujeto RETEFTE Renta
      if ($_POST['oCliNsrr'] == 'SI') {
        $_POST['oCliNsrr'] = "SI";
      } else {
        $_POST['oCliNsrr']   = "NO";
      }

      #Validando No Sujeto RETEFTE por IVA
      if ($_POST['oCliNsriv'] == 'SI') {
        $_POST['oCliNsriv'] = "SI";
      } else {
        $_POST['oCliNsriv']   = "NO";
      }

      #Validando No Sujeto de Retencion ICA
      if ($_POST['oCliNsrri'] == 'SI') {
        $_POST['oCliNsrri'] = "SI";
      } else {
        $_POST['oCliNsrri']   = "NO";
      }

      #Validando No Sujeto Retencion CREE
      if ($_POST['oCliNsrcr'] == 'SI') {
        $_POST['oCliNsrcr'] = "SI";
      } else {
        $_POST['oCliNsrcr']   = "NO";
      }

      #Validando Agente Retenedor Renta
      if ($_POST['oCliArr'] == 'SI') {
        $_POST['oCliArr'] = "SI";
      } else {
        $_POST['oCliArr']   = "NO";
      }

      #Validando Agente Retenedor en IVA
      if ($_POST['oCliAriva'] == 'SI') {
        $_POST['oCliAriva'] = "SI";
      } else {
        $_POST['oCliAriva']   = "NO";
      }

      #Validando Agente Retenedor CREE
      if ($_POST['oCliArcr'] == 'SI') {
        $_POST['oCliArcr'] = "SI";
      } else {
        $_POST['oCliArcr']   = "NO";
      }

      #Agente Retenedor ICA en
      if ($_POST['oCliArrI'] == 'SI') {
        #Validando que selecciono al menos una sucursal
        $mCliArrIs = explode("~",$_POST['cCliArrIs']);
        $nCon = 0;
        $cCliArrIs = "";
        for($i=0; $i<count($mCliArrIs);$i++) {
          if ($mCliArrIs[$i] != "") {
            $cCliArrIs .= $mCliArrIs[$i]."~";
            $nCon++;
          }
        }
        $cCliArrIs = substr($cCliArrIs,0,strlen($cCliArrIs)-1);
        if($nCon == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " Debe Seleccionar las ICA x Sucursales del Agente Retenedor ICA en.\n";
        }
      }else {
        $_POST['oCliArrI']  = "NO";
        $_POST['cCliArrIs'] = "";
        $cCliArrIs = "";
      }

      #No Sujeto a Retención ICA en
      if ($_POST['oCliNsrri'] == 'SI') {
        #Validando que selecciono al menos una sucursal
        $mCliNsrris = explode("~",$_POST['cCliNsrris']);
        $nCon = 0;
        $cCliNsrris = "";
        for($i=0; $i<count($mCliNsrris);$i++) {
          if ($mCliNsrris[$i] != "") {
            $cCliNsrris .= $mCliNsrris[$i]."~";
            $nCon++;
          }
        }
        $cCliNsrris = substr($cCliNsrris,0,strlen($cCliNsrris)-1);
        if($nCon == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " Debe Seleccionar las ICA x Sucursales del No Sujeto a Retención ICA en.\n";
        }
      }else {
        $_POST['oCliNsrri']  = "NO";
        $_POST['cCliNsrris'] = "";
        $cCliNsrris = "";
      }

      #Validando Proveedor Comercializadora Internacional
      if ($_POST['oCliPci'] == 'SI') {
        $_POST['oCliPci'] = "SI";
      } else {
        $_POST['oCliPci'] = "NO";
      }

      #Validando No Sujeto a Expedir Factura de Venta o Documento Equivalente
      if ($_POST['oCliNsOfe'] == 'SI') {
        $_POST['oCliNsOfe'] = "SI";
      } else {
        $_POST['oCliNsOfe'] = "NO";
      }

      /***
       * Validaciones de agrupamiento de las Condiciones Tributarias
       */

      /**
       * Si se Activa Responsable IVA - Regimen Simplificado no debe permitir que se active el Check de
       * Autorretenedor,
       * No Residente en el Pais,
       * Agente Retenedor de Renta,
       * Agente Retenedor de IVA,
       * Agente Retenedor CREE,
       * Agente Retenedor de Ica y
       * Proveedor Comercializadora Internacional.
       */

      if($_POST['oCliReg']   == "SIMPLIFICADO" &&
         ($_POST['oCliAr']   == 'SI' ||
          $_POST['oCliNrp']  == 'SI' ||
          $_POST['oCliArr']  == 'SI' ||
          $_POST['oCliAriva']== 'SI' ||
          //$_POST['oCliArcr'] == 'SI' ||
          $_POST['oCliArrI'] == 'SI'||
          $_POST['oCliPci']  == 'SI')) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " Si Marco que Aplica Responsable IVA - Regimen Simplificado, no Debe Seleccionar que Aplica Como Auterretenedor, ";
        $cMsj .= "No Residente en el Pais, ";
        $cMsj .= "Agente Retenedor en Renta, ";
        $cMsj .= "Agente Retenedor en IVA, ";
        $cMsj .= "Agente Retenedor CREE, ";
        $cMsj .= "Agente Retenedor de Ica en o ";
        $cMsj .= "Proveedor Comercializadora Internacional.\n";
      }

      /**
       * Si se Activa Regimen Simple Tributario no debe permitir que se active el Check de
       * Gran Contribuyente,
       * No Sujeto RETEFTE por Renta y
       * Agente Retenedor de Renta
       */

      if($_POST['oCliRegST']   == "SI" &&
          ($_POST['oCliGc']  == 'SI' ||
          $_POST['oCliNsrr'] == 'SI' ||
          $_POST['oCliArr']  == 'SI' )) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " Si Marco que Regimen Simple Tributario, no Debe Seleccionar que Aplica Como ";
        $cMsj .= "Gran Contribuyente, ";
        $cMsj .= "No Sujeto RETEFTE por Renta, ";
        $cMsj .= "Agente Retenedor en Renta.\n";
      }

      /**
       * Si se Activa Gran Contribuyente debe obligar a marcar el Campo Responsable IVA - Regimen Comun
       */
      if($_POST['oCliGc'] == 'SI' && $_POST['oCliReg'] != "COMUN") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " Si Marco que Aplica Como Gran Contribuyente debe Seleccionar que Aplica como Responsable IVA - Regimen Comun.\n";
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
      if($_POST['oCliNrp']    == 'SI' &&
          ($_POST['oCliReIva'] == 'SI' ||
          $_POST['oCliGc']    == 'SI' ||
          $_POST['oCliAr']    == 'SI' ||
          $_POST['oCliNsrr']  == 'SI' ||
          $_POST['oCliNsriv'] == 'SI' ||
          $_POST['oCliNsrri'] == 'SI' ||
          $_POST['oCliNsrcr'] == 'SI' ||
          $_POST['oCliArr']   == 'SI' ||
          $_POST['oCliAriva'] == 'SI' ||
          $_POST['oCliArcr']  == 'SI' ||
          $_POST['oCliArrI']  == 'SI' ||
          $_POST['oCliPci']   == 'SI')) {
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " Si Marco que Aplica No Residente en el Pais, no Debe Seleccionar que Aplica Como ";
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
       * Si se Activa Proveedor Comercializadora Internacional debe estar activo Responsable IVA - Regimen Comun.
       */
      if($_POST['oCliPci'] == 'SI' && $_POST['oCliReg'] != "COMUN") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= " Si Marco que Aplica Como Proveedor Comercializadora Internacional debe Seleccionar que Aplica como Responsable IVA - Regimen Comun.\n";
      }

      if (!f_InList($cAlfa,"TEUPSXXXXX","UPSXXXXX","DEUPSXXXXX")) {
        $_POST['cCliIdCor'] = "";
        $_POST['cCliIdProv'] = "";
        $_POST['cCliIdApr'] = "";
      }

      /**
       * Si se digito tasa pactada debe validarse que se seleccione el concepto de la tasa pactada y viceversa
       */

      if(($_POST['cCliTp']+0) > 0 && $_POST['cCliTpCto'] != "") {
        /*Valido que el concepto exista*/
        $qDatTp = "SELECT pucidxxx, ctoidxxx ";
        $qDatTp.= "FROM $cAlfa.fpar0119 ";
        $qDatTp.= "WHERE ctoidxxx =\"{$_POST['cCliTpCto']}\" AND ";
        $qDatTp.= "regestxx =\"ACTIVO\" LIMIT 0,1";
        $xDatTp = f_MySql("SELECT","",$qDatTp,$xConexion01,"");
        if (mysql_num_rows($xDatTp) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " El Concepto de cuenta corriente tasa de cambio fija no Existe o se encuentra en estado INACTIVO.\n";
        }
      } else {

        if(($_POST['cCliTp']+0) > 0 && $_POST['cCliTpCto'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " Digito el Valor de la Tasa de cambio fija para causacion de proveedores debe seleccionar el Concepto de cuenta corriente tasa de cambio fija.\n";
        }

        if(($_POST['cCliTp']+0) == 0 && $_POST['cCliTpCto'] != "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " Selecciono el Concepto de cuenta corriente tasa de cambio fija debe digitar el Valor de la Tasa de cambio fija para causacion de proveedores.\n";
        }
      }

      #Validando Datos Adicionales UPS (Solo aplica para UPS)

      switch ($cAlfa) {
        case "UPSXXXXX":
        case "DEUPSXXXXX":
        case "TEUPSXXXXX":
          // Valdando Nit de Identificacion Corporativo UPS
          if(trim($_POST['cCliIdCor']) == "") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " Debe Digitar el Nit de Identificaci&oacute;n Corporativo UPS.\n";
          }

          // Validando ID Corporativo del Proveedor
          if(trim($_POST['cCliIdProv']) == "") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " Debe Digitar el ID Corporativo del Proveedor.\n";
          }

          // Validando ID Adicional del Proveedor
          if(trim($_POST['cCliIdApr']) == "") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " Debe Digitar el ID Adicional del Proveedor.\n";
          }
        break;
        default:
          // Para otras bases de datos no valida (No Aplica)
        break;
      }

      ## Validaciones respectivas al Código SAP ##
      switch ($cAlfa) {
        case "TEALMACAFE":
        case "DEALMACAFE":
        case "ALMACAFE":
        case "DEALPOPULX":
        case "TEALPOPULP":
        case "ALPOPULX":
        case "DEALMAVIVA":
        case "TEALMAVIVA":
        case "ALMAVIVA":
          if (trim($_POST['cCliSap']) == "") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " Debe Digitar el C&oacute;digo SAP.\n";
          }
        break;
        default:
          $_POST['cCliSap'] = "";
        break;
      }
      ## FIN validaciones al Código SAP ##

      ## Validando que la Responsabilidad Fiscal exista en la Base de Datos y no este Inactiva ##
      if(trim($_POST['cCliResFi']) != ""){
        $vCliResFis = explode("~", $_POST['cCliResFi']);

        for($i=0; $i<count($vCliResFis); $i++){
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
            $cMsj .= " La Responsabilidad Fiscal[".$vCliResFis[$i]."], No Existe en la Base de Datos.\n";
          }elseif($vResFis['regestxx'] == "INACTIVO"){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " La Responsabilidad Fiscal[".$vCliResFis[$i]."], se Encuentra INACTIVA.\n";
          }
        }
      }
      ## FIN Validación Responsabilidad Fiscal ##

      ## Validando que el Tributo exista en la Base de Datos y no este Inactivo ##
      if(trim($_POST['cCliTri']) != ""){
        $vCliTribu = explode("~", $_POST['cCliTri']);

        for($i=0; $i<count($vCliTribu); $i++){
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
            $cMsj .= " El Tributo[".$vCliTribu[$i]."], No Existe en la Base de Datos.\n";
          }elseif($vTributo['regestxx'] == "INACTIVO"){
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " El Tributo[".$vCliTribu[$i]."], se Encuentra INACTIVO.\n";
          }
        }
      }
      ## FIN Validación Tributo ##

      if($vSysStr['system_activar_openetl'] == 'SI') {
        if(trim($_POST['cCliPCECn']) == ""){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " Correos Notificacion No Puede Ser Vacio.\n";
        }
      }

      if(trim($_POST['cCliPCECn']) != "") {
        $vCorreos = explode(",", $_POST['cCliPCECn']);
        for ($i=0; $i < count($vCorreos); $i++) { 
          $vCorreos[$i] = trim($vCorreos[$i]);
          if($vCorreos[$i] != ""){
            if (!filter_var($vCorreos[$i], FILTER_VALIDATE_EMAIL)) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= " El Correo Notificacion[".$vCorreos[$i]."], No es Valido.\n";
            }
          }
        }
      }

      /**
       * Valido que si se asignan vendedores, eston existan y esten marcados como vendedores
       */
      if($_POST['cCliVen'] != "" ){
        $vVendedores = explode(",",$_POST['cCliVen']);
        $_POST['cCliVen'] = "";
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
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " El Vendedor[{$vVendedores[$nI]}] no Existe, Esta Inactivo o no Esta Parametrizado como Vendedor.\n";
          }else{
            if($vVendedores[$nI] != $cTerId){
              $_POST['cCliVen'] .= $vVendedores[$nI]."~";
            }else{
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= " El Tercero No se puede asignar a si mismo como Vendedor.\n";
            }
          }
        }
        $_POST['cCliVen'] = substr($_POST['cCliVen'], 0, -1);
      }

      if($_POST['cTerId'] == $vSysStr['siacosia_importador_hp_colombia']){ 
        if(trim($_POST['cCliHPCnx']) == ""){
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= " Correos Notificacion Cliente HP No Puede Ser Vacio.\n";
        }else{
          $vCorreos = explode(",", $_POST['cCliHPCnx']);
          for ($i=0; $i < count($vCorreos); $i++) { 
            $vCorreos[$i] = trim($vCorreos[$i]);
            if($vCorreos[$i] != ""){
              if (!filter_var($vCorreos[$i], FILTER_VALIDATE_EMAIL)) {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= " El Correo Notificacion[".$vCorreos[$i]."] para el Cliente HP, No es Valido.\n";
              }
            }
          }
        }
      }

      if(trim($_POST['cCliCnrRf']) != ""){
        $vCorreos = explode(",", $_POST['cCliCnrRf']);
        for ($i=0; $i < count($vCorreos); $i++) { 
          $vCorreos[$i] = trim($vCorreos[$i]);
          if($vCorreos[$i] != ""){
            if (!filter_var($vCorreos[$i], FILTER_VALIDATE_EMAIL)) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= " El Correo Notificacion Rechazo Revisor Fiscal[".$vCorreos[$i]."], No es Valido.\n";
            }
          }
        }
      }

      //Validar que los campos de correos del facturador electronico sean validos
      if (trim($_POST['cCliCoEmi'] != '')) {
				if (!filter_var($_POST['cCliCoEmi'], FILTER_VALIDATE_EMAIL)) {
					$nSwitch = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= " El Correo de Emision Facturador Electronico[".$_POST['cCliCoEmi']."], No es Valido.\n";
				}
			}

			if (trim($_POST['cCliCoRep'] != '')) {
				if (!filter_var($_POST['cCliCoRep'], FILTER_VALIDATE_EMAIL)) {
					$nSwitch = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= " El Correo de Recepcion Facturador Electronico[".$_POST['cCliCoRep']."], No es Valido.\n";
				}
			}

      $cCliImpCs = "";
      $CliImpCr  = "";
      switch ($cAlfa) {
        case "TEDHLEXPRE":
        case "DEDHLEXPRE":
        case "DHLEXPRE":
          // Validacion Cuenta IMP
          for ($i=0;$i<$_POST['nSecuencia_Grid_ImpCash'];$i++) {
            if ($_POST['cCuentaGrid_ImpCash' . ($i+1)] != "" && $_POST['cEstadoGrid_ImpCash' . ($i+1)]) {
              $cCliImpCs .= $_POST['cCuentaGrid_ImpCash' . ($i+1)]."~".$_POST['cEstadoGrid_ImpCash' . ($i+1)]."|";
            }
          }

          for ($i=0;$i<$_POST['nSecuencia_Grid_ImpCre'];$i++) {
            if ($_POST['cCuentaGrid_ImpCre' . ($i+1)] != "" && $_POST['cEstadoGrid_ImpCre' . ($i+1)]) {
              $CliImpCr .= $_POST['cCuentaGrid_ImpCre' . ($i+1)]."~".$_POST['cEstadoGrid_ImpCre' . ($i+1)]."|";
            }
          }

          // Se validan los campos de la cuenta
          $cCuenErr = false;
          if ($_POST['cBanId'] != '' && ($_POST['cTipCta'] == '' || $_POST['cBanCta'] == '' || $_POST['cEstCta'] == '')) {
            $cCuenErr = true;
          } else if ($_POST['cTipCta'] != '' && ($_POST['cBanId'] == '' || $_POST['cBanCta'] == '' || $_POST['cEstCta'] == '')) {
					  $cCuenErr = true;
          } else if ($_POST['cBanCta'] != '' && ($_POST['cTipCta'] == '' || $_POST['cBanId'] == '' || $_POST['cEstCta'] == '')) {
					  $cCuenErr = true;
          } else if ($_POST['cEstCta'] != '' && ($_POST['cTipCta'] == '' || $_POST['cBanCta'] == '' || $_POST['cBanId'] == '')) {
					  $cCuenErr = true;
          }

          if ($cCuenErr) {
					  $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " Se Deben Diligenciar Todos los Campos de Cuenta, \n";
          }

          if($_POST['cBanId'] != '') {
            $qBanDes  = "SELECT ";
            $qBanDes .= "banidxxx ";
            $qBanDes .= "FROM $cAlfa.fpar0124 ";
            $qBanDes .= "WHERE ";
            $qBanDes .= "banidxxx = \"{$_POST['cBanId']}\" AND ";
            $qBanDes .= "regestxx = \"ACTIVO\" LIMIT 0,1";
            $xBanDes  = f_MySql("SELECT","",$qBanDes,$xConexion01,"");
            if (mysql_num_rows($xBanDes) === 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= " La Cuenta [{$_POST['cBanId']}] No Existe o Se Encuentra INACTIVA.\n";
            }
          }

          // Para los terceros marcados como clientes se valida que se digite cuenta IMP Cash
          // Si se digita cuenta IMP Cash esta debe ser DUTYCOADA
          for ($i=0;$i<$_POST['nSecuencia_Grid_ImpCash'];$i++) {
            if($cChCli == "SI") {
              if ($_POST['cCuentaGrid_ImpCash' . ($i+1)] == "") {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= " La Cuenta IMP Cash No Puede Ser Vacia.\n";
              }
            } 
            if ($_POST['cCuentaGrid_ImpCash' . ($i+1)] != "" && $_POST['cCuentaGrid_ImpCash' . ($i+1)] != "DUTYCOADA") {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= " Solo se Permite la Cuenta IMP Cash DUTYCOADA.\n";
            }
          }
            
          // Validacion de URL chat Privado
          // Si existe contenido se debe haber seleccionado la clasificacion de vendedor
          if($_POST['cCliCecNc'] !== ""){
            if($cChCliVenCo == ""){
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= " Para indicar URL Chat Privado, el Tercero debe Estar Clasificado como Vendedor.\n";
            }
          }
        break;
        case "TESIACOSIP":
        case "DESIACOSIP":
        case "SIACOSIA":
          // Valida los correos de notificacion para Bavaria
          if(trim($_POST['cCliBavCn']) != "") {
            $vCorreos = explode(",", $_POST['cCliBavCn']);
            for ($i=0; $i < count($vCorreos); $i++) { 
              $vCorreos[$i] = trim($vCorreos[$i]);
              if($vCorreos[$i] != ""){
                if (!filter_var($vCorreos[$i], FILTER_VALIDATE_EMAIL)) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= " El Correo Notificacion BAVARIA[".$vCorreos[$i]."], No es Valido.\n";
                }
              }
            }
          }
        break;
        case 'INTERLO2':
        case 'DEINTERLO2':
        case 'TEINTERLO2':
        /**
          * Valida que se seleccione al menos un vendedor.
          */ 
          if (($cChCli == "SI" || $cChProC == "SI" || $cChProE == "SI") && $_POST['cCliVen'] == "") {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= " Debe Seleccionar al Menos un Vendedor.\n";
          }
        break;
        default:
          // No hace nada
        break;
      }
      #Fin Validacion Cuenta IMP

    break;
    case "ANULAR";
      /**
       * Validando el Estado del Tercero.
       */
      if (!f_InList($_POST['cCliEst'],"ACTIVO","INACTIVO")) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
        $cMsj .= " El Estado Debe ser Inactivo o Activo.\n";
      }

      if ($_POST['cCliEst'] == "ACTIVO") {
        /** Validando que el Tercero no tenga movimiento contable * */
        $rRet = f_Bloquear_Importador("", "", "", $_POST['cTerId']);
        if ($rRet == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
          $cMsj .= " EL Tercero [{$_POST['cTerId']}] No se Puede Inactivar Porque Tiene Movimiento Contable.\n";
        }
      }
    break;
  }/***** Fin de la Validacion *****/

  /***** Ahora Empiezo a Grabar *****/
  /***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/


  if ($nSwitch == "0") {
    //// Las Observaciones ////
    $cTerObs = str_replace(array(chr(27),chr(9),chr(13),chr(10))," ",$_POST['cTerObs']);
    $cTerObs = str_replace("  "," ",$cTerObs);
    $cTerObs = str_replace("  "," ",$cTerObs);

    switch ($_COOKIE['kModo']) {
      case "NUEVO":

        $qInsert =  array(array('NAME'=>'CLIIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerId'])),'"'))     ,'CHECK'=>'SI'),
                          array('NAME'=>'CLINOMXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper(($_POST['cTpeId'] == "NATURAL" ? $cNombre: $_POST['cTerNom']))),'"'))    ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIAPE1X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerPApe'])),'"'))   ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIAPE2X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerSApe'])),'"'))   ,'CHECK'=>'NO'),
                          array('NAME'=>'CLINOM1X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerPNom'])),'"'))   ,'CHECK'=>'NO'),
                          array('NAME'=>'CLINOM2X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerSNom'])),'"'))   ,'CHECK'=>'NO'),
                          array('NAME'=>'CLICLIXX','VALUE'=>$cChCli                                                                             ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIEMPXX','VALUE'=>$cChEmp                                                                             ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIVENCO','VALUE'=>$cChCliVenCo                                                                        ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIPROCX','VALUE'=>$cChProC                                                                            ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIPROEX','VALUE'=>$cChProE                                                                            ,'CHECK'=>'NO'),
                          array('NAME'=>'CLISOCXX','VALUE'=>$cChSoc                                                                             ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIEFIXX','VALUE'=>$cChEfi                                                                             ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIOTRXX','VALUE'=>$cChOtr                                                                             ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIDIRXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerDir'])),'"'))    ,'CHECK'=>'SI'),
                          array('NAME'=>'CLICPOSX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerCPosF'])),'"'))  ,'CHECK'=>'NO'),                          
                          array('NAME'=>'CLITELXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerTel'])),'"'))    ,'CHECK'=>'SI'),
                          array('NAME'=>'CLIFAXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerFax'])),'"'))    ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIEMAXX','VALUE'=>str_replace($cBuscarEmail,$cReemplEmail,trim(trim($_POST['cTerEma']),'"'))          ,'CHECK'=>'NO','CS'=>'NONE'),
                          array('NAME'=>'CLITPERX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTpeId'])),'"'))     ,'CHECK'=>'SI'),
                          array('NAME'=>'CLINOMCX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerNomC'])),'"'))   ,'CHECK'=>'NO'),
                          array('NAME'=>'TDIIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTdiId'])),'"'))     ,'CHECK'=>'NO'),
                          array('NAME'=>'PAIIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cPaiId'])),'"'))     ,'CHECK'=>'SI'),
                          array('NAME'=>'DEPIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cDepId'])),'"'))     ,'CHECK'=>'NO'),
                          array('NAME'=>'CIUIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCiuId'])),'"'))     ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIAPAXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerApar'])),'"'))   ,'CHECK'=>'NO'),
                          array('NAME'=>'GRUIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cGruId'])),'"'))     ,'CHECK'=>'NO'),
                          array('NAME'=>'PAIID3XX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cPaiId1'])),'"'))    ,'CHECK'=>'SI'),
                          array('NAME'=>'DEPID3XX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cDepId1'])),'"'))    ,'CHECK'=>'NO'),
                          array('NAME'=>'CIUID3XX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCiuId1'])),'"'))    ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIDIR3X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerDirC'])),'"'))   ,'CHECK'=>'SI'),
                          array('NAME'=>'CLICPOS3','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerCPosC'])),'"'))  ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIFORPX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerFPa'])),'"'))    ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIPLAXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerPla'])),'"'))    ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIMEDPX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerMedP'])),'"'))   ,'CHECK'=>'NO'),
                          array('NAME'=>'CLICUEBA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliCueBa'])),'"'))  ,'CHECK'=>'NO'),
                          array('NAME'=>'AECIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cAecId'])),'"'))     ,'CHECK'=>'NO'),
                          array('NAME'=>'CLICIRXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['dCir'])),'"'))       ,'CHECK'=>'NO'),
                          array('NAME'=>'CLICAMCX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['dCamC'])),'"'))      ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIRUTXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['dRut'])),'"'))       ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIUAPXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerUapco'])),'"'))  ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIALTEX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerAltex'])),'"'))  ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIOBSXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerObs'])),'"'))    ,'CHECK'=>'NO'),
                          array('NAME'=>'CLITCONX','VALUE'=>$cChCon                                                                             ,'CHECK'=>'NO'),
                          array('NAME'=>'CLIREIVA','VALUE'=>trim(strtoupper($_POST['oCliReIva']))                                               ,'CHECK'=>'NO'), //Responsable de IVA
                          array('NAME'=>'CLIRECOM','VALUE'=>$cCliReCom                                                                          ,'CHECK'=>'NO'), //Regimen Comun
                          array('NAME'=>'CLIRESIM','VALUE'=>$cCliReSim                                                                          ,'CHECK'=>'NO'), //Regimen Simplificado
                          array('NAME'=>'CLIREGST','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliRegST'])),'"'))  ,'CHECK'=>'NO'), //Regimen Simple Tributacion
                          array('NAME'=>'CLIGCXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliGc'])),'"'))     ,'CHECK'=>'NO'), //Gran Contribuyente
                          array('NAME'=>'CLINRPXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNrp'])),'"'))    ,'CHECK'=>'NO'), //No Residente en el Pais
                          array('NAME'=>'CLINRPAI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNrpai'])),'"'))  ,'CHECK'=>'NO'), //Aplica IVA No Residentes
                          array('NAME'=>'CLINRPIF','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNrpif'])),'"'))  ,'CHECK'=>'NO'), //Aplica Gravamen Financiero No Residentes
                          array('NAME'=>'CLINRNSR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNrpNsr'])),'"')) ,'CHECK'=>'NO'), //No Sujeto RETEFTE por Renta No Residentes
                          array('NAME'=>'CLIARXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliAr'])),'"'))     ,'CHECK'=>'NO'), //Autoretenedor
                          array('NAME'=>'CLIARARE','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArAre'])),'"'))  ,'CHECK'=>'NO'), //Autoretenedor de Renta
                          array('NAME'=>'CLIARAIV','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArAiv'])),'"'))  ,'CHECK'=>'NO'), //Autoretenedor de IVA
                          array('NAME'=>'CLIARAIC','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArAic'])),'"'))  ,'CHECK'=>'NO'), //Autoretenedor de ICA
                          array('NAME'=>'CLIARACR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArAcr'])),'"'))  ,'CHECK'=>'NO'), //Autoretenedor de CREE
                          array('NAME'=>'CLIARAIS','VALUE'=>$cCliArAis                                                                          ,'CHECK'=>'NO'), //Autoretenedor de ICA Sucursales
                          array('NAME'=>'CLINSRRX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNsrr'])),'"'))   ,'CHECK'=>'NO'), //No sujeto RETEFTE por Renta
                          array('NAME'=>'CLINSRIV','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNsriv'])),'"'))  ,'CHECK'=>'NO'), //No sujeto RETEFTE por IVA
                          array('NAME'=>'CLINSRRI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNsrri'])),'"'))  ,'CHECK'=>'NO'), //No sujeto Retencion ICA
                          array('NAME'=>'CLINSRCR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNsrcr'])),'"'))  ,'CHECK'=>'NO'), //No Sujeto Retencion CREE
                          array('NAME'=>'CLIARRXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArr'])),'"'))    ,'CHECK'=>'NO'), //Agente Retenedor en Renta
                          array('NAME'=>'CLIARIVA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliAriva'])),'"'))  ,'CHECK'=>'NO'), //Agente Retenedor en IVA
                          array('NAME'=>'CLIARCRX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArcr'])),'"'))   ,'CHECK'=>'NO'), //Agente Retenedor CREE
                          array('NAME'=>'CLIARRIX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArrI'])),'"'))   ,'CHECK'=>'NO'), //Agente Retenedor ICA
                          array('NAME'=>'CLIARRIS','VALUE'=>$cCliArrIs                                                                          ,'CHECK'=>'NO'), //Agente Retenedor ICA Sucursales
                          array('NAME'=>'CLINICAS','VALUE'=>$cCliNsrris                                                                         ,'CHECK'=>'NO'), //No Sujeto a Retención ICA Sucursales
                          array('NAME'=>'CLIPCIXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliPci'])),'"'))    ,'CHECK'=>'NO'), //Proveedor Comercializadora Internacional
                          array('NAME'=>'CLINSOFE','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNsOfe'])),'"'))  ,'CHECK'=>'NO'), //No Sujeto a Expedir Factura de Venta o Documento Equivalente
                          array('NAME'=>'CLICTOXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliCto'])),'"'))    ,'CHECK'=>'NO'), //Conceptos contables
                          array('NAME'=>'CLIIDCOR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliIdCor'])),'"'))  ,'CHECK'=>'NO'), //Nit de Identificacion corporativo UPS
                          array('NAME'=>'CLIIDCPX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliIdProv'])),'"')) ,'CHECK'=>'NO'), //ID Corporativo del Proveedor UPS
                          array('NAME'=>'CLIIDAPX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliIdApr'])),'"'))  ,'CHECK'=>'NO'), // ID Adicional del proveedor UPS
                          array('NAME'=>'CLIDRLXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliDrl'])),'"'))    ,'CHECK'=>'NO'), //Documentos requisitos legales
                          array('NAME'=>'CLIVENXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliVen'])),'"'))    ,'CHECK'=>'NO'), //Vendedores
                          array('NAME'=>'CLICONTX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliCon'])),'"'))    ,'CHECK'=>'NO'), //Contactos
                          array('NAME'=>'CLIRESFI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliResFi'])),'"'))  ,'CHECK'=>'NO'), //Responsabilidad Fiscal
                          array('NAME'=>'CLITRIBU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliTri'])),'"'))    ,'CHECK'=>'NO'), //Tributos
                          array('NAME'=>'CLITPXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliTp'])),'"'))     ,'CHECK'=>'NO'), //Tasa Pactada
                          array('NAME'=>'CLITPCTO','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliTpCto'])),'"'))  ,'CHECK'=>'NO'), //Concepto Tasa Pactada
                          array('NAME'=>'CLICNRRF','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim($_POST['cCliCnrRf']),'"'))              ,'CHECK'=>'NO'), //Correos Notificacion Rechazos Revisor Fiscal
                          array('NAME'=>'CLIHPCNX','VALUE'=>str_replace($cBuscarEmail,$cReemplEmail,trim(trim($_POST['cCliHPCnx']),'"'))        ,'CHECK'=>'NO','CS'=>'NONE'), //Correos Notificación HP Colombia
                          array('NAME'=>'CLISAPXX','VALUE'=>trim(strtoupper($_POST['cCliSap']))                                                 ,'CHECK'=>'NO'), //Código SAP
                          array('NAME'=>'CLICWCCX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cClicWccX'])),'"'))  ,'CHECK'=>'NO'),//Código Cargowise
                          array('NAME'=>'CLIFRCCX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cClifRccX'])),'"'))  ,'CHECK'=>'NO'),//Código Forward
                          array('NAME'=>'CLIPCECN','VALUE'=>str_replace($cBuscarEmail,$cReemplEmail,trim(trim($_POST['cCliPCECn']),'"'))        ,'CHECK'=>'NO','CS'=>'NONE'), //Correos Notificación openETL
                          array('NAME'=>'CLIMMERX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerMaMer'])),'"'))  ,'CHECK'=>'NO'), //Matricula mercantil
                          array('NAME'=>'CLIIDPER','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerIdPer'])),'"'))  ,'CHECK'=>'NO'), //ID Personalizado FE
                          array('NAME'=>'CLIACUPA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliAcuPa'])),'"'))  ,'CHECK'=>'NO'), //Acuerdo de Pago
                          array('NAME'=>'CLIFACEL','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['rCliFacCel'])),'"')) ,'CHECK'=>'NO'), //Facturador Electronico FE
                          array('NAME'=>'CLIVERDI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliVer'])),'"'))    ,'CHECK'=>'NO'), //Version DIAN FE
                          array('NAME'=>'CLIOPERA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliOper'])),'"'))   ,'CHECK'=>'NO'), //Operador FE
                          array('NAME'=>'CLIFECFE','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliFec'])),'"'))    ,'CHECK'=>'NO'), //Fecha Inicial FE
                          array('NAME'=>'CLICOEMI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliCoEmi'])),'"'))  ,'CHECK'=>'NO'), //Correo de Emision FE
                          array('NAME'=>'CLICOREP','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliCoRep'])),'"'))  ,'CHECK'=>'NO'), //Correo de Recepcion FE
                          array('NAME'=>'CLIESTGM','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliEstGm'])),'"'))  ,'CHECK'=>'NO'), //Estado Cliente Grupo Malco
                          array('NAME'=>'CLIEMASI','VALUE'=>str_replace($cBuscarEmail,$cReemplEmail,trim(trim($_POST['cCliEmaSi']),'"'))        ,'CHECK'=>'NO','CS'=>'NONE'), //Correos Notificación Siemens
                          array('NAME'=>'CLIBAVCN','VALUE'=>str_replace($cBuscarEmail,$cReemplEmail,trim(trim($_POST['cCliBavCn']),'"'))        ,'CHECK'=>'NO','CS'=>'NONE'), //Correos Notificación BAVARIA
													array('NAME'=>'CLIROLCC','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliRolCc'])),'"'))  ,'CHECK'=>'NO'), //Cobrador cartera
                          array('NAME'=>'CLIMONXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliMon'])),'"'))    ,'CHECK'=>'NO'), //Moneda de Facturacion
                          array('NAME'=>'CLIDISID','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cDiscId'])),'"'))    ,'CHECK'=>'NO'), //Id Disconformidad
                          array('NAME'=>'CLIIMPCS','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($cCliImpCs)),'"'))           ,'CHECK'=>'NO'), //Cuenta IMP Cash
                          array('NAME'=>'CLIIMPCR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($CliImpCr)),'"'))            ,'CHECK'=>'NO'), //Cuenta IMP Credito
                          array('NAME'=>'CLIBANID','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cBanId'])),'"'))     ,'CHECK'=>'NO'), //ID del Banco
                          array('NAME'=>'CLITIPCU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTipCta'])),'"'))    ,'CHECK'=>'NO'), //Tipo de Cuenta
                          array('NAME'=>'CLINUMCU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cBanCta'])),'"'))    ,'CHECK'=>'NO'), //Numero de Cuenta
                          array('NAME'=>'CLIESTCU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cEstCta'])),'"'))    ,'CHECK'=>'NO'), //Estado de Cuenta
                          array('NAME'=>'CLIORCOM','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliOrCom'])),'"'))  ,'CHECK'=>'NO'), //Orden de Compra
                          array('NAME'=>'CLICECNC','VALUE'=>trim($_POST['cCliCecNc'])                                                           ,'CHECK'=>'NO'), // Chat Ejecutivo Cuenta Notificacion Checkpoint
                          array('NAME'=>'REGUSRXX','VALUE'=>$_COOKIE['kUsrId']                                                                  ,'CHECK'=>'SI'),
                          array('NAME'=>'REGFECXX','VALUE'=>date('Y-m-d')                                                                       ,'CHECK'=>'SI'),
                          array('NAME'=>'REGMODXX','VALUE'=>date('Y-m-d')                                                                       ,'CHECK'=>'SI'),
                          array('NAME'=>'REGHORXX','VALUE'=>date('H:i')                                                                         ,'CHECK'=>'SI'),
                          array('NAME'=>'REGESTXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cEstado'])),'"'))    ,'CHECK'=>'SI'));

        if (f_MySql("INSERT","SIAI0150",$qInsert,$xConexion01,$cAlfa)) {
          /***** Grabo Bien *****/
        } else {
          $nSwitch = 1;
          f_Mensaje(__FILE__,__LINE__,"Error Guardando Datos del Tercero, Verifique");
        }
      break;
      /*****************************   UPDATE    ***********************************************/
      case "EDITAR":
      case "APLICAR":
        /***** Validaciones Particulares *****/
        /* Validado El Estado del Registro */
        if (!f_InList($_POST['cEstado'],"ACTIVO","INACTIVO")) {
          $nSwitch = 1;
          f_Mensaje(__FILE__,__LINE__,"El Estado del Registro No es Correcto, Verifique");
        }
        /***** Fin de Validaciones Particulares *****/
        if ($nSwitch == "0") {
          $qUpdate =  array(array('NAME'=>'CLINOMXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper(($_POST['cTpeId'] == "NATURAL" ? $cNombre: $_POST['cTerNom']))),'"'))    ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIAPE1X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerPApe'])),'"'))   ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIAPE2X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerSApe'])),'"'))   ,'CHECK'=>'NO'),
                            array('NAME'=>'CLINOM1X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerPNom'])),'"'))   ,'CHECK'=>'NO'),
                            array('NAME'=>'CLINOM2X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerSNom'])),'"'))   ,'CHECK'=>'NO'),
                            array('NAME'=>'CLICLIXX','VALUE'=>$cChCli                                                                             ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIEMPXX','VALUE'=>$cChEmp                                                                             ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIVENCO','VALUE'=>$cChCliVenCo                                                                        ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIPROCX','VALUE'=>$cChProC                                                                            ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIPROEX','VALUE'=>$cChProE                                                                            ,'CHECK'=>'NO'),
                            array('NAME'=>'CLISOCXX','VALUE'=>$cChSoc                                                                             ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIEFIXX','VALUE'=>$cChEfi                                                                             ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIOTRXX','VALUE'=>$cChOtr                                                                             ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIDIRXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerDir'])),'"'))    ,'CHECK'=>'NO'),
                            array('NAME'=>'CLICPOSX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerCPosF'])),'"'))  ,'CHECK'=>'NO'),
                            array('NAME'=>'CLITELXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerTel'])),'"'))    ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIFAXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerFax'])),'"'))    ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIEMAXX','VALUE'=>str_replace($cBuscarEmail,$cReemplEmail,trim(trim($_POST['cTerEma']),'"'))          ,'CHECK'=>'NO','CS'=>'NONE'),
                            array('NAME'=>'CLITPERX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTpeId'])),'"'))     ,'CHECK'=>'SI'),
                            array('NAME'=>'CLINOMCX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerNomC'])),'"'))   ,'CHECK'=>'NO'),
                            array('NAME'=>'TDIIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTdiId'])),'"'))     ,'CHECK'=>'NO'),
                            array('NAME'=>'PAIIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cPaiId'])),'"'))     ,'CHECK'=>'SI'),
                            array('NAME'=>'DEPIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cDepId'])),'"'))     ,'CHECK'=>'NO'),
                            array('NAME'=>'CIUIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCiuId'])),'"'))     ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIAPAXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerApar'])),'"'))   ,'CHECK'=>'NO'),
                            array('NAME'=>'GRUIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cGruId'])),'"'))     ,'CHECK'=>'NO'),
                            array('NAME'=>'PAIID3XX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cPaiId1'])),'"'))    ,'CHECK'=>'SI'),
                            array('NAME'=>'DEPID3XX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cDepId1'])),'"'))    ,'CHECK'=>'NO'),
                            array('NAME'=>'CIUID3XX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCiuId1'])),'"'))    ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIDIR3X','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerDirC'])),'"'))   ,'CHECK'=>'SI'),
                            array('NAME'=>'CLICPOS3','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerCPosC'])),'"'))  ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIFORPX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerFPa'])),'"'))    ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIPLAXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerPla'])),'"'))    ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIMEDPX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerMedP'])),'"'))   ,'CHECK'=>'NO'),
                            array('NAME'=>'CLICUEBA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliCueBa'])),'"'))  ,'CHECK'=>'NO'),
                            array('NAME'=>'AECIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cAecId'])),'"'))     ,'CHECK'=>'NO'),
                            array('NAME'=>'CLICIRXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['dCir'])),'"'))       ,'CHECK'=>'NO'),
                            array('NAME'=>'CLICAMCX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['dCamC'])),'"'))      ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIRUTXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['dRut'])),'"'))       ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIUAPXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerUapco'])),'"'))  ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIALTEX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerAltex'])),'"'))  ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIOBSXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerObs'])),'"'))    ,'CHECK'=>'NO'),
                            array('NAME'=>'CLITCONX','VALUE'=>$cChCon                                                                             ,'CHECK'=>'NO'),
                            array('NAME'=>'CLIREIVA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliReIva'])),'"'))  ,'CHECK'=>'NO'), //Responsable de IVA
                            array('NAME'=>'CLIRECOM','VALUE'=>$cCliReCom                                                                          ,'CHECK'=>'NO'), //Regimen Comun
                            array('NAME'=>'CLIRESIM','VALUE'=>$cCliReSim                                                                          ,'CHECK'=>'NO'), //Regimen Simplificado
                            array('NAME'=>'CLIREGST','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliRegST'])),'"'))  ,'CHECK'=>'NO'), //Regimen Simple Tributacion
                            array('NAME'=>'CLIGCXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliGc'])),'"'))     ,'CHECK'=>'NO'), //Gran Contribuyente
                            array('NAME'=>'CLINRPXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNrp'])),'"'))    ,'CHECK'=>'NO'), //No Residente en el Pais
                            array('NAME'=>'CLINRPAI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNrpai'])),'"'))  ,'CHECK'=>'NO'), //Aplica IVA No Residentes
                            array('NAME'=>'CLINRPIF','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNrpif'])),'"'))  ,'CHECK'=>'NO'), //Aplica Gravamen Financiero No Residentes
                            array('NAME'=>'CLINRNSR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNrpNsr'])),'"')) ,'CHECK'=>'NO'), //No Sujeto RETEFTE por Renta No Residentes
                            array('NAME'=>'CLIARXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliAr'])),'"'))     ,'CHECK'=>'NO'), //Autoretenedor
                            array('NAME'=>'CLIARARE','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArAre'])),'"'))  ,'CHECK'=>'NO'), //Autoretenedor de Renta
                            array('NAME'=>'CLIARAIV','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArAiv'])),'"'))  ,'CHECK'=>'NO'), //Autoretenedor de IVA
                            array('NAME'=>'CLIARAIC','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArAic'])),'"'))  ,'CHECK'=>'NO'), //Autoretenedor de ICA
                            array('NAME'=>'CLIARACR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArAcr'])),'"'))  ,'CHECK'=>'NO'), //Autoretenedor de CREE
                            array('NAME'=>'CLIARAIS','VALUE'=>$cCliArAis                                                                          ,'CHECK'=>'NO'), //Autoretenedor de ICA Sucursales
                            array('NAME'=>'CLINSRRX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNsrr'])),'"'))   ,'CHECK'=>'NO'), //No sujeto RETEFTE por Renta
                            array('NAME'=>'CLINSRIV','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNsriv'])),'"'))  ,'CHECK'=>'NO'), //No sujeto RETEFTE por IVA
                            array('NAME'=>'CLINSRRI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNsrri'])),'"'))  ,'CHECK'=>'NO'), //No sujeto Retencion ICA
                            array('NAME'=>'CLINSRCR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNsrcr'])),'"'))  ,'CHECK'=>'NO'), //No Sujeto Retencion CREE
                            array('NAME'=>'CLIARRXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArr'])),'"'))    ,'CHECK'=>'NO'), //Agente Retenedor Renta
                            array('NAME'=>'CLIARIVA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliAriva'])),'"'))  ,'CHECK'=>'NO'), //Agente Retenedor en IVA
                            array('NAME'=>'CLIARCRX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArcr'])),'"'))   ,'CHECK'=>'NO'), //Agente Retenedor CREE
                            array('NAME'=>'CLIARRIX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliArrI'])),'"'))   ,'CHECK'=>'NO'), //Agente Retenedor ICA
                            array('NAME'=>'CLIARRIS','VALUE'=>$cCliArrIs                                                                          ,'CHECK'=>'NO'), //Agente Retenedor ICA Sucursales
                            array('NAME'=>'CLINICAS','VALUE'=>$cCliNsrris                                                                         ,'CHECK'=>'NO'), //No Sujeto a Retención ICA Sucursales
                            array('NAME'=>'CLIPCIXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliPci'])),'"'))    ,'CHECK'=>'NO'), //Proveedor Comercializadora Internacional
                            array('NAME'=>'CLINSOFE','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['oCliNsOfe'])),'"'))  ,'CHECK'=>'NO'), //No Sujeto a Expedir Factura de Venta o Documento Equivalente
                            array('NAME'=>'CLICTOXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliCto'])),'"'))    ,'CHECK'=>'NO'), //Conceptos contables
                            array('NAME'=>'CLIIDCOR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliIdCor'])),'"'))  ,'CHECK'=>'NO'), //Nit de Identificacion corporativo UPS
                            array('NAME'=>'CLIIDCPX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliIdProv'])),'"')) ,'CHECK'=>'NO'), //ID Corporativo del Proveedor UPS
                            array('NAME'=>'CLIIDAPX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliIdApr'])),'"'))  ,'CHECK'=>'NO'), // ID Adicional del proveedor UPS
                            array('NAME'=>'CLIDRLXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliDrl'])),'"'))    ,'CHECK'=>'NO'), //Documentos requisitos legales
                            array('NAME'=>'CLIVENXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliVen'])),'"'))    ,'CHECK'=>'NO'), //Vendedores
                            array('NAME'=>'CLICONTX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliCon'])),'"'))    ,'CHECK'=>'NO'), //Contactos
                            array('NAME'=>'CLIRESFI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliResFi'])),'"'))  ,'CHECK'=>'NO'), //Responsabilidad Fiscal
                            array('NAME'=>'CLITRIBU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliTri'])),'"'))    ,'CHECK'=>'NO'), //Tributos
                            array('NAME'=>'CLITPXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliTp'])),'"'))     ,'CHECK'=>'NO'), //Tasa Pactada
                            array('NAME'=>'CLITPCTO','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliTpCto'])),'"'))  ,'CHECK'=>'NO'), //Concepto Tasa Pactada
                            array('NAME'=>'CLICNRRF','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim($_POST['cCliCnrRf']),'"'))              ,'CHECK'=>'NO'), //Correos Notificacion Rechazos Revisor Fiscal
                            array('NAME'=>'CLIHPCNX','VALUE'=>str_replace($cBuscarEmail,$cReemplEmail,trim(trim($_POST['cCliHPCnx']),'"'))        ,'CHECK'=>'NO','CS'=>'NONE'), //Correos Notificación HP Colombia
                            array('NAME'=>'CLISAPXX','VALUE'=>trim(strtoupper($_POST['cCliSap']))                                                 ,'CHECK'=>'NO'), //Código SAP
                            array('NAME'=>'CLIPCESN','VALUE'=>"0000-00-00 00:00:00"                                                               ,'CHECK'=>'NO'), //Sincronizacion openETL
                            array('NAME'=>'CLICWCCX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cClicWccX'])),'"'))  ,'CHECK'=>'NO'),//Código Cargowise
                            array('NAME'=>'CLIFRCCX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cClifRccX'])),'"'))  ,'CHECK'=>'NO'),//Código Forward
                            array('NAME'=>'CLIPCECN','VALUE'=>str_replace($cBuscarEmail,$cReemplEmail,trim(trim($_POST['cCliPCECn']),'"'))        ,'CHECK'=>'NO','CS'=>'NONE'), //Correos Notificación openETL
                            array('NAME'=>'CLIMMERX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerMaMer'])),'"'))  ,'CHECK'=>'NO'), //Matricula mercantil
                            array('NAME'=>'CLIIDPER','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerIdPer'])),'"'))  ,'CHECK'=>'NO'), //ID Personalizado FE
                            array('NAME'=>'CLIACUPA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliAcuPa'])),'"'))  ,'CHECK'=>'NO'), //Acuerdo de Pago
                            array('NAME'=>'CLIESTGM','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliEstGm'])),'"'))  ,'CHECK'=>'NO'), //Estado Cliente Grupo Malco
                            array('NAME'=>'CLIACUPA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliAcuPa'])),'"'))  ,'CHECK'=>'NO'), //Acuerdo de Pago
                            array('NAME'=>'CLIFACEL','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['rCliFacCel'])),'"')) ,'CHECK'=>'NO'), //Facturador Electronico FE
                            array('NAME'=>'CLIVERDI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliVer'])),'"'))    ,'CHECK'=>'NO'), //Version DIAN FE
                            array('NAME'=>'CLIOPERA','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliOper'])),'"'))   ,'CHECK'=>'NO'), //Operador FE
                            array('NAME'=>'CLIFECFE','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliFec'])),'"'))    ,'CHECK'=>'NO'), //Fecha Inicial FE
                            array('NAME'=>'CLICOEMI','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliCoEmi'])),'"'))  ,'CHECK'=>'NO'), //Correo de Emision FE
                            array('NAME'=>'CLICOREP','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliCoRep'])),'"'))  ,'CHECK'=>'NO'), //Correo de Recepcion FE
                            array('NAME'=>'CLIEMASI','VALUE'=>str_replace($cBuscarEmail,$cReemplEmail,trim(trim($_POST['cCliEmaSi']),'"'))        ,'CHECK'=>'NO','CS'=>'NONE'), //Correos Notificación Siemens
                            array('NAME'=>'CLIBAVCN','VALUE'=>str_replace($cBuscarEmail,$cReemplEmail,trim(trim($_POST['cCliBavCn']),'"'))        ,'CHECK'=>'NO','CS'=>'NONE'), //Correos Notificación BAVARIA
														array('NAME'=>'CLIROLCC','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliRolCc'])),'"'))  ,'CHECK'=>'NO'), //Cobrador cartera
                            array('NAME'=>'CLIMONXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliMon'])),'"'))    ,'CHECK'=>'NO'), //Moneda de Facturacion
                            array('NAME'=>'CLIDISID','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cDiscId'])),'"'))    ,'CHECK'=>'NO'), //Id Disconformidad
                            array('NAME'=>'CLIIMPCS','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($cCliImpCs)),'"'))           ,'CHECK'=>'NO'), //Cuenta IMP Cash
                            array('NAME'=>'CLIIMPCR','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($CliImpCr)),'"'))            ,'CHECK'=>'NO'), //Cuenta IMP Credito
                            array('NAME'=>'CLIBANID','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cBanId'])),'"'))     ,'CHECK'=>'NO'), //ID del Banco
                            array('NAME'=>'CLITIPCU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTipCta'])),'"'))    ,'CHECK'=>'NO'), //Tipo de Cuenta
                            array('NAME'=>'CLINUMCU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cBanCta'])),'"'))    ,'CHECK'=>'NO'), //Numero de Cuenta
                            array('NAME'=>'CLIESTCU','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cEstCta'])),'"'))    ,'CHECK'=>'NO'), //Estado de Cuenta
                            array('NAME'=>'CLIORCOM','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cCliOrCom'])),'"'))  ,'CHECK'=>'NO'), //Orden de Compra
                            array('NAME'=>'CLICECNC','VALUE'=>trim($_POST['cCliCecNc'])                                                           ,'CHECK'=>'NO'), // Chat Ejecutivo Cuenta Notificacion Checkpoint
                            array('NAME'=>'REGMODXX','VALUE'=>date('Y-m-d')                                                                       ,'CHECK'=>'SI'),
                            array('NAME'=>'REGHORXX','VALUE'=>date('H:i')                                                                         ,'CHECK'=>'SI'),
                            array('NAME'=>'REGESTXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cEstado'])),'"'))    ,'CHECK'=>'SI'),
                            array('NAME'=>'CLIIDXXX','VALUE'=>str_replace($cBuscar01,$cReempl01,trim(trim(strtoupper($_POST['cTerId'])),'"'))     ,'CHECK'=>'WH'));

          if (f_MySql("UPDATE","SIAI0150",$qUpdate,$xConexion01,$cAlfa)) {
            /***** Grabo Bien *****/
          } else {
            $nSwitch = 1;
            f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro, Verifique");
          }
        }
      break;
      /*****************************   UPDATE    ***********************************************/
      case "ANULAR":
        if($_POST['cCliEst']=="ACTIVO"){
          $cEstado="INACTIVO";
        }
        if($_POST['cCliEst']=="INACTIVO"){
          $cEstado="ACTIVO";
        }

        $qUpdate   = array(array('NAME'=>'REGMODXX','VALUE'=>date('Y-m-d')                         ,'CHECK'=>'SI'),
                           array('NAME'=>'REGHORXX','VALUE'=>date('H:i')                           ,'CHECK'=>'SI'),
                           array('NAME'=>'REGESTXX','VALUE'=>$cEstado                              ,'CHECK'=>'SI'),
                           array('NAME'=>'CLIIDXXX','VALUE'=>trim(strtoupper($_POST['cTerId']))    ,'CHECK'=>'WH'));

        if (f_MySql("UPDATE","SIAI0150",$qUpdate,$xConexion01,$cAlfa)) {
          /***** Grabo Bien *****/
        } else {
         $nSwitch = 1;
         f_Mensaje(__FILE__,__LINE__,"Error al Actualizar el Registro, Verifique");
        }
      break;
    }
  }

  if ($nSwitch == 1) {
    f_Mensaje(__FILE__,__LINE__,"$cMsj Verifique.");
  }

  if ($nSwitch == 0) {

    switch ($_COOKIE['kModo']) {
      case "NUEVO":
      case "EDITAR":
      case "APLICAR":
      case "ANULAR":
        /**
         * Se Invoca el Servicio Web de Roldan para transmitir a SIAESA el Cliente, Proveedor o Tercero
         */
        if ($cAlfa == "ROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "DEROLDANLO") {
          $ObjWsRoldanSiesa   = new cWebServiceRoldanSiesa();

          $vDatos['cliidxxx'] = trim(strtoupper($_POST['cTerId']));
          $mRetorna = $ObjWsRoldanSiesa->fnClientesTerceros($vDatos);

          $cMsjAdv = "";
          for ($nR=1; $nR< count($mRetorna); $nR++) {
            $cMsjAdv .= $mRetorna[$nR]."\n";
          }

          f_Mensaje(__FILE__,__LINE__,"Resultado Transmision a SIESA:\n\n".$cMsjAdv."\n");

        }
      break;
      default:
        //No hace nada
      break;
    }

    if($_COOKIE['kModo']!="ANULAR"){
      f_Mensaje(__FILE__,__LINE__,"El Registro se cargo con Exito.");
    }
    if($_COOKIE['kModo']=="ANULAR"){
      f_Mensaje(__FILE__,__LINE__,"El Registro Cambio de Estado Con Exito.");
    }
    if($_COOKIE['kModo']=="APLICAR"){
    ?>
      <form name = "frgrm" action = "frternue.php?cTerId=<?php echo $cTerId ?>" method = "post" target = "fmwork"></form>
          <script languaje = "javascript">
            document.cookie='kModo=EDITAR;path='+'/';
            document.frgrm.submit();
          </script>
    <?php
    }
    if($_COOKIE['kModo']!="APLICAR"){
    ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
      document.forms['frgrm'].submit()
    </script>
    <?php
    }
  }
?>

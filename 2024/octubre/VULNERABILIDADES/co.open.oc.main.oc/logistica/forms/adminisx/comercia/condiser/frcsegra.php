<?php
  namespace openComex;
  /**
   * Graba Condiciones de Servicio.
   * --- Descripcion: Permite Guardar una Nueva Condicion de Servicio.
   * @author juan.trujillo@openits.co
   * @package opencomex
   * @version 001
   */
  include("../../../../../financiero/libs/php/utility.php");

  /**
   * Variable para saber si hay o no errores de validacion.
   *
   * @var int
   */
  $nSwitch = 0;

  /**
   * Variable para concatenar errores de validacion u otros.
   *
   * @var string
   */
  $cMsj = "\n";

  //Eliminando espacios en blanco del Id
  $_POST['cCseId'] = trim($_POST['cCseId']);
  $cApliCalc = ($_POST['cCseAcnn'] == true) ? 'SI' : 'NO';

  if ($_COOKIE['kModo'] == "NUEVO") {
    // Se calcula el consecutivo
    $nAnioActual = date('Y');
    $qCondServ  = "SELECT ";
    $qCondServ .= "cseidxxx, ";
    $qCondServ .= "csecscxx, ";
    $qCondServ .= "regfcrex ";
    $qCondServ .= "FROM $cAlfa.lpar0152 ";
    $qCondServ .= "WHERE ";
    $qCondServ .= "regfcrex LIKE \"$nAnioActual%\" ";
    $qCondServ .= "ORDER BY ABS(csecscxx) DESC ";
    $qCondServ .= "LIMIT 0,1";
    $xCondServ  = f_MySql("SELECT","",$qCondServ,$xConexion01,"");
    if (mysql_num_rows($xCondServ) > 0) {
      $vCondServ = mysql_fetch_array($xCondServ);

      $nAnioActual  = substr($nAnioActual, -2);
      $nConsecutivo = $vCondServ['csecscxx'] + 1;
      $cIdCondServ  = $nAnioActual . str_pad($nConsecutivo,4,"0",STR_PAD_LEFT);
    } else {
      $nAnioActual  = substr($nAnioActual, -2);
      $nConsecutivo = 1;
      $cIdCondServ  = $nAnioActual . str_pad("1",4,"0",STR_PAD_LEFT);
    }
  } else {
    $nConsecutivo = $_POST['cCseCsc'];
    $cIdCondServ  = $_POST['cCseId'];
  }
 

  switch ($_COOKIE['kModo']) {
    case "NUEVO":
    case "EDITAR":
      //INICIO DE VALIDACIONES
      //Validando que el NIT del cliente no sea vacio.
      if ($_POST['cCliId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Nit del Cliente no puede ser vacio.\n";
      } else {
        // Validando Nit Cliente exista
        $qCliente  = "SELECT cliidxxx ";
        $qCliente .= "FROM $cAlfa.lpar0150 ";
        $qCliente .= "WHERE ";
        $qCliente .= "cliidxxx = \"{$_POST['cCliId']}\" AND ";
        $qCliente .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xCliente  = f_MySql("SELECT","",$qCliente,$xConexion01,"");
        if (mysql_num_rows($xCliente) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Nit [".$_POST['cCliId']."] del Cliente no existe.\n";
        }
      }

      //Validando que la condicion comercial no sea vacio.
       if ($_POST['cCcoIdOc'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Condicion Comercial no puede ser vacia.\n";
      } else {
        // Validando que la condicion comercial exista
        $qCondiCom  = "SELECT ccoidocx ";
        $qCondiCom .= "FROM $cAlfa.lpar0151 ";
        $qCondiCom .= "WHERE ";
        $qCondiCom .= "ccoidocx = \"{$_POST['cCcoIdOc']}\" AND ";
        $qCondiCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xCondiCom  = f_MySql("SELECT","",$qCondiCom,$xConexion01,"");
        if (mysql_num_rows($xCondiCom) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La Condicion Comercial [".$_POST['cCcoIdOc']."] no existe.\n";
        }
      }

      //Validando que el codigo de servcicio SAP no sea vacio.
      if ($_POST['cSerSap'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Codigo SAP no puede ser vacio.\n";
      } else {
        // Validando que el codigo SAP exista
        $qServicio  = "SELECT sersapxx ";
        $qServicio .= "FROM $cAlfa.lpar0011 ";
        $qServicio .= "WHERE ";
        $qServicio .= "sersapxx = \"{$_POST['cSerSap']}\" AND ";
        $qServicio .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xServicio  = f_MySql("SELECT","",$qServicio,$xConexion01,"");
        if (mysql_num_rows($xServicio) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Codigo SAP [".$_POST['cSerSap']."] no existe.\n";
        }
      }

      //Validando que el subservicio no sea vacio.
      if ($_POST['cCseSubServ'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Debe seleccionar al menos un Subservicio.\n";
      } else {
        $vSubservicio = explode("~",$_POST['cCseSubServ']);

        for ($i=0; $i < count($vSubservicio); $i++) { 
          // Validando que el subservicio exista
          $qSubServ  = "SELECT sersapxx, subidxxx ";
          $qSubServ .= "FROM $cAlfa.lpar0012 ";
          $qSubServ .= "WHERE ";
          $qSubServ .= "sersapxx = \"{$_POST['cSerSap']}\" AND ";
          $qSubServ .= "subidxxx = \"{$vSubservicio[$i]}\" AND ";
          $qSubServ .= "regestxx = \"ACTIVO\" LIMIT 0,1";
          $xSubServ  = f_MySql("SELECT","",$qSubServ,$xConexion01,"");
          if (mysql_num_rows($xSubServ) == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Subservicio [".$vSubservicio[$i]."] no existe.\n";
          }
        }
      }

      //Validando que la unidad facturable no sea vacia.
      if ($_POST['cUfaId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Unidad Facturable no puede ser vacia.\n";
      } else {
        // Validando que La unidad facturable exista
        $qUniFact  = "SELECT ufaidxxx ";
        $qUniFact .= "FROM $cAlfa.lpar0006 ";
        $qUniFact .= "WHERE ";
        $qUniFact .= "ufaidxxx = \"{$_POST['cUfaId']}\" AND ";
        $qUniFact .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xUniFact  = f_MySql("SELECT","",$qUniFact,$xConexion01,"");
        if (mysql_num_rows($xUniFact) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La Unidad Facturable [".$_POST['cUfaId']."] no existe.\n";
        }
      }

      //Validando que el objeto facturable no sea vacio.
      if ($_POST['cObfId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Objeto Facturable no puede ser vacio.\n";
      } else {
        // Validando que el objeto facturable exista
        $qObjFact  = "SELECT obfidxxx ";
        $qObjFact .= "FROM $cAlfa.lpar0004 ";
        $qObjFact .= "WHERE ";
        $qObjFact .= "obfidxxx = \"{$_POST['cObfId']}\" AND ";
        $qObjFact .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xObjFact  = f_MySql("SELECT","",$qObjFact,$xConexion01,"");
        if (mysql_num_rows($xObjFact) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Objeto Facturable [".$_POST['cObfId']."] no existe.\n";
        }
      }

      if ($cApliCalc == "NO") {
        //Validando las organizaciones de ventas y las oficinas de ventas
        if ($_POST['cCseOrgVenta'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Debe seleccionar al menos una Organizacion de Venta.\n";
        } else {
          $vOrgVenta = explode("~", $_POST['cCseOrgVenta']);

          for ($i=0; $i < count($vOrgVenta); $i++) {
            // Validando la organizacion de venta exista
            $qOrgVenta  = "SELECT orvsapxx ";
            $qOrgVenta .= "FROM $cAlfa.lpar0001 ";
            $qOrgVenta .= "WHERE ";
            $qOrgVenta .= "orvsapxx = \"{$vOrgVenta[$i]}\" AND ";
            $qOrgVenta .= "regestxx = \"ACTIVO\" LIMIT 0,1";
            $xObjFact  = f_MySql("SELECT","",$qOrgVenta,$xConexion01,"");
            if (mysql_num_rows($xObjFact) == 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "La Organizacion de Venta [".$vOrgVenta[$i]."] no existe.\n";
            } else {
              // Validando que la oficina de venta no sea vacia
              if ($_POST['cCseOfiVenta_'.$vOrgVenta[$i]] == "") {
                $nSwitch = 1;
                $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                $cMsj .= "Debe seleccionar al menos una Oficina de Venta para la Organizacion de Venta [".$vOrgVenta[$i]."].\n";
              } else {
                $vOfiVenta = explode("~", $_POST['cCseOfiVenta_'.$vOrgVenta[$i]]);

                for ($j=0; $j < count($vOfiVenta); $j++) {
                  // Validando la oficina de venta exista
                  $qOfiVenta  = "SELECT orvsapxx, ofvsapxx ";
                  $qOfiVenta .= "FROM $cAlfa.lpar0002 ";
                  $qOfiVenta .= "WHERE ";
                  $qOfiVenta .= "orvsapxx = \"{$vOrgVenta[$i]}\" AND ";
                  $qOfiVenta .= "ofvsapxx = \"{$vOfiVenta[$j]}\" AND ";
                  $qOfiVenta .= "regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xOfiFact  = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");
                  if (mysql_num_rows($xOfiFact) == 0) {
                    $nSwitch = 1;
                    $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                    $cMsj .= "La Oficina de Venta [".$vOfiVenta[$j]."] de la Organizacion de Venta [".$vOrgVenta[$i]."] no existe.\n";
                  }
                }
              }
            }
          }
        }
      }

      //validando que no exista un registro con la misma informacion (Cliente - Condicion Comercial y Subservicio)
      $qCondiServ  = "SELECT cseidxxx ";
      $qCondiServ .= "FROM $cAlfa.lpar0152 ";
      $qCondiServ .= "WHERE ";
      $qCondiServ .= "cliidxxx = \"{$_POST['cCliId']}\" AND ";
      $qCondiServ .= "ccoidocx = \"{$_POST['cCcoIdOc']}\"";
      if ($_COOKIE['kModo'] == "EDITAR") {
        $qCondiServ .= "AND cseidxxx != \"$cIdCondServ\" ";
      }
      $xCondiServ  = f_MySql("SELECT","",$qCondiServ,$xConexion01,"");
      if (mysql_num_rows($xCondiServ) > 0) {

        while($xRCS = mysql_fetch_array($xCondiServ)) {
          // Se consultan los subservicios asociados al cliente y condicion comercial
          $vSubservicio = explode("~",$_POST['cCseSubServ']);
          for ($i=0; $i < count($vSubservicio); $i++) {
            $qSubservicio  = "SELECT cseidxxx ";
            $qSubservicio .= "FROM $cAlfa.lpar0153 ";
            $qSubservicio .= "WHERE ";
            $qSubservicio .= "cseidxxx = \"{$xRCS['cseidxxx']}\" AND ";
            $qSubservicio .= "sersapxx = \"{$_POST['cSerSap']}\" AND ";
            $qSubservicio .= "subidxxx = \"{$vSubservicio[$i]}\" ";
            $xSubservicio  = f_MySql("SELECT","",$qSubservicio,$xConexion01,"");

            if (mysql_num_rows($xSubservicio) > 0) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Ya existe una Condicion de Servicio para el Cliente [".$_POST['cCliId']."], Oferta Comercial [".$_POST['cCcoIdOc']."] y Subservicio [".$vSubservicio[$i]."].\n";
            }
          }
        }
      }
    break;
    case "CAMBIAESTADO":
      if ($_POST['cCseId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El ID no puede ser vacio.\n";
      } else {
        $qCondiServ  = "SELECT cseidxxx, regestxx ";
        $qCondiServ .= "FROM $cAlfa.lpar0152 ";
        $qCondiServ .= "WHERE ";
        $qCondiServ .= "cseidxxx = \"{$_POST['cCseId']}\" LIMIT 0,1";
        $xCondiServ  = f_MySql("SELECT","",$qCondiServ,$xConexion01,"");
  
        // Validando Codigo exista
        if (mysql_num_rows($xCondiServ) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Registro seleccionado no existe.\n";
        } else {
          $vCondiCom = mysql_fetch_array($xCondiServ);
          $cNueEst = ($vCondiCom['regestxx'] == "ACTIVO") ? "INACTIVO" : "ACTIVO";
        }
      }
    break;
  }

  //Empieza a grabar
  if ($nSwitch == 0) {
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        $qInsert  = array(array('NAME' => 'cseidxxx','VALUE' => trim($cIdCondServ)             ,'CHECK' => 'SI'),
                          array('NAME' => 'csecscxx','VALUE' => trim($nConsecutivo)            ,'CHECK' => 'SI'),
                          array('NAME' => 'cliidxxx','VALUE' => trim($_POST['cCliId'])         ,'CHECK' => 'SI'),
                          array('NAME' => 'ccoidocx','VALUE' => trim($_POST['cCcoIdOc'])       ,'CHECK' => 'SI'),
                          array('NAME' => 'sersapxx','VALUE' => trim($_POST['cSerSap'])        ,'CHECK' => 'SI'),
                          array('NAME' => 'ufaidxxx','VALUE' => trim($_POST['cUfaId'])         ,'CHECK' => 'SI'),
                          array('NAME' => 'obfidxxx','VALUE' => trim($_POST['cObfId'])         ,'CHECK' => 'SI'),
                          array('NAME' => 'cseacnnx','VALUE' => trim($cApliCalc)               ,'CHECK' => 'SI'),
                          array('NAME' => 'cseobsxx','VALUE' => trim($_POST['cCseObs'])        ,'CHECK' => 'NO'),
                          array('NAME' => 'regusrxx','VALUE' => trim($_COOKIE['kUsrId'])       ,'CHECK' => 'SI'),
                          array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                  ,'CHECK' => 'SI'),
                          array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                  ,'CHECK' => 'SI'),
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                  ,'CHECK' => 'SI'),
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                  ,'CHECK' => 'SI'),
                          array('NAME' => 'regestxx','VALUE' => "ACTIVO"                       ,'CHECK' => 'SI'));

        if (!f_MySql("INSERT","lpar0152",$qInsert,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Guardar el Registro.\n";
        } else {
          // Guardando Condiciones de Servicio - Subservicios
          $vSubservicio = explode("~",$_POST['cCseSubServ']);
          for ($i=0; $i < count($vSubservicio); $i++) {
            $qInsertSub = array(array('NAME' => 'cseidxxx','VALUE' => trim($cIdCondServ)             ,'CHECK' => 'SI'),
                                array('NAME' => 'sersapxx','VALUE' => trim($_POST['cSerSap'])        ,'CHECK' => 'SI'),
                                array('NAME' => 'subidxxx','VALUE' => trim($vSubservicio[$i])        ,'CHECK' => 'SI'),
                                array('NAME' => 'regusrxx','VALUE' => trim($_COOKIE['kUsrId'])       ,'CHECK' => 'SI'),
                                array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                  ,'CHECK' => 'SI'),
                                array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                  ,'CHECK' => 'SI'),
                                array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                  ,'CHECK' => 'SI'),
                                array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                  ,'CHECK' => 'SI'),
                                array('NAME' => 'regestxx','VALUE' => "ACTIVO"                       ,'CHECK' => 'SI'));

            if (!f_MySql("INSERT","lpar0153",$qInsertSub,$xConexion01,$cAlfa)) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Error al Guardar el Registro de Subservicios.\n";
            }
          }

          if ($cApliCalc == "NO") {
            // Guardando Condiciones de Servicio - Oficinas y Organizacion de Ventas
            $vOrgVenta = explode("~", $_POST['cCseOrgVenta']);
            for ($i=0; $i < count($vOrgVenta); $i++) {
              $vOfiVenta = explode("~", $_POST['cCseOfiVenta_'.$vOrgVenta[$i]]);
              for ($j=0; $j < count($vOfiVenta); $j++) {
                $qInsertOrg = array(array('NAME' => 'cseidxxx','VALUE' => trim($cIdCondServ)             ,'CHECK' => 'SI'),
                                    array('NAME' => 'orvsapxx','VALUE' => trim($vOrgVenta[$i])           ,'CHECK' => 'SI'),
                                    array('NAME' => 'ofvsapxx','VALUE' => trim($vOfiVenta[$j])           ,'CHECK' => 'SI'),
                                    array('NAME' => 'regusrxx','VALUE' => trim($_COOKIE['kUsrId'])       ,'CHECK' => 'SI'),
                                    array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                  ,'CHECK' => 'SI'),
                                    array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                  ,'CHECK' => 'SI'),
                                    array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                  ,'CHECK' => 'SI'),
                                    array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                  ,'CHECK' => 'SI'),
                                    array('NAME' => 'regestxx','VALUE' => "ACTIVO"                       ,'CHECK' => 'SI'));

                if (!f_MySql("INSERT","lpar0154",$qInsertOrg,$xConexion01,$cAlfa)) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Error al Guardar el Registro de Organizacion y Oficinas de Venta.\n";
                }
              }
            }
          }
        }

      break;
      case "EDITAR":
        $qUpdate  = array(array('NAME' => 'cliidxxx','VALUE' => trim($_POST['cCliId'])                ,'CHECK' => 'SI'),
                          array('NAME' => 'ccoidocx','VALUE' => trim($_POST['cCcoIdOc'])              ,'CHECK' => 'SI'),
                          array('NAME' => 'sersapxx','VALUE' => trim($_POST['cSerSap'])               ,'CHECK' => 'SI'),
                          array('NAME' => 'ufaidxxx','VALUE' => trim($_POST['cUfaId'])                ,'CHECK' => 'SI'),
                          array('NAME' => 'obfidxxx','VALUE' => trim($_POST['cObfId'])                ,'CHECK' => 'SI'),
                          array('NAME' => 'cseacnnx','VALUE' => trim($cApliCalc)                      ,'CHECK' => 'SI'),
                          array('NAME' => 'cseobsxx','VALUE' => trim($_POST['cCseObs'])               ,'CHECK' => 'NO'),
                          array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK' => 'SI'),
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),
                          array('NAME' => 'regestxx','VALUE' => trim(strtoupper($_POST['cEstado']))   ,'CHECK' => 'SI'),
                          array('NAME' => 'cseidxxx','VALUE' => trim($cIdCondServ)                    ,'CHECK' => 'WH'));
  
        if (!f_MySql("UPDATE","lpar0152",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Actualizar el Registro.\n";
        } else {
          $cDeleteSub = array(array('NAME'=>'cseidxxx','VALUE'=>trim($cIdCondServ),'CHECK'=>'WH'));
          f_MySql("DELETE","lpar0153",$cDeleteSub,$xConexion01,$cAlfa);
          
          // Guardando Condiciones de Servicio - Subservicios
          $vSubservicio = explode("~",$_POST['cCseSubServ']);
          for ($i=0; $i < count($vSubservicio); $i++) {
            $qInsertSub = array(array('NAME' => 'cseidxxx','VALUE' => trim($cIdCondServ)             ,'CHECK' => 'SI'),
                                array('NAME' => 'sersapxx','VALUE' => trim($_POST['cSerSap'])        ,'CHECK' => 'SI'),
                                array('NAME' => 'subidxxx','VALUE' => trim($vSubservicio[$i])        ,'CHECK' => 'SI'),
                                array('NAME' => 'regusrxx','VALUE' => trim($_COOKIE['kUsrId'])       ,'CHECK' => 'SI'),
                                array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                  ,'CHECK' => 'SI'),
                                array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                  ,'CHECK' => 'SI'),
                                array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                  ,'CHECK' => 'SI'),
                                array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                  ,'CHECK' => 'SI'),
                                array('NAME' => 'regestxx','VALUE' => "ACTIVO"                       ,'CHECK' => 'SI'));

            if (!f_MySql("INSERT","lpar0153",$qInsertSub,$xConexion01,$cAlfa)) {
              $nSwitch = 1;
              $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
              $cMsj .= "Error al Guardar el Registro de Subservicios.\n";
            }
          }

          // Elimina para guardar nuevamente
          $cDeleteOrg = array(array('NAME'=>'cseidxxx','VALUE'=>trim($cIdCondServ),'CHECK'=>'WH'));
          f_MySql("DELETE","lpar0154",$cDeleteOrg,$xConexion01,$cAlfa);

          if ($cApliCalc == "NO") {
            // Guardando Condiciones de Servicio - Oficinas y Organizacion de Ventas
            $vOrgVenta = explode("~", $_POST['cCseOrgVenta']);
            for ($i=0; $i < count($vOrgVenta); $i++) {
              $vOfiVenta = explode("~", $_POST['cCseOfiVenta_'.$vOrgVenta[$i]]);
              for ($j=0; $j < count($vOfiVenta); $j++) {
                $qInsertOrg = array(array('NAME' => 'cseidxxx','VALUE' => trim($cIdCondServ)             ,'CHECK' => 'SI'),
                                    array('NAME' => 'orvsapxx','VALUE' => trim($vOrgVenta[$i])           ,'CHECK' => 'SI'),
                                    array('NAME' => 'ofvsapxx','VALUE' => trim($vOfiVenta[$j])           ,'CHECK' => 'SI'),
                                    array('NAME' => 'regusrxx','VALUE' => trim($_COOKIE['kUsrId'])       ,'CHECK' => 'SI'),
                                    array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                  ,'CHECK' => 'SI'),
                                    array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                  ,'CHECK' => 'SI'),
                                    array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                  ,'CHECK' => 'SI'),
                                    array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                  ,'CHECK' => 'SI'),
                                    array('NAME' => 'regestxx','VALUE' => "ACTIVO"                       ,'CHECK' => 'SI'));

                if (!f_MySql("INSERT","lpar0154",$qInsertOrg,$xConexion01,$cAlfa)) {
                  $nSwitch = 1;
                  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
                  $cMsj .= "Error al Guardar el Registro de Organizacion y Oficinas de Venta.\n";
                }
              }
            }
          }
        }
      break;
      case "CAMBIAESTADO":
        $qUpdate  = array(array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                       ,'CHECK'=>'SI'),
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                       ,'CHECK'=>'SI'),
                          array('NAME' => 'regestxx','VALUE' => $cNueEst                            ,'CHECK'=>'SI'),
                          array('NAME' => 'cseidxxx','VALUE' => trim($cIdCondServ)                  ,'CHECK' => 'WH'));

        if (!f_MySql("UPDATE","lpar0152",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Actualizar Estado.\n";
        } else {
          $qUpdateSub = array(array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                              array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                       ,'CHECK'=>'SI'),
                              array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                       ,'CHECK'=>'SI'),
                              array('NAME' => 'regestxx','VALUE' => $cNueEst                            ,'CHECK'=>'SI'),
                              array('NAME' => 'cseidxxx','VALUE' => trim($cIdCondServ)                  ,'CHECK' => 'WH'));
          f_MySql("UPDATE","lpar0153",$qUpdateSub,$xConexion01,$cAlfa);

          $qUpdateOrg = array(array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                              array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                       ,'CHECK'=>'SI'),
                              array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                       ,'CHECK'=>'SI'),
                              array('NAME' => 'regestxx','VALUE' => $cNueEst                            ,'CHECK'=>'SI'),
                              array('NAME' => 'cseidxxx','VALUE' => trim($cIdCondServ)                  ,'CHECK' => 'WH'));
          f_MySql("UPDATE","lpar0154",$qUpdateOrg,$xConexion01,$cAlfa);
        }
      break;
    }
  }
  
  if ($nSwitch == 0) {
    if($_COOKIE['kModo'] == "NUEVO"){
      f_Mensaje(__FILE__,__LINE__,"El Registro se Cargo con Exito.");
    }elseif($_COOKIE['kModo'] == "EDITAR"){
      f_Mensaje(__FILE__,__LINE__,"El Registro se Actualizo con Exito.");
    }elseif($_COOKIE['kModo'] == "CAMBIAESTADO"){
      f_Mensaje(__FILE__,__LINE__,"El Registro Cambio de Estado con Exito.");
    }
    ?>
    <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
    <script languaje = "javascript">
      parent.fmnav.location="<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
      document.forms['frgrm'].submit()
    </script>
  <?php }
  
  if ($nSwitch == 1) {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.\n");
  }
?>
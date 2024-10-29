<?php
  namespace openComex;
  /**
   * Graba Deposito.
   * --- Descripcion: Permite Guardar un Nuevo Deposito.
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
  $_POST['cDepNum'] = trim($_POST['cDepNum']);

  switch ($_COOKIE['kModo']) {
    case "NUEVO":
    case "EDITAR":
      //INICIO DE VALIDACIONES
      // Validando el numero de deposito
      if ($_POST['cDepNum'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Numero de Deposito no puede ser vacio.\n";
      } else {
        if (!preg_match("/^[A-Za-z0-9]+$/", $_POST['cDepNum'])) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Numero de Deposito solo puede contener valores alfanumericos.\n";
        }
      }

      // Validando el tipo de deposito
      if ($_POST['cTdeId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Tipo de Deposito no puede ser vacio.\n";
      } else {
        // Validando que el tipo de deposito exista
        $qTipoDep  = "SELECT tdeidxxx ";
        $qTipoDep .= "FROM $cAlfa.lpar0007 ";
        $qTipoDep .= "WHERE ";
        $qTipoDep .= "tdeidxxx = \"{$_POST['cTdeId']}\" AND ";
        $qTipoDep .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xTipoDep  = f_MySql("SELECT","",$qTipoDep,$xConexion01,"");
        if (mysql_num_rows($xTipoDep) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Tipo de Deposito [".$_POST['cTdeId']."] no existe.\n";
        }
      }

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

      // Validando la oferta comercial
      if ($_POST['cCcoIdOc'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Oferta Comercial no puede ser vacia.\n";
      } else {
        // Validando que la oferta comercial exista
        $qCondiCom  = "SELECT ccoidocx ";
        $qCondiCom .= "FROM $cAlfa.lpar0151 ";
        $qCondiCom .= "WHERE ";
        $qCondiCom .= "ccoidocx = \"{$_POST['cCcoIdOc']}\" AND ";
        $qCondiCom .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xCondiCom  = f_MySql("SELECT","",$qCondiCom,$xConexion01,"");
        if (mysql_num_rows($xCondiCom) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La Oferta Comercial [".$_POST['cCcoIdOc']."] no existe.\n";
        }
      }

      // Validando la Periodicidad
      if ($_POST['cPfaId'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Periodicidad no puede ser vacia.\n";
      } else {
        // Validando que la Periodicidad exista
        $qPeriocidad  = "SELECT pfaidxxx ";
        $qPeriocidad .= "FROM $cAlfa.lpar0005 ";
        $qPeriocidad .= "WHERE ";
        $qPeriocidad .= "pfaidxxx = \"{$_POST['cPfaId']}\" AND ";
        $qPeriocidad .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xPeriocidad  = f_MySql("SELECT","",$qPeriocidad,$xConexion01,"");
        if (mysql_num_rows($xPeriocidad) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La Periodicidad [".$_POST['cPfaId']."] no existe.\n";
        }
      }

      // Validando la organizacion de venta
      if ($_POST['cOrvSap'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Organizacion de Venta no puede ser vacia.\n";
      } else {
        // Validando que la organizacion de venta exista
        $qOrgVenta  = "SELECT orvsapxx ";
        $qOrgVenta .= "FROM $cAlfa.lpar0001 ";
        $qOrgVenta .= "WHERE ";
        $qOrgVenta .= "orvsapxx = \"{$_POST['cOrvSap']}\" AND ";
        $qOrgVenta .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xOrgVenta  = f_MySql("SELECT","",$qOrgVenta,$xConexion01,"");
        if (mysql_num_rows($xOrgVenta) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La Organizacion de Venta [".$_POST['cOrvSap']."] no existe.\n";
        }
      }

      // Validando la oficina de venta
      if ($_POST['cOfvSap'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La Oficina de Venta no puede ser vacia.\n";
      } else {
        // Validando que la oficina de venta exista
        $qOfiVenta  = "SELECT ofvsapxx ";
        $qOfiVenta .= "FROM $cAlfa.lpar0002 ";
        $qOfiVenta .= "WHERE ";
        $qOfiVenta .= "orvsapxx = \"{$_POST['cOrvSap']}\" AND ";
        $qOfiVenta .= "ofvsapxx = \"{$_POST['cOfvSap']}\" AND ";
        $qOfiVenta .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xOfiVenta  = f_MySql("SELECT","",$qOfiVenta,$xConexion01,"");
        if (mysql_num_rows($xOfiVenta) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "La Oficina de Venta [".$_POST['cOfvSap']."] no existe o no pertenece a la Organizacion de Venta [".$_POST['cOrvSap']."].\n";
        }
      }

      // Validando el centro logistico
      if ($_POST['cCloSap'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Centro Logistico no puede ser vacio.\n";
      } else {
        // Validando que el centro logistico exista
        $qCentroLog  = "SELECT closapxx ";
        $qCentroLog .= "FROM $cAlfa.lpar0003 ";
        $qCentroLog .= "WHERE ";
        $qCentroLog .= "orvsapxx = \"{$_POST['cOrvSap']}\" AND ";
        $qCentroLog .= "ofvsapxx = \"{$_POST['cOfvSap']}\" AND ";
        $qCentroLog .= "closapxx = \"{$_POST['cCloSap']}\" AND ";
        $qCentroLog .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xCentroLog  = f_MySql("SELECT","",$qCentroLog,$xConexion01,"");
        if (mysql_num_rows($xCentroLog) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Centro Logistico [".$_POST['cCloSap']."] no existe o no pertenece a la Organizacion de Venta [".$_POST['cOrvSap']."] y La Oficina de Venta [".$_POST['cOfvSap']."].\n";
        }
      }

      // Validando el sector
      if ($_POST['cSecSap'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Sector no puede ser vacia.\n";
      } else {
        // Validando que el sector
        $qOrgVenta  = "SELECT secsapxx ";
        $qOrgVenta .= "FROM $cAlfa.lpar0009 ";
        $qOrgVenta .= "WHERE ";
        $qOrgVenta .= "secsapxx = \"{$_POST['cSecSap']}\" AND ";
        $qOrgVenta .= "regestxx = \"ACTIVO\" LIMIT 0,1";
        $xOrgVenta  = f_MySql("SELECT","",$qOrgVenta,$xConexion01,"");
        if (mysql_num_rows($xOrgVenta) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Sector [".$_POST['cSecSap']."] no existe.\n";
        }
      }

      //validando que no exista un registro con la misma informacion
      if ($_COOKIE['kModo'] == "NUEVO") {
        $qDeposito  = "SELECT depnumxx ";
        $qDeposito .= "FROM $cAlfa.lpar0155 ";
        $qDeposito .= "WHERE ";
        $qDeposito .= "depnumxx = \"{$_POST['cDepNum']}\" ";
        $qDeposito .= "LIMIT 0,1";
        $xDeposito  = f_MySql("SELECT","",$qDeposito,$xConexion01,"");
        if (mysql_num_rows($xDeposito) > 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Deposito [".$_POST['cDepNum']."] ya existe.\n";
        }
      }

    break;
    case "CAMBIAESTADO":
      if ($_POST['cDepNum'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Numero de Deposito no puede ser vacio.\n";
      } else {
        $qCondiServ  = "SELECT depnumxx, regestxx ";
        $qCondiServ .= "FROM $cAlfa.lpar0155 ";
        $qCondiServ .= "WHERE ";
        $qCondiServ .= "depnumxx = \"{$_POST['cDepNum']}\" LIMIT 0,1";
        $xCondiServ  = f_MySql("SELECT","",$qCondiServ,$xConexion01,"");
  
        /***** Validando Codigo exista *****/
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
        $qInsert  = array(array('NAME' => 'depnumxx','VALUE' => trim($_POST['cDepNum'])        ,'CHECK' => 'SI'),
                          array('NAME' => 'tdeidxxx','VALUE' => trim($_POST['cTdeId'])         ,'CHECK' => 'SI'),
                          array('NAME' => 'cliidxxx','VALUE' => trim($_POST['cCliId'])         ,'CHECK' => 'SI'),
                          array('NAME' => 'ccoidocx','VALUE' => trim($_POST['cCcoIdOc'])       ,'CHECK' => 'SI'),
                          array('NAME' => 'pfaidxxx','VALUE' => trim($_POST['cPfaId'])         ,'CHECK' => 'SI'),
                          array('NAME' => 'orvsapxx','VALUE' => trim($_POST['cOrvSap'])        ,'CHECK' => 'SI'),
                          array('NAME' => 'ofvsapxx','VALUE' => trim($_POST['cOfvSap'])        ,'CHECK' => 'SI'),
                          array('NAME' => 'closapxx','VALUE' => trim($_POST['cCloSap'])        ,'CHECK' => 'SI'),
                          array('NAME' => 'secsapxx','VALUE' => trim($_POST['cSecSap'])        ,'CHECK' => 'SI'),
                          array('NAME' => 'regusrxx','VALUE' => trim($_COOKIE['kUsrId'])       ,'CHECK' => 'SI'),
                          array('NAME' => 'regfcrex','VALUE' => date('Y-m-d')                  ,'CHECK' => 'SI'),
                          array('NAME' => 'reghcrex','VALUE' => date('H:i:s')                  ,'CHECK' => 'SI'),
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                  ,'CHECK' => 'SI'),
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                  ,'CHECK' => 'SI'),
                          array('NAME' => 'regestxx','VALUE' => "ACTIVO"                       ,'CHECK' => 'SI'));

        if (!f_MySql("INSERT","lpar0155",$qInsert,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Guardar el Registro.\n";
        } 
      break;
      case "EDITAR":
        $qUpdate  = array(array('NAME' => 'tdeidxxx','VALUE' => trim($_POST['cTdeId'])                ,'CHECK' => 'SI'),
                          array('NAME' => 'cliidxxx','VALUE' => trim($_POST['cCliId'])                ,'CHECK' => 'SI'),
                          array('NAME' => 'ccoidocx','VALUE' => trim($_POST['cCcoIdOc'])              ,'CHECK' => 'SI'),
                          array('NAME' => 'pfaidxxx','VALUE' => trim($_POST['cPfaId'])                ,'CHECK' => 'SI'),
                          array('NAME' => 'orvsapxx','VALUE' => trim($_POST['cOrvSap'])               ,'CHECK' => 'SI'),
                          array('NAME' => 'ofvsapxx','VALUE' => trim($_POST['cOfvSap'])               ,'CHECK' => 'SI'),
                          array('NAME' => 'closapxx','VALUE' => trim($_POST['cCloSap'])               ,'CHECK' => 'SI'),
                          array('NAME' => 'secsapxx','VALUE' => trim($_POST['cSecSap'])               ,'CHECK' => 'SI'),
                          array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId']))  ,'CHECK' => 'SI'),
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                         ,'CHECK' => 'SI'),
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                         ,'CHECK' => 'SI'),
                          array('NAME' => 'regestxx','VALUE' => trim(strtoupper($_POST['cEstado']))   ,'CHECK' => 'SI'),
                          array('NAME' => 'depnumxx','VALUE' => trim($_POST['cDepNum'])                ,'CHECK' => 'WH'));
  
        if (!f_MySql("UPDATE","lpar0155",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Actualizar el Registro.\n";
        } 
      break;
      case "CAMBIAESTADO":
        $qUpdate  = array(array('NAME' => 'regusrxx','VALUE' => trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                          array('NAME' => 'regfmodx','VALUE' => date('Y-m-d')                       ,'CHECK'=>'SI'),
                          array('NAME' => 'reghmodx','VALUE' => date('H:i:s')                       ,'CHECK'=>'SI'),
                          array('NAME' => 'regestxx','VALUE' => $cNueEst                            ,'CHECK'=>'SI'),
                          array('NAME' => 'depnumxx','VALUE' => trim($_POST['cDepNum'])             ,'CHECK' => 'WH'));

        if (!f_MySql("UPDATE","lpar0155",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Actualizar Estado.\n";
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
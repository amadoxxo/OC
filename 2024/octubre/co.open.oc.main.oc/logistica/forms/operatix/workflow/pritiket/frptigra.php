<?php
namespace openComex;

/**
 * Graba Prioridad de Tickets.
 * --- Descripcion: Permite Guardar una Nueva Prioridad de Ticket.
 * @author cristian.perdomo@openits.co
 * @package openComex
 * @version 001
 */
include("../../../../../financiero/libs/php/utility.php");

$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
$cMsj = "";
global $cAlfa;
global $xConexion01;
global $vSysStr;

switch ($_COOKIE['kModo']) {
  case "NUEVO":
  case "EDITAR":

    //Eliminando espacios en blanco
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        // Trayendo el ultimo consecutivo
        $qDatPri  = "SELECT pticodxx ";
        $qDatPri .= "FROM $cAlfa.lpar0156 ";
        $qDatPri .= "ORDER BY ABS(pticodxx) DESC LIMIT 0,1";
        $xDatPri  = f_MySql("SELECT", "", $qDatPri, $xConexion01, "");
        $_POST['cPtiCod'] = 1;
        if (mysql_num_rows($xDatPri) > 0) {
          $vDatPri = mysql_fetch_array($xDatPri);
          $_POST['cPtiCod'] = $vDatPri['pticodxx'] + 1;
        }
      break;
      default:
        $qDatPri  = "SELECT pticodxx,ptidesxx ";
        $qDatPri .= "FROM $cAlfa.lpar0156 ";
        $qDatPri .= "WHERE ";
        $qDatPri .= "pticodxx = \"{$_POST['cPtiCod']}\" LIMIT 0,1";
        $xDatPri  = f_MySql("SELECT", "", $qDatPri, $xConexion01, "");
        // f_Mensaje(__FILE__,__LINE__,$qDatPri."~".mysql_num_rows($xDatPri));
        /***** Validando Codigo exista *****/
        if (mysql_num_rows($xDatPri) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
          $cMsj .= "El Codigo no existe.\n";
        }
      break;
    }

    if ($_POST['cPtiDes'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "La Descripcion de Prioridad de tickets no puede ser vacia.\n ";
    }
    break;
  case "CAMBIAESTADO":
    if ($_POST['cPtiCod'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El Codigo no puede ser vacio.\n";
    } else {
      // Construcción de la consulta SQL
      $qDatPri  = "SELECT pticodxx, ptidesxx, regestxx ";
      $qDatPri .= "FROM $cAlfa.lpar0156 ";
      $qDatPri .= "WHERE pticodxx = '" . $_POST['cPtiCod'] . "' LIMIT 1";
      // Ejecución de la consulta usando f_MySql
      $xDatPri = f_MySql("SELECT", "", $qDatPri, $xConexion01, "");
      /***** Validando Codigo exista *****/
      if (mysql_num_rows($xDatPri) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "El Codigo no existe.\n";
      } else {
        $vDatPri = mysql_fetch_array($xDatPri);
        $cNueEst = ($vDatPri['regestxx'] == "ACTIVO") ? "INACTIVO" : "ACTIVO";
      }
    }
  break;
  case "ELIMINAR":
    // Validando que el codigo de la prioridad no exista en algun ticket
    for ($cAnio = date('Y'); $cAnio >= $vSysStr['logistica_ano_instalacion_modulo']; $cAnio--) {
      $qTickets = "SELECT * "; // Aquí debes especificar las columnas que deseas seleccionar
      $qTickets .= "FROM $cAlfa.ltid$cAnio ";
      $qTickets .= "WHERE $cAlfa.ltid$cAnio.pticodxx = " . $_POST['cPtiCod'];
      $xTickets = f_MySql("SELECT", "", $qTickets, $xConexion01, "");
      if (mysql_num_rows($xTickets) > 0) {
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "La prioridad ticket [" . $_POST['cPtiCod'] . "] ya ha sido utilizada en la creacion de un ticket.\n";
        break;
      }
    }
    break;
}
/***** Fin de la Validacion *****/

/***** Ahora Empiezo a Grabar *****/
/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      $qInsert  = array(array('NAME' => 'pticodxx', 'VALUE' => str_pad($_POST['cPtiCod'], 3, "0", STR_PAD_LEFT) , 'CHECK' => 'NO'),
                        array('NAME' => 'ptidesxx', 'VALUE' => trim(strtoupper($_POST['cPtiDes']))              , 'CHECK' => 'SI'),
                        array('NAME' => 'pticolxx', 'VALUE' => trim(strtoupper($_POST['cPtiCol']))              , 'CHECK' => 'SI'),
                        array('NAME' => 'regusrxx', 'VALUE' => trim($_COOKIE['kUsrId'])                         , 'CHECK' => 'SI'),
                        array('NAME' => 'regfcrex', 'VALUE' => date('Y-m-d')                                    , 'CHECK' => 'SI'),
                        array('NAME' => 'reghcrex', 'VALUE' => date('H:i:s')                                    , 'CHECK' => 'SI'),
                        array('NAME' => 'regfmodx', 'VALUE' => date('Y-m-d')                                    , 'CHECK' => 'SI'),
                        array('NAME' => 'reghmodx', 'VALUE' => date('H:i:s')                                    , 'CHECK' => 'SI'),
                        array('NAME' => 'regestxx', 'VALUE' => "ACTIVO"                                         , 'CHECK' => 'SI'));

      if (!f_MySql("INSERT", "lpar0156", $qInsert, $xConexion01, $cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "Error Guardando Datos.\n";
      }
    break;
    case "EDITAR":
      /***** Fin de Validaciones Particulares *****/
      $qUpdate  = array(array('NAME' => 'ptidesxx', 'VALUE' => trim(strtoupper($_POST['cPtiDes']))  , 'CHECK' => 'SI'),
                        array('NAME' => 'pticolxx', 'VALUE' => trim(strtoupper($_POST['cPtiCol']))              , 'CHECK' => 'SI'),
                        array('NAME' => 'regusrxx', 'VALUE' => trim(strtoupper($_COOKIE['kUsrId'])) , 'CHECK' => 'SI'),
                        array('NAME' => 'regfmodx', 'VALUE' => date('Y-m-d')                        , 'CHECK' => 'SI'),
                        array('NAME' => 'reghmodx', 'VALUE' => date('H:i:s')                        , 'CHECK' => 'SI'),
                        array('NAME' => 'regestxx', 'VALUE' => trim(strtoupper($_POST['cEstado']))  , 'CHECK' => 'SI'),
                        array('NAME' => 'pticodxx', 'VALUE' => trim($_POST['cPtiCod'])              , 'CHECK' => 'WH'));

      if (!f_MySql("UPDATE", "lpar0156", $qUpdate, $xConexion01, $cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "Error Actualizar Datos.\n";
      }
    break;
    case "CAMBIAESTADO":
      $qUpdate  = array(array('NAME' => 'regusrxx', 'VALUE' => trim(strtoupper($_COOKIE['kUsrId'])) , 'CHECK' => 'SI'),
                        array('NAME' => 'regfmodx', 'VALUE' => date('Y-m-d')                        , 'CHECK' => 'SI'),
                        array('NAME' => 'reghmodx', 'VALUE' => date('H:i:s')                        , 'CHECK' => 'SI'),
                        array('NAME' => 'regestxx', 'VALUE' => $cNueEst                             , 'CHECK' => 'SI'),
                        array('NAME' => 'pticodxx', 'VALUE' => trim($_POST['cPtiCod'])              , 'CHECK' => 'WH'));

      if (!f_MySql("UPDATE", "lpar0156", $qUpdate, $xConexion01, $cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "Error Actualizar Estado.\n";
      }
    break;
    case "ELIMINAR":
      $qDelete =  array(array('NAME' => 'pticodxx', 'VALUE' => trim($_POST['cPtiCod']), 'CHECK' => 'WH'));

      if (!f_MySql("DELETE", "lpar0156", $qDelete, $xConexion01, $cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "Error al Eliminar la Prioridad de Ticket " . $_POST['cPtiCod'] . ",\n";
      }
    break;
  }
}

if ($nSwitch == 0) {
  if($_COOKIE['kModo']=="NUEVO"){
    f_Mensaje(__FILE__,__LINE__,"El Registro se Cargo Con Exito");
  }
  if ($_COOKIE['kModo'] == "CAMBIAESTADO") {
    f_Mensaje(__FILE__, __LINE__, "El Registro Cambio de Estado Con Exito");
  }
  if ($_COOKIE['kModo'] == "ELIMINAR") {
    f_Mensaje(__FILE__, __LINE__, "El Registro se Elimino Con Exito");
  }
?>
  <form name="frgrm" action="<?php echo $_COOKIE['kIniAnt'] ?>" method="post" target="fmwork"></form>
  <script languaje="javascript">
    parent.fmnav.location = "<?php echo $cPlesk_Forms_Directory_Logistic ?>/frnivel3.php";
    document.forms['frgrm'].submit()
  </script>
<?php }

if ($nSwitch == 1) {
  f_Mensaje(__FILE__, __LINE__, $cMsj . "Verifique.\n");
}
?>
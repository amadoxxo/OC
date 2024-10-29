<?php
namespace openComex;
/**
 * Graba Tipos de Tickets.
 * --- Descripcion: Permite Guardar un Tipo de Ticket.
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
    
    switch ($_COOKIE['kModo']) {
      case "NUEVO":
        /***** Validando Codigo no exista *****/
        // Trayendo el ultimo consecutivo
        $qTipTic  = "SELECT tticodxx ";
        $qTipTic .= "FROM $cAlfa.lpar0158 ";
        $qTipTic .= "ORDER BY ABS(tticodxx) DESC LIMIT 0,1";
        $xTipTic  = f_MySql("SELECT", "", $qTipTic, $xConexion01, "");
        $_POST['cTtiCod'] = 1;
        if (mysql_num_rows($xTipTic) > 0) {
          $vTipTic = mysql_fetch_array($xTipTic);
          $_POST['cTtiCod'] = $vTipTic['tticodxx'] + 1;
        }
      break;
      default:
        /***** Validando Codigo exista *****/
        $qTipTic  = "SELECT tticodxx,ttidesxx ";
        $qTipTic .= "FROM $cAlfa.lpar0158 ";
        $qTipTic .= "WHERE ";
        $qTipTic .= "tticodxx = \"{$_POST['cTtiCod']}\" LIMIT 0,1";
        $xTipTic  = f_MySql("SELECT","",$qTipTic,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qTipTic."~".mysql_num_rows($xTipTic));
        if (mysql_num_rows($xTipTic) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Codigo no existe.\n";
        }
      break;
    }

    if ($_POST['cTtiDes'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "La Descripcion no puede ser vacia.\n ";
    }

    if ($_POST['cReAsig'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Debe seleccionar al menos un responsable.\n ";
    } else {
      // Validando que el usuario exista en estado ACTIVO y tenga la licencia del Modulo Logistica Activa
      $vUsuRes = explode("~", $_POST['cReAsig']);
      for ($i=0; $i < count($vUsuRes); $i++) {
        $qUsuRes  = "SELECT USRIDXXX ";
        $qUsuRes .= "FROM $cAlfa.SIAI0003 ";
        $qUsuRes .= "WHERE ";
        $qUsuRes .= "USRIDXXX = \"{$vUsuRes[$i]}\" AND ";
        $qUsuRes .= "USRMOPLO = \"1\" AND ";
        $qUsuRes .= "REGESTXX = \"ACTIVO\" LIMIT 0,1";
        $xUsuRes = f_MySql("SELECT", "", $qUsuRes, $xConexion01, "");
        if (mysql_num_rows($xUsuRes) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Usuario {$vUsuRes[$i]} no existe o no tiene la licencia del Modulo Logistica Activa.\n";
        }
      }
    }
  break;
  case "CAMBIAESTADO":
    if ($_POST['cTtiCod'] == "") {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Codigo no puede ser vacio.\n";
    } else {
      // Construcción de la consulta SQL
      $qTipTic  = "SELECT tticodxx, ttidesxx, regestxx ";
      $qTipTic .= "FROM $cAlfa.lpar0158 ";
      $qTipTic .= "WHERE tticodxx = '" . $_POST['cTtiCod'] . "' LIMIT 1";
      // Ejecución de la consulta usando f_MySql
      $xTipTic = f_MySql("SELECT", "", $qTipTic, $xConexion01, "");
      if (mysql_num_rows($xTipTic) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Codigo no existe.\n";
      } else {
        $vTipTic = mysql_fetch_array($xTipTic);
        $cNueEst = ($vTipTic['regestxx'] == "ACTIVO") ? "INACTIVO" : "ACTIVO";
      }
    }
  break;
  case "ELIMINAR":
    for ($cAnio = date('Y'); $cAnio >= $vSysStr['logistica_ano_instalacion_modulo']; $cAnio--) {
      $qTickets = "SELECT * "; // Aquí debes especificar las columnas que deseas seleccionar
      $qTickets .= "FROM $cAlfa.ltid$cAnio ";
      $qTickets .= "WHERE $cAlfa.ltid$cAnio.tticodxx = " . $_POST['cTtiCod'];
      $xTickets = f_MySql("SELECT", "", $qTickets, $xConexion01, "");
      if (mysql_num_rows($xTickets) > 0) {
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "El Tipo de Ticket [" . $_POST['cTtiCod'] . "] ya ha sido utilizada en la creacion de un ticket.\n";
        break;
      }
    }
  break;
}	/***** Fin de la Validacion *****/

/***** Ahora Empiezo a Grabar *****/
/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "NUEVO":

      //guado en la tabla tipos de ticket
      $qInsert	= array(array('NAME'=>'tticodxx','VALUE'=>str_pad($_POST['cTtiCod'], 3, "0", STR_PAD_LEFT),'CHECK'=>'NO'),
                        array('NAME'=>'ttidesxx','VALUE'=>trim(strtoupper($_POST['cTtiDes']))             ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim($_COOKIE['kUsrId'])                        ,'CHECK'=>'SI'),
                        array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')						                        ,'CHECK'=>'SI'),
                        array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                                ,'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')						                        ,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                                ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                                        ,'CHECK'=>'SI'));

      if (!f_MySql("INSERT","lpar0158",$qInsert,$xConexion01,$cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error Guardando Datos.\n";
      }

      //guardo en la tabla tipos de ticket con responsable asignado
      $vUsuRes = explode("~", $_POST['cReAsig']);
      $nError = 0;
      for ($i=0; $i < count($vUsuRes); $i++) {
        $qInsert	= array(array('NAME'=>'tticodxx','VALUE'=>str_pad($_POST['cTtiCod'], 3, "0", STR_PAD_LEFT),'CHECK'=>'NO'),
                          array('NAME'=>'ttiusrxx','VALUE'=>trim($vUsuRes[$i])                              ,'CHECK'=>'SI'),
                          array('NAME'=>'regusrxx','VALUE'=>trim($_COOKIE['kUsrId'])                        ,'CHECK'=>'SI'),
                          array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')						                        ,'CHECK'=>'SI'),
                          array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                                ,'CHECK'=>'SI'),
                          array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')						                        ,'CHECK'=>'SI'),
                          array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                                ,'CHECK'=>'SI'),
                          array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                                        ,'CHECK'=>'SI'));

        if (!f_MySql("INSERT","lpar0159",$qInsert,$xConexion01,$cAlfa)) {
          $nError = 1;
        }
      }
      if ($nError == 1) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error Guardando Datos.\n";
      }

    break;
    case "EDITAR":
      /***** Fin de Validaciones Particulares *****/
      $qUpdate	= array(array('NAME'=>'ttidesxx','VALUE'=>trim(strtoupper($_POST['cTtiDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'tticodxx','VALUE'=>trim($_POST['cTtiCod'])             ,'CHECK'=>'WH'));

      if (!f_MySql("UPDATE","lpar0158",$qUpdate,$xConexion01,$cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error Actualizar Datos.\n";
      }

      // Elimina para guardar nuevamente
      $qDelete =  array(array('NAME'=>'tticodxx','VALUE'=>trim($_POST['cTtiCod'])             ,'CHECK'=>'WH'));

      if (!f_MySql("DELETE","lpar0159",$qDelete,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Eliminar la Prioridad de Ticket ".$_POST['cTtiCod'].",\n";
      }

      //guardo en la tabla tipos de ticket con responsable asignado
      $vUsuRes = explode("~", $_POST['cReAsig']);
      $nError = 0;
      for ($i=0; $i < count($vUsuRes); $i++) {
        $qInsert	= array(array('NAME'=>'tticodxx','VALUE'=>trim($_POST['cTtiCod']) ,'CHECK'=>'NO'),
                        array('NAME'=>'ttiusrxx','VALUE'=>trim($vUsuRes[$i])        ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim($_COOKIE['kUsrId'])  ,'CHECK'=>'SI'),
                        array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')						  ,'CHECK'=>'SI'),
                        array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		          ,'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')						  ,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		          ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                  ,'CHECK'=>'SI'));

        if (!f_MySql("INSERT","lpar0159",$qInsert,$xConexion01,$cAlfa)) {
          $nError = 1;
        }
      }
      if ($nError == 1) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error Guardando Datos.\n";
      }

    break;
    case "CAMBIAESTADO":
        $qUpdate  = array(array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                          array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
                          array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                          array('NAME'=>'regestxx','VALUE'=>$cNueEst                            ,'CHECK'=>'SI'),
                          array('NAME'=>'tticodxx','VALUE'=>trim($_POST['cTtiCod'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0158",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error Actualizar Estado.\n";
        }
    break;
    case "ELIMINAR":
      $cTtiCod = isset($_POST['cTtiCod']) ? trim($_POST['cTtiCod']) : '';

      // Primero, eliminar de la tabla lpar0159
      $qDelete1 = array(
        array('NAME' => 'tticodxx', 'VALUE' => $cTtiCod, 'CHECK' => 'WH')
      );

      if (!f_MySql("DELETE", "lpar0159", $qDelete1, $xConexion01, $cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Error al eliminar el Tipo de Ticket usuario asociado $cTtiCod de la tabla lpar0159.\n";
      }

      // Luego, eliminar de la tabla lpar0158
      $qDelete2 = array(
        array('NAME' => 'tticodxx', 'VALUE' => $cTtiCod, 'CHECK' => 'WH')
      );

      if (!f_MySql("DELETE", "lpar0158", $qDelete2, $xConexion01, $cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Error al eliminar el Tipo de Ticket $cTtiCod de la tabla lpar0158.\n";
      }
      
    break;
  }
}

if ($nSwitch == 0) {
  if($_COOKIE['kModo']=="NUEVO"){
    f_Mensaje(__FILE__,__LINE__,"El Registro se Cargo Con Exito");
  }
  if($_COOKIE['kModo']=="CAMBIAESTADO"){
    f_Mensaje(__FILE__,__LINE__,"El Registro Cambio de Estado Con Exito");
  }
  if($_COOKIE['kModo']=="ELIMINAR"){
    f_Mensaje(__FILE__,__LINE__,"El Registro se Elimino Con Exito");
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

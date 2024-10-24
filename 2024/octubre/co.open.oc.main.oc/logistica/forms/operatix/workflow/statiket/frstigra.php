<?php
namespace openComex;
/**
 * Graba Orgainzacion de Ventas.
 * --- Descripcion: Permite Guardar una Nuvo Status Ticket.
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
        // Trayendo el ultimo consecutivo
        $qDatSta  = "SELECT sticodxx ";
        $qDatSta .= "FROM $cAlfa.lpar0157 ";
        $qDatSta .= "ORDER BY ABS(sticodxx) DESC LIMIT 0,1";
        $xDatSta  = f_MySql("SELECT", "", $qDatSta, $xConexion01, "");
        $_POST['cStiCod'] = 1;
        if (mysql_num_rows($xDatSta) > 0) {
          $vDatSta = mysql_fetch_array($xDatSta);
          $_POST['cStiCod'] = $vDatSta['sticodxx'] + 1;
        }
      break;
      default:
        /***** Validando Codigo exista *****/
        $qDatSta  = "SELECT sticodxx,stidesxx ";
        $qDatSta .= "FROM $cAlfa.lpar0157 ";
        $qDatSta .= "WHERE ";
        $qDatSta .= "sticodxx = \"{$_POST['cStiCod']}\" LIMIT 0,1";
        $xDatSta  = f_MySql("SELECT","",$qDatSta,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qDatSta."~".mysql_num_rows($xDatSta));
        if (mysql_num_rows($xDatSta) == 0) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "El Codigo no existe.\n";
        }
      break;
    }

    if ($_POST['StiDes'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "La Descripcion del status ticket no puede ser vacia..\n ";
    }

    // Validando que solo exista un tipo de estado para APERTURA y CIERRE
    if ($_POST['cStaPti'] != "") {
      $qDatSta  = "SELECT sticodxx, stidesxx ";
      $qDatSta .= "FROM $cAlfa.lpar0157 ";
      $qDatSta .= "WHERE ";
      $qDatSta .= "stitipxx = \"{$_POST['cStaPti']}\" ";
      if ($_COOKIE['kModo'] == "EDITAR") {
        $qDatSta .= "AND sticodxx != \"{$_POST['cStiCod']}\" "; 
      }
      $qDatSta .= "LIMIT 1";
      $xDatSta = f_MySql("SELECT", "", $qDatSta, $xConexion01, "");
      if (mysql_num_rows($xDatSta) > 0) {
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "Ya existe un estado con el tipo de estado {$_POST['cStaPti']}.\n";
      }
    }
  break;
  case "CAMBIAESTADO":
    if ($_POST['cStiCod'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El Codigo no puede ser vacio.\n";
    } else {
        // Construcción de la consulta SQL
        $qDatSta  = "SELECT sticodxx, stidesxx, regestxx ";
        $qDatSta .= "FROM $cAlfa.lpar0157 ";
        $qDatSta .= "WHERE sticodxx = '" . $_POST['cStiCod'] . "' LIMIT 1";
        // Ejecución de la consulta usando f_MySql
        $xDatSta = f_MySql("SELECT", "", $qDatSta, $xConexion01, "");
        /***** Validando Codigo exista *****/
        if (mysql_num_rows($xDatSta) == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
            $cMsj .= "El Codigo no existe.\n";
        } else {
            $vDatSta = mysql_fetch_array($xDatSta);
            $cNueEst = ($vDatSta['regestxx'] == "ACTIVO") ? "INACTIVO" : "ACTIVO";
        }
    }
  break;
  case "ELIMINAR":
    for ($cAnio = date('Y'); $cAnio >= $vSysStr['logistica_ano_instalacion_modulo']; $cAnio--) {
      $qTickets = "SELECT * "; // Aquí debes especificar las columnas que deseas seleccionar
      $qTickets .= "FROM $cAlfa.ltid$cAnio ";
      $qTickets .= "WHERE $cAlfa.ltid$cAnio.sticodxx = " . $_POST['cStiCod'];
      $xTickets = f_MySql("SELECT", "", $qTickets, $xConexion01, "");
      if (mysql_num_rows($xTickets) > 0) {
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "La Estado Ticket [" . $_POST['cStiCod'] . "] ya ha sido utilizada en la creacion de un ticket.\n";
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
      $qInsert	= array(array('NAME'=>'sticodxx','VALUE'=>str_pad($_POST['cStiCod'], 3, "0", STR_PAD_LEFT),'CHECK'=>'NO'),
                        array('NAME'=>'stidesxx','VALUE'=>trim(strtoupper($_POST['StiDes']))              ,'CHECK'=>'SI'),
                        array('NAME'=>'stitipxx','VALUE'=>trim(strtoupper($_POST['cStaPti']))             ,'CHECK'=>'NO'),
                        array('NAME'=>'regusrxx','VALUE'=>trim($_COOKIE['kUsrId'])                        ,'CHECK'=>'SI'),
                        array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')						                        ,'CHECK'=>'SI'),
                        array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                                ,'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')						                        ,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                                ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                                        ,'CHECK'=>'SI'));

      if (!f_MySql("INSERT","lpar0157",$qInsert,$xConexion01,$cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error Guardando Datos.\n";
      }
    break;
    case "EDITAR":
      /***** Fin de Validaciones Particulares *****/
      $qUpdate	= array(array('NAME'=>'stidesxx','VALUE'=>trim(strtoupper($_POST['StiDes']))  ,'CHECK'=>'SI'),
                        array('NAME'=>'stitipxx','VALUE'=>trim(strtoupper($_POST['cStaPti']))             ,'CHECK'=>'NO'),
                        array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'sticodxx','VALUE'=>trim($_POST['cStiCod'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0157",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error Actualizar Datos.\n";
        }
    break;
    case "CAMBIAESTADO":
        $qUpdate  = array(array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                          array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
                          array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                          array('NAME'=>'regestxx','VALUE'=>$cNueEst                            ,'CHECK'=>'SI'),
                          array('NAME'=>'sticodxx','VALUE'=>trim($_POST['cStiCod'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0157",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error Actualizar Estado.\n";
        }
    break;
    case "ELIMINAR":
        $qDelete =  array(array('NAME'=>'sticodxx','VALUE'=>trim($_POST['cStiCod'])             ,'CHECK'=>'WH'));

        if (!f_MySql("DELETE","lpar0157",$qDelete,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Eliminar la Prioridad de Ticket ".$_POST['cStiCod'].",\n";
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

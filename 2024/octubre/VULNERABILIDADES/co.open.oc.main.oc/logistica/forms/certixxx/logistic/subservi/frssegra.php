<?php
  namespace openComex;
/**
 * Graba Sub Servicios
 * --- Descripcion: Permite Guardar un Nuevo Sub Servicios.
 * @author diego.cortes@openits.co
 * @package openComex
 * @version 001
 */
include("../../../../../financiero/libs/php/utility.php");

$nSwitch = "0"; // Switch para Vericar la Validacion de Datos
$cMsj = "";

switch ($_COOKIE['kModo']) {
  case "NUEVO":
  case "EDITAR":
    
    //Eliminando espacios en blanco
    $_POST['cSSerId'] = trim($_POST['cSSerId']);
    $_POST['cSSerId'] = trim($_POST['cSSerId']);

    if ($_POST['cSerSap'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Codigo SAP del Servicio no puede ser vacio.\n";
    } else {
      $qServix  = "SELECT sersapxx ";
      $qServix .= "FROM $cAlfa.lpar0011 ";
      $qServix .= "WHERE ";
      $qServix .= "sersapxx = \"{$_POST['cSerSap']}\" LIMIT 0,1";
      $xServix  = f_MySql("SELECT","",$qServix,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qServix."~".mysql_num_rows($xServix)."~".mysql_error($xConexion01));

      /***** Validando Codigo exista *****/
      if (mysql_num_rows($xServix) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Codigo SAP del Servicio no existe.\n";
      }
    }

    if ($_POST['cSSeDes'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "La Descripcion del Sub Servicio no puede ser vacia.\n ";
    }

    if ($nSwitch == 0) {
      $qSubSer  = "SELECT sersapxx,subdesxx ";
      $qSubSer .= "FROM $cAlfa.lpar0012 ";
      $qSubSer .= "WHERE ";
      $qSubSer .= "sersapxx = \"{$_POST['cSerSap']}\" AND ";
      $qSubSer .= "subidxxx = \"{$_POST['cSSerId']}\" LIMIT 0,1";
      $xSubSer  = f_MySql("SELECT","",$qSubSer,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qSubSer."~".mysql_num_rows($xSubSer)."~".mysql_error($xConexion01));

      switch ($_COOKIE['kModo']) {
        case "NUEVO":
          /***** Validando Codigo no exista *****/
          if (mysql_num_rows($xSubSer) > 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Codigo SAP ya existe para el Codigo SAP del Servicio seleccionado.\n";
          }
        break;
        default:
          /***** Validando Codigo exista *****/
          if (mysql_num_rows($xSubSer) == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Codigo SAP NO existe para el Codigo SAP del Servicio seleccionado.\n";
          }
        break;
      }
    }
  break;
  case "CAMBIAESTADO":
    if ($_POST['cSerSap'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Codigo SAP del Servicio no puede ser vacio.\n";
    }

    if ($_POST['cSSerId'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Id no puede ser vacio.\n";
    }
    
    if ($nSwitch == 0) {
      $qSubSer  = "SELECT regestxx ";
      $qSubSer .= "FROM $cAlfa.lpar0012 ";
      $qSubSer .= "WHERE ";
      $qSubSer .= "sersapxx = \"{$_POST['cSerSap']}\" AND ";
      $qSubSer .= "subidxxx = \"{$_POST['cSSerId']}\" LIMIT 0,1";
      $xSubSer  = f_MySql("SELECT","",$qSubSer,$xConexion01,"");

      /***** Validando Codigo exista *****/
      if (mysql_num_rows($xSubSer) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Id [{$_POST['cSSerId']}] NO existe para el Codigo SAP del Servicio [{$_POST['cSerSap']}].\n";
      } else {
        $vSubSer = mysql_fetch_array($xSubSer);
        $cNueEst = ($vSubSer['regestxx'] == "ACTIVO") ? "INACTIVO" : "ACTIVO";
      }
    }
  break;
}	/***** Fin de la Validacion *****/

/***** Ahora Empiezo a Grabar *****/
/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      $qInsert	= array(array('NAME'=>'sersapxx','VALUE'=>trim($_POST['cSerSap'])             ,'CHECK'=>'SI'),
                        array('NAME'=>'subidxxx','VALUE'=>trim($_POST['cSSerId'])             ,'CHECK'=>'SI'),
                        array('NAME'=>'subdesxx','VALUE'=>trim(strtoupper($_POST['cSSeDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim($_COOKIE['kUsrId'])            ,'CHECK'=>'SI'),
                        array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')						            ,'CHECK'=>'SI'),
                        array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')						            ,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                            ,'CHECK'=>'SI'));

      if (!f_MySql("INSERT","lpar0012",$qInsert,$xConexion01,$cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error Guardando Datos.\n";
      }
    break;
    case "EDITAR":
      /***** Fin de Validaciones Particulares *****/
      $qUpdate	= array(array('NAME'=>'subdesxx','VALUE'=>trim(strtoupper($_POST['cSSeDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'sersapxx','VALUE'=>trim($_POST['cSerSap'])             ,'CHECK'=>'WH'),
                        array('NAME'=>'subidxxx','VALUE'=>trim($_POST['cSSerId'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0012",$qUpdate,$xConexion01,$cAlfa)) {
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
                          array('NAME'=>'sersapxx','VALUE'=>trim($_POST['cSerSap'])             ,'CHECK'=>'WH'),
                          array('NAME'=>'subidxxx','VALUE'=>trim($_POST['cSSerId'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0012",$qUpdate,$xConexion01,$cAlfa)) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error Actualizar Estado.\n";
        }
    break;
  }
}

if ($nSwitch == 0) {
  if($_COOKIE['kModo']!="CAMBIAESTADO"){
    f_Mensaje(__FILE__,__LINE__,"El Registro se cargo con Exito");
  }
  if($_COOKIE['kModo']=="CAMBIAESTADO"){
    f_Mensaje(__FILE__,__LINE__,"El Registro Cambio de Estado Con Exito");
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

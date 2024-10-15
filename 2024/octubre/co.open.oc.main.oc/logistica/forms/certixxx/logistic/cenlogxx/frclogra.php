<?php
  namespace openComex;
/**
 * Graba Centro Logístico.
 * --- Descripcion: Permite Guardar un Nuevo Centro Logístico.
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
    $_POST['cCLoSap'] = trim($_POST['cCLoSap']);

    if ($_POST['cOrvSap'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Codigo SAP de la Organizacion de Ventas no puede ser vacio.\n";
    } else {
      $qOrgVen  = "SELECT orvsapxx ";
      $qOrgVen .= "FROM $cAlfa.lpar0001 ";
      $qOrgVen .= "WHERE ";
      $qOrgVen .= "orvsapxx = \"{$_POST['cOrvSap']}\" LIMIT 0,1";
      $xOrgVen  = f_MySql("SELECT","",$qOrgVen,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qOrgVen."~".mysql_num_rows($xOrgVen)."~".mysql_error($xConexion01));

      /***** Validando Codigo exista *****/
      if (mysql_num_rows($xOrgVen) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Codigo SAP de la Organizacion de Ventas no existe.\n";
      }
    }

    if ($_POST['cCLoSap'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Codigo SAP no puede ser vacio.\n";
    } else {
      // Validando que se numerico
      
      if (!preg_match("/^[[:digit:]]+$/", $_POST['cCLoSap'])) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Codigo SAP debe ser numerico.\n";
      }

      if (strlen($_POST['cCLoSap']) != 4) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La longitud del Codigo SAP debe ser 4.\n";
      }
    }

    if ($_POST['cCloDes'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "La Descripcion del Centro Logistico no puede ser vacia.\n ";
    }

    if ($nSwitch == 0) {
      $qCenLog  = "SELECT ";
      $qCenLog .= "orvsapxx, ";
      $qCenLog .= "ofvsapxx, ";
      $qCenLog .= "closapxx ";
      $qCenLog .= "FROM $cAlfa.lpar0003 ";
      $qCenLog .= "WHERE ";
      $qCenLog .= "orvsapxx = \"{$_POST['cOrvSap']}\" AND ";
      $qCenLog .= "ofvsapxx = \"{$_POST['cOfvSap']}\" AND ";
      $qCenLog .= "closapxx = \"{$_POST['cCLoSap']}\" LIMIT 0,1";
      $xCenLog  = f_MySql("SELECT","",$qCenLog,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCenLog."~".mysql_num_rows($xCenLog)."~".mysql_error($xConexion01));

      switch ($_COOKIE['kModo']) {
        case "NUEVO":
          /***** Validando Codigo no exista *****/
          if (mysql_num_rows($xCenLog) > 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Codigo SAP ya existe para el Codigo SAP de la Organizacion de Ventas seleccionado.\n";
          }
        break;
        default:
          /***** Validando Codigo exista *****/
          if (mysql_num_rows($xCenLog) == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Codigo SAP NO existe para el Codigo SAP de la Organizacion de Ventas seleccionado.\n";
          }
        break;
      }
    }
  break;
  case "CAMBIAESTADO":
    if ($_POST['cOrvSap'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El Codigo SAP de la Organizacion de Ventas no puede ser vacio.\n ";
    }

    if ($_POST['cCLoSap'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El Codigo SAP no puede ser vacio.\n";
    }
    
    if ($nSwitch == 0) {
      $qCenLog  = "SELECT regestxx ";
      $qCenLog .= "FROM $cAlfa.lpar0003 ";
      $qCenLog .= "WHERE ";
      $qCenLog .= "orvsapxx = \"{$_POST['cOrvSap']}\" AND ";
      $qCenLog .= "closapxx = \"{$_POST['cCLoSap']}\" LIMIT 0,1";
      $xCenLog  = f_MySql("SELECT","",$qCenLog,$xConexion01,"");

      /***** Validando Codigo exista *****/
      if (mysql_num_rows($xCenLog) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Codigo SAP [{$_POST['cCLoSap']}] NO existe para el Codigo SAP del Centro Logistico [{$_POST['cCLoSap']}].\n";
      } else {
        $vCLoVen = mysql_fetch_array($xCenLog);
        $cNueEst = ($vCLoVen['regestxx'] == "ACTIVO") ? "INACTIVO" : "ACTIVO";
      }
    }
  break;
}	/***** Fin de la Validacion *****/

/***** Ahora Empieza a Grabar *****/
/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      $qInsert	= array(array('NAME'=>'orvsapxx','VALUE'=>trim($_POST['cOrvSap'])             ,'CHECK'=>'SI'),
                        array('NAME'=>'ofvsapxx','VALUE'=>trim($_POST['cOfvSap'])             ,'CHECK'=>'SI'),
                        array('NAME'=>'closapxx','VALUE'=>trim($_POST['cCLoSap'])             ,'CHECK'=>'SI'),
                        array('NAME'=>'clodesxx','VALUE'=>trim(strtoupper($_POST['cCloDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim($_COOKIE['kUsrId'])            ,'CHECK'=>'SI'),
                        array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')						            ,'CHECK'=>'SI'),
                        array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')						            ,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                            ,'CHECK'=>'SI'));

      if (!f_MySql("INSERT","lpar0003",$qInsert,$xConexion01,$cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error Guardando Datos.\n";
      }
    break;
    case "EDITAR":
      /***** Fin de Validaciones Particulares *****/
      $qUpdate	= array(array('NAME'=>'clodesxx','VALUE'=>trim(strtoupper($_POST['cCloDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'orvsapxx','VALUE'=>trim($_POST['cOrvSap'])             ,'CHECK'=>'WH'),
                        array('NAME'=>'ofvsapxx','VALUE'=>trim($_POST['cOfvSap'])             ,'CHECK'=>'WH'),
                        array('NAME'=>'closapxx','VALUE'=>trim($_POST['cCLoSap'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0003",$qUpdate,$xConexion01,$cAlfa)) {
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
                          array('NAME'=>'orvsapxx','VALUE'=>trim($_POST['cOrvSap'])             ,'CHECK'=>'WH'),
                          array('NAME'=>'ofvsapxx','VALUE'=>trim($_POST['cOfvSap'])             ,'CHECK'=>'WH'),
                          array('NAME'=>'closapxx','VALUE'=>trim($_POST['cCLoSap'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0003",$qUpdate,$xConexion01,$cAlfa)) {
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

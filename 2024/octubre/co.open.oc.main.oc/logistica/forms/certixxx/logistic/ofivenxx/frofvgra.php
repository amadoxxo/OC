<?php
  namespace openComex;
/**
 * Graba Orgainzacion de Ventas.
 * --- Descripcion: Permite Guardar una Nueva Organizacion de Ventas.
 * @author johana.arboleda@openits.co
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
    $_POST['cOfvSap'] = trim($_POST['cOfvSap']);
    $_POST['cOfvSap'] = trim($_POST['cOfvSap']);

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

    if ($_POST['cOfvSap'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Codigo SAP no puede ser vacio.\n";
    } else {
      // Validando que se numerico
      if (!preg_match("/^[[:digit:]]+$/", $_POST['cOfvSap'])) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Codigo SAP debe ser numerico.\n";
      }

      if (strlen($_POST['cOfvSap']) != 4) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La longitud del Codigo SAP debe ser 4.\n";
      }
    }

    if ($_POST['cOfvDes'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "La Descripcion de la Oficina de Ventas no puede ser vacia.\n ";
    }

    if ($nSwitch == 0) {
      $qOfiVen  = "SELECT orvsapxx,ofvdesxx ";
      $qOfiVen .= "FROM $cAlfa.lpar0002 ";
      $qOfiVen .= "WHERE ";
      $qOfiVen .= "orvsapxx = \"{$_POST['cOrvSap']}\" AND ";
      $qOfiVen .= "ofvsapxx = \"{$_POST['cOfvSap']}\" LIMIT 0,1";
      $xOfiVen  = f_MySql("SELECT","",$qOfiVen,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qOfiVen."~".mysql_num_rows($xOfiVen)."~".mysql_error($xConexion01));

      switch ($_COOKIE['kModo']) {
        case "NUEVO":
          /***** Validando Codigo no exista *****/
          if (mysql_num_rows($xOfiVen) > 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Codigo SAP ya existe para el Codigo SAP de la Organizacion de Ventas seleccionado.\n";
          }
        break;
        default:
          /***** Validando Codigo exista *****/
          if (mysql_num_rows($xOfiVen) == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Codigo SAP NO existe para el Codigo SAP de la Organizacion de Ventas seleccionado.\n";
          }
        break;
      }
    }
  break;
  case "CAMBIAESTADO":
    if ($_POST['cOrvSap'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Codigo SAP de la Organizacion de Ventas no puede ser vacio.\n";
    }

    if ($_POST['cOfvSap'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Codigo SAP no puede ser vacio.\n";
    }
    
    if ($nSwitch == 0) {
      $qOfiVen  = "SELECT regestxx ";
      $qOfiVen .= "FROM $cAlfa.lpar0002 ";
      $qOfiVen .= "WHERE ";
      $qOfiVen .= "orvsapxx = \"{$_POST['cOrvSap']}\" AND ";
      $qOfiVen .= "ofvsapxx = \"{$_POST['cOfvSap']}\" LIMIT 0,1";
      $xOfiVen  = f_MySql("SELECT","",$qOfiVen,$xConexion01,"");

      /***** Validando Codigo exista *****/
      if (mysql_num_rows($xOfiVen) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Codigo SAP [{$_POST['cOfvSap']}] NO existe para el Codigo SAP de la Organizacion de Ventas [{$_POST['cOrvSap']}].\n";
      } else {
        $vOfiVen = mysql_fetch_array($xOfiVen);
        $cNueEst = ($vOfiVen['regestxx'] == "ACTIVO") ? "INACTIVO" : "ACTIVO";
      }
    }
  break;
}	/***** Fin de la Validacion *****/

/***** Ahora Empiezo a Grabar *****/
/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      $qInsert	= array(array('NAME'=>'orvsapxx','VALUE'=>trim($_POST['cOrvSap'])             ,'CHECK'=>'SI'),
                        array('NAME'=>'ofvsapxx','VALUE'=>trim($_POST['cOfvSap'])             ,'CHECK'=>'SI'),
                        array('NAME'=>'ofvdesxx','VALUE'=>trim(strtoupper($_POST['cOfvDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim($_COOKIE['kUsrId'])            ,'CHECK'=>'SI'),
                        array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')						            ,'CHECK'=>'SI'),
                        array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')						            ,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                            ,'CHECK'=>'SI'));

      if (!f_MySql("INSERT","lpar0002",$qInsert,$xConexion01,$cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error Guardando Datos.\n";
      }
    break;
    case "EDITAR":
      /***** Fin de Validaciones Particulares *****/
      $qUpdate	= array(array('NAME'=>'ofvdesxx','VALUE'=>trim(strtoupper($_POST['cOfvDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'orvsapxx','VALUE'=>trim($_POST['cOrvSap'])             ,'CHECK'=>'WH'),
                        array('NAME'=>'ofvsapxx','VALUE'=>trim($_POST['cOfvSap'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0002",$qUpdate,$xConexion01,$cAlfa)) {
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
                          array('NAME'=>'ofvsapxx','VALUE'=>trim($_POST['cOfvSap'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0002",$qUpdate,$xConexion01,$cAlfa)) {
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

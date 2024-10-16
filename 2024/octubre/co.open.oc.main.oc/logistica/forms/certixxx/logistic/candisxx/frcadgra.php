<?php
  namespace openComex;
/**
 * Graba Paramétrica Canal distribución.
 * --- Permite Guardar un Nuevo Canal distribución.
 * @author oscar.perez@openits.co
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
    $_POST['cCdiSap'] = trim($_POST['cCdiSap']);

    if ($_POST['cCdiSap'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El codigo SAP no puede ser vacio.\n";
    } else {
      // Validando que se numerico
      if (!preg_match("/^[[:digit:]]+$/", $_POST['cCdiSap'])) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Codigo SAP debe ser numerico.\n";
      }

      if (strlen($_POST['cCdiSap']) != 2) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La longitud del codigo SAP debe ser 2.\n";
      }
      
      $qCanDis  = "SELECT cdisapxx,cdidesxx ";
      $qCanDis .= "FROM $cAlfa.lpar0008 ";
      $qCanDis .= "WHERE ";
      $qCanDis .= "cdisapxx = \"{$_POST['cCdiSap']}\" LIMIT 0,1";
      $xCanDis  = f_MySql("SELECT","",$qCanDis,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCanDis."~".mysql_num_rows($xCanDis)."~".mysql_error($xConexion01));

      switch ($_COOKIE['kModo']) {
        case "NUEVO":
          /***** Validando Codigo no exista *****/
          if (mysql_num_rows($xCanDis) > 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El codigo SAP ya existe.\n";
          }
        break;
        default:
          /***** Validando Codigo exista *****/
          if (mysql_num_rows($xCanDis) == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El codigo SAP no existe.\n";
          }
        break;
      }
    }

    if ($_POST['cCdiDes'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "La Descripcion del Canal distribucion no puede ser vacia.\n ";
    }
  break;
  case "CAMBIAESTADO":
    if ($_POST['cCdiSap'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El codigo SAP no puede ser vacio.\n";
    } else {
      $qCanDis  = "SELECT cdisapxx,cdidesxx,regestxx ";
      $qCanDis .= "FROM $cAlfa.lpar0008 ";
      $qCanDis .= "WHERE ";
      $qCanDis .= "cdisapxx = \"{$_POST['cCdiSap']}\"LIMIT 0,1";
      $xCanDis  = f_MySql("SELECT","",$qCanDis,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCanDis."~".mysql_num_rows($xCanDis)."~".mysql_error($xConexion01));

      /***** Validando Codigo exista *****/
      if (mysql_num_rows($xCanDis) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El codigo SAP no existe.\n";
      } else {
        $vCanDis = mysql_fetch_array($xCanDis);
        $cNueEst = ($vCanDis['regestxx'] == "ACTIVO") ? "INACTIVO" : "ACTIVO";
      }
    }
  break;
}	/***** Fin de la Validacion *****/

/***** Ahora Empiezo a Grabar *****/
/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      $qInsert	= array(array('NAME'=>'cdisapxx','VALUE'=>trim($_POST['cCdiSap'])             ,'CHECK'=>'NO'),
                        array('NAME'=>'cdidesxx','VALUE'=>trim(strtoupper($_POST['cCdiDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim($_COOKIE['kUsrId'])             ,'CHECK'=>'SI'),
                        array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')						             ,'CHECK'=>'SI'),
                        array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                     ,'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')						             ,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                     ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                             ,'CHECK'=>'SI'));

      if (!f_MySql("INSERT","lpar0008",$qInsert,$xConexion01,$cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error Guardando Datos.\n";
      }
    break;
    case "EDITAR":
      /***** Fin de Validaciones Particulares *****/
      $qUpdate	= array(array('NAME'=>'cdidesxx','VALUE'=>trim(strtoupper($_POST['cCdiDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'cdisapxx','VALUE'=>trim($_POST['cCdiSap'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0008",$qUpdate,$xConexion01,$cAlfa)) {
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
                          array('NAME'=>'cdisapxx','VALUE'=>trim($_POST['cCdiSap'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0008",$qUpdate,$xConexion01,$cAlfa)) {
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

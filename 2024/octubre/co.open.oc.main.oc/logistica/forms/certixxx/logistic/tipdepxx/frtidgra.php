<?php
  namespace openComex;
/**
 * Graba Paramétrica Tipo de Depósito
 * --- Permite Guardar un Nuevo Tipo de Depósito
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
    $_POST['cTdeId'] = trim($_POST['cTdeId']);

    if ($_POST['cTdeId'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El Id no puede ser vacio.\n";
    } else {
      //validando que sea alfanumerico y/o tenga un guion
      if (!preg_match("/^[a-zA-Z0-9]+$/", $_POST['cTdeId'])) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Id Debe Contener Letras y/o Numeros.\n";
      }

      if (strlen($_POST['cTdeId']) != 2) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La longitud del Id debe ser 2.\n";
      }
      
      $qTipDep  = "SELECT tdeidxxx,tdedesxx ";
      $qTipDep .= "FROM $cAlfa.lpar0007 ";
      $qTipDep .= "WHERE ";
      $qTipDep .= "tdeidxxx = \"{$_POST['cTdeId']}\" LIMIT 0,1";
      $xTipDep  = f_MySql("SELECT","",$qTipDep,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qTipDep."~".mysql_num_rows($xTipDep)."~".mysql_error($xConexion01));

      switch ($_COOKIE['kModo']) {
        case "NUEVO":
          /***** Validando Codigo no exista *****/
          if (mysql_num_rows($xTipDep) > 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Id ya existe.\n";
          }
        break;
        default:
          /***** Validando Codigo exista *****/
          if (mysql_num_rows($xTipDep) == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El id no existe.\n";
          }
        break;
      }
    }

    if ($_POST['cTdeDes'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "La Descripcion de el Tipo de Deposito no puede ser vacia.\n ";
    }
  break;
  case "CAMBIAESTADO":
    if ($_POST['cTdeId'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El ID no puede ser vacio.\n";
    } else {
      $qTipDep  = "SELECT tdeidxxx,tdedesxx,regestxx ";
      $qTipDep .= "FROM $cAlfa.lpar0007 ";
      $qTipDep .= "WHERE ";
      $qTipDep .= "tdeidxxx = \"{$_POST['cTdeId']}\"LIMIT 0,1";
      $xTipDep  = f_MySql("SELECT","",$qTipDep,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qTipDep."~".mysql_num_rows($xTipDep)."~".mysql_error($xConexion01));

      /***** Validando Codigo exista *****/
      if (mysql_num_rows($xTipDep) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Id no existe.\n";
      } else {
        $vTipDep = mysql_fetch_array($xTipDep);
        $cNueEst = ($vTipDep['regestxx'] == "ACTIVO") ? "INACTIVO" : "ACTIVO";
      }
    }
  break;
}	/***** Fin de la Validacion *****/

/***** Ahora Empiezo a Grabar *****/
/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      $qInsert	= array(array('NAME'=>'tdeidxxx','VALUE'=>trim($_POST['cTdeId'])             ,'CHECK'=>'NO'),
                        array('NAME'=>'tdedesxx','VALUE'=>trim(strtoupper($_POST['cTdeDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim($_COOKIE['kUsrId'])             ,'CHECK'=>'SI'),
                        array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')						             ,'CHECK'=>'SI'),
                        array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                     ,'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')						             ,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                     ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                             ,'CHECK'=>'SI'));

      if (!f_MySql("INSERT","lpar0007",$qInsert,$xConexion01,$cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error Guardando Datos.\n";
      }
    break;
    case "EDITAR":
      /***** Fin de Validaciones Particulares *****/
      $qUpdate	= array(array('NAME'=>'tdedesxx','VALUE'=>trim(strtoupper($_POST['cTdeDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'tdeidxxx','VALUE'=>trim($_POST['cTdeId'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0007",$qUpdate,$xConexion01,$cAlfa)) {
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
                          array('NAME'=>'tdeidxxx','VALUE'=>trim($_POST['cTdeId'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0007",$qUpdate,$xConexion01,$cAlfa)) {
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

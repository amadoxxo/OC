<?php
  namespace openComex;
/**
 * Graba Paramétrica Objeto Facturable.
 * --- Permite Guardar un Nuevo Objeto Facturable.
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
    $_POST['cObFidx'] = trim($_POST['cObFidx']);

    if ($_POST['cObFidx'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El Id no puede ser vacio.\n";
    } else {
      //validando que sea alfanumerico y/o tenga un guion
      if (!preg_match("/^[a-zA-Z0-9]+$/", $_POST['cObFidx'])) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Id Debe Contener Letras y/o Numeros.\n";
      }

      if (strlen($_POST['cObFidx']) != 4) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La longitud del Id debe ser 4.\n";
      }
      
      $qObjFac  = "SELECT obfidxxx,obfdesxx ";
      $qObjFac .= "FROM $cAlfa.lpar0004 ";
      $qObjFac .= "WHERE ";
      $qObjFac .= "obfidxxx = \"{$_POST['cObFidx']}\" LIMIT 0,1";
      $xObjFac  = f_MySql("SELECT","",$qObjFac,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qObjFac."~".mysql_num_rows($xObjFac)."~".mysql_error($xConexion01));

      switch ($_COOKIE['kModo']) {
        case "NUEVO":
          /***** Validando Codigo no exista *****/
          if (mysql_num_rows($xObjFac) > 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Id ya existe.\n";
          }
        break;
        default:
          /***** Validando Codigo exista *****/
          if (mysql_num_rows($xObjFac) == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El Id no existe.\n";
          }
        break;
      }
    }

    if ($_POST['cObfDes'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "La Descripcion de el Objeto Facturable no puede ser vacia.\n ";
    }
  break;
  case "CAMBIAESTADO":
    if ($_POST['cObFidx'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El ID no puede ser vacio.\n";
    } else {
      $qObjFac  = "SELECT obfidxxx,obfdesxx,regestxx ";
      $qObjFac .= "FROM $cAlfa.lpar0004 ";
      $qObjFac .= "WHERE ";
      $qObjFac .= "obfidxxx = \"{$_POST['cObFidx']}\"LIMIT 0,1";
      $xObjFac  = f_MySql("SELECT","",$qObjFac,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qObjFac."~".mysql_num_rows($xObjFac)."~".mysql_error($xConexion01));

      /***** Validando Codigo exista *****/
      if (mysql_num_rows($xObjFac) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Id no existe.\n";
      } else {
        $vObjFac = mysql_fetch_array($xObjFac);
        $cNueEst = ($vObjFac['regestxx'] == "ACTIVO") ? "INACTIVO" : "ACTIVO";
      }
    }
  break;
}	/***** Fin de la Validacion *****/

/***** Ahora Empiezo a Grabar *****/
/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      $qInsert	= array(array('NAME'=>'obfidxxx','VALUE'=>trim($_POST['cObFidx'])             ,'CHECK'=>'NO'),
                        array('NAME'=>'obfdesxx','VALUE'=>trim(strtoupper($_POST['cObfDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim($_COOKIE['kUsrId'])             ,'CHECK'=>'SI'),
                        array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')						             ,'CHECK'=>'SI'),
                        array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                     ,'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')						             ,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                     ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                             ,'CHECK'=>'SI'));

      if (!f_MySql("INSERT","lpar0004",$qInsert,$xConexion01,$cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error Guardando Datos.\n";
      }
    break;
    case "EDITAR":
      /***** Fin de Validaciones Particulares *****/
      $qUpdate	= array(array('NAME'=>'obfdesxx','VALUE'=>trim(strtoupper($_POST['cObfDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'obfidxxx','VALUE'=>trim($_POST['cObFidx'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0004",$qUpdate,$xConexion01,$cAlfa)) {
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
                          array('NAME'=>'obfidxxx','VALUE'=>trim($_POST['cObFidx'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0004",$qUpdate,$xConexion01,$cAlfa)) {
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

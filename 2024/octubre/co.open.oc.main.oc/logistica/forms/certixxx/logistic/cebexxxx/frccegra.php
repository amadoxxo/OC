<?php
  namespace openComex;
/**
 * Graba Codigo Cebe.
 * --- Descripcion: Permite Guardar un Nuevo Codigo Cebe.
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
    $_POST['cCebId'] = trim($_POST['cCebId']);
    $_POST['cCebId'] = trim($_POST['cCebId']);

    if ($_POST['cCebId'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El id no puede ser vacio.\n";
    }

    if ($_POST['cCebPla'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "La Plataforma del Codigo Cebe no puede ser vacia.\n ";
    }

    if ($_POST['cCebMun'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El Municipio del Codigo Cebe no puede ser vacia.\n ";
    }

    if ($_POST['cSecSap'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El Codigo SAP del Sector no puede ser vacio.\n";
    } else {
      $qCodCeb  = "SELECT secsapxx ";
      $qCodCeb .= "FROM $cAlfa.lpar0009 ";
      $qCodCeb .= "WHERE ";
      $qCodCeb .= "secsapxx = \"{$_POST['cSecSap']}\" LIMIT 0,1";
      $xCodCeb  = f_MySql("SELECT","",$qCodCeb,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCodCeb."~".mysql_num_rows($xCodCeb)."~".mysql_error($xConexion01));

      /***** Validando Codigo exista *****/
      if (mysql_num_rows($xCodCeb) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Codigo SAP del Sector no existe.\n";
      }
    }

    if ($_POST['cCebCod'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El codigo Cebe no puede ser vacio.\n";
    } else {
      // Validando que se numerico
      if (!preg_match("/^[[:digit:]]+$/", $_POST['cCebCod'])) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "El Codigo Cebe debe ser numerico.\n";
      }

      if (strlen($_POST['cCebCod']) != 7) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "La longitud del codigo Cebe debe ser 7.\n";
      }
      
      $qCodCeb  = "SELECT cebcodxx ";
      $qCodCeb .= "FROM $cAlfa.lpar0010 ";
      $qCodCeb .= "WHERE ";
      $qCodCeb .= "cebcodxx = \"{$_POST['cCebCod']}\" LIMIT 0,1";
      $xCodCeb  = f_MySql("SELECT","",$qCodCeb,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qCodCeb."~".mysql_num_rows($xCodCeb)."~".mysql_error($xConexion01));

      switch ($_COOKIE['kModo']) {
        case "NUEVO":
          /***** Validando Codigo no exista *****/
          if (mysql_num_rows($xCodCeb) > 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El codigo Cebe ya existe.\n";
          }
        break;
        default:
          /***** Validando Codigo exista *****/
          if (mysql_num_rows($xCodCeb) == 0) {
            $nSwitch = 1;
            $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "El codigo Cebe no existe.\n";
          }
        break;
      }
    }

    if ($_POST['cCebDes'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "La Descripcion del Codigo Cebe no puede ser vacia.\n ";
    }
    
  break;
  case "CAMBIAESTADO":
    if ($_POST['cSecSap'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Codigo SAP del Sector no puede ser vacio.\n";
    }

    if ($_POST['cCebId'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "El Id no puede ser vacio.\n";
    }
    
    if ($nSwitch == 0) {
      $qCodCeb  = "SELECT regestxx ";
      $qCodCeb .= "FROM $cAlfa.lpar0010 ";
      $qCodCeb .= "WHERE ";
      $qCodCeb .= "secsapxx = \"{$_POST['cSecSap']}\" AND ";
      $qCodCeb .= "cebidxxx = \"{$_POST['cCebId']}\" LIMIT 0,1";
      $xCodCeb  = f_MySql("SELECT","",$qCodCeb,$xConexion01,"");

      /***** Validando Codigo exista *****/
      if (mysql_num_rows($xCodCeb) == 0) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Codigo SAP [{$_POST['cCebId']}] NO existe para el Codigo SAP de la Organizacion de Ventas [{$_POST['cSecSap']}].\n";
      } else {
        $vCodCeb = mysql_fetch_array($xCodCeb);
        $cNueEst = ($vCodCeb['regestxx'] == "ACTIVO") ? "INACTIVO" : "ACTIVO";
      }
    }
  break;
}	/***** Fin de la Validacion *****/

/***** Ahora Empiezo a Grabar *****/
/***** Pregunto si el SWITCH Viene en 0 para Poder Seguir *****/
if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "NUEVO":
      $qInsert	= array(array('NAME'=>'secsapxx','VALUE'=>trim($_POST['cSecSap'])             ,'CHECK'=>'SI'),
                        array('NAME'=>'cebidxxx','VALUE'=>trim($_POST['cCebId'])              ,'CHECK'=>'SI'),
                        array('NAME'=>'cebplaxx','VALUE'=>trim(strtoupper($_POST['cCebPla'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'cebmunxx','VALUE'=>trim(strtoupper($_POST['cCebMun'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'cebcodxx','VALUE'=>trim($_POST['cCebCod'])             ,'CHECK'=>'SI'),
                        array('NAME'=>'cebdesxx','VALUE'=>trim(strtoupper($_POST['cCebDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim($_COOKIE['kUsrId'])            ,'CHECK'=>'SI'),
                        array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')						            ,'CHECK'=>'SI'),
                        array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')						            ,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                            ,'CHECK'=>'SI'));

      if (!f_MySql("INSERT","lpar0010",$qInsert,$xConexion01,$cAlfa)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error Guardando Datos.\n";
      }
    break;
    case "EDITAR":
      /***** Fin de Validaciones Particulares *****/
      $qUpdate	= array(array('NAME'=>'secsapxx','VALUE'=>trim($_POST['cSecSap'])             ,'CHECK'=>'SI'),
                        array('NAME'=>'cebplaxx','VALUE'=>trim(strtoupper($_POST['cCebPla'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'cebmunxx','VALUE'=>trim(strtoupper($_POST['cCebMun'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'cebdesxx','VALUE'=>trim(strtoupper($_POST['cCebDes'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'regusrxx','VALUE'=>trim(strtoupper($_COOKIE['kUsrId'])),'CHECK'=>'SI'),
                        array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')												,'CHECK'=>'SI'),
                        array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')		                    ,'CHECK'=>'SI'),
                        array('NAME'=>'regestxx','VALUE'=>trim(strtoupper($_POST['cEstado'])) ,'CHECK'=>'SI'),
                        array('NAME'=>'cebidxxx','VALUE'=>trim($_POST['cCebId'])              ,'CHECK'=>'WH'),
                        array('NAME'=>'cebcodxx','VALUE'=>trim($_POST['cCebCod'])             ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0010",$qUpdate,$xConexion01,$cAlfa)) {
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
                          array('NAME'=>'secsapxx','VALUE'=>trim($_POST['cSecSap'])             ,'CHECK'=>'WH'),
                          array('NAME'=>'cebidxxx','VALUE'=>trim($_POST['cCebId'])              ,'CHECK'=>'WH'));

        if (!f_MySql("UPDATE","lpar0010",$qUpdate,$xConexion01,$cAlfa)) {
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

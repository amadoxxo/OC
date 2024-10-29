<?php
  namespace openComex;
/**
 * Graba Codigo Cebe.
 * @package opencomex
 * @author oscar.perez@openits.co
 * 
 * Variables:
 * @var array   $kDf          Arreglo de Cookies Fijas.
 * @var mixed   $kMysqlHost   Mysql Host.
 * @var mixed   $kMysqlUser   MySql User.
 * @var mixed   $kMysqlPass   MySql Pass.
 * @var mixed   $kMysqlDb     MySql DataBase.
 * @var mixed   $kUser        User OpenComex.
 * @var mixed   $kLicencia    Licencia OpenComex.
 * @var string  $cSystemPath  Path del sistema.
 * @var array   $vBuscar01    Arreglo para buscar caracteres especiales.
 * @var array   $vReempl01    Arreglo para reemplazar caracteres especiales.
 * @var int     $nSwitch      Variable control de errores.
 * @var int     $nError       Variable control de errores para Insert y Update.
 * @var string  $cMsj         Mensajes de error.
 * @var string  $cNomFile
 * @var string  $cFile
 * @var string  $cTabCar
 * @var string  $qNewTab      Consulta para crear tabla temporal de PUC.
 * @var mixed   $xNewTab      Cursor respuesta de la consulta $qNewTab.
 * @var mixed   $xDescTabla   Cursor respuesta a funcion DESCRIBE de MySql.
 * @var array   $xRD          Arreglo con información del cursor $xDescTabla.
 * @var array   $vFields      Arreglo con campos de la tabla.
 * @var string  $cFields      Cadena con campos para cargar a la tabla temporal.
 * @var string  $qLoad        Consulta LOAD para cargar tabla temporal.
 * @var mixed   $xLoad        Cursor respuesta de la consulta $qLoad.
 * @var string  $qDatos       Consulta para ver datos de la tabla temporal.
 * @var mixed   $xDatos       Cursor respuesta de la consulta $qDatos.
 * @var array   $xRD          Arreglo con información del cursor $xDatos.
 * @var int     $nCanIns      Contador de registros insertados
 * @var int     $nCanAct      Contador de registros actualizados.
 * @var string  $qCeb         Consulta a lpar0010.
 * @var mixed   $xCeb         Cursor respuesta de la consulta @qCeb.
 * @var array   $qInsert      Insert a la tabla lpar0010.
 * @var array   $qUpdate      Update a la tabla lpar0010.
 */
 
# Librerias
include("../../../../../financiero/libs/php/utility.php");
include("../../../../../config/config.php");

# Cookie fija
$kDf = explode("~",$_COOKIE["kDatosFijos"]);
$kMysqlHost = $kDf[0];
$kMysqlUser = $kDf[1];
$kMysqlPass = $kDf[2];
$kMysqlDb   = $kDf[3];
$kUser      = $kDf[4];
$kLicencia  = $kDf[5];

$cSystemPath = OC_DOCUMENTROOT;

/**
 * Hacer la conexion a la base de datos
 */
$xConexion01 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);

#Cadenas para reemplazar caracteres espciales
$vBuscar = array(chr(13),chr(10),chr(27),chr(9));
$vReempl = array(" "," "," "," ");

$cBuscar01 = array('"',chr(13),chr(10),chr(27),chr(9));
$cReempl01 = array('\"'," "," "," "," ");

# Control de errores
$nSwitch = 0;
$cMsj    = "\n";

switch ($_COOKIE['kModo']) {
  case "CARGARCEBE":
    # Validando que haya seleccionado un archivo
    if ($_FILES['cArcPla']['name'] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
      $cMsj .= "Debe Digitar un Archivo.\n";
    } else {
      #Copiando el archivo a la carpeta de downloads
      $cNomFile = "/carbcoaut_".$kUser."_".date("YmdHis").".txt";
      switch (PHP_OS) {
        case "Linux" :
          $cFile = "$cSystemPath/opencomex/".$vSysStr['system_download_directory'].$cNomFile;
          break;
        case "WINNT":
          $cFile = "$cSystemPath/opencomex/".$vSysStr['system_download_directory'].$cNomFile;
          break;
      }
      //echo '<pre>';
      //print_r($_FILES);
      //echo $cFile;
      if(!copy($_FILES['cArcPla']['tmp_name'],$cFile)){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error al Copiar Archivo.\n";
      }
    }

    #Creando tabla temporal
    if ($nSwitch == 0) {
      $cTabCar  = fnCadenaAleatoria();
      $qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabCar (";
      $qNewTab .= "lineaidx INT(11)    NOT NULL AUTO_INCREMENT,";
      $qNewTab .= "cebidxxx varchar(4) NOT NULL COMMENT \"Id Tipo de Cebe\",";
      $qNewTab .= "cebplaxx varchar(255) NOT NULL COMMENT \"Plataforma\",";
      $qNewTab .= "secsapxx varchar(2) NOT NULL COMMENT \"Codigo SAP Sector\",";
      $qNewTab .= "secdesxx varchar(2) NOT NULL COMMENT \"Descripcion Sector\",";
      $qNewTab .= "cebcodxx varchar(7) NOT NULL COMMENT \"Codigo\",";
      $qNewTab .= "cebdesxx varchar(255) NOT NULL COMMENT \"Descripcion\",";
      $qNewTab .= "cebmunxx varchar(255) NOT NULL COMMENT \"Municipio\",";
      $qNewTab .= " PRIMARY KEY (LINEAIDX)) ENGINE=MyISAM ";
      //echo $qNewTab;
      // f_Mensaje(__FILE__, __LINE__, $qNewTab);
      $xNewTab = mysql_query($qNewTab,$xConexion01);
      if(!$xNewTab) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "Error al Crear la Tabla Temporal.\n";
      }else{
        # Llenado de la tabla temporal
        $xDescTabla = mysql_query("DESCRIBE $cAlfa.$cTabCar",$xConexion01);
        while ($xRD = mysql_fetch_array($xDescTabla)) {
          $vFields[count($vFields)] = $xRD['Field'];
        }
        array_shift($vFields); 
        $cFields = implode(",",$vFields);
        
        $qLoad  = "LOAD DATA LOCAL INFILE \"$cFile\" INTO TABLE $cAlfa.$cTabCar ";
        $qLoad .= "FIELDS TERMINATED BY \"\\t\" LINES TERMINATED BY \"\\n\" ";
        $qLoad .= "IGNORE 1 LINES ";
        $qLoad .= "($cFields) ";
        $xLoad = mysql_query($qLoad,$xConexion01);
        // f_mensaje(__FILE__,__LINE__,$qLoad);
        // echo $qLoad;
        if(!$xLoad) {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "Error al Cargar los Datos ".mysql_errno($xConexion01)." - ".mysql_error($xLoad);
        }
      }
    }
    # Graba
    if ($nSwitch == 0) {
      $qDatos = "SELECT * FROM $cAlfa.$cTabCar";
      $xDatos = mysql_query($qDatos,$xConexion01);
      $nCanIns = 0; 
      $nCanAct = 0;
      while ($xRD = mysql_fetch_assoc($xDatos)) {
        # Eliminando caracteres de tabulacion, interlineado de los campos
        foreach ($xRD as $ckey => $cValue) {
          $xRD[$ckey] = strtoupper(trim(str_replace($vBuscar,$vReempl,$xRD[$ckey])));
        }
        
        /**
         * Validaciones Generales
         */
        if ($xRD['cebidxxx'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El id no puede ser vacio.\n";
        }

        if ($xRD['cebplaxx'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Plataforma del Codigo Cebe No Puede Ser Vacio.\n";
        }

        if ($xRD['secsapxx'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Codigo SAP No Puede Ser Vacio.\n";
        }

        if ($xRD['cebcodxx'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Codigo Cebe No Puede Ser Vacio.\n";
        }

        if (strlen($xRD['cebcodxx']) == $xRD['cebcodxx']) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El codigo Cebe ya existe.\n";
        }

        if (strlen($xRD['cebcodxx']) != 7) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Codigo Cebe [{$xRD['cebcodxx']}] debe ser siete Numeros.\n";
        }

        if ($xRD['cebdesxx'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El DescripciOn del Codigo Cebe No Puede Ser Vacio.\n";
        }

        if ($xRD['cebmunxx'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Municipio del Codigo Cebe No Puede Ser Vacio.\n";
        }

        if (strlen($xRD['cebidxxx']) != 4) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Id del Codigo Cebe [{$xRD['cebidxxx']}] debe ser cuatro Numeros.\n";
        }
      }## while ($xRD = mysql_fetch_assoc($xDatos)) { ##
    }## if ($nSwitch == 0) { ##
  break;
  default:
    # No hace nada
  break;
}## switch ($_COOKIE['kModo']) { ##


if ($nSwitch == 0) {
  switch ($_COOKIE['kModo']) {
    case "CARGARCEBE":
     # Graba
      $qDatos = "SELECT * FROM $cAlfa.$cTabCar";
      $xDatos = mysql_query($qDatos,$xConexion01);
      $nCanIns = 0; 
      $nCanAct = 0;
      while ($xRD = mysql_fetch_assoc($xDatos)) {
        # Eliminando caracteres de tabulacion, interlineado de los campos
        foreach ($xRD as $ckey => $cValue) {
          $xRD[$ckey] = strtoupper(trim(str_replace($vBuscar,$vReempl,$xRD[$ckey])));
        }
        
        # Comprobar si ya existe el registro;
        $qCeb = "SELECT cebidxxx ";
        $qCeb.= "FROM $cAlfa.lpar0010 ";
        $qCeb.= "WHERE ";
        $qCeb.= "cebidxxx = \"{$xRD['cebidxxx']}\" AND ";
        $qCeb.= "cebcodxx = \"{$xRD['cebcodxx']}\" LIMIT 0,1 ";
        $xCeb = f_MySql("SELECT","",$qCeb,$xConexion01,"");
        
        if (mysql_num_rows($xCeb) == 0) {

          #Valores por defecto
          $nMaxId = 0;
          $qMaximo  = "SELECT MAX(ABS(cebidxxx)) AS cebidxxx ";
          $qMaximo .= "FROM $cAlfa.lpar0010 ";
          $xMaximo = f_MySql("SELECT","",$qMaximo,$xConexion01,"");
          if (mysql_num_rows($xMaximo) > 0){
            $vMaximo = mysql_fetch_array($xMaximo);
            $nMaxId = $vMaximo['cebidxxx'] + 1;
          } else {
            $nMaxId = 1;
          }

          $xRD['cebidxxx'] = str_pad($nMaxId, 4, "000", STR_PAD_LEFT);
          $qInsert = array(array('NAME'=>'secsapxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['secsapxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'cebidxxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['cebidxxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'cebplaxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['cebplaxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'cebmunxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['cebmunxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'cebcodxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['cebcodxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'cebdesxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['cebdesxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'regusrxx','VALUE'=>$kUser                                                               ,'CHECK'=>'SI'),
                           array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')                                                        ,'CHECK'=>'SI'),
                           array('NAME'=>'reghcrex','VALUE'=>date('H:i:s')                                                        ,'CHECK'=>'SI'),
                           array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                                                        ,'CHECK'=>'SI'),
                           array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                                                        ,'CHECK'=>'SI'),
                           array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                                                             ,'CHECK'=>'SI'));
          if (f_MySql("INSERT","lpar0010",$qInsert,$xConexion01,$cAlfa)) {
              $nCanIns++;
          } else {
            $nError = 1;
            $cMsj .= "Linea ".str_pad(($xRD['lineaidx']+1),4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al Insertar Codigo Cebe [{$xRD['cebidxxx']}-{$xRD['cebplaxx']}-{$xRD['secsapxx']}-{$xRD['cebcodxx']}-{$xRD['cebdesxx']}].\n";
          }                           
        }else{
          $qUpdate = array(array('NAME'=>'secsapxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['secsapxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'cebidxxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['cebidxxx']))),'CHECK'=>'WH'),
                           array('NAME'=>'cebplaxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['cebplaxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'cebmunxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['cebmunxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'cebcodxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['cebcodxx']))),'CHECK'=>'WH'),
                           array('NAME'=>'cebdesxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['cebdesxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'regusrxx','VALUE'=>$kUser                                                               ,'CHECK'=>'SI'),
                           array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                                                        ,'CHECK'=>'SI'),
                           array('NAME'=>'reghmodx','VALUE'=>date('H:i:s')                                                        ,'CHECK'=>'SI'),
                           array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                                                             ,'CHECK'=>'SI'),);
          if (f_MySql("UPDATE","lpar0010",$qUpdate,$xConexion01,$cAlfa)) {
              $nCanAct++;
          } else {
            $nError = 1;
            $cMsj .= "Linea ".str_pad(($xRD['lineaidx']+1),4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error en el Update al Codigo Cebe [{$xRD['cebidxxx']}-{$xRD['cebplaxx']}-{$xRD['secsapxx']}-{$xRD['cebcodxx']}-{$xRD['cebdesxx']}].\n";
          }                           
        }## if (mysql_num_rows($xCeb) > 0) { ##
      }## while ($xRD = mysql_fetch_assoc($xDatos)) { ##
      
    break;
    default:
      # No hace nada
    break;
  }## switch ($_COOKIE['kModo']) { ##
}## if ($nSwitch == 0) { ##

if ($nSwitch == 0) {
  $cMsj = "Se Crearon $nCanIns y Se Actualizaron $nCanAct Codigo Cebe.";
  f_Mensaje(__FILE__,__LINE__,$cMsj); ?>
  <form name = "frgrm" action = "<?php echo $_COOKIE['kIniAnt'] ?>" method = "post" target = "fmwork"></form>
  <script languaje = "javascript">
    parent.fmnav.location="<?php echo $cPlesk_Forms_Directory ?>/frnivel3.php";
    document.forms['frgrm'].submit()
  </script>
<?php }

if ($nSwitch == 1) {
  f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
}

/**
 * fnCadenaAleatoria
 * @param int     $pLength        Longitud de la cadena.
 * @var   string  $cCaracteres    Caracteres disponibles para la cadena.
 * @var   int     $nCaracteres    Cantidad de caracteres disponibles para la cadena.
 * @var   int     $nIndex         Posición aleatoria del caracter en la cadena.
 * @var   string  $cResult        Cadena aleatoria formada.
 */
function fnCadenaAleatoria($pLength = 8) {
  $cCaracteres = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ";
  $nCaracteres = strlen($cCaracteres);
  $cResult = "";
  for ($x=0;$x< $pLength;$x++) {
    $nIndex = mt_rand(0,$nCaracteres - 1);
    $cResult .= $cCaracteres[$nIndex];
  }
  return $cResult;
}
?>

<?php
/**
 * Graba. Cargar PUC
 * @package opencomex
 * @todo NA
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
 * @var string  $qPuc         Consulta a fpar0115.
 * @var mixed   $xPuc         Cursor respuesta de la consulta @qPuc.
 * @var array   $qInsert      Insert a la tabla fpar0115.
 * @var array   $qUpdate      Update a la tabla fpar0115.
 */
 
# Librerias
include("../../../../libs/php/utility.php");
include("../../../../../config/config.php");

# Cookie fija
$kDf = explode("~",$_COOKIE["kDatosFijos"]);
$kMysqlHost = $kDf[0];
$kMysqlUser = $kDf[1];
$kMysqlPass = $kDf[2];
$kMysqlDb   = $kDf[3];
$kUser      = $kDf[4];
$kLicencia  = $kDf[5];

$cSystemPath= OC_DOCUMENTROOT;

#Cadenas para reemplazar caracteres espciales
$vBuscar = array(chr(13),chr(10),chr(27),chr(9));
$vReempl = array(" "," "," "," ");

$cBuscar01 = array('"',chr(13),chr(10),chr(27),chr(9));
$cReempl01 = array('\"'," "," "," "," ");

# Control de errores
$nSwitch = 0;
$cMsj    = "\n";

switch ($_COOKIE['kModo']) {
  case "CARGAPUC":
    /**
     * Validando extension permitida del archivo
     */
    if($_FILES['cArcPla']['name'] != ""){
      $vExtPer = ["text/plain"];
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo, $_FILES['cArcPla']['tmp_name']);
      if (!in_array($mime, $vExtPer)) {
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
        $cMsj .= "Archivo No Permitido.\n";
      }
      finfo_close($finfo);
    }
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
      $qNewTab .= "lineaidx INT(11)      NOT NULL AUTO_INCREMENT,";
      $qNewTab .= "pucgruxx varchar(2)   NOT NULL COMMENT \"Grupo de la Cuenta\","; 
      $qNewTab .= "pucctaxx varchar(2)   NOT NULL COMMENT \"Id de la Cuenta\",";
      $qNewTab .= "pucsctax varchar(2)   NOT NULL COMMENT \"Sub-Cuenta\",";
      $qNewTab .= "pucauxxx varchar(2)   NOT NULL COMMENT \"Auxiliar\",";
      $qNewTab .= "pucsauxx varchar(2)   NOT NULL COMMENT \"Sub-Auxiliar\",";
      $qNewTab .= "pucdesxx varchar(50)  NOT NULL COMMENT \"Descripcion de la Cuenta\",";
      $qNewTab .= "pucactxx varchar(1)   NOT NULL COMMENT \"Es una Cuenta de Activos?\",";
      $qNewTab .= "pucdisxx varchar(1)   NOT NULL COMMENT \"Es una Cuenta del Disponible?\","; 
      $qNewTab .= "pucdetxx varchar(1)   NOT NULL COMMENT \"Tipo de Detalle de la Cuenta\",";
      $qNewTab .= "pucterxx varchar(1)   NOT NULL COMMENT \"Es una Cuenta de Terceros?\",";
      $qNewTab .= "pucbaret varchar(10)  NOT NULL COMMENT \"Base de Retencion\",";
      $qNewTab .= "pucretxx varchar(9) NOT NULL COMMENT \"Porcentaje Retencion de la Cuenta\",";
      $qNewTab .= "puccccxx varchar(1)   NOT NULL COMMENT \"Es una Cuenta de Centro Costos?\",";
      $qNewTab .= "pucajuxx varchar(1)   NOT NULL COMMENT \"Es una Cuenta Ajustable?\",";
      $qNewTab .= "pucmexxx varchar(1)   NOT NULL COMMENT \"La Cuenta Tiene Manejo en Moneda Extranjera?\",";
      $qNewTab .= "pucajuex varchar(1)   NOT NULL COMMENT \"La Cuenta se Ajusta en Moneda Extranjera?\",";
      $qNewTab .= "puctcuxx varchar(1)   NOT NULL COMMENT \"Tipo Cuenta?\",";
      $qNewTab .= "pucnatxx varchar(1)   NOT NULL COMMENT \"Naturaleza de la Cuenta?\",";
      $qNewTab .= "puccctxx varchar(1)   NOT NULL COMMENT \"Caracteristica de la Cuenta\",";
      $qNewTab .= "pucctaal varchar(10)  NOT NULL COMMENT \"Cual es la Cuenta Alterna\",";
      $qNewTab .= "pucdoscc varchar(2)   NOT NULL COMMENT \"Obliga DO en el Subcentro de Costo\",";
      $qNewTab .= "puctipej varchar(1)   NOT NULL COMMENT \"Tipo de Ejecucion(L-Local,N-Niif,vacio-Ambas)\",";
      $qNewTab .= " PRIMARY KEY (LINEAIDX)) ENGINE=MyISAM ";
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
        if ($xRD['pucgruxx'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Grupo de la Cuenta No Puede Ser Vacio.\n";
        }
        
        if (substr($xRD['pucgruxx'],0,1) == '0') {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "Grupo de cuenta [{$xRD['pucgruxx']}] no puede empezar en 0.\n";
        }
        
        if (!preg_match("/^[[:digit:]]+$/", $xRD['pucgruxx'])) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Grupo de cuenta [{$xRD['pucgruxx']}] debe ser numerico.\n";
        }
        
        if (strlen($xRD['pucgruxx']) != 2) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Grupo de cuenta [{$xRD['pucgruxx']}] debe ser dos Numeros.\n";
        }

        if ($xRD['pucctaxx'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Id de la Cuenta No Puede Ser Vacio.\n";
        }
        
        if (!preg_match("/^[[:digit:]]+$/", $xRD['pucctaxx'])) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Id de la Cuenta [{$xRD['pucctaxx']}] debe ser numerico.\n";
        }
        
        if (strlen($xRD['pucctaxx']) != 2) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Id de la Cuenta [{$xRD['pucctaxx']}] debe ser dos Numeros.\n";
        }
        
        if ($xRD['pucsctax'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "La Sub-Cuenta No Puede Ser Vacia.\n";
        }
        
        if (!preg_match("/^[[:digit:]]+$/", $xRD['pucsctax'])) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "La Sub-Cuenta [{$xRD['pucsctax']}] debe ser numerica.\n";
        }
        
        if (strlen($xRD['pucsctax']) != 2) {
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "La Sub-Cuenta [{$xRD['pucsctax']}] debe ser dos Numeros.\n";
        }
        
        if ($xRD['pucauxxx'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Auxiliar No Puede Ser Vacio.\n";
        }
        
        if (!preg_match("/^[[:digit:]]+$/", $xRD['pucauxxx'])) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Auxiliar [{$xRD['pucauxxx']}] debe ser numerico.\n";
        }
        
        if (strlen($xRD['pucauxxx']) != 2) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Auxiliar [{$xRD['pucauxxx']}] debe ser dos Numeros.\n";
        }
        
        if ($xRD['pucsauxx'] == "") {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Sub-Auxiliar No Puede Ser Vacio.\n";
        }
        
        if (!preg_match("/^[[:digit:]]+$/", $xRD['pucsauxx'])) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Sub-Auxiliar [{$xRD['pucsauxx']}] debe ser numerico.\n";
        }
        
        if (strlen($xRD['pucsauxx']) != 2) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "El Sub-Auxiliar [{$xRD['pucsauxx']}] debe ser dos Numeros.\n";
        }
        
        if (!f_InList($xRD['pucactxx'],"S","N","")) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "Es una Cuenta de Activos? [{$xRD['pucactxx']}] debe ser S o N o Vacio.\n";
        }
        
        if (!f_InList($xRD['pucdisxx'],"S","N","")) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "Es una Cuenta del Disponible? [{$xRD['pucdisxx']}] debe ser S o N o Vacio.\n";
        }
        
        if (!f_InList($xRD['pucdetxx'],"P","N","C","D","")) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "Es una Cuenta del Disponible? [{$xRD['pucdetxx']}] debe ser P o N o C o D o Vacio.\n";
        }
        
        if (!f_InList($xRD['pucterxx'],"N","S","R","")) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "Es una Cuenta de Terceros? [{$xRD['pucterxx']}] debe ser P o N o C o D o Vacio.\n";
        }
        
        if (!f_InList($xRD['puccccxx'],"S","N","")) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "Es una Cuenta de Centro Costos? [{$xRD['puccccxx']}] debe ser S o N o Vacio.\n";
        }
        
        if (!f_InList($xRD['pucajuxx'],"N","A","D","")) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "Es una Cuenta Ajustable? [{$xRD['pucajuxx']}] debe ser N o A o D o Vacio.\n";
        }
        
        if (!f_InList($xRD['pucmexxx'],"S","N","")) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "La Cuenta Tiene Manejo en Moneda Extranjera? [{$xRD['pucmexxx']}] debe ser S o N o Vacio.\n";
        }
        
        if (!f_InList($xRD['pucajuex'],"S","N","")) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "La Cuenta se Ajusta en Moneda Extranjera? [{$xRD['pucajuex']}] debe ser S o N o Vacio.\n";
        }
        
        if (!f_InList($xRD['puctcuxx'],"P","A","T","I","E","O","C","")) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "Tipo Cuenta? [{$xRD['puctcuxx']}] debe ser S o N o Vacio.\n";
        }
        
        if (!f_InList($xRD['pucnatxx'],"D","C")) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "Naturaleza de la Cuenta? [{$xRD['pucnatxx']}] debe ser D o C.\n";
        }
        
        
        if (!f_InList($xRD['puccctxx'],"N","G","E","I","M","X","T","")) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "Caracteristica de la Cuenta [{$xRD['puccctxx']}] debe ser N o G o E o I o M o X o T o Vacio.\n";
        }
        
        if ($xRD['pucctaal'] != "") {
          if (!preg_match("/^[[:digit:]]+$/", $xRD['pucctaal'])) {
            $nSwitch = 1;
            $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
            $cMsj .= "La Cual es la Cuenta Alterna [{$xRD['pucctaal']}] debe ser numerica.\n";
          }
        }
        
        if (!f_InList($xRD['pucdoscc'],"S","N","")) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "Obliga DO en el Subcentro de Costo [{$xRD['pucdoscc']}] debe ser S o N o Vacio.\n";
        }
        
        if (!f_InList($xRD['puctipej'],"L","N","")) {
          $nSwitch = 1;
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "Tipo de Ejecucion [{$xRD['pucdoscc']}] debe ser L (Local) o N (Niif) o Vacio (Ambas).\n";
        }
        
        # Validaciones desde el Graba de PUC
        if ($xRD['pucdesxx'] == "") {
          $nSwitch  = "1";
          $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
          $cMsj .= "Descripcion Grupo no puede ser vacio.\n";
        }
        
        if ($xRD['pucterxx'] == "R") {
          
          $xRD['pucretxx'] = str_replace(",", ".", $xRD['pucretxx']);
          if ($xRD['pucretxx'] == "" || $xRD['pucretxx'] == 0) {
            $nSwitch  = "1";
            $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
            $cMsj .= "Porcentaje de Retencion no puede ser vacio o cero.\n";
          } 
          
          if (($xRD['pucbaret']+0) < 0) {
            $nSwitch  = "1";
            $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
            $cMsj .= "La Base de Retencion debe ser Mayor o Igual a cero.\n";
          } 
        }
        
        if ($xRD['puctipej'] == "N") {
          # Valida que si la cuenta es de retencion la ejecucion no puede ser NIIF
          if ($xRD['pucterxx'] == "R") {
            $nSwitch  = "1";
            $cMsj .= "Linea ".($xRD['lineaidx']+1).": ";
            $cMsj .= "La cuentas de Retencion Deben Ser de Tipo Ejecucion LOCAL o AMBAS.\n";
          }
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
    case "CARGAPUC":
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
        
        #Valores por defecto
        if ($xRD['pucterxx'] != "R") {
          if ($xRD['pucretxx'] == "") {
            $xRD['pucretxx'] = 0;
          }
          if ($xRD['pucbaret'] == "") {
            $xRD['pucbaret'] = "";
          }
        }
        $xRD['pucretxx'] = str_replace(",", ".", $xRD['pucretxx']);
        
        # Comprobar si ya existe el registro;
        $qPuc = "SELECT pucgruxx ";
        $qPuc.= "FROM $cAlfa.fpar0115 ";
        $qPuc.= "WHERE ";
        $qPuc.= "pucgruxx = {$xRD['pucgruxx']} AND ";
        $qPuc.= "pucctaxx = {$xRD['pucctaxx']} AND ";
        $qPuc.= "pucsctax = {$xRD['pucsctax']} AND ";
        $qPuc.= "pucauxxx = {$xRD['pucauxxx']} AND ";
        $qPuc.= "pucsauxx = {$xRD['pucsauxx']} LIMIT 0,1 ";
        $xPuc = f_MySql("SELECT","",$qPuc,$xConexion01,"");
        
        if (mysql_num_rows($xPuc) == 0) {
          $qInsert = array(array('NAME'=>'pucgruxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucgruxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'pucctaxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucctaxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'pucsctax','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucsctax']))),'CHECK'=>'SI'),
                           array('NAME'=>'pucauxxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucauxxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'pucsauxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucsauxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'pucdesxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucdesxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucinvxx','VALUE'=>""                                                                   ,'CHECK'=>'NO'),
                           array('NAME'=>'pucactxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucactxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucdisxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucdisxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucdetxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucdetxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucterxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucterxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'puccccxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['puccccxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucajuxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucajuxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucmexxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucmexxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucajuex','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucajuex']))),'CHECK'=>'NO'),
                           array('NAME'=>'puctcuxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['puctcuxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucnatxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucnatxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'puccctxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['puccctxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucctaal','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucctaal']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucbaret','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucbaret']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucretxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucretxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucdcs1x','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucdcs1x']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucdoscc','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucdoscc']))),'CHECK'=>'NO'),
                           array('NAME'=>'puctipej','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['puctipej']))),'CHECK'=>'NO'),
                           array('NAME'=>'regusrxx','VALUE'=>$kUser                                                               ,'CHECK'=>'SI'),
                           array('NAME'=>'regfcrex','VALUE'=>date('Y-m-d')                                                        ,'CHECK'=>'SI'),
                           array('NAME'=>'reghcrex','VALUE'=>date('H:i')                                                          ,'CHECK'=>'SI'),
                           array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                                                        ,'CHECK'=>'SI'),
                           array('NAME'=>'reghmodx','VALUE'=>date('H:i')                                                          ,'CHECK'=>'SI'),
                           array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                                                             ,'CHECK'=>'SI'));
          if (f_MySql("INSERT","fpar0115",$qInsert,$xConexion01,$cAlfa)) {
              $nCanIns++;
          } else {
            $nError = 1;
            $cMsj .= "Linea ".str_pad(($xRD['lineaidx']+1),4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error al Insertar Cuenta [{$xRD['pucdruxx']}-{$xRD['pucctaxx']}-{$xRD['pucsctax']}-{$xRD['pucauxxx']}-{$xRD['pucscauxx']}].\n";
          }                           
        }else{
          $qUpdate = array(array('NAME'=>'pucgruxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucgruxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'pucctaxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucctaxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'pucsctax','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucsctax']))),'CHECK'=>'SI'),
                           array('NAME'=>'pucauxxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucauxxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'pucsauxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucsauxx']))),'CHECK'=>'SI'),
                           array('NAME'=>'pucdesxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucdesxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucinvxx','VALUE'=>""                                                                   ,'CHECK'=>'NO'),
                           array('NAME'=>'pucactxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucactxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucdisxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucdisxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucdetxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucdetxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucterxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucterxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'puccccxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['puccccxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucajuxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucajuxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucmexxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucmexxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucajuex','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucajuex']))),'CHECK'=>'NO'),
                           array('NAME'=>'puctcuxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['puctcuxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucnatxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucnatxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'puccctxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['puccctxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucctaal','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucctaal']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucbaret','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucbaret']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucretxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucretxx']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucdcs1x','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucdcs1x']))),'CHECK'=>'NO'),
                           array('NAME'=>'pucdoscc','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucdoscc']))),'CHECK'=>'NO'),
                           array('NAME'=>'puctipej','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['puctipej']))),'CHECK'=>'NO'),
                           array('NAME'=>'regusrxx','VALUE'=>$kUser                                                               ,'CHECK'=>'SI'),
                           array('NAME'=>'regfmodx','VALUE'=>date('Y-m-d')                                                        ,'CHECK'=>'SI'),
                           array('NAME'=>'reghmodx','VALUE'=>date('H:i')                                                          ,'CHECK'=>'SI'),
                           array('NAME'=>'regestxx','VALUE'=>"ACTIVO"                                                             ,'CHECK'=>'SI'),
                           array('NAME'=>'pucgruxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucgruxx']))),'CHECK'=>'WH'),
                           array('NAME'=>'pucctaxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucctaxx']))),'CHECK'=>'WH'),
                           array('NAME'=>'pucsctax','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucsctax']))),'CHECK'=>'WH'),
                           array('NAME'=>'pucauxxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucauxxx']))),'CHECK'=>'WH'),
                           array('NAME'=>'pucsauxx','VALUE'=>str_replace($cBuscar01,$cReempl01,strtoupper(trim($xRD['pucsauxx']))),'CHECK'=>'WH'),);
          if (f_MySql("UPDATE","fpar0115",$qUpdate,$xConexion01,$cAlfa)) {
              $nCanAct++;
          } else {
            $nError = 1;
            $cMsj .= "Linea ".str_pad(($xRD['lineaidx']+1),4,"0",STR_PAD_LEFT).": ";
            $cMsj .= "Error en el Update Cuenta [{$xRD['pucgruxx']}-{$xRD['pucctaxx']}-{$xRD['pucsctax']}-{$xRD['pucauxxx']}-{$xRD['pucsauxx']}].\n";
          }                           
        }## if (mysql_num_rows($xPuc) > 0) { ##
      }## while ($xRD = mysql_fetch_assoc($xDatos)) { ##
      
    break;
    default:
      # No hace nada
    break;
  }## switch ($_COOKIE['kModo']) { ##
}## if ($nSwitch == 0) { ##

if ($nSwitch == 0) {
  $cMsj = "Se Crearon $nCanIns y Se Actualizaron $nCanAct Cuentas.";
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

<?php
  define("_MICROTIME_",microtime(true));
  date_default_timezone_set('America/Bogota'); set_time_limit(0);
  define("_DATABASE_",trim($argv[3]));
  define("_LOTES_",10000);
  define("_FILELOG_","act03704.log"); // Archivo de LOG
  //define("_OCS_MYSQL_HOST_","localhost"); define("_OCS_MYSQL_USER_","admin"); define("_OCS_MYSQL_PASS_",'$AES-128-CBC$GMF4AdklEq61RNN6QP7BDw==$Glv++aeREZGkT9SDmFFjwQ=='); // Datos de Conexion a MYSQl
  //define("_OCS_MYSQL_HOST_","10.11.8.51"); define("_OCS_MYSQL_USER_",$argv[1]); define("_OCS_MYSQL_PASS_",$argv[2]); // Datos de Conexion a MYSQl - LAN Interna Repremundo

  # Definiendo Motor de Almacenamiento
  $cEngine = "InnoDB";
  define("_ENGINE_",$cEngine);

  include(dirname(dirname(__FILE__)).'/config/config.php'); //Se usa esta forma en el include para concatenar la ruta completa del servidor donde estoy
  $vus       = $argv[1];
  $vpw       = $argv[2];
  $cCapacita = $argv[4];
  //Establesco la conexion a la Base de datos
  $server = OC_SERVER;
  define("_OCS_MYSQL_HOST_",$server);
  define("_OCS_MYSQL_USER_",$argv[1]);
  define("_OCS_MYSQL_PASS_",$argv[2]); // Datos de Conexion a MYSQl

  $oMigra = new cMigra();
  $oMigra->fnConexion();
  if(empty($argv[3])){
    $aconn = $oMigra->fnObtenerDBs();
  }else{
    $aconn[0]["DB"]=$argv[3];
    $aconn[0]["US"]=strtolower($argv[3]);
  }

  define("_DIRLOG_","/tmp/"); // Directorio de LOG
  //define("_DIRLOG_","/var/www/html/desarrollo/opencomex/downloads/"); // Directorio de LOG
  system("/bin/echo -e '' > "._DIRLOG_._FILELOG_."; /bin/chmod 777 /"._DIRLOG_._FILELOG_); // Iniciar el Archivo de LOG
  system("/usr/bin/clear");

  define("SEARCH",serialize(array('"',"'","&","#","",chr(13),chr(10),chr(27),chr(9))));
  define("REPLACE",serialize(array('\"',"\'","\&","\#",""," "," "," "," ")));

  $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$argv[0];
  system("/bin/echo -e '$cMsj' >> "._DIRLOG_._FILELOG_); echo $cMsj."\n";

  $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tUsuario db: ".$argv[1];
  system("/bin/echo -e '$cMsj' >> "._DIRLOG_._FILELOG_); echo $cMsj."\n";

  if ($argv[3] != "") {
    $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t Parametro 1:".$argv[3];
    system("/bin/echo -e '$cMsj' >> "._DIRLOG_._FILELOG_); echo $cMsj."\n";
  }

  if ($argv[4] != "") {
    $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t Parametro 2:".$argv[4];
    system("/bin/echo -e '$cMsj' >> "._DIRLOG_._FILELOG_); echo $cMsj."\n";
  }

  if ($argv[5] != "") {
    $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t Parametro 3:".$argv[5];
    system("/bin/echo -e '$cMsj' >> "._DIRLOG_._FILELOG_); echo $cMsj."\n";
  }

  for ($i=0;$i<count($aconn);$i++){
    $vdb  = $aconn[$i]['DB'];
    $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\CREA OPCION DE MENU - CONCEPTOS DOCUMENTO SOPORTE AUTOFACTURA ".$vdb;
    system("/bin/echo -e '$cMsj' >> "._DIRLOG_._FILELOG_); echo "\n\n\n".$cMsj."\n\n\n";
    $oMigra->fnEjecutarQueries(0,100,$vdb);
  }

  class cMigra {

    function fnEjecutarQueries($pInicio,$pFinal,$pdb) {

      global $argv;

      $nSwicht = 0;

      $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tINICIO DEL METODO ".__METHOD__." Base de Datos ".$pdb;
      system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo $cMsj."\n";

      if ($argv[3] == "") {
        $nSwicht = 1;
        $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tDebe Digitar la Base de Datos";
        system("/bin/echo -e '$cMsj' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
      }

      if ($nSwicht == 0) {
        $mdb = mysql_select_db($pdb,_CONEXION_);
        if ($mdb){
          $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t"."Se Selecciona BD: ".$pdb;
          system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo $cMsj."\n";

          $qInsert  = "INSERT INTO sys00005 (";
          $qInsert .= "modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx";
          $qInsert .= ") VALUES (";
          $qInsert .= "\"1000\", \"100\", \"32\", \"frtsinue.php\", \"TRANSMISIESA\", \"00032\", \"Transmision a SIESA\", \"trasapiens.gif\", \"trasapiens_off.gif\", \"\", \"\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\")";
          $xInsert  = mysql_query($qInsert,_CONEXION_);
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_affected_rows(_CONEXION_)."\tSe creo la Opcion de Menu Transmision a SIESA";
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n"; // echo $cMsj."\n";
          } else {
            $nSwicht = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError Al Crear la Opcion de Menu Transmision a SIESA";
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00006 (";
          $qInsert .= "usridxxx, modidxxx, proidxxx, menidxxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx";
          $qInsert .= ") VALUES (";
          $qInsert .= "\"ADMIN\", \"1000\", \"100\", \"32\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\")";
          $xInsert  = mysql_query($qInsert,_CONEXION_);
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_affected_rows(_CONEXION_);
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n"; // echo $cMsj."\n";
          } else {
            $nSwicht = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\Error Al Actualizar Datos";
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          //Insertando el permiso para el usuario interno
          $qUsuarios  = "SELECT USRIDXXX ";
          $qUsuarios .= "FROM $pdb.SIAI0003 ";
          $qUsuarios .= "WHERE ";
          $qUsuarios .= "USRINTXX = \"SI\" ";
          $xUsuarios  = mysql_query($qUsuarios,_CONEXION_);
          $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qUsuarios." ~ ".mysql_num_rows($xUsuarios);
          system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); //echo "\33[01;01;34m".$cMsj."\33[00m\n";

          while ($xRU = mysql_fetch_array($xUsuarios)) {
            $qInsert  = "INSERT INTO sys00006 (usridxxx, modidxxx, proidxxx, menidxxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"1000\", \"100\", \"32\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\")";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_affected_rows(_CONEXION_);
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n"; // echo $cMsj."\n";
            } else {
              $nSwicht = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError Al Actualizar Datos";
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }
          }
        } else {
          $nSwicht = 1;
          $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t"."\tError No Selecciono BD: ".$pdb;
          system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
        }
      }

      $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tFIN DEL METODO ".__METHOD__." Base de Datos ".$pdb;
      system("/bin/echo -e '$cMsj' >> "._DIRLOG_._FILELOG_); echo $cMsj."\n";
    }

    function fnConexion() {
      $xConexion = mysql_connect(_OCS_MYSQL_HOST_,_OCS_MYSQL_USER_,_OCS_MYSQL_PASS_); // Conexion
      if (!$xConexion) {
        $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tEl Sistema no Logro Conexion con el Servidor openComex";
        system("/bin/echo -e '$cMsj' >> /tmp/"._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
      } else {
        define("_CONEXION_",$xConexion);
        $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tConexion Establecida en openComex";echo $cMsj."\n";
      }
    }

    function fnObtenerDBs() {
      mysql_select_db("LOCK");
      $result = mysql_query("SELECT DISTINCT USRDBXXX FROM SIAI0003 ORDER BY USRDBXXX");

      $aconn = array();
      $y     = 0;
      $usdb  = "";
      $Base  = "";
      $Base1 = "";
      while (($row = mysql_fetch_array($result)) != false){
        //if ($_SERVER["SERVER_PORT"] != "") {
          if (substr_count($_SERVER['SCRIPT_FILENAME'],"desarrollo") > 0 && substr_count($_SERVER['SCRIPT_FILENAME'],".gruporepremundo.com.co") == 0){
            $usdb = "de".strtolower($row['USRDBXXX']);
            $Base = "de";
            $Base1 = "DE";
          } elseif (substr_count($_SERVER['SCRIPT_FILENAME'],"pruebas") > 0 && substr_count($_SERVER['SCRIPT_FILENAME'],".gruporepremundo.com.co") == 0){

            $usdb = "te".strtolower($row['USRDBXXX']);
            $Base = "te";
            $Base1 = "TE";
          } else {
            $usdb = strtolower($row['USRDBXXX']);
            $Base = "";
            $Base1 = "";
          }
        //}

        $mdb = mysql_select_db($Base1.$row['USRDBXXX']);

        if ($mdb){
          $aconn[$y]['DB'] = $Base1.$row['USRDBXXX'];
          $aconn[$y]['US'] = $Base.strtolower($row['USRDBXXX']);
          $y++;
          if($Base1.$row['USRDBXXX'] == "DEDESARROL"){
            $aconn[$y]['DB'] = "CAPACITA";
            $aconn[$y]['US'] = "capacita";
            $y++;
          }
        }
      }

      if ($_SERVER["SERVER_PORT"] != "") {
        if (substr_count($_SERVER['SCRIPT_FILENAME'],"desarrollo") == 1) {
          $vFinanciero = array('DEKUEHNENA');
        } elseif (substr_count($_SERVER['SCRIPT_FILENAME'],"pruebas") == 1) {
          $vFinanciero = array('TESKYXXXXX');
        } else {
          $vFinanciero = array('SKYXXXXX');
        }
      }
      return $aconn;
    }
  } ## class cMigra { ##
?>

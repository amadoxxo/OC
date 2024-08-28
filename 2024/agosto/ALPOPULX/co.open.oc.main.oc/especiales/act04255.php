<?php
  define("_MICROTIME_",microtime(true));
  date_default_timezone_set('America/Bogota'); set_time_limit(0);
  define("_DATABASE_",trim($argv[3]));
  define("_LOTES_",10000);
  define("_FILELOG_","act04255.log"); // Archivo de LOG
  //define("_OCS_MYSQL_HOST_","localhost"); define("_OCS_MYSQL_USER_","admin"); define("_OCS_MYSQL_PASS_",'$AES-128-CBC$GMF4AdklEq61RNN6QP7BDw==$Glv++aeREZGkT9SDmFFjwQ=='); // Datos de Conexion a MYSQl
  //define("_OCS_MYSQL_HOST_","10.11.8.51"); define("_OCS_MYSQL_USER_",$argv[1]); define("_OCS_MYSQL_PASS_",$argv[2]); // Datos de Conexion a MYSQl - LAN Interna Repremundo

  # Definiendo el Motor de Almacenamiento
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
    $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tCreación de las Tablas Para el Módulo de Logistica Fase 3.3: ".$vdb;
    system("/bin/echo -e '$cMsj' >> "._DIRLOG_._FILELOG_); echo "\n\n\n".$cMsj."\n\n\n";
    $oMigra->fnEjecutarQueries(0,100,$vdb);
  }

  class cMigra {
    function fnEjecutarQueries($pInicio,$pFinal,$pdb) {

      global $argv;

      $nSwitch = 0;

      $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tINICIO DEL METODO ".__METHOD__." Base de Datos ".$pdb;
      system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo $cMsj."\n";

      if ($nSwitch == 0) {
        $mdb = mysql_select_db($pdb,_CONEXION_);
        if ($mdb){
          $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t"."Se Selecciona BD: ".$pdb;
          system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo $cMsj."\n";

          ###################################
          //Creación de Tabla Tipos Documentales
          ###################################
          $qCreate  = "CREATE TABLE lpar0162 ( ";
          $qCreate .= "tdoidxxx int(10) NOT NULL AUTO_INCREMENT COMMENT \"Id Tipo Documental\",";
          $qCreate .= "tdoserxx varchar(255) NOT NULL COMMENT \"Servicio\",";
          $qCreate .= "tdositxx varchar(255) NOT NULL COMMENT \"Sitio\",";
          $qCreate .= "tdogruxx varchar(255) NOT NULL COMMENT \"Grupo\",";
          $qCreate .= "tdoidecm int(10) NOT NULL COMMENT \"Id Tipo Documental ECM\",";
          $qCreate .= "tdodesxx varchar(255) NOT NULL COMMENT \"Descripcion Tipo Documental\",";
          $qCreate .= "regusrxx varchar(20) NOT NULL COMMENT \"Usuario que Creo el Registro\",";
          $qCreate .= "regfcrex date NOT NULL COMMENT \"Fecha de Creacion del Registro\",";
          $qCreate .= "reghcrex time NOT NULL COMMENT \"Hora de Creacion del Registro\",";
          $qCreate .= "regfmodx date NOT NULL COMMENT \"Fecha de Modificacion del Registro\",";
          $qCreate .= "reghmodx time NOT NULL COMMENT \"Hora de Modificacion del Registro\",";
          $qCreate .= "regestxx varchar(10) NOT NULL COMMENT \"Estado del Registro\",";
          $qCreate .= "regstamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT \"Modificado\",";
          $qCreate .= "PRIMARY KEY (tdoidxxx)";
          $qCreate .= ") ENGINE="._ENGINE_." COMMENT=\"Gestor Documental - Tipos Documentales\"; ";
          $xCreate  = mysql_query($qCreate,_CONEXION_);   
          if ($xCreate) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe Creo la Tabla lpar0162 en: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qCreate." ~ ".mysql_error(_CONEXION_)."\tError Al Crear la Tabla lpar0162 en: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          ##############################################################################
          //Creacion de registros en la tabla lpar0162 para el cliente ALPOPULX
          ##############################################################################
          if ($pdb == "DEALPOPULX" || $pdb == "TEALPOPULX" || $pdb == "ALPOPULX" || $pdb == "TEALPOPULP") {
            
            // OPERACIONES LOGISTICAS - LOGISTICA - MIF - REPORTES APLICATIVOS
            $qInsert  = "INSERT INTO lpar0162 (tdoserxx, tdositxx, tdogruxx, tdoidecm, tdodesxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"OPERACIONES LOGISTICAS\", \"LOGISTICA\", \"MIF\", \"\" ,\"REPORTES APLICATIVOS\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el campo OPERACIONES LOGISTICAS - LOGISTICA - MIF - REPORTES APLICATIVOS en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el campos OPERACIONES LOGISTICAS - LOGISTICA - MIF - REPORTES APLICATIVOS en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // OPERACIONES LOGISTICAS - LOGISTICA - MIF - PLANTILLAS DE TRABAJO
            $qInsert  = "INSERT INTO lpar0162 (tdoserxx, tdositxx, tdogruxx, tdoidecm, tdodesxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"OPERACIONES LOGISTICAS\", \"LOGISTICA\", \"MIF\", \"\" ,\"PLANTILLAS DE TRABAJO\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el campo OPERACIONES LOGISTICAS - LOGISTICA - MIF - PLANTILLAS DE TRABAJO en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el campos OPERACIONES LOGISTICAS - LOGISTICA - MIF - PLANTILLAS DE TRABAJO en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // OPERACIONES LOGISTICAS - LOGISTICA - MIF - OC - PO - EA - HS - MIGO
            $qInsert  = "INSERT INTO lpar0162 (tdoserxx, tdositxx, tdogruxx, tdoidecm, tdodesxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"OPERACIONES LOGISTICAS\", \"LOGISTICA\", \"MIF\", \"\" ,\"OC - PO - EA - HS - MIGO\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el campo OPERACIONES LOGISTICAS - LOGISTICA - MIF - OC - PO - EA - HS - MIGO en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el campos OPERACIONES LOGISTICAS - LOGISTICA - MIF - OC - PO - EA - HS - MIGO en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // OPERACIONES LOGISTICAS - LOGISTICA - MIF - OTROS ANEXOS
            $qInsert  = "INSERT INTO lpar0162 (tdoserxx, tdositxx, tdogruxx, tdoidecm, tdodesxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"OPERACIONES LOGISTICAS\", \"LOGISTICA\", \"MIF\", \"\" ,\"OTROS ANEXOS\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el campo OPERACIONES LOGISTICAS - LOGISTICA - MIF - OTROS ANEXOS en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el campos OPERACIONES LOGISTICAS - LOGISTICA - MIF - OTROS ANEXOS en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // OPERACIONES LOGISTICAS - LOGISTICA - CERTIFICACION - SOPORTES SERVICIO
            $qInsert  = "INSERT INTO lpar0162 (tdoserxx, tdositxx, tdogruxx, tdoidecm, tdodesxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"OPERACIONES LOGISTICAS\", \"LOGISTICA\", \"CERTIFICACION\", \"\" ,\"SOPORTES SERVICIO\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el campo OPERACIONES LOGISTICAS - LOGISTICA - CERTIFICACION - SOPORTES SERVICIO en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el campos OPERACIONES LOGISTICAS - LOGISTICA - CERTIFICACION - SOPORTES SERVICIO en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // OPERACIONES LOGISTICAS - LOGISTICA - CERTIFICACION - OC - PO - EA - HS - MIGO
            $qInsert  = "INSERT INTO lpar0162 (tdoserxx, tdositxx, tdogruxx, tdoidecm, tdodesxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"OPERACIONES LOGISTICAS\", \"LOGISTICA\", \"CERTIFICACION\", \"\" ,\"OC - PO - EA - HS - MIGO\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el campo OPERACIONES LOGISTICAS - LOGISTICA - CERTIFICACION - OC - PO - EA - HS - MIGO en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el campos OPERACIONES LOGISTICAS - LOGISTICA - CERTIFICACION - OC - PO - EA - HS - MIGO en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // OPERACIONES LOGISTICAS - LOGISTICA - CERTIFICACION - OTROS ANEXOS
            $qInsert  = "INSERT INTO lpar0162 (tdoserxx, tdositxx, tdogruxx, tdoidecm, tdodesxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"OPERACIONES LOGISTICAS\", \"LOGISTICA\", \"CERTIFICACION\", \"\" ,\"OTROS ANEXOS\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el campo OPERACIONES LOGISTICAS - LOGISTICA - CERTIFICACION - OTROS ANEXOS en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el campos OPERACIONES LOGISTICAS - LOGISTICA - CERTIFICACION - OTROS ANEXOS en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // OPERACIONES LOGISTICAS - LOGISTICA - PEDIDO - PRE-FACTURA
            $qInsert  = "INSERT INTO lpar0162 (tdoserxx, tdositxx, tdogruxx, tdoidecm, tdodesxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"OPERACIONES LOGISTICAS\", \"LOGISTICA\", \"PEDIDO\", \"\" ,\"PRE-FACTURA\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el campo OPERACIONES LOGISTICAS - LOGISTICA - PEDIDO - PRE-FACTURA en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el campos OPERACIONES LOGISTICAS - LOGISTICA - PEDIDO - PRE-FACTURA en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // OPERACIONES LOGISTICAS - LOGISTICA - PEDIDO - REPORTES APLICATIVOS
            $qInsert  = "INSERT INTO lpar0162 (tdoserxx, tdositxx, tdogruxx, tdoidecm, tdodesxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"OPERACIONES LOGISTICAS\", \"LOGISTICA\", \"PEDIDO\", \"\" ,\"REPORTES APLICATIVOS\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el campo OPERACIONES LOGISTICAS - LOGISTICA - PEDIDO - REPORTES APLICATIVOS en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el campos OPERACIONES LOGISTICAS - LOGISTICA - PEDIDO - REPORTES APLICATIVOS en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // OPERACIONES LOGISTICAS - LOGISTICA - PEDIDO - OC - PO - EA - HS - MIGO
            $qInsert  = "INSERT INTO lpar0162 (tdoserxx, tdositxx, tdogruxx, tdoidecm, tdodesxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"OPERACIONES LOGISTICAS\", \"LOGISTICA\", \"PEDIDO\", \"\" ,\"OC - PO - EA - HS - MIGO\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el campo OPERACIONES LOGISTICAS - LOGISTICA - PEDIDO - OC - PO - EA - HS - MIGO en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el campos OPERACIONES LOGISTICAS - LOGISTICA - PEDIDO - OC - PO - EA - HS - MIGO en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // OPERACIONES LOGISTICAS - LOGISTICA - PEDIDO - OTROS ANEXOS
            $qInsert  = "INSERT INTO lpar0162 (tdoserxx, tdositxx, tdogruxx, tdoidecm, tdodesxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"OPERACIONES LOGISTICAS\", \"LOGISTICA\", \"PEDIDO\", \"\" ,\"OTROS ANEXOS\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el campo OPERACIONES LOGISTICAS - LOGISTICA - PEDIDO - OTROS ANEXOS en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el campos OPERACIONES LOGISTICAS - LOGISTICA - PEDIDO - OTROS ANEXOS en la tabla lpar0162 Tipos Documentales: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }
          }

        } else {
          $nSwitch = 1;
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

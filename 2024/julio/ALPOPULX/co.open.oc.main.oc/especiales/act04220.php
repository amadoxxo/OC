<?php
  define("_MICROTIME_",microtime(true));
  date_default_timezone_set('America/Bogota'); set_time_limit(0);
  define("_DATABASE_",trim($argv[3]));
  define("_LOTES_",10000);
  define("_FILELOG_","act04220.log"); // Archivo de LOG
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
    $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tCrea Opciones de Menú Para el Módulo de Logistica Fase 3.2: ".$vdb;
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
          //Creación de opción de menú módulo Workflow
          ###################################
          // Tabla sys00037
          $qInsert  = "INSERT INTO sys00037 (modidxxx, modtipxx, moddirxx, moddesxx, modordxx, modimgon, modimgof, modtextx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"3\", \"workflow\", \"Workflow\", \"0030\", \"btn_modules_bg.gif\", \"btn_modules_bg_off.gif\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_); 
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo la opción de menú [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear la opción de menú [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qUpdate  = "UPDATE sys00037 SET modtipxx = \"2\", modordxx = \"0020\" WHERE modidxxx = \"2000\";";
          $xUpdate  = mysql_query($qUpdate,_CONEXION_); 
          if ($xUpdate) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe actualizo el modulo [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qUpdate." ~ ".mysql_error(_CONEXION_)."\tError al actualizar el modulo [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          ###################################
          //Prioridades Ticket
          ###################################
          // Tabla sys00038
          $qInsert  = "INSERT INTO sys00038 (modidxxx, proidxxx, prodirxx, protipxx, proinixx, prodesxx, proordxx, proverxx, proimgon, proimgof, proalcxx, proclixx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES";
          $qInsert .= "(\"3000\", \"200\", \"pritiket\", \"2\", \"frptiini.php\", \"Prioridades Tickets\", \"0200\", \"SI\", \"btn_preferences_bg.gif\", \"btn_preferences-disabled_bg.gif\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_); 
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          // Tabla sys00039
          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"200\", \"0\", \"\", \"CONSULTAR\", \"0000\", \"Consultar Prioridades Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de CONSULTAR para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de CONSULTAR para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"200\", \"10\", \"frptinue.php\", \"NUEVO\", \"0010\", \"Nueva Prioridad Ticket\", \"btn_add-new-ticket_bg.gif\", \"btn_add-new-ticket-disabled_bg.gif\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de NUEVO para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de NUEVO para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"200\", \"20\", \"\", \"EDITAR\", \"0020\", \"Editar Prioridad Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de EDITAR para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de EDITAR para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"200\", \"30\", \"\", \"CAMBIAESTADO\", \"0030\", \"Cambio Estado Prioridad Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de CAMBIAESTADO para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de CAMBIAESTADO para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"200\", \"40\", \"\", \"ELIMINAR\", \"0040\", \"Eliminar Prioridad Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de ELIMINAR para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de ELIMINAR para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          } 
          
          // Tabla sys00040
          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"200\", \"0\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CONSULTAR para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CONSULTAR para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"200\", \"10\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de NUEVO para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de NUEVO para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"200\", \"20\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de EDITAR para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de EDITAR para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"200\", \"30\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CAMBIAESTADO para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CAMBIAESTADO para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"200\", \"40\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de ELIMINAR para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de ELIMINAR para la opción de menú Prioridades Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          ###################################
          //Status Ticket
          ###################################
          // Tabla sys00038
          $qInsert  = "INSERT INTO sys00038 (modidxxx, proidxxx, prodirxx, protipxx, proinixx, prodesxx, proordxx, proverxx, proimgon, proimgof, proalcxx, proclixx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES";
          $qInsert .= "(\"3000\", \"250\", \"statiket\", \"2\", \"frstiini.php\", \"Status Tickets\", \"0250\", \"SI\", \"btn_skin-replace_bg.gif\", \"btn_skin-replace-disabled_bg.gif\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_); 
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          // Tabla sys00039
          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"250\", \"0\", \"\", \"CONSULTAR\", \"0000\", \"Consultar Status Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de CONSULTAR para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de CONSULTAR para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"250\", \"10\", \"frstinue.php\", \"NUEVO\", \"0010\", \"Nuevo Status Ticket\", \"btn_add-new-ticket_bg.gif\", \"btn_add-new-ticket-disabled_bg.gif\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de NUEVO para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de NUEVO para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"250\", \"20\", \"\", \"EDITAR\", \"0020\", \"Editar Status Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de EDITAR para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de EDITAR para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"250\", \"30\", \"\", \"CAMBIAESTADO\", \"0030\", \"Cambio Estado Status Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de CAMBIAESTADO para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de CAMBIAESTADO para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"250\", \"40\", \"\", \"ELIMINAR\", \"0040\", \"Eliminar Status Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de ELIMINAR para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de ELIMINAR para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          // Tabla sys00040
          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"250\", \"0\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CONSULTAR para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CONSULTAR para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"250\", \"10\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de 'NUEVO' para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de 'NUEVO' para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"250\", \"20\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de 'EDITAR' para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de 'EDITAR' para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"250\", \"30\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de 'CAMBIAESTADO' para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de 'CAMBIAESTADO' para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"250\", \"40\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de ELIMINAR para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de ELIMINAR para la opción de menú Status Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          ###################################
          //Tipos de Tickets
          ###################################
          // Tabla sys00038
          $qInsert  = "INSERT INTO sys00038 (modidxxx, proidxxx, prodirxx, protipxx, proinixx, prodesxx, proordxx, proverxx, proimgon, proimgof, proalcxx, proclixx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES";
          $qInsert .= "(\"3000\", \"300\", \"tiptiket\", \"2\", \"frttiini.php\", \"Tipos de Ticket\", \"0300\", \"SI\", \"btn_siteapp-pool_bg.gif\", \"btn_siteapp-pool-disabled_bg.gif\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_); 
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo la opción de menú Tipos de Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear la opción de menú Tipos de Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          // Tabla sys00039
          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"300\", \"0\", \"\", \"CONSULTAR\", \"0000\", \"Consultar Tipo de Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de CONSULTAR para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de CONSULTAR para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"300\", \"10\", \"frttinue.php\", \"NUEVO\", \"0010\", \"Nuevo Tipo de Ticket\", \"btn_add-new-ticket_bg.gif\", \"btn_add-new-ticket-disabled_bg.gif\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de NUEVO para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de NUEVO para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"300\", \"20\", \"\", \"EDITAR\", \"0020\", \"Editar Tipo de Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de EDITAR para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de EDITAR para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"300\", \"30\", \"\", \"CAMBIAESTADO\", \"0030\", \"Cambio Estado Tipo de Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de CAMBIAESTADO para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de CAMBIAESTADO para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"300\", \"40\", \"\", \"ELIMINAR\", \"0040\", \"Eliminar Tipo de Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de ELIMINAR para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de ELIMINAR para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          // Tabla sys00040
          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"300\", \"0\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CONSULTAR para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CONSULTAR para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"300\", \"10\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de NUEVO para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de NUEVO para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"300\", \"20\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de EDITAR para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de EDITAR para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"300\", \"30\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CAMBIAESTADO para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CAMBIAESTADO para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"300\", \"40\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de ELIMINAR para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de ELIMINAR para la opción de menú Tipos de Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          ###################################
          //Crear Ticket
          ###################################
          // Tabla sys00039
          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"1000\", \"170\", \"90\", \"\", \"NUEVOTICKET\", \"0090\", \"Nuevo Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de NUEVOTICKET para la opción de menú Crear Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de NUEVOTICKET para la opción de menú Crear Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"1000\", \"170\", \"100\", \"\", \"VERTICKETS\", \"0100\", \"Ver Ticket(s)\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de VERTICKETS para la opción de menú Crear Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de VERTICKETS para la opción de menú Crear Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          // Tabla sys00040
          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"1000\", \"170\", \"90\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de NUEVOTICKET para la opción de menú Crear Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de NUEVOTICKET para la opción de menú Crear Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"1000\", \"170\", \"100\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de VERTICKETS para la opción de menú Crear Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de VERTICKETS para la opción de menú Crear Ticket [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          ###################################
          //Mis Tickets
          ###################################
          // Tabla sys00038
          $qInsert  = "INSERT INTO sys00038 (modidxxx, proidxxx, prodirxx, protipxx, proinixx, prodesxx, proordxx, proverxx, proimgon, proimgof, proalcxx, proclixx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES";
          $qInsert .= "(\"3000\", \"100\", \"mistiket\", \"1\", \"frmtiini.php\", \"Mis Tickets\", \"0100\", \"SI\", \"btn_web-users_bg.gif\", \"btn_web-users-disabled_bg.gif\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_); 
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo la opción de menú Mis Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear la opción de menú Mis Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          // Tabla sys00039
          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"100\", \"0\", \"\", \"CONSULTAR\", \"0000\", \"Consultar Tickets\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de CONSULTAR para la opción de menú Mis Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de CONSULTAR para la opción de menú Mis Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"100\", \"10\", \"\", \"EDITAR\", \"0020\", \"Editar Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de EDITAR para la opción de menú Mis Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de EDITAR para la opción de menú Mis Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          // Tabla sys00040
          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"100\", \"0\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CONSULTAR para la opción de menú Mis Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CONSULTAR para la opción de menú Mis Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }   
          
          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"100\", \"10\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de 'EDITAR' para la opción de menú Mis Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de 'EDITAR' para la opción de menú Mis Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          } 

          ###################################
          //Administracion Tickets
          ###################################
          // Tabla sys00038
          $qInsert  = "INSERT INTO sys00038 (modidxxx, proidxxx, prodirxx, protipxx, proinixx, prodesxx, proordxx, proverxx, proimgon, proimgof, proalcxx, proclixx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES";
          $qInsert .= "(\"3000\", \"150\", \"admontic\", \"1\", \"fratiini.php\", \"Administracion Tickets\", \"0150\", \"SI\", \"btn_cl_tmpl-add_bg.gif\", \"btn_cl_tmpl-add-disabled_bg.gif\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_); 
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo la opción de menú Administracion Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear la opción de menú Administracion Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          // Tabla sys00039
          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"150\", \"0\", \"\", \"CONSULTAR\", \"0000\", \"Consultar Admon Tickets\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de CONSULTAR para la opción de menú Administracion Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de CONSULTAR para la opción de menú Administracion Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"150\", \"10\", \"\", \"EDITAR\", \"0020\", \"Editar Ticket\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de EDITAR para la opción de menú Administracion Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de EDITAR para la opción de menú Administracion Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          $qInsert  = "INSERT INTO sys00039 (modidxxx, proidxxx, menidxxx, menformx, menopcxx, menordxx, mendesxx, menimgon, menimgof, menalcxx, mentipxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
          $qInsert .= "(\"3000\", \"150\", \"20\", \"\", \"REPORTE\", \"0020\", \"Reporte Tickets\", \"\", \"\", \"\", \"\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", NOW())";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el submenú de 'REPORTE' para la opción de menú Administracion Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el submenú de 'REPORTE' para la opción de menú Administracion Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          }

          // Tabla sys00040
          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"150\", \"0\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CONSULTAR para la opción de menú Administracion de Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CONSULTAR para la opción de menú Administracion de Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          } 

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"150\", \"10\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de EDITAR para la opción de menú Administracion de Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de EDITAR para la opción de menú Administracion de Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
          } 

          $qInsert  = "INSERT INTO sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES  ";
          $qInsert .= "(\"ADMIN\", \"3000\", \"150\", \"20\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
          $xInsert  = mysql_query($qInsert,_CONEXION_);   
          if ($xInsert) {
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de REPORTE para la opción de menú Administracion de Tickets [Workflow]: ".$pdb;
            system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
          } else {
            $nSwitch = 1;
            $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de REPORTE para la opción de menú Administracion de Tickets [Workflow]: ".$pdb;
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

            // Crear permisos - Prioridades Ticket
            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"220\", \"0\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CONSULTAR para la opción de menú propiedades ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CONSULTAR para la opción de menú propiedades ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"220\", \"10\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de NUEVO para la opción de menú propiedades ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de NUEVO para la opción de menú propiedades ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"220\", \"20\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de EDITAR para la opción de menú propiedades ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de EDITAR para la opción de menú propiedades ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"220\", \"30\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CAMBIAESTADO para la opción de menú propiedades ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CAMBIAESTADO para la opción de menú propiedades ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"220\", \"40\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de ELIMINAR para la opción de menú propiedades ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de ELIMINAR para la opción de menú propiedades ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // Crear permisos - Status Ticket
            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"250\", \"0\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CONSULTAR para la opción de menú Status Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CONSULTAR para la opción de menú Status Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"250\", \"10\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de 'NUEVO' para la opción de menú Status Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de 'NUEVO' para la opción de menú Status Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"250\", \"20\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de 'EDITAR' para la opción de menú Status Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de 'EDITAR' para la opción de menú Status Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"250\", \"30\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de 'CAMBIAESTADO' para la opción de menú Status Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de 'CAMBIAESTADO' para la opción de menú Status Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"250\", \"40\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de ELIMINAR para la opción de menú Status Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de ELIMINAR para la opción de menú Status Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // Crear permisos - Tipos Ticket
            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"300\", \"0\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CONSULTAR para la opción de menú tipos ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CONSULTAR para la opción de menú tipos ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"300\", \"10\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de NUEVO para la opción de menú tipos ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de NUEVO para la opción de menú tipos ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"300\", \"20\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de EDITAR para la opción de menú tipos ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de EDITAR para la opción de menú tipos ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"300\", \"30\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CAMBIAESTADO para la opción de menú tipos ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CAMBIAESTADO para la opción de menú tipos ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"300\", \"40\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de ELIMINAR para la opción de menú tipos ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de ELIMINAR para la opción de menú tipos ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // Crear permisos - Crear Ticket
            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"1000\", \"170\", \"90\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de NUEVOTICKET para la opción de menú Crear Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de NUEVOTICKET para la opción de menú Crear Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"1000\", \"170\", \"100\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de HISTORICOTICKET para la opción de menú Crear Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de HISTORICOTICKET para la opción de menú Crear Ticket [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // Crear permisos - Mis Tickets
            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"100\", \"0\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CONSULTAR para la opción de menú Mis Tickets [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CONSULTAR para la opción de menú Mis Tickets [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"100\", \"10\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de 'EDITAR' para la opción de menú Mis Tickets [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de 'EDITAR' para la opción de menú Mis Tickets [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            // Crear permisos - Administracion Tickets
            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"150\", \"0\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de CONSULTAR para la opción de menú Administracion Tickets [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de CONSULTAR para la opción de menú Administracion Tickets [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"150\", \"10\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de EDITAR para la opción de menú Administracion Tickets [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de EDITAR para la opción de menú Administracion Tickets [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;91m".$cMsj."\33[00m\n";
            }

            $qInsert  = "INSERT INTO $pdb.sys00040 (usridxxx, modidxxx, proidxxx, menidxxx, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx, regstamp) VALUES ";
            $qInsert .= "(\"{$xRU['USRIDXXX']}\", \"3000\", \"150\", \"20\", \"ADMIN\", NOW(), NOW(), NOW(), NOW(), \"ACTIVO\", CURRENT_TIMESTAMP)";
            $xInsert  = mysql_query($qInsert,_CONEXION_);
            if ($xInsert) {
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\tSe creo el permiso de REPORTE para la opción de menú Administracion Tickets [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
              system("/bin/echo -e '".$cMsj."' >> "._DIRLOG_._FILELOG_); echo "\33[01;01;34m".$cMsj."\33[00m\n";
            } else {
              $nSwitch = 1;
              $cMsj = date("Y-m-d H:m:s")."\t".str_pad(__LINE__,4,"0",STR_PAD_LEFT)."\t".number_format(microtime(true)-_MICROTIME_,2)."\t".$qInsert." ~ ".mysql_error(_CONEXION_)."\tError al crear el permiso de REPORTE para la opción de menú Administracion Tickets [workflow] para el usuario interno [{$xRU['USRIDXXX']}] en: ".$pdb;
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

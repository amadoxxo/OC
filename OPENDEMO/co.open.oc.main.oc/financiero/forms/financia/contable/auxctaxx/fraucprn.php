
<?php
  /**
	 * Imprime Auxiliar Cuentas Detallado Por Tercero.
	 * --- Descripcion: Permite Imprimir Auxiliar Cuentas Detallado Por Tercero.
	 * @author Yulieth Campos <ycampos@opentecnologia.com.co>
	 */

  // ini_set('error_reporting', E_ALL);
  // ini_set("display_errors","1");

  ini_set("memory_limit","512M");
	set_time_limit(0);

  date_default_timezone_set("America/Bogota");

  /**
   * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales
   * @var Number
   */
  $cEjePro = 0;

  /**
   * Nombre(s) de los archivos en excel generados
   */
  $cNomFile = "";

  $nSwitch = 0; 	// Variable para la Validacion de los Datos
  $cMsj = "\n"; 	// Variable para Guardar los Errores de las Validaciones

  /**
   * Cuando se ejecuta desde el cron debe armarse la cookie para incluir los utilitys
   */
  if ($_SERVER["SERVER_PORT"] == "") {
    $vArg = explode(",", $argv[1]);

    if ($vArg[0] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El parametro Id del Proceso no puede ser vacio.\n";
    }

    if ($vArg[1] == "") {
      $nSwitch = 1;
      $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
      $cMsj .= "El parametro de la Cookie no puede ser vacio.\n";
    }

    if ($nSwitch == 0) {
      $_COOKIE["kDatosFijos"] = $vArg[1];

      # Librerias
      include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
      include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utility.php");
      include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/uticonta.php");
      include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php");

      /**
       * Buscando el ID del proceso
       */
      $qProBg = "SELECT * ";
      $qProBg .= "FROM $cBeta.sysprobg ";
      $qProBg .= "WHERE ";
      $qProBg .= "pbaidxxx= \"{$vArg[0]}\" AND ";
      $qProBg .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
      $xProBg = f_MySql("SELECT", "", $qProBg, $xConexion01, "");
      if (mysql_num_rows($xProBg) == 0) {
        $xRPB = mysql_fetch_array($xProBg);
        $nSwitch = 1;
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= "El Proceso en Background [{$vArg[0]}] No Existe o ya fue Procesado.\n";
      } else {
        $xRB = mysql_fetch_array($xProBg);

        /**
         * Reconstruyendo Post
         */
        $mPost = f_Explode_Array($xRB['pbapostx'], "|", "~");
        for ($nP = 0; $nP < count($mPost); $nP++) {
          if ($mPost[$nP][0] != "") {
            $_POST[$mPost[$nP][0]] = $mPost[$nP][1];
          }
        }
      }
    }
  }

  /**
   * Subiendo el archivo al sistema
   */
  if ($_SERVER["SERVER_PORT"] != "") {
    # Librerias
    include("../../../../../config/config.php");
    include("../../../../libs/php/utility.php");
    include("../../../../libs/php/uticonta.php");
    include("../../../../../libs/php/utiprobg.php");
  }

  if ($_SERVER["SERVER_PORT"] != "") {
    $cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
  }

  if ($_SERVER["SERVER_PORT"] == "") {
    $cTipo      = $_POST['cTipo'];
    $cTerId     = $_POST['cTerId'];
    $cTipTer    = $_POST['cTipTer'];
    $dDesde     = $_POST['dDesde'];
    $dHasta     = $_POST['dHasta'];
    $gPucIdIni  = $_POST['gPucIdIni'];
    $gPucIdFin  = $_POST['gPucIdFin'];
  }

  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
    $cEjePro = 1;

    $strPost  = "|cTipo~".$cTipo;
    $strPost .= "|cTerId~".$cTerId;
    $strPost .= "|cTipTer~".$cTipTer;
    $strPost .= "|dDesde~".$dDesde;
    $strPost .= "|dHasta~".$dHasta;
    $strPost .= "|gPucIdIni~".$gPucIdIni;
    $strPost .= "|gPucIdFin~".$gPucIdFin;
    $nRegistros = 0;

    $vParBg['pbadbxxx'] = $cAlfa;                                   //Base de Datos
    $vParBg['pbamodxx'] = "FACTURACION";                            //Modulo
    $vParBg['pbatinxx'] = "AUXILIARXCUENTASDETALLADOXTERCEROS";     //Tipo Interface
    $vParBg['pbatinde'] = "AUXILIAR CUENTAS DETALLADO X TERCEROS";  //Descripcion Tipo de Interfaz
    $vParBg['admidxxx'] = "";                                       //Sucursal
    $vParBg['doiidxxx'] = "";                                       //Do
    $vParBg['doisfidx'] = "";                                       //Sufijo
    $vParBg['cliidxxx'] = "";                                       //Nit
    $vParBg['clinomxx'] = "";                                       //Nombre Importador
    $vParBg['pbapostx'] = $strPost;														      //Parametros para reconstruir Post
    $vParBg['pbatabxx'] = "";                                       //Tablas Temporales
    $vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];              //Script
    $vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];                  //cookie
    $vParBg['pbacrexx'] = $nRegistros;                              //Cantidad Registros
    $vParBg['pbatxixx'] = 1;                                        //Tiempo Ejecucion x Item en Segundos
    $vParBg['pbaopcxx'] = "";                                       //Opciones
    $vParBg['regusrxx'] = $kUser;                                   //Usuario que Creo Registro
  
    #Incluyendo la clase de procesos en background
    $ObjProBg = new cProcesosBackground();
    $mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);
  
    #Imprimiendo resumen de todo ok.
    if ($mReturnProBg[0] == "true") {
      f_Mensaje(__FILE__, __LINE__, "Proceso en Background Agendado con Exito."); ?>
      <script languaje = "javascript">
          parent.fmwork.fnRecargar();
      </script>
    <?php 
    } else {
      $nSwitch = 1;
      for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= $mReturnProBg[$nR] . "\n";
      }
      f_Mensaje(__FILE__, __LINE__, $cMsj."Verifique.");
    }
  }  

  if ($cEjePro == 0) {
		if ($nSwitch == 0) {

      switch ($cTipo) {
        case 1: ?>
          <html>
          <head>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/estilo.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/general.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/layout.css'>
            <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory_New ?>/custom.css'>
            <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/date_picker.js'></script>
            <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/utility.js'></script>
            <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New ?>/ajax.js'></script>
            <link rel="stylesheet" type="text/css" href="../../../../../programs/gwtext/resources/css/ext-all.css">
            <script type="text/javascript" src="../../../../../programs/gwtext/adapter/ext/ext-base.js"></script>
            <script type="text/javascript" src="../../../../../programs/gwtext/ext-all.js"></script>
            <script language="JavaScript" src="../../../../../programs/gwtext/conexijs/loading/loading.js"></script>
            </head>
            <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0" onLoad="init();">
              <script>
                uLoad();
                var ld=(document.all);
                var ns4=document.layers;
                var ns6=document.getElementById&&!document.all;
                var ie4=document.all;

                function init() {
                  if(ns4){ld.visibility="hidden";}
                  else if (ns6||ie4) {
                    Ext.MessageBox.updateProgress(1,'100% completed');
                    Ext.MessageBox.hide();
                  }
                }
              </script>
              <?php
              ob_flush();
              flush();
              ?>
        <?php break;
        default:
          //No hace nada
        break;
      }

      $cPerAno = substr($dDesde,0,4);
      $nADesde = substr($dDesde,0,4);
      $nAHasta = substr($dHasta,0,4);
      $dFecIni = $cPerAno."-01-01";

      list($nAno,$nMes,$nDia)=split("-",$dDesde);
      $nFecFin = mktime(0,0,0, $nMes,$nDia,$nAno) - (24 * 60 * 60);
      $dFecFin = date("Y-m-d",$nFecFin);
      $dFecha  = date('Y-m-d');
      $cMes = "";
      switch (substr($dFecha,5,2)){
        case "01": $cMes="ENERO";      break;
        case "02": $cMes="FEBRERO";    break;
        case "03": $cMes="MARZO";      break;
        case "04": $cMes="ABRIL";      break;
        case "05": $cMes="MAYO";       break;
        case "06": $cMes="JUNIO";      break;
        case "07": $cMes="JULIO";      break;
        case "08": $cMes="AGOSTO";     break;
        case "09": $cMes="SEPTIEMBRE"; break;
        case "10": $cMes="OCTUBRE";    break;
        case "11": $cMes="NOVIEMBRE";  break;
        case "12": $cMes="DICIEMBRE";  break;
      }//switch (substr($dFecha,5,2)){

      $qCliDat  = "SELECT ";
      $qCliDat .= "$cAlfa.SIAI0150.*, ";
      $qCliDat .= "IF($cAlfa.SIAI0150.CLINOMXX <> \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X)) AS CLINOMXX ";
      $qCliDat .= "FROM $cAlfa.SIAI0150 ";
      $qCliDat .= "WHERE ";
      $qCliDat .= "$cAlfa.SIAI0150.CLIIDXXX = \"$cTerId\" ";
      $xCliDat  = f_MySql("SELECT","",$qCliDat,$xConexion01,"");
      $nFilCli  = mysql_num_rows($xCliDat);
      if ($nFilCli > 0) {
        $vCliDat  = mysql_fetch_array($xCliDat);
      }
      $vCliDat['NOMBREXX'] = $vCliDat['CLINOMXX'];
      $suma=0;
      $mCodDat = array();

      /**
       * Se cambia la logica del sistema 2016-01-18
       * Ticket 24700, se detecto que si un cliente no tiene movimiento contable en el periodo seleccionado
       * El sistema no muestra si tiene saldo anterior
       * Por lo que la logica se modifica que recorrer una a una las cuentas contables seleccionadas y si no hay registros
       * Mostrar solo el saldo anterior
       */
      $mDatos = array(); //Matriz con los datos a pintar

      /**
       * Se buscan todas las cuentas contables usadas por el cliente desde que se inicio el sistema
       */
      $cCuentas = ""; $vCuentas = array();
      for($i=$vSysStr['financiero_ano_instalacion_modulo'];$i<=$nAHasta;$i++){
        $qMovCue  = "SELECT DISTINCT pucidxxx ";
        $qMovCue .= "FROM $cAlfa.fcod$i ";
        $qMovCue .= "WHERE ";
        switch ($cTipTer) {
          case "CLIENTE":
            $qMovCue .= "$cAlfa.fcod$i.teridxxx = \"$cTerId\" AND ";
          break;
          case "PROVEEDOR":
            $qMovCue .= "$cAlfa.fcod$i.terid2xx = \"$cTerId\" AND ";
          break;
          default:
            $qMovCue .= "($cAlfa.fcod$i.teridxxx = \"$cTerId\" OR $cAlfa.fcod$i.terid2xx = \"$cTerId\") AND ";
          break;
        }
        $qMovCue .= "$cAlfa.fcod$i.regestxx = \"ACTIVO\"";
        $xMovCue  = f_MySql("SELECT","",$qMovCue,$xConexion01,"");
        while($xRMC = mysql_fetch_array($xMovCue)){
          if (in_array("\"{$xRMC['pucidxxx']}\"", $vCuentas) == false) {
            $cCuentas .= "\"{$xRMC['pucidxxx']}\",";
            $vCuentas[count($vCuentas)] = "\"{$xRMC['pucidxxx']}\"";
          }
        }
      }
      $cCuentas = substr($cCuentas, 0, -1);

      if ($cCuentas != "") {
        $qCuentas  = "SELECT ";
        $qCuentas .= "CONCAT(pucgruxx, pucctaxx, pucsctax, pucauxxx, pucsauxx) AS pucidxxx, ";
        $qCuentas .= "pucnatxx ";
        $qCuentas .= "FROM $cAlfa.fpar0115 ";
        $qCuentas .= "WHERE ";
        $qCuentas .= "CONCAT(pucgruxx, pucctaxx, pucsctax, pucauxxx, pucsauxx) IN ($cCuentas) ";
        if($gPucIdIni <> "" && $gPucIdFin <> ""){
          $qCuentas .= "AND CONCAT(pucgruxx, pucctaxx, pucsctax, pucauxxx, pucsauxx) BETWEEN \"$gPucIdIni\" AND \"$gPucIdFin\" ";
        }
        $qCuentas .= "ORDER BY pucidxxx";
        $xCuentas  = f_MySql("SELECT","",$qCuentas,$xConexion01,"");
        while ($xRC = mysql_fetch_array($xCuentas)) {
          for($i=$nADesde;$i<=$nAHasta;$i++){
            $qCodDat  = "SELECT DISTINCT ";
            $qCodDat .= "$cAlfa.fcod$i.*, ";
            $qCodDat .= "IF($cAlfa.fcod$i.commovxx=\"D\",$cAlfa.fcod$i.comvlrxx,0) AS debitoxx,";
            $qCodDat .= "IF($cAlfa.fcod$i.commovxx=\"C\",$cAlfa.fcod$i.comvlrxx,0) AS creditox ";
            $qCodDat .= "FROM $cAlfa.fcod$i ";
            $qCodDat .= "WHERE ";
            switch ($cTipTer) {
              case "CLIENTE":
                $qCodDat .= "$cAlfa.fcod$i.teridxxx = \"$cTerId\" AND ";
              break;
              case "PROVEEDOR":
                $qCodDat .= "$cAlfa.fcod$i.terid2xx = \"$cTerId\" AND ";
              break;
              default:
                $qCodDat .= "($cAlfa.fcod$i.teridxxx = \"$cTerId\" OR $cAlfa.fcod$i.terid2xx = \"$cTerId\") AND ";
              break;
            }
            $qCodDat .= "$cAlfa.fcod$i.comfecxx BETWEEN \"$dDesde\" AND \"$dHasta\" AND ";
            $qCodDat .= "$cAlfa.fcod$i.pucidxxx = \"{$xRC['pucidxxx']}\" AND ";
            $qCodDat .= "$cAlfa.fcod$i.regestxx = \"ACTIVO\" ORDER BY $cAlfa.fcod$i.pucidxxx ASC ";
            $xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qCodDat."~".mysql_num_rows($xCodDat));
            if (mysql_num_rows($xCodDat) == 0) {
              //Calculando saldo anterior, si tiene se muestra
              if(substr($dFecFin,0,4) < $cPerAno){
                $cPerAno = $cPerAno - 1;
                $dFecIni = $cPerAno."-01-01";
              }
              $mSalAnt = explode("~",f_Saldo_x_Cuenta_Cliente($cTerId,$xRC['pucidxxx'],$dFecIni,$dFecFin,$cTipTer));

              if ($mSalAnt[1] != 0) {
                $nInd_mDatos = count($mDatos);
                $mDatos[$nInd_mDatos]['pucidxxx'] = $xRC['pucidxxx'];
                $mDatos[$nInd_mDatos]['pucnatxx'] = $xRC['pucnatxx'];
                $mDatos[$nInd_mDatos]['debitoxx'] = 0;
                $mDatos[$nInd_mDatos]['creditox'] = 0;
              }
            } else {
              while ($xRCD = mysql_fetch_array($xCodDat)) {
                $xRCD['pucnatxx'] = $xRC['pucnatxx'];
                $mDatos[count($mDatos)] = $xRCD;
              }
            }
          }//for($i=$nADesde;$i<=$nAHasta;$i++){
        }
      }

      switch ($cTipo) {
        case 1:
          // PINTA POR PANTALLA// ?>
          <form name = 'frgrm' action='frinpgrf.php' method="POST">
            <center>
              <table border="1" cellspacing="0" cellpadding="0" width="98%" align=center style="margin:5px">

                <tr bgcolor = "white" height="20" style="padding-left:10px;padding-top:5px">
                  <?php
                  switch ($cAlfa) {
                    case "ROLDANLO"://ROLDAN
                    case "TEROLDANLO"://ROLDAN
                    case "DEROLDANLO"://ROLDAN?>
                      <td class="name"><center><img width="160" height="65" style="left: 15px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoroldan.png"></td>
                    <?php
                    break;
                    case "GRUMALCO"://GRUMALCO
                    case "TEGRUMALCO"://GRUMALCO
                    case "DEGRUMALCO"://GRUMALCO?>
                      <td class="name"><center><img width="160" height="65" style="left: 15px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomalco.jpg"></td>
                    <?php
                    break;
                    case "ALADUANA": //ALADUANA
                    case "TEALADUANA": //ALADUANA
                    case "DEALADUANA": //ALADUANA
                    case "DEDESARROL":?>
                      <td class="name"><center><img width="160" height="80" style="left: 35px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaladuana.jpg"></td>
                    <?php
                    break;
                    case "ANDINOSX": //ANDINOSX
                    case "TEANDINOSX": //ANDINOSX
                    case "DEANDINOSX": //ANDINOSX ?>
                      <td class="name"><center><img width="160" height="65" style="left: 22px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoandinos.jpg"></td>
                    <?php
                    break;
                    case "GRUPOALC": //GRUPOALC
                    case "TEGRUPOALC": //GRUPOALC
                    case "DEGRUPOALC": //GRUPOALC ?>
                      <td class="name"><center><img width="160" height="80" style="left: 22px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoalc.jpg"></td>
                    <?php
                    break;
                    case "AAINTERX": //AAINTERX
                    case "TEAAINTERX": //AAINTERX
                    case "DEAAINTERX": //AAINTERX ?>
                      <td class="name"><center><img width="160" height="80" style="left: 35px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointernacional.jpg"></td>
                    <?php
                    break;
                    case "AALOPEZX":
                    case "TEAALOPEZX": 
                    case "DEAALOPEZX":  ?>
                      <td class="name"><center><img width="140" style="left: 24px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaalopez.png"></td>
                    <?php
                    break;
                    case "ADUAMARX": //ADUAMARX
                    case "TEADUAMARX": //ADUAMARX
                    case "DEADUAMARX": //ADUAMARX ?>
                      <td class="name"><center><img width="82" height="82" style="left: 65px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoaduamar.jpg"></td>
                    <?php
                    break;
                    case "SOLUCION": //SOLUCION
                    case "TESOLUCION": //SOLUCION
                    case "DESOLUCION": //SOLUCION ?>
                      <td class="name"><center><img width="160" style="left: 28px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logosoluciones.jpg"></td>
                    <?php
										break;
										case "FENIXSAS": //FENIXSAS
										case "TEFENIXSAS": //FENIXSAS
										case "DEFENIXSAS": //FENIXSAS ?>
											<td class="name"><center><img width="160" style="left: 28px;margin-top: -25px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logofenix.jpg"></td>
										<?php
										break;
                    case "COLVANXX": //COLVANXX
                    case "TECOLVANXX": //COLVANXX
                    case "DECOLVANXX": //COLVANXX ?>
                      <td class="name"><center><img width="160" style="left: 28px;margin-top: -34px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logocolvan.jpg"></td>
                    <?php
                    break;
                    case "INTERLAC": //INTERLAC
                    case "TEINTERLAC": //INTERLAC
                    case "DEINTERLAC": //INTERLAC ?>
                      <td class="name"><center><img width="160" style="left: 28px;margin-top: -39px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logointerlace.jpg"></td>
                    <?php
                    break;
										case "DHLEXPRE": //DHLEXPRE
										case "TEDHLEXPRE": //DHLEXPRE
										case "DEDHLEXPRE": //DHLEXPRE?>
											<td class="name"><center><img width="160" height="65" style="left: 24px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logo_dhl_express.jpg"></td>
										<?php
										break;
                    case "KARGORUX": //KARGORUX
                    case "TEKARGORUX": //KARGORUX
                    case "DEKARGORUX": //KARGORUX?>
                      <td class="name"><center><img width="160" height="65" style="left: 24px;margin-top: -35px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logokargoru.jpg"></td>
                    <?php
                    break;
                    case "ALOGISAS": //LOGISTICA
                    case "TEALOGISAS": //LOGISTICA
                    case "DEALOGISAS": //LOGISTICA?>
                      <td class="name"><center><img width="165" style="left: 22px;margin-top: -33px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logologisticasas.jpg"></td>
                    <?php
                    break;
                    case "PROSERCO":
                    case "TEPROSERCO":
                    case "DEPROSERCO":?>
                      <td class="name"><center><img width="160" style="left: 28px;margin-top: -45px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoproserco.png"></td>
                    <?php
                    break;
                    case "MANATIAL":
                    case "TEMANATIAL":
                    case "DEMANATIAL":?>
                      <td class="name"><center><img width="160" style="left: 28px;margin-top: -25px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomanantial.jpg"></td>
                    <?php
                    break;
                    case "DSVSASXX":
                    case "DEDSVSASXX":
                    case "TEDSVSASXX": ?>
                      <td class="name"><center><img width="160" style="left: 28px;margin-top: -35px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logodsv.jpg"></td>
                    <?php
                    break;
                    case "MELYAKXX":    //MELYAK
                    case "DEMELYAKXX":  //MELYAK
                    case "TEMELYAKXX":  //MELYAK ?>
                      <td class="name"><center><img width="160" style="left: 28px;margin-top: -30px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logomelyak.jpg"></td>
                    <?php	
                    break;
                    case "FEDEXEXP":
                    case "DEFEDEXEXP":
                    case "TEFEDEXEXP":?>
                      <td class="name"><center><img width="160" style="left: 28px;margin-top: -48px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logofedexexp.jpg"></td>
                    <?php
                    break;
                    case "EXPORCOM":
                    case "DEEXPORCOM":
                    case "TEEXPORCOM":?>
                      <td class="name"><center><img width="160" style="left: 28px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoexporcomex.jpg"></td>
                    <?php
                    break;
                    case "HAYDEARX":
                    case "DEHAYDEARX":
                    case "TEHAYDEARX":?>
                      <td class="name"><center><img width="160" style="left: 28px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logohaydear.jpeg"></td>
                    <?php
                    break;
                    case "CONNECTA":
                    case "DECONNECTA":
                    case "TECONNECTA":?>
                      <td class="name"><center><img width="160" height="80" style="left: 28px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoconnecta.jpg"></td>
                    <?php
                    break;
                    case "OPENEBCO":
                    case "DEOPENEBCO":
                    case "TEOPENEBCO":?>
                      <td class="name"><center><img width="160" height="80" style="left: 28px;margin-top: -40px;position: absolute;" src = "<?php echo $cPlesk_Skin_Directory ?>/logoopen.jpg"></td>
                    <?php
                    break;
                  }?>
                  <td class="name" colspan="12" align="left">

                    <font size="3">
                      <b>AUXILIAR CUENTAS DETALLADO POR TERCEROS<BR>
                      RANGO CUENTAS: <?php echo " ".$gPucIdIni." - ".$gPucIdFin ?><br>
                      NIT: <?php echo $vCliDat['CLIIDXXX']."    "."  TERCERO: ".$vCliDat['NOMBREXX'] ?><br>
                      PERIODO: <?php echo " ".$dDesde." - ".$dHasta ?><br>
                      FECHA Y HORA DE CONSULTA: <?php echo " ".$cMes." ".substr($dFecha,8,2)." "."DE ".substr($dFecha,0,4)." "."- ".date('H:i:s') ?><br></b>
                    </font>
                  </td>
                </tr>
                <tr height="20">
                  <td style="background-color:#0B610B" class="letra8" align="center" width="180px"><b><font color=white>Documento</font></b></td>
                  <td style="background-color:#0B610B" class="letra8" align="center" width="180px"><b><font color=white>Documento Cruce</font></b></td>
                  <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha</font></b></td>
                  <td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Detalle</font></b></td>
                  <td style="background-color:#0B610B" class="letra8" align="center" width="140px"><b><font color=white>Debitos</font></b></td>
                  <td style="background-color:#0B610B" class="letra8" align="center" width="140px"><b><font color=white>Creditos</font></b></td>
                  <td style="background-color:#0B610B" class="letra8" align="center" width="140px"><b><font color=white>Saldo</font></b></td>
                </tr>
                <tr height="5">
                  <td style = "color:<?php echo $zColorPro ?> class="letra8" align="center" colspan="7"><b><font color=white></font></b></td>
                </tr>
                <?php
                $cCueAux   = "";  $cCueAux2  = "";
                $nTDebxCue = 0;   $nTCrexCue = 0;

                for($nM=0; $nM < count($mDatos); $nM++) {
                  if(substr($dFecFin,0,4) < $cPerAno){
                    $cPerAno = $cPerAno - 1;
                    $dFecIni = $cPerAno."-01-01";
                  }
                  $zColorPro = "#000000";
                  ##Impresion de total por cuenta##
                  if($cCueAux2 != $mDatos[$nM]['pucidxxx']){
                    if($cCueAux2 != ""){ ?>
                      <tr height="20" style="padding-left:4px;padding-right:4px">
                        <td style="background-color:#E3F6CE" class="letra7" align="right" colspan="4"><b>Total</b></td>
                        <td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b><?php echo (strpos($nTDebxCue+0,'.') > 0) ? number_format($nTDebxCue,2,',','.') : number_format($nTDebxCue,0,',','.') ?></b></td>
                        <td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b><?php echo (strpos($nTCrexCue+0,'.') > 0) ? number_format($nTCrexCue,2,',','.') : number_format($nTCrexCue,0,',','.') ?></b></td>
                        <td style="background-color:#E3F6CE" class="letra7" align="right"><b><?php echo ($nSalTot<>"")?((strpos($nSalTot+0,'.') > 0) ? number_format($nSalTot,2,',','.') : number_format($nSalTot,0,',','.')):"0" ?></b></td>
                      </tr>
                      <tr height="20" style="padding-left:4px;padding-right:4px">
                        <td colspan="7">&nbsp;</td>
                      </tr>
                      <?php
                      $nTDebxCue = 0;
                      $nTCrexCue = 0;
                      $nSalTot   = 0;
                    }
                    $cCueAux2 = $mDatos[$nM]['pucidxxx'];
                  }

                  ##Fin Impresion de total por cuenta##
                  if($cCueAux != $mDatos[$nM]['pucidxxx']) {
                    $cCueAux = $mDatos[$nM]['pucidxxx'];
                    $mSalAnt = explode("~",f_Saldo_x_Cuenta_Cliente($cTerId,$cCueAux,$dFecIni,$dFecFin,$cTipTer));
                    $nSalAnt = 0;
                    $nSalAnt = $mSalAnt[1];
                    //nuevo calculo de saldo anterior por cuenta #0B610B
                    ?>
                    <tr height="20" style="padding-left:4px;padding-right:4px">
                      <td style="background-color:#0B610B" class="letra7" align="left" colspan="4"><b><font color=white>Cuenta: <?php echo $mDatos[$nM]['pucidxxx']?></font></b></td>
                      <td style="background-color:#0B610B" class="letra7" align="right" colspan="2"><b><font color=white>Saldo Anterior</font></b></td>
                      <td style="background-color:#0B610B" class="letra7" align="right"><b><font color=white><?php echo ($mSalAnt[1]!="")?((strpos($mSalAnt[1]+0,'.') > 0) ? number_format($mSalAnt[1],2,',','.') : number_format($mSalAnt[1],0,',','.')):"0" ?></font></b></td>
                    </tr>
                  <?php }

                  //Calculo el saldo y lo guardo en otra variable para mostrar los totales por cuenta
                  $nSalAnt   += $mDatos[$nM]['debitoxx'] - $mDatos[$nM]['creditox'];

                  $nTDebxCue += ($mDatos[$nM]['debitoxx'] != ""?$mDatos[$nM]['debitoxx']:"0");
                  $nTCrexCue += ($mDatos[$nM]['creditox'] != ""?$mDatos[$nM]['creditox']:"0");
                  $nSalTot    = $nSalAnt;
                  if (($mDatos[$nM]['debitoxx']+0) > 0 || ($mDatos[$nM]['creditox']+0) > 0) { ?>
                    <tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">
                      <td class="letra7" align="left" style = "color:<?php echo $zColorPro ?>"><?php echo ($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mDatos[$nM]['comcsc3x'] != '') ? $mDatos[$nM]['comidxxx']."-".$mDatos[$nM]['comcodxx']."-".$mDatos[$nM]['comcscxx']."-".$mDatos[$nM]['comcsc3x'] : $mDatos[$nM]['comidxxx']."-".$mDatos[$nM]['comcodxx']."-".$mDatos[$nM]['comcscxx']."-".$mDatos[$nM]['comcsc2x'] ?></td>
                      <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo  $mDatos[$nM]['comidcxx']."-".$mDatos[$nM]['comcodcx']."-".$mDatos[$nM]['comcsccx'] ?></td>
                      <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo  $mDatos[$nM]['comfecxx'] ?></td>
                      <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo  $mDatos[$nM]['comobsxx'] ?></td>
                      <td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($mDatos[$nM]['debitoxx']<>"")?((strpos($mDatos[$nM]['debitoxx']+0,'.') > 0) ? number_format($mDatos[$nM]['debitoxx'],2,',','.') : number_format($mDatos[$nM]['debitoxx'],0,',','.')):"0" ?></td>
                      <td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($mDatos[$nM]['creditox']<>"")?((strpos($mDatos[$nM]['creditox']+0,'.') > 0) ? number_format($mDatos[$nM]['creditox'],2,',','.') : number_format($mDatos[$nM]['creditox'],0,',','.')):"0" ?></td>
                      <td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo ($nSalAnt!="")?((strpos($nSalAnt+0,'.') > 0) ? number_format($nSalAnt,2,',','.') : number_format($nSalAnt,0,',','.')):"0" ?></td>
                    </tr>
                  <?php }
                } ?>
                <tr height="20" style="padding-left:4px;padding-right:4px">
                  <td style="background-color:#E3F6CE" class="letra7" align="right" colspan="4"><b>Total</b></td>
                  <td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b><?php echo ((strpos($nTDebxCue+0,'.') > 0) ? number_format($nTDebxCue,2,',','.') : number_format($nTDebxCue,0,',','.')) ?></b></td>
                  <td style="background-color:#E3F6CE" class="letra7" align="right" colspan="1"><b><?php echo ((strpos($nTDebxCue+0,'.') > 0) ? number_format($nTCrexCue,2,',','.') : number_format($nTCrexCue,0,',','.')) ?></b></td>
                  <td style="background-color:#E3F6CE" class="letra7" align="right"><b><?php echo ($nSalTot!="")?((strpos($nSalTot+0,'.') > 0) ? number_format($nSalTot,2,',','.') : number_format($nSalTot,0,',','.')):"0" ?></b></td>
                </tr>
              </table><br>
            </center>
          </form>
          </body>
          </html>
        <?php break;
        case 2:

          // PINTA POR EXCEL //
          $cData = '';
          $cNomFile = "AUXILIAR_CUENTAS_DETALLADO_POR_TERCERO_".$_COOKIE['kUsrId'].date("YmdHis").".xls";

          if ($_SERVER["SERVER_PORT"] != "") {
            $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
          } else {
            $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
          }

          $fOp = fopen($cFile, 'a');

          $nColSpan = 7;

          $cData .= '<table cellpadding="1" cellspacing="1" border="1" style="font-family:arial;border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">';
          $cData .= '<tr>';
          $cData .= '<td colspan="'.$nColSpan.'" style="font-size:14px;font-weight:bold"><center>AUXILIAR CUENTA DETALLADO POR TERCEROS</td>';
          $cData .= '</tr>';
          $cData .= '<tr>';
          $cData .= '<td colspan="'.$nColSpan.'"><B><center>'."DE: "." ".$dDesde." "."A: "." ".$dHasta.'</center></B></td>';
          $cData .= '</tr>';
          $cData .= '<tr>';
          $cData .= '<td colspan="4" style="font-weight:bold">NIT: '.$vCliDat['CLIIDXXX'].'</td>';
          $cData .= '<td colspan="'.($nColSpan-4).'"><B>'."TERCERO: ".$vCliDat['NOMBREXX'].'</B></td>';
          $cData .= '</tr>';
          $cData .= '<tr>';
          $cData .= '<td colspan="'.$nColSpan.'" style="font-weight:bold">FECHA Y HORA DE CONSULTA: '.$cMes." ".substr($dFecha,8,2)." "."DE ".substr($dFecha,0,4)." "."- ".date('H:i:s').'</td>';
          $cData .= '</tr>';
          $cData .= '<tr>';
          $cData .= '<td style="font-weight:bold;width:190px"><center>DOCUMENTO</td>';
          $cData .= '<td style="font-weight:bold;width:190px"><center>DOCUMENTO CRUCE</td>';
          $cData .= '<td style="font-weight:bold;width:80px"><center>FECHA</td>';
          $cData .= '<td style="font-weight:bold;width:400px"><center>DETALLE</td>';
          $cData .= '<td style="font-weight:bold;width:110px"><center>DEBITOS</td>';
          $cData .= '<td style="font-weight:bold;width:110px"><center>CREDITOS</td>';
          $cData .= '<td style="font-weight:bold;width:150px"><center>SALDO</td>';
          $cData .= '</tr>';

          $cCueAux   = "";  $cCueAux2  = "";
          $nTDebxCue = 0;   $nTCrexCue = 0;

          for($nM=0; $nM < count($mDatos); $nM++) {
            if(substr($dFecFin,0,4) < $cPerAno){
              $cPerAno = $cPerAno - 1;
              $dFecIni = $cPerAno."-01-01";
            }
            $zColorPro = "#000000";
            ##Impresion de total por cuenta##
            if($cCueAux2 != $mDatos[$nM]['pucidxxx']){
              if($cCueAux2 != ""){
                $cData .= '<tr height="20" style="padding-left:4px;padding-right:4px">';
                  $cData .= '<td style="background-color:#E3F6CE" calign="right" colspan="4"><b>Total</b></td>';
                  $cData .= '<td style="background-color:#E3F6CE" calign="right" colspan="1"><b>'.((strpos($nTDebxCue+0,'.') > 0) ? number_format($nTDebxCue,2,',','.') : number_format($nTDebxCue,0,',','.')).'</b></td>';
                  $cData .= '<td style="background-color:#E3F6CE" calign="right" colspan="1"><b>'.((strpos($nTCrexCue+0,'.') > 0) ? number_format($nTCrexCue,2,',','.') : number_format($nTCrexCue,0,',','.')).'</b></td>';
                  $cData .= '<td style="background-color:#E3F6CE" calign="right"><b>'.(($nSalTot<>"")?((strpos($nSalTot+0,'.') > 0) ? number_format($nSalTot,2,',','.') : number_format($nSalTot,0,',','.')):"0").'</b></td>';
                $cData .= '</tr>';
                $cData .= '<tr height="20" style="padding-left:4px;padding-right:4px">';
                  $cData .= '<td colspan="7">&nbsp;</td>';
                $cData .= '</tr>';

                $nTDebxCue = 0;
                $nTCrexCue = 0;
                $nSalTot   = 0;
              }
              $cCueAux2 = $mDatos[$nM]['pucidxxx'];
            }

            ##Fin Impresion de total por cuenta##
            if($cCueAux != $mDatos[$nM]['pucidxxx']) {
              $cCueAux = $mDatos[$nM]['pucidxxx'];
              $mSalAnt = explode("~",f_Saldo_x_Cuenta_Cliente($cTerId,$cCueAux,$dFecIni,$dFecFin,$cTipTer));
              $nSalAnt = 0;
              $nSalAnt = $mSalAnt[1];
              //nuevo calculo de saldo anterior por cuenta #0B610B
              $cData .= '<tr height="20" style="padding-left:4px;padding-right:4px">';
                $cData .= '<td style="background-color:#0B610B" calign="left" colspan="4"><b><font color=white>Cuenta: '.$mDatos[$nM]['pucidxxx'].'</font></b></td>';
                $cData .= '<td style="background-color:#0B610B" calign="right" colspan="2"><b><font color=white>Saldo Anterior</font></b></td>';
                $cData .= '<td style="background-color:#0B610B" calign="right"><b><font color=white>'.(($mSalAnt[1]!="")?((strpos($mSalAnt[1]+0,'.') > 0) ? number_format($mSalAnt[1],2,',','.') : number_format($mSalAnt[1],0,',','.')):"0").'</font></b></td>';
              $cData .= '</tr>';
            }

            //Calculo el saldo y lo guardo en otra variable para mostrar los totales por cuenta
            $nSalAnt   += $mDatos[$nM]['debitoxx'] - $mDatos[$nM]['creditox'];

            $nTDebxCue += ($mDatos[$nM]['debitoxx'] != ""?$mDatos[$nM]['debitoxx']:"0");
            $nTCrexCue += ($mDatos[$nM]['creditox'] != ""?$mDatos[$nM]['creditox']:"0");
            $nSalTot    = $nSalAnt;
            if (($mDatos[$nM]['debitoxx']+0) > 0 || ($mDatos[$nM]['creditox']+0) > 0) {
              $cData .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
                $cData .= '<td calign="left" style = "color:'.$zColorPro.'">'.(($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mDatos[$nM]['comcsc3x'] != '') ? $mDatos[$nM]['comidxxx']."-".$mDatos[$nM]['comcodxx']."-".$mDatos[$nM]['comcscxx']."-".$mDatos[$nM]['comcsc3x'] : $mDatos[$nM]['comidxxx']."-".$mDatos[$nM]['comcodxx']."-".$mDatos[$nM]['comcscxx']."-".$mDatos[$nM]['comcsc2x']).'</td>';
                $cData .= '<td calign="left"   style = "color:'.$zColorPro.'">'.($mDatos[$nM]['comidcxx']."-".$mDatos[$nM]['comcodcx']."-".$mDatos[$nM]['comcsccx']).'</td>';
                $cData .= '<td calign="center" style = "color:'.$zColorPro.'">'.$mDatos[$nM]['comfecxx'].'</td>';
                $cData .= '<td calign="left"   style = "color:'.$zColorPro.'">'.$mDatos[$nM]['comobsxx'].'</td>';
                $cData .= '<td calign="right"  style = "color:'.$zColorPro.'">'.(($mDatos[$nM]['debitoxx']<>"")?((strpos($mDatos[$nM]['debitoxx']+0,'.') > 0) ? number_format($mDatos[$nM]['debitoxx'],2,',','.') : number_format($mDatos[$nM]['debitoxx'],0,',','.')):"0").'</td>';
                $cData .= '<td calign="right"  style = "color:'.$zColorPro.'">'.(($mDatos[$nM]['creditox']<>"")?((strpos($mDatos[$nM]['creditox']+0,'.') > 0) ? number_format($mDatos[$nM]['creditox'],2,',','.') : number_format($mDatos[$nM]['creditox'],0,',','.')):"0").'</td>';
                $cData .= '<td calign="right"  style = "color:'.$zColorPro.'">'.(($nSalAnt!="")?((strpos($nSalAnt+0,'.') > 0) ? number_format($nSalAnt,2,',','.') : number_format($nSalAnt,0,',','.')):"0").'</td>';
              $cData .= '</tr>';
            }
          }
          $cData .= '<tr height="20" style="padding-left:4px;padding-right:4px">';
            $cData .= '<td style="background-color:#E3F6CE" calign="right" colspan="4"><b>Total</b></td>';
            $cData .= '<td style="background-color:#E3F6CE" calign="right" colspan="1"><b>'.(((strpos($nTDebxCue+0,'.') > 0) ? number_format($nTDebxCue,2,',','.') : number_format($nTDebxCue,0,',','.'))).'</b></td>';
            $cData .= '<td style="background-color:#E3F6CE" calign="right" colspan="1"><b>'.(((strpos($nTDebxCue+0,'.') > 0) ? number_format($nTCrexCue,2,',','.') : number_format($nTCrexCue,0,',','.'))).'</b></td>';
            $cData .= '<td style="background-color:#E3F6CE" calign="right"><b>'.(($nSalTot!="")?((strpos($nSalTot+0,'.') > 0) ? number_format($nSalTot,2,',','.') : number_format($nSalTot,0,',','.')):"0").'</b></td>';
          $cData .= '</tr>';
          $cData .= '</table>';

          fwrite($fOp, $cData);
          fclose($fOp);

          if (file_exists($cFile)) {	
            if ($_SERVER["SERVER_PORT"] != "") {
              header('Content-Type: application/octet-stream');
              header("Content-Disposition: attachment; filename=\"".basename($cNomFile)."\";");
              header('Expires: 0');
              header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
              header("Cache-Control: private",false); // required for certain browsers
              header('Pragma: public');

              ob_clean();
              flush();
              readfile($cFile);
              exit;
            }
          } else {
            $nSwitch = 1;
            if ($_SERVER["SERVER_PORT"] != "") {
              f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
            } else {
              $cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
            }
          }

        break;
        case 3 :
          $cRoot = $_SERVER['DOCUMENT_ROOT'];

          define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
          require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

          class PDF extends FPDF {
            function Header() {
              global $cRoot; global $cPlesk_Skin_Directory;
              global $cAlfa; global $cTipoCta; global $cMes; global $dFecha; global $cTerId; global $nPag; global $dDesde; global $dHasta;

            if($cAlfa == "INTERLOG" || $cAlfa == "DESARROL" || $cAlfa == "PRUEBASX" ){

                $this->SetXY(13,7);
                $this->Cell(42,28,'',1,0,'C');
                $this->Cell(213,28,'',1,0,'C');

                // Dibujo //
                $this->Image($cRoot.$cPlesk_Skin_Directory.'/MaryAire.jpg',14,8,40,25);

                $this->SetFont('verdana','',16);
                $this->SetXY(55,15);
                $this->Cell(213,8,"AUXILIAR CUENTAS DETALLADO POR TERCERO",0,0,'C');
                $this->Ln(8);
                $this->SetFont('verdana','',12);
                $this->SetX(55);
                $this->Cell(213,6,"DE: "." ".$dDesde." "."A: "." ".$dHasta,0,0,'C');
                $this->Ln(15);
                $this->SetX(13);
              }else{
                $this->SetXY(13,7);
                $this->Cell(255,15,'',1,0,'C');

                switch ($cAlfa) {
                  case "LOGINCAR":
                  case "DELOGINCAR":
                  case "TELOGINCAR":
                    $this->Image($cRoot.$cPlesk_Skin_Directory.'/Logo_Login_Cargo_Ltda_2.jpg',14,8,40,12);
                  break;
                  case "TRLXXXXX":
                  case "DETRLXXXXX":
                  case "TETRLXXXXX":
                    $this->Image($cRoot.$cPlesk_Skin_Directory.'/logobma1.jpg',15,8,40,13);
                  break;
                  case "ADIMPEXX":
                  case "TEADIMPEXX":
                  case "DEADIMPEXX":
                  // case "DEGRUPOGLA":
                  // case "TEGRUPOGLA":
                    $this->Image($cRoot.$cPlesk_Skin_Directory.'/logoAdimpex.jpg',18,8,18,13);
                  break;
                  case "ROLDANLO"://ROLDAN
                  case "TEROLDANLO"://ROLDAN
                  case "DEROLDANLO"://ROLDAN
                    $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',14,8,37,12);
                  break;
                  case "GRUMALCO"://GRUMALCO
                  case "TEGRUMALCO"://GRUMALCO
                  case "DEGRUMALCO"://GRUMALCO
                    $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',14,8,37,12);
                  break;
                  case "ALADUANA": //ALADUANA
                  case "TEALADUANA": //ALADUANA
                  case "DEALADUANA": //ALADUANA
                  case "DEDESARROL":
                    $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',15,8,22,13);
                  break;
                  case "ANDINOSX": //ANDINOSX
                  case "TEANDINOSX": //ANDINOSX
                  case "DEANDINOSX": //ANDINOSX
                  case "DEDESARROL":
                    $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoandinos.jpg', 14, 8, 27, 13);
                  break;
                  case "GRUPOALC": //GRUPOALC
                  case "TEGRUPOALC": //GRUPOALC
                  case "DEGRUPOALC": //GRUPOAL
                    $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalc.jpg',15,8,25,13);
                  break;
                  case "AAINTERX": //AAINTERX
                  case "TEAAINTERX": //AAINTERX
                  case "DEAAINTERX": //AAINTERX
                    $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointernacional.jpg',15,8,22,13);
                  break;
                  case "AALOPEZX":
                  case "TEAALOPEZX":
                  case "DEAALOPEZX":
                  case "DEDESARROL":
                    $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoaalopez.png', 15, 8, 22, 13);
                  break;
                  case "ADUAMARX": //ADUAMARX
                  case "TEADUAMARX": //ADUAMARX
                  case "DEADUAMARX": //ADUAMARX
                    $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg',15,8.5,13);
                  break;
                  case "SOLUCION": //SOLUCION
                  case "TESOLUCION": //SOLUCION
                  case "DESOLUCION": //SOLUCION
                    $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',15,8,30);
                  break;
									case "FENIXSAS": //FENIXSAS
									case "TEFENIXSAS": //FENIXSAS
									case "DEFENIXSAS": //FENIXSAS
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg',15,9,38);
                  break;
                  case "COLVANXX": //COLVANXX
                  case "TECOLVANXX": //COLVANXX
                  case "DECOLVANXX": //COLVANXX
                    $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logocolvan.jpg',15,8,32);
                  break;
                  case "INTERLAC": //INTERLAC
                  case "TEINTERLAC": //INTERLAC
                  case "DEINTERLAC": //INTERLAC
                    $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointerlace.jpg',15,8,29);
                  break;
									case "DHLEXPRE": //DHLEXPRE
									case "TEDHLEXPRE": //DHLEXPRE
									case "DEDHLEXPRE": //DHLEXPRE
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',14,8,37,12);
									break;
                  case "KARGORUX": //KARGORUX
									case "TEKARGORUX": //KARGORUX
									case "DEKARGORUX": //KARGORUX
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logokargoru.jpg',14,8.5,37,12);
									break;
                  case "ALOGISAS": //LOGISTICA
                  case "TEALOGISAS": //LOGISTICA
                  case "DEALOGISAS": //LOGISTICA
										$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logologisticasas.jpg',14,8,32);
									break;
                  case "PROSERCO":
                  case "TEPROSERCO":
                  case "DEPROSERCO":
                    $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoproserco.png',14,7.3,25);
                  break;
                  case "MANATIAL":
                  case "TEMANATIAL":
                  case "DEMANATIAL":
                    $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomanantial.jpg',14,8,45,12);
                  break;
                  case "DSVSASXX":
                  case "DEDSVSASXX":
                  case "TEDSVSASXX":
                    $this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logodsv.jpg',14,8.5,45,12);
                  break;
                  case "MELYAKXX":    //MELYAK
                  case "DEMELYAKXX":  //MELYAK
                  case "TEMELYAKXX":  //MELYAK
                    $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomelyak.jpg', 14,8.5,45,12);
                  break;
                  case "FEDEXEXP":
                  case "DEFEDEXEXP":
                  case "TEFEDEXEXP":
                    $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logofedexexp.jpg', 14,8.5,35,12);
                  break;
                  case "EXPORCOM":
                  case "DEEXPORCOM":
                  case "TEEXPORCOM":
                    $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoexporcomex.jpg', 14,8.5,28,12);
                  break;
                  case "HAYDEARX":
                  case "DEHAYDEARX":
                  case "TEHAYDEARX":
                    $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logohaydear.jpeg', 14,8.5,45,12);
                  break;
                  case "CONNECTA":
                  case "DECONNECTA":
                  case "TECONNECTA":
                    $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoconnecta.jpg', 14,8.5,22,12);
                  break;
                  case "OPENEBCO":
                  case "DEOPENEBCO":
                  case "TEOPENEBCO":
                    $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoopen.jpg', 14,8.5,35,12);
                  break;
                }
                $this->SetFont('verdana','',16);
                $this->SetXY(13,8);
                $this->Cell(255,8,"AUXILIAR CUENTAS DETALLADO POR TERCERO",0,0,'C');
                $this->Ln(8);
                $this->SetFont('verdana','',12);
                $this->SetX(13);
                $this->Cell(255,6,"DE: "." ".$dDesde." "."A: "." ".$dHasta,0,0,'C');
                $this->Ln(10);
                $this->SetX(13);
              }

              if($this->PageNo() > 1 && $nPag ==1){
                $this->SetFont('verdana','B',7);
                $this->SetWidths(array('45','45','20','61','28','28','28'));
                $this->SetAligns(array('L','L','L','L','R','R','R'));
                $this->SetX(13);
                $this->Row(array("Documento",
                                "Documento Fuente",
                                "Fecha",
                                "Detalle",
                                "Debitos",
                                "Creditos",
                                "Saldo"));
                $this->SetFont('verdana','',7);
                $this->SetAligns(array('L','L','L','L','R','R','R'));
                $this->SetX(13);
              }
            }
            function Footer() {
              $this->SetY(-10);
              $this->SetFont('verdana','',6);
              $this->Cell(0,5,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
            }

            function SetWidths($w) {
              //Set the array of column widths
              $this->widths=$w;
            }

            function SetAligns($a){
              //Set the array of column alignments
              $this->aligns=$a;
            }

            function Row($data){
              //Calculate the height of the row
              $nb=0;
              for($i=0;$i<count($data);$i++)
                  $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
              $h=4*$nb;
              //Issue a page break first if needed
              $this->CheckPageBreak($h);
              //Draw the cells of the row
              for($i=0;$i<count($data);$i++)
              {
                  $w=$this->widths[$i];
                  $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                  //Save the current position
                  $x=$this->GetX();
                  $y=$this->GetY();
                  //Draw the border
                  $this->Rect($x,$y,$w,$h);
                  //Print the text
                  $this->MultiCell($w,4,$data[$i],0,$a);
                  //Put the position to the right of the cell
                  $this->SetXY($x+$w,$y);
              }
              //Go to the next line
              $this->Ln($h);
            }

            function CheckPageBreak($h){
              //If the height h would cause an overflow, add a new page immediately
              if($this->GetY()+$h>$this->PageBreakTrigger)
                  $this->AddPage($this->CurOrientation);
            }

            function NbLines($w,$txt){
              //Computes the number of lines a MultiCell of width w will take
              $cw=&$this->CurrentFont['cw'];
              if($w==0)
                  $w=$this->w-$this->rMargin-$this->x;
              $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
              $s=str_replace("\r",'',$txt);
              $nb=strlen($s);
              if($nb>0 and $s[$nb-1]=="\n")
                  $nb--;
              $sep=-1;
              $i=0;
              $j=0;
              $l=0;
              $nl=1;
              while($i<$nb){
                $c=$s[$i];
                if($c=="\n"){
                  $i++;
                  $sep=-1;
                  $j=$i;
                  $l=0;
                  $nl++;
                  continue;
                }
                if($c==' ')
                    $sep=$i;
                $l+=$cw[$c];
                if($l>$wmax){
                  if($sep==-1){
                    if($i==$j)
                        $i++;
                  }
                  else
                      $i=$sep+1;
                  $sep=-1;
                  $j=$i;
                  $l=0;
                  $nl++;
                }
                else
                    $i++;
              }
              return $nl;
            }

          }

          $pdf = new PDF('L','mm','Letter');
          $pdf->AddFont('verdana','','');
          $pdf->AddFont('verdana','B','');
          $pdf->AliasNbPages();
          $pdf->SetMargins(0,0,0);

          $pdf->AddPage();

          if($cTerId <> ""){
            $pdf->SetX(13);
            $pdf->SetFont('verdana','B',8);
            $pdf->Cell(22,5,"TERCERO:",0,0,'L');
            $pdf->SetFont('verdana','',8);
            $pdf->Cell(163,5,$vCliDat['CLINOMXX'],0,0,'L');

            $pdf->SetFont('verdana','B',8);
            $pdf->Cell(10,5,"NIT:",0,0,'L');
            $pdf->SetFont('verdana','',8);
            $pdf->Cell(60,5,$vCliDat['CLIIDXXX'],0,0,'L');

            $pdf->Ln(5);
            $pdf->SetX(13);
            $pdf->SetFont('verdana','B',8);
            $pdf->Cell(22,5,"DIRECCION:",0,0,'L');
            $pdf->SetFont('verdana','',8);
            $pdf->Cell(153,5,$vCliDat['CLIDIRXX'],0,0,'L');

            $pdf->SetFont('verdana','B',8);
            $pdf->Cell(20,5,"TELEFONO:",0,0,'L');
            $pdf->SetFont('verdana','',8);
            $pdf->Cell(60,5,$vCliDat['CLITELXX'],0,0,'L');
            $pdf->Ln(10);
          }

          $pdf->Ln(4);
          $pdf->SetFont('verdana','B',7);
          $pdf->SetWidths(array('45','45','20','61','28','28','28'));
          $pdf->SetAligns(array('L','L','L','L','R','R','R'));
          $pdf->SetX(13);
          $pdf->Row(array("Documento",
                          "Documento Fuente",
                          "Fecha",
                          "Detalle",
                          "Debitos",
                          "Creditos",
                          "Saldo"));
          $pdf->SetFont('verdana','',7);
          $pdf->SetAligns(array('L','L','L','L','R','R','R'));

          $cCueAux   = "";  $cCueAux2  = "";
          $nTDebxCue = 0;   $nTCrexCue = 0;
          $nPag = 1;

          for($nM=0; $nM < count($mDatos); $nM++) {
            if(substr($dFecFin,0,4) < $cPerAno){
              $cPerAno = $cPerAno - 1;
              $dFecIni = $cPerAno."-01-01";
            }
            $zColorPro = "#000000";
            ##Impresion de total por cuenta##
            if($cCueAux2 != $mDatos[$nM]['pucidxxx']){
              if($cCueAux2 != ""){
                $pdf->SetWidths(array('171','28','28','28'));
                $pdf->SetAligns(array('C','R','R','R'));
                $pdf->SetFont('verdana','B',7);
                $pdf->SetX(13);
                $pdf->Row(array("TOTAL",
                      ((strpos($nTDebxCue+0,'.') > 0) ? number_format($nTDebxCue,2,',','.') : number_format($nTDebxCue,0,',','.')),
                      ((strpos($nTCrexCue+0,'.') > 0) ? number_format($nTCrexCue,2,',','.') : number_format($nTCrexCue,0,',','.')),
                      ((strpos($nSalTot+0,'.') > 0)   ? number_format($nSalTot,2,',','.')   : number_format($nSalTot,0,',','.'))));

                $nTDebxCue = 0;
                $nTCrexCue = 0;
                $nSalTot   = 0;
              }
              $cCueAux2 = $mDatos[$nM]['pucidxxx'];
            }

            ##Fin Impresion de total por cuenta##
            if($cCueAux != $mDatos[$nM]['pucidxxx']) {
              $cCueAux = $mDatos[$nM]['pucidxxx'];
              $mSalAnt = explode("~",f_Saldo_x_Cuenta_Cliente($cTerId,$cCueAux,$dFecIni,$dFecFin,$cTipTer));
              $nSalAnt = 0;
              $nSalAnt = $mSalAnt[1];
              $pdf->SetX(13);
              $pdf->SetWidths(array('199','28','28'));
              $pdf->SetAligns(array('L','R','R'));
              $pdf->SetFont('verdana','B',7);
              $pdf->Row(array("CUENTA {$mDatos[$nM]['pucidxxx']}","SALDO ANTERIOR",(($mSalAnt[1]!="")?((strpos($mSalAnt[1]+0,'.') > 0) ? number_format($mSalAnt[1],2,',','.') : number_format($mSalAnt[1],0,',','.')):"0")));

              $pdf->SetFont('verdana','',7);
              $pdf->SetWidths(array('45','45','20','61','28','28','28'));
              $pdf->SetAligns(array('L','L','L','L','R','R','R'));
            }

            //Calculo el saldo y lo guardo en otra variable para mostrar los totales por cuenta
            $nSalAnt   += $mDatos[$nM]['debitoxx'] - $mDatos[$nM]['creditox'];

            $nTDebxCue += ($mDatos[$nM]['debitoxx'] != ""?$mDatos[$nM]['debitoxx']:"0");
            $nTCrexCue += ($mDatos[$nM]['creditox'] != ""?$mDatos[$nM]['creditox']:"0");
            $nSalTot    = $nSalAnt;
            $pdf->SetX(13);
            if (($mDatos[$nM]['debitoxx']+0) > 0 || ($mDatos[$nM]['creditox']+0) > 0) {
              $pdf->Row(array(($vSysStr['financiero_aplica_tercer_consecutivo'] == 'SI' && $mDatos[$nM]['comcsc3x'] != '') ? $mDatos[$nM]['comidxxx']."-".$mDatos[$nM]['comcodxx']."-".$mDatos[$nM]['comcscxx']."-".$mDatos[$nM]['comcsc3x'] : $mDatos[$nM]['comidxxx']."-".$mDatos[$nM]['comcodxx']."-".$mDatos[$nM]['comcscxx']."-".$mDatos[$nM]['comcsc2x'],
                                $mDatos[$nM]['comidcxx']."-".$mDatos[$nM]['comcodcx']."-".$mDatos[$nM]['comcsccx'],
                                $mDatos[$nM]['comfecxx'],
                                $mDatos[$nM]['comobsxx'],
                                (($mDatos[$nM]['debitoxx']<>"")?((strpos($mDatos[$nM]['debitoxx']+0,'.') > 0) ? number_format($mDatos[$nM]['debitoxx'],2,',','.') : number_format($mDatos[$nM]['debitoxx'],0,',','.')):"0"),
                                (($mDatos[$nM]['creditox']<>"")?((strpos($mDatos[$nM]['creditox']+0,'.') > 0) ? number_format($mDatos[$nM]['creditox'],2,',','.') : number_format($mDatos[$nM]['creditox'],0,',','.')):"0"),
                                (($nSalAnt<>"")?((strpos($nSalAnt+0,'.') > 0) ? number_format($nSalAnt,2,',','.') : number_format($nSalAnt,0,',','.')):"0")));
            }
          }
          $pdf->SetX(13);
          $pdf->SetWidths(array('171','28','28','28'));
          $pdf->SetAligns(array('C','R','R','R'));
          $pdf->SetFont('verdana','B',7);
          $pdf->Row(array("TOTAL",
                          ((strpos($nTDebxCue+0,'.') > 0) ? number_format($nTDebxCue,2,',','.') : number_format($nTDebxCue,0,',','.')),
                          ((strpos($nTCrexCue+0,'.') > 0) ? number_format($nTCrexCue,2,',','.') : number_format($nTCrexCue,0,',','.')),
                          ((strpos($nSalAnt+0,'.')  > 0) ? number_format($nSalAnt,2,',','.')   : number_format($nSalAnt,0,',','.'))));

          $nPag = 0;
          $pdf->Ln(5);
          $pdf->SetX(13);
          $pdf->SetFont('verdana','B',8);
          $pdf->Cell(50,5,"FECHA Y HORA DE CONSULTA:",0,0,'L');
          $pdf->SetFont('verdana','',8);
          $pdf->Cell(205,5,date('Y-m-d').' - '.date('H:i:s'),0,0,'L');

          $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";

          $pdf->Output($cFile);

          if (file_exists($cFile)){
            chmod($cFile,intval($vSysStr['system_permisos_archivos'],8));
          } else {
            f_Mensaje(__FILE__,__LINE__,"No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
          }
          echo "<html><script>document.location='$cFile';</script></html>";
        break;
      }
    }//if ($nSwitch == 0) {
  }//if ($cEjePro == 0) {
  ?>

  <?php
  if ($_SERVER["SERVER_PORT"] == "") {
    /**
     * Se ejecuto por el proceso en background
     * Actualizo el campo de resultado y nombre del archivo
     */
    $vParBg['pbarespr'] = ($nSwitch == 0) ? "EXITOSO" : "FALLIDO";  //Resultado Proceso
    $vParBg['pbaexcxx'] = ((count($mDatos) > 0) ? $cNomFile : "");  //Nombre Archivos Excel
    $vParBg['pbaerrxx'] = $cMsj;                                    //Errores al ejecutar el Proceso
    $vParBg['regdfinx'] = date('Y-m-d H:i:s');                      //Fecha y Hora Fin Ejecucion Proceso
    $vParBg['pbaidxxx'] = $vArg[0];                                 //id Proceso

    #Incluyendo la clase de procesos en background
    $ObjProBg = new cProcesosBackground();
    $mReturnProBg = $ObjProBg->fnFinalizarProcesoBackground($vParBg);

    #Imprimiendo resumen de todo ok.
    if ($mReturnProBg[0] == "false") {
      $nSwitch = 1;
      for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
        $cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
        $cMsj .= $mReturnProBg[$nR] . "\n";
      }
    }
  } // fin del if ($_SERVER["SERVER_PORT"] == "") ?>
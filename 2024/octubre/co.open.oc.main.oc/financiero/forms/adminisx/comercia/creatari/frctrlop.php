<?php
  /**
	 * Genera Reporte Autorizar Tarifas
	 * --- Descripcion: Permite Generar el Reporte Autorizar Tarifas 
	 * @author Johana Arboleda Ramos <johana.arboleda@openits.co>
   * @version 001
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
    include("../../../../config/config.php");
    include("../../../../libs/php/utility.php");
    include("../../../../../libs/php/utiprobg.php");
  }

  if ($_SERVER["SERVER_PORT"] != "") {
    $cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
  }

  if ($_SERVER["SERVER_PORT"] == "") {
    $cTarTip = $_POST['cTarTip'];
    $cCliId  = $_POST['cCliId'];
    $cUsrId  = $_POST['cUsrId'];
    $dDesde  = $_POST['dDesde'];
    $dHasta  = $_POST['dHasta'];
    $cTipRep = $_POST['cTipRep'];
    $cLogAcc = $_POST['cLogId'];
    $cLogAcc = $_POST['cLogAcc'];
  }

  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
    $cEjePro = 1;

    $strPost  = "|cTarTip~".$cTarTip;
    $strPost .= "|cCliId~" .$cCliId;
    $strPost .= "|cUsrId~" .$cUsrId;
    $strPost .= "|dDesde~" .$dDesde;
    $strPost .= "|dHasta~" .$dHasta;
    $strPost .= "|cTipRep~".$cTipRep;
    $strPost .= "|cLogId~" .$cLogId;
    $strPost .= "|cLogAcc~".$cLogAcc;
    $nRegistros = 0;

    $vParBg['pbadbxxx'] = $cAlfa;                                   //Base de Datos
    $vParBg['pbamodxx'] = "FACTURACION";                            //Modulo
    $vParBg['pbatinxx'] = "REPORTEAUTORIZARTARIFAS";                //Tipo Interface
    $vParBg['pbatinde'] = "REPORTE AUTORIZAR TARIFAS";              //Descripcion Tipo de Interfaz
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

      $dFecha  = date('Y-m-d');
      $cMes = "";
      switch (substr($dFecha,5,2)){
        case "01": $cMes="ENERO";       break;
        case "02": $cMes="FEBRERO";     break;
        case "03": $cMes="MARZO";       break;
        case "04": $cMes="ABRIL";       break;
        case "05": $cMes="MAYO";        break;
        case "06": $cMes="JUNIO";       break;
        case "07": $cMes="JULIO";       break;
        case "08": $cMes="AGOSTO";      break;
        case "09": $cMes="SEPTIEMBRE";  break;
        case "10": $cMes="OCTUBRE";     break;
        case "11": $cMes="NOVIEMBRE";   break;
        case "12": $cMes="DICIEMBRE";   break;
      }

      $nADesde = substr($dDesde,0,4);
      $nAHasta = substr($dHasta,0,4);

      //Consultando la tabla de LOG tarifas
      $qConLog  = "SELECT * ";
      $qConLog .= "FROM $cAlfa.flta$nADesde ";
      $qConLog .= "WHERE ";
      if ($cLogId != "") {
        $qConLog .= "logidxxx = \"$cLogId\" AND ";
      } else {
        if ($cCliId != "") {
          $qConLog .= "cliidxxx = \"$cCliId\" AND ";
        } elseif($cTarTip != "") {
          $qConLog .= "tartipxx = \"$cTarTip\" AND ";
        }
        if ($cUsrId != ""){
          $qConLog .= "regusrxx = \"$cUsrId\" AND ";
        }
        if ($cLogAcc != ""){
          $qConLog .= "logaccxx = \"$cLogAcc\" AND ";
        }
      }
      $qConLog .= "regfcrex BETWEEN \"$dDesde\" AND \"$dHasta\"";
      $xConLog  = f_MySql("SELECT","",$qConLog,$xConexion01,"");
      $mData = array();
      while ($xRCL = mysql_fetch_assoc($xConLog)) {

        switch ($xRCL['logaccxx']) {
          case 'NUEVO':
            $xRCL['logaccxx'] = "CREAR";
          break;
          case 'ANULAR':
            $xRCL['logaccxx'] = "CAMBIO ESTADO";
          break;
          case 'BORRAR':
            $xRCL['logaccxx'] = "ELIMINAR";
          break;
          case 'AUTORIZARTARIFA':
            $xRCL['logaccxx'] = "AUTORIZAR TARIFA";
          break;
          default:
            //No hace nada se muestra como esta guardado
          break;
        }

        $nInd_mData = count($mData);
        $mData[$nInd_mData] = $xRCL;
        if ($cTipRep == "DETALLADO") {
          // Extrayendo el detalle de los campos
          $mDatOld = json_decode($xRCL['logoldxx'], true);
          $mDatNew = json_decode($xRCL['lognewxx'], true);

          $mCamAux = array();
          for($i=0; $i<count($mDatOld); $i++) {
            $mCamAux["{$mDatOld[$i]['campoxxx']}"]['descamxx'] = $mDatOld[$i]['descamxx'];
            $mCamAux["{$mDatOld[$i]['campoxxx']}"]['valorold'] = $mDatOld[$i]['valorxxx'];
          }

          for($i=0; $i<count($mDatNew); $i++) {
            $mCamAux["{$mDatNew[$i]['campoxxx']}"]['descamxx'] = $mDatNew[$i]['descamxx'];
            $mCamAux["{$mDatNew[$i]['campoxxx']}"]['valornew'] = $mDatNew[$i]['valorxxx'];
          }

          $nCan = 0;
          foreach ($mCamAux as $cKey => $cValue) {
            if ($nCan > 0) {
              $nInd_mData = count($mData);
            }
            $mData[$nInd_mData]['descamxx'] = $cValue['descamxx'];
            $mData[$nInd_mData]['valorold'] = $cValue['valorold'];
            $mData[$nInd_mData]['valornew'] = $cValue['valornew'];
            $nCan++;
          }
        }
      }

      // echo "<pre>";
      // print_r($mData);
      // echo "</pre>";
      // die();

      // PINTA POR EXCEL //
      $cData = '';
      $cNomFile = "REPORTE_AUTORIZAR_TARIFAS_".$_COOKIE['kUsrId'].date("YmdHis").".xls";

      if ($_SERVER["SERVER_PORT"] != "") {
        $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
      } else {
        $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
      }

      $fOp = fopen($cFile, 'a');

      $nColSpan = ($cTipRep == "DETALLADO") ? 23 : 20;

      $cData .= '<table cellpadding="1" cellspacing="1" border="1" style="font-family:arial;border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">';
      $cData .= '<tr>';
        $cData .= '<td colspan="'.$nColSpan.'" style="font-size:14px;font-weight:bold" bgcolor = "#39D052"><center>REPORTE AUTORIZAR TARIFAS</td>';
      $cData .= '</tr>';
      $cData .= '<tr>';
        $cData .= '<td colspan="'.$nColSpan.'" bgcolor = "#5DD052"><B><center>'."DE: "." ".$dDesde." "."A: "." ".$dHasta.'</center></B></td>';
      $cData .= '</tr>';
      $cData .= '<tr>';
        $cData .= '<td colspan="'.$nColSpan.'" style="font-weight:bold" bgcolor = "#5DD052">FECHA Y HORA DE CONSULTA: '.$cMes." ".substr($dFecha,8,2)." "."DE ".substr($dFecha,0,4)." "."- ".date('H:i:s').'</td>';
      $cData .= '</tr>';
      $cData .= '<tr>';
        $cData .= '<td style="font-weight:bold;width:100px" bgcolor = "#E3F6CE"><center>ID LOG</td>';
        $cData .= '<td style="font-weight:bold;width:150px" bgcolor = "#E3F6CE"><center>ACCION</td>';
        $cData .= '<td style="font-weight:bold;width:100px" bgcolor = "#E3F6CE"><center>TIPO DE TARIFA</td>';
        $cData .= '<td style="font-weight:bold;width:200px" bgcolor = "#E3F6CE"><center>NIT DEL CLIENTE O ID GRUPO</td>';
        $cData .= '<td style="font-weight:bold;width:350px" bgcolor = "#E3F6CE"><center>NOMBRE DEL CLIENTE O DESCRIPCION GRUPO</td>';
        $cData .= '<td style="font-weight:bold;width:200px" bgcolor = "#E3F6CE"><center>ID DEL CONCEPTO DE COBRO</td>';
        $cData .= '<td style="font-weight:bold;width:350px" bgcolor = "#E3F6CE"><center>DESCRIPCION DEL CONCEPTO DE COBRO</td>';
        $cData .= '<td style="font-weight:bold;width:350px" bgcolor = "#E3F6CE"><center>DESCRIPCION PERSONALIZADA DEL CONCEPTO DE COBRO</td>';
        $cData .= '<td style="font-weight:bold;width:150px" bgcolor = "#E3F6CE"><center>ID FORMA DE COBRO</td>';
        $cData .= '<td style="font-weight:bold;width:350px" bgcolor = "#E3F6CE"><center>DESCRIPCION FORMA DE COBRO</td>';
        $cData .= '<td style="font-weight:bold;width:100px" bgcolor = "#E3F6CE"><center>TARIFA POR</td>';
        $cData .= '<td style="font-weight:bold;width:100px" bgcolor = "#E3F6CE"><center>ID TARIFA POR</td>';
        $cData .= '<td style="font-weight:bold;width:200px" bgcolor = "#E3F6CE"><center>DESCRIPCION TARIFA POR</td>';
        $cData .= '<td style="font-weight:bold;width:350px" bgcolor = "#E3F6CE"><center>SUCURSALES</td>';
        $cData .= '<td style="font-weight:bold;width:150px" bgcolor = "#E3F6CE"><center>TIPO DE OPERACION</td>';
        $cData .= '<td style="font-weight:bold;width:200px" bgcolor = "#E3F6CE"><center>MODO DE TRANSPORTE</td>';
        if ($cTipRep == "DETALLADO") {
          $cData .= '<td style="font-weight:bold;width:350px" bgcolor = "#E3F6CE"><center>CAMPO</td>';
          $cData .= '<td style="font-weight:bold;width:250px" bgcolor = "#E3F6CE"><center>VALOR ANTERIOR</td>';
          $cData .= '<td style="font-weight:bold;width:250px" bgcolor = "#E3F6CE"><center>VALOR NUEVO</td>';
        }
        $cData .= '<td style="font-weight:bold;width:150px" bgcolor = "#E3F6CE"><center>ID USUARIO</td>';
        $cData .= '<td style="font-weight:bold;width:200px" bgcolor = "#E3F6CE"><center>NOMBRE USUARIO</td>';
        $cData .= '<td style="font-weight:bold;width:100px" bgcolor = "#E3F6CE"><center>FECHA</td>';
        $cData .= '<td style="font-weight:bold;width:100px" bgcolor = "#E3F6CE"><center>HORA</td>';
      $cData .= '</tr>';
      fwrite($fOp, $cData);
      
      for ($i=0; $i<count($mData); $i++) {
        $cData  = '<tr>';
          $cData .= '<td align="left">'.$mData[$i]['logidxxx'].'</td>';
          $cData .= '<td align="center">'.$mData[$i]['logaccxx'].'</td>';
          $cData .= '<td align="center">'.$mData[$i]['tartipxx'].'</td>';
          $cData .= '<td align="center">'.$mData[$i]['cliidxxx'].'</td>';
          $cData .= '<td align="left">'.utf8_encode($mData[$i]['clinomxx']).'</td>';
          $cData .= '<td align="center">'.$mData[$i]['seridxxx'].'</td>';
          $cData .= '<td align="left">'.utf8_encode($mData[$i]['serdesxx']).'</td>';
          $cData .= '<td align="left">'.utf8_encode($mData[$i]['serdespc']).'</td>';
          $cData .= '<td align="center">'.$mData[$i]['fcoidxxx'].'</td>';
          $cData .= '<td align="left">'.utf8_encode($mData[$i]['fcodesxx']).'</td>';
          $cData .= '<td align="center">'.$mData[$i]['fcotptxx'].'</td>';
          $cData .= '<td align="center">'.$mData[$i]['fcotpixx'].'</td>';
          $cData .= '<td align="left">'.utf8_encode($mData[$i]['fcotpdxx']).'</td>';
          $cData .= '<td align="left">'.str_replace("~",",",$mData[$i]['sucidxxx']).'</td>';
          $cData .= '<td align="left">'.$mData[$i]['fcotopxx'].'</td>';
          $cData .= '<td align="left">'.str_replace("~",",",$mData[$i]['fcomtrxx']).'</td>';
          if ($cTipRep == "DETALLADO") {
            $cData .= '<td align="left">'.$mData[$i]['descamxx'].'</td>';
            $cData .= '<td align="left">'.str_replace("~",",",$mData[$i]['valorold']).'</td>';
            $cData .= '<td align="left">'.str_replace("~",",",$mData[$i]['valornew']).'</td>';
          }
          $cData .= '<td align="center">'.$mData[$i]['regusrxx'].'</td>';
          $cData .= '<td align="left">'.$mData[$i]['regusrno'].'</td>';
          $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.$mData[$i]['regfcrex'].'</td>';
          $cData .= '<td align="center">'.$mData[$i]['reghcrex'].'</td>';
        $cData .= '</tr>';
        fwrite($fOp, $cData);
      }
      fclose($fOp);

      if (file_exists($cFile)) {	
        // Obtener la ruta absoluta del archivo
        $cAbsolutePath = realpath($cFile);
        $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

        if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
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
        }
      } else {
        $nSwitch = 1;
        if ($_SERVER["SERVER_PORT"] != "") {
          f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
        } else {
          $cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
        }
      }
    }//if ($nSwitch == 0) {
  }//if ($cEjePro == 0) {

  if ($_SERVER["SERVER_PORT"] == "") {
    /**
     * Se ejecuto por el proceso en background
     * Actualizo el campo de resultado y nombre del archivo
     */
    $vParBg['pbarespr'] = ($nSwitch == 0) ? "EXITOSO" : "FALLIDO";  //Resultado Proceso
    $vParBg['pbaexcxx'] = ((count($mData) > 0) ? $cNomFile : "");  //Nombre Archivos Excel
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
  } // fin del if ($_SERVER["SERVER_PORT"] == "") 
?>
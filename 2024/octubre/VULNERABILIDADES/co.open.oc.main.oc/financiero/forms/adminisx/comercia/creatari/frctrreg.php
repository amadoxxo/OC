<?php
  /**
   * Genera archivo Excel.
	 * --- Descripcion: Permite generar el Excel del Reporte de Tarifas siempre por porceso en Background
   * @package opencomex
	 * @version 001
   */

  // ini_set('error_reporting', E_ERROR);
  // ini_set("display_errors", "1");

  ini_set("memory_limit","512M");
	set_time_limit(0);

  /**
   * Variables de control de errores.
   * 
   * @var int
   */
  $nSwitch = 0;

  /**
   * Variable para almacenar los mensajes de error.
   * 
   * @var string
   */
  $cMsj = "\n";

  /**
   * Variables para reemplazar caracteres especiales.
   * 
   * @var array
   */
  $cBuscar = array('"', "'", chr(13), chr(10), chr(27), chr(9));
  $cReempl = array('\"', "\'", " ", " ", " ", " ");

  /**
   * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales.
   * 
   * @var int
   */
  $cEjePro = 0;

  /**
   * Nombre(s) de los archivos en excel generados.
   * 
   * @var string
   */
  $cNomArc = "";

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

      // Librerias
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
    // Librerias
    include("../../../../config/config.php");
    include("../../../../libs/php/utility.php");
    include("../../../../../libs/php/utiprobg.php");
  }

  /**
   *  Cookie fija
   */
  $kDf = explode("~",$_COOKIE["kDatosFijos"]);
  $kMysqlHost = $kDf[0];
  $kMysqlUser = $kDf[1];
  $kMysqlPass = $kDf[2];
  $kMysqlDb   = $kDf[3];
  $kUser      = $kDf[4];
  $kLicencia  = $kDf[5];
  $swidth     = $kDf[6];
  $kModId     = $_COOKIE["kModId"];
  $kProId     = $_COOKIE["kProId"];

  $cSystemPath = OC_DOCUMENTROOT;

  if ($_SERVER["SERVER_PORT"] != "") {
    /*** Ejecutar proceso en Background ***/
    $cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;

    $cNitCli = "";
    if($gApliTar == "CLIENTE" && $gCliId != ""){
      $cNitCli = $gCliId;

      // Busco el nombre del cliente
      $qCliNom  = "SELECT ";
      $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
      $qCliNom .= "FROM $cAlfa.SIAI0150 ";
      $qCliNom .= "WHERE ";
      $qCliNom .= "CLIIDXXX = \"$gCliId\" LIMIT 0,1";
      $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
      if (mysql_num_rows($xCliNom) > 0) {
        $xDDE = mysql_fetch_array($xCliNom);
      } else {
        $xDDE['clinomxx'] = "CLIENTE SIN NOMBRE";
      }
    } else {
      $cNitCli = "";
      $xDDE['clinomxx'] = "";
    }
  } // fin if ($_SERVER["SERVER_PORT"] != "")

  //Validaciones
  //Valido que se envien los parametros necesarios
  if (!($gCliId != "" || $gDesde != "" || $gDesde != "0000-00-00" || $gHasta != "" || $gHasta != "0000-00-00")) {
    $nSwitch = 1;
    $cMsj .= "Debe seleccionar Cliente/Grupo o un Rango de Fechas.\n";
  }

  if ($gDesde != "" && $gDesde != "0000-00-00" && $gHasta != "" && $gHasta != "0000-00-00") {
    //Valido que la Fecha Hasta no sea menor a la Fecha Desde
    if($gDesde != "0000-00-00" && $gHasta != "0000-00-00"){
      if($gHasta < $gDesde){
        $nSwitch = 1;
        $cMsj .= "La Fecha Hasta no puede ser menor a la Fecha Desde.\n";
      }
    }
  }
  //Fin Validaciones

  if ($_SERVER["SERVER_PORT"] == "") {
    $gTipoOpe = $_POST['gTipoOpe'];
    $gEstTari = $_POST['gEstTari'];
    $gApliTar = $_POST['gApliTar'];
    $gCliId   = $_POST['gCliId'];
    $gEstCli  = $_POST['gEstCli'];
    $gTipoFec = $_POST['gTipoFec'];
    $gDesde   = $_POST['gDesde'];
    $gHasta   = $_POST['gHasta'];
  }  // fin del if ($_SERVER["SERVER_PORT"] == "")

  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
    $cEjePro = 1;
  
    $strPost = "gTipoOpe~"  . $gTipoOpe . 
               "|gEstTari~" . $gEstTari . 
               "|gApliTar~" . $gApliTar . 
               "|gCliId~"   . $gCliId .
               "|gEstCli~"  . $gEstCli . 
               "|gTipoFec~" . $gTipoFec . 
               "|gDesde~" . $gDesde . 
               "|gHasta~" . $gHasta;

    $vParBg['pbadbxxx'] = $cAlfa;                       // Base de Datos
    $vParBg['pbamodxx'] = "FACTURACION";                // Modulo
    $vParBg['pbatinxx'] = "REPORTETARIFAS";             // Tipo Interface
    $vParBg['pbatinde'] = "REPORTE TARIFAS";            // Descripcion Tipo de Interfaz
    $vParBg['admidxxx'] = "";                           // Sucursal
    $vParBg['doiidxxx'] = "";                           // Do
    $vParBg['doisfidx'] = "";                           // Sufijo
    $vParBg['cliidxxx'] = $cNitCli;                     // Nit
    $vParBg['clinomxx'] = $xDDE['clinomxx'];            // Nombre Importador
    $vParBg['pbapostx'] = $strPost;                     // Parametros para reconstruir Post
    $vParBg['pbatabxx'] = "";                           // Tablas Temporales
    $vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];  // Script
    $vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];      // cookie
    $vParBg['pbacrexx'] = 0;                            // Cantidad Registros
    $vParBg['pbatxixx'] = 1;                            // Tiempo Ejecucion x Item en Segundos
    $vParBg['pbaopcxx'] = "";                           // Opciones
    $vParBg['regusrxx'] = $kUser;                       // Usuario que Creo Registro
  
    // Incluyendo la clase de procesos en background
    $ObjProBg = new cProcesosBackground();
    $mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);
    
    // Imprimiendo resumen de todo ok.
		if ($mReturnProBg[0] == "true") {
			f_Mensaje(__FILE__, __LINE__, "Proceso en Background Agendado con Exito."); ?>
			<script languaje = "javascript">
				parent.fmwork.fnRecargar();
			</script>
		<?php } else {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
				$cMsj .= $mReturnProBg[$nR] . "\n";
			}
		}
	} // if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0)

  //CONSULTAS
  if ($cEjePro == 0) {
    if($nSwitch == 0){
      $mData = array();

      $cNomCon = "TRIM(CONCAT($cAlfa.SIAI0150.CLINOM1X,' ',$cAlfa.SIAI0150.CLINOM2X,' ',$cAlfa.SIAI0150.CLIAPE1X,' ',$cAlfa.SIAI0150.CLIAPE2X))";
      $cCliNom = "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF($cNomCon != \"\",$cNomCon,\"CLIENTE SIN NOMBRE\"))";
      $cGruDes = "IF($cAlfa.fpar0111.gtadesxx != \"\",$cAlfa.fpar0111.gtadesxx,\"GRUPO TARIFA SIN DESCRIPCION\")";

      // Consulta las tarifas
      $qTarifas  = "SELECT ";
      $qTarifas .= "$cAlfa.fpar0131.*, ";
      $qTarifas .= "IF($cAlfa.fpar0131.tartipxx = \"CLIENTE\",$cCliNom,$cGruDes) AS clinomxx, ";
      $qTarifas .= "$cAlfa.fpar0129.seridxxx, ";
      $qTarifas .= "$cAlfa.fpar0130.fcoidxxx, ";
      $qTarifas .= "IF($cAlfa.fpar0129.serdesxx != \"\", $cAlfa.fpar0129.serdesxx, \"CONCEPTO SIN DESCRIPCION\") AS serdesxx, ";
      $qTarifas .= "IF($cAlfa.fpar0131.serdespc != \"\", $cAlfa.fpar0131.serdespc, $cAlfa.fpar0129.serdespx) AS serdespx, ";
      $qTarifas .= "IF($cAlfa.fpar0130.fcodesxx != \"\", $cAlfa.fpar0130.fcodesxx, \"CONCEPTO SIN DESCRIPCION\") AS fcodesxx,  ";
      $qTarifas .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\", $cAlfa.SIAI0003.USRNOMXX, \"USUARIO SIN NOMBRE\") AS usrnomxx, ";
      $qTarifas .= "IF($cAlfa.SIAI0150.regestxx != \"\", $cAlfa.SIAI0150.regestxx, $cAlfa.fpar0111.regestxx) AS estcligr, ";
      $qTarifas .= "$cAlfa.fpar0142.prydesxx, ";
      $qTarifas .= "$cAlfa.SIAI0003.USRIDXXX ";
      $qTarifas .= "FROM $cAlfa.fpar0131 ";
      $qTarifas .= "LEFT JOIN $cAlfa.SIAI0003 ON $cAlfa.fpar0131.regusrxx = $cAlfa.SIAI0003.USRIDXXX ";
      $qTarifas .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fpar0131.cliidxxx = $cAlfa.SIAI0150.CLIIDXXX ";
      $qTarifas .= "LEFT JOIN $cAlfa.fpar0111 ON $cAlfa.fpar0131.cliidxxx = $cAlfa.fpar0111.gtaidxxx ";
      $qTarifas .= "LEFT JOIN $cAlfa.fpar0129 ON $cAlfa.fpar0131.seridxxx = $cAlfa.fpar0129.seridxxx ";
      $qTarifas .= "LEFT JOIN $cAlfa.fpar0130 ON $cAlfa.fpar0131.fcoidxxx = $cAlfa.fpar0130.fcoidxxx ";
      $qTarifas .= "LEFT JOIN $cAlfa.fpar0142 ON $cAlfa.fpar0131.fcotpixx = $cAlfa.fpar0142.pryidxxx ";
      $qTarifas .= "WHERE ";
      
      // Estado Tarifas
      if ($gEstTari == "TODOS") {
        $qTarifas .= "$cAlfa.fpar0131.regestxx IN(\"ACTIVO\",\"INACTIVO\") AND ";
      } else if ($gEstTari != "") {
        $qTarifas .= "$cAlfa.fpar0131.regestxx = \"$gEstTari\" AND ";
      }

      // Tipo Operacion
      if ($gTipoOpe == "TODOS") {
        $qTarifas .= "$cAlfa.fpar0131.fcotopxx IN(\"IMPORTACION\",\"EXPORTACION\",\"TRANSITO\",\"OTROS\") AND ";
      } else if ($gTipoOpe != "") {
        $qTarifas .= "$cAlfa.fpar0131.fcotopxx = \"$gTipoOpe\" AND ";
      }

      // Cliente o Grupo
      if ($gCliId != "") {
        $qTarifas .= "$cAlfa.fpar0131.cliidxxx = \"$gCliId\" AND ";

        //Estado Cliente o Grupo
        if ($gApliTar == "CLIENTE") {
          if ($gEstCli == "TODOS") {
            $qTarifas .= "$cAlfa.SIAI0150.regestxx IN(\"ACTIVO\",\"INACTIVO\") AND ";
          } else {
            $qTarifas .= "$cAlfa.SIAI0150.regestxx = \"$gEstCli\" AND ";
          }
        } else{
          if ($gEstCli == "TODOS") {
            $qTarifas .= "$cAlfa.fpar0111.regestxx IN(\"ACTIVO\",\"INACTIVO\") AND ";
          } else {
            $qTarifas .= "$cAlfa.fpar0111.regestxx = \"$gEstCli\" AND ";
          }
        }
      }

      // Rango de Fechas
      $dFecha = $gTipoFec == "CREACION" ? "regfcrex" : "regfmodx";
      if ($gDesde != "" && $gHasta != "") {
        $qTarifas .= "$cAlfa.fpar0131.$dFecha BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
      }

      $qTarifas  = substr($qTarifas, 0, -4);
      $qTarifas .= "ORDER BY $cAlfa.fpar0131.regfmodx DESC,$cAlfa.fpar0131.reghmodx DESC";
      $xTarifas  = f_MySql("SELECT","",$qTarifas,$xConexion01,"");
      // echo $qTarifas . " - " . mysql_num_rows($xTarifas);
      // die();

      if (mysql_num_rows($xTarifas) > 0) {
        //PINTAR EXCEL 
        $header  .= 'REPORTE TARIFAS'."\n";
        $header  .= "\n";
        $cData    = '';
        $cNomFile = "REPORTE_TARIFAS_".$_COOKIE['kUsrId'].date("YmdHis").".xls";

        if ($_SERVER["SERVER_PORT"] != "") {
          $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
        } else {
          $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
        }
    
        if (file_exists($cFile)) {
          unlink($cFile);
        }

        $fOp = fopen($cFile, 'a');

        //Tabla para el reporte
        $cData .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px">';

        //Cabecera del Excel
        $cData .= '<tr>';
          $cData .= '<td class="name" align="left" colspan="21" style="background-color:#0B610B">';
            $cData .= '<font size="3" color=white>';
              $cData .= '<b>REPORTE TARIFAS</b>';
            $cData .= '</font>';
          $cData .= '</td>';
        $cData .= '</tr>';

        if ($gCliId != "") {
          $cTextId  = ($gApliTar == "CLIENTE") ? "NIT DEL CLIENTE"    : "ID DEL GRUPO";
          $cTextDes = ($gApliTar == "CLIENTE") ? "NOMBRE DEL CLIENTE" : "DESCRIPCION DEL GRUPO";
          $cTextEst = ($gApliTar == "CLIENTE") ? "ESTADO CLIENTE"     : "ESTADO GRUPO";
        } else {
          $cTextId  = "NIT DEL CLIENTE O ID GRUPO";
          $cTextDes = "NOMBRE DEL CLIENTE O DESCRIPCION GRUPO";
          $cTextEst = "ESTADO CLIENTE O GRUPO";
        }

        // Columnas
        $cData .= '<tr>';
          $cData .= '<td style="background-color:#0B610B" width="120px" align="left"><b><font color=white>TIPO DE TARIFA</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>'.$cTextId.'</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>'.$cTextDes.'</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="120px" align="left"><b><font color=white>'.$cTextEst.'</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="450px" align="left"><b><font color=white>ID DEL CONCEPTO DE COBRO</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>DESCRIPCION DEL CONCEPTO DE COBRO</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="100px" align="left"><b><font color=white>DESCRIPCION PERSONALIZADA DEL CONCEPTO DE COBRO</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="400px" align="left"><b><font color=white>ID FORMA DE COBRO</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>DESCRIPCION FORMA DE COBRO</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>TARIFA POR</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>ID TARIFA POR</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="80px"  align="left"><b><font color=white>DESCRIPCION TARIFA POR</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>SUCURSALES</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="400px" align="left"><b><font color=white>TIPO DE OPERACION</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>MODO DE TRANSPORTE</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>FECHA CREADO</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>HORA CREADO</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>FECHA MODIFICADO</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>HORA MODIFICADO</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="150px" align="left"><b><font color=white>ID USUARIO ULTIMA MODIFICACION</font></b></td>';
          $cData .= '<td style="background-color:#0B610B" width="80px"  align="left"><b><font color=white>NOMBRE USUARIO ULTIMA MODIFICACION</font></b></td>';
        $cData .= '</tr>';

        // Columnas
        //Muestro la Matriz con los datos.
        $cColor = "#FFFFFF";

        while($xRTA = mysql_fetch_array($xTarifas)) {
          $cData.= '<tr bgcolor = "white" height="20" style="padding-right:4px;padding-right:4px">';
            $cData.= '<td align="center" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["tartipxx"].'</td>'; //TIPO DE TARIFA
            $cData.= '<td align="center" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["cliidxxx"].'</td>'; //NIT DEL CLIENTE O ID GRUPO
            $cData.= '<td align="left"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["clinomxx"].'</td>'; //NOMBRE DEL CLIENTE O DESCRIPCION GRUPO
            $cData.= '<td align="center" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["estcligr"].'</td>'; //ESTADO CLIENTE O GRUPO
            $cData.= '<td align="center" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["seridxxx"].'</td>'; //ID DEL CONCEPTO DE COBRO
            $cData.= '<td align="left"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["serdesxx"].'</td>'; //DESCRIPCION DEL CONCEPTO DE COBRO
            $cData.= '<td align="left"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["serdespx"].'</td>'; //DESCRIPCION PERSONALIZADA DEL CONCEPTO DE COBRO
            $cData.= '<td align="center" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["fcoidxxx"].'</td>'; //ID FORMA DE COBRO
            $cData.= '<td align="left"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["fcodesxx"].'</td>'; //DESCRIPCION FORMA DE COBRO
            $cData.= '<td align="center" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["fcotptxx"].'</td>'; //TARIFA POR
            $cData.= '<td align="center" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["fcotpixx"].'</td>'; //ID TARIFA POR
            $cData.= '<td align="left"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["prydesxx"].'</td>'; //DESCRIPCION TARIFA POR
            $cData.= '<td align="left"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.str_replace("~", ",", $xRTA["sucidxxx"]).'</td>'; //SUCURSALES
            $cData.= '<td align="left"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["fcotopxx"].'</td>'; //TIPO DE OPERACION
            $cData.= '<td align="left"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.str_replace("~", ",", $xRTA["fcomtrxx"]).'</td>'; //MODO DE TRANSPORTE
            $cData.= '<td align="center" style = "vertical-align: text-top;color:'.$cColorPro.'">'.date("Y-m-d", strtotime($xRTA['regfcrex'])).'</td>'; //FECHA CREADO
            $cData.= '<td align="center" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["reghcrex"].'</td>'; //HORA CREADO
            $cData.= '<td align="center" style = "vertical-align: text-top;color:'.$cColorPro.'">'.date("Y-m-d", strtotime($xRTA['regfmodx'])).'</td>'; //FECHA MODIFICADO
            $cData.= '<td align="center" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["reghmodx"].'</td>'; //HORA MODIFICADO
            $cData.= '<td align="center" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["USRIDXXX"].'</td>'; //ID USUARIO ULTIMA MODIFICACION
            $cData.= '<td align="left"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.$xRTA["usrnomxx"].'</td>'; //NOMBRE USUARIO ULTIMA MODIFICACION
          $cData.= '</tr>';
        }
        $cData .= '</table>';

        fwrite($fOp, $cData);
        fclose($fOp);

        if (file_exists($cFile)) {

          if ($cData == "") {
            $cData = "\n(0) REGISTROS!\n";
          }
  
          // Obtener la ruta absoluta del archivo
          $cAbsolutePath = realpath($cFile);
          $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

          if ($cData == "") {
            $cData = "\n(0) REGISTROS!\n";
          }
          
          if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
            chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
            $cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFile);
    
            if ($_SERVER["SERVER_PORT"] != "") {
    
              header('Content-Description: File Transfer');
              header('Content-Type: application/octet-stream');
              header('Content-Disposition: attachment; filename=' . $cDownLoadFilename);
              header('Content-Transfer-Encoding: binary');
              header('Expires: 0');
              header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
              header('Pragma: public');
              header('Content-Length: ' . filesize($cFile));
              
              ob_clean();
              flush();
              readfile($cFile);
            }else{
              $cNomArc = $cNomFile;
            }
          }
        }else {
          $nSwitch = 1;
          if ($_SERVER["SERVER_PORT"] != "") {
            f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
          } else {
            $cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
          }
        }
      }else{
        if ($_SERVER["SERVER_PORT"] != "") {
          f_Mensaje(__FILE__,__LINE__,"No se encontraron registros.");
        } else {
          $cMsj .= "No se encontraron registros.";
        }
      }
    }// if nSwitch
  } // if cEjePro 

  if ($nSwitch == 1) {
    f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.\n");
  }

  if ($_SERVER["SERVER_PORT"] == "") {
		/**
		 * Se ejecuto por el proceso en background
		 * Actualizo el campo de resultado y nombre del archivo
		 */
		$vParBg['pbarespr'] = ($nSwitch == 0) ? "EXITOSO" : "FALLIDO";  //Resultado Proceso
		$vParBg['pbaexcxx'] = $cNomArc;                                 //Nombre Archivos Excel
		$vParBg['pbaerrxx'] = $cMsj;                                    //Errores al ejecutar el Proceso
		$vParBg['regdfinx'] = date('Y-m-d H:i:s');                      //Fecha y Hora Fin Ejecucion Proceso
		$vParBg['pbaidxxx'] = $vArg[0];                                 //id Proceso
	
		//Incluyendo la clase de procesos en background
		$ObjProBg = new cProcesosBackground();
		$mReturnProBg = $ObjProBg->fnFinalizarProcesoBackground($vParBg);
	
		//Imprimiendo resumen de todo ok.
		if ($mReturnProBg[0] == "false") {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
				$cMsj .= $mReturnProBg[$nR] . "\n";
			}
		}
	} // fin del if ($_SERVER["SERVER_PORT"] == "")
?>

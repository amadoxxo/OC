<?php

  /**
   * Imprime Reporte de Anticipos Clientes. 
   * --- Descripcion: Permite Imprimir Reporte de Anticipos Clientes
   * @author Juan Jose Trujillo Ch. <juan.trujillo@openits.co>
   * @version 001
   */

  // ini_set('error_reporting', E_ALL);
  // ini_set("display_errors","1");

  set_time_limit(0);
  ini_set("memory_limit","512M");

  date_default_timezone_set("America/Bogota");

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
   * Cuando se ejecuta desde el cron debe armarse la cookie para incluir los utilitys.
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
    include("../../../../../config/config.php");
    include("../../../../libs/php/utility.php");
    include("../../../../../libs/php/utiprobg.php");
  }

  /**
   *  Cookie fija
   */
  $kDf = explode("~", $_COOKIE["kDatosFijos"]);
  $kUser = $kDf[4];

  $cSystemPath = OC_DOCUMENTROOT;
  

  if ($_SERVER["SERVER_PORT"] != "") {
    /// Ejecutar proceso en Background
    $cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
  } // fin if ($_SERVER["SERVER_PORT"] != "")

  if ($_SERVER["SERVER_PORT"] == "") {
    $gTerId  = $_POST['gTerId'];
    $gDesde  = $_POST['gDesde'];
    $gHasta  = $_POST['gHasta'];
    $gSucId  = $_POST['gSucId'];
    $gDocNro = $_POST['gDocNro'];
    $gDocSuf = $_POST['gDocSuf'];
    $gEstado = $_POST['gEstado'];
    $cTipo   = $_POST['cTipo'];
  }  // fin del if ($_SERVER["SERVER_PORT"] == "")

  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
    $cEjePro = 1;

    $strPost = "gTerId~" . $gTerId . "|gDesde~" . $gDesde . "|gHasta~" . $gHasta .  "|gSucId~" . $gSucId . "|gDocNro~" . $gDocNro . "|gDocSuf~" . $gDocSuf . "|cTipo~" . $cTipo . "|gEstado~" . $gEstado;

    # Numero de registros
    $qRegistros  = "SELECT SQL_CALC_FOUND_ROWS ";
    $qRegistros .= "$cAlfa.sys00121.sucidxxx, ";
    $qRegistros .= "$cAlfa.sys00121.docidxxx, ";
    $qRegistros .= "$cAlfa.sys00121.docsufxx, ";
    $qRegistros .= "$cAlfa.sys00121.cliidxxx, ";
    $qRegistros .= "$cAlfa.sys00121.regfcrex, ";
    $qRegistros .= "$cAlfa.sys00121.regestxx ";
    $qRegistros .= "FROM $cAlfa.sys00121 ";
    $qRegistros .= "WHERE ";
    if($gDocNro!=""){
    $qRegistros .= "$cAlfa.sys00121.sucidxxx = \"$gSucId\" AND ";
    $qRegistros .= "$cAlfa.sys00121.docidxxx = \"$gDocNro\" AND ";
    $qRegistros .= "$cAlfa.sys00121.docsufxx = \"$gDocSuf\" AND ";
    }
    if($gTerId!=""){
      $qRegistros .= "$cAlfa.sys00121.cliidxxx = \"$gTerId\" AND ";
    }
    $qRegistros .= "$cAlfa.sys00121.regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
    switch ($gEstado){
      case "TODOS":
        $qRegistros .= "$cAlfa.sys00121.regestxx IN (\"ACTIVO\",\"FACTURADO\") ";
      break;
      case "FACTURADO":
        $qRegistros .= "$cAlfa.sys00121.regestxx = \"FACTURADO\" ";
      break;
      case "ACTIVO":
        $qRegistros .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" ";
      break;
    }

    $xRegistros  = f_MySql("SELECT","",$qRegistros,$xConexion01,"");
    // f_Mensaje(__FILE__,__LINE__,$qRegistros."~".mysql_num_rows($xRegistros));
    mysql_free_result($xRegistros);

    $xNumRows = mysql_query("SELECT FOUND_ROWS();");
    $xRNR = mysql_fetch_array($xNumRows);
    $nRegistros =$xRNR['FOUND_ROWS()'];
    mysql_free_result($xNumRows);

    $vParBg['pbadbxxx'] = $cAlfa;                           // Base de Datos
    $vParBg['pbamodxx'] = "FACTURACION";                    // Modulo
    $vParBg['pbatinxx'] = "REPORTEANTICIPOSCLIENTES";       // Tipo Interface
    $vParBg['pbatinde'] = "REPORTE ANTICIPOS CLIENTES";     // Descripcion Tipo de Interfaz
    $vParBg['admidxxx'] = trim($gSucId);                    // Sucursal
    $vParBg['doiidxxx'] = trim($gDocNro);                   // Do
    $vParBg['doisfidx'] = trim($gDocSuf);                   // Sufijo
    $vParBg['cliidxxx'] = $gTerId;                          // Nit
    $vParBg['clinomxx'] = $xDDE['clinomxx'];                // Nombre Importador
    $vParBg['pbapostx'] = $strPost;                         // Parametros para reconstruir Post
    $vParBg['pbatabxx'] = "";                               // Tablas Temporales
    $vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];      // Script
    $vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];          // cookie
    $vParBg['pbacrexx'] = $nRegistros;                      // Cantidad Registros
    $vParBg['pbatxixx'] = 0.4;                              // Tiempo Ejecucion x Item en Segundos
    $vParBg['pbaopcxx'] = "";                               // Opciones
    $vParBg['regusrxx'] = $kUser;                           // Usuario que Creo Registro
  
    #Incluyendo la clase de procesos en background
    $ObjProBg = new cProcesosBackground();
    $mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);
  
    #Imprimiendo resumen de todo ok.
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
      f_Mensaje(__FILE__, __LINE__, $cMsj."Verifique.");
    }
  } // fin del if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0)

  if ($cEjePro == 0) {
    if ($nSwitch == 0) {

      if($gTerId != ""){
        #Busco el nombre del cliente
        $qCliNom  = "SELECT ";
        $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
        $qCliNom .= "FROM $cAlfa.SIAI0150 ";
        $qCliNom .= "WHERE ";
        $qCliNom .= "CLIIDXXX = \"{$gTerId}\" LIMIT 0,1";
        $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qCliNom." ~ ".mysql_num_rows($xCliNom));
    
        if (mysql_num_rows($xCliNom) > 0) {
          $xDDE = mysql_fetch_array($xCliNom);
        } else {
          $xDDE['clinomxx'] = "CLIENTE SIN NOMBRE";
        }
      }

      $cTitulo = "ESTADO: ";
      switch ($gEstado) {
        case "ACTIVO":
          $cTitulo .= "NO FACTURADO ";
        break;
        case "FACTURADO":
          $cTitulo .= "FACTURADO ";
        break;
        default:
          $cTitulo .= "";
        break;
      }

      // Cantidad de columnas del reporte
      $nColspan = 24;

      switch ($cTipo) {
        case 1: // PINTA POR PANTALLA //
          if ($_SERVER["SERVER_PORT"] != "") {
            ?>
            <html>
              <head>
                <title>Reporte Anticipos Clientes</title>
                <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
                <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
                <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
                <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
              
              </head>
              <body>
                <form name = 'frgrm' action='frracprn.php' method="POST">
                <table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">
                  <tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
                    <td class="name" colspan="<?php echo $nColspan; ?>" align="left">
                  
                      <font size="3"><b>
                        REPORTE ANTICIPOS CLIENTES <br>
                        <?php if($gDocNro!=""){ ?>
                          DO: <?php echo $gSucId." - ".$gDocNro." - ".$gDocSuf ?><br>
                        <?php }
                        if($gTerId!=""){ ?>
                          CLIENTE: <?php echo "[".$gTerId."] ".$xDDE['clinomxx'] ?><br>
                        <?php } 
                          echo "DESDE ".$gDesde." HASTA ".$gHasta ?><br>
                        <?php 
                        if($cTitulo != ""){
                          echo $cTitulo ?><br>
                        <?php 
                        } ?>
                      </b></font>
                    </td>
                  </tr>
                  <tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">
                    <td class="name" colspan="<?php echo $nColspan; ?>" align="left">
                      <font size="3">
                        <b>TOTAL DE REGISTROS <input type="text" name="nCanReg" style="width:80px" readonly><br>
                      </font>
                    </td>
                  </tr>
                  <tr height="20">
                    <td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Nit del Cliente</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nombre Cliente</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>DO</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Anticipo</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Documento del Anticipo</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha Documento Anticipo</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Tipo Operaci&oacute;n</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>No. Pedido</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nit Proveedor</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nombre Proveedor</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Detalle Gasto</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>No. de Factura Proveedor</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha de Causaci&oacute;n de Factura Proveedor</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>No. Causaci&oacute;n</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha de Vencimiento Proveedor</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Doc. que Cancela Factura del Proveedor</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>No. Factura de Venta de Mario Londo&ntilde;o</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha Factura de Venta Mario Londo&ntilde;o</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Documento que Cancela Factura de Venta</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nit del Facturar A</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nombre del Facturar A</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Valor del Gasto de la Factura del Proveedor</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Total Terceros</font></b></td>
                    <td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Total Anticipo</font></b></td>
                  </tr>
            <?php 
          }
        break;
        case 2: // PINTA POR EXCEL //
          $header  = 'REPORTE ANTICIPOS CLIENTES'."\n";
          $header .= "\n";
          $data = '';
          $cNomFile = "REPORTE_ANTICIPOS_CLIENTES_" . $kUser . "_" . date("YmdHis") . ".xls";

          if ($_SERVER["SERVER_PORT"] != "") {
            $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
          } else {
            $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
          }

          if (file_exists($cFile)) {
            unlink($cFile);
          }

          $fOp = fopen($cFile, 'a');

          $data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">';
            $data .= '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
              $data .= '<td class="name" colspan="'.$nColspan.'" align="left">';
            
                $data .= '<font size="3">';
                $data .= 'REPORTE ANTICIPOS CLIENTES <br>';
                if($gDocNro!=""){
                  $data .= 'DO: '.$gSucId." - ".$gDocNro." - ".$gDocSuf.'<br>';
                }
                if($gTerId!=""){
                  $data .= 'CLIENTE: '."[".$gTerId."] ".$xDDE['clinomxx'].'<br>';
                }
                $data .= 'DESDE '.$gDesde.' HASTA '.$gHasta.'<br>';
                if($cTitulo != ""){
                  $data .= '<b>'.$cTitulo.'<br>';
                }
                $data .= '</b>';
                $data .= '</font>';
              $data .= '</td>';
            $data .= '</tr>';
            $data .= '<tr height="20">';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Nit del Cliente</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nombre Cliente</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>DO</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Anticipo</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Documento del Anticipo</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha Documento Anticipo</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Tipo Operaci&oacute;n</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>No. Pedido</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nit Proveedor</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nombre Proveedor</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Detalle Gasto</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>No. de Factura Proveedor</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Fecha de Causaci&oacute;n de Factura Proveedor</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>No. Causaci&oacute;n</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Fecha de Vencimiento Proveedor</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Documento que Cancela Factura del Proveedor</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>No. Factura de Venta de Mario Londo&ntilde;o</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Fecha Factura de Venta de Mario Londo&ntilde;o</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Documento que Cancela Factura de Venta</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Nit del Facturar A</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Nombre del Facturar A</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Valor del Gasto de la Factura del Proveedor</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Total Terceros</font></b></td>';
              $data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Total Anticipo</font></b></td>';
            $data .= '</tr>';

            fwrite($fOp, $data);
        break;
      }

      // Trayendo comprobantes
      // Rango de años en los que debo buscar el reporte
      $nAnoI = ((substr($gDesde,0,4)-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (substr($gDesde,0,4)-1);
      $nAnoF = date('Y');

      $mTabDet = array(); // Array con nombre de las tablas temporales para los ajustes, anticipos y pagos a terceros en la tabla de detalle
      $mTabCue = array(); // Array con nombre de las tablas temporales para las cuentas por cobrar o por pagar de los comprobantes
      $mTabFac = array(); // Array con nombre de las tablas temporales para el encabezado de las facturas de venta

      $mCtoAnt  = array(); // Array con la marca de anticipos por cuenta-concepto
      $mCtoPCC  = array(); // Array con la marca de pcc por cuenta-concepto
      $mCuentas = array(); // Array con la marca de Cuentas por Cobrar o por Pagar
      $mConCon  = array(); // Matriz para almacenar las descripciones de los conceptos

      // Buscando las cuentas que sean por pagar o por cobrar
      $qCuentas  = "SELECT *, ";
      $qCuentas .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) as pucidxxx ";
      $qCuentas .= "FROM $cAlfa.fpar0115 ";
      $qCuentas .= "WHERE ";
      $qCuentas .= "(pucdetxx = \"P\" OR pucdetxx = \"C\") AND ";
      $qCuentas .= "regestxx = \"ACTIVO\"";
      $xCuentas  = f_MySql("SELECT","",$qCuentas,$xConexion01,"");
      $cCuentas  = "";
      while($xRC = mysql_fetch_array($xCuentas)) {
        $cCuentas .= "\"{$xRC['pucidxxx']}\",";
        $mCuentas[count($mCuentas)] = $xRC['pucidxxx'];
      }

      $cCuentas = $cCauAut.substr($cCuentas,0,strlen($cCuentas)-1);

      // Buscando conceptos de causaciones automaticas PCC
      $qCauAut  = "SELECT * ";
      $qCauAut .= "FROM $cAlfa.fpar0121 ";
      $qCauAut .= "WHERE ";
      $qCauAut .= "regestxx = \"ACTIVO\"";
      $xCauAut  = f_MySql("SELECT","",$qCauAut,$xConexion01,"");
      // echo "\n".$qCauAut."~".mysql_num_rows($xCauAut);

      $cCauAut = "";
      while($xRCA = mysql_fetch_array($xCauAut)) {
        $cCauAut .= "\"{$xRCA['pucidxxx']}~{$xRCA['ctoidxxx']}\",";
        $mCtoPCC[count($mCtoPCC)] = "{$xRCA['pucidxxx']}~{$xRCA['ctoidxxx']}";

        // Almaceno la informacion de la cuenta/concepto
        $xRCA['ctodesxp'] = $xRCA['ctodesxx'];
        $mConCon[$xRCA['pucidxxx']."~".$xRCA['ctoidxxx']] = $xRCA;
      }

      // Buscando la informacion de los comprobantes
      $qFpar117 = "SELECT ";
      $qFpar117 .= "$cAlfa.fpar0117.comidxxx, ";
      $qFpar117 .= "$cAlfa.fpar0117.comcodxx, ";
      $qFpar117 .= "$cAlfa.fpar0117.comids1x ";
      $qFpar117 .= "FROM $cAlfa.fpar0117";
      $xFpar117 = f_MySql("SELECT","",$qFpar117,$xConexion01,"");
      // echo $qFpar117."~".mysql_num_rows($xFpar117)."<br><br>";
      $mComprobantes = array();
      while($xR117 = mysql_fetch_array($xFpar117)) {
        $mComprobantes["{$xR117['comidxxx']}~{$xR117['comcodxx']}"]['comids1x'] = $xR117['comids1x'];
      }

      // Buscando conceptos PCC y Anticipos
      $qCauAnt  = "SELECT * ";
      $qCauAnt .= "FROM $cAlfa.fpar0119 ";
      $qCauAnt .= "WHERE ";
      $qCauAnt .= "(ctoantxx = \"SI\" OR ctopccxx = \"SI\") AND ";
      $qCauAnt .= "regestxx = \"ACTIVO\"";
      $xCauAnt  = f_MySql("SELECT","",$qCauAnt,$xConexion01,"");
      //f_Mensaje(__FILE__,__LINE__,$qCauAnt."~".mysql_num_rows($xCauAnt));
      $cCauAnt = "";
      while($xRCA = mysql_fetch_array($xCauAnt)) {
        $cCauAnt .= "\"{$xRCA['pucidxxx']}~{$xRCA['ctoidxxx']}\",";

        if ($xRCA['ctoantxx'] == "SI") {
          $mCtoAnt[count($mCtoAnt)] = "{$xRCA['pucidxxx']}~{$xRCA['ctoidxxx']}";
        }
        if ($xRCA['ctopccxx'] == "SI") {
          $mCtoPCC[count($mCtoPCC)] = "{$xRCA['pucidxxx']}~{$xRCA['ctoidxxx']}";
        }

        // Almaceno la informacion de la cuenta/concepto
        $mConCon[$xRCA['pucidxxx']."~".$xRCA['ctoidxxx']] = $xRCA;
      }

      // Llamando Metodo que hace la conexion
      $mReturnConexionTM = fnConectarDBReporteAnticiposClientes();
      if($mReturnConexionTM[0] == "true"){
        $xConexionTM = $mReturnConexionTM[1];
      }else{
        $nSwitch = 1;
        for($nR=1;$nR<count($mReturnConexionTM);$nR++){
          $mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
        }
      }

      $cCauAnt = $cCauAut.substr($cCauAnt,0,strlen($cCauAnt)-1);
      
      for($nPerAno=$nAnoI; $nPerAno<=$nAnoF; $nPerAno++) {
        // Creando y cargando tablas temporales de PCC y Anticipos
        $cFcod   = "fcod".$nPerAno;
        $cTabCar = mt_rand(100000, 999999);
        $cTabFac = "memffcod".$nPerAno.$cTabCar;

        $qNewTab = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcod";
        $xNewTab = mysql_query($qNewTab,$xConexionTM);

        $qMovDO  = "SELECT * ";
        $qMovDO .= "FROM $cAlfa.fcod$nPerAno ";
        $qMovDO .= "WHERE ";
        if($gDocNro != "") {
          $qMovDO .= "comcsccx = \"$gDocNro\" AND ";
          $qMovDO .= "comseqcx = \"$gDocSuf\" AND ";
        }
        $qMovDO .= "CONCAT(pucidxxx,\"~\",ctoidxxx) IN ($cCauAnt) AND "; //PCC Y ANTICPOS
        $qMovDO .= "comidxxx != \"F\"  AND ";
        $qMovDO .= "regestxx =  \"ACTIVO\" ";

        $qInsert = "INSERT INTO $cAlfa.$cTabFac $qMovDO";
        $xInsert = mysql_query($qInsert,$xConexion01);
        $mTabDet[$nPerAno] = $cTabFac;
        // Fin Creando y cargando tablas temporales de PCC y Anticipos

        // Creando y cargando tablas temporales de Cuentas por Pagar o Por Cobrar
        $cFcod   = "fcod".$nPerAno;
        $cTabCar = mt_rand(100000, 999999);
        $cTabFac = "memfcxcp".$nPerAno.$cTabCar;

        $qNewTab = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcod";
        $xNewTab = mysql_query($qNewTab,$xConexionTM);

        $qMovDO  = "SELECT * ";
        $qMovDO .= "FROM $cAlfa.fcod$nPerAno ";
        $qMovDO .= "WHERE ";
        $qMovDO .= "pucidxxx IN ($cCuentas) AND "; //Cuentas por Cobrar o por Pagar.
        $qMovDO .= "regestxx =  \"ACTIVO\" ";

        $qInsert = "INSERT INTO $cAlfa.$cTabFac $qMovDO";
        $xInsert = mysql_query($qInsert,$xConexion01);
        $mTabCue[$nPerAno] = $cTabFac;
        // Fin Creando y cargando tablas temporales de Cuentas por Pagar o Por Cobrar

        // Creando y cargando tablas temporales de encabezado de las facturas de venta
        $cFcoc   = "fcoc".$nPerAno;
        $cTabCar = mt_rand(100000, 999999);
        $cTabFac = "memfcocc".$nPerAno.$cTabCar;

        $qNewTab = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabFac LIKE $cAlfa.$cFcoc";
        $xNewTab = mysql_query($qNewTab,$xConexionTM);

        $qMovDO  = "SELECT * ";
        $qMovDO .= "FROM $cAlfa.fcoc$nPerAno ";
        $qMovDO .= "WHERE ";
        $qMovDO .= "comidxxx = \"F\" AND "; //Facturas de Venta
        $qMovDO .= "regestxx = \"ACTIVO\" ";
        // echo "<br>".$qMovDO."~".mysql_num_rows($xMovDO)."<br><br>";
        $qInsert = "INSERT INTO $cAlfa.$cTabFac $qMovDO";
        $xInsert = mysql_query($qInsert,$xConexion01);
        $mTabFac[$nPerAno] = $cTabFac;
        // Fin Creando y cargando tablas temporales de Cuentas por Pagar o Por Cobrar
      }
      // Fin Trayendo comprobantes

      $qDatDoi  = "SELECT ";
      $qDatDoi .= "$cAlfa.sys00121.sucidxxx, ";
      $qDatDoi .= "$cAlfa.sys00121.docidxxx, ";
      $qDatDoi .= "$cAlfa.sys00121.docsufxx, ";
      $qDatDoi .= "$cAlfa.sys00121.comidxxx, ";
      $qDatDoi .= "$cAlfa.sys00121.comcodxx, ";
      $qDatDoi .= "$cAlfa.sys00121.ccoidxxx, ";
      $qDatDoi .= "$cAlfa.sys00121.pucidxxx, ";
      $qDatDoi .= "$cAlfa.sys00121.succomxx, ";
      $qDatDoi .= "$cAlfa.sys00121.doctipxx, ";
      $qDatDoi .= "$cAlfa.sys00121.docpedxx, ";
      $qDatDoi .= "$cAlfa.sys00121.cliidxxx, ";
      $qDatDoi .= "$cAlfa.sys00121.diridxxx, ";
      $qDatDoi .= "$cAlfa.sys00121.docfacxx, ";
      $qDatDoi .= "$cAlfa.sys00121.docffecx, ";
      $qDatDoi .= "$cAlfa.sys00121.regfcrex, ";
      $qDatDoi .= "$cAlfa.sys00121.docusrce, ";
      $qDatDoi .= "$cAlfa.sys00121.docfecce, ";
      $qDatDoi .= "$cAlfa.sys00121.regestxx ";
      $qDatDoi .= "FROM $cAlfa.sys00121 ";
      $qDatDoi .= "WHERE ";
      if($gDocNro!=""){
        $qDatDoi .= "$cAlfa.sys00121.sucidxxx = \"$gSucId\" AND ";
        $qDatDoi .= "$cAlfa.sys00121.docidxxx = \"$gDocNro\" AND ";
        $qDatDoi .= "$cAlfa.sys00121.docsufxx = \"$gDocSuf\" AND ";
      }
      if($gTerId!=""){
        $qDatDoi .= "$cAlfa.sys00121.cliidxxx = \"$gTerId\" AND ";
      }
      $qDatDoi .= "$cAlfa.sys00121.regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
    
      switch ($gEstado){
        case "TODOS":
          $qDatDoi .= "$cAlfa.sys00121.regestxx IN (\"ACTIVO\",\"FACTURADO\") ";
        break;
        case "FACTURADO":
          $qDatDoi .= "$cAlfa.sys00121.regestxx = \"FACTURADO\" ";
        break;
        case "ACTIVO":
          $qDatDoi .= "$cAlfa.sys00121.regestxx = \"ACTIVO\" ";
        break;
      }

      $qDatDoi .= "ORDER BY $cAlfa.sys00121.regfcrex";
      $xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
      // f_Mensaje(__FILE__,__LINE__,$qDatDoi."~".mysql_num_rows($xDatDoi));
      // echo "\n".$qDatDoi."~".mysql_num_rows($xDatDoi);

      // Inicializa las variables
      $mTerceros = array();
      $mDatos    = array();
      // Recorro los Do's
      while ($xRDD = mysql_fetch_array($xDatDoi)) {

        // Busco el nombre del cliente dueño del DO
        if(!isset($mTerceros[$xRDD['cliidxxx']])){
          $qCliNom  = "SELECT ";
          $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,' ',CLINOM2X,' ',CLIAPE1X,' ',CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,' ',CLINOM2X,' ',CLIAPE1X,' ',CLIAPE2X)), CLINOMXX) AS clinomxx ";
          $qCliNom .= "FROM $cAlfa.SIAI0150 ";
          $qCliNom .= "WHERE ";
          $qCliNom .= "CLIIDXXX = \"{$xRDD['cliidxxx']}\" LIMIT 0,1";
          $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
          // echo "<br>".$qCliNom."~".mysql_num_rows($xCliNom);
          if (mysql_num_rows($xCliNom) > 0) {
            $vCliNom = mysql_fetch_array($xCliNom);
            $mTerceros[$xRDD['cliidxxx']] = $vCliNom['clinomxx'];
          } else {
            $mTerceros[$xRDD['cliidxxx']] = "CLIENTE SIN NOMBRE";
          }
        }
        $xRDD['clinomxx'] = $mTerceros[$xRDD['cliidxxx']];
        // Fin Busco el nombre del cliente dueño del DO

        // No. documento de pedido del DO
        $cPedido = "";
        switch ($xRDD['doctipxx']){
          case "TRANSITO":
          case "IMPORTACION":
            // Traigo Datos de la SIAI0200 DATOS DEL DO
            $qDoiDat  = "SELECT DOIPEDXX ";
            $qDoiDat .= "FROM $cAlfa.SIAI0200 ";
            $qDoiDat .= "WHERE ";
            $qDoiDat .= "DOIIDXXX = \"{$xRDD['docidxxx']}\" AND ";
            $qDoiDat .= "DOISFIDX = \"{$xRDD['docsufxx']}\" AND ";
            $qDoiDat .= "ADMIDXXX = \"{$xRDD['sucidxxx']}\" ";
            $xDoiDat  = f_MySql("SELECT","",$qDoiDat,$xConexion01,"");
            $nFilDoi  = mysql_num_rows($xDoiDat);
            if ($nFilDoi > 0) {
              $vDoiDat  = mysql_fetch_array($xDoiDat);
            }
            // Cargo Variables de Pedido
            $cPedido = $vDoiDat['DOIPEDXX'];
          break;
          case "EXPORTACION":
            // Consulto Datos de Do en Exportaciones tabla siae0199
            $qDexDat  = "SELECT dexpedxx ";
            $qDexDat .= "FROM $cAlfa.siae0199 ";
            $qDexDat .= "WHERE ";
            $qDexDat .= "$cAlfa.siae0199.dexidxxx = \"{$xRDD['docidxxx']}\" AND ";
            $qDexDat .= "$cAlfa.siae0199.admidxxx = \"{$xRDD['sucidxxx']}\" ";
            $xDexDat  = f_MySql("SELECT","",$qDexDat,$xConexion01,"");
            //f_Mensaje(__FILE__,__LINE__,$qDexDat);
            $nFilDex  = mysql_num_rows($xDexDat);
            if ($nFilDex > 0) {
              $vDexDat = mysql_fetch_array($xDexDat);
            }
            // Fin Consulto Datos de Do en Exportaciones tabla siae0199
            // Cargo Variable Pedido
            $cPedido = $vDexDat['dexpedxx'];
          break;
          case "OTROS":
          break;
        }//switch (){

        $nExisteFactura = 0;

        $nAno01I = ((substr($xRDD['regfcrex'],0,4)-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (substr($xRDD['regfcrex'],0,4)-1);
        $nAno01F = date('Y');

        $mPCC       = array();
        $mAnticipos = array();
        $mNotasPCC  = array();
        for($nAnio = $nAno01I; $nAnio <= $nAno01F; $nAnio++){
          // Consulta los comprobantes
          $cTabFacDet = $mTabDet[$nAnio];
          $qDataMov  = "SELECT ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comidxxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comcodxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comcscxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comcsc2x, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comseqxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comcsc3x, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comfecxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comfecve, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.ccoidxxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.pucidxxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comfacxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.ctoidxxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.teridxxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.terid2xx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.sucidxxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.docidxxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.docsufxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.commovxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comctocx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comobsxx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comcsccx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comseqcx, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comidc2x, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comcodc2, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comcscc2, ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comseqc2, ";
          $qDataMov .= "IF($cAlfa.$cTabFacDet.commovxx = \"C\",($cAlfa.$cTabFacDet.comvlrxx * -1), $cAlfa.$cTabFacDet.comvlrxx) AS comvlrxx ";
          $qDataMov .= "FROM $cAlfa.$cTabFacDet ";
          $qDataMov .= "WHERE ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comcsccx = \"{$xRDD['docidxxx']}\" AND ";
          $qDataMov .= "$cAlfa.$cTabFacDet.comseqcx = \"{$xRDD['docsufxx']}\" AND ";
          $qDataMov .= "$cAlfa.$cTabFacDet.regestxx = \"ACTIVO\" ";
          $xDataMov  = mysql_query($qDataMov,$xConexion01);
          // echo "<br>fcod".$nAnio."~".$qDataMov."~".mysql_num_rows($xDataMov)."<br><br>";

          if (mysql_num_rows($xDataMov) > 0) {
            while ($xRDM = mysql_fetch_array($xDataMov)) {
              // Discrimina los Anticipos por DO
              if ($xRDM['comidxxx'] == "R" && in_array("{$xRDM['pucidxxx']}~{$xRDM['ctoidxxx']}",$mCtoAnt)) {
                $nSw_Incluir = 0;
                if ($xRDM['sucidxxx'] != "" && $xRDM['docidxxx'] != "" && $xRDM['docsufxx'] != "") {
                  // Si tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
                  if ($xRDM['sucidxxx'] == $xRDD['sucidxxx'] && $xRDM['docidxxx'] == $xRDD['docidxxx'] && $xRDM['docsufxx'] == $xRDD['docsufxx']) {
                    $nSw_Incluir = 1;
                  }
                } else {
                  // Comparando por el centro de costo
                  if ($xRDM['ccoidxxx'] == $xRDD['ccoidxxx']) {
                    $nSw_Incluir = 1;
                  }
                }

                if ($nSw_Incluir == 1) {
                  $nInd_mAnticipos = count($mAnticipos);
                  $mAnticipos[$nInd_mAnticipos]['sucidxxx'] = $xRDD['sucidxxx'];
                  $mAnticipos[$nInd_mAnticipos]['docidxxx'] = $xRDD['docidxxx'];
                  $mAnticipos[$nInd_mAnticipos]['docsufxx'] = $xRDD['docsufxx'];
                  $mAnticipos[$nInd_mAnticipos]['comidxxx'] = $xRDM['comidxxx'];
                  $mAnticipos[$nInd_mAnticipos]['comcodxx'] = $xRDM['comcodxx'];
                  $mAnticipos[$nInd_mAnticipos]['comcscxx'] = $xRDM['comcscxx'];
                  $mAnticipos[$nInd_mAnticipos]['comcsc2x'] = $xRDM['comcsc2x'];
                  $mAnticipos[$nInd_mAnticipos]['vlrantix'] = ($xRDM['comvlrxx']*-1);
                  $mAnticipos[$nInd_mAnticipos]['docantix'] = $xRDM['comcscxx'];
                  $mAnticipos[$nInd_mAnticipos]['fechanti'] = $xRDM['comfecxx'];
                }
              }
              // Fin Discrimina los Anticipos por DO

              // Buscando los PCC por DO
              if (($xRDM['comidxxx'] == "P" || $xRDM['comidxxx'] == "L") && in_array("{$xRDM['pucidxxx']}~{$xRDM['ctoidxxx']}",$mCtoPCC)) {
                $nSw_Incluir = 0;
                if ($xRDM['sucidxxx'] != "" && $xRDM['docidxxx'] != "" && $xRDM['docsufxx'] != "") {
                  //si tiene los campos de sucursal, do y sufijo digitados se hace la comparacion con estos campos
                  if ($xRDM['sucidxxx'] == $xRDD['sucidxxx'] && $xRDM['docidxxx'] == $xRDD['docidxxx'] && $xRDM['docsufxx'] == $xRDD['docsufxx']) {
                    $nSw_Incluir = 1;
                  }
                } else {
                  //Comparando por el centro de costo
                  if ($xRDM['ccoidxxx'] == $xRDD['ccoidxxx']) {
                    $nSw_Incluir = 1;
                  }
                }

							  if ($nSw_Incluir == 1) {
                  // Busco el nombre del proveedor
                  if(!isset($mTerceros[$xRDM['terid2xx']])){
                    $qProNom  = "SELECT ";
                    $qProNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
                    $qProNom .= "FROM $cAlfa.SIAI0150 ";
                    $qProNom .= "WHERE ";
                    $qProNom .= "CLIIDXXX = \"{$xRDM['terid2xx']}\" LIMIT 0,1";
                    $xProNom = f_MySql("SELECT","",$qProNom,$xConexion01,"");
                    // echo "<br>".$qProNom."~".mysql_num_rows($xProNom);
                    if (mysql_num_rows($xProNom) > 0) {
                      $vProNom = mysql_fetch_array($xProNom);
                      $mTerceros[$xRDM['terid2xx']] = $vProNom['clinomxx'];
                    } else {
                      $mTerceros[$xRDM['terid2xx']] = "PROVEEDOR SIN NOMBRE";
                    }
                  }

                  $cComId    = "";
                  $cComCod   = "";
                  $cComCsc   = "";
                  $cComCsc2  = "";
                  $cFecFact  = "";
                  $cFactCanc = "";
                  $cNitFact  = "";
                  $cNomFact  = "";
                  // Valida si la causacion esta asociada en alguna factura
                  for($cAnoP = $nAno01F; $cAnoP >= $nAno01I; $cAnoP--){
                    $cTabFac = $mTabFac[$cAnoP];
                    $qComFec  = "SELECT ";
                    $qComFec .= "comidxxx, ";
                    $qComFec .= "comcodxx, ";
                    $qComFec .= "comcscxx, ";
                    $qComFec .= "comcsc2x, ";
                    $qComFec .= "comfecxx, ";
                    $qComFec .= "regfcrex, ";
                    $qComFec .= "terid2xx ";
                    $qComFec .= "FROM $cAlfa.fcod$cAnoP ";
                    $qComFec .= "WHERE ";
                    $qComFec .= "comidxxx = \"F\" AND ";
                    $qComFec .= "comidc2x = \"{$xRDM['comidxxx']}\" AND ";
                    $qComFec .= "comcodc2 = \"{$xRDM['comcodxx']}\" AND ";
                    $qComFec .= "comcscc2 = \"{$xRDM['comcscxx']}\" AND "; 
                    $qComFec .= "teridxxx = \"{$xRDM['teridxxx']}\" AND ";
                    $qComFec .= "regestxx = \"ACTIVO\" LIMIT 0,1 "; 
                    $xComFec = mysql_query($qComFec, $xConexion01);
                    // echo "<br>".$qComFec."~".mysql_num_rows($xComFec);
                    if(mysql_num_rows($xComFec) > 0){
                      $vComFec  = mysql_fetch_array($xComFec);
                      $cComId   = $vComFec['comidxxx'];
                      $cComCod  = $vComFec['comcodxx'];
                      $cComCsc  = $vComFec['comcscxx'];
                      $cComCsc2 = $vComFec['comcsc2x'];
                      $cFecFact = $vComFec['comfecxx'];

                      // Busco el nombre del facturar a
                      if(!isset($mTerceros[$vComFec['terid2xx']])){
                        $qCliNom  = "SELECT ";
                        $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
                        $qCliNom .= "FROM $cAlfa.SIAI0150 ";
                        $qCliNom .= "WHERE ";
                        $qCliNom .= "CLIIDXXX = \"{$vComFec['terid2xx']}\" LIMIT 0,1";
                        $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
                        // echo "<br>".$qCliNom."~".mysql_num_rows($xCliNom);
                        if (mysql_num_rows($xCliNom) > 0) {
                          $vCliNom = mysql_fetch_array($xCliNom);
                          $mTerceros[$vComFec['terid2xx']] = $vCliNom['clinomxx'];
                        } else {
                          $mTerceros[$vComFec['terid2xx']] = "CLIENTE SIN NOMBRE";
                        }
                      }
                      $cNitFact = $vComFec['terid2xx'];
                      $cNomFact = $mTerceros[$vComFec['terid2xx']];

                      // Busco si la facutra ya fue cruzada con un ajuste, un recibo de caja o una nota credito
                      for($cAnoPAux = $nAno01F; $cAnoPAux >= $cAnoP; $cAnoPAux--){
                        $cTabFac  = $mTabCue[$cAnoPAux];
                        $qDocFac  = "SELECT ";
                        $qDocFac .= "comidxxx, ";
                        $qDocFac .= "comcodxx, ";
                        $qDocFac .= "comcscxx, ";
                        $qDocFac .= "comcsc2x ";
                        $qDocFac .= "FROM $cAlfa.$cTabFac ";
                        $qDocFac .= "WHERE ";
                        $qDocFac .= "comidxxx NOT IN (\"F\",\"C\") AND ";
                        $qDocFac .= "comidcxx = \"{$vComFec['comidxxx']}\" AND ";
                        $qDocFac .= "comcodcx = \"{$vComFec['comcodxx']}\" AND ";
                        $qDocFac .= "comcsccx = \"{$vComFec['comcsc2x']}\" AND "; 
                        $qDocFac .= "terid2xx = \"{$vComFec['terid2xx']}\" LIMIT 0,1 "; 
                        $xDocFac = mysql_query($qDocFac, $xConexion01);
                        // echo "<br>".$qDocFac."~".mysql_num_rows($xDocFac);
                        if(mysql_num_rows($xDocFac) > 0){
                          while($xRDF = mysql_fetch_array($xDocFac)){
                            if($xRDF['comidxxx'] == "R" || $xRDF['comidxxx'] == "L"){
                              $cFactCanc = $xRDF['comidxxx']."-".$xRDF['comcodxx']."-".$xRDF['comcscxx']."-".$xRDF['comcsc2x']; # Doc. que cancela Factura
                            }
                          }
                          break;
                        }
                      }
                      break;
                    }
                  }

                  $cCscCruce = "";
                  // Busco datos del comprobante que cancela el pago a proveedor
                  for($cAnoP = $nAno01F; $cAnoP >= $nAno01I; $cAnoP--){
                    $cTabFac = $mTabCue[$cAnoP];
                    $qCauAut = "SELECT ";
                    $qCauAut .= "comidxxx, ";
                    $qCauAut .= "comcodxx, ";
                    $qCauAut .= "comcscxx, ";
                    $qCauAut .= "comcsc2x, ";
                    $qCauAut .= "comfecxx, ";
                    $qCauAut .= "comfecve, ";
                    $qCauAut .= "regfcrex ";
                    $qCauAut .= "FROM $cAlfa.$cTabFac ";
                    $qCauAut .= "WHERE ";
                    $qCauAut .= "comidcxx = \"{$xRDM['comidxxx']}\" AND ";
                    $qCauAut .= "comcodcx = \"{$xRDM['comcodxx']}\" AND ";
                    $qCauAut .= "comcsccx = \"{$xRDM['comcscxx']}\" AND "; 
                    $qCauAut .= "terid2xx = \"{$xRDM['terid2xx']}\" LIMIT 0,1 "; 
                    $xCauAut = mysql_query($qCauAut, $xConexion01);
                    // echo "<br>".$qCauAut."~".mysql_num_rows($xCauAut);
                    if(mysql_num_rows($xCauAut) > 0){
                      $vCauAut   = mysql_fetch_array($xCauAut);
                      $cCscCruce = $vCauAut['comidxxx']."-".$vCauAut['comcodxx']."-".$vCauAut['comcscxx']."-".$vCauAut['comcsc2x']; // No. doc. cruce
                      break;
                    }
                  }

                  // Si la variable $cComId es vacia almacena los datos del DO para agrupar por DO y no por Factura
                  if ($cComId == "") {
                    $cComId   = $xRDD['sucidxxx'];
                    $cComCod  = $xRDD['docidxxx'];
                    $cComCsc  = $xRDD['docsufxx'];
                  }

                  // Descripcion del concepto
                  if ($mConCon[$xRDM['pucidxxx']."~".$xRDM['ctoidxxx']]['ctodesx'.strtolower($xRDM['comidxxx'])] != "") {
                    $cCtoDes = $mConCon[$xRDM['pucidxxx']."~".$xRDM['ctoidxxx']]['ctodesx'.strtolower($xRDM['comidxxx'])];
                  } else {
                    $cCtoDes = $mConCon[$xRDM['pucidxxx']."~".$xRDM['ctoidxxx']]['ctodesxx'];
                  }

                  $cTieneFac = "NO";
                  // Si el comprobante en curso es una LP, se valida si el DO tiene una factura asociada
                  if ($mComprobantes["{$xRDM['comidxxx']}~{$xRDM['comcodxx']}"]['comids1x'] == "LP") {
                    for($cAnoP = $nAno01F; $cAnoP >= $nAno01I; $cAnoP--){
                      $cTabFac = $mTabFac[$cAnoP];
                      $qDocFac  = "SELECT ";
                      $qDocFac .= "comidxxx, ";
                      $qDocFac .= "comcodxx, ";
                      $qDocFac .= "comcscxx, ";
                      $qDocFac .= "comcsc2x, ";
                      $qDocFac .= "comfpxxx ";
                      $qDocFac .= "FROM $cAlfa.$cTabFac ";
                      $qDocFac .= "WHERE ";
                      $qDocFac .= "$cAlfa.$cTabFac.comidxxx = \"F\" AND ";
                      $qDocFac .= "$cAlfa.$cTabFac.comfpxxx LIKE \"%{$xRDD['docidxxx']}~{$xRDD['docsufxx']}%\" AND ";
                      $qDocFac .= "$cAlfa.$cTabFac.regestxx = \"ACTIVO\" ";
                      $qDocFac .= "ORDER BY $cAlfa.$cTabFac.comfecxx DESC, $cAlfa.$cTabFac.reghcrex ASC ";
                      $xDocFac = mysql_query($qDocFac, $xConexion01);
                      // echo $qDocFac . " - " . mysql_num_rows($xDocFac);
                      if (mysql_num_rows($xDocFac) > 0) {
                        while ($xRDF = mysql_fetch_array($xDocFac)) {
                          $mFacDo = f_explode_array($xRDF['comfpxxx'],"|","~");
                          for($x=0;$x<count($mFacDo);$x++){
                            if($mFacDo[$x][15] == $xRDD['sucidxxx'] && $mFacDo[$x][2] == $xRDD['docidxxx'] && $mFacDo[$x][3] == $xRDD['docsufxx']){
                              $cTieneFac = "SI";
                              break;
                            }
                          }
                        }
                        break;
                      }
                    }
                  }

                  $nInd_mPCC = count($mPCC);
                  $mPCC[$nInd_mPCC]['sucidxxx'] = $xRDD['sucidxxx'];
                  $mPCC[$nInd_mPCC]['docidxxx'] = $xRDD['docidxxx'];
                  $mPCC[$nInd_mPCC]['docsufxx'] = $xRDD['docsufxx'];
                  $mPCC[$nInd_mPCC]['comidxxx'] = $cComId;
                  $mPCC[$nInd_mPCC]['comcodxx'] = $cComCod;
                  $mPCC[$nInd_mPCC]['comcscxx'] = $cComCsc;
                  $mPCC[$nInd_mPCC]['comcsc2x'] = $cComCsc2;
                  $mPCC[$nInd_mPCC]['tipocomp'] = $mComprobantes["{$xRDM['comidxxx']}~{$xRDM['comcodxx']}"]['comids1x'];
                  $mPCC[$nInd_mPCC]['pronitxx'] = $xRDM['terid2xx'];                 // Nit Proveedor
                  $mPCC[$nInd_mPCC]['pronomxx'] = $mTerceros[$xRDM['terid2xx']];     // Nombre Proveedor
                  $mPCC[$nInd_mPCC]['ctodesxx'] = $cCtoDes;                          // Detalle gasto
                  $mPCC[$nInd_mPCC]['causcscx'] = $xRDM['comcscxx'];                 // No. de Factura Proveedor
                  $mPCC[$nInd_mPCC]['causfecx'] = $xRDM['comfecxx'];                 // Fecha de Causación de Factura Proveedor
                  $mPCC[$nInd_mPCC]['causcsc2'] = $xRDM['comcsc2x'];                 // No. Causación - Consecutivo 2
                  $mPCC[$nInd_mPCC]['caufecve'] = $xRDM['comfecve'];                 // Fecha Vencimiento de Pago al Proveedor
                  $mPCC[$nInd_mPCC]['csccruce'] = $cCscCruce;                        // Documento que Cancela Factura del Proveedor
                  $mPCC[$nInd_mPCC]['cscfactx'] = ($cComId == "F") ? $cComCsc : "";  // No. Factura de Venta de Mario Londoño
                  $mPCC[$nInd_mPCC]['fecfactx'] = ($cComId == "F") ? $cFecFact : ""; // Fecha Factura de Venta de Mario Londoño
                  $mPCC[$nInd_mPCC]['factcanc'] = $cFactCanc;                        // Documento que Cancela Factura de Venta
                  $mPCC[$nInd_mPCC]['nitfactx'] = $cNitFact;                         // Nit del Facturar A
                  $mPCC[$nInd_mPCC]['nomfactx'] = $cNomFact;                         // Nombre del Facturar A
                  $mPCC[$nInd_mPCC]['comvlrxx'] = $xRDM['comvlrxx'];                 // Valor del Gasto de la Factura del Proveedor
                  $mPCC[$nInd_mPCC]['tienefac'] = $cTieneFac;                        // Indica si el DO tiene una factura asociada
                }
              }

              // Obtiene los PCC de las notas credito
              if ($xRDM['comidxxx'] == "C" && in_array("{$xRDM['pucidxxx']}~{$xRDM['ctoidxxx']}",$mCtoPCC)) {
                $vComObs    = explode("^", $xRDM['comobsxx']);
                $cNitProv   = trim($vComObs[2]);
                $cComCsc    = "";
                $cComCsc2   = "";
                $cComFec    = "";
                $cComFecVen = "";

                // Consulta datos de pago a proveedor
                for($cAnoP = $nAno01F; $cAnoP >= $nAno01I; $cAnoP--){
                  $cTabFacDet = $mTabDet[$cAnoP];

                  $qComPCC  = "SELECT ";
                  $qComPCC .= "$cAlfa.$cTabFacDet.comidxxx, ";
                  $qComPCC .= "$cAlfa.$cTabFacDet.comcodxx, ";
                  $qComPCC .= "$cAlfa.$cTabFacDet.comcscxx, ";
                  $qComPCC .= "$cAlfa.$cTabFacDet.comcsc2x, ";
                  $qComPCC .= "$cAlfa.$cTabFacDet.comfecxx, ";
                  $qComPCC .= "$cAlfa.$cTabFacDet.comfecve ";
                  $qComPCC .= "FROM $cAlfa.$cTabFacDet ";
                  $qComPCC .= "WHERE ";
                  $qComPCC .= "$cAlfa.$cTabFacDet.comidxxx = \"{$xRDM['comidc2x']}\" AND ";
                  $qComPCC .= "$cAlfa.$cTabFacDet.comcodxx = \"{$xRDM['comcodc2']}\" AND ";
                  $qComPCC .= "$cAlfa.$cTabFacDet.comcscxx = \"{$xRDM['comcscc2']}\" AND ";
                  $qComPCC .= "$cAlfa.$cTabFacDet.comseqxx = \"{$xRDM['comseqc2']}\" AND ";
                  $qComPCC .= "$cAlfa.$cTabFacDet.terid2xx = \"$cNitProv\" LIMIT 0,1";
                  $xComPCC  = mysql_query($qComPCC, $xConexion01);
                  // echo $qComPCC . " - " . mysql_num_rows($xComPCC) . "<br><br>";
                  if (mysql_num_rows($xComPCC) > 0) {
                    $vComPCC    = mysql_fetch_array($xComPCC);
                    $cComCsc    = $vComPCC['comcscxx'];
                    $cComCsc2   = $vComPCC['comcsc2x'];
                    $cComFec    = $vComPCC['comfecxx'];
                    $cComFecVen = $vComPCC['comfecve'];
                  }
                }

                // Busco el nombre del facturar a
                if(!isset($mTerceros[$xRDM['teridxxx']])){
                  $qCliNom  = "SELECT ";
                  $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
                  $qCliNom .= "FROM $cAlfa.SIAI0150 ";
                  $qCliNom .= "WHERE ";
                  $qCliNom .= "CLIIDXXX = \"{$xRDM['teridxxx']}\" LIMIT 0,1";
                  $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
                  // echo "<br>".$qCliNom."~".mysql_num_rows($xCliNom);
                  if (mysql_num_rows($xCliNom) > 0) {
                    $vCliNom = mysql_fetch_array($xCliNom);
                    $mTerceros[$xRDM['teridxxx']] = $vCliNom['clinomxx'];
                  } else {
                    $mTerceros[$xRDM['teridxxx']] = "CLIENTE SIN NOMBRE";
                  }
                }

                $nInd_mNotasPCC = count($mNotasPCC);
                $mNotasPCC[$nInd_mNotasPCC]['sucidxxx'] = $xRDD['sucidxxx'];
                $mNotasPCC[$nInd_mNotasPCC]['docidxxx'] = $xRDD['docidxxx'];
                $mNotasPCC[$nInd_mNotasPCC]['docsufxx'] = $xRDD['docsufxx'];
                $mNotasPCC[$nInd_mNotasPCC]['comidxxx'] = $xRDM['comidxxx'];
                $mNotasPCC[$nInd_mNotasPCC]['comcodxx'] = $xRDM['comcodxx'];
                $mNotasPCC[$nInd_mNotasPCC]['comcscxx'] = $xRDM['comcscxx'];
                $mNotasPCC[$nInd_mNotasPCC]['comcsc2x'] = $xRDM['comcsc2x'];
                $mNotasPCC[$nInd_mNotasPCC]['tipocomp'] = $mComprobantes["{$xRDM['comidxxx']}~{$xRDM['comcodxx']}"]['comids1x'];
                $mNotasPCC[$nInd_mNotasPCC]['pronitxx'] = $cNitProv;         // Nit Proveedor
                $mNotasPCC[$nInd_mNotasPCC]['pronomxx'] = trim($vComObs[1]); // Nombre Proveedor
                $mNotasPCC[$nInd_mNotasPCC]['ctodesxx'] = trim($vComObs[0]); // Detalle gasto
                $mNotasPCC[$nInd_mNotasPCC]['causcscx'] = $cComCsc;          // No. de Factura Proveedor
                $mNotasPCC[$nInd_mNotasPCC]['causfecx'] = $cComFec;          // Fecha de Causación de Factura Proveedor
                $mNotasPCC[$nInd_mNotasPCC]['causcsc2'] = $cComCsc2;         // No. Causación
                $mNotasPCC[$nInd_mNotasPCC]['caufecve'] = $cComFecVen;       // Fecha Vencimiento de Pago al Proveedor
                $mNotasPCC[$nInd_mNotasPCC]['cscfactx'] = $xRDM['comcscxx']; // No. Factura de Venta de Mario Londoño
                $mNotasPCC[$nInd_mNotasPCC]['fecfactx'] = $xRDM['comfecxx']; // Fecha Factura de Venta de Mario Londoño
                $mNotasPCC[$nInd_mNotasPCC]['factcanc'] = "";                // Documento que Cancela Factura de Venta - Aplica solo a las Facturas
                $mNotasPCC[$nInd_mNotasPCC]['nitfactx'] = $xRDM['teridxxx']; // Nit del Facturar A
                $mNotasPCC[$nInd_mNotasPCC]['nomfactx'] = $mTerceros[$xRDM['teridxxx']]; // Nombre del Facturar A
                $mNotasPCC[$nInd_mNotasPCC]['comvlrxx'] = ($xRDM['comvlrxx']*-1); // Valor del Gasto de la Factura del Proveedor
              }
            }

            // Asigna el valor de los Anticipos en la misma matriz de pagos a terceros
            for ($i=0; $i < count($mAnticipos); $i++) { 
              if (count($mPCC) == 0) {
                $mPCC[$i]['sucidxxx'] = $mAnticipos[$i]['sucidxxx'];
                $mPCC[$i]['docidxxx'] = $mAnticipos[$i]['docidxxx'];
                $mPCC[$i]['docsufxx'] = $mAnticipos[$i]['docsufxx'];
                $mPCC[$i]['comidxxx'] = $mAnticipos[$i]['comidxxx'];
                $mPCC[$i]['comcodxx'] = $mAnticipos[$i]['comcodxx'];
                $mPCC[$i]['comcscxx'] = $mAnticipos[$i]['comcscxx'];
                $mPCC[$i]['comcsc2x'] = $mAnticipos[$i]['comcsc2x'];
              }
              $mPCC[$i]['vlrantix'] = $mAnticipos[$i]['vlrantix'];
              $mPCC[$i]['docantix'] = $mAnticipos[$i]['docantix'];
              $mPCC[$i]['fechanti'] = $mAnticipos[$i]['fechanti'];
            }

            $mPCC = array_merge($mPCC, $mNotasPCC);
          }
        }


        $nInd_mDatos = count($mDatos);
        $mDatos[$nInd_mDatos]['cliidxxx'] = $xRDD['cliidxxx'];
        $mDatos[$nInd_mDatos]['clinomxx'] = $xRDD['clinomxx'];
        $mDatos[$nInd_mDatos]['docidxxx'] = $xRDD['docidxxx'];
        $mDatos[$nInd_mDatos]['doctipxx'] = $xRDD['doctipxx'];
        $mDatos[$nInd_mDatos]['docpedxx'] = $cPedido;
        $mDatos[$nInd_mDatos]['datos']    = $mPCC;
      } ## while ($xDD = mysql_fetch_array($xDatDoi)) { ## Recorro Do's
      
      // echo "<pre>";
      // print_r($mDatos);

      $nCanReg = 0;
      foreach ($mDatos as $key => $value) {
        // Inicializa las variables para el control de la primera fila del DO
        $cSucId  = "";
        $cDocId  = "";
        $cDocSuf = "";

        // Inicializa las variables para el control de cambio de comprobante
        $cComId    = "";
        $cComCod   = "";
        $cComCsc   = "";
        $cComCsc2  = "";

        $mComprobantes = $value['datos'];
        for ($i=0; $i < count($mComprobantes); $i++) { 
          // Obtiene el total de los Pagos a Terceros por comprobante
          $nTotPagos = 0;
          for ($j=0; $j < count($mComprobantes); $j++) { 
            if ($mComprobantes[$i]['comidxxx'] == $mComprobantes[$j]['comidxxx'] && $mComprobantes[$i]['comcodxx'] == $mComprobantes[$j]['comcodxx'] && 
                $mComprobantes[$i]['comcscxx'] == $mComprobantes[$j]['comcscxx'] && $mComprobantes[$i]['comcsc2x'] == $mComprobantes[$j]['comcsc2x']) 
            {
              $nTotPagos += $mComprobantes[$j]['comvlrxx'] != "" ? $mComprobantes[$j]['comvlrxx'] : 0;
            }
          }

          $cNitCliDo = "";
          $cNomCliDo = "";
          $cNumeroDo = "";
          $cTipoOper = "";
          $cNumPedid = "";  
          // Detecta el cambio de DO
          if ($cSucId != $mComprobantes[$i]['sucidxxx'] || $cDocId != $mComprobantes[$i]['docidxxx'] || $cDocSuf != $mComprobantes[$i]['docsufxx']) {
            $cSucId    = $mComprobantes[$i]['sucidxxx'];
            $cDocId    = $mComprobantes[$i]['docidxxx'];
            $cDocSuf   = $mComprobantes[$i]['docsufxx'];
            $cNitCliDo = $value['cliidxxx'];
            $cNomCliDo = $value['clinomxx'];
            $cNumeroDo = $value['docidxxx'];
            $cTipoOper = $value['doctipxx'];
            $cNumPedid = $value['docpedxx'];
            $nCanReg++;

            // Obtiene el valor total de los Anticipos por DO
            $nTotAnticipos = 0;
            for ($j=0; $j < count($mComprobantes); $j++) { 
              $nTotAnticipos += $mComprobantes[$j]['vlrantix'] != "" ? $mComprobantes[$j]['vlrantix'] : 0;
            }
          }

          $nCambiaComp = 0;
          // Detecta el cambio de Comprobante
          if ($cComId != $mComprobantes[$i]['comidxxx'] || $cComCod != $mComprobantes[$i]['comcodxx'] || $cComCsc != $mComprobantes[$i]['comcscxx'] || $cComCsc2 != $mComprobantes[$i]['comcsc2x']) {
            $cComId      = $mComprobantes[$i]['comidxxx'];
            $cComCod     = $mComprobantes[$i]['comcodxx'];
            $cComCsc     = $mComprobantes[$i]['comcscxx'];
            $cComCsc2    = $mComprobantes[$i]['comcsc2x'];
            $nCambiaComp = 1;
          }

          switch ($cTipo) {
            case 1:  // PINTA POR PANTALLA //
              if ($_SERVER["SERVER_PORT"] != "") {
                $zColorPro = "#000000"; ?>
                <tr bgcolor = "white" height="20"   style="padding-left:4px;padding-right:4px">
                  
                  <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $cNitCliDo ?></td>
                  <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $cNomCliDo ?></td>
                  <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $cNumeroDo ?></td>
                  <td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo number_format($mComprobantes[$i]['vlrantix'],2,',','.') ?></td>
                  <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mComprobantes[$i]['docantix'] ?></td>
                  <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($mComprobantes[$i]['fechanti'] != "") ? date("Y/m/d", strtotime($mComprobantes[$i]['fechanti'])) : "" ?></td>
                  <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $cTipoOper ?></td>
                  <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $cNumPedid ?></td>
                  <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $mComprobantes[$i]['pronitxx'] ?></td>
                  <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mComprobantes[$i]['pronomxx'] ?></td>
                  <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mComprobantes[$i]['ctodesxx'] ?></td>
                  <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mComprobantes[$i]['causcscx'] ?></td>
                  <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($mComprobantes[$i]['causfecx'] != "") ? date("Y/m/d", strtotime($mComprobantes[$i]['causfecx'])) : "" ?></td>
                  <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mComprobantes[$i]['causcsc2'] ?></td>
                  <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($mComprobantes[$i]['caufecve'] != "") ? date("Y/m/d", strtotime($mComprobantes[$i]['caufecve'])) : "" ?></td>
                  <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mComprobantes[$i]['csccruce'] ?></td>
                  <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mComprobantes[$i]['cscfactx'] ?></td>
                  <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo ($mComprobantes[$i]['fecfactx'] != "") ? date("Y/m/d", strtotime($mComprobantes[$i]['fecfactx'])) : "" ?></td>
                  <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mComprobantes[$i]['factcanc'] ?></td>
                  <td class="letra7" align="center" style = "color:<?php echo $zColorPro ?>"><?php echo $mComprobantes[$i]['nitfactx'] ?></td>
                  <td class="letra7" align="left"   style = "color:<?php echo $zColorPro ?>"><?php echo $mComprobantes[$i]['nomfactx'] ?></td>
                  <td class="letra7" align="right"  style = "color:<?php echo $zColorPro ?>"><?php echo number_format($mComprobantes[$i]['comvlrxx'],2,',','.') ?></td>
                  <?php if ($nCambiaComp == 1) { 
                    $nVlrTotal = abs(abs($nTotAnticipos) - abs($nTotPagos));
                    $nVlrTotal = ($nTotAnticipos > $nTotPagos) ? ($nVlrTotal*-1) : $nVlrTotal;
                    $nVlrTotal = (($mComprobantes[$i]['tipocomp'] == "LP" || $mComprobantes[$i]['comidxxx'] == "C") && ($mComprobantes[$i]['cscfactx'] != "" || $mComprobantes[$i]['tienefac'] == "SI")) ? "0,00" : $nVlrTotal;

                    ?>
                    <td class="letra7" align="right" style = "color:<?php echo $zColorPro ?>"><?php echo number_format($nTotPagos,2,',','.') ?></td>
                    <td class="letra7" align="right" style = "color:<?php echo $zColorPro ?>"><?php echo number_format($nVlrTotal,2,',','.') ?></td>     
                  <?php } else { 
                    $nVlrTotal = (($mComprobantes[$i]['tipocomp'] == "LP" || $mComprobantes[$i]['comidxxx'] == "C") && ($mComprobantes[$i]['cscfactx'] != "" || $mComprobantes[$i]['tienefac'] == "SI")) ? "0,00" : "";
                    ?> 
                    <td class="letra7" align="right" style = "color:<?php echo $zColorPro ?>">&nbsp;</td>
                    <td class="letra7" align="right" style = "color:<?php echo $zColorPro ?>"><?php echo $nVlrTotal ?></td>
                  <?php } ?>

                </tr>
                <?php 
              }
            break;
            case 2: // PINTA POR EXCEL //
                $zColorPro = "#000000";
                $nValor01 = ($mComprobantes[$i]['vlrantix'] != "") ? number_format($mComprobantes[$i]['vlrantix'],2,',','.') : ""; // Valor Anticipos

                $data  = '<tr bgcolor = "white" height="20"   style="padding-left:4px;padding-right:4px">';
                  $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$cNitCliDo.'</td>';
                  $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$cNomCliDo.'</td>';
                  $data .= '<td class="letra7" align="left"   style = "mso-number-format:\'\@\';color:'.$zColorPro.'">'.$cNumeroDo.'</td>';
                  $data .= '<td class="letra7" align="rigth"  style = "color:'.$zColorPro.'">'.$nValor01.'</td>';
                  $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mComprobantes[$i]['docantix'].'</td>';
                  $data .= '<td class="letra7" align="center" style = "mso-number-format:\'yyyy/mm/dd\';color:'.$zColorPro.'">'.$mComprobantes[$i]['fechanti'].'</td>';
                  $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$cTipoOper.'</td>';
                  $data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$cNumPedid.'</td>';
                  $data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$mComprobantes[$i]['pronitxx'].'</td>';
                  $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mComprobantes[$i]['pronomxx'].'</td>';
                  $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mComprobantes[$i]['ctodesxx'].'</td>';
                  $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mComprobantes[$i]['causcscx'].'</td>';
                  $data .= '<td class="letra7" align="center" style = "mso-number-format:\'yyyy/mm/dd\';color:'.$zColorPro.'">'.$mComprobantes[$i]['causfecx'].'</td>';
                  $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mComprobantes[$i]['causcsc2'].'</td>';
                  $data .= '<td class="letra7" align="center" style = "mso-number-format:\'yyyy/mm/dd\';color:'.$zColorPro.'">'.$mComprobantes[$i]['caufecve'].'</td>';
                  $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mComprobantes[$i]['csccruce'].'</td>';
                  $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mComprobantes[$i]['cscfactx'].'</td>';
                  $data .= '<td class="letra7" align="center" style = "mso-number-format:\'yyyy/mm/dd\';color:'.$zColorPro.'">'.$mComprobantes[$i]['fecfactx'].'</td>';
                  $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mComprobantes[$i]['factcanc'].'</td>';
                  $data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$mComprobantes[$i]['nitfactx'].'</td>';
                  $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$mComprobantes[$i]['nomfactx'].'</td>';
                  $data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'">'.number_format($mComprobantes[$i]['comvlrxx'],2,',','.').'</td>';
                  if ($nCambiaComp == 1) {
                    $nVlrTotal = abs(abs($nTotAnticipos) - abs($nTotPagos));
                    $nVlrTotal = ($nTotAnticipos > $nTotPagos) ? ($nVlrTotal*-1) : $nVlrTotal;
                    $nVlrTotal = (($mComprobantes[$i]['tipocomp'] == "LP" || $mComprobantes[$i]['comidxxx'] == "C") && ($mComprobantes[$i]['cscfactx'] != "" || $mComprobantes[$i]['tienefac'] == "SI")) ? "0,00" : $nVlrTotal;

                    $data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'">'.number_format($nTotPagos,2,',','.').'</td>';
                    $data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'">'.number_format($nVlrTotal,2,',','.').'</td>';
                  } else {
                    $nVlrTotal = (($mComprobantes[$i]['tipocomp'] == "LP" || $mComprobantes[$i]['comidxxx'] == "C") && ($mComprobantes[$i]['cscfactx'] != "" || $mComprobantes[$i]['tienefac'] == "SI")) ? "0,00" : "";
                    $data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'"></td>';
                    $data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'">'.$nVlrTotal.'</td>';
                  }

                $data .= '</tr>';

                fwrite($fOp, $data);
            break;
          } // Fin Switch
        }
      }

      switch ($cTipo) {
        case 1:
            // PINTA POR PANTALLA// ?>
                </form>
                </table>
              </body>
            </html>
            <script type="text/javascript">
              document.forms['frgrm']['nCanReg'].value = "<?php echo $nCanReg ?>";
            </script>
          <?php 
        break;
        case 2:
            // Colspan una ultima fila
            $data  = '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
              $data .= '<td class="name" colspan="'.$nColspan.'" align="left">';
                  $data .= '<font size="3">';
                    $data   .= '<b>TOTAL DOs EN ESTA CONSULTA ['.$nCanReg.']<br>';
                  $data .= '</font>';
              $data .= '</td>';
            $data .= '</tr>';
          $data .= '</table>';
          
          fwrite($fOp, $data);
          fclose($fOp);
            
          if (file_exists($cFile)) {

            if ($data == "") {
              $data = "\n(0) REGISTROS!\n";
            }

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

          }else {
            $nSwitch = 1;
            if ($_SERVER["SERVER_PORT"] != "") {
              f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
            } else {
              $cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
            }
          }
        break;
      }//Fin Switch
    }
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
  
  /**
   * Metodo que realiza la conexion
   */
  function fnConectarDBReporteAnticiposClientes(){

    /**
     * Variable para saber si hay o no errores de validacion.
     *
     * @var int
     */
    $nSwitch = 0;

    /**
     * Matriz para Retornar Valores
     * 
     * @var array
     */
    $mReturn = array();

    /**
     * Reservo Primera Posicion para retorna true o false.
     */
    $mReturn[0] = "";

    $xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or ç("El Sistema no Logro Conexion con ".OC_SERVER);
    if($xConexion99){
      $nSwitch = 0;
    }else{
      $nSwitch = 1;
      $mReturn[count($mReturn)] = "El Sistema no Logro Conexion con ".OC_SERVER;
    }

    if($nSwitch == 0){
      $mReturn[0] = "true";
      $mReturn[1] = $xConexion99;
    }else{
      $mReturn[0] = "false";
    }
    return $mReturn;
  }##function fnConectarDBReporteAnticiposClientes(){##
  
?>

<?php
	/**
	 * Imprime Reporte PyG por DO.
	 * --- Descripcion: Permite Imprimir Reporte PyG por DO.
	 * @author Sebastian Cardenas Suarez <sebastian.cardenas@open-eb.co>
	 */
	header('Content-Type:text/html; charset=UTF-8');

	set_time_limit(0);
	ini_set("memory_limit", "512M");

	// ini_set('error_reporting', E_ERROR);
	// ini_set("display_errors","1");

	date_default_timezone_set("America/Bogota");

	/**
	 * Cantidad de Registros para reiniciar conexion
	 */
	define("_NUMREG_", 50);

	/**
	 * Variables de control de errores
	 * @var number
	 */
	$nSwitch = 0;

	/**
	 * Variable para almacenar los mensajes de error
	 * @var string
	 */
	$cMsj = "\n";

	/**
	 * Variables para reemplazar caracteres especiales
	 * @var array
	 */
	$cBuscar = array('"', "'", chr(13), chr(10), chr(27), chr(9));
	$cReempl = array('\"', "\'", " ", " ", " ", " ");

	/**
	 * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales
	 * @var Number
	 */
	$cEjePro = 0;

	/**
	 * Nombre del archivo excel
	 */
	$cNomArc = "";

	/**
	 * Cuando se ejecuta desde el cron debe armarse la cookie para incluir los utilitys
	 */
	if ($_SERVER["SERVER_PORT"] == "") {
		$vArg = explode(",", $argv[1]);

		if ($vArg[0] == "") {
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
			$cMsj .= "El parametro Id del Proceso no puede ser vacio.\n";
		}

		if ($vArg[1] == "") {
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
			$cMsj .= "El parametro de la Cookie no puede ser vacio.\n";
		}

		if ($nSwitch == 0) {
			$_COOKIE["kDatosFijos"] = $vArg[1];

			// Librerias
			include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
			include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utility.php");
			include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/uticrgen.php");
      include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php");
			/**
			 * Buscando el ID del proceso
			 */
			$qProBg = "SELECT * ";
			$qProBg .= "FROM $cBeta.sysprobg ";
			$qProBg .= "WHERE ";
			$qProBg .= "pbaidxxx= \"{$vArg[0]}\" AND ";
			$qProBg .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
			$xProBg = f_MySql("SELECT","",$qProBg,$xConexion01,"");
			if (mysql_num_rows($xProBg) == 0) {
				$xRPB = mysql_fetch_array($xProBg);
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
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
		## Librerias
		include("../../../../../config/config.php");
		include("../../../../libs/php/utility.php");
		include("../../../../libs/php/uticrgen.php");
		include("../../../../../libs/php/utiprobg.php");
	}

	/**
	 *  Cookie fija
	 */
	$kDf = explode("~", $_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb 	= $kDf[3];
	$kUser 			= $kDf[4];
	$kLicencia 	= $kDf[5];
	$swidth			= $kDf[6];

  $nSwitch = 0;
  $cMsj = "";

  //Creando tablas temporales
  if ($_SERVER["SERVER_PORT"] != "") {
    /*** Ejecutar proceso en Background ***/
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;

    //Creando tablas temporales
    $objEstructurasReportesGerenciales = new cEstructurasReportesGerenciales(); // se instancia la clase cTOE
    
    $vParametros['TIPOTABL'] = "PYGXDO";
    $mReturnTablaC = $objEstructurasReportesGerenciales->fnCrearEstructurasReportesGerenciales($vParametros);

    $vParametros['TIPOTABL'] = "ERRORES";
    $mReturnTablaE = $objEstructurasReportesGerenciales->fnCrearEstructurasReportesGerenciales($vParametros);

    //Imprimir parametros de la tabla creada (temporal)
    if($mReturnTablaC[0] == "false") {
      //No hace nada
      $nSwitch = 1;
      for($nR=1;$nR<count($mReturnTablaC);$nR++){
        $nSwitch = 1;
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "{$mReturnTablaC[$nR]}\n";
      }
    }

    if($mReturnTablaE[0] == "false") {
      //No hace nada
      $nSwitch = 1;
      for($nR=1;$nR<count($mReturnTablaE);$nR++){
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .= "{$mReturnTablaE[$nR]}\n";
      }
    }
  }

	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;
    $cTablas = $mReturnTablaC[1]."~".$mReturnTablaE[1];
		$strPost = "rTipo~" 	 . $rTipo.
							"|gTerId~" 	 . $gTerId.
							"|gTerNom~"  . $gTerNom.
							"|gSucId~" 	 . $gSucId.
							"|gDocNro~"  . $gDocNro.
							"|gDocSuf~"  . $gDocSuf.
							"|gComId~" 	 . $gComId.
							"|gComCod~"  . $gComCod.
							"|gComCsc~"  . $gComCsc.
							"|gComCsc2~" . $gComCsc2.
							"|gDesde~" 	 . $gDesde.
							"|gHasta~"   . $gHasta.
							"|gPerAno~"  . $gPerAno.
							"|cEjProBg~" . $cEjProBg;

		$vParBg['pbadbxxx'] = $cAlfa;                       // Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                // Modulo
		$vParBg['pbatinxx'] = "REPORTEPYGXDO";       	      // Tipo Interface
		$vParBg['pbatinde'] = "REPORTE PyG POR DO";     		// Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                           // Sucursal
		$vParBg['doiidxxx'] = "";                           // Do
		$vParBg['doisfidx'] = "";                           // Sufijo
		$vParBg['cliidxxx'] = $gTerId;                      // Nit
		$vParBg['clinomxx'] = $gTerNom;                     // Nombre Importador
		$vParBg['pbapostx'] = $strPost;											// Parametros para reconstruir Post
		$vParBg['pbatabxx'] = $cTablas;                     // Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];	// Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];      // cookie
		$vParBg['pbacrexx'] = 0;                            // Cantidad Registros
		$vParBg['pbatxixx'] = 1;                            // Tiempo Ejecucion x Item en Segundos
		$vParBg['pbaopcxx'] = "";                           // Opciones
		$vParBg['regusrxx'] = $kUser;                       // Usuario que Creo Registro

		## Incluyendo la clase de procesos en background
		$ObjProBg = new cProcesosBackground();
		$mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);

		## Imprimiendo resumen de todo ok.
		if ($mReturnProBg[0] == "true") {
			f_Mensaje(__FILE__, __LINE__, "Proceso en Background Agendado con Exito."); ?>
			<script languaje = "javascript">
					parent.fmwork.fnRecargar();
			</script>
		<?php } else {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}			
		}
	}

  if ($_SERVER["SERVER_PORT"] == "") {
		$rTipo     = $_POST['rTipo'];
		$gTerId    = $_POST['gTerId'];
		$gTerNom   = $_POST['gTerNom'];
		$gSucId    = $_POST['gSucId'];
		$gDocTip   = $_POST['gDocTip'];
		$gDocNro   = $_POST['gDocNro'];
		$gDocSuf   = $_POST['gDocSuf'];
		$gComId    = $_POST['gComId'];
		$gComCod   = $_POST['gComCod'];
		$gComCsc   = $_POST['gComCsc'];
		$gComCsc2  = $_POST['gComCsc2'];
		$gDesde    = $_POST['gDesde'];
		$gHasta    = $_POST['gHasta'];
		$gPerAno   = $_POST['gPerAno'];
		$cEjProBg  = $_POST['cEjProBg'];

    /**
     * Armando parametros de las tablas
     */
    $mTablas = explode("~",$xRB['pbatabxx']);
    
    /**
     * Vectore de tablas temporales
     */
    $mReturnTablaC[1] = $mTablas[0];
    $mReturnTablaE[1] = $mTablas[1];
	}

	if ($cEjePro == 0) {
		if ($nSwitch == 0) {
			$objReportesGerenciales = new cReportesGerenciales(); // se instancia la clase cTOE

      $vDatos = array();
      $vDatos['TABLAXXX'] = $mReturnTablaC[1];
      $vDatos['TABLAERR'] = $mReturnTablaE[1];
			$vDatos['TIPREPXX'] = $rTipo;            //TIPO DE REPORTE
			$vDatos['TERIDXXX'] = $gTerId;           //ID CLIENTE
			$vDatos['TERNOMXX'] = $gTerNom;          //NOMBRE DEL CLIENTE
			$vDatos['SUCIDXXX'] = $gSucId;           //ID DE LA SUCURSAL
			$vDatos['DOCIDXXX'] = $gDocNro;          //ID DEL DO
			$vDatos['DOCSUFXX'] = $gDocSuf;          //SUFIJO DEL DO
			$vDatos['PERANOXX'] = $gPerAno;          //AÑO DE LA FACTURA
			$vDatos['COMIDXXX'] = $gComId;           //ID DE LA FACTURA
			$vDatos['COMCODXX'] = $gComCod;          //CODIGO DE LA FACTURA
			$vDatos['COMCSCXX'] = $gComCsc;          //CONSECUTIVO DE LAFACTURA
			$vDatos['COMCSC2X'] = $gComCsc2;         //CONSECUTIVO 2 DE LA FACTURA
			$vDatos['DDESDEXX'] = $gDesde;           //FECHA DESDE
			$vDatos['DHASTAXX'] = $gHasta;           //FECHA HASTA

			$mReturnReporte = $objReportesGerenciales->fnReportePyGxDo($vDatos);
      if ($mReturnReporte[0] == false) {
        $nSwitch = 1;
        $qTabErr  = "SELECT * ";
        $qTabErr .= "FROM $cAlfa.{$vDatos['TABLAERR']}";
        $xTabErr = f_MySql("SELECT","",$qTabTem,$xConexion01,"");
        while ($xRTE = mysql_fetch_array($xTabErr)) {
          $cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
          $cMsj .= $xRTE['DESERROR']."\n";
        }
      } else {
        $cFecha = $vDatos['DDESDEXX']." HASTA:  ".$vDatos['DHASTAXX'];

        $qTabTem  = "SELECT * ";
        $qTabTem .= "FROM $cAlfa.$mReturnTablaC[1]";
        $xTabTem = f_MySql("SELECT","",$qTabTem,$xConexion01,"");

        if (mysql_num_rows($xTabTem) > 0) {
          switch ($vDatos['TIPREPXX']) {
            case 1: // PINTA POR PANTALLA//
              ?>
              <html>
                <title>Reporte PyG por DO</title>
                <head>
                  <LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
                  <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
                  <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
                  <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
                  <LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
                  <script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
                  <script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
                </head>
                <body topmargin = "0" leftmargin = "0" rightmargin = "0" bottommargin = "0" marginheight = "0" marginwidth = "0">
                  <?php
                  $nCol = 12;
                  ?>
                  <center>
                    <br>
                    <table border = "1" cellpadding = "0" cellspacing = "0" width = "95%">
                      <tr>
                        <td style="padding-left: 10px;font-size:14px" class="name">
                          <span style="font-size:18px">REPORTE PyG POR DO</span><br>
                          <?php if ($gDesde != '' || $gHasta != '') { ?>
                            <span style="font-size:18px"> <?php echo "FECHA - DESDE:  ". $cFecha ?></span><br>
                          <?php } ?>
                          <br>
                          <?php if ($gTerId != '') { ?>
                            <b>NIT: </b><b><?php echo $vDatos['TERIDXXX']."-".f_Digito_Verificacion($vDatos['TERIDXXX'])?></b><br>
                          <?php }
                                if ($gTerNom != '') { ?>
                            <b>CLIENTE: </b><b><?php echo $vDatos['TERNOMXX']?></b><br>
                          <?php }
                                if ($gSucId != '' || $gDocNro != '' || $gDocSuf != '') { ?>
                            <b>DO: </b><b><?php echo $vDatos['SUCIDXXX'].' - '.$vDatos['DOCIDXXX'].' - '.$vDatos['DOCSUFXX']?></b><br>
                          <?php } ?>
                          <b>FECHA Y HORA DE CONSULTA: </b><b><?php echo date("Y-m-d H:i:s");?></b>
                        </td>
                      </tr>
                    </table>
                    <br>
                    <table border = "1" cellpadding = "0" cellspacing = "0" width = "95%">
                      <tr>
                        <td style="padding-left: 10px;font-size:14px" align="center" colspan = "<?php echo $nCol ?>" bgcolor = "#96ADEB"><b>REPORTE PyG POR DO</b></td>
                      </tr>
                      <tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>'>
                        <td align="center" style="width:8%"><b>FACTURA</b></td>
                        <td align="center" style="width:8%"><b>FECHA FACTURA</b></td>
                        <td align="center" style="width:6%"><b>SUCURSAL</b></td>
                        <td align="center" style="width:7%"><b>DO</b></td>
                        <td align="center" style="width:6%"><b>SUFIJO</b></td>
                        <td align="center" style="width:8%"><b>NIT IMPORTADOR</b></td>
                        <td align="center" style="width:14%"><b>NOMBRE IMPORTADOR</b></td>
                        <td align="center" style="width:8%"><b>NIT FACTURAR A</b></td>
                        <td align="center" style="width:13%"><b>NOMBRE FACTURAR A</b></td>
                        <td align="center" style="width:8%"><b>ID CONCEPTO</b></td>
                        <td align="center" style="width:8%"><b>DESCRIPCION CONCEPTO</b></td>
                        <td align="center" style="width:6%"><b>VALOR</b></td>
                      </tr>
                      <?php
                      while ($xRTT = mysql_fetch_array($xTabTem)) {
                      ?>
                      <tr>
                        <td align="right"><?php echo $xRTT['resprexx'].$xRTT['comcscxx'] ?></td>
                        <td align="right"><?php echo $xRTT['comfecxx'] ?></td>
                        <td align="left"><?php echo $xRTT['sucidxxx'] ?></td>
                        <td align="right"><?php echo $xRTT['docidxxx'] ?></td>
                        <td align="left"><?php echo $xRTT['docsufxx'] ?></td>
                        <td align="right"><?php echo $xRTT['teridxxx'] ?></td>
                        <td align="left"><?php echo $xRTT['ternomxx'] ?></td>
                        <td align="right"><?php echo $xRTT['terid2xx'] ?></td>
                        <td align="left"><?php echo $xRTT['ternom2x'] ?></td>
                        <td align="left"><?php echo $xRTT['ctoidxxx'] ?></td>
                        <td align="left"><?php echo $xRTT['ctodesxx'] ?></td>
                        <td align="right"><?php echo ($xRTT['commovxx'] == 'C') ? $xRTT['comvlrxx']:($xRTT['comvlrxx'] * -1) ?></td>
                      </tr>
                      <?php
                      }
                      ?>
                    </table>
                  </center>
                </body>
              </html>
            <?php break;
            case 2: // PINTA POR EXCEL//
              /**
               * Variable para armar la cadena de texto que se envia al excel
               * @var Text
               */
              $header  .= 'Reporte PyG por DO'."\n";
              $header  .= "\n";
              $cData    = '';
              $cNomFile = "REPORTE_PyG_POR_DO_".$kUser."_".date('YmdHis').".xls";

              if ($_SERVER["SERVER_PORT"] != "") {
                $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
              } else {
                $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
              }
          
              if (file_exists($cFile)) {
                unlink($cFile);
              }

              $fOp = fopen($cFile, 'a');

              $nCol = 12;  
              $cData .= '<table border = "1" cellpadding = "0" cellspacing = "0">';
                $cData .= '<tr>';
                  $cData .= '<td style="padding-left: 10px;font-size:14px" class="name" colspan = "'.$nCol.'">';
                    $cData .= '<span style="font-size:18px">REPORTE PyG POR DO</span><br>';
                    if ($gDesde != '' || $gHasta != '') {
                      $cData .= '<span style="font-size:18px">FECHA - DESDE:  '.$cFecha.'</span><br>';
                    }
                  $cData .= '</td>';
                $cData .= '</tr>';
                if ($gTerId != '') {
                  $cData .= '<tr>';
                    $cData .= '<td style="padding-left: 10px;font-size:14px" class="name" colspan = "'.$nCol.'">';
                      $cData .= '<b>NIT: </b><b>'.$vDatos['TERIDXXX']."-".f_Digito_Verificacion($vDatos['TERIDXXX']).'</b><br>';
                    $cData .= '</td>';
                  $cData .= '</tr>';
                }
                if ($gTerNom != '') {
                  $cData .= '<tr>';
                    $cData .= '<td style="padding-left: 10px;font-size:14px" class="name" colspan = "'.$nCol.'">';
                      $cData .= '<b>CLIENTE: </b><b>'.$vDatos['TERNOMXX'].'</b><br>';
                    $cData .= '</td>';
                  $cData .= '</tr>';
                }
                if ($gSucId != '' || $gDocNro != '' || $gDocSuf != '') {
                  $cData .= '<tr>';
                    $cData .= '<td style="padding-left: 10px;font-size:14px" class="name" colspan = "'.$nCol.'">';
                      $cData .= '<b>DO: </b><b>'.$vDatos['SUCIDXXX'].' - '.$vDatos['DOCIDXXX'].' - '.$vDatos['DOCSUFXX'].'</b><br>';
                    $cData .= '</td>';
                  $cData .= '</tr>';
                }
                $cData .= '<tr>';
                  $cData .= '<td style="padding-left: 10px;font-size:14px" class="name" colspan = "'.$nCol.'">';
                    $cData .= '<b>FECHA Y HORA DE CONSULTA: </b><b>'.date("Y-m-d H:i:s").'</b>';
                  $cData .= '</td>';
                $cData .= '</tr>';
                $cData .= '<tr><td colspan = "'.$nCol.'"></td></tr>';
                $cData .= '<tr>';
                  $cData .= '<td style="padding-left: 10px;font-size:14px" align="left" colspan = "'.$nCol.'" bgcolor = "#96ADEB"><b>REPORTE PyG POR DO</b></td>';
                $cData .= '</tr>';
                $cData .= '<tr>';
                  $cData .= "<td align='center' bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" style='width:120px'><b>FACTURA</b></td>";
                  $cData .= "<td align='center' bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" style='width:130px'><b>FECHA FACTURA</b></td>";
                  $cData .= "<td align='center' bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" style='width:90px'><b>SUCURSAL</b></td>";
                  $cData .= "<td align='center' bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" style='width:140px'><b>DO</b></td>";
                  $cData .= "<td align='center' bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" style='width:90px'><b>SUFIJO</b></td>";
                  $cData .= "<td align='center' bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" style='width:140px'><b>NIT IMPORTADOR</b></td>";
                  $cData .= "<td align='center' bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" style='width:170px'><b>NOMBRE IMPORTADOR</b></td>";
                  $cData .= "<td align='center' bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" style='width:140px'><b>NIT FACTURAR A</b></td>";
                  $cData .= "<td align='center' bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" style='width:170px'><b>NOMBRE FACTURAR A</b></td>";
                  $cData .= "<td align='center' bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" style='width:120px'><b>ID CONCEPTO</b></td>";
                  $cData .= "<td align='center' bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" style='width:200px'><b>DESCRIPCION CONCEPTO</b></td>";
                  $cData .= "<td align='center' bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" style='width:90px'><b>VALOR</b></td>";
                $cData .= '</tr>';
                while ($xRTT = mysql_fetch_array($xTabTem)) {
                  $cValor = ($xRTT['commovxx'] == 'C') ? $xRTT['comvlrxx']:($xRTT['comvlrxx'] * -1);

                  $cData .= '<tr>';
                    $cData .= '<td align="right" style="mso-number-format:\'\@\'">'.$xRTT['resprexx'].$xRTT['comcscxx'].'</td>';
                    $cData .= '<td align="right" style="mso-number-format:\'\@\'">'.$xRTT['comfecxx'].'</td>';
                    $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['sucidxxx'].'</td>';
                    $cData .= '<td align="right" style="mso-number-format:\'\@\'">'.$xRTT['docidxxx'].'</td>';
                    $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['docsufxx'].'</td>';
                    $cData .= '<td align="right" style="mso-number-format:\'\@\'">'.$xRTT['teridxxx'].'</td>';
                    $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['ternomxx'].'</td>';
                    $cData .= '<td align="right" style="mso-number-format:\'\@\'">'.$xRTT['terid2xx'].'</td>';
                    $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['ternom2x'].'</td>';
                    $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['ctoidxxx'].'</td>';
                    $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.$xRTT['ctodesxx'].'</td>';
                    $cData .= '<td align="right" style="mso-number-format:\'\@\'">'.$cValor.'</td>';
                  $cData .= '</tr>';
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
                    echo "\n".$cNomArc;
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
            break;
            default:
              /*** PINTA POR PDF ***/
              $cAddr = "";
              if ($cAlfa == "DESARROL" || $cAlfa == "PRUEBASX") {
                $cAddr = "../";
              }

              $cRoot = $_SERVER['DOCUMENT_ROOT'];
              ##Switch para incluir fuente y clase pdf segun base de datos ##
              switch ($cAlfa) {
                case "COLMASXX":
                  define('FPDF_FONTPATH', "../../../../../fonts/");
                  require("../../../../../forms/fpdf.php");
                  break;
                default:
                  define('FPDF_FONTPATH', $_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
                  require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');
                  break;
              }
              ##Fin Switch para incluir fuente y clase pdf segun base de datos ##

              class PDF extends FPDF
              {
                function Header() {
                  global $vDatos;
                  global $cFecha;
                  global $gDesde;
                  global $gHasta;
                  global $gTerId;
                  global $gTerNom;
                  global $gSucId;
                  global $gDocNro;
                  global $gDocSuf;

                  $this->SetFont('verdana', 'B', 12);
                  $this->SetXY(6, 8);
                  $this->Cell(260, 8, "REPORTE PyG POR DO",0,0, 'C');
                  $this->Ln(7);
                  if ($gDesde != '' || $gHasta != '') {
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(260, 6, "FECHA - DESDE: ".$cFecha,0,0, 'C');
                    $this->Ln(4);
                  }
                  if ($gTerId != '') {
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(260,6,'NIT: '.$vDatos['TERIDXXX']."-".f_Digito_Verificacion($vDatos['TERIDXXX']),0,0,'C');
                    $this->Ln(4);
                  }
                  if ($gTerNom != '') {
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(260,6,'CLIENTE: '.$vDatos['TERNOMXX'],0,0,'C');
                    $this->Ln(4);
                  }
                  if ($gSucId != '' || $gDocNro != '' || $gDocSuf != '') {
                    $this->SetFont('verdana', '', 8);
                    $this->SetX(6);
                    $this->Cell(260,6,'DO: '.$vDatos['SUCIDXXX'].' - '.$vDatos['DOCIDXXX'].' - '.$vDatos['DOCSUFXX'],0,0,'C');
                    $this->Ln(4);
                  }
                  $this->SetFont('verdana', '', 8);
                  $this->SetX(6);
                  $this->Cell(260,6,'FECHA Y HORA DE CONSULTA: '.date("Y-m-d H:i:s"),0,0,'C');
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
                  for($i=0;$i<count($data);$i++){
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
                    $this->setX(6);
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

              $pdf = new PDF('L', 'mm', 'Letter');
              $pdf->AddFont('verdana', '', '');
              $pdf->AddFont('verdana', 'B', '');
              $pdf->AliasNbPages();
              $pdf->SetMargins(0,0,0);

              $pdf->AddPage();

              global $xRTT;
              global $xTabTem;
              
              $pdf->Ln(8);
              $pdf->SetFont('verdana', 'B', 6);
              $pdf->SetX(6);
              $pdf->SetLineWidth(0.2);
              $pdf->SetDrawColor(0,0,0);
              $pdf->SetFillColor(224,248,230);
              $pdf->Cell(22, 5, utf8_decode("FACTURA"), 'RLTB', 0, 'C', true);
              $pdf->Cell(22, 5, utf8_decode("FECHA FACTURA"), 'RLTB', 0, 'C', true);
              $pdf->Cell(18, 5, utf8_decode("SUCURSAL"), 'RLTB', 0, 'C', true);
              $pdf->Cell(16, 5, utf8_decode("DO"), 'RLTB', 0, 'C', true);
              $pdf->Cell(16, 5, utf8_decode("SUFIJO"), 'RLTB', 0, 'C', true);
              $pdf->Cell(24, 5, utf8_decode("NIT IMPORTADOR"), 'RLTB', 0, 'C', true);
              $pdf->Cell(30, 5, utf8_decode("NOMBRE IMPORTADOR"), 'RLTB', 0, 'C', true);
              $pdf->Cell(24, 5, utf8_decode("NIT FACTURAR A"), 'RLTB', 0, 'C', true);
              $pdf->Cell(30, 5, utf8_decode("NOMBRE FACTURAR A"), 'RLTB', 0, 'C', true);
              $pdf->Cell(20, 5, utf8_decode("ID CONCEPTO"), 'RLTB', 0, 'C', true);
              $pdf->Cell(32, 5, utf8_decode("DESCRIPCION CONCEPTO"), 'RLTB', 0, 'C', true);
              $pdf->Cell(14, 5, utf8_decode("VALOR"), 'RLTB', 0, 'C', true);
              $pdf->Ln(5);

              while ($xRTT = mysql_fetch_array($xTabTem)) {
                $pdf->SetFont('verdana', '', 6);
                $pdf->SetX(6);
                $pdf->SetLineWidth(0.2);
                $pdf->SetDrawColor(0,0,0);
                $pdf->SetFillColor(255,255,255);
                $pdf->setAligns(array('R', 'R', 'L', 'R', 'L', 'R', 'L', 'R', 'L', 'L', 'L', 'R'));
                $pdf->SetWidths(array(22,22,18,16,16,24,30,24,30,20,32,14));
                $pdf->Row(array(
                        $xRTT['resprexx'].$xRTT['comcscxx'],
                        $xRTT['comfecxx'],
                        $xRTT['sucidxxx'],
                        $xRTT['docidxxx'],
                        $xRTT['docsufxx'],
                        $xRTT['teridxxx'],
                        $xRTT['ternomxx'],
                        $xRTT['terid2xx'],
                        $xRTT['ternom2x'],
                        $xRTT['ctoidxxx'],
                        $xRTT['ctodesxx'],
                        ($xRTT['commovxx'] == 'C') ? $xRTT['comvlrxx']:($xRTT['comvlrxx'] * -1)
                      ));
              }

              $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory']."/pdf_".$_COOKIE['kUsrId']."_".date("YmdHis").".pdf";

              $pdf->Output($cFile);

              if (file_exists($cFile)) {
                chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
              } else {
                f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
              }

              echo "<html><script>document.location='$cFile';</script></html>";
            break;
          }
        } else {
          $nSwitch = 1;
          $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
          $cMsj .= "No se encontraron registros.\n";
        }
      }

			
		}
	}

  if ($_SERVER["SERVER_PORT"] != "") {
    if ($nSwitch == 1) {
      f_Mensaje(__FILE__, __LINE__, $cMsj."Verifique.");
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
				$cMsj .= "Linea ".str_pad(__LINE__, 4, "0", STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}
		}
	}

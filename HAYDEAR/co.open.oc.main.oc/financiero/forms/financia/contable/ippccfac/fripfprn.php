<?php

	/**
   * Genera archivo excel de ingresos propios y pagos a terceros facturados
   * @package opencomex
   * @todo ALL
   */

  // ini_set('display_errors', 1);
  // ini_set('display_startup_errors', 1);
  // error_reporting(E_ALL);

	ini_set("memory_limit","512M");
	set_time_limit(0);

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
	 * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales
	 * @var Number
	 */
	$cEjePro = 0;

	/**
	 * Nombre(s) de los archivos en excel generados
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

			# Librerias
			include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
			include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utility.php");
      include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php");
      include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utimovdo.php");

			/**
			 * Buscando el ID del proceso
			 */
			$qProBg  = "SELECT * ";
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
		include("../../../../../financiero/libs/php/utimovdo.php");
	}

	/**
	 *  Cookie fija
	 */
	$kDf = explode("~", $_COOKIE["kDatosFijos"]);
	$kUser = $kDf[4];

	$cSystemPath = OC_DOCUMENTROOT;
	
	if ($_SERVER["SERVER_PORT"] != "") {
		/*** Ejecutar proceso en Background ***/
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;

		if($gTerId != ""){
			#Busco el nombre del cliente
			$qCliNom  = "SELECT ";
			$qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
			$qCliNom .= "FROM $cAlfa.SIAI0150 ";
			$qCliNom .= "WHERE ";
			$qCliNom .= "CLIIDXXX = \"{$gTerId}\" LIMIT 0,1";
			$xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
			if (mysql_num_rows($xCliNom) > 0) {
				$xDDE = mysql_fetch_array($xCliNom);
			} else {
				$xDDE['clinomxx'] = "CLIENTE SIN NOMBRE";
			}
		}
	}
	
	if ($_SERVER["SERVER_PORT"] == "") {
		$gTerId  = $_POST['gTerId'];
		$gAnioD  = $_POST['gDesde'];
		$gAnioH  = $_POST['gHasta'];
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;
	
		$strPost = "gTerId~" . $gTerId . "|gDesde~" . $gAnioD . "|gHasta~" . $gAnioH;
	
		$vParBg['pbadbxxx'] = $cAlfa;                           // Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                    // Modulo
		$vParBg['pbatinxx'] = "REPORTE_IP_PCC_FACTURADOS";      // Tipo Interface
		$vParBg['pbatinde'] = "REPORTE DE INGRESOS PROPIOS Y PAGOS A TERCEROS FACTURADOS";      // Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                               // Sucursal
		$vParBg['doiidxxx'] = "";                               // Do
		$vParBg['doisfidx'] = "";                               // Sufijo
		$vParBg['cliidxxx'] = $gTerId;                          // Nit
		$vParBg['clinomxx'] = $xDDE['clinomxx'];                // Nombre Importador
		$vParBg['pbapostx'] = $strPost;													// Parametros para reconstruir Post
		$vParBg['pbatabxx'] = "";                               // Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];      // Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];          // cookie
		$vParBg['pbacrexx'] = 0;                      // Cantidad Registros
		$vParBg['pbatxixx'] = 1;                                // Tiempo Ejecucion x Item en Segundos
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
 
      ## array para el envío de datos al método ##
      $vDatos = array();
      $vDatos['cTipo']    = "2";    			// Tipo de impresión, por pdf o excel
      $vDatos['cGenerar'] = "FACTURADO"; 	// opción para impresión: facturado y/o no facturado
      $vDatos['cIntPag']  = "NO";  				// Intermediación de Pagos
      $vDatos['cTerId']   = $gTerId;   		// Tercero
      $vDatos['dFecDes']  = $gAnioD;  		// Fecha desde
			$vDatos['dFecHas']  = $gAnioH;  		// Fecha Hasta

      // Se instancia la clase cMovimientoDo del utility utimovdo.php
      $ObjMovimiento = new cMovimientoDo();

			// copiando
      // se envían todos los datos necesarios al método fnPagosaTerceros
      $mReturn   = $ObjMovimiento->fnPagosaTerceros($vDatos);
      $mDatosAux = $mReturn[1];
      $vResDat   = $mReturn[2];
      $vResId    = $mReturn[3];
      $vCocDat   = $mReturn[4];

      $mDatos = array();
      for ($i = 0; $i < count($mDatosAux); $i++) {
        $cTramite = $mDatosAux[$i]['sucidxxx']."~".$mDatosAux[$i]['docidxxx']."~".$mDatosAux[$i]['docsufxx'];
        $mInd_mDatos = count($mDatos[$cTramite][$mDatosAux[$i]['facturax']]); // índice para mDatos
        $mDatos[$cTramite][$mDatosAux[$i]['facturax']][$mInd_mDatos] = $mDatosAux[$i];
      }

      //Cuentas puc
      $qPucDat  = "SELECT *, ";
      $qPucDat .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
      $qPucDat .= "FROM $cAlfa.fpar0115 ";
      $xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
      $mCuenta = array();
      while($xRPD = mysql_fetch_array($xPucDat)) {
        $mCuenta["{$xRPD['pucidxxx']}"] = $xRPD;
      }

      # Consulta a la tabla de detalle para buscar los conceptos propios. OJO
      for ($cAno=substr($gAnioD,0,4); $cAno <= substr($gAnioH,0,4); $cAno++) {
        $qPropios = "SELECT $cAlfa.fcod$cAno.*, ";
        $qPropios.= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS ternomxx ";
        $qPropios.= "FROM $cAlfa.fcod$cAno ";
        $qPropios.= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
        $qPropios.= "WHERE $cAlfa.fcod$cAno.comidxxx = \"F\" ";
        $qPropios.= "AND $cAlfa.fcod$cAno.teridxxx = \"$gTerId\" ";
        $qPropios.= "AND $cAlfa.fcod$cAno.comfecxx BETWEEN \"$gAnioD\" AND \"$gAnioH\" ";
        $qPropios.= "AND $cAlfa.fcod$cAno.regestxx = \"ACTIVO\" ";
        $qPropios.= "AND $cAlfa.fcod$cAno.comctocx IN (\"IP\",\"RETFTE\",\"RETCRE\",\"RETICA\",\"RETIVA\",\"ARETFTE\",\"ARETCRE\",\"ARETICA\") ";
        $qPropios.= "ORDER BY $cAlfa.fcod$cAno.comidxxx,$cAlfa.fcod$cAno.comcodxx,$cAlfa.fcod$cAno.comcscxx,$cAlfa.fcod$cAno.comcsc2x,$cAlfa.fcod$cAno.comctocx";
        $xPropios = f_MySql("SELECT","",$qPropios,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qPropios."~".mysql_num_rows($xPropios));
        $cFacId = "";
        while( $xRP = mysql_fetch_assoc($xPropios)){
          if($cFacId != $xRP['comidxxx']."~".$xRP['comcodxx']."~".$xRP['comcscxx']."~".$xRP['comcsc2x']){
            if ($cFacId != "") {
              //Asignado retenciones al primer IP
              $mIP[$mRetenciones[0]['docidxxx']][$mRetenciones[0]['facturax']][0]['retencio'] = $mRetenciones;
            }

            $cFacId = $xRP['comidxxx']."~".$xRP['comcodxx']."~".$xRP['comcscxx']."~".$xRP['comcsc2x'];
            $mRetenciones = array();

            $qFcoc  = "SELECT comfpxxx, residxxx, resprexx, restipxx ";
            $qFcoc .= "FROM $cAlfa.fcoc$cAno ";
            $qFcoc .= "WHERE ";
            $qFcoc .= "comidxxx = \"{$xRP['comidxxx']}\" AND ";
            $qFcoc .= "comcodxx = \"{$xRP['comcodxx']}\" AND ";
            $qFcoc .= "comcscxx = \"{$xRP['comcscxx']}\" AND ";
            $qFcoc .= "comcsc2x = \"{$xRP['comcsc2x']}\" LIMIT 0,1";
            $xFcoc = f_MySql("SELECT","",$qFcoc,$xConexion01,"");
            // f_Mensaje(__FILE__,__LINE__,$qFcoc."~".mysql_num_rows($xFcoc));
            $vFcoc = mysql_fetch_array($xFcoc);
            $mTramites = f_Explode_Array($vFcoc['comfpxxx'],"|","~");

            $cPrimerTramite = ""; $vTramites = array();
            for ($i=0;$i<count($mTramites);$i++) {
              if ($cPrimerTramite == "") {
                $cPrimerTramite = $mTramites[$i][15]."~".$mTramites[$i][2]."~".$mTramites[$i][3];
              }
              $vTramites["{$mTramites[$i][2]}"] = $mTramites[$i][15]."~".$mTramites[$i][2]."~".$mTramites[$i][3];
            }
          }

          $cFactura = $xRP['comcscxx'];

          if ($cAlfa == "ROLDANLO" || $cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO") {

            //Para Roldan se imprime el prefijo de la factura y se completa con ceros el consecutivo
            //Se busca la resolucion con la que se guardo la factura
            if ($vResId["{$vFcoc['residxxx']}~{$vFcoc['resprexx']}~{$vFcoc['restipxx']}"]['resdesxx'] != "") {
              $cPrefijo  = $vResId["{$vFcoc['residxxx']}~{$vFcoc['resprexx']}~{$vFcoc['restipxx']}"]['resprexx'];
              $cLongitud = strlen($vResId["{$vFcoc['residxxx']}~{$vFcoc['resprexx']}~{$vFcoc['restipxx']}"]['resdesxx']);
            } else { //Si no hay registro, se usa la que tiene actualmente el comprobante
              $cPrefijo  = $vResDat["{$xRP['comidxxx']}~{$xRP['comcodxx']}"]['resprexx'];
              $cLongitud = strlen($vResDat["{$xRP['comidxxx']}~{$xRP['comcodxx']}"]['resdesxx']);
            }
            $cFactura = trim((($cPrefijo != "") ? $cPrefijo."-" : "").str_pad($xRP['comcscxx'],$cLongitud,"0",STR_PAD_LEFT));
          }

          switch ($xRP['comctocx']) {
            case 'IP':
              $cNumDo = $xRP['sucidxxx']."~".$xRP['docidxxx']."~".$xRP['docsufxx'];

              /**
               * Buscando Pedido del DO
               */
              $vPedDo = array();
              $qPedDo  = "SELECT docpedxx, doctipxx ";
              $qPedDo .= "FROM $cAlfa.sys00121 ";
              $qPedDo .= "WHERE ";
              $qPedDo .= "sucidxxx = \"{$xRP['sucidxxx']}\" AND ";
              $qPedDo .= "docidxxx = \"{$xRP['docidxxx']}\" AND ";
              $qPedDo .= "docsufxx = \"{$xRP['docsufxx']}\" LIMIT 0,1 ";
              $xPedDo  = f_MySql("SELECT","",$qPedDo,$xConexion01,"");
              // f_Mensaje(__FILE__,__LINE__,$qPedDo."~".mysql_num_rows($xPedDo));
              $vPedDo = mysql_fetch_array($xPedDo);

              $nIP = count($mIP[$cNumDo][$cFactura]);
              $mIP[$cNumDo][$cFactura][$nIP] = $xRP;
              $mIP[$cNumDo]['sucidxxx'] = $xRP['sucidxxx'];
              $mIP[$cNumDo]['docidxxx'] = $xRP['docidxxx'];
              $mIP[$cNumDo]['docsufxx'] = $xRP['docsufxx'];
              $mIP[$cNumDo]['docpedxx'] = $vPedDo['docpedxx'];
              $mIP[$cNumDo]['doctipxx'] = $vPedDo['doctipxx'];
            break;
            default:
              //Buscando el porcentaje de retencion
              $xRP['pucretxx'] = $mCuenta["{$xRP['pucidxxx']}"]['pucretxx'];

              $cNumDo = ($vTramites["{$xRP['sccidxxx']}"] != "") ? $vTramites["{$xRP['sccidxxx']}"] : $cPrimerTramite;

              $nInd_mRetenciones = count($mRetenciones);
              $mRetenciones[$nInd_mRetenciones]['docidxxx'] = $cNumDo;
              $mRetenciones[$nInd_mRetenciones]['facturax'] = $cFactura;
              $mRetenciones[$nInd_mRetenciones]['pucretxx'] = $xRP['pucretxx'];
              $mRetenciones[$nInd_mRetenciones]['comvlr01'] = $xRP['comvlr01'];
              $mRetenciones[$nInd_mRetenciones]['comvlrxx'] = $xRP['comvlrxx'];
              switch ($xRP['comctocx']) {
                case 'RETFTE':
                  $mRetenciones[$nInd_mRetenciones]['retenxxx'] = "Retefuente";
                break;
                case 'RETCRE':
                  $mRetenciones[$nInd_mRetenciones]['retenxxx'] = "ReteCREE";
                break;
                case 'RETIVA':
                  $mRetenciones[$nInd_mRetenciones]['retenxxx'] = "ReteIva";
                break;
                case 'RETICA':
                  $mRetenciones[$nInd_mRetenciones]['retenxxx'] = "ReteIca";
                break;
                case 'ARETFTE':
                  $mRetenciones[$nInd_mRetenciones]['retenxxx'] = "AutoRFte";
                break;
                case 'ARETCRE':
                  $mRetenciones[$nInd_mRetenciones]['retenxxx'] = "AutoRCree";
                break;
                case 'ARETICA':
                  $mRetenciones[$nInd_mRetenciones]['retenxxx'] = "AutoRICA";
                break;
                default:
                  //No hace nada
                break;
              }
            break;
          }## switch ($xRP['comctocx']) { ##
				}## while( $xRP = mysql_fetch_array($xPropios)){ ##
				$mIP[$mRetenciones[0]['docidxxx']][$mRetenciones[0]['facturax']][0]['retencio'] = $mRetenciones;
      }## for ($cAno=$gAnioD; $cAno <= $gAnioH; $cAno++) { ##

			// echo "<h1>IP</h1>";
			// echo'<pre>';
			// print_r($mIP);
			// echo'</pre>';

			//Inicializando varibles de totales
			$nTotFac = 0;
			$nTotCoT = 0;
			$nTotCos = 0;
			$nTotIva = 0;
			$nTotal  = 0;
			
			$nNumCol = 21;
			
			#### PINTA POR EXCEL //Reporte de Ingreos Propios y Pagos a Terceros
			$header  .= 'REPORTE DE INGRESOS PROPIOS Y PAGOS A TERCEROS FACTURADOS'."\n";
			$header  .= "\n";
			$data 		= '';
			$cNomFile = "REPORTE_DE_INGRESOS_PROPIOS_Y_PAGOS_TERCEROS_FACTURADOS_".$_COOKIE['kUsrId'].date("YmdHis").".xls";

			if ($_SERVER["SERVER_PORT"] != "") {
				$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
			} else {
				$cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
			}
	
			if (file_exists($cFile)) {
				unlink($cFile);
			}
	
			$fOp = fopen($cFile, 'a');

			# Tabla reporte de ingrsos propios y pagos a terceros
			$data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">';
			
			# Cabecera:
			$data.= '<tr>';
			$data.= '<td class="name" align="left" colspan="'.$nNumCol.'">';
			$data.= '<font size="3">';
			$data.= '<b>REPORTE DE INGRESOS PROPIOS Y PAGOS A TERCEROS FACTURADOS<br/>';
			$data.= 'PERIODO: DEL '.$gAnioD.' AL '.$gAnioH;
			$data.= '</b>';
			$data.= '</font>';
			$data.= '</td>';
			$data.= '</tr>';
			$data.= '<tr>';
			$data.= '<td class="name" align="left" colspan="'.($nNumCol-4).'">';
			$data.= '<b>Cliente: </b>'.$vCocDat['CLINOMXX'];
			$data.= '</td>';
			$data.= '<td class="name" align="left" colspan="2">';
			$data.= '<b>Nit: </b>'.$gTerId."-".f_Digito_Verificacion($gTerId);
			$data.= '</td>';
			$data.= '<td class="name" align="left" colspan="2">';
			$data.= '<b>Fecha Impresi&oacute;n: </b>'.date('Y-m-d');
			$data.= '</td>';
			$data.= '</tr>';

			# Columnas
			$data.= '<tr>';
			$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>DO</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>FACTURA</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>NO. DOCUMENTO DE TRANSPORTE</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>TERCERO</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="450px" align="center"><b><font color=white>NOMBRE</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>DOCUMENTO</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="100px" align="center"><b><font color=white>FECHA</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="400px" align="center"><b><font color=white>CONCEPTO DE TERCERO</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>COSTO DE TERCERO</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TIPO</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR BASE</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="80px"  align="center"><b><font color=white>%</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="400px" align="center"><b><font color=white>CONCEPTO INGRESO PROPIO</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>COSTO INGRESO PROPIO</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>IVA</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TOTAL</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TIPO</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR BASE</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="80px"  align="center"><b><font color=white>%</font></b></td>';
			$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR</font></b></td>';
			$data.= '</tr>';
							
			// fwrite($fOp, $data);
				
			foreach ($mDatos as $nDatDo => $mDatDo) {
			
				/*** Se agrega un for para recorrer la matriz interna de los pagos propios/Pagos a terceros y se avanza en posiciones con la ayuda de la sentencia next ***/
				for($nDD = 0; $nDD < count($mDatDo); $nDD++ ){
					
					# Obtener el documento de transporte
					switch ($mDatos[$nDatDo][key($mDatDo)][0]['doctipxx']) {
						case 'IMPORTACION':
							$qDocTran = "SELECT dgedtxxx ";
							$qDocTran.= "FROM $cAlfa.SIAI0200 ";
							$qDocTran.= "WHERE ";
							$qDocTran.= "admidxxx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['sucidxxx']}\" AND ";
							$qDocTran.= "doiidxxx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['docidxxx']}\" AND ";
							$qDocTran.= "doisfidx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['docsufxx']}\" LIMIT 0,1";
							$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
							// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
							$vDocTran = mysql_fetch_array($xDocTran);
							$cDocTran = $vDocTran['dgedtxxx'];
						break;
						case 'EXPORTACION':
							$qDocTran = "SELECT dexdtrxx ";
							$qDocTran.= "FROM $cAlfa.siae0199 ";
							$qDocTran.= "WHERE ";
							$qDocTran.= "admidxxx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['sucidxxx']}\" AND ";
							$qDocTran.= "dexidxxx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['docidxxx']}\" LIMIT 0,1";
							$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
							// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
							$vDocTran = mysql_fetch_array($xDocTran);
							$cDocTran = $vDocTran['dexdtrxx'];
						break;
						default:
							$cDocTran = "";
						break;
					}## switch ($vTipoDo['doctipxx']) { ##
					# Calcular el numero de filas
					// echo $nDatDo."~".key($mDatDo)."~".count($mDatos[$nDatDo][key($mDatDo)])."~".count($mIP[$nDatDo][key($mDatDo)])."<br />";
					// echo $mDatos[$nDatDo][key($mDatDo)]."<br />";
					//Verificando cuantos pagos a terceros y cuantos ingresos propios hay para la factura
					if(count($mDatos[$nDatDo][key($mDatDo)]) >= count($mIP[$nDatDo][key($mDatDo)])){
						$nRows = count($mDatos[$nDatDo][key($mDatDo)]);
						$nDat = 0;
					}else{
						$nRows = count($mIP[$nDatDo][key($mDatDo)]);
						$nDat = 1;
					}
		
					for ($i=0; $i < $nRows; $i++) {
		
						if ($nDat == 0) { //Son mas los PCC que los IP, se usan los datos generales de los PCC
							$cDocId = $mDatos[$nDatDo][key($mDatDo)][$i]['docidxxx'];
							if ($cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "ROLDANLO") {
								$cDocId = $mDatos[$nDatDo][key($mDatDo)][$i]['sucidxxx'].'-'.$mDatos[$nDatDo][key($mDatDo)][$i]['docidxxx'].'-'.$mDatos[$nDatDo][key($mDatDo)][$i]['docsufxx'];
							}
							$cFactura = $mDatos[$nDatDo][key($mDatDo)][$i]['facturax'];
						} else { //Son mas los IP que los PCC, se usan los datos generales de los IP
							$cDocId = $mIP[$nDatDo][key($mDatDo)][$i]['docidxxx'];
							if ($cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "ROLDANLO") {
								$cDocId = $mIP[$nDatDo][key($mDatDo)][$i]['sucidxxx'].'-'.$mIP[$nDatDo][key($mDatDo)][$i]['docidxxx'].'-'.$mIP[$nDatDo][key($mDatDo)][$i]['docsufxx'];
							}
							$cFactura = key($mDatDo);
						}
		
						# Linea
						# Calcular numero de rowspans
						$nRow = count($mIP[$nDatDo][key($mDatDo)][$i]['retencio']);
						$cColorPro = "#000000";
						$cColor = "#FFFFFF";
		
						# Columnas
						$data.= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
							$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$cDocId.'</td>'; //DO
							$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$cFactura.'</td>'; //FACTURA
							$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$cDocTran.'</td>'; //NO. DOCUMENTO DE TRANSPORTE
							//Imprimiendo pagos a terceros
							if ($i < count($mDatos[$nDatDo][key($mDatDo)])) {
								$data.= '<td style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['teridxxx'].'</td>'; //TERCERO
								$data.= '<td style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['ternomxx'].'</td>'; //NOMBRE
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['document'].'</td>'; //DOCUMENTO
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['comfecxx'].'</td>'; //FECHA
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['concepto'].'</td>'; //CONCEPTO DE TERCERO
								$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx'],2,',','').'</td>'; //COSTO DE TERCERO
		
								if (count($mDatos[$nDatDo][key($mDatDo)][$i]['retencio']) > 0) {
									//TIPO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
										$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px" >';
											foreach ($mDatos[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
												$data .= "<tr>";
													$data .= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$vValue['retenxxx'].'</td>';
												$data .= "</tr>";
											}
										$data .= '</table>';
									$data.= '</td>';
									//VALOR BASE
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
										$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px">';
											foreach ($mDatos[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
												$data .= "<tr>";
													$data .= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['comvlr01'],2,',','').'</td>';
												$data .= "</tr>";
											}
										$data .= '</table>';
									$data.= '</td>';
									//%
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
										$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="80px">';
											foreach ($mDatos[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
												$data .= "<tr>";
													$data .= '<td align="right"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['pucretxx'],3,',','').'</td>';
												$data .= "</tr>";
											}
										$data .= '</table>';
									$data.= '</td>';
									//VALOR
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
										$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px">';
											foreach ($mDatos[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
												$data .= "<tr>";
													if($vValue['retenxxx'] == 'ReteCree')  { $nTotRCreDat += $vValue['comvlrxx']; }
													if($vValue['retenxxx'] == 'Retefuente'){ $nTotRfteDat += $vValue['comvlrxx']; }
													if($vValue['retenxxx'] == 'ReteIva')   { $nTotRIvaDat += $vValue['comvlrxx']; }
													if($vValue['retenxxx'] == 'ReteIca')   { $nTotRIcaDat += $vValue['comvlrxx']; }
													$data .= '<td align="right"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['comvlrxx'],2,',','').'</td>';
												$data .= "</tr>";
											}
										$data .= '</table>';
									$data.= '</td>';
								} else {
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TIPO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR BASE
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //%
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR
								}
							}else{
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TERCERO
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //NOMBRE
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //DOCUMENTO
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //FECHA
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //CONCEPTO DE TERCERO
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //COSTO DE TERCERO
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TIPO
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR BASE
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //%
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR
							}
		
							if ($i < count($mIP[$nDatDo][key($mDatDo)])) {
								$vConcepto = explode("~", trim($mIP[$nDatDo][key($mDatDo)][$i]['comobsxx'],"|"));
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$vConcepto[2].'</td>'; //CONCEPTO INGRESO PROPIO
								$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mIP[$nDatDo][key($mDatDo)][$i]['comvlrxx'],2,',','').'</td>'; //COSTO INGRESO PROPIO
								$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format(($mIP[$nDatDo][key($mDatDo)][$i]['comvlr01']),2,',','').'</td>'; //IVA
								$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format(($mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']+$mIP[$nDatDo][key($mDatDo)][$i]['comvlrxx']+$mIP[$nDatDo][key($mDatDo)][$i]['comvlr01']),2,',','').'</td>'; //TOTAL
		
								$nTotal  += $mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']+$mIP[$nDatDo][key($mDatDo)][$i]['comvlrxx']+$mIP[$nDatDo][key($mDatDo)][$i]['comvlr01'];
		
								if (count($mIP[$nDatDo][key($mDatDo)][$i]['retencio']) > 0) {
									//TIPO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
										$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px" >';
											foreach ($mIP[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
												$data .= "<tr>";
													$data .= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$vValue['retenxxx'].'</td>';
												$data .= "</tr>";
											}
										$data .= '</table>';
									$data.= '</td>';
									//VALOR BASE
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
										$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px">';
											foreach ($mIP[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
												$data .= "<tr>";
													$data .= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['comvlr01'],2,',','').'</td>';
												$data .= "</tr>";
											}
										$data .= '</table>';
									$data.= '</td>';
									//%
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
										$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="80px">';
											foreach ($mIP[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
												$data .= "<tr>";
													$data .= '<td align="right"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['pucretxx'],3,',','').'</td>';
												$data .= "</tr>";
											}
										$data .= '</table>';
									$data.= '</td>';
									//VALOR
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
										$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px">';
											foreach ($mIP[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
												$data .= "<tr>";
													if($vValue['retenxxx'] == 'ReteCree')  { $nTotRCreIP += $vValue['comvlrxx']; }
													if($vValue['retenxxx'] == 'Retefuente'){ $nTotRfteIP += $vValue['comvlrxx']; }
													if($vValue['retenxxx'] == 'ReteIva')   { $nTotRIvaIP += $vValue['comvlrxx']; }
													if($vValue['retenxxx'] == 'ReteIca')   { $nTotRIcaIP += $vValue['comvlrxx']; }
													$data .= '<td align="right"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['comvlrxx'],2,',','').'</td>';
												$data .= "</tr>";
											}
										$data .= '</table>';
									$data.= '</td>';
								} else {
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TIPO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR BASE
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //%
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR
								}
							} else {
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //CONCEPTO INGRESO PROPIO
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //COSTO INGRESO PROPIO
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //IVA
								$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format(($mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']),2,',','').'</td>'; //TOTAL
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TIPO
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR BASE
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //%
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR
								$nTotal  += $mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx'];
							}
						$data.= '</tr>';
						$nTotCoT += $mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx'];
						$nTotCos += $mIP[$nDatDo][key($mDatDo)][$i]['comvlrxx'];
						$nTotIva += $mIP[$nDatDo][key($mDatDo)][$i]['comvlr01'];
						$data .= '</tr>';
					}## for ($i=0; $i < $nRows; $i++) { ##

					unset($mIP[$nDatDo][key($mDatDo)]);
					// unset($mDatos[$nDatDo]);
					unset($mDatos[$nDatDo][key($mDatDo)]); // se elimina la posicion actual, en vez de todo el vector. 
		
					/*** Se avanza a la siguiente posicion del vector. ***/
					next($mDatDo);
					// echo count($mDatos[$nDatDo][key($mDatDo)])."~".count($mIP[$nDatDo][key($mDatDo)])."<br />";
				}
		
			}## foreach ($mDatos as $nDatDo => $mDatDo) { ##
		
			// echo'<pre>';
			// print_r($mIP);
			// echo'</pre>';
			// echo $data;

			
			#Ingresos Propios Sobrantes
			foreach ($mIP as $nIPDo => $mIPDo) {
				//si solo hay 3 posiciones significa que se ya se pintaron todas las facturas del DO
    
				if (count($mIPDo) > 3) {
					/*** Se agrega un for para recorrer la matriz interna de los pagos propios y se avanza en posiciones con la ayuda de la sentencia next ***/
					for($nIPA = 0; $nIPA < count($mIPDo); $nIPA++ ){
						# Obtener el documento de transporte
						$qTipoDo = "SELECT doctipxx ";
						$qTipoDo.= "FROM $cAlfa.sys00121 ";
						$qTipoDo.= "WHERE ";
						$qTipoDo.= "sucidxxx = \"{$mIPDo['sucidxxx']}\" AND ";
						$qTipoDo.= "docidxxx = \"{$mIPDo['docidxxx']}\" AND ";
						$qTipoDo.= "docsufxx = \"{$mIPDo['docsufxx']}\" LIMIT 0,1;";
						$xTipoDo = f_MySql("SELECT","",$qTipoDo,$xConexion01,"");
						$vTipoDo = mysql_fetch_array($xTipoDo);
						switch ($vTipoDo['doctipxx']) {
							case 'IMPORTACION':
								$qDocTran = "SELECT dgedtxxx ";
								$qDocTran.= "FROM $cAlfa.SIAI0200 ";
								$qDocTran.= "WHERE ";
								$qDocTran.= "admidxxx = \"{$mIPDo['sucidxxx']}\" AND ";
								$qDocTran.= "doiidxxx = \"{$mIPDo['docidxxx']}\" AND ";
								$qDocTran.= "doisfidx = \"{$mIPDo['docsufxx']}\" LIMIT 0,1";
								$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
								$vDocTran = mysql_fetch_array($xDocTran);
								$cDocTran = $vDocTran['dgedtxxx'];
								// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
							break;
							case 'EXPORTACION':
								$qDocTran = "SELECT dexdtrxx ";
								$qDocTran.= "FROM $cAlfa.siae0199 ";
								$qDocTran.= "WHERE ";
								$qDocTran.= "admidxxx = \"{$mIPDo['sucidxxx']}\" AND ";
								$qDocTran.= "dexidxxx = \"{$mIPDo['docidxxx']}\" LIMIT 0,1";
								$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
								// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
								$vDocTran = mysql_fetch_array($xDocTran);
								$cDocTran = $vDocTran['dexdtrxx'];
							break;
							default:
								$cDocTran = "";
							break;
						}## switch ($vTipoDo['doctipxx']) { ##

						foreach ($mIP[$nIPDo][key($mIPDo)] as $i => $cValueIP) {
							# Linea
							# Calcular numeor de rowspans
							$nRow = count($mIP[$nIPDo][key($mIPDo)][$i]['retencio']);

							$cColorPro = "#000000";
							$cColor = "#FFFFFF";

							if (($cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "ROLDANLO") && $mDatos[$i]['docidxxx'] != "") {
								$mIP[$nIPDo][key($mIPDo)][$i]['docidxxx'] = $mIP[$nIPDo][key($mIPDo)][$i]['sucidxxx'].'-'.$mIP[$nIPDo][key($mIPDo)][$i]['docidxxx'].'-'.$mIP[$nIPDo][key($mIPDo)][$i]['docsufxx'];
							}

							$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['docidxxx'].'</td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.key($mIPDo).'</td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$cDocTran.'</td>';
							# Para Terceros
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['teridxxx'].'</td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['ternomxx'].'</td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['comfecxx'].'</td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';

							# Para Ingresos propios
							$vConcepto = explode("~", trim($mIP[$nIPDo][key($mIPDo)][$i]['comobsxx'],"|"));
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$vConcepto[2].'</td>';
							$data .= '<td align="right"  '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.number_format($mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx'],2,',','').'</td>';
							$data .= '<td align="right"  '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.number_format($mIP[$nIPDo][key($mIPDo)][$i]['comvlr01'],2,',','').'</td>';
							$data .= '<td align="right"  '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.number_format(($mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx']+$mIP[$nIPDo][key($mIPDo)][$i]['comvlr01']),2,',','').'</td>';
							if ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] != "") {
								foreach ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] as $nKey => $vValue) {
									if ($nKey != 0) {
										$data .= '<tr>';
									}
									if ($vValue['retenxxx'] != "") {
										$data .= '<td align="left"   style = "color:'.$cColorPro.'">'.$vValue['retenxxx'].'</td>';
										$data .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($vValue['comvlr01'],2,',','').'</td>';
										$data .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($vValue['pucretxx'],3,',','').'</td>';
										if($vValue['retenxxx'] == 'ReteCree')  { $nTotRCreIP += $vValue['comvlrxx']; }
										if($vValue['retenxxx'] == 'Retefuente'){ $nTotRfteIP += $vValue['comvlrxx']; }
										if($vValue['retenxxx'] == 'ReteIva')   { $nTotRIvaIP += $vValue['comvlrxx']; }
										if($vValue['retenxxx'] == 'ReteIca')   { $nTotRIcaIP += $vValue['comvlrxx']; }
										$data .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($vValue['comvlrxx'],2,',','').'</td>';
										$data .= '</tr>';
									}else{
										$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
										$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
										$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
										$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
									}## if ($vValue['retenxxx'] != "") { ##
								}## foreach ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] as $nKey => $vValue) { ##
							}else{
								$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
							}## if ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] != "") { ##
							$nTotCos += $mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx'];
							$nTotIva += $mIP[$nIPDo][key($mIPDo)][$i]['comvlr01'];
							$nTotal  += $mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx'] + $mIP[$nIPDo][key($mIPDo)][$i]['comvlr01'];
							$data .= '</tr>';
						}## for ($i=0; $i < $nRows; $i++) { ##

						/*** Se avanza a la siguiente posicion del vector. ***/
						next($mIPDo); 
					}
				}
  		}## foreach ($mIP as $nIPDo => $mIPDo) { ##
			
			$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
			$data .= '<td align="left"   colspan="'.($nNumCol-13).'" style="background-color:#0B610B"><b><font color=white>TOTAL FACTURAS</td>';
			$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotCoT,2,',','').'</font></b></td>';
			$data .= '<td align="left"   colspan="'.($nNumCol-16).'"  style="background-color:#0B610B"><b><font color=white></font></b></td>';
			$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotCos,2,',','').'</font></b></td>';
			$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotIva,2,',','').'</font></b></td>';
			$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotal,2,',','').'</font></b></td>';
			$data .= '<td align="right"  colspan="4" style="background-color:#0B610B"></td>';
			$data .= '</tr>';

			$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
			$data .= '<td align="center" colspan="'.$nNumCol.'" style = "color:'.$cColorPro.'"><b><font color=white></td>';
			$data .= '</tr>';

			if($nTotRfteDat != 0 || $nTotRfteIP != 0){
				$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
				$data .= '<td align="left" colspan="'.($nNumCol-1).'"  style = "color:'.$cColorPro.'"><b>TOTAL RETENCION EN LA FUENTE</b></td>';
				$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRfteDat + $nTotRfteIP,2,',','').'</b></td>';
				$data .= '</tr>';
			}

			if($nTotRCreDat != 0 || $nTotRCreIP != 0){
				$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
				$data .= '<td align="left" colspan="'.($nNumCol-1).'"  style = "color:'.$cColorPro.'"><b>TOTAL RETENCION CREE</b></td>';
				$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRCreDat + $nTotRCreIP,2,',','').'</b></td>';
				$data .= '</tr>';
			}

			if($nTotRIvaDat != 0 || $nTotRIvaIP != 0){
				$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
				$data .= '<td align="left" colspan="'.($nNumCol-1).'" style = "color:'.$cColorPro.'"><b>TOTAL RETENCION IVA</b></td>';
				$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRIvaDat +$nTotRIvaIP,2,',','').'</b></td>';
				$data .= '</tr>';
			}

			if($nTotRIcaDat != 0 || $nTotRIcaIP != 0){
				$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
				$data .= '<td align="left" colspan="'.($nNumCol-1).'" style = "color:'.$cColorPro.'"><b>TOTAL RETENCION ICA</b></td>';
				$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRIcaDat + $nTotRIcaIP,2,',','').'</b></td>';
			$data .= '</tr>';
			}

			# Final Reporte
			$data .= '</table>';
			fwrite($fOp, $data);
			// Fin Generarion de Reporte de Facturas

			##############################################################################################
																		// NOTAS DE CREDITO //
			###############################################################################################
			// Inicio reporte nota de credito
			$vDatos = array();
			$vDatos['cTipCom']   = "NC";     // Tipo comprobante
			$vDatos['cNit']      = $gTerId;   // Tercero
			$vDatos['dFecIni']   = $gAnioD;  	// Fecha desde
			$vDatos['dFecFin']   = $gAnioH;   // Fecha Hasta
			$mReturnGenerarReporteNotas = fnGenerarReporteNotas($vDatos);
			if($mReturnGenerarReporteNotas[0] == "false") {
				$nSwitch = 1;
				$cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
				for($nR=1;$nR<count($mReturnGenerarReporteNotas);$nR++){
					$cMsj .= $mReturnGenerarReporteNotas[$nR]."\n";
				}
			}else{
				$mDatos = $mReturnGenerarReporteNotas[1];
				$mIP = $mReturnGenerarReporteNotas[2];
			}


				// echo'<pre>';
				// print_r($mDatos);
				// echo'</pre>';
				// echo $data;

			if($nSwitch == 0 && (count($mDatos) > 0 || count($mIP) > 0)) {
				//Inicializando varibles de totales
				$nTotFac = 0;
				$nTotCotC = 0;
				$nTotCosC = 0;
				$nTotIvaC = 0;
				$nTotalC  = 0;

				$nTotRCreDat = 0;
				$nTotRfteDat = 0;
				$nTotRIvaDat = 0;
				$nTotRIcaDat = 0;

				$nTotRCreIP = 0;
				$nTotRfteIP = 0;
				$nTotRIvaIP = 0;
				$nTotRIcaIP = 0;
				
				$nNumCol = 21;
			
				#### PINTA POR EXCEL //Reporte de Ingreos Propios y Pagos a Terceros
				$data 		= '';

				# Tabla reporte de ingrsos propios y pagos a terceros
				$data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">';

				# Columnas
				$data.= '<tr>';
				$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>DO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>NOTA CREDITO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>NO. DOCUMENTO DE TRANSPORTE</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>TERCERO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="450px" align="center"><b><font color=white>NOMBRE</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>DOCUMENTO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="100px" align="center"><b><font color=white>FECHA</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="400px" align="center"><b><font color=white>CONCEPTO DE TERCERO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>COSTO DE TERCERO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TIPO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR BASE</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="80px"  align="center"><b><font color=white>%</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="400px" align="center"><b><font color=white>CONCEPTO INGRESO PROPIO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>COSTO INGRESO PROPIO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>IVA</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TOTAL</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TIPO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR BASE</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="80px"  align="center"><b><font color=white>%</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR</font></b></td>';
				$data.= '</tr>';

				foreach ($mDatos as $nDatDo => $mDatDo) {
					/*** Se agrega un for para recorrer la matriz interna de los pagos propios/Pagos a terceros y se avanza en posiciones con la ayuda de la sentencia next ***/
					for($nDD = 0; $nDD < count($mDatDo); $nDD++ ){
						
						# Obtener el documento de transporte
						switch ($mDatos[$nDatDo][key($mDatDo)][0]['doctipxx']) {
							case 'IMPORTACION':
								$qDocTran = "SELECT dgedtxxx ";
								$qDocTran.= "FROM $cAlfa.SIAI0200 ";
								$qDocTran.= "WHERE ";
								$qDocTran.= "admidxxx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['sucidxxx']}\" AND ";
								$qDocTran.= "doiidxxx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['docidxxx']}\" AND ";
								$qDocTran.= "doisfidx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['docsufxx']}\" LIMIT 0,1";
								$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
								// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
								$vDocTran = mysql_fetch_array($xDocTran);
								$cDocTran = $vDocTran['dgedtxxx'];
							break;
							case 'EXPORTACION':
								$qDocTran = "SELECT dexdtrxx ";
								$qDocTran.= "FROM $cAlfa.siae0199 ";
								$qDocTran.= "WHERE ";
								$qDocTran.= "admidxxx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['sucidxxx']}\" AND ";
								$qDocTran.= "dexidxxx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['docidxxx']}\" LIMIT 0,1";
								$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
								// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
								$vDocTran = mysql_fetch_array($xDocTran);
								$cDocTran = $vDocTran['dexdtrxx'];
							break;
							default:
								$cDocTran = "";
							break;
						}## switch ($vTipoDo['doctipxx']) { ##
						# Calcular el numero de filas
						// echo $nDatDo."~".key($mDatDo)."~".count($mDatos[$nDatDo][key($mDatDo)])."~".count($mIP[$nDatDo][key($mDatDo)])."<br />";
						// echo $mDatos[$nDatDo][key($mDatDo)]."<br />";
						//Verificando cuantos pagos a terceros y cuantos ingresos propios hay para la factura
						if(count($mDatos[$nDatDo][key($mDatDo)]) >= count($mIP[$nDatDo][key($mDatDo)])){
							$nRows = count($mDatos[$nDatDo][key($mDatDo)]);
							$nDat = 0;
						}else{
							$nRows = count($mIP[$nDatDo][key($mDatDo)]);
							$nDat = 1;
						}
			
						for ($i=0; $i < $nRows; $i++) {
			
							if ($nDat == 0) { //Son mas los PCC que los IP, se usan los datos generales de los PCC
								$cDocId = $mDatos[$nDatDo][key($mDatDo)][$i]['docidxxx'];
								if ($cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "ROLDANLO") {
									$cDocId = $mDatos[$nDatDo][key($mDatDo)][$i]['sucidxxx'].'-'.$mDatos[$nDatDo][key($mDatDo)][$i]['docidxxx'].'-'.$mDatos[$nDatDo][key($mDatDo)][$i]['docsufxx'];
								}
								$cFactura = $mDatos[$nDatDo][key($mDatDo)][$i]['facturax'];
							} else { //Son mas los IP que los PCC, se usan los datos generales de los IP
								$cDocId = $mIP[$nDatDo][key($mDatDo)][$i]['docidxxx'];
								if ($cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "ROLDANLO") {
									$cDocId = $mIP[$nDatDo][key($mDatDo)][$i]['sucidxxx'].'-'.$mIP[$nDatDo][key($mDatDo)][$i]['docidxxx'].'-'.$mIP[$nDatDo][key($mDatDo)][$i]['docsufxx'];
								}
								$cFactura = key($mDatDo);
							}
			
							# Linea
							# Calcular numero de rowspans
							$nRow = count($mIP[$nDatDo][key($mDatDo)][$i]['retencio']);
							$cColorPro = "#000000";
							$cColor = "#FFFFFF";
			
							# Columnas
							$data.= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$cDocId.'</td>'; //DO
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$cFactura.'</td>'; //FACTURA
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$cDocTran.'</td>'; //NO. DOCUMENTO DE TRANSPORTE
								//Imprimiendo pagos a terceros
								if ($i < count($mDatos[$nDatDo][key($mDatDo)])) {
									$data.= '<td style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['teridxxx'].'</td>'; //TERCERO
									$data.= '<td style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['ternomxx'].'</td>'; //NOMBRE
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['document'].'</td>'; //DOCUMENTO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['comfecxx'].'</td>'; //FECHA
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['concepto'].'</td>'; //CONCEPTO DE TERCERO
									$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx'],2,',','').'</td>'; //COSTO DE TERCERO
			
									if (count($mDatos[$nDatDo][key($mDatDo)][$i]['retencio']) > 0) {
										//TIPO
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px" >';
												foreach ($mDatos[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														$data .= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$vValue['retenxxx'].'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
										//VALOR BASE
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px">';
												foreach ($mDatos[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														$data .= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['comvlr01'],2,',','').'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
										//%
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="80px">';
												foreach ($mDatos[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														$data .= '<td align="right"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['pucretxx'],3,',','').'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
										//VALOR
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px">';
												foreach ($mDatos[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														if($vValue['retenxxx'] == 'ReteCree')  { $nTotRCreDat += $vValue['comvlrxx']; }
														if($vValue['retenxxx'] == 'Retefuente'){ $nTotRfteDat += $vValue['comvlrxx']; }
														if($vValue['retenxxx'] == 'ReteIva')   { $nTotRIvaDat += $vValue['comvlrxx']; }
														if($vValue['retenxxx'] == 'ReteIca')   { $nTotRIcaDat += $vValue['comvlrxx']; }
														$data .= '<td align="right"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['comvlrxx'],2,',','').'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
									} else {
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TIPO
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR BASE
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //%
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR
									}
								}else{
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TERCERO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //NOMBRE
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //DOCUMENTO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //FECHA
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //CONCEPTO DE TERCERO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //COSTO DE TERCERO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TIPO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR BASE
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //%
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR
								}
			
								if ($i < count($mIP[$nDatDo][key($mDatDo)])) {
									$vConcepto = explode("~", trim($mIP[$nDatDo][key($mDatDo)][$i]['comobsxx'],"|"));
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$vConcepto[2].'</td>'; //CONCEPTO INGRESO PROPIO
									$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mIP[$nDatDo][key($mDatDo)][$i]['comvlrxx'],2,',','').'</td>'; //COSTO INGRESO PROPIO
									$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format(($mIP[$nDatDo][key($mDatDo)][$i]['comvlr01']),2,',','').'</td>'; //IVA
									$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format(($mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']+$mIP[$nDatDo][key($mDatDo)][$i]['comvlrxx']+$mIP[$nDatDo][key($mDatDo)][$i]['comvlr01']),2,',','').'</td>'; //TOTAL
			
									$nTotalC  += $mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']+$mIP[$nDatDo][key($mDatDo)][$i]['comvlrxx']+$mIP[$nDatDo][key($mDatDo)][$i]['comvlr01'];
			
									if (count($mIP[$nDatDo][key($mDatDo)][$i]['retencio']) > 0) {
										//TIPO
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px" >';
												foreach ($mIP[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														$data .= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$vValue['retenxxx'].'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
										//VALOR BASE
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px">';
												foreach ($mIP[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														$data .= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['comvlr01'],2,',','').'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
										//%
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="80px">';
												foreach ($mIP[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														$data .= '<td align="right"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['pucretxx'],3,',','').'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
										//VALOR
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px">';
												foreach ($mIP[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														if($vValue['retenxxx'] == 'ReteCree')  { $nTotRCreIP += $vValue['comvlrxx']; }
														if($vValue['retenxxx'] == 'Retefuente'){ $nTotRfteIP += $vValue['comvlrxx']; }
														if($vValue['retenxxx'] == 'ReteIva')   { $nTotRIvaIP += $vValue['comvlrxx']; }
														if($vValue['retenxxx'] == 'ReteIca')   { $nTotRIcaIP += $vValue['comvlrxx']; }
														$data .= '<td align="right"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['comvlrxx'],2,',','').'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
									} else {
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TIPO
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR BASE
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //%
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR
									}
								} else {
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //CONCEPTO INGRESO PROPIO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //COSTO INGRESO PROPIO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //IVA
									$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format(($mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']),2,',','').'</td>'; //TOTAL
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TIPO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR BASE
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //%
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR
									$$nTotalC  += $mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx'];
								}
							$data.= '</tr>';
							$nTotCotC += $mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx'];
							$nTotCosC += $mIP[$nDatDo][key($mDatDo)][$i]['comvlrxx'];
							$nTotIvaC += $mIP[$nDatDo][key($mDatDo)][$i]['comvlr01'];
							$data .= '</tr>';
						}## for ($i=0; $i < $nRows; $i++) { ##
			
			
						unset($mIP[$nDatDo][key($mDatDo)]);
						// unset($mDatos[$nDatDo]);
						unset($mDatos[$nDatDo][key($mDatDo)]); // se elimina la posicion actual, en vez de todo el vector. 
			
						/*** Se avanza a la siguiente posicion del vector. ***/
						next($mDatDo);
						// echo count($mDatos[$nDatDo][key($mDatDo)])."~".count($mIP[$nDatDo][key($mDatDo)])."<br />";
					}
				}## foreach ($mDatos as $nDatDo => $mDatDo) { ##
		
				// echo'<pre>';
				// print_r($mIP);
				// echo'</pre>';
				// echo $data;
			
				#Ingresos Propios Sobrantes
				foreach ($mIP as $nIPDo => $mIPDo) {
					//si solo hay 3 posiciones significa que se ya se pintaron todas las facturas del DO
			
					if (count($mIPDo) > 3) {
						/*** Se agrega un for para recorrer la matriz interna de los pagos propios y se avanza en posiciones con la ayuda de la sentencia next ***/
						for($nIPA = 0; $nIPA < count($mIPDo); $nIPA++ ){
							# Obtener el documento de transporte
							$qTipoDo = "SELECT doctipxx ";
							$qTipoDo.= "FROM $cAlfa.sys00121 ";
							$qTipoDo.= "WHERE ";
							$qTipoDo.= "sucidxxx = \"{$mIPDo['sucidxxx']}\" AND ";
							$qTipoDo.= "docidxxx = \"{$mIPDo['docidxxx']}\" AND ";
							$qTipoDo.= "docsufxx = \"{$mIPDo['docsufxx']}\" LIMIT 0,1;";
							$xTipoDo = f_MySql("SELECT","",$qTipoDo,$xConexion01,"");
							$vTipoDo = mysql_fetch_array($xTipoDo);
							switch ($vTipoDo['doctipxx']) {
								case 'IMPORTACION':
									$qDocTran = "SELECT dgedtxxx ";
									$qDocTran.= "FROM $cAlfa.SIAI0200 ";
									$qDocTran.= "WHERE ";
									$qDocTran.= "admidxxx = \"{$mIPDo['sucidxxx']}\" AND ";
									$qDocTran.= "doiidxxx = \"{$mIPDo['docidxxx']}\" AND ";
									$qDocTran.= "doisfidx = \"{$mIPDo['docsufxx']}\" LIMIT 0,1";
									$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
									$vDocTran = mysql_fetch_array($xDocTran);
									$cDocTran = $vDocTran['dgedtxxx'];
									// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
								break;
								case 'EXPORTACION':
									$qDocTran = "SELECT dexdtrxx ";
									$qDocTran.= "FROM $cAlfa.siae0199 ";
									$qDocTran.= "WHERE ";
									$qDocTran.= "admidxxx = \"{$mIPDo['sucidxxx']}\" AND ";
									$qDocTran.= "dexidxxx = \"{$mIPDo['docidxxx']}\" LIMIT 0,1";
									$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
									// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
									$vDocTran = mysql_fetch_array($xDocTran);
									$cDocTran = $vDocTran['dexdtrxx'];
								break;
								default:
									$cDocTran = "";
								break;
							}## switch ($vTipoDo['doctipxx']) { ##

							foreach ($mIP[$nIPDo][key($mIPDo)] as $i => $cValueIP) {
								# Linea
								# Calcular numeor de rowspans
								$nRow = count($mIP[$nIPDo][key($mIPDo)][$i]['retencio']);

								$cColorPro = "#000000";
								$cColor = "#FFFFFF";

								if (($cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "ROLDANLO") && $mDatos[$i]['docidxxx'] != "") {
									$mIP[$nIPDo][key($mIPDo)][$i]['docidxxx'] = $mIP[$nIPDo][key($mIPDo)][$i]['sucidxxx'].'-'.$mIP[$nIPDo][key($mIPDo)][$i]['docidxxx'].'-'.$mIP[$nIPDo][key($mIPDo)][$i]['docsufxx'];
								}

								$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['docidxxx'].'</td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.key($mIPDo).'</td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$cDocTran.'</td>';
								# Para Terceros
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['teridxxx'].'</td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['ternomxx'].'</td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['comfecxx'].'</td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';

								# Para Ingresos propios
								$vConcepto = explode("~", trim($mIP[$nIPDo][key($mIPDo)][$i]['comobsxx'],"|"));
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$vConcepto[2].'</td>';
								$data .= '<td align="right"  '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.number_format($mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx'],2,',','').'</td>';
								$data .= '<td align="right"  '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.number_format($mIP[$nIPDo][key($mIPDo)][$i]['comvlr01'],2,',','').'</td>';
								$data .= '<td align="right"  '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.number_format(($mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx']+$mIP[$nIPDo][key($mIPDo)][$i]['comvlr01']),2,',','').'</td>';
								if ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] != "") {
									foreach ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] as $nKey => $vValue) {
										if ($nKey != 0) {
											$data .= '<tr>';
										}
										if ($vValue['retenxxx'] != "") {
											$data .= '<td align="left"   style = "color:'.$cColorPro.'">'.$vValue['retenxxx'].'</td>';
											$data .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($vValue['comvlr01'],2,',','').'</td>';
											$data .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($vValue['pucretxx'],3,',','').'</td>';
											if($vValue['retenxxx'] == 'ReteCree')  { $nTotRCreIP += $vValue['comvlrxx']; }
											if($vValue['retenxxx'] == 'Retefuente'){ $nTotRfteIP += $vValue['comvlrxx']; }
											if($vValue['retenxxx'] == 'ReteIva')   { $nTotRIvaIP += $vValue['comvlrxx']; }
											if($vValue['retenxxx'] == 'ReteIca')   { $nTotRIcaIP += $vValue['comvlrxx']; }
											$data .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($vValue['comvlrxx'],2,',','').'</td>';
											$data .= '</tr>';
										}else{
											$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
											$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
											$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
											$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
										}## if ($vValue['retenxxx'] != "") { ##
									}## foreach ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] as $nKey => $vValue) { ##
								}else{
									$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
									$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
									$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
									$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
								}## if ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] != "") { ##
								$nTotCosC += $mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx'];
								$nTotIvaC += $mIP[$nIPDo][key($mIPDo)][$i]['comvlr01'];
								$nTotalC  += $mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx'] + $mIP[$nIPDo][key($mIPDo)][$i]['comvlr01'];
								$data .= '</tr>';
							}## for ($i=0; $i < $nRows; $i++) { ##

							/*** Se avanza a la siguiente posicion del vector. ***/
							next($mIPDo); 
						}
					}
				}## foreach ($mIP as $nIPDo => $mIPDo) { ##
			
				$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
				$data .= '<td align="left"   colspan="'.($nNumCol-13).'" style="background-color:#0B610B"><b><font color=white>TOTAL NOTAS CREDITO</td>';
				$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotCotC,2,',','').'</font></b></td>';
				$data .= '<td align="left"   colspan="'.($nNumCol-16).'"  style="background-color:#0B610B"><b><font color=white></font></b></td>';
				$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotCosC,2,',','').'</font></b></td>';
				$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotIvaC,2,',','').'</font></b></td>';
				$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotalC,2,',','').'</font></b></td>';
				$data .= '<td align="right"  colspan="4" style="background-color:#0B610B"></td>';
				$data .= '</tr>';

				$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
				$data .= '<td align="center" colspan="'.$nNumCol.'" style = "color:'.$cColorPro.'"><b><font color=white></td>';
				$data .= '</tr>';

				if($nTotRfteDat != 0 || $nTotRfteIP != 0){
					$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
					$data .= '<td align="left" colspan="'.($nNumCol-1).'"  style = "color:'.$cColorPro.'"><b>TOTAL RETENCION EN LA FUENTE</b></td>';
					$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRfteDat + $nTotRfteIP,2,',','').'</b></td>';
					$data .= '</tr>';
				}

				if($nTotRCreDat != 0 || $nTotRCreIP != 0){
					$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
					$data .= '<td align="left" colspan="'.($nNumCol-1).'"  style = "color:'.$cColorPro.'"><b>TOTAL RETENCION CREE</b></td>';
					$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRCreDat + $nTotRCreIP,2,',','').'</b></td>';
					$data .= '</tr>';
				}

				if($nTotRIvaDat != 0 || $nTotRIvaIP != 0){
					$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
					$data .= '<td align="left" colspan="'.($nNumCol-1).'" style = "color:'.$cColorPro.'"><b>TOTAL RETENCION IVA</b></td>';
					$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRIvaDat +$nTotRIvaIP,2,',','').'</b></td>';
					$data .= '</tr>';
				}

				if($nTotRIcaDat != 0 || $nTotRIcaIP != 0){
					$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
					$data .= '<td align="left" colspan="'.($nNumCol-1).'" style = "color:'.$cColorPro.'"><b>TOTAL RETENCION ICA</b></td>';
					$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRIcaDat + $nTotRIcaIP,2,',','').'</b></td>';
				$data .= '</tr>';
				}

				# Final Reporte
				$data .= '</table>';
			
				fwrite($fOp, $data);
			}
			// Fin Generarion de de Nota de Credito

			##############################################################################################
																		// NOTAS DE DEBITO //
			###############################################################################################
			// Inicio reporte nota de credito
			$vDatos = array();
			$vDatos['cTipCom']   = "ND";      // Tipo comprobante
			$vDatos['cNit']      = $gTerId;   // Tercero
			$vDatos['dFecIni']   = $gAnioD;  	// Fecha desde
			$vDatos['dFecFin']   = $gAnioH;   // Fecha Hasta
			$mReturnGenerarReporteNotas = fnGenerarReporteNotas($vDatos);
			if($mReturnGenerarReporteNotas[0] == "false") {
				$nSwitch = 1;
				$cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
				for($nR=1;$nR<count($mReturnGenerarReporteNotas);$nR++){
					$cMsj .= $mReturnGenerarReporteNotas[$nR]."\n";
				}
			}else{
				$mDatos = $mReturnGenerarReporteNotas[1];
				$mIP = $mReturnGenerarReporteNotas[2];
			}

		  // echo'<pre>';
      // print_r($mDatos);
      // echo'</pre>';

			if($nSwitch == 0 && (count($mDatos) > 0 || count($mIP) > 0)) {
				//Inicializando varibles de totales
				$nTotFac = 0;
				$nTotCotD = 0;
				$nTotCosD = 0;
				$nTotIvaD = 0;
				$nTotalD  = 0;

				$nTotRCreDat = 0;
				$nTotRfteDat = 0;
				$nTotRIvaDat = 0;
				$nTotRIcaDat = 0;

				$nTotRCreIP = 0;
				$nTotRfteIP = 0;
				$nTotRIvaIP = 0;
				$nTotRIcaIP = 0;
				
				$nNumCol = 21;
			
				#### PINTA POR EXCEL //Reporte de Ingreos Propios y Pagos a Terceros
				$data 		= '';

				# Tabla reporte de ingrsos propios y pagos a terceros
				$data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">';

				# Columnas
				$data.= '<tr>';
				$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>DO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>NOTA DEBITO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>NO. DOCUMENTO DE TRANSPORTE</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>TERCERO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="450px" align="center"><b><font color=white>NOMBRE</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>DOCUMENTO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="100px" align="center"><b><font color=white>FECHA</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="400px" align="center"><b><font color=white>CONCEPTO DE TERCERO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>COSTO DE TERCERO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TIPO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR BASE</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="80px"  align="center"><b><font color=white>%</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="400px" align="center"><b><font color=white>CONCEPTO INGRESO PROPIO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>COSTO INGRESO PROPIO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>IVA</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TOTAL</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TIPO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR BASE</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="80px"  align="center"><b><font color=white>%</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR</font></b></td>';
				$data.= '</tr>';

				foreach ($mDatos as $nDatDo => $mDatDo) {
					/*** Se agrega un for para recorrer la matriz interna de los pagos propios/Pagos a terceros y se avanza en posiciones con la ayuda de la sentencia next ***/
					for($nDD = 0; $nDD < count($mDatDo); $nDD++ ){
						
						# Obtener el documento de transporte
						switch ($mDatos[$nDatDo][key($mDatDo)][0]['doctipxx']) {
							case 'IMPORTACION':
								$qDocTran = "SELECT dgedtxxx ";
								$qDocTran.= "FROM $cAlfa.SIAI0200 ";
								$qDocTran.= "WHERE ";
								$qDocTran.= "admidxxx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['sucidxxx']}\" AND ";
								$qDocTran.= "doiidxxx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['docidxxx']}\" AND ";
								$qDocTran.= "doisfidx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['docsufxx']}\" LIMIT 0,1";
								$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
								// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
								$vDocTran = mysql_fetch_array($xDocTran);
								$cDocTran = $vDocTran['dgedtxxx'];
							break;
							case 'EXPORTACION':
								$qDocTran = "SELECT dexdtrxx ";
								$qDocTran.= "FROM $cAlfa.siae0199 ";
								$qDocTran.= "WHERE ";
								$qDocTran.= "admidxxx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['sucidxxx']}\" AND ";
								$qDocTran.= "dexidxxx = \"{$mDatos[$nDatDo][key($mDatDo)][0]['docidxxx']}\" LIMIT 0,1";
								$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
								// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
								$vDocTran = mysql_fetch_array($xDocTran);
								$cDocTran = $vDocTran['dexdtrxx'];
							break;
							default:
								$cDocTran = "";
							break;
						}## switch ($vTipoDo['doctipxx']) { ##
						# Calcular el numero de filas
						// echo $nDatDo."~".key($mDatDo)."~".count($mDatos[$nDatDo][key($mDatDo)])."~".count($mIP[$nDatDo][key($mDatDo)])."<br />";
						// echo $mDatos[$nDatDo][key($mDatDo)]."<br />";
						//Verificando cuantos pagos a terceros y cuantos ingresos propios hay para la factura
						if(count($mDatos[$nDatDo][key($mDatDo)]) >= count($mIP[$nDatDo][key($mDatDo)])){
							$nRows = count($mDatos[$nDatDo][key($mDatDo)]);
							$nDat = 0;
						}else{
							$nRows = count($mIP[$nDatDo][key($mDatDo)]);
							$nDat = 1;
						}
			
						for ($i=0; $i < $nRows; $i++) {
			
							if ($nDat == 0) { //Son mas los PCC que los IP, se usan los datos generales de los PCC
								$cDocId = $mDatos[$nDatDo][key($mDatDo)][$i]['docidxxx'];
								if ($cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "ROLDANLO") {
									$cDocId = $mDatos[$nDatDo][key($mDatDo)][$i]['sucidxxx'].'-'.$mDatos[$nDatDo][key($mDatDo)][$i]['docidxxx'].'-'.$mDatos[$nDatDo][key($mDatDo)][$i]['docsufxx'];
								}
								$cFactura = $mDatos[$nDatDo][key($mDatDo)][$i]['facturax'];
							} else { //Son mas los IP que los PCC, se usan los datos generales de los IP
								$cDocId = $mIP[$nDatDo][key($mDatDo)][$i]['docidxxx'];
								if ($cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "ROLDANLO") {
									$cDocId = $mIP[$nDatDo][key($mDatDo)][$i]['sucidxxx'].'-'.$mIP[$nDatDo][key($mDatDo)][$i]['docidxxx'].'-'.$mIP[$nDatDo][key($mDatDo)][$i]['docsufxx'];
								}
								$cFactura = key($mDatDo);
							}
			
							# Linea
							# Calcular numero de rowspans
							$nRow = count($mIP[$nDatDo][key($mDatDo)][$i]['retencio']);
							$cColorPro = "#000000";
							$cColor = "#FFFFFF";
			
							# Columnas
							$data.= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$cDocId.'</td>'; //DO
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$cFactura.'</td>'; //FACTURA
								$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$cDocTran.'</td>'; //NO. DOCUMENTO DE TRANSPORTE
								//Imprimiendo pagos a terceros
								if ($i < count($mDatos[$nDatDo][key($mDatDo)])) {
									$data.= '<td style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['teridxxx'].'</td>'; //TERCERO
									$data.= '<td style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['ternomxx'].'</td>'; //NOMBRE
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['document'].'</td>'; //DOCUMENTO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['comfecxx'].'</td>'; //FECHA
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$mDatos[$nDatDo][key($mDatDo)][$i]['concepto'].'</td>'; //CONCEPTO DE TERCERO
									$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx'],2,',','').'</td>'; //COSTO DE TERCERO
			
									if (count($mDatos[$nDatDo][key($mDatDo)][$i]['retencio']) > 0) {
										//TIPO
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px" >';
												foreach ($mDatos[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														$data .= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$vValue['retenxxx'].'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
										//VALOR BASE
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px">';
												foreach ($mDatos[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														$data .= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['comvlr01'],2,',','').'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
										//%
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="80px">';
												foreach ($mDatos[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														$data .= '<td align="right"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['pucretxx'],3,',','').'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
										//VALOR
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px">';
												foreach ($mDatos[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														if($vValue['retenxxx'] == 'ReteCree')  { $nTotRCreDat += $vValue['comvlrxx']; }
														if($vValue['retenxxx'] == 'Retefuente'){ $nTotRfteDat += $vValue['comvlrxx']; }
														if($vValue['retenxxx'] == 'ReteIva')   { $nTotRIvaDat += $vValue['comvlrxx']; }
														if($vValue['retenxxx'] == 'ReteIca')   { $nTotRIcaDat += $vValue['comvlrxx']; }
														$data .= '<td align="right"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['comvlrxx'],2,',','').'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
									} else {
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TIPO
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR BASE
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //%
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR
									}
								}else{
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TERCERO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //NOMBRE
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //DOCUMENTO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //FECHA
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //CONCEPTO DE TERCERO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //COSTO DE TERCERO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TIPO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR BASE
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //%
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR
								}
			
								if ($i < count($mIP[$nDatDo][key($mDatDo)])) {
									$vConcepto = explode("~", trim($mIP[$nDatDo][key($mDatDo)][$i]['comobsxx'],"|"));
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$vConcepto[2].'</td>'; //CONCEPTO INGRESO PROPIO
									$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($mIP[$nDatDo][key($mDatDo)][$i]['comvlrxx'],2,',','').'</td>'; //COSTO INGRESO PROPIO
									$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format(($mIP[$nDatDo][key($mDatDo)][$i]['comvlr01']),2,',','').'</td>'; //IVA
									$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format(($mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']+$mIP[$nDatDo][key($mDatDo)][$i]['comvlrxx']+$mIP[$nDatDo][key($mDatDo)][$i]['comvlr01']),2,',','').'</td>'; //TOTAL
			
									$nTotalD  += $mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']+$mIP[$nDatDo][key($mDatDo)][$i]['comvlrxx']+$mIP[$nDatDo][key($mDatDo)][$i]['comvlr01'];
			
									if (count($mIP[$nDatDo][key($mDatDo)][$i]['retencio']) > 0) {
										//TIPO
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px" >';
												foreach ($mIP[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														$data .= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">'.$vValue['retenxxx'].'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
										//VALOR BASE
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px">';
												foreach ($mIP[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														$data .= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['comvlr01'],2,',','').'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
										//%
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="80px">';
												foreach ($mIP[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														$data .= '<td align="right"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['pucretxx'],3,',','').'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
										//VALOR
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'">';
											$data .= '<table border="1" cellspacing="0" cellpadding="10" align="center" width="150px">';
												foreach ($mIP[$nDatDo][key($mDatDo)][$i]['retencio'] as $nKey => $vValue) {
													$data .= "<tr>";
														if($vValue['retenxxx'] == 'ReteCree')  { $nTotRCreIP += $vValue['comvlrxx']; }
														if($vValue['retenxxx'] == 'Retefuente'){ $nTotRfteIP += $vValue['comvlrxx']; }
														if($vValue['retenxxx'] == 'ReteIva')   { $nTotRIvaIP += $vValue['comvlrxx']; }
														if($vValue['retenxxx'] == 'ReteIca')   { $nTotRIcaIP += $vValue['comvlrxx']; }
														$data .= '<td align="right"   style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format($vValue['comvlrxx'],2,',','').'</td>';
													$data .= "</tr>";
												}
											$data .= '</table>';
										$data.= '</td>';
									} else {
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TIPO
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR BASE
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //%
										$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR
									}
								} else {
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //CONCEPTO INGRESO PROPIO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //COSTO INGRESO PROPIO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //IVA
									$data.= '<td align="right" style = "vertical-align: text-top;color:'.$cColorPro.'">'.number_format(($mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']),2,',','').'</td>'; //TOTAL
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //TIPO
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR BASE
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //%
									$data.= '<td align="left" style = "vertical-align: text-top;color:'.$cColorPro.'"></td>'; //VALOR
									$nTotalD  += $mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx'];
								}
							$data.= '</tr>';
							$nTotCotD += $mDatos[$nDatDo][key($mDatDo)][$i]['ivaxxxxx']+$mDatos[$nDatDo][key($mDatDo)][$i]['costoxxx'];
							$nTotCosD += $mIP[$nDatDo][key($mDatDo)][$i]['comvlrxx'];
							$nTotIvaD += $mIP[$nDatDo][key($mDatDo)][$i]['comvlr01'];
							$data .= '</tr>';
						}## for ($i=0; $i < $nRows; $i++) { ##
			
			
						unset($mIP[$nDatDo][key($mDatDo)]);
						// unset($mDatos[$nDatDo]);
						unset($mDatos[$nDatDo][key($mDatDo)]); // se elimina la posicion actual, en vez de todo el vector. 
			
						/*** Se avanza a la siguiente posicion del vector. ***/
						next($mDatDo);
						// echo count($mDatos[$nDatDo][key($mDatDo)])."~".count($mIP[$nDatDo][key($mDatDo)])."<br />";
					}
				}## foreach ($mDatos as $nDatDo => $mDatDo) { ##
		
				// echo'<pre>';
				// print_r($mIP);
				// echo'</pre>';
				// echo $data;
			
				#Ingresos Propios Sobrantes
				foreach ($mIP as $nIPDo => $mIPDo) {
					//si solo hay 3 posiciones significa que se ya se pintaron todas las facturas del DO
			
					if (count($mIPDo) > 3) {
						/*** Se agrega un for para recorrer la matriz interna de los pagos propios y se avanza en posiciones con la ayuda de la sentencia next ***/
						for($nIPA = 0; $nIPA < count($mIPDo); $nIPA++ ){
							# Obtener el documento de transporte
							$qTipoDo = "SELECT doctipxx ";
							$qTipoDo.= "FROM $cAlfa.sys00121 ";
							$qTipoDo.= "WHERE ";
							$qTipoDo.= "sucidxxx = \"{$mIPDo['sucidxxx']}\" AND ";
							$qTipoDo.= "docidxxx = \"{$mIPDo['docidxxx']}\" AND ";
							$qTipoDo.= "docsufxx = \"{$mIPDo['docsufxx']}\" LIMIT 0,1;";
							$xTipoDo = f_MySql("SELECT","",$qTipoDo,$xConexion01,"");
							$vTipoDo = mysql_fetch_array($xTipoDo);
							switch ($vTipoDo['doctipxx']) {
								case 'IMPORTACION':
									$qDocTran = "SELECT dgedtxxx ";
									$qDocTran.= "FROM $cAlfa.SIAI0200 ";
									$qDocTran.= "WHERE ";
									$qDocTran.= "admidxxx = \"{$mIPDo['sucidxxx']}\" AND ";
									$qDocTran.= "doiidxxx = \"{$mIPDo['docidxxx']}\" AND ";
									$qDocTran.= "doisfidx = \"{$mIPDo['docsufxx']}\" LIMIT 0,1";
									$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
									$vDocTran = mysql_fetch_array($xDocTran);
									$cDocTran = $vDocTran['dgedtxxx'];
									// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
								break;
								case 'EXPORTACION':
									$qDocTran = "SELECT dexdtrxx ";
									$qDocTran.= "FROM $cAlfa.siae0199 ";
									$qDocTran.= "WHERE ";
									$qDocTran.= "admidxxx = \"{$mIPDo['sucidxxx']}\" AND ";
									$qDocTran.= "dexidxxx = \"{$mIPDo['docidxxx']}\" LIMIT 0,1";
									$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
									// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
									$vDocTran = mysql_fetch_array($xDocTran);
									$cDocTran = $vDocTran['dexdtrxx'];
								break;
								default:
									$cDocTran = "";
								break;
							}## switch ($vTipoDo['doctipxx']) { ##

							foreach ($mIP[$nIPDo][key($mIPDo)] as $i => $cValueIP) {
								# Linea
								# Calcular numeor de rowspans
								$nRow = count($mIP[$nIPDo][key($mIPDo)][$i]['retencio']);

								$cColorPro = "#000000";
								$cColor = "#FFFFFF";

								if (($cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "ROLDANLO") && $mDatos[$i]['docidxxx'] != "") {
									$mIP[$nIPDo][key($mIPDo)][$i]['docidxxx'] = $mIP[$nIPDo][key($mIPDo)][$i]['sucidxxx'].'-'.$mIP[$nIPDo][key($mIPDo)][$i]['docidxxx'].'-'.$mIP[$nIPDo][key($mIPDo)][$i]['docsufxx'];
								}

								$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['docidxxx'].'</td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.key($mIPDo).'</td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$cDocTran.'</td>';
								# Para Terceros
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['teridxxx'].'</td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['ternomxx'].'</td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['comfecxx'].'</td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';

								# Para Ingresos propios
								$vConcepto = explode("~", trim($mIP[$nIPDo][key($mIPDo)][$i]['comobsxx'],"|"));
								$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$vConcepto[2].'</td>';
								$data .= '<td align="right"  '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.number_format($mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx'],2,',','').'</td>';
								$data .= '<td align="right"  '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.number_format($mIP[$nIPDo][key($mIPDo)][$i]['comvlr01'],2,',','').'</td>';
								$data .= '<td align="right"  '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.number_format(($mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx']+$mIP[$nIPDo][key($mIPDo)][$i]['comvlr01']),2,',','').'</td>';
								if ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] != "") {
									foreach ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] as $nKey => $vValue) {
										if ($nKey != 0) {
											$data .= '<tr>';
										}
										if ($vValue['retenxxx'] != "") {
											$data .= '<td align="left"   style = "color:'.$cColorPro.'">'.$vValue['retenxxx'].'</td>';
											$data .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($vValue['comvlr01'],2,',','').'</td>';
											$data .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($vValue['pucretxx'],3,',','').'</td>';
											if($vValue['retenxxx'] == 'ReteCree')  { $nTotRCreIP += $vValue['comvlrxx']; }
											if($vValue['retenxxx'] == 'Retefuente'){ $nTotRfteIP += $vValue['comvlrxx']; }
											if($vValue['retenxxx'] == 'ReteIva')   { $nTotRIvaIP += $vValue['comvlrxx']; }
											if($vValue['retenxxx'] == 'ReteIca')   { $nTotRIcaIP += $vValue['comvlrxx']; }
											$data .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($vValue['comvlrxx'],2,',','').'</td>';
											$data .= '</tr>';
										}else{
											$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
											$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
											$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
											$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
										}## if ($vValue['retenxxx'] != "") { ##
									}## foreach ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] as $nKey => $vValue) { ##
								}else{
									$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
									$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
									$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
									$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
								}## if ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] != "") { ##
								$nTotCosD += $mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx'];
								$nTotIvaD += $mIP[$nIPDo][key($mIPDo)][$i]['comvlr01'];
								$nTotalD  += $mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx'] + $mIP[$nIPDo][key($mIPDo)][$i]['comvlr01'];
								$data .= '</tr>';
							}## for ($i=0; $i < $nRows; $i++) { ##

							/*** Se avanza a la siguiente posicion del vector. ***/
							next($mIPDo); 
						}
					}
				}## foreach ($mIP as $nIPDo => $mIPDo) { ##
			
				$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
				$data .= '<td align="left"   colspan="'.($nNumCol-13).'" style="background-color:#0B610B"><b><font color=white>TOTAL NOTAS DEBITO</td>';
				$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotCotD,2,',','').'</font></b></td>';
				$data .= '<td align="left"   colspan="'.($nNumCol-16).'"  style="background-color:#0B610B"><b><font color=white></font></b></td>';
				$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotCosD,2,',','').'</font></b></td>';
				$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotIvaD,2,',','').'</font></b></td>';
				$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotalD,2,',','').'</font></b></td>';
				$data .= '<td align="right"  colspan="4" style="background-color:#0B610B"></td>';
				$data .= '</tr>';

				$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
				$data .= '<td align="center" colspan="'.$nNumCol.'" style = "color:'.$cColorPro.'"><b><font color=white></td>';
				$data .= '</tr>';

				if($nTotRfteDat != 0 || $nTotRfteIP != 0){
					$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
					$data .= '<td align="left" colspan="'.($nNumCol-1).'"  style = "color:'.$cColorPro.'"><b>TOTAL RETENCION EN LA FUENTE</b></td>';
					$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRfteDat + $nTotRfteIP,2,',','').'</b></td>';
					$data .= '</tr>';
				}

				if($nTotRCreDat != 0 || $nTotRCreIP != 0){
					$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
					$data .= '<td align="left" colspan="'.($nNumCol-1).'"  style = "color:'.$cColorPro.'"><b>TOTAL RETENCION CREE</b></td>';
					$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRCreDat + $nTotRCreIP,2,',','').'</b></td>';
					$data .= '</tr>';
				}

				if($nTotRIvaDat != 0 || $nTotRIvaIP != 0){
					$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
					$data .= '<td align="left" colspan="'.($nNumCol-1).'" style = "color:'.$cColorPro.'"><b>TOTAL RETENCION IVA</b></td>';
					$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRIvaDat +$nTotRIvaIP,2,',','').'</b></td>';
					$data .= '</tr>';
				}

				if($nTotRIcaDat != 0 || $nTotRIcaIP != 0){
					$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
					$data .= '<td align="left" colspan="'.($nNumCol-1).'" style = "color:'.$cColorPro.'"><b>TOTAL RETENCION ICA</b></td>';
					$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRIcaDat + $nTotRIcaIP,2,',','').'</b></td>';
				$data .= '</tr>';
				}

				# Final Reporte
				$data .= '</table>';
			
				fwrite($fOp, $data);
			}
			// Fin Generarion de de Nota de debito


			##############################################################################################
																		// AJUSTES CONTABLES //
			###############################################################################################
			// Inicio reporte ajustes contables
			$mIP = array();
      for ($cAno=substr($gAnioD,0,4); $cAno <= substr($gAnioH,0,4); $cAno++) {
        $qPropios = "SELECT $cAlfa.fcod$cAno.*, ";
        $qPropios.= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS ternomxx ";
        $qPropios.= "FROM $cAlfa.fcod$cAno ";
				$qPropios.= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
				$qPropios.= "LEFT JOIN $cAlfa.fpar0117 ON $cAlfa.fcod$cAno.comidxxx = $cAlfa.fpar0117.comidxxx AND $cAlfa.fcod$cAno.comcodxx = $cAlfa.fpar0117.comcodxx ";
				if($vSysStr['financiero_grupos_contables_reportes_ip'] != ""){
					$qPropios.= "LEFT JOIN $cAlfa.fpar0115 ON $cAlfa.fcod$cAno.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) ";
				}     				
				$qPropios.= "WHERE ";
        $qPropios.= "$cAlfa.fcod$cAno.teridxxx = \"$gTerId\" ";
        $qPropios.= "AND $cAlfa.fcod$cAno.comfecxx BETWEEN \"$gAnioD\" AND \"$gAnioH\" ";
				$qPropios.= "AND $cAlfa.fcod$cAno.regestxx = \"ACTIVO\" ";
				$qPropios.= "AND $cAlfa.fpar0117.comtipxx  = \"AJUSTES\"  ";
				$qPropios.= "AND $cAlfa.fcod$cAno.comdocfa != \"\" ";
				if($vSysStr['financiero_grupos_contables_reportes_ip'] != ""){
					$qPropios .= "AND $cAlfa.fpar0115.pucgruxx IN ({$vSysStr['financiero_grupos_contables_reportes_ip']}) ";
				}  
        $qPropios.= "ORDER BY $cAlfa.fcod$cAno.comidxxx,$cAlfa.fcod$cAno.comcodxx,$cAlfa.fcod$cAno.comcscxx,$cAlfa.fcod$cAno.comcsc2x,$cAlfa.fcod$cAno.comctocx";
        $xPropios = f_MySql("SELECT","",$qPropios,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qPropios."~".mysql_num_rows($xPropios));
        while( $xRP = mysql_fetch_assoc($xPropios)){
					$vDocFa = explode('~',$xRP['comdocfa']);
					$vCscFa = explode('-',$vDocFa[1]);

					if($vDocFa[0] != ""){
						$qFcoc  = "SELECT comfpxxx ";
						$qFcoc .= "FROM $cAlfa.fcoc$cAno ";
						$qFcoc .= "WHERE ";
						$qFcoc .= "comidxxx = \"{$vCscFa[0]}\" AND ";
						$qFcoc .= "comcodxx = \"{$vCscFa[1]}\" AND ";
						$qFcoc .= "comcscxx = \"{$vCscFa[2]}\" AND ";
						$qFcoc .= "comcsc2x = \"{$vCscFa[3]}\" LIMIT 0,1";
						$xFcoc = f_MySql("SELECT","",$qFcoc,$xConexion01,"");
						// f_Mensaje(__FILE__,__LINE__,$qFcoc."~".mysql_num_rows($xFcoc));
						$vFcoc = mysql_fetch_array($xFcoc);
						$mTramites = f_Explode_Array($vFcoc['comfpxxx'],"|","~");
	
						//for ($i=0;$i<count($mTramites);$i++) {
							$cDoSuc = $mTramites[0][15];
							$cDocId = $mTramites[0][2];
							$cDoSuf = $mTramites[0][3];
							//$i = count($mTramites);
						//}
					}

					$cFactura = $xRP['comcscxx'];
					$cNumDo = $cDoSuc."~".$cDocId."~".$cDoSuf;
					// Calculo de Valores dependiendo del movimineto de la cuenta
					$xRP['comvlr01'] = ($xRP['commovxx'] == "D") ? $xRP['comvlr01'] : ($xRP['comvlr01']*-1);
					$xRP['comvlrxx'] = ($xRP['commovxx'] == "D") ? $xRP['comvlrxx'] : ($xRP['comvlrxx']*-1);


					/**
					 * Buscando Descripción del Concepto
					 */
					$vCtoDes = array();
					$qCtoDes  = "SELECT ctodesxp ";
					$qCtoDes .= "FROM $cAlfa.fpar0119 ";
					$qCtoDes .= "WHERE ";
					$qCtoDes .= "ctoidxxx = \"{$xRP['ctoidxxx']}\" LIMIT 0,1 ";
					$xCtoDes  = f_MySql("SELECT","",$qCtoDes,$xConexion01,"");
					$vCtoDes = mysql_fetch_array($xCtoDes);			
					$xRP['ctodesxx'] = ($xRP['ctodesxx'] != "") ? $xRP['ctodesxx'] : $vCtoDes['ctodesxp']; 

					/**
					 * Buscando Pedido del DO
					 */
					$vPedDo = array();
					$qPedDo  = "SELECT docpedxx, doctipxx ";
					$qPedDo .= "FROM $cAlfa.sys00121 ";
					$qPedDo .= "WHERE ";
					$qPedDo .= "sucidxxx = \"$cDoSuc\" AND ";
					$qPedDo .= "docidxxx = \"$cDocId\" AND ";
					$qPedDo .= "docsufxx = \"$cDoSuf\" LIMIT 0,1 ";
					$xPedDo  = f_MySql("SELECT","",$qPedDo,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qPedDo."~".mysql_num_rows($xPedDo));
					$vPedDo = mysql_fetch_array($xPedDo);

					$nIP = count($mIP[$cNumDo][$cFactura]);
					$mIP[$cNumDo][$cFactura][$nIP] = $xRP;
					$mIP[$cNumDo]['sucidxxx'] = $cDoSuc;
					$mIP[$cNumDo]['docidxxx'] = $cDocId;
					$mIP[$cNumDo]['docsufxx'] = $cDoSuf;
					$mIP[$cNumDo]['docpedxx'] = $vPedDo['docpedxx'];
					$mIP[$cNumDo]['doctipxx'] = $vPedDo['doctipxx'];
				}

			}## for ($cAno=$gAnioD; $cAno <= $gAnioH; $cAno++) { ##

			// echo'mIP: \n';
			// echo'<pre>';
			// print_r($mIP);
			// echo'</pre>';
				
			if($nSwitch == 0 && count($mIP) > 0) {
				//Inicializando varibles de totales
				$nTotFac = 0;
				$nTotCotA = 0;
				$nTotCosA = 0;
				$nTotIvaA = 0;
				$nTotalA  = 0;

				$nTotRCreDat = 0;
				$nTotRfteDat = 0;
				$nTotRIvaDat = 0;
				$nTotRIcaDat = 0;

				$nTotRCreIP = 0;
				$nTotRfteIP = 0;
				$nTotRIvaIP = 0;
				$nTotRIcaIP = 0;
				
				$nNumCol = 21;
			
				#### PINTA POR EXCEL //Reporte de Ingreos Propios y Pagos a Terceros
				$data 		= '';

				# Tabla reporte de ingrsos propios y pagos a terceros
				$data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">';

				# Columnas
				$data.= '<tr>';
				$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>DO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>AJUSTES</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>NO. DOCUMENTO DE TRANSPORTE</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="120px" align="center"><b><font color=white>TERCERO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="450px" align="center"><b><font color=white>NOMBRE</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>DOCUMENTO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="100px" align="center"><b><font color=white>FECHA</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="400px" align="center"><b><font color=white>CONCEPTO DE TERCERO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>COSTO DE TERCERO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TIPO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR BASE</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="80px"  align="center"><b><font color=white>%</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="400px" align="center"><b><font color=white>CONCEPTO INGRESO PROPIO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>COSTO INGRESO PROPIO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>IVA</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TOTAL</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>TIPO</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR BASE</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="80px"  align="center"><b><font color=white>%</font></b></td>';
				$data.= '<td style="background-color:#0B610B" width="150px" align="center"><b><font color=white>VALOR</font></b></td>';
				$data.= '</tr>';
			
				#Ingresos Propios Sobrantes
				foreach ($mIP as $nIPDo => $mIPDo) {
					//si solo hay 3 posiciones significa que se ya se pintaron todas las facturas del DO

					/*** Se agrega un for para recorrer la matriz interna de los pagos propios y se avanza en posiciones con la ayuda de la sentencia next ***/
					for($nIPA = 0; $nIPA < count($mIPDo); $nIPA++ ){
						//echo "veces: ".$f++."\n";
						# Obtener el documento de transporte
						$qTipoDo = "SELECT doctipxx ";
						$qTipoDo.= "FROM $cAlfa.sys00121 ";
						$qTipoDo.= "WHERE ";
						$qTipoDo.= "sucidxxx = \"{$mIPDo['sucidxxx']}\" AND ";
						$qTipoDo.= "docidxxx = \"{$mIPDo['docidxxx']}\" AND ";
						$qTipoDo.= "docsufxx = \"{$mIPDo['docsufxx']}\" LIMIT 0,1;";
						$xTipoDo = f_MySql("SELECT","",$qTipoDo,$xConexion01,"");
						$vTipoDo = mysql_fetch_array($xTipoDo);
						switch ($vTipoDo['doctipxx']) {
							case 'IMPORTACION':
								$qDocTran = "SELECT dgedtxxx ";
								$qDocTran.= "FROM $cAlfa.SIAI0200 ";
								$qDocTran.= "WHERE ";
								$qDocTran.= "admidxxx = \"{$mIPDo['sucidxxx']}\" AND ";
								$qDocTran.= "doiidxxx = \"{$mIPDo['docidxxx']}\" AND ";
								$qDocTran.= "doisfidx = \"{$mIPDo['docsufxx']}\" LIMIT 0,1";
								$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
								$vDocTran = mysql_fetch_array($xDocTran);
								$cDocTran = $vDocTran['dgedtxxx'];
								// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
							break;
							case 'EXPORTACION':
								$qDocTran = "SELECT dexdtrxx ";
								$qDocTran.= "FROM $cAlfa.siae0199 ";
								$qDocTran.= "WHERE ";
								$qDocTran.= "admidxxx = \"{$mIPDo['sucidxxx']}\" AND ";
								$qDocTran.= "dexidxxx = \"{$mIPDo['docidxxx']}\" LIMIT 0,1";
								$xDocTran = f_Mysql("SELECT","",$qDocTran,$xConexion01,"");
								// echo __LINE__.":".$qDocTran."~".mysql_num_rows($xDocTran)."~".$cDocTran."<br>";
								$vDocTran = mysql_fetch_array($xDocTran);
								$cDocTran = $vDocTran['dexdtrxx'];
							break;
							default:
								$cDocTran = "";
							break;
						}## switch ($vTipoDo['doctipxx']) { ##

						foreach ($mIP[$nIPDo][key($mIPDo)] as $i => $cValueIP) {
							# Linea
							# Calcular numeor de rowspans
							$nRow = count($mIP[$nIPDo][key($mIPDo)][$i]['retencio']);

							$cColorPro = "#000000";
							$cColor = "#FFFFFF";

							if (($cAlfa == "DEROLDANLO" || $cAlfa == "TEROLDANLO" || $cAlfa == "ROLDANLO") && $mDatos[$i]['docidxxx'] != "") {
								$mIP[$nIPDo][key($mIPDo)][$i]['docidxxx'] = $mIP[$nIPDo][key($mIPDo)][$i]['sucidxxx'].'-'.$mIP[$nIPDo][key($mIPDo)][$i]['docidxxx'].'-'.$mIP[$nIPDo][key($mIPDo)][$i]['docsufxx'];
							}

							$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIPDo['docidxxx'].'</td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.key($mIPDo).'</td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$cDocTran.'</td>';
							# Para Terceros
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['teridxxx'].'</td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['ternomxx'].'</td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$mIP[$nIPDo][key($mIPDo)][$i]['comfecxx'].'</td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'"></td>';

							# Para Ingresos propios
							$vConcepto = trim($mIP[$nIPDo][key($mIPDo)][$i]['ctodesxx']);
							$data .= '<td align="left"   '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.$vConcepto.'</td>';
							$data .= '<td align="right"  '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.number_format($mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx'],2,',','').'</td>';
							$data .= '<td align="right"  '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.number_format($mIP[$nIPDo][key($mIPDo)][$i]['comvlr01'],2,',','').'</td>';
							$data .= '<td align="right"  '.(($nRow > 1) ? "rowspan=\"$nRow\" " : "").'style = "color:'.$cColorPro.'">'.number_format(($mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx']+$mIP[$nIPDo][key($mIPDo)][$i]['comvlr01']),2,',','').'</td>';
							if ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] != "") {
								foreach ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] as $nKey => $vValue) {
									if ($nKey != 0) {
										$data .= '<tr>';
									}
									if ($vValue['retenxxx'] != "") {
										$data .= '<td align="left"   style = "color:'.$cColorPro.'">'.$vValue['retenxxx'].'</td>';
										$data .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($vValue['comvlr01'],2,',','').'</td>';
										$data .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($vValue['pucretxx'],3,',','').'</td>';
										if($vValue['retenxxx'] == 'ReteCree')  { $nTotRCreIP += $vValue['comvlrxx']; }
										if($vValue['retenxxx'] == 'Retefuente'){ $nTotRfteIP += $vValue['comvlrxx']; }
										if($vValue['retenxxx'] == 'ReteIva')   { $nTotRIvaIP += $vValue['comvlrxx']; }
										if($vValue['retenxxx'] == 'ReteIca')   { $nTotRIcaIP += $vValue['comvlrxx']; }
										$data .= '<td align="right"  style = "color:'.$cColorPro.'">'.number_format($vValue['comvlrxx'],2,',','').'</td>';
										$data .= '</tr>';
									}else{
										$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
										$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
										$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
										$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
									}## if ($vValue['retenxxx'] != "") { ##
								}## foreach ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] as $nKey => $vValue) { ##
							}else{
								$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
								$data .= '<td align="right"  style = "color:'.$cColorPro.'"></td>';
							}## if ($mIP[$nIPDo][key($mIPDo)][$i]['retencio'] != "") { ##
							$nTotCosA += $mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx'];
							$nTotIvaA += $mIP[$nIPDo][key($mIPDo)][$i]['comvlr01'];
							$nTotalA  += $mIP[$nIPDo][key($mIPDo)][$i]['comvlrxx'] + $mIP[$nIPDo][key($mIPDo)][$i]['comvlr01'];
							$data .= '</tr>';
						}## for ($i=0; $i < $nRows; $i++) { ##

						/*** Se avanza a la siguiente posicion del vector. ***/
						next($mIPDo); 
					}
				}## foreach ($mIP as $nIPDo => $mIPDo) { ##
			
				$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
				$data .= '<td align="left"   colspan="'.($nNumCol-13).'" style="background-color:#0B610B"><b><font color=white>TOTAL AJUSTES</td>';
				$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotCotA,2,',','').'</font></b></td>';
				$data .= '<td align="left"   colspan="'.($nNumCol-16).'"  style="background-color:#0B610B"><b><font color=white></font></b></td>';
				$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotCosA,2,',','').'</font></b></td>';
				$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotIvaA,2,',','').'</font></b></td>';
				$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotalA,2,',','').'</font></b></td>';
				$data .= '<td align="right"  colspan="4" style="background-color:#0B610B"></td>';
				$data .= '</tr>';

				$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
				$data .= '<td align="center" colspan="'.$nNumCol.'" style = "color:'.$cColorPro.'"><b><font color=white></td>';
				$data .= '</tr>';

				if($nTotRfteDat != 0 || $nTotRfteIP != 0){
					$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
					$data .= '<td align="left" colspan="'.($nNumCol-1).'"  style = "color:'.$cColorPro.'"><b>TOTAL RETENCION EN LA FUENTE</b></td>';
					$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRfteDat + $nTotRfteIP,2,',','').'</b></td>';
					$data .= '</tr>';
				}

				if($nTotRCreDat != 0 || $nTotRCreIP != 0){
					$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
					$data .= '<td align="left" colspan="'.($nNumCol-1).'"  style = "color:'.$cColorPro.'"><b>TOTAL RETENCION CREE</b></td>';
					$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRCreDat + $nTotRCreIP,2,',','').'</b></td>';
					$data .= '</tr>';
				}

				if($nTotRIvaDat != 0 || $nTotRIvaIP != 0){
					$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
					$data .= '<td align="left" colspan="'.($nNumCol-1).'" style = "color:'.$cColorPro.'"><b>TOTAL RETENCION IVA</b></td>';
					$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRIvaDat +$nTotRIvaIP,2,',','').'</b></td>';
					$data .= '</tr>';
				}

				if($nTotRIcaDat != 0 || $nTotRIcaIP != 0){
					$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
					$data .= '<td align="left" colspan="'.($nNumCol-1).'" style = "color:'.$cColorPro.'"><b>TOTAL RETENCION ICA</b></td>';
					$data .= '<td align="right"  style = "color:'.$cColorPro.'"><b>'.number_format($nTotRIcaDat + $nTotRIcaIP,2,',','').'</b></td>';
				$data .= '</tr>';
				}

				# Final Reporte
				$data .= '</table>';
			
				fwrite($fOp, $data);
			}//if($nSwitch == 0 && count($mIP) > 0) {
			//die();

			//Fin Generacion Reporte Ajustes contables

			// Totales
			$nTotales = $nTotCoT + $nTotCotC + $nTotCotD + $nTotCotA;
			$nTotCosT = $nTotCos + $nTotCosC + $nTotCosD + $nTotCosA;
			$nTotIvaT = $nTotIva + $nTotIvaC + $nTotIvaD + $nTotIvaA;
			$nTotalT  = $nTotal + $nTotalC + $nTotalD + $nTotalA;
			$data  = '';
			$data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">';
			$data .= '<tr bgcolor = "white" height="20" style="padding-left:4px;padding-right:4px">';
			$data .= '<td align="left"   colspan="'.($nNumCol-13).'" style="background-color:#0B610B"><b><font color=white>TOTALES</td>';
			$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotales,2,',','').'</font></b></td>';
			$data .= '<td align="left"   colspan="'.($nNumCol-16).'"  style="background-color:#0B610B"><b><font color=white></font></b></td>';
			$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotCosT,2,',','').'</font></b></td>';
			$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotIvaT,2,',','').'</font></b></td>';
			$data .= '<td align="right"  style="background-color:#0B610B"><b><font color=white>'.number_format($nTotalT,2,',','').'</font></b></td>';
			$data .= '<td align="right"  colspan="4" style="background-color:#0B610B"></td>';
			$data .= '</tr>';
			$data .= '</table>';
			fwrite($fOp, $data);

			fclose($fOp);
			if($nSwitch == 0){
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
			}else{
				if ($_SERVER["SERVER_PORT"] != "") {
					f_Mensaje(__FILE__, __LINE__, "Se presentaron Errores en el proceso.\n".$cMsj."\nVerifique.");
				}
			}
		} // If inicial
	} // if inicial

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

	/**
	 * Retorna los pagos a terceros y los ingresos propios para Notas de Credito y Nota de DEbito
	 *
	 * @param array $pArrayParametros
	 * @return array
	 */
	function fnGenerarReporteNotas($pArrayParametros){

		global $xConexion01; global $cAlfa; global $vSysStr; global $kUser; global $cPlesk_Skin_Directory; global $cPath; global $OPENINIT;
		/**
		 * La matriz debe traer los siguentes datos cargados:
		 *
		 * $pArrayParametros['cTipCom']   => Tipo de documento
		 * $pArrayParametros['cNit']      => Debe ser un array con los nit seleccionados
		 * $pArrayParametros['dFecIni']   => Fecha inicial
		 * $pArrayParametros['dFecFin']   => Fecha Fin
		 */

		/**
		 * Matriz para retornar los datos.
		 * @var array
		 */
		$mReturn = array();
		$mReturn[0] = "";

		/*
		* Switch para saber si hubo error
		* @var nSwitch
		*/
		$nSwitch = 0;

		/**
		 * Vectores para reemplazar caracteres de salto de linea y tabuladores
		 */
		$vBuscar = array(chr(13),chr(10),chr(27),chr(9),'"');
		$vReempl = array(" "," "," "," "," ");

		// Inicio reporte de NC y ND
		// Creación de las tablas temporales
		$mReturnTablaCab  = fnCrearEstructurasPcc(array('TIPOESTU'=>'PAGOS'));
		if($mReturnTablaCab[0] == "false"){
			$nSwitch = 1;
			for($nC=1;$nC<count($mReturnTablaCab);$nC++){
				$mReturn[count($mReturn)] = $mReturnTablaCab[$nC];
			}
		}

		if($nSwitch == 0){
			$mReturnTablaRet  = fnCrearEstructurasPcc(array('TIPOESTU'=>'RETENCIONES'));
			if($mReturnTablaRet[0] == "false"){
				$nSwitch = 1;
				for($nD=1;$nD<count($mReturnTablaRet);$nD++){
					$mReturn[count($mReturn)] = $mReturnTablaRet[$nD];
				}
			}
		}
		
		if($nSwitch == 0){
			$vDatos['cTipCom']   = $pArrayParametros['cTipCom'];
			$vDatos['cNit']      = $pArrayParametros['cNit'];
			$vDatos['dFecIni']   = $pArrayParametros['dFecIni'];
			$vDatos['dFecFin']   = $pArrayParametros['dFecFin'];
			$vDatos['cTablaCab'] = $mReturnTablaCab[1];
			$vDatos['cTablaRet'] = $mReturnTablaRet[1];
			$mReturnGenerarPccNotas = fnGenerarPccNotas($vDatos);
			if($mReturnGenerarPccNotas[0] == "false") {
				$nSwitch = 1;
				for($nR=1;$nR<count($mReturnGenerarPccNotas);$nR++){
					$mReturn[count($mReturn)] = $mReturnGenerarPccNotas[$nR];
				}
			}
		}

		if($nSwitch == 0){
			$mPagTer = array();
			$mDatos = array();
			$qPagTer  = "SELECT * ";
			$qPagTer .= "FROM $cAlfa.{$mReturnTablaCab[1]} ";
			$xPagTer = f_MySql("SELECT","",$qPagTer,$xConexion01,"");
			//f_Mensaje(__FILE__, __LINE__, $qPagTer."~".mysql_num_rows($xPagTer));
			$vPagTer = array(); $vRetPcc = array();
			$nCanReg = 0; $nCanReg01 = 0;
			while( $xRPT = mysql_fetch_assoc($xPagTer)){
				$mReten = array();
				$nCanReg++;
				if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBPcc($xConexion01); }
				$mPagTer[count($mPagTer)] = $xRPT;

				/**
				 * Buscando Pedido y tipo operacion del DO
				 */
				$qPedDo  = "SELECT docpedxx, doctipxx ";
				$qPedDo .= "FROM $cAlfa.sys00121 ";
				$qPedDo .= "WHERE ";
				$qPedDo .= "sucidxxx = \"{$xRPT['sucidxxx']}\" AND ";
				$qPedDo .= "docidxxx = \"{$xRPT['docidxxx']}\" AND ";
				$qPedDo .= "docsufxx = \"{$xRPT['docsufxx']}\" LIMIT 0,1 ";
				$xPedDo  = f_MySql("SELECT","",$qPedDo,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qPedDo."~".mysql_num_rows($xPedDo));
				$vPedDo = mysql_fetch_array($xPedDo);
				
				$vPagTer['sucidxxx'] = $xRPT['sucidxxx'];
				$vPagTer['docidxxx'] = $xRPT['docidxxx'];
				$vPagTer['docsufxx'] = $xRPT['docsufxx'];
				$vPagTer['docpedxx'] = $vPedDo['docpedxx'];
				$vPagTer['doctipxx'] = $vPedDo['doctipxx'];
				$vPagTer['comidxxx'] = $xRPT['comidcxx'];
				$vPagTer['comcodxx'] = $xRPT['comcodcx'];
				$vPagTer['facturax'] = $xRPT['comcsccx'];
				$vPagTer['teridxxx'] = $xRPT['terid2xx'];
				$vPagTer['ternomxx'] = $xRPT['pronomxx'];
				$vPagTer['document'] = $xRPT['comcscxx'];
				$vPagTer['comfecxx'] = $xRPT['comfecxx'];
				$vPagTer['concepto'] = $xRPT['ctodesxx'];
				$vPagTer['costoxxx'] = $xRPT['comvlrxx'];
				$vPagTer['ivaxxxxx'] = $xRPT['comvlr02'];
				$vPagTer['totalxxx'] = $xRPT['comvlr01'];
				$vPagTer['cliidxxx'] = $xRPT['teridxxx'];
				$vPagTer['regestxx'] = $xRPT['regestxx'];

				$qReten  = "SELECT * ";
				$qReten .= "FROM $cAlfa.{$mReturnTablaRet[1]} ";
				$qReten .= "WHERE ";
				$qReten .= "pccidxxx = \"{$xRPT['pccidxxx']}\" ";
				$xReten = f_MySql("SELECT","",$qReten,$xConexion01,"");
				//f_Mensaje(__FILE__, __LINE__, $qReten."~".mysql_num_rows($xReten));
				while( $xRRT = mysql_fetch_assoc($xReten)){
					$nCanReg01++;
					if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBPcc($xConexion01); }

					$vRetPcc['doiidxxx'] = $xRPT['docidxxx'];
					$vRetPcc['facturax'] = $xRPT['comidaxx']."-".$xRPT['comcodax']."-".$xRPT['comcscax']."-".$xRPT['comcsca2'];
					$vRetPcc['pucretxx'] = $xRRT['retporxx'];
					$vRetPcc['comvlr01'] = $xRRT['comvlr01'];
					$vRetPcc['comvlrxx'] = $xRRT['comvlrxx'];
					$vRetPcc['teridxxx'] = $xRRT['teridxxx'];
					$vRetPcc['ternomxx'] = $xRPT['ternomxx'];
					$vRetPcc['terid2xx'] = $xRRT['terid2xx'];
					$vRetPcc['comfecxx'] = $xRPT['comfecxx'];
					$vRetPcc['retenxxx'] = $xRRT['rettipxx'];
					$vRetPcc['comidsxx'] = $xRPT['comidxxx'];
					$vRetPcc['comcodsx'] = $xRPT['comcodxx'];
					$vRetPcc['comcscsx'] = $xRPT['comcscxx'];
					$vRetPcc['comseqsx'] = $xRPT['comseqxx'];
					$vRetPcc['ctoidsxx'] = $xRPT['ctoidxxx'];
					$vRetPcc['sucidsxx'] = $xRPT['sucidxxx'];
					$vRetPcc['docidsxx'] = $xRPT['docidxxx'];
					$vRetPcc['docsufsx'] = $xRPT['docsufxx'];
					$mReten[count($mReten)] = $vRetPcc;
				}
				$vPagTer['retencio'] = $mReten;
				
				$cTramite = $xRPT['sucidxxx']."~".$xRPT['docidxxx']."~".$xRPT['docsufxx'];
				$mInd_mDatos = count($mDatos[$cTramite][$xRPT['comcsccx']]); // índice para mDatos
				$mDatos[$cTramite][$xRPT['comcsccx']][$mInd_mDatos] = $vPagTer;
			}//while( $xRPT = mysql_fetch_assoc($xPagTer)){

			# Consulta a la tabla de detalle para buscar los conceptos propios.
			for ($cAno=substr($pArrayParametros['dFecIni'],0,4); $cAno <= substr($pArrayParametros['dFecFin'],0,4); $cAno++) {
				//$qPropios = "SELECT $cAlfa.fcod$cAno.*, ";
				$qPropios = "SELECT ";
				$qPropios .= "$cAlfa.fcod$cAno.comidxxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.comcodxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.comcscxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.comcsc2x, ";
				$qPropios .= "$cAlfa.fcod$cAno.comseqcx, ";
				$qPropios .= "$cAlfa.fcod$cAno.comidcxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.comcodcx, ";
				$qPropios .= "$cAlfa.fcod$cAno.comcsccx, ";
				$qPropios .= "$cAlfa.fcod$cAno.comcscc2, ";
				$qPropios .= "$cAlfa.fcod$cAno.comseqc2, ";
				$qPropios .= "$cAlfa.fcod$cAno.regestxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.pucidxxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.ctoidxxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.commovxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.ctodesxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.comobsxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.comctocx, ";
				$qPropios .= "$cAlfa.fcod$cAno.sccidxxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.docidxxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.sucidxxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.docsufxx, ";
				$qPropios .= "$cAlfa.fcod$cAno.comdocfa, ";
				$qPropios .= "$cAlfa.fcod$cAno.pucretxx, ";
				if($pArrayParametros['cTipCom'] == "NC"){
					$qPropios .= "IF($cAlfa.fcod$cAno.commovxx = \"D\",($cAlfa.fcod$cAno.comvlrxx * -1),$cAlfa.fcod$cAno.comvlrxx) as comvlrxx, "; 
					$qPropios .= "IF($cAlfa.fcod$cAno.commovxx = \"D\",($cAlfa.fcod$cAno.comvlr01 * -1),$cAlfa.fcod$cAno.comvlr01) as comvlr01, "; 
					$qPropios .= "IF($cAlfa.fcod$cAno.commovxx = \"D\",($cAlfa.fcod$cAno.comvlr02 * -1),$cAlfa.fcod$cAno.comvlr02) as comvlr02, "; 
				}else{
					$qPropios .= "IF($cAlfa.fcod$cAno.commovxx = \"D\",($cAlfa.fcod$cAno.comvlrxx * -1),$cAlfa.fcod$cAno.comvlrxx) as comvlrxx, "; 
					$qPropios .= "IF($cAlfa.fcod$cAno.commovxx = \"D\",($cAlfa.fcod$cAno.comvlr01 * -1),$cAlfa.fcod$cAno.comvlr01) as comvlr01, "; 
					$qPropios .= "IF($cAlfa.fcod$cAno.commovxx = \"D\",($cAlfa.fcod$cAno.comvlr02 * -1),$cAlfa.fcod$cAno.comvlr02) as comvlr02, "; 
				}
				$qPropios .= "$cAlfa.fcoc$cAno.comobs2x, ";
				$qPropios.= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS ternomxx ";
				$qPropios.= "FROM $cAlfa.fcod$cAno ";
        $qPropios.= "LEFT JOIN $cAlfa.fcoc$cAno ON $cAlfa.fcod$cAno.comidxxx = $cAlfa.fcoc$cAno.comidxxx AND $cAlfa.fcod$cAno.comcodxx = $cAlfa.fcoc$cAno.comcodxx AND $cAlfa.fcod$cAno.comcscxx = $cAlfa.fcoc$cAno.comcscxx AND $cAlfa.fcod$cAno.comcsc2x = $cAlfa.fcoc$cAno.comcsc2x ";
				$qPropios.= "LEFT JOIN $cAlfa.fpar0117 ON $cAlfa.fcod$cAno.comidxxx = $cAlfa.fpar0117.comidxxx AND $cAlfa.fcod$cAno.comcodxx = $cAlfa.fpar0117.comcodxx ";
				$qPropios.= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$cAno.teridxxx = $cAlfa.SIAI0150.CLIIDXXX ";
				$qPropios.= "WHERE ";
				if($pArrayParametros['cTipCom'] == "NC"){
					$qPropios.= "$cAlfa.fcod$cAno.comidxxx = \"C\" ";
				}else{
					$qPropios.= "$cAlfa.fcod$cAno.comidxxx = \"D\" ";
				}
				$qPropios.= "AND $cAlfa.fcod$cAno.teridxxx = \"{$pArrayParametros['cNit']}\" ";
				$qPropios.= "AND $cAlfa.fcod$cAno.comfecxx BETWEEN \"{$pArrayParametros['dFecIni']}\" AND \"{$pArrayParametros['dFecFin']}\" ";
				$qPropios.= "AND $cAlfa.fcod$cAno.regestxx = \"ACTIVO\" ";
				$qPropios.= "AND $cAlfa.fcod$cAno.comctocx IN (\"IP\",\"RETFTE\",\"RETCRE\",\"RETICA\",\"RETIVA\",\"ARETFTE\",\"ARETCRE\",\"ARETICA\") ";
				$qPropios.= "AND $cAlfa.fpar0117.comtipxx != \"AJUSTES\" ";
				$qPropios.= "ORDER BY $cAlfa.fcod$cAno.comidxxx,$cAlfa.fcod$cAno.comcodxx,$cAlfa.fcod$cAno.comcscxx,$cAlfa.fcod$cAno.comcsc2x,$cAlfa.fcod$cAno.comctocx";
				$xPropios = f_MySql("SELECT","",$qPropios,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qPropios."~".mysql_num_rows($xPropios));
				$cFacId = "";
				while( $xRP = mysql_fetch_array($xPropios)){
					if($cFacId != $xRP['comidxxx']."~".$xRP['comcodxx']."~".$xRP['comcscxx']."~".$xRP['comcsc2x']){
						
						if ($cFacId != "") {
							//Asignado retenciones al primer IP
							$mIP[$mRetenciones[0]['docidxxx']][$mRetenciones[0]['facturax']][0]['retencio'] = $mRetenciones;
						}

						$cFacId = $xRP['comidxxx']."~".$xRP['comcodxx']."~".$xRP['comcscxx']."~".$xRP['comcsc2x'];
						$mRetenciones = array();

						//Extraer factura afectada en la NC
						$vDocAfe = explode("~", $xRP['comobs2x']);

						$qFcoc  = "SELECT comfpxxx ";
						$qFcoc .= "FROM $cAlfa.fcoc$cAno ";
						$qFcoc .= "WHERE ";
						$qFcoc .= "comidxxx = \"{$vDocAfe[1]}\" AND ";
						$qFcoc .= "comcodxx = \"{$vDocAfe[2]}\" AND ";
						$qFcoc .= "comcscxx = \"{$vDocAfe[3]}\" AND ";
						$qFcoc .= "comcsc2x = \"{$vDocAfe[4]}\" LIMIT 0,1";
						$xFcoc = f_MySql("SELECT","",$qFcoc,$xConexion01,"");
						// f_Mensaje(__FILE__,__LINE__,$qFcoc."~".mysql_num_rows($xFcoc));
						$vFcoc = mysql_fetch_array($xFcoc);
						$mTramites = f_Explode_Array($vFcoc['comfpxxx'],"|","~");

						$cPrimerTramite = ""; $vTramites = array();
						for ($i=0;$i<count($mTramites);$i++) {
							if ($cPrimerTramite == "") {
								$cPrimerTramite = $mTramites[$i][15]."~".$mTramites[$i][2]."~".$mTramites[$i][3];
							}
							$vTramites["{$mTramites[$i][2]}"] = $mTramites[$i][15]."~".$mTramites[$i][2]."~".$mTramites[$i][3];
						}
					}

					$cFactura = $xRP['comcscxx'];

					switch ($xRP['comctocx']) {
						case 'IP':
							$cNumDo = $xRP['sucidxxx']."~".$xRP['docidxxx']."~".$xRP['docsufxx'];

							/**
							 * Buscando Pedido del DO
							 */
							$vPedDo = array();
							$qPedDo  = "SELECT docpedxx, doctipxx ";
							$qPedDo .= "FROM $cAlfa.sys00121 ";
							$qPedDo .= "WHERE ";
							$qPedDo .= "sucidxxx = \"{$xRP['sucidxxx']}\" AND ";
							$qPedDo .= "docidxxx = \"{$xRP['docidxxx']}\" AND ";
							$qPedDo .= "docsufxx = \"{$xRP['docsufxx']}\" LIMIT 0,1 ";
							$xPedDo  = f_MySql("SELECT","",$qPedDo,$xConexion01,"");
							// f_Mensaje(__FILE__,__LINE__,$qPedDo."~".mysql_num_rows($xPedDo));
							$vPedDo = mysql_fetch_array($xPedDo);

							$nIP = count($mIP[$cNumDo][$cFactura]);
							$mIP[$cNumDo][$cFactura][$nIP] = $xRP;
							$mIP[$cNumDo]['sucidxxx'] = $xRP['sucidxxx'];
							$mIP[$cNumDo]['docidxxx'] = $xRP['docidxxx'];
							$mIP[$cNumDo]['docsufxx'] = $xRP['docsufxx'];
							$mIP[$cNumDo]['docpedxx'] = $vPedDo['docpedxx'];
							$mIP[$cNumDo]['doctipxx'] = $vPedDo['doctipxx'];
						break;
						default:
							//Buscando el porcentaje de retencion
							$xRP['pucretxx'] = $mCuenta["{$xRP['pucidxxx']}"]['pucretxx'];

							$cNumDo = $cPrimerTramite;

							$nInd_mRetenciones = count($mRetenciones);
							$mRetenciones[$nInd_mRetenciones]['docidxxx'] = $cNumDo;
							$mRetenciones[$nInd_mRetenciones]['facturax'] = $cFactura;
							$mRetenciones[$nInd_mRetenciones]['pucretxx'] = $xRP['pucretxx'];
							if($pArrayParametros['cTipCom'] == "NC"){
								$nComVlr01 = ($xRP['commovxx'] == "D") ? $xRP['comvlr01'] : ($xRP['comvlr01']*-1);
								$nComVlr   = ($xRP['commovxx'] == "D") ? $xRP['comvlrxx'] : ($xRP['comvlrxx']*-1);

								$mRetenciones[$nInd_mRetenciones]['comvlr01'] = $nComVlr01;
								$mRetenciones[$nInd_mRetenciones]['comvlrxx'] = $nComVlr;
							}else{
								$nComVlr01 = ($xRP['commovxx'] == "C") ? $xRP['comvlr01'] : ($xRP['comvlr01']*-1);
								$nComVlr   = ($xRP['commovxx'] == "C") ? $xRP['comvlrxx'] : ($xRP['comvlrxx']*-1);

								$mRetenciones[$nInd_mRetenciones]['comvlr01'] = $nComVlr01;
								$mRetenciones[$nInd_mRetenciones]['comvlrxx'] = $nComVlr;								
							}
							switch ($xRP['comctocx']) {
								case 'RETFTE':
									$mRetenciones[$nInd_mRetenciones]['retenxxx'] = "Retefuente";
								break;
								case 'RETCRE':
									$mRetenciones[$nInd_mRetenciones]['retenxxx'] = "ReteCREE";
								break;
								case 'RETIVA':
									$mRetenciones[$nInd_mRetenciones]['retenxxx'] = "ReteIva";
								break;
								case 'RETICA':
									$mRetenciones[$nInd_mRetenciones]['retenxxx'] = "ReteIca";
								break;
								case 'ARETFTE':
									$mRetenciones[$nInd_mRetenciones]['retenxxx'] = "AutoRFte";
								break;
								case 'ARETCRE':
									$mRetenciones[$nInd_mRetenciones]['retenxxx'] = "AutoRCree";
								break;
								case 'ARETICA':
									$mRetenciones[$nInd_mRetenciones]['retenxxx'] = "AutoRICA";
								break;
								default:
									//No hace nada
								break;
							}
						break;
					}## switch ($xRP['comctocx']) { ##
				}## while( $xRP = mysql_fetch_array($xPropios)){ ##
				$mIP[$mRetenciones[0]['docidxxx']][$mRetenciones[0]['facturax']][0]['retencio'] = $mRetenciones;	
			}## for ($cAno=$gAnioD; $cAno <= $gAnioH; $cAno++) { ##
		}//if($nSwitch == 0){

		if($nSwitch == 0){
			$mReturn[0] = "true";
			$mReturn[1] = $mDatos;
			$mReturn[2] = $mIP;
		}else{
			$mReturn[0] = "false";
		}
		return $mReturn;
	}
	
	/**
	 * Genera el Pagos a Terceros y retenciones de Notas credito y Debito
	 *
	 * @param array $pArrayParametros
	 * @return array
	 */
	function fnGenerarPccNotas($pArrayParametros){

		global $xConexion01; global $cAlfa; global $vSysStr; global $kUser; global $cPlesk_Skin_Directory; global $cPath; global $OPENINIT;

		/**
		 * La matriz debe traer los siguentes datos cargados:
		 *
		 * $pArrayParametros['cNit']      => Debe ser un array con los nit seleccionados
		 * $pArrayParametros['dFecIni']   => Fecha inicial
		 * $pArrayParametros['dFecFin']   => Fecha Fin
		 * $pArrayParametros['cTablaCab'] => Tabla Temporal Certificados de Pagos a Terceros Revisados
		 * $pArrayParametros['cTablaRet'] => Tabla Temporal Retenciones Certificados de Pagos a Terceros Revisados
		 * 
		 * No se incluye logica para formularios, ya que Grumalco no factura formularios
		 */

		/**
		 * Matriz para retornar los datos.
		 * @var array
		 */
		$mReturn = array();
		$mReturn[0] = "";

		/*
		* Switch para saber si hubo error
		* @var nSwitch
		*/
		$nSwitch = 0;

		/**
		 * Vectores para reemplazar caracteres de salto de linea y tabuladores
		 */
		$vBuscar = array(chr(13),chr(10),chr(27),chr(9),'"');
		$vReempl = array(" "," "," "," "," ");

		/*** Creacion tablas temporales ***/
		/*** Tabla temporal para detalle de los comprobantes que tienen pagos a terceros ***/
		$mReturnTablaM  = fnCrearEstructurasPcc(array('TIPOESTU'=>'PAGOS'));
		if ($mReturnTablaM[0] == "false") {
			$nSwitch = 1;
			for($nRT=1;$nRT<count($mReturnTablaM);$nRT++){
				$mReturn[count($mReturn)] = $mReturnTablaM[$nRT];
			}
		}

		/*** Tabla temporal para Retenciones de los comprobantes que tienen pagos a terceros ***/
		$mReturnTablaR  = fnCrearEstructurasPcc(array('TIPOESTU'=>'RETENCIONESAUX'));
		if ($mReturnTablaR[0] == "false") {
			$nSwitch = 1;
			for($nRT=1;$nRT<count($mReturnTablaR);$nRT++){
				$mReturn[count($mReturn)] = $mReturnTablaR[$nRT];
			}
		}

		if ($nSwitch == 0) {
			/**
			 * Buscando los comprobantes que son recibos de caja menor
			 */
			$qDatCom  = "SELECT  ";
			$qDatCom .= "CONCAT(comidxxx,\"~\",comcodxx) AS comidxxx ";
			$qDatCom .= "FROM $cAlfa.fpar0117 ";
			$qDatCom .= "WHERE ";
			$qDatCom .= "comtipxx = \"RCM\" AND ";
			$qDatCom .= "regestxx = \"ACTIVO\" ";
			$xDatCom  = f_MySql("SELECT","",$qDatCom,$xConexion01,"");
			// f_Mensaje(__FILE__, __LINE__, $qDatCom."~".mysql_num_rows($xDatCom));
			$mDatCom = array();
			if (mysql_num_rows($xDatCom) > 0) {
				// Cargo la Matriz con los ROWS del Cursor
				while ($xRCD = mysql_fetch_array($xDatCom)) {
					$mDatCom[count($mDatCom)] = $xRCD['comidxxx'];
				}
				// Fin de Cargo la Matriz con los ROWS del Cursor
			}

			//Buscando los comprobantes de Notas
			$cNotas = "";
			if ($pArrayParametros['cTipCom'] == 'NC') {
				//Buscando comprobantes marcados de Nota Credito
				$qNotas  = "SELECT ";
				$qNotas .= "CONCAT(comidxxx,\"-\",comcodxx) AS comidxxx ";
				$qNotas .= "FROM $cAlfa.fpar0117 ";
				$qNotas .= "WHERE ";
				$qNotas .= "comidxxx = \"C\" AND ";
				$qNotas .= "comtipxx != \"AJUSTES\" ";
				$xNotas = f_MySql("SELECT","",$qNotas,$xConexion01,"");
				// f_Mensaje(__FILE__, __LINE__, $qNotas."~".mysql_num_rows($xNotas));
				while ($xRN = mysql_fetch_array($xNotas)) {
					$cNotas .= "\"{$xRN['comidxxx']}\",";
				}
				$cNotas = substr($cNotas,0,strlen($cNotas)-1);
			} else {
				//Buscando comprobantes marcados de Nota Debito
				$qNotas  = "SELECT ";
				$qNotas .= "CONCAT(comidxxx,\"-\",comcodxx) AS comidxxx ";
				$qNotas .= "FROM $cAlfa.fpar0117 ";
				$qNotas .= "WHERE ";
				$qNotas .= "comidxxx = \"D\" AND ";
				$qNotas .= "comtipxx != \"AJUSTES\" ";
				$xNotas = f_MySql("SELECT","",$qNotas,$xConexion01,"");
				// f_Mensaje(__FILE__, __LINE__, $qNotas."~".mysql_num_rows($xNotas));
				while ($xRN = mysql_fetch_array($xNotas)) {
					$cNotas .= "\"{$xRN['comidxxx']}\",";
				}
				$cNotas = substr($cNotas,0,strlen($cNotas)-1);
			}

			//Buscando las cuentas de retencion CREE
			$qRetCree  = "SELECT CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
			$qRetCree .= "FROM $cAlfa.fpar0115 ";
			$qRetCree .= "WHERE ";
			$qRetCree .= "pucgruxx LIKE \"23\" AND ";
			$qRetCree .= "pucterxx LIKE \"R\"  AND ";
			$qRetCree .= "pucdesxx LIKE \"%CREE%\" AND ";
			$qRetCree .= "regestxx = \"ACTIVO\" ";
			$xRetCree  = f_MySql("SELECT","",$qRetCree,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qRetCree."~".mysql_num_rows($xRetCree));
			$mRetCree = array();
			$cCueRetCree = "";
			while ($xRRC = mysql_fetch_array($xRetCree)){
				$mRetCree[count($mRetCree)] = $xRRC['pucidxxx'];
				$cCueRetCree .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) LIKE \"{$xRRC['pucidxxx']}\" OR ";
			}
			$cCueRetCree = substr($cCueRetCree, 0, strlen($cCueRetCree)-4);

			//Buscano conceptos de causaciones automaticas
			$qPCC121  = "SELECT * ";
			$qPCC121 .= "FROM $cAlfa.fpar0121";
			$xPCC121 = f_MySql("SELECT","",$qPCC121,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qPCC121."~".mysql_num_rows($xPCC121));
			$cPCC121 = ""; $mCtoDes = array();
			while($xRCP121 = mysql_fetch_array($xPCC121)) {
				$cPCC121 .= "\"{$xRCP121['pucidxxx']}~{$xRCP121['ctoidxxx']}\",";
				$mCtoDes["{$xRCP121['ctoidxxx']}"] = $xRCP121;
			}

			//Buscando conceptos
			$qCtoPCC  = "SELECT DISTINCT * ";
			$qCtoPCC .= "FROM $cAlfa.fpar0119 ";
			$qCtoPCC .= "WHERE ctopccxx = \"SI\"";
			$xCtoPCC  = f_MySql("SELECT","",$qCtoPCC,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qCtoPCC."~".mysql_num_rows($xCtoPCC));
			$cCtoPCC = "";
			while($xRCAP = mysql_fetch_array($xCtoPCC)) {
				$cCtoPCC .= "\"{$xRCAP['pucidxxx']}~{$xRCAP['ctoidxxx']}\",";
				$mCtoDes["{$xRCAP['ctoidxxx']}"] = $xRCAP;
			}
			$cCtoPCC = $cPCC121.substr($cCtoPCC,0,strlen($cCtoPCC)-1);

			//Cuentas puc
			$qPucDat  = "SELECT *, ";
			$qPucDat .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) AS pucidxxx ";
			$qPucDat .= "FROM $cAlfa.fpar0115 ";
			$xPucDat  = f_MySql("SELECT","",$qPucDat,$xConexion01,"");
			// f_Mensaje(__FILE__,__LINE__,$qPucDat."~".mysql_num_rows($xPucDat));
			$mCuenta = array();
			while($xRPD = mysql_fetch_array($xPucDat)) {
				$mCuenta["{$xRPD['pucidxxx']}"] = $xRPD;
			}

			/*** Armando Cabecera del Insert para insertar la data del moviento contable en la tabla temporal ***/
			$qCabMov  = "INSERT INTO $cAlfa.$mReturnTablaM[1] (";
			$qCabMov .= "comidxxx,"; //Id del Comprobante
			$qCabMov .= "comcodxx,"; //Codigo del Comprobante
			$qCabMov .= "comcscxx,"; //Consecutivo Uno del Comprobante
			$qCabMov .= "comcsc2x,"; //Consecutivo Dos del Comprobante
			$qCabMov .= "comcsc3x,"; //Consecutivo Tres
			$qCabMov .= "comseqxx,"; //Secuencia del Comprobante
			$qCabMov .= "comfecxx,"; //Fecha del Comprobante
			$qCabMov .= "teridxxx,"; //Id del Cliente
			$qCabMov .= "clinomxx,"; //Nombre del Cliente
			$qCabMov .= "terid2xx,"; //Id del Proveedor
			$qCabMov .= "pronomxx,"; //Nombre del Proveedor
			$qCabMov .= "sucidxxx,"; //Id de la Sucursal Operativa
			$qCabMov .= "docidxxx,"; //Id del DO
			$qCabMov .= "docsufxx,"; //Sufijo del DO
			$qCabMov .= "comidcxx,"; //Id de la Factura
			$qCabMov .= "comcodcx,"; //Codigo de la Factura
			$qCabMov .= "comcsccx,"; //Consecutivo Uno de la Factura
			$qCabMov .= "comcscc2,"; //Consecutivo Dos de la Factura
			$qCabMov .= "comfeccx,"; //Fecha de la Factura
			$qCabMov .= "comidaxx,"; //Id del Comprobante Afectado (ND-NC)
			$qCabMov .= "comcodax,"; //Codigo del Comprobante Afectado (ND-NC)
			$qCabMov .= "comcscax,"; //Consecutivo Uno del Comprobante Afectado (ND-NC)
			$qCabMov .= "comcsca2,"; //Consecutivo Dos del Comprobante Afectado (ND-NC)
			$qCabMov .= "ctoidxxx,"; //Id Concepto Contable del Comprobante
			$qCabMov .= "ctodesxx,"; //Descripcion Concepto Contable del Comprobante
			$qCabMov .= "comdocin,"; //Documento Informativo
			$qCabMov .= "comvlrxx,"; //Valor del Comprobante
			$qCabMov .= "comvlr01,"; //Valor sin Iva
			$qCabMov .= "comvlr02,"; //Iva
			$qCabMov .= "regusrxx,"; //Usuario que Creo el Registro
			$qCabMov .= "regfcrex,"; //Fecha de Creacion del Registro
			$qCabMov .= "reghcrex,"; //Hora de Creacion del Registro
			$qCabMov .= "regfmodx,"; //Fecha de Modificacion del Registro
			$qCabMov .= "reghmodx,"; //Hora de Modificacion del Registro
			$qCabMov .= "regestxx) VALUES "; //Estado del Registro

			/*** Armando Cabecera del Insert para insertar retenciones en la tabla temporal ***/
			$qCabRet  = "INSERT INTO $cAlfa.$mReturnTablaR[1] (";
			$qCabRet .= "doiidxxx,";
			$qCabRet .= "comidsxx,";
			$qCabRet .= "comcodsx,";
			$qCabRet .= "comcscsx,";
			$qCabRet .= "comcsc2s,";
			$qCabRet .= "comseqsx,";
			$qCabRet .= "comfecxx,";
			$qCabRet .= "terid2sx,";
			$qCabRet .= "pucidsxx,";
			$qCabRet .= "ctoidsxx,";
			$qCabRet .= "sucidsxx,";
			$qCabRet .= "docidsxx,";
			$qCabRet .= "docsufsx,";
			$qCabRet .= "sccidsxx,";
			$qCabRet .= "comidcxx,";
			$qCabRet .= "comcodcx,";
			$qCabRet .= "comcsccx,";
			$qCabRet .= "comcscc2,";
			$qCabRet .= "comfeccx,";
			$qCabRet .= "teridxxx,";
			$qCabRet .= "terid2xx,";
			$qCabRet .= "retenxxx,";
			$qCabRet .= "pucidxxx,";
			$qCabRet .= "pucretxx,";
			$qCabRet .= "comvlrxx,";
			$qCabRet .= "comvlr01) VALUES ";


			$nAnioD  = substr($pArrayParametros['dFecIni'], 0, 4); 
			$nAnioH  = substr($pArrayParametros['dFecFin'], 0, 4); 
			$nAnoIni = (($nAnioD - 1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : ($nAnioD - 1);

			for($cNewYear=$nAnioD;$cNewYear<=$nAnioH;$cNewYear++) {
				$qCocDat  = "SELECT ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comidxxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comcodxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comcscxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comcsc2x, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comseqxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.pucidxxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.ctoidxxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.teridxxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.terid2xx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.commovxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.puctipej, ";
				if ($pArrayParametros['cTipCom'] == 'NC') {
					$qCocDat .= "IF($cAlfa.fcod$cNewYear.commovxx = \"C\",$cAlfa.fcod$cNewYear.comvlrxx,$cAlfa.fcod$cNewYear.comvlrxx*-1) AS comvlrxx, ";
					$qCocDat .= "IF($cAlfa.fcod$cNewYear.commovxx = \"C\",$cAlfa.fcod$cNewYear.comvlr01,$cAlfa.fcod$cNewYear.comvlr01*-1) AS comvlr01, ";
					$qCocDat .= "IF($cAlfa.fcod$cNewYear.commovxx = \"C\",$cAlfa.fcod$cNewYear.comvlr02,$cAlfa.fcod$cNewYear.comvlr02*-1) AS comvlr02, ";
					$qCocDat .= "IF($cAlfa.fcod$cNewYear.commovxx = \"C\",$cAlfa.fcod$cNewYear.comvlrme,$cAlfa.fcod$cNewYear.comvlrme*-1) AS comvlrme, ";
					$qCocDat .= "IF($cAlfa.fcod$cNewYear.commovxx = \"C\",$cAlfa.fcod$cNewYear.comvlrne,$cAlfa.fcod$cNewYear.comvlrne*-1) AS comvlrne, ";
				} else {
					$qCocDat .= "IF($cAlfa.fcod$cNewYear.commovxx = \"C\",$cAlfa.fcod$cNewYear.comvlrxx,$cAlfa.fcod$cNewYear.comvlrxx*-1) AS comvlrxx, ";
					$qCocDat .= "IF($cAlfa.fcod$cNewYear.commovxx = \"C\",$cAlfa.fcod$cNewYear.comvlr01,$cAlfa.fcod$cNewYear.comvlr01*-1) AS comvlr01, ";
					$qCocDat .= "IF($cAlfa.fcod$cNewYear.commovxx = \"C\",$cAlfa.fcod$cNewYear.comvlr02,$cAlfa.fcod$cNewYear.comvlr02*-1) AS comvlr02, ";
					$qCocDat .= "IF($cAlfa.fcod$cNewYear.commovxx = \"C\",$cAlfa.fcod$cNewYear.comvlrme,$cAlfa.fcod$cNewYear.comvlrme*-1) AS comvlrme, ";
					$qCocDat .= "IF($cAlfa.fcod$cNewYear.commovxx = \"C\",$cAlfa.fcod$cNewYear.comvlrne,$cAlfa.fcod$cNewYear.comvlrne*-1) AS comvlrne, ";
				}
				$qCocDat .= "$cAlfa.fcod$cNewYear.comcsccx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comseqcx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.sucidxxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.docidxxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.docsufxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comfecxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comfacxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comdocin, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.ctodesxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comobsxx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comctocx, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comidc2x, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comcodc2, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comcscc2, ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comseqc2, ";
				$qCocDat .= "$cAlfa.fcoc$cNewYear.comobs2x, ";
				$qCocDat .= "IF(CLIENTE.CLINOMXX != \"\",CLIENTE.CLINOMXX,CONCAT(CLIENTE.CLINOM1X,\" \",CLIENTE.CLINOM2X,\" \",CLIENTE.CLIAPE1X,\" \",CLIENTE.CLIAPE2X)) AS clinomxx, ";
				$qCocDat .= "IF($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X)) AS pronomxx ";
				$qCocDat .= "FROM $cAlfa.fcod$cNewYear ";
				$qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 AS CLIENTE ON $cAlfa.fcod$cNewYear.teridxxx = CLIENTE.CLIIDXXX ";
				$qCocDat .= "LEFT JOIN $cAlfa.SIAI0150 ON $cAlfa.fcod$cNewYear.terid2xx = $cAlfa.SIAI0150.CLIIDXXX ";
				$qCocDat .= "LEFT JOIN $cAlfa.fcoc$cNewYear ON $cAlfa.fcod$cNewYear.comidxxx = $cAlfa.fcoc$cNewYear.comidxxx AND $cAlfa.fcod$cNewYear.comcodxx = $cAlfa.fcoc$cNewYear.comcodxx AND $cAlfa.fcod$cNewYear.comcscxx = $cAlfa.fcoc$cNewYear.comcscxx AND $cAlfa.fcod$cNewYear.comcsc2x = $cAlfa.fcoc$cNewYear.comcsc2x ";
				$qCocDat .= "WHERE ";
				$qCocDat .= "CONCAT($cAlfa.fcod$cNewYear.comidxxx,\"-\",$cAlfa.fcod$cNewYear.comcodxx) IN ($cNotas) AND ";
				$qCocDat .= "CONCAT($cAlfa.fcod$cNewYear.pucidxxx,\"~\",$cAlfa.fcod$cNewYear.ctoidxxx) IN ($cCtoPCC) AND ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.teridxxx = \"{$pArrayParametros['cNit']}\" AND ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.comfecxx BETWEEN  \"{$pArrayParametros['dFecIni']}\" AND \"{$pArrayParametros['dFecFin']}\" AND ";
				$qCocDat .= "$cAlfa.fcod$cNewYear.regestxx = \"ACTIVO\" ";
				$qCocDat .= "ORDER BY $cAlfa.fcod$cNewYear.regestxx, $cAlfa.fcod$cNewYear.teridxxx, $cAlfa.fcod$cNewYear.comidxxx,$cAlfa.fcod$cNewYear.comcodxx,$cAlfa.fcod$cNewYear.comcscxx,$cAlfa.fcod$cNewYear.comfecxx";
				$xCocDat  = f_MySql("SELECT","",$qCocDat,$xConexion01,"");
				// f_Mensaje(__FILE__,__LINE__,$qCocDat."~".mysql_num_rows($xCocDat));
				$qInsert = ""; $nCanReg = 0;
				while ($xRCD = mysql_fetch_array($xCocDat)) {
					//Pagos a terceros de las Notas
					if ($xRCD['comctocx'] == "PCC" && $xRCD['comidc2x'] != "X" && $xRCD['comidc2x'] != "") {
						//Buscando datos del comprobante
						$vPcc = array();
						for($nAnoAux = $nAnioH; $nAnoAux >= $vSysStr['financiero_ano_instalacion_modulo']; $nAnoAux--) {
							$qPcc = "SELECT comidxxx, comcodxx, comcscxx, comcsc2x, comseqxx, teridxxx, terid2xx, comfecxx ";
							$qPcc .= "FROM $cAlfa.fcod$nAnoAux ";
							$qPcc .= "WHERE ";
							$qPcc .= "comidxxx = \"{$xRCD['comidc2x']}\" AND ";
							$qPcc .= "comcodxx = \"{$xRCD['comcodc2']}\" AND ";
							$qPcc .= "comcscxx = \"{$xRCD['comcscc2']}\" AND ";
							$qPcc .= "ctoidxxx = \"{$xRCD['ctoidxxx']}\" AND ";
							$qPcc .= "teridxxx = \"{$xRCD['teridxxx']}\" AND ";               
							$qPcc .= "terid2xx = \"{$xRCD['terid2xx']}\" AND ";                
							$qPcc .= "sucidxxx = \"{$xRCD['sucidxxx']}\" AND ";
							$qPcc .= "docidxxx = \"{$xRCD['docidxxx']}\" AND ";
							$qPcc .= "docsufxx = \"{$xRCD['docsufxx']}\" LIMIT 0,1 ";
							$xPcc  = f_MySql("SELECT","",$qPcc,$xConexion01,"");
							// f_Mensaje(__FILE__, __LINE__, $qPcc."~".mysql_num_rows($xPcc));
							if (mysql_num_rows($xPcc) > 0) {
								$vPcc = mysql_fetch_array($xPcc);
								$nAnoAux = $vSysStr['financiero_ano_instalacion_modulo'] - 1;
							}
						}

						//Descripcion
						$vCtoDes = explode("^",($xRCD['ctodesxx'] != "") ? $xRCD['ctodesxx'] : $xRCD['comobsxx']); // Observacion campo memo separado por ^: 0=>observacion 1=> nombre proveedor 2=> nit proveedor

						//Trayendo descripcion del concepto
						$cCtoDes = trim($vCtoDes[0]);

						//Valor sin Iva
						$nBase = (($xRCD['comvlr01'] == 0) ? $xRCD['comvlrxx'] : $xRCD['comvlr01']);

						//Documento afectado
						$vDocAfe = explode("~", $xRCD['comobs2x']);

						$qInsert .= "(";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['comidc2x'])."\",";  //Id del Comprobante
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['comcodc2'])."\",";  //Codigo del Comprobante
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['comcscc2'])."\",";  //Consecutivo Uno del Comprobante
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$vPcc['comcsc2x'])."\",";  //Consecutivo Dos del Comprobante
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$vPcc['comcsc3x'])."\",";  //Consecutivo Tres
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$vPcc['comseqxx'])."\",";  //Secuencia del Comprobante
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$vPcc['comfecxx'])."\",";  //Fecha del Comprobante
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['teridxxx'])."\",";  //Id del Cliente
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['clinomxx'])."\",";  //Nombre del Cliente
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['terid2xx'])."\",";  //Id del Proveedor
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['pronomxx'])."\",";  //Nombre del Proveedor
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['sucidxxx'])."\",";  //Id de la Sucursal Operativa
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['docidxxx'])."\",";  //Id del DO
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['docsufxx'])."\",";  //Sufijo del DO
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['comidxxx'])."\",";  //Id de la Factura
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['comcodxx'])."\",";  //Codigo de la Factura
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['comcscxx'])."\",";  //Consecutivo Uno de la Factura
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['comcsc2x'])."\",";  //Consecutivo Dos de la Factura
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['comfecxx'])."\",";  //Fecha de la Factura
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$vDocAfe[1])."\",";        //Id del Comprobante Afectado (ND-NC)
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$vDocAfe[2])."\",";        //Codigo del Comprobante Afectado (ND-NC)
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$vDocAfe[3])."\",";        //Consecutivo Uno del Comprobante Afectado (ND-NC)
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$vDocAfe[4])."\",";        //Consecutivo Dos del Comprobante Afectado (ND-NC)
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['ctoidxxx'])."\",";  //Id Concepto Contable del Comprobante
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$cCtoDes)."\",";           //Descripcion Concepto Contable del Comprobante
						$qInsert .= "\"\",";                                                      //Documento Informativo
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['comvlrxx'])."\",";  //Valor del Comprobante
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$nBase)."\",";             //Valor sin Iva
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRCD['comvlr02'])."\",";  //Iva
						$qInsert .= "\"$kUser\",";                                                //Usuario que Creo el Registro
						$qInsert .= "\"".date('Y-m-d')."\",";                                     //Fecha de Creacion del Registro
						$qInsert .= "\"".date('H:i:s')."\",";                                     //Hora de Creacion del Registro
						$qInsert .= "\"".date('Y-m-d')."\",";                                     //Fecha de Modificacion del Registro
						$qInsert .= "\"".date('H:i:s')."\",";                                     //Hora de Modificacion del Registro
						$qInsert .= "\"ACTIVO\"";                                                 //Estado del Registro
						$qInsert .= "),";

						$nCanReg++;
						if (($nCanReg % _NUMREG_) == 0) { 
							$xConexion01 = fnReiniciarConexionDBPcc($xConexion01);

							/*** Organizo el Insert e ejecuto el script ***/
							$qInsert = substr($qInsert, 0, -1);
							$qInsert = $qCabMov.$qInsert;
							$xInsert = mysql_query($qInsert,$xConexion01);
							if (!$xInsert) {
								$nError = 1;
								$nSwitch = 1;
								$mReturn[count($mReturn)] = "Insert(".__LINE__."): ".$qInsert."~".mysql_error($xInsert)."~Error al Insertar en la Tabla Temporal. Favor Comunicarse con OpenTecnologia S.A.";
							}
							$qInsert = "";
						}
					}
				}

				if ($qInsert != "") {
					$xConexion01 = fnReiniciarConexionDBPcc($xConexion01);

					/*** Organizo el Insert e ejecuto el script ***/
					$qInsert = substr($qInsert, 0, -1);
					$qInsert = $qCabMov.$qInsert;
					$xInsert = mysql_query($qInsert,$xConexion01);
					if (!$xInsert) {
						$nError = 1;
						$nSwitch = 1;
						$mReturn[count($mReturn)] = "Insert(".__LINE__."): ".$qInsert."~".mysql_error($xInsert)."~Error al Insertar en la Tabla Temporal. Favor Comunicarse con OpenTecnologia S.A.";
					}
					$qInsert = "";
				}
			}
			/*****Fin select a CABECERA *****/
		}

		if ($nSwitch == 0) {
			//Trayendo retenciones del comprobante
			$qPcc  = "SELECT comidxxx, comcodxx, comcscxx, comcsc2x, comidcxx, comcodcx, comcsccx, comcscc2, comfeccx ";
			$qPcc .= "FROM $cAlfa.$mReturnTablaM[1] ";
			$qPcc .= "GROUP BY comidxxx, comcodxx, comcscxx, comcsc2x ";
			$xPcc  = f_MySql("SELECT","",$qPcc,$xConexion01,"");
			// f_Mensaje(__FILE__, __LINE__, $qPcc."~".mysql_num_rows($xPcc));
			$qInsert = ""; $nCanReg = 0;
			while ($xRP = mysql_fetch_array($xPcc)) {
				for($cNewYear=$nAnoIni;$cNewYear<=$nAnioH;$cNewYear++) {
					$cReteCree    = "";
					$cReteCreeInt = "";
					for($nRC=0; $nRC<count($mRetCree); $nRC++) {
						$cReteCree    .= "$cAlfa.fcod$cNewYear.pucidxxx LIKE \"{$mRetCree[$nRC]}\" OR ";
						$cReteCreeInt .= "$cAlfa.fcod$cNewYear.pucidinp LIKE \"{$mRetCree[$nRC]}\" OR ";
					}
					$cReteCree    = substr($cReteCree,    0, strlen($cReteCree)-4);
					$cReteCreeInt = substr($cReteCreeInt, 0, strlen($cReteCreeInt)-4);

					//Se trae todo el comprobante y se busca
					$qCodDat  = "SELECT $cAlfa.fcod$cNewYear.* ";
					$qCodDat .= "FROM $cAlfa.fcod$cNewYear ";
					$qCodDat .= "WHERE ";
					$qCodDat .= "$cAlfa.fcod$cNewYear.comidxxx = \"{$xRP['comidxxx']}\" AND ";
					$qCodDat .= "$cAlfa.fcod$cNewYear.comcodxx = \"{$xRP['comcodxx']}\" AND ";
					$qCodDat .= "$cAlfa.fcod$cNewYear.comcscxx = \"{$xRP['comcscxx']}\" AND ";
					$qCodDat .= "$cAlfa.fcod$cNewYear.comcsc2x = \"{$xRP['comcsc2x']}\" AND ";
					$qCodDat .= "(CONCAT($cAlfa.fcod$cNewYear.pucidxxx,\"~\",$cAlfa.fcod$cNewYear.ctoidxxx) IN ($cCtoPCC) OR ";
					$qCodDat .= "$cAlfa.fcod$cNewYear.pucidxxx LIKE \"2367%\" OR $cAlfa.fcod$cNewYear.pucidxxx LIKE \"2368%\" OR ";
					$qCodDat .= "$cAlfa.fcod$cNewYear.pucidxxx LIKE \"2365%\"".(($cReteCree != "") ? " OR $cReteCree": "").") AND ";
					$qCodDat .= "$cAlfa.fcod$cNewYear.regestxx = \"ACTIVO\" ORDER BY ABS($cAlfa.fcod$cNewYear.comseqxx) ";
					$xCodDat  = f_MySql("SELECT","",$qCodDat,$xConexion01,"");
					// f_Mensaje(__FILE__,__LINE__,$qCodDat."~".mysql_num_rows($xCodDat));
					//Se trae todo el comprobante y se busca cada retencion a que servico se le asocia
					//Si no se asocia a ningun servicio se le asigna al prime DO
					$mAuxRet = array(); $mAuxPcc = array();
					while ($xRCD = mysql_fetch_array($xCodDat)) {
						if(in_array($xRCD['pucidxxx'],$mRetCree) == true) { //ReteCree
							$xRCD['retenxxx']  = 'ReteCree';
							$mAuxRet[count($mAuxRet)] = $xRCD;
						} elseif(substr($xRCD['pucidxxx'],0,4) == '2365') { //Retefuente
							$xRCD['retenxxx']  = 'Retefuente';
							$mAuxRet[count($mAuxRet)] = $xRCD;
						}elseif(substr($xRCD['pucidxxx'],0,4) == '2367') { //ReteIva
							$xRCD['retenxxx']  = 'ReteIva';
							$mAuxRet[count($mAuxRet)] = $xRCD;
						}elseif(substr($xRCD['pucidxxx'],0,4) == '2368') { //ReteIca
							$xRCD['retenxxx']  = 'ReteIca';
							$mAuxRet[count($mAuxRet)] = $xRCD;
						} else {
							$xRCD['retencre'] = ""; //ReteCree
							$xRCD['retenrft'] = ""; //Retefuente
							$xRCD['retenriv'] = ""; //ReteIva
							$xRCD['retenric'] = ""; //ReteIca
							$mAuxPcc[count($mAuxPcc)] = $xRCD;
						}
					}

					//Buscando a que servicio aplica la retencion
					for($nA=0; $nA<count($mAuxRet); $nA++) {
						//Buscando el porcentaje de retencion
						$mAuxRet[$nA]['pucretxx'] = $mCuenta["{$mAuxRet[$nA]['pucidxxx']}"]['pucretxx'];

						//Buscando en todos los servicios
						for ($nB=0; $nB<count($mAuxPcc); $nB++) {
							//si la base del servicio es igual a la base de retencion del impuesto se asocia ese impuesto a ese servicio
							//Tambien se asigna la retencion al servicio segun el DO
							$nIncRet = 0;
							if ($mAuxRet[$nA]['retenxxx'] == "ReteIva") {
								//Si el pago a tercero ya tiene reteiva no se tiene en cuenta para este registro
								if ($mAuxPcc[$nB]['retenriv'] == "") {
									//Para el ReteIva primero se verifica tomando como base el IVA
									$nBase = $mAuxPcc[$nB]['comvlr02'];
									if (($mAuxRet[$nA]['docidxxx'] == "" || $mAuxRet[$nA]['docidxxx'] == $mAuxPcc[$nB]['docidxxx']) &&
											$mAuxRet[$nA]['comvlr01'] == $nBase) {
										$nIncRet = 1;
									}

									//Despues se verifica tomando como base la base del concepto
									if ($nIncRet == 0) {
										$nBase = $mAuxPcc[$nB]['comvlr01'];
										if (($mAuxRet[$nA]['docidxxx'] == "" || $mAuxRet[$nA]['docidxxx'] == $mAuxPcc[$nB]['docidxxx']) &&
												$mAuxRet[$nA]['comvlr01'] == $nBase) {
											$nIncRet = 1;
										}
									}

									//Despues se verifica tomando como base el valor del concepto
									if ($nIncRet == 0) {
										$nBase = $mAuxPcc[$nB]['comvlrxx'];
										if (($mAuxRet[$nA]['docidxxx'] == "" || $mAuxRet[$nA]['docidxxx'] == $mAuxPcc[$nB]['docidxxx']) &&
												$mAuxRet[$nA]['comvlr01'] == $nBase) {
											$nIncRet = 1;
										}
									}
								}
							} else {

								$nAnalizar = 0;
								//Si el pago a tercero ya tiene ReteIva no se tiene en cuenta para este registro
								if ($mAuxRet[$nA]['retenxxx'] == "ReteIva" && $mAuxPcc[$nB]['retenriv'] != "") {
									$nAnalizar = 1;
								}

								//Si el pago a tercero ya tiene ReteCree no se tiene en cuenta para este registro
								if ($mAuxRet[$nA]['retenxxx'] == "ReteCree" && $mAuxPcc[$nB]['retencre'] != "") {
									$nAnalizar = 1;
								}

								//Si el pago a tercero ya tiene Retefuente no se tiene en cuenta para este registro
								if ($mAuxRet[$nA]['retenxxx'] == "Retefuente" && $mAuxPcc[$nB]['retenrft'] != "") {
									$nAnalizar = 1;
								}

								//Si el pago a tercero ya tiene ReteIca no se tiene en cuenta para este registro
								if ($mAuxRet[$nA]['retenxxx'] == "ReteIca" && $mAuxPcc[$nB]['retenric'] != "") {
									$nAnalizar = 1;
								}

								if ($nAnalizar == 0) {
									//Buscando retenciones, comparando base uno a uno
									$nBase = ($mAuxPcc[$nB]['comvlr01'] > 0) ? $mAuxPcc[$nB]['comvlr01'] : $mAuxPcc[$nB]['comvlrxx'];
									if (($mAuxRet[$nA]['docidxxx'] == "" || $mAuxRet[$nA]['docidxxx'] == $mAuxPcc[$nB]['docidxxx']) &&
											$mAuxRet[$nA]['comvlr01'] == $nBase) {
										$nIncRet = 1;
									}
								}
							}

							if ($nIncRet == 1) {
								if ($mAuxRet[$nA]['retenxxx'] == "ReteIva") {
									$mAuxPcc[$nB]['retenriv'] = $mAuxRet[$nA]['retenxxx'];
								}

								if ($mAuxRet[$nA]['retenxxx'] == "ReteCree") {
									$mAuxPcc[$nB]['retencre'] = $mAuxRet[$nA]['retenxxx'];
								}

								if ($mAuxRet[$nA]['retenxxx'] == "Retefuente") {
									$mAuxPcc[$nB]['retenrft'] = $mAuxRet[$nA]['retenxxx'];
								}

								if ($mAuxRet[$nA]['retenxxx'] == "ReteIca") {
									$mAuxPcc[$nB]['retenric'] = $mAuxRet[$nA]['retenxxx'];
								}

								$mAuxRet[$nA]['comidsxx'] = $mAuxPcc[$nB]['comidxxx'];
								$mAuxRet[$nA]['comcodsx'] = $mAuxPcc[$nB]['comcodxx'];
								$mAuxRet[$nA]['comcscsx'] = $mAuxPcc[$nB]['comcscxx'];
								$mAuxRet[$nA]['comcsc2s'] = $mAuxPcc[$nB]['comcsc2x'];
								$mAuxRet[$nA]['comseqsx'] = $mAuxPcc[$nB]['comseqxx'];
								$mAuxRet[$nA]['terid2sx'] = $mAuxPcc[$nB]['terid2xx'];
								$mAuxRet[$nA]['pucidsxx'] = $mAuxPcc[$nB]['pucidxxx'];
								$mAuxRet[$nA]['ctoidsxx'] = $mAuxPcc[$nB]['ctoidxxx'];
								$mAuxRet[$nA]['sucidsxx'] = $mAuxPcc[$nB]['sucidxxx'];
								$mAuxRet[$nA]['docidsxx'] = $mAuxPcc[$nB]['docidxxx'];
								$mAuxRet[$nA]['docsufsx'] = $mAuxPcc[$nB]['docsufxx'];
								$mAuxRet[$nA]['sccidsxx'] = $mAuxPcc[$nB]['sccidxxx'];
								$nB = count($mAuxPcc);
							}
						}
					}

					for($nA=0; $nA<count($mAuxRet); $nA++) {
						if ($mAuxRet[$nA]['comidsxx'] == "" && $mAuxRet[$nA]['comcodsx'] == "" && $mAuxRet[$nA]['comcscsx'] == "" && $mAuxRet[$nA]['docidsxx'] == "") {
							//Asigno la retencion al primer DO
							$mAuxRet[$nA]['comidsxx'] = $mAuxPcc[0]['comidxxx'];
							$mAuxRet[$nA]['comcodsx'] = $mAuxPcc[0]['comcodxx'];
							$mAuxRet[$nA]['comcscsx'] = $mAuxPcc[0]['comcscxx'];
							$mAuxRet[$nA]['comcsc2s'] = $mAuxPcc[0]['comcsc2x'];
							$mAuxRet[$nA]['comseqsx'] = $mAuxPcc[0]['comseqxx'];
							$mAuxRet[$nA]['terid2sx'] = $mAuxPcc[0]['terid2xx'];
							$mAuxRet[$nA]['pucidsxx'] = $mAuxPcc[0]['pucidxxx'];
							$mAuxRet[$nA]['ctoidsxx'] = $mAuxPcc[0]['ctoidxxx'];
							$mAuxRet[$nA]['sucidsxx'] = $mAuxPcc[0]['sucidxxx'];
							$mAuxRet[$nA]['docidsxx'] = $mAuxPcc[0]['docidxxx'];
							$mAuxRet[$nA]['docsufsx'] = $mAuxPcc[0]['docsufxx'];
							$mAuxRet[$nA]['sccidsxx'] = $mAuxPcc[0]['sccidxxx'];
						}

						if($mAuxRet[$nA]['comvlr01'] == 0){
							$mAuxRet[$nA]['comvlr01'] = ($mAuxRet[$nA]['comvlrxx'] * 100)/ $mAuxRet[$nA]['pucretxx'];
						}

						//Tabla temporal para retenciones
						$qInsert .= "(";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['docidsxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['comidsxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['comcodsx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['comcscsx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['comcsc2s'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['comseqsx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['comfecxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['terid2sx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['pucidsxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['ctoidsxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['sucidsxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['docidsxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['docsufsx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['sccidsxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRP['comidcxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRP['comcodcx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRP['comcsccx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRP['comcscc2'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$xRP['comfeccx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['teridxxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['terid2xx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['retenxxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['pucidxxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['pucretxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['comvlrxx'])."\",";
						$qInsert .= "\"".str_replace($vBuscar,$vReempl,$mAuxRet[$nA]['comvlr01'])."\"";
						$qInsert .= "),";

						$nCanReg++;
						if (($nCanReg % _NUMREG_) == 0) { 
							$xConexion01 = fnReiniciarConexionDBPcc($xConexion01);

							/*** Organizo el Insert e ejecuto el script ***/
							$qInsert = substr($qInsert, 0, -1);
							$qInsert = $qCabRet.$qInsert;
							$xInsert = mysql_query($qInsert,$xConexion01);
							if (!$xInsert) {
								$nError = 1;
								$nSwitch = 1;
								$mReturn[count($mReturn)] = "Insert(".__LINE__."): ".$qInsert."~".mysql_error($xInsert)."~Error al Insertar en la Tabla Temporal. Favor Comunicarse con OpenTecnologia S.A.";
							}
							$qInsert = "";
						}
					}//for($nA=0; $nA<count($mAuxRet); $nA++) {
				}//for($cNewYear=$nAnoIni;$cNewYear<=$nAnioH;$cNewYear++) {
			}//while ($xRP = mysql_fetch_array($xPcc)) {

			if ($qInsert != "") {
				$xConexion01 = fnReiniciarConexionDBPcc($xConexion01);

				/*** Organizo el Insert e ejecuto el script ***/
				$qInsert = substr($qInsert, 0, -1);
				$qInsert = $qCabRet.$qInsert;
				$xInsert = mysql_query($qInsert,$xConexion01);
				if (!$xInsert) {
					$nError = 1;
					$nSwitch = 1;
					$mReturn[count($mReturn)] = "Insert(".__LINE__."): ".$qInsert."~".mysql_error($xInsert)."~Error al Insertar en la Tabla Temporal. Favor Comunicarse con OpenTecnologia S.A.";
				}
				$qInsert = "";
			}

			//Cabecera Insert Pcc
			$qCabMov = str_replace("$cAlfa.{$mReturnTablaM[1]}","$cAlfa.{$pArrayParametros['cTablaCab']}",$qCabMov);

			//Agrupando y ordenando por DO, Factura, Tercero, Documento, Concepto
			$qPcc  = "SELECT * ";
			$qPcc .= "FROM $cAlfa.$mReturnTablaM[1] ";
			$qPcc .= "GROUP BY docidxxx, comcsccx, terid2xx, comidxxx, comcodxx, comcscxx, comcsc2x, ctoidxxx";
			$xPcc  = f_MySql("SELECT","",$qPcc,$xConexion01,"");
			// f_Mensaje(__FILE__, __LINE__, $qPcc."~".mysql_num_rows($xPcc));
			$qInsert = ""; $nCanReg = 0;
			while ($xRP = mysql_fetch_array($xPcc)) {
				//PCC
				$qInsert .= "(";
				$qInsert .= "\"{$xRP['comidxxx']}\",";
				$qInsert .= "\"{$xRP['comcodxx']}\",";
				$qInsert .= "\"{$xRP['comcscxx']}\",";
				$qInsert .= "\"{$xRP['comcsc2x']}\",";
				$qInsert .= "\"{$xRP['comcsc3x']}\",";
				$qInsert .= "\"{$xRP['comseqxx']}\",";
				$qInsert .= "\"{$xRP['comfecxx']}\",";
				$qInsert .= "\"{$xRP['teridxxx']}\",";
				$qInsert .= "\"{$xRP['clinomxx']}\",";
				$qInsert .= "\"{$xRP['terid2xx']}\",";
				$qInsert .= "\"{$xRP['pronomxx']}\",";
				$qInsert .= "\"{$xRP['sucidxxx']}\",";
				$qInsert .= "\"{$xRP['docidxxx']}\",";
				$qInsert .= "\"{$xRP['docsufxx']}\",";
				$qInsert .= "\"{$xRP['comidcxx']}\",";
				$qInsert .= "\"{$xRP['comcodcx']}\",";
				$qInsert .= "\"{$xRP['comcsccx']}\",";
				$qInsert .= "\"{$xRP['comcscc2']}\",";
				$qInsert .= "\"{$xRP['comfeccx']}\",";
				$qInsert .= "\"{$xRP['comidaxx']}\",";
				$qInsert .= "\"{$xRP['comcodax']}\",";
				$qInsert .= "\"{$xRP['comcscax']}\",";
				$qInsert .= "\"{$xRP['comcsca2']}\",";
				$qInsert .= "\"{$xRP['ctoidxxx']}\",";
				$qInsert .= "\"{$xRP['ctodesxx']}\",";
				$qInsert .= "\"{$xRP['comdocin']}\",";
				$qInsert .= "\"{$xRP['comvlrxx']}\",";
				$qInsert .= "\"{$xRP['comvlr01']}\",";
				$qInsert .= "\"{$xRP['comvlr02']}\",";
				$qInsert .= "\"{$xRP['regusrxx']}\",";
				$qInsert .= "\"{$xRP['regfcrex']}\",";
				$qInsert .= "\"{$xRP['reghcrex']}\",";
				$qInsert .= "\"{$xRP['regfmodx']}\",";
				$qInsert .= "\"{$xRP['reghmodx']}\",";
				$qInsert .= "\"{$xRP['regestxx']}\"";
				$qInsert .= "),";

				$nCanReg++;
				if (($nCanReg % _NUMREG_) == 0) { 
					$xConexion01 = fnReiniciarConexionDBPcc($xConexion01);

					/*** Organizo el Insert e ejecuto el script ***/
					$qInsert = substr($qInsert, 0, -1);
					$qInsert = $qCabMov.$qInsert;
					$xInsert = mysql_query($qInsert,$xConexion01);
					if (!$xInsert) {
						$nError = 1;
						$nSwitch = 1;
						$mReturn[count($mReturn)] = "Insert(".__LINE__."): ".$qInsert."~".mysql_error($xInsert)."~Error al Insertar en la Tabla Temporal. Favor Comunicarse con OpenTecnologia S.A.";
					}
					$qInsert = "";
				}
			}

			if ($qInsert != "") {
				$xConexion01 = fnReiniciarConexionDBPcc($xConexion01);

				/*** Organizo el Insert e ejecuto el script ***/
				$qInsert = substr($qInsert, 0, -1);
				$qInsert = $qCabMov.$qInsert;
				$xInsert = mysql_query($qInsert,$xConexion01);
				if (!$xInsert) {
					$nError = 1;
					$nSwitch = 1;
					$mReturn[count($mReturn)] = "Insert(".__LINE__."): ".$qInsert."~".mysql_error($xInsert)."~Error al Insertar en la Tabla Temporal. Favor Comunicarse con OpenTecnologia S.A.";
				}
				$qInsert = "";
			}

			//Cabecera Insert Retenciones
			$qCabRet  = "INSERT INTO $cAlfa.{$pArrayParametros['cTablaRet']} (";
			$qCabRet .= "pccidxxx,";
			$qCabRet .= "teridxxx,";
			$qCabRet .= "rettipxx,";
			$qCabRet .= "comvlrxx,";
			$qCabRet .= "comvlr01,";
			$qCabRet .= "retporxx,";
			$qCabRet .= "regusrxx,";
			$qCabRet .= "regfcrex,";
			$qCabRet .= "reghcrex,";
			$qCabRet .= "regfmodx,";
			$qCabRet .= "reghmodx,";
			$qCabRet .= "regestxx) VALUES ";

			$qPcc  = "SELECT pccidxxx, teridxxx, comidxxx, comcodxx, comcscxx, comcsc2x, comseqxx, comvlrxx, comvlr01, comvlr02, regusrxx, regfcrex, reghcrex, regfmodx, reghmodx, regestxx ";
			$qPcc .= "FROM $cAlfa.{$pArrayParametros['cTablaCab']} ";
			$xPcc  = f_MySql("SELECT","",$qPcc,$xConexion01,"");
			// f_Mensaje(__FILE__, __LINE__, $qPcc."~".mysql_num_rows($xPcc));
			$qInsert = ""; $nCanReg = 0;
			while ($xRP = mysql_fetch_array($xPcc)) {
				//Retenciones del comprobante
				$qRet  = "SELECT * ";
				$qRet .= "FROM $cAlfa.$mReturnTablaR[1] ";
				$qRet .= "WHERE ";
				$qRet .= "comidsxx = \"{$xRP['comidxxx']}\" AND ";
				$qRet .= "comcodsx = \"{$xRP['comcodxx']}\" AND ";
				$qRet .= "comcscsx = \"{$xRP['comcscxx']}\" AND ";
				$qRet .= "comcsc2s = \"{$xRP['comcsc2x']}\" AND ";
				$qRet .= "comseqsx = \"{$xRP['comseqxx']}\" ";
				$xRet  = f_MySql("SELECT","",$qRet,$xConexion01,"");
				// f_Mensaje(__FILE__, __LINE__, $qRet."~".mysql_num_rows($xRet));
				while ($xRR = mysql_fetch_array($xRet)) {

					//Si el valor del pago es diferente al de la nota se debe re-calcular el valor de la retencion
					if ($xRR['rettipxx'] == "ReteIva") {
						if (round($xRP['comvlr02'],5) != round($xRR['comvlr01'],5)) {
							$nDec = (substr_count(($xRP['comvlr02']+0),".") > 0) ? 2 : 0;
							$xRR['comvlr01'] = $xRP['comvlr02'];
							$xRR['comvlrxx'] = round($xRP['comvlr02']*($xRR['pucretxx']/100),$nDec);
						}
					} else {
						//Retefuente y Reteica
						if (round($xRP['comvlr01'],5) != round($xRR['comvlr01'],5)) {
							$nDec = (substr_count(($xRP['comvlr01']+0),".") > 0) ? 2 : 0;
							$xRR['comvlr01'] = $xRP['comvlr01'];
							$xRR['comvlrxx'] = round($xRP['comvlr01']*($xRR['pucretxx']/100),$nDec);
						}
					}
					
					$qInsert .= "(";
					$qInsert .= "\"{$xRP['pccidxxx']}\",";
					$qInsert .= "\"{$xRP['teridxxx']}\",";
					$qInsert .= "\"{$xRR['retenxxx']}\",";
					$qInsert .= "\"{$xRR['comvlrxx']}\",";
					$qInsert .= "\"{$xRR['comvlr01']}\",";
					$qInsert .= "\"{$xRR['pucretxx']}\",";
					$qInsert .= "\"{$xRP['regusrxx']}\",";
					$qInsert .= "\"{$xRP['regfcrex']}\",";
					$qInsert .= "\"{$xRP['reghcrex']}\",";
					$qInsert .= "\"{$xRP['regfmodx']}\",";
					$qInsert .= "\"{$xRP['reghmodx']}\",";
					$qInsert .= "\"{$xRP['regestxx']}\"";
					$qInsert .= "),";

					$nCanReg++;
					if (($nCanReg % _NUMREG_) == 0) { 
						$xConexion01 = fnReiniciarConexionDBPcc($xConexion01);

						/*** Organizo el Insert e ejecuto el script ***/
						$qInsert = substr($qInsert, 0, -1);
						$qInsert = $qCabRet.$qInsert;
						$xInsert = mysql_query($qInsert,$xConexion01);
						if (!$xInsert) {
							$nError = 1;
							$nSwitch = 1;
							$mReturn[count($mReturn)] = "Insert(".__LINE__."): ".$qInsert."~".mysql_error($xInsert)."~Error al Insertar en la Tabla Temporal. Favor Comunicarse con OpenTecnologia S.A.";
						}
						$qInsert = "";
					}
				}
			}

			if ($qInsert != "") {
				$xConexion01 = fnReiniciarConexionDBPcc($xConexion01);

				/*** Organizo el Insert e ejecuto el script ***/
				$qInsert = substr($qInsert, 0, -1);
				$qInsert = $qCabRet.$qInsert;
				$xInsert = mysql_query($qInsert,$xConexion01);
				if (!$xInsert) {
					$nError = 1;
					$nSwitch = 1;
					$mReturn[count($mReturn)] = "Insert(".__LINE__."): ".$qInsert."~".mysql_error($xInsert)."~Error al Insertar en la Tabla Temporal. Favor Comunicarse con OpenTecnologia S.A.";
				}
				$qInsert = "";
			}
		}

		if($nSwitch == 0){
			$mReturn[0] = "true";
		}else{
			$mReturn[0] = "false";
		}

		return $mReturn;
	}
	
	/**
   * Metodo que se encarga de Crear las Estructuras de las Tablas Temporales
   */
	function fnCrearEstructurasPcc($pParametros){
		global $cAlfa; 

		/**
		 * Recibe como Parametro un vector con las siguientes posiciones:
		 * $pParametros['TIPOESTU'] //TIPO DE ESTRUCTURA
		 */

		/**
		 * Variable para saber si hay o no errores de validacion.
		 * @var number
		 */
		$nSwitch = 0;

		/**
		 * Matriz para Retornar Valores
		 */
		$mReturn = array();

		/**
		 * Reservando Primera Posición para retorna true o false
		 * Reservando Segunda Posición para el nombre de la tabla
		 */
		$mReturn[0] = "";
		$mReturn[1] = "";

		/**
		 * Llamando Metodo que hace conexion
		 */
		$mReturnConexionTM = fnConectarDBPcc();

		if($mReturnConexionTM[0] == "true"){
			$xConexionTM = $mReturnConexionTM[1];
		}else{
			$nSwitch = 1;
			for($nR=1;$nR<count($mReturnConexionTM);$nR++){
				$mReturn[count($mReturn)] = $mReturnConexionTM[$nR];
			}
		}

		/**
		 * Random para Nombre de la Tabla
		 */
		$cTabCar  = mt_rand(1000000000, 9999999999);

		switch($pParametros['TIPOESTU']){
			case "PAGOS":
				$cTabla = "mempcc".$cTabCar;

				$qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
				$qNewTab .= "pccidxxx int(11)       NOT NULL AUTO_INCREMENT COMMENT \"Autoincremental Pago a Tercero\",";
				$qNewTab .= "pcclotxx varchar(100)  NOT NULL COMMENT \"Lote de procesamiento\",";
				$qNewTab .= "comidxxx varchar(1)    NOT NULL COMMENT \"Id del Comprobante\",";
				$qNewTab .= "comcodxx varchar(4)    NOT NULL COMMENT \"Codigo del Comprobante\",";
				$qNewTab .= "comcscxx varchar(20)   NOT NULL COMMENT \"Consecutivo Uno del Comprobante\",";
				$qNewTab .= "comcsc2x varchar(20)   NOT NULL COMMENT \"Consecutivo Dos del Comprobante\",";
				$qNewTab .= "comcsc3x varchar(20)   NOT NULL COMMENT \"Consecutivo Tres\",";
				$qNewTab .= "comseqxx varchar(5)    NOT NULL COMMENT \"Secuencia del Comprobante\",";
				$qNewTab .= "comfecxx date          NOT NULL COMMENT \"Fecha del Comprobante\",";
				$qNewTab .= "teridxxx varchar(20)   NOT NULL COMMENT \"Id del Cliente\",";
				$qNewTab .= "clinomxx varchar(100)  NOT NULL COMMENT \"Nombre del Cliente\",";
				$qNewTab .= "terid2xx varchar(20)   NOT NULL COMMENT \"Id del Proveedor\",";
				$qNewTab .= "pronomxx varchar(100)  NOT NULL COMMENT \"Nombre del Proveedor\",";
				$qNewTab .= "sucidxxx varchar(3)    NOT NULL COMMENT \"Id de la Sucursal Operativa\",";
				$qNewTab .= "docidxxx varchar(20)   NOT NULL COMMENT \"Id del DO\",";
				$qNewTab .= "docsufxx varchar(3)    NOT NULL COMMENT \"Sufijo del DO\",";
				$qNewTab .= "comidcxx varchar(1)    NOT NULL COMMENT \"Id de la Comprobante\",";
				$qNewTab .= "comcodcx varchar(4)    NOT NULL COMMENT \"Codigo de la Comprobante\",";
				$qNewTab .= "comcsccx varchar(20)   NOT NULL COMMENT \"Consecutivo Uno de la Comprobante\",";
				$qNewTab .= "comcscc2 varchar(20)   NOT NULL COMMENT \"Consecutivo Dos de la Comprobante\",";
				$qNewTab .= "comidaxx varchar(1)    NOT NULL COMMENT \"Id del Comprobante Afectado (ND-NC)\",";
				$qNewTab .= "comcodax varchar(4)    NOT NULL COMMENT \"Codigo del Comprobante Afectado (ND-NC)\",";
				$qNewTab .= "comcscax varchar(20)   NOT NULL COMMENT \"Consecutivo Uno del Comprobante Afectado (ND-NC)\",";
				$qNewTab .= "comcsca2 varchar(20)   NOT NULL COMMENT \"Consecutivo Dos del Comprobante Afectado (ND-NC)\",";
				$qNewTab .= "comfeccx date          NOT NULL COMMENT \"Fecha de la Factura\",";
				$qNewTab .= "ctoidxxx varchar(10)   NOT NULL COMMENT \"Id Concepto Contable del Comprobante\",";
				$qNewTab .= "ctodesxx varchar(250)  NOT NULL COMMENT \"Descripcion Concepto Contable del Comprobante\",";
				$qNewTab .= "comdocin varchar(10)   NOT NULL COMMENT \"Documento Informativo\",";
				$qNewTab .= "comvlrxx decimal(15,2) NOT NULL COMMENT \"Valor del Comprobante\",";
				$qNewTab .= "comvlr01 decimal(15,2) NOT NULL COMMENT \"Valor sin Iva\",";
				$qNewTab .= "comvlr02 decimal(15,2) NOT NULL COMMENT \"Iva\",";
				$qNewTab .= "pccestrf varchar(20)   NOT NULL COMMENT \"Estado Asignado por el Revisor Fiscal\",";
				$qNewTab .= "pccenvco datetime      NOT NULL DEFAULT \"0000-00-00 00:00:00\" COMMENT \"Fecha y Hora de Envio Correo\",";
				$qNewTab .= "regusrxx varchar(20)   NOT NULL COMMENT \"Usuario que Creo el Registro\",";
				$qNewTab .= "regfcrex date          NOT NULL COMMENT \"Fecha de Creacion del Registro\",";
				$qNewTab .= "reghcrex time          NOT NULL COMMENT \"Hora de Creacion del Registro\",";
				$qNewTab .= "regfmodx date          NOT NULL COMMENT \"Fecha de Modificacion del Registro\",";
				$qNewTab .= "reghmodx time          NOT NULL COMMENT \"Hora de Modificacion del Registro\",";
				$qNewTab .= "regestxx varchar(20)   NOT NULL COMMENT \"Estado del Registro\",";
				$qNewTab .= "regstamp timestamp     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT \"Modificado\",";
				$qNewTab .= "PRIMARY KEY (pccidxxx),";
				$qNewTab .= "KEY (comidxxx, comcodxx, comcsc2x, comseqxx),";
				$qNewTab .= "KEY (sucidxxx,docidxxx,docsufxx),";
				$qNewTab .= "KEY (comidcxx,comcodcx,comcsccx,comcscc2)) ";
				$qNewTab .= "ENGINE=MyISAM COMMENT=\"Pagos a terceros de NC y NC\" ";
				$xNewTab  = mysql_query($qNewTab,$xConexionTM);

				if(!$xNewTab) {
					$nSwitch = 1;
					$mReturn[count($mReturn)] = "Error al Crear Tabla Temporal [Pagos a terceros de NC y NC], por Favor Informar a OpenTecnologia S.A. ".mysql_error($xConexionTM);
				}
			break;
			case "RETENCIONES":
				$cTabla = "mempcr".$cTabCar;

				$qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
				$qNewTab .= "retidxxx int(11)       NOT NULL AUTO_INCREMENT COMMENT \"Autoincremental Retetencion Pago a Tercero\",";
				$qNewTab .= "pccidxxx int(11)       NOT NULL COMMENT \"Autoincremental Pago a Tercero\",";
				$qNewTab .= "pcclotxx varchar(100)  NOT NULL COMMENT \"Lote de procesamiento\",";
				$qNewTab .= "teridxxx varchar(20)   NOT NULL COMMENT \"Id del Cliente\",";
				$qNewTab .= "rettipxx varchar(20)   NOT NULL COMMENT \"Tipo Retencion\",";
				$qNewTab .= "comvlrxx decimal(15,2) NOT NULL COMMENT \"Valor Retencion\",";
				$qNewTab .= "comvlr01 decimal(15,2) NOT NULL COMMENT \"Base Retencion\",";
				$qNewTab .= "retporxx decimal(6,3)  NOT NULL COMMENT \"Porcentaje de Retencion\",";
				$qNewTab .= "regusrxx varchar(20)   NOT NULL COMMENT \"Usuario que Creo el Registro\",";
				$qNewTab .= "regfcrex date          NOT NULL COMMENT \"Fecha de Creacion del Registro\",";
				$qNewTab .= "reghcrex time          NOT NULL COMMENT \"Hora de Creacion del Registro\",";
				$qNewTab .= "regfmodx date          NOT NULL COMMENT \"Fecha de Modificacion del Registro\",";
				$qNewTab .= "reghmodx time          NOT NULL COMMENT \"Hora de Modificacion del Registro\",";
				$qNewTab .= "regestxx varchar(20)   NOT NULL COMMENT \"Estado del Registro\",";       
				$qNewTab .= "regstamp timestamp     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT \"Modificado\",";
				$qNewTab .= "PRIMARY KEY (retidxxx)) ";
				$qNewTab .= "ENGINE=MyISAM COMMENT=\"Retenciones de Pagos a Terceros (NC y ND)\" ";
				$xNewTab  = mysql_query($qNewTab,$xConexionTM);

				if(!$xNewTab) {
					$nSwitch = 1;
					$mReturn[count($mReturn)] = "Error al Crear Tabla Temporal [Retenciones de Pagos a Terceros (NC y ND)], por Favor Informar a OpenTecnologia S.A. ";
				}
			break;
			case "RETENCIONESAUX":
				$cTabla = "mempcr".$cTabCar;

				$qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
				$qNewTab .= "lineaidx int(11) NOT   NULL AUTO_INCREMENT,";
				$qNewTab .= "doiidxxx varchar(20)   NOT NULL,";
				$qNewTab .= "comidsxx varchar(1)    NOT NULL,";
				$qNewTab .= "comcodsx varchar(4)    NOT NULL,";
				$qNewTab .= "comcscsx varchar(20)   NOT NULL,";
				$qNewTab .= "comcsc2s varchar(20)   NOT NULL,";
				$qNewTab .= "comseqsx varchar(20)   NOT NULL,";
				$qNewTab .= "comfecxx date          NOT NULL,";
				$qNewTab .= "terid2sx varchar(20)   NOT NULL,";
				$qNewTab .= "pucidsxx varchar(10)   NOT NULL,";
				$qNewTab .= "ctoidsxx varchar(10)   NOT NULL,";
				$qNewTab .= "sucidsxx varchar(3)    NOT NULL,";
				$qNewTab .= "docidsxx varchar(20)   NOT NULL,";
				$qNewTab .= "docsufsx varchar(3)    NOT NULL,";
				$qNewTab .= "sccidsxx varchar(20)   NOT NULL,";
				$qNewTab .= "comidcxx varchar(1)    NOT NULL,";
				$qNewTab .= "comcodcx varchar(4)    NOT NULL,";
				$qNewTab .= "comcsccx varchar(20)   NOT NULL,";
				$qNewTab .= "comcscc2 varchar(20)   NOT NULL,";
				$qNewTab .= "comfeccx date          NOT NULL,";
				$qNewTab .= "teridxxx varchar(20)   NOT NULL,";
				$qNewTab .= "terid2xx varchar(20)   NOT NULL,";
				$qNewTab .= "retenxxx varchar(20)   NOT NULL,";
				$qNewTab .= "pucidxxx varchar(10)   NOT NULL,";
				$qNewTab .= "pucretxx decimal(6,3)  NOT NULL,";
				$qNewTab .= "comvlrxx decimal(15,2) NOT NULL,";
				$qNewTab .= "comvlr01 decimal(15,2) NOT NULL,";
				$qNewTab .= "regstamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT \"Modificado\",";
				$qNewTab .= "PRIMARY KEY (lineaidx)) ";
				$qNewTab .= "ENGINE=MyISAM COMMENT=\"Auxiliar Retenciones\" ";
				$xNewTab  = mysql_query($qNewTab,$xConexionTM);

				if(!$xNewTab) {
					$nSwitch = 1;
					$mReturn[count($mReturn)] = "Error al Crear Tabla Temporal [Auxiliar Retenciones], por Favor Informar a OpenTecnologia S.A. ";
				}
			break;
			default:
				$nSwitch = 1;
				$mReturn[count($mReturn)] = "No se Recibio Tipo de Estructura a Crear, por Favor Informar a OpenTecnologia S.A.";
			break;
		}

		if($nSwitch == 0){
			$mReturn[0] = "true";
			$mReturn[1] = $cTabla;
		}else{
			$mReturn[0] = "false";
		}
		return $mReturn;
	}

	/**
   * Metodo que realiza la conexion
   */
	function fnConectarDBPcc(){
		/**
		 * Variable para saber si hay o no errores de validacion.
		 *
		 * @var number
		 */
		$nSwitch = 0;

		/**
		 * Matriz para Retornar Valores
		 */
		$mReturn = array();

		/**
		 * Reservo Primera Posicion para retorna true o false
		 */
		$mReturn[0] = "";

		$xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);

		if($xConexion99){
			$nSwitch = 0;
		}else{
			$nSwitch = 1;
			$mReturn[count($mReturn)] = "El Sistema no Logro Conexion con ".OC_SERVER;
		}

		if($nSwitch == 0){
			$mReturn[0] = "true";
			$mReturn[1] = $xConexion99;
		} else{
			$mReturn[0] = "false";
		}
		return $mReturn;
	}##function fnConectarDB(){##
	
	/**
   * Metodo que realiza el reinicio de la conexion
   */
	function fnReiniciarConexionDBPcc($pConexion){
		global $cHost;  global $cUserHost;  global $cPassHost;

		//echo "<br>Reconectando...";
		mysql_close($pConexion);
		if($cHost != "" && $cUserHost != "" && $cPassHost != ""){
			$xConexion01 = mysql_connect($cHost,$cUserHost,$cPassHost,TRUE);
		}else{
			$xConexion01 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT,TRUE);
		}
		return $xConexion01;
	}##function fnReiniciarConexionDBIBI(){##
	
	
	?>
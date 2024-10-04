<?php
	/**
	 * Generar Reporte de Relacion de Costos Gastos y Deducciones Formato 2613
	 * --- Descripcion: Permite Generar el Reporte Consolidado de Facturacion
	 * @author Juan Jose Trujillo <juan.trujillo@openits.co>
	 * @version 001
	 */

  // ini_set('display_errors', 1);
  // ini_set('display_startup_errors', 1);
  // error_reporting(E_ALL);

	ini_set("memory_limit","512M");
	set_time_limit(0);

  /**
   * Cantidad de Registros para reiniciar conexion.
   */
  define("_NUMREG_",100);

	/**
	 * Variables de control de errores.
   * 
	 * @var number
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
	 * @var Number
	 */
	$cEjePro = 0;

	/**
	 * Nombre(s) de los archivos en excel generados.
	 */
	$cNomArc = "";

  /**
   * Variable para limitar la cantidad de registros en la busqueda.
   * 
   * @var integer
   */
  $nNumReg = 1000;

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
		// Librerias
		include("../../../../../config/config.php");
		include("../../../../libs/php/utility.php");
		include("../../../../../libs/php/utiprobg.php");
	}

  /**
	 *  Cookie fija
	 */
	$kDf = explode("~", $_COOKIE["kDatosFijos"]);
	$kUser = $kDf[4];

  if ($_SERVER["SERVER_PORT"] == "") {
    $gDesde   = $_POST['gDesde'];
    $gHasta   = $_POST['gHasta'];
    $gTipoDoc = $_POST['gTipoDoc']; 
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

  if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;
	
		$strPost = "gDesde~" . $gDesde . 
              "|gHasta~" . $gHasta;
	
		$vParBg['pbadbxxx'] = $cAlfa;                                                             // Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                                                      // Modulo
		$vParBg['pbatinxx'] = "RELACIONCOSTOSGASTOSYDEDUCCIONES";                                 // Tipo Interface
		$vParBg['pbatinde'] = "REPORTE DE RELACION DE COSTOS GASTOS Y DEDUCCIONES FORMATO 2613";  // Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                                                                 // Sucursal
		$vParBg['doiidxxx'] = "";                                                                 // Do
		$vParBg['doisfidx'] = "";                                                                 // Sufijo
		$vParBg['cliidxxx'] = "";                                                                 // Nit
		$vParBg['clinomxx'] = "";                                                                 // Nombre Importador
		$vParBg['pbapostx'] = $strPost;                                                           // Parametros para reconstruir Post
		$vParBg['pbatabxx'] = "";                                                                 // Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];                                        // Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];                                            // cookie
		$vParBg['pbacrexx'] = 0;                                                                  // Cantidad Registros
		$vParBg['pbatxixx'] = 1;                                                                  // Tiempo Ejecucion x Item en Segundos
		$vParBg['pbaopcxx'] = "";                                                                 // Opciones
		$vParBg['regusrxx'] = $kUser;                                                             // Usuario que Creo Registro
	
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
			f_Mensaje(__FILE__, __LINE__, $cMsj."Verifique.");
		}
	} // fin del if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0)

  $cComApl = ""; 
  // Buscando comprobantes marcados de AJUSTES
  $qAjustes  = "SELECT ";
  $qAjustes .= "CONCAT(comidxxx,\"-\",comcodxx) AS comidxxx ";
  $qAjustes .= "FROM $cAlfa.fpar0117 ";
  $qAjustes .= "WHERE ";
  $qAjustes .= "comtipxx = \"AJUSTES\"  ";
  $xAjustes = f_MySql("SELECT","",$qAjustes,$xConexion01,"");
  while ($xRDB = mysql_fetch_array($xAjustes)) {
  	$cComAp .= "\"{$xRDB['comidxxx']}\",";
  }

  // Buscando comprobantes marcados de CPE
  $qCpe  = "SELECT ";
  $qCpe .= "CONCAT(comidxxx,\"-\",comcodxx) AS comidxxx ";
  $qCpe .= "FROM $cAlfa.fpar0117 ";
  $qCpe .= "WHERE ";
  $qCpe .= "comidxxx = \"P\" AND ";
  $qCpe .= "comtipxx = \"CPE\" ";
  $xCpe = f_MySql("SELECT","",$qCpe,$xConexion01,"");
  while ($xRDB = mysql_fetch_array($xCpe)) {
  	$cComAp .= "\"{$xRDB['comidxxx']}\",";
  }
  $cComAp = substr($cComAp,0,strlen($cComAp)-1);

  // Se debe cargar un array con las descripciones de las cuentas 5, 6 o 7
  $mPucIds = array();
  $qPucIds  = "SELECT ";
  $qPucIds .= "CONCAT(pucgruxx,pucctaxx,pucsctax,pucauxxx,pucsauxx) as pucidxxx,";
  $qPucIds .= "pucdesxx ";
  $qPucIds .= "FROM $cAlfa.fpar0115 ";
  $qPucIds .= "WHERE ";    
  $qPucIds .= "$cAlfa.fpar0115.pucgruxx LIKE \"5%\" OR ";
  $qPucIds .= "$cAlfa.fpar0115.pucgruxx LIKE \"6%\" OR ";
  $qPucIds .= "$cAlfa.fpar0115.pucgruxx LIKE \"7%\"";
  $xPucIds  = f_MySql("SELECT","",$qPucIds,$xConexion01,"");
  while ($xRPI = mysql_fetch_array($xPucIds)){
    $mPucIds["{$xRPI['pucidxxx']}"]['pucidxxx'] = $xRPI['pucidxxx'];
    $mPucIds["{$xRPI['pucidxxx']}"]['pucdesxx'] = $xRPI['pucdesxx'];
  }

  // Generar Reporte
	if ($cEjePro == 0) {
		if ($nSwitch == 0) {
      $nAnio = substr($gDesde, 0, 4);
      //Consulta para traer la cantidad total de registros
      $qCantReg  = "SELECT SQL_CALC_FOUND_ROWS comidxxx ";
      $qCantReg .= "FROM $cAlfa.fcod$nAnio ";
      $qCantReg .= "WHERE ";
      $qCantReg .= "CONCAT($cAlfa.fcod$nAnio.comidxxx,\"-\",$cAlfa.fcod$nAnio.comcodxx) IN ($cComAp) AND ";
      $qCantReg .= "$cAlfa.fcod$nAnio.comfecxx BETWEEN \"{$gDesde}\" AND \"{$gHasta}\" AND ";
      $qCantReg .= "($cAlfa.fcod$nAnio.pucidxxx LIKE \"5%\" OR $cAlfa.fcod$nAnio.pucidxxx LIKE \"6%\" OR $cAlfa.fcod$nAnio.pucidxxx LIKE \"7%\") AND ";
      $qCantReg .= "$cAlfa.fcod$nAnio.regestxx = \"ACTIVO\" ";
      $xCantReg  = mysql_query($qCantReg,$xConexion01);

      $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
      $xRNR     = mysql_fetch_array($xNumRows);
      $nCanReg  = $xRNR['FOUND_ROWS()'];

      $mDatos = array();
      if ($nCanReg > 0) {
        for($k=0; $k<=$nCanReg; $k+=$nNumReg) {
          // Consulta principal
          $qMovComp  = "SELECT  ";
          $qMovComp .= "$cAlfa.fcod$nAnio.comidxxx, ";
          $qMovComp .= "$cAlfa.fcod$nAnio.comcodxx, ";
          $qMovComp .= "$cAlfa.fcod$nAnio.comcscxx, ";
          $qMovComp .= "$cAlfa.fcod$nAnio.comcsc2x, ";
          $qMovComp .= "$cAlfa.fcod$nAnio.comfecxx, ";
          $qMovComp .= "$cAlfa.fcod$nAnio.teridxxx, ";
          $qMovComp .= "$cAlfa.fcod$nAnio.pucidxxx, ";
          $qMovComp .= "$cAlfa.fcod$nAnio.ctoidxxx, ";
          $qMovComp .= "IF($cAlfa.fcod$nAnio.commovxx = \"D\",$cAlfa.fcod$nAnio.comvlrxx,$cAlfa.fcod$nAnio.comvlrxx*-1) AS comvlrxx, ";
          $qMovComp .= "$cAlfa.fcoc$nAnio.dsoidxxx, ";
          $qMovComp .= "$cAlfa.fcoc$nAnio.dsoprexx, ";
          $qMovComp .= "$cAlfa.fcoc$nAnio.dsonumfa,";
          $qMovComp .= "$cAlfa.fcoc$nAnio.dsofecxx  ";
          $qMovComp .= "FROM $cAlfa.fcod$nAnio ";
          $qMovComp .= "LEFT JOIN $cAlfa.fcoc$nAnio ON $cAlfa.fcod$nAnio.comidxxx = $cAlfa.fcoc$nAnio.comidxxx AND $cAlfa.fcoc$nAnio.comcodxx = $cAlfa.fcod$nAnio.comcodxx AND $cAlfa.fcoc$nAnio.comcscxx = $cAlfa.fcod$nAnio.comcscxx AND $cAlfa.fcoc$nAnio.comcsc2x = $cAlfa.fcod$nAnio.comcsc2x ";
          $qMovComp .= "WHERE ";
          $qMovComp .= "CONCAT($cAlfa.fcod$nAnio.comidxxx,\"-\",$cAlfa.fcod$nAnio.comcodxx) IN ($cComAp) AND ";
          $qMovComp .= "$cAlfa.fcod$nAnio.comfecxx BETWEEN \"{$gDesde}\" AND \"{$gHasta}\" AND ";
          $qMovComp .= "($cAlfa.fcod$nAnio.pucidxxx LIKE \"5%\" OR $cAlfa.fcod$nAnio.pucidxxx LIKE \"6%\" OR $cAlfa.fcod$nAnio.pucidxxx LIKE \"7%\") AND ";
          $qMovComp .= "$cAlfa.fcod$nAnio.regestxx = \"ACTIVO\" ";
          $qMovComp .= "ORDER BY $cAlfa.fcod$nAnio.comfecxx LIMIT $k,$nNumReg";	
          $xMovComp  = f_MySql("SELECT","",$qMovComp,$xConexion01,"");

          if (mysql_num_rows($xMovComp) > 0) {
            while ($xRMC = mysql_fetch_array($xMovComp)){
              // Busco la informacion del tercero teridxxx
              $vTercero  = array();
              $qTercero  = "SELECT ";
              $qTercero .= "IF(CLINOMXX != \"\",CLINOMXX,TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X))) AS CLINOMXX, ";
              $qTercero .= "TDIIDXXX ";
              $qTercero .= "FROM $cAlfa.SIAI0150 ";
              $qTercero .= "WHERE ";
              $qTercero .= "CLIIDXXX = \"{$xRMC['teridxxx']}\" LIMIT 0,1";
              $xTercero  = f_MySql("SELECT","",$qTercero,$xConexion01,"");
              if (mysql_num_rows($xTercero) > 0) {
                $vTercero = mysql_fetch_array($xTercero);
              }

              $nInd_mDatos = count($mDatos);
							$mDatos[$nInd_mDatos] = $xRMC;
							$mDatos[$nInd_mDatos]['terdvxxx'] = f_Digito_Verificacion($xRMC['teridxxx']);
							$mDatos[$nInd_mDatos]['tertipdo'] = $vTercero['TDIIDXXX'];
							$mDatos[$nInd_mDatos]['ternomxx'] = $vTercero['CLINOMXX'];
							$mDatos[$nInd_mDatos]['pucdesxx'] = $mPucIds["{$xRMC['pucidxxx']}"]['pucdesxx'];
            }
          }
        }
      }

      // Pinta el reporte por Excel
      $header  .= 'Reporte de Relacion de Costos Gastos y Deducciones Formato 2613'."\n";
      $header  .= "\n";
      $cData    = '';
      $cNomFile = "REPORTE_RELACION_COSTOS_GASTOS_Y_DEDUCCIONES".$kUser."_".date('YmdHis').".xls";

      if ($_SERVER["SERVER_PORT"] != "") {
        $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
      } else {
        $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
      }
  
      if (file_exists($cFile)) {
        unlink($cFile);
      }

      $fOp = fopen($cFile, 'a');
      $cData .= '<table border = "1" cellpadding = "0" cellspacing = "0" width = "1200px">';
        $cData .= '<tr height="50">';
          $cData .= '<td class="name" colspan = "13" style="font-size:14px" bgcolor = "#144F5C">';
            $cData .= '<center><b>';
              $cData .= '<span style="font-size:18px"><font color="white">Relaci&oacute;n de Costos, Gastos y Deducci&oacute;n</font></span>';
            $cData .= '</b></center><br>';
          $cData .= '</td>';
        $cData .= '</tr>';

        $cData .= '<tr>';
          $cData .= '<td align="center" bgcolor = "#144F5C" style="width:130px"><b><font color="white">Documento Soporte</font></b></td>';
          $cData .= '<td align="center" bgcolor = "#144F5C" style="width:120px"><b><font color="white">Identificaci&oacute;n Soporte</font></b></td>';
          $cData .= '<td align="center" bgcolor = "#144F5C" style="width:120px"><b><font color="white">Tipo Documento</font></b></td>';
          $cData .= '<td align="center" bgcolor = "#144F5C" style="width:120px"><b><font color="white">Numero de Identificaci&oacute;n</font></b></td>';
          $cData .= '<td align="center" bgcolor = "#144F5C" style="width:100px"><b><font color="white">DV</font></b></td>';
          $cData .= '<td align="center" bgcolor = "#144F5C" style="width:180px"><b><font color="white">Apellidos y Nombres o Razon Social</font></b></td>';
          $cData .= '<td align="center" bgcolor = "#144F5C" style="width:100px"><b><font color="white">Prefijo Factura</font></b></td>';
          $cData .= '<td align="center" bgcolor = "#144F5C" style="width:100px"><b><font color="white">N. Factura</font></b></td>';
          $cData .= '<td align="center" bgcolor = "#144F5C" style="width:140px"><b><font color="white">Fecha Factura</font></b></td>';
          $cData .= '<td align="center" bgcolor = "#144F5C" style="width:160px"><b><font color="white">Valor del costo, gasto o deducci&oacute;n</font></b></td>';
          $cData .= '<td align="center" bgcolor = "#144F5C" style="width:100px"><b><font color="white">Cuenta</font></b></td>';
          $cData .= '<td align="center" bgcolor = "#144F5C" style="width:180px"><b><font color="white">Concepto costo, gasto o deducci&oacute;n</font></b></td>';
          $cData .= '<td align="center" bgcolor = "#144F5C" style="width:140px"><b><font color="white">Identificaci&oacute;n titular saldo</font></b></td>';
        $cData .= '</tr>';

        for ($i=0; $i < count($mDatos); $i++) {
          $cData .= '<tr>';
            $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mDatos[$i]['dsoidxxx'] != "") ? $mDatos[$i]['dsoidxxx'] : "").'</td>';
            $cData .= '<td align="center" style="mso-number-format:\'\@\'"></td>';
            $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mDatos[$i]['tertipdo'] != "") ? $mDatos[$i]['tertipdo'] : "").'</td>';
            $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mDatos[$i]['teridxxx'] != "") ? $mDatos[$i]['teridxxx'] : "").'</td>';
            $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mDatos[$i]['terdvxxx'] != "") ? $mDatos[$i]['terdvxxx'] : "").'</td>';
            $cData .= '<td align="left"   style="mso-number-format:\'\@\'">'.(($mDatos[$i]['ternomxx'] != "") ? $mDatos[$i]['ternomxx'] : "").'</td>';
            $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mDatos[$i]['dsoprexx'] != "") ? $mDatos[$i]['dsoprexx'] : "").'</td>';
            $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mDatos[$i]['dsonumfa'] != "") ? $mDatos[$i]['dsonumfa'] : "").'</td>';
            $cData .= '<td align="center" style="mso-number-format:yyyy-mm-dd">'.(($mDatos[$i]['dsofecxx'] != "") ? $mDatos[$i]['dsofecxx'] : "").'</td>';
            $cData .= '<td align="right">'.(number_format($mDatos[$i]['comvlrxx'],2,".","")).'</td>';
            $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($mDatos[$i]['pucidxxx'] != "") ? substr($mDatos[$i]['pucidxxx'], 0, 8) : "").'</td>';
            $cData .= '<td align="left" style="mso-number-format:\'\@\'">'.(($mDatos[$i]['pucdesxx'] != "") ? $mDatos[$i]['pucdesxx'] : "").'</td>';
            $cData .= '<td align="center" style="mso-number-format:\'\@\'">'.(($vSysStr['financiero_nit_agencia_aduanas'] != "") ? $vSysStr['financiero_nit_agencia_aduanas'] : "").'</td>';
          $cData .= '</tr>';
        }
      $cData .= '</table>';

      fwrite($fOp, $cData);
      fclose($fOp);

      if (file_exists($cFile)) {
        if ($cData == "") {
          $cData = "\n(0) REGISTROS!\n";
        }

        chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
        $cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFile);

        if ($_SERVER["SERVER_PORT"] != "") {
          // Obtener la ruta absoluta del archivo
					$cAbsolutePath = realpath($cFile);
					$cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

					if (in_array(realpath($cAbsolutePath), $vSystem_Path_Authorized)) {
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
          }
        }else{
          $cNomArc = $cNomFile;
          echo "\n".$cNomArc;
        }
      }else {
        $nSwitch = 1;
        if ($_SERVER["SERVER_PORT"] != "") {
          f_Mensaje(__FILE__, __LINE__, "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.");
        } else {
          $cMsj .= "No se encontro el archivo $cFile, Favor Comunicar este Error a openTecnologia S.A.";
        }
      }
    }
  }

  if ($nSwitch != 0) {
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
	
		// Incluyendo la clase de procesos en background
		$ObjProBg = new cProcesosBackground();
		$mReturnProBg = $ObjProBg->fnFinalizarProcesoBackground($vParBg);
	
		// Imprimiendo resumen de todo ok.
		if ($mReturnProBg[0] == "false") {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea " . str_pad(__LINE__, 4, "0", STR_PAD_LEFT) . ": ";
				$cMsj .= $mReturnProBg[$nR] . "\n";
			}
		}
  } // fin del if ($_SERVER["SERVER_PORT"] == "")

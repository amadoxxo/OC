<?php
  set_time_limit(0);
  ini_set("memory_limit","4096M");

  date_default_timezone_set("America/Bogota");

  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");
  
  /**
   * Cantidad de Registros para reiniciar conexion
   */
  define("_NUMREG_",1000);

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
      
      /**
       * Buscando el ID del proceso
       */
      $qProBg = "SELECT * ";
      $qProBg .= "FROM $cBeta.sysprobg ";
      $qProBg .= "WHERE ";
      $qProBg .= "pbaidxxx= \"{$vArg[0]}\" AND ";
      $qProBg .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
      $xProBg = f_MySql("SELECT", "", $qProBg, $xConexion01, "");
      // echo $qProBg."~".mysql_num_rows($xProBg)."\n";
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

  //Inicializando clase reporte
  $objEstructuras = new cEstructurasEstadoCuentaTramiteRoldan();
	
	if ($_SERVER["SERVER_PORT"] != "") {
		/*** Ejecutar proceso en Background ***/
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;

		if($gCcoId != "") {
			$mAux    = explode("~",$gCcoId);
			$gCcoId  = $mAux[0];
			$gSucCco = $mAux[1];
			$gCcoNom = $mAux[2];
		}
	}
	
	if ($_SERVER["SERVER_PORT"] == "") {
		$gCcoId    = $_POST['gCcoId'];
		$gTerId    = $_POST['gTerId'];
		$gDirId    = $_POST['gDirId'];
		$gDesde    = $_POST['gDesde'];
		$gHasta    = $_POST['gHasta'];
		$gSucId    = $_POST['gSucId'];
		$gDocNro   = $_POST['gDocNro'];
		$gDocSuf   = $_POST['gDocSuf'];
		$gSucCco   = $_POST['gSucCco'];
		$gCcoNom   = $_POST['gCcoNom'];
    $gEstado   = $_POST['gEstado'];
    $gFecCorte = $_POST['gFecCorte'];
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

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

	if($gDirId != ""){
		#Busco el nombre del director de cuenta
		$qNomDir  = "SELECT ";
		$qNomDir .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"DIRECTOR SIN NOMBRE\") AS USRNOMXX ";
		$qNomDir .= "FROM $cAlfa.SIAI0003 ";
		$qNomDir .= "WHERE ";
		$qNomDir .= "USRIDXXX = \"{$gDirId}\" LIMIT 0,1";
		$xNomDir = f_MySql("SELECT","",$qNomDir,$xConexion01,"");
		if (mysql_num_rows($xNomDir) > 0) {
			$xRU = mysql_fetch_array($xNomDir);
		} else {
			$xRU['USRNOMXX'] = "VENDEDOR SIN NOMBRE";
		}
	}

  $cTitulo = "";
	switch ($gEstado) {
		case "ACTIVO":
			$cTitulo .= "REPORTE DE TRAMITES ABIERTOS SIN FACTURAR ";
		break;
		case "ACTIVOCONSALDO":
			$cTitulo .= "REPORTE DE TRAMITES ACTIVO CON SALDO ";
		break;
		case "FACTURADO":
			$cTitulo .= "REPORTE DE TRAMITES FACTURADOS ";
		break;
  }

	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;

    $vParametros = array();
    $vParametros['TIPOESTU'] = "MOVIMIENTO";
    $mRetTabMov = $objEstructuras->fnCrearTablaEstadoCuentaTramiteRoldan($vParametros);
    if($mRetTabMov[0] == "false"){
      $nSwitch = 1;
      for($nR=1;$nR<count($mRetTabMov);$nR++){
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .=  $mRetTabMov[$nR]."\n";
      }
    }

    $vParametros = array();
    $vParametros['TIPOESTU'] = "SALDOS";
    $mRetTabSal = $objEstructuras->fnCrearTablaEstadoCuentaTramiteRoldan($vParametros);
    if($mRetTabSal[0] == "false"){
      $nSwitch = 1;
      for($nR=1;$nR<count($mRetTabSal);$nR++){
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
        $cMsj .=  $mRetTabSal[$nR]."\n";
      }
    }

    //Creando tabla temporal
    if ($nSwitch == 0) {
      $strPost  = "gTerId~"    . $gTerId;
      $strPost .= "|gCcoId~"   . $gCcoId;
      $strPost .= "|gDirId~"   . $gDirId;
      $strPost .= "|gDesde~"   . $gDesde;
      $strPost .= "|gHasta~"   . $gHasta;
      $strPost .= "|gSucId~"   . $gSucId;
      $strPost .= "|gDocNro~"  . $gDocNro;
      $strPost .= "|gDocSuf~"  . $gDocSuf;
      $strPost .= "|gSucCco~"  . $gSucCco;
      $strPost .= "|gCcoNom~"  . $gCcoNom;
      $strPost .= "|gEstado~"  . $gEstado;
      $strPost .= "|gFecCorte~". $gFecCorte;
      
      $vParBg['pbadbxxx'] = $cAlfa;                           	  // Base de Datos
      $vParBg['pbamodxx'] = "FACTURACION";                    	  // Modulo
      $vParBg['pbatinxx'] = "ESTADOCUENTATRAMITESROLDAN";     	  // Tipo Interface
      $vParBg['pbatinde'] = "ESTADO DE CUENTA TRAMITES ROLDAN";   // Descripcion Tipo de Interfaz
      $vParBg['admidxxx'] = trim($gSucId);                    	  // Sucursal
      $vParBg['doiidxxx'] = trim($gDocNro);                   	  // Do
      $vParBg['doisfidx'] = trim($gDocSuf);                   	  // Sufijo
      $vParBg['cliidxxx'] = $gTerId;                          	  // Nit
      $vParBg['clinomxx'] = $xDDE['clinomxx'];                	  // Nombre Importador
      $vParBg['pbapostx'] = $strPost;														  // Parametros para reconstruir Post
      $vParBg['pbatabxx'] = $mRetTabMov[1]."~".$mRetTabSal[1];    // Tablas Temporales
      $vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];      	  // Script
      $vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];          	  // cookie
      $vParBg['pbacrexx'] = 0;                                	  // Cantidad Registros
      $vParBg['pbatxixx'] = 1;                                	  // Tiempo Ejecucion x Item en Segundos
      $vParBg['pbaopcxx'] = "";                               	  // Opciones
      $vParBg['regusrxx'] = $kUser;                           	  // Usuario que Creo Registro
    
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
    }
  } // fin del if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0)

  if ($_SERVER["SERVER_PORT"] == "") {
    /**
     * Armando parametros para enviar al uticimpo
     */
    $mTablas = explode("~",$xRB['pbatabxx']);

    /**
     * Vector de tablas temporales
     */
    $mRetTabMov[1] = $mTablas[0];
    $mRetTabSal[1] = $mTablas[1];
    $mRetTabTra[1] = $mTablas[2];

  }  // fin del if ($_SERVER["SERVER_PORT"] == "")


	if ($cEjePro == 0) {
		if ($nSwitch == 0) {
			$nColspan = 17;
			// PINTA POR EXCEL //Reporte de Estado de Cuenta Tramites
			$header .= 'REPORTE DE ESTADO DE CUENTA TRAMITES ROLDAN'."<br>";
			$header .= "<br>";
			$data = '';
			$cNomFile = "REPORTE_ESTADO_DE_CUENTA_TRAMITES_ROLDAN" . $kUser . "_" . date("YmdHis") . ".xls";

			if ($_SERVER["SERVER_PORT"] != "") {
				$cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;

				if (file_exists($cFile)) {
					unlink($cFile);
				}
			} else {
				/**
				 * Ruta archivo
				 * @var string
				 */
				$cRuta = "{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa/estado_cuenta";
				if (!is_dir("{$OPENINIT['pathdr']}/opencomex/propios")) {
					mkdir("{$OPENINIT['pathdr']}/opencomex/propios");
					chmod("{$OPENINIT['pathdr']}/opencomex/propios", intval($vSysStr['system_permisos_directorios'], 8));
				}

				if (!is_dir("{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa")) {
					mkdir("{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa");
					chmod("{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa", intval($vSysStr['system_permisos_directorios'], 8));
				}

				if (!is_dir($cRuta)) {
					mkdir($cRuta);
					chmod($cRuta, intval($vSysStr['system_permisos_directorios'], 8));
				}

				$cFile = $cRuta . "/" . $cNomFile;

				/*** Eliminar los archivos creados por el Usuario Logueado que corresponden a dias diferentes de HOY ***/
				$vArchivos = array_slice(scandir($cRuta),2);
				$cArcUsu = "REPORTE_ESTADO_DE_CUENTA_TRAMITES_ROLDAN" . $kUser;
				$cArcHoy = "REPORTE_ESTADO_DE_CUENTA_TRAMITES_ROLDAN" . $kUser . "_" . date("Ymd");
				// echo "Archivo de Hoy: ".$cArcHoy;
				for($nA = 0; $nA < count($vArchivos); $nA++){
					if(substr_count($vArchivos[$nA],$cArcUsu) > 0){
						if(substr_count($vArchivos[$nA],$cArcHoy) == 0){
							$cFileDel = $cRuta . "/" . $vArchivos[$nA];
							if (file_exists($cFileDel)) {
								unlink($cFileDel);
							}
						}
					}
				}
				/*** Fin Eliminar los archivos creados por el Usuario Logueado que corresponden a dias diferentes de HOY ***/
			}
	
			$fOp = fopen($cFile, 'a');

			$data .= '<table border="1" cellspacing="0" cellpadding="0" align=center style="margin:5px" width="2000">';
				$data .= '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
					$data .= '<td class="name" colspan="'.$nColspan.'" align="left">';
						$data .= '<center>';
							$data .= '<font size="3">';
							$data .= '<b>'.$cTitulo.'<br>';
							$data .= 'DESDE '.$gDesde.' HASTA '.$gHasta.'<br>';
							if ($gFecCorte != "") {
								$data .= 'FECHA DE CORTE '.$gFecCorte.'<br>';
							}
							if($gCcoId!=""){
								$data .= 'SURCURSAL: '."[".$gCcoId."] ".$gCcoNom.'<br>';
							}
							if($gTerId!=""){
								$data .= 'CLIENTE: '."[".$gTerId."] ".$xDDE['clinomxx'].'<br>';
							}
							if($gDirId!=""){
								$data .= 'DIRECTOR: '."[".$gDirId."] ".$xRU['USRNOMXX'].'<br>';
							}
							$data .= '</b>';
							$data .= '</font>';
						$data .= '</center>';
					$data .= '</td>';
				$data .= '</tr>';
				$data .= '<tr height="20">';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Tramites</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Sucursal</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Pedido</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Fecha</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Fecha &Uacute;ltimo Comprobante</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Operaci&oacute;n</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Mayor Levante</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Entrega Carpeta</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="100px"><b><font color=white>Nit</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Cliente</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Estado</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="080px"><b><font color=white>Cierre</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center"><b><font color=white>Director</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Anticipo a Proveedores</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Anticipos Operativos</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Financiaciones Operativas</font></b></td>';
					$data .= '<td style="background-color:#0B610B" class="letra8" align="center" width="120px"><b><font color=white>Saldo</font></b></td>';
				$data .= '</tr>';

        fwrite($fOp, $data);

        #Rango de los Años en donde debo buscar los datos
        $nAnoI = ((substr($gDesde,0,4)-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (substr($gDesde,0,4)-1);
        $nAnoF = (substr($gHasta,0,4) > date('Y')) ? date('Y') : substr($gDesde,0,4);
        
        if ($gFecCorte != "") {
          $nAnoI = $vSysStr['financiero_ano_instalacion_modulo'];
          $nAnoF = substr($gFecCorte,0,4);
        }

        //Cuentas de anticipos operativos
        $vPucIdAnt = explode(",",$vSysStr['roldanlo_cuentas_anticipos_operativos_financiacion_clientes']);
        $cPucIdAnt = "\"".implode("\",\"", $vPucIdAnt)."\"";

        //Cuentas de Anticipos a Proveedor
        $vPucIdAp = explode(",",$vSysStr['roldanlo_cuentas_anticipos_proveedor_financiacion_clientes']);
        $cPucIdAp = "\"".implode("\",\"", $vPucIdAp)."\"";

        //Cuentas de pagos a terceros
        $vPucIdPcc = explode(",",$vSysStr['roldanlo_cuentas_pcc_financiacion_clientes']);
        $cPucIdPcc = "\"".implode("\",\"", $vPucIdPcc)."\"";

        $mAnticipos 	   = array(); //Matriz de Anticipos Operativos con DO completo
        $mAnticiposSinDO = array(); //Matriz de Anticipos Operativos sin DO completo
        $mAntiProve 	   = array(); //Matriz de Anticipos Proveedor con DO completo
        $mSumPCC         = array(); //Matriz de Pagos a Terceros con DO completo
        $mSumPCCSinDO    = array(); //Matriz de Pagos a Terceros sin DO completo

        //Cabecera Insert tabla temporal
        $qInCab = "INSERT INTO $cAlfa.$mRetTabMov[1] (";
        $qInCab .= "tiposalx,";
        $qInCab .= "sucidxxx,";
        $qInCab .= "docidxxx,";
        $qInCab .= "docsufxx,";
        $qInCab .= "comcsccx,";
        $qInCab .= "comseqcx,";
        $qInCab .= "comvlrxx) VALUES ";

        //Numero de registros por recorrido
        $nNumReg = 2000;
        for($nPerAno=$nAnoI;$nPerAno<=$nAnoF;$nPerAno++) {
          //Particionando ejecucion
          ## Buscando anticipos operativos
          $qMovConA  = "SELECT ";
          $qMovConA .= "SQL_CALC_FOUND_ROWS $cAlfa.fcod$nPerAno.sucidxxx ";
          $qMovConA .= "FROM $cAlfa.fcod$nPerAno ";
          $qMovConA .= "LEFT JOIN $cAlfa.fcoc$nPerAno ON ";
          $qMovConA .= "$cAlfa.fcoc$nPerAno.comidxxx = $cAlfa.fcod$nPerAno.comidxxx AND ";
          $qMovConA .= "$cAlfa.fcoc$nPerAno.comcodxx = $cAlfa.fcod$nPerAno.comcodxx AND ";
          $qMovConA .= "$cAlfa.fcoc$nPerAno.comcscxx = $cAlfa.fcod$nPerAno.comcscxx AND ";
          $qMovConA .= "$cAlfa.fcoc$nPerAno.comcsc2x = $cAlfa.fcod$nPerAno.comcsc2x ";
          $qMovConA .= "WHERE ";
          $qMovConA .= "$cAlfa.fcod$nPerAno.pucidxxx IN ($cPucIdAnt) AND ";
          if ($gFecCorte != "") {
            $qMovConA .= "$cAlfa.fcod$nPerAno.comfecxx <= \"$gFecCorte\" AND ";
          } else {
            $qMovConA .= "$cAlfa.fcod$nPerAno.comfecxx BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
          }
          if($gDocNro!=""){
            $qMovConA .= "(";
            $qMovConA .= "($cAlfa.fcod$nPerAno.comcsccx = \"$gDocNro\" AND $cAlfa.fcod$nPerAno.comseqcx = \"$gDocSuf\") OR ";
            $qMovConA .= "($cAlfa.fcod$nPerAno.docidxxx = \"$gDocNro\" AND $cAlfa.fcod$nPerAno.docsufxx = \"$gDocSuf\") OR ";
            $qMovConA .= "$cAlfa.fcod$nPerAno.comtraxx LIKE \"%-$gDocNro-%\" OR ";
            $qMovConA .= "$cAlfa.fcoc$nPerAno.comfpxxx LIKE \"%-$gDocNro-%\" OR ";
            $qMovConA .= ") AND ";
          }
          $qMovConA .= "(($cAlfa.fcod$nPerAno.comidxxx != \"F\" && $cAlfa.fcod$nPerAno.regestxx IN (\"ACTIVO\",\"PROVISIONAL\")) OR ($cAlfa.fcod$nPerAno.comidxxx = \"F\" && $cAlfa.fcod$nPerAno.regestxx = \"ACTIVO\")) ";
          $qMovConA .= "GROUP BY $cAlfa.fcod$nPerAno.sucidxxx, $cAlfa.fcod$nPerAno.docidxxx, $cAlfa.fcod$nPerAno.docsufxx, $cAlfa.fcod$nPerAno.comtraxx, $cAlfa.fcoc$nPerAno.comfpxxx ";
          $qMovConA .= "LIMIT 0,1";
          
          $nQueryTimeStart = microtime(true); $xMovConA  = mysql_query($qMovConA,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
          // echo "\nAnticipo: \n".mysql_num_rows($xMovConA)."~".$qMovConA."\n";
          $xFree = mysql_free_result($xMovConA);

          $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
          $xRNR = mysql_fetch_array($xNumRows);
          $nCanReg = $xRNR['FOUND_ROWS()'];
          $xFree = mysql_free_result($xNumRows);

          echo "Anticipo: ".$nPerAno."~".$nCanReg."\n";
          for ($nQ=0;$nQ<$nCanReg;$nQ+=$nNumReg) {
            ## Credito suma, debito resta
            $qMovConA  = "SELECT ";
            $qMovConA .= "$cAlfa.fcod$nPerAno.sucidxxx, ";
            $qMovConA .= "$cAlfa.fcod$nPerAno.docidxxx, "; 
            $qMovConA .= "$cAlfa.fcod$nPerAno.docsufxx, "; 
            $qMovConA .= "$cAlfa.fcod$nPerAno.comcsccx, "; 
            $qMovConA .= "$cAlfa.fcod$nPerAno.comseqcx, "; 
            $qMovConA .= "$cAlfa.fcod$nPerAno.comtraxx, ";
            $qMovConA .= "$cAlfa.fcoc$nPerAno.comfpxxx, ";
            $qMovConA .= "SUM(IF($cAlfa.fcod$nPerAno.commovxx=\"C\",IF($cAlfa.fcod$nPerAno.puctipej=\"L\" OR $cAlfa.fcod$nPerAno.puctipej=\"\", $cAlfa.fcod$nPerAno.comvlrxx, $cAlfa.fcod$nPerAno.comvlrnf),IF($cAlfa.fcod$nPerAno.puctipej=\"L\" OR $cAlfa.fcod$nPerAno.puctipej=\"\", $cAlfa.fcod$nPerAno.comvlrxx*-1, $cAlfa.fcod$nPerAno.comvlrnf*-1))) as comvlrxx ";
            $qMovConA .= "FROM $cAlfa.fcod$nPerAno ";
            $qMovConA .= "LEFT JOIN $cAlfa.fcoc$nPerAno ON ";
            $qMovConA .= "$cAlfa.fcoc$nPerAno.comidxxx = $cAlfa.fcod$nPerAno.comidxxx AND ";
            $qMovConA .= "$cAlfa.fcoc$nPerAno.comcodxx = $cAlfa.fcod$nPerAno.comcodxx AND ";
            $qMovConA .= "$cAlfa.fcoc$nPerAno.comcscxx = $cAlfa.fcod$nPerAno.comcscxx AND ";
            $qMovConA .= "$cAlfa.fcoc$nPerAno.comcsc2x = $cAlfa.fcod$nPerAno.comcsc2x ";
            $qMovConA .= "WHERE ";
            $qMovConA .= "$cAlfa.fcod$nPerAno.pucidxxx IN ($cPucIdAnt) AND ";
            if ($gFecCorte != "") {
              $qMovConA .= "$cAlfa.fcod$nPerAno.comfecxx <= \"$gFecCorte\" AND ";
            } else {
              $qMovConA .= "$cAlfa.fcod$nPerAno.comfecxx BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
            }
            if($gDocNro!=""){
              $qMovConA .= "(";
              $qMovConA .= "($cAlfa.fcod$nPerAno.comcsccx = \"$gDocNro\" AND $cAlfa.fcod$nPerAno.comseqcx = \"$gDocSuf\") OR ";
              $qMovConA .= "($cAlfa.fcod$nPerAno.docidxxx = \"$gDocNro\" AND $cAlfa.fcod$nPerAno.docsufxx = \"$gDocSuf\") OR ";
              $qMovConA .= "$cAlfa.fcod$nPerAno.comtraxx LIKE \"%-$gDocNro-%\" OR ";
              $qMovConA .= "$cAlfa.fcoc$nPerAno.comfpxxx LIKE \"%-$gDocNro-%\" OR ";
              $qMovConA .= ") AND ";
            }
            $qMovConA .= "(($cAlfa.fcod$nPerAno.comidxxx != \"F\" && $cAlfa.fcod$nPerAno.regestxx IN (\"ACTIVO\",\"PROVISIONAL\")) OR ($cAlfa.fcod$nPerAno.comidxxx = \"F\" && $cAlfa.fcod$nPerAno.regestxx = \"ACTIVO\")) ";
            $qMovConA .= "GROUP BY $cAlfa.fcod$nPerAno.sucidxxx, $cAlfa.fcod$nPerAno.docidxxx, $cAlfa.fcod$nPerAno.docsufxx, $cAlfa.fcod$nPerAno.comtraxx, $cAlfa.fcoc$nPerAno.comfpxxx ";
            $qMovConA .= "LIMIT $nQ,$nNumReg";
            $nQueryTimeStart = microtime(true); $xMovConA  = mysql_query($qMovConA,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
            // echo "\nAnticipo: \n".mysql_num_rows($xMovConA)."~".$qMovConA."\n";
          
            $nCanReg01 = 0;
            $qInsert   = "";
            while ($xRMC = mysql_fetch_assoc($xMovConA)) {
              $nCanReg01++;
              if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = $objEstructuras->fnReiniciarConexionDBEstadoCuentaTramite($xConexion01); }

              //Inicializando Do
              $cSucId  = "";
              $cDocId  = "";
              $cDocSuf = "";
              $cComCscC= "";
              $cComSeqC= "";

              // Es una factura y se debe verificar el DO exacto
              // Teniendo en cuenta que la sucursal del DO puede ser diferente al de la factura
              if ($xRMC['comtraxx'] != '' && $xRMC['comfpxxx'] != '') {
                $nEncDo = 0;
                $vTramite = explode("-",$xRMC['comtraxx']);
                $cSucId  = $vTramite[0];
                $cDocId  = "";
                for($nD=1; $nD<count($vTramite)-1; $nD++) {
                  $cDocId .= "{$vTramite[$nD]}-";
                }
                $cDocId  = substr($cDocId, 0,-1);
                $cDocSuf = $vTramite[count($vTramite)-1];

                $mDoiId = explode("|",$xRMC['comfpxxx']);
                for ($i=0;$i<count($mDoiId);$i++) {
                  if($mDoiId[$i] != ""){
                    $vDoiId  = explode("~",$mDoiId[$i]);
                    if($cDocId == $vDoiId[2] && $cDocSuf == $vDoiId[3]) {
                      $nEncDo = 1;
                      $cSucId  = $vDoiId[15];
                      $cDocId  = $vDoiId[2];
                      $cDocSuf = $vDoiId[3];
                    }
                  }//if($mDoiId[$i] != ""){
                }//for ($i=0;$i<count($mDoiId);$i++) {    
                
                if ($nEncDo == 0) {
                  $cComCscC= $xRMC['comcsccx'];
                  $cComSeqC= $xRMC['comseqcx'];
                }                
              } else {
                if ($xRMC['sucidxxx'] != "" && $xRMC['docidxxx'] != "" && $xRMC['docsufxx'] != "") {
                  $cSucId  = $xRMC['sucidxxx'];
                  $cDocId  = $xRMC['docidxxx'];
                  $cDocSuf = $xRMC['docsufxx'];
                } else {
                  $cComCscC= $xRMC['comcsccx'];
                  $cComSeqC= $xRMC['comseqcx'];
                }
              }

              //Se inserta
              $qInsert .= "(\"Anticipos\",";
              $qInsert .= "\"$cSucId\",";
              $qInsert .= "\"$cDocId\",";
              $qInsert .= "\"$cDocSuf\",";
              $qInsert .= "\"".(($cDocId != "") ? $cComCscC : "")."\",";
              $qInsert .= "\"".(($cDocId != "") ? $cComSeqC : "")."\",";
              $qInsert .= "\"{$xRMC['comvlrxx']}\"), ";
            }
            $xFree = mysql_free_result($xMovConA);
            //Se hace reinicio de conexion y se inserta
            if ($qInsert != "") {
              $xConexion01 = $objEstructuras->fnReiniciarConexionDBEstadoCuentaTramite($xConexion01);
              $qInsert   = $qInCab . substr($qInsert, 0, -2);
              $nQueryTimeStart = microtime(true); $xInsert  = mysql_query($qInsert,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
              // echo mysql_affected_rows($xConexion01)."~".mysql_error($xConexion01)."\n\n";
            }            
          }
          
          ## Consulto los Anticipos Proveedor
          $qMovConB  = "SELECT ";
          $qMovConB .= "SQL_CALC_FOUND_ROWS sucidxxx ";
          $qMovConB .= "FROM $cAlfa.fcod$nPerAno ";
          $qMovConB .= "WHERE ";
          $qMovConB .= "$cAlfa.fcod$nPerAno.pucidxxx IN ($cPucIdAp) AND ";
          if ($gFecCorte != "") {
            $qMovConB .= "$cAlfa.fcod$nPerAno.comfecxx <= \"$gFecCorte\" AND ";
          } else {
            $qMovConB .= "$cAlfa.fcod$nPerAno.comfecxx BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
          }
          $qMovConB .= "$cAlfa.fcod$nPerAno.sucidxxx != \"\" AND ";
          $qMovConB .= "$cAlfa.fcod$nPerAno.regestxx  = \"ACTIVO\" ";
          $qMovConB .= "GROUP BY $cAlfa.fcod$nPerAno.sucidxxx, $cAlfa.fcod$nPerAno.docidxxx, $cAlfa.fcod$nPerAno.docsufxx ";
          $qMovConB .= "LIMIT 0,1";
          $nQueryTimeStart = microtime(true); $xMovConB  = mysql_query($qMovConB,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
          // echo "\nCxP Anticipo Proveedores: \n".mysql_num_rows($xMovConB)."~".$qMovConB."\n";
          $xFree = mysql_free_result($xMovConB);

          $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
          $xRNR = mysql_fetch_array($xNumRows);
          $nCanReg = $xRNR['FOUND_ROWS()'];
          $xFree = mysql_free_result($xNumRows);

          echo "Anticipo Proveedores: ".$nPerAno."~".$nCanReg."\n";
          for ($nQ=0;$nQ<$nCanReg;$nQ+=$nNumReg) {
            $qMovConB  = "SELECT ";
            $qMovConB .= "$cAlfa.fcod$nPerAno.sucidxxx, ";
            $qMovConB .= "$cAlfa.fcod$nPerAno.docidxxx, "; 
            $qMovConB .= "$cAlfa.fcod$nPerAno.docsufxx, "; 
            $qMovConB .= "SUM(IF($cAlfa.fcod$nPerAno.commovxx=\"D\",IF($cAlfa.fcod$nPerAno.puctipej=\"L\" OR $cAlfa.fcod$nPerAno.puctipej=\"\", $cAlfa.fcod$nPerAno.comvlrxx, $cAlfa.fcod$nPerAno.comvlrnf),IF($cAlfa.fcod$nPerAno.puctipej=\"L\" OR $cAlfa.fcod$nPerAno.puctipej=\"\", $cAlfa.fcod$nPerAno.comvlrxx*-1, $cAlfa.fcod$nPerAno.comvlrnf*-1))) as comvlrxx ";
            $qMovConB .= "FROM $cAlfa.fcod$nPerAno ";
            $qMovConB .= "WHERE ";
            $qMovConB .= "$cAlfa.fcod$nPerAno.pucidxxx IN ($cPucIdAp) AND ";
            if ($gFecCorte != "") {
              $qMovConB .= "$cAlfa.fcod$nPerAno.comfecxx <= \"$gFecCorte\" AND ";
            } else {
              $qMovConB .= "$cAlfa.fcod$nPerAno.comfecxx BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
            }
            $qMovConB .= "$cAlfa.fcod$nPerAno.sucidxxx != \"\" AND ";
            $qMovConB .= "$cAlfa.fcod$nPerAno.regestxx  = \"ACTIVO\" ";
            $qMovConB .= "GROUP BY $cAlfa.fcod$nPerAno.sucidxxx, $cAlfa.fcod$nPerAno.docidxxx, $cAlfa.fcod$nPerAno.docsufxx ";
            $qMovConB .= "LIMIT $nQ,$nNumReg";
            $nQueryTimeStart = microtime(true); $xMovConB  = mysql_query($qMovConB,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
            // echo "\nCxP Anticipo Proveedores: \n".mysql_num_rows($xMovConB)."~".$qMovConB."\n";

            $nCanReg02 = 0;
            $qInsert   = "";
            while ($xRMC = mysql_fetch_assoc($xMovConB)) {
              $nCanReg02++;
              if (($nCanReg02 % _NUMREG_) == 0) { $xConexion01 = $objEstructuras->fnReiniciarConexionDBEstadoCuentaTramite($xConexion01); }

              //Se inserta
              $qInsert .= "(\"AntiProve\",";
              $qInsert .= "\"{$xRMC['sucidxxx']}\",";
              $qInsert .= "\"{$xRMC['docidxxx']}\",";
              $qInsert .= "\"{$xRMC['docsufxx']}\",";
              $qInsert .= "\"".(($xRMC['docidxxx'] != "") ? $xRMC['comcsccx'] : "")."\",";
              $qInsert .= "\"".(($xRMC['docidxxx'] != "") ? $xRMC['comseqcx'] : "")."\",";
              $qInsert .= "\"{$xRMC['comvlrxx']}\"), ";
            }
            $xFree = mysql_free_result($xMovConB);
            //Se hace reinicio de conexion y se inserta
            if ($qInsert != "") {
              $xConexion01 = $objEstructuras->fnReiniciarConexionDBEstadoCuentaTramite($xConexion01);
              $qInsert   = $qInCab . substr($qInsert, 0, -2);
              $nQueryTimeStart = microtime(true); $xInsert  = mysql_query($qInsert,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
              // echo mysql_affected_rows($xConexion01)."~".mysql_error($xConexion01)."\n";
            }
          }

          ## Buscando pagos a terceros
          $qMovConC  = "SELECT ";
          $qMovConC .= "SQL_CALC_FOUND_ROWS $cAlfa.fcod$nPerAno.sucidxxx ";
          $qMovConC .= "FROM $cAlfa.fcod$nPerAno ";
          $qMovConC .= "LEFT JOIN $cAlfa.fcoc$nPerAno ON ";
          $qMovConC .= "$cAlfa.fcoc$nPerAno.comidxxx = $cAlfa.fcod$nPerAno.comidxxx AND ";
          $qMovConC .= "$cAlfa.fcoc$nPerAno.comcodxx = $cAlfa.fcod$nPerAno.comcodxx AND ";
          $qMovConC .= "$cAlfa.fcoc$nPerAno.comcscxx = $cAlfa.fcod$nPerAno.comcscxx AND ";
          $qMovConC .= "$cAlfa.fcoc$nPerAno.comcsc2x = $cAlfa.fcod$nPerAno.comcsc2x ";
          $qMovConC .= "WHERE ";
          $qMovConC .= "$cAlfa.fcod$nPerAno.pucidxxx IN ($cPucIdPcc) AND ";
          if ($gFecCorte != "") {
            $qMovConC .= "$cAlfa.fcod$nPerAno.comfecxx <= \"$gFecCorte\" AND ";
          } else {
            $qMovConC .= "$cAlfa.fcod$nPerAno.comfecxx BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
          }
          if($gDocNro!=""){
            $qMovConC .= "(";
            $qMovConC .= "($cAlfa.fcod$nPerAno.comcsccx = \"$gDocNro\" AND $cAlfa.fcod$nPerAno.comseqcx = \"$gDocSuf\") OR ";
            $qMovConC .= "($cAlfa.fcod$nPerAno.docidxxx = \"$gDocNro\" AND $cAlfa.fcod$nPerAno.docsufxx = \"$gDocSuf\") OR ";
            $qMovConC .= "$cAlfa.fcod$nPerAno.comtraxx LIKE \"%-$gDocNro-%\" OR ";
            $qMovConC .= "$cAlfa.fcoc$nPerAno.comfpxxx LIKE \"%-$gDocNro-%\" OR ";
            $qMovConC .= ") AND ";
          }
          $qMovConC .= "(($cAlfa.fcod$nPerAno.comidxxx != \"F\" && $cAlfa.fcod$nPerAno.regestxx IN (\"ACTIVO\",\"PROVISIONAL\")) OR ($cAlfa.fcod$nPerAno.comidxxx = \"F\" && $cAlfa.fcod$nPerAno.regestxx = \"ACTIVO\")) ";
          $qMovConC .= "GROUP BY $cAlfa.fcod$nPerAno.sucidxxx, $cAlfa.fcod$nPerAno.docidxxx, $cAlfa.fcod$nPerAno.docsufxx, $cAlfa.fcod$nPerAno.comtraxx, $cAlfa.fcoc$nPerAno.comfpxxx ";
          $qMovConC .= "LIMIT 0,1";
          $nQueryTimeStart = microtime(true); $xMovConC  = mysql_query($qMovConC,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
          // echo "\nPCC: \n".mysql_num_rows($xMovConC)."~".$qMovConC."\n";
          $xFree = mysql_free_result($xMovConC);

          $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
          $xRNR = mysql_fetch_array($xNumRows);
          $nCanReg = $xRNR['FOUND_ROWS()'];
          $xFree = mysql_free_result($xNumRows);

          echo "PCC: ".$nPerAno."~".$nCanReg."\n";
          for ($nQ=0;$nQ<$nCanReg;$nQ+=$nNumReg) {
            ## Debito suma, credito resta
            $qMovConC  = "SELECT ";
            $qMovConC .= "$cAlfa.fcod$nPerAno.comidxxx, "; 
            $qMovConC .= "$cAlfa.fcod$nPerAno.sucidxxx, ";
            $qMovConC .= "$cAlfa.fcod$nPerAno.docidxxx, "; 
            $qMovConC .= "$cAlfa.fcod$nPerAno.docsufxx, "; 
            $qMovConC .= "$cAlfa.fcod$nPerAno.comcsccx, "; 
            $qMovConC .= "$cAlfa.fcod$nPerAno.comseqcx, ";
            $qMovConC .= "$cAlfa.fcod$nPerAno.comtraxx, ";
            $qMovConC .= "$cAlfa.fcoc$nPerAno.comfpxxx, ";
            $qMovConC .= "SUM(IF($cAlfa.fcod$nPerAno.commovxx=\"D\",IF($cAlfa.fcod$nPerAno.puctipej=\"L\" OR $cAlfa.fcod$nPerAno.puctipej=\"\", $cAlfa.fcod$nPerAno.comvlrxx, $cAlfa.fcod$nPerAno.comvlrnf),IF($cAlfa.fcod$nPerAno.puctipej=\"L\" OR $cAlfa.fcod$nPerAno.puctipej=\"\", $cAlfa.fcod$nPerAno.comvlrxx*-1, $cAlfa.fcod$nPerAno.comvlrnf*-1))) as comvlrxx ";
            $qMovConC .= "FROM $cAlfa.fcod$nPerAno ";
            $qMovConC .= "LEFT JOIN $cAlfa.fcoc$nPerAno ON ";
            $qMovConC .= "$cAlfa.fcoc$nPerAno.comidxxx = $cAlfa.fcod$nPerAno.comidxxx AND ";
            $qMovConC .= "$cAlfa.fcoc$nPerAno.comcodxx = $cAlfa.fcod$nPerAno.comcodxx AND ";
            $qMovConC .= "$cAlfa.fcoc$nPerAno.comcscxx = $cAlfa.fcod$nPerAno.comcscxx AND ";
            $qMovConC .= "$cAlfa.fcoc$nPerAno.comcsc2x = $cAlfa.fcod$nPerAno.comcsc2x ";
            $qMovConC .= "WHERE ";
            $qMovConC .= "$cAlfa.fcod$nPerAno.pucidxxx IN ($cPucIdPcc) AND ";
            if ($gFecCorte != "") {
              $qMovConC .= "$cAlfa.fcod$nPerAno.comfecxx <= \"$gFecCorte\" AND ";
            } else {
              $qMovConC .= "$cAlfa.fcod$nPerAno.comfecxx BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
            }
            if($gDocNro!=""){
              $qMovConC .= "(";
              $qMovConC .= "($cAlfa.fcod$nPerAno.comcsccx = \"$gDocNro\" AND $cAlfa.fcod$nPerAno.comseqcx = \"$gDocSuf\") OR ";
              $qMovConC .= "($cAlfa.fcod$nPerAno.docidxxx = \"$gDocNro\" AND $cAlfa.fcod$nPerAno.docsufxx = \"$gDocSuf\") OR ";
              $qMovConC .= "$cAlfa.fcod$nPerAno.comtraxx LIKE \"%-$gDocNro-%\" OR ";
              $qMovConC .= "$cAlfa.fcoc$nPerAno.comfpxxx LIKE \"%-$gDocNro-%\" OR ";
              $qMovConC .= ") AND ";
            }
            $qMovConC .= "(($cAlfa.fcod$nPerAno.comidxxx != \"F\" && $cAlfa.fcod$nPerAno.regestxx IN (\"ACTIVO\",\"PROVISIONAL\")) OR ($cAlfa.fcod$nPerAno.comidxxx = \"F\" && $cAlfa.fcod$nPerAno.regestxx = \"ACTIVO\")) ";
            $qMovConC .= "GROUP BY $cAlfa.fcod$nPerAno.sucidxxx, $cAlfa.fcod$nPerAno.docidxxx, $cAlfa.fcod$nPerAno.docsufxx, $cAlfa.fcod$nPerAno.comtraxx, $cAlfa.fcoc$nPerAno.comfpxxx ";
            $qMovConC .= "LIMIT $nQ,$nNumReg";
            $nQueryTimeStart = microtime(true); $xMovConC  = mysql_query($qMovConC,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
            // echo "\nPCC: \n".mysql_num_rows($xMovConC)."~".$qMovConC."\n";

            $nCanReg03 = 0;
            $qInsert   = "";
            while ($xRMC = mysql_fetch_assoc($xMovConC)) {
              $nIncluir = 0;
              $nCanReg03++;
              if (($nCanReg03 % _NUMREG_) == 0) { $xConexion01 = $objEstructuras->fnReiniciarConexionDBEstadoCuentaTramite($xConexion01); }
              
              //Inicializando Do
              $cSucId  = "";
              $cDocId  = "";
              $cDocSuf = "";
              $cComCscC= "";
              $cComSeqC= "";

              // Es una factura y se debe verificar el DO exacto
              // Teniendo en cuenta que la sucursal del DO puede ser diferente al de la factura
              if ($xRMC['comtraxx'] != '' && $xRMC['comfpxxx'] != '') {
                $nEncDo = 0;
                $vTramite = explode("-",$xRMC['comtraxx']);
                $cSucId  = $vTramite[0];
                $cDocId  = "";
                for($nD=1; $nD<count($vTramite)-1; $nD++) {
                  $cDocId .= "{$vTramite[$nD]}-";
                }
                $cDocId  = substr($cDocId, 0,-1);
                $cDocSuf = $vTramite[count($vTramite)-1];

                $mDoiId = explode("|",$xRMC['comfpxxx']);
                for ($i=0;$i<count($mDoiId);$i++) {
                  if($mDoiId[$i] != ""){
                    $vDoiId  = explode("~",$mDoiId[$i]);
                    if($cDocId == $vDoiId[2] && $cDocSuf == $vDoiId[3]) {
                      $nEncDo = 1;
                      $cSucId  = $vDoiId[15];
                      $cDocId  = $vDoiId[2];
                      $cDocSuf = $vDoiId[3];
                    }
                  }//if($mDoiId[$i] != ""){
                }//for ($i=0;$i<count($mDoiId);$i++) {    
                
                if ($nEncDo == 0) {
                  $cComCscC= $xRMC['comcsccx'];
                  $cComSeqC= $xRMC['comseqcx'];
                }                
              } else {
                if ($xRMC['sucidxxx'] != "" && $xRMC['docidxxx'] != "" && $xRMC['docsufxx'] != "") {
                  $cSucId  = $xRMC['sucidxxx'];
                  $cDocId  = $xRMC['docidxxx'];
                  $cDocSuf = $xRMC['docsufxx'];
                } else {
                  $cComCscC= $xRMC['comcsccx'];
                  $cComSeqC= $xRMC['comseqcx'];
                }
              }
              
              //Se inserta
              $qInsert .= "(\"SumPCC\",";
              $qInsert .= "\"$cSucId\",";
              $qInsert .= "\"$cDocId\",";
              $qInsert .= "\"$cDocSuf\",";
              $qInsert .= "\"".(($cDocId != "") ? $cComCscC : "")."\",";
              $qInsert .= "\"".(($cDocId != "") ? $cComSeqC : "")."\",";
              $qInsert .= "\"{$xRMC['comvlrxx']}\"), ";
            }
            $xFree = mysql_free_result($xMovConC);

            //Se hace reinicio de conexion y se inserta
            if ($qInsert != "") {
              $xConexion01 = $objEstructuras->fnReiniciarConexionDBEstadoCuentaTramite($xConexion01);
              $qInsert   = $qInCab . substr($qInsert, 0, -2);
              $nQueryTimeStart = microtime(true); $xInsert  = mysql_query($qInsert,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
              // echo mysql_affected_rows($xConexion01)."~".mysql_error($xConexion01)."\n";
            }
          }
        }

        //Borrando los registros de la tabla temporal con valor cero.
        $qDelete  = "DELETE FROM $cAlfa.$mRetTabMov[1] ";
        $qDelete .= "WHERE ";
        $qDelete .= "comvlrxx = \"0\"";
        $nQueryTimeStart = microtime(true); $xDelete  = mysql_query($qDelete,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);

        //si se encontraron anticipos o pagos a terceros sin DO completo, estos se asignan al primer DO
        $qSinDo  = "SELECT * ";
        $qSinDo .= "FROM $cAlfa.$mRetTabMov[1] ";
        $qSinDo .= "WHERE ";
        $qSinDo .= "docidxxx = \"\"";
        $nQueryTimeStart = microtime(true); $xSinDo  = mysql_query($qSinDo,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
        // echo "\nSinDo: \n".mysql_num_rows($xSinDo)."~".$qSinDo."\n";
        $nCanReg04 = 0;
        while ($xRSD = mysql_fetch_assoc($xSinDo)) {
          $nCanReg04++;
          if (($nCanReg04 % _NUMREG_) == 0) { $xConexion01 = $objEstructuras->fnReiniciarConexionDBEstadoCuentaTramite($xConexion01); }

          $qDatDo  = "SELECT sucidxxx, docidxxx, docsufxx ";
          $qDatDo .= "FROM $cAlfa.sys00121 ";
          $qDatDo .= "WHERE ";
          $qDatDo .= "docidxxx = \"{$xRSD['comcsccx']}\" AND ";
          $qDatDo .= "docsufxx = \"{$xRSD['comseqcx']}\" LIMIT 0,1";
          $nQueryTimeStart = microtime(true); $xDatDo  = mysql_query($qDatDo,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
          // echo "\nDO: \n".."~".$qDatDo."\n";
          if (mysql_num_rows($xDatDo) > 0) {
            $vDatDo = mysql_fetch_assoc($xDatDo);

            $qUpdate  = "UPDATE $cAlfa.$mRetTabMov[1] SET ";
            $qUpdate .= "sucidxxx = \"{$vDatDo['sucidxxx']}\", ";
            $qUpdate .= "docidxxx = \"{$vDatDo['docidxxx']}\", ";
            $qUpdate .= "docsufxx = \"{$vDatDo['docsufxx']}\" ";
            $qUpdate .= "WHERE ";
            $qUpdate .= "lineaidx = \"{$xRSD['lineaidx']}\"";
            $nQueryTimeStart = microtime(true); $xUpdate  = mysql_query($qUpdate,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
          }
        }

        //Cabecera Insert tabla temporal de saldos por tramite
        echo $gEstado."\n";
        $qInCab  = "INSERT INTO $cAlfa.$mRetTabSal[1] (";
        $qInCab .= "sucidxxx,";
        $qInCab .= "docidxxx,";
        $qInCab .= "docsufxx,";
        if ($gEstado == "ACTIVOCONSALDO") {
          $qInCab .= "comidxxx,";
          $qInCab .= "comcodxx,";
          $qInCab .= "ccoidxxx,";
          $qInCab .= "pucidxxx,";
          $qInCab .= "succomxx,";
          $qInCab .= "doctipxx,";
          $qInCab .= "docpedxx,";
          $qInCab .= "cliidxxx,";
          $qInCab .= "diridxxx,";
          $qInCab .= "docfacxx,";
          $qInCab .= "docffecx,";
          $qInCab .= "docusrce,";
          $qInCab .= "docfecce,";
          $qInCab .= "regfcrex,";
          $qInCab .= "regestxx,";
        }
        $qInCab .= "comvlran,";
        $qInCab .= "comvlrap,";
        $qInCab .= "comvlrpc) VALUES ";
        
        //Calculando Saldos por tramite
        $qSalDo  = "SELECT ";
        $qSalDo .= "SQL_CALC_FOUND_ROWS  ";
        $qSalDo .= "sucidxxx, "; 
        $qSalDo .= "docidxxx, ";
        $qSalDo .= "docsufxx, ";
        $qSalDo .= "SUM(IF(tiposalx = \"Anticipos\", comvlrxx, 0)) AS comvlran, ";
        $qSalDo .= "SUM(IF(tiposalx = \"AntiProve\", comvlrxx, 0)) AS comvlrap, ";
        $qSalDo .= "SUM(IF(tiposalx = \"SumPCC\", comvlrxx, 0)) AS comvlrpc ";
        $qSalDo .= "FROM $cAlfa.$mRetTabMov[1] ";
        $qSalDo .= "GROUP BY sucidxxx, docidxxx, docsufxx ";
        $qSalDo .= "HAVING comvlran != 0 OR comvlrap != 0 OR comvlrpc != 0 ";
        $qSalDo .= "LIMIT 0,1 ";
        $nQueryTimeStart = microtime(true); $xSalDo  = mysql_query($qSalDo,$xConexion01);
        $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
        echo "\nSalDo: \n".mysql_num_rows($xSalDo)."~".$qSalDo."\n";
        $xFree = mysql_free_result($xSalDo);

        $xNumRows = mysql_query("SELECT FOUND_ROWS();",$xConexion01);
        $xRNR = mysql_fetch_array($xNumRows);
        $nCanReg = $xRNR['FOUND_ROWS()'];
        $xFree = mysql_free_result($xNumRows);

        for ($nQ=0;$nQ<$nCanReg;$nQ+=$nNumReg) {
          $qSalDo  = "SELECT ";
          $qSalDo .= "sucidxxx, "; 
          $qSalDo .= "docidxxx, ";
          $qSalDo .= "docsufxx, ";
          $qSalDo .= "SUM(IF(tiposalx = \"Anticipos\", comvlrxx, 0)) AS comvlran, ";
          $qSalDo .= "SUM(IF(tiposalx = \"AntiProve\", comvlrxx, 0)) AS comvlrap, ";
          $qSalDo .= "SUM(IF(tiposalx = \"SumPCC\", comvlrxx, 0)) AS comvlrpc ";
          $qSalDo .= "FROM $cAlfa.$mRetTabMov[1] ";
          $qSalDo .= "GROUP BY sucidxxx, docidxxx, docsufxx ";
          $qSalDo .= "HAVING comvlran != 0 OR comvlrap != 0 OR comvlrpc != 0 ";
          $qSalDo .= "LIMIT $nQ,$nNumReg";
          $nQueryTimeStart = microtime(true); $xSalDo  = mysql_query($qSalDo,$xConexion01);
          $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
          echo "\nSalDo: \n".mysql_num_rows($xSalDo)."~".$qSalDo."\n";
          $nCanReg04 = 0;
          $qInsert   = "";
          while ($xRSD = mysql_fetch_assoc($xSalDo)) {
            $nCanReg04++;
            if (($nCanReg04 % _NUMREG_) == 0) { $xConexion01 = $objEstructuras->fnReiniciarConexionDBEstadoCuentaTramite($xConexion01); }

            //Cuando el filtro es ACTIVOCONSALDO solo se muestran los DO que tienen saldo
            if ($gEstado == "ACTIVOCONSALDO") {
              $qDatDo  = "SELECT ";
              $qDatDo .= "comidxxx,";
              $qDatDo .= "comcodxx,";
              $qDatDo .= "ccoidxxx,";
              $qDatDo .= "pucidxxx,";
              $qDatDo .= "succomxx,";
              $qDatDo .= "doctipxx,";
              $qDatDo .= "docpedxx,";
              $qDatDo .= "cliidxxx,";
              $qDatDo .= "diridxxx,";
              $qDatDo .= "docfacxx,";
              $qDatDo .= "docffecx,";
              $qDatDo .= "docusrce,";
              $qDatDo .= "docfecce,";
              $qDatDo .= "regfcrex,";
              $qDatDo .= "regestxx ";
              $qDatDo .= "FROM $cAlfa.sys00121 ";
              $qDatDo .= "WHERE ";
              $qDatDo .= "$cAlfa.sys00121.sucidxxx = \"{$xRSD['sucidxxx']}\" AND ";
              $qDatDo .= "$cAlfa.sys00121.docidxxx = \"{$xRSD['docidxxx']}\" AND ";
              $qDatDo .= "$cAlfa.sys00121.docsufxx = \"{$xRSD['docsufxx']}\" LIMIT 0,1 ";
              $xDatDo = f_MySql("SELECT","",$qDatDo,$xConexion01,"");
              // echo "\nDatDo: \n".mysql_num_rows($xDatDo)."~".$qDatDo."\n";
              $vDatDo = array();
              $vDatDo = mysql_fetch_assoc($xDatDo);
            }
            //Insert
            $qInsert .= "(\"{$xRSD['sucidxxx']}\",";
            $qInsert .= "\"{$xRSD['docidxxx']}\",";
            $qInsert .= "\"{$xRSD['docsufxx']}\",";
            if ($gEstado == "ACTIVOCONSALDO") {
              $qInsert .= "\"{$vDatDo['comidxxx']}\",";
              $qInsert .= "\"{$vDatDo['comcodxx']}\",";
              $qInsert .= "\"{$vDatDo['ccoidxxx']}\",";
              $qInsert .= "\"{$vDatDo['pucidxxx']}\",";
              $qInsert .= "\"{$vDatDo['succomxx']}\",";
              $qInsert .= "\"{$vDatDo['doctipxx']}\",";
              $qInsert .= "\"{$vDatDo['docpedxx']}\",";
              $qInsert .= "\"{$vDatDo['cliidxxx']}\",";
              $qInsert .= "\"{$vDatDo['diridxxx']}\",";
              $qInsert .= "\"{$vDatDo['docfacxx']}\",";
              $qInsert .= "\"{$vDatDo['docffecx']}\",";
              $qInsert .= "\"{$vDatDo['docusrce']}\",";
              $qInsert .= "\"{$vDatDo['docfecce']}\",";
              $qInsert .= "\"{$vDatDo['regfcrex']}\",";
              $qInsert .= "\"{$vDatDo['regestxx']}\",";
            }
            $qInsert .= "\"{$xRSD['comvlran']}\",";
            $qInsert .= "\"{$xRSD['comvlrap']}\",";
            $qInsert .= "\"{$xRSD['comvlrpc']}\"), ";
          }

          if ($qInsert != "") {
            $xConexion01 = $objEstructuras->fnReiniciarConexionDBEstadoCuentaTramite($xConexion01); 
            $qInsert   = $qInCab . substr($qInsert, 0, -2);
            $nQueryTimeStart = microtime(true); $xInsert  = mysql_query($qInsert,$xConexion01);
            $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
          }
        }
        
        //Cuando el filtro es ACTIVOCONSALDO solo se muestran los DO que tienen saldo
        //por lo que se hace la busqueda sobre la tabla temporal
        if ($gEstado == "ACTIVOCONSALDO") {
          $cTabla = $mRetTabSal[1];
        } else {
          $cTabla = "sys00121";
        }

        ##Se realiza la consulta principal a la tabla de los DO´s
        $qDatDoi  = "SELECT ";
        $qDatDoi .= "$cAlfa.$cTabla.sucidxxx, ";
        $qDatDoi .= "$cAlfa.$cTabla.docidxxx, ";
        $qDatDoi .= "$cAlfa.$cTabla.docsufxx, ";
        $qDatDoi .= "$cAlfa.$cTabla.comidxxx, ";
        $qDatDoi .= "$cAlfa.$cTabla.comcodxx, ";
        $qDatDoi .= "$cAlfa.$cTabla.ccoidxxx, ";
        $qDatDoi .= "$cAlfa.$cTabla.pucidxxx, ";
        $qDatDoi .= "$cAlfa.$cTabla.succomxx, ";
        $qDatDoi .= "$cAlfa.$cTabla.doctipxx, ";
        $qDatDoi .= "$cAlfa.$cTabla.docpedxx, ";
        $qDatDoi .= "$cAlfa.$cTabla.cliidxxx, ";
        $qDatDoi .= "$cAlfa.$cTabla.diridxxx, ";
        $qDatDoi .= "$cAlfa.$cTabla.docfacxx, ";
        $qDatDoi .= "$cAlfa.$cTabla.docffecx, ";
        $qDatDoi .= "$cAlfa.$cTabla.regfcrex, ";
        $qDatDoi .= "$cAlfa.$cTabla.docusrce, ";
        $qDatDoi .= "$cAlfa.$cTabla.docfecce, ";
        $qDatDoi .= "$cAlfa.$cTabla.regestxx, ";
        if ($gEstado == "ACTIVOCONSALDO") {
          $qDatDoi .= "lineaidx, ";
          $qDatDoi .= "comvlran, ";
          $qDatDoi .= "comvlrap, ";
          $qDatDoi .= "comvlrpc, ";
        }
        $qDatDoi = substr($qDatDoi, 0, -2)." ";
        $qDatDoi .= "FROM $cAlfa.$cTabla ";
        $qDatDoi .= "WHERE ";
        $nExacto = 0;
        if($gDocNro!=""){
          $nExacto = 1;
          $qDatDoi .= "$cAlfa.$cTabla.sucidxxx = \"$gSucId\" AND ";
          $qDatDoi .= "$cAlfa.$cTabla.docidxxx = \"$gDocNro\" AND ";
          $qDatDoi .= "$cAlfa.$cTabla.docsufxx = \"$gDocSuf\" AND ";
        }
        if($gCcoId!=""){
          $qDatDoi .= "$cAlfa.$cTabla.sucidxxx = \"$gSucCco\" AND ";
        }
        if($gTerId!=""){
          $qDatDoi .= "$cAlfa.$cTabla.cliidxxx = \"$gTerId\" AND ";
        }
        if($gDirId!=""){
          $qDatDoi .= "$cAlfa.$cTabla.diridxxx = \"$gDirId\" AND ";
        }
        if ($nExacto == 0) {
          if ($gFecCorte != "") {
            $qDatDoi .= "$cAlfa.$cTabla.regfcrex <= \"$gFecCorte\" AND ";
            $qDatDoi .= "$cAlfa.$cTabla.regfcrex != \"0000-00-00\" AND ";
          } else {
            $qDatDoi .= "$cAlfa.$cTabla.regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" AND ";
          }
        }
        if ($gFecCorte == "") {
          // Cuando el filtro sea ACTIVO, también se traen las facturadas para ADEMÁS pintar SOLO las provisionales de dichas facturadas.
          // Cuando el filtro sea FACTURADO, las provisionales NO deben pintarse.
          if ($gEstado == "FACTURADO") {
            $qDatDoi .= "$cAlfa.$cTabla.docfacxx NOT LIKE \"%-P%\" AND ";
            $qDatDoi .= "$cAlfa.$cTabla.regestxx = \"$gEstado\" ";
          } else {
            $qDatDoi .= "(";
            $qDatDoi .= "$cAlfa.$cTabla.regestxx = \"ACTIVO\" OR ";
            $qDatDoi .= "($cAlfa.$cTabla.regestxx = \"FACTURADO\" AND $cAlfa.$cTabla.docfacxx LIKE \"%-P%\")";
            $qDatDoi .= ") ";
          }
        } else {
          $qDatDoi = substr($qDatDoi, 0, -4);
        }
        $qDatDoi .= "ORDER BY $cAlfa.$cTabla.regfcrex";
        $xDatDoi = f_MySql("SELECT","",$qDatDoi,$xConexion01,"");
        // f_Mensaje(__FILE__,__LINE__,$qDatDoi."~".mysql_num_rows($xDatDoi));
        echo $qDatDoi."~".mysql_num_rows($xDatDoi)."\n\n";

        // Variables para acumular sumas de valores 
        $nTotAnti  = 0;
        $nAntPro   = 0;
        $nTotPag   = 0;
        $nTotSal   = 0;
        // Contador de registros
        $nCanReg = 0;
        $nCanTra = 0;
        #Recorro los Do's
        while ($xDD = mysql_fetch_array($xDatDoi)) {
          $nCanReg++;
          if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = $objEstructuras->fnReiniciarConexionDBEstadoCuentaTramite($xConexion01); } 

          $nPintar = 0;  // bandera para pintar registros según el filtro
          $cEstado = ""; // estado de factura provisional cuando se selecciona el filtro ACTIVO
          
          if ($xDD['regestxx'] == "FACTURADO") { // si el estado es FACTURADO, pueden ser provisionales, lo cual es lo que interesa aquí
            $cNumFactura = explode("-",$xDD['docfacxx']); // se explota por guión (-) el número de la factura
            if ($gEstado == "ACTIVO") { // si el filtro se seleccionó ACTIVO
              if (substr($cNumFactura[2],0,1) != "P") { // si la posición 2 no inicia en "P", indica que NO es PROVISIONAL, no se debe pintar para filtro ACTIVO
                if ($gFecCorte == "") {
                  $nPintar = 1; // no se pinta
                }
              } else {
                $cEstado = "PROFORMA"; // solo cuando el filtro es ACTIVO y la factura es PROVISIONAL
              }
            } elseif ($gEstado == "FACTURADO") { // si el filtro se seleccionó FACTURADO
              if (substr($cNumFactura[2],0,1) == "P") { // si la posición 2 inicia en "P", indica que es PROVISIONAL, no se debe  pintar para filtro FACTURADO
                if ($gFecCorte == "") {
                  $nPintar = 1; // no se pinta
                }
              }
            }
          }

          // f_Mensaje(__FILE__,__LINE__,$xDD['regestxx']);
          if ($nPintar == 0) { // Inicia pintado de registros
            $nAnticipo = 0;
            $nAntProve = 0;
            $nPagosTer = 0;
            $vSalDo    = array();

            //Se debe buscar en la tabla de saldos
            if ($gEstado != "ACTIVOCONSALDO") {
              // Anticipos - Anticipos proveedor - Pagos a terceros
              $qSalDo  = "SELECT * ";
              $qSalDo .= "FROM $cAlfa.$mRetTabSal[1] ";
              $qSalDo .= "WHERE ";
              $qSalDo .= "sucidxxx = \"{$xDD['sucidxxx']}\" AND ";
              $qSalDo .= "docidxxx = \"{$xDD['docidxxx']}\" AND ";
              $qSalDo .= "docsufxx = \"{$xDD['docsufxx']}\" LIMIT 0,1 ";
              $nQueryTimeStart = microtime(true); $xSalDo  = mysql_query($qSalDo,$xConexion01);
              $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
              if (mysql_num_rows($xCon) > 0) {
                $vSalDo = mysql_fetch_assoc($xSalDo);
                $nAnticipo = $vSalDo['comvlran'];
                $nAntProve = $vSalDo['comvlrap'];
                $nPagosTer = $vSalDo['comvlrpc'];
              }
            } else {
              $vSalDo['lineaidx'] = $xDD['lineaidx'];

              $nAnticipo = $xDD['comvlran'];
              $nAntProve = $xDD['comvlrap'];
              $nPagosTer = $xDD['comvlrpc'];
            }
            $nSaldo = $nAnticipo - $nAntProve - $nPagosTer;

            //Logica para determinar si el registro se muestra o no en el reporte
            $cPintar = "NO";
            if ($gEstado == "ACTIVO") {
              // Se pintan los DO en estado activo o que tengan estado FACTURADO pero con saldo
              if ($xDD['regestxx'] == "ACTIVO" || ($xDD['regestxx'] == "FACTURADO" && ($nAnticipo != 0 || $nAntProve != 0 || $nPagosTer != 0))) {
                $cPintar = "SI";
              }
            } elseif ($gEstado == "ACTIVOCONSALDO") {
              // Solo se pintan los DO's que el saldo sea diferente de Cero
              if ($nAnticipo != 0 || $nAntProve != 0 || $nPagosTer != 0) {
                $cPintar = "SI";
              }
            } elseif ($gEstado == "FACTURADO") {
                if ($xDD['regestxx'] == "FACTURADO") {
                  $cPintar = "SI";
                  if (substr($cNumFactura[2],0,1) == "P") { // si la posición 2 inicia en "P", indica que es PROVISIONAL, no se debe  pintar para filtro FACTURADO
                    $cPintar = "NO"; // no se pinta
                  }
                }
            }

            if ($cPintar == "SI") {
              //Cantidad de tramites
              $nCanTra++;

              echo "{$xDD['sucidxxx']}-{$xDD['docidxxx']}-{$xDD['docsufxx']}\n";
              if ($vSalDo['lineaidx'] != "") {
                //Actualziando registros
                $qUpdate  = "UPDATE $cAlfa.$mRetTabSal[1] SET ";
                $qUpdate .= "marcaxxx = \"SI\" ";
                $qUpdate .= "WHERE ";
                $qUpdate .= "lineaidx = \"{$vSalDo['lineaidx']}\"";
                $nQueryTimeStart = microtime(true); $xUpdate  = mysql_query($qUpdate,$xConexion01);
                $nQueryTime = (microtime(true) - $nQueryTimeStart); $objEstructuras->fnMysqlQueryInfo($xConexion01,$nQueryTime);
              }

              #Busco el nombre del cliente
              $qCliNom  = "SELECT ";
              $qCliNom .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
              $qCliNom .= "FROM $cAlfa.SIAI0150 ";
              $qCliNom .= "WHERE ";
              $qCliNom .= "CLIIDXXX = \"{$xDD['cliidxxx']}\" LIMIT 0,1";
              $xCliNom = f_MySql("SELECT","",$qCliNom,$xConexion01,"");
              if (mysql_num_rows($xCliNom) > 0) {
                $xRCN = mysql_fetch_array($xCliNom);
                $xDD['clinomxx'] = $xRCN['clinomxx'];
              } else {
                $xDD['clinomxx'] = "CLIENTE SIN NOMBRE";
              }

              #Busco el nombre del director de cuenta
              $qNomDir  = "SELECT ";
              $qNomDir .= "IF($cAlfa.SIAI0003.USRNOMXX != \"\",$cAlfa.SIAI0003.USRNOMXX,\"DIRECTOR SIN NOMBRE\") AS dirnomxx ";
              $qNomDir .= "FROM $cAlfa.SIAI0003 ";
              $qNomDir .= "WHERE ";
              $qNomDir .= "USRIDXXX = \"{$xDD['diridxxx']}\" LIMIT 0,1";
              $xNomDir = f_MySql("SELECT","",$qNomDir,$xConexion01,"");
              if (mysql_num_rows($xNomDir) > 0) {
                $xRND = mysql_fetch_array($xNomDir);
                $xDD['dirnomxx'] = $xRND['dirnomxx'];
              } else {
                $xDD['dirnomxx'] = "DIRECTOR SIN NOMBRE";
              }

              ## Consulto la fecha del ultimo comprobante del DO
              $nAno01I = ((substr($xDD['regfcrex'],0,4)-1) < $vSysStr['financiero_ano_instalacion_modulo']) ? $vSysStr['financiero_ano_instalacion_modulo'] : (substr($xDD['regfcrex'],0,4)-1);
              $nAno01F = ($gFecCorte == "") ? date('Y') : substr($gFecCorte, 0, 4);
              $nCanReg01 = 0;
              for($nAnoBus = $nAno01F; $nAnoBus >= $nAno01I; $nAnoBus--) {
                $nCanReg01++;
                if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = $objEstructuras->fnReiniciarConexionDBEstadoCuentaTramite($xConexion01); } 
                
                $qFcodxxx  = "SELECT ";
                $qFcodxxx .= "$cAlfa.fcod$nAnoBus.comidxxx, ";
                $qFcodxxx .= "$cAlfa.fcod$nAnoBus.comcodxx, ";
                $qFcodxxx .= "$cAlfa.fcod$nAnoBus.comcscxx, ";
                $qFcodxxx .= "$cAlfa.fcod$nAnoBus.comfecxx ";
                $qFcodxxx .= "FROM $cAlfa.fcod$nAnoBus ";
                $qFcodxxx .= "WHERE ";
                $qFcodxxx .= "$cAlfa.fcod$nAnoBus.sucidxxx = \"{$xDD['sucidxxx']}\" AND ";
                $qFcodxxx .= "$cAlfa.fcod$nAnoBus.docidxxx = \"{$xDD['docidxxx']}\" AND ";
                $qFcodxxx .= "$cAlfa.fcod$nAnoBus.docsufxx = \"{$xDD['docsufxx']}\" ";
                $qFcodxxx .= "ORDER BY $cAlfa.fcod$nAnoBus.comfecxx DESC, ";
                $qFcodxxx .= "$cAlfa.fcod$nAnoBus.reghcrex DESC LIMIT 0,1 ";
                $xFcodxxx  = mysql_query($qFcodxxx,$xConexion01);
                // echo $qFcodxxx."~".mysql_num_rows($xFcodxxx)."\n\n";
                $xDD['feccomdo'] = "";
                while ($xRFD = mysql_fetch_array($xFcodxxx)) {
                  $xDD['feccomdo'] = $xRFD['comfecxx'];
                  $nAnoBus = $vSysStr['financiero_ano_instalacion_modulo']-1;
                }
              }
              ## Fin Consulto la fecha del ultimo comprobante del DO

              ## Buscando Fecha Mayor De Levante y Fecha de Entrega a Facturacion ##
              switch($xDD['doctipxx']){
                case "IMPORTACION":
                case "TRANSITO":
                  $qDat200  = "SELECT ";
                  $qDat200 .= "$cAlfa.SIAI0200.DOIMYLEV, ";
                  $qDat200 .= "$cAlfa.SIAI0200.DOIFENCA ";
                  $qDat200 .= "FROM $cAlfa.SIAI0200 ";
                  $qDat200 .= "WHERE ";
                  $qDat200 .= "$cAlfa.SIAI0200.DOIIDXXX = \"{$xDD['docidxxx']}\" AND ";
                  $qDat200 .= "$cAlfa.SIAI0200.DOISFIDX = \"{$xDD['docsufxx']}\" AND ";
                  $qDat200 .= "$cAlfa.SIAI0200.ADMIDXXX = \"{$xDD['sucidxxx']}\" AND ";
                  $qDat200 .= "$cAlfa.SIAI0200.regestxx = \"ACTIVO\" LIMIT 0,1";
                  $xDat200 = f_MySql("SELECT","",$qDat200,$xConexion01,"");
                  if(mysql_num_rows($xDat200) > 0 ) {
                    $xDDO = mysql_fetch_array($xDat200);
                    $xDD['doimylev'] = $xDDO['DOIMYLEV'];
                    $xDD['doifenca'] = $xDDO['DOIFENCA'];
                  }
                break;
                case "EXPORTACION":
                  /*** Buscando Fecha Entrega de Carpeta - Control Fechas Exportaciones. ***/
                  $qDatExp  = "SELECT dexfenfa ";
                  $qDatExp .= "FROM $cAlfa.siae0199 ";
                  $qDatExp .= "WHERE ";
                  $qDatExp .= "dexidxxx = \"{$xDD['docidxxx']}\" AND ";
                  $qDatExp .= "admidxxx = \"{$xDD['sucidxxx']}\" LIMIT 0,1 ";
                  $xDatExp  = f_MySql("SELECT","",$qDatExp,$xConexion01,"");
                  // echo $qDatExp."~".mysql_num_rows($xDatExp)."\n";
                  if(mysql_num_rows($xDatExp) > 0 ) {
                    $vDatExp = mysql_fetch_array($xDatExp);
                    $xDD['doifenca'] = $vDatExp['dexfenfa'];
                  }
                break;
              }
              ## Fin Buscando Fecha Mayor De Levante y Fecha de Entrega a Facturacion##

              $nValor01 = ($xDD['docpedxx'] != "") ? $xDD['docpedxx'] : "";
              $nValor02 = ($cEstado != "") ? $cEstado : (($xDD['regestxx'] != "") ? $xDD['regestxx'] : "");
              $nValor03 = ($xDD['dirnomxx'] != "") ? $xDD['dirnomxx'] : "";
              $nValor04 = ($nAnticipo != "") ? number_format($nAnticipo,0,',','') : 0;
              $nValor05 = ($nPagosTer != "") ? number_format($nPagosTer,0,',','') : 0;
              $nValor06 = ($nSaldo  != "") ? number_format($nSaldo,0,',','')  : 0;
              $nValor07 = ($xDD['docffecx'] != '0000-00-00' && $xDD['docffecx'] != "") ? $xDD['docffecx']: "";
              $nValor08 = ($xDD['regfcrex'] != '0000-00-00' && $xDD['regfcrex'] != "") ? $xDD['regfcrex']: "";
              $nValor09 = ($xDD['doimylev'] != '0000-00-00' && $xDD['doimylev'] != "") ? $xDD['doimylev']: "";
              $nValor10 = ($xDD['doifenca'] != '0000-00-00' && $xDD['doifenca'] != "") ? $xDD['doifenca']: "";
              $nValor11 = ($nAntProve != "") ? number_format($nAntProve,0,',','') : 0;
              $nValor12 = ($xDD['feccomdo'] != '0000-00-00' && $xDD['feccomdo'] != "") ? $xDD['feccomdo'] : "";

              // Valores acumulados para las 4 columnas que se totalizan
              $nTotAnti += $nValor04;
              $nTotPag  += $nValor05;
              $nAntPro  += $nValor11;
              $nTotSal  += $nValor06;

              $zColorPro = "#000000";
              $data  = '<tr bgcolor = "white" height="20"   style="padding-left:4px;padding-right:4px">';
                $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$xDD['docidxxx']."-".$xDD['docsufxx'].'</td>';
                $data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$xDD['sucidxxx'].'</td>';
                $data .= '<td class="letra7" align="left"   style = "mso-number-format:\'\@\';color:'.$zColorPro.'">'.$nValor01.'</td>';
                $data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$nValor08.'</td>';
                $data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$nValor12.'</td>';
                $data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$xDD['doctipxx'].'</td>';
                $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$nValor09.'</td>';
                $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$nValor10.'</td>';
                $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$xDD['cliidxxx'].'</td>';
                $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$xDD['clinomxx'].'</td>';
                $data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$nValor02.'</td>';
                $data .= '<td class="letra7" align="center" style = "color:'.$zColorPro.'">'.$nValor07.'</td>';
                $data .= '<td class="letra7" align="left"   style = "color:'.$zColorPro.'">'.$nValor03.'</td>';
                $data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'">'.$nValor11.'</td>';
                $data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'">'.$nValor04.'</td>';
                $data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'">'.$nValor05.'</td>';
                $data .= '<td class="letra7" align="right"  style = "color:'.$zColorPro.'">'.$nValor06.'</td>';
              $data .= '</tr>';

              fwrite($fOp, $data);
            }
          } // FIN pintado de registros
        } ## while ($xDD = mysql_fetch_array($xDatDoi)) { ##
        
        echo "\nFin\n";
        
        $data  = '<tr bgcolor = "white" height="20" style="padding-left:5px;padding-top:5px">';
          $data .= '<td class="name" colspan="13" align="center">';
            $data .= '<center>';
              $data .= '<font size="3">';
                $data   .= '<b>TOTAL TRAMITES EN ESTA CONSULTA ['.$nCanTra.']</b>';
              $data .= '</font>';
            $data .= '</center>';
          $data .= '</td>';
          $data .= '<td class="name" colspan="1" align="right">';
            $data .= '<font size="3">';
              $data   .= '<b>'.$nAntPro.'</b>';
            $data .= '</font>';
          $data .= '</td>';
          $data .= '<td class="name" colspan="1" align="right">';
            $data .= '<font size="3">';
              $data   .= '<b>'.$nTotAnti.'</b>';
            $data .= '</font>';
          $data .= '</td>';
          $data .= '<td class="name" colspan="1" align="right">';
            $data .= '<font size="3">';
              $data   .= '<b>'.$nTotPag.'</b>';
            $data .= '</font>';
          $data .= '</td>';
          $data .= '<td class="name" colspan="1" align="right">';
            $data .= '<font size="3">';
              $data   .= '<b>'.$nTotSal.'</b>';
            $data .= '</font>';
          $data .= '</td>';
        $data .= '</tr>';
      $data .= '</table>';
      
      fwrite($fOp, $data);
      fclose($fOp);
      
      if (file_exists($cFile)) {
        if ($data == "") {
          $data = "(0) REGISTROS!";
        }

        chmod($cFile, intval($vSysStr['system_permisos_archivos'], 8));
        $cDownLoadFilename = $cDownLoadFilename !== null ? $cDownLoadFilename : basename($cFile);

        if ($_SERVER["SERVER_PORT"] != "") {
          // Obtener la ruta absoluta del archivo
          $cAbsolutePath = realpath($cFile);
          $cAbsolutePath = substr($cAbsolutePath,0,strrpos($cAbsolutePath, '/'));

          /* 
            Esta lógica valida si la ruta absoluta comienza con alguna de las rutas autorizadas en el array,
            permitiendo que cualquier subdirectorio dentro de una ruta base permitida (Ej: /var/www/html/desarrollo/opencomex/propios/GRUMALCO/estado_cuenta) para que
            sea considerado valido para descargar un archivo.
          */
          $nEncontro = 0;
          foreach ($vSystem_Path_Authorized as $cAuthorizedPath) {
            if (strpos(realpath($cAbsolutePath), $cAuthorizedPath) === 0) {
              $nEncontro = 1;
              break;
            }
          }
          
          if ($nEncontro == 1) {
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
   * Clase para crear Tabla Temporal
   */
  class cEstructurasEstadoCuentaTramiteRoldan {

    /**
     * Metodo que se encarga de Crear las Estructuras de las Tablas de acuerdo al Tipo de Interface
     * Parametros recibidos:
     * 
     * $pParametros['TIPOESTU'] -> Tipo de Estructura
     * @return array   $mReturn    Retorna estado de ejecución del método con nombre de tabla o de las tablas creadas unidas por tilde(~)
     */ 

    function fnCrearTablaEstadoCuentaTramiteRoldan($pParametros){
      global $cAlfa;
  
      $xConexion99 = mysql_connect(OC_SERVER,OC_USERROBOT,OC_PASSROBOT) or die("El Sistema no Logro Conexion con ".OC_SERVER);
      if($xConexion99){
        $nSwitch = 0;
      }else{
        $nSwitch = 1;
        $mReturn[count($mReturn)] = "El Sistema no Logro Conexion con ".OC_SERVER;
      }
  
      if($nSwitch == 0){
  
        switch($pParametros['TIPOESTU']) {
          case "MOVIMIENTO": //Movimiento Contable por tramite
            /**
             * Random para Nombre de la Tabla
             */
            $cTabCar  = mt_rand(1000000000, 9999999999);
            $cTabla = "memrepem".$cTabCar;
            /**
             * Creando Tabla Memory
             */
            $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
            $qNewTab .= "lineaidx INT(11)       NOT NULL AUTO_INCREMENT,"; //LINEA
            $qNewTab .= "tiposalx VARCHAR(20)   NOT NULL COMMENT \"Tipo Saldo\",";
            $qNewTab .= "sucidxxx VARCHAR(3)    NOT NULL COMMENT \"Id de la Sucursal Operativa\",";
            $qNewTab .= "docidxxx VARCHAR(20)   NOT NULL COMMENT \"Id del DO\",";
            $qNewTab .= "docsufxx VARCHAR(3)    NOT NULL COMMENT \"Sufijo del DO\",";
            $qNewTab .= "comcsccx VARCHAR(20)   NOT NULL COMMENT \"Consecutivo del Comprobante Cruce\",";
            $qNewTab .= "comseqcx VARCHAR(5)    NOT NULL COMMENT \"Secuencia del Comprobante Cruce\",";
            $qNewTab .= "comvlrxx DECIMAL(15,2) NOT NULL COMMENT \"Valor\",";
            $qNewTab .= "regstamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT \"Modificado\",";
            $qNewTab .= "PRIMARY KEY (lineaidx), ";
            $qNewTab .= "INDEX sucidxxx (sucidxxx, docidxxx, docsufxx)) ENGINE=MyISAM ";
            $xNewTab  = mysql_query($qNewTab,$xConexion99);
            //f_Mensaje(__FILE__,__LINE__,$qNewTab);
      
            if(!$xNewTab) {
              $nSwitch = 1;
              $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal.".mysql_error($xConexion99);
            }
          break;
          case "SALDOS": //Saldos por tramite
            /**
             * Random para Nombre de la Tabla
             */
            $cTabCar  = mt_rand(1000000000, 9999999999);
            $cTabla = "memrepes".$cTabCar;
            /**
             * Creando Tabla Memory
             */
            $qNewTab  = "CREATE TABLE IF NOT EXISTS $cAlfa.$cTabla (";
            $qNewTab .= "lineaidx INT(11)       NOT NULL AUTO_INCREMENT,"; //LINEA
            $qNewTab .= "sucidxxx VARCHAR(3)    NOT NULL COMMENT \"Id de la Sucursal Operativa\",";
            $qNewTab .= "docidxxx VARCHAR(20)   NOT NULL COMMENT \"Id del DO\",";
            $qNewTab .= "docsufxx VARCHAR(3)    NOT NULL COMMENT \"Sufijo del DO\",";
            $qNewTab .= "comidxxx VARCHAR(1)    NOT NULL COMMENT \"Tipo de Comprobante Contable\",";
            $qNewTab .= "comcodxx VARCHAR(3)    NOT NULL COMMENT \"Codigo del Comprobante Contable\",";
            $qNewTab .= "ccoidxxx VARCHAR(10)   NOT NULL COMMENT \"Id del Centro de Costos\",";
            $qNewTab .= "pucidxxx VARCHAR(10)   NOT NULL COMMENT \"Id de la Cuenta Contable\",";
            $qNewTab .= "succomxx VARCHAR(10)   NOT NULL COMMENT \"Sucursal Comercial\",";
            $qNewTab .= "doctipxx VARCHAR(12)   NOT NULL COMMENT \"Tipo de Operacion del DO\",";
            $qNewTab .= "docpedxx VARCHAR(250)  NOT NULL DEFAULT \"\" COMMENT \"Numero del Pedido del Cliente\",";
            $qNewTab .= "cliidxxx VARCHAR(12)   NOT NULL COMMENT \"Id del Cliente\",";
            $qNewTab .= "diridxxx VARCHAR(12)   NOT NULL COMMENT \"Id Director de Cuenta\",";
            $qNewTab .= "docfacxx VARCHAR(30)   NOT NULL COMMENT \"Numero de Factura\",";
            $qNewTab .= "docffecx DATE          NOT NULL COMMENT \"Fecha Factura\",";
            $qNewTab .= "docusrce VARCHAR(15)   NOT NULL COMMENT \"Ultimo Usuario que Modifico Condiciones Especiales\",";
            $qNewTab .= "docfecce DATETIME      NOT NULL COMMENT \"Ultima Fecha de Modificacion Condiciones Especiales\",";
            $qNewTab .= "regfcrex DATE          NOT NULL COMMENT \"Fecha de Creacion del Registro\",";
            $qNewTab .= "regestxx VARCHAR(10)   NOT NULL COMMENT \"Estado del Registro\",";
            $qNewTab .= "comvlran DECIMAL(15,2) NOT NULL COMMENT \"Valor Anticipos\",";
            $qNewTab .= "comvlrap DECIMAL(15,2) NOT NULL COMMENT \"Valor Anticipos Proveedores\",";
            $qNewTab .= "comvlrpc DECIMAL(15,2) NOT NULL COMMENT \"Valor Pagos a Terceros\",";
            $qNewTab .= "marcaxxx VARCHAR(2)    NOT NULL COMMENT \"Usado\",";
            $qNewTab .= "regstamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT \"Modificado\",";
            $qNewTab .= "PRIMARY KEY (lineaidx), ";
            $qNewTab .= "INDEX sucidxxx (sucidxxx, docidxxx, docsufxx)) ENGINE=MyISAM ";
            $xNewTab  = mysql_query($qNewTab,$xConexion99);
            //f_Mensaje(__FILE__,__LINE__,$qNewTab);
      
            if(!$xNewTab) {
              $nSwitch = 1;
              $mReturn[count($mReturn)] = "Error al Crear Tabla Temporal.".mysql_error($xConexion99);
            }
          break;
          default:
            $nSwitch = 1;
            $mReturn[count($mReturn)] = "No se Recibio Tipo de Estructura a Crear";
          break;
          }
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
     * Metodo que reinicia la conexion
     */
    function fnReiniciarConexionDBEstadoCuentaTramite($pConexion){
      global $cHost;  global $cUserHost;  global $cPassHost;

      mysql_close($pConexion);
      $xConexion01 = mysql_connect($cHost,$cUserHost,$cPassHost,TRUE);

      return $xConexion01;
    }##function fnReiniciarConexionDBEstadoCuentaTramite(){##
		

		## Metodo para capturar la informacion del motor de DB asosciada al query
  	function fnMysqlQueryInfo($xConexion,$xQueryTime) {

			global $cSystemPath; global $cAlfa; global $_SERVER; global $kDf;

			$xMysqlInfo = mysql_info($xConexion);

			ereg("Changed: ([0-9]*)",$xMysqlInfo,$vChanged);
			ereg("Deleted: ([0-9]*)",$xMysqlInfo,$vDeleted);
			ereg("Duplicates: ([0-9]*)",$xMysqlInfo,$vDuplicates);
			ereg("Records: ([0-9]*)",$xMysqlInfo,$vRecords);
			ereg("Rows matched: ([0-9]*)",$xMysqlInfo,$vRows_matched);
			ereg("Skipped: ([0-9]*)",$xMysqlInfo,$vSkipped);
			ereg("Warnings: ([0-9]*)",$xMysqlInfo,$vWarnings);

			$cQueryInfo  = "|";
			$cQueryInfo .= "Changed~{$vChanged[1]}|";
			$cQueryInfo .= "Deleted~{$vDeleted[1]}|";
			$cQueryInfo .= "Duplicates~{$vDuplicates[1]}|";
			$cQueryInfo .= "Records~{$vRecords[1]}|";
			$cQueryInfo .= "Rows matched~{$vRows_matched[1]}|";
			$cQueryInfo .= "Skipped~{$vSkipped[1]}|";
			$cQueryInfo .= "Warnings~{$vWarnings[1]}|";
			$cQueryInfo .= "Affected Rows~".mysql_affected_rows($xConexion)."|";
			$cQueryInfo .= "Query Time~".number_format($xQueryTime,2)."|";
			$cQueryInfo .= "Error Number~".mysql_errno($xConexion)."|";
			$cQueryInfo .= "Error Description~".mysql_error($xConexion)."|";

			$cIP = "";
			$cHost = "";
			if ($_SERVER['HTTP_CLIENT_IP'] != "") {
				$cIP  = $_SERVER['HTTP_CLIENT_IP'];
				$cHost = $_SERVER['HTTP_VIA'];
			}elseif ($_SERVER['HTTP_X_FORWARDED_FOR'] != "") {
				$cIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
				$cHost = $_SERVER['HTTP_VIA'];
			}else{
				$cIP = $_SERVER['REMOTE_ADDR'];
				$cHost = $_SERVER['HTTP_VIA'];
			}

			if ($cHost == "") {
				$cHost = $cIP;
			}

			$copenComex  = "|";
			$copenComex .= "{$kDf[4]}~";
			$copenComex .= "{$_SERVER['PHP_SELF']}~";
			$copenComex .= "$cIP~";
			$copenComex .= "$cHost~";
			$copenComex .= "{$kDf[3]}~";
			$copenComex .= date("Y-m-d")."~";
			$copenComex .= date("H:i:s");
			$copenComex .= "|";
			$xopenComex = mysql_query("SET @opencomex = \"$copenComex\"",$xConexion);
			$xQueryInfo = mysql_query("SET @mysqlinfo = \"$cQueryInfo\"",$xConexion);
		} ## function f_Mysql_Query_Info($xConexion,$xQueryTime) {
  	## Metodo para capturar la informacion del motor de DB asosciada al query
  }
  ?>

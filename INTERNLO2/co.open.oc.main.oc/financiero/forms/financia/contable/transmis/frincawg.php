<?php
	// ini_set('error_reporting', E_ERROR);
	// ini_set("display_errors", "1");

	set_time_limit(0);
	ini_set("memory_limit", "512M");

	/**
	 * Variables de control de errores
	 * @var number
	 */
	$nSwitch = 0;

	/**
	 * Variable para almacenar los mensajes de error
	 * @var string
	 */
	$cMsj = "";

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
			$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "El parametro Id del Proceso no puede ser vacio.\n";
		}

		if ($vArg[1] == "") {
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "El parametro de la Cookie no puede ser vacio.\n";
		}

		if ($nSwitch == 0) {
			$_COOKIE["kDatosFijos"] = $vArg[1];

			include("{$OPENINIT['pathdr']}/opencomex/config/config.php");
			include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utility.php");
			include("{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php");
			include("{$OPENINIT['pathdr']}/opencomex/financiero/libs/php/utidsvcw.php");

			/**
			 * Buscando el ID del proceso
			 */
			$qProBg = "SELECT * ";
			$qProBg .= "FROM $cBeta.sysprobg ";
			$qProBg .= "WHERE ";
			$qProBg .= "pbaidxxx= \"{$vArg[0]}\" AND ";
			$qProBg .= "regestxx = \"ACTIVO\" LIMIT 0,1 ";
			$xProBg = f_MySql("SELECT","",$qProBg,$xConexion01, "");
			if (mysql_num_rows($xProBg) == 0) {
				$xRPB = mysql_fetch_array($xProBg);
				$nSwitch = 1;
				$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "El Proceso en Background [{$vArg[0]}] No Existe o ya fue Procesado.\n".$qProBg;
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
		include("../../../../../config/config.php");
		include("../../../../libs/php/utility.php");
		include("../../../../../libs/php/utiprobg.php");
		include("../../../../libs/php/utidsvcw.php");
	}

	/**
	 *  Cookie fija
	 */
	$kDf = explode("~", $_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb   = $kDf[3];
	$kUser      = $kDf[4];
	$kLicencia  = $kDf[5];
	$swidth     = $kDf[6];

	$cSystemPath = OC_DOCUMENTROOT;

	if ($_SERVER["SERVER_PORT"] != "") {
		/*** Ejecutar proceso en Background ***/
		$cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
	} // fin if ($_SERVER["SERVER_PORT"] != "")

	if ($_SERVER["SERVER_PORT"] == "") {
    $dDesde    = $_POST['dDesde'];
		$dHasta    = $_POST['dHasta'];
    $gInterfaz = $_POST['gInterfaz'];
		$gComId    = $_POST['gComId'];
    $gComCod   = $_POST['gComCod'];
    $gUsrId    = $_POST['gUsrId'];
    $cEjProBg  = $_POST['cEjProBg'];
	}  // fin del if ($_SERVER["SERVER_PORT"] == "")

	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;	
		$strPost  = "dDesde~".   $dDesde."|";
		$strPost .= "dHasta~".   $dHasta."|";
    $strPost .= "gInterfaz~".$gInterfaz."|";
    $strPost .= "gComId~".   $gComId."|";
		$strPost .= "gComCod~".  $gComCod."|";
		$strPost .= "gUsrId~".   $gUsrId."|";
		$strPost .= "cEjProBg~". $cEjProBg;

		$vParBg['pbadbxxx'] = $cAlfa;                        //Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                 //Modulo
		$vParBg['pbatinxx'] = "TRANSMICARGOWISE";            //Tipo Interface
		$vParBg['pbatinde'] = "TRANSMISION CARGO WISE";      //Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                            //Sucursal
		$vParBg['doiidxxx'] = "";                            //Do
		$vParBg['doisfidx'] = "";                            //Sufijo
		$vParBg['cliidxxx'] = "";                            //Nit
		$vParBg['clinomxx'] = "";                            //Nombre Importador
		$vParBg['pbapostx'] = $strPost;										   //Parametros para reconstruir Post
		$vParBg['pbatabxx'] = "";                            //Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];   //Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];       //cookie
		$vParBg['pbacrexx'] = 0;                             //Cantidad Registros
		$vParBg['pbatxixx'] = 1;                             //Tiempo Ejecucion x Item en Segundos
		$vParBg['pbaopcxx'] = "";                            //Opciones
		$vParBg['regusrxx'] = $kUser;                        //Usuario que Creo Registro

		#Incluyendo la clase de procesos en background
		$ObjProBg = new cProcesosBackground();
		$mReturnProBg = $ObjProBg->fnCrearProcesoBackground($vParBg);

		#Imprimiendo resumen de todo ok.
		if ($mReturnProBg[0] == "true") {
			f_Mensaje(__FILE__, __LINE__, "Proceso en Background Agendado con Exito.");
			?>
			<script language = "javascript">
				parent.fmwork.fnRecargar();
			</script>
			<?php

		} else {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}
		}
	} // fin del if ($_SERVER["SERVER_PORT"] != "" && $_POST['cEjProBg'] == "SI" && $nSwitch == 0)

	/**
	 * Ejecucion del proceso
	 */
	if ($cEjePro == 0) {
		if ($nSwitch == 0) { // cuando lo hace el navegador y por consola
			/**
			 * Instanciando Objeto para la creacion de las tabla de errores
			 */
			$objTablasTemporales = new cEstructurasCargoWiseDsv();

			//Creando Tabla Temporal de Errores
			$vParametros = array();
			$vParametros['TIPOESTU'] = "ERRORES";
			$mReturnTablaE = $objTablasTemporales->fnCrearEstructurasCargoWiseDsv($vParametros,$cAlfa);

			if($mReturnTablaE[0] == "false") {
				$nSwitch = 1;
				for($nR=3;$nR<count($mReturnTablaE);$nR++){
					$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
					$cMsj .= $mReturnTablaE[$nR]."\n";
				}
			}
      
      //Creando Tablas temporal movimiento
      $vParametros = array();
      $vParametros['TIPOESTU'] = "MOVCONTABLE";
      $mReturnTablaM = $objTablasTemporales->fnCrearEstructurasCargoWiseDsv($vParametros,$cAlfa);

      if($mReturnTablaM[0] == "false"){
        $nSwitch = 1;
        for($nR=3;$nR<count($mReturnTablaM);$nR++){
          $cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
          $cMsj .= $mReturnTablaM[$nR]."\n";
        }
      }

			if ($nSwitch == 0) {
				/**
				 * Instanciando Objeto para la Integracion Cargo Wise DSV
				 */
				$ObjIntegracionCargoWiseDsv = new cIntegracionCargoWiseDsv();

        $mArchivos = array();
        $vDatos = array();
        $vDatos['basedato'] = $cAlfa;
        $vDatos['tablaerr'] = $mReturnTablaE[1];
        $vDatos['tablamov'] = $mReturnTablaM[1];
        $vDatos['comidxxx'] = $gComId;  //Comprobante
        $vDatos['comcodxx'] = $gComCod; //Codigo Comprobante
        $vDatos['comcscxx'] = "";       //Consecutivo Uno
        $vDatos['comcsc2x'] = "";       //Consecutivo Dos
        $vDatos['comfecde'] = $dDesde;  //Fecha Comprobante desde
        $vDatos['comfecha'] = $dHasta;  //Fecha Comprobante hasta
        $vDatos['usuariox'] = $gUsrId;  //Id usuario
        $vDatos['origenxx'] = "";

        if ($gInterfaz == "CAUSACIONES_TERCEROS") {
          $mRetornaDsv = $ObjIntegracionCargoWiseDsv->fnCausaciones($vDatos);
        } elseif ($gInterfaz == "FACTURAS" || $gInterfaz == "NOTAS_CREDITO") {
          $vDatos['interfaz'] = $gInterfaz; // Tipo de comprobantes
          $mRetornaDsv = $ObjIntegracionCargoWiseDsv->fnFacturas($vDatos);
        }

        if ($mRetornaDsv[0] == "false") {
          $nSwitch = 1;
        }
			}

			if ($nSwitch == 0) {
				/**
				 * Creando el archivo XML
				 */
        $vDatos = array();
        $vDatos['basedato'] = $cAlfa;
        $vDatos['tablaerr'] = $mReturnTablaE[1];
        $vDatos['tablamov'] = $mReturnTablaM[1];
        $vDatos['ejeprobg'] = $cEjProBg;
        $vDatos['origenxx'] = "";
        $mRetornaDsv = $ObjIntegracionCargoWiseDsv->fnXmlComprobantes($vDatos);
        if ($mRetornaDsv[0] == "false"){
          $nSwitch = 1;
        } else {
          // Se crea el archivo zip con los XML
          $cRutaZip = f_Buscar_Niveles_Hasta_Opencomex(getcwd()).$vSysStr['system_download_directory'];

          $nFiles = 0;
          $vFiles = array();
          
          $nIndexFolder = 1;
          $cFileNameZip = "Documentos_".$nIndexFolder."_".$kUser."_".date("YmdHis").".zip"; // Zip name
          if (file_exists($cRutaZip."/".$cFileNameZip) == true){
            unlink($cRutaZip."/".$cFileNameZip);
          }
          $vFiles[count($vFiles)] = $cFileNameZip;
          
          $oZip = new ZipArchive();
          $oZip->open($cRutaZip."/".$cFileNameZip,  ZipArchive::CREATE);

          for ($nA=0; $nA < count($mRetornaDsv[2]);$nA++) {
            $nInd_mArchivos = count($mArchivos);
            $mArchivos[$nInd_mArchivos]['interfaz'] = $gInterfaz;
            $mArchivos[$nInd_mArchivos]['rutaxxxx'] = $mRetornaDsv[1].'/'.$mRetornaDsv[2][$nA];
            $mArchivos[$nInd_mArchivos]['nomarcxx'] = $mRetornaDsv[2][$nA];
            if (isset($mRetornaDsv[3][$nA])) {
              $nInd_mArchivos = count($mArchivos);
              $mArchivos[$nInd_mArchivos]['interfaz'] = $gInterfaz;
              $mArchivos[$nInd_mArchivos]['rutaxxxx'] = $mRetornaDsv[1].'/'.$mRetornaDsv[3][$nA];
              $mArchivos[$nInd_mArchivos]['nomarcxx'] = $mRetornaDsv[3][$nA];
            }

            $cFileCargoWise = $cRutaZip."/".$mRetornaDsv[2][$nA];
            if (is_file($cFileCargoWise)){               
              $oZip->addFromString(basename($cFileCargoWise), file_get_contents($cFileCargoWise));  
            }

            if (isset($mRetornaDsv[3][$nA])) {
              $cFileCargoWise = $cRutaZip."/".$mRetornaDsv[3][$nA];
              if (is_file($cFileCargoWise)){               
                $oZip->addFromString(basename($cFileCargoWise), file_get_contents($cFileCargoWise));  
              }
            }
          }

		      $oZip->close();

        }
			}

			if ($nSwitch == 0) {
        for ($nA=0; $nA<count($mArchivos); $nA++) {
          if (!file_exists($mArchivos[$nA]['rutaxxxx'])) {
            $cMsj .= "No se encontro el archivo {$mArchivos[$nA]['nomarcxx']}, Favor Comunicar este Error a openTecnologia S.A.";
          }
        }
        if ($cMsj != "") {
          if ($_SERVER["SERVER_PORT"] != "") {
            f_Mensaje(__FILE__, __LINE__,"\n".$cMsj);
          } else {
            $nSwitch = 1;
          }
        }
			}

			if ($nSwitch != 0 && $mReturnTablaE[1] != '') {
				$qMsjErr  = "SELECT * ";
				$qMsjErr .= "FROM $cAlfa.{$mReturnTablaE[1]} ";
				$xMsjErr = f_MySql("SELECT","",$qMsjErr,$xConexion01, "");
				while ($xRME = mysql_fetch_array($xMsjErr)) {
					$cMsj .= $xRME['DESERROR']." | ";
				}
				$cMsj = substr($cMsj, 0, -3);
			}

			if ($nSwitch == 0) {
        if ($_SERVER["SERVER_PORT"] != "") {
          $cBrowser = "<html>";
            $cBrowser .= "<head>";
              $cBrowser .= "<title>Integracion Cargo Wise</title>";
              $cBrowser .= "<LINK rel = 'stylesheet' href = '".$cSystem_Libs_JS_Directory."/estilo.css'>";
              $cBrowser .= "<LINK rel = 'stylesheet' href = '".$cSystem_Libs_JS_Directory."/general.css'>";
              $cBrowser .= "<LINK rel = 'stylesheet' href = '".$cSystem_Libs_JS_Directory."/layout.css'>";
              $cBrowser .= "<LINK rel = 'stylesheet' href = '".$cSystem_Libs_JS_Directory."/custom.css'>";
            $cBrowser .= "</head>";
            $cBrowser .= "<body>";
              $cBrowser .= "<script language = 'javascript'>";
              $cBrowser .= "</script>";
              if (count($mArchivos) > 0) {
                $cBrowser .= "<br><br><center>";
                  $cBrowser .= "<table border =\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"450\">";
                    $cBrowser .= "<tr bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" style=\"height: 20px\">";
                      $cBrowser .= "<td style=\"padding: 4px\" class = \"name\">Intefaz</td>";
                      $cBrowser .= "<td style=\"padding: 4px\" class = \"name\">Archivo</td>";
                    $cBrowser .= "</tr>";
                    for ($nA=0; $nA<count($mArchivos); $nA++) {
                      $cBrowser .= "<tr>";
                        $cBrowser .= "<td style=\"padding: 4px\" class=\"letra7\">".$mArchivos[$nA]['interfaz']."</td>";
                        $cBrowser .= "<td style=\"padding: 4px\" class=\"letra7\"><a href ='".$mArchivos[$nA]['rutaxxxx']."'>".$mArchivos[$nA]['nomarcxx']."</a></td>";
                      $cBrowser .= "</tr>";
                    }
                  $cBrowser .= "</table>";
                $cBrowser .= "</center>";
                $cBrowser .= "<center>";
                  $cBrowser .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"450\">";
                    $cBrowser .= "<tr height=\"21\">";
                      $cBrowser .= "<td width=\"268\" height=\"21\"></td>";
                      $cBrowser .= "<td width=\"91\" height=\"21\" Class=\"name\" background='".$cPlesk_Skin_Directory."/btn_ok_bg.gif' style=\"cursor:hand\"><a style=\"text-decoration:none;color:#555555;\" href ='".$cRutaZip."/".$cFileNameZip."' download='".$cFileNameZip."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Descargar</td>";
                      $cBrowser .= "<td width=\"91\" height=\"21\" Class=\"name\" background='".$cPlesk_Skin_Directory."/btn_cancel_bg.gif' style=\"cursor:hand\" onclick='window.close()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir</td>";
                    $cBrowser .= "</tr>";
                  $cBrowser .= "</table>";
                $cBrowser .= "</center>";
              } else {
                echo "<br><br><center>No se Generaron Registros.</center>";
              }
            $cBrowser .= "</body>";
          $cBrowser .= "</html>";
          echo $cBrowser;	
        } else {
          $cNomArc = "";
          for ($nA=0; $nA<count($mArchivos); $nA++) {
            $cNomArc .= $mArchivos[$nA]['nomarcxx']."~";
          }
        }
			} else {
				echo str_replace(" | ", "<br>", $cMsj);
			}
			/*** Fin Logica del proceso  ***/
		}
	}

	if ($_SERVER["SERVER_PORT"] == "") {
		/**
		 * Se ejecuto por el proceso en background
		 * Actualizo el campo de resultado y nombre del archivo
		 */
		$vParBg['pbarespr'] = ($nSwitch == 0) ? "EXITOSO" : "FALLIDO";  //Resultado Proceso
		$vParBg['pbaexcxx'] = $cNomArc;                                 //Nombre Archivos Excel
		$vParBg['pbaerrxx'] = str_replace(" | ", "\n", $cMsj);          //Errores al ejecutar el Proceso
		$vParBg['regdfinx'] = date('Y-m-d H:i:s');                      //Fecha y Hora Fin Ejecucion Proceso
		$vParBg['pbaidxxx'] = $vArg[0];                                 //id Proceso

		#Incluyendo la clase de procesos en background
		$ObjProBg = new cProcesosBackground();
		$mReturnProBg = $ObjProBg->fnFinalizarProcesoBackground($vParBg);

		#Imprimiendo resumen de todo ok.
		if ($mReturnProBg[0] == "false") {
			$nSwitch = 1;
			for ($nR = 1; $nR < count($mReturnProBg); $nR++) {
				$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}
		}
	} // fin del if ($_SERVER["SERVER_PORT"] == "")
?>

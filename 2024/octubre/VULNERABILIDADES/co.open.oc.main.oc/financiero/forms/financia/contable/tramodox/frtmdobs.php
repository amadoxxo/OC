<?php
  namespace openComex;
	##Estableciendo que el tiempo de ejecucion no se limite

	// ini_set('error_reporting', E_ERROR);
	// ini_set("display_errors", "1");
	// date_default_timezone_set('America/Bogota');

	set_time_limit(0);
	ini_set("memory_limit", "512M");

	/**
	 * Cantidad de Registros para reiniciar conexion
	 */
	define("_NUMREG_",50);
	
	/**
	 * Variable para limitar la cantidad de registros en la busqueda.
	 */
	$nNumReg01 = 50;

	/**
	 * Variables para reemplazar caracteres especiales
	 * @var array
	 */
	$cBuscar = array('"',"'",chr(13),chr(10),chr(27),chr(9));
	$cReempl = array('\"',"\'"," "," "," "," ");
	
	/**
	 * Variable para saber si hay o no errores de validacion.
	 * @var number
	 */
	$nSwitch = 0;

	/**
	 * Variable para concatenar los errores de validacion
	 * @var string
	 */
	$cMsj = "";
	
	/**
	 * Variable que indica si se debe seguir ejecutando el proceso de la interface despues de cargar los datos en las tablas temporales
	 * @var Number
	 */
	$cEjePro = 0;

	/**
	 * Nombre(s) de los archivos en excel generados
	 */
	$cNomArc = "";

	if ($_SERVER["SERVER_PORT"] == "") {
		echo "{$OPENINIT['pathdr']}/opencomex/libs/php/utiprobg.php";
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

			/**
			 * Buscando el ID del proceso
			 */
			$qProBg  = "SELECT * ";
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
		# Librerias
		include("../../../../libs/php/utility.php");
		include("../../../../../config/config.php");
		include("../../../../../libs/php/utiprobg.php"); 
	}

	/**
	 *  Cookie fija
	 */
	$kDf = explode("~", $_COOKIE["kDatosFijos"]);
	$kMysqlHost = $kDf[0];
	$kMysqlUser = $kDf[1];
	$kMysqlPass = $kDf[2];
	$kMysqlDb = $kDf[3];
	$kUser = $kDf[4];
	$kLicencia = $kDf[5];
	$swidth = $kDf[6];

	$cSystemPath = OC_DOCUMENTROOT;

	if ($_SERVER["SERVER_PORT"] != "") {
		/**
		 * Validando Licencia
		 */
		$nLic = f_Licencia();
		if ($nLic == 0){
			$nSwitch = 1;
			$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
			$cMsj .= "Error grave de Seguridad otro usuario ingreso con su clave.\n";
		}
		#Ejecutar proceso en Background
		$gEjProBg = ($gEjProBg != "SI") ? "NO" : $gEjProBg;
	}
	
	if ($nSwitch == 0) {
		if ($_SERVER["SERVER_PORT"] != "") {

      //Validando fechas
      if ($gDesde == "") {
        $nSwitch = "1";
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "La Fecha Desde no Puede ser Vacia.\n";
      }
      
      if ($gHasta == "") {
        $nSwitch = "1";
        $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
				$cMsj .= "La Fecha Hasta no Puede ser Vacia.\n";
			}

      //Validando DO origen
      if(!empty($gDocIdOri)){
      	$qDocDes  = "SELECT ";
      	$qDocDes .= "sucidxxx, ";
				$qDocDes .= "docidxxx, ";
        $qDocDes .= "docsufxx, ";
        $qDocDes .= "ccoidxxx, ";
				$qDocDes .= "cliidxxx, ";
      	$qDocDes .= "regestxx, ";
				$qDocDes .= "regfcrex	";
	  		$qDocDes .= "FROM $cAlfa.sys00121 ";
      	$qDocDes .= "WHERE ";
      	$qDocDes .= "sucidxxx = \"$gSucIdOri\" AND ";
      	$qDocDes .= "docidxxx = \"$gDocIdOri\" AND ";
      	$qDocDes .= "docsufxx = \"$gDocSufOri\" LIMIT 0,1";
				$xDocDes  = f_MySql("SELECT","",$qDocDes,$xConexion01,"");
				if(mysql_num_rows($xDocDes) == 0){
          $nSwitch = 1;
	    	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	    	  $cMsj .= "El DO Origen [".$gSucIdOri. "-".$gDocIdOri."-".$gDocSufOri."] No Existe.\n";
        }
      }

      //Validando DO destino
      if(!empty($gDocIdDes)){
      	$qDocDes  = "SELECT ";
      	$qDocDes .= "sucidxxx, ";
				$qDocDes .= "docidxxx, ";
        $qDocDes .= "docsufxx, ";
        $qDocDes .= "ccoidxxx, ";
				$qDocDes .= "cliidxxx, ";
      	$qDocDes .= "regestxx, ";
				$qDocDes .= "regfcrex	";
	  		$qDocDes .= "FROM $cAlfa.sys00121 ";
      	$qDocDes .= "WHERE ";
      	$qDocDes .= "sucidxxx = \"$gSucIdDes\" AND ";
      	$qDocDes .= "docidxxx = \"$gDocIdDes\" AND ";
      	$qDocDes .= "docsufxx = \"$gDocSufDes\" LIMIT 0,1";
				$xDocDes  = f_MySql("SELECT","",$qDocDes,$xConexion01,"");
				if(mysql_num_rows($xDocDes) == 0){
          $nSwitch = 1;
	    	  $cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
	    	  $cMsj .= "El DO Destino [".$gSucIdDes. "-".$gDocIdDes."-".$gDocSufDes."] No Existe.\n";
        }
      }

      //Validando el cliente
			if($gCliId != ""){
				$qDatExt  = "SELECT CLIIDXXX ";
				$qDatExt .= "FROM $cAlfa.SIAI0150 ";
				$qDatExt .= "WHERE ";
				$qDatExt .= "CLIIDXXX = \"$gCliId\" AND ";
				$qDatExt .= "CLICLIXX = \"SI\" ";
				$xDatExt  = f_MySql("SELECT","",$qDatExt,$xConexion01,"");
				// f_Mensaje(__FILE__, __LINE__,$qDatExt."~".mysql_num_rows($xDatExt));
				if(mysql_num_rows($xDatExt) == 0){
					$nSwitch = 1;
					$cMsj .= "Linea ".str_pad(__LINE__,4,"0",STR_PAD_LEFT).": ";
					$cMsj .= "El Cliente[$gCliId] No Existe.\n";
				}
			}
		}
	}
	
	if ($_SERVER["SERVER_PORT"] == "") {
    //Cargando datos cuando el proceso es en background
    $gTipo      = $_POST['gTipo'];
    $gDesde     = $_POST['gDesde'];
    $gHasta     = $_POST['gHasta'];
    $gSucIdOri  = $_POST['gSucIdOri'];
    $gDocIdOri  = $_POST['gDocIdOri'];
    $gDocSufOri = $_POST['gDocSufOri'];
    $gSucIdDes  = $_POST['gSucIdDes'];
    $gDocIdDes  = $_POST['gDocIdDes'];
    $gDocSufDes = $_POST['gDocSufDes'];
		$gCliId     = $_POST['gCliId'];
  }
	
	if ($_SERVER["SERVER_PORT"] != "" && $gEjProBg == "SI" && $nSwitch == 0) {
		$cEjePro = 1;
		
		/**
		 * Trayendo cantidad de registros de la interface
		 */
		$qLoad  = "SELECT ";
		$qLoad .= "SQL_CALC_FOUND_ROWS cliidxxx ";
		$qLoad .= "FROM $cAlfa.fpar0158 ";
		$qLoad .= "WHERE ";
		if($gSucIdOri != "" && $gDocIdOri != "" && $gDocSufOri != ""){
			$qLoad .= "sucidorx = \"$gSucIdOri\"  AND ";
      $qLoad .= "docidorx = \"$gDocIdOri\"  AND ";
      $qLoad .= "docsufor = \"$gDocSufOri\" AND ";
    }
    if($gSucIdDes != "" && $gDocIdDes != "" && $gDocSufDes != ""){
			$qLoad .= "suciddex = \"$gSucIdDes\"  AND ";
      $qLoad .= "dociddex = \"$gDocIdDes\"  AND ";
      $qLoad .= "docsufde = \"$gDocSufDes\" AND ";
    }
    if($gCliId != ""){
			$qLoad .= "cliidorx = \"$gCliId\"  AND ";
		}
    $qLoad .= "regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" ";
		$qLoad .= "LIMIT 0,1";
		$cIdCountRow = mt_rand(1000000000, 9999999999);
		$xLoad = mysql_query($qLoad, $xConexion01, true, $cIdCountRow);
		// f_Mensaje(__FILE__, __LINE__,$qLoad."~".mysql_num_rows($xLoad));

		mysql_free_result($xLoad);

		$xNumRows   = mysql_query("SELECT @foundRows".$cIdCountRow." AS CANTIDAD", $xConexion01, false);
		$xRNR       = mysql_fetch_array($xNumRows);
		$nRegistros = $xRNR['CANTIDAD'];
		mysql_free_result($xNumRows);

    $cPost  = "gTipo~"     .$gTipo     ."|";
    $cPost .= "gDesde~"    .$gDesde    ."|";
    $cPost .= "gHasta~"    .$gHasta    ."|";
    $cPost .= "gSucIdOri~" .$gSucIdOri ."|";
    $cPost .= "gDocIdOri~" .$gDocIdOri ."|";
    $cPost .= "gDocSufOri~".$gDocSufOri."|";
    $cPost .= "gSucIdDes~" .$gSucIdDes ."|";
    $cPost .= "gDocIdDes~" .$gDocIdDes ."|";
    $cPost .= "gDocSufDes~".$gDocSufDes."|";
		$cPost .= "gCliId~"    .$gCliId;

		$cTablas = "";
	
		$vParBg['pbadbxxx'] = $cAlfa;                                         	//Base de Datos
		$vParBg['pbamodxx'] = "FACTURACION";                                  	//Modulo
		$vParBg['pbatinxx'] = "TRASLADODO";                                     //Tipo Interface
		$vParBg['pbatinde'] = "REPORTE TRASLADO DO A DO";                       //Descripcion Tipo de Interfaz
		$vParBg['admidxxx'] = "";                                             	//Sucursal
		$vParBg['doiidxxx'] = "";                                             	//Do
		$vParBg['doisfidx'] = "";                                             	//Sufijo
		$vParBg['cliidxxx'] = "";                                             	//Nit
		$vParBg['clinomxx'] = "";                                             	//Nombre Importador
		$vParBg['pbapostx'] = $cPost;																					  //Parametros para reconstruir Post
		$vParBg['pbatabxx'] = $cTablas;                                         //Tablas Temporales
		$vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];                    	//Script
		$vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];                        	//cookie
		$vParBg['pbacrexx'] = $nRegistros;                                    	//Cantidad Registros
		$vParBg['pbatxixx'] = 0.5;                                              //Tiempo Ejecucion x Item en Segundos
		$vParBg['pbaopcxx'] = "";                                             	//Opciones
		$vParBg['regusrxx'] = $kUser;                                         	//Usuario que Creo Registro

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
	} // fin del if ($_SERVER["SERVER_PORT"] != "" && $gEjProBg == "SI" && $nSwitch == 0)

	if ($cEjePro == 0) {
		if ($nSwitch == 0) {
  
      /**
       * Array con los nombres de los terceros 
       */
      $vNomTer = array();
      $vNitTer = array();


      /**
       * Array con los nombres de los usuarios 
       */
      $vNomUsr = array();
      $vIdUsr = array();

      // Busco todos los registros
      $qDatos  = "SELECT * ";
      $qDatos .= "FROM $cAlfa.fpar0158 ";
      $qDatos .= "WHERE ";
      if($gSucIdOri != "" && $gDocIdOri != "" && $gDocSufOri != ""){
        $qDatos .= "sucidorx = \"$gSucIdOri\"  AND ";
        $qDatos .= "docidorx = \"$gDocIdOri\"  AND ";
        $qDatos .= "docsufor = \"$gDocSufOri\" AND ";
      }
      if($gSucIdDes != "" && $gDocIdDes != "" && $gDocSufDes != ""){
        $qDatos .= "suciddex = \"$gSucIdDes\"  AND ";
        $qDatos .= "dociddex = \"$gDocIdDes\"  AND ";
        $qDatos .= "docsufde = \"$gDocSufDes\" AND ";
      }
      if($gCliId != ""){
        $qDatos .= "cliidorx = \"$gCliId\"  AND ";
      }
      $qDatos .= "regfcrex BETWEEN \"$gDesde\" AND \"$gHasta\" ";
      $qDatos .= "ORDER BY regfcrex";
      $xDatos  = f_MySql("SELECT","",$qDatos,$xConexion01,"");
      // echo $qDatos."~".mysql_num_rows($xDatos);
      $nCanReg = 0;
      $mDatos = array();
			while ($xRD = mysql_fetch_array($xDatos)) {
				$nCanReg++;
        if (($nCanReg % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }
        
        //Buscando nombre del cliente origen
        if(in_array("{$xRD['cliidorx']}",$vNitTer) == false) {
          $vNitTer[count($vNitTer)] = "{$xRD['cliidorx']}";
          //Trayendo el nombre del proveedor
          $qCliOri  = "SELECT ";
          $qCliOri .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
          $qCliOri .= "FROM $cAlfa.SIAI0150 ";
          $qCliOri .= "WHERE ";
          $qCliOri .= "CLIIDXXX = \"{$xRD['cliidorx']}\" LIMIT 0,1";
          $xCliOri = f_MySql("SELECT","",$qCliOri,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qCliOri."~".mysql_num_rows($xCliOri));
          if (mysql_num_rows($xCliOri) > 0) {
            $vCliOri = mysql_fetch_array($xCliOri);
            $xRD['clinomor'] = $vCliOri['clinomxx'];
            $vNomTer["{$xRD['cliidorx']}"] = $vCliOri['clinomxx'];
          }
        } else {
          $xRD['clinomor'] = $vNomTer["{$xRD['cliidorx']}"];
        }

        //Buscando nombre del cliente destino
        if(in_array("{$xRD['cliiddex']}",$vNitTer) == false) {
          $vNitTer[count($vNitTer)] = "{$xRD['cliiddex']}";
          //Trayendo el nombre del proveedor
          $qCliOri  = "SELECT ";
          $qCliOri .= "IF(TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)) != \"\",TRIM(CONCAT(CLINOM1X,\" \",CLINOM2X,\" \",CLIAPE1X,\" \",CLIAPE2X)), CLINOMXX) AS clinomxx ";
          $qCliOri .= "FROM $cAlfa.SIAI0150 ";
          $qCliOri .= "WHERE ";
          $qCliOri .= "CLIIDXXX = \"{$xRD['cliiddex']}\" LIMIT 0,1";
          $xCliOri = f_MySql("SELECT","",$qCliOri,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qCliOri."~".mysql_num_rows($xCliOri));
          if (mysql_num_rows($xCliOri) > 0) {
            $vCliOri = mysql_fetch_array($xCliOri);
            $xRD['clinomde'] = $vCliOri['clinomxx'];
            $vNomTer["{$xRD['cliiddex']}"] = $vCliOri['clinomxx'];
          }
        } else {
          $xRD['clinomde'] = $vNomTer["{$xRD['cliiddex']}"];
        }

        //Buscando nombre del usuario
        if(in_array("{$xRD['regusrxx']}",$vIdUsr) == false) {
          $vIdUsr[count($vIdUsr)] = "{$xRD['regusrxx']}";
          //Trayendo el nombre del proveedor
          $qUsrNom  = "SELECT ";
          $qUsrNom .= "USRNOMXX ";
          $qUsrNom .= "FROM $cAlfa.SIAI0003 ";
          $qUsrNom .= "WHERE ";
          $qUsrNom .= "USRIDXXX = \"{$xRD['regusrxx']}\" LIMIT 0,1";
          $xUsrNom = f_MySql("SELECT","",$qUsrNom,$xConexion01,"");
          // f_Mensaje(__FILE__,__LINE__,$qUsrNom."~".mysql_num_rows($xUsrNom));
          if (mysql_num_rows($xUsrNom) > 0) {
            $vUsrNom = mysql_fetch_array($xUsrNom);
            $xRD['usrnomxx'] = $vUsrNom['USRNOMXX'];
            $vNomUsr["{$xRD['regusrxx']}"] = $vUsrNom['USRNOMXX'];
          }
        } else {
          $xRD['usrnomxx'] = $vNomUsr["{$xRD['regusrxx']}"];
        }

				$mDatos[] = $xRD;
			}
			mysql_free_result($xDatos);
						
			if($nSwitch == 0){
				switch ($gTipo) {
					case 1: ?>
						<html>
							<head>
								<title>Reporte Traslado DO a DO</title>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css'>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
								<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
								<link rel="stylesheet" type="text/css" href="<?php echo $cSystem_Libs_JS_Directory ?>/gwtext/resources/css/ext-all.css" />
								<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/utility.js'></script>
								<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory_New  ?>/ajax.js'></script>
								<script type="text/javascript"  src = "<?php echo $cSystem_Libs_JS_Directory ?>/gwtext/adapter/ext/ext-base.js"></script>
								<script type="text/javascript"  src = "<?php echo $cSystem_Libs_JS_Directory ?>/gwtext/ext-all.js"></script>
								<script language = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/gwtext/conexijs/loading/loading.js"></script>
								<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js'></script>
								<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
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
								flush();?>
								<form name = 'frgrm' action='frrmmprn.php' method="post">
									<center>
										<table border="1" width="98%" cellspacing="0" cellpadding="0" align=center>
											<tr bgcolor = "white" height="30">
												<td class="name" align="left" colspan="34"><font size="4"><b>Reporte Traslado DO a DO</font></b>
												</td>
											</tr>
											<tr bgcolor = "white" height="30">
												<td class="name" align="left" colspan="34"><font size="2">Registros Analizados : <?php echo count($mDatos)?></font></td>
											</tr>
											<tr bgcolor = '<?php echo $vSysStr['system_row_title_color_ini'] ?>' height="30">
												<td class="name" width="100px">DO ORIGEN</td>
												<td class="name" width="100px">DO DESTINO</td>
												<td class="name" width="150px">USUARIO</td>
												<td class="name" width="100px">FECHA OPERACION</td>
												<td class="name" width="110px">FECHA MODIFICACION</td>
												<td class="name" width="100px">HORA</td>
												<td class="name" width="100px">NIT CLIENTE ORIGEN</td>
												<td class="name" width="150px">NOMBRE CLIENTE ORIGEN</td>
												<td class="name" width="100px">NIT CLIENTE DESTINO</td>
												<td class="name" width="150px">NOMBRE CLIENTE DESTINO</td>
												<td class="name" width="100px">No. COMPROBANTE ORIGEN</td>
												<td class="name" width="100px">No. COMPROBANTE DESTINO</td>
												<td class="name" width="100px">VALOR</td>
												<td class="name">OBSERVACIONES</td>
											</tr>
											<?php
											$nCanReg01 = 0;
											$y = 0;
											for($i=0; $i<count($mDatos); $i++){

												$cColor = "{$vSysStr['system_row_impar_color_ini']}";
												if($y % 2 == 0) {
													$cColor = "{$vSysStr['system_row_par_color_ini']}";
												}

												$nCanReg01++;
												if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }
												?>
												<tr bgcolor = "<?php echo $cColor ?>" onmouseover="javascript:uRowColor(this,'<?php echo $vSysStr['system_row_select_color_ini'] ?>')" onmouseout="javascript:uRowColor(this,'<?php echo $cColor ?>')">
													<td class="letra7" style="padding:2px;text-align:left"><?php  echo $mDatos[$i]['sucidorx']."-".$mDatos[$i]['docidorx']."-".$mDatos[$i]['docsufor'] ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php  echo $mDatos[$i]['suciddex']."-".$mDatos[$i]['dociddex']."-".$mDatos[$i]['docsufde'] ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php  echo $mDatos[$i]['usrnomxx'] ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php  echo $mDatos[$i]['comfecor'] ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php echo $mDatos[$i]['comfecde'] ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php echo $mDatos[$i]['comhorde'] ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php echo $mDatos[$i]['cliidorx'] ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php echo $mDatos[$i]['clinomor'] ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php echo $mDatos[$i]['cliiddex'] ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php echo $mDatos[$i]['clinomde'] ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php echo $mDatos[$i]['comidorx']."-".$mDatos[$i]['comcodor']."-".$mDatos[$i]['comcscor']."-".$mDatos[$i]['comcsc2o'] ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php echo $mDatos[$i]['comiddex']."-".$mDatos[$i]['comcodde']."-".$mDatos[$i]['comcscde']."-".$mDatos[$i]['comcsc2d'] ?></td>
													<td class="letra7" style="padding:2px;text-align:right"><?php echo ((strpos(($mDatos[$i]['comvlrxx']+0),'.') > 0) ? number_format(($mDatos[$i]['comvlrxx']+0),2,',','.') : number_format(($mDatos[$i]['comvlrxx']+0),0,',','.')) ?></td>
													<td class="letra7" style="padding:2px;text-align:left"><?php echo $mDatos[$i]['comobsxx'] ?></td>
												</tr>
												<?php
												$y++;
											} //Fin While que recorre el cursor de la matriz generada por la consulta. ?>
										</table><br>
									</center>
								</form>
							</body>
						</html>
					<?php
					break;
					case 2:
						// PINTA POR EXCEL
						if(count($mDatos) > 0){
			
							$cNomFile = "REPORTETRASLADO_DO_A_DO_".$kUser."_".date('YmdHis').".xls";
							// $cFile = "{$OPENINIT['pathdr']}/opencomex/".$vSysStr['system_download_directory']."/".$cNomFile;
							
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
								$cRuta = "{$OPENINIT['pathdr']}/opencomex/propios/$cAlfa/traslado_do";
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
								$cArcUsu = "REPORTETRASLADO_DO_A_DO_" . $kUser;
								$cArcHoy = "REPORTETRASLADO_DO_A_DO_" . $kUser . "_" . date("Ymd");
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
							
							$cF01 = fopen($cFile,"a");

							$cData = "<table cellspacing=\"0\" border=\"1\">";
								$cData .= "<tr>";
									$cData .= "<td colspan=\"14\"><b><font size=\"4\">Reporte Traslado DO a DO</font></b></td>";
								$cData .= "</tr>";
								$cData .= "<tr>";
								$cData .= "<td colspan=\"14\">Generado: ".date("Y-m-d")."  ".date("H:i:s")."</td>";
								$cData .= "</tr>";
								$cData .= "<tr>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>DO ORIGEN</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>DO DESTINO</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"150px\"><b>USUARIO</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>FECHA OPERACION</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"110px\"><b>FECHA MODIFICACION</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>HORA</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>NIT CLIENTE ORIGEN</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"150px\"><b>NOMBRE CLIENTE ORIGEN</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>NIT CLIENTE DESTINO</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"150px\"><b>NOMBRE CLIENTE DESTINO</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>No. COMPROBANTE ORIGEN</b></td>";
									$cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>No. COMPROBANTE DESTINO</b></td>";
                  $cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"100px\"><b>VALOR</b></td>";
                  $cData .= "<td bgcolor = \"".$vSysStr['system_row_title_color_ini']."\" width=\"200px\"><b>OBSERVACION</b></td>";
                $cData .= "</tr>";
                fwrite($cF01,$cData);
								$nCanReg01 = 0;
								for($i=0; $i<count($mDatos); $i++){
									$nCanReg01++;
									if (($nCanReg01 % _NUMREG_) == 0) { $xConexion01 = fnReiniciarConexionDBReporte($xConexion01); }

									$nToTCup = $mDatos[$i]['clicupcl'] + $mDatos[$i]['clicupsf']+0;

                  $cData  = '<tr>';
                  $cData .= '<td style="mso-number-format:\'\@\'">'.$mDatos[$i]['sucidorx'].'-'.$mDatos[$i]['docidorx'].'-'.$mDatos[$i]['docsufor'].'</td>';
                  $cData .= '<td style="mso-number-format:\'\@\'">'. $mDatos[$i]['suciddex'].'-'.$mDatos[$i]['dociddex'].'-'.$mDatos[$i]['docsufde'].'</td>';
                  $cData .= '<td style="mso-number-format:\'\@\'">'.$mDatos[$i]['usrnomxx'].'</td>';
                  $cData .= '<td style="mso-number-format:\'\@\'">'.$mDatos[$i]['comfecor'].'</td>';
                  $cData .= '<td style="mso-number-format:\'\@\'">'.$mDatos[$i]['comfecde'].'</td>';
                  $cData .= '<td style="mso-number-format:\'\@\'">'.$mDatos[$i]['comhorde'].'</td>';
                  $cData .= '<td style="mso-number-format:\'\@\'">'.$mDatos[$i]['cliidorx'].'</td>';
                  $cData .= '<td style="mso-number-format:\'\@\'">'.$mDatos[$i]['clinomor'].'</td>';
                  $cData .= '<td style="mso-number-format:\'\@\'">'.$mDatos[$i]['cliiddex'].'</td>';
                  $cData .= '<td style="mso-number-format:\'\@\'">'.$mDatos[$i]['clinomde'].'</td>';
                  $cData .= '<td style="mso-number-format:\'\@\'">'.$mDatos[$i]['comidorx'].'-'.$mDatos[$i]['comcodor'].'-'.$mDatos[$i]['comcscor'].'-'.$mDatos[$i]['comcsc2o'].'</td>';
                  $cData .= '<td style="mso-number-format:\'\@\'">'.$mDatos[$i]['comiddex'].'-'.$mDatos[$i]['comcodde'].'-'.$mDatos[$i]['comcscde'].'-'.$mDatos[$i]['comcsc2d'].'</td>';
                  $cData .= '<td style=\'text-align:right\'>'.((strpos(($mDatos[$i]['comvlrxx']+0),'.') > 0) ? number_format(($mDatos[$i]['comvlrxx']+0),2,',','.') : number_format(($mDatos[$i]['comvlrxx']+0),0,',','.')).'</td>';
                  $cData .= '<td style="mso-number-format:\'\@\'">'.$mDatos[$i]['comobsxx'].'</td>';
									$cData .= "</tr>";
									fwrite($cF01,$cData);
								} //Fin While que recorre el cursor de la matriz generada por la consulta.
							$cData = '</table>';
							fwrite($cF01,$cData);
							fclose($cF01);

							if (file_exists($cFile)) {

								if ($_SERVER["SERVER_PORT"] != "") {?>
										<script languaje = "javascript">
											parent.fmpro2.location = 'frtmddoc.php?cRuta=<?php echo $cNomFile ?>';
										</script>
										<?php 
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

							if ($_SERVER["SERVER_PORT"] != "") {
								$cMsj = "Proceso Realizado con Exito.\n";
								f_Mensaje(__FILE__,__LINE__,$cMsj);
							}
						}else{
							$nSwitch = 1;
							$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
							$cMsj .= "No Se Encontraron Registros.\n";
						}
					break;
				}//Fin Switch
			}
		}## if ($cEjePro == 0) {
	}## if ($nSwitch == 0) {

	if($nSwitch == 1){
		$cMsj = "Se Presentaron Errores en el Proceso.\n".$cMsj;
	}
	
	if ($nSwitch == 1){
		if ($_SERVER["SERVER_PORT"] != "") {
			f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.");
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
				$cMsj .= "Linea ".str_pad(__LINE__, 4,"0",STR_PAD_LEFT).": ";
				$cMsj .= $mReturnProBg[$nR]."\n";
			}
		}
	} // fin del if ($_SERVER["SERVER_PORT"] == "")

	/**
		* Metodo que realiza la conexion
		*/
	function fnConectarDBReporte(){
		global $cAlfa;

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
			$mReturn[0] = "true"; $mReturn[1] = $xConexion99;
		}else{
			$mReturn[0] = "false";
		}
		return $mReturn;
	}##function fnConectarDBReporte(){##

	/**
		* Metodo que realiza el reinicio de la conexion
		*/
	function fnReiniciarConexionDBReporte($pConexion){
		global $cHost;  global $cUserHost;  global $cPassHost;

		mysql_close($pConexion);
		$xConexion01 = mysql_connect($cHost,$cUserHost,$cPassHost,TRUE);

		return $xConexion01;
	}##function fnReiniciarConexionDBReporte(){##
?>

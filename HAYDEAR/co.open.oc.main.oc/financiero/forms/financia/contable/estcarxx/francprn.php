<?php
  /**
	 * Imprime Analisis de Cuentas.
	 * --- Descripcion: Permite Imprimir Estado de cuentas(por Cobrar / por Pagar).
	 * @author Sandra Guerrero <sguerrero@opentecnologia.com.co>
	 */

  ini_set('error_reporting', E_ERROR);
  ini_set("display_errors","1");

	ini_set("memory_limit","1024M");
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

  $mArcCre = array();  

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
    include("../../../../../config/config.php");
    include("../../../../libs/php/utility.php");
    include("../../../../../libs/php/utiprobg.php");
  }

  if ($_SERVER["SERVER_PORT"] != "") {
    $cEjProBg = ($cEjProBg != "SI") ? "NO" : $cEjProBg;
  }

	$fec  = $_POST['dHasta'];
	$cMes = "";

  switch (substr($fec,5,2)){
    case "01": $cMes="ENERO";     break;
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
  }

  /////INICO DE VALIDACIONES /////
  ///Inicio Validaciones para condiciones del Reporte ///
  // Inicio Edades de Cartera //
  if ($_POST['rCarEda'] == "SI") {
    if ($_POST['nComVlr01'] == "") {
			$nSwitch = 1;
			$cMsj = "El Rango Uno para el Filtro por Edades de Cartera no puede ser vacio.\n";
    } else {
	    if ($_POST['nComVlr01'] > $_POST['nComVlr02']) {
				$nSwitch = 1;
				$cMsj = "El Rango Uno no puede ser mayor al Rango Dos para el Filtro por Edades de Cartera.\n";
	    }
	    if ($_POST['nComVlr01'] > $_POST['nComVlr03']) {
				$nSwitch = 1;
				$cMsj = "El Rango Uno no puede ser mayor al Rango Tres para el Filtro por Edades de Cartera.\n";
	    }
	    if ($_POST['nComVlr01'] > $_POST['nComVlr04']) {
				$nSwitch = 1;
				$cMsj = "El Rango Uno no puede ser mayor al Rango Cuatro para el Filtro por Edades de Cartera.\n";
	    }
    }

    if ($_POST['nComVlr02'] == "") {
			$nSwitch = 1;
			$cMsj .= "El Rango Dos para el Filtro por Edades de Cartera no puede ser vacio.\n";
    } else {
	    if ($_POST['nComVlr02'] < $_POST['nComVlr01']) {
				$nSwitch = 1;
				$cMsj = "El Rango Dos no puede ser menor al Rango Uno para el Filtro por Edades de Cartera.\n";
	    }
	    if ($_POST['nComVlr02'] > $_POST['nComVlr03']) {
				$nSwitch = 1;
				$cMsj = "El Rango Dos no puede ser mayor al Rango Tres para el Filtro por Edades de Cartera.\n";
	    }
	    if ($_POST['nComVlr02'] > $_POST['nComVlr04']) {
				$nSwitch = 1;
				$cMsj = "El Rango Dos no puede ser mayor al Rango Cuatro para el Filtro por Edades de Cartera.\n";
	    }
    }

    if ($_POST['nComVlr03'] == "") {
			$nSwitch = 1;
			$cMsj .= "El Rango Tres para el Filtro por Edades de Cartera no puede ser vacio.\n";
    } else {
	    if ($_POST['nComVlr03'] < $_POST['nComVlr01']) {
				$nSwitch = 1;
				$cMsj = "El Rango Tres no puede ser menor al Rango Uno para el Filtro por Edades de Cartera.\n";
	    }
	    if ($_POST['nComVlr03'] < $_POST['nComVlr02']) {
				$nSwitch = 1;
				$cMsj = "El Rango Tres no puede ser menor al Rango Dos para el Filtro por Edades de Cartera.\n";
	    }
	    if ($_POST['nComVlr03'] > $_POST['nComVlr04']) {
				$nSwitch = 1;
				$cMsj = "El Rango Tres no puede ser mayor al Rango Dos para el Filtro por Edades de Cartera.\n";
	    }
    }

    if ($_POST['nComVlr04'] == "") {
			$nSwitch = 1;
			$cMsj .= "El Rango Cuatro para el Filtro por Edades de Cartera no puede ser vacio.\n";
    } else {
	    if ($_POST['nComVlr04'] < $_POST['nComVlr01']) {
				$nSwitch = 1;
				$cMsj = "El Rango Cuatro no puede ser menor al Rango Uno para el Filtro por Edades de Cartera.\n";
	    }
	    if ($_POST['nComVlr04'] < $_POST['nComVlr02']) {
				$nSwitch = 1;
				$cMsj = "El Rango Cuatro no puede ser menor al Rango Dos para el Filtro por Edades de Cartera.\n";
	    }
	    if ($_POST['nComVlr04'] < $_POST['nComVlr03']) {
				$nSwitch = 1;
				$cMsj = "El Rango Cuatro no puede ser menor al Rango Tres para el Filtro por Edades de Cartera.\n";
	    }
    }
  }
  // Fin Edades de Cartera //

  // Inicio Rango de Cuentas //
  if ($_POST['cPucIni'] != "" && $_POST['cPucFin'] == "") {
  	$nSwitch = 1;
  	$cMsj .= "El Rango Dos para el Filtro por Cuenta Contable no puede ser vacio.\n";
  }

  if ($_POST['cPucIni'] == "" && $_POST['cPucFin'] != "") {
  	$nSwitch = 1;
  	$cMsj .= "El Rango Uno para el Filtro por Cuenta Contable no puede ser vacio.\n";
  }

  if ($_POST['cPucIni'] != "" && $_POST['cPucFin'] != "" && $_POST['cOpe01'] == "A") {
  	if ($_POST['cPucFin'] < $_POST['cPucIni']) {
	  	$nSwitch = 1;
	  	$cMsj .= "El Rango Dos para el Filtro por Cuenta Contable no puede ser menor al Rango Uno.\n";
  	}
  }
  // Fin Rango de Cuentas //

  // Inicio Rango de Nits //
  if ($_POST['nNitIni'] != "" && $_POST['nNitFin'] == "") {
  	$nSwitch = 1;
  	$cMsj .= "El Rango Dos para el Filtro por Nit no puede ser vacio.\n";
  }

  if ($_POST['nNitIni'] == "" && $_POST['nNitFin'] != "") {
  	$nSwitch = 1;
  	$cMsj .= "El Rango Uno para el Filtro por Nit no puede ser vacio.\n";
  }

  if ($_POST['nNitIni'] != "" && $_POST['nNitFin'] != "" && $_POST['cOpe02'] == "A") {
  	if ($_POST['nNitFin'] < $_POST['nNitIni']) {
	  	$nSwitch = 1;
	  	$cMsj .= "El Rango Dos para el Filtro por Nit no puede ser menor al Rango Uno.\n";
  	}
  }
  // Fin Rango de Nits //

  // Inicio Rango de Centros de Costo //
  if ($_POST['nCcoIni'] != "" && $_POST['nCcoFin'] == "") {
  	$nSwitch = 1;
  	$cMsj .= "El Rango Dos para el Filtro por Centro de Costo no puede ser vacio.\n";
  }

  if ($_POST['nCcoIni'] == "" && $_POST['nCcoFin'] != "") {
  	$nSwitch = 1;
  	$cMsj .= "El Rango Uno para el Filtro por Centro de Costo no puede ser vacio.\n";
  }

  if ($_POST['nCcoIni'] != "" && $_POST['nCcoFin'] != "" && $_POST['cOpe03'] == "A") {
  	if ($_POST['nCcoFin'] < $_POST['nCcoIni']) {
	  	$nSwitch = 1;
	  	$cMsj .= "El Rango Dos para el Filtro por Centro de Costo no puede ser menor al Rango Uno.\n";
  	}
  }
  // Fin Rango de Centros de Costo //

  // Inicio Fecha de Corte //
  if ($_POST['dHasta'] == "") {
  	$nSwitch = 1;
  	$cMsj .= "La Fecha de Corte no puede ser vacio.\n";
  } else {
  	if (substr($_POST['dHasta'],0,4) < $vSysStr['financiero_ano_instalacion_modulo']) {
	  	$nSwitch = 1;
	  	$cMsj .= "El Ano de la Fecha de Corte no puede ser menor al Ano en que se instalo el Modulo Financiero Contable.\n";
  	}
  }

	if ($_SERVER["SERVER_PORT"] != "" && $cEjProBg == "SI" && $nSwitch == 0) {
    $cEjePro = 1;        

    $strPost  = "|rTipo~".$_POST['rTipo'];
    $strPost .= "|rTipCta~".$_POST['rTipCta'];
    $strPost .= "|rCarEda~".$_POST['rCarEda'];
    $strPost .= "|nComVlr01~".$_POST['nComVlr01'];
    $strPost .= "|nComVlr02~".$_POST['nComVlr02'];
    $strPost .= "|nComVlr03~".$_POST['nComVlr03'];
    $strPost .= "|nComVlr04~".$_POST['nComVlr04'];
    $strPost .= "|cPucIni~".$_POST['cPucIni'];
    $strPost .= "|cOpe01~".$_POST['cOpe01'];
    $strPost .= "|cPucFin~".$_POST['cPucFin'];
    $strPost .= "|nNitIni~".$_POST['nNitIni'];
    $strPost .= "|cOpe02~".$_POST['cOpe02'];
    $strPost .= "|nNitFin~".$_POST['nNitFin'];    
		$strPost .= "|nCcoIni~".$_POST['nCcoIni'];
		$strPost .= "|cOpe03~".$_POST['cOpe03'];
		$strPost .= "|nCcoFin~".$_POST['nCcoFin'];
		$strPost .= "|dHasta~".$_POST['dHasta'];
		$strPost .= "|rOrdRep~".$_POST['rOrdRep'];
		$nRegistros = 0;

    $vParBg['pbadbxxx'] = $cAlfa;                             //Base de Datos
    $vParBg['pbamodxx'] = "FACTURACION";                      //Modulo
    $vParBg['pbatinxx'] = "ANALISISDECARTERA";                 //Tipo Interface
    $vParBg['pbatinde'] = "ANALISIS DE CARTERA";             	//Descripcion Tipo de Interfaz
    $vParBg['admidxxx'] = "";                                 //Sucursal
    $vParBg['doiidxxx'] = "";                                 //Do
    $vParBg['doisfidx'] = "";                                 //Sufijo
    $vParBg['cliidxxx'] = "";                                 //Nit
    $vParBg['clinomxx'] = "";                                 //Nombre Importador
    $vParBg['pbapostx'] = $strPost;														//Parametros para reconstruir Post
    $vParBg['pbatabxx'] = "";                                 //Tablas Temporales
    $vParBg['pbascrxx'] = $_SERVER['SCRIPT_FILENAME'];        //Script
    $vParBg['pbacookx'] = $_COOKIE['kDatosFijos'];            //cookie
    $vParBg['pbacrexx'] = $nRegistros;                        //Cantidad Registros
    $vParBg['pbatxixx'] = 1;                                  //Tiempo Ejecucion x Item en Segundos
    $vParBg['pbaopcxx'] = "";                                 //Opciones
    $vParBg['regusrxx'] = $kUser;                             //Usuario que Creo Registro
  
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

  // Fin Fecha de Corte //
  //Fin de Validaciones para condiciones del Reporte
  /////FIN DE VALIDACIONES /////
	if ($cEjePro == 0) {
		if ($nSwitch == 0) {

			$AnoIni = $vSysStr['financiero_ano_instalacion_modulo'];
			$AnoFin=substr($_POST['dHasta'],0,4);

			if ($_POST['rTipCta']=="PAGAR") {
				$det='P';
			}
			if ($_POST['rTipCta']=="COBRAR") {
				$det='C';
			}

			##Creacion de la tabla detalle del dia
			$mTabMov = array(); //Nombre de las tablas temporales para el movimiento
			for ($cAno=$AnoIni;$cAno<=$AnoFin;$cAno++) {

				$cTabFac = fnCadenaAleatoria();
				$qNewTab  = "CREATE TEMPORARY TABLE IF NOT EXISTS $cAlfa.$cTabFac (";
				$qNewTab .= "comidcxx varchar(1),comcodcx varchar(4),comcsccx varchar(20),";
				$qNewTab .= "teridxxx varchar(12),pucidxxx varchar(10),comfecxx date,";
				$qNewTab .= "comfecve date,commovxx varchar(1),";
				$qNewTab .= "comvlrxx decimal(15,2),pucdesxx varchar(50),regestxx varchar(12),";
				$qNewTab .= "PRIMARY KEY (comidcxx,comcodcx,comcsccx,teridxxx,pucidxxx,commovxx))";
				$xNewTab = mysql_query($qNewTab,$xConexion01);

				$qDatMov  = "SELECT ";
				$qDatMov .= "$cAlfa.fcod$cAno.comidcxx, ";
				$qDatMov .= "$cAlfa.fcod$cAno.comcodcx, ";
				$qDatMov .= "$cAlfa.fcod$cAno.comcsccx, ";
				$qDatMov .= "$cAlfa.fcod$cAno.teridxxx, ";
				$qDatMov .= "$cAlfa.fcod$cAno.pucidxxx, ";
				$qDatMov .= "$cAlfa.fcod$cAno.comfecxx, ";
				$qDatMov .= "$cAlfa.fcod$cAno.comfecve, ";
				$qDatMov .= "$cAlfa.fcod$cAno.commovxx, ";
				$qDatMov .= "SUM(IF($cAlfa.fcod$cAno.commovxx = \"D\", $cAlfa.fcod$cAno.comvlrxx, $cAlfa.fcod$cAno.comvlrxx*-1)) AS comvlrxx, ";
				$qDatMov .= "IF($cAlfa.fpar0115.pucdesxx != \"\",$cAlfa.fpar0115.pucdesxx,\"SIN DESCRIPCION\") AS pucdesxx, ";
				$qDatMov .= "$cAlfa.fcod$cAno.regestxx ";
				$qDatMov .= "FROM $cAlfa.fcod$cAno ";
				$qDatMov .= "LEFT JOIN $cAlfa.fpar0115 ON $cAlfa.fcod$cAno.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) ";
				$qDatMov .= "WHERE $cAlfa.fcod$cAno.pucidxxx = CONCAT($cAlfa.fpar0115.pucgruxx,$cAlfa.fpar0115.pucctaxx,$cAlfa.fpar0115.pucsctax,$cAlfa.fpar0115.pucauxxx,$cAlfa.fpar0115.pucsauxx) AND ";
				// Inicio Rango de Cuentas //
				if ($_POST['cPucIni'] != "" && $_POST['cPucFin'] != "") {
					if ($_POST['cOpe01'] == "A") {
						$qDatMov .= "$cAlfa.fcod$cAno.pucidxxx BETWEEN \"{$_POST['cPucIni']}\" AND \"{$_POST['cPucFin']}\" AND ";
					}
					if ($_POST['cOpe01'] == "Y") {
						$qDatMov .= "$cAlfa.fcod$cAno.pucidxxx IN(\"{$_POST['cPucIni']}\",\"{$_POST['cPucFin']}\") AND ";
					}
				}
				// Fin Rango de Cuentas //

				// Inicio Rango de Nits //
				if ($_POST['nNitIni'] != "" && $_POST['nNitFin'] != "") {
					if ($_POST['cOpe02'] == "A") {
						$qDatMov .= "$cAlfa.fcod$cAno.teridxxx BETWEEN \"{$_POST['nNitIni']}\" AND \"{$_POST['nNitFin']}\" AND ";
					}
					if ($_POST['cOpe02'] == "Y") {
						$qDatMov .= "$cAlfa.fcod$cAno.teridxxx IN(\"{$_POST['nNitIni']}\",\"{$_POST['nNitFin']}\") AND ";
					}
				}
				// Fin Rango de Nits //

				// Inicio Rango de Centros de Costo //
				if ($_POST['nCcoIni'] != "" && $_POST['nCcoFin'] != "") {
					if ($_POST['cOpe03'] == "A") {
						$qDatMov .= "$cAlfa.fcod$cAno.ccoidxxx BETWEEN \"{$_POST['nCcoIni']}\" AND \"{$_POST['nCcoFin']}\" AND ";
					}
					if ($_POST['cOpe03'] == "Y") {
						$qDatMov .= "$cAlfa.fcod$cAno.ccoidxxx IN(\"{$_POST['nCcoIni']}\",\"{$_POST['nCcoFin']}\") AND ";
					}
				}

				// $qDatMov .= "$cAlfa.fcod$cAno.comidcxx = \"F\" AND ";
				// $qDatMov .= "$cAlfa.fcod$cAno.comcodcx = \"001\" AND ";
				// $qDatMov .= "$cAlfa.fcod$cAno.comcsccx = \"47446\" AND ";

				// Fin Rango de Centros de Costo //
				$qDatMov .= "$cAlfa.fcod$cAno.comfecxx <= \"{$_POST['dHasta']}\" AND ";
				$qDatMov .= "$cAlfa.fcod$cAno.regestxx = \"ACTIVO\" AND ";
				$qDatMov .= "$cAlfa.fpar0115.pucdetxx = \"$det\" ";
				$qDatMov .= "GROUP BY $cAlfa.fcod$cAno.comidcxx,$cAlfa.fcod$cAno.comcodcx,$cAlfa.fcod$cAno.comcsccx,$cAlfa.fcod$cAno.teridxxx,$cAlfa.fcod$cAno.pucidxxx,$cAlfa.fcod$cAno.commovxx ";
				$qDatMov .= "ORDER BY $cAlfa.fcod$cAno.comidcxx,$cAlfa.fcod$cAno.comcodcx,$cAlfa.fcod$cAno.comcsccx,$cAlfa.fcod$cAno.teridxxx,$cAlfa.fcod$cAno.pucidxxx,$cAlfa.fcod$cAno.comfecxx ";
				// f_Mensaje(__FILE__,__LINE__,$qDatMov);

				$qInsert = "INSERT INTO $cAlfa.$cTabFac $qDatMov";
        $xInsert = mysql_query($qInsert,$xConexion01);
        $xFree = mysql_free_result($xInsert);
				$mTabMov[$cAno] = $cTabFac;
				##Fin Creacion de la tabla detalle del dia

			}

			##Fin Acciones sobre la DB en el paso Dos
			$mDatMov = array();
			for ($cAno=$AnoIni;$cAno<=$AnoFin;$cAno++) {
				$qDatMov  = "SELECT ";
				$qDatMov .= "$cAlfa.{$mTabMov[$cAno]}.comidcxx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$cAno]}.comcodcx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$cAno]}.comcsccx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$cAno]}.teridxxx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$cAno]}.pucidxxx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$cAno]}.pucdesxx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$cAno]}.comfecxx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$cAno]}.comfecve, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$cAno]}.commovxx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$cAno]}.comvlrxx, ";
				$qDatMov .= "$cAlfa.{$mTabMov[$cAno]}.regestxx ";
				$qDatMov .= "FROM $cAlfa.{$mTabMov[$cAno]} ";
				$qDatMov .= "GROUP BY $cAlfa.{$mTabMov[$cAno]}.comidcxx,$cAlfa.{$mTabMov[$cAno]}.comcodcx,$cAlfa.{$mTabMov[$cAno]}.comcsccx,$cAlfa.{$mTabMov[$cAno]}.teridxxx,$cAlfa.{$mTabMov[$cAno]}.pucidxxx,$cAlfa.{$mTabMov[$cAno]}.commovxx ";
				$qDatMov .= "ORDER BY $cAlfa.{$mTabMov[$cAno]}.comidcxx,$cAlfa.{$mTabMov[$cAno]}.comcodcx,$cAlfa.{$mTabMov[$cAno]}.comcsccx,$cAlfa.{$mTabMov[$cAno]}.teridxxx,$cAlfa.{$mTabMov[$cAno]}.pucidxxx,$cAlfa.{$mTabMov[$cAno]}.comfecxx ";
				$xDatMov = mysql_query($qDatMov,$xConexion01);
				//f_Mensaje(__FILE__,__LINE__,$qDatMov."~".mysql_num_rows($xDatMov));
				while ($xCre = mysql_fetch_array($xDatMov)) {
					$cKey = $xCre['comidcxx']."-".$xCre['comcodcx']."-".$xCre['comcsccx']."-".$xCre['teridxxx']."-".$xCre['pucidxxx'];

					if($mDatMov[$cKey]['comidcxx'] == '') {
						$mDatMov[$cKey]['comidcxx']  = $xCre['comidcxx'];
						$mDatMov[$cKey]['comcodcx']  = $xCre['comcodcx'];
						$mDatMov[$cKey]['comcsccx']  = $xCre['comcsccx'];
						$mDatMov[$cKey]['teridxxx']  = $xCre['teridxxx'];
						$mDatMov[$cKey]['pucidxxx']  = $xCre['pucidxxx'];
						$mDatMov[$cKey]['pucdesxx']  = $xCre['pucdesxx'];
						$mDatMov[$cKey]['comfecxx']  = $xCre['comfecxx'];
						$mDatMov[$cKey]['comfecve']  = $xCre['comfecve'];
						$mDatMov[$cKey]['commovxx']  = $xCre['commovxx'];
						$mDatMov[$cKey]['regestxx']  = $xCre['regestxx'];
					}

					//Verificando la fecha menor
					if ($mDatMov[$cKey]['comfecxx'] != "") {
						$dFecAnt = mktime(0,0,0,substr($mDatMov[$cKey]['comfecxx'],5,2), substr($mDatMov[$cKey]['comfecxx'],8,2), substr($mDatMov[$cKey]['comfecxx'],0,4));
						$dFecNue = mktime(0,0,0,substr($xCre['comfecxx'],5,2), substr($xCre['comfecxx'],8,2), substr($xCre['comfecxx'],0,4));
						if ($dFecAnt > $dFecNue) {
							$mDatMov[$cKey]['comfecxx']  = $xCre['comfecxx'];
							$mDatMov[$cKey]['comfecve']  = $xCre['comfecve'];
						}
					}

					$mDatMov[$cKey]['saldoxxx'] = round($mDatMov[$cKey]['saldoxxx'],5) + round($xCre['comvlrxx'],5);
        }
        $xFree = mysql_free_result($xDatMov);
			}

			//// Empiezo a Recorrer la Matriz de Creditos Vs Debitos para Dejar las Cuentas que a la fecha de Corte tienen Saldo ////
			$j=0;
			foreach ($mDatMov as $i => $cValue) {
				if ($mDatMov[$i]['saldoxxx'] != 0) {
					# Traigo el Nombre del Cliente
					$qNomCli  = "SELECT ";
					$qNomCli .= "if($cAlfa.SIAI0150.CLINOMXX != \"\",$cAlfa.SIAI0150.CLINOMXX,IF($cAlfa.SIAI0150.CLIAPE1X  != \"\",CONCAT($cAlfa.SIAI0150.CLINOM1X,\" \",$cAlfa.SIAI0150.CLINOM2X,\" \",$cAlfa.SIAI0150.CLIAPE1X,\" \",$cAlfa.SIAI0150.CLIAPE2X),\"TERCERO SIN NOMBRE\")) AS clinomxx, ";
					$qNomCli .= "$cAlfa.SIAI0150.CLITELXX ";
					$qNomCli .= "FROM $cAlfa.SIAI0150 ";
					$qNomCli .= "WHERE $cAlfa.SIAI0150.CLIIDXXX = \"{$mDatMov[$i]['teridxxx']}\" LIMIT 0,1";
					$xNomCli = f_MySql("SELECT","",$qNomCli,$xConexion01,"");
					$vNomCli = mysql_fetch_array($xNomCli);
          $xFree = mysql_free_result($xNomCli);
					# Fin Traigo el Nombre del Cliente

					$dFecCor = str_replace("-","",$_POST['dHasta']);
					$dFecVen = str_replace("-","",$mDatMov[$i]['comfecve']);

					$dateCor = mktime(0,0,0,substr($dFecCor,4,2), substr($dFecCor,6,2), substr($dFecCor,0,4));
					$dateVen = mktime(0,0,0,substr($dFecVen,4,2), substr($dFecVen,6,2), substr($dFecVen,0,4));
					$valor= round(($dateCor  - $dateVen) / (60 * 60 * 24));

					$mDatos1[$j]['document'] = $mDatMov[$i]['comidcxx']."-".$mDatMov[$i]['comcodcx']."-".$mDatMov[$i]['comcsccx'];

					if ($vSysStr['financiero_aplica_tercer_consecutivo'] == "SI" && $mDatMov[$i]['comidcxx'] != "F") {
						// si el ups busco el comcsc3x
						for ($nAno=$AnoIni;$nAno<=$AnoFin;$nAno++) {

							$qDatMov  = "SELECT ";
							$qDatMov .= "$cAlfa.fcod$nAno.comcsc3x, ";
							$qDatMov .= "$cAlfa.fcod$nAno.comcsc2x ";
							$qDatMov .= "FROM $cAlfa.fcod$nAno ";
							$qDatMov .= "WHERE ";
							$qDatMov .= "($cAlfa.fcod$nAno.comidxxx = \"{$mDatMov[$i]['comidcxx']}\" OR $cAlfa.fcod$nAno.comidxxx =\"S\" ) AND ";
							$qDatMov .= "$cAlfa.fcod$nAno.comidcxx = \"{$mDatMov[$i]['comidcxx']}\" AND ";
							$qDatMov .= "$cAlfa.fcod$nAno.comcodcx = \"{$mDatMov[$i]['comcodcx']}\" AND ";
							$qDatMov .= "$cAlfa.fcod$nAno.comcsccx = \"{$mDatMov[$i]['comcsccx']}\" AND ";
							$qDatMov .= "$cAlfa.fcod$nAno.teridxxx = \"{$mDatMov[$i]['teridxxx']}\" AND ";
							$qDatMov .= "$cAlfa.fcod$nAno.pucidxxx = \"{$mDatMov[$i]['pucidxxx']}\" ";
							$qDatMov .= "LIMIT 0,1";
							$xDatMov  = mysql_query($qDatMov,$xConexion01);
							// echo $qDatMov.'~'.mysql_num_rows($xDatMov).'<br><br>';
							if (mysql_num_rows($xDatMov) > 0 ) {

								$vCre = mysql_fetch_array($xDatMov);
								//muestro el comcsc3x en el comprobante
								$mDatos1[$j]['document'] = $mDatMov[$i]['comidcxx']."-".$mDatMov[$i]['comcodcx']."-".$mDatMov[$i]['comcsccx']."-".(($vCre['comcsc3x'] != '') ? $vCre['comcsc3x'] : $vCre['comcsc2x']);
								$nAno = $AnoFin + 1;
              }
              $xFree = mysql_free_result($xDatMov);
						}
					}

					$mDatos1[$j]['comidxxx']=$mDatMov[$i]['comidcxx'];
					$mDatos1[$j]['comcodxx']=$mDatMov[$i]['comcodcx'];
					$mDatos1[$j]['comcscxx']=$mDatMov[$i]['comcsccx'];
					$mDatos1[$j]['comfecxx']=$mDatMov[$i]['comfecxx'];
					//$mDatos1[$j]['document']=$mDatMov[$i]['comidcxx']."-".$mDatMov[$i]['comcodcx']."-".$mDatMov[$i]['comcsccx'];
					$mDatos1[$j]['comfecve']=$mDatMov[$i]['comfecve'];
					$mDatos1[$j]['diascart']=$valor;
					$mDatos1[$j]['teridxxx']=$mDatMov[$i]['teridxxx'];
					$mDatos1[$j]['clinomxx']=($vNomCli['clinomxx'] != "") ? trim($vNomCli['clinomxx']) : "CLIENTE SIN NOMBRE";
					$mDatos1[$j]['clitelxx']=$vNomCli['CLITELXX'];
					$mDatos1[$j]['pucidxxx']=$mDatMov[$i]['pucidxxx'];
					$mDatos1[$j]['pucdesxx']=$mDatMov[$i]['pucdesxx'];
					$mDatos1[$j]['comvlrxx']=$mDatMov[$i]['comvlrxx'];
					$mDatos1[$j]['saldoxxx']=$mDatMov[$i]['saldoxxx'];

					$j++;
				}
			}

			//// Fin Recorrer la Matriz de Creditos-Debitos para Dejar las Cuentas que a la fecha de Corte tienen Saldo ////
			/////FIN DE CALCULOS PARA ARMAR EL ARCHIVO /////

			if ($_POST['rOrdRep'] == "NIT") {
				$mDatos = f_ordenar_array_bidimensional($mDatos1,'teridxxx',SORT_ASC,'pucidxxx',SORT_ASC,'document',SORT_ASC);
			}

			if ($_POST['rOrdRep'] == "CUENTA") {
				$mDatos = f_ordenar_array_bidimensional($mDatos1,'pucidxxx',SORT_ASC,'teridxxx',SORT_ASC,'document',SORT_ASC);
			}

			if ($_POST['rOrdRep'] == "ALFABETICO") {
				$mDatos = f_ordenar_array_bidimensional($mDatos1,'clinomxx',SORT_ASC,'pucidxxx',SORT_ASC,'document',SORT_ASC);
			}

			if ($_POST['rOrdRep'] == "MONTO") {
				$mDatos = f_ordenar_array_bidimensional($mDatos1,'saldoxxx',SORT_ASC,'teridxxx',SORT_ASC,'pucidxxx',SORT_ASC);
			}

			switch ($_POST['rTipo']) {
				case 1:
					// PINTA POR PANTALLA//
					?>
					<html>
						<head><title>Estado de Cuentas</title>
							<LINK rel = "stylesheet" href = "<?php echo $cSystem_Libs_JS_Directory ?>/estilo.css">
							<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/general.css'>
							<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/layout.css'>
							<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/custom.css'>
							<LINK rel = 'stylesheet' href = '<?php echo $cSystem_Libs_JS_Directory ?>/overlib.css'>
							<script languaje = "javascript" src = "<?php echo $cSystem_Libs_JS_Directory ?>/date_picker.js"></script>
							<script languaje = 'javascript' src = '<?php echo $cSystem_Libs_JS_Directory ?>/utility.js'></script>
						</head>
						<body>
							<?php
							if (count($mDatos) > 0) { ?>
								<center>
									<table width="100%">
										<tr>
											<td>
												<fieldset>
													<legend><h5> Resultado Consulta (<?php echo count($mDatos)?>)</h5></legend>
													<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; border: 1px solid black;">
														<tr>
															<td witdh="10%" rowspan="2">
																<?php
																switch ($cAlfa) {
																	case "ADUANAMO":
																	case "TEADUANAMO":
																	case "DEADUANAMO":?>
																		<img src="<?php echo $cRoot.$cPlesk_Skin_Directory.'/logo_aduanamo.png'?>" >
																	<?php break;
																	case "LOGINCAR":
																	case "DELOGINCAR":
																	case "TELOGINCAR":?>
																		<img width="156" height="41" src="<?php echo $cRoot.$cPlesk_Skin_Directory.'/Logo_Login_Cargo_Ltda_2.jpg'?>" >
																	<?php break;
																	case "ROLDANLO"://ROLDAN
																	case "TEROLDANLO"://ROLDAN
																	case "DEROLDANLO"://ROLDAN?>
																		<img width="160" height="60" src="<?php echo $cRoot.$cPlesk_Skin_Directory.'/logoroldan.png'?>" >
																	<?php break;
																	case "CASTANOX":
																	case "DECASTANOX":
																	case "TECASTANOX": ?>
																			<img width="156" height="90" src="<?php echo $cRoot.$cPlesk_Skin_Directory.'/logomartcam.jpg'?>" >
																	<?php break;
																	case "ALMACAFE": //ALMACAFE
																	case "TEALMACAFE": //ALMACAFE
																	case "DEALMACAFE": //ALMACAFE ?>
																		<img width="170" height="80" src="<?php echo $cRoot.$cPlesk_Skin_Directory.'/logoalmacafe.jpg'?>" >
																	<?php break;
																	case "TEADIMPEXX": // ADIMPEX
																	case "DEADIMPEXX": // ADIMPEX
																	case "ADIMPEXX": // ADIMPEX ?>
																		<img width="225" height="50" src="<?php echo $cRoot.$cPlesk_Skin_Directory.'/logoadimpex4.jpg'?>" >
																	<?php break;
																	case "GRUMALCO"://GRUMALCO
																	case "TEGRUMALCO"://GRUMALCO
																	case "DEGRUMALCO"://GRUMALCO?>
																		<img width="160" height="60" src="<?php echo $cRoot.$cPlesk_Skin_Directory.'/logomalco.jpg'?>" >
																	<?php break;
																	case "DHLEXPRE": //DHLEXPRE
																	case "TEDHLEXPRE": //DHLEXPRE
																	case "DEDHLEXPRE": //DHLEXPRE?>
																		<img width="150" height="50" src="<?php echo $cRoot.$cPlesk_Skin_Directory.'/logo_dhl_express.jpg'?>" >
																	<?php break;                                
																	case "ALADUANA"://ALADUANA
																	case "TEALADUANA"://ALADUANA
																	case "DEALADUANA": ?>
																		<img width="160" height="80" src="<?php echo $cRoot.$cPlesk_Skin_Directory.'/logoaladuana.jpg'?>" >
																	<?php break;
																	case "ANDINOSX"://ANDINOSX
																	case "TEANDINOSX"://ANDINOSX
																	case "DEANDINOSX": ?>
																		<img width="200" height="70" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logoandinos.jpg' ?>" >
																	<?php break;
																	case "GRUPOALC"://GRUPOALC
																	case "TEGRUPOALC"://GRUPOALC
																	case "DEGRUPOALC": //GRUPOALC ?>
																		<img width="160" height="70" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logoalc.jpg' ?>" >
																	<?php break;
																	case "AAINTERX"://AAINTERX
																	case "TEAAINTERX"://AAINTERX
																	case "DEAAINTERX": ?>
																		<img width="160" height="80" src="<?php echo $cRoot.$cPlesk_Skin_Directory.'/logointernacional.jpg'?>" >
																	<?php break;
																	case "AALOPEZX":
																	case "TEAALOPEZX":
																	case "DEAALOPEZX": ?>
																		<img width="140" height="80" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logoaalopez.png' ?>" >
																	<?php break;
																	case "ADUAMARX"://ADUAMARX
																	case "TEADUAMARX"://ADUAMARX
																	case "DEADUAMARX": //ADUAMARX ?>
																		<img width="70" height="70" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logoaduamar.jpg' ?>" >
																	<?php break;
																	case "SOLUCION"://SOLUCION
																	case "TESOLUCION"://SOLUCION
																	case "DESOLUCION": //SOLUCION ?>
																		<img width="150" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logosoluciones.jpg' ?>" >
																	<?php break;
																	case "FENIXSAS"://FENIXSAS
																	case "TEFENIXSAS"://FENIXSAS
																	case "DEFENIXSAS": //FENIXSAS ?>
																		<img width="150" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logofenix.jpg' ?>" >
																	<?php break;
																	case "COLVANXX"://COLVANXX
																	case "TECOLVANXX"://COLVANXX
																	case "DECOLVANXX": //COLVANXX ?>
																		<img width="150" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logocolvan.jpg' ?>" >
																	<?php break;
																	case "INTERLAC"://INTERLAC
																	case "TEINTERLAC"://INTERLAC
																	case "DEINTERLAC": //INTERLAC ?>
																		<img width="150" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logointerlace.jpg' ?>" >
																	<?php break;
																	case "KARGORUX": //KARGORUX
																	case "TEKARGORUX": //KARGORUX
																	case "DEKARGORUX": //KARGORUX ?>
																		<img width="150" style="margin-left: 10px;" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logokargoru.jpg' ?>">
																	<?php break;
                                  case "ALOGISAS": //LOGISTICA
                                  case "TEALOGISAS": //LOGISTICA
                                  case "DEALOGISAS": //LOGISTICA ?>
																		<img width="150" style="margin-left: 10px;" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logologisticasas.jpg' ?>">
																	<?php break;
                                  case "PROSERCO": //PROSERCO
                                  case "TEPROSERCO": //PROSERCO
                                  case "DEPROSERCO": //PROSERCO ?>
																		<img width="150" style="margin-left: 10px;" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logoproserco.png' ?>">
                                  <?php break;
                                  case "MANATIAL":
                                  case "TEMANATIAL":
                                  case "DEMANATIAL":?>
                                    <img width="150" style="margin-left: 10px;" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logomanantial.jpg' ?>">
                                  <?php break;
                                  case "DSVSASXX":
                                  case "DEDSVSASXX":
                                  case "TEDSVSASXX": ?>
                                    <img width="150" style="margin-left: 10px;" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logodsv.jpg' ?>">
                                  <?php break;
                                  case "MELYAKXX":    //MELYAK
                                  case "DEMELYAKXX":  //MELYAK
                                  case "TEMELYAKXX":  //MELYAK?>
                                    <img width="150" style="margin-left: 10px;" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logomelyak.jpg' ?>">
                                  <?php break;
                                  case "FEDEXEXP":    //FEDEX
                                  case "DEFEDEXEXP":  //FEDEX
                                  case "TEFEDEXEXP":  //FEDEX?>
                                      <img width="150" style="margin-left: 10px;" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logofedexexp.jpg' ?>">
                                    <?php break;
																	case "EXPORCOM":    //EXPORCOMEX
																	case "DEEXPORCOM":  //EXPORCOMEX
																	case "TEEXPORCOM":  //EXPORCOMEX?>
																			<img width="150" style="margin-left: 10px;" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logoexporcomex.jpg' ?>">
																		<?php break;
																	case "HAYDEARX":   //EXPORCOMEX
																	case "DEHAYDEARX": //EXPORCOMEX
																	case "TEHAYDEARX": //EXPORCOMEX ?>
																			<img width="200" style="margin-left: 10px;" src="<?php echo $cRoot . $cPlesk_Skin_Directory . '/logohaydear.jpeg' ?>">
																		<?php break;
																	default:
																		//No hace nada
																	break;
																}
																?>
															</td>
															<td width="90%" style="font-size:13px;font-weight:bold"><center><br>ESTADO DE CUENTAS POR <?php echo $_POST['rTipCta'] ?> A  <?php echo $cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4) ?></center><br></td>
														</tr>
														<tr>
															<td class="name" width="20%"><center>Fecha Consulta: <?php echo date('Y-m-d') ?> ( ORDENADO POR <?php echo $_POST['rOrdRep'] ?> )</center><br></td>
														</tr>
													</table>
													<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
														<tr bgcolor="<?php echo $vSysStr['system_row_title_color_ini'] ?>">
															<?php if($_POST['rCarEda'] == "SI") { ?>
																	<td class="name" width="05%"><center>Nit</center></td>
																	<td class="name" width="15%"><center>Nombre</center></td>
																	<td class="name" width="05%"><center>Cuenta</center></td>
																	<td class="name" width="15%"><center>Nombre Cuenta</center></td>
																	<td class="name" width="05%"><center>Tel&eacute;fono</center></td>
																	<td class="name" width="08%"><center>Documento</center></td>
																	<td class="name" width="06%"><center>Fecha Documento</center></td>
																	<td class="name" width="06%"><center>Fecha Vencimiento</center></td>
																	<td class="name" width="05%"><center>D&iacute;as Cartera</center></td>
																	<td class="name" width="06%"><center><?php echo " <= ".$_POST['nComVlr01'] ?></center></td>
																	<td class="name" width="06%"><center><?php echo ($_POST['nComVlr01']+1)." - ".$_POST['nComVlr02']  ?></center></td>
																	<td class="name" width="06%"><center><?php echo ($_POST['nComVlr02']+1)." - ".$_POST['nComVlr03']  ?></center></td>
																	<td class="name" width="06%"><center><?php echo ($_POST['nComVlr03']+1)." - ".$_POST['nComVlr04']  ?></center></td>
																	<td class="name" width="06%"><center><?php echo " > ".$_POST['nComVlr04'] ?></center></td>
															<?php } else { ?>
																<td class="name" width="06%"><center>Nit</center></td>
																<td class="name" width="20%"><center>Nombre</center></td>
																<td class="name" width="08%"><center>Cuenta</center></td>
																<td class="name" width="16%"><center>Nombre Cta</center></td>
																<td class="name" width="06%"><center>Telefono</center></td>
																<td class="name" width="10%"><center>Documento</center></td>
																<td class="name" width="08%"><center>Fecha Documento</center></td>
																<td class="name" width="08%"><center>Fecha Vencimiento</center></td>
																<td class="name" width="08%"><center>D&iacute;as Cartera</center></td>
																<td class="name" width="10%"><center>Saldo</center></td>
															<?php } ?>
														</tr>
														<tr>
															<?php
															$color = '#D5D5D5';

															if($_POST['rCarEda'] == "SI") {
																$AcuPri0=0; $AcuPri1=0; $AcuPri2=0; $AcuPri3=0; $AcuPri4=0;
																$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
																$AcuTer0=0; $AcuTer1=0; $AcuTer2=0; $AcuTer3=0; $AcuTer4=0;

																if ($_POST['rOrdRep']=='NIT') {
																	for($j=0;$j<count($mDatos);$j++) {
																		$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;

																		if ($mDatos[$j]['diascart'] <= $_POST['nComVlr01']) {
																			$Saldo0=$mDatos[$j]['saldoxxx'];
																			$AcuTer0+=$mDatos[$j]['saldoxxx'];
																			$Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr01'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr02']) {
																			$Saldo1=$mDatos[$j]['saldoxxx'];
																			$AcuTer1+=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr02'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr03']) {
																			$Saldo2=$mDatos[$j]['saldoxxx'];
																			$AcuTer2+=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo1=0; $Saldo3=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr03'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr04']) {
																			$Saldo3=$mDatos[$j]['saldoxxx'];
																			$AcuTer3+=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr04']) {
																			$Saldo4=$mDatos[$j]['saldoxxx'];
																			$AcuTer4+=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0;
																		}

																		$AcuPri0+=$Saldo0;
																		$AcuPri1+=$Saldo1;
																		$AcuPri2+=$Saldo2;
																		$AcuPri3+=$Saldo3;
																		$AcuPri4+=$Saldo4;

																		$AcuSeg0+=$Saldo0;
																		$AcuSeg1+=$Saldo1;
																		$AcuSeg2+=$Saldo2;
																		$AcuSeg3+=$Saldo3;
																		$AcuSeg4+=$Saldo4;

																		?>
																		<tr bgcolor="<?php echo $color ?>">
																			<td class="letra7" width="05%"><?php echo $mDatos[$j]['teridxxx'] ?></td>
																			<td class="letra7" width="15%"><?php echo substr($mDatos[$j]['clinomxx'],0,30) ?></td>
																			<td class="letra7" width="05%"><?php echo $mDatos[$j]['pucidxxx'] ?></td>
																			<td class="letra7" width="15%"><?php echo substr($mDatos[$j]['pucdesxx'],0,30) ?></td>
																			<td class="letra7" width="05%"><?php echo $mDatos[$j]['clitelxx'] ?></td>
																			<td class="letra7" width="08%"><?php echo $mDatos[$j]['document'] ?></td>
																			<td class="letra7" width="06%" align="center"><?php echo $mDatos[$j]['comfecxx'] ?></td>
																			<td class="letra7" width="06%" align="center"><?php echo $mDatos[$j]['comfecve'] ?></td>
																			<td class="letra7" width="05%" align="right"><?php echo $mDatos[$j]['diascart'] ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo0,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo1,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo2,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo3,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo4,2,',','.') ?></td>
																		</tr>
																		<?php
																		if ($mDatos[$j]['teridxxx'] == $mDatos[$j+1]['teridxxx']) {
																			if ($mDatos[$j]['pucidxxx'] != $mDatos[$j+1]['pucidxxx']) {
																				?>
																				</table>
																				<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																					<tr bgcolor="#FFF8DC">
																						<td class="letra7" width="70%" style="font-size:11px;font-weight:bold">SUBTOTAL CUENTA <?php echo $mDatos[$j]['pucdesxx'] ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg0,2,',','.') ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg1,2,',','.') ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg2,2,',','.') ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg3,2,',','.') ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg4,2,',','.') ?></td>
																					</tr>
																				</table>
																				<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																				<?php
																				$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
																			}
																		}	else {
																			?>
																			</table>
																			<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																				<tr bgcolor="#FFF8DC">
																					<td class="letra7" width="70%" style="font-size:11px;font-weight:bold">SUBTOTAL CUENTA <?php echo $mDatos[$j]['pucdesxx'] ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg0,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg1,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg2,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg3,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg4,2,',','.') ?></td>
																				</tr>
																				<tr bgcolor="#FFE4C4">
																					<td class="letra7" width="70%" style="font-size:11px;font-weight:bold">SUBTOTAL TERCERO <?php echo $mDatos[$j]['clinomxx'] ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri0,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri1,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri2,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri3,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri4,2,',','.') ?></td>
																				</tr>
																			</table>
																			<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																			<?php
																			$AcuPri0=0; $AcuPri1=0; $AcuPri2=0; $AcuPri3=0; $AcuPri4=0;
																			$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
																		}
																	}
																}

																if ($_POST['rOrdRep']=='CUENTA') {
																	for($j=0;$j<count($mDatos);$j++) {
																		$Total+=$mDatos[$j]['saldoxxx'];
																		$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;

																		if ($mDatos[$j]['diascart'] <= $_POST['nComVlr01']) {
																			$Saldo0=$mDatos[$j]['saldoxxx'];
																			$Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr01'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr02']) {
																			$Saldo1=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr02'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr03']) {
																			$Saldo2=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo1=0; $Saldo3=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr03'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr04']) {
																			$Saldo3=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr04']) {
																			$Saldo4=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0;
																		}

																		$AcuPri0+=$Saldo0;
																		$AcuPri1+=$Saldo1;
																		$AcuPri2+=$Saldo2;
																		$AcuPri3+=$Saldo3;
																		$AcuPri4+=$Saldo4;

																		$AcuSeg0+=$Saldo0;
																		$AcuSeg1+=$Saldo1;
																		$AcuSeg2+=$Saldo2;
																		$AcuSeg3+=$Saldo3;
																		$AcuSeg4+=$Saldo4;
																		?>
																		<tr bgcolor="<?php echo $color ?>">
																			<td class="letra7" width="05%"><?php echo $mDatos[$j]['teridxxx'] ?></td>
																			<td class="letra7" width="15%"><?php echo substr($mDatos[$j]['clinomxx'],0,30) ?></td>
																			<td class="letra7" width="05%"><?php echo $mDatos[$j]['pucidxxx'] ?></td>
																			<td class="letra7" width="15%"><?php echo substr($mDatos[$j]['pucdesxx'],0,30) ?></td>
																			<td class="letra7" width="05%"><?php echo $mDatos[$j]['clitelxx'] ?></td>
																			<td class="letra7" width="08%"><?php echo $mDatos[$j]['document'] ?></td>
																			<td class="letra7" width="06%" align="center"><?php echo $mDatos[$j]['comfecxx'] ?></td>
																			<td class="letra7" width="06%" align="center"><?php echo $mDatos[$j]['comfecve'] ?></td>
																			<td class="letra7" width="05%" align="right"><?php echo $mDatos[$j]['diascart'] ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo0,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo1,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo2,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo3,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo4,2,',','.') ?></td>
																		</tr>
																		<?php
																		if ($mDatos[$j]['pucidxxx'] == $mDatos[$j+1]['pucidxxx']) {
																			if ($mDatos[$j]['teridxxx'] != $mDatos[$j+1]['teridxxx']) {
																				?>
																				</table>
																				<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																					<tr bgcolor="#FFF8DC">
																						<td class="letra7" width="70%" style="font-size:11px;font-weight:bold">SUBTOTAL TERCERO <?php echo $mDatos[$j]['clinomxx'] ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg0,2,',','.') ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg1,2,',','.') ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg2,2,',','.') ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg3,2,',','.') ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg4,2,',','.') ?></td>
																					</tr>
																				</table>
																				<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																				<?php
																				$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
																			}
																		}	else {
																			?>
																			</table>
																			<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																				<tr bgcolor="#FFF8DC">
																					<td class="letra7" width="70%" style="font-size:11px;font-weight:bold">SUBTOTAL TERCERO <?php echo $mDatos[$j]['clinomxx'] ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg0,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg1,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg2,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg3,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg4,2,',','.') ?></td>
																				</tr>
																				<tr bgcolor="#FFE4C4">
																					<td class="letra7" width="70%" style="font-size:12px;font-weight:bold">SUBTOTAL CUENTA <?php echo $mDatos[$j]['pucdesxx'] ?></td>
																					<td class="letra7" width="06%" style="font-size:12px;font-weight:bold" align="right"><?php echo number_format($AcuPri0,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:12px;font-weight:bold" align="right"><?php echo number_format($AcuPri1,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:12px;font-weight:bold" align="right"><?php echo number_format($AcuPri2,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:12px;font-weight:bold" align="right"><?php echo number_format($AcuPri3,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:12px;font-weight:bold" align="right"><?php echo number_format($AcuPri4,2,',','.') ?></td>
																				</tr>
																			</table>
																			<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																			<?php
																			$AcuPri0=0; $AcuPri1=0; $AcuPri2=0; $AcuPri3=0; $AcuPri4=0;
																			$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
																		}
																	}
																}

																if ($_POST['rOrdRep']=='ALFABETICO') {
																	for($j=0;$j<count($mDatos);$j++) {
																		$Total+=$mDatos[$j]['saldoxxx'];
																		$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;

																		if ($mDatos[$j]['diascart'] <= $_POST['nComVlr01']) {
																			$Saldo0=$mDatos[$j]['saldoxxx'];
																			$Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr01'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr02']) {
																			$Saldo1=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr02'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr03']) {
																			$Saldo2=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo1=0; $Saldo3=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr03'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr04']) {
																			$Saldo3=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr04']) {
																			$Saldo4=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0;
																		}

																		$AcuPri0+=$Saldo0;
																		$AcuPri1+=$Saldo1;
																		$AcuPri2+=$Saldo2;
																		$AcuPri3+=$Saldo3;
																		$AcuPri4+=$Saldo4;

																		$AcuSeg0+=$Saldo0;
																		$AcuSeg1+=$Saldo1;
																		$AcuSeg2+=$Saldo2;
																		$AcuSeg3+=$Saldo3;
																		$AcuSeg4+=$Saldo4;
																		?>
																		<tr bgcolor="<?php echo $color ?>">
																			<td class="letra7" width="05%"><?php echo $mDatos[$j]['teridxxx'] ?></td>
																			<td class="letra7" width="15%"><?php echo substr($mDatos[$j]['clinomxx'],0,30) ?></td>
																			<td class="letra7" width="05%"><?php echo $mDatos[$j]['pucidxxx'] ?></td>
																			<td class="letra7" width="15%"><?php echo substr($mDatos[$j]['pucdesxx'],0,30) ?></td>
																			<td class="letra7" width="05%"><?php echo $mDatos[$j]['clitelxx'] ?></td>
																			<td class="letra7" width="08%"><?php echo $mDatos[$j]['document'] ?></td>
																			<td class="letra7" width="06%" align="center"><?php echo $mDatos[$j]['comfecxx'] ?></td>
																			<td class="letra7" width="06%" align="center"><?php echo $mDatos[$j]['comfecve'] ?></td>
																			<td class="letra7" width="05%" align="right"><?php echo $mDatos[$j]['diascart'] ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo0,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo1,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo2,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo3,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo4,2,',','.') ?></td>
																		</tr>
																		<?php
																		if ($mDatos[$j]['clinomxx'] == $mDatos[$j+1]['clinomxx']) {
																			if ($mDatos[$j]['pucidxxx'] != $mDatos[$j+1]['pucidxxx']) {
																				?>
																				</table>
																				<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																					<tr bgcolor="#FFF8DC">
																						<td class="letra7" width="70%" style="font-size:11px;font-weight:bold">SUBTOTAL CUENTA <?php echo $mDatos[$j]['pucdesxx'] ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg0,2,',','.') ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg1,2,',','.') ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg2,2,',','.') ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg3,2,',','.') ?></td>
																						<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg4,2,',','.') ?></td>
																					</tr>
																				</table>
																				<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																				<?php
																				$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
																			}
																		}	else {
																			?>
																			</table>
																			<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																				<tr bgcolor="#FFF8DC">
																					<td class="letra7" width="70%" style="font-size:11px;font-weight:bold">SUBTOTAL CUENTA <?php echo $mDatos[$j]['pucdesxx'] ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg0,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg1,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg2,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg3,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg4,2,',','.') ?></td>
																				</tr>
																				<tr bgcolor="#FFE4C4">
																					<td class="letra7" width="70%" style="font-size:11px;font-weight:bold">SUBTOTAL TERCERO <?php echo $mDatos[$j]['clinomxx'] ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri0,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri1,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri2,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri3,2,',','.') ?></td>
																					<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri4,2,',','.') ?></td>
																				</tr>
																			</table>
																			<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																			<?php
																			$AcuPri0=0; $AcuPri1=0; $AcuPri2=0; $AcuPri3=0; $AcuPri4=0;
																			$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
																		}
																	}
																}

																if ($_POST['rOrdRep']=='MONTO') {
																	for($j=0;$j<count($mDatos);$j++) {
																		$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;

																		if ($mDatos[$j]['diascart'] <= $_POST['nComVlr01']) {
																			$Saldo0=$mDatos[$j]['saldoxxx'];
																			$AcuTer0+=$mDatos[$j]['saldoxxx'];
																			$Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr01'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr02']) {
																			$Saldo1=$mDatos[$j]['saldoxxx'];
																			$AcuTer1+=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr02'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr03']) {
																			$Saldo2=$mDatos[$j]['saldoxxx'];
																			$AcuTer2+=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo1=0; $Saldo3=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr03'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr04']) {
																			$Saldo3=$mDatos[$j]['saldoxxx'];
																			$AcuTer3+=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo4=0;
																		}

																		if ($mDatos[$j]['diascart'] > $_POST['nComVlr04']) {
																			$Saldo4=$mDatos[$j]['saldoxxx'];
																			$AcuTer4+=$mDatos[$j]['saldoxxx'];
																			$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0;
																		}

																		$AcuPri0+=$Saldo0;
																		$AcuPri1+=$Saldo1;
																		$AcuPri2+=$Saldo2;
																		$AcuPri3+=$Saldo3;
																		$AcuPri4+=$Saldo4;
																		?>
																		<tr bgcolor="<?php echo $color ?>">
																			<td class="letra7" width="05%"><?php echo $mDatos[$j]['teridxxx'] ?></td>
																			<td class="letra7" width="15%"><?php echo substr($mDatos[$j]['clinomxx'],0,30) ?></td>
																			<td class="letra7" width="05%"><?php echo $mDatos[$j]['pucidxxx'] ?></td>
																			<td class="letra7" width="15%"><?php echo substr($mDatos[$j]['pucdesxx'],0,30) ?></td>
																			<td class="letra7" width="05%"><?php echo $mDatos[$j]['clitelxx'] ?></td>
																			<td class="letra7" width="08%"><?php echo $mDatos[$j]['document'] ?></td>
																			<td class="letra7" width="06%" align="center"><?php echo $mDatos[$j]['comfecxx'] ?></td>
																			<td class="letra7" width="06%" align="center"><?php echo $mDatos[$j]['comfecve'] ?></td>
																			<td class="letra7" width="05%" align="right"><?php echo $mDatos[$j]['diascart'] ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo0,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo1,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo2,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo3,2,',','.') ?></td>
																			<td class="letra7" width="06%" align="right"><?php echo number_format($Saldo4,2,',','.') ?></td>
																		</tr>
																		<?php
																	}
																	?>
																	</table>
																	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																		<tr bgcolor="#FFE4C4">
																			<td class="letra7" width="70%" style="font-size:11px;font-weight:bold">SUBTOTAL <?php echo $mDatos[$j]['clinomxx'] ?></td>
																			<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri0,2,',','.') ?></td>
																			<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri1,2,',','.') ?></td>
																			<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri2,2,',','.') ?></td>
																			<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri3,2,',','.') ?></td>
																			<td class="letra7" width="06%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri4,2,',','.') ?></td>
																		</tr>
																	</table>
																	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																	<?php
																}?>
																<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																	<tr>
																		<td class="letra7" width="70%" style="font-size:10px;font-weight:bold">TOTAL GENERAL </td>
																			<td class="letra7" width="06%" style="font-size:10px;font-weight:bold" align="right"><?php echo number_format($AcuTer0,0,',','.') ?></td>
																			<td class="letra7" width="06%" style="font-size:10px;font-weight:bold" align="right"><?php echo number_format($AcuTer1,0,',','.') ?></td>
																			<td class="letra7" width="06%" style="font-size:10px;font-weight:bold" align="right"><?php echo number_format($AcuTer2,0,',','.') ?></td>
																			<td class="letra7" width="06%" style="font-size:10px;font-weight:bold" align="right"><?php echo number_format($AcuTer3,0,',','.') ?></td>
																			<td class="letra7" width="06%" style="font-size:10px;font-weight:bold" align="right"><?php echo number_format($AcuTer4,0,',','.') ?></td>
																	</tr>
																</table>
																<?php
															}

															if($_POST['rCarEda'] == "NO") {
																$Total=0; $Saldo0=0; $AcuPri0=0; $AcuSeg0=0;

																if ($_POST['rOrdRep']=='NIT') {
																	for($j=0;$j<count($mDatos);$j++) {
																		$Total+=$mDatos[$j]['saldoxxx'];
																		$Saldo0=$mDatos[$j]['saldoxxx'];
																		$AcuPri0+=$Saldo0;
																		$AcuSeg0+=$Saldo0;
																		?>
																		<tr bgcolor="<?php echo $color ?>">
																			<td class="letra7" width="06%"><?php echo $mDatos[$j]['teridxxx'] ?></td>
																			<td class="letra7" width="20%"><?php echo substr($mDatos[$j]['clinomxx'],0,30) ?></td>
																			<td class="letra7" width="08%"><?php echo $mDatos[$j]['pucidxxx'] ?></td>
																			<td class="letra7" width="16%"><?php echo substr($mDatos[$j]['pucdesxx'],0,30) ?></td>
																			<td class="letra7" width="06%"><?php echo $mDatos[$j]['clitelxx'] ?></td>
																			<td class="letra7" width="10%"><?php echo $mDatos[$j]['document'] ?></td>
																			<td class="letra7" width="08%" align="center"><?php echo $mDatos[$j]['comfecxx'] ?></td>
																			<td class="letra7" width="08%" align="center"><?php echo $mDatos[$j]['comfecve'] ?></td>
																			<td class="letra7" width="08%" align="right"><?php echo $mDatos[$j]['diascart'] ?></td>
																			<td class="letra7" width="10%" align="right"><?php echo number_format($Saldo0,2,',','.') ?></td>
																		</tr>
																		<?php
																		if ($mDatos[$j]['teridxxx'] == $mDatos[$j+1]['teridxxx']) {
																			if ($mDatos[$j]['pucidxxx'] != $mDatos[$j+1]['pucidxxx']) {
																				?>
																				</table>
																				<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																					<tr bgcolor="#FFF8DC">
																						<td class="letra7" width="90%" style="font-size:11px;font-weight:bold">SUBTOTAL CUENTA <?php echo $mDatos[$j]['pucdesxx'] ?></td>
																						<td class="letra7" width="10%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg0,2,',','.') ?></td>
																					</tr>
																				</table>
																				<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																				<?php
																				$AcuSeg0=0;
																			}
																		}	else {
																			?>
																			</table>
																			<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																				<tr bgcolor="#FFF8DC">
																					<td class="letra7" width="90%" style="font-size:11px;font-weight:bold">SUBTOTAL CUENTA <?php echo $mDatos[$j]['pucdesxx'] ?></td>
																					<td class="letra7" width="10%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg0,2,',','.') ?></td>
																				</tr>
																				<tr bgcolor="#FFE4C4">
																					<td class="letra7" width="70%" style="font-size:11px;font-weight:bold">SUBTOTAL TERCERO <?php echo $mDatos[$j]['clinomxx'] ?></td>
																					<td class="letra7" width="10%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri0,2,',','.') ?></td>
																				</tr>
																			</table>
																			<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																			<?php
																			$AcuPri0=0;$AcuSeg0=0;
																		}
																	}
																}

																if ($_POST['rOrdRep']=='CUENTA') {
																	for($j=0;$j<count($mDatos);$j++) {
																		$Total+=$mDatos[$j]['saldoxxx'];
																		$Saldo0=$mDatos[$j]['saldoxxx'];
																		$AcuPri0+=$Saldo0;
																		$AcuSeg0+=$Saldo0;
																		?>
																		<tr bgcolor="<?php echo $color ?>">
																			<td class="letra7" width="06%"><?php echo $mDatos[$j]['teridxxx'] ?></td>
																			<td class="letra7" width="20%"><?php echo substr($mDatos[$j]['clinomxx'],0,30) ?></td>
																			<td class="letra7" width="08%"><?php echo $mDatos[$j]['pucidxxx'] ?></td>
																			<td class="letra7" width="16%"><?php echo substr($mDatos[$j]['pucdesxx'],0,30) ?></td>
																			<td class="letra7" width="06%"><?php echo $mDatos[$j]['clitelxx'] ?></td>
																			<td class="letra7" width="10%"><?php echo $mDatos[$j]['document'] ?></td>
																			<td class="letra7" width="08%" align="center"><?php echo $mDatos[$j]['comfecxx'] ?></td>
																			<td class="letra7" width="08%" align="center"><?php echo $mDatos[$j]['comfecve'] ?></td>
																			<td class="letra7" width="08%" align="right"><?php echo $mDatos[$j]['diascart'] ?></td>
																			<td class="letra7" width="10%" align="right"><?php echo number_format($Saldo0,2,',','.') ?></td>
																		</tr>
																		<?php
																		if ($mDatos[$j]['pucidxxx'] == $mDatos[$j+1]['pucidxxx']) {
																			if ($mDatos[$j]['teridxxx'] != $mDatos[$j+1]['teridxxx']) {
																				?>
																				</table>
																				<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																					<tr bgcolor="#FFF8DC">
																						<td class="letra7" width="90%" style="font-size:11px;font-weight:bold">SUBTOTAL TERCERO <?php echo $mDatos[$j]['clinomxx'] ?></td>
																						<td class="letra7" width="10%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg0,2,',','.') ?></td>
																					</tr>
																				</table>
																				<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																				<?php
																				$AcuSeg0=0;
																			}
																		}	else {
																			?>
																			</table>
																			<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																				<tr bgcolor="#FFF8DC">
																					<td class="letra7" width="90%" style="font-size:11px;font-weight:bold">SUBTOTAL TERCERO <?php echo $mDatos[$j]['clinomxx'] ?></td>
																					<td class="letra7" width="10%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg0,2,',','.') ?></td>
																				</tr>
																				<tr bgcolor="#FFE4C4">
																					<td class="letra7" width="90%" style="font-size:12px;font-weight:bold">SUBTOTAL CUENTA <?php echo $mDatos[$j]['pucdesxx'] ?></td>
																					<td class="letra7" width="10%" style="font-size:12px;font-weight:bold" align="right"><?php echo number_format($AcuPri0,2,',','.') ?></td>
																				</tr>
																			</table>
																			<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																			<?php
																			$AcuPri0=0; $AcuSeg0=0;
																		}
																	}
																}

																if ($_POST['rOrdRep']=='ALFABETICO') {
																	for($j=0;$j<count($mDatos);$j++) {
																		$Total+=$mDatos[$j]['saldoxxx'];
																		$Saldo0=$mDatos[$j]['saldoxxx'];
																		$AcuPri0+=$Saldo0;
																		$AcuSeg0+=$Saldo0;
																		?>
																		<tr bgcolor="<?php echo $color ?>">
																			<td class="letra7" width="06%"><?php echo $mDatos[$j]['teridxxx'] ?></td>
																			<td class="letra7" width="20%"><?php echo substr($mDatos[$j]['clinomxx'],0,30) ?></td>
																			<td class="letra7" width="08%"><?php echo $mDatos[$j]['pucidxxx'] ?></td>
																			<td class="letra7" width="16%"><?php echo substr($mDatos[$j]['pucdesxx'],0,30) ?></td>
																			<td class="letra7" width="06%"><?php echo $mDatos[$j]['clitelxx'] ?></td>
																			<td class="letra7" width="10%"><?php echo $mDatos[$j]['document'] ?></td>
																			<td class="letra7" width="08%" align="center"><?php echo $mDatos[$j]['comfecxx'] ?></td>
																			<td class="letra7" width="08%" align="center"><?php echo $mDatos[$j]['comfecve'] ?></td>
																			<td class="letra7" width="08%" align="right"><?php echo $mDatos[$j]['diascart'] ?></td>
																			<td class="letra7" width="10%" align="right"><?php echo number_format($Saldo0,2,',','.') ?></td>
																		</tr>
																		<?php
																		if ($mDatos[$j]['clinomxx'] == $mDatos[$j+1]['clinomxx']) {
																			if ($mDatos[$j]['pucidxxx'] != $mDatos[$j+1]['pucidxxx']) {
																				?>
																				</table>
																				<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																					<tr bgcolor="#FFF8DC">
																						<td class="letra7" width="90%" style="font-size:11px;font-weight:bold">SUBTOTAL CUENTA <?php echo $mDatos[$j]['pucdesxx'] ?></td>
																						<td class="letra7" width="10%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg0,2,',','.') ?></td>
																					</tr>
																				</table>
																				<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																				<?php
																				$AcuSeg0=0;
																			}
																		}	else {
																			?>
																			</table>
																			<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																				<tr bgcolor="#FFF8DC">
																					<td class="letra7" width="90%" style="font-size:11px;font-weight:bold">SUBTOTAL CUENTA <?php echo $mDatos[$j]['pucdesxx'] ?></td>
																					<td class="letra7" width="10%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuSeg0,2,',','.') ?></td>
																				</tr>
																				<tr bgcolor="#FFE4C4">
																					<td class="letra7" width="90%" style="font-size:11px;font-weight:bold">SUBTOTAL TERCERO <?php echo $mDatos[$j]['clinomxx'] ?></td>
																					<td class="letra7" width="10%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri0,2,',','.') ?></td>
																				</tr>
																			</table>
																			<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																			<?php
																			$AcuPri0=0; $AcuSeg0=0;
																		}
																	}
																}

																if ($_POST['rOrdRep']=='MONTO') {
																	for($j=0;$j<count($mDatos);$j++) {
																		$Total+=$mDatos[$j]['saldoxxx'];
																		$Saldo0=$mDatos[$j]['saldoxxx'];
																		$AcuPri0+=$Saldo0;
																		?>
																		<tr bgcolor="<?php echo $color ?>">
																			<td class="letra7" width="06%"><?php echo $mDatos[$j]['teridxxx'] ?></td>
																			<td class="letra7" width="20%"><?php echo substr($mDatos[$j]['clinomxx'],0,30) ?></td>
																			<td class="letra7" width="08%"><?php echo $mDatos[$j]['pucidxxx'] ?></td>
																			<td class="letra7" width="16%"><?php echo substr($mDatos[$j]['pucdesxx'],0,30) ?></td>
																			<td class="letra7" width="06%"><?php echo $mDatos[$j]['clitelxx'] ?></td>
																			<td class="letra7" width="10%"><?php echo $mDatos[$j]['document'] ?></td>
																			<td class="letra7" width="08%" align="center"><?php echo $mDatos[$j]['comfecxx'] ?></td>
																			<td class="letra7" width="08%" align="center"><?php echo $mDatos[$j]['comfecve'] ?></td>
																			<td class="letra7" width="08%" align="right"><?php echo $mDatos[$j]['diascart'] ?></td>
																			<td class="letra7" width="10%" align="right"><?php echo number_format($Saldo0,2,',','.') ?></td>
																		</tr>
																		<?php
																	}
																	?>
																	</table>
																	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																		<tr bgcolor="#FFE4C4">
																			<td class="letra7" width="90%" style="font-size:11px;font-weight:bold">SUBTOTAL <?php echo $mDatos[$j]['clinomxx'] ?></td>
																			<td class="letra7" width="10%" style="font-size:11px;font-weight:bold" align="right"><?php echo number_format($AcuPri0,2,',','.') ?></td>
																		</tr>
																	</table>
																	<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																	<?php
																}?>
																<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border-collapse: collapse; border-top:none; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
																	<tr>
																		<td class="letra7" width="70%" style="font-size:10px;font-weight:bold">TOTAL GENERAL </td>
																		<td class="letra7" width="30%" style="font-size:10px;font-weight:bold" align="right"><?php echo number_format($Total,0,',','.') ?></td>
																	</tr>
																</table>
																<?php
															} ?>
														</tr>
													</table>
												</fieldset>
											</td>
										</tr>
									</table>
								</center>
							<?php
						} else { $nSwitch= 1; $cMsj = "No se Generaron Registros.";  }
							?>
						</body>
					</html> <?php
				break;
				case 2:
					// PINTA POR EXCEL//
					if ($mDatos > 0) {

						$data = '';
						$cNomFile = "ESTADO_DE_CUENTAS_POR_".$_POST['rTipCta']."_".$_COOKIE['kUsrId'].date("YmdHis").".xls";

            if ($_SERVER["SERVER_PORT"] != "") {
              $cFile = f_Buscar_Niveles_Hasta_Opencomex(getcwd()) . $vSysStr['system_download_directory'] . "/" . $cNomFile;
            } else {
              $cFile = "{$OPENINIT['pathdr']}/opencomex/" . $vSysStr['system_download_directory'] . "/" . $cNomFile;
            }

						$fOp = fopen($cFile, 'a');

						if($_POST['rCarEda'] == "SI") {
							$data .= '<table width="2400" cellpadding="0" cellspacing="0" border="1" style="border-collapse: collapse; border: 1px solid black;">';
								$data .= '<tr>';
									$data .= '<td colspan="14" style="font-size:18px;font-weight:bold"><center>ANALISIS CUENTAS POR '.$_POST['rTipCta'].'</td>';
								$data .= '</tr>';
								$data .= '<tr>';
									$data .= '<td colspan="14"><b><center>'."CORTE A: ".$cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4).'</center></td>';
								$data .= '</tr>';
								$data .= '<tr>';
									$data .= '<td colspan="14"><b>FECHA Y HORA DE CONSULTA:</b> '.date('Y-m-d')."-".date('H:i:s').'(ORDENADO POR '.$_POST['rOrdRep'].')</td>';
								$data .= '</tr>';

							$titulo1=($_POST['nComVlr01']+1);
							$titulo2=($_POST['nComVlr02']+1);
							$titulo3=($_POST['nComVlr03']+1);

							$data .= '<tr style="font-weight:bold">';
								$data .= '<td width="80"><center>NIT</center></td>';
								$data .= '<td width="360"><center>NOMBRE</center></td>';
								$data .= '<td width="85"><center>CUENTA</center></td>';
								$data .= '<td width="250"><center>NOMBRE CUENTA</center></td>';
								$data .= '<td width="85"><center>TELEFONO</center></td>';
								$data .= '<td width="150"><center>DOCUMENTO</center></td>';
								$data .= '<td width="85"><center>FECHA DOCUMEN</center></td>';
								$data .= '<td width="85"><center>FECHA VENCIMI</center></td>';
								$data .= '<td width="70"><center>DIAS CARTERA</center></td>';
								$data .= '<td width="100"><center><= '.$_POST['nComVlr01'].'</center></td>';
								$data .= '<td width="100"><center>'.$titulo1.' - '.$_POST['nComVlr02'].'</center></td>';
								$data .= '<td width="100"><center>'.$titulo2.' - '.$_POST['nComVlr03'].'</center></td>';
								$data .= '<td width="100"><center>'.$titulo3.' - '.$_POST['nComVlr04'].'</center></td>';
								$data .= '<td width="100"><center>>'.$_POST['nComVlr04'].'</center></td>';
							$data .= '</tr>';

							$AcuPri0=0; $AcuPri1=0; $AcuPri2=0; $AcuPri3=0; $AcuPri4=0;
							$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
							$AcuTer0=0; $AcuTer1=0; $AcuTer2=0; $AcuTer3=0; $AcuTer4=0;

							if ($_POST['rOrdRep']=='NIT') {
								for($j=0;$j<count($mDatos);$j++) {
									$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;

									if ($mDatos[$j]['diascart'] <= $_POST['nComVlr01']) {
										$Saldo0=$mDatos[$j]['saldoxxx'];
										$AcuTer0+=$mDatos[$j]['saldoxxx'];
										$Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr01'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr02']) {
										$Saldo1=$mDatos[$j]['saldoxxx'];
										$AcuTer1+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr02'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr03']) {
										$Saldo2=$mDatos[$j]['saldoxxx'];
										$AcuTer2+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr03'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr04']) {
										$Saldo3=$mDatos[$j]['saldoxxx'];
										$AcuTer3+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr04']) {
										$Saldo4=$mDatos[$j]['saldoxxx'];
										$AcuTer4+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0;
									}

									$AcuPri0+=$Saldo0;
									$AcuPri1+=$Saldo1;
									$AcuPri2+=$Saldo2;
									$AcuPri3+=$Saldo3;
									$AcuPri4+=$Saldo4;

									$AcuSeg0+=$Saldo0;
									$AcuSeg1+=$Saldo1;
									$AcuSeg2+=$Saldo2;
									$AcuSeg3+=$Saldo3;
									$AcuSeg4+=$Saldo4;

									$data .= '<tr>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['teridxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['clinomxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucidxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucdesxx'].'</td>';
										$data .='<td style="font-size:13px" align="left">'.$mDatos[$j]['clitelxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['document'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecve'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['diascart'].'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo0,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo1,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo2,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo3,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo4,2,',','.').'</td>';
									$data .= '</tr>';									

									if ($mDatos[$j]['teridxxx'] == $mDatos[$j+1]['teridxxx']) {
										if ($mDatos[$j]['pucidxxx'] != $mDatos[$j+1]['pucidxxx']) {
											$data .= '<tr>';
												$data .= '<td bgcolor="#FFF8DC" colspan="09" style="font-size:13px;font-weight:bold">SUBTOTAL CUENTA '.$mDatos[$j]['pucdesxx'].'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg0,2,',','.').'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg1,2,',','.').'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg2,2,',','.').'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg3,2,',','.').'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg4,2,',','.').'</td>';
											$data .= '</tr>';

											$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
										}
									}	else {
										$data .= '<tr>';
											$data .= '<td bgcolor="#FFF8DC" colspan="09" style="font-size:13px;font-weight:bold">SUBTOTAL CUENTA '.$mDatos[$j]['pucdesxx'].'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg0,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg1,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg2,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg3,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg4,2,',','.').'</td>';
										$data .= '</tr>';
										$data .= '<tr>';
											$data .= '<td bgcolor="#FFE4C4" colspan="09" style="font-weight:bold">SUBTOTAL TERCERO '.$mDatos[$j]['clinomxx'].'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri0,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri1,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri2,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri3,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri4,2,',','.').'</td>';
										$data .= '</tr>';

										$AcuPri0=0; $AcuPri1=0; $AcuPri2=0; $AcuPri3=0; $AcuPri4=0;
										$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
									}
								}
							}

							if ($_POST['rOrdRep']=='CUENTA') {
								for($j=0;$j<count($mDatos);$j++) {
									$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;

									if ($mDatos[$j]['diascart'] <= $_POST['nComVlr01']) {
										$Saldo0=$mDatos[$j]['saldoxxx'];
										$AcuTer0+=$mDatos[$j]['saldoxxx'];
										$Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr01'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr02']) {
										$Saldo1=$mDatos[$j]['saldoxxx'];
										$AcuTer1+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr02'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr03']) {
										$Saldo2=$mDatos[$j]['saldoxxx'];
										$AcuTer2+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr03'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr04']) {
										$Saldo3=$mDatos[$j]['saldoxxx'];
										$AcuTer3+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr04']) {
										$Saldo4=$mDatos[$j]['saldoxxx'];
										$AcuTer4+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0;
									}

									$AcuPri0+=$Saldo0;
									$AcuPri1+=$Saldo1;
									$AcuPri2+=$Saldo2;
									$AcuPri3+=$Saldo3;
									$AcuPri4+=$Saldo4;

									$AcuSeg0+=$Saldo0;
									$AcuSeg1+=$Saldo1;
									$AcuSeg2+=$Saldo2;
									$AcuSeg3+=$Saldo3;
									$AcuSeg4+=$Saldo4;

									$data .= '<tr>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['teridxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['clinomxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucidxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucdesxx'].'</td>';
										$data .='<td style="font-size:13px" align="left">'.$mDatos[$j]['clitelxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['document'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecve'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['diascart'].'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo0,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo1,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo2,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo3,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo4,2,',','.').'</td>';
									$data .= '</tr>';

									if ($mDatos[$j]['pucidxxx'] == $mDatos[$j+1]['pucidxxx']) {
										if ($mDatos[$j]['teridxxx'] != $mDatos[$j+1]['teridxxx']) {
											$data .= '<tr>';
												$data .= '<td bgcolor="#FFF8DC" colspan="09" style="font-size:11px;font-weight:bold">SUBTOTAL TERCERO '.$mDatos[$j]['clinomxx'].'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:11px;font-weight:bold" align="right">'.number_format($AcuSeg0,2,',','.').'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:11px;font-weight:bold" align="right">'.number_format($AcuSeg1,2,',','.').'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:11px;font-weight:bold" align="right">'.number_format($AcuSeg2,2,',','.').'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:11px;font-weight:bold" align="right">'.number_format($AcuSeg3,2,',','.').'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:11px;font-weight:bold" align="right">'.number_format($AcuSeg4,2,',','.').'</td>';
											$data .= '</tr>';

											$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
										}
									}	else {
										$data .= '<tr>';
											$data .= '<td bgcolor="#FFF8DC" colspan="09" style="font-size:11px;font-weight:bold">SUBTOTAL TERCERO '.$mDatos[$j]['clinomxx'].'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:11px;font-weight:bold" align="right">'.number_format($AcuSeg0,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:11px;font-weight:bold" align="right">'.number_format($AcuSeg1,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:11px;font-weight:bold" align="right">'.number_format($AcuSeg2,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:11px;font-weight:bold" align="right">'.number_format($AcuSeg3,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:11px;font-weight:bold" align="right">'.number_format($AcuSeg4,2,',','.').'</td>';
										$data .= '</tr>';
										$data .= '<tr>';
											$data .= '<td bgcolor="#FFE4C4" colspan="09" style="font-weight:bold">SUBTOTAL CUENTA '.$mDatos[$j]['pucdesxx'].'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri0,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri1,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri2,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri3,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri4,2,',','.').'</td>';
										$data .= '</tr>';

										$AcuPri0=0; $AcuPri1=0; $AcuPri2=0; $AcuPri3=0; $AcuPri4=0;
										$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
									}
								}
							}

							if ($_POST['rOrdRep']=='ALFABETICO') {
								for($j=0;$j<count($mDatos);$j++) {
									$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;

									if ($mDatos[$j]['diascart'] <= $_POST['nComVlr01']) {
										$Saldo0=$mDatos[$j]['saldoxxx'];
										$AcuTer0+=$mDatos[$j]['saldoxxx'];
										$Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr01'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr02']) {
										$Saldo1=$mDatos[$j]['saldoxxx'];
										$AcuTer1+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr02'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr03']) {
										$Saldo2=$mDatos[$j]['saldoxxx'];
										$AcuTer2+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr03'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr04']) {
										$Saldo3=$mDatos[$j]['saldoxxx'];
										$AcuTer3+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr04']) {
										$Saldo4=$mDatos[$j]['saldoxxx'];
										$AcuTer4+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0;
									}

									$AcuPri0+=$Saldo0;
									$AcuPri1+=$Saldo1;
									$AcuPri2+=$Saldo2;
									$AcuPri3+=$Saldo3;
									$AcuPri4+=$Saldo4;

									$AcuSeg0+=$Saldo0;
									$AcuSeg1+=$Saldo1;
									$AcuSeg2+=$Saldo2;
									$AcuSeg3+=$Saldo3;
									$AcuSeg4+=$Saldo4;

									$data .= '<tr>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['teridxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['clinomxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucidxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucdesxx'].'</td>';
										$data .='<td style="font-size:13px" align="left">'.$mDatos[$j]['clitelxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['document'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecve'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['diascart'].'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo0,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo1,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo2,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo3,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo4,2,',','.').'</td>';
									$data .= '</tr>';

									if ($mDatos[$j]['clinomxx'] == $mDatos[$j+1]['clinomxx']) {
										if ($mDatos[$j]['pucidxxx'] != $mDatos[$j+1]['pucidxxx']) {
											$data .= '<tr>';
												$data .= '<td bgcolor="#FFF8DC" colspan="09" style="font-size:13px;font-weight:bold">SUBTOTAL CUENTA '.$mDatos[$j]['pucdesxx'].'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg0,2,',','.').'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg1,2,',','.').'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg2,2,',','.').'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg3,2,',','.').'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg4,2,',','.').'</td>';
											$data .= '</tr>';

											$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
										}
									}	else {
										$data .= '<tr>';
											$data .= '<td bgcolor="#FFF8DC" colspan="09" style="font-size:13px;font-weight:bold">SUBTOTAL CUENTA '.$mDatos[$j]['pucdesxx'].'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg0,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg1,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg2,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg3,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg4,2,',','.').'</td>';
										$data .= '</tr>';
										$data .= '<tr>';
											$data .= '<td bgcolor="#FFE4C4" colspan="09" style="font-weight:bold">SUBTOTAL TERCERO '.$mDatos[$j]['clinomxx'].'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri0,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri1,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri2,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri3,2,',','.').'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri4,2,',','.').'</td>';
										$data .= '</tr>';

										$AcuPri0=0; $AcuPri1=0; $AcuPri2=0; $AcuPri3=0; $AcuPri4=0;
										$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
									}
								}
							}

							if ($_POST['rOrdRep']=='MONTO') {
								for($j=0;$j<count($mDatos);$j++) {
									$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;

									if ($mDatos[$j]['diascart'] <= $_POST['nComVlr01']) {
										$Saldo0=$mDatos[$j]['saldoxxx'];
										$AcuTer0+=$mDatos[$j]['saldoxxx'];
										$Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr01'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr02']) {
										$Saldo1=$mDatos[$j]['saldoxxx'];
										$AcuTer1+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr02'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr03']) {
										$Saldo2=$mDatos[$j]['saldoxxx'];
										$AcuTer2+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr03'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr04']) {
										$Saldo3=$mDatos[$j]['saldoxxx'];
										$AcuTer3+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr04']) {
										$Saldo4=$mDatos[$j]['saldoxxx'];
										$AcuTer4+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0;
									}

									$AcuPri0+=$Saldo0;
									$AcuPri1+=$Saldo1;
									$AcuPri2+=$Saldo2;
									$AcuPri3+=$Saldo3;
									$AcuPri4+=$Saldo4;

									$AcuSeg0+=$Saldo0;
									$AcuSeg1+=$Saldo1;
									$AcuSeg2+=$Saldo2;
									$AcuSeg3+=$Saldo3;
									$AcuSeg4+=$Saldo4;

									$data .= '<tr>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['teridxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['clinomxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucidxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucdesxx'].'</td>';
										$data .='<td style="font-size:13px" align="left">'.$mDatos[$j]['clitelxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['document'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecve'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['diascart'].'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo0,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo1,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo2,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo3,2,',','.').'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo4,2,',','.').'</td>';
									$data .= '</tr>';
								}
									$data .= '<tr>';
										$data .= '<td bgcolor="#FFE4C4" colspan="09" style="font-weight:bold">SUBTOTAL '.$mDatos[$j]['clinomxx'].'</td>';
										$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri0,2,',','.').'</td>';
										$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri1,2,',','.').'</td>';
										$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri2,2,',','.').'</td>';
										$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri3,2,',','.').'</td>';
										$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri4,2,',','.').'</td>';
									$data .= '</tr>';
							}

							$data .= '<tr>';
								$data .= '<td colspan="09" style="font-weight:bold">TOTAL GENERAL</td>';
								$data .= '<td colspan="01" style="font-weight:bold" align="right">'.number_format($AcuTer0,0,',','.').'</td>';
								$data .= '<td colspan="01" style="font-weight:bold" align="right">'.number_format($AcuTer1,0,',','.').'</td>';
								$data .= '<td colspan="01" style="font-weight:bold" align="right">'.number_format($AcuTer2,0,',','.').'</td>';
								$data .= '<td colspan="01" style="font-weight:bold" align="right">'.number_format($AcuTer3,0,',','.').'</td>';
								$data .= '<td colspan="01" style="font-weight:bold" align="right">'.number_format($AcuTer4,0,',','.').'</td>';
							$data .= '</tr>';
						}
						$data .= '</table>';

						if ($_POST['rCarEda'] == "NO") {
							$Total=0; $Saldo0=0; $AcuPri0=0; $AcuSeg0=0;
							$data .= '<table width="2400" cellpadding="0" cellspacing="0" border="1" style="border-collapse: collapse; border: 1px solid black;">';
								$data .= '<tr>';
									$data .= '<td colspan="10" style="font-size:18px;font-weight:bold"><center>ANALISIS CUENTAS POR '.$_POST['rTipCta'].'</td>';
								$data .= '</tr>';
								$data .= '<tr>';
									$data .= '<td colspan="10"><b><center>'."CORTE A: ".$cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4).'</center></td>';
								$data .= '</tr>';
								$data .= '<tr>';
									$data .= '<td colspan="10"><b>FECHA Y HORA DE CONSULTA:</b> '.date('Y-m-d')."-".date('H:i:s').'(ORDENADO POR '.$_POST['rOrdRep'].')</td>';
								$data .= '</tr>';
								$data .= '<tr style="font-weight:bold">';
									$data .= '<td width="80"><center>NIT</center></td>';
									$data .= '<td width="360"><center>NOMBRE</center></td>';
									$data .= '<td width="85"><center>CUENTA</center></td>';
									$data .= '<td width="250"><center>NOMBRE CUENTA</center></td>';
									$data .= '<td width="85"><center>TELEFONO</center></td>';
									$data .= '<td width="150"><center>DOCUMENTO</center></td>';
									$data .= '<td width="85"><center>FECHA DOCUMEN</center></td>';
									$data .= '<td width="85"><center>FECHA VENCIMI</center></td>';
									$data .= '<td width="70"><center>DIAS CARTERA</center></td>';
									$data .= '<td width="100"><center>SALDO</center></td>';
								$data .= '</tr>';

							$AcuPri0=0; $AcuSeg0=0;

							if ($_POST['rOrdRep']=='NIT') {
								for($j=0;$j<count($mDatos);$j++) {
									$Total+=$mDatos[$j]['saldoxxx'];
									$Saldo0=$mDatos[$j]['saldoxxx'];
									$AcuPri0+=$Saldo0;
									$AcuSeg0+=$Saldo0;

									$data .= '<tr>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['teridxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['clinomxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucidxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucdesxx'].'</td>';
										$data .='<td style="font-size:13px" align="left">'.$mDatos[$j]['clitelxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['document'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecve'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['diascart'].'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo0,2,',','.').'</td>';
									$data .= '</tr>';

									if ($mDatos[$j]['teridxxx'] == $mDatos[$j+1]['teridxxx']) {
										if ($mDatos[$j]['pucidxxx'] != $mDatos[$j+1]['pucidxxx']) {
											$data .= '<tr>';
												$data .= '<td bgcolor="#FFF8DC" colspan="09" style="font-size:13px;font-weight:bold">SUBTOTAL CUENTA '.$mDatos[$j]['pucdesxx'].'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg0,2,',','.').'</td>';
											$data .= '</tr>';
											$AcuSeg0=0;
										}
									}	else {
										$data .= '<tr>';
											$data .= '<td bgcolor="#FFF8DC" colspan="09" style="font-size:13px;font-weight:bold">SUBTOTAL CUENTA '.$mDatos[$j]['pucdesxx'].'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg0,2,',','.').'</td>';
										$data .= '</tr>';
										$data .= '<tr>';
											$data .= '<td bgcolor="#FFE4C4" colspan="09" style="font-weight:bold">SUBTOTAL TERCERO '.$mDatos[$j]['clinomxx'].'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri0,2,',','.').'</td>';
										$data .= '</tr>';
										$AcuPri0=0;$AcuSeg0=0;
									}
								}
							}

							if ($_POST['rOrdRep']=='CUENTA') {
								for($j=0;$j<count($mDatos);$j++) {
									$Total+=$mDatos[$j]['saldoxxx'];
									$Saldo0=$mDatos[$j]['saldoxxx'];
									$AcuPri0+=$Saldo0;
									$AcuSeg0+=$Saldo0;

									$data .= '<tr>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['teridxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['clinomxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucidxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucdesxx'].'</td>';
										$data .='<td style="font-size:13px" align="left">'.$mDatos[$j]['clitelxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['document'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecve'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['diascart'].'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo0,2,',','.').'</td>';
									$data .= '</tr>';

									if ($mDatos[$j]['pucidxxx'] == $mDatos[$j+1]['pucidxxx']) {
										if ($mDatos[$j]['teridxxx'] != $mDatos[$j+1]['teridxxx']) {
											$data .= '<tr>';
												$data .= '<td bgcolor="#FFF8DC" colspan="09" style="font-size:13px;font-weight:bold">SUBTOTAL TERCERO '.$mDatos[$j]['clinomxx'].'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg0,2,',','.').'</td>';
											$data .= '</tr>';
											$AcuSeg0=0;
										}
									}	else {
										$data .= '<tr>';
											$data .= '<td bgcolor="#FFF8DC" colspan="09" style="font-size:13px;font-weight:bold">SUBTOTAL TERCERO '.$mDatos[$j]['clinomxx'].'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg0,2,',','.').'</td>';
										$data .= '</tr>';
										$data .= '<tr>';
											$data .= '<td bgcolor="#FFE4C4" colspan="09" style="font-weight:bold">SUBTOTAL CUENTA '.$mDatos[$j]['pucdesxx'].'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri0,2,',','.').'</td>';
										$data .= '</tr>';
										$AcuPri0=0; $AcuSeg0=0;
									}
								}
							}

							if ($_POST['rOrdRep']=='ALFABETICO') {
								for($j=0;$j<count($mDatos);$j++) {
									$Total+=$mDatos[$j]['saldoxxx'];
									$Saldo0=$mDatos[$j]['saldoxxx'];
									$AcuPri0+=$Saldo0;
									$AcuSeg0+=$Saldo0;

									$data .= '<tr>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['teridxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['clinomxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucidxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucdesxx'].'</td>';
										$data .='<td style="font-size:13px" align="left">'.$mDatos[$j]['clitelxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['document'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecve'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['diascart'].'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo0,2,',','.').'</td>';
									$data .= '</tr>';

									if ($mDatos[$j]['clinomxx'] == $mDatos[$j+1]['clinomxx']) {
										if ($mDatos[$j]['pucidxxx'] != $mDatos[$j+1]['pucidxxx']) {
											$data .= '<tr>';
												$data .= '<td bgcolor="#FFF8DC" colspan="09" style="font-size:13px;font-weight:bold">SUBTOTAL CUENTA '.$mDatos[$j]['pucdesxx'].'</td>';
												$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg0,2,',','.').'</td>';
											$data .= '</tr>';
											$AcuSeg0=0;
										}
									}	else {
										$data .= '<tr>';
											$data .= '<td bgcolor="#FFF8DC" colspan="09" style="font-size:13px;font-weight:bold">SUBTOTAL CUENTA '.$mDatos[$j]['pucdesxx'].'</td>';
											$data .= '<td bgcolor="#FFF8DC" colspan="01" style="font-size:13px;font-weight:bold" align="right">'.number_format($AcuSeg0,2,',','.').'</td>';
										$data .= '</tr>';
										$data .= '<tr>';
											$data .= '<td bgcolor="#FFE4C4" colspan="09" style="font-weight:bold">SUBTOTAL TERCERO '.$mDatos[$j]['clinomxx'].'</td>';
											$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri0,2,',','.').'</td>';
										$data .= '</tr>';
										$AcuPri0=0; $AcuSeg0=0;
									}
								}
							}

							if ($_POST['rOrdRep']=='MONTO') {
								for($j=0;$j<count($mDatos);$j++) {
									$Total+=$mDatos[$j]['saldoxxx'];
									$Saldo0=$mDatos[$j]['saldoxxx'];
									$AcuPri0+=$Saldo0;
									$AcuSeg0+=$Saldo0;

									$data .= '<tr>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['teridxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['clinomxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucidxxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['pucdesxx'].'</td>';
										$data .='<td style="font-size:13px" align="left">'.$mDatos[$j]['clitelxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['document'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecxx'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['comfecve'].'</td>';
										$data .='<td style="font-size:13px">'.$mDatos[$j]['diascart'].'</td>';
										$data .='<td style="font-size:13px" align="right">'.number_format($Saldo0,2,',','.').'</td>';
									$data .= '</tr>';
								}
								$data .= '<tr>';
									$data .= '<td bgcolor="#FFE4C4" colspan="09" style="font-weight:bold">SUBTOTAL '.$mDatos[$j]['clinomxx'].'</td>';
									$data .= '<td bgcolor="#FFE4C4" colspan="01" style="font-weight:bold" align="right">'.number_format($AcuPri0,2,',','.').'</td>';
								$data .= '</tr>';
							}

							$data .= '<tr>';
								$data .= '<td colspan="09" style="font-weight:bold">TOTAL GENERAL</td>';
								$data .= '<td colspan="01" style="font-weight:bold" align="right">'.number_format($Total,0,',','.').'</td>';
							$data .= '</tr>';
						}
						$data .= '</table>';
						fwrite($fOp, $data);
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

					} else { 
						f_mensaje(__FILE__,__LINE__,"No se Generaron Registros.");  
					}
				break;
				case 3:
					if ($mDatos >= 0) {
						$cRoot = $_SERVER['DOCUMENT_ROOT'];

						define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].$cSystem_Fonts_Directory.'/');
						require($_SERVER['DOCUMENT_ROOT'].$cSystem_Class_Directory.'/fpdf/fpdf.php');

						class PDF extends FPDF {
							function Header() {
								global $cRoot; global $cPlesk_Skin_Directory;
								global $cAlfa; global $cMes; global $fec; global $cTerId; global $nPag;

								if ($cAlfa == "DEINTERLOG" || $cAlfa == "TEPRUEBASX") {
									$this->SetXY(13,7);
									$this->Cell(42,28,'',1,0,'C');
									$this->Cell(213,28,'',1,0,'C');

									$this->Image($cRoot.$cPlesk_Skin_Directory.'/MaryAire.jpg',14,8,40,25);

									$this->SetFont('verdana','',16);
									$this->SetXY(55,15);
									$this->Cell(213,8,"ESTADO DE CUENTAS POR {$_POST['rTipCta']}",0,0,'C');
									$this->Ln(8);
									$this->SetFont('verdana','',12);
									$this->SetX(55);
									$this->Cell(213,6,"CORTE A: ".$cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4)."  "."(ORDENADO POR {$_POST['rOrdRep']})",0,0,'C');
									$this->Ln(15);
									$this->SetX(13);
								} else {

									##Impresión de Logo de ADIMPEX en la parte superior derecha##
									switch($cAlfa){
										case "TEADIMPEXX": // ADIMPEX
										case "DEADIMPEXX": // ADIMPEX
										case "ADIMPEXX": // ADIMPEX
											$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoadimpex5.jpg',255,00,25,20);
										break;
										default:
											// No hace nada
										break;
									}
									##Fin Impresión de Logo de ADIMPEX en la parte superior derecha##

									$this->SetXY(13,7);
									$this->Cell(255,15,'',1,0,'C');

									switch ($cAlfa) {
										case 'ADUANAMO':
										case 'DEADUANAMO':
										case 'TEADUANAMO':
											$this->Image($cRoot.$cPlesk_Skin_Directory.'/logo_aduanamo.jpg',14,8,30,'');
										break;
										case "LOGINCAR":
										case "DELOGINCAR":
										case "TELOGINCAR":
											$this->Image($cRoot.$cPlesk_Skin_Directory.'/Logo_Login_Cargo_Ltda_2.jpg',14,8,43,13);
										break;
										case "TRLXXXXX":
										case "DETRLXXXXX":
										case "TETRLXXXXX":
											$this->Image($cRoot.$cPlesk_Skin_Directory.'/logobma.jpg',14,8,17,13);
										break;
										case "TEADIMPEXX": // ADIMPEX
										case "DEADIMPEXX": // ADIMPEX
										case "ADIMPEXX": // ADIMPEX
											$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoadimpex4.jpg',17,10,36,8);
										break;
										case "ROLDANLO"://ROLDAN
										case "TEROLDANLO"://ROLDAN
										case "DEROLDANLO"://ROLDAN
											$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoroldan.png',14,8,40,13);
										break;
										case "CASTANOX":
										case "DECASTANOX":
										case "TECASTANOX":
											$this->Image($cRoot.$cPlesk_Skin_Directory.'/logomartcam.jpg',14,8,29,13);
										break;
										case "ALMACAFE": //ALMACAFE
										case "TEALMACAFE": //ALMACAFE
										case "DEALMACAFE": //ALMACAFE
											$this->Image($cRoot.$cPlesk_Skin_Directory.'/logoalmacafe.jpg',14,8,29,13);
										break;
										case "GRUMALCO"://GRUMALCO
										case "TEGRUMALCO"://GRUMALCO
										case "DEGRUMALCO"://GRUMALCO
											$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logomalco.jpg',14,8,40,13);
										break;
										case "DHLEXPRE": //DHLEXPRE
										case "TEDHLEXPRE": //DHLEXPRE
										case "DEDHLEXPRE": //DHLEXPRE
											$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logo_dhl_express.jpg',14,8,40,13);
										break;
										case "ALADUANA"://ALADUANA
										case "TEALADUANA"://ALADUANA
										case "DEALADUANA"://ALADUANA
											$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaladuana.jpg',16,8,28,13);
										break;
										case "ANDINOSX"://ANDINOSX
										case "TEANDINOSX"://ANDINOSX
										case "DEANDINOSX"://ANDINOSX
											$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoandinos.jpg', 15, 8, 30, 13);
											break;
										case "GRUPOALC"://GRUPOALC
										case "TEGRUPOALC"://GRUPOALC
										case "DEGRUPOALC"://GRUPOALC
											$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoalc.jpg',16,8,28,13);
										break;
										case "AAINTERX"://AAINTERX
										case "TEAAINTERX"://AAINTERX
										case "DEAAINTERX"://AAINTERX
											$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointernacional.jpg',16,8,28,13);
										break;
										case "AALOPEZX":
										case "TEAALOPEZX":
										case "DEAALOPEZX":
											$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoaalopez.png', 16, 8,26);
										break;
										case "ADUAMARX"://ADUAMARX
										case "TEADUAMARX"://ADUAMARX
										case "DEADUAMARX"://ADUAMARX
											$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logoaduamar.jpg',16,8,13);
										break;
										case "SOLUCION"://SOLUCION
										case "TESOLUCION"://SOLUCION
										case "DESOLUCION"://SOLUCION
											$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logosoluciones.jpg',16,8,30);
										break;
										case "FENIXSAS"://FENIXSAS
										case "TEFENIXSAS"://FENIXSAS
										case "DEFENIXSAS"://FENIXSAS
											$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logofenix.jpg',16,10,36);
										break;
										case "COLVANXX"://COLVANXX
										case "TECOLVANXX"://COLVANXX
										case "DECOLVANXX"://COLVANXX
											$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logocolvan.jpg',16,8,32);
										break;
										case "INTERLAC"://INTERLAC
										case "TEINTERLAC"://INTERLAC
										case "DEINTERLAC"://INTERLAC
											$this->Image($_SERVER['DOCUMENT_ROOT'].$cPlesk_Skin_Directory.'/logointerlace.jpg',16,8,28);
										break;
										case "KARGORUX": //KARGORUX
										case "TEKARGORUX": //KARGORUX
										case "DEKARGORUX": //KARGORUX
											$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logokargoru.jpg', 16, 8.5, 28);
										break;
                    case "ALOGISAS": //LOGISTICA
                    case "TEALOGISAS": //LOGISTICA
                    case "DEALOGISAS": //LOGISTICA
                      $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logologisticasas.jpg', 16, 8, 32);
                    break;
                    case "PROSERCO": //PROSERCO
                    case "TEPROSERCO": //PROSERCO
                    case "DEPROSERCO": //PROSERCO
                      $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoproserco.png', 16, 8, 23);
                    break;
                    case "MANATIAL": //MANATIAL
                    case "TEMANATIAL": //MANATIAL
                    case "DEMANATIAL": //MANATIAL
                      $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomanantial.jpg', 16, 9, 45, 10);
                    break;
                    case "DSVSASXX":   //DSVSAS
                    case "DEDSVSASXX": //DSVSAS
                    case "TEDSVSASXX": //DSVSAS
                      $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logodsv.jpg', 16, 9.5, 45, 10);
                    break;
                    case "MELYAKXX":    //MELYAK
                    case "DEMELYAKXX":  //MELYAK
                    case "TEMELYAKXX":  //MELYAK
                      $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logomelyak.jpg', 16, 9.5, 40, 10);
                    break;
                    case "FEDEXEXP":    //FEDEX
                    case "DEFEDEXEXP":  //FEDEX
                    case "TEFEDEXEXP":  //FEDEX
                      $this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logofedexexp.jpg', 16, 9.5, 30, 15);
                    break;
										case "EXPORCOM":    //EXPORCOMEX
										case "DEEXPORCOM":  //EXPORCOMEX
										case "TEEXPORCOM":  //EXPORCOMEX
											$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logoexporcomex.jpg', 16, 8.5, 25, 12);
										break;
										case "HAYDEARX":   //HAYDEARX
										case "DEHAYDEARX": //HAYDEARX
										case "TEHAYDEARX": //HAYDEARX
											$this->Image($_SERVER['DOCUMENT_ROOT'] . $cPlesk_Skin_Directory . '/logohaydear.jpeg', 16, 8.5, 40, 12);
										break;
									}

									$this->SetFont('verdana','',16);
									$this->SetXY(13,8);
									$this->Cell(255,8,"ESTADO DE CUENTAS POR {$_POST['rTipCta']}",0,0,'C');
									$this->Ln(8);
									$this->SetFont('verdana','',12);
									$this->SetX(13);
									$this->Cell(255,6,"CORTE A: ".$cMes." ".substr($fec,8,2)." DE ".substr($fec,0,4)."  "."(ORDENADO POR {$_POST['rOrdRep']})",0,0,'C');
									$this->Ln(10);
									$this->SetX(13);
								}

								if ($this->PageNo() > 1 && $nPag ==1) {
									if($_POST['rCarEda'] == "SI") {
										$titulo1=($_POST['nComVlr01']+1);
										$titulo2=($_POST['nComVlr02']+1);
										$titulo3=($_POST['nComVlr03']+1);

										$pdf->SetWidths(array('13','37','15','30','15','22','15','15','8','17','17','17','17','17'));
										$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
										$pdf->SetX(13);
										$pdf->Row(array("Nit",
																		"Nombre",
																		"Cuenta",
																		"Nombre Cuenta",
																		"Telefono",
																		"Documento",
																		"Fecha Doc",
																		"Fecha Ven",
																		"Dias Car",
																		"<= {$_POST['nComVlr01']} ",
																		"$titulo1 - {$_POST['nComVlr02']}",
																		"$titulo2 - {$_POST['nComVlr03']}",
																		"$titulo3 - {$_POST['nComVlr04']}",
																		"> {$_POST['nComVlr04']}"));
										$pdf->SetFont('verdana','',5);
										$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R','R','R','R','R'));
									}

									if($_POST['rCarEda'] == "NO") {
										$titulo1=($_POST['nComVlr01']+1);
										$titulo2=($_POST['nComVlr02']+1);
										$titulo3=($_POST['nComVlr02']+1);

										$pdf->SetWidths(array('20','20','20','20','20','10','20','20','10','20'));
										$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C',));
										$pdf->SetX(13);
										$pdf->Row(array("Nit",
																		"Nombre",
																		"Cuenta",
																		"Nombre Cuenta",
																		"Telefono",
																		"Documento",
																		"Fecha Doc",
																		"Fecha Ven",
																		"Dias Car",
																		"Saldo"));
										$pdf->SetFont('verdana','',7);
										$pdf->SetAligns(array('C','L','C','C','C','C','C','L','C','R'));
									}
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
								//f_mensaje(__FILE__,__LINE__,count($data));
								for($i=0;$i<count($data);$i++) {
									$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
									$h=4*$nb;
								}
								//Issue a page break first if needed
								$this->CheckPageBreak($h);
								//Draw the cells of the row
								for($i=0;$i<count($data);$i++) {
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
						$pdf->Ln(1);
						$pdf->SetFont('verdana','B',6);

						if($_POST['rCarEda'] == "SI") {

							$titulo1=($_POST['nComVlr01']+1);
							$titulo2=($_POST['nComVlr02']+1);
							$titulo3=($_POST['nComVlr03']+1);

							$AcuPri0=0; $AcuPri1=0; $AcuPri2=0; $AcuPri3=0; $AcuPri4=0;
							$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;
							$AcuTer0=0; $AcuTer1=0; $AcuTer2=0; $AcuTer3=0; $AcuTer4=0;

							$pdf->SetWidths(array('13','37','15','30','15','22','15','15','8','17','17','17','17','17'));
							$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
							$pdf->SetX(13);
							$pdf->Row(array("Nit",
															"Nombre",
															"Cuenta",
															"Nombre Cuenta",
															"Telefono",
															"Documento",
															"Fecha Doc",
															"Fecha Ven",
															"Dias Cart",
															"<= {$_POST['nComVlr01']} ",
															"$titulo1 - {$_POST['nComVlr02']}",
															"$titulo2 - {$_POST['nComVlr03']}",
															"$titulo3 - {$_POST['nComVlr04']}",
															"> {$_POST['nComVlr04']}"));
							$pdf->SetFont('verdana','',5);
							$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R','R','R','R','R'));

							if ($_POST['rOrdRep']=='NIT') {
								for($j=0;$j<count($mDatos);$j++) {
									$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;

									if ($mDatos[$j]['diascart'] <= $_POST['nComVlr01']) {
										$Saldo0=$mDatos[$j]['saldoxxx'];
										$AcuTer0+=$mDatos[$j]['saldoxxx'];
										$Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr01'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr02']) {
										$Saldo1=$mDatos[$j]['saldoxxx'];
										$AcuTer1+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr02'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr03']) {
										$Saldo2=$mDatos[$j]['saldoxxx'];
										$AcuTer2+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr03'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr04']) {
										$Saldo3=$mDatos[$j]['saldoxxx'];
										$AcuTer3+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr04']) {
										$Saldo4=$mDatos[$j]['saldoxxx'];
										$AcuTer4+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0;
									}

									$AcuPri0+=$Saldo0;
									$AcuPri1+=$Saldo1;
									$AcuPri2+=$Saldo2;
									$AcuPri3+=$Saldo3;
									$AcuPri4+=$Saldo4;

									$AcuSeg0+=$Saldo0;
									$AcuSeg1+=$Saldo1;
									$AcuSeg2+=$Saldo2;
									$AcuSeg3+=$Saldo3;
									$AcuSeg4+=$Saldo4;

									$pdf->SetFont('verdana','',5);
									$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R','R','R','R','R'));

									$pdf->SetX(13);
									$pdf->Row(array($mDatos[$j]['teridxxx'],
																	substr($mDatos[$j]['clinomxx'],0,30),
																	$mDatos[$j]['pucidxxx'],
																	substr($mDatos[$j]['pucdesxx'],0,23),
																	$mDatos[$j]['clitelxx'],
																	$mDatos[$j]['document'],
																	$mDatos[$j]['comfecxx'],
																	$mDatos[$j]['comfecve'],
																	$mDatos[$j]['diascart'],
																	number_format($Saldo0,2,',','.'),
																	number_format($Saldo1,2,',','.'),
																	number_format($Saldo2,2,',','.'),
																	number_format($Saldo3,2,',','.'),
																	number_format($Saldo4,2,',','.')));

									if ($mDatos[$j]['teridxxx'] == $mDatos[$j+1]['teridxxx']) {
										if ($mDatos[$j]['pucidxxx'] != $mDatos[$j+1]['pucidxxx']) {
											$pdf->SetX(13);
											$pdf->SetFont('verdana','B',5);
											$pdf->Cell(170,3,"   SUBTOTAL CUENTA:".$mDatos[$j]['pucdesxx'],0,0,'L');
											$pdf->Cell(17,3,number_format($AcuSeg0,2,',','.'),0,0,'R');
											$pdf->Cell(17,3,number_format($AcuSeg1,2,',','.'),0,0,'R');
											$pdf->Cell(17,3,number_format($AcuSeg2,2,',','.'),0,0,'R');
											$pdf->Cell(17,3,number_format($AcuSeg3,2,',','.'),0,0,'R');
											$pdf->Cell(17,3,number_format($AcuSeg4,2,',','.'),0,0,'R');

											$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;

											$pdf->SetFont('verdana','',5);
											$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R','R','R','R','R'));
											$pdf->Ln(4);
										}
									}	else {
										$pdf->Ln(2);
										$pdf->SetX(13);
										$pdf->SetFont('verdana','B',5);
										$pdf->Cell(170,3,"   SUBTOTAL CUENTA:".$mDatos[$j]['pucdesxx'],0,0,'L');
										$pdf->Cell(17,3,number_format($AcuSeg0,2,',','.'),0,0,'R');
										$pdf->Cell(17,3,number_format($AcuSeg1,2,',','.'),0,0,'R');
										$pdf->Cell(17,3,number_format($AcuSeg2,2,',','.'),0,0,'R');
										$pdf->Cell(17,3,number_format($AcuSeg3,2,',','.'),0,0,'R');
										$pdf->Cell(17,3,number_format($AcuSeg4,2,',','.'),0,0,'R');

										$pdf->Ln(3);
										$pdf->SetX(13);
										$pdf->SetFont('verdana','B',6);
										$pdf->Cell(170,4,"SUBTOTAL TERCERO:".$mDatos[$j]['clinomxx'],0,0,'L');
										$pdf->Cell(17,4,number_format($AcuPri0,2,',','.'),0,0,'R');
										$pdf->Cell(17,4,number_format($AcuPri1,2,',','.'),0,0,'R');
										$pdf->Cell(17,4,number_format($AcuPri2,2,',','.'),0,0,'R');
										$pdf->Cell(17,4,number_format($AcuPri3,2,',','.'),0,0,'R');
										$pdf->Cell(17,4,number_format($AcuPri4,2,',','.'),0,0,'R');
										$pdf->Ln(4);
										$AcuPri0=0; $AcuPri1=0; $AcuPri2=0; $AcuPri3=0; $AcuPri4=0;
										$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;

										$pdf->SetFont('verdana','',5);
										$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R','R','R','R','R'));
										$pdf->Ln(2);
									}
								}
							}

							if ($_POST['rOrdRep']=='CUENTA') {
								for($j=0;$j<count($mDatos);$j++) {
									$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;

									if ($mDatos[$j]['diascart'] <= $_POST['nComVlr01']) {
										$Saldo0=$mDatos[$j]['saldoxxx'];
										$AcuTer0+=$mDatos[$j]['saldoxxx'];
										$Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr01'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr02']) {
										$Saldo1=$mDatos[$j]['saldoxxx'];
										$AcuTer1+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr02'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr03']) {
										$Saldo2=$mDatos[$j]['saldoxxx'];
										$AcuTer2+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr03'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr04']) {
										$Saldo3=$mDatos[$j]['saldoxxx'];
										$AcuTer3+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr04']) {
										$Saldo4=$mDatos[$j]['saldoxxx'];
										$AcuTer4+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0;
									}

									$AcuPri0+=$Saldo0;
									$AcuPri1+=$Saldo1;
									$AcuPri2+=$Saldo2;
									$AcuPri3+=$Saldo3;
									$AcuPri4+=$Saldo4;

									$AcuSeg0+=$Saldo0;
									$AcuSeg1+=$Saldo1;
									$AcuSeg2+=$Saldo2;
									$AcuSeg3+=$Saldo3;
									$AcuSeg4+=$Saldo4;

									$pdf->SetFont('verdana','',5);
									$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R','R','R','R','R'));

									$pdf->SetX(13);
									$pdf->Row(array($mDatos[$j]['teridxxx'],
																	substr($mDatos[$j]['clinomxx'],0,30),
																	$mDatos[$j]['pucidxxx'],
																	substr($mDatos[$j]['pucdesxx'],0,23),
																	$mDatos[$j]['clitelxx'],
																	$mDatos[$j]['document'],
																	$mDatos[$j]['comfecxx'],
																	$mDatos[$j]['comfecve'],
																	$mDatos[$j]['diascart'],
																	number_format($Saldo0,2,',','.'),
																	number_format($Saldo1,2,',','.'),
																	number_format($Saldo2,2,',','.'),
																	number_format($Saldo3,2,',','.'),
																	number_format($Saldo4,2,',','.')));

									if ($mDatos[$j]['pucidxxx'] == $mDatos[$j+1]['pucidxxx']) {
										if ($mDatos[$j]['teridxxx'] != $mDatos[$j+1]['teridxxx']) {
											$pdf->SetX(13);
											$pdf->SetFont('verdana','B',5);
											$pdf->Cell(170,3,"   SUBTOTAL TERCERO:".$mDatos[$j]['clinomxx'],0,0,'L');
											$pdf->Cell(17,3,number_format($AcuSeg0,2,',','.'),0,0,'R');
											$pdf->Cell(17,3,number_format($AcuSeg1,2,',','.'),0,0,'R');
											$pdf->Cell(17,3,number_format($AcuSeg2,2,',','.'),0,0,'R');
											$pdf->Cell(17,3,number_format($AcuSeg3,2,',','.'),0,0,'R');
											$pdf->Cell(17,3,number_format($AcuSeg4,2,',','.'),0,0,'R');

											$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;

											$pdf->SetFont('verdana','',5);
											$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R','R','R','R','R'));
											$pdf->Ln(5);
										}
									}	else {
										$pdf->Ln(2);
										$pdf->SetX(13);
										$pdf->SetFont('verdana','B',5);
										$pdf->Cell(170,3,"   SUBTOTAL TERCERO:".$mDatos[$j]['clinomxx'],0,0,'L');
										$pdf->Cell(17,3,number_format($AcuSeg0,2,',','.'),0,0,'R');
										$pdf->Cell(17,3,number_format($AcuSeg1,2,',','.'),0,0,'R');
										$pdf->Cell(17,3,number_format($AcuSeg2,2,',','.'),0,0,'R');
										$pdf->Cell(17,3,number_format($AcuSeg3,2,',','.'),0,0,'R');
										$pdf->Cell(17,3,number_format($AcuSeg4,2,',','.'),0,0,'R');

										$pdf->Ln(3);
										$pdf->SetX(13);
										$pdf->SetFont('verdana','B',6);
										$pdf->Cell(170,4,"SUBTOTAL CUENTA:".$mDatos[$j]['pucdesxx'],0,0,'L');
										$pdf->Cell(17,4,number_format($AcuPri0,2,',','.'),0,0,'R');
										$pdf->Cell(17,4,number_format($AcuPri1,2,',','.'),0,0,'R');
										$pdf->Cell(17,4,number_format($AcuPri2,2,',','.'),0,0,'R');
										$pdf->Cell(17,4,number_format($AcuPri3,2,',','.'),0,0,'R');
										$pdf->Cell(17,4,number_format($AcuPri4,2,',','.'),0,0,'R');
										$pdf->Ln(4);
										$AcuPri0=0; $AcuPri1=0; $AcuPri2=0; $AcuPri3=0; $AcuPri4=0;
										$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;

										$pdf->SetFont('verdana','',5);
										$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R','R','R','R','R'));
										$pdf->Ln(2);
									}
								}
							}

							if ($_POST['rOrdRep']=='ALFABETICO') {
								for($j=0;$j<count($mDatos);$j++) {
									$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;

									if ($mDatos[$j]['diascart'] <= $_POST['nComVlr01']) {
										$Saldo0=$mDatos[$j]['saldoxxx'];
										$AcuTer0+=$mDatos[$j]['saldoxxx'];
										$Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr01'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr02']) {
										$Saldo1=$mDatos[$j]['saldoxxx'];
										$AcuTer1+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr02'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr03']) {
										$Saldo2=$mDatos[$j]['saldoxxx'];
										$AcuTer2+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr03'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr04']) {
										$Saldo3=$mDatos[$j]['saldoxxx'];
										$AcuTer3+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr04']) {
										$Saldo4=$mDatos[$j]['saldoxxx'];
										$AcuTer4+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0;
									}

									$AcuPri0+=$Saldo0;
									$AcuPri1+=$Saldo1;
									$AcuPri2+=$Saldo2;
									$AcuPri3+=$Saldo3;
									$AcuPri4+=$Saldo4;

									$AcuSeg0+=$Saldo0;
									$AcuSeg1+=$Saldo1;
									$AcuSeg2+=$Saldo2;
									$AcuSeg3+=$Saldo3;
									$AcuSeg4+=$Saldo4;

									$pdf->SetFont('verdana','',5);
									$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R','R','R','R','R'));

									$pdf->SetX(13);
									$pdf->Row(array($mDatos[$j]['teridxxx'],
																	substr($mDatos[$j]['clinomxx'],0,30),
																	$mDatos[$j]['pucidxxx'],
																	substr($mDatos[$j]['pucdesxx'],0,23),
																	$mDatos[$j]['clitelxx'],
																	$mDatos[$j]['document'],
																	$mDatos[$j]['comfecxx'],
																	$mDatos[$j]['comfecve'],
																	$mDatos[$j]['diascart'],
																	number_format($Saldo0,2,',','.'),
																	number_format($Saldo1,2,',','.'),
																	number_format($Saldo2,2,',','.'),
																	number_format($Saldo3,2,',','.'),
																	number_format($Saldo4,2,',','.')));

									if ($mDatos[$j]['clinomxx'] == $mDatos[$j+1]['clinomxx']) {
										if ($mDatos[$j]['pucidxxx'] != $mDatos[$j+1]['pucidxxx']) {
											$pdf->SetX(13);
											$pdf->SetFont('verdana','B',5);
											$pdf->Cell(170,3,"   SUBTOTAL CUENTA:".$mDatos[$j]['pucdesxx'],0,0,'L');
											$pdf->Cell(17,3,number_format($AcuSeg0,2,',','.'),0,0,'R');
											$pdf->Cell(17,3,number_format($AcuSeg1,2,',','.'),0,0,'R');
											$pdf->Cell(17,3,number_format($AcuSeg2,2,',','.'),0,0,'R');
											$pdf->Cell(17,3,number_format($AcuSeg3,2,',','.'),0,0,'R');
											$pdf->Cell(17,3,number_format($AcuSeg4,2,',','.'),0,0,'R');

											$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;

											$pdf->SetFont('verdana','',5);
											$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R','R','R','R','R'));
											$pdf->Ln(2);
										}
									}	else {
										$pdf->Ln(2);
										$pdf->SetX(13);
										$pdf->SetFont('verdana','B',5);
										$pdf->Cell(170,3,"   SUBTOTAL CUENTA:".$mDatos[$j]['pucdesxx'],0,0,'L');
										$pdf->Cell(17,3,number_format($AcuSeg0,2,',','.'),0,0,'R');
										$pdf->Cell(17,3,number_format($AcuSeg1,2,',','.'),0,0,'R');
										$pdf->Cell(17,3,number_format($AcuSeg2,2,',','.'),0,0,'R');
										$pdf->Cell(17,3,number_format($AcuSeg3,2,',','.'),0,0,'R');
										$pdf->Cell(17,3,number_format($AcuSeg4,2,',','.'),0,0,'R');

										$pdf->Ln(3);
										$pdf->SetX(13);
										$pdf->SetFont('verdana','B',6);
										$pdf->Cell(170,4,"SUBTOTAL TERCERO:".$mDatos[$j]['clinomxx'],0,0,'L');
										$pdf->Cell(17,4,number_format($AcuPri0,2,',','.'),0,0,'R');
										$pdf->Cell(17,4,number_format($AcuPri1,2,',','.'),0,0,'R');
										$pdf->Cell(17,4,number_format($AcuPri2,2,',','.'),0,0,'R');
										$pdf->Cell(17,4,number_format($AcuPri3,2,',','.'),0,0,'R');
										$pdf->Cell(17,4,number_format($AcuPri4,2,',','.'),0,0,'R');
										$pdf->Ln(4);
										$AcuPri0=0; $AcuPri1=0; $AcuPri2=0; $AcuPri3=0; $AcuPri4=0;
										$AcuSeg0=0; $AcuSeg1=0; $AcuSeg2=0; $AcuSeg3=0; $AcuSeg4=0;

										$pdf->SetFont('verdana','',5);
										$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R','R','R','R','R'));
										$pdf->Ln(2);
									}
								}
							}

							if ($_POST['rOrdRep']=='MONTO') {
								for($j=0;$j<count($mDatos);$j++) {
									$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;

									if ($mDatos[$j]['diascart'] <= $_POST['nComVlr01']) {
										$Saldo0=$mDatos[$j]['saldoxxx'];
										$AcuTer0+=$mDatos[$j]['saldoxxx'];
										$Saldo1=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr01'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr02']) {
										$Saldo1=$mDatos[$j]['saldoxxx'];
										$AcuTer1+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo2=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr02'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr03']) {
										$Saldo2=$mDatos[$j]['saldoxxx'];
										$AcuTer2+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo3=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr03'] && $mDatos[$j]['diascart'] <= $_POST['nComVlr04']) {
										$Saldo3=$mDatos[$j]['saldoxxx'];
										$AcuTer3+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo4=0;
									}

									if ($mDatos[$j]['diascart'] > $_POST['nComVlr04']) {
										$Saldo4=$mDatos[$j]['saldoxxx'];
										$AcuTer4+=$mDatos[$j]['saldoxxx'];
										$Saldo0=0; $Saldo1=0; $Saldo2=0; $Saldo3=0;
									}

									$AcuPri0+=$Saldo0;
									$AcuPri1+=$Saldo1;
									$AcuPri2+=$Saldo2;
									$AcuPri3+=$Saldo3;
									$AcuPri4+=$Saldo4;

									$pdf->SetFont('verdana','',5);
									$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R','R','R','R','R'));

									$pdf->SetX(13);
									$pdf->Row(array($mDatos[$j]['teridxxx'],
																	substr($mDatos[$j]['clinomxx'],0,30),
																	$mDatos[$j]['pucidxxx'],
																	substr($mDatos[$j]['pucdesxx'],0,23),
																	$mDatos[$j]['clitelxx'],
																	$mDatos[$j]['document'],
																	$mDatos[$j]['comfecxx'],
																	$mDatos[$j]['comfecve'],
																	$mDatos[$j]['diascart'],
																	number_format($Saldo0,2,',','.'),
																	number_format($Saldo1,2,',','.'),
																	number_format($Saldo2,2,',','.'),
																	number_format($Saldo3,2,',','.'),
																	number_format($Saldo4,2,',','.')));
								}
								$pdf->Ln(3);
								$pdf->SetX(13);
								$pdf->SetFont('verdana','B',6);
								$pdf->Cell(170,4,"SUBTOTAL",0,0,'L');
								$pdf->Cell(17,4,number_format($AcuPri0,2,',','.'),0,0,'R');
								$pdf->Cell(17,4,number_format($AcuPri1,2,',','.'),0,0,'R');
								$pdf->Cell(17,4,number_format($AcuPri2,2,',','.'),0,0,'R');
								$pdf->Cell(17,4,number_format($AcuPri3,2,',','.'),0,0,'R');
								$pdf->Cell(17,4,number_format($AcuPri4,2,',','.'),0,0,'R');
								$pdf->Ln(4);
							}

							$pdf->SetX(13);
							$pdf->SetFont('verdana','B',6);
							$pdf->Cell(170,4,"TOTAL GENERAL",0,0,'L');
							$pdf->Cell(17,4,number_format($AcuTer0,0,',','.'),0,0,'R');
							$pdf->Cell(17,4,number_format($AcuTer1,0,',','.'),0,0,'R');
							$pdf->Cell(17,4,number_format($AcuTer2,0,',','.'),0,0,'R');
							$pdf->Cell(17,4,number_format($AcuTer3,0,',','.'),0,0,'R');
							$pdf->Cell(17,4,number_format($AcuTer4,0,',','.'),0,0,'R');
							$pdf->Ln(4);

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
						}

						if($_POST['rCarEda'] == "NO") {
							$AcuPri0=0; $AcuSeg0=0; $AcuTer0=0; $Total;

							$pdf->SetWidths(array('15','55','18','40','18','25','22','22','15','25'));
							$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
							$pdf->SetX(13);
							$pdf->Row(array("Nit",
															"Nombre",
															"Cuenta",
															"Nombre Cuenta",
															"Telefono",
															"Documento",
															"Fecha Doc",
															"Fecha Ven",
															"Dias Cart",
															"Saldo"));
							$pdf->SetFont('verdana','',5);
							$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R'));

							if ($_POST['rOrdRep']=='NIT') {
								for($j=0;$j<count($mDatos);$j++) {
									$Total+=$mDatos[$j]['saldoxxx'];
									$Saldo0=$mDatos[$j]['saldoxxx'];
									$AcuPri0+=$Saldo0;
									$AcuSeg0+=$Saldo0;

									$pdf->SetFont('verdana','',5);
									$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R'));

									$pdf->SetX(13);
									$pdf->Row(array($mDatos[$j]['teridxxx'],
																	substr($mDatos[$j]['clinomxx'],0,40),
																	$mDatos[$j]['pucidxxx'],
																	substr($mDatos[$j]['pucdesxx'],0,25),
																	$mDatos[$j]['clitelxx'],
																	$mDatos[$j]['document'],
																	$mDatos[$j]['comfecxx'],
																	$mDatos[$j]['comfecve'],
																	$mDatos[$j]['diascart'],
																	number_format($Saldo0,2,',','.')));

									if ($mDatos[$j]['teridxxx'] == $mDatos[$j+1]['teridxxx']) {
										if ($mDatos[$j]['pucidxxx'] != $mDatos[$j+1]['pucidxxx']) {
											$pdf->SetX(13);
											$pdf->SetFont('verdana','B',5);
											$pdf->Cell(230,3,"   SUBTOTAL CUENTA:".$mDatos[$j]['pucdesxx'],0,0,'L');
											$pdf->Cell(25,3,number_format($AcuSeg0,2,',','.'),0,0,'R');

											$AcuSeg0=0;

											$pdf->SetFont('verdana','',5);
											$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R'));
											$pdf->Ln(2);
										}
									}	else {
										$pdf->Ln(2);
										$pdf->SetX(13);
										$pdf->SetFont('verdana','B',5);
										$pdf->Cell(230,3,"   SUBTOTAL CUENTA:".$mDatos[$j]['pucdesxx'],0,0,'L');
										$pdf->Cell(25,3,number_format($AcuSeg0,2,',','.'),0,0,'R');

										$pdf->Ln(3);
										$pdf->SetX(13);
										$pdf->SetFont('verdana','B',6);
										$pdf->Cell(230,4,"SUBTOTAL TERCERO:".$mDatos[$j]['clinomxx'],0,0,'L');
										$pdf->Cell(25,4,number_format($AcuPri0,2,',','.'),0,0,'R');
										$pdf->Ln(4);
										$AcuPri0=0;	$AcuSeg0=0;

										$pdf->SetFont('verdana','',5);
										$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R'));
										$pdf->Ln(2);
									}
								}
							}

							if ($_POST['rOrdRep']=='CUENTA') {
								for($j=0;$j<count($mDatos);$j++) {
									$Total+=$mDatos[$j]['saldoxxx'];
									$Saldo0=$mDatos[$j]['saldoxxx'];
									$AcuPri0+=$Saldo0;
									$AcuSeg0+=$Saldo0;

									$pdf->SetFont('verdana','',5);
									$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R'));

									$pdf->SetX(13);
									$pdf->Row(array($mDatos[$j]['teridxxx'],
																	substr($mDatos[$j]['clinomxx'],0,40),
																	$mDatos[$j]['pucidxxx'],
																	substr($mDatos[$j]['pucdesxx'],0,25),
																	$mDatos[$j]['clitelxx'],
																	$mDatos[$j]['document'],
																	$mDatos[$j]['comfecxx'],
																	$mDatos[$j]['comfecve'],
																	$mDatos[$j]['diascart'],
																	number_format($Saldo0,2,',','.')));

									if ($mDatos[$j]['pucidxxx'] == $mDatos[$j+1]['pucidxxx']) {
										if ($mDatos[$j]['teridxxx'] != $mDatos[$j+1]['teridxxx']) {
											$pdf->SetX(13);
											$pdf->SetFont('verdana','B',5);
											$pdf->Cell(230,3,"   SUBTOTAL TERCERO:".$mDatos[$j]['clinomxx'],0,0,'L');
											$pdf->Cell(25,3,number_format($AcuSeg0,2,',','.'),0,0,'R');
											$AcuSeg0=0;

											$pdf->SetFont('verdana','',5);
											$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R'));
											$pdf->Ln(5);
										}
									}	else {
										$pdf->Ln(2);
										$pdf->SetX(13);
										$pdf->SetFont('verdana','B',5);
										$pdf->Cell(230,3,"   SUBTOTAL TERCERO:".$mDatos[$j]['clinomxx'],0,0,'L');
										$pdf->Cell(25,3,number_format($AcuSeg0,2,',','.'),0,0,'R');

										$pdf->Ln(3);
										$pdf->SetX(13);
										$pdf->SetFont('verdana','B',6);
										$pdf->Cell(230,4,"SUBTOTAL CUENTA:".$mDatos[$j]['pucdesxx'],0,0,'L');
										$pdf->Cell(25,4,number_format($AcuPri0,2,',','.'),0,0,'R');
										$pdf->Ln(4);
										$AcuPri0=0; $AcuSeg0=0;

										$pdf->SetFont('verdana','',5);
										$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R'));
										$pdf->Ln(2);
									}
								}
							}

							if ($_POST['rOrdRep']=='ALFABETICO') {
								for($j=0;$j<count($mDatos);$j++) {

									$Total+=$mDatos[$j]['saldoxxx'];
									$Saldo0=$mDatos[$j]['saldoxxx'];
									$AcuPri0+=$Saldo0;
									$AcuSeg0+=$Saldo0;

									$pdf->SetFont('verdana','',5);
									$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R'));

									$pdf->SetX(13);
									$pdf->Row(array($mDatos[$j]['teridxxx'],
																	substr($mDatos[$j]['clinomxx'],0,40),
																	$mDatos[$j]['pucidxxx'],
																	substr($mDatos[$j]['pucdesxx'],0,25),
																	$mDatos[$j]['clitelxx'],
																	$mDatos[$j]['document'],
																	$mDatos[$j]['comfecxx'],
																	$mDatos[$j]['comfecve'],
																	$mDatos[$j]['diascart'],
																	number_format($Saldo0,2,',','.')));

									if ($mDatos[$j]['clinomxx'] == $mDatos[$j+1]['clinomxx']) {
										if ($mDatos[$j]['pucidxxx'] != $mDatos[$j+1]['pucidxxx']) {
											$pdf->SetX(13);
											$pdf->SetFont('verdana','B',5);
											$pdf->Cell(230,3,"   SUBTOTAL CUENTA:".$mDatos[$j]['pucdesxx'],0,0,'L');
											$pdf->Cell(25,3,number_format($AcuSeg0,2,',','.'),0,0,'R');

											$AcuSeg0=0;

											$pdf->SetFont('verdana','',5);
											$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R'));
											$pdf->Ln(2);
										}
									}	else {
										$pdf->Ln(2);
										$pdf->SetX(13);
										$pdf->SetFont('verdana','B',5);
										$pdf->Cell(230,3,"   SUBTOTAL CUENTA:".$mDatos[$j]['pucdesxx'],0,0,'L');
										$pdf->Cell(25,3,number_format($AcuSeg0,2,',','.'),0,0,'R');

										$pdf->Ln(3);
										$pdf->SetX(13);
										$pdf->SetFont('verdana','B',6);
										$pdf->Cell(230,4,"SUBTOTAL TERCERO:".$mDatos[$j]['clinomxx'],0,0,'L');
										$pdf->Cell(25,4,number_format($AcuPri0,2,',','.'),0,0,'R');
										$pdf->Ln(4);
										$AcuPri0=0;$AcuSeg0=0;

										$pdf->SetFont('verdana','',5);
										$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R'));
										$pdf->Ln(2);
									}
								}
							}

							if ($_POST['rOrdRep']=='MONTO') {
								for($j=0;$j<count($mDatos);$j++) {
																		$Total+=$mDatos[$j]['saldoxxx'];
																		$Saldo0=$mDatos[$j]['saldoxxx'];
																		$AcuPri0+=$Saldo0;

									$pdf->SetFont('verdana','',5);
									$pdf->SetAligns(array('L','L','L','L','L','L','L','L','R','R','R','R','R','R'));

									$pdf->SetX(13);
									$pdf->Row(array($mDatos[$j]['teridxxx'],
																	substr($mDatos[$j]['clinomxx'],0,40),
																	$mDatos[$j]['pucidxxx'],
																	substr($mDatos[$j]['pucdesxx'],0,25),
																	$mDatos[$j]['clitelxx'],
																	$mDatos[$j]['document'],
																	$mDatos[$j]['comfecxx'],
																	$mDatos[$j]['comfecve'],
																	$mDatos[$j]['diascart'],
																	number_format($Saldo0,2,',','.')));
								}
								$pdf->Ln(3);
								$pdf->SetX(13);
								$pdf->SetFont('verdana','B',6);
								$pdf->Cell(230,4,"SUBTOTAL",0,0,'L');
								$pdf->Cell(25,4,number_format($AcuPri0,2,',','.'),0,0,'R');
								$pdf->Ln(4);
							}

							$pdf->SetX(13);
							$pdf->SetFont('verdana','B',6);
							$pdf->Cell(230,4,"TOTAL GENERAL",0,0,'L');
							$pdf->Cell(25,4,number_format($Total,0,',','.'),0,0,'R');
							$pdf->Ln(4);

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
						}
					} else {
						f_Mensaje(__FILE__,__LINE__,"No se Generaron registros");
					}
				break;
			}
		}
	}//if ($cEjePro == 0) {

	if ($nSwitch == 0) {
	} else {
	  f_Mensaje(__FILE__,__LINE__,$cMsj."Verifique.\n");

	   switch ($_POST['rTipo']) {
      case 2:
        /** Excel
         * No hace nada porque se ejecuta en el fmpro
        **/
      break;
      default: ?>
        <script languaje = "javascript">
          window.close();
        </script>
      <?php break;
	   }
	} ?>

	<?php
	function fnCadenaAleatoria($pLength = 8) {
    $cCaracteres = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ";
    $nCaracteres = strlen($cCaracteres);
    $cResult = "";
    for ($x=0;$x< $pLength;$x++) {
      $nIndex = mt_rand(0,$nCaracteres - 1);
      $cResult .= $cCaracteres[$nIndex];
    }
    return $cResult;
  }?>

	<?php
	if ($_SERVER["SERVER_PORT"] == "") {
		/**
		 * Se ejecuto por el proceso en background
		 * Actualizo el campo de resultado y nombre del archivo
		 */
		$vParBg['pbarespr'] = ($nSwitch == 0) ? "EXITOSO" : "FALLIDO";  //Resultado Proceso
		$vParBg['pbaexcxx'] = $cNomFile;                                //Nombre Archivos Excel
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
